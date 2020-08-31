<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparaci&oacute;n Entre A&ntilde;os de Costos de Apoyo Recibidos (T73)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc72.php Ver. 2018-02-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc72.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "P") or !isset($wper1)  or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof))  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPARACI&oacute;N ENTRE A&Ntilde;OS DE COSOTOS DE APOYO RECIBIDOS (T73)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and strtoupper ($call) == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>C.C Destino Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>C.C Destino Final de Proceso</td>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Informacion (R - Real / P - Presupuestada)</td>";
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
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and emp = '".$wemp."'";
			$query = $query."    and mes =   ".$wper2;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or (isset($call) and $call=="SIC") or $key=="costosyp")
			{
			$wmeses = $wper2 - $wper1 + 1;
			$wanopa=$wanop-1;
			$query = "SELECT  Procco,cconom, sum(Promon)  from ".$empresa."_000073,".$empresa."_000005 ";
			$query = $query." where Proano = ".$wanopa;
			$query = $query."   and Proemp = '".$wemp."'";
			$query = $query."   and Promes between ".$wper1." and ".$wper2;
			$query = $query."   and Proccd between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and Protip = '".$wres."'";
			$query = $query."   and Proemp = ccoemp ";
			$query = $query."   and Procco = ccocod ";
			$query = $query."  group by Procco,cconom";
			$query = $query."  order by Procco,cconom";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT  Procco,cconom, sum(Promon)  from ".$empresa."_000073,".$empresa."_000005 ";
			$query = $query." where Proano = ".$wanop;
			$query = $query."   and Proemp = '".$wemp."'";
			$query = $query."   and Promes between ".$wper1." and ".$wper2;
			$query = $query."   and Proccd between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and Protip = '".$wres."'";
			$query = $query."   and Proemp = ccoemp ";
			$query = $query."   and Procco = ccocod ";
			$query = $query."  group by Procco,cconom";
			$query = $query."  order by Procco,cconom";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=9 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=9 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=9 align=center>COMPARACI&oacute;N ENTRE A&Ntilde;OS DE COSOTOS DE APOYO RECIBIDOS (T73)</td></tr>";
			echo "<tr><td colspan=9 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=9 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=9 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>C.C. ORIGEN</b></td><td><b>DESCRIPCION</b></td><td align=right><b>A&Ntilde;O : ".$wanopa."</b></td><td align=right><b>A&Ntilde;O : ".$wanop."</b></td><td align=right><b>% VARIACION</b></td><td align=right><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='ZZZZ';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='ZZZZ';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($key1 == $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[1];
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=$row2[2];
					if($row1[2] != 0)
						$wdata[$num][3]=($row2[2]/$row1[2] - 1)* 100;
					else
						$wdata[$num][3]=0;
					$wdata[$num][4]=$row2[2] - $row1[2];
					$wdata[$num][5]=$row1[0];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="ZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
					if($k2 > $num2)
						$key2="ZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[1];
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=0;
					if($row1[2] != 0)
						$wdata[$num][3]=(0/$row1[2] - 1) * 100;
					else
						$wdata[$num][3]=0;
					$wdata[$num][4]=0 - $row1[2];
					$wdata[$num][5]=$row1[0];
					$k1++;
					if($k1 > $num1)
						$key1="ZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[1];
					$wdata[$num][1]=0;
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=$row2[2] - 0;
					$wdata[$num][5]=$row2[0];
					$k2++;
					if($k2 > $num2)
						$key2="ZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
			}
			$wtotr=0;
			$wtotp=0;
			for ($i=0;$i<=$num;$i++)
			{
				$wtotr=$wtotr+$wdata[$i][1];
				$wtotp=$wtotp+$wdata[$i][2];
				
				echo"<tr><td>".$wdata[$i][5]."</td><td>".$wdata[$i][0]."</td><td align=right>".number_format((double)$wdata[$i][1],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][2],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][4],2,'.',',')."</td></tr>";
    		}
    		if($wtotp != 0)
				$weje=$wtotr/$wtotp * 100;
			else
				$weje=0;
			$wdif= $wtotp - $wtotr;
    		echo"<tr><td colspan=2><B>TOTALES</B></td><td align=right><B>".number_format((double)$wtotr,2,'.',',')."</B></td><td align=right><B>".number_format((double)$wtotp,2,'.',',')."</B></td><td align=right><B>".number_format((double)$weje,2,'.',',')." %</B></td><td align=right><B>".number_format((double)$wdif,2,'.',',')."</B></td></tr>";
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
