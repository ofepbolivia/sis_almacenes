CREATE OR REPLACE FUNCTION alm.f_get_arbol_clasificacion (
  p_id_clasificacion integer
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema Almacenes
 FUNCION: 		orga.f_get_arbol_clasificacion
 DESCRIPCION:   Funcion que recupera los hijos o nietos clasificacion.
 AUTOR: 		(franklin.espinoza)
 FECHA:	        17-02-2020 15:15:26
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
    v_record_ids			record;
    v_cadena_ids			varchar = '';
BEGIN

    v_nombre_funcion = 'orga.f_get_arbol_clasificacion';

    for v_record_ids in select * from alm.tclasificacion tc where tc.id_clasificacion_fk = p_id_clasificacion order by tc.codigo_largo asc  loop
        if v_record_ids.id_clasificacion is null then
	       	return '';
        end if; --raise notice 'v_record_ids.id_clasificacion :%', p_id_clasificacion;
    	v_cadena_ids = v_cadena_ids||v_record_ids.id_clasificacion||','||alm.f_get_arbol_clasificacion(v_record_ids.id_clasificacion);

    end loop;

    RETURN v_cadena_ids;

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