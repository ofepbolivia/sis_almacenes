<?php
//incluimos la libreria
//echo dirname(__FILE__);
//include_once(dirname(__FILE__).'/../PHPExcel/Classes/PHPExcel.php');
class RMinisterioExistenciasXLS
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas=array();
    private $fila;
    private $equivalencias=array();

    private $indice, $m_fila, $titulo;
    private $swEncabezado=0; //variable que define si ya se imprimió el encabezado
    private $objParam;
    public  $url_archivo;


    function __construct(CTParametro $objParam){

        //reducido menos 23,24,26,27,29,30
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por BoA')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");



        $this->docexcel->setActiveSheetIndex(0);

        $this->equivalencias=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
            9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            50=>'AY',51=>'AZ',
            52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            76=>'BY',77=>'BZ');

    }

    function imprimeDatos(){
        $this->docexcel->getActiveSheet()->setTitle('Cantidad Clasificacion');
        $datos = $this->objParam->getParametro('datos');
        $columnas = 0;
        $this->docexcel->setActiveSheetIndex(0);



        $styleTitulos = array(
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


        $this->docexcel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->mergeCells('A1:G1');
        $this->docexcel->getActiveSheet()->mergeCells('A2:G2');
        $this->docexcel->getActiveSheet()->mergeCells('A3:G3');

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);



        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValue('A1','Bienes de Consumo');
        $this->docexcel->getActiveSheet()->setCellValue('A2','Al '.$this->objParam->getParametro('fecha_hasta'));
        $this->docexcel->getActiveSheet()->setCellValue('A3','(Expresado en Bolivianos)');

        $this->docexcel->getActiveSheet()->setCellValue('A4','Codigo');
        $this->docexcel->getActiveSheet()->setCellValue('B4','Descripción');
        $this->docexcel->getActiveSheet()->setCellValue('C4','Saldo Inicial');
        $this->docexcel->getActiveSheet()->setCellValue('D4','Ingresos');
        $this->docexcel->getActiveSheet()->setCellValue('E4','Salidas');
        $this->docexcel->getActiveSheet()->setCellValue('F4','Saldo Final');
        $this->docexcel->getActiveSheet()->setCellValue('G4','Grupo');

        //*************************************Detalle*****************************************
        $fila = 5;

        $color_pestana = array('FA8072','0095b6','e74c3c','138d75','a93226','229954','884ea0','1f618d','117a65');
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

        foreach($datos as $value) {
            //$columna = 0;

            if($value['codigo'] != $codigo  && $value['tamano'] == 1 && $codigo != ''){
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
                $this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($relleno);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$fila,$value['codigo']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$fila,$value['nombre']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$fila,$value['saldo_ini']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,$fila,$value['ingreso']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$fila,$value['salida']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,$fila,$value['saldo_fin']);//$columna++;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,$fila,'Total Clasificación '.$value['codigo']);

            }
            if($value['tamano'] == -1){
                $this->docexcel->getActiveSheet()->getStyle("A$fila:F$fila")->applyFromArray($normal);
            }

            if($value['tamano'] > 1){
                $this->docexcel->getActiveSheet()->getStyle("A$fila:F$fila")->applyFromArray($relleno);
            }



            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$fila,$value['codigo']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$fila,$value['nombre']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$fila,$value['saldo_ini']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,$fila,$value['ingreso']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$fila,$value['salida']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,$fila,$value['saldo_fin']);

            $fila++;
            $codigo = $value['codigo'];
        }

        //************************************************Fin Detalle***********************************************

    }

    function generarReporte(){
        //echo $this->nombre_archivo; exit;
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->docexcel->setActiveSheetIndex(0);
        $this->imprimeDatos();
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);

    }
}
?>