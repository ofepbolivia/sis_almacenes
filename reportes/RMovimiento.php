<?php
require_once dirname(__FILE__) . '/pxpReport/Report.php';
//require_once dirname(__FILE__) . '/../../lib/tcpdf/tcpdf.php';
//ini_set('display_errors', 'On');
class CustomReport extends MYPDF
{
    //fRnk: reporte solicitud de material y entrega de material
    private $dataSource;
    public $headerFistPage = true;

    public function setDataSource(DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function getDataSource()
    {
        return $this->dataSource;
    }

    public function Header()
    {
        $dataSource = $this->getDataSource();
        $data = $dataSource->getDataset();//var_dump($data);exit();
        if ($data[0]['tipo'] == 'salida') {//nombre_movimiento_tipo
            if ($data[0]['estado_mov'] == 'finalizado') {
                $titulo = 'ENTREGA DE MATERIAL';
            } else {
                $titulo = 'SOLICITUD DE MATERIAL';
            }
        } else {
            $titulo = strtoupper($data[0]['tipo']) . ' VALORADO DE MATERIALES';
        }
        $content = '<table border="1" cellpadding="1" style="font-size: 11px">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="3">
                    &nbsp;<img  style="width: 120px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 52%; color: #444444;text-align: center" rowspan="3">
                   <div style="margin-top: 5px"><b style="font-size: 14px;">' . $titulo . '</b></div>
                   <b style="font-size: 12px">' . $data[0]['nombre_almacen'] . '</b>
                </td>
                <td style="width: 25%; color: #444444; text-align: left;height: 20px">&nbsp;<b>Código:</b> ' . $data[0]['codigo_mov'] . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;height: 20px">&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;height: 20px">&nbsp;<b>Página:</b> ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</td>
            </tr>
        </table>';
        //$this->writeHTML($content, false, false, true, false, '');
        $this->writeHTMLCell(0, 10, 15, 8, $content, 0, 0, 0, true, 'L', true);
        $this->ln(29);

        if ($this->headerFistPage == false) {
            $this->headerFistPage = true;
        }
    }

    public function Footer()
    {
        //TODO: implement the footer manager
    }

}

class RMovimiento extends Report
{

