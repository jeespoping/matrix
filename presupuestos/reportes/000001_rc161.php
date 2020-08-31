<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte Control de Evaluacion de Convenios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc161.php Ver. 2013-06-28</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc161.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wper2) or $wper2 < 1 or $wper2 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>REPORTE CONTROL DE EVALUACION DE CONVENIOS</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=9 bgcolor=#FFFFFF><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center colspan=9 bgcolor=#FFFFFF><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center colspan=9 bgcolor=#FFFFFF><b>REPORTE CONTROL DE EVALUACION DE CONVENIOS</b></td></tr>";
			echo "<tr><td colspan=9 align=center bgcolor=#FFFFFF>A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=9 align=center bgcolor=#FFFFFF>PERIODO INICIAL  : ".$wper1. " CPERIODO FINAL : ".$wper2."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td bgcolor=#cccccc><b>CONCEPTO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td bgcolor=#cccccc><b>PROCEDIMIENTO</b></td><td bgcolor=#cccccc><b>NOMBRE</b></td><td bgcolor=#cccccc><b>CANTIDAD</b></td><td bgcolor=#cccccc><b>INGRESO<br>PROPIO</b></td><td bgcolor=#cccccc><b>INGRESO<br>PARA TERCEROS</b></td></tr>";
			//                  0      1      2      3      4      5      6      7          8           9           10
			$query  = "select Moslin,Lindes,Moscco,Cconom,Moscon,Cfades,Mospro,Mosdes,sum(Moscan),sum(Mosipr),sum(Mosite) ";
			$query .= " from ".$empresa."_000108,".$empresa."_000107,".$empresa."_000005,".$empresa."_000060 ";
			$query .= "  where mosano = ".$wanop; 
			$query .= "	   and mosmes between ".$wper1." and  ".$wper2; 
			$query .= "	   and mostip = 'FA' "; 
			$query .= "	   and mosest = 'off' "; 
			$query .= "	   and moslin = lincod ";
			$query .= "	   and moscco = ccocod ";
			$query .= "	   and moscon = cfacod  ";
			$query .= " group by 1,2,3,4,5,6,7,8 ";
			$query .= " order by 1,3,5,8 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$cypkey="";
			for ($i=0;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($i % 2 == 0)
					$color = "#C3D9FF";
				else
					$color = "#FFFFFF";
				if($row[0] != $cypkey)
				{
					$cypkey=$row[0];
					echo "<tr><td colspan=9 bgcolor=#FFCC66><b>LINEA : ".$row[0]." - ".$row[1]."</b></td></tr>";
				}
				echo "<tr><td bgcolor=".$color."> ".$row[2]."</td><td bgcolor=".$color."> ".$row[3]."</td><td bgcolor=".$color."> ".$row[4]."</td><td bgcolor=".$color."> ".$row[5]."</td><td bgcolor=".$color."> ".$row[6]."</td><td bgcolor=".$color."> ".$row[7]."</td><td bgcolor=".$color."> ".number_format($row[8],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($row[9],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($row[10],0,'.',',')."</td></tr>";
			}
			echo "</table>";
		}
}
?>
</body>
</html>
