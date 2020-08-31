<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Movimiento Real vs Presupuestado</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro8.php Ver. 2015-09-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro8.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmesi) or !isset($wmesf) or !isset($wccoi) or !isset($wccof))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center>GENERACION DE MOVIMIENTO REAL VS PRESUPUESTADO</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>C.C. Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>C.C. Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
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
			$query = "DELETE from ".$empresa."_000044 ";
			$query = $query." where rvpano = ".$wanop;
			$query = $query."   and rvpemp = '".$wemp."' ";
			$query = $query."   and rvpper >= ".$wmesi;
			$query = $query."   and rvpper <= ".$wmesf;
			$query = $query."   and rvpcco >= '".$wccoi."'";
			$query = $query."   and rvpcco <= '".$wccof."'";
			$err = mysql_query($query,$conex);
			#echo $query."<br>";
			$query = "SELECT rescco,resano,resper,rescpr,sum(resmon) as wpres from ".$empresa."_000043 ";
			$query = $query." where resano = ".$wanop;
			$query = $query."   and resemp = '".$wemp."' ";
			$query = $query."   and resper >= ".$wmesi;
			$query = $query."   and resper <= ".$wmesf;
			$query = $query."   and rescco >= '".$wccoi."'";
			$query = $query."   and rescco <= '".$wccof."'";
			$query = $query."   group by rescco,resano,resper,rescpr ";
			$query = $query."   order by rescco,resano,resper,rescpr ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			#echo $num1." ".$query."<br>";
			$query = "SELECT meccco,mecano,mecmes,meccpr,sum(mecval) as wreal from ".$empresa."_000026 ";
			$query = $query." where mecano = ".$wanop;
			$query = $query."   and mecemp = '".$wemp."' ";
			$query = $query."   and mecmes >= ".$wmesi;
			$query = $query."   and mecmes <= ".$wmesf;
			$query = $query."   and meccco >= '".$wccoi."'";
			$query = $query."   and meccco <= '".$wccof."'";
			$query = $query."   group by meccco,mecano,mecmes,meccpr ";
			$query = $query."   order by meccco,mecano,mecmes,meccpr ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			#echo $num2." ".$query."<br>";
			$i=0;
			$j=0;
			$k=0;
			if($num1>0)
			{
				$i++;
				$row1 = mysql_fetch_array($err1);
				if($row1[2] < 10)
					$kl1=$row1[0]."0".$row1[2].$row1[3];
				else
					$kl1=$row1[0].$row1[2].$row1[3];
			}
			else
			{
				$kl1="zzzzzzzzzz";
				$i=1;
			}
			if($num2>0)
			{
				$j++;
				$row2 = mysql_fetch_array($err2);
				if($row2[2] < 10)
					$kl2=$row2[0]."0".$row2[2].$row2[3];
				else
					$kl2=$row2[0].$row2[2].$row2[3];
			}
			else
			{
				$kl2="zzzzzzzzzz";
				$j=1;
			}
			while($i<=$num1 or $j<=$num2)
			{
				#echo $kl1." ".$kl2."<br>";
				if($kl1== $kl2)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000044 (medico,fecha_data,hora_data,rvpemp,rvpcco,rvpcpr,rvpano,rvpper,rvpvre,rvpvpr,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row1[0]."','".$row1[3]."',".$row1[1].",".$row1[2].",".$row2[4].",".$row1[4].",'C-".$empresa."')";
					#echo "IGUALES <br>";
					$err = mysql_query($query,$conex);
					$k++;
					$i++;
					$j++;
					if($i > $num1)
						$kl1="zzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						if($row1[2] < 10)
							$kl1=$row1[0]."0".$row1[2].$row1[3];
						else
							$kl1=$row1[0].$row1[2].$row1[3];
					}
					if($j > $num2)
						$kl2="zzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						if($row2[2] < 10)
							$kl2=$row2[0]."0".$row2[2].$row2[3];
						else
							$kl2=$row2[0].$row2[2].$row2[3];
					}
				}
				else
					if($kl1 < $kl2)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000044 (medico,fecha_data,hora_data,rvpemp,rvpcco,rvpcpr,rvpano,rvpper,rvpvre,rvpvpr,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row1[0]."','".$row1[3]."',".$row1[1].",".$row1[2].",0,".$row1[4].",'C-".$empresa."')";
						#echo "MENOR K11 <br>";
						$err = mysql_query($query,$conex);
						$k++;
						$i++;
						if($i > $num1)
							$kl1="zzzzzzzzzz";
						else
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[2] < 10)
								$kl1=$row1[0]."0".$row1[2].$row1[3];
							else
								$kl1=$row1[0].$row1[2].$row1[3];
						}
					}
					else
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000044 (medico,fecha_data,hora_data,rvpemp,rvpcco,rvpcpr,rvpano,rvpper,rvpvre,rvpvpr,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row2[0]."','".$row2[3]."',".$row2[1].",".$row2[2].",".$row2[4].",0,'C-".$empresa."')";
						#echo "MENOR K12 <br>";
						$err = mysql_query($query,$conex);	
						$k++;
						$j++;
						if($j > $num2)
							$kl2="zzzzzzzzzz";
						else
						{
							$row2 = mysql_fetch_array($err2);
							if($row2[2] < 10)
								$kl2=$row2[0]."0".$row2[2].$row2[3];
							else
								$kl2=$row2[0].$row2[2].$row2[3];
						}
					}
			echo "GRABANDO EL REGISTRO NRO : ".$k."<br>";
			 }
			  echo "NUMERO DE REGISTROS GENERADOS : ".$k."<br>";
		 }
}
?>
</body>
</html>
