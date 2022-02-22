CREATE OR REPLACE FUNCTION alm.ft_movimiento_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/***************************************************************************
 SISTEMA:        Almacenes
 FUNCION:        alm.ft_movimiento_sel
 DESCRIPCION:    Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'alm.tmovimiento'
 AUTOR:          Gonzalo Sarmiento
 FECHA:          02-10-2012
 COMENTARIOS:
***************************************************************************/

DECLARE
  v_nombre_funcion    varchar;
  v_consulta         varchar;
  v_parametros         record;
  v_respuesta        varchar;
  v_fecha_ini        date;
  v_fecha_fin        date;
  v_periodo_subsistema_estado varchar;
  v_filtro             varchar;
  v_filadd           varchar;
  v_tipo_mov        varchar;
  v_historico        varchar;
  v_id_func_wf		integer;
BEGIN
  v_nombre_funcion='alm.ft_movimiento_sel';
  v_parametros=pxp.f_get_record(p_tabla);


      /*********************************
     #TRANSACCION:  'SAL_MOV_SEL'
     #DESCRIPCION:  Consulta de datos
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        02-12-2012
    ***********************************/

    if(p_transaccion='SAL_MOV_SEL')then
      begin
        v_filtro='';

        IF  pxp.f_existe_parametro(p_tabla,'historico') THEN

             v_historico =  v_parametros.historico;

        ELSE

            v_historico = 'no';

        END IF;


        if (v_parametros.id_funcionario_usu is null) then
            v_parametros.id_funcionario_usu = -1;
        end if;



        if lower(v_parametros.tipo_interfaz) = 'movimientoreq' or  lower(v_parametros.tipo_interfaz) = 'movimientoreqsalida' then
            if p_administrador !=1 then
                v_filtro = '(mov.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' or mov.id_usuario_reg='||p_id_usuario||' ) and ';
            end if;
            if lower(v_parametros.tipo_interfaz) = 'movimientoreq' then
              if v_historico = 'no' then
                  v_filtro = v_filtro || ' lower(mov.estado_mov)=''borrador'' and ';
              end if;
            end if;
        elsif lower(v_parametros.tipo_interfaz) = 'movimientoalm' then
            if v_historico = 'no' then
                v_filtro = ' lower(mov.estado_mov)=''prefin'' and ';
            end if;
            if p_administrador != 1 then
                v_filtro = v_filtro || 'mov.id_almacen in (	select id_almacen
            									from alm.talmacen_usuario
            									where estado_reg = ''activo'' and id_usuario = '||p_id_usuario ||' ) and ';
            end if;
        elsif lower(v_parametros.tipo_interfaz) = 'movimientovb' then
            if p_administrador !=1 then

                v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' )
            				and lower(mov.estado_mov) not in (''borrador'' ,''finalizado'',''cancelado'',''prefin'',''pendiente'') and ';
            else
                v_filtro = 'lower(mov.estado_mov) not in (''borrador'' ,''finalizado'',''cancelado'',''prefin'',''pendiente'') and ';
            end if;

        end if;


        v_consulta:='
        	SELECT
            	mov.id_movimiento,
                movtip.tipo as tipo,
            	mov.id_movimiento_tipo,
                movtip.nombre as nombre_movimiento_tipo,
            	mov.id_funcionario,
                emp.desc_funcionario1::varchar as nombre_funcionario,
                fun.descripcion_cargo,
       			fun.lugar_nombre as estacion,
       			fun.oficina_nombre as oficina,
            	mov.id_proveedor,
                pro.desc_proveedor::varchar as nombre_proveedor,
                mov.id_almacen,
                almo.nombre as nombre_almacen,
            	mov.id_almacen_dest,
                almd.nombre as nombre_almacen_destino,
            	mov.fecha_mov,
            	mov.codigo,
            	mov.descripcion,
            	mov.observaciones,
                mov.id_movimiento_origen,
                movorig.codigo as codigo_origen,
                mov.id_proceso_wf,
                mov.id_estado_wf,
            	mov.estado_mov,
            	usu1.cuenta as usr_reg,
            	mov.fecha_reg,
            	usu2.cuenta as usr_mod,
            	mov.fecha_mod,
            	mov.id_depto_conta,
            	dpto.nombre as nombre_depto,
                mov.comail,
                mov.fecha_salida,
                pw.nro_tramite,
                mov.codigo_tran,
                mov.id_plantilla,
                pla.desc_plantilla
            FROM alm.tmovimiento mov
            INNER JOIN alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo
            INNER JOIN alm.talmacen almo on almo.id_almacen = mov.id_almacen
            INNER JOIN segu.tusuario usu1 on usu1.id_usuario = mov.id_usuario_reg

            LEFT JOIN wf.testado_wf ew on  ew.id_estado_wf = mov.id_estado_wf and ew.estado_reg = ''activo''
            LEFT JOIN wf.tproceso_wf pw on pw.id_proceso_wf=mov.id_proceso_wf

            LEFT JOIN orga.vfuncionario_cargo_lugar fun on fun.id_funcionario = mov.id_funcionario
            and now() between fun.fecha_asignacion and COALESCE(fun.fecha_finalizacion,now())
            and fun.id_uo_funcionario < 10000000
            INNER JOIN orga.vfuncionario emp on emp.id_funcionario=mov.id_funcionario
            LEFT JOIN param.vproveedor pro on pro.id_proveedor = mov.id_proveedor
            LEFT JOIN alm.talmacen almd on almd.id_almacen = mov.id_almacen_dest
            LEFT JOIN alm.tmovimiento movorig on movorig.id_movimiento = mov.id_movimiento_origen
            LEFT JOIN segu.tusuario usu2 on usu2.id_usuario = mov.id_usuario_mod
            LEFT JOIN param.tdepto dpto on dpto.id_depto = mov.id_depto_conta
            left join param.tplantilla pla on pla.id_plantilla = mov.id_plantilla
			WHERE mov.estado_reg != ''inactivo'' and ';

        v_consulta:=v_consulta||v_filtro;
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by '||v_parametros.ordenacion||' '||v_parametros.dir_ordenacion||' limit '||v_parametros.cantidad||' offset '||v_parametros.puntero;
        raise notice '%',v_consulta;
        return v_consulta;
    end;
  /*********************************
     #TRANSACCION:  'SAL_MOV_CONT'
     #DESCRIPCION:  Conteo de registros
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        24-09-2012
    ***********************************/
  elsif(p_transaccion='SAL_MOV_CONT')then
    begin
        v_filtro='';

        IF  pxp.f_existe_parametro(p_tabla,'historico') THEN

             v_historico =  v_parametros.historico;

        ELSE

            v_historico = 'no';

        END IF;


        if (v_parametros.id_funcionario_usu is null) then
            v_parametros.id_funcionario_usu = -1;
        end if;



        if lower(v_parametros.tipo_interfaz) = 'movimientoreq' or  lower(v_parametros.tipo_interfaz) = 'movimientoreqsalida' then
            if p_administrador !=1 then
                v_filtro = '(mov.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' or mov.id_usuario_reg='||p_id_usuario||' ) and ';
            end if;
            if lower(v_parametros.tipo_interfaz) = 'movimientoreq' then
                if v_historico = 'no' then
                    v_filtro = v_filtro || ' lower(mov.estado_mov)=''borrador'' and ';
                end if;
            end if;
        elsif lower(v_parametros.tipo_interfaz) = 'movimientoalm' then
            if v_historico = 'no' then
                v_filtro = ' lower(mov.estado_mov)=''prefin'' and ';
            end if;
            if p_administrador != 1 then
                v_filtro = v_filtro || 'mov.id_almacen in (	select id_almacen
            									from alm.talmacen_usuario
            									where estado_reg = ''activo'' and id_usuario = '||p_id_usuario ||' ) and ';
            end if;
        elsif lower(v_parametros.tipo_interfaz) = 'movimientovb' then
            if p_administrador !=1 then
                v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' )
            				and lower(mov.estado_mov) not in (''borrador'' ,''finalizado'',''cancelado'',''prefin'') and ';
            else
                v_filtro = 'lower(mov.estado_mov) not in (''borrador'' ,''finalizado'',''cancelado'',''prefin'') and ';
            end if;

        end if;



        v_consulta:='
        	select count(mov.id_movimiento)
        	FROM alm.tmovimiento mov
            INNER JOIN alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo
            INNER JOIN alm.talmacen almo on almo.id_almacen = mov.id_almacen
            INNER JOIN segu.tusuario usu1 on usu1.id_usuario = mov.id_usuario_reg

            LEFT JOIN wf.testado_wf ew on  ew.id_estado_wf = mov.id_estado_wf and ew.estado_reg = ''activo''
            LEFT JOIN wf.tproceso_wf pw on pw.id_proceso_wf=mov.id_proceso_wf

            LEFT JOIN orga.vfuncionario_cargo_lugar fun on fun.id_funcionario = mov.id_funcionario
            and now() between fun.fecha_asignacion and COALESCE(fun.fecha_finalizacion,now())
            and fun.id_uo_funcionario < 1000000
            LEFT JOIN param.vproveedor pro on pro.id_proveedor = mov.id_proveedor
            LEFT JOIN alm.talmacen almd on almd.id_almacen = mov.id_almacen_dest
            LEFT JOIN alm.tmovimiento movorig on movorig.id_movimiento = mov.id_movimiento_origen
            LEFT JOIN segu.tusuario usu2 on usu2.id_usuario = mov.id_usuario_mod
            LEFT JOIN param.tdepto dpto on dpto.id_depto = mov.id_depto_conta
            left join param.tplantilla pla on pla.id_plantilla = mov.id_plantilla
            WHERE mov.estado_reg != ''inactivo'' and ';


        v_consulta:=v_consulta||v_filtro;
        v_consulta:=v_consulta||v_parametros.filtro;
        raise notice '%',v_consulta;
        return v_consulta;



     end;

  /*********************************
     #TRANSACCION:  'SAL_MOVREPORT_SEL'
     #DESCRIPCION:  Consulta de datos
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        28-02-2013
    ***********************************/

    elseif(p_transaccion='SAL_MOVREPORT_SEL')then
      begin
        v_consulta:='
        	select
            	item.codigo,
                item.nombre,
                item.descripcion as descripcion_item,
                umed.codigo as unidad_medida,
                item.id_clasificacion,
                cla.nombre as nombre_clasificacion,
                sum(detval.cantidad) as cantidad,
                detval.costo_unitario,
                --case when item.codigo != ''2.5.6.91'' then round(sum(detval.cantidad * detval.costo_unitario)*0.87,2)/detval.cantidad else round(sum(detval.cantidad  * detval.costo_unitario),2)/detval.cantidad end as costo_unitario,
                sum(detval.cantidad) * detval.costo_unitario as costo_total,
                --case when item.codigo != ''2.5.6.91'' then round(sum(detval.cantidad * detval.costo_unitario)*0.87,2) else round(sum(detval.cantidad  * detval.costo_unitario),2) end as costo_total,
                --case when item.codigo != ''2.5.6.91'' then round(sum(detval.cantidad * detval.costo_unitario)*0.87,2) else round(sum(detval.cantidad  * detval.costo_unitario),2) end as costo_total,
                mov.codigo as codigo_mov,
                mov.comail,
                almac.nombre as nombre_almacen,
                mtipo.tipo,
                mtipo.nombre as nombre_movimiento_tipo,
                mov.descripcion,
                mov.observaciones,
                to_char(mov.fecha_mov,''dd/mm/yyyy'')::varchar as fecha_mov,
                fun.desc_funcionario1 as nombre_funcionario,
                prov.desc_proveedor as nombre_proveedor,
                to_char(mov.fecha_mod,''dd/mm/yyyy'')::varchar as fecha_mod,
                sum(movdet.cantidad_solicitada) as cantidad_solicitada,
                to_char(mov.fecha_salida,''dd/mm/yyyy'')::varchar as fecha_salida,
                mov.codigo_tran
            from alm.tmovimiento_det_valorado detval
            inner join alm.tmovimiento_det movdet on movdet.id_movimiento_det = detval.id_movimiento_det
            inner join alm.titem item on item.id_item = movdet.id_item
            inner join param.tunidad_medida umed on umed.id_unidad_medida = item.id_unidad_medida
            left join alm.tclasificacion cla on cla.id_clasificacion = item.id_clasificacion
            inner join alm.tmovimiento mov on mov.id_movimiento = movdet.id_movimiento
            inner join alm.talmacen almac on almac.id_almacen = mov.id_almacen
            inner join alm.tmovimiento_tipo mtipo on mtipo.id_movimiento_tipo = mov.id_movimiento_tipo
            left join orga.vfuncionario fun on fun.id_funcionario = mov.id_funcionario
            left join param.vproveedor prov on prov.id_proveedor = mov.id_proveedor
            where mov.estado_mov != ''cancelado'' and ';

        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta = v_consulta || ' group by item.codigo,
                item.nombre,
                item.descripcion,
                umed.codigo,
                item.id_clasificacion,
                cla.nombre,
                detval.costo_unitario,
                mov.codigo,
                mov.comail,
                almac.nombre,
                mtipo.tipo,
                mtipo.nombre,
                mov.descripcion,
                mov.observaciones,
                mov.fecha_mov,
                fun.desc_funcionario1,
                prov.desc_proveedor,
                mov.fecha_mod,
                mov.fecha_salida,
                mov.codigo_tran, detval.cantidad ';
        v_consulta:=v_consulta||' order by '||v_parametros.ordenacion||' '||v_parametros.dir_ordenacion||' limit '||v_parametros.cantidad||' offset '||v_parametros.puntero;
        raise notice 'v_consulta %', v_consulta;
        return v_consulta;
    end;

  /*********************************
     #TRANSACCION:  'SAL_MOVREPCON_SEL'
     #DESCRIPCION:  Consulta de datos reporte consolidado
     #AUTOR:        Gonzalo Sarmiento Sejas
     #FECHA:        25-05-2017
    ***********************************/

    elseif(p_transaccion='SAL_MOVREPCON_SEL')then
      begin
        v_consulta:='
        	select DISTINCT
            	item.codigo,
                item.nombre,
                item.descripcion as descripcion_item,
                umed.codigo as unidad_medida,
                item.id_clasificacion,
                cla.nombre as nombre_clasificacion,
                sum(detval.cantidad) as cantidad,
                mov.comail,
                almac.nombre as nombre_almacen,
                mtipo.tipo,
                mtipo.nombre as nombre_movimiento_tipo,
                mov.descripcion,
                alm.f_get_solicitantes_movimiento_dotaciones(mov.codigo_tran) as nombre_funcionario,
                prov.desc_proveedor as nombre_proveedor,
                sum(movdet.cantidad_solicitada) as cantidad_solicitada,
                mov.codigo_tran,
                fun.lugar_nombre
            from alm.tmovimiento_det_valorado detval
            inner join alm.tmovimiento_det movdet on movdet.id_movimiento_det = detval.id_movimiento_det
            inner join alm.titem item on item.id_item = movdet.id_item
            inner join param.tunidad_medida umed on umed.id_unidad_medida = item.id_unidad_medida
            left join alm.tclasificacion cla on cla.id_clasificacion = item.id_clasificacion
            inner join alm.tmovimiento mov on mov.id_movimiento = movdet.id_movimiento
            inner join alm.talmacen almac on almac.id_almacen = mov.id_almacen
            inner join alm.tmovimiento_tipo mtipo on mtipo.id_movimiento_tipo = mov.id_movimiento_tipo
            left join orga.vfuncionario_cargo_lugar fun on fun.id_funcionario = mov.id_funcionario
            and now() between fun.fecha_asignacion and COALESCE(fun.fecha_finalizacion,now())
            and fun.id_uo_funcionario < 10000000
            left join param.vproveedor prov on prov.id_proveedor = mov.id_proveedor
            where mov.estado_mov != ''cancelado'' and detval.cantidad > 0 and ';

        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta = v_consulta || ' group by item.codigo,
                item.nombre,
                item.descripcion,
                umed.codigo,
                item.id_clasificacion,
                cla.nombre,
                mov.comail,
                almac.nombre,
                mtipo.tipo,
                mtipo.nombre,
                mov.descripcion,
                prov.desc_proveedor,
                mov.codigo_tran,
                fun.lugar_nombre';
        v_consulta:=v_consulta||' order by '||v_parametros.ordenacion||' '||v_parametros.dir_ordenacion||' limit '||v_parametros.cantidad||' offset '||v_parametros.puntero;
        raise notice 'v_consulta %', v_consulta;
        return v_consulta;
    end;

  /*********************************
     #TRANSACCION:  'SAL_MOVREPORT_CONT'
     #DESCRIPCION:  Conteo de registros
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        28-02-2013
    ***********************************/
  elsif(p_transaccion='SAL_MOVREPORT_CONT')then
    begin
        v_consulta:='
        	select count(detval.id_movimiento)
        	from alm.tmovimiento_det_valorado detval
            inner join alm.tmovimiento_det movdet on movdet.id_movimiento_det = detval.id_movimiento_det
            inner join alm.titem item on item.id_item = movdet.id_item
            inner join param.tunidad_medida umed on umed.id_unidad_medida = item.id_unidad_medida
            left join alm.tclasificacion cla on cla.id_clasificacion = item.id_clasificacion
            where movdet.estado_reg = ''activo'' and ';
        v_consulta:= v_consulta||v_parametros.filtro;
        return v_consulta;
     end;

      /*********************************
     #TRANSACCION:  'SAL_MOVPENPER_SEL'
     #DESCRIPCION:  Consulta de datos
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        19-03-2013
    ***********************************/
    elseif(p_transaccion='SAL_MOVPENPER_SEL')then
      begin
        select peri.fecha_ini, peri.fecha_fin into v_fecha_ini, v_fecha_fin
        from param.tperiodo_subsistema pesu
        inner join param.tperiodo peri on peri.id_periodo = pesu.id_periodo
        where pesu.id_periodo_subsistema = v_parametros.id_periodo_subsistema;

        v_consulta:='
        	SELECT
            	mov.id_movimiento,
                mov.estado_mov
            FROM alm.tmovimiento mov
            WHERE mov.estado_reg = ''activo''
            	and mov.estado_mov = ''borrador''
                and mov.fecha_mov between ''' || v_fecha_ini || ''' and ''' || v_fecha_fin ||''' ;';
        return v_consulta;
    end;
    /*********************************
     #TRANSACCION:  'SAL_MOVPENPER_CONT'
     #DESCRIPCION:  Conteo de registros
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        19-03-2013
    ***********************************/
    elsif(p_transaccion='SAL_MOVPENPER_CONT')then
    begin
        select peri.fecha_ini, peri.fecha_fin, pesu.estado into v_fecha_ini, v_fecha_fin, v_periodo_subsistema_estado
        from param.tperiodo_subsistema pesu
        inner join param.tperiodo peri on peri.id_periodo = pesu.id_periodo
        where pesu.id_periodo_subsistema = v_parametros.id_periodo_subsistema;

        if (v_periodo_subsistema_estado = 'abierto') then
        v_consulta:='
			SELECT count(mov.id_movimiento)
		    FROM alm.tmovimiento mov
		    WHERE mov.estado_reg = ''activo''
			and mov.estado_mov = ''borrador''
			and mov.fecha_mov between ''' || v_fecha_ini || ''' and ''' || v_fecha_fin ||''' ;';
        else
            v_consulta:='SELECT count(null);';
        end if;
        return v_consulta;
     end;

     /*********************************
     #TRANSACCION:  'SAL_FUNCIOCAR_SEL'
     #DESCRIPCION:  Listado de los funcionarios para el registro del movimiento
     #AUTOR:        RCM
     #FECHA:        27/08/2013
    ***********************************/
    elseif(p_transaccion='SAL_FUNCIOCAR_SEL')then
          begin
              --Se obtiene el tipo de movimiento
              select tipo
              into v_tipo_mov
              from alm.tmovimiento_tipo
              where id_movimiento_tipo = v_parametros.id_movimiento_tipo;

              --Verifica la variable global para aplicar filtro
              if pxp.f_get_variable_global('alm_filtrar_funcionario_tipomov_asistente') = 'si' and v_tipo_mov = 'salida' then
                  v_filadd = '0=0 and ';
                IF (pxp.f_existe_parametro(p_tabla,'estado_reg_asi')) THEN
                   v_filadd = ' (FUNCAR.estado_reg_asi = '''||v_parametros.estado_reg_asi||''') and ';
                END IF;

                   v_consulta:='SELECT
	                            FUNCAR.id_uo_funcionario,
	                            FUNCAR.id_funcionario,
	                            FUNCAR.desc_funcionario1,
	                            FUNCAR.desc_funcionario2,
	                            FUNCAR.id_uo,
	                            FUNCAR.nombre_cargo,
	                            FUNCAR.fecha_asignacion,
	                            FUNCAR.fecha_finalizacion,
	                            FUNCAR.num_doc,
	                            FUNCAR.ci,
	                            FUNCAR.codigo,
	                            FUNCAR.email_empresa,
	                            FUNCAR.estado_reg_fun,
	                            FUNCAR.estado_reg_asi
	                            FROM orga.vfuncionario_cargo FUNCAR
	                            WHERE '||v_filadd || '
	                            FUNCAR.id_funcionario IN (select *
															  FROM orga.f_get_funcionarios_x_usuario_asistente('''||v_parametros.fecha||''',' ||
                                                              p_id_usuario || ',''norecursivo'') AS (id_funcionario INTEGER))
								AND FUNCAR.id_uo IN (SELECT id_uo from alm.tmovimiento_tipo_uo
													WHERE id_movimiento_tipo = '||v_parametros.id_movimiento_tipo||') AND';


                   v_consulta:=v_consulta||v_parametros.filtro;
                   v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion ||
                                   ' limit ' || v_parametros.cantidad || ' OFFSET ' || v_parametros.puntero;
              else
                  v_filadd = '0=0 and ';
                IF (pxp.f_existe_parametro(p_tabla,'estado_reg_asi')) THEN
                   v_filadd = ' (FUNCAR.estado_reg_asi = '''||v_parametros.estado_reg_asi||''') and ';
                END IF;

                   v_consulta:='SELECT
	                            FUNCAR.id_uo_funcionario,
	                            FUNCAR.id_funcionario,
	                            FUNCAR.desc_funcionario1,
	                            FUNCAR.desc_funcionario2,
	                            FUNCAR.id_uo,
	                            FUNCAR.nombre_cargo,
	                            FUNCAR.fecha_asignacion,
	                            FUNCAR.fecha_finalizacion,
	                            FUNCAR.num_doc,
	                            FUNCAR.ci,
	                            FUNCAR.codigo,
	                            FUNCAR.email_empresa,
	                            FUNCAR.estado_reg_fun,
	                            FUNCAR.estado_reg_asi
	                            FROM orga.vfuncionario_cargo FUNCAR
	                            WHERE '||v_filadd || '
	                            FUNCAR.id_funcionario IN (select *
															  FROM orga.f_get_funcionarios_x_usuario_asistente('''||v_parametros.fecha||''',' ||
                                                              p_id_usuario || ') AS (id_funcionario INTEGER)) AND ';


                   v_consulta:=v_consulta||v_parametros.filtro;
                   v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion ||
                                   ' limit ' || v_parametros.cantidad || ' OFFSET ' || v_parametros.puntero;



              end if;
              raise notice 'CONSULTA: %',v_consulta;
            return v_consulta;
        end;
    /*********************************
     #TRANSACCION:  'SAL_FUNCIOCAR_CONT'
     #DESCRIPCION:  Conteo de registros
     #AUTOR:        RCM
     #FECHA:        27/08/2013
    ***********************************/
    elsif(p_transaccion='SAL_FUNCIOCAR_CONT')then
        begin
            --Se obtiene el tipo de movimiento
              select tipo
              into v_tipo_mov
              from alm.tmovimiento_tipo
              where id_movimiento_tipo = v_parametros.id_movimiento_tipo;
            --Verifica la variable global para aplicar filtro
              if pxp.f_get_variable_global('alm_filtrar_funcionario_tipomov_asistente') = 'si' and v_tipo_mov = 'salida' then
                  v_filadd = '0=0 and ';
                IF (pxp.f_existe_parametro(p_tabla,'estado_reg_asi')) THEN
                   v_filadd = ' (FUNCAR.estado_reg_asi = '''||v_parametros.estado_reg_asi||''') and ';
                END IF;

               v_consulta:='SELECT
                            COUNT(FUNCAR.id_uo_funcionario)
                            FROM orga.vfuncionario_cargo FUNCAR
                            WHERE '||v_filadd || '
                            FUNCAR.id_funcionario IN (select *
                                                          FROM orga.f_get_funcionarios_x_usuario_asistente('''||v_parametros.fecha||''',' ||
                                                          p_id_usuario || ',''norecursivo'') AS (id_funcionario INTEGER))
                            AND FUNCAR.id_uo IN (SELECT id_uo from alm.tmovimiento_tipo_uo
                                                WHERE id_movimiento_tipo = '||v_parametros.id_movimiento_tipo||') AND';


               v_consulta:=v_consulta||v_parametros.filtro;

              else
                  v_filadd = '0=0 and ';
                IF (pxp.f_existe_parametro(p_tabla,'estado_reg_asi')) THEN
                   v_filadd = ' (FUNCAR.estado_reg_asi = '''||v_parametros.estado_reg_asi||''') and ';
                END IF;

                   v_consulta:='SELECT
	                            COUNT(FUNCAR.id_uo_funcionario)
	                            FROM orga.vfuncionario_cargo FUNCAR
	                            WHERE '||v_filadd || '
	                            FUNCAR.id_funcionario IN (select *
															  FROM orga.f_get_funcionarios_x_usuario_asistente('''||v_parametros.fecha||''',' ||
                                                              p_id_usuario || ') AS (id_funcionario INTEGER)) AND ';


                   v_consulta:=v_consulta||v_parametros.filtro;

              end if;

            return v_consulta;
     end;

  /*********************************
     #TRANSACCION:  'SAL_DECSOL_SEL'
     #DESCRIPCION:  Consulta de datos
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        14-11-2016
    ***********************************/

  elsif(p_transaccion='SAL_DECSOL_SEL')then
      begin

          v_consulta:='select vg.variable, vg.valor, vg.descripcion
                      from pxp.variable_global vg
                      where variable='''||v_parametros.alm_cantidad_decimales||'''';
          return v_consulta;
      end;

  /*********************************
     #TRANSACCION:  'SAL_DECSOL_CONT'
     #DESCRIPCION:  Consulta de datos
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        14-11-2016
    ***********************************/

  elsif(p_transaccion='SAL_DECSOL_CONT')then
      begin

          v_consulta:='select count(id_variable_global)
                      from pxp.variable_global vg
                      where variable='''||v_parametros.alm_cantidad_decimales||'''';
          return v_consulta;
      end;

  else

      raise exception 'Procedimiento no encontrado';

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

ALTER FUNCTION alm.ft_movimiento_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;