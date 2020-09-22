<?php
include_once("conex.php");
/********************************************************************************************************************************
 * Actualizado:
 *
 * Octubre 19 de 2018
 * Se hacen cambios necesarios para la grabación de servicio farmaceutico del día 21 de octubre de 2018 de acuerdo al requerimiento
 * 1710-4752
 * SF: Solicitamos  el favor que  el dia 21 octubre a las 2 am permita grabar hasta las 14 horas. y  a las 10 am se reestableca las rondas normales. 
 * Junio 06 de 2018
 * Se hacen cambios necesarios para la grabación del día 13 de junio de 2018
 * Mayo 24 de 2018
		 Buenas tardes:

		Para: Juan Carlos

		Asunto: Corte de energia Miercoles 30 de Mayo. 

		Solicitamos el favor que las rondas de hospitalizacion, sean de la siguiente forma: 
		8 am: Permita grabar 10,12,14
		10 am: Permita grabar 16,18
		12 m: Permita grabar 20,22
		4 pm: Permita grabar 00, 2 am

		Gracias

		Beatriz O. 

		
 * Mayo 4 de 2018
		  Buenos dias

		Asunto: inventario en el centro de costos 1050 Domingo  6 de mayo de 2018

		Rondas de grabacion de medicamentos

		Solicitamos el favor de programarnos la ronda de medicamentos asi:

		Mayo 5:  
		Hospitalizacion:  A las 20 horas: grabar 22 y 00
		Uci,Ucet3, Ucet4, 1t4 y 4t4: A las 22 horas: grabar 2 am, 4 am, 6 am

		Mayo 6: 
		Hospitalizacion: A las 00 horas: grabar 2am, 4 am, 6 am
		Hospitalizacion: A las 2 am: grabar 8 am
		Hospitalizacion: A las 4 am: grabar 10 am y 12m

		Uci,Ucet3, Ucet4, 1t4 y 4t4: A las 4 am: grabar 8 am, 10 am y 12.

		Gracias

		Beatriz O. 

 * Abril 22 de 2018.	Se hacen cambios necesarios para la dispensación del 22 de Abril de 2018 según requerimiento 1710-4573 de Beatriz Orrego:
 
	 Buenas tardes Juan Carlos

	Asunto: Corte de energia Abril 22

	Ronda Hospitalizacion:

	A las 00: Que el sistema  permita grabar las rondas de 2 am, 4 am y 6 am
	A las 4 am: Que el sistema permta grabar las rondas 8 am , 10 am
	A las 8 am: Que el sistema permita grabar las rondas 12, 14, 16
	A las 10 am: Que el sistema permita grabar las rondas 18,20, 22

	Ronda UCI, UCE, 4T4, 1T4

	A las 4 am: Que el sistema permita grabar las rondas de: 1 am, 10 am y 12
	A las 8 am: Que el sistema permita grabar las rondas de: 14, 16 ,18
	A las 10 am: Que el sistema permta grabar las rondas de: 20, 22, 24


	Gracias

	Beatriz O. 
 
 * Noviembre 17 de 2017.	Se hacen cambios necesarios para la dispensación del 19 de Noviembre de 2017 sergún requerimiento 1710-4434 de Beatriz Orrego:
					Buenas tardes

					Para: Juan Carlos

					Asunto: Inventario Servicio Farmaceutico Nov 19 de 2017

					Con el fin de facilitar la toma del inventario, solicitamos para este dia, cambiar los horarios de grabacion de las rondas.

					A las 00:00 horas: Grabar  rondas de 2 am y 4 am de los centros de costos: 1182, 1183,1184,1185,1186,1283,1285.

					A las 02:00 horas: Grabar  rondas de 6 am, 8 am  y 10 am de los centros de costos: 1182, 1183,1184,1185,1186,1283,1285.

					A las 04:00 horas: Grabar  rondas de 8 am, 10 am  y 12 pm de los centros de costos: 1020, 1180,1281,1282,1284.

					Gracias

					Beatriz O.
 * Julio 21 de 2017.		Se hacen los cambios necesarios para el día 20 de Julio de 2017.
 * Febrero 28 de 2017.		Se agrega funcion para activar los CTC de responsable contributivo el 1 de marzo de 2017 a las 00:00:00
 * Enero 31 de 2017.		Se hacen modificaciones para el proceso de grabación del 5 de Febrero de 2017. Este cambio incluye
 *							la rondas a aplicar según el tiempo del evento.
 * Septiembre 23 de 2016.	Se hacen modificaciones para el proceso de grabación del 25 de Septiembre de 2016
 * Junio 03 de 2016.		Se hacen modificaciones para el proceso de grabación del 5 de junio de 2016
 * Enero 20 de 2016.		Se actualiza para el proceso de grabación del 20 de Enero de 2015.
 * Septiembre 08 de 2015.	Se actualiza para el proceso de grabación del 09 de Septiembre de 2015.
 * Noviembre 16 de 2012.	Se actualiza para el proceso del 17 de Noviembre de 2012.
 *							Solo se puede grabar hasta las 12 horas, a las 14 se restaura como estaba anteriormente
 ********************************************************************************************************************************/

 /********************************************************************************
 * Consulta el valor de una aplicacion en root_000051
 ********************************************************************************/ 
