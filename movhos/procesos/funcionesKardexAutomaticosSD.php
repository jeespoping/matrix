<?php

/* *
* Archivo de funciones para generar Kardex automáticamente para Servicio Domiciliario
* luego de haber ejecutado scripts para egresar automáticamente y para admisiones automáticas.
* @author: Julian Mejia - julian.mejia@lasamericas.com.co
*/
ob_start();
/**************************************
 * INCLUDES
 **************************************/
include_once("conex.php"); //Conexion a la BD
include_once("root/comun.php"); // fn consultarAliasPorAplicacion

/*******************
 * INICIO FUNCIONES
 *******************/
ob_end_clean();
 
/**
 * Esta funcion consulta los pacientes en el log generado por los scripts de egreso y admision automatica
 * como parametros recibe conexion BD, el prefijo de la tabla y $mostrarConteo para saber si es solo
 * devolver el conteo o si es replicar las ordenes
 */

function consultarPacientes( $conex, $wbasedato, $mostrarConteo){
	global $wemp_pmla;
	$datos = array();
	$datos['finalizados'] = []; // array que devuelve las ordenes replicadas exitosamente
	$datos['fallidos'] = []; // array que devuelve los fallos si los hay
	$totalOrdenes = 0;
	$replico = false;
	$bdLog = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame' );
	if( empty( $fecha ) ){
		$fecha = date("Y-m-d");
	}
	/** Consulta el Nro de ordenes totales a replicar */
	$totalOrdenes = consultarOrdenesTotales($conex, $wbasedato,$bdLog);
	if (!$mostrarConteo){
		if ($totalOrdenes == 0){ //si no hay ordenes, se actualiza la tabla de logs
			actualizarLogsTotales($conex,$bdLog);
			return $datos;
		} else { 
			/**Consulta en la tabla de logs a qué historias se les debe hacer el proceso */
			$query = " SELECT 
							historia, ingreso, tipo_documento, documento_paciente, paciente, replicado_ordenes
						FROM 
							{$bdLog}_000343 
						WHERE egresado IS NOT NULL 
						AND   ingresado IS NOT NULL 
						AND   replicado_ordenes IS NULL";
			
			$res = mysql_query( $query, $conex ) or ($descripcion = utf8_encode(mysql_errno() . " -consultando la tabla de logs " . mysql_error()));
			if ($descripcion){ 
				$datos['fallidos'] = array('historia' => '','ingreso' => '','tipo_documento' => '', 'documento' => '','paciente' => '' ,'descripcion' => $descripcion);										
			} 
			while( $rows = mysql_fetch_array( $res ) ){
				$replico = false;
				// Se consulta la funcion que replica las ordenes y recibe un array de datos finalizados o fallidos
				$datosTmp = crearKardexAutomaticamente( $conex, $wbasedato, $rows['historia'], $rows['ingreso'], $fecha, $rows['documento_paciente']);

					if (!empty($datosTmp)){
						if (isset($datosTmp['descripcion'])){
							if ($datosTmp['descripcion'] != 'No hay encabezado de Kardex'){
								$datosTmp['tipo_documento'] = $rows['tipo_documento'];
								$datosTmp['paciente'] = $rows['paciente'];
								array_push($datos['fallidos'],$datosTmp);
								devolverCambios($rows['historia'],$rows['ingreso']); //Si hay un error, se devuelven los cambios hechos
							}else {
								actualizarTablaLog($conex, $bdLog, $rows['historia'], $rows['ingreso'],$replico); //si fue exitoso, se actualiza la tabla de logs
							}
						}
					}else{
						$replico = true;
						$ingN = $rows['ingreso'] + 1;
						$url = "/matrix/hce/procesos/ordenes.php?wemp_pmla={$wemp_pmla}&wcedula={$rows['documento_paciente']}&&wtipodoc={$rows['tipo_documento']}&hce=on"; //URL para consultar en caso de exito
						//$url = "/matrix/hce/procesos/ordenes.php?wemp_pmla={$wemp_pmla}&whistoria={$rows['historia']}&&wingreso={$ingN}&hce=on";
						actualizarTablaLog($conex, $bdLog, $rows['historia'], $rows['ingreso'],$replico); //si fue exitoso, se actualiza la tabla de logs
						array_push($datos['finalizados'],array('historia' => $rows['historia'],'ingreso' => $ingN,'tipo_documento' => $rows['tipo_documento'], 'documento' => $rows['documento_paciente'], 'paciente' => $rows['paciente'],'descripcion' => 'Se realizó la replicación de las Ordenes correctamente', 'url' => $url));
					} 
			}
			
		}
	}else{ // Si es mostrar conteo solo devuelve el numero de ordenes a replicar
		return $totalOrdenes;
	}
	return $datos;
}
	
	
/**
 * Funcion que devuleve los cambios si hubo fallos para una historia y el ingreso nuevo
 */

 function devolverCambios($his, $ing){
	global $wbasedato;
	global $conex;
	$ingNuevo = $ing + 1;
	$arrayDelete = array("DELETE FROM {$wbasedato}_000070 WHERE Infhis = '{$his}' AND Infing = '{$ingNuevo}' AND inffec = CURRENT_DATE;",
					"DELETE FROM {$wbasedato}_000071 WHERE Indhis = '{$his}' AND Inding = '{$ingNuevo}' AND indfec = CURRENT_DATE;",
					"DELETE FROM {$wbasedato}_000054 WHERE Kadhis = '{$his}' AND Kading = '{$ingNuevo}' AND Kadfec = CURRENT_DATE;",
					"DELETE FROM {$wbasedato}_000050 WHERE Ekahis = '{$his}' AND Ekaing = '{$ingNuevo}' AND Ekafec = CURRENT_DATE;",
					"DELETE FROM {$wbasedato}_000051 WHERE Inkhis = '{$his}' AND Inking = '{$ingNuevo}' AND Inkfec = CURRENT_DATE;",
					"DELETE FROM {$wbasedato}_000047 WHERE Methis = '{$his}' AND Meting = '{$ingNuevo}' AND Metfek = CURRENT_DATE;",
					"DELETE FROM {$wbasedato}_000052 WHERE Dikhis = '{$his}' AND Diking = '{$ingNuevo}' AND Dikfec = CURRENT_DATE;",
					"DELETE FROM {$wbasedato}_000208 WHERE Ekxhis = '{$his}' AND Ekxing = '{$ingNuevo}' AND Ekxfec = CURRENT_DATE;",
					"DELETE FROM {$wbasedato}_000053 WHERE Karhis = '{$his}' AND Karing = '{$ingNuevo}' AND fecha_data = CURRENT_DATE;");
	for ($i = 0; $i < count($arrayDelete); $i++){
		$res = mysql_query( $arrayDelete[$i], $conex ) or ($descripcion = utf8_encode(mysql_errno() . " - Error insertando la tabla de kardex definitivo - " . mysql_error()));
	}
	rollBackOrdenesHCEAuditoria($his, $ing);
 }

 /**
  * Funcion que hace rollback para una historia y un ingreso en ordenes pendientes tabla de auditoria hce 27
  * y movhos 55
  */
 function rollBackOrdenesHCEAuditoria($his, $ing){
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	$wbd_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce' );
	$ingNuevo = $ing + 1;
	$sqlUpdate = "UPDATE {$wbd_hce}_000027 SET Ording = '{$ing}' WHERE Ordhis = '{$his}' AND Ording = '{$ingNuevo}';";
	$resUpdate = mysql_query( $sqlUpdate, $conex ) or ($descripcion = utf8_encode(mysql_errno() . " - Error haciendo rollback - " . mysql_error()));
	if (mysql_affected_rows($conex) > 0){
		 $sql = "DELETE FROM {$wbasedato}_000055 WHERE Kauhis = '{$his}' AND Kauing = '{$ing}' AND fecha_data = CURRENT_DATE;";
		 $res = mysql_query( $sql, $conex ) or ($descripcion = utf8_encode(mysql_errno() . " - Error haciendo rollback - " . mysql_error()));
	}

 }

