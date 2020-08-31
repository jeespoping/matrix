<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Crecimiento Ponderado de Ingresos x Escenario</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc110.php Ver. 2016-03-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc110.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CRECIMIENTO PONDERADO DE INGRESOS X ESCENARIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes De Corte</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=2 align=center><input type='RADIO' name=tip value=1 checked> INGRESOS PROPIOS <input type='RADIO' name=tip value=2> INGRESOS PARA TERCEROS<input type='RADIO' name=tip value=3> INGRESOS TOTALES</td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=2 align=center><input type='RADIO' name=esc value=1 checked>ESCENARIO ALTO <input type='RADIO' name=esc value=2> ESCENARIO MEDIO<input type='RADIO' name=esc value=3> ESCENARIO BAJO</td></tr>";
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
			$query = "SELECT  Meccco,cconom, sum(Mecval)  from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query." where Mecano = ".$wanopa;
			$query = $query."   and Mecemp = '".$wemp."' ";
			$query = $query."   and Mecmes between 1 and ".$wper1;
			if($tip == 1)
				$query = $query."   and Meccpr = '100' ";
			elseif($tip == 2)
						$query = $query."   and Meccpr = '900' ";
					else
						$query = $query."   and (Meccpr = '100' or Meccpr = '900') ";
			$query = $query."    and Meccco = ccocod ";
			$query = $query."    and Mecemp = ccoemp ";
			$query = $query."   group by Meccco,cconom";
			$query = $query."   order by Meccco";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT  Rescco,cconom, sum(Resmon)  from ".$empresa."_000043,".$empresa."_000005 ";
			$query = $query." where Resano = ".$wanop;
			$query = $query."   and Resemp = '".$wemp."' ";
			$query = $query."   and Resper between 1 and ".$wper1;
			if($tip == 1)
				$query = $query."   and Rescpr = '100' ";
			elseif($tip == 2)
						$query = $query."   and Rescpr = '900' ";
					else
						$query = $query."   and (Rescpr = '100' or Rescpr = '900') ";
			$query = $query."    and Rescco = ccocod ";
			$query = $query."    and Resemp = ccoemp ";
			$query = $query."   group by Rescco,cconom";
			$query = $query."   order by Rescco";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			switch ($tip)
			{
				case 1:
					$tipn="INGRESOS PROPIOS";
				break;
				case 2:
					$tipn="INGRESOS PARA TERCEROS";
				break;
				case 3:
					$tipn="INGRESOS TOTALES";
				break;
			}
			switch ($esc)
			{
				case 1:
					$escn="ESCENARIO ALTO";
				break;
				case 2:
					$escn="ESCENARIO MEDIO";
				break;
				case 3:
					$escn="ESCENARIO BAJO";
				break;
			}
			echo "<table border=0>";
			echo "<tr><td bgcolor=#dddddd colspan=9 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=9 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=9 align=center>CRECIMIENTO PONDERADO DE INGRESOS X ESCENARIO</td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=9 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=9 align=center>PERIODO DE CORTE : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=9 align=center>TIPO DE INGRESOS : ".$tipn. " -- TIPO DE ESCENARIO : ".$escn."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td align=right bgcolor=#cccccc><b>A&Ntilde;O : ".$wanopa."</b></td><td align=right bgcolor=#cccccc><b>A&Ntilde;O : ".$wanop."</b></td><td align=right bgcolor=#cccccc><b>DIFERENCIA</b></td><td align=right bgcolor=#cccccc><b>% VARIACION<br> TOTAL</b></td><td align=right bgcolor=#cccccc><b>% VARIACION<br>  NOMINAL</b></td><td align=right bgcolor=#cccccc><b>% VARIACION<br>  REAL</b></td></tr>";
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
					$wdata[$num][4]=$row2[2] - $row1[2];
					if($row1[2] != 0)
						$wdata[$num][5]=($row2[2]/$row1[2] - 1) * 100;
					else
						$wdata[$num][5]=0;
					switch ($esc)
					{
						case 1:
							$query = "SELECT Inees1  from ".$empresa."_000010 ";
						break;
						case 2:
							$query = "SELECT  Inees2  from ".$empresa."_000010 ";
						break;
						case 3:
							$query = "SELECT  Inees3  from ".$empresa."_000010 ";
						break;
					}
					$query = $query." where Ineano = ".$wanop;
					$query = $query."   and Ineemp = '".$wemp."' ";
					$query = $query."   and Inecco = '".$row1[0]."' ";
					$query = $query."   and Inetip = 'P' ";
					$err3 = mysql_query($query,$conex);
					$num3 = mysql_num_rows($err3);
					if($num3 > 0)
					{
						$row3 = mysql_fetch_array($err3);
						$wdata[$num][6]=$row3[0];
					}
					else
						$wdata[$num][6]=0;
					if((1 + $wdata[$num][6]/100) != 0)
						$wdata[$num][7] = (((1 + $wdata[$num][5]/100)/(1 + $wdata[$num][6]/100)) -1) * 100;
					else
						$wdata[$num][7] = 0;
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
					$wdata[$num][4]=0 - $row1[2];
					if($row1[2] != 0)
						$wdata[$num][5]=(0/$row1[2] - 1) * 100;
					else
						$wdata[$num][5]=0;
					switch ($esc)
					{
						case 1:
							$query = "SELECT Inees1  from ".$empresa."_000010 ";
						break;
						case 2:
							$query = "SELECT  Inees2  from ".$empresa."_000010 ";
						break;
						case 3:
							$query = "SELECT  Inees3  from ".$empresa."_000010 ";
						break;
					}
					$query = $query." where Ineano = ".$wanop;
					$query = $query."   and Ineemp = '".$wemp."' ";
					$query = $query."   and Inecco = '".$row1[0]."' ";
					$query = $query."   and Inetip = 'P' ";
					$err3 = mysql_query($query,$conex);
					$num3 = mysql_num_rows($err3);
					if($num3 > 0)
					{
						$row3 = mysql_fetch_array($err3);
						$wdata[$num][6]=$row3[0];
					}
					else
						$wdata[$num][6]=0;
					if((1 + $wdata[$num][6]/100) != 0)
						$wdata[$num][7] = (((1 + $wdata[$num][5]/100)/(1 + $wdata[$num][6]/100)) -1) * 100;
					else
						$wdata[$num][7] = 0;
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
					$wdata[$num][4]=$row2[2];
					$wdata[$num][5]=0;
					switch ($esc)
					{
						case 1:
							$query = "SELECT Inees1  from ".$empresa."_000010 ";
						break;
						case 2:
							$query = "SELECT  Inees2  from ".$empresa."_000010 ";
						break;
						case 3:
							$query = "SELECT  Inees3  from ".$empresa."_000010 ";
						break;
					}
					$query = $query." where Ineano = ".$wanop;
					$query = $query."   and Ineemp = '".$wemp."' ";
					$query = $query."   and Inecco = '".$row1[0]."' ";
					$query = $query."   and Inetip = 'P' ";
					$err3 = mysql_query($query,$conex);
					$num3 = mysql_num_rows($err3);
					if($num3 > 0)
					{
						$row3 = mysql_fetch_array($err3);
						$wdata[$num][6]=$row3[0];
					}
					else
						$wdata[$num][6]=0;
					if((1 + $wdata[$num][6]/100) != 0)
						$wdata[$num][7] = (((1 + $wdata[$num][5]/100)/(1 + $wdata[$num][6]/100)) -1) * 100;
					else
						$wdata[$num][7] = 0;
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
			$wtotac=0;
			$wtotan=0;
			$wtotvar=0;
			for ($i=0;$i<=$num;$i++)
			{
				if($i % 2 == 0)
					$wcolor="#99CCFF";
				else
					$wcolor="#FFFFCC";
				$wtotan+=$wdata[$i][2];
				$wtotac+=$wdata[$i][3];
				$wtotvar+=$wdata[$i][6] / 100  * $wdata[$i][2];
				echo"<tr><td bgcolor=".$wcolor.">".$wdata[$i][0]."</td><td bgcolor=".$wcolor.">".$wdata[$i][1]."</td><td align=right bgcolor=".$wcolor.">".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right bgcolor=".$wcolor.">".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right bgcolor=".$wcolor.">".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right bgcolor=".$wcolor.">".number_format((double)$wdata[$i][5],2,'.',',')."%</td><td align=right bgcolor=".$wcolor.">".number_format((double)$wdata[$i][6],2,'.',',')."%</td><td align=right bgcolor=".$wcolor.">".number_format((double)$wdata[$i][7],2,'.',',')."%</td></tr>";
    		}
    		if($wtotan != 0)
				$wvart=($wtotac/$wtotan -1) * 100;
			else
				$wvart=0;
			$wdif= $wtotac - $wtotan;
			if($wtotan != 0)
				$wvarn= $wtotvar / $wtotan * 100;
			else
				$wvarn=0;
			if((1 + $wvarn/100) != 0)
				$wvarr = (((1 + $wvart/100)/(1 + $wvarn/100)) -1) * 100;
			else
				$wvarr = 0;
    		echo"<tr><td colspan=2 bgcolor=#cccccc><B>TOTALES</B></td><td align=righ bgcolor=#cccccc><B>".number_format((double)$wtotan,0,'.',',')."</B></td><td align=right bgcolor=#cccccc><B>".number_format((double)$wtotac,0,'.',',')."</B></td><td align=right bgcolor=#cccccc><B>".number_format((double)$wdif,0,'.',',')." </B></td><td align=right bgcolor=#cccccc><B>".number_format((double)$wvart,2,'.',',')."%</B></td><td align=right bgcolor=#cccccc><B>".number_format((double)$wvarn,2,'.',',')."%</B></td><td align=right bgcolor=#cccccc><B>".number_format((double)$wvarr,2,'.',',')."%</B></td></tr>";
		}
	}
?>
</body>
</html>
