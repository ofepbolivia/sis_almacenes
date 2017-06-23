<?php
require_once dirname(__FILE__) . '/pxpReport/Report.php';
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/mypdf.php';

class CustomReportConsolidado extends MYPDF {

    private $dataSource;
    public $headerFistPage=true;

    public function setDataSource(DataSource $dataSource) {
        $this->dataSource = $dataSource;
    }

    public function getDataSource() {
        return $this->dataSource;
    }

    public function Header() {
        $height = 6;
        $midHeight = 9;
        $longHeight = 24;

        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetXY($x, $y);
        $dataSource = $this->getDataSource();

        $this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 19, 9, 36);

        $this->SetFontSize(14);
        $this->SetFont('', 'B');
        $this->Cell(44, $midHeight, '', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
        $tmpDatos=$dataSource->getDataset();
        $this->Cell(105, $midHeight, strtoupper($tmpDatos[0]['tipo']) . ' VALORADO DE MATERIALES', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');

        $x = $this->GetX();
        $y = $this->GetY();
        $this->Ln();
        $this->Cell(44, $midHeight, '', 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(105, $midHeight, $tmpDatos[0]['nombre_almacen'], 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetFontSize(7);

        $width1 = 15;
        $width2 = 25;
        $this->SetXY($x, $y);

        $this->SetFont('', '');
        $this->Cell(44, $longHeight, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetXY($x, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1-4, $height, 'Código:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->Cell($width2+8, $height, $tmpDatos[0]['codigo_tran'], "B", 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetFont('', '');
        $y += $height;
        $this->SetXY($x, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1+4, $height, 'Fecha:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->Cell($width2, $height, $tmpDatos[0]['fecha_mov'], "B", 0, 'L', false, '', 0, false, 'T', 'C');

        $this->SetFont('', '');
        $y += $height;
        $this->SetXY($x, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height, 'Página:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->Cell($w = $width2, $h = $height, $txt = $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), $border = "B", $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');


        $this->SetFont('', '');
        $y += $height;
        $this->SetXY($x, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height, 'Comail:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->Cell($w = $width2, $h = $height, $dataSource->getParameter('comail'), $border = "B", $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

    }

    public function Footer() {
        //TODO: implement the footer manager
    }

}

Class RMovimientoConsolidado extends Report {

    function write($fileName) {

        $pdf = new CustomReportConsolidado(PDF_PAGE_ORIENTATION, PDF_UNIT, "LETTER", true, 'UTF-8', false);
        $pdf->headerFistPage=false;
        $pdf->setDataSource($this->getDataSource());
        $dataSource = $this->getDataSource();
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        /*$pdf->SetAuthor('Nicola Asuni');
         $pdf->SetTitle('TCPDF Example 006');
         $pdf->SetSubject('TCPDF Tutorial');
         $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
         */

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(7);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        // $pdf->setLanguageArray($l);

        // add a page
        $pdf->AddPage();

        $hGlobal = 5;
        $hMin = 3.5;
        $hMedium = 7.5;
        $hLong = 15;

        //Carga el dataset en un temporal
        $tmpDatos=$dataSource->getDataset();

        $pdf->SetFontSize(7.5);
        $pdf->SetFont('', 'B');
        //$hMedium

        $pdf->Cell($w = 30, $h = 1, $txt = 'CODIGO DOTACIONES: ', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->SetFont('', '');
        $pdf->Cell($w = 60, $h = 1, $txt = $tmpDatos[0]['codigo_tran'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Ln();
        $pdf->Ln();

        $pdf->SetFontSize(7.5);
        $pdf->SetFont('', 'B');
        //$hMedium
        $pdf->Cell($w = 30, $h = 1, $txt = 'MOTIVO: ', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->SetFont('', '');
        $pdf->Cell($w = 60, $h = 1, $txt = $tmpDatos[0]['nombre_movimiento_tipo'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Ln();

        $pdf->SetFont('', 'B');
        $pdf->Ln();
        $pdf->Cell($w = 30, $h = $hMedium, $txt = 'DESCRIPCION: ', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

        if ($tmpDatos[0]['descripcion'] != null && $tmpDatos[0]['descripcion'] != '') {
            $pdf->SetFont('', '');
            if (strlen($dataSource->getParameter('descripcion')) > 150) {
                $pdf->MultiCell($w = 0, $h = $hLong, $txt = $tmpDatos[0]['descripcion'], $border = 0, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
            } else {
                $pdf->Cell($w = 0, $h = $hMedium, $txt = $tmpDatos[0]['descripcion'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $pdf->Ln();
            }
        } else {
            $pdf->Ln();
        }

        if ($tmpDatos[0]['tipo'] == "salida") {
            //Fecha del movimiento
            $pdf->SetFont('', 'B');
            $pdf->Cell($w = 30, $h = $hMedium, $txt = 'FECHA ' . strtoupper($tmpDatos[0]['tipo']) . ':', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->SetFont('', '');
            $pdf->Cell($w = 60, $h = $hMedium, $txt = $dataSource->getParameter('fechaSalida'), $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        }
        //Fecha de la ultima modificacion
        /*$pdf->SetFont('', 'B');
        $pdf->Cell($w = 30, $h = $hMedium, $txt = 'FECHA REMISION: ', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->SetFont('', '');
        $fechaRemision = new DateTime($dataSource->getParameter('fechaRemision'));
        $pdf->Cell($w = 60, $h = $hMedium, $txt = $tmpDatos[0]['fecha_mod'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        */

        $this->writeGlobalDetail($pdf, $dataSource, 2);
        $pdf->Ln();
        $pdf->SetFont('', 'B');
        $pdf->Cell($w = 30, $h = $hGlobal, $txt = 'OBSERVACIONES: ', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->SetFont('', '');
        if ($dataSource->getParameter('observaciones') != null && $dataSource->getParameter('observaciones') != '') {
            $pdf->MultiCell($w = 0, $h = $hMedium, $txt = $dataSource->getParameter('observaciones'), $border = 0, $align = 'L', $fill = false, $ln = 0, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
        }

        if ($tmpDatos[0]['tipo'] == "salida") {
            $wMargin = 0;
            $wColumn1 = 90;
            $wColumn2 = 90;

            $pdf->Ln();
            $pdf->Ln();
            $pdf->Cell($w = $wColumn2, $h = $hMin, $txt = '', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wColumn1, $h = $hMin, $txt = $dataSource->getParameter('solicitante'), $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Ln();
            $pdf->Cell($w = $wColumn1, $h = $hMin, $txt = 'ENTREGUE CONFORME', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wColumn2, $h = $hMin, $txt = 'RECIBI CONFORME', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Cell($w = $wColumn1, $h = $hGlobal, $txt = '____________________________', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wColumn2, $h = $hGlobal, $txt = '____________________________', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Ln();
            $pdf->Cell($w = $wColumn1, $h = $hGlobal, $txt = 'Entregado Por: ___________________________', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wColumn2, $h = $hGlobal, $txt = 'Recibido Por: ___________________________', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

            $pdf->Ln();
            $pdf->Ln();
            $this->writeSolicitantes($pdf, $dataSource, $pdf);
        }


        $pdf->Output($fileName, 'F');
    }

    function writeGlobalDetail($pdf, $dataSource, $numberDecimals) {

        $wNro = 10;
        $wCodigo = 15;
        $wDescripcionItem = 90;
        $wUnidad = 15;
        $wCantidad = 20;
        $wCostoUnitario = 15;
        $wCostoTotal = 20;
        $cantTot=0;
        $costoUnitTot=0;
        $hGlobal = 5;

        if($this->headerFistPage==false){
            $this->headerFistPage=true;
        } else{
            $this->Ln();
            $this->SetFontSize(6.5);
            $this->SetFont('', 'B');
            $this->Cell($w = $wNro-2, $h = $hGlobal, $txt = 'Nro.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->Cell($w = $wCodigo, $h = $hGlobal, $txt = 'Código', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->Cell($w = $wDescripcionItem-5, $h = $hGlobal, $txt = 'Descripcion del Material', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->Cell($w = $wUnidad-5, $h = $hGlobal, $txt = 'Unidad', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->Cell($w = $wCantidad, $h = $hGlobal, $txt = 'Cantidad Sol.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->Cell($w = $wCantidad, $h = $hGlobal, $txt = 'Cantidad Real', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->Cell($w = $wCostoUnitario, $h = $hGlobal, $txt = 'Costo Unit.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->Cell($w = $wCostoTotal, $h = $hGlobal, $txt = 'Costo Total', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->Ln();
        }

        $hGlobal = 5;
        $wNro = 10;
        $wCodigo = 15;
        $wDescripcionItem = 90;
        $wUnidad = 15;
        $wCantidad = 20;
        $wCostoUnitario = 15;
        $wCostoTotal = 20;
        $cantTot=0;
        $cantTotSoli=0;
        $costoUnitTot=0;
        $costoTotal=0;
        $pdf->Ln();

        $pdf->SetFontSize(6.5);
        $pdf->SetFont('', 'B');
        $pdf->Cell($w = $wNro-2, $h = $hGlobal, $txt = 'Nro.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = 'Código', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wDescripcionItem-35, $h = $hGlobal, $txt = 'Descripcion del Material', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wUnidad-5, $h = $hGlobal, $txt = 'Unidad', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = 'Cantidad Sol.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = 'Cantidad Real', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        if($dataSource->getParameter('costos') == 'si'){
            $pdf->Cell($w = $wCostoUnitario, $h = $hGlobal, $txt = 'Costo Unit.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCostoTotal, $h = $hGlobal, $txt = 'Costo Total', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        }
        $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = 'Estacion', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Ln();

        $count = 1;
        $pdf->SetFont('', '');

        foreach ($dataSource->getDataSet() as $datarow) {

            $pdf->Cell($w = $wNro-2, $h = $hGlobal, $txt = $count, $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = $datarow['codigo'], $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wDescripcionItem-35, $h = $hGlobal, $txt = $datarow['nombre']. ' - ' . $datarow['descripcion_item'], $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 1, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wUnidad-5, $h = $hGlobal, $txt = $datarow['unidad_medida'], $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($datarow['cantidad_solicitada'], 2), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($datarow['cantidad'], 2), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            if($dataSource->getParameter('costos') == 'si'){
                $pdf->Cell($w = $wCostoUnitario, $h = $hGlobal, $txt = number_format($datarow['costo_unitario'], $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $pdf->Cell($w = $wCostoTotal, $h = $hGlobal, $txt = number_format($datarow['costo_total'], $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            }
            $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = $datarow['lugar_nombre'], $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 1, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Ln();
            $count++;
            $cantTot+=$datarow['cantidad'];
            $cantTotSoli+=$datarow['cantidad_solicitada'];
            $costoUnitTot+=$datarow['costo_unitario'];
            $costoTotal+=$datarow['costo_total'];
        }

        $pdf->SetFont('', 'B');
        $pdf->Cell($w = $wNro-2, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wDescripcionItem-$wCantidad-15, $h = $hGlobal, $txt = 'Totales', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wUnidad-5, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($cantTot,$numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($cantTot,$numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        if($dataSource->getParameter('costos') == 'si'){
            $pdf->Cell($w = $wCostoUnitario, $h = $hGlobal, '', $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCostoTotal, $h = $hGlobal, $txt = $costoTotal, $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        }
        $pdf->Ln();
    }

    function writeSolicitantes($pdf, $dataSource, TCPDF $pdf) {
        $hGlobal = 5;
        $wNro = 10;
        $wCodigo = 15;
        $wDescripcionItem = 22;
        $wUnidad = 15;
        $wCantidad = 20;
        $wCostoUnitario = 15;
        $wCostoTotal = 20;
        $cantTot=0;
        $cantTotSoli=0;
        $costoUnitTot=0;
        $pdf->Ln();

        $pdf->SetFontSize(7);
        $pdf->SetFont('', 'B');
        $pdf->Cell($w = $wNro-2+$wDescripcionItem+$wCodigo, $h = $hGlobal, $txt = 'SOLICITANTES', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wDescripcionItem+$wCantidad+2, $h = $hGlobal, $txt = 'ITEMS', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wNro-4+$wDescripcionItem, $h = $hGlobal, $txt = 'ESTACION', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wNro-2+$wDescripcionItem, $h = $hGlobal, $txt = 'OFICINA', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad+$wCostoUnitario, $h = $hGlobal, $txt = 'FIRMAS', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Ln();

        $count = 1;
        $pdf->SetFont('', '');

        $dataset = $dataSource->getDataSet();
        $solicitantes = explode(',',$dataset[0]['nombre_funcionario']);

        $pdf->SetFillColor(255,255,255,true);
        $pdf->tablewidths=array($wNro-2+$wDescripcionItem+$wCodigo, $wDescripcionItem+$wCantidad+2,$wNro-4+$wDescripcionItem,$wNro-2+$wDescripcionItem,$wCantidad+$wCostoUnitario);
        $pdf->tablealigns=array('L','L','L','L','L');
        $pdf->tablenumbers=array(0,0,0,0,0);

        $RowArray;
        foreach ($solicitantes as $solicitante) {
            $datosSolicitante = explode('~',$solicitante);

            $RowArray = array(
                'solicitantes'  =>  $datosSolicitante[0],
                'items'  => $datosSolicitante[3],
                'estacion'    => $datosSolicitante[1],
                'oficina' => $datosSolicitante[2],
                'firmas' => ''
            );
            $pdf->MultiRow($RowArray, $fill = false, $border = 1);
        }

        $pdf->Ln();
    }
}
?>