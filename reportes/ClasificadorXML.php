<?php
//fRnk: para plantilla de importación excel
include(dirname(__FILE__) . '/../../lib/DatosGenerales.php');
include(dirname(__FILE__) . "/../../lib/lib_modelo/conexion.php");

$xml = new SimpleXMLElement('<xml/>');
try {
    $cone = new conexion();
    $link = $cone->conectarpdo();
    if (isset($_GET['ingresogasto']))
        $sql = "SELECT DISTINCT con.desc_ingas AS nombre FROM pre.tconcepto_partida conp
                    inner join pre.tpartida par on par.id_partida = conp.id_partida
                    inner join param.tgestion ges on ges.id_gestion = par.id_gestion
                    left join  param.tconcepto_ingas con on conp.id_concepto_ingas = con.id_concepto_ingas
                    where par.codigo like '3%'
                    and ges.gestion = '2023'
                    order by con.desc_ingas";
    else
        $sql = "SELECT '['|| codigo_largo||']-'|| nombre AS nombre FROM alm.tclasificacion WHERE sw_transaccional='movimiento' ORDER BY nombre";
    $consulta = $link->query($sql);
    $consulta->execute();
    $data = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as $item) {
        $node = $xml->addChild('item');
        $node->addChild('nombre', $item["nombre"]);
    }
    Header('Content-type: text/xml');
    print($xml->asXML());
} catch (Exception $ex) {
    var_dump($ex);
}

