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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>	</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc167.php Ver. 2017-10-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
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
		

		

		echo "<form action='000001_rc167.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wano2) or !isset($wcco1) or !isset($wcco2) or !isset($wper1) or !isset($wper2)  or !isset($wgru) or !isset($wrub) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DETALLE EXPLICACIONES X TERCERO ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wrub'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
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
			echo "<td bgcolor=#cccccc align=center>Grupo</td>";
			echo "<td bgcolor=#cccccc align=center><select name='wgru'>";
			$query = "SELECT Ccouni from ".$empresa."_000005  group by 1 order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<option>Todos</option>";
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."</option>";
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
			$wrub = substr($wrub,0,strpos($wrub,"-"));	
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
			//                 0       1     2      3          4   
			$query = "select Expnit,Expnte,Expano,Expper,sum(Expmon) ";
			if($wgru != "Todos")
				$query .= "  from ".$empresa."_000011 ,".$empresa."_000005 ";
			else
				$query .= "  from ".$empresa."_000011 ";
			$query .= "	   where ((Expano = ".$wano1;
			$query .= "	     and Expper >= ".$wper1." and Expano < ".$wano2.") ";
			$query .= "	      or (Expano > ".$wano1;
			$query .= "	     and  Expano < ".$wano2.") ";
			$query .= "	      or (Expano = ".$wano2;
			$query .= "	     and  Expper <= ".$wper2." and Expano > ".$wano1.") ";
			$query .= "	      or (Expano = ".$wano1." and Expano = ".$wano2; 
			$query .= "	     and  Expper >= ".$wper1." and Expper <= ".$wper2.")) ";
			$query .= " 	 and expcco between '".$wcco1."' and '".$wcco2."' "; 
			$query .= " 	 and expemp = '".$wemp."' ";  
			$query .= " 	 and expcpr = '".$wrub."' ";  
			if($wgru != "Todos")
			{
				$query .= " 	 and expcco = ccocod ";
				$query .= " 	 and expemp = ccoemp ";
				$query .= " 	 and ccouni = '".$wgru."' ";  
			}
			$query .= "   group by 1,2,3,4 "; 
			$query .= "   order by 1,3,4 ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$tot=array();
				$tott=array();
				$totp=array();
				$totp1=array();
				$totp2=array();
				$cpr=array();
				$cprn=array();
				$ncol=2;
				$ncolor=0;
				$wpaso=0;
				$inc=periodo($wper1,$wfactor);
				$inf=periodo($wper2,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wano1-$wano1);$i<=($wano2-$wano1);$i++)
				{
					while((($i+$wano1) < $wano2 and $inc <= $iter) or (($i+$wano1) == $wano2 and $inc <= $inf))
					{
						$ncol++;
						$inc +=1;
					}
					$inc=1;
				}
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
				echo "<table border=1>";
				echo "<tr><td colspan=".$ncol." align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>DETALLE EXPLICACIONES X TERCERO ENTRE A&Ntilde;OS</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>C.C. INICIAL :<b> ".$wcco1."</b>C.C. FINAL<b> ".$wcco2."</b></font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><b>A&Ntilde;O INICIAL: ".$wano1." A&Ntilde;O FINAL : ".$wano2."</b></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><b>MES INICIAL: ".$wper1." MES FINAL : ".$wper2."</b></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><b>RUBRO : ".$wrub."</b></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td bgcolor=#cccccc><b>Nit</b></td><td bgcolor=#cccccc><b>Tercero</b></td>";
				$inc=periodo($wper1,$wfactor);
				$inf=periodo($wper2,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wano1-$wano1);$i<=($wano2-$wano1);$i++)
				{
					while((($i+$wano1) < $wano2 and $inc <= $iter) or (($i+$wano1) == $wano2 and $inc <= $inf))
					{
						$ano=$i+$wano1;
						if($wfactor != 12)
							echo "<td bgcolor=#cccccc align=center><font size=2><b>".$ano.$wp.$inc."</b></font></td>";
						else
							echo "<td bgcolor=#cccccc align=center><font size=2><b>".$ano."</b></font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				$kr=-1;
				$nita = "";
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					$fil=$row[2] - $wano1;
					$col=periodo($row[3],$wfactor);
					if($nita != trim($row[0]))
					{
						$kr++;
						$nit[$kr] = $row[0];
						$nitn[$kr] = $row[1];
						$nita = trim($row[0]);
					}
					$data[$kr][$fil][$col] += ($row[4] / 1);
					$tot[$fil][$col] += ($row[4] / 1);
				}
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				
				for ($w=0;$w<=$kr;$w++)
				{
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$nit[$w]."</font></td><td bgcolor=".$color."><font size=2>".$nitn[$w]."</font></td>";
					$inc=periodo($wper1,$wfactor);
					$inf=periodo($wper2,$wfactor);
					for ($i=($wano1-$wano1);$i<=($wano2-$wano1);$i++)
					{
						while((($i+$wano1) < $wano2 and $inc <= $iter) or (($i+$wano1) == $wano2 and $inc <= $inf))
						{
							$anoff=$wano1+$i;
							if($anoff == $wano1 and $wano1 == $wano2)
							{
								$partes=explode("-",$meses[$wfactor][$inc]);
								$primero="";
								$ultimo="";
								for ($w1=0;$w1<count($partes);$w1++)
								{
									if(strlen($primero) == 0 and $partes[$w1] >= $wper1)
										$primero=$partes[$w1];
									if($partes[$w1] <= $wper2)
										$ultimo=$partes[$w1];
								}
								$esquema=$primero."-".$ultimo;
							}
							elseif($anoff == $wano1 and $wano1 != $wano2)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wper1)
											$primero=$partes[$w1];
									}
									$ultimo=$partes[count($partes)-1];
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wano2 and $wano1 != $wano2)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if($partes[$w1] <= $wper2)
												$ultimo=$partes[$w1];
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
							$path1="/matrix/presupuestos/reportes/000001_rc168.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&wnit=".$nit[$w]."&empresa=".$empresa."&wgru=".$wgru."&wrub=".$wrub."&wemp=".$wempt;
							echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
							$inc +=1;
						}
						$inc=1;
					}
				}
				echo "</tr>";
				
				//TOTAL
				$inc=periodo($wper1,$wfactor);
				$inf=periodo($wper2,$wfactor);
				echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL</b></font></td>";
				for ($i=($wano1-$wano1);$i<=($wano2-$wano1);$i++)
				{
					while((($i+$wano1) < $wano2 and $inc <= $iter) or (($i+$wano1) == $wano2 and $inc <= $inf))
					{
						echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot[$i][$inc],0,'.',',')."</b></font></td>";
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
