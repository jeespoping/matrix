<?php

/**
 * Modificaciones
 *
 * Octubre 20 de 2020	Edwin MG	Se hacen cambios para que el cron se crea diaramente solo para servicio domiciliario. También
 *									se deja la opción para que se puede ejecutar para todos agregando en la url el parametro todos.
 *									El parametro $todos puede tener cualquier valor
 */


function crearKardexAutomaticamente( $conex, $wbd, $his, $ing, $fecha ){

	global $wbasedato;
	global $usuario;
	global $wemp_pmla;
	
	$wbasedato = $wbd;
	
	$pac[ 'his' ] = $his;
	$pac[ 'ing' ] = $ing;

	//Obtengo la fehca del día anterior
	$ayer = date( "Y-m-d", strtotime( $fecha." 00:00:00" ) - 24*3600 );
	
	//Creo un array con los diferentes cco de costos que se deben crear
	$sql = "SELECT Karcco 
			FROM
				{$wbd}_000053
			WHERE
				karhis = '{$pac['his']}'
				AND karing = '{$pac['ing']}'
				AND fecha_data = '$ayer'
			";
	
	$resCcos = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	$numCcos = mysql_num_rows( $resCcos );

	if( $numCcos > 0 ){	//Indica que hubo kardex el día anterior y por tanto puede hacerse el kardex automatico
	
		//Consulto si se ha generado kardex el día de hoy, es decir si tiene encabezado
		$sql = "SELECT * 
				FROM
					{$wbd}_000053
				WHERE
					karhis = '{$pac['his']}'
					AND karing = '{$pac['ing']}'
					AND fecha_data = '$fecha'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		//Si no existe kardex
		if( $numrows < $numCcos ){	//Si la cantidad de kardex generados hoy es menor a los del día anterior quiere decir que faltan kardex por generar
		
			//verifico que no halla articulos en la temporal
			$sql = "SELECT * 
					FROM
						{$wbd}_000060
					WHERE
						kadhis = '{$pac['his']}'
						AND kading = '{$pac['ing']}'
						AND kadfec = '$fecha'
					";
					
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numrows = mysql_num_rows( $res );
			
			if( $numrows == 0 ){	//No se ha generado kardex ni esta abierto
			
				$sql = "SELECT * 
						FROM
							{$wbd}_000053
						WHERE
							karhis = '{$pac['his']}'
							AND karing = '{$pac['ing']}'
							AND fecha_data = '$ayer'
							AND kargra != 'on'
						";
					
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$numrows = mysql_num_rows( $res );
			
				if( $numrows == 0 ){
				
					//Verifico que no se halla pasado la hora de corte kardex
					$corteKardex = true; //consultarHoraCorteKardex( $conex );
				
					if( $corteKardex ){
					
						//if( true || time() < strtotime( "$fecha $corteKardex" ) ){
						if( true ){
						
							//Si la hora actual es menor a la hora corte del kardex
							
							//Creo kardex nuevo para el día actual
							$auxUsuario = $usuario;							//Se activa está línea. Junio 01 de 2015
							// $usuario = consultarUsuarioKardex($auxUsuario);
							// $usuario->esUsuarioLactario = false;
							$usuario->gruposMedicamentos = false;			//Se activa está línea. Junio 01 de 2015
							
							/*********************************************************************************************************************
							 * Junio 12 de 2012
							 *********************************************************************************************************************/
							for( ;$rowsCcos = mysql_fetch_array($resCcos); ){
							
								//Busco si el cco es de lactario
								$sql = "SELECT
											ccolac
										FROM
											{$wbd}_000011
										WHERE
											ccocod = '".trim( $rowsCcos[ 'Karcco' ] )."'
											AND ccolac = 'on'
										";
								
								$resLac = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".msyql_error() );
								$numLac = mysql_num_rows( $resLac );
								
								$ccos[ trim( $rowsCcos[ 'Karcco' ] ) ] = false;
								
								if( $numLac > 0 ){								
									$ccos[ trim( $rowsCcos[ 'Karcco' ] ) ] = true;
								}
							}
							/*********************************************************************************************************************/
							
							foreach( $ccos as $keyCcos => $valueCcos ){
								
								//Consulto si se ha generado kardex el día de hoy, es decir si tiene encabezado
								$sql = "SELECT * 
										FROM
											{$wbd}_000053
										WHERE
											karhis = '{$pac['his']}'
											AND karing = '{$pac['ing']}'
											AND fecha_data = '$fecha'
											AND karcco = '$keyCcos'
										";
										
								$resKardexHoy = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
								$numKardexHoy = mysql_num_rows( $resKardexHoy );
							
								if( $numKardexHoy == 0 ){	//Esto se hace si no se ha generado kardex
								
									$usuario->centroCostosGrabacion = $keyCcos;
									$usuario->esUsuarioLactario = $valueCcos;	//El valor del array dice si el cco es de lactario o no
									
									$paciente = consultarInfoPacienteKardex( $pac['his'], '' );
									$kardexAc = consultarKardexPorFechaPaciente( $fecha, $paciente );
									
									cargarEsquemaDextrometer( $pac['his'], $pac['ing'], $ayer, $fecha );
									
									//Dejo los articulos del día anterior en la tabla definitiva por si se quedaron en la temporal
									cargarArticulosADefinitivo( $pac['his'], $pac['ing'], $ayer, false, $keyCcos );
									cargarExamenesADefinitivo($pac['his'], $pac['ing'], $ayer);
									cargarInfusionesADefinitivo($pac['his'], $pac['ing'], $ayer);
									cargarMedicoADefinitivo($pac['his'], $pac['ing'],$ayer);
									cargarDietasADefinitivo($pac['his'], $pac['ing'],$ayer);
									
									//Cargos los datos del día anterior al actual
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "N", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "Q", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "A", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "U", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									
									cargarArticulosADefinitivo( $pac['his'], $pac['ing'], $fecha, false, $keyCcos );
									
									/************************************************************************************************
									 * Agosto 27 de 2011
									 ************************************************************************************************/
									if( $keyCcos == '*' ){	//Solo lo hace enfermería
										cargarExamenesAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarInfusionesAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarMedicosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarDietasAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										
										
										cargarExamenesADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarInfusionesADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarMedicoADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarDietasADefinitivo( $pac['his'], $pac['ing'], $fecha );
									}
									/************************************************************************************************/
									
									//Creo encabezado del kardex tal cual esta el día anterior
									$sql = "INSERT INTO
												{$wbd}_000053(Medico,Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karpal,Kardie,Karmez,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karfir,Karmeg,Karsuc,Karaut,Karord,Seguridad)
											SELECT
															  Medico,'".$fecha."','".date( "H:i:s" )."',Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karpal,Kardie,Karmez,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,karare,Karcco,Karusu,Karfir,Karmeg,Karsuc,'on',Karord,Seguridad
												FROM
													{$wbd}_000053
												WHERE
													Karhis = '{$pac['his']}'
													AND karing = '{$pac['ing']}'
													AND fecha_data = '$ayer'
													AND karcco = '$keyCcos'
											";
									
									$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
									
									if( mysql_affected_rows() > 0 ){
									
										//Dejo todos los registros del kardex como estaban antes
										$sql = "SELECT
													*
												FROM
													{$wbd}_000054
												WHERE
													kadhis = '{$pac['his']}'
													AND kading = '{$pac['ing']}'
													AND kadfec = '$fecha'
													AND kadcco = '$keyCcos'
												";
										
										$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
										$numrows = mysql_num_rows( $res );
										
										if( $numrows > 0 ){
										
											for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
											
												$sqlAnt = "SELECT
																*
															FROM
																{$wbd}_000054
															WHERE
																id = '{$rows['Kadreg']}'
															";
										
												$resAnt = mysql_query( $sqlAnt, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
												
												if( $rowsAnt = mysql_fetch_array($resAnt) ){
											
													$sqlAct = "UPDATE
																	{$wbd}_000054
																SET
																	kadare = '{$rowsAnt['Kadare']}',
																	kadcon = '{$rowsAnt['Kadcon']}'
																WHERE
																	id = '{$rows['id']}'
																";
											
													$resAct = mysql_query( $sqlAct, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );							
												}
											}
										}
									}
									
								}
							}
							
							$medicamentosControlAuto = consultarAliasPorAplicacion( $conex, $wemp_pmla, "MedicamentosControlAuto" );
				
							if( $medicamentosControlAuto == 'on' ){
								
								// $opciones = array(
								  // 'http'=>array(
									// 'method'=>"GET",
									// 'header'=>"Accept-language: en\r\n",
									// 'content'=>"user=".$user,
								  // )
								// );
								// $contexto = stream_context_create($opciones);
								// $url = 'http://'.$_SERVER['HTTP_HOST'];
 								// $varGet = file_get_contents( $url."/matrix/movhos/procesos/impresionMedicamentosControl.php?wemp_pmla=".$wemp_pmla."&historia=".$pac['his']."&ingreso=".$pac['ing']."&fechaKardex=".$fecha."&consultaAjax=10", false, $contexto );
								$url .= "/matrix/movhos/procesos/impresionMedicamentosControl.php?wemp_pmla=".$wemp_pmla."&historia=".$pac['his']."&ingreso=".$pac['ing']."&fechaKardex=".$fecha."&consultaAjax=10";
								?>
								<script>
									try{
										$.post("<?= $url ?>",function(data){})
									}
									catch(e){}
								</script>
								<?php
							}
							
							$usuario = $auxUsuario;
						}
						else{
							return false;
						}
					}
					else{
						return false;
					}
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
}


function consultarPacientes( $conex, $wemp_pmla, $wbasedato, $all, $fecha = '' ){
	
	if( empty( $fecha ) ){
		$fecha = date("Y-m-d");
	}
	
	$val = false;

	if( !$all ){
		$sql = "SELECT Ubihis, Ubiing, Ubisac, Ubihan
				  FROM ".$wbasedato."_000018 a, ".$wbasedato."_000011 b
				 WHERE a.ubiald = 'off'
				   AND a.ubisac = b.ccocod
				   AND b.ccodom = 'on'
				 ";
	}
	else{
		$sql = "SELECT Ubihis, Ubiing, Ubisac, Ubihan
				  FROM ".$wbasedato."_000018 a, ".$wbasedato."_000011 b
				 WHERE a.ubiald = 'off'
				   AND a.ubisac = b.ccocod
				 ";
	}
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );
	
	while( $rows = mysql_fetch_array( $res ) ){
		crearKardexAutomaticamente( $conex, $wbasedato, $rows['Ubihis'], $rows['Ubiing'], date("Y-m-d") );
	}
	
	return $val;
}

include_once("conex.php");
include_once("movhos/kardex.inc.php");

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos' );

$wuser = 'movhos';

$usuario = consultarUsuarioKardex($wuser);

if(!isset($fecha) )
	$fecha = '';

consultarPacientes( $conex, $wemp_pmla, $wbasedato, isset($todos), $fecha );

