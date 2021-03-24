<?php 
include '../presap/service/GeneradorCSV.php';

use matrix\admisiones\presap\service\GeneradorCSV;

$prueba = new GeneradorCSV();
$prueba->crearArchivo();
?> 