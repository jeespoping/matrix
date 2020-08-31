<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Obligaciones Financieras Nuevas x A&ntilde;o x Tipo</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc136.php Ver. 2016-03-08</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc136.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanof) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "P" and strtoupper ($wres) != "T"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>OBLIGACIONES FINANCIERAS NUEVAS X A&Ntilde;O X TIPO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Informacion (R - Real / P - Presupuestada / T - Todas)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
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
			//                   0       1         2  
			$query  = "select Moftob,Mofani,sum(Mofmon) from ".$empresa."_000132 ";
			$query .= "where mofani between ".$wanoi." and ".$wanof;
			$query .= "  and mofemp = '".$wemp."' ";
			if(strtoupper($wres) != "T")
				$query .= "  AND Moftip = '".$wres."' ";
			$query .= "  AND Moftob != 'RP' ";
			$query .= "  AND Mofest = 'on' ";
			$query .= " group by 1,2 ";
			$query .= " order by 1,2 ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$tot=array();
				$tit1["CR"]="CREDITOS";
				$tit1["LG"]="LEASING";
				$tit1["FG"]="FACTORING";
				$tit1["CT"]="CREDITOS DE TESORERIA";
				$tit1["LI"]="LEASING INMOBILIARIO";
				$tit1["AP"]="ACCIONES PRIVILEGIADAS";
				for ($i=$wanoi;$i<=$wanof;$i++)
				{
					$data[$i]=0;
					$tot[$i]=0;
				}
				$ncol = $wanof - $wanoi + 2;
				echo "<table border=0 align=center>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2><font size=3>DIRECCION DE INFORMATICA</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>OBLIGACIONES FINANCIERAS NUEVAS X A&Ntilde;O X TIPO</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>DESDE EL A&Ntilde;O ".$wanoi." HASTA EL A&Ntilde;O ".$wanof."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>TIPO DE CONSULTA ".$wres."</font></td></tr>";
				echo "<tr><td bgcolor=#cccccc><font size=2><b>TIPO<BR>OBLIGACION</b></font></td>";
				for ($i=$wanoi;$i<=$wanof;$i++)
					echo "<td bgcolor=#cccccc align=center><font size=2><b>".$i."</b></font></td>";
				echo "</tr>";
				$wobl="";
				$ncolor=0;
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0] != $wobl)
					{
						if($j != 0)
						{
							$ncolor++;
							if($ncolor % 2 == 0)
								$color="#FFFFFF";
							else
								$color="#99CCFF";
							echo "<tr><td bgcolor=".$color."><font size=2>".$tit1[$wobl]."</font></td>";
							for ($i=$wanoi;$i<=$wanof;$i++)
								echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i],0,'.',',')."</font></td>";
							echo "</tr>";
						}
						$wobl = $row[0];
						for ($i=$wanoi;$i<=$wanof;$i++)
							$data[$i]=0;
					}
					$data[$row[1]] += $row[2];
					$tot[$row[1]] += $row[2];
				}
				$ncolor++;
				if($ncolor % 2 == 0)
					$color="#FFFFFF";
				else
					$color="#99CCFF";
				echo "<tr><td bgcolor=".$color."><font size=2>".$tit1[$wobl]."</font></td>";
				for ($i=$wanoi;$i<=$wanof;$i++)
					echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$i],0,'.',',')."</font></td>";
				echo "</tr>";
				$ncolor++;
				if($ncolor % 2 == 0)
					$color="#FFFFFF";
				else
					$color="#99CCFF";
				echo "<tr><td bgcolor=#FFCC66><font size=2><b>TOTALES GENERALES</b></font></td>";
				for ($i=$wanoi;$i<=$wanof;$i++)
					echo "<td bgcolor=#FFCC66 align=right><font size=2>".number_format((double)$tot[$i],0,'.',',')."</font></td>";
				echo "</tr>";
				echo "</table>";
			}
		}
	}
?>
</body>
</html>
