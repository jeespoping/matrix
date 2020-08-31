<?php
include_once("conex.php");
/*************************************************************************************************************
 * Modificaciones:
 * 
 * Mayo 05 de 2016			Se traen los datos de acompañante y persona responsable del último ingreso del paciente al momento de admitir al paciente.
 * Abril 24 de 2014			Se agrega campo de EPS y se deja en causa externa "evento catastrófico" por defecto
 ************************************************************************************************************/
 
/*************************************************************************************************************
 * 												FUNCIONES
 ************************************************************************************************************/

/**
 * * Busca las bases necesarias para cada medico
 * 
 * @param $med	codigo del medico
 * @return unknown_type
 */
function buscarBases( $med ){
	
	global $conex;
	global $wbasedato;
	global $wbasecitas;
	
	$sql = "SELECT
				*
			FROM
				root_000055
			WHERE
				medusu = '$med'
				AND medest
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$wbasedato = $rows['Medbhc'];
		$wbasecitas = $rows['Medbci'];	
	}
	
}

/**
 * Busca un filtro por profesional
 * 
 * @return unknown_type
 */
function filtroMedicos(){
	
	global $conex;
	global $key;
	
	if( !isset($doc) ){
		
		$sql = "SELECT
					*
				FROM
					root_000055
				WHERE
					medest = 'on'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 1 ){
			
			echo "<table align='center' id='tbFiltroProfesional'>";
			echo "<tr class='encabezadotabla'>";
			echo "<td align='center' style='width:400'>FILTRO POR PROFESIONAL</td>";
			echo "</tr>";
			echo "<tr class='fila1'>";
			echo "<td>";
			
			echo "<SELECT name='slFiltroProfesional' name='slFiltroProfesional' style='width:100%' onChange='javascript: cambiarMedico( this )'>";
			echo "<option></option>";
			for( $i = 0; $rows = mysql_fetch_array( $res ) ; $i++ ){
				
				if( $i == 0 ){
					$doc = $rows['Medusu']; 
				}
				
				$exp = explode( ",", $rows['Medsec'] );
				
				for( $j = 0; $j < count( $exp ); $j++){
					if( $exp[ $j ] == $key ){
						echo "<option value='{$rows['Medusu']}'>{$rows['Mednom']}</option>";
					}
				}
			}
			
			echo "</select>";
			
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			
//			echo "<br><br>";
//			
//			echo "<center><b>AGENDAS MEDICAS</b></center><br>";
//			
//			for( $i = 0; $rows=mysql_fetch_array( $res ); $i++ ){
//				
//				echo "<center>";
//				echo "<br><br>";
//				echo "<a href='agendaMedicaConsultorio.php?doc={$rows['Medusu']}'>";
//				echo "Agenda Médicas del Dr. ".strtoupper( $rows['Mednom'] );
//				echo "</a>";
//				echo "</center>";
//				
//			}
		}
		elseif( $numrows == 0 ){
			echo "<center><b>NO TIENE MEDICOS ASIGNADOS</b></center>";
		}
		else{
			$rows = mysql_fetch_array( $res );
			$doc = $rows['Medusu'];
		}
	}
}

/**
 * Busca los datos del ingreso anterior, para darlos como sugerencias al momento de realizar un ingreso
 * 
 * @param $his			Historia
 * @return unknown_type
 */
function ingresoAnterior( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$val = "**";
		
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
		$val = "{$rows['Ingmre']}*{$rows['Ingdia']}*{$rows['Ingcex']}*{$rows['Ingnre']}*{$rows['Ingtre']}*{$rows['Ingpar']}*{$rows['Ingaco']}*{$rows['Ingtac']}";
	}
//	return $val;
	return utf8_encode( $val );
}


/**
 * Si el paciente no tiene la primera historia no se le puede hacer un nuevo ingreso por Consulta
 * 
 * @param $his			Historia
 * @return unknown_type
 */
function tienePrimeraHistoria( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	if( $ing > 0 ){
		
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000001
				WHERE
					hclhis like '$his'
					AND hcling like '$ing'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
			return true;
		}
		else{
//			echo "false";
			return false;
		}
	}
	
}

/************************************************************************************
 * Inserta un registro en la tabla de sala de Espera
 * 
 * @param $his		Historia
 * @param $ing		Ingreso
 * @param $doc		Nro de documento
 * @param $npa		Nombre del paciente
 * @param $hor		Hora de la cita
 * @return unknown_type
 ************************************************************************************/
function insertarRegistroSalaEspera( $his, $ing, $doc, $npa, $hor, $tin, $med ){
	
	global $conex;
	global $wbasedato;
	global $wbasecitas;
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO {$wbasedato}_000017
					(     medico    , fecha_data, hora_data,     seguridad   , esphis, esping, espdoc, espnpa, espfec  , esphor , espesp, espate, espfin  ,  esphin   , espest,   esphsa  , espmed, esptin, espmat  )
			VALUES	( '{$wbasedato}', '$fecha'  , '$hora'  , 'C-{$wbasedato}', '$his', '$ing', '$doc', '$npa', '$fecha', '$hor' ,  'on' ,  'off', '$fecha', '00:00:00',  'on' , '00:00:00', '$med', '$tin',   ''  )";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
	
}

/**
 * Busca los datos necesarios del paciente para actualizar la sala de Espera
 * 
 * @param $his		Historia
 * @param $ing		Ingreso
 * @param $doc		Nro de documento
 * @param $npa		Nombre del paciente
 * @param $hor		Hora de la cita
 * @param $med		Código del médico
 * @return unknown_type
 */
function actualizarSalaEspera( $his, $ing, $idc, $tin, $med ){
	
	global $conex;
	global $wbasedato;
	global $wbasecitas;
	
	$sql = "SELECT
				pacnpa, pacnid
			FROM
				{$wbasedato}_000002
			WHERE
				pachis = '$his'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		if( $rows = mysql_fetch_array( $res ) ){
			
			$doc = $rows['pacnid'];
			$npa = $rows['pacnpa'];
			
			$sql = "SELECT
						TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi 
					FROM
						{$wbasecitas}_000009
					WHERE
						id = '$idc'
					";
			
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			if( $rows = mysql_fetch_array( $res ) ){
			
				insertarRegistroSalaEspera( $his, $ing, $doc, $npa, $rows['hi'], $tin, $med );
				return true;
			}
		}
	}
	else{
		return false;
	}
	
}