/**
 * Funcion que setea los valores de replicado ordenes si el conteo es cero de  las historias a replicar
 */

 function actualizarLogsTotales($conex,$bdLog){
	$query = " SELECT 
				historia, ingreso
			FROM 
				{$bdLog}_000343 
			WHERE replicado_ordenes IS NULL
			AND   ingresado IS NOT NULL 
			AND   egresado IS NOT NULL 
			AND fecha_creado = CURRENT_DATE";
			//egresado IS NOT NULL 
			// AND   ingresado IS NOT NULL 
			// AND  
			$res = mysql_query( $query) or ($descripcion = utf8_encode(mysql_errno() . " -consultando la tabla de logs " . mysql_error()));
			while( $rows = mysql_fetch_array( $res ) ){
				$q = "UPDATE {$bdLog}_000343
				SET replicado_ordenes = '1'				
				WHERE 
				historia = '{$rows['historia']}'
				AND ingreso = '{$rows['ingreso']}'
				";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			}
 }

/**
 * Funcion que consulta el total de ordenes que se van a procesar por el programa
 */

 function consultarOrdenesTotales($conex, $wbasedato,$bdLog){
	$queryTotalOrdenes = "SELECT 
								{$wbasedato}_000053.Karhis 
								FROM {$wbasedato}_000053 
								INNER JOIN {$bdLog}_000343
								ON ({$wbasedato}_000053.Karhis = {$bdLog}_000343.historia 
								AND {$wbasedato}_000053.Karing = {$bdLog}_000343.ingreso)
								WHERE
								{$bdLog}_000343.egresado IS NOT NULL 
								AND {$bdLog}_000343.ingresado IS NOT NULL 
								AND {$bdLog}_000343.replicado_ordenes IS NULL
								GROUP by {$wbasedato}_000053.Karhis";
	$res = mysql_query( $queryTotalOrdenes, $conex ) or die( mysql_errno(). " - Error en el query $queryTotalOrdenes - ". mysql_error());
	return mysql_num_rows($res);
 }

 /**
  * Funcion que Actualiza la tabla de logs cuando se hizo la replicacion del Kardex exitosamente
  * El estado de las ordenes varia, si es 1 es porque no existe encabezado de kardex (movhos 53)
  * si es 2 es porque se replico esa orden ya que existian ordenes para esa historia e ingreso
  */

  function actualizarTablaLog($conex, $bdLog, $his, $ing, $replico){
	$estadoOrdenes = $replico ? 2 : 1;
	$q = "UPDATE {$bdLog}_000343
				SET replicado_ordenes = {$estadoOrdenes}				
				WHERE 
				historia = '$his'
				AND ingreso = '$ing'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
  }

/**
 * 
 * Funcion principal que llama a todas las funciones que están implicadas en las ordenes
 * AL final replica en encabezado del kardex de la 53
 * 
 */

function crearKardexAutomaticamente( $conex, $wbd, $his, $ing, $fecha, $pacienteIni){
	global $wbasedato;
	global $usuario;
	global $wemp_pmla;
	$datosMostrar = [];
	$datosMostrartmp = [];
	$wbasedato = $wbd;
	$pac[ 'his' ] = $his;
	$pac[ 'ing' ] = $ing;

	//Obtengo la fehca del día anterior
	$ayer = date( "Y-m-d", strtotime( $fecha." 00:00:00" ) - 24*3600 );
	
	//consulto la ultima fecha data en el encabezado para revisar en las demas tablas del kardex
	$sql = "SELECT Fecha_data, Karcco
			FROM
				{$wbd}_000053
			WHERE
				karhis = '{$pac['his']}'
				AND karing = '{$pac['ing']}'
				AND fecha_data = 
				(SELECT MAX(fecha_data) FROM {$wbd}_000053 WHERE Karhis = '{$pac['his']}' AND Karing = '{$pac['ing']}')
			";

	$resCcos = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	$numCcos = mysql_num_rows( $resCcos );
    
	if( $numCcos > 0 ){	//Indica que hay Kardex en el encabezado por lo que se debe replicar automaticamente
	
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
		
			$auxUsuario = $usuario;	// se deja temporal por si se debe consultar						
			// $usuario = consultarUsuarioKardex($auxUsuario);
			// $usuario->esUsuarioLactario = false;
			$usuario->gruposMedicamentos = false;	// se deja temporal por si se debe consultar
			
			while( $rowsCcos = mysql_fetch_array( $resCcos ) ){

				$ultimaFechaData = $rowsCcos[ 'Fecha_data' ];
				$datosMostrartmp = replicarEsquemaDextrometer( $pac['his'], $pac['ing'], $fecha, $ultimaFechaData); //Replica esquema Dextrometer
				$datosMostrartmp = replicarExamenesADefinitivo($pac['his'], $pac['ing'], $fecha, $ultimaFechaData); // Replica Examenes en tabla definitiva
				$datosMostrartmp = replicarInfusionesADefinitivo($pac['his'], $pac['ing'], $fecha, $ultimaFechaData); // Replica infusiones en tabla definitiva
				$datosMostrartmp = replicarMedicoADefinitivo($pac['his'], $pac['ing'],$fecha, $ultimaFechaData); // Replica Medicos definitivo
				$datosMostrartmp = replicarDietasADefinitivo($pac['his'], $pac['ing'],$fecha, $ultimaFechaData); // Replica Dietas
				$datosMostrartmp = cargarOrdenesHCEADefinitivoSinTemporal($pac['his'], $pac['ing']); // Actualiza tabla de ordenes hce cuando una orden sigue pendiente
				$datosMostrartmp = replicarArticulosADefinitivo( $pac['his'], $pac['ing'], $fecha, $ultimaFechaData); // Replica Kardex Definitivo
				$datosMostrartmp = replicarKardexARespaldo( $pac['his'], $pac['ing'], $fecha, $ultimaFechaData); // replica tabla de kardex de respaldo
				if(!empty($datosMostrartmp)){
					$datosMostrartmp['documento'] = $pacienteIni;
					$datosMostrar = $datosMostrartmp;
				}

				// Se arma un objeto con los datos necesarios para la replicacion, en este caso el encabezado de kardex
				$ingresoNuevo = $pac['ing'] + 1;
				$objParams = new datosReplicar();
				$objParams->nombreTabla = '000053';
				$arrayColumnas = consultarColumnasTabla($conex,$wbd,$objParams->nombreTabla);
				array_pop($arrayColumnas);
				$objParams->camposTablaInsert = implode(",", $arrayColumnas);
				$fieldsReplace = array("Fecha_data","Hora_data","Karing");
				$horaData = date( "H:i:s" );
				$newFields = array("'{$fecha}'","'{$horaData}'","'{$ingresoNuevo}'");
				$objParams->camposTablaSelect = str_replace($fieldsReplace, $newFields, $objParams->camposTablaInsert);	
				$objParams->adicionalQuery = "Karhis = '{$pac['his']}'
												AND karing = '{$pac['ing']}'
												AND fecha_data = '{$ultimaFechaData}' ";
				$objParams->error = ' - Error insertando la tabla de kardex definitivo - ';
				$descripcion = replicarOrdenesAutomaticamente($objParams);
				if ($descripcion) $datosMostrar = array('historia' => $pac['his'],'ingreso' => $pac['ing'], 'documento' => $pacienteIni, 'descripcion' => $descripcion);
					
				break;
			}
			
			// se comenta esta funcion debido a la cantidad de errores que genera
			/* $medicamentosControlAuto = consultarAliasPorAplicacion( $conex, $wemp_pmla, "MedicamentosControlAuto" );
			if( $medicamentosControlAuto == 'on' ){
				$url .= "/matrix/movhos/procesos/impresionMedicamentosControl.php?wemp_pmla=".$wemp_pmla."&historia=".$pac['his']."&ingreso=".$pac['ing']."&fechaKardex=".$fecha."&consultaAjax=10";
				?>
				<script>
					try{
						$.post("<?= $url ?>",function(data){})
					}
					catch(e){}
				</script>
				<?php
			}*/
			
			$usuario = $auxUsuario;

		} else {
			if(empty($datosMostrar))$datosMostrar = array('historia' => $his,'ingreso' => $ing, 'documento' => $pacienteIni,  'descripcion' => 'Esta Abierto el Kardex en la temporal');
			return $datosMostrar;
		}

	} else {	 
		 //array_push($data,array('Historia' => $his,'Ingreso' => $ing, 'Descripcion' => 'No hay encabezado de Kardex'));
		 $datosMostrar = array('historia' => $his,'ingreso' => $ing, 'documento' => $pacienteIni,  'descripcion' => 'No hay encabezado de Kardex');
		 return $datosMostrar;
	}
	return $datosMostrar;
}

