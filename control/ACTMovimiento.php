<?php
/**
 * @Package pxP
 * @file    ACTMovimiento.php
 * @author  Gonzalo Sarmiento
 * @date    02-10-2012
 * @descripcion Clase que recibe los parametros enviados por la vista para luego ser mandadas a la capa Modelo
 */

require_once (dirname(__FILE__) . '/../reportes/pxpReport/ReportWriter.php');
require_once (dirname(__FILE__) . '/../reportes/RMovimiento.php');
require_once (dirname(__FILE__) . '/../reportes/pxpReport/DataSource.php');

class ACTMovimiento extends ACTbase {
	
    function listarMovimiento() {
        $this->objParam->defecto('ordenacion', 'mov.fecha_mov');
        $this->objParam->defecto('dir_ordenacion', 'asc');
		
		//Filtro del tipo de movimiento de la barra de herramientas
		if($this->objParam->getParametro('cmb_tipo_movimiento')!=''){
			if($this->objParam->getParametro('cmb_tipo_movimiento')=='ingreso'||$this->objParam->getParametro('cmb_tipo_movimiento')=='salida'){
				$this->objParam->addFiltro("movtip.tipo = ''".$this->objParam->getParametro('cmb_tipo_movimiento')."''");	
			}
		}
		
		//Filtro para ventana de cierre de gestión
		if($this->objParam->getParametro('ids')!=''){
			$this->objParam->addFiltro("mov.id_movimiento in  (".$this->objParam->getParametro('ids').")");
		}
		
		if($this->objParam->getParametro('pes_estado')=='borrador'){
             $this->objParam->addFiltro("mov.estado_mov in (''borrador'')");
        }
        if($this->objParam->getParametro('pes_estado')=='en_aprobacion'){
             $this->objParam->addFiltro("mov.estado_mov in (''vbarea'',''autorizacion'')");
        }
        if($this->objParam->getParametro('pes_estado')=='en_almacenes'){
             $this->objParam->addFiltro("mov.estado_mov in (''prefin'')");
        }
		if($this->objParam->getParametro('pes_estado')=='entregado'){
             $this->objParam->addFiltro("mov.estado_mov in (''finalizado'')");
        }
		
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODMovimiento', 'listarMovimiento');
        } else {
            if ($this->objParam->getParametro('estado_mov') != null) {
                $this->objParam->addFiltro(" mov.estado_mov = ''" . $this->objParam->getParametro('estado_mov') . "'' ");
            }
            if ($this->objParam->getParametro('tipo') != null) {
                $this->objParam->addFiltro(" movtip.tipo = ''" . $this->objParam->getParametro('tipo') . "'' ");
            }
												
			$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);
            $this->objFunc = $this->create('MODMovimiento');
            $this->res = $this->objFunc->listarMovimiento();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarMovimiento() {
        $this->objFunc = $this->create('MODMovimiento');
        if ($this->objParam->insertar('id_movimiento')) {
            $this->res = $this->objFunc->insertarMovimiento();
        } else {
            $this->res = $this->objFunc->modificarMovimiento();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	function insertarMovimientoREST() {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->insertarMovimientoREST();
        
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarMovimiento() {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->eliminarMovimiento();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function finalizarMovimiento() {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->finalizarMovimiento();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function cancelarMovimiento() {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->cancelarMovimiento();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function revertirMovimiento() {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->revertirMovimiento();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function movimientosPendientesPeriodo() {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->movimientosPendientesPeriodo();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function generarReporteMovimiento() {
        $idMovimiento = $this->objParam->getParametro('id_movimiento');

        $idProcesoWf= $this->objParam->getParametro('id_proceso_wf');

		$costos = $this->objParam->getParametro('costos');
        $tipoMovimiento = $this->objParam->getParametro('tipo');
        $tipoPersonalizado = $this->objParam->getParametro('nombre_movimiento_tipo');
        $codigoMovimiento = $this->objParam->getParametro('codigo');
        $nombreAlmacen = $this->objParam->getParametro('nombre_almacen');
        $descripcionMovimiento = $this->objParam->getParametro('descripcion');
        $observacionesMovimiento = $this->objParam->getParametro('observaciones');
        $fechaRegMovimiento = $this->objParam->getParametro('fecha_reg');
        $fechaMovimiento = $this->objParam->getParametro('fecha_movimiento');
        $nombreFuncionario = $this->objParam->getParametro('nombre_funcionario');
        $nombreProveedor = $this->objParam->getParametro('nombre_proveedor');
        
        $dataSource = new DataSource();
        if($idMovimiento == ''){
            $this->objParam->addParametroConsulta('filtro', ' mov.id_proceso_wf = ' . $idProcesoWf);
        }else{
            $this->objParam->addParametroConsulta('filtro', ' movdet.id_movimiento = ' . $idMovimiento);
        }
        //$this->objParam->addParametroConsulta('ordenacion', 'cla.id_clasificacion');
        $this->objParam->addParametroConsulta('ordenacion', 'item.codigo');
        $this->objParam->addParametroConsulta('dir_ordenacion', 'asc');
        $this->objParam->addParametroConsulta('cantidad', 1000);
        $this->objParam->addParametroConsulta('puntero', 0);
        $this->objFunc = $this->create('MODMovimiento');
        $resultRepMovimiento = $this->objFunc->listarReporteMovimiento($this->objParam);

        $resultData = $resultRepMovimiento->getDatos();

        //1. En caso de que el movimiento sea un inventario Inicial
        if ($tipoMovimiento == "ingreso" && $tipoPersonalizado == "Inventario Inicial") {
            	
            $lastNombreClasificacion = $resultData[0]['nombre_clasificacion'];
            $dataSourceArray = Array();
            $dataSourceClasificacion = new DataSource();
            $dataSetClasificacion = Array();
            $totalCostoClasificacion = 0;
            $mainDataSet = array();
            $costoTotal = 0;
            foreach ($resultData as $row) {
                if ($row['nombre_clasificacion'] != $lastNombreClasificacion) {
                    $costoTotal += $totalCostoClasificacion;
                    $mainDataSet[] = array("nombreClasificacion" => $lastNombreClasificacion, "totalClasificacion" => $totalCostoClasificacion);
                    $dataSourceClasificacion->setDataSet($dataSetClasificacion);
                    $dataSourceClasificacion->putParameter('totalCosto', $totalCostoClasificacion);
                    $dataSourceClasificacion->putParameter('nombreClasificacion', $lastNombreClasificacion);
                    $dataSourceArray[] = $dataSourceClasificacion;
    
                    $lastNombreClasificacion = $row['nombre_clasificacion'];
                    $dataSourceClasificacion = new DataSource();
                    $dataSetClasificacion = Array();
                    $totalCostoClasificacion = 0;
                }
                $dataSetClasificacion[] = $row;
                $totalCostoClasificacion += $row['costo_total'];
            }
            $costoTotal += $totalCostoClasificacion;
            $mainDataSet[] = array("nombreClasificacion" => $lastNombreClasificacion, "totalClasificacion" => $totalCostoClasificacion);
            $dataSourceClasificacion->setDataSet($dataSetClasificacion);
            $dataSourceClasificacion->putParameter('totalCosto', $totalCostoClasificacion);
            $dataSourceClasificacion->putParameter('nombreClasificacion', $lastNombreClasificacion);
            $dataSourceArray[] = $dataSourceClasificacion;
    
            $dataSource->putParameter('clasificacionDataSources', $dataSourceArray);
            $dataSource->putParameter('costoTotal', $costoTotal);
            $dataSource->setDataSet($mainDataSet);
            //Fin 1.
        } else {
            $costoTotal = 0;
            foreach($resultData as $row) {
                $costoTotal += $row['costo_total'];

                $nombreSolicitante = $row['nombre_funcionario'];
                $comail = $row['comail'];
                $fechaSalida = $row['fecha_salida'];
            }
            $dataSource->setDataSet($resultData);
            $dataSource->putParameter('totalCosto', $costoTotal);
        }
        
        $dataSource->putParameter('codigo', $codigoMovimiento);
        $dataSource->putParameter('tipoMovimiento', $tipoMovimiento);
        $dataSource->putParameter('almacen', $nombreAlmacen);
        $dataSource->putParameter('motivo', $tipoPersonalizado);
        $dataSource->putParameter('descripcion', $descripcionMovimiento);
        $dataSource->putParameter('observaciones', $observacionesMovimiento);
        $dataSource->putParameter('fechaRemision', $fechaRegMovimiento);
        $dataSource->putParameter('fechaMovimiento', $fechaMovimiento);
		$dataSource->putParameter('costos', $costos);
		$dataSource->putParameter('funcionario_solicitante', $nombreSolicitante);
		$dataSource->putParameter('comail', $comail);
		$dataSource->putParameter('fechaSalida',$fechaSalida);

        if ($nombreFuncionario != null && $nombreFuncionario != '') {
            $dataSource->putParameter('solicitante', $nombreFuncionario);
        } else {
            $dataSource->putParameter('solicitante', $nombreProveedor);
        }

        $reporte = new RMovimiento();
        $reporte->setDataSource($dataSource);
        $nombreArchivo = 'Movimiento.pdf';
        $reportWriter = new ReportWriter($reporte, dirname(__FILE__) . '/../../reportes_generados/' . $nombreArchivo);
        $reportWriter->writeReport(ReportWriter::PDF);

        $mensajeExito = new Mensaje();
        $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $mensajeExito;
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    function siguienteEstadoMovimiento(){
        $this->objFunc=$this->create('MODMovimiento');  
        $this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]); 
        $this->res=$this->objFunc->siguienteEstadoMovimiento($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    function anteriorEstadoMovimiento(){
        $this->objFunc=$this->create('MODMovimiento');  
        $this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]); 
        $this->res=$this->objFunc->anteriorEstadoSolicitud($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	
	function listarFuncionarioMovimientoTipo() {
		$this->objParam->defecto('ordenacion','PERSON.nombre_completo1');
		$this->objParam->defecto('dir_ordenacion','asc');
		
		if ($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte=new Reporte($this->objParam, $this);
			$this->res=$this->objReporte->generarReporteListado('MODMovimiento','listarFuncionarioMovimientoTipo');
		}
		else {
			$this->objFunSeguridad=$this->create('MODMovimiento');
			$this->res=$this->objFunSeguridad->listarFuncionarioMovimientoTipo($this->objParam);
		}
		
		$this->res->imprimirRespuesta($this->res->generarJson());		
	}
	
	function revertirPreingreso(){
        $this->objFunc=$this->create('MODMovimiento');  
        $this->res=$this->objFunc->revertirPreingreso($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function decimalesSolicitud() {

        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->decimalesSolicitud();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}
?>
