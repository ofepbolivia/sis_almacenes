CREATE OR REPLACE FUNCTION alm.f_get_solicitantes_movimiento_dotaciones (
  p_codigo_dotaciones varchar
)
RETURNS varchar AS
$body$
DECLARE
  	v_funcionario 	record;
    v_i				integer;
    v_array_funcionarios varchar[];
BEGIN

	v_i=0;
	FOR v_funcionario IN (select fun.desc_funcionario1, fun.lugar_nombre, fun.oficina_nombre, alm.f_get_items_movimiento(mov.id_movimiento) as item
         				 from alm.tmovimiento mov
         				 inner join orga.vfuncionario_cargo_lugar fun on fun.id_funcionario=mov.id_funcionario
                         and now() between fun.fecha_asignacion and COALESCE(fun.fecha_finalizacion,now())
            			 and fun.id_uo_funcionario < 1000000
                         left join orga.tuo uo on uo.id_uo=fun.id_uo
        				 where mov.codigo_tran=p_codigo_dotaciones
        				 group by fun.desc_funcionario1, fun.lugar_nombre, fun.oficina_nombre, uo.nombre_unidad, mov.id_movimiento
         				 order by fun.lugar_nombre,fun.oficina_nombre)LOOP

    v_array_funcionarios[v_i]=v_funcionario.desc_funcionario1||'~'||v_funcionario.lugar_nombre||'~'||v_funcionario.oficina_nombre||'~'||v_funcionario.item;
    v_i = v_i + 1;
    END LOOP;

    return array_to_string(v_array_funcionarios,',');
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;