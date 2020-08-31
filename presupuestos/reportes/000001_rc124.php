<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Tendencia del Comportamiento de Un Rubro Frente a los Ingresos Propios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc124.php Ver. 2016-02-05</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
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
		

		

		echo "<form action='000001_rc124.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanof) or !isset($wrub) or !isset($wcco1) or !isset($wcco2) or !isset($wmesi) or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or !isset($wmesf) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>TENDENCIA DEL COMPORTAMIENTO DE UN RUBRO FRENTE A LOS INGRESOS PROPIOS</td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wrub' size=3 maxlength=3></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Forma de Agrupacion</td>";
			echo "<td bgcolor=#cccccc>";
			echo "<input type='RADIO' name=wfactor value=1 checked> Mensual<br>";
			echo "<input type='RADIO' name=wfactor value=3> Trimestral<br>";
			echo "<input type='RADIO' name=wfactor value=4> Cuatrimestral<br>";
			echo "<input type='RADIO' name=wfactor value=6> Semestral<br>";
			echo "<input type='RADIO' name=wfactor value=12> Anual";
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query  = "select Mgacod, Mganom from ".$empresa."_000028 ";
			$query .= " where Mgacod = '".$wrub."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wnr=$row[1];
			}
			else
				$wnr="";
			$query  = "select Meccco, Cconom, Meccpr, Mecano, Mecmes, sum(Mecval) from ".$empresa."_000026, ".$empresa."_000005 ";
			$query .= "where ((Mecano = ".$wanoi;
			$query .= "   and Mecmes >= ".$wmesi." and Mecano < ".$wanof.") ";
			$query .= "    or (Mecano > ".$wanoi;
			$query .= "   and  Mecano < ".$wanof.") ";
			$query .= "    or (Mecano = ".$wanof;
			$query .= "   and  Mecmes <= ".$wmesf." and Mecano > ".$wanoi.") ";
			$query .= "    or (Mecano = ".$wanoi." and Mecano = ".$wanof;
			$query .= "   and  Mecmes >= ".$wmesi." and Mecmes <= ".$wmesf.")) ";
			$query .= "   and Meccco between '".$wcco1."' and '".$wcco2."' ";
			$query .= "   and Meccpr in ('100','".$wrub."')  ";
			$query .= "   and Mecemp = '".$wemp."'";
			$query .= "   and Meccco = Ccocod ";
			$query .= "   and Mecemp = Ccoemp ";
			$query .= " group by 1,2,3,4,5 ";
			$query .= " order by 1,4,5  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wccoa="";
				$wccoan="";
				$data=array();
				$totl=array();
				$ncol=2;
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
				echo "<tr><td colspan=".$ncol." align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>TENDENCIA DEL COMPORTAMIENTO DE UN RUBRO FRENTE A LOS INGRESOS PROPIOS</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>DESDE EL MES ".$wmesi." A&Ntilde;O ".$wanoi." HASTA EL MES ".$wmesf." A&Ntilde;O ".$wanof."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>RUBRO : ".$wrub."-".$wnr."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
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
				echo "<tr><td bgcolor=#cccccc><b>RUBRO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$data[$i][$inc][0]=0;
						$data[$i][$inc][1]=0;
						$totl[$i][$inc][0]=0;
						$totl[$i][$inc][1]=0;
						$ano=$i+$wanoi;
						if($wfactor != 12)
							echo "<td bgcolor=#cccccc align=center><b>".$ano.$wp.$inc."</b></td>";
						else
							echo "<td bgcolor=#cccccc align=center><b>".$ano."</b></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0] != $wccoa)
					{
						if($j != 0)
						{
							echo "<tr><td bgcolor=#dddddd colspan=".$ncol."><b>".$wccoa."-".$wccoan."</b></td></tr>";
							echo "<tr><td bgcolor=#99CCFF>100</td><td bgcolor=#99CCFF>INGRESOS</td>";
							$inc=periodo($wmesi,$wfactor);
							$inf=periodo($wmesf,$wfactor);
							$iter=12 / $wfactor;
							for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
							{
								while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
								{
									echo "<td bgcolor=#99CCFF align=right>".number_format((double)$data[$i][$inc][0],0,'.',',')."</td>";
									$inc +=1;
								}
								$inc=1;
							}
							echo "</tr>";
							echo "<tr><td bgcolor=#FFFFFF>".$wrub."</td><td bgcolor=#FFFFFF>".$wnr."</td>";
							$inc=periodo($wmesi,$wfactor);
							$inf=periodo($wmesf,$wfactor);
							$iter=12 / $wfactor;
							for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
							{
								while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
								{
									echo "<td bgcolor=#FFFFFF align=right>".number_format((double)$data[$i][$inc][1],0,'.',',')."</td>";
									$inc +=1;
								}
								$inc=1;
							}
							echo "</tr>";
							echo "<tr><td bgcolor=#99CCFF colspan=2 align=center>PROPORCION</td>";
							$inc=periodo($wmesi,$wfactor);
							$inf=periodo($wmesf,$wfactor);
							$iter=12 / $wfactor;
							for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
							{
								while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
								{
									if($data[$i][$inc][0] != 0)
										$wpro=($data[$i][$inc][1] / $data[$i][$inc][0]) * 100;
									else
										$wpro=0;
									echo "<td bgcolor=#99CCFF align=right>".number_format((double)$wpro,2,'.',',')."%</td>";
									$inc +=1;
								}
								$inc=1;
							}
							echo "</tr>";
						}
						$wccoa=$row[0];
						$wccoan=$row[1];
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						$iter=12 / $wfactor;
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$data[$i][$inc][0]=0;
								$data[$i][$inc][1]=0;
								$inc +=1;
							}
							$inc=1;
						}
					}
					$fil=$row[3] - $wanoi;
					$col=periodo($row[4],$wfactor);
					if($row[2] == "100")
					{
						$data[$fil][$col][0] += $row[5];
						$totl[$fil][$col][0] += $row[5];
					}
					else
					{
						$data[$fil][$col][1] += $row[5];
						$totl[$fil][$col][1] += $row[5];
					}
				}
				echo "<tr><td bgcolor=#dddddd colspan=".$ncol."><b>".$wccoa."-".$wccoan."</b></td></tr>";
				echo "<tr><td bgcolor=#99CCFF>100</td><td bgcolor=#99CCFF>INGRESOS</td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#99CCFF align=right>".number_format((double)$data[$i][$inc][0],0,'.',',')."</td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				echo "<tr><td bgcolor=#FFFFFF>".$wrub."</td><td bgcolor=#FFFFFF>".$wnr."</td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#FFFFFF align=right>".number_format((double)$data[$i][$inc][1],0,'.',',')."</td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				echo "<tr><td bgcolor=#99CCFF colspan=2 align=center>PROPORCION</td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						if($data[$i][$inc][0] != 0)
							$wpro=($data[$i][$inc][1] / $data[$i][$inc][0]) * 100;
						else
							$wpro=0;
						echo "<td bgcolor=#99CCFF align=right>".number_format((double)$wpro,2,'.',',')."%</td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				echo "<tr><td bgcolor=#dddddd colspan=".$ncol."><b>TOTALES</b></td></tr>";
				echo "<tr><td bgcolor=#99CCFF>100</td><td bgcolor=#99CCFF>INGRESOS</td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#99CCFF align=right>".number_format((double)$totl[$i][$inc][0],0,'.',',')."</td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				echo "<tr><td bgcolor=#FFFFFF>".$wrub."</td><td bgcolor=#FFFFFF>".$wnr."</td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#FFFFFF align=right>".number_format((double)$totl[$i][$inc][1],0,'.',',')."</td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				echo "<tr><td bgcolor=#99CCFF colspan=2 align=center>PROPORCION</td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						if($totl[$i][$inc][0] != 0)
							$wpro=($totl[$i][$inc][1] / $totl[$i][$inc][0]) * 100;
						else
							$wpro=0;
						echo "<td bgcolor=#99CCFF align=right>".number_format((double)$wpro,2,'.',',')."%</td>";
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
