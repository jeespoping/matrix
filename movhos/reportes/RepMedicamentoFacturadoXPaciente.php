<?php
include_once("conex.php");

/*************************************************************************************************
 * 	Fecha de creacion:	2009-05-14
 * 	Por:				Edwin Molina Grisales
 * 	Descripcion:		Consultar un medicamento facturado a un paciente que se encuentre activo
 * 						en el sistema con la cantidad cargada de dicho medicamento.
 *************************************************************************************************/

/*************************************************************************************************
 * 												FUNCIONES
 *************************************************************************************************/

/**
 * Consulta el nombre del paciente
 * 
 * @param $his				Historia del paciente
 * @return string			Nombre del paciente
 */
function consultarNombrePaciente( $his ){
	
	global $conex;
	global $wbasedato;
	
	$nombre = "";
	
	$sql = "SELECT
				pacno1, pacno2, pacap1, pacap2, pacced
			FROM
				root_000036 a, root_000037 b
			WHERE
				pacced = oriced
				and orihis = '$his'";
	
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de consulta de Nombre del Paciente - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$nombre = $rows[0]." ".$rows[1]." ".$rows[2]." ".$rows[3];
	}
	
	return $nombre;
	
}


/**
 * Consulta la habitacion del paciente
 * 
 * @param $his				Historia del paciente
 * @param $ing				Ingreso del paciente
 * @return string			Habitacion del paciente
 */
function consultarHabitacionPaciente( $his, $ing ){

	global $conex;
	global $wbasedato;
	
	$hab = "";
	
	$sql = "SELECT
				ubihac
			FROM
				{$wbasedato}_000018 a
			WHERE
				ubihis = '$his'
				and ubiing = '$ing'
				and ubialp <> 'on' 
				and ubiald <> 'on'";
	
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de consulta de Habitacion del Paciente - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$hab = $rows[0];
	}
	
	return $hab;
	
}

/**
 * Consulta los nombres y apellidos del paciente y la habitación actual en la que se encuentra
 * de acuerdo a la historia e ingreso
 * 
 * @param $his
 * @param $ing
 * @param $nombre
 * @param $hab
 * @return unknown_type
 */
function consultarPaciente( $his, $ing, &$nombre, &$hab ){
	
	$nombre = consultarNombrePaciente( $his );
	$hab = consultarHabitacionPaciente( $his, $ing );
	
}

/**
 * Consulta el nombre de un Medicamento
 * 
 * @param $art				Codigo del articulo
 * @return string			Nombre del articulo
 */
function consultarMedicamento( $art ){
	
	global $conex;
	global $wbasedato;
	
	$nomart = "";
	
	$sql = "SELECT
				artcom, artcod
			FROM
				{$wbasedato}_000026 a
			WHERE
				artcod = '$art' ";
	
	$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de consulta de Medicamento - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$nomart = $rows[0];
	}
	else{
		$sql = "SELECT
					artcom, artcod
				FROM
					cenpro_000002 a
				WHERE
					artcod = '$art' ";
				
		$res =  mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de consulta de Medicamento - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$nomart = $rows[0];
		}
	}
	
	return $nomart;
	
}

function pintarMedicamento( $art, $ini, $fin ){
	
	$nombre = consultarMedicamento( $art );
	
	echo "<br><table align='center'>";
	echo "<tr>";
	echo "<td class='encabezadotabla'>Codigo del Articulo</td>";
	echo "<td class='fila1'>".strtoupper($art)."</td>";
	echo "<tr>";
	echo "<td class='encabezadotabla'>Nombre del articulo</td>";
	echo "<td class='fila1'>$nombre</td>";
	echo "<tr>";
	echo "<td class='encabezadotabla'>Desde</td>";
	echo "<td class='fila1'>$ini</td>";
	echo "<tr>";
	echo "<td class='encabezadotabla'>Hasta</td>";
	echo "<td class='fila1'>$fin</td>";
	echo "</table>";
	echo "<br><br>";
	
}

/*************************************************************************************************
 * 												FIN FUNCIONES
 *************************************************************************************************/

include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = $institucion->baseDeDatos;
$wentidad = $institucion->nombre;

$wactualiz = "Octubre 14 de 2009";
encabezado("REPORTE MEDICAMENTO FACTURADO POR PACIENTE", $wactualiz, "clinica");

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );

