<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo Costos Administrativos de Insumos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro149.php Ver. 2011-06-09</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro149.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or !isset($wlin))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO COSTOS ADMINISTRATIVOS DE INSUMOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Ano de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Linea de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wlin' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$count=0;
			//                                  0                       1                      2                     3                      4                       5                      6                     7
			$query  = "select ".$empresa."_000108.Moslin,".$empresa."_000108.moscco,".$empresa."_000108.mosent,".$empresa."_000108.moscon,".$empresa."_000108.mospro,".$empresa."_000108.Mosipr,".$empresa."_000126.Pripor,".$empresa."_000108.Mosctt from ".$empresa."_000108,".$empresa."_000126 ";
			$query .= " where ".$empresa."_000108.mosano = ".$wanop; 
			$query .= "   and ".$empresa."_000108.mosmes = ".$wper1;
			$query .= "   and ".$empresa."_000108.moslin = '".$wlin."'";
			$query .= "   and ".$empresa."_000108.mosano = ".$empresa."_000126.priano "; 
			$query .= "   and ".$empresa."_000108.mosmes = ".$empresa."_000126.primes "; 
			$query .= "   and ".$empresa."_000108.moscco = ".$empresa."_000126.pricco ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wpor= $row[5] * ($row[6] / 100);
				$ctt = $row[7] + $wpor;
				$utt = $row[5] - $ctt;
				$query = "update ".$empresa."_000108 set Mosctt= Mosctt + ".$wpor.", Mosutt=".$utt." where mosano=".$wanop." and mosmes=".$wper1." and moslin='".$row[0]."' and moscco='".$row[1]."' and mosent='".$row[2]."'  and moscon='".$row[3]."'  and mospro='".$row[4]."'";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				$count++;
				echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
			}
			echo "TOTAL REGISTROS ACTUALIZADOS : ".$count;
		}
	}
?>
</body>
</html>
