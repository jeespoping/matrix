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
		document.forms.rc07.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparacion de Resultados Entre A&ntilde;os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc07.php Ver. 2016-09-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc07' action='000001_rc07.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N") or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPARACION DE RESULTADOS ENTRE A&Ntilde;OS</td></tr>";
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
			$wanopa=$wanop-1;
			$query = "SELECT meccpr,mganom,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanop;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and meccco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and meccco = Ccocod";
			$query = $query."    and mecemp = ccoemp ";
			if($wgru != "Todos")
			{
				$query = $query."    and Ccouni = '".$wgru."' ";
			}
			$query = $query."    and mecmes between ".$wper1." and ".$wper2;
			$query = $query."    and meccpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by meccpr,mganom";
			$query = $query."   order by meccpr";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT meccpr,mganom,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanopa;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and meccco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and meccco = Ccocod";
			$query = $query."    and mecemp = ccoemp ";
			if($wgru != "Todos")
			{
				$query = $query."    and Ccouni = '".$wgru."' ";
			}
			$query = $query."    and mecmes between ".$wper1." and ".$wper2;
			$query = $query."    and meccpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by meccpr,mganom";
			$query = $query."   order by meccpr";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>COMPARACION DE RESULTADOS ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=8 align=center>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td><b>CODIGO</b></td><td><b>RUBRO</b></td><td><b>A&Ntilde;O : ".$wanopa."</b></td><td><b>%PART</b></td><td><b>A&Ntilde;O : ".$wanop."</b></td><td><b>%PART</b></td><td align=right><b>DIFERENCIA</b></td><td align=right><b>% VARIACION</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			$ingresos1 = 0;
			$ingresos2 = 0;
			if ($num1 ==  0)
			{
				$k1++;
				$row1[0]='999';
				$row1[1]="";
				$row1[2]=0;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2++;
				$row2[0]='999';
				$row2[1]="";
				$row2[2]=0;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				#echo $k1."-".$k2."-".$num1."-".$num2."<br>";
				if($row1[0] == $row2[0])
				{
					if ($row1[0] == "100")
					{
						$ingresos1 = $row2[2];
						$ingresos2 = $row1[2];
					}
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=$row1[2];
					$wdata[$num][4]=$row1[2]-$row2[2];
					if($row2[2] != 0)
						$wdata[$num][5]=(($row1[2]/$row2[2])-1)*100;
					else
						$wdata[$num][5]=0;
					$k1++;
					$k2++;
					if($k1 > $num1)
						$row1[0]="999";
					else
						$row1 = mysql_fetch_array($err1);
					if($k2 > $num2)
						$row2[0]="999";
					else
						$row2 = mysql_fetch_array($err2);
				}
				else if($row1[0] < $row2[0])
				{
					if ($row1[0] == "100")
					{
						$ingresos1 = 0;
						$ingresos2 = $row1[2];
					}
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row1[2];
					$wdata[$num][4]=$row1[2]-0;
					$wdata[$num][5]=0;
					$k1++;
					if($k1 > $num1)
						$row1[0]="999";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
					if ($row2[0] == "100")
					{
						$ingresos1 = $row2[2];
						$ingresos2 = 0;
					}
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=0-$row2[2];
					if($row2[2] != 0)
						$wdata[$num][5]=((0/$row2[2])-1)*100;
					else
						$wdata[$num][5]=0;
					$k2++;
					if($k2 > $num2)
						$row2[0]="999";
					else
						$row2 = mysql_fetch_array($err2);
				}
			}
			if($ingresos1 == 0 and  $ingresos2 ==0)
			{
				for ($i=0;$i<=$num;$i++)
				{
					if (substr($wdata[$i][0],0,1) == "2")
					{
						$ingresos2+=$wdata[$i][3];
						$ingresos1+=$wdata[$i][2];
					}
				}
			}
			$wtotal1=array();
			$wtotal2=array();
			$ita=0;
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if(!isset($wtotal1[$it]))
				{
					$wtotal1[$it]=0;
					$wtotal2[$it]=0;
					switch ($ita)
					{
						case 1:
						$ita=7;
						$wdif=$wtotal1[1]-$wtotal2[1];
						if($wtotal2[1] != 0)
							$wpor=(($wtotal1[1]/$wtotal2[1])-1)*100;
						else
							$wpor=0;
						if($ingresos1 != 0)
							$i1=$wtotal2[1]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal1[1]/$ingresos2*100;
						else
							$i2=0;
						echo"<tr><td colspan=2><b>INGRESOS NETOS</b></td><td align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
						case 2:
						$ita=7;
						$wdif=$wtotal1[2]-$wtotal2[2];
						if($wtotal2[2] != 0)
							$wpor=(($wtotal1[2]/$wtotal2[2])-1)*100;
						else
							$wpor=0;
						if($ingresos1 != 0)
							$i1=$wtotal2[2]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal1[2]/$ingresos2*100;
						else
							$i2=0;
						echo"<tr><td colspan=2><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						if(isset($wtotal1[1] ))
							$wtotal1[9] = $wtotal1[1] - $wtotal1[2];
						else
							$wtotal1[9] = 0 - $wtotal1[2];
						if(isset($wtotal2[1] ))
                    		$wtotal2[9] = $wtotal2[1] - $wtotal2[2];
                    	else
                    		$wtotal2[9] = 0 - $wtotal2[2];
                    	$wdif=$wtotal1[9]-$wtotal2[9];
						if($wtotal2[9] != 0)
							$wpor=(($wtotal1[9]/$wtotal2[9])-1)*100;
						else
							$wpor=0;
						if($ingresos1 != 0)
							$i1=$wtotal2[9]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal1[9]/$ingresos2*100;
						else
							$i2=0;
						echo "<tr><td colspan=8 align=center>--------------------------------------------------</td></tr>";
						echo"<tr><td colspan=2><b>UTILIDAD OPERACIONAL</b></td><td align=right><b>".number_format((double)$wtotal2[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
						case 3:
						break;
						case 4:
						$ita=7;
						$wdif=$wtotal1[4]-$wtotal2[4];
						if($wtotal2[4] != 0)
							$wpor=(($wtotal1[4]/$wtotal2[4])-1)*100;
						else
							$wpor=0;
						if($ingresos1 != 0)
							$i1=$wtotal2[4]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal1[4]/$ingresos2*100;
						else
							$i2=0;
						echo"<tr><td colspan=2><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotal2[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
						case 5:
						$ita=7;
						$wdif=$wtotal1[5]-$wtotal2[5];
						if($wtotal2[5] != 0)
							$wpor=(($wtotal1[5]/$wtotal2[5])-1)*100;
						else
							$wpor=0;
						if($ingresos1 != 0)
							$i1=$wtotal2[5]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal1[5]/$ingresos2*100;
						else
							$i2=0;
						echo"<tr><td colspan=2><b>TOTAL GASTOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotal2[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
						case 6:
						$ita=7;
						$wdif=$wtotal1[6]-$wtotal2[6];
						if($wtotal2[6] != 0)
							$wpor=(($wtotal1[6]/$wtotal2[6])-1)*100;
						else
							$wpor=0;
						if($ingresos1 != 0)
							$i1=$wtotal2[6]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal1[6]/$ingresos2*100;
						else
							$i2=0;
						echo"<tr><td colspan=2><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right><b>".number_format((double)$wtotal2[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						break;
					}
					switch ($it)
					{
						case 1:
						echo "<tr><td colspan=8><b>INGRESOS</B></td></tr>";
						$ita=1;
						break;
						case 2:
						echo "<tr><td colspan=8 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=8><b>COSTOS DEL SERVICIO</B></td></tr>";
						$ita=2;
						break;
						case 3:
						echo "<tr><td colspan=8 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=8><b>GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
						$ita=3;
						break;
						case 4:
						echo "<tr><td colspan=8 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=8><b>INGRESOS NO OPERACIONALES</B></td></tr>";
						$ita=4;
						break;
						case 5:
						echo "<tr><td colspan=8 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=8><b>GASTOS NO OPERACIONALES</B></td></tr>";
						$ita=5;
						break;
						case 6:
						echo "<tr><td colspan=8 align=center>--------------------------------------------------</td></tr>";
						echo "<tr><td colspan=8><b>GASTOS FINANCIEROS</B></td></tr>";
						$ita=6;
						break;
					}
				}
				if($it < 7)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0)
						$wpor=(($wdata[$i][3]/$wdata[$i][2])-1)*100;
					else
						$wpor=0;
					if($ingresos1 != 0)
						$i1=$wdata[$i][2]/$ingresos1*100;
					else
						$i1=0;
					if($ingresos2 != 0)
						$i2=$wdata[$i][3]/$ingresos2*100;
					else
						$i2=0;
					if($wres == "D")
					{
						if($call != "SIF")
						{
							$path1="/matrix/presupuestos/reportes/000001_rc130.php?wanoi=".$wanop."&wanof=".$wanop."&wmesi=".$wper1."&wmesf=".$wper2."&wcco1=".$wcco1."&wcco2=".$wcco2."&wserv=".$wserv."&wgru=".$wgru."&call=SIF&wfactor=1&empresa=".$empresa."&wemp=".$wempt;
							$path2="/matrix/presupuestos/reportes/000001_rc130.php?wanoi=".$wanopa."&wanof=".$wanopa."&wmesi=".$wper1."&wmesf=".$wper2."&wcco1=".$wcco1."&wcco2=".$wcco2."&wserv=".$wserv."&wgru=".$wgru."&call=SIF&wfactor=1&empresa=".$empresa."&wemp=".$wempt;
							echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right onclick='ejecutar(".chr(34).$path2.chr(34).")'>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
						}
						else
							echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
					}
                 }
                 else
                 	if($wdata[$i][0] == "700")
                 	{
                 		$wtotal2[7]=$wtotal2[7]+$wdata[$i][2];
                 		$wtotal1[7]=$wtotal1[7]+$wdata[$i][3];
                 	}
                 	else if($wdata[$i][0] == "750")
                 			{
	                 			if(!isset($wtotal1[8]))
	                 				$wtotal1[8]=0;
	                 			if(!isset($wtotal2[8]))
	                 				$wtotal2[8]=0;
                 				$wtotal2[8]=$wtotal2[8]+$wdata[$i][2];
                 				$wtotal1[8]=$wtotal1[8]+$wdata[$i][3];
                 			}
                 			else if($wdata[$i][0] == "760")
                 					{
	                 					if(!isset($wtotal1[12]))
	                 						$wtotal1[12]=0;
	                 					if(!isset($wtotal2[12]))
	                 						$wtotal2[12]=0;
                 						$wtotal2[12]=$wtotal2[12]+$wdata[$i][2];
                 						$wtotal1[12]=$wtotal1[12]+$wdata[$i][3];
                 					}
                 					else if($wdata[$i][0] == "900")
											{
												if(!isset($wtotal1[90]))
													$wtotal1[90]=0;
												if(!isset($wtotal2[90]))
													$wtotal2[90]=0;
												$wtotal2[90]=$wtotal2[90]+$wdata[$i][2];
												$wtotal1[90]=$wtotal1[90]+$wdata[$i][3];
											}
    		}
    		switch ($ita)
			{
				case 1:
				$wdif=$wtotal1[1]-$wtotal2[1];
				if($wtotal2[1] != 0)
					$wpor=(($wtotal1[1]/$wtotal2[1])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[1]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[1]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2><b>INGRESOS NETOS</b></td><td align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
				case 2:
				$wdif=$wtotal1[2]-$wtotal2[2];
				if($wtotal2[2] != 0)
					$wpor=(($wtotal1[2]/$wtotal2[2])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[2]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[2]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				$wtotal1[9] = $wtotal1[1] - $wtotal1[2];
            	$wtotal2[9] = $wtotal2[1] - $wtotal2[2];
            	$wdif=$wtotal1[9]-$wtotal2[9];
				if($wtotal2[9] != 0)
					$wpor=(($wtotal1[9]/$wtotal2[9])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[9]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[9]/$ingresos2*100;
				else
					$i2=0;
				echo "<tr><td colspan=6 align=center>--------------------------------------------------</td></tr>";
				echo"<tr><td colspan=2><b>UTILIDAD OPERACIONAL</b></td><td align=right><b>".number_format((double)$wtotal2[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[9],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
				case 3:
				break;
				case 4:
				$wdif=$wtotal1[4]-$wtotal2[4];
				if($wtotal2[4] != 0)
					$wpor=(($wtotal1[4]/$wtotal2[4])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[4]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[4]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotal2[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[4],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
				case 5:
				$wdif=$wtotal1[5]-$wtotal2[5];
				if($wtotal2[5] != 0)
					$wpor=(($wtotal1[5]/$wtotal2[5])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[5]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[5]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2><b>TOTAL GASTOS NO OPERACIONALES</b></td><td align=right><b>".number_format((double)$wtotal2[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[5],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
				case 6:
				$wdif=$wtotal1[6]-$wtotal2[6];
				if($wtotal2[6] != 0)
					$wpor=(($wtotal1[6]/$wtotal2[6])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[6]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[6]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right><b>".number_format((double)$wtotal2[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[6],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				break;
			}
    		for ($i=0;$i<13;$i++)
    			if(!isset($wtotal1[$i+1]))
    				$wtotal1[$i+1]=0;
    		for ($i=0;$i<13;$i++)
    			if(!isset($wtotal2[$i+1]))
    				$wtotal2[$i+1]=0;
    		echo "<tr><td colspan=8 align=center>--------------------------------------------------</td></tr>";
    		if(isset($wtotal1[7]) and isset($wtotal2[7]))
    		{
	    		$wdif=$wtotal1[7]-$wtotal2[7];
				if($wtotal2[7] != 0)
					$wpor=(($wtotal1[7]/$wtotal2[7])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[7]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[7]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2><b>CORRECCION MONETARIA</b></td><td align=right><b>".number_format((double)$wtotal2[7],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[7],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }
             if((isset($wtotal1[4]) and isset($wtotal1[5]) and isset($wtotal1[6]) and isset($wtotal1[7]) and isset($wtotal1[9])) and (isset($wtotal2[4]) and isset($wtotal2[5]) and isset($wtotal2[6]) and isset($wtotal2[7]) and isset($wtotal2[9])) )
    		{
	    		$wtotal1[10] = $wtotal1[9] + $wtotal1[4] - $wtotal1[5] - $wtotal1[6] + $wtotal1[7];
	    		$wtotal2[10] = $wtotal2[9] + $wtotal2[4] - $wtotal2[5] - $wtotal2[6] + $wtotal2[7];
	    		$wdif=$wtotal1[10]-$wtotal2[10];
				if($wtotal2[10] != 0)
					$wpor=(($wtotal1[10]/$wtotal2[10])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[10]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[10]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2><b>UTILIDAD ANTES IMPUESTOS TOTAL</b></td><td align=right><b>".number_format((double)$wtotal2[10],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[10],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }
             if(isset($wtotal1[12]) or isset($wtotal2[12]))
    		{
	    		$wdif=$wtotal1[12]-$wtotal2[12];
    			if($wtotal2[12] != 0)
                    $wpor=(($wtotal1[12]/$wtotal2[12])-1) * 100;
                else
                    $wpor=0;
                if($ingresos1 != 0)
               		 $i1=$wtotal2[12]/$ingresos1*100;
                else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[12]/$ingresos2*100;
				else
					$i2=0;
                echo"<tr><td colspan=2><b>PROVISION IMPUESTO DE RENTA</b></td><td align=right><b>".number_format((double)$wtotal2[12],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[12],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			}  
             else
             {
             	$wtotal1[12]=0;
             	$wtotal2[12]=0;
         	}
             if(isset($wtotal1[10]) and isset($wtotal2[10]))
    		{
	    		$wtotal1[13]=$wtotal1[10]-$wtotal1[12];
	    		$wtotal2[13]=$wtotal2[10]-$wtotal2[12];
	    		$wdif=$wtotal1[13]-$wtotal2[13];
    			if($wtotal2[13] != 0)
                    $wpor=(($wtotal1[13]/$wtotal2[13])-1) * 100;
                else
                    $wpor=0;
                if($ingresos1 != 0)
                	$i1=$wtotal2[13]/$ingresos1*100;
                else
					$i1=0; 
				if($ingresos2 != 0)
					$i2=$wtotal1[13]/$ingresos2*100;
				else
					$i2=0;
                echo"<tr><td colspan=2><b>UTILIDAD NETA DESPUES DE IMPUESTOS</b></td><td align=right><b>".number_format((double)$wtotal2[13],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[13],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }   
             if(!isset($wtotal1[8]))
             	$wtotal1[8]=0;
              if(!isset($wtotal2[8]))
             	$wtotal2[8]=0;
             if(isset($wtotal2[8]))
    		{
	    		if(isset($wtotal1[8]) and isset($wtotal2[8]))
	    			if($wtotal2[8] != 0)
						$wpor=(($wtotal1[8]/$wtotal2[8])-1)*100;
					else
						 $wpor=0;
				$wdif=$wtotal1[8]-$wtotal2[8];
				if($ingresos1 != 0)
					$i1=$wtotal2[8]/$ingresos1*100;
				else
					$i1=0; 
				if($ingresos2 != 0)
					$i2=$wtotal1[8]/$ingresos2*100;
				else
					$i2=0;
                echo"<tr><td colspan=2><b>PARTICIPACION TERCEROS EN UNIDAD</b></td><td align=right><b>".number_format((double)$wtotal2[8],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[8],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }   

			 if(isset($wtotal1[8]) and isset($wtotal1[10]) and isset($wtotal2[8]) and isset($wtotal2[10]))
    		{
	    		$wtotal1[11] = $wtotal1[13] - $wtotal1[8];
	    		$wtotal2[11] = $wtotal2[13] - $wtotal2[8];
	    		$wdif=$wtotal1[11]-$wtotal2[11];
				if($wtotal2[11] != 0)
					$wpor=(($wtotal1[11]/$wtotal2[11])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[11]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[11]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2><b>UTILIDAD DESPUES IMPUESTOS NETO PROMOTORA</b></td><td align=right><b>".number_format((double)$wtotal2[11],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[11],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
             }
            if(isset($wtotal1[90]) and isset($wtotal2[90]))
    		{
	    		$wdif=$wtotal1[90]-$wtotal2[90];
				if($wtotal2[90] != 0)
					$wpor=(($wtotal1[90]/$wtotal2[90])-1)*100;
				else
					$wpor=0;
				if($ingresos1 != 0)
					$i1=$wtotal2[90]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal1[90]/$ingresos2*100;
				else
					$i2=0;
				echo"<tr><td colspan=2 bgcolor=#DDDDDD><b>INGRESOS RECIBIDOS PARA TERCEROS</b></td><td align=right bgcolor=#DDDDDD><b>".number_format((double)$wtotal2[90],0,'.',',')."</b></td><td align=right bgcolor=#DDDDDD><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right bgcolor=#DDDDDD><b>".number_format((double)$wtotal1[90],0,'.',',')."</b></td><td align=right bgcolor=#DDDDDD><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right bgcolor=#DDDDDD><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right bgcolor=#DDDDDD><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
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
