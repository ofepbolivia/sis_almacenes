<?php
/**
 *@package pXP
 *@file gen-MODPreingresoDet.php
 *@author  (admin)
 *@date 07-10-2013 17:46:04
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODPreingresoDet extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarPreingresoDet(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='alm.ft_preingreso_det_sel';
        $this->transaccion='SAL_PREDET_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_preingreso_det','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('id_preingreso','int4');
        $this->captura('id_cotizacion_det','int4');
        $this->captura('id_item','int4');
        $this->captura('id_almacen','int4');
        $this->captura('cantidad_det','numeric');
        $this->captura('precio_compra','numeric');
        $this->captura('id_depto','int4');
        $this->captura('id_clasificacion','int4');
        $this->captura('sw_generar','varchar');
        $this->captura('observaciones','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');

        $this->captura('desc_almacen','text');
        $this->captura('desc_depto','text');
        $this->captura('desc_item','text');
        $this->captura('desc_clasificacion','text');
        $this->captura('desc_ingas','varchar');
        $this->captura('estado','varchar');
        $this->captura('tipo','varchar');

        $this->captura('nombre','varchar');
        $this->captura('descripcion','text');
        $this->captura('precio_compra_87','numeric');
        $this->captura('id_lugar','integer');
        $this->captura('nombre_lugar','varchar');
        $this->captura('ubicacion','varchar');
        $this->captura('c31','varchar');
        $this->captura('fecha_conformidad','date');
        $this->captura('fecha_compra','date');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarPreingresoDet(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='alm.ft_preingreso_det_ime';
        $this->transaccion='SAL_PREDET_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('id_preingreso','id_preingreso','int4');
        $this->setParametro('id_cotizacion_det','id_cotizacion_det','int4');
        $this->setParametro('id_item','id_item','int4');
        $this->setParametro('id_almacen','id_almacen','int4');
        $this->setParametro('cantidad_det','cantidad_det','numeric');
        $this->setParametro('precio_compra','precio_compra','numeric');
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('id_clasificacion','id_clasificacion','int4');
        $this->setParametro('sw_generar','sw_generar','varchar');
        $this->setParametro('observaciones','observaciones','varchar');

        $this->setParametro('nombre','varchar','varchar');
        $this->setParametro('descripcion','text','text');
        $this->setParametro('precio_compra_87','numeric','numeric');
        $this->setParametro('id_lugar','integer','integer');
        $this->setParametro('ubicacion','varchar','varchar');
        $this->setParametro('c31','c31','varchar');
        $this->setParametro('fecha_conformidad','fecha_conformidad','date');
        $this->setParametro('fecha_compra','fecha_compra','date');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarPreingresoDet(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='alm.ft_preingreso_det_ime';
        $this->transaccion='SAL_PREDET_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_preingreso_det','id_preingreso_det','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('id_preingreso','id_preingreso','int4');
        $this->setParametro('id_cotizacion_det','id_cotizacion_det','int4');
        $this->setParametro('id_item','id_item','int4');
        $this->setParametro('id_almacen','id_almacen','int4');
        $this->setParametro('cantidad_det','cantidad_det','numeric');
        $this->setParametro('precio_compra','precio_compra','numeric');
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('id_clasificacion','id_clasificacion','int4');
        $this->setParametro('sw_generar','sw_generar','varchar');
        $this->setParametro('observaciones','observaciones','varchar');

        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('descripcion','descripcion','text');
        $this->setParametro('precio_compra_87','precio_compra_87','numeric');
        $this->setParametro('id_lugar','id_lugar','integer');
        $this->setParametro('ubicacion','ubicacion','varchar');
        $this->setParametro('c31','c31','varchar');
        $this->setParametro('fecha_conformidad','fecha_conformidad','date');
        $this->setParametro('fecha_compra','fecha_compra','date');
        //-------------------AUMENTANDO LOS CAMPOS------------------------------//
        $this->setParametro('id_unidad_medida','id_unidad_medida','int4');
        $this->setParametro('vida_util_original','vida_util_original','int4');
        $this->setParametro('nro_serie','nro_serie','varchar');
        $this->setParametro('marca','marca','varchar');
        $this->setParametro('id_cat_estado_fun','id_cat_estado_fun','int4');
        $this->setParametro('id_deposito','id_deposito','int4');
        $this->setParametro('id_oficina','id_oficina','int4');
        $this->setParametro('id_proveedor','id_proveedor','int4');
        $this->setParametro('documento','documento','varchar');
        $this->setParametro('id_cat_estado_compra','id_cat_estado_compra','int4');
        $this->setParametro('fecha_cbte_asociado','fecha_cbte_asociado','date');
        $this->setParametro('monto_compra','monto_compra','numeric');
        $this->setParametro('id_proyecto','id_proyecto','int4');
        $this->setParametro('tramite_compra','tramite_compra','varchar');
        $this->setParametro('subtipo','subtipo','varchar');
        $this->setParametro('movimiento','movimiento','varchar');

        $this->setParametro('fecha_inicio','fecha_inicio','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        //------------------------------------------------------------------------


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        //var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarPreingresoDet(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='alm.ft_preingreso_det_ime';
        $this->transaccion='SAL_PREDET_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_preingreso_det','id_preingreso_det','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function preparaPreingreso(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='alm.ft_preingreso_det_ime';
        $this->transaccion='SAL_PREPPRE_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_preingreso_det','id_preingreso_det','int4');
        $this->setParametro('id_preingreso','id_preingreso','int4');
        $this->setParametro('desc_funcionario1','desc_funcionario1','varchar');
        $this->setParametro('desc_proveedor','desc_proveedor','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarPreingresoDetPreparacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='alm.ft_preingreso_det_ime';
        $this->transaccion='SAL_PREPPRE_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('id_preingreso','id_preingreso','int4');
        $this->setParametro('id_cotizacion_det','id_cotizacion_det','int4');
        $this->setParametro('id_item','id_item','int4');
        $this->setParametro('id_almacen','id_almacen','int4');
        $this->setParametro('cantidad_det','cantidad_det','numeric');
        $this->setParametro('precio_compra','precio_compra','numeric');
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('id_clasificacion','id_clasificacion','int4');
        $this->setParametro('sw_generar','sw_generar','varchar');
        $this->setParametro('observaciones','observaciones','varchar');

        $this->setParametro('fecha_inicio','fecha_inicio','date');
        $this->setParametro('fecha_fin','fecha_fin','date');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarPreingresoDetPreparacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='alm.ft_preingreso_det_ime';
        $this->transaccion='SAL_PREDETPRE_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_preingreso_det','id_preingreso_det','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function preparaPreingresoAll(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='alm.ft_preingreso_det_ime';
        $this->transaccion='SAL_PREPPREALL_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_preingreso','id_preingreso','int4');
        $this->setParametro('desc_funcionario1','desc_funcionario1','varchar');
        $this->setParametro('desc_proveedor','desc_proveedor','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function quitaPreingresoAll(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='alm.ft_preingreso_det_ime';
        $this->transaccion='SAL_QUITAPREALL_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_preingreso','id_preingreso','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarPreingresoDetV2(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='alm.ft_preingreso_det_sel';
        $this->transaccion='SAL_PREDETV2_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_preingreso_det','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('id_preingreso','int4');
        $this->captura('id_cotizacion_det','int4');
        $this->captura('id_item','int4');
        $this->captura('id_almacen','int4');
        $this->captura('cantidad_det','numeric');
        $this->captura('precio_compra','numeric');
        $this->captura('id_depto','int4');
        $this->captura('id_clasificacion','int4');
        $this->captura('sw_generar','varchar');
        $this->captura('observaciones','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');

        $this->captura('desc_almacen','text');
        $this->captura('desc_depto','text');
        $this->captura('desc_item','text');
        $this->captura('desc_clasificacion','text');
        $this->captura('desc_ingas','varchar');
        $this->captura('estado','varchar');
        $this->captura('tipo','varchar');

        $this->captura('nombre','varchar');
        $this->captura('descripcion','text');
        $this->captura('precio_compra_87','numeric');
        $this->captura('id_lugar','integer');
        $this->captura('nombre_lugar','varchar');
        $this->captura('ubicacion','varchar');
        $this->captura('c31','varchar');
        $this->captura('fecha_conformidad','date');
        $this->captura('fecha_compra','date');
        //--------------------------CAMPOS AUMENTADOS-----------------------------//
        $this->captura('id_unidad_medida','int4');
        $this->captura('codigo_unmed','varchar');
        $this->captura('descripcion_unmed','varchar');

        $this->captura('vida_util_original','int4');
        $this->captura('vida_util_original_anios','numeric');
        $this->captura('nro_serie','varchar');
        $this->captura('marca','varchar');
        $this->captura('id_cat_estado_fun','int4');
        $this->captura('estado_fun','varchar');
        $this->captura('id_deposito','int4');
        $this->captura('deposito','varchar');
        $this->captura('id_oficina','int4');
        $this->captura('oficina','text');
        $this->captura('id_proveedor','int4');
        $this->captura('desc_proveedor','varchar');
        $this->captura('documento','varchar');
        $this->captura('id_cat_estado_compra','int4');
        $this->captura('estado_compra','varchar');
        $this->captura('fecha_cbte_asociado','date');
        $this->captura('monto_compra','numeric');
        $this->captura('id_proyecto','int4');
        $this->captura('desc_proyecto','varchar');
        $this->captura('tramite_compra','varchar');
        $this->captura('nombre_clasi','varchar');
        $this->captura('subtipo','varchar');
        $this->captura('movimiento','varchar');

        $this->captura('fecha_inicio','date');
        $this->captura('fecha_fin','date');
        //-------------------------------------------------------------------------

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>
