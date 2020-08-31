<?php
include_once("conex.php");
/**
 PROGRAMA                   : rep_trasladoDePacientes.php
 AUTOR                      : Frederick Aguirre.
 FECHA CREACION             : 01 de octubre de 2012

 DESCRIPCION:
 Muestra en que piso de hospitalizacion estan ubicados los pacientes.

 CAMBIOS:
    2019-12-27   -Arleyda I.C. Se adiciona opción para exportar el reporte en formato Excel, incluyendo en la tabla la 					  información de solicitud, asignación, entrega y recibo del paciente.
    2019-12-16   -Arleyda I.C. Se modifica el campo 'tiempo_solcama' en el array principal 'fila' para que utilice los
                  campos 'Fec_asigcama' y 'Hora_asigcama' remplazando los campos 'Fecha_cumplimiento', 'Hora_cumplimiento'
                  y así, mejorar el indicador de cumplimiento
    2019-02-25_  -Arleyda I.C. Migración realizada
	2017-08-15:  - Se cambian la consulta de solicitud de cama para que muestre la información correcta.
				 - Se corrgien tildes y caracteres especiales en los nombres de pacientes y usuarios que realizan los movimientos de solicitud de cama
	2012-10-02:  Ahora el rango de fechas se hace de la tabla 0000_17 y no de la 0000_18. Este cambio implica tener en cuenta
				 que hay pacientes que no registran una entrega pero si recibo y viceversa
**/

