<?php
include_once("conex.php");





include_once("root/comun.php");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
conexionOdbc($conex, $wbasedato, $conexUnix, 'facturacion');
$conexUnix = odbc_pconnect('facturacion','informix','sco') or die("No se ralizo Conexion con Unix");

$dia_anterior = date("Y-m-d", strtotime("$wfecha_actual -1 day"));

$wformulario = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormHceIncapacidades');
$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');
$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$responsablesIncapacidades = consultarAliasPorAplicacion($conex, $wemp_pmla, 'responsablesIncapacidades');
$ingresoAccidenteTransito = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ingresoAccidenteTransito');

$respInca = explode("-",$responsablesIncapacidades);
$respIncapacidades = implode("','",$respInca);

$selectnit = "SELECT Empnit
			    FROM root_000050
			   WHERE Empcod = '".$wemp_pmla."' ";
$resnit = mysql_query($selectnit,$conex) or die ("Query : ".$selectnit." - ".mysql_error());
$rownit = mysql_fetch_assoc($resnit);
$nit_empresa = explode("-",$rownit['Empnit']);

/*
//=======================================================================
	Modificaciones
//=======================================================================

	Septiembre 22 de 2017 Jonatan:  Se agregan los titulos de las columnas al archivo de hostpitalizados.
*/

