<?php
include_once("conex.php");
/**
 PROGRAMA                   : ws_cliente_mantenimiento.php
 AUTOR                      : Edwar Jaramillo - Jonatan Lopez.
 FECHA CREACION             : 11 Julio de 2013

 DESCRIPCION:
 Este script se encarga de conectarse o consumir un servidor webservice asociado al siftware AM de mantenimiento, al momento de crear este script se permitía consumir un web service
 para guardar una nueva solicitud de un requerimiento en el sistema AM luego de haberlo almacenado primero en Matrix.
 tendrán efecto en la actualización.

 */ $wactualiza = "(Julio 12 de 2018)"; /*
 ACTUALIZACIONES:
 *  Edwar Jaramillo     : Fecha de la creación del cliente WS.
	Jonatan Lopez		: Se agrega la funcion generarCierreMantenimiento para que traiga los regsitros en el servidor http://132.1.18.15/amservice/AMWebService.asmx?WSDL&op= y los cargue
							en las tablas manto_000001, manto_000002, manto_000003
**/

function guardarRequerimientoMantenimiento($conex, $wbasedato, $campos_solicitud, $dir_ws, $identificador_unico_req)
{
    include_once("webservices/nusoap_mx.php");
    $error = 'off';

    /*
        This is your webservice server WSDL URL address
        $wsdl = "http://localhost/include/webservices/ws_serv_requerimiento.php?wsdl";
    */
    $wsdl = $dir_ws."NuevaSolicitud";

    //create client object
    $client = new nusoap_client($wsdl, 'wsdl');

    $err = $client->getError();
    if($err)
    {
        // Display the error
        echo '<h2>Constructor error</h2> '.$err;
        echo "<br>[!] [Origen: ws_cliente_mantenimiento => guardarRequerimientoMantenimiento] NO SE HA PODIDO CONECTAR AL WEB SERVICE. ".$wsdl;
        $error = 'on';
        //exit(' * NO conexión con ws *');
    }
    else
    {
        // Para mantenimiento el tipo de requerimientos es 09, pero desde AM todos los codigos empiezan sin cero (0), por eso aquí antes de usar este código en el query, se concatena un cero al inicio
        $campos_solicitud['OS'] = ($campos_solicitud['OS']*1);
        $campos_solicitud['DiagnosticoOS'] = substr($campos_solicitud['DiagnosticoOS'], 0, 400); // El máximo a enviar debe ser solo de 400 caracteres.
        //unset($campos_solicitud['PrioridadOS']); // en el momento no está en el web service que envió winsoft
        /*
            $campos_solicitud = array   (
                                            'OS'                  => '',
                                            'CCOS'                => '',
                                            'DiagnosticoOS'       => '',
                                            'SolicitanteOS'       => '',
                                            'EstadoOS'            => '',
                                            //'PrioridadOS'         => '',
                                            'SolicitanteEmailOS'  => ''
                                        );
        */

        // USAR FUNCIÓN SEGÚN SEA DISEÑADA EN WINSOFT
        $result1=$client->call('NuevaSolicitud', $campos_solicitud);
        /*  SI HAY ERROR AL ENVIAR NOMBRES ERRONEOS DE LOS PARÁMETROS (PERO! HAY ERRORES CUANDO SE MANDAN MENOS PARÁMETROS)
            $result1 = (
                            [faultcode] => soap:Client
                            [faultstring] => El servidor no puede leer la solicitud. ---> Error en el documento XML (1, 379). ---> La cadena de entrada no tiene el formato correcto.
                            [detail] =>
                        )

            SI HAY ERROR AL ENVIAR UN OS QUE YA ESTABA
            $result1 = (
                            [faultcode] => soap:Server
                            [faultstring] => El servidor no puede procesar la solicitud. ---> Violation of UNIQUE KEY constraint 'MATRIX_UNIQUE'. Cannot insert duplicate key in object 'dbo.OSAMERICASCONSEC'.
                        The statement has been terminated.
                            [detail] =>
                        )

            NO HAY ERRORES LA RESPUESTA ES VACÍA
            $result1 = ""
        */

         // && ($result1 != '' && (!array_key_exists("faultstring", $result1) || (array_key_exists("faultstring", $result1) && $result1['faultstring'] == '' )))
        if(empty($result1))
        {
            // Si se conectó al WS y se retornó una respuesta exitosa entonces se escribe 'on' en el estado relacionado al WS en el requerimiento.
            $q = "  UPDATE  ".$wbasedato."_000040
                            SET   Reqews = 'on'
                    WHERE   Reqtpn = '".$identificador_unico_req."'";
            $err = mysql_query($q,$conex) or die ("[!] [Origen: ws_cliente_mantenimiento => guardarRequerimientoMantenimiento] NO SE HA PODIDO REGISTRAR CORRECTAMENTE LA SOLICITUD EN EL WEB SERVICE. ".mysql_errno().':'.mysql_error());
            //echo "<pre> Respuesta exito>>> "; print_r($result1); echo "</pre>";
        }
        else
        {
            //echo "<pre> Respuesta error: >>> "; print_r($result1); echo "</pre>";
            $error = 'on';
        }
    }

    return $error;
}

