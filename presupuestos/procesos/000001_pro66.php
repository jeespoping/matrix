<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo Costo x Actividad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro66.php Ver. 2017-01-19</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function actualizarreg(&$REG,$cco,$sub,$num)
{
	
	for ($i=0;$i<$num;$i++)
	{
		if($REG[$i][0] == $cco and $REG[$i][1] == $sub)
		{
			$REG[$i][2] = 1;
		}
	}
}
function busqueda($d,$n,$a,$m,$c,$s)
{
	$p="on";
	for ($i=0;$i<$n;$i++)
	{
		if($d[$i][0] == $a and $d[$i][1] == $m and $d[$i][2] == $c and $d[$i][3] == $s)
			return $d[$i][4];
	}
	return $p;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro66.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wcco1) or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO COSTO X ACTIVIDAD</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			$query = "SELECT ciccco from ".$empresa."_000131  ";
			$query = $query."  where cicano = ".$wanop;
			$query = $query."    and cicemp = '".$wemp."'";
			$query = $query."    and cicmes = ".$wper1;
			$query = $query."    and ciccco between '".$wcco1."' and '".$wcco2."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num == 0)
			{
				$DATA=array();
				$query = "SELECT Cxaano, Cxames, Cxacco, Cxasub, Cxaest from ".$empresa."_000083 ";
				$query = $query."  where Cxaano = ".$wanop;
				$query = $query."    and Cxaemp = '".$wemp."'";
				$query = $query."    and Cxames = ".$wper1;
				$query = $query."    and Cxacco between '".$wcco1."' and '".$wcco2."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$DATA[$i][0] = $row[0];
						$DATA[$i][1] = $row[1];
						$DATA[$i][2] = $row[2];
						$DATA[$i][3] = $row[3];
						$DATA[$i][4] = $row[4];
					}
				}
				$ext=$num;
				$query = "delete from ".$empresa."_000083 ";
				$query = $query."  where Cxaano = ".$wanop;
				$query = $query."    and Cxaemp = '".$wemp."'";
				$query = $query."    and Cxames = ".$wper1;
				$query = $query."    and Cxacco between '".$wcco1."' and '".$wcco2."'";
				$err = mysql_query($query,$conex);
				echo "<table border=1>";
				echo "<tr><td colspan=2 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=2 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=2 align=center>ERRORES EN CALCULO COSTO X ACTIVIDAD</td></tr>";
				echo "<tr><td colspan=2 align=center>UNIDAD  :<b> ".$wcco1. "</b></td></tr>";
				echo "<tr><td colspan=2 align=center>EMPRESA  :<b> ".$wempt. "</b></td></tr>";
				echo "<tr><td colspan=2 align=center>PERIODO  : ".$wper1." A&Ntilde;O : ".$wanop."</td></tr>";
				$REG=array();
				$query = "SELECT Mdacco, Mdasub  from ".$empresa."_000090 ";
				$query = $query."  where Mdaano = ".$wanop;
				$query = $query."    and Mdaemp = '".$wemp."'";
				$query = $query."    and Mdames = ".$wper1;
				$query = $query."    and Mdacco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."   group by Mdacco, Mdasub  ";
				$query = $query."   order by Mdacco, Mdasub  ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$numreg=$num;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$REG[$i][0] = trim($row[0]);
					$REG[$i][1] = trim($row[1]);
					$REG[$i][2] = 0;
				}
				$count=0;
				$query = "SELECT Gascco,Gassub,sum(Gasval) from ".$empresa."_000087 ";
				$query = $query."  where Gasano = ".$wanop;
				$query = $query."    and Gasemp = '".$wemp."'";
				$query = $query."    and Gasmes = ".$wper1;
				$query = $query."    and Gascco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."   group by Gascco,Gassub ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					actualizarreg($REG,trim($row[0]),trim($row[1]),$numreg);
					$query = "SELECT Mdacan from ".$empresa."_000090 ";
					$query = $query."  where Mdaano = ".$wanop;
					$query = $query."    and Mdaemp = '".$wemp."'";
					$query = $query."    and Mdames = ".$wper1;
					$query = $query."    and Mdacco = '".$row[0]."'";
					$query = $query."   and Mdasub = '".$row[1]."'";
					$err2 = mysql_query($query,$conex);
					$num2 = mysql_num_rows($err2);
					if($num2 > 0)
					{
						for ($j=0;$j<$num2;$j++)
						{
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$row2 = mysql_fetch_array($err2);
							if($row2[0] != 0)
							{
								$wpor= $row[2] / $row2[0];
								$pos=busqueda($DATA,$ext,$wanop,$wper1,$row[0],$row[1]);
								$query = "insert ".$empresa."_000083 (medico,fecha_data,hora_data,Cxaemp, Cxaano, Cxames, Cxacco, Cxasub, Cxacos, Cxaest, Cxapro, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."',".$wpor.",'".$pos."',0,'C-".$empresa."')";
								$err1 = mysql_query($query,$conex);
								if ($err1 != 1)
									echo mysql_errno().":".mysql_error()."<br>";
								$count++;
								echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
							}
							else
							{
								$wpor= 0;
								$pos=busqueda($DATA,$ext,$wanop,$wper1,$row[0],$row[1]);
								$query = "insert ".$empresa."_000083 (medico,fecha_data,hora_data,Cxaemp, Cxaano, Cxames, Cxacco, Cxasub, Cxacos, Cxaest, Cxapro, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."',".$wpor.",'".$pos."',0,'C-".$empresa."')";
								$err1 = mysql_query($query,$conex);
								if ($err1 != 1)
									echo mysql_errno().":".mysql_error()."<br>";
								$count++;
								echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
								echo "<tr><td>".$row[0]."-".$row[1]."</td><td>DRIVER EN CERO</td></tr>";
							}
						}
	    			}
	    			else
	    				echo "<tr><td>".$row[0]."-".$row[1]."</td><td>NO EXISTE MOVIMIENTO DRIVER</td></tr>";
				}
				
				echo "<br><br>REGISTROS SIN COSTOS EN TABLA 87<br>";
				for ($i=0;$i<$numreg;$i++)
				{
					//echo $REG[$i][0]." ".$REG[$i][1]." ".$REG[$i][2]."<br>";
					if($REG[$i][2] == 0)
					{
						$pos=busqueda($DATA,$ext,$wanop,$wper1,$REG[$i][0],$REG[$i][1]);
						$query = "insert ".$empresa."_000083 (medico,fecha_data,hora_data,Cxaemp, Cxaano, Cxames, Cxacco, Cxasub, Cxacos, Cxaest, Cxapro, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$REG[$i][0]."','".$REG[$i][1]."',0,'".$pos."',0,'C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						$count++;
						echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
					}
				}
				echo "</TABLE>";
				echo "TOTAL REGISTROS ACTUALIZADOS : ".$count;
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL CCO ESTA CERRADO EN ESTE PERIODO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
