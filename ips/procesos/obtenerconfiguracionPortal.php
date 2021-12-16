<?php
//script con los tipos de datos
include_once("conex.php");
include("root/comun.php");
include("clasesturnero.php");
ob_end_clean();


if ($_SERVER['REQUEST_METHOD'] == 'GET')
{

	$codigoPortal = $_GET['codigoPortal'];
	$wemp_pmla = $_GET['wemp_pmla'];	
	$conex = obtenerConexionBD("matrix");	
	$objPortal = new Portal($codigoPortal,$wemp_pmla,$conex);
	$objPortal->CargarConfiguracion();

	header("HTTP/1.1 200 OK");
	echo json_encode($objPortal);
	exit();

}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");


?>