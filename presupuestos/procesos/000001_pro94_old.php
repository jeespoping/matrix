<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo de Ingresos Promedio Laboratorio x CC</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro94.php Ver. 2011-09-15</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro94.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop)  or !isset($wper1)  or !isset($wper2)  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE INGRESOS PROMEDIO LABORATORIO X CC</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanob' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			#INICIO PROGRAMA
			$k=0;
			$data=array();
			$query = "select sum(Mioint),sum(Mioito) from ".$empresa."_000063 ";
			$query = $query." where mioano =  ".$wanop; 
			$query = $query." and miomes between ".$wper1." and ".$wper2;
			$query = $query." and miocco = '3081' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wint=$row[0];
				$wito=$row[1];
			}
			else
			{
				$wint=0;
				$wito=0;
			}
			$query = "select Labano, Labcco, Labpor,Ccouni  from ".$empresa."_000017,".$empresa."_000005 ";
			$query = $query." where Labano =  ".$wanop; 
			$query = $query."     and Labcco =  Ccocod"; 
			$query = $query."  union "; 
			$query = $query." select Labano, Labcco, Labpor,'99'  from ".$empresa."_000017 ";
			$query = $query." where Labano =  ".$wanop; 
			$query = $query."     and Labcco =  '99' "; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[3] == "2H")
					{
						$query = "select sum(Morcan) from ".$empresa."_000032 ";
						$query = $query." where Morano =  ".$wanop; 
						$query = $query." and Mormes between ".$wper1." and ".$wper2;
						$query = $query." and Morcco = '".$row[1]."'";
						$query = $query." and Morcod = '12' ";
					}
					else
					{
						$query = "select sum(Morcan) from ".$empresa."_000032 ";
						$query = $query." where Morano =  ".$wanop; 
						$query = $query." and Mormes between ".$wper1." and ".$wper2;
						$query = $query." and Morcco = '".$row[1]."'";
					}
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					if($row1[0] !=  0)
						$can = $row1[0];
					else
						$can=$wper2 - $wper1 + 1;
					if($can != 0)
					{
						$winpr=$wito * ($row[2] / 100) / $can;
						$winpr = round($winpr, 0);
					}
					else
						$winpr=0;
					if(($wito * $row[2]) != 0)
						$winte=($wint * ($row[2] / 100)) / ($wito * $row[2]) * 100;
					else
						$winte=0;
					$query = "update ".$empresa."_000017 set Labinp=".$winpr.",Labter=".$winte."  where Labano=".$wanop." and Labcco='".$row[1]."'";
	       			$err2 = mysql_query($query,$conex);
	           		$k++;
	           		echo "REGISTROS ACTUALIZADO  : ".$k."<br>";
   				}
   				echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k."</b><br>";
			}
   		}
}		
?>
</body>
</html>
