<?php
include_once("conex.php");
echo "<html>";
echo "<head>";
echo "<title>CONSULTA DE TURNOS X PACIENTE</title>";
echo "</head>";
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";
echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=6><b>Turnos x Paciente</b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> 000001_rrx4.php Ver. 2006-06-14</b></font></tr></td></table>";
echo "</center>";
$wfec=date("Y-m-d");
echo "<form action='000001_rrx4.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if (!isset($wpac) or !isset($wper1) or !isset($wper2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>CONSULTA DE TURNOS X PACIENTE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Paciente : </td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpac' size=50 maxlength=60></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<td bgcolor='#cccccc'  align=center colspan=2><input type='submit' value='IR'>";
		echo "</td></tr></table><br>";
	}
	else
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr><td  bgcolor=".$color." align=center colspan=7><font size=6>CONSULTA DE TURNOS X PACIENTE</font></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Equipo</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Fecha</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Hora Inicial</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Hora Final</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Usuario Resp</b></font></td>";
		echo "</tr>";
		$query = "select Nom_pac, cod_equ, cod_exa, fecha, hi, hf, usuario
		 from ".$empresa."_000009 ";
		$query .= "  where fecha between '".$wper1."' and '".$wper2."' ";
		$query .= "       and Nom_pac like '%".$wpac."%'";
		$query .= " Order by Nom_pac, cod_equ ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if ($i % 2 == 0)
					$color="#CCCCCC";
				else
					$color="#dddddd";
				$query = "select codigo,descripcion from ".$empresa."_000010 where codigo='".$row[1]."' group by 1";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$query = "select codigo,descripcion from ".$empresa."_000011 where codigo='".$row[2]."' group by 1";
				$err2 = mysql_query($query,$conex);
				$row2 = mysql_fetch_array($err2);
				echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[1]."-".$row1[1]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[2]."-".$row2[1]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[3]."</font></td>";
				if(substr($row[4],0,2) > "12")
				{
					$hr1 ="". (string)((integer)substr($row[4],0,2) - 12).":".substr($row[4],2,2). " pm ";
					echo "<td bgcolor=".$color." align=center><font size=2>".$hr1."</font></td>";
				}
				else
					echo "<td bgcolor=".$color." align=center><font size=2>".substr($row[4],0,2).":".substr($row[4],2)."</font></td>";
				if(substr($row[5],0,2) > "12")
				{
					$hr1 ="". (string)((integer)substr($row[5],0,2) - 12).":".substr($row[5],2,2). " pm ";
					echo "<td bgcolor=".$color." align=center><font size=2>".$hr1."</font></td>";
				}
				else
					echo "<td bgcolor=".$color." align=center><font size=2>".substr($row[5],0,2).":".substr($row[5],2)."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[6]."</font></td></tr>";
			}
		}
		echo "</table>";
	}
	echo "</body>";
	echo "</html>";
	include_once("free.php");
	}
?>
