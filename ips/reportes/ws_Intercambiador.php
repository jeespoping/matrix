<?php

/** ===========================================================================================|
 * TODO: WEBSERVICE PARA EL PROCESO DE INTERCAMBIADOR DE DOCUMENTOS
 * ============================================================================================|
 * * REPORTE					:	DESCARGAS DE SOPORTES
 * * FECHA CREACIÓN				:	2021-07-30
 * * FECHA ULTIMA ACTUALIZACIÓN	:	2021-08-02
 * * DESCRIPCIÓN				:	Realiza peticiones a los web services del proyecto toyota 
 * 									para llevar a cabo el proceso de intercambio de documentos
 * 									de un servidor a otro
 * 			

 * ============================================================================================|
 * TODO: ACTUALIZACIONES
 * ============================================================================================|
 * . @update [2021-08-02]	-	Se modifica la manera de obtener los datos para ejecutar las peticiones
 */

if(isset($_POST['accion'])) {
	
	$url = $_POST['url'];
	$accion = $_POST['accion'];
	
	$ch = curl_init();
	
	switch($accion){
		case 'informationToInteroperability':
			
			$options = array(
				CURLOPT_URL                 	=> $url,
				CURLOPT_HEADER                 	=> false,
				CURLOPT_RETURNTRANSFER 			=> true
			);
			
			curl_setopt_array($ch, $options);
		
		break;
		
		case 'generateObject':
			
			$request = $_POST['data'];
		
			$patients = $request['patients'];
			$contab   = $request['contab'];
			$wemppmla = $request['wemppmla'];
			
			$data = [
				'patients'  => $patients,
				'contab'    => $contab,
				'wemppmla'  => $wemppmla
			];
			
			$options = array(
				CURLOPT_URL                 	=> $url,
				CURLOPT_HEADER                 	=> false,
				CURLOPT_POSTFIELDS             	=> $data,
				CURLOPT_RETURNTRANSFER 			=> true,
				CURLOPT_CUSTOMREQUEST         	=> 'POST',
			);
		
			curl_setopt_array($ch, $options);
		break;
		
		case 'putDocument':
			$request = $_POST['data'];
		
			$patients 		  = $request['patients'];
			$supportsParients = $request['supportsParients'];
			$contab   		  = $request['contab'];
			$wemppmla 	      = $request['wemppmla'];
			$accessData       = $request['accessData'];

			$data = [
				'patients'  		=> $patients,
				'supportsParients'  => $supportsParients,
				'contab'    		=> $contab,
				'wemppmla'  		=> $wemppmla,
				'accessData'        => $accessData
			];
			
			$options = array(
				CURLOPT_URL                 	=> $url,
				CURLOPT_HEADER                 	=> false,
				CURLOPT_POSTFIELDS             	=> $data,
				CURLOPT_RETURNTRANSFER 			=> true,
				CURLOPT_CUSTOMREQUEST         	=> 'POST',
			);
		
			curl_setopt_array($ch, $options);
		break;

		case 'multipleStore':
			$request = $_POST['data'];
		
			$patients 		  = $request['patients'];
			$info             = $request['info'];
			
			$data = [
				'uniques'  		=> $patients,
				'commons'  => $info,
			];
			
			$options = array(
				CURLOPT_URL                 	=> $url,
				CURLOPT_HEADER                 	=> false,
				CURLOPT_POSTFIELDS             	=> http_build_query($data),
				CURLOPT_RETURNTRANSFER 			=> true,
				CURLOPT_CUSTOMREQUEST         	=> 'POST',
			);
		
			curl_setopt_array($ch, $options);
		break;
	}
	
	$response = curl_exec($ch);
	curl_close($ch);
	
	
	echo $response;

}
