<head>
<title>MATRIX - [REPORTE DE FACTURAS POR TARIFA]</title>
</head>

<script type="text/javascript">
	function cerrarVentana(){
			window.close();
		}
</script>

<body>


<?php
include_once("conex.php");
/***************************************************************
 * 
 * Creado por: Edwin Molina Grisales
 * programa: rep_factarifa.php;
 * Fecha de Creacion:  23-04-2009
 * Cod de requerimiento= 1480
 * Objetivo: Reporte de todo lo facturado por Tarifa de acuerdo 
 *           a un Rango de Fecha
 *  
 *           
 * Variables:
 * 
 * $fechaini	-	Indica la fecha inicial con que se generar� 
 * 					el reporte
 * $fechafin	-	Indica la fecha final con que se generar� 
 * 					el reporte
 *           
 ***************************************************************/
include_once("root/comun.php");

//Funcion para devolver el nombre de los meses en Espa�ol
function mesEsp($mes){
	switch($mes){
		case '1': return "Enero";
		case '2': return "Febrero";
		case '3': return "Marzo";
		case '4': return "Abril";
		case '5': return "Mayo";
		case '6': return "Junio";
		case '7': return "Julio";
		case '8': return "Agosto";
		case '9': return "Septiembre";
		case '10': return "Octubre";
		case '11': return "Noviembre";
		case '12': return "Diciembre";		
	}	
}

$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

encabezado("REPORTE DE FACTURA POR TARIFA","1.0 Abril 23 de 2009",$wbasedato);

//Inicializa la fecha inicial y final del formulario
if( (!isset($fechaini) && !isset($fechafin))
	|| $fechaini > $fechafin ){
	
	if( (!isset($fechaini) && !isset($fechafin))){
		$fechaini = date("Y")."-".date("m")."-01";
		$fechafin = date("Y")."-".date("m")."-".date("t");
	}

	echo 
	"<FORM method=\"post\" action=\"rep_factarifa.php?wemp_pmla=$wemp_pmla\">
	<br><br><TABLE align=center>
	<tr class=\"encabezadotabla\">
		<th align=\"center\" colspan=2>Ingrese el Rango de fechas<br>
	</tr>
	<tr class=\"fila1\">	
		<td>Fecha inicial:
		<td>";
	
	campoFechaDefecto("fechaini",$fechaini);
		
	echo "<tr class=\"fila1\">
		<td>Fecha final:
		<td>";
	
	campoFechaDefecto("fechafin",$fechafin);
		
	echo "<tr><td>&nbsp;</td></tr><tr>
		<td colspan=2 align=center><INPUT type=\"submit\" value=\"Ver\" style='width:100'>
		<input type='button' onClick='javascript:cerrarVentana();' value='Cerrar' style='width:100'>
	</tr></TABLE>	
	</FORM>";
	
	
	//Mensaje de error para elusuario si la fecha inicial es menor que la final
	if($fechaini > $fechafin){
		echo"<p align=center><font color='red'><b><br>La fecha inicial debe<br>ser menor que la fecha final</font></b></br>";
	}
}
else{	
	$query = "SELECT 
				tcartar,
			 	tardes,
			 	sum(tcarvto),
			 	sum(tcarfex+tcarfre) as vtofac,
			 	sum(tcarfex+tcarfre)-(sum(tcarfex)+sum(tcarfre)) as falfac			 	
			 FROM {$wbasedato}_000106, {$wbasedato}_000025, {$wbasedato}_000004  
			 WHERE 
			 	tcartar=tarcod AND 
				tcarfec BETWEEN '$fechaini' AND '$fechafin' AND
				tcarest='on' AND
				gruabo <> 'on' AND
				grucod = tcarconcod
			 GROUP BY tardes 
			 UNION
			 SELECT 
			 	0 as tcartar, 
			 	'0' as tardes, 
			 	sum(tcarvto), 
			 	sum(tcarfex)+sum(tcarfre) as vtofac,
			 	sum(tcarvto)-(sum(tcarfex)+sum(tcarfre)) as falfac 
			 FROM {$wbasedato}_000106, {$wbasedato}_000025, {$wbasedato}_000004
			 WHERE 
			 	tcartar=tarcod AND 
			 	tcarfec BETWEEN '$fechaini' AND '$fechafin' AND
				tcarest='on' AND
				gruabo <> 'on' AND
				grucod = tcarconcod";
	
	$res = mysql_query($query,$conex);
	if(!$res)
		echo "error de consulta";
	$num = mysql_num_rows($res);	
	
	echo "<br><br><table align=center cellspacing=0>
			<tr class='encabezadotabla'>
				<th width=100>Fecha Inicial</th>
				<th width=100>Fecha Final</th>
			</tr><tr class='fila1' align=center>
				<td width=100>$fechaini</td>
				<td width=100>$fechafin</td>
			</tr>			
		 </table><br>";
	
	echo "<form action='rep_factarifa.php?wemp_pmla=$wemp_pmla' method=post>
		<TABLE align=center>
		<tr class=\"encabezadoTabla\" align=center>
			<td>Codigo de Tarifa
			<td>Nombre Tarifa
			<td>Cargado
			<td>Total Facturado
			<td>Faltante por Facturar";
	
	for($i=0; $i < $num-1; $i++){
		//Llena el color de las filas
		$rows = mysql_fetch_array($res);
		if($i%2==0)
			$filacolor="fila1";
		else
			$filacolor="fila2";
			
		echo "<tr class=\"$filacolor\">";
		echo "<td align=center>".$rows[0];
		echo "<td>".$rows[1];
		echo "<td align=right>".number_format($rows[2],0,"",",");
		echo "<td align=right>".number_format($rows[3],0,"",",");
		echo "<td align=right>".number_format($rows[4],0,"",",");		
	}
	
	$rows = mysql_fetch_array($res);
	echo "<tr class=\"encabezadoTabla\">";
	echo "<td align=center colspan=2>Totales";
	echo "<td align=right>".number_format($rows[2],0,"",",");
	echo "<td align=right>".number_format($rows[3],0,"",",");
	echo "<td align=right>".number_format($rows[4],0,"",",");
		
	echo "<tr><td colspan=5 align=right><br><br>Reporte generado a los ".date("d")." d�a(s) del mes de ".mesEsp(date("n"))." del ".date("Y");
	echo "<tr><tr align=center><td align=center colspan=5><br><input type='submit' value='Retornar' style='width:100'>
		  &nbsp;|&nbsp;<INPUT type='button' value='Cerrar' onClick='javascript:window.close();' style='width:100'>";
	echo "</TABLE></form>";
}

?>
</body>
