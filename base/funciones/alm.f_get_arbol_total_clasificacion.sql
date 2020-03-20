CREATE OR REPLACE FUNCTION alm.f_get_arbol_total_clasificacion (
  p_id_clasificacion integer,
  p_id_almacen integer,
  p_saldo_cero varchar,
  p_lista_clasificacion varchar,
  p_fecha_fin date,
  out p_saldo_ini numeric,
  out p_ingreso numeric,
  out p_salida numeric,
  out p_saldo_fin numeric
)
RETURNS record AS
$body$
/**************************************************************************
 SISTEMA:		Sistema Almacenes
 FUNCION: 		public.f_get_arbol_total_clasificacion
 DESCRIPCION:   Funcion que recupera la suma total por Clasificacion.
 AUTOR: 		(franklin.espinoza)
 FECHA:	        19-02-2020 15:15:26
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_resp		            varchar='';
	v_nombre_funcion        text;
	v_record 				record;
    v_id_class				varchar;
    v_codigo				varchar;
    v_nombre				varchar;
	v_id_clasificacion		integer;
    v_valor_item			record;

    v_saldo_inicial			numeric=0;
    v_ingresos				numeric=0;
    v_salidas				numeric=0;
    v_saldo_final			numeric=0;

    v_all_almacen			varchar;
    va_id_movimiento_inv_fin  integer[];
    v_fecha_fin				date;

    v_codigo_act			varchar;

BEGIN

    v_nombre_funcion = 'orga.f_get_arbol_total_clasificacion';
     v_fecha_fin = p_fecha_fin + interval '1 day';
	--raise 'llega %', p_lista_clasificacion;
    /*if p_all_almacen = 'si' then
     v_all_almacen = 'case when mov.id_almacen = 1 then date_trunc(''day'',mov.fecha_mov) between '''||p_fecha_ini||'''::date and '''||p_fecha_fin||'''::date and
					   	   else date_trunc(''day'',mov.fecha_mov) between ''01/01/2015''::date and '''||p_fecha_fin||'''::date end';
    else
    	if p_id_almacen = 1 then
        	v_all_almacen = 'mov.id_almacen = '||p_id_almacen||' and (date_trunc(''day'',mov.fecha_mov) between '''||p_fecha_ini||'''::date and  '''||p_fecha_fin||'''::date)';
        else
        	v_all_almacen = 'mov.id_almacen = '||p_id_almacen||' and (date_trunc(''day'',mov.fecha_mov) between ''01/01/2015''::date and '''||p_fecha_fin||'''::date)';
        end if;
    end if;*/

    --v_all_almacen = 'mov.id_almacen = '||p_id_almacen||' and mov.fecha_mov <= '''||p_fecha_fin||'''::date';
    v_all_almacen = 'mov.id_almacen = '||p_id_almacen||' and case when mov.id_almacen = 1 then mov.fecha_mov <= '''||p_fecha_fin||'''::date + 1 else mov.fecha_mov <= '''||p_fecha_fin||'''::date end';

    select pxp.aggarray(id_movimiento)
    into va_id_movimiento_inv_fin
    from alm.tmovimiento m
    inner join alm.tmovimiento_tipo mt on   mt.id_movimiento_tipo = m.id_movimiento_tipo and  mt.codigo ='INVFIN' and m.fecha_mov::date = p_fecha_fin;

    select tc.codigo_largo
    into v_codigo_act
    from alm.tclasificacion tc
    where tc.id_clasificacion = p_id_clasificacion;

      for v_id_class in select unnest(string_to_array(p_id_clasificacion||','||rtrim(alm.f_get_arbol_clasificacion(p_id_clasificacion),','), ',')) as id_class  loop
         if v_id_class != '' then
           select tc.codigo_largo, tc.nombre
           into v_codigo, v_nombre
           from alm.tclasificacion tc
           where tc.id_clasificacion = v_id_class::integer;

           if v_codigo_act = '3.4.1'  then
    			if v_codigo_act != v_codigo then
                	continue;
                end if;
    	   end if;

		   --IF v_id_class::integer != ANY(string_to_array(p_lista_clasificacion,',')::integer[]) THEN --CONTINUE; END IF;
           for v_record in select ti.codigo, ti.nombre, ti.id_item
                          from alm.titem ti
                          where ti.estado_reg = 'activo' and ti.id_clasificacion = v_id_class::integer --and ti.id_clasificacion != ANY(string_to_array(p_lista_clasificacion,',')::integer[])
                          order by ti.num_por_clasificacion asc loop
              --if p_id_clasificacion::integer = ANY(string_to_array(p_lista_clasificacion,',')::integer[]) then continue;  end if;
              for v_valor_item in execute(' select
                                      case mtipo.tipo
                                          when ''ingreso'' then case when item.codigo != ''2.5.6.91'' then sum(mdval.cantidad*mdval.costo_unitario)*0.87 else sum(mdval.cantidad*mdval.costo_unitario) end
                                          else 0
                                      end as ingreso,
                                      case mtipo.tipo
                                          when ''salida'' then case when item.codigo != ''2.5.6.91'' then sum(mdval.cantidad*mdval.costo_unitario)*0.87 else sum(mdval.cantidad*mdval.costo_unitario) end
                                          else 0
                                      end as salida,
                                      mtipo.codigo as codigo_mov
                                    from alm.tmovimiento mov
                                    --inner join wf.tproceso_wf tpw on tpw.id_proceso_wf = mov.id_proceso_wf
                                    inner join alm.tmovimiento_det mdet on mdet.id_movimiento = mov.id_movimiento
                                    inner join alm.tmovimiento_det_valorado mdval on mdval.id_movimiento_det = mdet.id_movimiento_det
                                    inner join alm.titem item on item.id_item = mdet.id_item
                                    inner join alm.talmacen alma on alma.id_almacen = mov.id_almacen
                                    inner join alm.tmovimiento_tipo mtipo on mtipo.id_movimiento_tipo = mov.id_movimiento_tipo
                                    where mov.estado_mov = ''finalizado'' /*and mdet.cantidad > 0*/ and mdet.estado_reg = ''activo''
                                    and '||v_all_almacen||' and mdet.id_item = '||v_record.id_item||' and mov.id_movimiento not in ('||COALESCE(array_to_string(va_id_movimiento_inv_fin,','),'0')||')
                                    group by mtipo.tipo,codigo_mov, item.codigo') loop

                  --if v_id_class::integer != ANY(string_to_array(p_lista_clasificacion,',')::integer[]) then
                    if v_valor_item.codigo_mov = 'INVINI' then
                        v_saldo_inicial = v_saldo_inicial + coalesce(v_valor_item.ingreso,0);
                    elsif (v_valor_item.codigo_mov like 'IN%' or v_valor_item.codigo_mov = 'DEV') and v_valor_item.codigo_mov != 'INVINI' and v_valor_item.codigo_mov != 'INVFIN' then
                        v_ingresos = v_ingresos + coalesce(v_valor_item.ingreso,0);
                    elsif v_valor_item.codigo_mov like 'SAL%' or v_valor_item.codigo_mov = 'INVFIN' then
                        v_salidas = v_salidas + coalesce(v_valor_item.salida,0);
                    end if;
                  --end if;
              end loop;

              /*if v_id_class::integer = ANY(string_to_array(p_lista_clasificacion,',')::integer[])  then
                v_saldo_inicial	= 0;
                v_ingresos = 0;
                v_salidas = 0;
                v_saldo_final = 0;
                continue;
              end if;*/

              /*if p_saldo_cero = 'no' and v_salidas = 0 then
              	continue;
              end if;*/
           end loop;
           --END IF;
         end if;

      end loop;

      p_saldo_ini = round(v_saldo_inicial,2);
      p_ingreso = round(v_ingresos,2);
      p_salida = round(v_salidas,2);
      p_saldo_fin = round(v_saldo_inicial + v_ingresos - v_salidas,2);


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