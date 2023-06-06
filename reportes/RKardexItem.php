<?php
//fRnk: nuevo reporte Kardex Item Almacenes
require_once dirname(__FILE__) . '/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');

class RKardexItem extends ReportePDF
{
    var $datos;
    var $item;
    var $fecha_ini;
    var $fecha_fin;
    var $ancho_hoja;

    function Header()
    {
        $fini = explode('-', $this->fecha_ini);
        $ffin = explode('-', $this->fecha_fin);
        $gini = count($fini) > 2 ? intval($fini[0]) : '';
        $gfin = count($ffin) > 2 ? intval($ffin[0]) : '';
        $gestion = $gini != $gfin ? $gini . ' - ' . $gfin : $gini;
        $content = '<table border="1" cellpadding="1" style="font-size: 10px">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="4">
                    &nbsp;<img  style="width: 120px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 52%; color: #444444;text-align: center" rowspan="4">
                   <b style="font-size: 14px;">Kardex Item</b><br>
                   <span style="font-size: 12px">Del: ' . $this->convertDate($this->fecha_ini) . ' Al: ' . $this->convertDate($this->fecha_fin) . '</span><br/>
                   <b style="font-size: 12px">' . $this->item . '</b>
                </td>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;<b>Gestión:</b> ' . $gestion . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '</td>               
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;<b>Usuario:</b> ' . $_SESSION['_LOGIN'] . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;<b>Página:</b> ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</td>
            </tr>
        </table>';
        //$this->writeHTML($content, false, false, true, false, '');
        $this->writeHTMLCell(0, 10, 10, 8, $content, 0, 0, 0, true, 'L', true);
        $this->ln(29);
    }

    function setDatos($datos, $item, $fecha_ini, $fecha_fin)
    {
        $this->datos = $datos;
        $this->item = $item;
        $this->fecha_ini = substr($fecha_ini, 0, 10);
        $this->fecha_fin = substr($fecha_fin, 0, 10);
    }

    function getTableHeader()
    {
        return '
            <tr>
                <td style="width: 3%;"><b>N°</b></td>
                <td style="width: 8%;"><b>Fecha Solicitud</b></td>
                <td style="width: 8%;"><b>Fecha Salida</b></td>
                <td style="width: 12%;"><b>Num.Movimiento</b></td>
                <td style="width: 8%;"><b>Almacén</b></td>
                <td style="width: 8%;"><b>Motivo</b></td>
                <td style="width: 7%;"><b>Cantidad ingreso</b></td>
                <td style="width: 7%;"><b>Cantidad salida</b></td>
                <td style="width: 7%;"><b>Saldo Físico</b></td>
                <td style="width: 8%;"><b>Saldo Valorado</b></td>
                <td style="width: 8%;"><b>Costo Unitario</b></td>
                <td style="width: 8%;"><b>Valorado Ingreso</b></td>
                <td style="width: 8%;"><b>Valorado Salida</b></td>
            </tr>
        ';
    }

    function generarReporte()
    {
        $this->SetMargins(10, 30, 10);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->AddPage();
        $html = '<table border="1" cellpadding="1" style="width:100%;font-size: 8px;text-align: center">';
        $html .= $this->getTableHeader();
        $i = 1;
        foreach ($this->datos as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $i . '</td>';
            $html .= '<td>' . $this->convertDate(substr($record['fecha'], 0, 10)) . '</td>';
            $html .= '<td>' . $this->convertDate($record['fecha_salida']) . '</td>';
            $html .= '<td>' . $record['nro_mov'] . '</td>';
            $html .= '<td>' . $record['almacen'] . '</td>';
            $html .= '<td>' . $record['motivo'] . '</td>';
            $html .= '<td style="text-align: right">' . $this->formatNumber($record['ingreso'], 4) . '</td>';
            $html .= '<td style="text-align: right">' . $this->formatNumber($record['salida'], 4) . '</td>';
            $html .= '<td style="text-align: right">' . $this->formatNumber($record['saldo'], 4) . '</td>';
            $html .= '<td style="text-align: right">' . $this->formatNumber($record['saldo_val'], 4) . '</td>';
            $html .= '<td style="text-align: right">' . $this->formatNumber($record['costo_unitario'], 4) . '</td>';
            $html .= '<td style="text-align: right">' . $this->formatNumber($record['ingreso_val'], 4) . '</td>';
            $html .= '<td style="text-align: right">' . $this->formatNumber($record['salida_val'], 4) . '</td>';
            $html .= '</tr>';
            $i++;
        }
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
