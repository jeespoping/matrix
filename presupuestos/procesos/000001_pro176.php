<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion de Maestro de Nombres de Tarifas</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro176.php Ver. 2016-05-19</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro176.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wpro) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE MAESTRO DE NOMBRES DE TARIFAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde; Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wemp'>";
			echo "<option>Seleccione</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wempt = $wemp;
		$wemp = substr($wemp,0,2);
		echo "<center><table border=1>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center  colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
		echo "<tr><td align=center  colspan=2>ACTUALIZACION DE MAESTRO DE NOMBRES DE TARIFAS</td></tr>";
		echo "<tr><td align=center  colspan=2>EMPRESA : ".$wempt."</td></tr>";
		echo "<tr><td>ARTICULO</td><td>DESCRIPCION</td></tr>";
		$query = "DELETE  from ".$empresa."_000150 WHERE Ntaemp = '".$wemp."' ";
		$err1 = mysql_query($query,$conex);
		$query = "SELECT query from ".$empresa."_000049  ";
		$query = $query."  where codigo =  31 ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$conex_o = odbc_connect($ODBC,'','');
		$query = $row[0];
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$count=0;
		while (odbc_fetch_row($err_o))
		{
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}

			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000150 (medico,fecha_data,hora_data,Ntaemp, Ntacod, Ntades, Ntaest,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[0]."','".$odbc[1]."','on','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Tarifa");
			$count++;
			echo "<tr>";
			echo "<td>REGISTRO NRO. ".$count."</td>";
			echo "<td>ADICIONADO</td></tr>";
		}
		echo "<tr><td align=center  colspan=2><B>REGISTROS ADICIONADOS : ".$count."</B></td></tr></table>";
	}
}
?>
</body>
</html>