//----------Se organiza el correo para ser enviado a los interesados
	$wcorreopmla = consultarAliasPorAplicacion( $conex, $wemp_pmla, "CorreoAdmisiones");
	$wcorreopmla = explode("--", $wcorreopmla );
	$wpassword   = $wcorreopmla[1];
	$wremitente  = $wcorreopmla[0];
	$datos_remitente = array();
	$datos_remitente['email']	= $wremitente;
	$datos_remitente['password']= $wpassword;
	$datos_remitente['from'] = $wremitente;
	$datos_remitente['fromName'] = $wremitente;

	$q_inc = "  SELECT * 
		          FROM ".$wbasedato."_000236
			     WHERE Plaest = 'on'
				   AND Platip = 'I'";
	$res_inc = mysql_query($q_inc,$conex) or die("Error en el query: ".$q_inc."<br>Tipo Error:".mysql_error());

	$array_ent_inc = array();
	
	while($row_inc = mysql_fetch_array($res_inc)){

		$array_ent_inc[$row_inc['Planit']] = $row_inc;
		$array_ent_inc[$row_inc['Planit']]['destinatarios'] = $row_inc['Plaema'];
		$array_ent_inc[$row_inc['Planit']]['dia'] = $row_inc['Pladia'];

	}

	$array_datos = array();
	$array_incap_enviados = array();
		
	if(count($array_ent_inc) > 0)	{		
		echo "Envio correo incapacitados<br>";	
	}
	
	foreach($array_ent_inc as $key => $value1){
			

		$loginc = "SELECT count(*) as cuantosinc
					 FROM ".$wbasedato."_000237
					WHERE Lognit = '".$key."'
					  AND Logtip = 'I'
					  AND Logest = 'on'
					  AND Fecha_data = '".date("Y-m-d")."' ";
		$resinc = mysql_query($loginc,$conex) or die ("Query : ".$loginc." - ".mysql_error());
		$rowinc = mysql_fetch_assoc($resinc);
			
		if($rowinc['cuantosinc'] == 0){		
			
			$filtro_consulta = "AND ".$whce."_000036.Fecha_data = '".$dia_anterior."'";
			
			$logi = "  SELECT Empnom
						 FROM ".$wcliame."_000024
						WHERE Empcod = '".$key."'";
			$resi = mysql_query($logi,$conex) or die ("Query : ".$logi." - ".mysql_error());
			$rowi = mysql_fetch_assoc($resi);			
			
			$texto_mail = "Buenos dias, el presente archivo contiene la informacion de los pacientes que registraron incapacidades en la fecha ".$dia_anterior." para la entidad ".$rowi['Empnom']." con nit ".$key."";
			
			//Consulta del envio inicial, si ya tiene registros solo consulta el dia anterior.
			$loginc1 = "SELECT count(*) as cuantosinicial
						 FROM ".$wbasedato."_000237
						WHERE Lognit = '".$key."'
						  AND Logtip = 'I'
						  AND Logest = 'on'";
			$resinc1 = mysql_query($loginc1,$conex) or die ("Query : ".$loginc1." - ".mysql_error());
			$rowinc1 = mysql_fetch_assoc($resinc1);
			
			if($rowinc1['cuantosinicial'] == 0){	
				
				$filtro_consulta = "AND ".$whce."_000036.Fecha_data BETWEEN '2017-01-01' AND '".$dia_anterior."'";
				$texto_mail = "Buenos dias, el presente archivo contiene la informacion desde el 1 de Enero de 2017 hasta el dia de ayer, a partir de mañana se enviará un archivo con las incapacidades del dia anterior.";
			}		
			
			$query = "  SELECT *
						  FROM ".$whce."_000036, ".$whce."_".$wformulario.", root_000036, root_000037, usuarios, ".$wbasedato."_000016, ".$wcliame."_000024
						 WHERE Firhis = Movhis
						   AND Firing = Moving
						   AND Firpro = '".$wformulario."'
						   AND Firfir = 'on'
						   AND Orihis = Firhis
						   AND Oriing = Firing
						   AND Orihis = Movhis
						   AND Oriing = Moving
						   AND Pacced = Oriced
						   AND Pactid = Oritid
						   AND movusu = codigo
						   AND Inghis = Firhis
						   AND Inging = Firing
						   AND Inghis = Movhis
						   AND Inging = Moving
						   AND Empcod = Ingres
						   AND Empnit = '".$key."' 
						   AND ".$whce."_000036.Fecha_data = ".$whce."_".$wformulario.".Fecha_data
						   AND ".$whce."_000036.Hora_data = ".$whce."_".$wformulario.".Hora_data
						   $filtro_consulta";
			$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

			while($row = mysql_fetch_assoc($res)){
				
				$llave = $row['Firhis']."-".$row['Firing'];
				$array_datos[$row['Ingres']][$llave][$row['movcon']] = $row['movdat'];	
				$array_datos[$row['Ingres']][$llave]['cedula'] = $row['Pacced'];	
				$array_datos[$row['Ingres']][$llave]['tipo_doc'] = $row['Pactid'];	
				$array_datos[$row['Ingres']][$llave]['nombre_medico'] = $row['Descripcion'];	
				$array_datos[$row['Ingres']][$llave]['responsable'] = $row['Ingres'];	
				
				$soat = "SELECT Ingcai
						   FROM ".$wcliame."_000101
						  WHERE Inghis = '".$row['Firhis']."'
							AND Ingnin = '".$row['Firing']."' ";
				$ressoat = mysql_query($soat,$conex) or die ("Query : ".$soat." - ".mysql_error());
				$rowsoat = mysql_fetch_assoc($ressoat);
				
				if($rowsoat['Ingcai'] == $ingresoAccidenteTransito){		
					$array_datos[$row['Ingres']][$llave]['soat'] = 'S';		
				}else{
					$array_datos[$row['Ingres']][$llave]['soat'] = 'N';		
				}
				
			}
			
			// echo "<pre>";
			// print_r($array_datos);
			// echo "</pre>";		
			
			$incapacitados_adjunto = $key.".csv";	
			$file = fopen( '/var/www/matrix/planos/archivosplanosentidades/incapacitados/'.$incapacitados_adjunto, "w" );
			
			$datos = "";
			$datos .= "Tipo de identificacion;Numero de identificacion;Origen;Fecha de inicio;Duracion;Fecha final;Clasificacion;Diagnostico;Tipo identificación de la institucion;Numero de identificacion institucion;Nombre medico atendio;SOAT;Tipo de atencion". PHP_EOL;
			
			if(!empty($array_datos)){
			
				foreach($array_datos as $nit => $array_informacion){
								
					foreach($array_informacion as $his_ing => $value){
						
						$diagnostico_array = explode(",", strip_tags($value[16]));
						$diagnosticos = $diagnostico_array[0];
						$datos_diag = explode(".",$diagnosticos);
						$cie10_array = explode("-",$datos_diag[0]);
						$cie10 = explode(" ",$cie10_array[2]);	
						
						//$origen = explode("-", $value[15]);

						$tipo_atencion = explode("-",$value[17]);	
						
						$cco = "  SELECT Ccourg
									 FROM ".$wbasedato."_000011
									WHERE Ccocod = '".$tipo_atencion[0]."'";
						$res_cco = mysql_query($cco,$conex) or die ("Query : ".$cco." - ".mysql_error());
						$row_cco = mysql_fetch_assoc($res_cco);
						
						$cod_tipo_atencion = "Consulta programada";
						
						if($row_cco['Ccourg'] == 'on'){
							
							$cod_tipo_atencion = "Urgencias";
						}
						
						$prorroga = explode("-",$value[13]);
						
						if($prorroga[1] == 'Si'){
							
							$prorroga = "Prorroga";
							
						}else{
							
							$prorroga = "Inicial";
						}
						
						//															 origen	,   fecha_inicio    ,	 duracion	    ,  fecha_final ,     clasif      , diagnostico  , tipo id entidad ,   nit_entidad	,       nombre_medico	    ,     SOAT	   , tipo de atencion															
						$datos .= trim($value['tipo_doc']).";".trim($value['cedula']).";EG;".trim($value[5]).";".trim($value[7]).";".trim($value[6]).";".$prorroga.";".trim($cie10[0]).";NIT;".$nit_empresa[0].";".trim($value['nombre_medico']).";".$value['soat'].";".$cod_tipo_atencion."". PHP_EOL;
					}
				}
				
				fwrite($file, $datos);			
				fclose($file);
				
				$message  = "<html><body>";

				$message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";

				$message .= "<tr><td>";

				$message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";

				$message .= "<thead>
								<tr height='80' align='left'>
									<th colspan='4' style='background-color:#ffffff; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:20px;' >
									<img width='110px' height='70px' src='../../images/medical/root/clinica.JPG'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Archivo plano pacientes con incapacidad</th>
								</tr>
							 </thead>";			
				
				$message .= "<tbody>
							   <tr>
							   <td colspan='4' style='padding:15px;'>
							   <p style='font-size:18px;'>".$texto_mail."</p>
							   </tr>
							   <tr height='80'>
							   <hr />
							   <td colspan='4' align='center' style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:24px; '>
							   </td>
							   </tr>
							</tbody>";

				$message .= "</table>";

				$message .= "</td></tr>";
				$message .= "</table>";

				$message .= "</body></html>";

				$mensaje = $message;
				
				$ruta_archivo = '/var/www/matrix/planos/archivosplanosentidades/incapacitados/'.$incapacitados_adjunto;	 
				
				$wdestinatarios = explode(",", $value1['destinatarios']);
				
				$wasunto 		= "Envio incapacitados por Clinica las Americas";
				$altbody 		= "Cordial saludo,<br> \n\n Archivo de incapacidades Clinica las Americas";
				//$sendToEmail = sendToEmail($wasunto,$mensaje,$altbody, $datos_remitente, $wdestinatarios, $ruta_archivo, $incapacitados_adjunto );	
				
				 $q = " INSERT INTO ".$wbasedato."_000237 (   Medico  ,      Fecha_data     ,   Hora_data       ,   Lognit , Logtip , Logest ,  Seguridad        ) "
							 ."            VALUES (  'movhos' ,  '".date("Y-m-d")."','".date("H:m:s")."', '".$key."', 'I',   'on' , 'C-movhos')";  
				 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
				
				$wdestinatarios_log = implode(",", $wdestinatarios);
				
				echo $sendToEmail['mensError']." ".$key." - ".$value1['destinatarios']."<br>";
				
				$array_incap_enviados[$key] = $wdestinatarios_log;
			
			}
		}
	}
		
	if(!empty($array_incap_enviados)){
				
		//Envio certificacion correo
		//ConfirmacionEnvioIncapacitados
		$correoconfirmacionEnvioIncapacitados = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConfirmacionEnvioIncapacitados');
		$correoconfirmacionEnvioIncapacitados = explode(",", $correoconfirmacionEnvioIncapacitados);
		$wasuntologi = "Confirmacion incapacitados por Clinica las Americas";
		$altbodylogi = "";
		$mensajelogi .= "<thead>
							<tr height='80' align='left'>
								<th colspan='4' style='background-color:#ffffff; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:20px;' >
								<img width='110px' height='70px' src='../../images/medical/root/clinica.JPG'><br><br>Confirmación envio pacientes incapacitados para las siguientes entidades:</th>
							</tr>
						 </thead><br><br> ";
		$mensajelogi .= "<table border='1' width='600px' cellpadding='0' cellspacing='0' border='0'>";
		$mensajelogi .= "<tr class='encabezadoTabla'>";
		$mensajelogi .= "<td align=center><b>Nit</b></td><td align=center><b>Entidad Inc</b></td><td align=center><b>Correo</b></td>";
		$mensajelogi .= "</tr>";
		
			foreach($array_incap_enviados as $keyi => $valuei){
				
				$logent = "SELECT Empnom
							 FROM ".$wcliame."_000024
							WHERE Empcod = '".$keyi."'";
				$resent = mysql_query($logent,$conex) or die ("Query : ".$loginc." - ".mysql_error());
				$rowent = mysql_fetch_assoc($resent);
				
				$mensajelogi .= "<tr>";
				$mensajelogi .= "<td>".$keyi."</td><td>".$rowent['Empnom']."</td><td>".$valuei."</td>";
				$mensajelogi .= "</tr>";
				
			}
			
		$mensajelogi .= "</table>";
		//$sendToEmail = sendToEmail($wasuntologi,$mensajelogi,$altbodylogi, $datos_remitente, $correoconfirmacionEnvioIncapacitados );
		
	}
	
	//=================================================================================================================================
	//timestamp(concat(".$wbasedato."_000018.Fecha_data,' ', ".$wbasedato."_000018.Hora_data )) < timestamp(DATE_SUB( NOW() , INTERVAL 24 HOUR ) ) AND 
	// Pacientes hospitalizados.
						
	echo $query = "SELECT * FROM (
			  SELECT Ubihis AS pachis, Ubiing AS pacnum, Pacced, Pactid, CONCAT( c100.Pacno1, ' ', c100.Pacno2 ) AS pacnom, c100.pacap1, c100.pacap2, Habcod AS trahab, 'H' AS pachos, ".$wbasedato."_000018.Fecha_data AS pacfec, ".$wbasedato."_000018.Hora_data AS pachor, ingpol, pactel, Ingcem AS empnit, Ingpla as pacmrepla
				FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, root_000037, root_000036, ".$wbasedato."_000016 AS m16, ".$wcliame."_000100 AS c100, ".$wcliame."_000101 AS c101
			   WHERE ubiald =  'off'
				AND ubisac = Ccocod
				AND Ccohos =  'on'
				AND ubihis = Habhis
				AND ubiing = Habing
				AND ubihis = orihis
				AND ubiing = oriing
				AND oriori =  '".$wemp_pmla."'
				AND oriced = pacced
				AND oritid = pactid
				AND orihis = m16.inghis
				AND oriing = m16.inging
				AND c101.Inghis = m16.inghis
				AND c101.Ingnin = m16.inging
				AND Pacdoc = pacced
				AND c100.Pachis = c101.inghis
			  UNION
			 SELECT Ubihis AS pachis, Ubiing AS pacnum, Pacced, Pactid, CONCAT( c100.Pacno1, ' ', c100.Pacno2 ) AS pacnom, c100.pacap1, c100.pacap2, 'URG' AS trahab, 'U' AS pachos, ".$wbasedato."_000018.Fecha_data AS pacfec, ".$wbasedato."_000018.Hora_data AS pachor, ingpol, pactel, Ingcem AS empnit, Ingpla as pacmrepla
			  from ".$wbasedato."_000018,".$wbasedato."_000011,root_000037,root_000036,".$wbasedato."_000016 AS m16, ".$wcliame."_000100 AS c100, ".$wcliame."_000101 AS c101, ".$whce."_000022
			  WHERE ubiald = 'off' 
				AND ubisac = Ccocod  
				AND Ccourg = 'on'  
				AND ubihis = orihis 
				AND ubiing = oriing 
				AND oriori = '".$wemp_pmla."' 
				AND oriced = pacced 
				AND oritid = pactid  
				AND orihis = m16.inghis 
				AND oriing = m16.inging 
				AND orihis = mtrhis 
				AND oriing = mtring 
				AND m16.inghis = mtrhis 
				AND m16.inging = mtring  
				AND c101.Inghis = m16.inghis
				AND c101.Ingnin = m16.inging
				AND c100.Pachis = c101.inghis) AS t";
	$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

	$arreglo = array();
	
	 while ($row = mysql_fetch_array($res)){
				
		$queryp = "SELECT plades
			         FROM ".$wcliame."_000153
			        WHERE placod = '".$row['pacmrepla']."'";
		$err_op = mysql_query($queryp, $conex) or die("Error: ".mysql_errno()." - en el query: ".$queryp." - ".mysql_error());
		$rowp = mysql_fetch_array($err_op);		
		$empresa = trim($row['empnit']);
		
		$wvlr = 0;
		$query1 = " SELECT count(*) as conteo
					  FROM FACARDET
					 WHERE CARDETFAC = 'S'
					   AND CARDETVFA = 0
					   AND CARDETANU = '0'
					   AND CARDETHIS = '".$row['pachis']."'
					   AND CARDETNUM = '".$row['pacnum']."'";
		$err_o1 = odbc_do($conexUnix,$query1) or die (odbc_errormsg());
		$num = odbc_fetch_array($err_o1);
	
		if($num['conteo'] > 0){
			
			$query1a = "SELECT SUM(CARDETTOT) as sumCARDETTOT
						  FROM FACARDET
						 WHERE CARDETFAC = 'S'
						   AND CARDETVFA = 0
						   AND CARDETANU = '0'
						   AND CARDETHIS = '".$row['pachis']."'
						   AND CARDETNUM = '".$row['pacnum']."'";
			$err_o1a = odbc_do($conexUnix,$query1a) or die (odbc_errormsg());			
			$wvlr = odbc_result($err_o1a,'sumCARDETTOT');
				
		
		}
		
		$wliq = 0;
		//{Total habitaciones Anteriores }
		//{Que no esten liquidadas ya en facardet}
		$query2 = "SELECT count(*) as conteo1
					 FROM inmtra
				    WHERE TRALIQ = '0'  
				      AND TRAEGR is not null
					  AND TRAHIS = '".$row['pachis']."'
					  AND TRANUM = '".$row['pacnum']."'";
		$err_o2 = odbc_do($conexUnix,$query2) or die (odbc_errormsg());
		$num2 = odbc_fetch_array($err_o2);
		
		if($num2['conteo1'] > 0){
			
			$query2 = "SELECT SUM(TRAVAL*TRADFA) as sumvaldfa
						FROM inmtra
					   WHERE TRALIQ = '0'  
						 AND TRAEGR is not null
						 AND TRAHIS = '".$row['pachis']."'
						 AND TRANUM = '".$row['pacnum']."'";
			$err_o2 = odbc_do($conexUnix,$query2) or die (odbc_errormsg());			
			$wliq = odbc_result($err_o2,'sumvaldfa');			
		}
		
		
		$wsin = 0;
		//{Total habitacion Actual a la fecha }
		//{Que no esten liquidadas ya en facardet}
		$query3 = "SELECT count(*) as conteo3
		             FROM inmtra
				    WHERE TRALIQ = '0'       
					  AND traegr is null
					  AND TRAHIS = '".$row['pachis']."'
					  AND TRANUM = '".$row['pacnum']."'";
		$err_o3 = odbc_do($conexUnix,$query3) or die (odbc_errormsg());
		$num2 = odbc_fetch_array($err_o3);	
		
		if($num2['conteo3'] > 0){
			
			$query3 = "SELECT SUM(TRAVAL*(TODAY - TRAING)) as sumwsin
						 FROM inmtra
						WHERE TRALIQ = '0'       
						  AND traegr is null
						  AND TRAHIS = '".$row['pachis']."'
						  AND TRANUM = '".$row['pacnum']."'";
			$err_o3 = odbc_do($conexUnix,$query3) or die (odbc_errormsg());			
			$wsin = odbc_result($err_o3,'sumwsin');
			
		}
		 		
		$wtot = $wvlr+$wliq+$wsin;		
		
		//Arreglo de pacientes hospitalizados
		if($row['pachos'] == 'H'){
			$arreglo[$empresa][$row['pachis'].$row['pacnum']] = $row ;
			$arreglo[$empresa][$row['pachis'].$row['pacnum']]['plan'] = $rowp['plades'];
			$arreglo[$empresa][$row['pachis'].$row['pacnum']]['cuenta_total'] = $wtot;
		}
		
		//Arreglo de pacientes en urgencias con mas de 24 horas
		if($row['pachos'] == 'U'){
		
			$arreglo_urg[$empresa][$row['pachis'].$row['pacnum']] = $row ;
			$arreglo_urg[$empresa][$row['pachis'].$row['pacnum']]['plan'] = $rowp['plades'];
			$arreglo_urg[$empresa][$row['pachis'].$row['pacnum']]['cuenta_total'] = $wtot;
		
		}
	
	}
	
	echo "<pre>";
	print_r($arreglo_urg);
	echo "</pre>";
	
	//echo count($arreglo);
	
	
	$q_hos = "  SELECT * 
		          FROM ".$wbasedato."_000236
			     WHERE Plaest = 'on'
				   AND Platip = 'H'";
	$res_hos = mysql_query($q_hos,$conex) or die("Error en el query: ".$q_hos."<br>Tipo Error:".mysql_error());

	$array_ent_hos = array();
	
	while($row_hos = mysql_fetch_array($res_hos)){

		$array_ent_hos[$row_hos['Planit']] = $row_hos;
		$array_ent_hos[$row_hos['Planit']]['destinatarios'] = $row_hos['Plaema'];
		$array_ent_hos[$row_hos['Planit']]['dia'] = $row_hos['Pladia'];
		$array_ent_hos[$row_hos['Planit']]['urg'] = $row_hos['Plaurg'];

	}
	
	$array_hospita_enviados = array();
	
	if(count($array_ent_hos) > 0){
		
		echo "<br><br>Envio correo hospitalizados<br>";
		
	}
	
	// echo "<pre>";
	// print_r($array_ent_hos);
	// echo "</pre>";
	
	foreach($array_ent_hos as $key => $value){
				
		$datos_hos = "";
		$datos_hos .= "Historia;Ingreso;Nombre Paciente;Fecha Ingreso;Hora Ingreso;Habitacion;Poliza;Cedula;Total Cuenta;Hos;Plan". PHP_EOL;
		
		$loghos = "SELECT count(*) as cuantoshos
					 FROM ".$wbasedato."_000237
					WHERE Lognit = '".$key."'
					  AND Logtip = 'H'
					  AND Fecha_data = '".date("Y-m-d")."'
					  AND Logest = 'on'";
		$reshos = mysql_query($loghos,$conex) or die ("Query : ".$loghos." - ".mysql_error());
		$rowhos = mysql_fetch_assoc($reshos);
		
		if($rowhos['cuantoshos'] == 1){			
			
			$message_hosp  = "<html><body>";

			$message_hosp .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";

			$message_hosp .= "<tr><td>";

			$message_hosp .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";

			$message_hosp .= "<thead>
							<tr height='80' align='left'>
								<th colspan='4' style='background-color:#ffffff; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:20px;' >
								<img width='110px' height='70px' src='../../images/medical/root/clinica.JPG'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Archivo plano pacientes hospitalizados</th>
							</tr>
						 </thead>";
			
			$logh = "   SELECT Empnom
						 FROM ".$wcliame."_000024
						WHERE Empcod = '".$key."'";
			$resh = mysql_query($logh,$conex) or die ("Query : ".$logh." - ".mysql_error());
			$rowh = mysql_fetch_assoc($resh);
			
			$message_hosp .= "<tbody>
						   <tr>
						   <td colspan='4' style='padding:15px;'>
						   <p style='font-size:18px;'>Buenos dias, el presente archivo contiene la información de los pacientes hospitalizados en la fecha ".date("Y-m-d")." para la entidad ".$rowh['Empnom']." con nit ".$key." </p>
						   </tr>
						   <tr height='80'>
						   <hr />
						   <td colspan='4' align='center' style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:24px; '>
						   </td>
						   </tr>
						</tbody>";

			$message_hosp .= "</table>";

			$message_hosp .= "</td></tr>";
			$message_hosp .= "</table>";

			$message_hosp .= "</body></html>";

			
			$nombre_adjunto_hos = $key.".csv";
			$file = fopen( '/var/www/matrix/planos/archivosplanosentidades/hospitalizados/'.$nombre_adjunto_hos, "w" );		
							
			$responsable = $arreglo[$key];
			$responsables_urg = $arreglo_urg[$key]; 
			$cuantosxresponsable = count($arreglo[$key]);			
			
			// echo "<pre>";
			// echo $key;
			// print_r($responsables_urg);
			// echo "</pre>";
			
			if(!empty($responsable) or !empty($responsables_urg)){
				
				$suma_cuenta = 0;
				$suma_cuenta_urg = 0;
				$suma_total = 0;
				$cuantos_total = 0;
				
				foreach($responsable as $key1 => $value1){
					
					
					$datos_hos .= $value1['pachis'].";".trim($value1['pacnum']).";".trim($value1['pacap1'])." ".trim($value1['pacap2'])." ".trim($value1['pacnom']).";".trim($value1['pacfec']).";".trim($value1['pachor']).";".trim($value1['trahab']).";".trim($value1['pacpol']).";".trim($value1['pacced']).";".trim($value1['cuenta_total']).";".trim($value1['pachos']).";".trim($value1['pacmrepla']). PHP_EOL;
					$suma_cuenta = $suma_cuenta + $value1['cuenta_total'];
					
					
				}
				
				$cuantosxresponsable_urg = 0;
				
				//Si la entidad tiene activo el envio de pacientes de urgencias, agregara los datos de estos pacientes al archivo, recorriendo el arreglo $responsables_urg.
				if($value['urg'] == 'on'){
					
					foreach($responsables_urg as $key1 => $value1){
						
						echo $value1['pachis']."-";
						$datos_hos .= $value1['pachis'].";".trim($value1['pacnum']).";".trim($value1['pacap1'])." ".trim($value1['pacap2'])." ".trim($value1['pacnom']).";".trim($value1['pacfec']).";".trim($value1['pachor']).";".trim($value1['trahab']).";".trim($value1['pacpol']).";".trim($value1['pacced']).";".trim($value1['cuenta_total']).";".trim($value1['pachos']).";".trim($value1['pacmrepla']). PHP_EOL;
						$suma_cuenta_urg = $suma_cuenta_urg + $value1['cuenta_total'];						
						
					}
					
					$cuantosxresponsable_urg = count($arreglo_urg[$key]);
				}
				
				$suma_total = $suma_cuenta + $suma_cuenta_urg;
				$cuantos_total = $cuantosxresponsable+$cuantosxresponsable_urg;
				
				echo $cuantos_total."-".$key."<br>";
				$datos_hos = $datos_hos."\n".($cuantos_total)." pacientes ".number_format($suma_total,2);
				
				fwrite($file, $datos_hos);
				fclose($file);
				
				$wasunto 		= "Envio hospitalizados Clinica Las Americas";
				$altbody 		= "Cordial saludo,<br> \n\n Archivo de pacientes Hospitalizados Clinica las Americas";
				$ruta_archivo_hos = '/var/www/matrix/planos/archivosplanosentidades/hospitalizados/'.$nombre_adjunto_hos;
				$wdestinatarios_hosp = explode(",",$value['destinatarios']);
				
				//Valida si el todos los dias o dias especificos
				if($value['dia'] == '*'){
					
					//$sendToEmail = sendToEmail($wasunto,$message_hosp,$altbody, $datos_remitente, $wdestinatarios_hosp, $ruta_archivo_hos, $nombre_adjunto_hos );	
										
					$q = " INSERT INTO ".$wbasedato."_000237 (   Medico  ,      Fecha_data     ,   Hora_data       ,   Lognit ,    Logtip       , Logest ,  Seguridad        ) "
						 ."                   VALUES (  'movhos' ,  '".date("Y-m-d")."','".date("H:m:s")."', '".$key."', 'H',   'on' , 'C-movhos')";  
					//$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
					
					//echo $sendToEmail['mensError']." ".$key." - ".$value['destinatarios']."<br>";
					
				}else{
					
					$dia_envio = explode("-",$value['dia']);
					
					foreach($dia_envio as $key2 => $value){
						
						if($value == date('w', date('Y-m-d'))){
							
							//$sendToEmail = sendToEmail($wasunto,$message_hosp,$altbody, $datos_remitente, $wdestinatarios_hosp, $ruta_archivo_hos, $nombre_adjunto_hos );
														
							$q = " INSERT INTO ".$wbasedato."_000237 (   Medico  ,      Fecha_data     ,   Hora_data       ,   Lognit ,    Logtip       , Logest ,  Seguridad        ) "
										."            VALUES (  'movhos' ,  '".date("Y-m-d")."','".date("H:m:s")."', '".$key."', 'hospitalizados',   'on' , 'C-movhos')";  
							//$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());							
							
							echo $sendToEmail['mensError']." ".$key." - ".$value['destinatarios']."<br>";
						}
					}
				}
				
				$array_hospita_enviados[$key] = implode(",", $wdestinatarios_hosp);
				
			}	
		}	
	}
	
	//print_r($array_hospita_enviados);
	
	if(!empty($array_hospita_enviados)){
				
		//Envio certificacion correo hospitalizados
		//ConfirmacionEnvioHospitalizados
		$correoconfirmacionEnvioHospitalizados = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConfirmacionEnvioHospitalizados');
		$correoconfirmacionEnvioHospitalizados = explode(",", $correoconfirmacionEnvioHospitalizados);
		$wasuntologh = "Confirmacion hospitalizados por Clinica las Americas";
		$altbodylogh = "";
		$mensajelogh .= "<thead>
							<tr height='80' align='left'>
								<th colspan='4' style='background-color:#ffffff; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:20px;' >
								<img width='110px' height='70px' src='../../images/medical/root/clinica.JPG'><br><br>Confirmación envio pacientes hospitalizados para las siguientes entidades:</th>
							</tr>
						 </thead><br><br> ";
		$mensajelogh .= "<table border='1' width='600px' cellpadding='0' cellspacing='0' border='0'>";
		$mensajelogh .= "<tr class='encabezadoTabla'>";
		$mensajelogh .= "<td align=center><b>Nit</b></td><td align=center><b>Entidad</b></td><td align=center><b>Correo</b></td>";
		$mensajelogh .= "</tr>";
		
			foreach($array_hospita_enviados as $keyh => $valueh){
				
				$log = "SELECT Empnom
						  FROM ".$wcliame."_000024
						 WHERE Empcod = '".$keyh."'";
				$res = mysql_query($log,$conex) or die ("Query : ".$log." - ".mysql_error());
				$row = mysql_fetch_assoc($res);
				
				$mensajelogh .= "<tr>";
				$mensajelogh .= "<td>".$keyh."</td><td>".$row['Empnom']."</td><td>".$valueh."</td>";
				$mensajelogh .= "</tr>";
				
			}
			
		$mensajelogh .= "</table>";
		//$sendToEmail = sendToEmail($wasuntologh,$mensajelogh,$altbodylogh, $datos_remitente, $correoconfirmacionEnvioHospitalizados );
		$i++;
	}
	
	
	
	
	