/*****************************************************
 * Muestra la tabla de pacientes atendidos
 * 
 * @return unknown_type
 ****************************************************/
function pacientesAtendidos( $key ){
	
	global $conex;
	global $wbasedato;
	global $doc;
	
	$infoMedico = new classMedico( $key );
	
	$ing = 0;
	
	//Buscando pacientes a los que se le ha hecho ingreso
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000017 a, 
				{$wbasedato}_000002
			WHERE
				espate = 'on'
				AND espest = 'on'
				AND esphis = pachis
				AND espfec = '".date("Y-m-d")."'
			";
				
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000017 a, 
				{$wbasedato}_000002
			WHERE
				espest = 'on'
				AND esphis = pachis
				AND espfec = '".date("Y-m-d")."'
				AND espmed = '$doc'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		echo "<center><b>PACIENTES ADMITIDOS</b></center>";
		echo "<br><br>";
		
		echo "<table id='tbAtendidos' align='center'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td style='width:120;text-align:center'>Historia</td>";
		echo "<td style='width:100;text-align:center'>Ingreso</td>";
		echo "<td style='width:300;text-align:center'>Nombre del Paciente</td>";
		echo "<td style='width:150;text-align:center'>Nro de Documento</td>";
		echo "<td style='width:150;text-align:center'>Datos<br>Demogr&aacute;ficos</td>";
		echo "<td style='width:100;text-align:center'>Impresi&oacute;n</td>";
		echo "</tr>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$fila = "class='fila".($i%2+1)."'";
//			$url = "impresion.php?his={$rows['Esphis']}&ing={$rows['Esping']}&doc=".$doc;
			$url = "../../{$infoMedico->grupoSolucion}/procesos/impresion.php?txHis={$rows['Esphis']}&doc=".$doc."&sala";
			
			$urlActualizar = "../../det_registro.php?id={$rows['id']}&pos1={$wbasedato}&pos2=2010-03-16&pos3=16:46:49&pos4=000002&pos5=0&pos6={$wbasedato}&tipo=P&Valor=&Form=000002-{$wbasedato}-C-Informacion%20del%20Paciente&call=0&change=0&key=$key&Pagina=1";
			$urlActualizar .= "&nid={$rows['Pacnid']}";
			
			echo "<tr $fila>";
			echo "<td style='text-align:center'>{$rows['Esphis']}</td>";
			echo "<td style='text-align:center'>{$rows['Esping']}</td>";
			echo "<td>{$rows['Pacnpa']}</td>";
			echo "<td style='text-align:center'>{$rows['Pacnid']}</td>";
			echo "<td align='center'><a target='_blank' href='$urlActualizar'>Actualizar</a></td>";
			echo "<td align='center'><a target='_blank' href='$url'>Ir a Impresion</a></td>";
			echo "</tr>";
			
		}
		
		echo "</table>";
		
	}
	else{
		echo "<center><b>NO HAY PACIENTES ATENDIDOS</b></center>";
	}
	
}

/**
 * Consulta el ultimo ingreso para un paciente
 * 
 * @param $his
 * @return unknown_type
 */
function consultarUltimoIngreso( $his ){
	
	global $conex;
	global $wbasedato;
	
	$ing = 0;
	
	$sql = "SELECT
				MAX(inging+0) as inging
			FROM
				{$wbasedato}_000003
			WHERE
				inghis = '$his'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$ing = @$rows['inging'];
	}
	
	return $ing;
}

/******************************************************************************************
 * Crea la lista de opciones para elegir la empresa a la cual esta afiliado el paciente
 * 
 * @param $codMed		Codigo del Medico
 * @return unknown_type
 * @tipo				Indica si es EPS o Responsable
 *******************************************************************************************/
function maestroEmpresas( $codMed, $name, $tipo ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				Medemp, Medbhc 
			FROM
				root_000055
			WHERE
				medest = 'on'
				AND medusu = '$codMed'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_errno() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$exp = explode( "-", $rows['Medemp'] );
		$basedato = $exp[0];
		$tabla = $exp[1];
	}
	

	if( !empty($tabla) || !empty($basedato) ){
		
		if( $basedato == $rows['Medbhc'] ){
			
			$sql = "SELECT
						empnit, empnom
					FROM
						{$basedato}_{$tabla}
					WHERE
						empest = 'on'
						AND emp".substr( $tipo, 0, 3 )." = 'on'
					";
		}
		else{
					
			$sql = "SELECT
						Nit as empnit, descripcion as empnom
					FROM
						{$basedato}_{$tabla}
					WHERE
						activo = 'A'
						AND $tipo = 'on'
				   ";
		}
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		$val = "<select name='$name'>";
		$val .= "<option></option>";
	
		for( $i = 0; $rows = mysql_fetch_array( $res ) ; $i++ ){
			$val .= "<option>{$rows['empnit']}-{$rows['empnom']}</option>";
		}
		
		$val .= "</select>";
		
		return $val;
	}
}

/**
 * Crea la opcion para hacer el ingreso a un paciente
 * 
 * @param $codMed			Codigo del Medico
 * @return unknown_type
 */
