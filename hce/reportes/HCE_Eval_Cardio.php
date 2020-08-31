<html>
<head>
  	<title>MATRIX Informe de Evaluacion Cardiovascular</title>
	<link type='text/css' href='../procesos/HCE2.css' rel='stylesheet'> 
	<script>
		function toggleDisplay(id)
		{
			if (id.style.display=="none")
			{
				id.style.display="";
			}
			else 
			{
				id.style.display="none";
			}
		}
	</script>
</head>
<body onLoad= 'pintardivs();' BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function partir_grid($origen,&$data)
{
	if($origen != "0*")
	{
		$Gdataseg=explode("*",$origen);
		for ($g=1;$g<=$Gdataseg[0];$g++)
		{
			if($g == $Gdataseg[0])
			{
				$Gdatadata=explode("|",$Gdataseg[$g]);
				for ($g1=0;$g1<count($Gdatadata);$g1++)
				{
					$data[$g1+11] = $Gdatadata[$g1];
				}
			}
		}
	}
}

/**********************************************************************************************************************  
	   PROGRAMA : ".$empresa."_Eval_Cardio.php
	   Fecha de Liberación : 2014-04-18
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2014-04-18
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar una consulta cronologica de la información
	   sobre el estado cardiovascular de los pacientes de la unidad de Fisiatria.
	   
	   REGISTRO DE MODIFICACIONES 
	   	.2014-04-18
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_Eval_Cardio' action='HCE_Eval_Cardio.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'origen' value='".$origen."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'wformulario' value='".$wformulario."'>";
	echo "<input type='HIDDEN' name= 'wformulario1' value='".$wformulario1."'>";
	echo "<input type='HIDDEN' name= 'wcons' value='".$wcons."'>";
	//                 0      1      2      3      4      5      6      7      8      9      10     11
	$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
	$query .= " where pacced = '".$wcedula."'";
	$query .= "   and pactid = '".$wtipodoc."'";
	$query .= "   and pacced = oriced ";
	$query .= "   and pactid = oritid ";
	$query .= "   and oriori = '".$origen."'";
	$query .= "   and inghis = orihis ";
	$query .= "   and inging = '".$wing."' ";
	$query .= "   and ubihis = inghis "; 
	$query .= "   and ubiing = inging ";
	$query .= "   and ccocod = ubisac ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	$wsex="M";
	$sexo="MASCULINO";
	if($row[5] == "F")
	{
		$sexo="FEMENINO";
		$wsex="F";
	}
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
	$whis=$row[6];
	if(!isset($wing))
		$wing=$row[7];
	$dia=array();
	$dia["Mon"]="Lun";
	$dia["Tue"]="Mar";
	$dia["Wed"]="Mie";
	$dia["Thu"]="Jue";
	$dia["Fri"]="Vie";
	$dia["Sat"]="Sab";
	$dia["Sun"]="Dom";
	$mes["Jan"]="Ene";
	$mes["Feb"]="Feb";
	$mes["Mar"]="Mar";
	$mes["Apr"]="Abr";
	$mes["May"]="May";
	$mes["Jun"]="Jun";
	$mes["Jul"]="Jul";
	$mes["Aug"]="Ago";
	$mes["Sep"]="Sep";
	$mes["Oct"]="Oct";
	$mes["Nov"]="Nov";
	$mes["Dec"]="Dic";
	$fechal=strftime("%a %d de %b del %Y");
	$fechal=$dia[substr($fechal,0,3)].substr($fechal,3);
	$fechal=substr($fechal,0,10).$mes[substr($fechal,10,3)].substr($fechal,13);
	$color="#dddddd";
	$color1="#C3D9FF";
	$color2="#E8EEF7";
	$color3="#CC99FF";
	$color4="#99CCFF";
	if(!isset($wing))
		$wintitulo="Historia:".$row[6]." Ingreso:".$row[7]." Paciente:".$wpac;
	else
		$wintitulo="Historia:".$row[6]." Ingreso:".$wing." Paciente:".$wpac;
	$Hgraficas=" |";
	echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
	echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
	echo "<center><table border=1 width='712' class=tipoTABLE1>";
	echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/".$empresa."".$origen.".jpg' id='logo'></td>";	
	echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
	echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
	echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
	echo "</table></center><br>";
	
	
	echo "<table align=center border=1 class='tipoTABLEGRID' id='div".$id."' style='display: '>";
	echo "<tr><td id='tipoAL05GRID' colspan=29>EVALUACION CARDIOVASCULAR</td></tr>";
	echo "<tr><td id='tipoAL06GRID' colspan=3>DATOS GENERALES</td><td id='tipoAL06GRID' colspan=8>FACTORES DE RIESGO</td><td id='tipoAL06GRID' colspan=7>SIGNOS VITALES EVALUACION INICIAL</td><td id='tipoAL06GRID' colspan=6>SIGNOS VITALES EN EJERCICIO MAXIMO</td><td id='tipoAL06GRID' colspan=5>SIGNOS VITALES EN REPOSO</td></tr>";
	echo "<tr><td id='tipoAL07GRID'>FECHA</td>";
	echo "<td id='tipoAL07GRID'>DIAGNOSTICO</td>";
	echo "<td id='tipoAL07GRID'>ANTECEDENTES<br> QUIRURGICOS</td>";
	echo "<td id='tipoAL07GRID'>DIABETES</td>";
	echo "<td id='tipoAL07GRID'>HTA</td>";
	echo "<td id='tipoAL07GRID'>DISLIPIDEMIA</td>";
	echo "<td id='tipoAL07GRID'>ACTIVIDAD<br> FISICA</td>";
	echo "<td id='tipoAL07GRID'>TABAQUISMO</td>";
	echo "<td id='tipoAL07GRID'>RIESGO</td>";
	echo "<td id='tipoAL07GRID'>CLASE<br> FUNCIONAL</td>";
	echo "<td id='tipoAL07GRID'>FRACCION<br> DE EYECCION</td>";
	echo "<td id='tipoAL07GRID'>FCE</td>";
	echo "<td id='tipoAL07GRID'>PESO</td>";
	echo "<td id='tipoAL07GRID'>PAS</td>";
	echo "<td id='tipoAL07GRID'>PAD</td>";
	echo "<td id='tipoAL07GRID'>FRECUENCIA<br> CARDIACA</td>";
	echo "<td id='tipoAL07GRID'>SATURACION<br> DE OXIGENO</td>";
	echo "<td id='tipoAL07GRID'>MODALIDAD</td>";
	echo "<td id='tipoAL07GRID'>PAS</td>";
	echo "<td id='tipoAL07GRID'>PAD</td>";
	echo "<td id='tipoAL07GRID'>FRECUENCIA<br> CARDIACA</td>";
	echo "<td id='tipoAL07GRID'>SATURACION<br> DE OXIGENO</td>";
	echo "<td id='tipoAL07GRID'>OBSERVACIONES</td>";
	echo "<td id='tipoAL07GRID'>METS</td>";
	echo "<td id='tipoAL07GRID'>PAS</td>";
	echo "<td id='tipoAL07GRID'>PAD</td>";
	echo "<td id='tipoAL07GRID'>FRECUENCIA<br> CARDIACA</td>";
	echo "<td id='tipoAL07GRID'>SATURACION<br> DE OXIGENO</td>";
	echo "<td id='tipoAL07GRID'>OBSERVACIONES</td></tr>";
	
	$g = 0;
	$data=array();
	for ($i=0;$i<29;$i++)
		$data[$i] = " ";
	$query  = "select ".$empresa."_000036.Fecha_data,".$empresa."_000036.Hora_data,".$empresa."_000036.Firhis,".$empresa."_000036.Firing from ".$empresa."_000036 ";
	$query .= "  where ".$empresa."_000036.firpro='".$wformulario."' ";  
	$query .= "    and ".$empresa."_000036.firhis='".$whis."' ";
	$query .= "  group by 1,2,3,4 ";
	$query .= "  order by 4,1,2 ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$data[0] = $row[0];
			$query  = " select ".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movcon from ".$empresa."_".$wformulario;
			$query .= " where ".$empresa."_".$wformulario.".Fecha_data='".$row[0]."' "; 
			$query .= "   and ".$empresa."_".$wformulario.".Hora_data='".$row[1]."' "; 
			$query .= "   and ".$empresa."_".$wformulario.".movpro='".$wformulario."' "; 
			$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
			$query .= "   and ".$empresa."_".$wformulario.".moving='".$row[3]."' ";
			$query .= "   and ".$empresa."_".$wformulario.".movcon in (8,14,15,16,17,23,24,25,26,39,40,50,51) ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if ($num1 > 0)
			{
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					if($j == 0)
					{
						$query  = " select ".$empresa."_".$wformulario1.".Fecha_data,".$empresa."_".$wformulario1.".movcon,".$empresa."_".$wformulario1.".movdat from ".$empresa."_".$wformulario1;
						$query .= " where ".$empresa."_".$wformulario1.".movpro='".$wformulario1."' "; 
						$query .= "   and ".$empresa."_".$wformulario1.".movhis='".$whis."' ";
						$query .= "   and ".$empresa."_".$wformulario1.".moving='".$row[3]."' ";
						$query .= "   and ".$empresa."_".$wformulario1.".movcon in (11,12,35,41,87,88,89,92,95,98) ";
						$query .= "  order by 1 desc ";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$num2 = mysql_num_rows($err2);
						if ($num2 > 0)
						{
							for ($w=0;$w<$num2;$w++)
							{
								$row2 = mysql_fetch_array($err2);
								switch($row2[1])
								{
									case 11:
										$data[2] = $row2[2];
									break;
									case 12:
										$data[1] = $row2[2];
									break;
									case 35:
										$data[7] = $row2[2];
									break;
									case 41:
										$data[9] = $row2[2];
									break;
									case 87:
										$data[10] = $row2[2];
									break;
									case 88:
										$data[8] = $row2[2];
									break;
									case 89:
										$data[4] = $row2[2];
									break;
									case 92:
										$data[3] = $row2[2];
									break;
									case 95:
										$data[5] = $row2[2];
									break;
									case 98:
										$data[6] = $row2[2];
									break;
								}
							}
						}
					}
					switch($row1[1])
					{
						case 8:
							partir_grid($row1[0],&$data);
						break;
						case 14:
							$data[18] = $row1[0];
						break;
						case 15:
							$data[19] = $row1[0];
						break;
						case 16:
							$data[20] = $row1[0];
						break;
						case 17:
							$data[22] = $row1[0];
						break;
						case 23:
							$data[24] = $row1[0];
						break;
						case 24:
							$data[25] = $row1[0];
						break;
						case 25:
							$data[26] = $row1[0];
						break;
						case 26:
							$data[28] = $row1[0];
						break;
						case 39:
							$data[21] = $row1[0];
						break;
						case 40:
							$data[27] = $row1[0];
						break;
						case 50:
							$data[23] = $row1[0];
						break;
						case 51:
							$data[17] = $row1[0];
						break;
					}
				}
			}
			$g++;
			if($g % 2 == 0)
				$gridcolor="tipoAL02GRID1";
			else
				$gridcolor="tipoAL02GRID2";
			$wsprint = "<tr>";
			for ($w=0;$w<29;$w++)
				$wsprint .= "<td class='".$gridcolor."'>".$data[$w]."</td>";
			$wsprint .= "</tr>";
			echo $wsprint;
		}	
	}
}
?>
