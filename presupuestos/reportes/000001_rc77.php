<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Validacion Costos de Nomina</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc77.php Ver. 2016-03-10</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc77.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanop)   or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>VALIDACION COSTOS DE NOMINA</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			$query = "SELECT cierre_costos from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = ".$wper1;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "off")
			{
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>VALIDACION COSTOS DE NOMINA</b></td></tr>";
			echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=6 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>CODIGO C.C.</b></td><td><b>NOMBRE C.C.</b></td><td><b>VALOR COSTOS</b></td><td><b>VALOR PRESUPUESTO</b></td><td><b>DIFERENCIA.</b></td></tr>";
			$query = "SELECT   Mnocco,cconom, sum(Mnopag)  from ".$empresa."_000094,".$empresa."_000005 ";
			$query = $query."  where Mnoano = ".$wanop;
			$query = $query."    and Mnoemp = '".$wemp."' ";
			$query = $query."    and Mnomes = ".$wper1;
			$query = $query."    and Mnocco= ccocod";
			$query = $query."    and Mnoemp= ccoemp";
			$query = $query."   group by Mnocco,cconom";
			$query = $query."   order by Mnocco";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT Meccco,cconom,sum(Mecval)   from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query."  where Mecano = ".$wanop;
			$query = $query."    and Mecemp = '".$wemp."' ";
			$query = $query."    and Mecmes = ".$wper1;
			$query = $query."    and Meccpr = '201' ";
			$query = $query."    and Meccco= ccocod";
			$query = $query."    and Mecemp= ccoemp";
			$query = $query."   group by Meccco,cconom";
			$query = $query."   order by Meccco";
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
					$k1++;
					if($k1 > $num1)
						$row1[0]="9999";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
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
       			echo "<td align=right>".number_format($wdata[$i][4],0,'.',',')."</td></tr>";
			}
			$wdif=$wtotac-$wtotan;
			echo "<tr>";
       		echo "<td>&nbsp</td>";
       		echo "<td><B>TOTALES</B></td>";
       		echo "<td align=right><B>".number_format($wtotan,0,'.',',')."</B></td>";
       		echo "<td align=right><B>".number_format($wtotac,0,'.',',')."</B></td>";
       		echo "<td align=right><B>".number_format($wdif,0,'.',',')."</B></td></tr></table>";
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
