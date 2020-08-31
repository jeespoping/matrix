<head>
<title>TIEMPOS DE ALTAS CON HOTELERIA</title>
</head>

<script type="text/javascript">
	function abrirEnlace( enlace ){
		window.open( enlace, "", "channelmode=1, titlebar=0" );
	}

	function cerrarVentana()
	 { 
      top.close();      		  
     }
</script>

<body>

<?php
include_once("conex.php");
/**************************************************************************************
 *
 * Creado por: Edwin Molina Grisales
 * Programa: rep_TiemposAltasHoteleria.php
 * Fecha: 2009-05-21
 * Codigo de requerimientos: 1518
 * Objetivo: Generar informe del programa de altas con la siguiente 
 *           información: el informe debe contener 4 procesos de alta así. Hora 
 *           y nombre de quien activa el proceso de alta, hora y nombre de quien 
 *           coloca pagó o no paga si es el caso, hora y nombre de hotelera que 
 *           toma el caso, hora y nombre de quien da el alta definitiva.
 *
 * Modificaciones
 * 2019-01-29 Arleyda Insignares C. Migración, modificación de consultas mysql_fetch_array,
 *                                  el cual generaba 'Illegal string offset'
 **************************************************************************************/
 
/**************************************************************************************
 *
 * Variables del programa
 *
 * $fechaini			Fecha inicial con que se genera el reporte
 * $fecahfin			Fecha final con la que se genera el reporte
 * $his					No de Historia con que se genera el reporte
 * $noide				No. de identificación del paciente con que se genera el 
 * 						reporte
 * $fechasintermedias	Variable que adiciona la busqueda por fechas en el query
 * 						en caso de no haber ingresado una 'Historia' o 'No. de 
 * 						Identificación'
 * $rowpago				Array que contiene la informacion sobre el pago dado
 * 						por el proceso de alta
 * $rowhot				Contiene el nombre de la hotelera en caso de que halla una
 * $rowuser				Contiene el nombre del usuario que Diò el Alta definitiva
 * 
 *******************************************************************************/

?>

<?php
include_once("root/comun.php");

if( !isset($_SESSION['user']) )
	exit ("error");
	
$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

encabezado("TIEMPOS DE ALTAS CON HOTELERIA", "1.0 Mayo 21 de 2009" , "clinica");

