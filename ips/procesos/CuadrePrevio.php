<html>
<head>
  <title>PreCuadre de Caja </title>

  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	.titulo2{color:#003366;background:#57C8D5;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#A4E1E8;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}    	    	
    </style>
</head>
<body>
<?php
include_once("conex.php");
/**
 * PROGRAMA QUE MUESTRA EL CONSOLIDADO DE LA CAJA PREVIO A UN CUADRE
 * 
 * Muestra el estado consolidado de la caja en un momento dado.  Es decir muestra la sumatoria de las 
 * formas de pago para el saldo anterior y los nuevos ingresos.
 * 
 * @name	Precuadre de caja
 * @author	Ana María Betancur Vargas
 * @created	2006-10-01
 * @version 2006-12-13
 * 
 * @wvar String $user		usuario
 * @wvar String $cco		Código del centro de costos
 * @wvar String $cajCod 	Código de la caja
 * @wvar String $cajDes 	Nombre o descripción de la caja
 * @wvar Int 	$cuaAnt Numero del cuadre anterior al que se va a realizar.
 * @wvar Array 	$valxFpA	Información de las formas de pago, su composición depende del tipo de llamado.<br>
 * 							['ant']['fpa']:Código  y descripción de la forma de pago.<br>
 * 							['ant']['val']:Valor de la forma de pago.
 * @wvar Array 	$valxFpN	Información de las formas de pago, su composición depende del tipo de llamado.<br>
 * 							['nue']['fpa']:Código  y descripción de la forma de pago.<br>
 * 							['nue']['val']:Valor de la forma de pago.
 * 
 * 
 * @modified 2007-01-30 Se quita un echo "<input type='hidden' name='proceso' value='1'>"; que estaba repetido
 * @modified 2006-12-13 Se adiciona el echo "<input type='hidden' name='timeStamp', para las modificaciones en el 
 * programa Procesos/cuadre_ips.php correspondiente a la modificación de la misma fecha.
 * 
 * 
 */
/**
 * Include que contiene las funciones a utilizar
 */
include_once('IPS/cuadre_ips.php');
if(!isset($_SESSION['user']))
echo "error";
else
{

	

	$cco = '';
	$cajCod = '';
	$cajDes = '';
	$cuaAnt = '';
	Cuadre(substr($user,2), &$cco, &$cajCod, &$cajDes, &$cuaAnt);
	
	echo "<center><table border='1' width='350'>";
	echo "<tr><td colspan='2' class='titulo1'><img src='/matrix/images/medical/POS/logo_".$tipoTablas.".png' WIDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td colspan='2'class='titulo1'><b>REPORTE PRELIMINAR CUADRE DE CAJA  </b></font></td></tr>";
	echo "<tr><td colspan='2'class='titulo1'><b>$cajDes</b></font></td></tr>";
	//echo "<tr><td colspan='2'class='titulo1'><b>preliminarCuadreFac.php ".$wactualiz."</b></font></td></tr>";
	echo "</table><br><br>";
	

	if($cajCod != ''){
		
		anteriores($cajCod, $cuaAnt, $cco, &$valxFpA,1);
		nuevos($cco, $cajCod, &$valxFpN,1);
		echo "<form name='forma' method='POST' action='cuadre.php'>";
		echo "<input type='hidden' name='user' value=".substr($user,2).">";
		echo "<input type='hidden' name='tipoTablas' value=".$tipoTablas.">";

		echo "<center><table border='0'>";
		echo "<tr><td>";
		MostrarPantallaReporte('ant', $valxFpA);
		echo "<br><br></tr></td>";
		echo "<tr><td>";
		MostrarPantallaReporte('nue', $valxFpN);
		echo "<input type='hidden' name='cco' value='".$cco."'>";
		echo "<input type='hidden' name='cajCod' value='".$cajCod."'>";
		echo "<input type='hidden' name='cajDes' value='".$cajDes."'>";
		echo "<input type='hidden' name='cuaAnt' value='".$cuaAnt."'>";
		echo "</tr></td>";
		echo "<tr><td align='center'>";
		echo "<input type='hidden' name='timeStamp' value='".mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'))."'>";
		echo "<input type='hidden' name='proceso' value='1'>";
		echo "<br><br><input type='submit' value='Continuar >>'>";
		echo "</tr></td>";
		echo "</table>";
		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
		echo "</form>";

	}else{
		echo "El usuario no tiene una caja signada para realizar esta operación";
	}

	include_once("free.php");
}
?>
</body>
</html>