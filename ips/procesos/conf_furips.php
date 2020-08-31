<html>
<head>
<title>Modificacion datos basicos FURIPS</title>
<script type="text/javascript">

	//Envio del formulario
	function enviar(){  
		document.forma.submit();		
	}
	

	//Redirecciona a la pagina inicial
	function inicioReporte(wemp_pmla,wfecini,wfecfin,wsede,wproveedor)
	{
	 	document.location.href='uvgmoenxfe.php?wemp_pmla='+wemp_pmla+'&wfecini='+wfecini+'&wfecfin='+wfecfin+'&wsede='+wsede+'&wproveedor='+wproveedor+'&bandera=1';
	}
</script>

</head>
<body>
<?php
include_once("conex.php");
/*
 * Configuracion de datos básicos FURIPS
 */
//=================================================================================================================================

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+------------------------------------------------+
//|	 Permite modificar los datos básicos para FURIPS como son Tope aseguradora, Tope aseguradora|
//|	 estatal y Responsable FURIPS																|
//|	 Autor: Mario Cadavid																		|
//|	 Fecha: Sept. 14 de 2011																	|
//+-------------------+------------------------+------------------------------------------------+
//+-------------------+------------------------+------------------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 		|
//+-------------------+------------------------+------------------------------------------------+
//|	 2011-11-24       |   MARIO CADAVID        |   El nombre de la seguradora estatal estaba	|
//|	 quemado por lo que se cambia para que consulte en root_000051 la empresa actual encargada  |
//+-------------------+------------------------+------------------------------------------------+
	
//FECHA ULTIMA ACTUALIZACION 	: 2011-11-24

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

//Validación de usuario
$usuarioValidado = true;
if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = $institucion->baseDeDatos;
$wentidad = $institucion->nombre;
$wactualiz = 'Nov. 24 de 2011';

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("Modificación datos básicos FURIPS",$wactualiz,"logo_".$wbasedato);

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es válido
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;
	
  	$wnomprog="conf_furips.php"; 
  	
	// Consulto los datos de la aseguradora estatal actual
	$q=  " SELECT Detval "
		."	 FROM root_000051  "
		."  WHERE Detapl = 'aseguradoraEstatal' "
		."	  AND Detemp = '".$wemp_pmla."'";
	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$ase_est = explode("-",$row['Detval']);
	// Consulto el NIT de la aseguradora estatal
	$nit_ase_est = $ase_est[0];
	// Consulto el nombre de la aseguradora estatal
	$nom_ase_est = $ase_est[1];

    if (isset($resultado) && $resultado=='1')
	{
		//Update
		$query  = "UPDATE ".$wbasedato."_000049
					  SET cfgtas = '".$wcfgtas."', cfgtfo = '".$wcfgtfo."', cfgres = '".$wcfgres."' ";
		$err = mysql_query($query,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		if($err)
			echo "<div align='center' style='font-size:12px; color:#0000FF'> <b>Los datos han sido actualizados correctamente <b> </div>";
	}

	//Consulto datos basicos FURIPS
	$q=  "SELECT cfgtas, cfgtfo, cfgres "
	."      FROM ".$wbasedato."_000049 ";
	$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row1 = mysql_fetch_array($res1);
	
	if($row1)
	{
		$cfgtas = $row1['cfgtas'];
		$cfgtfo = $row1['cfgtfo'];
		$cfgres = $row1['cfgres'];
	}
	else
	{
		$cfgtas = "";
		$cfgtfo = "";
		$cfgres = "";
	}
	
  	echo "<br>";
  	echo "<form action='conf_furips.php' method=post name='forma'>";
  	echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  	echo "<input type='hidden' name='wbasedato' value='".$wbasedato."'>";

	echo "<center><table border=0 cellpadding=2 cellspacing=2>";
	 
	//Petición de ingreso de parametros
	echo "<tr>";
	echo "<td height='37' colspan='2'>";
	echo '<p align="left" class="titulo"><strong> &nbsp; Ingrese los datos básicos de FURIPS en el siguiente formulario &nbsp;  &nbsp; </strong></p>';
	echo "</td></tr>";

	//Tope Aseguradora
	echo "<tr>";
	echo "<td class=fila2 align=right><b>Tope aseguradora : </b></td><td class=fila2 align=left> <input type='text' name='wcfgtas' value='".$cfgtas."'></td>";
	echo "</tr>";
	
	//Tope aseguradora estatal
	echo "<tr>";
	echo "<td class=fila2 align=right><b>Tope ".$nom_ase_est." : </b></td><td class=fila2 align=left> <input type='text' name='wcfgtfo' value='".$cfgtfo."'></td>";
	echo "</tr>";
	
	//Responsable Furips
	echo "<tr>";
	echo "<td class=fila2 align=right><b>Responsable Furips : </b></td><td class=fila2 align=left> <input type='text' name='wcfgres' value='".$cfgres."'></td>";
	echo "</tr>";


	echo "<tr align='center'><td colspan=2>";
	echo "<br><div align='center'><input type='submit' value='Guardar'> &nbsp; | &nbsp; <input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
	echo "</td></tr>";
	
	echo "<input type='hidden' name='resultado' value='1'>";
	echo "</table>";
	echo "";
}
liberarConexionBD($conex);
?>
</body>
</html>