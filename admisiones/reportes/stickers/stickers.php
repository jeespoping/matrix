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
    $empresa = empty($_GET['empresa']) ? null : $_GET['empresa'];
    $json = empty($_GET['json']) ? false : $_GET['json'];

//    echo "$wEmp $nHis $nIng";

//$conex = obtenerConexionBD("matrix");

    $datosPaciente = new DatosPaciente($conex, $empresa, $nHis, $nIng);
    if($json){
        header('Content-Type: text/json');

        echo json_encode($datosPaciente->jsonSerialize());
    } else {
        try {
            $pdf = new StickerPDFController('L', 'mm', [50, 38]);
            $pdf->generarSticker($empresa,
                $datosPaciente->getNombreCompleto(),
                $datosPaciente->getDocumento(),
                $datosPaciente->getNumeroHistoria(),
                $datosPaciente->getNumeroIngreso(),
                $datosPaciente->getGenero(),
                $datosPaciente->getEdad(),
                $datosPaciente->getDiagnostico(),
                $datosPaciente->getAseguradora());
            $pdf->Output();
        } catch (Exception $e) {
            die("Error intentando generar el sticker");
        }

    }