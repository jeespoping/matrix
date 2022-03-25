<?php
include_once("conex.php");
if(!isset($CI))
{
	echo "<html>";
	echo "<body>";
	echo "<head>";
	echo "	<title>HCE EPICRISIS</title>";
}
	echo "<style type='text/css'>";
	echo ".nobreak {page-break-inside: avoid;}";
	echo "#tipoT01{color:#000000;background:#FFFFFF;font-size:5pt;font-family:Arial;font-weight:bold;width:15em;text-align:center;height:2em;}";
	echo "#tipoT02{color:#000000;background:#C3D9FF;font-size:10pt;font-family:Arial;font-weight:bold;width:65em;text-align:left;height:2em;}";
	echo ".tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}	";
	echo ".tipoGRID1{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}";
	echo ".tipoGRID2{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:justify;width:80em;}";
	echo "#tipoL01GRID{color:#000066;background:#999999;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;width:80em;}";
	echo "#tipoL02GRID{color:#000066;background:#dddddd;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;width:80em;}";
	echo ".tipoGRID3{color:#000066;background:#C3D9FF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}";
	echo ".tipoGRID4{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}";
	echo ".tipoGRID5{color:#000066;background:#dddddd;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}";
	echo ".tipoGRID6{color:#000066;background:#dddddd;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}";
	echo ".tipoGRID7{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;}";
	echo ".tipoGRID8{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;}";
	echo "#tipoL03GRID{color:#000066;background:#dddddd;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;width:80em;}";
	echo ".tipoT01{color:#000066;background:#FFFFFF;font-size:6pt;font-family:Arial;font-weight:normal;text-align:center;height:1em;}";
	echo "</style> ";
if(!isset($CI))
{
	echo "	<script>";
	echo "	function enter()";
	echo "	{";
	echo "		document.forms.HCE_Resumen.submit();";
	echo "	}";
	echo "	function toggleDisplay(id) ";
	echo "	{ ";
	echo "		if (id.style.display=='none') ";
	echo "		{ ";
	echo "			id.style.display=''; ";
	echo "		} ";
	echo "		else  ";
	echo "		{ ";
	echo "			id.style.display='none'; ";
	echo "		} ";
	echo "	} ";
	echo "	</script>";
	echo "</head>";
}

/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : HCE_Resumen.php
	   Fecha de Liberacion : 2013-09-17
	   Autor : Pedro Ortiz Tamayo
	   Version Inicial : 2013-09-17
	   Version actual  : 2020-02-20
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite visualizar la informacion
	   de la Epicrisis.
	   
	   REGISTRO DE MODIFICACIONES :
	   
	   11/03/2022 - Brigith Lagares: Se realiza estadarización del wemp_pmla y se actualiza encabezado

	   .2020-02-20
			Se crean las tablas movhos_000278 - Encabezado configuracion Resumen HCE y movhos_000279 - Detalle configuracion Resumen HCE 
			con los formularios y consecutivos de cada uno de los items a consultar en el resumen de egreso y se modifica este script 
			para que tome la configuración de dichas tablas y que el programa sea dinámico.
	   .2020-01-16
			Se agrega el formulario  de epicrisis (hce_000353) para el Motivo de Consulta y Diagnostico de Ingreso 
	   .2017-06-22
	   		Se actualizan los formularios de ingreso y egreso.
	   .2015-05-13
	   		Se adiciona consultas al formulario 71 Resumen de Egreso para Motivo de Consulta, Dx de Ingreso, Dx de Egreso.
	   .2014-03-27
	   		Se adiciona el Plan de Egreso a la informacion de la Epicrisis. 
	   .2013-09-17
	   		Release de Version Beta.
		
	
[*DOC]   		
***********************************************************************************************************************/

