<html>
<head>
<title>Creación de nuevas categorías de clientes AAA</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	
</head>
<body >

<?php
include_once("conex.php");

/**
 * SELECCION DE TIPOS DE PACIENTES AFINIDAD
 * 
 * Este programa programa permite crear tipos de clientes para afinidad
 *
 * @name matrix\magenta\procesos\tipoCliente.php
 * @author Ing. Carolina Castaño 
 * @created 2007-01-25
 * @version 2007-01-25
 * 
 * 
 * @table 000013 insert, select
 * 
 * @wvar $color variar color en la presentacion
 * @wvar $grabar ya se ha seleccionado un tipo de cliente para nueva clasificacion
 * @wvar $Tipcom decripcion de la clasificacion
 * @wvar $Tiptip nueva clasificacion
 * 
 */

/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-4 (Arleyda Insignares C.)
						-Se cambia encabezado, titulo y tablas con ultimo diseño 
*************************************************************************************************************************/

$wautor="Ana Maria Betancur";
$wmodificado="Carolina Castaño";
$wversion='2007-01-19';
$wactualiz = "2016-05-04";

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	include_once("root/comun.php");
  	// Se muestra el encabezado del programa
	$titulo    = "SISTEMA DE COMENTARIOS Y SUGERENCIAS"; 
    encabezado($titulo,$wactualiz, "clinica");  

	/**
	 * conexion unix
	 *
	 */
	

	

	$bd='facturacion';

	IF (isset($grabar))
	{
		if (strlen ($Tiptip)>3)
		{
			$grabar=0;
			echo '<script language="Javascript">';
			echo 'window.location.href=window.location.href;';
			echo 'alert ("EL NOMBRE DE LA NUEVA CATEGORIA DE USUARIO DEBE CONTENER 3 LETRAS, INTENTE NUEVAMENTE POR FAVOR")';
			echo '</script>';
		}else
		{

			$Tiptip=strtoupper($Tiptip);
			$query="select * from magenta_000013 where Tiptip='$Tiptip' ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if($num >=1)
			{
				$grabar=0;
				echo '<script language="Javascript">';
				echo 'window.location.href=window.location.href;';
				echo 'alert ("EL NOMBRE DE LA NUEVA CATEGORIA YA EXISTE, INGRESE OTRO NOMBRE POR FAVOR")';
				echo '</script>';
			}else
			{
				IF ($Tipcom=='')
				{
					echo '<script language="Javascript">';
					echo 'window.location.href=window.location.href;';
					echo 'alert ("ESCRIBA POR FAVOR ALGUNA DESCRIPCIÓN DE LA NUEVA CATEGORÍA")';
					echo '</script>';
				}else
				{
					$q="insert into magenta_000013 (medico, Fecha_data, Hora_data, Tiptip, Tipniv, Tipdes, Tipcol, Tipimg, Tipact, Tipres, Tipcom, Seguridad) ";
					$q= $q."values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$Tiptip."','1', 'AAA', 'EA198E', 'aaa.gif', 'A', '0', '".$Tipcom."', 'C-magenta')";

					$err=mysql_query($q,$conex);

					$q="insert into magenta_000013 (medico, Fecha_data, Hora_data, Tiptip, Tipniv, Tipdes, Tipcol, Tipimg, Tipact, Tipres, Tipcom, Seguridad) ";
					$q= $q."values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$Tiptip."','2', 'BBB', '0000FF', 'bbb.gif', 'A', '0', '".$Tipcom."', 'C-magenta')";

					$err=mysql_query($q,$conex);
				}
			}
		}
	}

	echo "<div align='center' class='tituloPagina'><font size='4'><A HREF='auditoria.php'>REPORTE Y CREACIÓN DE NUEVAS CATEGORIAS DE USUARIOS</a></font></div><br><br>" ;
	ECHO "<table border=0 align=center size=100%>";
	ECHO "<tr>";
	ECHO "<td>";
	echo "<center><img SRC='/MATRIX/images/medical/Magenta/AAA.gif'></center>";
	ECHO "</td>";
	ECHO "</tr>";
	ECHO "</table>";
	ECHO "</br></br></br></br><table border=0 align=center size=100%>";
	ECHO "<tr class='fila1'><td align=center ><font size=5 color='blue'>Categorías de Usuarios</font></td></tr>";
	ECHO "</table></br>";

	$query="select Tiptip, Tipcom from magenta_000013 Group by Tiptip ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);

	if($num >=1)
	{
		ECHO '<TABLE border=1 align=center>';
		ECHO "<Tr class='encabezadotabla'>";
		echo "<td align=center>Categoría de Usuario</td>";
		echo "<td align=center>Descripción</td>";
		$i=0;
		ECHO "</Tr>";

		while ($resulta = mysql_fetch_row($err))
		{
			if (is_int ($i/2))
			$color='fila1';
			else
			$color='fila2';

			ECHO "<Tr class=".$color.">";
			echo "<td >".$resulta[0].'</td>';
			echo "<td >".$resulta[1].'</td>';
			ECHO "</Tr >";
			$i++;
		}
		ECHO '</TABLE></br></br></br></br>';

	}else
	{
		echo "<table>";
		echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
		echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNA CATEGORÍA DE USUARIO SE ENCUENTRA REGITRADA</td><tr>";
		echo "</table></fieldset>";
	}
}

ECHO "<table border=0 align=center size=100%>";
ECHO "<tr class='fila1'><td align=center ><font size=5 color='blue'>Creación de nueva categoría</font></td></tr>";
ECHO "</table></br>";

echo "<fieldset border=0 align=center></br>";
echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
echo "<table align='center' size=100%>";
echo "<tr class='encabezadotabla'>";
echo "<td align=center width='250'><font size=3  face='arial'>Nueva categoría (3 letras):&nbsp</td>";
echo "<td width='200'><font size='2'  face='arial'><input type='text' name='Tiptip'  value='XXX' size='2'></td>";
echo "</tr>";
echo "<tr class='fila2'>";
echo "<td align=center width='200'><font size=2  face='arial'>Descripción:</b>&nbsp</td>";
echo "<td bgcolor='#cccccc' width='200'><font size='2'face='arial'><input type='text' name='Tipcom'  size='50'></td>";
echo "</tr></TABLE></br>";
echo "<TABLE align=center><tr>";
echo "<input type='hidden' name='grabar' value='1'>";
echo "<tr><td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='CREAR' ></td></tr>";
echo "</TABLE>";
echo "</td>";
echo "</tr>";
echo "</form>";
echo "</fieldset>";

/**
 * free
 */
include_once("free.php");

?>