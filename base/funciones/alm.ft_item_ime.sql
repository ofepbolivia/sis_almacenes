CREATE OR REPLACE FUNCTION alm.ft_item_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:        Almacenes
 FUNCION:        alm.ft_item_ime
 DESCRIPCION:    Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones) de la tabla 'alm.titem'
 AUTOR:          Gonzalo Sarmiento
 FECHA:          21-09-2012
 COMENTARIOS:   
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:   
 AUTOR:           
 FECHA:       
***************************************************************************/

DECLARE

    v_nro_requerimiento        integer;
    v_parametros               record;
    v_id_requerimiento         integer;
    v_resp                    varchar;
    v_nombre_funcion        text;
    v_mensaje_error         text;
    v_id_item    integer;
    v_num_por_clasificacion		integer;
    v_codigo_acumulado			varchar;
    v_almacenes					text[];
    v_id_almacen				text;
    v_id_metodo_val				integer;
    v_codigo					varchar;
    v_cuenta					varchar;

    --franklin.espinoza
    v_llamada         varchar;
BEGIN

    v_nombre_funcion = 'alm.ft_item_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'SAL_ITEM_INS'
     #DESCRIPCION:  Insercion de registros
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        21-09-2012
    ***********************************/

    if(p_transaccion='SAL_ITEM_INS')then

        begin
        	IF v_parametros.id_clasificacion is not null THEN
            	UPDATE alm.tclasificacion SET
                	estado = 'restringido'
                WHERE id_clasificacion = v_parametros.id_clasificacion;
        	ELSE
            	v_codigo_acumulado = 'Sin clasificar';
            END IF;
            /*if exists (	select 1
            			from alm.titem i
                        where estado_reg = 'activo' and
                        upper(TRIM( both ' ' from i.nombre)) like upper(TRIM( both ' ' from v_parametros.nombre))) then

            	raise exception 'Ya existe un item con el mismo nombre, cambie el nombre para poder guardar el registro';
            end if; */


        	--Sentencia de la insercion
            insert into alm.titem(
            	id_usuario_reg,
                fecha_reg,
                estado_reg,
                id_clasificacion,
                codigo,
                nombre,
                descripcion,
                palabras_clave,
                codigo_fabrica,
                observaciones,
                numero_serie,
                id_unidad_medida,
                precio_ref,
                id_moneda
            ) values (
                p_id_usuario,
                now(),
                'activo',
                v_parametros.id_clasificacion,
                v_codigo_acumulado,
                v_parametros.nombre,
                v_parametros.descripcion,
                v_parametros.palabras_clave,
                v_parametros.codigo_fabrica,
                v_parametros.observaciones,
                v_parametros.numero_serie,
                v_parametros.id_unidad_medida,
                v_parametros.precio_ref,
                param.f_get_moneda_base()
            ) RETURNING id_item into v_id_item;

            v_almacenes = string_to_array (v_parametros.id_almacen, ',');

            foreach v_id_almacen in array v_almacenes loop

            	select id_metodo_val into v_id_metodo_val
            	from alm.talmacen
            	where id_almacen = v_id_almacen::integer;

            	if (v_id_metodo_val is not null) then
	            	INSERT INTO
					  alm.talmacen_stock
					(
					  id_usuario_reg,
					  fecha_reg,
					  estado_reg,
					  id_almacen,
					  id_item,
					  cantidad_min,
					  cantidad_alerta_amarilla,
					  cantidad_alerta_roja,
					  id_metodo_val
					)
					VALUES (
					  p_id_usuario,
					  now(),
					  'activo',
					  v_id_almacen::integer,
					  v_id_item,
					  0,
					  0,
					  0,
					  v_id_metodo_val
					);
				end if;
            end loop;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Item almacenado(a) con exito (id_item'||v_id_item||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item',v_id_item::varchar);

            --Devuelve la respuesta
            return v_resp;
        end;

    /*********************************
     #TRANSACCION:  'SAL_ITEM_MOD'
     #DESCRIPCION:  Modificacion de registros
     #AUTOR:        Gonzalo Sarmiento
     #FECHA:        21-09-2012
    ***********************************/

    elsif(p_transaccion='SAL_ITEM_MOD')then

        begin
        	IF v_parametros.id_clasificacion is not null THEN
            	UPDATE alm.tclasificacion SET
                	estado = 'restringido'
                WHERE id_clasificacion = v_parametros.id_clasificacion;
            END IF;

            /*if exists (	select 1
            			from alm.titem i
                        where estado_reg = 'activo' and
                        i.id_item != v_parametros.id_item and
                        upper(TRIM( both ' ' from i.nombre)) like upper(TRIM( both ' ' from v_parametros.nombre))) then

            	raise exception 'Ya existe un item con el mismo nombre, cambie el nombre para poder guardar el registro';
            end if; */
            --Sentencia de la modificacion

            select codigo into v_codigo
            from alm.titem
            where id_item=v_parametros.id_item;

            select cuenta into v_cuenta
            from segu.tusuario
            where id_usuario=p_id_usuario;

            update alm.titem set
            	id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_clasificacion = v_parametros.id_clasificacion,
                nombre = v_parametros.nombre,
                descripcion = v_parametros.descripcion,
                palabras_clave = v_parametros.palabras_clave,
                codigo_fabrica = v_parametros.codigo_fabrica,
                observaciones = v_parametros.observaciones,
                numero_serie = v_parametros.numero_serie,
                id_unidad_medida = v_parametros.id_unidad_medida,
                precio_ref = v_parametros.precio_ref
            where id_item=v_parametros.id_item;
            v_almacenes = string_to_array (v_parametros.id_almacen, ',');

            foreach v_id_almacen in array v_almacenes loop

            	select id_metodo_val into v_id_metodo_val
            	from alm.talmacen
            	where id_almacen = v_id_almacen::integer;

                if (not exists (select 1 from alm.talmacen_stock
                				where estado_reg = 'activo' and
                				id_almacen = v_id_almacen::integer and id_item = v_parametros.id_item )) then

                    if (v_id_metodo_val is not null) then
                        INSERT INTO
                          alm.talmacen_stock
                        (
                          id_usuario_reg,
                          fecha_reg,
                          estado_reg,
                          id_almacen,
                          id_item,
                          cantidad_min,
                          cantidad_alerta_amarilla,
                          cantidad_alerta_roja,
                          id_metodo_val
                        )
                        VALUES (
                          p_id_usuario,
                          now(),
                          'activo',
                          v_id_almacen::integer,
                          v_parametros.id_item,
                          0,
                          0,
                          0,
                          v_id_metodo_val
                        );
                    end if;
				end if;
            end loop;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Item modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item',v_parametros.id_item::VARCHAR);
            v_resp = pxp.f_agrega_clave(v_resp,'codigo',v_codigo::VARCHAR);
            v_resp = pxp.f_agrega_clave(v_resp,'usuario',v_cuenta::VARCHAR);
              
            --Devuelve la respuesta
            return v_resp;
        end;

    /*********************************   
     #TRANSACCION:  'SAL_ITEM_ELI'
     #DESCRIPCION:  Eliminacion de registros
     #AUTOR:        Gonzalo Sarmiento   
     #FECHA:        21-09-2012
    ***********************************/

    elsif(p_transaccion='SAL_ITEM_ELI')then

        begin
            update alm.titem set
            	estado_reg = 'inactivo'
            where id_item = v_parametros.id_item;
              
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Item eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item',v_parametros.id_item::varchar);
             
            --Devuelve la respuesta
            return v_resp;

        end;
        
    /*********************************   
     #TRANSACCION:  'SAL_GENCODE_MOD'
     #DESCRIPCION:  Modificacion del codigo de un item
     #AUTOR:        Ariel Ayaviri
     #FECHA:        05-02-2013
    ***********************************/

    elsif(p_transaccion='SAL_GENCODE_MOD')then

        begin
        	select max(num_por_clasificacion) into v_num_por_clasificacion
            from alm.titem
            where id_clasificacion = v_parametros.id_clasificacion;
            
            IF(v_num_por_clasificacion is null) THEN
            	v_num_por_clasificacion := 1;
            ELSE
            	v_num_por_clasificacion := v_num_por_clasificacion + 1;
            END IF;
            
            --se debe obtener el codigo de todos los padres del item seleccionado
            
            v_codigo_acumulado = alm.f_codigo_clasificaciones_recursivo(v_parametros.id_clasificacion);
            
            update alm.titem set
            	codigo = v_codigo_acumulado||'.'||v_num_por_clasificacion,
                num_por_clasificacion = v_num_por_clasificacion
            where id_item = v_parametros.id_item;
            
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Item eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item',v_parametros.id_item::varchar);
            
            --Devuelve la respuesta
            return v_resp;

        end;
    /*********************************
     #TRANSACCION:  'SAL_FISICO_ITEM_IME'
     #DESCRIPCION:  Modificacion del codigo de un item
     #AUTOR:        franklin.espinoza
     #FECHA:        07-01-2020
    ***********************************/

    elsif(p_transaccion='SAL_FISICO_ITEM_IME')then

        begin

          v_llamada = alm.f_upload_saldo_fisico_item(v_parametros.fecha_cierre);

          --Definicion de la respuesta
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Carga de Saldo Exitosa');
          v_resp = pxp.f_agrega_clave(v_resp,'id_item','0'::varchar);

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