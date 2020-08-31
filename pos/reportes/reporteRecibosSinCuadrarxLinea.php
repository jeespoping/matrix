<html>
<head>
<title>REPORTE RECIBOS SIN CUADRAR POR LINEA</title>
</head>
<body>
<script type="text/javascript">

function irAInicio(){
	document.location.href = "reporteRecibosSinCuadrar.php?wemp_pmla="+document.forms.forma.wemp_pmla.value+"&aplicacion="+document.forms.forma.aplicacion.value;
}

function cerrarVentana() {	
	window.close(); 
}

function alternarDisplay(cdElemento){
	var tabla = document.getElementById(cdElemento);
	
	if(tabla){
		if(tabla.style.display == 'none'){
			tabla.style.display = 'block';
			document.getElementById("l"+cdElemento).innerHTML = "Ocultar";
		} else {
			tabla.style.display = 'none';
			document.getElementById("l"+cdElemento).innerHTML = "Ver";
		}
	}
}
</script>
<div id="cargando" style="display:block">
<center>
	<img src="../../images/medical/ajax-loader3.gif"/>
	<h1>Cargando...</h1>
</center>
</div>
<?php
include_once("conex.php");
include_once("root/comun.php");
/**
 * REPORTE RECIBOS SIN CUADRAR POR LINEA
 */
$wautor = "Mauricio Sánchez Castaño";
$wactualiz = "2008-08-05";

$AzulClar="#006699";  	//Azul claro
$AzulText="#003366"; 	//COLOR DE LA LETRA  Azul Oscuro
$colorFila1 = "#CDD5F0";
$colorFila2 = "#F6F5FF";

if(!isset($_SESSION['user']))
	terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
else
{
	$key = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));

	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	if(!isset($aplicacion)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."aplicacion");
	}

//	echo $aplicacion;
//	echo $wemp_pmla;
	
	$baseDatos = consultarAliasPorAplicacion($conex,$wemp_pmla,$aplicacion);

	//Variables
	$centroCostos = "";
	$nombreCentroCostos = "";
	$codigoCaja = "";
	$tipoCuadre = "";

	echo "<center><table border width='350' cellpadding=0 cellspacing=0>";
	echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$baseDatos.".png' WIDTH=388 HEIGHT=70></td></tr>";	

	//Consulta el ultimo cuadre de caja (fecha, hora y codigo), codigo de la sucursal, codigo de la caja asociada al usuario 
	$q ="SELECT
				Cjecco, Cjecaj, Cjetin, Cajcua, Cajdes, ".$baseDatos."_000037.Fecha_data fechaCuadre, ".$baseDatos."_000037.hora_data horaCuadre
			FROM 
				".$baseDatos."_000030, ".$baseDatos."_000028, ".$baseDatos."_000037
				WHERE Cjeusu = '".$wusuario."'
				AND Cjeest = 'on'
				AND Cajcod = SUBSTRING_INDEX( Cjecaj, '-', 1 ) 
				AND ".$baseDatos."_000037.id = ( 
							SELECT Max( id ) 
							FROM ".$baseDatos."_000037	
							WHERE Cdecco = SUBSTRING_INDEX( Cjecco, '-', 1 )
							AND Cdecaj = SUBSTRING_INDEX( Cjecaj, '-', 1 )); ";
	
