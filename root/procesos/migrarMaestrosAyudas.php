<?php
include_once("root/comun.php");
// --> Libreria que contiene todas las funciones y rutinas relacionadas con el sistema unix
include_once("root/kron_maestro_ayudas.php");

if($hay_unix)
{
	mysql_select_db("matrix");
	$conex 		= obtenerConexionBD("matrix");
	$wemp_pmla 	= '01';

	$ejCron = new datosDeAyudas();

	echo date("Y-m-d-H:i:s");

	// --> la variable $tiempoEjec, viene inicializada en la ruta definida por el cron programado en el servidor
	if(isset($tiempoEjec))
	{
		$tiempoEjec = trim($tiempoEjec);

		switch($tiempoEjec)
		{
			// --> Aqui van todas las ejecuciones que se realizaran cada 24 horas
			case '24':
			
				$ejCron->actualizarMaestroExamenes();
				$ejCron->actualizarMaestroAnatomias();
				$ejCron->actualizarMaestroEspecimenes();
				
			break;
			
			default: break;
		}
	}
	// --> 	Esta variable viene por url y contiene el nombre de una funcion a ejecutar, esto es para cuando
	//		se tenga la necesidad de ejecutar alguna rutina manualmente.
	if(isset($funcion))
	{
		$funcion = trim($funcion);
		$ejCron->$funcion();
	}

	echo '<br>'.date("Y-m-d-H:i:s");
}
?>