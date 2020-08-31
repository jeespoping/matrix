<head>
<title>MATRIX - [REPORTE DE FACTURADO Y PRESUPUESTADO POR EMPRESA]</title>
</head>

<?php
include_once("conex.php");

/***************************************************************
 * 
 * Creado por: Edwin Molina Grisales
 * Programa: rep_facterceros.php
 * Fecha: 27-04-2009
 * Cod de requerimientos: 1479
 * Objetivo: Reporte de facturacion con las siguientes 
 * 			 columnas para SOE:
 * 			 PPtado, Cargado, Facturado  por tercero y empresa.
 * 
 **************************************************************/

/***************************************************************
 * 
 * Variables del programa
 * 
 * $emp: 		tcarres - Indica que se cambia de empresa
 * $conc: 		Indica cambio de concepto
 * $cont:		Contador de Terceros por Concepto
 * $ptoconc		Indica el presupuesto por concepto
 * $carconc - 	Indica la suma total por concepto
 * $facconc - 	Indica la suma total por facturado
 * $fpf - 		Inidca el faltante por facturar por concepto
 * $ptoemp		Inidca el presupuesto por empresa
 * $caremp - 	Indica la suma de lo cargado por empresa
 * $facemp - 	Inidica la suma total facturado por empresa
 * $fpfemp - 	Inidica el total que falta por facturar por empresa
 * 
 **************************************************************/
?>

<body>

<?php 
include_once("root/comun.php");

//Funcion para devolver el nombre de los meses en Español
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
if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");

encabezado("REPORTE DE FACTURADO Y PRESUPUESTADO POR EMPRESA", "1.0 Abril 28 de 2009" ,$wbasedato);

//Consulta para hallar las empresas
$sqlemp = "SELECT 
				tcarres
		  FROM {$wbasedato}_000106
		  WHERE SUBSTRING_INDEX( tcarres, '-', 1 )<>''
		  GROUP BY tcarres
		  ORDER BY SUBSTRING(tcarres FROM INSTR(tcarres,'-')+1)"; 

//Consulta para hallar los conceptos
$sqlcon = "SELECT tcarconnom
		  FROM {$wbasedato}_000106
		  WHERE tcarternom<>''
		  GROUP BY tcarconnom
		  ORDER BY tcarconnom";

//Consulta para hallar los terceros
$sqlter = "SELECT tcarternom, tcartercod 
		  FROM {$wbasedato}_000106
		  WHERE tcarternom<>''
		  GROUP BY tcarternom
		  ORDER BY tcarternom";

if( !isset($mostrar) )
{
	$mostrar = 'on';
}

