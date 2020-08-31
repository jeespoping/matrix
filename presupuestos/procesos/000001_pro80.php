<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo Relacion Costo - Ingresos - Honorarios Fijos(T115)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro80.php Ver. 2014-08-14</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro80.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE CONVENIOS</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO RELACION COSTO - INGRESOS - HONORARIOS FIJOS(T115)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$k=0;
			#INICIO PROGRAMA
			$query = "SELECT Cholin,Chocos,Chopor,sum(Mosipr)  from ".$empresa."_000108,".$empresa."_000115 ";
			$query = $query." where Mosano = ".$wanop;
			$query = $query."   and Mosmes = ".$wmesi;
			$query = $query."   and Mostip = 'FA' "; 
			$query = $query."   and Mosano =  Choano ";
			$query = $query."   and Mosmes =  Chomes ";
			$query = $query."   and Moslin =  Cholin ";
			$query = $query."   group by Cholin "; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[3] != 0)
						$wpro=($row[1] * ($row[2] / 100)) / $row[3];
					else
						$wpro=0;
					$query = "update ".$empresa."_000115 set Chopro='".$wpro."', Choipr=".$row[3]."  where Choano=".$wanop." and Chomes=".$wmesi." and Cholin='".$row[0]."' " ;
					$err2 = mysql_query($query,$conex);
					$k++;
					echo "REGISTROS ACTUALIZADO : ".$k."<br>";
               	}
			}
			echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k."</b><br>";
        }
}		
?>
</body>
</html>
