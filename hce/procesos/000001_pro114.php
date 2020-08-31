<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Resumen de Gastos de Personal (T43)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro114.php Ver. 2014-03-25</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro114.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wpor) or !isset($wtip) or (strtoupper ($wtip) != "I" and strtoupper ($wtip) != "D"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION RESUMEN DE GASTOS DE PERSONAL</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Incremento o Decremento ? (I/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Porcentaje</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpor' size=6 maxlength=6></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = 0 ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$k=0;
				if($wtip == "I")
					$mult=1;
				else
					$mult=-1;
				$query = "delete from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resind = '114'";
				$err = mysql_query($query,$conex);
				$query = "SELECT Nopcco, '201', Nopano, Nopper,sum(Nopmon)  from ".$empresa."_000035,".$empresa."_000005 ";
				$query = $query."  where Nopano = ".$wanop;
				$query = $query."      and  Nopcco = Ccocod ";
				$query = $query."      and  Ccoclas != 'UNI' ";
				$query = $query."  group by Nopcco, Nopano, Nopper ";
				$query = $query."   UNION ALL ";
				$query = $query." SELECT Nopcco, '515', Nopano, Nopper,sum(Nopmon)  from ".$empresa."_000035,".$empresa."_000005 ";
				$query = $query."  where Nopano = ".$wanop;
				$query = $query."      and  Nopcco = Ccocod ";
				$query = $query."      and  Ccoclas = 'UNI' ";
				$query = $query."  group by Nopcco, Nopano, Nopper ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$suma=0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[3] == 12)
					{
						$suma += $row[4];
						$suma = $suma * ($wpor / 100);
						$row[4] = $row[4] +($suma * $mult);
						$suma=0;
					}
					else
						$suma += $row[4];
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
				
					$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('costosyp','".$fecha."','".$hora."','".$row[0]."','".$row[1]."',".$row[2].",".$row[3].",".$row[4].",'114','C-costosyp')";
					$err1 = mysql_query($query,$conex);
					if ($err1 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
						$k++;
						echo "REGISTRO INSERTADO  : ".$k."<br>";
					}
				}
				echo "REGISTROS INSERTADOS : ".$k;
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