function admisionPacientes( $codMed ){
	
	global $conex;
	global $wbasedato;
	
	echo "<br><br>";
	echo "<table id='tbAdmision' align='center' style='display:none'>";
	echo "<tr>";
	echo "<td class='fila1'>Tipo de Ingreso</td>";
	echo "<td class='fila2'>".tiposIngresos()."</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1'>Acompa&ntilde;ante</td>";
	echo "<td class='fila2'><INPUT type='text' name='txAco' style='width:100%'></td>";
	echo "</tr>";
	
	
	echo "<tr>";
	echo "<td class='fila1'>Telefono acompañante</td>";
	echo "<td class='fila2'><INPUT type='text' name='txTac' style='width:100%'></td>";
	echo "</tr>";
	
	
	echo "<tr>";
	echo "<td class='fila1'>M&eacute;dico Remitente</td>";
	echo "<td class='fila2'><INPUT type='text' name='txMre' style='width:100%'></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'>Diagn&oacute;stico</td>";
	echo "<td class='fila2'><INPUT type='text' name='txDia' style='width:100%'></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'>Causa externa</td>";
	echo "<td class='fila2'><INPUT type='text' name='txCex' style='width:100%'></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'>Empresa</td>";
	echo "<td class='fila2'>".maestroEmpresas( $codMed, 'slEmpresa', 'responsable' )."</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'>EPS</td>";
	echo "<td class='fila2'>".maestroEmpresas( $codMed, 'slEps', 'Eps' )."</td>";
	echo "</tr>";
	
	
	echo "<tr>";
	echo "<td class='fila1'>Persona Responsable del paciente</td>";
	echo "<td class='fila2'><INPUT type='text' name='txNre' style='width:100%'></td>";
	echo "</tr>";
	
	
	echo "<tr>";
	echo "<td class='fila1'>Telefono responsable</td>";
	echo "<td class='fila2'><INPUT type='text' name='txTre' style='width:100%'></td>";
	echo "</tr>";
	
	
	echo "<tr>";
	echo "<td class='fila1'>Parentesco</td>";
	echo "<td class='fila2'><INPUT type='text' name='txPar' style='width:100%'></td>";
	echo "</tr>";
	
	
	
	echo "<tr>";
	echo "<td class='fila1'>Tipo de vinculaci&oacute;n</td>";
	echo "<td class='fila2'><INPUT type='text' name='txTvi' style='width:100%'></td>";
	echo "</tr>";
	
	
	
	echo "<tr align='center'>";
	echo "<td colspan='2'><br><INPUT type='checkbox' name='cbEnviar'> Confirmar admisi&oacute;n</td>";
	echo "</tr>";
	echo "<tr align='center'>";
	echo "<td colspan='2'><br><INPUT type='button' name='btEnviar' value='Grabar admisi&oacute;n' style='width:150' onclick='javascript: registrarIngreso()'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2' align='center'><INPUT type='button' value='Retornar' onClick='javascript: regresarAdmision(this);'></td>";
	echo "</tr>";
	echo "</table>";
	
}

/**
 * Registra el ingreso para un paciente
 * 
 * @param $his		Historia
 * @param $ing		Ingreso
 * @param $tin		Tipo de Ingreso
 * @param $emp		Empresa
 * @param $doc		Nro de Documento
 * @param $idc		Id de consulta
 * @param $aco		Acompñante
 * @param $mre		Medico remitente
 * @param $cex		Causa externa
 * @param $dia		diagnostico
 * @return unknown_type
 */
function registrarIngreso( $his, $ing, $tin, $emp, $doc, $idc, $aco, $mre, $cex, $dia, $eps, $nre, $tre, $par, $tvi, $tac ){
	
	global $conex;
	global $wbasedato;
	global $wbasecitas;
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	
	$sql = "INSERT INTO {$wbasedato}_000003
					(      medico   , fecha_data, hora_data, inghis, inging,  ingfin , inghin , ingtin, ingemp, ingaco, ingmre, ingcex, ingdia, ingeps, Ingnre, Ingtre, Ingpar, Ingtvi, Ingtac, seguridad     )
			VALUES 	( '{$wbasedato}',  '$fecha'  , '$hora' , '$his', '$ing', '$fecha', '$hora', '$tin', '$emp', '$aco', '$mre', '$cex', '$dia', '$eps', '$nre', '$tre', '$par', '$tvi', '$tac','C-{$wbasedato}' )";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		
		$sql = "UPDATE
					{$wbasecitas}_000009
				SET
					atendido = 'on',
					asistida = 'on'
				WHERE
					fecha = '$fecha'
					AND cedula = '$doc'
					AND id = '$idc'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		return true;
	}
	else{
		return false;	
	}
}

/**
 * Crea las opciones del select para dar el ingreso a un paciente
 * 
 * @return unknown_type
 */
function tiposIngresos(){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	$sql = "SELECT
				subcodigo, descripcion
			FROM
				det_selecciones
			WHERE
				medico = '{$wbasedato}'
				AND codigo = '004'
				AND activo = 'A'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$val = "<select name='slTipoIngreso' id='idOpciones'>";
//		$val .= "<option></option>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$val .= "<option>{$rows['subcodigo']}-{$rows['descripcion']}</option>";
		}
		
		$val .= "</select>";
	}
	
	return $val;
}

/**
 * Busca la inforamción del paciente si ya esta ingresado
 * 
 * @param $nrodoc
 * @param $infopac
 * @return unknown_type
 */
function informacionPaciente( $nrodoc, &$infopac ){

	global $conex;
	global $wbasedato;
	
	$infopac = array();
	$infopac['Inging'] = '';
	$infopac['Ingemp'] = '';
	$infopac['Ingeps'] = '';
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000002
			WHERE
				pacnid = '$nrodoc'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		 $rows =  mysql_fetch_array( $res );
		 $infopac = $rows;
		 
		 $infopac['Inging'] = consultarUltimoIngreso( $rows['Pachis'] );
		 $infopac['Ingemp'] = '';
		 $infopac['Ingeps'] = '';
		 
		 $sql = "SELECT
						*
					FROM
						{$wbasedato}_000003
					WHERE
						inghis = '{$infopac['Pachis']}'
						AND inging = '{$infopac['Inging']}' 
					";
						
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$infopac['Ingemp'] = $rows['Ingemp'];
			$infopac['Ingeps'] = $rows['Ingeps'];
		}
		else{
			$infopac['Ingemp'] = '';
			$infopac['Ingeps'] = '';
		}
		 
		 return true;
	}
	else{
		return false;
	}
}

/**
 * Muestra la tabla de pacientes que tienen cita para el dia actual y no han sido atendidos
 * @param $doctor
 * @return unknown_type
 */
