<html>
<head>
  	<title>MATRIX Reporte de Prestamos de Nomina x Fechas</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte de Prestamos de Nomina x Fechas</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Lnomina.php Ver. 2007-01-22</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Lnomina' action='Lnomina.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper1) or !isset($wper2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>REPORTE DE PRESTAMOS DE NOMINA X FECHAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa</td>";
		$query = "SELECT Nomnom, Nomdes    from ".$empresa."_000036 Group by Nomnom order by Nomnom ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<td bgcolor=#cccccc align=center><select name='wemp'>";
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
		echo "<table border=0 align=center>";
		echo "<tr><td align=center  colspan=9><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=6 face='tahoma'><b>REPORTE DE PRESTAMOS DE NOMINA X FECHAS Ver. 2007-01-22</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='tahoma'><b>EMPRESA : ".substr($wemp,strpos($wemp,"-")+1)."</b></font></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='tahoma'><b>PERIODO : ".$wper1." A ".$wper2."</b></font></font></td></tr>";
		$query = "select Pnocon, Pnofec, Pnocod, Pnonom, Pnoval, (Pnoval / Pnocuo), Pnocuo, Pnocup, Pnovta  from ".$empresa."_000046 ";
		$query .= " where Pnofec between '".$wper1."' and '".$wper2."'";
		$query .= "     and Pnoemp = '".substr($wemp,0,strpos($wemp,"-"))."'";
		$query .= "     and Pnoest = 'on'  ";
		$query .= " Order by Pnonom, Pnocon  ";
		$err = mysql_query($query,$conex)  or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$wtotg=0;
		$wtots=0;
		echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Prestamo<br> Nro.</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Fecha</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Codigo<br>Nomina</b></font></td><td align=center bgcolor=#dddddd ><font face='tahoma' size=2><b>Empleado</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Valor<br>Total</b></font><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Valor<br>Cuota</b></font></td></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Cuotas <br>Totales</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Cuotas<br>Pendientes</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Saldo</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Nro Venta</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtotg += $row[4];
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			$wsaldo=$row[5]*$row[7];
			$wtots += $wsaldo;
			echo "<tr><td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[4],0,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[5],0,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".number_format((double)$row[6],0,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".number_format((double)$row[7],0,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$wsaldo,0,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[8]."</font></td></tr>";
		}
		echo "<tr><td bgcolor=#999999 colspan=4><font face='tahoma' size=2><b>TOTAL PRESTAMOS : ".number_format((double)$num,0,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotg,0,'.',',')."</b></font></td><td bgcolor=#999999 align=right colspan=3>&nbsp </td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtots,0,'.',',')."</b></font></td><td bgcolor=#999999 align=right>&nbsp </td></tr>";	
		echo"</table>";
	}
}
?>
</body>
</html>