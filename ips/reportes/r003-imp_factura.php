<?php
include_once("conex.php");

/****************************************************************
 *     GENERACIÓN DE FACTURA EN FORMATO DE IMPRESIÓN		    *
 ****************************************************************/

/*--------------------------------------------------------------------------
| DEDSCRIPCIÓN: Genera un formato de impresión para la factura con base en	|
| los datos recibidos ($wfactura,$wbasedato)						|
| FECHA DE CREACIÓN: Noviembre 14 de 2007									|
| FECHA DE ACTUALIZACIÓN: Diciembre 24 de 2013									|
----------------------------------------------------------------------------*/

/*----------------------------------------------------------------------------------------------
| ACTUALIZACIONES																				|
|-----------------------------------------------------------------------------------------------|
| FECHA: Marzo 15 de 2011																		|
| AUTOR: John Mario Cadavid García																|
| DESCRIPCIÓN: Se pasaron las funciones para el script "include/IPS/imprimir_factura_inc.php" 	|
| asi en este script solo se llama la función "imprimirFactura". Esto se hace para permitir		|
| el llamado de esta función desde reportes que muestran varias facturas a la vez.				|
-----------------------------------------------------------------------------------------------*/

echo "<html>";
echo "<head>";

echo "<title>FACTURACION</title>";
echo "</head>";
echo "<body TEXT='#000066'>";

$empresa=$wbasedato;

session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	echo "<form name='r003-imp_factura' action='r003-imp_factura.php' method=post>";
	include_once("root/comun.php");
	include_once("../../../include/ips/imprimir_factura_inc.php");

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";

	if(!isset($user))
		echo "Error Usuario NO Registrado";
	else
	{
		imprimirFactura($wfactura,$wbasedato);
	}
	echo "</body>";
	echo "</html>";
}
?>