function generarTablaPacientes( $doctor, $key ){
	
	global $conex;
	global $wbasedato;
	global $wbasecitas;
	global $infoPacientes;
	
	$sql = "SELECT
				Medcci
			FROM
				root_000055
			WHERE
				medusu = '$key'
			";
	
	$res2 = mysql_query( $sql, $conex ) or die( mysql_errno()."- Error $sql ".mysql_error() );
	
	if( $rows2 = mysql_fetch_array( $res2 ) ){
		$cci = $rows2[0];
	}
	else{
		$cci = '';
	}
	
	$select = tiposIngresos();
	
	$objetojs = "info={";	//variable que contiene el objeto info
	
	$sql = "SELECT
				Fecha, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as Hi, Cedula, Nom_pac, Edad, Telefono, a.id, nit_res, descripcion
			FROM
				{$wbasecitas}_000009 a, {$wbasecitas}_000002 b
			WHERE
				fecha = '".date("Y-m-d")."'
				AND atendido != 'on'
				AND nit_res = nit
				AND b.activo = 'A'
				AND cod_equ = '$cci'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		echo "<table id='tbAgenda' align='center'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td style='width:120;text-align:center'>Fecha</td>";
		echo "<td style='width:100;text-align:center'>Hora</td>";
		echo "<td style='width:300;text-align:center'>Nombre del Paciente</td>";
		echo "<td style='width:150;text-align:center'>Nro de Documento</td>";
		echo "<td style='width:150;text-align:center'>Datos<br>Demogr&aacute;ficos</td>";
		echo "<td style='width:100;text-align:center'>Admitir</td>";
		echo "</tr>";		
		
		for( $i = 0; $rows = mysql_fetch_array( $res );$i++){
			
			$fila = "class='fila".($i%2+1)."'";
			
			echo "<tr $fila>";
			echo "<td style='text-align:center'>{$rows['Fecha']}</td>";
			echo "<td style='text-align:center'>{$rows['Hi']}</td>";
			
			$infopac = '';
			$conInformacion = informacionPaciente( $rows['Cedula'], $infopac );
			
			if( $infopac[ 'Ingemp' ] == '' ){
				$infopac[ 'Ingemp' ] = $rows[ 'nit_res' ]."-".$rows[ 'descripcion' ];
			}
			
			$url = "../../det_registro.php?id=0&pos1={$wbasedato}&pos2=0&pos3=0&pos4=000002&pos5=0&pos6={$wbasedato}&tipo=P&Valor=&Form=000002-{$wbasedato}-C-Informacion%20del%20Paciente&call=0&change=0&key=$key&Pagina=0";
			$url .= "&npa={$rows['Nom_pac']}&nid={$rows['Cedula']}&edad={$rows['Edad']}&tel={$rows['Telefono']}&cita={$rows['id']}&slBdCitas=$wbasecitas";
			
			if( $conInformacion ){
				echo "<td>{$infopac['Pacnpa']}</td>";
//				$copySelect = "<select name='nmOpciones[$i]' id='idOpciones[$i]' onChange='javascript: habilitarChecbox(this);'>".$select;
				$copySelect = $select;
				$copySelect = "<INPUT type='checkbox' name='cbConfirmacion[$i]' onclick='javascript: habilitarAdmision(this);'>";
				
				echo "<script>agregarPacientes( $i, '{$infopac['Pachis']}', '{$infopac['Inging']}', '{$infopac['Pacnid']}', '{$infopac['Ingemp']}', '{$rows['id']}', '{$infopac['Ingeps']}' )</script>";
				
				$infopac['Inging'] = ($infopac['Inging'] == '' )? 0 : $infopac['Inging'];
				
				$objetojs .= "$i : { cit  : '{$rows['id']}',
									 doc : '{$infopac['Pacnid']}',
									 emp : '{$infopac['Ingemp']}',
									 eps : '{$infopac['Ingeps']}',
									 his : '{$infopac['Pachis']}', 
				                     ing : '{$infopac['Inging']}'
			                       },";
				
				$infoPacientes[$i] = $infopac;
				
				$nuevoPaciente = '';
				
				$urlActualizar = "../../det_registro.php?id={$infopac['id']}&pos1={$wbasedato}&pos2=2010-03-16&pos3=16:46:49&pos4=000002&pos5=0&pos6={$wbasedato}&tipo=P&Valor=&Form=000002-{$wbasedato}-C-Informacion%20del%20Paciente&call=0&change=0&key=$key&Pagina=1";
				$urlActualizar .= "&nid={$infopac['Pacnid']}&cita={$rows['id']}&slBdCitas=$wbasecitas";
				$nuevoPaciente = "<a href='$urlActualizar' target='_blank'>Actualizar</a>";
				
//				if( $infopac['Inging'] > 0 && !tienePrimeraHistoria( $infopac['Pachis'], $infopac['Inging'] ) ){
//					$copySelect = "CON INGRESO";
//				}
			}
			else{
				echo "<td>{$rows['Nom_pac']}</td>";
				$copySelect = '';
				
				$nuevoPaciente = "<a href='$url' target='_blank'>Registrar 1ra vez</a>";
			}
			
			echo "<td style='text-align:center'>{$rows['Cedula']}</td>";
			echo "<td align='center'>$nuevoPaciente</td>";
			
			//Desactivo esta opción por que un paciente pudo no haber sido atendido con HCE por el Dr.
			// if( $infopac['Inging'] > 0 && !tienePrimeraHistoria( $infopac['Pachis'], $infopac['Inging'] ) ){
				// echo "<td align='center' bgcolor='yellow'>CON INGRESO</td>";
			// }
			// else{
				// echo "<td align='center'>$copySelect</td>";
			// }
			
			echo "<td align='center'>$copySelect</td>";
			echo "</tr>";
			
		}
		
		echo "</table>";
		
		//creando obejeto info para el javascript.......
//		echo ".....<script>mm = {0 :{ his:1 }, 1:{his:2}}</script>";
//		echo ".........$objetojs length : $i, selectedIndex : -1 };";
//		echo "<script>$objetojs length : $i, selectedIndex : -1 };</script>";
//		echo "*@|@*$objetojs length : $i, selectedIndex : -1 };*@|@*";
		
	}
	else{
		echo "<center><b>NO HAY CITAS ASIGNADAS</b></center>";
	}
}

/*************************************************************************************************************
 * 											  FIN FUNCIONES
 ************************************************************************************************************/

