<?php
include_once("conex.php");
echo "<html>";
echo "<head>";
echo "<title>MATRIX  TURNOS : ".$wtit."</title>";
echo "</head>";
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";
echo "<center>";
if (!isset($wfec))
	$wfec=date("Y-m-d");
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=6><b>Turnos x Equipo :".$wtit."</b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> 000001_prx1.php Ver. 1.03</b></font></tr></td></table>";
echo "</center>";
echo "<form action='000001_prx1.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	$key = substr($user,2,strlen($user));
	

	

	$query = "select prioridad from usuarios where codigo = '".substr($user,2,strlen($user))."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	$prioridad = $row[0];
	if (isset($Ano) and isset($Mes) and isset($Dia))
	{
		if ($Mes < "10")
			$Mes = "0".$Mes;
		if ($Dia < "10")
			$Dia = "0".$Dia;
		$numDaysInMonth = date("t", mktime(0, 0, 0, (integer)$Mes, 1, (integer)$Ano));
		if((integer)$Dia > $numDaysInMonth)
			$Dia = $numDaysInMonth;
		$wfec = $Ano."-".$Mes."-".$Dia; 
	}
	else
	{
		$wfec1 = (string)$wfec;
		$Ano = substr($wfec1,0,4);
		$Mes = substr($wfec1,5,2);
		$Dia = substr($wfec1,8,2);
	}
	$year = (integer)substr($wfec,0,4);
	$month = (integer)substr($wfec,5,2);
	$day = (integer)substr($wfec,8,2);
	$nomdia=mktime(0,0,0,$month,$day,$year);
	$nomdia = strftime("%w",$nomdia);
	switch ($nomdia)
			{
				case 0:
					$diasem = "DOMINGO";
					break;
				case 1:
					$diasem = "LUNES";
					break;
				case 2:
					$diasem = "MARTES";
					break;
				case 3:
					$diasem = "MIERCOLES";
					break;
				case 4:
					$diasem = "JUEVES";
					break;
				case 5:
					$diasem = "VIERNES";
					break;
				case 6:
					$diasem = "SABADO";
					break;
			}
	echo "<table border=0 align=center cellpadding=6>";
	echo "<tr>";
	echo "<td bgcolor='#cccccc'><b>Fecha :</b></td>";
	echo "<td bgcolor='#cccccc'><font size=3><b>".$diasem."  ".$wfec."</b></font></td>";
	echo "<td bgcolor='#cccccc'><b>A?o :</b><td bgcolor='#cccccc'>";
	echo "<select name='Ano'>";
	for ($i=2003;$i<2051;$i++)
	{
		if (isset($Ano) and $Ano == (string)$i)
		{
			echo "<option selected>".$i."</option>";
		}
		else
			echo "<option>".$i."</option>";
	}
	echo "</td><td bgcolor='#cccccc'><b>Mes :</b><td bgcolor='#cccccc'>";
	echo "<select name='Mes'>";
	for ($i=1;$i<13;$i++)
	{
		if (isset($Mes) and $Mes == (string)$i)
			echo "<option selected>".$i."</option>";
		else
			echo "<option>".$i."</option>";
	}
	echo "</td><td bgcolor='#cccccc'><b>Dia :</b><td bgcolor='#cccccc'>";
	echo "<select name='Dia'>";
	for ($i=1;$i<32;$i++)
	{
		if (isset($Dia) and $Dia == (string)$i)
			echo "<option selected>".$i."</option>";
		else
			echo "<option>".$i."</option>";
	}
	echo "<input type='HIDDEN' name= 'wfec' value='".$wfec."'>";
	echo "<input type='HIDDEN' name= 'wequ' value='".$wequ."'>";
	echo "<input type='HIDDEN' name= 'wtit' value='".$wtit."'>";
	echo "<td bgcolor='#cccccc'><input type='submit' value='IR'>";
	echo "</td></tr></table><br>";
	$query = "select fecha,equipo,uni_hora,hi,hf from radio_000004 where fecha = '".$wfec."' and equipo = '".$wequ."'";
	$err = mysql_query($query,$conex);	
	$num = mysql_num_rows($err);
	if ($num == 0)
	{
		//echo "no";
		$query = "select codigo,descripcion,uni_hora,hi,hf,activo from radio_000003 where codigo='".$wequ."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);

	}
	else
	{
		//echo "si";
		$row = mysql_fetch_array($err);
	}
	$whi = $row[3];
	$wul = $row[4];
	$inc = $row[2];
	$part1 = (int)substr($whi,0,2);
	$part2 = (int)substr($whi,2,2);
	$part2 = $part2 + $inc;
	while ($part2 >= 60)
	{
		$part2 = $part2 - 60;
		$part1 = $part1 + 1;
	}
	$whf = (string)$part1.(string)$part2;
	if ($part1 < 10)
		$whf = "0".$whf;
	if ($part2 < 10)
		$whf = substr($whf,0,2)."0".substr($whf,2,1);	
	$color="#999999";
	echo "<table border=0 align=center>";
	echo "<tr>";
	echo "<td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Hora Final</b></font></td>"; 
  	echo "<td bgcolor=".$color."><font size=2><b>Medico</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";	
  	echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";	
  	echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Edad</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Estado</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Seleccion</b></font></td>"; 
	echo "</tr>";
	$r = 0;
	$i = 0;
	$fila = 0;
	$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from radio_000001 where fecha='".$wfec."' and cod_equ='".$wequ."' order by hi";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$row = mysql_fetch_array($err);
	}
	while ($whi < $wul)
	{
		$r = $i/2;
		if ($r*2 === $i)
			$color="#CCCCCC";
		else
			$color="#999999";
		if(strlen($row[0]) == 1 and $row[0] == "0"and $num > 0 and $row[4] == $whi)
				$color="#99ccff";
		echo "<tr>";
		if(substr($whi,0,2) > "12")
		{
			$hr1 ="". (string)((integer)substr($whi,0,2) - 12).":".substr($whi,2,2). " pm ";
			echo "<td bgcolor=".$color." align=center><font size=2>".$hr1."</font></td>";
		}
		else
			echo "<td bgcolor=".$color." align=center><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></td>";
		if(substr($whf,0,2) > "12")
		{
			$hr2 ="". (string)((integer)substr($whf,0,2) - 12).":".substr($whf,2,2). " pm ";
			echo "<td bgcolor=".$color." align=center><font size=2>".$hr2."</font></td>";
		}
		else
			echo "<td bgcolor=".$color." align=center><font size=2>".substr($whf,0,2).":".substr($whf,2,2)."</font></td>";
		if ($num > 0 and $row[4] == $whi)
		{	
			$query = "select codigo,nombre,oficio,tipo,edad_pac,activo from radio_000008 where codigo='".$row[0]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row1 = mysql_fetch_array($err1);	
			echo "<td bgcolor=".$color."><font size=2>".$row1[1]."</font></td>";
			$query = "select codigo,descripcion,preparacion,cod_equipo,activo,especial from radio_000006 where codigo='".$row[2]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row2 = mysql_fetch_array($err1);
			echo "<td bgcolor=".$color."><font size=2>".$row2[1]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[6]."</font></td>";
			$query = "select nit,descripcion from radio_000002 where nit='".$row[7]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row3 = mysql_fetch_array($err1);
			echo "<td bgcolor=".$color."><font size=2>".$row3[0]."-".$row3[1]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[8]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[9]."</font></td>";
			switch ($row[12])
			{
				case "A":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
					break;
				case "I":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/inactivo.gif' ></td>";
					break;
				default:
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
					break;
			}
			if ($prioridad > 0)
				echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx2.php?pos1=".$row1[0]."&amp;pos2=".$wequ."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=0&amp;pos8=".$row[9]."&amp;pos9=".$wtit."'>Editar</font></td>";
			else
				echo "<td bgcolor=".$color." align=center><font size=2>Sin Edicion</font></td>";
			echo "</tr>";
			$row = mysql_fetch_array($err);
			$fila = $fila + 1;
		}
		else
		{
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
			if ($prioridad > 0)
				echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx2.php?pos1=0&amp;pos2=".$wequ."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=0&amp;pos8=0&amp;pos9=".$wtit."'>Editar</font></td>";
			else
				echo "<td bgcolor=".$color." align=center><font size=2>Sin Edicion</font></td>";
			echo "</tr>";
		}
		$whi = $whf;
		$part1 = (int)substr($whi,0,2);
		$part2 = (int)substr($whi,2,2);
		$part2 = $part2 + $inc;
		while ($part2 >= 60)
		{
			$part2 = $part2 - 60;
			$part1 = $part1 + 1;
		}
		$whf = (string)$part1.(string)$part2;
		if ($part1 < 10)
			$whf = "0".$whf;
		if ($part2 < 10)
			$whf = substr($whf,0,2)."0".substr($whf,2,1);
		$i = $i + 1;
	}
	echo "</tabla>";
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";	
	echo "</body>";
	echo "</html>";
	include_once("free.php");
	}
?>
