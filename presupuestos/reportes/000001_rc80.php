<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Validacion Movimiento de Activos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc80.php Ver. 2016-03-10</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc80.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wcco1) or !isset($wcco2)  or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>VALIDACION MOVIMIENTO DE ACTIVOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			$wcco2=strtolower ($wcco2);
			$query = "SELECT   Depcco, Depcod, Acfdes, sum(Depvac), sum(Depvde)  from ".$empresa."_000076,".$empresa."_000005,".$empresa."_000075 ";
			$query = $query." where Depano = ".$wanop;
			$query = $query."   and Depemp = '".$wemp."' ";
			$query = $query."   and Depmes = ".$wper1;
			$query = $query."   and Depcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and Depcco= ccocod";
			$query = $query."   and Depemp= ccoemp";
			$query = $query."   and ccocos= 'S'";
			$query = $query."   and Depcod= Acfcod";
			$query = $query."   and Depemp= Acfemp";
			$query = $query."  group by Depcco, Depcod, Acfdes ";
			$query = $query."  order by Depcco, Depcod";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT Daccco, Daccod, Acfdes, sum(0), sum(Dacpor)  from ".$empresa."_000084,".$empresa."_000075 ";
			$query = $query." where Dacano = ".$wanop;
			$query = $query."   and Dacemp = '".$wemp."' ";
			$query = $query."   and Dacmes = ".$wper1;
			$query = $query."   and Daccod = Acfcod";
			$query = $query."   and Dacemp = Acfemp";
			$query = $query."  group by Daccco, Daccod, Acfdes";
			$query = $query."  order by Daccco, Daccod";
			$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>VALIDACION MOVIMIENTO DE ACTIVOS</td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>ACTIVO</b></td><td><b>DESCRIPCION</b></td><td><b>VALOR EN LIBROS</b></td><td align=right><b>PORCENTAJE</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzzzzzzzzz';
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
				$key2='zzzzzzzzzz';
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
					if ($row2[4] != 1)
					{
						$num++;
						$wdata[$num][0]=$row1[0];
						$wdata[$num][1]=$row1[1];
						$wdata[$num][2]=$row1[2];
						$wdata[$num][3]=$row1[3];
						$wdata[$num][4]=$row2[4];
					}
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
					if($k2 > $num2)
						$key2="zzzzzzzzzz";
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
					$wdata[$num][4]=0;
					$k1++;
					if($k1 > $num1)
						$key1="zzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
				}
				else
				{
					$k2++;
					if($k2 > $num2)
						$key2="zzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td>".$wdata[$i][2]."</td><td align=right>".number_format((double)$wdata[$i][3],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][4],2,'.',',')."</td></tr>";
    		}
		}
	}
?>
</body>
</html>
