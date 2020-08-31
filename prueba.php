<?php
$archivo	= fopen("/var/www/matrix/ips/rips/clisur/nombredelarchivo.txt", "a+") or die("Problemas en la creacion del archivo");
$log = "PRUEBA ARCHIVO";
fputs($archivo, $log);
fclose($archivo);


?>
