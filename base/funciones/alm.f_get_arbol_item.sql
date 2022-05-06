CREATE OR REPLACE FUNCTION alm.f_get_arbol_item (
  p_lista_clasificacion varchar,
  p_id_almacen integer,
  p_saldo_cero varchar,
  p_fecha_ini date,
  p_fecha_fin date
)
RETURNS TABLE (
  codigo varchar,
  nombre varchar,
  saldo_ini numeric,
  ingreso numeric,
  salida numeric,
  saldo_fin numeric,
  descripcion varchar,
  tamano integer,
  id_clasificacion_fk integer
) AS
$body$
/**************************************************************************
 SISTEMA:		Sistema Almacenes
 FUNCION: 		public.f_get_arbol_item
 DESCRIPCION:   Funcion que recupera los Items de una Clasificacion.
 AUTOR: 		(franklin.espinoza)
 FECHA:	        14-06-2019 15:15:26
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
    v_descripcion			varchar;
    v_length 				integer;
    v_id_clasificacion_fk   integer;
    v_all_almacen			varchar;

    va_id_movimiento_inv_fin  integer[];
    v_fecha_fin				date;

BEGIN

    v_nombre_funcion = 'orga.f_get_arbol_item';
    --raise 'p_all_almacen: %, %',p_fecha_fin,p_id_almacen ;
    v_fecha_fin = p_fecha_fin + interval '1 day';

    /*if p_all_almacen = 'si' then
     v_all_almacen = 'case when mov.id_almacen = 1 then date_trunc(''day'',mov.fecha_mov) between '''||p_fecha_ini||'''::date and '''||p_fecha_fin||'''::date
					   	   else date_trunc(''day'',mov.fecha_mov) between ''01/01/2015''::date and '''||p_fecha_fin||'''::date end';
    else
    	if p_id_almacen = 1 then
        	v_all_almacen = 'mov.id_almacen = '||p_id_almacen||' and (date_trunc(''day'',mov.fecha_mov) between '''||p_fecha_ini||'''::date and '''||p_fecha_fin||'''::date)';
        else
        	v_all_almacen = 'mov.id_almacen = '||p_id_almacen||' and (date_trunc(''day'',mov.fecha_mov) between ''01/01/2015''::date and '''||p_fecha_fin||'''::date)';
        end if;
    end if;*/

    v_all_almacen = 'mov.id_almacen = '||p_id_almacen||' and case when mov.id_almacen = 1 then mov.fecha_mov <= '''||p_fecha_fin||'''::date + 1 else mov.fecha_mov <= '''||p_fecha_fin||'''::date end';

    select pxp.aggarray(id_movimiento)
    into va_id_movimiento_inv_fin
    from alm.tmovimiento m
    inner join alm.tmovimiento_tipo mt on   mt.id_movimiento_tipo = m.id_movimiento_tipo and  mt.codigo ='INVFIN' and m.fecha_mov::date = p_fecha_fin;
--raise 'p_id_almacen: %', va_id_movimiento_inv_fin;
--raise 'p_lista_clasificacion: %', p_lista_clasificacion;
	for v_id_clasificacion in select tc.id_clasificacion from alm.tclasificacion tc where tc.id_clasificacion = ANY(string_to_array(p_lista_clasificacion,',')::integer[])/*tc.id_clasificacion_fk is null*/ order by tc.codigo_largo asc loop

      for v_id_class in select unnest(string_to_array(v_id_clasificacion||','||rtrim(alm.f_get_arbol_clasificacion(v_id_clasificacion),','), ',')) as id_class  loop
	  	if v_id_class != '' then
           select tc.codigo_largo, tc.nombre, tc.descripcion, length(replace(tc.codigo_largo,'.','')), tc.id_clasificacion_fk
           into v_codigo, v_nombre, v_descripcion, v_length, v_id_clasificacion_fk
           from alm.tclasificacion tc
           where tc.id_clasificacion = v_id_class::integer;

           select p_saldo_ini, p_ingreso, p_salida, p_saldo_fin
           into v_saldo_inicial, v_ingresos, v_salidas, v_saldo_final
           from alm.f_get_arbol_total_clasificacion(v_id_class::integer,  p_id_almacen, p_saldo_cero, p_lista_clasificacion, p_fecha_fin);

           if p_saldo_cero = 'no' and v_saldo_final = 0 then
           	continue;
           end if;

           codigo = v_codigo;
           nombre = v_nombre;
           saldo_ini = round(v_saldo_inicial,2);
           ingreso = round(v_ingresos,2);
           salida = round(v_salidas,2);
           saldo_fin = round(v_saldo_final,2);
           descripcion = v_descripcion;
           tamano = v_length;
           id_clasificacion_fk = v_id_clasificacion_fk;
           return next;
		  --IF v_id_class::integer = ANY(string_to_array(p_lista_clasificacion,',')::integer[]) THEN /*if v_id_class::integer = 31 then raise 'v_id_class: %',v_id_class;   end if;*/ CONTINUE; END IF;
          for v_record in select ti.codigo, ti.nombre, ti.id_item, ti.descripcion
                          from alm.titem ti
                          where ti.estado_reg = 'activo' and ti.id_clasificacion = v_id_class::integer --and v_id_clasificacion::integer != ANY(string_to_array(p_lista_clasificacion,',')::integer[])
                          order by ti.num_por_clasificacion asc loop

              v_saldo_inicial = 0;
              v_ingresos = 0;
              v_salidas = 0;
              --if v_id_class::integer = ANY(string_to_array(p_lista_clasificacion,',')::integer[]) then continue;  end if;
              for v_valor_item in execute('select
                                      case mtipo.tipo
                                          when ''ingreso'' then case when item.codigo != ''2.5.6.91'' then sum(mdval.cantidad*mdval.costo_unitario)*0.87 else sum(mdval.cantidad*mdval.costo_unitario) end
                                          else 0
                                      end as ingreso,
                                      case mtipo.tipo
                                          when ''salida'' then case when item.codigo != ''2.5.6.91'' then sum(mdval.cantidad*mdval.costo_unitario)*0.87 else sum(mdval.cantidad*mdval.costo_unitario) end
                                          else 0
                                      end as salida,
                                      mtipo.codigo as codigo_mov,
                                      item.codigo as codigo_item
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

                  if v_valor_item.codigo_mov = 'INVINI' then
                    /*if v_valor_item.codigo_item = '3.2.1.1' then
                      raise notice 'codigo_mov: %, ingreso: %',v_valor_item.codigo_mov,v_valor_item.ingreso;
                    end if;*/
                      v_saldo_inicial = v_saldo_inicial + v_valor_item.ingreso;
                  elsif (v_valor_item.codigo_mov like 'IN%' or v_valor_item.codigo_mov = 'DEV') and v_valor_item.codigo_mov != 'INVINI' and v_valor_item.codigo_mov != 'INVFIN' then
                  	/*if v_valor_item.codigo_item = '3.2.1.1' then
                  		raise notice 'codigo_mov: %, ingreso: %',v_valor_item.codigo_mov,v_valor_item.ingreso;
                    end if;*/
                      v_ingresos = v_ingresos + v_valor_item.ingreso;
                  elsif v_valor_item.codigo_mov like 'SAL%' or v_valor_item.codigo_mov = 'INVFIN' then
                  	/*if v_valor_item.codigo_item = '3.2.1.1' then
                  		raise notice 'codigo_mov: %, salida: %',v_valor_item.codigo_mov,v_valor_item.salida;
                    end if;*/
                      v_salidas = v_salidas + v_valor_item.salida;
                  end if;
              end loop;

            if (p_saldo_cero = 'no' and (v_saldo_inicial + v_ingresos - v_salidas) = 0) /*or v_id_class::integer = ANY(string_to_array(p_lista_clasificacion,',')::integer[])*/  then
            	continue;
            end if;
		    /*if v_record.codigo = '3.2.1.1' then
            	raise 'v_saldo_inicial: %, v_ingresos: %, v_salidas: %, total: %', v_saldo_inicial, v_ingresos, v_salidas,(v_saldo_inicial + v_ingresos - v_salidas);
            end if;*/
            codigo = v_record.codigo;
            nombre = case when length(v_record.nombre) > 7 then v_record.nombre else v_record.nombre||' - '||v_record.descripcion end;
            saldo_ini = round(v_saldo_inicial,2);
            ingreso = round(v_ingresos,2);
            salida = round(v_salidas,2);
            saldo_fin = round(v_saldo_inicial + v_ingresos - v_salidas,2);
            descripcion = '';
            tamano = -1;
            id_clasificacion_fk = v_id_clasificacion_fk;

            v_saldo_inicial = 0;
            v_ingresos = 0;
            v_salidas = 0;
            /*if p_saldo_cero = 'no' and v_salidas = 0  then
            	continue;
            end if;*/
            return next;
          end loop;
          --END IF;
      	end if;
      end loop;
	end loop;

    return;
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
COST 100 ROWS 1000;