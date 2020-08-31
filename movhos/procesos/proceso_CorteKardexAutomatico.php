<?php
include_once("conex.php");
/********************************************************************************
 * Agosto 9 de 2011 (Edwin MG)
 * Quita la marca de kardex generado automaticamente, para todos los pacientes
 * en el dia
 ********************************************************************************/
 
 /********************************************************************************
 * Consulta la hora de corte para crear el kardex automaticamente
 ********************************************************************************/
 
 function consultarHoraCorteKardex( $conex ){

	global $wemp_pmla;
	
	$sql = "SELECT
				Detval
			FROM
				root_000051
			WHERE
				detapl = 'Hora corte kardex'
				AND detemp = '$wemp_pmla'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0){
		
		if( $rows =  mysql_fetch_array( $res ) ){
		
			return $rows[0];
		}
		else{
			return false;
		}
	}
	else{
		$val = false;
	}
	
	return $val;
}

/********************************************************************************
  * Agosto 3 de 2011
  * Si el kardex fue generado automaticamente, los campos karcon y kadare son
  * desactivados 
  ********************************************************************************/
 function quitandoKardexAutomatico( $conex, $wbasedato, $fecha ){
	
	$sql = "UPDATE
				{$wbasedato}_000053 a, 
				{$wbasedato}_000054 b
			SET
				karcon = 'off',
				karaut = 'off',
				kadare = 'off',
				kadcon = 'off'
			WHERE
				a.fecha_data = '$fecha'
				AND karhis = kadhis
				AND karing = kading
				AND a.fecha_data = kadfec
				AND karaut = 'on'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	
	return false;
 }

include_once("root/comun.php"); 


mysql_select_db("matrix") or die("No se selecciono la base de datos");

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );

$corteKardex = consultarHoraCorteKardex( $conex );
			
if( $corteKardex ){

	$fecha = date( "Y-m-d" );
	
	if( time() >= strtotime( "$fecha $corteKardex" ) ){
		quitandoKardexAutomatico( $conex, $wbasedato, $fecha );
	}
}


?>