<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparacion Operativa Presupuesto vs Real Anterior</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc108.php Ver. 2016-03-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc108.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPARACION OPERATIVA PRESUPUESTO VS REAL ANTERIOR</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes De Corte</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";	
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$wanopa=$wanop-1;
			$query = "SELECT  Morcco, Morcod, cconom, Prodes, sum(Morcan)  from ".$empresa."_000032,".$empresa."_000005,".$empresa."_000059 ";
			$query = $query." where Morano = ".$wanopa;
			$query = $query."   and Moremp = '".$wemp."' ";
			$query = $query."   and Mormes between 1 and ".$wper1;
			$query = $query."   and Morcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and Morcod = Procod ";
			$query = $query."   and Moremp = Proemp ";
			$query = $query."   and Morcco = ccocod ";
			$query = $query."   and Moremp = ccoemp ";
			$query = $query."  group by Morcco, Morcod, cconom, Prodes ";
			$query = $query."  order by Morcco, Morcod";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT  Mopcco, Mopcod, cconom, Prodes, sum(Mopcan)  from ".$empresa."_000031,".$empresa."_000005,".$empresa."_000059 ";
			$query = $query." where Mopano = ".$wanop;
			$query = $query."   and Mopemp = '".$wemp."' ";
			$query = $query."   and Mopmes between 1 and ".$wper1;
			$query = $query."   and Mopcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and Mopcod = Procod ";
			$query = $query."   and Mopemp = Proemp ";
			$query = $query."   and Mopcco = ccocod ";
			$query = $query."   and Mopemp = ccoemp ";
			$query = $query."  group by Mopcco, Mopcod, cconom, Prodes ";
			$query = $query."  order by Mopcco, Mopcod ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=9 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=9 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=9 align=center>COMPARACION OPERATIVA PRESUPUESTO VS REAL ANTERIOR</td></tr>";
			echo "<tr><td colspan=9 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=9 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=9 align=center>PERIODO DE CORTE : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>PROCEDIMIENTO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td align=right bgcolor=#cccccc><b>A&Ntilde;O : ".$wanopa."</b></td><td align=right bgcolor=#cccccc><b>A&Ntilde;O : ".$wanop."</b></td><td align=right bgcolor=#cccccc><b>% VARIACION</b></td><td align=right bgcolor=#cccccc><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='ZZZZZZ';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0].$row1[1];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='ZZZZZZ';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0].$row2[1];
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
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=$row1[4];
					$wdata[$num][5]=$row2[4];
					if($row1[4] != 0)
						$wdata[$num][6]=($row2[4]/$row1[4] - 1) * 100;
					else
						$wdata[$num][6]=0;
					$wdata[$num][7]=$row2[4] - $row1[4];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="ZZZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
					if($k2 > $num2)
						$key2="ZZZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=$row1[4];
					$wdata[$num][5]=0;
					if($row1[4] != 0)
						$wdata[$num][6]=(0/$row1[4] - 1) * 100;
					else
						$wdata[$num][6]=0;
					$wdata[$num][7]=0 - $row1[4];
					$k1++;
					if($k1 > $num1)
						$key1="ZZZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=$row2[3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row2[4];
					$wdata[$num][6]=0;
					$wdata[$num][7]=$row2[4];
					$k2++;
					if($k2 > $num2)
						$key2="ZZZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
			}
			$wtotr=0;
			$wtotp=0;
			$wccoa="";
			for ($i=0;$i<=$num;$i++)
			{
				$wtotr=$wtotr+$wdata[$i][4];
				$wtotp=$wtotp+$wdata[$i][5];
				if($wccoa != $wdata[$i][0])
				{
					echo "<tr><td colspan=9 bgcolor=#99CCFF align=center><b>UNIDAD : ".$wdata[$i][0]. " - ".$wdata[$i][2]."</b></td></tr>";
					$wccoa=$wdata[$i][0];
				}
				if($wdata[$i][1] == "27" or $wdata[$i][1] == "34")
				{
					$wdata[$i][4]=$wdata[$i][4] / $wper1;
					$wdata[$i][5]=$wdata[$i][5] / $wper1;
					if($wdata[$i][1] == "34")
					{
						if($wdata[$i][5] != 0)
							$wdata[$i][6]=(round($wdata[$i][5])/round($wdata[$i][4]) - 1) * 100;
						else
							$wdata[$i][6]=0;
						$wdata[$i][7]=$wdata[$i][5] - $wdata[$i][4];
					}
				}
				if($wdata[$i][1] != "27" )
					echo"<tr><td align=center>".$wdata[$i][1]."</td><td>".$wdata[$i][3]."</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][7],0,'.',',')."</td></tr>";
				else
					echo"<tr><td align=center>".$wdata[$i][1]."</td><td>".$wdata[$i][3]."</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>N/A</td><td align=right>N/A</td></tr>";
    		}
		}
	}
?>
</body>
</html>
