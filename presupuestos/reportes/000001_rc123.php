<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Procedimientos Comparativos Entre A&ntilde;os x C.C. Mes</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc123.php Ver. 2016-03-08</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc123.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wano2) or !isset($wcco1)  or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROCEDIMIENTOS COMPARATIVOS ENTRE A&Ntilde;OS X C.C. MES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			$query  = "select Morcco, Cconom, Morcod, Prodes, Morano, Mormes, Morcan from ".$empresa."_000032, ".$empresa."_000005, ".$empresa."_000059 ";
			$query .= "  where Morano between '".$wano1."' and '".$wano2."'";
			$query .= "    and Morcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and moremp = '".$wemp."' ";
			$query .= "    and Morcco = Ccocod";
			$query .= "    and moremp = ccoemp ";
			$query .= "    and Morcod = Procod";
			$query .= "    and moremp = proemp ";
			$query .= "  order by 1,3,5,6 ";
			$err = mysql_query($query,$conex)or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			echo "<table border=0 align=center>";
			echo "<tr><td colspan=15 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=15 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=15 align=center>PROCEDIMIENTOS COMPARATIVOS ENTRE A&Ntilde;OS X C.C. MES</td></tr>";
			echo "<tr><td colspan=15 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=15 align=center>A&Ntilde;O INICIAL : ".$wano1. " A&Ntilde;O FINAL : ".$wano2."</td></tr>";
			echo "<tr><td colspan=15 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>A&Ntilde;O</b></td><td bgcolor=#cccccc><b>ENE</b></td><td bgcolor=#cccccc><b>FEB</b></td><td bgcolor=#cccccc><b>MAR</b></td><td bgcolor=#cccccc><b>ABR</b></td><td bgcolor=#cccccc><b>MAY</b></td><td bgcolor=#cccccc><b>JUN</b></td><td bgcolor=#cccccc><b>JUL</b></td><td bgcolor=#cccccc><b>AGO</b></td><td bgcolor=#cccccc><b>SEP</b></td><td bgcolor=#cccccc><b>OCT</b></td><td bgcolor=#cccccc><b>NOV</b></td><td bgcolor=#cccccc><b>DIC</b></td><td bgcolor=#cccccc><b>TOTAL</b></td><td bgcolor=#cccccc><b>PROMEDIO</b></td></tr>";
			$wtotal=0;
			$woccur=0;
			$wccoa="";
			$wlina="";
			$wanoa=0;
			$woccco=0;
			$wl=0;
			$wtotcco = array();
			for ($j=1;$j<=12;$j++)
				$wtotcco[$j]=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[4] != $wanoa or $row[0]."-".$row[1] != $wccoa or $row[2]."-".$row[3] != $wlina)
				{
					if($i != 0)
					{
						$wpor=$wtotal / $woccur;
						if($wl % 2 == 0)
							$color="#dddddd";
						else
							$color="#ffffff";
						$wl++;
						echo "<tr><td bgcolor=".$color.">".$wanoa."</td>";
						for ($j=1;$j<=12;$j++)
							echo "<td bgcolor=".$color." align=right>".number_format((double)$wdat[$j],0,'.',',')."</td>"; 
						echo "<td bgcolor=".$color." align=right>".number_format((double)$wtotal,0,'.',',')."</td>"; 
						echo "<td bgcolor=".$color." align=right>".number_format((double)$wpor,0,'.',',')."</td></tr>"; 
					}
					for ($j=1;$j<=12;$j++)
						$wdat[$j]=0;
					$wtotal=0;
					$woccur=0;
					$wanoa=$row[4];
				}
			
				if($row[0]."-".$row[1] != $wccoa)
				{
					if($i != 0)
					{
						$wtotCO=0;
						$woccco=0;
						echo "<tr><td bgcolor=#F2F5A9><b>TOTAL ".$wccoa."</b></td>";
						for ($j=1;$j<=12;$j++)
						{
							$wtotCO += $wtotcco[$j];
							if($wtotcco[$j] > 0)
								$woccco += 1;
							echo "<td bgcolor=#F2F5A9 align=right><b>".number_format((double)$wtotcco[$j],0,'.',',')."</b></td>";
						}
						if($woccco > 0)
							$wpor=$wtotCO / $woccco;
						else
							$wpor=0;
						echo "<td bgcolor=#F2F5A9 align=right><b>".number_format((double)$wtotCO,0,'.',',')."</b></td>"; 
						echo "<td bgcolor=#F2F5A9 align=right><b>".number_format((double)$wpor,0,'.',',')."</b></td></tr>";
					}
					$woccco=0;
					for ($j=1;$j<=12;$j++)
						$wtotcco[$j]=0;
					$wccoa = $row[0]."-".$row[1];
					echo "<tr><td bgcolor=#FFCC66 colspan=15><b>UNIDAD : ".$wccoa."</b></td></tr>";
					$wlina = $row[2]."-".$row[3];
					echo "<tr><td bgcolor=#99CCFF colspan=15><b>&nbsp&nbspLINEA : ".$wlina."</b></td></tr>";
				}
				if($row[2]."-".$row[3] != $wlina)
				{
					$wlina = $row[2]."-".$row[3];
					echo "<tr><td bgcolor=#99CCFF colspan=15><b>&nbsp&nbspLINEA : ".$wlina."</b></td></tr>";
				}
				$wdat[$row[5]] = $row[6];
				$wtotcco[$row[5]] += $row[6];
				$wtotal += $row[6];
				$woccur += 1;
			}
			$wpor=$wtotal / $woccur;
			echo "<tr><td>".$wanoa."</td>";
			for ($j=1;$j<=12;$j++)
				echo "<td align=right>".number_format((double)$wdat[$j],0,'.',',')."</td>"; 
			echo "<td align=right>".number_format((double)$wtotal,0,'.',',')."</td>"; 
			echo "<td align=right>".number_format((double)$wpor,0,'.',',')."</td></tr>";
			echo "<tr><td bgcolor=#F2F5A9><b>TOTAL ".$wccoa."</b></td>";
			for ($j=1;$j<=12;$j++)
			{
				$wtotCO += $wtotcco[$j];
				if($wtotcco[$j] > 0)
					$woccco += 1;
				echo "<td bgcolor=#F2F5A9 align=right><b>".number_format((double)$wtotcco[$j],0,'.',',')."</b></td>";
			}
			$wpor=$wtotCO / $woccco;
			echo "<td bgcolor=#F2F5A9 align=right><b>".number_format((double)$wtotCO,0,'.',',')."</b></td>"; 
			echo "<td bgcolor=#F2F5A9 align=right><b>".number_format((double)$wpor,0,'.',',')."</b></td></tr>";
		}
	}
?>
</body>
</html>
