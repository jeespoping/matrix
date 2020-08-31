<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Costos Promedio Variables</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro74.php Ver. 2016-05-19</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro74.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE COSTOS PROMEDIO VARIABLES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			$query = "SELECT Cierre_costos from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and emp = '".$wemp."'";
			$query = $query."    and mes =   ".$wper1;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "off")
			{
			$count=0;
			// Cvaano Cvames Cvacco  Cvacod Cvagru Cvapor Cvactp Cvatmn Cvapro 
			//                  0      1       2       3       4        5         6
			$query = "select Cvacco, Cvacod, Cvagru, Cvapor, Cvacon, count(*),sum(Cvactp) ";
 			$query = $query." from ".$empresa."_000082 ";
			$query = $query."  where Cvaano = ".$wanop;
			$query = $query."    and Cvaemp = '".$wemp."'";
			$query = $query."    and Cvames between 1 and ".$wper1;
			$query = $query."  group  by  Cvacco, Cvacod, Cvagru, Cvapor, Cvacon ";
			$query = $query."  order  by  Cvacco, Cvacod, Cvagru, Cvapor, Cvacon ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$key1="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wpro = $row[6] / $row[5];
				$query = "update ".$empresa."_000082 set Cvapro = ".$wpro." where Cvaano=".$wanop." and Cvames=".$wper1." and Cvacco='".$row[0]."' and Cvacod='".$row[1]."' and Cvagru='".$row[2]."' and Cvapor=".$row[3]." and Cvacon='".$row[4]."' and Cvaemp='".$wemp."'";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				$count++;
				echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";

			}
			echo "TOTAL REGISTROS ACTUALIZADOS : ".$count;
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA CERRADO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
