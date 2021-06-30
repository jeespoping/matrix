<?php
include_once("root/comun.php");
include_once("./funcionesGeneralesEnvioHL7.php");
include_once("./interoperabilidad_ws.php");
$wemp_pmla=$_POST['wemp_pmla'];
$historia=$_POST['historia'];
$ingreso=$_POST['ingreso'];
$numeroOrden=$_POST['numeroOrden'];
$tipoOrden=$_POST['tipoOrden'];



$wmovhos=consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$wcliame=consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
$whce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
$paciente = informacionPaciente( $conex, $wemp_pmla, $historia, $ingreso );
$estudios=consultarEstudios( $conex, $whce,$wcliame,$tipoOrden, $numeroOrden,$paciente);
$conexionSabbag = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'ipHL7Sabbag' );


function marcarOrdenesEnviadasSabbag( $conex, $whce, $wbasedato, $tor, $nro ){
	
	$sql = "UPDATE ".$whce."_000028 a
			   SET Detenv = 'off', 
				   Deteex = 'O'
			 WHERE Dettor = '".$tor."'
			   AND Detnro = '".$nro."'
		";

	$resEnv	= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
	$sql = "UPDATE ".$wbasedato."_000159 a
			   SET Detenv = 'off', 
				   Deteex = 'O'
			 WHERE Dettor = '".$tor."'
			   AND Detnro = '".$nro."'
		";

	$resEnv	= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
}

function armarMsgHL7($paciente,$estudio){
	$estado = '';
	if($estudio['estadoEstudio'] =='P'){
        $estado = 'NW';
	}elseif($estudio['estadoEstudio'] == 'C'){
        $estado = 'CA';
	}
	
	
	$mensaje =	"\013"."MSH|^~\&|MATRIX|PMLA|SABBAG|MEDILAB|".date("Ymdhis")."||ORM^O01|449||2.5||||||8859/1\r"
					."PID||".$paciente['nroDocumento']."^".$paciente['tipoDocumento']."|".$paciente['historia']."||".$paciente['apellido1']." ".$paciente['apellido2']."^".$paciente['nombre1']."||".date("Ymdhis", strtotime($paciente['fechaNacimiento']))."|".$paciente['genero']."|".$paciente['apellido1']."^".$paciente['nombre1']."||".$paciente['direccion']."^^".$paciente['municipio']."^^".$paciente['codigomMunicipio']."^^P||".$paciente['telefono']."|".$paciente['celular']."||\r"
					."PV1||I||".$paciente['habitacion']."|||||||||||||||".$paciente['ingreso']."|||||||||||||||||||||||||".$paciente['fechaAdmision']."\r"
					."IN1|||CPA|||||\r"
					."ORC|".$estado."|A04-".$estudio['numeroOrden']."-".$estudio['numeroItemEnOrden']."|||SC||".date("Ymdhis", strtotime($estudio['fechaOrdenamiento']))."|||||||\r"
					."OBR||".$estudio['tipoOrden']."-".$estudio['numeroOrden']."-".$estudio['numeroItemEnOrden']."||".$estudio['cups']."^".$estudio['nombreCups']."||".date("Ymdhis", strtotime($estudio['fechaOrdenamiento']))."|||||||"."Tarifa Estudio: ".$estudio['tarifaEstudio']."||||||||||||||||||".$estudio['justificacionEstudio']."\034\015";
return $mensaje;
}
function enviarMsgHL7($mensaje,$conexionSabbag){
		
	$sufijo  = "\034\015";
	$prefijo = "\013";
	
	/*$host = "10.90.128.250";
	$port = "3300";*/

    list($host,$port)=explode(":",$conexionSabbag);
	
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	$result = socket_connect($socket, $host, $port);
	
	
  
    if( $socket )
	{
		// if( $val = fwrite($socket, utf8_encode( $texto ) ) )
		if( $val = socket_write( $socket, utf8_encode( $mensaje ), strlen( utf8_encode( $mensaje ) ) ) )
		{
			echo "Esperando respuesta...";
			// $result = socket_read ($socket, 6000) or die("Could not read server response\n");
			
			// $val = fread( $socket, utf8_encode($val) );
			
			$recibo = "";
			$startTime = time();
			echo date( "Y-m-d H:i:s", $startTime )."<br>";
			while( ($val = socket_read( $socket, 1024 )) !== false )
			{
				$recibo .= $val;
				
				if( preg_match('/' . "\034\015" . '$/', $recibo) )
				{
					break;
				}
				
				if( (time() - $startTime) > 10 ) {
					die( "No estoy recibiendo..." );
				}
			}
			
			return $recibo;
			
			
			
		}
		else{
			echo "No escribió en socket";
		}
	}
	else{
		echo "Sin conectar...$errno-$errstr";
	}
	
}

	

foreach($estudios as $estudio ){

	
	try {
		$mensaje=armarMsgHL7($paciente,$estudio);
		
		print_r($paciente);
	    $respuestaSocket=enviarMsgHL7($mensaje,$conexionSabbag);
		if($respuestaSocket !=false){
			registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'MATRIX-SABBAG', $estudio['tipoOrden'], $estudio['numeroOrden'],$estudio['numeroItemEnOrden'],$mensaje,$respuestaSocket );
			registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso,$estudio['tipoOrden'], $estudio['numeroOrden'], $estudio['numeroItemEnOrden'], "Mensaje enviado a Sabbag",$mensaje,$respuestaSocket );
			if( strpos($respuestaSocket,"OK")){marcarOrdenesEnviadasSabbag( $conex, $whce, $wmovhos,$estudio['tipoOrden'], $estudio['numeroOrden']);}
		}else{
			registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'MATRIX-SABBAG:respuesta : '."No se envio socket no pudo conectar", $estudio['tipoOrden'], $estudio['numeroOrden'],$estudio['numeroItemEnOrden'],$mensaje );
		    registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso,$estudio['tipoOrden'], $estudio['numeroOrden'], $estudio['numeroItemEnOrden'],"Mensaje no enviado a sabbag", $mensaje );
		}
	
		
	} catch (Exception $e) {
		registrarMsgLogHl7( $conex, $wmovhos, $historia, $ingreso, $paciente['tipoDocumento'], $paciente['nroDocumento'], 'MATRIX-SABBAG:respuesta'.$e, $estudio['tipoOrden'], $estudio['numeroOrden'],$estudio['numeroItemEnOrden'],$mensaje );
		registrarDetalleLog( $conex, $wmovhos, $historia, $ingreso,$estudio['tipoOrden'], $estudio['numeroOrden'], $estudio['numeroItemEnOrden'], 'MATRIX-SABBAG:respuesta'.$e, $mensaje );
	}
	
				

}


