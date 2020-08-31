<?php
include_once("conex.php");

/*********************************************************************************************************
 * Programa:		impresionFormulasMedicas.php
 * Por:				Edwin Molna Grisales
 * Descripcion:		Imprime la Formula Médicas para un paciente
 * 
 *********************************************************************************************************/

/**
 * Para la impresión del Dr. Rodolfo Gomez Wolf el formato de formulas tiene un tamaño de 21.5x14.0 cm
 */

/*********************************************************************************************************
 *	 									    FUNCIONES
 *********************************************************************************************************/

/**
 * Consulta la entidad responsable para un paciente
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function consultarEntidad( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000003
			WHERE
				inghis = '$his'
				AND inging = '$ing'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Ingemp' ];
	}
	
	return $val;
	
}

/**
 * Consulta las NOtas de Formulas medicas para una historia e ingreso datos
 * 
 * @param $his	Hisotria
 * @param $ing	Ingreso
 * @return unknown_type
 */
function consultarNotasFQH( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000001
			WHERE
				hclhis = '$his'
				AND hcling like '%$ing'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Hclfqh' ];
	}
	
	return $val;

}
/**
 * Consultar notas
 * 
 * @param $his		Historia
 * @param $fecha	Fecha
 * @return unknown_type
 */
function consultarFormula( $his,  $fecha, $ing = '' ){
	
	global $conex;
	global $wbasedato;
	
	$nota = '';
	$fila1 = '';
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000015, {$wbasedato}_000006, {$wbasedato}_000004
			WHERE
				rechis = '$his'
				AND recing like '%$ing'
				AND recfec = '$fecha'
				AND recest = 'on'
				AND recmed = artcod
				AND recvia = viacod
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows ){
		
		echo "<table align='center' style='font-size:10pt'>";
		
//		echo "<tr align='center'>";
//		echo "<td><b>Medicamento</b></td>";
//		echo "<td><b>V&iacute;a de<br>aplicaci&oacute;n</b></td>";
//		echo "<td><b>Dosis</b></td>";
//		echo "<td><b>Cantidad</b></td>";
//		echo "</tr>";
		
		for( $i =0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$fila = "class='fila".($i%2+1)."'";
			
			$exp = explode(  "-", $rows['Recffa'] );
			
			if( empty( $rows['Artcon'] ) ){
				$rows['Artcon'] = 1;
			}
			
//			echo "<tr $fila>";
			echo "<tr>";
			echo "<td>{$rows['Artnom']}</td>";
//			echo "<td>{$rows['Viades']}</td>";
//			echo "<td align='center'>{$rows['Recdos']} {$rows['Artuni']}</td>";
//			echo "<td align='center'>#".ceil( ($rows['Recdto']*($rows['Recdos']))/($rows['Artcon']) )."</td>";
			echo "<td align='center'>#".(($rows['Recdto']*1)/1)."</td>";
			echo "</tr>";
		 	
			if( !empty( $rows['Recdci'] ) ){
				echo "<tr>";
				echo "<td colspan='2'>";
				echo "{$rows['Recdci']} por via {$rows['Viades']}";
				echo "</td>";
				echo "</tr>";
				echo "<tr {$fila1}><td colspan='2'><br></td></tr>";
			}
			else{
				echo "<tr {$fila1}><td colspan='2'><br></td></tr>";
			}
		}
		
		echo "</table>";
	}
	else{
	}
	
	return $nota;
}

/*********************************************************************************************************
 *	 									  FIN DE FUNCIONES
 *********************************************************************************************************/

/*********************************************************************************************************
 * 										INCIO DEL PROGRAMA
 *********************************************************************************************************/

include_once("root/comun.php");

include_once( "../../consultorios/procesos/funcionesGenerales.php" );

$conex = obtenerConexionBD("matrix");

//Seteando variables
if( !isset($infoMedico) ){
	$infoMedico = new classMedico( $doc );
}

$key = substr( $user, 2, strlen($user) );

$wbasedato = $infoMedico->bdHC;
$wbasecitas = $infoMedico->bdCitas;

if(!isset( $infoPac ) ){
	$infoPac = '';
	infoPaciente( $his, $infoPac );
}

