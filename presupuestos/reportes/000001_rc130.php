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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Estado de Resultados Comparativo x Periodo</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc130.php Ver. 2018-06-14</b></font></tr></td></table>
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
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name='r130' action='000001_rc130.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanof) or !isset($wmesi) or !isset($wmesf) or !isset($wcco1)  or !isset($wcco2) or !isset($wserv) or (strtoupper($wserv) != "S" and strtoupper($wserv) != "N") or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ESTADO DE RESULTADOS COMPARATIVO POR PERIODO</td></tr>";
			echo "<tr><td align=center colspan=2>000001_rc130.php</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000161 where empleado ='".$key."'  and cc=ccocod group by 1 order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wcco1'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				$wcco2="NO";
				echo "<center><input type='HIDDEN' name= 'wcco2' value='".$wcco2."'>";
				echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153,".$empresa."_000125 where empleado = '".$key."' and empresa = Empcod group by 1 order by Empcod";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wemp' OnChange='enter()'>";
					echo "<option>Seleccione</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wemp) and substr($wemp,0,strpos($wemp,"-")) == $row[0])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Grupo de Unidades</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT ccouni  from ".$empresa."_000161 group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wgru'>";
				echo "<option>Todos</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centros de Servicio ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
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
			if(!isset($wcco2) or $wcco2 == "NO")
			{
				$wcco1 = ver($wcco1);
				$wcco2 = $wcco1;
			}
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
			$query  = "select Meccpr, Mganom, MID(Meccpr,1,1), Mecano, Mecmes, sum(Mecval) from ".$empresa."_000026,".$empresa."_000028,".$empresa."_000161  ";
			$query .= "	where ((Mecano =  ".$wanoi;
			$query .= "	  and Mecmes >= ".$wmesi." and Mecano < ".$wanof.") ";
			$query .= "	   or (Mecano > ".$wanoi;
			$query .= "	  and  Mecano < ".$wanof.") ";
			$query .= "	   or (Mecano = ".$wanof;
			$query .= "	  and  Mecmes <= ".$wmesf." and Mecano > ".$wanoi.") ";
			$query .= "	   or (Mecano = ".$wanoi." and Mecano = ".$wanof; 
			$query .= "	  and  Mecmes >= ".$wmesi." and Mecmes <= ".$wmesf.")) ";
			$query .= "   and Mecemp = '".$wemp."'";
			$query .= "	  and Meccco between '".$wcco1."' and '".$wcco2."'";
			$query .= "	  and Meccco = Ccocod ";
			$query .= "	  and Mecemp = Ccoemp ";
			if($wgru != "Todos")
			{
				$query .= "    and Ccouni = '".$wgru."' ";
			}
			$query .= "	  and Meccpr = Mgacod  "; 
			if(strtoupper($wserv) == "N") 
				$query .= "   and mgatip = '0' ";
			$query .= "	group by Meccpr, Mganom, MID(Meccpr,1,1), Mecano, Mecmes  ";
			$query .= "	order by MID(Meccpr,1,1),Meccpr, Mecano, Mecmes  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wtipa="";
				$wcpra="";
				$wcpran="";
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
				echo "<tr><td colspan=".$ncol." align=center><font size=2>ESTADO DE RESULTADOS COMPARATIVO POR PERIODO</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>DESDE EL MES ".$wmesi." A&Ntilde;O ".$wanoi." HASTA EL MES ".$wmesf." A&Ntilde;O ".$wanof."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=1.5>000001_rc130.php</font></td></tr>";
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
				echo "<tr><td bgcolor=#cccccc><font size=2><b>CODIGO</b></font></td><td bgcolor=#cccccc><font size=2><b>RUBRO</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						////$data[$i][$inc]=0;
						//for ($w=1;$w<=10;$w++)
							//$tot[$w][$i][$inc]=0;
						//$tot[90][$i][$inc]=0;
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
				
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					$fil=$row[3] - $wanoi;
					$col=periodo($row[4],$wfactor);
					//echo $fil." ".$col." ".$row[0]."<br>";
					$cpr[(integer)$row[0]] = $row[0];
					$cprn[(integer)$row[0]] = $row[1];
					$data[(integer)$row[0]][$fil][$col] += ($row[5] / 1);
					if($row[0] > "129" and $row[0] < "199")
						$row[0] = "0".$row[0];
					$tot[(integer)substr($row[0],0,1)][$fil][$col] += ($row[5] / 1);
					$tott[(integer)substr($row[0],0,1)] = $row[0];
				}
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				
				
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$totp[$i][$inc] = 0;
						$inc +=1;
					}
					$inc=1;
				}
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$totp1[$i][$inc] = 0;
						$inc +=1;
					}
					$inc=1;
				}
				
				//UNO
				$ntitle = -1;
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and $cpr[$w] >= "100" and $cpr[$w] <= "129")
					{
						$ntitle++;
						if($ntitle == 0)
							echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><font size=2><i><b>INGRESOS DE OPERACIONES ORDINARIAS</b></i></font></td></tr>";
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$anoff=$wanoi+$i;
								if($anoff == $wanoi and $wanoi == $wanof)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
											$primero=$partes[$w1];
										if($partes[$w1] <= $wmesf)
											$ultimo=$partes[$w1];
									}
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wanoi and $wanoi != $wanof)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
												$primero=$partes[$w1];
										}
										$ultimo=$partes[count($partes)-1];
										$esquema=$primero."-".$ultimo;
									}
									elseif($anoff == $wanof and $wanoi != $wanof)
										{
											$partes=explode("-",$meses[$wfactor][$inc]);
											$primero="";
											$ultimo="";
											for ($w1=0;$w1<count($partes);$w1++)
											{
												if($partes[$w1] <= $wmesf)
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
								if($cpr[$w] == 100)
									$path1="/matrix/presupuestos/reportes/000001_rc30.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&wgru=".$wgru."&call=SIG&empresa=".$empresa."&wemp=".$wempt;
								else
									$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$cpr[$w]."-".$cprn[$w]."&wcodf=".$cpr[$w]."-".$cprn[$w]."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wempt."&wgru=".$wgru;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($w=1;$w<=9;$w++)
				{
					if(isset($tott[$w]) and substr($tott[$w],0,1) == "1")
					{
						echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL INGRESOS DE OPERACIONES ORDINARIAS</b></font></td>";
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=#dddddd align=right align=right><font size=2><b>".number_format((double)$tot[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp[$i][$inc] += $tot[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//DOS
				$ntitle = -1;
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and substr($cpr[$w],0,1) == "2")
					{
						$ntitle++;
						if($ntitle == 0)
							echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><font size=2><i><b>COSTOS DE OPERACION</b></i></font></td></tr>";
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$anoff=$wanoi+$i;
								if($anoff == $wanoi and $wanoi == $wanof)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
											$primero=$partes[$w1];
										if($partes[$w1] <= $wmesf)
											$ultimo=$partes[$w1];
									}
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wanoi and $wanoi != $wanof)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
												$primero=$partes[$w1];
										}
										$ultimo=$partes[count($partes)-1];
										$esquema=$primero."-".$ultimo;
									}
									elseif($anoff == $wanof and $wanoi != $wanof)
										{
											$partes=explode("-",$meses[$wfactor][$inc]);
											$primero="";
											$ultimo="";
											for ($w1=0;$w1<count($partes);$w1++)
											{
												if($partes[$w1] <= $wmesf)
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
								if($cpr[$w] == 201)
									$path1="/matrix/presupuestos/reportes/000001_rc78.php?wanoi=".$wanoi."&wmesi=".$wmesi."&wanof=".$wanof."&wmesf=".$wmesf."&wcco1=".$wcco1."&wcco2=".$wcco2."&wfactor=".$wfactor."&empresa=".$empresa."&wemp=".$wempt;
								elseif($cpr[$w] == 203 or $cpr[$w] == 204)
										$path1="/matrix/presupuestos/reportes/000001_rc63.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIC&wrubro=".$wcpra."&empresa=".$empresa."&wemp=".$wempt;
									elseif($cpr[$w] == 298)
											$path1="/matrix/presupuestos/reportes/000001_rc67.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wres=R&empresa=".$empresa."&wemp=".$wempt;
										else
											$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$cpr[$w]."-".$cprn[$w]."&wcodf=".$cpr[$w]."-".$cprn[$w]."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wempt."&wgru=".$wgru;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($w=1;$w<=9;$w++)
				{
					if(isset($tott[$w]) and substr($tott[$w],0,1) == "2")
					{
						echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL COSTOS DE OPERACION</b></font></td>";
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp[$i][$inc] -= $tot[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//TRES
				$ntitle = -1;
				
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and substr($cpr[$w],0,1) == "3")
					{
						$ntitle++;
						if($ntitle == 0)
							echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><font size=2><i><b>GASTOS DE ADMINISTRACION</b></i></font></td></tr>";
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$anoff=$wanoi+$i;
								if($anoff == $wanoi and $wanoi == $wanof)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
											$primero=$partes[$w1];
										if($partes[$w1] <= $wmesf)
											$ultimo=$partes[$w1];
									}
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wanoi and $wanoi != $wanof)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
												$primero=$partes[$w1];
										}
										$ultimo=$partes[count($partes)-1];
										$esquema=$primero."-".$ultimo;
									}
									elseif($anoff == $wanof and $wanoi != $wanof)
										{
											$partes=explode("-",$meses[$wfactor][$inc]);
											$primero="";
											$ultimo="";
											for ($w1=0;$w1<count($partes);$w1++)
											{
												if($partes[$w1] <= $wmesf)
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
								if($cpr[$w] == 301)
									$path1="/matrix/presupuestos/reportes/000001_rc78.php?wanoi=".$wanoi."&wmesi=".$wmesi."&wanof=".$wanof."&wmesf=".$wmesf."&wcco1=".$wcco1."&wcco2=".$wcco2."&wfactor=".$wfactor."&empresa=".$empresa."&wemp=".$wempt;
								elseif($cpr[$w] == 303 or $cpr[$w] == 304)
										$path1="/matrix/presupuestos/reportes/000001_rc63.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIC&wrubro=".$wcpra."&empresa=".$empresa."&wemp=".$wempt;
									elseif($cpr[$w] == 398)
											$path1="/matrix/presupuestos/reportes/000001_rc67.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wres=R&empresa=".$empresa."&wemp=".$wempt;
										else
											$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$cpr[$w]."-".$cprn[$w]."&wcodf=".$cpr[$w]."-".$cprn[$w]."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wempt."&wgru=".$wgru;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($w=1;$w<=9;$w++)
				{
					if(isset($tott[$w]) and substr($tott[$w],0,1) == "3")
					{
						echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL GASTOS DE ADMINISTRACION</b></font></td>";
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp[$i][$inc] -= $tot[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//OCHO
				$ntitle = -1;
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and substr($cpr[$w],0,1) == "8")
					{
						$ntitle++;
						if($ntitle == 0)
							echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><font size=2><i><b>GASTOS DE VENTAS</b></i></font></td></tr>";
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$anoff=$wanoi+$i;
								if($anoff == $wanoi and $wanoi == $wanof)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
											$primero=$partes[$w1];
										if($partes[$w1] <= $wmesf)
											$ultimo=$partes[$w1];
									}
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wanoi and $wanoi != $wanof)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
												$primero=$partes[$w1];
										}
										$ultimo=$partes[count($partes)-1];
										$esquema=$primero."-".$ultimo;
									}
									elseif($anoff == $wanof and $wanoi != $wanof)
										{
											$partes=explode("-",$meses[$wfactor][$inc]);
											$primero="";
											$ultimo="";
											for ($w1=0;$w1<count($partes);$w1++)
											{
												if($partes[$w1] <= $wmesf)
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
								if($cpr[$w] == 801)
									$path1="/matrix/presupuestos/reportes/000001_rc78.php?wanoi=".$wanoi."&wmesi=".$wmesi."&wanof=".$wanof."&wmesf=".$wmesf."&wcco1=".$wcco1."&wcco2=".$wcco2."&wfactor=".$wfactor."&empresa=".$empresa."&wemp=".$wempt;
								elseif($cpr[$w] == 803 or $cpr[$w] == 804)
										$path1="/matrix/presupuestos/reportes/000001_rc63.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIC&wrubro=".$wcpra."&empresa=".$empresa."&wemp=".$wempt;
									elseif($cpr[$w] == 898)
											$path1="/matrix/presupuestos/reportes/000001_rc67.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wres=R&empresa=".$empresa."&wemp=".$wempt;
										else
											$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$cpr[$w]."-".$cprn[$w]."&wcodf=".$cpr[$w]."-".$cprn[$w]."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wempt."&wgru=".$wgru;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($w=1;$w<=9;$w++)
				{
					if(isset($tott[$w]) and substr($tott[$w],0,1) == "8")
					{
						echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL GASTOS DE VENTAS</b></font></td>";
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp[$i][$inc] -= $tot[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//UNO BARRA
				$ntitle = -1;
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and $cpr[$w] >= "130" and $cpr[$w] <= "199")
					{
						$ntitle++;
						if($ntitle == 0)
							echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><font size=2><i><b>OTROS INGRESOS</b></i></font></td></tr>";
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$anoff=$wanoi+$i;
								if($anoff == $wanoi and $wanoi == $wanof)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
											$primero=$partes[$w1];
										if($partes[$w1] <= $wmesf)
											$ultimo=$partes[$w1];
									}
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wanoi and $wanoi != $wanof)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
												$primero=$partes[$w1];
										}
										$ultimo=$partes[count($partes)-1];
										$esquema=$primero."-".$ultimo;
									}
									elseif($anoff == $wanof and $wanoi != $wanof)
										{
											$partes=explode("-",$meses[$wfactor][$inc]);
											$primero="";
											$ultimo="";
											for ($w1=0;$w1<count($partes);$w1++)
											{
												if($partes[$w1] <= $wmesf)
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
								if($cpr[$w] == 100)
									$path1="/matrix/presupuestos/reportes/000001_rc30.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&wgru=".$wgru."&call=SIG&empresa=".$empresa."&wemp=".$wempt;
								else
									$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$cpr[$w]."-".$cprn[$w]."&wcodf=".$cpr[$w]."-".$cprn[$w]."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wempt."&wgru=".$wgru;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($w=0;$w<=9;$w++)
				{
					if(isset($tott[$w]) and substr($tott[$w],0,1) == "0")
					{
						echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL OTROS INGRESOS</b></font></td>";
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=#dddddd align=right align=right><font size=2><b>".number_format((double)$tot[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp[$i][$inc] += $tot[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//CINCO
				$ntitle = -1;
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and substr($cpr[$w],0,1) == "5")
					{
						$ntitle++;
						if($ntitle == 0)
							echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><font size=2><i><b>OTRO GASTOS DE OPERACION</b></i></font></td></tr>";
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$anoff=$wanoi+$i;
								if($anoff == $wanoi and $wanoi == $wanof)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
											$primero=$partes[$w1];
										if($partes[$w1] <= $wmesf)
											$ultimo=$partes[$w1];
									}
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wanoi and $wanoi != $wanof)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
												$primero=$partes[$w1];
										}
										$ultimo=$partes[count($partes)-1];
										$esquema=$primero."-".$ultimo;
									}
									elseif($anoff == $wanof and $wanoi != $wanof)
										{
											$partes=explode("-",$meses[$wfactor][$inc]);
											$primero="";
											$ultimo="";
											for ($w1=0;$w1<count($partes);$w1++)
											{
												if($partes[$w1] <= $wmesf)
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
								if($cpr[$w] == 515)
									$path1="/matrix/presupuestos/reportes/000001_rc78.php?wanoi=".$wanoi."&wmesi=".$wmesi."&wanof=".$wanof."&wmesf=".$wmesf."&wcco1=".$wcco1."&wcco2=".$wcco2."&wfactor=".$wfactor."&empresa=".$empresa."&wemp=".$wempt;
								elseif($cpr[$w] == 517)
										$path1="/matrix/presupuestos/reportes/000001_rc63.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIC&wrubro=".$wcpra."&empresa=".$empresa."&wemp=".$wempt;
									else
										$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$cpr[$w]."-".$cprn[$w]."&wcodf=".$cpr[$w]."-".$cprn[$w]."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wempt."&wgru=".$wgru;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($w=1;$w<=9;$w++)
				{
					if(isset($tott[$w]) and substr($tott[$w],0,1) == "5")
					{
						echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL OTRO GASTOS DE OPERACION</b></font></td>";
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp[$i][$inc] -= $tot[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//SUBTOTAL
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>RESULTADOS DE ACTIVIDADES DE LA OPERACION</b></font></td>";
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$totp[$i][$inc],0,'.',',')."</b></font></td>";
						$totp1[$i][$inc] += $totp[$i][$inc];
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$totp[$i][$inc] = 0;
						$inc +=1;
					}
					$inc=1;
				}
				
				//CUATRO
				$ntitle = -1;
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and substr($cpr[$w],0,1) == "4")
					{
						$ntitle++;
						if($ntitle == 0)
							echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><font size=2><i><b>INGRESO FINANCIERO</b></i></font></td></tr>";
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$anoff=$wanoi+$i;
								if($anoff == $wanoi and $wanoi == $wanof)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
											$primero=$partes[$w1];
										if($partes[$w1] <= $wmesf)
											$ultimo=$partes[$w1];
									}
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wanoi and $wanoi != $wanof)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
												$primero=$partes[$w1];
										}
										$ultimo=$partes[count($partes)-1];
										$esquema=$primero."-".$ultimo;
									}
									elseif($anoff == $wanof and $wanoi != $wanof)
										{
											$partes=explode("-",$meses[$wfactor][$inc]);
											$primero="";
											$ultimo="";
											for ($w1=0;$w1<count($partes);$w1++)
											{
												if($partes[$w1] <= $wmesf)
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

								$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$cpr[$w]."-".$cprn[$w]."&wcodf=".$cpr[$w]."-".$cprn[$w]."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wempt."&wgru=".$wgru;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($w=1;$w<=9;$w++)
				{
					if(isset($tott[$w]) and substr($tott[$w],0,1) == "4")
					{
						echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL INGRESO FINANCIERO</b></font></td>";
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp[$i][$inc] += $tot[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//SEIS
				$ntitle = -1;
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and substr($cpr[$w],0,1) == "6")
					{
						$ntitle++;
						if($ntitle == 0)
							echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><font size=2><i><b>GASTOS FINANCIEROS</b></i></font></td></tr>";
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								$anoff=$wanoi+$i;
								if($anoff == $wanoi and $wanoi == $wanof)
								{
									$partes=explode("-",$meses[$wfactor][$inc]);
									$primero="";
									$ultimo="";
									for ($w1=0;$w1<count($partes);$w1++)
									{
										if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
											$primero=$partes[$w1];
										if($partes[$w1] <= $wmesf)
											$ultimo=$partes[$w1];
									}
									$esquema=$primero."-".$ultimo;
								}
								elseif($anoff == $wanoi and $wanoi != $wanof)
									{
										$partes=explode("-",$meses[$wfactor][$inc]);
										$primero="";
										$ultimo="";
										for ($w1=0;$w1<count($partes);$w1++)
										{
											if(strlen($primero) == 0 and $partes[$w1] >= $wmesi)
												$primero=$partes[$w1];
										}
										$ultimo=$partes[count($partes)-1];
										$esquema=$primero."-".$ultimo;
									}
									elseif($anoff == $wanof and $wanoi != $wanof)
										{
											$partes=explode("-",$meses[$wfactor][$inc]);
											$primero="";
											$ultimo="";
											for ($w1=0;$w1<count($partes);$w1++)
											{
												if($partes[$w1] <= $wmesf)
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

								$path1="/matrix/presupuestos/reportes/000001_rc06.php?wanop=".$anoff."&wper1=".$primero."&wper2=".$ultimo."&wcco1=".$wcco1."&wcco2=".$wcco2."&call=SIG&wcodi=".$cpr[$w]."-".$cprn[$w]."&wcodf=".$cpr[$w]."-".$cprn[$w]."&wcodin=0&wcodfn=0&empresa=".$empresa."&wemp=".$wempt."&wgru=".$wgru;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($w=1;$w<=9;$w++)
				{
					if(isset($tott[$w]) and substr($tott[$w],0,1) == "6")
					{
						echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>TOTAL GASTOS FINANCIEROS</b></font></td>";
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$tot[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp[$i][$inc] -= $tot[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//SUBTOTAL
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>COSTO FINANCIERO NETO</b></font></td>";
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$totp[$i][$inc],0,'.',',')."</b></font></td>";
						$totp1[$i][$inc] += $totp[$i][$inc];
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$totp2[$i][$inc] = 0;
						$inc +=1;
					}
					$inc=1;
				}
				
				//SUBTOTAL
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>GANANCIAS ANTES DE IMPUESTOS</b></font></td>";
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$totp1[$i][$inc],0,'.',',')."</b></font></td>";
						$totp2[$i][$inc] += $totp1[$i][$inc];
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				//SIETE 60 Y 50
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and ($cpr[$w] == "760" or $cpr[$w] == "770"))
					{
						$ncolor++;
						if($ncolor % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color."><font size=2>".$cpr[$w]."</font></td><td bgcolor=".$color."><font size=2>".$cprn[$w]."</font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
								$totp2[$i][$inc] = $totp2[$i][$inc] - $data[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//SUBTOTAL
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				echo "<tr><td bgcolor=#dddddd colspan=2><font size=2><b>RESULTADOS PROCEDENTES DE OPERACIONES CONTINUADAS</b></font></td>";
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=#dddddd align=right><font size=2><b>".number_format((double)$totp2[$i][$inc],0,'.',',')."</b></font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				//NOVECIENTOS
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and $cpr[$w] == "900")
					{
						$color="#999999";
						echo "<tr><td bgcolor=".$color."><font size=2><b>".$cpr[$w]."</b></font></td><td bgcolor=".$color."><font size=2><b>".$cprn[$w]."</b></font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=".$color." align=right><font size=2><b>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$totp2[$i][$inc] -= $data[$w][$i][$inc];
								$inc +=1;
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				//750
				for ($w=100;$w<=999;$w++)
				{
					if(isset($cpr[$w]) and $cpr[$w] == "750")
					{
						$color="#CCCCCC";
						echo "<tr><td bgcolor=".$color."><font size=2><b>".$cpr[$w]."</b></font></td><td bgcolor=".$color."><font size=2><b>".$cprn[$w]."</b></font></td>";
						$inc=periodo($wmesi,$wfactor);
						$inf=periodo($wmesf,$wfactor);
						for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
						{
							while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
							{
								echo "<td bgcolor=".$color." align=right><font size=2><b>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</b></font></td>";
								$inc +=1;
								$totp2[$i][$inc] -= $data[$w][$i][$inc];
							}
							$inc=1;
						}
					}
				}
				echo "</tr>";
				
				echo "</table>";
			}
		}
	}
?>
</body>
</html>
