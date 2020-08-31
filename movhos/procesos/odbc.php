<html>
<head>
<title>
Administraci�n de la Conexi�n con UNIX
</title>
</head>
<body>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size='4' face='Arial'>Administraci�n de la Conexi�n con Unix</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size='2' face='arial'> <b> odbc.php Ver. 2006-09-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

/**
 * PROGRAMA MODIFICAR EL ESTADO DE UNA CONEXI�N CON UNA BASE DE DATOS DE UNIX
 * 
 * El objetivo principal es quese modifique el estado del registro en donde se establece el puerto y 
 * la base de datos deseado.  
 * El puerto y la base de datos deben venir por par�metro dentro de la llamada al programa.
 * 
 * @author Ana MAria Betancur Vargas
 * @version 2006-09-18
 * @created 2006-09-18
 * @var Integer puerto	Puerto de UNIX con el cual se establece la conexi�n debe darsele como par�metro.
 * @var String bd 		BD con la que se debe conectar	debe entregarsele como par�metro.
 * @table 000012 SELECT, UPDATE
 * @table 000013 SELECT, INSERT
 * 
 * @modified 2007-06-12 Se cambian la variable $bd por la variable $bdO.
 * @modified 2007-06-07 Se cambian las tablas, se pone generico de la base de datos $bd. 
 * @modified 2007-06-07 Se cambian las tablas farmpda_000011 por ".$bd."_0000012 y la farmpda_000017 por ".$bd."_0000013
 * 
 */



/**
 * Verifica el estado de la conexi�n.
 * 
 * Es decir trae con los datos de pueto y base de datos la informaci�n existente en el registro 
 * de la conexi�n con el Odbc. Osea si el registro Odbc esta en 'on' o en off'
 * 
 *
 * @param Srting[4]	$puerto		puerto de UNIX con el cual se establece la conexi�n
 * @param String[] $bdO			BD con la que se debe conectar	
 * @param String[] $value		
 * @param String[3] $action		Tipo de operaci�n
 * 
 * @return Integer
 */
function EstadoDeConexion($puerto, $bdO, &$value, &$action){

	global $conex;
	global $bd;

	$q = " SELECT * "
	."       FROM ".$bd."_000012 "
	."      WHERE Puerto='".$puerto."' "
	."        AND Bd='".$bdO."'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	if($num == 1) {
		$row = mysql_fetch_array($err);
		if($row['Odbc']=='on'){
			$value='Desconectar con UNIX';
			$action='off';
		}else{
			$value='Conectar con UNIX';
			$action='on';
		}
	}
	return ($num);
}



session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	include_once('conex.php');
	

	echo "<br><br><br><table align='center' border='0' width='300'>";
	echo "<tr><td align='center'>";
	if(!isset($value)){
		/**
		 * Vertificar que la conexi�n exista y dar al usuario la posibilidad de conectarse o desconectarse segun sea el caso.
		 */
		$value="";
		$action=true;
		$estado=EstadoDeConexion($puerto, $bdO, &$value, &$action);
		if($estado == 1){
			echo "<form action='' method='POST'>";

			echo "<input type='hidden' name='puerto' value='".$puerto."'>";
			echo "<input type='hidden' name='bdO' value='".$bdO."'>";
			echo "<input type='hidden' name='action' value='".$action."'>";
			echo "<center><input type='submit' name='value' value='".$value."'>";
			echo "</form>";

		}elseif($estado == 0){
			echo "<font face='arial' color='red'><h1>NO PUEDE ACCEDERSE A ESTE SERVICIO<BR>POR FAVOR CONSULTE CON LA DIRECCI�N DE INFORM�TICA</h1></font><b><font face='arial' size='3'>La informaci�n que debe dar es: no hay una conexi�n ODBC que coincida con la especificada, para el programa odbc.php<b></font> ";


		}else if($estado > 1){
			echo "<font face='arial' color='red'><h1>NO PUEDE ACCEDERSE A ESTE SERVICIO<BR>POR FAVOR CONSULTE CON LA DIRECCI�N DE INFORM�TICA<br></font></h1><font face='arial' size='3'><b>La informaci�n que debe dar es:<b> existen muchos registros para la misma conexi�n de ODBC en el programa odbc.php</font>";
		}
	}else{

		$q = "UPDATE ".$bd."_000012 "
		."       SET Odbc='".$action."' "
		."     WHERE Puerto='".$puerto."' "
		."       AND Bd='".$bdO."' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		if($err){
			$q = "INSERT INTO ".$bd."_000013 "
			."        (   medico,          Fecha_data,           Hora_data,        Puerto,        Bd,        Tipo,    Codigo,   Seguridad) "
			." VALUES ('".$bd."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$puerto."', '".$bdO."','".$value."','".$key."', 'A-".$bd."') ";
			$err = mysql_query($q,$conex);
			echo mysql_error();
			if($err){
				if($action == 'on'){
					echo "<font face='arial' size='3' color='green'><b>La Conexi�n con Unix fue realizada satisfactoriamente, Recuerde seguir todo el protocolo de Reconexi�n con UNIX.</b></font>";
				}else{
					echo "<font face='arial' size='3' color='green'><b>La Desconexi�n con Unix fue realizada satisfactoriamente, Recuerde que usted y todos sus compa�eros deben SALIR de los Programas de Grabaci�n y Devoluci�n de cargos y volver a entrar, antes de continuar.</b></font>";
				}
			}
		}else{
			echo "<font face='arial' color='red'><h1>La operaci�n no pudo ser efectuada.<h1></font><br><font face='arial' size='3'>Intentelo nuevamente y si contin�a con problemas consulte con la Direcci�n de Inform�tica</font>";
		}
	}
	echo "</td></tr>";
	echo "</table>";
}


?>
</body>
</html>