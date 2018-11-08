<?php
//(Franklin Espinoza A.-02/08/2017)Certificacion Presupuestario
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');

class RExistenciasUpdate extends  ReportePDF{
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

    function  generarReporte()
    {

        $this->AddPage();
        $this->SetMargins(15, 40, 16);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $this->Ln(5);

        //variables para la tabla
        $id_item = '';
        $id_cp = 0;
        $cod_partida = '';
        $tbl =  $this->datos[0]['clasificacion'].'<br><table border="1" style="font-size: 6pt;">
            <tr align="center">
                <td width="5%"><b>Nro.</b></td>
                <td width="9%"><b>Fecha</b></td>
                <td width="17%"><b>Num. Movimiento</b></td>
                <td width="8%"><b>Codigo</b></td>
                <td width="17%"><b>Descripcion del Material</b></td>
                <td width="8%"><b>Tipo Movimiento</b></td>
                <td width="7%"><b>Unidad</b></td>
                <td width="7%"><b>Cantidad</b></td>
                <td width="10%"><b>Precio Unitario</b></td>
                <td width="12%"><b>C/Total</b></td>
             </tr>
        ';

        $cont_total = 0;
        $contador = 1;

        $total_parcial = 0;
        $total_costo_parcial = 0;

        $total_general = 0;
        $total_costo_general = 0;
        $codigo = '';

        $total_ingresos = 0;
        $total_salidas  = 0;

        $total_costo_ingreso = 0;
        $total_costo_salida = 0;

        foreach( $this->datos as $record) {

            if($id_item!=$record["id_item"] && $id_item!=''){
                $total_parcial = $total_ingresos - $total_salidas;
                $total_costo_parcial = $total_costo_ingreso - $total_costo_salida;
                $total_general += $total_parcial;
                $total_costo_general+=$total_costo_parcial;

                $tbl.='<tr>
                           <td colspan="7" align="center" ><b>Total Items ['.$codigo.']</b></td>
                           <td align="center" ><b>'.number_format($total_parcial,0, ',', '.').'</b></td>
                           <td align="center" ><b>-</b></td>
                           <td align="right" ><b>'.number_format($total_costo_parcial,2, ',', '.').'</b></td>
                       </tr>';
                $total_ingresos = 0;
                $total_salidas  = 0;
                $total_costo_ingreso = 0;
                $total_costo_salida = 0;
            }

            $cantidad = $record["tipo_movimiento"] == 'ingreso' ? $record["ingreso"] : $record["salida"];
            $costo = $record["tipo_movimiento"] == 'ingreso' ? $record["ingreso"] * $record["costo_unitario"] : $record["salida"] * $record["costo_unitario"];
            $tbl .= '   <tr>
                            <td  align="center">' . $contador . '</td>
                            <td  align="center">' . date_format(date_create($record["fecha"]),'d/m/Y'). '</td>
                            <td  align="left">' . $record["nro_mov"] . '</td>
                            <td  align="center"><br>' . $record["codigo"] . '</td>
                            <td  align="left"><br>' . $record["nombre"] . '</td>
                            <td  align="center"><br>' . $record["tipo_movimiento"] . '</td>
                            <td  align="center"><br>' . $record["unidad_medida"] . '</td>
                            <td  align="center" >' . number_format($cantidad, 0, ',', '.') . '</td>
                            <td  align="center" valign="center"><br>' . number_format($record["costo_unitario"], 0, ',', '.')  . '</td>
                            <td  align="right"><br>' . number_format($costo, 2, ',', '.') . '</td>
                        </tr>';

            $contador+=1;
            if($record["tipo_movimiento"] == 'ingreso'){
                $total_ingresos += $record["ingreso"];
                $total_costo_ingreso += $costo;
            }else{
                $total_salidas += $record["salida"];
                $total_costo_salida += $costo;
            }

            $id_item = $record["id_item"];
            $codigo = $record["codigo"];
        }

        $total_parcial = $total_ingresos - $total_salidas;
        $total_general += $total_parcial;

        $total_costo_parcial = $total_costo_ingreso - $total_costo_salida;
        $total_costo_general+=$total_costo_parcial;

        $tbl.='<tr>
                   <td colspan="7" align="center" ><b>Total Items ['.$codigo.']</b></td>                   
                   <td align="center" ><b>'.number_format($total_parcial,0, ',', '.').'</b></td>
                   <td align="center" ><b>-</b></td>
                   <td align="right" ><b>'.number_format($total_costo_parcial,2, ',', '.').'</b></td>
               </tr>
               <tr>
                   <td colspan="7" align="center" ><b> Total General '.$this->datos[0]['clasificacion'].'</b></td>
                   <td align="center" ><b>'.number_format($total_general,0, ',', '.').'</b></td>
                   <td align="center" ><b>-</b></td>
                   <td align="right" ><b>'.number_format($total_costo_general,2, ',', '.').'</b></td>
               </tr>';

        $tbl.='</table>';
        $this->writeHTML ($tbl);

        $contador = 1;
        $tbl = '<table border="1" style="font-size: 8pt;"> 
                <tr align="center"><td width="5%"><b>Nro.</b></td> <td width="80%"><b>Detalle</b></td> <td width="15%"><b>Costo</b></td></tr>
                <tr><td align="center">'.$contador.'</td><td> '.$this->datos[0]['clasificacion'].'</td><td align="center">'.number_format($total_costo_general, 2, ',', '.').'</td></tr>
                </table>
                ';
        $this->Ln(5);
        $this->writeHTML ($tbl);


    }

