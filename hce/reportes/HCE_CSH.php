<html>
<head>
<title>MATRIX</title>
	<style type="text/css">
		#tipo1{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:normal;text-align:left;}
		#tipo2{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;text-align:left;}
		#tipo3{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipo4{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipo5{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
		#tipo6{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
		#tipo7{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:right;}
		.tipoTABLE1{font-family:Arial;border-style:solid;border-collapse:collapse;}
		#tipoL04{color:#000000;background:#E8EEF7;font-size:10pt;font-family:Arial;font-weight:bold;width:75em;text-align:center;height:2em;}
		#tipoL01C{color:#000000;background:#C3D9FF;font-size:7pt;font-family:Arial;font-weight:bold;width:15em;text-align:center;height:2em;}
		#tipoL02C{color:#000066;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:bold;width:50em;text-align:center;height:2em;}
		#tipoL01{color:#000000;background:#C3D9FF;font-size:7pt;font-family:Arial;font-weight:bold;width:15em;text-align:center;height:2em;}
		#tipoA{color:#000066;background:#F4FA58;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
		#tipoV{color:#000066;background:#01DF01;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
		#tipoR{color:#000066;background:#FF0000;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
	</style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
	<script type="text/javascript">
		function enter()
		{
			document.forms.CSH.submit();
		}
	</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Control y Seguimiento de Heridas 2014-10-21</font></a></tr></td>
