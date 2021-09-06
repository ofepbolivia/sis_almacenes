--------------- SQL ---------------

CREATE OR REPLACE FUNCTION alm.ft_almacen_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:        Almacenes
 FUNCION:        alm.ft_almacen_sel
 DESCRIPCION:    Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'alm.talmacen'
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

    v_consulta            varchar;
    v_parametros          record;
    v_nombre_funcion      text;
    v_resp                varchar;
              
BEGIN

    v_nombre_funcion = 'alm.ft_almacen_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************  
     #TRANSACCION:  'SAL_ALM_SEL'
     #DESCRIPCION:  Consulta de datos
     #AUTOR:        Gonzalo Sarmiento  
     #FECHA:        21-09-2012
    ***********************************/

    if(p_transaccion='SAL_ALM_SEL') then
	begin
    
    	v_consulta:='
        	select
                alm.id_almacen,
                alm.codigo,
                alm.nombre,
                alm.localizacion,
                alm.fecha_reg,
                alm.fecha_mod,
                usu1.cuenta as usr_reg,
                usu2.cuenta as usr_mod,
                alm.estado,
                alm.id_departamento,
                dpto.nombre as nombre_depto,
                meval.codigo as codigo_metodo_val,
                meval.id_metodo_val
            from alm.talmacen alm
            inner join segu.tusuario usu1 on usu1.id_usuario = alm.id_usuario_reg
            left join segu.tusuario usu2 on usu2.id_usuario = alm.id_usuario_mod
            inner join param.tdepto dpto on dpto.id_depto = alm.id_departamento
            left join alm.tmetodo_val meval on meval.id_metodo_val = alm.id_metodo_val';

        if p_administrador!=1 then
            /*v_consulta = v_consulta || ' inner join param.tdepto dp on dp.id_depto = alm.id_departamento
                        inner join param.tdepto_usuario dpu on dpu.id_depto = dp.id_depto
                        and dpu.id_usuario = '||p_id_usuario;*/
        end if;

        v_consulta = v_consulta || ' where alm.estado_reg = ''activo'' and ';

        --Definicion de la respuesta--
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        
        return v_consulta;
    end;

    /*********************************  
     #TRANSACCION:  'SAL_ALM_CONT'
     #DESCRIPCION:  Conteo de registros
     #AUTOR:        Gonzalo Sarmiento  
     #FECHA:        21-09-2012
    ***********************************/

    elsif(p_transaccion='SAL_ALM_CONT') then
	begin
    
        v_consulta:='
        	select count(alm.id_almacen)
            from alm.talmacen alm
            inner join segu.tusuario usu1 on usu1.id_usuario = alm.id_usuario_reg
            left join segu.tusuario usu2 on usu2.id_usuario = alm.id_usuario_mod
            inner join param.tdepto dpto on dpto.id_depto = alm.id_departamento
            left join alm.tmetodo_val meval on meval.id_metodo_val = alm.id_metodo_val';

        if p_administrador!=1 then
            /*v_consulta = v_consulta || ' inner join param.tdepto dp on dp.id_depto = alm.id_departamento
                        inner join param.tdepto_usuario dpu on dpu.id_depto = dp.id_depto
                        and dpu.id_usuario = '||p_id_usuario;*/
        end if;

        v_consulta = v_consulta || ' where alm.estado_reg = ''activo'' and ';
          
        --Definicion de la respuesta--
        v_consulta:=v_consulta||v_parametros.filtro;
        
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
