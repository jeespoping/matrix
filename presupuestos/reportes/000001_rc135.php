<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<center>
	<table border=0 align=center>
	<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Causacion y Flujo de Obligaciones Financieras</font></a></td></tr>
	<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc135.php Ver. 2016-03-08</b></font></td></tr></table>
</center>
<BODY TEXT="#000066">

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc135.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanof))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CAUSACION Y FLUJO DE OBLIGACIONES FINANCIERAS</td></tr>";
			echo "<tr><td align=center colspan=2>000001_rc135.php Ver. 2013-11-01</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
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
			//                   0       1         2          3           4                5   
			$query  = "select Movtob,Movano,sum(Movcin),sum(Movfca),sum(Movfin),sum(Movfca + Movfin) from ".$empresa."_000135 ";
			$query .= "	 where movano between ".$wanoi." and ".$wanof;
			$query .= "    and movemp = '".$wemp."' ";
			$query .= "	   and Movtip in ('R','M') ";
			$query .= "	 group by 1,2 ";
			$query .= "	order by 1,2  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$tot=array();
				$tit[0]="CAUSACION DE INTERESES";
				$tit[1]="FLUJO DE CAPITAL";
				$tit[2]="FLUJO DE INTERESES";
				$tit[3]="FLUJO DE CAPITAL + INTERESES";
				$tit1["CR"]="CREDITOS";
				$tit1["LG"]="LEASING";
				$tit1["FG"]="FACTORING";
				$tit1["CT"]="CREDITOS DE TESORERIA";
				$tit1["RP"]="REPROGRAMACION CREDITOS";
				$tit1["AP"]="ACCIONES PRIVILEGIADAS";
				$tit1["LI"]="LEASING INMOBILIARIO";
				for ($i=$wanoi;$i<=$wanof;$i++)
				{
					$data[$i][0]=0;
					$data[$i][1]=0;
					$data[$i][2]=0;
					$data[$i][3]=0;
					$tot[$i][0]=0;
					$tot[$i][1]=0;
					$tot[$i][2]=0;
					$tot[$i][3]=0;
				}
				$ncol = $wanof - $wanoi + 2;
				echo "<table border=0 align=center>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2><font size=3>DIRECCION DE INFORMATICA</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>CAUSACION Y FLUJO DE OBLIGACIONES FINANCIERAS</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>DESDE EL A&Ntilde;O ".$wanoi." HASTA EL A&Ntilde;O ".$wanof."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=3><b>OBLIGACIONES ACTUALES</b></font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=1.5>000001_rc135.php Ver. 2013-11-01</font></td></tr>";
				echo "<tr><td bgcolor=#cccccc><font size=2><b>TIPO<BR>OBLIGACION</b></font></td>";
				for ($i=$wanoi;$i<=$wanof;$i++)
					echo "<td bgcolor=#cccccc align=center><font size=2><b>".$i."</b></font></td>";
				echo "</tr>";
				$wobl="";
				
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0] != $wobl)
					{
						if($j != 0)
						{
							$ncolor=0;
							echo "<tr><td bgcolor=#FFCC66 colspan=".$ncol."><font size=2>".$tit1[$wobl]."</font></td></tr>";
							for ($ki=0;$ki<3;$ki++)
							{
								$ncolor++;
								if($ncolor % 2 == 0)
									$color="#FFFFFF";
								else
									$color="#99CCFF";
								echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
								for ($i=$wanoi;$i<=$wanof;$i++)
									echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$ki],0,'.',',')."</font></td>";
								echo "</tr>";
							}
						}
						$wobl = $row[0];
						for ($i=$wanoi;$i<=$wanof;$i++)
						{
							$data[$i][0]=0;
							$data[$i][1]=0;
							$data[$i][2]=0;
							$data[$i][3]=0;
						}
					}
					$data[$row[1]][0] += $row[2];
					$data[$row[1]][1] += $row[3];
					$data[$row[1]][2] += $row[4];
					$data[$row[1]][3] += $row[5];
					$tot[$row[1]][0] += $row[2];
					$tot[$row[1]][1] += $row[3];
					$tot[$row[1]][2] += $row[4];
					$tot[$row[1]][3] += $row[5];
				}
				$ncolor=0;
				echo "<tr><td bgcolor=#FFCC66 colspan=".$ncol."><font size=2>".$tit1[$wobl]."</font></td></tr>";
				for ($ki=0;$ki<3;$ki++)
				{
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
					for ($i=$wanoi;$i<=$wanof;$i++)
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$ki],0,'.',',')."</font></td>";
					echo "</tr>";
				}
				$ncolor=0;
				echo "<tr><td bgcolor=#CC99FF colspan=".$ncol."><font size=2><b>TOTALES GENERALES</b></font></td></tr>";
				for ($ki=0;$ki<3;$ki++)
				{
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
					for ($i=$wanoi;$i<=$wanof;$i++)
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$tot[$i][$ki],0,'.',',')."</font></td>";
					echo "</tr>";
				}
				echo "</table><BR><BR>";
			}
			
			
			//                   0       1         2          3           4                5   
			$query  = "select Movtob,Movano,sum(Movcin),sum(Movfca),sum(Movfin),sum(Movfca + Movfin) from ".$empresa."_000135 ";
			$query .= "	 where movano between ".$wanoi." and ".$wanof;
			$query .= "    and movemp = '".$wemp."' ";
			$query .= "	   and Movtip = 'P' ";
			$query .= "	 group by 1,2 ";
			$query .= "	order by 1,2  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$tot=array();
				$tit[0]="CAUSACION DE INTERESES";
				$tit[1]="FLUJO DE CAPITAL";
				$tit[2]="FLUJO DE INTERESES";
				$tit[3]="FLUJO DE CAPITAL + INTERESES";
				$tit1["CR"]="CREDITOS";
				$tit1["LG"]="LEASING";
				$tit1["FG"]="FACTORING";
				$tit1["CT"]="CREDITOS DE TESORERIA";
				$tit1["RP"]="REPROGRAMACION CREDITOS";
				$tit1["AP"]="ACCIONES PRIVILEGIADAS";
				$tit1["LI"]="LEASING INMOBILIARIO";
				for ($i=$wanoi;$i<=$wanof;$i++)
				{
					$data[$i][0]=0;
					$data[$i][1]=0;
					$data[$i][2]=0;
					$data[$i][3]=0;
					$tot[$i][0]=0;
					$tot[$i][1]=0;
					$tot[$i][2]=0;
					$tot[$i][3]=0;
				}
				$ncol = $wanof - $wanoi + 2;
				echo "<table border=0 align=center>";
				echo "<tr><td colspan=".$ncol." align=center><font size=3><b>OBLIGACIONES NUEVAS</b></font></td></tr>";
				echo "<tr><td bgcolor=#cccccc><font size=2><b>TIPO<BR>OBLIGACION</b></font></td>";
				for ($i=$wanoi;$i<=$wanof;$i++)
					echo "<td bgcolor=#cccccc align=center><font size=2><b>".$i."</b></font></td>";
				echo "</tr>";
				$wobl="";
				
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0] != $wobl)
					{
						if($j != 0)
						{
							$ncolor=0;
							echo "<tr><td bgcolor=#FFCC66 colspan=".$ncol."><font size=2>".$tit1[$wobl]."</font></td></tr>";
							for ($ki=0;$ki<3;$ki++)
							{
								$ncolor++;
								if($ncolor % 2 == 0)
									$color="#FFFFFF";
								else
									$color="#99CCFF";
								echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
								for ($i=$wanoi;$i<=$wanof;$i++)
									echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$ki],0,'.',',')."</font></td>";
								echo "</tr>";
							}
						}
						$wobl = $row[0];
						for ($i=$wanoi;$i<=$wanof;$i++)
						{
							$data[$i][0]=0;
							$data[$i][1]=0;
							$data[$i][2]=0;
							$data[$i][3]=0;
						}
					}
					$data[$row[1]][0] += $row[2];
					$data[$row[1]][1] += $row[3];
					$data[$row[1]][2] += $row[4];
					$data[$row[1]][3] += $row[5];
					$tot[$row[1]][0] += $row[2];
					$tot[$row[1]][1] += $row[3];
					$tot[$row[1]][2] += $row[4];
					$tot[$row[1]][3] += $row[5];
				}
				$ncolor=0;
				echo "<tr><td bgcolor=#FFCC66 colspan=".$ncol."><font size=2>".$tit1[$wobl]."</font></td></tr>";
				for ($ki=0;$ki<3;$ki++)
				{
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
					for ($i=$wanoi;$i<=$wanof;$i++)
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$ki],0,'.',',')."</font></td>";
					echo "</tr>";
				}
				$ncolor=0;
				echo "<tr><td bgcolor=#CC99FF colspan=".$ncol."><font size=2><b>TOTALES GENERALES</b></font></td></tr>";
				for ($ki=0;$ki<3;$ki++)
				{
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
					for ($i=$wanoi;$i<=$wanof;$i++)
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$tot[$i][$ki],0,'.',',')."</font></td>";
					echo "</tr>";
				}
				echo "</table><BR><BR>";
			}
			
			
			//                   0       1         2          3           4                5   
			$query  = "select Movtob,Movano,sum(Movcin),sum(Movfca),sum(Movfin),sum(Movfca + Movfin) from ".$empresa."_000135 ";
			$query .= "	 where movano between ".$wanoi." and ".$wanof;
			$query .= "    and movemp = '".$wemp."' ";
			$query .= "	 group by 1,2 ";
			$query .= "	order by 1,2  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$tot=array();
				$tit[0]="CAUSACION DE INTERESES";
				$tit[1]="FLUJO DE CAPITAL";
				$tit[2]="FLUJO DE INTERESES";
				$tit[3]="FLUJO DE CAPITAL + INTERESES";
				$tit1["CR"]="CREDITOS";
				$tit1["LG"]="LEASING";
				$tit1["FG"]="FACTORING";
				$tit1["CT"]="CREDITOS DE TESORERIA";
				$tit1["RP"]="REPROGRAMACION CREDITOS";
				$tit1["AP"]="ACCIONES PRIVILEGIADAS";
				$tit1["LI"]="LEASING INMOBILIARIO";
				for ($i=$wanoi;$i<=$wanof;$i++)
				{
					$data[$i][0]=0;
					$data[$i][1]=0;
					$data[$i][2]=0;
					$data[$i][3]=0;
					$tot[$i][0]=0;
					$tot[$i][1]=0;
					$tot[$i][2]=0;
					$tot[$i][3]=0;
				}
				$ncol = $wanof - $wanoi + 2;
				echo "<table border=0 align=center>";

				echo "<tr><td colspan=".$ncol." align=center><font size=3><b>OBLIGACIONES GENERALES</b></font></td></tr>";
				echo "<tr><td bgcolor=#cccccc><font size=2><b>TIPO<BR>OBLIGACION</b></font></td>";
				for ($i=$wanoi;$i<=$wanof;$i++)
					echo "<td bgcolor=#cccccc align=center><font size=2><b>".$i."</b></font></td>";
				echo "</tr>";
				$wobl="";
				
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0] != $wobl)
					{
						if($j != 0)
						{
							$ncolor=0;
							echo "<tr><td bgcolor=#FFCC66 colspan=".$ncol."><font size=2>".$tit1[$wobl]."</font></td></tr>";
							for ($ki=0;$ki<3;$ki++)
							{
								$ncolor++;
								if($ncolor % 2 == 0)
									$color="#FFFFFF";
								else
									$color="#99CCFF";
								echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
								for ($i=$wanoi;$i<=$wanof;$i++)
									echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$ki],0,'.',',')."</font></td>";
								echo "</tr>";
							}
						}
						$wobl = $row[0];
						for ($i=$wanoi;$i<=$wanof;$i++)
						{
							$data[$i][0]=0;
							$data[$i][1]=0;
							$data[$i][2]=0;
							$data[$i][3]=0;
						}
					}
					$data[$row[1]][0] += $row[2];
					$data[$row[1]][1] += $row[3];
					$data[$row[1]][2] += $row[4];
					$data[$row[1]][3] += $row[5];
					$tot[$row[1]][0] += $row[2];
					$tot[$row[1]][1] += $row[3];
					$tot[$row[1]][2] += $row[4];
					$tot[$row[1]][3] += $row[5];
				}
				$ncolor=0;
				echo "<tr><td bgcolor=#FFCC66 colspan=".$ncol."><font size=2>".$tit1[$wobl]."</font></td></tr>";
				for ($ki=0;$ki<3;$ki++)
				{
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
					for ($i=$wanoi;$i<=$wanof;$i++)
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i][$ki],0,'.',',')."</font></td>";
					echo "</tr>";
				}
				$ncolor=0;
				echo "<tr><td bgcolor=#CC99FF colspan=".$ncol."><font size=2><b>TOTALES GENERALES</b></font></td></tr>";
				for ($ki=0;$ki<3;$ki++)
				{
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$tit[$ki]."</font></td>";
					for ($i=$wanoi;$i<=$wanof;$i++)
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$tot[$i][$ki],0,'.',',')."</font></td>";
					echo "</tr>";
				}
				echo "</table>";
			}
		}
	}
?>
</body>
</html>