if(!isset($CI))
{
	$wsesion = 0;
	@session_start();
	if(!isset($_SESSION['user']))
	{
		echo "ERROR SESION CERRADA";
		$wsesion = 1;
	}
	else
		$key = substr($user,2,strlen($user));
}
if((!isset($CI) and $wsession == 0) or isset($CI))
{
	
	function consultarConfiguracionHCE($conex, $wdbmhos)
	{
		$queryConfiguracion = "SELECT Ecrcod, Ecrdes, Ecrtiq, Dcrfor, Dcrcon  
								 FROM ".$wdbmhos."_000278, ".$wdbmhos."_000279 
								WHERE Ecrest='on'
								  AND Dcrcod=Ecrcod
								  AND Dcrest='on'
							 ORDER BY Ecrord;";
		
		$resConfiguracion = mysql_query($queryConfiguracion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryConfiguracion . " - " . mysql_error());
		$numConfiguracion = mysql_num_rows($resConfiguracion);
		
		$arrayConfiguracion = array();
		if($numConfiguracion>0)
		{
			while($rowConfiguracion = mysql_fetch_array($resConfiguracion))
			{
				$arrayConfiguracion[$rowConfiguracion['Ecrcod']]['descripcionTema'] = $rowConfiguracion['Ecrdes'];
				$arrayConfiguracion[$rowConfiguracion['Ecrcod']]['tipoQuery'] = $rowConfiguracion['Ecrtiq'];
				$arrayConfiguracion[$rowConfiguracion['Ecrcod']]['formularios'][$rowConfiguracion['Dcrfor']] = $rowConfiguracion['Dcrcon'];
			}
		}
		// echo "<pre>".print_r($arrayConfiguracion, true)."</pre>";
		return $arrayConfiguracion;
	}
	 	
	echo "<form name='HCE_Resumen' action='HCE_Resumen.php' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	$cadena_html = "";
	if(!isset($CI))
	{
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing from root_000036,root_000037 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and pacced = oriced ";
		$query .= "   and pactid = oritid ";
		$query .= "   and oriori = '".$wemp_pmla."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$wpac = strtoupper($row[2])." ".strtoupper($row[3])." ".ucfirst(strtolower($row[0]))." ".ucfirst(strtolower($row[1]));
		$widen = $wtipodoc." ".$wcedula;
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
		$whis = $row[6];
		if(!isset($wing))
			$wing=$row[7];
		echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
		echo "<input type='HIDDEN' name= 'wing' value='".$wing."'>";
		echo "<input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
		echo "<input type='HIDDEN' name= 'wtipodoc' value='".$wtipodoc."'>";
		if(!isset($ok))
		{
			$query = "select Fecha_data,Hora_data,Oaedat,Oaeusu,Descripcion ";
			$query .= "  from ".$empresa."_000048,usuarios "; 
			$query .= "  where Oaehis = '".$row[6]."' ";
			$query .= "    and Oaeing = '".$wing."' ";
			$query .= "    and Oaeusu = '".$key."' ";
			$query .= "    and Oaeusu = Codigo ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$obs="";
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$obs = $row1[2];
				}
			}
		}
		$gridcolor1="tipoGRID6";
		echo "<p onclick='toggleDisplay(T01);' class='tipoT01'>Datos</p>";
		echo "<div id='T01' style='display: none'> ";
		echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
		echo "<tr><td id=tipoL01GRID colspan=3>EPICRISIS / RESUMEN DE HISTORIA</td></tr>";
		echo "<tr><td id=tipoL01GRID colspan=3>INFORMACION DEL PACIENTE</td></tr>";
		echo "<tr><td class=".$gridcolor1." colspan=2>Nombre Paciente : ".$wpac."</td><td class=".$gridcolor1.">Identificacion : ".$widen."</td></tr>";
		echo "<tr><td class=".$gridcolor1.">Historia / Ingreso : ".$whis."-".$wing."</td><td class=".$gridcolor1.">Edad : ".$wedad."</td><td class=".$gridcolor1.">Sexo : ".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01GRID colspan=3>OBSERVACIONES DE AUDITORIA EN LA EPICRISIS / RESUMEN DE HISTORIA</td></tr>";
		echo "<tr><td class=".$gridcolor1." colspan=3><textarea name='obs' cols=60 rows=10>".$obs."</textarea></td></tr>";
		echo "<tr><td id=tipoL01GRID colspan=3>ENTER<input type='checkbox' name='ok' OnClick='enter()'></td></tr>";
		echo "</table>";
		echo "</div>";
		if(isset($ok))
		{
			$query  = "select * ";
			$query .= "  from ".$empresa."_000048 "; 
			$query .= "  where Oaehis = '".$whis."' ";
			$query .= "    and Oaeing = '".$wing."' ";
			$query .= "    and Oaeusu = '".$key."' ";
			$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num3 = mysql_num_rows($err3);
			if($num3 > 0)
			{
				if(strlen($obs) > 0)
				{
					$query =  " update ".$empresa."_000048  set Oaedat = '".$obs."' ";
					$query .=  "  where Oaehis = '".$whis."' and Oaeing = '".$wing."' and Oaeusu = '".$key."'";
					$err3 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO HCE 48 : ".mysql_errno().":".mysql_error());
				}
				else
				{
					$query =  " delete from ".$empresa."_000048 ";
					$query .=  "  where Oaehis = '".$whis."' and Oaeing = '".$wing."' and Oaeusu = '".$key."'";
					$err3 = mysql_query($query,$conex) or die("ERROR BORRANDO HCE 48 : ".mysql_errno().":".mysql_error());
				}
			}
			else
			{
				if(strlen($obs) > 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$empresa = "hce";
					$query = "insert ".$empresa."_000048 (medico, fecha_data, Hora_data, Oaehis, Oaeing, Oaedat, Oaeusu, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha."','";
					$query .=  $hora."','";
					$query .=  $whis."','";
					$query .=  $wing."','";
					$query .=  $obs."','";
					$query .=  $key."',";
					$query .=  "'C-".$empresa."')";
					$err3 = mysql_query($query,$conex) or die("ERROR GRABANDO HCE 48 : ".mysql_errno().":".mysql_error());
				}
			}
		}
	}
	$hostUrl = "http://".getenv("REMOTE_ADDR");
	//                 0      1      2      3      4      5      6      7      8      9      10     11                     12            13
	$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom,".$wdbmhos."_000016.fecha_data,Ubifad from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
	$query .= " where pacced = '".$wcedula."'";
	$query .= "   and pactid = '".$wtipodoc."'";
	$query .= "   and pacced = oriced ";
	$query .= "   and pactid = oritid ";
	$query .= "   and oriori = '".$wemp_pmla."' ";
	$query .= "   and inghis = orihis ";
	$query .= "   and inging = '".$wing."' ";
	$query .= "   and ubihis = inghis "; 
	$query .= "   and ubiing = '".$wing."' ";
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
	$wpac = strtoupper($row[2])." ".strtoupper($row[3])." ".ucfirst(strtolower($row[0]))." ".ucfirst(strtolower($row[1]));
	$widen = $wtipodoc." ".$wcedula;
	$whis = $row[6];
	if(!isset($wing))
		$wing=$row[7];
	$wfei = $row[12];
	$whab = $row[10];
	$wser = $row[11];
	$weps = $row[8];
	$wfee = $row[13];
	$gridcolor1="tipoGRID1";
	$gridcolor2="tipoGRID2";
	$cadena_html .="<body style='background-color:#FFFFFF' FACE='ARIAL' LINK='BLACK'>";				
	$cadena_html .="<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
	if(!isset($CI))
	{
		$cadena_html .="<tr><td align=center id=tipoT01 colspan=3><div class='nobreak'><IMG SRC='/matrix/images/medical/root/lmatrix.jpg'></div></td>";
		$cadena_html .="<td id=tipoT02><div class='nobreak'>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;HCE - HISTORIA CLINICA ELECTRONICA&nbsp;&nbsp;Version 2022-03-11</A></div></td></tr></table><br>";
		$cadena_html .="<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
		$cadena_html .="<tr><td id=tipoL01GRID colspan=3><div class='nobreak'>INFORMACION DEMOGRAFICA</div></td></tr>";
		$cadena_html .="<tr><td class=".$gridcolor1." colspan=2><div class='nobreak'>Nombre Paciente : ".$wpac."</div></td><td class=".$gridcolor1."><div class='nobreak'>Identificacion : ".$widen."</div></td></tr>";
		$cadena_html .="<tr><td class=".$gridcolor1."><div class='nobreak'>Historia / Ingreso : ".$whis."-".$wing."</div></td><td class=".$gridcolor1."><div class='nobreak'>Edad : ".$wedad."</div></td><td class=".$gridcolor1."><div class='nobreak'>Sexo : ".$sexo."</div></td></tr>";
		$cadena_html .="<tr><td class=".$gridcolor1."><div class='nobreak'>Fecha Ingreso : ".$wfei."</div></td><td class=".$gridcolor1."><div class='nobreak'>Fecha Egreso : ".$wfee."</div></td><td class=".$gridcolor1."><div class='nobreak'>Responsable : ".$weps."</div></td></tr>";
		$cadena_html .="<tr><td id=tipoL01GRID colspan=3><div class='nobreak'>RESUMEN DE EGRESO - EPICRISIS</div></td></tr>";
	}
	
	$arrayConfiguracion = consultarConfiguracionHCE($conex, $wdbmhos);
	
	if(count($arrayConfiguracion)>0)
	{
		foreach($arrayConfiguracion as $keyConfiguracion => $valueConfiguracion)
		{
			$cadena_html .="<tr><td id=tipoL02GRID colspan=3><div class='nobreak'>".$valueConfiguracion['descripcionTema']."</div></td></tr>";
			
			if($valueConfiguracion['tipoQuery']=="S") // Query sencillo
			{
				$query = "";
				foreach($valueConfiguracion['formularios'] as $formulario => $consecutivos)
				{
					$query .= "SELECT movdat
								FROM ".$empresa."_".$formulario."
							   WHERE movhis='".$whis."'
								 AND moving='".$wing."'
								 AND movcon=".$consecutivos."
								 
								 UNION ";
				}
				
				$query = substr($query,0,-6).";";
				
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$var="";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$var = $row1[0]."<br>";
					}
					$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$var."</div></td></tr>";
				}
				
			}
			else if($valueConfiguracion['tipoQuery']=="D") // Query detallado
			{
				$query = "";
				foreach($valueConfiguracion['formularios'] as $formulario => $consecutivos)
				{
					$query .= "SELECT Fecha_data,Hora_data,movdat
								FROM ".$empresa."_".$formulario."
							   WHERE movhis='".$whis."'
								 AND moving='".$wing."'
								 AND movcon IN (".$consecutivos.")
								 
								 UNION ";
				}
				
				$query = substr($query,0,-6)." order by 1,2 ;";
				
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$fecant="";
					$var="";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						if($fecant != $row1[0])
						{
							if($fecant != "")
								$var .= "<br><br>";
							$fecant = $row1[0];
							$var .= "<u>".$row1[0]."</u><br>";
						}
						$var .= $row1[2]."<br>";
					}
					$cadena_html .="<tr><td class=".$gridcolor2." colspan=3>".$var."</td></tr>";
				}
			}
		}
	}
	
	// $cadena_html .="<tr><td id=tipoL02GRID colspan=3><div class='nobreak'>Motivo de Consulta</div></td></tr>";
	// $query = "select movdat ";
	// $query .= "  from ".$empresa."_000051 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 4 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000134 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 5 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000138 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 9 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000244 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 173 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000137 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 86 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000071 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 7 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000360 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 12 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000353 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 18 ";
	// // echo "<pre>".print_r($query,true)."</pre>";
	// $err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	// $num1 = mysql_num_rows($err1);
	// if($num1 > 0)
	// {
		// $var="";
		// for ($i=0;$i<$num1;$i++)
		// {
			// $row1 = mysql_fetch_array($err1);
			// $var = $row1[0]."<br>";
		// }
		// $cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$var."</div></td></tr>";
	// }
	// $cadena_html .="<tr><td id=tipoL02GRID colspan=3><div class='nobreak'>Evoluciones</div></td></tr>";
	// $query  = "select Fecha_data,Hora_data,movdat ";
	// $query .= "  from ".$empresa."_000139 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon in (5,7) ";
	// $query .= " UNION  ";
	// $query .= "select Fecha_data,Hora_data,movdat ";
	// $query .= "  from ".$empresa."_000069 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon in (76,42,50) ";
	// $query .= " UNION  ";
	// $query .= "select Fecha_data,Hora_data,movdat ";
	// $query .= "  from ".$empresa."_000367 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon in (57,67,53) ";
	// $query .= " UNION  ";
	// $query .= "select Fecha_data,Hora_data,movdat ";
	// $query .= "  from ".$empresa."_000251 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon in (6,8,10,14) ";
	// $query .= " UNION  ";
	// $query .= "select Fecha_data,Hora_data,movdat ";
	// $query .= "  from ".$empresa."_000184 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon in (5,18) ";
	// $query .= " UNION  ";
	// $query .= "select Fecha_data,Hora_data,movdat ";
	// $query .= "  from ".$empresa."_000175 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon in (14,16) ";
	// $query .= " UNION  ";
	// $query .= "select Fecha_data,Hora_data,movdat ";
	// $query .= "  from ".$empresa."_000071 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon in (88,87,103) ";
	// $query .= " UNION  ";
	// $query .= "select Fecha_data,Hora_data,movdat ";
	// $query .= "  from ".$empresa."_000077 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon in (17,19) ";
	// $query .= " order by 1,2 ";
	// $err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	// $num1 = mysql_num_rows($err1);
	// if($num1 > 0)
	// {
		// $fecant="";
		// $var="";
		// for ($i=0;$i<$num1;$i++)
		// {
			// $row1 = mysql_fetch_array($err1);
			// if($fecant != $row1[0])
			// {
				// if($fecant != "")
					// $var .= "<br><br>";
				// $fecant = $row1[0];
				// $var .= "<u>".$row1[0]."</u><br>";
			// }
			// $var .= $row1[2]."<br>";
		// }
		// //$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$var."</div></td></tr>";
		// $cadena_html .="<tr><td class=".$gridcolor2." colspan=3>".$var."</td></tr>";
	// }
	// $cadena_html .="<tr><td id=tipoL02GRID colspan=3><div class='nobreak'>Diagnostico de Ingreso</div></td></tr>";
	// $query = "select movdat ";
	// $query .= "  from ".$empresa."_000051 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 156 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000138 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 139 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000360 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 68 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000244 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 157 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000137 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 59 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000071 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 82 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000353 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 53 ";
	// // echo "<pre>".print_r($query,true)."</pre>";
	// $err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	// $num1 = mysql_num_rows($err1);
	// if($num1 > 0)
	// {
		// $var="";
		// for ($i=0;$i<$num1;$i++)
		// {
			// $row1 = mysql_fetch_array($err1);
			// $var = $row1[0]."<br>";
		// }
		// $cadena_html .="<tr><td class=".$gridcolor2." colspan=3>".$var."</td></tr>";
	// }
	// $cadena_html .="<tr><td id=tipoL02GRID colspan=3><div class='nobreak'>Diagnostico de Egreso</div></td></tr>";
	// $query = "select movdat ";
	// $query .= "  from ".$empresa."_000071 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 111 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000204 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 103 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000248 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 89 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000195 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 52 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000108 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 81 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000353 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 63 ";
	// $err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	// $num1 = mysql_num_rows($err1);
	// if($num1 > 0)
	// {
		// $var="";
		// for ($i=0;$i<$num1;$i++)
		// {
			// $row1 = mysql_fetch_array($err1);
			// $var = $row1[0]."<br>";
		// }
		// $cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$var."</div></td></tr>";
	// }
	// $cadena_html .="<tr><td id=tipoL02GRID colspan=3><div class='nobreak'>Plan de Egreso</div></td></tr>";
	// $query = "select movdat ";
	// $query .= "  from ".$empresa."_000071 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 93 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000204 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 109 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000248 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 87 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000195 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 58 ";
	// $query .= " UNION  ";
	// $query .= "select movdat ";
	// $query .= "  from ".$empresa."_000353 "; 
	// $query .= "  where movhis = '".$whis."' ";
	// $query .= "    and moving = '".$wing."' ";
	// $query .= "    and movcon = 68 ";
	// $err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	// $num1 = mysql_num_rows($err1);
	// if($num1 > 0)
	// {
		// $var="";
		// for ($i=0;$i<$num1;$i++)
		// {
			// $row1 = mysql_fetch_array($err1);
			// $var = $row1[0]."<br>";
		// }
		// $cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$var."</div></td></tr>";
	// }
	$query  = "select ".$empresa."_000036.Fecha_data,".$empresa."_000036.Hora_data,firpro,firusu,firrol,Descripcion,Medtdo,Meddoc,Medreg,Espnom from ".$empresa."_000036,usuarios,".$wdbmhos."_000048,".$wdbmhos."_000044 ";
	$query .= "  where firhis = '".$whis."' ";
	$query .= "    and firing = '".$wing."' ";
	$query .= "    and firpro in ('000071','000204','000248')  ";
	$query .= "    and firusu = Codigo  ";
	$query .= "    and Codigo = Meduma  ";
	$query .= "    and firrol = Espcod  ";
	$query .= "   order by 1 desc,2 desc ";
	$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num1 = mysql_num_rows($err1);
	if($num1 > 0)
	{
		$row1 = mysql_fetch_array($err1);
		$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$row1[5]." Identificacion : ".$row1[6]." ".$row1[7]." Registro : ".$row1[8]." Profesi&oacute;n o Especialidad : ".$row1[9]." Fecha : ".$row1[0]." Hora : ".$row1[1];
		$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$datfirma."</div></td></tr>";
		if(file_exists("/var/www/matrix/images/medical/hce/Firmas/".$row1[3].".png"))
			if(!isset($CI))
				$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'><IMG SRC='/matrix/images/medical/hce/Firmas/".$row1[3].".png'></div></td></tr>";
			else
				$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'><IMG SRC='".$hostUrl."/matrix/images/medical/hce/Firmas/".$row1[3].".png'></div></td></tr>";
	}
	else
	{
		$query  = "select ".$empresa."_000036.Fecha_data,".$empresa."_000036.Hora_data,firpro,firusu,firrol,Descripcion,Medtdo,Meddoc,Medreg,Espnom from ".$empresa."_000036,usuarios,".$wdbmhos."_000048,".$wdbmhos."_000044 ";
		$query .= "  where firhis = '".$whis."' ";
		$query .= "    and firing = '".$wing."' ";
		$query .= "    and firpro in ('000069','000251') ";
		$query .= "    and firusu = Codigo  ";
		$query .= "    and Codigo = Meduma  ";
		$query .= "    and firrol = Espcod  ";
		$query .= " order by 1 desc,2 desc ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$row1[5]." Identificacion : ".$row1[6]." ".$row1[7]." Registro : ".$row1[8]." Profesi&oacute;n o Especialidad : ".$row1[9]." Fecha : ".$row1[0]." Hora : ".$row1[1];
			$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$datfirma."</div></td></tr>";
			if(file_exists("/var/www/matrix/images/medical/hce/Firmas/".$row1[3].".png"))
				if(!isset($CI))
					$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'><IMG SRC='/matrix/images/medical/hce/Firmas/".$row1[3].".png'></div></td></tr>";
				else
					$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'><IMG SRC='".$hostUrl."/matrix/images/medical/hce/Firmas/".$row1[3].".png'></div></td></tr>";
		}
	}
	$query  = "select Oaedat,Oaeusu,Medtdo,Meddoc,Medreg,".$empresa."_000048.Fecha_data,".$empresa."_000048.Hora_data,Medno1,Medno2,Medap1,Medap2 from ".$empresa."_000048,".$wdbmhos."_000048 "; 
	$query .= "  where Oaehis = '".$whis."' ";
	$query .= "    and Oaeing = '".$wing."' ";
	$query .= "    and Oaeusu = Meduma ";
	$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num1 = mysql_num_rows($err1);
	if($num1 > 0)
	{
		$cadena_html .="<tr><td id=tipoL02GRID colspan=3><div class='nobreak'>Observaciones de Auditor&iacute;a</div></td></tr>";
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($err1);
			$wmed = strtoupper($row1[7])." ".strtoupper($row1[8])." ".strtoupper($row1[9])." ".strtoupper($row1[10]);
			$row1[0] = str_replace(chr(10),"<br>",$row1[0]);
			$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$row1[0]."</div></td></tr>";
			$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$wmed." Identificacion : ".$row1[2]." ".$row1[3]." Registro : ".$row1[4]." Profesi&oacute;n o Especialidad : Auditor M&eacute;dico Fecha : ".$row1[5]." Hora : ".$row1[6];
			$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><div class='nobreak'>".$datfirma."</div></td></tr>";
			if(file_exists("/var/www/matrix/images/medical/hce/Firmas/".$row1[1].".png"))
				if(!isset($CI))
					$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><IMG SRC='/matrix/images/medical/hce/Firmas/".$row1[1].".png'></td></tr>";
				else
					$cadena_html .="<tr><td class=".$gridcolor2." colspan=3><IMG SRC='".$hostUrl."/matrix/images/medical/hce/Firmas/".$row1[1].".png'></td></tr>";
		}
	}
	$cadena_html .="</table><br>";
	//$cadena_html .="<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
	//$cadena_html .="<tr><td id=tipoL01GRID colspan=6><div class='nobreak'>Medicamentos Aplicados No POS</div></td></tr>";
	//$gridcol = "tipoGRID5";
	//$cadena_html .="<tr><td class=".$gridcol."><div class='nobreak'>Fecha</div></td><td class=".$gridcol."><div class='nobreak'>Codigo</div></td><td class=".$gridcol."><div class='nobreak'>Generico</div></td><td class=".$gridcol."><div class='nobreak'>Comercial</div></td><td class=".$gridcol."><div class='nobreak'>Dosis</div></td><td class=".$gridcol."><div class='nobreak'>Unidad</div></td></tr>";
	//$query = "SELECT Artcod, Artgen, Artcom, Aplufr, Aplfec, Aplufr, sum( Apldos ) FROM ".$wdbmhos."_000015 a, ".$wdbmhos."_000026 b ";
	//$query .= " WHERE aplhis = '".$whis."' ";
	//$query .= "   AND apling = '".$wing."' ";
	//$query .= "   AND aplest = 'on'";
	//$query .= "   AND artcod = aplart";
	//$query .= "   AND artpos = 'N'";
	//$query .= " group by Artcod, Artgen, Artcom, Aplufr, Aplfec, Aplufr";
	//$query .= " UNION ";
	//$query .= " SELECT Artcod, Artgen, Artcom, Aplufr, Aplfec, Aplufr, sum( Apldos ) FROM ".$wdbmhos."_000015 a, cenpro_000002 b, cenpro_000001 f";
	//$query .= " WHERE aplhis = '".$whis."' ";
	//$query .= "  AND apling = '".$wing."' ";
	//$query .= "  AND aplest = 'on'";
	//$query .= "  AND b.artcod = aplart ";
	//$query .= "  AND b.arttip = f.tipcod ";
	//$query .= "  AND f.tipcdo != 'on' ";
	//$query .= "  AND b.artcod IN (SELECT c.Pdepro FROM cenpro_000003 c, cenpro_000009 d, ".$wdbmhos."_000026 e WHERE pdepro = b.artcod AND appcod = pdeins AND apppre = e.artcod AND e.artpos = 'N' AND appest = 'on')";
	//$query .= " group by Artcod, Artgen, Artcom, Aplufr, Aplfec, Aplufr";
	//$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	//$num1 = mysql_num_rows($err1);if(!isset($CI))
	//if($num1 > 0)
	//{
		//for ($i=0;$i<$num1;$i++)
		//{
			//$row1 = mysql_fetch_array($err1);
			//if($i % 2 == 0)
				//$gridcol = "tipoGRID3";
			//else
				//$gridcol = "tipoGRID4";
			//$cadena_html .="<tr><td class=".$gridcol."><div class='nobreak'>".$row1[4]."</div></td><td class=".$gridcol."><div class='nobreak'>".$row1[0]."</div></td><td class=".$gridcol."><div class='nobreak'>".$row1[1]."</div></td><td class=".$gridcol."><div class='nobreak'>".$row1[2]."</div></td><td class=".$gridcol."><div class='nobreak'>".$row1[6]."</div></td><td class=".$gridcol."><div class='nobreak'>".$row1[5]."</div></td></tr>";
		//}
	//}
	//$cadena_html .="</table><br>";
	$cadena_html .="<br><table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
	$cadena_html .="<tr><td id=tipoL03GRID><div class='nobreak'>Durante la Atenci&oacute;n se Utilizaron las Siguientes Tecnolog&iacute;as en Salud</div></td></tr>";
	$gridcol = "tipoGRID5";
	//$cadena_html .="<tr><td class=".$gridcol."><div class='nobreak'>Observacion</div></td></tr>";
	
	$query = "SELECT Usuarios FROM ".$wdbmhos."_000034 ";
	$query .= " WHERE Codigo = 'CTC' ";
	$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num1 = mysql_num_rows($err1);

	if($num1 > 0)
	{
		$row1 = mysql_fetch_array($err1);
		$usuariosCTC = $row1[0];
	}
	
	$query = "SELECT Fecha_data, Bitobs, Bitusr FROM ".$wdbmhos."_000021 ";
	$query .= " WHERE Bithis = '".$whis."' ";
	$query .= "   AND Biting = '".$wing."' ";
	$query .= "   AND Bittem = 'CTC'";
	$query .= " Order by 1 ";
	$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num1 = mysql_num_rows($err1);

	if($num1 > 0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($err1);
			if($i % 2 == 0)
				$gridcol = "tipoGRID7";
			else
				$gridcol = "tipoGRID8";
			if(strpos($usuariosCTC,$row1[2]) !== false)
				$cadena_html .="<tr><td class=".$gridcol."><div class='nobreak'>".$row1[1]."</div></td></tr>";
		}
	}
	$cadena_html .="</table><br><br>";
	if(!isset($CI))
		echo "<IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'></IMG><br><br>";
	echo $cadena_html;
}		
if(!isset($CI))
{
	echo "</body>";
	echo "</html>";
}
?>
