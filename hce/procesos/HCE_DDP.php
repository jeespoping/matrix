<html>
<head>
  	<title>MATRIX Programa de Impresion de Datos Demograficos HCE</title>
  	<link type='text/css' href='HCE.css' rel='stylesheet'> 
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">

<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("hce/funcionesHCE.php");
/**********************************************************************************************************************  
	   PROGRAMA : HCE_DDP.php
	   Fecha de Liberación : 2017-12-21
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2019-08-13
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar una impresion parametrizada de los registros
	   que corresponden a los datos demograficos complemantarios del paciente en la HCE.
	   
	   
	   REGISTRO DE MODIFICACIONES 
	   	.2019-08-13
			Se agrega el include a funcionesHCE.php con la función calcularEdadPaciente() y se reemplaza en el script el cálculo 
			de la edad del paciente por dicha función, ya que el cálculo se realizaba con 360 días, es decir, no se tenían en cuenta 
			los meses de 31 días y para los pacientes neonatos este dato es fundamental.
	   	.2017-12-21
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_DDP' action='HCE_DDP.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'origen' value='".$origen."'>";
	if(isset($wservicio))
		echo "<input type='HIDDEN' name= 'wservicio' value='".$wservicio."'>";
	else
		$wservicio = "*";
	
	$query = "select count(*) from root_000037 ";
	$query .= " where oriced = '".$wcedula."'";
	$query .= "   and oritid = '".$wtipodoc."'";
	$query .= "   and oriori = '".$origen."'";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	if($row[0] == 0)
	{
		echo "<center><table border=0>";
		echo "<tr><td id=tipoL09 colspan=".$span."><IMG SRC='/matrix/images/medical/HCE/Triste.png' style='vertical-align:middle;'>NO EXISTE INFORMACION EN LA HCE PARA ESTE PACIENTE</td></tr>";
		echo "</table></center>";
	}
	else
	{
		//                 0      1      2      3      4      5      6      7      8      9      10     11                     12
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom,".$wdbmhos."_000016.Fecha_data  from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
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
		// $ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
		// if(!isset($wing))
			// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		// else
			// $aa=(integer)substr($row[12],0,4)*360 +(integer)substr($row[12],5,2)*30 + (integer)substr($row[12],8,2);
		// $ann1=($aa - $ann)/360;
		// $meses=(($aa - $ann) % 360)/30;
		// if ($ann1<1)
		// {
			// $dias1=(($aa - $ann) % 360) % 30;
			// $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
		// }
		// else
		// {
			// $dias1=(($aa - $ann) % 360) % 30;
			// $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		// }
		$wedad = calcularEdadPaciente($row[4]);
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
		
		//                 0      1       2     3      4      5       6      7      8      9     10     11     12
		$query = "select Pacofi,Pacdir,Pactel,Pacmuh,Pacest,Pacfna,Pacnru,Pactru,Pacnoa,Pactea,ingcem,Pactaf,Pacpru from ".$empresa."_000100,".$empresa."_000101 ";
		$query .= " where pacdoc = '".$wcedula."'";
		$query .= "   and pactdo = '".$wtipodoc."'";
		$query .= "   and pachis = inghis ";
		$query .= "   and ingnin = '".$wing."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		echo "<center><table border=1 width='712' class=tipoTABLE1>";
		echo "<td id=tipoLGOK colspan=2>DATOS DEMOGR&Aacute;FICOS GENERALES DEL PACIENTE</td></tr>";
		$query = "select Nombre from root_000008 ";
		$query .= " where codigo = '".$row[0]."'";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<td id=tipoL01C>Oficio Paciente</td><td id=tipoL02C>".$row1[0]."</td></tr>";
		echo "<td id=tipoL01C>Direcci&oacute;n Paciente</td><td id=tipoL02C>".$row[1]."</td></tr>";
		echo "<td id=tipoL01C>Tel&eacute;fono Domicilio</td><td id=tipoL02C>".$row[2]."</td></tr>";
		$query = "select Nombre from root_000006 ";
		$query .= " where codigo = '".$row[3]."'";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<td id=tipoL01C>Ciudad de  Residencia</td><td id=tipoL02C>".$row1[0]."</td></tr>";
		$query = "select Seldes from ".$empresa."_000105 ";
		$query .= " where seltip = '25'";
		$query .= "   and selcod = '".$row[4]."'";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<td id=tipoL01C>Estado Civil</td><td id=tipoL02C>".$row1[0]."</td></tr>";
		echo "<td id=tipoL01C>Fecha de Nacimiento</td><td id=tipoL02C>".$row[5]."</td></tr>";
		echo "<td id=tipoL01C>Nombre del Responsable</td><td id=tipoL02C>".$row[6]."</td></tr>";
		echo "<td id=tipoL01C>Tel&eacute;fono del Responsable</td><td id=tipoL02C>".$row[7]."</td></tr>";
		echo "<td id=tipoL01C>Parestesco del Responsable</td><td id=tipoL02C>".$row[12]."</td></tr>";
		echo "<td id=tipoL01C>Nombre del Acompa&ntilde;ante</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "<td id=tipoL01C>Tel&eacute;fono del Acompa&ntilde;ante</td><td id=tipoL02C>".$row[9]."</td></tr>";
		$query = "select Empnom from ".$empresa."_000024 ";
		$query .= " where Empcod = '".$row[10]."'";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<td id=tipoL01C>Aseguradora</td><td id=tipoL02C>".$row1[0]."</td></tr>";
		$query = "select Seldes from ".$empresa."_000105 ";
		$query .= " where seltip = '16'";
		$query .= "   and selcod = '".$row[11]."'";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<td id=tipoL01C>Tipo de afiliaci&oacute;n</td><td id=tipoL02C>".$row1[0]."</td></tr>";
		echo "</table></center>";

	}
}
?>
