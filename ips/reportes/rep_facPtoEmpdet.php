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
<script>

	function retornar(){
	
		window.close();
		window.opener.focus();	
	}
</script>

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


//Inicializa la fecha inicial y final
if( false ){
}
else
{
//	if($optemp == "Todos"){
//		$optemp = "%";		
//	}
//	
//	if($optter == "Todos"){
//		$optter = "%";
//	}
//	
//	if($optcon == "Todos"){
//		$optcon = "%";
//	}

	if( !isset($optemp) || empty($optemp) ){
		$optemp = '%';
	}
	
	if( !isset($optcon) || empty($optcon) ){
		$optcon = '%';
	}
	
	$query ="SELECT 
				tcarhis as his,
				tcardoc as cedula,
				tcarno1 as no1,
				tcarno2 as no2,
				tcarap1 as ap1,
				tcarap2 as ap2,
				tcarres as emp, 
				ptopro,
				ptonpr, 
				Tcarconcod as conc, 
				Tcarconnom as concnom, 
				Tcartercod, 
				Tcarternom as tercero,
				sum(ptoval*ptocan) as pto, 
				sum( Tcarvto ) AS cargado, 
				sum( Tcarfex ) + sum( Tcarfre ) AS facturado, 
				sum( Tcarvto ) - ( sum( Tcarfex ) + sum( Tcarfre ) ) AS fpf
			FROM ".$wbasedato."_000106, ".$wbasedato."_000131
			WHERE
				tcarhis=ptohis AND
				tcaring=ptoing AND
				tcarconcod=ptocpt AND
				tcarprocod=ptopro AND
				tcarres like '$optemp' AND
				tcarconcod like '$optcon' AND
				tcartercod like '$optter' AND
				tcarfec BETWEEN '$fechaini' AND '$fechafin' AND
				tcarest = 'on' AND
				SUBSTRING_INDEX( tcarres, '-', 1 ) <> ''
			GROUP BY tcarres, Tcarconcod, Tcartercod, tcarhis, tcarprocod
			ORDER BY tcarres, Tcarconcod, Tcartercod";
	
	$res = mysql_query($query, $conex);
	
	echo "<form action='rep_facPtoEmp.php?wemp_pmla=$wemp_pmla' method=post>";
	$cont = 0;	//Contador de Terceros por Concepto
	$numrows = mysql_num_rows( $res );
	
	$fila="fila1";
	
	//inicializando variables
	$totpto = 0;
	$totcar = 0;
	$totfac = 0;
	$totfpf = 0;
	
	$his = '';
 	
	if( $numrows > 0 ){
		
		for( $i = 0, $j = 0; $rows = mysql_fetch_array($res); $j++ )
		{
			
			if( $j == 0 ){
				
				echo "<table align=center>";
				echo "<tr>";
				echo "<td class='fila1'>Empresa</td>";
				
				echo "<td class='fila2'>"; 
				echo ( $optemp != '%' ) ? $rows['emp'] : "Todos"; 
				echo "</td>";
				
				echo "</tr>";
				echo "<tr>";
				
				echo "<td class='fila1'>Concepto</td>";
				echo "<td class='fila2'>"; 
				echo ( $optcon != '%' ) ? $rows['concnom']: "Todos"; 
				echo "</td>";
				
				echo "</tr>";
				
				echo "<tr>";
				echo "<td class='fila1'>Codigo del tercero</td>";
				echo "<td class='fila2'>{$rows['Tcartercod']}</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td class='fila1'>Nombre del tercero</td>";
				echo "<td class='fila2'>{$rows['tercero']}</td>";
				echo "</tr>";
				echo "</table><br><br>";
				
				echo "<table align=center cellspacing=0>
			 	<tr class='encabezadotabla'>
			 		<th width=100>Fecha Inicial</th>
			 		<th width=100>Fecha Final</th>
			 	</tr>
			 	<tr class='fila1' align=center>
			 		<td>$fechaini</td>
			 		<td>$fechafin</td>
			 	</tr>
			 	</table><br><br>";
				
				echo "<table align=center>";
				
				$encempresa = "<tr class='encabezadotabla'colspan=5>
						<th style='width:100'>Historia
						<th style='width:100'>Cedula
						<th style='width:300'>Paciente
						<th style='width:300'>Procedimiento
						<th style='width:100'>Presupuestado
						<th style='width:100'>Cargado			
						<th style='width:100'>Facturado
						<th style='width:100'>Falta por facturar";
		
				echo $encempresa;
			}
			
//			$fila = " class='fila".($i%2+1)."'";
			
			if( $rows['his'] != $his )
			{
				
				$fila = " class='fila".($i%2+1)."'";
				
				echo "<tr $fila>";
				
				echo "<td align='center'>".$rows['his']."</td>";
				echo "<td align='right'>".number_format($rows['cedula'],0,"",",")."</td>";
				echo "<td>{$rows['no1']} {$rows['no2']} {$rows['ap1']} {$rows['ap2']}</td>";
				
				$i++;
				
			}
			else
			{
				
				echo "<tr $fila>";
				
				echo "<td align='center' style='border-top-width:0'></td>";
				echo "<td align='right'></td>";
				echo "<td>&nbsp;</td>";
			}
			
			
			echo "<td>{$rows['ptonpr']}</td>";
			echo "<td align='right'>".number_format($rows['pto'],0,"",",")."</td>";
			echo "<td align='right'>".number_format($rows['cargado'],0,"",",")."</td>";
			echo "<td align='right'>".number_format($rows['facturado'],0,"",",")."</td>";
			echo "<td align='right'>".number_format($rows['fpf'],0,"",",")."</td>";
			echo "</tr>";
			
			$totpto += $rows['pto'];
			$totcar += $rows['cargado'];
			$totfac += $rows['facturado'];
			$totfpf += $rows['fpf'];
			
			$his = $rows['his'];
		}
		
		echo "<tr class='encabezadotabla'>";
		echo "<td colspan='2' align='center'>Totales</td>";
		echo "<td align='center'>".$i."</td>";
		echo "<td align='center'>".$j."</td>";
		echo "<td align='right'>".number_format($totpto,0,"",",")."</td>";
		echo "<td align='right'>".number_format($totcar,0,"",",")."</td>";
		echo "<td align='right'>".number_format($totfac,0,"",",")."</td>";
		echo "<td align='right'>".number_format($totfpf,0,"",",")."</td>";
		echo "<tr>";
		
		echo "</table>";
	
	}
	else{
		echo "<p align=center style='font-size:12pt'>No hay registros en la base dedatos para su consulta</p><br><br>";
	}
	
//	if( $totgenpto > 0 ){
//		echo "<tr><td><br></td></tr>";
//		echo "<tr class='encabezadotabla'>"; 
//		echo "<td colspan='4'><b>Total general</b></td>";
//		echo "<td align='right'>".number_format($totgenpto,0,"",",")."</td>";
//		echo "<td align='right'>".number_format($totgencar,0,"",",")."</td>";
//		echo "<td align='right'>".number_format($totgenfac,0,"",",")."</td>";
//		echo "<td align='right'>".number_format($totgenfpf,0,"",",")."</td>";
//		echo "</tr>";
//	}
	
	
	
	echo "</table>";
	
	echo "<table align='center' width='100%'>";
	echo "<tr><td align=right colspan=".mysql_num_fields($res)."><br><br>Reporte generado a los ".date("d")." día(s) de ".mesEsp(date("n"))."  de ".date("Y");
	echo "<tr><tr align=center><td align=center colspan=8><br><input type='button' value='Cerrar' style='width:100' onClick='retornar();'>";
	echo "</table>";
	
	echo "</form>";
}
?>


</body>