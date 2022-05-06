<?php
/**
*@package pXP
*@file gen-ACTItemConceptoIngas.php
*@author  (gsarmiento)
*@date 18-05-2017 14:01:28
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTItemConceptoIngas extends ACTbase{    
			
	function listarItemConceptoIngas(){
		$this->objParam->defecto('ordenacion','id_item_concepto_ingas');
		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_item')!=''){
			$this->objParam->addFiltro("id_item = ".$this->objParam->getParametro('id_item'));
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODItemConceptoIngas','listarItemConceptoIngas');
		} else{
			$this->objFunc=$this->create('MODItemConceptoIngas');
			
			$this->res=$this->objFunc->listarItemConceptoIngas($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarItemConceptoIngas(){
		$this->objFunc=$this->create('MODItemConceptoIngas');	
		if($this->objParam->insertar('id_item_concepto_ingas')){
			$this->res=$this->objFunc->insertarItemConceptoIngas($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarItemConceptoIngas($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarItemConceptoIngas(){
			$this->objFunc=$this->create('MODItemConceptoIngas');	
		$this->res=$this->objFunc->eliminarItemConceptoIngas($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>