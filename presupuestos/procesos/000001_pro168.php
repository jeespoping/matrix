<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion de Honorarios Con Pago Fijo Lineas(15,17,18,21,23,25,26,47,49,53,55,57,59)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro168.php Ver. 2014-10-15</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro168.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION DE HONORARIOS CON PAGO FIJO LINEAS(15,17,18,21,23,25,26,47,49,53,55,57,59)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			//                  0       1       2        3       4      5       6       7       8       9       10      11      12
			$query  = "select Mosipr, Mosctt, Mosctv, Chopro, Mostip, Moscon, Moslin, Moscco, Mosent, Mospro, Moshis, Mosing, Mosmed  "; 
			//$query .= " from ".$empresa."_000108,".$empresa."_000115,".$empresa."_000060 "; 
			$query .= " from ".$empresa."_000108,".$empresa."_000115"; 
			$query .= "  where mosano = ".$wanop;
			$query .= "    and mosmes = ".$wmesi; 
			$query .= "    and mostip = 'FA' ";
			$query .= "    and mosano = choano "; 
			$query .= "    and mosmes = chomes "; 
			$query .= "    and moslin = cholin "; 
			//$query .= "    and moscon = cfacod "; 
			//$query .= "    and moslin = cfalin "; 
			//$query .= "    and cfaeva = '1' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$k1=0;
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wctt = $row[0] * $row[3];
					$wutt = $row[0] - $wctt;
					$wctv = 0;
					$wutv = $row[0];
					$query = "update ".$empresa."_000108 set Mosctt=".$wctt.",Mosutt=".$wutt.",Mosctv=".$wctv.",Mosutv=".$wutv.",Mosest='on' where Mosano=".$wanop." and Mosmes=".$wmesi." and Mostip='".$row[4]."' and Moscon='".$row[5]."' and Moslin='".$row[6]."' and Moscco='".$row[7]."' and Mosent='".$row[8]."' and Mospro='".$row[9]."' and Moshis='".$row[10]."' and Mosing='".$row[11]."' and Mosmed='".$row[12]."'  ";
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
