<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Autollenado de Ingresos En Pesos x Linea x CC</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro97.php Ver. 2011-09-15</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro97.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>AUTOLLENADO DE INGRESOS EN PESOS X LINEA X CC</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes a Partir del Cual se Hace Autollenado</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			#INICIO PROGRAMA
			$k=0;
			$query = "delete  from ".$empresa."_000018 ";
			$query = $query." where Inpano = ".$wanop; 
			$query = $query."     and Inptip = 'A' "; 
			$err = mysql_query($query,$conex);
			$query = "select Ocpano, Ocpcco, Ocplin, Ocpmes, Ocptip  from ".$empresa."_000020 ";
			$query = $query." where Ocpano = ".$wanop; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = "select  sum(Inping),sum(Inping * Inppte / 100 )  from ".$empresa."_000018 ";
					$query = $query." where Inpano =  ".$row[0]; 
					$query = $query." 	and Inpmes between 1 and ".$row[3];
					$query = $query." 	and Inpcco = '".$row[1]."'";
					$query = $query."     and Inplin = '".$row[2]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						if($row[4] == "1")
							$ing = $row1[0] /( $wper1 -1);
						else
						{
							$query = "select count(*) from ".$empresa."_000123 ";
							$query = $query." where Rlcano = ".$row[0]; 
							$query = $query." and Rlccco ='".$row[1]."'";
							$query = $query." and Rlclin = '".$row[2]."'";
							$err3 = mysql_query($query,$conex);
							$row3 = mysql_fetch_array($err3);
							$query = "select sum(Mioito) from ".$empresa."_000063 ";
							$query = $query." where mioano = ".$row[0]; 
							$query = $query." and miomes between 1 and ".$row[3];
							$query = $query." and miocco ='".$row[1]."'";
							if($row3[0] > 0)
								$query = $query." and miocfa in (select  Rlccfa from ".$empresa."_000123 where Rlcano = ".$row[0]." and  Rlccco ='".$row[1]."' and Rlclin = '".$row[2]."')";
							$err2 = mysql_query($query,$conex);
							$row2 = mysql_fetch_array($err2);
							$totac=$row2[0];
							$wanopa=$row[0] - 1;
							$query = "select sum(Mioito) from ".$empresa."_000063 ";
							$query = $query." where mioano = ".$wanopa; 
							$query = $query." and miomes between 1 and ".$row[3];
							$query = $query." and miocco ='".$row[1]."'";
							if($row3[0] > 0)
								$query = $query." and miocfa in (select  Rlccfa from ".$empresa."_000123 where Rlcano = ".$row[0]." and  Rlccco ='".$row[1]."' and Rlclin = '".$row[2]."')";
							$err2 = mysql_query($query,$conex);
							$row2 = mysql_fetch_array($err2);
							$totan=$row2[0];
						}
						$por = $row1[1] / $row1[0] * 100;
						$mes=$row[3] + 1;
						for ($j=$wper1;$j<= 12;$j++)
						{
							if($row[4] != "1")
							{
								$query = "select sum(Mioito) from ".$empresa."_000063 ";
								$query = $query." where mioano = ".$wanopa; 
								$query = $query." and miomes = ".$j;
								$query = $query." and miocco ='".$row[1]."'";
								if($row3[0] > 0)
									$query = $query." and miocfa in (select  Rlccfa from ".$empresa."_000123 where Rlcano = ".$row[0]." and  Rlccco ='".$row[1]."' and Rlclin = '".$row[2]."')";
								$err2 = mysql_query($query,$conex);
								$row2 = mysql_fetch_array($err2);
								$ing = $row2[0] * ($totac / $totan);
								$ing = round($ing, 0);
							}
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000018 (medico,fecha_data,hora_data, Inpano, Inpmes, Inpcco, Inplin, Inping, Inppte, Inptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$row[0].",".$j.",'".$row[1]."','".$row[2]."',".$ing.",".$por.",'A','C-".$empresa."')";
			       			$err2 = mysql_query($query,$conex);
			       			if ($err2 != 1)
								echo mysql_errno().":".mysql_error()."<br>";
							else
							{
			           			$k++;
			           			echo "REGISTRO INSERTADO  : ".$k."<br>";
		   					}
	   					}
   					}
   				}
   				echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
			}
   		}
}		
?>
</body>
</html>
