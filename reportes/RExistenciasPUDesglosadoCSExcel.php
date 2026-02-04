<?php

class RExistenciasPUDesglosadoCSExcel
{
    private $docexcel;
    private $objWriter;
    private $objParam;
    public  $url_archivo;

    // ---------------------------------------------------------------
    // Estilos reutilizables (se inicializan en el constructor)
    // ---------------------------------------------------------------
    private $styletitulo;          // título superior, centrado grande
    private $styleCabecera;        // cabecera de columnas: blanco sobre azul oscuro
    private $styleAgrupacion;      // fila de agrupación por item: blanco sobre azul medio
    private $styleDetallePar;      // fila detalle par (ingreso): fondo muy claro
    private $styleDetalleImpar;    // fila detalle impar (ingreso): blanco
    private $styleDetalleSalida;   // fila detalle salida: fondo amarillo claro
    private $styleSubtotal;        // fila subtotal item: gris medio, negrita
    private $styleSaldo;           // fila saldo existencia: verde claro, negrita
    private $styleTotalGeneral;    // fila totales generales: azul oscuro, blanco negrita
    private $styleResumenCab;      // hoja resumen cabecera
    private $styleResumenTitulo;   // hoja resumen título superior

    private $numberFormat = '#,##0.00';

    // ---------------------------------------------------------------
    // Constructor
    // ---------------------------------------------------------------
    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');

        set_time_limit(400);
        $cacheMethod  = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()
            ->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "' . $this->objParam->getParametro('titulo_archivo') . '", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");

