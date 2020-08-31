
.
<html>
<head>
  <title>Resumen Cuadre de Caja </title>

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
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    </style>
</head>
<body>
<?php
include_once("conex.php");
/**
 * PROGRAMA QUE MUESTRA EL CONSOLIDADO DE LA CAJAPOSTERIOR A UN CUADRE
 *
 * Muestra el resumen de un cuadre, es decir la sumatoria de las diferentes formas de pago para el saldo anterior (como estaba la caja antes de cuadre,
 * los nuevos ingresesos (los movimientos efectuados desde el cuadre anterior y el cuadre en cuestion), los dineros egresados (el dineo que sale de la
 * caja con motivo del cuadre) y e el saldo "actual" (los dineros que quedan en la caja despues del cuadre).
 *
 * @name	Cuadre Resumen
 * @author	Ana María Betancur Vargas
 * @created	2006-10-01
 * @version 2006-12-13
 *
 * @wvar String 	$user		usuario
 * @wvar String 	$cco		Código del centro de costos
 * @wvar String		$cajCod 	Código de la caja
 * @wvar String 	$cajDes 	Nombre o descripción de la caja
 * @wvar Integer	$cuaAnt 	Numero del cuadre anterior al que se va a realizar.
 * @wvar Integer	$proceso	Determina si es un reporte o se esta realizando un cuadre de caja.<br>
 * 								[0]:reporte.<br>
 * 								[1]:proceso cuadre de caja.
 * @wvar Array 		$cajas		Contiene la información de las cajas.<br>
 * 								['cod']:código de la caja.<br>
 * 								['des']:descripción de la caja.<br>
 * 								['cco']:centro de costos al que pertenece la caja
 * @wvar String[10] $fecha1 	Fecha inicial
 * @wvar String[10] $fecha2 	Fecha Final
 * @wvar Array[3][] $cua		Array del cuadre .<br>
 * 								['cencua']:Cuadre .<br>
 * 								['fecha_Data']:fecha.<br>
 * 								['hora_data']: hora
 * @wvar String[]	$resp		Persona que realizo el cuadre.
 * @wvar Array		$valxFp		Valor por forma de pago, contiene la información de todos los registros.<br>
 * 								Es tridimencional así (por ejemplo $valxFp['nue']['fpa'][$i]: <br>
 * 								La tercera dimención es solo un contador de los valores
 * 								Primera dimensión solo toma dos valores y define que el registro pertenece a:<br>
 * 								['saa']:saldo de la caja antes del cuadre en cuestion.<br>
 * 								['nin']:nuevos ingresos de un cuadre anterior.<br>
 *								['egr']:valor egresado durante el cuadre.<br>
 * 								['ant']:el registro pertenece a un saldo anterior, para mostrarlo en pantalla se eunvia como tipo "nsa" pero la función MostrarPantalla la transforma en "ant".<br>
 * 								La Segundo define el tipo de dato que guarda:<br>
 * 								['fpa']:Código y detalle de la forma de pago.<br>
 * 								['val']:Valor de la forma de pago.
 *
 * @modified 2006-12-05 Se adecua para que funcione como reporte, tal que si un usuario tiene asignada una caja pueda ver los cuadres pasados.
 * @modified 2006-12-13 Se adecua para que cualquier usuario pueda ver el reporte de los cuadres, eligiendo la caja si no tiene una caja asignada.
 * @modified 2006-12-14 se cambia para que todos los uasuaios sin importar si tienen una caja asignada o no deban elegir la caja para ver el reporte.
 *
 */

/**
 * Include que contiene las funciones a utilizar
 */
include_once('ips/cuadre_ips.php');




//session_start();


