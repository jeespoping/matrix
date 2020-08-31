<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Distribuci�n De Los Ingresos x Segmento</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc39.php Ver. 1.01</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_rc39.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wseg) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DISTRIBUCI�N DE LOS INGRESOS X SEGMENTO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Segmento</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT segcod,segdes from ".$empresa."_000045 order by segdes";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wseg'>";
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
			$ini = strpos($wseg,"-");
			$wsegm=substr($wseg,$ini+1);
			$wseg=substr($wseg,0,$ini);
			$Graph="/matrix/images/medical/presupuestos/rc39_".$wanop."_".$wper1."_".$wseg.".jpg";
			$query = "SELECT cierre_ingresos,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper1;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on")
			{
			$query = "select sum(Mioito) from ".$empresa."_000063 ";
			$query = $query."  where mioano = ".$wanop;
			$query = $query."      and miomes = ".$wper1;
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$gtotal=$row[0];
			$query = "select empdes,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061 ";
			$query = $query."  where mioano = ".$wanop;
			$query = $query."      and miomes = ".$wper1;
			$query = $query."      and miocco between '0' and 'z'";
			$query = $query."      and mionit = epmcod   ";
			$query = $query."      and empseg = '".$wseg."'";
			$query = $query."    group by empdes  ";
			$query = $query."    order by empdes";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=7 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=7 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=7 align=center>DISTRIBUCI�N DE LOS INGRESOS X SEGMENTO</td></tr>";
			echo "<tr><td colspan=7 align=center>SEGMENTO : ".$wsegm."</td></tr>";
			echo "<tr><td colspan=7 align=center>PERIODO  : ".$wper1."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			echo "<tr><td><b>ENTIDAD</b></td><td><b>VALOR FACTURADO</b></td><td><b>% PART EN SEG.</b></td><td><b>% PART EN ING. TOT.</b></td></tr>";
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
				if($val != 0)
					$pro1=$wdata[$i][1]/$val * 100;
				else
					$pro1=0;
				if($gtotal != 0)
					$pro2=$wdata[$i][1]/$gtotal * 100;
				else
					$pro2=0;
				echo"<tr><td>".$wdata[$i][0]."</td><td  align=right>".number_format((double)$wdata[$i][1],0,'.',',')."</td><td  align=right>".number_format((double)$pro1,2,'.',',')."</td><td  align=right>".number_format((double)$pro2,2,'.',',')."</td></tr>";
			}
			$pro1= 100;
			if($val != 0)
				$pro2=$val/$gtotal *100;
			else
				$pro2=0;
			echo"<tr><td bgcolor='#99CCFF'><b>TOTAL FACTURADO</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$val,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$pro1,2,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$pro2,2,'.',',')."</b></td></tr>";
			echo "<tr><td colspan=4 align=center><IMG SRC=".$Graph."></td></tr></table>";
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
