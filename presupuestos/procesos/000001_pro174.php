<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ajuste Costo de Personal (T43)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro174.php Ver. 2017-01-13</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro174.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper) or !isset($wpor) or !isset($wdiav) or !isset($wporc) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>AJUSTE COSTO DE PERSONAL X VACACIONES (T43)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestaci&oacute;n</td>";
			if(!isset($wanop))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' value=".$wanop." size=4 maxlength=4></td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Dias de Vacaciones</td>";
			if(!isset($wdiav))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wdiav' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wdiav' value=".$wdiav." size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Incremento Proyectado</td>";
			if(!isset($wpor))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpor' size=5 maxlength=5></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpor' value=".$wpor." size=5 maxlength=5></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Incremento</td>";
			if(!isset($wper))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' value=".$wper." size=2 maxlength=2></td></tr>";
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
					if(isset($wemp) and $wemp == $row[0]."-".$row[1])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			if(isset($wanop) and isset($wdiav) and isset($wper) and isset($wpor) and isset($wemp))
			{
				$monto=array();
				for($i=1;$i<12;$i++)
					$monto[$i]=0;
				$query = "SELECT nomcco,nomofi,nommin,nommfi,nompre,nomrec,nomaju,nommaj,nombom,nombas,sum(nomhco),(sum(nomhco)*nombas*(1+nomrec)),(sum(nomhco)*nomaju*(1+nomrec)) from ".$empresa."_000034 where nomano=".$wanop." and nomtip = 'E' and nomemp = '".substr($wemp,0,2)."' ";
				$query = $query."  group by nomcco,nomofi,nommin,nommfi,nompre,nomrec,nomaju,nommaj,nombom,nombas ";
				$query = $query."  order by nomcco,nomofi,nommin,nommfi ";
				//echo $query."<br>";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						for($j=$row[2];$j<=$row[3];$j++)
						{
							if($j >= $row[7])
								if($j >= $wper)
									$monto[$j] += $row[11]*(1 + $wpor/100);
								else
									$monto[$j] += $row[11];
							else
								$monto[$j] += $row[11];
						}
					}
				}
				$s=0;
				for($i=1;$i<=12;$i++)
					$s += $monto[$i];
				$s = $s / 360 * $wdiav;
				echo "<tr>";
				echo "<td bgcolor=#cccccc align=center>Monto a Ajustar</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmonto' value=".$s." size=12 maxlength=12 readonly=readonly></td></tr>";
				echo "<tr>";
				echo "<td bgcolor=#cccccc align=center>Porcentaje a Ajustar</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wporc' size=6 maxlength=6></td></tr>";
			}
			
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
				$query = "delete from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resemp = '".$wemp."' ";
				$query = $query."    and resind = '174'";
				$err = mysql_query($query,$conex);
				
				$s = $wmonto * ($wporc / 100);
				echo "valor de s : ".$s."<br>";
				$MESES=array();
				$query  = "SELECT Varmes,Varpor from ".$empresa."_000124 ";
				$query .= "  where Varano=".$wanop;
				$query .= "    and Varemp = '".$wemp."' ";
				$query .= " order by 1 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$MESES[$row[0]] = $s * ($row[1] / 100);
					}
				}
				
				$PESOS=array();
				$query  = "SELECT Resper,sum(Resmon) from ".$empresa."_000043 ";
				$query .= "  where Resano=".$wanop;
				$query .= "    and Resemp = '".$wemp."' ";
				$query .= "    and Resind='114' ";
				$query .= " group by 1 ";
				$query .= " order by 1 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);	
						$PESOS[$row[0]] = $row[1];
					}
				}
				
				$query  = "SELECT Rescco,Rescpr,Resper,sum(Resmon) from ".$empresa."_000043 ";
				$query .= "  where Resano=".$wanop;
				$query .= "    and Resemp = '".$wemp."' ";
				$query .= "    and Resind='114' ";
				$query .= " group by 1,2,3 ";
				$query .= " order by 1,3 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$k=0;
					for($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$MONTO = $MESES[$row[2]] * ($row[3] / $PESOS[$row[2]]) * -1;
						$MONTO = round($MONTO,0);
					
						$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','".$row[1]."',".$wanop.",".$row[2].",".$MONTO.",'174','C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
							$k++;
							echo "REGISTRO INSERTADO  : ".$k."<br>";
						}
					}
					echo "<b>TOTAL REGISTROS INSERTADOS  : ".$k."</b><br>";
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
