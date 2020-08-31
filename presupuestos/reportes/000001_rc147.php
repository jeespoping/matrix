<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Conciliacion de Explicaciones</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc147.php Ver. 2016-09-19</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc147.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper) or $wper < 1 or $wper > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE CONCILIACION DE EXPLICACIONES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' size=2 maxlength=2></td></tr>";
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
			$wemp = substr($wemp,0,2);
			$query  = "select ".$empresa."_000011.Expcco,".$empresa."_000011.expcpr,SUM(".$empresa."_000011.Expmon) ";
			$query .= " from ".$empresa."_000011 ";
			$query .= " where ".$empresa."_000011.expano = ".$wano;
			$query .= "   and ".$empresa."_000011.expemp = '".$wemp."'";
			$query .= "   and ".$empresa."_000011.expper = ".$wper; 
			$query .= "   group by 1,2 ";
			$query .= "   order by 1,2 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query  = "select ".$empresa."_000026.meccco,".$empresa."_000026.meccpr,SUM(".$empresa."_000026.Mecval) "; 
			$query .= " from ".$empresa."_000026 "; 
			$query .= " where ".$empresa."_000026.mecano = ".$wano;
			$query .= "   and ".$empresa."_000026.mecemp = '".$wemp."'";
			$query .= "   and ".$empresa."_000026.mecmes = ".$wper;
			$query .= "   group by 1,2 ";
			$query .= "   order by 1,2 ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<center><table border=0>";
			echo "<tr><td colspan=7 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=7 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=7 align=center>INFORME DE CONCILIACION DE EXPLICACIONES</td></tr>";
			echo "<tr><td colspan=7 align=center>A&Ntilde;O : ".$wano." MES : ".$wper. "</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>RUBRO</b></td><td bgcolor=#cccccc><b>VALOR TABLA 11</b></td><td bgcolor=#cccccc><b>VALOR TABLA 26</b></td><td bgcolor=#cccccc><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1=1;
				$row1[0]='zzzzzzz';
				$row1[1]=0;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2=1;
				$row2[0]='zzzzzzz';
				$row2[1]=0;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($row1[0].$row1[1] == $row2[0].$row2[1])
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=$row1[2] - $row2[2];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$row1[0]="zzzzzzz";
					else
						$row1 = mysql_fetch_array($err1);
					if($k2 > $num2)
						$row2[0]="zzzzzzz";
					else
						$row2 = mysql_fetch_array($err2);
				}
				else if($row1[0].$row1[1] < $row2[0].$row2[1])
				{
					$k1++;
					if($k1 > $num1)
						$row1[0]="zzzzzzz";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
					$k2++;
					if($k2 > $num2)
						$row2[0]="zzzzzzz";
					else
						$row2 = mysql_fetch_array($err2);
				}
			}
			$k=0;
			for ($i=0;$i<=$num;$i++)
			{
				if(abs($wdata[$i][4]) >= 1)
				{
					if($k % 2 == 0)
						$color="#99CCFF";
					else
						$color="#FFFFFF";
					echo "<tr><td bgcolor=".$color.">".$wdata[$i][0]."</td><td bgcolor=".$color.">".$wdata[$i][1]."</td><td bgcolor=".$color." align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td></tr>";
					$k++;
				}
			}
			echo "</center></tabla>";
		}
	}
?>
</body>
</html>
