<?php
//incluimos la libreria
//echo dirname(__FILE__);
//include_once(dirname(__FILE__).'/../PHPExcel/Classes/PHPExcel.php');
//NMQ: Ájustes al reporte por HR 2024-01111
class RMinisterioExistenciasXLS
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas = array();
    private $fila;
    private $equivalencias = array();

    private $indice, $m_fila, $titulo;
    private $swEncabezado = 0; //variable que define si ya se imprimió el encabezado
    private $objParam;
    public $url_archivo;


    function __construct(CTParametro $objParam)
    {

        //reducido menos 23,24,26,27,29,30
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "' . $this->objParam->getParametro('titulo_archivo') . '", generado por ERP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");


        $this->docexcel->setActiveSheetIndex(0);

        $this->equivalencias = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I',
            9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R',
            18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z',
            26 => 'AA', 27 => 'AB', 28 => 'AC', 29 => 'AD', 30 => 'AE', 31 => 'AF', 32 => 'AG', 33 => 'AH',
            34 => 'AI', 35 => 'AJ', 36 => 'AK', 37 => 'AL', 38 => 'AM', 39 => 'AN', 40 => 'AO', 41 => 'AP',
            42 => 'AQ', 43 => 'AR', 44 => 'AS', 45 => 'AT', 46 => 'AU', 47 => 'AV', 48 => 'AW', 49 => 'AX',
            50 => 'AY', 51 => 'AZ',
            52 => 'BA', 53 => 'BB', 54 => 'BC', 55 => 'BD', 56 => 'BE', 57 => 'BF', 58 => 'BG', 59 => 'BH',
            60 => 'BI', 61 => 'BJ', 62 => 'BK', 63 => 'BL', 64 => 'BM', 65 => 'BN', 66 => 'BO', 67 => 'BP',
            68 => 'BQ', 69 => 'BR', 70 => 'BS', 71 => 'BT', 72 => 'BU', 73 => 'BV', 74 => 'BW', 75 => 'BX',
            76 => 'BY', 77 => 'BZ');

    }

    function imprimeDatos()
    {
        $this->docexcel->getActiveSheet()->setTitle('Cantidad Clasificacion');
        $datos = $this->objParam->getParametro('datos');
        $columnas = 0;
        $this->docexcel->setActiveSheetIndex(0);


        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '4682B4'
                )
            )/*,
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )*/);

        /*$grupo1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '4682B4'
                )
            )
        );
        $grupo2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '4682B4'
                )
            )
        );
        $grupo3 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '4682B4'
                )
            )
        );
        $grupo4 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '4682B4'
                )
            )
        );
        $grupo5 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '4682B4'
                )
            )
        );*/


        $reporte = $this->objParam->getParametro('tipo_reporte');

        if ($reporte == 'por_partida') {
            $this->docexcel->getActiveSheet()->getStyle('A1:E4')->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setWrapText(true);

            $this->docexcel->getActiveSheet()->mergeCells('A1:E1');
            $this->docexcel->getActiveSheet()->mergeCells('A2:E2');
            $this->docexcel->getActiveSheet()->mergeCells('A3:E3');
            $this->docexcel->getActiveSheet()->mergeCells('A4:E4');

            $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
            $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

            //*************************************Cabecera*****************************************
            $this->docexcel->getActiveSheet()->setCellValue('A1', $this->objParam->getParametro('datos')[0]['vr_entidad']);
            $this->docexcel->getActiveSheet()->setCellValue('A2', 'RESUMEN DE ALMACENES');
            $this->docexcel->getActiveSheet()->setCellValue('A3', 'Al ' . $this->objParam->getParametro('fecha_hasta'));
            $this->docexcel->getActiveSheet()->setCellValue('A4', '(Expresado en Bolivianos)');

            $this->docexcel->getActiveSheet()->setCellValue('A5', 'Partida');
            $this->docexcel->getActiveSheet()->setCellValue('B5', "Cantidad Inicial\nal " . $this->objParam->getParametro('fecha_ini'));
            $this->docexcel->getActiveSheet()->setCellValue('C5', "Saldo Inicial\nal " . $this->objParam->getParametro('fecha_ini') . ' (Bs)');
            $this->docexcel->getActiveSheet()->setCellValue('D5', "Cantidad Final\nal " . $this->objParam->getParametro('fecha_hasta'));
            $this->docexcel->getActiveSheet()->setCellValue('E5', "Saldo Final\nal " . $this->objParam->getParametro('fecha_hasta') . ' (Bs)');
            $this->docexcel->getActiveSheet()->getStyle('A5:E5')->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle('A5:E5')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //*************************************Detalle*****************************************
            $fila = 6;

            $color_pestana = array('FA8072', '0095b6', 'e74c3c', '138d75', 'a93226', '229954', '884ea0', '1f618d', '117a65');
            $index = 0;
            $relleno = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => $color_pestana[$index]
                    )
                )
            );

            $normal = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'ffffff'
                    )
                )
            );

            $codigo = '';
            foreach ($datos as $value) {
                //$columna = 0;

                if ($value['codigo'] != $codigo && $value['tamano'] == 1 && $codigo != '') {
                    $index++;
                    $relleno = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => $color_pestana[$index]
                            )
                        )
                    );
                }

                if ($value['id_clasificacion_fk'] == null) {
                    $this->docexcel->getActiveSheet()->getStyle("A$fila:E$fila")->applyFromArray($value['codigo'] == 'total' ? $relleno : $normal);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['codigo'] . ' ' . $value['nombre']);//$columna++;
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['vr_cantidad_saldo_inicial']);//$columna++;
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['saldo_ini']);//$columna++;
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['vr_cantidad_saldo_final']);//$columna++;
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['saldo_fin']);//$columna++;

                }
                if ($value['tamano'] == -1) {
                    $this->docexcel->getActiveSheet()->getStyle("A$fila:D$fila")->applyFromArray($normal);
                }

                if ($value['tamano'] > 1) {
                    $this->docexcel->getActiveSheet()->getStyle("A$fila:D$fila")->applyFromArray($normal);
                }

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['codigo'] . ' ' . $value['nombre']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['vr_cantidad_saldo_inicial']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['saldo_ini']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['vr_cantidad_saldo_final']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['saldo_fin']);

                $fila++;
                $codigo = $value['codigo'];
            }

            //************************************************Fin Detalle***********************************************
        } else {
            $this->docexcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle('A2:I2')->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle('A3:I3')->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle('A4:I4')->getAlignment()->setWrapText(true);

            $this->docexcel->getActiveSheet()->mergeCells('A1:I1');
            $this->docexcel->getActiveSheet()->mergeCells('A2:I2');
            $this->docexcel->getActiveSheet()->mergeCells('A3:I3');
            $this->docexcel->getActiveSheet()->mergeCells('A4:I4');

            $this->docexcel->getActiveSheet()->mergeCells('A5:A6');
            $this->docexcel->getActiveSheet()->mergeCells('B5:B6');
            $this->docexcel->getActiveSheet()->mergeCells('C5:D5');
            $this->docexcel->getActiveSheet()->mergeCells('E5:F5');
            $this->docexcel->getActiveSheet()->mergeCells('G5:I5');

            $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
            $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            // $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            // $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);

            //*************************************Cabecera*****************************************
            $this->docexcel->getActiveSheet()->setCellValue('A1', $this->objParam->getParametro('datos')[0]['vr_entidad']);
            $this->docexcel->getActiveSheet()->setCellValue('A2', 'DETALLE DE ALMACENES');
            $this->docexcel->getActiveSheet()->setCellValue('A3', 'Al ' . $this->objParam->getParametro('fecha_hasta'));
            $this->docexcel->getActiveSheet()->setCellValue('A4', '(Expresado en Bolivianos)');

            $this->docexcel->getActiveSheet()->setCellValue('A5', 'Codigo');
            $this->docexcel->getActiveSheet()->setCellValue('B5', 'Descripción Item');
            $this->docexcel->getActiveSheet()->setCellValue('C5', 'Ingresos');
            $this->docexcel->getActiveSheet()->setCellValue('E5', 'Salidas');
            $this->docexcel->getActiveSheet()->setCellValue('G5', 'Saldos');
            // $this->docexcel->getActiveSheet()->setCellValue('F5','Valores');

            $this->docexcel->getActiveSheet()->setCellValue('C6', 'Cantidad');
            $this->docexcel->getActiveSheet()->setCellValue('D6', 'Total');
            $this->docexcel->getActiveSheet()->setCellValue('E6', 'Cantidad');
            $this->docexcel->getActiveSheet()->setCellValue('F6', 'Total');
            $this->docexcel->getActiveSheet()->setCellValue('G6', 'Cantidad');
            $this->docexcel->getActiveSheet()->setCellValue('H6', 'Costo');
            $this->docexcel->getActiveSheet()->setCellValue('I6', 'Total');
            // $this->docexcel->getActiveSheet()->setCellValue('K6','Saldo Final');

            $this->docexcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $this->docexcel->getActiveSheet()->getStyle('A6:I6')->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle('A6:I6')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //*************************************Detalle*****************************************
            $fila = 7;

            $color_pestana = array('FA8072', '0095b6', 'e74c3c', '138d75', 'a93226', '229954', '884ea0', '1f618d', '117a65');
            $index = 0;
            $relleno = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => $color_pestana[$index]
                    )
                )
            );

            $normal = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'ffffff'
                    )
                )
            );

            $codigo = '';
            // var_dump($datos); exit;
            foreach ($datos as $value) {
                //$columna = 0;

                if ($value['codigo'] != $codigo && $value['tamano'] == 1 && $codigo != '') {
                    $index++;
                    $relleno = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => $color_pestana[$index]
                            )
                        )
                    );
                }

                // if ($value['id_clasificacion_fk'] == null) {
                //     $this->docexcel->getActiveSheet()->getStyle("A$fila:K$fila")->applyFromArray($value['codigo'] == 'total' ? $relleno : $normal);
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$fila,$value['codigo'].' '.$value['nombre']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$fila,$value['vr_unidad_medida']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$fila,0);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,$fila,$value['vr_cantidad_saldo_inicial']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$fila,$value['vr_cantidad_ingreso']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,$fila,$value['vr_cantidad_egreso']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,$fila,$value['vr_cantidad_saldo_final']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,$fila,$value['saldo_ini']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,$fila,$value['ingreso']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,$fila,$value['salida']);//$columna++;
                //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,$fila,$value['saldo_fin']);//$columna++;

                // }
                if ($value['tamano'] == -1) {
                    $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($normal);
                }

                if ($value['tamano'] > 1) {
                    $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($normal);
                }

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['codigo']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nombre']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['vr_cantidad_ingreso']);//$columna++; Traer el precio desde la base de datos
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['ingreso']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['vr_cantidad_egreso']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['salida']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['vr_cantidad_saldo_final']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['vr_prec_unitario']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['saldo_fin']);//$columna++;

                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$fila,$value['codigo'].' '.$value['nombre']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$fila,$value['vr_unidad_medida']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$fila,$value['vr_prec_unitario']);//$columna++; Traer el precio desde la base de datos
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,$fila,$value['vr_cantidad_saldo_inicial']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$fila,$value['vr_cantidad_ingreso']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,$fila,$value['vr_cantidad_egreso']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,$fila,$value['vr_cantidad_saldo_final']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,$fila,$value['saldo_ini']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,$fila,$value['ingreso']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,$fila,$value['salida']);//$columna++;
                // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,$fila,$value['saldo_fin']);//$columna++;

                $fila++;
                $codigo = $value['codigo'];
            }

            //************************************************Fin Detalle***********************************************
        }

    }

    function generarReporte()
    {
        //echo $this->nombre_archivo; exit;
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->docexcel->setActiveSheetIndex(0);
        $this->imprimeDatos();
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);

    }
}

?>