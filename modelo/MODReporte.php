<?php
/**
 *@package pXP
 *@file    MODMovimiento.php
 *@author  Ariel Ayaviri Omonte
 *@date    17-04-2013
 *@description: DAO para los reportes del sistema de almacenes
 */

class MODReporte extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function listarItemsPorAlmacenFecha()
    {
        $this->procedimiento = 'alm.ft_reporte_sel';
        $this->transaccion = 'SAL_REPEXIST_SEL';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_almacen', 'id_almacen', 'integer');
        //$this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_hasta', 'fecha_hasta', 'date');
        $this->setParametro('all_items', 'all_items', 'varchar');
        $this->setParametro('id_items', 'id_items', 'varchar');
        $this->setParametro('saldo_cero', 'saldo_cero', 'varchar');
        $this->setParametro('alertas', 'alertas', 'varchar');
        $this->setParametro('id_clasificacion', 'id_clasificacion', 'varchar');
        $this->setParametro('porcentaje', 'porcentaje', 'varchar');
        $this->setParametro('formato', 'formato', 'varchar');

        $this->captura('id_item', 'integer');
        $this->captura('codigo', 'varchar');
        $this->captura('nombre', 'varchar');
        $this->captura('unidad_medida', 'varchar');
        $this->captura('clasificacion', 'varchar');
        $this->captura('cantidad', 'numeric');
        $this->captura('costo', 'numeric');
        $this->captura('cantidad_min', 'numeric');
        $this->captura('cantidad_alerta_amarilla', 'numeric');
        $this->captura('cantidad_alerta_roja', 'numeric');

        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function listarKardexItem()
    {
        $this->procedimiento = 'alm.ft_rep_kardex_item_sel';
        $this->transaccion = 'SAL_RKARIT_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->tipo_retorno = 'record';
        $this->count = false;

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_item', 'id_item', 'integer');
        $this->setParametro('id_almacen', 'id_almacen', 'varchar');
        $this->setParametro('all_almacen', 'all_almacen', 'varchar');

        //$this->captura('id', 'integer');
        $this->captura('fecha', 'timestamp');
        $this->captura('nro_mov', 'varchar');
        $this->captura('almacen', 'varchar');
        $this->captura('motivo', 'varchar');
        $this->captura('ingreso', 'numeric');
        $this->captura('salida', 'numeric');
        $this->captura('saldo', 'numeric');
        $this->captura('costo_unitario', 'numeric');
        $this->captura('ingreso_val', 'numeric');
        $this->captura('salida_val', 'numeric');
        $this->captura('saldo_val', 'numeric');
        $this->captura('id_movimiento', 'integer');
        $this->captura('id_movimiento_det_valorado', 'integer');
        $this->captura('id_mov_det_val_origen', 'integer');
        $this->captura('nro_tramite', 'varchar');
        $this->captura('fecha_salida', 'date');

        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function listarItemEntRec()
    {
        $this->procedimiento = 'alm.ft_reporte_sel';
        $this->transaccion = 'SAL_REITEN_SEL';
        $this->tipo_procedimiento = 'SEL';
        //$this->count=false;

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');

        $this->setParametro('tipo_mov', 'tipo_mov', 'varchar');
        $this->setParametro('tipo_sol', 'tipo_sol', 'varchar');
        $this->setParametro('id_funcionario', 'id_funcionario', 'varchar');
        $this->setParametro('id_proveedor', 'id_proveedor', 'integer');
        $this->setParametro('all_items', 'all_items', 'varchar');
        $this->setParametro('id_items', 'id_items', 'varchar');
        $this->setParametro('id_clasificacion', 'id_clasificacion', 'varchar');
        $this->setParametro('all_alm', 'all_alm', 'varchar');
        $this->setParametro('id_almacen', 'id_almacen', 'varchar');
        $this->setParametro('all_funcionario', 'all_funcionario', 'varchar');
        $this->setParametro('id_estructura_uo', 'id_estructura_uo', 'varchar');

        $this->captura('id_movimiento_det_valorado', 'integer');
        $this->captura('fecha_mov', 'date');
        $this->captura('codigo', 'varchar');
        $this->captura('nombre', 'varchar');
        $this->captura('cantidad', 'numeric');
        $this->captura('costo_unitario', 'numeric');
        $this->captura('costo_total', 'numeric');
        $this->captura('desc_funcionario1', 'text');
        $this->captura('desc_proveedor', 'varchar');
        $this->captura('mov_codigo', 'varchar');
        $this->captura('tipo_nombre', 'varchar');
        $this->captura('tipo', 'varchar');
        $this->captura('desc_almacen', 'varchar');

        $this->captura('desc_centro_costo', 'varchar');
        $this->captura('desc_partida', 'varchar');

        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function listarKardexItemIngSal()
    {
        $this->procedimiento = 'alm.ft_reporte_sel';
        $this->transaccion = 'SAL_ITEM_CU_SEL';
        $this->tipo_procedimiento = 'SEL';
        //$this->tipo_retorno='record';
        $this->count = false;

        $this->setParametro('id_almacen', 'id_almacen', 'integer');
        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_hasta', 'fecha_hasta', 'date');
        $this->setParametro('all_items', 'all_items', 'varchar');
        $this->setParametro('id_items', 'id_items', 'varchar');
        $this->setParametro('saldo_cero', 'saldo_cero', 'varchar');
        $this->setParametro('alertas', 'alertas', 'varchar');
        $this->setParametro('id_clasificacion', 'id_clasificacion', 'varchar');
        $this->setParametro('porcentaje', 'porcentaje', 'varchar');

        //$this->captura('id', 'integer');
        $this->captura('fecha', 'timestamp');
        $this->captura('nro_mov', 'varchar');
        $this->captura('almacen', 'varchar');
        $this->captura('motivo', 'varchar');
        $this->captura('ingreso', 'numeric');
        $this->captura('salida', 'numeric');
        $this->captura('saldo', 'numeric');
        $this->captura('costo_unitario', 'numeric');
        $this->captura('ingreso_val', 'numeric');
        $this->captura('salida_val', 'numeric');
        $this->captura('saldo_val', 'numeric');
        $this->captura('id_movimiento', 'integer');
        $this->captura('id_movimiento_det_valorado', 'integer');
        $this->captura('id_mov_det_val_origen', 'integer');
        $this->captura('nro_tramite', 'varchar');
        $this->captura('id_item', 'integer');
        $this->captura('codigo', 'varchar');
        $this->captura('nombre', 'varchar');
        $this->captura('unidad_medida', 'varchar');
        $this->captura('clasificacion', 'varchar');
        $this->captura('nombre_almacen', 'varchar');
        $this->captura('tipo_movimiento', 'varchar');
        $this->captura('fecha_ini', 'varchar');
        $this->captura('fecha_hasta', 'varchar');
        $this->captura('grupo_clasif', 'varchar'); //fRnk: añadido para cabecera padre de reporte

        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function listarKardexItemDesglosado()
    {
        $this->procedimiento = 'alm.ft_reporte_sel';
        $this->transaccion = 'SAL_ITEMS_DESG_SEL';
        $this->tipo_procedimiento = 'SEL';
        //$this->tipo_retorno='record';
        $this->count = false;

        $this->setParametro('id_almacen', 'id_almacen', 'integer');
        //$this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_hasta', 'fecha_hasta', 'date');
        $this->setParametro('all_items', 'all_items', 'varchar');
        $this->setParametro('id_items', 'id_items', 'varchar');
        $this->setParametro('saldo_cero', 'saldo_cero', 'varchar');
        $this->setParametro('alertas', 'alertas', 'varchar');
        $this->setParametro('id_clasificacion', 'id_clasificacion', 'varchar');
        $this->setParametro('porcentaje', 'porcentaje', 'varchar');

        //$this->captura('id', 'integer');
        //$this->captura('fecha', 'timestamp');
        $this->captura('nro_tramite', 'varchar');
        $this->captura('tipo_movimiento', 'varchar');
        $this->captura('cantidad', 'numeric');
        $this->captura('costo_unitario', 'numeric');
        $this->captura('fecha_mov', 'date');
        $this->captura('fecha_salida', 'date');
        $this->captura('saldo_actual', 'numeric');
        $this->captura('id_item', 'integer');
        $this->captura('codigo', 'varchar');
        $this->captura('nombre', 'varchar');
        $this->captura('unidad_medida', 'varchar');
        $this->captura('clasificacion', 'varchar');
        $this->captura('nombre_almacen', 'varchar');

        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    //{'develop':'franklin.espinoza', 'date':'26/2/2020'}
    function listarCantidadesClasificacion()
    {
        $this->procedimiento = 'alm.ft_reporte_sel';
        //$this->transaccion = 'SAL_MIN_EXIST_SEL';
        $this->transaccion = 'SAL_EXISCANTVAL_SEL';
        $this->tipo_procedimiento = 'SEL';
        //$this->tipo_retorno='record';
        $this->count = false;

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_hasta', 'fecha_hasta', 'date');
        /*$this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_clasificacion', 'id_clasificacion', 'varchar');
        $this->setParametro('id_almacen', 'id_almacen', 'varchar');
        $this->setParametro('all_alm', 'all_alm', 'varchar');*/

        $this->setParametro('id_almacen', 'id_almacen', 'integer');
        //$this->setParametro('fecha_hasta', 'fecha_hasta', 'date');
        $this->setParametro('all_items', 'all_items', 'varchar');
        $this->setParametro('id_clasificacion', 'id_clasificacion', 'varchar');

        $this->setParametro('id_items', 'id_items', 'varchar');
        $this->setParametro('saldo_cero', 'saldo_cero', 'varchar');
        $this->setParametro('alertas', 'alertas', 'varchar');
        $this->setParametro('porcentaje', 'porcentaje', 'varchar');
        $this->setParametro('formato', 'formato', 'varchar');

        /* $this->captura('codigo', 'varchar');
         $this->captura('nombre', 'varchar');
         $this->captura('saldo_ini', 'numeric');
         $this->captura('ingreso', 'numeric');
         $this->captura('salida', 'numeric');
         $this->captura('saldo_fin', 'numeric');
         $this->captura('descripcion', 'varchar');
         $this->captura('tamano', 'integer');
         $this->captura('id_clasificacion_fk', 'integer');*/

        $this->captura('codigo', 'varchar');
        $this->captura('detalle', 'varchar');
        $this->captura('unidad_medida', 'varchar');
        $this->captura('cantidad_saldo_inicial', 'numeric');
        $this->captura('valor_saldo_inicial ', 'numeric');
        $this->captura('cantidad_ingreso', 'numeric');
        $this->captura('valor_ingreso', 'numeric');
        $this->captura('cantidad_egreso', 'numeric');
        $this->captura('valor_egreso', 'numeric');
        $this->captura('cantidad_saldo_final', 'numeric');
        $this->captura('valor_saldo_final', 'numeric');
        $this->captura('fuente', 'varchar');
        $this->captura('grupo', 'varchar');
        $this->captura('nivel', 'varchar');
        $this->captura('entidad', 'varchar');
        $this->captura('nom_almacen', 'varchar');
        $this->captura('fecha_ini', 'varchar');
        $this->captura('fecha_hasta', 'varchar');
        $this->captura('fuente_gral', 'varchar');

        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        //var_dump($this->respuesta);exit();
        return $this->respuesta;
    }

    function listarTotalCantidadesClasificacion()
    {
        $this->procedimiento = 'alm.ft_reporte_existencias';
        //$this->transaccion = 'SAL_EXISCANTVAL_SEL';
        $this->tipo_procedimiento = 'OTRO';
        $this->count = false;

        $fechaIni = $this->objParam->getParametro('fecha_ini');
        $fechaFin = $this->objParam->getParametro('fecha_hasta');
        $idClasificacionLista = $this->objParam->getParametro('id_clasificacion');
        $idalmacen = $this->objParam->getParametro('id_almacen');
        if ($this->objParam->getParametro('all_items') == 'si') {
            $idalmacen = '';
        }
        $idUsuario = 0;
        if ($this->null($this->id_usuario)) {
            $idUsuario = 0;
        } else {
            $idUsuario = $this->id_usuario;
        }
        
        $this->consulta = "select * from alm.ft_reporte_existencias(array[" . $idClasificacionLista . "]::integer[], 
        to_date('" . $fechaIni . "','DD/MM/YYYY'), to_date('" . $fechaFin . "','DD/MM/YYYY'),
        array[" . $idalmacen . "]::integer[]," . $idUsuario . ") 
        as (codigo  varchar, nombre varchar, vr_unidad_medida varchar, vr_cantidad_saldo_inicial numeric, saldo_ini numeric,
         vr_cantidad_ingreso numeric, ingreso numeric, vr_cantidad_egreso numeric, salida numeric, vr_cantidad_saldo_final numeric,
         saldo_fin numeric, vr_fuente varchar, vr_grupo varchar, vr_nivel varchar,
        vr_entidad varchar, vr_nom_almacen varchar, vr_fecha_ini date, vr_fecha_hasta date, vr_fuentes_gral varchar, id_almacen integer)";
        $this->ejecutarConsultaSel();
        return $this->respuesta;
    }
}
?>