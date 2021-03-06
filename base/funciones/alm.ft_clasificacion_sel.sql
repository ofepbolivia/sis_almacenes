CREATE OR REPLACE FUNCTION alm.ft_clasificacion_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:        Almacenes
 FUNCION:         alm.ft_clasificacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'alm.tclasificacion'
 AUTOR:         Gonzalo Sarmiento
 FECHA:            24-09-2012
 COMENTARIOS:   
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:   
 AUTOR:           
 FECHA:       
***************************************************************************/

DECLARE

    v_consulta            varchar;
    v_parametros          record;
    v_nombre_funcion       text;
    v_resp                varchar;
    v_where varchar;
    v_join varchar;
               
BEGIN

    v_nombre_funcion = 'alm.ft_clasificacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************   
     #TRANSACCION:  'ALM_CLA_SEL'
     #DESCRIPCION:    Consulta de datos
     #AUTOR:            Gonzalo Sarmiento
     #FECHA:            24-09-2012
    ***********************************/

    if(p_transaccion='ALM_CLA_SEL')then
                    
        begin
            --Sentencia de la consulta
            v_consulta:='
            	select
                    cla.id_clasificacion,
                    cla.nombre,
                    cla.codigo_largo
                from alm.tclasificacion cla
                where ';
           
            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;
                       
        end;

    /*********************************
     #TRANSACCION:  'ALM_ITEMRTRAB_SEL'
     #DESCRIPCION:    Consulta de Items de Ropa de Trabajo
     #AUTOR:            Gonzalo Sarmiento
     #FECHA:            23-05-2016
    ***********************************/

    elsif(p_transaccion='ALM_ITEMRTRAB_SEL')then

        begin
            --Sentencia de la consulta
            v_consulta:='
            	select it.id_clasificacion as id, it.codigo_largo as codigo,it.nombre,it.descripcion::varchar,
                	(case when  exists (select 1
                    					from alm.tclasificacion
                                        where nombre like ''Varon'' and codigo_largo like it.codigo_largo || ''.%'') or exists (select 1
                    					from alm.titem
                                        where nombre like ''Varon'' and codigo like it.codigo_largo || ''.%'') then
                    	''si''
                    else
                    	''no''
                    end)::varchar as tiene_genero,
                    (case when exists (select 1
                    					from alm.titem
                                        where (nombre like ''L'' or nombre like ''17'' or nombre like ''38'' or nombre like ''39'') and codigo like it.codigo_largo || ''.%'') then
                    	''si''
                    else
                    	''no''
                    end)::varchar as tiene_tamano
                from alm.tclasificacion it
                where (it.codigo_largo like ''3.4.1.%'' or it.codigo_largo like ''3.4.2.%'')
                    and it.estado_reg=''activo'' and array_length(string_to_array(it.codigo_largo,''.''),1)=4 and '
            	|| v_parametros.filtro || '

                union all

                select it.id_item as id, it.codigo,it.nombre,it.descripcion::varchar,''no''::varchar as tiene_genero,''no''::varchar as tiene_tamano
                from alm.titem it
                where (it.codigo like ''3.4.1.%'' or it.codigo like ''3.4.2.%'')
                    and it.estado_reg=''activo'' and array_length(string_to_array(it.codigo,''.''),1)=4 and '
            	|| v_parametros.filtro;

            --Definicion de la respuesta
            --v_consulta:=v_consulta||v_parametros.filtro;
            raise notice '%',v_consulta;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'ALM_ITEMRTMED_SEL'
     #DESCRIPCION:    Consulta de Medidas de los Items de Ropa de Trabajo
     #AUTOR:            Gonzalo Sarmiento
     #FECHA:            11-11-2016
    ***********************************/

    elsif(p_transaccion='ALM_ITEMRTMED_SEL')then

        begin
            --Sentencia de la consulta
           v_consulta:='
            	select it.id_item as id, it.codigo,it.nombre
                from alm.titem it
                where (it.codigo like ''3.4.1.%'' or it.codigo like ''3.4.2.%'')
                    and it.estado_reg=''activo'' and ';

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            raise notice '%',v_consulta;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

        end;

     /*********************************
     #TRANSACCION:  'ALM_CLA_ARB_SEL'
     #DESCRIPCION:    Consulta de datos
     #AUTOR:            Gonzalo Sarmiento
     #FECHA:            24-09-2012
    ***********************************/

    elseif(p_transaccion='ALM_CLA_ARB_SEL')then
		begin
			if(v_parametros.id_padre = '%') then
            	v_where := ' cla.id_clasificacion_fk is NULL';
            else
            	v_where := ' cla.id_clasificacion_fk = '||v_parametros.id_padre;
            end if;

            --Sentencia de la consulta
            v_consulta:='
            	select
                    cla.id_clasificacion,
                    cla.id_clasificacion_fk,
                    cla.codigo,
                    cla.nombre,
                    cla.descripcion,
                    case
                        when (cla.id_clasificacion_fk is null and cla.estado != ''bloqueado'') then
                        	''raiz''::varchar
                        when (cla.id_clasificacion_fk is null and cla.estado = ''bloqueado'') then
                        	''raiz_bloqueado''::varchar
                        when (cla.id_clasificacion_fk is not null and cla.estado = ''bloqueado'') then
                        	''hijo_bloqueado''::varchar
                        ELSE
                            ''hijo''::varchar
                    END as tipo_nodo,
                    cla.codigo_largo,
                    cla.estado,
                    ''false''::varchar as checked
                from alm.tclasificacion cla
                where  '||v_where|| '
                ORDER BY
                	case
                        when substring(cla.codigo from ''^[0-9]+$'') is null then 9999
                        else cast(cla.codigo as integer)
                    end,
                    cla.codigo ';
            raise notice '%',v_consulta;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'ALM_ITEMRTRAB_CONT'
     #DESCRIPCION:    Conteo de Items de Ropa de Trabajo
     #AUTOR:            Gonzalo Sarmiento
     #FECHA:            23-05-2016
    ***********************************/

    elsif(p_transaccion='ALM_ITEMRTRAB_CONT')then

        begin
            --Sentencia de la consulta
            v_consulta:=' select count(*) from (
            	select it.codigo_largo as codigo,it.nombre,it.descripcion::varchar
                from alm.tclasificacion it
                where (it.codigo_largo like ''3.4.1.%'' or it.codigo_largo like ''3.4.2.%'')
                    and it.estado_reg=''activo'' and array_length(string_to_array(it.codigo_largo,''.''),1)=4 and '
            	|| v_parametros.filtro || '

                union all

                select it.codigo,it.nombre,it.descripcion::varchar
                from alm.titem it
                where (it.codigo like ''3.4.1.%'' or it.codigo like ''3.4.2.%'')
                    and it.estado_reg=''activo'' and array_length(string_to_array(it.codigo,''.''),1)=4 and '
            	|| v_parametros.filtro || ') tabla';

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'ALM_ITEMRTMED_CONT'
     #DESCRIPCION:    Consulta de Medidas de los Items de Ropa de Trabajo
     #AUTOR:            Gonzalo Sarmiento
     #FECHA:            11-11-2016
    ***********************************/

    elsif(p_transaccion='ALM_ITEMRTMED_CONT')then

        begin
            --Sentencia de la consulta
            v_consulta:='
            	select count(it.id_item)
                from alm.titem it
                where (it.codigo like ''3.4.1.%'' or it.codigo like ''3.4.2.%'')
                    and it.estado_reg=''activo'' and '
            	|| v_parametros.filtro || ' group by it.codigo';

            --Definicion de la respuesta
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

        end;


    /*********************************   
     #TRANSACCION:  'ALM_CLA_CONT'
     #DESCRIPCION:    Conteo de registros
     #AUTOR:            Gonzalo Sarmiento
     #FECHA:            24-09-2012
    ***********************************/

    elsif(p_transaccion='ALM_CLA_CONT')then

        begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(id_clasificacion)
                        from alm.tclasificacion cla
                        where ';
           
            --Definicion de la respuesta           
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;
    /*********************************
 	#TRANSACCION:  'ALM_GET_ARB_CLA_SEL'
 	#DESCRIPCION:	Arbol Clasificacion Materiales
 	#AUTOR:		franklin.espinoza
 	#FECHA:		18-05-2018 20:55:18
	***********************************/
	elsif(p_transaccion='ALM_GET_ARB_CLA_SEL')then

    	begin

        	create temp table padres (
                id_clasificacion int4,
              	id_clasificacion_fk int4,
              	codigo varchar,
              	nombre varchar,
                tipo varchar
            ) on commit drop;

            create temp table hijos (
                id_clasificacion int4,
              	id_clasificacion_fk int4,
              	codigo varchar,
              	nombre varchar,
                tipo varchar
            ) on commit drop;


        	v_consulta = 'with recursive niveles (nivel, id_clasificacion, id_clasificacion_fk, codigo, nombre, camino, codigo_largo) as
				(
                    select
                    0,
                    tcc.id_clasificacion,
                    tcc.id_clasificacion_fk,
                    tcc.codigo,
                    tcc.nombre,
                    tcc.codigo::TEXT as camino,
                    tcc.codigo_largo
                    from alm.tclasificacion tcc
                    where tcc.codigo_largo = '''||v_parametros.codigo||'''::varchar

                    union all

                    select
                    padre.nivel+1,
                    hijo.id_clasificacion,
                    hijo.id_clasificacion_fk,
                    hijo.codigo,
                    hijo.nombre,
                    padre.camino || ''.'' || hijo.codigo::TEXT,
                    hijo.codigo_largo
                    from alm.tclasificacion hijo,  niveles padre
                    where hijo.id_clasificacion_fk  = padre.id_clasificacion

				)
              insert into padres
              select
              niv.id_clasificacion,
              niv.id_clasificacion_fk,
              niv.codigo_largo as codigo,
              niv.nombre,
              ''padre''::varchar as tipo
              from niveles  niv
              ';

              execute(v_consulta);

            v_consulta = '
             			  insert into hijos
                          select
                          	tit.id_item as id_clasificacion,
              				tit.id_clasificacion as id_clasificacion_fk,
                            tit.codigo,
                            tit.nombre,
                            ''hijo''::varchar as tipo
                          from padres  niv
                          inner join alm.titem tit on tit.id_clasificacion = niv.id_clasificacion
                          where tit.estado_reg = ''activo''
                          ';
            execute(v_consulta);

            v_consulta = ' select
              				id_clasificacion,
                            id_clasificacion_fk,
                            codigo,
                            nombre,
                            tipo
                           from padres


                           union all

                           select
              				id_clasificacion,
                            id_clasificacion_fk,
                            codigo,
                            nombre,
                            tipo
                           from hijos

                           ';
        	raise notice 'v_consulta: %', v_consulta;
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