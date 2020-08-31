<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion Mensual de Movimiento de Compras</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pco3.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pco3.php' method=post>";
	

	

	if(!isset($wanop) or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION MENSUAL DE MOVIMIENTO DE COMPRAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "delete from compras_000002 ";
		$query = $query."  where Movano = ".$wanop;
		$query = $query."    and Movmes = ".$wper1;
		$err = mysql_query($query,$conex);
		$query = "SELECT query from compras_000005  ";
		$query = $query."  where codigo =  3 ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla compras_000005");
		$row = mysql_fetch_array($err);
		$conex_o = odbc_connect('inventarios','','');
		$query = $row[0];
		$query=str_replace("ANO",$wanop,$query);
		$query=str_replace("MES",$wper1,$query);
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
			$query = "insert compras_000002 (medico,fecha_data,hora_data,Movano, Movmes, Movart, Movnit,Movval, Movcan, seguridad) values ('compras','".$fecha."','".$hora."',".$odbc[0].",".$odbc[1].",'".$odbc[2]."','".$odbc[3]."',".$odbc[4].",".$odbc[5].",'C-compras')";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$count++;
			echo "REGISTRO ADICIONADO NRO : ".$count."<br>";
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B>";
	}
}
?>
</body>
</html>