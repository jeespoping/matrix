<html>
<head>
<title>CANCELACION DE CITAS X PACIENTE</title>
</head>
<body BGCOLOR='#FFFFFF' TEXT='#000066'>
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Canfis.submit();
	}
//-->
</script>
<?php
include_once("conex.php");
echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=6><b>Cancelacion de Citas x Paciente</b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> Canfis.php Ver. 2010-01-26</b></font></tr></td></table>";
echo "</center>";
$wfec=date("Y-m-d");
echo "<form name='Canfis' action='Canfis.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok))
	{
		// 2020-11-20 se desactiva para reducir los bloqueos a las tabla citasfi_000017
		//$query = "lock table ".$empresa."_000017 LOW_PRIORITY WRITE ";
		//$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
		for ($i=0;$i<$num;$i++)
		{
			if(isset($fac[$i]))
			{
				$query =  " update ".$empresa."_000017 set Actividad = 2 where Terapeuta='".$data[$i][0]."' and Fecha='".$data[$i][1]."' and Hora_inicial='".$data[$i][2]."' and Cedula='".$data[$i][3]."'";
				$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
		}
		//$query = " UNLOCK TABLES";													
		//$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");
		unset($ok);
		unset($wpac);
	}
	if (!isset($wpac) or !isset($wper1) or !isset($wper2) or $wper1 < date("Y-m-d") or $wper2 < date("Y-m-d") or $wper2 < $wper1)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>CANCELACION DE CITAS X PACIENTE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Cedula del Paciente : </td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpac' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial del Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final del Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<td bgcolor='#cccccc'  align=center colspan=2><input type='submit' value='IR'>";
		echo "</td></tr></table><br>";
	}
	else
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr><td  bgcolor=".$color." align=center colspan=8><font size=6>CANCELACION DE CITAS X PACIENTE</font></td></tr>";
		echo "<tr><td align=center bgcolor=#dddddd colspan=8><b>Marcar Todos</b><input type='checkbox' name='all'  onclick='enter()'></td></tr>";
		$color="#999999";
		echo "<tr>";
		echo "<td bgcolor=".$color."><font size=2><b>Anular</b></font></td>";
		echo "<td bgcolor=".$color."><font size=2><b>Terapeuta</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Fecha</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Hora Inicial</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Cedula</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Nombre</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Resposable</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Tipo de Paciente</b></font></td>";
		echo "</tr>";
		
		//                  0                            1        2         3          4                         5           6            7         8
		$query = "select Terapeuta, ".$empresa."_000015.Nombre, Fecha, Hora_inicial, Cedula, ".$empresa."_000017.Nombre, Responsable, Descripcion, Tipo from ".$empresa."_000017,".$empresa."_000015,".$empresa."_000002 ";
		$query .= "  where Fecha between '".$wper1."' and '".$wper2."' ";
		$query .= "    and Cedula = '".$wpac."' ";
		$query .= "    and Terapeuta = Codigo ";
		$query .= "    and Responsable = Nit ";
		$query .= "    and Actividad IN (0,1) ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$data=array();
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if ($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#FFFFFF";
				$data[$i][0]=$row[0]; #Terapeuta
				$data[$i][1]=$row[2]; #Fecha
				$data[$i][2]=$row[3]; #Hora Inicial
				$data[$i][3]=$row[4]; #Cedula
				if($row[8] == "A")
					$tipo="AMBULATORIO";
				else
					$tipo="HOSPITALIZADO";
				echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][2]' value='".$data[$i][2]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][3]' value='".$data[$i][3]."'>";
				echo"<tr>";
				if(isset($fac[$i]) or isset($all))
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]' checked></td>";
				else
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]'></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[0]."-".$row[1]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[2]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[3]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[4]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[5]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[6]."-".$row[7]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$tipo."</font></td></tr>";
			}
			echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
			echo "<input type='HIDDEN' name= 'wpac' value='".$wpac."'>";
			echo "<input type='HIDDEN' name= 'wper1' value='".$wper1."'>";
			echo "<input type='HIDDEN' name= 'wper2' value='".$wper2."'>";
			$color="#999999";
			echo "<tr><td align=center bgcolor=".$color." colspan=8><b>ANULAR</b><input type='checkbox' name='ok' onclick='enter()'></td></tr>";
		}
		echo "</table>";
	}
	include_once("free.php");
}
?>
</body>
</html>