<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<?php
$key = substr($user,2,strlen($user));
if (isset($accion) and $accion == 'actualizar')  //atendido cambia el campo asistida y atendido
 { 
	

	


    $horaAten=date("H:i:s");

	if ($caso == 3 or $caso == 1)
	{
		$sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Hora_aten = '".$horaAten."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}
	else if ($caso == 2 and $valCitas != "on")    //el asiste de clisur utiliza la funcion vieja
	{
		$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Hora_aten = '".$horaAten."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	return;
}
else if (isset($accion) and $accion == 'cancelar')  //cancelar
{   
	

	


	if ($caso == 3 or $caso == 1)
	{
		 $sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Activo = '".$est."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";
	}
	else if ($caso = 2)
	{

		$sql = "UPDATE
				".$wemp_pmla."_000009
			SET
				Activo = '".$est."',
				Causa = '".$causa."'

			WHERE
				id = '$id'";
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return;
}
else if (isset($accion) and $accion == 'actualizar1')  //no asiste
 { 
	

	


	if ($caso == 3 or $caso == 1)
	{
		$sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}
	else if ($caso == 2 and $valCitas != "on")
	{
		$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}
	else if ($caso == 2 and $valCitas == "on")
	{
		$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."',
						Causa = '".$causa."'
					WHERE
						id = '$id'";
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return;
}
else if (isset($accion) and $accion == 'demora')  //atendido cambia el campo asistida y atendido cuando han pasado 15 minutos de la cita se pide causa
 { 
	

	


    $horaAten=date("H:i:s");

	if ($caso == 3 or $caso == 1)
	{
		$sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Hora_aten = '".$horaAten."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}
	else if ($caso == 2 and $valCitas != "on")    //el asiste de clisur utiliza la funcion vieja
	{
		$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Hora_aten = '".$horaAten."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return;
}

//Fecha creación: 2016-02-11
//Autor: Eimer Castro
//Esta función de post se utiliza para realizar la creación del registro en la tabla de logs citasen_000023
//y adicionalmente realiza la impresión del turno para el paciente que tiene cita.
else if (isset($accion) and $accion == 'autorizacion') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '', 'Turno' => '', 'fichoTurno' => '');

	// --> Validar si ya existe un turno asignado hoy, para el documento
	$sqlValTurno = " SELECT Logtur
					   FROM " . $solucionCitas . "_000023
					  WHERE Fecha_data 	= '" . date("Y-m-d")."'
					    AND Logdoc 		= '" . $cedulaPac."'
					  ORDER BY Logtur DESC";

	$resValTurno = mysql_query($sqlValTurno, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValTurno):</b><br>".mysql_error());

	// --> Obtener el ultimo consecutivo
	$sqlObtConsec = " SELECT MAX(REPLACE(Logtur, '-E', '')*1) AS turno
						FROM " . $solucionCitas . "_000023
					   WHERE Logtur LIKE '".date('ymd')."%'";

	$resObtConsec = mysql_query($sqlObtConsec, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlObtConsec):</b><br>".mysql_error());
	if($rowObtConsec = mysql_fetch_array($resObtConsec))
	{
		$fechaUltiConse = substr($rowObtConsec['turno'], 0, 6);
		$ultConsecutivo = (($fechaUltiConse == date('ymd')) ? substr($rowObtConsec['turno'], 6) : 0);
		$ultConsecutivo	= ($ultConsecutivo*1)+1;
		// --> Asignar ceros a la izquierda hasta completar 3 digitos
		while(strlen($ultConsecutivo) < 3)
			$ultConsecutivo = '0'.$ultConsecutivo;

		$nuevoTurno		= date('ymd').'-E'.$ultConsecutivo;

		// --> Asignarle el turno al paciente
		$usuario = explode("-", $_SESSION['user']);
		$wuse = $usuario[1];
		$sqlAsigTur = " INSERT INTO " . $solucionCitas . "_000023 (Medico, Fecha_data, Hora_data, Logtur, Logdoc, Logfau, Loghau, Logeau, Loguau, Logest, Seguridad, id)
											VALUES ('" . $solucionCitas . "', '".date('Y-m-d')."', '".date('H:i:s')."', '".$nuevoTurno."', '".$cedulaPac."', '".date('Y-m-d')."', '".date('H:i:s')."', 'on', '".$wuse."', 'on', 'C-".$wuse."', '')";
		$resObtConsec = mysql_query($sqlAsigTur, $conex);

		// --> Si ha ocurrido un error guardando el turno
		if(!$resObtConsec)
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error asignando el turno.<br>Por favor contacte al personal de soporte.</span><br>
										<span style='font-size:10px'>($sqlAsigTur: ".mysql_error().')</span>';
		}
		// --> Genero el ficho del turno
		else
		{
			$respuesta['Turno'] 		= $nuevoTurno;
			$respuesta['fichoTurno'] 	= htmlTurno($nuevoTurno, $cedulaPac, $nombrePac, false);
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= 'Error: El turno no se ha podido asignar.';
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del llamado a admisión.
else if (isset($accion) and $accion == 'llamarPacienteAdmision') {

	

	include_once("root/comun.php");
	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');
	
	//2016-08-04 Verónica Arismendy lo primero es validar que en la misma taquilla no se esté llamando otro paciente al tiempo
	$validarTaquilla = validarDisponibilidadTaquilla($solucionCitas, $ubicacion, "admision");
	
	if($validarTaquilla){		
		$idcitasen9 = explode("_", $idLlamadoAdmision);
		$idce9 = $idcitasen9[1];

		// Trae la cedula asociada al id de la tabla citasen_000009
		$sqlIdCE09 = "	SELECT cedula
							FROM " . $solucionCitas . "_000009
						WHERE id = '" . $idce9 . "';";

		$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

		if($rowIdCE09 = mysql_fetch_array($resIdCE09))
		{
			// --> Validar si ya existe un turno asignado hoy, para el documento
			$sqlValAutorizacion = " SELECT Logtur AS Turno, Logdoc AS Documento, Logela, Loguba, Logfia
							   FROM " . $solucionCitas . "_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
								AND Hora_data	!=	'00:00:00'
								AND Logfau		=	'" . date('Y-m-d') . "'
								AND Loghau		!=	'00:00:00'
								AND Logeau		=	'on'
								AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
							  ORDER BY Turno DESC;";

			$resValAutorizacion = mysql_query($sqlValAutorizacion, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValAutorizacion):</b><br>".mysql_error());

			if($rowValAutorizacion = mysql_fetch_array($resValAutorizacion))
			{
				if($rowValAutorizacion['Logela'] != 'on')
				{
					$sqlActLlamadoAdm = " UPDATE " . $solucionCitas . "_000023
												SET
													Logfla			=	'".date('Y-m-d')."',
													Loghla			=	'".date('H:i:s')."',
													Logela			=	'on',
													Loguba			=	'" . $ubicacion . "'
												WHERE	Logtur		=	'" . $rowValAutorizacion['Turno'] . "'
														AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

					$resActLlamadoAdm = mysql_query( $sqlActLlamadoAdm, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadoAdm - ".mysql_error() );

					$turnoExplode = explode("-", $rowValAutorizacion['Turno']);
					$turnoLlamado = $turnoExplode[1];
					$respuesta['Error'] 	= false;
					$respuesta['Mensaje'] 	= "Paciente con turno " . $turnoLlamado . " pasar a taquilla.";

					// --> Si ha ocurrido un error guardando el turno
					if(!$resActLlamadoAdm)
					{
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error llamando al paciente.<br>Por favor contacte al personal de soporte.</span><br>
													<span style='font-size:10px'>($sqlActLlamadoAdm: ".mysql_error().')</span>';
					}
				}
				else
				{				
					//2016-04-07 Se debe mirar si el mensaje a mostrar es que está siendo llamado en taquilla o que ya se inició el proceso de adminisión
					if($rowValAutorizacion['Logfia'] != "0000-00-00"){
						$respuesta['Mensaje'] 	= "No es posible realizar el llamado, ya se ha iniciado la admisi&oacute;n del paciente.";
					}else{
						$nameTable = consultarAliasPorAplicacion($conex, "01", 'citasen');
						$arrayUbicaciones = crearArrayPerfilxUbicacion($nameTable);						
						$respuesta['Mensaje'] 	= "El paciente está siendo llamado a admisión en este momento en ".$arrayUbicaciones["01"][$rowValAutorizacion['Loguba']]."";
					}					
					
					$respuesta['Error'] 	= true;					
				}
			}
			
			
			else
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No es posible realizar el llamado del paciente.<br>Verifique que este ya tenga un turno asignado.";
			}
		}
		else
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No es posible realizar la validación para el llamado del paciente.<br>Verifique que se encuentre agendado.";
		}
	}else {
		$respuesta['Error'] = true;	
		$respuesta['Mensaje'] 	= "No es posible realizar el llamado de dos pacientes al mismo tiempo.";		
	}

	echo json_encode($respuesta);
	return;
}


//Fecha creación: 2016-03-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del llamado a admisión.
else if (isset($accion) and $accion == 'llamarPacienteAdmisionSinCita') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

		// --> Validar si ya existe un turno asignado hoy, para el documento
		$sqlValAutorizacion = " SELECT Logtur AS Turno, Logdoc AS Documento
						   FROM " . $solucionCitas . "_000023
						  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
						    AND Hora_data	!=	'00:00:00'
						    AND Logfau		=	'" . date('Y-m-d') . "'
						    AND Loghau		!=	'00:00:00'
						    AND Logeau		=	'on'
						    AND Logdoc		=	'" . $cedulaPac . "'
						  ORDER BY Turno DESC;";

		$resValAutorizacion = mysql_query($sqlValAutorizacion, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValAutorizacion):</b><br>".mysql_error());

		if($rowValAutorizacion = mysql_fetch_array($resValAutorizacion))
		{

			$sqlActLlamadoAdm = " UPDATE " . $solucionCitas . "_000023
										SET
											Logfla			=	'".date('Y-m-d')."',
											Loghla			=	'".date('H:i:s')."',
											Logela			=	'on',
											Loguba			=	'" . $ubicacion . "'
										WHERE	Logtur		=	'" . $turnoPac . "'
												AND Logdoc	=	'" . $cedulaPac . "'";

			$resActLlamadoAdm = mysql_query( $sqlActLlamadoAdm, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadoAdm - ".mysql_error() );

			$turnoExplode = explode("-", $rowValAutorizacion['Turno']);
			$turnoLlamado = $turnoExplode[1];
			$respuesta['Error'] 	= false;
			$respuesta['Mensaje'] 	= "Paciente con turno " . $turnoLlamado . " pasar a taquilla.";

			// --> Si ha ocurrido un error guardando el turno
			if(!$resActLlamadoAdm)
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error llamando al paciente.<br>Por favor contacte al personal de soporte.</span><br>
											<span style='font-size:10px'>($sqlActLlamadoAdm: ".mysql_error().')</span>';
			}
		}
		else
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el llamado del paciente.<br>Verifique que este ya tenga un turno asignado.";
		}

	echo json_encode($respuesta);
	return;
}


//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la admisión de la persona desde el programa de
//admisiones_erp.php e inmediatamente se realiza la actualización del registro en la tabla de
//logs citasen_000023 de los campos del inicio de la admisión.
else if (isset($accion) and $accion == 'admision') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '', 'Turno' => '');

	$idcitasen9 = explode("_", $idAdmision);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
						FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		// --> Validar si ya existe un turno asignado hoy, para el documento
		$sqlValLlamadoAdmision = " SELECT Logtur AS Turno, Logdoc AS Documento
									   FROM " . $solucionCitas ."_000023
									  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
									    AND Hora_data	!=	'00:00:00'
									    AND Logfla		=	'" . date('Y-m-d') . "'
									    AND Loghla		!=	'00:00:00'
									    AND Logela		=	'on'
									    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
									  ORDER BY Turno DESC;";
						
									  
		$resValLlamadoAdmision = mysql_query($sqlValLlamadoAdmision, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValLlamadoAdmision):</b><br>".mysql_error());

		if($rowValLlamadoAdmision = mysql_fetch_array($resValLlamadoAdmision))
		{			
			$sqlActLlamadoAdm = " UPDATE " . $solucionCitas ."_000023
										SET
											Logfia			=	'".date('Y-m-d')."',
											Loghia			=	'".date('H:i:s')."',
											Logeia			=	'on'
										WHERE	Logtur		=	'" . $rowValLlamadoAdmision['Turno'] . "'
												AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

			$resActLlamadoAdm = mysql_query( $sqlActLlamadoAdm, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadoAdm - ".mysql_error() );

			// --> Si ha ocurrido un error actualizando el turno
			if(!$resActLlamadoAdm)
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error iniciando la admisi&oacute;n del paciente.<br>Por favor contacte al personal de soporte.</span><br>
											<span style='font-size:10px'>($sqlActLlamadoAdm: ".mysql_error().')</span>';
			}
			else
			{
				$sqlActCitasen09 = "UPDATE " . $solucionCitas ."_000009
										SET
											Asistida		=	'on'
										WHERE	Fecha		=	'" . date('Y-m-d') . "'
												AND Cedula	=	'" . $rowValLlamadoAdmision['Documento'] . "';";

				$resActCitasen09 = mysql_query( $sqlActCitasen09, $conex ) or die( mysql_errno()." - Error en el query $sqlActCitasen09 - ".mysql_error() );

				if(!$resActCitasen09)
				{
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error actualizando la agenda de citas.<br>Por favor contacte al personal de soporte.</span><br>
												<span style='font-size:10px'>($sqlActLlamadoAdm: ".mysql_error().')</span>';
				}
				else
				{

					$respuesta['Turno'] 	=	$rowValLlamadoAdmision['Turno'];
				}
			}
		}
		else
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido iniciar el proceso para realizar la admisi&oacute;n del paciente.<br>Verifique que ya se realiz&oacute; el llamado de este.";
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para la admisi&oacute;n del paciente.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-03-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del apagado al llamado a la admisión de un paciente sin cita.
else if (isset($accion) and $accion == 'apagarLlamarPacienteAdmisionSinCita') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$sqlValApagarLlamadoAdmisionSinCita = " SELECT Logtur AS Turno, Logdoc AS Documento
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Hora_data	!=	'00:00:00'
							    AND Logfla		=	'" . date('Y-m-d') . "'
							    AND Loghla		!=	'00:00:00'
							    AND Logela		=	'on'
							    AND Logdoc		=	'" . $cedulaPac . "'
							  ORDER BY Turno DESC;";

	$resValApagarLlamadoAdmisionSinCita = mysql_query($sqlValApagarLlamadoAdmisionSinCita, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValApagarLlamadoAdmisionSinCita):</b><br>".mysql_error());

	if($rowValApagarLlamadoAdmisionSinCita = mysql_fetch_array($resValApagarLlamadoAdmisionSinCita))
	{
		$sqlActApagarLlamadoAdmisionSinCita = " UPDATE " . $solucionCitas ."_000023
									SET
										Logfla			=	'0000-00-00',
										Loghla			=	'00:00:00',
										Logela			=	'off'
									WHERE	Logtur		=	'" . $turno . "'
											AND Logdoc	=	'" . $cedulaPac . "'";

		$resActLlamadoAdm = mysql_query( $sqlActApagarLlamadoAdmisionSinCita, $conex ) or die( mysql_errno()." - Error en el query $sqlActApagarLlamadoAdmisionSinCita - ".mysql_error() );

		// --> Si ha ocurrido un error guardando el turno
		if(!$resActLlamadoAdm)
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error apagando el llamado al paciente.<br>Por favor contacte al personal de soporte.</span><br>
										<span style='font-size:10px'>($sqlActApagarLlamadoAdmisionSinCita: ".mysql_error().')</span>';
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar el apagado al llamado del paciente.<br>Verifique que ya se realiz&oacute; el llamado de este.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-03-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del apagado al llamado a la admisión de un paciente con cita.
else if (isset($accion) and $accion == 'apagarLlamarPacienteAdmisionConCita') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$sqlValApagarLlamadoAdmisionConCita = " SELECT Logtur AS Turno, Logdoc AS Documento
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Hora_data	!=	'00:00:00'
							    AND Logfla		=	'" . date('Y-m-d') . "'
							    AND Loghla		!=	'00:00:00'
							    AND Logela		=	'on'
							    AND Logdoc		=	'" . $cedulaPac . "'
							  ORDER BY Turno DESC;";

	$resValApagarLlamadoAdmisionConCita = mysql_query($sqlValApagarLlamadoAdmisionConCita, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValApagarLlamadoAdmisionConCita):</b><br>".mysql_error());

	if($rowValApagarLlamadoAdmisionConCita = mysql_fetch_array($resValApagarLlamadoAdmisionConCita))
	{
		$sqlActApagarLlamadoAdmisionConCita = " UPDATE " . $solucionCitas ."_000023
									SET
										Logfla			=	'0000-00-00',
										Loghla			=	'00:00:00',
										Logela	
 										=	'off'
									WHERE	Logtur		=	'" . $rowValApagarLlamadoAdmisionConCita['Turno'] . "'
											AND Logdoc	=	'" . $rowValApagarLlamadoAdmisionConCita['Documento'] . "'";
		$resActLlamadoAdm = mysql_query( $sqlActApagarLlamadoAdmisionConCita, $conex ) or die( mysql_errno()." - Error en el query $sqlActApagarLlamadoAdmisionConCita - ".mysql_error() );

		// --> Si ha ocurrido un error guardando el turno
		if(!$resActLlamadoAdm)
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error apagando el llamado al paciente.<br>Por favor contacte al personal de soporte.</span><br>
										<span style='font-size:10px'>($sqlActApagarLlamadoAdmisionConCita: ".mysql_error().')</span>';
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar el apagado al llamado del paciente.<br>Verifique que ya se realiz&oacute; el llamado de este.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del llamado a atención.
else if (isset($accion) and $accion == 'llamarPacienteAtencion') {

	

	

	
		$horaAten=date("H:i:s");

		$respuesta = array('Error' => false, 'Mensaje' => '');

		$idcitasen9 = explode("_", $idLlamadoAtencion);
		$idce9 = $idcitasen9[1];

		// Trae la cedula asociada al id de la tabla citasen_000009
		$sqlIdCE09 = "	SELECT cedula
							FROM " . $solucionCitas . "_000009
						WHERE id = '" . $idce9 . "';";

		$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

		if($rowIdCE09 = mysql_fetch_array($resIdCE09))
		{
			// ESTE ES EL QUERY QUE SE UTILIZARÁ
			$sqlValAdmision = " SELECT Logtur AS Turno, Logdoc AS Documento, Logelt, Logeat
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
								AND Hora_data	!=	'00:00:00'
								AND Logfua      =   '" . date('Y-m-d') . "'
								AND Loghua		!= '00:00:00'
								AND Logeua		=   'on'
								AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest      = 'on'
							  ORDER BY Turno DESC
							  ;";
				
			$resValAdmision = mysql_query($sqlValAdmision, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValAdmision):</b><br>".mysql_error());

			if($rowValAdmision = mysql_fetch_array($resValAdmision))
			{
				if($rowValAdmision['Logelt'] != 'on')
				{
					$sqlActLlamadoAdm = " UPDATE " . $solucionCitas ."_000023
												SET
													Logflt			=	'".date('Y-m-d')."',
													Loghlt			=	'".date('H:i:s')."',
													Logelt			=	'on',
													Logubt			=	'" . $ubicacion . "'
												WHERE	Logtur		=	'" . $rowValAdmision['Turno'] . "'
														AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

					$resActLlamadoAdm = mysql_query( $sqlActLlamadoAdm, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadoAdm - ".mysql_error() );

					$turnoExplode = explode("-", $rowValAdmision['Turno']);
					$turnoLlamado = $turnoExplode[1];
					$respuesta['Error'] 	= false;
					$respuesta['Mensaje'] 	= "Paciente con turno " . $turnoLlamado . " pasar a la sala para iniciar preparaci&oacute;n del procedimiento.";

					// --> Si ha ocurrido un error guardando el turno
					if(!$resActLlamadoAdm)
					{
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error llamando al paciente para la atenci&oacute;n.<br>Por favor contacte al personal de soporte.</span><br>
													<span style='font-size:10px'>($sqlActLlamadoAdm: ".mysql_error().')</span>';
					}
					else
					{
						$sqlActCitasen09 = "UPDATE " . $solucionCitas ."_000009
												SET
													Atendido		=	'on',
													Hora_aten		=	'" . date('H:i:s') . "'
												WHERE	Fecha		=	'" . date('Y-m-d') . "'
														AND Cedula	=	'" . $rowIdCE09['cedula'] . "';";

						$resActCitasen09 = mysql_query( $sqlActCitasen09, $conex ) or die( mysql_errno()." - Error en el query $sqlActCitasen09 - ".mysql_error() );

						if(!$resActCitasen09)
						{
							$respuesta['Error'] 	= true;
							$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error actualizando la agenda de citas.<br>Por favor contacte al personal de soporte.</span><br>
														<span style='font-size:10px'>($sqlActLlamadoAdm: ".mysql_error().')</span>';
						}
						else
						{

							$respuesta['Turno'] 	=	$rowValAdmision['Turno'];
						}
					}
				}
				else
				{
					if($rowValAdmision['Logelt'] != 'on'){
						$respuesta['Mensaje'] 	= "No es posible realizar el llamado del paciente.<br>Ya se encuentra llamando al pa
						ciente.";
					}else{
						$respuesta['Mensaje'] 	= "No es posible realizar el llamado. El paciente ya fue llamado a preparación.";
					}
					$respuesta['Error'] 	= true;
					
				}
			}
			else
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No se ha podido realizar el llamado del paciente.<br>Verifique que ya se finaliz&oacute; el proceso de admisi&oacute;n.";
			}
		}
		else
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar la validación para el llamado a atenci&oacute;n del paciente.<br>Verifique que se encuentre agendado.";
		}
		
	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del llamado a atención.
else if (isset($accion) and $accion == 'apagarLlamarPacienteAtencion') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idApagarLlamadoAtencion);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
						FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		// ESTE QUERY ES PARA PROBAR
		$sqlValLlamadoAtencion = " SELECT Logtur AS Turno, Logdoc AS Documento
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Hora_data	!=	'00:00:00'
							    AND Logflt		=	'" . date('Y-m-d') . "'
							    AND Loghlt		!=	'00:00:00'
							    AND Logelt		=	'on'
							    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest      = 'on'
							  ORDER BY Turno DESC;";

		$resValLlamadoAtencion = mysql_query($sqlValLlamadoAtencion, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValLlamadoAtencion):</b><br>".mysql_error());

		if($rowValLlamadoAtencion = mysql_fetch_array($resValLlamadoAtencion))
		{
			$usuario = explode("-", $_SESSION['user']);
			$wuse = $usuario[1];
									
			$sqlActLlamadoAtencion = " UPDATE " . $solucionCitas ."_000023
										SET
											-- Logflt			= '0000-00-00',
											-- Loghlt			= '00:00:00',
											-- Logelt			= 'off',
											Logfat			=	'".date('Y-m-d')."',
											Loghat			=	'".date('H:i:s')."',
											Logeat			=	'on'
											-- Loguat			=	'".$wuse."'
										WHERE	Logtur		=	'" . $rowValLlamadoAtencion['Turno'] . "'
												AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";
									
												
			$resActLlamadoAdm = mysql_query( $sqlActLlamadoAtencion, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadoAtencion - ".mysql_error() );
	
			if(!$resActLlamadoAdm) {
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error apagando el llamado al paciente.<br>Por favor contacte al personal de soporte.</span><br>
											<span style='font-size:10px'>($sqlActLlamadoAtencion: ".mysql_error().')</span>';
			}
		} else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el apagado al llamado del paciente.<br>Verifique que ya se realiz&oacute; el llamado de este.";
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validación para la atenci&oacute;n del paciente.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de acuerdo a si se iniciará o se finalizará el procedimiento del paciente.
else if (isset($accion) and $accion == 'inicioProcedimiento') {

	

	

	
	$validarTaquilla = validarDisponibilidadTaquilla($solucionCitas, $ubicacion, "procedimiento");
	
	if($validarTaquilla){
		$horaAten=date("H:i:s");

		$respuesta = array('Error' => false, 'Mensaje' => '', 'Estado' => '', 'reiniciarPrc' => false, 'turno' => '', 'procNuevo' => false);

		$idcitasen9 = explode("_", $idInicioProcedimiento);
		$idce9 = $idcitasen9[1];

		// Trae la cedula asociada al id de la tabla citasen_000009
		$sqlIdCE09 = "	SELECT cedula
							FROM " . $solucionCitas . "_000009
						WHERE id = '" . $idce9 . "';";

		$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

		if($rowIdCE09 = mysql_fetch_array($resIdCE09))
		{
			$sqlValApagadoLlamadoAtencion = " SELECT Logtur AS Turno, Logdoc AS Documento, Logfip, Loghip, Logeip, Logefp, Logeir
								   FROM " . $solucionCitas ."_000023
								  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
									AND Hora_data	!=	'00:00:00'
									AND Logeaf		=	'on'
									AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
									AND Logest      =  'on'
								  ORDER BY Turno DESC;";

			$resValApagadoLlamadoAtencion = mysql_query($sqlValApagadoLlamadoAtencion, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValApagadoLlamadoAtencion):</b><br>".mysql_error());

			if($rowValApagadoLlamadoAtencion = mysql_fetch_array($resValApagadoLlamadoAtencion))
			{
				if($rowValApagadoLlamadoAtencion['Logeip'] != 'on')
				{
					if($rowValApagadoLlamadoAtencion['Logfip'] == '0000-00-00' && $rowValApagadoLlamadoAtencion['Loghip'] == '00:00:00' && $rowValApagadoLlamadoAtencion['Logeip'] == 'off')
					{
						$sqlActProcedimiento = " UPDATE " . $solucionCitas ."_000023
														SET
															Logfip			=	'".date('Y-m-d')."',
															Loghip			=	'".date('H:i:s')."',
															Logeip			=	'on',
															Logubp			=	'" . $ubicacion . "'
														WHERE	Logtur		=	'" . $rowValApagadoLlamadoAtencion['Turno'] . "'
																AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

						$resActProcedimiento = mysql_query( $sqlActProcedimiento, $conex ) or die( mysql_errno()." - Error en el query $sqlActProcedimiento - ".mysql_error() );

						$turnoExplode = explode("-", $rowValApagadoLlamadoAtencion['Turno']);
						$turnoLlamado = $turnoExplode[1];
						$respuesta['Error'] 	= false;
						$respuesta['Mensaje'] 	= "Paciente con turno " . $turnoLlamado . " se encuentra en procedimiento.";
						$respuesta['Estado'] 	= "iniciarProcedimiento";

						// --> Si ha ocurrido un error guardando el turno
						if(!$resActProcedimiento)
						{
							$respuesta['Error'] 	= true;
							$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error al iniciar el procedimiento del paciente.<br>Por favor contacte al personal de soporte.</span><br>
														<span style='font-size:10px'>($sqlActProcedimiento: ".mysql_error().')</span>';
						}
					}
				}
				else
				{
					//Quiere decir que el procedimiento ya ha sido iniciado y quieren volverlo a iniciar
					//Primero se valida que no se esté intentado reiniciar el procedimiento cuando este ya ha sido finalizado y adicional se ha pasado a la sigueinte fase del proceso
					if($rowValApagadoLlamadoAtencion['Logeir'] != "off"){
						$respuesta['Error'] 		= false;						
						$respuesta['Mensaje'] 		= "No es posible reiniciar el procedimiento una vez que el paciente ya se encuentre en recuperaci&oacute;n.";
					}else{

						//Se valida si el paciente tiene doble agenda para saber si se va es a iniciar el segundo procedimiento
						$sqlValidarAgenda = "SELECT COUNT(0) as cantidadCitas
											 FROM ".$solucionCitas."_000009
											 WHERE 
												Fecha = '".date("Y-m-d")."'
												AND Cedula = '".$rowIdCE09['cedula']."'
												AND Activo = 'A'
						";
						
						$resCant = mysql_query($sqlValidarAgenda, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValidarAgenda):</b><br>".mysql_error());
						$rowCant = mysql_fetch_array($resCant);
						
						if(isset($rowCant["cantidadCitas"]) && $rowCant["cantidadCitas"] > 1){
							$respuesta['Error'] 	= false;
							$respuesta['Mensaje'] 	= "El paciente tiene dos citas el d&iacute;a de hoy para diferentes procedimientos. <br>
														Seleccione si desea reiniciar el primer procedimiento o si de sea empezar uno nuevo.";
							$respuesta['procNuevo'] 	= true;
						}else{
							if($rowValApagadoLlamadoAtencion['Logefp'] != "off"){
							$respuesta['Error'] 	= false;
							$respuesta['Mensaje'] 	= "El procedimiento se encuentra en estado finalizado. ¿Desea reiniciarlo?";
							}else{
								$respuesta['Error'] 	= false;
								$respuesta['Mensaje'] 	= "El procedimiento se encuentra iniciado y no ha sido finalizado. ¿Desea reiniciarlo?";
							}
						}
						$respuesta['reiniciarPrc'] 	= true;
					}
					$respuesta['Estado'] 	= "corregirProcedimiento";					
					$respuesta['turno'] 	= $rowValApagadoLlamadoAtencion['Turno'];					
				}
			}
			else
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No se ha podido realizar el inicio del procedimiento.<br>Verifique que ya se finaliz&oacute; la preparaci&oacute;n del paciente.";
			}
		}
		else
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para el inicio del procedimiento del paciente.<br>Verifique que se encuentre agendado.";
		}
	}else{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No es posible realizar dos llamados al mismo tiempo.";
	}
	
	echo json_encode($respuesta);
	return;
}

else if (isset($accion) and $accion == 'reiniciarProcedimiento') {
	

	

	
	$respuesta = array('Error' => false, 'Mensaje' => '', 'Estado' => '');
	$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";	
	$sqlActProcedimiento = "";
	
	if(isset($tipoReinicio) && $tipoReinicio == "reiniciaPro"){
		$sqlActProcedimiento = "UPDATE " . $solucionCitas ."_000023
								SET Logfip			=	'".date('Y-m-d')."',
									Loghip			=	'".date('H:i:s')."',
									Logeip			=	'on',
									Logffp			=	'0000:00:00',
									Loghfp			=	'00:00:00',
									Logefp			=	'off',
									Logupr			=	'',
									Logubp			=	'" . $ubicacion . "'
								WHERE	Fecha_data	=	'" . date('Y-m-d') . "'
								AND Logdoc	=	'" . $cedulaPac . "'
								AND Logtur = '".$turno."'							
		";
	}
	if(isset($tipoReinicio) && $tipoReinicio == "nuevoPro"){
				
		$sqlActProcedimiento = "UPDATE " . $solucionCitas ."_000023
								SET Logfrp			=	'".date('Y-m-d')."',
									Loghrp			=	'".date('H:i:s')."',
									Logerp			=	'on',
									loguri			=	'".$usuario."',
									Logurp			=	'" . $ubicacion . "'
								WHERE	Fecha_data	=	'" . date('Y-m-d') . "'
								AND Logdoc	=	'" . $cedulaPac . "'
								AND Logtur = '".$turno."'							
		";
	}
	
	if($sqlActProcedimiento != ""){
		$resActFinProcedimiento = mysql_query( $sqlActProcedimiento, $conex ) or die( mysql_errno()." - Error en el query $sqlActProcedimiento - ".mysql_error() );
	
		if(!$resActFinProcedimiento)
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No fue posible reiniciar el procedimiento";
		}
	}else{
		$sqlActProcedimiento = "";
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No fue posible reiniciar el procedimiento";
	}
	
	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de acuerdo a si se iniciará o se finalizará el procedimiento del paciente.
else if (isset($accion) and $accion == 'finProcedimiento') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idFinProcedimiento);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
						FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		// --> Validar si ya existe un turno asignado hoy, para el documento
		$sqlValInicioProcedimiento = "SELECT Logtur AS Turno, Logdoc AS Documento, Logeip, Logefp, Logerp, Logepf
											   FROM " . $solucionCitas ."_000023
											  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
											    AND Hora_data	!=	'00:00:00'
											    AND Logfip		=	'" . date('Y-m-d') . "'
											    AND Loghip		!=	'00:00:00'
											    AND Logeip		=	'on'
											    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
												AND Logest      =   'on'
											  ORDER BY Turno DESC;";

		$resValInicioProcedimiento = mysql_query($sqlValInicioProcedimiento, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValInicioProcedimiento):</b><br>".mysql_error());

		if($rowValInicioProcedimiento = mysql_fetch_array($resValInicioProcedimiento))
		{
			$usuario = explode("-", $_SESSION['user']);
			$wuse = $usuario[1];
			$sqlActProcedimiento = "";
			
			if($rowValInicioProcedimiento['Logeip'] == "on" && $rowValInicioProcedimiento['Logefp'] == "off" ){
				$sqlActProcedimiento = " UPDATE " . $solucionCitas ."_000023
	                                        SET
	                                            Logffp          =   '".date('Y-m-d')."',
	                                            Loghfp          =   '".date('H:i:s')."',
	                                            Logefp          =   'on',
	                                            Logupr          =   '".$wuse."'
	                                        WHERE   Logtur      =   '" . $rowValInicioProcedimiento['Turno'] . "'
	                                                AND Logdoc  =   '" . $rowIdCE09['cedula'] . "'";;
			}
			
			if($rowValInicioProcedimiento['Logerp'] == "on" && $rowValInicioProcedimiento['Logepf'] == "off" ){
				$sqlActProcedimiento = " UPDATE " . $solucionCitas ."_000023
	                                        SET
	                                            Logfpf          =   '".date('Y-m-d')."',
	                                            Loghpf          =   '".date('H:i:s')."',
	                                            Logepf          =   'on',
	                                            loguri          =   '".$wuse."'
	                                        WHERE   Logtur      =   '" . $rowValInicioProcedimiento['Turno'] . "'
	                                                AND Logdoc  =   '" . $rowIdCE09['cedula'] . "'";;
			}
			
			if($sqlActProcedimiento != "")
			{
				$resActProcedimiento = mysql_query( $sqlActProcedimiento, $conex ) or die( mysql_errno()." - Error en el query $sqlActProcedimiento - ".mysql_error() );

				$turnoExplode = explode("-", $rowValInicioProcedimiento['Turno']);
				$turnoLlamado = $turnoExplode[1];
				$respuesta['Error'] 	= false;
				$respuesta['Mensaje'] 	= "Paciente con turno " . $turnoLlamado . " finaliz&oacute; procedimiento.";

				// --> Si ha ocurrido un error guardando el turno
				if(!$resActProcedimiento)
				{
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error al finalizar el procedimiento del paciente.<br>Por favor contacte al personal de soporte.</span><br>
												<span style='font-size:10px'>($sqlActProcedimiento: ".mysql_error().')</span>';
				}
			}else{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No ha sido posible finalizar el procedimiento.";
			}			
		}
		else
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el fin del procedimiento del paciente.<br>Verifique que ya se realiz&oacute; el inicio del procedimiento de este.";
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para finalizar el procedimiento del paciente.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//para indicar el inicio de la recuperación del paciente.
else if (isset($accion) and $accion == 'inicioRecuperacion') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idInicioRecuperacion);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
						FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		// --> Validar si ya existe un turno asignado hoy, para el documento
		$sqlValFinProcedimiento = " SELECT Logtur AS Turno, Logdoc AS Documento, Logfir, Loghir, Logeir, Logefp, Logcps, Logpss
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Hora_data	!=	'00:00:00'
								AND ((Logerp = 'off' && Logepf = 'off') || (Logerp = 'on' && Logepf = 'on'))
							    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest		= 	'on'
							  ORDER BY Turno DESC;";

		$resValFinProcedimiento = mysql_query($sqlValFinProcedimiento, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValFinProcedimiento):</b><br>".mysql_error());

		if($rowValFinProcedimiento = mysql_fetch_array($resValFinProcedimiento))
		{
			if($rowValFinProcedimiento['Logeir'] != 'on')
			{				
				if($rowValFinProcedimiento['Logpss'] == "on"){
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "Este paciente sali&oacute; de un procedimiento sin sedaci&oacute;n no requiere inicio de recuperaci&oacute;n."; 
				}else{
								
					if($rowValFinProcedimiento['Logefp'] == "on" || $rowValFinProcedimiento['Logcps'] == "on")
					{
						$usuario = explode("-", $_SESSION['user']);
						$wuse = $usuario[1];
						$sqlActRecuperacion = " UPDATE " . $solucionCitas ."_000023
														SET
															Logfir          =   '".date('Y-m-d')."',
															Loghir          =   '".date('H:i:s')."',
															Logeir          =   'on',
															Logubr          =   '".$ubicacion."'
														WHERE   Logtur      =   '" . $rowValFinProcedimiento['Turno'] . "'
																AND Logdoc  =   '" . $rowIdCE09['cedula'] . "'
						";

						$resActRecuperacion = mysql_query( $sqlActRecuperacion, $conex ) or die( mysql_errno()." - Error en el query $sqlActRecuperacion - ".mysql_error() );

						$turnoExplode = explode("-", $rowValFinProcedimiento['Turno']);
						$turnoLlamado = $turnoExplode[1];
						$respuesta['Error'] 	= false;
						$respuesta['Mensaje'] 	= "Paciente con turno " . $turnoLlamado . " finaliz&oacute; procedimiento.";
						$respuesta['Estado'] 	= "iniciarRecuperacion";

						// --> Si ha ocurrido un error guardando el turno
						if(!$resActRecuperacion)
						{
							$respuesta['Error'] 	= true;
							$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error al finalizar el procedimiento del paciente.<br>Por favor contacte al personal de soporte.</span><br>
														<span style='font-size:10px'>($sqlActRecuperacion: ".mysql_error().')</span>';
						}
					}else{
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= "No es posible iniciar la recuperaci&oacute;n. <br> Verifique que le hayan dado finalizar al procedimiento o si no le hicieron el procedimiento deben haber cancelado la atenció&oacute;n";
					}
				}
			} else {
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No se ha podido realizar el inicio de la recuperaci&oacute;n del paciente.<br>El paciente ya se encuentra en recuperaci&oacute;n.";
			}
		}
		else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el inicio de la recuperaci&oacute;n del paciente.<br>Verifique que ya se finaliz&oacute; el procedimiento de este o si no le hicieron el procedimiento deben haber cancelado la atenció&oacute;n.";
		}
	} else {
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para el inicio de la recuperaci&oacute;n del paciente.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//para indicar el fin de la recuperación del paciente.
else if (isset($accion) and $accion == 'finRecuperacion') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idFinRecuperacion);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
						FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idFila . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		$sqlValInicioRecuperacion = "SELECT Logtur AS Turno, Logdoc AS Documento, Logels estLlamadoResult, Logeas finLlamadoResult, Logerf, Logcps
									FROM " . $solucionCitas ."_000023
									WHERE Fecha_data	=	'" . date('Y-m-d') . "'
										AND Hora_data	!=	'00:00:00'
										AND Logfir		=	'" . date('Y-m-d') . "'
										AND Loghir		!=	'00:00:00'
										AND Logeir		=	'on'
										AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
										AND Logest      =   'on'
									ORDER BY Turno DESC;";

		$resValInicioRecuperacion = mysql_query($sqlValInicioRecuperacion, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValInicioRecuperacion):</b><br>".mysql_error());

		if($rowValInicioRecuperacion = mysql_fetch_array($resValInicioRecuperacion)) {
			
			//Se valida si ya se hizo la entrega de resultados sino no permite terminar la recuperacion
			if(($rowValInicioRecuperacion["Logerf"] == "on") || ($rowValInicioRecuperacion["Logcps"] == "on")){
				
				$usuario = explode("-", $_SESSION['user']);
				$wuse = $usuario[1];
				$sqlActFinRecuperacion = " UPDATE " . $solucionCitas ."_000023
												SET
													Logffr			=	'".date('Y-m-d')."',
													Loghfr			=	'".date('H:i:s')."',
													Logefr			=	'on',
													Logure			=	'".$wuse."'
												WHERE	Logtur		=	'" . $rowValInicioRecuperacion['Turno'] . "'
														AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'
														AND Fecha_data = '".date("Y-m-d")."'
				";
														

				$resActProcedimiento = mysql_query( $sqlActFinRecuperacion, $conex ) or die( mysql_errno()." - Error en el query $sqlActFinRecuperacion - ".mysql_error() );

				// --> Si ha ocurrido un error guardando el turno
				if(!$resActProcedimiento) {
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error al finalizar la recuperaci&oacute;n del paciente.<br>Por favor contacte al personal de soporte.</span><br>
												<span style='font-size:10px'>($sqlActFinRecuperacion: ".mysql_error().')</span>';
				}
			}else{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No se ha podido realizar el fin de la recuperaci&oacute;n del paciente.<br>Verifique que ya se realiz&oacute; la entrega de resultados o que se haya hecho la cancelaci&oacute;n del procedimiento.";
			}
		} else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el fin de la recuperaci&oacute;n del paciente.<br>Verifique que ya se inici&oacute; la recuperaci&oacute;n y se finaliz&oacute el llamado del acompañante..";
		}
	} else {
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para el fin de la recuperaci&oacute;n del paciente.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//para indicar la llamada del acompañante del paciente a la sala de recuperación.
else if (isset($accion) and $accion == 'llamadoAcompananteRecuperacion') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idFinRecuperacion);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
						FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		// --> Validar si ya existe un turno asignado hoy, para el documento
		// ESTE QUERY ES PARA PROBAR
		$sqlValFinRecuperacion = " SELECT Logtur AS Turno, Logdoc AS Documento
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Hora_data	!=	'00:00:00'
							    AND Logffr		=	'" . date('Y-m-d') . "'
							    AND Loghfr		!=	'00:00:00'
							    AND Logefr		=	'on'
							    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest     = 'on'
							  ORDER BY Turno DESC;";

		$resValFinRecuperacion = mysql_query($sqlValFinRecuperacion, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValFinRecuperacion):</b><br>".mysql_error());

		if($rowValFinRecuperacion = mysql_fetch_array($resValFinRecuperacion)) {

			$sqlActLlamadaAcompananteRecuperacion = " UPDATE " . $solucionCitas ."_000023
											SET
												Logflr			=	'".date('Y-m-d')."',
												Loghlr			=	'".date('H:i:s')."',
												Logelr			=	'on'
											WHERE	Logtur		=	'" . $rowValFinRecuperacion['Turno'] . "'
													AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

			$resActProcedimiento = mysql_query( $sqlActLlamadaAcompananteRecuperacion, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadaAcompananteRecuperacion - ".mysql_error() );

			$turnoExplode = explode("-", $rowValInicioRecuperacion['Turno']);
			$turnoLlamado = $turnoExplode[1];
			$respuesta['Error'] 	= false;
			$respuesta['Mensaje'] 	= "El acompañante del paciente con turno " . $turnoLlamado . ", por favor pasar a la sala de recuperaci&ocute;n.";

			// --> Si ha ocurrido un error guardando el turno
			if(!$resActProcedimiento)
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error al finalizar la recuperaci&oacute;n del paciente.<br>Por favor contacte al personal de soporte.</span><br>
											<span style='font-size:10px'>($sqlActLlamadaAcompananteRecuperacion: ".mysql_error().')</span>';
			}
		}
		else
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el fin de la recuperaci&oacute;n del paciente.<br>Verifique que ya se finaliz&oacute; la recuperaci&oacute;n de este.";
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para el llamado al acompa&ntilde;ante del paciente.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-29
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del llamado a la entrega de la biopsia.
else if (isset($accion) and $accion == 'llamarPacienteBiopsia') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idLlamadoBiopsia);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
					FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		$sqlValLlamadoBiopsia = "SELECT Logtur AS Turno, Logdoc AS Documento, Logelb, Logelr, Logear, Logels, Logeas, Logpss, Logcps, Logeir
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
								AND Hora_data	!=	'00:00:00'								
								AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest      = 'on'
							  ORDER BY Turno DESC;";

		$resValLlamadoBiopsia = mysql_query($sqlValLlamadoBiopsia, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValLlamadoBiopsia):</b><br>".mysql_error());

		if($rowResultados = mysql_fetch_array($resValLlamadoBiopsia)) {
			if($rowResultados['Logcps'] == "on" ){
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "A este paciente no se le realiz&oacute; procedimiento";
			} else {
				//Primero se valida que no se esté llamando en ese momento al acompañante a recuperacion
				if($rowResultados['Logear'] == "on" && $rowResultados['Logelr'] == "off"){					
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "No es posible realizar el llamado.<br>En este momento se est&aacute; llamando el acompa&ntilde;ante a la sala de recuperaci&oacute;n.";
				} else if($rowResultados['Logels'] == "on" && $rowResultados['Logeas'] == "off"){					
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "No es posible realizar el llamado.<br>En este momento se est&aacute; llamando el acompa&ntilde;ante para la entrega de resultados";
				} else {	

					if(($rowResultados['Logeir'] == "on") || ($rowResultados['Logpss'] == "on")){	
						$sqlActLlamadoBiopsia = " UPDATE " . $solucionCitas ."_000023
														SET
															Logflb			=	'".date('Y-m-d')."',
															Loghlb			=	'".date('H:i:s')."',
															Logelb			=	'on',
															Logubb			=	'" . $ubicacion . "',
															Logeab          =   'off'
														WHERE	
															Logtur		=	'" . $rowResultados['Turno'] . "'
															AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

						$resActLlamadoAdm = mysql_query( $sqlActLlamadoBiopsia, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadoBiopsia - ".mysql_error() );

						$turnoExplode = explode("-", $rowResultados['Turno']);
						$turnoLlamado = $turnoExplode[1];
						
						if(!$resActLlamadoAdm) {
							$respuesta['Error'] 	= true;
							$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error llamando al paciente para la entrega de resultados de la Biopsia.<br>Por favor contacte al personal de soporte.</span><br>
														<span style='font-size:10px'>($sqlActLlamadoBiopsia: ".mysql_error().')</span>';
						}
					}else{
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= "No es posible llamar para entrega de biopsia. <br>El paciente debe estar en recuperaci&oacute;n o si el procedimiento fue sin sedaci&oacute;n deben darle de alta desde la sala de procedimiento.";
					}	
				}
			}				
		} else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el llamado del paciente.<br>Verifique que el paciente ya se encuentre en la sala de recuperaci&oacute;n.";
		}
	} else {
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para el llamado del paciente a reclamar la biopsia.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del apagado al llamado a la entrega de la biopsia.
else if (isset($accion) and $accion == 'apagarLlamarPacienteBiopsia') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idApagarLlamadoBiopsia);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
						FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		$sqlValApagarLlamadoBiopsia = " SELECT Logtur AS Turno, Logdoc AS Documento
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Hora_data	!=	'00:00:00'
							    AND Logflb		=	'" . date('Y-m-d') . "'
							    AND Loghlb		!=	'00:00:00'
							    AND Logelb		=	'on'
							    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest     = 'on'
							  ORDER BY Turno DESC;";

		$resValApagarLlamadoBiopsia = mysql_query($sqlValApagarLlamadoBiopsia, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValApagarLlamadoBiopsia):</b><br>".mysql_error());

		if($rowValApagarLlamadoBiopsia = mysql_fetch_array($resValApagarLlamadoBiopsia))
		{
			$usuario = explode("-", $_SESSION['user']);
			$wuse = $usuario[1];
			$sqlActApagarLlamadoBiopsia = " UPDATE " . $solucionCitas ."_000023
										SET
											Logfab			=	'".date('Y-m-d')."',
											Loghab			=	'".date('H:i:s')."',
											Logeab			=	'on',
											Logueb			=	'".$wuse."'
										WHERE	Logtur		=	'" . $rowValApagarLlamadoBiopsia['Turno'] . "'
												AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

			$resActLlamadoAdm = mysql_query( $sqlActApagarLlamadoBiopsia, $conex ) or die( mysql_errno()." - Error en el query $sqlActApagarLlamadoBiopsia - ".mysql_error() );

			// --> Si ha ocurrido un error guardando el turno
			if(!$resActLlamadoAdm)
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error apagando el llamado al paciente.<br>Por favor contacte al personal de soporte.</span><br>
											<span style='font-size:10px'>($sqlActApagarLlamadoBiopsia: ".mysql_error().')</span>';
			}
		}
		else
			
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el apagado al llamado del paciente.<br>Verifique que ya se realiz&oacute; el llamado de este.";
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para apagar el llamado del paciente a reclamar la biopsia.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-29
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del llamado a la entrega de la biopsia.
else if (isset($accion) and $accion == 'llamarPacienteResultados') {

	

	

	
	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idLlamadoResultados);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
							FROM " . $solucionCitas . "_000009
						WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		$sqlValLlamadoResultados = " SELECT 
										Logtur AS Turno, Logdoc AS Documento, Logels, Logear, Logelr, Logeir, Logpss, Logcps
									FROM " . $solucionCitas ."_000023
									WHERE Fecha_data	=	'" . date('Y-m-d') . "'
									AND Hora_data	!=	'00:00:00'
									AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
									AND Logest      = 'on'
									ORDER BY Turno DESC;";

		$resValLlamadoResultados = mysql_query($sqlValLlamadoResultados, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValLlamadoResultados):</b><br>".mysql_error());

		if($rowResultados = mysql_fetch_array($resValLlamadoResultados))
		{
			if($rowResultados['Logcps'] == "on"){
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "A este paciente no se le realiz&oacute; procedimiento.";
			}else{
					//Primero se valida que no se esté llamando en ese momento al acompañante a recuperacion
				if($rowResultados['Logear'] != "off" && $rowResultados['Logelr'] != "on"){					
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "No es posible realizar el llamado.<br>En este momento se est&aacute; llamando el acompa&ntilde;ante a la sala de recuperaci&oacute;n.";
				}else{
					
					//Se debe validar si el paciente esta en recuperacion o es del caso del que salió de alta desde procedimiento
					if($rowResultados['Logeir'] == "on" || $rowResultados['Logpss'] == "on"){						
										
						$sqlActLlamadoResultados = " UPDATE " . $solucionCitas ."_000023
															SET
																Logfls			=	'".date('Y-m-d')."',
																Loghls			=	'".date('H:i:s')."',
																Logels			=	'on',
																Logubs			=	'" . $ubicacion . "',
																Logeas          =   'off'
															WHERE	Logtur		=	'" . $rowResultados['Turno'] . "'
																	AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

						$resActLlamadoAdm = mysql_query( $sqlActLlamadoResultados, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadoResultados - ".mysql_error() );

						$turnoExplode = explode("-", $rowResultados['Turno']);
						$turnoLlamado = $turnoExplode[1];
						
						if(!$resActLlamadoAdm)
						{
							$respuesta['Error'] 	= true;
							$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error llamando al paciente para la entrega de Resultados.<br>Por favor contacte al personal de soporte.</span><br>
														<span style='font-size:10px'>($sqlActLlamadoResultados: ".mysql_error().')</span>';
						}
					}else{
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= "No es posible llamar para entrega de resultados. <br>El paciente debe estar en recuperaci&oacute;n o si el procedimiento fue sin sedaci&oacute;n deben darle de alta desde la sala de procedimiento.";
					}				
				}	
			}	
		} else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el llamado del paciente.<br>Verifique que tenga un turno.";
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para el llamado del paciente a reclamar los resultados.<br>Verifique que se encuentre agendado.";
	}
		
	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-15
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//de los campos del apagado al llamado a la entrega de la biopsia.
else if (isset($accion) and $accion == 'apagarLlamarPacienteResultados') {

	

	

	$horaAten = date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	$idcitasen9 = explode("_", $idApagarLlamadoResultados);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
					FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		$sqlValApagarLlamadoResultados = " SELECT Logtur AS Turno, Logdoc AS Documento
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Hora_data	!=	'00:00:00'
							    AND Logfls		=	'" . date('Y-m-d') . "'
							    AND Loghls		!=	'00:00:00'
							    AND Logels		=	'on'
							    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest     = 'on'
							  ORDER BY Turno DESC;";

		$resValApagarLlamadoResultados = mysql_query($sqlValApagarLlamadoResultados, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValApagarLlamadoResultados):</b><br>".mysql_error());

		if($rowValApagarLlamadoResultados = mysql_fetch_array($resValApagarLlamadoResultados))
		{
			$usuario = explode("-", $_SESSION['user']);
			$wuse = $usuario[1];
			$sqlActApagarLlamadoResultados = " UPDATE " . $solucionCitas ."_000023
										SET
											Logfas			=	'".date('Y-m-d')."',
											Loghas			=	'".date('H:i:s')."',
											Logeas			=	'on',
											Logues			=	'".$wuse."'
										WHERE	Logtur		=	'" . $rowValApagarLlamadoResultados['Turno'] . "'
										AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

			$resActLlamadoAdm = mysql_query( $sqlActApagarLlamadoResultados, $conex ) or die( mysql_errno()." - Error en el query $sqlActApagarLlamadoResultados - ".mysql_error() );

			// --> Si ha ocurrido un error guardando el turno
			if(!$resActLlamadoAdm) {
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error apagando el llamado al paciente.<br>Por favor contacte al personal de soporte.</span><br>
											<span style='font-size:10px'>($sqlActApagarLlamadoResultados: ".mysql_error().')</span>';
			}
		} else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el apagado al llamado del paciente.<br>Verifique que ya se realiz&oacute; el llamado de este.";
		}
	} else {
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para apagar el llamado del paciente a reclamar los resultados.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}


//Fecha creación: 2016-02-16
//Autor: Eimer Castro
//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
//para indicar la finalización de la atención al paciente.
else if (isset($accion) and $accion == 'terminarAtencion') {

	

	

	$horaAten = date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '', 'msjCamillero' => '');

	$idcitasen9 = explode("_", $idTerminarAtencion);
	$idce9 = $idcitasen9[1];

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula, Nom_pac 
					FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idce9 . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		// --> Validar si ya existe un turno asignado hoy, para el documento
		// ESTE QUERY ES PARA PROBAR
		$sqlValExisteTurno = " SELECT Logtur AS Turno, Logdoc AS Documento, Logeas estadoResultados, Logefr finRecuperacion, Logcps
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Logest		=	'on'
							    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest      = 'on'
							  ORDER BY Turno DESC;";
		
		$resValExisteTurno = mysql_query($sqlValExisteTurno, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValExisteTurno):</b><br>".mysql_error());

		if($rowValExisteTurno = mysql_fetch_array($resValExisteTurno))
		{			
			//Se debe validar que ya haya terminado la entrega de resultados y se haya finalizado la recuperación
			if((($rowValExisteTurno['estadoResultados'] == "on") || ($rowValExisteTurno['Logcps'] == "on")) && $rowValExisteTurno['finRecuperacion'] != "off"){
				$usuario = explode("-", $_SESSION['user']);
				$wuse = $usuario[1];

				$sqlActTerminarAtencion = " UPDATE " . $solucionCitas ."_000023
											SET
												Logfta			=	'".date('Y-m-d')."',
												Loghta			=	'".date('H:i:s')."',
												Logeta			=	'on',
												Loguta			=	'".$wuse."'
											WHERE	Logtur		=	'" . $rowValExisteTurno['Turno'] . "'
												AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

				$resActProcedimiento = mysql_query( $sqlActTerminarAtencion, $conex ) or die( mysql_errno()." - Error en el query $sqlActTerminarAtencion - ".mysql_error() );

				// --> Si ha ocurrido un error guardando el turno
				if(!$resActProcedimiento)
				{
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error al finalizar la atención del paciente.<br>Por favor contacte al personal de soporte.</span><br>
											   <span style='font-size:10px'>($sqlActTerminarAtencion: ".mysql_error().')</span>';
				}else{
					//Como ya se termino la atención se llama la función para la solicitud de camillero
					$nombrePaciente = isset($rowIdCE09['Nom_pac']) ? $rowIdCE09['Nom_pac'] : "";
					$solicitudCamillero = solicitarCamillero($centroCosto, $empresa, $nombrePaciente);
										
					if($solicitudCamillero != 0){
						
						//Se debe guardar el id de la solicitud de camillero en la tabla en el campo logisc 
						$sqlUpdateCamillero = " UPDATE " . $solucionCitas ."_000023
											SET
												Logisc		=	'".$solicitudCamillero."'												
											WHERE	
												Logtur		=	'" . $rowValExisteTurno['Turno'] . "'
												AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

						$resUpdateCamillero = mysql_query( $sqlUpdateCamillero, $conex ) or die( mysql_errno()." - Error en el query $sqlUpdateCamillero - ".mysql_error() );
						
						$respuesta['msjCamillero'] 	= "Se ha iniciado el proceso de alta. <br> Recuerde que la solicitud del camillero se ha realizado autom&aacute;ticamente por el sistema.";
					}else{
						$respuesta['msjCamillero'] 	= "No fue posible realizar la solicitud del camillero.";
					}
				}
			}else{				
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No es posible terminar la atención, por favor verifique que ya se haya dado por finalizada la recueración y se haya hecho la entrega de resultados.";
			}
		} else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar la finalizació de la atenci&oacute;n del paciente.";
		}		
	} else {
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar la validaci&oacute;n para terminar la atenci&oacute;n del paciente.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-02-16
//Autor: Eimer Castro
//Esta función realiza el post para realizar el envío del array de las relaciones de monitores y procesos.
else if (isset($accion) and $accion == 'crearArrayPerfilxProceso') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '', 'Arreglo' => '');

	//Consulta para traer la relación de monitores con procesos.
    $query_mon_pro = "SELECT CE24.Mondes, CE25.Prodes, CE26.Rpmmon, CE26.Rpmpro
                      FROM " . $solucionCitas ."_000024 AS CE24
                      INNER JOIN " . $solucionCitas ."_000026 AS CE26 ON CE24.Moncod = CE26.Rpmmon
                      INNER JOIN " . $solucionCitas ."_000025 AS CE25 ON CE25.Procod = CE26.Rpmpro
                      WHERE CE26.Rpmmon IN ('" . $monitor . "', '*');";
					
    $resQuery_mon_pro = mysql_query($query_mon_pro) or die(mysql_errno() - "Eror en el query " . $query_mon_pro . " - " . mysql_error());

	if($rowQuery_mon_pro = mysql_num_rows($resQuery_mon_pro))
	{
		$arrayRlnMonitorProceso = array();
        for($i = 0; $rows = mysql_fetch_array($resQuery_mon_pro); $i++) {

            if(!array_key_exists($rows[2], $arrayRlnMonitorProceso))
            {
                $arrayRlnMonitorProceso[$rows[2]] = array();
                array_push($arrayRlnMonitorProceso[$rows[2]], $rows[3]);
            }
            else
            {
                array_push($arrayRlnMonitorProceso[$rows[2]], $rows[3]);
            }
        }
       
        $respuesta['Arreglo'] 	= $arrayRlnMonitorProceso;

		// --> Si ha ocurrido un error guardando el turno
		if(!$resQuery_mon_pro)
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error al mostrar las columnas seg&uacute;n el perfil.<br>Por favor contacte al personal de soporte.</span><br>
										<span style='font-size:10px'>($resQuery_mon_pro: ".mysql_error().')</span>';
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido mostrar las columnas de los procesos.";
	}

	echo json_encode($respuesta);
	return;
}

//Fecha creación: 2016-03-02
//Autor: Eimer Castro
//Esta función realiza el post para realizar el envío del array de las relaciones de monitores y procesos.
else if (isset($accion) and $accion == 'crearArrayPerfilxUbicacion') {

	

	

	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '', 'Arreglo' => '', 'Ubicaciones' => '');

	$opcionesUbicaciones = "<option id='ubicacion_00' value='00'>Seleccione una ubicaci&oacute;n...</option>";

	//Consulta para traer la relación de monitores con ubicaciones.
    $queryUbicaciones = "SELECT CE24.Mondes, CE27.Ubides, CE27.Ubicod, CE28.Rummon, CE28.Rumubi
						FROM " . $solucionCitas ."_000024 AS CE24
						INNER JOIN " . $solucionCitas ."_000028 AS CE28 ON CE24.Moncod = CE28.Rummon
						INNER JOIN " . $solucionCitas ."_000027 AS CE27 ON CE27.Ubicod = CE28.Rumubi
						WHERE CE28.Rummon = '" . $monitor ."';";

    $resQueryUbicaciones = mysql_query($queryUbicaciones) or die(mysql_errno() - "Eror en el query " . $queryUbicaciones . " - " . mysql_error());

	if($rowQueryUbicaciones = mysql_num_rows($resQueryUbicaciones))
	{
		$arrayRlnMonitorUbicacion = array();
        for($i = 0; $rows = mysql_fetch_array($resQueryUbicaciones); $i++) {

        	$ubicacionSelected = "";
            if(!array_key_exists($rows[2], $arrayRlnMonitorUbicacion))
            {
            	if(isset($ubicacionSeleccionada) && $rows['Ubicod'] == $ubicacionSeleccionada)
				{
					$ubicacionSelected = "selected='selected'";
				}
                $arrayRlnMonitorUbicacion[$rows[2]] = array();
                array_push($arrayRlnMonitorUbicacion[$rows[2]], $rows[3]);
            	$opcionesUbicaciones .= "<option " . $ubicacionSelected . "id='ubicacion_" . $rows['Ubicod'] . "' value='" . $rows['Ubicod'] . "'>" . $rows['Ubicod'] . " - " . utf8_encode($rows['Ubides']) . "</option>";
            }
            else
            {
            	if(isset($ubicacionSeleccionada) && $rows['Ubicod'] == $ubicacionSeleccionada)
				{
					$ubicacionSelected = "selected='selected'";
				}
                array_push($arrayRlnMonitorUbicacion[$rows[2]], $rows[3]);
            	$opcionesUbicaciones .= "<option " . $ubicacionSelected . "id='ubicacion_" . $rows['Ubicod'] . "' value='" . $rows['Ubicod'] . "'>" . $rows['Ubicod'] . " - " . utf8_encode($rows['Ubides']) . "</option>";
            }
        }
      
        $respuesta['Arreglo']				=	$arrayRlnMonitorUbicacion;
        $respuesta['Ubicaciones']		=	$opcionesUbicaciones;

		// --> Si ha ocurrido un error guardando el turno
		if(!$resQueryUbicaciones)
		{
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error al mostrar las ubicaciones seg&uacute;n el perfil.<br>Por favor contacte al personal de soporte.</span><br>
										<span style='font-size:10px'>($resQueryUbicaciones: ".mysql_error().')</span>';
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido mostrar las columnas de los procesos.";
	}

	echo json_encode($respuesta);
	return;
}

// --> 	Llama la funcion que lista los pacientes con turno
// 		Eimer Castro, 2016-02-18.
if (isset($accion) and $accion == 'listarPacientesConTurno'){

	$respuesta = array('Error' => false, 'Mensaje' => '', 'html' => '', 'FechaCitas' => '');

	

	

	global $conex;
	global $solucionCitas;

	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];

	$respuesta['html'] = " <br><div style='overflow:auto;background:none repeat scroll 0 0;'>
								<table id='tablaListaTurnos' align='center' id='tablaPacTurnos'>
									<tr align='center'>
										<td class='encabezadoTabla' colspan='7'>PACIENTES CON TURNO Y SIN CITA</td>
									</tr>
									<tr align='center' class='trTurno'>
										<td class='encabezadoTabla'>Fecha</td>
										<td class='encabezadoTabla'>Hora</td>
										<td class='encabezadoTabla'>Turno</td>
										<td class='encabezadoTabla'>Documento</td>
										<td class='encabezadoTabla'>Nombre</td>
										<td class='encabezadoTabla' colspan='3' align='center'>Opciones</td>
									</tr>";

	$sqlTurnos = " SELECT CE23.Fecha_data, CE23.Hora_data, CE23.Logtur, CE23.Logtip, CE23.Logdoc, CE23.Logfla, CE23.Logela, CE23.id, RT36.Pacced, RT36.Pactid, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2, ' ') AS nombreSinCita
						FROM ".$solucionCitas."_000023 AS CE23
							LEFT JOIN ".$solucionCitas."_000009 AS CE09 ON (CE23.Logdoc = replace( replace( CE09.cedula, '\t', '' ) , ' ', '' ) AND CE23.Logfau = Fecha)
							LEFT JOIN root_000036 AS RT36 ON (CE23.Logdoc = RT36.Pacced AND CE23.Logtip = RT36.Pactid)
					WHERE	CE23.Fecha_data = '" . date("Y-m-d") . "'
						AND CE23.Logest	=	'on'
						AND CE23.Logeau	=	'on'
						AND CE23.Logeia	!=	'on'
						AND CE23.Logefa	!=	'on'
						AND CE09.Cedula	IS NULL
					 ORDER BY Logtur ASC;";

	$resTurnos 	= mysql_query($sqlTurnos) or die("<b>ERROR EN QUERY MATRIX($sqlTurnos):</b><br>".mysql_error());
	$coloFila	= 'fila2';
	$turnoConLlamadoEnVentanilla = '';
	if($rowTurnos = mysql_num_rows($resTurnos))
	{
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			$coloFila 	= (($coloFila == 'fila2') ? 'fila1' : 'fila2');

			$nomPaciente = "";//obtenerNombrePaciente($rowTurnos['Logtip'], $rowTurnos['Logdoc']);

			// --> El turno ya tiene llamado a la ventanilla.
			$tieneLlamado = (($rowTurnos['Logela'] == 'on') ? TRUE : FALSE);
			$blinkFila = "";
			if($tieneLlamado)
			{
				$turnoConLlamadoEnVentanilla = $rowTurnos['Logtur'];
				$blinkFila = "blink='true' class='blinkProcesoActual'";
			}

			$respuesta['html'] .= "<tr class='".$coloFila." find filaDraggable' id='trTurno_".$rowTurnos['Logtur']."'>
										<td style='padding:2px;' >".$rowTurnos['Fecha_data']."</td>
										<td style='padding:2px;' >".$rowTurnos['Hora_data']."</td>
										<td style='padding:2px;' align='center' id='tdrTurno_".$rowTurnos['Logtur']."' turno='".$rowTurnos['Logtur']."' nombre='' " . $blinkFila . "><b>".substr($rowTurnos['Logtur'], 7)."</b></td>
										<td style='padding:2px;' align='center' id='tdrCedula_".$rowTurnos['Logdoc']."' cedula='".$rowTurnos['Logdoc']."'>".$rowTurnos['Logtip']."-".$rowTurnos['Logdoc']."</td>
										<td style='padding:2px;' align='center' id='tdrNombre_".$rowTurnos['Logdoc']."' nombre='".$rowTurnos['nombreSinCita']."'>".$rowTurnos['nombreSinCita']."</td>
										<td style='padding:2px;' align='center' id='tdOpcionesTurnoSinCita_".$rowTurnos['id']."' turno='".$rowTurnos['Logtur']."' cedula='".$rowTurnos['Logdoc']."'>
											<img id='rdLlamadoAdmision_".$rowTurnos['id']."' style='cursor:pointer;' class='botonLlamarPacienteAdmisionSinCita' width='20' heigth='20' tooltip='Llamar para admisi&oacute;n' title='Llamar para admisi&oacute;n' src='../../images/medical/root/Call2.png' onclick='llamarPacienteAdmisionSinCita(\"rdLlamadoAdmision_".$rowTurnos['id']."\",\"".$rowTurnos['Logdoc']."\",\"\",\"".$solucionCitas."\", consultarUbicacion())';this.onclick=\"\"'>
											<img id='rdApagadoLlamadoAdmisionSinCita_".$rowTurnos['id']."' style='cursor:pointer;' class='botonApagarLlamarPacienteAdmisionSinCita' width='20' heigth='20' tooltip='Apagar llamado para admisi&oacute;n' title='Apagar llamado para admisi&oacute;n' src='../../images/medical/root/Call3.png' onclick='apagarLlamarPacienteAdmisionSinCita(\"rdApagadoLlamadoAdmisionSinCita_".$rowTurnos['id']."\",\"".$rowTurnos['Logdoc']."\",\"\",\"".$solucionCitas."\", consultarUbicacion())';this.onclick=\"\"'>
											<a onclick='javascript:abrirVentanCitas(\"$solucionCitas\",\"$wemp_pmla\",\"$caso\",\"$wsw\",\"$fest\")'>
												<img id='rdAsignarCita_".$rowTurnos['id']."' style='cursor:pointer;'  class='botonAsignarCita' width='20' heigth='20' tooltip='Asignar Sala' title='Asignar Sala' src='../../images/medical/hce/hceQ.png'>
											</a>
											<img id='rdCancelarCita_".$rowTurnos['id']."' style='cursor:pointer;' width='20' heigth='20' title='Anular turno' src='../../images/medical/citas/cancelar.PNG' onClick='cancelarTurno(\"".$rowTurnos['Logtur']."\")'>
										</td>
									</tr>
			";
		}

		$respuesta['html'] .= "</table>
									<input type='hidden' id='turnoLlamadoPorEsteUsuario' value='".$turnoConLlamadoEnVentanilla."'>
								</div>";

		$respuesta['FechaCitas'] = date('Y-m-d');
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No hay turnos asignados sin citas agendadas.";
	}

	echo json_encode($respuesta);
	return;
}

// --> 	Llama la funcion que lista los pacientes con turno y en agenda
// 		Eimer Castro, 2016-03-10.
if (isset($accion) and $accion == 'mostrarPacientesConCita'){

	$respuesta = array('Error' => false, 'Mensaje' => '', 'ArrayTurnosAgenda' => '', 'actualizarMonitor' => false, 'msjActualizarMonitor' => '', 'sqlTurnos' => '' );

	

	

	global $conex;
	global $solucionCitas;

	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];
	
	//Se crea el array de ubicaciones para indicar en que lugar se hace cada proceso	
	$arrayUbicaciones = crearArrayPerfilxUbicacion($solucionCitas);		

	$arrayTurnosAgenda = array();
	
	$arrayUbicacionProceso = array();
	
	$sqlTurnos = " SELECT CE23.Fecha_data, CE23.Hora_data, CE23.Logtur, CE23.Logtip, CE23.Logdoc, CE23.id,
							CE23.Logeau, CE23.Logela, CE23.Logeia, CE23.Logefa, CE23.Logelt, CE23.Logeat,
							CE23.Logeip, CE23.Logefp, CE23.Logeir, CE23.Logefr, CE23.Logelr, CE23.Logelb,
							CE23.Logeab, CE23.Logels, CE23.Logeas, CE23.Logeta, CE09.id AS id09
							, CE23.loguba, CE23.logubt,CE23.logubp, CE23.logubr, CE23.logubb, CE23.logubs 
							, CE23.Logfua, CE23.Loghua, CE23.Logeua, CE23.Logisc, CE23.Logebf , CE23.Logerf, CE23.Logeaf
							, CE23.Logerp, CE23.Logepf, CE23.Logpss, CE23.Logcps, CE23.Logcdc, CE23.Loguam
					FROM ".$solucionCitas."_000023 AS CE23
							INNER JOIN ".$solucionCitas."_000009 AS CE09 ON (CE23.logdoc = replace( replace( CE09.cedula, '\t', '' ) , ' ', '' ) AND CE23.Fecha_data = CE09.Fecha)
					WHERE	CE23.Fecha_data = '" . date("Y-m-d") . "'
			
			AND CE23.Logest	=	'on'
					AND CE09.Activo = 'A'
					ORDER BY Logtur ASC;";

	$resTurnos 	= mysql_query($sqlTurnos) or die("<b>ERROR EN QUERY MATRIX($sqlTurnos):</b><br>".mysql_error());

	if($rowTurnos = mysql_num_rows($resTurnos))
	{
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{			
			//Sólo para admisión para saber si ya se empezó
			if($rowTurnos['Logeia'] != 'off' && $rowTurnos['Logefa'] != 'on'){
					$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EmpezoAdmision']						= 1;
			} else {
					$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EmpezoAdmision']						= 0;
			}
							
			if($rowTurnos['Logcdc'] == 'on'){				
				if (strpos($rowTurnos['Loguam'], $ubicacion) === false) {
					$respuesta["actualizarMonitor"] = true;
					$respuesta["msjActualizarMonitor"] = "Se han modificado los datos de un paciente. Para actualizar la informaci&oacute;n debe recargar la p&aacute;gina dando clic en el siguiente bot&oacute;n ";
				}
			}			
			
			//se arma el array de ubicaciones
			$arrayUbicacionProceso[$rowTurnos['Logdoc']]['loguba'] = utf8_encode($arrayUbicaciones["01"][$rowTurnos['loguba']]); //Admision
			$arrayUbicacionProceso[$rowTurnos['Logdoc']]['logubt'] = utf8_encode($arrayUbicaciones["02"][$rowTurnos['logubt']]); //preparacion
			$arrayUbicacionProceso[$rowTurnos['Logdoc']]['logubp'] = utf8_encode($arrayUbicaciones["03"][$rowTurnos['logubp']]); //procedimiento
			$arrayUbicacionProceso[$rowTurnos['Logdoc']]['logubr'] = utf8_encode($arrayUbicaciones["04"][$rowTurnos['logubr']]); //recuperacion
			$arrayUbicacionProceso[$rowTurnos['Logdoc']]['logubb'] = utf8_encode($arrayUbicaciones["01"][$rowTurnos['logubb']]); //biopsia
			$arrayUbicacionProceso[$rowTurnos['Logdoc']]['logubs'] = utf8_encode($arrayUbicaciones["01"][$rowTurnos['logubs']]);	//resultados
									
			if(!array_key_exists($rowTurnos['Logdoc'], $arrayTurnosAgenda))
			{
				$arrayTurnosAgenda[$rowTurnos['Logdoc']] = array();
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['TurnoAutorizacion']						= $rowTurnos['Logtur'];
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['id09']									= $rowTurnos['id09'];

				$arrayAutorizacion = array($rowTurnos['Logeau']);
				$estadoAutorizacion = verificarEstadoProceso($arrayAutorizacion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAutorizacion']					= $estadoAutorizacion;

				$arrayAdmision = array($rowTurnos['Logela'], $rowTurnos['Logeia'], $rowTurnos['Logefa']);
				$estadoAdmision = verificarEstadoProceso($arrayAdmision);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAdmision']							= $estadoAdmision;
				
				//una opcion nueva para validar la finalizacion total del proceso de admision
				$arrayFinalizaAdmision = array($rowTurnos['Logeua']);
				$EstadoAdmisionFinalizada = verificarEstadoProceso($arrayFinalizaAdmision);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAdmisionFinalizada']				= $EstadoAdmisionFinalizada;				
				
				$arrayAtencion = array($rowTurnos['Logelt'], $rowTurnos['Logeat']);
				$estadoAtencion = verificarEstadoProceso($arrayAtencion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAtencion']							= $estadoAtencion;

				//una opcion nueva para validar la finalizacion total del proceso de preparación
				$arrayFinalizaAtencion = array($rowTurnos['Logeaf']);
				$EstadoAtencionFinalizada = verificarEstadoProceso($arrayFinalizaAtencion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAtencionFinalizada']				= $EstadoAtencionFinalizada;	
								
				$arrayProcedimiento = array($rowTurnos['Logeip'], $rowTurnos['Logefp']);
				$estadoProcedimiento = verificarEstadoProceso($arrayProcedimiento);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoProcedimiento']						= $estadoProcedimiento;
				
				//para validar si tiene segundo procedimiento
				if($rowTurnos['Logerp'] == "on"){
					if($rowTurnos['Logepf'] == "off"){
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoSegundoProcedimiento']		= 0;
					}					
					if($rowTurnos['Logepf'] == "on"){
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoSegundoProcedimiento']		= 1;
					}
				}				
				
				//Validar si el paciente fue dado de alta desde procedimiento
				if($rowTurnos['Logpss'] == "on"){				
					
					if($rowTurnos['Logerf'] == "on"){
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoAltaProcimiento']		= 1;	
					}else{
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoAltaProcimiento']		= 0;	
					}								
				}else{
					$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoAltaProcimiento']		= -1;
				}
				
				$arrayRecuperacion = array($rowTurnos['Logeir'], $rowTurnos['Logefr'], $rowTurnos['Logelr']);
				$estadoRecuperacion = verificarEstadoProceso($arrayRecuperacion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoRecuperacion']						= $estadoRecuperacion;
				
				//Validar si el paciente fue sedado pero no se le realizó el procedimiento
				if($rowTurnos['Logcps'] == "on"){		
					$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoPacSinProcedimiento']		= 1;													
				}else{
					$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoPacSinProcedimiento']		= -1;
				}

				
				//ESTADO ENTREGA DE RESULTADOS
				$arrayResultados = array($rowTurnos['Logels'], $rowTurnos['Logeas']);
				$estadoResultados = verificarEstadoProceso($arrayResultados);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoResultados']						= $estadoResultados;
						
				$arrayTerminoAtencion = array($rowTurnos['Logefr'], $rowTurnos['Logeta']);
				$estadoTerminoAtencion = verificarEstadoProceso($arrayTerminoAtencion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoTerminoAtencion']					= $estadoTerminoAtencion;
				
				//ESTADO ENTREGA DE BIOPSIA FINALIZADA
				$arrayFinalizaBiopsia = array($rowTurnos['Logebf']);
				$EstadoBiopsiaFinalizada = verificarEstadoProceso($arrayFinalizaBiopsia);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoBiopsiaFinalizada']					= $EstadoBiopsiaFinalizada;	
				
				//ESTADO ENTREGA DE RESULTADOS FINALIZADA
				$arrayFinalizaResultados = array($rowTurnos['Logerf']);
				$EstadoResultadosFinalizada = verificarEstadoProceso($arrayFinalizaResultados);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoResultadosFinalizada']				= $EstadoResultadosFinalizada;	
			}
			else
			{
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['TurnoAutorizacion']						= $rowTurnos['Logtur'];
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['id09']									= $rowTurnos['id09'];

				$arrayAutorizacion = array($rowTurnos['Logeau']);
				$estadoAutorizacion = verificarEstadoProceso($arrayAutorizacion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAutorizacion']					= $estadoAutorizacion;

				$arrayAdmision = array($rowTurnos['Logela'], $rowTurnos['Logeia'], $rowTurnos['Logefa']);
				$estadoAdmision = verificarEstadoProceso($arrayAdmision);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAdmision']							= $estadoAdmision;

				//una opcion nueva para validar la finalizacion total del proceso de admision
				$arrayFinalizaAdmision = array($rowTurnos['Logeua']);
				$EstadoAdmisionFinalizada = verificarEstadoProceso($arrayFinalizaAdmision);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAdmisionFinalizada']				= $EstadoAdmisionFinalizada;
				
				
				$arrayAtencion = array($rowTurnos['Logelt'], $rowTurnos['Logeat']);
				$estadoAtencion = verificarEstadoProceso($arrayAtencion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAtencion']							= $estadoAtencion;
				
				//una opcion nueva para validar la finalizacion total del proceso de preparación
				$arrayFinalizaAtencion = array($rowTurnos['Logeaf']);
				$EstadoAtencionFinalizada = verificarEstadoProceso($arrayFinalizaAtencion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoAtencionFinalizada']				= $EstadoAtencionFinalizada;	
				
				
				$arrayProcedimiento = array($rowTurnos['Logeip'], $rowTurnos['Logefp']);
				$estadoProcedimiento = verificarEstadoProceso($arrayProcedimiento);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoProcedimiento']						= $estadoProcedimiento;
				
				//para validar si tiene segundo procedimiento
				if($rowTurnos['Logerp'] == "on"){
					if($rowTurnos['Logepf'] == "off"){
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoSegundoProcedimiento']		= 0;
					}					
					if($rowTurnos['Logepf'] == "on"){
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoSegundoProcedimiento']		= 1;
					}
				}			
				
				//Validar si el paciente fue dado de alta desde procedimiento
				if($rowTurnos['Logpss'] == "on"){				
					
					if($rowTurnos['Logerf'] == "on"){
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoAltaProcimiento']		= 1;	
					}else{
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoAltaProcimiento']		= 0;	
					}								
				}else{
					$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoAltaProcimiento']		= -1;
				}
				
				
				$arrayRecuperacion = array($rowTurnos['Logeir'], $rowTurnos['Logefr'], $rowTurnos['Logelr']);
				$estadoRecuperacion = verificarEstadoProceso($arrayRecuperacion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoRecuperacion']						= $estadoRecuperacion;
				
				//Validar si el paciente fue sedado pero no se le realizó el procedimiento
				if($rowTurnos['Logcps'] == "on"){		
					$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoPacSinProcedimiento']		= 1;													
				}else{
					$arrayTurnosAgenda[$rowTurnos['Logdoc']]['estadoPacSinProcedimiento']		= -1;
				}				
				
				$arrayResultados = array($rowTurnos['Logels'], $rowTurnos['Logeas']);
				$estadoResultados = verificarEstadoProceso($arrayResultados);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoResultados']						= $estadoResultados;
				
				
				$arrayTerminoAtencion = array($rowTurnos['Logefr'], $rowTurnos['Logeta']);
				$estadoTerminoAtencion = verificarEstadoProceso($arrayTerminoAtencion);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoTerminoAtencion']					= $estadoTerminoAtencion;
			
				//ESTADO ENTREGA DE BIOPSIA FINALIZADA
				$arrayFinalizaBiopsia = array($rowTurnos['Logebf']);
				$EstadoBiopsiaFinalizada = verificarEstadoProceso($arrayFinalizaBiopsia);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoBiopsiaFinalizada']					= $EstadoBiopsiaFinalizada;	
				
				//ESTADO ENTREGA DE RESULTADOS FINALIZADA
				$arrayFinalizaResultados = array($rowTurnos['Logerf']);
				$EstadoResultadosFinalizada = verificarEstadoProceso($arrayFinalizaResultados);
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoResultadosFinalizada']				= $EstadoResultadosFinalizada;	
				
				
				//Se valida si ya tiene una solicitud de camillero si la tiene se debe consultar el estado en la que se encuentra
				$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoSolicitudCamillero']				= 0;
				if(isset($rowTurnos['Logisc']) && $rowTurnos['Logisc'] != 0){
					
					$estadoSolicitudCam = validarEstadoSolicitudCamillero($wemp_pmla, $rowTurnos['Logisc']);
					
					if($estadoSolicitudCam){
						
						//Si la solicitud ya ha sido asignada y el camillero ya llegó o se anuló la solicitud se actualiza citasen_000023
						$sqlUpdateEstadoCamillero = "UPDATE 
														".$solucionCitas."_000023
													 SET Logesc = 'on'
													 WHERE Logdoc = '".$rowTurnos['Logdoc']."'
													 AND Logtur = '".$rowTurnos['Logtur']."'
						";
						$resultUpdate =  mysql_query($sqlUpdateEstadoCamillero) or die("<b>ERROR EN QUERY MATRIX($sqlUpdateEstadoCamillero):</b><br>".mysql_error());
						
						$arrayTurnosAgenda[$rowTurnos['Logdoc']]['EstadoSolicitudCamillero']					= 1;
					}				
				}
			}
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No hay turnos asignados con citas agendadas.";
	}
	
	$respuesta['ArrayTurnosAgenda'] = $arrayTurnosAgenda;
	$respuesta['arrayUbicacionProceso'] = $arrayUbicacionProceso;
	$respuesta['sqlTurnos'] = $sqlTurnos;
	
	echo json_encode($respuesta);
	return;
}

// --> 	Muestra el popup para la edición y asociación de un paciente con turno sin cita
// 		Eimer Castro, 2016-02-19.
if (isset($accion) and $accion == 'asociarTurnoCita'){

	$respuesta = array('Error' => false, 'Mensaje' => '', 'html' => '', 'turno' => '', 'cedulaAgenda' => '', 'nombreAgenda' => '', 'cedulaSinCita' => '', 'nombreSinCita' => '');

	

	

	global $conex;
	global $solucionCitas;

	if($turnoPac != '')
	{
		$respuesta['html'] .= "<table id='tablaAsociarTurno' align='center' id='tablaPacTurnos'>
									<tr align='center'>
										<td class='encabezadoTabla' colspan='7'>ASOCIAR PACIENTES CON TURNO Y SIN CITA</td>
									</tr>

									<tr align='center'>
										<td class='encabezadoTabla'>Turno</td>
										<td class='encabezadoTabla'>Documento</td>
										<td class='encabezadoTabla'>Nombre del Paciente</td>
										<td class='encabezadoTabla'>Seleccione correcto</td>
									</tr>

									<tr class='fila1 find' id='trEditarTurnoCita_".$turnoPac."'>
										<td style='padding:2px;' ><input type='hidden' id='asociarTurnoPacAgenda' name='asociarTurnoPacAgenda' value='".$turnoPac."' documentoAgenda='".$cedulaPacAgenda."'><b>".substr($turnoPac, 7)."</b></td>
										<td style='padding:2px;' ><input type='text' id='asociarCedulaPacAgenda' name='asociarCedulaPacAgenda' value='".$cedulaPacAgenda."'></td>
										<td style='padding:2px;' ><input type='text' id='asociarNombrePacAgenda' name='asociarNombrePacAgenda' value='".$nombrePacAgenda."' size='".strlen($nombrePacAgenda)."'></td>
										<td style='padding:2px;' align='center'><input type='radio' name='opcionCorrectaTurnoSinCita' id='optTurnoAgenda' value='PacAgenda' /></td>
									</tr>

									<tr class='fila1 find' id='trEditarTurnoCita_".$turnoPac."'>
										<td style='padding:2px;' ><input type='hidden' id='asociarTurnoPacSinCita' name='asociarTurnoPacSinCita' value='".$turnoPac."'><b>".substr($turnoPac, 7)."</b></td>
										<td style='padding:2px;' ><input type='text' id='asociarCedulaPacSinCita' name='asociarCedulaPacSinCita' value='".$cedulaPacSincita."'></td>
										<td style='padding:2px;' ><input type='text' id='asociarNombrePacSinCita' name='asociarNombrePacSinCita' value='".$nombrePacSincita."' size='".strlen($nombrePacSincita)."'></td>
										<td style='padding:2px;' align='center'><input type='radio' name='opcionCorrectaTurnoSinCita' id='optTurnoSinCita' value='PacSinCita' /></td>
									</tr>

									<tr>
										<td></td>
										<td style='padding:2px;' align='center'><input type='button' id='guardarAsociarTurnoCita_".$turnoPac."' name='guardarAsociarTurnoCita_".$turnoPac."' onclick='guardarAsociarTurnoCita(\"".$turnoPac."\", \"".$cedulaPacSincita."\", \"".$nombrePacSincita."\", \"".$cedulaPacAgenda."\", \"".$nombrePacAgenda."\", \"".$idAgenda."\")' value='Guardar'></td>
										<td style='padding:2px;' align='center'><input type='button' id='cancelarAsociarTurnoCita_".$turnoPac."' name='cancelarAsociarTurnoCita_".$turnoPac."' onclick='cerrarAsociarTurnoCita();' value='Cancelar'></td>
									</tr>
								</table>
								
								<br>
								<span id='msjErorOpcionCorrecta' style='color:red' ></span>
								
								";

		$respuesta['turno'] 		= $turnoPac;
		$respuesta['cedulaAgenda'] 	= $cedulaPacAgenda;
		$respuesta['nombreAgenda'] 	= $nombrePacAgenda;
		$respuesta['cedulaSinCita'] = $cedulaPacSincita;
		$respuesta['nombreSinCita'] = $nombrePacSincita;
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se pudo mostrar la tabla de edici&oacute;n de la informaci&oacute;n del paciente.";
	}

	echo json_encode($respuesta);
	return;
}

// --> 	Efectua el guardado de la información de una paciente
// 		Eimer Castro, 2016-02-22.
if (isset($accion) and $accion == 'guardarAsociarTurnoCita'){

	$respuesta = array('Error' => false, 'MensajeCitasen23' => '', 'MensajeCitasen09' => '');

	

	

	global $conex;
	global $solucionCitas;
	
	//2016-07-11 Se valida si la cedula buena era la que digito el usuario y no la que estaba ingresada en la agenda 
	//para modificar el valor de Logcdc
	$sqlUpdateAdd = "";
	if($cedulaSinCita === $cedulaPac){
		$sqlUpdateAdd = ", Logcdc = 'on'";	
	}
	
	$sqlUpdateLogCitasen23 ="UPDATE citasen_000023
							 SET Logdoc = '".$cedulaPac."'
							 ".$sqlUpdateAdd."
							 WHERE Logtur='".$turnoPac."'";						
	
	$resSqlUpdateLogCitasen23 = mysql_query($sqlUpdateLogCitasen23) or die(mysql_errno() - "Eror en el query " . $sqlUpdateLogCitasen23 . " - " . mysql_error());
	
	if($resSqlUpdateLogCitasen23)
	{
		if($resSqlUpdateLogCitasen23) {
			$respuesta['Error'] 	= false;
			$respuesta['MensajeCitasen23'] 	= "Se han actualizado los datos correctamente en el registro de procesos.";
		}
	} else {
		$respuesta['Error'] 	= true;
		$respuesta['MensajeCitasen23'] 	= "No se ha podido actualizar los datos.";
	}
	
	//Se actualizan los datos en la tabla de la agenda 
	$sqlUpdateCitasCitasen09 ="UPDATE citasen_000009
								SET Cedula = '".$cedulaPac."',
									Nom_pac = '".$nombrePac."'
								WHERE id='".$idFilaAgenda."'  
								AND Fecha='".date("Y-m-d")."';";

	$resSqlUpdateCitasCitasen09 = mysql_query($sqlUpdateCitasCitasen09) or die(mysql_errno() - "Eror en el query " . $sqlUpdateCitasCitasen09 . " - " . mysql_error());
		
	if($resSqlUpdateCitasCitasen09)
	{
		if($resSqlUpdateCitasCitasen09) {
			$respuesta['Error'] 	= false;
			$respuesta['MensajeCitasen09'] 	= "Se han actualizado los datos correctamente en la agenda de citas.";
		}
	} else {
		$respuesta['Error'] 	= true;
		$respuesta['MensajeCitasen09'] 	= "No se ha podido actualizar los datos.";
	}

	echo json_encode($respuesta);
	return;
}

if (isset($accion) and $accion == 'validarCancelacion'){
	$respuesta = array('Error' => false, 'Respuesta' => true, "Mensaje" => "");

	

	

	global $conex;
	global $solucionCitas;
	
	$sqlObtenerDatos = "SELECT Logdoc, Logffp, Loghfp, Logefp
						FROM 
							".$solucionCitas."_000023
						WHERE 
							Fecha_data = '" . date("Y-m-d"). "'
						AND Logdoc = '" . $cedulaPac ."'
	";
	$resConsulta 	= mysql_query($sqlObtenerDatos) or die("<b>ERROR EN QUERY MATRIX($sqlObtenerDatos):</b><br>".mysql_error());
	$arrRspuesta    = mysql_fetch_assoc($resConsulta);
	
	if(isset($arrRspuesta["Logdoc"]) && $arrRspuesta["Logdoc"] != ""){
		if($arrRspuesta["Logffp"] != "0000-00-00" && $arrRspuesta["Loghfp"] != "00:00:00" && $arrRspuesta["Logefp"] == "on"){
			$respuesta["Error"] = true;
			$respuesta["Respuesta"] = false;
			$respuesta["Mensaje"] = "No es posible cancelar la cita, el procedimiento de este paciente ya ha sido finalizado.";
		}
	}
	
	echo json_encode($respuesta);
	return;
}

if (isset($accion) and $accion == 'validarNoAsiste'){
	$respuesta = array('Error' => false, 'Respuesta' => true, "Mensaje" => "");

	

	

	global $conex;
	global $solucionCitas;
	
	$sqlObtenerDatos = "SELECT Logdoc, Logtur
						FROM 
							".$solucionCitas."_000023
						WHERE 
							Fecha_data = '" . date("Y-m-d"). "'
						AND Logdoc = '" . $cedulaPac ."'
	";
	$resConsulta 	= mysql_query($sqlObtenerDatos) or die("<b>ERROR EN QUERY MATRIX($sqlObtenerDatos):</b><br>".mysql_error());
	$arrRspuesta    = mysql_fetch_assoc($resConsulta);
	
	if(isset($arrRspuesta["Logtur"]) && $arrRspuesta["Logtur"] != ""){	
		$turno = explode("-", $arrRspuesta["Logtur"]);
		$turno = isset($turno[1]) ? $turno[1] : "";
				
		$respuesta["Error"] = true;
		$respuesta["Respuesta"] = false;		
		$respuesta["Mensaje"] = "No es posible marcar la cita como no asistida, el paciente ya tiene el turno: " . $turno;
	}
	
	echo json_encode($respuesta);
	return;
}


if (isset($accion) and $accion == 'validarDisponibilidadUbicacion'){
	
	

	

	global $conex;

	$respuesta = array('Error' => false, 'Ocupado' => false, "Mensaje" => "");
	$usuarioLogueado = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
	
	$sqlValidarUbicacion = "SELECT Rumusu as usuarioOcupa
						FROM 
							".$solucionCitas."_000028
						WHERE 
							Rummon = '" . $monitor . "'
						AND Rumubi = '" . $ubicacion ."'
						AND Rumusu != ''						
	";
	$resConsulta 	= mysql_query($sqlValidarUbicacion) or die("<b>ERROR EN QUERY MATRIX($sqlValidarUbicacion):</b><br>".mysql_error());
	$arrRspuesta    = mysql_fetch_assoc($resConsulta);
	
	if(isset($arrRspuesta["usuarioOcupa"]) && $arrRspuesta["usuarioOcupa"] != "" && ($arrRspuesta["usuarioOcupa"] != $usuarioLogueado)){
		
		$respuesta["Ocupado"] = true;
		
		//Se consulta el nombre del usuario
		$sqlDatosUsuario = "SELECT 
								descripcion as nombreUsuario
							FROM 
								usuarios
							WHERE 
								codigo = '".$arrRspuesta["usuarioOcupa"]."'
		";
		$resUser 	= mysql_query($sqlDatosUsuario) or die("<b>ERROR EN QUERY MATRIX($sqlDatosUsuario):</b><br>".mysql_error());
		$arrInfoUser    = mysql_fetch_assoc($resUser);
	
		$nameUser = isset($arrInfoUser["nombreUsuario"]) ? $arrInfoUser["nombreUsuario"] : "";
		$respuesta["Mensaje"] = "Esta ubicación ya se encuentra ocupada por " . $nameUser . ". <br> ¿Desea que sea reasignada?";
	}else{
		//Se asigna el usuario actual a la ubicacion seleccionada		
		
		//Antes de asignar la ubicación seleccionada al usuario actual se libera la ubicación que tenía seleccionada en caso de que así sea
		$liberarUbicaciones = liberarUbicaciones($usuarioLogueado,$solucionCitas);
				
		$sqlAsignarUsuario = "UPDATE 
								".$solucionCitas."_000028
							SET 
								Rumusu = '".$usuarioLogueado."'
							WHERE 
								Rummon = '" . $monitor . "'
								AND Rumubi = '" . $ubicacion ."'
		";
		$resAsignarUser 	= mysql_query($sqlAsignarUsuario) or die("<b>ERROR EN QUERY MATRIX($sqlAsignarUsuario):</b><br>".mysql_error());
		
		if(!$resAsignarUser){		
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No fue posible asociar su usuario con la ubicación seleccionada.";
		}
	}
		
	echo json_encode($respuesta);
	return;
}

/*Verónica Arismendy
Función que reasigna una ubicación al usuario actual.
*/
if (isset($accion) and $accion == 'reasignarUbicacion'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");

	$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";	
	
	if($usuario != ""){
		$liberarUbicaciones = liberarUbicaciones($usuario,$solucionCitas);
		
		$sqlReasignarUsuario = "UPDATE
								".$solucionCitas."_000028
							SET  Rumusu = '" . $usuario ."'	
							WHERE 
								Rummon = '" . $monitor . "'
							AND Rumubi = '" . $ubicacion ."'						
		";
		$resConsulta 	= mysql_query($sqlReasignarUsuario) or die("<b>ERROR EN QUERY MATRIX($sqlReasignarUsuario):</b><br>".mysql_error());
			
		if(!$resConsulta) {		
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No ha sido posible reasignar la ubicación.";
		}
			
	} else {
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No ha sido posible reasignar la ubicación.";
	}
	
	echo json_encode($respuesta);
	return;	
}

//funcion que libera cualquier ubicación que pueda estar siendo usada por el usuario actual
if (isset($accion) and $accion == 'liberarUbicaciones'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
	
	$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
	
	if($usuario != ""){		
		$liberarUbicaciones = liberarUbicaciones($usuario,$solucionCitas);
			
		if(!$liberarUbicaciones){		
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No ha sido posible liberar la ubicación que tenía seleccionada.";
		}			
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No ha sido posible liberar la ubicación que tenía seleccionada.";
	}
	
	echo json_encode($respuesta);
	return;	
}


// Verónica Arismendy 2016-05-16
//funcion que guarda los datos cuando se selecciona o des-selecciona la opción entrega de biopsia 
//con el fin de guardar el registro de que se entrega biopsia cuando aplique. 
if (isset($accion) and $accion == 'registraEntregaBiopsia'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
	
	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
					FROM " . $solucionCitas . "_000009
					WHERE id = " . $idFila . "
	";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
	
		$sqlValidar = "SELECT Logtur turno, Logebf, Logeir, Logpss, Logcps
						FROM ".$solucionCitas."_000023
						WHERE Fecha_data = '".date("Y-m-d")."'
						AND Logdoc = '".$cedulaPac."'
						AND Logest = 'on'
						ORDER BY 1 DESC
						LIMIT 1
		";
	
		$resSqlValidar = mysql_query($sqlValidar, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValidar):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($resSqlValidar);
				
		if(isset($row["turno"]) && $row["turno"] != ""){
			if($row["Logcps"] == "on"){
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "A este paciente no se le realiz&oacute; procedimiento";
			}else{
				if($row["Logebf"] == "off" && ($row["Logeir"] == "on" || $row["Logpss"] == "on" )){			
					$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
					$sqlUpdate = "UPDATE 
										".$solucionCitas."_000023
									  SET 
											Logelb = 'on'
										  , Logeab = 'on'
										  , Logubb = '".$usuario."'
										  , Logfbf = '".date("Y-m-d")."'
										  , Loghbf = '".date("H:i:s")."'
										  , Logebf = 'on'
									  WHERE Fecha_data = '".date("Y-m-d")."'
									  AND Logdoc = '".$cedulaPac."'
									  AND Logtur = '".$row["turno"]."'								  
					";
					$resConsulta = mysql_query($sqlUpdate) or die("<b>ERROR EN QUERY MATRIX($sqlUpdate):</b><br>".mysql_error());

					if(!$resConsulta){		
						$respuesta["Error"] = true;
						$respuesta["Mensaje"] = "No ha sido posible finalizar el proceso de entrega de biopsia.";
					}else{
						$respuesta["Error"] = false;
						$respuesta["Mensaje"] = "Se ha dado por finalizado el proceso de entrega de biopsia.";	
					}
				}else{
					$respuesta["Error"] = false;
					$respuesta["Mensaje"] = "El proceso de entrega de biopsia no ha sido finalizado. <br>Por favor verifique que el paciente si est&eacute; en recuperaci&oacute.";	
				}
			}
			
		}else{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No es posible finalizar el proceso de entrega de biopsia. <br> Verifique que el paciente tenga un turno.";
		}
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible finalizar el proceso de entrega de biopsia. <br> Verifique que el paciente se encuentre agendado.";
	}
	
	echo json_encode($respuesta);
	return;	
		
	echo json_encode($respuesta);
	return;	
}

//2016-05-18 Verónica Arismendy 
//Función que registra la llamada del acompañante a la sala de recuperación
if (isset($accion) and $accion == 'llamadaAcompanante'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
	
	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
						FROM " . $solucionCitas . "_000009
					WHERE id = " . $idfila . "
	";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
	
		$sqlValidar = "SELECT Logtur turno, Logeir inicioRecuperacion, Logefr finRecuperacion, Logels llamadoResul, Logeas finLlamadoResult
						FROM ".$solucionCitas."_000023
						WHERE Fecha_data = '".date("Y-m-d")."'
						AND Logdoc = '".$cedulaPac."'
						AND Logest = 'on'
						ORDER BY 1 DESC
						LIMIT 1
		";	 

		$resSqlValidar = mysql_query($sqlValidar, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValidar):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($resSqlValidar);
				
		if(isset($row["turno"]) && $row["turno"] != ""){
			
			//Primero se valida que el acompañante no esté siendo llamado para la entrega de resultados
			if($row["llamadoResul"] != "off" && $row["finLlamadoResult"] != "on"){
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "No ha sido posible realizar el llamado. <br> El acompañante est&aacute; siendo llamado para la entrega de resultados.";
				
			}else{
				if($row["inicioRecuperacion"] != "off" && $row["finRecuperacion"] != "on" ){
					$sqlUpdate = "UPDATE 
									".$solucionCitas."_000023
								  SET 
										Logfar = '".date("Y-m-d")."'
									  , Loghar = '".date("H:i:s")."'
									  , Logear = 'on'
									  , logflr = '0000-00-00'
									  , loghlr = '00:00:00'
									  , logelr = 'off'
								  WHERE Fecha_data = '".date("Y-m-d")."'
								  AND Logdoc = '".$cedulaPac."'
								  AND Logtur = '".$row["turno"]."'								  
					";
					$resConsulta = mysql_query($sqlUpdate) or die("<b>ERROR EN QUERY MATRIX($sqlUpdate):</b><br>".mysql_error());
		
					if(!$resConsulta){		
						$respuesta["Error"] = true;
						$respuesta["Mensaje"] = "No ha sido posible realizar el llamado del acompa&ntilde;ante.";
					}
				}else if($row["inicioRecuperacion"] != "on"){
					$respuesta["Error"] = true;
					$respuesta["Mensaje"] = "No ha sido posible realizar el llamado. <br> El proceso de recuperación no ha sido iniciado.";
				}else{
					$respuesta["Error"] = true;
					$respuesta["Mensaje"] = "No ha sido posible realizar el llamado. <br> El proceso de recuperación ya ha sido finalizado.";
				}
			}
		}else{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No ha sido posible realizar el llamado. <br> Verifique que el paciente tenga un turno.";
		}
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No ha sido posible realizar el llamado. <br> Verifique que el paciente se encuentre agendado.";
	}
	
	echo json_encode($respuesta);
	return;	
}

//2016-05-18 Verónica Arismendy 
//Función que registra la llegada del acompañante y el finalizado del llamado
if (isset($accion) and $accion == 'finalizarLlamadaAcompanante'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
	
	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
					FROM " . $solucionCitas . "_000009
					WHERE id = " . $idfila . "
	";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
	
		$sqlValidar = "SELECT Logtur turno, Logear estadoLllamado, Logefr finRecuperacion
						FROM ".$solucionCitas."_000023
						WHERE Fecha_data = '".date("Y-m-d")."'
						AND Logdoc = '".$cedulaPac."'
						AND Logest = 'on'
						ORDER BY 1 DESC
						LIMIT 1
		";
	
		$resSqlValidar = mysql_query($sqlValidar, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValidar):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($resSqlValidar);
				
		if(isset($row["turno"]) && $row["turno"] != ""){
			
			if($row["estadoLllamado"] != "off" && $row["finRecuperacion"] != "on" ){
				$sqlUpdate = "UPDATE 
								".$solucionCitas."_000023
							  SET 
									Logflr = '".date("Y-m-d")."'
								  , Loghlr = '".date("H:i:s")."'
								  , Logelr = 'on'
							  WHERE Fecha_data = '".date("Y-m-d")."'
							  AND Logdoc = '".$cedulaPac."'
							  AND Logtur = '".$row["turno"]."'								  
				";
				$resConsulta = mysql_query($sqlUpdate) or die("<b>ERROR EN QUERY MATRIX($sqlUpdate):</b><br>".mysql_error());
	
				if(!$resConsulta){		
					$respuesta["Error"] = true;
					$respuesta["Mensaje"] = "No ha sido posible finalizar el llamado del acompa&ntilde;ante.";
				}
			} else if($row["inicioRecuperacion"] != "on"){
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "No ha sido posible apagar el llamado. <br> No se ha iniciado el llamado.";
			} else {
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "No ha sido posible realizar el llamado. <br> El proceso de recuperación ya ha sido finalizado.";
			}			
		} else {
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No ha sido posible realizar el llamado. <br> Verifique que el paciente tenga un turno.";
		}
	} else {
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No ha sido posible realizar el llamado. <br> Verifique que el paciente se encuentre agendado.";
	}
	
	echo json_encode($respuesta);
	return;	
}


if (isset($accion) and $accion == 'finalizarAdmision'){
	
	

	include_once("root/comun.php");
	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
	
	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
					FROM " . $solucionCitas . "_000009
					WHERE id = " . $idFila . "
	";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
	
		$sqlValidar = "SELECT Logtur turno, Logffa, Loghfa, Logefa, Loghis, Loging
						FROM ".$solucionCitas."_000023
						WHERE Fecha_data = '".date("Y-m-d")."'
						AND Logdoc = '".$cedulaPac."'
						AND Logest = 'on'
						ORDER BY 1 DESC
						LIMIT 1
		";
	
		$resSqlValidar = mysql_query($sqlValidar, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValidar):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($resSqlValidar);
				
		if(isset($row["turno"]) && $row["turno"] != ""){		
		
			//Se debe validar si se guardo ingreso y numero de historia sino se debe ir a consultar y guardar
			$sqlAddUpdate = "";
			if($row["Loghis"] == "" || $row["Loging"] == ""){
				$prefMovhos = consultarAliasPorAplicacion($conex,$empresa,"movhos");
				$fecha = date("Y-m-d");
								
				$consultaHistoria ="SELECT 
										m.Inghis,  m.Inging
									FROM 
										root_000037 r 
									INNER JOIN 
										".$prefMovhos."_000016 m ON m.Inghis = r.Orihis AND m.Inging = r.Oriing 
									WHERE 
										r.Oriced = '".$cedulaPac."'
										AND m.Fecha_data = '".$fecha."'
										AND r.Oriori = '".$empresa."'				
				";
				$resConHistoria = mysql_query($consultaHistoria) or die("<b>ERROR EN QUERY MATRIX($consultaHistoria):</b><br>".mysql_error());
				$rowHistoria = mysql_fetch_array($resConHistoria);
				
				if(isset($rowHistoria["Inghis"]) && $rowHistoria["Inghis"] != "") {
	
					$sqlAddUpdate = ", Loghis = '".$rowHistoria["Inghis"]."'
									 , Loging = '".$rowHistoria["Inging"]."'
					";
				}
			}
			
			$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
			$sqlUpdate = "UPDATE 
								".$solucionCitas."_000023
							  SET 
									Logeia = 'on'
								  , Logefa = 'on'
								  , Loguad = '".$usuario."'
								  , Logfua = '".date("Y-m-d")."'
								  , Loghua = '".date("H:i:s")."'
								  , Logeua = 'on'
								  , Logela = 'on'
								  ".$sqlAddUpdate."
							  WHERE Fecha_data = '".date("Y-m-d")."'
							  AND Logdoc = '".$cedulaPac."'
							  AND Logtur = '".$row["turno"]."'								  
			";
			
			$resConsulta = mysql_query($sqlUpdate) or die("<b>ERROR EN QUERY MATRIX($sqlUpdate):</b><br>".mysql_error());

			if(!$resConsulta){		
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "No ha sido posible finalizar el llamado del acompa&ntilde;ante.";
			}else{
				$respuesta["Error"] = false;
				$respuesta["Mensaje"] = "Se ha dado por finalizada la admisi&oacute;n.";	
			}
			
		}else{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No es posible finalizar  el proceso de admisi&oacute;n. <br> Verifique que el paciente tenga un turno.";
		}
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible finalizar el proceso de admisi&oacute;n. <br> Verifique que el paciente se encuentre agendado.";
	}
	
	echo json_encode($respuesta);
	return;	
}

//funcion que finaliza el proceso de preparacion
if (isset($accion) and $accion == 'finalizarPreparacion'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
	
	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
					FROM " . $solucionCitas . "_000009
					WHERE id = " . $idfila . "
	";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
	
		$sqlValidar = "SELECT Logtur turno, Logeua
						FROM ".$solucionCitas."_000023
						WHERE Fecha_data = '".date("Y-m-d")."'
						AND Logdoc = '".$cedulaPac."'
						AND Logest = 'on'
						ORDER BY 1 DESC
						LIMIT 1
		";
	
		$resSqlValidar = mysql_query($sqlValidar, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValidar):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($resSqlValidar);
				
		if(isset($row["turno"]) && $row["turno"] != ""){
			if($row["Logeua"] == "on"){			
				$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
				$sqlUpdate = "	UPDATE 
									".$solucionCitas."_000023
								SET 
									  Logelt = 'on'
									, Logeat = 'on'
									, Loguat = '".$usuario."'
									, Logfaf = '".date("Y-m-d")."'
									, Loghaf = '".date("H:i:s")."'
									, Logeaf = 'on'
								WHERE Fecha_data = '".date("Y-m-d")."'
								  AND Logdoc = '".$cedulaPac."'
								  AND Logtur = '".$row["turno"]."'								  
				";
				$resConsulta = mysql_query($sqlUpdate) or die("<b>ERROR EN QUERY MATRIX($sqlUpdate):</b><br>".mysql_error());

				if(!$resConsulta){		
					$respuesta["Error"] = true;
					$respuesta["Mensaje"] = "No ha sido posible finalizar el proceso de preparaci&oacute;n.";
				}else{
					$respuesta["Error"] = false;
					$respuesta["Mensaje"] = "Se ha dado por finalizada la preparaci&oacute;n.";	
				}
			}else{
				$respuesta["Error"] = false;
				$respuesta["Mensaje"] = "La preparaci&oacute;n no ha sido finalizada.";	
			}			
		}else{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No es posible finalizar  el proceso de preparaci&oacute;n. <br> Verifique que el paciente tenga un turno.";
		}
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible finalizar el proceso de preparaci&oacute;n. <br> Verifique que el paciente se encuentre agendado.";
	}
	
	echo json_encode($respuesta);
	return;	
}



//funcion que finaliza el proceso de entrega de resultados
if (isset($accion) and $accion == 'registraFinalizarResultado'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
	
	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
					FROM " . $solucionCitas . "_000009
					WHERE id = " . $idfila . "
	";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
	
		$sqlValidar = "SELECT Logtur turno, Logeir, Logpss, Logcps
						FROM ".$solucionCitas."_000023
						WHERE Fecha_data = '".date("Y-m-d")."'
						AND Logdoc = '".$cedulaPac."'
						AND Logest = 'on'
						ORDER BY 1 DESC
						LIMIT 1
		";
	
		$resSqlValidar = mysql_query($sqlValidar, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValidar):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($resSqlValidar);
				
		if(isset($row["turno"]) && $row["turno"] != ""){
			
			if($row["Logcps"] == "on" ){
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "A este paciente no se le realiz&oacute; procedimiento.";
			}else{
				if($row["Logeir"] == "on" || $row["Logpss"] == "on"){			
					$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
					$sqlUpdate = "UPDATE 
										".$solucionCitas."_000023
									  SET 
											Logels = 'on'
										  , Logeas = 'on'
										  , Logues = '".$usuario."'
										  , Logfrf = '".date("Y-m-d")."'
										  , Loghrf = '".date("H:i:s")."'
										  , Logerf = 'on'
									  WHERE Fecha_data = '".date("Y-m-d")."'
									  AND Logdoc = '".$cedulaPac."'
									  AND Logtur = '".$row["turno"]."'								  
					";
					$resConsulta = mysql_query($sqlUpdate) or die("<b>ERROR EN QUERY MATRIX($sqlUpdate):</b><br>".mysql_error());

					if(!$resConsulta){		
						$respuesta["Error"] = true;
						$respuesta["Mensaje"] = "No ha sido posible finalizar el proceso de entrega de resultados.";
					}else{
						$respuesta["Error"] = false;
						$respuesta["Mensaje"] = "El proceso de entrega de resultados ha sido finalizado.";	
					}
				}else{
					$respuesta["Error"] = true;
					$respuesta["Mensaje"] = "La entrega de resultados no ha sido finalizada. <br> Verifique que el paciente si est&eacute; en recuperaci&oacute o si el procedimiento fue sin sedaci&oacute;n que le den de alta desde la sala de procedimiento.";	
				}
			}
		}else{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No es posible finalizar el proceso de entrega de resultados. <br> Verifique que el paciente tenga un turno.";
		}
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible finalizar el proceso de entrega de resultados. <br> Verifique que el paciente se encuentre agendado.";
	}
	
	echo json_encode($respuesta);
	return;	
}


//2016-06-21
//VERONICA ARISMENDY
//FUNCION PARA REALIZAR EL LLAMADO DEL ACOMPAÑANTE POR PARTE DEL MEDICO
else if (isset($accion) and $accion == 'llamarPacienteMedico') {

	

	

	
	$horaAten=date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
							FROM " . $solucionCitas . "_000009
						WHERE id = '" . $idfila . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		$sqlValLlamadoResultados = " SELECT Logtur AS Turno, Logdoc AS Documento, Logels, Logear, Logelr, Logelb, Logeab, Logeas
							   FROM " . $solucionCitas ."_000023
							   WHERE Fecha_data	=	'" . date('Y-m-d') . "'
								 AND Hora_data	!=	'00:00:00'
								 AND Logfir		=	'" . date('Y-m-d') . "'
								 AND Loghir		!=	'00:00:00'
								 AND Logeir		=	'on'
								 AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								 AND Logest     = 'on'
							   ORDER BY Turno DESC;";

		$resValLlamadoResultados = mysql_query($sqlValLlamadoResultados, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValLlamadoResultados):</b><br>".mysql_error());

		if($rowResultados = mysql_fetch_array($resValLlamadoResultados))
		{
			//Primero se valida que no se esté llamando en ese momento al acompañante a recuperacion
			if($rowResultados['Logear'] == "on" && $rowResultados['Logelr'] == "off"){					
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No es posible realizar el llamado.<br>En este momento se est&aacute; llamando el acompa&ntilde;ante a la sala de recuperaci&oacute;n.";
			
			}else if($rowResultados['Logelb'] == "on" && $rowResultados['Logeab'] == "off"){
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No es posible realizar el llamado.<br>En este momento se est&aacute; llamando el acompa&ntilde;ante para la entrega de biopsia.";
			
			}else if($rowResultados['Logels'] == "on" && $rowResultados['Logeas'] == "off"){
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No es posible realizar el llamado.<br>En este momento se est&aacute; llamando el acompa&ntilde;ante para la entrega de resultados.";
			
			}else{
				$sqlActLlamadoMedico = " UPDATE " . $solucionCitas ."_000023
											SET
												Logflm			=	'".date('Y-m-d')."',
												Loghlm			=	'".date('H:i:s')."',
												Logelm			=	'on',
												Logubs			=	'" . $ubicacion . "',
												Logefm          =   'off'
											WHERE	Logtur		=	'" . $rowResultados['Turno'] . "'
											AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

				$resActLlamadoAdm = mysql_query( $sqlActLlamadoMedico, $conex ) or die( mysql_errno()." - Error en el query $sqlActLlamadoMedico - ".mysql_error() );

				$turnoExplode = explode("-", $rowResultados['Turno']);
				$turnoLlamado = $turnoExplode[1];
				
				if(!$resActLlamadoAdm)
				{
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error llamando al acompa&ntilde;ante del paciente.<br>Por favor contacte al personal de soporte.</span><br>
												<span style='font-size:10px'>($sqlActLlamadoMedico: ".mysql_error().')</span>';
				}				
			}
		} else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el llamado del acompa&ntilde;ante del paciente.<br>Verifique que ya se haya iniciado la recuperaci&oacute;n.";
		}
	}
	else
	{
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar el llamado del acompa&ntilde;ante del paciente. <br>Verifique que el paciente se encuentre agendado.";
	}
		
	echo json_encode($respuesta);
	return;
}

//2016-06-21
//VERONICA ARISMENDY
//FUNCION PARA TERMINAR EL LLAMADO DEL ACOMPAÑANTE POR PARTE DEL MEDICO
else if (isset($accion) and $accion == 'apagarLlamarPacienteMedico') {

	

	

	$horaAten = date("H:i:s");

	$respuesta = array('Error' => false, 'Mensaje' => '');

	// Trae la cedula asociada al id de la tabla citasen_000009
	$sqlIdCE09 = "	SELECT cedula
					FROM " . $solucionCitas . "_000009
					WHERE id = '" . $idfila . "';";

	$resIdCE09 = mysql_query($sqlIdCE09, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlIdCE09):</b><br>".mysql_error());

	if($rowIdCE09 = mysql_fetch_array($resIdCE09))
	{
		$sqlValApagarLlamadoMedico = " SELECT Logtur AS Turno, Logdoc AS Documento
							   FROM " . $solucionCitas ."_000023
							  WHERE Fecha_data	=	'" . date('Y-m-d') . "'
							    AND Hora_data	!=	'00:00:00'
							    AND Logflm		=	'" . date('Y-m-d') . "'
							    AND Loghlm		!=	'00:00:00'
							    AND Logelm		=	'on'
							    AND Logdoc		=	'" . $rowIdCE09['cedula'] . "'
								AND Logest     = 'on'
							  ORDER BY Turno DESC;";

		$resValApagarLlamadoResultados = mysql_query($sqlValApagarLlamadoMedico, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValApagarLlamadoMedico):</b><br>".mysql_error());

		if($rowValApagarLlamadoResultados = mysql_fetch_array($resValApagarLlamadoResultados))
		{
			$usuario = explode("-", $_SESSION['user']);
			$wuse = $usuario[1];
			$sqlActApagarLlamadoMedico = " UPDATE " . $solucionCitas ."_000023
										SET
											Logffm			=	'".date('Y-m-d')."',
											Loghfm			=	'".date('H:i:s')."',
											Logefm			=	'on',
											Logues			=	'".$wuse."',
											Logelm          = 	'off'
										WHERE Logtur		=	'" . $rowValApagarLlamadoResultados['Turno'] . "'
										AND Logdoc	=	'" . $rowIdCE09['cedula'] . "'";

			$resActLlamadoAdm = mysql_query( $sqlActApagarLlamadoMedico, $conex ) or die( mysql_errno()." - Error en el query $sqlActApagarLlamadoMedico - ".mysql_error() );

			// --> Si ha ocurrido un error guardando el turno
			if(!$resActLlamadoAdm) {
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, ha ocurrido un error apagando el llamado al paciente.<br>Por favor contacte al personal de soporte.</span><br>
											<span style='font-size:10px'>($sqlActApagarLlamadoMedico: ".mysql_error().')</span>';
			}
		} else {
			$respuesta['Error'] 	= true;
			$respuesta['Mensaje'] 	= "No se ha podido realizar el apagado al llamado del acompa&ntilde;ante paciente.<br>Verifique que ya se realiz&oacute; el llamado de este.";
		}
	} else {
		$respuesta['Error'] 	= true;
		$respuesta['Mensaje'] 	= "No se ha podido realizar el apagado al llamado del acompa&ntilde;ante paciente.<br>Verifique que se encuentre agendado.";
	}

	echo json_encode($respuesta);
	return;
}


//FUNCION PARA ANULAR UN TURNO
if (isset($accion) and $accion == 'cancelarTurno'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
		
	if(isset($turno)) {			
		$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
		$sqlUpdate = "UPDATE 
						".$solucionCitas."_000023
						SET 
							Logest = 'off',
							Loguet = '".$usuario."'
						WHERE Fecha_data = '".date("Y-m-d")."'
						AND Logtur = '".$turno."'								  
		";
		$resConsulta = mysql_query($sqlUpdate) or die("<b>ERROR EN QUERY MATRIX($sqlUpdate):</b><br>".mysql_error());

		if(!$resConsulta){		
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No ha sido posible anular el turno.";
		}else{
			$respuesta["Error"] = false;
			$respuesta["Mensaje"] = "El turno ha sido anular.";	
		}		
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible anular el turno, por favor verifique que no haya sido asignado a una cita.";
	}
	
	echo json_encode($respuesta);
	return;	
}

//FUNCION PARA QUITAR DE LA LISTA UN PACIENTE POR SER DE OTRA UBICACION
if (isset($accion) and $accion == 'quitarPacienteAgenda'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
		
	if(isset($cedulaPaciente))
	{	
		$sqlValLlamadoResultados = " SELECT count(0) as existe
							   FROM " . $solucionCitas ."_000023
							   WHERE Fecha_data	=	'" . date('Y-m-d') . "'
								 AND Hora_data	!=	'00:00:00'								
								 AND Logdoc		=	'" . $cedulaPaciente. "'
							   ";

		$resValLlamadoResultados = mysql_query($sqlValLlamadoResultados, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValLlamadoResultados):</b><br>".mysql_error());
		$rowResultados = mysql_fetch_array($resValLlamadoResultados);
		
		if(isset($rowResultados["existe"]) && $rowResultados["existe"] > 0)
		{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No es posible eliminar el paciente de la lista porque ya tiene un turno.";
			
		}else{
			$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
			$sqlInsert = "	INSERT INTO 
								".$solucionCitas."_000030
								(Medico, Fecha_data, Hora_data, Pouced, Pouusu, Poucub, Seguridad)
							VALUES('".$solucionCitas."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$cedulaPaciente."', '".$usuario."', '".$ubicacion."', 'C-".$solucionCitas."')						  
			";
			$resConsulta = mysql_query($sqlInsert) or die("<b>ERROR EN QUERY MATRIX($sqlInsert):</b><br>".mysql_error());

			if(!$resConsulta){		
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "No ha sido posible eliminar el paciente e la lista.";
			}
		}		
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible anular el turno, por favor verifique que la cedula no este vac&iacute;a.";
	}
	
	echo json_encode($respuesta);
	return;	
}


//FUNCION PARA QUITAR DE LA LISTA UN PACIENTE POR SER DE OTRA UBICACION
if (isset($accion) and $accion == 'abrirHistoriaClinica'){
	
	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "", "historia" => "", "ingreso" => "", "tipoDoc" => "");
		
	if(isset($cedulaPaciente))
	{	
		$sqlValLlamadoResultados = " SELECT Logtur, Loghis, Loging, Logtip
									 FROM " . $solucionCitas ."_000023
									 WHERE Fecha_data	=	'" . date('Y-m-d') . "'
										AND Hora_data	!=	'00:00:00'								
										AND Logdoc		=	'" . $cedulaPaciente. "'
										AND Logest 	= 	'on'
							   ";

		$resValLlamadoResultados = mysql_query($sqlValLlamadoResultados, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValLlamadoResultados):</b><br>".mysql_error());
		$rowResultados = mysql_fetch_array($resValLlamadoResultados);
		
		if(isset($rowResultados["Logtur"]) && $rowResultados["Logtur"] != ""){
			if(isset($rowResultados["Loghis"]) && $rowResultados["Loghis"] != "")
			{	
				$respuesta["historia"] 	= $rowResultados["Loghis"];
				$respuesta["ingreso"] 	= $rowResultados["Loging"];
				$respuesta["tipoDoc"] 	= $rowResultados["Logtip"];			
			}else{
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "No es posible abrir la historia, al paciente no se le ha hecho ingreso el día de hoy.";
			}	
		}else{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No es posible abrir la historia, el paciente no tiene un turno.";
		}	
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible abrir la historia, por favor verifique que la cedula no este vac&iacute;a.";
	}
	
	echo json_encode($respuesta);
	return;	
}

//Función que permite dar salida a paciente que se hace procedimiento sin sedación
else if (isset($accion) and $accion == 'finalizaPacienteSinSedacion') {

	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
		
	if(isset($cedulaPaciente))
	{	
		$sqlSalidaPacSinSedacion = " SELECT Logtur, Logefp, Logepf
									 FROM " . $solucionCitas ."_000023
									 WHERE Fecha_data	=	'" . date('Y-m-d') . "'
										AND Hora_data	!=	'00:00:00'								
										AND Logdoc		=	'" . $cedulaPaciente. "'
										AND Logest 	= 	'on'
							   ";

		$resValLlamadoResultados = mysql_query($sqlSalidaPacSinSedacion, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlSalidaPacSinSedacion):</b><br>".mysql_error());
		$rowResultados = mysql_fetch_array($resValLlamadoResultados);
		
		if(isset($rowResultados["Logtur"]) && $rowResultados["Logtur"] != ""){
			
			if($rowResultados["Logefp"] == "on" || $rowResultados["Logepf"] == "on"){			
			
				$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
				$sqlUpdateSalida = "UPDATE 
										" . $solucionCitas ."_000023
									SET
										Logpss	= 'on' ,
										Loghps	= '".date("H:i:S")."' ,
										Logups  = '".$usuario."' 
									WHERE 
										Logtur 	= '".$rowResultados["Logtur"]."'
									AND Logdoc 	= '" . $cedulaPaciente. "'

				";
				
				$resUpdateSalida = mysql_query($sqlUpdateSalida, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlUpdateSalida):</b><br>".mysql_error());
		
				if(!resUpdateSalida){
					$respuesta["Error"] = true;
					$respuesta["Mensaje"] = "No ha sido posible darle salida al paciente.";
				}else{
					$respuesta["Mensaje"] = "Se ha dado salida al paciente sin recuperaci&oacute;n, recuerde informarle que debe esperar los resultados.";
				}
			}else{
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "Debe finalizar el procedimiento antes de darle salida al paciente.";
			}
		}else{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "No es posible dar salida a este paciente porque no tiene un turno.";
		}	
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible dar salida a este paciente, por favor verifique que la cedula no este vac&iacute;a.";
	}
	
	echo json_encode($respuesta);
	return;	
}


//Función que registra en base de datos los datos de la cancelación de procedimiento a un paciente que ya estaba sedado y que debe pasar a recuperacion
else if (isset($accion) and $accion == 'cancelaPacienteSedado') {

	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
		
	if(isset($cedulaPaciente))
	{	
		$sqlSalidaPacSinSedacion = " SELECT Logtur, Logefp, Logepf, Logeir, Logeaf
									 FROM " . $solucionCitas ."_000023
									 WHERE Fecha_data	=	'" . date('Y-m-d') . "'
										AND Hora_data	!=	'00:00:00'								
										AND Logdoc		=	'" . $cedulaPaciente. "'
										AND Logest 	= 	'on'
							   ";

		$resValLlamadoResultados = mysql_query($sqlSalidaPacSinSedacion, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlSalidaPacSinSedacion):</b><br>".mysql_error());
		$rowResultados = mysql_fetch_array($resValLlamadoResultados);
		
		if(isset($rowResultados["Logtur"]) && $rowResultados["Logtur"] != ""){
			
			if($rowResultados["Logeaf"] == "on" || $rowResultados["Logeir"] == "off"){			
			
				$usuario = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
				$sqlUpdateSalida = "UPDATE 
										" . $solucionCitas ."_000023
									SET
										Logcps	= 'on' ,
										Loghcp	= '".date("H:i:S")."' ,
										Logucp  = '".$usuario."' ,
										Logfip  = '0000-00-00',
										Loghip  = '00:00:00',
										Logeip  = 'off',
										Logffp  = '0000-00-00',
										Loghfp  = '00:00:00',
										Logefp  = 'off'
									WHERE 
										Logtur 	= '".$rowResultados["Logtur"]."'
									AND Logdoc 	= '" . $cedulaPaciente. "'
				";
				
				$resUpdateSalida = mysql_query($sqlUpdateSalida, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlUpdateSalida):</b><br>".mysql_error());
		
				if(!resUpdateSalida){
					$respuesta["Error"] = true;
					$respuesta["Mensaje"] = "No ha sido cancelar el procedimiento de este paciente.";
				}else{
					$respuesta["Mensaje"] = "Se ha cancelado el procedimiento del paciente. <br> Recuerde que igual por estar sedado debe pasarlo a recuperaci&oacute;n pero no debe esperar resultados.";
				}
			}else{
				$respuesta["Error"] = true;
				$respuesta["Mensaje"] = "El proceso de preparaci&oacute;n no ha sido marcado como finalizado.";
			}
		}else{
			$respuesta["Error"] = true;
			$respuesta["Mensaje"] = "El paciente no tiene un turno.";
		}	
	}else{
		$respuesta["Error"] = true;
		$respuesta["Mensaje"] = "No es posible cancelar el procedimiento de este paciente, por favor verifique que la cedula no este vac&iacute;a.";
	}
	
	echo json_encode($respuesta);
	return;	
}

else if (isset($accion) and $accion == 'guardarUbicacionRecargaPagina') {

	

	

	global $conex;
	$respuesta = array('Error' => false,"Mensaje" => "");
		
	if(isset($ubicacion))
	{	
		$sqlUsuariosActualizan = "SELECT Loguam, id
								  FROM " . $solucionCitas ."_000023
								  WHERE Fecha_data = '" . date('Y-m-d') . "'										
								  AND Logest = 'on'
								  AND Logcdc = 'on'
							   ";

		$arrUsuariosActualizan = mysql_query($sqlUsuariosActualizan, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlUsuariosActualizan):</b><br>".mysql_error());
		$newSql = "";
		$result = "";
		while($rowResultados = mysql_fetch_array($arrUsuariosActualizan))
		{					
			$id = $rowResultados["id"];
		
			$pos = strpos($rowResultados["Loguam"], $ubicacion);
			if($pos === false){
				
				$newArrayUsuarios = $rowResultados["Loguam"] != "" ? ($rowResultados["Loguam"] . "-" . $ubicacion) : ($ubicacion);
				
				$newSql = "UPDATE 
						" . $solucionCitas ."_000023
					   SET Loguam = '".$newArrayUsuarios."'
					   WHERE Fecha_data = '" . date('Y-m-d') . "'										
					   AND id = '".$id."'
				";
				$result = mysql_query($newSql, $conex) or die("<b>ERROR EN QUERY MATRIX($newSql):</b><br>".mysql_error());		
			}
		}		
	}else{
		$respuesta["Error"] = true;		
	}
	
	echo json_encode($respuesta);
	return;	
}


///////////////////////////////////////////////////////
//////FIN DE FUNCIONES AJAX
///////////////////////////////////////////////////////
?>

<html>
<head>
<title>Agenda Salas</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

<style>
  .blinkProcesoActual {
        background-color: #FF9664;
    }

</style>
<script>

	$(document).ready( function (){	
		
		//Se valida si el campo valorPerfil y ValorUbicacion está lleno
		var valPerfil = $("#valorPerfil").val();
		if(valPerfil != ""){
			$("#monitores").val(valPerfil);
		}
		
		
		/*var url = $(location).attr('href');
		if(url.contains("solucionCitas=citasen"))
		{			
			$("#selectPerfil").show();
		}*/
		
		if($("#solucionCitas").val() == "citasen"){
			$("#selectPerfil").show();
		}

		var monitorSeleccionado = $("#monitores");
		if(monitorSeleccionado.val() != '00')
		{
			monitorSeleccionado.trigger("onchange");
		}

		
		var actualizarListaPacientes = setInterval(function() {
														var monitor = $("#monitores").val();
														var ubicacion = $("#ubicacion").val();
														if(ubicacion != '')
														{
															listarPacientesConTurno();
															if(monitor == '01')
															{
																$("#tablaListaTurnos").show();
															}
															else
															{
																$("#tablaListaTurnos").hide();
															}
														}
													}, 10000);

		var blinkProcesoActual = setInterval(function() {
												$("td[blink=true]").toggleClass("blinkProcesoActual");
											}, 1000);
	});

	idRadio = '';
	accion = '';
	est = '';
	func = '';
	campoCancela = '';

	function abrirVentana( adicion, citas, solucion, wdoc, mostrarCausa, wtdo, wagendaMedica)
	{		
		var auxDiv = document.createElement( "div" );
		auxDiv.innerHTML = "<INPUT type='hidden' name='admision' value='"+adicion+"'>";
		document.forms[0].appendChild( auxDiv.firstChild );
		/*se le adiciona al div causa_demora unos parametros para poder consultarlos despues desde
		otra funcion*/
		$('#causa_demora')[0].adicion = adicion;
		$('#causa_demora')[0].citas = citas;
		$('#causa_demora')[0].solucion = solucion;
		$('#causa_demora')[0].wdoc = wdoc;
		$('#causa_demora')[0].wtdo = wtdo;
		$('#causa_demora')[0].wagendaMedica = wagendaMedica;

		if (mostrarCausa == 'on')
		{
			$.blockUI({ message: $('#causa_demora') });
		}
		else
		{
			abrirVentanaAdmision(adicion, citas, solucion, wdoc, wtdo, wagendaMedica)
		}
	}

	function abrirVentanaAdmision(adicion, citas, solucion, wdoc, wtdo, wagendaMedica)
	{
		var ancho=screen.width;
		var alto=screen.availHeight;		
		var wemp_pmla = $('#wemp_pmla').val();
		var path = "../../admisiones/procesos/admision_erp.php?wemp_pmla="+wemp_pmla+"&TipoDocumentoPacAm=" + wtdo + "&DocumentoPacAm=" + wdoc + "&TurnoCsAm=''&AgendaMedica=" + wagendaMedica + "&solucionCitas=" + solucion;
		window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');

	}

	function asistida( adicion,mostrarCausa )
	{
		var auxDiv = document.createElement( "div" );
		auxDiv.innerHTML = "<INPUT type='hidden' name='asistida' value='"+adicion+"'>";
		
		if (mostrarCausa == 'on')
		{
			$.blockUI({ message: $('#causa_demora') });			
		}
		document.forms[0].appendChild( auxDiv.firstChild );
	}

	function asiste(id_radio, id, mostrarCausa)
		{

			var valora = $('[name="'+id_radio+'"]:checked').val();

			if (valora!='on')
			{
				valora = 'off';
			}

			if (mostrarCausa == 'off')
			{
				$.post("pantallaAdmisionSala.php",
					{
						wemp_pmla:      $('#solucionCitas').val(),
						consultaAjax:   '',
						id:          	id,
						accion:         'actualizar',
						est: 			valora,
						caso:			$('#caso').val()
					}
					,function(data) {

						if(data.error == 1) {

						} else {
							document.location.reload(true);
						}
				});			
			}
			else
			{
				$.blockUI({ message: $('#causa_demora') });

				idRadio = id;
				accion = 'demora';
				est = valora;
				func = respuestaAjaxDemora;

				//Busco el select de causa para el div correspondiente
				var contenedorCancela = document.getElementById( "causa_demora" );

				campoCancela = document.getElementById( "causa_demora" ).getElementsByTagName( "select" );			
			}
		}

		//Estas opcioens aplican para las agendas que usan turnero como endoscopia
		function cancelaTurno(id_radio1, id, otroid, solucionCitas, cedulaPac)
		{
			
			//Primero debo validar que el procedimiento no ha sido iniciado
			$.post("pantallaAdmisionSala.php",
					{
						wemp_pmla:      $('#wemp_pmla').val(),
						solucionCitas:  $('#solucionCitas').val(),
						consultaAjax:   'on',
						accion:         'validarCancelacion',
						cedulaPac: 		cedulaPac
					}
					,function(data) {

						console.log(data);
						if(data.Error)
						{
							jAlert(data.Mensaje, "Alerta");
							$("#"+id_radio1).removeAttr("checked");
						}
						else
						{						
							if(data.Respuesta){						
							
								var valorc = $('[name="'+id_radio1+'"]:checked').val();
								if (valorc!='I')
								{
									valorc = 'A';
								}

								mes_confirm = 'Confirma que desea cancelar la cita?';
								if(confirm(mes_confirm))
								{
									$.blockUI({ message: $('#causa_cancelacion') });

									idRadio = id;
									accion = 'cancelar';
									est = valorc;
									func = respuestaAjaxCancela;

									//Busco el select de causa para el div correspondiente
									var contenedorCancela = document.getElementById( "causa_cancelacion" );

									campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );
								}
								else
								{
									$("#"+id_radio1).removeAttr("checked");
								}
							}else{
								$("#"+id_radio1).removeAttr("checked");
							}
						}
				}, 'json');
		}


		function no_asiste_turno(id_radio2, id, otroid, solucionCitas, cedulaPac)
		{
			//Primero debo validar que el procedimiento no ha sido iniciado
			$.post("pantallaAdmisionSala.php",
					{
						wemp_pmla:      $('#wemp_pmla').val(),
						solucionCitas:  $('#solucionCitas').val(),
						consultaAjax:   'on',
						accion:         'validarNoAsiste',
						cedulaPac: 		cedulaPac
					}
					,function(data) {

						console.log(data);
						if(data.Error)
						{
							jAlert(data.Mensaje, "Alerta");
							$("#"+id_radio2).removeAttr("checked");
						}
						else
						{						
							if(data.Respuesta){						
								var valora = $('[name="'+id_radio2+'"]:checked').val();

								if (valora!='off')
								{
									valora = 'off';
								}

								mes_confirm = 'Confirma que desea marcar la cita como no asistida?';
								if(confirm(mes_confirm))
								{
									$.blockUI({ message: $('#causa_noasiste') });

										idRadio = id;
										accion = 'actualizar1';
										est = valora;
										func = respuestaAjaxNoAsiste;

											//Busco el select de causa para el div correspondiente
											var contenedorCancela = document.getElementById( "causa_noasiste" );

											campoCancela = document.getElementById( "causa_noasiste" ).getElementsByTagName( "select" );
								}
								else
								{
									$("#"+id_radio2).removeAttr("checked");
								}								
							}else{
								$("#"+id_radio2).removeAttr("checked");
							}
						}
				}, 'json');
			
		}

	///////////////////////////////////////////////////////////////////////////////////////////	
	//Estas opcioens aplican en general para agendas que no usan turnero
	//2016-06-15 Verónica Arismendy
	function cancela(id_radio1, id)
	{			
			var valorc = $('[name="'+id_radio1+'"]:checked').val();
			if (valorc!='I')
			{
				valorc = 'A';
			}

			mes_confirm = 'Confirma que desea cancelar la cita?';
			if(confirm(mes_confirm))
			{
				$.blockUI({ message: $('#causa_cancelacion') });

				idRadio = id;
				accion = 'cancelar';
				est = valorc;
				func = respuestaAjaxCancela;

				//Busco el select de causa para el div correspondiente
				var contenedorCancela = document.getElementById( "causa_cancelacion" );

				campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );
			}
			else
			{
				$("#"+id_radio1).removeAttr("checked");
			}
	}
		
		//Función que marca las citas como no asistidas (cuando no usan turnero)
	function no_asiste(id_radio2, id)
	{
		var valora = $('[name="'+id_radio2+'"]:checked').val();

		if (valora!='off')
		{
			valora = 'off';
		}

		mes_confirm = 'Confirma que desea marcar la cita como no asistida?';
		if(confirm(mes_confirm))
		{
			$.blockUI({ message: $('#causa_noasiste') });

			idRadio = id;
			accion = 'actualizar1';
			est = valora;
			func = respuestaAjaxNoAsiste;

			//Busco el select de causa para el div correspondiente
			var contenedorCancela = document.getElementById( "causa_noasiste" );

			campoCancela = document.getElementById( "causa_noasiste" ).getElementsByTagName( "select" );
		} else {
			$("#"+id_radio2).removeAttr("checked");
		}				
	}	
		
	
	function llamarAjax()
	{
		var radioadmision = $('[name="rdAdmision"]:checked').val();
		var radioasistida = $('[name="rdAsistida"]:checked').val();

		if(radioadmision == "on" || radioasistida == "on")
		{
			if (radioadmision == "on") {
				abrirVentanaAdmision($('#causa_demora')[0].adicion, $('#causa_demora')[0].citas, $('#causa_demora')[0].solucion, $('#causa_demora')[0].wdoc, $('#causa_demora')[0].wtdo, $('#causa_demora')[0].wagendaMedica);
			}

			//agregar el select al form porque cuando se hace el submit jquery lo saca del form
			document.forms[0].appendChild( document.getElementById( "causa_demora" ).getElementsByTagName( "select" )[0] );
			document.forms[0].submit();
		} else {
			//Asigno el valor seleccionado de la causa
			tipo = campoCancela[0].options[ campoCancela[0].selectedIndex ].text;

			if( idRadio != '' && accion != ''  && est != '' && func != '' && tipo !='' ){

				$.post("pantallaAdmisionSala.php",
						{
							wemp_pmla:      $('#solucionCitas').val(),
							consultaAjax:   '',
							id:          	idRadio,
							accion:         accion,
							est: 			est,
							caso:			$('#caso').val(),
							causa:			tipo
						}
						,func
					);
			}

			idRadio = '';
			accion = '';
			est = '';
			func = '';
			campoCancela = '';
			campoCancela.selectedIndex = 0;
		}
	}

		function respuestaAjaxNoAsiste(data) {

			if(data.error == 1)	{

			} else {
				document.location.reload(true);
			}
		}

		function respuestaAjaxCancela(data){

			if(data.error == 1)
			{
				alert("No se pudo realizar la cancelacion");
			} else {
				alert("Turno Cancelado"); // update Ok.
				document.location.reload(true);
			}
		}

		function respuestaAjaxDemora(data){
			if(data.error == 1) {

			} else {
				document.location.reload(true);
			}
		}

	function imprimir(wemp_pmla,caso,wsw,solucionCitas,slDoctor,valCitas,wfec)
	{
		var v = window.open( 'impresionAgendaSala.php?wemp_pmla='+wemp_pmla+'&caso='+caso+'&wsw='+wsw+'&solucionCitas='+solucionCitas+'&slDoctor='+slDoctor+'&valCitas='+valCitas+'&wfec='+wfec,'','scrollbars=1', 'width=300', 'height=300' );
	}

	function obtenerMonitor(elem){
		var href2 = $(elem).attr("href2");

		href2 = href2+'&monitor='+$("#monitor").val()+'&ubicacion='+$("#ubicacion").val();

		$(elem).attr("href",href2);
		$(elem).removeAttr("onclick");
		$(elem).trigger("click");
	}

	function obtenerUbicacion(){
		return $("#ubicacion").val();
	}

	function asignarMonitor(opcion, solucionCitas, ubicacionSeleccionada)
    {
        $("td[id^='tdAutorizacion_']").hide();
        $("td[id^='tdAdmision_']").hide();
        $("td[id^='tdAtencion_']").hide();
        $("td[id^='tdProcedimiento_']").hide();
        $("td[id^='tdRecuperacion_']").hide();
        $("td[id^='tdBiopsia_']").hide();
        $("td[id^='tdResultados_']").hide();
        $("td[id^='tdTerminarAtencion_']").hide();
        $("td[id^='tdCancela_']").hide();
        $("td[id^='tdno_asiste_']").hide();
        $("td[id^='tdLlamadoMedico_']").hide();
        $("td[id^='tdquitar_agenda_']").hide();
        $("td[id^='tdhistoria_clinica_']").hide();
        $("td[id^='tdcancelaPacienteSedado_']").hide();
        $("td[id^='tdFinalizaPacienteSinSedacion_']").hide();
		
		//***Veronica
		$("td[id^='tdEstadoProceso_']").hide();

        $("#selectUbicacion").hide();
        var monitor = opcion;
        $("#monitor").val(monitor);

        $("#selectUbicacion").show();
		var valUbicacion = ""; //$("#ubicacion").val()
        crearArrayPerfilxUbicacion(monitor, solucionCitas, valUbicacion);

    }

    function validarUbicacion(opcion, solucionCitas)
    {
        var ubicacion = opcion;
        $("#ubicacion").val(ubicacion);
        var monitor = $("#monitor").val();

        if(monitor != '00')
        {
			if(ubicacion != "00"){		
				//Verónica Arismendy
				//Se debe validar primero si otro usuario tiene en uso el perfil y ubicación que se acaban de seleccionar			
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax	:  '',
					accion			:  'validarDisponibilidadUbicacion',	           
					solucionCitas	:  solucionCitas,
					ubicacion		:  ubicacion,
					monitor			:  monitor
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
					}
					else
					{	
						if(data.Ocupado){
							//Verónica le debo preguntar si desea reasignar la ubicación					
							jConfirm(data.Mensaje,"Confirmacion", function(r) {  
								if(r) {
									//Reasigna ubicacion
									$.post("pantallaAdmisionSala.php",
									{
											consultaAjax:      '',
											accion:            'reasignarUbicacion',
											solucionCitas	:  solucionCitas,
											ubicacion		:  ubicacion,
											monitor			:  monitor
									}, function(data){
											if(data.Error)
											{
												jAlert(data.Mensaje, "Alerta");
											} else {
												//Si fue posible reasignar la ubicación se llama función asignarUbicacion();
												asignarUbicacion(monitor, solucionCitas, ubicacion);		
											}
									}, 'json');
								}else{
									$("#Ubicaciones").val("00");
								}
							});
						} else{
							//Si la ubicación no esta ocupada se llama función asignarUbicacion();	
							asignarUbicacion(monitor, solucionCitas, ubicacion);							
						}				
					}	
				}, 'json');
			} else { 
				//Quiere decir que seleccino una opción vacía y se debe liberar cualquier ubicación que haya podido tener ocupada
				$.post("pantallaAdmisionSala.php",
					{
					consultaAjax:      '',
					accion:            'liberarUbicaciones',
					solucionCitas	:  solucionCitas,
					}, function(data){
						if(data.Error)	{
							jAlert(data.Mensaje, "Alerta");
						}
					}, 'json');
			}
        } else {
            $("td[id^='tdAutorizacion_']").hide();
            $("td[id^='tdAdmision_']").hide();
            $("td[id^='tdAtencion_']").hide();
            $("td[id^='tdProcedimiento_']").hide();
            $("td[id^='tdRecuperacion_']").hide();
            $("td[id^='tdBiopsia_']").hide();
            $("td[id^='tdResultados_']").hide();
            $("td[id^='tdTerminarAtencion_']").hide();
            $("td[id^='tdCancela_']").hide();
            $("td[id^='tdno_asiste_']").hide();
            $("#selectUbicacion").hide();	
			$("td[id^='tdEstadoProceso_']").hide();
			$("td[id^='tdLlamadoMedico_']").hide();
			$("td[id^='tdquitar_agenda_']").hide();
			$("td[id^='tdhistoria_clinica_']").hide();
			$("td[id^='tdcancelaPacienteSedado_']").hide();
			$("td[id^='tdFinalizaPacienteSinSedacion_']").hide();
        }
    }
	
	
	function asignarUbicacion(monitor, solucionCitas, ubicacion ){
		if(monitor == '01')
            {
                $("#permitirVerListaTurnos").val(true);
                listarPacientesConTurno();
            }
            else
            {
                $("#tablaListaTurnos").hide();
            }

            $.post("pantallaAdmisionSala.php",
            {
                consultaAjax:           '',
                accion:                 'crearArrayPerfilxProceso',
                solucionCitas:          solucionCitas,
                monitor:                monitor
            }, function(data){

                if(data.Error)
                {
                    alert(data.Mensaje);
                }
                else
                {
                	if(ubicacion != "00" && ubicacion != "")
                	{
	                    $("td[id^='tdAutorizacion_']").hide();
	                    $("td[id^='tdAdmision_']").hide();
	                    $("td[id^='tdAtencion_']").hide();
	                    $("td[id^='tdProcedimiento_']").hide();
	                    $("td[id^='tdRecuperacion_']").hide();
	                    $("td[id^='tdBiopsia_']").hide();
	                    $("td[id^='tdResultados_']").hide();
	                    $("td[id^='tdTerminarAtencion_']").hide();
	                    $("td[id^='tdCancela_']").hide();
	                    $("td[id^='tdno_asiste_']").hide();
	                    $("td[id^='tdLlamadoMedico_']").hide();
	                    $("td[id^='tdquitar_agenda_']").hide();
	                    $("td[id^='tdhistoria_clinica_']").hide();
	                    $("td[id^='tdcancelaPacienteSedado_']").hide();
	                    $("td[id^='tdFinalizaPacienteSinSedacion_']").hide();
												
						//***Veronica
						$("td[id^='tdEstadoProceso_']").hide();

	                    for (var perfil in data.Arreglo)
	                    {
	                        var arregloNivel2 = data.Arreglo[perfil];
	                        for (var columna in arregloNivel2)
	                        {
	                            if((perfil == '*') || (perfil == monitor))
	                            {
	                                if(arregloNivel2[columna] == '01')
	                                {
	                                    $("td[id^='tdAutorizacion_']").show();
	                                } if(arregloNivel2[columna] == '02') {
	                                    $("td[id^='tdAdmision_']").show();
	                                } else if(arregloNivel2[columna] == '03') {
	                                    $("td[id^='tdAtencion_']").show();
	                                } else if(arregloNivel2[columna] == '04') {
	                                    //
	                                } else if(arregloNivel2[columna] == '05') {
	                                    $("td[id^='tdProcedimiento_']").show();
	                                } else if(arregloNivel2[columna] == '06') {
	                                    $("td[id^='tdRecuperacion_']").show();
	                                } else if(arregloNivel2[columna] == '07') {
	                                    $("td[id^='tdBiopsia_']").show();
	                                } else if(arregloNivel2[columna] == '08') {
	                                    $("td[id^='tdResultados_']").show();
	                                } else if(arregloNivel2[columna] == '09') {
	                                    $("td[id^='tdTerminarAtencion_']").show();
	                                } else if(arregloNivel2[columna] == '10') {
	                                    $("td[id^='tdCancela_']").show();
	                                } else if(arregloNivel2[columna] == '11') {
	                                    $("td[id^='tdno_asiste_']").show();
	                                }else if(arregloNivel2[columna] == '12') {
	                                    $("td[id^='tdEstadoProceso_']").show();
	                                }else if(arregloNivel2[columna] == '13') {
	                                    $("td[id^='tdLlamadoMedico_']").show();
	                                }else if(arregloNivel2[columna] == '14') {
	                                    $("td[id^='tdquitar_agenda_']").show();
	                                }else if(arregloNivel2[columna] == '15') {
	                                    $("td[id^='tdhistoria_clinica_']").show();
	                                }else if(arregloNivel2[columna] == '16') {
	                                    $("td[id^='tdcancelaPacienteSedado_']").show();
	                                }else if(arregloNivel2[columna] == '17') {
	                                    $("td[id^='tdFinalizaPacienteSinSedacion_']").show();
	                                }
	                            }
	                        }
	                    }
	                }
                }

            }, 'json');
	}
	

    //Autor: Eimer Castro
    //Fecha: 2016-03-01
    //Esta función crea el array y las opciones de las ubicaciones donde se encuentra algún usuario.
    //Sólo se mostrará para el día actual.
	function crearArrayPerfilxUbicacion(monitor, solucionCitas, ubicacionSeleccionada)
	{
		var fechaActual = new Date();
		var diaHoy = (fechaActual.getDate() < 10) ? "0" + fechaActual.getDate() : fechaActual.getDate();
		var mesHoy = (fechaActual.getMonth() < 10) ? "0" + (fechaActual.getMonth() + 1) : (fechaActual.getMonth() + 1);
		var fechaHoy = fechaActual.getFullYear() + "-" + mesHoy + "-" + diaHoy;
		if(monitor != '')
		{
			$.post("pantallaAdmisionSala.php",
	        {
	            consultaAjax: 			'',
	            accion: 				'crearArrayPerfilxUbicacion',
            	solucionCitas: 			solucionCitas,
	            monitor: 				monitor,
	            ubicacionSeleccionada: 	ubicacionSeleccionada
	        }, function(data){

	            if(data.Error) {
	                alert(data.Mensaje);
					
	            } else {
	            	$("#Ubicaciones").html(data.Ubicaciones);
	            	if($("#wfec").val() == fechaHoy || $("#wfec").val() == "")
	            	{
	            		$("#selectUbicacion").show();
	            	} else {
	            		$("#selectUbicacion").hide();
	            	}
	            }
	        }, 'json').done(function() {
				//si tiene valor para ubicación se selecciona ubicacion
				var valUbicacion = $("#valorUbicacion").val();
				if(valUbicacion != ""){
					$("#Ubicaciones").val(valUbicacion);
					$("#Ubicaciones").trigger("onchange");
				}
	        				if(ubicacionSeleccionada != '00' && ubicacionSeleccionada != '')
	        				{
	        					$("#Ubicaciones").trigger("onchange");
	        				}
	        });				
		}
	}

	function consultarUbicacion()
	{
		return $("#ubicacion").val();
	}

	//Fecha creación: 2016-02-11
	//Autor: Eimer Castro
	//Esta función realiza la impresión del turno para el paciente que tiene cita.
    function imprimirTurno(fichoTurno)
    {   
        setTimeout(function(){
            $("#fichoTurno").html("");
        }, 6000);

        // --> Imprimir tiquete de turno.
        setTimeout(function(){
            var contenido   = "<html><body onload='window.print();window.close();'>";
            contenido       = contenido + fichoTurno + "</body></html>";

            var windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=1,height=1,resizable=yes,screenX=1,screenY=1,personalbar=no,scrollbars=no";
            var ventana = window.open( "", "",  windowAttr );
            ventana.document.write(contenido);
            ventana.document.close();
        }, 1000);
    }

	//Fecha creación: 2016-02-11
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la creación del registro en la tabla de logs citasen_000023
	//y adicionalmente realiza la impresión del turno para el paciente que tiene cita.
    function autorizacion(idAutorizacion, cedulaPaciente, nombrePaciente, solucionCitas) {
        $.post("pantallaAdmisionSala.php",
        {
            consultaAjax:           '',
            accion:                 'autorizacion',
            cedulaPac:         		cedulaPaciente,
            nombrePac:         		nombrePaciente,
            solucionCitas:     		solucionCitas
        }, function(data){

            if(data.Error) {
                alert(data.Mensaje);
            } else {
                imprimirTurno(data.fichoTurno);
                $("#" + idAutorizacion).attr("disabled", "disabled");
            }

        }, 'json');
    }

	//Fecha creación: 2016-03-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
	//de los campos del llamado a admisiónde los turnos que no tiene cita.
    function llamarPacienteAdmisionSinCita(idLlamadoAdmision, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion) {
    	if(ubicacion)
    	{
    		var idtd = idLlamadoAdmision.split("_", 2);
    		var turno = $("#tdOpcionesTurnoSinCita_" + idtd[1]).attr("turno");
	        $.post("pantallaAdmisionSala.php",
	        {
	            consultaAjax:           '',
	            accion:                 'llamarPacienteAdmisionSinCita',
	            idLlamadoAdmision: 		idLlamadoAdmision,
	            cedulaPac:         		cedulaPaciente,
	            nombrePac:         		nombrePaciente,
	            solucionCitas:     		solucionCitas,
	            ubicacion: 				ubicacion,
	            turnoPac: 				turno
	        }, function(data){

	            if(data.Error)
	            {
	                jAlert(data.Mensaje, "Alerta");
	            }
	            else
	            {
					$("#tdrTurno_" + turno).attr("blink", "true");
					$("td[blink=true]").toggleClass("blinkProcesoActual");
	            }

	        }, 'json');
	    }
	    else
	    {
	    	jAlert("Debe seleccionar una ubicaci&oacute;n antes de realizar el llamado.", "Alerta");
	    }
    }

	//Fecha creación: 2016-02-12
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la actualización del registro en la tabla de logs citasen_000023
	//de los campos del llamado a admisión de los turnos que tienen cita.
    function llamarPacienteAdmision(idLlamadoAdmision, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion) {
    	if(ubicacion)
    	{
	        $.post("pantallaAdmisionSala.php",
	        {
	            consultaAjax:           '',
	            accion:                 'llamarPacienteAdmision',
	            idLlamadoAdmision: 		idLlamadoAdmision,
	            cedulaPac:         		cedulaPaciente,
	            nombrePac:         		nombrePaciente,
	            solucionCitas:     		solucionCitas,
	            ubicacion: 				ubicacion
	        }, function(data){

	            if(data.Error)
	            {
	                jAlert(data.Mensaje, "Alerta");
	            }
	            else
	            {	          
			        var idAdmision = idLlamadoAdmision.split("_", 2);
					$("#tdAdmision_" + idAdmision[1]).attr("blink", "true");
					$("td[blink=true]").toggleClass("blinkProcesoActual");
					$("#rdAdmision_" + idAdmision[1]).prop("disabled", false);
					
					$("#rdFinalizarAdmision_" + idAdmision[1]).prop("disabled", false);
	            }

	        }, 'json');
	    }
	    else
	    {
	    	jAlert("Debe seleccionar una ubicaci&oacute;n antes de realizar el llamado.", "Alerta");
	    }
    }

	//Fecha creación: 2016-02-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la admisión de la persona desde el programa de
	//admisiones_erp.php e inmediatamente se realiza la actualización del registro en la tabla de
	//logs citasen_000023 de los campos del inicio de la admisión.
    function admision(idAdmision, tipoDocumentoPaciente, cedulaPaciente, nombrePaciente, solucionCitas, agendaMedica) {
		var wemp_pmla = $('#wemp_pmla').val();
        $.post("pantallaAdmisionSala.php",
        {
            consultaAjax:           '',
            accion:                 'admision',
            idAdmision: 			idAdmision,
            cedulaPac:         		cedulaPaciente,
            nombrePac:         		nombrePaciente,
            solucionCitas:     		solucionCitas,
            tipDocPaciente: 		tipoDocumentoPaciente,
			wemp_pmla: 				wemp_pmla
        }, function(data){

            if(data.Error)
            {
                jAlert(data.Mensaje, "Alerta");
                $("#" + idAdmision).prop("checked", false);
            } else {
            	var path = "../../admisiones/procesos/admision_erp.php?wemp_pmla="+wemp_pmla+"&TipoDocumentoPacAm="+ "&DocumentoPacAm=" + cedulaPaciente + "&TurnoEnAm=" + data.Turno + "&AgendaMedica=" + agendaMedica + "&solucionCitas=" + solucionCitas;
            	window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
                $("#" + idAdmision).attr("disabled", "disabled");
                var idTdAdmision = idAdmision.split("_", 2);
        						
				$("#tdEstadoProceso_" + cedulaPaciente).removeAttr("blink");	
				$("#tdEstadoProceso_" + cedulaPaciente).text("Admision iniciada");	
            }
        }, 'json');
    }

    //Fecha creación: 2016-03-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar el apagado al llamado para la admisión de un paciente sin cita.
    function apagarLlamarPacienteAdmisionSinCita(idApagarLlamadoAdmision, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion) {

        var idtd = idApagarLlamadoAdmision.split("_", 2);
		var turno = $("#tdOpcionesTurnoSinCita_" + idtd[1]).attr("turno");

        $.post("pantallaAdmisionSala.php",
        {
            consultaAjax:           '',
            accion:                 	'apagarLlamarPacienteAdmisionSinCita',
            idApagarLlamadoAdmision: 	idApagarLlamadoAdmision,
            cedulaPac:         			cedulaPaciente,
            nombrePac:         			nombrePaciente,
            solucionCitas:     			solucionCitas,
            turno: 						turno
        }, function(data){

            if(data.Error) {
                jAlert(data.Mensaje, "Alerta");
            } else {
        		$("#tdrTurno_" + turno).removeAttr("blink").removeClass("blinkProcesoActual");
            }
        }, 'json');
    }

    //Fecha creación: 2016-03-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar el apagado al llamado para la admisión de un paciente sin cita.
    function apagarLlamarPacienteAdmisionConCita(idApagarLlamadoAdmision, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion) {

        $.post("pantallaAdmisionSala.php",
        {
            consultaAjax:           '',
            accion:                 	'apagarLlamarPacienteAdmisionConCita',
            idApagarLlamadoAdmision: 	idApagarLlamadoAdmision,
            cedulaPac:         			cedulaPaciente,
            nombrePac:         			nombrePaciente,
            solucionCitas:     			solucionCitas
        }, function(data){

            if(data.Error)
            {
                jAlert(data.Mensaje, "Alerta");
            }
            else
            {                
                var idTdAdmision = idApagarLlamadoAdmision.split("_", 2);
        		$("#tdAdmision_" + idTdAdmision[1]).removeAttr("blink").removeClass("blinkProcesoActual");
            }

        }, 'json');
    }

	//2016-05-31 Verónica Arismendy función que actualiza los campos cuando las secretarias den por terminado la admisión en unix
	// y esto finaliza por completo el proceso de admsión
	function finalizarAdmision(idApagarLlamadoAdmision, idFila, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion){
		
		//Existe la posibilidad de que la admisión no se finalice porque el paciente no requeria admision pero los demás procesos si		
		var msj = "Recuerde que una vez finalice este proceso no podr&aacute; abrir nuevamente la admisi&oacute;n. <br> Est&aacute; completamente seguro de finalizarlo?";
		
		jConfirm(msj,"Confirmacion", function(r) {  
			if(r) {
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:           '',
					accion:                 'finalizarAdmision',
					idFila: 				idFila,
					cedulaPac:         		cedulaPaciente,
					solucionCitas:     		solucionCitas,
					empresa:     			$("#wemp_pmla").val(),
					wemp_pmla: 				$('#wemp_pmla').val()
				}, function(data){

					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
						$("#rdFinalizarAdmision_" + idFila).prop("checked", false);
					}
					else
					{   
						jAlert(data.Mensaje, "Alerta");
						$("#tdAdmision_" + idFila).css("background-color", "#96FF96");
						$("#rdFinalizarAdmision_" + idFila).attr("disabled", "disabled");
						$("#rdFinalizarAdmision_" + idFila).prop("checked", true);
						$("#rdAdmision_" + idFila).attr("disabled", "disabled");
					}

				}, 'json');
			}else{
				$("#rdFinalizarAdmision_" + idFila).prop("checked", false);
			}
		});
	}	
	
	
	//Fecha creación: 2016-02-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar al llamado atención del paciente e
	//inmediatamente se realiza la actualización del registro en la tabla de
	//logs citasen_000023 de los campos del llamado a atención.
    function llamarPacienteAtencion(idLlamadoAtencion, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion) {
    	if(ubicacion)
    	{
	        $.post("pantallaAdmisionSala.php",
	        {
	            consultaAjax:           '',
	            accion:                 'llamarPacienteAtencion',
	            idLlamadoAtencion: 		idLlamadoAtencion,
	            cedulaPac:         		cedulaPaciente,
	            nombrePac:         		nombrePaciente,
	            solucionCitas:     		solucionCitas,
	            ubicacion: 		ubicacion
	        }, function(data){

	            if(data.Error) {
	                jAlert(data.Mensaje, "Alerta");
	                $("#" + idLlamadoAtencion).attr("checked", "");
	            } else {	               
			        var idAtencion = idLlamadoAtencion.split("_", 2);
					$("#tdAtencion_" + idAtencion[1]).attr("blink", "true");
					$("td[blink=true]").toggleClass("blinkProcesoActual");
					
					$("#finPreparacion_" + idAtencion[1]).prop("disabled", false);
	            }
	        }, 'json');
	    } else {
	    	jAlert("Debe seleccionar una ubicaci&oacute;n antes de realizar el llamado.", "Alerta");
	    }
    }

	//Fecha creación: 2016-02-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar al llamado atención del paciente e
	//inmediatamente se realiza la actualización del registro en la tabla de
	//logs citasen_000023 de los campos del llamado a atención.
    function apagarLlamarPacienteAtencion(idApagarLlamadoAtencion, cedulaPaciente, nombrePaciente, solucionCitas) {
        $.post("pantallaAdmisionSala.php",
        {
            consultaAjax:           '',
            accion:                 	'apagarLlamarPacienteAtencion',
            idApagarLlamadoAtencion: 	idApagarLlamadoAtencion,
            cedulaPac:         			cedulaPaciente,
            nombrePac:         			nombrePaciente,
            solucionCitas:     			solucionCitas
        }, function(data){

            if(data.Error) {
                jAlert(data.Mensaje, "Alerta");
            } else {              
                var idTdAtencion = idApagarLlamadoAtencion.split("_", 2);
        		$("#tdAtencion_" + idTdAtencion[1]).removeAttr("blink");				
            }
        }, 'json');
    }

    //Fecha creación: 2016-02-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la actualización del registro en la tabla
	//citasen_000023 de acuerdo a si se iniciará o se finalizará el procedimiento.
    function inicioProcedimiento(idInicioProcedimiento, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion) {
    	if(ubicacion)
    	{
			
	        $.post("pantallaAdmisionSala.php",
	        {
	            consultaAjax:           '',
	            accion:                 'inicioProcedimiento',
	            idInicioProcedimiento: 	idInicioProcedimiento,
	            cedulaPac:          	cedulaPaciente,
	            nombrePac:          	nombrePaciente,
	            solucionCitas:     		solucionCitas,
	            ubicacion: 				ubicacion
	        }, function(data){

	            if(data.Error)
	            {
	                jAlert(data.Mensaje, "Alerta");
	            	$("#rdInicioProcedimiento_" + cedulaPaciente).prop("checked", false);
	            }
	            else
	            {
					var idProcedimiento = idInicioProcedimiento.split("_", 2);
					if(data.Estado == "iniciarProcedimiento")
					{
		                $("#rdInicioProcedimiento_" + cedulaPaciente).attr("disabled", "disabled");
						$("#tdProcedimiento_" + idProcedimiento[1]).attr("blink", "true");
						$("td[blink=true]").toggleClass("blinkProcesoActual");
												
						$("#rdFinProcedimiento_" + cedulaPaciente).removeAttr("disabled");
						$("#rdFinProcedimiento_" + cedulaPaciente).prop("checked", false);						
					}
					else 
					{
						$("#div_reiniciar_procedimiento" ).html(data.Mensaje);
						$("#div_reiniciar_procedimiento" ).dialog({
							"closeOnEscape": false,
							show: {
								effect: "blind",
								duration: 100
							},
							hide: {
								effect: "blind",
								duration: 100
							},
							height: 'auto',
							width:  'auto',
							buttons: {
								"Reiniciar procedimiento": {
									text: "Reiniciar procedimiento",
									id: "reiniciar",
									click: function(){
										reiniciarProcedimiento(cedulaPaciente, solucionCitas, ubicacion, data.turno, idProcedimiento[1], "reiniciaPro");
										$( this ).dialog( "close" );
									} 							 
								} ,
								"Iniciar nuevo procedimiento": {
									text: "Iniciar nuevo procedimiento",
									id: "iniciarNuevo",
									click: function(){
										reiniciarProcedimiento(cedulaPaciente, solucionCitas, ubicacion, data.turno, idProcedimiento[1], "nuevoPro");
										$( this ).dialog( "close" );
									} 							 
								} ,
								"Cancelar": {
									text: "Cancelar",
									id: "cerrar",
									click: function(){
										 $( this ).dialog( "close" );
									} 							 
								}
							},
							dialogClass: 'fixed-dialog',
							modal: true,
							title: "Elegir una opci&oacute;n para continuar",
							beforeClose: function( event, ui ) {
								$("#rdInicioProcedimiento_"+cedulaPaciente).prop("checked", false);
							},
							create: function() {
							   $(this).closest('.ui-dialog').on('keydown', function(ev) {
								   if (ev.keyCode === $.ui.keyCode.ESCAPE) {
									   $("#div_reiniciar_procedimiento" ).dialog('close');
								   }
							   });
							}
						}).on("dialogopen", function( event, ui ) {
						  
						});
						
						//Se valida si es reinicio o nuevo procedimiento para habilitar los botones
						if(data.procNuevo){
							$("#iniciarNuevo").show();
						}else{
							$("#iniciarNuevo").hide();
						}					
					}
	            }
	        }, 'json');
		}
	    else
	    {
	        $("#rdInicioProcedimiento_" + cedulaPaciente).prop("checked", false);
	    	jAlert("Debe seleccionar una ubicaci&oacute;n antes de realizar el llamado.", "Alerta");
	    }
    }
	
	//Función que reinicia un procedimiento o si desea empezar uno nuevo para un paciente con doble cita para procedimiento diferente.
	function reiniciarProcedimiento(cedulaPaciente, solucionCitas, ubicacion, turno, idFila, tipoReinicio){
			
		$.post("pantallaAdmisionSala.php",
		{
			consultaAjax:           '',
			accion:                 'reiniciarProcedimiento',
			cedulaPac:          	cedulaPaciente,
			solucionCitas:     		solucionCitas,
			ubicacion: 				ubicacion,
			turno: 					turno,
			tipoReinicio:			tipoReinicio
			}, function(data){
				if(data.Error)
				{
					jAlert(data.Mensaje, "Alerta");
					$("#" + idInicioProcedimiento).prop("checked", false);
				} else {
					$("#rdFinProcedimiento_" + cedulaPaciente).removeAttr("disabled");
					$("#rdFinProcedimiento_" + cedulaPaciente).prop("checked", false);					
					$("#tdProcedimiento_" + idFila).attr("blink", "true").css("background-color", "");					
				}
		}, 'json');
	}
	
    //Fecha creación: 2016-02-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la actualización del registro en la tabla
	//citasen_000023 para fuardar la información del fin del procedimiento.
    function finProcedimiento(idFinProcedimiento, cedulaPaciente, nombrePaciente, solucionCitas) {
        $.post("pantallaAdmisionSala.php",
        {
            consultaAjax:           '',
            accion:                 'finProcedimiento',
            idFinProcedimiento: 	idFinProcedimiento,
            cedulaPac:          	cedulaPaciente,
            nombrePac:          	nombrePaciente,
            solucionCitas:     		solucionCitas
        }, function(data){

            if(data.Error)
            {
                jAlert(data.Mensaje, "Alerta");
				$("#" + idFinProcedimiento).prop("checked", false);
            }
            else
            {
                $("#" + idFinProcedimiento).attr("disabled", "disabled");
                var idInicioProcedimiento = idFinProcedimiento.split("_", 2);
				$("#rdInicioProcedimiento_" + cedulaPaciente).prop("checked", false);
				$("#rdInicioProcedimiento_" + cedulaPaciente).prop("disabled", false);
				
        		$("#tdProcedimiento_" + idInicioProcedimiento[1]).removeAttr("blink").css("background-color", "#96FF96");
            }

        }, 'json');
    }

    //Fecha creación: 2016-02-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la actualización del registro en la tabla
	//citasen_000023 para indicar el inicio de la recuperación del paciente.
    function inicioRecuperacion(idInicioRecuperacion, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion) {
    	if(ubicacion)
    	{
	        $.post("pantallaAdmisionSala.php",
	        {
	            consultaAjax:           '',
	            accion:                 'inicioRecuperacion',
	            idInicioRecuperacion: 	idInicioRecuperacion,
	            cedulaPac:          	cedulaPaciente,
	            nombrePac:          	nombrePaciente,
	            solucionCitas:     		solucionCitas,
	            ubicacion: 		ubicacion
	        }, function(data){

	            if(data.Error)
	            {
	                jAlert(data.Mensaje, "Alerta");
					$("#" + idInicioRecuperacion).prop("checked", false);
	            }
	            else
	            {
				    var idRecuperacion = idInicioRecuperacion.split("_", 2);
					if(data.Estado == "iniciarRecuperacion")
					{
		                $("#" + idInicioRecuperacion).attr("disabled", "disabled");						
						$("#rdFinRecuperacion_" + idRecuperacion[1]).removeAttr("disabled");
						$("#rdFinRecuperacion_" + idRecuperacion[1]).removeAttr("checked");
					}
					else if(data.Estado == "corregirRecuperacion")
					{
						$("#rdFinRecuperacion_" + idRecuperacion[1]).removeAttr("checked");
						$("#rdFinRecuperacion_" + idRecuperacion[1]).attr("clave", "");
						$("#rdFinRecuperacion_" + idRecuperacion[1]).removeAttr("disabled");
						$("#tdRecuperacion_" + idRecuperacion[1]).attr("blink", "true").css("background-color", "");
					}
					else
					{
	        			$("#tdRecuperacion_" + idRecuperacion[1]).removeAttr("blink").css("background-color", "#96FF96");
					}
	            }
	        }, 'json');
		}
	    else
	    {
			$("#" + idInicioRecuperacion).prop("checked", false);
	    	jAlert("Debe seleccionar una ubicaci&oacute;n antes de realizar el llamado.", "Alerta");
	    }
    }

    //Fecha creación: 2016-02-15
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la actualización del registro en la tabla
	//citasen_000023 para indicar el inicio de la recuperación del paciente.
    function finRecuperacion(idFinRecuperacion, idFila, cedulaPaciente, solucionCitas, ubicacion) {
    	
	    $.post("pantallaAdmisionSala.php",
	    {
	            consultaAjax:           '',
	            accion:                 'finRecuperacion',
	            idFila: 				idFila,
	            cedulaPac:          	cedulaPaciente,
	            ubicacion:          	ubicacion,
            	solucionCitas:     		solucionCitas
	    }, function(data){

	            if(data.Error) {
	                jAlert(data.Mensaje, "Alerta");
					$("#" + idFinRecuperacion+""+idFila).prop("checked", false);
					
	            } else {
					//Se deshabilita checkbox de finalizar recuperacion	
					$("#" + idFinRecuperacion+""+idFila).attr("checked", "checked");
					$("#" + idFinRecuperacion+""+idFila).prop("disabled", true);	

					//Se marca el proceso de recuperacion com finalizado
					$("#tdRecuperacion_" + idFila).css("background-color", "#96FF96");			

					//Se deshabilitan los botones de llamado del acompañante
					$("#imgLlamadoAcompanante_" + idFila).prop("disabled", true);			
					$("#imgApagarLlamadoAcompanante_" + idFila).prop("disabled", true);			
	            }
	    }, 'json');
    }

	//Fecha creación: 2016-02-29
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar el llamado para la entrega de la biopsia al
	//paciente e inmediatamente se realiza la actualización del registro en la tabla de
	//logs citasen_000023 de los campos del llamado a entrega de biopsia.
    function llamarPacienteBiopsia(idLlamadoBiopsia, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion) {
    	if(ubicacion)
    	{
	        $.post("pantallaAdmisionSala.php",
	        {
	            consultaAjax:           '',
	            accion:                 'llamarPacienteBiopsia',
	            idLlamadoBiopsia: 		idLlamadoBiopsia,
	            cedulaPac:         		cedulaPaciente,
	            nombrePac:         		nombrePaciente,
	            solucionCitas:     		solucionCitas,
	            ubicacion: 				ubicacion
	        }, function(data){

	            if(data.Error) {
	                jAlert(data.Mensaje, "Alerta");
	                $("#" + idLlamadoBiopsia).attr("checked", "");
					
	            } else {	                
			        var idBiopsia = idLlamadoBiopsia.split("_", 2);
					$("#tdBiopsia_" + idBiopsia[1]).attr("blink", "true");
					$("td[blink=true]").toggleClass("blinkProcesoActual");					
					$("#finalizarEntregaBiopsia_" + idBiopsia[1]).attr("disabled",  false);
	            }
	        }, 'json');
	    }
	    else
	    {
	    	jAlert("Debe seleccionar una ubicaci&oacute;n antes de realizar el llamado.", "Alerta");
	    }
    }

	//Fecha creación: 2016-02-29
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar el apagado al llamado para la entrega de la biopsia al
	//paciente e inmediatamente se realiza la actualización del registro en la tabla de
	//logs citasen_000023 de los campos del apagado al llamado a entrega de biopsia.
    function apagarLlamarPacienteBiopsia(idApagarLlamadoBiopsia, cedulaPaciente, nombrePaciente, solucionCitas) {
        $.post("pantallaAdmisionSala.php",
        {
			
            accion:                 	'apagarLlamarPacienteBiopsia',
            idApagarLlamadoBiopsia: 	idApagarLlamadoBiopsia,
            cedulaPac:         			cedulaPaciente,
            nombrePac:         			nombrePaciente,
            solucionCitas:     			solucionCitas
        }, function(data){

            if(data.Error) {
                jAlert(data.Mensaje, "Alerta");
            } else {              
                var idTdBiopsia = idApagarLlamadoBiopsia.split("_", 2);
        		$("#tdBiopsia_" + idTdBiopsia[1]).removeAttr("blink");
				$("td[blink=true]").toggleClass("blinkProcesoActual");
            }
        }, 'json');
		
    }

	//Fecha creación: 2016-02-29
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar el llamado para la entrega de la biopsia al
	//paciente e inmediatamente se realiza la actualización del registro en la tabla de
	//logs citasen_000023 de los campos del llamado a entrega de biopsia.
    function llamarPacienteResultados(idLlamadoResultados, cedulaPaciente, nombrePaciente, solucionCitas, ubicacion, IdTd) {
    	if(ubicacion)
    	{
	        $.post("pantallaAdmisionSala.php",
	        {
	            consultaAjax:           '',
	            accion:                 'llamarPacienteResultados',
	            idLlamadoResultados: 	idLlamadoResultados,
	            cedulaPac:         		cedulaPaciente,
	            nombrePac:         		nombrePaciente,
	            solucionCitas:     		solucionCitas,
	            ubicacion: 				ubicacion
	        }, function(data){

	            if(data.Error)
	            {
	                jAlert(data.Mensaje, "Alerta");				
	            } else {	
			        var idResultados = idLlamadoResultados.split("_", 2);
					$("#tdResultados_" + idResultados[1]).attr("blink", "true");
					$("td[blink=true]").toggleClass("blinkProcesoActual");
					
					$("#finEntregaResultados_" + idResultados[1]).attr("disabled",  false);
	            }
	        }, 'json');
	    }
	    else
	    {
	    	jAlert("Debe seleccionar una ubicaci&oacute;n antes de realizar el llamado.", "Alerta");
	    }
    }

	//Fecha creación: 2016-02-29
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar el apagado al llamado para la entrega de la Resultados al
	//paciente e inmediatamente se realiza la actualización del registro en la tabla de
	//logs citasen_000023 de los campos del apagado al llamado a entrega de Resultados.
    function apagarLlamarPacienteResultados(idApagarLlamadoResultados, cedulaPaciente, nombrePaciente, solucionCitas) {
        $.post("pantallaAdmisionSala.php",
        {
            consultaAjax:           '',
            accion:                 	'apagarLlamarPacienteResultados',
            idApagarLlamadoResultados: 	idApagarLlamadoResultados,
            cedulaPac:         			cedulaPaciente,
            nombrePac:         			nombrePaciente,
            solucionCitas:     			solucionCitas
        }, function(data){

            if(data.Error)
            {
                jAlert(data.Mensaje, "Alerta");
            } else {              
                var idTdResultados = idApagarLlamadoResultados.split("_", 2);
        		$("#tdResultados_" + idTdResultados[1]).removeAttr("blink");
				$("td[blink=true]").toggleClass("blinkProcesoActual");
            }
        }, 'json');
    }

	/*
	* Verónica Arismendy 2016-05-16 
	* Funcion que hace por ajax el envio de datos para registrar los datos de 
	* la entrega de biopsia o limpíarlos en caso de que se desseleccione el checkbox
	*/	
	function registraEntregaBiopsia(idEntregarBiopsia, idFila, cedulaPaciente, solucionCitas, ubicacion) {
        
		jConfirm("\xBFEst&aacute; seguro de dar por finalizada la entrega de biopsia?", "Confirmaci\xF3n", function(respuesta) {
		    if(respuesta)
		    {
		        $.post("pantallaAdmisionSala.php",
				{
					consultaAjax:           '',
					accion:                 'registraEntregaBiopsia',
					cedulaPac:         		cedulaPaciente,
					solucionCitas:     		solucionCitas,
					idFila:     			idFila,
					ubicacion: 				ubicacion
				}, function(data){

					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
						$("#finalizarEntregaBiopsia_" + idFila).prop("checked", false);
					} else {             
						$("#finalizarEntregaBiopsia_" + idFila).prop("disabled", true);
						$("#tdBiopsia_"+idFila).css("background-color", "#96FF96");
						$("#rdLlamadoBiopsia_"+idFila).removeAttr('onclick');
						$("#rdApagadoLlamadoBiopsia_"+idFila).removeAttr('onclick');
					}

				}, 'json');
		    } else
		    {
				$("#finalizarEntregaBiopsia_" + idFila).prop("checked", false);
    		}
    	});
    }
	

    //Fecha creación: 2016-02-16
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la actualización del registro en la tabla
	//citasen_000023 para indicar que terminó la atención al paciente.
    function terminarAtencion(idTerminarAtencion, cedulaPaciente, nombrePaciente, idFila, solucionCitas) {

        jConfirm("\xBFDesea terminar la atenci\xF3n?", "Confirmaci\xF3n", function(respuesta) {
		    if(respuesta)
		    {
		        $.post("pantallaAdmisionSala.php",
		        {
		            consultaAjax:           '',
		            accion:                 'terminarAtencion',
		            idTerminarAtencion: 	idTerminarAtencion,
		            cedulaPac:          	cedulaPaciente,
		            nombrePac:          	nombrePaciente,
		            solucionCitas:     		solucionCitas,
					centroCosto: 			$("#ccosto").val(),
					empresa:				$("#wemp_pmla").val(),
					wemp_pmla: 				$('#wemp_pmla').val()
		        }, function(data){

		            if(data.Error)
		            {
		                jAlert(data.Mensaje, "Alerta");
						$("#" + idTerminarAtencion).prop("checked", false);
		            }
		            else
		            {
		                $("#" + idTerminarAtencion).attr("disabled", "disabled");
						var idTd = idTerminarAtencion.split("_", 2);
						$("#tdTerminarAtencion_" + idTd[1]).removeAttr("blink");					
														
						//Se da el mensaje de que ya se hizo la solicitud 							
						jAlert(data.msjCamillero, "Alerta");								
		            }
		        }, 'json').done(function () {
		               
		        });
		    } else {
				$("#" + idTerminarAtencion).prop("checked", false);
    		}
    	});
    }

    function listarPacientesConTurno()
	{
		var fechaActual = new Date();
		var diaHoy = (fechaActual.getDate() < 10) ? "0" + fechaActual.getDate() : fechaActual.getDate();
		var mesHoy = (fechaActual.getMonth() < 10) ? "0" + (fechaActual.getMonth() + 1) : (fechaActual.getMonth() + 1);
		var fechaHoy = fechaActual.getFullYear() + "-" + mesHoy + "-" + diaHoy;

		$.post("pantallaAdmisionSala.php",
		{
			consultaAjax:   		'',
			accion:         		'listarPacientesConTurno',
			solucionCitas: 			$('#solucionCitas').val(),
			caso: 					$('#caso').val(),
			wemp_pmla: 				$('#wemp_pmla').val()

		}, function(data){
			if(data.Error)
			{				
				$("#divListaPacientesConTurno").html("");
			}
			else
			{
				$("#divListaPacientesConTurno").html("");
				if($("#wfec").val() == fechaHoy || $("#wfec").val() == "")
	            {
					$("#divListaPacientesConTurno").html(data.html);
					mostrarPacientesConCita();
				}
			}
		}, 'json').done(
					mostrarPacientesConCita(),
					function() {
						$(".filaDraggable").draggable({
							revert: true,
							helper: 'clone'
						});
					},
					function(){
						$(".filaDroppable").droppable({		
							accept: ".filaDraggable",
							drop: function(ev, ui) {
								var turno          = ui.draggable.find("td[id^=tdrTurno_]").attr("turno");
								var cedulaSinCita  = ui.draggable.find("td[id^=tdrCedula_]").attr("cedula");
								var nombreSinCita  = ui.draggable.find("td[id^=tdrNombre_]").attr("nombre");
								var cedulaAgenda   = $(this).attr("cedula");
								var nombreAgenda   = $(this).attr("nombre");
								var idAgenda     = $(this).attr("idAgenda");
							
								if($(this).find("td[id^=tdAutorizacion_]").attr("turno") === '')
								{
									asociarTurnoCita(turno, cedulaSinCita, nombreSinCita, cedulaAgenda, nombreAgenda, idAgenda);
								}
							}
						});
					});
	}

	function mostrarPacientesConCita()
	{
		$.post("pantallaAdmisionSala.php",
		{
			consultaAjax:   		'',
			accion:         		'mostrarPacientesConCita',
			solucionCitas: 			$('#solucionCitas').val(),
			caso: 					$('#caso').val(),
			wemp_pmla: 				$('#wemp_pmla').val(),
			ubicacion: 				$('#Ubicaciones').val()
		}, function(data){
			if(data.Error){	
		
			}
			else
			{					
				//Se valida si hubo cambios que obliguen a actualizar la pagina para mostrar el div de actualización
				if(data.actualizarMonitor){
					$("#div_actualizar_monitor").css("display", "block");
					$("#mensaje_recargar").html(data.msjActualizarMonitor);
				}else{
					$("#div_actualizar_monitor").css("display", "none");
					$("#mensaje_recargar").html("");
				}
			
				for(var cedulaAgenda in data.ArrayTurnosAgenda)
                {	
	                var id09 = "";
					var estadoActual;
					var iluminarLlamado = false;
					var turnoAgendaP = "";
                	for(var estadoProceso in data.ArrayTurnosAgenda[cedulaAgenda])
                	{							
	                	var turnoAgenda = "";
						
	                	var estadoProcesoActual = data.ArrayTurnosAgenda[cedulaAgenda][estadoProceso];
												
	                	if(estadoProceso == 'id09')
	                	{
	                		id09 = data.ArrayTurnosAgenda[cedulaAgenda][estadoProceso];
	                	}
	                	
	                	if(estadoProceso == 'TurnoAutorizacion')
	                	{
	                		var turnoAgenda = data.ArrayTurnosAgenda[cedulaAgenda][estadoProceso].substr(7, 10);
	                		turnoAgendaP = data.ArrayTurnosAgenda[cedulaAgenda][estadoProceso].substr(7, 10);
	                		$("#tdAutorizacion_" + cedulaAgenda).html("<b>" + turnoAgenda + "<b>").css("background-color", "#96FF96");
	                		$("#tdAutorizacion_" + cedulaAgenda).attr("turno", turnoAgenda);
							
							estadoActual = "En espera de admisi&oacute;n";
	                	}
	                	if(estadoProceso == 'EstadoAdmision')
	                	{							
	                		if(estadoProcesoActual == 0)
	                		{
		                		$("#tdAdmision_" + id09).attr("blink", "true");
								$("#rdAdmision_" + id09).removeAttr("disabled");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
																
								//Validar si ya está iniciando admision
								if ($("#tdEstadoProceso_" + cedulaAgenda).text() === "Admision iniciada"){
									estadoActual = "Admision iniciada";
									iluminarLlamado = false;
								}else{
									estadoActual = "Llamado para admisi&oacute;n en "+data.arrayUbicacionProceso[cedulaAgenda]["loguba"];
									iluminarLlamado = true;
								}								
	                		}
	                		else if(estadoProcesoActual == -1)
	                		{
	                			$("#tdAdmision_" + id09).removeAttr("blink");
	                			$("#tdAdmision_" + id09).removeClass("blinkProcesoActual");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
	                		}							
	                		else
	                		{	                		
								$("#rdAdmision_" + id09).attr("disabled", "disabled");							
								estadoActual = "En admsi&oacute;n en "+data.arrayUbicacionProceso[cedulaAgenda]["loguba"];								
								//Se habilita la opcón de finalizar totalmente el proceso de admsión
								$("#rdFinalizarAdmision_" + id09).attr("disabled", false);								
	                		}
							
							if(data.ArrayTurnosAgenda[cedulaAgenda].EmpezoAdmision){
								$("#tdAdmision_" + id09).removeAttr("blink");
	                			$("#tdAdmision_" + id09).removeClass("blinkProcesoActual");
								$("td[blink=true]").toggleClass("blinkProcesoActual");								
								
								estadoActual = "En admsi&oacute;n en "+data.arrayUbicacionProceso[cedulaAgenda]["loguba"];
								iluminarLlamado = false;
							}													
	                	}
						
						//se debe validar el final final de admisión						
						if(estadoProceso == 'EstadoAdmisionFinalizada')
	                	{							
	                		if(estadoProcesoActual == 0) {
		                									
	                		} else if(estadoProcesoActual == -1) {
	                			
	                		} else {
	                			$("#tdAdmision_" + id09).css("background-color", "#96FF96");
								$("#rdAdmision_" + id09).attr("disabled", "disabled");
								
								$("#rdLlamadoAdmision" + id09).attr("disabled", "disabled");
								$("#rdApagadoLlamadoAdmision" + id09).attr("disabled", "disabled");
								
								
								$("#rdFinalizarAdmision_" + id09).attr("disabled", "disabled");
								$("#rdFinalizarAdmision_" + id09).prop("checked", true);
								
								estadoActual = "Admisi&oacute;n finalizada";
	                		}																			
	                	}
											
						
	                	if(estadoProceso == 'EstadoAtencion')
	                	{
	                		if(estadoProcesoActual == 0)
	                		{
		                		$("#tdAtencion_" + id09).attr("blink", "true");
								$("#rdAtencion_" + id09).removeAttr("disabled");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
								
								estadoActual = "Llamado para iniciar preparacion";
								iluminarLlamado = true;
	                		}
	                		else if(estadoProcesoActual == -1)
	                		{
	                			$("#tdAtencion_" + id09).removeAttr("blink");
	                			$("#tdAtencion_" + id09).removeClass("blinkProcesoActual");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
	                		}
	                		else
	                		{
								$("#tdAtencion_" + id09).removeAttr("blink");
	                			$("#tdAtencion_" + id09).removeClass("blinkProcesoActual");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
								estadoActual = "En preparaci&oacute;n";
	                		}
	                	}
						
						//se debe validar el final final del proceso de atencion						
						if(estadoProceso == 'EstadoAtencionFinalizada')
	                	{							
	                		if(estadoProcesoActual == 1) {		                									
	                			$("#tdAtencion_" + id09).css("background-color", "#96FF96");
								
								$("#rdLlamadoAtencion_" + id09).attr("disabled", "disabled");								
								$("#rdApagadoLlamadoAtencion_" + id09).attr("disabled", "disabled");
								$("#finPreparacion_" + id09).attr("disabled", "disabled");
								$("#finPreparacion_" + id09).prop("checked", "checked");
																
								estadoActual = "Preparaci&oacute;n finalizada";
	                		}																			
	                	}
												
						//ESTADO DEL PROCEDIMIENTO
	                	if(estadoProceso == 'EstadoProcedimiento')
	                	{
							$("#rdInicioProcedimiento_" + cedulaAgenda).prop("checked", false);
							$("#rdInicioProcedimiento_" + cedulaAgenda).prop("disabled", false);
								
	                		if(estadoProcesoActual == 0)
	                		{
		                		$("#tdProcedimiento_" + id09).attr("blink", "true");
								$("#rdProcedimiento_" + id09).removeAttr("disabled");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
								
								$("#rdFinProcedimiento_" + cedulaAgenda).removeAttr("disabled");
								$("#rdFinProcedimiento_" + cedulaAgenda).prop("checked", false);
								$("#tdProcedimiento_" + id09).attr("blink", "true").css("background-color", "");
																
								estadoActual = "Procedimiento iniciado en "+data.arrayUbicacionProceso[cedulaAgenda]["logubp"];
	                		}
	                		else if(estadoProcesoActual == -1)
	                		{
	                			$("#tdProcedimiento_" + id09).css("background-color", "");
	                			$("#tdProcedimiento_" + id09).removeAttr("blink")
	                			$("#tdProcedimiento_" + id09).removeClass("blinkProcesoActual");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
	                		}
	                		else
	                		{	                			
								$("#rdFinProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");	
	                			$("#tdProcedimiento_" + id09).css("background-color", "#96FF96");
								
								estadoActual = "Procedimiento finalizado";
	                		}
	                	}
						
						//ESTADO DEL SEGUNDO PROCEDIMIENTO
	                	if(estadoProceso == 'estadoSegundoProcedimiento')
	                	{
	                		if(estadoProcesoActual == 0)
	                		{
		                		$("#tdProcedimiento_" + id09).attr("blink", "true");
								$("#rdProcedimiento_" + id09).removeAttr("disabled");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
								
								$("#rdFinProcedimiento_" + cedulaAgenda).removeAttr("disabled");
								$("#rdFinProcedimiento_" + cedulaAgenda).prop("checked", false);
								
								$("#tdProcedimiento_" + id09).attr("blink", "true").css("background-color", "");
									
								//Se deshabilita el reinicio del segundo procedimiento									
								$("#rdInicioProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");
								
								estadoActual = "Segundo procedimiento iniciado en "+data.arrayUbicacionProceso[cedulaAgenda]["logubp"];
	                		}
							
	                		if(estadoProcesoActual == 1)
	                		{	                											
	                			$("#rdFinProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");								
	                			$("#tdProcedimiento_" + id09).css("background-color", "#96FF96");
																
								$("#rdInicioProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");	
								$("#rdFinProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");
								
								estadoActual = "Segundo procedimiento finalizado";
	                		}
	                	}	

						//VALIDAR SI EL PACIENTE SALIÓ DE ALTA DESDE Procedimiento
						if(estadoProceso == 'estadoAltaProcimiento')
	                	{
	                		if(estadoProcesoActual == 0){ 
								$("#rdInicioProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");	
								$("#rdFinProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");
								$("#rdFinalizaPacienteSinSedacion_" + cedulaAgenda).prop("checked", "checked");	
								$("#tdFinalizaPacienteSinSedacion_" + id09).css("background-color", "#96FF96");
								
								estadoActual = estadoActual+" (Paciente sin sedaci&oacute;n en espera de resultados)";								
							}
							//Quiere decir que salió de alta desde procedimiento y ya se le entregarón resultados se quita de la lista
							if(estadoProcesoActual == 1){
								$("#rwPacienteAgendado_" + cedulaAgenda).remove();
								$("tr[id^=rwPacienteAgendado_]").removeClass("fila1");
								$("tr[id^=rwPacienteAgendado_]").removeClass("fila2");
								var cont=0;
								$("tr[id^=rwPacienteAgendado_]").each(function(){
																		cont++;
																		var css = (cont % 2 == 0) ? 'fila1': 'fila2';
																		$(this).addClass(css);
																		$(this).find("td").first().html(cont);
							
								});	
							}
	                	}
																		
						//ESTADO DE LA RECUPERACION
	                	if(estadoProceso == 'EstadoRecuperacion')
	                	{
	                		if(estadoProcesoActual == 0)
	                		{ 
								estadoActual = "En recuperaci&oacute;n";
								iluminarLlamado = false;
	                		}
	                		else if(estadoProcesoActual == -1)
	                		{
	                			$("#tdRecuperacion_" + id09).removeAttr("blink");
	                			$("#tdRecuperacion_" + id09).removeClass("blinkProcesoActual");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
								$("#rdRecuperacion_" + id09).removeAttr("disabled");								
	                		}
	                		else
	                		{
	                			$("#tdRecuperacion_" + id09).css("background-color", "#96FF96");								
								estadoActual = "Recuperaci&oacute;n finalizada";
	                		}
	                	}
							

						//VALIDAR SI EL PACIENTE ESTA SEDADO Y SE LE SUSPENDIÓ EL PROCEDIMIENTO
						//PARA ESTE PACIENTE NO SE ENTREGAN RESULTADOS NI BIOPSIA
						if(estadoProceso == 'estadoPacSinProcedimiento')
	                	{
	                		if(estadoProcesoActual == 1){ 		
								$("#rdInicioProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");	
								$("#rdFinProcedimiento_" + cedulaAgenda).attr("disabled", "disabled");								
								$("#rdcancelaPacienteSedado_" + cedulaAgenda).prop("checked", "checked");	
								$("#tdcancelaPacienteSedado_" + id09).css("background-color", "#96FF96");	

								estadoActual = estadoActual+" Procedimiento cancelado. Paciente sedado no debe esperar resultados.";									
							}
	                	}

							
	                	if(estadoProceso == 'EstadoResultados')
	                	{
	                		if(estadoProcesoActual == 0)
	                		{
		                		$("#tdResultados_" + id09).attr("blink", "true");
								$("#rdResultados_" + id09).removeAttr("disabled");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
								
								estadoActual = "Llamando para entrega de resultados";
								iluminarLlamado = true;
	                		}
	                		if(estadoProcesoActual == -1)
	                		{
	                			$("#tdResultados_" + id09).removeAttr("blink");
	                			$("#tdResultados_" + id09).removeClass("blinkProcesoActual");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
	                		}	                		
	                	}
						
						//FINAL DEL PROCESO DE ENTREGA DE BIOPSIA						
						if(estadoProceso == 'EstadoBiopsiaFinalizada')
	                	{							
	                		if(estadoProcesoActual == 1) {	                								
	                			$("#tdBiopsia_" + id09).css("background-color", "#96FF96");
								$("#finalizarEntregaBiopsia_" + id09).prop("checked", "checked");
																
								estadoActual = "" + estadoActual + " (Biopsia entregada)";
	                		}																			
	                	}
						
						//FINAL DEL PROCESO DE ENTREGA DE RESULTADOS				
						if(estadoProceso == 'EstadoResultadosFinalizada')
	                	{							
	                		if(estadoProcesoActual == 1) {	                								
	                			$("#tdResultados_" + id09).css("background-color", "#96FF96");	
							
								$("#finEntregaResultados_" + id09).prop("checked", "checked");
																
								estadoActual = "" + estadoActual + "(Resultados entregados)";    
							}																			
	                	}
												
	                	if(estadoProceso == 'EstadoTerminoAtencion')
	                	{							
							if(estadoProcesoActual == 1){							
								$("#tdTerminarAtencion_" + id09).css("background-color", "#96FF96");
								estadoActual = "En proceso de alta";									
							}
							if(estadoProcesoActual == 0){
								$("#tdTerminarAtencion_" + id09).attr("blink", "true");
								$("#tdTerminarAtencion_" + id09).removeAttr("disabled");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
								
								estadoActual = "Recuperaci&oacute;n finalizada";
							}
	                	}	

						if(estadoProceso == 'EstadoSolicitudCamillero')
	                	{							
							if(estadoProcesoActual == 1){
								$("#rwPacienteAgendado_" + cedulaAgenda).remove();
								$("tr[id^=rwPacienteAgendado_]").removeClass("fila1");
								$("tr[id^=rwPacienteAgendado_]").removeClass("fila2");
								var cont=0;
								$("tr[id^=rwPacienteAgendado_]").each(function(){
																		cont++;
																		var css = (cont % 2 == 0) ? 'fila1': 'fila2';
																		$(this).addClass(css);
																		$(this).find("td").first().html(cont);
							
									});					
							}
	                	}	
                	}					
					
					//Verónica Arismendy	
						$("#tdEstadoProceso_" + cedulaAgenda).html(estadoActual);
						
						if(iluminarLlamado){
							if (! $("#tdEstadoProceso_" + cedulaAgenda).is('[blink]') ){
								$("#tdEstadoProceso_" + cedulaAgenda).attr("blink", "true");
								$("td[blink=true]").toggleClass("blinkProcesoActual");
							}							
						}else{
							$("#tdEstadoProceso_" + cedulaAgenda).removeAttr("blink");
							$("#tdEstadoProceso_" + cedulaAgenda).removeClass("blinkProcesoActual");
							$("td[blink=true]").toggleClass("blinkProcesoActual");
						}	
						
					//Veronica Arismendy
					//2016-06-28
					var cont = 0;
					$("tr#rwPacienteAgendado_"+cedulaAgenda).each(function(i) {
						  $(this).find("#tdEstadoProceso_"+cedulaAgenda).html(estadoActual); 
					  if(cont >= 1){
						  $(this).find("input:radio").prop("disabled", true);
						  $(this).find("input:checkbox").prop("disabled", true);
						  $(this).find("img").removeAttr('onclick');
						  $(this).find("#tdEstadoProceso_"+ cedulaAgenda).html(estadoActual+" (Doble agenda)"); 
						  $(this).find("#tdAutorizacion_"+ cedulaAgenda).html(turnoAgendaP);
						  						  
						  $(this).find("#rdInicioProcedimiento_"+ cedulaAgenda).prop("disabled", false);
						  $(this).find("#rdFinProcedimiento_"+ cedulaAgenda).prop("disabled", false);
						  $(this).find("#rdhistoria_clinica_"+ cedulaAgenda).prop("disabled", false);
					  }	
					  cont++;
					});	
                }
			}
		}, 'json');
	}

	function asociarTurnoCita(turno, cedulaSinCita, nombreSinCita, cedulaAgenda, nombreAgenda, idAgenda)
	{
		$.post("pantallaAdmisionSala.php",
		{
			consultaAjax:   		'',
			accion:         		'asociarTurnoCita',
			turnoPac: 				turno,
			cedulaPacSincita: 		cedulaSinCita,
			nombrePacSincita: 		nombreSinCita,
			cedulaPacAgenda: 		cedulaAgenda,
			nombrePacAgenda: 		nombreAgenda,
			idAgenda: 				idAgenda

		}, function(data){
			if(data.Error)
			{
				jAlert(data.Mensaje, "Alerta");
			}
			else
			{
				$("#divAsociarTurnoCita").html(data.html);
			}
		}, 'json').done(function(){
				$("#divAsociarTurnoCita").dialog({
												height: 'auto',
												width: 'auto',
												modal: true
											}
										)
				});
	}

	function guardarAsociarTurnoCita(turno, cedulaSinCita, nombreSinCita, cedulaAgenda, nombreAgenda, idAgenda)
	{
		var seleccion_paciente = $("input:radio[name=opcionCorrectaTurnoSinCita]:checked").length;
		
		if(seleccion_paciente > 0)
		{			
			var opcionPaciente = $("input:radio[name=opcionCorrectaTurnoSinCita]:checked").val();
			
			var cedulaPac      = $("#asociarCedula" + opcionPaciente).val();
			var nombrePac      = $("#asociarNombre" + opcionPaciente).val();
			
			//2016-04-07 Verónica Arismendy
			//se debe validar que los campos de la opción seleccionada estén completamente diligenciados
			if(cedulaPac != "" && nombrePac != ""){			
				$("#msjErorOpcionCorrecta").html("");
				
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:   		'',
					accion:         		'guardarAsociarTurnoCita',
					turnoPac: 				$("#asociarTurno" + opcionPaciente).val(),
					cedulaPac: 				cedulaPac,
					nombrePac: 				nombrePac,
					cedulaPacLog: 			$("#asociarTurnoPacAgenda").attr("documentoAgenda"),
					idFilaAgenda: 			idAgenda,	
					ubicacion: 				$("#ubicacion").val(),
					cedulaSinCita:			cedulaSinCita					
				}, function(data){
					if(data.Error)
					{
						if(data.MensajeCitasen23 != '')
						{
							jAlert(data.MensajeCitasen23, "Alerta");
						}
						else if(data.MensajeCitasen09 != '')
						{
							jAlert(data.MensajeCitasen09, "Alerta");
						}
					}
					else
					{
						jAlert(data.MensajeCitasen09, "Alerta");
					}
				}, 'json').done(function(){
					$("#divAsociarTurnoCita").dialog("close");

					var turnoAsociado = $("#asociarTurno" + opcionPaciente).val().substr(7, 10);
					var cedulaAsociada = $("#asociarCedula" + opcionPaciente).val();
					var nomPacAsociado = $("#asociarNombre" + opcionPaciente).val();
										
					$("#trTurno_" + turnoAsociado).remove();
					$("#tdAutorizacion_" + cedulaAgenda).html("<b>" + turnoAsociado + "<b>").css("background-color", "#96FF96");
					$("#tdCedulaAgenda_" + cedulaAgenda).html(cedulaAsociada);
					$("#tdNomPacAgenda_" + cedulaAgenda).html(nomPacAsociado);

					$("td[blink=true]").toggleClass("blinkProcesoActual");
					
					if ($("tablaListaTurnos trTurno_").length < 1) {
						$("#tablaListaTurnos").hide();
					} else {
						listarPacientesConTurno();
					}
					
				});
			} else {
				$("#msjErorOpcionCorrecta").html("La opcion seleccionada como la correcta debe tener diligenciados <br> los campos de Documento y Nombre del Paciente.");
			}
		} else {
			jAlert("Se deben diligenciar todos los campos.", "Alerta");
		} 		
	}

	function cerrarAsociarTurnoCita()	{
		$("#divAsociarTurnoCita").dialog("close");
	}
	
	//para abrir la ventana de agenda
	function abrirVentanCitas(solucionCitas, wemp_pmla, caso, wsw, fest){			
		var ventanaAgenda = window.open("../../citas/procesos/calendarSala.php?empresa="+solucionCitas+"&wemp_pmla="+wemp_pmla+"&caso="+caso+"&wsw="+wsw+"&fest="+fest+"&consultaAjax=","miventana","width=1000,height=750");
	}
	
	//Para que cuando se cierre la ventana de agenda se recargue está pagina.
	function postCerrarVentanaCitas(){
		
		var monitores = $("#monitores").val();
		var ubicacion = $("#Ubicaciones").val();
		var ccosto    = $("#ccosto").val();
		var solucionCitas = $("#solucionCitas").val();
		var caso = $("#caso").val();
		var wemp_pmla = $('#wemp_pmla').val();
		
		if(solucionCitas === "citasen"){
			location.href = "../../citas/procesos/pantallaAdmisionSala.php?wemp_pmla="+wemp_pmla+"&caso="+caso+"&solucionCitas="+solucionCitas+"&ccosto="+ccosto+"&per="+monitores+"&ubn="+ubicacion;
		}
	}
	
	
	//2016-05-18 Verónica Arismendy 
	//Función que realiza la llamada al acompañante a recuperación
	function llamadaAcompanante(idTdRecuperacion, idfila, cedulaPac, solucionCitas, ubicacion){
		
		$.post("pantallaAdmisionSala.php",
		{
			consultaAjax:   	'',
			accion:         	'llamadaAcompanante',
			cedulaPac: 			cedulaPac,
			solucionCitas:		solucionCitas,
			ubicacion:			ubicacion,
			idfila:				idfila
		}, function(data){
			if(data.Error)
			{
				jAlert(data.Mensaje, "Alerta");
			} else {	
				//Se dehabilita finalizar recuperación
				$("#rdFinRecuperacion"+idfila).prop("disabled", true);					
				$("#imgApagarLlamadoAcompanante_"+idfila).prop("disabled", false);
				$("#" + idTdRecuperacion + ""+ idfila).attr("blink", "true");
				$("td[blink=true]").toggleClass("blinkProcesoActual");
			}
		}, 'json').done(function () {
		               
		        });		
	}
	
	
	//2016-05-18 Verónica Arismendy 
	//Función que realiza el registro de que se finalizó la llamada del acompañante a recuperación
	function finalizarLlamadaAcompanante(idTdRecuperacion, idfila, cedulaPac, solucionCitas, ubicacion){
		
		$.post("pantallaAdmisionSala.php",
		{
			consultaAjax:   	'',
			accion:         	'finalizarLlamadaAcompanante',
			cedulaPac: 			cedulaPac,
			solucionCitas:		solucionCitas,
			ubicacion:			ubicacion,
			idfila:				idfila
		}, function(data){
			if(data.Error)
			{
				jAlert(data.Mensaje, "Alerta");
			} else {		
				//Se habilita finalizar recueperacion
				$("#rdFinRecuperacion"+idfila).prop("disabled", true);	
				$("#imgApagarLlamadoAcompanante_"+idfila).prop("disabled", true);
				$("#" + idTdRecuperacion + ""+ idfila).removeAttr("blink");	
				$("#" + idTdRecuperacion + ""+ idfila).removeClass("blinkProcesoActual");				
				$("td[blink=true]").toggleClass("blinkProcesoActual");
			}
		}, 'json').done(function () {
		               
		});		
	}
	
	
	//2016-06-16
	function finalizarPreparacion(idTdRecuperacion, idfila, cedulaPac, solucionCitas, ubicacion){
		//Existe la posibilidad de que la admisión no se finalice porque el paciente no requeria admision pero los demás procesos si		
		var msj = "Est&aacute; seguro de dar por finalizada la preparaci&oacute;n del paciente?";
		
		jConfirm(msj,"Confirmacion", function(r) {  
			if(r) {
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:   	'',
					accion:         	'finalizarPreparacion',
					cedulaPac: 			cedulaPac,
					solucionCitas:		solucionCitas,
					ubicacion:			ubicacion,
					idfila:				idfila
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
					} else {				
						$("#tdAtencion_"+idfila).css("background-color", "#96FF96");
						$("#rdLlamadoAtencion_"+idfila).removeAttr('onclick');
						$("#rdApagadoLlamadoAtencion_"+idfila).removeAttr('onclick');
						$("#finPreparacion_"+idfila).prop("disabled", true);	
						$("#finPreparacion_"+idfila).attr("checked", "checked");	
					}
				}, 'json').done(function () {
							   
				});
			}else{
				$("#finPreparacion_"+idfila).attr("checked", false);
			}
		});
	}
	
	
	//2016-06-21
	//Función para dar por finalizada la entrega de resultados
	function registraFinalizarResultado(idTdRecuperacion, idfila, cedulaPac, solucionCitas, ubicacion){
		//Existe la posibilidad de que la admisión no se finalice porque el paciente no requeria admision pero los demás procesos si		
		var msj = "Est&aacute; seguro de dar por finalizada la entrega de resultados?";
		
		jConfirm(msj,"Confirmacion", function(r) {  
			if(r) {
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:   	'',
					accion:         	'registraFinalizarResultado',
					cedulaPac: 			cedulaPac,
					solucionCitas:		solucionCitas,
					ubicacion:			ubicacion,
					idfila:				idfila
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
						$("#finEntregaResultados_"+idfila).attr("checked", false);
					} else {				
						$("#tdResultados_"+idfila).css("background-color", "#96FF96");
						$("#rdLlamadoResultados_"+idfila).removeAttr('onclick');
						$("#rdApagadoLlamadoResultados_"+idfila).removeAttr('onclick');
						$("#finEntregaResultados_"+idfila).prop("disabled", true);	
						$("#finEntregaResultados_"+idfila).attr("checked", "checked");	
					}
				}, 'json').done(function () {
							   
				});
			}else{
				$("#finEntregaResultados_"+idfila).attr("checked", false);
			}
		});
	}
			
	//PARA EL LLAMADO DEL MEDICO AL ACOMPAÑANTE
	//2016-06-21 Verónica Arismendy 
	//Función que realiza la llamada al acompañante para que el medico pueda hablar con el 
	function llamarPacienteMedico(idTdMedico, idfila, cedulaPac, solucionCitas, ubicacion){
				
		$.post("pantallaAdmisionSala.php",
		{
			consultaAjax:   	'',
			accion:         	'llamarPacienteMedico',
			cedulaPac: 			cedulaPac,
			solucionCitas:		solucionCitas,
			ubicacion:			ubicacion,
			idfila:				idfila
		}, function(data){
			if(data.Error)
			{
				jAlert(data.Mensaje, "Alerta");
			}
			else
			{					
				$("#" + idTdMedico + ""+ idfila).attr("blink", "true");
				$("td[blink=true]").toggleClass("blinkProcesoActual");
			}
		}, 'json').done(function () {
							   
		});	
	}
		
	//2016-06-21 Verónica Arismendy 
	//Función que realiza el registro de que se finalizó la llamada del acompañante a recuperación
	function apagarLlamarPacienteMedico(idTdMedico, idfila, cedulaPac, solucionCitas, ubicacion){
		
		$.post("pantallaAdmisionSala.php",
		{
			consultaAjax:   	'',
			accion:         	'apagarLlamarPacienteMedico',
			cedulaPac: 			cedulaPac,
			solucionCitas:		solucionCitas,
			ubicacion:			ubicacion,
			idfila:				idfila
		}, function(data){
			if(data.Error)
			{
				jAlert(data.Mensaje, "Alerta");
			}
			else
			{	
				$("#" + idTdMedico + ""+ idfila).removeAttr("blink");	
				$("#" + idTdMedico + ""+ idfila).removeClass("blinkProcesoActual");				
				$("td[blink=true]").toggleClass("blinkProcesoActual");
			}
		}, 'json').done(function () {
		               
		});		
	}
	//FIN PROCESO DE LLAMADO DEL ACOMPAÑANTE POR PARTE DEL MEDICO
	
		
	
	//2016-06-17
	//Función para cancelar los turnos que no tenian cita y que no se les va asignar
	//una para el dia actual o pacientes que solo toman turno para preguntar algo.
	function cancelarTurno(turno){
		//Existe la posibilidad de que la admisión no se finalice porque el paciente no requeria admision pero los demás procesos si		
		var msj = "Est&aacute; seguro de anular este turno?";
		var solucionCitas = $("#solucionCitas").val();
		jConfirm(msj,"Confirmacion", function(r) {  
			if(r) {
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:   	'',
					accion:         	'cancelarTurno',
					turno: 				turno,
					solucionCitas: 		solucionCitas
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
					}
					else
					{				
						alert("El turno ha sido cancelado");
					}
				}, 'json').done(function () {
							   
				});
			}else{
				$("#finPreparacion_"+idfila).attr("checked", false);
			}
		});
	}
	
	
	
	//2016-06-22
	//Función para quitar un paciente de la agenda por ser de otra ubicaicon
	function quitarPacienteAgenda(idTd, idFila, solucionCitas, cedulaPac, ubicacion ){
		//Existe la posibilidad de que la admisión no se finalice porque el paciente no requeria admision pero los demás procesos si		
		var msj = "Est&aacute; seguro de eliminar de la lista &eacute;ste paciente por ser de otra ubicaci&oacute;n ?";
		var solucionCitas = $("#solucionCitas").val();
		jConfirm(msj,"Confirmacion", function(r) {  
			if(r) {
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:   	'',
					accion:         	'quitarPacienteAgenda',
					cedulaPaciente: 	cedulaPac,
					solucionCitas: 		solucionCitas,
					ubicacion: 			ubicacion
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
						$("#rdquitar_agenda_"+idFila).attr("checked", false);
					}
					else
					{	
						$("#rwPacienteAgendado_" + cedulaPac).remove();						
						alert("El paciente se ha eliminado de la lista");
					}
				}, 'json').done(function () {
							   
				});
			}else{
				$("#rdquitar_agenda_"+idFila).attr("checked", false);
			}
		});
	}
	
	
	
	//----------------------------------------------------------------------------------
	// --> Enlace para ir a la historia clinica
	//----------------------------------------------------------------------------------
	function abrirHistoriaClinica(idTd, idFila, solucionCitas, cedulaPac, ubicacion )
	{
		
		$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:   	'',
					accion:         	'abrirHistoriaClinica',
					cedulaPaciente: 	cedulaPac,
					solucionCitas: 		solucionCitas
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
						$("#rdhistoria_clinica_"+cedulaPac).attr("checked", false);
					}
					else
					{	
						
						var historia = data.historia;
						var ingreso  = data.ingreso;
						var tipoDoc  = data.tipoDoc;
						var wemp_pmla = $('#wemp_pmla').val();
						var whce = $('#whce').val();
						var wmovhos = $('#wmovhos').val();
						
						var url = "/matrix/hce/procesos/HCE_iframes.php?wemp_pmla="+wemp_pmla+"&empresa="+whce+"&origen="+origen+"&wcedula="+cedulaPac+"&wtipodoc="+tipoDoc+"&wdbmhos="+wmovhos+"&whis="+historia+"&wing="+ingreso+"&accion=F&ok=0";

						var ventanaHce = window.open(url,"miventana","width=auto,height=auto");		
						$("#rdhistoria_clinica_"+cedulaPac).attr("checked", false);						
					}
				}, 'json').done(function () {
							   
				});		
	}
	
	//2016-07-07
	//Función para finalizar la atención de un paciente que no tuvo sedación y no requiere pasar por recuperación.
	function finalizaPacienteSinSedacion(idTd, idFila, solucionCitas, cedulaPac, ubicacion ){
		//Existe la posibilidad de que la admisión no se finalice porque el paciente no requeria admision pero los demás procesos si		
		var msj = "Est&aacute; seguro de dar salida a este(a) paciente certificando que es porque no se uso sedaci&oacute;n para realizar el procedimiento?";
		var solucionCitas = $("#solucionCitas").val();
		
		jConfirm(msj,"Confirmacion", function(r) {  
			if(r) {
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:   	'',
					accion:         	'finalizaPacienteSinSedacion',
					cedulaPaciente: 	cedulaPac,
					solucionCitas: 		solucionCitas,
					ubicacion: 			ubicacion
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
						$("#rdFinalizaPacienteSinSedacion_"+cedulaPac).attr("checked", false);					
					}
					else
					{				
						$("#rdInicioProcedimiento_"+cedulaPac).prop("disabled", true);
						$("#rdFinProcedimiento_"+cedulaPac).prop("disabled", true);				
						jAlert(data.Mensaje, "Alerta");
						
					}
				}, 'json').done(function () {
							   
				});
			}else{
				$("#rdFinalizaPacienteSinSedacion_"+cedulaPac).attr("checked", false);
			}
		});
	}
	
	
	//2016-07-07
	//Función para el procedimiento de paciente que ya ha sido sedado y debe pasar a recuperacion asi no se le haya hecho procedimiento
	function cancelaPacienteSedado(idTd, idFila, solucionCitas, cedulaPac, ubicacion ){
		//Existe la posibilidad de que la admisión no se finalice porque el paciente no requeria admision pero los demás procesos si		
		var msj = "Confirma que a este paciente se le cancel&oacute; el procedimiento?";
		var solucionCitas = $("#solucionCitas").val();
		
		jConfirm(msj,"Confirmacion", function(r) {  
			if(r) {
				$.post("pantallaAdmisionSala.php",
				{
					consultaAjax:   	'',
					accion:         	'cancelaPacienteSedado',
					cedulaPaciente: 	cedulaPac,
					solucionCitas: 		solucionCitas,
					ubicacion: 			ubicacion
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
						$("#rdcancelaPacienteSedado_"+cedulaPac).attr("checked", false);						
					}
					else
					{						
						$("#rdInicioProcedimiento_"+cedulaPac).prop("disabled", true);
						$("#rdFinProcedimiento_"+cedulaPac).prop("disabled", true);				
						jAlert(data.Mensaje, "Alerta");		
					}
				}, 'json').done(function () {
							   
				});
			}else{
				$("#rdcancelaPacienteSedado_"+cedulaPac).attr("checked", false);
			}
		});
	}
	
	
	//guarda los datos del usuario que acaba de recargar la pagina para no mostrarle más mensajes de actualizaciones
	function recargarPagina(){			
		$.post("pantallaAdmisionSala.php",
			{
				consultaAjax:   	'',
				accion:         	'guardarUbicacionRecargaPagina',
				solucionCitas: 		$("#solucionCitas").val(),
				ubicacion: 			$("#Ubicaciones").val()
			}, function(data){
				if(!data.Error)
				{
					window.location.reload(true);				
				}
			}, 'json').done(function () {
							   
		});	
	}
	
