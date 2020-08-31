<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Porcentajes de Criterios Para un Periodo</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro39.php Ver. 2015-11-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro39.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wtip))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE PORCENTAJES DE CRITERIOS PARA UN PERIODO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Informacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
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
			$count=0;
			$query = "SELECT Mcrcri,sum(mcrmon) from ".$empresa."_000068 ";
			$query = $query."  where mcrano = ".$wanop;
			$query = $query."    and mcrmes = ".$wper1;
			$query = $query."    and mcremp = '".$wemp."' ";
			$query = $query."    and mcrtip = '".$wtip."'";
			$query = $query."   group by Mcrcri ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT Mcrcco,mcrmon from ".$empresa."_000068 ";
				$query = $query."  where mcrano = ".$wanop;
				$query = $query."    and mcrmes = ".$wper1;
				$query = $query."    and mcremp = '".$wemp."' ";
				$query = $query."    and mcrcri = '".$row[0]."'";
				$query = $query."   and mcrtip = '".$wtip."'";
				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);
				if($num2 > 0)
				{
					for ($j=0;$j<$num2;$j++)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$row2 = mysql_fetch_array($err2);
						$wpor= $row2[1] / $row[1];
						$query = "update ".$empresa."_000068 set mcrpor=".$wpor." where mcrano=".$wanop." and mcrmes=".$wper1." and Mcrcco='".$row2[0]."' and mcrcri = '".$row[0]."' and mcremp = '".$wemp."'";
						$err1 = mysql_query($query,$conex);
						$count++;
						echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
					}
    			}
			}
			echo "TOTAL REGISTROS ACTUALIZADOS : ".$count;
		}
	}
?>
</body>
</html>
