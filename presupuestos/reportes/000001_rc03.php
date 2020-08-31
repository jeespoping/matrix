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
	function enter()
	{
		document.forms.rc03.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Ejecucion Presupuestal del Estado de Resultados</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc03.php Ver. 2015-08-25</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function dif($real,$pres,$tip)
{
	if($tip == 1 or $tip == 4 or $tip == 7)
		$dif=$real - $pres;
	else
		$dif=$pres - $real;
	return $dif;
}
		
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc03' action='000001_rc03.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N") or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE EJECUCION PRESUPUESTAL DEL ESTADO DE RESULTADOS</td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
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
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				if(isset($wemp) and $wemp != "Seleccione")
				{
					$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod and Empresa = '".substr($wemp,0,strpos($wemp,"-"))."' order by Cc";
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
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Grupo de Unidades</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT ccouni  from ".$empresa."_000005 group by 1 order by 1";
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
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Centros de Servicio ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wres=strtoupper ($wres);
			$wserv=strtoupper ($wserv);
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			$query = "SELECT rvpcpr,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where rvpano = ".$wanop;
			$query = $query."    and rvpemp = '".$wemp."'";
			$query = $query."    and rvpcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and rvpcco = Ccocod ";
			$query = $query."    and rvpemp = ccoemp ";
			if($wgru != "Todos")
			{
				$query = $query."    and Ccouni = '".$wgru."' ";
			}
			$query = $query."    and rvpper between ".$wper1." and ".$wper2;
			$query = $query."    and rvpcpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by rvpcpr,mganom";
			$query = $query."   order by rvpcpr";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=5 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=5 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=5 align=center>INFORME DE EJECUCION PRESUPUESTAL DEL ESTADO DE RESULTADOS</td></tr>";
			echo "<tr><td colspan=5 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=5 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=5 align=center>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
			echo "<tr><td colspan=5 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td><b>RUBRO</b></td><td><b>REAL</b></td><td><b>PRESUPUESTADO</b></td><td><b>% CUMPLIMIENTO</b></td><td><b>DIFERENCIA</b></td></tr>";
			$wtotalR=array();
			$wtotalP=array();
			$ita=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$it=(integer)substr($row[0],0,1);
				if(!isset($wtotalP[$it]))
				{
					$wtotalR[$it]=0;
					$wtotalP[$it]=0;
					switch ($ita)
					{
						case 1:
						$ita = 7;
						if($wtotalP[1] != 0)
							$wpor=$wtotalR[1]/$wtotalP[1]*100;
						$wdif=dif($wtotalR[1],$wtotalP[1],1);
						if($wtotalP[1] != 0)
							echo"<tr><td><b>INGRESOS NETOS</b></td><td align=right><b>".number_format((double)$wtotalR[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						else
							echo"<tr><td><b>INGRESOS NETOS</b></td><td align=right><b>".number_format((double)$wtotalR[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[1],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						break;
						case 2:
						$ita = 7;
						if($wtotalP[2] != 0)
							$wpor=$wtotalR[2]/$wtotalP[2]*100;
						$wdif=dif($wtotalR[2],$wtotalP[2],2);
						if($wtotalP[2] != 0)
							echo"<tr><td><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right><b>".number_format((double)$wtotalR[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						else
							echo"<tr><td><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right><b>".number_format((double)$wtotalR[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[2],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						if(isset($wtotalR[1]))
							$wtotalR[9] = $wtotalR[1] - $wtotalR[2] ;
						else
							$wtotalR[9] =0 - $wtotalR[2] ;
						if(isset($wtotalP[1]))
                   			$wtotalP[9] = $wtotalP[1] - $wtotalP[2] ;
                   		else
                   			$wtotalP[9] = 0 - $wtotalP[2] ;
						if($wtotalP[9] != 0)
							$wpor=$wtotalR[9]/$wtotalP[9]*100;
						$wdif=dif($wtotalR[9],$wtotalP[9],9);
						echo "<tr><td colspan=5 align=center>--------------------------------------------------</td></tr>";
						if($wtotalP[9] != 0)
							echo"<tr><td><b>UTILIDAD OPERACIONAL</b></td><td align=right><b>".number_format((double)$wtotalR[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						else
							echo"<tr><td><b>UTILIDAD OPERACIONAL</b></td><td align=right><b>".number_format((double)$wtotalR[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[9],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						break;
						case 3:
						break;
						case 4:
						$ita = 7;
						if($wtotalP[4] != 0)
							$wpor=$wtotalR[4]/$wtotalP[4]*100;
						$wdif=dif($wtotalR[4],$wtotalP[4],4);
						if($wtotalP[4] != 0)
							echo"<tr><td><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotalR[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						else
							echo"<tr><td><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotalR[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[4],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						break;
						case 5:
						$ita = 7;
						if($wtotalP[5] != 0)
							$wpor=$wtotalR[5]/$wtotalP[5]*100;
						$wdif=dif($wtotalR[5],$wtotalP[5],5);
						if($wtotalP[5] != 0)
							echo"<tr><td><b>TOTAL GASTOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotalR[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						else
							echo"<tr><td><b>TOTAL GASTOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotalR[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[5],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						break;
						case 6:
						$ita = 7;
						if($wtotalP[6] != 0)
							$wpor=$wtotalR[6]/$wtotalP[6]*100;
						$wdif=dif($wtotalR[6],$wtotalP[6],6);
						if($wtotalP[6] != 0)
							echo"<tr><td><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right><b>".number_format((double)$wtotalR[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						else
							echo"<tr><td><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right><b>".number_format((double)$wtotalR[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[6],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
						break;
					}
					switch ($it)
					{
						case 1:
						echo "<tr><td colspan=5><b>INGRESOS</B></td></tr>";
						$ita=1;
						break;
						case 2:
						echo "<tr><td colspan=5 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=5><b>COSTOS DEL SERVICIO</B></td></tr>";
						$ita=2;
						break;
						case 3:
						echo "<tr><td colspan=5 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=5><b>GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
						$ita=3;
						break;
						case 4:
						echo "<tr><td colspan=5 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=5><b>INGRESOS NO OPERACIONALES</B></td></tr>";
						$ita=4;
						break;
						case 5:
						echo "<tr><td colspan=5 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=5><b>GASTOS NO OPERACIONALES</B></td></tr>";
						$ita=5;
						break;
						case 6:
						echo "<tr><td colspan=5 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=5><b>GASTOS FINANCIEROS</B></td></tr>";
						$ita=6;
						break;
					}
				}
				if($it < 7)
				{
					$wtotalR[$it]=$wtotalR[$it]+$row[2];
					$wtotalP[$it]=$wtotalP[$it]+$row[3];
					if($row[3] != 0)
						$wpor=(string)$row[2]/$row[3]*100;
					$wdif=dif($row[2],$row[3],$it);
					if($wres == "D")
						if($row[3] != 0)
						{
							if($call == "SIF")
							{
								$path1="/matrix/presupuestos/reportes/000001_rc130.php?wanoi=".$wanop."&wanof=".$wanop."&wmesi=".$wper1."&wmesf=".$wper2."&wcco1=".$wccof."&wcco2=NO&wserv=".$wserv."&wgru=".$wgru."&call=SIF&wfactor=1&empresa=".$empresa;
								$path2="/matrix/presupuestos/reportes/000001_rc149.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wcco=".$wccof."&wcod=".$row[0]."&empresa=".$empresa;
								echo"<tr><td>".$row[1]."</td><td align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'>".number_format((double)$row[2],0,'.',',')."</td><td align=right onclick='ejecutar(".chr(34).$path2.chr(34).")'>".number_format((double)$row[3],0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</td></tr>";
							}
							else
							{
								$path1="/matrix/presupuestos/reportes/000001_rc19.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wcco1=".$wcco1."&wcco2=".$wcco2."&wrubro=".$row[0]."-".$row[1]."&empresa=".$empresa;
								echo"<tr onclick='ejecutar(".chr(34).$path1.chr(34).")'><td>".$row[1]."</td><td align=right>".number_format((double)$row[2],0,'.',',')."</td><td align=right>".number_format((double)$row[3],0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</td></tr>";
							}
						}
						else
						{
							if($call == "SIF")
							{
								$path1="/matrix/presupuestos/reportes/000001_rc130.php?wanoi=".$wanop."&wanof=".$wanop."&wmesi=".$wper1."&wmesf=".$wper2."&wcco1=".$wccof."&wcco2=NO&wserv=".$wserv."&wgru=".$wgru."&call=SIF&wfactor=1&empresa=".$empresa;
								$path2="/matrix/presupuestos/reportes/000001_rc149.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wcco=".$wccof."&wcod=".$row[0]."&empresa=".$empresa;
								echo"<tr><td>".$row[1]."</td><td align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'>".number_format((double)$row[2],0,'.',',')."</td><td align=right onclick='ejecutar(".chr(34).$path2.chr(34).")'>".number_format((double)$row[3],0,'.',',')."</td><td align=right>NO PRE</td><td align=right>".number_format((double)$wdif,0,'.',',')."</td></tr>";
							}
							else
							{
								$path1="/matrix/presupuestos/reportes/000001_rc19.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wcco1=".$wcco1."&wcco2=".$wcco2."&wrubro=".$row[0]."-".$row[1]."&empresa=".$empresa;
								echo"<tr onclick='ejecutar(".chr(34).$path1.chr(34).")'><td>".$row[1]."</td><td align=right>".number_format((double)$row[2],0,'.',',')."</td><td align=right>".number_format((double)$row[3],0,'.',',')."</td><td align=right>NO PRE</td><td align=right>".number_format((double)$wdif,0,'.',',')."</td></tr>";
							}
						}
                 }
                 else
                 	if($row[0] == "700")
                 	{
                 		$wtotalR[7]=$wtotalR[7]+$row[2];
						$wtotalP[7]=$wtotalP[7]+$row[3];
					}
                 	else if($row[0] == "750")
                 			{
	                 			if(!isset($wtotalR[8]))
	                 			{
	                 				$wtotalR[8]=0;
	                 				$wtotalP[8]=0;
                 				}
                 				$wtotalR[8]=$wtotalR[8]+$row[2];
								$wtotalP[8]=$wtotalP[8]+$row[3];
                 			}
                 			else if($row[0] == "760")
                 					{
	                 					if(!isset($wtotalR[12]))
	                 					{
	                 						$wtotalP[12]=0;
	                 						$wtotalR[12]=0;
                 						}
                 						$wtotalR[12]=$wtotalR[12]+$row[2];
                 						$wtotalP[12]=$wtotalP[12]+$row[3];
                 					}
                 					else if($row[0] == "900")
										{
											if(!isset($wtotalR[90]))
											{
												$wtotalP[90]=0;
												$wtotalR[90]=0;
											}
											$wtotalR[90]=$wtotalR[90]+$row[2];
											$wtotalP[90]=$wtotalP[90]+$row[3];
										}
    		}
    		switch ($ita)
			{
				case 1:
				if($wtotalP[1] != 0)
					$wpor=$wtotalR[1]/$wtotalP[1]*100;
				$wdif=dif($wtotalR[1],$wtotalP[1],1);
				if($wtotalP[1] != 0)
					echo"<tr><td><b>INGRESOS NETOS</b></td><td align=right><b>".number_format((double)$wtotalR[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>INGRESOS NETOS</b></td><td align=right><b>".number_format((double)$wtotalR[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[1],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				break;
				case 2:
				if($wtotalP[2] != 0)
					$wpor=$wtotalR[2]/$wtotalP[2]*100;
				$wdif=dif($wtotalR[2],$wtotalP[2],2);
				if($wtotalP[2] != 0)
					echo"<tr><td><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right><b>".number_format((double)$wtotalR[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right><b>".number_format((double)$wtotalR[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[2],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				if(!isset($wtotalR[1]))
					$wtotalR[1]=0;
				if(!isset($wtotalP[1]))
					$wtotalP[1]=0;
				$wtotalR[9] = $wtotalR[1] - $wtotalR[2] ;
           		$wtotalP[9] = $wtotalP[1] - $wtotalP[2] ;
				if($wtotalP[9] != 0)
					$wpor=$wtotalR[9]/$wtotalP[9]*100;
				$wdif=dif($wtotalR[9],$wtotalP[9],9);
				echo "<tr><td colspan=5 align=center>--------------------------------------------------</td></tr>";
				if($wtotalP[9] != 0)
					echo"<tr><td><b>UTILIDAD OPERACIONAL</b></td><td align=right><b>".number_format((double)$wtotalR[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>UTILIDAD OPERACIONAL</b></td><td align=right><b>".number_format((double)$wtotalR[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[9],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				break;
				case 3:
				break;
				case 4:
				if($wtotalP[4] != 0)
					$wpor=$wtotalR[4]/$wtotalP[4]*100;
				$wdif=dif($wtotalR[4],$wtotalP[4],4);
				if($wtotalP[4] != 0)
					echo"<tr><td><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotalR[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotalR[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[4],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				break;
				case 5:
				if($wtotalP[5] != 0)
					$wpor=$wtotalR[5]/$wtotalP[5]*100;
				$wdif=dif($wtotalR[5],$wtotalP[5],5);
				if($wtotalP[5] != 0)
					echo"<tr><td><b>TOTAL GASTOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotalR[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>TOTAL GASTOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotalR[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[5],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				break;
				case 6:
				if($wtotalP[6] != 0)
					$wpor=$wtotalR[6]/$wtotalP[6]*100;
				$wdif=dif($wtotalR[6],$wtotalP[6],6);
				if($wtotalP[6] != 0)
					echo"<tr><td><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right><b>".number_format((double)$wtotalR[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right><b>".number_format((double)$wtotalR[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[6],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				break;
			}
    		echo "<tr><td colspan=5 align=center>--------------------------------------------------</td></tr>";
    		for ($i=0;$i<13;$i++)
    			if(!isset($wtotalP[$i+1]))
    				$wtotalP[$i+1]=0;
    		for ($i=0;$i<13;$i++)
    			if(!isset($wtotalR[$i+1]))
    				$wtotalR[$i+1]=0;
    		if(isset($wtotalP[7]))
    		{
    			if($wtotalP[7] != 0)
					$wpor=$wtotalR[7]/$wtotalP[7]*100;
				$wdif=dif($wtotalR[7],$wtotalP[7],7);
				if($wtotalP[7] != 0)
					echo"<tr><td><b>CORRECCION MONETARIA</b></td><td align=right><b>".number_format((double)$wtotalR[7],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[7],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>CORRECCION MONETARIA</b></td><td align=right><b>".number_format((double)$wtotalR[7],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[7],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
    		}
             if(isset($wtotalP[4]) and isset($wtotalP[5]) and isset($wtotalP[6]) and isset($wtotalP[7]) and isset($wtotalP[9]))
    		{
	    		$wtotalP[10] = $wtotalP[9] + $wtotalP[4] - $wtotalP[5] - $wtotalP[6] + $wtotalP[7];
	    		$wtotalR[10] = $wtotalR[9] + $wtotalR[4] - $wtotalR[5] - $wtotalR[6] + $wtotalR[7];
	    		if($wtotalP[10] != 0)
					$wpor=$wtotalR[10]/$wtotalP[10]*100;
				$wdif=dif($wtotalR[10],$wtotalP[10],7);
				if($wtotalP[10] != 0)
					echo"<tr><td><b>UTILIDAD ANTES IMPUESTOS TOTAL</b></td><td align=right><b>".number_format((double)$wtotalR[10],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[10],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>UTILIDAD ANTES IMPUESTOS TOTAL</b></td><td align=right><b>".number_format((double)$wtotalR[10],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[10],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
             }
             if(isset($wtotalP[12]))
    		{
    			if($wtotalP[12] != 0)
                    $wpor=$wtotalR[12]/$wtotalP[12] * 100;
                else
                    $wpor=0;
                $wdif=dif($wtotalR[12],$wtotalP[12],7);
				if($wtotalP[12] != 0)
					echo"<tr><td><b>PROVISION IMPUESTO DE RENTA</b></td><td align=right><b>".number_format((double)$wtotalR[12],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[12],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>PROVISION IMPUESTO DE RENTA</b></td><td align=right><b>".number_format((double)$wtotalR[12],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[12],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";      
			}  
             else
             	$wtotalp[12]=0;
             if(isset($wtotalP[10]))
    		{
	    		$wtotalP[13]=$wtotalP[10]-$wtotalP[12];
	    		$wtotalR[13]=$wtotalR[10]-$wtotalR[12];
    			if($wtotalP[13] != 0)
                    $wpor=$wtotalR[13]/$wtotalP[13] * 100;
                else
                    $wpor=0;
                $wdif=dif($wtotalR[13],$wtotalP[13],7);
				if($wtotalP[13] != 0)
					echo"<tr><td><b>UTILIDAD NETA DESPUES DE IMPUESTOS</b></td><td align=right><b>".number_format((double)$wtotalR[13],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[13],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>UTILIDAD NETA DESPUES DE IMPUESTOS</b></td><td align=right><b>".number_format((double)$wtotalR[13],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[13],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";      
             }   
             if(!isset($wtotalP[8]))
             	$wtotalP[8]=0;
             if(isset($wtotalP[8]))
    		{
	    		if($wtotalP[8] != 0)
					$wpor=$wtotalR[8]/$wtotalP[8]*100;
				$wdif=dif($wtotalR[8],$wtotalP[8],7);
				if($wtotalP[8] != 0)
					echo"<tr><td><b>PARTICIPACION TERCEROS EN UNIDAD</b></td><td align=right><b>".number_format((double)$wtotalR[8],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[8],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>PARTICIPACION TERCEROS EN UNIDAD</b></td><td align=right><b>".number_format((double)$wtotalR[8],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[8],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
             }   
			 if(isset($wtotalP[8]) and isset($wtotalP[10]))
    		{
	    		$wtotalP[11] = $wtotalP[13] - $wtotalP[8];
	    		$wtotalR[11] = $wtotalR[13] - $wtotalR[8];
	    		if($wtotalP[11] != 0)
					$wpor=$wtotalR[11]/$wtotalP[11]*100;
				$wdif=dif($wtotalR[11],$wtotalP[11],7);
				if($wtotalP[11] != 0)
					echo"<tr><td><b>UTILIDAD DESPUES IMPUESTOS NETO PROMOTORA</b></td><td align=right><b>".number_format((double)$wtotalR[11],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[11],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td><b>UTILIDAD DESPUES IMPUESTOS NETO PROMOTORA</b></td><td align=right><b>".number_format((double)$wtotalR[11],0,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotalP[11],0,'.',',')."</b></td><td align=right><b>NO PRE</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
             }
             if(isset($wtotalP[90]))
    		{
	    		if($wtotalP[90] != 0)
					$wpor=$wtotalR[90]/$wtotalP[90]*100;
				$wdif=dif($wtotalR[90],$wtotalP[90],7);
				if($wtotalP[90] != 0)
					echo"<tr><td bgcolor=#999999><b>INGRESOS RECIBIDOS PARA TERCEROS</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wtotalR[90],0,'.',',')."</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wtotalP[90],0,'.',',')."</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wpor,2,'.',',')."</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
				else
					echo"<tr><td bgcolor=#999999><b>INGRESOS RECIBIDOS PARA TERCEROS</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wtotalR[90],0,'.',',')."</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wtotalP[90],0,'.',',')."</b></td><td align=right bgcolor=#999999><b>NO PRE</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wdif,0,'.',',')."</b></td></tr>";
             }
         }
         else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO NO!! ESTA CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
		}
	}
?>
</body>
</html>
