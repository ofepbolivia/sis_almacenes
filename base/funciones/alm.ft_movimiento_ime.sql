CREATE OR REPLACE FUNCTION alm.ft_movimiento_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:        Almacenes
 FUNCION:        alm.ft_movimiento_ime
 DESCRIPCION:    Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones) de la tabla 'alm.tmovimiento'
 AUTOR:          Gonzalo Sarmiento
 FECHA:          03-10-2012
 COMENTARIOS:
************************************************************************/
DECLARE

	v_nombre_funcion        		varchar;
	v_parametros          			record;
	v_id_movimiento_tipo    		integer;
	v_id_movimiento       			integer;
	v_id_movimiento_tipo_ingreso  	integer;
	v_id_movimiento_tipo_salida 	integer;
	v_respuesta         			varchar;
	v_id_movimiento_ingreso   		integer;
	v_id_movimiento_salida    		integer;
	v_transferencia       			record;
	v_consulta          			varchar;
	v_detalle           			record;
	v_contador          			numeric;
	v_contador_2          			numeric;
	v_estado_almacen        		varchar;
	v_estado_mov          			varchar;
	g_registros         			record;
	g_registros_2         			record;
	v_tipo_mov          			varchar;
	v_tipo_mov_personalizado    	varchar;
	v_id_almacen          			integer;
	v_codigo_valoracion     		varchar;
	v_saldo_cantidad        		numeric;
	v_aux_integer         			integer;
	v_costo_valorado        		numeric;
	v_cantidad_valorada     		numeric;
	v_id_movimiento_det_val_desc  	integer;
	v_nombre_item         			varchar;
	v_errores           			varchar;
	v_id_almacen_dest       		integer;
	v_id_movimiento_dest      		integer;
	v_id_movimiento_det_dest    	integer;
	v_fecha_mov_ultima      		timestamp;
	v_fecha_mov         			timestamp;
	v_id_depto          			integer;
	v_cod_documento       			varchar;
	v_alerta_amarilla       		numeric;
	v_alerta_roja         			numeric;
	v_cantidad_minima       		numeric;
	v_nombre_almacen        		varchar;
	v_alerts            			boolean;
	v_descripcion_alerta      		varchar;
	v_mostrar_alerts        		boolean;
	v_cant_aux            			numeric;
	v_id_periodo					integer;
	v_estado_periodo_subsistema		varchar;
	v_cod_almacen					varchar;
    v_codigo_tipo_proceso			varchar;
    v_id_proceso_macro				integer;
    v_num_tramite 					varchar;
    v_id_proceso_wf					integer;
    v_id_estado_wf					integer;
    v_codigo_estado					varchar;
	v_codigo_tran					varchar;
    v_movimientos					record;
    v_mov							record;
    v_id_movs						integer ARRAY;

    va_id_tipo_estado 				integer [];
    va_codigo_estado 				varchar [];
    va_disparador 					varchar [];
    va_regla 						varchar [];
    va_prioridad 					integer [];

    v_id_estado_actual  			integer;

    v_id_tipo_estado				integer;
    v_id_tipo_proceso				integer;
    v_id_funcionario				integer;
    v_id_usuario_reg				integer;
    v_id_estado_wf_ant				integer;

    v_pedir_obs						varchar;
    v_num_estados					integer;
    v_num_funcionarios				integer;
    v_num_deptos					integer;
    v_id_funcionario_estado			integer;
    v_id_depto_estado				integer;
    v_codigo_estado_siguiente		varchar;
    v_obs							text;
    v_uo_sol						varchar;
    v_codigo						varchar;
    v_asunto_alerta					varchar;
    v_id_preingreso					integer;
    v_rec_wf						record;
    v_codigo_mov_tipo				varchar;
    v_result						varchar;
    v_codigo_mov					varchar;
    v_tipo_nodo						varchar;
    v_aux_estado_wf					varchar;
    v_cont							integer;
    v_id_gestion					integer;
    v_aux_nombre_estado_wf			varchar;
    v_registros						record;
    v_id_item						integer;
    v_id_movimiento_det				integer;
    v_id_tipo_estado_wf				integer;
    v_id_funcionario_wf				integer;
    v_tipo_movimiento	varchar;
    v_codigo_movimiento	varchar;
	v_usuario						varchar;

    v_records						integer[];
	v_control_salida_id   		text;
  v_gestion 						integer;
