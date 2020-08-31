<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion de Insumos (Convenios)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro85.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro85.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE CONVENIOS</td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION DE INSUMOS (CONVENIOS)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$k=0;
			#INICIO PROGRAMA
			$query = "update ".$empresa."_000109 set Motcos=0  where Motano=".$wanop." and Motmes=".$wmesi." and Motlin='5' ";
           	$err2 = mysql_query($query,$conex);
			$query = " select Mitano,Mitmes,Mitcco,Mitent,sum(Mitcan*Mincpr) as calculo  ";
			$query = $query." from ".$empresa."_000111,".$empresa."_000093 ";
			$query = $query." where mitano = ".$wanop;
			$query = $query." and mitmes = ".$wmesi; 
			$query = $query." and mitano = minano ";
			$query = $query." and mitmes = minmes ";
			$query = $query." and mitins = mincod ";
			$query = $query." group by Mitano,Mitmes,Mitcco,Mitent ";
			$query = $query." Order by Mitano,Mitmes,Mitcco,Mitent ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = " select Motfto * (Pripor / 100)   ";
					$query = $query." from ".$empresa."_000109,".$empresa."_000126 ";
					$query = $query." where Motano = ".$row[0];
					$query = $query." and Motmes = ".$row[1]; 
					$query = $query." and Motcco = '".$row[2]."'"; 
					$query = $query." and Motemp = '".$row[3]."'"; 
					$query = $query." and Motlin = '5' ";
					$query = $query." and Motano = Priano ";
					$query = $query." and Motmes = Primes ";
					$query = $query." and Motcco = Pricco ";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$calculo = 0;
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$calculo=$row1[0];
					}
					$row[4] = $row[4] + $calculo;
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "update ".$empresa."_000109 set Motcos=".$row[4]."  where Motano=".$row[0]." and Motmes=".$row[1]." and Motcco='".$row[2]."' and Motemp='".$row[3]."' and Motlin='5' ";
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
