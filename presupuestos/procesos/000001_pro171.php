<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion de Servicios Sin Costo (Linea 39)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro171.php Ver. 2013-06-28</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro171.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION DE SERVICIOS SIN COSTO (LINEA 39)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			//                  0     1      2      3      4      5      6      7      8      9   
			$query = "select Mosano,Mosmes,Mostip,Moscon,Moslin,Moscco,Mosent,Mospro,Moscan,Mosipr ";
			$query .= " from ".$empresa."_000108 ";
			$query .= "   where mosano = ".$wanop;
			$query .= " 	and mosmes = ".$wmesi;
			$query .= " 	and mostip = 'FA' "; 
			$query .= " 	and moslin = '39' "; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$k1=0;
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wctt = 0;
					$wutt = $row[9];
					$wctv = 0;
					$wutv = $row[9];
					$query = "update ".$empresa."_000108 set Mosctt=".$wctt.",Mosutt=".$wutt.",Mosctv=".$wctv.",Mosutv=".$wutv.",Mosest='on' where Mosano=".$wanop." and Mosmes=".$wmesi." and Mostip='".$row[2]."' and Moscon='".$row[3]."' and Moslin='".$row[4]."' and Moscco='".$row[5]."' and Mosent='".$row[6]."' and Mospro='".$row[7]."' ";
					$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$k1++;
					echo "REGISTROS ACTUALIZADO : ".$k1."<br>";
               	}
			}
			echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k1."</b><br>";
        }
}		
?>
</body>
</html>
