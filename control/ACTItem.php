<?php
/**
 *@package pXP
 *@file gen-ACTItem.php
 *@author  (admin)
 *@date 17-08-2012 11:18:22
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTItem extends ACTbase {

    function listarItem() {
        $this->objParam->defecto('ordenacion', 'id_item');

        $this->objParam->defecto('dir_ordenacion', 'asc');
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODItem', 'listarItem');
        } else {
            if($this->objParam->getParametro('id_clasificacion') == 'null') {
                $this->objParam->addFiltro(" item.id_clasificacion is null");
            }
            elseif ($this->objParam->getParametro('id_clasificacion') != null) {
                $this->objParam->addFiltro(" item.id_clasificacion = ".$this->objParam->getParametro('id_clasificacion'));
            } 
            elseif($this->objParam->getParametro('id_item') != null) {
                $this->objParam->addFiltro(" item.id_item = ".$this->objParam->getParametro('id_item'));
            }
            $this->objFunc = $this->create('MODItem');
            $this->res = $this->objFunc->listarItem();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

	function listarSaldoXItem() {
        $this->objParam->defecto('ordenacion', 'id_item');

        $this->objParam->defecto('dir_ordenacion', 'asc');
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODItem', 'listarSaldoXItem');
        } else {            
            $this->objFunc = $this->create('MODItem');
            $this->res = $this->objFunc->listarSaldoXItem();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	
	function listarSaldosXItems() {
        $this->objParam->defecto('ordenacion', 'id_item');

        $this->objParam->defecto('dir_ordenacion', 'asc');
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODItem', 'listarSaldosXItems');
        } else {            
            $this->objFunc = $this->create('MODItem');
            $this->res = $this->objFunc->listarSaldosXItems();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarItemNotBase() {
        $this->objParam->defecto('ordenacion', 'id_item');
        $this->objParam->defecto('dir_ordenacion', 'asc');
        if($this->objParam->getParametro('id_sucursal') != '') {
                $this->objParam->addFiltro(" (select s.clasificaciones_para_venta 
                                                from vef.tsucursal s 
                                                where s.id_sucursal = " . $this->objParam->getParametro('id_sucursal') . " ) && string_to_array(alm.f_get_id_clasificaciones(item.id_clasificacion,''padres''), '','')::integer[] ");
        }
        
        $this->objFunc = $this->create('MODItem');
        $this->res = $this->objFunc->listarItemNotBase();

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarItem() {
        $this->objFunc = $this->create('MODItem');
        if ($this->objParam->insertar('id_item')) {
            $this->res = $this->objFunc->insertarItem();
        } else {
            $this->res = $this->objFunc->modificarItem();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarItem() {
        $this->objFunc = $this->create('MODItem');
        $this->res = $this->objFunc->eliminarItem();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    function generarCodigoItem() {
        $this->objFunc = $this->create('MODItem');
        $this->res = $this->objFunc->generarCodigoItem();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    function buscarItemArb() {
        $this->objFunc = $this->create('MODItem');
        $this->res = $this->objFunc->buscarItemArb();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	
	function listarItemExistenciaAlmacen() {
        $this->objParam->defecto('ordenacion', 'id_item');
        $this->objParam->defecto('dir_ordenacion', 'asc');
		
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODItem', 'listarItemExistenciaAlmacen');
        } else {
            $this->objFunc = $this->create('MODItem');
            $this->res = $this->objFunc->listarItemExistenciaAlmacen();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}
?>