</center>
<?php
include_once("conex.php");
 @session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form name='CSH' action='HCE_CSH.php' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'origen' value='".$origen."'>";
	echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
	echo "<input type='HIDDEN' name= 'wing' value='".$wing."'>";
	echo "<input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
	echo "<input type='HIDDEN' name= 'wtipodoc' value='".$wtipodoc."'>";
	if(!isset($wnhe))
	{
		//                 0      1      2      3      4      5      6      7      8      9      10     11
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and pacced = oriced ";
		$query .= "   and pactid = oritid ";
		$query .= "   and oriori = '".$origen."'";
		$query .= "   and inghis = orihis ";
		if(!isset($wing))
			$query .= "   and inging = oriing ";
		else
			$query .= "   and inging = '".$wing."' ";
		$query .= "   and ubihis = inghis "; 
		$query .= "   and ubiing = inging ";
		$query .= "   and ccocod = ubisac ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$sexo="MASCULINO";
		if($row[5] == "F")
			$sexo="FEMENINO";
		$ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$ann1=($aa - $ann)/360;
		$meses=(($aa - $ann) % 360)/30;
		if ($ann1<1)
		{
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
		}
		else
		{
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		}
		$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		if(!isset($wing))
			$wing=$row[7];
		if(!isset($whis))
			$whis=$row[6];
		$color="#dddddd";
		$color1="#C3D9FF";
		$color2="#E8EEF7";
		$color3="#CC99FF";
		$color4="#99CCFF";
		echo "<center><table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$origen.".jpg' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=5 id=tipoL04>".$wpac."</td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table></center><br>";
		
		echo "<center><table border=0>";
		echo "<tr><td colspan=2 align=center><b>CONTROL Y SEGUIMIENTO DE HERIDAS</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nro de Herida</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT movdat FROM ".$empresa."_000299 where movhis = '".$whis."' and moving = '".$wing."' and movcon = 148 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$row = mysql_fetch_array($err);
			$num = $row[0];
			echo "<select name='wnhe' onChange='enter()'>";
			echo "<option>----</option>";
			for ($i=1;$i<=$num;$i++)
			{
				echo "<option>".$i."</option>";
			}
			echo "</select>";
		} 
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		//                 0      1      2      3      4      5      6      7      8      9      10     11
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and pacced = oriced ";
		$query .= "   and pactid = oritid ";
		$query .= "   and oriori = '".$origen."'";
		$query .= "   and inghis = orihis ";
		if(!isset($wing))
			$query .= "   and inging = oriing ";
		else
			$query .= "   and inging = '".$wing."' ";
		$query .= "   and ubihis = inghis "; 
		$query .= "   and ubiing = inging ";
		$query .= "   and ccocod = ubisac ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$sexo="MASCULINO";
		if($row[5] == "F")
			$sexo="FEMENINO";
		$ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$ann1=($aa - $ann)/360;
		$meses=(($aa - $ann) % 360)/30;
		if ($ann1<1)
		{
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
		}
		else
		{
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		}
		$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		if(!isset($wing))
			$wing=$row[7];
		if(!isset($whis))
			$whis=$row[6];
		$color="#dddddd";
		$color1="#C3D9FF";
		$color2="#E8EEF7";
		$color3="#CC99FF";
		$color4="#99CCFF";
		echo "<center><table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$origen.".jpg' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=5 id=tipoL04>".$wpac."</td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table></center><br>";
			
		echo "<center><table border=0>";
		$query = "select ".$empresa."_000300.Movdat ";
		$query .= "  from ".$empresa."_000300 ";
		$query .= "  where ".$empresa."_000300.Movhis = '".$whis."' ";
		$query .= "    and ".$empresa."_000300.Moving = '".$wing."' ";
		$query .= "    and ".$empresa."_000300.Movcon = 7 ";
		$query .= "    and ".$empresa."_000300.Movpro = '000300' ";
		$err = mysql_query($query ,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wubi = $row[0];
		}
		//                                      0                               1                   2                  
		$query = "select ".$empresa."_000300.Fecha_data,".$empresa."_000300.Hora_data ";
		$query .= "  from ".$empresa."_000300 ";
		$query .= "  where ".$empresa."_000300.Movhis = '".$whis."' ";
		$query .= "    and ".$empresa."_000300.Moving = '".$wing."' ";
		$query .= "    and ".$empresa."_000300.Movcon = 6 ";
		$query .= "    and ".$empresa."_000300.Movdat = '".$wnhe."' ";
		$query .= "    and ".$empresa."_000300.Movpro = '000300' ";
		$query .= "  order by 1,2  ";  
		$err = mysql_query($query ,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$fechas = "(";
			$horas = "(";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($i == 0)
				{
					$horas .= chr(34).$row[1].chr(34);
					$fechas .= chr(34).$row[0].chr(34);
				}
				else
				{
					$horas .= ",".chr(34).$row[1].chr(34);
					$fechas .= ",".chr(34).$row[0].chr(34);
				}
			}
			$horas .= ")";
			$fechas .= ")";
		}
		//                                      0                               1                   2                  
		$query = "select ".$empresa."_000300.Fecha_data,".$empresa."_000300.Hora_data,".$empresa."_000300.Movdat ";
		$query .= "  from ".$empresa."_000300 ";
		$query .= "  where ".$empresa."_000300.Movhis = '".$whis."' ";
		$query .= "    and ".$empresa."_000300.Moving = '".$wing."' ";
		$query .= "    and ".$empresa."_000300.Movcon = 11 ";
		$query .= "    and ".$empresa."_000300.Movpro = '000300' ";
		$query .= "    and ".$empresa."_000300.Fecha_data in  ".$fechas." ";
		$query .= "    and ".$empresa."_000300.Hora_data in  ".$horas." ";
		$query .= "  order by 1,2  ";  
		$err = mysql_query($query ,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			echo "<tr><td colspan=4 id='tipo4'>Herida Numero : ".$wnhe."</td></tr>";
			echo "<tr><td colspan=4 id='tipo4'>Ubicacion : ".$wubi."</td></tr>";
			echo "<tr>";
			echo "<td id='tipo3'>Fecha</td>";
			echo "<td id='tipo3'>Hora</td>";
			echo "<td id='tipo3'>Progreso de<br>Cierre</td>";
			echo "<td id='tipo3'>% Curacion</td>";
			echo "</tr>";
			$AA = -1; 
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$tipo = "tipo5";
				else
					$tipo = "tipo6";
				$row = mysql_fetch_array($err);
				if($AA == -1)
					$AA = $row[2];
				$PCI = $row[2] - $AA;
				$PCU = ($AA - $row[2]) / $AA * 100;
				if($PCI < 0)
					$tipoc="tipoV";
				elseif($PCI == 0)
						$tipoc="tipoA";
					else
						$tipoc="tipoR";
				echo "<tr>";
				echo "<td id=".$tipo.">".$row[0]."</td>";
				echo "<td id=".$tipo.">".$row[1]."</td>";
				echo "<td id=".$tipoc.">".number_format((double)$PCI,2,'.',',')."</td>";
				echo "<td id=".$tipo.">".number_format((double)$PCU,2,'.',',')."</td>";
				echo "</tr>"; 
				$AA = $row[2];
			}
			unset($wnhe);
			echo "<td colspan=4><input type='button' onClick='enter()' value='RETORNAR'></td>";
			echo "</table></enter>"; 
		}
		else
		{
			echo "<tr><td colspan=4 id='tipo3'>SIN REGISTROS PARA ESTE PACIENTE</td></tr>";
			echo "<tr>";
			echo "<td id='tipo4' colspan=4><input type='button' onClick='enter()' value='RETORNAR'></td>";
			echo "</tr>";
			echo "</table></enter>"; 
		}
	}
}
?>
</body>
</html>
