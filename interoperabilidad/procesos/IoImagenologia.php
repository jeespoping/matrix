<?php
/**************************************************************************************************************************************
 * Modificaciones:
 * ====================================================================================================================================
 * Agosto 04 de 2020		Edwin MG	- En la función consultarPacienteConCita se tiene en cuenta el cco dónde el paciente tiene cita
 * Diciembre 17 de 2019		Edwin MG	- En caso de no enviarse un mensaje HL7 al sistema HIRUKO se envia un correo electronico a los
 *										  destinatarios configurados por tipo de orden en el campo Tipeml de la tabla hce_000015
 * Noviembre 21 de 2019		Edwin MG	- Se corrige el recibo de mensajes HL7 de agendas canceladas para pacientes ambulatorios
 **************************************************************************************************************************************/

//Prueba de envio de mensajes con ordenes
// $.post( "../../interoperabilidad/procesos/IoImagenologia.php", { historia: 551258, ingreso: 17, tipoOrden: 'A04', nroOrden: 989630, accion: 'agendarOrden', consultaAjax: '' } );
/**
 * Este script es llamado desde interoperabilidad.php
 * Se asume que:
 * Ya está incluido el conex
 * Existe la variable HL7
 */
include_once("root/comun.php");
include_once("./funcionesGeneralesEnvioHL7.php");

// El segmento PID contiene la información del paciente 
define("MSH_SISTEMA_ENVIA", 3 );

// El segmento PID contiene la información del paciente 
define("PID_IDENTIFICACION_PACIENTE", 2 );

// El segmento AIS contiene la información de la cita
define("AIS_ID_CITA", 1 );
define("AIS_FECHA_HORA_CITA", 4 );
define("AIS_ESTADO_ACTIVIDAD", 10 );

//El segmento OBR contiene la información del procedimiento, examen o estudio del paciente
define("OBR_NUMERO_ESTUDIO", 3 );
define("OBR_ESTUDIO", 4 );
define("OBR_ESTADO", 25 );
define("OBR_PRIORIDAD", 5 );
define("OBR_INIDCACION", 11 );
define("OBR_COMENTARIO", 13 );
define("PV1_SEDE", 10 );

//Url de la imagen tomada
define("OBX_URL", 1 );
define("OBX_URL_REPORTE", 5 );

if( !isset( $wemp_pmla ) )
	$wemp_pmla = "01";


class medicoDTO {
	var $tipoDocumento = "";
	var $numeroDocumento = "";
	var $nombre1 = "";
	var $nombre2 = "";
	var $apellido1 = "";
	var $apellido2 = "";
	var $telefono = "";
	var $registroMedico = "";
	var $interconsultante = "";
	var $tratante = "";
	var $codigoEspecialidad = "";
	var $usuarioMatrix = "";
	var $id = "";
}

function marcarEstudioComoEnviado( $conex, $wmovhos, $whce, $tipoOrden, $nroOrden, $item ){
	
	$val = false;
	
	//El campo detenv indica si debe enviar un mensaje hl7 cuando está activo
	//por tal motivo se apaga
	echo $sql = "UPDATE ".$wmovhos."_000159
			   SET Detenv = 'off'
			 WHERE Dettor = '".$tipoOrden."'
			   AND Detnro = '".$nroOrden."'
			   AND Detite = '".$item."'";
	
	$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	//El campo detenv indica si debe enviar un mensaje hl7 cuando está activo
	//por tal motivo se apaga
	$sql = "UPDATE ".$whce."_000028
			   SET Detenv = 'off'
			 WHERE Dettor = '".$tipoOrden."'
			   AND Detnro = '".$nroOrden."'
			   AND Detite = '".$item."'";
	
	$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_affected_rows($res) ){
		$val = true;
	}
	
	return $val;
}


/**
 *	consultarModalidadPorCup	2
 *	consultarSedePorCcoYCup		3	
 *	consultarPacienteConCita	1	Se puede
 */
function consultarTablaOfertadoPorTipoOrden( $conex, $wmovhos, $tipoOrden ){
	
	$val = false;
	
	//Buscando los estado según el estandar HL7
	$sql = "SELECT Valtoc
			  FROM ".$wmovhos."_000267
			 WHERE valtor = '".$tipoOrden."'
			   AND valest = 'on'";
	
	$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array($res) ){
		$val = $row['Valtoc'];
	}
	
	return $val;
}

function consultarTablaOferadosPorCco( $conex, $wmovhos, $cco ){
	
	$val = false;
	
	//Buscando los estado según el estandar HL7
	$sql = "SELECT Sedtor
			  FROM ".$wmovhos."_000264
			 WHERE sedcco = '".$cco."'
			   AND sedest = 'on'";
	
	$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array($res) ){
			
		$tipoOrden = $row['Sedtor'];	//Esta es la tabla dónde se encuentra el médico remitente
		
		$val = consultarTablaOfertadoPorTipoOrden( $conex, $wmovhos, $tipoOrden );
	}
	
	return $val;
}

function envarReporteMatrix( $conex ){
	
	$tipoOrden 	= "";
	$nroOrden	= "";
	
	$wemp_pmla 	= $_POST['wemp_pmla'];
	
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
	$whce 	 = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
	
	$historia 	= $_POST['historia'];
	$ingreso 	= $_POST['ingreso'];
	$idCita 	= $_POST['idCita'];
	
	$rows 		= consultarDatosPorId( $conex, $wmovhos, $idCita );
	$paciente 	= informacionPaciente( $conex, $wemp_pmla, $historia, $ingreso );
	
	$indicacion	= '';
	
	$sede 		= consultarSedePorCcoYCup( $conex, $wmovhos, $rows['Mvcsed'], $rows['Mvccup'] );
	
	$idMatrix = $datosCita['tipoOrden']."-".$datosCita['nroOrden'];
	
	$nroOrden	= $idCita;
	if( !empty( $rows['Mvcci'] ) && $rows['Mvcci'] == 'on' ){
		$item 		= "CC-".$idCita;
		$tipoOrden 	= "CC";
	}
	else{
		$item 		= "SC-".$idCita;
		$tipoOrden 	= "SC";
	}
	
	$datosCita = [
			'idHiruko' 			=> $row['Mvcidh'],
			'idAgenda' 			=> $row['Mvcida'],
			'estadoAgenda' 		=> "AG",
			'cco' 				=> $paciente['servicioActual'],
			'tipoOrden' 		=> $tipoOrden,
			'nroOrden' 			=> $nroOrden,
			'sede' 				=> $sede,
			'recepcionado'  	=> $rows['Mvcrec'],
			'cups' 				=> [[
										'cup' 			=> $rows['Mvccup'],
										'modalidad'		=> $rows['Mvcmod'],
										'sala'			=> $rows['Mvcsal'],
										'prioridad'		=> $rows['Mvcpri'],
										'item'			=> $item,
										'orden'			=> '',
										'justificacion'	=> '',
										'urlReporte'	=> $rows['Mvcurp'],
									]],
		];
	
	
	$mensaje 	= crearMensajesHL7ORMAgenda( $conex, $wemp_pmla, $paciente, $datosCita );
	
	$direccion 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ipHL7HirukoEnvioReporte' );

	$socket 	= stream_socket_client("tcp://$direccion", $errno, $errstr);

	if( $socket ){
		$val = fwrite($socket, utf8_encode($mensaje ) );
		
		fclose($socket);
	}
	
	registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'MATRIX-HIRUKO', $rows['Mvctor'], $rows['Mvcnor'], '', $mensaje );
	
	echo $mensaje;
	
}

/************************************************************************
 * Consulta las indicaciones que hay por cup 
 *
 * $conex		Tipo de orden
 * $whce		Solucion hce
 * $tipoOrden	Tipo de orden
 * $cup			CUP al cuál se le busca las indicaciones por cup
 ************************************************************************/
function consultarIndicacionesPorCup( $conex, $whce, $tipoOrden, $cup ){
	
	$val = [];
	
	//Buscando los estado según el estandar HL7
	$sql = "SELECT Tiptic
			  FROM ".$whce."_000015
			 WHERE codigo = '".$tipoOrden."'
			   AND estado = 'on'";
	
	$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array($res) ){
			
		$tablaIndicaciones = $row['Tiptic'];	//Esta es la tabla dónde se encuentra el médico remitente
		
		//Buscando los estado según el estandar HL7
		$sql = "SELECT Indind
				  FROM ".$tablaIndicaciones."
				 WHERE indcup  = '".$cup."'
				   AND Indind != ''
				   AND indest  = 'on'";
		
		$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		while( $row = mysql_fetch_array($res) ){
			$val[] = [
					'descripcion'	=> utf8_encode( $row['Indind'] ),
				];
		}
	}
	
	return $val;
}

/************************************************************************
 * Consulta el medico remitente según lo ingrsado por el usuario ($parametro)
 *
 * $conex		Tipo de orden
 * $whce		Solucion hce
 * $tipoOrden	Tipo de orden
 * $parametro	Número  de orden
 ************************************************************************/
function consultarMedicosRemitentes( $conex, $whce, $tipoOrden, $parametro ){
	
	$val = [];
	
	//Buscando los estado según el estandar HL7
	$sql = "SELECT Tipsmr, Tippmr
			  FROM ".$whce."_000015
			 WHERE codigo = '".$tipoOrden."'
			   AND Tiptmr = 'on'
			   AND estado = 'on'";
	
	$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array($res) ){
			
		$solucionMedicoRemitente = $row['Tipsmr'];	//Esta es la tabla dónde se encuentra el médico remitente
		
		$parametroMedicoTratante = $row['Tippmr'];				//Parametro del médico remitente
		
		//Buscando los estado según el estandar HL7
		$sql = "SELECT Opccod, Opcdes, Opctdo, Opcndo, Opcno1, Opcno2, Opcap1, Opcap2
				  FROM ".$solucionMedicoRemitente."_000004 a , ".$solucionMedicoRemitente."_000005 b
				 WHERE a.tblcod = '".$parametroMedicoTratante."'
				   AND a.tblest = 'on'
				   AND b.opctbl = a.tblcod 
				   AND b.opcest = 'on'
				   AND b.Opctdo != ''
				   AND ( b.opccod LIKE '%".$parametro."%'
				    OR b.opcdes LIKE '%".$parametro."%' )";
		
		$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		while( $row = mysql_fetch_array($res) ){
			$val[] = [
					'codigo' 			=> utf8_encode( $row['Opccod'] ),
					'descripcion' 		=> utf8_encode( $row['Opcdes'] ),
					'tipoDocumento'		=> utf8_encode( $row['Opctdo'] ),
					'numeroDocumento'	=> utf8_encode( $row['Opcndo'] ),
					'nombreCompleto'	=> utf8_encode( trim( $row['Opcno1']." ".$row['Opcno2'] )." ".trim( $row['Opcap1']." ".$row['Opcap2'] ) ),
					'nombresCompletos'	=> utf8_encode( trim( $row['Opcno1']." ".$row['Opcno2'] ) ),
					'apellidosCompletos'=> utf8_encode( trim( $row['Opcap1']." ".$row['Opcap2'] ) ),
					'nombre1' 			=> utf8_encode( $row['Opcno1'] ),
					'nombre2' 			=> utf8_encode( $row['Opcno2'] ),
					'apellido1'			=> utf8_encode( $row['Opcap1'] ),
					'apellido2'			=> utf8_encode( $row['Opcap2'] ),
					'registroMedico'	=> utf8_encode( $row['Opcrme'] ),
					'label' 			=> utf8_encode( $row['Opcdes'] ),
					'value' 			=> utf8_encode( $row['Opcdes'] ),
				];
		}
	}
	
	return $val;
}


/************************************************************************
 * Consulta el código matrix del médico que ordeno el estudio
 *
 * $conex		Tipo de orden
 * $whce		Solucion hce
 * $tipoOrden	Tipo de orden
 * $nroOrden	Número  de orden
 * $item		Item de orden
 ************************************************************************/
function consultarCodigoMedicoPorEstudio( $conex, $whce, $tipoOrden, $nroOrden, $item ){
	
	$val = [ 'medico' => '', 'justificacion' => '' ];
	
	//Buscando los estado según el estandar HL7
	$sql = "SELECT Detusu, Detjus
			  FROM ".$whce."_000028
			 WHERE Dettor = '".$tipoOrden."'
			   AND Detnro = '".$nroOrden."'
			   AND Detite = '".$item."'";
	
	$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array($res) ){
		$val = [ 
					'medico' 		=> $row['Detusu'], 
					'justificacion' => $row['Detjus'],
				];
	}
	
	return $val;
}

/************************************************************************
 * Indica si un estudio(CUP) tiene transcripción en matrix o no
 *
 * $conex		Tipo de orden
 * $whce		Solucion hce
 * $tor			Tipo de orden
 * $cup			Código cup del estudio
 ************************************************************************/
