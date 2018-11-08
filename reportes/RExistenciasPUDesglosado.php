<?php
//(Franklin Espinoza A.-02/08/2017)Certificacion Presupuestario
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');

class RExistenciasPUDesglosado extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;

    function Header() {

        $height = 6;
        $longHeight = 18;
        $this->Ln(3);

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 20,10,25,18);
        $this->ln(5);
        $this->SetFont('','B',12);
        $this->Cell(40, $longHeight, '', 'LRTB', 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(105, $longHeight, ' REPORTE DE EXISTENCIAS', 'LRTB', 0, 'C', false, '', 0, false, 'T', 'C');

        $x = $this->GetX();
        $y = $this->GetY();

        $this->SetFontSize(7);

        $width1 = 15;
        $width2 = 25;
        $this->SetXY($x, $y);

        $this->SetFont('', '');
        $this->Cell(40, $longHeight, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetXY($x, $y);
        $this->setCellPaddings(2);
        //$this->Cell($width1, $height, 'Codigo:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->Cell($width1 + $width2, $height, $this->datos[0]['nombre_almacen'], "B", 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetFont('', '');
        $y += $height;
        $this->SetXY($x, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height, 'Fecha:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->Cell($width2, $height, date("d/m/Y"), "B", 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetFont('', '');
        $y += $height;
        $this->SetXY($x, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height, 'Página:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->Cell($w = $width2, $h = $height, $txt = $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), $border = "B", $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

        //$this->Ln(2);

    }

    function setDatos($datos) {

        $this->datos = $datos;
        //var_dump( $this->datos);exit;
    }

    function  generarReporte(){

        $this->AddPage();
        $this->SetMargins(15, 40, 16);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $this->Ln(5);

        //variables para la tabla
        $id_item = '';

        $tbl =  $this->datos[0]['clasificacion'].'<br><table border="1" style="font-size: 6pt;">';
        $cont_total = 0;
        $contador = 1;


        $total_general = 0;
        $total_costo_general = 0;

        $total_ingresos = 0;
        $total_salidas  = 0;

        $total_costo= 0;
        $total_costo_salida = 0;

        /**********************************/
        $total_cantidad = 0;
        foreach( $this->datos as $record) {

            if($id_item==''){
                //<tr align="center"><td  colspan="5"><b>'.$record['codigo'].' '.$record['nombre'].'</b></td></tr>
                $tbl.='
                    
                    <tr align="center">
                        <td width="7%"><b>Nro.</b></td>
                        <td width="17%"><b>Descripción</b></td>
                        <td width="7%"><b>Unidad</b></td>
                        <td width="22%"><b>Saldo Actual</b></td>
                        <td width="24%"><b>Precio Unitario</b></td>
                        <td width="23%"><b>C/Total</b></td>
                     </tr>    
                ';
            }
            if($id_item!=$record["id_item"] && $id_item!=''){

                $total_costo_general+=$total_costo;

                $tbl.='<tr>
                           <td colspan="3" align="center" ><b>Total Grupo</b></td>
                           <td align="center" ><b>'.number_format($total_cantidad,0, ',', '.').'</b></td>
                           <td align="center" ><b>-</b></td>
                           <td align="right" ><b>'.number_format($total_costo,2, ',', '.').'</b></td>
                       </tr>';
                //<tr align="center"><td colspan="5"><b>'.$record['codigo'].' '.$record['nombre'].'</b></td></tr>
                $tbl.='
                    
                    <tr align="center">
                        <td width="7%"><b>Nro.</b></td>
                        <td width="17%"><b>Descripción</b></td>
                        <td width="7%"><b>Unidad</b></td>
                        <td width="22%"><b>Saldo Actual</b></td>
                        <td width="24%"><b>Precio Unitario</b></td>
                        <td width="23%"><b>C/Total</b></td>
                     </tr>      
                ';

                $total_cantidad = 0;
                $total_costo = 0;
            }

            $costo = $record["cantidad"] * $record["costo_unitario"];
            $tbl .= '   <tr>
                            <td  align="center">' . $contador . '</td>
                            <td  align="left"><b>'.$record['codigo'].' '.$record['nombre'].'</b></td>
                            <td  align="center"><br>' . $record["unidad_medida"] . '</td>
                            <td  align="center" >' . number_format($record["saldo_actual"], 0, ',', '.') . '</td>
                            <td  align="center" valign="center"><br>' . number_format($record["costo_unitario"], 0, ',', '.')  . '</td>
                            <td  align="right"><br>' . number_format($costo, 2, ',', '.') . '</td>
                        </tr>';

            $contador+=1;

            $total_cantidad += $record["saldo_actual"];
            $total_costo += $costo;


            $id_item = $record["id_item"];
        }


        $total_costo_general+=$total_costo;

        $tbl.='<tr>
                   <td colspan="3" align="center" ><b>Total Grupo</b></td>
                   <td align="center" ><b>'.number_format($total_cantidad,0, ',', '.').'</b></td>
                   <td align="center" ><b>-</b></td>
                   <td align="right" ><b>'.number_format($total_costo,2, ',', '.').'</b></td>
               </tr>
               <tr>
                   <td colspan="5" align="center" ><b> Total Costo General</b></td>
                   <td align="right" ><b>'.number_format($total_costo_general,2, ',', '.').'</b></td>
               </tr>';

        $tbl.='</table>';
        $this->writeHTML ($tbl);

        $contador = 1;
        $tbl = '<table border="1" style="font-size: 8pt;"> 
                <tr align="center"><td width="5%"><b>Nro.</b></td> <td width="80%"><b>Detalle</b></td> <td width="15%"><b>Costo</b></td></tr>
                <tr><td align="center">'.$contador.'</td><td> '.$this->datos[0]['clasificacion'].'</td><td align="center">'.number_format($total_costo_general,2, ',', '.').'</td></tr>
                </table>
                ';
        $this->Ln(5);
        $this->writeHTML ($tbl);


    }
}
?>