include_once( "root/comun.php" );

include_once( "./funcionesGenerales.php" );
	
$conex = obtenerConexionBD( "matrix" );

$key = substr($user, 2, strlen($user));

if( @$consultaAjax ){
	
	switch( $consultaAjax ){
		
		case 10:
//			registrarIngreso( $his, $ing, $tin, $emp, $doc, $idc, utf8_decode( $aco ), utf8_decode( $mre ), utf8_decode( $cex ), utf8_decode( $dia ) );
			registrarIngreso( $his, $ing, $tin, $emp, $doc, $idc, $aco, $mre, $cex, $dia, $eps, $nre, $tre, $par, $tvi, $tac );
			break;
			
		case 11:
			generarTablaPacientes( strtoupper( $doctorName ), $cod );
			admisionPacientes( $cod );
			break;
			
		case 12:
			pacientesAtendidos();
			break;
			
		case 13:
			buscarBases( $codigoMedico );
			actualizarSalaEspera( $his, $ing, $idc, $tin, $codigoMedico );
			break;
			
		case 14:
			$val = tienePrimeraHistoria( $his, $ing ); 
			
			if( $val === false ){
				echo "false";
			}
			break;
			
		case 15:
			echo ingresoAnterior( $his, $ing );
			break;
		
		default:
			break;
	}
	
}
else{
	
	if( !isset($doc) ){
		
		$sql = "SELECT
					*
				FROM
					root_000055
				WHERE
					medsec like '%$key%'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 1 ){
			
//			encabezado( "AGENDAS MEDICAS", "2010-01-13", "fmatrix" );
//			
//			echo "<br><br>";
//			
//			echo "<center><b>AGENDAS MEDICAS</b></center><br>";
			
			for( $i = 0; $rows=mysql_fetch_array( $res ); $i++ ){
				
				$doc = $rows['Medusu'];
				
//				echo "<center>";
//				echo "<br><br>";
//				echo "<a href='agendaMedicaConsultorio.php?doc={$rows['Medusu']}'>";
//				echo "Agenda Médicas del Dr. ".strtoupper( $rows['Mednom'] );
//				echo "</a>";
//				echo "</center>";
				
			}
		}
		elseif( $numrows == 0 ){
			echo "<center><b>NO TIENE PROFESIONALES ASIGNADOS</b></center>";
		}
		else{
			$rows = mysql_fetch_array( $res );
			$doc = $rows['Medusu'];
		}
	}
	
	if( isset( $doc ) && !empty( $doc ) ){
		
		$infoMedico = new classMedico( $doc );
	
		$wbasedato = $infoMedico->bdHC;
		$wbasecitas = $infoMedico->bdCitas;
		
		$wemp_pmla = '01';
		
		if(!isset($wemp_pmla)){
			terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
		}
?>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script> 	<!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery-ui-1.7.2.custom.js"></script> 	<!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script> <!-- Block UI -->
<script type="text/javascript" src="../../../include/root/ui.core.js"></script>	<!-- Nucleo jquery -->
<script type="text/javascript">

/************************************************************
 * AJAX DE MAURICIO
 ***********************************************************/

/******************************************************************
	 * Realiza una llamada ajax a una pagina
	 * 
	 * met:		Medtodo Post o Get
	 * pag:		Página a la que se realizará la llamada
	 * param:	Parametros de la consulta
	 * as:		Asincronro? true para asincrono, false para sincrono
	 * fn:		Función de retorno del Ajax
	 *
	 * Nota: Si la llamada es GET las opciones deben ir con la pagina.
	 ******************************************************************/
	function consultasAjax( met, pag, param, as, fn ){
		
		this.metodo = met;
		this.parametros = param; 
		this.pagina = pag;
		this.asc = as;
		this.fnchange = fn; 
	
		try{
			this.ajax=nuevoAjax();
	
			this.ajax.open( this.metodo, this.pagina, this.asc );
			this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			this.ajax.send(this.parametros);
	
			if( this.asc ){
				var xajax = this.ajax;
	//			this.ajax.onreadystatechange = this.fnchange;
				this.ajax.onreadystatechange = function(){ fn( xajax ) };
				
				if ( !estaEnProceso(this.ajax) ) {
					this.ajax.send(null);
				}
			}
			else{
				return this.$.trim( ajax.responseText );
			}
		}catch(e){	}
	}

/************************************************************
 * AJAX
 ***********************************************************/

/************************************************************
 * AJAX
 ***********************************************************/

	var XMLHttpRequestObject = false;
	
	if( window.XMLHttpRequest ){
		XMLHttpRequestObject = new XMLHttpRequest();
	} 
	else if( window.ActiveXObject ){
		XMLHttpRequestObject = new ActiveXObject( "Microsoft.XMLHTTP" );
	}
	
	function cambioEstadoAjax(){
		if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
	//		alert( XMLHttpRequestObject.responseText );
	//		alert( "la opcion fue: " + opcionmas );
			cerrarventanaajax = false;
		}
	}
	
	function pedirDatos(pagina, optajax, add, asc ){
		
		if( XMLHttpRequestObject ){
	
			XMLHttpRequestObject.open( "GET", pagina+"?consultaAjax="+optajax+add, asc );
	//		XMLHttpRequestObject.onreadystatechange = cambioEstadoAjax;
			XMLHttpRequestObject.send( null );
	
			if( asc == false ){
				return $.trim( XMLHttpRequestObject.responseText );
			}
		} 
	}
 
