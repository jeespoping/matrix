<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo de Ingresos Promedio x Cco</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro93.php Ver. 2015-09-07</b></font></tr></td></table>
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
	

	

	echo "<form action='000001_pro93.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wanop) or !isset($wanob) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
		echo "<tr><td align=center colspan=2>CALCULO DE INGRESOS PROMEDIO X CCO</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc align=center>A&ntilde;o Presupuesto</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanob' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
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
		$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
		$query = $query."  where ano = ".$wanop;
		$query = $query."    and mes = 0 ";
		$query = $query."    and emp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		if($num > 0 and $row[0] == "on")
		{
			$query = "DELETE from ".$empresa."_000014 ";
			$query = $query." where Iplano = ".$wanob;
			$query = $query."   and Ipltip = 'A' ";
			$query = $query."   and Iplemp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("ERROR EN BORRADO T14");
			
			$PER=0;
			$query  = "SELECT sum(Perlab) from ".$empresa."_000040 where perano = ".$wanob." and Perper between ".$wper1." and ".$wper2;
			$query .= "    order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$PER=$row[0];
			}
			
			$ITO=array();
			$query  = "SELECT Miocco, sum(Mioito) from ".$empresa."_000063 where Mioano = ".$wanob." and Miomes between ".$wper1." and ".$wper2." and Mioemp = '".$wemp."'";
			$query .= "   Group by 1 ";
			$query .= "   order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$ITO[$i][0]=$row[0];
					$ITO[$i][1]=$row[1];
				}
			}
			$numito=$num;
			
			$INT=array();
			$query  = "SELECT Miocco, sum(Mioint) from ".$empresa."_000063 where Mioano = ".$wanob." and Miomes between ".$wper1." and ".$wper2." and Mioemp = '".$wemp."'";
			$query .= "   Group by 1 ";
			$query .= "   order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$INT[$i][0]=$row[0];
					$INT[$i][1]=$row[1];
				}
			}
			$numint=$num;
			
			$CAN=array();
			$query  = "SELECT Morcco, sum(Morcan) from ".$empresa."_000032 where Morano = ".$wanob." and Mormes between ".$wper1." and ".$wper2." and Mortip = 'P' and Moremp = '".$wemp."' ";
			$query .= "   Group by 1 ";
			$query .= "   order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$CAN[$i][0]=$row[0];
					$CAN[$i][1]=$row[1];
				}
			}
			$numcan=$num;
			
			$query  = "select Cpicco, Cpiuni from ".$empresa."_000123 ";
			$query .= " where Cpiano = ".$wanop;
			$query .= "   and Cpipre =  'on' ";
			$query .= "   and Cpical =  '2' ";
			$query .= "   and Cpiemp =  '".$wemp."' ";
			$query .= "  order by 1 ";
			$err = mysql_query($query,$conex) or die("ERROR : ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$data=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					switch($row[1])
					{
						case "1":
							$inp = 0;
							$pos=bi($ITO,$numito,$row[0]);
							if($pos != -1)
							{
								$inp = $ITO[$pos][1];
								$pos=bi($CAN,$numcan,$row[0]);
								if($pos != -1)
								{
									$inp = $inp / $CAN[$pos][1];
								}
							}
							$int = 0;
							$pos=bi($INT,$numint,$row[0]);
							if($pos != -1)
							{
								$int = $INT[$pos][1];
								$pos=bi($ITO,$numito,$row[0]);
								if($pos != -1)
								{
									if($ITO[$pos][1] > 0)
										$int = $int / $ITO[$pos][1] * 100;
									else
										$int = 0;
								}
							}
						break;
						case "2":
							$inp = 0;
							$pos=bi($ITO,$numito,$row[0]);
							if($pos != -1)
							{
								$inp = $ITO[$pos][1];
								$inp = $inp / ($wper2 - $wper1 +1);
							}
							$int = 0;
							$pos=bi($INT,$numint,$row[0]);
							if($pos != -1)
							{
								$int = $INT[$pos][1];
								$pos=bi($ITO,$numito,$row[0]);
								if($pos != -1)
								{
									if($ITO[$pos][1] > 0)
										$int = $int / $ITO[$pos][1] * 100;
									else
										$int = 0;
								}
							}
						break;
						break;
						case "3":
							$inp = 0;
							$pos=bi($ITO,$numito,$row[0]);
							if($pos != -1)
							{
								$inp = $ITO[$pos][1];
								$inp = $inp / $PER;
							}
							$int = 0;
							$pos=bi($INT,$numint,$row[0]);
							if($pos != -1)
							{
								$int = $INT[$pos][1];
								$pos=bi($ITO,$numito,$row[0]);
								if($pos != -1)
								{
									if($ITO[$pos][1] > 0)
										$int = $int / $ITO[$pos][1] * 100;
									else
										$int = 0;
								}
							}
						break;
						break;
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000014 (medico,fecha_data,hora_data, Iplemp, Iplano, Iplcco, Iplinp, Iplpte, Ipltip, Seguridad) values (";
					$query .=  "'".$empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $wemp."',";
					$query .=  $wanob.",'";
					$query .=  $row[0]."',";
					$query .=  $inp.",";
					$query .=  $int.",";
					$query .=  "'A','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS : ".mysql_errno().":".mysql_error());
					echo "GRABANDO EL REGISTRO NRO : ".$i."<br>";
				}
				echo "<b>NUMERO DE REGISTROS GENERADOS : ".$num."</b><br>";
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
