CREATE OR REPLACE FUNCTION alm.f_finalizar_movimientos_intermedios (
)
RETURNS boolean AS
$body$
DECLARE
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_id_almacen 			integer;
    v_record				record;
    v_id_tipo_estado		integer;
    v_id_estado_actual		integer;
    v_gestion				integer;
    v_tramite				varchar;

    v_id_estado_wf			integer;
BEGIN
	v_nombre_funcion = 'alm.f_finalizar_movimientos_intermedios';

	for v_record in select mov.id_movimiento, mov.id_proceso_macro, mov.id_proceso_wf, mov.id_estado_wf, mov.fecha_mov
                    from alm.tmovimiento mov
                    INNER JOIN alm.tmovimiento_tipo movtip on movtip.id_movimiento_tipo = mov.id_movimiento_tipo
                    WHERE  lower(mov.estado_mov)in ('anulado', 'prefin', 'autorizacion', 'vbarea') and
                    movtip.tipo = 'salida' and (mov.fecha_mov between '1/1/2018'::date and '31/12/2018'::date ) loop



        --if(v_record.id_movimiento != 23553) then

          v_gestion = date_part('year', v_record.fecha_mov);

          select te.id_tipo_estado, tp.nro_tramite
          into  v_id_tipo_estado, v_tramite
          from wf.tproceso_wf tp
          inner join wf.ttipo_estado te on te.id_tipo_proceso = tp.id_tipo_proceso
          where tp.id_proceso_wf = v_record.id_proceso_wf and te.codigo = 'anulado';

          select tm.id_estado_wf
          into v_id_estado_wf
          from public.tmov_regularizacion tm
          where tm.id_movimiento = v_record.id_movimiento and tm.id_proceso_wf = v_record.id_proceso_wf;


          v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                             63,
                                                             v_id_estado_wf,
                                                             v_record.id_proceso_wf,
                                                             78,
                                                             null,
                                                             null,
                                                             null,
                                                             'Proceso anulado por cierre de Gestión '||v_gestion);

          raise notice 'anulado : %, %', v_record.id_movimiento, v_tramite;
          update alm.tmovimiento  set
              id_estado_wf =  v_id_estado_actual,
              estado_mov = 'anulado',
              id_usuario_mod=78,
              fecha_mod='31/12/2018'::timestamp,
              id_usuario_ai = null,
              usuario_ai = null,
              estado_reg = 'inactivo'
          where id_movimiento= v_record.id_movimiento;
        --end if;

    end loop;

    return true;
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