/***********************************************************
 * FIN DE AJAX
 ***********************************************************/


 

 	info = {};
 	info.length = 0;



 	function stopEvent(e) {
 		alert( "Hola de detener evento evento....." );
 	    if (!e) e = window.event;
 	    if (e.stopPropagation) {
 	        e.stopPropagation();
 	    } else {
 	        e.cancelBubble = true;
 	    }
 	}

 	function cancelEvent(e) {
 	 	alert( "Hola de cancelar evento....." );
 	    if (!e) e = window.event;
 	    if (e.preventDefault) {
 	        e.preventDefault();
 	    } else {
 	        e.returnValue = false;
 	    }
 	}
 	 	

 	/**************************************************************
 	 * Cambia la agenda al medico seleccionado
 	 ***************************************************************/
 	function cambiarMedico( campo ){

 		if( campo.options[ campo.selectedIndex ].value == '' ){
 	 		return;
 		}
		
 	 	document.forms[0].elements['doc'].value = campo.options[ campo.selectedIndex ].value;
 	 	document.forms[0].submit();
 	}

 	/**
 	 * Detiene los eventos
 	 */
 	function detenerEventos( evt ){

		if( !evt ){
			evt = event;
		}
		
		var key = evt.keyCode ? evt.keyCode : evt.which ;

		if ( (key <= 123 && key >= 112) ){
			
			if (evt && evt.stopPropagation) 
				evt.stopPropagation();
			else if (evt) {
				evt.cancelBubble = true;
				evt.returnValue = false;
			}
			if (evt && evt.preventDefault) 
				evt.preventDefault(); //Para Mozilla Firefox

			evt.keyCode = 0;
			return false;
		}
		
	}

 	/**************************************************
 	 * Crea un mensaje de Dialogo para el usuario
 	 **************************************************/
	function cuadroDeDialogo( mensaje ){

		$.blockUI({ message: "<br><center><b>"+mensaje+"</b><center><br><br><center><input type='button' onclick='javascript: $.unblockUI();' value='Aceptar'></center><br>",
					css:{ backgroundColor: '#E8EEF7' } 
				  });
	}

 	/********************************************************
	 * Detiene un evento
	 ********************************************************/
 	function detenerEventos( e ){

 		if (!e) e = window.event;
 	    if (e.stopPropagation) {
 	        e.stopPropagation();
 	        e.preventDefault();
 	    } else {
 	    	e.returnValue = false;
 	    }

// 		if (!e){
// 	 		 e = window.event;
// 		}
// 	    if (e.stopPropagation) {
// 	        e.stopPropagation();
// 	    } 
// 	    else {
// 	        e.cancelBubble = true;
// 	    }

	}

 	function regresarAdmision(){

 	 	var tabla = document.getElementById( 'tbAgenda' );

 	 	tabla.rows[ info.selectedIndex+1 ].cells[5].firstChild.checked = false;
 	 	habilitarAdmision( tabla.rows[ info.selectedIndex+1 ].cells[5].firstChild );
 	}
 	
 	/********************************************************
 	 * Muestra la tabla de pacientes atendidos
 	********************************************************/
	function mostrarAtendidos( campo ){

		if( campo.checked == false ){
			document.getElementById( 'dvAtendidos' ).style.display = "none";
		}
		else{
			document.getElementById( 'dvAtendidos' ).style.display = "";
		}
	}
 	
 	/********************************************************
 	 * Deja un valor por defecto para la empresa al momento
 	 * de hacer el ingreso para un paciente
 	 *
 	 * val : nit de la empres
 	 ********************************************************/
	function porDefecto( val ){

		slCampo =  document.forms[0].elements['slEmpresa'];

		for( var i = 0; i < slCampo.options.length; i++ ){

			if( slCampo.options[i].text == val ){
				slCampo.selectedIndex = i;
				return;
			}
		}
	}
	
	/********************************************************
 	 * Deja un valor por defecto para la empresa al momento
 	 * de hacer el ingreso para un paciente
 	 *
 	 * val : nit de la empres
 	 ********************************************************/
	function porDefecto2( val ){

		slCampo =  document.forms[0].elements['slEps'];

		for( var i = 0; i < slCampo.options.length; i++ ){

			if( slCampo.options[i].text == val ){
				slCampo.selectedIndex = i;
				return;
			}
		}
	}
 	
 	function agregarPacientes( id, his, ing, doc, emp, idc, eps ){

 	 	info[id] = Array();
 	 	info[id].his = his;
 	 	info[id].ing = ing;
 	 	info[id].doc = doc;
 	 	info[id].emp = emp;
 	 	info[id].cit = idc;
 	 	info[id].eps = eps;

 	 	if( ing == '' ){
 	 		info[id].ing = '0';
 	 	}
 	 	
 	 	info.length++;
 	 	info.selectedIndex = -1;
 	}
 
	function habilitarAdmision( campo ){

		if( campo.checked == true ){

			document.getElementById( 'dvPiePagina' ).style.display = 'none';

//			clearInterval( idInterval );

			var tbAdmision = document.getElementById( "tbAdmision" );
			tbAdmision.style.display = "";
			tbAdmision.align = 'center';

			info.selectedIndex = parseInt( campo.name.substring( campo.name.indexOf("[")+1, campo.name.length-1 ) );

			//Buscando los valores de los datos anteriores
			var add = "wbasedato="+document.forms[0].elements['wbasedato'].value;
			add = add+"&his="+info[ info.selectedIndex ].his;
			add = add+"&ing="+info[ info.selectedIndex ].ing;
			add = add+"&consultaAjax=15";
			var ingresoAnterior = consultasAjax( 'POST', './agendaMedicaConsultorio.php', add, false );

			var arIngAnt = ingresoAnterior.split("*");
			document.forms[0].elements[ 'txMre' ].value = arIngAnt[0];
			document.forms[0].elements[ 'txDia' ].value = arIngAnt[1];
			document.forms[0].elements[ 'txCex' ].value = arIngAnt[2];
			
			document.forms[0].elements[ 'txNre' ].value = arIngAnt[3];
			document.forms[0].elements[ 'txTre' ].value = arIngAnt[4];
			document.forms[0].elements[ 'txPar' ].value = arIngAnt[5];
			document.forms[0].elements[ 'txAco' ].value = arIngAnt[6];
			document.forms[0].elements[ 'txTac' ].value = arIngAnt[7];
			
			//Si esta vacio, causa externa siempre es EVENTO CATASTRÓFICOS
			if( document.forms[0].elements[ 'txCex' ].value == '' ){
				document.forms[0].elements[ 'txCex' ].value = 'Evento catastrófico';
			}

			var tbPac = document.getElementById( "tbAgenda" );

			campo.checked = false;
			inTb = tbPac.innerHTML;
			campo.checked = true; 

			for( var i=1; i < tbPac.rows.length; i++ ){
				if( info.selectedIndex != i-1 ){
					tbPac.rows[i].style.display = "none";
				}
			}

			porDefecto( info[ info.selectedIndex ].emp );
			porDefecto2( info[ info.selectedIndex ].eps );
			
			if( info[ info.selectedIndex ].ing > 0 ){
				document.forms[0].elements[ 'slTipoIngreso' ].selectedIndex = 0;
			}
			else{
				document.forms[0].elements[ 'slTipoIngreso' ].selectedIndex = 0;
			}

			document.getElementById( 'lkCita' ).style.display = "none";
			document.getElementById( 'dvGeneralAtendidos' ).style.display = "none";
		}
		else{

			document.getElementById( 'dvPiePagina' ).style.display = '';

			var tbAdmision = document.getElementById( "tbAdmision" );
			tbAdmision.style.display = "none";

			var tbPac = document.getElementById( "tbAgenda" );

			for( var i=1; i < tbPac.rows.length; i++ ){
				if( info.selectedIndex != i-1 ){
					tbPac.rows[i].style.display = "";
				}
			}

			info.selectedIndex = -1;

			document.getElementById( 'lkCita' ).style.display = "";

			document.getElementById( 'dvGeneralAtendidos' ).style.display = "";

			document.forms[0].elements['txAco'].value = '';
			document.forms[0].elements['txDia'].value = '';
			document.forms[0].elements['txCex'].value = '';
			

//			idInterval = setTimeout( "actualizarDatos()", 60000 );
		}
	}

	function registrarIngreso(){

		if( document.forms[0].cbEnviar.checked == true ){

			var slTin = document.forms[0].elements[ 'slTipoIngreso' ];
			var slEmp = document.forms[0].elements[ 'slEmpresa' ];
			var slEps = document.forms[0].elements[ 'slEps' ];

			if( slTin.options[ slTin.selectedIndex ].text == '' ){
				cuadroDeDialogo( "Debe seleccionar un Tipo de ingreso" );
			}
			else if( slEmp.options[ slEmp.selectedIndex ].text == '' ){
				cuadroDeDialogo( "Debe seleccionar una Empresa" );
			}
			else if( slEps.options[ slEps.selectedIndex ].text == '' ){
				cuadroDeDialogo( "Debe seleccionar una EPS" );
			}
			else if( slTin.options[ slTin.selectedIndex ].text != "01-CONSULTA" && info[ info.selectedIndex ].ing == 0 ){
//				alert( "Debe elegir la opcion de CONSULTA" );
				cuadroDeDialogo( "Debe elegir la opcion de CONSULTA" );
			}
			// Se quita esta validación ya que el profesional de la salud puede no hacer la HCE
			// else if( pedirDatos( './agendaMedicaConsultorio.php', 
					// 14, 
					// "&his="+info[ info.selectedIndex ].his+"&wbasedato="+document.forms[0].elements[ 'wbasedato' ].value+"&ing="+info[ info.selectedIndex ].ing, 
					// false) != '' && 
					// slTin.options[ slTin.selectedIndex ].text == "01-CONSULTA" ){

					// alert(  "......."+pedirDatos( './agendaMedicaConsultorio.php', 
							// 14, 
							// "&his="+info[ info.selectedIndex ].his+"&wbasedato="+document.forms[0].elements[ 'wbasedato' ].value+"&ing="+info[ info.selectedIndex ].ing, 
							// false)+info[ info.selectedIndex ].ing );
// //					alert( "No puede hacer un ingreso por Consulta" );

					// cuadroDeDialogo( "No puede hacer un ingreso por Consulta.<br><br>Ya tiene un ingreso sin Historia." );
			// }
			else{
				
		
				add = "&his="+info[ info.selectedIndex ].his;
	
				if( slTin.options[ slTin.selectedIndex ].text == "01-CONSULTA" ){
					var newing = 1+parseInt(info[ info.selectedIndex ].ing);
					add = add+"&ing=" + newing;
				}
				else{
					add = add+"&ing="+parseInt( info[ info.selectedIndex ].ing );
				}
				
				add = add+"&tin="+slTin.options[ slTin.selectedIndex ].text;
				add = add+"&emp="+slEmp.options[ slEmp.selectedIndex ].text;
				add = add+"&eps="+slEps.options[ slEps.selectedIndex ].text;
				add = add+"&doc="+info[ info.selectedIndex ].doc;
				add = add+"&wbasedato="+document.forms[0].elements[ 'wbasedato' ].value;
				add = add+"&wbasecitas="+document.forms[0].elements[ 'wbasecitas' ].value;
				add = add+"&idc="+info[ info.selectedIndex ].cit;
				add = add+"&dia="+document.forms[0].elements[ 'txDia' ].value;
				add = add+"&aco="+document.forms[0].elements[ 'txAco' ].value;
				add = add+"&cex="+document.forms[0].elements[ 'txCex' ].value;
				add = add+"&mre="+document.forms[0].elements[ 'txMre' ].value;
				
				
				add = add+"&nre="+document.forms[0].elements[ 'txNre' ].value;
				add = add+"&tre="+document.forms[0].elements[ 'txTre' ].value;
				add = add+"&par="+document.forms[0].elements[ 'txPar' ].value;
				add = add+"&tvi="+document.forms[0].elements[ 'txTvi' ].value;
				add = add+"&tac="+document.forms[0].elements[ 'txTac' ].value;
				
				var txtres = pedirDatos( './agendaMedicaConsultorio.php', 10, add, false);

//				alert( "........"+txtres );
	
				if( txtres == "" ){

					//Actualizando Sala de Espera
					add = "&his="+info[ info.selectedIndex ].his;

					if( slTin.options[ slTin.selectedIndex ].text == "01-CONSULTA" ){
						var newing = 1+parseInt(info[ info.selectedIndex ].ing);
						add = add + "&ing=" + newing;
					}
					else{
						add = add + "&ing="+parseInt( info[ info.selectedIndex ].ing );
					}

					add = add + "&idc="+info[ info.selectedIndex ].cit;
					add = add + "&wbasedato="+document.forms[0].elements[ 'wbasedato' ].value;
					add = add + "&wbasecitas="+document.forms[0].elements[ 'wbasecitas' ].value;
					add = add + "&tin="+slTin.options[ slTin.selectedIndex ].text;
					add = add + "&codigoMedico="+document.forms[0].elements[ 'doc' ].value;
					
					var txtAct = pedirDatos( './agendaMedicaConsultorio.php', 13, add, false );

					document.forms[0].submit();
				}
				else{
//					alert( "............"+txtres );
					alert( "Hay un error en los datos" );
				}
			}
			
		}
		else{
//			alert( "Debe confirmar ingreso" );
			cuadroDeDialogo( "Debe confirmar admisión" );
		}
	}

	function recargarPagina(){
//		alert("Hola.....");
		if( info.selectedIndex == -1 ){
			document.forms[0].submit();
		}

		setTimeout( "recargarPagina()", 10000 );
	}