function cambiarHoraCorte( $conex, $emp, $hora ){

	$val = false;

	$sql = "UPDATE root_000051
			   SET detval = '".$hora."'
			 WHERE detapl = 'horaCorteDispensacion'
				AND detemp = '$emp'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return $val;
} 
 
function activarCTCcontributivo( $conex, $emp )
{
	$val = false;

	$sql = "UPDATE root_000051
			   SET detval = 'on'
			 WHERE detapl = 'CTCcontributivo'
				AND detemp = '$emp'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = true;
	}

	return $val;
}

function cambiarRondasAAplicar( $conex, $emp, $rondas ){

	$val = false;

	$sql = "UPDATE root_000051
			   SET detval = '".$rondas."'
			 WHERE detapl = 'mostrarRondasAnteriorsIpods'
				AND detemp = '$emp'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = true;
	}

	return $val;
}

function consultarAliasPorAplicacion_Proc( $conex, $emp, $aplicacion ){

	$val = '';

	$sql = "SELECT
				*
			FROM
				root_000051
			WHERE
				detapl = '$aplicacion'
				AND detemp = '$emp'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 'Detval' ];
	}

	return $val;
}

// En esta función se actualizan los horarios de dispensación para SF, CM y por centro de costos, se deben configurar los 
// horarios como estaban antes de iniciar el proceso de cambio de horas de dispensación.
function restaurar(){

	global $conex;
	global $wmovhos;

	// restaurar los horarios de dispensación de CM
	$sql = "UPDATE
				{$wmovhos}_000099
			SET
				tarhcd = '14:00:00'
			WHERE
				tarcod != 'LC';
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	// restaurar los horarios de dispensación de SF
	$sql = "UPDATE
				{$wmovhos}_000099
			SET
				tarhcd = '14:00:00'
			WHERE
				tarcod = 'A'
				OR tarcod = 'N';
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	//restaurar los horarios de dispensación por centro de costos
	$sql = "UPDATE
				{$wmovhos}_000011
			SET
				ccotdi = '14:00:00'
			WHERE
				ccotdi != '00:00:00'
				AND ccohos = 'on'
				AND ccocod != '1189'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
}

function restaurar2( $ccos, $hora ){

	global $conex;
	global $wmovhos;
	
	$sql = "UPDATE
				{$wmovhos}_000011
			SET
				ccotdi = '$hora'
			WHERE
				ccohos = 'on'
				AND ccocod != '1189'
				AND ccocod IN( $ccos )
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
}

// En esta función se cambian los horarios de dispensación en la tabla movhos_000099 para los artículos de SF y CM, 
// también se modifican los horarios de dispensación en movhos_000011 para los centros de costos que tienen definido
// tiempo de dispensación
function cambiarHoraDispensacion( $hora, $hora2, $horacm, $ccos ){
	
	global $conex;
	global $wmovhos;
	
	$val = false;

	// articulos de servicio farmaceutico
	$sql = "UPDATE
				{$wmovhos}_000099
			SET
				tarhcd = '$hora'
			WHERE
				tarcod IN( 'A', 'N' )
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	// // articulos de central de mezclas
	$sql = "UPDATE
				{$wmovhos}_000099
			SET
				tarhcd = '$horacm'
			WHERE
				tarcod NOT IN( 'LC', 'A', 'N' )
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	
	// cambiar los horarios de dispensación de los cco que tienen un horario diferente a cero
	$hora2 = $hora;
	$sql = "UPDATE
				{$wmovhos}_000011
			SET
				ccotdi = '$hora2'
			WHERE ccohos = 'on'
				AND ccocod != '1189'
				AND ccotdi != '00:00:00'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}

global $wmovhos;

$wmovhos = consultarAliasPorAplicacion_Proc( $conex, $emp, "movhos" );

// En este switch cada vez que abren los  programas de cargos (SF o CM), de acuerdo a la ronda se actual (case) se determinan 
// las horas de dispensación para SF y CM para modificarlas en la función cambiarHoraDispensacion(). También se deben restaurar
// el horario de dispensación con la función restaurar(), donde se deben configurar las horas normales de dispensación (como estaba 
// antes de iniciar el proceso de modificar horas de grabación)

// $hora : cantidad de horas que se puede dispensar en la ronda especifica para servicio farmaceutico
// $horacm : cantidad de horas que se puede dispensar en la ronda especifica para central de mezclas
// $hora2 : cantidad de horas que se puede dispensar en la ronda especifica por centro de costos
$hora = "";

if( date("Y-m-d") == '2020-09-19' ){
	
	switch( floor(date( "H" )/2)*2 ){
		
		case 16:
		case 18:
		case 20:
		case 22:
			cambiarHoraCorte( $conex, $emp, '22' );
			$hora = "30:00:00";
			$hora2 = "30:00:00";
			$horacm = "30:00:00";
			cambiarHoraDispensacion( $hora, $hora2, $horacm, $ccos );
			
			break;
		
		default: break;
	}
}
else if( date("Y-m-d") == '2020-09-20' ){
	cambiarHoraCorte( $conex, $emp, '18' );
	restaurar( $ccos, $hora );
}

if( !empty($rondasAplicar) ){
	cambiarRondasAAplicar( $conex, $emp, $rondasAplicar );
}
?>