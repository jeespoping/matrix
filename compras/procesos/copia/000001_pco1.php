<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion de Maestro de Insumos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pco1.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pco1.php' method=post>";
	

	

	if(!isset($wanop))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE MAESTRO DE INSUMOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<center><table border=1>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center  colspan=2>APLICACION DE CONTROL DE COMPRAS</td></tr>";
		echo "<tr><td align=center  colspan=2>ACTUALIZACION DE MAESTRO DE INSUMOS</td></tr>";
		echo "<tr><td>ARTICULO</td><td>DESCRIPCION</td></tr>";
		$query = "SELECT query from compras_000005  ";
		$query = $query."  where codigo =  1";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla compras_000005");
		$row = mysql_fetch_array($err);
		$conex_o = odbc_connect('inventarios','','');
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
			$query = "SELECT *  from compras_000003  ";
			$query = $query."  where Artcod =  '".$odbc[0]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num1 == 0)
			{
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$odbc[1] = str_replace("'","-",$odbc[1]);
				$query = "insert compras_000003 (medico,fecha_data,hora_data, Artcod, Artnom, Artuni, seguridad) values ('compras','".$fecha."','".$hora."','".$odbc[0]."','".$odbc[1]."','".$odbc[2]."','C-compras')";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$count++;
				echo "<tr>";
       			echo "<td>".$odbc[0]."</td>";
       			echo "<td>".$odbc[1]."</td></tr>";
   			}
		}
		echo "<tr><td align=center  colspan=2><B>REGISTROS ADICIONADOS : ".$count."</B></td></tr></table>";
	}
}
?>
</body>
</html>