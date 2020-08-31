<script>

//function stopEvent() {
//    if (!e) e = window.event;
//    if (e.stopPropagation) {
//        e.stopPropagation();
//        e.preventDefault();
//    } else {
//    	e.returnValue = false;
//    }
//}
//
//window.onload = function(){
//					document.forms[0].onsubmit = function(){ alert( "hola" ); }
////					document.forms[0].submit();
//					var a = document.getElementById( 'met' );
//					alert( a );
//				}
//window.onunload = function(){ alert("Hola onunload"); stopEvent(); }

	function abrirVentana( adicion, citas, solucion ){

		var ancho=screen.width;
		var alto=screen.availHeight;
		var v = window.open( 'admision.php?ok=9&empresa='+solucion+'&idCita='+adicion+'&wemp2='+citas,'','scrollbars=1, width='+ancho+', height='+alto );
		v.moveTo(0,0);
	}

	function asistida( adicion ){

		var auxDiv = document.createElement( "div" );
		auxDiv.innerHTML = "<INPUT type='hidden' name='asistida' value='"+adicion+"'>";

		document.forms[0].appendChild( auxDiv.firstChild );

		document.forms[0].submit();
	}

</script>

<?php
include_once("conex.php");
/**
 * Programa:	pantallaAdmision.php
 * Por:			Edwin Molina Grisales
 * Fecha:		2010-01-13
 * Descripcion:	Este programa muestra una lista de todos los pacientes con cita médica, con la
 * 				posibilidad de filtrar la lista por médico, y permitir hacerles la admision a
 * 				cada paciente con o sin cita.
 */

/**
 * Variables del sistema
 *
 * $slDoctor		Filtro por Doctor. Contiene el nombre del doctor por el que esta filtrado la lista
 * $idCita			Identificador unico de la cita que se le hace la admision
 * $filtro			Codigo del doctor por el que es filtrado el paciente
 */

/********************************************************************************************************
 * 												FUNCIONES
 *******************************************************************************************************/

function marcarAsistida( $id ){

	global $conex;
	global $solucionCitas;

	if( isset($id) ){

		if( !empty($id) ){

			$sql = "UPDATE
						{$solucionCitas}_000009
					SET
						asistida = 'on',
						atendido = 'off'
					WHERE
						id = '$id'";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

			if( mysql_affected_rows() > 0 ){
				return true;
			}
			else{
				return false;
			}

		}

	}
}

//Busca la solucion correspondientes de citas para la empresa dada
function solucionCitas( $codEmp ){

	global $conex;
	global $solucionCitas;

	$solucionCitas = '';

	$sql = "SELECT
				detval
			FROM
				root_000051
			WHERE
				detapl = 'citas'
				AND detemp = '$codEmp'
			";

	$res = mysql_query( $sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$solucionCitas = $rows[0];
	}

	return $solucionCitas;
}


/********************************************************************************************************
 * 											FIN DE FUNCIONES
 *******************************************************************************************************/

/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/

include_once("root/comun.php");

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower( $institucion->baseDeDatos );
$wentidad = $institucion->nombre;

