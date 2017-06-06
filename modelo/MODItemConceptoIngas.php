<?php
/**
*@package pXP
*@file gen-MODItemConceptoIngas.php
*@author  (gsarmiento)
*@date 18-05-2017 14:01:28
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODItemConceptoIngas extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarItemConceptoIngas(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='alm.ft_item_concepto_ingas_sel';
		$this->transaccion='ALM_ITMINGAS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_item_concepto_ingas','int4');
		$this->captura('id_item','int4');
		$this->captura('id_concepto_ingas','int4');
		$this->captura('desc_ingas','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarItemConceptoIngas(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='alm.ft_item_concepto_ingas_ime';
		$this->transaccion='ALM_ITMINGAS_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_item','id_item','int4');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarItemConceptoIngas(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='alm.ft_item_concepto_ingas_ime';
		$this->transaccion='ALM_ITMINGAS_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_item_concepto_ingas','id_item_concepto_ingas','int4');
		$this->setParametro('id_item','id_item','int4');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarItemConceptoIngas(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='alm.ft_item_concepto_ingas_ime';
		$this->transaccion='ALM_ITMINGAS_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_item_concepto_ingas','id_item_concepto_ingas','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>