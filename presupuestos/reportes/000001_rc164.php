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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comportamiento Comparativo de un Rubro x Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc164.php Ver. 2017-10-04</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
//Estado De Resultados Comparativo Por Periodo
function periodo($mes,$factor)
{
	// Funcion que permite dividir el año en periodos dados x el factor y saber el mes en que periodo se encuentra
    if($mes % $factor == 0)
    	$mes--;
	$periodo=($mes + ($factor - ($mes % $factor)))/$factor;
	return $periodo;
}
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function path($wcco1,$wcco2,$wcpra,$wcpran,$empresa,$wgru,$wanoi,$wanof,$i,&$meses,$wfactor,$inc,$wmesi,$wmesf,$wemp)
{
	$anoff=$wanoi+$i;
	if($anoff == $wanoi and $wanoi == $wanof)
	{
		$partes=explode("-",$meses[$wfactor][$inc]);
		$primero="";
		$ultimo="";
		for ($w=0;$w<count($partes);$w++)
		{
			if(strlen($primero) == 0 and $partes[$w] >= $wmesi)
				$primero=$partes[$w];
			if($partes[$w] <= $wmesf)
				$ultimo=$partes[$w];
		}
		$esquema=$primero."-".$ultimo;
	}
	elseif($anoff == $wanoi and $wanoi != $wanof)
		{
			$partes=explode("-",$meses[$wfactor][$inc]);
			$primero="";
			$ultimo="";
			for ($w=0;$w<count($partes);$w++)
			{
				if(strlen($primero) == 0 and $partes[$w] >= $wmesi)
					$primero=$partes[$w];
			}
			$ultimo=$partes[count($partes)-1];
			$esquema=$primero."-".$ultimo;
		}
		elseif($anoff == $wanof and $wanoi != $wanof)
			{
				$partes=explode("-",$meses[$wfactor][$inc]);
				$primero="";
				$ultimo="";
				for ($w=0;$w<count($partes);$w++)
				{
					if(strlen($ultimo) == 0 and $partes[$w] <= $wmesf)
						$ultimo=$partes[$w];
				}
				$primero=$partes[0];
				$esquema=$primero."-".$ultimo;
			}
			else
			{
				$partes=explode("-",$meses[$wfactor][$inc]);
				$primero=$partes[0];
				$ultimo=$partes[count($partes)-1];
				$esquema=$primero."-".$ultimo;
			}
	
	if($wcpra != 100 and $wcpra != 200 and $wcpra != 201 and $wcpra != 203 and $wcpra != 204 and $wcpra != 298 and $wcpra != 515 and $wcpra != 517 and $wcpra != 598)
		$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$wcpra."-".$wcpran."&wcodf=".$wcpra."-".$wcpran."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wemp;
	elseif($wcpra == 203 or $wcpra == 204 or $wcpra == 517)
			$path1="/matrix/presupuestos/reportes/000001_rc63.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIC&wrubro=".$wcpra."&empresa=".$empresa."&wemp=".$wemp;
		elseif($wcpra == 298 or $wcpra == 598)
				$path1="/matrix/presupuestos/reportes/000001_rc67.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wres=R&empresa=".$empresa."&wemp=".$wemp;
			elseif($wcpra == 100)
					$path1="/matrix/presupuestos/reportes/000001_rc30.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&wgru=".$wgru."&call=SIG&empresa=".$empresa."&wemp=".$wemp;
				elseif($wcpra == 201 or $wcpra == 515)
						$path1="/matrix/presupuestos/reportes/000001_rc78.php?wanoi=".$wanoi."&wmesi=".$wmesi."&wanof=".$wanof."&wmesf=".$wmesf."&wcco1=".$wcco1."&wcco2=".$wcco2."&wfactor=".$wfactor."&empresa=".$empresa."&wemp=".$wemp;
					else
						$path1="";
	return $path1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc164.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione"  or !isset($wanof) or !isset($wmesi) or !isset($wmesf) or !isset($wrubro) or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPORTAMIENTO COMPARATIVO DE UN RUBRO X UNIDAD</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT Rurrub from ".$empresa."_000163 where Rurusu = '".$key."' and Rurest='on' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wtipusu = $row[0];
			}
			else
				$wtipusu = "0";
			if($wtipusu == "*")
				$query = "SELECT mgacod,mganom from ".$empresa."_000028 order by mganom";
			elseif($wtipusu != "0")
					$query = "SELECT mgacod,mganom from ".$empresa."_000028,".$empresa."_000163 where mgacod = Rurrub and Rurusu = '".$key."' and Rurest='on' order by mganom";
				else
					$query = "SELECT mgacod,mganom from ".$empresa."_000028 where 1 = 2 order by mganom";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wrubro'>";
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
			$meses=array();
			$meses[1][1]="1";
			$meses[1][2]="2";
			$meses[1][3]="3";
			$meses[1][4]="4";
			$meses[1][5]="5";
			$meses[1][6]="6";
			$meses[1][7]="7";
			$meses[1][8]="8";
			$meses[1][9]="9";
			$meses[1][10]="10";
			$meses[1][11]="11";
			$meses[1][12]="12";
			
			$meses[3][1]="1-2-3";
			$meses[3][2]="4-5-6";
			$meses[3][3]="7-8-9";
			$meses[3][4]="10-11-12";
			$meses[4][1]="1-2-3-4";
			$meses[4][2]="5-6-7-8";
			$meses[4][3]="9-10-11-12";
			$meses[6][1]="1-2-3-4-5-6";
			$meses[6][2]="7-8-9-10-11-12";
			$meses[12][1]="1-2-3-4-5-6-7-8-9-10-11-12";
			//                   0       1         2              3       4         5     
			$query  = "select Meccco, Cconom, MID(Meccpr,1,1), Mecano, Mecmes, sum(Mecval) from ".$empresa."_000026,".$empresa."_000005  ";
			$query .= "	where Meccpr =  '".substr($wrubro,0,strpos($wrubro,"-"))."'";
			$query .= "    and mecemp = '".$wemp."'";
			$query .= "	  and ((Mecano =  ".$wanoi;
			$query .= "	  and Mecmes >= ".$wmesi." and Mecano < ".$wanof.") ";
			$query .= "	   or (Mecano > ".$wanoi;
			$query .= "	  and  Mecano < ".$wanof.") ";
			$query .= "	   or (Mecano = ".$wanof;
			$query .= "	  and  Mecmes <= ".$wmesf." and Mecano > ".$wanoi.") ";
			$query .= "	   or (Mecano = ".$wanoi." and Mecano = ".$wanof; 
			$query .= "	  and  Mecmes >= ".$wmesi." and Mecmes <= ".$wmesf.")) ";
			$query .= "	  and Meccco = Ccocod ";
			$query .= "   and mecemp = ccoemp ";
			$query .= "	group by Meccco, Cconom, MID(Meccpr,1,1), Mecano, Mecmes  ";
			$query .= "	order by MID(Meccpr,1,1),Meccco, Mecano, Mecmes  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{

				$wcco="";
				$wccom="";
				$data=array();
				$tot=array();
				$ncol=2;
				$ncolor=0;
				$wpaso=0;
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
				echo "<tr><td colspan=".$ncol." align=center><font size=2>COMPORTAMIENTO COMPARATIVO DE UN RUBRO X UNIDAD</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>DESDE EL MES ".$wmesi." A&Ntilde;O ".$wanoi." HASTA EL MES ".$wmesf." A&Ntilde;O ".$wanof."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>RUBRO : ".$wrubro."</td></tr>";
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
				echo "<tr><td bgcolor=#cccccc><font size=2><b>CODIGO</b></font></td><td bgcolor=#cccccc><font size=2><b>NOMBRE</b></font></td>";
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
					if($row[0] != $wcco)
					{
						if($j != 0)
						{
							$ncolor++;
							if($ncolor % 2 == 0)
								$color="#FFFFFF";
							else
								$color="#99CCFF";
							echo "<tr><td bgcolor=".$color."><font size=2>".$wcco."</font></td>";
							echo "<td bgcolor=".$color."><font size=2>".$wccon."</font></td>";
							$inc=periodo($wmesi,$wfactor);
							$inf=periodo($wmesf,$wfactor);
							$iter=12 / $wfactor;
							for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
							{
								while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
								{
									$path1=path($wcco,$wcco,substr($wrubro,0,strpos($wrubro,"-")),substr($wrubro,strpos($wrubro,"-")+1),$empresa,"Todos",$wanoi,$wanof,$i,&$meses,$wfactor,$inc,$wmesi,$wmesf,$wemp);
									echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$i][$inc],0,'.',',')."</font></td>";
									$inc +=1;
								}
								$inc=1;
							}
							echo "</tr>";
						}
						$wcco=$row[0];
						$wccon=$row[1];
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
				$ncolor++;
				if($ncolor % 2 == 0)
					$color="#FFFFFF";
				else
					$color="#99CCFF";
				echo "<tr><td bgcolor=".$color."><font size=2>".$wcco."</font></td>";
				$path1="/matrix/presupuestos/reportes/000001_rc132.php?wanoi=".$wanoi."&wmesi=".$wmesi."&wanof=".$wanof."&wmesf=".$wmesf."&wcco1=".$wcco1."&wcco2=".$wcco2."&wfactor=".$wfactor."&empresa=".$empresa;
				echo "<td bgcolor=".$color." onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".$wccon."</font></td>";
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
				echo "<tr><td bgcolor='#dddddd' colspan=2><font size=2><b>TOTALES</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor='#dddddd' align=right><font size=2><b>".number_format((double)$tot[$i][$inc],0,'.',',')."</b></font></td>";
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
