<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparacion Presupuesto vs Real Anterior</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc107.php Ver. 2016-03-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc107.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wrubro) or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPARACION PRESUPUESTO VS REAL ANTERIOR</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes De Corte</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal</td>";
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
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wanopa=$wanop-1;
			$ini=strpos($wrubro,"-");
			$query = "SELECT  Meccco,cconom, sum(Mecval)  from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query." where Mecano = ".$wanopa;
			$query = $query."   and Mecemp = '".$wemp."' ";
			$query = $query."   and Mecmes between 1 and ".$wper1;
			$query = $query."   and Meccpr = '".substr($wrubro,0,$ini)."'";
			$query = $query."   and Meccco = ccocod ";
			$query = $query."   and Mecemp = ccoemp ";
			$query = $query."  group by Meccco,cconom";
			$query = $query."  order by Meccco";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT  Rescco,cconom, sum(Resmon)  from ".$empresa."_000043,".$empresa."_000005 ";
			$query = $query." where Resano = ".$wanop;
			$query = $query."   and Resemp = '".$wemp."' ";
			$query = $query."   and Resper between 1 and ".$wper1;
			$query = $query."   and Rescpr = '".substr($wrubro,0,$ini)."'";
			$query = $query."   and Rescco = ccocod ";
			$query = $query."   and Resemp = ccoemp ";
			$query = $query."  group by Rescco,cconom";
			$query = $query."  order by Rescco";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=9 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=9 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=9 align=center>COMPARACION PRESUPUESTO VS REAL ANTERIOR</td></tr>";
			echo "<tr><td colspan=9 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=9 align=center>PERIODO DE CORTE : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=9 align=center>RUBRO : ".$wrubro. "</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>DESCRIPCION</b></td><td align=right><b>A&Ntilde;O : ".$wanopa."</b></td><td align=right><b>A&Ntilde;O : ".$wanop."</b></td><td align=right><b>% VARIACION</b></td><td align=right><b>DIFERENCIA</b></td></tr>";
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
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row2[2];
					if($row1[2] != 0)
						$wdata[$num][4]=($row2[2]/$row1[2] - 1) * 100;
					else
						$wdata[$num][4]=0;
					$wdata[$num][5]=$row2[2] - $row1[2];
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
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					if($row1[2] != 0)
						$wdata[$num][4]=(0/$row1[2] - 1) * 100;
					else
						$wdata[$num][4]=0;
					$wdata[$num][5]=0 - $row1[2];
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
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row2[2] - 0;
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
				$wtotr=$wtotr+$wdata[$i][2];
				$wtotp=$wtotp+$wdata[$i][3];
				echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][4],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][5],2,'.',',')."</td></tr>";
    		}
    		if($wtotp != 0)
				$weje=($wtotp/$wtotr -1) * 100;
			else
				$weje=0;
			$wdif= $wtotp - $wtotr;
    		echo"<tr><td colspan=2><B>TOTALES</B></td><td align=right><B>".number_format((double)$wtotr,2,'.',',')."</B></td><td align=right><B>".number_format((double)$wtotp,2,'.',',')."</B></td><td align=right><B>".number_format((double)$weje,2,'.',',')." %</B></td><td align=right><B>".number_format((double)$wdif,2,'.',',')."</B></td></tr>";
		}
	}
?>
</body>
</html>
