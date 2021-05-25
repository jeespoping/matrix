<?php
include_once("conex.php");
include '../presap/models/Admisiones.php';

use matrix\admisiones\presap\models\Admisiones;

$conex = obtenerConexionBD("matrix");
$wtabcco = "cliame";
$modeloAdmisiones = new Admisiones($conex, $wemp_pmla, $wtabcco);
try{
    $reporte = $modeloAdmisiones->todasPorFechaIngreso('"2021-02-23"', '"2021-03-25"');
    $json = json_encode($reporte);
    if($json){
        echo $json;
    }else {
        echo json_last_error_msg();
    }
}catch(Error $err){
    echo json_encode($err);
}
liberarConexionBD($conex);