/**
 * Replica Kardex definitivo forma simple, consultando los nombres de los campos y haciendo el insert
 */
function replicarArticulosADefinitivo( $historia, $ingreso, $fechaActual, $ultimaFechaData){
	global $wbasedato;
	global $conex;
	$ingresoNuevo = $ingreso +1;
	$objParams = new datosReplicar();
	$objParams->nombreTabla = '000054';
	$arrayColumnas = consultarColumnasTabla($conex,$wbasedato,$objParams->nombreTabla);
	array_pop($arrayColumnas);
	$objParams->camposTablaInsert = implode(",", $arrayColumnas);
	$fieldsReplace = array("Kading", "Kadfec");
	$newFields = array("'{$ingresoNuevo}'", "'{$fechaActual}'");
	$objParams->camposTablaSelect = str_replace($fieldsReplace, $newFields, $objParams->camposTablaInsert);	
	$objParams->adicionalQuery = "Kadhis = '$historia'
									AND Kading = '$ingreso'
									AND Kadfec = '$ultimaFechaData'";
	$objParams->error = ' - Error insertando la tabla de kardex definitivo - ';
	$descripcion = replicarOrdenesAutomaticamente($objParams);
	if ($descripcion) return array('historia' => $historia,'ingreso' => $ingreso, 'descripcion' => $descripcion);
}

