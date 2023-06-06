<?php
//fRnk: nuevo reporte Movimiento de Almacenes
require_once dirname(__FILE__) . '/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');

class RMovimientoAlmacenes extends ReportePDF
{
    var $datos;
    var $ancho_hoja;

    function Header()
    {
        //var_dump($this->datos); exit();
        $content = '<table border="1" cellpadding="1" style="font-size: 11px">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="2">
                    &nbsp;<img  style="width: 120px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 52%; color: #444444;text-align: center" rowspan="2">
                   <div style="margin-top: 5px"><b style="font-size: 14px;">MOVIMIENTO DE ALMACENES</b></div>
                   <b style="font-size: 12px">Del: ' . $this->convertDate($this->datos[0]['fecha_ini']) . ' Al: ' . $this->convertDate($this->datos[0]['fecha_hasta']) . '</b>
                </td>
                <td style="width: 25%; color: #444444; text-align: left;height:30px">&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;<b>Página:</b> ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</td>
            </tr>
        </table>';
        //$this->writeHTML($content, false, false, true, false, '');
        $this->writeHTMLCell(0, 10, 15, 8, $content, 0, 0, 0, true, 'L', true);
        $this->ln(29);
    }

    function setDatos($datos)
    {
        $this->datos = $datos;
    }

    function getTableHeader()
    {
        return '
            <tr>
                <td rowspan="2" width="6%" style="line-height:22px;"><b>CÓDIGO</b></td>
                <td rowspan="2" width="22%" style="line-height:22px;"><b>DETALLE</b></td>
                <td rowspan="2" width="8%"><b>UNIDAD DE MEDIDA</b></td>
                <td colspan="2" width="16%"><b>SALDOS INICIALES</b></td>
                <td colspan="2" width="16%"><b>INGRESOS</b></td>
                <td colspan="2" width="16%"><b>EGRESOS</b></td>
                <td colspan="2" width="16%"><b>SALDO FINAL</b></td>
            </tr>
            <tr>
                 <td><b>CANTIDAD</b></td>
                 <td><b>VALOR</b></td>
                 <td><b>CANTIDAD</b></td>
                 <td><b>VALOR</b></td>
                 <td><b>CANTIDAD</b></td>
                 <td><b>VALOR</b></td>
                 <td><b>CANTIDAD</b></td>
                 <td><b>VALOR</b></td>
            </tr>
        ';
    }

    function generarReporte()
    {
        $this->SetMargins(15, 30, 16);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->createPage('T');
        $this->createPage('D');
    }

    function createPage($type)
    {
        $this->AddPage();
        $total_cant_si = 0;
        $total_valor_si = 0;
        $total_cant_in = 0;
        $total_valor_in = 0;
        $total_cant_eg = 0;
        $total_valor_eg = 0;
        $total_cant_sf = 0;
        $total_valor_sf = 0;
        $html = '<table border="0" cellpadding="1" style="width:100%;font-size: 9px;">
                <tr><td width="7%"><b>Entidad:</b></td><td>' . $this->datos[0]['entidad'] . '</td></tr>
                <tr><td><b>Fuente:</b></td><td>' . $this->datos[0]['fuente_gral'] . '</td></tr>
                <tr><td><b>Almacén:</b></td><td>' . $this->datos[0]['nom_almacen'] . '</td></tr>
            </table><br/><br/>';
        $html .= '<table border="1" cellpadding="1" style="width:100%;font-size: 8px;text-align: center">';
        $html .= $this->getTableHeader();

        foreach ($this->datos as $record) {
            if ($type == 'T') {
                if ($record['nivel'] == 'total') {
                    $html .= '<tr>';
                    $html .= '<td style="text-align: right">' . $record['codigo'] . '</td>';
                    $html .= '<td style="text-align: left">' . $record['detalle'] . '</td>';
                    $html .= '<td>' . $record['unidad_medida'] . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_saldo_inicial'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_saldo_inicial']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_ingreso'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_ingreso']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_egreso'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_egreso']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_saldo_final'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_saldo_final']) . '</td>';
                    $html .= '</tr>';
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
                    $html .= '<tr><td style="text-align: right">' . $record['codigo'] . '</td>';
                    $html .= '<td style="text-align: left">' . $record['detalle'] . '</td>';
                    $html .= '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
                } elseif ($record['nivel'] == 'item') {
                    $html .= '<tr>';
                    $html .= '<td style="text-align: right">' . $record['codigo'] . '</td>';
                    $html .= '<td style="text-align: left">' . $record['detalle'] . '</td>';
                    $html .= '<td>' . $record['unidad_medida'] . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_saldo_inicial'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_saldo_inicial']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_ingreso'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_ingreso']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_egreso'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_egreso']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_saldo_final'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_saldo_final']) . '</td>';
                    $html .= '</tr>';
                } else {
                    $html .= '<tr>';
                    $html .= '<td colspan="3">SUBTOTAL</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_saldo_inicial'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_saldo_inicial']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_ingreso'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_ingreso']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_egreso'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_egreso']) . '</td>';
                    $html .= '<td>' . $this->formatNumber($record['cantidad_saldo_final'], 0) . '</td>';
                    $html .= '<td style="text-align: right">' . $this->formatNumber($record['valor_saldo_final']) . '</td>';
                    $html .= '</tr>';
                    $total_cant_si += $record['cantidad_saldo_inicial'];
                    $total_valor_si += $record['valor_saldo_inicial'];
                    $total_cant_in += $record['cantidad_ingreso'];
                    $total_valor_in += $record['valor_ingreso'];
                    $total_cant_eg += $record['cantidad_egreso'];
                    $total_valor_eg += $record['valor_egreso'];
                    $total_cant_sf += $record['cantidad_saldo_final'];
                    $total_valor_sf += $record['valor_saldo_final'];
                }
            }
        }
        $html .= '<tr style="font-weight: bold">';
        $html .= '<td colspan="3">TOTAL</td>';
        $html .= '<td>' . $this->formatNumber($total_cant_si, 0) . '</td>';
        $html .= '<td style="text-align: right">' . $this->formatNumber($total_valor_si) . '</td>';
        $html .= '<td>' . $this->formatNumber($total_cant_in, 0) . '</td>';
        $html .= '<td style="text-align: right">' . $this->formatNumber($total_valor_in) . '</td>';
        $html .= '<td>' . $this->formatNumber($total_cant_eg, 0) . '</td>';
        $html .= '<td style="text-align: right">' . $this->formatNumber($total_valor_eg) . '</td>';
        $html .= '<td>' . $this->formatNumber($total_cant_sf, 0) . '</td>';
        $html .= '<td style="text-align: right">' . $this->formatNumber($total_valor_sf) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $this->writeHTML($html, false, false, true, false, '');
    }

    function formatNumber($value, $decimals = 2)
    {
        return number_format($value, $decimals, ',', '.');
    }

    function convertDate($date)
    {
        return implode('/', array_reverse(explode('-', $date)));
    }
}

?>
