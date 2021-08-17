<?php
include_once("conex.php");

include dirname(__FILE__) . '/../../presap/controllers/PacienteController.php';
include dirname(__FILE__) . '/../../presap/service/StickerPDFService.php';

use Admisiones\controllers\PacienteController;
use Admisiones\service\StickerPDFService;

$wEmp = empty($_GET['wemp_pmla']) ? 0 : $_GET['wemp_pmla'];
$nHis = empty($_GET['nHis']) ? 0 : $_GET['nHis'];
$nIng = empty($_GET['nIng']) ? 0 : $_GET['nIng'];
$empresa = empty($_GET['empresa']) ? null : $_GET['empresa'];
$json = empty($_GET['json']) ? false : $_GET['json'];

try {
    $paciente = new PacienteController($conex, $empresa, $nHis, $nIng);
    if ($json) {
        header('Content-Type: text/json');
        echo json_encode($paciente->jsonSerialize());
    } else {
        $pdf = new StickerPDFService('L', 'mm', [50, 38]);
        $pdf->generarSticker(
            $paciente->getNombreCompleto(),
            $paciente->getDocumento(),
            $paciente->getNumeroHistoria(),
            $paciente->getNumeroIngreso(),
            $paciente->getGenero(),
            $paciente->getEdad(),
            $paciente->getDiagnostico(),
            $paciente->getAseguradora(),
            $empresa
        );
        $pdf->Output();
    }
} catch (Exception $e) {
    die("Error intentando generar el sticker");
}

