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
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Movimiento Comparativo Costo x Actividad x Unidad x Mes</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc87.php Ver. 2018-07-05</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[15] > $vec2[15])
		return 1;
	elseif ($vec1[15] < $vec2[15])
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
		

		

		echo "<form action='000001_rc87.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2)  or ((!isset($wcco1) or !isset($wcco2)) and !isset($wccof)) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>MOVIMIENTO COMPARATIVO COSTO X ACTIVIDAD X UNIDAD X MES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and strtoupper ($call) == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Inicial</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Final</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wccof'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and strtoupper ($call) == "SIC"))
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 group by 1 order by Empcod";
			else
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153,".$empresa."_000125 where empleado = '".$key."' and empresa = Empcod group by 1 order by Empcod";
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
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=$wcco1;
			}
			for ($wcco=$wcco1;$wcco<=$wcco2;$wcco++)
			{
				while(strlen($wcco) < 4)
					$wcco = "0".$wcco;
				$query = "select count(*)  from ".$empresa."_000005 where ccocod='".$wcco."' and ccoest='on' and ccocos='S' and ccoemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				if($row[0] > 0)
				{
					echo "<table border=1>";
					echo "<tr><td colspan=17 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
					echo "<tr><td colspan=17 align=center>DIRECCION DE INFORMATICA</td></tr>";
					echo "<tr><td colspan=17 align=center>MOVIMIENTO COMPARATIVO COSTO X ACTIVIDAD X UNIDAD X MES</td></tr>";
					echo "<tr><td colspan=17 align=center>EMPRESA : ".$wempt."</td></tr>";
					echo "<tr><td colspan=17 align=center> UNIDAD :".$wcco."</td></tr>";
					echo "<tr><td colspan=17 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&ntilde;O : ".$wanop."</td></tr>";
					echo "<tr><td colspan=17 align=center bgcolor=99CCFF><b>GASTOS DISTRIBUIDOS</b></td></tr>";
					$wdat=array();
					$wdatt=array();
					$wmeses=array();
					for ($i=$wper1;$i<=$wper2;$i++)
					{
						switch ($i)
						{
							case 1:
								$wmeses[$i]="ENERO";
								break;
							case 2:
								$wmeses[$i]="FEBRERO";
								break;
							case 3:
								$wmeses[$i]="MARZO";
								break;
							case 4:
								$wmeses[$i]="ABRIL";
								break;
							case 5:
								$wmeses[$i]="MAYO";
								break;
							case 6:
								$wmeses[$i]="JUNIO";
								break;
							case 7:
								$wmeses[$i]="JULIO";
								break;
							case 8:
								$wmeses[$i]="AGOSTO";
								break;
							case 9:
								$wmeses[$i]="SEPTIEMBRE";
								break;
							case 10:
								$wmeses[$i]="OCTUBRE";
								break;
							case 11:
								$wmeses[$i]="NOVIEMBRE";
								break;
							case 12:
								$wmeses[$i]="DICIEMBRE";
								break;
						}
					}
					echo "<tr><td bgcolor=#cccccc><b>SUBPROCESO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td>";
					for ($i=$wper1;$i<=$wper2;$i++)
						echo "<td align=center bgcolor=#cccccc><b>".$wmeses[$i]."</b></td>";
					echo "<td bgcolor=#cccccc><b>PROMEDIO</b></td>";
					echo "</tr>";
					$query = "select Gassub,Subdes,Gasmes,sum(Gasval)  as monto from ".$empresa."_000087,".$empresa."_000104 ";
					$query = $query."  where Gasano  = ".$wanop;
					$query = $query."    and Gasemp = '".$wemp."' ";
					$query = $query."    and Gasmes  between ".$wper1." and ".$wper2;
					$query = $query."    and Gascco = '".$wcco."' ";
					$query = $query."    and Gassub = subcod ";
					$query = $query."   group by  Gassub,Subdes,Gasmes  ";
					$query = $query."   order by Gassub,Gasmes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$seg=-1;
					$segn="";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($row[0] != $segn)
						{
							$seg++;
							$segn=$row[0];
							$wdat[$seg][0]=$row[0];
							$wdat[$seg][1]=$row[1];
							for ($j=2;$j<14;$j++)
								$wdat[$seg][$j]=0;
						}
						$wdat[$seg][$row[2]+1]+=$row[3];
					}
					for ($i=0;$i<=$seg;$i++)
					{
						echo"<tr><td>".$wdat[$i][0]."</td><td>".$wdat[$i][1]."</td>";
						$prom=0;
						for ($j=$wper1;$j<=$wper2;$j++)
						{
							$path="/matrix/presupuestos/reportes/000001_rc85.php?wanop=".$wanop."&wper1=".$j."&wper2=".$j."&wcco1=".$wcco."&wcco2=".$wcco."&wsubp=".$wdat[$i][0]."-".$wdat[$i][1]."&wres=2&empresa=".$empresa."&wemp=".$wempt;
							echo "<td align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";	
							$prom += $wdat[$i][$j+1];
						}
						$prom = $prom / ($wper2 - $wper1 + 1);
						echo "<td align=right>".number_format((double)$prom,2,'.',',')."</td>";	
						echo "</tr>";	
					}
					echo "<tr><td colspan=16 align=center bgcolor=FFCC66><b>DRIVERS DE ACTIVIDAD</b></td></tr>";
					echo "<tr><td bgcolor=#cccccc><b>SUBPROCESO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td>";
					for ($i=$wper1;$i<=$wper2;$i++)
						echo "<td align=center bgcolor=#cccccc><b>".$wmeses[$i]."</b></td>";
					echo "<td bgcolor=#cccccc><b>PROMEDIO</b></td>";
					echo "</tr>";
					$query = "select trim(Mdasub),Subdes,Mdames,sum(Mdacan) as monto,Dicdri,Dicndr from ".$empresa."_000090,".$empresa."_000104,".$empresa."_000018 ";
					$query = $query."  where Mdaano  = ".$wanop;
					$query = $query."    and Mdaemp = '".$wemp."' ";
					$query = $query."    and Mdames  between ".$wper1." and ".$wper2;
					$query = $query."    and Mdacco = '".$wcco."' ";
					$query = $query."    and Mdasub = subcod ";
					$query = $query."    and Mdaemp = Dicemp ";
					$query = $query."    and Mdacco = Diccco ";
					$query = $query."    and Mdasub = Dicsub ";
					$query = $query."   group by  trim(Mdasub),Subdes,Mdames  ";
					$query = $query."   order by Mdasub,Mdames";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$seg=-1;
					$segn="";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(trim($row[0]) != $segn)
						{
							$seg++;
							$segn=$row[0];
							$wdat[$seg][0]=$row[0];
							$wdat[$seg][1]=$row[1]."<br><br><b>".strtolower($row[4]."-".$row[5])."</b>";
							for ($j=2;$j<14;$j++)
								$wdat[$seg][$j]=0;
						}
						$wdat[$seg][$row[2]+1]+=$row[3];
					}
					for ($i=0;$i<=$seg;$i++)
					{
						$prom=0;
						echo"<tr><td>".$wdat[$i][0]."</td><td>".$wdat[$i][1]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
						{
							echo "<td align=right>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";	
							$prom += $wdat[$i][$j+1];
						}
						$prom = $prom / ($wper2 - $wper1 + 1);
						echo "<td align=right>".number_format((double)$prom,2,'.',',')."</td>";	
						echo "</tr>";	
					}
					echo "<tr><td colspan=16 align=center bgcolor=FF0000><b>COSTO X ACTIVIDAD</b></td></tr>";	
					echo "<tr><td bgcolor=#cccccc><b>SUBPROCESO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td>";
					for ($i=$wper1;$i<=$wper2;$i++)
						echo "<td align=center bgcolor=#cccccc><b>".$wmeses[$i]."</b></td>";
					echo "<td bgcolor=#cccccc><b>PROMEDIO</b></td>";
					echo "</tr>";	
					//                 0      1      2      3         4
					$query = "select Cxasub,Subdes,Cxames,Cxaest,sum(Cxacos)  as monto from ".$empresa."_000083,".$empresa."_000104 ";
					$query = $query."  where Cxaano  = ".$wanop;
					$query = $query."    and Cxaemp = '".$wemp."' ";
					$query = $query."    and Cxames  between ".$wper1." and ".$wper2;
					$query = $query."    and Cxacco = '".$wcco."' ";
					$query = $query."    and Cxasub = subcod ";
					$query = $query."   group by  Cxasub,Subdes,Cxames,Cxaest  ";
					$query = $query."   order by Cxasub,Cxames ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$seg=-1;
					$segn="";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($row[0] != $segn)
						{
							$seg++;
							$segn=$row[0];
							$wdat[$seg][0]=$row[0];
							$wdat[$seg][1]=$row[1];
							for ($j=2;$j<14;$j++)
							{
								$wdat[$seg][$j]=0;
								$wdat[$seg][$j+12]="on";
							}
						}
						$wdat[$seg][$row[2]+1]+=$row[4];
						$wdat[$seg][$row[2]+13]=$row[3];
					}
					for ($i=0;$i<=$seg;$i++)
					{
						$prom=0;
						$numero=0;
						echo"<tr><td>".$wdat[$i][0]."</td><td>".$wdat[$i][1]."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
						{
							echo "<td align=right>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";
							if($wdat[$i][$j+13] == "on" and $wdat[$i][$j+1] > 0)
							{
								$numero++;
								$prom += $wdat[$i][$j+1];	
							}
						}
						if($numero > 0)
							$prom = $prom / ($numero);
						else
							$prom = 0;
						echo "<td align=right>".number_format((double)$prom,2,'.',',')."</td>";	
						echo "</tr>";	
					}
					echo "</table>";	
				}
			}
		}
	}
?>
</body>
</html>
