<?php
include_once("conex.php");
include_once("root/comun.php");
include_once("root/kron_maestro_unix.php");

if( $hay_unix ){

	

	$conex = obtenerConexionBD("matrix");

	$ejCron = new datosDeUnix();


	echo date("Y-m-d-H:i:s");

	/*$sql  = "TRUNCATE cliame_000077 ";
	mysql_query( $sql, $conex );
	$ejCron->cco_conceptos();

	$sql1 = "TRUNCATE cliame_000189 ";
	mysql_query( $sql1, $conex );
	$ejCron->maestro_nits();

	$sql2 = "TRUNCATE cliame_000029 ";
	mysql_query( $sql2, $conex );
	$ejCron->maestro_tipos_empresa();

	$sql3 = "TRUNCATE cliame_000069 ";
	mysql_query( $sql3, $conex );
	$ejCron->maestro_bancos();

	$sql5 = "TRUNCATE cliame_000196 ";
	mysql_query( $sql5, $conex );
	$ejCron->maestro_terceros_no_oficial();

	$sql6 = "TRUNCATE cliame_000004 ";
	mysql_query( $sql6, $conex );
	$ejCron->maestroGruposArticulos();

	$sql7 = "TRUNCATE cliame_000214 ";
	mysql_query( $sql7, $conex );
	$ejCron->maestro_medicamentos_empresas();

	$sql8 = "TRUNCATE cliame_000026 ";
	mysql_query( $sql8, $conex );
	$ejCron->tarifasmedicamentos();*/

	/*$sql9 = "TRUNCATE cliame_000103 ";
	mysql_query( $sql9, $conex );

	$sql10 = "TRUNCATE cliame_000104";
	mysql_query( $sql10, $conex );

	$sql12 = "TRUNCATE cliame_000070";
	mysql_query( $sql12, $conex );

	$sql11 = "TRUNCATE cliame_000192";
	mysql_query( $sql11, $conex );
	*/

	if(isset($funcion))
		$ejCron->$funcion();

	if(isset($tipo))
	{
		if($tipo == 1)
			$ejCron->insercionTarifasHomologacionDeFacturacion('E');
		if($tipo == 2)
			$ejCron->insercionTarifasHomologacionDeFacturacion('P');
		if($tipo == 3)
			$ejCron->insercionTarifasUvrGqxHabitacionesConceptos();
	}
	echo '<br>'.date("Y-m-d-H:i:s");

	echo "<br><br>
	<input type='button' value='Ejecutar' Onclick='javascript:location.reload()'>";

}
else{
	// echo "<br>NO EXISTE EL ARCHIVO...";
}
?>