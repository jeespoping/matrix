<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Indicadores Por Unidades de Negocio</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc15.php Ver. 2016-03-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc15.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wgru) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE INDICADORES POR UNIDADES DE NEGOCIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper1;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			$wgru1=$wgru;
			$ini=strpos($wgru,"-");
			$wgru=substr($wgru,0,$ini);
			$wgru1=substr($wgru1,$ini+1,strlen($wgru1));
			if($wgru != "MENS")
				$wper1=13;
			$query = "SELECT indcod,inddes from ".$empresa."_000015  order by indcod ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$span=$num1+1;
			$query = "SELECT infcco,cconom,infcod,infmon from ".$empresa."_000016,".$empresa."_000005 ";
			$query = $query."  where infano = ".$wanop;
			$query = $query."    and infemp = '".$wemp."' ";
			$query = $query."    and infmes = ".$wper1;
			$query = $query."    and infgru = '".$wgru."'";
			$query = $query."    and infcco = ccocod";
			$query = $query."    and infemp = ccoemp";
			$query = $query."   order by infcco,infcod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=".$span." align=center><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
			echo "<tr><td colspan=".$span." align=center><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
			echo "<tr><td colspan=".$span." align=center><font size=2>INFORME DE INDICADORES POR UNIDADES DE NEGOCIO</font></td></tr>";
			echo "<tr><td colspan=".$span." align=center><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td colspan=".$span." align=center><font size=2>AGRUPACION  :<b> ".$wgru1."</b> PERIODO  :<b> ".$wper1."</b> A&Ntilde;O :<b> ".$wanop."</b></font></td></tr>";
			echo "<tr><td><font size=2><b>UNIDAD</b></td>";
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				echo "<td align=center><font size=2><b>".$row1[0]."</b></font></td>";
			}
			echo "</tr>";
			$cco="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0] != $cco)
				{
					$cco=$row[0];
					if($i == 0)
						echo "<tr><td><font size=2>".$row[1]."</font></td>";
					else
						echo "</tr><tr><td><font size=2>".$row[1]."</font></td>";
				}
				if ($row[2] == "RCA" or $row[2] == "RID")
					echo"<td align=right><font size=2>".number_format((double)$row[3],2,'.',',')." D</font></td>";
				else
					echo"<td align=right><font size=2>".number_format((double)$row[3],2,'.',',')." %</font></td>";
    		}
    		echo "</table><table border=0>";
    		$query = "SELECT indcod,inddes from ".$empresa."_000015  order by indcod ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
    		for ($i=0;$i<$num1;$i++)
			{
				if(($i % 2) == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				$row1 = mysql_fetch_array($err1);
				echo "<tr><td bgcolor=".$color."><font size=2><b>".$row1[0]." = ".$row1[1]."</b></font></td></tr>";
			}
			echo "</table>";
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
