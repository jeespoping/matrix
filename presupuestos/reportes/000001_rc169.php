<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparacion de Resultados Entre A&ntilde;os (Presupuestal vs Real) NIIF</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc169.php Ver. 2018-03-01</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc169.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or  ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N") or !isset($wtip) or (strtoupper ($wtip) != "C" and strtoupper ($wtip) != "A") or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 )
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPARACION DE RESULTADOS ENTRE A&Ntilde;OS (PRESUPUESTAL VS REAL) NIIF</td></tr>";
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
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Administrativo o Contable ? (A/C)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centros de Servicio ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
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
			$wres=strtoupper ($wres);
			$wserv=strtoupper ($wserv);
			$wtip=strtoupper ($wtip);
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$query = "SELECT cierre_pptal from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or $key=="costosyp" or (isset($call) and $call == "SIC"))
			{
			$wanopa=$wanop-1;
			if($wtip == "C")
			{
				$query = "SELECT rescpr,mganom,sum(resmon) as wmonto from ".$empresa."_000043,".$empresa."_000028 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resemp = '".$wemp."' ";
				$query = $query."    and rescco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and resper between ".$wper1." and ".$wper2;
				$query = $query."    and rescpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by rescpr,mganom";
				$query = $query."   order by rescpr";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$query = "SELECT meccpr,mganom,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000028 ";
				$query = $query."  where mecano = ".$wanopa;
				$query = $query."    and mecemp = '".$wemp."' ";
				$query = $query."    and meccco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and mecmes between ".$wper1." and ".$wper2;
				$query = $query."    and meccpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by meccpr,mganom";
				$query = $query."   order by meccpr";
				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);
			}
			else
			{
				$query = "SELECT mgacoa,mganom,sum(resmon) as wmonto from ".$empresa."_000043,".$empresa."_000028 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resemp = '".$wemp."' ";
				$query = $query."    and rescco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and resper between ".$wper1." and ".$wper2;
				$query = $query."    and rescpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by mgacoa,mganom";
				$query = $query."   order by mgacoa";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$query = "SELECT mgacoa,mganom,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000028 ";
				$query = $query."  where mecano = ".$wanopa;
				$query = $query."    and mecemp = '".$wemp."' ";
				$query = $query."    and meccco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and mecmes between ".$wper1." and ".$wper2;
				$query = $query."    and meccpr = mgacod ";
				if($wserv == "N") 
					$query = $query."   and mgatip = '0' ";
				$query = $query."   group by mgacoa,mganom";
				$query = $query."   order by mgacoa";
				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);
			}
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>ESTADO DE RESULTADOS DEL PERIODO Y OTRO RESULTADO INTEGRAL SEPARADO <b>NIIF</b></td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=8 align=center>OPERACIONES CONTINUADAS</td></tr>";
			echo "<tr><td><b>CODIGO</b></td><td><b>RUBRO</b></td><td><b>A&Ntilde;O REAL : ".$wanopa."</b></td><td><b>%PART</b></td><td><b>A&Ntilde;O PPTAL : ".$wanop."</b></td><td><b>%PART</b></td><td align=right><b>DIFERENCIA</b></td><td align=right><b>% VARIACION</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1=1;
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
				$k2=1;
				$row2[0]='999';
				$row2[1]="";
				$row2[2]=0;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$k2++;
			}
			$ingresos1 = 0;
			$ingresos2 = 0;
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				#echo $k1."-".$k2."-".$num1."-".$num2." ".$row1[0]." ".$row2[0]."<br>";
				if($row1[0] == $row2[0])
				{
					if ($row1[0] >= "100" and $row1[0] <= "129")
					{
						$ingresos1 += $row2[2];
						$ingresos2 += $row1[2];
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
					if ($row1[0] >= "100" and $row1[0] <= "129")
					{
						$ingresos1 += 0;
						$ingresos2 += $row1[2];
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
					if ($row1[0] >= "100" and $row1[0] <= "129")
					{
						$ingresos1 += $row2[2];
						$ingresos2 += 0;
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

			$wtotal1=array();
			$wtotal2=array();
			echo "<tr><td colspan=8><b>INGRESOS DE OPERACIONES ORDINARIAS</B></td></tr>";
			$it = 1;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "100" and $wdata[$i][0] <= "129")
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					if((integer)$wdata[$i][0] == 100)
					{
						$wtotal2[100]=$wtotal2[100]+$wdata[$i][2];
						$wtotal1[100]=$wtotal1[100]+$wdata[$i][3];
					}
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			$wdif=$wtotal1[$it]-$wtotal2[$it];
			if($wtotal2[$it] != 0)
				$wpor=(($wtotal1[$it]/$wtotal2[$it])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[$it]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[$it]/$ingresos2*100;
			else
				$i2=0;
			echo"<tr><td colspan=2><b>TOTAL INGRESOS DE OPERACIONES ORDINARIAS</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			if($wtip == "C")
				echo "<tr><td colspan=8><b>COSTOS DE OPERACION</B></td></tr>";
			else
				echo "<tr><td colspan=8><b>COSTOS  Y GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
			$it = 2;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 2)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			$wdif=$wtotal1[$it]-$wtotal2[$it];
			if($wtotal2[$it] != 0)
				$wpor=(($wtotal1[$it]/$wtotal2[$it])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[$it]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[$it]/$ingresos2*100;
			else
				$i2=0;
			if($wtip == "C")
				echo"<tr><td colspan=2><b>TOTAL COSTOS DE OPERACION</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			else
				echo"<tr><td colspan=2><b>TOTAL COSTOS Y GASTOS DE ADMINISTRACION Y VENTAS</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			if($wtip == "C")
				echo "<tr><td colspan=8><b>GASTOS DE ADMINISTRACION</B></td></tr>";
			$it = 3;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 3)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			$wdif=$wtotal1[$it]-$wtotal2[$it];
			if($wtotal2[$it] != 0)
				$wpor=(($wtotal1[$it]/$wtotal2[$it])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[$it]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[$it]/$ingresos2*100;
			else
				$i2=0;
			if($wtip == "C")
			{
				echo"<tr><td colspan=2><b>TOTAL GASTOS DE ADMINISTRACION</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				echo "<tr><td colspan=8><b>GASTOS DE VENTAS</B></td></tr>";
			}
			$it = 8;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 8)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			$wdif=$wtotal1[$it]-$wtotal2[$it];
			if($wtotal2[$it] != 0)
				$wpor=(($wtotal1[$it]/$wtotal2[$it])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[$it]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[$it]/$ingresos2*100;
			else
				$i2=0;
			if($wtip == "C")
				echo"<tr><td colspan=2><b>TOTAL GASTOS DE VENTAS</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			
			echo "<tr><td colspan=8><b>OTROS INGRESOS</B></td></tr>";
			$it = 99;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "130" and $wdata[$i][0] <= "199")
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					if((integer)$wdata[$i][0] == 100)
					{
						$wtotal2[100]=$wtotal2[100]+$wdata[$i][2];
						$wtotal1[100]=$wtotal1[100]+$wdata[$i][3];
					}
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			$wdif=$wtotal1[$it]-$wtotal2[$it];
			if($wtotal2[$it] != 0)
				$wpor=(($wtotal1[$it]/$wtotal2[$it])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[$it]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[$it]/$ingresos2*100;
			else
				$i2=0;
			echo"<tr><td colspan=2><b>TOTAL OTROS INGRESOS</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			
			echo "<tr><td colspan=8><b>OTROS GASTOS DE OPERACION</B></td></tr>";
			$it = 5;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 5)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			$wdif=$wtotal1[$it]-$wtotal2[$it];
			if($wtotal2[$it] != 0)
				$wpor=(($wtotal1[$it]/$wtotal2[$it])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[$it]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[$it]/$ingresos2*100;
			else
				$i2=0;
			echo"<tr><td colspan=2><b>TOTAL OTROS GASTOS DE OPERACION</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			//*** TOTAL 10 ***
			$wtotal1[10] = $wtotal1[1] - $wtotal1[2] - $wtotal1[3] - $wtotal1[8] - $wtotal1[5] + $wtotal1[99];
			$wtotal2[10] = $wtotal2[1] - $wtotal2[2] - $wtotal2[3] - $wtotal2[8] - $wtotal2[5] + $wtotal2[99];
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
			echo"<tr><td colspan=2><b>RESULTADOS DE ACTIVIDADES DE LA OPERACION</b></td><td align=right><b>".number_format((double)$wtotal2[10],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[10],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			echo "<tr><td colspan=8><b>INGRESO FINANCIERO</B></td></tr>";
			$it = 4;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 4)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			$wdif=$wtotal1[$it]-$wtotal2[$it];
			if($wtotal2[$it] != 0)
				$wpor=(($wtotal1[$it]/$wtotal2[$it])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[$it]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[$it]/$ingresos2*100;
			else
				$i2=0;
			echo"<tr><td colspan=2><b>TOTAL INGRESO FINANCIERO</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			echo "<tr><td colspan=8><b>GASTO FINANCIERO</B></td></tr>";
			$it = 6;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)substr($wdata[$i][0],0,1) == 6)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			$wdif=$wtotal1[$it]-$wtotal2[$it];
			if($wtotal2[$it] != 0)
				$wpor=(($wtotal1[$it]/$wtotal2[$it])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[$it]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[$it]/$ingresos2*100;
			else
				$i2=0;
			echo"<tr><td colspan=2><b>TOTAL GASTO FINANCIERO</b></td><td align=right><b>".number_format((double)$wtotal2[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[$it],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			//*** TOTAL 11 ***
			$wtotal1[11] = $wtotal1[4] - $wtotal1[6];
			$wtotal2[11] = $wtotal2[4] - $wtotal2[6];
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
			echo"<tr><td colspan=2><b>COSTO FINANCIERO NETO</b></td><td align=right><b>".number_format((double)$wtotal2[11],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[11],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			//*** TOTAL 12 ***
			$wtotal1[12] = $wtotal1[10] + $wtotal1[11];
			$wtotal2[12] = $wtotal2[10] + $wtotal2[11];
			$wdif=$wtotal1[12]-$wtotal2[12];
			if($wtotal2[12] != 0)
				$wpor=(($wtotal1[12]/$wtotal2[12])-1)*100;
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
			echo"<tr><td colspan=2><b>GANANCIAS ANTES DE IMPUESTOS</b></td><td align=right><b>".number_format((double)$wtotal2[12],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[12],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			//*** LINEA 760 ***
			$it = 760;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)$wdata[$i][0] == 760)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			//*** LINEA 770 ***
			$it = 770;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)$wdata[$i][0] == 770)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			//*** TOTAL 13 ***
			$wtotal1[13] = $wtotal1[12] - $wtotal1[760] - $wtotal1[770];
			$wtotal2[13] = $wtotal2[12] - $wtotal2[760] - $wtotal2[770];
			$wdif=$wtotal1[13]-$wtotal2[13];
			if($wtotal2[13] != 0)
				$wpor=(($wtotal1[13]/$wtotal2[13])-1)*100;
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
			echo"<tr><td colspan=2><b>RESULTADOS PROCEDENTES DE OPERACIONES CONTINUADAS</b></td><td align=right><b>".number_format((double)$wtotal2[13],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[13],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			//*** LINEA 900 ***
			$it = 900;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)$wdata[$i][0] == 900)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			//*** TOTAL 14 ***
			$wtotal1[14] = $wtotal1[100] + $wtotal1[900];
			$wtotal2[14] = $wtotal2[100] + $wtotal2[900];
			$wdif=$wtotal1[14]-$wtotal2[14];
			if($wtotal2[14] != 0)
				$wpor=(($wtotal1[14]/$wtotal2[14])-1)*100;
			else
				$wpor=0;
			if($ingresos1 != 0)
				$i1=$wtotal2[14]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal1[14]/$ingresos2*100;
			else
				$i2=0;
			echo"<tr><td colspan=2><b>FACTURACION TOTAL POR PRESTACION DE SERVICIOS</b></td><td align=right><b>".number_format((double)$wtotal2[14],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i1,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wtotal1[14],0,'.',',')."</b></td><td align=right><b>".number_format((double)$i2,2,'.',',')."</b></td><td align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			//*** LINEA 750 ***
			$it = 750;
			$wtotal1[$it]=0;
			$wtotal2[$it]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if((integer)$wdata[$i][0] == 750)
				{
					$wtotal2[$it]=$wtotal2[$it]+$wdata[$i][2];
					$wtotal1[$it]=$wtotal1[$it]+$wdata[$i][3];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($wdata[$i][2] != 0 and $wdata[$i][2] > 0.9)
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
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</b></td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
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
