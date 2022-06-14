<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
include_once("middlewares/requestResponse.php");


	/****************************************************************
	 * FUNCIONES
	 ****************************************************************/
	//Funcionalidad para el consumo de la APi de wompi :)
	function llamarws_x_curl($method, $url2, $data){
		//method = POST , GET , PUT
		// $url = direccion del servicio web .
		// $data es el JSON en texto , usar json_encode.
		//	$url = replace


		$url = str_replace(" " , "" , $url2);//quitar los espacios 5 julio 2019
		//https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/AnularProgramacion/800149026/h9948isYdO3Gf9OHavJgp03wcX6aFnq8zPeq5bmvkdY=/ 2707748
		//aqui hay un error con el espacios

		$result = array();//" error ";
		if($ch = curl_init($url)) {
			if ($data != ""){
				$fields = $data ;
				//json_encode($data);//utf8_encode($data);//json_decode($data);//(is_array($data)) ? json_encode($data) : $data; //http_build_query($data)


				/*

				$arrOpciones = array ('Content-Type:'.'application/json',
				'Accept:'.'application/json');//curl no soporta arreglos asociativos ."Content-Type" => " application/json" );
				*/
				$arrOpciones = array ('Content-Length: ' . strlen($fields),
								  'Content-Type:' . ' application/json ');


			}else{
				$arrOpciones = array('Content-Type: application/json');
			}

			switch ($method){
				case "POST":
					curl_setopt($ch, CURLOPT_POST, 1);
					if ($data != ""){
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

					}
					break;

				case "PUT":

					if ($data != ""){//$data){//
						curl_setopt($ch, CURLOPT_POST, 0);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

					}else{//anular
						curl_setopt($ch, CURLOPT_PUT, true);//linea que faltaba para que funcionara el PUT
					}

					break;

				default:
					if ($data != ""){
						$url = sprintf("%s?%s", $url, http_build_query($data));
					}
					break;
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			if ($data != ""){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $arrOpciones); //,'Content-Type:'." multipart/form-data"

				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

			}

			$result = curl_exec($ch);
			$error = "";// curl_error($ch);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			//$msg = "Error ";
			$msg = "";//suponer que no hay error.
			if (($status>=200) && ($status <= 299) ){
				$msg = "";
			}elseif (($status>=300) && ($status <= 399) ){
				$msg = "Error ".$status;
			}elseif (($status>=400) && ($status <= 499) ){
				switch($status ){
					case 400:
						$msg = "Bad Request ";
						break;
					case 403:
						$msg = "Forbidden";
						break;
					case 404:
						$msg = "Not Found";
						break;
					case 411:
						$msg = "Error 411";
						break;
					case 415:
						$msg = "Error 415 ";
						break;
					case 422://ya fue programado
						$msb = "Error 422 direccionamiento ya programado o informacion ya enviada (duplicada) ";
						//El direccionamiento 1732432 ya fue programado
						break;
					default:
						$msg = "Error >400 = ".$status;
						break;
				}//switch($status )
			}elseif (($status >= 500) && ($status <= 599) ){
				switch($status ){
					case 500:
						$msg = "Internal Server Error";
						break;
					case 503:
						$msg = "Service Unavailable";
						break;
					default:
						$msg = "Error >500 = ".$status;
						break;

				}
			}else{
				$msg = "Error <".$status."> ";
			}

			curl_close($ch);
			if ($msg  != ""){//si hay error

				//echo "<script>jAlert('$error Error del web service : $result <br>$status $msg<br>$url<br>Token del dia en: tokenDiarioWebserviceSuministroMipres','ALERTA')</script>";


				return "Error| $msg $url $status $result <br> $data ";

			}//if ($msg  != "")



			$result = str_replace("\\","",$result );//Importante eliminar los \

			return json_decode($result , true );//respuesta correcta ,convierte el resultado a un Arreglo Json
			//1 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto

		}//	if($ch = curl_init($url))
		return "Error|En metodo curl_init";
	}//function llamarws_x_curl($method, $url, $data)

	function consultarAPIbyId($id, $env){
			if (isset($env)) {
				if ($env == 'test') {
					$ruta = 'https://sandbox.wompi.co/v1/transactions/'.$id;
				}else{
					$ruta = 'https://production.wompi.co/v1/transactions/'.$id;					
				}
			}
			else{
				//$ruta_pruebas = 'https://sandbox.wompi.co/v1/transactions/'.$id;
				$ruta = 'https://sandbox.wompi.co/v1/transactions/'.$id;
			}
			
			
			$arrinfo =  llamarws_x_curl("GET", $ruta, "");//file_get_contents($url2, false);
			return $arrinfo;
	}//function fbuscarxdireccionamiento()

	function consultarParametros( $conex, $wemp_pmla, $tipo ){
		$val = []; // iniciar array
		$sql = "SELECT Ppgcit, Ppglog  FROM root_000131  WHERE Ppgemp = '".$wemp_pmla."' AND Ppgtip = '".$tipo."' AND Ppgest = 'on';";
		$res = mysql_query( $sql, $conex );	
		if( $res ){		
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$rows = mysql_fetch_array($res);
				$val = [ 
					'tablacitas' => $rows['Ppgcit'],
					'tablalog' 	 => $rows['Ppglog'],
				];
			}
		}
		else{
			die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		}
		
		return $val;
	}

	/**********************************************************************************
	 *	Consulta las tablas de configuración para la pasarela de pago requerida
	 **********************************************************************************/ 
	function registrarLog( $conex, $tablalog, $json, $msg ){	
		$val = false;	
		$medico = explode( "_", $tablalog )[0];
		$fecha 	= date( "Y-m-d" );
		$hora 	= date( "H:i:s" );
		$ref 	= $json->data->transaction->reference;
		$id 	= $json->data->transaction->id;
		$res 	= $json->data->transaction->status;

		if( !empty($id) )
		{	
			$sql = "INSERT INTO ".$tablalog."(    Medico    ,  Fecha_data  , Hora_data ,    Logref ,  Logidm  ,    Logres ,   Logmsg  , Logest,   Seguridad    , id   )
			VALUES( '".$medico."', '".$fecha."' ,'".$hora."', '".$ref."', '".$id."', '".$res."', '".$msg."',  'on' , 'C-".$medico."', NULL ) ";
			$res = mysql_query( $sql, $conex );		
			if( $res )
			{
				if( mysql_affected_rows() > 0 ){
					$val = true;
				}
			}
			else{
				die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			}
		}	
		return $val;
	}

	/**********************************************************************************
	 *	Consulta las tablas de configuración para la pasarela de pago requerida
	 **********************************************************************************/ 
	function actualizarConfirmacionPago( $conex, $tablacitas, $json ){	
		$val = false;
		$ref 	= $json->data->transaction->reference;
		$id 	= $json->data->transaction->reference;
		$res 	= $json->data->transaction->status;
		if( !empty($id) && is_numeric($id) )
		{		
			$sql = "UPDATE ".$tablacitas."  SET drvpcf = 'on' WHERE id = ".$id." ";
			$res = mysql_query( $sql, $conex );		
			if( $res )
			{
				if( mysql_affected_rows() > 0 ){
					$val = true;
				}
			}
			else{
				die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			}
		}	
		return $val;
	}

	function verificarCitaByRefencia( $conex, $tablacitas, $referencia, $valor_referencia ){	
		$val = false;
		if( !empty($referencia) && is_numeric($referencia) )
		{			
			$sql = "SELECT drvidp, drvvcr FROM ".$tablacitas." WHERE drvest = 'on' AND drvvcr = ".$valor_referencia." AND id = ".$referencia." ";
			$res = mysql_query( $sql, $conex );		
			if( $res )
			{
				if( mysql_affected_rows() > 0 ){
					$val = true;
				}
			}
			else{
				die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			}
		}	
		return $val;
	}

	function actualizarConfirmacionByReferencia( $conex, $tablacitas, $referencia, $valor_referencia ){	
		$val = false;
		if( !empty($referencia) && is_numeric($referencia) )
		{		
			$sql = "UPDATE ".$tablacitas." SET drvpcf = 'on' WHERE id = ".$referencia." ";
			$res = mysql_query( $sql, $conex );		
			if( $res )
			{
				if( mysql_affected_rows() > 0 ){
					$val = true;
				}
			}
			else{
				die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			}
		}	
		return $val;
	}

	$wactualiz = '2020-10-15';
	$respuesta = array('message'=>'', 'result'=>array(), 'tipo'=>'', 'status'=>'' );
	if(!isset($wemp_pmla)){
		$wemp_pmla = "01";
	}
	$pasarela= 'drivethru';
	$id_pago =  $_GET['id'];
	$content 	= file_get_contents("php://input");
	$parametros = consultarParametros( $conex, $wemp_pmla, $pasarela );
	if( $parametros ){
		$tablacitas = $parametros['tablacitas'];
		$tablalog 	= $parametros['tablalog'];
	}else{
		$tablacitas = 'citaslc_000032';
		$tablalog 	= 'citaslc_000037';
	}

	//header("Content-Type: application/json");
	$request_method = $_SERVER['REQUEST_METHOD'];
	switch ($request_method) {
		case 'POST':
			$_POST =json_decode($content, true);
			$json = json_decode( $content );
			sleep(5);
			
			//Se valida el tipo de evento recibido :)
			if( $json->event == 'transaction.updated' ){
				//Solo si el contenido trae informacion de una transacción se registra el log :)
				registrarLog( $conex, $tablalog, $json, $content );
				//Se valida el estado del pago :)
				if( $json->data->transaction->status == 'APPROVED' ){
					$updated = actualizarConfirmacionPago( $conex, $tablacitas, $json );				
					if( $updated ){
						$respuesta['message'] = 'La solicitud recibida. El pago fue confirmado.';
						$respuesta['result'] = ($_POST);
						$respuesta['tipo'] = 'success';
						$respuesta['status'] = '200';
					}
					else{
						$respuesta['message'] = 'La solicitud se ha recibido, pero aún no se ha confirmado el pago.';
						$respuesta['result'] = ($_POST);
						$respuesta['tipo'] = 'danger';
						$respuesta['status'] = '202';
					}
				}
				else{
					$respuesta['message'] = 'La petición se ha completado con éxito pero el pago no fue autorizado.';
					$respuesta['result'] = ($_POST);
					$respuesta['tipo'] = 'danger';
					$respuesta['status'] = '204';
				}
			}
			else{
				$respuesta['message'] = 'Servidor no pudo interpretar la solicitud dada una sintaxis inválida.';
				$respuesta['result'] = ($_POST);
				$respuesta['tipo'] = 'danger';
				$respuesta['status'] = '400';
			}
			//echo json_encode( $respuesta );
		break;
		case 'GET':
			if (isset($_GET['id'])) {
				sleep(1);
				//Se consulta la informacion con del id en la api :)
				$datos_API 		= consultarAPIbyId( $_GET['id'], $_GET['env'] );
				//Validar los datos solo si no se produjeron errores :)
				if ( !is_string($datos_API) ){
					//Se toman los datos de referencia :)
					$referencia 	= $datos_API['data']['reference'];
					$valor_ 		= $datos_API['data']['amount_in_cents'];
					$valor 			= ($valor_/100);
					$estado			= $datos_API['data']['status'];
					//Si el estado de la información de la API es aceptada :)
					if( $estado == 'APPROVED' ){
						//Se actualiza información teniendo en cuenta la referencia :)
						$updated = actualizarConfirmacionByReferencia( $conex, $tablacitas, $referencia, $valor );
						if ($updated) {
							$respuesta['message']='La solicitud recibida. El pago fue CONFIRMADO y VALIDADO.';
							$respuesta['tipo'] = 'success';
							$respuesta['status'] = '200';
						}else{
							$respuesta['message'] = 'La solicitud recibida. El pago fue CONFIRMADO.';
							$respuesta['tipo'] = 'success';
							$respuesta['status'] = '204';
						}
					}else{
						$respuesta['message'] = 'La solicitud recibida. Pero el pago fue DECLINADO.';
						$respuesta['tipo'] = 'danger';
						$respuesta['status'] = '404';
					}
				}else{
					$respuesta['message'] = 'El servidor no pudo interpretar la solicitud dada una sintaxis inválida.';
					$respuesta['tipo'] = 'danger';
					$respuesta['status'] = '400';
				}
			}else{
				$respuesta['message'] = 'El servidor no pudo interpretar la solicitud dada una sintaxis inválida.';
				$respuesta['tipo'] = 'danger';
				$respuesta['status'] = '400';
			}
		break;
		case 'PUT':
			$_PUT =json_decode($content, true);
			$respuesta['message'] = 'Se recibe mensaje por PUT para actualizar la información';
			$respuesta['result'] = ($_PUT);
			$respuesta['status'] = '200';
			//echo json_encode( $respuesta );
		break;
		case 'DELETE':
			$respuesta['message'] = 'Se recibe mensaje por DELETE';
			//$respuesta['result'] = json_encode($_GET['id']);
			$respuesta['status'] = '200';
			//echo json_encode( $respuesta );
		break;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibiendo pasarela de pago</title>
    <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.css">
    <link type="text/css" rel="stylesheet" href="../../../include/root/matrix.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.css"> -->
</head>
<body>
	
    <div class="container">
    	<?php encabezado("<h2>Recibiendo pasarela de pago.</h2>", $wactualiz, "logo_labmed");  ?>
    	<div class="alert alert-<?php echo $respuesta['tipo']; ?>" role="alert">	
		<ul>
			<li><?php echo $respuesta['message']; ?></li>
			<li>Una vez nuestro sistema identifique la cancelaci&oacute;n del pago, ser&aacute; contactado por nuestro personal para realzar la asignaci&oacute;n de su cita y admisi&oacute;n, proceso para el cual es indispensable que cuente con todos demogr&aacute;ficos del paciente.</li>
		</ul>
		</div>
		<b>Nos transformamos para cuidarte.</b>
		<p class="text-justify">Recuerde que sus datos personales ser&aacute;n tratados por Auna Las Am&eacute;ricas (Promotora M&eacute;dica Las Am&eacute;ricas S.A. y sus filiales), de acuerdo con su Pol&iacute;tica de Tratamiento, para los fines relacionados con su objeto social y en especial para los siguientes fines: Atenci&oacute;n por toma de muestra del laboratorio. En todo caso, en cualquier momento y de acuerdo con la ley 1581 de 2012, puede revocar el consentimiento y ejercer su derecho a la supresi&oacute;n de los mismos.</p>
		<p class="text-justify"> Esperamos haber entregado la informaci&oacute;n necesaria para que tenga una buena experiencia con su atenci&oacute;n. Si tiene alguna inquietud, puede comunicarse con nosotros a trav&eacute;s de este mismo correo electr&oacute;nico o a la l&iacute;nea telef&oacute;nica 3227900. Su horario de atenci&oacute;n es de lunes a viernes de 7:00 am a 5:00 pm y s&aacute;bados 7:00 am a 1:00 pm.</p>
    </div>    
</body>
</html>
<?php
	header( "refresh:10;url=https://laboratoriomedico.lasamericas.com.co/" ); 
?>
