<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Tasas Costos de Apoyo (T87 - T164)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro200.php Ver. 2018-03-15</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function buscalin($AE,$LIN,$LAE)
{
	for ($i=0;$i<=$LAE;$i++)
	{
		if($AE[$i][0] == $LIN)
			return $i;
	}
	return -1;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='000001_pro200.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wcco1) or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>CALCULO DE TASAS COSTOS DE APOYO (T87 - T164)</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
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
		$wemp = substr($wemp,0,2);
		$query = "SELECT Gassub from costosyp_000087  ";
		$query = $query."  where Gasano = ".$wanop;
		$query = $query."    and Gasemp = '".$wemp."'";
		$query = $query."    and Gasmes = ".$wper1;
		$query = $query."    and Gascco = '".$wcco1."'";
		$query = $query."    and Gassub not in (select Dcasub from costosyp_000164 where Dcaemp = Gasemp and Dcacco = Gascco group by 1)";
		$query = $query."   Group by 1 ";
		$query = $query."   Order by 1 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			echo "<table border=1>";
			echo "<tr><td colspan=2 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE TASAS COSTOS DE APOYO (T87 - T164) INCONSISTENCIAS EN INFORMACI&Oacute;N</td></tr>";
			echo "<tr><td align=center colspan=2>A&Ntilde;O : ".$wano." MES : ".$wpre1."</td></tr>";
			echo "<tr><td align=center>CENTRO DE COSTOS</td><td align=center>SUBPROCESO</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<tr><td align=center>".$wcco1."</td><td align=center>".$row[0]."</td></tr>";
			}
			echo "</table>";
		}
		else
		{
			$GA = 0;
			$AE = array();
			$OO = array();
			$GE = array();
			$LAE = -1;
			$LOO = -1;
			$LGE = -1;
			//                 0      1      2      3      4        5
			$query = "SELECT Gassub,Dcacla,Dcatip,Dcalin,Dcapor,sum(Gasval) from costosyp_000087,costosyp_000164  ";
			$query = $query."  where Gasano = ".$wanop;
			$query = $query."    and Gasemp = '".$wemp."'";
			$query = $query."    and Gasmes = ".$wper1;
			$query = $query."    and Gascco = '".$wcco1."'";
			$query = $query."    and Gasemp = Dcaemp ";
			$query = $query."    and Gascco = Dcacco ";
			$query = $query."    and Gassub = Dcasub ";
			$query = $query."   Group by 1,2,3,4,5 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[1] == "A" and $row[2] == "G")
						$GA += $row[5];
					if($row[1] == "A" and $row[2] == "E")
					{
						$pos=buscalin($AE,$row[3],$LAE);
						if($pos != -1)
							$AE[$pos][1] += $row[5] * $row[4] / 100; 
						else
						{
							$LAE++;
							$AE[$LAE][0] = $row[3];
							$AE[$LAE][1] = $row[5] * $row[4] / 100; 
						}
					}
					if($row[1] == "O" and $row[2] == "O")
					{
						$pos=buscalin($OO,$row[3],$LOO);
						if($pos != -1)
							$OO[$pos][1] += $row[5] * $row[4] / 100; 
						else
						{
							$LOO++;
							$OO[$LOO][0] = $row[3];
							$OO[$LOO][1] = $row[5] * $row[4] / 100; 
						}
					}
				}
				$SUMAOO = 0;
				for ($i=0;$i<=$LOO;$i++)
					$SUMAOO += $OO[$i][1];
				for ($i=0;$i<=$LOO;$i++)
					$OO[$i][2] = $OO[$i][1] / $SUMAOO;
				for ($i=0;$i<=$LOO;$i++)
					$OO[$i][3] = $OO[$i][2] * $GA;
				for ($i=0;$i<=$LOO;$i++)
				{
					$pos=buscalin($AE,$OO[$i][0],$LAE);
					if($pos != -1)
					{
						$LGE++;
						$GE[$LGE][0] = $OO[$i][0];
						$GE[$LGE][1] = $OO[$i][3] + $AE[$pos][1];
						$GE[$LGE][2] = 1;
					}
					else
					{
						$LGE++;
						$GE[$LGE][0] = $OO[$i][0];
						$GE[$LGE][1] = $OO[$i][3];
						$GE[$LGE][2] = 1;
					}
				}
				for ($i=0;$i<=$LAE;$i++)
				{
					$pos=buscalin($OO,$AE[$i][0],$LOO);
					if($pos == -1)
					{
						$LGE++;
						$GE[$LGE][0] = $AE[$i][0];
						$GE[$LGE][1] = $AE[$i][1];
						$GE[$LGE][2] = 1;
					}
				}
				for ($i=0;$i<=$LOO;$i++)
				{
					$pos=buscalin($GE,$OO[$i][0],$LGE);
					if($pos != -1)
						$GE[$pos][2] = $GE[$pos][1] / $OO[$i][1];
				}
				$query = "delete from ".$empresa."_000166 ";
				$query = $query."  where Tcaano = ".$wanop;
				$query = $query."    and Tcaemp = '".$wemp."'";
				$query = $query."    and Tcames = ".$wper1;
				$query = $query."    and Tcacco = '".$wcco1."'";
				$err = mysql_query($query,$conex);
				$count = 0;
				//                 0       1  
				$query = "SELECT Dcasub, Dcalin from costosyp_000164  ";
				$query = $query."  where Dcaemp = ".$wemp;
				$query = $query."    and Dcacco = '".$wcco1."'";
				$query = $query."    and Dcacla = 'O' ";
				$query = $query."    and Dcatip = 'O' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$pos=buscalin($GE,$row[1],$LGE);
						if($pos != -1)
							$tasa = $GE[$pos][2];
						else	
							$tasa = 0;
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000166 (medico,fecha_data,hora_data,Tcaemp, Tcaano, Tcames, Tcacco, Tcasub, Tcalin, Tcatas, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$wcco1."','".$row[0]."','".$row[1]."',".$tasa.",'C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						$count++;
						echo "REGISTRO INSERTADO NRO : ".$count."<br>";
					}
					echo "<b>TOTAL REGISTROS INSERTADOS NRO : ".$count."</b><br>";
				}
			}
		}
		
	}
}
?>
</body>
</html>