function tieneTranscripcionMatrix( $conex, $whce, $tor, $cup ){
	
	$val = false;
	
	//Buscando los estado según el estandar HL7
	$sql = "SELECT Tipstm
			  FROM ".$whce."_000015
			 WHERE codigo = '".$tor."'
			   AND Tipttm = 'on'
			   AND estado = 'on'";
	
	$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array($res) ){
		
		$solucionTranscripcion = $row['Tipstm'];
		
		//Buscando los estado según el estandar HL7
		$sql = "SELECT Enfcod
				  FROM ".$solucionTranscripcion."_000001
				 WHERE Enfcup = '".$cup."'
				   AND Enfest = 'on'
				   AND Enfttm = 'on'";
		
		$res = mysql_query($sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $row = mysql_fetch_array($res) ){
			$val = true;
		}
	}
	
	return $val;
}

function consultarEmails( $conex, $whce, $tipoOrden ){
	
	$val = [];

	//Buscando los estado según el estandar HL7
	$sql = "SELECT Tipeml
			  FROM ".$whce."_000015
			 WHERE codigo = '".$tipoOrden."'
			";
	
	$res = mysql_query($sql, $conex);
	
	if( $res ){
		
		if( $row = mysql_fetch_array($res) ){
			$val = explode( ",", $row['Tipeml'] );
		}
	}

	return $val;
	
}

function consultarIdPorAgenda( $conex, $wmovhos, $idHiruko ){
		
	$val = false;

	//Buscando los estado según el estandar HL7
	$sql = "SELECT id
			  FROM ".$wmovhos."_000268
			 WHERE Mvcidh = '".$idHiruko."'
			   AND Mvcest = 'on'";
	
	$res = mysql_query($sql, $conex);
	
	if( $res ){
		
		if( $row = mysql_fetch_array($res) ){
			$val = $row['id'];
		}
	}

	return $val;
}

function actualizarUrlPorItem( $conex, $whce, $tipoOrden, $nroOrden, $item, $url, $urlReporte, $historia, $ingreso ){
	
	$respuesta = array('message'=>'', 'result'=>array( 'proceso' => true ), 'status'=>'' );
	
	$val = false;

	//Busco la orden por
	$sql = "UPDATE ".$whce."_000028
			   SET Deturl = '".$url."',
			       Deturp = '".$urlReporte."'
			 WHERE Dettor = '".$tipoOrden."'
			   AND Detnro = '".$nroOrden."'
			   AND Detite = '".$item."'
			";

	$res = mysql_query( $sql, $conex );

	if( $res ){
		
		$rowsaffect = mysql_affected_rows();
		
		if( $rowsaffect === false ){
			$respuesta['message'] = 'No se encuentra estudio para asignar resultados';
			$respuesta['result']['proceso'] = false;
		}
		else{
			if( !empty($url) ){
				registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Url imagen asignado', $url );
			}
			
			if( !empty($urlReporte) ){
				registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Url reporte asignado', $urlReporte );
			}
		}
	}
	else{
		$respuesta['message'] = 'No se encontro la orden';
		$respuesta['result']['proceso'] = false;
	}

	return $respuesta;
}

function actualizarUrl( $conex, $whce, $tipoOrden, $nroOrden, $url ){
	
	$respuesta = array('message'=>'', 'result'=>array( 'proceso' => true ), 'status'=>'' );
	
	$val = false;

	//Busco la orden por
	$sql = "UPDATE ".$whce."_000027
			   SET Ordurl = '".$url."'
			 WHERE Ordest = 'on'
			   AND Ordtor = '".$tipoOrden."'
			   AND Ordnro = '".$nroOrden."'
			";

	$res = mysql_query( $sql, $conex );

	if( $res ){
		
		$rowsaffect = mysql_affected_rows();
		
		if( $rowsaffect === false ){
			$respuesta['message'] = 'No se encuentra orden para anexar';
			$respuesta['result']['proceso'] = false;
		}
	}
	else{
		$respuesta['message'] = 'No se encontro la orden';
		$respuesta['result']['proceso'] = false;
	}

	return $respuesta;
}

function cambiarEstadoExamen( $conex, $wemp_pmla, $tipoOrden, $nroOrden, $item, $estado, $fecha, $hora, $justificacion, $historia, $ingreso ){
	
	$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );
	
	$whce 	 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce " );
	$wmovhos 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos " );
	
	$estadoPorTipoOrden = consultarAliasPorAplicacion( $conex, $wemp_pmla, "permitirCambiarEstadoInteroperabilidadPorTipoOrden" );
		
	$estadoPorTipoOrden = explode( "-", $estadoPorTipoOrden );
	
	$permitirCambiarEstado = in_array( $tipoOrden, $estadoPorTipoOrden ) ? false : true;
	
	$val = true;
	
	//Buscando los estado según el estandar HL7
	$sql = "SELECT *
			  FROM ".$wmovhos."_000257, ".$wmovhos."_000045
			 WHERE Esthl7 = '".$estado."'
			   AND Estest = 'on'
               AND Estepc = Eexcod";
	
	$resEstado = mysql_query($sql, $conex);
	
	if( $resEstado ){
		
		if( $row = mysql_fetch_array($resEstado) ){
			
			//Valido si la orden existe
			$sql = "SELECT *
					  FROM ".$whce."_000028
					 WHERE Dettor = '".$tipoOrden."'
					   AND Detnro = '".$nroOrden."'
					   AND Detite = '".$item."'";
			
			$resExiste = mysql_query( $sql, $conex );
			$num = mysql_num_rows( $resExiste );
			
			if( $num > 0 ){
				
				$rowsOrdenClinica = mysql_fetch_array( $resExiste );
				
				$whereFecha = '';
				if( !empty( $fecha ) ){
					$whereFecha = ",Detfec = '".$fecha."',Dethci = '".$hora."'";
				}
				
				//Si el estado es cancelado, deje el registro como pendiente de por examen cancelado (Detplc)
				$pendienteLecuraCancelado = $rowsOrdenClinica['Detplc'];
				if( $row['Estplp'] == 'on' ){
					$pendienteLecuraCancelado = 'on';
				}
				
				//Si no permite cambiar Estado, dejo como estaba
				$estadoOrden = $row['Estepc'];
				if( !$permitirCambiarEstado )
					$estadoOrden = $rowsOrdenClinica['Detesi'];
				
				//Actuzando el estado de la orden
				$sql = "UPDATE ".$wmovhos."_000159
						   SET Detesi = '".$estadoOrden."',
							   Deteex = '".$estado."',
							   Detjoc = '".mysql_escape_string( $justificacion )."',
							   Detfme = '".date( "Y-m-d" )."',
							   Dethme = '".date( "H:i:s" )."',
							   Detcor = '".$row['Esteco']."',
							   Detplc = '".$pendienteLecuraCancelado."'
							   $whereFecha
						 WHERE Dettor = '".$tipoOrden."'
						   AND Detnro = '".$nroOrden."'
						   AND Detite = '".$item."'";
				
				$res = mysql_query( $sql, $conex );
				
				//Actuzando el estado de la orden
				$sql = "UPDATE ".$whce."_000028
						   SET Detesi = '".$estadoOrden."',
							   Deteex = '".$estado."',
							   Detjoc = '".mysql_escape_string( $justificacion )."',
							   Detfme = '".date( "Y-m-d" )."',
							   Dethme = '".date( "H:i:s" )."',
							   Detcor = '".$row['Esteco']."',
							   Detplc = '".$pendienteLecuraCancelado."'
							   $whereFecha
						 WHERE Dettor = '".$tipoOrden."'
						   AND Detnro = '".$nroOrden."'
						   AND Detite = '".$item."'";
				
				$res = mysql_query( $sql, $conex );
				
                $estGeneraCca = $row['Eexcca'];
				
				/* FUNCION QUE REALIZA LA VALIDACION DE CARGOS AUTOMATICOS */
				$worigen = 'Interoperabilidad - Imex';
				interoperabilidadCargosAutomaticos($conex, $wemp_pmla, $whce, $wmovhos, $worigen, $nroOrden, $item, $tipoOrden, $estGeneraCca);

				if( $res ){
					
					registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Cambio de estado externo', $estado."-".$row['Estdes']."-".$row['Estdpa'] );
					registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Cambio de estado ordenes', $row['Estepc'] );
					
					if( !empty($justificacion) ){
						registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Justificacion asignada', $justificacion );
					}
					
					if( !empty( $fecha ) ){
						registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Cita asignada', $fecha." ".$hora );
					}
					
					$num = mysql_affected_rows();
					
					if( $num === false ){
						$respuesta['message'] 	= 'No se logró actualizar el estudio';
						endRoutine( $respuesta, 400 );
						$val = false;
					}
					else{
						
						if( strtolower( $row['Estple'] ) == 'on' ){
							
							$sql = "UPDATE ".$whce."_000027
									   SET Ordple = 'on'
									 WHERE Ordtor = '".$tipoOrden."'
									   AND Ordnro = '".$nroOrden."'
									   ";
							
							$res = mysql_query( $sql, $conex );
							
							$num = mysql_affected_rows();
					
							if( $num === false ){
								$respuesta['message'] = 'No se logra actualizar el estado pendiente de lectura de la orden';
								endRoutine( $respuesta, 400 );
								$val = false;
							}
							else{
								registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, 0, 'Cambio de estado', 'Orden pendiente de lectura' );
							}
						}
					}
				}
				else{
					$respuesta['message'] 	= 'No se encontró el estudio a actualizar';
					endRoutine( $respuesta, 400 );
					$val = false;
				}
			}
			else{
				$respuesta['message'] 	= 'El estudio de la orden solicitada no se encuentra';
				endRoutine( $respuesta, 400 );
				$val = false;
			}
		}
		else{
			$respuesta['message'] 	= 'Estado de estudio desconocido';
			endRoutine( $respuesta, 400 );
			$val = false;
		}
	}
	else{
		$respuesta['message'] 	= 'Estado de estudio no encontrado';
		endRoutine( $respuesta, 400 );
		$val = false;
	}
	
	return $val;
}

function modalidadesPorCup( $conex, $wmovhos, $cup )
{
	//Consultando modalidades y salas
	$sql = "SELECT Promod, b.Moddes
			  FROM ".$wmovhos."_000271 a, ".$wmovhos."_000262 b
			 WHERE a.Procup = '".$cup."'
			   AND a.Proest = 'on'
			   AND b.Modcod = a.Promod
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $rows = mysql_fetch_array($res) ){
		$modalidadesPorCup[] = [
			'codigo' 	  => $rows['Promod'],
			'descripcion' => utf8_encode( $rows['Moddes'] ),
		];
	}

	return $modalidadesPorCup;
}

function consultarModalidadPorCup( $conex, $wmovhos, $cup ){
	
	$val = "";

	$sql = "SELECT *
			  FROM ".$wmovhos."_000271 a
			 WHERE a.Procup = '".$cup."'
			   AND a.Proest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows['Promod'];
	}
	
	return $val;
}


/********************************************************************************
 * Consulta las modalidades por cup
 ********************************************************************************/
function consultarModalidadesPorCup( $conex, $wmovhos, $cup ){
	
	$val = [];

	$sql = "SELECT *
			  FROM ".$wmovhos."_000271 a
			 WHERE a.Procup = '".$cup."'
			   AND a.Proest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $rows = mysql_fetch_array($res) ){
		$val[] = $rows['Promod'];
	}
	
	return $val;
}

function consultarUltimosDatosCita( $conex, $wemp_pmla, $historia, $ingreso, $cco ){
	
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	
	$val = [];

	$sql = "SELECT *
			  FROM ".$wmovhos."_000268 a
			 WHERE a.Mvchis = '".$historia."'
			   AND a.Mvcing = '".$ingreso."'
			   AND a.Mvcsed = '".$cco."'
			   AND a.Mvcest = 'on'
		  ORDER BY fecha_data DESC, hora_data DESC
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows;
	}
	
	return $val;
}

