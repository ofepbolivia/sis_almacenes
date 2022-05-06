CREATE OR REPLACE FUNCTION alm.ft_item_concepto_ingas_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Almacenes
 FUNCION: 		alm.ft_item_concepto_ingas_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'alm.titem_concepto_ingas'
 AUTOR: 		 (gsarmiento)
 FECHA:	        18-05-2017 14:01:28
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

	v_nombre_funcion = 'alm.ft_item_concepto_ingas_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ALM_ITMINGAS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		gsarmiento
 	#FECHA:		18-05-2017 14:01:28
	***********************************/

	if(p_transaccion='ALM_ITMINGAS_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						itmingas.id_item_concepto_ingas,
						itmingas.id_item,
						itmingas.id_concepto_ingas,
                        cgas.desc_ingas,
						itmingas.estado_reg,
						itmingas.id_usuario_ai,
						itmingas.id_usuario_reg,
						itmingas.usuario_ai,
						itmingas.fecha_reg,
						itmingas.fecha_mod,
						itmingas.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from alm.titem_concepto_ingas itmingas
                        inner join param.tconcepto_ingas cgas on cgas.id_concepto_ingas=itmingas.id_concepto_ingas
						inner join segu.tusuario usu1 on usu1.id_usuario = itmingas.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = itmingas.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'ALM_ITMINGAS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		gsarmiento
 	#FECHA:		18-05-2017 14:01:28
	***********************************/

	elsif(p_transaccion='ALM_ITMINGAS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_item_concepto_ingas)
					    from alm.titem_concepto_ingas itmingas
                        inner join param.tconcepto_ingas cgas on cgas.id_concepto_ingas=itmingas.id_concepto_ingas
					    inner join segu.tusuario usu1 on usu1.id_usuario = itmingas.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = itmingas.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

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