/**
 * Se Replican los Kardex en la tabla 208 (que es la tabla de respaldo)
 */
function replicarKardexARespaldo( $historia, $ingreso, $fechaActual, $ultimaFechaData){
	global $wbasedato;
	global $conex;
	$ingresoNuevo = $ingreso +1;
	$objParams = new datosReplicar();
	$objParams->nombreTabla = '000208';
	//$objParams->camposTablaInsert = 'Medico,Fecha_data,Hora_data,Ekxhis,Ekxing,Ekxfec,Ekxart,Ekxido,Ekxest,Ekxpro,Ekxtra,Ekxped,Ekxin1,Ekxin2,Ekxayu,Seguridad';
	//$objParams->camposTablaSelect = "Medico,Fecha_data,Hora_data,Ekxhis,'{$ingresoNuevo}','{$fechaActual}',Ekxart,Ekxido,Ekxest,Ekxpro,Ekxtra,Ekxped,Ekxin1,Ekxin2,Ekxayu,Seguridad";
	$arrayColumnas = consultarColumnasTabla($conex,$wbasedato,$objParams->nombreTabla);
	array_pop($arrayColumnas);
	$objParams->camposTablaInsert = implode(",", $arrayColumnas);
	$fieldsReplace = array("Ekxing", "Ekxfec");
	$newFields = array("'{$ingresoNuevo}'", "'{$fechaActual}'");
	$objParams->camposTablaSelect = str_replace($fieldsReplace, $newFields, $objParams->camposTablaInsert);	
	$objParams->adicionalQuery = "Ekxhis = '$historia'
									AND Ekxing = '$ingreso'
									AND Ekxfec = '$ultimaFechaData'";
	$objParams->error = ' - Error insertando la tabla de Encabezado de Kardex Respaldo - ';
	$descripcion = replicarOrdenesAutomaticamente($objParams);
	if ($descripcion) return array('historia' => $historia,'ingreso' => $ingreso, 'descripcion' => $descripcion);
}


/**
 * Funcion que replica la tabla de examenes si la fecha coincide con la fecha_data del encabezado kardex
 */
function replicarExamenesADefinitivo($historia,$ingreso,$fecha, $ultimaFechaData){
	global $wbasedato;
	global $conex;
	$ingresoNuevo = $ingreso + 1;
	$objParams = new datosReplicar();
	$objParams->nombreTabla = '000050';
	$arrayColumnas = consultarColumnasTabla($conex,$wbasedato,$objParams->nombreTabla);
	array_pop($arrayColumnas);
	$objParams->camposTablaInsert = implode(",", $arrayColumnas);
	$fieldsReplace = array("Ekaing", "Ekafec");
	$newFields = array("'{$ingresoNuevo}'", "'{$fecha}'");
	$objParams->camposTablaSelect = str_replace($fieldsReplace, $newFields, $objParams->camposTablaInsert);	
	//$objParams->camposTablaInsert = 'Medico,Fecha_data,Hora_data,Ekahis,Ekaing,Ekacod,Ekaest,Ekafec,Ekaobs,Ekafes,Ekafmo,seguridad';
	//$objParams->camposTablaSelect = "Medico,Fecha_data,Hora_data,Ekahis,'{$ingresoNuevo}',Ekacod,Ekaest,'{$fecha}',Ekaobs,Ekafes,Ekafmo,seguridad";
	$objParams->adicionalQuery = "Ekahis = '$historia'
								AND Ekaing = '$ingreso'
								AND Ekafec = '$ultimaFechaData'";
	$objParams->error = ' - Error insertando la tabla de examenes - ';
	
	$descripcion = replicarOrdenesAutomaticamente($objParams);
	if ($descripcion) return array('historia' => $historia,'ingreso' => $ingreso, 'descripcion' => $descripcion);
}