BEGIN

	v_nombre_funcion='alm.ft_movimiento_ime';
	v_parametros=pxp.f_get_record(p_tabla);


	 /*********************************
     #TRANSACCION:  'SAL_MOV_INS'
     #DESCRIPCION:  Insercion de datos
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        03-10-2012
    ***********************************/
	if(p_transaccion='SAL_MOV_INS') then
  	begin

    	select
        v_parametros.id_movimiento_tipo,
        v_parametros.id_almacen,
        v_parametros.id_funcionario,
        v_parametros.id_proveedor,
        v_parametros.id_almacen_dest,
        v_parametros.fecha_mov,
        v_parametros.descripcion,
        v_parametros.observaciones,
        v_parametros.id_movimiento_origen,
        v_parametros.id_gestion,
        v_parametros.id_depto_conta,
        v_parametros.id_plantilla
        into g_registros;

        --Llama a la función de registro del movimiento
        v_id_movimiento = alm.f_insercion_movimiento(p_id_usuario,hstore(g_registros));

        v_respuesta =pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento almacenado correctamente');
        v_respuesta =pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_id_movimiento::varchar);

        return v_respuesta;
	end;

    /*********************************
     #TRANSACCION:  'SAL_MOVREST_INS'
     #DESCRIPCION:  Inserta un movimiento con su detalle y lo finaliza
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        03-10-2012
    ***********************************/
	elsif(p_transaccion='SAL_MOVREST_INS') then
  	begin
    	select g.id_gestion into v_id_gestion
        from param.tgestion g
        where g.gestion = to_char(v_parametros.fecha_mov,'YYYY')::integer;

		if (pxp.f_existe_parametro(p_tabla,'codigo_tran')) then

            select
              v_parametros.id_movimiento_tipo,
              v_parametros.id_almacen,
              v_parametros.id_funcionario,
              NULL,
              NULL,
              v_parametros.fecha_mov,
              v_parametros.descripcion,
              v_parametros.codigo_tran,
              NULL,
              NULL,
              v_id_gestion as id_gestion,
              NULL
              into g_registros;

        else
        	select
            v_parametros.id_movimiento_tipo,
            v_parametros.id_almacen,
            v_parametros.id_funcionario,
            NULL,
            NULL,
            v_parametros.fecha_mov,
            v_parametros.descripcion,
            NULL,
            NULL,
            v_id_gestion as id_gestion,
            NULL
            into g_registros;

        end if;


        --Llama a la función de registro del movimiento
        v_id_movimiento = alm.f_insercion_movimiento(p_id_usuario,hstore(g_registros));

        for v_registros in (select *
            					from json_populate_recordset(null::alm.detalle_movimiento,v_parametros.detalle::json))loop

        		v_id_item = NULL;
                select id_item into v_id_item
                from alm.titem i
                where i.codigo = v_registros.codigo_item;


                if (v_id_item is null) then
                	raise exception 'No existe un item con codigo: %', v_registros.codigo_item;
                end if;

                insert into alm.tmovimiento_det(
                    id_usuario_reg,
                    fecha_reg,
                    estado_reg,
                    id_movimiento,
                    id_item,
                    cantidad,
                    cantidad_solicitada,
                    costo_unitario,
                    fecha_caducidad,
                    observaciones,
                    id_concepto_ingas,
                    estado_dotacion
                ) VALUES (
                    p_id_usuario,
                    now(),
                    'activo',
                    v_id_movimiento,
                    v_id_item,
                    v_registros.cantidad,
                    v_registros.cantidad,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    'comprometido'
                ) RETURNING id_movimiento_det into v_id_movimiento_det;

                insert into alm.tmovimiento_det_valorado (
                    id_usuario_reg,
                    fecha_reg,
                    estado_reg,
                    id_movimiento_det,
                    cantidad,
                    costo_unitario,
                    aux_saldo_fisico
                ) VALUES (
                    p_id_usuario,
                    now(),
                    'activo',
                    v_id_movimiento_det,
                    v_registros.cantidad,
                    NULL,
                    v_registros.cantidad
                );
        end loop;

		select
        v_id_movimiento as id_movimiento,
        v_parametros.id_almacen as id_almacen,
        'verificar'::varchar as operacion,
        NULL as id_funcionario_wf,
        NULL as fecha_mov,
        NULL as id_tipo_estado,
        NULL as obs
        into g_registros;

        --Llama a la función de registro del movimiento
        v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(g_registros));

        v_id_tipo_estado_wf = pxp.f_obtiene_clave_valor(v_respuesta,'id_tipo_estado_wf','','nada','valor')::integer;

        select
        v_id_movimiento as id_movimiento,
        v_parametros.id_almacen as id_almacen,
        'siguiente'::varchar as operacion,
        v_parametros.id_funcionario_aprobador as id_funcionario_wf,
        NULL as fecha_mov,
        v_id_tipo_estado_wf as id_tipo_estado,
        NULL as obs
        into g_registros;

        --Llama a la función de registro del movimiento
        v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(g_registros));

        select
        v_id_movimiento as id_movimiento,
        v_parametros.id_almacen as id_almacen,
        'verificar'::varchar as operacion,
        NULL as id_funcionario_wf,
        NULL as fecha_mov,
        NULL as id_tipo_estado,
        NULL as obs
        into g_registros;

        --Llama a la función de registro del movimiento
        v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(g_registros));

        --Generación de Alertas
        v_mostrar_alerts = alm.f_generar_alertas_mov(p_id_usuario, v_id_movimiento);

        if v_mostrar_alerts then
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'alerts',v_mostrar_alerts::varchar);
        end if;

        v_id_tipo_estado_wf = pxp.f_obtiene_clave_valor(v_respuesta,'id_tipo_estado_wf','','nada','valor')::integer;
        v_id_funcionario_wf = pxp.f_obtiene_clave_valor(v_respuesta,'id_funcionario_wf','','nada','valor')::integer;

        /*select
        v_id_movimiento as id_movimiento,
        v_parametros.id_almacen as id_almacen,
        'siguiente'::varchar as operacion,
        v_id_funcionario_wf as id_funcionario_wf,
        NULL as fecha_mov,
        v_id_tipo_estado_wf as id_tipo_estado,
        NULL as obs
        into g_registros;

        --Llama a la función de registro del movimiento
        v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(g_registros));*/

        v_respuesta =pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento almacenado correctamente');
        v_respuesta =pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_id_movimiento::varchar);

        return v_respuesta;
	end;

    /*********************************
     #TRANSACCION:  'SAL_MOVS_REST_INS'
     #DESCRIPCION:  Inserta un movimiento con su detalle y lo finaliza
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        03-10-2012
    ***********************************/
	elsif(p_transaccion='SAL_MOVS_REST_INS') then
    begin
		CREATE GLOBAL TEMPORARY TABLE temp_movimientos
        (
           control_salida_id	INTEGER,
           id_movimiento		INTEGER
        )
        ON COMMIT DELETE ROWS;
           for v_mov in (select *
                                        from json_populate_recordset(null::alm.movimiento,v_parametros.movimientos::json)
                                        limit 1)loop
                select g.id_gestion into v_id_gestion
                from param.tgestion g
                where g.gestion = to_char(v_mov.fecha_mov,'YYYY')::integer;
        end loop;
    	FOR v_movimientos in (select *
                                        from json_populate_recordset(null::alm.movimiento,v_parametros.movimientos::json))loop
        	--raise exception 'llega %', v_id_movimiento::varchar;
                /*select g.id_gestion into v_id_gestion
                from param.tgestion g
                where g.gestion = to_char(v_movimientos.fecha_mov,'YYYY')::integer;*/

                if (v_movimientos.codigo_tran is not null) then

                    select
                      v_movimientos.id_movimiento_tipo,
                      v_movimientos.id_almacen,
                      v_movimientos.id_funcionario,
                      NULL,
                      NULL,
                      v_movimientos.fecha_mov,
                      v_movimientos.descripcion,
                      v_movimientos.codigo_tran,
                      NULL,
                      NULL,
                      v_id_gestion as id_gestion,
                      NULL
                      into g_registros;

                else
                    select
                    v_movimientos.id_movimiento_tipo,
                    v_movimientos.id_almacen,
                    v_movimientos.id_funcionario,
                    NULL,
                    NULL,
                    v_movimientos.fecha_mov,
                    v_movimientos.descripcion,
                    NULL,
                    NULL,
                    v_id_gestion as id_gestion,
                    NULL
                    into g_registros;

                end if;

        		--raise exception 'registros %', g_registros::text;
                --Llama a la función de registro del movimiento
                v_id_movimiento = alm.f_insercion_movimiento(p_id_usuario,hstore(g_registros));

                for v_registros in (select *
                                        from json_populate_recordset(null::alm.detalle_movimiento,v_movimientos.detalle::json))loop
						--raise exception 'llega %', v_registros.cantidad;
                        v_id_item = NULL;
                        select id_item into v_id_item
                        from alm.titem i
                        where i.codigo = v_registros.codigo_item;

                        if (v_id_item is null) then
                            raise exception 'No existe un item con codigo: %', v_registros.codigo_item;
                        end if;

                        insert into alm.tmovimiento_det(
                            id_usuario_reg,
                            fecha_reg,
                            estado_reg,
                            id_movimiento,
                            id_item,
                            cantidad,
                            cantidad_solicitada,
                            costo_unitario,
                            fecha_caducidad,
                            observaciones,
                            id_concepto_ingas,
                            estado_dotacion
                        ) VALUES (
                            p_id_usuario,
                            now(),
                            'activo',
                            v_id_movimiento,
                            v_id_item,
                            v_registros.cantidad,
                            v_registros.cantidad,
                            NULL,
                            NULL,
                            NULL,
                            NULL,
                            'comprometido'
                        ) RETURNING id_movimiento_det into v_id_movimiento_det;

                        insert into alm.tmovimiento_det_valorado (
                            id_usuario_reg,
                            fecha_reg,
                            estado_reg,
                            id_movimiento_det,
                            cantidad,
                            costo_unitario,
                            aux_saldo_fisico
                        ) VALUES (
                            p_id_usuario,
                            now(),
                            'activo',
                            v_id_movimiento_det,
                            v_registros.cantidad,
                            NULL,
                            v_registros.cantidad
                        );
                --v_respuesta =pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento almacenado correctamente');
        		--v_respuesta =pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_id_movimiento::varchar);
                end loop;


                --Llama a la función de registro del movimiento
                --observaciones a analizar franklin
                /*v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(g_registros));

                v_id_tipo_estado_wf = pxp.f_obtiene_clave_valor(v_respuesta,'id_tipo_estado_wf','','nada','valor')::integer;*/

                select
                m.id_proceso_wf, m.id_estado_wf, m.estado_mov
                into
                v_id_proceso_wf, v_id_estado_wf, v_estado_mov
                from alm.tmovimiento m
                where m.id_movimiento=v_id_movimiento;

                --Siguiente estado correspondiente al proceso del WF
                SELECT
                ps_id_tipo_estado, ps_codigo_estado, ps_disparador, ps_regla, ps_prioridad
                into
                va_id_tipo_estado, va_codigo_estado, va_disparador, va_regla, va_prioridad
                FROM wf.f_obtener_estado_wf(v_id_proceso_wf, v_id_estado_wf,NULL,'siguiente');

                select
                v_id_movimiento as id_movimiento,
                v_movimientos.id_almacen as id_almacen,
                'siguienteRest'::varchar as operacion,
                v_movimientos.id_funcionario_aprobador as id_funcionario_wf,
                NULL as fecha_mov,
                va_id_tipo_estado[1]::varchar as id_tipo_estado,
                NULL as obs
                into g_registros;
        		--raise exception 'registros %', g_registros;
                --Llama a la función de registro del movimiento
                v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(g_registros));



                --Generación de Alertas

                /*v_mostrar_alerts = alm.f_generar_alertas_mov(p_id_usuario, v_id_movimiento);

                if v_mostrar_alerts then
                    v_respuesta=pxp.f_agrega_clave(v_respuesta,'alerts',v_mostrar_alerts::varchar);
                end if;*/

                --raise 'registrps %', g_registros;
                /*v_id_tipo_estado_wf = pxp.f_obtiene_clave_valor(v_respuesta,'id_tipo_estado_wf','','nada','valor')::integer;
                v_id_funcionario_wf = pxp.f_obtiene_clave_valor(v_respuesta,'id_funcionario_wf','','nada','valor')::integer;*/

                INSERT INTO temp_movimientos VALUES (v_movimientos.control_salida_id ,v_id_movimiento);

			end loop;

			/*SELECT json_agg(json_build_object(	te.control_salida_id :: text,
												te.id_movimiento :: text))
			INTO   v_consulta
			FROM   temp_movimientos te;*/

            SELECT  array_to_json(array_agg(to_json(tm.*)))
            INTO 	v_consulta
			FROM 	temp_movimientos tm;

            v_result =pxp.f_agrega_clave(v_result,'mensaje','Movimiento almacenado correctamente');
        	v_result =pxp.f_agrega_clave(v_result,'movimientos',v_consulta);

            return v_result;
	end;


 /*********************************
     #TRANSACCION:  'SAL_REVREST_MOD'
     #DESCRIPCION:  Inserta un movimiento con su detalle y lo finaliza
     #AUTOR:        Alan Felipez, Yamil Medina
     #FECHA:        03-10-2012
    ***********************************/
	elsif(p_transaccion='SAL_REVREST_MOD') then
  	begin


		--if (pxp.f_existe_parametro(p_tabla,'codigo_tran')) then

			for v_registros in (SELECT id_proceso_wf,
                                       id_estado_wf,
                                       id_funcionario,
                                       id_movimiento,
                                       estado_mov,
                                       codigo_tran
                                FROM   alm.tmovimiento
                                WHERE  id_movimiento = any (translate(v_parametros.id_movimientos, '[]', '{}')::int[])
                                AND    codigo_tran is not NULL)loop

				if (v_registros.estado_mov = 'aprobado' or v_registros.estado_mov = 'prefin') then

                	--v_movimientos = v_movimientos || v_registros.id_movimiento;
                    SELECT te.id_tipo_estado into v_id_tipo_estado
                    FROM   wf.tproceso_wf pw
                           INNER JOIN wf.ttipo_proceso tp
                                   ON pw.id_tipo_proceso = tp.id_tipo_proceso
                           INNER JOIN wf.ttipo_estado te
                                   ON te.id_tipo_proceso = tp.id_tipo_proceso
                                      AND te.codigo = 'anulado'
                    WHERE  pw.id_proceso_wf = v_registros.id_proceso_wf;

                    if v_id_tipo_estado is null  then
                        raise exception 'No se parametrizo el estado "anulado" para reclamos';
                    end if;

                    -- pasamos el reclamo  al siguiente anulado
                    v_id_estado_actual =  wf.f_registra_estado_wf(
                        v_id_tipo_estado,
                        v_registros.id_funcionario,
                        v_registros.id_estado_wf,
                        v_registros.id_proceso_wf,
                        p_id_usuario,
                        null,
                        null,
                        null,
                        'Revercion de movimiento'|| COALESCE(v_registros.codigo_tran,'--')
                    );

                    update 	alm.tmovimiento
                    set 	estado_mov = 'anulado', fecha_mod = now()
                    where 	id_movimiento  = v_registros.id_movimiento;

                    update 	alm.tmovimiento_det
                    set 	estado_dotacion = 'revertido'
                    where 	id_movimiento = v_registros.id_movimiento;
                    v_id_movs = v_id_movs || v_registros.id_movimiento;
               --ELSE
               		--raise exception 'no se puede revertir un movimiento finalizado';
               end if;
            end loop;
        --else
        	--raise 'sin codigo de transaccion';
       -- end if;

		/*select
        v_id_movimiento as id_movimiento,
        v_parametros.id_almacen as id_almacen,
        'verificar'::varchar as operacion,
        NULL as id_funcionario_wf,
        NULL as fecha_mov,
        NULL as id_tipo_estado,
        NULL as obs
        into g_registros;

        --Llama a la función de registro del movimiento
        v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(g_registros));


        --Llama a la función de registro del movimiento
        v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(g_registros));

        --Generación de Alertas
        v_mostrar_alerts = alm.f_generar_alertas_mov(p_id_usuario, v_id_movimiento);

        if v_mostrar_alerts then
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'alerts',v_mostrar_alerts::varchar);
        end if;

        v_id_tipo_estado_wf = pxp.f_obtiene_clave_valor(v_respuesta,'id_tipo_estado_wf','','nada','valor')::integer;
        v_id_funcionario_wf = pxp.f_obtiene_clave_valor(v_respuesta,'id_funcionario_wf','','nada','valor')::integer;*/

        --raise exception 'id movimiento %', v_id_movimiento;
        v_respuesta =pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento almacenado correctamente');
        --v_respuesta =pxp.f_agrega_clave(v_respuesta,'id_movimientos',array_to_string(v_id_movs, ',', '*')::varchar);
        v_respuesta =pxp.f_agrega_clave(v_respuesta,'respuesta','ok');

        return v_respuesta;
	end;

    /*********************************
     #TRANSACCION:  'SAL_REV_TRANREST_MOD'
     #DESCRIPCION:  Inserta un movimiento con su detalle y lo finaliza
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        03-10-2012
    ***********************************/
	elsif(p_transaccion='SAL_REV_TRANREST_MOD') then
  	begin


		if (pxp.f_existe_parametro(p_tabla,'codigo_tran')) then
        	v_result = 'ok';
			for v_registros in (SELECT id_proceso_wf,
                                       id_estado_wf,
                                       id_funcionario,
                                       id_movimiento,
                                       estado_mov
                                FROM   alm.tmovimiento
                                WHERE  codigo_tran  = any (translate(v_parametros.codigo_tran, '[]', '{}')::varchar[]))loop

				if (v_registros.estado_mov = 'aprobado' or v_registros.estado_mov = 'prefin') then
                    SELECT te.id_tipo_estado into v_id_tipo_estado
                    FROM   wf.tproceso_wf pw
                           INNER JOIN wf.ttipo_proceso tp
                                   ON pw.id_tipo_proceso = tp.id_tipo_proceso
                           INNER JOIN wf.ttipo_estado te
                                   ON te.id_tipo_proceso = tp.id_tipo_proceso
                                      AND te.codigo = 'anulado'
                    WHERE  pw.id_proceso_wf = v_registros.id_proceso_wf;

                    if v_id_tipo_estado is null  then
                        raise exception 'No se parametrizo el estado "anulado" para reclamos';
                    end if;

                    -- pasamos el reclamo  al siguiente anulado
                    v_id_estado_actual =  wf.f_registra_estado_wf(
                        v_id_tipo_estado,
                        v_registros.id_funcionario,
                        v_registros.id_estado_wf,
                        v_registros.id_proceso_wf,
                        p_id_usuario,
                        null,
                        null,
                        null,
                        'Revercion de movimiento'|| COALESCE(v_parametros.codigo_tran,'--')
                    );

                    update 	alm.tmovimiento
                    set 	estado_mov = 'anulado', fecha_mod = now()
                    where 	id_movimiento  = v_registros.id_movimiento;

                    update 	alm.tmovimiento_det
                    set 	estado_dotacion = 'revertido'
                    where 	id_movimiento = v_registros.id_movimiento;
                    v_id_movs = v_id_movs || v_registros.id_movimiento;

               ELSE
               		--raise exception 'no se puede revertir un movimiento finalizado';
                    v_result = 'movimiento:, %' || v_registros.id_movimiento || ' ,se encuentra en estado finalizado';
               end if;
            end loop;

        else
        	raise 'sin codigo de transaccion';
        end if;
		--raise exception 'movvv %', v_id_movs::varchar;
        v_respuesta =pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento revertido correctamente');
       v_respuesta =pxp.f_agrega_clave(v_respuesta,'respuesta',v_result);

        return v_respuesta;
	end;

  /*********************************
     #TRANSACCION:  'SAL_MOV_MOD'
     #DESCRIPCION:  Modificacion de datos
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        03-10-2012
    ***********************************/

  elseif(p_transaccion='SAL_MOV_MOD')then
    begin

      select mov.estado_mov, mvt.tipo, mvt.codigo into v_estado_mov, v_tipo_movimiento, v_codigo_movimiento
      from alm.tmovimiento mov
      inner join alm.tmovimiento_tipo mvt on mvt.id_movimiento_tipo=mov.id_movimiento_tipo
      where mov.id_movimiento = v_parametros.id_movimiento;

      if (v_estado_mov = 'cancelado') then
          raise exception '%', 'El movimiento actual no puede ser modificado';
      end if;
	  --IF(p_id_usuario != 78 AND p_id_usuario != 589 AND p_id_usuario != 569 AND v_codigo_movimiento != 'SALTRNSF') THEN
        IF pxp.f_get_variable_global('alm_habilitar_fecha_tope') = 'si' THEN
          IF v_parametros.fecha_mov::date < pxp.f_get_variable_global('alm_fecha_tope_solicitudes')::date THEN
            IF v_tipo_movimiento = 'salida' AND v_codigo_movimiento != 'SALNORSERB' THEN
                raise exception 'No se permite hacer solicitudes de salidas de almacenes, debido a que se realiza cierre de gestion';
            END IF;
          END IF;
        END IF;
      --END IF;
	  -- modificamos fecha de salida cuando seleccionamos mas de un registro
      v_records = string_to_array(v_parametros.registros, ',');
      --raise exception 'v_records: %',v_parametros.registros;
        if(array_length(v_records,1)>1)then
           for v_ind in 1..array_length(v_records,1) loop
            update alm.tmovimiento set
              fecha_salida = v_parametros.fecha_salida,
              comail = v_parametros.comail
            where id_movimiento = v_records[v_ind];
           end loop;
        end if;

      update alm.tmovimiento set
          id_usuario_mod = p_id_usuario,
            fecha_mod = now(),
            id_movimiento_tipo = v_parametros.id_movimiento_tipo,
          	id_almacen = v_parametros.id_almacen,
            id_funcionario = v_parametros.id_funcionario,
            id_proveedor = v_parametros.id_proveedor,
            id_almacen_dest = v_parametros.id_almacen_dest,
            fecha_mov = date(v_parametros.fecha_mov) + interval '12 hours',
            descripcion = v_parametros.descripcion,
            observaciones = v_parametros.observaciones,
            id_movimiento_origen = v_parametros.id_movimiento_origen,
            id_depto_conta = v_parametros.id_depto_conta,
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai,
            comail = v_parametros.comail,
            fecha_salida = v_parametros.fecha_salida
        where id_movimiento = v_parametros.id_movimiento;

        v_respuesta=pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento modificado con exito');
        v_respuesta=pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_parametros.id_movimiento::varchar);
        return v_respuesta;

    end;
  	/*********************************
     #TRANSACCION:  'SAL_MOV_ELI'
     #DESCRIPCION:  Eliminacion de datos
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        03-10-2012
    ***********************************/
    elseif(p_transaccion='SAL_MOV_ELI')then
		begin
            select mov.estado_mov, mov.id_preingreso
            into v_estado_mov, v_id_preingreso
            from alm.tmovimiento mov
            where mov.id_movimiento = v_parametros.id_movimiento;

            if (v_estado_mov = 'cancelado' or v_estado_mov = 'finalizado') then
                raise exception '%', 'El movimiento actual no puede ser eliminado';
            end if;

            if (v_id_preingreso is not null) then
                raise exception '%', 'El movimiento no puede eliminarse porque viene de un Preingreso finalizado';
            end if;

            --delete from alm.tmovimiento
            update alm.tmovimiento set
            	estado_reg = 'inactivo'
            where id_movimiento=v_parametros.id_movimiento;

            v_respuesta=pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento eliminado');
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_parametros.id_movimiento::varchar);

            return v_respuesta;
        end;

    /*********************************
     #TRANSACCION:  'SAL_MOVFIN_MOD'
     #DESCRIPCION:  Finalizacion de un movimiento
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        20-02-2013
    ***********************************/
    /*
    MODIFICACIONES
    AUTOR: RCM
    FECHA: 10/10/2013
    DESCRIPCION: SE REESTRUCTURA LA FINALIZACIÓN Y CAMBIO DE ESTADOS PARA QUE UN SOLO PROCEDIMIENTO DIRECCIONE AL SIGUIENTE ESTADO
    */
	elseif(p_transaccion='SAL_MOVFIN_MOD')then

    	begin



        	--Llama a la función de registro del movimiento
	        v_respuesta = alm.f_movimiento_workflow_principal(p_id_usuario,hstore(v_parametros));

           	--Respuesta
            select cuenta into v_usuario
            from segu.tusuario
            where id_usuario=p_id_usuario;

            select movtp.codigo into v_tipo_movimiento
            from alm.tmovimiento mov
            inner join alm.tmovimiento_tipo movtp on movtp.id_movimiento_tipo=mov.id_movimiento_tipo
            where mov.id_movimiento=v_parametros.id_movimiento;

         	v_respuesta=pxp.f_agrega_clave(v_respuesta,'usuario',v_usuario);
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'tipo_movimiento',v_tipo_movimiento);
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_parametros.id_movimiento::varchar);

            --Generación de Alertas
            v_mostrar_alerts = alm.f_generar_alertas_mov(p_id_usuario, v_parametros.id_movimiento);

            if v_mostrar_alerts then
                v_respuesta=pxp.f_agrega_clave(v_respuesta,'alerts',v_mostrar_alerts::varchar);
            end if;

            --Devuelve respuesta
            return v_respuesta;

        end;

    /*********************************
     #TRANSACCION:  'SAL_MOVCNL_MOD'
     #DESCRIPCION:  Cancelacion de un movimiento
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        20-02-2013
    ***********************************/
	elseif (p_transaccion='SAL_MOVCNL_MOD') then
    	begin

            --Obtiene el Proceso WF
            SELECT
            mov.id_estado_wf, pw.id_tipo_proceso, pw.id_proceso_wf
           	into
            v_id_estado_wf, v_id_tipo_proceso, v_id_proceso_wf
           	FROM alm.tmovimiento mov
           	inner join wf.tproceso_wf pw on pw.id_proceso_wf = mov.id_proceso_wf
           	WHERE mov.id_movimiento = v_parametros.id_movimiento;

            --Obtiene el estado cancelado del WF
            select
            te.id_tipo_estado
            into
            v_id_tipo_estado
            from wf.tproceso_wf pw
            inner join wf.ttipo_proceso tp on pw.id_tipo_proceso = tp.id_tipo_proceso
            inner join wf.ttipo_estado te on te.id_tipo_proceso = tp.id_tipo_proceso and te.codigo = 'cancelado'
            where pw.id_proceso_wf = v_id_proceso_wf;

            --Se cancela el WF
            v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                           NULL,
                                                           v_id_estado_wf,
                                                           v_id_proceso_wf,
                                                           p_id_usuario,
                                                           v_parametros._id_usuario_ai,
            											   v_parametros._nombre_usuario_ai,
                                                           null);

            --Cancela el movimiento
            update alm.tmovimiento set
            id_estado_wf =  v_id_estado_actual,
          	estado_mov = 'cancelado',
            id_usuario_mod=p_id_usuario,
            fecha_mod=now(),
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai
        	where id_movimiento = v_parametros.id_movimiento;

            v_respuesta=pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento cancelado');
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_parametros.id_movimiento::varchar);

            return v_respuesta;
    	end;

    /*********************************
     #TRANSACCION:  'SAL_MOVREV_MOD'
     #DESCRIPCION:  Reversion de un movimiento
     #AUTOR:        Ariel Ayaviri Omonte
     #FECHA:        26-02-2013
    ***********************************/
    elseif (p_transaccion='SAL_MOVREV_MOD') then
    begin
      	--Revisar que sea el último movimiento finalizado.
        select mov.id_movimiento
        into v_id_movimiento
        from alm.tmovimiento mov
        where mov.estado_mov = 'finalizado'
        and mov.estado_reg = 'activo'
        order by mov.fecha_mov desc limit 1;

        if (v_id_movimiento != v_parametros.id_movimiento) then
          --raise exception '%', 'No se puede revertir el movimiento seleccionado. Para revertir un movimiento no deben existir movimiento finalizados despues de este.';
        end if;

        --se obtienen los datos del movimiento a revertir
        select mov.fecha_mov, movtip.tipo, movtip.nombre, mov.id_almacen_dest
        into v_fecha_mov, v_tipo_mov, v_tipo_mov_personalizado, v_id_almacen_dest
        from alm.tmovimiento mov
        inner join alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo
        where mov.id_movimiento = v_parametros.id_movimiento;

        -- solo para salidas.
        if (v_tipo_mov = 'salida') then
        -- BUCLE de los detalle del movimiento
          FOR g_registros in (
                select
                    movdet.id_item,
                    item.nombre as nombre_item,
                    movdet.id_movimiento_det,
                    movdet.cantidad as cantidad_item
                from alm.tmovimiento_det movdet
                inner join alm.titem item on item.id_item = movdet.id_item
                where movdet.estado_reg = 'activo'
                    and movdet.id_movimiento = v_parametros.id_movimiento
            ) LOOP
              --se obtiene el tipo de valoracion
                --obtener el codigo del metodo de valoracion
                select metval.codigo into v_codigo_valoracion
                from alm.talmacen_stock alstock
                inner join alm.tmetodo_val metval on metval.id_metodo_val = alstock.id_metodo_val
                where alstock.id_almacen = v_parametros.id_almacen
                  and alstock.id_item = g_registros.id_item
                    and alstock.estado_reg = 'activo';

                --se va recorriendo todos los detalles valorados del detalle_movimiento
              FOR g_registros_2 in (
                  select detval.cantidad, detval.id_mov_det_val_origen
                    from alm.tmovimiento_det_valorado detval
                    where detval.id_movimiento_det = g_registros.id_movimiento_det
                    order by detval.id_movimiento_det_valorado desc
                ) LOOP
                  IF (g_registros_2.id_mov_det_val_origen is not null) THEN
                        -- sumar la cantidad del ultimo detalle valorado al aux_saldo encontrado
                        update alm.tmovimiento_det_valorado set
                            id_usuario_mod = p_id_usuario,
                            fecha_mod = now(),
                            aux_saldo_fisico = aux_saldo_fisico + g_registros_2.cantidad
                        where id_movimiento_det_valorado = g_registros_2.id_mov_det_val_origen;
                    END IF;
                END LOOP;

                -- eliminar todos los det_valorados del detalle_movimiento
                delete from alm.tmovimiento_det_valorado detval
                where detval.id_movimiento_det = g_registros.id_movimiento_det;

                --Insertar un nuevo detalle_valorado con la cantidad del detalle_movimiento
                insert into alm.tmovimiento_det_valorado (
                    id_usuario_reg,
                    fecha_reg,
                    estado_reg,
                    id_movimiento_det,
                    cantidad
                ) VALUES (
                    p_id_usuario,
                    now(),
                    'activo',
                    g_registros.id_movimiento_det,
                    g_registros.cantidad_item
                );
            END LOOP;
        end if;

        --Obtiene datos del WF
        SELECT
        mov.id_estado_wf, pw.id_tipo_proceso, pw.id_proceso_wf
        into
        v_id_estado_wf, v_id_tipo_proceso, v_id_proceso_wf
        FROM alm.tmovimiento mov
        inner join wf.tproceso_wf pw on pw.id_proceso_wf = mov.id_proceso_wf
        WHERE mov.id_movimiento = v_parametros.id_movimiento;

        --Recuperamos el estado inicial segun tipo_proceso
        SELECT
        ps_id_tipo_estado, ps_codigo_estado
        into
        v_id_tipo_estado,v_codigo_estado
        FROM wf.f_obtener_tipo_estado_inicial_del_tipo_proceso(v_id_tipo_proceso);

        --Recupera el funcionario según log
        SELECT
        ps_id_funcionario, ps_codigo_estado, ps_id_depto
        into
        v_id_funcionario, v_codigo_estado, v_id_depto
        FROM wf.f_obtener_estado_segun_log_wf(v_id_estado_wf, v_id_tipo_estado);

         --Registra estado borrador
         v_id_estado_actual = wf.f_registra_estado_wf(
                v_id_tipo_estado,
                v_id_funcionario,
                v_id_estado_wf,
                v_id_proceso_wf,
                p_id_usuario,
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                v_id_depto,
                v_parametros.obs);

         --Actualiza estado en el movimiento
         update alm.tmovimiento  m set
         id_estado_wf = v_id_estado_actual,
         estado_mov = v_codigo_estado,
         id_usuario_mod = p_id_usuario,
         fecha_mod = now(),
         id_usuario_ai =  v_parametros._id_usuario_ai,
         usuario_ai = v_parametros._nombre_usuario_ai
         where id_movimiento = v_parametros.id_movimiento;

         v_respuesta=pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento revertido');
         v_respuesta=pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_parametros.id_movimiento::varchar);
         return v_respuesta;
    end;

  /*********************************
 	#TRANSACCION:  'ALM_ANTEMOV_IME'
 	#DESCRIPCION:	Transaccion utilizada  pasar a  estados anterior del movimiento
                    segun la operacion definida
 	#AUTOR:		GSS
 	#FECHA:		12-07-2013 12:12:51
	***********************************/

	elseif(p_transaccion='SAL_ANTEMOV_IME')then
        begin

		---------------------
        --Verificamos el estado del movimiento
        ---------------------
		select estado_mov,codigo_tran into v_estado_mov, v_codigo_tran
        from alm.tmovimiento
        where id_movimiento = v_parametros.id_movimiento;
        --------------------------------------------------
        --REtrocede al estado inmediatamente anterior
        -------------------------------------------------
         IF  v_parametros.operacion = 'cambiar' THEN
			if (v_estado_mov = 'aprobado' and v_codigo_tran is not null) then
            	v_respuesta='no se puede volver al estado borrador una salida de trabajo aprobada';
            else
               raise notice 'es_estado_wf %',v_parametros.id_estado_wf;

                      --recupera estado anterior segun Log del WF
                        SELECT
                           ps_id_tipo_estado,
                           ps_id_funcionario,
                           ps_id_usuario_reg,
                           ps_id_depto,
                           ps_codigo_estado,
                           ps_id_estado_wf_ant
                        into
                           v_id_tipo_estado,
                           v_id_funcionario,
                           v_id_usuario_reg,
                           v_id_depto,
                           v_codigo_estado,
                           v_id_estado_wf_ant
                        FROM wf.f_obtener_estado_ant_log_wf(v_parametros.id_estado_wf);

                        --
                      select
                           ew.id_proceso_wf
                        into
                           v_id_proceso_wf
                      from wf.testado_wf ew
                      where ew.id_estado_wf= v_id_estado_wf_ant;

                      -- registra nuevo estado

                      v_id_estado_actual = wf.f_registra_estado_wf(
                          v_id_tipo_estado,
                          v_id_funcionario,
                          v_parametros.id_estado_wf,
                          v_id_proceso_wf,
                          p_id_usuario,
                          v_parametros._id_usuario_ai,
                          v_parametros._nombre_usuario_ai,
                          v_id_depto,
                          v_parametros.obs);

                      -- actualiza estado del movimiento
                        update alm.tmovimiento  m set
                           id_estado_wf =  v_id_estado_actual,
                           estado_mov = v_codigo_estado,
                           id_usuario_mod=p_id_usuario,
                           fecha_mod=now(),
                           id_suario_ai = v_parametros._id_usuario_ai,
                           usuario_ai = v_parametros._nombre_usuario_ai
                         where id_movimiento = v_parametros.id_movimiento;

                        -- si hay mas de un estado disponible  preguntamos al usuario
                        v_respuesta = pxp.f_agrega_clave(v_respuesta,'mensaje','Se realizo el cambio de estado)');
                        v_respuesta = pxp.f_agrega_clave(v_respuesta,'operacion','cambio_exitoso');


                      --Devuelve la respuesta
                        return v_respuesta;
				end if;
           ----------------------------------------------------------------------
           -- PAra retornar al estado borrador de la solicitud de manera directa
           ---------------------------------------------------------------------
           ELSEIF  v_parametros.operacion = 'inicio' THEN
			if ((v_estado_mov = 'aprobado' or v_estado_mov = 'prefin') and v_codigo_tran is not null) then
            	v_respuesta='no se puede volver al estado borrador una salida de trabajo aprobada o en prefin';
            else
               SELECT
                mov.id_estado_wf,
                pw.id_tipo_proceso,
                pw.id_proceso_wf
               into
                v_id_estado_wf,
                v_id_tipo_proceso,
                v_id_proceso_wf

               FROM alm.tmovimiento mov
               inner join wf.tproceso_wf pw on pw.id_proceso_wf = mov.id_proceso_wf
               WHERE  mov.id_movimiento = v_parametros.id_movimiento;

                 raise notice 'BUSCAMOS EL INICIO PARA %',v_id_tipo_proceso;

                -- recuperamos el estado inicial segun tipo_proceso

                 SELECT
                   ps_id_tipo_estado,
                   ps_codigo_estado
                 into
                   v_id_tipo_estado,
                   v_codigo_estado
                 FROM wf.f_obtener_tipo_estado_inicial_del_tipo_proceso(v_id_tipo_proceso);

                 --recupera el funcionario segun ultimo log borrador
                 raise notice 'CODIGO ESTADO BUSCADO %',v_codigo_estado ;

                 SELECT
                   ps_id_funcionario,
                   ps_codigo_estado ,
                   ps_id_depto
                 into
                  v_id_funcionario,
                  v_codigo_estado,
                  v_id_depto

                 FROM wf.f_obtener_estado_segun_log_wf(v_id_estado_wf, v_id_tipo_estado);

                  raise notice 'CODIGO ESTADO ENCONTRADO %',v_codigo_estado ;

                 --registra estado borrador
                  v_id_estado_actual = wf.f_registra_estado_wf(
                      v_id_tipo_estado,
                      v_id_funcionario,
                      v_parametros.id_estado_wf,
                      v_id_proceso_wf,
                      p_id_usuario,
                      v_parametros._id_usuario_ai,
                      v_parametros._nombre_usuario_ai,
                      v_id_depto,
                      v_parametros.obs);

                  -- actualiza estado en el movimiento
                    update alm.tmovimiento  m set
                       id_estado_wf =  v_id_estado_actual,
                       estado_mov = v_codigo_estado,
                       id_usuario_mod=p_id_usuario,
                       fecha_mod=now(),
                       id_usuario_ai = v_parametros._id_usuario_ai,
                       usuario_ai = v_parametros._nombre_usuario_ai
                     where id_movimiento = v_parametros.id_movimiento;

                   -- si hay mas de un estado disponible  preguntamos al usuario
                    v_respuesta = pxp.f_agrega_clave(v_respuesta,'mensaje','Se regresoa borrador con exito)');
                    v_respuesta = pxp.f_agrega_clave(v_respuesta,'operacion','cambio_exitoso');

                  --Devuelve la respuesta

                  return v_respuesta;
			end if;
         ELSE

           		raise exception 'Operacion no reconocida %',v_parametros.operacion;

           END IF;
       end;

    /*********************************
 	#TRANSACCION:  'SAL_SIGEMOV_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado del movimiento, integrado con el WF
 	#AUTOR:		GSS
 	#FECHA:		12-07-2013 12:12:51
	***********************************/

	elseif(p_transaccion='SAL_SIGEMOV_IME')then
        begin
        --obtenermos datos basicos
		--RAISE EXCEPTION 'ESTIMADO USUARIO NO PUEDE PASAR A SIGUIENTE ESTADO FAVOR COMUNCICARSE CON FRANKLIN ESPINOZA (71721380)';
          select
            m.id_proceso_wf,
            m.id_estado_wf,
            m.estado_mov,
            m.fecha_mov
          into
            v_id_proceso_wf,
            v_id_estado_wf,
            v_codigo_estado,
            v_fecha_mov

          from alm.tmovimiento m
          where m.id_movimiento=v_parametros.id_movimiento;

           select
            ew.id_tipo_estado ,
            te.pedir_obs
           into
            v_id_tipo_estado,
            v_pedir_obs
          from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
          where ew.id_estado_wf = v_id_estado_wf;

         ---------------------------------------------
         -- Verifica  los posibles estados sigueintes para que desde la interfza se tome la decision si es necesario
         --------------------------------------------------
          IF  v_parametros.operacion = 'verificar' THEN

              --buscamos siguiente estado correpondiente al proceso del WF

              ----- variables de retorno------

              v_num_estados=0;
              v_num_funcionarios=0;
              v_num_deptos=0;

              ---------------------------------

             SELECT
                 ps_id_tipo_estado,
                 ps_codigo_estado,
                 ps_disparador,
                 ps_regla,
                 ps_prioridad
              into
                va_id_tipo_estado,
                va_codigo_estado,
                va_disparador,
                va_regla,
                va_prioridad
              FROM adq.f_obtener_sig_estado_sol_rec(v_parametros.id_movimiento, v_id_proceso_wf, v_id_tipo_estado);


            v_num_estados= array_length(va_id_tipo_estado, 1);

             IF v_pedir_obs = 'no' THEN

                IF v_num_estados = 1 then
                      -- si solo hay un estado,  verificamos si tiene mas de un funcionario por este estado

                     SELECT
                     *
                      into
                     v_num_funcionarios
                     FROM wf.f_funcionario_wf_sel(
                         p_id_usuario,
                         va_id_tipo_estado[1],
                         v_fecha_mov::date,
                         v_id_estado_wf,
                         TRUE) AS (total bigint);
                                         raise exception 'fass b finish %',v_num_estados;
                    IF v_num_funcionarios = 1 THEN
                    -- si solo es un funcionario, recuperamos el funcionario correspondiente
                         SELECT
                             id_funcionario
                               into
                             v_id_funcionario_estado
                         FROM wf.f_funcionario_wf_sel(
                             p_id_usuario,
                             va_id_tipo_estado[1],
                             v_fecha_mov::date,
                             v_id_estado_wf,
                             FALSE)
                             AS (id_funcionario integer,
                               desc_funcionario text,
                               desc_funcionario_cargo text,
                               prioridad integer);
                    END IF;

                  --verificamos el numero de deptos

                    SELECT
                    *
                    into
                      v_num_deptos
                   FROM wf.f_depto_wf_sel(
                       p_id_usuario,
                       va_id_tipo_estado[1],
                       v_fecha_mov::date,
                       v_id_estado_wf,
                       TRUE) AS (total bigint);

                  IF v_num_deptos = 1 THEN
                      -- si solo es un funcionario, recuperamos el funcionario correspondiente
                           SELECT
                               id_depto
                                 into
                               v_id_depto_estado
                          FROM wf.f_depto_wf_sel(
                               p_id_usuario,
                               va_id_tipo_estado[1],
                               v_fecha_soli,
                               v_id_estado_wf,
                               FALSE)
                               AS (id_depto integer,
                                 codigo_depto varchar,
                                 nombre_corto_depto varchar,
                                 nombre_depto varchar,
                                 prioridad integer);
                    END IF;

                 END IF;

           END IF;

            -- si hay mas de un estado disponible  preguntamos al usuario
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'mensaje','Verificacion para el siguiente estado)');
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'estados', array_to_string(va_id_tipo_estado, ','));
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'operacion','preguntar_todo');
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'num_estados',v_num_estados::varchar);
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'num_funcionarios',v_num_funcionarios::varchar);
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'num_deptos',v_num_deptos::varchar);
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'id_funcionario_estado',v_id_funcionario_estado::varchar);
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'id_depto_estado',v_id_depto_estado::varchar);
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'id_tipo_estado', va_id_tipo_estado[1]::varchar);


           ----------------------------------------
           --Se se solicita cambiar de estado a la solicitud
           ------------------------------------------
           ELSEIF  v_parametros.operacion = 'cambiar' THEN

            -- obtener datos tipo estado

            select
             te.codigo
            into
             v_codigo_estado_siguiente
            from wf.ttipo_estado te
            where te.id_tipo_estado = v_parametros.id_tipo_estado;

            IF  pxp.f_existe_parametro('p_tabla','id_depto') THEN

             v_id_depto = v_parametros.id_depto;

            END IF;

            v_obs=v_parametros.obs;

             v_id_estado_actual =  wf.f_registra_estado_wf(v_parametros.id_tipo_estado,
                                                           v_parametros.id_funcionario,
                                                           v_id_estado_wf,
                                                           v_id_proceso_wf,
                                                           p_id_usuario,
                                                           v_parametros._id_usuario_ai,
                                                           v_parametros._nombre_usuario_ai,
                                                           v_id_depto,
                                                           v_obs);


             -- actualiza estado en el movimiento

             update alm.tmovimiento  s set
               id_estado_wf =  v_id_estado_actual,
               estado_mov = v_codigo_estado_siguiente,
               id_usuario_mod=p_id_usuario,
               fecha_mod=now(),
               id_usuario_ai = v_parametros._id_usuario_ai,
               usuario_ai = v_parametros._nombre_usuario_ai

             where id_movimiento= v_parametros.id_movimiento;

           -- si hay mas de un estado disponible  preguntamos al usuario
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'mensaje','Se realizo el cambio de estado)');
            v_respuesta = pxp.f_agrega_clave(v_respuesta,'operacion','cambio_exitoso');

          END IF;

          --Devuelve la respuesta
            return v_respuesta;

        end;

  	/*********************************
 	#TRANSACCION:  'SAL_MOVPRE_REV'
 	#DESCRIPCION:	REvierte un ingreso hasta el preingreso. Cancela el movimiento y retrocede el estado del preingreso
 	#AUTOR:			RCM
 	#FECHA:			21/10/2013
	***********************************/

	elseif(p_transaccion='SAL_MOVPRE_REV')then
        begin

        	--------------------------------------------------------------------
        	--(1)Verifica que el ingreso este en borrador y que tenga preingreso
            --------------------------------------------------------------------
            if not exists(select 1 from alm.tmovimiento
            			where id_movimiento = v_parametros.id_movimiento
                        and id_preingreso is not null
                        and estado_mov = 'borrador') then
            	raise exception 'El ingreso debe estar en Borrador y debe tener un Preingreso asociado';
            end if;

        	------------------------
            --(2)Cancela el Ingreso
            ------------------------
        	--Obtiene el Proceso WF
            SELECT
            mov.id_estado_wf, pw.id_tipo_proceso, pw.id_proceso_wf
           	into
            v_id_estado_wf, v_id_tipo_proceso, v_id_proceso_wf
           	FROM alm.tmovimiento mov
           	inner join wf.tproceso_wf pw on pw.id_proceso_wf = mov.id_proceso_wf
           	WHERE mov.id_movimiento = v_parametros.id_movimiento;

            --Obtiene el estado cancelado del WF
            select
            te.id_tipo_estado
            into
            v_id_tipo_estado
            from wf.tproceso_wf pw
            inner join wf.ttipo_proceso tp on pw.id_tipo_proceso = tp.id_tipo_proceso
            inner join wf.ttipo_estado te on te.id_tipo_proceso = tp.id_tipo_proceso and te.codigo = 'cancelado'
            where pw.id_proceso_wf = v_id_proceso_wf;

            --Se cancela el WF
            v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                           NULL,
                                                           v_id_estado_wf,
                                                           v_id_proceso_wf,
                                                           p_id_usuario,
                                                           v_parametros._id_usuario_ai,
                                                           v_parametros._nombre_usuario_ai,
                                                           null);

            --Cancela el movimiento
            update alm.tmovimiento set
            id_estado_wf =  v_id_estado_actual,
          	estado_mov = 'cancelado',
            id_usuario_mod=p_id_usuario,
            fecha_mod=now(),
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai
        	where id_movimiento = v_parametros.id_movimiento;

            ---------------------------
            --(3)Cancela el PreIngreso
            ---------------------------
            --Obtiene el preingreso
            select mov.id_preingreso, alm.id_departamento
            into v_id_preingreso, v_id_depto
            from alm.tmovimiento mov
            inner join alm.talmacen alm on alm.id_almacen = mov.id_almacen
            where mov.id_movimiento = v_parametros.id_movimiento;

            --Obtiene el Proceso WF
            SELECT
            pre.id_estado_wf, pw.id_tipo_proceso, pw.id_proceso_wf
           	into
            v_id_estado_wf, v_id_tipo_proceso, v_id_proceso_wf
           	FROM alm.tpreingreso pre
           	inner join wf.tproceso_wf pw on pw.id_proceso_wf = pre.id_proceso_wf
           	WHERE pre.id_preingreso = v_id_preingreso;

            --Recupera estado anterior segun Log del WF
            SELECT
            ps_id_tipo_estado,ps_id_funcionario,ps_id_usuario_reg,
            ps_id_depto,ps_codigo_estado,ps_id_estado_wf_ant
            into
            v_id_tipo_estado,v_id_funcionario,v_id_usuario_reg,
			v_id_depto,v_codigo_estado,v_id_estado_wf_ant
			FROM wf.f_obtener_estado_ant_log_wf(v_id_estado_wf);

            --Encuentra el proceso
            select ew.id_proceso_wf
            into v_id_proceso_wf
            from wf.testado_wf ew
            where ew.id_estado_wf= v_id_estado_wf_ant;

            --Registra nuevo estado
            v_id_estado_actual = wf.f_registra_estado_wf(
                          v_id_tipo_estado,
                          v_id_funcionario,
                          v_id_estado_wf,
                          v_id_proceso_wf,
                          p_id_usuario,
                          v_parametros._id_usuario_ai,
                          v_parametros._nombre_usuario_ai,
                          v_id_depto,
                          'Reversión del ingreso generado');

			--Actualiza estado del movimiento
            update alm.tpreingreso  set
            id_estado_wf = v_id_estado_actual,
            estado = v_codigo_estado,
            id_usuario_mod = p_id_usuario,
            fecha_mod = now(),
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai
            where id_preingreso = v_id_preingreso;

			--Respuesta
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento revertido a Preingreso');
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'id_movimiento',v_parametros.id_movimiento::varchar);
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'id_preingreso',v_id_preingreso::varchar);

            --Devuelve la respuesta
            return v_respuesta;

        end;

		/*********************************
  		#TRANSACCION:  'SAL_MOVAREST_MOD'
        #DESCRIPCION:	Modificacion de registros
        #AUTOR:		franklin.espinoza
        #FECHA:		10-09-2019 19:52:53
        ***********************************/

	elsif(p_transaccion='SAL_MOVAREST_MOD')then

		begin

      for v_registros in SELECT mm.id_movimiento,
                                       mm.id_almacen, mm.id_estado_wf, mm.id_proceso_wf, mm.estado_mov
                                FROM   alm.tmovimiento mm
                                WHERE  mm.codigo_tran = any(string_to_array(v_parametros.codigo_tran,',')) loop

        if  v_registros.estado_mov = 'aprobado' then
          --Siguiente estado correspondiente al proceso del WF
          SELECT ps_id_tipo_estado, ps_codigo_estado, ps_disparador, ps_regla, ps_prioridad
          into va_id_tipo_estado, va_codigo_estado, va_disparador, va_regla, va_prioridad

          FROM wf.f_obtener_estado_wf(v_registros.id_proceso_wf, v_registros.id_estado_wf,NULL,'siguiente');

          v_id_estado_actual =  wf.f_registra_estado_wf(va_id_tipo_estado[1]::integer,
                                                        null,
                                                        v_registros.id_estado_wf,
                                                        v_registros.id_proceso_wf,
                                                        p_id_usuario,
                                                        null,
                                                        null,
                                                        null);

          --Actualiza estado de WF
          update alm.tmovimiento set
          id_estado_wf = v_id_estado_actual,
          estado_mov = va_codigo_estado[1],
          fecha_mod = now(),
          id_usuario_mod = p_id_usuario
          where id_movimiento = v_registros.id_movimiento::integer;
        end if;
      end loop;

      v_respuesta =pxp.f_agrega_clave(v_respuesta,'mensaje','Movimiento almacenado correctamente');
      v_respuesta =pxp.f_agrega_clave(v_respuesta,'respuesta','ok');

      --Devuelve la respuesta
      return v_respuesta;
		end;
	/*********************************
     #TRANSACCION:  'SAL_MOV_ANULAR_GROUP'
     #DESCRIPCION:  Anular movimientos en estado borrador
     #AUTOR:        franklin.espinoza
     #FECHA:        03-01-2020
    ***********************************/
	elseif (p_transaccion='SAL_MOV_ANULAR_GROUP') then
    	begin

            select tg.gestion
            into v_gestion
			      from param.tgestion tg
            where tg.id_gestion = v_parametros.id_gestion;

            for v_movimientos in select mov.id_movimiento, mov.id_proceso_wf, mov.id_usuario_ai, mov.usuario_ai
             					           from alm.tmovimiento mov
                                 inner join alm.tmovimiento_tipo mtip on mtip.id_movimiento_tipo = mov.id_movimiento_tipo
                                 where mov.id_almacen = v_parametros.id_almacen
                                 and mov.estado_mov not in ('finalizado','cancelado', 'anulado')
                                 and mtip.tipo = 'salida'
                                 and date_part('year',mov.fecha_mov) = v_gestion loop

              --Obtiene el Proceso WF
              SELECT
              mov.id_estado_wf, pw.id_tipo_proceso, pw.id_proceso_wf
              into
              v_id_estado_wf, v_id_tipo_proceso, v_id_proceso_wf
              FROM alm.tmovimiento mov
              inner join wf.tproceso_wf pw on pw.id_proceso_wf = mov.id_proceso_wf
              WHERE mov.id_movimiento = v_movimientos.id_movimiento;

              --Obtiene el estado anulado del WF
              select
              te.id_tipo_estado
              into
              v_id_tipo_estado
              from wf.tproceso_wf pw
              inner join wf.ttipo_proceso tp on pw.id_tipo_proceso = tp.id_tipo_proceso
              inner join wf.ttipo_estado te on te.id_tipo_proceso = tp.id_tipo_proceso and te.codigo = 'anulado'
              where pw.id_proceso_wf = v_movimientos.id_proceso_wf;

              --Se anula el WF
              v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                             NULL,
                                                             v_id_estado_wf,
                                                             v_id_proceso_wf,
                                                             p_id_usuario,
                                                             v_movimientos.id_usuario_ai,
                                                             v_movimientos.usuario_ai,
                                                             null);

              --Anula el movimiento
              update alm.tmovimiento set
              id_estado_wf =  v_id_estado_actual,
              estado_mov = 'anulado',
              id_usuario_mod=p_id_usuario,
              fecha_mod=now(),
              id_usuario_ai = v_movimientos.id_usuario_ai,
              usuario_ai = v_movimientos.usuario_ai
              where id_movimiento = v_movimientos.id_movimiento;
            end loop;
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'mensaje','Movimientos Anulados');
            v_respuesta=pxp.f_agrega_clave(v_respuesta,'id_movimiento','1'::varchar);
            return v_respuesta;
    	end;
  else
     raise exception 'Transaccion inexistente: %',p_transaccion;
  end if;
EXCEPTION
  WHEN OTHERS THEN
        v_respuesta='';
        v_respuesta = pxp.f_agrega_clave(v_respuesta,'mensaje',SQLERRM);
        v_respuesta = pxp.f_agrega_clave(v_respuesta,'codigo_error',SQLSTATE);
        v_respuesta = pxp.f_agrega_clave(v_respuesta,'procedimientos',v_nombre_funcion);
        raise exception '%',v_respuesta;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;