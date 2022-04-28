<?php
//$.post( "../..//interoperabilidad/procesos/interoperabilidad.php", { message: "John"} );

$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
include_once("middlewares/requestResponse.php");
// include_once("middlewares/validator.php");
// include_once("middlewares/authorization.php");

// $wemp_pmla = "01";
// $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
// $wtalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");
// $validador = new Validator();
// $auth      = new Authorization();


$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );

/**
 * Solo se deja en el log los últimos 30 días, por tanto siempre se borrar el log en una fecha específica
 */
function borrarMsgLog( $conex, $wmovhos, $fecha ){
	
	$val = false;
		
	$sql = "DELETE FROM ".$wmovhos."_000270
				  WHERE logfec = '".$fecha."'
			   ";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ". mysql_error() );
	$num = mysql_affected_rows();
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}

/**
 * Registra el mensaje en el log
 */
function registrarMsgLog( $conex, $wmovhos, $tor, $msg ){
	
	$val = false;
	
	$fecha 	= date("Y-m-d");
	$hora 	= date("H:i:s");
	
	$sql = "INSERT INTO 
				".$wmovhos."_000270(     Medico    , Fecha_data  , Hora_data  ,  Logfec     , Loghor     , Logtor    ,           Logmsg                 , Logest ,   Seguridad      ) 
							VALUES ( '".$wmovhos."', '".$fecha."', '".$hora."', '".$fecha."', '".$hora."', '".$tor."', '".mysql_escape_string( $msg )."',  'on'  , 'C-".$wmovhos."' )
			   ";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ". mysql_error() );
	$num = mysql_affected_rows();
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}

function consultarScript( $conex, $aplicacion, $empresa ){
	
	$script = "";
	
	$sql = "SELECT * 
			  FROM root_000125
			 WHERE Msgaev = '".$aplicacion."'
			   AND Msgeev = '".$empresa."'
			   ";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ". mysql_error() );
	$num = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array($res) ){
		$script = $rows['Msgrut'];
	}
	
	return $script;
}

function arraySegmentoHL( $msg ){
	
	$val = [];
	
	$items = explode( "\r", $msg );
	
	foreach( $items as $key => $value ){
		
		$item = explode( "|", trim( $value ) );
		
		$val[ $item[0] ][] = $item;
	}
	
	return $val;
}

if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	
	$content = file_get_contents("php://input");
	
	if( count($_POST) <= 0 ){
		$_POST      = json_decode( $content, true );
	}
	
	$message = false;
	if( $_POST['message'] )
		$message = $_POST['message'];
	
	if( !$message ){
		if( substr( trim( $content ), 0, 3 ) == 'MSH'  ){
			$message = $content;
			$_POST = [0];
		}
	}
	
	if( $message ){
		
		$wmovhos 	= consultarAliasPorAplicacion( $conex, '01', "movhos" );
		//Borro los registros de hace un mes
		borrarMsgLog( $conex, $wmovhos, date("Y-m-d", time()-30*24*3600) );
		
		$valCaracter = substr($message,0,1);
		if($valCaracter != "M"){
			$message = substr($message,1,-1);
		}
		$HL7 = arraySegmentoHL( $message );
		$script = consultarScript( $conex, $HL7['MSH'][0][2], $HL7['MSH'][0][3] );
			// registrarMsgLog( $conex, $wmovhos, '', $message );
		if( !empty( $script ) && file_exists( $script ) ){
			
			//El script debe encargar se procesar el protocolo HL7
			///Esto quiere decir que el script también conocerá la variable $HL7, el cual es un arreglo con cada segmento del protocolo HL7
			//Otra varible importante es la respuesta y el conex
			include( $script );
		}
		else{
			//Registro el msg que llegó
			registrarMsgLog( $conex, $wmovhos, '', $message );
			
			$respuesta['message'] 	= 'Petición desconocida';
			endRoutine( $respuesta, 404 );
		}
	}
	else{
		// //Registro el msg que llegó
		registrarMsgLog( $conex, "movhos", 'SABBAG', $content );
		// registrarMsgLog( $conex, "movhos", 'HIRUKO', json_decode( file_get_contents("php://input"), true ) );
		// registrarMsgLog( $conex, "movhos", 'HIRUKO', json_decode( $_POST, true ) );
		
		
		$respuesta['message'] 	= 'Parametros no validos';
		endRoutine( $respuesta, 400 );
	}
}
