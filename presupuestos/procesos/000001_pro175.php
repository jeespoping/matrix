<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion de Tarifas de Facturacion</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro175.php Ver. 2017-08-23</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro175.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wtip) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE TARIFAS DE FACTURACION</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Tipo de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='RADIO' name=wtip value=E>Examenes&nbsp&nbsp<input type='RADIO' name=wtip value=P>Procedimientos&nbsp&nbsp<input type='RADIO' name=wtip value=H>Habitaci&oacute;n&nbsp&nbsp<input type='RADIO' name=wtip value=I>Insumos</td></tr>";
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
		switch($wtip)
		{
			case "E":
				$query = "delete  from ".$empresa."_000105 ";
				$query = $query."  where Tartip = '".$wtip."'";
				$query = $query."    and Taremp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "SELECT query from ".$empresa."_000049  ";
				$query = $query."  where codigo =  26";
				$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
				$row = mysql_fetch_array($err);
				$conex_o = odbc_connect('facturacion','','');
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
					$query = "insert ".$empresa."_000105 (medico,fecha_data,hora_data,Taremp,Tarcco,Tarcod,Tartar,Tarcon,Tarfec,Tarmon,Tartip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[0]."','".$odbc[1]."','".$odbc[2]."','".$odbc[3]."','".$odbc[4]."',".$odbc[5].",'E','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Concepto ".mysql_errno().":".mysql_error());
					$count++;
					echo "REGISTRO NRO : ".$count." INSERTADO<br>";
				}
				echo "<B>REGISTROS ADICIONADOS : ".$count."</B>";
			break;
			case "P":
				$query = "delete  from ".$empresa."_000105 ";
				$query = $query."  where Tartip = '".$wtip."'";
				$query = $query."    and Taremp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "SELECT query from ".$empresa."_000049  ";
				$query = $query."  where codigo =  27";
				$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
				$row = mysql_fetch_array($err);
				$conex_o = odbc_connect('facturacion','','');
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
					$query = "insert ".$empresa."_000105 (medico,fecha_data,hora_data,Taremp,Tarcco,Tarcod,Tartar,Tarcon,Tarfec,Tarmon,Tartip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[0]."','".$odbc[1]."','".$odbc[2]."','".$odbc[3]."','".$odbc[4]."',".$odbc[5].",'P','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Concepto ".mysql_errno().":".mysql_error());
					$count++;
					echo "REGISTRO NRO : ".$count." INSERTADO<br>";
				}
				echo "<B>REGISTROS ADICIONADOS : ".$count."</B>";
			break;
			case "H":
				$query = "delete  from ".$empresa."_000105 ";
				$query = $query."  where Tartip = '".$wtip."'";
				$query = $query."    and Taremp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "SELECT query from ".$empresa."_000049  ";
				$query = $query."  where codigo =  28";
				$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
				$row = mysql_fetch_array($err);
				$conex_o = odbc_connect('facturacion','','');
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
					$query = "insert ".$empresa."_000105 (medico,fecha_data,hora_data,Taremp,Tarcco,Tarcod,Tartar,Tarcon,Tarfec,Tarmon,Tartip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[0]."','".$odbc[1]."','".$odbc[2]."','".$odbc[3]."','".$odbc[4]."',".$odbc[5].",'H','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Concepto ".mysql_errno().":".mysql_error());
					$count++;
					echo "REGISTRO NRO : ".$count." INSERTADO<br>";
				}
				echo "<B>REGISTROS ADICIONADOS : ".$count."</B>";
			break;
			case "I":
				$query = "delete  from ".$empresa."_000105 ";
				$query = $query."  where Tartip = '".$wtip."'";
				$query = $query."    and Taremp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "SELECT query from ".$empresa."_000049  ";
				$query = $query."  where codigo =  29";
				$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
				$row = mysql_fetch_array($err);
				$conex_o = odbc_connect('facturacion','','');
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
					$query = "insert ".$empresa."_000105 (medico,fecha_data,hora_data,Taremp,Tarcco,Tarcod,Tartar,Tarcon,Tarfec,Tarmon,Tartip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[0]."','".$odbc[1]."','".$odbc[2]."','".$odbc[3]."','".$odbc[4]."',".$odbc[5].",'I','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Concepto ".mysql_errno().":".mysql_error());
					$count++;
					echo "REGISTRO NRO : ".$count." INSERTADO<br>";
				}
				echo "<B>REGISTROS ADICIONADOS : ".$count."</B>";
			break;
		}
	}
}
?>
</body>
</html>
