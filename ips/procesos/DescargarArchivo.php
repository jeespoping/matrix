<?php


//Descripcion

//Este programa se realiza con la finalidad generar un archivo de excel a partir del archivo csv incrementotarifas.csv generado por el script incremento_tarifas.php

//Creador : LEandro Meneses
//Fecha : 2021/02715

$filename = "IncrementoTarifas.xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);


if (($gestor = fopen("incrementotarifas.csv", "r")) !== FALSE) 
{
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) 
	{

		echo implode("\t", array_values($datos)) . "\n";
	
	}
	fclose($gestor);
}




?>