function cancelarRequerimiento($conex, $wbasedato, $dir_ws, $identificador_unico_req, $westado_am)
{
    include_once("webservices/nusoap_mx.php");
    $error = 'off';
    //This is your webservice server WSDL URL address
    //$wsdl = "http://localhost/include/webservices/ws_serv_requerimiento.php?wsdl";
    $wsdl = $dir_ws."CancelarSolicitud";

    //create client object
    $client = new nusoap_client($wsdl, 'wsdl');

    $err = $client->getError();
    if($err)
    {
        // Display the error
        echo '<h2>Constructor error</h2> '.$err;
        echo "<br>[!] [Origen: ws_cliente_mantenimiento => cancelarRequerimiento] NO SE HA PODIDO CONECTAR AL WEB SERVICE. ".$wsdl;
        $error = 'on'; //exit(' * NO conexión con ws *');
    }
    else
    {
        // Para mantenimiento el tipo de requerimientos es 09, pero desde AM todos los codigos empiezan sin cero (0), por eso aquí antes de usar este código en el query, se concatena un cero al inicio
        $identificador_unico_req_am = ($identificador_unico_req*1);

        // USAR FUNCIÓN SEGÚN SEA DISEÑADA EN WINSOFT
        $result1=$client->call('CancelarSolicitud', array("OS" => $identificador_unico_req_am)); // Solo se envía código consecutivo para que el AM haga la cancelación.
        /*  SI HAY ERROR AL ENVIAR NOMBRES ERRONEOS DE LOS PARÁMETROS (PERO! HAY ERRORES CUANDO SE MANDAN MENOS PARÁMETROS)
            $result1 = (
                            [faultcode] => soap:Client
                            [faultstring] => El servidor no puede leer la solicitud. ---> Error en el documento XML (1, 379). ---> La cadena de entrada no tiene el formato correcto.
                            [detail] =>
                        )

            SI HAY ERROR AL ENVIAR UN OS QUE YA ESTABA
            $result1 = (
                            [faultcode] => soap:Server
                            [faultstring] => El servidor no puede procesar la solicitud. ---> Violation of UNIQUE KEY constraint 'MATRIX_UNIQUE'. Cannot insert duplicate key in object 'dbo.OSAMERICASCONSEC'.
                        The statement has been terminated.
                            [detail] =>
                        )

            NO HAY ERRORES LA RESPUESTA ES VACÍA
            $result1 = ""
        */

        if(empty($result1))
        {
            // Si se conectó al WS y se retornó una respuesta exitosa entonces se escribe 'on' en el estado relacionado al WS en el requerimiento.
            $q = "  UPDATE  ".$wbasedato."_000040
                            SET   Reqews = 'on'
                    WHERE   Reqtpn = '".$identificador_unico_req."'";
            $err = mysql_query($q,$conex) or die ("[!] [Origen: ws_cliente_mantenimiento => cancelarRequerimiento] NO SE HA PODIDO REGISTRAR CORRECTAMENTE LA SOLICITUD EN EL WEB SERVICE. ".mysql_errno().':'.mysql_error());
        }
        else
        {
            $error = 'on';
        }
    }

    return $error;
}

function generarCierreMantenimiento($conex, $wbasedato, $ano, $mes, $desde, $hasta){
		
    $enviar_por_ws = 'on';
    $ruta_ws       = "http://132.1.18.15/amservice/AMWebService.asmx?WSDL&op=";

    // Validar si ya se ejecutó el cierre para el mes actual
    //if(!validarMesCierreExiste($conex, $wbasedato, $ano, $mes)){
	consultarWSAmMovimientos($conex, $wbasedato, $ruta_ws, $ano, $mes, $desde, $hasta);
	//}
	
}

