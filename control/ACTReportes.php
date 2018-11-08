<?php
/**
 * @Package pxP
 * @file    ACTAlmacen.php
 * @author  Gonzalo Sarmiento
 * @date    21-09-2012
 * @descripcion Clase que recibe los parametros enviados por la vista para luego ser mandadas a la capa Modelo
 */
require_once (dirname(__FILE__) . '/../reportes/pxpReport/ReportWriter.php');
require_once (dirname(__FILE__) . '/../reportes/RExistencias.php');
require_once (dirname(__FILE__) . '/../reportes/RExistenciasUpdate.php');
require_once (dirname(__FILE__) . '/../reportes/RExistenciasPUDesglosado.php');
require_once (dirname(__FILE__) . '/../reportes/pxpReport/DataSource.php');
require_once (dirname(__FILE__) . '/../reportes/RExistenciasExcel.php');

class ACTReportes extends ACTbase {
    function reporteExistencias() {
        //var_dump($this->objParam->getParametro('formato_reporte'));exit;
        //TODO: pasos para el reporte:
        //iterar sobre el array de ids de almacenes
        //Obtener el listado de los items ordenados por clasificacion y por fecha de un determinado almacen:
        $fechaHasta = $this->objParam->getParametro('fecha_hasta');

        if ($this->objParam->getParametro('formato_reporte') == 'pdf') {
            $idAlmacen = $this->objParam->getParametro('id_almacen');
            $this->objParam->addParametroConsulta('ordenacion', 'cla.id_clasificacion');
            $this->objParam->addParametroConsulta('dir_ordenacion', 'asc');
            $this->objParam->addParametroConsulta('cantidad', 10000);
            $this->objParam->addParametroConsulta('puntero', 0);

            $nombreArchivo = 'Existencias.pdf';

            if($this->objParam->getParametro('formato') == 'antiguo') {
                $this->objFunc = $this->create('MODReporte');
                $resultRepExistencias = $this->objFunc->listarItemsPorAlmacenFecha($this->objParam);

                $dataSource = new DataSource();
                $resultData = $resultRepExistencias->getDatos();//var_dump($resultData);exit;
                $lastNombreClasificacion = $resultData[0]['clasificacion'];
                $dataSourceArray = Array();
                $dataSourceClasificacion = new DataSource();
                $dataSetClasificacion = Array();
                $totalCostoClasificacion = 0;
                $mainDataSet = array();
                $costoTotal = 0;
                foreach ($resultData as $row) {
                    if ($row['clasificacion'] != $lastNombreClasificacion) {
                        $costoTotal += $totalCostoClasificacion;
                        $mainDataSet[] = array("nombreClasificacion" => $lastNombreClasificacion, "totalClasificacion" => $totalCostoClasificacion);
                        $dataSourceClasificacion->setDataSet($dataSetClasificacion);
                        $dataSourceClasificacion->putParameter('totalCosto', $totalCostoClasificacion);
                        $dataSourceClasificacion->putParameter('nombreClasificacion', $lastNombreClasificacion);
                        $dataSourceArray[] = $dataSourceClasificacion;
                        $lastNombreClasificacion = $row['clasificacion'];
                        $dataSourceClasificacion = new DataSource();
                        $dataSetClasificacion = Array();
                        $totalCostoClasificacion = 0;
                    }
                    $dataSetClasificacion[] = $row;
                    $totalCostoClasificacion += $row['costo'];
                }
                $costoTotal += $totalCostoClasificacion;
                $mainDataSet[] = array("nombreClasificacion" => $lastNombreClasificacion, "totalClasificacion" => $totalCostoClasificacion);
                $dataSourceClasificacion->setDataSet($dataSetClasificacion);
                $dataSourceClasificacion->putParameter('totalCosto', $totalCostoClasificacion);
                $dataSourceClasificacion->putParameter('nombreClasificacion', $lastNombreClasificacion);
                $dataSourceArray[] = $dataSourceClasificacion;
                $dataSource->putParameter('clasificacionDataSources', $dataSourceArray);
                $dataSource->putParameter('costoTotal', $costoTotal);
                $dataSource->putParameter('fechaHasta', $fechaHasta);
                $dataSource->putParameter('almacen', $this->objParam->getParametro('almacen'));
                $dataSource->putParameter('mostrar_costos', $this->objParam->getParametro('mostrar_costos'));
                $dataSource->setDataSet($mainDataSet);
                $reporte = new RExistencias();
                $reporte->setDataSource($dataSource);


                $reportWriter = new ReportWriter($reporte, dirname(__FILE__) . '/../../reportes_generados/' . $nombreArchivo);
                $reportWriter->writeReport(ReportWriter::PDF);
            }else if($this->objParam->getParametro('formato') == 'nuevo'){

                $this->objFunc = $this->create('MODReporte');
                $resultRepExistencias = $this->objFunc->listarKardexItemIngSal($this->objParam);

                $resultData = $resultRepExistencias->getDatos();

                $this->objParam->addParametro('orientacion','P');
                $this->objParam->addParametro('tamano','LETTER');
                $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

                $reporte = new RExistenciasUpdate($this->objParam);
                $reporte->setDatos($resultData);
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo,'F');
            }else if($this->objParam->getParametro('formato')=='ingresos'){

                $this->objFunc = $this->create('MODReporte');
                $resultRepExistencias = $this->objFunc->listarKardexItemDesglosado($this->objParam);

                $resultData = $resultRepExistencias->getDatos();

                $this->objParam->addParametro('orientacion','P');
                $this->objParam->addParametro('tamano','LETTER');
                $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

                $reporte = new RExistenciasPUDesglosado($this->objParam);
                $reporte->setDatos($resultData);
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo,'F');
            }

