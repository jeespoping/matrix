<?php
$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
$localIP = getHostByName(getHostName());
socket_bind($socket,$localIP,30000);
socket_listen($socket);
echo "Esperando conexión\n\n";
$client = false;
switch(@socket_select($r = array($socket), $w = array($socket), $e = array($socket), 120000)) {
  case 0:
    echo "Tiempo de espera excedido!\n\n";
  break;
  case 1:
   
		while($client = socket_accept($socket)) {
			  echo "Conexión aceptada!\n\n";
		      $buffer=socket_read($client, 60000);
			  $url = "http://".$localIP."/matrix/interoperabilidad/procesos/interoperabilidadSabbag.php?wemp_pmla=01";
				
			try{
				$response_hl7 = curl_init(); 
				curl_setopt($response_hl7, CURLOPT_URL,            $url );   
				curl_setopt($response_hl7, CURLOPT_CONNECTTIMEOUT, 10); 
				curl_setopt($response_hl7, CURLOPT_TIMEOUT,        10); 
				curl_setopt($response_hl7, CURLOPT_RETURNTRANSFER, true );
				curl_setopt($response_hl7, CURLOPT_SSL_VERIFYPEER, false);  
				curl_setopt($response_hl7, CURLOPT_SSL_VERIFYHOST, false); 
				curl_setopt($response_hl7, CURLOPT_POST,           true ); 
				curl_setopt($response_hl7, CURLOPT_POSTFIELDS,$buffer); 
				curl_setopt($response_hl7, CURLOPT_HTTPHEADER,    array('Content-Type: text/plain; charset=utf-8', 'Content-Length: '.strlen($buffer) ));
				$result = curl_exec($response_hl7);
				 echo $result;
			}catch(Exception $e){
				 echo "Error en la conexion ". $e;
			}
				
	        $texto = "\013"."MSH|^~\&|MATRIX|PMLA|GATEWAY|PMLA|".date("Ymdhis")."||ACK^ACK|".date("Ymdhis")."|P|2.3.1||||||8859/1\r"
			 ." MSA|AA|449|OK"."\034\015";
				
				//$output =$buffer. "\n";
                 socket_write($client, $texto, strlen ($texto)) or die("No se escribio en el socket\n");
			
		}
  break;

  case 2:
     echo "Conexión rechazada!\n\n";
  break;
}



?>