if( !isset($his) || !isset($noide) ){
	
	if( !isset($fechaini) )
		$fechaini = date("Y-m-01");
	if( !isset($fechafin) )
		$fechafin = date("Y-m-t");
		
	echo "<br><br>
	<form name='inicio' action='rep_TiemposAltasHoteleria.php?wemp_pmla=$wemp_pmla' method=post>
		<INPUT type=hidden name='wemp_pmla' value='$wemp_pmla'>
		<table align=center>
			<tr class='encabezadotabla' align=center>
				<td>Historia</td>
				<td>No. Identificaci&oacute;n</td>
			</tr><tr class='fila1' align=center>
				<td><INPUT type='text' name='his' id='idhis'></td>
				<td><INPUT type='text' name='noide' id='idnoide'></td>
			</tr>
		</table>
		<br><br>
		<table align=center>
			<tr class='encabezadotabla' align=center>
				<td>Fecha inicial</td>
				<td>Fecha final</td>
			</tr><tr class='fila1'>
				<td>";
	campoFechaDefecto( "fechaini", $fechaini );
	echo "</td><td>"; 
	campoFechaDefecto( "fechafin", $fechafin );
	echo "</td>
			</td>
		</table>
		<br><br>
		<table align=center>
			<tr>
				<td>
					<INPUT type=submit value='Generar' style='width:100'>
					<INPUT type=button value='Cerrar' style='width:100' onClick='javascript:cerrarVentana()'>
				</td><td>
			</tr>
		</table>
	</form>
	<br><br>		
	";
}
else if( !isset( $nom ) ){
	
	echo "<br><br>
	    <form action='rep_TiemposAltasHoteleria.php?wemp_pmla=$wemp_pmla&fechaini=$fechaini&fechafin=$fechafin' method=post>
		<INPUT type=hidden name='fechaini' value='$fechaini'>
		<INPUT type=hidden name='fechafin' value='$fechafin'>
		<INPUT type=hidden name='wemp_pmla' value='$wemp_pmla'>";
	
	$fechasintermedias ="";
	
	if( empty( $noide ) && empty( $his ) ){		
		$fechasintermedias = "(ubifad BETWEEN '$fechaini' and '$fechafin') AND";
	
		echo "		<table align=center>
				<tr class='encabezadotabla' align=center>
					<td width=100>Fecha inicial</td>
					<td width=100>Fecha final</td>				
				</tr>
				<tr align=center class=fila1>
					<td>$fechaini</td>
					<td>$fechafin</td>
				</tr>
			</table>
			<br><br>";
	}
		
	echo "<table align=center>
			<tr class='encabezadotabla' align=center>
				<td colspan=4>Información del paciente</td>
				<td rowspan=2>Tiempo Total del Alta</td>
				<td colspan=2 scope=col>Alta en Proceso</td>
				<td colspan=3>Hotelera</td>
				<td colspan=2>Pago</td>
				<td colspan=2>Alta Definitiva</td>
				<td rowspan=2 width=200>Quien di&oacute; alta</td>
			</tr>
			<tr class=encabezadotabla align=center>
				<td width=75>His - Ing</td>
				<td>Ultima hab.</td>
				<td>No. Id</td>
				<td>Nombre</td>
				<td width=85 align=center>Fecha</td>
				<td>Hora</td>
				<td width=85 align=center>Fecha</td>
				<td>Hora</td>
				<td>Nombre</td>
				<td width=85 align=center>Fecha</td>
				<td>Hora</td>
				<td width=85 align=center>Fecha</td>
				<td>Hora</td>
			</tr>";
	
	//Consulta Principal
	//Hallando pacientes con tiempos de alta definitiva
	$sql = "SELECT 
				ubifad,	ubihad, ubifap, ubihap, ubihac, ubiing, ubihot,  
				ubihis, oriced, pacno1, pacno2, pacap1, pacap2, ubifho, 
				ubialp, ubisan, {$wbasedato}_000018.id, ubiprg, ubihho, ubiuad
			FROM 
				root_000036, root_000037, 
				movhos_000018, movhos_000011
			WHERE
				$fechasintermedias
				ubihis = orihis
				AND oriori = '$wemp_pmla'
				AND oriced = pacced
				AND oritid = pactid
				AND ubiald = 'on'
				AND ubisac = ccocod
				AND ccohos = 'on'
				AND ccourg != 'on'
				AND ccocir != 'on'
				AND ubihis like '$his%'
				AND oriced like '$noide%'
			ORDER BY ubihis, ubifad, ubiing ASC";
		
				
    $res = mysql_query($sql, $conex);

    for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
		
    	$fila = "fila".(($i%2)+1);	//Elige la clase para pintar fila
    	
    	//Hallando el nombre de quien dió el alta
    	//No todos lo tienen
    	$resusuario="";
    	if( !empty( $rows['ubiuad'] ) ){
	    	$sqluser = "SELECT descripcion FROM usuarios WHERE codigo='{$rows['ubiuad']}'";
	    	$resuser = mysql_query($sqluser,$conex);
	    	$rowuser = mysql_fetch_array($resuser);
            $resusuario = $rowuser['descripcion'];
    	}
    	
	    //Hallando el nombre de la hotelera
    	$reshotel= "";
		if( !empty( $rows['ubihot'] ) ){
			$sqlhot = "SELECT descripcion FROM usuarios WHERE codigo = '{$rows['ubihot']}'";
		
			$reshot = mysql_query( $sqlhot, $conex );
			$rowhot = mysql_fetch_array($reshot);
			$reshotel = $rowhot['descripcion'];
		}    		
    	
    	//Consulta para hallar fecha y hora de pago
    	//No todos lo tienen por eso se hace en una consulta aparte
    	$sqlpago = "SELECT 
						cuefpa,	cuehpa,	cuepag  
				    FROM 
						{$wbasedato}_000018, {$wbasedato}_000022
				    WHERE
				    	ubiing = cueing
				    	AND ubihis = cuehis
				    	AND ubiing = '{$rows['ubiing']}'
				    	AND ubihis = '{$rows['ubihis']}'";
		
		$fecpago = "";
		$horpago = "";
		$respago = mysql_query($sqlpago,$conex);
		if ( !($rowpago = mysql_fetch_array($respago) ) ){
			$rowpago['cuefpa'] = "0000-00-00";
			$rowpago['cuehpa']= "00:00:00";
            $fecpago = $rowpago['cuefpa'];
		    $horpago = $rowpago['cuehpa'];
		}
		
    	//Hallando el tiempo total de alta
    	//Solo si la fecha de hora total es igual a la fecha de hora total
    	$dif = "N/A";
    	if( $rows['ubifap'] == $rows['ubifad'] ){
			//Tiempo total de que se demoró el proceso de alta
			$difseg = strtotime( $rows['ubihad'] ) - strtotime( $rows['ubihap'] );  //se calcula la diferencia en segundos
			$dif = date("H:i:s", mktime( 0, 0, $difseg ));	// se crea la hora (Tiempo de Alta total)
		}
    	
		echo "		<tr class='$fila'>
					<td align=right>{$rows['ubihis']}-{$rows['ubiing']}</td>
					<td align=center>{$rows['ubihac']}</td>
					<td align=right>{$rows['oriced']}</td>
					<td>{$rows['pacno1']} {$rows['pacno2']} {$rows['pacap1']} {$rows['pacap2']}</td>
					<td align=center>$dif</td>
					<td align=center>{$rows['ubifap']}</td>
					<td align=center>{$rows['ubihap']}</td>
					<td align=center>{$rows['ubifho']}</td>
					<td align=center>{$rows['ubihho']}</td>
					<td>{$reshotel}</td>
					<td align=center>{$fecpago}</td>
					<td align=center>{$horpago}</td>
					<td align=center>{$rows['ubifad']}</td>
					<td align=center>{$rows['ubihad']}</td>
					<td>{$resusuario}</td>
				</tr>";
	}			
	
	echo "	</table>
		<br>
		<p align=center><b>Nota: </b>El 'Tiempo de alta' se calcula cuando la 'Fecha' de 'Alta en proceso' es igual a la 'Fecha' de 'Alta definitiva'</p>
		<br>
		<table align=center>
			<tr align=center>
				<td><INPUT type='submit' style='width:100' value=Retornar><td>
				<td><INPUT type='button' style='width:100' value='Cerrar' onClick='javascript:cerrarVentana();'></td>
			</tr>
		</table>
	</form>";
}
?>