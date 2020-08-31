<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Nomina Presupuestal Resumida (T34 a T35)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro9.php Ver. 2017-06-08</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro9.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper) or !isset($wpor) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE NOMINA PRESUPUESTAL RESUMIDA (T34 a T35)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestaci&oacute;n</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Incremento Proyectado</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpor' size=5 maxlength=5></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Incremento</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' size=2 maxlength=2></td></tr>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$query = "DELETE from ".$empresa."_000035 ";
				$query = $query." where nopano=".$wanop;
				$query = $query."   and nopemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				//                 0      1      2      3      4      5      6      7      8     9          10                        11                                       12
				$query = "SELECT nomcco,nomofi,nommin,nommfi,nompre,nomrec,nomaju,nommaj,nombom,nombas,sum(nomhco),(sum(nomhco)*nombas*(1+nompre)*(1+nomrec)),(sum(nomhco)*nomaju*(1+nompre)*(1+nomrec)) from ".$empresa."_000034 ";
				$query = $query."  where nomano = ".$wanop;
				$query = $query."    and nomemp = '".$wemp."' ";
				$query = $query."  group by nomcco,nomofi,nommin,nommfi,nompre,nomrec,nomaju,nommaj,nombom,nombas ";
				$query = $query."  order by nomcco,nomofi,nommin,nommfi ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$k1=0;
				$k2=0;
				$klave="";
				if ($num>0)
				{
					for($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						for($j=$row[2];$j<=$row[3];$j++)
						{
							$query = "SELECT Nopcco,Nopano,Nopper,Nopcar,Nophor,Nopmon,Noppre,Noprec  from ".$empresa."_000035 ";
							$query = $query." where nopano = ".$wanop;
							$query = $query."   and nopemp = '".$wemp."' ";
							$query = $query."   and nopcco = '".$row[0]."'";
							$query = $query."   and nopcar = '".$row[1]."'";
							$query = $query."   and nopper = ".$j;
							$err1 = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err1);
							if ($num1>0)
							{
								$row1 = mysql_fetch_array($err1);
								if($j >= $row[7])
									if($j >= $wper)
										$monto=$row1[5] + ($row[11] + $row[12])*(1 + $wpor/100) + $row[8];
									else
										$monto=$row1[5] + $row[11] + $row[12] + $row[8];
								else
									$monto=$row1[5] + $row[11] + $row[8];
								$horas=$row1[4]+$row[10];
								$query = "update ".$empresa."_000035 set nophor=".$horas.", nopmon=".$monto." where nopano=".$wanop." and nopper=".$j." and nopcco='".$row[0]."' and nopcar='".$row[1]."' and nopemp='".$wemp."'";
								$err2 = mysql_query($query,$conex);
								$k1++;
								echo "REGISTROS ACTUALIZADOS : ".$k1."<br>";
							}
							else
							{
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								if($j >= $row[7])
									if($j >= $wper)
										$monto=($row[11] + $row[12])*(1 + $wpor/100) + $row[8];
									else
										$monto=$row[11] + $row[12] + $row[8];
								else
									$monto=$row[11] + $row[8];
								$query = "insert ".$empresa."_000035 (medico,fecha_data,hora_data,nopemp,nopcco,nopano,nopper,nopcar,nophor,nopmon,noppre,noprec,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$j.",'".$row[1]."',".$row[10].",".$monto.",".$row[4].",".$row[5].",'C-".$empresa."')";
								$err2 = mysql_query($query,$conex) or die ("Error ".mysql_errno().":".mysql_error());
								$k2++;
								echo "REGISTROS INSERTADOS      : ".$k2."<br>";
							}
						}
					}
					echo "NUMERO DE REGISTROS ACTUALIZADOS : ".$k1."<br>";
					echo "NUMERO DE REGISTROS INSERTADOS      : ".$k2."<br>";
				}
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