//	window.onbeforeunload = function( event ){ 
//		debugger;
//		if( info.selectedIndex > -1 ){
////			alert( "Deteniendo evento......" );
//			detenerEventos( event );
////			cancelEvent(event);
////			stopEvent(event);
//						
//		}	
//	}

	function pacientes( ajax ){
		
		if ( ajax.readyState==4 && ajax.status==200)
		{ 
			var auxDv = document.getElementById( "dvPacientes" );

			var datos = $.trim( ajax.responseText ).split( "*@|@*" );
			
			auxDv.innerHTML = $.trim( ajax.responseText );
			auxDv.innerHTML = datos[0]+datos[2];
			info={};
			eval( datos[1] );
//			alert( "....." + $.trim( ajax.responseText ) );
		}
	}

	function atendidos(){

		if ( this.readyState==4 && this.status==200)
		{ 
			var auxDv = document.getElementById( "dvAtendidos" );
			auxDv.innerHTML = this.responseText;
		}
	}

	function actualizarDatos(){

		var wbasedato = document.forms[0].elements[ 'wbasedato' ].value; 
		var wbasecitas = document.forms[0].elements[ 'wbasecitas' ].value;
		var doctorName = document.forms[0].elements[ 'doctorName' ].value;
		var cod = document.forms[0].elements[ 'doc' ].value;;
		var parametros = "consultaAjax=11&wbasedato="+wbasedato+"&wbasecitas="+wbasecitas+"&doctorName="+doctorName+"&cod="+cod;

		var txtres = consultasAjax( "POST", "./agendaMedicaConsultorio.php", parametros, true, pacientes );
//		pacientes( txtres );
//alert( ".......Hola" );
//		var parametros = "consultaAjax=12&wbasedato="+wbasedato+"&wbasecitas="+wbasecitas+"&cod="+cod;
//		txtres = consultasAjax( "POST", "./agendaMedicaConsultorio.php", parametros, true, pacientes );
//		atendidos( txtres );

		idInterval = setTimeout( "actualizarDatos()", 60000 );
	}

	window.onload = function(){

		setTimeout( "recargarPagina()", 60000 );

		try{
			if( document.forms[0].elements[ 'slFiltroProfesional' ].options.length == 2 ){
				document.getElementById( 'tbFiltroProfesional' ).style.display = 'none';
			}
			else{
//				document.forms[0].elements[ 'slFiltroProfesional' ].selectedIndex = 2;
			}
		}
		catch(e){}

//		actualizarDatos();
	}

