<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo de Ingresos Promedio Serv. Farmaceutico x Uni Hosp. (T81)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro131.php Ver. 2015-09-25</b></font></tr></td></table>
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
	

	

	echo "<form action='000001_pro131.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanob) or !isset($wper1) or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
		echo "<tr><td align=center colspan=2>CALCULO DE INGRESOS PROMEDIO SERV. FARMACEUTICO X UNI HOSP. (T81)</td></tr>";
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
		$query = $query."    and Emp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		if($num > 0 and $row[0] == "on")
		{
			$query = "DELETE from ".$empresa."_000081 ";
			$query = $query." where Ipfano = ".$wanob;
			$query = $query."   and ipfemp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("ERROR EN BORRADO T81");
			
			$IPR=array();
			$query  = "SELECT igicco, sum(Igiinp) from ".$empresa."_000149 where Igiano = ".$wanob." and Igimes between ".$wper1." and ".$wper2." and Igiccd = '1050' ";
			$query .= "   Group by 1 ";
			$query .= "   order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$IPR[$i][0]=$row[0];
					$IPR[$i][1]=$row[1];
				}
			}
			$numipr=$num;
			
			$CAN=array();
			$query  = "SELECT Morcco, sum(Morcan) from ".$empresa."_000032 where Morano = ".$wanob." and Moremp = '".$wemp."' and Mormes between ".$wper1." and ".$wper2." and Mortip = 'P' ";
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
			
			$query  = "select Igicco from ".$empresa."_000149 ";
			$query .= " where Igiano = ".$wanob;
			$query .= "   and Igimes between ".$wper1." and ".$wper2;
			$query .= "   and Igiccd = '1050' ";
			$query .= "  Group by 1 ";
			$query .= "  Order by 1 ";
			$err = mysql_query($query,$conex) or die("ERROR : ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$data=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$inp = 0;
					$pos=bi($IPR,$numipr,$row[0]);
					if($pos != -1)
					{
						$inp = $IPR[$pos][1];
						$pos=bi($CAN,$numcan,$row[0]);
						if($pos != -1)
						{
							$inp = $inp / $CAN[$pos][1];
						}
					}
					$int = 0;
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000081 (medico,fecha_data,hora_data,Ipfemp, Ipfano, Ipfcco, Ipfipr, Ipfpor, Seguridad) values (";
					$query .=  "'".$empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $wemp."',";
					$query .=  $wanob.",'";
					$query .=  $row[0]."',";
					$query .=  $inp.",";
					$query .=  $int.",";
					$query .=  "'C-".$empresa."')";
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