if( false ){
	
}
else{
	
	if( !isset($formula) ){
		
		if( !isset($fecha) || empty($fecha) ){
			$fecha = date("Y-m-d");			
		}
		
//		$formula = consultarFormula( $his, $fecha, $ing  );
	}
	
	if( $infoMedico->codigo != '' ){
		
		if( true || !empty($formula ) ){
		
			$dx = consultarDx( $his, $ing, $fecha );
			echo "<div id='bdFormulas'>";
			echo "<div id='dvFormulas1' class='centrar3' style='width:12cm'>";

//			echo "Medellín, ".date( "d" )." de ".nombreMes( date( "n" ) )." de ".date( "Y" );
//			echo "<br><br><br>";

//			echo "<center><b><H3>FORMULA MEDICA</b></center></H3><br><br>";

			list( $tipoDocumentoIdentidad ) = explode( "-", $infoPac['Pactid'] );
			
//			echo $infoPac['Pacnpa']."<br>";
//			echo $tipoDocumentoIdentidad." ";
//			echo number_format($infoPac['Pacnid'], "", "", "." )."<br>";
//			echo "Dirección: ".$infoPac['Pacdir']."<br>";
//			echo "Teléfono: ".$infoPac['Pactel']."<br>";
//			
//			list( $aux ,$entidad ) = explode( "-", consultarEntidad( $his, $ing ) );
//			
//			if( $entidad != '' ){
//				echo "Entidad: ".$entidad;
//			}
			
			echo "<table align='right' style='width:12cm; font-size:10pt'>";
			
			echo "<tr>";
			echo "<td style='height:4.0cm' colspan='5'></td>";
			echo"</tr>";
			
			echo "<tr align='center'>";
			echo "<td style='width:1.2cm' rowSpan='5'></td>";
			echo "<td style='width:6.6cm' align='left'><b>".$infoPac['Pacnpa']."</b></td>";
			echo "<td style='width:0.9cm'>".date( "d" )."</td>";
			echo "<td style='width:0.9cm'>".date( "m" )."</td>";
			echo "<td style='width:1.9cm'>".date( "Y" )."</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='4'><br>";
			echo $tipoDocumentoIdentidad." ";
			echo number_format($infoPac['Pacnid'], 0, "", "." )."<br>";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='4'>";
			echo "Dirección: ".$infoPac['Pacdir'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='4'>";
			echo "Teléfono: ".$infoPac['Pactel'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='4'>";
			list( $aux ,$entidad ) = explode( "-", consultarEntidad( $his, $ing ) );
			
			if( $entidad != '' ){
				echo "Entidad: ".$entidad;
			}
			echo "</td>";
			echo "</tr>";
			
//			echo "</table>";
			
//			echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";

//			echo "<table>";
			echo "<tr>";
			echo "<td colspan='5'>";
			echo "<br><b>Diagnóstico:</b> ".$dx;
			echo "<br><br><br>";
			echo "</td>";
			echo "</tr>";
			
//			echo "</table><br>";

//			echo str_replace( "\n", "<br>", htmlentities( trim($formula) ) );
			
			echo "<tr>";
			echo "<td colspan='5'>";
			
			consultarFormula( $his, $fecha, $ing  );
			
			$fqh = consultarNotasFQH($his, $ing );
			
			if( $fqh != '' && $fqh != '.' ){
				echo "<br><br><b>Notas:</b> ";
				echo str_replace( "\n", "<br>", htmlentities( trim( consultarNotasFQH($his, $ing ) ) ) );
			}

			echo "</td>";
			echo "</tr>";
			
			echo "</table>";

//			echo "<br><br>";
//			echo $infoMedico->nombre;
//			echo "<br>C.C. ";
//			echo number_format( $infoMedico->nroIdentificacion, '', '', '.' );
//			echo "<br>R.M.: ";
//			echo $infoMedico->registro;
//			echo "<br>Dirección: ";
//			echo $infoMedico->consultorio->direccion;
//			echo "<br>Teléfono: ";
//			echo $infoMedico->consultorio->telefono;
			
			echo "</div>";
			echo "</div>";
		
		}
	}
	else{
		echo "<center><b>EL DOCTOR NO SE ENCUENTRA REGISTRADO EN LA BASE DE DATOS</b></center>";
	}
}

/*********************************************************************************************************
 * 										INCIO DEL PROGRAMA
 *********************************************************************************************************/
?>