function consultarEstudiosPorOrden( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden, $historia, $ingreso ){
	
	$val = [];
	
	$tabla 		= "";
	$c_estado 	= "";
	$campo  	= "";
	
	$sql = "SELECT a.Valtoc, a.Valeoc, a.Valcoc
			  FROM ".$wmovhos."_000267 a
			 WHERE a.Valtor  = '".$tipoOrden."'
			   AND a.Valest  = 'on'
			   AND a.Valtoc != ''
			   AND a.Valeoc != ''
			   AND a.Valcoc != ''
			";
	
	$resOfertas = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rowOferta = mysql_fetch_array( $resOfertas ) ){
		
		$tabla 		= $rowOferta['Valtoc'];	//tabla de cups ofertados
		$c_estado 	= $rowOferta['Valeoc']; //tabla de cups ofertados
		$campo  	= $rowOferta['Valcoc']; //tabla de cups ofertados
		
		$sql = "SELECT a.Dettor, a.Detnro, a.Detite, a.Detcod, c.Codigo, c.Nombre, b.Codcups, a.id, Detesi, Detlog, Detjus, Deteex, b.Codigo as Codlma, Detusu
				  FROM ".$whce."_000028 a, ".$whce."_000047 b, root_000012 c
				 WHERE a.Dettor = '".$tipoOrden."'
				   AND a.Detnro = '".$nroOrden."'
				   AND a.Detcod = b.codigo
				   AND a.Detenv = 'on'
				   AND a.Detest = 'on'
				   AND b.Estado = 'on'
				   AND b.Codcups= c.Codigo
				 UNION
				SELECT a.Dettor, a.Detnro, a.Detite, a.Detcod, c.Codigo, c.Nombre, b.Codcups, a.id, Detesi, Detlog, Detjus, Deteex, b.Codigo as Codlma, Detusu
				  FROM ".$whce."_000028 a, ".$whce."_000017 b, root_000012 c
				 WHERE a.Dettor = '".$tipoOrden."'
				   AND a.Detnro = '".$nroOrden."'
				   AND a.Detcod = b.codigo
				   AND a.Detest = 'on'
				   AND a.Detenv = 'on'
				   AND b.Nuevo  = 'on'
				   AND b.Estado = 'on'
				   AND b.Codcups= c.Codigo
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		while( $row = mysql_fetch_array( $res ) ){
						
			//Busco si el cups es ofertado
			$sql = "SELECT *
					  FROM ".$tabla." a
					 WHERE a.".$c_estado." = 'on'
					   AND a.".$campo." = '".$row['Codcups']."'
					";
			
			$resOfertas = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			$num = mysql_num_rows( $resOfertas );
			
			//Si está ofertado deja ordenar
			if( $num > 0 ){
				
				$val[] = [
						'codigo' 		=> $row[ 'Codcups' ] ,
						'descripcion' 	=> $row[ 'Nombre' ] ,
						'item' 			=> $row[ 'Detite' ] ,
						'estado'		=> $row[ 'Detesi' ] ,
						'justificacion'	=> $row[ 'Detjus' ] ,
						'estadoExterno'	=> $row[ 'Deteex' ] ,	//Estado en que se encuentra la cita hl7
						'medico'		=> $row[ 'Detusu' ] ,	//Médico que ordena
						'procod'		=> $row[ 'Codlma' ] ,	//2022-01-28 - Sebastián Nevado: Se agrega código de procedmiento
					];
			}
			else
			{	
				registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $row['Dettor'], $row['Detnro'], $row['Detite'], 'CUP no ofertado', 'Estudio no ofertado '.$row['Codcups']."-".$row['Nombre']." (".$row['Codlma'].")" );
			}
		}
	}
	
	return $val;
}

function agregarDetalleMovimiento( $conex, $wemp_pmla, $wmovhos, $datos ){
	
	$val = false;
	
	$fecha_data = date("Y-m-d");
	$hora_data  = date("H:i:s");
	
	$dmcidc 	= empty( $datos['idMatrix'] ) ? '' : $set[] = $datos[''];
	$dmcidh 	= empty( $datos['idHiruko'] ) ? '' : $set[] = $datos['idHiruko'];
	$dmcida 	= empty( $datos['idAgenda'] ) ? '' : $set[] = $datos['idAgenda'];
	$dmctdo 	= empty( $datos['tipoDocumento'] ) ? '' : $set[] = $datos['tipoDocumento'];
	$dmcdoc 	= empty( $datos['documento'] ) ? '' : $set[] = $datos['documento'];
	$dmchis 	= empty( $datos['historia'] ) ? '' : $set[] = $datos['historia'];
	$dmcing 	= empty( $datos['ingreso'] ) ? '' : $set[] = $datos['ingreso'];
	$dmccco 	= empty( $datos['cco'] ) ? '' : $set[] = $datos['cco'];
	$dmcfec 	= empty( $datos['fechaCita'] ) ? '' : $set[] = $datos['fechaCita'];
	$dmchor 	= empty( $datos['horaCita'] ) ? '' : $set[] = $datos['horaCita'];
	$dmcmod 	= empty( $datos['modalidad'] ) ? '' : $set[] = $datos['modalidad'];
	$dmcsal 	= empty( $datos['sala'] ) ? '' : $set[] = $datos['sala'];
	$dmcpri 	= empty( $datos['prioridad'] ) ? '' : $set[] = $datos['prioridad'];
	$dmcest 	= 'on';
	$dmcrec 	= empty( $datos['recepcionado'] ) ? 'off' : $datos['recepcionado'];
	$dmccci 	= empty( $datos['conCita'] ) ? 'off' : $datos['conCita'];
	$dmccup 	= empty( $datos['cup'] ) ? '' : $set[] = $datos['cup'];
	$dmcsed 	= empty( $datos['sede'] ) ? '' : $set[] = $datos['sede'];
	$dmctor 	= empty( $datos['tipoOrden'] ) ? '' : $set[] = $datos['tipoOrden'];
	$dmcnro 	= empty( $datos['nroOrden'] ) ? '' : $set[] = $datos['nroOrden'];
	$dmcite 	= empty( $datos['item'] ) ? '' : $datos['item'];
	
	// `Dmcidc`, `Dmccup`, `Dmcmod`, `Dmcsal`, `Dmcpri`, `Dmcest`, `Dmcrec`, `Dmctor`, `Dmcnro`, `Dmcite`
	
	//Consultando modalidades y salas
	$sql = "INSERT INTO 
				".$wmovhos."_000269(    Medico    , Fecha_data      ,  Hora_data      ,    Dmcidc   ,    Dmccup   ,    Dmcmod   ,   Dmcsal    ,    Dmcpri   ,    Dmctor   ,    Dmcnro   ,    Dmcite   , Dmcest, Seguridad )
							 VALUES( '".$wmovhos."','".$fecha_data."','".$hora_data."','".$dmcidc."','".$dmccup."','".$dmcmod."','".$dmcsal."','".$dmcpri."','".$dmctor."','".$dmcnro."','".$dmcite."',  'on' , 'C-".$wmovhos."' )
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = mysql_insert_id();
	}
	
	return $val;
}



function procesarMsgORU( $conex, $wemp_pmla, $HL7 )
{
	/*
	MSH|^~\&|IMEXHS|HOSIX|MEDICOSNET|MEDICOSNET|20190406160830||ORM^O01|454479|P|2.5||||AL
	PID|454479|DNI^29309668|||CARMEN ROSA^DEL CARPIO CASTELO||19460715|F|
	ORC|SC|20190000026071|||CM||^^^20190406132816^20190406160805||||||||||||||||||F
	OBR|454479|20190000026071||G900205^RESONANCIA DE COLUMNA LUMBAR : LUMBO - SACRA||20190406160830|
	NTE|||https://cliente.hiruko.com.co/viewer/view?sd=1.2.4.0.13.1.4.2252867.20190406132425199.1
	OBX|1|ED|||3154250_2593517.PDF^TEXT^PDF^Base64^JVBERi0xLjMKMSAwIG9iago8PCAvVHlwZSAvQ2F0Y
	WxvZwovT3V0bGluZXMgMiAwIFIKL1BhZ2Vz@IDMgMCBSID4+CmVuZG9iagoyIDAgb2JqCjw8IC9UeXBlIC9PdXRs
	aW5lcyAvQ291bnQgMCA+Pgpl@bmRvYmoKMyAwIG9iago8PCAvVHlwZSAvUGFnZXMKL0tpZHMgWzYgMCBSCl
	0KL0NvdW50IDEKL1Jl@c291cmNlcyA8PAovUHJvY1NldCA0IDAgUgovRm9udCA8PCAKL0YxIDggMCBSCi9GMiA5I
	DAgUgo+@Pgo+PgovTWVkaWFCb3ggWzAuMDAwIDAuMDAwIDYxMi4wMDAgNzkyLjAwMF0KID4+CmVuZG9ia
	go0@IDAgb2JqClsvUERGIC9UZXh0IF0KZW5kb2JqCjUgMCBvYmoKPDwKL1Byb2R1Y2VyIChkb21wZGYg@PDBm
	NDE4YzZiPiArIENQREYpCi9DcmVhdGlvbkRhdGUgKEQ6MjAxOTA0MDUyMDU1MDQtMDUnMDAn@KQovTW9k
	RGF0ZSAoRDoyMDE5MDQwNTIwNTUwNC0wNScwMCcpCi9UaXRsZSAoUERGIFJlcG9ydCkK@Pj4KZW5kb2JqCjY
	gMCBvYmoKPDwgL1R5cGUgL1BhZ2UKL01lZGlhQm94IFswLjAwMCAwLjAwMCA2@MTIuMDAwIDc5Mi4wMDB
	dCi9QYXJlbnQgMyAwIFIK.....
	*/
	
	$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );
	
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	
	if( $HL7['PID'] && $HL7['OBR'] )
	{
		list( $tipoDocumento, $nroDocumento ) 	= explode( "^", $HL7['PID'][0][ PID_IDENTIFICACION_PACIENTE ] );
		
		$sede = consultarSedePorCodigo( $conex, $wmovhos, $sede );
		
		//Reviso si es una orden (paciente hospitalizado) o no
		$esOrden = false;
		
		if( !empty($idMatrix) )
		{
			list( $sinuso, $idMatrix ) = explode( "-", $idMatrix );
			$esOrden = !( $sinuso == 'CC' || $sinuso == 'SC' );
		}

		//Siempre se recibe un solo OBR
		foreach( $HL7['OBR'] as $kOBR => $OBR )
		{
			$esOrden = false;
			
			$datosOrden  = explode( "-", $OBR[ OBR_NUMERO_ESTUDIO ] );
			
			$esOrden = !( $datosOrden[0] == 'CC' || $datosOrden[1] == 'SC' );
			
			if( $esOrden ){
				$idMatrix = consultarIdPorOrden( $conex, $wmovhos, $datosOrden[0], $datosOrden[1], $datosOrden[2] );
			}
			else{
				$idMatrix = $datosOrden[1];
			}
			
			
			list( $cup, $modalidad, $sala, $descripcionCUP ) = explode( "^", $OBR[ OBR_ESTUDIO ] );
			$prioridad = $OBR[ OBR_PRIORIDAD ];
			// $idMatrix  = $OBR[ OBR_NUMERO_ESTUDIO ];
		}
		
		$datosCita = [
						'idMatrix' 			=> $idMatrix,
						'estadoResultado'	=> '',
						'url' 				=> '',
					];
		
		if( !empty($idMatrix) ){
			//Si el id existe, hay que actualizar los datos correspondientes
			actualizarMovimiento( $conex, $wmovhos, $idMatrix, $datosCita );
		}
	}
	else{
		$respuesta['message'] 	= 'Mensaje no reconocido';
		endRoutine( $respuesta, 400 );
	}
}