/**
 * Funcion que replica las infusiones si la fecha coincide con la fecha_data del encabezado kardex
 */
function replicarInfusionesADefinitivo($historia,$ingreso,$fecha, $ultimaFechaData){
	global $wbasedato;
	global $conex;
	$ingresoNuevo = $ingreso + 1;
	$objParams = new datosReplicar();
	$objParams->nombreTabla = '000051';
	$arrayColumnas = consultarColumnasTabla($conex,$wbasedato,$objParams->nombreTabla);
	array_pop($arrayColumnas);
	$objParams->camposTablaInsert = implode(",", $arrayColumnas);
	$fieldsReplace = array("Inking", "Inkfec");
	$newFields = array("'{$ingresoNuevo}'", "'{$fecha}'");
	$objParams->camposTablaSelect = str_replace($fieldsReplace, $newFields, $objParams->camposTablaInsert);	
	//$objParams->camposTablaInsert = 'Medico,Fecha_data,hora_data,Inkhis,Inking,Inkcon,Inkdes,Inkfec,Inkobs,Inkfes,seguridad';
	//$objParams->camposTablaSelect = "Medico,Fecha_data,hora_data,Inkhis,'{$ingresoNuevo}',Inkcon,Inkdes,'{$fecha}',Inkobs,Inkfes,seguridad";
	$objParams->adicionalQuery = "Inkhis = '$historia'
									AND Inking = '$ingreso'
									AND Inkfec = '$ultimaFechaData'";
	$objParams->error = ' - Error insertando la tabla de infusiones - ';
	$descripcion = replicarOrdenesAutomaticamente($objParams);
	if ($descripcion) return array('historia' => $historia,'ingreso' => $ingreso, 'descripcion' => $descripcion);
}

/**
 * Funcion que replica los campos de medicos si la fecha coincide con la fecha_data del encabezado kardex
 */
function replicarMedicoADefinitivo($historia,$ingreso,$fecha, $ultimaFechaData){
	global $wbasedato;
	global $conex;
	$ingresoNuevo = $ingreso + 1;
	$objParams = new datosReplicar();
	$objParams->nombreTabla = '000047';
	$arrayColumnas = consultarColumnasTabla($conex,$wbasedato,$objParams->nombreTabla);
	array_pop($arrayColumnas);
	$objParams->camposTablaInsert = implode(",", $arrayColumnas);
	$fieldsReplace = array("Meting", "Metfek");
	$newFields = array("'{$ingresoNuevo}'", "'{$fecha}'");
	$objParams->camposTablaSelect = str_replace($fieldsReplace, $newFields, $objParams->camposTablaInsert);	
	//$objParams->camposTablaInsert = 'Medico,Fecha_data,hora_data,Mettdo,Metdoc,Methis,Meting,Metfek,Metest,Metint,Metesp,seguridad';
	//$objParams->camposTablaSelect = "Medico,Fecha_data,hora_data,Mettdo,Metdoc,Methis,'{$ingresoNuevo}','{$fecha}',Metest,Metint,Metesp,seguridad";
	$objParams->adicionalQuery = "Methis = '$historia'
									AND Meting = '$ingreso'
									AND Metfek = '$ultimaFechaData'";
	$objParams->error = ' - Error insertando la tabla de Medicos - ';
	$descripcion = replicarOrdenesAutomaticamente($objParams);
	if ($descripcion) return array('historia' => $historia,'ingreso' => $ingreso, 'descripcion' => $descripcion);
}

/**
 * Funcion que replica Dietas a definitivo si la fecha coincide con la fecha_data del encabezado kardex
 */

function replicarDietasADefinitivo($historia,$ingreso,$fecha, $ultimaFechaData){
	global $wbasedato;
	global $conex;
	$ingresoNuevo = $ingreso + 1;
	$objParams = new datosReplicar();
	$objParams->nombreTabla = '000052';
	$arrayColumnas = consultarColumnasTabla($conex,$wbasedato,$objParams->nombreTabla);
	array_pop($arrayColumnas);
	$objParams->camposTablaInsert = implode(",", $arrayColumnas);
	$fieldsReplace = array("Diking", "Dikfec");
	$newFields = array("'{$ingresoNuevo}'", "'{$fecha}'");
	$objParams->camposTablaSelect = str_replace($fieldsReplace, $newFields, $objParams->camposTablaInsert);	
	//$objParams->camposTablaInsert = 'Medico,Fecha_data,hora_data,Dikcod,Dikhis,Diking,Dikfec,Dikest,seguridad';
	//$objParams->camposTablaSelect = "Medico,Fecha_data,hora_data,Dikcod,Dikhis,'{$ingresoNuevo}','{$fecha}',Dikest,seguridad";
	$objParams->adicionalQuery = "Dikhis = '$historia'
									AND Diking = '$ingreso'
									AND Dikfec = '$ultimaFechaData'";
	$objParams->error = ' - Error insertando la tabla de Dietas - ';
	$descripcion = replicarOrdenesAutomaticamente($objParams);
	if ($descripcion) return array('historia' => $historia,'ingreso' => $ingreso, 'descripcion' => $descripcion);
}

/**
 * Funcion que replica los esquemas Dextrometers si la fecha coincide con la fecha_data del encabezado kardex
 */

