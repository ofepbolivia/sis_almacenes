CREATE OR REPLACE FUNCTION alm.ft_clasificacion_partida_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:        Almacenes
 FUNCION:        alm.ft_clasificacion_partida_ime
 DESCRIPCION:    Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones) de la tabla 'alm.talmacen_partida'
 AUTOR:          maylee.perez
 FECHA:          25-11-2020
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

    v_nro_requerimiento        	integer;
    v_parametros               	record;
    v_id_requerimiento         	integer;
    v_resp                      varchar;
    v_nombre_funcion        	text;
    v_mensaje_error         	text;
    v_id_item_partida			integer;

    v_parametros_item			record;
    v_id_item_partida_fk		integer;
    v_parametros_item_partida	record;

    v_param_id_clasificacion	integer;
    v_param_id_partida			integer;
    v_partida_clasificacion		integer;
    v_id_item					record;

    v_item_partida				record;

    v_gestion_nom				integer;
    v_gestion_actual			integer;
    v_id_gestion				integer;


BEGIN

    v_nombre_funcion = 'alm.ft_clasificacion_partida_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'SAL_CLASPAR_INS'
     #DESCRIPCION:  Insercion de registros
     #AUTOR:        maylee.perez
     #FECHA:        25-11-2020
    ***********************************/

    if(p_transaccion='SAL_CLASPAR_INS')then

        begin

             --raise exception 'llega %',v_parametros.id_item;
           IF (v_parametros.id_item = 0 ) THEN --is null

                  IF EXISTS (SELECT 1
                      FROM alm.titem_partida ip
                      WHERE ip.id_clasificacion= v_parametros.id_clasificacion
                      AND ip.estado_reg = 'activo' )
                      AND ip.id_gestion = v_parametros.id_gestion) THEN

                      RAISE EXCEPTION 'SOLO SE PERMITE REGISTRAR UNA PARTIDA.';

                  END IF;

                  --Sentencia de la insercion
                  insert into alm.titem_partida(
                      id_usuario_reg,
                      fecha_reg,
                      estado_reg,

                      id_clasificacion,
                      id_partida,
                      tipo,
                      id_item,
                      id_gestion

                  ) values (
                      p_id_usuario,
                      now(),
                      'activo',

                      v_parametros.id_clasificacion,
                      v_parametros.id_partida,
                      'directo',
                      null, --v_parametros_item.id_item,
                      v_parametros.id_gestion

                  ) RETURNING id_item_partida into v_id_item_partida;


                 FOR v_parametros_item  IN(SELECT itm.id_item
                                          FROM alm.titem itm
                                          join alm.tclasificacion cla on cla.id_clasificacion = itm.id_clasificacion
                                          WHERE itm.id_clasificacion = v_parametros.id_clasificacion
                                          or cla.id_clasificacion_fk = v_parametros.id_clasificacion
                						) LOOP

                              UPDATE alm.titem SET
                              id_partida = v_parametros.id_partida
                              WHERE id_item = v_parametros_item.id_item;

                 END LOOP;


           ELSE

                IF EXISTS (SELECT 1
                    FROM alm.titem_partida ip
                    WHERE ip.id_item= v_parametros.id_item
                    AND ip.estado_reg = 'activo'
                    AND ip.id_gestion = v_parametros.id_gestion ) THEN

                    RAISE EXCEPTION 'SOLO SE PERMITE REGISTRAR UNA PARTIDA PARA EL ITEM.';

                END IF;

           		--Sentencia de la insercion
              insert into alm.titem_partida(
                  id_usuario_reg,
                  fecha_reg,
                  estado_reg,

                  id_clasificacion,
                  id_partida,
                  tipo,
                  id_item,
                  id_gestion

              ) values (
                  p_id_usuario,
                  now(),
                  'activo',

                  null,--v_parametros.id_clasificacion,
                  v_parametros.id_partida,
                  'directo',
                  v_parametros.id_item,
                  v_parametros.id_gestion

              ) RETURNING id_item_partida into v_id_item_partida;

              update alm.titem set
                id_partida = v_parametros.id_partida
                where id_item = v_parametros.id_item;


           END IF;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida almacenado(a) con exito (id_item_partida'||v_id_item_partida||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item_partida',v_id_item_partida::varchar);

            --Devuelve la respuesta
            return v_resp;
        end;

    /*********************************
     #TRANSACCION:  'SAL_CLASPAR_MOD'
     #DESCRIPCION:  Modificacion de registros
     #AUTOR:        maylee.perez
     #FECHA:        25-11-2020
    ***********************************/

    elsif(p_transaccion='SAL_CLASPAR_MOD')then

        begin

        	--raise exception 'llegaedit %',v_parametros.id_item;

            IF (v_parametros.id_item is not null) THEN

            	update alm.titem set
                id_partida = v_parametros.id_partida
                where id_item = v_parametros.id_item;

                --Sentencia de la insercion
                insert into alm.titem_partida(
                    id_usuario_reg,
                    fecha_reg,
                    estado_reg,

                    id_clasificacion,
                    id_partida,
                    tipo,
                    id_item,
                    id_gestion

                ) values (
                    p_id_usuario,
                    now(),
                    'activo',

                    null,
                    v_parametros.id_partida,
                    'directo',
                    v_parametros.id_item,
                    v_parametros.id_gestion

                ) RETURNING id_item_partida into v_id_item_partida;

                IF EXISTS (SELECT 1
            			FROM alm.titem_partida ip
                        WHERE ip.id_item= v_parametros.id_item
                        AND ip.estado_reg = 'activo'
                        AND ip.id_gestion = v_parametros.id_gestion) THEN

                        RAISE EXCEPTION 'SOLO SE PERMITE REGISTRAR UNA PARTIDA PARA EL ITEM.';

            	END IF;

            	/*update alm.titem_partida set
            	id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_item_partida = v_parametros.id_item_partida,
                id_clasificacion = v_parametros.id_clasificacion,
                id_partida = v_parametros.id_partida,
                id_gestion = v_parametros.id_gestion
            	where id_item_partida=v_parametros.id_item_partida;*/

            ELSE

            	update alm.titem_partida set
            	id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                --id_item_partida = v_parametros.id_item_partida,
                id_clasificacion = v_parametros.id_clasificacion,
                id_partida = v_parametros.id_partida,
                id_gestion = v_parametros.id_gestion
                --tipo= v_parametros.tipo

            	where id_item_partida=v_parametros.id_item_partida;

                 FOR v_parametros_item  IN(SELECT itm.id_item
                                          FROM alm.titem itm
                                          join alm.tclasificacion cla on cla.id_clasificacion = itm.id_clasificacion
                                          WHERE itm.id_clasificacion = v_parametros.id_clasificacion
                                          or cla.id_clasificacion_fk = v_parametros.id_clasificacion
                						) LOOP

                              UPDATE alm.titem SET
                              id_partida = v_parametros.id_partida
                              WHERE id_item = v_parametros_item.id_item;

                 END LOOP;


            END IF;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item_partida',v_parametros.id_item_partida::VARCHAR);

            --Devuelve la respuesta
            return v_resp;
        end;

    /*********************************
     #TRANSACCION:  'SAL_CLASPAR_ELI'
     #DESCRIPCION:  Eliminacion de registros
     #AUTOR:        maylee.perez
     #FECHA:        25-11-2020
    ***********************************/

    elsif(p_transaccion='SAL_CLASPAR_ELI')then

        begin


            SELECT ip.*
            INTO v_parametros_item_partida
            FROM alm.titem_partida ip
            WHERE ip.id_item_partida = v_parametros.id_item_partida;
            --raise exception 'llegaeli % - %',v_parametros_item_partida.id_item,v_parametros_item_partida.id_clasificacion ;


            IF (v_parametros_item_partida.id_item is NULL) THEN

            	--raise exception 'llegapartida111 % ',v_parametros_item_partida.id_clasificacion;

                FOR v_id_item  IN(SELECT itm.id_item
                                          FROM alm.titem itm
                                          join alm.tclasificacion cla on cla.id_clasificacion = itm.id_clasificacion
                                          WHERE itm.id_clasificacion = v_parametros_item_partida.id_clasificacion
                                          or cla.id_clasificacion_fk = v_parametros_item_partida.id_clasificacion
                						) LOOP

                              update alm.titem set
                              id_partida = null
                              where id_item = v_id_item.id_item;

                 END LOOP;

                 update alm.titem_partida set
                 estado_reg = 'inactivo',
                 fecha_mod = now(),
                 id_usuario_mod = p_id_usuario
                 where id_clasificacion =v_parametros_item_partida.id_clasificacion;


            ELSE

                SELECT item.id_partida, item.id_clasificacion
                INTO v_param_id_partida, v_param_id_clasificacion
                FROM alm.titem item
                WHERE item.id_item = v_parametros_item_partida.id_item;

                SELECT ip.id_partida
                INTO  v_partida_clasificacion
                FROM alm.titem_partida ip
                WHERE ip.id_clasificacion = v_param_id_clasificacion;

            	--raise exception 'llegapartida %',v_partida_clasificacion;

                update alm.titem set
            	id_partida = v_partida_clasificacion
            	where id_item = v_parametros_item_partida.id_item;

                update alm.titem_partida set
            	estado_reg = 'inactivo',
                fecha_mod = now(),
                id_usuario_mod = p_id_usuario
            	where id_item_partida = v_parametros.id_item_partida;

            END IF;



            /*update alm.titem_partida set
            	estado_reg = 'inactivo'
            where id_item_partida = v_parametros.id_item_partida;*/

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_item_partida',v_parametros.id_item_partida::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;

        /*********************************
         #TRANSACCION:  'SAL_CLOCLAPA_IME'
         #DESCRIPCION:  clonar registros
         #AUTOR:        maylee.perez
         #FECHA:        30-12-2020
        ***********************************/

        elsif(p_transaccion='SAL_CLOCLAPA_IME')then

         begin


                 select ip.*
                 into v_item_partida
                 from alm.titem_partida ip
                 where ip.id_item_partida = v_parametros.id_item_partida;


                SELECT ges.gestion
                INTO v_gestion_nom
                FROM param.tgestion ges
                WHERE ges.id_gestion = v_item_partida.id_gestion;

                v_gestion_actual = v_gestion_nom + 1 ;

                SELECT ges.id_gestion
                INTO v_id_gestion
                FROM param.tgestion ges
                WHERE ges.gestion = v_gestion_actual;



                --inserta

                FOR v_parametros_item_partida  IN(select ip.*
                                       from alm.titem_partida ip
                                       where ip.id_gestion = v_item_partida.id_gestion
                                       and ip.estado_reg ='activo'
                						) LOOP




                              --Sentencia de la insercion
                              insert into alm.titem_partida(
                                  id_usuario_reg,
                                  fecha_reg,
                                  id_usuario_mod,
                                  fecha_mod,
                                  estado_reg,

                                  id_clasificacion,
                                  id_partida,
                                  tipo,
                                  id_item,
                                  id_gestion

                              ) values (
                                  v_parametros_item_partida.id_usuario_reg,
                                  v_parametros_item_partida.fecha_reg,
                                  v_parametros_item_partida.id_usuario_mod,
                                  v_parametros_item_partida.fecha_mod,
                                  v_parametros_item_partida.estado_reg,

                                  v_parametros_item_partida.id_clasificacion,
                                  v_parametros_item_partida.id_partida,
                                  'directo',
                                  v_parametros_item_partida.id_item,
                                  v_id_gestion

                              ) RETURNING id_item_partida into v_id_item_partida;


                 END LOOP;


              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partidas Replicado');
              v_resp = pxp.f_agrega_clave(v_resp,'id_item_partida',v_id_item_partida::varchar);

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