function procesarMsgORM( $conex, $wemp_pmla, $HL7, $message )
{
	$datosCita = [];
	
	$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );
	
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	$whce 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
	
	//Siempre se espera un segmento AIS y un OBR
	if( $HL7['AIS'] && $HL7['OBR'] )
	{
		$historia 	= '';
		$ingreso 	= '';
		$tipoOrden	= ''; 
		$nroOrden	= '';
		$item		= '';
		
		$tipoDocumento	= ""; 
		$nroDocumento 	= "";
		if( $HL7['PID'] && $HL7['PID'][0] ){
			list( $tipoDocumento, $nroDocumento ) 	= explode( "^", $HL7['PID'][0][ PID_IDENTIFICACION_PACIENTE ] );
			
			//Si existe tipo de documento y nro de documento, la guardo
			if( !empty($tipoDocumento) )
				$datosCita[ 'tipoDocumento' ] = $tipoDocumento;
						
			if( !empty($nroDocumento) )
				$datosCita[ 'documento' ] = $nroDocumento;
		}
		
		//Si recibo un segmento PV1 se guarda los datos en en array que se usará para actualizar o insertar datos
		if( $HL7['PV1'] && $HL7['PV1'][0] ){
			
			list( $sinusar, $sede ) = explode( "^", $HL7['PV1'][0][ PV1_SEDE ] );
			
			$sede = consultarSedePorCodigo( $conex, $wmovhos, $sede );
			
			$datosCita['cco']	= $sede['cco'];
			$datosCita['sede']	= $sede['cco'];
			
			if( empty( $sede['cco'] ) ){
				
				registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Proceso con sede no iniciado', $message );
				
				registrarMsgLogHl7( $conex, $wmovhos, '', '', '', '', 'HIRUKO-MATRIX', '', '', '', $message );
				
				$respuesta['message'] 	= 'Proceso con sede no iniciado';
				endRoutine( $respuesta, 400 );
			}
		}
		
		$url 		= "";
		$urlReporte = "";
		if( $HL7['OBX'] && $HL7['OBX'][0] ){
			$url 		= trim( $HL7['OBX'][0][ OBX_URL ] ); 
			$urlReporte = $HL7['OBX'][0][ OBX_URL_REPORTE ];
			
			list( $urlReporte ) = explode( "^",$HL7['OBX'][0][ OBX_URL_REPORTE ] );
			
			if( !empty( $url ) )
				$url		= "https://".$url;
			
			if( !empty($urlReporte) )
				$urlReporte = "https://".$urlReporte;
		}
		
		//El segmento AIS contiene la información de la cita, siempre se espera
		list( $idMatrix, $idHiruko, $idAgenda ) = explode( "^", $HL7['AIS'][0][ AIS_ID_CITA ] );
		
		$esOrden = false;
		
		if( !empty($idMatrix) )
		{
			list( $sinuso, $idMatrix ) = explode( "-", $idMatrix );
			$esOrden = !( $sinuso == 'CC' || $sinuso == 'SC' );
		}
		
		$fechaHoraCita 	= $HL7['AIS'][0][ AIS_FECHA_HORA_CITA ];
		
		list( $fechaCita, $horaCita ) = explode( "^", $fechaHoraCita );
		$estado 		= $HL7['AIS'][0][ AIS_ESTADO_ACTIVIDAD ];
		
		$comentario = '';
		$indicacion = '';

		//Siempre se recibe un solo OBR
		foreach( $HL7['OBR'] as $kOBR => $OBR )
		{
			list( $cup, $modalidad, $sala, $descripcionCUP ) = explode( "^", $OBR[ OBR_ESTUDIO ] );
			$prioridad = $OBR[ OBR_PRIORIDAD ];
			
			$datosOrden  = explode( "-", $OBR[ OBR_NUMERO_ESTUDIO ] );
			$nroOrden = $datosOrden[1];
			
			if( $esOrden ){
				$idMatrix = consultarIdPorOrden( $conex, $wmovhos, $datosOrden[0], $datosOrden[1], $datosOrden[2] );
			}
			else{
				list( $sinuso, $idMatrix ) = $datosOrden;
			}
			
			$comentario = utf8_decode( $OBR[ OBR_COMENTARIO ] );
			
			$indicacion = utf8_decode( $OBR[ OBR_INIDCACION ] );
			
			//Siempre se recibe un solo OBR
			break;
		}
		
		if( !empty($estado) || $estado === '0' ){
			
			//El estado depende de donde viene el mensaje, si de la AGENDA o de HIRUKO
			if( $HL7['MSH'][0][ MSH_SISTEMA_ENVIA ] == 'HIRUKO' ){
				$datosCita['estadoResultado']	= $estado;
			}
			else{
				$datosCita['estadoCita']	= $estado;
			}
		}
		
		// Si el id de Matrix es vacio pero existe el id de agenda, puede ser que sea la modificación a una agenda
		// ya asignada por tanto busco el id en matrix por el id de Hiruko
		if( empty($idMatrix) && !empty($idHiruko) ){
			$idMatrix = consultarIdPorAgenda( $conex, $wmovhos, $idHiruko );
			
			if( !$idMatrix ) $idMatrix = "";
		}
		
		$tieneTranscripcionMatrix = tieneTranscripcionMatrix( $conex, $whce, $sede['tipoOrden'], $cup );
		
		if( $tieneTranscripcionMatrix ){
			$urlReporte = '';
		}
		
		$datosCita['idMatrix'] 			= $idMatrix;
		$datosCita['idHiruko'] 			= $idHiruko;
		$datosCita['idAgenda'] 			= $idAgenda;
		$datosCita['modalidad'] 		= $modalidad;
		$datosCita['sala'] 				= $sala;
		$datosCita['prioridad'] 		= $prioridad ;
		$datosCita['conCita'] 			= empty( $idAgenda ) ? 'off' : 'on';
		$datosCita['fechaCita']			= $fechaCita;
		$datosCita['horaCita']			= $horaCita;
		$datosCita['url']				= $url;
		$datosCita['urlReporte']		= $urlReporte;
		
		if( !empty( $indicacion ) ){
			$datosCita['indicaciones'] = $indicacion;
		}
		
		//Si no es una orden, se puede agregar cup
		//Si es una orden médica el cup ya existe y no se puede modificar
		// if( empty( $tipoOrden ) || empty( $nroOrden ) || empty( $item ) ){
		if( empty( $nroOrden ) ){
			$datosCita['cup'] = $cup;
		}
		
		if( empty($idMatrix) ){
			
			//Si el idMatrix es vacio significa que el paciente no se encuentra en la BD
			//Por tal motivo consulto los datos adicionales que pueda obtener del paciente
			$pac = consultarHistoriaPaciente( $conex, $wemp_pmla, $tipoDocumento, $nroDocumento );
			
			if( count($pac) > 0 ){
				if( !empty( $pac['historia'] ) ){
					$historia = $datosCita['historia'] = $pac['historia'];
				}
			}
			
			$idCita = agregarMovimiento( $conex, $wemp_pmla, $wmovhos, $datosCita );
			
			$idMatrix = $idCita;
		}
		else{
			//Si el id existe, hay que actualizar los datos correspondientes
			actualizarMovimiento( $conex, $wmovhos, $idMatrix, $datosCita );
		}
		
		
		//Si hay un estado se modifica el estado de las ordenes solo si es posible
		if( !empty($estado) || $estado === '0' ){
			
			$textCita = '';
			if( !empty( $fechaCita ) && !empty( $horaCita ) ){
				
				$datosSala = consultarSala( $conex, $wmovhos, $sala );
				
				$trComentario = '';
				if( !empty($comentario) )
				{	
					$trComentario ="<tr>
										<td colspan=2><b>Comentarios:</b><br><b>".date("Y-m-d H:i:s:")."</b><br>$comentario</td>
									</tr>";
				}
				
				$textCita = "<table>
								<tr>
									<td>Fecha</td><td><b>".$fechaCita."</b></td>
								</tr>
								<tr>
									<td>Hora</td><td><b>".$horaCita."</b></td>
								</tr>
								<tr>
									<td>Sala</td><td><b>".$datosSala['descripcion']."</b></td>
								</tr>
								$trComentario
							</table>";
			}
			 
			$rows = consultarDatosPorId( $conex, $wmovhos, $idMatrix );
			
			$historia 		= $rows['Mvchis'];
			$ingreso 		= $rows['Mvcing'];
			$tipoOrden		= $rows['Mvctor'];
			$nroOrden		= $rows['Mvcnro'];
			$item			= $rows['Mvcite'];
			
			registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $tipoDocumento, $nroDocumento, 'HIRUKO-MATRIX', $tipoOrden, $nroOrden, $item, $message );
			
			if( !empty($comentario) )
				registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Comentario asignado', $comentario );
			
			//Si tiene tipo de orden, numero de orden e item significa que está en orden y por tanto voy a actualizar los datos necesario en ordenes
			if( !empty($rows['Mvctor']) && !empty($rows['Mvcnro']) && !empty($rows['Mvcite']) ){
				cambiarEstadoExamen( $conex, $wemp_pmla, $rows['Mvctor'], $rows['Mvcnro'], $rows['Mvcite'], $estado, $fechaCita, $horaCita, $comentario, $historia, $ingreso );
				
				if( !empty($url) || !empty($urlReporte) ){
					actualizarUrlPorItem( $conex, $whce, $rows['Mvctor'], $rows['Mvcnro'], $rows['Mvcite'], $url, $urlReporte, $historia, $ingreso );
				}
			}
			
		}
		
	}
	else{
		
		registrarMsgLogHl7( $conex, $wmovhos, '', '', '', '', 'HIRUKO-MATRIX', '', '', '', $message );
		
		$respuesta['message'] 	= 'Mensaje no reconocido';
		endRoutine( $respuesta, 400 );
	}
}

function consultarSedePorTipoOrden( $conex, $wmovhos, $tor ){
	
	$val = [];
	
	//Consultando prioridades
	$sql = "SELECT *
			  FROM ".$wmovhos."_000264
			 WHERE Sedtor = '".$tor."'
			   AND Sedest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		$val = [
					'cco' 	  		=> $rows['Sedcco'],
					'descripcion' 	=> $rows['Seddes'],
					'tipoOrden'		=> $rows['Sedtor'],
					'codigo'   		=> $rows['Sedcod'],
				];
	}
	
	return $val;
}

function consultarSedePorCodigo( $conex, $wmovhos, $sede ){
	
	$val = [];
	
	//Consultando prioridades
	$sql = "SELECT *
			  FROM ".$wmovhos."_000264
			 WHERE Sedcod = '".$sede."'
			   AND Sedest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		$val = [
					'cco' 	  		=> trim( $rows['Sedcco'] ),
					'descripcion' 	=> trim( $rows['Seddes'] ),
					'tipoOrden'		=> $rows['Sedtor'],
					'codigo'   		=> $rows['Sedcod'],
				];
	}
	
	return $val;
}


function consultarSede( $conex, $wmovhos, $sede ){
	
	$val = [];
	
	//Consultando prioridades
	$sql = "SELECT *
			  FROM ".$wmovhos."_000264
			 WHERE Sedcco = '".$sede."'
			   AND Sedest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		$val = [
					'cco' 	  		=> $rows['Sedcco'],
					'descripcion' 	=> $rows['Seddes'],
					'tipoOrden'		=> $rows['Sedtor'],
					'codigo'   		=> $rows['Sedcod'],
				];
	}
	
	return $val;
}


function consultarSedePorCcoYCup( $conex, $wmovhos, $sede, $cup ){
	
	$val = [];
	
	//Consultando prioridades
	$sql = "SELECT a.Sedcco, a.Seddes, a.Sedtor, a.Sedcod
			  FROM ".$wmovhos."_000264 a, ".$wmovhos."_000263 b, ".$wmovhos."_000271 c
			 WHERE a.Sedcco = '".$sede."'
			   AND a.Sedest = 'on'
			   AND b.Salsed = a.Sedcod
			   AND b.Salest = 'on'
			   AND c.Procup = '".$cup."'
			   AND c.Proest = 'on'
			   AND b.Salmod = c.Promod
		  GROUP BY 1, 2, 3, 4
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		$val = [
					'cco' 	  		=> $rows['Sedcco'],
					'descripcion' 	=> $rows['Seddes'],
					'tipoOrden'		=> $rows['Sedtor'],
					'codigo'   		=> $rows['Sedcod'],
				];
	}
	
	return $val;
}


function consultarCco( $conex, $wmovhos, $cco ){
	
	$val = [];
		
	//Consultando modalidades y salas
	$sql = "SELECT * 
			  FROM ".$wmovhos."_000011
			 WHERE Ccocod = '".$cco."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = [ 
					'codigo' 		=> $rows['Ccocod'],
					'cco' 			=> $rows['Ccocod'],
					'descripcion' 	=> $rows['Cconom'],
				];
	}
	
	return $val;
}

function consultarProcedencia( $conex, $wmovhos, $procedencia ){
	
	$val = [];
		
	//Consultando modalidades y salas
	$sql = "SELECT * 
			  FROM ".$wmovhos."_000266
			 WHERE Procco = '".$procedencia."'
			   AND Proest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = [ 
					'codigo' 		=> $rows['Procod'],
					'cco' 			=> $rows['Procco'],
					'descripcion' 	=> $rows['Prodes'],
				];
	}
	
	return $val;
}

function consultarSala( $conex, $wmovhos, $sala ){
	
	$val = [];
		
	//Consultando modalidades y salas
	$sql = "SELECT * 
			  FROM ".$wmovhos."_000263
			 WHERE Salcod = '".$sala."'
			   AND Salest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = [ 
					'codigo' 		=> $rows['Salcod'],
					'descripcion'	=> $rows['Saldes'],
					'modalidad' 	=> $rows['Salmod'],
				];
	}
	
	return $val;
}



/**
 * Esta funcion cambia en nombre de los campos del registro por algo más legible
 */
function traducirCamposPorRegistro( $row ){
	
	$datos = [];
	
	$datos['idMatrix'] 		= $row['id'];
	$datos['idHiruko'] 		= $row['Mvcidh'];
	$datos['idAgenda'] 		= $row['Mvcida'];
	$datos['tipoDocumento'] = $row['Mvctdo'];
	$datos['documento'] 	= $row['Mvcdoc'];
	$datos['historia'] 		= $row['Mvchis'];
	$datos['ingreso'] 		= $row['Mvcing'];
	$datos['cco'] 			= $row['Mvccco'];
	$datos['fechaCita'] 	= $row['Mvcfec'];
	$datos['horaCita'] 		= $row['Mvchor'];
	$datos['modalidad'] 	= $row['Mvcmod'];
	$datos['sala']	 		= $row['Mvcsal'];
	$datos['prioridad'] 	= $row['Mvcpri'];
	$datos['estado'] 		= $row['Mvcest'];
	$datos['recepcionado'] 	= $row['Mvcrec'];
	$datos['conCita'] 		= $row['Mvccci'];
	$datos['cup'] 			= $row['Mvccup'];
	$datos['tipoOrden'] 	= $row['Mvctor'];
	$datos['nroOrden'] 		= $row['Mvcnro'];
	$datos['item'] 			= $row['Mvcite'];
	$datos['sede'] 			= $row['Mvcsed'];
	$datos['estadoAgenda'] 	= $row['Mvcesc'];
	
	return $datos;
}