if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";

	echo "<title>Reporte Trastalado de Pacientes</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';

	echo '<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />';

	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
}

	//********************FUNCIONES COMUNES****************************//
	

	include_once("root/comun.php");
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoCamas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
	$conex = obtenerConexionBD("matrix");
	$wccos = consultaCentrosCostos("ccohos");
	$wccosSU = consultaCentrosCostos("ccohos = 'on' AND ccourg != 'on'", true);
	$wactualiz = "2019-12-27";

	//FIN***************************************************************//


	//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
	if( isset($_REQUEST['action'] )){
		$action = $_REQUEST['action'];
		if($action=="consultar"){
			ejecutarBusqueda( $_REQUEST['fecha_inicial'], $_REQUEST['fecha_final'], $_REQUEST['servicio_origen'], $_REQUEST['servicio_destino']);
		}
		return;
	}
	//FIN****************************************************************************************************************//


	//**************************FUNCIONES DE PHP********************************************//

	function consultarSolicitudCamas($whis, $fecha, $hab ){

		global $wbasedatoCamas;
		global $conex;
		global $wprefijoUrg;
		
		$fecha_in = date( "Y-m-d", strtotime( $fecha )-7*24*3600 );

		$query = "SELECT Solicito, Usu_central, Fecha_data, Hora_data, Fecha_cumplimiento, Hora_cumplimiento, Fec_asigcama, Hora_asigcama "
				  ."FROM ".$wbasedatoCamas."_000003 "
			     ."WHERE central = 'CAMAS' "
				 ."AND Anulada ='No' "
				 ."AND (Habitacion like '%".$whis."%' OR Observacion like '%".$whis."%' OR historia='".$whis."')"
				." AND Fecha_data = '".$fecha."'";
				
		$query = "SELECT Solicito, Usu_central, Fecha_data, Hora_data, Fecha_cumplimiento, Hora_cumplimiento, Fec_asigcama, Hora_asigcama "
				  ."FROM ".$wbasedatoCamas."_000003 "
			     ."WHERE central = 'CAMAS' "
				 ."  AND Anulada ='No' "
				 ."  AND historia='".$whis."' "
				 ."  AND Fecha_data BETWEEN '".$fecha_in."' AND '".$fecha."' "
				 ."  AND Hab_asignada  = '".$hab."'";

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$wresultado = array();
		if( $num > 0 ){
			while($datatmp = mysql_fetch_assoc($res)) {
				$datatmp['ususol'] = buscarUsuario($datatmp['Solicito']);
				$datatmp['usucum'] = buscarUsuario($datatmp['Usu_central']);
				array_push($wresultado, $datatmp );
			}
		}
		return $wresultado;
	}


	//DADO QUE EN DETERMINADAS TABLAS EL CAMPO SEGURIDAD (USUARIO QUE REGISTRA) TIENE LA LETRA C Y UN GUION ANTES DEL CODIGO
	//ESTE METODO RECIBE ESE CAMPO Y BUSCA EL NOMBRE DEL USUARIO
	function buscarUsuario($wcod_funcionario){
		global $conex;

		if (strpos($wcod_funcionario, '-')){
			$explode = explode('-',$wcod_funcionario);
			$wcod_funcionario = $explode[1];
		}
		$wcod_funcionario = trim($wcod_funcionario);
		$usuario = "";
		$query = "  SELECT  Descripcion as nombre
					FROM    usuarios
					WHERE   Codigo = '$wcod_funcionario'";

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);

		if ($num > 0)
		{
			$row = mysql_fetch_array($res);
			$usuario = $row['nombre'];
		}
		return $usuario;
	}


	function buscarPaciente($whis){
		global $conex;
		global $wemp_pmla;

		$query_info = "
			SELECT  datos_paciente.Pactid ,pacientes_id.Oriced,"
				."  datos_paciente.Pacno1, datos_paciente.Pacno2,"
				."  datos_paciente.Pacap1, datos_paciente.Pacap2"
		  ."  FROM  root_000037 as pacientes_id, root_000036 as datos_paciente"
		  ." WHERE  pacientes_id.Orihis = '".$whis."'"
		  ."   AND  pacientes_id.Oriori =  '".$wemp_pmla."'"
		  ."   AND 	pacientes_id.Oriced = datos_paciente.Pacced"
		  ."   AND 	Oritid = Pactid";

		  
		$res_info = mysql_query($query_info, $conex);
		$row_datos = mysql_fetch_array($res_info);
		$nombres_pac = trim($row_datos['Pacno1'].' '.$row_datos['Pacno2'].' '.$row_datos['Pacap1'].' '.$row_datos['Pacap2']);
		$datos = array('nombres_pac'=>$nombres_pac,'doc'=>$row_datos['Oriced'],'tipodoc'=>$row_datos['Pactid']);
		return $datos;
	}

	function buscarUbicacion($whis, $wing){
		global $conex;
		global $wbasedato;
		global $wemp_pmla;

		$query = "SELECT 	Ubihac as habitacion, Ubiald as alta_definitiva, Ubiptr as proceso_traslado"
				." FROM 	".$wbasedato."_000018"
				." WHERE 	Ubihis = '".$whis."'"
                ." AND 		Ubiing = '".$wing."'"
				." LIMIT 1";

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$datos = array();
		if( $num > 0 ){
			$datos = mysql_fetch_assoc($res);
		}
		return $datos;
	}

	//FUNCION QUE SE LLAMA CUANDO SE HAN ELEGIDO LOS PARAMETROS DE BUSQUEDA PARA MOSTRAR LA TABLA HTML CON LOS RESULTADOS
	function ejecutarBusqueda($fecha_ini, $fecha_fin, $servicio_origen, $servicio_destino){

		global $wbasedato;
		global $conex;
		global $wccos;
		global $wccosSU;
		global $wemp_pmla;

		$where_origen=" (";
		if(  $servicio_origen != ""){
			//Si selecciono un centro de costos de origen
			$where_origen = " Eyrsor=".$servicio_origen;
		}else{
			//Para todos los centros de costos
			foreach ($wccos as $centroCostos)
			{
				$where_origen.=" Eyrsor = $centroCostos->codigo OR";
			}
			$where_origen = substr($where_origen, 0, -2);
			$where_origen.=")";
		}

		$where_destino=" (";
		if(  $servicio_destino != ""){
			//Si selecciono un centro de costos de destino
			$where_destino = " Eyrsde='".$servicio_destino."'";
		}else{
			//Para todos los centros de costos
			foreach ($wccosSU as $centroCostos)
			{
				$where_destino.=" Eyrsde = '$centroCostos->codigo' OR";
			}
			$where_destino = substr($where_destino, 0, -2);
			$where_destino.=")";
		}

		$query = "SELECT 	Eyrhis as historia, Eyring as ingreso, Eyrsor as serv_origen, Eyrhor as hab_origen, Eyrsde as serv_destino, Eyrhde as hab_destino,"
				."			Eyrtip as tipo, Fecha_data as fecha, Hora_data as hora, Seguridad as usuario"
				." FROM 	".$wbasedato."_000017"
				." WHERE 	Fecha_data BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'"
				." AND 		Eyrest != 'off'"
				." AND		".$where_origen
				." AND		".$where_destino
			    ." ORDER BY Eyrhis, Eyring, Fecha_data ASC, Hora_data ASC";

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);


		if ($num > 0 ){

			$entrega = true;
			$historia_ingreso_viejo = "";
			$historia_ingreso_nuevo = "";
			$ya_entrego = false;

			$arreglo = array();
			$fila    = array();
			$filaAnt = array();
			
			while($row = mysql_fetch_assoc($res)) {
				
				//Si el registro anterior era un recibo No lo muestro debido a que la entrega fue realizada antes del filtro de rango de fechas
				if( ( $filaAnt['historia'] == $row['historia'] && $filaAnt['tipo'] == $row['tipo'] && $filaAnt['tipo'] == 'Recibo' )
				    || ( $filaAnt['historia'] != $row['historia'] && $row['tipo'] == 'Recibo' )
				){
					$filaAnt = $row;
					continue;
				}

				$solicitud_camas = "";
				$paciente = "";
				$ubicacion = "";

				$historia_ingreso_nuevo = $row['historia']."-".$row['ingreso'];

				$usuario = buscarUsuario( $row['usuario'] );
				if( isset( $usuario ) ){
					$usuario = str_replace( 'Ñ', '&Ntilde;', $usuario );
					$usuario = str_replace( '?æ', '&Ntilde;', $usuario );
				}

				if( $row['tipo'] == 'Entrega' ){
					$entrega = true;
				}else{
					$entrega = false;
				}

				// CONDICIONES
				if( $entrega == true and $ya_entrego == false){
					
					//*****************FILA NUEVA***********************//
					$solicitud_camas = consultarSolicitudCamas( $row['historia'], $row['fecha'] , $row['hab_destino'] );
					$paciente = buscarPaciente( $row['historia'] );
					$ubicacion = buscarUbicacion( $row['historia'], $row['ingreso'] );
					$historia_ingreso_nuevo = $row['historia']."-".$row['ingreso'];

					$usuario = buscarUsuario( $row['usuario'] );
					if( isset( $usuario ) ){
						$usuario = str_replace( 'Ñ', '&Ntilde;', $usuario );
						$usuario = str_replace( '?æ', '&Ntilde;', $usuario );
					}
					//Obtener los nombres de los centros de costos
					foreach ($wccos as $centroCostos){
						if( $row['serv_origen'] == $centroCostos->codigo ){
							$row['serv_origen_n'] = $centroCostos->nombre;
						}
						if( $row['serv_destino'] == $centroCostos->codigo ){
							$row['serv_destino_n'] = $centroCostos->nombre;
						}
					}
					$fila = array();
					if( $ubicacion['proceso_traslado'] == "off" )
						$fila['proceso_traslado'] = "No";
					else
						$fila['proceso_traslado'] = "Si";
					$fila['his_ing'] = $row['historia']."-".$row['ingreso'];
					$fila['nombre_paciente'] = $paciente['nombres_pac'];
					$fila['tipo_documento_paciente'] = $paciente['tipodoc'];
					$fila['documento_paciente'] = $paciente['doc'];
					$fila['cco_origen'] = $row['serv_origen'];
					$fila['cco_origen_nombre'] = $row['serv_origen_n'];
					if( count($solicitud_camas) > 0 ){
						foreach($solicitud_camas as $keyx => $valx){
							if(isset($valx['Fec_asigcama'])){
								if( $valx['Fec_asigcama'] == "0000-00-00" ){
									$fila['tiempo_solcama'] = "Sin asignar";
								}else{
									$fila['tiempo_solcama'] = calcularDiferenciaFechas( $valx['Fec_asigcama'] . " " . $valx['Hora_asigcama'],$valx['Fecha_data'] . " " . $valx['Hora_data']);
									$fila['tiempo_solcama'] = substr_replace( $fila['tiempo_solcama'] ,"",-3 );
								}
							}else{
								$fila['tiempo_solcama'] = "";
							}
							$fila['usuario_solcama'] = $valx['ususol'];
							$fila['usuario_solcama'] = str_replace( '?æ', '&Ntilde;', $fila['usuario_solcama'] );
							$fila['usuario_solcama'] = str_replace( 'Ñ', '&Ntilde;', $fila['usuario_solcama'] );
							$fila['fecha_solcama'] = $valx['Fecha_data'];
							$fila['hora_solcama']  = $valx['Hora_data'];

							$fila['usuario_asigcama'] = $valx['usucum'];
							$fila['usuario_asigcama'] = str_replace( '?æ', '&Ntilde;', $fila['usuario_asigcama'] );
							$fila['usuario_asigcama'] = str_replace( 'Ñ', '&Ntilde;', $fila['usuario_asigcama'] );
							if(isset($valx['Fec_asigcama'])){
								$fila['fecha_asigcama'] = $valx['Fec_asigcama'];
								$fila['hora_asigcama']  = $valx['Hora_asigcama'];
							}
						}
					}else{
						$fila['tiempo_solcama'] = "";
						$fila['usuario_solcama'] = "";
						$fila['fecha_solcama'] ="";
						$fila['hora_solcama'] = "";
						$fila['usuario_asigcama']="";
						$fila['fecha_asigcama'] = "";
						$fila['hora_asigcama'] = "";
					}

					$fila['cco_destino'] = $row['serv_destino'];
					$fila['cco_destino_nombre'] = $row['serv_destino_n'];
					$fila['habitaciondes'] = $row['hab_destino'];
					$fila['habitacionori'] = $row['hab_origen'];
					if($ubicacion['alta_definitiva'] == 'off')
						$fila['alta_definitiva'] = "No";
					else
						$fila['alta_definitiva'] = "Si";
					//*****************FIN FILA NUEVA***********************//

					//*****************EXISTE ENTREGA***********************//
					$fila['usuario_entpac'] = $usuario;
					$fila['fecha_entpac'] = $row['fecha'];
					$fila['hora_entpac'] = substr_replace($row['hora'] ,"",-3);
					//*****************FIN EXISTE ENTREGA***********************//
					$ya_entrego = true;
					$historia_ingreso_viejo = $row['historia']."-".$row['ingreso'];
					
				}
				else if( $entrega == true and $ya_entrego == true){
					
					
					//*****************NO EXISTE RECIBO, TERMINAR FILA***********************//
					$fila['usuario_recpac'] = "";
					$fila['fecha_recpac'] = "";
					$fila['hora_recpac'] = "";
					$fila['tiempo_total'] = "";
					
					array_push( $arreglo, $fila );
					$fila = array();
					//*****************FIN NO EXISTE RECIBO, TERMINAR FILA***********************//

					//*****************FILA NUEVA***********************//
					$solicitud_camas = consultarSolicitudCamas( $row['historia'], $row['fecha'], $row['hab_destino'] );
					$paciente = buscarPaciente( $row['historia'] );
					$ubicacion = buscarUbicacion( $row['historia'], $row['ingreso'] );
					$usuario = buscarUsuario( $row['usuario'] );
					if( isset( $usuario ) ){
						$usuario = str_replace( 'Ñ', '&Ntilde;', $usuario );
						$usuario = str_replace( '?æ', '&Ntilde;', $usuario );
					}
					//Obtener los nombres de los centros de costos
					foreach ($wccos as $centroCostos){
						if( $row['serv_origen'] == $centroCostos->codigo ){
							$row['serv_origen_n'] = $centroCostos->nombre;
						}
						if( $row['serv_destino'] == $centroCostos->codigo ){
							$row['serv_destino_n'] = $centroCostos->nombre;
						}
					}
					
					$fila = array();
					if( $ubicacion['proceso_traslado'] == "off" )
						$fila['proceso_traslado'] = "No";
					else
						$fila['proceso_traslado'] = "Si";
					$fila['his_ing'] = $row['historia']."-".$row['ingreso'];
					$fila['nombre_paciente'] = $paciente['nombres_pac'];
					$fila['tipo_documento_paciente'] = $paciente['tipodoc'];
					$fila['documento_paciente'] = $paciente['doc'];
					$fila['cco_origen'] = $row['serv_origen'];
					$fila['cco_origen_nombre'] = $row['serv_origen_n'];
					if( count($solicitud_camas) > 0 ){
						foreach($solicitud_camas as $keyx => $valx){
							if(isset($valx['Fec_asigcama'])){
								if( $valx['Fec_asigcama'] == "0000-00-00" ){
									$fila['tiempo_solcama'] = "Sin asignar";
								}else{
									$fila['tiempo_solcama'] = calcularDiferenciaFechas( $valx['Fec_asigcama'] . " " . $valx['Hora_asigcama'],$valx['Fecha_data'] . " " . $valx['Hora_data']);
									$fila['tiempo_solcama'] = substr_replace( $fila['tiempo_solcama'] ,"",-3 );
								}
							}else{
								$fila['tiempo_solcama'] = "";
							}
							$fila['usuario_solcama'] = $valx['ususol'];
							$fila['usuario_solcama'] = str_replace( '?æ', '&Ntilde;', $fila['usuario_solcama'] );
							$fila['usuario_solcama'] = str_replace( 'Ñ', '&Ntilde;', $fila['usuario_solcama'] );
							$fila['fecha_solcama'] = $valx['Fecha_data'];
							$fila['hora_solcama'] = $valx['Hora_data'];

							$fila['usuario_asigcama'] = $valx['usucum'];
							$fila['usuario_asigcama'] = str_replace( '?æ', '&Ntilde;', $fila['usuario_asigcama'] );
							$fila['usuario_asigcama'] = str_replace( 'Ñ', '&Ntilde;', $fila['usuario_asigcama'] );
							if(isset($valx['Fec_asigcama'])){
								$fila['fecha_asigcama'] = $valx['Fec_asigcama'];
								$fila['hora_asigcama'] = $valx['Hora_asigcama'];
							}
						}
					}else{
						$fila['tiempo_solcama'] = "";
						$fila['usuario_solcama'] = "";
						$fila['fecha_solcama'] ="";
						$fila['hora_solcama'] = "";
						$fila['usuario_asigcama']="";
						$fila['fecha_asigcama'] = "";
						$fila['hora_asigcama'] = "";
					}

					$fila['cco_destino'] = $row['serv_destino'];
					$fila['cco_destino_nombre'] = $row['serv_destino_n'];
					$fila['habitaciondes'] = $row['hab_destino'];
					$fila['habitacionori'] = $row['hab_origen'];
					if($ubicacion['alta_definitiva'] == 'off')
						$fila['alta_definitiva'] = "No";
					else
						$fila['alta_definitiva'] = "Si";
					//*****************FIN FILA NUEVA***********************//
					//*****************EXISTE ENTREGA***********************//
					$fila['usuario_entpac'] = $usuario;
					$fila['fecha_entpac'] = $row['fecha'];
					$fila['hora_entpac'] = substr_replace($row['hora'] ,"",-3);
					//*****************FIN EXISTE ENTREGA***********************//
					$ya_entrego = true;
					$historia_ingreso_viejo = $row['historia']."-".$row['ingreso'];
				}

				if( $entrega == false and ($historia_ingreso_nuevo == $historia_ingreso_viejo) and $ya_entrego == true){
					
					//*****************EXISTE RECIBO, TERMINAR FILA***********************//
					$fila['usuario_recpac'] = $usuario;
					$fila['fecha_recpac'] = $row['fecha'];
					$fila['hora_recpac'] = substr_replace($row['hora'] ,"",-3);
					if( isset($fila['fecha_solcama']) ){
						$tiempo_total = calcularDiferenciaFechas( $fila['fecha_recpac'], $fila['fecha_solcama']." ".$fila['hora_solcama'] );
						$fila['tiempo_total'] = substr_replace($tiempo_total ,"",-3);
					}else{
						$fila['tiempo_total'] = "";
					}
					array_push( $arreglo, $fila );
					$fila = array();
					$ya_entrego = false;
					//*****************FIN EXISTE RECIBO, TERMINAR FILA***********************//
				}
				else if( $entrega == false and $ya_entrego == false ){
					
					//*****************FILA NUEVA***********************//
					$solicitud_camas = consultarSolicitudCamas( $row['historia'], $row['fecha'], $row['hab_destino'] );
					$paciente = buscarPaciente( $row['historia'] );
					$ubicacion = buscarUbicacion( $row['historia'], $row['ingreso'] );
					
					$usuario = buscarUsuario( $row['usuario'] );
					if( isset( $usuario ) ){
						$usuario = str_replace( 'Ñ', '&Ntilde;', $usuario );
						$usuario = str_replace( '?æ', '&Ntilde;', $usuario );
					}
					//Obtener los nombres de los centros de costos
					foreach ($wccos as $centroCostos){
						if( $row['serv_origen'] == $centroCostos->codigo ){
							$row['serv_origen_n'] = $centroCostos->nombre;
						}
						if( $row['serv_destino'] == $centroCostos->codigo ){
							$row['serv_destino_n'] = $centroCostos->nombre;
						}
					}
					$fila = array();
					if( $ubicacion['proceso_traslado'] == "off" )
						$fila['proceso_traslado'] = "No";
					else
						$fila['proceso_traslado'] = "Si";
					
					$fila['his_ing'] = $row['historia']."-".$row['ingreso'];
					$fila['nombre_paciente'] = $paciente['nombres_pac'];
					$fila['tipo_documento_paciente'] = $paciente['tipodoc'];
					$fila['documento_paciente'] = $paciente['doc'];
					$fila['cco_origen'] = $row['serv_origen'];
					$fila['cco_origen_nombre'] = $row['serv_origen_n'];
					if( count($solicitud_camas) > 0 ){
						foreach($solicitud_camas as $keyx => $valx){
							if(isset($valx['Fec_asigcama'])){
								if( $valx['Fec_asigcama'] == "0000-00-00" ){
									$fila['tiempo_solcama'] = "Sin asignar";
								}else{
									$fila['tiempo_solcama'] = calcularDiferenciaFechas( $valx['Fec_asigcama'] . " " . $valx['Hora_asigcama'],$valx['Fecha_data'] . " " . $valx['Hora_data']);
									$fila['tiempo_solcama'] = substr_replace( $fila['tiempo_solcama'] ,"",-3 );
								}
							}else{
								$fila['tiempo_solcama'] = "";
							}
							$fila['usuario_solcama'] = $valx['ususol'];
							$fila['usuario_solcama'] = str_replace( '?æ', '&Ntilde;', $fila['usuario_solcama'] );
							$fila['usuario_solcama'] = str_replace( 'Ñ', '&Ntilde;', $fila['usuario_solcama'] );
							$fila['fecha_solcama'] = $valx['Fecha_data'];
							$fila['hora_solcama'] = $valx['Hora_data'];

							$fila['usuario_asigcama'] = $valx['usucum'];
							$fila['usuario_asigcama'] = str_replace( '?æ', '&Ntilde;', $fila['usuario_asigcama'] );
							$fila['usuario_asigcama'] = str_replace( 'Ñ', '&Ntilde;', $fila['usuario_asigcama'] );
							if(isset($valx['Fec_asigcama'])){
								$fila['fecha_asigcama'] = $valx['Fec_asigcama'];
								$fila['hora_asigcama']  = $valx['Hora_asigcama'];
							}
						}
					}else{
						$fila['tiempo_solcama'] = "";
						$fila['usuario_solcama'] = "";
						$fila['fecha_solcama'] ="";
						$fila['hora_solcama'] = "";
						$fila['usuario_asigcama']="";
						$fila['fecha_asigcama'] = "";
						$fila['hora_asigcama'] = "";
					}

					$fila['cco_destino'] = $row['serv_destino'];
					$fila['cco_destino_nombre'] = $row['serv_destino_n'];
					$fila['habitaciondes'] = $row['hab_destino'];
					$fila['habitacionori'] = $row['hab_origen'];
					if($ubicacion['alta_definitiva'] == 'off')
						$fila['alta_definitiva'] = "No";
					else
						$fila['alta_definitiva'] = "Si";
					//*****************FIN FILA NUEVA***********************//
					$ya_entrego = true;
					$historia_ingreso_viejo = $row['historia']."-".$row['ingreso'];

					//*****************NO EXISTE ENTREGA, TERMINAR FILA***********************//
					$fila['usuario_entpac'] = "";
					$fila['fecha_entpac'] = "";
					$fila['hora_entpac'] = "";
					$fila['usuario_recpac'] = $usuario;
					$fila['fecha_recpac'] = $row['fecha'];
					$fila['hora_recpac'] = substr_replace($row['hora'] ,"",-3);
					if( isset($fila['fecha_solcama']) ){
						$tiempo_total = calcularDiferenciaFechas( $fila['fecha_recpac'], $fila['fecha_solcama']." ".$fila['hora_solcama'] );
						$fila['tiempo_total'] = substr_replace($tiempo_total ,"",-3);
					}else{
						$fila['tiempo_total'] = "";
					}
					array_push( $arreglo, $fila );
					$fila = array();
					$ya_entrego = false;
					//*****************FIN NO EXISTE ENTREGA, TERMINAR FILA***********************//
				}
				
				$filaAnt = $row;
			}

			$titulo_tabla = "Traslados entre ".$fecha_ini." y ".$fecha_fin;
			if( $servicio_origen != "" )
				$titulo_tabla.= " que vienen del centro de costos ".$servicio_origen;

			if( $servicio_destino != "" and $servicio_origen != "")
				$titulo_tabla.= " y ";

			if( $servicio_destino != "" )
				$titulo_tabla.= " que estan ubicados en el centro de costos ".$servicio_destino;


			//Respuesta es la variable que se retornara a la solicitud ajax
			$respuesta = '<div>';
			$respuesta.= "<center>";
			$respuesta.= "<table align='center' id='tabla_resultados'>";
			$respuesta.= "<tr class='encabezadotabla'>";
			$respuesta.= "<td colspan='14' align='center'>".$titulo_tabla."</td>";
			$respuesta.= "</tr>";

			$respuesta.= "<tr class='encabezadoTabla'>";

			$respuesta.= "<th rowspan='2'>Historia</th>";
			$respuesta.= "<th rowspan='2'>Nombre Paciente</th>";
			$respuesta.= "<th rowspan='2'>Centro de costos <br> de origen</th>";
			$respuesta.= "<th>Solicitud cama</th>";
			$respuesta.= "<th colspan='2'>Entrega</th>";
			$respuesta.= "<th colspan='2'>Recibo</th>";
			$respuesta.= "<th rowspan='2'>Centro de costos <br> de destino</th>";
			$respuesta.= "<th rowspan='2'>Habitaci&oacuten <br> origen</th>";
			$respuesta.= "<th rowspan='2'>Habitaci&oacuten <br> destino</th>";
			$respuesta.= "<th rowspan='2'>Alta def.</th>";
			$respuesta.= "<th rowspan='2'>Proc. tras.</th>";
			$respuesta.= "<th rowspan='2' class='msg_tooltip' nowrap='nowrap' title='Desde la solicitud de cama <br> hasta la entrega en hospitalizacion'>Tiempo total<br>(hh:mm)</th>";

			$respuesta.= "</tr>";
			$respuesta.= "<tr class='encabezadotabla'>";
			$respuesta.= "<th nowrap='nowrap' class='msg_tooltip' title='Desde la solicitud de cama <br> hasta la asignacion de cama'>Tiempo total<br>(hh:mm)</th>";
			$respuesta.= "<th nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>";
			$respuesta.= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$respuesta.= "<th nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>";
			$respuesta.= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$respuesta.= "</tr>";

            //TITULO PARA EL ARCHIVO A EXPORTAR
			$resexportar  = "<table align='center' id='tabla_exportar' style='display:none'>";
			$resexportar .= "<tr class='encabezadotabla'>";
			$resexportar .= "<td colspan='22' align='center'>".$titulo_tabla."</td>";
			$resexportar .= "</tr>";
		    $resexportar .= "<tr class='encabezadoTabla'>";

			$resexportar .= "<th rowspan='2'>Historia</th>";
			$resexportar .= "<th rowspan='2'>Nombre Paciente</th>";
						
			$resexportar .= "<th colspan='3'>Usuario que registra <br> solicitud de cama</th>";
			$resexportar .= "<th colspan='3'>Usuario que registro <br> asignacion de cama</th>";
			$resexportar .= "<th>Solicitud cama</th>";
			$resexportar .= "<th rowspan='2'>Centro de costos origen</th>";
			$resexportar .= "<th colspan='3'>Usuario que registra <br> la entrega del paciente</th>";
			$resexportar .= "<th colspan='3'>Usuario que registra <br> el recibo del paciente</th>";
			$resexportar .= "<th rowspan='2'>Centro de costos destino</th>";
			$resexportar .= "<th rowspan='2'>Habitacion <br> origen</th>";
			$resexportar .= "<th rowspan='2'>Habitacion <br> destino</th>";
			$resexportar .= "<th rowspan='2'>Alta def.</th>";
			$resexportar .= "<th rowspan='2'>Proc. tras.</th>";
			$resexportar .= "<th rowspan='2' class='msg_tooltip' nowrap='nowrap' title='Desde la solicitud de cama <br> hasta la entrega en hospitalizacion'>Tiempo total<br>(hh:mm)</th>";

			$resexportar .= "</tr>";
			$resexportar .= "<tr class='encabezadotabla'>";

			$resexportar .= "<th>Nombre</th>";
			$resexportar .= "<th nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>";
			$resexportar .= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$resexportar .= "<th>Nombre</th>";
			$resexportar .= "<th nowrap='nowrap'>Fecha </th>";
			$resexportar .= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$resexportar .= "<th>Tiempo total</th>";			
			$resexportar .= "<th nowrap='nowrap'>Nombre</th>";
			$resexportar .= "<th nowrap='nowrap'>Fecha </th>";
			$resexportar .= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$resexportar .= "<th nowrap='nowrap'>Nombre</th>";
			$resexportar .= "<th nowrap='nowrap'>Fecha</th>";
			$resexportar .= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$resexportar .= "</tr>";

			$wclass="";
			$i = 0;
			$texto_oculto = "";

			foreach( $arreglo as $row){

				if ( ($i%2) == 0 )
					$wclass="fila1";
				else
					$wclass="fila2";


				$texto_oculto="<tr id='oculto_$i' style='display:none'>
							<td colspan='13'>
								<table align='center'>
									<tbody>
										<tr class='fondoGris'>
											<td colspan='3'>Usuario que registra <br> solicitud de cama</td>
											<td colspan='3'>Usuario que registro <br> asignacion de cama</td>
											<td rowspan='2'>Usuario que registra <br> la entrega del paciente</td>
											<td rowspan='2'>Usuario que registra <br> el recibo del paciente</td>
										</tr>
										<tr class='fondoGris'>
											<td>Nombre</td>
											<td nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</td>
											<td nowrap='nowrap'>Hora <br>(hh:mm)</td>
											<td>Nombre</td>
											<td nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</td>
											<td nowrap='nowrap'>Hora <br>(hh:mm)</td>
										</tr>";
				$texto_oculto.="<tr class='fondoCrema'>";


				$id = "oculto_".$i;
				$respuesta   .="<tr onclick="."\""."javascript:mostrarOculto('".$id."',this)"."\"".">";
				$resexportar .="<tr>"; 

				//HISTORIAL INGRESO
				$respuesta   .="<td class='$wclass centrar' nowrap='nowrap'>".$row['his_ing']."</td>";
				$resexportar .="<td >".$row['his_ing']."</td>";
				
				//PACIENTE
				$respuesta   .="<td nowrap='nowrap' class='$wclass izquierda msg_tooltip' title='".$row['tipo_documento_paciente']." - ".$row['documento_paciente']."'>".utf8_encode( $row['nombre_paciente'] )."</td>";

				$resexportar .="<td title='".$row['tipo_documento_paciente']." - ".$row['documento_paciente']."'>".utf8_encode( $row['nombre_paciente'] )."</td>";

				//CCO DE ORIGEN
				$respuesta   .="<td  class='$wclass msg_tooltip centrar' title='".$row['cco_origen_nombre']."'>".$row['cco_origen']."</td>";


				//SOLICITUD CAMA
				$texto_oculto.="<td align='center' nowrap='nowrap'>".utf8_encode( $row['usuario_solcama'] )."</td>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['fecha_solcama']."</td>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".substr_replace($row['hora_solcama'] ,"",-3)."</td>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".utf8_encode( $row['usuario_asigcama'] )."</td>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['fecha_asigcama']."</td>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".substr_replace($row['hora_asigcama'],"",-3)."</td>";

				$resexportar .= "<td align='center'>".utf8_encode( $row['usuario_solcama'] )."</td>";
				$resexportar .= "<td align='center'>".$row['fecha_solcama']."</td>";
				$resexportar .= "<td align='center'>".substr_replace($row['hora_solcama'] ,"",-3)."</td>";
				$resexportar .= "<td align='center'>".utf8_encode( $row['usuario_asigcama'] )."</td>";
				$resexportar .= "<td align='center'>".$row['fecha_asigcama']."</td>";
				$resexportar .= "<td align='center'>".substr_replace($row['hora_asigcama'],"",-3)."</td>";
				$resexportar .= "<td>".$row['tiempo_solcama']."</td>"; 
				$resexportar .= "<td  title='".$row['cco_origen_nombre']."'>".$row['cco_origen']."</td>";

				//TIEMPO DIFERENCIA ENTRE SOLICITUD DE CAMA Y CUMPLIMIENTO
				$respuesta   .="<td class='$wclass centrar' nowrap='nowrap'>".$row['tiempo_solcama']."</td>";
				

				if (!isset($row['fecha_entpac']))
					$row['fecha_entpac'] = "";
				if( !isset($row['hora_entpac']))
					$row['hora_entpac'] = "";
				if (!isset( $row['usuario_entpac']))
					$row['usuario_entpac'] = "";
				
				//ENTREGA
				$respuesta   .="<td class='$wclass centrar' nowrap='nowrap'>".$row['fecha_entpac']."</td>";
				$resexportar .="<td>".utf8_encode( $row['usuario_entpac'] )."</td>";//usuario_entrega
				$resexportar .="<td>".$row['fecha_entpac']."</td>";
				$respuesta   .="<td class='$wclass centrar' nowrap='nowrap'>".$row['hora_entpac']."</td>";
				$resexportar .="<td>".$row['hora_entpac']."</td>";

				$texto_oculto.="<td align='center' nowrap='nowrap'>".utf8_encode( $row['usuario_entpac'] )."</td>";//usuario_entrega
				

				//RECIBO
				$respuesta   .="<td class='$wclass centrar' nowrap='nowrap'>".$row['fecha_recpac']."</td>";
				$resexportar .="<td>".utf8_encode( $row['usuario_recpac'] )."</td>";//usuario_recibo
                $resexportar .="<td>".$row['fecha_recpac']."</td>";
				$respuesta   .="<td class='$wclass centrar' nowrap='nowrap'>".$row['hora_recpac']."</td>";
				$resexportar .="<td>".$row['hora_recpac']."</td>";

				$texto_oculto.="<td align='center' nowrap='nowrap'>".utf8_encode( $row['usuario_recpac'] )."</td>";//usuario_recibo
				

				//CCO DE DESTINO
				$respuesta   .="<td  class='$wclass msg_tooltip centrar' title='".$row['cco_destino_nombre']."'>".$row['cco_destino']."</td>";
				$resexportar .="<td title='".$row['cco_destino_nombre']."'>".$row['cco_destino']."</td>";

				//HABITACION
				$respuesta   .="<td class='$wclass centrar'>".$row['habitacionori']."</td>";
				$respuesta   .="<td class='$wclass centrar'>".$row['habitaciondes']."</td>";
				$resexportar .="<td>".$row['habitacionori']."</td>";
				$resexportar .="<td>".$row['habitaciondes']."</td>";

				//ALTA DEFINITIVA
				$respuesta   .="<td class='$wclass centrar'>".$row['alta_definitiva']."</td>";
				$resexportar .="<td>".$row['alta_definitiva']."</td>";

				//PROCESO TRASLADO
				$respuesta   .="<td class='$wclass centrar'>".$row['proceso_traslado']."</td>";
                $resexportar .="<td>".$row['proceso_traslado']."</td>";

				$fecha_recibo = $row['fecha_recpac']." ".$row['hora_recpac'];				

				$tiempo_total = calcularDiferenciaFechas( $fecha_recibo, $row['fecha_solcama']." ".$row['hora_solcama'] );

				//TIEMPO TOTAL
				$respuesta   .="<td class='$wclass centrar' nowrap='nowrap'>".substr_replace($tiempo_total ,"",-3)."</td>";
                $resexportar .="<td>".substr_replace($tiempo_total ,"",-3)."</td>";

				$respuesta   .="</tr>";
				$resexportar .="</tr>";

				$texto_oculto.="</tr>";

				$texto_oculto.=" </tbody>
								 </table>
								 </td>
							     </tr>";

				$respuesta.=$texto_oculto;
				$i++;


			}

			$respuesta.= "</table>";
			$respuesta.= "</center>";
			$respuesta.='</div>';

		}else{
			$respuesta = "No hay traslados con los parametros elegidos";
		}
		echo $respuesta;
        echo $resexportar;

	}

	function calcularDiferenciaFechas($fecha_hora, $fecha_hora2){
		global $conex;
		if( !isset($fecha_hora) && !isset($fecha_hora2) ){
			return;
		}

		$query = "SELECT TIMEDIFF(  '".$fecha_hora."',  '".$fecha_hora2."' ) as diferencia";
		// echo "<pre>$query</pre>";
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$diferencia = "";
		if($num > 0){
			$row = mysql_fetch_assoc($res);
			$diferencia = $row['diferencia'];
		}
		return $diferencia;
	}

	//FUNCION QUE SE LLAMA CUANDO LA PAGINA CARGA Y MUESTRA LOS PARAMETROS DE CONSULTA
	function vistaInicial(){

		global $wemp_pmla;
		global $wccos;
		global $wccosSU;
		global $wactualiz;
		//Se imprimen variables ocultas
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		echo "<center>";

		encabezado("REPORTE TRASLADO DE PACIENTES",$wactualiz,"clinica");


		$fecha_hoy = date("Y-m-d");
		$fecha_pddm = date("Y-m");
		$fecha_pddm.="-01";

		echo '<span class="subtituloPagina2">Par&aacutemetros de consulta</span>';
		echo '<br><br>';

		echo "<div id='fechas'>";

		echo "<table align='center'>";
		echo "<tbody>";
		echo "<tr>";
		echo "<td class='fila1'>Fecha inicial</td>";
		echo "<td class='fila2' align='center'>";
		campoFechaDefecto( "f_inicial", $fecha_pddm );
		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td class='fila1'>Fecha final</td>";
		echo "<td class='fila2' align='center'>";
		campoFechaDefecto( "f_final", $fecha_hoy );
		echo "</td>";
		echo "</tr>";


		echo "<tr>";
		echo "<td class='fila1'>Servicio origen</td>";
		echo "<td class='fila2' align='center'>";
		echo "<select id='servicio_origen'> <option value=''>Todos</option>";
		foreach ($wccos as $centroCostos)
		{
			echo "<option value='".$centroCostos->codigo."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td class='fila1'>Servicio destino</td>";
		echo "<td class='fila2' align='center'>";
		echo "<select id='servicio_destino'> <option value=''>Todos</option>";
		foreach ($wccosSU as $centroCostos)
		{
			echo "<option value='".$centroCostos->codigo."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";

		echo "</table>";

		echo "<br>";
		echo '<input type="button" id="consultar" value="Consultar"></input>';
		echo '&nbsp;<input type="button" id="exportar" value="Exportar"></input>';
		echo '</div>';
		echo '<br><br>';
		echo '<div id="resultados"></div>';
		echo '</center>';

		echo '<center>';
		echo '<br><br>';
		echo '<table>';
		echo '<tr>';
		echo "<td align='center'>";
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>';
		echo "<input type=button value='Cerrar Ventana' onclick='cerrarVentana()'>";
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</center>';
		echo '<br><br><br><br>';

		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
	}
	//FIN***********************************************************************************************************//
?>

		<style type="text/css">
			    #tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
			    #tooltip h3, #tooltip div{margin:0; width:250px}

			    .centrar{
					text-align:center;
					nowrap: nowrap;
			    }

				.fondoGris{
					font-weight:normal;
					text-align:center;
				}
				.fondoCrema{
					font-weight:bold;
					font-size: 9pt;
				}

			    .izquierda{
					text-align:left;
					nowrap: nowrap;
			    }

			    .fila1{
					font-size: 9pt;
				}

				.fila2{
					font-size: 9pt;
				}

			    a img{
					border:0;
				}
		</style>

		<script>

			$(document).ready(function() {
				//Cuando cargue completamente la pagina

				//agregar eventos a campos de la pagina
				$("#consultar").click(function() {
					realizarConsulta(1);
				});

				$("#exportar").click(function() {
					realizarConsulta(2);
				});				

				$("#enlace_retornar").click(function() {
					restablecer_pagina();
				});

				//esconde el elemento
				$("#enlace_retornar").hide();
			});

			function mostrarOculto(idElemento, elemento){
				//ESCONDE O MUESTRA LA FILA OCULTA

				if ( document.getElementById(idElemento).style.display==''){
					document.getElementById(idElemento).style.display='none';
			    }
				else{
					document.getElementById(idElemento).style.display='';
				}

			}

			function restablecer_pagina(){
				$("#enlace_retornar").hide();
				$("#fechas").fadeIn('slow');
				$(".subtituloPagina2").fadeIn('slow');

				$('#resultados').html("");
			}


	     
		function realizarConsulta(opc){
				//muestra el mensaje de cargando
				$.blockUI({ message: $('#msjEspere') });

				$("#enlace_retornar").fadeIn('slow');

				var f_inicial = $("#f_inicial").val();
				var f_final = $("#f_final").val();
				var wemp_pmla = $("#wemp_pmla").val();
				var servicio_origen =$("#servicio_origen").val();
				var servicio_destino =$("#servicio_destino").val();

				//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

				//Realiza el llamado ajax con los parametros de busqueda
				$.get('rep_trasladoDePacientes.php', { wemp_pmla: wemp_pmla, action: "consultar", fecha_inicial: f_inicial, fecha_final: f_final, servicio_origen: servicio_origen,  servicio_destino: servicio_destino, consultaAjax: aleatorio } ,
					function(data) {
						//oculta el mensaje de cargando
						$.unblockUI();
						//imprime resultado
						$('#resultados').html(data);						
						//lleva mens. emergente a los elementos con la clase msg_tooltip
						$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
						
						if (opc==2)
						{
							//Creamos un Elemento Temporal en forma de enlace
				            var tmpElemento = document.createElement('a');
				            var data_type   = 'data:application/vnd.ms-excel'; //Formato anterior xls

				            // Obtenemos la información de la tabla
				            var tabla_div = document.getElementById('tabla_exportar');
				            var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');
				            
				            tmpElemento.href = data_type + ', ' + tabla_html;
				            //Asignamos el nombre a nuestro EXCEL
				            tmpElemento.download = 'rep_trasladodepacientes.xls';
				            // Simulamos el click al elemento creado para descargarlo
				            tmpElemento.click();
			            }
					});
			}
		</script>

    </head>
    <body>

		<!-- EN ADELANTE ES LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>
