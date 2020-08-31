<html>
<head>
  <title>Cuadre de Caja </title>

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
  <script language="javascript">
  function val(name, i){
  	//function val(){
  	var vsa =forma.valxFpN[vsa][i].value;
  	alert(vsa);
  }
  </script>
</head>
<body>
<?php
include_once("conex.php");

/**
 * PROGRAMA QUE REALIZA EL PROCESO DE CUADRE DE CAJA
 *
 * Se basa en las tablas de centro de costos, de cuadre y de recibos para mostrsr
 * el estado de la caja detallado es decir la forma en que cada recibo afecta la caja.
 * Funciona como reporte y proceso.
 *
 * <b>Funciones principales</b>
 *
 * Busca las formas de pago de los recibos que estan es la caja $cajcod aun no han sido cuadradas en su totalidad.
 * Rfpcaf=$cajcod.<br>
 * Saldos pendientes: formas de pago de recibos que pertenecieron a un cuadre de la caja $cajCod pero no han sido cuadrados.<br>
 * Rfpecu=	'I':Incompleto, se cuadro parcialmente.<br>
 *  		'P':Pendiente, quedo pendiente por cuadrar.<br>
 * Nuevos Ingresos: formas de pago de recibos que fueron efectuados en la caja $cajcod o fueron trasladados a estos.<br>
 * Rfpecu=	'T':Trasladado, la caja debe ser principal.<br>
 *  		'S':Sin cuadrar, el recibo no pertenece a ningún cuadre.
 *
 * Muestra los recibos en pantalla y el usuario puede elegir cuales recibos va a egresar, es decir cuales van a salir de la caja,
 * hay algunas formas de pago que permiten hace egresos parciales (000023.Fpacmu='on' : en función CondicionesFpa $fpaPar=true), osea que de un recibo con una forma de pago no se tiene que
 * egresar todo el valor, si no una parte que el usurio digita en el campo que el programa provee para ello.  También hay formas de pago
 * que permiten que se cambie el banco (000023.Fpache='on' or 000023.Fpatar='on : en función CondicionesFpa $fpaMBa=true), para los
 * a todos los registros que pertenescan a esta forma de pago se les pone al lado un hypervinculo al programa cambioBanco.php.
 *
 * El programa también proporciona un campo de texto para que el usuario grabe comentarios si así lo desea.
 *
 * Cada vez que se carga el programa se corre la funión tiempo, esta se encarga de verificar que no se haya efectuado un cuadre
 * a la caja $cajcod desde la hora $timeStamp en que se empezo a cuadrar la caja y la hora actual. De no ser así, quiere decir que se
 * estaban haciendo cuadres concurrentes, loq ue puede ocacionar errores, por lo tanto el programa muestra la información
 * del cuadre concurrente (almacenado en $error) y no permite que se siga realizando el cuadre.
 *
 * Cuando se abre el programa inicialmente muestra todos los registros seleccionados para egresar, el usuario debe elegir los que no desea egresar y
 * recalcular los totales oprimiendo el botón "Continuar".  Una vez tenga lo que desea debe seleccionar el checkbox cuadrar caja,
 * y el programa despliega un campo para ingresar el password, este password debe estar registrado en el maestro de cajeros, y
 * que el usuario que tiene ese password tenga asignada la caja $cajcod (en la función Cajero: MID(000030.Cjecco,1,4) = $cco AND
 * MID(000030.Cjecaj,1,2) = $cajCod).  si el password cumple las condiciones se efectua el cuadre: <br>
 * -Se crean registros en las tablas 000073, 000074 de encabezado y detalle de cuadre.<br>
 * -Se hace un update a 000022.Rfpecu todas las formas de pago involuctradas según corresponda P, I o C (ver arriba).<br>
 * -Aumenta el consecutivo de cuadres en el maestro de cajas (000028.Cajcua+=1, si Cajcod=$cajCod, Cajcco=$cco).<br>
 *
 * Cuando el cuadre se realiza existosamente se imprime un hypervinculo pra ver el resumen en el programa cuadreResumen.php
 *
 * <b>Consideraciones</b>
 *
 * -No puede existir mas de una caja con el mismo código.<br>
 * -No pueden hacerce dos cuadres sobre el mismo estado de la caja.<br>
 * -Si existen dos formas de pago iguales para un recibo se mostraran sumadas en el cuadre (como si fuera una sola).<br>
 * -La númeración de los recibos se da por centro de costos
 *
 *
 * @name	Cuadre de caja
 * @author	Ana María Betancur Vargas
 * @created	2006-10-01
 * @version 2006-12-27
 *
 * @wvar String 	$user		usuario
 * @wvar String 	$cco		Código del centro de costos
 * @wvar String		$cajCod 	Código de la caja
 * @wvar String 	$cajDes 	Nombre o descripción de la caja
 * @wvar Float		$timeStamp	time stamp de UNIX que indicala hora exacta en que se empezo el proceso de cuadre.
 * @wvar Integer	$cuaAnt 	Numero del cuadre anterior al que se va a realizar.
 * @wvar String		$pass		Password del usuario que efectua el cuadre.
 * @wvar Integer	$proceso	Determina si es un reporte o se esta realizando un cuadre de caja.<br>
 * 								[0]:reporte.<br>
 * 								[1]:proceso cuadre de caja.
 * @wvar Integer	$grabar		Solo existe si $proceso=1 y el usuario ya selecciono la opcion "CUADRAR CAJA", esta
 * 								opción la imprime en pantalla la función Terminar en el include IPS/cuadre_ips.php
 * @wvar Integer	$tipo 		Solo existe si $proceso=1. Define en que parte del proceso esta la caja.
 * 								0:el usurio aun no ha elegido cuadrar.<br>
 * 								1:el usuario ya eligio cuadrar.<br>
 * 								2:ya se efectuo el cuadre.
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
 * 								Es cuatridimensional así (por ejemplo $valxFp['nue']['fpa'][$i]: <br>
 * 								La tercera dimención es solo un contador de los valores
 * 								Primera dimensión solo toma dos valores y define:<br>
 * 								['ant']:el registro pertenece a un saldo anterior.<br>
 * 								['nue']:el registro pertenece a un nuevo valor que no ha estado en ningún cuadre de esa caja.<br>
 * 								La Segundo define el tipo de dato que guarda:<br>
 * 								['fpa']:Código y detalle de la forma de pago.<br>
 * 								['fec']:Fecha del recibo.<br>
 * 								['fue']:Fuente del recibo.<br>
 * 								['num']:Número del recibo.<br>
 * 								['vfp']:Valor de la forma de pago.<br>
 * 								['ban']:Vector de bancos, ver cuarta dimensión mas abajo.<br>
 * 								['cco']:Código del centro de costos.<br>
 * 								['vsa']:Valor del saldo, lo que deberia ser egresado en el cuadre.<br>
 * 								['veg']:Valor a egresar.<br>
 * 								['vns']:Valor nuevo saldo, lo que queda pendiente para el siguiente ciuadre.<br>
 * 								['res']: Responsable, persona que efectuo el recibo.<br>
 * 								['chk']:booleano, indica si el recibova a ingresar al cuadre.
 * 								La cuarta dimensión son los bancos que puede tener cada uno de los registros,
 * 								pues un recibo puede tener dos formas de pago iguales que van a dos bancos
 * 								diferentes es en esta cuarta dimensión donde quedan almacenados, así:<br>
 * 								['nue']['ban'][$i][$j]: Código y descripción banco número $j pertenceciente al registro numero $i.
 *
 * @wvar Boolean	$cuadreVacio true:El cuadre esta vacio no se puede realizar.<br>
 * 								 false:El cuadre tiene registros, se puede realizar.
 * @wvar Array		$totales 	Array donde se almacenan los totales, es lla funcion MostrarPantallaProceso quien los calcula.<br>
 * 								['vfp']:Valor suceptible de ser egrasado, es decir el total de las formas de pago.<br>
 * 								['vsa']:Calor nuevo saldo.<br>
 * 								['veg']:Valor a egresar.
 *
 * @modified 2006-12-05 Se adecua para que funcione como reporte, tal que si un usuario tiene asignada una caja pueda ver los cuadres pasados.
 * @modified 2006-12-13 Se adecua para que cualquier usuario pueda ver el reporte de los cuadres, eligiendo la caja si no tiene una caja asignada.
 * @modified 2006-12-14 se cambia para que todos los uasuaios sin importar si tienen una caja asignada o no deban elegir la caja para ver el reporte.
 */