function consultarIdPorOrden( $conex, $wmovhos, $tipoOrden, $nroOrden, $item ){
	
	$val = false;
		
	//Consultando modalidades y salas
	$sql = "SELECT id 
			  FROM ".$wmovhos."_000268
			 WHERE mvctor = '".$tipoOrden."'
			   AND mvcnro = '".$nroOrden."'
			   AND mvcite = '".$item."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows['id'];
	}
	
	return $val;
}

function consultarDatosPorId( $conex, $wmovhos, $id ){
	
	$val = false;
	
	if( $id > 0 ){
		
		//Consultando modalidades y salas
		$sql = "SELECT * 
				  FROM ".$wmovhos."_000268
				 WHERE id = $id
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array($res) ){
			$val = $rows;
		}
	}
	
	return $val;
}

function consultarItemPacienteARecepcionar( $conex, $wemp_pmla, $wmvohos, $idCita, $cup ){
	
	$val = false;
	
	//Consultando modalidades y salas
	$sql = "SELECT * 
			  FROM ".$wmvohos."_000269
			 WHERE id = '".$idCita."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows;
	}
	
	$val = false;
}

function consultarPacienteARecepcionar( $conex, $wemp_pmla, $wmvohos, $historia, $ingreso, $tipoDocumento, $nroDocumento, $fecha = '' ){
	
	$val = false;
	
	if( empty($fecha ) )
		$fecha = date( "Y-m-d" );
	
	//Consultando modalidades y salas
	$sql = "SELECT * 
			  FROM ".$wmvohos."_000268
			 WHERE mvchis  = '".$historia."'
			   AND mvcing  = '".$ingreso."'
			   AND mvcest  = 'on'
			   AND ( mvcfec  = '".$fecha."' OR fecha_data = '".$fecha."' )
			 UNION
			SELECT * 
			  FROM ".$wmvohos."_000268
			 WHERE mvctdo  = '".$tipoDocumento."'
			   AND mvcdoc  = '".$nroDocumento."'
			   AND mvcest  = 'on'
			   AND ( mvcfec  = '".$fecha."' OR fecha_data = '".$fecha."' )
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		$val = $rows;
		
		//Consultando modalidades y salas
		$sql = "SELECT * 
				  FROM ".$wmvohos."_000268
				 WHERE id = '".$rows['id']."'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	}
	
	return $val;
}

function consultarPacienteConCita( $conex, $wemp_pmla, $wmovhos, $historia, $ingreso, $tipoDocumento, $nroDocumento, $cup, $fecha = '' ){
	
	$val = false;
	
	if( empty($fecha ) )
		$fecha = date( "Y-m-d" );
	
	//Consultando modalidades y salas
	// AND mvcing  = '".$ingreso."'
	$sql = "SELECT * 
			  FROM ".$wmovhos."_000268 d
			 WHERE mvchis  = '".$historia."'
			   AND mvcest  = 'on'
			   AND mvcfec  = '".$fecha."'
			   AND mvcesc != '0'
			   AND mvccup  = '".$cup."'
			   AND mvcrec != 'on'
			   AND mvcsed IN( SELECT a.Sedcco
							    FROM ".$wmovhos."_000264 a, ".$wmovhos."_000263 b, ".$wmovhos."_000271 c
							   WHERE a.Sedest = 'on'
							     AND b.Salsed = a.Sedcod
							     AND b.Salest = 'on'
							     AND c.Procup = '".$cup."'
							     AND c.Proest = 'on'
							     AND b.Salmod = c.Promod 
								 AND d.mvcmod = c.Promod )
			 UNION
			SELECT * 
			  FROM ".$wmovhos."_000268 d
			 WHERE mvctdo  = '".$tipoDocumento."'
			   AND mvcdoc  = '".$nroDocumento."'
			   AND mvcest  = 'on'
			   AND mvcfec  = '".$fecha."'
			   AND mvcesc != '0'
			   AND mvccup  = '".$cup."'
			   AND mvcrec != 'on'
			   AND mvcsed  IN( SELECT a.Sedcco
							     FROM ".$wmovhos."_000264 a, ".$wmovhos."_000263 b, ".$wmovhos."_000271 c
							    WHERE a.Sedest = 'on'
							      AND b.Salsed = a.Sedcod
							      AND b.Salest = 'on'
							      AND c.Procup = '".$cup."'
							      AND c.Proest = 'on'
							      AND b.Salmod = c.Promod 
								 AND d.mvcmod = c.Promod  )
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		$val = $rows;
	}
	
	return $val;
}

function actualizarMovimiento( $conex, $wmvohos, $id, $datos = [] ){
	
	$val = false;
	$camposActualizados = false;
	$set = [];
	
	//Solo se actualizan los campos que no esten vacios
	if( !empty( $datos['idHiruko'] ) )
		$set[] = "mvcidh = '".$datos['idHiruko']."'";
	
	if( !empty( $datos['idAgenda'] ) )
		$set[] = "mvcida = '".$datos['idAgenda']."'";
	
	if( !empty( $datos['tipoDocumento'] ) )
		$set[] = "mvctdo = '".$datos['tipoDocumento']."'";
	
	if( !empty( $datos['documento'] ) )
		$set[] = "mvcdoc = '".$datos['documento']."'";
	
	if( !empty( $datos['historia'] ) )
		$set[] = "mvchis = '".$datos['historia']."'";
	
	if( !empty( $datos['ingreso'] ) )
		$set[] = "mvcing = '".$datos['ingreso']."'";
	
	if( !empty( $datos['cco'] ) )
		$set[] = "mvccco = '".$datos['cco']."'";
	
	if( !empty( $datos['fechaCita'] ) )
		$set[] = "mvcfec = '".$datos['fechaCita']."'";
	
	if( !empty( $datos['horaCita'] ) )
		$set[] = "mvchor = '".$datos['horaCita']."'";
	
	if( !empty( $datos['modalidad'] ) )
		$set[] = "mvcmod = '".$datos['modalidad']."'";
	
	if( !empty( $datos['sala'] ) )
		$set[] = "mvcsal = '".$datos['sala']."'";
	
	if( !empty( $datos['prioridad'] ) )
		$set[] = "mvcpri = '".$datos['prioridad']."'";
	
	if( !empty( $datos['estado'] ) )
		$set[] = "mvcpri = '".$datos['estado']."'";
	
	if( !empty( $datos['recepcionado'] ) )
		$set[] = "mvcrec = '".$datos['recepcionado']."'";
	
	if( !empty( $datos['conCita'] ) )
		$set[] = "mvccci = '".$datos['conCita']."'";
	
	if( !empty( $datos['cup'] ) )
		$set[] = "mvccup = '".$datos['cup']."'";
	
	if( !empty( $datos['sede'] ) )
		$set[] = "mvcsed = '".$datos['sede']."'";
	
	if( !empty( $datos['tipoOrden'] ) )
		$set[] = "mvctor = '".$datos['tipoOrden']."'";
	
	if( !empty( $datos['nroOrden'] ) )
		$set[] = "mvcnro = '".$datos['nroOrden']."'";
	
	if( !empty( $datos['item'] ) )
		$set[] = "mvcite = '".$datos['item']."'";
	
	if( !empty( $datos['estadoResultado'] ) )
		$set[] = "mvcere = '".$datos['estadoResultado']."'";
	
	if( !empty( $datos['url'] ) )
		$set[] = "mvcurl = '".$datos['url']."'";
	
	if( !empty( $datos['urlReporte'] ) )
		$set[] = "mvcurp = '".$datos['urlReporte']."'";
	
	if( !empty( $datos['estadoCita'] ) || $datos['estadoCita'] === '0' )
		$set[] = "mvcesc = '".$datos['estadoCita']."'";
	
	if( !empty( $datos['indicaciones'] ) )
		$set[] = "mvcind = '".$datos['indicaciones']."'";
	
	if( !empty( $datos['medicoRemitente'] ) )
		$set[] = "mvcmre = '".$datos['medicoRemitente']."'";
	
	//Se hace forma el set( campo = 'valor1', campo2 = 'valor2', ... )
	//Para ello se hace un implode
	if( count($set ) > 0 ){
		$camposActualizados = implode( ",", $set );
	}
	
	if( $id && $camposActualizados ){
	
		//Consultando modalidades y salas
		$sql = "UPDATE ".$wmvohos."_000268
				   SET ".$camposActualizados."
				 WHERE id = $id
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			$val = true;
		}
		
		$historia	= ( !empty( $datos['historia'] ) ) ? $datos['historia'] : '';
		$ingreso	= ( !empty( $datos['ingreso'] ) ) ? $datos['ingreso'] : '';
		$tor		= ( !empty( $datos['tipoOrden'] ) ) ? $datos['tipoOrden'] : '';
		$nro		= ( !empty( $datos['nroOrden'] ) ) ? $datos['nroOrden'] : '';
		$item 		= ( !empty( $datos['item'] ) ) ? $datos['item'] : '';
		
		registrarDetalleLog( $conex, $wmvohos, $historia, $ingreso, $tor, $nro, $item, 'Movimiento cita actualizado', $id.":".json_encode($set) );
	}
	
	return $val;
}

function agregarMovimiento( $conex, $wemp_pmla, $wmvohos, $datos = [] ){
	
	$val = false;
	
	$medico 	= $wmvohos;
	$fecha_data = date( "Y-m-d" );
	$hora_data 	= date( "H:i:s" );
	$mvcidm 	= empty( $datos[''] ) ? '' : $set[] = $datos[''];
	$mvcidh 	= empty( $datos['idHiruko'] ) ? '' : $set[] = $datos['idHiruko'];
	$mvcida 	= empty( $datos['idAgenda'] ) ? '' : $set[] = $datos['idAgenda'];
	$mvctdo 	= empty( $datos['tipoDocumento'] ) ? '' : $set[] = $datos['tipoDocumento'];
	$mvcdoc 	= empty( $datos['documento'] ) ? '' : $set[] = $datos['documento'];
	$mvchis 	= empty( $datos['historia'] ) ? '' : $set[] = $datos['historia'];
	$mvcing 	= empty( $datos['ingreso'] ) ? '' : $set[] = $datos['ingreso'];
	$mvccco 	= empty( $datos['cco'] ) ? '' : $set[] = $datos['cco'];
	$mvcfec 	= empty( $datos['fechaCita'] ) ? '' : $set[] = $datos['fechaCita'];
	$mvchor 	= empty( $datos['horaCita'] ) ? '' : $set[] = $datos['horaCita'];
	$mvcesc 	= empty( $datos['estadoCita'] ) ? '' : $set[] = $datos['estadoCita'];
	$mvcmod 	= empty( $datos['modalidad'] ) ? '' : $set[] = $datos['modalidad'];
	$mvcsal 	= empty( $datos['sala'] ) ? '' : $set[] = $datos['sala'];
	$mvcpri 	= empty( $datos['prioridad'] ) ? '' : $set[] = $datos['prioridad'];
	$mvcest 	= 'on';
	$mvcrec 	= empty( $datos['recepcionado'] ) ? 'off' : $datos['recepcionado'];
	$mvccci 	= empty( $datos['conCita'] ) ? 'off' : $datos['conCita'];
	$mvccup 	= empty( $datos['cup'] ) ? '' : $set[] = $datos['cup'];
	$mvcsed 	= empty( $datos['sede'] ) ? '' : $set[] = $datos['sede'];
	$mvctor 	= empty( $datos['tipoOrden'] ) ? '' : $set[] = $datos['tipoOrden'];
	$mvcnro 	= empty( $datos['nroOrden'] ) ? '' : $set[] = $datos['nroOrden'];
	$mvcite 	= empty( $datos['item'] ) ? '' : $datos['item'];
	$mvcere 	= empty( $datos['estadoResultado'] ) ? '' : $datos['estadoResultado'];
	$mvcurl 	= empty( $datos['url'] ) ? '' : $datos['url'];
	$mvcurp 	= empty( $datos['urlReporte'] ) ? '' : $datos['urlReporte'];
	$mvcind 	= empty( $datos['indicaciones'] ) ? '' : $datos['indicaciones'];
	$mvcmre 	= empty( $datos['medicoRemitente'] ) ? '' : $datos['medicoRemitente'];
	$seguridad	= 'C-'.$wmvohos;
	
	//Consultando modalidades y salas
	$sql = "INSERT INTO 
				".$wmvohos."_000268( Medico, Fecha_data, Hora_data, Mvcidm, Mvcidh, Mvcida, Mvctdo, Mvcdoc, Mvchis, Mvcing, Mvccco, Mvcfec, Mvchor, Mvcesc, Mvcmod, Mvcsal, Mvcpri, Mvcest, Mvcrec, Mvccci, Mvccup, Mvcsed, Mvctor, Mvcnro, Mvcite, Mvcere, Mvcurl, Mvcurp, Mvcind, Mvcmre, Seguridad )
							 VALUES( '".$medico."','".$fecha_data."','".$hora_data."','".$mvcidm."','".$mvcidh."','".$mvcida."','".$mvctdo."','".$mvcdoc."','".$mvchis."','".$mvcing."','".$mvccco."','".$mvcfec."','".$mvchor."','".$mvcesc."','".$mvcmod."','".$mvcsal."','".$mvcpri."','".$mvcest."','".$mvcrec."','".$mvccci."','".$mvccup."','".$mvcsed."','".$mvctor."','".$mvcnro."','".$mvcite."','".$mvcere."','".$mvcurl."','".$mvcurp."','".$mvcind."','".$mvcmre."','".$seguridad."' )
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = mysql_insert_id();
		
		$historia	= ( !empty( $datos['historia'] ) ) ? $datos['historia'] : '';
		$ingreso	= ( !empty( $datos['ingreso'] ) ) ? $datos['ingreso'] : '';
		$tor		= ( !empty( $datos['tipoOrden'] ) ) ? $datos['tipoOrden'] : '';
		$nro		= ( !empty( $datos['nroOrden'] ) ) ? $datos['nroOrden'] : '';
		$item 		= ( !empty( $datos['item'] ) ) ? $datos['item'] : '';
		
		registrarDetalleLog( $conex, $wmvohos, $historia, $ingreso, $tor, $nro, $item, 'Movimiento cita agregado', json_encode($datos) );
	}
	
	return $val;
}

