<?php
include_once("conex.php");

//include dirname(__FILE__) . '/../../presap/controller/DatosEmpresaController.php';
include dirname(__FILE__) . '/../../presap/controller/DatosSocioEconomicosController.php';
include dirname(__FILE__) . '/../../presap/controller/StickerPDFController.php';


use Admisiones\Controller\{DatosEmpresaController,
    DatosSocioEconomicosController as DatosPaciente,
    StickerPDFController};

if (isset($_POST['wEmp'])) {
    $wEmp = $_POST['wEmp'];
}

if (!empty($_POST['nHis'])) {
    $nHis = $_POST['nHis'];
}

if (!empty($POST['nIng'])) {
    $nIng = $_POST['nIng'];
}

//$datosEmpresa = new DatosEmpresaController($conex, $wEmp);
$datosPaciente = new DatosPaciente($conex, $nHis, $nIng);

if ($datosPaciente->validarDatos()) {
    $datosPaciente->getDatosPaciente();
}

$pdf = new StickerPDFController('L', 'mm', array(50, 38));

$pdf->AddPage();
$pdf->SetMargins(1, 1, 1);
$pdf->SetFont('Arial', 'B', 8);

$pdf->Celda(1, 2, $datosPaciente->getNombreCompleto(), 'C');

//$imageURL = $datosEmpresa->getImagenURL();
//$pdf->Image(dirname(__FILE__) . '/../../../images/medical/root/cliame.jpg', 1, 4, 14, 10, "jpg");

$baseline = strlen($datosPaciente->getDocumento());
//$pdf->Code39(1, 4, $datosPaciente->getDocumento(), 0.6, 10);
$pdf->Code39(1, 4, '807502426011994', 0.5, 10);
//$pdf->Code39(15, 4, $datosPaciente->getDocumento(), 0.25, 10);

//$pdf->Code39(15, 4, 'PEP 8075024260', 0.405, 10);
//$pdf->Code39(15, 4, 'PEP 807502426011994', 0.31, 10);
$pdf->Escribir(1, 22, 'Historia: ', $datosPaciente->getNumeroHistoria());
$pdf->Escribir(30, 22, 'Ingreso: ', $datosPaciente->getNumeroIngreso());
$pdf->Escribir(1, 28, 'Sexo: ', $datosPaciente->getGenero());
$pdf->Escribir(15, 28, 'Edad: ', $datosPaciente->getEdad());
$pdf->Escribir(37, 28   , 'Dx: ', $datosPaciente->getDiagnostico());
$pdf->Escribir(1, 35    , 'EPS: ', $datosPaciente->getAseguradora());

//$pdf->QrCode("CC 16864870", 1, 4, 14, 10, "png");
//$file = $pdf->QrCode("CC 16864870");
//$pdf->Image($file, 1, 4, 14, 10, "png");

//$datosPaciente = new DatosSocioeconomicos($conex, $nHis, $nIng);
//$datosEmpresa = new DatosEmpresaController($conex, $wEmp);

$pdf->Output();

