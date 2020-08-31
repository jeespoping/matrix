<style>
<!--
.table{
	border-style:solid;
	border-collapse:collapse;
	border-color:black;
	border-width:1px;
	width:100%;
	font-size:8pt;
}

.table td{
	border-width:2px;
	border-style:solid;
	border-color:black;
}

.tdTopLeftRight{
	border-bottom-style:none;
	border-bottom-width:2px;
	border-bottom-color:black;
	
	border-top-width:2px;
	border-top-color:black;
	border-top-style:solid;
	
	border-left-width:2px;
	border-left-color:black;
	border-left-style:solid;
	
	border-right-width:2px;
	border-right-color:black;
	border-right-style:solid;
}

.tdBottomLeftRight{
	border-bottom-style:solid;
	border-bottom-width:2px;
	border-bottom-color:black;
	
	border-top-width:2px;
	border-top-color:black;
	border-top-style:none;
	
	border-left-width:2px;
	border-left-color:black;
	border-left-style:solid;
	
	border-right-width:2px;
	border-right-color:black;
	border-right-style:solid;
}

.tdLeftRight{
	border-bottom-style:none;
	border-bottom-width:2px;
	border-bottom-color:black;
	
	border-top-width:2px;
	border-top-color:black;
	border-top-style:none;
	
	border-left-width:2px;
	border-left-color:black;
	border-left-style:solid;
	
	border-right-width:2px;
	border-right-color:black;
	border-right-style:solid;
}

.tdTop{
	border-bottom-style:none;
	border-bottom-width:2px;
	border-bottom-color:black;
	
	border-top-width:2px;
	border-top-color:black;
	border-top-style:solid;
	
	border-left-width:2px;
	border-left-color:black;
	border-left-style:none;
	
	border-right-width:2px;
	border-right-color:black;
	border-right-style:none;
}

.tdbottom{
	border-bottom-style:none;
	border-top-width:2px;
	border-top-color:black;
	border-left-width:2px;
	border-left-color:black;
	border-right-width:2px;
	border-right-color:black;
	border-top-style:solid;
	border-left-style:solid;
	border-right-style:solid;
}

.tdTopBottom{
	border-bottom-style:solid;
	border-bottom-width:2px;
	border-bottom-color:black;
	
	border-top-width:2px;
	border-top-color:black;
	border-top-style:solid;
	
	border-left-width:2px;
	border-left-color:black;
	border-left-style:none;
	
	border-right-width:2px;
	border-right-color:black;
	border-right-style:none;
}

-->
</style>

<?php
include_once("conex.php");
/*************************************************************************************************************
 * 											  FUNCIONES
 ************************************************************************************************************/

function consultarCTC( $his, $ing, $fecha, &$infoCTC ){
	
	global $conex;
	global $wbasedato;
	
	$infoCTC = '';
		
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000029
			WHERE
				ctchis = '$his'
				AND ctcing = '$ing'
				AND ctcfdd = '$fecha'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$infoCTC = $rows;
	}
	
}
/*************************************************************************************************************
 * 										   FIN DE FUNCIONES
 ************************************************************************************************************/

/*************************************************************************************************************
 * 										   INICIO DEL PROGRAMA
 ************************************************************************************************************/

include_once( "root/comun.php" );

include_once( "../../consultorios/procesos/funcionesGenerales.php" );
	
$conex = obtenerConexionBD( "matrix" );

$key = substr($user, 2, strlen($user));

//Seteando variables
if( !isset($infoMedico) ){
	$infoMedico = new classMedico( $doc );
}

$wbasedato = $infoMedico->bdHC;
$wbasecitas = $infoMedico->bdCitas;

