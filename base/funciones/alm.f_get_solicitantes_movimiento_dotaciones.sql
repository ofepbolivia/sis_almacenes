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
	FOR v_funcionario IN (select fun.desc_funcionario1
         				 from alm.tmovimiento mov
         				 inner join orga.vfuncionario fun on fun.id_funcionario=mov.id_funcionario
        				 where mov.codigo_tran=p_codigo_dotaciones
        				 group by fun.desc_funcionario1
         				 order by fun.desc_funcionario1)LOOP

    v_array_funcionarios[v_i]=v_funcionario.desc_funcionario1;
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