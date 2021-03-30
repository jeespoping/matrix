<?php
    include_once("conex.php");

    include dirname(__FILE__) . '/../../presap/controllers/DatosSocioEconomicosController.php';
    include dirname(__FILE__) . '/../../presap/controllers/StickerPDFController.php';

    use Admisiones\Controller\DatosSocioEconomicosController as DatosPaciente;
    use Admisiones\Controller\StickerPDFController;

//    if (isset($_GET['wemp_pmla'])) {
//        $wEmp = $_GET['wemp_pmla'];
//    }
    $wEmp = empty($_GET['wemp_pmla']) ? 0 : $_GET['wemp_pmla'];
    $nHis = empty($_GET['nHis']) ? 0 : $_GET['nHis'];
    $nIng = empty($_GET['nIng']) ? 0 : $_GET['nIng'];

//    echo "$wEmp $nHis $nIng";

//$conex = obtenerConexionBD("matrix");

    $datosPaciente = new DatosPaciente($conex, $nHis, $nIng);
    header('Content-Type: text/json');

    echo $datosPaciente->getJsonData();
    //$pdf = new StickerPDFController('L', 'mm', array(50, 38));
    //
    //    try {
    //        $pdf->generarSticker('cliame',
    //            $datosPaciente->getNombreCompleto(),
    //            $datosPaciente->getDocumento(),
    //            $datosPaciente->getNumeroHistoria(),
    //            $datosPaciente->getNumeroIngreso(),
    //            $datosPaciente->getGenero(),
    //            $datosPaciente->getEdad(),
    //            $datosPaciente->getDiagnostico(),
    //            $datosPaciente->getAseguradora());
    //    } catch (Exception $e) {
    //        die("Error intentando generar el sticker");
    //    }
    //
    //    $pdf->Output();
