<?php
include_once("conex.php");

include dirname(__FILE__) . '/../../presap/controller/DatosSocioeconomicosController.php';
include dirname(__FILE__) . '/../../presap/controller/StickerPDFController.php';


use Admisiones\Controller\{DatosSocioeconomicosController as DatosPaciente, StickerPDFController};

if (isset($_POST['wEmp'])) {
    $wEmp = $_POST['wEmp'];
}

if (!empty($_POST['nHis'])) {
    $nHis = $_POST['nHis'];
}

if (!empty($POST['nIng'])) {
    $nIng = $_POST['nIng'];
}

$datosPaciente = new DatosPaciente($conex, $nHis, $nIng);

if ($datosPaciente->validarDatos()) {
    $datosPaciente->getDatosPaciente();
//    $datosPaciente->getDatosDiagnostico();
}

$pdf = new StickerPDFController('L', 'mm', array(50, 38));

$pdf->AddPage();
$pdf->SetMargins(1, 1, 1);
$pdf->SetFont('Arial', 'B', 8);

$pdf->Celda(1, 2, $datosPaciente->getNombreCompleto(), 'C');
//$pdf->Code39(15, 4, $datosPaciente->getDocumento(), 0.4, 10);
$pdf->Image(dirname(__FILE__) . '/../../../images/medical/root/cliame.jpg', 1, 4, 14, 10, "jpg");
$pdf->Code39(15, 4, 'PEP 807502426011994', 0.40, 10);
$pdf->Escribir(1, 17, 'Historia: ' . $datosPaciente->getNumeroHistoria());
$pdf->Escribir(20, 17, 'Ingreso: ' . $datosPaciente->getNumeroIngreso());
$pdf->Escribir(1, 20, 'Sexo: ' . $datosPaciente->getGenero());
$pdf->Escribir(15, 20, 'Edad: ' . $datosPaciente->getEdad());
$pdf->Escribir(35, 20, 'Dx: ' . $datosPaciente->getDiagnostico());
$pdf->Escribir(1, 37, 'EPS: ' . $datosPaciente->getAseguradora());

//$file = $pdf->QrCode("CC 16864870");

//$datosPaciente = new DatosSocioeconomicos($conex, $nHis, $nIng);
//$datosEmpresa = new DatosEmpresa($conex, $wEmp);

$pdf->Output();

