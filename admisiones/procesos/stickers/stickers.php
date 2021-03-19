<?php
include_once("conex.php");
include '../../presap/model/DatosSocioeconomicos.php';

use matrix\admisiones\presap\models\DatosSocioeconomicos;

$datas = new DatosSocioeconomicos(900666, 1, $conex);
?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=339">
        <title>Impresi&oacute;n Stickers</title>
        <link href="../../../assets/vendor/bootstrap/css/bootstrap.min.css" crossorigin="anonymous">

    </head>

    <body class="container-fluid">
        <div class="w-100 text-center my-1">
            <div class="row p-1">
                <div class="col text-center"><img src="barcode.gif" class="barcode align-content-center img-fluid" ></div>
            </div>
            <div class="row">
                <div class="col-12">Historia: <span><?php echo $datas->getNumeroHistoria(); ?>-<?php echo $datas->getNumeroIngreso(); ?></span></div>
                <div class="col-4">Doc: <span><?php echo $datas->getTipoDocumento(); ?></span>.<span><?php echo $datas->getNumeroDocumento(); ?></span></div>
                <div class="col-12"><?php echo $datas->getApellidos(); ?>, <?php echo $datas->getNombres(); ?></div>
                <div class="col-4"><span class="text-black-50">Edad:</span> <span><?php echo $datas->getEdad(); ?></span></div>
                <div class="col-4">Sexo: <span><?php echo $datas->getGenero(); ?></span></div>
                <div class="col-4">EPS: <?php echo $datas->getAseguradora(); ?></div>
                <div class="col-12">Dx: <?php echo $datas->getDiagnostico(); ?></div>
            </div>
        </div>
    </body>
</html>