            if ($resultRepExistencias->getTipo() == 'ERROR') {
                $resultRepExistencias->imprimirRespuesta($resultRepExistencias->generarMensajeJson());
                exit;
            }
            $mensajeExito = new Mensaje();
            $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
            $mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->res = $mensajeExito;
            $this->res->imprimirRespuesta($this->res->generarJson());
        }else{
            $this->objParam->addParametroConsulta('ordenacion', 'cla.id_clasificacion');
            $this->objParam->addParametroConsulta('dir_ordenacion', 'asc');
            $this->objParam->addParametroConsulta('cantidad', 10000);
            $this->objParam->addParametroConsulta('puntero', 0);
            $this->objFunc = $this->create('MODReporte');
            $this->res = $this->objFunc->listarItemsPorAlmacenFecha($this->objParam);
            //var_dump( $this->res);exit;
            //obtener titulo de reporte
            $titulo = 'Reporte Existencias';
            //Genera el nombre del archivo (aleatorio + titulo)
            $nombreArchivo = uniqid(md5(session_id()) . $titulo);
            $nombreArchivo .= '.xls';
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
            $this->objParam->addParametro('datos', $this->res->datos);
            $this->objParam->addParametro('fechaHasta', $fechaHasta);
            //Instancia la clase de excel
            $this->objReporteFormato = new RExistenciasExcel($this->objParam);
            $this->objReporteFormato->generarDatos();
            $this->objReporteFormato->generarReporte();
            $this->mensajeExito = new Mensaje();
            $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
        }
    }
    function listarKardexItem() {
        $this->objParam->defecto('ordenacion', 'codigo');
        $this->objParam->defecto('dir_ordenacion', 'asc');

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODReporte', 'listarKardexItem');
        } else {
            $this->objFunc = $this->create('MODReporte');
            $this->res = $this->objFunc->listarKardexItem();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarItemEntRec() {
        $this->objParam->defecto('ordenacion', 'codigo');
        $this->objParam->defecto('dir_ordenacion', 'asc');

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODReporte', 'listarItemEntRec');
        } else {
            $this->objFunc = $this->create('MODReporte');
            $this->res = $this->objFunc->listarItemEntRec();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}
?>