function consultarWSAmMovimientos($conex, $wbasedato, $ruta_ws, $ano, $mes, $fechaDesde, $fechaHasta){

    include_once("webservices/nusoap_mx.php");
    $error = 'off';
	$result1 = array();

    /*
        This is your webservice server WSDL URL address
        $wsdl = "http://localhost/include/webservices/ws_serv_requerimiento.php?wsdl";
    */
    $op = "GetKD";
    // $op = "GetOT";
    // $op = "GetRP";
    $wsdl = $ruta_ws.$op;

    //create client object
    $client = new nusoap_client($wsdl, 'wsdl');

    $err = $client->getError();
	
	if($ano != '' and $mes != ''){
		
		$numero = date("t",strtotime($ano."-".$mes."-01"));
		
		$fechaDesde = $ano."-".$mes."-01";
		$fechaHasta = $ano."-".$mes."-".$numero;	
		
	}
		
    if($err)
    {
		
        // Display the error
        echo '<h2>Constructor error</h2> '.$err;
        echo PHP_EOL."<br>[!] [Origen: ws_cliente_mantenimiento => consultarWSAmMovimientos] NO SE HA PODIDO CONECTAR AL WEB SERVICE. ".$wsdl;
        $error = 'on';
        //exit(' * NO conexión con ws *');
    }
    else
    {
        if($op == "GetKD"){
            // Para mantenimiento el tipo de requerimientos es 09, pero desde AM todos los codigos empiezan sin cero (0), por eso aquí antes de usar este código en el query, se concatena un cero al inicio
            //unset($campos_solicitud['PrioridadOS']); // en el momento no está en el web service que envió winsoft

            $campos_solicitud = array   (
                                            'desde' => $fechaDesde,
                                            'hasta' => $fechaHasta
                                        );


            // USAR FUNCIÓN SEGÚN SEA DISEÑADA EN WINSOFT
            $result1=$client->call('GetKD', $campos_solicitud);
            //echo date("H:i:s")."<br>Datos de entrada WS:<br>".print_r($campos_solicitud,true)."<pre> Respuesta error: >>> ".print_r($result1,true)."</pre>";
        } elseif($op == "GetOT"){
            $campos_solicitud = array   (
                                            'NumMatrix'           => '0954940'
                                            // 'OSAMERICASCONSEC'    => '0954908'
                                        );


            // USAR FUNCIÓN SEGÚN SEA DISEÑADA EN WINSOFT
            $result1=$client->call('GetOT', $campos_solicitud);
           // echo date("H:i:s")."<br>Datos de entrada WS:<br>".print_r($campos_solicitud,true)."<pre> Respuesta error: >>> ".print_r($result1,true)."</pre>";
        }elseif($op == "GetRP"){
            $campos_solicitud = array   (
                                            'IdeRP'           => 'DAPEQINFORM',
                                            'IdeALRP'         => '1'
                                        );


            // USAR FUNCIÓN SEGÚN SEA DISEÑADA EN WINSOFT
            $result1=$client->call('GetRP', $campos_solicitud);
           // echo date("H:i:s")."<br>Datos de entrada WS:<br>".print_r($campos_solicitud,true)."<pre> Respuesta error: >>> ".print_r($result1,true)."</pre>";
        }

        //exit();
        /*  SI HAY ERROR AL ENVIAR NOMBRES ERRONEOS DE LOS PARÁMETROS (PERO! HAY ERRORES CUANDO SE MANDAN MENOS PARÁMETROS)
            $result1 = (
                            [faultcode] => soap:Client
                            [faultstring] => El servidor no puede leer la solicitud. ---> Error en el documento XML (1, 379). ---> La cadena de entrada no tiene el formato correcto.
                            [detail] =>
                        )

            SI HAY ERROR AL ENVIAR UN OS QUE YA ESTABA
            $result1 = (
                            [faultcode] => soap:Server
                            [faultstring] => El servidor no puede procesar la solicitud. ---> Violation of UNIQUE KEY constraint 'MATRIX_UNIQUE'. Cannot insert duplicate key in object 'dbo.OSAMERICASCONSEC'.
                        The statement has been terminated.
                            [detail] =>
                        )

            NO HAY ERRORES LA RESPUESTA ES VACÍA
            $result1 = ""
        */	
		
		//Recorrer $result1 para insertar la informacion en la tabla.	
        $fecha_actual = date("Y-m-d");
        $hora_actual = date("H:i:s");        
        $insert_ok = false;
		if (is_array($result1['GetKDResult']) || is_object($result1['GetKDResult'])){
				
				foreach($result1 as $key => $GetKDResult){
					
					foreach($GetKDResult as $key1 => $datos_array){	
						
						//Cuando solo es un encabezado y un detalle entra por aqui
						if(count($datos_array['Movimientos']) == 1){
								
								$q = "  SELECT * 
										  FROM ".$wbasedato."_000003
										 WHERE IdEncDlle = '".$value['Id']."'
										   AND RegestDlle = 'on'";
								$err = mysql_query($q,$conex) or die ("No se puedo realizar la consulta $q ".mysql_errno().':'.mysql_error());
								$num = mysql_num_rows($err);
								
								//Si el registro no existe en la tabla lo inserta.
								if($num == 0){
								//if(true){
								
									$Reganc = date("Y"); // año cierre
									$Regmec = date("m"); // mes cierre
									$row = array();
									$row['IdEnc'] = $datos_array['Id'];
									$row['Codigo'] = $datos_array['Codigo'];
									$row['Almacen'] = $datos_array['Almacen'];
									$row['Tipo'] = $datos_array['Tipo'];							
									$fecha_hora_enc = $datos_array['FechaCreacion'];
									
									$array_fecha_enc = explode(" ",$fecha_hora_enc);
									$dato_fecha_enc = explode("/",$array_fecha_enc[0]);
									$fecha_creacion_enc = $dato_fecha_enc[2]."-".$dato_fecha_enc[1]."-".$dato_fecha_enc[0];
									$dato_hora_enc = explode(":",$array_fecha_enc[1]);
									$hora_creacion_enc = $dato_hora_enc[0].":".$dato_hora_enc[1].":".$dato_hora_enc[2];
																
									$row['FechaCreacion'] = $fecha_creacion_enc;
									$row['HoraCreacion'] = $hora_creacion_enc;
									
									$row['Concepto'] = $datos_array['Concepto'];
									$row['Responsable'] = $datos_array['Responsable'];
									$row['IdCentroCosto'] = $datos_array['IdCentroCosto'];
									$row['Cerrado'] = $datos_array['Cerrado'];
									$row['Regest'] = 'on';
									$row['Seguridad'] = 'C-manto';
									
									// Si se conectó al WS y se retornó una respuesta exitosa entonces se escribe 'on' en el estado relacionado al WS en el requerimiento.
									$q = "  INSERT INTO  {$wbasedato}_000002
													(Medico,
													Fecha_data,
													Hora_data,
													Reganc,
													Regmec,
													IdEnc,
													Codigo,
													Almacen,
													Tipo,
													FechaCreacion,
													HoraCreacion,
													Concepto,
													Responsable,
													CentroCostos,
													Cerrado,
													Regest,
													Seguridad)
											VALUES  ('{$wbasedato}',
													'{$fecha_actual}',
													'{$hora_actual}',
													'{$Reganc}',
													'{$Regmec}',
													'{$row['IdEnc']}',
													'{$row['Codigo']}',
													'{$row['Almacen']}',
													'{$row['Tipo']}',
													'{$row['FechaCreacion']}',
													'{$row['HoraCreacion']}',
													'{$row['Concepto']}',
													'{$row['Responsable']}',
													'{$row['IdCentroCosto']}',
													'{$row['Cerrado']}',
													'{$row['Regest']}',
													'{$row['Seguridad']}')";
													
									if($err = mysql_query($q,$conex)){
										// echo "<pre>".print_r($q,true)."</pre>";
										$insert_ok = true;
											
											$row2 = array();
											$row2['IdDlle'] = $datos_array['Movimientos']['GetKXResult']['Id'];
											$row2['NumKCDlle'] = $datos_array['Movimientos']['GetKXResult']['NumKC'];
											$row2['AlmacenDlle'] = $datos_array['Movimientos']['GetKXResult']['Almacen'];
											$fecha_hora_cre = $datos_array['Movimientos']['GetKXResult']['FechaCreacion'];
											
											$array_fecha = explode(" ",$fecha_hora_cre);
											$dato_fecha = explode("/",$array_fecha[0]);
											$fecha_creacion = $dato_fecha[2]."-".$dato_fecha[1]."-".$dato_fecha[0];
											$dato_hora = explode(":",$array_fecha[1]);
											$hora_creacion = $dato_hora[0].":".$dato_hora[1].":".$dato_hora[2];
											
											$fecha_hora_reg = $datos_array['Movimientos']['GetKXResult']['FechaRegistro'];											
											$array_fecha_reg = explode(" ",$fecha_hora_reg);
											$dato_fecha_reg = explode("/",$array_fecha_reg[0]);
											$fecha_registro = $dato_fecha_reg[2]."-".$dato_fecha_reg[1]."-".$dato_fecha_reg[0];
											$dato_hora_reg = explode(":",$array_fecha_reg[1]);
											$hora_registro = $dato_hora_reg[0].":".$dato_hora_reg[1].":".$dato_hora_reg[2];
																						
											$row2['FechaCreacionDlle'] = $fecha_creacion;																						
											$row2['HoraCreacionDlle'] = $hora_creacion;											
											$row2['RepuestoCodigoDlle'] = $datos_array['Movimientos']['GetKXResult']['RepuestoCodigo']; 
											$row2['RepuestoDescripcionDlle'] = $datos_array['Movimientos']['GetKXResult']['RepuestoDescripcion'];
											$row2['AnhoOTDlle'] = $datos_array['Movimientos']['GetKXResult']['AnhoOT'];
											$row2['NumOTDlle'] = $datos_array['Movimientos']['GetKXResult']['NumOT'];
											$row2['EquipoCodigoDlle'] = $datos_array['Movimientos']['GetKXResult']['EquipoCodigo'];
											$row2['EquipoDescripcionDlle'] = $datos_array['Movimientos']['GetKXResult']['EquipoDescripcion'];
											$row2['CantidadDlle'] = $datos_array['Movimientos']['GetKXResult']['Cantidad'];
											$row2['ValorUnitarioDlle'] = $datos_array['Movimientos']['GetKXResult']['ValorUnitario'];
											$row2['IvaDlle'] = $datos_array['Movimientos']['GetKXResult']['Iva'];
											$row2['ComentarioDlle'] = $datos_array['Movimientos']['GetKXResult']['Comentario'];
											$row2['CantidadAnteriorDlle'] = $datos_array['Movimientos']['GetKXResult']['CantidadAnterior'];
											$row2['FechaRegistroDlle'] = $fecha_registro." ".$hora_registro;
											$row2['InventarioActualDlle'] = $datos_array['Movimientos']['GetKXResult']['InventarioActual'];
											$datos_array['Movimientos']['GetKXResult']['ValorUnitarioRP'] = str_replace(",",".",$datos_array['Movimientos']['GetKXResult']['ValorUnitarioRP']);											
											$row2['ValorUnitarioRpDlle'] = $datos_array['Movimientos']['GetKXResult']['ValorUnitarioRP'];
											$row2['KDDlle'] = 'KD';
											$row2['RegestDlle'] = 'on';
											$row2['Seguridad'] = 'C-manto';

											$qDlle = "  INSERT INTO  {$wbasedato}_000003
															(Medico,
															Fecha_data,
															Hora_data,
															IdEncDlle,
															IdDlle,
															NumKCDlle,
															AlmacenDlle,
															FechaCreacionDlle,
															HoraCreacionDlle,
															RepuestoCodigoDlle,
															RepuestoDescripcionDlle,
															AnhoOTDlle,
															NumOTDlle,
															EquipoCodigoDlle,
															EquipoDescripcionDlle,
															CantidadDlle,
															ValorUnitarioDlle,
															IvaDlle,
															ComentarioDlle,
															CantidadAnteriorDlle,
															FechaRegistroDlle,
															InventarioActualDlle,
															ValorUnitarioRpDlle,
															KDDlle,
															RegestDlle,
															Seguridad)
													VALUES  ('{$wbasedato}',
															'{$fecha_actual}',
															'{$hora_actual}',
															'{$row['IdEnc']}',
															'{$row2['IdDlle']}',
															'{$row2['NumKCDlle']}',
															'{$row2['AlmacenDlle']}',
															'{$row2['FechaCreacionDlle']}',
															'{$row2['HoraCreacionDlle']}',
															'{$row2['RepuestoCodigoDlle']}',
															'{$row2['RepuestoDescripcionDlle']}',
															'{$row2['AnhoOTDlle']}',
															'{$row2['NumOTDlle']}',
															'{$row2['EquipoCodigoDlle']}',
															'{$row2['EquipoDescripcionDlle']}',
															'{$row2['CantidadDlle']}',
															'{$row2['ValorUnitarioDlle']}',
															'{$row2['IvaDlle']}',
															'{$row2['ComentarioDlle']}',
															'{$row2['CantidadAnteriorDlle']}',
															'{$row2['FechaRegistroDlle']}',
															'{$row2['InventarioActualDlle']}',
															'{$row2['ValorUnitarioRpDlle']}',
															'{$row2['KDDlle']}',
															'{$row2['RegestDlle']}',
															'{$row2['Seguridad']}')";
											if($errDlle = mysql_query($qDlle,$conex)){
												// echo "<pre>".print_r($q,true)."</pre>";
												$insert_ok = true;
											} else {
												$insert_ok = false;
												echo ("[!] [Origen: ws_cliente_mantenimiento => consultarWSAmMovimientos] NO SE HA PODIDO GUARDAR EL DETALLE CORRECTAMENTE. ".mysql_errno().':'.mysql_error()).PHP_EOL;
											}									
									} else {
										echo ("[!] [Origen: ws_cliente_mantenimiento => consultarWSAmMovimientos] NO SE HA PODIDO GUARDAR EL ENCABEZADO CORRECTAMENTE. ".mysql_errno().':'.mysql_error()).PHP_EOL;
									}							
								}
								
						}
						else{
							
							$array_codigos_sin_mov = array();
							//echo "<pre>".print_r($datos_array,true)."</pre>";
							
							foreach($datos_array as $key2 => $value){
								
								$q = "  SELECT * 
										  FROM ".$wbasedato."_000003
										 WHERE IdEncDlle = '".$value['Id']."'
										   AND RegestDlle = 'on'";
								$err = mysql_query($q,$conex) or die ("No se puedo realizar la consulta $q ".mysql_errno().':'.mysql_error());
								$num = mysql_num_rows($err);
								
								//Si el registro no existe en la tabla lo inserta.
								if($num == 0){
								//if(true){
								
									$Reganc = date("Y"); // año cierre
									$Regmec = date("m"); // mes cierre
									$row = array();
									$row['IdEnc'] = $value['Id'];
									$row['Codigo'] = $value['Codigo'];
									$row['Almacen'] = $value['Almacen'];
									$row['Tipo'] = $value['Tipo'];							
									$fecha_hora_enc = $value['FechaCreacion'];
									
									$array_fecha_enc = explode(" ",$fecha_hora_enc);
									$dato_fecha_enc = explode("/",$array_fecha_enc[0]);
									$fecha_creacion_enc = $dato_fecha_enc[2]."-".$dato_fecha_enc[1]."-".$dato_fecha_enc[0];
									$dato_hora_enc = explode(":",$array_fecha_enc[1]);
									$hora_creacion_enc = $dato_hora_enc[0].":".$dato_hora_enc[1].":".$dato_hora_enc[2];
																
									$row['FechaCreacion'] = $fecha_creacion_enc;
									$row['HoraCreacion'] = $hora_creacion_enc;
									
									$row['Concepto'] = $value['Concepto'];
									$row['Responsable'] = $value['Responsable'];
									$row['IdCentroCosto'] = $value['IdCentroCosto'];
									$row['Cerrado'] = $value['Cerrado'];
									$row['Regest'] = 'on';
									$row['Seguridad'] = 'C-manto';
									
									// Si se conectó al WS y se retornó una respuesta exitosa entonces se escribe 'on' en el estado relacionado al WS en el requerimiento.
									$q = "  INSERT INTO  {$wbasedato}_000002
													(Medico,
													Fecha_data,
													Hora_data,
													Reganc,
													Regmec,
													IdEnc,
													Codigo,
													Almacen,
													Tipo,
													FechaCreacion,
													HoraCreacion,
													Concepto,
													Responsable,
													CentroCostos,
													Cerrado,
													Regest,
													Seguridad)
											VALUES  ('{$wbasedato}',
													'{$fecha_actual}',
													'{$hora_actual}',
													'{$Reganc}',
													'{$Regmec}',
													'{$row['IdEnc']}',
													'{$row['Codigo']}',
													'{$row['Almacen']}',
													'{$row['Tipo']}',
													'{$row['FechaCreacion']}',
													'{$row['HoraCreacion']}',
													'{$row['Concepto']}',
													'{$row['Responsable']}',
													'{$row['IdCentroCosto']}',
													'{$row['Cerrado']}',
													'{$row['Regest']}',
													'{$row['Seguridad']}')";
													
									if($err = mysql_query($q,$conex)){
																				
										if(is_array($value['Movimientos']['GetKXResult'])){
										
											$insert_ok = true;									
											$keyActual = key($value['Movimientos']['GetKXResult']);
											
											if( $keyActual === "Id" ){
												$aux = array();
												$aux = $value['Movimientos']['GetKXResult'];
												unset( $value['Movimientos']['GetKXResult'] );
												$value['Movimientos']['GetKXResult'] = array();
												array_push( $value['Movimientos']['GetKXResult'], $aux );
											}
											
											foreach($value['Movimientos']['GetKXResult'] as $key3 => $datos_detalle){
																				
												
														$row2 = array();
														$row2['IdDlle'] = $datos_detalle['Id'];
														$row2['NumKCDlle'] = $datos_detalle['NumKC'];
														$row2['AlmacenDlle'] = $datos_detalle['Almacen'];
														$fecha_hora_cre = $datos_detalle['FechaCreacion'];
														
														$array_fecha = explode(" ",$fecha_hora_cre);
														$dato_fecha = explode("/",$array_fecha[0]);
														$fecha_creacion = $dato_fecha[2]."-".$dato_fecha[1]."-".$dato_fecha[0];
														$dato_hora = explode(":",$array_fecha[1]);
														$hora_creacion = $dato_hora[0].":".$dato_hora[1].":".$dato_hora[2];
														
														$fecha_hora_reg = $datos_detalle['FechaRegistro'];											
														$array_fecha_reg = explode(" ",$fecha_hora_reg);
														$dato_fecha_reg = explode("/",$array_fecha_reg[0]);
														$fecha_registro = $dato_fecha_reg[2]."-".$dato_fecha_reg[1]."-".$dato_fecha_reg[0];
														$dato_hora_reg = explode(":",$array_fecha_reg[1]);
														$hora_registro = $dato_hora_reg[0].":".$dato_hora_reg[1].":".$dato_hora_reg[2];
																									
														$row2['FechaCreacionDlle'] = $fecha_creacion;																						
														$row2['HoraCreacionDlle'] = $hora_creacion;											
														$row2['RepuestoCodigoDlle'] = $datos_detalle['RepuestoCodigo']; 
														$row2['RepuestoDescripcionDlle'] = $datos_detalle['RepuestoDescripcion'];
														$row2['AnhoOTDlle'] = $datos_detalle['AnhoOT'];
														$row2['NumOTDlle'] = $datos_detalle['NumOT'];
														$row2['EquipoCodigoDlle'] = $datos_detalle['EquipoCodigo'];
														$row2['EquipoDescripcionDlle'] = $datos_detalle['EquipoDescripcion'];
														$row2['CantidadDlle'] = $datos_detalle['Cantidad'];
														$row2['ValorUnitarioDlle'] = $datos_detalle['ValorUnitario'];
														$row2['IvaDlle'] = $datos_detalle['Iva'];
														$row2['ComentarioDlle'] = $datos_detalle['Comentario'];
														$row2['CantidadAnteriorDlle'] = $datos_detalle['CantidadAnterior'];
														$row2['FechaRegistroDlle'] = $fecha_registro." ".$hora_registro;
														$row2['InventarioActualDlle'] = $datos_detalle['InventarioActual'];
														$datos_detalle['ValorUnitarioRP'] = str_replace(",",".",$datos_detalle['ValorUnitarioRP']);											
														$row2['ValorUnitarioRpDlle'] = $datos_detalle['ValorUnitarioRP'];
														$row2['KDDlle'] = 'KD';
														$row2['RegestDlle'] = 'on';
														$row2['Seguridad'] = 'C-manto';

														$qDlle = "  INSERT INTO  {$wbasedato}_000003
																		(Medico,
																		Fecha_data,
																		Hora_data,
																		IdEncDlle,
																		IdDlle,
																		NumKCDlle,
																		AlmacenDlle,
																		FechaCreacionDlle,
																		HoraCreacionDlle,
																		RepuestoCodigoDlle,
																		RepuestoDescripcionDlle,
																		AnhoOTDlle,
																		NumOTDlle,
																		EquipoCodigoDlle,
																		EquipoDescripcionDlle,
																		CantidadDlle,
																		ValorUnitarioDlle,
																		IvaDlle,
																		ComentarioDlle,
																		CantidadAnteriorDlle,
																		FechaRegistroDlle,
																		InventarioActualDlle,
																		ValorUnitarioRpDlle,
																		KDDlle,
																		RegestDlle,
																		Seguridad)
																VALUES  ('{$wbasedato}',
																		'{$fecha_actual}',
																		'{$hora_actual}',
																		'{$row['IdEnc']}',
																		'{$row2['IdDlle']}',
																		'{$row2['NumKCDlle']}',
																		'{$row2['AlmacenDlle']}',
																		'{$row2['FechaCreacionDlle']}',
																		'{$row2['HoraCreacionDlle']}',
																		'{$row2['RepuestoCodigoDlle']}',
																		'{$row2['RepuestoDescripcionDlle']}',
																		'{$row2['AnhoOTDlle']}',
																		'{$row2['NumOTDlle']}',
																		'{$row2['EquipoCodigoDlle']}',
																		'{$row2['EquipoDescripcionDlle']}',
																		'{$row2['CantidadDlle']}',
																		'{$row2['ValorUnitarioDlle']}',
																		'{$row2['IvaDlle']}',
																		'{$row2['ComentarioDlle']}',
																		'{$row2['CantidadAnteriorDlle']}',
																		'{$row2['FechaRegistroDlle']}',
																		'{$row2['InventarioActualDlle']}',
																		'{$row2['ValorUnitarioRpDlle']}',
																		'{$row2['KDDlle']}',
																		'{$row2['RegestDlle']}',
																		'{$row2['Seguridad']}')";
														if($errDlle = mysql_query($qDlle,$conex)){													
															$insert_ok = true;
														} else {
															$insert_ok = false;
															echo ("[!] [Origen: ws_cliente_mantenimiento => consultarWSAmMovimientos] NO SE HA PODIDO GUARDAR EL DETALLE CORRECTAMENTE. ".mysql_errno().':'.mysql_error()).PHP_EOL;
														}	
											
											}
										}else{
											
											if(!array_key_exists($value['Id'],$array_codigos_sin_mov )){
												$array_codigos_sin_mov[$value['Id']] = $value;
											}
										}
									} else {
										echo ("[!] [Origen: ws_cliente_mantenimiento => consultarWSAmMovimientos] NO SE HA PODIDO GUARDAR EL ENCABEZADO CORRECTAMENTE. ".mysql_errno().':'.mysql_error()).PHP_EOL;
									}							
								}
							}
						}
					}            				
				}
				
				encabezado("<div class='titulopagina2'>Servicio web mantenimiento</div>", $wactualiza, "clinica");
				
				if(count($array_codigos_sin_mov) == 0){
				
					echo "<div align=center><b>Datos cargados correctamente!</b></div><br><br>";
				
				}else{
					
					echo "<div align=center><b>Algunos registros del AM no fueron cargados en Matrix, los registros son los siguientes:</b></div><br><br>";
					
					echo "<table align=center>";
					echo "<tr class=encabezadotabla><td>Id</td><td>Almacen</td><td>Codigo</td><td>Tipo</td><td>FechaCreacion</td><td>Concepto</td><td>Responsable</td><td>CentroCostos</td><td>Cerrado</td></tr>";
					$i = 0;
					foreach($array_codigos_sin_mov as $key => $value){
												
						if ($i % 2 == 0)
							
						   $wclass = "fila1";
						  else
							$wclass = "fila2";
						echo "<tr class='{$wclass}'><td>{$value['Id']}</td><td>{$value['Almacen']}</td><td>{$value['Codigo']}</td><td>{$value['Tipo']}</td><td>{$value['FechaCreacion']}</td><td>{$value['Concepto']}</td><td>{$value['Responsable']}</td><td>{$value['CentroCostos']}</td><td>{$value['Cerrado']}</td></tr>";
						
						$i++;
					}
					
					echo "</table>";
					
					//echo "<pre>".print_r($array_codigos_sin_mov,true)."</pre>";
					
				
				}
				
				registrarCierreMes($conex, $wbasedato, $ano, $mes);
				
				echo "<table align='center'>
							<tr><td align='center' colspan='9'><input type='button' value='Regresar' onclick='history.back()'><input type='button' value='Cerrar Ventana' onclick='cerrarVentanaPpal();'></td></tr>
						</table>";
			
        }
        else
        {
            //echo "<pre> Respuesta error: >>> "; print_r($result1); echo "</pre>";
			encabezado("<div class='titulopagina2'>Servicio web mantenimiento</div>", $wactualiza, "clinica");
				echo "<div align=center>No hay datos para cargar en este mes.</div><br><br>
						<table align='center'>
							<tr><td align='center' colspan='9'><input type='button' value='Regresar' onclick='history.back()'><input type='button' value='Cerrar Ventana' onclick='cerrarVentanaPpal();'></td></tr>
						</table>";
            $error = 'on';
        }
    }

    return $error;
}

