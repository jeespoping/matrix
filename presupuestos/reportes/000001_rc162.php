<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function completar(id)
	{
		if (document.getElementById('witc'+id).checked==true)
		{
			x = document.getElementById('wits'+id).value;
			if(x != "Seleccione")
			{
				y = document.getElementById('wit'+id).value;
				if(y.length >0)
				{
					if(y != "Todos" && x != "Todos")
					{
						y = y + "|" + x;
					}
					else
					{
						y = "Todos";
					}
				}
				else
				{
					y = x;
				}
				document.getElementById('wit'+id).value = y;
			}
		}
		else
		{
			document.getElementById('wits'+id).value = "Seleccione";
			document.getElementById('witn'+id).value = "Orden";
			document.getElementById('wit'+id).value = "";
		}
		
	}
	function orden(id)
	{
		if(id == 0 && document.getElementById('witn'+id).value != '1')
		{
			document.getElementById('witn'+id).value = '1';
		}
		if(id == 1 && document.getElementById('witc'+'0').checked==true)
		{
			document.getElementById('witn'+id).value = '2';
		}
		if(id == 1 && document.getElementById('witc'+'0').checked!=true)
		{
			document.getElementById('witn'+id).value = '1';
		}
		if (document.getElementById('witc'+id).checked==true && document.getElementById('witn'+id).value != "Orden")
		{
			for (i=0;i<6;i++)
			{
				if (document.getElementById('witc'+i).checked==true && document.getElementById('witn'+i).value != "Orden" && i != id && document.getElementById('witn'+id).value == document.getElementById('witn'+i).value)
				{
					document.getElementById('witn'+id).value = "Orden";
				}
			}
		}
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Evaluacion de Convenios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc162.php Ver. 2017-08-30</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][$i]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
function comparacion($vec1,$vec2)
{
	if($vec1[1] > $vec2[1])
		return 1;
	elseif ($vec1[1] < $vec2[1])
				return -1;
			else
				return 0;
}
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc162.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		$wnoway=0;
		for ($i=0;$i<6;$i++)
		{
			if(isset($witc[$i]) and isset($witn[$i]) and $witn[$i] == "Orden")
				$wnoway=1;
			elseif(isset($witc[$i]) and isset($witn[$i]) and $witn[$i] != "Orden" and $wnoway == 0)
					$wnoway=2;
		}
		for ($i=0;$i<6;$i++)
		{
			if(isset($witc[$i]) and isset($wit[$i]) and strlen($wit[$i]) == 0)
				$wnoway=1;
		}
				
		if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wper2) or $wper2 < 1 or $wper2 > 12 or !isset($ok) or $wnoway == 0 or $wnoway == 1)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=3><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=3>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=3>EVALUACION DE CONVENIOS</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			if(isset($wanop))
				echo "<td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='wanop' size=4 maxlength=4 value=".$wanop."></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			if(isset($wper1))
				echo "<td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='wper1' size=2 maxlength=2 value=".$wper1."></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			if(isset($wper2))
				echo "<td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='wper2' size=2 maxlength=2 value=".$wper2."></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";

			$items=array();
			$items[0][0]="Grupos de Empresas";
			$items[0][1]="SELECT Empgru from ".$empresa."_000061 where Empgru like '%";
			$items[0][2]="%'  and Empemp='01' group by 1 order by 1";
			$items[0][3]="SELECT Empgru from ".$empresa."_000061 where Empemp='01' group by 1 order by 1";
			$items[0][4]=1;
			$items[1][0]="Segmentos";
			$items[1][1]="SELECT Segcod from ".$empresa."_000045 where Segcod like '%";
			$items[1][2]="%' group by 1 order by 1";
			$items[1][3]="SELECT Segcod from ".$empresa."_000045 group by 1 order by 1";
			$items[1][4]=1;
			$items[2][0]="Empresas";
			$items[2][1]="SELECT Empcin,Empdes from ".$empresa."_000061 where Empdes like '%";
			$items[2][2]="%' and Empemp='01' group by 1 order by 2";
			$items[2][3]="SELECT Empcin,Empdes from ".$empresa."_000061 where Empemp='01'  group by 1 order by 2";
			$items[2][4]=2;
			$items[3][0]="Lineas";
			$items[3][1]="SELECT Lincod,Lindes from ".$empresa."_000107 where Lindes like '%";
			$items[3][2]="%' group by 1 order by cast(Lincod as UNSIGNED)";
			$items[3][3]="SELECT Lincod,Lindes from ".$empresa."_000107 group by 1 order by cast(Lincod as UNSIGNED)";
			$items[3][4]=2;
			$items[4][0]="Centros de Costos";
			$items[4][1]="SELECT Ccocod, Cconom from ".$empresa."_000005 where Ccoclas='PR' and Cconom like '%";
			$items[4][2]="%' and ccoemp='01' group by 1 order by 1";
			$items[4][3]="SELECT Ccocod, Cconom from ".$empresa."_000005 where Ccoclas='PR' and Ccoest='on' and Ccoemp='01' group by 1 order by 1";
			$items[4][4]=2;
			$items[5][0]="Concepto de Facturacion";
			$items[5][1]="SELECT Cfacod,Cfades from ".$empresa."_000060 where Cfades like '%";
			$items[5][2]="%'  and Cfaemp='01' group by 1 order by 1";
			$items[5][3]="SELECT Cfacod,Cfades from ".$empresa."_000060 where Cfaemp='01' group by 1 order by 1";
			$items[5][4]=2;
			
			for ($i=0;$i<6;$i++)
			{
				echo "<tr><td bgcolor=#cccccc align=center>".$items[$i][0]."</td>";
				echo "<td bgcolor=#cccccc>";
				echo "Criterio : <input type='TEXT' name='witb[".$i."]' size=30 maxlength=30><br>";
				if(isset($witc[$i]))
					echo "<input type='checkbox' name='witc[".$i."]' id='witc".$i."' style='vertical-align:middle;' onClick='completar(".$i.")' checked>";
				else
					echo "<input type='checkbox' name='witc[".$i."]' id='witc".$i."' style='vertical-align:middle;' onClick='completar(".$i.")'>";
				echo "<select name='witn[".$i."]' style='vertical-align:middle;' id='witn".$i."' onchange='orden(".$i.")'>";
				echo "<option>Orden</option>";
				for ($h=0;$h<6;$h++)
				{
					$j=$h+1;
					if(isset($witn[$i]) and $witn[$i] == $j)
						echo "<option selected>".$j."</option>";
					else
						echo "<option>".$j."</option>";
				}
				echo "</select>";
				if(isset($witb[$i]))
					$query = $items[$i][1].$witb[$i].$items[$i][2];
				else
					$query = $items[$i][3];
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wits[".$i."]' onchange='completar(".$i.")' id='wits".$i."' style='vertical-align:middle;'>";
					echo "<option>Seleccione</option>";
					echo "<option>Todos</option>";
					for ($h=0;$h<$num;$h++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wits[$i]) and $wits[$i] == $row[0])
							if($items[$i][4] == 1)
								echo "<option selected>".htmlentities($row[0])."</option>";
							else
								echo "<option selected>".$row[0]."-".htmlentities($row[1])."</option>";
						else
							if($items[$i][4] == 1)
								echo "<option>".htmlentities($row[0])."</option>";
							else
								echo "<option>".$row[0]."-".htmlentities($row[1])."</option>";
					}
					echo "</select>";
				}
				echo "</td>";
				if(isset($wit[$i]))
					echo "<td bgcolor=#cccccc align=center colspan=2><textarea name='wit[".$i."]' cols=30 rows=3 id='wit".$i."' style='vertical-align:middle;'>".$wit[$i]."</textarea>";
				else
					echo "<td bgcolor=#cccccc align=center colspan=2><textarea name='wit[".$i."]' cols=30 rows=3 id='wit".$i."' style='vertical-align:middle;'></textarea>";			
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc colspan=3 align=center><input type='RADIO' name=x value=0>Evaluados&nbsp&nbsp<input type='RADIO' name=x value=1>Sin Evaluar&nbsp&nbsp<input type='RADIO' name=x value=2>Todos</td></tr>";
			echo "<tr><td bgcolor=#999999  colspan=3 align=center><input type='checkbox' name='ok' id='ok' style='vertical-align:middle;'> Datos Completos?</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=3 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$evaco1=array();
			$query = "SELECT Segcod, Segdes from ".$empresa."_000045 group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tot1=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$evaco1[$i][0]=$row[0];
				$evaco1[$i][1]=$row[1];
			}
			
			$evaco2=array();
			$query = "SELECT Empcin,Empdes from ".$empresa."_000061 where Empemp='01' group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tot2=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$evaco2[$i][0]=$row[0];
				$evaco2[$i][1]=$row[1];
			}
			
			$evaco3=array();
			$query = "SELECT Lincod,Lindes from ".$empresa."_000107 group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tot3=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$evaco3[$i][0]=$row[0];
				$evaco3[$i][1]=$row[1];
			}
			
			$evaco4=array();
			$query = "SELECT Ccocod, Cconom from ".$empresa."_000005 where Ccoclas='PR' and Ccoest='on' and Ccoemp='01' group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tot4=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$evaco4[$i][0]=$row[0];
				$evaco4[$i][1]=$row[1];
			}
			
			$evaco5=array();
			$query = "SELECT Cfacod,Cfades from ".$empresa."_000060 where Cfaemp='01' group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tot5=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$evaco5[$i][0]=$row[0];
				$evaco5[$i][1]=$row[1];
			}
			
			$query = "DROP TABLE IF EXISTS tbis";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			
			$query = "CREATE TEMPORARY TABLE if not exists tbis as ";
			$query .= " select Empcin,Empseg,Empgru from ".$empresa."_000061 where Empemp='01' group by 1,2,3 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			
			$query = "CREATE UNIQUE INDEX clave1 on tbis (Empcin(4))";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$var=array();
			$var[0]="Empgru";
			$var[1]="Empseg";
			$var[2]="Mosent";
			$var[3]="Moslin";
			$var[4]="Moscco";
			$var[5]="Moscon";
			$nom=array();
			$nom[0]="GRUPO";
			$nom[1]="SEGMENTO";
			$nom[2]="ENTIDAD";
			$nom[3]="LINEA";
			$nom[4]="CENTRO DE COSTOS";
			$nom[5]="CONCEPTO";
			$orden=array();
			for ($i=0;$i<6;$i++)
				if(isset($witc[$i]))
				{
					$orden[$i][0]=$i;
					$orden[$i][1]=$witn[$i];
					$orden[$i][2]=$var[$i];
					$orden[$i][3]=$wit[$i];
					$orden[$i][4]=$nom[$i];
				}
			usort($orden,'comparacion');
			$query = "SELECT ";
			for ($i=0;$i<count($orden);$i++)
			{
				if($i > 0)
					$query .= ",";
				$query .= $orden[$i][2];
			}
			switch ($x)
			{
				case 0:
					$Rquery = " AND Mosest='on'";
				break;
				case 1:
					$Rquery = " AND Mosest='off'";
				break;
				case 2:
					$Rquery = " ";
				break;
			}
			if(strpos($query,"Emp") !== false)
				$query .= ",sum(Mosipr),sum(Mosite),sum(Mosctt),sum(Mosutt),sum(Mosctv),sum(Mosutv) FROM ".$empresa."_000108,tbis WHERE Mosano=".$wanop." and Mosmes between ".$wper1." and ".$wper2." and Mosent = Empcin ".$Rquery;
			else
				$query .= ",sum(Mosipr),sum(Mosite),sum(Mosctt),sum(Mosutt),sum(Mosctv),sum(Mosutv) FROM ".$empresa."_000108 WHERE Mosano=".$wanop." and Mosmes between ".$wper1." and ".$wper2." ".$Rquery;
			for ($i=0;$i<count($orden);$i++)
			{
				if($orden[$i][3] != "Todos")
				{
					$query .= " AND ".$orden[$i][2]." in (";
					$pieces = explode("|", $orden[$i][3]);
					for ($j=0;$j<count($pieces);$j++)
					{
						$pieces[$j] = ver($pieces[$j]);
						if($j > 0)
							$query .= ",";
						$query .= "'".$pieces[$j]."'";
					}
					$query .= ") ";
				}
			}
			$query .= " GROUP BY ";
			for ($i=0;$i<count($orden);$i++)
			{
				if($i > 0)
					$query .= ",";
				$j = $i + 1;
				$query .= $j;
			}
			$query .= " ORDER BY ";
			for ($i=0;$i<count($orden);$i++)
			{
				if($i > 0)
					$query .= ",";
				$j = $i + 1;
				$query .= $j;
			}

			echo "<center><table border=1>";
			echo "<tr>";
			for ($i=0;$i<count($orden);$i++)
				echo "<td>".$orden[$i][4]."</td>";
			echo "<td>INGRESO PROPIO</td><td>INGRESO TERCEROS</td><td>COSTO TOTAL</td><td>UTILIDAD TOTAL</td><td>COSTO TOTAL VARIABLE</td><td>UTILIDAD TOTAL VARIABLE</td><td>MARGEN TOTAL</td><td>MARGEN CONTRIBUCION</td>";
			echo "</tr>";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$z=count($orden);
				$tot = array();
				for ($j=0;$j<8;$j++)
					$tot[$j] = 0;
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					echo "<tr>";
					for ($i=0;$i<$z;$i++)
					{
						switch ($orden[$i][2])
						{
							case "Empgru":
								echo "<td>".$row1[$i]."</td>";
							break;
							case "Empseg":
								$pos=bi($evaco1,$tot1,$row1[$i],0);
								if($pos == -1)
									echo "<td>".$row1[$i]."</td>";
								else
									echo "<td>".$row1[$i]."-".$evaco1[$pos][1]."</td>";
							break;
							case "Mosent":
								$pos=bi($evaco2,$tot2,$row1[$i],0);
								if($pos == -1)
									echo "<td>".$row1[$i]."</td>";
								else
									echo "<td>".$row1[$i]."-".$evaco2[$pos][1]."</td>";
							break;
							case "Moslin":
								$pos=bi($evaco3,$tot3,$row1[$i],0);
								if($pos == -1)
									echo "<td>".$row1[$i]."</td>";
								else
									echo "<td>".$row1[$i]."-".$evaco3[$pos][1]."</td>";
							break;
							case "Moscco":
								$pos=bi($evaco4,$tot4,$row1[$i],0);
								if($pos == -1)
									echo "<td>".$row1[$i]."</td>";
								else
									echo "<td>".$row1[$i]."-".$evaco4[$pos][1]."</td>";
							break;
							case "Moscon":
								$pos=bi($evaco5,$tot5,$row1[$i],0);
								if($pos == -1)
									echo "<td>".$row1[$i]."</td>";
								else
									echo "<td>".$row1[$i]."-".$evaco5[$pos][1]."</td>";
							break;
						}
					}
					if($row1[$z] > 0)
					{
						$margenT = $row1[$z+3] / $row1[$z] * 100;
						$margenC = $row1[$z+5] / $row1[$z] * 100;
					}
					else
					{
						$margenT = 0;
						$margenC = 0;
					}
					$tot[0] += (double)$row1[$z];
					$tot[1] += (double)$row1[$z+1];
					$tot[2] += (double)$row1[$z+2];
					$tot[3] += (double)$row1[$z+3];
					$tot[4] += (double)$row1[$z+4];
					$tot[5] += (double)$row1[$z+5];
					echo "<td align=right>".number_format((double)$row1[$z],0,'.',',')."</td>";
					echo "<td align=right>".number_format((double)$row1[$z+1],0,'.',',')."</td>";
					echo "<td align=right>".number_format((double)$row1[$z+2],0,'.',',')."</td>";
					echo "<td align=right>".number_format((double)$row1[$z+3],0,'.',',')."</td>";
					echo "<td align=right>".number_format((double)$row1[$z+4],0,'.',',')."</td>";
					echo "<td align=right>".number_format((double)$row1[$z+5],0,'.',',')."</td>";
					echo "<td align=right>".number_format((double)$margenT,1,'.',',')."</td>";
					echo "<td align=right>".number_format((double)$margenC,1,'.',',')."</td>";
					echo "</tr>";
				}
				$tot[6] = $tot[3] / $tot[0] * 100;
				$tot[7] = $tot[5] / $tot[0] * 100;
				echo "<tr>";
				echo "<td colspan=".count($orden).">TOTALES</td>";
				echo "<td align=right>".number_format((double)$tot[0],0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$tot[1],0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$tot[2],0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$tot[3],0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$tot[4],0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$tot[5],0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$tot[6],1,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$tot[7],1,'.',',')."</td>";
				echo "</tr>";
			}
		}
}
?>
</body>
</html>
