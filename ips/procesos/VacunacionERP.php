<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2018-02-21';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//                
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    if(isset($accion) && $accion != '')
	{
		$respuesta['session'] = 'Primero recargue la p&aacute;gina principal de Matrix &oacute; inicie sesi&oacute;n nuevamente, para poder relizar esta acción.';
		
		echo json_encode($respuesta);
		return;		
	}
	else
	{
		echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
					[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
				</div>';
		return;
	}
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	

	include_once("root/comun.php");
	
	

	
	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	
	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$movhos 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wfecha			= date("Y-m-d");   
    $whora 			= date("H:i:s");

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//---------------------------------------------------------
	// --> 
	//---------------------------------------------------------

//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		case 'borrarRegAgenda':
		{
			$sqlBorrar = "
			UPDATE ".$wbasedato."_000296 SET Dvaest = 'off' WHERE id = '".$idReg."'
			";
			$respuesta['borro'] = mysql_query($sqlBorrar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlBorrar):</b><br>".$sqlBorrar."<br>".mysql_error());
						
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'agendarVacuna':
		{			
			$sqlInser = "
			INSERT INTO ".$wbasedato."_000296
					SET Medico 		= 'cliame',
						Fecha_data 	= '".date("Y-m-d")."',
						Hora_data 	= '".date("H:i:s")."',
						Dvavac 		= '".$inputVac."',
					    Dvafco 		= '".$inputFec."',
					".$campoHis." 	= '".$hisProgramar."',
						Dvaest		= 'on',
						Seguridad	= 'C-".$wuse."'
			";
			$resInser  				= mysql_query($sqlInser, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInser):</b><br>".$sqlInser."<br>".mysql_error());
			$respuesta['idReg'] 	= mysql_insert_id($conex);
			$respuesta['sqlInser'] 	= $sqlInser;
			
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'consultarRegistros':
		{
			$respuesta 	= array('Html' => '');
			$arrInfo 	= array();
			
			// --> Consultar el maestro de vacunas
			$maestroVacunas = array();
			$sqlVac = "
			SELECT Vaccod, Vacnom
			  FROM ".$wbasedato."_000297
			 WHERE Vacest = 'on'
			 UNION
			SELECT Artcod, Artcom 
			  FROM ".$movhos."_000026
			 WHERE Artest = 'on'
			   AND Artvac = 'on'
			";
			$resVac = mysql_query($sqlVac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVac):</b><br>".$sqlVac."<br>".mysql_error());
			while($rowVac = mysql_fetch_array($resVac))
			{
				$maestroVacunas[$rowVac[0]] = str_replace($caracter_ma, $caracter_ok, utf8_encode(trim(strtolower ($rowVac[1]))));
			}
			
			$respuesta['Html'].= "
			<tr align='center'>
				<td class='encabezadoTabla'>Historia</td>
				<td class='encabezadoTabla'>Código Unix</td>
				<td class='encabezadoTabla'>Documento</td>
				<td class='encabezadoTabla'>Paciente</td>
				<td class='encabezadoTabla'>Edad</td>
				<td class='encabezadoTabla'>Responsable</td>
				<td class='encabezadoTabla'>Vacunas</td>
				<td></td>
			</tr>";
			
			$buscHis		= ($buscHis != '') ? $buscHis : '%';
			$buscCodUnix	= ($buscCodUnix != '') ? $buscCodUnix : '%';
			$buscDoc		= ($buscDoc != '') ? $buscDoc : '%';
			$buscNom		= ($buscNom != '') ? $buscNom : '%';
			$buscVac		= ($buscVac != '') ? $buscVac : '%';
			switch($buscTip)
			{
				case '*':
					$filtro = "AND ((Dvafec BETWEEN '".$buscFecIn."' AND '".$buscFecFi."') OR Dvafec = '0000-00-00')";
					break;
				case 'on':
					$filtro = "AND (Dvafec BETWEEN '".$buscFecIn."' AND '".$buscFecFi."') 
							   AND Dvafec != '0000-00-00'";
					break;
				case 'off':
					$filtro = "
					AND (Dvafco BETWEEN '".$buscFecIn."' AND '".$buscFecFi."')
					AND  Dvafec = '0000-00-00'
					";
					break;
			}
			
			$sqlVac = "";
			
			if($buscHis == "%")
			{
				$sqlVac.="
				SELECT A.id as idReg, Evacpu, Evaced AS Cedula, CONCAT(Evanom, ' ', Evaap1, ' ', Evaap2) AS Nombre, Evafna AS fna, Evares, Evahis, Dvavac, Dvafco, Dvafec, B.id AS idDet
				  FROM ".$wbasedato."_000295 AS A LEFT JOIN ".$wbasedato."_000296 AS B ON(A.Evacpu = B.Dvacpu) 					   
				 WHERE Evahis = ''
				   AND Evacpu != '' 
				   AND Evacpu LIKE '%".$buscCodUnix."%'
				   AND Evaced LIKE '%".$buscDoc."%'
				   AND CONCAT(Evanom, Evaap1, Evaap2) LIKE '%".$buscNom."%'
				   AND Dvavac LIKE '".$buscVac."'
				   AND Dvaest = 'on'				   
				   ".$filtro."
				 UNION";
			}
				 
				$sqlVac.="
				SELECT A.id as idReg, Evacpu, CONCAT(Pactdo, '-', Pacdoc) AS Cedula, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) AS Nombre, Pacfna AS fna, Evares, Evahis, Dvavac, Dvafco, Dvafec, C.id AS idDet
				  FROM ".$wbasedato."_000295 AS A LEFT JOIN  ".$wbasedato."_000100 AS B ON(Evahis != '' AND Evahis = Pachis)
					   LEFT JOIN ".$wbasedato."_000296 AS C ON(Pachis = Dvahis) 			   
				 WHERE Evacpu LIKE '%".$buscCodUnix."%'
				   AND Evahis LIKE '%".$buscHis."%'
				   AND Pacdoc LIKE '%".$buscDoc."%'
				   AND CONCAT(Pacno1, Pacno2, Pacap1, Pacap2) LIKE '%".$buscNom."%'
				   AND Dvavac LIKE '".$buscVac."'	
				   AND Dvaest = 'on'				   
				   ".$filtro."			 
				 ORDER BY idDet DESC
				";
			
			$respuesta['Sql'] = $sqlVac;
			
			$resVac = mysql_query($sqlVac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVac):</b><br>".$sqlVac."<br>".mysql_error());
			while($rowVac = mysql_fetch_array($resVac, MYSQL_ASSOC))
			{
				$idPac = $rowVac['Evacpu']."-".$rowVac['Evahis'];
				$arrInfo[$idPac]['Info']	= $rowVac;
				
				$idx = ((isset($arrInfo[$idPac]['Vacunas'])) ? count($arrInfo[$idPac]['Vacunas']) : 0);
				
				$arrInfo[$idPac]['Vacunas'][$idx]['Vacuna'] 		= $rowVac['Dvavac'];
				$arrInfo[$idPac]['Vacunas'][$idx]['Nombre'] 		= utf8_encode($maestroVacunas[$rowVac['Dvavac']]);
				$arrInfo[$idPac]['Vacunas'][$idx]['fColocar'] 		= $rowVac['Dvafco'];
				$arrInfo[$idPac]['Vacunas'][$idx]['fAplicacion'] 	= $rowVac['Dvafec'];
			}
			
			$respuesta['numReg'] = mysql_num_rows($resVac);
			
			$cf = 'fila2';
			foreach($arrInfo as $hisPac => $info)
			{
				$infoPac 	= $info['Info'];
				$cf 		= (($cf == 'fila2') ? 'fila1' : 'fila2');
				$infoVacuna = "
				<table style=font-weight:normal>
					<tr class=filaAzul align=center><td>VACUNA</td><td>FECHA A APLICAR</td><td>FECHA APLICACIÓN</td></tr>"; 
				foreach($info['Vacunas'] as $infoVac)
					$infoVacuna.= "
					<tr class=fondoBlanco>
						<td>".$infoVac['Vacuna']."-".$infoVac['Nombre']."</td>
						<td align=center>".$infoVac['fColocar']."</td>
						<td align=center>".$infoVac['fAplicacion']."</td>
					</tr>
					";
				
				$infoVacuna.= "
				</table>	
				";
				
				$dif  	= abs(strtotime(date('Y-m-d'))-strtotime($infoPac['fna']));
				$años 	= floor($dif/(365*60*60*24));
				$months = floor(($dif - $años * 365*60*60*24) / (30*60*60*24));
				
				$respuesta['Html'].= "
				<tr>
					<td class='".$cf."'>".$infoPac['Evahis']."</td>
					<td class='".$cf."'>".$infoPac['Evacpu']."</td>
					<td class='".$cf."'>".$infoPac['Cedula']."</td>
					<td class='".$cf."'>".utf8_encode($infoPac['Nombre'])."</td>
					<td class='".$cf."'>".(($años >= 1) ? $años." Años" : $months." Meses")."</td>
					<td class='".$cf."'>".utf8_encode(ucwords(strtolower($infoPac['Evares'])))."</td>
					<td class='".$cf."' align='center'>".count($info['Vacunas'])."&nbsp;<img width='12' height='12' style='cursor:pointer;' class='tooltip' title='".$infoVacuna."' src='../../images/medical/sgc/info.png'></td>
					<td><img width='15' height='11' style='cursor:pointer;' onClick='verPaciente(\"".$infoPac['idReg']."\")' class='tooltip' title='<span class=textTooltip>Abrir</span>' src='../../images/medical/sgc/abrir.png'></td>
				</tr>
				";
			}
			
			if(mysql_num_rows($resVac) == 0)
			{
				$respuesta['Html'].= "
				<tr>
					<td align='center' colspan='7' class='".$cf."'>No se encontraron registros</td>
				</tr>
				";
			}	
				
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'verPaciente':
		{
			$respuesta 		= array('Html' => '');
			$movhos 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			
			// --> Consultar info del paciente
			$sqlInfo = "
			SELECT CONCAT(Evanom, ' ', Evaap1, ' ', Evaap2) AS Nombre, Evafna AS fna, Evahis, Evacpu, Evaced AS Cedula,
				   Evadir AS Dir, Evaema, Evatel AS Tel, Evares AS Res, '' AS Ingtpa, 1 Ingnin 	
			  FROM ".$wbasedato."_000295
			 WHERE id = ".$idReg."
			   AND Evahis = ''
			 UNION
			SELECT CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) AS Nombre, Pacfna AS fna, Evahis, Evacpu, CONCAT(Pactdo, '-', Pacdoc) AS Cedula,
				   Pacdir AS Dir, Evaema, Pactel AS Tel, Empnom AS Res, Ingtpa, Ingnin
			  FROM ".$wbasedato."_000295 AS A INNER JOIN ".$wbasedato."_000100 AS D ON(Evahis = Pachis)
				   LEFT JOIN ".$wbasedato."_000101 ON (Pachis = Inghis)
				   LEFT JOIN ".$wbasedato."_000024 ON (Ingcem = Empcod)
			 WHERE A.id = ".$idReg."
			   AND Evahis != ''
			 ORDER BY Ingnin DESC 
			 LIMIT 1			 
			";
			
			$respuesta['sqlInfo'] = $sqlInfo;
			
			$resVac = mysql_query($sqlInfo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo):</b><br>".$sqlInfo."<br>".mysql_error());
			if($rowVac = mysql_fetch_array($resVac, MYSQL_ASSOC))
			{
				if($rowVac['Ingtpa'] == 'P')
					$responsable = 'Particular';
				else
					$responsable = $rowVac['Res'];
				
				$dif  	= abs(strtotime(date('Y-m-d'))-strtotime($rowVac['fna']));
				$años 	= floor($dif/(365*60*60*24));
				$months = floor(($dif - $años * 365*60*60*24) / (30*60*60*24));
				
				$respuesta['Html'].= "
				<br>
				<fieldset align='center' style='padding:15px;'>
					<legend class='fieldset'>Informaci&oacute;n del paciente</legend>
					<table width='100%'>
						<tr>
							<td class='fila1'>Nombre:</td>
							<td class='fila2' colspan='3'>".$rowVac['Nombre']."</td>
							<td class='fila1'>Edad:</td>
							<td class='fila2'>".(($años >= 1) ? $años." A&ntilde;os" : $months." Meses")."</td>
						</tr>
						<tr>
							<td class='fila1'>Historia:</td>
							<td class='fila2'>".$rowVac['Evahis']."</td>
							<td class='fila1'>Codigo Unix:</td>
							<td class='fila2'>".$rowVac['Evacpu']."</td>
							<td class='fila1'>Documento:</td>
							<td class='fila2'>".$rowVac['Cedula']."</td>
						</tr>						
						<tr>
							<td class='fila1'>Direcci&oacute;n:</td>
							<td class='fila2'>".$rowVac['Dir']."</td>
							<td class='fila1'>Correo:</td>
							<td class='fila2'>".$rowVac['Evaema']."</td>
							<td class='fila1'>Telefono:</td>
							<td class='fila2'>".$rowVac['Tel']."</td>
						</tr>
						<tr>
							<td class='fila1'>Fecha Nacimiento:</td>
							<td class='fila2'>".$rowVac['fna']."</td>
							<td class='fila1'>Responsable:</td>
							<td class='fila2'>".$responsable."</td>
							<td class='fila1'></td>
							<td class='fila2'></td>
						</tr>
					</table>
				</fieldset>
				<br>
				";
			}
			
			$hisProgramar 	= ($rowVac['Evahis'] != '') ? $rowVac['Evahis'] : $rowVac['Evacpu'];
			$campoHis 		= ($rowVac['Evahis'] != '') ? 'Dvahis ' : 'Dvacpu';
			
			$respuesta['Html'].= "
				<fieldset align='center' style='padding:15px;'>
					<legend class='fieldset'>Detalle de vacunas</legend>
					<table width='100%' id='detVacPorPac'>
						<tr align='center'>
							<td colspan='7' align='right'>
								<img width='15' height='15' style='cursor:pointer;' onClick='programarVacuna(\"".$hisProgramar."\", \"".$campoHis."\")' class='tooltip' title='<span class=textTooltip>Programar nueva vacuna</span>' src='../../images/medical/sgc/mas.png'>
							</td>
						</tr>
						<tr align='center'>
							<td class='fila1'>Num ingreso</td>
							<td class='fila1'>Vacuna</td>
							<td class='fila1'>Fecha a aplicar</td>
							<td class='fila1'>Fecha de aplicaci&oacute;n</td>
							<td class='fila1'>Lote</td>
							<td></td>
						</tr>
				";
				
			// --> Detalle de vacunas
			$sqlDetVac = "
			SELECT B.id AS idReg, B.*, C.*
			  FROM ".$wbasedato."_000295 AS A LEFT JOIN ".$wbasedato."_000296 AS B ON(A.Evacpu = B.Dvacpu AND Dvaest = 'on') 
				   LEFT JOIN ".$wbasedato."_000297 AS C ON(Dvavac = Vaccod)
			 WHERE A.id   = ".$idReg."
			   AND Evahis = ''
			 UNION
			SELECT B.id AS idReg, B.*, C.*
			  FROM ".$wbasedato."_000295 AS A LEFT JOIN ".$wbasedato."_000296 AS B ON(A.Evahis = B.Dvahis AND Dvaest = 'on') 
				   LEFT JOIN ".$wbasedato."_000297 AS C ON(Dvavac = Vaccod)
			 WHERE A.id   = ".$idReg."
			   AND Evahis != ''
			 ORDER BY idReg DESC
			";
			$resDetVac = mysql_query($sqlDetVac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDetVac):</b><br>".$sqlDetVac."<br>".mysql_error());
			while($rowDetVac = mysql_fetch_array($resDetVac, MYSQL_ASSOC))
			{
				if($rowDetVac['Vacnom'] == "")
				{
					$sqlNomVac = "
					SELECT Artcom 
					  FROM ".$movhos."_000026
					 WHERE Artcod = '".$rowDetVac['Dvavac']."'
					";
					$resNomVac = mysql_query($sqlNomVac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomVac):</b><br>".$sqlNomVac."<br>".mysql_error());
					if($rowNomVac = mysql_fetch_array($resNomVac))
						$rowDetVac['Vacnom'] = $rowNomVac['Artcom'];
				}
				
				$respuesta['Html'].= "
						<tr>
							<td class='fila2' align='center'>".$rowDetVac['Dvaing']."</td>
							<td class='fila2'>".$rowDetVac['Dvavac']."-".utf8_encode(ucwords(strtolower($rowDetVac['Vacnom'])))."</td>
							<td class='fila2' align='center'>".$rowDetVac['Dvafco']."</td>
							<td class='fila2' align='center'>
								<span 	class='spanFechaApl' >".$rowDetVac['Dvafec']."</span>&nbsp;&nbsp;
								<input type='text' class='inputFechaApl bordeRed'  style='display:none' value='".$rowDetVac['Dvafec']."' readonly>
								<img 	class='guardarFechaApl tooltip' 		style='cursor:pointer;display:none' title='<span class=textTooltip>Guardar</span>' src='../../images/medical/root/grabar16.png' width='12px' height='12px' onClick='guardarFechaApl(this, \"".$rowDetVac['idReg']."\")'>
								<img 	class='cancelarFechaApl tooltip' 		style='cursor:pointer;display:none' title='<span class=textTooltip>Cancelar</span>r' src='../../images/medical/eliminar1.png' width='12px' height='12px' onClick='cancelarFechaApl(this)'>
								<img 	class='editarFechaApl tooltip' 			style='cursor:pointer;' 			title='<span class=textTooltip>Doble click para editar fecha de aplicacion</span>' src='../../images/medical/hce/mod.PNG' ondblclick='editarFechaApl(this)'>
							</td>
							<td class='fila2' align='center'>
								<span 	class='spanLote' >".$rowDetVac['Dvalot']."</span>&nbsp;&nbsp;
								<input type='text' class='inputLote bordeRed'  style='display:none' value='".$rowDetVac['Dvalot']."'>
								<img 	class='guardarLot tooltip' 		style='cursor:pointer;display:none' title='<span class=textTooltip>Guardar</span>' src='../../images/medical/root/grabar16.png' width='12px' height='12px' onClick='guardarLote(this, \"".$rowDetVac['idReg']."\")'>
								<img 	class='cancelartLot tooltip' 	style='cursor:pointer;display:none' title='<span class=textTooltip>Cancela</span>r' src='../../images/medical/eliminar1.png' width='12px' height='12px' onClick='cancelarEditarLote(this)'>
								<img 	class='editarLot tooltip' 		style='cursor:pointer;".(($rowDetVac['Dvafec'] != '' && $rowDetVac['Dvafec'] != '0000-00-00') ? "" : "display:none" )."' 			title='<span class=textTooltip>Doble click para editar lote</span>' src='../../images/medical/hce/mod.PNG' ondblclick='editarLote(this)'>
							</td>
							<td align='center'>".(($rowDetVac['Dvafec'] == '0000-00-00') ? "
								<img width='13' height='13' style='cursor:pointer;' onClick='borrarRegAgenda(\"".$rowDetVac['idReg']."\", this)' title='Borrar' src='../../images/medical/eliminar1.png'>"
								: "")."
							</td>
						</tr>					
				";
			}
			$respuesta['Html'].= "
					</table>
				</fieldset>
				<br>
			";
			
			echo json_encode($respuesta);
			return;
			break;
		}
		
		case 'imprimirDetalleVac':
		{
			$html = str_replace('\\', '', $html);
			
			$respuesta['HtmlPdf'] = "
			<html>
				<head></head>
				<body style='margin:0mm'>
					<style type='text/css'>
						.fila1
						{
							background-color: 	#FFFFFF;
							color: 				#000000;
							font-size: 			3 mm;
							padding:			1px;
							font-family: 		verdana;
							font-weight: 		bold;
						}
						.fila2
						{
							background-color: 	#FFFFFF;
							color: 				#000000;
							font-size: 			3 mm;
							padding:			1px;
							font-family: 		verdana;
						}
						fieldset{
							border: 1px solid #000000;
							border-radius: 5px;
						}
						legend{
							border: 1px solid #000000;
							border-top: 0px;
							font-family: Verdana;
							background-color: #000000;
							color: #ffffff;
							font-size: 4 mm;
						}
					</style>
					<br>
					<table width='100%'>
						<tr>
							<td width='50%'><span class='fila1'>Historia de vacunaci&oacute;n</span></td>
							<td width='50%' align='right' valign='top'><img src='../../../images/medical/root/clinica.JPG' heigth='50' width='80'></td>							
						</tr>
					</table>
					".utf8_decode($html)."
				</body>
			</html>";
				
			// --> Generar archivo pdf			
			$wnombrePDF 	= "vacunacion_HisVacunacion";
			$archivo_dir 	= "soportes/".$wnombrePDF.".html";
			$dir			= "soportes";

			if(is_dir($dir)){ }
			else { mkdir($dir,0777); }

			if(file_exists($archivo_dir)){
				unlink($archivo_dir);
			}

			$f = fopen($archivo_dir, "w+" );
			fwrite($f, $respuesta['HtmlPdf']);
			fclose($f);

			if(file_exists("soportes/".$wnombrePDF.".pdf")){
				unlink("soportes/".$wnombrePDF.".pdf");
			}

			//chmod("./generarPdf_soportesCargos.sh", 0777);
			shell_exec( "./generarPdf_soportesCargos.sh ".$wnombrePDF );

			$respuesta['HtmlPdf'] = "
				<object type='application/pdf' data='../../../matrix/ips/procesos/soportes/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='800' height='700'>"
				  ."<param name='src' value='soportes/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
				  ."<p style='text-align:center; width: 60%;'>"
					."Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />"
					."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
					  ."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
					."</a>"
				  ."</p>"
				."</object>
				<br>
			";
			
			$respuesta['nombrePdf']	= $wnombrePDF;
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'reportePacVacunadosPorEdad':
		{
			$respuesta 		= array('HtmlPdf' => '');
			$movhos 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			$arrVac			= array();
			$arrInfo		= array();
			$totalesVac		= array();
			$arrRangos		= array(1 => "Menor a 1", 2 => "1 a 4", 3 => "5 a 14", 4 => "15 a 44", 5 => "Mayor a 44");
			
			$respuesta['Html'] = date("Y-m-d H:i:s");
			
			// --> Maestro de vacunas
			$sqlVac = "
			SELECT Vaccod, Vacnom
			  FROM ".$wbasedato."_000297
			 WHERE Vacest = 'on'
			 UNION
			SELECT Artcod, Artcom 
			  FROM ".$movhos."_000026
			 WHERE Artest = 'on'
			   AND Artvac = 'on'		   
			";
			$resVac = mysql_query($sqlVac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVac):</b><br>".$sqlVac."<br>".mysql_error());
			while($rowVac = mysql_fetch_array($resVac))
				$arrVac[$rowVac[0]] = $rowVac[1];
			
			// --> Consultar info 
			$sqlInfo = "
			SELECT Dvavac, Pacfna as fecNac
			  FROM ".$wbasedato."_000296 INNER JOIN ".$wbasedato."_000100 ON(Dvahis = Pachis)
			 WHERE Dvafec BETWEEN '".$fechaIni."' AND '".$fechaFin."'
               AND Dvaest = 'on'
			   AND Dvahis != '' 
			 UNION
			SELECT Dvavac, Evafna as fecNac
			  FROM ".$wbasedato."_000296 INNER JOIN ".$wbasedato."_000295 ON(Dvacpu = Evacpu)
			 WHERE Dvafec BETWEEN '".$fechaIni."' AND '".$fechaFin."'
               AND Dvaest = 'on'
			   AND Dvahis = '' 			   
			";			
			$resVac = mysql_query($sqlInfo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo):</b><br>".$sqlInfo."<br>".mysql_error());
			while($rowVac = mysql_fetch_array($resVac, MYSQL_ASSOC))
			{
				$rowVac['fecNac'] = ($rowVac['fecNac'] == '' || $rowVac['fecNac'] == '0000-00-00') ? date('Y-m-d') : $rowVac['fecNac'];
				
				$dif  	= abs(strtotime(date('Y-m-d'))-strtotime($rowVac['fecNac']));
				$años 	= floor($dif/(365*60*60*24));
				$arrInfo[$rowVac['Dvavac']]['Nombre'] = $arrVac[$rowVac['Dvavac']];
				
				if($años < 1)
					$rango = 1;
				
				if($años >= 1 && $años < 5)
					$rango = 2;
				
				if($años >= 5 && $años < 15)
					$rango = 3;
				
				if($años >= 15 && $años < 44)
					$rango = 4;
				
				if($años > 44)
					$rango = 5;
				
				if(!isset($arrInfo[$rowVac['Dvavac']][$rango]))
					$arrInfo[$rowVac['Dvavac']][$rango] = 0;
				
				$arrInfo[$rowVac['Dvavac']][$rango]++;
				
				if(!isset($totalesVac[$rowVac['Dvavac']]))
					$totalesVac[$rowVac['Dvavac']] = 0;
				
				$totalesVac[$rowVac['Dvavac']]++;
			}
			
			$html = "
			<br>
			<table width='100%' cellspacing='0' style='font-size:8pt;padding:1px;font-family:verdana;'>
				<tr>
					<td></td>
			";
			
			// --> Pintar titulos de rangos
			foreach($arrRangos as $nomRango)
				$html.= "<td style='border:1px;border-style:solid;font-weight:bold' align='center'>".$nomRango."</td>";
			
			$html.= "<td style='border:1px;border-style:solid;font-weight:bold' align='center'>Total</td>	
				</tr>";
			
			// --> Pintar info vacunas
			foreach($arrInfo as $codVac => $valVac)
			{
				$html.= "
					<tr>
						<td style='border:1px;border-style:solid;'>".$codVac."-".$valVac['Nombre']."</td>";
					
				foreach($arrRangos as $rango => $nomRango)
				{
					$html.= "
						<td style='border:1px;border-style:solid' align='center'>".$valVac[$rango]."</td>";
				}
				$html.= "
						<td style='border:1px;border-style:solid' align='center'>".$totalesVac[$codVac]."</td>
					</tr>";
			}
			$html.= "
			</table>
			";
			
						
			$respuesta['HtmlPdf'] = "
			<html>
				<head></head>
				<body style='margin:0mm'>
					<style type='text/css'>
						.doted{
							font-family: 'Courier New';
							font-size:4mm;
							font-weight: 400;
						}
						
					</style>
					<br>
					<table width='100%'>
						<tr>
							<td width='50%'><span class='fila1'>Reporte pacientes vacunados por edad:<br>".$fechaIni." al ".$fechaFin."</span></td>
							<td width='50%' align='right' valign='top'><img src='../../../images/medical/root/clinica.JPG' heigth='50' width='80'></td>							
						</tr>
					</table>
					".$html."
					<table width='100%'>
						<tr>
							<td style='font-size:2.5mm;' align='right'>Fecha Imp:".date("Y-m-d")."&nbsp;&nbsp;Hora Imp:".date("H:i:s")."&nbsp;&nbsp;Usuario:".$wuse."</td>
						</tr>
					</table>
				</body>
			</html>";
				
			// --> Generar archivo pdf			
			$wnombrePDF 	= "reporteVacunacion";
			$archivo_dir 	= "soportes/".$wnombrePDF.".html";
			$dir			= "soportes";

			if(is_dir($dir)){ }
			else { mkdir($dir,0777); }

			if(file_exists($archivo_dir)){
				unlink($archivo_dir);
			}

			$f = fopen($archivo_dir, "w+" );
			fwrite($f, $respuesta['HtmlPdf']);
			fclose($f);

			if(file_exists("soportes/".$wnombrePDF.".pdf")){
				unlink("soportes/".$wnombrePDF.".pdf");
			}

			//chmod("./generarPdf_soportesCargos.sh", 0777);
			shell_exec( "./generarPdf_soportesCargos.sh ".$wnombrePDF );

			$respuesta['HtmlPdf'] = "
				<object type='application/pdf' data='../../../matrix/ips/procesos/soportes/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='800' height='590'>"
				  ."<param name='src' value='soportes/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
				  ."<p style='text-align:center; width: 60%;'>"
					."Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />"
					."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
					  ."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
					."</a>"
				  ."</p>"
				."</object>
				<br>
			";
			
			$respuesta['nombrePdf']	= $wnombrePDF;
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case'guardarLote':
		{
			$sqlLote = "
			UPDATE ".$wbasedato."_000296
			   SET Dvalot = '".$lote."'
			 WHERE id = '".$idReg."' 
			";
			mysql_query($sqlLote, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLote):</b><br>".$sqlLote."<br>".mysql_error());
			return;
			break;
		}
		case'guardarFechaApl':
		{
			$sqlLote = "
			UPDATE ".$wbasedato."_000296
			   SET Dvafec = '".$fecha."'
			 WHERE id = '".$idReg."' 
			";
			mysql_query($sqlLote, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLote):</b><br>".$sqlLote."<br>".mysql_error());
			return;
			break;
		}
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{
	?>
	<html>
	<head>
	  <title>Vacunación</title>
	</head>	
		<meta charset="UTF-8">
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	//var url_add_params 		= addUrlCamposCompartidosTalento();
	var blink;
	
	$(function(){
		// --> Parametrización del datapicker
		cargar_elementos_datapicker();
		// --> Activar datapicker
		$("#buscFecIn").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				
			}
		});
		$("#buscFecFi").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				
			}
		});
		
		consultarRegistros();
		
		crear_autocomplete("maestroVacunas", $("#buscVac"));		
	});
	//-----------------------------------------------------------
	//	--> Cargar autocomplete de campos
	//-----------------------------------------------------------
	function crear_autocomplete(HiddenArray, CampoCargar)
	{
		ArrayVal	  		= JSON.parse($("#"+HiddenArray).val());		
		var ArraySource   	= new Array();
		var index		  	= -1;
		
		for (var CodVal in ArrayVal)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].codigo = CodVal;
			ArraySource[index].value  = CodVal+"-"+ArrayVal[CodVal];
			ArraySource[index].label  = CodVal+"-"+ArrayVal[CodVal];
			ArraySource[index].nombre = CodVal+"-"+ArrayVal[CodVal];
		}

		// --> Si el autocomplete ya existe, lo destruyo
		if( CampoCargar.attr("autocomplete") != undefined )
			CampoCargar.removeAttr("autocomplete");

		// --> Creo el autocomplete
		CampoCargar.autocomplete({
			minLength: 	0,
			source: 	ArraySource,
			select: 	function( event, ui ){
				
				valorAnt = CampoCargar.attr('valor');
				CampoCargar.val(ui.item.label);
				CampoCargar.attr('valor', ui.item.codigo);
				CampoCargar.attr('nombre', ui.item.nombre);		
				return false;
			}
		});
		limpiaAutocomplete(CampoCargar, HiddenArray);
	}
	//----------------------------------------------------------------------------------
	//	--> Controlar que el input no quede con basura, sino solo con un valor seleccionado
	//----------------------------------------------------------------------------------
	function limpiaAutocomplete(CampoCargar, HiddenArray)
	{
		CampoCargar.on({
			focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{
					$(this).val("");
					$(this).attr("valor","");
					$(this).attr("nombre","");
				}
				else
				{
					$(this).val($(this).attr("nombre"));
				}
			}
		});
	}
	//--------------------------------------------------------
	//	--> Activar datapicker
	//---------------------------------------------------------
	function cargar_elementos_datapicker()
	{
		$.datepicker.regional['esp'] = {
			closeText: 'Cerrar',
			prevText: 'Antes',
			nextText: 'Despues',
			monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
			'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
			'Jul','Ago','Sep','Oct','Nov','Dic'],
			dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
			dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
			dayNamesMin: ['D','L','M','M','J','V','S'],
			weekHeader: 'Sem.',
			dateFormat: 'yy-mm-dd',
			yearSuffix: ''
		};
		$.datepicker.setDefaults($.datepicker.regional['esp']);
	}
	//--------------------------------------------------------
	//	--> Consulta y pinta la lista de pacientes
	//---------------------------------------------------------
	function consultarRegistros()
	{
		blockUI();
		
		if($("#buscFecIn").val() > $("#buscFecFi").val())
		{
			alert("La fecha final es mayor a la inicial");
			return;
		}	
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:   	$('#wemp_pmla').val(),
			accion:         'consultarRegistros',
			buscHis:		$("#buscHis").val(),
			buscCodUnix:	$("#buscCodUnix").val(),
			buscDoc:		$("#buscDoc").val(),
			buscNom:		$("#buscNom").val(),
			buscVac:		$("#buscVac").attr("valor"),
			buscTip:		$("#buscTip").val(),
			buscFecIn:		$("#buscFecIn").val(),
			buscFecFi:		$("#buscFecFi").val()
			
		}, function(respuesta){
			$.unblockUI();
			$("#regVacunas").html(respuesta.Html);
			// --> Activar tooltip
			$(".tooltip").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			
			// --> scroll
			if($("#contScroll").height() > 480)
				$("#contScroll").css({"height":"480px","overflow":"auto","background":"none repeat scroll 0 0"});
			
			$("#divNumReg").html("Num Registros: "+respuesta.numReg+"");
			
		}, 'json');
	}
	//---------------------------------------------------------
	//	--> Abre la modal con el detalle del paciente
	//---------------------------------------------------------
	function imprimirDetalleVac(html)
	{
		blockUI();
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'imprimirDetalleVac',
			html:			html
		}, function(respuesta){
			$.unblockUI();
			$("#duvImprimirDetalleVac").html(respuesta.HtmlPdf).dialog({
				title: "<span style='color:#2A5DB0;font-family:verdana;font-weight:normal'>Imprimir detalle de vacunación:</span>",
				width: 810,
				height: 700,
				modal: true,
				buttons:{
					"Cerrar": function() {
						$( this ).dialog( "close" );
					}					
				},
				close: function( event, ui ) {
				}
			});	
		}, 'json');
	}
	//---------------------------------------------------------
	//	--> Abre la modal con el detalle del paciente
	//---------------------------------------------------------
	function verPaciente(idReg)
	{
		blockUI();
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'verPaciente',
			idReg:			idReg
		}, function(respuesta){
			
			$.unblockUI();
			
			$("#divVerPaciente").html(respuesta.Html).dialog({
				title: "<span style='color:#2A5DB0;font-family:verdana;font-weight:normal'>Detalle de vacunación:</span>",
				width: 900,
				modal: true,
				buttons:{
					"Imprimir": function() {
						imprimirDetalleVac($("#divVerPaciente").html());
					},
					"Cerrar": function() {
						$( this ).dialog( "close" );						
					}					
				},
				close: function( event, ui ) {
				}
			});
			
			// --> Activar tooltip
			$("#divVerPaciente .tooltip").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			
		
		}, 'json');
	}
	function blockUI()
	{
		$.blockUI({
			message: "<div style='background-color: #111111;color:#ffffff;font-size: 15pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Consultando...</div>",
			css:{"border": "2pt solid #7F7F7F"}
		});
	}
	//---------------------------------------------------------
	//	--> Agrega un tr para programar la vacuna
	//---------------------------------------------------------
	function programarVacuna(hisProgramar, campoHis){
		hoy = new Date();
		mes = hoy.getMonth()+1;
		dia = hoy.getDate();
		fec = hoy.getFullYear()+"-"+((mes<10) ? "0"+mes: mes)+"-"+((dia<10) ? "0"+dia: dia);

		$("#detVacPorPac").append("<tr align='center'><td class='fila2'></td><td class='fila2'><input class='bordeRed vacunas' size='30' placeholder='Digite...' type='text' valor=''></td><td class='fila2'></td><td class='fila2'><input class='bordeRed fecha' type='text' size='15' value='"+fec+"'></td><td class='fila2'></td><td class='fila2'></td><td align='center'><img width='13' height='13' style='cursor:pointer;' onClick='agendarVacuna(this, \""+hisProgramar+"\", \""+campoHis+"\")' title='Guardar' src='../../images/medical/root/grabar16.png'>&nbsp;<img width='13' height='13' style='cursor:pointer;' onClick='$(this).parent().parent().remove();' title='Borrar' src='../../images/medical/eliminar1.png'></td></tr>");
		
		$("#detVacPorPac tr:last").find(".fecha").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				
			}
		});
		
		crear_autocomplete("maestroVacunas", $("#detVacPorPac tr:last").find(".vacunas"));
	}
	//---------------------------------------------------------
	//	--> Agendar nueva vacuna
	//---------------------------------------------------------
	function agendarVacuna(elemento, hisProgramar, campoHis){
		$("#detVacPorPac .bordeRojo").removeClass("bordeRojo");
		inputVac = $(elemento).parent().parent().find(".vacunas");
		inputFec = $(elemento).parent().parent().find(".fecha");
		
		guardar = true;
		if(inputVac.attr("valor") == ""){
			inputVac.addClass("bordeRojo");
			guardar = false;
		}
		
		if(inputFec.val() == ""){
			inputFec.addClass("bordeRojo");	
			guardar = false;
		}
		
		if(guardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				accion:         'agendarVacuna',
				hisProgramar:	hisProgramar,
				campoHis:		campoHis,
				inputVac:		inputVac.attr("valor"),
				inputFec:		inputFec.val()
			}, function(respuesta){				
				if(respuesta.idReg)
				{
					htmlMsj = "<span blink style='color:#2A5DB0;font-family:verdana;font-weight:normal'>Vacuna programada!</span>&nbsp;&nbsp;&nbsp;"
					$("button[class^=ui-button]:eq(0)").before(htmlMsj);								
								
					blink = setInterval(function(){
						$("span[blink]").each(function(){
							$(this).css('visibility' , $(this).css('visibility') === 'hidden' ? '' : 'hidden');
						});
					}, 300);
					setTimeout( function(){	
						clearInterval(blink);
						$("span[blink]").remove();
					}, 3000);
					
					$(elemento).parent().parent().html("<td class='fila2'></td><td class='fila2' align='left'>"+inputVac.val()+"</td><td class='fila2'></td><td class='fila2'>"+inputFec.val()+"</td><td class='fila2'></td><td class='fila2'></td><td align='center'><img width='13' height='13' style='cursor:pointer;' onClick='borrarRegAgenda(\""+respuesta.idReg+"\", this)' title='Borrar' src='../../images/medical/eliminar1.png'></td>");
				}	
			}, 'json');
		}
	}
	//---------------------------------------------------------
	//	--> Borrar registro de agenda
	//---------------------------------------------------------
	function borrarRegAgenda(idReg, elemento){
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'borrarRegAgenda',
			idReg:			idReg
		}, function(respuesta){
			if(respuesta.borro)
			{
				$(elemento).parent().parent().remove();
			}	
		}, 'json');
	}
	
	//---------------------------------------------------------
	//	--> Editar el lote de una vacuna
	//---------------------------------------------------------
	function editarLote(elemento){
		
		td = $(elemento).parent();
		td.find(".inputLote").show();
		td.find(".guardarLot").show();
		td.find(".cancelartLot").show();
		td.find(".editarLot").hide();
		td.find(".spanLote").hide();
	}
	//---------------------------------------------------------
	//	--> Cancelar la edicion del lote de una vacuna
	//---------------------------------------------------------
	function cancelarEditarLote(elemento){
		
		td = $(elemento).parent();
		td.find(".inputLote").hide().val(td.find(".spanLote").text());
		td.find(".guardarLot").hide();
		td.find(".cancelartLot").hide();
		td.find(".editarLot").show();
		td.find(".spanLote").show();
	}
	//---------------------------------------------------------
	//	--> Guardar el lote de una vacuna
	//---------------------------------------------------------
	function guardarLote(elemento, idReg){
		
		td 		= $(elemento).parent();
		lote 	= td.find(".inputLote").val(); 
		
		if(lote == "")
		{
			alert("Debe ingresar el lote");
			return;
		}	
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'guardarLote',
			idReg:			idReg,
			lote:			lote
		}, function(respuesta){
			td.find(".inputLote").hide();
			td.find(".guardarLot").hide();
			td.find(".cancelartLot").hide();
			td.find(".spanLote").show().text(lote);
			td.find(".editarLot").show();	
		}, 'json');
	}
	//---------------------------------------------------------
	//	--> Editar fecha de aplicacion de la vacuna
	//---------------------------------------------------------
	function editarFechaApl(elemento){
		
		td = $(elemento).parent();
		td.find(".inputFechaApl").show();
		td.find(".guardarFechaApl").show();
		td.find(".cancelarFechaApl").show();
		td.find(".editarFechaApl").hide();
		td.find(".spanFechaApl").hide();
		
		$(".inputFechaApl").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			maxDate:"+0D",
			onSelect: function(){
				
			}
		});
	}
	//---------------------------------------------------------
	//	--> Cancelar la edicion de la fecha de aplicacion
	//---------------------------------------------------------
	function cancelarFechaApl(elemento){
		
		td = $(elemento).parent();
		td.find(".inputFechaApl").hide().val(td.find(".spanFechaApl").text());
		td.find(".guardarFechaApl").hide();
		td.find(".cancelarFechaApl").hide();
		td.find(".editarFechaApl").show();
		td.find(".spanFechaApl").show();
		$(".inputFechaApl").datepicker( "destroy" );
	}
	//---------------------------------------------------------
	//	--> Guardar la fecha de aplicacion
	//---------------------------------------------------------
	function guardarFechaApl(elemento, idReg){
		
		td 		= $(elemento).parent();
		fecha 	= td.find(".inputFechaApl").val(); 
		
		if(fecha == "")
		{
			alert("Debe ingresar la fecha de aplicacion");
			return;
		}	
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'guardarFechaApl',
			idReg:			idReg,
			fecha:			fecha
		}, function(respuesta){
			td.find(".inputFechaApl").hide();
			td.find(".guardarFechaApl").hide();
			td.find(".cancelarFechaApl").hide();
			td.find(".spanFechaApl").show().text(fecha);
			td.find(".editarFechaApl").show();	
			$(".inputFechaApl").datepicker( "destroy" );
		}, 'json');
	}
	//----------------------------------------------------------
	//	--> Mostrar reporte de los pacientes vacunados por edad
	//----------------------------------------------------------
	function reportePacVacunadosPorEdad(){
		
		hoy = new Date();
		mes = hoy.getMonth()+1;
		dia = hoy.getDate();
		fec = hoy.getFullYear()+"-"+((mes<10) ? "0"+mes: mes)+"-"+((dia<10) ? "0"+dia: dia);
		
		var html = ""+
		 "<br>Fecha Inicial: <input id='repFecIn' class='bordeRed' type='text' size='12' value='"+fec+"'>&nbsp;&nbsp;&nbsp;"+
		 "Fecha Final:   <input id='repFecFi' class='bordeRed' type='text' size='12' value='"+fec+"'>";
		
		$("#divVerPaciente").html(html).dialog({
			title: "<span style='color:#2A5DB0;font-family:verdana;font-weight:normal'>Reporte pacientes vacunados por edad:</span>",
			width: 450,
			height: 150,
			modal: true,
			buttons:{
				"Consultar": function() {
					fechaIni = $("#repFecIn").val();
					fechaFin = $("#repFecFi").val();
					
					if(fechaIni == "" || fechaFin == "")
					{
						alert("Debe seleccionar las fechas a consultar");
						return;
					}
					
					if(fechaIni > fechaFin)
					{
						alert("La fecha final es mayor a la inicial");
						return;
					}
					
					blockUI();
					
					$( this ).dialog( "destroy" );
					
					$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
					{
						consultaAjax:   '',
						wemp_pmla:      $('#wemp_pmla').val(),
						accion:         'reportePacVacunadosPorEdad',
						fechaIni:		fechaIni,
						fechaFin:		fechaFin
					}, function(respuesta){	
						$.unblockUI();
						$("#divVerPaciente").html(respuesta.HtmlPdf).dialog({
							title: "<span style='color:#2A5DB0;font-family:verdana;font-weight:normal'>Reporte pacientes vacunados por edad:</span>",
							width: 810,
							height: 700,
							modal: true,
							buttons:{
								"Cerrar": function() {
									$( this ).dialog( "close" );						
								}					
							},
							close: function( event, ui ) {
							}
						});
					}, 'json');
				},
				"Cerrar": function() {
					$( this ).dialog( "close" );						
				}					
			},
			close: function( event, ui ) {
			}
		});
		
		$("#repFecIn").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				
			}
		});
		$("#repFecFi").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				
			}
		});
	}
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		.fila1
		{
			background-color: 	#C3D9FF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fila2
		{
			background-color: 	#E8EEF7;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.filaAzul {
			background-color: 	#62BBE8;
			color: 				#ffffff;
			font-size: 			7pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fondoBlanco {
			background-color: 	#FFFFFF;
			color: 				#000000;
			font-size: 			7pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.textTooltip {
			background-color: 	#FFFFFF;
			color: 				#000000;
			font-size: 			7pt;
			padding:			1px;
			font-family: 		verdana;
			font-weight: 		normal;
		}
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.bordeRed{
			border-radius: 	4px;
			border:			1px solid #AFAFAF;
		}
		.bordeRojo{
			border-radius: 	4px;
			border:			1px solid red;
		}
		fieldset{
			border: 1px solid #000000;
			border-radius: 5px;
		}
		legend{
			border: 1px solid #000000;
			border-top: 0px;
			font-family: Verdana;
			background-color: #000000;
			color: #ffffff;
			font-size: 11pt;
		}
		.ui-autocomplete{
			max-width: 	480px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	7pt;
		}
		#tooltip{font-family: verdana;font-weight:normal;color: #ffffff;font-size: 7pt;position:absolute;z-index:3000;border:1px solid grey;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY>
	<?php
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
	
	// --> Consultar el maestro de vacunas
	$maestroVacunas = array();
	$sqlVac = "
	SELECT Vaccod, Vacnom
	  FROM ".$wbasedato."_000297
	 WHERE Vacest = 'on'
	 UNION
	SELECT Artcod, Artcom 
	  FROM ".$movhos."_000026
	 WHERE Artest = 'on'
	   AND Artvac = 'on'
	";
	$resVac = mysql_query($sqlVac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVac):</b><br>".$sqlVac."<br>".mysql_error());
	while($rowVac = mysql_fetch_array($resVac))
	{
		$maestroVacunas[$rowVac[0]] = str_replace($caracter_ma, $caracter_ok, utf8_encode(trim(strtolower ($rowVac[1]))));
	}
	
	echo "
	<input type='hidden' id='wemp_pmla' 		value='".$wemp_pmla."'>
	<input type='hidden' id='maestroVacunas' 	value='".json_encode($maestroVacunas)."'>
	
	<fieldset align='center' style='padding:15px;'>
		<legend class='fieldset'>Registros de vacunas</legend>
		<table width='100%'>
			<tr>
				<td><b>Buscar por:</b></td>
				<td class='fila1'>Historia:</td>
				<td class='fila2'><input id='buscHis' class='bordeRed' size='8' placeholder='Digite...' type='text'></td>
				<td class='fila1'>C&oacutedigo Unix:</td>
				<td class='fila2'><input id='buscCodUnix' class='bordeRed' size='8' placeholder='Digite...' type='text'></td>
				<td class='fila1'>Docum:</td>
				<td class='fila2'><input id='buscDoc' class='bordeRed' size='8' placeholder='Digite...' type='text'></td>
				<td class='fila1'>Nombre:</td>
				<td class='fila2'><input id='buscNom' class='bordeRed' size='8' placeholder='Digite...' type='text'></td>
				<td class='fila1'>Vacuna:</td>
				<td class='fila2'><input id='buscVac' class='bordeRed' size='8' placeholder='Digite...' type='text' valor=''></td>
				<td class='fila1'>Tipo:</td>
				<td class='fila2'>
					<select class='bordeRed' id='buscTip'>
						<option value='*'>Todos</option>
						<option value='off'>Por vacunar</option>
						<option value='on' selected='selected'>Vacunados</option>
					</select>
				</td>
				<td class='fila1'>Fecha Vacu:</td>
				<td class='fila2'>
					<input id='buscFecIn' class='bordeRed' type='text' size='8' value='".date("Y-m-01")."'>
					al
					<input id='buscFecFi' class='bordeRed' type='text' size='8' value='".date("Y-m-d")."'>
				</td>
				<td><button style='cursor:pointer;font-size:8pt;' onClick='consultarRegistros()'>Buscar</button></td>
			</tr>
		</table>
		<br>
		<div style='border-top:1px solid grey;color:grey;font-size:7pt;font-family:verdana;' align='right'>
			<span style='cursor:pointer' onClick='reportePacVacunadosPorEdad()' onmouseover='$(this).css({\"color\": \"#2A5DB0\"})' onmouseout='$(this).css({\"color\": \"grey\"})'>
				<img width='14' height='14' src='../../images/medical/sgc/hoja.png'>
				Pacientes vacunados por edad
			</span>
			&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
			<span id='divNumReg'></span>
		</div>
		<br>
		<div id='contScroll'>
			<table width='100%' id='regVacunas'>			
			</table>
		<div>
	</fieldset>
	<div id='duvImprimirDetalleVac' style='display:none' align='center'></div>
	<div id='divVerPaciente' style='display:none;color:#000000;font-size:10pt;padding:1px;font-family:verdana;' align='center'></div>
	";
	?>
	</BODY>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