echo "Ok";
	

function construirQueryUnix( $tablas, $campos_nulos, $campos_todos='', $condicionesWhere='',$defecto_campos_nulos=''){

	$condicionesWhere = trim($condicionesWhere);

	if( $campos_nulos == NULL || $campos_nulos == "" ){
		$campos_nulos = array("");
	}

	if( $tablas == "" ){ //Debe existir al menos una tabla
		return false;
	}

	if(gettype($tablas) == "array"){
		$tablas = implode(",",$tablas);
	}

	$pos = strpos($tablas, ",");
	if( $pos !== false && $condicionesWhere == ""){ //Si hay mas de una tabla, debe mandar condicioneswhere
		return false;
	}

	//Si recibe un string, convertirlo a un array
	if( gettype($campos_nulos) == "string" )
		$campos_nulos = explode(",",$campos_nulos);

	$campos_todos_arr = array();

	//Por cual string se reemplazan los campos nulos en el query
	if( $defecto_campos_nulos == "" ){
		$defecto_campos_nulos = array();
		foreach( $campos_nulos as $posxy=>$valorxy ){
			array_push($defecto_campos_nulos, "''");
		}
	}else{
		if(gettype($defecto_campos_nulos) == "string"){
			$defecto_campos_nulos = explode(",",$defecto_campos_nulos);
		}
		if(  count( $defecto_campos_nulos ) == 1 ){ //Significa que todos los campos nulos van a ser reemplazados con el mismo valor
			$defecto_campos_nulos_aux = array();
			foreach( $campos_nulos as $posxyc=>$valorxyc ){
				array_push($defecto_campos_nulos_aux, $defecto_campos_nulos[0]);
			}
			$defecto_campos_nulos = $defecto_campos_nulos_aux;
		}else if(  count( $defecto_campos_nulos ) != count( $campos_nulos ) ){
			return false;
		}
	}

	if( gettype($campos_todos) == "string" ){
		$campos_todos_arr = explode(",",trim($campos_todos));
	}else if(gettype($campos_todos) == "array"){
		$campos_todos_arr = $campos_todos;
		$campos_todos = implode(",",$campos_todos);
	}
	foreach( $campos_todos_arr as $pos22=>$valor ){ //quitar espacios a cada valor
		$campos_todos_arr[$pos22] = trim($valor);
	}
	foreach( $campos_nulos as $pos221=>$valor1 ){ //quitar espacios a cada valor
		$campos_nulos[$pos221] = trim($valor1);

		//Si el campo nulo no existe en el arreglo de todos los campos, agregarlo al final
		$clavex = array_search(trim($valor1), $campos_todos_arr);
		if( $clavex === false ){
			array_push($campos_todos_arr,trim($valor1));
		}
	}
	//Quitar la palabra and, si las condiciones empiezan asi.
	if( substr($condicionesWhere, 0, 3)  == "AND" || substr($condicionesWhere, 0, 3) == "and" ){
		$condicionesWhere = substr($condicionesWhere, 3);
	}
	$condicionesWhere = str_replace("WHERE", "", $condicionesWhere); //Que no tenga la palabra WHERE
	$condicionesWhere = str_replace("where", "", $condicionesWhere); //Que no tenga la palabra WHERE

	$query = "";

	$bits = count( $campos_nulos );
	if( $bits >= 10 ){ //No pueden haber más de 10 campos nulos
		return false;
	}

	if( $bits == 1 && $campos_nulos[0] == "" ){ //retornar el query normal
		$query = "SELECT ".$campos_todos ." FROM ".$tablas;
		if( $condicionesWhere != "" )
			$query.= " WHERE ".$condicionesWhere;
		return $query;
	}

	$max = (1 << $bits);
	$fila_bits = array();
	for ($i = 0; $i < $max; $i++){
		/*-->decbin Entrega el valor binario del decimal $i,
		  -->str_pad Rellena el string hasta una longitud $bits con el caracter 0 por la izquierda:
			 EJEMPLO $input = "Alien" str_pad($input, 10, "-=", STR_PAD_LEFT);  // produce "-=-=-Alien", rellena por la izquierda hasta juntar 10 caracteres
		  -->str_split Convierte un string (el entregado por str_pad) en un array, asi tengo el arreglo con el codigo binario generado
		*/
		$campos_todos_arr_copia = array();
		$campos_todos_arr_copia = $campos_todos_arr;

		$fila_bits = str_split( str_pad(decbin($i), $bits, '0', STR_PAD_LEFT) );
		$select = "SELECT ";
		$where = " WHERE ";
		if( $condicionesWhere != "" )
			$where.= $condicionesWhere." AND ";

		for($pos = 0; $pos < count($fila_bits); $pos++ ){
			if($pos!=0) $where.= " AND ";
			if( $fila_bits[$pos] == 0 ){
				$clave = array_search($campos_nulos[$pos], $campos_todos_arr_copia);
				//if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = "'.' as ".$campos_nulos[$pos];
				if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = $defecto_campos_nulos[$pos]." as ".$campos_nulos[$pos];
				$where.= $campos_nulos[$pos]." IS NULL ";
			}else{
				$where.= $campos_nulos[$pos]." IS NOT NULL ";
			}
		}

		$select.= implode(",",$campos_todos_arr_copia);
		$query.= $select." FROM ".$tablas.$where;
		if( ($i+1) < $max ) $query.= " UNION ";
	}
	return $query;
}

?>