//El usuario se encuentra registrado
if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
}
else{

	encabezado("AGENDA MEDICA", "2010-01-13", "logo_".$wbasedato );
	$solucionCitas = solucionCitas( $wemp_pmla );

	if( isset($asistida) ){
		marcarAsistida( $asistida );
	}

	//Buscando el doctor por el que fue filtrado
	if( !isset( $slDoctor ) ){
		$nmFiltro = "% - Todos";
		$filtro = '%';
		$slDoctor = "% - Todos";
	}
	else{
		$nmFiltro = $slDoctor;
		$exp = explode( " - ", $slDoctor);
		$filtro = $exp[0];
	}

	echo "<form name='pantalla' method=post>";
	echo "<br><br>";

	$sql = "SELECT
				Mednom, Medcod
			FROM
				{$wbasedato}_000051
			WHERE
				Medcid != ''
				AND Medest = 'on'
			ORDER BY Mednom";

	$res1 = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );


	//Filtro por doctor
	echo "<table align=center>";
	echo "	<tr>";
	echo "		<td class='encabezadotabla' align=center>";
	echo "			Filtro por Profesional";
	echo "		</td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='fila1'>";
	echo "			<select name='slDoctor' onchange='javascript: document.forms[0].submit();'>";
	echo "				<option>% - Todos</option>";

	for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ ){
		if( $slDoctor != "{$rows['Medcod']} - {$rows['Mednom']}" ){
			echo "				<option>{$rows['Medcod']} - {$rows['Mednom']}</option>";
		}
		else{
			echo "				<option selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
		}
	}

	echo "			</select>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";

	//Aqui comienza la lista de pacientes

	//Buscando los pacientes que tienen cita
	//y no van para interconsulta
	$sql = "SELECT
				fecha,
				cod_equ,
				TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,
				hf,
				nom_pac,
				mednom,
				b.id,
				IF(cedula IN ((SELECT pacdoc FROM {$wbasedato}_000100 WHERE pacdoc = cedula AND pacact = 'on' )),'on','off') as act
			FROM
				{$wbasedato}_000051 a,
				{$solucionCitas}_000009 b
			WHERE
				medcid = cod_equ
				AND medcod like '$filtro'
				AND fecha = '".date("Y-m-d")."'
				AND atendido != 'on'
				AND asistida != 'on'
				AND nom_pac != 'CANCELADA'
				AND cedula NOT IN (SELECT espdoc FROM {$wbasedato}_000141 WHERE espdoc = cedula AND esphor = TIME_FORMAT( CONCAT(hi,'00'), '%H:%i:%s') AND espmed = medcod )
			ORDER BY hi, mednom, nom_pac
			";
//				AND cedula NOT IN (SELECT pacdoc FROM {$wbasedato}_000100 WHERE pacdoc = cedula AND pacact = 'on' )

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	echo "<br><br>";
	echo "<table align='center'>";

	if( $num > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

			//Definiendo la clase por cada fila
			if( $i%2 == 0 ){
				$class = "class='fila1'";
			}
			else{
				$class = "class='fila2'";
			}

			if( $i == 0 ){
				echo "	<tr class='encabezadotabla'  align=center>";
				echo "		<td style='width:90'>";
				echo "			Fecha";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora";
				echo "		</td>";
				echo "		<td>";
				echo "			Nombre del Paciente";
				echo "		</td>";
				echo "		<td>";
				echo "			Doctor";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Admision";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Asiste";
				echo "		</td>";
				echo "	</tr>";
			}

			echo "	<tr $class>";
			echo "		<td align=center>";
			echo "			{$rows['fecha']}";
			echo "		</td>";
			echo "		<td align=center>";
			echo "			{$rows['hi']}";
			echo "		</td>";
			echo "		<td>";
			echo "			{$rows['nom_pac']}";
			echo "		</td>";
			echo "		<td>";
			echo "			{$rows['mednom']}";
			echo "		</td>";

			if( $rows['act'] != 'on' ){
				echo "		<td align=center>";
				echo "			<input type='radio' name='rdAdmision' onclick=\"javascript: abrirVentana( {$rows['id']}, '$solucionCitas', '$wbasedato' );\">";
				echo "		</td>";
			}
			else{
				echo "		<td align=center>";
				echo "		</td>";
			}

			if( $rows['act'] == 'on' ){
				echo "		<td align=center>";
				echo "			<input type='radio' name='rdAdmision' onclick=\"javascript: asistida( {$rows['id']} );\">";
				echo "		</td>";
			}
			else{
				echo "		<td align=center>";
				echo "		</td>";
			}

			echo "	</tr>";
		}
	}
	else{
		echo "<center>NO HAY CITAS ASIGNADAS PARA HOY</center>";
	}


	echo "</table>";

	echo "<br><br>";
	echo "<center><a href='admision.php?ok=9&empresa=$wbasedato' target='_blank'>Admision sin cita</a></center>";

	echo "<br><br>";
	echo "<center><a href='../../citas/procesos/000001_prx5.php?empresa=$solucionCitas&wemp_pmla={$wemp_pmla}' target='_blank'>Asignar cita</a></center>";

	echo "<meta name='met' id='met' http-equiv='refresh' content='60;url=pantallaAdmision.php?wemp_pmla=$wemp_pmla&slDoctor=$slDoctor'>";

	echo "<br><br><center><input type='button' value='Cerrar' style='width:100' onclick='javascript: cerrarVentana();'>";

	echo "</form>";

}
?>


