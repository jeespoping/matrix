<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparativo de Procedimientos Entre A&ntilde;os X Unidades de Negocio</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc28.php Ver. 2016-03-08</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc28.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2)  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPARATIVO DE PROCEDIMIENTOS ENTRE A&Ntilde;OS X UNIDADES DE NEGOCIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
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
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query = "SELECT Cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			$wmeses = $wper2 - $wper1 + 1;
			$wanopa = $wanop -1;
			$query = "SELECT ccocod,cconom, sum(morcan)  from ".$empresa."_000032,".$empresa."_000005,".$empresa."_000059 ";
			$query = $query."  where morano = ".$wanopa;
			$query = $query."    and moremp = '".$wemp."' ";
			$query = $query."    and mormes between ".$wper1." and ".$wper2;
			$query = $query."    and morcco = ccocod ";
			$query = $query."    and moremp = ccoemp ";
			$query = $query."    and ccoclas = 'PR' ";
			$query = $query."    and morcod = procod ";
			$query = $query."    and moremp = proemp ";
			$query = $query."    and protip = 'A' ";
			$query = $query." group by ccocod,cconom ";
			$query = $query." order by ccocod ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT ccocod,cconom, sum(morcan)  from ".$empresa."_000032,".$empresa."_000005,".$empresa."_000059 ";
			$query = $query."  where morano = ".$wanop;
			$query = $query."    and moremp = '".$wemp."' ";
			$query = $query."    and mormes between ".$wper1." and ".$wper2;
			$query = $query."    and morcco = ccocod ";
			$query = $query."    and moremp = ccoemp ";
			$query = $query."    and ccoclas = 'PR' ";
			$query = $query."    and morcod = procod ";
			$query = $query."    and moremp = proemp ";
			$query = $query."    and protip = 'A' ";
			$query = $query." group by ccocod,cconom ";
			$query = $query." order by ccocod ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>COMPARATIVO DE PROCEDIMIENTOS ENTRE A&Ntilde;OS X UNIDADES DE NEGOCIO</td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>LINEA DE INGRESOS</b></td><td align=right><b>CANTIDAD A&Ntilde;O :".$wanopa."</b></td><td align=right><b>CANTIDAD A&Ntilde;O:".$wanop."</b></td><td align=right><b>VARIACION</b></td><td align=right><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='ZZZ';
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
					$wdata[$num][0]=$row1[0]."-".$row1[1];
					if($row1[0] == "NCA" or $row1[0] == "INO")
					{
						$row1[2]=$row1[2]/$wmeses;
						$row2[2]=$row2[2]/$wmeses;
					}
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=($row2[2]-$row1[2])/$row1[2] * 100;
					$wdata[$num][4]=$row2[2] - $row1[2];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="ZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
					if($k2 > $num2)
						$key2="ZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0]."-".$row1[1];
					if($row1[0] == "NCA" or $row1[0] == "INO")
						$row1[2]=$row1[2]/$wmeses;
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=0;
					if($row1[2] != 0)
						$wdata[$num][3]=0/$row1[2] * 100;
					else
						$wdata[$num][3]=0;
					$wdata[$num][4]=0 - $row1[2];
					$k1++;
					if($k1 > $num1)
						$key1="ZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0]."-".$row2[1];
					$wdata[$num][1]=0;
					if($row2[0] == "NCA" or $row2[0] == "INO")
						$row2[2]=$row2[2]/$wmeses;
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=$row2[2] - 0;
					$k2++;
					if($k2 > $num2)
						$key2="ZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
			}
			if (isset($wdata[0][0]))
				$cargo=$wdata[0][0];
			for ($i=0;$i<=$num;$i++)
			{
				echo"<tr><td>".$wdata[$i][0]."</td><td align=right>".number_format((double)$wdata[$i][1],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td></tr>";
    		}
    		echo "</table>";
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
