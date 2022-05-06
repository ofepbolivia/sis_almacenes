CREATE OR REPLACE FUNCTION alm.f_regularizar (
)
RETURNS void AS
$body$
DECLARE

	v_id_funcionario	integer;
    v_record			record;
    v_id_usuario		integer;
    v_acceso_directo	varchar;
    v_clase 			varchar;
    v_parametros_ad 	varchar;
    v_tipo_noti			varchar;
    v_titulo			varchar;
    v_id_estado_actual	integer;

    v_fecha				date;

    v_cont integer = 1;
    v_cantidad integer;
    v_diferencia  integer;
    v_diferencia_total integer;
    v_saldo_real integer;


BEGIN
	 --Procesamos todos los reclamos
     for  v_record in
     select tm.id_movimiento, tm.codigo, tmd.id_movimiento_det, tmdv.id_movimiento_det_valorado, tmdv.cantidad, tmdv.aux_saldo_fisico, ti.codigo, tmt.tipo

	 from alm.tmovimiento tm
	 inner join alm.tmovimiento_det tmd on tmd.id_movimiento = tm.id_movimiento
     inner join alm.titem ti on ti.id_item = tmd.id_item
	 inner join alm.tmovimiento_det_valorado tmdv on tmdv.id_movimiento_det = tmd.id_movimiento_det
	 inner join alm.tmovimiento_tipo tmt on tmt.id_movimiento_tipo = tm.id_movimiento_tipo
	 where tmt.tipo = 'ingreso' and tm.id_almacen = 1 loop

     select sum(tmv.cantidad)
     into v_cantidad
     from alm.tmovimiento_det_valorado tmv
     where tmv.id_mov_det_val_origen = v_record.id_movimiento_det_valorado;
	 v_saldo_real = v_record.cantidad -  v_cantidad;
      if((v_record.cantidad - coalesce( v_record.aux_saldo_fisico,0)) != v_cantidad)then
      raise notice 'contador: %',v_cont;
      	raise notice 'id_movimiento: %, id_movimiento_det_valorado: %, cantidad: %, aux_saldo_fisico: %' ,v_record.id_movimiento, v_record.id_movimiento_det_valorado, v_record.cantidad, v_record.aux_saldo_fisico;
        v_diferencia = (v_record.cantidad - coalesce(v_record.aux_saldo_fisico,0));
        v_diferencia_total = v_diferencia - v_cantidad;
        raise notice 'salidas/ingresos: % , total salidas: %, diferencia_total: %, saldo_real: %',v_diferencia,  v_cantidad, v_diferencia_total, v_saldo_real;
        raise notice 'codigo: % tipo: %', v_record.codigo, v_record.tipo;
        raise notice '================================================================================================================================================';
        v_cont = v_cont +1;
         /*update alm.tmovimiento_det_valorado set
          aux_saldo_fisico = v_saldo_real
         where id_movimiento_det_valorado = v_record.id_movimiento_det_valorado;*/

      end if;




    end loop;

    --INSERT INTO rec.ttipo_incidente(nombre_incidente,fk_tipo_incidente,tiempo_respuesta, nivel) VALUES (p_valor,1,'5',1);

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
