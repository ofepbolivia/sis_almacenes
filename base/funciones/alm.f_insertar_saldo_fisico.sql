CREATE OR REPLACE FUNCTION alm.f_insertar_saldo_fisico (
  p_fecha date
)
RETURNS boolean AS
$body$
DECLARE

	v_nombre_funcion	text;
    v_resp				varchar;
   	v_resultado 		record;
   	v_consulta  		varchar;
	v_fisico			numeric;
BEGIN

	 v_nombre_funcion = 'alm.f_insertar_saldo_fisico';


     /*create temp table ttsaldo_fisico_item(
     	id_item integer,
    	id_almacen integer,
    	fecha_hasta date,
    	fisico numeric

     ) on commit drop;*/

     for v_resultado in select ti.id_item, ti.codigo, ti.nombre, ta.id_almacen, ta.codigo, ta.nombre
                        from alm.talmacen_stock tas
                        inner join alm.titem ti on ti.id_item = tas.id_item
                        inner join alm.talmacen ta on ta.id_almacen = tas.id_almacen
                        where tas.id_almacen = 1 loop

     	v_fisico = alm.f_get_saldo_fisico_item(v_resultado.id_item, v_resultado.id_almacen, p_fecha, 'no');

        insert into alm.tsaldo_fisico_item (
        	id_item,
            id_almacen,
            fecha_hasta,
            fisico
        ) values (
        	v_resultado.id_item,
            v_resultado.id_almacen,
            p_fecha,
            v_fisico
        );
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