    function basico($numero) {
        $valor = array ('Uno','Dos','Tres','Cuatro','Cinco','Seis','Siete','Ocho',
            'Nueve','Diez','Once','Doce','Trece','Catorce','Quince','Dieciséis','Diecisiete',
            'Dieciocho','Diecinueve','Veinte','Veintiuno','Veintidós','Veintitrés','Veinticuatro','Veinticinco',
            'Veintiséis','Veintisiete','Veintiocho','Veintinueve');
        return $valor[$numero - 1];
    }

    function decenas($n) {
        $decenas = array (30=>'Treinta',40=>'Cuarenta',50=>'Cincuenta',60=>'Sesenta',
            70=>'Setenta',80=>'Ochenta',90=>'Noventa');
        if( $n <= 29) return $this->basico($n);
        $x = $n % 10;
        if ( $x == 0 ) {
            return $decenas[$n];
        } else
            return $decenas[$n - $x].' y '. $this->basico($x);
    }

    function centenas($n) {
        $cientos = array (100 =>'Cien',200 =>'Doscientos',300=>'Trecientos',
            400=>'Cuatrocientos', 500=>'Quinientos',600=>'Seiscientos',
            700=>'Setecientos',800=>'Ochocientos', 900 =>'Novecientos');
        if( $n >= 100) {
            if ( $n % 100 == 0 ) {
                return $cientos[$n];
            } else {
                $u = (int) substr($n,0,1);
                $d = (int) substr($n,1,2);
                return
                    (($u == 1)?'Ciento':$cientos[$u*100]).' '.$this->decenas($d);
            }
        } else
            return $this->decenas($n);
    }

    function miles($n) {
        if($n > 999) {
            if( $n == 1000) {return 'Mil';}
            else {
                $l = strlen($n);
                $c = (int)substr($n,0,$l-3);
                $x = (int)substr($n,-3);
                if($c == 1) {$cadena = 'Mil '.$this->centenas($x);}
                else if($x != 0) {$cadena = $this->centenas($c).' Mil '.$this->centenas($x);}
                else $cadena = $this->centenas($c). ' Mil';
                return $cadena;
            }
        } else return $this->centenas($n);
    }

    function millones($n) {
        if($n == 1000000) {return 'Un Millón';}
        else {
            $l = strlen($n);
            $c = (int)substr($n,0,$l-6);
            $x = (int)substr($n,-6);
            if($c == 1) {
                $cadena = ' Millón ';
            } else {
                $cadena = ' Millones ';
            }
            return $this->miles($c).$cadena.(($x > 0)?$this->miles($x):'');
        }
    }
    function convertir($n) {
        switch (true) {
            case ( $n >= 1 && $n <= 29) : return $this->basico($n); break;
            case ( $n >= 30 && $n < 100) : return $this->decenas($n); break;
            case ( $n >= 100 && $n < 1000) : return $this->centenas($n); break;
            case ($n >= 1000 && $n <= 999999): return $this->miles($n); break;
            case ($n >= 1000000): return $this->millones($n);
        }
    }

    function generarImagen($nom, $car, $ofi){
        $cadena_qr = 'Nombre: '.$nom. "\n" . 'Cargo: '.$car."\n".'Oficina: '.$ofi ;
        $barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,M');
        $png = $barcodeobj->getBarcodePngData($w = 8, $h = 8, $color = array(0, 0, 0));
        $im = imagecreatefromstring($png);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im, dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png");
            imagedestroy($im);

        } else {
            echo 'A ocurrido un Error.';
        }
        $url_archivo = dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png";

        return $url_archivo;
    }

}
?>
