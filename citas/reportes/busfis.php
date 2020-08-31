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
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> busfis.php Ver. 2011-08-17</b></font></tr></td></table>";
echo "</center>";
$wfec=date("Y-m-d");
echo "<form action='busfis.php' method=post>";
@session_start();
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
		echo "<tr><td  bgcolor=".$color." align=center colspan=10><font size=6>CONSULTA DE TURNOS X PACIENTE</font></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=".$color."><font size=2><b>Terapeuta</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Fecha</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Hora Inicial</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Cedula</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Nombre</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Observaciones</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Tipo</b></font></td>";
	  	echo "<td bgcolor=".$color."><font size=2><b>Estado</b></font></td>";
	  	echo "<td bgcolor=".$color." style='width:20em;'><font size=2><b>Firma</b></font></td>";
		echo "</tr>";
		//                  0         1        2           3                           4         5            6                             7          8         9       10
		$query = "select Terapeuta, Fecha, Hora_Inicial, Cedula, ".$empresa."_000017.Nombre, Responsable, Comentarios, ".$empresa."_000015.Nombre, Descripcion, Tipo, Actividad  from ".$empresa."_000017, ".$empresa."_000015, ".$empresa."_000002 ";
		$query .= "  where Fecha between '".$wper1."' and '".$wper2."' ";
		$query .= "       and ".$empresa."_000017.Nombre like '%".$wpac."%'";
		$query .= "       and Terapeuta = Codigo ";
		$query .= "       and Responsable = Nit ";
		$query .= " Order by 5,2,3 ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if ($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#FFFFFF";
				if((integer)substr($row[2],0,2) > 12)
				{
					$hora12=(integer)substr($row[2],0,2) - 12;
					if(strlen($hora12) < 2)
						$hora12 = "0".$hora12;
					$row[2]=$hora12.":".substr($row[2],2). " pm";
				}
				else
					$row[2]=substr($row[2],0,2).":".substr($row[2],2). " am";
				echo"<tr>";
				echo "<td bgcolor=".$color."><font size=2>".$row[0]."-".$row[7]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[1]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[2]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[3]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[4]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[5]."-".$row[8]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[6]."</font></td>";
				switch ($row[9])
				{
					case "A":
						$colorA="#FFCC66";
						echo "<td bgcolor=".$colorA.">AMBULATORIO</td>";
					break;
					case "H":
						$colorA="#CCCCFF";
						echo "<td bgcolor=".$colorA.">HOSPITALIZADO</td>";
					break;
				}
				switch ($row[10])
				{
					case 0:
						$colorA="#0099CC";
						echo "<td bgcolor=".$colorA.">PENDIENTE</td>";
					break;
					case 1:
						$colorA="#33FF00";
						echo "<td bgcolor=".$colorA.">ASISTIO</td>";
					break;
					case 2:
						$colorA="#CC99FF";
						echo "<td bgcolor=".$colorA.">CANCELO</td>";
					break;
					case 3:
						$colorA="#FF0000";
						echo "<td bgcolor=".$colorA.">NO ASISTIO</td>";
					break;
				}
				echo "<td bgcolor=".$color." style='width:20em;height:2.5em;'></td>";
				echo "</tr>";
			}
		}
		echo "<tr>";
		echo "<td colspan=10 bgcolor=#dddddd><font size=2><b>RECOMENDACIONES</b><br>";
		echo "* RECUERDE ASISTIR A LA CITA A LA HORA INDICADA.<br>";
		echo "* PROCURE VENIR CON ROPA COMODA.<br>";
		echo "* DEBE CANCELAR LA CITA UN DIA ANTES, SI LLAMA EL MISMO DIA LA CITA NO SE LE REPONE.<br>";
		echo "* SI FALTA A DOS CITAS Y NO SE REPORTA O SI CANCELA DOS CITAS CONSECUTIVAMENTE, EL TRATAMIENTO LE SER&Aacute; CANCELADO.<br>";
		echo "* SI VIENE POR EL SOAT, EN LA PRIMERA CITA DE TERAPIA DEBE TRAER NUEVAMENTE LAS COPIAS.<br>";
		echo "* SOLO CUANDO SEA NECESARIO, SE PERMITE LA ENTRADA DE UN ACOMPA&Ntilde;ANTE.<br>";
		echo "</font></td>";
		echo "</tr>";
		echo "</table>";
	}
	echo "</body>";
	echo "</html>";
	include_once("free.php");
	}
?>
