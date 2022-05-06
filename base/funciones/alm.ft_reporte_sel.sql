CREATE OR REPLACE FUNCTION alm.ft_reporte_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/***************************************************************************
 SISTEMA:        Almacenes
 FUNCION:        alm.ft_reporte_sel
 DESCRIPCION:    Funcion que devuelve conjuntos de registros para los reportes del sistema de almacenes
 AUTOR:          Ariel Ayaviri Omonte
 FECHA:          29-04-2013
 COMENTARIOS:
***************************************************************************/

DECLARE
  v_nombre_funcion	varchar;
  v_consulta 		varchar;
  v_parametros 		record;
  v_respuesta		varchar;
  v_id_items		varchar[];
  v_where			varchar;
  v_ids				varchar;
  --reporte
  v_saldo_fis			numeric;
  v_saldo_val			numeric;
  v_saldo_fis_ant		numeric;
  v_saldo_val_ant		numeric;
  v_rec					record;

  v_items				record;
  v_consulta_aux		varchar;

  --(franklin.espinoza)
  v_saldo_fisico		boolean;
  v_saldo_valorado		boolean;
BEGIN

	v_nombre_funcion='alm.ft_reporte_sel';
  	v_parametros=pxp.f_get_record(p_tabla);

  	/*********************************
     #TRANSACCION:  'SAL_REPEXIST_SEL'
     #DESCRIPCION:  Retorna las existencias de n items de un almacen.
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        29-04-2013
    ***********************************/

	if(p_transaccion='SAL_REPEXIST_SEL') then
		begin
			--RAISE EXCEPTION 'REPORTE: %, %, %', v_parametros.all_items, v_parametros.alertas, v_parametros.saldo_cero;

            if (v_parametros.all_items = 'Todos los Items') then
                    v_where = 'where ';
            elsif (v_parametros.all_items = 'Seleccionar Items') then
                v_where = 'where itm.id_item = ANY(ARRAY['||v_parametros.id_items||']) and ';
            elsif (v_parametros.all_items = 'Por Clasificacion') then
                --Obtener los IDs de todas las clasificaciones
                v_ids=alm.f_get_id_clasificaciones_varios(v_parametros.id_clasificacion);
                --raise exception 'clasificacion: %, %', v_parametros.id_clasificacion, v_ids;
                IF (v_ids = '') THEN

                   v_ids = '0';

                END IF;

                v_where = 'where itm.id_clasificacion in (' ||v_ids||') and ';
            else
                raise exception 'Error desconocido';
            end if;

                --Verifica si se debe incluir los items que tengan existencias iguales a cero a la fecha
                if (v_parametros.alertas != 'todos') THEN
                    if(v_parametros.alertas = 'cantidad_minima') then
                        v_where = v_where || ' (alm.f_get_saldo_fisico_item(itm.id_item, '||v_parametros.id_almacen||', date('''''|| v_parametros.fecha_hasta||'''''))) < almsto.cantidad_min and ';
                    elsif(v_parametros.alertas = 'cantidad_amarilla') then
                        v_where = v_where || ' (alm.f_get_saldo_fisico_item(itm.id_item, '||v_parametros.id_almacen||', date('''''|| v_parametros.fecha_hasta||'''''))) < almsto.cantidad_alerta_amarilla and ';
                    elsif(v_parametros.alertas = 'cantidad_roja') then
                        v_where = v_where || ' (alm.f_get_saldo_fisico_item(itm.id_item, '||v_parametros.id_almacen||', date('''''|| v_parametros.fecha_hasta||'''''))) < almsto.cantidad_alerta_roja and ';
                    end if;
                end if;
                    --(fea)
                    if(v_parametros.saldo_cero = 'no') then
                        v_where = v_where || ' (alm.f_get_saldo_fisico_item(itm.id_item, '||v_parametros.id_almacen||', date('''''|| v_parametros.fecha_hasta||'''''))) > 0 and ';
                    end if;

                    /*if(v_parametros.saldo_cero = 'no') then
                        v_where = v_where || ' tsf.fisico > 0 and ';
                    end if;*/

    			--raise exception 'v_parametros: %', v_parametros.filtro;
                /*delete from alm.tsaldo_fisico_item;
                delete from alm.tsaldo_valorado_item;
                v_saldo_fisico = alm.f_insertar_saldo_fisico((v_parametros.fecha_hasta+1)::date);
                v_saldo_valorado = alm.f_insertar_saldo_valorado((v_parametros.fecha_hasta+1)::date);*/

                v_consulta:='select
                            id_item,
                            codigo,
                            nombre,
                            (case when codigo = ''2.5.6.24'' then ''bloc'' else
                            unidad_medida end)::varchar as unidad_medida,
                            clasificacion,
                            cantidad,
                            case when '''||v_parametros.porcentaje ||''' = ''ochenta'' and codigo != ''2.5.6.91'' then (costo*0.87) else costo end as costo,
                            cantidad_min,
                            cantidad_alerta_roja,
                            cantidad_alerta_amarilla
                            from alm.f_existencias_almacen_sel('||v_parametros.id_almacen||','''||v_parametros.fecha_hasta||''','''||v_where||''','''||v_parametros.filtro||''')
                            as (id_item integer,
                            codigo varchar,
                            nombre varchar,
                            unidad_medida varchar,
                            clasificacion varchar,
                            cantidad numeric,
                            costo numeric,
                            cantidad_min numeric,
                            cantidad_alerta_roja numeric,
                            cantidad_alerta_amarilla numeric)';

            raise notice 'v_consulta: %',v_consulta;

	        return v_consulta;
	    end;
  	/*********************************
     #TRANSACCION:  'SAL_REPEXIST_CONT'
     #DESCRIPCION:  Conteo de registros
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        29-04-2013
    ***********************************/
	elsif(p_transaccion='SAL_REPEXIST_CONT')then

        begin
        if (v_parametros.all_items = 'si') then
            v_where = 'where ';
        else
             IF v_parametros.id_items='' THEN
                 v_parametros.id_items='0';
             END IF;


            v_where = 'where itm.id_item = ANY(ARRAY['||v_parametros.id_items||']) and ';
        end if;

            v_consulta:='
                select count(itm.id_item)
                from alm.titem itm
            inner join param.tunidad_medida umed on umed.id_unidad_medida = itm.id_unidad_medida
            inner join alm.tclasificacion cla on cla.id_clasificacion = itm.id_clasificacion ' || v_where;

        v_consulta:=v_consulta||v_parametros.filtro;
            return v_consulta;
         end;

	/*********************************
     #TRANSACCION:  'SAL_REITEN_SEL'
     #DESCRIPCION:  Retorna listado de los materiales entregados/recibidos
     #AUTOR:        RCM
     #FECHA:        13/08/2013
    ***********************************/

	elsif(p_transaccion='SAL_REITEN_SEL') then
		begin

            --Fecha
            if v_parametros.fecha_ini is not null and v_parametros.fecha_fin is not null then
				v_where = ' and date_trunc(''day'',mov.fecha_mov) between ''' ||v_parametros.fecha_ini||''' and ''' || v_parametros.fecha_fin || '''';
            else
            	raise exception 'Fechas no definidas';
            end if;

        	--Tipo de Movimiento
			if v_parametros.tipo_mov = 'ingreso' then
				v_where = ' and mtipo.tipo = ''ingreso''';
			elsif v_parametros.tipo_mov = 'salida' then
				v_where = ' and mtipo.tipo = ''salida''';
			end if;

            --Solicitante
            if v_parametros.tipo_sol = 'func' then
                if v_parametros.all_funcionario = 'Seleccionar Funcionarios' then
	            	v_where = v_where || ' and fun.id_funcionario = ANY(ARRAY['||v_parametros.id_funcionario||'])';
	            elsif v_parametros.all_funcionario = 'Por Organigrama' then
	            	--Obtener los IDs de todos los organigramas
					v_ids=orga.f_get_id_uo(v_parametros.id_estructura_uo);
					v_where = v_where || ' and uofun.id_uo in (' ||v_ids||')';
				end if;

            elsif v_parametros.tipo_sol = 'prov' then
            	if coalesce(v_parametros.id_proveedor,'') != '' then
            		v_where = v_where || ' and prov.id_proveedor in (' || v_parametros.id_proveedor || ')';
                end if;
            end if;

            --Items
            if v_parametros.all_items = 'Seleccionar Items' then
            	v_where = v_where || ' and item.id_item = ANY(ARRAY['||v_parametros.id_items||'])';
            elsif v_parametros.all_items = 'Por Clasificacion' then
            	--Obtener los IDs de todas las clasificaciones
				v_ids=alm.f_get_id_clasificaciones_varios(v_parametros.id_clasificacion);
				v_where = v_where || ' and item.id_clasificacion in (' ||v_ids||')';
			end if;

            --Almacenes
            if v_parametros.all_alm = 'no' then
            	v_where = v_where || ' and mov.id_almacen in('||v_parametros.id_almacen||')';
            end if;

	    	v_consulta:='
	        	select
                distinct mval.id_movimiento_det_valorado ,
                		mov.fecha_mov::date,
                        item.codigo,
                        item.nombre,
                        mval.cantidad,
                		mval.costo_unitario,
                        mval.cantidad*mval.costo_unitario as costo_total,
                        fun.desc_funcionario1,
                		prov.desc_proveedor,
                        mov.codigo as mov_codigo,
                        mtipo.nombre as tipo_nombre,
                        mtipo.tipo,
                		alm.nombre as desc_almacen,

                        cc.codigo_cc::varchar as desc_centro_costo,
                        (par.codigo ||''-''|| par.nombre_partida)::varchar as desc_partida

                from alm.tmovimiento_det mdet
                inner join alm.tmovimiento_det_valorado mval on mval.id_movimiento_det = mdet.id_movimiento_det
                inner join alm.tmovimiento mov on mov.id_movimiento = mdet.id_movimiento
                inner join alm.tmovimiento_tipo mtipo on mtipo.id_movimiento_tipo = mov.id_movimiento_tipo
                inner join alm.titem item on item.id_item = mdet.id_item
                left join orga.vfuncionario fun on fun.id_funcionario = mov.id_funcionario
                left join param.vproveedor prov on prov.id_proveedor = mov.id_proveedor
                inner join alm.talmacen alm on alm.id_almacen = mov.id_almacen

                left join orga.tuo_funcionario uofun on uofun.id_funcionario = fun.id_funcionario
                and uofun.fecha_asignacion <= '''||v_parametros.fecha_fin || '''
                left join orga.tuo_funcionario uofun1 on uofun1.id_funcionario = fun.id_funcionario
                and '''||v_parametros.fecha_fin || ''' BETWEEN uofun1.fecha_asignacion and uofun1.fecha_finalizacion

                left join orga.tcargo_presupuesto cp on cp.id_cargo = uofun1.id_cargo
				left join param.vcentro_costo cc on cc.id_centro_costo = cp.id_centro_costo
                --left join alm.titem_partida ipar on ipar.id_item = item.id_item
                left join pre.tpartida par on par.id_partida = item.id_partida

                where mval.cantidad > 0
                and mov.estado_mov = ''finalizado''
                and uofun1.estado_funcional = ''activo''
                and uofun.estado_funcional = ''activo''
                and uofun1.estado_reg = ''activo''
                and uofun.estado_reg = ''activo''
                and mdet.fecha_reg::date BETWEEN '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin ||'''
                and ';

				v_consulta:=v_consulta||v_parametros.filtro;
                v_consulta = v_consulta || v_where;
				v_consulta:=v_consulta||' order by '||v_parametros.ordenacion||' '||v_parametros.dir_ordenacion ||' limit '||v_parametros.cantidad||' offset '||v_parametros.puntero;
                raise notice '%',v_consulta;
	        return v_consulta;
	    end;

    /*********************************
     #TRANSACCION:  'SAL_REITEN_CONT'
     #DESCRIPCION:  Conteo de registros
     #AUTOR:        RCM
     #FECHA:        13/08/2013
    ***********************************/
	elsif(p_transaccion='SAL_REITEN_CONT')then

        begin
        	--Fecha
            if v_parametros.fecha_ini is not null and v_parametros.fecha_fin is not null then
				v_where = ' and date_trunc(''day'',mov.fecha_mov) between ''' ||v_parametros.fecha_ini||''' and ''' || v_parametros.fecha_fin || '''';
            else
            	raise exception 'Fechas no definidas';
            end if;

        	--Tipo de Movimiento
			if v_parametros.tipo_mov = 'ingreso' then
				v_where = ' and mtipo.tipo = ''ingreso''';
			elsif v_parametros.tipo_mov = 'salida' then
				v_where = ' and mtipo.tipo = ''salida''';
			end if;

            --Solicitante
            if v_parametros.tipo_sol = 'func' then
                if v_parametros.all_funcionario = 'Seleccionar Funcionarios' then
	            	v_where = v_where || ' and fun.id_funcionario = ANY(ARRAY['||v_parametros.id_funcionario||'])';
	            elsif v_parametros.all_funcionario = 'Por Organigrama' then
	            	--Obtener los IDs de todos los organigramas
					v_ids=orga.f_get_id_uo(v_parametros.id_estructura_uo);
					v_where = v_where || ' and uofun.id_uo in (' ||v_ids||')';
				end if;

            elsif v_parametros.tipo_sol = 'prov' then
            	if coalesce(v_parametros.id_proveedor,'') != '' then
            		v_where = v_where || ' and prov.id_proveedor in (' || v_parametros.id_proveedor || ')';
                end if;
            end if;

            --Items
            if v_parametros.all_items = 'Seleccionar Items' then
            	v_where = v_where || ' and item.id_item = ANY(ARRAY['||v_parametros.id_items||'])';
            elsif v_parametros.all_items = 'Por Clasificacion' then
            	--Obtener los IDs de todas las clasificaciones
				v_ids=alm.f_get_id_clasificaciones_varios(v_parametros.id_clasificacion);
				v_where = v_where || ' and item.id_clasificacion in (' ||v_ids||')';
			end if;

            --Almacenes
            if v_parametros.all_alm = 'no' then
            	v_where = v_where || ' and mov.id_almacen in('||v_parametros.id_almacen||')';
            end if;

	    	v_consulta:='
	        	select count(distinct mval.id_movimiento_det_valorado)

                from alm.tmovimiento_det mdet
                inner join alm.tmovimiento_det_valorado mval on mval.id_movimiento_det = mdet.id_movimiento_det
                inner join alm.tmovimiento mov on mov.id_movimiento = mdet.id_movimiento
                inner join alm.tmovimiento_tipo mtipo on mtipo.id_movimiento_tipo = mov.id_movimiento_tipo
                inner join alm.titem item on item.id_item = mdet.id_item
                left join orga.vfuncionario fun on fun.id_funcionario = mov.id_funcionario
                left join param.vproveedor prov on prov.id_proveedor = mov.id_proveedor
                inner join alm.talmacen alm on alm.id_almacen = mov.id_almacen

                left join orga.tuo_funcionario uofun on uofun.id_funcionario = fun.id_funcionario
                and uofun.fecha_asignacion <= '''||v_parametros.fecha_fin || '''
                left join orga.tuo_funcionario uofun1 on uofun1.id_funcionario = fun.id_funcionario
                and '''||v_parametros.fecha_fin || ''' BETWEEN uofun1.fecha_asignacion and uofun1.fecha_finalizacion

                left join orga.tcargo_presupuesto cp on cp.id_cargo = uofun1.id_cargo
				left join param.vcentro_costo cc on cc.id_centro_costo = cp.id_centro_costo
                --left join alm.titem_partida ipar on ipar.id_item = item.id_item
                left join pre.tpartida par on par.id_partida = item.id_partida

                where mval.cantidad > 0
                and mov.estado_mov = ''finalizado''
                and uofun1.estado_funcional = ''activo''
                and uofun.estado_funcional = ''activo''
                and uofun1.estado_reg = ''activo''
                and uofun.estado_reg = ''activo''
                and mdet.fecha_reg::date BETWEEN '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin ||'''
                and ';

			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta = v_consulta || v_where;

			return v_consulta;
         end;
    /*********************************
 	#TRANSACCION:  'SAL_ITEM_CU_SEL'
 	#DESCRIPCION:	Consulta de datos Item, Costo Unitario Individual
 	#AUTOR:			franklin.espinoza
 	#FECHA:			23/10/2018
	***********************************/

	elsif(p_transaccion='SAL_ITEM_CU_SEL')then

    	begin

            --1. Crear tabla temporal con un solo mes, que adelante sera complementada con mas campos para mas meses si es el caso
            create temp table tt_rep_kardex_item(
              id serial,
              fecha timestamp,
              nro_mov varchar,
              almacen varchar,
              motivo varchar,
              ingreso numeric,
              salida numeric,
              saldo numeric,
              costo_unitario numeric,
              ingreso_val numeric,
              salida_val numeric,
              saldo_val numeric,
              id_movimiento integer,
              id_movimiento_det_valorado integer,
              id_mov_det_val_origen integer,
              nro_tramite varchar,
              id_item	  integer,
              codigo	  varchar,
              nombre	  varchar,
              unidad_medida varchar,
              clasificacion varchar,
              nombre_almacen varchar,
              tipo_movimiento varchar
            ) on commit drop;

                if (v_parametros.all_items = 'Todos los Items') then
                    v_where = 'where ';
                elsif (v_parametros.all_items = 'Seleccionar Items') then
                    v_where = 'where itm.id_item = ANY(ARRAY['||v_parametros.id_items||']) and ';
                elsif (v_parametros.all_items = 'Por Clasificacion') then
                    --Obtener los IDs de todas las clasificaciones
                    v_ids=alm.f_get_id_clasificaciones_varios(v_parametros.id_clasificacion);
                    IF (v_ids = '') THEN
                       v_ids = '0';
                    END IF;
                    v_where = 'where itm.id_clasificacion in (' ||v_ids||') and ';
                else
                    raise exception 'Error desconocido';
                end if;

             v_consulta_aux = '
            	select
                itm.id_item
                from alm.titem itm
                inner join alm.tclasificacion cla on cla.id_clasificacion = itm.id_clasificacion
                '||v_where||' itm.codigo is not null
            ';

            for v_items in execute(v_consulta_aux) loop

                --3.Carga los datos en la table temporal
                v_consulta = '
                insert into tt_rep_kardex_item(
                	fecha,nro_mov,almacen,motivo,ingreso,salida,
                	ingreso_val,salida_val,costo_unitario,id_movimiento, id_movimiento_det_valorado , id_mov_det_val_origen, nro_tramite,
                	id_item, codigo, nombre, unidad_medida, clasificacion, nombre_almacen, tipo_movimiento
                )
                select
                mov.fecha_mov as fecha,
                mov.codigo as nro_mov,
                alma.codigo as almacen,
                mtipo.nombre as motivo,
                case mtipo.tipo
                    when ''ingreso'' then sum(mdval.cantidad)
                    else 0
                end as ingreso,
                case mtipo.tipo
                    when ''salida'' then sum(mdval.cantidad)
                    else 0
                end as salida,
                case mtipo.tipo
                    when ''ingreso'' then coalesce(sum(mdval.cantidad),0) * coalesce(mdval.costo_unitario,0)
                    else 0
                end as ingreso_val,
                case mtipo.tipo
                    when ''salida'' then coalesce(sum(mdval.cantidad),0) * coalesce(mdval.costo_unitario,0)
                    else 0
                end as salida_val,
                case when (mtipo.tipo = ''ingreso'' or  mtipo.tipo = ''salida'') and  '''||v_parametros.porcentaje ||''' = ''ochenta'' then coalesce(mdval.costo_unitario*0.87,0)
                    else coalesce(mdval.costo_unitario,0)
                end as costo_unitario,
                mov.id_movimiento,
                mdval.id_movimiento_det_valorado,
                mdval.id_mov_det_val_origen,
                tpw.nro_tramite,

                item.id_item,
                item.codigo::varchar,
                item.descripcion::varchar as nombre,
                umed.codigo::varchar as unidad_medida,
                (cla.codigo_largo||'' - ''||cla.nombre)::varchar as clasificacion,
        		alma.nombre as nombre_almacen,
                mtipo.tipo as tipo_movimiento
                from alm.tmovimiento mov
                inner join wf.tproceso_wf tpw on tpw.id_proceso_wf = mov.id_proceso_wf
                inner join alm.tmovimiento_det mdet on mdet.id_movimiento = mov.id_movimiento
                inner join alm.tmovimiento_det_valorado mdval on mdval.id_movimiento_det = mdet.id_movimiento_det
                inner join alm.titem item on item.id_item = mdet.id_item
                inner join param.tunidad_medida umed on umed.id_unidad_medida = item.id_unidad_medida
                inner join alm.tclasificacion cla on cla.id_clasificacion = item.id_clasificacion
                inner join alm.talmacen alma on alma.id_almacen = mov.id_almacen
                inner join alm.tmovimiento_tipo mtipo on mtipo.id_movimiento_tipo = mov.id_movimiento_tipo
                where mov.estado_mov = ''finalizado'' and mdet.cantidad > 0';

                if coalesce(v_parametros.id_almacen,0) != 0 then
                	v_consulta = v_consulta || ' and mov.id_almacen in ('||v_parametros.id_almacen||')';
                end if;

                v_consulta = v_consulta || '
                and date_trunc(''day'',mov.fecha_mov) between ''01/01/2013''::date and ''' || v_parametros.fecha_hasta ||'''::date
                and mdet.id_item = ' || v_items.id_item ||'
                group by mov.fecha_mov, mov.codigo, alma.codigo, mtipo.nombre, mov.id_movimiento, mtipo.tipo,mdval.costo_unitario, mdval.id_movimiento_det_valorado,
                mdval.id_mov_det_val_origen, tpw.nro_tramite, item.id_item, item.codigo, item.descripcion, unidad_medida, clasificacion, nombre_almacen, tipo_movimiento
                order by mov.fecha_mov, mov.codigo';

                raise notice 'v_consulta: %', v_consulta;

                execute(v_consulta);
            end loop;

            v_consulta = '
            		select
                            fecha,
                            nro_mov,
                            almacen,
                            motivo,
                            ingreso,
                            salida,
                            saldo,
                            costo_unitario,
                            ingreso_val,
                            salida_val,
                            saldo_val,
                            id_movimiento,
                            id_movimiento_det_valorado,
                            id_mov_det_val_origen,
                            nro_tramite,
                            id_item,
                            codigo,
                            nombre,
                            unidad_medida,
                            clasificacion,
                            nombre_almacen,
                            tipo_movimiento
                    from tt_rep_kardex_item
                    --order by  id_item, codigo
                ';
            --RAISE NOTICE 'v_consulta: %', v_consulta;
            return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'SAL_ITEMS_DESG_SEL'
 	#DESCRIPCION:	Consulta de datos Item, Costo Unitario Desglose
 	#AUTOR:			franklin.espinoza
 	#FECHA:			23/10/2018
	***********************************/

	elsif(p_transaccion='SAL_ITEMS_DESG_SEL')then

    	begin

            --1. Crear tabla temporal con un solo mes, que adelante sera complementada con mas campos para mas meses si es el caso
            create temp table tt_items_desglose(
              nro_tramite varchar,
              tipo_movimiento varchar,
              cantidad numeric,
              costo_unitario numeric,
              fecha_mov date,
              fecha_salida date,
              saldo_actual numeric,
              id_item	  integer,
              codigo	  varchar,
              nombre	  varchar,
              unidad_medida varchar,
              clasificacion varchar,
              nombre_almacen varchar

            ) on commit drop;

            if (v_parametros.all_items = 'Todos los Items') then
                    v_where = 'where ';
            elsif (v_parametros.all_items = 'Seleccionar Items') then
                v_where = 'where itm.id_item = ANY(ARRAY['||v_parametros.id_items||']) and ';
            elsif (v_parametros.all_items = 'Por Clasificacion') then
                --Obtener los IDs de todas las clasificaciones
                v_ids=alm.f_get_id_clasificaciones_varios(v_parametros.id_clasificacion);

                IF (v_ids = '') THEN
                   v_ids = '0';
                END IF;
                v_where = 'where itm.id_clasificacion in (' ||v_ids||') and ';
            else
                raise exception 'Error desconocido';
            end if;

            v_consulta_aux = '
            	select
                itm.id_item
                from alm.titem itm
                inner join alm.tclasificacion cla on cla.id_clasificacion = itm.id_clasificacion
                '||v_where||' itm.codigo is not null
            ';

            for v_items in execute(v_consulta_aux) loop

                --3.Carga los datos en la table temporal
                v_consulta = '
                insert into tt_items_desglose
                select
                    tpw.nro_tramite,
                    movtip.tipo,
                    detval.cantidad,
                    detval.costo_unitario,
                    mov.fecha_mov,
                    mov.fecha_salida,
                    detval.aux_saldo_fisico as saldo_actual,
                    item.id_item,
                    item.codigo::varchar,
                    item.descripcion::varchar as nombre,
                    umed.codigo::varchar as unidad_medida,
                    (cla.codigo_largo||'' - ''||cla.nombre)::varchar as clasificacion,
                    alma.nombre as nombre_almacen
                from alm.tmovimiento_det_valorado detval
                inner join alm.tmovimiento_det movdet on movdet.id_movimiento_det = detval.id_movimiento_det
                inner join alm.tmovimiento mov on mov.id_movimiento = movdet.id_movimiento
                inner join wf.tproceso_wf tpw on tpw.id_proceso_wf = mov.id_proceso_wf
                inner join alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo

                inner join alm.titem item on item.id_item = movdet.id_item
                inner join param.tunidad_medida umed on umed.id_unidad_medida = item.id_unidad_medida
                inner join alm.tclasificacion cla on cla.id_clasificacion = item.id_clasificacion
                inner join alm.talmacen alma on alma.id_almacen = mov.id_almacen
                where movdet.estado_reg = ''activo''
                    and movtip.tipo = ''ingreso''
                    and movdet.id_item = '||v_items.id_item||'
                    and mov.estado_mov = ''finalizado''
                    and mov.id_almacen = '||v_parametros.id_almacen||'
                    and mov.fecha_mov < '''||v_parametros.fecha_hasta||'''::date and detval.aux_saldo_fisico !=0 ';


                execute(v_consulta);

            end loop;

            v_consulta = '
            		select
                      nro_tramite,
                      tipo_movimiento,
                      cantidad,
                      /*case when '''||v_parametros.porcentaje ||''' = ''ochenta'' then (costo_unitario*0.87) else*/ costo_unitario /*end*/ as costo_unitario,
                      fecha_mov,
                      fecha_salida,
                      saldo_actual,
                      id_item,
                      codigo,
                      nombre,
                      unidad_medida,
                      clasificacion,
                      nombre_almacen
                    from tt_items_desglose
                    --group by costo_unitario, nro_tramite,tipo_movimiento,fecha_mov, fecha_salida, saldo_actual, id_item, codigo, nombre, unidad_medida, clasificacion, nombre_almacen
                    order by  id_item, codigo
                ';
                --raise exception 'hola';
            return v_consulta;

		end;
	/*********************************
     #TRANSACCION:  'SAL_MIN_EXIST_SEL'
     #DESCRIPCION:  Retorna las existencias de items a nivel clasificacion totales
     #AUTOR:        franklin.espinoza
     #FECHA:        26-02-2020
    ***********************************/

	elsif(p_transaccion='SAL_MIN_EXIST_SEL') then
		begin
      v_consulta = '
                    select
                    codigo,
                    nombre,
                    saldo_ini::numeric,
                    ingreso::numeric,
                    salida::numeric,
                    saldo_fin::numeric,
                    descripcion,
                    tamano,
                    id_clasificacion_fk
                    from alm.f_get_arbol_item('''||v_parametros.id_clasificacion||''','|| v_parametros.id_almacen||','''||v_parametros.all_alm||''', '''||v_parametros.fecha_ini||'''::date, '''||v_parametros.fecha_fin||'''::date)
      ';
	        return v_consulta;
	    end;
	else
  		raise exception 'Transacción inexistente';
	end if;
EXCEPTION
  WHEN OTHERS THEN
    v_respuesta='';
    v_respuesta=pxp.f_agrega_clave(v_respuesta,'mensaje',SQLERRM);
    v_respuesta=pxp.f_agrega_clave(v_respuesta,'codigo_error',SQLSTATE);
    v_respuesta=pxp.f_agrega_clave(v_respuesta,'procedimiento',v_nombre_funcion);
    raise exception '%',v_respuesta;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;