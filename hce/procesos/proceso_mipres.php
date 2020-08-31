<?php
include_once("conex.php");

//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:	Kron que consulta las prescripciones y novedades en mipres del día anterior y las registra en Matrix.


//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2017-08-18

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
// 	2020-02-14	Jessica Madrid Mejía	- Se agrega parámetros en el llamado a la función consumirWebServicesKron() para que consuma 
// 										  el web service en los últimos dos días.
// 	2019-03-01	Jessica Madrid Mejía	- Se agrega parametro wemp_pmla en el llamado a la función consumirWebServicesKron().
// 	2017-08-28	Jessica Madrid Mejía	- Si se consumen correctamente los web services se actualiza en root_000051 en los parámetros 
// 										  ultimaFechaPrescripcionesKronMipres y ultimaFechaNovedadesKronMipres la fecha, de lo contrario 
// 										  cada vez que se ejecute el kron lo hará entre la última fecha registrada en el parámetro y 
// 										  la fecha del día anterior para garantizar que queden todos los registros de Mipres.
//--------------------------------------------------------------------------------------------------------------------------------------------

$proceso = "actualizar";



include_once("/var/www/matrix/hce/procesos/CTCmipres.php");




$fechaActual = date("Y-m-d"); 

if(isset($wfechaPrescripcion))
{
	$fechaDiaAnterior = $wfechaPrescripcion;
}
else
{
	$fechaAnterior = strtotime ( '-1 day' , strtotime ( $fechaActual ) ) ;
	$fechaDiaAnterior = date ( "Y-m-d" , $fechaAnterior );
}


if($fechaDiaAnterior<$fechaActual)
{
	$ultimaFechaKronP = consultarAliasPorAplicacion($conex,$wemp_pmla,'ultimaFechaPrescripcionesKronMipres');
	$fechaKronP = $ultimaFechaKronP;
	
	$ultimaFechaKronN = consultarAliasPorAplicacion($conex,$wemp_pmla,'ultimaFechaNovedadesKronMipres');
	$fechaKronN = $ultimaFechaKronN;	
	
	$resultadoKron = consumirWebServicesKron($fechaDiaAnterior,$ultimaFechaKronP,$fechaKronP,$ultimaFechaKronN,$fechaKronN,$wemp_pmla);
	
	echo $resultadoKron;
	
}
else
{
	echo "No se consumen los web services para la fecha actual";
}



?>