function replicarEsquemaDextrometer($historia, $ingreso, $fechaActual, $ultimaFechaData){
	global $wbasedato;
	global $conex;
    $ingresoNuevo = $ingreso + 1;
	//Infhis  Infing  Inffec  Infade  Inffde  Infcde
	$q = "SELECT
			Medico,Fecha_data,Hora_data,Infade,Inffde,Infcde,Seguridad,id
		FROM
			".$wbasedato."_000070
		WHERE
			Infhis = '$historia'
			AND Infing = '$ingreso'
			AND Infade <> ''
            AND  Inffec = '$ultimaFechaData' ";
          //  (SELECT MAX(Inffec) FROM ".$wbasedato."_000070 WHERE Infhis = '$historia' AND Infing = '$ingreso' AND Infade <> '') "; //preguntar si es necesario infade

 	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

   if ($num > 0){ //Quiere decir que hay datos en la tabla para replicar
        while( $info = mysql_fetch_array( $res ) ){
                $q = "INSERT INTO ".$wbasedato."_000070
                    (Medico,Fecha_data,Hora_data,Infhis,Infing,Inffec,Infade,Inffde,Infcde,Seguridad)
                VALUES
                ('{$info['Medico']}','{$info['Fecha_data']}','{$info['Hora_data']}','{$historia}','{$ingresoNuevo}','{$fechaActual}','{$info['Infade']}','{$info['Inffde']}','{$info['Infcde']}','{$info['Seguridad']}')";
            
            //$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
            $res2 = mysql_query($q, $conex)  or ($descripcion = utf8_encode(mysql_errno() . " - Error insertando la tabla de dextrometer - " . mysql_error()));
        }
		

    }
    
    //Carga de los intervalos dextrometer
	//Medico  Fecha_data  Hora_data  Indhis  Inding  Indfec  Indime  Indima  Inddos  Indudo  Indobs  Indvia  Seguridad
	
	$objParams = new datosReplicar();
	$objParams->nombreTabla = '000071';
	$arrayColumnas = consultarColumnasTabla($conex,$wbasedato,$objParams->nombreTabla);
	array_pop($arrayColumnas);
	$objParams->camposTablaInsert = implode(",", $arrayColumnas);
	$fieldsReplace = array("Inding", "Indfec");
	$newFields = array("'{$ingresoNuevo}'", "'{$fechaActual}'");
	$objParams->camposTablaSelect = str_replace($fieldsReplace, $newFields, $objParams->camposTablaInsert);	
	//$objParams->camposTablaInsert = 'Medico,Fecha_data,Hora_data,Indhis,Inding,Indfec,Indime,Indima,Inddos,Indudo,Indobs,Indvia,Seguridad';
	//$objParams->camposTablaSelect = "Medico,Fecha_data,Hora_data,Indhis,'{$ingresoNuevo}','{$fechaActual}',Indime,Indima,Inddos,Indudo,Indobs,Indvia,Seguridad";
	$objParams->adicionalQuery = "Indhis = '$historia'
									AND Inding = '$ingreso'
									AND Indime IS NOT NULL
									AND Indime != '' 
									AND Indime != ' '
									AND Indfec = '$ultimaFechaData'";
	$objParams->error = ' - Error insertando la tabla de dextrometer - ';
	$descripcion = replicarOrdenesAutomaticamente($objParams);
	if ($descripcion) return array('historia' => $historia,'ingreso' => $ingreso, 'descripcion' => $descripcion);
}

// se deja como ejemplo para los campos quemados
function cargarMedicoADefinitivoSinTemporal($historia,$ingreso,$fecha, $ultimaFechaData){
	global $wbasedato;
	global $conex;
	$ingresoNuevo = $ingreso + 1;
	$objParams = new datosReplicar();
	$objParams->nombreTabla = '000047';
	$objParams->camposTablaInsert = 'Medico,Fecha_data,hora_data,Mettdo,Metdoc,Methis,Meting,Metfek,Metest,Metint,Metesp,seguridad';
	$objParams->camposTablaSelect = "Medico,Fecha_data,hora_data,Mettdo,Metdoc,Methis,'{$ingresoNuevo}','{$fecha}',Metest,Metint,Metesp,seguridad";
	$objParams->adicionalQuery = "Methis = '$historia'
									AND Meting = '$ingreso'
									AND Metfek = '$ultimaFechaData'";
	$objParams->error = ' - Error insertando la tabla de Medicos - ';
	$descripcion = replicarOrdenesAutomaticamente($objParams);
	if ($descripcion) return array('historia' => $historia,'ingreso' => $ingreso, 'descripcion' => $descripcion);
}

/**
 * Esta funcion replica los datos de la tabla hce 27 con ordenes pendientes
 * es una orden que sigue activa para el nuevo ingreso
 */
