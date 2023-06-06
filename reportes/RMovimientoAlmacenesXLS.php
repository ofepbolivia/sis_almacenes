<?php

//fRnk: nuevo reporte Movimiento de Almacenes XLS
class RMovimientoAlmacenesXLS
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas = array();
    private $fila;
    private $equivalencias = array();

    private $indice, $m_fila, $titulo;
    private $swEncabezado = 0;
    private $objParam;
    public $url_archivo;


    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator($_SESSION['_TITULO_SIS_CORTO'])
            ->setLastModifiedBy($_SESSION['_TITULO_SIS_CORTO'])
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "' . $this->objParam->getParametro('titulo_archivo'))
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");
        $this->docexcel->setActiveSheetIndex(0);
    }

    function imprimeDatos()
    {
        $this->docexcel->getActiveSheet()->setTitle('Movimiento de Almacenes');
        $sheet = $this->docexcel->getActiveSheet();
        $datos = $this->objParam->getParametro('datos');
        $this->createSheet($sheet, $datos, 'T'); //Totales
        $sheet2 = $this->docexcel->createSheet()->setTitle('Desglose');;
        $this->createSheet($sheet2, $datos, 'D'); //Desglose
    }

    function createSheet($sheet, $datos, $type)
    {
        $sharedStyle1 = new PHPExcel_Style();
        $sheet->setCellValue('A2', 'MOVIMIENTO DE ALMACENES')
            ->setCellValue('A3', 'Del: ' . $this->convertDate($datos[0]['fecha_ini']) . ' Al: ' . $this->convertDate($datos[0]['fecha_hasta']))
            ->setCellValue('A4', '(Expresado en Bolivianos)');
        $sheet->mergeCells('A2:K2')->mergeCells('A3:K3')->mergeCells('A4:K4');

        $sheet->setCellValue('A6', 'Entidad:')->setCellValue('B6', $datos[0]['entidad'])
            ->setCellValue('A7', 'Fuente:')->setCellValue('B7', $datos[0]['fuente_gral'])
            ->setCellValue('A8', 'Almacén:')->setCellValue('B8', $datos[0]['nom_almacen']);

        $first = 10;
        $sheet->setCellValue('A' . $first, 'CÓDIGO')
            ->setCellValue('B' . $first, 'DETALLE')
            ->setCellValue('C' . $first, 'UNIDAD DE MEDIDA')
            ->setCellValue('D' . $first, 'SALDOS INICIALES')->setCellValue('D' . ($first + 1), 'CANTIDAD')->setCellValue('E' . ($first + 1), 'VALOR')
            ->setCellValue('F' . $first, 'INGRESOS')->setCellValue('F' . ($first + 1), 'CANTIDAD')->setCellValue('G' . ($first + 1), 'VALOR')
            ->setCellValue('H' . $first, 'EGRESOS')->setCellValue('H' . ($first + 1), 'CANTIDAD')->setCellValue('I' . ($first + 1), 'VALOR')
            ->setCellValue('J' . $first, 'SALDO FINAL')->setCellValue('J' . ($first + 1), 'CANTIDAD')->setCellValue('K' . ($first + 1), 'VALOR')
            ->getStyle('A' . $first . ':K2')->getFont()->setBold(true);
        $sheet->mergeCells('A' . $first . ':A' . ($first + 1))->mergeCells('B' . $first . ':B' . ($first + 1))->mergeCells('C' . $first . ':C' . ($first + 1))->mergeCells('D' . $first . ':E' . $first)->mergeCells('F' . $first . ':G' . $first)->mergeCells('H' . $first . ':I' . $first)->mergeCells('J' . $first . ':K' . $first);

        $sharedStyle1->applyFromArray(
            array('fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFFFFFFF')//FFCCFFCC
            ),
                'borders' => array(
                    'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                )
            ));
        $styleTitle = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleHeaderTable = array(
            'font' => array(
                'bold' => true,
                'color' => array(
                    'rgb' => 'ffffff'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '2B579A'
                )
            )
        );
        $ar = array();
        $total_cant_si = 0;
        $total_valor_si = 0;
        $total_cant_in = 0;
        $total_valor_in = 0;
        $total_cant_eg = 0;
        $total_valor_eg = 0;
        $total_cant_sf = 0;
        $total_valor_sf = 0;
        $row_subtotal = array();
        $i = $first + 2;
        foreach ($datos as $record) {
            if ($type == 'T') {
                if ($record['nivel'] == 'total') {
                    $ar[] = array($record['codigo'],
                        $record['detalle'],
                        $record['unidad_medida'],
                        $record['cantidad_saldo_inicial'],
                        $this->formatMoney($record['valor_saldo_inicial']),
                        $record['cantidad_ingreso'],
                        $this->formatMoney($record['valor_ingreso']),
                        $record['cantidad_egreso'],
                        $this->formatMoney($record['valor_egreso']),
                        $record['cantidad_saldo_final'],
                        $this->formatMoney($record['valor_saldo_final'])
                    );
                    $total_cant_si += $record['cantidad_saldo_inicial'];
                    $total_valor_si += $record['valor_saldo_inicial'];
                    $total_cant_in += $record['cantidad_ingreso'];
                    $total_valor_in += $record['valor_ingreso'];
                    $total_cant_eg += $record['cantidad_egreso'];
                    $total_valor_eg += $record['valor_egreso'];
                    $total_cant_sf += $record['cantidad_saldo_final'];
                    $total_valor_sf += $record['valor_saldo_final'];
                }
            } else {
                if ($record['nivel'] == 'grupo') {
                    $ar[] = array($record['codigo'], $record['detalle'], '', '', '', '', '', '', '', '', '');
                } elseif ($record['nivel'] == 'item') {
                    $ar[] = array($record['codigo'],
                        $record['detalle'],
                        $record['unidad_medida'],
                        $record['cantidad_saldo_inicial'],
                        $this->formatMoney($record['valor_saldo_inicial']),
                        $record['cantidad_ingreso'],
                        $this->formatMoney($record['valor_ingreso']),
                        $record['cantidad_egreso'],
                        $this->formatMoney($record['valor_egreso']),
                        $record['cantidad_saldo_final'],
                        $this->formatMoney($record['valor_saldo_final'])
                    );
                } else {
                    $ar[] = array('SUBTOTAL', '', '',
                        $record['cantidad_saldo_inicial'],
                        $this->formatMoney($record['valor_saldo_inicial']),
                        $record['cantidad_ingreso'],
                        $this->formatMoney($record['valor_ingreso']),
                        $record['cantidad_egreso'],
                        $this->formatMoney($record['valor_egreso']),
                        $record['cantidad_saldo_final'],
                        $this->formatMoney($record['valor_saldo_final']));
                    $total_cant_si += $record['cantidad_saldo_inicial'];
                    $total_valor_si += $record['valor_saldo_inicial'];
                    $total_cant_in += $record['cantidad_ingreso'];
                    $total_valor_in += $record['valor_ingreso'];
                    $total_cant_eg += $record['cantidad_egreso'];
                    $total_valor_eg += $record['valor_egreso'];
                    $total_cant_sf += $record['cantidad_saldo_final'];
                    $total_valor_sf += $record['valor_saldo_final'];
                    $row_subtotal[] = $i;
                }
                $i++;
            }
        }
        $ar[] = array('TOTAL', '', '', $total_cant_si,
            $this->formatMoney($total_valor_si),
            $total_cant_in,
            $this->formatMoney($total_valor_in),
            $total_cant_eg,
            $this->formatMoney($total_valor_eg),
            $total_cant_sf,
            $this->formatMoney($total_valor_sf));

        $rows = count($ar) + $first + 1;
        $sheet->fromArray($ar, null, 'A' . ($first + 2));
        $sheet->mergeCells('A' . $rows . ':C' . $rows);
        $sheet->setSharedStyle($sharedStyle1, "A" . $first . ":K" . $rows);
        $sheet->getStyle('D' . ($first + 2) . ':K' . $rows)
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A1:A4')->applyFromArray($styleTitle);
        $sheet->getStyle('A6:A8')->getFont()->setBold(true);
        $sheet->getStyle('A' . $first . ':K' . ($first + 1))->applyFromArray($styleHeaderTable);
        $sheet->getStyle('A' . $rows . ':K' . $rows)->getFont()->setBold(true);
        if ($type == 'D') {
            foreach ($row_subtotal as $item) {
                $sheet->mergeCells('A' . $item . ':C' . $item);
                $sheet->getStyle('A' . $item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }
        $sheet->getStyle('A' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getColumnDimension('B')->setWidth(40);
    }

    function formatMoney($value)
    {
        return number_format($value, 2, ',', '.');
    }

    function convertDate($date)
    {
        return implode('/', array_reverse(explode('-', $date)));
    }

    function generarReporte()
    {
        $this->docexcel->setActiveSheetIndex(0);
        $this->imprimeDatos();
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }
}

?>