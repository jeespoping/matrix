<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ejecucion de un Centro de Servicio</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc76.php Ver. 2016-05-27</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc76.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or !isset($wcco1)  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EJECUCION DE UN CENTRO DE SERVICIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Servicio</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
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
			$wmeses = $wper2 - $wper1 + 1;
			$query = "SELECT Proccd,cconom,sum(Promon) from ".$empresa."_000073,".$empresa."_000005 ";
			$query = $query." where Proano = ".$wanop;
			$query = $query."   and Proemp = '".$wemp."'";
			$query = $query."   and Promes between ".$wper1." and ".$wper2;
			$query = $query."   and Procco = '".$wcco1."'";
			$query = $query."   and Protip = 'R'";
			$query = $query."   and Proemp = ccoemp ";
			$query = $query."   and Proccd= ccocod";
			$query = $query."  group by  Procco,cconom";
			$query = $query."  order by Procco";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT Proccd,cconom,sum(Promon) from ".$empresa."_000073,".$empresa."_000005 ";
			$query = $query." where Proano = ".$wanop;
			$query = $query."   and Proemp = '".$wemp."'";
			$query = $query."   and Promes between ".$wper1." and ".$wper2;
			$query = $query."   and Procco = '".$wcco1."'";
			$query = $query."   and Protip = 'P'";
			$query = $query."   and Proemp = ccoemp ";
			$query = $query."   and Proccd= ccocod";
			$query = $query."  group by  Procco,cconom";
			$query = $query."  order by Procco";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>EJECUCION DE UN CENTRO DE SERVICIO</td></tr>";
			echo "<tr><td colspan=8 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=8 align=center>CENTRO DE SERVICIO  : ".$wcco1."</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>DESCRIPCION</b></td><td align=right><b>MONTO REAL</b></td><td align=right><b>MONTO PPTO</b></td><td align=right><b>EJECUCION</b></td><td align=right><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzzz';
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
				$key2='zzzz';
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
					$wdata[$num][4]=$row1[2]/$row2[2] * 100;
					$wdata[$num][5]=$row2[2] - $row1[2];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
					if($k2 > $num2)
						$key2="zzzz";
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
					$wdata[$num][4]=0;
					$wdata[$num][5]=0 - $row1[2];
					$k1++;
					if($k1 > $num1)
						$key1="zzzz";
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
					$wdata[$num][4]=0/$row2[2] * 100;;
					$wdata[$num][5]=$row2[2] - 0;
					$k2++;
					if($k2 > $num2)
						$key2="zzzz";
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
				$weje=$wtotr/$wtotp * 100;
			else
				$weje=0;
			$wdif= $wtotp - $wtotr;
    		echo"<tr><td colspan=2><B>TOTALES</B></td><td align=right><B>".number_format((double)$wtotr,2,'.',',')."</B></td><td align=right><B>".number_format((double)$wtotp,2,'.',',')."</B></td><td align=right><B>".number_format((double)$weje,2,'.',',')." %</B></td><td align=right><B>".number_format((double)$wdif,2,'.',',')."</B></td></tr>";
		}
	}
?>
</body>
</html>