function cargarOrdenesHCEADefinitivoSinTemporal($historia,$ingreso){
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	$wbd_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce' );
	$ingresoNuevo = $ingreso + 1;
	$q = " SELECT Ordfec, Ordnro, Ordtor
				FROM 
					{$wbd_hce}_000027
				WHERE 
					Ordhis = '$historia'
					AND Ording = '$ingreso'
	";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0){
		while( $info = mysql_fetch_array( $res ) ){
		
		$q1 = " SELECT Detesi 
						FROM 
							{$wbd_hce}_000028 
						WHERE 
							Detnro = '{$info['Ordnro']}' 
							AND dettor = '{$info['Ordtor']}'
				";
			$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
			$num1 = mysql_num_rows($res1);
			if($num1 >0 ){
				while( $info1 = mysql_fetch_array( $res1 ) ){
					$q2 = " SELECT Eexpen
								FROM
									{$wbasedato}_000045
								WHERE 
									Eexcod = '{$info1['Detesi']}'
					";
					$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
					$num2 = mysql_num_rows($res2);
					if ($num2 > 0){
						while( $info2 = mysql_fetch_array( $res2 ) ){
							if ($info2['Eexpen'] == 'on'){ //si tiene ordenes pendientes, se hace la actualizacion para el ingreso nuevo
								$sql = "UPDATE {$wbd_hce}_000027
										SET Ording = '{$ingresoNuevo}'				
										WHERE 
										Ordnro = '{$info['Ordnro']}'
										AND Ordtor = '{$info['Ordtor']}'
									";
								$res_sql = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
								//poner el affected rows
								$texto = "Es una orden que sigue activa para el nuevo ingreso ". $info['Ordtor'] . "-" . $info['Ordnro'];
								$resumen = "Orden Activa Nuevo Ingreso";
								registrarAuditoriaNuevoIngresoKardex($historia,$ingreso,$info['Ordtor'],$info['Ordnro'], $info['Ordfec'],$texto,$resumen); //Preguntar que ingreso es?
							}
						}
					}
				}
			}
		}
	}
}


/**
 * Funcion para registrar auditoria en ordenes cada que actualizo el ingreso en hce 27
 */

function registrarAuditoriaNuevoIngresoKardex($his, $ing ,$tipoOrden,$NroOrden, $fechaOrden,$texto = '',$resumen = ''){
	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	$texto = consultarAliasPorAplicacion($conex, $wemp_pmla, 'TextoAuditoriaOrdernesAutSD' );
	$texto .= " {$tipoOrden}-{$NroOrden}";
	$hora = date( "H:i:s" );
	$fecha = date( "Y-m-d" );
	if (isset($_SESSION['user'])) {
		$user = $_SESSION['user'];
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	}
	$seguridad = "A-$wuser";
	$sql = "INSERT INTO {$wbasedato}_000055
					(Medico,Fecha_data,Hora_data,Kauhis,Kauing,Kaufec,Kaudes,Kaumen,Seguridad)
			 VALUES( 'movhos','$fecha', '$hora', '$his', '$ing', '$fechaOrden','$texto', '$resumen', '$seguridad' )";
	
	$res = mysql_query( $sql, $conex ) or ($descripcion = utf8_encode(mysql_errno() . " - Error insertando en auditorias Kárdex - " . mysql_error()));
	
	return true;
}

 /**
  * consulta los datos del usuario kardex
  */
  function consultarUsuarioKardex($codigo, $wbasedato){
	global $conex;
	global $wemp_pmla;

	global $centroCostosServicioFarmaceutico;
	global $centroCostosCentralMezclas;
	
	//$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$q = "SELECT
				Codigo,Password,Passdel,Feccap,Tablas,Descripcion,Prioridad,Grupo,Empresa,Cc,Cconom,Ccohos,Ccogka,Ccopek,Ccouct,Ccolac,Ccocir,Ccoing,Ccourg
			FROM
				usuarios, root_000025 LEFT JOIN ".$wbasedato."_000011 ON Cc = Ccocod
			WHERE
				Codigo = '".$codigo."'
				AND Empleado = Codigo
				AND Activo = 'A'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$consulta = new UsuarioKardex();

	$cont1 = 0;

	if($num > 0){
		$rs = mysql_fetch_array($res);

		$consulta->codigo = $rs['Codigo'];
		$consulta->descripcion = $rs['Descripcion'];
		$consulta->empresa = $rs['Empresa'];
		$consulta->centroCostos = $rs['Cc'];

		//Nombre del centro de costos
		if(isset($rs['Cconom']) && !empty($rs['Cconom'])){
			$consulta->nombreCentroCostos = $rs['Cconom'];
		} else {
			$consulta->nombreCentroCostos = "";
		}

		//Es hospitalario o no
		// if(isset($rs['Ccohos']) && !empty($rs['Ccohos']) && ($rs['Ccohos'] == 'on' || $rs['Ccocir'] == 'on'  || $rs['Ccourg'] == 'on' )){	//Abril 1 de 2013. Si el servicio es de urgencia también se debe grabar con *
		if( !empty($rs['Ccolac']) && ($rs['Ccolac'] != 'on') ){	//Abril 1 de 2013. Si el servicio es de urgencia también se debe grabar con *
			$consulta->centroCostosHospitalario = true;
			$consulta->centroCostosGrabacion = "*";
		} else {
			$consulta->centroCostosHospitalario = false;
			$consulta->centroCostosGrabacion = $consulta->centroCostos;
		}
		
//		var $esCcoUrgencias = "";
//		var $esCcoCirugia = "";
//		var $esCcoIngreso = "";
		
		if( isset($rs['Ccocir']) && $rs['Ccocir'] == 'on' ){
			$consulta->esCcoCirugia = true;
		}
		
		if( isset($rs['Ccoing']) && $rs['Ccoing'] == 'on' ){
			$consulta->esCcoIngreso = true;
		}
		
		if( isset($rs['Ccourg']) && $rs['Ccourg'] == 'on' ){
			$consulta->esCcoUrgencias = true;
		}

		//Pestañas
		if(isset($rs['Ccopek']) && !empty($rs['Ccopek']) && $rs['Ccopek'] != 'NO APLICA'){
			$consulta->pestanasKardex = $rs['Ccopek'];
		} else {
			$consulta->pestanasKardex = "";
		}

		//Grupos medicamentos
		if(isset($rs['Ccogka']) && !empty($rs['Ccogka']) && $rs['Ccogka'] != 'NO APLICA'){
			$consulta->gruposMedicamentos = $rs['Ccogka'];
		} else {
			$consulta->gruposMedicamentos = "*";
		}

		//Grupos de medicamentos formateados para ser usados en queries y clausulas tipo IN Ej.  Campo IN ('LTR','LTQ')
		if(isset($rs['Ccogka']) && !empty($rs['Ccogka']) && $rs['Ccogka'] != 'NO APLICA'){
			$gruposIncluidos = "(";

			if($rs['Ccogka'] != "*"){
				$vecGrupos = explode(",",$rs['Ccogka']);
				$cuenta = count($vecGrupos);
				$cont1 = 0;
				foreach ($vecGrupos as $grupo){
					$gruposIncluidos .= "'".str_replace(",","','",$grupo)."'";
					if($cont1 < $cuenta-1){
						$gruposIncluidos .= ",";
					}
					$cont1++;
				}
			}
			$gruposIncluidos .= ")";
			$consulta->gruposMedicamentosQuery = $gruposIncluidos;
		} else {
			$consulta->gruposMedicamentosQuery = "('')";
		}

		//Usuario es de central de mezclas
		$consulta->esUsuarioSF = ($consulta->centroCostos == $centroCostosServicioFarmaceutico) ? true : false;

		//Usuario es de servicio farmaceutico
		$consulta->esUsuarioCM = ($consulta->centroCostos == $centroCostosCentralMezclas) ? true : false;

		//Usuario tiene permisos de modificar ctc
		if (strpos($rs['Ccouct'], $consulta->codigo) !== false){
			$consulta->esUsuarioCTC = true;
		} else {
			$consulta->esUsuarioCTC = false;
		}

		//Usuario es de lactario
		if(isset($rs['Ccolac']) && !empty($rs['Ccolac']) && $rs['Ccolac'] == 'on'){
			$consulta->esUsuarioLactario = true;
		} else {
			$consulta->esUsuarioLactario = false;
		}
	}
//	var_dump($consulta);
	return $consulta;
}

