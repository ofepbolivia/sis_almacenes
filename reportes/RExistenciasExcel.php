<?php
class RExistenciasExcel
{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    var $datos_detalle;
    var $datos_titulo;
    public  $url_archivo;
    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");

        $this->equivalencias=array( 0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
            9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            50=>'AY',51=>'AZ',
            52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            76=>'BY',77=>'BZ');

    }

    function imprimeCabecera() {
        $this->docexcel->createSheet(0);
        $this->docexcel->setActiveSheetIndex(0);
        $this->docexcel->getActiveSheet()->setTitle('Detalle');

        $this->docexcel->createSheet(1);
        $this->docexcel->setActiveSheetIndex(1);
        $this->docexcel->getActiveSheet()->setTitle('Totales');

        $this->docexcel->setActiveSheetIndex(0);




        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );


        $styleTitulos2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )

            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '0066CC'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $styleTitulos3 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos
        //fRnk: modificado título
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,$_SESSION['_TITULO_SIS_LARGO'] );
        $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:H2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'Bienes de Consumo' );
        $this->docexcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A3:H3');
        //var_dump(date("Y", $this->obtenerFechaEnLetra($this->objParam->getParametro('fecha_hasta')) ));exit;
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'AL: '. $this->objParam->getParametro('fechaHasta'));
        $this->docexcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A4:H4');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,5,'(Expresado en Bolivianos)' );
        $this->docexcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A5:H5');

        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);



        /*$this->docexcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:H6')->applyFromArray($styleTitulos2);*/



        //*************************************Cabecera*****************************************
        /*$this->docexcel->getActiveSheet()->setCellValue('A6','Nro');
        $this->docexcel->getActiveSheet()->setCellValue('B6','Código');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Descripcion del Material');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Unidad');
        $this->docexcel->getActiveSheet()->setCellValue('E6','Cantidad');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Cant. Min.');
        $this->docexcel->getActiveSheet()->setCellValue('G6','C/Unit.');
        $this->docexcel->getActiveSheet()->setCellValue('H6','C/Total');*/

    }
    function generarDatos()
    {

        //$hoja_detalle = $this->docexcel->getSheet(0);
        //$hoja_totales = $this->docexcel->getSheet(1);
        $styleTitulos3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )

            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '0066CC'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )

            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '626eba'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $styleTitulos3 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )

            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '3287c1'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $this->numero = 1;
        $fila = 6;
        $datos = $this->objParam->getParametro('datos');
        //subtotales
        $clasificacion = '';
        $contadorCostoGrupo = 0;
        $contadorCostoTotal = 0;
        $this->imprimeCabecera(0);

        //$numberFormat = '#,#0.##;[Red]-#,#0.##';
        $numberFormat = '#,##0.00';
        $cant_datos = count($datos);
        $cont_total = 1;
        $fila_total = 1;

        $this->docexcel->getSheet(1)->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getSheet(1)->getColumnDimension('C')->setWidth(70);
        $this->docexcel->getSheet(1)->getColumnDimension('D')->setWidth(25);

        foreach ($datos as $value)
        {
            //subtotales{
            if($clasificacion!=''){
                if($clasificacion!=$value['clasificacion']){
                    $styleTitulos['fill']['color']['rgb'] = '4b9bd1';
                    $this->docexcel->getSheet(0)->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setWrapText(true);
                    $this->docexcel->getSheet(0)->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($styleTitulos2);
                    $this->docexcel->getSheet(0)->mergeCells('A'.$fila.':G'.$fila);
                    $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(0, $fila, 'Total');
                    //$this->docexcel->getActiveSheet()->getStyle('H7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                    $this->docexcel->getSheet(0)->getStyle('H'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                    $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(7, $fila, $contadorCostoGrupo);
                    $contadorCostoTotal+=$contadorCostoGrupo;

                    //totales
                    $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(1,$fila_total,$cont_total);
                    $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(2,$fila_total,$clasificacion);
                    $this->docexcel->getSheet(1)->getStyle('D'.$fila_total)->getNumberFormat()->setFormatCode($numberFormat);
                    $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(3,$fila_total,$contadorCostoGrupo);

                    $contadorCostoGrupo = 0;
                    $fila++;

                    $cont_total++;
                    $fila_total++;
                }
            }
            if($clasificacion=='' || $clasificacion!=$value['clasificacion']){
                if($clasificacion==''){
                    //totales
                    $this->docexcel->getSheet(1)->getStyle('B1:D1')->getAlignment()->setWrapText(true);
                    $this->docexcel->getSheet(1)->getStyle('B1:D1')->applyFromArray($styleTitulos3);
                    $this->docexcel->getSheet(1)->mergeCells('B'.$fila_total.':D'.$fila_total);
                    $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(1,$fila_total,'Totales Existencias');
                    $fila_total++;
                    $this->docexcel->getSheet(1)->getStyle('B2:D2')->getAlignment()->setWrapText(true);
                    $this->docexcel->getSheet(1)->getStyle('B2:D2')->applyFromArray($styleTitulos3);
                    $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(1,$fila_total,'Nro.');
                    $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(2,$fila_total,'Detalle');
                    $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(3,$fila_total,'Costo');

                    $fila_total++;
                }

                //detalle
                $this->docexcel->getSheet(0)->getStyle('A'.$fila.':H'.$fila)->getAlignment()->setWrapText(true);
                $this->docexcel->getSheet(0)->getStyle('A'.$fila.':H'.$fila)->applyFromArray($styleTitulos1);

                $this->docexcel->getSheet(0)->mergeCells('A'.$fila.':H'.$fila);
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(0, $fila, $value['clasificacion']);
                $fila++;

                $this->docexcel->getSheet(0)->getStyle('A'.$fila.':H'.$fila)->getAlignment()->setWrapText(true);
                $this->docexcel->getSheet(0)->getStyle('A'.$fila.':H'.$fila)->applyFromArray($styleTitulos1);
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(0,$fila,'Nro');
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(1,$fila,'Código');
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(2,$fila,'Descripcion del Material');
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(3,$fila,'Unidad');
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(4,$fila,'Cantidad');
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(5,$fila,'Cant. Min.');
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(6,$fila,'C/Unit.');
                $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(7,$fila,'C/Total');
                $fila++;
            }

            $styleTitulos['fill']['color']['rgb'] = 'e6e8f4';
            $this->docexcel->getSheet()->getStyle('A'.$fila.':H'.$fila)->applyFromArray($styleTitulos);
            //$this->docexcel->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setWrapText(true);
            //subtotales}

            $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(0, $fila, $this->numero);
            $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(1, $fila, $value['codigo']);
            $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(2, $fila, $value['nombre']);
            $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(3, $fila, $value['unidad_medida']);
            $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(4, $fila, $value['cantidad']);
            $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(5, $fila, $value['cantidad_min']);
            if ($value['cantidad'] != 0) {
                $costoUnitario = $value['costo']/$value['cantidad'];
            }
            $this->docexcel->getSheet(0)->getStyle('G'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
            $this->docexcel->getSheet(0)->getStyle('H'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
            $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(6, $fila,$costoUnitario);
            $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(7, $fila, $value['costo']);
            $contadorCostoGrupo += $value['costo'];
            $fila++;
            $this->numero++;
            //subtotales
            $clasificacion = $value['clasificacion'];
        }
        //totales
        $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(1,$fila_total,$cont_total);
        $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(2,$fila_total,$clasificacion);
        $this->docexcel->getSheet(1)->getStyle('D'.$fila_total)->getNumberFormat()->setFormatCode($numberFormat);
        $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(3,$fila_total,$contadorCostoGrupo);

        $this->docexcel->getSheet(0)->getStyle('H'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
        $this->docexcel->getSheet(0)->getStyle('A'.$fila.':H'.$fila)->applyFromArray($styleTitulos2);
        $this->docexcel->getSheet(0)->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(0, $fila, 'Total');
        $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(7, $fila, $contadorCostoGrupo);
        $fila++;
        $contadorCostoTotal+=$contadorCostoGrupo;

        $fila_total++;
        $this->docexcel->getSheet(1)->mergeCells('B'.$fila_total.':C'.$fila_total);
        $this->docexcel->getSheet(1)->getStyle('B'.$fila_total.':D'.$fila_total)->applyFromArray($styleTitulos2);
        //$this->docexcel->getSheet(1)->setCellValueByColumnAndRow(1,$fila_total,$cont_total);
        $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(1,$fila_total,'Totales');
        $this->docexcel->getSheet(1)->getStyle('D'.$fila_total)->getNumberFormat()->setFormatCode($numberFormat);
        $this->docexcel->getSheet(1)->setCellValueByColumnAndRow(3,$fila_total,$contadorCostoTotal);

        $this->docexcel->getSheet(0)->getStyle('H'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
        $this->docexcel->getSheet(0)->getStyle('A'.$fila.':H'.$fila)->applyFromArray($styleTitulos2);
        $this->docexcel->getSheet(0)->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(0, $fila, 'Costo Total');
        $this->docexcel->getSheet(0)->setCellValueByColumnAndRow(7, $fila, $contadorCostoTotal);

    }
    function obtenerFechaEnLetra($fecha){
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $dia= date("d", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        // var_dump()
        $mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
        return $dia.' de '.$mes.' del '.$anno;
    }
    function generarReporte(){

        //$this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
        $this->imprimeCabecera(0);

    }

}
?>