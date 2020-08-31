<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion de Maestro de Habitaciones</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro181.php Ver. 2018-02-15</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro181.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE TARIFAS DE FACTURACION</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Empcod,Empdes,Empodd  from ".$empresa."_000153 order by Empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wemp'>";
			echo "<option>Seleccione</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1].":".$row[2]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		if(strpos($wemp,":") !== false)
			$ODBC = substr($wemp,strpos($wemp,":")+1);
		$wemp = substr($wemp,0,2);
		$query = "delete  from ".$empresa."_000039 where Habtac='A' ";
		$err = mysql_query($query,$conex);
		$query = "SELECT Query,Odbc from ".$empresa."_000049  ";
		$query = $query."  where codigo =  32 ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$conex_o = odbc_connect($row[1],'','');
		$query = $row[0];
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$count=0;
		$clave="";
		while (odbc_fetch_row($err_o))
		{
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			if($clave != $odbc[0].$odbc[1])
			{
				$query = "SELECT count(*) from ".$empresa."_000039 where Habcco = '".$odbc[0]."' and Habhab = '".$odbc[1]."'  ";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				if($row[0] == 0)
				{
					$query = "insert ".$empresa."_000039 (medico,fecha_data,hora_data,Habcco,Habhab,Habtip,Habdes,Habtac,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$odbc[0]."','".$odbc[1]."','".$odbc[2]."','".$odbc[3]."','A','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." Error en la Insercion de la Habitacion");
					$count++;
					echo "REGISTRO NRO : ".$count." INSERTADO<br>";
				}
				$clave = $odbc[0].$odbc[1];
			}
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B>";
	}
}
?>
</body>
</html>
