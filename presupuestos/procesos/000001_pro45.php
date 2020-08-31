<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion Maestro de Activos Fijos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro45.php Ver. 2016-12-05</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro45.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE MAESTRO ACTIVOS FIJOS</td></tr>";
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
		if(strpos($wemp,":") !== false)
			$ODBC = substr($wemp,strpos($wemp,":")+1);
		$wemp = substr($wemp,0,2);
		$query = "delete from ".$empresa."_000075 where Acfcod NOT LIKE 'UO%' and Acfemp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." Error Borrando 75");
		$query = "SELECT query, Odbc from ".$empresa."_000049  ";
		$query = $query."  where codigo =  11";
		$query = $query."    and empresa = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$ODBC = $row[1];
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
			if(substr($odbc[0],0,2) == "01" or substr($odbc[0],0,2) == "05")
				$wtipo = "0";
			else
				$wtipo = "1";
			$waad=substr($odbc[2],0,4);
			$wmad=substr($odbc[2],5,2);
			$odbc[1]=str_replace("'","\"",$odbc[1]);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000075 (medico,fecha_data,hora_data,Acfemp,acfcod,acfdes,acfaac,acfmac,acfvid,acftip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[0]."','".$odbc[1]."',".$waad.",".$wmad.",".$odbc[3].",'".$wtipo."','C-".$empresa."')";
			//echo $query."<br><br>";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." Error en la Insercion de la Explicacion");
			$count++;
			 echo "REGISTROS ADICIONADO NRO : ".$count."<br>";
		}
		odbc_close($conex_o);
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B>";
	}
}
?>
</body>
</html>
