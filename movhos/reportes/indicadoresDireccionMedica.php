<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        INDICADORES HOSPITALARIOS PARA LA DIRECCION MEDICA        
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2021-03-08';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//	2020-06-08: Jerson Trujillo: Se corrige warning de division por cero.
//	2020-02-12: Jerson Trujillo: Se agrega cuadro resumen de ocupación dependiendo de parametros en la movhos_11
//  2020-01-28: Jerson Trujillo. Se cambia el calculo de la ocupacion hospitalaria del dia actual, para que no consulte en la movhos_38
//				sino directamente en la movhos_20
//	2018-07-17: Jerson Trujillo. Se modifica el calculo de la ocupacion de cx, para que tenga en cuenta el estado de los quirofanos
//				y omita deñ calculo los quirofanos virtuales.
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	

	include_once("root/comun.php");

    if (is_null($selectsede)){
        $selectsede = consultarsedeFiltro();
    }
	

	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wfecha			= date("Y-m-d");   
    $whora 			= date("H:i:s");
	
	$colores		= array("#AFF8DB", "#C4FBF8", "#85E3FF", "#ADE7FF", "#6DB5FF");

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//---------------------------------------------------------
	// --> Obtener % de ocupacion
	//---------------------------------------------------------
	function ocupacionHospitalaria($fechaBuscar1, &$ocupacionGeneral)
	{
		global $conex;
		global $wbasedato;
		
		$respuesta = array();

        $sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
		
		// --> Ocupacion del dia
		if($fechaBuscar1 == date("Y-m-d")){
			
			$arrInfoOcu = array();
			
			// --> Total habitaciones activas por servicio
			$sqlHabTot = "			
			SELECT 	COUNT(*) as Total, Ccocod as Cieser, Cconoc AS Nombre, Ccotor AS Torre, Ccopis AS Piso, Ccodtp AS tipoPac,
					Ccoioh AS EsHos, Ccouci AS EsUci, Ccouce AS EsUce 
			  FROM ".$wbasedato."_000020 INNER JOIN ".$wbasedato."_000011 ON(Habcco = Ccocod)
			 WHERE Habest = 'on'
			   AND Ccourg != 'on'
			 GROUP BY Habcco
			";
			$resHabTot = mysql_query($sqlHabTot, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlHabTot):</b><br>".mysql_error());
			while($rowHabTot = mysql_fetch_array($resHabTot, MYSQL_ASSOC))
				$arrInfoOcu[$rowHabTot['Cieser']] = $rowHabTot;

			
			// --> Total habitaciones disponibles por servicio
			$sqlHabDis = "			
			SELECT COUNT(*) as t, Habcco
			  FROM ".$wbasedato."_000020 INNER JOIN ".$wbasedato."_000011 ON(Habcco = Ccocod)
			 WHERE Habest = 'on'
			   AND Habdis = 'on'
			   AND Ccourg != 'on'
			 GROUP BY Habcco
			";
			$resHabDis = mysql_query($sqlHabDis, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlHabDis):</b><br>".mysql_error());
			while($rowHabDis = mysql_fetch_array($resHabDis))
				$arrInfoOcu[$rowHabDis['Habcco']]['Disponible'] = $rowHabDis['t'];
			
			// --> Total habitaciones en alistamiento por servicio
			$sqlHabAlis = "			
			SELECT COUNT(*) as t, Habcco
			  FROM ".$wbasedato."_000020 INNER JOIN ".$wbasedato."_000011 ON(Habcco = Ccocod)
			 WHERE Habest = 'on'
			   AND Habali = 'on'
			   AND Habdis != 'on'
			   AND Ccourg != 'on'
			 GROUP BY Habcco
			";
			$resHabAlis = mysql_query($sqlHabAlis, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlHabAlis):</b><br>".mysql_error());
			while($rowHabAlis = mysql_fetch_array($resHabAlis))
				$arrInfoOcu[$rowHabAlis['Habcco']]['Alistamiento'] = $rowHabAlis['t'];
			
			foreach($arrInfoOcu as $habcco => $infoOcu){
				$arrInfoOcu[$habcco]['Ciedis'] = $infoOcu['Disponible']+$infoOcu['Alistamiento'];
				$arrInfoOcu[$habcco]['Cieocu'] = $infoOcu['Total']-$arrInfoOcu[$habcco]['Ciedis'];
			}
			
		}
		// --> Historico
		else{

			$arrInfoOcu = array();
			$sqlOcupacion = "
			SELECT 	A.Cieser, SUM(Ciedis) AS Ciedis, SUM(Cieocu) AS Cieocu, Cconoc AS Nombre, Ccodtp AS tipoPac, Ccotor AS Torre, Ccopis AS Piso, 0 AS Alistamiento,
					Ccoioh AS EsHos, Ccouci AS EsUci, Ccouce AS EsUce 
			  FROM ".$wbasedato."_000038 AS A INNER JOIN ".$wbasedato."_000011 AS B ON(A.Cieser = B.Ccocod AND Ccoest = 'on')
			 WHERE A.Fecha_data = '".$fechaBuscar1."'
			 GROUP BY A.Cieser
			 ORDER BY Ccotor, Ccopis DESC
			";
			$resOcupacion 	= mysql_query($sqlOcupacion, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlOcupacion):</b><br>".mysql_error());
			while($rowOcup = mysql_fetch_array($resOcupacion, MYSQL_ASSOC))
			{
				if($rowOcup['Torre'] == '')
					continue;
				else
					$arrInfoOcu[$rowOcup['Cieser']] = $rowOcup;
			}
		}
		
		$totalCamas 	= 0;
		$totalOcupadas 	= 0;
		
		foreach($arrInfoOcu as $rowOcupacion)
		{
			if($rowOcupacion['Torre'] == '')
				continue;
			
			if(!isset($respuesta[$rowOcupacion['Torre']]))
			{
				$respuesta[$rowOcupacion['Torre']]['totalCamas']		= 0;
				$respuesta[$rowOcupacion['Torre']]['totalOcupadas']		= 0;
				$respuesta[$rowOcupacion['Torre']]['totalAlistamiento']	= 0;
			}
			
			$respuesta[$rowOcupacion['Torre']]['totalCamas']+= $rowOcupacion['Ciedis']+$rowOcupacion['Cieocu'];
			$respuesta[$rowOcupacion['Torre']]['totalOcupadas']+= $rowOcupacion['Cieocu'];
			$respuesta[$rowOcupacion['Torre']]['totalAlistamiento']+= $rowOcupacion['Alistamiento'];
						
			$respuesta[$rowOcupacion['Torre']]['Detalle'][$rowOcupacion['Cieser']]					= $rowOcupacion;
			$respuesta[$rowOcupacion['Torre']]['Detalle'][$rowOcupacion['Cieser']]['Ocupacion'] 	= @($rowOcupacion['Cieocu']/($rowOcupacion['Ciedis']+$rowOcupacion['Cieocu']))*100; 
			$respuesta[$rowOcupacion['Torre']]['Detalle'][$rowOcupacion['Cieser']]['Ocupacion'] 	= number_format($respuesta[$rowOcupacion['Torre']]['Detalle'][$rowOcupacion['Cieser']]['Ocupacion'], 0);
			
			$totalCamas+= $rowOcupacion['Ciedis']+$rowOcupacion['Cieocu'];
			$totalOcupadas+= $rowOcupacion['Cieocu'];
		}		
		// echo "<pre>";
		// print_r($respuesta);
		// echo "</pre>";
		
		if($totalCamas>0)
		{
			$ocupacionGeneral = @number_format(($totalOcupadas/$totalCamas)*100, 0); 
			
		}
		
		return $respuesta;
	}


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
		case 'ocupacionGeneral':
		{
			$ocupacionGeneral		= 0;
			ocupacionHospitalaria($fechaBuscar1, $ocupacionGeneral);
			$respuesta['ocupacion']	= $ocupacionGeneral;
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'verOcupacion':
		{
			$sqlTorres = "
			SELECT Torcod, Tornom 
			  FROM ".$wbasedato."_000234
			 WHERE Torest = 'on'
			";
			$resTorres = mysql_query($sqlTorres, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTorres):</b><br>".mysql_error());
			while($rowTorres = mysql_fetch_array($resTorres, MYSQL_ASSOC))
				$arrayTorres[$rowTorres['Torcod']] = $rowTorres['Tornom'];
			
			$arrayOcupacion 		= ocupacionHospitalaria($fechaBuscar1, $ocupacionGeneral);
			$respuesta['ocupacion']	= $ocupacionGeneral;
			$totalCamasDispCli 		= 0;
			$totalCamasOcupCli 		= 0;
			$totalCamasAlisCli 		= 0;
				
			$respuesta['Html'] = "
			<table width='100%'>
				<tr>";
			foreach ($arrayOcupacion as $torre => $info)
			{
				if($torre == '')
					continue;
				
				$respuesta['Html'].= "
				<td width='50%' align='center' style='padding:3px;color:#000000'>
					<table width='95%' style='border-collapse:collapse;font-size:12pt;font-family:verdana' class='bordeTabla'>
						<tr align='center'><td colspan='6' class='encabezadoTabla' style='font-size:12pt'><b>".$arrayTorres[$torre]." - ".@number_format(($info['totalOcupadas']/$info['totalCamas'])*100, 0)." %</b></td></tr>
						<tr align='center' class='fondoAmarillo' style='font-weight:bold'>
							<td>Tipo de paciente</td>
							<td>Piso</td>
							<td>Disponibles</td>
							<td>Ocupadas</td>
							<td>Total Camas</td>
							<td>Ocupaci&oacute;n</td>
						</tr>
				";
				
				$colorFila 		= 'fila2';
				$totalCamasPiso = 0;
				$totalCamasDisp = 0;
				$totalCamasOcup = 0;
				$totalCamasAlis = 0;
				
				foreach($info['Detalle'] as $cco => $info2)
				{
					// $colorRand = (($colorRand < count($colores)-1) ?  $colorRand+1 : 0);
					$colorFila = (($colorFila == 'fila1') ? "fila2" : "fila1");
					$respuesta['Html'].= "
						<tr align='center' class='".$colorFila."'>
							<td>".$info2['tipoPac']."</td>
							<td>".$info2['Nombre']."</td>
							<td>".$info2['Ciedis']."</td>
							<td>".$info2['Cieocu']."</td>
							<td>".($info2['Cieocu']+$info2['Ciedis'])."</td>
							<td style='font-size:12pt'><b>".$info2['Ocupacion']." %</b></td>
						</tr>";
						
					$totalCamasPiso+= ($info2['Cieocu']+$info2['Ciedis']);
					$totalCamasOcup+= $info2['Cieocu'];
					$totalCamasDisp+= $info2['Ciedis'];
					$totalCamasAlis+= $info2['Alistamiento'];
					
					// --> Esto es para el cuadro resumen
					
					
					if($info2['EsUci'] == 'on'){
						// --> UCI
						$resumen['UCI']['DIS'] = $info2['Ciedis'];
						$resumen['UCI']['OCU'] = $info2['Cieocu'];
					}elseif($info2['EsUce'] == 'on'){
							// --> UCE
							$resumen['UCE']['DIS']+= $info2['Ciedis'];
							$resumen['UCE']['OCU']+= $info2['Cieocu'];
						}
						elseif($info2['EsHos'] == 'on'){
								// --> Hospitalizacion
								$resumen['HOS']['DIS']+= $info2['Ciedis'];
								$resumen['HOS']['OCU']+= $info2['Cieocu'];
							}
							else
								$excluyen.= $info2['Nombre'].", "; 			
				}
				
				$totalCamasDispCli+= $totalCamasDisp;
				$totalCamasOcupCli+= $totalCamasOcup;
				$totalCamasAlisCli+= $totalCamasAlis;
				
				$respuesta['Html'].= "
						<tr class='filaB' style='font-weight:bold;font-size:10pt;font-family:verdana'>
							<td colspan='2' align='right'>Total&nbsp;</td>
							<td align='center'>".$totalCamasDisp."</td>
							<td align='center'>".$totalCamasOcup."</td>
							<td align='center'>".$totalCamasPiso."</td>
							<td></td>
						</tr>
					</table>";
				
				if($torre == 'T4')
					$respuesta['Html'].= "
						<br>
						<table width='95%' class='fondoAmarillo bordeTabla' style='cursor:pointer;border-collapse:collapse;font-size:12pt;font-family:verdana'>
							<tr align='center'><td colspan='2' onclick='verUrgencias(\"".$fechaBuscar1."\")'><b>Urgencias</b></td></tr>
						</table>
						<br>
						<table width='95%' class='fondoAmarillo bordeTabla' style='cursor:pointer;border-collapse:collapse;font-size:12pt;font-family:verdana'>
							<tr align='center'><td colspan='2' onClick='verCx()'><b>Cirugía</b></td></tr>
						</table>
					</td>
					";
			}
			$respuesta['Html'].= "
			</table>
			";
			
			$respuesta['Html'].= "
			<br>
			<table width='50%' style='border-collapse:collapse;font-size:12pt;font-family:verdana' class='bordeTabla'>
				<tr><td align='center' colspan='4' class='encabezadoTabla'><b>Cuadro resumen (Torre 3 y 4)</b></td></tr>
				<tr style='font-weight:bold'><td class='fondoAmarillo'>Area</td><td class='fondoAmarillo'>Ocupadas</td><td class='fondoAmarillo'>Total Camas</td><td class='fondoAmarillo'>Ocupación</td>
				</tr>
				<tr>
					<td class='fila1'><b>Ocupación en hopsitalización</b><br><span style='font-size:7pt'>(No se incluye: ".$excluyen.")</span></td>
					<td class='fila2'>".$resumen['HOS']['OCU']."</td>
					<td class='fila2'>".($resumen['HOS']['DIS']+$resumen['HOS']['OCU'])."</td>
					<td class='fila2'>".@round (($resumen['HOS']['OCU']/($resumen['HOS']['DIS']+$resumen['HOS']['OCU']))*100)." %</td>
				</tr>
				<tr>
					<td class='fila1'><b>Ocupación de UCE</b></td>
					<td class='fila2'>".$resumen['UCE']['OCU']."</td>
					<td class='fila2'>".($resumen['UCE']['DIS']+$resumen['UCE']['OCU'])."</td>
					<td class='fila2'>".@round (($resumen['UCE']['OCU']/($resumen['UCE']['DIS']+$resumen['UCE']['OCU']))*100)." %</td>
				</tr>
				<tr>
					<td class='fila1'><b>Ocupación de UCI</b></td>
					<td class='fila2'>".$resumen['UCI']['OCU']."</td>
					<td class='fila2'>".($resumen['UCI']['DIS']+$resumen['UCI']['OCU'])."</td>
					<td class='fila2'>".@round (($resumen['UCI']['OCU']/($resumen['UCI']['DIS']+$resumen['UCI']['OCU']))*100)." %</td>
				</tr>
			</table>
			";
			
			$respuesta['totalCamasDispCli'] = $totalCamasDispCli-$totalCamasAlisCli;
			$respuesta['totalCamasOcupCli'] = $totalCamasOcupCli;
			$respuesta['totalCamasAlisCli'] = $totalCamasAlisCli;
			$respuesta['totalCamasCli'] 	= $totalCamasDispCli+$totalCamasOcupCli;
			
			
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'verUrgencias':
		{			
			$respuesta['Html'] 	 = "<img id='planoUrgencias' width='100%' height='100%' src='../../images/medical/movhos/PLANOURGENCIAS.jpg' style='border-radius:8px;opacity:0.9'>";
			$arrayOcupacion = array();
			$ccoUrg 		= "1130";
			$movhos 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			$basedatoshce 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
			$cliame 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
			
			$codHabExcluida		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'codHabExcluidasIndicadorOcupacion');
			$codHabExcluida 	= explode(',', $codHabExcluida);
			foreach($codHabExcluida as &$codHabEx)
				$codHabEx = trim($codHabEx);
			
			// --> Ocupacion zonas de urgencias
			$sqlOcu = "
			SELECT Habcod, Habcpa, Habhis, Habing, Habvir, Habdis, Habzon, Habord, Aredes, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) as nomPac, Pacdoc, Pactdo, Mtrtur 
			  FROM ".$wbasedato."_000020 INNER JOIN ".$wbasedato."_000169 ON(Habzon = Arecod) 
				   LEFT JOIN ".$cliame."_000100 ON(Habhis = Pachis)
				   LEFT JOIN ".$basedatoshce."_000022 ON(Habhis = Mtrhis AND Habing = Mtring)
			 WHERE Habcco = '".$ccoUrg."' 
			   AND Habest = 'on'
			   AND Habcub = 'on'
			 ORDER BY Habzon, Habord 
			";
			$respuesta['sqlOcu'] = $sqlOcu;
			$resOcu = mysql_query($sqlOcu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlOcu):</b><br>".mysql_error());
			while($rowOcu = mysql_fetch_array($resOcu))
			{
				if(!isset($arrayOcupacion[$rowOcu['Habzon']]))
				{
					$arrayOcupacion[$rowOcu['Habzon']]['totalHab']		= 0;
					$arrayOcupacion[$rowOcu['Habzon']]['totalHabOcu'] 	= 0;
					$arrayOcupacion[$rowOcu['Habzon']]['totalHabVir']	= 0;
					$arrayOcupacion[$rowOcu['Habzon']]['totalHabOcuVir']= 0;
					$arrayOcupacion[$rowOcu['Habzon']]['totalPac'] 		= 0;
				}	
				
				if(!in_array($rowOcu['Habcod'], $codHabExcluida))	
				{
					$arrayOcupacion[$rowOcu['Habzon']]['totalHabVir']+= (($rowOcu['Habvir'] == 'on') ? 1 : 0); 
					$arrayOcupacion[$rowOcu['Habzon']]['totalHabOcuVir']+= (($rowOcu['Habvir'] == 'on' && $rowOcu['Habdis'] == 'off') ? 1 : 0);
					
					$arrayOcupacion[$rowOcu['Habzon']]['totalHab']+= (($rowOcu['Habvir'] != 'on') ? 1 : 0); 
					$arrayOcupacion[$rowOcu['Habzon']]['totalHabOcu']+= (($rowOcu['Habvir'] != 'on' && $rowOcu['Habdis'] == 'off') ? 1 : 0);
					$arrayOcupacion[$rowOcu['Habzon']]['totalPac']+= (($rowOcu['Habdis'] == 'off') ? 1 : 0);
				}
				
				$arrayOcupacion[$rowOcu['Habzon']]['nomZona'] 										= $rowOcu['Aredes'];
				$arrayOcupacion[$rowOcu['Habzon']]['Habitaciones'][$rowOcu['Habcod']]['Nombre']		= $rowOcu['Habcpa'];
				$arrayOcupacion[$rowOcu['Habzon']]['Habitaciones'][$rowOcu['Habcod']]['Disponible']	= $rowOcu['Habdis'];
				$arrayOcupacion[$rowOcu['Habzon']]['Habitaciones'][$rowOcu['Habcod']]['Virtual']	= $rowOcu['Habvir'];
				$arrayOcupacion[$rowOcu['Habzon']]['Habitaciones'][$rowOcu['Habcod']]['Documento']	= $rowOcu['Pacdoc'];
				$arrayOcupacion[$rowOcu['Habzon']]['Habitaciones'][$rowOcu['Habcod']]['tipoDoc']	= $rowOcu['Pactdo'];
				$arrayOcupacion[$rowOcu['Habzon']]['Habitaciones'][$rowOcu['Habcod']]['Historia']	= $rowOcu['Habhis']."-".$rowOcu['Habing'];
				$arrayOcupacion[$rowOcu['Habzon']]['Habitaciones'][$rowOcu['Habcod']]['nomPac']		= utf8_encode(($rowOcu['nomPac'] != '') ? $rowOcu['nomPac'] : '&nbsp;');
				$arrayOcupacion[$rowOcu['Habzon']]['Habitaciones'][$rowOcu['Habcod']]['Turno']		= utf8_encode(($rowOcu['Mtrtur'] != '') ? $rowOcu['Mtrtur'] : '&nbsp;');
			}			
			
			$totalHabUrg 	= 0; 
			$totalHabOcu 	= 0;
			$totalHabVirOcu = 0;
			
			foreach($arrayOcupacion as $zona => &$info)
			{
				$habOcupadas		= $info['totalHabOcu'];
				
				// --> Si la ocupacion de las habitaciones fisicas es del 100%, entonces se le suma al numerador las virtuales ocupadas
				$valTem				= ($habOcupadas == $info['totalHab']) ? $info['totalHabOcuVir'] : 0;
				
				$info['Ocupacion'] 	= @(($habOcupadas+$valTem)/$info['totalHab'])*100;
				$info['Ocupacion'] 	= number_format($info['Ocupacion'], 0);
				
				$totalHabUrg+= 		$info['totalHab'];
				$totalHabOcu+= 		$habOcupadas;
				$totalHabVirOcu+= 	$valTem;
			}
			
			$respuesta['arrayOcupacion'] = print_r($arrayOcupacion, true);
			
			// --> Numero de pacientes en sala de espera
			$pacSala	= array("enUrg" => 0);
			$fechaC 	= date("Y-m-d");
			// $fechaC 	= "2017-06-30";
			
			$pacSala['penTriage']["Niños"]['Can'] 		= 0;
			$pacSala['penTriage']["Adultos"]['Can'] 	= 0;
			$pacSala['penAdmision']["Niños"]['Can'] 	= 0;
			$pacSala['penAdmision']["Adultos"]['Can'] 	= 0;
			$pacSala['penConsulta']["Niños"]['Can'] 	= 0;
			$pacSala['penConsulta']["Adultos"]['Can'] 	= 0;
			
			$sqlTurnos 	= "
			SELECT Atutur, Atuetr, Atuutr, Atucta, Atupad, Atuven, Atuusu, Atuadm, Atuctl, Atuulc, Atucon, Atueda, Atuted, Atunom, Fecha_data, Hora_data, Atufll, Atuhll
			  FROM ".$movhos."_000178
			 WHERE Fecha_data = '".$fechaC."'
			   AND Atuest = 'on'
			   AND Atuaor != 'on'
			 ORDER BY REPLACE(Atutur, '-', '')*1 ASC
			";
			$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());

			while($rowTurnos = mysql_fetch_array($resTurnos))
			{
				$tiempoEspera 				= gmdate("H:i:s", (strtotime(date("Y-m-d H:i:s"))-strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data'])));
				$rowTurnos['tiempoEspera'] 	= $tiempoEspera;
				$rowTurnos['obsAdm']		= "";
				
				// --> Si no tiene triage
				if($rowTurnos['Atucta'] != 'on')
				{
					// --> En triage
					if($rowTurnos['Atuetr'] == 'on')
					{
						if(!isset($pacSala['enTriage'][$rowTurnos['Atuctl']]['Can']))
							$pacSala['enTriage'][$rowTurnos['Atuctl']]['Can'] = 0;
						
						$pacSala['enTriage'][$rowTurnos['Atuctl']]['Can']+= 1 ;
						
						// --> Consultar nombre del usuario
						$sqlNomUsu = "
						SELECT Descripcion
						  FROM usuarios
						 WHERE Codigo = '".$rowTurnos['Atuutr']."'
						";
						$resNomUsu 	= mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
						if($rowNomUsu = mysql_fetch_array($resNomUsu))
						{
							$nomUsu 	= explode(' ', strtolower($rowNomUsu['Descripcion']));
							$nomUsu[2] 	= "<br>".$nomUsu[2];
							unset($nomUsu[3]);
							$nomUsu 	= implode(" ", $nomUsu);
						}
						else
							$nomUsu = "?";
						
						$pacSala['enTriage'][$rowTurnos['Atuctl']]['Med'] 	= $nomUsu;
						$pacSala['enTriage'][$rowTurnos['Atuctl']]['Det'][] = $rowTurnos;	
					}
					// --> Pendiente de triage
					else
					{
						if($rowTurnos['Atuted'] != 'A' || $rowTurnos['Atueda'] <= 12)
							$tipoPac = "Niños";
						else
							$tipoPac = "Adultos";
						
						$pacSala['penTriage'][$tipoPac]['Can']+= 1;
						$pacSala['penTriage'][$tipoPac]['Det'][] = $rowTurnos;
					}
				}
				else
				{
					$sql22 = "
					SELECT *
					  FROM ".$basedatoshce."_000022
					 WHERE Mtrtur = '".$rowTurnos['Atutur']."'
					   AND Mtrest = 'on'
					";
					$res22 = mysql_query($sql22, $conex) or die("<b>ERROR EN QUERY MATRIX(sql22):</b><br>".mysql_error());					
							
					// --> No tiene admision
					if($rowTurnos['Atuadm'] != 'on' && mysql_num_rows($res22) == 0)
					{
						// --> En proceso de admision
						if($rowTurnos['Atupad'] == 'on')
						{
							if(!isset($pacSala['enAdmision'][$rowTurnos['Atuven']]['Can']))
								$pacSala['enAdmision'][$rowTurnos['Atuven']]['Can'] = 0;
							
							$pacSala['enAdmision'][$rowTurnos['Atuven']]['Can']+= 1;
							
							// --> Consultar nombre del usuario
							$sqlNomUsu = "
							SELECT Descripcion
							  FROM usuarios
							 WHERE Codigo = '".$rowTurnos['Atuusu']."'
							";
							$resNomUsu 	= mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
							if($rowNomUsu = mysql_fetch_array($resNomUsu))
							{
								$nomUsu 	= explode(' ', strtolower($rowNomUsu['Descripcion']));
								$nomUsu[2] 	= "<br>".$nomUsu[2];
								unset($nomUsu[3]);
								$nomUsu 	= implode(" ", $nomUsu);
							}
							else
								$nomUsu = "?";
							
							// --> Si lleva mas de 15 minutos, es porque la admision quedó mal finalizada
							$tiempoEnAdmi = (strtotime(date("Y-m-d H:i:s"))-strtotime($rowTurnos['Atufll']." ".$rowTurnos['Atuhll']))/60;							
							if($tiempoEnAdmi >= 15)
								$rowTurnos['obsAdm'] = "<span style=color:red><b>Admision mal finalizada</b></span>";
							
							$pacSala['enAdmision'][$rowTurnos['Atuven']]['Med'] = $nomUsu;					
							$pacSala['enAdmision'][$rowTurnos['Atuven']]['Det'][] = $rowTurnos;					
						}
						// --> Pendiente de admision
						else
						{
							if($rowTurnos['Atuted'] != 'A' || $rowTurnos['Atueda'] <= 13)
								$tipoPac = "Niños";
							else
								$tipoPac = "Adultos";
							
							$pacSala['penAdmision'][$tipoPac]['Can']+= 1;
							$pacSala['penAdmision'][$tipoPac]['Det'][] = $rowTurnos;;
						}
					}
					else
					{
						// --> Ya tiene admision
						$sqlInfo22 = "
						SELECT Mtrcur, Mtrfco
						  FROM ".$basedatoshce."_000022 INNER JOIN ".$movhos."_000018 ON (Mtrhis = Ubihis AND Mtring = Ubiing)
						 WHERE Mtrtur = '".$rowTurnos['Atutur']."'
						   AND Mtrest = 'on'
						   AND Ubiald != 'on'
						";
						$resInfo22 = mysql_query($sqlInfo22, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo22):</b><br>".mysql_error());						
						if($rowInfo22 = mysql_fetch_array($resInfo22, MYSQL_ASSOC))
						{
							// --> En consulta
							if($rowInfo22['Mtrcur'] == 'on')
							{
								if(!isset($pacSala['enConsulta'][$rowTurnos['Atucon']]['Can']))
									$pacSala['enConsulta'][$rowTurnos['Atucon']]['Can'] = 0;
								
								$pacSala['enConsulta'][$rowTurnos['Atucon']]['Can']+= 1;
								
								// --> Consultar nombre del usuario
								$sqlNomUsu = "
								SELECT Descripcion
								  FROM usuarios
								 WHERE Codigo = '".$rowTurnos['Atuulc']."'
								";
								$resNomUsu 	= mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
								if($rowNomUsu = mysql_fetch_array($resNomUsu))
								{
									$nomUsu 	= explode(' ', strtolower($rowNomUsu['Descripcion']));
									$nomUsu[2] 	= "<br>".$nomUsu[2];
									unset($nomUsu[3]);
									$nomUsu 	= implode(" ", $nomUsu);
								}
								else
									$nomUsu = "?";
								
								$pacSala['enConsulta'][$rowTurnos['Atucon']]['Med'] = $nomUsu;								
								$pacSala['enConsulta'][$rowTurnos['Atucon']]['Det'][] = $rowTurnos;									
							}
							// --> Pendiente de consulta
							elseif($rowInfo22['Mtrfco'] == '0000-00-00')
								{
									if($rowTurnos['Atuted'] != 'A' || $rowTurnos['Atueda'] <= 13)
										$tipoPac = "Niños";
									else
										$tipoPac = "Adultos";
									
									$pacSala['penConsulta'][$tipoPac]['Can']+= 1;
									$pacSala['penConsulta'][$tipoPac]['Det'][] = $rowTurnos;
								}
							// --> Ya ingresado en urgencias			
								else
									$pacSala['enUrg']+= 1;
						}
					}	
				}
			}

			$respuesta['pacSala'] = print_r($pacSala, true);
			
			$pacAlta = array("Cant" => 0);
			// --> Cantidad de pacientes pendientes de alta
			$sqlPacAlta = "
			SELECT Ubihis, Ubiing, Ubifap, Ubihap, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) as nomPac 	
			 FROM ".$movhos."_000018 INNER JOIN ".$cliame."_000100 ON(Ubihis = Pachis)
			WHERE Ubisac = '".$ccoUrg."'
			  AND Ubialp = 'on'
			  AND Ubiald != 'on'";
			$resPacAlta = mysql_query($sqlPacAlta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPacAlta):</b><br>".mysql_error());
			while($rowPacAlta = mysql_fetch_array($resPacAlta, MYSQL_ASSOC))
			{
				$pacAlta["Cant"]+= 1;
				$pacAlta["Det"][] = $rowPacAlta;
			}
			
			// --> Pacientes pendientes de alta
			if($pacAlta['Cant'] > 0)
				$arrayCoordenadas["13-11"] = "<span infoDet='".json_encode($pacAlta["Det"])."' onClick='verPacientesAlta(this)' style='cursor:pointer;color:red;font-size:9pt;font-family:verdana;'>".$pacAlta['Cant']." Alta</span>";
			
			// --> Coordenadas
			// --> Emergencias
			$arrayCoordenadas["4-12"] 	= "<span style='font-size:12pt;cursor:pointer' infoZOna='".json_encode($arrayOcupacion["EM"])."' onClick=verPacientesZona(this)>".$arrayOcupacion["EM"]['Ocupacion']."%</span>";
			$arrayCoordenadas["5-12"] 	= "<span style='font-size:9pt;cursor:default'>".$arrayOcupacion["EM"]['totalPac']."Pac</span>";
			// --> Covid
			$arrayCoordenadas["8-15"] 	= "<span style='font-size:12pt;cursor:pointer' infoZOna='".json_encode($arrayOcupacion["COVID"])."' onClick=verPacientesZona(this)>".$arrayOcupacion["COVID"]['Ocupacion']."%</span>";
			$arrayCoordenadas["9-15"] 	= "<span style='font-size:9pt;cursor:default'>".$arrayOcupacion["COVID"]['totalPac']."Pac</span>";
			// --> Sala 2
			$arrayCoordenadas["6-3"] 	= "<span style='font-size:12pt;cursor:pointer' infoZOna='".json_encode($arrayOcupacion["S2"])."' onClick=verPacientesZona(this)>".$arrayOcupacion["S2"]['Ocupacion']."%</span>";
			$arrayCoordenadas["7-3"] 	= "<span style='font-size:9pt;cursor:default'>".$arrayOcupacion["S2"]['totalPac']."Pac</span>";
			// --> Sala 1
			$arrayCoordenadas["11-2"] 	= "<span style='font-size:12pt;cursor:pointer' infoZOna='".json_encode($arrayOcupacion["S1"])."' onClick=verPacientesZona(this)>".$arrayOcupacion["S1"]['Ocupacion']."%</span>";
			$arrayCoordenadas["12-2"] 	= "<span style='font-size:9pt;cursor:default'>".$arrayOcupacion["S1"]['totalPac']."Pac</span>";
			// --> Covid
			$arrayCoordenadas["11-4"] 	= "<span style='font-size:12pt;cursor:pointer' infoZOna='".json_encode($arrayOcupacion["PE"])."' onClick=verPacientesZona(this)>".$arrayOcupacion["PE"]['Ocupacion']."%</span>";
			$arrayCoordenadas["12-4"] 	= "<span style='font-size:9pt;cursor:default'>".$arrayOcupacion["PE"]['totalPac']."Pac</span>";
			// --> Sala rapida 1
			$arrayCoordenadas["14-15"] 	= "<span style='font-size:12pt;cursor:pointer' infoZOna='".json_encode($arrayOcupacion["SAR"])."' onClick=verPacientesZona(this)>".$arrayOcupacion["SAR"]['Ocupacion']."%</span>";
			$arrayCoordenadas["15-15"] 	= "<span style='font-size:9pt;cursor:default'>".$arrayOcupacion["SAR"]['totalPac']."Pac</span>";
			// --> Sala rapida 2
			$arrayCoordenadas["14-2"] 	= "<span style='font-size:12pt;cursor:pointer' infoZOna='".json_encode($arrayOcupacion["SAR1"])."' onClick=verPacientesZona(this)>".$arrayOcupacion["SAR1"]['Ocupacion']."%</span>";
			$arrayCoordenadas["15-2"] 	= "<span style='font-size:9pt;cursor:default'>".$arrayOcupacion["SAR1"]['totalPac']."Pac</span>";
			// --> En espera de triage
			$arrayCoordenadas["15-4"] 	= "<span style='font-size:12pt;cursor:pointer' infoDet='".json_encode($pacSala['penTriage']['Niños']['Det'])."' onClick='verPacientesEspera(this, \"En espera de triage (Niños)\")'>".$pacSala['penTriage']['Niños']['Can']." Ni</span>";
			$arrayCoordenadas["15-5"] 	= "<span style='font-size:12pt;cursor:pointer' infoDet='".json_encode($pacSala['penTriage']['Adultos']['Det'])."' onClick='verPacientesEspera(this, \"En espera de triage (Adultos)\")'>".$pacSala['penTriage']['Adultos']['Can']." Ad</span>";
			// --> En espera de admision
			$arrayCoordenadas["15-6"] 	= "<span style='font-size:12pt;cursor:pointer' infoDet='".json_encode($pacSala['penAdmision']['Niños']['Det'])."' onClick='verPacientesEspera(this, \"En espera de admision (Niños)\")'>".$pacSala['penAdmision']['Niños']['Can']." Ni</span>";
			$arrayCoordenadas["15-7"] 	= "<span style='font-size:12pt;cursor:pointer' infoDet='".json_encode($pacSala['penAdmision']['Adultos']['Det'])."' onClick='verPacientesEspera(this, \"En espera de admision (Adultos)\")'>".$pacSala['penAdmision']['Adultos']['Can']." Ad</span>";
			// --> En espera de consulta
			$arrayCoordenadas["15-9"] 	= "<span style='font-size:12pt;cursor:pointer' infoDet='".json_encode($pacSala['penConsulta']['Niños']['Det'])."' onClick='verPacientesEspera(this, \"En espera de consulta (Niños)\")'>".$pacSala['penConsulta']['Niños']['Can']." Ni</span>";
			$arrayCoordenadas["15-10"] 	= "<span style='font-size:12pt;cursor:pointer' infoDet='".json_encode($pacSala['penConsulta']['Adultos']['Det'])."' onClick='verPacientesEspera(this, \"En espera de consulta (Adultos)\")'>".$pacSala['penConsulta']['Adultos']['Can']." Ad</span>";
			
			$imgPersona = "<img tooltip='si' blink='' width='16px' heigth='16px' src='../../images/medical/root/personasilueta.png'>";
			$span 		= "<span style='display:none;color:#000000;font-size:7pt;font-family:verdana;font-weight:normal'>";
			
			// --> Los codigos de los consultorios está en la tabla movhos_000180
			
			// --> Ventanilla admision 1
			if($pacSala['enAdmision']['01']['Can'] > 0)
				$arrayCoordenadas["14-11"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enAdmision']['01']['Det'])."' onClick='verPacientesEspera(this, \"En admision\")'>".$imgPersona.$span.$pacSala['enAdmision']['01']['Med']."</span></span>";
			// --> Ventanilla admision 2
			if($pacSala['enAdmision']['02']['Can'] > 0)
				$arrayCoordenadas["15-11"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enAdmision']['02']['Det'])."' onClick='verPacientesEspera(this, \"En admision\")'>".$imgPersona.$span.$pacSala['enAdmision']['02']['Med']."</span></span>";
			// --> Consultorio triage 1
			if($pacSala['enTriage']['23']['Can'] > 0)
				$arrayCoordenadas["13-8"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enTriage']['23']['Det'])."' onClick='verPacientesEspera(this, \"En triage\")'>".$imgPersona.$span.$pacSala['enTriage']['23']['Med']."</span></span>";
			// --> Consultorio triage 2
			if($pacSala['enTriage']['22']['Can'] > 0)
				$arrayCoordenadas["13-6"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enTriage']['22']['Det'])."' onClick='verPacientesEspera(this, \"En triage\")'>".$imgPersona.$span.$pacSala['enTriage']['22']['Med']."</span></span>";
			// --> Consultorio adulto 1
			if($pacSala['enConsulta']['07']['Can'] > 0)
				$arrayCoordenadas["12-8"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['07']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['07']['Med']."</span></span>";
			// --> Consultorio adulto 2
			if($pacSala['enConsulta']['08']['Can'] > 0)
				$arrayCoordenadas["11-8"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['08']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['08']['Med']."</span></span>";
			// --> Consultorio adulto 3
			if($pacSala['enConsulta']['05']['Can'] > 0)
				$arrayCoordenadas["10-8"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['05']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['05']['Med']."</span></span>";
			// --> Consultorio adulto 4
			if($pacSala['enConsulta']['10']['Can'] > 0)
				$arrayCoordenadas["9-8"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['10']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['10']['Med']."</span></span>";
			// --> Consultorio pediatria 1
			if($pacSala['enConsulta']['03']['Can'] > 0)
				$arrayCoordenadas["12-10"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['03']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['03']['Med']."</span></span>";
			// --> Consultorio pediatria 2
			if($pacSala['enConsulta']['04']['Can'] > 0)
				$arrayCoordenadas["10-10"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['04']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['04']['Med']."</span></span>";
			// --> Consultorio especialista
			if($pacSala['enConsulta']['16']['Can'] > 0)
				$arrayCoordenadas["11-10"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['16']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['16']['Med']."</span></span>";
			// --> Consultorio adulto 5
			// if($pacSala['enConsulta']['09']['Can'] > 0)
				// $arrayCoordenadas["12-10"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['09']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['09']['Med']."</span></span>";
			// --> Consultorio adulto 6   
			// if($pacSala['enConsulta']['25']['Can'] > 0)
				// $arrayCoordenadas["16-3"] = "<span style='cursor:pointer' infoDet='".json_encode($pacSala['enConsulta']['25']['Det'])."' onClick='verPacientesEspera(this, \"En consulta\")'>".$imgPersona.$span.$pacSala['enConsulta']['25']['Med']."</span></span>";
			
			// --> Pintar cuadricula de 10 x 10 //border='1'
			$respuesta['Html'].= "
			<table id='datosPlanoUrg' class='' style='border-collapse:collapse;display:none;color:blue;font-size:11pt;font-family:verdana;font-weight:bold'>
			";
			
			$numCuadros = 16;
			for($x=1; $x<=$numCuadros;$x++)
			{
				$respuesta['Html'].= "
				<tr>";
				
				for($y=1; $y<=$numCuadros;$y++)
				{
					$dato = '&nbsp;';
					if(array_key_exists($x."-".$y, $arrayCoordenadas))
						$dato = $arrayCoordenadas[$x."-".$y];
					// else
						// $dato = $x."-".$y;
					
					$respuesta['Html'].= "
					<td width='".(100/$numCuadros)."%' align='center' class='bordeLetra'>".$dato."</td>";						
				}
				
				$respuesta['Html'].= "
				</tr>";
			}
			$respuesta['Html'].= "
			</table>
			";			
			
			
			$respuesta['detOcupacion'] 	= $arrayOcupacion;
			$respuesta['totalHabUrg'] 	= $totalHabUrg;
			
			// --> Si la ocupacion de las habitaciones fisicas es del 100%, entonces se le suma al numerador las virtuales ocupadas
			$tempNum = ($totalHabOcu == $totalHabUrg) ? $totalHabVirOcu : 0; 
			
			$respuesta['ocupacionGen'] 	= number_format((($totalHabOcu+$tempNum)/$totalHabUrg)*100, 0);
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'verCx':
		{	
			$tcx 				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
			$movhos 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			$respuesta['Html'] 	= "";
			$arrayQx			= array();
			$arrMedicosQx		= array();
			$totalCx			= array("Rea" => 0, "Can" => 0, "Adi" => 0);
			
			
			$sqlQx = "
			SELECT Turfec, Turtur, Turhin, Turhfi, Turqui as Qui, 'Rea' as Tip, Quides, Quicco, Cconoc, Turtip, Turmed 
			  FROM ".$tcx."_000011 INNER JOIN ".$tcx."_000012 ON(Turqui = Quicod) 
			       INNER JOIN ".$movhos."_000011 ON(Quicco = Ccocod)
			 WHERE Turfec = '".$fechaBuscar1."'
			   AND Turest = 'on'
			   AND Quiest = 'on'
			   AND Quivir != 'on'
			 UNION
			SELECT Mcafec, Mcatur, Mcahin, Mcahfi, Mcaqui as Qui, 'Can' as Tip, Quides, Quicco, Cconoc, '', '' Turmed
			  FROM ".$tcx."_000007 AS A INNER JOIN ".$tcx."_000012 AS B ON(Mcaqui = Quicod) 
			       INNER JOIN ".$movhos."_000011 ON(Quicco = Ccocod) 
			 WHERE A.Fecha_data = '".$fechaBuscar1."'
			   AND Mcafec = '".$fechaBuscar1."'
			   AND Quiest = 'on'
			   AND Quivir != 'on'
			 ORDER BY Qui
			";
			$respuesta['sqlQx'] = $sqlQx;
			$resQx = mysql_query($sqlQx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlQx):</b><br>".mysql_error());
			while($rowQx = mysql_fetch_array($resQx))
			{
				if($rowQx['Tip'] == 'Rea')
					$arrMedicosQx[] = $rowQx['Turmed'];	
				
				$masSegundo = 0;
				if($rowQx['Turhfi'] == '24:00')
				{
					$rowQx['Turhfi'] = '23:59';
					$masSegundo = 60;
				}
				
				$duracion = (strtotime('1970-01-01 '.$rowQx['Turhfi'].':00')+$masSegundo)-strtotime('1970-01-01 '.$rowQx['Turhin'].':00');
								
				if($rowQx['Tip'] == 'Rea' && $rowQx['Turtip'] == 'U')
					$rowQx['Tip'] = 'Adi';
				
				if(!isset($arrayQx[$rowQx['Quicco']]['Detalle'][$rowQx['Qui']][$rowQx['Tip']]))
					$arrayQx[$rowQx['Quicco']]['Detalle'][$rowQx['Qui']][$rowQx['Tip']] = 0;
				
				$arrayQx[$rowQx['Quicco']]['NomCco'] 								= $rowQx['Cconoc'];
				$arrayQx[$rowQx['Quicco']]['Detalle'][$rowQx['Qui']]['NomQui'] 		= $rowQx['Quides'];
				$arrayQx[$rowQx['Quicco']]['Detalle'][$rowQx['Qui']][$rowQx['Tip']]+= $duracion;
				
				// --> Total de cx
				$totalCx[$rowQx['Tip']]++;
			}
			
			$arrayMed = array();
			$sqlMedEsp = "
			SELECT Mednom, Espcod, Espdet
			  FROM ".$tcx."_000006 INNER JOIN ".$tcx."_000005 ON(Medesp = Espcod)
			 WHERE Medest = 'on'
			";
			$resMedEsp = mysql_query($sqlMedEsp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMedEsp):</b><br>".mysql_error());
			while($rowMedEsp = mysql_fetch_array($resMedEsp))
			{
				$arrayMed[$rowMedEsp['Mednom']]['CodEspe'] = $rowMedEsp['Espcod'];
				$arrayMed[$rowMedEsp['Mednom']]['NomEspe'] = $rowMedEsp['Espdet'];
			}
			
			$arrayCxEsp = array();
			// $sqlQxEspe = "
			// SELECT Turmed
			  // FROM ".$tcx."_000008 INNER JOIN ".$tcx."_000011 ON(Mcitur = Turtur)
			 // WHERE Mcifec = '".$fechaBuscar1."'
			   // AND Turest = 'on'			 
			// ";
			// $resQxEspe = mysql_query($sqlQxEspe, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlQxEspe):</b><br>".mysql_error());
			// while($rowQxEspe = mysql_fetch_array($resQxEspe))
				
			foreach($arrMedicosQx as $valMedico)		
			{
				$medicos = explode('-', $valMedico);
				// foreach($medicos as $medicoCx)
				// {
					$medicoCx = trim($medicos[0]);
					
					if(array_key_exists($medicoCx, $arrayMed))
					{
						$CodEspe = $arrayMed[$medicoCx]['CodEspe'];
						if(!isset($arrayCxEsp[$CodEspe]))
						{
							$arrayCxEsp[$CodEspe]['Can'] = 0;
							$arrayCxEsp[$CodEspe]['Nom'] = $arrayMed[$medicoCx]['NomEspe'];
						}
						
						$arrayCxEsp[$CodEspe]['Can']++;							
					}
					else
					{
						$CodEspe = "Otras";
						if(!isset($arrayCxEsp[$CodEspe]))
						{
							$arrayCxEsp[$CodEspe]['Can'] = 0;
							$arrayCxEsp[$CodEspe]['Nom'] = $CodEspe; 
						}
						
						$arrayCxEsp[$CodEspe]['Can']++;	
					}	
						
				// }
			}
			
			$arrDia		= array(1 => 'LUN', 2 => 'MAR', 3 => 'MIE', 4 => 'JUE', 5 => 'VIE', 6 => 'SAB', 7 => 'DOM');
			$date 		= new DateTime($fechaBuscar1);
			$tipoDia 	= $arrDia[$date->format('N')];
			
			// --> Si es festivo
			$sqlFes = "
			SELECT count(*) AS C
			  FROM root_000063
			 WHERE Fecha = '".$fechaBuscar1."'
			";
			$resFes = mysql_query($sqlFes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlFes):</b><br>".mysql_error());
			if($rowFes = mysql_fetch_array($resFes))
				$tipoDia = (($rowFes['C'] > 0) ? 'FES' : $tipoDia);
			
			// --> Obtener las horas ofertadas
			$horasOfer = 0;
			$sqlHorOfer = "
			SELECT Ofecan*Ofehor AS H
			  FROM ".$tcx."_000022
			 WHERE Ofeest = 'on'	
			   AND (Ofedia = 'NOC' OR Ofedia = '".$tipoDia."')			 
			";
			$resHorOfer = mysql_query($sqlHorOfer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlHorOfer):</b><br>".mysql_error());
			while($rowHorOfer = mysql_fetch_array($resHorOfer))
				$horasOfer+= $rowHorOfer['H'];
			
			$respuesta['Html'].= "
			<table width='100%'>
				<tr>
					<td width='55%' align='center' valign='top'>
						<table class='bordeTabla' style='border-collapse:collapse;' width='90%'>
							<tr class='encabezadoTabla'>
								<td align='center'><b>Quir&oacute;fano</b></td>
								<td align='center'><b>Horas ".(($fechaBuscar1 == date("Y-m-d")) ? "Programadas" : "Realizadas")."</b></td>							
								<td align='center'><b>Horas Canceladas</b></td>							
								<td align='center'><b>Horas Adicionadas - Urgentes</b></td>							
							</tr>
						";
						$totalGr = 0;
						$totalGc = 0;
						foreach($arrayQx as $cco => $det)
						{
							$cf 	= 'fila1';
							$totalR = 0;
							$totalC = 0;
							$totalA = 0;
							
							$respuesta['Html'].= "
							<tr class='fondoAmarillo'><td colspan='4'><b>&nbsp;".$det['NomCco']."</b></td></tr>";
							
							foreach($det['Detalle'] as $quirofano => $detQui)
							{
								$cf 	= ($cf == 'fila2') ? 'fila1' : 'fila2';
								$horRea = floor(($detQui['Rea']/60)/60).":".(($detQui['Rea']/60)%60);
								$horCan = floor(($detQui['Can']/60)/60).":".(($detQui['Can']/60)%60);
								$horAdi = floor(($detQui['Adi']/60)/60).":".(($detQui['Adi']/60)%60);
								$respuesta['Html'].= "
								<tr>
									<td class='".$cf."'>".$detQui['NomQui']."</td>
									<td class='".$cf."' align='center'>".$horRea."</td>
									<td class='".$cf."' align='center'>".$horCan."</td>
									<td class='".$cf."' align='center'>".$horAdi."</td>
								</tr>";
								$totalR+=$detQui['Rea'];
								$totalC+=$detQui['Can'];
								$totalA+=$detQui['Adi'];
							}
							
							$vtotalR = floor(($totalR/60)/60).":".(($totalR/60)%60);
							$vtotalC = floor(($totalC/60)/60).":".(($totalC/60)%60);
							$vtotalA = floor(($totalA/60)/60).":".(($totalA/60)%60);
							
							$respuesta['Html'].= "
							<tr class='filaB' style='font-weight:bold'>
								<td align='right'>TOTAL&nbsp;</td>
								<td align='center'>".$vtotalR."</td>
								<td align='center'>".$vtotalC."</td>
								<td align='center'>".$vtotalA."</td>
							</tr>";
							$totalGr += $totalR;
							$totalGc += $totalC;
							$totalGa += $totalA;
						}
						
						$vtotalGr = floor(($totalGr/60)/60).":".(($totalGr/60)%60);
						$vtotalGc = floor(($totalGc/60)/60).":".(($totalGc/60)%60);
						$vtotalGa = floor(($totalGa/60)/60).":".(($totalGa/60)%60);
						
						$totalHor = floor((($totalGa+$totalGr)/60)/60).":".((($totalGa+$totalGr)/60)%60);				
						
							
						$respuesta['Html'].= "
							<tr class='encabezadoTabla' style='font-weight:bold'>
								<td align='right'>TOTAL GENERAL&nbsp;</td>
								<td align='center'>".$vtotalGr."</td>
								<td align='center'>".$vtotalGc."</td>
								<td align='center'>".$vtotalGa."</td>
							</tr>
							<tr class='filaB' style='font-weight:bold'>
								<td align='right'>NUMERO DE CX&nbsp;</td>
								<td align='center'>".$totalCx['Rea']."</td>
								<td align='center'>".$totalCx['Can']."</td>
								<td align='center'>".$totalCx['Adi']."</td>
							</tr>
						</table>
					</td>
					<td width='45%' align='center' valign='top'>
						<table class='bordeTabla' style='border-collapse:collapse;' width='90%'>
							<tr>
								<td class='encabezadoTabla' colspan='2' align='center'>Resumen</td>
							</tr>
							<tr>
								<td class='fila1' align='' width='50%'>Total Horas ".(($fechaBuscar1 == date("Y-m-d")) ? "Programadas" : "Realizadas").":</td>
								<td class='fila2' align='center'>".$totalHor."</td>
							</tr>
							<tr>
								<td class='fila1' align=''>Horas Ofertadas:</td>
								<td class='fila2' align='center'>".$horasOfer."</td>
							</tr>
							<tr>
								<td class='fila1' align=''>Ocupaci&oacute;n:</td>
								<td class='fila2' align='center'>".number_format(($totalHor/$horasOfer)*100, 1)." %</td>
							</tr>
						</table>
						<br>
						<table class='bordeTabla' style='border-collapse:collapse;' width='90%'>
							<tr class='encabezadoTabla' align='center'><td colspan='2'>Programaci&oacute;n de Cx por especialidad</td></tr>
							<tr class='fondoAmarillo' style='font-weight:bold'><td align='center'>&nbsp;&nbsp;Especialidad</td><td align='center'>Cantidad cx</td></tr>
						";
						$total 	= 0;
						$cf    	= 'fila1';
						$arrOrden = array();
						foreach($arrayCxEsp as $codEsp => $datos)
							$arrOrden[$codEsp] = $datos['Can'];
						
						arsort($arrOrden);
							
						foreach($arrOrden as $codEsp => $val)
						{
							$datos = $arrayCxEsp[$codEsp];	
							$cf    = ($cf == 'fila2') ? 'fila1' : 'fila2';
							$total+= $datos['Can'];
							
							$respuesta['Html'].= "
							<tr class='".$cf."'><td>".$datos['Nom']."</td><td align='center'>".$datos['Can']."</td></tr>";							
						}						
						$respuesta['Html'].= "
							<tr class='filaB' style='font-weight:bold'><td align='right'>TOTAL&nbsp;</td><td align='center'>".$total."</td></tr>
						</table>
					</td>
				</tr>
			</table>
			";	
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'verEspecialidades':
		{
			$cliame				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
			$arrayEspe			= array();
			$arrayMedi			= array();
			
			// --> Obtener array de medicos
			$sqlMedicos = "
			SELECT Meddoc, CONCAT(Medno1, ' ', Medno2, ' ', Medap1, ' ', Medap2) AS Nombre
			  FROM ".$wbasedato."_000048
			 WHERE Medest = 'on'
			";
			$resMedicos = mysql_query($sqlMedicos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMedicos):</b><br>".mysql_error());
			while($rowmedicos = mysql_fetch_array($resMedicos))
				$arrayMedi[$rowmedicos['Meddoc']] = utf8_encode($rowmedicos['Nombre']);
			
			$sqlEspecialidades 	= "
			SELECT Methis, Meting, Metesp, Espnom, Metdoc, Ubisac, Ubihac, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) AS NomPac, Pacdoc, Pactdo, Cconoc
			  FROM ".$wbasedato."_000047 AS A LEFT JOIN ".$wbasedato."_000044 AS B ON (A.Metesp = B.Espcod)
				   INNER JOIN ".$wbasedato."_000018 AS C ON(Methis = Ubihis AND Meting = Ubiing)
				   INNER JOIN ".$cliame."_000100    AS E ON (Ubihis = Pachis) 
				   INNER JOIN ".$wbasedato."_000011 AS D ON(Ubisac = Ccocod)
			 WHERE Metfek = '".$fechaBuscar1."'
			   AND Metest = 'on'
			 ORDER BY Metesp
			";
			$resEspecialidades = mysql_query($sqlEspecialidades, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEspecialidades):</b><br>".mysql_error());
			$x 			= 0;
			$total		= 0;
			$cantPac 	= array();
			while($rowEspecialidades = mysql_fetch_array($resEspecialidades, MYSQL_ASSOC))
			{
				$rowEspecialidades['Metesp'] = ($rowEspecialidades['Metesp'] == '') ? 'NA' : trim($rowEspecialidades['Metesp']); 
				if(isset($arrayEspe[$rowEspecialidades['Metesp']]))
				{
					$arrayEspe[$rowEspecialidades['Metesp']]['Cantidad']++;					
				}
				else
				{
					$arrayEspe[$rowEspecialidades['Metesp']]['Nombre']		= ($rowEspecialidades['Espnom'] == '') ? 'Sin especialidad' : $rowEspecialidades['Espnom'];
					$arrayEspe[$rowEspecialidades['Metesp']]['Cantidad']	= 1;
				}
				
				$arrayEspe[$rowEspecialidades['Metesp']]['Pacientes'][$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']]['Cco'] 	= $rowEspecialidades['Ubisac'];
				$arrayEspe[$rowEspecialidades['Metesp']]['Pacientes'][$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']]['Cconoc'] 	= $rowEspecialidades['Cconoc'];
				$arrayEspe[$rowEspecialidades['Metesp']]['Pacientes'][$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']]['Medico'] 	= $rowEspecialidades['Metdoc'];
				$arrayEspe[$rowEspecialidades['Metesp']]['Pacientes'][$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']]['NomPac'] 	= $rowEspecialidades['NomPac'];
				$arrayEspe[$rowEspecialidades['Metesp']]['Pacientes'][$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']]['Ubihac'] 	= $rowEspecialidades['Ubihac'];
				$arrayEspe[$rowEspecialidades['Metesp']]['Pacientes'][$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']]['Pacdoc'] 	= $rowEspecialidades['Pacdoc'];
				$arrayEspe[$rowEspecialidades['Metesp']]['Pacientes'][$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']]['Pactdo'] 	= $rowEspecialidades['Pactdo'];
				
				$total++;
				
				if(!isset($cantPac[$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']]))
					$cantPac[$rowEspecialidades['Methis']."-".$rowEspecialidades['Meting']] = '';
			}
			
			//$respuesta['Html'].= "<pre>".print_r($arrayEspe, true)."</pre>";
			$respuesta['Html'].= "
			<table width='95%' style='padding:3px;color:#000000;border-collapse:collapse;font-size:10pt;font-family:verdana'>
				<tr style='font-weight:bold'>
					<td width='5%'></td>
					<td class='bordeTabla encabezadoTabla' align='center' colspan='2'>Nombre</td>
					<td class='bordeTabla encabezadoTabla' align='center' colspan='2'>N° Pacientes</td>
					<td class='bordeTabla encabezadoTabla' align='center' colspan='2'>%</td>
				</tr>";
			
			// --> Ordenar array
			$arrayOrden = array();
			foreach ($arrayEspe as $key => $info)
				$arrayOrden[$key] = ($info['Cantidad']/$total)*100;
			
			arsort($arrayOrden);
			
			$totalPac = 0;
			
			// --> Recorrer y pintar info
			foreach ($arrayOrden as $key => $val)
			{
				$info = $arrayEspe[$key];
				$totalPac+= count($info['Pacientes']);
				
				$respuesta['Html'].= "
				<tr class='fila1' style='background-color:#c9eeff'>
					<td width='5%' class='bordeTabla' align='center'>
						<img style='cursor:pointer' onclick='mostrarOcultar(\"".$key."\", this);' src='../../images/medical/root/mas.png' width='15px' height='15px'>
					</td>
					<td  class='bordeTabla' align='center' colspan='2'>
						<b>".$info['Nombre']."</b>
					</td>
					<td class='bordeTabla' align='center' colspan='2'>
						<b>".count($info['Pacientes'])."</b>
					</td>
					<td  class='bordeTabla' align='center' style='font-size:10pt' colspan='2'>
						<b>".number_format((($info['Cantidad']/$total)*100), 1)." %</b>
					</td>
				</tr>
				<tr clase='".$key."' style='font-weight:bold;display:none'>
					<td width='5%' style='border:0px;'></td>
					<td class='fondoAmarillo bordeTabla' align='center'>
						Historia
					</td>
					<td class='fondoAmarillo bordeTabla' align='center'>
						Paciente
					</td>
					<td class='fondoAmarillo bordeTabla' align='center'>
						Ubicaci&oacute;n
					</td>
					<td class='fondoAmarillo bordeTabla' align='center'>
						Habitaci&oacute;n
					</td>
					<td class='fondoAmarillo bordeTabla' align='center'>
						Medico
					</td>
					<td class='fondoAmarillo bordeTabla' align='center'>
						Hce
					</td>
				</tr>
				";
				
				$cf = 'fila1';
				foreach($info['Pacientes'] as $hisIng => $datos)
				{
					$cf = ($cf == 'fila1') ? 'fila2' : 'fila1';
					$hi = explode('-', $hisIng);
					
					$respuesta['Html'].= "
					<tr clase='".$key."' style='display:none'>
						<td width='5%'></td>
						<td class='".$cf." bordeTabla' align='center'>
							".$hisIng."
						</td>
						<td class='".$cf." bordeTabla' align='center'>
							".$datos['NomPac']."
						</td>
						<td class='".$cf." bordeTabla' align='center' nowrap>
							".$datos['Cconoc']."
						</td>
						<td class='".$cf." bordeTabla' align='center'>
							".$datos['Ubihac']."
						<td class='".$cf." bordeTabla' align='center' nowrap>
							".$arrayMedi[$datos['Medico']]."
						</td>
						<td class='".$cf." bordeTabla' align='center'>
							<span onClick='abrirHce(\"".$datos['Pacdoc']."\", \"".$datos['Pactdo']."\", \"".$hi[0]."\", \"".$hi[1]."\")'><img src='../../images/medical/sgc/verHce.png' width='17px' height='17px' style='cursor:pointer;'></span>
						</td>
					</tr>
					";
				}
			}
			
			$respuesta['Html'].= "
				<tr style='font-weight:bold'><td colspan='3' align='right'>TOTAL:</td><td align='center'>".$totalPac."</td><td></td></tr>
				<tr style='font-weight:bold'><td colspan='3' align='right'>NUMERO DE PACIENTES:</td><td align='center'>".count($cantPac)."</td><td></td></tr>
			</table>
			";
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'verDiagnosticos':
		{
			$arrayDiagnosticos	= array();
			$arrayCategorias	= array();
			$arrayEstadistica	= array();
			$formulariosHce		= array();			
			$wbasedatoHce 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
			$wbasedatoCliame 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
			
			// --> Consultar el maestro de diagnosticos
			$sqlMaesDiag = "
			SELECT Codigo, Descripcion, Cod_cat, Categoria
			  FROM root_000011
			 WHERE Estado = 'on'
			"; 
			$resMaesDiag = mysql_query($sqlMaesDiag, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMaesDiag):</b><br>".mysql_error());
			while($rowMaesDiag = mysql_fetch_array($resMaesDiag))
			{
				$arrayDiagnosticos[$rowMaesDiag['Codigo']]['Nombre']		= $rowMaesDiag['Descripcion'];
				$arrayDiagnosticos[$rowMaesDiag['Codigo']]['Categoria']		= $rowMaesDiag['Cod_cat'];
				$arrayDiagnosticos[$rowMaesDiag['Codigo']]['NomCategoria']	= $rowMaesDiag['Categoria'];
			}
			
			// --> Consultar los formularios para obtener el diagnostico
			$sqlForm = "
			SELECT Coefor, Coecon
			  FROM ".$wbasedatoCliame."_000184
			 WHERE Coetip = 'D'
			   AND Coeest = 'on'
			"; 
			$resForm = mysql_query($sqlForm, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlForm):</b><br>".mysql_error());
			while($rowForm = mysql_fetch_array($resForm))
				$formulariosHce[$rowForm['Coefor']] = $rowForm['Coecon'];
			
			$arrayPacientes	= array();
			// $sqlPacientes = "
			// SELECT Ubihis, Ubiing, Ubisac, Cconoc
			  // FROM ".$wbasedato."_000018 AS A INNER JOIN ".$wbasedato."_000011 AS B ON(Ubisac = Ccocod AND Ccohos = 'on')
			 // WHERE Ubifad = '".$fechaBuscar1."' 
			   // AND Ubiald = 'on'
			 // UNION
			// SELECT Ubihis, Ubiing, Ubisac, Cconoc
			  // FROM ".$wbasedato."_000018 AS A INNER JOIN ".$wbasedato."_000011 AS B ON(Ubisac = Ccocod AND Ccohos = 'on')
			 // WHERE A.Fecha_data >= '".$fechaBuscar1."'
			   // AND Ubiald != 'on'			   
			// ";
			$sqlPacientes = "
			SELECT Ubihis, Ubiing, Ubisac, Ubihac, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) AS NomPac, Pacdoc, Pactdo, Cconoc 
			 FROM ".$wbasedato."_000047 AS A INNER JOIN ".$wbasedato."_000018 AS B ON(Methis = Ubihis AND Meting = Ubiing)
				   INNER JOIN ".$wbasedatoCliame."_000100 AS E ON (Ubihis = Pachis) 
				   INNER JOIN ".$wbasedato."_000011 AS D ON(Ubisac = Ccocod)
			 WHERE Metfek = '".$fechaBuscar1."'
			   AND Metest = 'on'			 
			";
			
			$resPacientes 	= mysql_query($sqlPacientes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPacientes):</b><br>".mysql_error());
			
			while($rowPacientes = mysql_fetch_array($resPacientes, MYSQL_ASSOC))
			{
				$indice 							= $rowPacientes['Ubihis']."-".$rowPacientes['Ubiing']; 
				$arrayPacientes[$indice]['His'] 	= $rowPacientes['Ubihis'];	
				$arrayPacientes[$indice]['Ing'] 	= $rowPacientes['Ubiing'];		
				$arrayPacientes[$indice]['Ubisac'] 	= $rowPacientes['Ubisac'];		
				$arrayPacientes[$indice]['Ubihac'] 	= $rowPacientes['Ubihac'];		
				$arrayPacientes[$indice]['Cconoc'] 	= $rowPacientes['Cconoc'];		
				$arrayPacientes[$indice]['NomPac'] 	= $rowPacientes['NomPac'];		
				$arrayPacientes[$indice]['Pacdoc'] 	= $rowPacientes['Pacdoc'];		
				$arrayPacientes[$indice]['Pactdo'] 	= $rowPacientes['Pactdo'];		
			}
			
			//$arrayEstadistica['TOTAL'] = 0;
			$sinDiagnos = 0;
			
			// --> Buscarle el ultimo diagnostico al paciente
			foreach($arrayPacientes as $key => &$info)
			{
				$fechaMayor		= "1970-01-01";
				$info["Diag"]	= "";
				$info["conDiag"]= false;
				
				// --> Recorrer cada formulario y consultar en la HCE
				foreach($formulariosHce as $formulario => $campo)
				{					
					$sqlDiag = "
					SELECT movdat, Fecha_data, Hora_data
					  FROM ".$wbasedatoHce."_".$formulario."
					 WHERE movcon = '".$campo."'
					   AND movhis = '".$info['His']."' 
					   AND moving = '".$info['Ing']."'
					   AND movdat != ''
					 ORDER BY Fecha_data, Hora_data DESC
					 LIMIT 1
					";
					$resDiag = mysql_query($sqlDiag, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDiag):</b><br>".mysql_error());
					if($rowDiag = mysql_fetch_array($resDiag, MYSQL_ASSOC))
					{
						// --> Se tomara el dato mas reciente
						if(strtotime($fechaMayor) < strtotime($rowDiag['Fecha_data']." ".$rowDiag['Hora_data']))
						{	
							$fechaMayor 	= $rowDiag['Fecha_data']." ".$rowDiag['Hora_data'];	
							$info["Diag"] 	= $rowDiag['movdat'];
							$info["SQL"] 	= $sqlDiag;
						}						
					}
				}
				
				// --> procesar diagnostico (codigo y descripcion)
				$auxiliarInicial  = explode( "<option" , $info["Diag"]);
				
				foreach( $auxiliarInicial as $diagnosticoI => $datosDiagnosticoI)
				{
					if( trim($datosDiagnosticoI) == "" ){
						continue;
					}
					$auxiliar 	= explode( ">" , $datosDiagnosticoI );
					$auxiliar 	= $auxiliar[1];
					$posEsp  	= strpos( $auxiliar, " ");//--> primer espacio
					$posCie  	= strpos( $auxiliar, "<");//--> inicio de cierre de etiqueta pos descripcion
					$codDiag 	= substr( $auxiliar, 0, $posEsp );
					$desDiag	= substr( $auxiliar, $posEsp, $posCie-$posEsp );
					$auxCodDiag = explode("-", $codDiag);
					$codDiag    = trim($auxCodDiag[ count($auxCodDiag)-1 ]);
					
					// --> Se toma el primer diagnostico valido
					if(trim($codDiag) != "" && array_key_exists($codDiag, $arrayDiagnosticos))
					{
						$categoria 						= $arrayDiagnosticos[$codDiag]['Categoria'];
						$arrayCategorias[$categoria]	= $arrayDiagnosticos[$codDiag]['NomCategoria'];
						// $arrayEstadistica['TOTAL']++;
						
						if(array_key_exists($categoria, $arrayEstadistica))
						{
							$arrayEstadistica[$categoria]['Cant']++;
							if(array_key_exists($codDiag, $arrayEstadistica[$categoria]['Deta']))
								$arrayEstadistica[$categoria]['Deta'][$codDiag]['Cant']++;
							else
								$arrayEstadistica[$categoria]['Deta'][$codDiag]['Cant'] = 1;
						}
						else
						{
							$arrayEstadistica[$categoria]['Cant'] 					= 1;
							$arrayEstadistica[$categoria]['Deta'][$codDiag]['Cant'] = 1;
						}
						
						$arrayEstadistica[$categoria]['Deta'][$codDiag]['Deta'][$info['His']."-".$info['Ing']]['Cco'] 	= $info['Ubisac'];
						$arrayEstadistica[$categoria]['Deta'][$codDiag]['Deta'][$info['His']."-".$info['Ing']]['Cconoc'] = $info['Cconoc'];
						$arrayEstadistica[$categoria]['Deta'][$codDiag]['Deta'][$info['His']."-".$info['Ing']]['NomPac'] = $info['NomPac'];
						$arrayEstadistica[$categoria]['Deta'][$codDiag]['Deta'][$info['His']."-".$info['Ing']]['Pacdoc'] = $info['Pacdoc'];
						$arrayEstadistica[$categoria]['Deta'][$codDiag]['Deta'][$info['His']."-".$info['Ing']]['Pactdo'] = $info['Pactdo'];
						$arrayEstadistica[$categoria]['Deta'][$codDiag]['Deta'][$info['His']."-".$info['Ing']]['Ubihac'] = $info['Ubihac'];
						
						$info["conDiag"] = true;
						break;
					}
				}
				
				$sinDiagnos+= (!$info["conDiag"]) ? 1 : 0;			
			}
						
			arsort($arrayEstadistica);
			//$respuesta['arrayPacientes'] = print_r($arrayPacientes, true);
			
			$respuesta['Html'] = "
			<table width='95%' style='padding:3px;color:#000000;border-collapse:collapse;font-size:10pt;font-family:verdana' class='bordeTabla'>
				<tr class='encabezadoTabla'>
					<td align='center' class='bordeTabla' colspan='7'>
						<b>Diagn&oacute;stico</b>
					</td>
					<td  align='center' class='bordeTabla'>
						<b>Cantidad</b>
					</td>
				</tr>
				";
			if(count($arrayEstadistica) == 0)
			{
				$respuesta['Html'].= "
				<tr class='fila1' style='background-color:#c9eeff'><td colspan='7' align='center' class='bordeTabla'>No se encontraron datos...</td></tr>";
			}
			
			$totalPac = 0;	
			
			foreach($arrayEstadistica as $categ => $info1)
			{				
				$respuesta['Html'].= "
				<tr class='fila1' style='background-color:#c9eeff'>
					<td align='center' class='bordeTabla'>
						<img style='cursor:pointer' onclick='mostrarOcultar(\"".$categ."\", this);' src='../../images/medical/root/mas.png' width='15px' height='15px'>
					</td>
					<td colspan='6' align='center' class='bordeTabla'>
						<b>".$categ."-".utf8_encode($arrayCategorias[$categ])."</b>
					</td>
					<td align='center' class='bordeTabla' style='font-size:10pt'>
						<b>".$info1['Cant']."</b>
					</td>
				</tr>
				";
				$cf = 'fila1';
				foreach($info1['Deta'] as $codDiag => $info2)
				{
					$totalPac+= $info2['Cant'];
					$cf = ($cf == 'fila1') ? 'fila2' : 'fila1';
					
					$respuesta['Html'].= "
					<tr clase='".$categ."' style='display:none'>
						<td align='center' width='5%' style='border:0px'>
						<td align='center' class='bordeTabla ".$cf."'>
							<img onclick='mostrarOcultar(\"".$categ."-".$codDiag."\", this);' src='../../images/medical/root/mas.png' width='15px' height='15px' style='cursor:pointer;'>
						</td>
						</td>
						<td colspan='5' align='center' class='bordeTabla ".$cf."'>
							".utf8_encode($arrayDiagnosticos[$codDiag]['Nombre'])."
						</td>
						<td align='center' class='bordeTabla ".$cf."' style='font-size:10pt'>
							<b>".$info2['Cant']."</b>
						</td>
					</tr>
					";
					
					$respuesta['Html'].= "
					<tr clase='".$categ."-".$codDiag."' style='display:none;font-size:10pt'>
						<td align='center' width='5%' style='border:0px'>
						</td>
						<td align='center' width='5%' style='border:0px'>
						</td>
						<td align='center' class='fondoAmarillo'>
							<b>Historia</b>
						</td>
						<td align='center' class='fondoAmarillo'>
							<b>Paciente</b>
						</td>
						<td align='center' class='fondoAmarillo'>
							<b>Ubicaci&oacute;n</b>
						</td>
						<td align='center' class='fondoAmarillo' >
							<b>Habitaci&oacute;n</b>
						</td>
						<td align='center' class='fondoAmarillo' >
							<b>Medico</b>
						</td>
						<td align='center' class='fondoAmarillo' >
							<b>Hce</b>
						</td>
					</tr>
					";
					
					foreach($info2['Deta'] as $hisIng => $info3)
					{						
						$respuesta['Html'].= "
						<tr clase='".$categ."-".$codDiag."' style='display:none'>
							<td align='center' width='5%' style='border:0px'>
							</td>
							<td align='center' width='5%' style='border:0px'>
							</td>
							<td align='center' class='bordeTabla filaB' style='font-size:7pt'>
								".$hisIng."
							</td>
							<td  align='left' class='bordeTabla filaB' style='font-size:7pt'>
								".$info3['NomPac']."
							</td>
							<td align='left' class='bordeTabla filaB' style='font-size:7pt'>
								".$info3['Cconoc']."
							</td>
							<td align='center' class='bordeTabla filaB' style='font-size:7pt'>
								".$info3['Ubihac']."
							</td>";
							
							// --> Obtener el medico tratante
							$hisIng 		= explode('-', $hisIng);
							$medTratantes 	= "";
							$sqlMed = "
							SELECT Metdoc, CONCAT(Medno1, ' ', Medno2, ' ', Medap1, ' ', Medap2) AS Nom
							  FROM ".$wbasedato."_000047 AS A INNER JOIN ".$wbasedato."_000048 ON(Metdoc = Meddoc AND Mettdo = Medtdo)
							 WHERE Methis = '".trim($hisIng[0])."'
							   AND Meting = '".trim($hisIng[1])."'
							   AND Metfek = '".$fechaBuscar1."'
							   AND Metest = 'on'
							 GROUP BY Metdoc
							 ORDER BY Metfek ASC
							";
							$resMed = mysql_query($sqlMed, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMed):</b><br>".mysql_error());
							while($rowMed = mysql_fetch_array($resMed, MYSQL_ASSOC))
							{
								$medTratantes.= (($medTratantes == "" ) ? "" : "<br>").utf8_encode($rowMed ['Nom']);
							}
							
							$respuesta['Html'].= "
							<td align='left' class='bordeTabla filaB' style='font-size:7pt'>
								".$medTratantes."
							</td>";
							
							$respuesta['Html'].= "							
							<td align='center' class='bordeTabla filaB' style='font-size:7pt'>
								<span onClick='abrirHce(\"".$info3['Pacdoc']."\", \"".$info3['Pactdo']."\", \"".$hisIng[0]."\", \"".$hisIng[1]."\")'><img src='../../images/medical/sgc/verHce.png' width='17px' height='17px' style='cursor:pointer;'></span>
							</td>
						</tr>
						";
					}
				}
			}

			
			// --> Pintar pacientes sin diagnostico
			$respuesta['Html'].= "
				<tr class='fila1' style='background-color:#c9eeff'>
					<td align='center' class='bordeTabla'>
						<img style='cursor:pointer' onclick='mostrarOcultar(\"SIN_DIAG\", this);' src='../../images/medical/root/mas.png' width='15px' height='15px'>
					</td>
					<td colspan='6' align='center' class='bordeTabla'>
						<b>Sin diagnostico</b>
					</td>
					<td align='center' class='bordeTabla' style='font-size:10pt'>
						<b>".$sinDiagnos."</b>
					</td>
				</tr>
				<tr clase='SIN_DIAG' style='display:none;font-size:10pt'>
					<td align='center' width='5%' style='border:0px'>
					</td>
					<td align='center' width='5%' style='border:0px'>
					</td>
					<td align='center' class='fondoAmarillo'>
						<b>Historia</b>
					</td>
					<td align='center' class='fondoAmarillo'>
						<b>Paciente</b>
					</td>
					<td align='center' class='fondoAmarillo'>
						<b>Ubicaci&oacute;n</b>
					</td>
					<td align='center' class='fondoAmarillo' >
						<b>Habitaci&oacute;n</b>
					</td>
					<td align='center' class='fondoAmarillo' >
						<b>Medico</b>
					</td>
					<td align='center' class='fondoAmarillo' >
						<b>Hce</b>
					</td>
				</tr>";
				
				$cf = 'fila1';
				
			foreach($arrayPacientes as $clave2 => $infoPac2)
			{
				if($infoPac2["conDiag"])
					continue;
				
				$respuesta['Html'].= "
				<tr clase='SIN_DIAG' style='display:none'>
					<td align='center' width='5%' style='border:0px'>
					</td>
					<td align='center' width='5%' style='border:0px'>
					</td>
					<td align='center' class='bordeTabla filaB' style='font-size:7pt'>
						".$clave2."
					</td>
					<td  align='left' class='bordeTabla filaB' style='font-size:7pt'>
						".$infoPac2['NomPac']."
					</td>
					<td align='left' class='bordeTabla filaB' style='font-size:7pt'>
						".$infoPac2['Cconoc']."
					</td>
					<td align='center' class='bordeTabla filaB' style='font-size:7pt'>
						".$infoPac2['Ubihac']."
					</td>";
					
					// --> Obtener el medico tratante
					$hisIng 		= explode('-', $clave2);
					$medTratantes 	= "";
					$sqlMed = "
					SELECT Metdoc, CONCAT(Medno1, ' ', Medno2, ' ', Medap1, ' ', Medap2) AS Nom
					  FROM ".$wbasedato."_000047 AS A INNER JOIN ".$wbasedato."_000048 ON(Metdoc = Meddoc AND Mettdo = Medtdo)
					 WHERE Methis = '".trim($hisIng[0])."'
					   AND Meting = '".trim($hisIng[1])."'
					   AND Metfek = '".$fechaBuscar1."'
					   AND Metest = 'on'
					 GROUP BY Metdoc
					 ORDER BY Metfek ASC
					";
					$resMed = mysql_query($sqlMed, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMed):</b><br>".mysql_error());
					while($rowMed = mysql_fetch_array($resMed, MYSQL_ASSOC))
					{
						$medTratantes.= (($medTratantes == "" ) ? "" : "<br>").utf8_encode($rowMed ['Nom']);
					}
					
					$respuesta['Html'].= "
					<td align='left' class='bordeTabla filaB' style='font-size:7pt'>
						".$medTratantes."
					</td>";
					
					$respuesta['Html'].= "							
					<td align='center' class='bordeTabla filaB' style='font-size:7pt'>
						<span onClick='abrirHce(\"".$infoPac2['Pacdoc']."\", \"".$infoPac2['Pactdo']."\", \"".$hisIng[0]."\", \"".$hisIng[1]."\")'><img src='../../images/medical/sgc/verHce.png' width='17px' height='17px' style='cursor:pointer;'></span>
					</td>
				</tr>
				";
			}
			
			$respuesta['Html'].= "
				<tr style='font-weight:bold'><td colspan='7' align='right'>TOTAL:</td><td align='center'>".($totalPac+$sinDiagnos)."</td></tr>
			</table>
			";
			
			echo json_encode($respuesta);
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
		<title>Indicadores Hospitalarios, Direcci&oacute;n Medica</title>
		
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
	var blinkPor;
	var relojTemp;

    $(document).on('change','#selectsede',function(){
        window.location.href = "indicadoresDireccionMedica.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val();
    });
	
	$(function(){
		// --> Parametrizaci&oacute;n del datapicker
		cargar_elementos_datapicker();
		// --> Activar datapicker
		$("#fechaBuscar1").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){		
				navegar(0);
			}
		});
		// --> Modificarle el z-index al calendar
		$("img[class=ui-datepicker-trigger]").click(function(){
			$("#ui-datepicker-div").css("z-index", "501");
		});
		
		cargarMenuPrincipal();
	});
	
	//--------------------------------------------------------
	//	--> Activar datapicker
	//---------------------------------------------------------
	function cargarMenuPrincipal()
	{
		$("#tableContenedor").attr("accion", "cargarMenuPrincipal");
		
		clearInterval(blinkPor);
		clearInterval(relojTemp);
		
		html = ""
		+"<img id='imgClinica' src='../../images/medical/movhos/c1.jpg' width='100%' height='"+$("#tableContenedor").height()+"' style='border-radius:8px;'>"
		+"<div id='menu' style='display:none;z-index:500;'>"			
			+"<div id='infoOcupacion' align='center' onclick='verOcupacion()' style='cursor:pointer;opacity: 0.7;background-color:#FFFFFF;font-family:verdana;font-size:30pt;border:solid 1px #000000;border-radius:8px;padding:5px;color:#000000'>"
				+"Ocupaci&oacute;n Hospitalaria <br>"
				+"<span style='font-size:35pt;' id='ocupacionGeneral'></span>"
			+"</div>"
			+"<br>"
			+"<div id='infoEspecialidad' align='center' onclick='verEspecialidades()' style='cursor:pointer;opacity: 0.7;background-color:#FFFFFF;font-family:verdana;font-size:30pt;border:solid 1px #000000;border-radius:8px;padding:5px;color:#000000'>"
				+"Especialidades"
			+"</div>"
			+"<br>"
			+"<div id='infoDiagnostico' align='center' onclick='verDiagnosticos()' style='cursor:pointer;opacity: 0.7;background-color:#FFFFFF;font-family:verdana;font-size:30pt;border:solid 1px #000000;border-radius:8px;padding:5px;color:#000000'>"
				+"Diagn&oacute;sticos"
			+"</div>"
		+"</div>";
	
		$("#contenedor").html(html);
		
		var posicion 	= $("#imgClinica").position();	
		var ancho 		= ($("#imgClinica").width()-$("#menu").width())/2;
		var alto 		= ($("#imgClinica").height()-$("#menu").height())/2;
		
		
		$("#menu").css({'left':posicion.left+ancho,'top':posicion.top+alto/4, 'position':'absolute'}).slideDown( 800, function() {});
		
		$("#infoOcupacion").hover(function(){$(this).css("opacity", "0.8");}, function(){$(this).css("opacity", "0.7");});
		$("#infoEspecialidad").hover(function(){$(this).css("opacity", "0.8");}, function(){$(this).css("opacity", "0.7");});
		$("#infoDiagnostico").hover(function(){$(this).css("opacity", "0.8");}, function(){$(this).css("opacity", "0.7");});
	
				
		$.post("indicadoresDireccionMedica.php",
		{
			consultaAjax	:   '',
			accion			:   'ocupacionGeneral',
			wemp_pmla		:	$('#wemp_pmla').val(),
            selectsede      :   $('#sede').val(),
			fechaBuscar1	:	$("#fechaBuscar1").val()
		}, function(respuesta){
			
			$("#ocupacionGeneral").text(respuesta.ocupacion+" %");
			
		}, 'json');
		
		$("#tableContenedor").css({'background-color':'#F2F5F7'});
				
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
	//	--> acordeon para mostrar u ocultar diagnoticos
	//---------------------------------------------------------
	function mostrarOcultar(elemento, img)
	{
		$("[clase="+elemento+"]").toggle();
		img = $(img);
		if(img.attr("src") == "../../images/medical/root/mas.png")
		{
			img.attr("src",  "../../images/medical/root/menos.png");
		}
		else
		{
			img.attr("src",  "../../images/medical/root/mas.png");
			$("[clase^="+elemento+"-]").hide();
			$("[clase="+elemento+"]").find("img").attr("src",  "../../images/medical/root/mas.png");
		}
	}
	//--------------------------------------------------------
	//	--> Ver Ocupaci&oacute;n
	//---------------------------------------------------------
	function verOcupacion()
	{
		$("#selectFecha").show();
		$("#tableContenedor").attr("accion", "verOcupacion");
		
		$("#menu").hide();
		clearInterval(relojTemp);
		clearInterval(blinkPor);
		
		$.post("indicadoresDireccionMedica.php",
		{
			consultaAjax	:   '',
			accion			:   'verOcupacion',
			wemp_pmla		:	$('#wemp_pmla').val(),
            selectsede      :   $('#sede').val(),
			fechaBuscar1	:	$("#fechaBuscar1").val()
		}, function(respuesta){
			
			msjHtml = "Ocupaci&oacute;n Hospitalaria "+respuesta.ocupacion+" %<br><span style='font-size:10pt;font-weight:normal'><table><tr class=encabezadoTabla><td>Camas listas para ocupar</td><td>Camas en alistamiento</td><td>Camas Ocupadas</td><td>Total Camas</td><tr class=fondoAmarillo align=center><td>"+respuesta.totalCamasDispCli+"</td><td>"+respuesta.totalCamasAlisCli+"</td><td>"+respuesta.totalCamasOcupCli+"</td><td>"+respuesta.totalCamasCli+"</td></tr></table></span>";
			
			if($("#imgClinica").attr("id") != undefined)
			{
				$("#imgClinica").hide(500, function(){
					$("#retornar").show();
					$("#contenedor").html(respuesta.Html);
					$("#tituloMenu").html(msjHtml);				
				});
			}
			else
			{
				$("#retornar").show();
				$("#contenedor").html(respuesta.Html);
				$("#tituloMenu").html(msjHtml);				
			}
			
		}, 'json');
	}
	function activarRelojTemporizador(fechaBuscar1)
	{
		clearInterval(relojTemp);
		// --> Incializar contador en dos minutos
		$("#relojTemp").text("00:30");
		$("#relojTemp").attr("cincoMinTem", "86430000");	
		
		// --> Recorre cada segundo
		relojTemp = setInterval(function(){
			var cincoMin 	= new Date(parseInt($("#relojTemp").attr("cincoMinTem")));
			var cincoMinTem	= cincoMin.getTime();
			cincoMinTem -= 1000;
			cincoMin.setTime(cincoMinTem);
			minuto	 	= ((String(cincoMin.getMinutes()).length == 1) ? "0"+cincoMin.getMinutes() : cincoMin.getMinutes());
			segundo 	= ((String(cincoMin.getSeconds()).length == 1) ? "0"+cincoMin.getSeconds() : cincoMin.getSeconds());

			var nuevoCincoMin = minuto+":"+segundo;
			$("#relojTemp").text(nuevoCincoMin);
			$("#relojTemp").attr("cincoMinTem", cincoMinTem);

			// --> Actualizar cuando el reloj quede en 00:00
			if(parseInt($("#relojTemp").attr("cincoMinTem")) == 86400000)
				verUrgencias(fechaBuscar1);
		}, 1000);
	}
	//--------------------------------------------------------
	//	--> Ver Urgencias
	//---------------------------------------------------------
	function verUrgencias(fechaBuscar1)
	{
		clearInterval(blinkPor);
		$("#menu").hide();
		$("#selectFecha").hide();
		
		$.post("indicadoresDireccionMedica.php",
		{
			consultaAjax	:   '',
			accion			:   'verUrgencias',
			wemp_pmla		:	$('#wemp_pmla').val(),
            selectsede      :   $('#sede').val()
		}, function(respuesta){
			$("#retornar").show();
			$("#tituloMenu").html("<table width='100%' style='font-family:verdana;font-size:18pt;color:#109DDC;font-weight:bold'><tr><td width='20%'><img id='atras' title='Retornar' src='../../images/medical/sgc/atras.png' onclick='verOcupacion()' width='25px' height='27px' style='cursor:pointer;'></td><td width='50%' align='center'>Ocupaci&oacute;n Urgencias "+respuesta.ocupacionGen+" %</td><td width='30%' style='font-size:8pt;font-weight:normal;'>Pr&oacute;xima actualizaci&oacute;n:&nbsp;<span id='relojTemp' cincoMinTem='86400000'></span>&nbsp;<img width='15px' height='15px' src='../../images/medical/sgc/Clock-32.png'>&nbsp;<img title='Actualizar' src='../../images/medical/sgc/Refresh-128.png' onclick='verUrgencias(\""+fechaBuscar1+"\")' width='15px' height='15px' style='cursor:pointer;'></td></tr></table>");
						
			$("#contenedor").html(respuesta.Html);
			var posicion 	= $("#contenedor").position();
			
			setTimeout(function(){ 
				$("#datosPlanoUrg").css({'width':$("#planoUrgencias").width(),'height':$("#planoUrgencias").height(),'left':posicion.left,'top':posicion.top, 'position':'absolute'}).show();	
			}, 500);
			 
			$("#tableContenedor").css({'background-color':'#FFFFFF'});
			
			// --> Nombre del medico
			setTimeout(function(){ 
				$('[tooltip=si]').each(function(){
					var posicion = $(this).position();				
					$(this).next().css({'left':posicion.left+11,'top':posicion.top-11, 'position':'absolute'}).show(400);
				});
			}, 600);
			
			// --> Blink
			setTimeout(function(){ 
				blinkPor = setInterval(function(){
					$("[Blink]").css('visibility' , $("[Blink]").css('visibility') === 'hidden' ? '' : 'hidden')
					//$("[desvanecer]").css('opacity' , $("[desvanecer]").css('opacity') == 1 ? 0.6 : 1);
					// .css("opacity", "0.8")
				}, 700);
			}, 700);
			
			// $("[desvanecer]").hover(function(){$(this).css("color", "#2A5DB0");}, function(){$(this).css("color", "red");});
			
			activarRelojTemporizador(fechaBuscar1);
			
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> verPacientesZona
	//---------------------------------------------------------
	function verPacientesZona(elem)
	{
		infoZOna 	= JSON.parse($(elem).attr("infoZOna"));
		colorF		= "fila2";
		
		htmlZona = "<table width='100%'>"
			+"			<tr class='fila1' style='font-weight:bold' align='center'>"
			+"				<td>Habitaciones</td><td>Ocupadas</td><td>Disponibles</td><td>Total</td>"
			+"			</tr>"
			+"			<tr class='fila2' align='center'>"
			+"				<td>Fisicas:</td><td>"+infoZOna['totalHabOcu']+"</td><td>"+((infoZOna['totalHab']*1)-(infoZOna['totalHabOcu']*1))+"</td><td>"+infoZOna['totalHab']+"</td>"
			+"			</tr>"
			+"			<tr class='fila2' align='center'>"
			+"				<td>Virtuales:</td><td>"+infoZOna['totalHabOcuVir']+"</td><td>"+((infoZOna['totalHabVir']*1)-(infoZOna['totalHabOcuVir']*1))+"</td><td>"+infoZOna['totalHabVir']+"</td>"
			+"			</tr>"
			+"			<tr class='fila2' align='center' style='font-weight:bold'>"
			+"				<td>TOTAL:</td><td>"+(infoZOna['totalHabOcuVir']+infoZOna['totalHabOcu'])+"</td><td>"+(((infoZOna['totalHabVir']*1)-(infoZOna['totalHabOcuVir']*1))+((infoZOna['totalHab']*1)-(infoZOna['totalHabOcu']*1)))+"</td><td>"+(infoZOna['totalHabVir']+infoZOna['totalHab'])+"</td>"
			+"			</tr>"
			+"		</table><br>";
			
		htmlZona+= "<table width='100%'><tr align='center'class='encabezadoTabla'><td>Turno</td><td>Habitaci&oacute;n</td><td>Historia</td><td>Paciente</td><td>Hce</td></tr>";
		
		jQuery.each( infoZOna['Habitaciones'], function( i, val ){
			colorF		= (colorF == "fila2") ? "fila1" : "fila2";
			htmlZona 	= htmlZona+"<tr class='"+colorF+"'><td>"+val['Turno']+"</td><td>"+val['Nombre']+"</td><td>"+((val['Historia'] != '-') ? val['Historia'] : "")+"</td><td>"+val['nomPac']+"</td>";
			
			hisIng 		= val['Historia'].split("-");
			historia 	= hisIng[0];
			ingreso 	= hisIng[1];
			
			if(historia != '')
				htmlZona 	= htmlZona+"<td align='center'><span onClick='abrirHce(\""+val['Documento']+"\", \""+val['tipoDoc']+"\", \""+historia+"\", \""+ingreso+"\")'><img src='../../images/medical/sgc/verHce.png' width='17px' height='17px' style='cursor:pointer;'></span></td></tr>";
			else
				htmlZona 	= htmlZona+"<td></td></tr>";
		});
		
		htmlZona+= "</table>";
		
		$("#divZonaPac").html(htmlZona).dialog({
			title: "<div align='left' style='font-weight:normal'>Pacientes en: <span style='color:#2A5DB0;font-weight:bold'>"+infoZOna['nomZona']+"</span></div>",
			width: 600,
			modal: true,
			close: function( event, ui ) {
			}
		});
	}
	//--------------------------------------------------------
	//	--> ver Pacientes en alta
	//---------------------------------------------------------
	function verPacientesAlta(elem)
	{
		if($(elem).attr("infoDet") == 'null')
			return;
		
		infoZOna 	= JSON.parse($(elem).attr("infoDet"));
		colorF		= "fila2";
		htmlZona 	= "<br><table width='100%'><tr align='center'class='encabezadoTabla'><td>Historia</td><td>Paciente</td><td>Alta en proceso</td></tr>";
		
		jQuery.each( infoZOna, function( i, val ){
			colorF		= (colorF == "fila2") ? "fila1" : "fila2";
					
			htmlZona 	= htmlZona+"<tr class='"+colorF+"'><td>"+val['Ubihis']+"-"+val['Ubiing']+"</td><td>"+val['nomPac']+"</td><td align='center'>"+val['Ubifap']+" "+val['Ubihap']+"</td></tr>";
		});
		
		htmlZona+= "</table>";
		
		$("#divZonaPac").html(htmlZona).dialog({
			title: "<div align='left' style='font-weight:normal'><span style='color:#2A5DB0;font-weight:bold'>Pacientes en alta</span></div>",
			width: 600,
			modal: true,
			close: function( event, ui ) {
			}
		});
	}
	//--------------------------------------------------------
	//	--> verPacientesEspera
	//---------------------------------------------------------
	function verPacientesEspera(elem, titulo)
	{
		if($(elem).attr("infoDet") == 'null')
			return;
		
		infoZOna 	= JSON.parse($(elem).attr("infoDet"));
		colorF		= "fila2";
		htmlZona 	= "<br><table width='100%'><tr align='center'class='encabezadoTabla'><td>Turno</td><td>Paciente</td><td>Edad</td><td>En Espera</td><td>Nota</td></tr>";
		
		jQuery.each( infoZOna, function( i, val ){
			colorF		= (colorF == "fila2") ? "fila1" : "fila2";
			switch(val['Atuted'])
			{
				case 'A':
					tipoE = 'A&ntilde;os';
					break;
				case 'M':
					tipoE = 'Meses';
					break;
				case 'D':
					tipoE = 'Dias';
					break;
			}
			
			htmlZona 	= htmlZona+"<tr class='"+colorF+"'><td>"+val['Atutur']+"</td><td>"+val['Atunom']+"</td><td>"+val['Atueda']+" "+tipoE+"</td><td align=center>"+val['tiempoEspera']+"</td><td align=center>"+val['obsAdm']+"</td></tr>";
		});
		
		htmlZona+= "</table>";
		
		$("#divZonaPac").html(htmlZona).dialog({
			title: "<div align='left' style='font-weight:normal'><span style='color:#2A5DB0;font-weight:bold'>"+titulo+"</span></div>",
			width: 600,
			modal: true,
			close: function( event, ui ) {
			}
		});
	}
	//-------------------------------------------------------------------------
	//	Abrir la historia clinica
	//-------------------------------------------------------------------------
	function abrirHce(documento, tipoDoc, historia, ingreso)
	{
		var url 	= "/matrix/HCE/procesos/HCE_Impresion.php?empresa=hce&origen="+$("#wemp_pmla").val()+"&wcedula="+documento+"&wtipodoc="+tipoDoc+"&wdbmhos=movhos&whis="+historia+"&wing="+ingreso+"&wservicio=*&protocolos=0&CLASE=I&BC=1";
		alto		= screen.availHeight;
		ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
		ventana.document.open();
		ventana.document.write("<span><input type='button' value='Cerrar Ventana' onclick='window.close()'><br><b>CONSULTA DESDE INDICADORES DIRECCION MEDICA<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe><input type='button' value='Cerrar Ventana' onclick='window.close()'><br><br>");
	}
	//--------------------------------------------------------
	//	--> Ver Cx
	//---------------------------------------------------------
	function verCx()
	{
		$("#tableContenedor").attr("accion", "verCx");
		$("#menu").hide();
		
		$.post("indicadoresDireccionMedica.php",
		{
			consultaAjax	:   '',
			accion			:   'verCx',
			wemp_pmla		:	$('#wemp_pmla').val(),
            selectsede      :   $('#sede').val(),
			fechaBuscar1	:	$("#fechaBuscar1").val()
		}, function(respuesta){
			$("#retornar").show();
			$("#tituloMenu").html("<table width='100%' style='font-family:verdana;font-size:18pt;color:#109DDC;font-weight:bold'><tr><td width='25%'><img id='atras' src='../../images/medical/sgc/atras.png' onclick='verOcupacion()' width='25px' height='25px' style='cursor:pointer;'></td><td width='50%' align='center'>Ocupaci&oacute;n Cx</td><td width='25%'></td></tr></table>");
			
			$("#contenedor").html(respuesta.Html);
			
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Ver especialidades
	//---------------------------------------------------------
	function verEspecialidades()
	{
		$("#tableContenedor").attr("accion", "verEspecialidades");
		
		$("#menu").hide();
		
		$.post("indicadoresDireccionMedica.php",
		{
			consultaAjax	:   '',
			accion			:   'verEspecialidades',
			wemp_pmla		:	$('#wemp_pmla').val(),
            selectsede      :   $('#sede').val(),
			fechaBuscar1	:	$("#fechaBuscar1").val()
		}, function(respuesta){
			
			if($("#imgClinica").attr("id") != undefined)
			{
				$("#imgClinica").hide(500, function(){
					$("#retornar").show();
					$("#contenedor").html(respuesta.Html);
					$("#tituloMenu").html("Especialidades");				
				});	
			}
			else
			{
				$("#retornar").show();
				$("#contenedor").html(respuesta.Html);
				$("#tituloMenu").html("Especialidades");
			}
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Ver diagnosticos
	//---------------------------------------------------------
	function verDiagnosticos()
	{
		$("#tableContenedor").attr("accion", "verDiagnosticos");
		
		$("#menu").hide();
		
		$.post("indicadoresDireccionMedica.php",
		{
			consultaAjax	:   '',
			accion			:   'verDiagnosticos',
			wemp_pmla		:	$('#wemp_pmla').val(),
            selectsede      :   $('#sede').val(),
			fechaBuscar1	:	$("#fechaBuscar1").val()
		}, function(respuesta){
			if($("#imgClinica").attr("id") != undefined)
			{
				$("#imgClinica").hide(500, function(){
					$("#retornar").show();
					$("#contenedor").html(respuesta.Html);
					$("#tituloMenu").html("Diagnosticos");				
				});	
			}
			else
			{
				$("#retornar").show();
				$("#contenedor").html(respuesta.Html);
				$("#tituloMenu").html("Diagnosticos");
			}
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Ver el menu principal
	//---------------------------------------------------------
	function retornarMenu()
	{
		$("#selectFecha").show();
		$("#retornar").hide();
		cargarMenuPrincipal();
		$("#tituloMenu").html("");
	}
	//--------------------------------------------------------
	//	--> Adelantar o atrasar una fecha
	//---------------------------------------------------------
	function navegar(dias)
	{	
		var f = new Date($("#fechaBuscar1").val());
		f.setDate(f.getDate() + 1);
		f.setDate(f.getDate() + dias);
		fecha = f.getFullYear()+"-"+(((f.getMonth()+1) >= 10) ? (f.getMonth()+1) : "0"+(f.getMonth()+1))+"-"+((f.getDate() < 10) ? "0"+(f.getDate()) : f.getDate());
		$("#fechaBuscar1").val(fecha);
		$("#tituloMenu").html("");
		
		switch($("#tableContenedor").attr("accion"))
		{
			case 'cargarMenuPrincipal':
			{
				cargarMenuPrincipal();
				break;
			}
			case 'verOcupacion':
			{
				verOcupacion();
				break;
			}
			case 'verEspecialidades':
			{
				verEspecialidades();
				break;
			}
			case 'verDiagnosticos':
			{
				verDiagnosticos();
				break;
			}
			case 'verCx':
			{
				verCx();
				break;
			}
		}
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
		.filaB
		{
			background-color: 	#FFFFFF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		.bordeRedondo{
			border-radius: 	4px;
			border:			1px solid #AFAFAF;
		}
		.bordeTabla td{
			border: 			1px solid black;
		}
		.bordeTabla{
			border: 			1px solid black;
		}
		.bordeLetra{
			text-shadow: 2px 0 0 #fff, -2px 0 0 #fff, 0 2px 0 #fff, 0 -2px 0 #fff, 1px 1px #fff, -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff;
		}
		#tooltip{font-family: verdana;font-weight:normal;color: #ffffff;font-size: 7pt;position:absolute;z-index:5000;border:1px solid #000000;background-color:#000000;padding:3px;opacity:1;border-radius: 4px;}
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
	// -->	ENCABEZADO
	encabezado("Indicadores Direcci&oacute;n Medica", $wactualiz, 'clinica', true);
	//<table id='tableContenedor' width='70%' height='50%' class='bordeRedondo' style='border-radius:8px;border:1px solid #AFAFAF;padding:10px;z-index:-2;background:#eeeeee url(../../../include/root/jqueryui_1_9_2/cupertino/images/ui-bg_diagonals-thick_90_eeeeee_40x40.png) 50% 50% repeat;'>
		
	echo "
	<input type='hidden' value='".$wemp_pmla."' id='wemp_pmla'>
	<input type='hidden' value='".$selectsede."' id='sede'>
	<div align='center' >
		<table  width='70%'>
			<tr>
				<td align='center' id='selectFecha'>
					<img id='atras' src='../../images/medical/sgc/atras.png' onclick='navegar(-1)' width='25px' height='25px' style='cursor:pointer;'>&nbsp;&nbsp;
					<input id='fechaBuscar1' style='border-radius:4px;border:1px solid #AFAFAF;' size='11' type='text' value='".date("Y-m-d")."'>&nbsp;&nbsp;
					<img id='adelante' src='../../images/medical/sgc/adelante.png' onclick='navegar(1)' width='25px' height='25px' style='cursor:pointer;'>
				</td>
			</tr>
		</table>
		<br>
		<table id='tableContenedor' accion='cargarMenuPrincipal' width='70%' height='58%' class='bordeRedondo' style='border-radius:8px;border:1px solid #c9c9c9;padding:10px;z-index:-2;background:#F2F5F7'>
			<tr>
				<td width='5%'>
					<img id='retornar' src='../../images/medical/root/casa.PNG' onClick='retornarMenu();'  style='cursor:pointer;display:none'>
				</td>
				<td id='tituloMenu' align='center' style='font-family:verdana;font-size:18pt;color:#109DDC;font-weight:bold'>
				</td>
			</tr>
			<tr>	
				<td id='contenedor' align='center' height='100%' colspan='2'>		
				</td>
			</tr>
		</table>
		<br>
		<input type='button' value='Cerrar Ventana' onclick='window.close()'>
	</div>
	<div id='divZonaPac' style='display:none'></div>
	<br>
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
