CREATE OR REPLACE FUNCTION alm.ft_item_concepto_ingas_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Almacenes
 FUNCION: 		alm.ft_item_concepto_ingas_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'alm.titem_concepto_ingas'
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

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_item_concepto_ingas	integer;

BEGIN

    v_nombre_funcion = 'alm.ft_item_concepto_ingas_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ALM_ITMINGAS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		gsarmiento
 	#FECHA:		18-05-2017 14:01:28
	***********************************/

	if(p_transaccion='ALM_ITMINGAS_INS')then

        begin

        	IF EXISTS (SELECT 1
            		  FROM alm.titem_concepto_ingas
                      WHERE id_item = v_parametros.id_item) THEN
            	raise exception 'Ya existe un concepto de gasto asignado al item';
            END IF;
        	--Sentencia de la insercion
        	insert into alm.titem_concepto_ingas(
			id_item,
			id_concepto_ingas,
			estado_reg,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_item,
			v_parametros.id_concepto_ingas,
			'activo',
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null



			)RETURNING id_item_concepto_ingas into v_id_item_concepto_ingas;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Item Concepto Ingas almacenado(a) con exito (id_item_concepto_ingas'||v_id_item_concepto_ingas||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item_concepto_ingas',v_id_item_concepto_ingas::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ALM_ITMINGAS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		gsarmiento
 	#FECHA:		18-05-2017 14:01:28
	***********************************/

	elsif(p_transaccion='ALM_ITMINGAS_MOD')then

		begin
			--Sentencia de la modificacion
			update alm.titem_concepto_ingas set
			id_item = v_parametros.id_item,
			id_concepto_ingas = v_parametros.id_concepto_ingas,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_item_concepto_ingas=v_parametros.id_item_concepto_ingas;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Item Concepto Ingas modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item_concepto_ingas',v_parametros.id_item_concepto_ingas::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ALM_ITMINGAS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		gsarmiento
 	#FECHA:		18-05-2017 14:01:28
	***********************************/

	elsif(p_transaccion='ALM_ITMINGAS_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from alm.titem_concepto_ingas
            where id_item_concepto_ingas=v_parametros.id_item_concepto_ingas;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Item Concepto Ingas eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item_concepto_ingas',v_parametros.id_item_concepto_ingas::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

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