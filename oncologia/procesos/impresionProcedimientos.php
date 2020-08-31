<?php
include_once("conex.php");

/*********************************************************************************************************
 *	 									     FUNCIONES
 *********************************************************************************************************/

/**
 * Consulta procedimientos
 * 
 * @param $his		Historia
 * @param $fecha	Fecha
 * @return unknown_type
 */
function consultarProcedimiento( $his,  $fecha, $ing = '' ){
	
	global $conex;
	global $wbasedato;
	
	$notas = Array();
	
	$sql = "SELECT
				pronot
			FROM
				{$wbasedato}_000016
			WHERE
				prohis = '$his'
				AND proing like '%$ing'
				AND profec = '$fecha'
				AND proest = 'on'
				AND pronot != '.'				
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		 $notas[$i] = $rows;
	}
	
	return $notas;
	
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
	
	if( !isset($procedimiento) ){
		
		if( !isset($fecha) || empty($fecha) ){
			$fecha = date("Y-m-d");			
		}
		
		$procedimiento = consultarProcedimiento( $his, $fecha, $ing  );
	}
	
	if( $infoMedico->codigo != '' ){
		
		echo "<div id='dvProcedimiento'>";
		for( $i = 0; $i < count($procedimiento); $i++ ){
			
			if( $i > 0 ){
				echo "<div class='saltopagina'></div>";
			}
		
			$dx = consultarDx( $his, $ing, $fecha );
			
//			echo "<body id='bdProcedimiento'>";
//			echo "<div id='dvProcedimiento'>";
			echo "<div>";

			echo "Medellín, ".date( "d" )." de ".nombreMes( date( "n" ) )." de ".date( "Y" );
			echo "<br><br><br>";

			echo "<center><b><H3>PROCEDIMIENTOS</b></center></H3><br><br>";

			echo "Favor expedir las siguientes ordenes:";
			echo "<br><br>";
			echo str_replace( "\n", "<br>", htmlentities( trim($procedimiento[$i]['pronot']) ) );

			echo "<br><br><br>";
			echo "Paciente: ".$infoPac['Pacnpa']."<br>";
			echo "Tipo de Documento: ".$infoPac['Pactid']."<br>";
			echo "Nro. de Docuemento: ".number_format($infoPac['Pacnid'], 0, "","." )."<br>";
			echo "<br><br>";

			echo "Diagnóstico: ".$dx;
			echo "<br><br><br>";

			echo "Las ordenes pueden ser Enviadas al Fax: ".$infoMedico->consultorio->fax;

			echo "<br><br><br>";

			echo "Atentamente, ";
			echo "<br><br><br><br><br>";
			echo $infoMedico->nombre;
			echo "<br>C.C. ";
			echo number_format( $infoMedico->nroIdentificacion, 0, '', '.' );
			echo "<br>R.M.: ";
			echo $infoMedico->registro;
			
			
			
			
			echo "</div>";
//			echo "</body>";

//			echo "</td></tr><table>";
		}
		echo "</div>";
	}
	else{
		echo "<center><b>EL DOCTOR NO SE ENCUENTRA REGISTRADO EN LA BASE DE DATOS</b></center>";
	}
}
/*********************************************************************************************************
 * 										FIN DEL PROGRAMA
 *********************************************************************************************************/
?>