function consultarMaestros( $conex, $wemp_pmla, $sede ){
	
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	
	$val = [];
	
	$prioridades = [];
	$modalidades = [];
	$salas 		 = [];
	
	//Consultando prioridades
	$sql = "SELECT *
			  FROM ".$wmovhos."_000265
			 WHERE Priest = 'on'
		  ORDER BY Prides
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $rows = mysql_fetch_array($res) ){
		
		$prioridades[] = [
					'codigo' 	  => $rows['Pricod'],
					'descripcion' => $rows['Prides'],
				];
	}
	
	//Consultando modalidades y salas
	$sql = "SELECT Modcod, Moddes, Salcod, Saldes
			  FROM ".$wmovhos."_000262 a, ".$wmovhos."_000263 b
			 WHERE b.Salsed = '".$sede."'
			   AND b.Salmod = a.Modcod
			   AND a.Modest = 'on'
			   AND b.Salest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $rows = mysql_fetch_array($res) ){
		
		
		
		if( !isset( $salas[ $rows['Modcod'] ] ) ){
			$salas[ $rows['Modcod'] ] = [];
			
			$modalidades[] = [
				'codigo' 	  => $rows['Modcod'],
				'descripcion' => utf8_encode( $rows['Moddes'] ),
			];
		}
				
		$salas[ $rows['Modcod'] ][] = [
					'codigo' 	  => $rows['Salcod'],
					'descripcion' => utf8_encode( $rows['Saldes'] ),
				];
	}
	
	$val = [
			'prioridades' 	=> $prioridades,
			'modalidades' 	=> $modalidades,
			'salas' 		=> $salas,
		];
	
	return $val;
}

function crearMensajesHL7ORMAgenda( $conex, $wemp_pmla, $paciente, $datosCita ){
	
	$whce 	 = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
	$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	
	
	$idMatrix 	= empty( $datosCita['idMatrix'] ) ? '': $datosCita['idMatrix'];
	$idHiruko 	= empty( $datosCita['idHiruko'] ) ? '': $datosCita['idHiruko'];
	$idAgenda 	= empty( $datosCita['idAgenda'] ) ? '': $datosCita['idAgenda'];
	$fechaCita 	= empty( $datosCita['fechaCita'] ) ? '': str_replace( "-", "", $datosCita['fechaCita'] );
	$horaCita 	= empty( $datosCita['horaCita'] ) ? '':str_replace( ":", "", $datosCita['horaCita'] );
	
	$medicoNombres			= utf8_decode( trim( $datosCita['medico']->nombre1." ". $datosCita['medico']->nombre2 ) );
	$medicoApellidos		= utf8_decode( trim( $datosCita['medico']->apellido1." ". $datosCita['medico']->apellido2 ) );
	$medicoNroDocumento		= $datosCita['medico']->numeroDocumento;
	$medicoTipoDocumento	= $datosCita['medico']->tipoDocumento;
	
	$indicacion				= !empty( $datosCita['indicacion'] ) ? $datosCita['indicacion'] : '';
	
	$procedimientosCargados = $datosCita['cups'];
	
	$procedencia = consultarCco( $conex, $wmovhos, $paciente['servicioActual'] );
	$sede 		 = $datosCita['sede'];
	
	$estado = $datosCita['estadoAgenda']; //AG
	
	$mensaje = "";
		
	$idMatrix = $datosCita['tipoOrden']."-".$datosCita['nroOrden'];
	
	$mensaje =	 "MSH|^~\\&|PMLA|MATRIX|IMEXHS|HIRUKO|".date("Y-m-d H:i:s")."||ORM^O01|".$idMatrix."^".$idHiruko."^".$idAgenda."|P|2.5||||AL"
				."\nPID||".$paciente['tipoDocumento']."^".$paciente['nroDocumento']."|||".$paciente['nombre1']."^".$paciente['nombre2']."^".$paciente['apellido1']."^".$paciente['apellido2']."||".$paciente['fechaNacimiento']."|".$paciente['genero']."|||".$paciente['direccion']."^^".$paciente['codigomMunicipio']."^".$paciente['codigomDepartamento']."^^CO||".$paciente['celular']."^^^".$paciente['correoElectronico']."^^^".$paciente['telefono']."|"
				."\nAIS|".$idMatrix."^".$idHiruko."^".$idAgenda."|||".$fechaCita.$horaCita."||||||".$estado."|"
				."\nPV1||||||".$procedencia['descripcion']."^".$procedencia['codigo']."||".$medicoNombres."^".$medicoApellidos."^".$medicoNroDocumento."^".$medicoTipoDocumento."||".$sede['descripcion']."^".$sede['codigo']."||"
				."\nIN1|||".$paciente['codigoResponsable']."|".$paciente['nombreResponsable']."|||||";
				
	if( count($procedimientosCargados) > 0 ){
		
		foreach( $procedimientosCargados as $proc ){
			
			$infoCup = consultarProcedimiento( $conex, $wcliame, $proc['cup'] );
			
			$modalidad		= empty( $proc['modalidad'] ) ? '': $proc['modalidad'];
			$prioridad		= empty( $proc['prioridad'] ) ? '': $proc['prioridad'];
			$estudio		= empty( $proc['item'] ) ? '': $proc['item'];
			$orden			= empty( $proc['orden'] ) ? '': $proc['orden'];
			$sala			= empty( $proc['sala'] ) ? '': $proc['sala']; 
			$justificacion	= empty( $proc['justificacion'] ) ? '': str_replace( "\n", " ", str_replace( "\r", " ", $proc['justificacion'] ) ); 
			
			$cup 			= $infoCup[0]['codigo'];
			$descripcionCUP = $infoCup[0]['descripcion'].( empty( $justificacion ) ? '' : "(".$justificacion.")" );
			
			$urlReporte		= empty( $proc['urlReporte'] ) ? '': $proc['urlReporte']; 
			
			$mensaje .= "\nOBR|||".$estudio."|".$cup."^".$modalidad."^".$sala."^".$descripcionCUP."|".$prioridad."||||||".$indicacion."||".$justificacion;
			
			if( !empty( $urlReporte ) ){
				$mensaje .= "\nOBX|||||".$urlReporte."||||||||";
			}
		}
	}

	return $mensaje;
}


function crearMensajesHL7ORM( $conex, $wemp_pmla, $paciente, $datosCita ){
	
	$whce 	 = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
	$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );	
	
	$idMatrix 	= empty( $datosCita['idMatrix'] ) ? '': $datosCita['idMatrix'];
	$idHiruko 	= empty( $datosCita['idHiruko'] ) ? '': $datosCita['idHiruko'];
	$idAgenda 	= empty( $datosCita['idAgenda'] ) ? '': $datosCita['idAgenda'];
	$fechaCita 	= empty( $datosCita['fechaCita'] ) ? '': str_replace( "-", "", $datosCita['fechaCita'] );
	$horaCita 	= empty( $datosCita['horaCita'] ) ? '':str_replace( ":", "", $datosCita['horaCita'] );
	$sala		= empty( $datosCita['sala'] ) ? '': $datosCita['sala']; 
	$modalidad	= empty( $datosCita['modalidad'] ) ? '': $datosCita['modalidad'];
	$prioridad	= empty( $datosCita['prioridad'] ) ? '':  $datosCita['prioridad'];
	$orden		= empty( $datosCita['tipoOrden'] ) ? '':  $datosCita['tipoOrden'];
	$usuarioGC	= empty( $datosCita['usuarioGraboCargo'] ) ? '' : $datosCita['usuarioGraboCargo'];
	
	$procedimientosCargados = consultarProcedimiento( $conex, $wcliame, $datosCita['cup'] );
	
	$indicacion	= !empty( $datosCita['indicacion'] ) ? $datosCita['indicacion'] : '';
	
	
	$medicoNombres			= utf8_decode( trim( $datosCita['medico']->nombre1." ". $datosCita['medico']->nombre2 ) );
	$medicoApellidos		= utf8_decode( trim( $datosCita['medico']->apellido1." ". $datosCita['medico']->apellido2 ) );
	$medicoNroDocumento		= $datosCita['medico']->numeroDocumento;
	$medicoTipoDocumento	= $datosCita['medico']->tipoDocumento;
	
	
	if( !empty( $orden ) && !empty( $datosCita['nroOrden'] ) && !empty( $datosCita['item'] ) )
		$orden		= $orden."-".$datosCita['nroOrden']."-".$datosCita['item'];
	
	// $procedencia = consultarProcedencia( $conex, $wmovhos, $datosCita['cco'] );
	// $procedencia = $datosCita['cco'];
	$procedencia = consultarCco( $conex, $wmovhos, $paciente['servicioActual'] );
	$sede 		 = $datosCita['sede'];
	
	$mensaje = "";
	
	if( !empty( $sala ) && !empty( $modalidad ) && !empty( $prioridad ) ){
		
		if( !empty( $datosCita['conCita'] ) && $datosCita['conCita'] == 'on' ){
			$idMatrix = "CC-".$idMatrix;
			$estado = "AG";
		}
		else{
			$idMatrix = "SC-".$idMatrix;
			$estado = "PR";
		}
		
		$mensaje =	 "MSH|^~\\&|PMLA|MATRIX|IMEXHS|HIRUKO|".date("Y-m-d H:i:s")."||ORM^O01|".$idMatrix."^".$idHiruko."^".$idAgenda."|P|2.5||||AL"
					."\nPID||".$paciente['tipoDocumento']."^".$paciente['nroDocumento']."|||".$paciente['nombre1']."^".$paciente['nombre2']."^".$paciente['apellido1']."^".$paciente['apellido2']."||".$paciente['fechaNacimiento']."|".$paciente['genero']."|||".$paciente['direccion']."^^".$paciente['codigomMunicipio']."^".$paciente['codigomDepartamento']."^^CO||".$paciente['celular']."^^^".$paciente['correoElectronico']."^^^".$paciente['telefono']."|"
					."\nAIS|".$idMatrix."^".$idHiruko."^".$idAgenda."|||".$fechaCita.$horaCita."||||||".$estado."|"
					."\nPV1||||||".$procedencia['descripcion']."^".$procedencia['codigo']."||".$medicoNombres."^".$medicoApellidos."^".$medicoNroDocumento."^".$medicoTipoDocumento."||".$sede['descripcion']."^".$sede['codigo']."||"
					."\nIN1|||".$paciente['codigoResponsable']."|".$paciente['nombreResponsable']."|||".$usuarioGC."||";
					// ."\nOBR||||".$cup."^".$modalidad."^".$sala."^".$descripcionCUP."|".$prioridad."|";
					
		if( count($procedimientosCargados) > 0 ){
			
			foreach( $procedimientosCargados as $proc ){
				
				$cup 			= $proc['codigo'];
				$descripcionCUP = $proc['descripcion'];
				$justificacion	= empty( $proc['justificacion'] ) ? '':  str_replace( "\n", " ", str_replace( "\r", " ", $proc['justificacion'] ) ); 
				
				$mensaje .= "\nOBR|||".$idMatrix."|".$cup."^".$modalidad."^".$sala."^".$descripcionCUP."|".$prioridad."||||||".$indicacion."||".$justificacion;
				
				break;
			}
		}
	}
	
	//Conectando vía socket
	$direccion = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ipHL7HirukoPacienteAmbulatorio' );
	// $direccion = "181.143.71.154";
	// $puerto = 6662;

	$socket = stream_socket_client("tcp://$direccion", $errno, $errstr);
	// $socket = fsockopen( $direccion, $puerto, $errno, $errstr, 5 );
	// $socket = stream_socket_client("tcp://$direccion:$puerto", $errno, $errstr);

	$val = true;

	if( $socket ){
		
		$val = fwrite( $socket, utf8_encode($mensaje ) );
		
		fclose($socket);
	}
	else{
		$val = false;
	}
	
	if( !$val ){
		$historia 	= $_POST['historia'];
		$ingreso 	= $_POST['ingreso'];
		
		registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $sede['tipoOrden'], 0, 0, 'Mensaje no enviado - sin conexion socket', $mensaje );
		
		$wasunto 			= utf8_decode( "Recepción no realizada" );
		$msg 				= "No se logra realizar conexi&oacute;n con el sistema HIRUKO para enviar el mensaje perteneciente al paciente:<br><br>";
		$msg 			   .= $paciente['nombre1']." ".$paciente['nombre2']." ".$paciente['apellido1']." ".$paciente['apellido2']."<br>";
		$msg 			   .= $paciente['tipoDocumento']." ".$paciente['nroDocumento']."<br>";
		$msg 			   .= "Fecha y hora: ".date("H:i:s")."<br>Mensaje HL7:<br><br>";
		$msg 			   .= str_replace( "\n", "<br>", $mensaje );
		$altbody 			= "";
		$email        		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailpmla");
		$email        		= explode("--", $email );
		$wremitente			= array( 'email'	=> $email[0],
									 'password' => $email[1],
									 'from' 	=> $email[0],
									 'fromName' => 'Clinica las americas',
							 );
		// $wdestinatario[]	= $email[0];
		$wdestinatario	= consultarEmails( $conex, $whce, $sede['tipoOrden'] );
		
		//Si no hay destinatario no se envía correo
		if( !empty($wdestinatario) )
			$rp = sendToEmail($wasunto,$msg,$altbody,$wremitente,$wdestinatario);
		
		die( "No hubo conexión con HIRUKO" );
	}

	return $mensaje;
}

