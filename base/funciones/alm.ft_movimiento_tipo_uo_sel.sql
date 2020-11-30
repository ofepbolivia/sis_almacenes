CREATE OR REPLACE FUNCTION alm.ft_movimiento_tipo_uo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Almacenes
 FUNCION: 		alm.ft_movimiento_tipo_uo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'alm.tmovimiento_tipo_uo'
 AUTOR: 		 (admin)
 FECHA:	        22-08-2013 22:55:37
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'alm.ft_movimiento_tipo_uo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'SAL_TIMVUO_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		22-08-2013 22:55:37
	***********************************/

	if(p_transaccion='SAL_TIMVUO_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						timvuo.id_movimiento_tipo_uo,
						timvuo.id_movimiento_tipo,
						timvuo.id_uo,
						timvuo.estado_reg,
						timvuo.fecha_reg,
						timvuo.id_usuario_reg,
						timvuo.fecha_mod,
						timvuo.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						uo.codigo,
						uo.nombre_cargo as desc_uo,
                        pxp.list(vf.desc_funcionario1)::varchar as funcionario
						from alm.tmovimiento_tipo_uo timvuo
						inner join segu.tusuario usu1 on usu1.id_usuario = timvuo.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = timvuo.id_usuario_mod
						inner join orga.tuo uo on uo.id_uo = timvuo.id_uo
                        inner join orga.tuo_funcionario tuo on tuo.id_uo = uo.id_uo and tuo.estado_reg = ''activo'' and coalesce(tuo.fecha_finalizacion,''31/12/9999'') >= current_date
						inner join orga.vfuncionario vf on vf.id_funcionario = tuo.id_funcionario
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||'group by uo.nombre_cargo,timvuo.id_movimiento_tipo_uo, usu1.cuenta, usu2.cuenta, uo.codigo';
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
raise notice 'v_consulta: %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'SAL_TIMVUO_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		22-08-2013 22:55:37
	***********************************/

	elsif(p_transaccion='SAL_TIMVUO_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_movimiento_tipo_uo)
					    from alm.tmovimiento_tipo_uo timvuo
					    inner join segu.tusuario usu1 on usu1.id_usuario = timvuo.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = timvuo.id_usuario_mod
						inner join orga.tuo uo on uo.id_uo = timvuo.id_uo
						inner join orga.tuo_funcionario tuo on tuo.id_uo = uo.id_uo and tuo.estado_reg = ''activo'' and coalesce(tuo.fecha_finalizacion,''31/12/9999'') >= current_date
						inner join orga.vfuncionario vf on vf.id_funcionario = tuo.id_funcionario
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||'group by uo.nombre_cargo,timvuo.id_movimiento_tipo_uo, usu1.cuenta, usu2.cuenta, uo.codigo';
			--Devuelve la respuesta
			return v_consulta;

		end;

	else

		raise exception 'Transaccion inexistente';

	end if;

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