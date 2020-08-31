<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Comparativo de Indicadores Financieros Entre A�os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc96.php Ver. 1.00</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return 1;
	elseif ($vec1[0] < $vec2[0])
				return -1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc96.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wgru) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME COMPARATIVO DE INDICADORES FINANCIEROS ENTRE A�OS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Indicador</td>";
			echo "<td bgcolor=#cccccc align=center><select name='wind'>";
			$query = "SELECT indcod,inddes from ".$empresa."_000015  order by indcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}	
			echo "</td></tr>";
			echo "<td bgcolor=#cccccc align=center>Forma de Agrupacion</td>";
			echo "<td bgcolor=#cccccc align=center><select name='wgru'>";
			$query = "SELECT grucod ,grudes  from ".$empresa."_000013 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}	
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wind1=$wind;
			$ini=strpos($wind,"-");
			$wind=substr($wind,0,$ini);
			$wind1=substr($wind1,$ini+1,strlen($wind1));
			$wgru1=$wgru;
			$ini=strpos($wgru,"-");
			$wgru=substr($wgru,0,$ini);
			$wgru1=substr($wgru1,$ini+1,strlen($wgru1));
			if ($wind == "RCA" or $wind == "RID")
				$por = "D";
			else
				$por = "%";
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper1;
			if($wgru != "MENS")
				$wper1=13;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			$wanopa=$wanop-1;
			$query = "SELECT infcco,cconom,infmon from ".$empresa."_000016,".$empresa."_000005 ";
			$query = $query."  where infano = ".$wanopa;
			$query = $query."    and infmes = ".$wper1;
			$query = $query."    and infcod = '".$wind."'";
			$query = $query."    and infgru = '".$wgru."'";
			$query = $query."    and infcco = ccocod";
			$query = $query."   order by infcco,infcod";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT infcco,cconom,infmon from ".$empresa."_000016,".$empresa."_000005 ";
			$query = $query."  where infano = ".$wanop;
			$query = $query."    and infmes = ".$wper1;
			$query = $query."    and infcod = '".$wind."'";
			$query = $query."    and infgru = '".$wgru."'";
			$query = $query."    and infcco = ccocod";
			$query = $query."   order by infcco,infcod";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=5 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=5 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=5 align=center>INFORME COMPARATIVO DE INDICADORES FINANCIEROS ENTRE A�OS</td></tr>";
			echo "<tr><td colspan=5 align=center><font size=2>INDICADOR :<b> ".$wind1."</b>  AGRUPACION  :<b> ".$wgru1."</b></font></td></tr>";
			echo "<tr><td><b>UNIDAD</b></td><td><b>A�O : ".$wanopa."</b></td><td><b>A�O : ".$wanop."</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1=1;
				$kla1="9999";
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$kla1=$row1[0];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2=1;
				$kla2="9999";
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$kla2=$row2[0];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($kla1 == $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[1];
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=$row2[2];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$kla1="9999";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=$row1[0];
					}
					if($k2 > $num2)
						$kla2="9999";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=$row2[0];
					}
				}
				else if($kla1 < $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[1];
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=0;
					$k1++;
					if($k1 > $num1)
						$kla1="9999";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=$row1[0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[1];
					$wdata[$num][1]=0;
					$wdata[$num][2]=$row2[2];
					$k2++;
					if($k2 > $num2)
						$kla2="9999";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=$row2[0];
					}
				}
			}
			usort($wdata,'comparacion');
			$wtotal1=0;
			$wtotal2=0;
			for ($i=0;$i<=$num;$i++)
				echo"<tr><td>".$wdata[$i][0]."</td><td align=right>".number_format((double)$wdata[$i][1],2,'.',',').$por."</td><td align=right>".number_format((double)$wdata[$i][2],2,'.',',').$por."</td></tr>";
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
