CREATE OR REPLACE FUNCTION alm.f_upload_saldo_fisico_item(p_fecha_hasta date)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		SISTEMA DE ALMACENES
 FUNCION: 		alm.f_upload_saldo_fisico_item
 DESCRIPCION:   Función que carga en una tabla auxiliar los saldos fisicos de los items
 RETORNA:		void
 AUTOR: 		Franklin Espinoza
 FECHA:	        07/01/2020
 COMENTARIOS:
***************************************************************************/

DECLARE

	v_nombre_funcion	text;
    v_resp				varchar;
    v_item_saldo 		numeric;
   	v_resultado 		record;
   	v_consulta  		varchar;
    v_ingresos			numeric;
  	v_salidas			numeric;
    v_existencias		numeric;

    v_fecha_fin date;
    va_id_movimiento_inv_fin integer[];
	v_estado_salida		varchar;v_cadena varchar;

    v_condicion 		VARCHAR = '';
    v_fecha_actual  	date;

    v_movimientos		record;
    v_mov				record;
    v_id_movimiento		integer;
    v_id_gestion		integer;
    v_contador      integer;
BEGIN

    v_nombre_funcion = 'alm.f_upload_saldo_fisico_item';

    v_fecha_fin = p_fecha_hasta::date;
    p_fecha_hasta = p_fecha_hasta + interval '1 day';
    v_item_saldo := 0;

    --  identifica si tiene cirres al dia solcitado
    select
      pxp.aggarray(id_movimiento)
    into
     va_id_movimiento_inv_fin
    from alm.tmovimiento m
    inner join alm.tmovimiento_tipo mt on mt.id_movimiento_tipo = m.id_movimiento_tipo
    and  mt.codigo ='INVFIN' and m.fecha_mov::date = v_fecha_fin;

    select tg.id_gestion
    into v_id_gestion
    from param.tgestion tg
    where tg.gestion = date_part('year',v_fecha_fin);

    v_id_movimiento = va_id_movimiento_inv_fin[1];

    select mov.id_movimiento, mov.id_almacen, mov.fecha_mov, mtip.tipo, mov.fecha_salida
    into v_mov
    from alm.tmovimiento mov
    inner join alm.tmovimiento_tipo mtip
    on mtip.id_movimiento_tipo = mov.id_movimiento_tipo
    where id_movimiento = v_id_movimiento;

	for v_movimientos in  select
                          movdet.id_item,
                          item.nombre as nombre_item,
                          sum(movdet.cantidad) as cantidad_item,
                          sum(movdet.cantidad_solicitada) as cantidad_solicitada
                          from alm.tmovimiento_det movdet
                          inner join alm.titem item on item.id_item = movdet.id_item
                          where movdet.estado_reg = 'activo'
                          and movdet.id_movimiento = v_id_movimiento
                          group by movdet.id_item,item.nombre
                          order by movdet.id_item asc loop

      select count(sal.id_item)
      into v_contador
      from alm.tsaldo_fisico_item sal
      where sal.id_item = v_movimientos.id_item and sal.id_almacen = v_mov.id_almacen and sal.id_gestion = v_id_gestion;

      if v_contador = 1 then
       continue;
      end if;

      select coalesce(sum(movdet.cantidad),0) into v_ingresos
      from alm.tmovimiento_det movdet
      inner join alm.tmovimiento mov on mov.id_movimiento = movdet.id_movimiento
      inner join alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo
      where movdet.estado_reg = 'activo'
      and movtip.tipo like '%ingreso%'
      and movdet.id_item = v_movimientos.id_item
      and mov.estado_mov = 'finalizado'
      and mov.id_almacen = v_mov.id_almacen
      and mov.fecha_mov < p_fecha_hasta;

     --salidas

      v_estado_salida = ' and mov.estado_mov = ''finalizado'' ';
      v_condicion = ' and mov.fecha_mov < '''||p_fecha_hasta||'''::date ';

      execute('select coalesce(sum(movdet.cantidad),0)
      from alm.tmovimiento_det movdet
      inner join alm.tmovimiento mov on mov.id_movimiento = movdet.id_movimiento
                                    and mov.id_movimiento not in ('||COALESCE(array_to_string(va_id_movimiento_inv_fin,','),'0')||')

      inner join alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo
      where movdet.estado_reg = ''activo''
          and movtip.tipo like ''%salida%''
          and movdet.id_item = '||v_movimientos.id_item|| v_estado_salida || '

          and mov.id_almacen = '||v_mov.id_almacen||
          v_condicion
          ) into v_salidas;

      if (v_ingresos is null) then
          v_existencias = 0;
      elseif (v_salidas is null) then
          v_existencias = v_ingresos;
      else
          v_existencias = v_ingresos - v_salidas;
      end if;

      insert into alm.tsaldo_fisico_item(
          id_item,
          id_almacen,
          fecha_hasta,
          fisico,
          id_gestion
      ) values(
          v_movimientos.id_item,
          v_mov.id_almacen,
          p_fecha_hasta,
          v_existencias,
          v_id_gestion
      );

    end loop;

	return 'exito';
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
