<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Variacion de Un Rubro x Cco Entre A&ntilde;os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc41.php Ver. 2016-01-29</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc41.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione"  or !isset($wanop) or !isset($wrubro) or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>VARIACION DE UN RUBRO X CCO ENTRE A&Ntilde;OS</td></tr>";
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
			$query = "SELECT mgacod,mganom from ".$empresa."_000028 order by mganom";
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
			$wanopa=$wanop - 1;
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>APLICACION DE PRESUPUESTOS</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>VARIACION DE UN RUBRO X CCO ENTRE A&Ntilde;OS</b></td></tr>";
			echo "<tr><td colspan=6 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=6 align=center>RUBRO PRESUPUESTAL : ".$wrubro."</td></tr>";
			echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td><b>CODIGO C.C.</b></td><td><b>NOMBRE C.C.</b></td><td><b>VALOR A&Ntilde;O : ".$wanopa."</b></td><td><b>VALOR A&Ntilde;O : ".$wanop."</b></td><td><b>DIFERENCIA.</b></td><td><b>% VARIACION.</b></td></tr>";
			$ini = strpos($wrubro,"-");
			$query = "SELECT rvpcco,cconom,sum(rvpvre)  from ".$empresa."_000044,".$empresa."_000005 ";
			$query = $query." where rvpcpr = '".substr($wrubro,0,$ini)."'";
			$query = $query."   and rvpano = ".$wanopa;
			$query = $query."   and rvpemp = '".$wemp."'";
			$query = $query."   and rvpper between ".$wper1." and ".$wper2;
			$query = $query."   and rvpcco= ccocod";
			$query = $query."   and rvpemp = ccoemp ";
			$query = $query."   group by rvpcco,cconom";
			$query = $query."   order by rvpcco,cconom";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT rvpcco,cconom,sum(rvpvre)  from ".$empresa."_000044,".$empresa."_000005 ";
			$query = $query." where rvpcpr = '".substr($wrubro,0,$ini)."'";
			$query = $query."   and rvpano = ".$wanop;
			$query = $query."   and rvpemp = '".$wemp."'";
			$query = $query."   and rvpper between ".$wper1." and ".$wper2;
			$query = $query."   and rvpcco= ccocod";
			$query = $query."   and rvpemp = ccoemp ";
			$query = $query."   group by rvpcco,cconom";
			$query = $query."   order by rvpcco,cconom";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$wtotan=0;
			$wtotac=0;
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1=1;
				$row1[0]='9999';
				$row1[1]=" ";
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
				$row2[0]='9999';
				$row2[1]=" ";
				$row2[2]=0;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($row1[0] == $row2[0])
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=$row2[2]-$row1[2];
					if($row1[2] != 0)
						$wdata[$num][5]=($row2[2] - $row1[2])/$row1[2] *100;
					else
						$wdata[$num][5]=0;
					$k1++;
					$k2++;
					if($k1 > $num1)
						$row1[0]="9999";
					else
						$row1 = mysql_fetch_array($err1);
					if($k2 > $num2)
						$row2[0]="9999";
					else
						$row2 = mysql_fetch_array($err2);
				}
				else if($row1[0] < $row2[0])
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=0-$row1[2];
					if($row1[2] != 0)
						$wdata[$num][5]=(0 - $row1[2])/$row1[2] *100;
					else
						$wdata[$num][5]=0;
					$k1++;
					if($k1 > $num1)
						$row1[0]="9999";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=$row2[2]-0;
					$wdata[$num][5]=0;
					$k2++;
					if($k2 > $num2)
						$row2[0]="9999";
					else
						$row2 = mysql_fetch_array($err2);
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				$wtotan+=$wdata[$i][2];
				$wtotac+=$wdata[$i][3];
				echo "<tr>";
       			echo "<td>".$wdata[$i][0]."</td>";
       			echo "<td>".$wdata[$i][1]."</td>";
       			echo "<td align=right>".number_format($wdata[$i][2],0,'.',',')."</td>";
       			echo "<td align=right>".number_format($wdata[$i][3],0,'.',',')."</td>";
       			echo "<td align=right>".number_format($wdata[$i][4],0,'.',',')."</td>";
       			echo "<td align=right>".number_format($wdata[$i][5],2,'.',',')."%</td></tr>";
			}
			$wdif=$wtotac-$wtotan;
			$wpor=($wtotac - $wtotan) / $wtotan * 100;
			echo "<tr>";
       		echo "<td>&nbsp</td>";
       		echo "<td><B>TOTALES</B></td>";
       		echo "<td align=right><B>".number_format($wtotan,0,'.',',')."</B></td>";
       		echo "<td align=right><B>".number_format($wtotac,0,'.',',')."</B></td>";
       		echo "<td align=right><B>".number_format($wdif,0,'.',',')."</B></td>";
       		echo "<td align=right><B>".number_format($wpor,2,'.',',')."%</B></td></tr>";
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
