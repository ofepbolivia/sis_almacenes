--------------- SQL ---------------

CREATE OR REPLACE FUNCTION alm.ft_clasificacion_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Almacenes
 FUNCION: 		alm.ft_clasificacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'alm.tclasificacion'
 AUTOR: 		Gonzalo Sarmiento
 FECHA:	        25-09-2012
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
	v_id_clasificacion	integer;
    v_codigo_largo varchar;
    v_aux_cont				integer;
    v_aux_resp				boolean;
    v_usuario				varchar;

BEGIN

    v_nombre_funcion = 'alm.ft_clasificacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'SAL_CLA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:			Gonzalo Sarmiento
 	#FECHA:			25-09-2012
	***********************************/

	if(p_transaccion='SAL_CLA_INS')then

		begin
        	IF v_parametros.id_clasificacion_fk is null THEN
            	v_codigo_largo = v_parametros.codigo;
            ELSE
            	v_codigo_largo = alm.f_codigo_clasificaciones_recursivo(v_parametros.id_clasificacion_fk)||'.'||v_parametros.codigo;
                UPDATE alm.tclasificacion SET
                    fecha_mod = now(),
                	id_usuario_mod = p_id_usuario,
                	estado = 'restringido'
                WHERE id_clasificacion = v_parametros.id_clasificacion_fk;
            END IF;
        	--Sentencia de la insercion
        	insert into alm.tclasificacion (
                estado_reg,
                fecha_reg,
                id_usuario_reg,
                codigo,
                id_clasificacion_fk,
                nombre,
                descripcion,
                codigo_largo
          	) values(
                'activo',
                now(),
                p_id_usuario,
                v_parametros.codigo,
                v_parametros.id_clasificacion_fk,
                v_parametros.nombre,
                v_parametros.descripcion,
                v_codigo_largo
			)RETURNING id_clasificacion into v_id_clasificacion;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Clasificacion almacenado(a) con exito (id_clasificacion'||v_id_clasificacion||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion',v_id_clasificacion::varchar);

            --Devuelve la respuesta
            return v_resp;
		end;

	/*********************************
 	#TRANSACCION:  'SAL_CLA_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:			Gonzalo Sarmiento
 	#FECHA:			25-09-2012
	***********************************/

	elsif(p_transaccion='SAL_CLA_MOD')then
		begin

        	SELECT cuenta into v_usuario
            FROM segu.tusuario
            WHERE id_usuario=p_id_usuario;

            IF v_parametros.id_clasificacion_fk is null THEN
            	v_codigo_largo = v_parametros.codigo;
            ELSE
            	v_codigo_largo = alm.f_codigo_clasificaciones_recursivo(v_parametros.id_clasificacion_fk)||'.'||v_parametros.codigo;
                UPDATE alm.tclasificacion SET
                	fecha_mod = now(),
                	id_usuario_mod = p_id_usuario,
                	estado = 'restringido'
                WHERE id_clasificacion = v_parametros.id_clasificacion_fk;
            END IF;
			--Sentencia de la modificacion
			update alm.tclasificacion set
                fecha_mod = now(),
                id_usuario_mod = p_id_usuario,
                codigo = v_parametros.codigo,
                id_clasificacion_fk = v_parametros.id_clasificacion_fk,
                nombre = v_parametros.nombre,
                descripcion = v_parametros.descripcion,
                codigo_largo=v_codigo_largo
			where id_clasificacion=v_parametros.id_clasificacion;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Clasificacion modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion',v_parametros.id_clasificacion::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'codigo',v_codigo_largo::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'usuario',v_usuario::varchar);
               
            --Devuelve la respuesta
            return v_resp;
		end;

	/*********************************    
 	#TRANSACCION:  'SAL_CLA_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:			Gonzalo Sarmiento	
 	#FECHA:			25-09-2012
	***********************************/

	elsif(p_transaccion='SAL_CLA_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from alm.tclasificacion
            where id_clasificacion=v_parametros.id_clasificacion;
        	
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Clasificacion eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion',v_parametros.id_clasificacion::varchar);
        	
            --Devuelve la respuesta
            return v_resp;

		end;
        
    /*********************************    
 	#TRANSACCION:  'SAL_ESTCLA_MOD'
 	#DESCRIPCION:	Bloqueo y desbloqueo de clasificacion
 	#AUTOR:			Ariel Ayaviri Omonte
 	#FECHA:			06-02-2013
	***********************************/

	elsif(p_transaccion='SAL_ESTCLA_MOD')then
		begin
            
			v_aux_resp = alm.f_update_estado_clasificacion_recursivo(v_parametros.id_clasificacion, v_parametros.estado, p_id_usuario);
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Clasificacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion',v_parametros.id_clasificacion::varchar);
               
            --Devuelve la respuesta
            return v_resp;
		end;
    
    /*******************************    
	#TRANSACCION:	SAL_CLADD_MOD
	#DESCRIPCION:	Inserta interfaces en el arbol
	#AUTOR:			Ariel Ayaviri Omonte
	#FECHA:			132-03-2013
	#RESUMEN:		
    ***********************************/
	ELSEIF (p_transaccion='SAL_CLADD_MOD') THEN
    BEGIN
    
    	-- 1) si point es igual append
        IF (v_parametros.punto='append') then 
        	IF (v_parametros.tipo_nodo = 'item') THEN
            	UPDATE alm.titem
                SET id_clasificacion = v_parametros.id_target
                WHERE id_item = v_parametros.id_nodo;
            
            ELSEIF(v_parametros.tipo_nodo = 'hijo') THEN
            	UPDATE alm.tclasificacion
                SET id_clasificacion_fk = v_parametros.id_target
                WHERE id_clasificacion = v_parametros.id_nodo;
            END IF;
        ELSE
 		  --	2) regresar error point no soportados
          raise exception 'POINT no soportado %',v_parametros.punto;
        END IF;
        
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','DRANG AND DROP exitoso id_nodo='||v_parametros.id_nodo||' id_target= '|| v_parametros.id_target||'  id_old_gui='|| v_parametros.id_old_parent || ' tipo_nodo=' ||v_parametros.tipo_nodo); 
        return v_resp;
    END;
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