<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
	<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Salarios x Cargo y Unidad (nomina)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc78.php Ver. 2016-03-04</b></font></tr></td></table>
</center>
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
//SALARIOS X CARGO Y UNIDAD (NOMINA)
function periodo($mes,$factor)
{
	// Funcion que permite dividir el año en periodos dados x el factor y saber el mes en que periodo se encuentra
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
		

		

		echo "<form action='000001_rc78.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanof) or !isset($wmesi) or !isset($wmesf) or !isset($wcco1)  or !isset($wcco2) or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>SALARIOS X CARGO Y UNIDAD (NOMINA)</td></tr>";
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
			//                   0       1       2      3       4        5          6       7
			$query  = "select Norcco, Carnom, Norano, Norper, Norcar, Norpre, sum(Normon),Contip from ".$empresa."_000036,".$empresa."_000008, ".$empresa."_000004 ";
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
			$query .= "	  and Contip in ('B','R','O')"; 
			$query .= "	  and Norcar = Carcod ";
			$query .= "   and norfil = Caremp ";
			$query .= "	group by Norcco, Carnom, Norano, Norper, Norcar, Norpre, Contip ";
			$query .= "	order by Norcco, Norcar, Norano, Norper ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wclaa="";
				$wcco="";
				$wcar="";
				$wcarn="";
				$data=array();
				$tot=array();
				$ncol=2;
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
				echo "<tr><td colspan=".$ncol." align=center><font size=2>SALARIOS X CARGO Y UNIDAD (NOMINA)</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>DESDE EL MES ".$wmesi." A&Ntilde;O ".$wanoi." HASTA EL MES ".$wmesf." A&Ntilde;O ".$wanof."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=1.5>000001_rc78.php Ver. 2008-06-17</font></td></tr>";
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
				echo "<tr><td bgcolor=#cccccc><font size=2><b>CENTRO<BR>DE COSTOS</b></font></td><td bgcolor=#cccccc><font size=2><b>CARGO</b></font></td>";
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
					$wcla=$row[0].trim($row[4]);
					if($wclaa != $wcla)
					{
						if($j != 0)
						{
							$ncolor++;
							if($ncolor % 2 == 0)
								$color="#FFFFFF";
							else
								$color="#99CCFF";
							$path="/matrix/presupuestos/reportes/000001_rc10.php?wcarg=".$wcarn."&wcargn=".$wcar."&wcco=".$wcco."&wanoi=".$wanoi."&wmesi=".$wmesi."&wanof=".$wanof."&wmesf=".$wmesf."&wcco1=".$wcco1."&wcco2=".$wcco2."&wfactor=".$wfactor."&empresa=".$empresa."&wemp=".$wemp;
							echo "<tr onclick='ejecutar(".chr(34).$path.chr(34).")'><td bgcolor=".$color."><font size=2>".$wcco."</font></td><td bgcolor=".$color."><font size=2>".$wcarn."-".$wcar."</font></td>";
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
						}
						$wcco=$row[0];
						$wcar=$row[1];
						$wcarn=trim($row[4]);
						$wclaa=$row[0].trim($row[4]);
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
					$fil=$row[2] - $wanoi;
					$col=periodo($row[3],$wfactor);
					if($row[7] != "O")
					{
						$data[$fil][$col] += ($row[6] *(1 + $row[5]));
						$tot[$fil][$col] += ($row[6] *(1 + $row[5]));
					}
					else
					{
						$data[$fil][$col] += $row[6];
						$tot[$fil][$col] += $row[6];
					}
				}
				echo "</tr>";
				$ncolor++;
				if($ncolor % 2 == 0)
					$color="#FFFFFF";
				else
					$color="#99CCFF";
				$path="/matrix/presupuestos/reportes/000001_rc10.php?wcarg=".$wcarn."&wcargn=".$wcar."&wcco=".$wcco."&wanoi=".$wanoi."&wmesi=".$wmesi."&wanof=".$wanof."&wmesf=".$wmesf."&wcco1=".$wcco1."&wcco2=".$wcco2."&wfactor=".$wfactor."&empresa=".$empresa."&wemp=".$wemp;
				echo "<tr onclick='ejecutar(".chr(34).$path.chr(34).")'><td bgcolor=".$color."><font size=2>".$wcco."</font></td><td bgcolor=".$color."><font size=2>".$wcarn."-".$wcar."</font></td>";
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
				echo "<tr><td bgcolor=".$color." colspan=2><font size=2>TOTAL : </font></td>";
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