//	echo $q;
	
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num > 0){
		$row = mysql_fetch_array($res);

		$centroCostos = strpos($row[0],"-");
		$wcco = substr($row[0],0,$centroCostos);
		$nombreCentroCostos = substr($row[0],$centroCostos+1,strlen($row[0]));

		$vecCodigoCaja=explode("-",$row['Cjecaj']);
		$codigoCaja = $vecCodigoCaja[0];
		$nombreCaja = $vecCodigoCaja[1];
		$tipoCuadre = $row['Cjetin'];
		$ultimoCuadre = $row['Cajcua'];
		$fechaUltimoCuadre = $row['fechaCuadre'];
		$horaUltimoCuadre = $row['horaCuadre'];
		$centroCostos=explode("-",$row['Cjecco']);
		$nombreCentroCostos = $centroCostos[1];

		
		//Fecha y hora para hacer pruebas
//		$fechaUltimoCuadre = '2008-07-09';
//		$horaUltimoCuadre = '19:00:00';
		
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>REPORTE RECIBOS SIN CUADRAR POR LINEA</b></font></td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>reporteRecibosSinCuadrarxLinea.php</b></font></td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>Ultimo cuadre: $fechaUltimoCuadre a las $horaUltimoCuadre</b></font></td></tr>";
		
		echo '<form name="forma" action="reporteRecibosSinCuadrarxLinea.php" method="POST">';
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>$codigoCaja - $nombreCaja - $centroCostos[0]:$centroCostos[1]</b></font></td></tr>";		
		echo "</table>";
		
		//Consulta de los recibos sin cuadrar por linea, dada una caja, dado su cco.
		$q ="SELECT 
				* 
			FROM (
		SELECT 
				Vennum, farstore_000016.Fecha_data,  farstore_000016.Hora_data,  SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, Artnom, Grudes, Venvto, Vdevun, Vdecan, (Vdevun*Vdecan) Vdesub,
				CASE WHEN (
							SELECT Rdevta
							FROM farstore_000037, farstore_000021
							WHERE Rdecco = Cdecco
							AND Rdenum = Cdenum
							AND Rdecco = Vencco
							AND Cdecaj = Vencaj
							AND Rdevta = Vennum
				) IS NULL 
				THEN 'N'
				ELSE 'S'
				END Factura_cuadrada   
			FROM 
				".$baseDatos."_000016, ".$baseDatos."_000017, ".$baseDatos."_000001, ".$baseDatos."_000004 
			WHERE 
				Venest = 'on'
				AND Vdenum = Vennum 
				AND Vdeart = Artcod
				AND SUBSTRING_INDEX( Artgru, '-', 1 ) = Grucod 
				AND CAST(CONCAT(".$baseDatos."_000016.Fecha_data, ' ', ".$baseDatos."_000016.Hora_data) AS DATETIME ) > CAST( CONCAT('".$fechaUltimoCuadre."', ' ', '".$horaUltimoCuadre."') AS DATETIME ) 
				AND Vencco = '".$centroCostos[0]."' 
				AND Vencaj = '".$codigoCaja."'				
			ORDER BY Artgru,Vennum
			) a
		WHERE a.Factura_cuadrada LIKE '%'; ";
		
//		echo $q;
		
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		
		echo "<br>";
		echo "<br>";
		echo "<center><table border width='450' cellpadding=0 cellspacing=0>";
		if ($num > 0){
			$sumaSinCuadre = 0;
			$sumaSinCuadreLinea = 0;
			$linea = "";
			
			//Encabezado
			echo "<tr align=center><td bgcolor=".$AzulText."><font size=3 text color=#FFFFFF><b>Línea</b></font>
			</td><td bgcolor=".$AzulText."><font size=3 text color=#FFFFFF><b>Valor</b></font></td>
			</td><td bgcolor=".$AzulText."><font size=3 text color=#FFFFFF><b>Detalle</b></font></td></tr>";
			
			$row = mysql_fetch_array($res);
			
			$linea = $row['Artgru'];
			$lineaTabla = $row['Artgru'];
			$nombreLinea = $row['Grudes'];
			$colorEncabezado = $colorFila1;
			$colorDetalle = $colorFila2;
				
			//Resumen
			for($cont1 = 0; $cont1 <= $num; $cont1++){
				
				$cont1 % 2 == 0 ? $color = $colorFila1: $color = $colorFila2;
				
//				mensajeEmergente($row['Vdesub']." ".$row['Artgru']);
				
				if($linea != $row['Artgru']){
					$linea = $row['Artgru'];	
					echo "<tr bgcolor=".$colorEncabezado."><td>".$nombreLinea."</td>
											<td align=right>".number_format($sumaSinCuadreLinea,2,'.',',')."</td>
											 <td align=center>";
					echo "<a href=javascript:alternarDisplay("."'t".$lineaTabla."'".");><div id='l"."t".$lineaTabla."'".">Ver</div></a></td>
					  </tr>";
					$sumaSinCuadreLinea = 0;
					$nombreLinea = $row['Grudes'];
					$lineaTabla = $row['Artgru'];
					$colorEncabezado == $colorFila1 ? $colorEncabezado = $colorFila2 : $colorEncabezado = $colorFila1;
				}
				
				//Tabla oculta con detalle
				$sumaSinCuadre+=$row['Vdesub'];
				$sumaSinCuadreLinea+=$row['Vdesub'];
				
				$row = mysql_fetch_array($res);
			}
			echo "<tr bgcolor=".$AzulText."><td><font size=3 text color=#FFFFFF><b>Total</b></font></td><td align=right><font size=3 text color=#FFFFFF><b>".number_format($sumaSinCuadre,2,'.',',')."</b></font></td><td align=right><font size=3 text color=#FFFFFF><b>&nbsp;</b></font></td></tr>";
			
			//En la consulta traje el detalle, en la sección anterior se resumió el reporte por php, vuelvo a iterar el resultSet para mostrar 
			//el detalle, sin ejecutar la consulta nuevamente
			
			echo "<script language='javascript'>";
			echo "document.getElementById('cargando').style.display = 'none';";
			echo "</script>";
			
			mysql_data_seek($res,0);
			$row = mysql_fetch_array($res);
			
			$venta = $row['Vennum'];
			$linea = "";
			$nombreLinea = $row['Grudes'];
			
			echo "<center>";
			//Detalle en tablas ocultas
			for($cont1 = 0; $cont1 < $num; $cont1++){

				$cont1 % 2 == 0 ? $color = $colorFila1: $color = $colorFila2;
				
				if($linea != $row['Artgru']){
					$linea = $row['Artgru'];
					echo "</table>";					
					echo "<table id=t$linea style='display:none' align='center'>";
					echo "<tr bgcolor=".$AzulText." align='center'><td colspan=5><font size=3 text color=#FFFFFF><b>DETALLE RECIBOS DE ".$nombreLinea."</b></font></td></tr>";
					echo "<tr bgcolor=".$AzulText." align='center'>
					<td><font size=3 text color=#FFFFFF><b>Venta</b></font></td>					
					<td><font size=3 text color=#FFFFFF><b>Articulo</b></font></td>
					<td><font size=3 text color=#FFFFFF><b>Cantidad</b></font></td>
					<td><font size=3 text color=#FFFFFF><b>Valor Unit</b></font></td>
					<td><font size=3 text color=#FFFFFF><b>Subtotal</b></font></td>
					</tr>";
				} 
				echo "<tr bgcolor=".$color.">";
				echo "<td align=right>".$row['Vennum']."</td>				  
				      <td align=left>".$row['Artnom']."</td>
				      <td align=left>".$row['Vdecan']."</td>
				      <td align=center>".number_format($row['Vdevun'],2,'.',',')."</td>
				      <td align=center>".number_format($row['Vdesub'],2,'.',',')."</td>
				 ";
				echo "</tr>";

				$row = mysql_fetch_array($res);
				
				$venta = $row['Vennum'];				
				$nombreLinea = $row['Grudes'];
			}
			echo "</center>";
		} else {
			echo "<tr><td><b>No hay recibos pendientes despues de la fecha y hora del ultimo cuadre.<b></td></tr>";
			echo "<script language='javascript'>";
			echo "document.getElementById('cargando').style.display = 'none';";
			echo "</script>";
		}
		echo "</table>";
		echo "<br><center><input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></center>";
	} else {
		mensajeEmergente("EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR.");
		funcionJavascript("cerrarVentana();");
	}
}
liberarConexionBD($conex);
?>
</body>
</html>
