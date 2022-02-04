<?php
/***************************************************************************************************************************************************
 *	Modificaciones:
 *
 * Mayo 23 de 2020			Edwin MG	Si el estudio es cancelado en ordenes no se permite cambiar el estado por interoperabilidad
 * Mayo 19 de 2020			Edwin MG	Se realizan los siguientes cambios
 *										  - Se llama a la función cambioEstadoInteroperabilidad desde la función crearMensajesHL7OLMTrasladoPacientes 
 *										    de estado automático al momento de recibir un paciente en piso en el traslado de pacientes, para que 
 *										    se haga un cambio  por interoperabilidad.
 *										  - Se corrige el anexo a pacientes ya que no se estaba tomando correctamente el mensaje HL7 de anexos
 *											que llegaba desde laboratorio
 * Mayo 04 de 2020			Edwin MG	Se hacen cambios varios para la interoperabilidad con laboratorio por cco
 * Febrero 03 de 2020		Edwin MG	Se recibe el tipo de documento en el mensaje HL7 enviado desde laboratorio
 ***************************************************************************************************************************************************/
 
/**
 * Este script es llamado desde interoperabilidad.php
 * Se asume que:
 * Ya está incluido el conex
 * Existe la variable HL7
 */

//Defino constantes para acceder más faciles a los posiciones del mensaje
//Por convención comienza con el segmento y luego nombre de la posición la posición
define("MSH_EMPRESA_ENVIA", 3 );
define("PID_NUMERO_DOCUMENTO", 2 );
define("PID_HISTORIA_PACIENTE", 3 );
define("PV1_INGRESO_PACIENTE", 19 );
define("ORC_NUMERO_ORDEN", 3 );
define("ORC_ORDEN_DE_TRABAJO", 2 );	//Este es la orden de trabajo de laboratroio y es único
define("ORC_URL", 11 );	//Este es la orden de trabajo de laboratroio y es único
define("OBR_NUMERO_ESTUDIO", 3 );
define("OBR_ESTUDIO", 4 );
define("OBR_USUARIO_TOMA_MUESTRA", 10 );
define("OBR_ESTADO", 25 );
define("OBR_MUESTRA_SITIO_ANATOMICO", 15 );

include_once("root/comun.php");
include_once("./funcionesGeneralesEnvioHL7.php");

$wemp_pmla = $_REQUEST['wemp_pmla'];

// if( !isset( $wemp_pmla ) )
// 	$wemp_pmla = "01";

