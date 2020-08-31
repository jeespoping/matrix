<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Costo Promedio x Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc99.php Ver. 2016-06-02</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='000001_rc99.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or (!isset($wcco1) and !isset($wccof)))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>COSTO PROMEDIO X UNIDAD</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Grupo</td>";
		echo "<td bgcolor=#cccccc align=center>";
		$query = "SELECT Grucod, Grudes   from ".$empresa."_000088 order by Grudes ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wgru'>";
			echo "<option>*-TODOS</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod order by Cc";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wccof'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		else
		{
			$query = "SELECT ccocod,cconom  from ".$empresa."_000005 order by ccocod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wccof'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
		}
		echo "</td></tr>";
		echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
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
		if(isset($wccof))
		{
			$ini=strpos($wccof,"-");
			$wcco1=substr($wccof,0,$ini);
		}
		$ini = strpos($wgru,"-");
		$wgrum=substr($wgru,$ini+1);
		$wgru=substr($wgru,0,$ini);
		$query = "SELECT max(cicmes) as maximo from ".$empresa."_000131  ";
		$query = $query."  where cicano = ".$wanop;
		$query = $query."    and cicemp = '".$wemp."'";
		$query = $query."    and ciccco = ".$wcco1;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$wper1=$row[0];
		if($wper1 > 0)
		{
			$query = " CREATE TEMPORARY TABLE if not exists temp1 as ";
			$query = $query."select  Mprcco,Mprpro, Mprnom, Mprgru, Mprpor from ".$empresa."_000095 ";
			$query = $query."  where Mprcco  = '".$wcco1."'";
			$query = $query."    and Mpremp = '".$wemp."'";
			if($wgru != "*")
				$query = $query."      and Mprgru  = '".$wgru."'";
			$query = $query."      and Mprtip in ('P','C','O') ";
			$err = mysql_query($query,$conex);
			//                    0      1       2       3        4           5         6        
			$query = "select   Mprgru, grudes, Mprpro, Mprnom , Mprpor, sum(Pcapro), sum(Cvapro) from ".$empresa."_000097, temp1, ".$empresa."_000088, ".$empresa."_000082 ";
			$query = $query."  where Pcaano  = ".$wanop;
			$query = $query."    and Pcaemp = '".$wemp."'";
			$query = $query."    and Pcames = ".$wper1;
			$query = $query."    and Pcacco = '".$wcco1."'";
			$query = $query."    and Pcacco = Mprcco ";
			$query = $query."    and Pcacod = Mprpro ";
			$query = $query."    and Pcagru = Mprgru ";
			$query = $query."    and Pcaemp = Gruemp ";
			$query = $query."    and Mprgru = Grucod ";
			$query = $query."    and Cvaano  = ".$wanop;
			$query = $query."    and Cvames = ".$wper1;
			$query = $query."    and Cvacco = '".$wcco1."'";
			$query = $query."    and Pcaemp = Cvaemp ";
			$query = $query."    and Pcacco = Cvacco ";
			$query = $query."    and Pcacod = Cvacod ";
			$query = $query."    and Pcagru = Cvagru ";
			$query = $query."   group by   Mprgru, grudes, Mprpro, Mprnom , Mprpor, Cvapor ";
			$query = $query."   order by  Mprgru, Mprpro , Mprpor ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center>COSTO PROMEDIO X UNIDAD</td></tr>";
			echo "<tr><td colspan=6 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=16 align=center>UNIDAD : ".$wcco1."</td></tr>";
			echo "<tr><td colspan=16 align=center>GRUPO : ".$wgru." - ".$wgrum."</td></tr>";
			echo "<tr><td colspan=16 align=center> A&Ntilde;O : ".$wanop."</td></tr>";
			$wdat=array();
			echo "<tr><td><b>COD. PROCEDIMIENTO</b></td><td><b>NOM. PROCEDIMIENTO</b></td><td><b>% TERCERO</b></td><td><b>COSTO PROMEDIO</b></td><td><b>TMN PROMEDIO</b></td><td><b>COSTO VARIABLE<BR>PROMEDIO</b></td><td><b>TMN VARIABLE</b></td></tr>";
			$seg=-1;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$seg++;
				$wdat[$seg][0]=$row[0];
				$wdat[$seg][1]=$row[1];
				$wdat[$seg][2]=$row[2];
				$wdat[$seg][3]=$row[3];
				$wdat[$seg][4]=$row[4];
				$wdat[$seg][5]=$row[5];
				$wdat[$seg][6]=$row[6];
				$wdat[$seg][7]=$row[5] /( 1 - $row[4]);
				$wdat[$seg][8]=$row[6] /( 1 - $row[4]);
			}
			if($num > 0)
			{
				$wlin="";
				for ($i=0;$i<=$seg;$i++)
				{
					if ($wlin != $wdat[$i][0])
					{
						echo"<tr><td bgcolor=#99CCFF colspan=16>".$wdat[$i][0]."-".$wdat[$i][1]."</td></tr>";
						$wlin = $wdat[$i][0];
					}
					echo"<tr><td>".$wdat[$i][2]."</td><td>".$wdat[$i][3]."</td><td  align=right>".number_format((double)$wdat[$i][4],2,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][5],0,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][7],0,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][6],0,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][8],0,'.',',')."</td></tr>";
				}
				echo "</tr></table>";				
			}
		}
		 else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>PARA ESTE A&Ntilde;O NO HAY COTOS PROMEDIOS CALCULADOS LLAME A COSTOS Y PRESUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
	}
}
?>
</body>
</html>