session_start();
if(false && !isset($_SESSION['user'])){
	echo "error";
}
else{
	if( !isset($mostrar) || $mostrar == '0' || empty( $art ) || $art == "" ){
		/**********************************
		 * PRESENTACION INICIAL
		 **********************************/
		
		//fecha inicial del reporte
		if( !isset($fecini) ){
			$fecini = date("Y-m-01");
		}
		
		//fecha final del reporte
		if( !isset($fecfin) ){
			$fecfin = date("Y-m-t");
		}
		
		echo "<form method='post'>";
		
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Codigo del Articulo</td>";
		echo "<td class='fila1'><INPUT type='text' name='art'></td>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>";
		echo "Fecha Inicial"; 
		echo "</td>";
		echo "<td class='fila1'>";
		campoFechaDefecto("fecini", $fecini ); 
		echo "</td>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>";
		echo "Fecha Final"; 
		echo "</td>";
		echo "<td class='fila1'>";
		campoFechaDefecto("fecfin", $fecfin ); 
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		
		echo "<input type='hidden' name='mostrar' value='1'>";
		
		echo "<br>";
		echo "<table align='center'>";
		echo "<tr align='center'>";
		echo "<td>";
		echo "<INPUT type='submit' value='Mostrar' style='width:100'>"; 
		echo "</td>";
		echo "<td>";
		echo "<BUTTON onClick='javascript:cerrarVentana();' style='width:100'>Cerrar</BUTTON>";
		echo "</td>";
		echo "</tr>";
		
		echo "</form>";
	}
	else{
		/*********************************
		 * CUERPO DEL REPORTE
		 ********************************/
		
		echo "<form method='post'>";
		
		//se consultan los cargos
//		$q = " CREATE TEMPORARY TABLE if not exists tempo2 as " 
		$sql =	"  SELECT fdeart, sum(fdecan) as fdecan, fenhis, fening "
				."   FROM ".$wbasedato."_000002 a,".$wbasedato."_000003, ".$wbasedato."_000011  "
				."  WHERE fennum = fdenum "
				."    AND a.fecha_data BETWEEN '$fecini' AND '$fecfin' "
				."    AND fenest = 'on' "
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND fdeart = '$art' "
				."    AND ccoima = 'off' "
				."  GROUP BY fenhis, fening "
				."  UNION ALL "
				." SELECT fdeari as fdeart, count(distinct(fdenum)) as fdecan, fenhis, fening "
				."   FROM ".$wbasedato."_000002 a,".$wbasedato."_000003,  ".$wbasedato."_000011 "
				."  WHERE fennum = fdenum "
				."    AND a.fecha_data BETWEEN '$fecini' AND '$fecfin' "
				."    AND fenest = 'on' "
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND fdeart = '$art' "
				."    AND ccoima = 'on' "
				."    AND fdelot <> ''  "
				."  GROUP BY fenhis, fening  "
				."  UNION ALL "
				." SELECT fdeart, count(distinct(fdenum)) as fdecan, fenhis, fening "
				."   FROM ".$wbasedato."_000002 a,".$wbasedato."_000003,  ".$wbasedato."_000011 "
				."  WHERE fennum = fdenum "
				."    AND a.fecha_data BETWEEN '$fecini' AND '$fecfin' "
				."    AND fenest = 'on' "
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND fdeart = '$art' "
				."    AND ccoima = 'on' "
				."    AND fdelot = ''  "
				."  GROUP BY fenhis, fening  ";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno() - " - Error en el query - " - mysql_errno() );

		if( mysql_num_rows($res) > 0 ){
			
			pintarMedicamento( $art, $fecini, $fecfin );
			
			echo "<table align='center'>";
			echo "<tr class='encabezadotabla'>";
			echo "<td>HAB.</td>";
			echo "<td>PACIENTE</td>";
			echo "<td>HISTORIA</td>";
			echo "<td>CANTIDAD</td>";
			echo "<td>Detalle</td>";
			echo "</tr>";
		
			for( $i=0; $rows = mysql_fetch_array($res); ){
				
				$totFacturado = $rows[1];
				
				$nombre = "";
				$hab = "";
				
				$whis = $rows[2];
				$wing = $rows[3];
				
				consultarPaciente( $whis, $wing, $nombre, $hab );
				
				if( !empty($hab) ){
					
					$classfila = "class='fila".(($i%2)+1)."'";
					
					$i++;
					
					$link = "rep_facturado_vs_aplicado_detalle.php?wemp=$wemp_pmla&whis=$whis&wing=$wing&whab=$hab&wart=$art&wdet=on&wpac=$nombre";
					
					$sql=" CREATE TEMPORARY TABLE if not exists tempo1 as "          //APLICACIONES
//						." SELECT SUM(aplcan) AS can "
//						."   FROM ".$wbasedato."_000015 "
//						."  WHERE aplhis = '".$whis."'"
//						."    AND apling = '".$wing."'"
//						."    AND aplart = '".$rows[0]."'"
//						."    AND aplest = 'on' "
//						."  UNION ALL "
						." SELECT SUM(descan) AS can "                               //DESCARTES
						."   FROM ".$wbasedato."_000035 a, ".$wbasedato."_000031 "
						."  WHERE denhis = '".$whis."'"
						."    AND dening = '".$wing."'"
						."    AND a.fecha_data BETWEEN '$fecini' AND '$fecfin' "
						."    AND dencon = descon "
						."    AND desart = '".$rows[0]."'"
						."  UNION ALL "
						." SELECT sum(fdecan) "                                      //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
						."   FROM ".$wbasedato."_000002 a,".$wbasedato."_000003, ".$wbasedato."_000011  "
						."  WHERE fenhis = ".$whis
						."    AND fening = ".$wing
						."    AND a.fecha_data  BETWEEN '$fecini' AND '$fecfin' "
						."    AND fennum = fdenum "
						."    AND fenest = 'on' "
						."    AND fdeart = '".$rows[0]."'"
						."    AND fentip in ('DA','DP') "
						."    AND fencco = ccocod "
						."    AND ccoima = 'off' "
						//."  GROUP BY fdenum "
						."  UNION ALL "
						." SELECT COUNT(DISTINCT(fdenum)) "                           //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
						."   FROM ".$wbasedato."_000002 a,".$wbasedato."_000003, ".$wbasedato."_000011  "
						."  WHERE fenhis = ".$whis
						."    AND fening = ".$wing
						."    AND fennum = fdenum "
						."    AND a.fecha_data  BETWEEN '$fecini' AND '$fecfin' "
						."    AND fenest = 'on' "
						."    AND fdeari = '".$rows[0]."'"
						."    AND fentip in ('DA','DP') "
						."    AND fencco = ccocod "
						."    AND ccoima = 'on' ";// echo ".......<br><br>".$sql."fin.....<br><br>";
					
					$resapl = mysql_query( $sql, $conex ) or die ("Error: ".mysql_errno()." - Error en el query: ".$sql." - ".mysql_error());
					
					$sql = " SELECT SUM(can) "
						."   FROM tempo1 "
						."  WHERE can != '' ";
					
					$resapl = mysql_query( $sql, $conex ) or die ("Error: ".mysql_errno()." - en el query: ".$sql." - ".mysql_error());
					
					$wnumapl = mysql_num_rows($resapl);
					
					if( $rowapl = mysql_fetch_array( $resapl ) ){
						echo "<tr $classfila>";
						echo "<td align='center'>$hab</td>";
						echo "<td>$nombre</td>";
						echo "<td align='center'>$whis-$wing</td>";
						echo "<td align='right'>".($rows[1]-$rowapl[0])."</td>";
						echo "<td align='center'><a href='$link' target='blank'>ver</a></td>";
						echo "</tr>";
					}
					
					$q = " DROP TABLE tempo1 ";
					$resapl = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				}
				
			}//fin for
			
			echo "</table>";
			
		}//fin if
		else{
			echo "<p align='center'>No se encontro ningun Registro</p>";
		}
		
		echo "<input type='hidden' name='mostrar' value='0'>";
		echo "<input type='hidden' name='fecini' value='$fecini'>";
		echo "<input type='hidden' name='fecfin' value='$fecfin'>";
		
		echo "<br>";
		echo "<table align='center'>";
		echo "<tr align='center'>";
		echo "<td>";
		echo "<INPUT type='submit' value='Retornar' style='width:100'>"; 
		echo "</td>";
		echo "<td>";
		echo "<BUTTON onClick='javascript:cerrarVentana();' style='width:100'>Cerrar</BUTTON>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		
		echo "</form>";
	}//fin cuerpo del reporte
}

?>
