<?php
/**
 *@package pXP
 *@file gen-ACTClasificacionPartida.php
 *@author  maylee.perez
 *@date 25-11-2020 11:18:22
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTClasificacionPartida extends ACTbase {

    function listarClasificacionPartida() {
        $this->objParam->defecto('ordenacion', 'id_item_partida');

        /*if ($this->objParam->getParametro('id_clasificacion') != '') {
            $this->objParam->addFiltro("cpa.id_clasificacion = " . $this->objParam->getParametro('id_clasificacion'));
        }*/

        if ($this->objParam->getParametro('id_gestion') != '') {
            $this->objParam->addFiltro("cpa.id_gestion=" . $this->objParam->getParametro('id_gestion'));
        }

        $this->objParam->defecto('dir_ordenacion', 'asc');
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODClasificacionPartida', 'listarClasificacionPartida');
        } else {
            $this->objFunc = $this->create('MODClasificacionPartida');
            $this->res = $this->objFunc->listarClasificacionPartida();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarClasificacionPartida() {
        $this->objFunc = $this->create('MODClasificacionPartida');
        if ($this->objParam->insertar('id_item_partida')) {
            $this->res = $this->objFunc->insertarClasificacionPartida();
        } else {
            $this->res = $this->objFunc->modificarClasificacionPartida();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarClasificacionPartida() {
        $this->objFunc = $this->create('MODClasificacionPartida');
        $this->res = $this->objFunc->eliminarClasificacionPartida();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function clonarCLasificacionPartida(){
        $this->objFunc=$this->create('MODClasificacionPartida');
        $this->res=$this->objFunc->clonarCLasificacionPartida($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}
?>