    function write($fileName)
    {
        $pdf = new CustomReport(PDF_PAGE_ORIENTATION, PDF_UNIT, "LETTER", true, 'UTF-8', false);
        $pdf->headerFistPage = false;
        $pdf->SetFontSize(8);
        $pdf->setDataSource($this->getDataSource());
        $dataSource = $this->getDataSource();
        //var_dump($dataSource);exit();
        // set document information
        $pdf->SetCreator(PDF_CREATOR);

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


        if ($dataSource->getParameter('motivo') == "Inventario Inicial") {

            $wMargin = 30;
            $wNro = 10;
            $wDetalle = 110;
            $wTotal = 20;

            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFontSize(7);
            $pdf->SetFont('', 'B');
            $pdf->Cell($w = $wMargin, $h = $hGlobal, $txt = '', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wNro, $h = $hGlobal, $txt = 'Nro.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wDetalle, $h = $hGlobal, $txt = 'Detalle', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wTotal + 10, $h = $hGlobal, $txt = 'Costo', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Ln();
            $count = 1;
            $pdf->SetFont('', '');
            foreach ($dataSource->getDataSet() as $row) {
                $pdf->Cell($w = $wMargin, $h = $hGlobal, $txt = '', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $pdf->Cell($w = $wNro, $h = $hGlobal, $txt = $count, $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $pdf->Cell($w = $wDetalle, $h = $hGlobal, $txt = $row['nombreClasificacion'], $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $pdf->Cell($w = $wTotal + 10, $h = $hGlobal, $txt = number_format($row['totalClasificacion'], 6), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $pdf->Ln();
                $count++;
            }
            $pdf->SetFont('', 'B');
            $pdf->Cell($w = $wMargin, $h = $hGlobal, $txt = '', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wNro, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wDetalle, $h = $hGlobal, $txt = 'COSTO TOTAL', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wTotal + 10, $h = $hGlobal, $txt = number_format($dataSource->getParameter('costoTotal'), 6), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFont('', 'B');
            $pdf->Cell($w = 30, $h = $hMedium, $txt = 'OBSERVACIONES: ', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->SetFont('', '');
            $pdf->MultiCell($w = 0, $h = $hLong, $txt = $dataSource->getParameter('observaciones'), $border = 0, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);

            $pdf->AddPage();

            $pdf->SetFontSize(8);
            $pdf->SetFont('', 'B');

            $pdf->Cell($w = 0, $h = $hMedium, $txt = 'DETALLE DE MATERIALES', $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Ln();
            foreach ($dataSource->getParameter('clasificacionDataSources') as $clasificacionDataSource) {
                $this->writeClasificationDetail($pdf, $clasificacionDataSource, $dataSource, 2);
            }
        } else {
            if ($dataSource->getDataset()[0]['tipo'] == 'salida') {//nombre_movimiento_tipo
                if ($dataSource->getDataset()[0]['estado_mov'] == 'finalizado') {
                    $this->writeEntregaMaterial($pdf, $dataSource, 2);
                } else {
                    $this->writeSolicitudMaterial($pdf, $dataSource, 2);
                }
            } else {
                $this->writeGlobal($pdf, $dataSource, 2);
            }
        }

        $pdf->Output($fileName, 'F');
    }

    function writeClasificationDetail($pdf, $dataSource, $dataMaestro, $numberDecimals)
    {
        $hGlobal = 5;
        $wNro = 10;
        $wCodigo = 15;
        //$wDescripcionItem = 90;
        $wDescripcionItem = 95;
        $wUnidad = 15;
        $wCantidad = /*20*/
            30;
        $wCostoUnitario = 15;
        $wCostoTotal = 20;
        $pdf->Ln();
        $cantTot = 0;
        $costoUnitTot = 0;

        $pdf->SetFontSize(7);
        $pdf->SetFont('', 'B');

        $pdf->Cell($w = $wDescripcion, $h = $hGlobal, $txt = '* ' . $dataSource->getParameter('nombreClasificacion'), $border = 0, $ln = 1, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

        $pdf->SetFontSize(6.5);
        $pdf->SetFont('', 'B');
        $pdf->Cell($w = $wNro - 2, $h = $hGlobal, $txt = 'Nro.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = 'Código', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = /*$wDescripcionItem-10*/ $wDescripcionItem, $h = $hGlobal, $txt = 'Descripcion del Material', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wUnidad, $h = $hGlobal, $txt = 'Unidad', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = 'Cantidad Sol.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = 'Cantidad Real', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

        /*if($dataMaestro->getParameter('costos') == 'si'){
            $pdf->Cell($w = $wCostoUnitario, $h = $hGlobal, $txt = 'Costo Unit.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCostoTotal, $h = $hGlobal, $txt = 'Costo Tota', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        }*/

        $pdf->Ln();

        $count = 1;
        $pdf->SetFont('', '');
        foreach ($dataSource->getDataset() as $datarow) {

            $pdf->Cell($w = $wNro - 2, $h = $hGlobal, $txt = $count, $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = $datarow['codigo'], $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = /*$wDescripcionItem-10*/ $wDescripcionItem, $h = $hGlobal, $txt = $datarow['nombre'], $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wUnidad, $h = $hGlobal, $txt = $datarow['unidad_medida'], $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($datarow['cantidad_solicitada'], 2), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($datarow['cantidad'], 2), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            if ($dataMaestro->getParameter('costos') == 'si') {
                $pdf->Cell($w = $wCostoUnitario, $h = $hGlobal, $txt = number_format($datarow['costo_unitario'], $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $pdf->Cell($w = $wCostoTotal, $h = $hGlobal, $txt = $datarow['costo_total'], $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            }
            $pdf->Ln();
            $count++;
            $cantTot += $datarow['cantidad'];
            $costoUnitTot += $datarow['costo_unitario'];
        }

        $pdf->SetFont('', 'B');
        $pdf->Cell($w = $wNro - 2, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = /*$wDescripcionItem-$wCantidad+10*/ $wDescripcionItem, $h = $hGlobal, $txt = 'Totales', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wUnidad, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($cantTot, $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($cantTot, $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        if ($dataMaestro->getParameter('costos') == 'si') {
            $pdf->Cell($w = $wCostoUnitario, $h = $hGlobal, $txt = number_format($costoUnitTot, $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $pdf->Cell($w = $wCostoTotal, $h = $hGlobal, $txt = number_format($dataSource->getParameter('totalCosto'), $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        }
        $pdf->Ln();
    }

    function writeSolicitudMaterial($pdf, $dataSource, $numberDecimals)
    {
        $tmpDatos = $dataSource->getDataset();
        $html = '<table border="0" cellpadding="1" style="font-size: 10px;width: 100%">';
        $html .= '<tr><td width="20%"><b>SOLICITANTE:</b></td><td width="80%">' . $dataSource->getParameter('funcionario_solicitante') . '</td></tr>';
        $html .= '<tr><td><b>CARGO:</b></td><td>' . $tmpDatos[0]['descripcion_cargo'] . '</td></tr>';
        $html .= '<tr><td><b>UNIDAD:</b></td><td>' . $tmpDatos[0]['nombre_unidad'] . '</td></tr>';
        $html .= '<tr><td><b>FECHA DE LA SOLICITUD:</b></td><td>' . $tmpDatos[0]['fecha_mov'] . '</td></tr>';
        $desc = empty($tmpDatos[0]['descripcion']) ? '' : $tmpDatos[0]['descripcion'];
        $html .= '<tr><td><b>DESCRIPCIÓN:</b></td><td>' . $desc . '<br/></td></tr>';
        $html .= '</table>';
        $pdf->writeHTML($html, false, false, true, false, '');

        $total_sol = $total_ent = 0;
        $html = '<table border="1" cellpadding="1" style="font-size: 9px;text-align: center">';
        $html .= '<tr><td width="4%" rowspan="2" style="font-weight: bold">N°</td>
                <td width="10%" rowspan="2" style="font-weight: bold">CÓDIGO</td>
                <td width="34%" rowspan="2" style="font-weight: bold">DETALLE</td>
                <td width="10%" rowspan="2" style="font-weight: bold">UNIDAD DE MEDIDA</td>
                <td width="20%" colspan="2" style="font-weight: bold">CANTIDAD</td>
                <td width="22%" rowspan="2" style="font-weight: bold">OBSERVACIONES</td></tr>
                <tr><td style="font-weight: bold">SOLICITADA</td><td style="font-weight: bold">ENTREGADA</td></tr>';
        $i = 1;
        foreach ($dataSource->getDataSet() as $datarow) {

            $html .= '<tr>';
            $html .= '<td>' . $i . '</td>';
            $html .= '<td style="text-align: right">' . $datarow['codigo'] . '</td>';
            $html .= '<td style="text-align: left">' . $datarow['nombre'] . '</td>';
            $html .= '<td>' . $datarow['unidad_medida'] . '</td>';
            $html .= '<td>' . number_format($datarow['cantidad_solicitada'], 0, ',', '.') . '</td>';
            $html .= '<td>' . number_format($datarow['cantidad'], 0, ',', '.') . '</td>';
            $html .= '<td></td>';
            $html .= '</tr>';
            $i++;

            $total_ent += $datarow['cantidad'];
            $total_sol += $datarow['cantidad_solicitada'];
        }
        $html .= '<tr><td colspan="4"><b>TOTALES</b></td>
        <td><b>' . number_format($total_sol, 0, ',', '.') . '</b></td>
        <td><b>' . number_format($total_ent, 0, ',', '.') . '</b></td><td></td></tr>';
        $html .= '</table>';
        $pdf->writeHTML($html, false, false, true, false, '');
        $pdf->Ln(8);
        if ($dataSource->getDataSet()[0]['tipo'] == "salida") {
            $html = '<table border="1" cellpadding="1" style="text-align: center">';
            $html .= '<tr><td><b>SOLICITADO POR:</b></td><td><b>AUTORIZADO POR:</b></td><td><b>AUTORIZADO POR:</b></td></tr>';
            $html .= '<tr><td height="20px">' . $dataSource->getParameter('funcionario_solicitante') . '</td>
                    <td>' . $dataSource->getDataSet()[0]['func_autorizado1'] . '</td>
                    <td>' . $dataSource->getDataSet()[0]['func_autorizado2'] . '</td></tr>';
            $html .= '</table>';
            $pdf->writeHTML($html, false, false, true, false, '');
        }
    }

    function writeEntregaMaterial($pdf, $dataSource, $numberDecimals)
    {
        $tmpDatos = $dataSource->getDataset();
        $total_sol = $total_ent = $total_costo = 0;
        $html = '<table border="1" cellpadding="1" style="font-size: 9px;text-align: center">';
        $html .= '<tr><td width="3%" rowspan="2" style="font-weight: bold">N°</td>
                <td width="7%" rowspan="2" style="font-weight: bold">CÓDIGO</td>
                <td width="30%" rowspan="2" style="font-weight: bold">DETALLE</td>
                <td width="10%" rowspan="2" style="font-weight: bold">UNIDAD DE MEDIDA</td>
                <td width="10%" rowspan="2" style="font-weight: bold">FECHA DE VENCIMIENTO</td>
                <td width="20%" colspan="2" style="font-weight: bold">CANTIDAD</td>
                <td width="10%" rowspan="2" style="font-weight: bold">COSTO UNITARIO</td>
                <td width="10%" rowspan="2" style="font-weight: bold">COSTO TOTAL</td></tr>
                <tr><td style="font-weight: bold">SOLICITADA</td><td style="font-weight: bold">ENTREGADA</td></tr>';
        $fuentes = '';
        $i = 1;
        foreach ($dataSource->getDataSet() as $datarow) {
            $html .= '<tr>';
            $html .= '<td>' . $i . '</td>';
            $html .= '<td style="text-align: right">' . $datarow['codigo'] . '</td>';
            $html .= '<td style="text-align: left">' . $datarow['nombre'] . '</td>';
            $html .= '<td>' . $datarow['unidad_medida'] . '</td>';
            $html .= '<td>'.implode('/', array_reverse(explode('-', $datarow['fecha_vencimiento']))).'</td>';
            $html .= '<td>' . number_format($datarow['cantidad_solicitada'], 0, ',', '.') . '</td>';
            $html .= '<td>' . number_format($datarow['cantidad'], 0, ',', '.') . '</td>';
            $html .= '<td style="text-align: right">' . number_format($datarow['costo_unitario'], 2, ',', '.') . '</td>';
            $html .= '<td style="text-align: right">' . number_format($datarow['costo_total'], 2, ',', '.') . '</td>';
            $html .= '</tr>';
            $i++;

            $total_ent += $datarow['cantidad'];
            $total_sol += $datarow['cantidad_solicitada'];
            $total_costo += $datarow['costo_total'];
            $fuentes .= empty($datarow['fuente']) ? '' : $datarow['fuente'] . ', ';
        }
        $html .= '<tr><td colspan="5"><b>TOTALES</b></td>
        <td><b>' . number_format($total_sol, 0, ',', '.') . '</b></td>
        <td><b>' . number_format($total_ent, 0, ',', '.') . '</b></td>
        <td></td><td style="text-align: right">' . number_format($total_costo, 2, ',', '.') . '</td></tr>';
        $html .= '</table>';

        $cabecera = '<table border="0" cellpadding="1" style="font-size: 10px;width: 100%">';
        $cabecera .= '<tr><td width="18%"><b>ENTIDAD:</b></td><td width="82%">' . $tmpDatos[0]['entidad'] . '</td></tr>';
        $cabecera .= '<tr><td><b>FUENTE:</b></td><td>' . substr($fuentes, 0, strlen($fuentes) - 2) . '</td></tr>';
        $cabecera .= '<tr><td><b>ALMACEN:</b></td><td>' . $tmpDatos[0]['nombre_almacen'] . '</td></tr>';
        $cabecera .= '<tr><td><b>FECHA DE ENTREGA:</b></td><td>' . $tmpDatos[0]['fecha_entrega'] . '</td></tr>';
        $desc = empty($tmpDatos[0]['descripcion']) ? '' : $tmpDatos[0]['descripcion'];
        $cabecera .= '<tr><td><b>DESCRIPCIÓN:</b></td><td>' . $desc . '<br/></td></tr>';
        $cabecera .= '</table>';

        $pdf->writeHTML($cabecera . $html, false, false, true, false, '');
        $pdf->Ln(8);

        $html = '<table border="1" cellpadding="1" style="text-align: center">';
        $html .= '<tr><td><b>ENTREGUÉ CONFORME</b></td><td><b>V°B°</b></td><td><b>RECIBÍ CONFORME</b></td></tr>';
        $html .= '<tr><td height="20px">' . $tmpDatos[0]['func_entrega'] . '</td><td></td><td>' . $dataSource->getParameter('funcionario_solicitante') . '</td></tr>';
        $html .= '</table>';
        $pdf->writeHTML($html, false, false, true, false, '');

    }

    function writeGlobal($pdf, $dataSource, $numberDecimals)
    {
        $hGlobal = 5;
        $wNro = 10;
        $wCodigo = 15;
        $wDescripcionItem = /*90*/
            95;
        $wUnidad = 15;
        $wCantidad = /*20*/
            30;
        $wCostoUnitario = 20;
        $wCostoTotal = 20;
        $cantTot = 0;
        $cantTotSoli = 0;
        $costoUnitTot = 0;
        $pdf->Ln();

        $pdf->SetFontSize(6.5);
        $pdf->SetFont('', 'B');
        $pdf->Cell($w = $wNro - 2, $h = $hGlobal, $txt = 'Nro.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = 'Código', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = /*$wDescripcionItem-10*/ $wDescripcionItem, $h = $hGlobal, $txt = 'Descripcion del Material', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wUnidad, $h = $hGlobal, $txt = 'Unidad', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = 'Cantidad Sol.', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = 'Cantidad Real', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

        $pdf->Ln();

        $count = 1;
        $pdf->SetFont('', '');

        foreach ($dataSource->getDataSet() as $datarow) {

            if ($dataSource->getParameter('costos') == 'si') {
                /*$pdf->Cell($w = $wCostoUnitario, $h = $hGlobal, $txt = number_format($datarow['costo_unitario'], $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $pdf->Cell($w = $wCostoTotal, $h = $hGlobal, $txt = number_format($datarow['costo_total'], $numberDecimals), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');*/
                $pdf->tableborders = array('LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB');
                $pdf->tablewidths = array(8, 15,/*80*/
                    95, 15,/*20*/
                    30, 30/*20,15,20*/);
                $pdf->tablealigns = array('C', 'C', 'L', 'C', 'R', 'R'/*,'R','R'*/);
                $pdf->tablenumbers = array(0, 0, 0, 0, 0, 0/*,2,2*/);
                $RowArray = array(
                    's1' => $count,
                    's2' => $datarow['codigo'],
                    's3' => $datarow['nombre'] . ' - ' . $datarow['descripcion_item'],
                    's4' => $datarow['unidad_medida'],
                    's5' => number_format($datarow['cantidad_solicitada'], 0),
                    's6' => number_format($datarow['cantidad'], 0)/*,
                    's7' => number_format($datarow['costo_unitario'], $numberDecimals),
                    's8' => $datarow['costo_total']*/
                );
            } else {
                $pdf->tableborders = array('LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB');
                $pdf->tablewidths = array(8, 15,/*80*/
                    95, 15,/*20*/
                    30, 30/*20,15,20*/);
                $pdf->tablealigns = array('C', 'C', 'L', 'C', 'R', 'R'/*,'R','R'*/);
                $pdf->tablenumbers = array(0, 0, 0, 0, 0, 0/*,2,2*/);
                $RowArray = array(
                    's1' => $count,
                    's2' => $datarow['codigo'],
                    's3' => $datarow['nombre'] . ' - ' . $datarow['descripcion_item'],
                    's4' => $datarow['unidad_medida'],
                    's5' => number_format($datarow['cantidad_solicitada'], 0),
                    's6' => number_format($datarow['cantidad'], 0)/*,
                    's7' => number_format($datarow['costo_unitario'], $numberDecimals),
                    's8' => $datarow['costo_total']*/
                );
            }
            $pdf->MultiRow($RowArray, false, 1);
            //$pdf->Ln();
            $count++;
            $cantTot += $datarow['cantidad'];
            $cantTotSoli += $datarow['cantidad_solicitada'];
            $costoUnitTot += $datarow['costo_unitario'];
        }

        $pdf->SetFont('', 'B');
        $pdf->Cell($w = $wNro - 2, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCodigo, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = /*$wDescripcionItem-$wCantidad+10*/ $wDescripcionItem, $h = $hGlobal, $txt = 'Totales', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wUnidad, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($cantTotSoli, 0), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $pdf->Cell($w = $wCantidad, $h = $hGlobal, $txt = number_format($cantTot, 0), $border = 1, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

        $pdf->Ln();

        $pdf->Ln();
        $pdf->SetFont('', 'B');
        $pdf->Cell($w = 30, $h = $hGlobal, $txt = 'OBSERVACIONES: ', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

        $tmpDatos = $dataSource->getDataset();
        $hMin = 3.5;
        if ($tmpDatos[0]['observaciones'] != null && $tmpDatos[0]['observaciones'] != '') {
            $pdf->SetFont('', '');
            $pdf->MultiCell($w = 0, $h = $hLong, $txt = $tmpDatos[0]['observaciones'], $border = 0, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = true);
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
        }
    }
}

?>
