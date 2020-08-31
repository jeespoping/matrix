<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo de Costo Promedio de Insumos (T93)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro167.php Ver. 2015-12-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro167.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wano1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmes1) or $wmes1 < 1 or $wmes1 > 12 or !isset($wano2) or !isset($wmes2) or $wmes2 < 1 or $wmes2 > 12 or !isset($wano3) or !isset($wmes3) or $wmes3 < 1 or $wmes3 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>CALCULO DE COSTO PROMEDIO DE INSUMOS (T93)</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano1' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano2' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes2' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Grabaci&oacute;n</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano3' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Grabaci&oacute;n</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes3' size=2 maxlength=2></td></tr>";
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
		$DATA=array();
		$query  = "select Mincod from ".$empresa."_000093 ";
		$query .= "  where minano = ".$wano3;
		$query .= "    and minmes = ".$wmes3;
		$query .="     and minemp = '".$wemp."' ";
		$query .= " Group by 1 ";
		$query .= " Order by 1 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				$DATA[$i][0] = $row2[0];
			}
		}
		
		$ESTERIL=array();
		$query  = "select Usicod, sum(Pcapro) from ".$empresa."_000093,".$empresa."_000152,".$empresa."_000097 ";
		$query .= "   where minano between ".$wano1." and ".$wano2;
		$query .="      and minemp = '".$wemp."' ";
		$query .= " 	and ((minmes between 1 and 12 and minano != ".$wano1." and minano != ".$wano2.") ";
		$query .= " 	   or (minmes >= ".$wmes1." and minano = ".$wano1." and ".$wano1." != ".$wano2.") ";
		$query .= " 	   or (minmes <= ".$wmes2." and minano = ".$wano2." and ".$wano1." != ".$wano2.") "; 
		$query .= " 	   or (minmes between ".$wmes1." and ".$wmes2." and ".$wano1." = ".$wano2.")) ";
		$query .= " 	and Mincod = Usicod ";
		$query .= " 	and Minemp = Usiemp ";
		$query .= " 	and Usiemp = Pcaemp ";
		$query .= " 	and Usicoe = Pcacod ";
		$query .= " 	and Minano = Pcaano ";
		$query .= " 	and Minmes = Pcames ";
		$query .= " 	and Usicco = Pcacco ";
		$query .= " 	and Usigru = Pcagru ";
		$query .= " 	and Usicon = Pcacon ";
		$query .= "   group by 1 "; 
		$query .= "   order by 1 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				$ESTERIL[$i][0] = $row2[0];
				$ESTERIL[$i][1] = $row2[1];
			}
		}
		$numest = $num2;
		
		$REUSO=array();
		$query  = "select usicor,sum(Mincpr) from ".$empresa."_000093,".$empresa."_000152 ";
		$query .= "   where minano between ".$wano1." and ".$wano2;
		$query .="      and minemp = '".$wemp."' ";
		$query .= " 	and ((minmes between 1 and 12 and minano != ".$wano1." and minano != ".$wano2.") ";
		$query .= " 	   or (minmes >= ".$wmes1." and minano = ".$wano1." and ".$wano1." != ".$wano2.") ";
		$query .= " 	   or (minmes <= ".$wmes2." and minano = ".$wano2." and ".$wano1." != ".$wano2.") "; 
		$query .= " 	   or (minmes between ".$wmes1." and ".$wmes2." and ".$wano1." = ".$wano2.")) ";
		$query .= " 	and Mincod = usicod ";
		$query .= " 	and Minemp = Usiemp ";
		$query .= "   group by 1 "; 
		$query .= "   order by 1 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				$REUSO[$i][0] = $row2[0];
				$REUSO[$i][1] = $row2[1];
			}
		}
		$numreu = $num2;
		
		$NUSOS=array();
		$query  = "select Inscod,Insuso from ".$empresa."_000089 ";
		$query .="    where Insemp = '".$wemp."' ";
		$query .= "   order by 1 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				$NUSOS[$i][0] = $row2[0];
				$NUSOS[$i][1] = $row2[1];
			}
		}
		$numnuso = $num2;
		
		$k=0;
		$query  = "select Mincod,count(*),sum(Mincpr) from ".$empresa."_000093 ";
		$query .= "  where minano between ".$wano1." and ".$wano2;
		$query .="     and minemp = '".$wemp."' ";
		$query .= "    and ((minmes between 1 and 12 and minano != ".$wano1." and minano != ".$wano2.") ";
		$query .= "	   or (minmes >= ".$wmes1." and minano = ".$wano1." and ".$wano1." != ".$wano2.") ";
		$query .= "	   or (minmes <= ".$wmes2." and minano = ".$wano2." and ".$wano1." != ".$wano2.") ";
		$query .= "	   or (minmes between ".$wmes1." and ".$wmes2." and ".$wano1." = ".$wano2.")) ";
		$query .= "  group by 1 ";
		$query .= "  order by 1 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$pos=bi($ESTERIL,$numest,$row[0]);
				if($pos != -1)
					$wvalest = $ESTERIL[$pos][1];
				else
					$wvalest = 0;
					
				$pos=bi($REUSO,$numreu,$row[0]);
				if($pos != -1)
					$row[2] = $REUSO[$pos][1];
				
				$pos=bi($NUSOS,$numnuso,$row[0]);
				if($pos != -1)
					$wnusos = $NUSOS[$pos][1];
				else
					$wnusos = 1;
				
				$prom = ($row[2] / $row[1]) / $wnusos + ($wvalest / $row[1]);
				
				$pos=bi($DATA,$num2,$row[0]);
				if($pos == -1)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000093 (medico,fecha_data,hora_data,Minemp, Minano, Minmes, Mincod, Mincpr, Minpro, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wano3.",".$wmes3.",'".$row[0]."',".$prom.",".$prom.",'C-".$empresa."')";
					$err1 = mysql_query($query,$conex); //or die("Error en la Insercion del Movimiento del Insumo ".mysql_errno().":".mysql_error());
					if($err != 1)
					{
						$query = "update ".$empresa."_000093 set Minpro = ".$prom." where minano=".$wano3." and minmes=".$wmes3." and Mincod='".$row[0]."' and Minemp='".$wemp."' ";
						$err1 = mysql_query($query,$conex);
						$k++;
						echo "REGISTROS ACTUALIZADO : ".$k."<BR>";
					}
					else
					{
						$k++;
						echo "REGISTROS INSERTADO : ".$k."<BR>";
					}
				}
				else
				{
					$query = "update ".$empresa."_000093 set Minpro = ".$prom." where minano=".$wano3." and minmes=".$wmes3." and Mincod='".$row[0]."' and Minemp='".$wemp."'  ";
					$err1 = mysql_query($query,$conex);
					$k++;
					echo "REGISTROS ACTUALIZADO : ".$k."<BR>";
				}
			}
		}
		echo "<B>REGISTROS INSERTADOS O ACTUALIZADOS : ".$k."</B><BR>";
	}
}
?>
</body>
</html>
