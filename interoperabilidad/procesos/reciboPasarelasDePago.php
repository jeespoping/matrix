<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
include_once("middlewares/requestResponse.php");

/****************************************************************
 * FUNCIONES
 ****************************************************************/
 
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
								   VALUES( '".$medico."', '".$fecha."' ,'".$hora."', '".$ref."', '".$id."', '".$res."', '".$msg."',  'on' , 'C-".$medico."', NULL )
				 ";

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
function actualizarConfirmacionPago( $conex, $tablacitas, $id ){
	
	$val = false;

	if( !empty($id) && is_numeric($id) )
	{
		
		$sql = "UPDATE ".$tablacitas."
				   SET drvpcf = 'on'
				 WHERE id = ".$id."
				 ";

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
function consultarParametros( $conex, $wemp_pmla, $tipo ){
	
	$val = false;

	$sql = "SELECT Ppgcit, Ppglog
			  FROM root_000131
			 WHERE Ppgemp = '".$wemp_pmla."'
			   AND Ppgtip = '".$tipo."'
			   AND Ppgest = 'on';";

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

$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );

$parametros = consultarParametros( $conex, $_GET['wemp_pmla'], $_GET['pasarela'] );

if( $parametros ){
	
	$tablacitas = $parametros['tablacitas'];
	$tablalog 	= $parametros['tablalog'];
	
	$content 	= file_get_contents("php://input");
	
	if( !empty( $content ) ){
	
		$json 		= json_decode( $content );
		
		registrarLog( $conex, $tablalog, $json, $content );
		
		if( $json->event == 'transaction.updated' ){
			
			if( $json->data->transaction->status == 'APPROVED' ){
				$updated = actualizarConfirmacionPago( $conex, $tablacitas, $json->data->transaction->reference );
				
				if( $updated ){
					$respuesta['status'] 	= '1';
					$respuesta['message'] 	= 'Pago confirmado';
					endRoutine( $respuesta, 200 );
				}
				else{
					$respuesta['status'] 	= '0';
					$respuesta['message'] 	= 'Referencia no encontrada';
					endRoutine( $respuesta, 200 );
				}
			}
			else{
				$respuesta['status'] 	= '1';
				$respuesta['message'] 	= 'Pago no autorizado';
				endRoutine( $respuesta, 200 );
			}
		}
		else{
			$respuesta['status'] 	= '1';
			$respuesta['message'] 	= 'Mensaje no esperado';
			endRoutine( $respuesta, 400 );
		}
	}
	else{
		$respuesta['status'] 	= '1';
		$respuesta['message'] 	= 'Mensaje vacio';
		endRoutine( $respuesta, 400 );
	}
}
else{
	$respuesta['message'] 	= 'Pasarela no configurada correctamente';
	endRoutine( $respuesta, 400 );
}