function validarMesCierreExiste($conex, $wbasedato, $anio, $mes){
    $generado = false;
    $sql = "SELECT id
              FROM {$wbasedato}_000001
             WHERE Logani = '{$anio}'
               AND Logmes = '{$mes}'";
    if($result = mysql_query($sql,$conex)) {
        if(mysql_num_rows($result) > 0){
            $generado = true;
        }
    } else {
        echo "NO SE HA PODIDO CONSULTAR LOG MESES GENERADOS.<br>".mysql_errno().'-'.mysql_error()." => <br>".$sql."<br>";
    }
    return $generado;
}

function registrarCierreMes($conex, $wbasedato, $anio, $mes){
    
	$user_session = explode('-',$_SESSION['user']);
    $user_session = $user_session[1];
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i:s");
    $log_txt = "|{$fecha_actual} {$hora_actual},usuario:{$user_session},intento:";

    $sql = "";
    if(validarMesCierreExiste($conex, $wbasedato, $anio, $mes)){
        $sql = "UPDATE {$wbasedato}_000001
                        SET Lognin = Lognin + 1,
                            Loguge = '{$user_session}',
                            Loglog = CONCAT(Loglog,'{$log_txt}',Lognin + 1,'|')
                 WHERE Logani = '{$anio}'
                   AND Logmes = '{$mes}'";
		$res = mysql_query($sql,$conex);
		
    } else {
        $log_txt = $log_txt."1"; // intento
        $sql = "INSERT INTO {$wbasedato}_000001
                            (Medico, Fecha_data, Hora_data, Logani, Logmes, Lognin, Loguge, Loglog, Logest, Seguridad)
                VALUES      ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}', '{$anio}', '{$mes}', '1', '{$user_session}', '{$log_txt}', 'on', 'C-{$user_session}')";
		$res = mysql_query($sql,$conex);
		
    }
}
?>