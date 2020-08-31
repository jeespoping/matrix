<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ejecucion de Un Rubro x Cco</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc19.php Ver. 2017-10-04</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc19.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanop) or !isset($wrubro) or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>EJECUCION DE UN RUBRO X CENTRO DE COSTOS</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
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
			echo "<td bgcolor=#cccccc align=center>Rubro Presupuestal</td>";
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
				echo "<select name='wrubro'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$wtotre=0;
				$wtotpr=0;
				echo "<center><table border=1>";
				echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
				echo "<tr><td align=center  colspan=6><b>APLICACION DE PRESUPUESTOS</b></td></tr>";
				echo "<tr><td align=center  colspan=6><b>EJECUCION DE UN RUBRO X CENTRO DE COSTOS</b></td></tr>";
				echo "<tr><td colspan=6 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
				echo "<tr><td colspan=6 align=center>RUBRO PRESUPUESTAL : ".$wrubro."</td></tr>";
				echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td><b>CODIGO C.C.</b></td><td><b>NOMBRE C.C.</b></td><td><b>VALOR REAL.</b></td><td><b>VALOR PPTAL.</b></td><td><b>DIFERENCIA.</b></td><td><b>% EJECUCION.</b></td></tr>";
				$ini = strpos($wrubro,"-");
				$query = "SELECT rvpcco,cconom,sum(rvpvre),sum(rvpvpr)  from ".$empresa."_000044,".$empresa."_000005 ";
				$query = $query." where rvpcpr = '".substr($wrubro,0,$ini)."'";
				$query = $query."   and rvpano = ".$wanop;
				$query = $query."   and rvpemp = '".$wemp."'";
				$query = $query."   and rvpper between ".$wper1." and ".$wper2;
				if(isset($wcco1) and isset($wcco2))
					$query = $query."   and rvpcco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."   and rvpcco = ccocod";
				$query = $query."   and rvpemp = ccoemp ";
				$query = $query."   group by rvpcco,cconom";
				$query = $query."   order by rvpcco,cconom";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1>0)
				{
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtotre=$wtotre+$row1[2];
						$wtotpr=$wtotpr+$row1[3];
						$wdif =$row1[2]-$row1[3];
						if($row1[3] != 0)
							$wpor=($row1[2]/$row1[3])*100;
						else
							$wpor=0;
						 echo "<tr>";
       					 echo "<td>".$row1[0]."</td>";
       					 echo "<td>".$row1[1]."</td>";
       					 echo "<td align=right>".number_format($row1[2],0,'.',',')."</td>";
       					 echo "<td align=right>".number_format($row1[3],0,'.',',')."</td>";
       					 echo "<td align=right>".number_format($wdif,0,'.',',')."</td>";
       					 echo "<td align=right>".number_format($wpor,2,'.',',')."%</td></tr>";
					}
					$wdif=$wtotre-$wtotpr;
					$wpor=($wtotre/$wtotpr)*100;
					echo "<tr>";
       				echo "<td>&nbsp</td>";
       				echo "<td><B>TOTALES</B></td>";
       				echo "<td align=right><B>".number_format($wtotre,0,'.',',')."</B></td>";
       				echo "<td align=right><B>".number_format($wtotpr,0,'.',',')."</B></td>";
       				echo "<td align=right><B>".number_format($wdif,0,'.',',')."</B></td>";
       				echo "<td align=right><B>".number_format($wpor,2,'.',',')."%</B></td></tr>";
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
