CREATE OR REPLACE FUNCTION alm.ft_clasificacion_partida_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:        Almacenes
 FUNCION:        alm.ft_clasificacion_partida_sel
 DESCRIPCION:    Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'alm.tclasificacion_partida'
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

    v_consulta            	varchar;
    v_parametros          	record;
    v_nombre_funcion       	text;
    v_resp                	varchar;
    v_where 				varchar;
	v_resp_global			varchar;
	v_id_movimiento_tipo	integer;
	v_ids				varchar;
	v_from				varchar;

    v_id_clasifica		varchar;
    v_id_item			integer;

BEGIN

    v_nombre_funcion = 'alm.ft_clasificacion_partida_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'SAL_CLASPAR_SEL'
     #DESCRIPCION:    Consulta de datos
     #AUTOR:        maylee.perez
     #FECHA:        25-11-2020
    ***********************************/

    if(p_transaccion='SAL_CLASPAR_SEL')then

        begin
        --raise exception 'llegaitem %',v_parametros.id_item;
        	IF (v_parametros.id_item = 0 ) THEN
            	--raise exception 'llega1 %',v_parametros.id_clasificacion;
                    --recuperar los padres de la rama
                   WITH RECURSIVE clasificacion(id_clasificacion, id_clasificacion_padre) AS (

                      select  clas.id_clasificacion,
                              clas.id_clasificacion_fk
                      from alm.tclasificacion clas
                      where clas.id_clasificacion = v_parametros.id_clasificacion
                      and clas.estado_reg = 'activo'

                      UNION

                      SELECT clas2.id_clasificacion,
                             clas2.id_clasificacion_fk
                      FROM alm.tclasificacion clas2, clasificacion pc
                      WHERE clas2.id_clasificacion = pc.id_clasificacion_padre  and clas2.estado_reg = 'activo'

                   )

                      SELECT  pxp.list(id_clasificacion::varchar)
                      into v_id_clasifica
                      FROM clasificacion;

                    v_id_clasifica = COALESCE(v_id_clasifica, '0');

                    --Sentencia de la consulta
            v_consulta:='
            	select  cpa.id_item_partida,
                        cpa.id_clasificacion,
                        cpa.id_partida,

                        (CASE WHEN cpa.id_clasificacion = '||v_parametros.id_clasificacion||'
                        THEN
                           ''directo''
                        ELSE
                          ''indirecto''
                        END)::varchar as tipo,

                        (par.codigo ||''-''|| par.nombre_partida)::varchar as desc_partida,

                        cpa.estado_reg,
                        cpa.fecha_reg,
						cpa.id_usuario_reg,
						cpa.fecha_mod,
						cpa.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        cpa.id_gestion,
                        cpa.id_item

                from alm.titem_partida cpa
                inner join pre.tpartida par on par.id_partida = cpa.id_partida
                inner join segu.tusuario usu1 on usu1.id_usuario = cpa.id_usuario_reg
				left join segu.tusuario usu2 on usu2.id_usuario = cpa.id_usuario_mod

                where cpa.id_clasificacion in ('||v_id_clasifica||')
                and cpa.estado_reg != ''inactivo''
                and ';


            ELSE
            	--raise exception 'llega2 %',COALESCE(v_parametros.id_item,1);
                --recuperar los padres de la rama
                   WITH RECURSIVE clasificacion(id_clasificacion, id_clasificacion_padre) AS (

                      select  clas.id_clasificacion,
                              clas.id_clasificacion_fk
                      from alm.tclasificacion clas
                      join alm.titem item on item.id_clasificacion = clas.id_clasificacion
                      where clas.id_clasificacion = v_parametros.id_clasificacion
                      and item.id_item = v_parametros.id_item
                      and clas.estado_reg = 'activo'

                      UNION

                      SELECT clas2.id_clasificacion,
                             clas2.id_clasificacion_fk
                      FROM alm.tclasificacion clas2, clasificacion pc
                      --join alm.titem item on item.id_clasificacion = clas2.id_clasificacion
                      WHERE clas2.id_clasificacion = pc.id_clasificacion_padre
                      and clas2.estado_reg = 'activo'
                      --and item.id_item = v_parametros.id_item

                   )

                      SELECT  pxp.list(id_clasificacion::varchar)
                      into v_id_clasifica
                      FROM clasificacion;

                --raise exception 'llega2 %',v_id_clasifica;
                    v_id_clasifica = COALESCE(v_id_clasifica, '0');






              --Sentencia de la consulta
              v_consulta:='
                  select  cpa.id_item_partida,
                          cpa.id_clasificacion,
                          cpa.id_partida,

                          (CASE WHEN cpa.id_item = '||v_parametros.id_item||'
                          THEN
                             ''directo''
                          ELSE
                            ''indirecto''
                          END)::varchar as tipo,

                          (par.codigo ||''-''|| par.nombre_partida)::varchar as desc_partida,

                          cpa.estado_reg,
                          cpa.fecha_reg,
                          cpa.id_usuario_reg,
                          cpa.fecha_mod,
                          cpa.id_usuario_mod,
                          usu1.cuenta as usr_reg,
                          usu2.cuenta as usr_mod,
                          cpa.id_gestion,
                          cpa.id_item

                  from alm.titem_partida cpa
                  inner join pre.tpartida par on par.id_partida = cpa.id_partida
                  inner join segu.tusuario usu1 on usu1.id_usuario = cpa.id_usuario_reg
                  left join segu.tusuario usu2 on usu2.id_usuario = cpa.id_usuario_mod

                  where (cpa.id_clasificacion in ('||v_id_clasifica||')
                  or cpa.id_item = '||v_parametros.id_item||' )
                  and cpa.estado_reg != ''inactivo''
                  and ';


			END IF;

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

        end;
    /*********************************
     #TRANSACCION:  'SAL_CLASPAR_CONT'
     #DESCRIPCION:  Conteo de registros
     #AUTOR:        maylee.perez
     #FECHA:        25-11-2020
    ***********************************/

    elsif(p_transaccion='SAL_CLASPAR_CONT')then

        begin

        	--recueprar los padres de la rama
           WITH RECURSIVE clasificacion(id_clasificacion, id_clasificacion_padre) AS (

              select  clas.id_clasificacion,
                      clas.id_clasificacion_fk
              from alm.tclasificacion clas
              where clas.id_clasificacion = v_parametros.id_clasificacion
              and clas.estado_reg = 'activo'

              UNION

              SELECT
               clas2.id_clasificacion,
               clas2.id_clasificacion_fk
              FROM alm.tclasificacion clas2, clasificacion pc
              WHERE clas2.id_clasificacion = pc.id_clasificacion_padre  and clas2.estado_reg = 'activo'

           )

              SELECT  pxp.list(id_clasificacion::varchar)
              into v_id_clasifica
              FROM clasificacion;

            v_id_clasifica = COALESCE(v_id_clasifica, '0');


            --Sentencia de la consulta de conteo de registros
            v_consulta:='
            	select count(cpa.id_item_partida)

                from alm.titem_partida  cpa
                inner join pre.tpartida par on par.id_partida = cpa.id_partida
                inner join segu.tusuario usu1 on usu1.id_usuario = cpa.id_usuario_reg
				left join segu.tusuario usu2 on usu2.id_usuario = cpa.id_usuario_mod

                where ( cpa.id_clasificacion in ('||v_id_clasifica||')
                or cpa.id_item = '||v_parametros.id_item||' )
                and cpa.estado_reg = ''activo''
                and ';

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