///////////////////////////////////////////////////////////////////////////
////////////////////////FIN FUNCIONES JAVASCRIPT//////////////////////////	
/////////////////////////////////////////////////////////////////////////	
	
	
	
	
</script>
</head>

<?php
/**
 * Programa:	pantallaAdmisionSala.php
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

 /*
 Modificaciones:
 16/03/2022 - Brigith Lagares: Se realiza estadarización del wemp_pmla.
 2017-04-04	 Se agrega validacion $solucionCitas == "citasen") a funcion que actualizaba la agenda con errores en la cedula solo aplica para endoscopia.
 2016-03-11: Se modifica el script para mostrar las columnas respectivas de cada uno de los procesos de Endoscopia.
 			Cada uno de estos pasos en el proceso Endoscopia guarda fechas, horas y estados en un log llamado citasen_000023.
 			Se cargan distintos perfiles(monitores) y dependiendo del perfil seleccionado se muestran ciertas columans de
 			acuerdo a la configuración que se tenga en la relación de monitores-procesos en la tabla citasen_000026.
 			Los monitores se encuentran en la tabla citasen-000024 y los procesos se encuentran en la tabla citasen_000025.
 			Se muestra la ubicación que puede tener un usuario, la cual se encuentra en la tabla citasen_000027 y de acuerdo
 			a dicha ubicación se muestran las posibles ubicaciones que pueda tener un monitor, de acuerdo a la relación en la
 			tabla citasen_000028.
 2013-07-03 Se modifica el script por un error en una variable quedo asi en caso = 2 and valCitas!=on
 2013-05-16 Se modifica el script con la variable fest que es la que valida si se asignan citas los dias festivos, si esta en on,
							  es porque si se asignan citas los festivos en esa unidad.
 2013-04-25: Se organiza el las citas por medicos que le faltaba un paramero de mostrar causa de demora en el ingreso. Viviana Rodas
 2013-03-04: Se le agrega el campo Hora_aten a la funcion marcarAsistida para que guarde la hora cuando le dan clic en asistida.
 2013-01-16: Se agrega la entidad responsable a la lista de citas Viviana Rodas
 2012-12-26: Se agregan los colores a las celdas de hora, medico, paciente Viviana Rodas
 2012-12-20: Se agregan las causas de demora en la atencion, solo las pide cuando pasaron 15 minutos despues de la cita, tambien que
			 imprima la agenda de cualquier dia. Viviana Rodas
 2012-12-19: Se envia en la url de recarga la variable wfec para que cuando recargue la pagina se quede en la fecha que tenia el usuario. Viviana Rodas
 2012-12-18: Se agrega a la tabla de la agenda la columna usuario para que se visualice el usuario que asigno la cita. Viviana Rodas
 2012-12-17: Se agrega la variable tipoAtencion para evaluar cuando las citas son de cardiologia muestre el campo tipo atencion que se ingresa cuando se
			  asigna la cita. Viviana Rodas
 2012-12-07: Se agrega la opcion de imprimir la agenda de los pacientes. Viviana Rodas
 2012-12-06: Se agrega color diferente de fondo en la celda de medico o en la celda de medico para diferenciarlos. Viviana Rodas
 2012-11-26: Se cambian las consultas que traen las citas de el dia actual para que traigan el campo asistida!=on tambien el campo asistida, ademas se
 consulta tambien que el campo causa se igual a ''. Viviana Rodas.
 2012-11-10: Se agregan las causas para que cuando se marque la cita como cancelada guarde la causa en la base de datos, tambien para el campo no asiste,
 para que guarde la causa de la no asistencia.
 2012-10-26: Se cambian los nombres de los encabezados de la tabla, ahora es ingreso a la atencion que guarda la hora exacta en la que el paciente,
 ingreso a ser atendido por el medico, cancela pone ese registro en la tabla de citas como inactivo, no asiste guarda en la base de datos el campo asistida en off.
 2012-10-19: Se modifica toda la agenda para que nuestre la agenda diaria de las citas de todas las unidades de la clinica, incluyendo la clinica del sur con sus citas de medicos y de equipos, esta agenda permite visualizar todos los medicos o equipos dependiendo del tipo de cita que sea, o tambien filtrar por uno deseado, tambien se puede navegar entre fechas para ver las citas de dias anteriores o de dias posteriores, solo en el dia actual se muestran las columnas asiste o atendido, cancela, no asiste y en el caso de la clinica del sur admision, la lista de pacientes se muestran  por orden de cita, ademas cuando se graba el campo asiste se guarda null para que cuando en la agenda se chequee asiste se le cambie el estado a on u off dependiendo. Viviana Rodas

 2012-10-30: Se crea un blockUI para ingresar las causas de cancelacion y no asistencia, las cuales se pueden seleccionar, cuando se chequea el radiobutton de cancelada o de no asiste. Viviana Rodas
 2012-10-31: Se agrega el link deAsignar Sala en la parte superior de la tabla que lista los pacientes, tambien se agrego el campo causa a la consulta que trae los pacienes con cita en la clinica del sur, para que cuando se le de no asiste si lo borre de la lista.Viviana Rodas
 */