//Inicializa la fecha inicial y final
if( $mostrar == 'on' || (!isset($fechaini) && !isset($fechafin))
	|| $fechaini > $fechafin ){
		
	if( !isset($fechaini) ){
		$fechaini = date("Y")."-".date("m")."-01";
	}
	
	if( !isset($fechafin) ){
		$fechafin = date("Y")."-".date("m")."-".date("t");
	}
	
	if( !isset( $optemp ) ){
		$optemp = '';
	}
	else{
//		echo "$optemp";
//		$exp = explode( "-", $optemp );
//		$optemp = $exp[0];
	}

	echo 
	"<br><br><FORM method=\"post\" action=\"rep_facPtoEmp.php?wemp_pmla=$wemp_pmla\">
	<TABLE align=center>
	<tr class='encabezadotabla'>
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

	echo "</tr></TABLE>

	<br><table align=center>
	<tr class='fila1' align=center>
		<td><b>Consultar por Empresa</b><br>
			<SELECT name='optemp' style='width:400'>
				<option>Todos</option>";
	
	//Se rellena la lista de empresas
	$res = mysql_query($sqlemp,$conex);
	
	while($rows = mysql_fetch_array($res) )	{
		if( $rows[0] == $optemp ){
			echo "<option value='$rows[0]' selected>$rows[0]</option>";
		}
		else{
			echo "<option value='$rows[0]'>$rows[0]</option>";
		}
	}
	//substr($rows[2],strpos($rows[0],"-")+1)	
			
	echo "</SELECT>
		<tr><td><tr class='fila1' align=center><td><b>Consultar por Concepto</b><br>
			<SELECT name='optcon' style='width:400'><option>Todos</option>";
	
	//Se rellena la lista de Conceptos
	$res = mysql_query($sqlcon,$conex);
	
	while($rows = mysql_fetch_array($res) )	{
		echo ".......".$optcon;
		if( $optcon == $rows[0] ){
			echo "<option selected>$rows[0]</option>";
		}
		else{
			echo "<option>$rows[0]</option>";
		}
	}
	
	echo "		</SELECT>
		<tr><td><tr class='fila1' align=center><td><b>Consultar por Tercero</b><br>
		<SELECT name='optter' style='width:400'><OPTION>Todos</OPTION>";
	
	
	//Se rellena la lista de Terceros
	$res = mysql_query($sqlter,$conex);
	
	while($rows = mysql_fetch_array($res) )	{
		if( $rows[0] == $optter ){
			echo "<option value='$rows[0]' selected>$rows[1]-$rows[0]</option>";
		}
		else{
			echo "<option value='$rows[0]'>$rows[1]-$rows[0]</option>";
		}
	}
	
	echo "</SELECT>";
	
	echo "<INPUT type='hidden' name='mostrar' value='off'>";
	
	echo "<tr>
		<td colspan=3 align=center><br><INPUT type=\"submit\" value=\"Ver reporte\">
		<input type='button' onClick='javascript:window.close();' value='Cerrar Ventana'>
	</table>		
	</FORM>";
	
	if($fechaini > $fechafin){
		echo"<p align=center><font color='red'><b><br>La fecha inicial debe<br>ser menor que la fecha final</font></b></br>";
	}

}//$emp="";	//indica si hubo cambio de empresa
else
{
	$codigoconcepto = false;
	
	if($optemp == "Todos"){
		$optemp = "%";		
	}
	if($optter == "Todos"){
		$optter = "%";
	}
	if($optcon == "Todos"){
		$optcon = "%";
	}
	else{
		$codigoconcepto = true;
	}

	$query ="SELECT 
				tcarres as emp, 
				Tcarconcod as conc, 
				Tcarconnom as concnom, 
				Tcartercod, 
				Tcarternom as tercero,
				sum(ptoval) as pto, 
				sum( Tcarvto ) AS cargado, 
				sum( Tcarfex ) + sum( Tcarfre ) AS facturado, 
				sum( Tcarvto ) - ( sum( Tcarfex ) + sum( Tcarfre ) ) AS fpf
			FROM ".$wbasedato."_000106, (
				SELECT sum(ptocan*ptoval) as ptoval, ptocpt, ptohis, ptoing, ptopro
				FROM {$wbasedato}_000131
				GROUP BY ptocpt, ptohis
				) AS s131 
			WHERE
				tcarhis=ptohis AND
				tcaring=ptoing AND
				tcarconcod=ptocpt AND
				tcarprocod=ptopro AND
				tcarres like '$optemp' AND
				tcarconnom like '$optcon' AND
				tcarternom like '$optter' AND
				tcarfec BETWEEN '$fechaini' AND '$fechafin' AND
				SUBSTRING_INDEX( tcarres, '-', 1 ) <> ''
			GROUP BY tcarres, Tcarconcod, Tcartercod
			ORDER BY tcarres, Tcarconcod, Tcartercod";
	
	$query ="SELECT 
				tcarres as emp, 
				Tcarconcod as conc, 
				Tcarconnom as concnom, 
				Tcartercod, 
				Tcarternom as tercero,
				sum(ptoval) as pto, 
				sum( Tcarvto ) AS cargado, 
				sum( Tcarfex ) + sum( Tcarfre ) AS facturado, 
				sum( Tcarvto ) - ( sum( Tcarfex ) + sum( Tcarfre ) ) AS fpf
			FROM ".$wbasedato."_000106, (
				SELECT sum(ptocan*ptoval) as ptoval, ptocpt, ptohis, ptoing, ptopro
				FROM {$wbasedato}_000131
				GROUP BY ptohis, ptoing, ptocpt, ptopro
				) AS s131 
			WHERE
				tcarhis=ptohis AND
				tcaring=ptoing AND
				tcarconcod=ptocpt AND
				tcarprocod=ptopro AND
				tcarres like '$optemp' AND
				tcarconnom like '$optcon' AND
				tcarternom like '$optter' AND
				tcarfec BETWEEN '$fechaini' AND '$fechafin' AND
				tcarest = 'on' AND
				SUBSTRING_INDEX( tcarres, '-', 1 ) <> ''
			GROUP BY tcarres, Tcarconcod, Tcartercod
			ORDER BY tcarres, Tcarconcod, Tcartercod";
	
	$query ="SELECT 
				tcarres as emp, 
				Tcarconcod as conc, 
				Tcarconnom as concnom, 
				Tcartercod, 
				Tcarternom as tercero,
				sum(ptoval*ptocan) as pto, 
				sum( Tcarvto ) AS cargado, 
				sum( Tcarfex ) + sum( Tcarfre ) AS facturado, 
				sum( Tcarvto ) - ( sum( Tcarfex ) + sum( Tcarfre ) ) AS fpf,
				count( distinct (Tcardoc) ) as numpac
			FROM ".$wbasedato."_000106, {$wbasedato}_000131
			WHERE
				tcarhis=ptohis AND
				tcaring=ptoing AND
				tcarconcod=ptocpt AND
				tcarprocod=ptopro AND
				tcarres like '$optemp' AND
				tcarconnom like '$optcon' AND
				tcarternom like '$optter' AND
				tcarfec BETWEEN '$fechaini' AND '$fechafin' AND
				tcarest = 'on' AND
				SUBSTRING_INDEX( tcarres, '-', 1 ) <> ''
			GROUP BY tcarres, Tcarconcod, Tcartercod
			ORDER BY tcarres, Tcarconcod, Tcartercod";
	
	$res = mysql_query($query, $conex);

	
	
	$encempresa = "<tr class='encabezadotabla'colspan=5>
			<th style='width:100'>Código Concepto
			<th style='width:250'>Concepto
			<th style='width:100'>Cod Tercero
			<th style='width:250'>Tercero
			<th style='width:100'>Presupuesto			
			<th style='width:100'>Cargado
			<th style='width:100'>Facturado
			<th style='width:100'>Falta por facturar
			<th style='width:100'>Pacientes atendidos
			<th style='width:100'>Detalle";
	
	echo "<table align='center'>";
	
	echo "<tr>";
	echo "<td class='fila1'>Empresa</td>";
	echo "<td class='fila2'>";
	echo ( $optemp == "%" ) ? "Todos" : $optemp;
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'>Concepto</td>";
	echo "<td class='fila2'>"; 
	echo ( $optcon == '%' ) ? "Todos": $optcon ;
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'>Tercero</td>";
	echo "<td class='fila2'>";
	echo ( $optter == '%' ) ?  "Todos" : $optter;
	echo "</td>";
	echo "</tr>";
	
	echo "</table>";
	

	echo "<br><br><table align=center cellspacing=0>
			 	<tr class='encabezadotabla'>
			 		<th width=100>Fecha Inicial</th>
			 		<th width=100>Fecha Final</th>
			 	</tr>
			 	<tr class='fila1' align=center>
			 		<td>$fechaini</td>
			 		<td>$fechafin</td>
			 	</tr>
			 </table>";
	echo "<table align='center' width='100%'>";
	echo "<tr align=center><td align=center colspan=8><br><input type='submit' value='Retornar' style='width:100'>
		  &nbsp;|&nbsp;<INPUT type='button' value='Cerrar' onClick='javascript:window.close();' style='width:100'></td></tr>";
	echo "</table>";
	
	
echo 
"<form action='rep_facPtoEmp.php?wemp_pmla=$wemp_pmla' method=post>
	<table align=center>";
	$cont = 0;	//Contador de Terceros por Concepto
	$rows = mysql_fetch_array($res, MYSQL_BOTH);
	$emp = $rows["emp"];
	$conc = $rows["conc"];
	$concnom= $rows["concnom"];
	
	$conc2 = '';
	
	if( $codigoconcepto ){
		$conc2 = $conc;
	}
	
	
	$fila="fila1";
	
	if(!$rows){
		echo "<p align=center style='font-size:12pt'>No hay registros en la base dedatos para su consulta</p><br><br>";
	}

	//inicializando variables
	$ptoconc = 0;
	$carconc = 0;
	$facconc = 0;
	$fpf = 0;				
	$numpacconc = 0;
	
	$ptoemp = 0;
	$caremp = 0;
 	$facemp = 0;
 	$fpfemp = 0;
 	$numpacemp = 0;
 	
 	$totgenpto = $ptoemp; 
	$totgencar = $caremp;
	$totgenfac = $facemp;
	$totgenfpf = $fpfemp;
	$totgennumpac = $numpacemp;
	
	$terceros = Array();
	
	for($i=0; $rows;){
		
		echo "<tr><td><br><tr class='colorazul4'>
			 <th align='left' colspan=".mysql_num_fields($res).">".$emp.$encempresa;
		
		//Ciclo para empresas
		while( $emp == $rows["emp"])
		{
			
			echo "<tr style='font-size:10pt' class='$fila'>";
			
			//Ciclo para Concepto
			echo "<td bgcolor='white' align='center'><b>$conc</b>
				 <td bgcolor='white'><b>$concnom</b>";
			while($conc == $rows["conc"] && $emp == $rows["emp"] )
			{
				
				@$terceros[ $rows["Tcartercod"] ][ "nombre" ] = $rows["tercero"];
				@$terceros[ $rows["Tcartercod"] ][ "pto" ] += $rows[ "pto" ];
				@$terceros[ $rows["Tcartercod"] ][ "car" ] += $rows[ "cargado" ];
				@$terceros[ $rows["Tcartercod"] ][ "fac" ] += $rows[ "facturado" ];
				@$terceros[ $rows["Tcartercod"] ][ "fpf" ] += $rows[ "fpf" ];
				@$terceros[ $rows["Tcartercod"] ][ "numpac" ] += $rows[ "numpac" ];
				
				$cont++;		//Contador de Terceros por Concepto
				
				for($j=3;$j < mysql_num_fields($res); $j++)
				{					
					if ($j==4)
						echo "<td>".$rows[$j];
					else
						echo "<td align='right'>".number_format($rows[$j],0,"",",");
				}
				
				echo "<td align='center'><a href='rep_facPtoEmpDet.php?wemp_pmla=$wemp_pmla&fechaini=$fechaini&fechafin=$fechafin&optemp={$rows['emp']}&optcon={$rows['conc']}&optter={$rows['Tcartercod']}' target='_blank'>ver</a></td>";
				
				$ptoconc += $rows["pto"];
				$carconc += $rows["cargado"];
				$facconc += $rows["facturado"];
				$fpf += $rows["fpf"];
				$numpacconc += $rows["numpac"];
				
				$ptoemp += $rows["pto"];;
				$caremp += $rows["cargado"];
 				$facemp += $rows["facturado"];
 				$fpfemp += $rows["fpf"];
 				$numpacemp += $rows["numpac"];
 				
 				$rows = mysql_fetch_array($res, MYSQL_BOTH);
				
 				//Calculando el color de la fila
 				$i++;
 				$fila = "fila".($i%2+1);
 				
 				//Imprimienod fila
 				if ($conc == $rows["conc"] && $emp == $rows["emp"]){
					echo "<tr style='font-size:10pt' class='$fila'>
						 <td bgcolor='white'>
						 <td bgcolor='white'>";	
				}
				
			}
			//Imprimi el Total por concepto solo si el concepto
			//tiene mas de un tercero
			if($cont > 1 ){ 
				
				echo "<tr class='fila1' style='font-size:10pt'>
				 	 <td colspan=4><b>Totales por concepto ($conc)</b>
				 	 <td align='right'><b>".number_format($ptoconc,0,"",",")."</b>
				 	 <td align='right'><b>".number_format($carconc,0,"",",")."</b>
				 	 <td align='right'><b>".number_format($facconc,0,"",",")."</b>
				 	 <td align='right'><b>".number_format($fpf,0,"",",")."</b>
				 	 <td align='right'><b>".number_format($numpacconc,0,"",",")."</b>
				 	 <td align='right'><b>&nbsp;</b>";
			}
			
			$cont=0;
			
			$ptoconc = 0;
			$carconc = 0;
			$facconc = 0;
			$numpacconc = 0;
			$fpf = 0;
			
			$conc = $rows["conc"];
			$concnom = $rows["concnom"];
		
		}
		
		//Imprime en pantalla el total por empresas
		echo "<tr class='colorazul4' style='font-size:10pt'>
				<td colspan=4><b>Total por empresa ($emp)</b>
				<td align='right'><b>".number_format($ptoemp,0,"",",")."</b>
				<td align='right'><b>".number_format($caremp,0,"",",")."</b>
				<td align='right'><b>".number_format($facemp,0,"",",")."</b>
				<td align='right'><b>".number_format($fpfemp,0,"",",")."</b>
				<td align='right'><b>".number_format($numpacemp,0,"",",")."</b>
				<td align='right'><b>&nbsp;</b>";
				
		$totgenpto += $ptoemp; 
		$totgencar += $caremp;
		$totgenfac += $facemp;
		$totgenfpf += $fpfemp;
		$totgennumpac += $numpacemp;
		
		$ptoemp = 0;
		$caremp = 0;
 		$facemp = 0;
 		$fpfemp = 0;
 		$numpacemp = 0;
		
		$emp = $rows["emp"];
		
	}
	
	if( $totgenpto > 0 ){
		echo "<tr><td><br></td></tr>";
		echo "<tr class='encabezadotabla'>"; 
		echo "<td colspan='4'><b>Total general</b></td>";
		echo "<td align='right'>".number_format($totgenpto,0,"",",")."</td>";
		echo "<td align='right'>".number_format($totgencar,0,"",",")."</td>";
		echo "<td align='right'>".number_format($totgenfac,0,"",",")."</td>";
		echo "<td align='right'>".number_format($totgenfpf,0,"",",")."</td>";
		echo "<td align='right'>".number_format($totgennumpac,0,"",",")."</td>";
		echo "<td align='right'>&nbsp;</td>";
		echo "</tr>";
	}
	
	echo "</table>";
	
	//Resumen por terceros
	if(  $totgenpto > 0 ){
		
		echo "<br><br>";
		
		echo "<center><b>REPORTE RESUMIDO POR TERCEROS</b></center>";
		echo "<br><br>";
		
		echo "<table align='center'>";
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td width='100'>Codigo</td>";
		echo "<td>Nombre</td>";
		echo "<td width='100'>Presupuesto</td>";
		echo "<td width='100'>Cargado</td>";
		echo "<td width='100'>Facturado</td>";
		echo "<td width='100'>Falta por facturar</td>";
		echo "<td width='100'>Pacientes atendidos</td>";
		echo "<td width='100'>Detalle</td>";
		echo "</tr>";
		
		$i = 0;
		
		$pto = 0;
		$car = 0;
		$fac = 0;
		$fpf = 0;
		$numpac = 0;

		foreach( $terceros as $key => $valueTerceros ){
			
			$i++;
			
			$fila = " class='fila".($i%2+1)."'";
			
			echo "<tr $fila>"; 
//			echo "<td>$key</td>"; number_format($rows[$j],0,"",",");
			echo "<td align='right'>".number_format($key,0,"",",")."</td>"; //number_format($rows[$j],0,"",",");
			echo "<td>{$valueTerceros['nombre']}</td>";
			echo "<td align='right'>".number_format($valueTerceros['pto'],0,"",",")."</td>";
			echo "<td align='right'>".number_format($valueTerceros['car'],0,"",",")."</td>";
			echo "<td align='right'>".number_format($valueTerceros['fac'],0,"",",")."</td>";
			echo "<td align='right'>".number_format($valueTerceros['fpf'],0,"",",")."</td>";
			echo "<td align='right'>".$valueTerceros['numpac']."</td>";
			echo "<td align='center'><a href='rep_facPtoEmpDet.php?wemp_pmla=$wemp_pmla&fechaini=$fechaini&fechafin=$fechafin&optter=$key&optcon=$conc2&optemp=$optemp' target='_blank'>ver</a></td>";
			echo "</tr>";
			
			$pto += $valueTerceros['pto'];
			$car += $valueTerceros['car'];
			$fac += $valueTerceros['fac'];
			$fpf += $valueTerceros['fpf'];
			$numpac += $valueTerceros['numpac'];
		}
		
		echo "<tr class='encabezadotabla'>"; 
		echo "<td colspan='2'>Total</td>";
		echo "<td align='right'>".number_format($pto,0,"",",")."</td>";
		echo "<td align='right'>".number_format($car,0,"",",")."</td>";
		echo "<td align='right'>".number_format($fac,0,"",",")."</td>";
		echo "<td align='right'>".number_format($fpf,0,"",",")."</td>";
		echo "<td align='right'>".$numpac."</td>";
		echo "<td align='right'>&nbsp;</td>";
		echo "</tr>";
		
		echo "</table>";
		
	}
	
	echo "<INPUT type='hidden' name='fechaini' value='$fechaini'>";
	echo "<INPUT type='hidden' name='fechafin' value='$fechafin'>";
	echo "<INPUT type='hidden' name='optemp' value='$optemp'>";
	echo "<INPUT type='hidden' name='optcon' value='$optcon'>";
	echo "<INPUT type='hidden' name='optter' value='$optter'>";
	echo "<INPUT type='hidden' name='mostrar' value='on'>";
	
	echo "<table align='center' width='100%'>";
	echo "<tr><td align=right colspan=".mysql_num_fields($res)."><br><br>Reporte generado a los ".date("d")." día(s) de ".mesEsp(date("n"))."  de ".date("Y");
	echo "<tr><tr align=center><td align=center colspan=8><br><input type='submit' value='Retornar' style='width:100'>
		  &nbsp;|&nbsp;<INPUT type='button' value='Cerrar' onClick='javascript:window.close();' style='width:100'>";
	echo "</table>";
	
	echo "</form>";
}
?>


</body>