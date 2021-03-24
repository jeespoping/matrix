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

echo "--";
if ($datosPaciente->validarDatos()) {
    $datosPaciente->getDatosPaciente();
    $datosPaciente->getDatosDiagnostico();
}

$pdf = new StickerPDFController('L', 'mm', array(50, 38));
//
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'¡Hola, Mundo!');
$pdf->Output();
 //$datosPaciente = new DatosSocioeconomicos($conex, $nHis, $nIng);
//$datosEmpresa = new DatosEmpresa($conex, $wEmp);
?>
<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=339">
        <title>Impresi&oacute;n Stickers</title>
        <link rel="stylesheet" href="../../../assets/vendor/bootstrap/css/bootstrap.min.css">

    </head>

    <body class="container-fluid">
        <?php if ($datosPaciente->getError()): ?>
            <div class="alert alert-danger" role="alert"><?php echo $datosPaciente->getError(); ?></div>
        <?php else: ?>
            <div class="w-100 text-center my-1">
                <div class="row p-1">
                    <div class="col text-center"><img src="barcode.gif" class="barcode align-content-center img-fluid" ></div>
                </div>
                <div class="row">
                    <div class="col-12">Historia: <span><?php echo $datosPaciente->getNumeroHistoria(); ?>-<?php echo $datosPaciente->getNumeroIngreso(); ?></span></div>
                    <div class="col-4">Doc: <span><?php echo $datosPaciente->getTipoDocumento(); ?></span>.<span><?php echo $datosPaciente->getNumeroDocumento(); ?></span></div>
                    <div class="col-12"><?php echo $datosPaciente->getApellidos(); ?>, <?php echo $datosPaciente->getNombres(); ?></div>
                    <div class="col-4"><span class="text-black-50">Edad:</span> <span><?php echo $datosPaciente->getEdad(); ?></span></div>
                    <div class="col-4">Sexo: <span><?php echo $datosPaciente->getGenero(); ?></span></div>
                    <div class="col-4">EPS: <?php echo $datosPaciente->getAseguradora(); ?></div>
                    <div class="col-12">Dx: <?php echo $datosPaciente->getDiagnostico(); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </body>
    <script src="../../../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
</html>