if(!isset($_SESSION['user']))
echo "error";
else
{


	$wactualiz="2006-12-13";

	echo "<center><table border='1' width='220'>";
	echo "<tr><td colspan='2' class='titulo1'><img src='/matrix/images/medical/POS/logo_".$tipoTablas.".png' WIDTH=215 HEIGHT=76></td></tr>";
	echo "<tr><td colspan='2'class='titulo1'><b>cuadreResumen.php <br>Versión ".$wactualiz."</b></font></td></tr>";
	if(!isset($cajCod)) {
		$cco = '';
		$cajCod = '';
		$cajDes = '';
		$cuaAnt = '';
		$comentario ='Ninguno';
		if( $proceso == 1) {
			Cuadre(substr($user,2), $cco, $cajCod, $cajDes, $cuaAnt);
		}
	}


	if($cajCod == '' and  $proceso == 1) {
		echo "El usuario no tiene una caja signada para realizar esta operación";
	}else {

		if(isset($proceso) and $proceso == 0) {
			$cuaAnt='';
			if(!isset($cuadre)) {
				echo "<form action='' method='POST'> ";


				if($cajCod == "") {
					/*Seleccionar las cajas*/
					SeleccionCajas($cajas);

					echo "</table><BR><BR><center><table border='1'>";
					echo "<tr><td colspan='2'class='titulo3'><b>ELIJA UNA CALJA:</b></font></td></tr>";
					$num=count($cajas);
					if($num>0) {
						for($i=0;$i<$num; $i++) {

							echo "<tr><td><input type='radio' name='cajCod' value='".$cajas[$i]['cod']."-".$cajas[$i]['des']."-".$cajas[$i]['cco']."'>";
							echo "<td class='texto'><b>".$cajas[$i]['cod']."</b>-".$cajas[$i]['des']." <b>(".$cajas[$i]['cco'].")</b></td>";
						}
					}else{
						echo "<tr><td class='error1'>No existen cajas para seleccionar.<br> Vuelva a intentarlo</td>";
					}
				}else {

					if(!isset($cco) or $cco==""){
						list($cajCod, $cajDes, $cco) = explode("-", $cajCod);
					}

					echo "<tr><td colspan='2'class='titulo1'><b>RESUMEN<br>CUADRE DE CAJA  </b></font></td></tr>";
					echo "<tr><td colspan='2'class='titulo1'><b>$cajCod $cajDes</b></font></td></tr>";

					if(!isset($fecha1)) {
						/*Buscar fecha de inicio y fin para los cuadres*/

						echo "</table><BR><BR>";
						echo "<center><table border width='390'>";
						echo "<tr><td class='titulo2'><b>Fecha de Inicio </b></font></td>";
						echo "<td class='titulo2'><input type='text' name='fecha1' value='".date('Y-m-d')."' size='9'></font></td>";
						echo "<tr><td class='titulo2'><b>Fecha de fin </b></font></td>";
						echo "<td class='titulo2'><input type='text' name='fecha2' value='".date('Y-m-d')."' size='9'></font></td>";

					}else {

						/*Mostrar los cuadres entre las dos fechas*/
						SeleccionCuadres($cajCod, $fecha1, $fecha2, $cua);
						echo "<tr><td colspan='2'class='titulo1'><b>ENTRE ".$fecha1." Y ".$fecha2." </b></font></td></tr>";
						echo "</table><BR><BR><center><table border width='390'>";
						$num=count($cua);
						if($num>0)
						for($i=0;$i<$num; $i++) {

							echo "<tr><td><input type='radio' name='cuadre' value='".$cua[$i]['cua']."'>";
							echo "<td class='texto'> Cuadre <b>#".$cua[$i]['cua']."</b> Realizado el ".$cua[$i]['fec']." a las ".$cua[$i]['hor']."</td>";
						}else{
							echo "<tr><td class='error1'>No existen cuadres en las fechas seleccionadas.<br> Vuelva a intentarlo</td>";
						}
					}
					echo "<input type='hidden' name='cco' value='".$cco."'>";
					echo "<input type='hidden' name='cajCod' value='".$cajCod."'>";
					echo "<input type='hidden' name='cajDes' value='".$cajDes."'>";
				}
				echo "<input type='hidden' name='proceso' value='$proceso'>";
				echo "<input type='hidden' name='tipoTablas' value='$tipoTablas'>";
				echo "</table><br><br>";
				echo "<table align='center' border='0'>";
				echo "<tr><td align='center'><input type='submit' name='aceptar' value='ACEPTAR'></td></tr></table>";
				echo "</form>";

			}else {
				$cuaAnt = $cuadre;
			}

		}//else{



		$hora='00:00:00';
		$fecha='0000-00-00';
		$resp='';
		CuadrePasado($cuaAnt, $cajCod, $fecha, $hora, $resp, $comentario);
		//}

		if($cuaAnt != '') {
			echo "<tr><td colspan='2'class='titulo1'><b>RESUMEN<br>CUADRE DE CAJA  ".$cuaAnt."</b></font></td></tr>";
			echo "<tr><td colspan='2'class='titulo1'><b>$cajCod $cajDes</b></font></td></tr>";
			echo "<tr><td class='titulo3' >$resp</td></tr>";
			echo "<tr><td class='titulo3' >$fecha $hora</td></tr>";
			echo "</table><br>";

			anteriores($cajCod, ($cuaAnt-1), $cco, $valxFp,3);
			anteriores($cajCod, $cuaAnt, $cco, $valxFp,4);
			anteriores($cajCod, $cuaAnt, $cco, $valxFp,1);
			anteriores($cajCod, $cuaAnt, $cco, $valxFp,5);
			echo "<center><table border='0'>";
			echo "<tr><td align='center'>";
			MostrarPantallaReporte('saa', $valxFp);/*tipo=3*/
			echo "</tr></td><tr><td></tr></td>";
			echo "<tr><td align='center'>";
			MostrarPantallaReporte('nin', $valxFp);/*tipo=4*/
			echo "</tr></td><tr><td></tr></td>";
			echo "<tr><td align='center'>";
			MostrarPantallaReporte('nsa', $valxFp);/*tipo1*/
			echo "</tr></td><tr><td></tr></td>";
			echo "<tr><td align='center'>";
			MostrarPantallaReporte('egr', $valxFp);/*tipo5*/
			echo "<input type='hidden' name='cco' value='".$cco."'>";
			echo "<input type='hidden' name='cajCod' value='".$cajCod."'>";
			echo "<input type='hidden' name='cajDes' value='".$cajDes."'>";
			echo "<input type='hidden' name='cuaAnt' value='".$cuaAnt."'>";
			//echo "<input type='hidden' name='' value='".."'>";
			echo "</tr></td>";
			echo "<tr>";//<td class='texto'>";
			echo "<center><table border='1'>";
			echo "<tr><td class='titulo2'>";
			echo "COMENTARIO<br></td></tr>";
			echo "<tr><td class='texto'>".$comentario;
			echo "</tr></td>";
			echo "</table>";
			echo "</tr></td>";
			echo "</table>";

		}
	}
	include_once("free.php");
}

?>
</body>
</html>