if( @$consultaAjax ){
	
	switch( $consultaAjax ){
		
		case 10:
			break;
			
		case 11:
			break;
			
		case 12:
			break;
		
		default:
			break;
	}
	
}
else{
	
	if( !isset($_SESSION['user']) ){
		echo "Error: Usuario No registrado";
	}
	else{
		
		$infoCTC = '';
		
		if(!isset( $infoPac ) ){
			$infoPac = '';
			infoPaciente( $his, $infoPac );
		}
		
		consultarCTC( $his, $ing, $fecha, $infoCTC );
		
		if( count( $infoCTC ) > 0 && !empty($infoCTC) ){
			
			$exp = explode( '-', $infoCTC['Ctcfre'] );
			$freYear = $exp[0];
			$freMonth = $exp[1];
			$freDay = $exp[2];
			
			$exp = explode( '-', $infoCTC['Ctcfdd'] );
			$fddYear = $exp[0];
			$fddMonth = $exp[1];
			$fddDay = $exp[2];

			$exp = explode( '-', $infoCTC['Ctcfdx'] );
			$fdxYear = $exp[0];
			$fdxMonth = $exp[1];
			$fdxDay = $exp[2]; 
			
			if( $infoPac['Pacsex'] == '01-M' ){
				$sexCTC = 'Masculino';
			}
			else{
				$sexCTC = 'Femenino';
			}
			
			echo "<div id='dvCTC'>";
			
			echo "<center><b>INSTITUTO DE CANCEROLOGÍA</b></center>";
			echo "<center>SOLICITUD Y JUSTIFICACIÓN DEL MEDICO TRATANTE DEL USO<br>DE MEDICAMENTO NO POS</center>";
			echo "<br><br>";
			echo "<table align='center' border=2 style='border-style:solid; border-collapse:collapse;'>";
			echo "<tr>";
			echo "<td><b>D&iacute;a</b></td>";
			echo "<td>";
			echo $fddDay;
			echo "</td>";
			echo "<td><b>Mes</b></td>";
			echo "<td>";
			echo $fddMonth;
			echo "</td>";
			echo "<td><b>Año</b></td>";
			echo "<td>";
			echo $fddYear;
			echo "</td>";
			echo "</tr>";
			echo "</table>"; 
			echo "<br><br>";
			
			echo "<table class='table' id='tbCTC'>";
			echo "<tr style='display:none'>";
			echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				  <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				  <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='24'>";
			echo "Nombre del paciente";
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='24' align='center'>";
			echo $infoPac['Pacnpa'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='8'>";
			echo "Documento de Identidad";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "CC";
			echo "</td>";
			echo "<td colspan='1'>";
			echo ( substr($infoPac['Pactid'],0, strpos($infoPac['Pactid'], '-' ) ) ==  "CC" ) ? "X": "";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "TI";
			echo "</td>";
			echo "<td colspan='1'>";
			echo ( substr($infoPac['Pactid'],0, strpos($infoPac['Pactid'], '-' ) ) ==  "TI" ) ? "X": "";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "RC";
			echo "</td>";
			echo "<td colspan='1'>";
			echo ( substr($infoPac['Pactid'],0, strpos($infoPac['Pactid'], '-' ) ) ==  "RC" ) ? "X": "";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "Otros";
			echo "</td>";
			echo "<td colspan='1'>";
			echo (    substr($infoPac['Pactid'],0, strpos($infoPac['Pactid'], '-' ) ) !=  "CC" 
				   && substr($infoPac['Pactid'],0, strpos($infoPac['Pactid'], '-' ) ) !=  "TI" 
				   && substr($infoPac['Pactid'],0, strpos($infoPac['Pactid'], '-' ) ) !=  "RC" ) ? "X": "";
			echo "</td>";
			echo "<td colspan='3'>";
			echo "Numero:";
			echo "</td>";
			echo "<td colspan='5'>";
			echo $infoPac['Pacnid'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='14'>";
			echo "</td>";
			echo "<td colspan='4'>";
			echo "Tipo de vinculacion";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "A";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "DH";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "P";
			echo "</td>";
			echo "<td colspan='1'>";
			echo "";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='3'>";
			echo "Historia";
			echo "</td>";
			echo "<td colspan='21'>";
			echo $infoPac['Pachis'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='4'>";
			echo "DIAGNÓSTICO";
			echo "</td>";
			echo "<td colspan='12'>";
			echo $infoCTC['Ctcdxp'];
			echo "</td>";
			echo "<td colspan='4'>";
			echo "Fecha";
			echo "</td>";
			echo "<td colspan='4'>";
			echo $infoCTC['Ctcfdx'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='4'>";
			echo "TRATAMIENTO";
			echo "</td>";
			echo "<td colspan='20'>";
			echo $infoCTC['Ctctip'];
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br>";
			
			echo "<b>DESCRIPCION DEL CASO CLINICO</b><br>";
			
			echo "<table class='table'>";
			echo "<tr>";
			echo "<td>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcdcc'] );
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "<br>";
			echo "<b>MEDICAMENTOS POS UTILIZADOS</b>";
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td>";
			echo "1. Principio activo";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcmp1'];
			echo "</td>";
			echo "<td>";
			echo "Posología";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcco1'];
			echo "</td>";
			echo "<td>";
			echo "Presentación";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcpr1'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "Dosis/Día";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdd1'];
			echo "</td>";
			echo "<td>";
			echo "Cantidad";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdt1']*$infoCTC['Ctcdd1'];
			echo "</td>";
			echo "<td>";
			echo "Tiempos/Meses";
			echo "</td>";
			echo "<td>";
			echo number_format( $infoCTC['Ctcdt1']/30, 1 );
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td>";
			echo "2. Principio activo";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcmp2'];
			echo "</td>";
			echo "<td>";
			echo "Posología";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcco2'];
			echo "</td>";
			echo "<td>";
			echo "Presentación";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcpr2'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "Dosis/Día";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdd2'];
			echo "</td>";
			echo "<td>";
			echo "Cantidad";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdt2']*$infoCTC['Ctcdd2'];
			echo "</td>";
			echo "<td>";
			echo "Tiempos/Meses";
			echo "</td>";
			echo "<td>";
			echo number_format( $infoCTC['Ctcdt2']/30, 1 );
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td>";
			echo "3. Principio activo";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcmp3'];
			echo "</td>";
			echo "<td>";
			echo "Posología";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcco3'];
			echo "</td>";
			echo "<td>";
			echo "Presentación";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcpr3'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "Dosis/Día";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdd3'];
			echo "</td>";
			echo "<td>";
			echo "Cantidad";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdt3']*$infoCTC['Ctcdd3'];
			echo "</td>";
			echo "<td>";
			echo "Tiempos/Meses";
			echo "</td>";
			echo "<td>";
			echo number_format( $infoCTC['Ctcdt3']/30, 1 );
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td>";
			echo "4. Principio activo";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcmp4'];
			echo "</td>";
			echo "<td>";
			echo "Posología";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcco4'];
			echo "</td>";
			echo "<td>";
			echo "Presentación";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcpr4'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "Dosis/Día";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdd4'];
			echo "</td>";
			echo "<td>";
			echo "Cantidad";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdt4']*$infoCTC['Ctcdd4'];
			echo "</td>";
			echo "<td>";
			echo "Tiempos/Meses";
			echo "</td>";
			echo "<td>";
			echo number_format( $infoCTC['Ctcdt4']/30, 1 );
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo"<br>";
			
			echo "<b>RESPUESTA CLÍNICA Y PARACLÍNICA ALCANZADA CON MEDICAMENTO POS</b>";
			
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td colspan='5'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcrcp'] );
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "<b>Reacciones adversas o intolerancia a medicamentos POS<b>";
			echo "</td>";
			echo "<td>";
			echo "SI";
			echo "</td>";
			echo "<td>";
			echo ( $infoCTC['Ctcrai'] == 'on' ) ? "X" : "";
			echo "</td>";
			echo "<td>";
			echo "NO";
			echo "</td>";
			echo "<td>";
			echo ( $infoCTC['Ctcrai'] != 'on' ) ? "X" : "";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='5'>";
			echo "Cuales:";
			echo "</td>";
			echo "<tr>";
			echo "<td colspan='5'>"; 
			echo str_replace( "\n","<br>", $infoCTC['Ctcrad'] );
			echo "</td>";
			echo "</tr>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "<b>Contraindicaciones expresas, sin alternativa POS</b>";
			echo "</td>";
			echo "<td>";
			echo "SI";
			echo "</td>";
			echo "<td>";
			echo ( $infoCTC['Ctccie'] == 'on' ) ? "X" : "";
			echo "</td>";
			echo "<td>";
			echo "NO";
			echo "</td>";
			echo "<td>";
			echo ( $infoCTC['Ctccie'] != 'on' ) ? "X" : "";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='5'>";
			echo "Cuales:";
			echo "</td>";
			echo "<tr>";
			echo "<td colspan='5'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctccid'] ); 
			echo "</td>";
			echo "</tr>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br>";
			echo "<b>MEDICAMENTOS NO POS</b>";
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td>";
			echo "1. Principio activo";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcmn1'];
			echo "</td>";
			echo "<td>";
			echo "Posología";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcpo1'];
			echo "</td>";
			echo "<td>";
			echo "Presentación";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcff1'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "Dosis/Día";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdd5'];
			echo "</td>";
			echo "<td>";
			echo "Cantidad";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctccm5'];
			echo "</td>";
			echo "<td>";
			echo "Tiempos/Meses";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctctm1'];
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br><br>";
			
			echo "<b>INDICACIONES CLARAS DEL TX CON MEDICAMENTO NO POS</b>";
			echo "<br>";
			
			echo "<table class='table'>";
			echo "<tr>";
			echo "<td width='30%'>";
			echo "Efecto deseado al TX";
			echo "</td>";
			echo "<td width='70%'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcet1'] );
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "<br><br>";
			
			echo "<table class='table'>";
			echo "<tr>";
			echo "<td width='30%'>";
			echo "Tiempo de respuesta esperado";
			echo "</td>";
			echo "<td width='70%'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctctr1'] );
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "<br><br>";
			
			echo "<table class='table'>";
			echo "<tr>";
			echo "<td width='30%'>";
			echo "Efectos secundarios y posibles riesgos al TX";
			echo "</td>";
			echo "<td width='70%'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcpe1'] );
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "<br><br>";
			
			echo "Consideraciones y soporte bibliográfico ( Justificación para el uso de medicamento NO POS)";
			
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td colspan='3'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcjm1'] );
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br>";
			echo "<b>MEDICAMENTOS NO POS</b>";
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td>";
			echo "2. Principio activo";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcmn2'];
			echo "</td>";
			echo "<td>";
			echo "Posología";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcpo2'];
			echo "</td>";
			echo "<td>";
			echo "Presentación";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcff2'];
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo "Dosis/Día";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctcdd6'];
			echo "</td>";
			echo "<td>";
			echo "Cantidad";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctccm6'];
			echo "</td>";
			echo "<td>";
			echo "Tiempos/Meses";
			echo "</td>";
			echo "<td>";
			echo $infoCTC['Ctctm2'];
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br><br>";
			
			echo "<b>INDICACIONES CLARAS DEL TX CON MEDICAMENTO NO POS</b>";
			echo "<br>";
			
			echo "<table class='table'>";
			echo "<tr>";
			echo "<td width='30%'>";
			echo "Efecto deseado al TX";
			echo "</td>";
			echo "<td width='70%'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcet2'] ); 
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "<br><br>";
			
			echo "<table class='table'>";
			echo "<tr>";
			echo "<td width='30%'>";
			echo "Tiempo de respuesta esperado";
			echo "</td>";
			echo "<td width='70%'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctctr2'] );
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "<br><br>";
			
			echo "<table class='table'>";
			echo "<tr>";
			echo "<td width='30%'>";
			echo "Efectos secundarios y posibles riesgos al TX";
			echo "</td>";
			echo "<td width='70%'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcpe2'] );
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "<br><br>";
			
			echo "Consideraciones y soporte bibliográfico ( Justificación para el uso de medicamento NO POS)";
			
			echo "<br>";
			
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td colspan='3'>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcjm2'] );
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br>";
			echo "<table class='table'>";
			
			echo "<tr>";
			echo "<td style='width:40%' align='center'>";
			echo "<b>NOMBRE DEL MEDICO TRATANTE</b>";
			echo "</td>";
			echo "<td style='width:30%' align='center'>";
			echo "<b>FIRMA<b>";
			echo "</td>";
			echo "<td style='width:30%'>";
			echo "<b>REGISTRO MEDICO</b>";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td>";
			echo $infoMedico->nombre;
			echo "</td>";
			echo "<td>";
			echo "</td>";
			echo "<td>";
			echo $infoMedico->registro;
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br><br>";
			echo "</div>";
		}
	}
}
?>