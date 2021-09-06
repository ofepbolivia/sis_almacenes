<?php
/**
 *@package pXP
 *@file    MODAlmacenStock.php
 *@author  Gonzalo Sarmiento
 *@date    01-10-2012
 */

class MODAlmacenStock extends MODbase {

    function __construct(CTParametro $pParam) {
        parent::__construct($pParam);
    }

    function listarAlmacenItem() {
        $this->procedimiento = 'alm.ft_almacen_stock_sel';
        $this->transaccion = 'SAL_ALMITEM_SEL';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_almacen', 'id_almacen', 'integer');

        $this->captura('id_almacen_stock', 'int4');
        $this->captura('estado_reg', 'varchar');
        $this->captura('id_almacen', 'int4');
        $this->captura('id_item', 'int4');
        $this->captura('desc_item', 'varchar');
        $this->captura('cantidad_min', 'numeric');
        $this->captura('cantidad_alerta_amarilla', 'numeric');
        $this->captura('cantidad_alerta_roja', 'numeric');
        $this->captura('id_metodo_val', 'int4');
        $this->captura('codigo_metodo_val', 'varchar');
        $this->captura('codigo_unidad', 'varchar');
        $this->captura('usr_reg', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('usr_mod', 'varchar');
        $this->captura('fecha_mod', 'timestamp');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function insertarAlmacenItem() {
        $this->procedimiento = 'alm.ft_almacen_stock_ime';
        $this->transaccion = 'SAL_ALMITEM_INS';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('estado_reg', 'estado_reg', 'varchar');
        $this->setParametro('id_almacen', 'id_almacen', 'int4');
        $this->setParametro('id_item', 'id_item', 'int4');
        $this->setParametro('cantidad_min', 'cantidad_min', 'numeric');
        $this->setParametro('cantidad_alerta_amarilla', 'cantidad_alerta_amarilla', 'numeric');
        $this->setParametro('cantidad_alerta_roja', 'cantidad_alerta_roja', 'numeric');
        $this->setParametro('id_metodo_val', 'id_metodo_val', 'int4');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function modificarAlmacenItem() {
        $this->procedimiento = 'alm.ft_almacen_stock_ime';
        $this->transaccion = 'SAL_ALMITEM_MOD';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_almacen_stock', 'id_almacen_stock', 'int4');
        $this->setParametro('estado_reg', 'estado_reg', 'varchar');
        $this->setParametro('id_almacen', 'id_almacen', 'int4');
        $this->setParametro('id_item', 'id_item', 'int4');
        $this->setParametro('cantidad_min', 'cantidad_min', 'numeric');
        $this->setParametro('cantidad_alerta_amarilla', 'cantidad_alerta_amarilla', 'numeric');
        $this->setParametro('cantidad_alerta_roja', 'cantidad_alerta_roja', 'numeric');
        $this->setParametro('id_metodo_val', 'id_metodo_val', 'int4');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function eliminarAlmacenItem() {
        $this->procedimiento = 'alm.ft_almacen_stock_ime';
        $this->transaccion = 'SAL_ALMITEM_ELI';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_almacen_stock', 'id_almacen_stock', 'int4');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

}
?>