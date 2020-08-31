<?php
include_once("conex.php");
echo "<html>";
echo "<head>";
echo "<title>ASIGNACION DE TURNOS X MEDICO</title>";
echo "</head>";
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";
echo "<center>";
echo "<A NAME='Arriba'><h1>Turnos x Medico :".substr($Medico,6,strlen($Medico))."</h1></a>";
echo "</center>";
$wfec=date("Y-m-d");
echo "<form action='000001_rrx2.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if (isset($Ano) and isset($Mes) and isset($Dia))
	{
		if ($Mes < "10")
			$Mes = "0".$Mes;
		if ($Dia < "10")
			$Dia = "0".$Dia;
		$wfec = $Ano."-".$Mes."-".$Dia; 
	}
	else
	{
		$wfec1 = (string)$wfec;
		$Ano = substr($wfec1,0,4);
		$Mes = substr($wfec1,5,2);
		$Dia = substr($wfec1,8,2);
	}
	echo "<table border=1 align=center>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Medico</td>";
	echo "<td bgcolor=#cccccc>";			
	echo "<select name='Medico'>";
	$query = "select codigo,nombre,oficio,tipo,edad_pac,activo from ".$empresa."_000008 where oficio='1-MEDICO'  order by codigo";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	for ($i=0;$i<$num1;$i++)
	{	
		$row1 = mysql_fetch_array($err1);
		if ($row1[0] == substr($Medico,0,5))
			echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
		else
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}
	echo "</td></tr></table><br>";
	echo "<table border=1 align=center cellpadding=6>";
	echo "<tr>";
	echo "<td bgcolor='#cccccc'><b>Fecha :</b></td>";
	echo "<td bgcolor='#cccccc'><font size=3><b>".$wfec."</b></font></td>";
	echo "<td bgcolor='#cccccc'><b>Año :</b><td bgcolor='#cccccc'>";
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
	echo "<td bgcolor='#cccccc'><input type='submit' value='IR'>";
	echo "</td></tr></table><br>";
	$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from ".$empresa."_000001 where fecha='".$wfec."' and cod_med='".substr($Medico,0,strpos($Medico,"-"))."' order by hi";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$color="#999999";
	echo "<table border=1 align=center>";
	echo "<tr><td align=center colspan=8><IMG SRC='/matrix/images/medical/Citas/logo_".$empresa.".png'></td></tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Equipo</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";	
  	echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";	
  	echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Edad</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Usuario</b></font></td>";
	echo "</tr>";
	$r = 0;
	$i = 0;
	$fila = 0;
	$spaces = "";
	for ($i=0;$i<30;$i++)
	{
		$spaces = $spaces.".";
	}
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);
		$r = $i/2;
		if ($r*2 === $i)
			$color="#CCCCCC";
		else
			$color="#999999";
		echo "<tr>";
		echo "<td bgcolor=".$color." align=center><font size=1>".substr($row[4],0,2).":".substr($row[4],2,2)."</font></td>";
		$query = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".$row[1]."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		$row1 = mysql_fetch_array($err1);		
		echo "<td bgcolor=".$color."><font size=1>".$row1[1]."</font></td>";
		$query = "select codigo,descripcion,preparacion,cod_equipo,activo,especial from ".$empresa."_000006 where codigo='".$row[2]."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		$row2 = mysql_fetch_array($err1);
		echo "<td bgcolor=".$color."><font size=1>".$row2[1]."</font></td>";
		echo "<td bgcolor=".$color."><font size=1>".$row[6]."</font></td>";
		$query = "select nit,descripcion,activo from ".$empresa."_000002 where nit='".$row[7]."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		$row3 = mysql_fetch_array($err1);
		echo "<td bgcolor=".$color."><font size=1>".$row3[1]."</font></td>";
		echo "<td bgcolor=".$color."><font size=1>".$row[8]."</font></td>";
		echo "<td bgcolor=".$color."><font size=1>".$row[9]."</font></td>";
		echo "<td bgcolor=".$color."><font size=1>".$row[11]."</font></td>";
	}
	echo "</table>";
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></table>";
	echo "</body>";
	echo "</html>";
	include_once("free.php");
	}
?>
