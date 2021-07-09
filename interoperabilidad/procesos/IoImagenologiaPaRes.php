<?php

include_once("root/comun.php");
include_once("./funcionesGeneralesEnvioHL7.php");
include_once("./interoperabilidad_ws.php");

// El segmento PID contiene la información del paciente 
define("PID_TIPO_IDENTIFICACION_PACIENTE", 2 );
define("PID_IDENTIFICACION_PACIENTE", 3 );

//Segmento ORC
define("ORC_ID_CITA", 2 );
define("ORC_CONTROL_ORDEN", 1 );
define("ORC_ESTADO_ORDEN" , 5 );

//Resultado OBX
define("OBX_ID", 1 );
define("OBX_OBSERVATION_ID", 3 );
define("OBX_OBSERVATION_VALUE", 5 );
define("OBX_OBSERVATION_DATE", 14 );

//Resultado OBR
define("OBR_NUMERO_ESTUDIO", 2 );
define("OBR_ESTUDIO", 4 );
define("OBR_FECHAHORA", 6 );
define("OBR_FECHAHORARESULTADO", 7 );
define("OBR_CODIGO_PACS", 18 );
define("OBR_STATUS", 24 );

function procesarMsgORM( $conex, $wemp_pmla, $HL7, $message )
{	
	$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );
	$wmovhos=consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	
	//Siempre se espera un segmento OBR
	if( $HL7['ORC'] && $HL7['OBR'] )
	{
		$status = '';
		$historiaClinica 	= '';
		$ingresoClinica 	= '';
		$tipoOrden	= ''; 
		$nroOrden	= '';
		$item		= '';
		$fechaHoraResultado = '';
		
		$tipoDocumento	= ""; 
		$nroDocumento 	= "";
		if( $HL7['PID'] && $HL7['PID'][0] ){
			$docPID = explode("^", $HL7['PID'][0][ PID_IDENTIFICACION_PACIENTE ]);
			$tipoDocumento = $docPID[1];
			$nroDocumento = $docPID[0];
		}
		$pac = consultarHistoriaPaciente( $conex, $wemp_pmla, $tipoDocumento, $nroDocumento );
		$historiaClinica 	= $pac['historia'];
		$ingresoClinica 	= $pac['ultimoIngreso'];
				
		$obxId 		         = ""; 
		$obxObservationId    = "";
		$obxObservationValue = "";
		$obxObservationDate  = "";
		
		//El segmento AIS contiene la información de la cita, siempre se espera
		list( $tipoOrden, $nroOrden, $item ) = explode( "-", $HL7['ORC'][0][ ORC_ID_CITA ] );
		$controlOrden = $HL7['ORC'][0][ ORC_CONTROL_ORDEN ];
		$estado = $HL7['ORC'][0][ ORC_ESTADO_ORDEN ];
		$codigoPACS = "";
		$detUrl = "";
		$detUrp = "";
		
		//Siempre se recibe un solo OBR
		foreach( $HL7['OBR'] as $kOBR => $OBR )
		{
			$fechaHoraProcedimiento= $OBR[ OBR_FECHAHORA ];
			$fechaHoraResultado = $OBR[ OBR_FECHAHORARESULTADO ];
			$codigoPACS = $OBR[ OBR_CODIGO_PACS ];
			$status = $OBR[ OBR_STATUS ];
			break;
		}

		if( $HL7['OBX'] && $HL7['OBX'][0] ){
			$obxId 		         = $HL7['OBX'][0][ OBX_ID ]; 
			$obxObservationId    = $HL7['OBX'][0][ OBX_OBSERVATION_ID ];
			$obxObservationValue = $HL7['OBX'][0][ OBX_OBSERVATION_VALUE ];
			$obxObservationDate  = $HL7['OBX'][0][ OBX_OBSERVATION_DATE ];
			// $observationValue = explode("\\.br\\", $obxObservationValue);
			// $detUrl = $observationValue[(count($observationValue) - 1)];
			$detUrl = "https://imagenes.sabbagradiologos.com/MediWorksLightWeb/loginViewer?loginUsuario=CPA&loginSenha=CPA&exame=".$codigoPACS;
			$detUrp = "https://imagenes.sabbagradiologos.com/MediWorksLightWeb/loginLaudo?loginUsuario=CPA&loginSenha=CPA&exame=".$codigoPACS;
			$respuesta = array(
							'Rldtio'=> $tipoOrden,
							'Rldnuo'=> $nroOrden,
							'Rldite' => $item,
							'Rldxml' => str_replace("\\.br\\","\\n",$obxObservationValue),
							'Rldhis' => $historiaClinica,
							'Rlding' => $ingresoClinica
						);
			guardarResultadoWs($conex,$wmovhos, $respuesta);
		}
		
		$fechaCita = date("Y-m-d", strtotime($fechaHoraProcedimiento));
		$horaCita = date("H:i:s", strtotime($fechaHoraProcedimiento));
			
		registrarMsgLogHl7( $conex, $wmovhos, $historiaClinica, $ingresoClinica, $tipoDocumento, $nroDocumento, 'SABBAG-MATRIX', $tipoOrden, $nroOrden, $item, $message,"" );
		registrarDetalleLog( $conex, $wmovhos, $historiaClinica, $ingresoClinica, $tipoOrden, $nroOrden, $item, 'SABBAG-MATRIX', $message,"" );
			
		//Si tiene tipo de orden, numero de orden e item significa que está en orden y por tanto voy a actualizar los datos necesario en ordenes
		if( !empty($tipoOrden) && !empty($nroOrden) && !empty($item) ){
			if(!empty($estado)){
				// $valEstado = "E";
				// if($estado == "IP"){
					// $valEstado = "EP";
				// }else if($estado == "SC"){
					// $valEstado = "EP";
					// $comentario = "Cancelado por parte de SABBAG";
				// }else if($estado == "CM"){
					// if(!empty($obxObservationId)){
						// $fechaCita = date("Y-m-d", strtotime($fechaHoraResultado));
						// $horaCita = date("H:i:s", strtotime($fechaHoraResultado));
						// if($obxObservationId == "REP"){
							// $valEstado = "F";
						// }else if($obxObservationId == "ADT"){
							// $valEstado = "C";
						// }
					// }
				// }
				
				$valEstado = $estado;
				if($estado == "CM"){
					if(!empty($status)){
						$valEstado = $status;
					}
				}
				
				$fechaCita 	= ""; 
				$horaCita	= "";
				cambiarEstadoExamen( $conex, $wemp_pmla, $tipoOrden, $nroOrden, $item, $valEstado, $fechaCita, $horaCita, $comentario, $historiaClinica, $ingresoClinica, $detUrl, $detUrp );	
			}
		}
		
	}
	else{
		
		registrarMsgLogHl7( $conex, $wmovhos, '', '', '', '', 'SABBAG-MATRIX', '', '', '', $message, "" );
		
		$respuesta['message'] 	= 'Mensaje no reconocido';
		endRoutine( $respuesta, 400 );
	}
}

//Proceso de acuerdo al protocolo HL7
if( $_POST ){
	if( $HL7 ){
		//La posición 8 indica el tipo de mensaje recibido
		list( $tipoMsg ) = explode( "^", $HL7['MSH'][0][8] );
				
		switch( $tipoMsg ){
			
			case 'ORM':
				procesarMsgORM( $conex, $wemp_pmla, $HL7, $message );
				$respuesta['message'] 	= 'Mensage procesado';
				endRoutine( $respuesta, 200 );
				break;
			case 'ORU':
				procesarMsgORM( $conex, $wemp_pmla, $HL7, $message );
				$respuesta['message'] 	= 'Mensage procesado';
				endRoutine( $respuesta, 200 );
				break;
			default:
				$respuesta['message'] 	= 'Petición desconocida';
				endRoutine( $respuesta, 400 );
				break;
		}
	}
}