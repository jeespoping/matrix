<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Margen de Contribucion x Unidad x Entidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc104.php Ver. 2010-05-12</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc104.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>MARGEN DE CONTRIBUCION X UNIDAD X ENTIDAD</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Entidad</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT Empcin, Empdes  from ".$empresa."_000061  WHERE Emptar='S' group by empcin order by Empdes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wemp'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."_".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			}
			else
			{
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
				echo "</td></tr>";
			}
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";	
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$ini = strpos($wemp,"_");
			$wempm=substr($wemp,$ini+1);
			$wemp=substr($wemp,0,$ini);
			$query = "select  max(Mes) as maximo from ".$empresa."_000048 ";
			$query = $query."  where Ano  = ".$wanop;
			$query = $query."      and Cierre_costos = 'on' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$wper1=$row[0];
			if($wper1 > 0)
			{
				$query = " CREATE TEMPORARY TABLE if not exists temp1 as ";
				$query = $query."select  Mprcco, Mprpro, Mprnom, Mprgru, Mprpor from ".$empresa."_000095 ";
				$query = $query."  where Mprcco   between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and Mprtip in ('P','C','O') ";
				$err = mysql_query($query,$conex);
				$query = "select   Cvacco, Cconom, Mprgru, grudes, Mprpro, Mprnom , Mprpor, Tartap, Tartai, Tartas, sum(Cvapro) from ".$empresa."_000082, temp1, ".$empresa."_000088, ".$empresa."_000105, ".$empresa."_000005  ";
				$query = $query."  where Cvaano  = ".$wanop;
				$query = $query."      and Cvames = ".$wper1;
				$query = $query."      and Cvacco  between '".$wcco1."' and '".$wcco2."'";
				$query = $query."      and Cvacco = Mprcco ";
				$query = $query."      and Cvacod = Mprpro ";
				$query = $query."      and Cvagru = Mprgru ";
				$query = $query."      and Mprgru = Grucod ";
				$query = $query."      and Tarano =  ".$wanop;
				$query = $query."      and Cvacco =  Tarcco";
				$query = $query."      and Cvacod = Tarpro ";
				$query = $query."      and Tarent = '".$wemp."'";
				$query = $query."      and Cvacco = ccocod ";
				$query = $query."   group by  Cvacco, Cconom, Mprgru, grudes, Mprpro, Mprnom , Mprpor, Tartap, Tartai, Tartas ";
				$query = $query."   order by  Cvacco, Mprgru, Mprpro, Mprpor ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				echo "<table border=1>";
				echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=16 align=center>MARGEN DE CONTRIBUCION X UNIDAD X ENTIDAD</td></tr>";
				echo "<tr><td colspan=16 align=center>ENTIDAD : ".$wemp." - ".$wempm."</td></tr>";
				echo "<tr><td colspan=16 align=center> A�O : ".$wanop."</td></tr>";
				$wdat=array();
				echo "<tr><td bgcolor=#cccccc><b>CODIGO</b></td><td bgcolor=#cccccc><b>NOM. PROCEDIMIENTO</b></td><td bgcolor=#cccccc align=center><font size=2><b>TARIFA</b></font></td><td bgcolor=#cccccc align=center><font size=2><b>%<br> TERCERO</b></font></td><td bgcolor=#cccccc align=center><font size=2><b>COSTO<br> PROMEDIO<BR> VARIABLE</b></font></td><td bgcolor=#cccccc align=center><font size=2><b>MARGEN <BR> CONTRIBUCION</b></font></td><td bgcolor=#cccccc align=center><font size=2><b> % MARGEN <br>DE CONTRIBUCION</b></font></td></tr>";
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
					$wdat[$seg][7]=$row[7];
					$wdat[$seg][8]=$row[10];
					$wdat[$seg][9]=($row[7] * (1 - $row[6])) - $row[10] + $row[8] + $row[9];
					$wdat[$seg][10]=$wdat[$seg][9] / ($row[7] + $row[8] + $row[9]) * 100;
				}
				if($num > 0)
				{
					$wcco="";
					$wlin="";
					for ($i=0;$i<=$seg;$i++)
					{
						if ($wcco != $wdat[$i][0])
						{
							echo"<tr><td bgcolor=#FFCC66 colspan=16>".$wdat[$i][0]."-".$wdat[$i][1]."</td></tr>";
							$wcco = $wdat[$i][0];
						}
						if ($wlin != $wdat[$i][2])
						{
							echo"<tr><td bgcolor=#99CCFF colspan=16>".$wdat[$i][2]."-".$wdat[$i][3]."</td></tr>";
							$wlin = $wdat[$i][2];
						}
						if($wdat[$i][9] < 0)
							echo"<tr><td>".$wdat[$i][4]."</td><td>".$wdat[$i][5]."</td><td  align=right>".number_format((double)$wdat[$i][7],0,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][6]*100,2,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][8],0,'.',',')."</td><td  align=right bgcolor=#FFFF00><font color=#FF0000><b>".number_format((double)$wdat[$i][9],0,'.',',')."</b></font></td><td  align=right>".number_format((double)$wdat[$i][10],2,'.',',')."%</td></tr>";
						else
							echo"<tr><td>".$wdat[$i][4]."</td><td>".$wdat[$i][5]."</td><td  align=right>".number_format((double)$wdat[$i][7],0,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][6]*100,2,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][8],0,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][9],0,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][10],2,'.',',')."%</td></tr>";
					}
					echo "</tr></table>";				
				}
			}
			 else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>PARA ESTE A�O NO HAY COTOS PROMEDIOS CALCULADOS LLAME A COSTOS Y PRESUPUESTOS</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