function ccoCodNuclear($conex, $wmovhos)
{
	$val = null;
	
	//Consultando si es medicina nuclear
	$sql = "SELECT Sedcco
			  FROM ".$wmovhos."_000264
			 WHERE Sedtor = 'A11'
			   AND Sedest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) )
	{
		$val = $rows['Sedcco'];
	}

	return $val;
}

function esMedicinaNuclear($conex, $wmovhos, $wcliame, $cco_sede, $historia, $ingreso, $cup)
{
	$val = false;

	//Consultando si es medicina nuclear
	$sql = "SELECT Sedtor
			  FROM ".$wmovhos."_000264
			 WHERE Sedcco = '".$cco_sede."'
			   AND Sedest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array($res) ){

		if ($rows['Sedtor'] == 'A11')
		{
			$sql = "SELECT *
			  		  FROM ".$wcliame."_000106
					 WHERE Fecha_data = '".date("Y-m-d")."'
					   AND Tcarhis = '".$historia."'
					   AND Tcaring = '".$ingreso."'
					   AND Tcarser = '".$cco_sede."'
					   AND Tcarprocod = '".$cup."'
					   AND Tcarest = 'on'
			";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );

			if( $num > 1 )
			{
				$val = true;
			}
		}
	}

	return $val;
}

/**
 * Metodo que permite obtener toda la información del usuario que realizó
 * el proceso de grabación del cargo para enviar sus datos por la interoperabilidad
 * 
 * @author Joel David Payares Hernández
 * @since 2022-02-24
 */
function informacionUsuarioGrabaCargos($conex, $wemp_pmla, $usuarioGC)
{
	$response = null;

	$sql = "
		  SELECT	Documento 
			FROM	usuarios 
		   WHERE	Codigo = '{$usuarioGC}'
			 AND	Activo = 'A';";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query ".mysql_error() );

	while($row = mysql_fetch_array($res) )
	{
		$response = $row['Documento'];
	}

	return $response;
}

//Proceso de acuerdo al protocolo HL7
if( $_POST ){
	
	if( $HL7 ){
		
		list( $tipoMsg ) = explode( "^", $HL7['MSH'][0][8] );
		
		switch( $tipoMsg ){
			
			case 'ORM':
				procesarMsgORM( $conex, $wemp_pmla, $HL7, $message );
				$respuesta['message'] 	= 'Mensage procesado';
				endRoutine( $respuesta, 200 );
				break;
				
			case 'ORU': 
				procesarMsgORM( $conex, $wemp_pmla, $HL7, $message );
				// procesarMsgORU( $conex, $wemp_pmla, $HL7 );
				$respuesta['message'] 	= 'Mensage procesado';
				endRoutine( $respuesta, 200 );
				break;
			
			default:
				$respuesta['message'] 	= 'Petición desconocida';
				endRoutine( $respuesta, 400 );
				break;
		}
	}
	else{
		$accion = $_POST['accion'];
		
		if( $accion ){
			
			switch( $accion ){
				
				case 'enviarReporte':
					envarReporteMatrix( $conex );
				break;
				
				case 'crearMensaje': 
				
					$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
					$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
					$whce 	 = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
					
					$historia 	= $_POST['historia'];
					$ingreso 	= $_POST['ingreso'];
					$sala 		= $_POST['sala'];
					$modalidad 	= $_POST['modalidad'];
					$prioridad 	= $_POST['prioridad'];
					$idCita 	= $_POST['idCita'];
					$cco_sede 	= $_POST['cco_sede'];
					$indicacion	= $_POST['indicacion'] ? $_POST['indicacion'] : '';
					$usuarioGC	= $_POST['ndoUsuarioGC'] ? $_POST['ndoUsuarioGC'] : '';
					
					$paciente = informacionPaciente( $conex, $wemp_pmla, $historia, $ingreso );
					
					$procedimientosCargados = consultarProcedimientosCargados( $conex, $wcliame, $historia, $ingreso, $cco_sede );
					$cup = $procedimientosCargados[0]['codigo'];
					
					// $sede = consultarSede( $conex, $wmovhos, $cco_sede );
					$sede = consultarSedePorCcoYCup( $conex, $wmovhos, $cco_sede, $cup );
					
					$datosCita = [
							'tipoDocumento' 	=> $paciente['tipoDocumento'],
							'documento' 		=> $paciente['nroDocumento'],
							'historia' 			=> $historia,
							'ingreso' 			=> $ingreso,
							'cco' 				=> $paciente['servicioActual'],
							'modalidad' 		=> $modalidad,
							'sala' 				=> $sala,
							'prioridad' 		=> $prioridad ,
							'tipoOrden' 		=> $sede['tipoOrden'],
							'sede' 				=> $sede['cco'],
							'recepcionado'  	=> 'on',
							'cup' 				=> $cup,
							'estadoResultado'	=> 'PR',
							'indicaciones'		=> $indicacion,
							'medicoRemitente'	=> $_POST['medico']['codigo'] ? $_POST['medico']['codigo'] : '',
						];
					
					//Si no hay cita es que es ambulatorio y se debe crear registros nuevo
					//caso contrario tiene cita y se usa esos datos
					$esHospitalario = false;
					if( empty($idCita) ){
						$id 		= agregarMovimiento( $conex, $wemp_pmla, $wmovhos, $datosCita );
						$rows 		= consultarDatosPorId( $conex, $wmovhos, $id );
						$datosCita 	= traducirCamposPorRegistro( $rows );
					}
					else{
						
						$id 				= $idCita;
						
						actualizarMovimiento( $conex, $wmovhos, $id, [
													'tipoDocumento' 	=> $paciente['tipoDocumento'],
													'documento' 		=> $paciente['nroDocumento'],
													'historia' 			=> $historia,
													'ingreso' 			=> $ingreso,
													'modalidad' 		=> $modalidad,
													'sala' 				=> $sala,
													'prioridad' 		=> $prioridad ,
													'recepcionado'  	=> 'on',
													'estadoResultado'	=> 'PR',
													'indicaciones'		=> $indicacion,
													'medicoRemitente'	=> $_POST['medico']['codigo'] ? $_POST['medico']['codigo'] : '',
												] );
												
						$rows 				= consultarDatosPorId( $conex, $wmovhos, $id );
						$datosCita 			= traducirCamposPorRegistro( $rows );
						
						if( empty($rows['Mvctor']) || empty($rows['Mvcnro']) || empty($rows['Mvcite']) ){
							$datosCita['cup'] 	= $cup;
						}
						else{
							
							$esHospitalario = true;
							
							$datosCita['sede'] = $sede;
							
							$datosCita['cups'][] = [
											'cup' 			=> $cup,
											'item' 			=> $rows['Mvctor']."-".$rows['Mvcnro']."-".$rows['Mvcite'],
											'orden'			=> $rows['Mvctor']."-".$rows['Mvcnro'],
											'modalidad' 	=> $modalidad,
											'sala' 			=> $sala,
											'prioridad' 	=> $prioridad,
											'justificacion' => '',
										];
						}
						
						if( !empty( $rows['Mvctor'] ) && !empty( $rows['Mvcnro'] ) && empty( $rows['Mvcite'] ) )
							cambiarEstadoExamen( $conex, $wemp_pmla, $rows['Mvctor'],$rows['Mvcnro'], $rows['Mvcite'], 'PR', date("Y-m-d"), date("H:m:s"), '', $historia, $ingreso );
					}

					if( !$esHospitalario ){

						$datosCita['usuarioGraboCargo'] = $usuarioGC;

						// Si es ambulatario
						$datosCita['sede'] 			= $sede;
						$datosCita['recepcionado']  = 'on';
						$datosCita['indicacion']	= $indicacion;
						
						$medico = new medicoDTO();
						
						if( $_POST['medico'] ){
							$medico->tipoDocumento 	= $_POST['medico']['tipoDocumento'];
							$medico->numeroDocumento= $_POST['medico']['numeroDocumento'];
							$medico->nombre1		= $_POST['medico']['nombre1'];
							$medico->nombre2		= $_POST['medico']['nombre2'];
							$medico->apellido1		= $_POST['medico']['apellido1'];
							$medico->apellido2		= $_POST['medico']['apellido2'];
						}
						
						$datosCita['medico'] 		= $medico;
						
						echo $mensaje = crearMensajesHL7ORM( $conex, $wemp_pmla, $paciente, $datosCita );
						
						registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $sede['tipoOrden'], 0, 0, 'Mensaje enviado', $mensaje );
						
						registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'MATRIX-HIRUKO', $dc['tipoOrden'], $dc['nroOrden'], '', $mensaje );
					}
					else{
						$cod_medico = consultarCodigoMedicoPorEstudio( $conex, $whce, $rows['Mvctor'], $rows['Mvcnro'], $rows['Mvcite'] );
						
						$medico 	= informacionMedico( $conex, $wmovhos, $wemp_pmla, $cod_medico['medico'] );
						
						$datosCita['medico'] 					= $medico;
						$datosCita['indicacion']				= $indicacion;
						$datosCita['cups'][0]['justificacion']	= $cod_medico['justificacion'];
						
						$mensaje = crearMensajesHL7ORMAgenda( $conex, $wemp_pmla, $paciente, $datosCita );
						//Si es hospitalario
						
						//Conectando vía socket
						// $direccion = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ipHL7HirukoAgendarOrden' );
						$direccion = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ipHL7HirukoPacienteAmbulatorio' ); 

						$socket = stream_socket_client("tcp://$direccion", $errno, $errstr);

						if( $socket ){
							$val = fwrite($socket, utf8_encode($mensaje ) );
							
							fclose($socket);
						}
						
						registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'MATRIX-HIRUKO', $dc['tipoOrden'], $dc['nroOrden'], '', $mensaje );
						
						echo $mensaje;
					}
				break;
				
				
				case 'agendarOrden': 
				
					$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
					$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
					$whce 	 = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
					
					$historia 	= $_POST['historia'];
					$ingreso 	= $_POST['ingreso'];
					$tipoOrden	= $_POST['tipoOrden'];
					$nroOrden 	= $_POST['nroOrden'];
					
					$ordenes	= consultarEstudiosPorOrden( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden, $historia, $ingreso );
					
					$paciente 	= informacionPaciente( $conex, $wemp_pmla, $historia, $ingreso );
					
					//medico remitente
					$medico		= informacionMedico( $conex, $wmovhos, $wemp_pmla, $ordenes['medico'] );
					
					$sede 		= consultarSedePorTipoOrden( $conex, $wmovhos, $tipoOrden );
					
					if( empty( $ordenes ) ){
						registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, 0, 'Orden no enviada', 'Estudios no realizables por '.$sede['descripcion'] );
						// exit( 'Orden no enviada: Estudios no realizables por '.$sede['descripcion'] );
						exit();
					}
					
					$datosCita = [
								'tipoDocumento' => $paciente['tipoDocumento'],
								'documento' 	=> $paciente['nroDocumento'],
								'historia' 		=> $historia,
								'ingreso' 		=> $ingreso,
								'cco' 			=> $paciente['servicioActual'],
								'sede' 			=> $sede,
								'tipoOrden' 	=> $tipoOrden,
								'nroOrden' 		=> $nroOrden,
								'medico' 		=> $medico,		//Esto es un objeto con la información del médico
								'cups' 			=> [],
							];
					
					$datosPorEstado = [];
					
					foreach( $ordenes as $orden ){
						
						
						$modDefecto 	= consultarModalidadPorCup( $conex, $wmovhos, $orden['codigo'] );
						$modalidadesCup	= consultarModalidadesPorCup( $conex, $wmovhos, $orden['codigo'] );
					
						$datos = [
								'tipoDocumento' 	=> $paciente['tipoDocumento'],
								'documento' 		=> $paciente['nroDocumento'],
								'historia' 			=> $historia,
								'ingreso' 			=> $ingreso,
								'cco' 				=> $paciente['servicioActual'],
								'tipoOrden' 		=> $tipoOrden,
								'nroOrden' 			=> $nroOrden,
								'sede' 				=> $sede['cco'],
								'item' 				=> $orden['item'],
								'cup' 				=> $orden['procod'], //2022-01-28 - Sebastián Nevado: Se cambia código cups por código del procedimiento 
								'modalidad' 		=> $modDefecto,
								'medico' 			=> $medico,		//Esto es un objeto con la información del médico
								'medicoRemitente'	=> $orden['medico'],		//Esto es un objeto con la información del médico
							];
						
						$id 	= consultarIdPorOrden( $conex, $wmovhos, $tipoOrden, $nroOrden, $orden['item'] );
						
						if( $id == false )
							$id 	= agregarMovimiento( $conex, $wemp_pmla, $wmovhos, $datos );
						
						$rows 	= consultarDatosPorId( $conex, $wmovhos, $id );
						$datos 	= traducirCamposPorRegistro( $rows );
						
						$datosCita['idMatrix'] = $id;
						
						$datosCita['cups'] = [];
							
						// $datosCita['estadoAgenda'] = $orden['estado'] == 'C' ? $orden['estadoExterno'] : 'AG';
						
						//Este es el estado para pedir una la asignación de una cita
						$datosCita['estadoAgenda'] = 'AG';
						
						//Si es cancelado el estado de agenda debe cambiar a Sin cita o Con cita
						//Esto se hace para que en la agenda sepan si ya tienen asignado la cita o no
						if( $orden['estado'] == 'C' )
						{
							if( empty( $orden['estadoExterno'] ) ){
								$datosCita['estadoAgenda'] = 'SC';
							}
							else{
								$datosCita['estadoAgenda'] = 'CC';
							}
						}
						
						if( $orden['estado'] == 'C' ){
							
							$datosCita['idAgenda'] = $datos['idAgenda'];
							$datosCita['idHiruko'] = $datos['idHiruko'];
							
							$tieneCita = false;
							if( $datos['conCita'] == 'on' || ( !empty( $datos['fechaCita'] ) && $datos['fechaCita'] != '0000-00-00' ) || !empty( $datos['idHiruko'] ) ){
								$tieneCita = true;
							}
							
							$index = 0;
							
							foreach( $modalidadesCup as $keyMods => $mods ){
								
								$datosCita['cups'][] = [
										'cup' 			=> $orden['procod'], //2022-01-28 - Sebastián Nevado: Se cambia código cups por código del procedimiento 
										'item' 			=> $tipoOrden."-".$nroOrden."-".$orden['item'].( $tieneCita ? '' : ",".$index ),
										'orden'			=> $tipoOrden."-".$nroOrden,
										'modalidad' 	=> $mods,
										'justificacion' => $orden['justificacion'],
									];
									
								$index++;
								
								$datosPorEstado[ 'C' ][] = $datosCita;
								
								if( $tieneCita )
									break;
								else{
									$datosCita['cups'] = [];
								}
							}	
						}
						else{
							
							if( !isset($datosPorEstado['O']) ){
								$datosPorEstado[ 'O' ][0] 	= $datosCita;
							}
							
							$index = 0;
						
							foreach( $modalidadesCup as $keyMods => $mods ){
								
								$datosPorEstado[ 'O' ][0]['cups'][] = [
											'cup' 			=> $orden['procod'], //2022-01-28 - Sebastián Nevado: Se cambia código cups por código del procedimiento 
											'item' 			=> $tipoOrden."-".$nroOrden."-".$orden['item'].",".$index,
											'orden'			=> $tipoOrden."-".$nroOrden,
											'modalidad' 	=> $mods,
											'justificacion' => $orden['justificacion'],
										];
								
								$index++;
							}
						}
					}

					//se crea el mensaje ORM para procesar las ordenes por modalidad
					if( count($datosPorEstado['O']) > 0 )
					{	
						foreach( $datosPorEstado['O'] as $dc )
						{
							$mensaje = crearMensajesHL7ORMAgenda( $conex, $wemp_pmla, $paciente, $dc );
							
							//Conectando vía socket
							$direccion = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ipHL7HirukoAgendarOrden' );
							// $direccion = "181.143.71.154";
							// $puerto = 6663;

							$socket = stream_socket_client("tcp://$direccion", $errno, $errstr);
							// $socket = fsockopen( $direccion, $puerto, $errno, $errstr, 5 );
							// $socket = stream_socket_client("tcp://$direccion:$puerto", $errno, $errstr);

							if( $socket ){
								$val = fwrite($socket, utf8_encode($mensaje ) );
								
								fclose($socket);
								
								foreach( $dc['cups'] as $k => $v ){
									
									list( $v_tor, $v_nor, $v_item ) = explode( "-", $v['item'] );
									
									marcarEstudioComoEnviado( $conex, $wmovhos, $whce, $v_tor, $v_nor, $v_item );
								}
							}
							
							registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'MATRIX-HIRUKO', $dc['tipoOrden'], $dc['nroOrden'], '', $mensaje );
							
							echo $mensaje;
						}
					}
					
					if( count($datosPorEstado['C']) > 0 )
					{
						//se crea el mensaje ORM para procesar las ordenes por modalidad
						foreach( $datosPorEstado['C'] as $datosCita )
						{
							actualizarMovimiento( $conex, $wmovhos, $datosCita['idMatrix'], [ 'estadoCita' => '0' ] );
							
							$mensaje =  crearMensajesHL7ORMAgenda( $conex, $wemp_pmla, $paciente, $datosCita );
							
							//Conectando vía socket
							$direccion = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ipHL7HirukoCancelarOrden' );
							// $direccion = "181.143.71.154";
							// $puerto = 6664;

							$socket = stream_socket_client("tcp://$direccion", $errno, $errstr);
							// $socket = fsockopen( $direccion, $puerto, $errno, $errstr, 5 );
							// $socket = stream_socket_client("tcp://$direccion:$puerto", $errno, $errstr);

							if( $socket ){
								$val = fwrite($socket, utf8_encode($mensaje ) );
								
								fclose($socket);
								
								foreach( $datosCita['cups'] as $k => $v ){
									
									list( $v_tor, $v_nor, $v_item ) = explode( "-", $v['item'] );
									
									marcarEstudioComoEnviado( $conex, $wmovhos, $whce, $v_tor, $v_nor, $v_item );
								}
							}
							
							list( $tor, $nro, $ite ) = explode( "-", $datosCita['cups'][0]['item'] );
							
							registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'MATRIX-HIRUKO', $tor, $nro, $ite, $mensaje );
							
							echo $mensaje;
						}
					}
					
				break;
				
				default: break;
			}
		}
	}
}

