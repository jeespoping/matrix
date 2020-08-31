<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion de Empleados</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro130.php Ver. 2017-07-03</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro130.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE EMPLEADOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
		$wemp = substr($wemp,0,2);
		$query = "delete from ".$empresa."_000038 where Nomemp = '".$wemp."' ";
		$err = mysql_query($query,$conex);
		$query = "SELECT query,Odbc from ".$empresa."_000049  ";
		$query = $query."  where codigo =  22";
		$query = $query."    and Empresa = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$ODBC = $row[1];
		echo $ODBC."<br>";
		//$conex_o = odbc_connect($ODBC,'','');
		$conex_o = odbc_connect($ODBC,"","") or die(odbc_errormsg());
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
			$nombre=$odbc[1]." ".$odbc[2]." ".$odbc[3]." ".$odbc[4];
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000038 (medico,fecha_data,hora_data,Nomemp, Nomcod, Nomnom, Nometr, Nomfre, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[0]."','".$nombre."','".$odbc[5]."','".$odbc[6]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Nomina : ".mysql_errno().":".mysql_error());
			$count++;
			echo "REGISTROS INSERTADOS : ".$count."<BR>";
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
	}
}
?>
</body>
</html>
