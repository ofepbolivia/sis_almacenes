<?php
/**
 *@package pXP
 *@file  MODClasificacionPartida.php
 *@author  maylee.perez
 *@date 25-11-2020 11:18:22
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODClasificacionPartida extends MODbase {

    function __construct(CTParametro $pParam) {
        parent::__construct($pParam);
    }

    function listarClasificacionPartida() {
        $this->procedimiento = 'alm.ft_clasificacion_partida_sel';
        $this->transaccion = 'SAL_CLASPAR_SEL';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_clasificacion','id_clasificacion','int4');
        $this->setParametro('id_item','id_item','int4');

        $this->captura('id_item_partida', 'integer');
        $this->captura('id_clasificacion', 'integer');
        $this->captura('id_partida', 'integer');
        $this->captura('tipo', 'varchar');
        $this->captura('desc_partida', 'varchar');

        $this->captura('estado_reg', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('id_usuario_reg', 'int4');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('id_usuario_mod', 'int4');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');

        $this->captura('id_gestion', 'int4');
        $this->captura('id_item', 'int4');


        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function insertarClasificacionPartida() {
        $this->procedimiento = 'alm.ft_clasificacion_partida_ime';
        $this->transaccion = 'SAL_CLASPAR_INS';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_clasificacion', 'id_clasificacion', 'integer');
        $this->setParametro('id_partida', 'id_partida', 'integer');
        $this->setParametro('tipo', 'tipo', 'varchar');
        $this->setParametro('id_item','id_item','int4');
        $this->setParametro('id_gestion','id_gestion','int4');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function modificarClasificacionPartida() {
        $this->procedimiento = 'alm.ft_clasificacion_partida_ime';
        $this->transaccion = 'SAL_CLASPAR_MOD';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_item_partida', 'id_item_partida', 'integer');
        $this->setParametro('id_clasificacion', 'id_clasificacion', 'integer');
        $this->setParametro('id_partida', 'id_partida', 'integer');
        $this->setParametro('tipo', 'tipo', 'varchar');
        $this->setParametro('id_gestion','id_gestion','int4');
        $this->setParametro('id_item','id_item','int4');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function eliminarClasificacionPartida() {
        $this->procedimiento = 'alm.ft_clasificacion_partida_ime';
        $this->transaccion = 'SAL_CLASPAR_ELI';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_item_partida', 'id_item_partida', 'int4');
        //$this->setParametro('id_item', 'id_item', 'int4');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

}
?>