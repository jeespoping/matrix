<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Costos de Nomina x Subproceso</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc79.php Ver. 2018-03-27</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc79.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanop) or !isset($wcco1) or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>COSTOS DE NOMINA X SUBPROCESO</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Final</td>";
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
			for ($wcco=$wcco1;$wcco<=$wcco2;$wcco++)
			{
				while(strlen($wcco) < 4)
					$wcco = "0".$wcco;
				$query = "select count(*)  from ".$empresa."_000005 where ccocod='".$wcco."' and ccoest='on' and ccocos='S' and ccoemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				if($row[0] > 0)
				{
					echo "<center><table border=1>";
					echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
					echo "<tr><td align=center colspan=6><b>APLICACION DE COSTOS</b></td></tr>";
					echo "<tr><td align=center colspan=6><b>COSTOS DE NOMINA X SUBPROCESO</b></td></tr>";
					echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
					echo "<tr><td colspan=6 align=center>UNIDAD  : ".$wcco."</td></tr>";
					echo "<tr><td colspan=6 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
					echo "<tr><td><b>CODIGO</b></td><td><b>SUBPROCESO</b></td><td><b>PORCENTAJE</b></td><td><b>COSTO</b></td></tr>";
					$query = "SELECT  Mnoofi,carnom, sum(Mnopag)  from ".$empresa."_000094,".$empresa."_000004 ";
					$query = $query."  where Mnoano = ".$wanop;
					$query = $query."    and Mnoemp = '".$wemp."' ";
					$query = $query."    and Mnomes = ".$wper1;
					$query = $query."    and Mnocco = '".$wcco."'";
					$query = $query."    and Mnoofi= Carcod";
					$query = $query."    and Mnoemp= Caremp";
					$query = $query."  group by  Mnoofi,carnom";
					$query = $query."  order by Mnoofi";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$key="";
					$wtotct=0;
					$wtotpp=0;
					$wtotcp=0;
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($key != $row[0])
						{
							if($i != 0)
							{
								echo "<tr>";
								echo "<td colspan=2 bgcolor=#cccccc> TOTAL OFICIO</td>";
								echo "<td align=right bgcolor=#cccccc>".number_format($wtotpp,2,'.',',')."</td>";
								echo "<td align=right bgcolor=#cccccc>".number_format($wtotcp,0,'.',',')."</td></tr>";
							}
							$key = $row[0];
							echo "<tr><td colspan=4 align=center bgcolor=#99CCFF>".$row[0]."-".$row[1]."</td></tr>";
							$wtotpp=0;
							$wtotcp=0;
						}
						$query = "SELECT    Pdisub, Subdes, Pdipor, Pditip, '', 1, Subdes  from ".$empresa."_000098,".$empresa."_000104 ";
						$query = $query."  where Pdiano = ".$wanop;
						$query = $query."    and Pdiemp = '".$wemp."' ";
						$query = $query."    and Pdimes = ".$wper1;
						$query = $query."    and Pdicco = '".$wcco."'";
						$query = $query."    and Pdiofi = '".$row[0]."'";
						$query = $query."    and Pdisub = Subcod ";
						$query = $query."    and Pditip = 'S' ";
						$query = $query."	UNION ALL "; 
						$query = $query." SELECT   Pdisub, Subdes, Pdipor, Pditip, Mdrsub, Mdrpor, Subdes  from ".$empresa."_000098,".$empresa."_000091,".$empresa."_000104  ";
						$query = $query."  where Pdiano = ".$wanop;
						$query = $query."    and Pdiemp = '".$wemp."' ";
						$query = $query."    and Pdimes = ".$wper1;
						$query = $query."    and Pdicco = '".$wcco."'";
						$query = $query."    and Pdiofi = '".$row[0]."'";
						$query = $query."    and Pditip = 'D' ";
						$query = $query."    and Pdiemp = Mdremp ";
						$query = $query."    and Pdiano = Mdrano ";
						$query = $query."    and Pdimes = Mdrmes ";
						$query = $query."    and Pdicco = Mdrcco ";
						$query = $query."    and Pdisub = Mdrcod ";
						$query = $query."    and Mdrsub = Subcod ";
						$query = $query."   order by Pdisub ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							/*
							if($row1[3] == "D")
							{
								$query = " SELECT  Mdrsub, Mdrpor   from ".$empresa."_000091 ";
								$query = $query."  where Mdrano = ".$wanop;
								$query = $query."    and Mdremp = '".$wemp."'";
								$query = $query."    and Mdrmes = ".$wper1;
								$query = $query."    and Mdrcco = '".$wcco."'";
								$query = $query."    and Mdrcod = '".$row1[0]."'";
								$query = $query."   group by 1";
								$err2 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
								$num2 = mysql_num_rows($err2);
							}
							else
								$num2 = 0;
							
							if($num2 == 0)
							*/
							if($row1[3] != "D")
							{
								$wtotpp+=$row1[2]*100;
								$wtotcp+=$row[2] * $row1[2];
								$wtotct+=$row[2] * $row1[2];
								$wtot=$row[2] * $row1[2];
								$wpor=$row1[2]*100;
								$row1[1] = $row1[6];
							}
							else
							{
								//$row2 = mysql_fetch_array($err2);
								$row1[0] = $row1[4];
								$wtotpp+=$row1[2] * 100 * $row1[5];
								$wtotcp+=$row[2] * $row1[2] * $row1[5];
								$wtotct+=$row[2] * $row1[2] * $row1[5];
								$wtot=$row[2] * $row1[2] * $row1[5];
								$wpor=$row1[2]*100* $row1[5];
								$row1[1] = $row1[6];
							}
							echo "<tr>";
							echo "<td>".$row1[0]."</td>";
							echo "<td>".$row1[1]."</td>";
							echo "<td align=right>".number_format($wpor,2,'.',',')."</td>";
							echo "<td align=right>".number_format($wtot,0,'.',',')."</td></tr>";
						}
					}
					echo "<tr>";
					echo "<td colspan=2 bgcolor=#cccccc> TOTAL OFICIO</td>";
					echo "<td align=right bgcolor=#cccccc>".number_format($wtotpp,2,'.',',')."</td>";
					echo "<td align=right bgcolor=#cccccc>".number_format($wtotcp,0,'.',',')."</td></tr>";
					echo "<tr>";
					echo "<td colspan=3 bgcolor=#FFCC66> TOTAL GENERAL</td>";
					echo "<td align=right bgcolor=#FFCC66>".number_format($wtotct,0,'.',',')."</td></tr>";
				}
			}
		}
}
?>
</body>
</html>
