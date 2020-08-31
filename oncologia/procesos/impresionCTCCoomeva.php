<style>
<!--
.table{
	border-style:none;
	border-collapse:collapse;
	border-color:black;
	border-width:2px;
	width:100%;
	font-size:8pt;
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
				{$wbasedato}_000012
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
			echo "<center><b>SOLICITUD JUSTIFICACION MEDICAMENTOS NO POS</b></center>";
			echo "<br><br>";
			echo "<table class='table' id='tbCTC'>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight'><b>Caso N&deg;</b></td>";
			echo "<td class='tdTopLeftRight'><b>Acta N&deg;</b></td>";
			echo "<td class='tdTopLeftRight'><b>Ciudad</b></td>";
			echo "<td colspan='3' class='tdTopLeftRight' align='center'><b>Fecha de respuesta Esperada</b></td>";
			echo "<td colspan='3' class='tdTopLeftRight' align='center'><b>Fecha de diligenciamiento</b></td>";
			echo "<td colspan='3' class='tdTopLeftRight' align='center'><b>Tipo</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctccas']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcact']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcciu']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>$freYear</td>";
			echo "<td class='tdTopLeftRight' align='center'>$freMonth</td>";
			echo "<td class='tdTopLeftRight' align='center'>$freDay</td>";
			echo "<td class='tdTopLeftRight' align='center'>$fddYear</td>";
			echo "<td class='tdTopLeftRight' align='center'>$fddMonth</td>";
			echo "<td class='tdTopLeftRight' align='center'>$fddDay</td>";
			echo "<td class='tdTopLeftRight' colspan='3' align='center'>{$infoCTC['Ctctip']}</td>";
//			echo "<td></td>";
//			echo "<td></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight' colspan='3'><b>Nombre del afiliado</b></td>";
			echo "<td class='tdTopLeftRight'><b>Edad</b></td>";
			echo "<td class='tdTopLeftRight' colspan='2' align='center'><b>Sexo</b></td>";
			echo "<td class='tdTopLeftRight' colspan='3'><b>Tipo de Identificación</b></td>";
			echo "<td class='tdTopLeftRight' colspan='3' align='center'><b>Numero de Identificación</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdLeftRight' colspan='3'>{$infoPac['Pacnpa']}</td>";
			echo "<td class='tdLeftRight' align='center'>".calculoEdad( $infoPac['Pacfna'] )."</td>";
			echo "<td class='tdTopLeftRight' colspan='2' align='center'>$sexCTC</td>";
			echo "<td  class='tdLeftRight' colspan='3' align='center'>{$infoPac['Pactid']}</td>";
			echo "<td  class='tdLeftRight' colspan='3'>{$infoPac['Pacnid']}</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight' colspan='6'><b>I.P.S. Asignada</b></td>";
			echo "<td class='tdTopLeftRight' colspan='3'><b>Oficina</b></td>";
			echo "<td class='tdTopLeftRight' colspan='3' align='center'><b>Fecha de diagnóstico</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdLeftRight' colspan='6'>{$infoCTC['Ctcips']}</td>";
			echo "<td class='tdLeftRight' colspan='3'>{$infoCTC['Ctcofi']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>$fdxYear</td>";
			echo "<td class='tdTopLeftRight' align='center'>$fdxMonth</td>";
			echo "<td class='tdTopLeftRight' align='center'>$fdxDay</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight' colspan='7'><b>Diagnóstico</b></td>";
			echo "<td class='tdTopLeftRight' colspan='5'><b>Número historia clínica</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdLeftRight' colspan='7'>{$infoCTC['Ctcdxp']}</td>";
			echo "<td class='tdLeftRight' colspan='5' align='center'>{$infoPac['Pachis']}</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight' colspan='5'><b>I.P.S. que presta el servicio</b></td>";
			echo "<td class='tdTopLeftRight' colspan='4'><b>Médico tratante</b></td>";
			echo "<td class='tdTopLeftRight' colspan='3'><b>Especialidad</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdBottomLeftRight' colspan='5'>{$infoCTC['Ctcisv']}</td>";
			echo "<td class='tdBottomLeftRight' colspan='4' align='center'>{$infoMedico->nombre}</td>";
			echo "<td class='tdBottomLeftRight' colspan='3' align='center'>{$infoMedico->especialidad->descripcion}</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br><br>";
			echo "<b>Descripción del caso clínico</b>";
			echo "<br>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcdcc'] );
			echo "<br><br>";
			echo "<b>Tratamientos con medicamentos POS</b>";
			echo "<br><br>";
			echo "<table class='table' style='border-style:solid'>";
			echo "<tr align='center'>";
			echo "<td class='tdTopLeftRight'><b>Principio Activo</b></td>";
			echo "<td class='tdTopLeftRight'><b>Presentación</b></td>";
			echo "<td class='tdTopLeftRight'><b>Concentración</b></td>";
			echo "<td class='tdTopLeftRight' width='10%'><b>Dosis/Días</b></td>";
			echo "<td class='tdTopLeftRight' width='15%'><b>Días de tratamiento</b></td>";
			echo "<td class='tdTopLeftRight' width='15%'><b>Cantidad por mes</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight'>1 {$infoCTC['Ctcmp1']}</td>";
			echo "<td class='tdTopLeftRight'>{$infoCTC['Ctcpr1']}</td>";
			echo "<td class='tdTopLeftRight'>{$infoCTC['Ctcco1']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctcdd1']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctcdt1']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctccm1']}</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight'>2 {$infoCTC['Ctcmp2']}</td>";
			echo "<td class='tdTopLeftRight'>{$infoCTC['Ctcpr2']}</td>";
			echo "<td class='tdTopLeftRight'>{$infoCTC['Ctcco2']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctcdd2']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctcdt2']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctccm2']}</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight'>3 {$infoCTC['Ctcmp3']}</td>";
			echo "<td class='tdTopLeftRight'>{$infoCTC['Ctcpr3']}</td>";
			echo "<td class='tdTopLeftRight'>{$infoCTC['Ctcco3']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctcdd3']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctcdt3']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctccm3']}</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight'>4 {$infoCTC['Ctcmp4']}</td>";
			echo "<td class='tdTopLeftRight'>{$infoCTC['Ctcpr4']}</td>";
			echo "<td class='tdTopLeftRight'>{$infoCTC['Ctcco4']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctcdd4']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctcdt4']}</td>";
			echo "<td class='tdTopLeftRight' align='center'>{$infoCTC['Ctccm4']}</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br><br>";
			echo "<b>Respuesta clínica y paraclínica alcanzada con medicamentos POS</b>";
			echo "<br>";
			echo str_replace( "\n","<br>", $infoCTC['Ctcrcp'] );
			echo "<br><br>";
			echo "<b>Tratamiento NO POS solicitado</b>";
			echo "<br><br>";
			echo "<table class='table' style='border-style:solid'>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight'><b>Principio Activo</b></td>";
			echo "<td class='tdTopLeftRight'><b>Forma Farmaceútica</b></td>";
			echo "<td class='tdTopLeftRight'><b>Concentración</b></td>";
			echo "<td class='tdTopLeftRight'><b>Dosis/Día</b></td>";
			echo "<td class='tdTopLeftRight'><b>Días/trat</b></td>";
			echo "<td class='tdTopLeftRight'><b>Posología</b></td>";
			echo "<td class='tdTopLeftRight'><b>Cant. por mes</b></td>";
			echo "<td class='tdTopLeftRight'><b>Tiempo/mes</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcmn1']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcff1']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcco5']}</td>";
			echo "<td class='tdLeftRight' align='center'>{$infoCTC['Ctcdd5']}</td>";
			echo "<td class='tdLeftRight' align='center'>{$infoCTC['Ctcdt5']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcpo1']}</td>";
			echo "<td class='tdLeftRight' align='center'>{$infoCTC['Ctccm5']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctctm1']}</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight' colspan='8'><b>Justificación del medicamento solicitado</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='8'>".str_replace( "\n","<br>", $infoCTC['Ctcjm1'] )."</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight' colspan='8'><b>Precauciones, contraindicaciones, efectos secundaros y toxicidad asociada con el empleo o abuso del medicamento o tratamiento NO POS</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='8'>".str_replace( "\n","<br>", $infoCTC['Ctcpe1'] )."</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br><br>";
			echo "<table class='table' style='border-style:solid'>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight'><b>Principio Activo</b></td>";
			echo "<td class='tdTopLeftRight'><b>Forma Farmaceútica</b></td>";
			echo "<td class='tdTopLeftRight'><b>Concentración</b></td>";
			echo "<td class='tdTopLeftRight'><b>Dosis/Día</b></td>";
			echo "<td class='tdTopLeftRight'><b>Días/trat</b></td>";
			echo "<td class='tdTopLeftRight'><b>Posología</b></td>";
			echo "<td class='tdTopLeftRight'><b>Cant. por mes</b></td>";
			echo "<td class='tdTopLeftRight'><b>Tiempo/mes</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcmn2']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcff2']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcco6']}</td>";
			echo "<td class='tdLeftRight' align='center'>{$infoCTC['Ctcdd6']}</td>";
			echo "<td class='tdLeftRight' align='center'>{$infoCTC['Ctcdt6']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctcpo2']}</td>";
			echo "<td class='tdLeftRight' align='center'>{$infoCTC['Ctccm6']}</td>";
			echo "<td class='tdLeftRight'>{$infoCTC['Ctctm2']}</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight' colspan='8'><b>Justificación del medicamento solicitado</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='8'>".str_replace( "\n","<br>", $infoCTC['Ctcjm2'] )."</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdTopLeftRight' colspan='8'><b>Precauciones, contraindicaciones, efectos secundaros y toxicidad asociada con el empleo o abuso del medicamento o tratamiento NO POS</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='8'>".str_replace( "\n","<br>", $infoCTC['Ctcpe2'] )."</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br><br>";
			echo "</div>";
		}
	}
}
?>