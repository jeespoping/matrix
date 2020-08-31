<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Proyeccion Inicial Ingresos Unidades Sin Procedimientos Asociados (T30)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro104.php Ver. 2015-11-06</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='000001_pro104.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
		echo "<tr><td align=center colspan=2>PROYECCION INICIAL INGRESOS UNIDADES SIN PROCEDIMIENTOS ASOCIADOS (T30)</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
		$wemp=substr($wemp,0,strpos($wemp,"-"));
		#INICIO PROGRAMA
		$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
		$query = $query."  where ano = ".$wanop;
		$query = $query."    and mes = 0 ";
		$query = $query."    and Emp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		if($num > 0 and $row[0] == "on")
		{
			$k=0;
			$DIAS=array();
			$query = "SELECT Perper,Perlab from ".$empresa."_000040 ";
			$query .= "  where Perano = ".$wanop;
			$query .= " order by Perper ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$DIAS[$row[0]]=$row[1];
				}
			}
			$query = "delete  from ".$empresa."_000030 ";
			$query = $query."  where Ippano = ".$wanop;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			//                 0       1      2      3     4      5      6      7      8      9     
			$query = "SELECT Cipcco,Cipinp,Ciptin,Cipuni,Cipini,Cipper,Cipmax,Cipinc,Cipene,Cipdic from ".$empresa."_000025 ";
			$query .= "  where Cipano = ".$wanop;
			$query .= "    and Cipemp = '".$wemp."' ";
			$query .= " order by Cipcco ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[3] == 2)
					{
						if($row[2] == "on")
							$caso = 1;
						else
							$caso = 2;
					}
					else
					{
						if($row[2] == "on")
							$caso = 3;
						else
							$caso = 4;
					}
					switch ($caso)
					{
						case 1:
							for ($j=1;$j<=12;$j++)
							{
								switch ($j)
								{
									case 1:
										if($row[8] != 0)
											$val = $row[1] * (1 + ($row[8] / 100));
										elseif($row[5] <= $j)
												$val = $row[1] * (1 + ($row[4] / 100));
											else
												$val = $row[1];
									break;
									case 12:
										if($row[9] != 0)
											$val = $row[1] * (1 + ($row[9] / 100));
										else
											$val = $row[1] * (1 + ($row[4] / 100));
									break;
									default:
										if($row[5] <= $j)
											$val = $row[1] * (1 + ($row[4] / 100));
										else
											$val = $row[1];
									break;
								}
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$empresa = "costosyp";
								$query = "insert ".$empresa."_000030 (medico, fecha_data, Hora_data,Ippemp,Ippano,Ippmes,Ippcco,Ippipp, Seguridad) values ('";
								$query .=  $empresa."','";
								$query .=  $fecha."','";
								$query .=  $hora."','";
								$query .=  $wemp."',";
								$query .=  $wanop.",";
								$query .=  $j.",'";
								$query .=  $row[0]."',";
								$query .=  $val.",";
								$query .=  "'C-".$empresa."')";
								$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO ".$empresa."_000030: ".mysql_errno().":".mysql_error());
								$k++;
								echo "REGISTRO INSERTADO  : ".$k."<br>";
							}
						break;
						case 2:
							$VAL=array();
							$inc=0;
							for ($j=1;$j<=12;$j++)
							{
								switch ($j)
								{
									case 1:
										$VAL[$j] = $row[1] + $row[8];
										if($j == $row[5])
										{
											$VAL[$j] +=	$row[4];
											$inc = $row[4];
										}
										elseif($j > $row[5])
											{
												$inc += $row[7];
												if($inc <= $row[6])
													$VAL[$j] += $inc;
												else
													$VAL[$j] += $row[6];
											}
									break;
									case 12:
										$VAL[$j] = $row[1] + $row[9];
										if($j == $row[5])
										{
											$VAL[$j] +=	$row[4];
											$inc = $row[4];
										}
										elseif($j > $row[5])
											{
												$inc += $row[7];
												if($inc <= $row[6])
													$VAL[$j] += $inc;
												else
													$VAL[$j] += $row[6];
											}
									break;
									default:
										$VAL[$j] = $row[1];
										if($j == $row[5])
										{
											$VAL[$j] +=	$row[4];
											$inc = $row[4];
										}
										elseif($j > $row[5])
											{
												$inc += $row[7];
												if($inc <= $row[6])
													$VAL[$j] += $inc;
												else
													$VAL[$j] += $row[6];
											}
									break;
								}
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$empresa = "costosyp";
								$query = "insert ".$empresa."_000030 (medico, fecha_data, Hora_data,Ippemp, Ippano,Ippmes,Ippcco,Ippipp, Seguridad) values ('";
								$query .=  $empresa."','";
								$query .=  $fecha."','";
								$query .=  $hora."','";
								$query .=  $wemp."',";
								$query .=  $wanop.",";
								$query .=  $j.",'";
								$query .=  $row[0]."',";
								$query .=  $VAL[$j] .",";
								$query .=  "'C-".$empresa."')";
								$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO ".$empresa."_000030: ".mysql_errno().":".mysql_error());
								$k++;
								echo "REGISTRO INSERTADO  : ".$k."<br>";
							}
						break;
						case 3:
							for ($j=1;$j<=12;$j++)
							{
								switch ($j)
								{
									case 1:
										if($row[8] != 0)
											$val = $row[1] * (1 + ($row[8] / 100)) * $DIAS[$j];
										elseif($row[5] <= $j)
												$val = $row[1] * (1 + ($row[4] / 100)) * $DIAS[$j];
											else
												$val = $row[1] * $DIAS[$j];
									break;
									case 12:
										if($row[9] != 0)
											$val = $row[1] * (1 + ($row[9] / 100)) * $DIAS[$j];
										else
											$val = $row[1] * (1 + ($row[4] / 100)) * $DIAS[$j];
									break;
									default:
										if($row[5] <= $j)
											$val = $row[1] * (1 + ($row[4] / 100)) * $DIAS[$j];
										else
											$val = $row[1] * $DIAS[$j];
									break;
								}
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$empresa = "costosyp";
								$query = "insert ".$empresa."_000030 (medico, fecha_data, Hora_data,Ippemp, Ippano,Ippmes,Ippcco,Ippipp, Seguridad) values ('";
								$query .=  $empresa."','";
								$query .=  $fecha."','";
								$query .=  $hora."','";
								$query .=  $wemp."',";
								$query .=  $wanop.",";
								$query .=  $j.",'";
								$query .=  $row[0]."',";
								$query .=  $val.",";
								$query .=  "'C-".$empresa."')";
								$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO ".$empresa."_000030: ".mysql_errno().":".mysql_error());
								$k++;
								echo "REGISTRO INSERTADO  : ".$k."<br>";
							}
						break;
						case 4:
							$VAL=array();
							$inc=0;
							for ($j=1;$j<=12;$j++)
							{
								switch ($j)
								{
									case 1:
										$VAL[$j] = $row[1] + $row[8];
										if($j == $row[5])
										{
											$VAL[$j] +=	$row[4];
											$inc = $row[4];
										}
										elseif($j > $row[5])
											{
												$inc += $row[7];
												if($inc <= $row[6])
													$VAL[$j] += $inc;
												else
													$VAL[$j] += $row[6];
											}
									break;
									case 12:
										$VAL[$j] = $row[1] + $row[9];
										if($j == $row[5])
										{
											$VAL[$j] +=	$row[4];
											$inc = $row[4];
										}
										elseif($j > $row[5])
											{
												$inc += $row[7];
												if($inc <= $row[6])
													$VAL[$j] += $inc;
												else
													$VAL[$j] += $row[6];
											}
									break;
									default:
										$VAL[$j] = $row[1];
										if($j == $row[5])
										{
											$VAL[$j] +=	$row[4];
											$inc = $row[4];
										}
										elseif($j > $row[5])
											{
												$inc += $row[7];
												if($inc <= $row[6])
													$VAL[$j] += $inc;
												else
													$VAL[$j] += $row[6];
											}
									break;
								}
								$VAL[$j] *= $DIAS[$j];
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$empresa = "costosyp";
								$query = "insert ".$empresa."_000030 (medico, fecha_data, Hora_data,Ippemp, Ippano,Ippmes,Ippcco,Ippipp, Seguridad) values ('";
								$query .=  $empresa."','";
								$query .=  $fecha."','";
								$query .=  $hora."','";
								$query .=  $wemp."',";
								$query .=  $wanop.",";
								$query .=  $j.",'";
								$query .=  $row[0]."',";
								$query .=  $VAL[$j] .",";
								$query .=  "'C-".$empresa."')";
								$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO ".$empresa."_000030: ".mysql_errno().":".mysql_error());
								$k++;
								echo "REGISTRO INSERTADO  : ".$k."<br>";
							}
						break;
					}
				}
				echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA CERRADO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
			echo "<br><br>";			
		}
	}
}		
?>
</body>
</html>
