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
require_once (dirname(__FILE__) . '/../reportes/RMovimientoConsolidado.php');
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
            $this->objParam->addFiltro("mov.estado_mov in (''vbarea'',''autorizacion'', ''aprobado'')");
        }
        if($this->objParam->getParametro('pes_estado')=='en_almacenes'){
            $this->objParam->addFiltro("mov.estado_mov in (''prefin'')");
        }
        if($this->objParam->getParametro('pes_estado')=='entregado'){
            $this->objParam->addFiltro("mov.estado_mov in (''finalizado'')");
        }
        if($this->objParam->getParametro('pes_estado')=='anulado'){
            $this->objParam->addFiltro("mov.estado_mov in (''anulado'')");
        }
        $this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);
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
            //$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);
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
    function insertarMovimientosREST() {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->insertarMovimientosREST();

        $this->res->imprimirRespuesta(json_encode($this->res->getDatos()));
        //$this->res->imprimirRespuesta($this->res->generarJson());
    }

    function revertirMovimientoREST(){
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->revertirMovimientoREST();

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function revertirMovimientoTranREST(){
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->revertirMovimientoTranREST();

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function actualizarEstadoMovimientoREST(){
        $this->objFunc=$this->create('MODMovimiento');

        $this->res=$this->objFunc->actualizarEstadoMovimientoREST($this->objParam);

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

        $datos = $this->res->getDatos();
        $operacion =$this->objParam->getParametro('operacion');
        if($operacion=='inicio'&& $datos['tipo_movimiento']=='SALNORROPA'){   //solo en caso de ropa de trabajo
            $data = array("id_movimiento" => $datos['id_movimiento'], "usuario" => $datos['usuario']);
            $data_string = json_encode($data);
            //$request =  'http://wservices.obairlines.bo/Dotacion.AppService/SvcDotacion.svc/RevertirDotacionAlmacenes';
            $request =  'http://wservices.obairlines.bo/Dotacion.AppService/Api/Dotaciones/RevertirDotacionAlmacenes';
            $session = curl_init($request);
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
            );

            $result = curl_exec($session);
            curl_close($session);

            $respuesta = json_decode($result);
            if($respuesta->state =='false'){
                throw new Exception(__METHOD__.$respuesta->mensaje);
            }else {
                $this->objFunc = $this->create('MODMovimiento');
                $this->res = $this->objFunc->eliminarMovimiento();
            }
        }

        if($operacion=='siguiente'&& $datos['tipo_movimiento']=='SALNORROPA'){   //solo en caso de ropa de trabajo
            $data = array("id_movimiento" => $datos['id_movimiento']);
            $data_string = json_encode($data);
            //$request =  'http://wservices.obairlines.bo/Dotacion.AppService/SvcDotacion.svc/RevertirDotacionAlmacenes';
            $request =  'http://wservices.obairlines.bo/Dotacion.AppService/Api/Dotaciones/NotificarRecojoERP';
            $session = curl_init($request);
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
            );

            $result = curl_exec($session);
            curl_close($session);

            $respuesta = json_decode($result);
            if($respuesta->state =='false'){
                throw new Exception(__METHOD__.$respuesta->mensaje);
            }

        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }



    function finalizarGrupo() {
        $id_funcionarios_wf='';
        $id_tipos_estado='';
        $id_movimientos = explode(',',$this->objParam->getParametro('id_movimientos'));
        $id_almacenes = explode(',',$this->objParam->getParametro('id_almacenes'));
        if($this->objParam->getParametro('id_funcionario_wf')!=''){
            $id_funcionario_wf = $this->objParam->getParametro('id_funcionario_wf');
        }
        if($this->objParam->getParametro('id_tipo_estado')!=''){
            $id_tipo_estado = $this->objParam->getParametro('id_tipo_estado');
        }
        $operacion = $this->objParam->getParametro('operacion');

        for($i=0;$i<count($id_movimientos);$i++){

            $this->objParam->addParametro('id_movimiento',$id_movimientos[$i]);
            $this->objParam->addParametro('id_almacen',$id_almacenes[$i]);
            $this->objParam->addParametro('id_funcionario_wf',$id_funcionario_wf);
            $this->objParam->addParametro('id_tipo_estado',$id_tipo_estado);
            $this->objParam->addParametro('operacion',$operacion);
            $this->objFunc = $this->create('MODMovimiento');
            $this->res = $this->objFunc->finalizarMovimiento();

            if($this->res->getTipo() !='EXITO'){
                throw new Exception(__METHOD__.$this->res->getMensaje());
            }
            $datos = $this->res->getDatos();

            if($operacion=='siguiente'&& $datos['tipo_movimiento']=='SALNORROPA'){   //solo en caso de ropa de trabajo
                $data = array("id_movimiento" => $datos['id_movimiento']);
                $data_string = json_encode($data);
                //$request =  'http://wservices.obairlines.bo/Dotacion.AppService/SvcDotacion.svc/RevertirDotacionAlmacenes';
                $request =  'http://wservices.obairlines.bo/Dotacion.AppService/Api/Dotaciones/NotificarRecojoERP';
                $session = curl_init($request);
                curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($session, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string))
                );

                $result = curl_exec($session);
                curl_close($session);

                $respuesta = json_decode($result);
                if($respuesta->state =='false'){
                    throw new Exception(__METHOD__.$respuesta->mensaje);
                }
            }
        }
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
        $nombreSolicitante;
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

        if($resultData[0]['codigo_tran']==NULL) {
            $reporte = new RMovimiento();
            $reporte->setDataSource($dataSource);
        }else{
            $this->objParam->addParametroConsulta('filtro', ' mov.codigo_tran = ' . "''". $resultData[0]['codigo_tran']."''");
            $this->objParam->addParametroConsulta('ordenacion', 'fun.lugar_nombre');
            $this->objParam->addParametroConsulta('dir_ordenacion', 'asc');
            $this->objParam->addParametroConsulta('cantidad', 1000);
            $this->objParam->addParametroConsulta('puntero', 0);
            $this->objFunc = $this->create('MODMovimiento');
            $resultRepMovimiento = $this->objFunc->listarReporteMovimientoConsolidado($this->objParam);
            $resultData = $resultRepMovimiento->getDatos();
            $costoTotal = 0;
            foreach($resultData as $row) {
                $costoTotal += $row['costo_total'];

                $nombreSolicitante = $row['nombre_funcionario'];
                $comail = $row['comail'];
                $fechaSalida = $row['fecha_salida'];
            }
            $dataSource->setDataSet($resultData);
            $reporte = new RMovimientoConsolidado();
            $reporte->setDataSource($dataSource);
        }

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

    //(franklin.espinoza) Anular movimientos en bloque en estado borrador
    function anularMovimientoBloque() {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->anularMovimientoBloque();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}
?>
