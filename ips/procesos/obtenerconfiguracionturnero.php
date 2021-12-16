<?php
include_once("conex.php");
include("root/comun.php");
include("clasesturnero.php");
ob_end_clean();


if ($_SERVER['REQUEST_METHOD'] == 'GET')
{

	$codigoTurnero = $_GET['codigoTurnero'];
	$wemp_pmla = $_GET['wemp_pmla'];
	$conex = obtenerConexionBD("matrix");	
	$objTurnero = new Turnero($codigoTurnero,$wemp_pmla,$conex);
	$objTurnero->CargarConfiguracion();

	header("HTTP/1.1 200 OK");
	echo json_encode($objTurnero);
	exit();

}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");


?>