/**
 * funcion que replica las ordenes automaticamente de acuerdo a los parametros pasados en el
 * objeto objParams
 */
function replicarOrdenesAutomaticamente($objParams){
	global $wbasedato;
	global $conex;
	$q = "INSERT INTO {$wbasedato}_{$objParams->nombreTabla}
					 ({$objParams->camposTablaInsert}) 
				SELECT
					 {$objParams->camposTablaSelect}
				FROM 
					{$wbasedato}_{$objParams->nombreTabla}
				WHERE
					{$objParams->adicionalQuery}
				";
			//	(SELECT MAX(Ekafec) FROM ".$wbasedato."_000050 WHERE Ekahis = '$historia' AND Ekaing = '$ingreso')";
		$res = mysql_query($q, $conex) or ($descripcion = utf8_encode(mysql_errno() . $objParams->error . mysql_error()));
		return $descripcion;
}

/**
 * Funcion que consulta los campos de la tabla $tabla y los devuelve en un array
 */
function consultarColumnasTabla($conex,$wbasedato,$tabla){
	$arrayFinal = array();
	$q = "SHOW FIELDS FROM {$wbasedato}_{$tabla}
	";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0){
		while( $info = mysql_fetch_array( $res ) ){
			array_push($arrayFinal,$info['Field']); 
		}
	}
	return $arrayFinal;
}
/***********************************
 * CLASES
 ***********************************/
class datosReplicar{
	var $nombreTabla;
	var $camposTablaSelect;
	var $camposTablaInsert;
	var $adicionalQuery;
	var $error;
}

class UsuarioKardex{
	var $codigo;
	var $contrasena;
	var $descripcion;
	var $empresa;

	//Centro costos
	var $centroCostos;
	var $nombreCentroCostos;
	var $centroCostosHospitalario;
	var $centroCostosGrabacion;
	var $pestanasKardex;
	var $gruposMedicamentos;
	var $gruposMedicamentosQuery;
	var $esUsuarioCM;
	var $esUsuarioSF;
	var $esUsuarioCTC;
	var $esUsuarioLactario;
	
	var $esCcoUrgencias = false;
	var $esCcoCirugia = false;
	var $esCcoIngreso = false;
}


class pacienteKardexDTO {
	var $historiaClinica = "";
	var $ingresoHistoriaClinica = "";
	var $documentoIdentidad = "";
	var $tipoDocumentoIdentidad = "";
	var $nombre1 = "";
	var $nombre2 = "";
	var $apellido1 = "";
	var $apellido2 = "";

	//Adicionales en UNIX
	var $fechaIngreso = "";
	var $horaIngreso = "";
	var $servicioActual = "";
	var $servicioAnterior = "";
	var $servicioAnteriorUrgencias = "";
	var $servicioAnteriorCirugia = "";
	var $habitacionActual = "";
	var $habitacionAnterior = "";
	var $numeroIdentificacionResponsable = "";
	var $nombreResponsable = "";
	var $genero = "";
	var $fechaNacimiento = "";
	var $deHospitalizacion = "";
	var $ultimoMvtoHospitalario = "";
	var $fechaHoraIngresoServicio = "";
	var $nombreServicioActual = "";

	var $altaProceso = "";
	var $altaDefinitiva = "";
	
	var $sexo = "";
	
	var $esDeAyudaDx = false;
}


?>