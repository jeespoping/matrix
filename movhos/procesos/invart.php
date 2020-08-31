<html>
<head>
<title>Carga de Articulos a Matrix</title>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#006699;background:#FFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup2{color:#4DBECB;background:#FFFFF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;text-align:center;}
    	<!-- -->
    	<!--.titulo2{color:#003366;background:#57C8D5;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}-->
    	.titulo2{color:#003366;background:#4DBECB;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#0A3D6F;background:#61D2DF;font-size:11pt;font-family:Arial;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:10pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#006699;background:#CCFFFF;font-size:11pt;font-family:Tahoma;font-weight:bold;}
    	.texto3{color:#FFFFF;background:red;font-size:10pt;font-family:Tahoma;text-align:center;}
    	<!-- .acumulado1{color:#003366;background:#FFCC66;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	-->
    	.errorTitulo{color:#FF0000;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}

    	.alert{background:#FFFFAA;color:#FF9900;font-size:10pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:10pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:10pt;font-family:Arial;text-align:center;}

    	.tituloA1{color:#FFFFFF;background:#660099;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#660066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;text-align:center;}

    </style>
</head>
<body>
<?php
include_once("conex.php");
//*========================================================DOCUMENTACION PROGRAMA================================================================================*/
/*
1. AREA DE VERSIONAMIENTO

Nombre del programa: invart.php
Fecha de creacion:  2007-11-16
Autor: Carolina Castano P
Ultima actualizacion:
2007-11-19  Carolina Castaño  Primera publicacion del programa


2. AREA DE DESCRIPCION:

Este script permite cargar las tablas de articulos y de codigo de barras de proovedor a Matrix


3. AREA DE VARIABLES DE TRABAJO


4. AREA DE TABLAS
000026 SELECT, UPDATE
000009 SELECT, UPDATE
*/

session_start();
if (!isset($_SESSION['user']))
echo "error";
else
{

	include_once("movhos/otros.php");
	connectOdbc($conex_o, 'facturacion');

	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario


	echo "</br></br><table align='center' border='0'>";
	echo "<tr><td align=center class='tituloSup' colspan='2'><b>ESTADO DE GRABACION DE DOCUMENTOS</b></td></tr>";
	echo "<tr><td align=center class='tituloSup1' colspan='2'>invart.php Versión 2007-11-16<br><br></td></tr>";
	echo "<tr>";

	if (!isset($ejecutar))
	{
		/**
        * Pinta boton de ejecucion
        */
		echo "<form action='' method='post'>";
		echo "<input type='hidden' name='ejecutar' value='1'></td>";
		echo "<table border=0 width=400 align='center'>";
		echo"<tr><td class='texto2' colspan='2' align='center'><input type='submit' value='CARGAR ARTICULOS'></td></tr></form>";
		echo "</form>";
	}
	else  if ($conex_o != 0)
	{
		$tiempo=mktime(0,0,0,date('m'),date('d'),date('Y'))-(1*24*60*60);
		$fec1=date('Y-m-d', $tiempo);

		//Ponemos la fecha data del primer registro de las tablas de articulos del dia anterior
		$q = "Update ".$bd."_000009 "
		."      SET  Fecha_data='".$fec1."' "
		."      WHERE id=1 "
		."       AND Fecha_data='".date('Y-m-d')."' ";

		$err=mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PRODIDO REALIZAR LA ACTUALIZACION, ERROR AL LIMPIAR LA TABLA INICIAL ".mysql_error());
		
		//AHORA INVOCAMOS LAS FUNCIONES DE OTROS.PHP PARA RECARGAR LAS TABLAS
		ivartCba($wusuario);
		
		//Ponemos la fecha data del primer registro de las tablas de articulos del dia anterior
		date('Y-m-d');
		$q = "Update ".$bd."_000026 "
		."      SET  Fecha_data='".$fec1."' "
		."      WHERE id=1 "
		."       AND Fecha_data='".date('Y-m-d')."' ";
		$err=mysql_query($q, $conex) or die (mysql_errno()." -NO SE HA PRODIDO REALIZAR LA ACTUALIZACION, ERROR AL LIMPIAR LA TABLA INICIAL ".mysql_error());
		ivart($wusuario);
		echo "<tr><td align=center class='tituloSup1' colspan='2'>SE HA REALIZADO LA ACTUALIZACION<br><br></td></tr>";

	}
	else
	{
		echo "<td class='errorTitulo'>EN ESTO MOMENTO NO HAY CONEXION CON UNIX</td></tr>";
	}
	echo "</table>";
}

?>
</body>
</html>
