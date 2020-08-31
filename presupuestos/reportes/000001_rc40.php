<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Analisis De Facturacion Por Grupos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc40.php Ver. 1.01</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[1] > $vec2[1])
		return -1;
	elseif ($vec1[1] < $vec2[1])
				return 1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc40.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ANALISIS DE FACTURACION POR GRUPOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$query = "SELECT cierre_ingresos,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper1;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on")
			{
			$wanopa=$wanop-1;
			$query = "select empgru,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061 ";
			$query = $query."  where mioano = ".$wanop;
			$query = $query."      and miomes = ".$wper1;
			$query = $query."      and miocco between '0' and 'z'";
			$query = $query."      and mionit = epmcod   ";
			$query = $query."    group by empgru  ";
			$query = $query."    order by empgru";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<center><table border=1>";
			echo "<tr><td colspan=7 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=7 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=7 align=center>ANALISIS DE FACTURACION POR GRUPOS</td></tr>";
			echo "<tr><td colspan=7 align=center>PERIODO  : ".$wper1."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			echo "<tr><td><b>GRUPO</b></td><td><b>FACTURADO</b></td><td><b>% PART</b></td></tr>";
			$wdata=array();
			$val=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wdata[$i][0]=$row[0];
				$wdata[$i][1]=$row[1];
				$val+=$row[1];
			}
			usort($wdata,'comparacion');
			for ($i=0;$i<$num;$i++)
			{
				if($val !=0)
					$pro=$wdata[$i][1]/$val * 100;
				else
					$pro=0;
				echo"<tr><td>".$wdata[$i][0]."</td><td  align=right>".number_format((double)$wdata[$i][1],0,'.',',')."</td><td  align=right>".number_format((double)$pro,2,'.',',')."</td></tr>";
			}
			$pro= 100;
			echo"<tr><td bgcolor='#99CCFF'><b>TOTAL FACTURADO</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$val,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$pro,2,'.',',')."</b></td></tr></table></center>";
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
