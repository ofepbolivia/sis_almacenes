--------------- SQL ---------------

CREATE OR REPLACE FUNCTION alm.f_get_saldo_item_val (
  p_id_item integer,
  p_id_almacen varchar,
  p_fecha_hasta date
)
RETURNS numeric AS
$body$
/**************************************************************************
 SISTEMA:		SISTEMA DE ALMACENES
 FUNCION: 		alm.f_get_saldo_item_val
 DESCRIPCION:   Función que devuelve la cantidad valorada existente del item con ID: p_id_item
 RETORNA:		Devuelve el valor de la cantidad disponible para el item: p_id_item
 AUTOR: 		RCM
 FECHA:	        26/07/2013
 COMENTARIOS:
 *********************************** CAMBIOS
  DESCRIPCION:  se agrega el filtro al tipo demovimeinto de invetarios final para no ser considerado en el calculo
                por ejemplo el repote de kardek al 31 de diciembre no tiene que considerar la salida realizada por el inventario final
 AUTOR: 		    RAC
 FECHA:	        10/04/2014


***************************************************************************/

DECLARE

	v_nombre_funcion	text;
    v_resp				varchar;
    v_ingresos			numeric;
  	v_salidas			numeric;
    v_saldo				numeric;
    v_sql 				varchar;
    v_cond_almacen 		varchar;
    v_fecha_fin         date;
    va_id_movimiento_inv_fin  integer[];

BEGIN



    v_fecha_fin = p_fecha_hasta::date;

    p_fecha_hasta = p_fecha_hasta + interval '1 day';
    v_nombre_funcion = 'alm.f_get_saldo_valorado_item';

   --RAISE EXCEPTION 'v_fecha_fin: %, p_fecha_hasta: %', v_fecha_fin,p_fecha_hasta;
    -- identifica si tiene cirres al dia solicitado
    select
      pxp.aggarray(id_movimiento)
    into
     va_id_movimiento_inv_fin
    from alm.tmovimiento m
    inner join alm.tmovimiento_tipo mt
       on   mt.id_movimiento_tipo = m.id_movimiento_tipo
       and  mt.codigo ='INVFIN' and m.fecha_mov::date = v_fecha_fin;



    v_cond_almacen='';
	if coalesce(p_id_almacen,'') != '' then
		v_cond_almacen = ' and mov.id_almacen in ('||p_id_almacen||')';
    end if;

    create temp table tt_saldo_val(
    saldo numeric
    ) on commit drop;

    v_sql = '
    insert into tt_saldo_val
	with saldos as(
    select sum(detval.cantidad * detval.costo_unitario) as valor
    from alm.tmovimiento_det_valorado detval
    inner join alm.tmovimiento_det movdet on movdet.id_movimiento_det = detval.id_movimiento_det
    inner join alm.tmovimiento mov on mov.id_movimiento = movdet.id_movimiento
    inner join alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo
    where movdet.estado_reg = ''activo''
    and movtip.tipo = ''ingreso''
    and movdet.id_item = ' || p_id_item ||'
    and mov.estado_mov = ''finalizado''';

    if v_cond_almacen != '' then
    	v_sql = v_sql ||v_cond_almacen;
    end if;

    v_sql = v_sql || '
    and mov.fecha_mov < ''' || p_fecha_hasta || '''
    union all
    select -sum(detval.cantidad * detval.costo_unitario) as valor
    from alm.tmovimiento_det_valorado detval
    inner join alm.tmovimiento_det movdet on
             movdet.id_movimiento_det = detval.id_movimiento_det

    inner join alm.tmovimiento mov on mov.id_movimiento = movdet.id_movimiento
    			--and mov.id_movimiento not in ('||COALESCE(array_to_string(va_id_movimiento_inv_fin,','),'0')||')
    inner join alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo
    where movdet.estado_reg = ''activo''
    and movtip.tipo = ''salida''
    and movdet.id_item = '  || p_id_item ||'
    and mov.estado_mov = ''finalizado''';

	if v_cond_almacen != '' then
    	v_sql = v_sql ||v_cond_almacen;
    end if;

    v_sql = v_sql || '
	and mov.fecha_mov < ''' || p_fecha_hasta ||''')
    select
    sum(valor) as saldo
    from saldos';

    raise notice '%',v_sql;

    execute(v_sql);

    select saldo
    into v_saldo
    from tt_saldo_val;

    return coalesce(v_saldo,0);

EXCEPTION
	WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;