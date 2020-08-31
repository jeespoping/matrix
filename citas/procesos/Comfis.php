<html>
<head>
<title>COMPENSATORIOS EN HORARIOS DE TERAPEUTAS</title>
</head>
<body BGCOLOR='#FFFFFF' TEXT='#000066'>
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Comfis.submit();
	}
//-->
</script>
<?php
include_once("conex.php");
echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=6><b>Compensatorios En Horarios De Terapeutas</b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> Comfis.php Ver. 2010-04-14</b></font></tr></td></table>";
echo "</center>";
$wfec=date("Y-m-d");
echo "<form name='Comfis' action='Comfis.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if (!isset($wter) or $wter == "SELECCIONAR" or !isset($wper) or !isset($whori) or !isset($whorf)  or ($whori > $whorf)  or $wper < date("Y-m-d") or $whor == "SELECCIONAR")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>Compensatorios En Horarios De Terapeutas</td></tr>";
		echo "<tr><td align=center bgcolor='#cccccc'>Terapeuta : </td><td bgcolor=#cccccc>";
		$query = "SELECT Codigo, Nombre  from  ".$empresa."_000015 where estado='on' order by Nombre";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wter'>";
		if ($num>0)
		{
			echo "<option>SELECCIONAR</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo"</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wper' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Hora de Inicial</td><td bgcolor=#cccccc>";
		echo "<select name='whori'>";
			echo "<option>SELECCIONAR</option>";
			echo "<option>0700</option>";
			echo "<option>0800</option>";
			echo "<option>0900</option>";
			echo "<option>1000</option>";
			echo "<option>1100</option>";
			echo "<option>1200</option>";
			echo "<option>1300</option>";
			echo "<option>1400</option>";
			echo "<option>1500</option>";
			echo "<option>1600</option>";
			echo "<option>1700</option>";
			echo "<option>1800</option>";
		echo "</select>";
		echo"</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Hora de Inicial</td><td bgcolor=#cccccc>";
		echo "<select name='whorf'>";
			echo "<option>SELECCIONAR</option>";
			echo "<option>0700</option>";
			echo "<option>0800</option>";
			echo "<option>0900</option>";
			echo "<option>1000</option>";
			echo "<option>1100</option>";
			echo "<option>1200</option>";
			echo "<option>1300</option>";
			echo "<option>1400</option>";
			echo "<option>1500</option>";
			echo "<option>1600</option>";
			echo "<option>1700</option>";
			echo "<option>1800</option>";
		echo "</select>";
		echo"</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Cancelar Compensatorio</td><td bgcolor=#cccccc><input type='checkbox' name='wcan'></td></tr>";
		echo "<td bgcolor='#cccccc'  align=center colspan=2><input type='submit' value='IR'>";
		echo "</td></tr></table><br>";
	}
	else
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr><td  bgcolor=".$color." align=center colspan=4><font size=6>Compensatorios En Horarios De Terapeutas</font></td></tr>";
		$color="#999999";
		echo "<tr>";
		echo "<td bgcolor=".$color."><font size=2><b>Terapeuta</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Fecha</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Hora Inicial</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Comentarios</b></font></td>";
		echo "</tr>";
		$whor=$whori;
		while($whor <= $whorf)
		{
			if(isset($wcan))
			{
				$query =  " DELETE FROM ".$empresa."_000017  ";
				$query .=  "  where Terapeuta='".substr($wter,0,strpos($wter,"-"))."' and Fecha='".$wper."' and Hora_Inicial='".$whor."' and Cedula='0' and Actividad=9 ";
				$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO COMPENSATORIO : ".mysql_errno().":".mysql_error());
				echo "<tr><td bgcolor=#dddddd align=center>".$wter."</td><td bgcolor=#dddddd align=center>".$wper."</td><td bgcolor=#dddddd align=center>".$whor."</td><td bgcolor=#dddddd align=center>COMPENSATORIO BORRADO!!</td></tr>";
			}
			else
			{
				$query =  " Select count(*) from ".$empresa."_000017  where Terapeuta='".substr($wter,0,strpos($wter,"-"))."' and Fecha = '".$wper."' and Hora_inicial='".$whor."' and Actividad < 9 ";
				$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err3);
				if($row[0] == 0)
				{
					$query =  " DELETE FROM ".$empresa."_000017  ";
					$query .=  "  where Terapeuta='".substr($wter,0,strpos($wter,"-"))."' and Fecha='".$wper."' and Hora_Inicial='".$whor."' and Cedula='0' and Actividad=9 ";
					$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO COMPENSATORIO : ".mysql_errno().":".mysql_error());
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$wced="0";
					$wnom="";
					$wres="";
					$wtip="A";
					$wobs="";
					$query = "insert ".$empresa."_000017 (medico,fecha_data,hora_data, Terapeuta, Fecha, Hora_Inicial, Cedula, Nombre, Responsable, Tipo, Comentarios, Actividad, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  substr($wter,0,strpos($wter,"-"))."','";
					$query .=  $wper."','";
					$query .=  $whor."','";
					$query .=  $wced."','";
					$query .=  $wnom."','";
					$query .=  $wres."','";
					$query .=  $wtip."','";
					$query .=  $wobs."',";
					$query .=  "'9','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO TURNOS : ".mysql_errno().":".mysql_error());
					echo "<tr><td bgcolor=#dddddd align=center>".$wter."</td><td bgcolor=#dddddd align=center>".$wper."</td><td bgcolor=#dddddd align=center>".$whor."</td><td bgcolor=#dddddd align=center>COMPENSATORIO GRABADO</td></tr>";
				}
				else
				{
					echo "<tr><td bgcolor=#dddddd align=center>".$wter."</td><td bgcolor=#dddddd align=center>".$wper."</td><td bgcolor=#dddddd align=center>".$whor."</td><td bgcolor=#dddddd align=center>EXISTEN TURNOS ASIGNADOS A ESTA TERAPEUTA. CANCELELOS PRIMERO</td></tr>";
				}
			}
			$whor = (integer)substr($whor,0,2) + 1;
			if($whor < 10)
				$whor = "0".$whor;
			$whor .= "00";
		}
		echo "</table>";
	}
	include_once("free.php");
}
?>
</body>
</html>