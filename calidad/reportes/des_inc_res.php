<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<?php
include_once("conex.php");
/********************************************************
 *           Autor: Ana Maria Betancur					*
 *			Fecha de Creación:2005-04-19				*
 ********************************************************
 Descripción:
 	Realiza un informe descriptivo de  de inconsistencias en la facturacion por responsable, entre dos fechas determinados por el usuario. Los permisos determinan si tiene derecho a ver los de todos los responsables o solo el propio		
 */
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='' method=post>";
	if(!isset($permiso) and !isset($fechaini))
	{
		$query = "select Responsable_inc From calidad_000003 where Responsable_inc like '%-".substr($user,2)."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row=mysql_fetch_row($err);
			$permiso=$row[0];
		}
		else
		$permiso="";
	}
	if(!isset($fechaini)  and $permiso != "")
	{
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>INFORME DESCRIPTIVO</b></td></tr>";
		if($permiso != 'Todos'){
			echo "<input type='hidden' name='resp' value='".$permiso."'>";

		}else
		{
			echo "<tr><td bgcolor=#cccccc align=center>Responsable:</td>";
			echo "<td bgcolor=#cccccc align=center><select name='resp'>";
			$query = "select DISTINCT(calidad_000003.Responsable_inc) from calidad_000003 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<option>Todas</option>";
			if ($num > 0){
				for($i=0;$i<$num;$i++){
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
			}
			echo "</select></td></tr>";
		}
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Ini</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fechaini' size=10 maxlength=10> AAAA-mm-dd</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Fin</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fechafin' size=10 maxlength=10> AAAA-mm-dd</td></tr>";

		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else if (isset($resp) and $resp != "")
	{
		echo "<table border='1' width='600'>";
		echo "<tr><td colspan=6 align=center bgcolor='#000066'><font color='#FFFFFF'><b>INFORME DESCRIPTIVO DE INCONSISTENCIAS</b></td></tr>";
		if($resp == "Todas"){
			$resp="";
		}else{
			//echo "<tr><td colspan=6 align=center><b>$resp</b></td></tr>";
			$resp=" Responsable_inc='".$resp."' and";
		}
		echo "<tr><td colspan=6 align=center bgcolor='#000066'><b><font color='#FFFFFF'>Desde $fechaini hasta $fechafin</b></td></tr></table>";
		
		
		/*La prefactura*/
			$tabla='calidad_000005';
			$var='fecha_inc';
			$sel="Historia";
			echo "<BR><BR><BR><table border='1' width='600'>";			
			echo "<tr><td colspan='6' align=center bgcolor='#000066'><font color='#FFFFFF'><b>EN LA PREFACTURA</b></td></tr>";
			include_once("calidad/descXincXres.php");//imprimir en pantalla por unidad  
			
			/*La factura*/
			$tabla='calidad_000003';
			$var="fecha_inconsistencia";
			$sel="Factura";
			echo "</table><BR><BR><table border='1' width='600'>";		
			echo "<tr><td colspan='6' align=center bgcolor='#000066'><font color='#FFFFFF'><b>EN LA FACTURA</b></td></tr>";
			include_once("calidad/descXincXres.php");//imprimir en pantalla por unidad 

	} else {
		echo "<b>No existen inconsistencias registradas para el usuario con codigo ".substr($user,2)."</b>";
	}
}
?>
</body>
</html>