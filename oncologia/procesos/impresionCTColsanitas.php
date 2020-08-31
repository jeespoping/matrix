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
/**
 * Calcula la edad de la persona de acuerdo a la fecha de nacimiento
 * 
 * @param date $fnac		Fecha de nacimientos				
 * @return entero			Edad de la persona
 */
//function calculoEdad( $fnac ){
//	
//	$edad = 0;
//	
//	$nac = explode( "-", $fnac );				//fecha de nacimiento
//	$fact = date( "Y-m-d" );					//fecha actual
//
//	if( count($nac) == 3 ){
//		$edad = date("Y") - $nac[0];
//		
//		if( date("Y-m-d") < date( "Y-".$nac[1]."-".$nac[2] ) ){
//			$edad--;
//		}
//	}
//		
//	return $edad;
//}

function consultarCTC( $his, $ing, $fecha, &$infoCTC ){
	
	global $conex;
	global $wbasedato;
	
	$infoCTC = '';
		
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000032
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

			$exp = explode( '-', $infoCTC['Ctcffu'] );
			$ffuYear = $exp[0];
			$ffuMonth = $exp[1];
			$ffuDay = $exp[2];
			
			$exp = explode( '-', $infoCTC['Ctcfdd'] );
			$fddYear = $exp[0];
			$fddMonth = $exp[1];
			$fddDay = $exp[2];
			
			$exp = explode( '-', $infoCTC['Ctcdti'] );
			$dtiYear = $exp[0];
			$dtiMonth = $exp[1];
			$dtiDay = $exp[2];
			
			$exp = explode( '-', $infoCTC['Ctcdtf'] );
			$dtfYear = $exp[0];
			$dtfMonth = $exp[1];
			$dtfDay = $exp[2];
			
			echo "<div id='dvCTC'>";
			
			echo "<table style='width:100%' class='table' id='tbCTC'>";

			echo "<tr style='display:none'>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='6'></td>";
			echo "<td colspan='10'>SOLICITUD DE MEDICAMENTOS NO INCLUIDOS EN EL POS</td>";
//			echo "<td colspan='16'><br><br></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='3' rowspan='2'>Fecha de entrega del<br>formato al usuario</td>";
			echo "<td colspan='13' align='center'>Informacion de la solicitud</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='10'>N�mero de Solicitd Afirma</td>";
			echo "<td colspan='3'>Fecha</td>";
			echo "</tr>";
			

			
			//INFORMACION DE LA SOLICITUD
			echo "<tr>";
			
			echo "<td align=center>";
			echo "&nbsp;";
			echo $ffuDay;
			echo "</td>";
			echo "<td align=center>";
			echo $ffuMonth;
			echo "</td>";
			echo "<td align=center>";
			echo $ffuYear;
			echo "</td>";
			
			
			echo "<td colspan='10'>";
			echo $infoCTC['Ctccas'];
			echo "</td>";
			
			echo "<td align='center'>";
			echo $fddDay;
			echo "</td>";
			echo "<td align='center'>";
			echo $fddMonth;
			echo "</td>";
			echo "<td align='center'>";
			echo $fddYear;
			echo "</td>";
			
			
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td>";
			echo "DD";
			echo "</td>";
			echo "<td>";
			echo "MM";
			echo "</td>";
			echo "<td>";
			echo "AAA";
			echo "</td>";
			echo "</td>";
			
			echo "<td colspan='10' align='center'>";
			echo "N�mero";
			echo "</td>";
			
			echo "<td>";
			echo "DD";
			echo "</td>";
			echo "<td>";
			echo "MM";
			echo "</td>";
			echo "<td>";
			echo "AAA";
			echo "</td>";
			echo "</td>";
			
			echo "</tr>";
			
			echo "</table><br>";
			
			echo "<table class='table'>";
			
			
//			echo "<tr>";
//			echo "<td colspan='16'><br></td>";
//			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='16'>Informaci�n del usuario</td>";
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td colspan='8'>";
			echo "Nombre";
			echo "</td>";
			
			echo "<td colspan='5'>";
			echo "Documento de Identificacion";
			echo "</td>";
			
			echo "<td>";
			echo "Edad";
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "Sexo";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='8'>";
			echo "&nbsp;";
			echo $infoPac['Pacnpa'];
			echo "</td>";
			
			echo "<td>";
			echo "&nbsp;";
			echo substr( $infoPac['Pactid'], 0, strpos( $infoPac['Pactid'], "-" ) );
			echo "</td>";
			
			echo "<td colspan='4'>";
			echo "&nbsp;";
			echo number_format( $infoPac['Pacnid'], 0, "", "." );
			echo "</td>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo calculoEdad( $infoPac['Pacfna'] );
			echo "</td>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo ( substr( $infoPac['Pacsex'], 0, strpos( $infoPac['Pacsex'], "-" ) ) == "02" ) ? "X": "";
			echo "</td>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo ( substr( $infoPac['Pacsex'], 0, strpos( $infoPac['Pacsex'], "-" ) ) == "01" ) ? "X": "";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			
			echo "<tr align='center'>";
			
			echo "<td colspan='8' align='center'>";
			echo "Nombres y apellidos";
			echo "</td>";
			
			echo "<td>";
			echo "Tipo";
			echo "</td>";
			
			echo "<td colspan='4'>";
			echo "N�mero";
			echo "</td>";
			
			echo "<td>";
			echo "A�os";
			echo "</td>";
			
			echo "<td>";
			echo "F";
			echo "</td>";
			
			echo "<td>";
			echo "M";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='5'>";
			echo "Direcci�n";
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "Telefono";
			echo "</td>";
			
			echo "<td colspan='4'>";
			echo "Ciudad";
			echo "</td>";
			
			
			echo "<td colspan='4'>";
			echo "Numero de contrato";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='5'>";
			echo "&nbsp;";
			echo $infoPac['Pacdir'];
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "&nbsp;";
			echo $infoPac['Pactel'];
			echo "</td>";
			
			echo "<td colspan='4'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcciu'];
			echo "</td>";
			
			
			echo "<td colspan='4'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcact'];
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "</table>";
			
			
			
			echo "<table class='table'>";
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "Informaci�n de los medicamentos";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "Medicamentos solicitados no incluidos en el POS";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='10'>";
			echo "Principio Activo";
			echo "</td>";
			
			echo "<td colspan='6'>";
			echo "Grupo Terape�tico";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='10'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcmn1'];
			echo "</td>";
			
			echo "<td colspan='6'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcisv'];
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='3' align='center'>";
			echo "Concentraci�n";
			echo "</td>";
			
			echo "<td colspan='3' align='center'>";
			echo "Forma Farmace�tica";
			echo "</td>";
			
			echo "<td colspan='4' align='center'>";
			echo "D�as/Tratamiento";
			echo "</td>";
			
			echo "<td colspan='6' align='center'>";
			echo "Dosis";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			
			echo "<tr align='center'>";
			
			echo "<td colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcco5'];
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcff2'];
			echo "</td>";
			
			echo "<td colspan='4'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcdt5'];
			echo "</td>";
			
			echo "<td colspan='6'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcpo5'];
			echo "</td>";
			
			echo "</tr>";
			
			echo "</table><br>";
			
			
			
			
			echo "<table class='table'>";
			
//			echo "<tr>";
//			
//			echo "<td colspan='16'>";
//			echo "&nbsp;";
//			echo "</td>";
//			
//			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='16' align='center'>";
			echo "Campos de diligenciamiento exclusivo del medico tratante";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "Apreciado doctor: En su calidad de medico tratante del usuario anteriormente identificado, es necesario que diligencie completamente los siguientes campos de informaci�n del formato  con el prop�sito de brindar la mayor cantidad de informaci�n posible al Comit� T�cnico Cient�fico.";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "Informaci�n del m�dico tratante";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='8'>";
			echo "Nombre";
			echo "</td>";
			
			echo "<td colspan='5'>";
			echo "Documento de Identificacion";
			echo "</td>";
			
			
			echo "<td colspan='3'>";
			echo "Registro M�dico";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='8'>";
			echo "&nbsp;";
			echo $infoMedico->nombre;
			echo "</td>";
			
			echo "<td>";
			echo "&nbsp;";
			echo "CC";
			echo "</td>";
			
			echo "<td colspan='4'>";
			echo "&nbsp;";
			echo number_format( '70090293', 0, "", "." );
			echo "</td>";
			
			echo "<td colspan='3' align='center'>";
			echo "&nbsp;";
			echo $infoMedico->registro;
			echo "</td>";
			
			
			echo "</tr>";
			
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='8' align='center'>";
			echo "Nombres y apellidos";
			echo "</td>";
			
			echo "<td align='center'>";
			echo "Tipo";
			echo "</td>";
			
			echo "<td colspan='4' align='center'>";
			echo "N�mero";
			echo "</td>";
			
			echo "<td colspan='3' align='center'>";
			echo "N�mero";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='5'>";
			echo "Especialidad";
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "Direcci�n";
			echo "</td>";
			
			echo "<td colspan='4'>";
			echo "T�lefono";
			echo "</td>";
			
			
			echo "<td colspan='4'>";
			echo "Ciudad";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='5'>";
			echo "&nbsp;";
			echo $infoMedico->especialidad->descripcion;
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "&nbsp;";
			echo $infoMedico->consultorio->direccion;
			echo "</td>";
			
			echo "<td colspan='4'>";
			echo "&nbsp;";
			echo $infoMedico->consultorio->telefono;
			echo "</td>";
			
			
			echo "<td colspan='4'>";
			echo "&nbsp;";
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "</table>";
			
			
			
			echo "<table class='table'>";
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "Medicamentos incluidos en el POS del mismo grupo terap�utico";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr align='center'>";
			
			echo "<td colspan='6'>";
			echo "Principio Activo";
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "Concentraci�n";
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "Forma Farmaceutica";
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "D�as/Tratamiento";
			echo "</td>";
			
			echo "<td align='center' colspan='3'>";
			echo "Dosis";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='6'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcmp1'];
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcco1'];
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcff1'];
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcdt1'];
			echo "</td>";
			
			echo "<td align='center' colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctccm1'];
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td colspan='6'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcmp2'];
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcco2'];
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcff2'];
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcdt2'];
			echo "</td>";
			
			echo "<td align='center' colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctccm2'];
			echo "</td>";
			
			echo "</tr>";
			
			echo "<tr>";
			
			echo "<td colspan='6'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcmp3'];
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcco3'];
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcff3'];
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcdt3'];
			echo "</td>";
			
			echo "<td align='center' colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctccm3'];
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td colspan='6'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcmp4'];
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcco4'];
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcff4'];
			echo "</td>";
			
			echo "<td colspan='2'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcdt4'];
			echo "</td>";
			
			echo "<td align='center' colspan='3'>";
			echo "&nbsp;";
			echo $infoCTC['Ctccm4'];
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "</table>";
			
			
			
			echo "<table class='table'>";
			
			echo "<tr>";
			
			echo "<td rowspan='4' colspan='7'>";
			echo "Diagn�stico, evoluci�n, clasificaci�n y estado de la patolog�a  (Realice una descripci�n del estado actual y / o evoluci�n de la enfermedad)";
			echo "</td>";
			
			echo "<td colspan='9'>";
			echo "Duraci�n del tratamiento";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td colspan='3'>";
			echo "Desde";
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "Hasta";
			echo "</td>";
			
			echo "<td colspan='3'>";
			echo "Tiempo estimado";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo $dtiDay;
			echo "</td>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo $dtiMonth;
			echo "</td>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo $dtiYear;
			echo "</td>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo $dtfDay;
			echo "</td>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo $dtfMonth;
			echo "</td>";
			
			echo "<td align='center'>";
			echo "&nbsp;";
			echo $dtfYear;
			echo "</td>";
			
			echo "<td colspan='3' align='center'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcfee'];
			echo "</td>";
			
			echo "</tr>";
			
			
			
			
			echo "<tr>";
			
			echo "<td align='center'>";
			echo "DD";
			echo "</td>";
			
			echo "<td align='center'>";
			echo "MM";
			echo "</td>";
			
			echo "<td align='center'>";
			echo "AAAA";
			echo "</td>";
			
			echo "<td align='center'>";
			echo "DD";
			echo "</td>";
			
			echo "<td align='center'>";
			echo "MM";
			echo "</td>";
			
			echo "<td align='center'>";
			echo "AAAA";
			echo "</td>";
			
			echo "<td colspan='3' align='center'>";
			echo "D�as";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='2'>";
			echo "Diagn�stico:";
			echo "</td>";
			
			echo "<td colspan='14'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcdxp'];
			echo "</td>";
			
			echo "</tr>";
			
			echo "</table>";
			
			
			
			echo "<table class='table'>";
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "Resumen de la historia cl�nica";
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcdcc'];
			echo "</td>";
			
			echo "</tr>";
			
			echo "</table><br>";
			
			
			
			echo "<table class='table'>";
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "Justificaci�n del medicamento";
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "&nbsp;";
			echo $infoCTC['Ctcjm1'];
			echo "</td>";
			
			echo "</tr>";
			
			echo "</table><br>";
			
			
			
			echo "<table class='table'>";
			
//			echo "<tr>";
//			
//			echo "<td colspan='16'>";
//			echo "&nbsp;";
//			echo "</td>";
//			
//			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "<b>Apreciado usuario, esta solicitud debe ser radicada en la EPS junto con los siguientes documentos soporte:</b>";
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "<b>1</b>. Fotocopia de la formula medica completamente diligenciada, con firma y sello del medico tratante, legible.";
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "<b>2</b>. Fotocopia de la Historia Cl�nica completa y actualizada.";
			echo "</td>";
			
			echo "</tr>";
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "EL COMIT� T�CNICO CIENT�FICO NO PODR� ADELANTAR EL ESTUDIO DEL CASO SIN EL SUMINISTRO COMPLETO DE LA INFORMACI�N Y DOCUMENTACI�N ANTERIORMENTE INDICADA.";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "<tr>";
			
			echo "<td colspan='16'>";
			echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "Firma y sello del m�dico remitente<br>";
			echo "Documento de identificaci�n<br>";
			echo $infoMedico->nroIdentificacion;
			echo "<br>";
			echo "</td>";
			
			echo "</tr>";
			
			
			
			echo "</table>";
			
			echo "</div>";
		}
	}
}
?>