function actualizarUrl( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden, $item, $url ){
	
	$respuesta = array(
					'message'=>'', 
					'result'=>array( 'proceso' => true ), 
					'status'=>'',
				);
	
	$val = false;
	
	//Busco la orden por
	$sql = "UPDATE ".$wmovhos."_000159
			   SET Deturp = '".$url."'
			 WHERE Dettor = '".$tipoOrden."'
			   AND Detnro = '".$nroOrden."'
			   AND Detite = '".$item."'
			";

	$res = mysql_query( $sql, $conex );

	//Busco la orden por
	$sql = "UPDATE ".$whce."_000028
			   SET Deturp = '".$url."'
			 WHERE Dettor = '".$tipoOrden."'
			   AND Detnro = '".$nroOrden."'
			   AND Detite = '".$item."'
			";

	$res1 = mysql_query( $sql, $conex );	
	
	// // Busco la orden por
	// $sql = "UPDATE ".$whce."_000027
			   // SET Ordurl = '".$url."'
			 // WHERE Ordest = 'on'
			   // AND Ordtor = '".$tipoOrden."'
			   // AND Ordnro = '".$nroOrden."'
			// ";

	// $res2 = mysql_query( $sql, $conex );

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


function atualizarOrdenTrabajoLaboratorio( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden, $item, $ordenLaboratorio ){
	
	$val = true;
	
	//Busco la orden por
	$sql = "UPDATE ".$whce."_000028 a
			   SET a.Detotr = '".$ordenLaboratorio."'
			 WHERE a.Dettor = '".$tipoOrden."'
			   AND a.Detnro = '".$nroOrden."'
			   AND a.Detite = '".$item."'
			";

	$res = mysql_query( $sql, $conex );
	
	if( mysql_affected_rows() === false ){
		$val = false;
	}
	
	//Busco la orden por
	$sql = "UPDATE ".$wmovhos."_000159 a
			   SET a.Detotr = '".$ordenLaboratorio."'
			 WHERE a.Dettor = '".$tipoOrden."'
			   AND a.Detnro = '".$nroOrden."'
			   AND a.Detite = '".$item."'
			";

	$res = mysql_query( $sql, $conex );
	
	if( mysql_affected_rows() === false ){
		$val = false;
	}
	
	return false;
}

function activarOrdenAnexa( $conex, $whce, $tipoOrden, $ordenAnexar, $ordenTrabajo ){
	
	$respuesta = array('message'=>'', 'result'=>array( 'proceso' => true ), 'status'=>'' );
	
	$val = false;

	//Busco la orden por
	$sql = "UPDATE ".$whce."_000027
			   SET Ordanx = 'on',
			       Ordotr = '".$ordenTrabajo."'
			 WHERE Ordest = 'on'
			   AND Ordtor = '".$tipoOrden."'
			   AND Ordnro = '".$ordenAnexar."'
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


function registrarLog( $conex, $wmovhos, $tipoOrden, $nroOrden, $item, $consecutivo, $sitioAnatomicoAnterior, $nuevoSitioAnatomico, $muestraAnterior, $nuevaMuestra ){
	
	$val = true;
	
	$fecha 	= date( "Y-m-d" );
	$hora 	= date( "H:i:s" );
	
	
	//Busco el estudio asociado
	$sql = "INSERT INTO 
				".$wmovhos."_000261(    Medico     , Fecha_data ,   Hora_data ,    Logtor     ,     Lognro     ,   Logite  ,    Logcns        ,      Lognsa              ,         Logntm    ,            Logsaa           ,     Logtma           , Logest, Seguridad )
							 VALUES( '".$wmovhos."','".$fecha."', '".$hora."' ,'".$tipoOrden."','".$nroOrden."','".$item."','".$consecutivo."','".$nuevoSitioAnatomico."','".$nuevaMuestra."','".$sitioAnatomicoAnterior."','".$muestraAnterior."',  'on' , 'C-".$wmovhos."' )
			 ";
	
	$res = mysql_query($sql, $conex);
	
	if( mysql_affected_rows() === false ){
		$val = false;
	}
	
	return false;
}

function corregirEstudio( $conex, $wemp_pmla, $tipoOrden, $nroOrden, $item, $estado, $codigoTipoMuestra, $descripcionTipoMuestra, $codigoSitioAnatomico, $descripcionSitioAnatomico, $consecutivo ){
	
	$respuesta = array('message'=>'', 'result'=>array( 'proceso' => true ), 'status'=>'' );
	
	$val = false;
	
	$whce 	 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce " );
	$wmovhos 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos " );
	$wmcliame 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame " );
	
	//Busco el estudio asociado
	$sql = "SELECT *
			  FROM ".$wmovhos."_000258
			 WHERE Saotor = '".$tipoOrden."'
			   AND Saonro = '".$nroOrden."'
			   AND Saoite = '".$item."'
			   AND Saocns = '".$consecutivo."'
			 ";
	
	$res = mysql_query($sql, $conex);
	
	if( $res )
	{
		if( $rows = mysql_fetch_array($res) )
		{
			//Actualizo a lo nuevo 
			$sql = "UPDATE ".$wmovhos."_000258
					   SET Saocor = '".$codigoTipoMuestra."',
						   Saodor = '".$descripcionTipoMuestra."',
						   Saocsa = '".$codigoSitioAnatomico."',
						   Saodsa = '".$descripcionSitioAnatomico."'
					 WHERE id = ".$rows['id']." ";
			
			$res = mysql_query($sql, $conex);
			
			if( mysql_affected_rows() !== false ){
				
				$sitioAnatomicoAnterior = $rows['Saocsa']."-".$rows['Saodsa'];
				$nuevoSitioAnatomico 	= $codigoSitioAnatomico."-".$descripcionSitioAnatomico;;
				$muestraAnterior 		= $rows['Saocor']."-".$rows['Saodor'];
				$nuevaMuestra 			= $codigoTipoMuestra."-".$descripcionTipoMuestra;
				
				registrarLog( $conex, $wmovhos, $tipoOrden, $nroOrden, $item, $consecutivo, $sitioAnatomicoAnterior, $nuevoSitioAnatomico, $muestraAnterior, $nuevaMuestra );
			}
			else{
				$respuesta['message'] 	= 'No se actualizo la muestras '.$tipoOrden.'-'.$nroOrden.'-'.$item.'-'.$consecutivo;
				endRoutine( $respuesta, 400 );
				$val = false;
			}
		}
		else{
			$respuesta['message'] 	= 'Muestras no encontradas para la orden '.$tipoOrden.'-'.$nroOrden.'-'.$item.'-'.$consecutivo;
			endRoutine( $respuesta, 400 );
			$val = false;
		}
	}
	
	return $val;
}

function cambiarEstadoExamen( $conex, $wemp_pmla, $tipoOrden, $nroOrden, $item, $estado, $estudio, $justificacion, $usuario_toma_muestra, $historia = '', $ingreso = '' ){
	
	$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );
	
	$whce 	 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce " );
	$wmovhos 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos " );
	
	// $estadoPorTipoOrden = consultarAliasPorAplicacion( $conex, $wemp_pmla, "permitirCambiarEstadoInteroperabilidadPorTipoOrden" );
	// $estadoPorTipoOrden = explode( "-", $estadoPorTipoOrden );
	
	$estadoPorTipoOrden = ccoConInteroperabilidadLaboratorio( $conex, $wmovhos, $historia, $ingreso );
	
	$tieneInteroperabilidadPorCco = in_array( $tipoOrden, $estadoPorTipoOrden ) ? true : false;
	
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
			
			$resExiste 	= mysql_query( $sql, $conex );
			$num 		= mysql_num_rows( $resExiste );
			
			if( $num > 0 ){
				
				$rowsOrdenClinica = mysql_fetch_array( $resExiste );
				
				$toma_muestra 		= $rowsOrdenClinica['Detutm'];
				$fecha_toma_muestra = $rowsOrdenClinica['Detftm'];
				$hora_toma_muestra 	= $rowsOrdenClinica['Dethtm'];
				if( !empty( $usuario_toma_muestra ) && empty( $rowsOrdenClinica['Detutm'] ) ){
					$toma_muestra 		=  utf8_decode( $usuario_toma_muestra );
					$fecha_toma_muestra =  date("Y-m-d");
					$hora_toma_muestra 	=  date("H:i:s");
				} 
				
				//Si el estado es cancelado, deje el registro como pendiente de por examen cancelado (Detplc)
				$pendienteLecuraCancelado = $rowsOrdenClinica['Detplc'];
				if( $row['Eexcan'] == 'on' || $row['Eexere'] == 'on' ){
					$pendienteLecuraCancelado = 'on';
				}
				
				//Si no permite cambiar Estado, dejo como estaba
				$estadoOrden = $row['Estepc'];
				if( !$tieneInteroperabilidadPorCco )
					$estadoOrden = $rowsOrdenClinica['Detesi'];
				
				if( strtoupper( trim( $rowsOrdenClinica['Detesi'] ) ) == 'C' ){
					$estadoOrden = $rowsOrdenClinica['Detesi'];
				}
				
				//Actuzando el estado de la orden
				$sql = "UPDATE ".$wmovhos."_000159
						   SET Detesi = '".$estadoOrden."',
							   Deteex = '".$estado."',
							   Detjoc = '".mysql_escape_string( $justificacion )."',
							   Detfme = '".date( "Y-m-d" )."',
							   Dethme = '".date( "H:i:s" )."',
							   Detcor = '".$row['Esteco']."',
							   Detplc = '".$pendienteLecuraCancelado."',
							   Detutm = '".$toma_muestra."',
							   Detftm = '".$fecha_toma_muestra."',
							   Dethtm = '".$hora_toma_muestra."'
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
							   Detplc = '".$pendienteLecuraCancelado."',
							   Detutm = '".$toma_muestra."',
							   Detftm = '".$fecha_toma_muestra."',
							   Dethtm = '".$hora_toma_muestra."'
						 WHERE Dettor = '".$tipoOrden."'
						   AND Detnro = '".$nroOrden."'
						   AND Detite = '".$item."'";
				
				$res = mysql_query( $sql, $conex );
				
				$estGeneraCca = $row['Eexcca'];
				
				/* FUNCION QUE REALIZA LA VALIDACION DE CARGOS AUTOMATICOS */
				$worigen = 'Interoperabilidad - Laboratorio';
				interoperabilidadCargosAutomaticos($conex, $wemp_pmla, $whce, $wmovhos, $worigen, $nroOrden, $item, $tipoOrden, $estGeneraCca);
				
				if( $res ){
					
					registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Cambio de estado externo', $estado."-".$row['Estdes']."-".$row['Estdpa'] );
					registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Cambio de estado ordenes', $rowsOrdenClinica['Detesi'].'->'.$row['Estepc'] );
					
					if( !empty($justificacion) ){
						registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso, $tipoOrden, $nroOrden, $item, 'Justificacion asignada', $justificacion );
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

function procesarMsgOML( $conex, $wemp_pmla, $HL7, $his, $ing )
{
	$whce 	 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
	$wmovhos 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	
	//Indica si se debe actualizar la orden de laboratorio por estudio
	$actualizarOrdenTrabajoLab 	= false;
	$actualizarUrl 				= false;
	$ordenLaboratorio 			= "";
	
	$usuario_toma_muestra = '';
	
	$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );
	
	$tipoOrden	= "";
		
	if( $ORC = $HL7['ORC'][0] )
	{
		//Busco la orden a la que se anexaran más ordenes
		$ordenLaboratorio = $ORC[ ORC_ORDEN_DE_TRABAJO ];
		
		list( $nroOrden, $ordenAnexar, $ordenTrabajo ) = explode( "^", $ORC[ ORC_NUMERO_ORDEN ] );

		list( $tipoOrden, $nroOrden ) 		= explode( "-", $nroOrden );
		
		//Si hay una orden a la que anexarserle ordenes, activo el parametro correspondiente
		if( !empty($ordenAnexar) ){
			list( $tipoOrden2, $ordenAnexar ) 	= explode( "-", $ordenAnexar );
			
			//Siempre deben ser el mismo tipo de orden
			activarOrdenAnexa( $conex, $whce, $tipoOrden2, $ordenAnexar, $ordenTrabajo );
		}
		
		//Si hay una orden a la que anexarserle ordenes, activo el parametro correspondiente
		if( !empty($tipoOrden) && !empty($nroOrden) && !empty($ordenLaboratorio) ){
			
			//Siempre deben ser el mismo tipo de orden
			$actualizarOrdenTrabajoLab = true;
		}
		
		$url = $ORC[ ORC_URL ];
		
		//Actualzando url
		if( !empty($tipoOrden) && !empty($nroOrden) && !empty($url) ){
			
			// $url = "https://s3.amazonaws.com/lmla-resultados/".$url.".pdf";
			//Siempre deben ser el mismo tipo de orden
			$actualizarUrl = true;
		}
	}
	else{
		$respuesta['message'] 	= 'Mensaje OML mal formado';
		endRoutine( $respuesta, 400 );
	}
	
	if( $HL7['OBR'] )
	{
		$cambiosEstados = [];

		foreach( $HL7['OBR'] as $kOBR => $OBR )
		{
			$usuario_toma_muestra = $OBR[ OBR_USUARIO_TOMA_MUESTRA ];
			
			$numero_estudio = $OBR[ OBR_NUMERO_ESTUDIO ];
			list( $tpOrden, $nroOrden, $item ) = explode( "-", $numero_estudio );
			
			//Este es el cups del procedimiento/examen
			list( $estudio, $descripcionEstudio ) = explode( "^", $OBR[ OBR_ESTUDIO ] );
			
			list( $estado, $justificacion ) = explode( "^", $OBR[ OBR_ESTADO ] );
			
			if( !isset( $cambiosEstados[$nroOrden][$item] ) )
				$cambiosEstados[$nroOrden][$item] = [];
				
			$cambiosEstados[$nroOrden][$item] = [
							'estado' 		=> $estado, 
							'estudio' 		=> $estudio, 
							'justificacion' => $justificacion, 
						];
						
			
			if( !empty( $OBR[ OBR_MUESTRA_SITIO_ANATOMICO ] ) ){
				
				$sitioAnatomicoMuestra = explode( "^", $OBR[ OBR_MUESTRA_SITIO_ANATOMICO ] );
				
				list( $codigoTipoMuestra, $descripcionTipoMuestra ) 		= explode( "-", $sitioAnatomicoMuestra[0] );
				list( $codigoSitioAnatomico, $descripcionSitioAnatomico ) 	= explode( "-", $sitioAnatomicoMuestra[3] );
				
				$consecutivo = $sitioAnatomicoMuestra[5];
				
				$cambiosEstados[$nroOrden][$item]['especimenes'][] = [
							'codigoTipoMuestra' 		=> $codigoTipoMuestra,
							'descripcionTipoMuestra' 	=> $descripcionTipoMuestra,
							'codigoSitioAnatomico' 		=> $codigoSitioAnatomico,
							'descripcionSitioAnatomico' => $descripcionSitioAnatomico,
							'consecutivo' 				=> $consecutivo,
						];
			}
		}
		
		//Recorro nuevamente el array
		//Se guarda así por que en el OBR pueden recibir
		foreach( $cambiosEstados as $nroOrden => $vnorOrden ){
			foreach( $vnorOrden as $item => $datos ){
				cambiarEstadoExamen( $conex, $wemp_pmla, $tipoOrden, $nroOrden, $item, $datos['estado'], $datos['estudio'], $datos['justificacion'], $usuario_toma_muestra, $his, $ing );
				
				//Si hay un estudio corregido, hago el cambio y registro el log
				if( $datos['estado'] == 'C' ){
					foreach( $datos['especimenes'] as $especimen ){
						corregirEstudio( $conex, $wemp_pmla, $tipoOrden, $nroOrden, $item, $datos['estado'], $especimen['codigoTipoMuestra'], $especimen['descripcionTipoMuestra'], $especimen['codigoSitioAnatomico'], $especimen['descripcionSitioAnatomico'], $especimen['consecutivo'] );
					}
				}
				
				if( $actualizarOrdenTrabajoLab ){
					atualizarOrdenTrabajoLaboratorio( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden, $item, $ordenLaboratorio );
				}
				
				if( $actualizarUrl ){
					actualizarUrl( $conex, $whce, $wmovhos, $tipoOrden, $nroOrden, $item, $url );
				}
			}
		}
	}
	// else{
		// $respuesta['message'] 	= 'Mensaje OML mal formado';
		// endRoutine( $respuesta, 400 );
	// }
}

function procesarMsgORU( $HL7 ){
	
}

function ConectarFTP( $server, $port, $user, $password, $modo ){
	
	//Permite conectarse al Servidor FTP
	$id_ftp = ftp_connect( $server, $port ); //Obtiene un manejador del Servidor FTP
	
	if( $id_ftp ){
		
		$login = ftp_login( $id_ftp, $user, $password ); //Se loguea al Servidor FTP
		
		if( $login ){
			$pasv = ftp_pasv( $id_ftp, $modo ); //Establece el modo de conexión
			
			if( !$pasv ){
				$id_ftp = false;
			}
		}
		else{
			$id_ftp = false;
		}
	}
	
	return $id_ftp; //Devuelve el manejador a la función
}

function subirArchivosFtp( $archivo_remoto, $archivo_local, $wemp_pmla = '01' ){
	
	$val = false;
	
	$ftpLaboratorio = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ftpLaboratorio' );
	
	list( $server, $port, $user, $password, $carpeta ) = explode( ",", $ftpLaboratorio );
	
	//Sube archivo de la maquina Cliente al Servidor (Comando PUT)
	$id_ftp = ConectarFTP( $server, $port, $user, $password, $modo = true ); //Obtiene un manejador y se conecta al Servidor FTP
	
	if( $id_ftp ){
		
		$put = ftp_put( $id_ftp, $carpeta.$archivo_remoto, $archivo_local, FTP_ASCII );

		if( $put ){
			$val = true;
			
			//Sube un archivo al Servidor FTP en modo Binario
			ftp_quit($id_ftp); //Cierra la conexion FTP
		}
	}
		
	return $val;
}

function crearMensajesHL7OLMTrasladoPacientes( $conex, $wemp_pmla, $historia, $ingreso, $cco ){

	$ipLabTomaMuestra 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ipLMATomaMuestra' );

	$whce 		= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
	$wbasedato 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	
	//Si entrá aquí es por que le paciente fue recibido en un piso
	//Por tanto llamo a la función que cambia estados de las ordenes por interoperabilidad en caso de ser necesario
	cambioEstadoInteroperabilidad( $conex, $wbasedato, $whce, $historia, $ingreso );

	// $paciente	= consultarInfoPacienteOrdenHCEPorHistoria( $conex, $wbasedato, $historia );

	//Está función se encuentra en el script interoperabilidad/procesos/funcionesGeneralesEnvioHL7.php
	$pac = informacionPaciente( $conex, $wemp_pmla, $historia, $ingreso );

	$sql = "SELECT Ccocod, Cconom
			  FROM ".$wbasedato."_000011
			 WHERE Ccocod = '".$cco."'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$rows = mysql_fetch_array($res);
	
	$nombreCco = $rows['Ccocod'];
	$descripcionCco = $rows['Cconom'];
	
	$sql = "SELECT *
			  FROM ".$whce."_000027 a, ".$whce."_000028 b
			 WHERE a.Ordhis = '".$historia."'
			   AND a.Ording = '".$ingreso."'
			   AND a.Ordest = 'on'
			   AND b.Dettor = a.Ordtor
			   AND b.Detnro = a.Ordnro
			   AND b.Detest = 'on'
			   AND b.Deteex != ''
			   AND b.Deteex != 'F'
			   AND b.Deteex != 'C'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	while( $rows = mysql_fetch_array($res) )
	{
		$tablaOfertas 	= "";
		$campoOferta	= "";
		$campoEstado	= "";

		//Consulto si existe cups ofertados por tipo de orden
		$sql = "SELECT Valtoc, Valcoc, Valeoc
				  FROM ".$wbasedato."_000267
				 WHERE valtor = '".$rows['Ordtor']."'
				   AND valest = 'on'
			  GROUP BY 1,2,3";

		$resToOfertado 	= mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		$numToOfertado	= mysql_num_rows($resToOfertado);

		if( $numToOfertado > 0 ){

			if( $rowsToOfertado = mysql_fetch_array( $resToOfertado ) ){
				$tablaOfertas 	= $rowsToOfertado['Valtoc'];
				$campoOferta	= $rowsToOfertado['Valcoc'];
				$campoEstado	= $rowsToOfertado['Valeoc'];
			}
		}
		else{
			//Si no se encuentra en la tabla entonces no se puede enviar el mensaje
			continue;
		}
		
		$mensaje 		= "";

		$mensaje =   "MSH|^~\\&|MATRIX|PMLA|LIS4D|LMLA|".date("YmdHis")."||OML^O21|".$rows['Ordnro']."|P|2.5|"
					."\nPID||".trim( $pac['nroDocumento'] )."^".trim( $pac['tipoDocumento'] )."|".$historia."||".$pac['apellido1']."&".$pac['apellido2']."^".$pac['nombresCompletos']."||".str_replace( "-", "", $pac['fechaNacimiento'] )."|".$pac['genero']."|||".$pac['direccion']."^^".$pac['municipio']."^".$pac['departamento']."^^".$pac['pais']."||".$pac['celular']."^^^".$pac['correoElectronico']."^^^".$pac['telefono']."|"
					."\nPV1||I|^^".$pac['habitacion']."^CLA^^^^".$nombreCco."-".$descripcionCco."||||||||||||||||".$ingreso."|"
					."\nIN1|||".$pac['codigoResponsable']."|".$pac['nombreResponsable']."|||||||||||".$pac['tarifa']."|";

		if( !empty($mensaje ) ){

			/******************************************************************
			 * Creando archivo
			 ******************************************************************/
			$archivo = fopen( $nombreArchivo = "../../hce/procesos/".date("Ymd")."-$historia-$ingreso-".$rows['Ordnro'].".txt", "w+b ");

			if( $archivo == false ) {
				echo "Error al crear el archivo";
			}
			else
			{
				// Escribir en el archivo:
				fwrite($archivo,$mensaje );

				// Fuerza a que se escriban los datos pendientes en el buffer:
				fflush($archivo);
			}

			// Cerrar el archivo:
			fclose($archivo);

			$subirArchivo = subirArchivosFtp( date("Ymd")."-$historia-$ingreso-".$rows['Ordnro'].".txt", $nombreArchivo, $wemp_pmla );

			if( $subirArchivo ){
				registrarMsgLogHl7( $conex, $wbasedato, $historia, $ingreso, $pac['tipoDocumento'], $pac['nroDocumento'], 'MATRIX-LMLA', $rows['Ordtor'], $rows['Ordnro'], 0, $mensaje );
			}

			unlink( $nombreArchivo );
		}

		/******************************************************************/
	
		
		return;
	}
}


//Proceso de acuerdo al protocolo HL7
if( $_POST ){
	
	if( $HL7 ){
		
		//La posición 8 indica el tipo de mensaje recibido
		list( $tipoMsg ) = explode( "^", $HL7['MSH'][0][8] );
		
		$his = !empty( $HL7['PID'][0][PID_HISTORIA_PACIENTE] ) ? $HL7['PID'][0][PID_HISTORIA_PACIENTE] : '' ;
		$ing = !empty( $HL7['PV1'][0][PV1_INGRESO_PACIENTE] ) ? $HL7['PV1'][0][PV1_INGRESO_PACIENTE] : '' ; 
		$tdo = !empty( $HL7['PID'][0][PID_NUMERO_DOCUMENTO] ) ? explode( "^", $HL7['PID'][0][PID_NUMERO_DOCUMENTO] )[1] : '' ;
		$ndo = !empty( $HL7['PID'][0][PID_NUMERO_DOCUMENTO] ) ? explode( "^", $HL7['PID'][0][PID_NUMERO_DOCUMENTO] )[0] : '' ;
		$des = !empty( $HL7['MSH'][0][MSH_EMPRESA_ENVIA] ) ? $HL7['MSH'][0][MSH_EMPRESA_ENVIA] : '' ;
		$tor = !empty( $HL7['ORC'][0][ORC_NUMERO_ORDEN] ) ? explode( "^", $HL7['ORC'][0][ ORC_NUMERO_ORDEN ] )[0] : '' ;
		$nro = '';
		
		if( !empty($tor) ){
			list( $tor, $nro ) = explode( "-", $tor );
		}
		
		$msg = $message;
		
		$wmovhos = 'movhos';
		
		registrarMsgLogHl7( $conex, $wmovhos, $his, $ing, $tdo, $ndo, $des."-MATRIX", $tor, $nro, ''  , $msg );
		
		switch( $tipoMsg ){
			
			case 'OML': 
				procesarMsgOML( $conex, $wemp_pmla, $HL7, $his, $ing );
				$respuesta['message'] 	= 'Mensage procesado';
				endRoutine( $respuesta, 200 );
				break;
				
			case 'ORU': 
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
		
		switch( $_POST['accion'] ){
			case 'cambioUbicacion':
				crearMensajesHL7OLMTrasladoPacientes( $conex, $wemp_pmla, $historia, $ingreso, $cco );
			break;
		}
	}
}

