<?php
include_once("conex.php");
/*
 * Sincronización Maestro de especialidades medicas
 * 
 * Creado:  	Mauricio Sánchez
 * 
 * Tabla involucrada:
 * 
 * ctc_000001: Especialidades medicas
 */
include_once("/root/comun.php");

//Apertura de conexion a unix
$conex_o = odbc_connect('facturacion','','');

if($conex_o){
	
	//Apertura de conexion a Matrix
	$conex = obtenerConexionBD("matrix");

	$query = "TRUNCATE ".$bd."_000001 "; // Elimina el contenido de la tabla
	$err = mysql_query($query,$conex);

	$query = "LOCK TABLE ".$bd."_000001 "; // Bloqueo la tabla
	$err = mysql_query($query,$conex);

	$rr=1;
	$query= " SELECT espcod, espnom FROM inesp ";

	$err_o= odbc_do($conex_o,$query);
	while(odbc_fetch_row($err_o))
	{
		$q = "INSERT INTO 
					".$bd."_000001 (Medico, Fecha_data, Hora_data, Espcod, Espnom, Seguridad)
		      VALUES 
		      		('".$bd."', '".date('Y-m-d')."', '".date('H:i:s')."', '".odbc_result($err_o,1)."', '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,2)))."', 'A-ctc') ";
		$err = mysql_query($q,$conex);
		if (($errComun=mysql_error()) != "")
		{
			echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$rr.") ".$errComun."</b><br>".$q."</font><br>";
			$rr++;
		}
	}
	$query = "UNLOCK TABLES";
	$err = mysql_query($query,$conex);
	echo "<font color='#57C8D5' face='Arial'><b>Tiempo de ejecucion inesp:".(time())."</b></font><br>";

	//Liberación de ambas conexiones
	liberarConexionBD($conex);
	//odbc_close($conex_o);
	
	odbc_close($conex_o);
	odbc_close_all();

}
?>