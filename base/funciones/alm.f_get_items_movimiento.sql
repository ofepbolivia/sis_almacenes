CREATE OR REPLACE FUNCTION alm.f_get_items_movimiento (
  p_id_movimiento integer
)
RETURNS varchar AS
$body$
DECLARE
  	v_items 		record;
    v_i				integer;
    v_array_items 	varchar[];
BEGIN

	v_i=0;
	FOR v_items IN (
                    select itm.descripcion
                    from alm.tmovimiento_det det
                    inner join alm.titem itm on itm.id_item=det.id_item
                    where det.id_movimiento=p_id_movimiento)LOOP

    v_array_items[v_i]=v_items.descripcion;
    v_i = v_i + 1;
    END LOOP;

    return array_to_string(v_array_items,';');
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;