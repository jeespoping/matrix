<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Comparativo de Segmentos De Las Unidades Entre A&ntilde;os</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc154.php Ver. 2016-02-19</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[7] > $vec2[7])
		return 1;
	elseif ($vec1[7] < $vec2[7])
				return -1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc154.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME COMPARATIVO DE SEGMENTO DE LAS UNIDADES ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Ano de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Ingresos</td>";
			echo "<td bgcolor=#cccccc align=center><input type='RADIO' name=tipo checked value=1 ><b>Terceros</b>";
			echo "<input type='RADIO' name=tipo value=2 ><b>Propios</b>";
			echo "<input type='RADIO' name=tipo value=3 ><b>Totales</b></td></tr>";
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
			$wanoa=$wano - 1;
			$wa=-1;
			$tg=array();
			$tp=array();
			$seg=array();
			$seg[0]="EPS";
			$seg[1]="PREPAGADAS";
			$seg[2]="IPS";
			$seg[3]="ASEGURADORAS";
			$seg[4]="PARTICULARES";
			$seg[5]="SOAT";
			$seg[6]="OTROS";
			$key="";
			//                   0      1      2      3       4    
			if($tipo == 1)
				$query  = "select mioano,miocco,cconom,empseg,sum(Mioint) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000005";
			elseif($tipo == 2)
					$query  = "select mioano,miocco,cconom,empseg,sum(Mioinp) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000005";
				else
					$query  = "select mioano,miocco,cconom,empseg,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000005";
			$query .= "	  where mioano in (".$wanoa.",".$wano.")";
			$query .= "     and mioemp = '".$wemp."'";
			$query .= "		and miomes between ".$wper1." and ".$wper2." ";
			$query .= "		and mionit = epmcod ";
			$query .= "		and mioemp = empemp ";   
			$query .= "		and miocco = ccocod ";  
			$query .= "		and mioemp = ccoemp ";  
			$query .= "	 group by 1,2,3,4 ";
			$query .= "	 order by 2,1,4 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($key != $row[1])
					{
						if($i > 0)
						{
							for ($j=0;$j<7;$j++)
							{
								$wa++;
								$tg[$wa][0]=$tp[$j][0];
								$tg[$wa][1]=$tp[$j][1];
								$tg[$wa][2]=$tp[$j][2];
								$tg[$wa][3]=$tp[$j][3];
								$tg[$wa][4]=$tp[$j][4];
							}
						}
						for ($j=0;$j<7;$j++)
						{
							$tp[$j][0]=$row[1];
							$tp[$j][1]=$row[2];
							$tp[$j][2]=$seg[$j];
							$tp[$j][3]=0;
							$tp[$j][4]=0;
						}
						$key=$row[1];
					}
					switch ($row[3])
					{
						case "EPS":
							if($row[0] == $wanoa)
								$tp[0][3] += $row[4];
							else
								$tp[0][4] += $row[4];
						break;
						case "MP":
							if($row[0] == $wanoa)
								$tp[1][3] += $row[4];
							else
								$tp[1][4] += $row[4];
						break;
						case "IPS":
							if($row[0] == $wanoa)
								$tp[2][3] += $row[4];
							else
								$tp[2][4] += $row[4];
						break;
						case "ASEG":
							if($row[0] == $wanoa)
								$tp[3][3] += $row[4];
							else
								$tp[3][4] += $row[4];
						break;
						case "PPJ":
							if($row[0] == $wanoa)
								$tp[4][3] += $row[4];
							else
								$tp[4][4] += $row[4];
						break;
						case "PPN":
							if($row[0] == $wanoa)
								$tp[4][3] += $row[4];
							else
								$tp[4][4] += $row[4];
						break;
						case "SOAT":
							if($row[0] == $wanoa)
								$tp[5][3] += $row[4];
							else
								$tp[5][4] += $row[4];
						break;
						default:
							if($row[0] == $wanoa)
								$tp[6][3] += $row[4];
							else
								$tp[6][4] += $row[4];
						break;
					}
				}
			}
			echo "<center><table border=1>";
			echo "<tr><td colspan=6 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=6 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=6 align=center>INFORME COMPARATIVO DE SEGMENTO DE LAS UNIDADES ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=6 align=center>PERIODO BASE : ".$wano."/".$wper1."-".$wper2." PERIODO ANTERIOR : ".$wanoa."/".$wper1."-".$wper2."</td></tr>";
			echo "<tr><td><b>CENTRO<BR>DE COSTOS</BR></b></td><td><b>UNIDAD</b></td><td><b>SEGMENTO</b></td><td><b>A&Ntilde;O : ".$wanoa."</b></td><td align=right><b>A&Ntilde;O : ".$wano."</b></td></tr>";
			for ($i=0;$i<=$wa;$i++)
			{
				echo"<tr><td>".$tg[$i][0]."</td><td>".$tg[$i][1]."</td><td>".$tg[$i][2]."</td><td align=right>".number_format((double)$tg[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$tg[$i][4],0,'.',',')."</td></tr>";
			}
			echo "</center></table>";
		}
	}
?>
</body>
</html>
