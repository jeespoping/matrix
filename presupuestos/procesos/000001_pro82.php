<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion Movimiento Notas (Convenios)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro82.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro82.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION MOVIMIENTO NOTAS (CONVENIOS)</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A�o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "delete from ".$empresa."_000118";
		$query = $query."  where Notano = ".$wanop;
		$query = $query."    and Notmes = ".$wper1;
		$err = mysql_query($query,$conex);
		$query = "SELECT query from ".$empresa."_000049  ";
		$query = $query."  where codigo =  20 ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$conex_o = odbc_connect('facturacion','','');
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
			$query = "SELECT Empcin from ".$empresa."_000061 ";
			$query = $query." where Epmcod = '".$odbc[4] ."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$odbc[4]=$row1[0];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data,Notano, Notmes, Notfue, Notcon, Notdes, Notcue, Notent, Notmon, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wper1.",'".$odbc[0]."','".$odbc[1]."','".$odbc[2]."','".$odbc[3]."','".$odbc[4]."',".$odbc[5].",'C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Movimiento del Insumo");
				$count++;
				echo "REGISTRO ADICIONADO NRO : ".$count."<br>";
			}
		}
		echo "<b>REGISTROS ADICIONADOS : ".$count."</B>";
	}
}
?>
</body>
</html>