if( $_GET ){
	
	$accion = $_GET['accion'];
		
	if( $accion ){
		
		switch( $accion ){
			
			case 'consultarMedicosRemitentes': 
				
				$whce = consultarAliasPorAplicacion( $conex, $_GET['wemp_pmla'], 'hce' );
			
				$result = consultarMedicosRemitentes( $conex, $whce, $_GET['tipoOrden'], $_GET['term'] );
				
				echo json_encode( $result );
			break;
			
			case 'consultarMaestros': 
				
				$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
				$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
				$whce 	 = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
				
				//Cco desde donde se carga el procedimiento
				$cco_sede = $_GET['cco_sede'];

				//Código de matrix del usuario que grabo el cargo
				$usuarioGC = $_GET['usuarioGC'];

				$usuario_gc	= informacionUsuarioGrabaCargos( $conex, $wemp_pmla, $usuarioGC );
			
				//Consulto la información básica del paciente
				$paciente 	= informacionPaciente( $conex, $wemp_pmla, $historia, $ingreso );	//función en funcionesGeneralesEnvioHL7
				
				//Consulto los datos de la última cita, esto solo para dar una prioridad por defecto
				$cita 		= consultarUltimosDatosCita( $conex, $wemp_pmla, $historia, $ingreso, $cco_sede );
				
				//Consulto los último procedimiento cargado por cco
				//La función siempre trae el último procedimiento cargado por cco para el día en curso
				$procedimientosCargados = consultarProcedimientosCargados( $conex, $wcliame, $historia, $ingreso, $cco_sede ); //función en funcionesGeneralesEnvioHL7
				
				//Codigo cup
				$cup 		= $procedimientosCargados[0]['codigo'];
				
				//Modalidad por defecto
				$modDefecto = consultarModalidadPorCup( $conex, $wmovhos, $cup );
				
				//Modalidades del cup
				$modPorCup = modalidadesPorCup( $conex, $wmovhos, $cup );
				//Consulto la sede
				$sede  = consultarSedePorCcoYCup( $conex, $wmovhos, $cco_sede, $cup );
				
				//Consulto todos los maestros respectivos para la sede y los cuales se mostraran en la modal
				$result = consultarMaestros( $conex, $wemp_pmla, $sede['codigo'] );
				$result['paciente'] = $paciente;
				
				//Solo se muestra la modal si se encuentra una sede para el cco que carga y el cup cargado
				$result['validar'] = !empty($sede);
				//Modalidades del cup
				$result['modalidades_cup'] = $modPorCup;
				
				//Debe mostrarse la modal solo tiene cita
				$result['mostrar'] = true;	//Variable que indica si la modal se muestra
				
				$result['sede'] = $sede;	//Variable que indica si la modal se muestra
				
				$result['sede']['descripcion'] = utf8_encode( $result['sede']['descripcion'] );
				
				$result['indicaciones'] = consultarIndicacionesPorCup( $conex, $whce, $sede['tipoOrden'], $cup );
				
				$datosCita = consultarPacienteConCita( $conex, $wemp_pmla, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], $cup );

				//Permite obtener el centro de costo de medicina nuclear si no existe cita
				$ccoSede = isset( $datosCita['Mvcsed'] ) ? $datosCita['Mvcsed'] : ccoCodNuclear($conex, $wmovhos);

				// Se valida si el estudio a realziar es de medicina nuclear
				$es_medicina_nuclear = esMedicinaNuclear($conex, $wmovhos, $wcliame, $ccoSede, $historia, $ingreso, $cup);
				
				if( $datosCita )
					$result['datosCita'] = $datosCita;
				if( count( $cita ) > 0 )
					$result['defaults']['prioridad'] = $cita['Mvcpri'];
				
				if( !empty($modDefecto) ){ 
					$result['defaults']['modalidad'] = ( isset($datosCita['Mvcmod']) && !empty($datosCita['Mvcmod']) ) ? $datosCita['Mvcmod'] : $modDefecto;
				}
				
				if( !empty($cita['Mvcind']) ){
					$result['defaults']['indicacion'] = $cita['Mvcind'];
				}
				
				if( !empty($cita) ){
					$result['defaults']['sala'] = $datosCita['Mvcsal'];
				}

				if ( isset( $datosCita['Mvcmod'] ) && ($datosCita['Mvcmod'] == 'NM' || $datosCita['Mvcmod'] == 'TH') ) {
					$result['mostrar'] = $es_medicina_nuclear;
				}

				if( is_array( $result['modalidades'] ) )
				{
					foreach($result['modalidades'] as $modalidad)
					{
						if ( $modalidad['codigo'] == 'NM' || $modalidad['codigo'] == 'TH'  ) {
							$result['mostrar'] = $es_medicina_nuclear;
						}
					}
				}

				$result['user_gc'] = $usuario_gc;
				
				echo json_encode($result);
			break;
			
			default: break;
		}
	}
}

