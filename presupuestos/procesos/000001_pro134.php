<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Distribucion Porcentual de Horas Empleado x CC</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro134.php Ver. 2017-06-14</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro134.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>DISTRIBUCION PORCENTUAL DE HORAS EMPLEADO X CC</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  de Presupuestaci&oacute;n</td>";
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
		$wemp=substr($wemp,0,strpos($wemp,"-"));
		$empant="";
		$query  = "SELECT Discod,Discco,Dispor from ".$empresa."_000159 ";
		$query .= "  where Disemp = '".$wemp."' ";
		$query .= " order by 1 ";
		$err2 = mysql_query($query,$conex) or die ("Error ".mysql_errno().":".mysql_error());
		$num2 = mysql_num_rows($err2);
		if($num2>0)
		{
			for($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);	
				if($empant != $row2[0])
				{
					//                 0        1       2      3        4      5       6       7       8       9       10      11      12      13     14 
					$query = "SELECT Nomano, Nomcco, Nomcod, Nomofi, Nomnom, Nomhco, Nommin, Nommfi, Nombas, Nompre, Nomrec, Nomaju, Nommaj, Nombom, Nomobs from ".$empresa."_000034 ";
					$query = $query."  where Nomano =".$wanop;
					$query = $query."    and Nomemp = '".$wemp."' ";
					$query = $query."    and Nomcod = '".$row2[0]."'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num > 0)
					{
						$empant = $row2[0];
						$row = mysql_fetch_array($err);
						$query = "DELETE from ".$empresa."_000034 ";
						$query = $query."  where Nomano =".$wanop;
						$query = $query."    and Nomemp = '".$wemp."' ";
						$query = $query."    and Nomcod = '".$row2[0]."'";
						$err1 = mysql_query($query,$conex);
					}
				}
				if($empant == $row2[0])
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$valor = $row[5] * ($row2[2] / 100);
					//echo $row[0]." ".$row[1]." ".$row[5]." ".$valor." ".$odbc[2]."<br>";
					$query = "insert ".$empresa."_000034 (medico, fecha_data, hora_data,Nomemp, Nomano, Nomcco, Nomcod, Nomofi, Nomnom, Nomhco, Nommin, Nommfi, Nombas, Nompre, Nomrec, Nomaju, Nommaj, Nombom, Nomobs, Nomtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[0].",'".$row2[1]."','".$row[2]."','".$row[3]."','".$row[4]."',".$valor.",".$row[6].",".$row[7].",".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$row[12].",".$row[13].",'".$row[14]."','E','C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					if ($err1 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
						$k++;
						echo "REGISTRO ACTUALIZADO  : ".$k."<br>";
					}
				}
			}
		}
		echo "<B>REGISTROS ACTUALIZADOS : ".$k."</B><BR>";
	}
}
?>
</body>
</html>
