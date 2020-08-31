<?php
$ip="132.1.18.85";
$salida = pingAddress("132.1.18.85");

function pingAddress($ip){ 
	$pingresult = shell_exec("ping -c 1 $ip"); 
	$dead = "Destination Host Unreachable"; 
	echo $pingresult;
	$deadoralive = strpos($dead, $pingresult); 
	echo $deadoralive;
	if ($deadoralive === false){ 
		echo "The IP address, $ip, is dead"; 
	} else { 
		echo "The IP address, $ip, is alive"; 
	}
} 

echo $salida;
?>