</script>

<?php 
/*************************************************************************************************************
 * 											INICIO DEL PROGRAMA
 ************************************************************************************************************/
	
		//El usuario se encuentra registrado
		if( !isset($_SESSION['user']) ){
			echo "Error: Usuario No registrado";
		}
		else{
			
			echo "<form>";
			
			$infopacientes = array();
			$doctorName = $infoMedico->nombre;
			$titulo = "AGENDA MEDICA DR. ".strtoupper( $doctorName );
			encabezado( $titulo, "2010-01-13", "fmatrix" );
			
			echo "<br><br>";
			
			filtroMedicos();
			
			echo "<br><br>";
			
			echo "<div id='dvPacientes'>";
			generarTablaPacientes( strtoupper( $doctorName ), $infoMedico->codigo );
//			
			admisionPacientes( $infoMedico->codigo );
			echo "</div>";
			
			echo "<div id='dvGeneralAtendidos'>";
			
			echo "<p align='right'>Ver Admitidos  <INPUT type='checkbox' name='cbMostarAntendidos' onClick='javascript: mostrarAtendidos( this );'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
			
			echo "<div id='dvAtendidos' style='display:none'>";
			pacientesAtendidos( $key );
			echo "</div>";
			echo "</div>";
			
			echo "<div id='dvPiePagina'>";
			echo "<br><br>";
			echo "<center>"; 
			echo "<a id='lkCita' href='../../citas/procesos/000001_prx5.php?empresa=$wbasecitas' target='_blank'>Asignar cita</a>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
//			echo "<a id='lkImpresion' href='impresion.php?doc={$infoMedico->codigo}' target='_blank'>Consultar e imprimir</a>";
			echo "<a id='lkImpresion' href='../../{$infoMedico->grupoSolucion}/procesos/impresion.php?doc={$infoMedico->codigo}' target='_blank'>Consultar e imprimir</a>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='javascript: document.forms[0].submit();'>Recargar Página</a>";
			echo "</center>";
			
			echo "<br><br>";
			
			echo "<center><INPUT type='button' value='Cerrar ventana' style='width:150' onClick='javascript: cerrarVentana();'></center>";
			echo "</div>";
			
//			echo "<meta http-equiv='refresh' content='10;url=agendaMedicaConsultorio.php?doc={$infoMedico->codigo}'>";
	
			echo "<INPUT type='hidden' name='wbasedato' value='$wbasedato'>";
			echo "<INPUT type='hidden' name='wbasecitas' value='$wbasecitas'>";
			echo "<INPUT type='hidden' name='doctorName' value='{$infoMedico->nombre}'>";
			echo "<INPUT type='hidden' name='doc' value='{$infoMedico->codigo}'>";
			
			echo "</form>";
			
		}
		
	}

}
/*************************************************************************************************************
 * 												FIN DEL PROGRAMA
 ************************************************************************************************************/
?>