        $this->_inicializarEstilos();
    }

    // ---------------------------------------------------------------
    // Definición centralizada de estilos
    // ---------------------------------------------------------------
    private function _inicializarEstilos()
    {
        // ── Título superior ──────────────────────────────────────
        $this->styletitulo = array(
            'font' => array('bold' => true, 'size' => 12, 'name' => 'Arial'),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        // ── Cabecera de columnas (azul oscuro #0066CC) ───────────
        $this->styleCabecera = array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial',
                            'color' => array('rgb' => 'FFFFFF')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '0066CC'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        // ── Fila agrupación por item (azul medio #3287c1) ────────
        $this->styleAgrupacion = array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial',
                            'color' => array('rgb' => 'FFFFFF')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3287c1'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        // ── Detalle par / impar (ingresos) ───────────────────────
        $this->styleDetallePar = array(
            'font' => array('size' => 9, 'name' => 'Arial'),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E8EBF4'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        $this->styleDetalleImpar = array(
            'font' => array('size' => 9, 'name' => 'Arial'),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFFFF'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        // ── Detalle salida (amarillo #FFF3CD) ────────────────────
        $this->styleDetalleSalida = array(
            'font' => array('size' => 9, 'name' => 'Arial'),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFF3CD'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        // ── Subtotal item (gris #E8E8E8, negrita) ────────────────
        $this->styleSubtotal = array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial'),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E8E8E8'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        // ── Saldo existencia (verde #D4EDDA, negrita) ────────────
        $this->styleSaldo = array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial'),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'D4EDDA'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        // ── Total general (azul oscuro #4472C4, blanco negrita) ──
        $this->styleTotalGeneral = array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial',
                            'color' => array('rgb' => 'FFFFFF')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '4472C4'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        // ── Resumen: título ──────────────────────────────────────
        $this->styleResumenTitulo = array(
            'font' => array('bold' => true, 'size' => 11, 'name' => 'Arial',
                            'color' => array('rgb' => 'FFFFFF')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '4472C4'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );

        // ── Resumen: cabecera de columnas ─────────────────────────
        $this->styleResumenCab = array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial',
                            'color' => array('rgb' => 'FFFFFF')),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'D6E4F0'),
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        );
        // Override font color para que sea oscura en el resumen cab
        $this->styleResumenCab['font']['color'] = array('rgb' => '000000');
    }

    // ---------------------------------------------------------------
    // Crear hojas y configurar anchos de columna
    // ---------------------------------------------------------------
    private function _prepararHojas()
    {
        // ── Hoja 0: Detalle ──────────────────────────────────────
        $this->docexcel->createSheet(0);
        $this->docexcel->setActiveSheetIndex(0);
        $this->docexcel->getActiveSheet()->setTitle('Detalle');

        // Anchos ajustados a cada columna (A–M)
        //                          A     B     C     D     E     F     G     H     I     J     K     L     M
        $anchos = array('A'=>22, 'B'=>18, 'C'=>18, 'D'=>14, 'E'=>14, 'F'=>24, 'G'=>12, 'H'=>38, 'I'=>16, 'J'=>10, 'K'=>12, 'L'=>14, 'M'=>16);
        foreach ($anchos as $col => $ancho) {
            $this->docexcel->getActiveSheet()->getColumnDimension($col)->setWidth($ancho);
        }

        // ── Hoja 1: Resumen ──────────────────────────────────────
        $this->docexcel->createSheet(1);
        $this->docexcel->setActiveSheetIndex(1);
        $this->docexcel->getActiveSheet()->setTitle('Resumen');

        //                           A     B     C     D     E     F     G     H
        $anchos2 = array('A'=>10, 'B'=>14, 'C'=>38, 'D'=>12, 'E'=>16, 'F'=>16, 'G'=>16, 'H'=>18);
        foreach ($anchos2 as $col => $ancho) {
            $this->docexcel->getActiveSheet()->getColumnDimension($col)->setWidth($ancho);
        }

        // Volver a Detalle
        $this->docexcel->setActiveSheetIndex(0);
    }

    // ---------------------------------------------------------------
    // Imprimir bloque de título en la hoja Detalle (filas 1-4)
    // ---------------------------------------------------------------
    private function _imprimeTituloDetalle()
    {
        $sheet = $this->docexcel->getSheet(0);
        $datos = $this->objParam->getParametro('datos');

        // Fila 1: Título sistema
        $sheet->setCellValue('A1', $_SESSION['_TITULO_SIS_LARGO']);
        $sheet->getStyle('A1:M1')->applyFromArray($this->styletitulo);
        $sheet->mergeCells('A1:M1');

        // Fila 2: "SALIDA DE ALMACEN"
        $sheet->setCellValue('A2', 'SALIDA DE ALMACEN');
        $sheet->getStyle('A2:M2')->applyFromArray($this->styleAgrupacion);
        $sheet->mergeCells('A2:M2');

        // Fila 3: Almacén + Código
        $sheet->setCellValue('A3', 'ALMACEN:');
        $sheet->setCellValue('B3', $datos[0]['nombre_almacen']);
        $sheet->setCellValue('H3', 'CODIGO:');
        $sheet->setCellValue('I3', $datos[0]['nro_tramite']);
        $sheet->getStyle('A3:M3')->applyFromArray(array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial'),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
        ));

        // Fila 4: Fecha
        $sheet->setCellValue('A4', 'FECHA:');
        $sheet->setCellValue('B4', date('d/m/Y'));
        $sheet->getStyle('A4:M4')->applyFromArray(array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial'),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
        ));
    }

    // ---------------------------------------------------------------
    // Imprimir cabecera de columnas en una fila dada (hoja Detalle)
    // ---------------------------------------------------------------
    private function _imprimeCabeceraColumnas($fila)
    {
        $sheet = $this->docexcel->getSheet(0);

        $cabeceras = array(
            0  => 'Nombre Solicitante',
            1  => 'Área Solicitante',
            2  => 'Cargo Solicitante',
            3  => 'Fecha Solic.',
            4  => 'Fecha Ent.',
            5  => 'Observaciones',
            6  => 'Código',
            7  => 'Descripción Material / Suministro',
            8  => 'Grupo',
            9  => 'Unidad',
            10 => 'Cantidad',
            11 => 'Precio Unit.',
            12 => 'Costo Total',
        );

        foreach ($cabeceras as $col => $texto) {
            $sheet->setCellValueByColumnAndRow($col, $fila, $texto);
        }
        $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->styleCabecera);
    }

    // ---------------------------------------------------------------
    // Aplicar formato numérico a celdas específicas de una fila
    //   $columnas: array de índices 0-based, ej array(10,11,12) = K,L,M
    // ---------------------------------------------------------------
    private function _formatNumero($sheet, $fila, array $columnas)
    {
        $letras = array('A','B','C','D','E','F','G','H','I','J','K','L','M');
        foreach ($columnas as $idx) {
            $sheet->getStyle($letras[$idx] . $fila)
                  ->getNumberFormat()
                  ->setFormatCode($this->numberFormat);
        }
    }

    // ---------------------------------------------------------------
    // Aplicar formato de fecha a celdas específicas
    // ---------------------------------------------------------------
    private function _formatFecha($sheet, $fila, array $columnas)
    {
        $letras = array('A','B','C','D','E','F','G','H','I','J','K','L','M');
        foreach ($columnas as $idx) {
            $sheet->getStyle($letras[$idx] . $fila)
                  ->getNumberFormat()
                  ->setFormatCode('dd/mm/yyyy');
        }
    }

    // ---------------------------------------------------------------
    // Escribir una fila de subtotal en la hoja Detalle
    //   La estructura replica el PDF: "Subtotal Item:" en columnas
    //   A–J (merged), luego Cantidad, (vacio), Costo Neto en K, L, M
    // ---------------------------------------------------------------
    private function _escribirSubtotal($sheet, $fila, $sub_cantidad_ingreso, $sub_cantidad_salida, $sub_costo_neto)
    {
        $sheet->mergeCells('A' . $fila . ':J' . $fila);
        $sheet->setCellValue('A' . $fila, 'Subtotal Item:');
        // Cantidad: "Ing / Sal"
        $sheet->setCellValue('K' . $fila, $sub_cantidad_ingreso . ' / ' . number_format($sub_cantidad_salida, 2));
        // L vacío
        $sheet->setCellValue('L' . $fila, '');
        // Costo neto
        $sheet->setCellValue('M' . $fila, $sub_costo_neto);
        $this->_formatNumero($sheet, $fila, array(12)); // M

        $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->styleSubtotal);
    }

    // ---------------------------------------------------------------
    // Escribir una fila de saldo existencia
    // ---------------------------------------------------------------
    private function _escribirSaldo($sheet, $fila, $saldo_actual)
    {
        $sheet->mergeCells('A' . $fila . ':J' . $fila);
        $sheet->setCellValue('A' . $fila, 'Saldo Existencia:');
        $sheet->setCellValue('K' . $fila, $saldo_actual);
        $sheet->setCellValue('L' . $fila, '');
        $sheet->setCellValue('M' . $fila, '');
        $this->_formatNumero($sheet, $fila, array(10)); // K

        $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->styleSaldo);
    }

    // ---------------------------------------------------------------
    // GENERADOR PRINCIPAL
    // ---------------------------------------------------------------
    function generarDatos()
    {
        $this->_prepararHojas();
        $this->_imprimeTituloDetalle();

        $sheet   = $this->docexcel->getSheet(0);
        $datos   = $this->objParam->getParametro('datos');
        // var_dump($datos); exit;        

        // Primera cabecera de columnas en fila 5
        $this->_imprimeCabeceraColumnas(5);
        $fila = 6; // primera fila de datos

        // ── Acumuladores globales ──────────────────────────────────
        $total_ingresos      = 0;
        $total_salidas       = 0;
        $total_costo_general = 0;
        $total_costo_salida  = 0;

        // ── Acumuladores por item ──────────────────────────────────
        $sub_cantidad_ingreso = 0;
        $sub_cantidad_salida  = 0;
        $sub_costo_ingreso    = 0;
        $sub_costo_salida     = 0;

        $id_item_actual = null;
        $saldo_actual   = 0;
        $contador_detalle = 0; // par/impar dentro de un item

        // ── Resumen (hoja 1): preparar título + cabecera ──────────
        $sheetRes = $this->docexcel->getSheet(1);

        // Fila 1: título merged
        $sheetRes->mergeCells('A1:H1');
        $sheetRes->setCellValue('A1', 'RESUMEN - EXISTENCIAS PU DESGLOSADO');
        $sheetRes->getStyle('A1:H1')->applyFromArray($this->styleResumenTitulo);

        // Fila 2: almacén
        $sheetRes->setCellValue('A2', 'ALMACEN:');
        $sheetRes->setCellValue('B2', $datos[0]['nombre_almacen']);
        $sheetRes->getStyle('A2:H2')->applyFromArray(array(
            'font' => array('bold' => true, 'size' => 9, 'name' => 'Arial'),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
        ));

        // Fila 3: cabecera columnas del resumen
        $cabRes = array(0=>'Nro', 1=>'Código', 2=>'Descripción', 3=>'Unidad',
                        4=>'Total Ingresos', 5=>'Total Salidas', 6=>'Saldo Cantidad', 7=>'Costo Neto');
        foreach ($cabRes as $col => $texto) {
            $sheetRes->setCellValueByColumnAndRow($col, 3, $texto);
        }
        $sheetRes->getStyle('A3:H3')->applyFromArray($this->styleResumenCab);

        $filaRes    = 4;  // primera fila de datos en resumen
        $nroResumen = 1;

        // ============================================================
        // BUCLE PRINCIPAL
        // ============================================================
        foreach ($datos as $record)
        {
            $cantidad    = (float) $record['cantidad'];
            $precio_unit = (float) $record['costo_unitario'];
            $costo_total = $cantidad * $precio_unit;
            $es_ingreso  = (strtoupper(trim($record['tipo_movimiento'])) === 'INGRESO');

            // ----------------------------------------------------------
            // CAMBIO DE ITEM: cerrar grupo anterior
            // ----------------------------------------------------------
            if ($id_item_actual !== null && $id_item_actual !== $record['id_item'])
            {
                // Fila subtotal
                $sub_costo_neto = $sub_costo_ingreso - $sub_costo_salida;
                $this->_escribirSubtotal($sheet, $fila, $sub_cantidad_ingreso, $sub_cantidad_salida, $sub_costo_neto);
                $fila++;

                // Fila saldo
                $this->_escribirSaldo($sheet, $fila, $saldo_actual);
                $fila++;

                // ── Escribir fila en hoja Resumen ────────────────────
                $sheetRes->setCellValueByColumnAndRow(0, $filaRes, $nroResumen);
                $sheetRes->setCellValueByColumnAndRow(1, $filaRes, $prev_codigo);
                $sheetRes->setCellValueByColumnAndRow(2, $filaRes, $prev_nombre);
                $sheetRes->setCellValueByColumnAndRow(3, $filaRes, $prev_unidad);
                $sheetRes->setCellValueByColumnAndRow(4, $filaRes, $sub_cantidad_ingreso);
                $sheetRes->setCellValueByColumnAndRow(5, $filaRes, $sub_cantidad_salida);
                $sheetRes->setCellValueByColumnAndRow(6, $filaRes, $saldo_actual);
                $sheetRes->setCellValueByColumnAndRow(7, $filaRes, $sub_costo_neto);
                $this->_formatNumero($sheetRes, $filaRes, array(4,5,6,7));
                $sheetRes->getStyle('A' . $filaRes . ':H' . $filaRes)->applyFromArray(array(
                    'font' => array('size' => 9, 'name' => 'Arial'),
                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
                ));
                $filaRes++;
                $nroResumen++;

                // Reiniciar subtotales
                $sub_cantidad_ingreso = 0;
                $sub_cantidad_salida  = 0;
                $sub_costo_ingreso    = 0;
                $sub_costo_salida     = 0;
                $contador_detalle     = 0;
            }

            // ----------------------------------------------------------
            // NUEVO ITEM: fila de agrupación (azul)
            // ----------------------------------------------------------
            if ($id_item_actual !== $record['id_item'])
            {
                $id_item_actual = $record['id_item'];
                $saldo_actual   = (float) $record['saldo_actual'];

                // Guardar datos del item para el resumen
                $prev_codigo = $record['codigo'];
                $prev_nombre = $record['nombre'];
                $prev_unidad = $record['unidad_medida'];

                // Fila merged con info del item
                $sheet->mergeCells('A' . $fila . ':F' . $fila);
                $sheet->setCellValue('A' . $fila, $record['codigo'] . '  -  ' . $record['nombre']);
                $sheet->setCellValue('G' . $fila, $record['codigo']);
                $sheet->setCellValue('H' . $fila, $record['nombre']);
                $sheet->setCellValue('I' . $fila, $record['clasificacion']);
                $sheet->setCellValue('J' . $fila, $record['unidad_medida']);
                // K, L, M vacíos en fila agrupación
                $sheet->setCellValue('K' . $fila, '');
                $sheet->setCellValue('L' . $fila, '');
                $sheet->setCellValue('M' . $fila, '');

                $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->styleAgrupacion);
                $fila++;
            }

            // ----------------------------------------------------------
            // ACUMULAR totales
            // ----------------------------------------------------------
            if ($es_ingreso) {
                $sub_cantidad_ingreso += $cantidad;
                $sub_costo_ingreso    += $costo_total;
                $total_ingresos       += $cantidad;
                $total_costo_general  += $costo_total;
            } else {
                $sub_cantidad_salida  += $cantidad;
                $sub_costo_salida     += $costo_total;
                $total_salidas        += $cantidad;
                $total_costo_salida   += $costo_total;
            }

            // ----------------------------------------------------------
            // FILA DE DETALLE
            // ----------------------------------------------------------
            // Convertir fechas a objetos DateTime para que Excel las
            // reconozca como fechas nativas (si existen)
            $fecha_mov    = !empty($record['fecha_mov'])    ? date('Y-m-d', strtotime($record['fecha_mov']))    : '';
            $fecha_salida = !empty($record['fecha_salida']) ? date('Y-m-d', strtotime($record['fecha_salida'])) : '';

            $sheet->setCellValueByColumnAndRow(0,  $fila, $record['nombre_solicitante']  ?? '');
            $sheet->setCellValueByColumnAndRow(1,  $fila, $record['area_solicitante']    ?? '');
            $sheet->setCellValueByColumnAndRow(2,  $fila, $record['cargo_solicitante']   ?? '');

            // Fechas: escribir como string si está presente
            if ($fecha_mov !== '') {
                $sheet->setCellValueByColumnAndRow(3, $fila, PHPExcel_Shared_Date::PHPToExcel(new DateTime($fecha_mov)));                
                $this->_formatFecha($sheet, $fila, array(3));
            }
            if ($fecha_salida !== '') {
                $sheet->setCellValueByColumnAndRow(4, $fila, PHPExcel_Shared_Date::PHPToExcel(new DateTime($fecha_salida)));
                $this->_formatFecha($sheet, $fila, array(4));
            }

            $sheet->setCellValueByColumnAndRow(5,  $fila, $record['observaciones']       ?? '');
            $sheet->setCellValueByColumnAndRow(6,  $fila, $record['codigo']);
            $sheet->setCellValueByColumnAndRow(7,  $fila, $record['nombre']);
            $sheet->setCellValueByColumnAndRow(8,  $fila, $record['clasificacion']);
            $sheet->setCellValueByColumnAndRow(9,  $fila, $record['unidad_medida']);
            $sheet->setCellValueByColumnAndRow(10, $fila, $cantidad);
            $sheet->setCellValueByColumnAndRow(11, $fila, $precio_unit);
            $sheet->setCellValueByColumnAndRow(12, $fila, $costo_total);

            // Formato numérico en K, L, M
            $this->_formatNumero($sheet, $fila, array(10, 11, 12));

            // Estilo según tipo de movimiento
            if ($es_ingreso) {
                $style = ($contador_detalle % 2 === 0) ? $this->styleDetallePar : $this->styleDetalleImpar;
            } else {
                $style = $this->styleDetalleSalida;
            }
            $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($style);

            $fila++;
            $contador_detalle++;
        }
        // fin foreach

        // ==============================================================
        // CERRAR ÚLTIMO GRUPO
        // ==============================================================
        if ($id_item_actual !== null)
        {
            $sub_costo_neto = $sub_costo_ingreso - $sub_costo_salida;
            $this->_escribirSubtotal($sheet, $fila, $sub_cantidad_ingreso, $sub_cantidad_salida, $sub_costo_neto);
            $fila++;
            $this->_escribirSaldo($sheet, $fila, $saldo_actual);
            $fila++;

            // Última fila en resumen
            $sheetRes->setCellValueByColumnAndRow(0, $filaRes, $nroResumen);
            $sheetRes->setCellValueByColumnAndRow(1, $filaRes, $prev_codigo);
            $sheetRes->setCellValueByColumnAndRow(2, $filaRes, $prev_nombre);
            $sheetRes->setCellValueByColumnAndRow(3, $filaRes, $prev_unidad);
            $sheetRes->setCellValueByColumnAndRow(4, $filaRes, $sub_cantidad_ingreso);
            $sheetRes->setCellValueByColumnAndRow(5, $filaRes, $sub_cantidad_salida);
            $sheetRes->setCellValueByColumnAndRow(6, $filaRes, $saldo_actual);
            $sheetRes->setCellValueByColumnAndRow(7, $filaRes, $sub_costo_neto);
            $this->_formatNumero($sheetRes, $filaRes, array(4,5,6,7));
            $sheetRes->getStyle('A' . $filaRes . ':H' . $filaRes)->applyFromArray(array(
                'font' => array('size' => 9, 'name' => 'Arial'),
                'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
            ));
            $filaRes++;
        }

        // ==============================================================
        // TOTALES GENERALES en hoja Detalle (2 filas al final)
        // ==============================================================
        $fila++; // línea en blanco de separación

        // Fila: "Total Ingresos"
        $sheet->mergeCells('A' . $fila . ':J' . $fila);
        $sheet->setCellValue('A' . $fila, 'Total Ingresos');
        $sheet->setCellValue('K' . $fila, $total_ingresos);
        $sheet->setCellValue('L' . $fila, ($total_ingresos > 0 ? $total_costo_general / $total_ingresos : 0));
        $sheet->setCellValue('M' . $fila, $total_costo_general);
        $this->_formatNumero($sheet, $fila, array(10, 11, 12));
        $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->styleTotalGeneral);
        $fila++;

        // Fila: "Total Salidas"
        $sheet->mergeCells('A' . $fila . ':J' . $fila);
        $sheet->setCellValue('A' . $fila, 'Total Salidas');
        $sheet->setCellValue('K' . $fila, $total_salidas);
        $sheet->setCellValue('L' . $fila, ($total_salidas > 0 ? $total_costo_salida / $total_salidas : 0));
        $sheet->setCellValue('M' . $fila, $total_costo_salida);
        $this->_formatNumero($sheet, $fila, array(10, 11, 12));
        $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->styleTotalGeneral);
        $fila++;

        // Fila: "Saldo General"
        $sheet->mergeCells('A' . $fila . ':J' . $fila);
        $sheet->setCellValue('A' . $fila, 'Saldo General (Ing. - Sal.)');
        $sheet->setCellValue('K' . $fila, $total_ingresos - $total_salidas);
        $sheet->setCellValue('L' . $fila, '');
        $sheet->setCellValue('M' . $fila, $total_costo_general - $total_costo_salida);
        $this->_formatNumero($sheet, $fila, array(10, 12));
        $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->styleTotalGeneral);

        // ==============================================================
        // TOTALES en hoja Resumen
        // ==============================================================
        $sheetRes->mergeCells('A' . $filaRes . ':C' . $filaRes);
        $sheetRes->setCellValue('A' . $filaRes, 'TOTALES');
        $sheetRes->setCellValue('D' . $filaRes, '');
        $sheetRes->setCellValueByColumnAndRow(4, $filaRes, $total_ingresos);
        $sheetRes->setCellValueByColumnAndRow(5, $filaRes, $total_salidas);
        $sheetRes->setCellValueByColumnAndRow(6, $filaRes, $total_ingresos - $total_salidas);
        $sheetRes->setCellValueByColumnAndRow(7, $filaRes, $total_costo_general - $total_costo_salida);
        $this->_formatNumero($sheetRes, $filaRes, array(4,5,6,7));
        $sheetRes->getStyle('A' . $filaRes . ':H' . $filaRes)->applyFromArray($this->styleTotalGeneral);
    }

    // ---------------------------------------------------------------
    // Generar y guardar el archivo .xls
    // ---------------------------------------------------------------
    function generarReporte()
    {
        $this->generarDatos();
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }
}
?>