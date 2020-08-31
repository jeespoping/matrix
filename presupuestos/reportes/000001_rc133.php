<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
//-->
</script>
<?php
include_once("conex.php");
//HORAS X EMPLEADO Y UNIDAD (NOMINA)
function periodo($mes,$factor)
{
	// Funcion que permite dividir el a�o en periodos dados x el factor y saber el mes en que periodo se encuentra
    if($mes % $factor == 0)
    	$mes--;
	$periodo=($mes + ($factor - ($mes % $factor)))/$factor;
	return $periodo;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc133.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanof) or !isset($wmesi) or !isset($wmesf) or !isset($wcco1)  or !isset($wcco2) or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>HORAS X EMPLEADO Y UNIDAD (NOMINA)</td></tr>";
			echo "<tr><td align=center colspan=2>000001_rc133.php Ver. 2008-05-27</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Forma de Agrupacion</td>";
			echo "<td bgcolor=#cccccc>";
			echo "<input type='RADIO' name=wfactor value=1 checked> Mensual<br>";
			echo "<input type='RADIO' name=wfactor value=3> Trimestral<br>";
			echo "<input type='RADIO' name=wfactor value=4> Cuatrimestral<br>";
			echo "<input type='RADIO' name=wfactor value=6> Semestral<br>";
			echo "<input type='RADIO' name=wfactor value=12> Anual";
			echo "</td></tr>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			//                   0       1       2      3       4         5          6       7 
			$query  = "select Norcco, Nomnom, Connom, Norano, Norper, sum(Norhor), Norcod, Noremp  from ".$empresa."_000036,".$empresa."_000008, ".$empresa."_000038 ";
			$query .= " where norfil = '".$wemp."' ";
			$query .= "	  and ((Norano =  ".$wanoi;
			$query .= "	  and Norper >= ".$wmesi." and Norano < ".$wanof.") ";
			$query .= "	   or (Norano > ".$wanoi;
			$query .= "	  and  Norano < ".$wanof.") "; 
			$query .= "	   or (Norano = ".$wanof;
			$query .= "	  and  Norper <= ".$wmesf." and Norano > ".$wanoi.") "; 
			$query .= "	   or (Norano = ".$wanoi." and Norano = ".$wanof; 
			$query .= "	  and  Norper >= ".$wmesi." and Norper <= ".$wmesf.")) "; 
			$query .= "	  and Norcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "	  and Norcod = Concod ";
			$query .= "   and norfil = conemp ";
		    $query .= "	  and Concod in ('0001','0014','0022') ";
		    if(isset($wcarg))
		    	$query .= "	  and Norcar = '".$wcarg."' ";
			$query .= "	  and Noremp = Nomcod ";
			$query .= "   and norfil = Nomemp ";
			$query .= "	group by Norcco, Nomnom, Connom, Norano, Norper, Norcod, Noremp ";
			$query .= "	order by Norcco, Noremp, Connom  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wclaa="";
				$wcco="";
				$wcar="";
				$wcarn="";
				$wcon="";
				$data=array();
				$tot=array();
				$ncol=3;
				$ncolor=0;
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$ncol++;
						$inc +=1;
					}
					$inc=1;
				}
				echo "<table border=0 align=center>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2><font size=3>DIRECCION DE INFORMATICA</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>HORAS X EMPLEADO Y UNIDAD (NOMINA)</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>DESDE EL MES ".$wmesi." A&Ntilde;O ".$wanoi." HASTA EL MES ".$wmesf." A&Ntilde;O ".$wanof."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
				if(isset($wcarg))
					echo "<tr><td colspan=".$ncol." align=center><font size=2>CARGO : ".$wcarg."-".$wcargn."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=1.5>000001_rc133.php Ver. 2008-05-27</font></td></tr>";
				switch ($wfactor)
				{
					case 1:
						$wp="/M";
					break;
					case 3:
						$wp="/T";
					break;
					case 4:
						$wp="/C";
					break;
					case 6:
						$wp="/S";
					break;
					case 12:
						$wp="";
					break;
				}
				echo "<tr><td bgcolor=#cccccc><font size=2><b>CENTRO<BR>DE COSTOS</b></font></td><td bgcolor=#cccccc><font size=2><b>EMPLEADO</b></font></td><td bgcolor=#cccccc><font size=2><b>CONCEPTO</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$data[$i][$inc]=0;
						$tot[$i][$inc]=0;
						$ano=$i+$wanoi;
						if($wfactor != 12)
							echo "<td bgcolor=#cccccc align=center><font size=2><b>".$ano.$wp.$inc."</b></font></td>";
						else
							echo "<td bgcolor=#cccccc align=center><font size=2><b>".$ano."</b></font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					$wcla=$row[0].$row[6].$row[7];
					if($wclaa != $wcla)
					{
						if($j != 0)
						{
							$ncolor++;
							if($ncolor % 2 == 0)
								$color="#FFFFFF";
							else
								$color="#99CCFF";
							//$path="/matrix/presupuestos/reportes/000001_rc133.php?wcar=".$wcarn."&wcco=".$wcco."&wano=".$wanof."&wmes=".$wmesf."";
							echo "<tr><td bgcolor=".$color."><font size=2>".$wcco."</font></td><td bgcolor=".$color."><font size=2>".$wcarn."-".$wcar."</font></td><td bgcolor=".$color."><font size=2>".$wcon."</font></td>";
							$inc=periodo($wmesi,$wfactor);
							$inf=periodo($wmesf,$wfactor);
							$iter=12 / $wfactor;
							for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
							{
								while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
								{
									echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$inc],0,'.',',')."</font></td>";
									$inc +=1;
								}
								$inc=1;
							}
							echo "</tr>";
							if($wcarn != $row[7])
							{
								$color="#FFCC66";
								//$path="/matrix/presupuestos/reportes/000001_rc133.php?wcar=".$wcarn."&wcco=".$wcco."&wano=".$wanof."&wmes=".$wmesf."";
								echo "<tr><td bgcolor=".$color."><font size=2>".$wcco."</font></td><td bgcolor=".$color."><font size=2>TOTAL : ".$wcarn."-".$wcar."</font></td><td bgcolor=".$color."><font size=2>".$wcon."</font></td>";
								$inc=periodo($wmesi,$wfactor);
								$inf=periodo($wmesf,$wfactor);
								$iter=12 / $wfactor;
								for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
								{
									while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
									{
										echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$tot[$i][$inc],0,'.',',')."</font></td>";
										$inc +=1;
									}
									$inc=1;
								}
								echo "</tr>";
								for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
								{
									while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
									{
										$tot[$i][$inc]=0;
										$inc +=1;
									}
									$inc=1;
								}
							}
						}
						$wcco=$row[0];
						$wcar=$row[1];
						$wcon=$row[2];
						$wcarn=$row[7];
						$wclaa=$row[0].$row[6].$row[7];
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						$iter=12 / $wfactor;
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$data[$i][$inc]=0;
								$inc +=1;
							}
							$inc=1;
						}
					}
					$fil=$row[3] - $wanoi;
					$col=periodo($row[4],$wfactor);
					$data[$fil][$col] += ($row[5] / 1);
					$tot[$fil][$col] += ($row[5] / 1);
				}
				echo "</tr>";
				$ncolor++;
				if($ncolor % 2 == 0)
					$color="#FFFFFF";
				else
					$color="#99CCFF";
				//$path="/matrix/presupuestos/reportes/000001_rc133.php?wcar=".$wcarn."&wcco=".$wcco."&wano=".$wanof."&wmes=".$wmesf."";
				echo "<tr><td bgcolor=".$color."><font size=2>".$wcco."</font></td><td bgcolor=".$color."><font size=2>".$wcarn."-".$wcar."</font></td><td bgcolor=".$color."><font size=2>".$wcon."</font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$inc],0,'.',',')."</font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				$color="#FFCC66";
				//$path="/matrix/presupuestos/reportes/000001_rc133.php?wcar=".$wcarn."&wcco=".$wcco."&wano=".$wanof."&wmes=".$wmesf."";
				echo "<tr><td bgcolor=".$color."><font size=2>".$wcco."</font></td><td bgcolor=".$color."><font size=2>TOTAL : ".$wcarn."-".$wcar."</font></td><td bgcolor=".$color."><font size=2>".$wcon."</font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$tot[$i][$inc],0,'.',',')."</font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
			}
		}
	}
?>
</body>
</html>