$wversion = '2006-12-27';


if(!isset($_SESSION['user']))
echo "error";
else
{

	/**
 * Include que contiene las funciones a utilizar
 */
include_once('ips/cuadre_ips.php');

	if(!isset($tipo)) {
		$tipo=0;
	}elseif (isset($grabar)){
		$tipo=1;
	}
	if(!isset($pass)){
		$pass='';
	}
	if(!isset($comentario) or $comentario==''){
		$comentario='Ninguno';
	}
	if(!isset($cajCod)) {
		$cco = '';
		$cajCod = '';
		$cajDes = '';
		$cuaAnt = '';
		if($proceso == 1 ){
			Cuadre(substr($user,2), $cco, $cajCod, $cajDes, $cuaAnt);
		}
	}

	echo "<center><table border='1' width='360'>";
	echo "<tr><td colspan='2' class='titulo1'><img src='/matrix/images/medical/POS/logo_".$tipoTablas.".png' WIDTH='359' HEIGHT='127'></td></tr>";
	echo "<tr><td colspan='2'class='titulo1'><b>cuadre.php Versión ".$wversion."</b></font></td></tr>";

	$error='';
	if($proceso == 0 or tiempo($timeStamp,$cajCod, $error)  ) {
		if(!isset($tipo)) {
			$tipo=0;
		}
		if($proceso == 0) {
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

					echo "<tr><td colspan='2'class='titulo1'><b>DETALLE<br>CUADRE DE CAJA  ".$cuaAnt."</b></font></td></tr>";
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
						if($num>0) {
							for($i=0;$i<$num; $i++) {

								echo "<tr><td><input type='radio' name='cuadre' value='".$cua[$i]['cua']."'>";
								echo "<td class='texto'> Cuadre <b>#".$cua[$i]['cua']."</b> Realizado el ".$cua[$i]['fec']." a las ".$cua[$i]['hor']."</td>";
							}
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
				$cuaAnt = $cuadre-1;
				$cco = '';
				$hora='00:00:00';
				$fecha='0000-00-00';
				$resp='';
				CuadrePasado(($cuaAnt+1), $cajCod, $fecha, $hora, $resp, $comentario);
			}
		}else {
			$hora=date('H:i:s');
			$fecha=date('Y-m-d');
		}

		if($cuaAnt != '') {
			echo "<tr><td colspan='2'class='titulo1'><b>DETALLE CUADRE DE CAJA  ".($cuaAnt+1)."</b></font></td></tr>";
			echo "<tr><td colspan='2'class='titulo1'><b>$cajDes</b></font></td></tr>";
			echo "<tr><td class='titulo1' >$fecha $hora</td></tr>";
			echo "</table><br><br>";
			if(!isset($continuar)) {
				if($proceso == 1){
					anteriores($cajCod, $cuaAnt, $cco, $valxFp,2);
					nuevos($cco, $cajCod, $valxFp,2);
				}else{
					anteriores($cajCod, $cuaAnt+1, $cco, $valxFp,6);
				}
			}
			$totales['vfp']=0;
			$totales['vsa']=0;
			$totales['veg']=0;
			/*
			if(!isset($valxFpA))
			$valxFpA = '';
			if(!isset($valxFpN))
			$valxFpN = '';
			*/
			echo "<form name='forma' method='POST' action='cuadre.php'>";
			echo "<input type='hidden' name='user' value=".substr($user,2).">";
			echo "<input type='hidden' name='tipoTablas' value=".$tipoTablas.">";
			echo "<center><table border='0' align='center'>";
			echo "<tr><td colspan='2'>";
			//echo "<br><div id='0'></div><br><br>";
			echo "<br><br>";
			echo "</tr></td>";
			echo "<tr><td align='center' colspan='2'>";
			MostrarPantallaProceso($proceso,'ant'  , 'valxFp', $valxFp, $totales);

			echo "<br><br></tr></td>";
			echo "<tr><td align='center' colspan='2'>";
			MostrarPantallaProceso($proceso,'nue', 'valxFp' ,$valxFp, $totales);

			if($proceso == 1){
				echo "<input type='hidden' name='cco' value='".$cco."'>";
				echo "<input type='hidden' name='cajCod' value='".$cajCod."'>";
				echo "<input type='hidden' name='cajDes' value='".$cajDes."'>";
				echo "<input type='hidden' name='cuaAnt' value='".$cuaAnt."'>";
				echo "<input type='hidden' name='tipo' value='".$tipo."'>";
				echo "<input type='hidden' name='timeStamp' value='".$timeStamp."'>";
				echo "<input type='hidden' name='proceso' value='".$proceso."'>";
				//echo "<input type='hidden' name='' value='".."'>";
			}
			echo "<br><br></tr></td>";
			echo "<tr><td align='center' colspan='2'>";
			$cuadreVacio=acumuladoFin($totales);
			echo "</tr></td>";

			if($cuadreVacio and $proceso == 1) {
				echo "<tr><td align='center'  class='error1'>";
				echo "<br><br>NO HAY VALORES A SER CUADRADOS<BR>EL CUADRE NO SE PUEDE REALIZAR";
			}else{
				echo "<tr><td colspan='2' class='texto'>";
				echo "<br><br>";
				Terminar($tipo, $user, $valxFp, $pass, $cco, $cajCod, $comentario, $proceso);
			}
			echo "</tr></td>";
			echo "</table>";
			echo "</form>";
		}elseif ($proceso == 1){
			echo "<tr><td class='error1'>El usuario no tiene una caja asignada para realizar esta operación</td>";
		}
		include_once("free.php");


	}else{
		echo "<tr><td class='error1'>".$error."</td>";
	}
}
?>
</body>
</html>
