<?php
/**
 *@package pXP
 *@file gen-MODAlmacenUsuario.php
 *@author  (admin)
 *@date 13-11-2012 01:30:22
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODAlmacenUsuario extends MODbase {

    function __construct(CTParametro $pParam) {
        parent::__construct($pParam);
    }

    function listarAlmacenUsuario() {
        $this->procedimiento = 'alm.ft_almacen_usuario_sel';
        $this->transaccion = 'SAL_ALMUSR_SEL';
        $this->tipo_procedimiento = 'SEL';
        
        $this->setParametro('id_almacen', 'id_almacen', 'int4');
        
        $this->captura('id_almacen_usuario', 'int4');
        $this->captura('id_usuario', 'int4');
        $this->captura('cuenta', 'varchar');
        $this->captura('desc_person', 'text');
        $this->captura('id_almacen', 'int4');
        $this->captura('tipo', 'varchar');
        $this->captura('estado_reg', 'varchar');
        $this->captura('id_usuario_reg', 'int4');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('id_usuario_mod', 'int4');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');
        

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function insertarAlmacenUsuario() {
        $this->procedimiento = 'alm.ft_almacen_usuario_ime';
        $this->transaccion = 'SAL_ALMUSR_INS';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_usuario', 'id_usuario', 'int4');
        $this->setParametro('id_almacen', 'id_almacen', 'int4');
        $this->setParametro('tipo', 'tipo', 'varchar');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function modificarAlmacenUsuario() {
        $this->procedimiento = 'alm.ft_almacen_usuario_ime';
        $this->transaccion = 'SAL_ALMUSR_MOD';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_almacen_usuario', 'id_almacen_usuario', 'int4');
        $this->setParametro('id_usuario', 'id_usuario', 'int4');
        $this->setParametro('tipo', 'tipo', 'varchar');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function eliminarAlmacenUsuario() {
        $this->procedimiento = 'alm.ft_almacen_usuario_ime';
        $this->transaccion = 'SAL_ALMUSR_ELI';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_almacen_usuario', 'id_almacen_usuario', 'int4');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }
}
?>