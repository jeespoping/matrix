<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Asignacion de Reservas de Turnos Instituto de la Mujer</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_prx11.php Ver. 1.01</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
function valfec($chain)
{
	$fecha="/^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$/";
	if(preg_match($fecha,$chain,$occur))
	{
		if($occur[2] < 0 or $occur[2] > 12)
			return false;
		if(($occur[3] < 0 or $occur[3] > 31) or 
		  ($occur[2] == 4 and  $occur[3] > 30) or 
		  ($occur[2] == 6 and  $occur[3] > 30) or 
		  ($occur[2] == 9 and  $occur[3] > 30) or 
		  ($occur[2] == 11 and $occur[3] > 30) or 
		  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or 
		  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
			return false;
		return true;
	}
	else
		return false;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$wfec=date("Y-m-d");
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_prx11.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($y1) or !isset($m1) or !isset($d1) or !isset($y2) or !isset($m2) or !isset($d2) or !isset($whi) or !isset($whf) or !isset($wreserva) or !isset($wequip))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/Citas/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center colspan=2>Asignacion de Reservas de Turnos</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de Reserva Inicial</td>";
		echo "<td bgcolor=#cccccc>";
		echo "<select name='y1'>";
		for($f=1900;$f<2051;$f++)
			if ($f == substr($wfec,0,4))
				echo "<option selected>".$f."</option>";
			else
				echo "<option>".$f."</option>";
		echo "</select><select name='m1'>";
		for($f=1;$f<13;$f++)
		{
			if($f < 10)
				if ($f == substr($wfec,5,2))
					echo "<option selected>0".$f."</option>";
				else
					echo "<option>0".$f."</option>";
			else
				if ($f == substr($wfec,5,2))
					echo "<option selected>".$f."</option>";
				else
					echo "<option>".$f."</option>";
		}
		echo "</select><select name='d1'>";
		for($f=1;$f<32;$f++)
		{
			if($f < 10)
				if ($f == substr($wfec,8,2))
					echo "<option selected>0".$f."</option>";
				else
					echo "<option>0".$f."</option>";
			else
				if ($f == substr($wfec,8,2))
					echo "<option selected>".$f."</option>";
				else
					echo "<option>".$f."</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de Reserva Final</td>";
		echo "<td bgcolor=#cccccc>";
		echo "<select name='y2'>";
		for($f=1900;$f<2051;$f++)
			if ($f == substr($wfec,0,4))
				echo "<option selected>".$f."</option>";
			else
				echo "<option>".$f."</option>";
		echo "</select><select name='m2'>";
		for($f=1;$f<13;$f++)
		{
			if($f < 10)
				if ($f == substr($wfec,5,2))
					echo "<option selected>0".$f."</option>";
				else
					echo "<option>0".$f."</option>";
			else
				if ($f == substr($wfec,5,2))
					echo "<option selected>".$f."</option>";
				else
					echo "<option>".$f."</option>";
		}
		echo "</select><select name='d2'>";
		for($f=1;$f<32;$f++)
		{
			if($f < 10)
				if ($f == substr($wfec,8,2))
					echo "<option selected>0".$f."</option>";
				else
					echo "<option>0".$f."</option>";
			else
				if ($f == substr($wfec,8,2))
					echo "<option selected>".$f."</option>";
				else
					echo "<option>".$f."</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Hora Inicial</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='whi' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Hora Final</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='whf' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Origen de la Reserva</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wreserva' size=80 maxlength=80></td></tr>";
		$query = "select codigo,descripcion from ".$empresa."_000003 order by codigo";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Equipo</td>";		
		echo "<td bgcolor=#cccccc>";
		echo "<select name='wequip'>";	
		for($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($err1);
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wfec1=$y1."-".$m1."-".$d1;
		$wfec2=$y2."-".$m2."-".$d2;
		while($wfec1 <= $wfec2)
		{
			$tiperr="0000";
			$query = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".substr($wequip,0,3)."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$unidad=$row[2];
			$hi=$row[3];
			$hf=$row[4];
			$activo=$row[5];
			if($whi < $hi or strlen($whi) != 4 or !is_numeric($whi))
				$tiperr ="1".substr($tiperr,1,3);
			if($whf > $hf or strlen($whf) != 4 or !is_numeric($whf))
				$tiperr =substr($tiperr,0,1)."1".substr($tiperr,2,2);
			if($activo == "I")
				$tiperr =substr($tiperr,0,2)."1".substr($tiperr,3,1);
			$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,Ndias,Cedula,nom_pac,nit_resp,telefono,edad,comentarios,Asistida,usuario,activo from ".$empresa."_000001 where cod_equ = '".substr($wequip,0,3)."'";
			$query = $query." and fecha = '".$wfec1."'";
			$query = $query." and hi >= '".$whi."'";
			$query = $query." and hf <= '".$whf."'";	
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err); 
			if ($num > 0 )
				$tiperr =substr($tiperr,0,3)."1";
			if ($tiperr != "0000")
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				if (substr($tiperr,0,1) == "1")
				{
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>HORA INICIAL ESTA ERRONEA O ES MENOR QUE LA HORA INICIAL DEL EQUIPO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				if (substr($tiperr,1,1) == "1")
				{
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33cc33 LOOP=-1>HORA FINAL  ESTA ERRONEA O ES MAYOR QUE LA HORA FINAL DEL EQUIPO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				if (substr($tiperr,2,1) == "1")
				{
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>EL EQUIPO NO ESTA ACTIVO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				if (substr($tiperr,3,1) == "1")
				{
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ff0000 LOOP=-1>YA HAY TURNOS ASIGNADOS EN LAS HORAS ESPECIFICADAS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				echo "CODIGO DEL ERROR : ".$tiperr." PARA : ".$wfec1."<br>";
			}
			else
			{
				echo "TURNO ASIGNADO : ".$whi."-".$whf." PARA : ".$wfec1."<br>";
				$phi=$hi;
				while ($phi < $hf)
				{
					$part1 = (int)substr($phi,0,2);
					$part2 = (int)substr($phi,2,2);
					$part2 = $part2 + $unidad;
					while ($part2 >= 60)
					{
						$part2 = $part2 - 60;
						$part1 = $part1 + 1;
					}
					$phf = (string)$part1.(string)$part2;
					if ($part1 < 10)
						$phf = "0".$phf;
					if ($part2 < 10)
						$phf = substr($phf,0,2)."0".substr($phf,2,1);
					if ($phi >= $whi and $phf<=$whf)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000001 (medico,fecha_data,hora_data,cod_med,cod_equ,cod_exa,fecha,hi,hf,Ndias,Cedula,nom_pac,nit_resp,telefono,edad,comentarios,Asistida,usuario,activo,seguridad) values ('".$empresa."','".$fecha."','".$hora."','0','".substr($wequip,0,3)."','0','".$wfec1."','".$phi."','".$phf."',0,'.','".ucwords($wreserva)."','0','0',0,'.','.','".substr($user,2,strlen($user))."','A','C-".$empresa."')";
						$err = mysql_query($query,$conex);
						 if ($err != 1)
						 {
							echo "<center><table border=0 aling=center>";
							echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
							echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>TURNO YA EXISTE -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
							echo "<br><br>";
						}
					 }
					$phi = $phf;
				}
			}
			do
			{
				$d1++;
				 if($d1 > 31)
				{
					$d1=1;
					$m1++;
					if($m1 > 12)
					{
						$m1=1;
						$d1=1;
						$y1++;
					}
				}
				if(strlen($m1) < 2)
					$m1="0".$m1;
				if(strlen($d1) < 2)
					$d1="0".$d1;
				$wfec1=$y1."-".$m1."-".$d1;
			}while(!valfec($wfec1));
		}
	}
}
?>
</body>
</html>