/********************************************************************************************************
 * 												FUNCIONES
 *******************************************************************************************************/

 
	function crearArrayPerfilxUbicacion($wbasedato) {
        
        

        

        $horaAten=date("H:i:s");
		
        //Consulta para traer la relación de monitores con ubicaciones.
        $queryUbicaciones = "SELECT CE24.Mondes, CE27.Ubides, CE27.Ubicod, CE28.Rummon, CE28.Rumubi
                             FROM " . $wbasedato ."_000024 AS CE24
                             INNER JOIN " . $wbasedato ."_000028 AS CE28 ON CE24.Moncod = CE28.Rummon
                             INNER JOIN " . $wbasedato ."_000027 AS CE27 ON CE27.Ubicod = CE28.Rumubi;";

        $resQueryUbicaciones = mysql_query($queryUbicaciones) or die(mysql_errno() - "Eror en el query " . $queryUbicaciones . " - " . mysql_error());

        if($rowQueryUbicaciones = mysql_num_rows($resQueryUbicaciones))
        {
            $arrayRlnMonitorUbicacion = array();
            for($i = 0; $rows = mysql_fetch_array($resQueryUbicaciones); $i++)
            {
                if(!array_key_exists($rows[3], $arrayRlnMonitorUbicacion)) {
                    $arrayRlnMonitorUbicacion[$rows[3]] = array();
                    $arrayRlnMonitorUbicacion[$rows[3]][$rows[4]] = $rows[1];
                } else {
                    $arrayRlnMonitorUbicacion[$rows[3]][$rows[4]] = $rows[1];
                }
            }            
        }
        return $arrayRlnMonitorUbicacion;
    }
  
	function marcarAsistida( $id, $causa ){

		global $conex;
		global $solucionCitas;
		$horaAten=date("H:i:s");

		if( isset($id) ){

			if( !empty($id) ){

				 $sql = "UPDATE
							{$solucionCitas}_000009
						SET
							asistida = 'on',
							atendido = 'on',
							Hora_aten = '".$horaAten."',
							Causa = '".$causa."'
						WHERE
							id = '$id'";

				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

				if( mysql_affected_rows() > 0 ){
					return true;
				} else {
					return false;
				}
			}
		}
	}


	function guardarCausaAdmision( $id, $causa )
	{
		global $conex;
		global $solucionCitas;
		$horaAten = date("H:i:s");

		if( isset($id) ){

			if( !empty($id) ){

				 $sql = "UPDATE
							{$solucionCitas}_000009
						SET
							Hora_aten = '".$horaAten."',
							Causa = '".$causa."'
						WHERE
							id = '$id'";

				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

				if( mysql_affected_rows() > 0 ){
					return true;
				} else {
					return false;
				}
			}
		}
	}

	function causas($tipo)
	{
		global $conex;

		$sql5="select Caucod, Caudes, Cautip, Cauest  from root_000086 where Cautip = '".$tipo."' and Cauest ='on' group by Caudes";
		$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );

		echo "<table>";
		echo "<tr class='encabezadotabla'  align=center>";
		echo "<td width='100%' colspan='2'>Seleccione la causa:</td>";
		echo "</tr> ";

		echo "<tr>";
		echo "<td align=center><select name='causa' onchange='javascript: llamarAjax();'>";
		echo "<option></option>";

		for( $i = 0; $rows5 = mysql_fetch_array( $res5 ); $i++ )
		{
			if( $causa != trim( $rows5['Caucod'] )." - ".trim( $rows5['Caudes'] ) )	{
				echo "<option>".$rows5['Caucod']." - ".$rows5['Caudes']."</option>";
			} else {
				echo "<option selected>".$rows5['Caucod']." - ".$rows5['Caudes']."</option>";
			}
		}
				
		echo "</select>";
		echo "</td></tr>";
		echo "</table>";
	}

	function htmlTurno($turno, $cedulaPac, $nombrePac, $reimpresion)
	{
		$html = "
		<table style='font-family: verdana;font-size:1rem;'>
			<tr>
				<td colspan='2' align='center'>
					<img width='118' heigth='58' src='../../images/medical/root/logo_Clinica.jpg'>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
					Es un placer servirle.
					<br><br>
				</td>
			</tr>
			<tr>
				<td >Turno:&nbsp;&nbsp;</td>
				<td align='right' style='font-size:2rem;'><b>".substr($turno, 7)."</b></td></tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>Documento de identificaci&oacute;n: &nbsp;&nbsp;&nbsp;".$cedulaPac."</td>
			</tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>Nombre del paciente: &nbsp;&nbsp;&nbsp;".ucwords(strtolower($nombrePac))."</td>
			</tr>
			<tr>
				<td colspan='2' align='center' style='font-size:0.8rem'>
					<br><b>Por favor conserve este tiquete hasta la consulta con el médico.</b>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center' style='font-size:0.7rem'>
					".(($reimpresion) ? "<b>(Reimpresión)</b>" : "")." Fecha: ".date('Y-m-d')." &nbsp;Hora: ".date('g:i:s a')."
				</td>
			</tr>
		</table>";

		return $html;
	}

	//Fecha creación: 2016-02-17
	//Autor: Eimer Castro
	//Esta función verifica los pasos realizados en la agenda de la cita del paciente
	//al momento de recargar la página por algún motivo.
	function validarPasosRealizados($fecha)
	{
		global $conex;
		global $solucionCitas;

		//Este query trae todos los registros del día
		$sqlValPasosRealizados = " SELECT *
								   FROM " . $solucionCitas ."_000023
								   WHERE Fecha_data	=	'" . $fecha . "'
								   AND Logest		=	'on'
								   ORDER BY Logtur DESC
		";

    	$resValPasosRealizados = mysql_query($sqlValPasosRealizados, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValPasosRealizados):</b><br>".mysql_error());
    	
    	$arrayRegistrosDia = array();
    	while ($rowValPasosRealizados = mysql_fetch_array($resValPasosRealizados))
    	{
    		$arrayRegistrosDia[$rowValPasosRealizados['Logdoc']] = $rowValPasosRealizados;
    	}
    	
    	return $arrayRegistrosDia;
	}

	function obtenerTipoDocumento($documento)
	{
		global $conex;

		$sqlTipoDocPac = "	SELECT Pactid AS tipoDocPac
							FROM root_000036
							WHERE Pacced = '".$documento."'
							ORDER BY tipoDocPac DESC LIMIT 1
		";
							
		$resTipoDocPac = mysql_query($sqlTipoDocPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipoDocPac):</b><br>".mysql_error());
			
		if($rowTipoDocPac = mysql_fetch_array($resTipoDocPac)){
			$nomPaciente = $rowTipoDocPac['tipoDocPac'];
		} else {
			$nomPaciente = "";
		}
		
		return $nomPaciente;
	}

	function obtenerNombrePaciente($tipoDocumento, $documento)
	{
		global $conex;

		$sqlNomPac = "SELECT CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2, ' ') AS nombrePac
					  FROM root_000036
					  WHERE Pacced = '".$documento."'
					  AND Pactid = '".$tipoDocumento."'
		";
		
		$resNomPac = mysql_query($sqlNomPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomPac):</b><br>".mysql_error());
		if($rowNomPac = mysql_fetch_array($resNomPac)){
			$nomPaciente = $rowNomPac['nombrePac'];
		} else {
			$nomPaciente = "";
		}
		
		return $nomPaciente;
	}

	function pintarColumnasXPerfil($solucionCitas, $fechaActual, $idQuery, $cedulaQuery, $nombreQuery, $validarPasosRealizados, $wagendaMedica)
	{
		$columnasXPerfil = "";
		if($solucionCitas == 'citasen')
		{
			// Este llamado a la funcion validarPasosRealizados verifica los pasos que ya se han
			// realizado para mostrarlos al usuario y evitar sobreescribir el registro del log.			
			$validarProcesos = $validarPasosRealizados[$cedulaQuery];
			$tipoDocumento = obtenerTipoDocumento($validarProcesos['Logdoc']);
			
			if($tipoDocumento != ""){
				$tipoDocumento = obtenerTipoDocumento($cedulaQuery);
			}
			
			$tipoDocumento = $tipoDocumento != "" ? $tipoDocumento : $validarProcesos['Logtip'];
				
			$columnasXPerfil = "";
			$colorProcedimientoRealizado = "background-color: #96FF96;";
			$estiloProcedimientoRealizado = "";
			$blink = false;

			//Columna de Turno Autorización
			$autorizacionChecked = "";
			$autorizacionDisabled = "";
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfau'] != '0000-00-00' && $validarProcesos['Loghau'] != '00:00:00' && $validarProcesos['Logeau'] != 'off' && $validarProcesos['Loguau'] != '')
			{
				$autorizacionChecked = "checked='true'";
				$autorizacionDisabled = "disabled='true'";
				$estiloProcedimientoRealizado = $colorProcedimientoRealizado;
			}
		    $columnasXPerfil .= "<td align=center id='tdAutorizacion_" . $cedulaQuery ."' style='display:none;".$estiloProcedimientoRealizado."' turno='". substr($validarProcesos['Logtur'], 7) . "'>"
		                            	//<input type='radio' name='rdAutorizacion_".$idQuery."' id='rdAutorizacion_".$idQuery."' tooltip='Autorizar' title='Autorizar' onclick='autorizacion(\"rdAutorizacion_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\")' " . $autorizacionChecked . " " . $autorizacionDisabled . ">
		                            	."<b>". substr($validarProcesos['Logtur'], 7) . "</b>
		                            </td>";


			//Columna de llamado paciente para Admisión
			$estiloProcedimientoRealizado = "";
			
			$llamarPacienteAdmisionOnclick = "onclick='llamarPacienteAdmision(\"rdLlamadoAdmision_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfla'] != '0000-00-00' && $validarProcesos['Loghla'] != '00:00:00' && $validarProcesos['Logela'] != 'off')
			{
				$llamarPacienteAdmisionOnclick = "";
			} else {				
				$admisionDisabled = "disabled='true'";
			}
			$admisionChecked = "";
			
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfia'] != '0000-00-00' && $validarProcesos['Loghia'] != '00:00:00' && $validarProcesos['Logeia'] != 'off' && $validarProcesos['Logefa'] != 'off')
			{				
				$admisionDisabled = "disabled='true'";				
				$admisionChecked = "checked='true'";
			}
			
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfla'] != '0000-00-00' && $validarProcesos['Loghla'] != '00:00:00' && $validarProcesos['Logela'] != 'off'
				&& $validarProcesos['Logfia'] == '0000-00-00' && $validarProcesos['Loghia'] == '00:00:00' && $validarProcesos['Logeia'] == 'off')
			{
				$blink = "true";
				$admisionDisabled = "";
				$llamarPacienteAdmisionOnclick = "onclick='llamarPacienteAdmision(\"rdLlamadoAdmision_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			} else {
				$blink = "false";
			}
			
			$onClickFinalizarAdmision = "onclick='finalizarAdmision(\"rdLlamadoAdmision_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			
			$checkedFinalizarAdmision = "";
			if($validarProcesos['Logeua'] == "on" && $validarProcesos['Logefa'] == "on"){
				$checkedFinalizarAdmision = "checked='true'";
			}
			
			$enabledFinalizarAdmision = "disabled='true'";				
			if($validarProcesos['Logeua'] == "off" && $validarProcesos['Logefa'] == "on"){
				$enabledFinalizarAdmision = "";
			}	
			
			$onclickApagarLlamadoAdmision = "onclick='apagarLlamarPacienteAdmisionConCita(\"rdApagadoLlamadoAdmisionSinCita_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			if($validarProcesos['Logeia'] == "on"){
				$onclickApagarLlamadoAdmision = "";
				$llamarPacienteAdmisionOnclick = "";
			}
			
			
			$enabledApagarLlamadoAdm = "disabled='disabled'";
			if($validarProcesos['Logefa'] == "off"){
				$enabledApagarLlamadoAdm = "";				
			}	
			
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logeua'] == "on"){
				$estiloProcedimientoRealizado = $colorProcedimientoRealizado;
			}
									
		    $columnasXPerfil .= "<td align=center id='tdAdmision_" . $idQuery ."' style='display:none;".$estiloProcedimientoRealizado."' blink='".$blink."'>
		                            	<img id='rdLlamadoAdmision_".$idQuery."' style='cursor:pointer;' class='botonLlamarPacienteAdmision' width='20' heigth='20' tooltip='Llamar para admisi&oacute;n' title='Llamar para admisi&oacute;n' src='../../images/medical/root/Call2.png' " . $llamarPacienteAdmisionOnclick . ">
		                            	<img id='rdApagadoLlamadoAdmision_".$idQuery."' style='cursor:pointer;' class='botonApagarLlamarPacienteAdmision' width='20' heigth='20' tooltip='ApagarLlamar para admisi&oacute;n' title='ApagarLlamar para admisi&oacute;n' src='../../images/medical/root/Call3.png' ".$onclickApagarLlamadoAdmision." ".$enabledApagarLlamadoAdm.">
		                            	<input type='radio' name='rdAdmision_".$idQuery."' id='rdAdmision_".$idQuery."' title='Iniciar admisi&oacute;n' onclick='admision(\"rdAdmision_".$idQuery."\",\"".$tipoDocumento."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\",\"".$wagendaMedica."\")' " . $admisionChecked . " " . $admisionDisabled . ">
										<input type='checkbox' id='rdFinalizarAdmision_".$idQuery."' title='Finalizar proceso de admisi&oacute;n' ".$onClickFinalizarAdmision . $checkedFinalizarAdmision . $enabledFinalizarAdmision . " > 
								</td>";


			//COLUMNA DE ATENCION
			$estiloProcedimientoRealizado = "";
			$llamarPacienteAtencionOnclick = "onclick='llamarPacienteAtencion(\"rdLlamadoAtencion_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logflt'] != '0000-00-00' && $validarProcesos['Loghlt'] != '00:00:00' && $validarProcesos['Logelt'] != 'off')
			{
				$llamarPacienteAtencionOnclick = "";
			}
			
			$apagarLlamarPacienteAtencionOnclick = "onclick='apagarLlamarPacienteAtencion(\"rdApagadoLlamadoAtencion_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\")'";
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfat'] != '0000-00-00' && $validarProcesos['Loghat'] != '00:00:00' && $validarProcesos['Logeat'] != 'off' && $validarProcesos['Loguat'] != '')
			{
				$apagarLlamarPacienteAtencionOnclick = "";
			}
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logflt'] != '0000-00-00' && $validarProcesos['Loghlt'] != '00:00:00' && $validarProcesos['Logelt'] != 'off'
				&& $validarProcesos['Logfat'] == '0000-00-00' && $validarProcesos['Loghat'] == '00:00:00' && $validarProcesos['Logeat'] == 'off')
			{
				$blink = "true";
			} else	{ 
				$blink = "false";
			}
			
			//Se hace la opción para finalizar completamente el proceso de preparacion
			$onclickFinalizarPreparacion = "";
			$enabledFinPreparacion = "";
			$checkFinPreparacion = "";
			$onclickFinalizarPreparacion = "onclick='finalizarPreparacion(\"tdAtencion_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logeua'] == 'on' && $validarProcesos['Logelt'] == 'on' && $validarProcesos['Logeaf'] == 'off')
			{
			}else{
				$enabledFinPreparacion = "disabled=disabled";
			}
			
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logeaf'] == 'on'){	
				$estiloProcedimientoRealizado = $colorProcedimientoRealizado;
				$checkFinPreparacion = "checked='checked'";
			}
						
		    $columnasXPerfil .= "<td align=center id='tdAtencion_" . $idQuery ."' style='display:none;".$estiloProcedimientoRealizado."' blink='".$blink."'>
		                            <img id='rdLlamadoAtencion_".$idQuery."' style='cursor:pointer;' class='botonLlamarPacienteAtencion' width='20' heigth='20' title='Llamar para atenci&oacute;n' src='../../images/medical/root/Call2.png' " . $llamarPacienteAtencionOnclick . ">
		                            <img id='rdApagadoLlamadoAtencion_".$idQuery."' style='cursor:pointer;' class='botonApagarLlamarPaciente' width='20' heigth='20' title='ApagarLlamar para atenci&oacute;n' src='../../images/medical/root/Call3.png' " . $apagarLlamarPacienteAtencionOnclick . ">
									<input type='checkbox' id='finPreparacion_".$idQuery."' title='Finalizar proceso de preparaci&oacute;n' ".$onclickFinalizarPreparacion . $checkFinPreparacion . $enabledFinPreparacion  .">
								</td>";
		

			//COLUMNA DE PROCEDIMIENTO
			$inicioProcedimientoChecked = "";
			$inicioProcedimientoDisabled = "";
			$iniciarProcedimientoOnClick = "onclick='inicioProcedimiento(\"rdInicioProcedimiento_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfip'] != '0000-00-00' && $validarProcesos['Loghip'] != '00:00:00' && $validarProcesos['Logeip'] != 'off')
			{				
				$inicioProcedimientoDisabled = "disabled='true'";
			} else	{
				$estiloProcedimientoRealizado = "";
				$finProcedimientoDisabled = "disabled='true'";
			}
			
			$finProcedimientoChecked = "";
			
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logffp'] != '0000-00-00' && $validarProcesos['Loghfp'] != '00:00:00' && $validarProcesos['Logefp'] != 'off' && $validarProcesos['Logupr'] != '')
			{
				$finProcedimientoChecked = "checked='true'";
				$finProcedimientoDisabled = "disabled='true'";
				$estiloProcedimientoRealizado = $colorProcedimientoRealizado;
			}
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfip'] != '0000-00-00' && $validarProcesos['Loghip'] != '00:00:00' && $validarProcesos['Logeip'] != 'off'
				&& $validarProcesos['Logffp'] == '0000-00-00' && $validarProcesos['Loghfp'] == '00:00:00' && $validarProcesos['Logefp'] == 'off')
			{
				$blink = "true";
				$estiloProcedimientoRealizado = "";
				$finProcedimientoDisabled = "";
			} else {
				$blink = "false";
				if($validarProcesos['Logfir'] == '0000-00-00' && $validarProcesos['Loghir'] == '00:00:00' && $validarProcesos['Logeir'] == 'off')
				{
					$inicioProcedimientoDisabled = "";
				}
			}
		    $columnasXPerfil .= "<td align=center id='tdProcedimiento_" . $idQuery ."' style='display:none;".$estiloProcedimientoRealizado."' blink='".$blink."'>
		                            	<input type='radio' name='rdInicioProcedimiento_".$cedulaQuery."' id='rdInicioProcedimiento_".$cedulaQuery."' tooltip='Iniciar procedimiento' title='Iniciar procedimiento' " . $iniciarProcedimientoOnClick . " " . $inicioProcedimientoChecked . " " . $inicioProcedimientoDisabled . ">
		                            	<input type='radio' name='rdFinProcedimiento_".$cedulaQuery."' id='rdFinProcedimiento_".$cedulaQuery."' tooltip='Finalizar procedimiento' title='Finalizar procedimiento' onclick='finProcedimiento(\"rdFinProcedimiento_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\")' " . $finProcedimientoChecked . " " . $finProcedimientoDisabled . ">
		                        </td>";

			//COLUMNA DE RECUPERACION
			$inicioRecuperacionChecked = "";
			$inicioRecuperacionDisabled = "";
			$estiloProcedimientoRealizado = "";
			$finRecuperacionChecked = "";
			$finRecuperacionDisabled = "";
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfir'] != '0000-00-00' && $validarProcesos['Logeir'] == 'on')	
			{				
				$inicioRecuperacionDisabled = "disabled='true'";
				$inicioRecuperacionChecked = "checked='true'";
			} 
			
			//Para habilitar el fin de la recuperación se debe haber terminado el llamado del acompañante, terminado el llamado para entrega de resultados
			if($validarProcesos['Logtur'] != '' && ($validarProcesos['Logeir'] == "off" || $validarProcesos['Logefr'] == "on")){
				$finRecuperacionDisabled = "disabled='true'";
			}
			
			//Para habilitar el fin de la recuperación se debe haber terminado el llamado del acompañante, terminado el llamado para entrega de resultados
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logefr'] == "on"){
				$estiloProcedimientoRealizado = $colorProcedimientoRealizado;
				$finRecuperacionChecked = "checked='true'";
			}
						
			//se valida si se muestra el botón de llamar o de cancelar llamado
			if(isset($validarProcesos['Logear']) && $validarProcesos['Logear'] != 'off' && $validarProcesos['Logelr'] != 'on') {						
				$blink = "true";	
			} else {	
				$blink = "false";					
			}					
			
			$clickLlamadoAcom = "onclick='llamadaAcompanante(\"tdRecuperacion_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			$clickApagarLlamadoAcom = "onclick='finalizarLlamadaAcompanante(\"tdRecuperacion_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			
		    $columnasXPerfil .= "
				<td align=center id='tdRecuperacion_" . $idQuery ."' style='display:none;".$estiloProcedimientoRealizado."' blink='".$blink."'>
		           
					<input type='radio' name='rdInicioRecuperacion_".$idQuery."' id='rdInicioRecuperacion_".$idQuery."' title='Inicio recuperaci&oacute;n' onclick='inicioRecuperacion(\"rdInicioRecuperacion_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion())' " . $inicioRecuperacionChecked . " " . $inicioRecuperacionDisabled . ">
					
					<img id='imgLlamadoAcompanante_".$idQuery."' class='botonLlamarPacienteAtencion' width='20' heigth='20' title='Llamar acompa&ntilde;ante' src='../../images/medical/root/Call2.png' " . $clickLlamadoAcom . ">
					
					<img id='imgApagarLlamadoAcompanante_".$idQuery."' class='botonApagarLlamarPaciente' width='20' heigth='20' title='Apagar Llamado de acompa&ntilde;ante' src='../../images/medical/root/Call3.png' " . $clickApagarLlamadoAcom  ." >
					
					<input type='radio' name='rdFinRecuperacion_".$idQuery."' id='rdFinRecuperacion_".$idQuery."' title='Fin recuperaci&oacute;n' onclick='finRecuperacion(\"rdFinRecuperacion_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$solucionCitas."\", consultarUbicacion())' " . $finRecuperacionChecked . " " . $finRecuperacionDisabled . ">
				</td>";
			

			//COLUMNA PARA ENTREGA DE BIOPSIA
			$llamarPacienteBiopsiaOnclick = "onclick='llamarPacienteBiopsia(\"rdLlamadoBiopsia_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			$apagarLlamarPacienteBiopsiaOnclick = "onclick='apagarLlamarPacienteBiopsia(\"rdApagadoLlamadoBiopsia_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\")'";
						
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logelb'] != 'off' &&  $validarProcesos['Logeab'] == 'off')
			{
			    $blink = "true";
			}
			else
			{
				$blink = "false";
			}
			
			$entregaBiopsiaOnclick = "onclick='registraEntregaBiopsia(\"finalizarEntregaBiopsia_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";	
			$checkedBiopsia = "";
			$estiloProcedimientoRealizado = "";
			
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logeir'] == "on" && $validarProcesos['Logefr'] == "off" && $validarProcesos['Logebf'] == "off") {
				$disabledBiopsia = "";				
			} else {
				$disabledBiopsia = "disabled='disabled'";				
			}
						
			//validar si ya se dijo que si y recargaron la pagina marcar como seleccionado
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logebf'] == 'on') {
				$checkedBiopsia = "checked='true'";
				$estiloProcedimientoRealizado = $colorProcedimientoRealizado;
				$llamarPacienteBiopsiaOnclick = "";
				$apagarLlamarPacienteBiopsiaOnclick = "";
			}
			
			$columnasXPerfil .= "<td align=center id='tdBiopsia_" . $idQuery ."' style='display:none;".$estiloProcedimientoRealizado."' blink='".$blink."'>
			                            <img id='rdLlamadoBiopsia_".$idQuery."' style='cursor:pointer;' class='botonLlamarPacienteBiopsia' width='20' heigth='20' tooltip='Llamar para entrega de Biopsia' title='Llamar para entrega de Biopsia' src='../../images/medical/root/Call2.png' " . $llamarPacienteBiopsiaOnclick . ">
			                            <img id='rdApagadoLlamadoBiopsia_".$idQuery."' style='cursor:pointer;' class='botonApagarLlamarPaciente' width='20' heigth='20' tooltip='Apagar llamado para entrega de Biopsia' title='Apagar llamado para entrega de Biopsia' src='../../images/medical/root/Call3.png' " . $apagarLlamarPacienteBiopsiaOnclick . ">
										<input type='checkbox' id='finalizarEntregaBiopsia_".$idQuery."' ".$entregaBiopsiaOnclick  . $checkedBiopsia . $disabledBiopsia .  ">
 								</td>";			
			//FIN COLUMNA BIOPSIA

		
			//Columna ENTREGA DE RESULTADOS
			$llamarPacienteResultadosOnclick = "onclick='llamarPacienteResultados(\"rdLlamadoResultados_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\", consultarUbicacion(), \"".$idQuery."\")'";
			$apagarLlamarPacienteResultadosOnclick = "onclick='apagarLlamarPacienteResultados(\"rdApagadoLlamadoResultados_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"".$solucionCitas."\")'";
						
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logels'] == 'on' &&  $validarProcesos['Logeas'] == 'off') {
			    $blink = "true";
			} else	{
				$blink = "false";
			}
			
			$entregaResultadosOnclick = "onclick='registraFinalizarResultado(\"finalizarEntregaBiopsia_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";	
			$checkedResultados = "";
			$estiloProcedimientoRealizado = "";
			
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logeir'] == "on" && $validarProcesos['Logefr'] == "off" && $validarProcesos['Logerf'] == "off") {
				$disabledResultados = "";				
			} else {
				$disabledResultados = "disabled='disabled'";				
			}
						
			//validar si ya se dijo que si y recargaron la pagina marcar como seleccionado
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logerf'] == 'on') {
				$checkedResultados = "checked='true'";
				$estiloProcedimientoRealizado = $colorProcedimientoRealizado;
				$llamarPacienteResultadosOnclick = "";
				$apagarLlamarPacienteResultadosOnclick = "";
			}				
		
			$columnasXPerfil .= "<td align=center id='tdResultados_" . $idQuery ."' style='display:none;".$estiloProcedimientoRealizado."' blink='".$blink."'>
			                        <img id='rdLlamadoResultados_".$idQuery."' style='cursor:pointer;' class='botonLlamarPacienteResultados' width='20' heigth='20' tooltip='Llamar para entrega de Resultados' title='Llamar para entrega de Resultados' src='../../images/medical/root/Call2.png' " . $llamarPacienteResultadosOnclick . ">
			                        <img id='rdApagadoLlamadoResultados_".$idQuery."' style='cursor:pointer;' class='botonApagarLlamarPaciente' width='20' heigth='20' tooltip='Apagar llamado para entrega de Resultados' title='Apagar llamado para entrega de Resultados' src='../../images/medical/root/Call3.png' " . $apagarLlamarPacienteResultadosOnclick . ">
									<input type='checkbox' id='finEntregaResultados_" . $idQuery ."' title='Finalizar entrega de resultados' ".$entregaResultadosOnclick . $checkedResultados . $disabledResultados . ">
								</td>";
			
			///////FIN ENTREGA DE RESULTADOS

						
			//Columna LLAMADO DEL MEDICO
			$llamarPacienteMedicoOnclick = "onclick='llamarPacienteMedico(\"tdLlamadoMedico_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
			$apagarLlamarPacienteMedicoOnclick = "onclick='apagarLlamarPacienteMedico(\"tdLlamadoMedico_\",\"".$idQuery."\",\"".$cedulaQuery."\",\"".$solucionCitas."\", consultarUbicacion())'";
						
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logelm'] == 'on' &&  $validarProcesos['Logefm'] == 'off') {
			    $blink = "true";
			} else	{
				$blink = "false";
			}
								
			//validar si el paciente se encuentra en recuperacion
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logefr'] == "on") {					
				$llamarPacienteMedicoOnclick = "";
				$apagarLlamarPacienteMedicoOnclick = "";
			}	
		
			$columnasXPerfil .= "<td align=center id='tdLlamadoMedico_" . $idQuery ."' style='display:none;"."' blink='".$blink."'>
			                        <img id='rdLlamadoMedico_".$idQuery."' style='cursor:pointer;' class='botonLlamarPacienteResultados' width='20' heigth='20' title='Llamado por el m&eacute;dico' src='../../images/medical/root/Call2.png' " . $llamarPacienteMedicoOnclick . ">
			                        <img id='rdApagadoLlamadoMedico_".$idQuery."' style='cursor:pointer;' class='botonApagarLlamarPaciente' width='20' heigth='20' title='Apagar llamado por el m&eacute;dico' src='../../images/medical/root/Call3.png' " . $apagarLlamarPacienteMedicoOnclick . ">
								</td>";
			//FIN OPCION LLAMADO POR EL MEDICO			
			
			// OPCION PARA TERMINAR TODA LA ATENDICON						
			$terminarAtencionChecked = "";
			$terminarAtencionDisabled = "";
			$estiloAtencionFinalizado = "";
			if($validarProcesos['Logtur'] != '' && $validarProcesos['Logfta'] != '0000-00-00' && $validarProcesos['Loghta'] != '00:00:00' && $validarProcesos['Logeta'] != 'off' && $validarProcesos['Loguta'] != '')
			{				
				$terminarAtencionDisabled = "disabled='true'";
				$terminarAtencionChecked = "checked='true'";
				$estiloAtencionFinalizado = "background-color: #96FF96;";
			}
			
			//validar si lo unico que falta es entregar resultados para alumbrar la columna
			if(isset($validarProcesos['Logtur']) && $validarProcesos['Logeas'] != "off" && $validarProcesos['Logeta'] != "on" && $validarProcesos['Logefr'] != "off"){
				$blink = "true";
			}else{
				$blink = "false";
			}
				
		    $columnasXPerfil .= "<td align=center id='tdTerminarAtencion_" . $idQuery ."' style='display:none;".$estiloAtencionFinalizado."' blink='".$blink."'>
		                                <input type='radio' name='rdTerminarAtencion_".$idQuery."' id='rdTerminarAtencion_".$idQuery."' title='Terminar atenci&oacute;n' onclick='terminarAtencion(\"rdTerminarAtencion_".$idQuery."\",\"".$cedulaQuery."\",\"".$nombreQuery."\",\"rwPacienteAgendado_".$idQuery."\",\"".$solucionCitas."\")' " . $terminarAtencionChecked . " " . $terminarAtencionDisabled . ">
		                        </td>";

			//FIN TERMINAR ATENCION 
			
			//OTRAS OPCIONES
		    $columnasXPerfil .= "<td align=center id='tdCancela_" . $idQuery ."' style='display:none;'>
										<input type='radio' name='rdCancela".$idQuery."' id='rdCancela".$idQuery."' tooltip='Cancelar' title='Cancelar' onclick='cancelaTurno(\"rdCancela".$idQuery."\",\"".$idQuery."\",\"".$solucionCitas."\", \"\",\"".$cedulaQuery."\")' value='I'>
								</td>";

		    $columnasXPerfil .= "<td align=center id='tdno_asiste_" . $idQuery ."' style='display:none;'>
										<input type='radio' name='rdno_asiste".$idQuery."' id='rdno_asiste".$idQuery."' tooltip='No asiste' title='No asiste' onclick='no_asiste_turno(\"rdno_asiste".$idQuery."\",\"".$idQuery."\",\"".$solucionCitas."\", \"\",\"".$cedulaQuery."\")' value='off'>
								</td>";
			
			$columnasXPerfil .= "<td align=center id='tdquitar_agenda_" . $idQuery ."' style='display:none;'>
										<input type='radio' name='rdquitar_agenda_".$idQuery."' id='rdquitar_agenda_".$idQuery."' title='Quitar de la agenda. (Paciente de otra ubicacion)' onclick='quitarPacienteAgenda(\"rdquitar_agenda_\",\"".$idQuery."\",\"".$solucionCitas."\",\"".$cedulaQuery."\", consultarUbicacion())'>
								</td>";
			
			
															
			$columnasXPerfil .= "<td align=center id='tdcancelaPacienteSedado_" . $idQuery ."' style='display:none;'>
										<input type='radio' name='rdcancelaPacienteSedado_".$cedulaQuery."' id='rdcancelaPacienteSedado_".$cedulaQuery."' title='Abrir historia clinica' onclick='cancelaPacienteSedado(\"rdhistoria_clinica_\",\"".$idQuery."\",\"".$solucionCitas."\",\"".$cedulaQuery."\", consultarUbicacion())'>
								</td>";
													
			$columnasXPerfil .= "<td align=center id='tdFinalizaPacienteSinSedacion_" . $idQuery ."' style='display:none;'>
										<input type='radio' name='rdFinalizaPacienteSinSedacion_".$cedulaQuery."' id='rdFinalizaPacienteSinSedacion_".$cedulaQuery."' title='Abrir historia clinica' onclick='finalizaPacienteSinSedacion(\"rdhistoria_clinica_\",\"".$idQuery."\",\"".$solucionCitas."\",\"".$cedulaQuery."\", consultarUbicacion())'>
								</td>";
			$columnasXPerfil .= "<td align=center id='tdhistoria_clinica_" . $idQuery ."' style='display:none;'>
										<input type='radio' name='rdhistoria_clinica_".$cedulaQuery."' id='rdhistoria_clinica_".$cedulaQuery."' title='Abrir historia clinica' onclick='abrirHistoriaClinica(\"rdhistoria_clinica_\",\"".$idQuery."\",\"".$solucionCitas."\",\"".$cedulaQuery."\", consultarUbicacion())'>
								</td>";					
			$columnasXPerfil .= "<td align=center id='tdEstadoProceso_" . $cedulaQuery ."' style='display:none;'>										
								</td>";
		
		}
		return $columnasXPerfil;
	}

	/**
	 * [existeTabla: Función para consultar si una tabla está creada o no en la base de datos] [updt-83]
	 * @param  [type] $conex     [description]
	 * @param  [type] $wemp_pmla [description]
	 * @param  [type] $wbasedato [Prefijo de la tabla en la que se quere buscar]
	 * @param  [type] $tabla     [Tabla sin prefijo que se quiere verificar]
	 * @return [type]            [description]
	 */
	function existeTabla($conex, $wemp_pmla, $wbasedato, $tabla)
	{
	    $tabla  = $wbasedato.'_'.$tabla;
	    $sql    = "SHOW TABLES LIKE '{$tabla}'";
	    $result = mysql_query($sql);
	    return (mysql_num_rows($result) == 1) ? true: false;
	}

	function verificarEstadoProceso($arrayEstados)
	{
		$cadenaEstados = "";
		foreach ($arrayEstados as $valor) {
			$cadenaEstados .= $valor;
		}
				
		$resultReplace = str_replace('on', '', $cadenaEstados);
			
		if(strlen ($resultReplace) == strlen ($cadenaEstados)) {
			$estadoProceso = -1;
			
		} else if ((strlen ($resultReplace) < strlen ($cadenaEstados)) && (strlen ($resultReplace) > 0)) {
			$estadoProceso = 0;
			
		} else {
			$estadoProceso = 1;
		}
		
		return $estadoProceso;
	}
	
	
	//Verónica Arismendy
	//Función que permite validar cuando se desea hacer un llamado en una taquilla si en ese momento la taquilla se encuentra haciendo un llamado a otro paciente y no está habilitada para hacer un nuevo llamado
	//return isValid booleano true si la taquilla está disponible para llamar un paciente o false si la taquilla ya se encuentra realizando un llamado en el momento.
	function validarDisponibilidadTaquilla($prefijoTabla, $ubicacion, $procedimiento){
		global $conex;
		
		$isValid = true;
		
		$and = "";
		
		switch($procedimiento){
			case "admision" : 
						$and = "AND Logela = 'on'
							    AND Logefa != 'on' 
								AND Loguba = '" . $ubicacion . "'";
						break;
							
			case "biopsia" : 
						$and = "AND Logelb = 'on'
								AND Logeab = 'off'
								AND Logubb = '" . $ubicacion . "'";
						break;
						
			case "resultados" : 
						$and = "AND Logels = 'on'
								AND Logeas = 'off'
								AND Logubs = '" . $ubicacion . "'";
						break;			
			case "preparacion" : 
						$and = "AND Logelt = 'on'
								AND Logeat = 'off'
								AND Logubt = '" . $ubicacion . "'";
						break;
			case "procedimiento" : 
						$and = "AND Logeip = 'on'
								AND Logefp = 'off'
								AND Logubp = '" . $ubicacion . "'";
						break;							
		}
				
		$sqlValidarTaquilla = "
							SELECT COUNT(0) as cantidadLlamados
							FROM " . $prefijoTabla . "_000023
							WHERE fecha_data = '" . date("Y-m-d") . "'
							AND Logest = 'on'
							" . $and. "							
		";

		$result = mysql_query($sqlValidarTaquilla, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlValidarTaquilla."):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($result);		

		if(isset($row["cantidadLlamados"]) && $row["cantidadLlamados"] >= 1){
			$isValid = false;
		}		
		
		return $isValid;		
	}
	
	
	//Verónica Arismendy 2016-05-03
	//Función que libera la ubicacion usada por el usuario enviado
	function liberarUbicaciones($usuario, $solucionCitas){
		
		$sqlReasignarUsuario = "UPDATE
								".$solucionCitas."_000028
							SET  Rumusu = ''	
							WHERE 
								Rumusu = '" . $usuario . "'					
		";
		$resConsulta 	= mysql_query($sqlReasignarUsuario) or die("<b>ERROR EN QUERY MATRIX($sqlReasignarUsuario):</b><br>".mysql_error());
		
		if(!$resConsulta){
			return false;
		} else {
			return true;
		}
	}
	
	//Verónica Arismendy 2016-05-03
	//Función que se encarga de hacer la solicitud del camillero cuando se da por terminada la atención.
	function solicitarCamillero($centroCosto, $empresa, $nombrePac){
		global $conex;
		$consultaAjax = '';
		include_once("root/comun.php");
		$idRegistroInsertado = 0;
				
		//Con el ccentro de conto de origen consulta en cencam 4 el nombre
		$tablaCencam = consultarAliasPorAplicacion($conex, $empresa, 'camilleros');
			
		$sqlRoot = "SELECT
					r.Ccaorg, r.Ccamot, r.Ccades, r.Ccaobs, cn.central
				FROM 
					root_000107 r
				INNER JOIN ".$tablaCencam."_000001 cn ON cn.Descripcion = r.Ccamot
				WHERE r.Ccaorg = '".$centroCosto."'		
		";
	
		$result = mysql_query($sqlRoot, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlRoot."):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($result);		
	
		if(isset($row["Ccaorg"]) && $row["Ccaorg"] != ""){		
			
			if($tablaCencam != ""){
				
				$sqlCencam = "SELECT Nombre
							  FROM ".$tablaCencam."_000004
							  WHERE Cco LIKE '".$centroCosto."%'
							  LIMIT 1
				";
				 
				$resultCen= mysql_query($sqlCencam, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlCencam."):</b><br>".mysql_error());
				$rowCen = mysql_fetch_assoc($resultCen);		
				
				if(isset($rowCen["Nombre"]) && $rowCen["Nombre"] != ""){
					
					$anexObs = $nombrePac != "" ? " para el paciente: " . $nombrePac : "";
					$anexObs .= $historia != "" ? " Historia: " . $historia : "";
					$anexObs .= " a las " . date("H:i:s");
					
					//Variables necesarias para solicitar un camillero
					$origen = $rowCen["Nombre"];
					$motivo = $row["Ccamot"];
					$destino = $row["Ccades"];
					$solicito = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
					$ccosto = $row["Ccades"];
					$observacion = $row["Ccaobs"] . $anexObs;
					$fecha = date("Y-m-d");
					$hora = date("H:i:s");
					$central = $row["central"];
					
					$sqlSolcitudCamillero = "INSERT INTO ".$tablaCencam."_000003
												(Medico, Fecha_data, Hora_data, Origen, Motivo, Observacion, Destino, Solicito, Ccosto, Anulada, Central, Seguridad)
											VALUES 
											('".$tablaCencam."', '".$fecha."', '".$hora."', '".$origen."', '".$motivo."', '".$observacion."',
											'".$destino."', '".$solicito."', '".$ccosto."', 'No', '".$central."', 'C-".$tablaCencam."')
					";
				 
					$respSolicitud = mysql_query($sqlSolcitudCamillero, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlSolcitudCamillero."):</b><br>".mysql_error());
					
					$sqlLastId = "SELECT MAX(id) AS id
								  FROM ".$tablaCencam."_000003
								  WHERE Fecha_data = '".$fecha."'
								  AND Hora_data = '".$hora."'
					
					";
					$obtenerUltimoId = mysql_query($sqlLastId, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlLastId."):</b><br>".mysql_error());
					$rowId = mysql_fetch_assoc($obtenerUltimoId);
					
					$idRegistroInsertado = isset($rowId["id"]) ? $rowId["id"] : 0;									
				}				
			}
		}	
		return $idRegistroInsertado;
	}
	
	
	function validarEstadoSolicitudCamillero($empresa, $idSolicitud){
		global $conex;
		$consultaAjax = '';
		include_once("root/comun.php");
		$idRegistroInsertado = 0;
		$estadoSolicitud = false;
		
		//Con el ccentro de conto de origen consulta en cencam 4 el nombre
		$tablaCencam = consultarAliasPorAplicacion($conex, $empresa, 'camilleros');
			
		$sqlRoot = "SELECT
					Anulada, Fecha_llegada, Hora_llegada
				FROM 
					".$tablaCencam."_000003 
				WHERE id = ".$idSolicitud."		
		";
	
		$result = mysql_query($sqlRoot, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlRoot."):</b><br>".mysql_error());
		$row = mysql_fetch_assoc($result);	

		if((isset($row["Anulada"]) && $row["Anulada"] == "Si") || (isset($row["Fecha_llegada"]) && $row["Fecha_llegada"] != "0000-00-00" && $row["Hora_llegada"] != "00:00:00")){
			$estadoSolicitud = true;
		}
		
		return $estadoSolicitud;
	}
	
	
/********************************************************************************************************
 * 											FIN DE FUNCIONES
 *******************************************************************************************************/

/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/
echo "<body>";

include_once("root/comun.php");


if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$verListaTurnos 	= FALSE;

// Verifica que sea una secretaria la que pueda ver los pacientes con turno y que no están en agenda
if($monitor != '')
{
	$verListaTurnos = TRUE;
}
$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

echo "<input type='HIDDEN' name= 'wemp_pmla' id= 'wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='HIDDEN' name= 'whce' id= 'whce' value='".$whce."'>";
echo "<input type='HIDDEN' name= 'wmovhos' id= 'wmovhos' value='".$wmovhos."'>";
echo "<input type='HIDDEN' name= 'solucionCitas' id= 'solucionCitas' value='".$solucionCitas."'>";
echo "<input type='HIDDEN' name= 'valCitas' id= 'valCitas' value='".@$valCitas."'>";
echo "<input type='HIDDEN' name= 'monitor' id= 'monitor' value='".$monitor."'>";
echo "<input type='hidden' name='permitirVerListaTurnos' id='permitirVerListaTurnos' value='".$verListaTurnos."'>";
echo "<input type='HIDDEN' name= 'ubicacion' id= 'ubicacion' value='".$ubicacion."'>";
echo "<input type='HIDDEN' name= 'wfec' id= 'wfec' value='".$wfec."'>";

echo "<input type='HIDDEN' name='ccosto' id= 'ccosto' value='".$ccosto."'>";

//Estos son input que solo tendrán valor en caso de que la página haya sido recargada al cerrar una url externa
$valorPerfil = isset($per) ? $per : '';
$valorUbicacion = isset($ubn) ? $ubn : '';

echo "<input type='HIDDEN' name='valorPerfil' id='valorPerfil' value='".$valorPerfil."'>";
echo "<input type='HIDDEN' name='valorUbicacion' id='valorUbicacion' value='".$valorUbicacion."'>";

//El usuario se encuentra registrado


session_start();

if(!isset($_SESSION['user']) ){
	echo "<br /><br /><br /><br />
				  <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
					  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
		</div>";
	return;
}
else
{
	$key = substr($user, 2, strlen($user));

	$conex = obtenerConexionBD("matrix");

	
	//2016-07-28 
	//Se hace una función que se encarga de corregir las cédulas mal escritas en la tabla de citas médicas
	//Se ejecuta sólo una vez por día, para ello se valida con el parametro fechaActualizacionAgendaEndoscopia de la root_000051
	$fechaActAgenda = consultarAliasPorAplicacion($conex, $wemp_pmla, "fechaActualizacionAgendaCitas");	
	if(isset($fechaActAgenda) && $fechaActAgenda < date("Y-m-d") && $solucionCitas == "citasen"){
		//Se deben corregir las cédulas de la tabla de citas
		$sqlActualizar = "UPDATE
							".$solucionCitas."_000009 
						  SET
							Cedula = replace( replace( Cedula, '\t', '' ) , ' ', '' )
						  WHERE Fecha = '".date("Y-m-d")."'
		";	
		$resActualizar = mysql_query($sqlActualizar) or die(mysql_errno() - "Error en el query " . $sqlActualizar . " - " . mysql_error());
		
		if($resActualizar){
			$sqlActUltimaFecha = "UPDATE
									root_000051
								  SET DetVal = '".date("Y-m-d")."'
								  WHERE Detapl = 'fechaActualizacionAgendaCitas'
			";
			$resActualizarRoot = mysql_query($sqlActUltimaFecha) or die(mysql_errno() - "Error en el query " . $sqlActUltimaFecha . " - " . mysql_error());		
		}
	}
	//Fin funcionalidad actualizar agenda

	
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower( $institucion->baseDeDatos );
	$wentidad = $institucion->nombre;

	if ($wemp_pmla == 01) {
		encabezado("AGENDA SALAS", "2022-03-16", $wbasedato );
	} else {
		encabezado("AGENDA SALAS", "2022-03-16", "logo_".$wbasedato );
	}

	if (!isset($wfec)) {
		$wfec = date("Y-m-d");
	}
	
	$horaActSec=time();
	$horaAct=date("H:i", $horaActSec );

	if (!isset($valCitas)) {
		$valCitas = "off";
	}

	if ($caso ==1 and $solucionCitas=='citasca') {
		$tipoAtencion='on';
	} else {
		$tipoAtencion='off';
	}

	if( !isset( $ret ) ){
		$ret = 'off';
	}

	if( isset($asistida) ){
		marcarAsistida( $asistida, $causa );
	}

	if( isset($admision) )	{
		guardarCausaAdmision( $admision, $causa );
	}

	//Buscando el doctor por el que fue filtrado
	if( !isset( $slDoctor ) ){
		$nmFiltro = "% - Todos";
		$filtro = '%';
		$slDoctor = "% - Todos";
	} else {
		$nmFiltro = $slDoctor;
		$exp = explode( " - ", $slDoctor);
		$filtro = $exp[0];
	}

	if (!isset($fest))
	{
		$fest = "off";
	}

	echo "<form name='pantalla' method=post>";
	echo "<br><br>";

	$existeTablaAgenda = existeTabla($conex, $wemp_pmla, $solucionCitas, "000024");

	if(isset($solucionCitas) && $existeTablaAgenda)
	{
		$queryMonitores = "SELECT Moncod, Mondes
							FROM " . $solucionCitas ."_000024
							WHERE Monest = 'on'
									AND Moncod != '*'";
		$resQueryMonitores = mysql_query($queryMonitores) or die(mysql_errno() - "Error en el query " . $queryMonitores . " - " . mysql_error());
		$opcionesMonitores = "<option id='monitor_00' value='00'>Seleccione un perfil...</option>";
		for($i = 0; $rows = mysql_fetch_array($resQueryMonitores); $i++) {
			$monitorSeleccionado = '';
			if(isset($monitor) && $rows['Moncod'] == $monitor)
			{
				$monitorSeleccionado = 'selected="selected"';
			}
			$opcionesMonitores .= "<option " . $monitorSeleccionado . " id='monitor_{$rows['Moncod']}' value='{$rows['Moncod']}'>{$rows['Moncod']} - {$rows['Mondes']}</option>";
		}
		$ubicacionSeleccionada = '';
		if(isset($ubicacion))
		{
			$ubicacionSeleccionada = $ubicacion;
		}
		echo "  <table align=center id='selectPerfil' style='display:none;'>
					<tr>
						<td class='encabezadotabla'>SELECCIONE SU PERFIL</td>
						<td class='fila1'><select id='monitores' onchange='asignarMonitor(value, \"".$solucionCitas."\", \"". $ubicacionSeleccionada . "\");'>" . $opcionesMonitores . "</select></td>
					<tr>
				</table>
				<br>
				<table align=center id='selectUbicacion' style='display:none;'>
					<tr>
						<td class='encabezadotabla'>SELECCIONE SU UBICACI&Oacute;N</td>
						<td class='fila1'><select id='Ubicaciones' onchange='validarUbicacion(value, \"".$solucionCitas."\");'></select></td>
					<tr>
				</table>
				<br>";
	}

	if ($caso == 2 and $valCitas=="on")
	{
		$sql = "SELECT
			Mednom, Medcod
		FROM
			{$wbasedato}_000051
		WHERE
			Medcid != ''
			AND Medest = 'on'
		ORDER BY Mednom";
		
	} else if ($caso == 3 or $caso == 1) {
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000003 where activo='A' ";
		
	} else if ($caso == 2 and $valCitas!="on") {
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000010 where activo='A' group by descripcion order by descripcion";
	}

	$res1 = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	//Filtro por doctor o equipo
	echo "<table align=center>";
	echo "	<tr>";
	
	if ($caso == 2)	{
		echo "	<td class='encabezadotabla' align=center>Filtro por Profesional</td>";
	} else {
		echo "	<td class='encabezadotabla' align=center>Filtro por Sala</td>";
	}
	
	echo "	</tr>";
	echo "	<tr>";
	echo "	<td class='fila1'><select name='slDoctor' onchange='javascript: document.forms[0].submit();'>";
	echo "	<option>% - Todos</option>";

	for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ ){

		if ($caso == 2 and $valCitas=="on")
		{			
			$rows['Medcod'] = trim( $rows['Medcod'] );
			$rows['Mednom'] = trim( $rows['Mednom'] );

			if( $slDoctor != trim( $rows['Medcod'] )." - ".trim( $rows['Mednom'] ) )
			{
				echo "<option>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
		}
		else if ($caso == 1 or $caso == 3)
		{
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );

			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			{
				echo "<option>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		else if ($caso == 2 and $valCitas!= "on")
		{
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );

			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			{
				echo "<option>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
	}//for

	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	echo "<div id='divListaPacientesConTurno' align='center'>";
	echo "</div>";


	$dia=date("l", strtotime( $wfec ) );
	$diaNum=date("d",strtotime( $wfec ));
	$mes=date("F",strtotime( $wfec ));
	$anio=date("Y",strtotime( $wfec ));

	// Obtenemos y traducimos el nombre del día
	if ($dia=="Monday") $dia="Lunes";
	if ($dia=="Tuesday") $dia="Martes";
	if ($dia=="Wednesday") $dia="Mi&eacute;rcoles";
	if ($dia=="Thursday") $dia="Jueves";
	if ($dia=="Friday") $dia="Viernes";
	if ($dia=="Saturday") $dia="Sabado";
	if ($dia=="Sunday") $dia="Domingo";

	// Obtenemos y traducimos el nombre del mes
	if ($mes=="January") $mes="Enero";
	if ($mes=="February") $mes="Febrero";
	if ($mes=="March") $mes="Marzo";
	if ($mes=="April") $mes="Abril";
	if ($mes=="May") $mes="Mayo";
	if ($mes=="June") $mes="Junio";
	if ($mes=="July") $mes="Julio";
	if ($mes=="August") $mes="Agosto";
	if ($mes=="September") $mes="Septiembre";
	if ($mes=="October") $mes="Octubre";
	if ($mes=="November") $mes="Noviembre";
	if ($mes=="December") $mes="Diciembre";

	//tabla para navegar en las fechas de las citas
	$wfecAnt = date( "Y-m-d", strtotime($wfec) - 24*3600 );
	$wfecSig = date( "Y-m-d", strtotime($wfec) + 24*3600 );

	echo "<br>";
	echo "<table border='0' align='center'>";
	echo "<th class='encabezadotabla' colspan='3'>Seleccione la fecha:</th>";
	echo "</table>";
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td><a href='javascript:' onclick='obtenerMonitor(this);' href2='../../citas/procesos/pantallaAdmisionSala.php?solucionCitas=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&ccosto=".$ccosto."&caso=".$caso."&wsw=".@$wsw."&wfec=".$wfecAnt."&valCitas=".@$valCitas."&fest=".$fest."' title='Atras'><img src='../../images/medical/citas/atras.jpg' alt='Atras'  height='30' width='30' border=0/></a></td>";
	
	if ($wfec == date("Y-m-d"))	{
		$hoy = "Hoy:";
	} else {
		$hoy ="";
	}
	
	echo "<td class='fila1' ><font size='4'><b>".$hoy."</b> ".$dia." ".$diaNum." de ".$mes ." de ".$anio."</font></td>";
	echo "<td><a href='javascript:' onclick='obtenerMonitor(this);' href2='../../citas/procesos/pantallaAdmisionSala.php?solucionCitas=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&ccosto=".$ccosto."&caso=".$caso."&wsw=".@$wsw."&wfec=".$wfecSig."&valCitas=".@$valCitas."&fest=".$fest."' title='Adelante'><img src='../../images/medical/citas/adelante.jpg' alt='Adelante'  height='30' width='30' border=0/></a></td>";
	echo "</tr>";
	echo "</table>";

	//fin tabla para navegar en las fechas de las citas

	echo "<br><br>";
	
	echo "<center>
		<a onclick='javascript:abrirVentanCitas(\"$solucionCitas\",\"$wemp_pmla\",\"$caso\",\"$wsw\",\"$fest\")'  style='color:#1e90ff'>Asignar Sala</a>
	</center>
	";
	
	echo "<br>";
	echo "<div id='div_actualizar_monitor' style='display:none;  background-color: #f2dede; border-color: #ebccd1; color: #b94a48; border: 1px solid transparent;  border-radius: 4px; margin-bottom: 20px; padding: 15px' align='left'>	
		<b><span id='mensaje_recargar'></span></b>
		<input type='button' onClick='javascript:recargarPagina()' value='Recargar'>
	</div>";
	
	//Aqui comienza la lista de pacientes
	//Buscando los pacientes que tienen cita
	//y no van para interconsulta	
	//Primero se valida si es de endoscopia entonces el where cambia si son las demás agendas el where sigue igual
	if($solucionCitas === "citasen"){		
		$andAsistida =  "";
		$inner = "INNER JOIN ".$solucionCitas."_000011 ex ON ex.codigo = ci.Cod_exa AND ex.Cod_equipo = ci.Cod_equ";
		$adicional = ", ex.Descripcion as examen";
		
		//Se arma el array de pacientes excluidos de la lista
		$sqlQuitarLista = "SELECT
								Pouced
							FROM 
								".$solucionCitas."_000030
							WHERE 
								Fecha_data = '".date("Y-m-d")."'								
		";
		
		$resLista = mysql_query( $sqlQuitarLista, $conex ) or die( mysql_errno()." - Error en el query $sqlQuitarLista - ".mysql_error() );
		$newVar	= array();
				
		while($rowLista = mysql_fetch_array($resLista)){			
			$newVar[] = $rowLista["Pouced"];
		}	
		if(count($newVar) > 0){
			$newVar2 = implode("','", $newVar);		
			$notIn = " AND cedula NOT IN('" . $newVar2. "')";
		}else{
			$notIn = "";
		}		
	}else{
		$andAsistida = "AND atendido != 'on'
						AND asistida != 'on'
		" ;
		$inner = "";
		$adicional = "";
		$notIn = "";
	}	
	 
	if ($caso == 2 and $valCitas =="on")
	{
	  $sql = "SELECT
				fecha,
				cod_equ,
				TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,
				hf,
				nom_pac,
				mednom,
				b.id,
				b.tipoA,
				b.tipoS,
				b.cedula,
				b.usuario,
				b.Nit_res,

				IF(cedula IN ((SELECT pacdoc FROM {$wbasedato}_000100 WHERE pacdoc = cedula AND pacact = 'on' )),'on','off') as act
			FROM
				{$wbasedato}_000051 a,
				{$solucionCitas}_000009 b
			WHERE
				medcid = cod_equ
				AND medcod like '$filtro'
				AND fecha = '".$wfec."'
				" .$andAsistida. "
				" .$notIn. "
				AND Causa = ''
				AND nom_pac != 'CANCELADA'
				AND activo = 'A'
				AND cedula NOT IN (SELECT espdoc FROM {$wbasedato}_000141 WHERE espdoc = cedula AND esphor = TIME_FORMAT( CONCAT(hi,'00'), '%H:%i:%s') AND espmed = medcod )
			ORDER BY hi, mednom, nom_pac
			";

	} 
	else if ($caso == 3 or $caso == 1)
	{
		$sql = "SELECT 
					cod_med,cod_equ,cod_exa,fecha,TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,TIME_FORMAT( CONCAT(hf,'00'), '%H:%i') as hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,tipoA,cedula,Asistida,id 
				FROM ".$solucionCitas."_000001 
				WHERE fecha='".$wfec."' 
				AND cod_equ like '".$filtro."' 
				AND Atendido != 'on' 
				AND Asistida != 'on' 
				AND Activo='A' 
				AND Causa='' 
				ORDER BY hi, cod_equ";	
	}
	else if ($caso == 2 and $valCitas != "on") // error se tenia solucionCitas en vez de valCitas
	{
		$sql = "SELECT 
					ci.cod_equ, ci.cod_exa, ci.fecha, TIME_FORMAT( CONCAT(ci.hi,'00'), '%H:%i') as hi, ci.hf, ci.nom_pac, ci.nit_res, ci.telefono, ci.edad, 
					ci.comentario, ci.usuario, ci.activo, ci.Asistida, ci.id, ci.cedula ".$adicional."
				FROM ".$solucionCitas."_000009 ci
				".$inner."
				WHERE fecha='".$wfec."' 
				AND cod_equ like '".$filtro."' 
				" .$andAsistida. "
				" .$notIn. "
				AND ci.Activo='A' 
				AND ci.Causa='' 
				ORDER BY ci.hi, ci.cod_equ";		
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	echo "<br><br><div id='divTablaAgenda'>";
	echo "<table id='tablaAgenda' align='center'>";
	$color[0]="#99FFFF"; //
	$color[1]="#CC9999";//
	$color[2]="#00CC99";//
	$color[3]="#CCFF99";//
	$color[4]="#CCCCFF";//
	$color[5]="#EAADEA";//
	$color[6]="#00CCCC";//
	$color[7]="#E6E8FA";//
	$color[8]="#999966";
	$color[9]="#FF9900";
	$color[10]="#FFFF33";
	$color[11]="#0099FF";
	$color[12]="#00FF99";
	$color[13]="#CC99CC";
	$color[14]="#CCCCCC";

	if( $num > 0 )
	{
		if($solucionCitas == 'citasen')
		{
			$validarProcesos = validarPasosRealizados($wfec);
		}
		$j=0;
		for( $i = 0, $k = 0; $rows = mysql_fetch_array( $res ); $i++ )
		{

			if( !isset( $array_colors[ $rows['cod_equ'] ] ) )
			{
				$array_colors[ $rows['cod_equ'] ] = $color[$k];
				$k++;
			}

			$color_fondo = $array_colors[ $rows['cod_equ'] ];

			$j++;
			//Definiendo la clase por cada fila
			if( $j%2 == 0 )
			{
				$class = "class='fila1 filaDroppable'";
			}
			else
			{
				$class = "class='fila2 filaDroppable'";
			}

			//para mostrar el nombre del equipo, del medico y del examen
			if ($caso == 3 or $caso == 1)
			{
				$sql1 = "select Codigo,Descripcion from ".$solucionCitas."_000003 where Codigo = '{$rows['cod_equ']}' and activo = 'A'";
				$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
				$rows1 = mysql_fetch_array( $res1 );


				$sql2 ="select Codigo, Nombre from ".$solucionCitas."_000008 where Codigo = '{$rows['cod_med']}' and activo = 'A'";
				$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
				$rows2 = mysql_fetch_array( $res2 );


				$sql3 ="select Codigo, Descripcion from ".$solucionCitas."_000006 where Codigo = '{$rows['cod_exa']}' and Cod_equipo = '{$rows['cod_equ']}' and activo = 'A'";
				$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
				$rows3 = mysql_fetch_array( $res3 );

				$sql4 ="select descripcion,nit from ".$solucionCitas."_000002 where nit = '{$rows['nit_resp']}' and activo = 'A'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				$rows4 = mysql_fetch_array( $res4 );
			}

			if ($caso == 2 and $valCitas != "on")
			{
				$sql4="select Codigo, Descripcion  from ".$solucionCitas."_000010 where codigo='{$rows['cod_equ']}' and activo = 'A' group by Codigo, Descripcion";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				$rows4 = mysql_fetch_array( $res4 );

				$sql5="select nit, Descripcion  from ".$solucionCitas."_000002 where nit='{$rows['nit_res']}' and activo = 'A'";
				$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
				$rows5 = mysql_fetch_array( $res5 );
			}

			if ($caso == 2 and $valCitas == "on")
			{
				$sql5="select nit, Descripcion  from ".$solucionCitas."_000002 where nit='{$rows['Nit_res']}' and activo = 'A'";
				$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
				$rows5 = mysql_fetch_array( $res5 );

				$sql6="SELECT Selcod, Seldes
				FROM {$wbasedato}_000100, {$wbasedato}_000105
				WHERE Pacdoc = '".$rows['cedula']."'
				AND Pactdo = Selcod
				AND Seltip = '01'";
				$res6 = mysql_query( $sql6, $conex ) or die( mysql_errno()." - Error en el query $sql6 - ".mysql_error() );
				$rows6 = mysql_fetch_array( $res6 );
				$tipodoc="";
				$tipodoc= $rows6['Selcod']."-".$rows6['Seldes'];

				$sql7="SELECT pachis
					FROM {$wbasedato}_000100
					WHERE '".$rows['cedula']."'= pacdoc";
				$res7 = mysql_query( $sql7, $conex ) or die( mysql_errno()." - Error en el query $sql6 - ".mysql_error() );
				$rows7 = mysql_fetch_array( $res7 );
			}

			//validacion de la hora para las causas de demora
			//se pasa a segundos la hora de la cita y se le suman 15 minutos
			$horaCitaseg=strtotime(date("Y-m-d")." ".$rows['hi']);
			$horaCita=($horaCitaseg+900);
			
			if ($horaActSec >=$horaCita)
			{
				$mostrarCausa='on';
			}
			else
			{
				$mostrarCausa='off';
			}

			//mostrar el encabezado de la tabla
			//citas caso 1 o caso 3
			if( $i == 0  and ($caso ==1 or $caso==3)){
				echo "	<tr class='encabezadotabla'  align=center>";
				echo "		<td style='width:40'>";
				echo "			Num";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Fecha";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora Inicial";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora Final";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Codigo";
				echo "		</td>";
				echo "		<td>";
				echo "			Nombre";
				echo "		</td>";
				/*echo "		<td>";
				echo "			C.Costos";
				echo "		</td>";
				echo "		<td>";
				echo "			Administrador";
				echo "		</td>";*/
				echo "		<td>";
				echo "			Sala";
				echo "		</td>";
				echo "		<td>";
				echo "			Solicitud";
				echo "		</td>";
				if ($tipoAtencion=='on')
				{
					echo "		<td>";
					echo "			Tipo Atencion";
					echo "		</td>";
				}
				echo "		<td>";
				//echo "			Usuario";
				echo "			Comentarios";
				echo "		</td>";

				/*if ( $wfec==date("Y-m-d") )
				{
					echo "		<td style='width:90'>";
					echo "			Utilizo la Sala";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			Cancelada";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			No asiste";
					echo "		</td>";

				}*/
				echo "	</tr>";
			}

			if ($i == 0 and $caso == 2 and $valCitas == "on")  //citas clinica del sur
			{
				echo "	<tr class='encabezadotabla'  align=center>";
				echo "		<td style='width:40'>";
				echo "			Num";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Fecha";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Documento";
				echo "		</td>";
				echo "		<td>";
				echo "			Nombre";
				echo "		</td>";
				echo "		<td>";
				echo "			C.Costos";
				echo "		</td>";
				echo "		<td>";
				echo "			Administrador";
				echo "		</td>";
				echo "    <td>";
				echo "			Servicio";
				echo "		</td>";
				echo "		<td>";
				echo "			Tipo Servicio";
				echo "		</td>";
				echo "		<td>";
				echo "			Usuario";
				echo "		</td>";
				echo "		<td>";
				echo "			Historia";
				echo "		</td>";
				if ( $wfec==date("Y-m-d") )
				{
					echo "		<td style='width:90'>";
					echo "			Admision";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			Asiste";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			Cancelada";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			No asiste";
					echo "		</td>";

				}
				echo "	</tr>";
			}

			if ($i == 0 and @$valCitas != "on" and $caso==2 && $solucionCitas == "citasen")   //citas caso 2 diferentes a clinica del sur
			{
				echo "	<tr class='encabezadotabla'  align=center>";
				echo "		<td style='width:40'>";
				echo "			Num";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Fecha";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Documento";
				echo "		</td>";
				echo "		<td>";
				echo "			Nombre";
				echo "		</td>";
				echo "		<td>";
				echo "			Solicitud";
				echo "		</td>";
				echo "		<td>";
				echo "			C.Costos";
				echo "		</td>";
				echo "		<td>";
				echo "			Administrador";
				echo "		</td>";
				echo "		<td>";
				echo "			Usuario";
				echo "		</td>";
				if ( $wfec==date("Y-m-d") )
				{
					$encabezadosXPerfil = "";

					$encabezadosXPerfil .= "<td style='width:90; display:none;' id='tdAutorizacion_'>
													Turno Autorizaci&oacute;n
												</td>
											<td style='width:150; display:none;' id='tdAdmision_'>
													Admisi&oacute;n
												</td>
											<td style='width:90; display:none;' id='tdAtencion_'>
													Inicio atenci&oacute;n
												</td>
											<td style='width:90; display:none;' id='tdProcedimiento_'>
													Procedimiento<br>
													Inicio/Fin
												</td>
											<td style='width:260; display:none;' id='tdRecuperacion_'>
													Recuperaci&oacute;n<br>
													Inicio/Llamar acompa&ntilde;ante/Fin
												</td>
											<td style='width:90; display:none;' id='tdBiopsia_'>
													Entrega de biopsia
												</td>
											<td style='width:90; display:none;' id='tdResultados_'>
													Entrega de resultado
											</td>
											<td style='width:90; display:none;' id='tdLlamadoMedico_'>
													Llamado del m&eacute;dico
											</td>	
											<td style='width:90; display:none;' id='tdTerminarAtencion_'>
													Terminar atenci&oacute;n
												</td>
											
											<td style='width:90; display:none;' id='tdCancela_'>
													Cancelada
												</td>
											<td style='width:90; display:none;' id='tdno_asiste_'>
													No asiste
											</td>
											
											<td style='width:90; display:none;' id='tdquitar_agenda_'>
													Quitar de la agenda
											</td>											
											<td style='width:90; display:none;' id='tdcancelaPacienteSedado_'>
													Cancelar procedimiento paciente sedado
											</td>
											<td style='width:90; display:none;' id='tdFinalizaPacienteSinSedacion_'>
													Dar salida a paciente sin sedaci&oacute;n
											</td>
											<td style='width:90; display:none;' id='tdhistoria_clinica_'>
													Historia Clinica
											</td>
											<td style='width:90; display:none;' id='tdEstadoProceso_'>
													Estado <br>del <br> proceso
											</td>													
											";

					echo $encabezadosXPerfil;
				}
				echo "	</tr>";
			}
			
			if ($i == 0 and @$valCitas != "on" and $caso==2 && $solucionCitas != "citasen")   //citas caso 2 diferentes a Endoscopia
			{
				echo "	<tr class='encabezadotabla'  align=center>";
				echo "		<td style='width:40'>";
				echo "			Num";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Fecha";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Documento";
				echo "		</td>";
				echo "		<td>";
				echo "			Nombre";
				echo "		</td>";
				echo "		<td>";
				echo "			C.Costos";
				echo "		</td>";
				echo "		<td>";
				echo "			Administrador";
				echo "		</td>";
				echo "		<td>";
				echo "			Usuario";
				echo "		</td>";
				if ( $wfec==date("Y-m-d") )
				{
					echo "		<td style='width:90'>";
					echo "			Ingresa a la Sala";
					echo "		</td>";
					
					echo "		<td style='width:90'>";
					echo "			Cancelada";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			No asiste";
					echo "		</td>";
					
				}
				echo "	</tr>";
			}

			//mostrar la informacion de las citas de esa fecha
			if ($caso ==1 or $caso==3)
			{
				echo "	<tr $class>";
				echo "		<td align=center>";
				echo "			".$j."";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['fecha']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."' align=center>";
				echo "			{$rows['hi']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."' align=center>";
				echo "			{$rows['hf']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."' align=center>";
				echo "			{$rows['cedula']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."'>";
				echo "			{$rows['nom_pac']}";
				echo "		</td>";
				/*echo "		<td bgcolor='".$color_fondo."'>";
				echo "			{$rows4['descripcion']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."'>";
				echo "			".$rows2['Codigo']."-".$rows2['Nombre']."";
				echo "		</td>";*/
				echo "		<td bgcolor='".$color_fondo."'>";
				echo "			".$rows1['Codigo']."-".$rows1['Descripcion']."";
				echo "		</td>";
				echo "		<td>";
				echo "			".$rows3['Codigo']."-".$rows3['Descripcion']."";
				echo "		</td>";
				/*if ($tipoAtencion=='on')
				{
					echo "		<td>";
					echo "		{$rows['tipoA']}";
					echo "		</td>";
				}*/
				echo "		<td align=center>";
				//echo "			{$rows['usuario']}";
				echo "			{$rows['comentarios']}";
				echo "		</td>";

				/*if ( $wfec==date("Y-m-d") and $rows['usuario'] == $key )
				{

					echo "		<td align=center>";
					echo "			<input type='radio' name='rdAsiste".$rows['id']."' id='rdAsiste".$rows['id']."' onclick='asiste(\"rdAsiste".$rows['id']."\",\"".$rows['id']."\",\"".$mostrarCausa."\")' value='on'>";
					echo "		</td>";

					echo "		<td align=center>";
					echo "			<input type='radio' name='rdCancela".$rows['id']."' id='rdCancela".$rows['id']."' onclick='cancela(\"rdCancela".$rows['id']."\",\"".$rows['id']."\", \"\",\"\",\"".$rows['cedula']."\" )' value='I'>";
					echo "		</td>";
				
					echo "		<td align=center>";
					echo "			<input type='radio' name='rdno_asiste".$rows['id']."' id='rdno_asiste".$rows['id']."' onclick='no_asiste(\"rdno_asiste".$rows['id']."\",\"".$rows['id']."\", \"\",\"\",\"".$rows['cedula']."\" )' value='off'>";
					echo "		</td>";
				}
				else
				{
					echo "<td align=center>Sin edicion</td>";
					echo "<td align=center>Sin edicion</td>";
					echo "<td align=center>Sin edicion</td>";
				}*/
			}

			//clinica del sur
			if (@$valCitas == "on" and $caso == 2)
			{
				$atencion=$rows['tipoA'];
				$atencion=explode("-", $atencion);
				@$atencion=$atencion[1];
				$servicio=$rows['tipoS'];
				$servicio=explode("-", $servicio);
				@$servicio=$servicio[1];

				echo "	<tr $class>";
				echo "		<td align=center>";
				echo "			".$j."";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['fecha']}";
				echo "		</td>";
				echo "		<td align=center bgcolor=''>";
				echo "			{$rows['hi']}";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['cedula']}";
				echo "		</td>";
				echo "		<td bgcolor=''>";
				echo "			{$rows['nom_pac']}";
				echo "		</td>";
				echo "		<td bgcolor=''>";
				echo "			{$rows5['Descripcion']}";
				echo "		</td>";
				echo "		<td bgcolor=''>";
			    echo "			{$rows['mednom']}";
			    echo "		</td>";
				echo "		<td>";
				echo "			$atencion";
				echo "		</td>";
				echo "		<td>";
				echo "			$servicio";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['usuario']}";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows7['pachis']}";
				echo "		</td>";
				if ( $wfec==date("Y-m-d"))
				{
					$wagendaMedica = 'on';

					$wdoc=$rows['cedula'];
					$tipdocto = explode("-", $tipodoc);
					$tipodoc = $tipdocto[0];
					if( $rows['act'] != 'on' ){
						echo "		<td align=center>";
						echo "			<input type='radio' name='rdAdmision' value='on' onclick=\"javascript: abrirVentana( {$rows['id']}, '$solucionCitas', '$wbasedato','$wdoc','$mostrarCausa','$tipodoc', '$wagendaMedica');\">";
						echo "		</td>";
					}
					else{
						echo "		<td align=center>";
						echo "		</td>";
					}

					if( $rows['act'] == 'on' ){
						echo "		<td align=center>";
						echo "			<input type='radio' name='rdAsistida' value='on' onclick=\"javascript: asistida( {$rows['id']}, '$mostrarCausa' );\">";
						echo "		</td>";
					}
					else{
						echo "		<td align=center>";
						echo "		</td>";
					}
						echo "		<td align=center>";
						echo "			<input type='radio' name='rdCancela".$rows['id']."' id='rdCancela".$rows['id']."' onclick='cancela(\"rdCancela".$rows['id']."\",\"".$rows['id']."\",\"\",\"\", \"".$wdoc."\")' value='I'>";
						echo "		</td>";

						echo "		<td align=center>";
						echo "			<input type='radio' name='rdno_asiste".$rows['id']."' id='rdno_asiste".$rows['id']."' onclick='no_asiste(\"rdno_asiste".$rows['id']."\",\"".$rows['id']."\",\"\",\"\", \"".$wdoc."\")' value='off'>";
						echo "		</td>";
				}
			}

			     //****************************revisar lo de asiste *************************
				if ($caso==2 and $valCitas!="on" && $solucionCitas == "citasen") // ENDOSCOPIA
				{
					$wagendaMedica = 'on';

					//if($validarProcesos[$rows['cedula']]['Logeta'] != 'on')
					if($validarProcesos[$rows['cedula']]['Logesc'] != 'on')
					{
						//$cedula = trim($rows['cedula']);
						$cedula = trim(preg_replace('/[ ]+/', '', $rows['cedula']));
						//replace( replace( CE09.cedula, '\t', '' ) , ' ', '' )
						echo "	<tr $class id='rwPacienteAgendado_".$cedula."' cedula='{$cedula}' nombre='{$rows['nom_pac']}' idAgenda='{$rows['id']}'>";
						echo "		<td align=center>";
						echo "			".$j."";
						echo "		</td>";
						echo "		<td align=center>";
						echo "			{$rows['fecha']}";
						echo "		</td>";
						echo "		<td align=center bgcolor=''>";
						echo "			{$rows['hi']}";
						echo "		</td>";
						echo "		<td bgcolor='' align=center id='tdCedulaAgenda_{$cedula}'><span class='doc_{$cedula}'>";
						echo "			{$cedula}";
						echo "		</span></td>";
						echo "		<td bgcolor='' id='tdNomPacAgenda_{$cedula}'>";
						echo "			{$rows['nom_pac']}";
						echo "		</td>";
						echo "		<td bgcolor='' id='tdExamAgenda_{$cedula}'>";
						echo "			{$rows['examen']}";
						echo "		</td>";
						echo "		<td bgcolor=''>";
						echo "			{$rows5['Descripcion']}";  //responsable
						echo "		</td>";
						echo "		<td bgcolor=''>";
						echo "			".$rows4['Codigo']."-".$rows4['Descripcion']."";
						echo "		</td>";
						echo "		<td align=center>";
						echo "			{$rows['usuario']}";
						echo "		</td>";
						if ( $wfec==date("Y-m-d"))
						{
							echo pintarColumnasXPerfil($solucionCitas, $wfec, $rows['id'], $cedula, $rows['nom_pac'], $validarProcesos, $wagendaMedica);
						}
					}
					else
					{
						$j-=1;
					}
				}
				
				 //****************************revisar lo de asiste *************************
				if ($caso==2 and $valCitas!="on" && $solucionCitas != "citasen")
				{
					$wagendaMedica = 'on';

					if($validarProcesos[$rows['cedula']]['Logeta'] != 'on')					
					{
						echo "	<tr $class id='rwPacienteAgendado_".$rows['id']."' cedula='{$rows['cedula']}' nombre='{$rows['nom_pac']}'>";
						echo "		<td align=center>";
						echo "			".$j."";
						echo "		</td>";
						echo "		<td align=center>";
						echo "			{$rows['fecha']}";
						echo "		</td>";
						echo "		<td align=center bgcolor=''>";
						echo "			{$rows['hi']}";
						echo "		</td>";
						echo "		<td bgcolor='' align=center id='tdCedulaAgenda_{$rows['cedula']}'><span class='doc_{$rows['cedula']}'>";
						echo "			{$rows['cedula']}";
						echo "		</span></td>";
						echo "		<td bgcolor='' id='tdNomPacAgenda_{$rows['cedula']}'>";
						echo "			{$rows['nom_pac']}";
						echo "		</td>";
						echo "		<td bgcolor=''>";
						echo "			{$rows5['Descripcion']}";  //responsable
						echo "		</td>";
						echo "		<td bgcolor=''>";
						echo "			".$rows4['Codigo']."-".$rows4['Descripcion']."";
						echo "		</td>";
						echo "		<td align=center>";
						echo "			{$rows['usuario']}";
						echo "		</td>";
						if ( $wfec==date("Y-m-d"))
						{
							echo "		<td align=center>";
							echo "			<input type='radio' name='rdAsiste".$rows['id']."' id='rdAsiste".$rows['id']."' onclick='asiste(\"rdAsiste".$rows['id']."\",\"".$rows['id']."\",\"".$mostrarCausa."\")' value='on'>";
							echo "		</td>";

							echo "		<td align=center>";
							echo "			<input type='radio' name='rdCancela".$rows['id']."' id='rdCancela".$rows['id']."' onclick='cancela(\"rdCancela".$rows['id']."\",\"".$rows['id']."\")' value='I'>";
							echo "		</td>";

							echo "		<td align=center>";
							echo "			<input type='radio' name='rdno_asiste".$rows['id']."' id='rdno_asiste".$rows['id']."' onclick='no_asiste(\"rdno_asiste".$rows['id']."\",\"".$rows['id']."\")' value='off'>";
							echo "		</td>";
						}
					}
					else
					{
						$j-=1;
					}

				}

			echo "	</tr>";
		} //for
	}
	else{
		echo "<center>NO HAY TURNOS ASIGNADOS PARA HOY</center>";
	}

	echo "</table>";

	if ($caso == 2 and $valCitas == "on")
	{
		echo "<br><br>";
		echo "<center><a href='../../IPS/Procesos/admision.php?ok=9&empresa=$wbasedato&wemp2=citascs' target='_blank'>Admision sin cita</a></center>";
    }
	echo "<br>";

	echo "<br>";
	 echo "<center>
	<a onclick='javascript:abrirVentanCitas(\"$solucionCitas\",\"$wemp_pmla\",\"$caso\",\"$wsw\",\"$fest\")' style='color:#1e90ff'>Asignar Sala</a></center>
	";

	echo "<meta name='met' id='met' url=pantallaAdmisionSala.php?solucionCitas=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&ccosto=".$ccosto."&caso=".$caso."&wsw=".@$wsw."&slDoctor=$slDoctor&valCitas=".$valCitas."&wfec=".$wfec."&fest=".$fest.">";

	echo "<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' />";
	echo "<br><br>";

	echo "<table align='left'><tr><td><a onclick='javascript:imprimir(\"$wemp_pmla\",\"$caso\",\"$wsw\",\"$solucionCitas\",\"$slDoctor\",\"$valCitas\",\"$wfec\")'><b>Imprimir</b></a></td></tr></table>";

	//div causa cancelacion
	echo "<div id='causa_cancelacion' style='display:none'>";
	echo "<center>";
	$tipo = "C";
    causas($tipo);
	echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
	echo "</center>";
	echo "</div>";

	//div causa no asiste
	echo "<div id='causa_noasiste' style='display:none'>";
	echo "<center>";
	$tipo = "NA";
    causas($tipo);
	echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
	echo "</center>";
	echo "</div>";


	//div causa demora
	echo "<div id='causa_demora' style='display:none'>";
	echo "<center>";
	$tipo = "DA";
    causas($tipo);
	echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
	echo "</center>";
	echo "</div>";

	echo "</form>";
	echo "<div id='fichoTurno' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'></div>";
	echo "<div id='fichoTurnoImp' style='display:none;'></div>";

	echo "<div id='divAsociarTurnoCita' title='ASOCIAR PACIENTES CON TURNO Y SIN CITA' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'>
			</div>";
	echo "<br><br><br><br>";
	
	echo "<div id='div_reiniciar_procedimiento' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'>
	</div>";
	
	
	echo "<br><br><br><br>";
	echo "</body>";
	echo "</html>";
}
?>


