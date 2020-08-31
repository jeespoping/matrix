<html>
<head>
  	<title>MATRIX Consulta del Historial del Campo Grid Especial</title>
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
function pintar_grid($data,$struc,$id)
{
	$wsgrid="";
	if($data != "0*")
	{
		$Gridseg=explode("*",$struc);
		$Gridtit=explode("|",$Gridseg[0]);
		$Gridtip=explode("|",$Gridseg[1]);
		$wsgrid .= "<table align=center border=1 class='tipoTABLEGRID' id='div".$id."' style='display: '>";
		$wsgrid .= "<tr>";
		$wsgrid .= "<td id='tipoAL06GRID'><div class='nobreak'>ITEM</div></td>";
		for ($g=0;$g<count($Gridtit);$g++)
		{
			$wsgrid .= "<td id='tipoAL06GRID'><div class='nobreak'>".$Gridtit[$g]."</div></td>";
		}
		$wsgrid .= "</tr>";
		$Gdataseg=explode("*",$data);
		for ($g=1;$g<=$Gdataseg[0];$g++)
		{
			if($g % 2 == 0)
				$gridcolor="tipoAL02GRID1";
			else
				$gridcolor="tipoAL02GRID2";
			$Gdatadata=explode("|",$Gdataseg[$g]);
			$wsgrid .= "<tr>";
			$wsgrid .= "<td class='".$gridcolor."'><div class='nobreak'>".$g."</div></td>";
			for ($g1=0;$g1<count($Gdatadata);$g1++)
			{
				$unimed="";
				if(substr($Gridtip[$g1],0,1) == "N")
				{
					$Gridnum=explode("(",$Gridtip[$g1]);
					$unimed=substr($Gridnum[1],0,strlen($Gridnum[1])-1);
				}
				if(substr($Gridtip[$g1],0,1) == "S")
					if(strpos($Gdatadata[$g1],"-") !== false)
						$Gdatadata[$g1] = substr($Gdatadata[$g1],strpos($Gdatadata[$g1],"-")+1);
				$wsgrid .= "<td class=".$gridcolor.">".$Gdatadata[$g1]." ".$unimed."</td>";
			}
			$wsgrid .= "</tr>";
		}
		$wsgrid .= "</table>";
	}
	return $wsgrid;
}

/**********************************************************************************************************************  
	   PROGRAMA : HCE_GHE.php
	   Fecha de Liberación : 2014-05-28
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2017-06-29
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar una consulta cronologica de los campos
	   tipo Grid de cualquier formulario de la HCE
	   
	   REGISTRO DE MODIFICACIONES 
	    .2018-08-14
	   		Al reemplazar la historia e ingreso en el query obtenido en el campo Detfor de hce_000002 se agregan las 
			comillas simples para garantizar que el query sea más rápido ya que la historia e ingreso son tipo varchar
		.2017-06-29
	   		Se generaliza para poder ser llamado desde la HCE de la Clinica Las Americas 
	   	.2014-05-28
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_GHE' action='HCE_GHE.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'origen' value='".$origen."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'query' value='".$query."'>";
	$wcon = $query;
	$wing = "1";
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
	echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$origen.".jpg' id='logo'></td>";	
	echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
	echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
	echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
	echo "</table></center><br>";
	
	
	$query = "select Detfor from ".$empresa."_000002 ";
	$query .= " where Detpro = '".$wfor."'";
	$query .= "   and Detcon = ".$wcon;
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	$query = $row[0];
	
	$query=str_replace("HIS","'".$whis."'",$query);
	$query=str_replace("ING","'".$wing."'",$query);
	$err = mysql_query($query,$conex) or die("aqui ".mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if(substr($row[2],0,2) != "0*")
			{
				echo "<h3 OnClick='toggleDisplay(div".$i.")' class=tipo3G>Ingreso Numero : ".$row[3]." Fecha : ".$row[0]." Hora : ".$row[1]." (click)</h3>";
				echo pintar_grid($row[2],$row[4],$i);
			}
		}
	}
	else
		echo "<h3 class=tipo3G>SIN REGISTROS PARA ESTE PACIENTE</h3>";
					
	
}
?>
