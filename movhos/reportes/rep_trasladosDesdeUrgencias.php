<?php
include_once("conex.php");
/**
 PROGRAMA                   : rep_trasladosDesdeUrgencias.php
 AUTOR                      : Frederick Aguirre.
 FECHA CREACION             : 17 de Septiembre de 2012

 DESCRIPCION:
 Muestra los pacientes que son trasladados desde urgencias.

 CAMBIOS:
  25 Febrero 2019:      Arleyda I.C. Migración realizada.
  03 octubre 2012:  	Ahora el rango de fechas se hace de la tabla 0000_17 y no de la 0000_18. Este cambio implica tener en cuenta
						que hay pacientes que no registran una entrega pero si recibo y viceversa
  27 septiembre 2012: 	Se realizan acciones para controlar los errores que se visualizan en internet explorer
  25 septiembre 2012: 	Se agrega informacion de los usuarios que solicitan cama y registran cumplimiento, asi como mas informacion al darle click al tr
  24 septiembre 2012: 	Se agrego la condicion Ubiptr != 'on' para que no muestre los que estan en proceso de traslado y se quito la condicion Ubiald !='on' para que muestre
						aquellos que estuvieron en la habitacion
  19 septiembre 2012: 	Se quitaron algunos datos que el reporte arrojaba 
						Se agregaron los usuarios que intervienen en el proceso de admision, entrega y recibo
  18 septiembre 2012: 	Se creo la funcion vistaInicial que imprime la primera pantalla que ve el usuario al cargar la pagina.

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

	echo "<title>Reporte Trastalados desde Urgencias</title>";
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
	$wccosSU = consultaCentrosCostos("ccohos = 'on' AND ccourg != 'on'", true);
	$wccos = consultaCentrosCostos("ccohos ");
	$wactualiz = "2012-10-03";

	//FIN***************************************************************//

	
	//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
	if( isset($_REQUEST['action'] )){
		$action = $_REQUEST['action'];
		if($action=="consultar"){
			ejecutarBusqueda( $_REQUEST['fecha_inicial'], $_REQUEST['fecha_final'], $_REQUEST['servicio']);
			return;
		}else{
			return;
		}
	}
	//FIN****************************************************************************************************************//
	
	
	
	//**************************FUNCIONES DE PHP********************************************//
	
	function consultarSolicitudCamas($whis, $fecha){
	
		global $wbasedatoCamas;
		global $conex;
		
		$query = "SELECT Solicito, Usu_central, Fecha_data, Hora_data, Fecha_cumplimiento, Hora_cumplimiento "
				  ."FROM ".$wbasedatoCamas."_000003 "
			     ."WHERE central = 'CAMAS' "
				 ."AND Anulada ='No' "
				 ."AND (Habitacion like '%".$whis."%' OR Observacion like '%".$whis."%')"
				." AND Fecha_data = '".$fecha."'";
		
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
	
	function buscarPaciente($whis){
		global $conex;
		global $wemp_pmla;

		$query_info = "
			SELECT  datos_paciente.Pactid ,pacientes_id.Oriced,"
				."  datos_paciente.Pacno1, datos_paciente.Pacno2,"
				."  datos_paciente.Pacap1, datos_paciente.Pacap2"
		  ." FROM    root_000037 as pacientes_id, root_000036 as datos_paciente"
		  ." WHERE   pacientes_id.Orihis = '".$whis."'"
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
	
	//DADO QUE EN DETERMINADAS TABLAS EL CAMPO SEGURIDAD (USUARIO QUE REGISTRA) TIENE LA LETRA C Y UN GUION ANTES DEL CODIGO
	//ESTE METODO RECIBE ESE CAMPO Y BUSCA EL NOMBRE DEL USUARIO
	function buscarUsuario($wcod_funcionario){
		global $conex;

		if (strpos($wcod_funcionario, '-'))
		{
			$explode = explode('-',$wcod_funcionario);
			$wcod_funcionario = $explode[1];
		}

		$usuario = "";
		$query = "  SELECT  Descripcion as nombre
					FROM    usuarios
					WHERE   Codigo = '$wcod_funcionario'";
		$res = mysql_query($query, $conex) or die ("Error: ".mysql_errno()." - en el query - Buscar usuarios: ".$query." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0)
		{
			$row = mysql_fetch_array($res);
			$usuario = $row['nombre'];
		}
		
		return $usuario;
	}
	
	
	
	function buscarCcosUrgencias(){
		global $conex;
		global $wbasedato;

		$query = "SELECT 	Ccocod "
				."FROM 			".$wbasedato."_000011 "
				."WHERE 		ccourg='on'";
			
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);

		$wresultado = array();
		if ($num > 0)
		{
			while($datatmp = mysql_fetch_assoc($res)) {
				array_push($wresultado, $datatmp['Ccocod'] );
			}
		}
		
		return $wresultado;
	}
	
	//FUNCION QUE SE LLAMA CUANDO SE HAN ELEGIDO LOS PARAMETROS DE BUSQUEDA PARA MOSTRAR LA TABLA HTML CON LOS RESULTADOS
	function ejecutarBusqueda( $fecha_ini, $fecha_fin, $servicio_destino ){

		global $wbasedato;
		global $conex;
		global $wccos;
		global $wemp_pmla;
		
		$where_origen=" (";
		
		$ccos_urgencias = buscarCcosUrgencias();
		//Para todos los centros de costos de urgencias
		foreach ($ccos_urgencias as $centroCostos=> $valor)
		{
			$where_origen.=" Eyrsor = $valor OR";
		}
		$where_origen = substr($where_origen, 0, -2);
		$where_origen.=")";
		
		$where_destino=" (";
		if(  $servicio_destino != ""){
			//Si selecciono un centro de costos de destino
			$where_destino = " Eyrsde='".$servicio_destino."'";
		}else{
			//Para todos los centros de costos
			foreach ($wccos as $centroCostos)
			{
				$where_destino.=" Eyrsde = '$centroCostos->codigo' OR";
			}
			$where_destino = substr($where_destino, 0, -2);
			$where_destino.=")";
		}

		$query = "SELECT 	Eyrhis as historia, Eyring as ingreso, Eyrsor as serv_origen, Eyrhor as hab_origen, Eyrsde as serv_destino, Eyrhde as hab_destino,"
				."			Eyrtip as tipo, eyr.Fecha_data as fecha, eyr.Hora_data as hora, eyr.Seguridad as usuario"
				." ,ing.Fecha_data as fecha_ing, ing.Hora_data as hora_ing, ing.Seguridad as usuario_admision"
				." FROM 	".$wbasedato."_000017 as eyr, ".$wbasedato."_000016 as ing"
				." WHERE 	eyr.Fecha_data BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'"
				." AND 		Eyrest != 'off'"
				." AND 		Inghis = Eyrhis"
				." AND 		Inging = Eyring"
				." AND		".$where_origen
				." AND		".$where_destino
			    ." ORDER BY Eyrsde, Eyrhis, Eyring, eyr.Fecha_data ASC, eyr.Hora_data ASC";

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);

		if ($num > 0 ){

			$entrega = true;
			$historia_ingreso_viejo = "";
			$historia_ingreso_nuevo = "";
			$ya_entrego = false;

			$arreglo = array();
			$fila = array();

			while($row = mysql_fetch_assoc($res)) {

				$solicitud_camas = "";
				$paciente = "";
				$ubicacion = "";

				if( !isset( $arreglo[ $row['serv_destino'] ] )){
					$arreglo[ $row['serv_destino'] ] = array();
				}
				
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

				if( $entrega == true){

				}

				// CONDICIONES
				if( $entrega == true and $ya_entrego == false){
					
					//*****************FILA NUEVA***********************//
					$solicitud_camas = consultarSolicitudCamas( $row['historia'], $row['fecha'] );
					$paciente = buscarPaciente( $row['historia'] );
					$ubicacion = buscarUbicacion( $row['historia'], $row['ingreso'] );
					$historia_ingreso_nuevo = $row['historia']."-".$row['ingreso'];
					$usuario_admision =  buscarUsuario( $row['usuario_admision'] );
					if( isset( $usuario_admision ) ){
						$usuario_admision = str_replace( 'Ñ', '&Ntilde;', $usuario_admision );
						$usuario_admision = str_replace( '?æ', '&Ntilde;', $usuario_admision );
					}
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
					$fila['fecha_admision'] = $row['fecha_ing'];
					$fila['hora_admision'] = $row['hora_ing'];
					$fila['usuario_admision'] = $usuario_admision;
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
							if(isset($valx['Fecha_cumplimiento'])){
								if( $valx['Fecha_cumplimiento'] == "0000-00-00" ){
									$fila['tiempo_solcama'] = "Sin asignar";
								}else{
									$fila['tiempo_solcama'] = calcularDiferenciaFechas( $valx['Fecha_cumplimiento'] . " " . $valx['Hora_cumplimiento'],$valx['Fecha_data'] . " " . $valx['Hora_data']);
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
							if(isset($valx['Fecha_cumplimiento'])){
								$fila['fecha_asigcama'] = $valx['Fecha_cumplimiento'];
								$fila['hora_asigcama'] = $valx['Hora_cumplimiento'];
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
					$fila['habitacion'] = $row['hab_destino'];
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
					
				}else if( $entrega == true and $ya_entrego == true){
					
					
					//*****************NO EXISTE RECIBO, TERMINAR FILA***********************//
					$fila['usuario_recpac'] = "";
					$fila['fecha_recpac'] = "";
					$fila['hora_recpac'] = "";
					$fila['tiempo_total'] = "";
					
					array_push( $arreglo[ $fila['cco_destino'] ], $fila );
					$fila = array();
					//*****************FIN NO EXISTE RECIBO, TERMINAR FILA***********************//

					//*****************FILA NUEVA***********************//
					$solicitud_camas = consultarSolicitudCamas( $row['historia'], $row['fecha'] );
					$paciente = buscarPaciente( $row['historia'] );
					$ubicacion = buscarUbicacion( $row['historia'], $row['ingreso'] );
					$usuario_admision =  buscarUsuario( $row['usuario_admision'] );
					if( isset( $usuario_admision ) ){
						$usuario_admision = str_replace( 'Ñ', '&Ntilde;', $usuario_admision );
						$usuario_admision = str_replace( '?æ', '&Ntilde;', $usuario_admision );
					}
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
					$fila['fecha_admision'] = $row['fecha_ing'];
					$fila['hora_admision'] = $row['hora_ing'];
					$fila['usuario_admision'] = $usuario_admision;
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
							if(isset($valx['Fecha_cumplimiento'])){
								if( $valx['Fecha_cumplimiento'] == "0000-00-00" ){
									$fila['tiempo_solcama'] = "Sin asignar";
								}else{
									$fila['tiempo_solcama'] = calcularDiferenciaFechas( $valx['Fecha_cumplimiento'] . " " . $valx['Hora_cumplimiento'],$valx['Fecha_data'] . " " . $valx['Hora_data']);
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
							if(isset($valx['Fecha_cumplimiento'])){
								$fila['fecha_asigcama'] = $valx['Fecha_cumplimiento'];
								$fila['hora_asigcama'] = $valx['Hora_cumplimiento'];
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
					$fila['habitacion'] = $row['hab_destino'];
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
					if( isset($fila['fecha_recpac']) ){
						$tiempo_total = calcularDiferenciaFechas( $fila['fecha_recpac']." ".$fila['hora_recpac'], $fila['fecha_admision']." ".$fila['hora_admision'] );
						$fila['tiempo_total'] = substr_replace($tiempo_total ,"",-3);
					}else{
						$fila['tiempo_total'] = "";
					}
					array_push( $arreglo[ $fila['cco_destino'] ], $fila );
					$fila = array();
					$ya_entrego = false;
					//*****************FIN EXISTE RECIBO, TERMINAR FILA***********************//
				}else if( $entrega == false and $ya_entrego == false ){
					
					//*****************FILA NUEVA***********************//
					$solicitud_camas = consultarSolicitudCamas( $row['historia'], $row['fecha'] );
					$paciente = buscarPaciente( $row['historia'] );
					$ubicacion = buscarUbicacion( $row['historia'], $row['ingreso'] );
					$usuario_admision =  buscarUsuario( $row['usuario_admision'] );
					if( isset( $usuario_admision ) ){
						$usuario_admision = str_replace( 'Ñ', '&Ntilde;', $usuario_admision );
						$usuario_admision = str_replace( '?æ', '&Ntilde;', $usuario_admision );
					}
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
					$fila['fecha_admision'] = $row['fecha_ing'];
					$fila['hora_admision'] = $row['hora_ing'];
					$fila['usuario_admision'] = $usuario_admision;
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
							if(isset($valx['Fecha_cumplimiento'])){
								if( $valx['Fecha_cumplimiento'] == "0000-00-00" ){
									$fila['tiempo_solcama'] = "Sin asignar";
								}else{
									$fila['tiempo_solcama'] = calcularDiferenciaFechas( $valx['Fecha_cumplimiento'] . " " . $valx['Hora_cumplimiento'],$valx['Fecha_data'] . " " . $valx['Hora_data']);
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
							if(isset($valx['Fecha_cumplimiento'])){
								$fila['fecha_asigcama'] = $valx['Fecha_cumplimiento'];
								$fila['hora_asigcama'] = $valx['Hora_cumplimiento'];
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
					$fila['habitacion'] = $row['hab_destino'];
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
					if( isset($fila['fecha_recpac']) ){
						$tiempo_total = calcularDiferenciaFechas( $fila['fecha_recpac']." ".$fila['hora_recpac'], $fila['fecha_admision']." ".$fila['hora_admision'] );
						$fila['tiempo_total'] = substr_replace($tiempo_total ,"",-3);
					}else{
						$fila['tiempo_total'] = "";
					}
					array_push( $arreglo[ $fila['cco_destino'] ], $fila );
					$fila = array();
					$ya_entrego = false;
					//*****************FIN NO EXISTE ENTREGA, TERMINAR FILA***********************//
				}

			}

			$titulo_tabla = "Datos entre ".$fecha_ini." y ".$fecha_fin;
			if( $servicio_destino != "" )
				$titulo_tabla.= " para el centro de costos ".$servicio_destino;
			else
				$titulo_tabla.= " para todos los centros de costo de hospitalizacion";


			//Respuesta es la variable que se retornara a la solicitud ajax
			$respuesta = '<div>';
			$respuesta.= "<center>";
			$respuesta.= "<table align='center' id='tabla_resultados'>";
			$respuesta.= "<tr class='encabezadotabla'>";
			$respuesta.= "<td colspan='14' align='center'>".$titulo_tabla."</td>";
			$respuesta.= "</tr>";
			
			$respuesta.= "<tr class='encabezadoTabla'>";
			$respuesta.= "<th rowspan='2'>Centro de costo</th>";
			$respuesta.= "<th rowspan='2'>Habitacion</th>";
			$respuesta.= "<th rowspan='2'>Alta def.</th>";
			$respuesta.= "<th rowspan='2'>Proc. tras.</th>";
			$respuesta.= "<th rowspan='2'>Historia</th>";
			$respuesta.= "<th rowspan='2'>Nombre Paciente</th>";
			$respuesta.= "<th colspan='2'>Admision</th>";
			$respuesta.= "<th>Solicitud cama</th>";
			$respuesta.= "<th colspan='2'>Entrega</th>";
			$respuesta.= "<th colspan='2'>Recibo</th>";
			$respuesta.= "<th rowspan='2' nowrap='nowrap' class='msg_tooltip' title='Desde la admision <br> hasta el recibo en hospitalizacion'>Tiempo total<br>(hh:mm)</th>";
			
			$respuesta.= "</tr>";
			$respuesta.= "<tr class='encabezadotabla'>";
			$respuesta.= "<th nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>";
			$respuesta.= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$respuesta.= "<th nowrap='nowrap' class='msg_tooltip' title='Desde la solicitud de cama <br> hasta la asignacion de cama'>Tiempo total<br>(hh:mm)</th>";
			$respuesta.= "<th nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>";
			$respuesta.= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$respuesta.= "<th nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>";
			$respuesta.= "<th nowrap='nowrap'>Hora <br>(hh:mm)</th>";
			$respuesta.= "</tr>";

			$wclass="";
			$i = 0;
			$j = 0;
			$texto_oculto = "";
			
			foreach( $arreglo as $key => $value){
				
				$j=0;
				//SI EL NAVEGADOR ES INTERNET EXPLORER MULTIPLICA EL ROWSPAN X 2 PARA QUE TENGA EN CUENTA LOS OCULTOS
				$internet_explorer = false;
				if(preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"])){
					$internet_explorer = true;
				}
				$tamano_rowspan = sizeof($value);
				$num_filas = $tamano_rowspan;
				if($internet_explorer === true ){
					$tamano_rowspan = ($tamano_rowspan*2)-1;
				}
				//******
				
				$clase_ultima_fila_colspan = "";
				
				if($num_filas == $j+1 and $internet_explorer === true){
					$clase_ultima_fila_colspan = "class = 'ultimo_del_colspan fila_unica'";
					$tamano_rowspan = $tamano_rowspan +1;
				}
					
				$id = "oculto_".$i;	
				$respuesta.="<tr $clase_ultima_fila_colspan onclick="."\""."javascript:mostrarOculto('".$id."',this)"."\"".">";
				$clase_ultima_fila_colspan = '';
				//CENTRO DE COSTO			
				$respuesta.="<td  class='encabezadotabla msg_tooltip centrar' title='".$arreglo[$key][0]['cco_destino_nombre']."'  rowspan='".$tamano_rowspan."'>".$key."</td>";
			
			
				foreach( $value as $key2 => $row ){

					if ( ($i%2) == 0 )
						$wclass="fila1";
					else
						$wclass="fila2";


					$texto_oculto="<tr id='oculto_$i' class='oculto' style='display:none'>
								<td colspan='13'>
									<table align='center'>
										<tbody>
											<tr class='fondoGris'>
												<th rowspan='2'>Usuario que registra <br>la admision</th>
												<th colspan='3'>Usuario que registra <br> solicitud de cama</th>
												<th colspan='3'>Usuario que registro <br> asignaci&oacute;n de cama</th>
												<th rowspan='2'>Usuario que registra <br> la entrega del paciente</th>
												<th rowspan='2'>Usuario que registra <br> el recibo del paciente</th>
											</tr>
											<tr class='fondoGris'>
												<th>Nombre</th>
												<th nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>
												<th nowrap='nowrap'>Hora <br>(hh:mm)</th>
												<th>Nombre</th>
												<th nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>
												<th nowrap='nowrap'>Hora <br>(hh:mm)</th>
											</tr>";
					$texto_oculto.="<tr class='fondoCrema'>";

					if( $j!=0){
						$id = "oculto_".$i;
						if($num_filas == $j+1)
							$clase_ultima_fila_colspan = "class = 'ultimo_del_colspan'";
						$respuesta.="<tr $clase_ultima_fila_colspan onclick="."\""."javascript:mostrarOculto('".$id."',this)"."\"".">";
					}
					
					//HABITACION
					$respuesta.="<td class='$wclass centrar'>".$row['habitacion']."</td>";
					
					//ALTA DEFINITIVA
					$respuesta.="<td class='$wclass centrar'>".$row['alta_definitiva']."</td>";
					
					//PROCESO TRASLADO
					$respuesta.="<td class='$wclass centrar'>".$row['proceso_traslado']."</td>";

					//HISTORIAL INGRESO
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$row['his_ing']."</td>";
					
					//PACIENTE
					$respuesta.="<td nowrap='nowrap' class='$wclass izquierda msg_tooltip' title='".$row['tipo_documento_paciente']." - ".$row['documento_paciente']."'>".$row['nombre_paciente']."</td>";
					
					//ADMISION 
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$row['fecha_admision']."</td>";
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".substr_replace( $row['hora_admision'] ,"",-3 )."</td>";
					$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['usuario_admision']."</td>";
					
					//SOLICITUD CAMA
					$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['usuario_solcama']."</td>";
					$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['fecha_solcama']."</td>";
					$texto_oculto.="<td align='center' nowrap='nowrap'>".substr_replace($row['hora_solcama'] ,"",-3)."</td>";
					$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['usuario_asigcama']."</td>";
					$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['fecha_asigcama']."</td>";
					$texto_oculto.="<td align='center' nowrap='nowrap'>".substr_replace($row['hora_asigcama'],"",-3)."</td>";

					//TIEMPO DIFERENCIA ENTRE SOLICITUD DE CAMA Y CUMPLIMIENTO
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$row['tiempo_solcama']."</td>";

					if (!isset($row['fecha_entpac']))
						$row['fecha_entpac'] = "";
					if( !isset($row['hora_entpac']))
						$row['hora_entpac'] = "";
					if (!isset( $row['usuario_entpac']))
						$row['usuario_entpac'] = "";
					//ENTREGA
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$row['fecha_entpac']."</td>";
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$row['hora_entpac']."</td>";
					$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['usuario_entpac']."</td>";//usuario_entrega

					//RECIBO
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$row['fecha_recpac']."</td>";
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$row['hora_recpac']."</td>";
					$texto_oculto.="<td align='center' nowrap='nowrap'>".$row['usuario_recpac']."</td>";//usuario_recibo

					//$fecha_recibo  = $row['fecha_recpac']." ".$row['hora_recpac'];
					//$tiempo_total = calcularDiferenciaFechas( $fecha_recibo, $row['fecha_solcama']." ".$row['hora_solcama'] );

					//TIEMPO TOTAL
					$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$row['tiempo_total']."</td>";
					$respuesta.="</tr>";

					$texto_oculto.="</tr>";

					$texto_oculto.="	</tbody>
										</table>
									</td>
								</tr>";

					$respuesta.=$texto_oculto;
					if($num_filas == 1 and $internet_explorer === true){
						$respuesta.="<tr style='display:none'><td colspan=3></td></tr>";
					}
					$i++;
					$j++;
				}
			}

			$respuesta.= "</table>";
			$respuesta.= "</center>";
			$respuesta.='</div>';

		}else{
			$respuesta = "No hay traslados con los parametros elegidos";
		}
		echo $respuesta;


	}
	
	
	function calcularDiferenciaFechas($fecha_hora, $fecha_hora2){
		global $conex;
		if( !isset($fecha_hora) && !isset($fecha_hora2) ){
			return;
		}
		
		$query = "SELECT TIMEDIFF(  '".$fecha_hora."',  '".$fecha_hora2."' ) as diferencia";
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
		global $wccosSU;
		global $wactualiz;
		//Se imprimen variables ocultas
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		echo "<center>";
		
		encabezado("REPORTE TRASLADADOS DESDE URGENCIAS",$wactualiz,"clinica");
		
		if(!isset($_SESSION['user'])){
			echo "Error";
			return;
		}
		
		$fecha_hoy = date("Y-m-d");
		$fecha_pddm = date("Y-m");
		$fecha_pddm.="-01";
		
		echo '<span class="subtituloPagina2">Parámetros de consulta</span>';
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
		echo "<td class='fila1'>Servicio destino</td>";
		echo "<td class='fila2' align='center'>";
		echo "<select id='servicio'> <option value=''>Todos</option>";
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
				
				.oculto th{
					font-weight:normal;
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
					realizarConsulta();
				});
				
				$("#enlace_retornar").click(function() {
					restablecer_pagina();
				});
				
				//esconde el elemento
				$("#enlace_retornar").hide();
			});
			
			//PARA CONTROLAR EL ERROR QUE SE PRODUCE EN INTERNET EXPLORER
				//ERROR QUE SE PRODUCE PORQUE EL ROWSPAN EN INTERNET EXPLORER DEBE INCLUIR LOS ELEMENTOS OCULTOS
			
			function mostrarOculto(idElemento, elemento){
				//ESCONDE O MUESTRA LA FILA OCULTA 
				//EXTIENDE U RECORTA EL ROWSPAN DE LA COLUMNA DE CENTRO DE COSTOS

				var ocultar = false;
			 
				var rowspan_old = $(elemento).find('td[rowspan]').attr("rowspan");
				
				if( $(elemento).hasClass('fila_unica') == true){
					rowspan_old = 1;
				}
				var booleano = false;
				
				if( rowspan_old != undefined){
					booleano = true;
				}else{
					rowspan_old = $(elemento).prevAll('tr:has(td[rowspan]):first').find('td[rowspan]').attr("rowspan");
				}

				
				if ( document.getElementById(idElemento).style.display==''){
					document.getElementById(idElemento).style.display='none';
					rowspan_old = parseInt(rowspan_old)-1;	
					ocultar = true;
			    }
				else{
					document.getElementById(idElemento).style.display='';
					rowspan_old = parseInt(rowspan_old)+1;	
				}
				
				//si el navegador es internet explorer no tiene que agregar o quitar un rowspan
				if ($.browser.msie ){

					if ( $(elemento).hasClass('ultimo_del_colspan') == true){						
						if(booleano){
							if(ocultar == false && $(elemento).hasClass('fila_unica') == true)
							$(elemento).find('td[rowspan]').attr("rowspan", rowspan_old);
						}else{
							$(elemento)
								.prevAll('tr:has(td[rowspan]):first')
								.find('td[rowspan]').attr("rowspan", rowspan_old);
						}
						if($(elemento).hasClass('fila_unica') == false){
							if(ocultar == false){
								$("#"+idElemento).after("<tr style='display:none'><td colspan=2></td></tr>");
							}else{
								$("#"+idElemento).next("tr").remove();
							}	
						}						
					}
					return;
				}
			
				if(booleano){
					$(elemento).find('td[rowspan]').attr("rowspan",rowspan_old);
				}else{
				$(elemento)
					.prevAll('tr:has(td[rowspan]):first')
					.find('td[rowspan]').attr("rowspan",rowspan_old);
				}
					
			}
			
			function restablecer_pagina(){
				$("#enlace_retornar").hide();
				$("#fechas").fadeIn('slow');
				$(".subtituloPagina2").fadeIn('slow');
				
				$('#resultados').html("");
			}
			
			function realizarConsulta(){
				//muestra el mensaje de cargando
				$.blockUI({ message: $('#msjEspere') });
				
				$("#enlace_retornar").fadeIn('slow');
				
				var f_inicial = $("#f_inicial").val();
				var f_final = $("#f_final").val();
				var wemp_pmla = $("#wemp_pmla").val();
				var servicio =$("#servicio").val();
				
				//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
						
				//Realiza el llamado ajax con los parametros de busqueda
				$.get('rep_trasladosDesdeUrgencias.php', { wemp_pmla: wemp_pmla, action: "consultar", fecha_inicial: f_inicial, fecha_final: f_final, servicio: servicio, consultaAjax: aleatorio } ,
					function(data) {
						//oculta el mensaje de cargando
						$.unblockUI();
						//imprime resultado
						$('#resultados').html(data);
						//lleva mens. emergente a los elementos con la clase msg_tooltip
						$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });						
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
