<?php
include_once("conex.php");
/********************************************************
 *           Autor: Ana Maria Betancur					*
 *			Fecha de Creación:2005-05					*
 *   Prueba la existencia de una buena conexión con el	*
 *				servidor del UNIX						*
 ********************************************************	*/
 $addr="132.1.18.2";

$fp = fsockopen( $addr,23, $errno, $errstr);

if(!$fp) {
	echo "ERROR 23:=> "."$errstr ($errno)<br>\n";
	$conex_o=0;
}
else
{
	echo "Puerto Ok 23";
	fclose($fp);
	$conex_o = odbc_connect('inventarios','','');
	
	odbc_close($conex_o);
	odbc_close_all();

}
 ?>