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
require_once (dirname(__FILE__) . '/../reportes/RMovimientoAlmacenes.php');
require_once (dirname(__FILE__) . '/../reportes/RExistenciasUpdate.php');
require_once (dirname(__FILE__) . '/../reportes/RExistenciasPUDesglosado.php');
require_once (dirname(__FILE__) . '/../reportes/pxpReport/DataSource.php');
require_once (dirname(__FILE__) . '/../reportes/RExistenciasExcel.php');
require_once (dirname(__FILE__) . '/../reportes/RMinisterioExistenciasXLS.php');
require_once (dirname(__FILE__) . '/../reportes/RMovimientoAlmacenesXLS.php');
require_once (dirname(__FILE__) . '/../reportes/RKardexItem.php');

class ACTReportes extends ACTbase {
    function reporteExistencias() {
        //var_dump($this->objParam->getParametro('formato_reporte'));exit;
        //TODO: pasos para el reporte:
        //iterar sobre el array de ids de almacenes
        //Obtener el listado de los items ordenados por clasificacion y por fecha de un determinado almacen:
        //fRnk: add fecha_ini
        $this->objParam->addParametro('fecha_ini',$this->objParam->getParametro('fecha_ini'));
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
                $reporte->setDatos($resultData, $this->objParam->getParametro('all_items'), $this->objParam->getParametro('clasificacion'));
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo,'F');
            }else if($this->objParam->getParametro('formato') == 'ministerio'){

                $this->objFunc = $this->create('MODReporte');
                $resultRepExistencias = $this->objFunc->listarCantidadesClasificacion($this->objParam);
                $resultData = $resultRepExistencias->getDatos();

                $this->objParam->addParametro('orientacion','P');
                $this->objParam->addParametro('tamano','LETTER');
                $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

                $reporte = new RMovimientoAlmacenes($this->objParam);//var_dump($resultData);exit();
                $reporte->setDatos($resultData);
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo,'F');


                /*
                 * $this->objFunc = $this->create('MODReporte');
                $this->res=$this->objFunc->listarCantidadesClasificacion($this->objParam);
                $titulo_archivo = 'Reporte Ministerio Existencias';
                $this->datos=$this->res->getDatos();

                $nombreArchivo = uniqid(md5(session_id()).$titulo_archivo).'.xls';
                $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
                $this->objParam->addParametro('titulo_archivo',$titulo_archivo);
                $this->objParam->addParametro('datos',$this->datos);
                $this->objParam->addParametro('fecha_hasta',$this->objParam->getParametro('fecha_hasta'));

                $this->objReporte = new RMinisterioExistenciasXLS($this->objParam);
                $this->objReporte->generarReporte();*/


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

            if ($this->objParam->getParametro('formato') != 'ministerio') {
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

            }else{
                $this->objFunc = $this->create('MODReporte');
                $this->res=$this->objFunc->listarCantidadesClasificacion($this->objParam);
                $titulo_archivo = 'Reporte Ministerio Existencias';
                $this->datos=$this->res->getDatos();

                $nombreArchivo = uniqid(md5(session_id()).$titulo_archivo).'.xls';
                $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
                $this->objParam->addParametro('titulo_archivo',$titulo_archivo);
                $this->objParam->addParametro('datos',$this->datos);
                $this->objParam->addParametro('fecha_hasta',$this->objParam->getParametro('fecha_hasta'));

                $this->objReporte = new RMovimientoAlmacenesXLS($this->objParam);
                $this->objReporte->generarReporte();
            }

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
        } elseif ($this->objParam->getParametro('tipoReporte') == 'reporte') {
            //fRnk: añadido para el nuevo reporte de Kardex almacen
            $this->reporteKardexItem($this->objParam->getParametro('item'), $this->objParam->getParametro('fecha_ini'), $this->objParam->getParametro('fecha_fin'));
        }
        else {
            $this->objFunc = $this->create('MODReporte');
            $this->res = $this->objFunc->listarKardexItem();
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function reporteKardexItem($item, $fecha_ini, $fecha_fin) {
        $nombreArchivo = uniqid(md5(session_id()).'KardexAlm').'.pdf';
        $this->objFunc = $this->create('MODReporte');
        $repDatos = $this->objFunc->listarKardexItem();
        $dataSource = $repDatos;
        $tamano = 'LETTER';
        $orientacion = 'P';
        $titulo = 'Kardex Almacenes';

        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',$tamano);
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $reporte=new RKardexItem($this->objParam);
        $reporte->setDatos($dataSource->getDatos(), $item, $fecha_ini, $fecha_fin);
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo,'F');

        $mensajeExito=new Mensaje();
        $mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $mensajeExito;
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

    //{'develop':'franklin.espinoza', 'date':'26/2/2020'}
    function listarCantidadesClasificacion(){

        $this->objFunc = $this->create('MODReporte');


        $this->res=$this->objFunc->listarCantidadesClasificacion($this->objParam);
        $titulo_archivo = 'Reporte Ministerio Existencias';
        $this->datos=$this->res->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).$titulo_archivo).'.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('titulo_archivo',$titulo_archivo);
        $this->objParam->addParametro('datos',$this->datos);
        $this->objParam->addParametro('fecha_hasta',$this->objParam->getParametro('fecha_fin'));

        $this->objReporte = new RMinisterioExistenciasXLS($this->objParam);
        $this->objReporte->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $this->mensajeExito;
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function listarTotalCantidadesClasificacion(){
        $this->objFunc = $this->create('MODReporte');

        $this->objParam->addParametro('fecha_hasta',$this->objParam->getParametro('fecha_hasta'));

        $this->res=$this->objFunc->listarTotalCantidadesClasificacion($this->objParam);
        $this->datos=$this->res->getDatos();

        $titulo_archivo = 'Reporte Ministerio Existencias';
        $nombreArchivo = uniqid(md5(session_id()).$titulo_archivo).'.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('titulo_archivo',$titulo_archivo);
        $this->objParam->addParametro('datos',$this->datos);

       
        $this->objReporte = new RMinisterioExistenciasXLS($this->objParam);
        $this->objReporte->generarReporte();
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $this->mensajeExito;
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }    
}
?>