<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');

class RExistenciasPUDesglosadoCS extends ReportePDF {
    var $datos;
    var $ancho_hoja;

    function Header()
    {
        $height     = 6;
        $longHeight = 18;

        $this->Ln(3);

        // LOGO
        $this->Image(
            dirname(__FILE__).'/../../lib/' . $_SESSION['_DIR_LOGO'],
            20, 13, 30, 18
        );

        $this->Ln(5);
        $this->SetFont('', 'B', 12);

        // =========================
        // BLOQUE 1: LOGO
        // =========================
        $this->Cell(40, $longHeight, '', 1, 0, 'C');

        // =========================
        // BLOQUE 2: TITULO / ALMACEN
        // =========================
        $xCentro = $this->GetX();
        $yInicio = $this->GetY();

        $this->Cell(155, $longHeight, '', 1, 0);

        // Título
        $this->SetXY($xCentro, $yInicio);
        $this->Cell(155, $height * 2, 'SALIDA DE ALMACEN', 0, 0, 'C');

        // Fila: ALMACEN
        $this->SetFont('', '');
        $this->SetXY($xCentro, $yInicio + ($height * 2));
        $this->Cell(35, $height, 'ALMACEN:', 'T', 0, 'C');

        $this->SetFont('', 'B');
        $this->Cell(120, $height, $this->datos[0]['nombre_almacen'], 'T', 0, 'C');

        // =========================
        // BLOQUE 3: CODIGO / FECHA / PAGINA
        // =========================
        $xDerecha = $this->GetX();
        $this->SetXY($xDerecha, $yInicio);

        $this->SetFontSize(7);
        $w1 = 18.5;
        $w2 = 35;

        $this->Cell($w1 + $w2, $longHeight, '', 1);

        // CODIGO
        $this->SetXY($xDerecha, $yInicio);
        $this->SetFont('');
        $this->Cell($w1, $height, ' CODIGO:', 'B', 0);
        $this->SetFont('', 'B');
        $this->Cell($w2, $height, $this->datos[0]['nro_tramite'], 'B', 0, 'C');

        // FECHA
        $this->SetXY($xDerecha, $yInicio + $height);
        $this->SetFont('');
        $this->Cell($w1, $height, ' FECHA:', 'B', 0);
        $this->SetFont('', 'B');
        $this->Cell($w2, $height, date('d/m/Y'), 'B', 0, 'C');

        // PAGINA
        $this->SetXY($xDerecha, $yInicio + ($height * 2));
        $this->SetFont('');
        $this->Cell($w1, $height, ' PAGINA:', 'B', 0);
        $this->SetFont('', 'B');
        $this->Cell(
            $w2, $height,
            $this->getAliasNumPage().' DE '.$this->getAliasNbPages(),
            'B', 0, 'C'
        );
    }

    function setDatos($datos) {
        $this->datos = $datos;
    }

