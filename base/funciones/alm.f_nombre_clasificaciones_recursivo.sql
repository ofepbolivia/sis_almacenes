CREATE OR REPLACE FUNCTION alm.f_nombre_clasificaciones_recursivo (
  v_id_clasificacion integer
)
RETURNS varchar AS
$body$
DECLARE
  v_codigo_largo varchar;
  v_id_clasificacion_padre 	integer;
  v_nombre_acumulado 		varchar;
  v_nombre_clasificacion	varchar;
BEGIN
	SELECT
    	cla.codigo_largo, cla.id_clasificacion_fk, cla.nombre
        into v_codigo_largo, v_id_clasificacion_padre, v_nombre_clasificacion
    FROM alm.tclasificacion cla
    WHERE cla.id_clasificacion = v_id_clasificacion;

    IF(length(v_codigo_largo) < 6) THEN
    	RETURN v_nombre_clasificacion;
    ELSE
    	v_nombre_acumulado = alm.f_nombre_clasificaciones_recursivo(v_id_clasificacion_padre);
    	RETURN v_nombre_acumulado ||'~'|| v_nombre_clasificacion;
    END IF;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;