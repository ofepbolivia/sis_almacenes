--------------- SQL ---------------

CREATE OR REPLACE FUNCTION alm.f_insercion_movimiento (
  p_id_usuario integer,
  p_parametros public.hstore
)
RETURNS integer AS
$body$
/*
Autor: RCM
Fecha: 09/10/2013
Descripción: Función para insertar un movimiento (Se la independiza para poder llamarla desde otras funciones)
*/
DECLARE

	v_codigo_tipo_proceso varchar;
    v_id_proceso_macro integer;
    v_num_tramite varchar;
    v_id_proceso_wf integer;
    v_id_estado_wf integer;
    v_codigo_estado varchar;
    v_id_movimiento integer;
    v_tipo_movimiento	varchar;
    v_codigo_movimiento	varchar;

BEGIN

	--Obtener el codigo del tipo_proceso
    select tp.codigo, pm.id_proceso_macro, mt.tipo, mt.codigo
    into v_codigo_tipo_proceso, v_id_proceso_macro, v_tipo_movimiento, v_codigo_movimiento
    from  alm.tmovimiento_tipo mt
    inner join wf.tproceso_macro pm
    on pm.id_proceso_macro =  mt.id_proceso_macro
    inner join wf.ttipo_proceso tp
    on tp.id_proceso_macro = pm.id_proceso_macro
    where mt.id_movimiento_tipo = (p_parametros->'id_movimiento_tipo')::integer
    and tp.estado_reg = 'activo'
    and tp.inicio = 'si';

    IF pxp.f_get_variable_global('alm_habilitar_fecha_tope') = 'si' THEN
      IF (p_parametros->'fecha_mov')::date > pxp.f_get_variable_global('alm_fecha_tope_solicitudes')::date THEN
          IF v_tipo_movimiento = 'salida' AND v_codigo_movimiento != 'SALNORSERB' THEN
              raise exception 'No se permite hacer solicitudes de salidas de almacenes, debido a que se realiza cierre de gestion';
          END IF;
      END IF;
    END IF;

    if v_codigo_tipo_proceso is null then
       raise exception 'No existe un proceso inicial para el proceso macro indicado (Revise la configuración)';
    end if;
        
    --Iniciar el trámite en el sistema de WF
	select 
    ps_num_tramite, ps_id_proceso_wf, ps_id_estado_wf, ps_codigo_estado 
    into
    v_num_tramite, v_id_proceso_wf, v_id_estado_wf, v_codigo_estado   
    from wf.f_inicia_tramite(
         p_id_usuario, 
         (p_parametros->'_id_usuario_ai')::integer,
         (p_parametros->'_nombre_usuario_ai')::varchar,
         (p_parametros->'id_gestion')::integer, 
         v_codigo_tipo_proceso, 
         (p_parametros->'id_funcionario')::integer,
         NULL,
         'Generación del movimiento',
         'S/N');
     
	--Se hace el registro del movimiento   
    insert into alm.tmovimiento (
          id_usuario_reg,
          fecha_reg, 
          estado_reg,
          id_movimiento_tipo, 
          id_almacen,
          id_funcionario, 
          id_proveedor,
          id_almacen_dest, 
          fecha_mov,
          descripcion,
          observaciones,
          estado_mov,
          id_movimiento_origen,
          id_proceso_macro,
          id_proceso_wf,
          id_estado_wf,
          id_depto_conta,
          id_preingreso,
          id_usuario_ai,
          usuario_ai
        ) values (
          p_id_usuario,
          now(),
          'activo',
          (p_parametros->'id_movimiento_tipo')::integer,
          (p_parametros->'id_almacen')::integer,
          (p_parametros->'id_funcionario')::integer, 
          (p_parametros->'id_proveedor')::integer,
          (p_parametros->'id_almacen_dest')::integer,
          (p_parametros->'fecha_mov')::date + interval '12 hours',
          (p_parametros->'descripcion')::varchar,
          (p_parametros->'observaciones')::varchar,
          v_codigo_estado,
          (p_parametros->'id_movimiento_origen')::integer,
          v_id_proceso_macro,
          v_id_proceso_wf,
          v_id_estado_wf,
          (p_parametros->'id_depto_conta')::integer,
          (p_parametros->'id_preingreso')::integer,
          (p_parametros->'_id_usuario_ai')::integer,
          (p_parametros->'_nombre_usuario_ai')::varchar
        ) RETURNING id_movimiento into v_id_movimiento;

	--Respuesta
    return v_id_movimiento;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;