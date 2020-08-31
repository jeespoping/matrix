<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
	<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Comparativo de Ingresos</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc129.php Ver. 2016-02-19</b></font></td></tr></table>
</center>


<?php
include_once("conex.php");
function periodo($mes,$factor)
{
	// Funcion que permite dividir el ano en periodos dados x el factor y saber el mes en que periodo se encuentra
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
		

		

		echo "<form action='000001_rc129.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanof) or !isset($wmesi) or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or !isset($wmesf) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME COMPARATIVO DE INGRESOS</td></tr>";
			echo "<tr><td align=center colspan=2>000001_rc129.php Ver. 2008-02-19</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
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
			//                   0       1       2      3       4           5           6
			$query  = "select Miocco, Cconom, Ccouni, Mioano, Miomes, sum(Mioinp), sum(Mioint) from ".$empresa."_000063,".$empresa."_000005 ";
			$query .= "where ((Mioano = ".$wanoi;
			$query .= "	and Miomes >= ".$wmesi." and Mioano < ".$wanof.") ";
			$query .= "	or (Mioano > ".$wanoi;
			$query .= "	and  Mioano < ".$wanof.") ";
			$query .= "	or (Mioano = ".$wanof;
			$query .= "	and  Miomes <= ".$wmesf." and Mioano > ".$wanoi.") ";
			$query .= "	or (Mioano = ".$wanoi." and Mioano = ".$wanof; 
			$query .= "	and  Miomes >= ".$wmesi." and Miomes <= ".$wmesf.")) ";
			$query .= " and mioemp = '".$wemp."'";
			$query .= "	and miocco = ccocod   ";
			$query .= "	and mioemp = ccoemp   ";
			$query .= "	group by Miocco,Cconom,Ccouni,Mioano, Miomes   ";
			$query .= "	order by Ccouni,miocco,Mioano, Miomes ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$Tipos=array();
				$tipos["1Q"]="UNIDADES QUIRURGICAS";
				$tipos["2H"]="UNIDADES DE HOSPITALIZACION";
				$tipos["3D"]="UNIDADES DE APOYO DIAGNOSTICO Y TERAPEUTICO";
				$tipos["4A"]="UNIDAD DE URGENCIAS";
				$tipos["5O"]="OTRAS UNIDADES";
				$tipos["7E"]="UNIDADES EXTERNAS";
				$tipos["6OGI"]="OTRAS UNIDADES";
				$wtipa="";
				$wccoa="";
				$wccoan="";
				$data=array();
				$terc=array();
				$totl=array();
				$tot2=array();
				$tot3=array();
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
				echo "<tr><td colspan=".$ncol." align=center><font size=2>INFORME COMPARATIVO DE INGRESOS</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>CIFRAS EN MILLONES DE PESOS</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>DESDE EL MES ".$wmesi." A&Ntilde;O ".$wanoi." HASTA EL MES ".$wmesf." A&Ntilde;O ".$wanof."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=1.5>000001_rc129.php Ver. 2008-02-19</font></td></tr>";
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
				echo "<tr><td bgcolor=#cccccc><font size=2><b>CENTRO DE<BR>COSTOS</b></font></td><td bgcolor=#cccccc><font size=2><b>DESCRIPCION</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$data[$i][$inc]=0;
						$terc[$i][$inc]=0;
						$totl[$i][$inc]=0;
						$tot2[$i][$inc]=0;
						$tot3[$i][$inc]=0;
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
					if($row[2] == "6OGI")
						$row[2]="5O";
					if($row[0] != $wccoa)
					{
						if($j != 0)
						{
							$ncolor++;
							if($ncolor % 2 == 0)
								$color="#FFFFFF";
							else
								$color="#99CCFF";
							echo "<tr><td bgcolor=".$color."><font size=2>".$wccoa."</font></td><td bgcolor=".$color."><font size=2>".$wccoan."</font></td>";
							$inc=periodo($wmesi,$wfactor);
							$inf=periodo($wmesf,$wfactor);
							$iter=12 / $wfactor;
							for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
							{
								while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
								{
									echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$inc],2,'.',',')."</font></td>";
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
								$data[$i][$inc]=0;
								$inc +=1;
							}
							$inc=1;
						}
						if($wtipa != $row[2])
						{
							if($j != 0)
							{
								echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b> TOTAL ".$tipos[$wtipa]."</b></font></td>";
								$inc=periodo($wmesi,$wfactor);
								$inf=periodo($wmesf,$wfactor);
								$iter=12 / $wfactor;
								for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
								{
									while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
									{
										echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot2[$i][$inc],2,'.',',')."</b></font></td>";
										$inc +=1;
									}
									$inc=1;
								}
								echo "</tr>";
							}
							$wtipa=$row[2];
							$inc=periodo($wmesi,$wfactor);
							$inf=periodo($wmesf,$wfactor);
							$iter=12 / $wfactor;
							for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
							{
								while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
								{
									$tot2[$i][$inc]=0;
									$inc +=1;
								}
								$inc=1;
							}
						}
					}
					
					$fil=$row[3] - $wanoi;
					$col=periodo($row[4],$wfactor);
					$data[$fil][$col] += ($row[5] / 1000000);
					$totl[$fil][$col] += ($row[5] / 1000000);
					$tot2[$fil][$col] += ($row[5] / 1000000);
					$terc[$fil][$col] += ($row[6] / 1000000);
					$tot3[$fil][$col] += ($row[5] / 1000000);
					$tot3[$fil][$col] += ($row[6] / 1000000);
				}
				
				echo "</tr>";
				$ncolor++;
				if($ncolor % 2 == 0)
					$color="#FFFFFF";
				else
					$color="#99CCFF";
				echo "<tr><td bgcolor=".$color."><font size=2>".$wccoa."</font></td><td bgcolor=".$color."><font size=2>".$wccoan."</font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$inc],2,'.',',')."</font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b> TOTAL ".$tipos[$wtipa]."</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot2[$i][$inc],2,'.',',')."</b></font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				
				echo "<tr><td bgcolor=#CC99FF colspan=2><font size=2><b>TOTAL INGRESOS PROPIOS</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#CC99FF align=right><font size=2><b>".number_format((double)$totl[$i][$inc],2,'.',',')."</b></font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				echo "<tr><td bgcolor=#FFCC66 colspan=2><font size=2><b>TOTAL INGRESOS PARA TERCEROS</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#FFCC66 align=right><font size=2><b>".number_format((double)$terc[$i][$inc],2,'.',',')."</b></font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				echo "<tr><td bgcolor=#999999 colspan=2><font size=2><b>TOTAL GENERAL</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#999999 align=right><font size=2><b>".number_format((double)$tot3[$i][$inc],2,'.',',')."</b></font></td>";
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
