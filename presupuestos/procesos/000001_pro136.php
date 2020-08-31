<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion de Proyeccion de Obligaciones Financieras</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro136.php Ver. 2015-09-25</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='000001_pro136.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>APLICACION DE OBLIGACIONES FINANCIERAS</td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE PROYECCION DE OBLIGACIONES FINANCIERAS</td></tr>";
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
		#INICIO PROGRAMA
		$k=0;
		$tasas=array();
		$query  =" select Mtaano, Mtatip, Mtaval from ".$empresa."_000134 ";
		$query .= " where Mtaest = 'on' "; 
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$tasas[$row[1]][$row[0]]=$row[2];
			}
		}
		
		$KO = 0;
		
		$query  =" Delete from ".$empresa."_000135 where Movtip != 'M' and Movemp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
		
		//                  0       1       2       3       4        5       6       7      8        9      10      11      12     13       14     15       16      17
		$query  =" select Moftob, Mofnid, Mofent, Mofani, Mofmei, Mofmon, Mofopc, Mofpla, Mofpga, Moftta, Moftas, Mofpad, Moffam, Mofper, Moftip, Mofest, Mofpco, Mofpek from ".$empresa."_000132 ";
		$query .= " where Mofest = 'on' "; 
		$query .= "   and Moftip != 'M' "; 
		$query .= "   and Mofemp = '".$wemp."' ";
		$query .= " Order by 1 "; 
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$plazo =  $row[7];
				if($row[16] != 0)
					$plazo =  $row[16];
				if($row[0] == "CT" or $row[0] == "FG" or $row[0] == "CR" or $row[0] == "RP" or $row[0] == "AP")
				{
					$tipobl = $row[0];
					$row[0] = "CR";
				}
				if($row[0] == "LI" or $row[0] == "LG")
				{
					$tipobl = $row[0];
					$row[0] = "LG";
				}
				switch ($row[0])
				{
					case "LG":
						$sci = 0;
						for ($k=0;$k<=$plazo;$k++)
						{
							if($k == 0)
							{
								$ano = $row[3];
								$mes = $row[4];
								$si = $row[5];
								$sf = $row[5];
							}
							else
							{
								$mes++;
								if($mes > 12)
								{
									$ano++;
									$mes = 1;
								}
							}
							if($row[9] == "TV")
								$T=$tasas[$row[10]][$ano];
							else
								$T=$row[10];
							$ip1 = (1 + ($T /  100) + ($row[11] / 100));
							$ip2 = ($row[13] / 12);
							$ip3 = (1 / 12);
							$ip = pow($ip1,$ip2) - 1;
							$im = pow($ip1,$ip3) - 1;
							
							if($k % $row[13] == 0 and $k > 0)
							{
								$cuota1 = ($row[7] - $k +  $row[13]) / $row[13];
								$cuota2 = (1 + $ip);
								$cuota3 = pow($cuota2,$cuota1);
								if($k == $row[7])
								{
									$cuotaR = (($ip * $cuota3) / ($cuota3 - 1)) * ($sf - ($row[6] / $cuota3));
									$cuotaR += $row[6];
								}
								else
								{
									$cuotaR = (($ip * $cuota3) / ($cuota3 - 1)) * ($sf - ($row[6] / $cuota3));
								}
								
								$fi = $ip * $sf;
								$ci = $fi - $sci;
								$sci = 0;
							}
							else
							{
								if($k > 0)
								{
									$ci = $im * $sf;
									$sci += $ci;
								}
								else
									$ci = 0;
								$fi = 0;
								$cuotaR = 0;
							}
							
							// Calculo Flujo de Capital
							$fk = $cuotaR - $fi;
							// Nuevo Saldo Inicial
							$si = $sf;
							// Nuevo saldo final
							$sf = $si - $fk;
							
							
							/*
								si 		- Saldo Inicial
								sf 		- Saldo Final
								ip 		- Tasa de Interes del Periodo
								im 		- Tasa de Interes Mensual
								cuotaR 	- Cuota del Periodo
								fi 		- Flujo de Intereses
								fk		- Flujo de Capital
								ci		- Causacion de Intereses
								k		- Periodo
								
							*/
							//echo "Iteracion : ".$k." A�o:".$ano." Mes:".$mes." Flujo de Interes: ".$fi." Flujo de Capital: ".$fk." Cuota: ".$cuotaR." Causacion Intereses: ".$ci." Saldo Inicial: ".$si." Saldo Final: ".$sf."<br>";
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000135 (medico,fecha_data,hora_data,Movemp, Movtob, Movnid, Movano, Movmes, Movper, Movsai, Movfca, Movfin, Movcin, Movcuo, Movsaf, Movtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$tipobl."','".$row[1]."',".$ano.",".$mes.",".$k.",".$si.",".$fk.",".$fi.",".$ci.",".$cuotaR.",".$sf.",'".$row[14]."','C-".$empresa."')";
							$err2 = mysql_query($query,$conex);
							if ($err2 != 1)
								echo mysql_errno().":".mysql_error()."<br>";
							else
							{
								$KO++;
								echo "REGISTRO INSERTADO  : ".$KO."<br>";
							}
						}
					break;
					case "CR":
						$sci = 0;
						for ($k=0;$k<=$plazo;$k++)
						{
							if($k == 0)
							{
								$ano = $row[3];
								$mes = $row[4];
								$si = $row[5];
								$sf = $row[5];
							}
							else
							{
								$mes++;
								if($mes > 12)
								{
									$ano++;
									$mes = 1;
								}
							}
							if($row[9] == "TV")
								$T=$tasas[$row[10]][$ano];
							else
								$T=$row[10];
							$ip1 = (1 + ($T /  100) + ($row[11] / 100));
							$ip2 = ($row[13] / 12);
							$ip3 = (1 / 12);
							$ip = pow($ip1,$ip2) - 1;
							$im = pow($ip1,$ip3) - 1;
							//*** de $row[13] a $row[17]
							if($row[17] == 0)
								$row[17] = $row[13]; 
							if(($k / $row[17]) <= ($row[8] / $row[17]))
								$fk = 0;
							else
								if($k % $row[17] == 0 and $k > 0)
									$fk = $row[5] / (($row[7] - $row[8]) / $row[17]);
								else
									$fk = 0;
							//**************
							if($k % $row[13] == 0 and $k > 0)
							{
								$fi = $ip * $sf;
								$ci = $fi - $sci;
								$sci = 0;
							}
							else
							{
								if($k > 0)
								{
									$ci = $im * $sf;
									$sci += $ci;
								}
								else
									$ci = 0;
								$fi = 0;
							}
							$cuotaR = $fk + $fi;
							
							// Nuevo Saldo Inicial
							$si = $sf;
							// Nuevo saldo final
							$sf = $si - $fk;
							
							/*
								si 		- Saldo Inicial
								sf 		- Saldo Final
								ip 		- Tasa de Interes del Periodo
								im 		- Tasa de Interes Mensual
								cuotaR 	- Cuota del Periodo
								fi 		- Flujo de Intereses
								fk		- Flujo de Capital
								ci		- Causacion de Intereses
								k		- Periodo
								
							*/
							//echo "Iteracion : ".$k." A�o:".$ano." Mes:".$mes." Flujo de Interes: ".$fi." Flujo de Capital: ".$fk." Cuota: ".$cuotaR." Causacion Intereses: ".$ci." Saldo Inicial: ".$si." Saldo Final: ".$sf."<br>";
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000135 (medico,fecha_data,hora_data,Movemp, Movtob, Movnid, Movano, Movmes, Movper, Movsai, Movfca, Movfin, Movcin, Movcuo, Movsaf, Movtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$tipobl."','".$row[1]."',".$ano.",".$mes.",".$k.",".$si.",".$fk.",".$fi.",".$ci.",".$cuotaR.",".$sf.",'".$row[14]."','C-".$empresa."')";
							$err2 = mysql_query($query,$conex);
							if ($err2 != 1)
								echo mysql_errno().":".mysql_error()."<br>";
							else
							{
								$KO++;
								echo "REGISTRO INSERTADO  : ".$KO."<br>";
							}
						}
					break;
				}
			}
			echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$KO."</b><br>";
		}
	}
}		
?>
</body>
</html>
