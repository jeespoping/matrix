<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Tarifas Comparativas x Unidad x Servicio</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc103.php Ver. 2010-05-12</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc103.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>TARIFAS COMPARATIVAS X UNIDAD X SERVICIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			//                    0       1       2       3       4       5       6       7                    8                     9       10      11     12
			$query = "select   Pcacco, Cconom, Mprgru, grudes, Mprpro, Mprnom , Mprpor, Tarent, sum(Tartap + Tartai + Tartas),sum(Pcapro), Tartap, Tartai, Tartas   from ".$empresa."_000097,".$empresa."_000131,".$empresa."_000095,".$empresa."_000088,".$empresa."_000105,".$empresa."_000005  ";
			$query = $query."    where Pcaano = ".$wanop;
			$query = $query."      and Pcames = ".$wper1;
			$query = $query."      and Pcacco  between '".$wcco1."' and '".$wcco2."'";
			$query = $query."      and Cicano = ".$wanop;
			$query = $query."      and Cicmes = ".$wper1;
			$query = $query."      and Pcacco = Ciccco ";
			$query = $query."      and Pcacco = Mprcco ";
			$query = $query."      and Pcacod = Mprpro ";
			$query = $query."      and Mprtip in ('P','C','O') ";
			$query = $query."      and Pcagru = Mprgru ";
			$query = $query."      and Mprgru = Grucod ";
			$query = $query."      and Tarano = Pcaano";
			$query = $query."      and Pcacco = Tarcco";
			$query = $query."      and Pcacod = Tarpro ";
			$query = $query."      and Pcacco = ccocod ";
			$query = $query."   group by   Pcacco,Cconom,Mprgru, grudes, Mprpro, Mprnom , Mprpor,Tarent,Tartap, Tartai, Tartas ";
			$query = $query."   order by   Pcacco,Mprgru, Mprpro , Mprpor,Tarent ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$query = "select   Tarent, Empdes   from ".$empresa."_000105,".$empresa."_000061  ";
			$query = $query."  where Tarano  = ".$wanop;
			$query = $query."      and Tarcco  between '".$wcco1."' and '".$wcco2."'";
			$query = $query."      and Tarent = Empcin ";
			$query = $query."   group by  Tarent,Empdes ";
			$query = $query."   order by  Tarent ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$Tar=array();
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				$Tar[$i][0]=$row1[0];
				$Tar[$i][1]=$row1[1];
			}
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center>TARIFAS COMPARATIVAS X UNIDAD X SERVICIO</td></tr>";
			echo "<tr><td colspan=16 align=center>UNIDADES : ".$wcco1." - ".$wcco2."</td></tr>";
			echo "<tr><td colspan=16 align=center> A�O : ".$wanop."</td></tr>";
			$wdat=array();
			echo "<tr><td bgcolor=#cccccc><b>CODIGO</b></td><td bgcolor=#cccccc><b>NOM. PROCEDIMIENTO</b></td><td bgcolor=#cccccc align=center><b>%<br> TERCERO</b></td><td bgcolor=#cccccc align=center><b>TMN</b></td>";
			for ($i=0;$i<$num1;$i++)
				echo "<td bgcolor=#cccccc align=center><font size=2><b>".$Tar[$i][0]."<br> ".substr($Tar[$i][1],0,15)."<br>".substr($Tar[$i][1],15,15)."<br>".substr($Tar[$i][1],30,15)."</b></font></td>";
			echo "</tr>";
			$seg=-1;
			$kla = "";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($kla != $row[0].$row[2].$row[4].$row[6])
				 {
					$seg++;
					$wdat[$seg][0]=$row[0];
					$wdat[$seg][1]=$row[1];
					$wdat[$seg][2]=$row[2];
					$wdat[$seg][3]=$row[3];
					$wdat[$seg][4]=$row[4];
					$wdat[$seg][5]=$row[5];
					$wdat[$seg][6]=$row[6];
					$wdat[$seg][7]=$row[9]/(1 - $row[6]);
					for ($j=0;$j<$num1;$j++)
					{
						$wdat[$seg][$j+8]=0;
						$wdat[$seg][$j+8+$num1]=0;
					}
					$kla=$row[0].$row[2].$row[4].$row[6];
				}
				for ($j=0;$j<$num1;$j++)
					if($row[7] == $Tar[$j][0])
					{
						$wdat[$seg][$j+8]+=$row[8];
						$wdat[$seg][$j+$num1+8]=($row[10] * (1 - $row[6])) - $row[9] + $row[11] + $row[12];
					}
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
					$porct=$wdat[$i][6] * 100;
					echo"<tr><td>".$wdat[$i][4]."</td><td>".$wdat[$i][5]."</td><td  align=right>".number_format((double)$porct,0,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][7],0,'.',',')."</td>";
					for ($j=0;$j<$num1;$j++)
						if ($wdat[$i][$j+8+$num1] < 0)
							echo "<td  align=right bgcolor=#FFFF00><font color=#FF0000><b>".number_format((double)$wdat[$i][$j+8],0,'.',',')."</b></font></td>";
						else
							echo "<td  align=right>".number_format((double)$wdat[$i][$j+8],0,'.',',')."</td>";
					echo "</tr>";
				}
				echo "</tr></table>";				
			}
		}
	}
?>
</body>
</html>