    // ---------------------------------------------------------------
    // Función auxiliar: formatear fecha desde DB (Y-m-d) a d/m/Y
    // Si ya viene en otro formato o es null, retorna vacío
    // ---------------------------------------------------------------
    private function fmtFecha($val) {
        if (empty($val)) return '';
        // Si ya tiene formato d/m/Y lo retorna directo
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $val)) return $val;
        // Intenta parsear
        $ts = strtotime($val);
        return ($ts !== false) ? date('d/m/Y', $ts) : $val;
    }

    function generarReporte() {

        // Orientación landscape para que la tabla no se corte
        $this->AddPage('L');
        $this->SetMargins(12, 40, 12);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->Ln(5);

        // -------------------------------------------------------
        // Acumuladores globales
        // -------------------------------------------------------
        $total_ingresos       = 0;
        $total_salidas        = 0;
        $total_costo_general  = 0;   // costo de ingresos
        $total_costo_salida   = 0;   // costo de salidas

        // -------------------------------------------------------
        // Acumuladores por item (se reinician al cambiar id_item)
        // -------------------------------------------------------
        $sub_cantidad_ingreso = 0;
        $sub_cantidad_salida  = 0;
        $sub_costo_ingreso    = 0;
        $sub_costo_salida     = 0;

        $id_item_actual = null;
        $saldo_actual   = 0;

        // -------------------------------------------------------
        // HTML de la cabecera repetible (se inserta al inicio
        // y al abrir cada nuevo item)
        // -------------------------------------------------------
        $cabecera =
        '<tr style="font-weight:bold; background-color:#d9d9d9; font-size:5.5pt;">
            <td width="10%" align="center">Nombre Solicitante</td>
            <td width="9%"  align="center">Área Solicitante</td>
            <td width="9%"  align="center">Cargo Solicitante</td>
            <td width="6%"  align="center">Fecha Solic.</td>
            <td width="6%"  align="center">Fecha Ent.</td>
            <td width="8%"  align="center">Observaciones</td>
            <td width="6%"  align="center">Código</td>
            <td width="15%" align="center">Descripción Material / Suministro</td>
            <td width="7%"  align="center">Grupo</td>
            <td width="5%"  align="center">Unidad</td>
            <td width="5%"  align="center">Cantidad</td>
            <td width="6%"  align="center">Precio Unit.</td>
            <td width="8%"  align="center">Costo Total</td>
        </tr>';

        // -------------------------------------------------------
        // HTML buffer acumulador
        // -------------------------------------------------------
        $html = '<table border="1" cellpadding="1.5" cellspacing="0" width="100%" style="font-size:5.5pt;">';
        $html .= $cabecera;

        // -------------------------------------------------------
        // Bucle principal
        // -------------------------------------------------------
        foreach ($this->datos as $record) {

            $cantidad    = (float) $record['cantidad'];
            $precio_unit = (float) $record['costo_unitario'];
            $costo_total = $cantidad * $precio_unit;
            $es_ingreso  = (strtoupper(trim($record['tipo_movimiento'])) === 'INGRESO');

            // ------------------------------------------------------
            // Cambio de item → cerrar grupo anterior con subtotales
            // ------------------------------------------------------
            if ($id_item_actual !== null && $id_item_actual !== $record['id_item']) {

                // Fila subtotal item
                $html .=
                '<tr style="background-color:#e8e8e8;">
                    <td colspan="10" align="right" style="font-size:5.5pt;"><b>Subtotal Item:</b></td>
                    <td align="right" style="font-size:5.5pt;"><b>' . number_format($sub_cantidad_ingreso, 2) . ' / ' . number_format($sub_cantidad_salida, 2) . '</b></td>
                    <td align="right" style="font-size:5.5pt;"><b>&nbsp;</b></td>
                    <td align="right" style="font-size:5.5pt;"><b>' . number_format(($sub_costo_ingreso - $sub_costo_salida), 2) . '</b></td>
                </tr>';

                // Fila saldo existencia
                $html .=
                '<tr style="background-color:#d4edda;">
                    <td colspan="10" align="right" style="font-size:5.5pt;"><b>Saldo Existencia:</b></td>
                    <td align="right" style="font-size:5.5pt;"><b>' . number_format($saldo_actual, 2) . '</b></td>
                    <td align="right" style="font-size:5.5pt;">&nbsp;</td>
                    <td align="right" style="font-size:5.5pt;">&nbsp;</td>
                </tr>';

                // Reiniciar subtotales
                $sub_cantidad_ingreso = 0;
                $sub_cantidad_salida  = 0;
                $sub_costo_ingreso    = 0;
                $sub_costo_salida     = 0;
            }

            // ------------------------------------------------------
            // Nuevo item → fila de agrupación (azul, como en imagen)
            // ------------------------------------------------------
            if ($id_item_actual !== $record['id_item']) {
                $id_item_actual = $record['id_item'];
                $saldo_actual   = (float) $record['saldo_actual'];

                $html .=
                '<tr style="background-color:#b3d9ff; font-weight:bold; font-size:5.5pt;">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="center">' . htmlspecialchars($record['codigo'])        .'</td>
                    <td>'                . htmlspecialchars($record['nombre'])         .'</td>
                    <td align="center">' . htmlspecialchars($record['clasificacion']) .'</td>
                    <td align="center">' . htmlspecialchars($record['unidad_medida']) .'</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>';
            }

            // ------------------------------------------------------
            // Acumular totales
            // ------------------------------------------------------
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

            // ------------------------------------------------------
            // Fila de detalle del movimiento
            // Salidas en amarillo claro, ingresos en blanco
            // ------------------------------------------------------
            $bg = $es_ingreso ? '#ffffff' : '#fff3cd';

            $html .=
            '<tr style="background-color:' . $bg . '; font-size:5.5pt;">
                <td>'  . htmlspecialchars($record['nombre_solicitante']  ?? '') .'</td>
                <td>'  . htmlspecialchars($record['area_solicitante']    ?? '') .'</td>
                <td>'  . htmlspecialchars($record['cargo_solicitante']   ?? '') .'</td>
                <td align="center">' . $this->fmtFecha($record['fecha_mov'])    .'</td>
                <td align="center">' . $this->fmtFecha($record['fecha_salida']) .'</td>
                <td>'  . htmlspecialchars($record['observaciones']       ?? '') .'</td>
                <td align="center">' . htmlspecialchars($record['codigo'])      .'</td>
                <td>'  . htmlspecialchars($record['nombre'])                    .'</td>
                <td align="center">' . htmlspecialchars($record['clasificacion']) .'</td>
                <td align="center">' . htmlspecialchars($record['unidad_medida']) .'</td>
                <td align="right">'  . number_format($cantidad, 2)             .'</td>
                <td align="right">'  . number_format($precio_unit, 2)          .'</td>
                <td align="right">'  . number_format($costo_total, 2)          .'</td>
            </tr>';
        }
        // fin foreach

        // ------------------------------------------------------
        // Cerrar último grupo (subtotal + saldo)
        // ------------------------------------------------------
        if ($id_item_actual !== null) {
            $html .=
            '<tr style="background-color:#e8e8e8;">
                <td colspan="10" align="right" style="font-size:5.5pt;"><b>Subtotal Item:</b></td>
                <td align="right" style="font-size:5.5pt;"><b>' . number_format($sub_cantidad_ingreso, 2) . ' / ' . number_format($sub_cantidad_salida, 2) . '</b></td>
                <td align="right" style="font-size:5.5pt;"><b>&nbsp;</b></td>
                <td align="right" style="font-size:5.5pt;"><b>' . number_format(($sub_costo_ingreso - $sub_costo_salida), 2) . '</b></td>
            </tr>
            <tr style="background-color:#d4edda;">
                <td colspan="10" align="right" style="font-size:5.5pt;"><b>Saldo Existencia:</b></td>
                <td align="right" style="font-size:5.5pt;"><b>' . number_format($saldo_actual, 2) . '</b></td>
                <td align="right" style="font-size:5.5pt;">&nbsp;</td>
                <td align="right" style="font-size:5.5pt;">&nbsp;</td>
            </tr>';
        }

        // Cerrar tabla
        $html .= '</table>';

        // Volcar todo el HTML
        $this->WriteHTML($html);

        // ------------------------------------------------------
        // RESUMEN GENERAL (tabla pequeña al final)
        // ------------------------------------------------------
        $this->Ln(4);

        $saldo_cantidad = $total_ingresos - $total_salidas;
        $saldo_costo    = $total_costo_general - $total_costo_salida;

        $resumen =
        '<table border="1" cellpadding="2" cellspacing="0" width="60%" style="font-size:6.5pt;" align="right">
            <tr style="background-color:#4472c4; font-weight:bold;">
                <td colspan="4" align="center" style="color:#ffffff; font-size:7pt;">RESUMEN GENERAL</td>
            </tr>
            <tr style="background-color:#d6e4f0; font-weight:bold;">
                <td width="34%">Concepto</td>
                <td width="22%" align="right">Cantidad</td>
                <td width="22%" align="right">Precio Unit. Prom.</td>
                <td width="22%" align="right">Costo Total</td>
            </tr>
            <tr>
                <td>Total Ingresos</td>
                <td align="right">' . number_format($total_ingresos, 2)       .'</td>
                <td align="right">' . ($total_ingresos > 0 ? number_format($total_costo_general / $total_ingresos, 2) : '0.00') .'</td>
                <td align="right">' . number_format($total_costo_general, 2)  .'</td>
            </tr>
            <tr style="background-color:#fff3cd;">
                <td>Total Salidas</td>
                <td align="right">' . number_format($total_salidas, 2)        .'</td>
                <td align="right">' . ($total_salidas > 0 ? number_format($total_costo_salida / $total_salidas, 2) : '0.00') .'</td>
                <td align="right">' . number_format($total_costo_salida, 2)   .'</td>
            </tr>
            <tr style="background-color:#e2e3e5; font-weight:bold;">
                <td>Saldo (Ing. - Sal.)</td>
                <td align="right">' . number_format($saldo_cantidad, 2)       .'</td>
                <td align="right">&nbsp;</td>
                <td align="right">' . number_format($saldo_costo, 2)          .'</td>
            </tr>
        </table>';

        $this->WriteHTML($resumen);

        // ------------------------------------------------------
        // FIRMAS
        // ------------------------------------------------------
        $this->Ln(10);

        $firmas =
        '<table border="0" cellpadding="2" cellspacing="0" width="100%" style="font-size:6.5pt;">
            <tr>
                <td width="30%" align="center" style="border-bottom:1px solid #000000;">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td width="30%" align="center" style="border-bottom:1px solid #000000;">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td width="30%" align="center" style="border-bottom:1px solid #000000;">&nbsp;</td>
            </tr>
            <tr>
                <td width="30%" align="center">Firma Solicitante</td>
                <td width="5%">&nbsp;</td>
                <td width="30%" align="center">Firma Encargado Almacén</td>
                <td width="5%">&nbsp;</td>
                <td width="30%" align="center">Firma Supervisor</td>
            </tr>
            <tr>
                <td width="30%" align="center">Fecha: ___/___/______</td>
                <td width="5%">&nbsp;</td>
                <td width="30%" align="center">Fecha: ___/___/______</td>
                <td width="5%">&nbsp;</td>
                <td width="30%" align="center">Fecha: ___/___/______</td>
            </tr>
        </table>';

        $this->WriteHTML($firmas);
    }
}
?>