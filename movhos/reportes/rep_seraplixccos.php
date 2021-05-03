<head>
<title>INSUMOS APLICADOS POR CENTRO DE COSTOS</title>
</head>

<script type="text/javascript">
	function cerrarVentana()
	 {
      top.close();		  
     }
</script>

<body>

<?php
include_once("conex.php");
/***************************************************************
 *
 * Creado por: Edwin Molina Grisales
 * Programa: rep_seraplixccos.php
 * Fecha: 11-05-2009
 * Codigo de requerimientos: 1505
 * Objetivo: Generar un query de los registros de aplicacion del
 * 			 sistema de alta por centro de costos, articulos y
 * 			 cantidad aplicada. Gracias.
 *
 **************************************************************/
//Modificacion:
//Julio 4 de 2012 Viviana Rodas
// Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos de un grupo seleccionado y dibujarSelect //que dibuja el select con los centros de costos obtenidos de la primera funcion.
 
 
/***************************************************************
 *
 * Variables del programa
 *
 * $ccos:					Centro de costos
 * $fechaini				Fecha Inicial
 * $fechafin				Fecha Final
 *
 **************************************************************/
?>

<?php
include_once("root/comun.php");

if(!isset($_SESSION['user']))
	exit("error");
//else{
	
$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

encabezado("INSUMOS APLICADOS POR CENTRO DE COSTOS", "2012-07-04" ,"clinica");

//AQUI COMIENZA EL PROGRAMA
if( !isset($ccos) || !isset($fechaini) || !isset($fechafin) )
{
	$q = " SELECT detapl, detval "
		. "   FROM root_000050, root_000051 "
		. "  WHERE empcod = '" . $wemp_pmla . "'"
		. "    AND empest = 'on' "
		. "    AND empcod = detemp ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);

			if ($row[0] == "cenmez")
				$wcenmez = $row[1];

			if ($row[0] == "afinidad")
				$wafinidad = $row[1];

			if ($row[0] == "movhos")
				$wbasedato = $row[1];

			if ($row[0] == "tabcco")
				$wtabcco = $row[1];
		}
	}
	else
	{
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	}
	
	if( !isset($fechaini) )
		$fechaini = date("Y-m-01");
	
	if( !isset($fechafin) )
		$fechafin = date("Y-m-t");

	
	echo "<br><br><form action='rep_seraplixccos.php?wemp_pmla=".$wemp_pmla."' method='post'>";
		
	//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		$cco="Ccohos";
		$sub="off";
		$tod="Todos";
		$ipod="off";
		//$cco=" ";
		$centrosCostos = consultaCentrosCostos($cco);
					
		echo "<table align='center' border=0 width=410>";		
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "ccos");
					
		echo $dib;
		echo "</table>";

	//tabla para la eleccion de fechas
	echo "
	<br>
	<table align=center>
		<tr class='fila1' align=center>
			<td>Fecha inicial
			<td>Fecha final
		<tr class='fila1'>
			<td>";
	campoFechaDefecto( "fechaini", $fechaini );
	echo "		<td>";
	campoFechaDefecto( "fechafin", $fechafin );
	echo "
	</table><br>

	<table align=center>
		<tr>
			<td><INPUT type='submit' value='Generar' style='width:120'></td>
			<td><INPUT type='button' value='Cerrar Ventana' onClick='cerrarVentana();' style='width:120'></td>
	</table>
	</form>";
}
else{
	//encabezado del informe, fecha inicial y fecha final con que fue generado el reporte
	
	$ccos1=explode("-", $ccos);
	$ccos=$ccos1[0];
	echo 
	"<br><br><table align=center>
		<tr class='encabezadotabla'>
			<th width=100>Fecha inicial</th>
			<th width=100>Fecha final</th>
		</tr><tr class='fila1' align=center>
			<td>$fechaini</td>
			<td>$fechafin</td>
		</tr>
	</table>";
	
	echo "<br><br><form name='main' action='rep_seraplixccos.php?wemp_pmla=$wemp_pmla' method=post>";

	$query = "SELECT 
				aplart AS codigo, 
				apldes AS descripcion, 
				SUM(aplcan) AS cantidad, 
				aplcco AS centrocostos
			 FROM {$wbasedato}_000015
			 WHERE 
			 	aplfec BETWEEN '$fechaini' AND '$fechafin'
			 	AND aplcco like '$ccos'
			 	AND aplest = 'on'
			 GROUP BY 1, 4
			 ORDER BY aplcco, apldes";

	$res = mysql_query($query, $conex);
	$numrows = mysql_num_rows($res);
	
	//Tabla con la informaciòn del reporte
	
	$rows = "";

	//Recorrro las fials halladas
	$rows = mysql_fetch_array($res);
	
	for(  ; $rows;  ){
		
		$auxcostos = $rows[3];
		
		$sql = "SELECT ccocod, cconom 
				FROM {$wbasedato}_000011
				WHERE ccocod = $auxcostos";
		
		$r = mysql_query($sql,$conex);
		$rs = mysql_fetch_array($r);			
		
		echo "<br><table align=center>
			<tr>
				<td colspan=4 class=''><b>Centro de Costos: {$rs[0]} - {$rs[1]}</b> 
			<tr class='encabezadotabla'>
				<th width=70>Código</th>
				<th>Descripción</th>
				<th>Unidad de manejo</th>
				<th width=100>Cantidad</th>";
		
		for($i = 0; $rows[3] == $auxcostos; $i++){
			$fila = "fila".(($i%2)+1);
			
			//Hallo la unidad de medida
			$query = "SELECT artuni 
					 FROM {$wbasedato}_000026
					 WHERE artcod = '{$rows[0]}'";
			
			$result = mysql_query($query);
			if( mysql_num_rows($result) > 0 )
				$rowuni = mysql_fetch_array($result);
			else{
				$query = "SELECT artuni 
					 	 FROM {$wcenmez}_000002
					 	 WHERE artcod = '{$rows[0]}'";
				
				$result = mysql_query($query);
				$rowuni = mysql_fetch_array($result);
			} 
			
			echo "	</tr><tr class='$fila'>
					<td align=center>{$rows[0]}</td>
					<td>{$rows[1]}</td>
					<td align=center>{$rowuni[0]}</td>
					<td align=right>".number_format($rows[2],2,".","")."</td>
					";
			
			$rows = mysql_fetch_array($res);			
		}
		echo "</table>";		
	}
	
	echo "<INPUT type=HIDDEN name=fechaini value='$fechaini'>";
	echo "<INPUT type=HIDDEN name=fechafin value='$fechafin'>";
	
	echo "<br><table align=center>
			<tr align=center>
			<td colspan=5>
				<INPUT type='submit' value='Retornar' style='width:100'> | 
				<INPUT type='button' value='Cerrar' onClick='cerrarVentana()' style='width:100'></td>
		</tr>
	</table>
	</form>";
}

?>
</body>
