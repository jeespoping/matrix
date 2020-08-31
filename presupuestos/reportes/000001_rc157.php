<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Variacion en Traslados de Almacen</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc157.php Ver. 2016-02-05</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc157.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanop2) or !isset($wper11) or !isset($wper12) or !isset($wper21) or !isset($wper22) or !isset($wcco) or !isset($wpor) or $wper11 < 1 or $wper11 > 12 or $wper12 < 1 or $wper12 > 12 or $wper21 < 1 or $wper21 > 12 or $wper22 < 1 or $wper22 > 12 or $wper11 > $wper12 or $wper21 > $wper22)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>VARIACION EN TRASLADOS DE ALMACEN</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de  Inicial Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper11' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de  Final Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper12' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de  Inicial Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper21' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de  Final Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper22' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco' size=4 maxlength=4></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal Inicial</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT mgacod,mganom from ".$empresa."_000028 order by mgacod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wcodi'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal Final</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT mgacod,mganom from ".$empresa."_000028 order by mgacod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wcodf'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>% de Variacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpor' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wcco2=strtolower ($wcco2);
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=8 bgcolor=#FFFFFF><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center colspan=8 bgcolor=#FFFFFF><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center colspan=8 bgcolor=#FFFFFF><b>CONCILIACION DE TRASLADOS Y EXPLICACIONES</b></td></tr>";
			echo "<tr><td colspan=8 align=center bgcolor=#FFFFFF>PERIODO  INICIAL : A&Ntilde;O : ".$wanop1. " MES INICIAL : ".$wper11." MES FINAL . ".$wper12."</td></tr>";
			echo "<tr><td colspan=8 align=center bgcolor=#FFFFFF>PERIODO  FINAL   : A&Ntilde;O : ".$wanop2. " MES INICIAL : ".$wper21." MES FINAL . ".$wper22."</td></tr>";
			echo "<tr><td colspan=8 align=center bgcolor=#FFFFFF>RUBRO PRESUPUESTAL INICIAL : ".substr($wcodi,0,strpos($wcodi,"-"))." RUBRO PRESUPUESTAL FINAL  : ".substr($wcodf,0,strpos($wcodf,"-"))."</td></tr>";
			echo "<tr><td colspan=8 align=center bgcolor=#FFFFFF>UNIDAD  : ".$wcco."</td></tr>";
			echo "<tr><td colspan=8 align=center bgcolor=#FFFFFF>% VARIACION  : ".$wpor."</td></tr>";
			echo "<tr><td colspan=8 align=center bgcolor=#FFFFFF>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>RUBRO</b></td><td bgcolor=#cccccc><b>DESCRIPCION<br>RUBRO</b></td><td bgcolor=#cccccc><b>CODIGO<BR>ARTICULO</b></td><td bgcolor=#cccccc><b>DESCRIPCION<BR>ARTICULO</b></td><td bgcolor=#cccccc><b>COSTO TOTAL<br>INICIAL</b></td><td bgcolor=#cccccc><b>COSTO TOTAL<br>FINAL</b></td><td bgcolor=#cccccc><b>DIFERENCIA</b></td><td bgcolor=#cccccc><b>% VARIACION</b></td></tr>";
			//                  0       1        2      3           4
 			$query  = "select Almcpr, Mganom, Almcod, Almdes, sum(Almcto) from ".$empresa."_000002,".$empresa."_000028 ";
			$query .= " where Almano = ".$wanop1; 
			$query .= "   and Almemp = '".$wemp."'";
			$query .= "   and Almmes between ".$wper11." and ".$wper12;
			$query .= "   and Almcco = '".$wcco."' ";
			$query .= "   and Almcpr between '".substr($wcodi,0,strpos($wcodi,"-"))."' and '".substr($wcodf,0,strpos($wcodf,"-"))."'";
			$query .= "   and Almcpr = Mgacod ";
			$query .= "  group by Almcpr, Mganom, Almcod, Almdes  ";
			$query .= "  order by Almcpr, Almcod ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			//                  0       1       2       3           4 
			$query  = "select Almcpr, Mganom, Almcod, Almdes, sum(Almcto) from ".$empresa."_000002,".$empresa."_000028 ";
			$query .= " where Almano = ".$wanop2; 
			$query .= "   and Almemp = '".$wemp."'";
			$query .= "   and Almmes between ".$wper21." and ".$wper22;
			$query .= "   and Almcco = '".$wcco."' ";
			$query .= "   and Almcpr between '".substr($wcodi,0,strpos($wcodi,"-"))."' and '".substr($wcodf,0,strpos($wcodf,"-"))."'";
			$query .= "   and Almcpr = Mgacod ";
			$query .= "  group by Almcpr, Mganom, Almcod, Almdes  ";
			$query .= "  order by Almcpr, Almcod ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzzzzzzzzzz';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0].$row1[2];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='zzzzzzzzzzz';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0].$row2[2];
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
					$wdata[$num][6]=$row2[4] - $row1[4];
					if($row1[4] > 0)
						$wdata[$num][7]=($row2[4] - $row1[4]) / $row1[4] * 100;
					else
						$wdata[$num][7]=100;
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[2];
					}
					if($k2 > $num2)
						$key2="zzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[2];
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
					$wdata[$num][6]=0 - $row1[4];
					$wdata[$num][7]=-100;
					$k1++;
					if($k1 > $num1)
						$key1="zzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[2];
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
					$wdata[$num][6]=$row2[4] - 0;
					$wdata[$num][7]=100;
					$k2++;
					if($k2 > $num2)
						$key2="zzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[2];
					}
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				if($i % 2 == 0)
					$color = "#99CCFF";
				else
					$color = "#FFFFFF";
				echo "<tr><td bgcolor=".$color."> ".$wdata[$i][0]."</td><td bgcolor=".$color."> ".$wdata[$i][1]."</td><td bgcolor=".$color."> ".$wdata[$i][2]."</td><td bgcolor=".$color."> ".$wdata[$i][3]."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][4],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][5],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][6],0,'.',',')."</td>";
				if($wdata[$i][7] > $wpor or $wdata[$i][7] < $wpor*(-1))
					$color = "#FF0000";
				echo "<td align=right bgcolor=".$color."> ".number_format($wdata[$i][7],0,'.',',')."%</td></tr>";
			}
			echo "</table>";
		}
}
?>
</body>
</html>
