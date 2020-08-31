<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	2016-06-09
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2019-06-26';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2019-06-26 Jerson Trujillo: Se adapta el programa para que soporte dos campos del formulario de triage de la hce, para definir si se da de alta al paciente.              
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
	

	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wfecha			= date("Y-m-d");   
    $whora 			= date("H:i:s");

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//---------------------------------------------------------
	// --> Pintar lista de pacientes pendientes de triage
	//---------------------------------------------------------
	function listaDePacientesEnEspera($filtroSalaDeEspera="%")
	{
		global $wbasedato;
		global $wemp_pmla;
		global $conex;
		global $wuse;
		
		$respuesta 						= array("html" => "", "cantidad" => 0);
		$wbasedatoCliame 				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
		$turnoConLlamadoEnVentanilla 	= "";
		
		// --> Obtener maestro de salas de espera
		$arraySalasEspera = array();
		$sqlSalaEsp = "
		SELECT Salcod, Salnom
		  FROM ".$wbasedato."_000182
		 WHERE Salest = 'on'
		";
		$resSalaEsp = mysql_query($sqlSalaEsp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalaEsp):</b><br>".mysql_error());
		while($rowSalaEsp = mysql_fetch_array($resSalaEsp))
			$arraySalasEspera[$rowSalaEsp['Salcod']] = $rowSalaEsp['Salnom'];
		
		$respuesta['html'] = "
		<div style=''>
		<table id='tablaListaEsperaTriage' style='width:100%' align='center'>
			<tr align='center'>
				<td class='encabezadoTabla'>Fecha</td>
				<td class='encabezadoTabla'>Hora</td>
				<td class='encabezadoTabla'>En espera<br><span style='font-family: verdana;font-weight:normal;font-size: 7pt;'>H:m:s</span></td>
				<td class='encabezadoTabla'>Turno</td>
				<td class='encabezadoTabla'>Documento</td>
				<td class='encabezadoTabla'>Nombre</td>
				<td class='encabezadoTabla'>Edad</td>
				<td class='encabezadoTabla'>Categor&iacutea</td>
				<td class='encabezadoTabla'>Sala de espera</td>
				<td class='encabezadoTabla'>Hce<br><span style='font-family: verdana;font-weight:normal;font-size: 7pt;'>Cl&iacutenica</span></td>
				<td class='encabezadoTabla'>Hce<br><span style='font-family: verdana;font-weight:normal;font-size: 7pt;'>IDC</span></td>
				<td class='encabezadoTabla' colspan='3' align='center'>Acciones</td>
			</tr>";
		
		// --> Obtener lista de pacientes pendientes de triage
		$sqlTurnos = "
		SELECT A.Fecha_data, A.Hora_data, Atutur, Atudoc, Atutdo, Atunom, Atueda, Atuted, Atullt, Atuutr, Atusea, Catnom, Orihis, Oriing
		  FROM ".$wbasedato."_000178 AS A INNER JOIN ".$wbasedato."_000207 AS B ON(A.Atuten = B.Catcod) 
			   LEFT JOIN root_000037 AS C ON(Atudoc = Oriced AND Oriori = '".$wemp_pmla."' AND  Atutdo = Oritid)
		 WHERE Atuest  = 'on'
		   AND Atucta != 'on'
		   AND Atusea LIKE '".$filtroSalaDeEspera."'
		 ORDER BY REPLACE(Atutur, '-', '')*1 ASC
		";
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
		$coloFila	= 'fila2';
		
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			// --> Obtener si el paciente tiene historia clinica para el idc
			$sqlIdc = "
			SELECT Orihis, Oriing
			  FROM root_000037
			 WHERE Oriced = '".$rowTurnos['Atudoc']."'
			   AND Oriori = '10'
			   AND Oritid = '".$rowTurnos['Atutdo']."'
			";
			$resIdc = mysql_query($sqlIdc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlIdc):</b><br>".mysql_error());
			if($rowIdc = mysql_fetch_array($resIdc))
			{
				$historiaIdc 	= $rowIdc['Orihis'];
				$ingresoIdc 	= $rowIdc['Oriing'];
			}
			else
			{
				$historiaIdc	= "";
				$ingresoIdc		= "";
			}
			
			$horaTurno	= new DateTime($rowTurnos['Hora_data']);
			$coloFila 	= (($coloFila == 'fila2') ? 'fila1' : 'fila2');
			
			// --> El turno ya tiene llamado a la ventanilla.
			if($rowTurnos['Atullt'] == 'on' && $rowTurnos['Atuutr'] == $wuse)
				$turnoConLlamadoEnVentanilla = $rowTurnos['Atutur'];
			
			switch($rowTurnos['Atuted'])
			{
				case 'A':
					$tipoEdad = "A&ntilde;os";
					break;
				case 'M':
					$tipoEdad = "Meses";
					break;
				case 'D':
					$tipoEdad = "D&iacuteas";
					break;
				default:
					$tipoEdad = "";
					break;
			}			
			
			$respuesta['html'].= "
			<tr class='".$coloFila." find' id='trTurno_".$rowTurnos['Atutur']."'>
				<td align='center'>".$rowTurnos['Fecha_data']."</td>
				<td align='center'>".$horaTurno->format('h:i:s a')."</td>
				<td align='center'>".gmdate("H:i:s", (strtotime(date("Y-m-d H:i:s"))-strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data'])))."</td>				
				<td align='center'><b>".substr($rowTurnos['Atutur'], 4)."</b></td>
				<td>
					<table width='100%' class='".$coloFila."' id='tablaEditarDocumento_".$rowTurnos['Atutur']."'>
						<tr>
							<td>".$rowTurnos['Atutdo']."-".$rowTurnos['Atudoc']."</td>
							<td align='right'><img class='botonEditarDocumento' style='cursor:pointer' tipoDocAnt='".$rowTurnos['Atutdo']."' docAnt='".$rowTurnos['Atudoc']."' tooltip='si' ondblclick='editarDocumento(this, \"".$rowTurnos['Atutur']."\")' title='Doble click para editar documento' src='../../images/medical/hce/mod.PNG'></td>
						</tr>
					</table>
				</td>
				<td>".$rowTurnos['Atunom']."</td>
				<td>".$rowTurnos['Atueda']." ".$tipoEdad."</td>
				<td>".strtoupper($rowTurnos['Catnom'])."</td>
				<td align='center'>".strtoupper($arraySalasEspera[$rowTurnos['Atusea']])."</td>
				<td align='center'>
					<img width='14' height='14' style='cursor:pointer;".(($rowTurnos['Orihis'] != '') ? "" : "display:none")."' tooltip='si' title='Ver HCE Cl&iacutenica' src='../../images/medical/sgc/lupa.png' onclick='abrirHce(\"".$rowTurnos['Atudoc']."\", \"".$rowTurnos['Atutdo']."\", \"".$rowTurnos['Orihis']."\", \"".$rowTurnos['Oriing']."\", \"".$wemp_pmla."\")'>
				</td>
				<td align='center'>
					<img width='14' height='14' style='cursor:pointer;".(($historiaIdc != '') ? "" : "display:none")."' tooltip='si' title='Ver HCE IDC' src='../../images/medical/sgc/lupa.png' onclick='abrirHce(\"".$rowTurnos['Atudoc']."\", \"".$rowTurnos['Atutdo']."\", \"".$historiaIdc."\", \"".$ingresoIdc."\", \"10\")'>
				</td>
				<td align='center' >
					<img id='imgLlamar".$rowTurnos['Atutur']."' 	style='cursor:pointer;' 				class='botonLlamarPaciente' width='20' heigth='20' tooltip='si' title='Llamar' 				src='../../images/medical/root/Call2.png'		onclick='llamarPacienteAtencion(\"".$rowTurnos['Atutur']."\")'>
					<img id='botonColgar".$rowTurnos['Atutur']."' 	style='cursor:pointer;display:none' 	class='botonColgarPaciente' width='20' heigth='20' tooltip='si' title='Cancelar llamado'  	src='../../images/medical/root/call3.png'		onclick='cancelarLlamarPacienteAtencion(\"".$rowTurnos['Atutur']."\")'>
					<img id='botonLlamando".$rowTurnos['Atutur']."' style='display:none' 					class='botonColgarPaciente' 																src='../../images/medical/ajax-loader1.gif'>
				</td>
				<td align='center' >
					<img id='botonAdmitir".$rowTurnos['Atutur']."' style='cursor:pointer;display:none'  width='18' height='18' tooltip='si' title='Realizar triage' src='../../images/medical/root/grabar.png' onclick='realizarTriage(\"".$rowTurnos['Atutur']."\", \"".$rowTurnos['Atutdo']."\", \"".$rowTurnos['Atudoc']."\", \"".$rowTurnos['Atunom']."\", \"".$rowTurnos['Atueda']."\", \"".strtoupper($rowTurnos['Catnom'])."\")'>
				</td>
				<td align='center' >
					<img id='botonCancelar".$rowTurnos['Atutur']."' style='cursor:pointer;' tooltip='si' 	class='botonCancelarTurno' title='Cancelar turno' src='../../images/medical/eliminar1.png' onclick='cancelarTurno(\"".$rowTurnos['Atutur']."\")'>
				</td>				
			</tr>
			";
			
			$respuesta['cantidad']++;
		}
		if(mysql_num_rows($resTurnos) == 0)
			$respuesta['html'].= "<tr><td colspan='12' align='center' class='fila2'>Sin registros</td></tr>";
		
		$respuesta['html'].= "
		</table>
		<input type='hidden' id='turnoLlamadoPorEsteUsuario' value='".$turnoConLlamadoEnVentanilla."'>
		<br>
		</div>
		";
		
		return $respuesta;
	}
	//-----------------------------------------------------------------------------------
	// --> Pintar lista de pacientes a los que la enfermera de triage canceló el turno
	//-----------------------------------------------------------------------------------
	function listaDePacientesCancelados($fecha)
	{
		global $wbasedato;
		global $wemp_pmla;
		global $conex;
		global $wuse;
		
		$respuesta 						= array("html" => "");
		$wbasedatoCliame 				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
		
		$respuesta['html'] = "
		<div style=''>
		<table id='' style='width:100%' align='center'>
			<tr align='center'>
				<td class='encabezadoTabla' rowspan='2'>Fecha</td>
				<td class='encabezadoTabla' rowspan='2'>Hora</td>
				<td class='encabezadoTabla' rowspan='2'>Turno</td>
				<td class='encabezadoTabla' rowspan='2'>Documento</td>
				<td class='encabezadoTabla' rowspan='2'>Nombre</td>
				<td class='encabezadoTabla' rowspan='2'>Edad</td>
				<td class='encabezadoTabla' rowspan='2'>Categor&iacutea</td>
				<td class='encabezadoTabla' rowspan='2'>Sala de espera</td>
				<td class='encabezadoTabla' align='center' colspan='3'>Cancel&oacute</td>
				<td class='encabezadoTabla' rowspan='2'>Reactivar</td>
			</tr>
			<tr>
				<td class='encabezadoTabla' align='center'>Fecha</td>
				<td class='encabezadoTabla' align='center'>Hora</td>
				<td class='encabezadoTabla' align='center'>Usuario</td>
			</tr>
			";
			
		// --> Obtener lista de pacientes cancelados
		$sqlTurnos = "
		SELECT A.Fecha_data, A.Hora_data, Atutur, Atudoc, Atutdo, Atunom, Atueda, Atuted, Catnom, Salnom  
		  FROM ".$wbasedato."_000178 AS A INNER JOIN ".$wbasedato."_000207 AS B ON(A.Atuten = B.Catcod)
			   INNER JOIN ".$wbasedato."_000182 AS C ON (Atusea = Salcod)
		 WHERE Atuest 		!= 'on'
		   AND Atucta 		!= 'on'
		   AND A.Fecha_data  = '".$fecha."'
		 ORDER BY REPLACE(Atutur, '-', '')*1 ASC
		";
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
		$coloFila	= 'fila2';
		
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			$coloFila 	= (($coloFila == 'fila2') ? 'fila1' : 'fila2');
			$horaTurno	= new DateTime($rowTurnos['Hora_data']);
						
			switch($rowTurnos['Atuted'])
			{
				case 'A':
					$tipoEdad = "A&ntilde;os";
					break;
				case 'M':
					$tipoEdad = "Meses";
					break;
				case 'D':
					$tipoEdad = "D&iacuteas";
					break;
				default:
					$tipoEdad = "";
					break;
			}

			// --> Obtener fecha, hora y usuario canceló
			$fechaCan 		= "";
			$horaCan 		= "00:00:00";
			$usuarioCan 	= "";
			
			$sqlLogCancelo = "
			SELECT Fecha_data, Hora_data, Logusu
			  FROM ".$wbasedato."_000179
			 WHERE Logtur = '".$rowTurnos['Atutur']."'
			   AND Logacc = 'turnoCanceladoDesdeTriage'
			 ORDER BY id DESC
			 LIMIT 1
			";
			$resLogCancelo = mysql_query($sqlLogCancelo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogCancelo):</b><br>".mysql_error());
			if($rowLogCancelo = mysql_fetch_array($resLogCancelo))
			{
				$fechaCan 	= $rowLogCancelo['Fecha_data'];
				$horaCan 	= $rowLogCancelo['Hora_data'];
				
				$sqlNomUsu = "
				SELECT Descripcion
				  FROM usuarios
				 WHERE Codigo = '".$rowLogCancelo['Logusu']."'
				";
				$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
				if($rowNomUsu = mysql_fetch_array($resNomUsu))
					$usuarioCan = $rowNomUsu['Descripcion'];			
			}
			
			$horaCance	= new DateTime($horaCan);
			
			$respuesta['html'].= "
			<tr class='".$coloFila." find' id='trTurno_".$rowTurnos['Atutur']."'>
				<td align='center'>".$rowTurnos['Fecha_data']."</td>
				<td align='center'>".$horaTurno->format('h:i:s a')."</td>
				<td align='center'>".substr($rowTurnos['Atutur'], 4)."</td>
				<td>".$rowTurnos['Atutdo']."-".$rowTurnos['Atudoc']."</td>
				<td>".$rowTurnos['Atunom']."</td>
				<td>".$rowTurnos['Atueda']." ".$tipoEdad."</td>
				<td>".strtoupper($rowTurnos['Catnom'])."</td>
				<td>".strtoupper($rowTurnos['Salnom'])."</td>
				<td align='center'>".$fechaCan."</td>				
				<td align='center'>".$horaCance->format('h:i:s a')."</td>				
				<td>".utf8_encode($usuarioCan)."</td>
				<td align='center'>
					<img style='cursor:pointer;' width='18' height='18' tooltip='si' title='Reactivar paciente' src='../../images/medical/root/grabar.png' onclick='reactivarPaciente(\"".$rowTurnos['Atutur']."\")'>
				</td>				
			</tr>
			";
		}
		if(mysql_num_rows($resTurnos) == 0)
			$respuesta['html'].= "<tr><td colspan='12' align='center' class='fila2'>Sin registros</td></tr>";
		
		$respuesta['html'].= "
		</table>
		</div>
		";
		
		return $respuesta;
	}
	//---------------------------------------------------------
	// --> Pintar lista de pacientes pendientes de triage
	//---------------------------------------------------------
	function listaDePacientesAtendidos($fechaTriage)
	{
		global $wbasedato;
		global $wemp_pmla;
		global $conex;
		global $wuse;
		
		$respuesta 						= array("html" => "");
		$wbasedatoCliame 				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
		$wbasedatoHce 					= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
		$forYcampoTriage				= consultarAliasPorAplicacion($conex, $wemp_pmla, "formularioYcampoTriage");
		$forYcampoTriage				= explode("-", $forYcampoTriage);
		$campoConducta					= trim(consultarAliasPorAplicacion($conex, $wemp_pmla, "CampoPlanConductaDeTriageHce"));
		$campoPlanManejo				= trim(consultarAliasPorAplicacion($conex, $wemp_pmla, "CampoPlanDeManejoTriageHCE"));
				
		$arrHomoConductas 	= array();
		$sqlHomoCon = "
		SELECT Hctcon, Hctcch, Hctcom, Hctpin
		  FROM ".$wbasedato."_000205
		 WHERE Hctest = 'on' 
		";
		$resHomoCon = mysql_query($sqlHomoCon, $conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error en el query sqlHomoCon:$sqlHomoCon - ".mysql_error() ) );
		while($rowHomoCon = mysql_fetch_array($resHomoCon))
		{
			$arrHomoConductas[$rowHomoCon['Hctcon']][$rowHomoCon['Hctcch']]['Especialidad']		= $rowHomoCon['Hctcom'];
			$arrHomoConductas[$rowHomoCon['Hctcon']][$rowHomoCon['Hctcch']]['permiteIngreso']	= $rowHomoCon['Hctpin'];
		}
		
		// --> Obtener maestro de salas de espera
		$arraySalasEspera = array();
		$sqlSalaEsp = "
		SELECT Salcod, Salnom
		  FROM ".$wbasedato."_000182
		 WHERE Salest = 'on'
		";
		$resSalaEsp = mysql_query($sqlSalaEsp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalaEsp):</b><br>".mysql_error());
		while($rowSalaEsp = mysql_fetch_array($resSalaEsp))
			$arraySalasEspera[$rowSalaEsp['Salcod']] = $rowSalaEsp['Salnom'];
			
		$respuesta['html'] = "
		<div style=''>
		<table id='tablaListaAtendidosTriage' style='width:100%' align='center'>
			<tr align='center'>
				<td class='encabezadoTabla' rowspan='2'>Turno</td>
				<td class='encabezadoTabla' rowspan='2'>Documento</td>
				<td class='encabezadoTabla' rowspan='2'>Nombre</td>
				<td class='encabezadoTabla' rowspan='2'>Edad</td>
				<td class='encabezadoTabla' rowspan='2'>Categor&iacutea</td>
				<td class='encabezadoTabla' rowspan='2'>Prioridad</td>
				<td class='encabezadoTabla' rowspan='2'>Sala de espera</td>
				<td class='encabezadoTabla' rowspan='2'>Tom&oacute turno</td>
				<td class='encabezadoTabla' colspan='3'>Triage</td>
				<td class='encabezadoTabla' rowspan='2'>Admitido</td>
				<td class='encabezadoTabla' rowspan='2'>Tiempo espera consulta<br><span style='font-size:9px'>(Desde el triage)</span></td>
				<td class='encabezadoTabla' rowspan='2'>Estado</td>
				<td class='encabezadoTabla' rowspan='2' align='center'>Reclasificar</td>
			</tr>
			<tr align='center' rowspan='2'>
				<td class='encabezadoTabla'>Fecha/Hora</td>
				<td class='encabezadoTabla'>Nivel</td>
				<td class='encabezadoTabla'>Ver</td>
			</tr>";
		
		// --> Obtener maestro de prioridades
		$arrayPrioridades 	= array();
		$sqlPrioridades 	= "
		SELECT Pricod, Prinom
		  FROM ".$wbasedato."_000206
		 WHERE Priest  = 'on'
		";
		$resPrioridades = mysql_query($sqlPrioridades, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPrioridades):</b><br>".mysql_error());	
		while($rowPrioridades = mysql_fetch_array($resPrioridades))
			$arrayPrioridades[$rowPrioridades['Pricod']] = $rowPrioridades['Prinom'];
		
		// --> Obtener lista de pacientes que ya tienen triage
		$sqlTurnos = "
		SELECT A.Fecha_data, A.Hora_data, Atufad, Atuhad, Atufat, Atutur, Atudoc, Atutdo, Atunom, Atueda, Atuted, Atuadm, Atupri, Atusea, Catnom, Ahthis, Ahting, Ahthte, Atuest
		  FROM ".$wbasedato."_000178 AS A INNER JOIN ".$wbasedato."_000207 AS B ON(A.Atuten = B.Catcod)
			   LEFT JOIN ".$wbasedato."_000204 AS C ON(Atutur = Ahttur)
		 WHERE Atucta  = 'on'
		   AND Atufat  LIKE '".$fechaTriage."%'
		 ORDER BY REPLACE(Atutur, '-', '')*1 DESC
		";
		
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
		$coloFila	= 'fila2';		
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			$fechaTriage	= explode(" ",$rowTurnos['Atufat']);
			$fhTomoTurno	= new DateTime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']);
			
			if($rowTurnos['Atufad'] != '0000-00-00' && $rowTurnos['Atuhad'] != '00:00:00')
				$fhAdmitido		= new DateTime($rowTurnos['Atufad']." ".$rowTurnos['Atuhad']);
			else
				unset($fhAdmitido);
			
			$horaTriage		= new DateTime($fechaTriage[1]);
			$fechaTriage	= $fechaTriage[0];
			$coloFila 		= (($coloFila == 'fila2') ? 'fila1' : 'fila2');
			$permitirReclasi= false;
			$tiempoEspera 	= "";
			$tiempoMax 		= "";
			$nivelTriage	= "";
			$estado 		= "";
			
			switch($rowTurnos['Atuted'])
			{
				case 'A':
					$tipoEdad = "A&ntilde;os";
					break;
				case 'M':
					$tipoEdad = "Meses";
					break;
				case 'D':
					$tipoEdad = "D&iacuteas";
					break;
				default:
					$tipoEdad = "";
					break;
			}
			
			// --> Obtener estado del paciente
			
			// --> Turno cancelado
			if(trim($rowTurnos['Atuest']) != 'on')
			{
				// --> Obtener quien cancelo el turno
				$sqlCancel = "
				SELECT Descripcion
				  FROM ".$wbasedato."_000179, usuarios
				 WHERE Logtur = '".$rowTurnos['Atutur']."'
				   AND Logacc LIKE 'turnoCancelado%'
				   AND Logusu = Codigo
				 ORDER BY id DESC
				 LIMIT 1
				";
				$resCancel = mysql_query($sqlCancel, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancel):</b><br>".mysql_error());
				if($rowCancel = mysql_fetch_array($resCancel))
					$nombreUsuCancelo = utf8_encode($rowCancel['Descripcion']);
				else
					$nombreUsuCancelo = "";
				
				$estado 		= "<b>Cancelado </b><img style='cursor:help;' width='15' height='15' tooltip='si' title='".$nombreUsuCancelo."' src='../../images/medical/sgc/info_black.png'>";
				$permitirReclasi= false;
			}	
			else
			{
				// --> Turno que no tiene admision
				if($rowTurnos['Atuadm'] != 'on')
				{
					$datosHce 			= obtenerDatoHce($rowTurnos['Ahthte'], '1', trim($forYcampoTriage[0]), array($campoConducta));				
					$datosHce2 			= obtenerDatoHce($rowTurnos['Ahthte'], '1', trim($forYcampoTriage[0]), array($campoPlanManejo));				
					$mostrarTr 			= true; 
					$datosHce			= $datosHce[$campoConducta];
					$datosHceArr		= explode("-", $datosHce);
					$datosHce 			= $datosHceArr[0];
					
					$datosHce2			= $datosHce2[$campoPlanManejo];
					$datosHceArr2		= explode("-", $datosHce2);
					$datosHce2 			= $datosHceArr2[0];
					if(trim($datosHce) != '' || trim($datosHce2) != '' )
					{
						//$especialidadTriage	= $arrHomoConductas[$datosHce]['Especialidad'];
						// --> 	Si es un campo que no permite hacer ingreso 
						//		osea el campo de alta o de direccionamiento del formulario de triage, estan asignados en "si".
						if(trim($datosHce) != '' && $arrHomoConductas[$campoConducta][$datosHce]['permiteIngreso'] != 'on')
						{
							$estado 		= $datosHceArr[1];
							$permitirReclasi= true;
						}
						elseif(trim($datosHce2) != '' && $arrHomoConductas[$campoPlanManejo][$datosHce2]['permiteIngreso'] != 'on')
							{
								$estado 		= $datosHceArr2[1];
								$permitirReclasi= true;							
							}
							else
							{
								$estado 		= "Pendiente de admisi&oacuten";
								$permitirReclasi= true;
							}
					}
					else
					{
						$estado 		= "Pendiente de admisi&oacuten";
						$permitirReclasi= true;
					}
				}
				else
				{
					if($rowTurnos['Ahthis'] != '' && $rowTurnos['Ahting'] != '')
					{
						// --> Buscar si ya le hicieron consulta
						$sqlCons = "
						SELECT Mtrfco, Mtrhco
						  FROM ".$wbasedatoHce."_000022
						 WHERE Mtrhis = '".$rowTurnos['Ahthis']."'
						   AND Mtring = '".$rowTurnos['Ahting']."'
						   AND Mtrfco != '0000-00-00'
						";
						$resCons = mysql_query($sqlCons, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCons):</b><br>".mysql_error());
						if($rowCons = mysql_fetch_array($resCons))
						{
							$estado 			= "Atendido";
							$fechaHoraConsulta 	= $rowCons['Mtrfco']." ".$rowCons['Mtrhco'];
						}
						else
						{
							$permitirReclasi= true;
							$estado = "Pendiente de consulta";
						}
					}
					// --> 	Si ya está admitido y no tiene historia ni ingreso asignado no lo muestro
					// 		Esto es porque se puede anular la admision, pero queda el registro en la 178
					else
					{
						continue;
					}	
				}
			}
			
			if($rowTurnos['Ahthis'] != '' && $rowTurnos['Ahting'] != '')
			{
				$historiaHce 	= $rowTurnos['Ahthis'];
				$ingresoHce 	= $rowTurnos['Ahting'];
			}
			else
			{
				$historiaHce 	= $rowTurnos['Ahthte'];
				$ingresoHce 	= '1';
			}	
			
			// --> Obtener el valor del triage
			$respuestaHce		= obtenerDatoHce($historiaHce, $ingresoHce, trim($forYcampoTriage[0]), array($forYcampoTriage[1]));
			$triage				= $respuestaHce[$forYcampoTriage[1]];
			if($triage != '')
			{
				$triage			= explode("-", $triage);
				$nivelTriage	= $triage[0];
				$triage			= "Nivel ".trim($triage[0])*1;
			}
			else
				$triage = "";	
			
			// --> Calcular tiempo de espera
			if($estado == "Alta o remitido" || trim($rowTurnos['Atuest']) != 'on')
			{
				$tiempoEspera = 0;
			}
			else
			{
				if(isset($fechaHoraConsulta))
					$tiempoEspera = strtotime($fechaHoraConsulta)-strtotime($rowTurnos['Atufat']);
				else
				{
					// --> Si aun no hay tiempo de consulta, activar alertas				
					$tiempoEspera 	= strtotime(date("Y-m-d H:i:s"))-strtotime($rowTurnos['Atufat']);				
					$tiempoMax		= "";
					$tiempoCero 	= strtotime('1970-01-01 00:00:00')-18000;
					// --> Calcular si se supera el tiempo de espera
					switch($nivelTriage)
					{
						// --> Atencion inmediata
						case '01':
						{
							break;
						}
						// --> Maximo 30 minutos
						case '02':
						{
							$tiempoMax = $tiempoCero+1800;							
							break;
						}
						// --> Maximo 1 hora
						case '03':
						{
							$tiempoMax = $tiempoCero+3600;
							break;
						}
						// --> Maximo 2 horas
						case '04':
						{
							$tiempoMax = $tiempoCero+7200;
							break;
						}
						// --> Maximo 4 horas
						case '05':
						{
							$tiempoMax = $tiempoCero+14400;
							break;
						}					
					}
				}
			}
			
			$respuesta['html'].= "
			<tr class='".$coloFila." find' id='trTurno_".$rowTurnos['Atutur']."'>			
				<td align='center'><b>".substr($rowTurnos['Atutur'], 4)."</b></td>
				<td>".$rowTurnos['Atutdo']."-".$rowTurnos['Atudoc']."</td>
				<td nowrap>".$rowTurnos['Atunom']."</td>
				<td>".$rowTurnos['Atueda']." ".$tipoEdad."</td>
				<td>".strtoupper($rowTurnos['Catnom'])."</td>
				<td align='center'>".$arrayPrioridades[$rowTurnos['Atupri']]."</td>
				<td align='center'>".strtoupper($arraySalasEspera[$rowTurnos['Atusea']])."</td>
				<td align='center'>".$fhTomoTurno->format('Y-m-d h:i:s a')."</td>
				<td align='center'>".$fechaTriage." ".$horaTriage->format('h:i:s a')."</td>
				<td align='center'>".$triage."</td>				
				<td align='center'>
					<img width='14' height='14' style='cursor:pointer;' tooltip='si' title='Ver triage' src='../../images/medical/sgc/lupa.png' onclick='imprimirTriage(\"".$rowTurnos['Atudoc']."\", \"".$rowTurnos['Atutdo']."\", \"".$historiaHce."\", \"".$ingresoHce."\", \"".$rowTurnos['Atunom']."\", \"".$fechaTriage."\")'>
				</td>
				<td align='center'>".((isset($fhAdmitido)) ? $fhAdmitido->format('Y-m-d h:i:s a') : "")."</td>
				";
				
			if(($tiempoMax != '' && $tiempoEspera > $tiempoMax) || $nivelTriage == '01')
			{
				$respuesta['html'].= "
				<td align='center'><span blink style='color:red;font-weight:bold'>".gmdate("H:i:s", $tiempoEspera)."</span></td>";
			}
			else
			{
				$respuesta['html'].= "
				<td align='center'>".gmdate("H:i:s", $tiempoEspera)."</td>";
			}
			
			$respuesta['html'].= "
				<td align='center'>".$estado."</td>
				<td align='center' >
					<img style='cursor:pointer;".(($permitirReclasi) ? "" : "display:none")."'  width='18' height='18' tooltip='si' title='Reclasificar' src='../../images/medical/root/grabar.png' onclick='reclasificarPaciente(\"".$rowTurnos['Atutur']."\", \"".$rowTurnos['Atutdo']."\", \"".$rowTurnos['Atudoc']."\", \"".$rowTurnos['Atunom']."\", \"".$historiaHce."\", \"".$ingresoHce."\", \"".$rowTurnos['Atupri']."\", \"".$triage."\", \"".$arrayPrioridades[$rowTurnos['Atupri']]."\")'>
				</td>
			</tr>
			";
		}
		if(mysql_num_rows($resTurnos) == 0)
			$respuesta['html'].= "<tr><td colspan='17' align='center' class='fila2'>Sin registros</td></tr>";
		
		$respuesta['html'].= "
		</table>
		</div>
		";
		
		return $respuesta;
	}
	//-----------------------------------------------------------------------------
	// --> 	Funcion que obtiene el valor del triage en la HCE
	//		2016-06-16, Jerson Trujillo.
	//-----------------------------------------------------------------------------
	function obtenerDatoHce($historia, $ingreso, $formulario, $arrCampos)
	{
		global $conex;
		global $wemp_pmla;
		
		$respuesta			= array();
		$wbasedatoHce 		= consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
		
		$campos 			= "";
		foreach($arrCampos as $valorC)
		{
			if(trim($valorC) != '')
				$campos.= (($campos == "") ? "'".$valorC."'" : ", '".$valorC."'");
		}
		
		// --> Consultar fecha y hora del formulario.
		$sqlForTri = "
		SELECT Fecha_data, Hora_data
		  FROM ".$wbasedatoHce."_000036
		 WHERE Firhis = '".$historia."' 
		   AND Firing = '".$ingreso."' 
		   AND Firpro = '".$formulario."'
		 ORDER BY Fecha_data DESC, Hora_data DESC	   
		";
		$resForTri = mysql_query($sqlForTri, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlForTri):</b><br>".mysql_error());
		if($rowForTri = mysql_fetch_array($resForTri))
		{
			// --> Consultar datos del formulario
			$sqlDatosHce = "
			SELECT movcon, movdat
			  FROM ".$wbasedatoHce."_".$formulario."
			 WHERE Fecha_data = '".$rowForTri['Fecha_data']."'
			   AND Hora_data  = '".$rowForTri['Hora_data']."'
			   AND movpro 		= '".$formulario."'
			   AND movcon 		IN(".$campos.")
			   AND movhis 		= '".$historia."'
			   AND moving 		= '".$ingreso."'
			";
			$resDatosHce = mysql_query($sqlDatosHce, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDatosHce):".$sqlDatosHce."</b><br>".mysql_error());
			while($rowDatosHce = mysql_fetch_array($resDatosHce))
				$respuesta[$rowDatosHce['movcon']] = trim($rowDatosHce['movdat']);
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
		case 'llamarPacienteAtencion':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');

			// --> Validar que el paciente no este siendo llamado a triage en este momento
			$sqlValLla = "
			SELECT Descripcion
			  FROM ".$wbasedato."_000178, usuarios
			 WHERE Atutur = '".$turno."'
			   AND (Atullt = 'on' OR Atuetr = 'on' OR Atucta = 'on')
			   AND Atuutr != '".$wuse."'
			   AND Atuutr = Codigo
			";
			$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
			if($rowValLla = mysql_fetch_array($resValLla))
			{
				$respuesta['Error'] 	= TRUE;
				$respuesta['Mensaje'] 	= "El turno ya est&aacute; siendo llamado a triage por:<br>".utf8_encode($rowValLla['Descripcion']);
			}
			else
			{
				// --> realizar el llamado
				$sqlLlamar = "
				UPDATE ".$wbasedato."_000178
				   SET Atullt = 'on',
					   Atufht = '".date('Y-m-d')." ".date("H:i:s")."',
					   Atuutr = '".$wuse."',
					   Atuctl = '".$consultorio."'
				 WHERE Atutur = '".$turno."'
				";
				mysql_query($sqlLlamar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLlamar):</b><br>".mysql_error());

				// --> Registrar en el log el llamado
				$sqlRegLLamado = "
				INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,			id)
											VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'llamadoTriage',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
				";
				mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());
			}

			echo json_encode($respuesta);
			return;
		}
		case 'llamarPacienteAtencionRecla':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');

			// --> Validar que el paciente no tenga ningun tipo de llamado en este momento
			$sqlValLla = "
			SELECT Atuusu, Atuulc, Atullv, Atullc, Atuart, Atuurt, Atuetr
			  FROM ".$wbasedato."_000178, usuarios
			 WHERE Atutur = '".$turno."'
			   AND (Atullv = 'on' OR Atullc = 'on' OR Atuart = 'on' OR Atuetr = 'on')
			";
			$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
			if($rowValLla = mysql_fetch_array($resValLla))
			{
				$respuesta['Error'] 	= TRUE;
				
				if($rowValLla['Atullv'] == 'on')
				{
					$respuesta['Mensaje'] 	= "El paciente est&aacute; siendo llamado a admisi&oacute;n por ";
					$usuario 				= $rowValLla['Atuusu']; 
				}
				elseif($rowValLla['Atullc'] == 'on')
					{
						$respuesta['Mensaje'] 	= "El paciente est&aacute; siendo llamado a consulta por ";
						$usuario 				= $rowValLla['Atuulc']; 
					}
					elseif($rowValLla['Atuart'] == 'on')
						{
							// --> Usuario diferente al actual
							if($rowValLla['Atuurt'] != $wuse)
							{
								$respuesta['Mensaje'] 	= "El paciente est&aacute; siendo llamado a reclasificaci&oacute;n triage por ";
								$usuario 				= $rowValLla['Atuurt'];
							}
							else
								$respuesta['Error'] = FALSE;
						}
						elseif($rowValLla['Atuetr'] == 'on')
							{
								// --> Usuario diferente al actual
								if($rowValLla['Atuurt'] != $wuse)
								{
									$respuesta['Mensaje'] 	= "El paciente est&aacute; en reclasificaci&oacute;n triage con ";
									$usuario 				= $rowValLla['Atuurt']; 
								}
								else
									$respuesta['Error'] = FALSE;
							}

				$sqlNomUsu = "
				SELECT Descripcion
				  FROM usuarios
				 WHERE Codigo = '".$usuario."'
				";
				$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
				if($rowNomUsu = mysql_fetch_array($resNomUsu))
					$nomUsuario = $rowNomUsu['Descripcion'];
				else
					$nomUsuario = '';
				
				$respuesta['Mensaje'].= "<br>".$nomUsuario;
			}
			
			if(!$respuesta['Error'])
			{
				$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
				// --> Validar que no este en consulta con el medico
				$sqlConsul = "
				SELECT Descripcion
				  FROM ".$wbasedatoHce."_000022, usuarios
				 WHERE Mtrhis = '".$historia."'
				   AND Mtring = '".$ingreso."'
				   AND Mtrcur = 'on'
				   AND Mtrmed = Codigo
				";
				$resConsul = mysql_query($sqlConsul, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConsul):</b><br>".mysql_error());
				if($rowConsul = mysql_fetch_array($resConsul))
				{
					$respuesta['Error'] 	= TRUE;
					$respuesta['Mensaje'] 	= "El paciente est&aacute; en consulta con ".$rowConsul['Descripcion'];
				}
				else
				{				
					// --> realizar el llamado
					$sqlLlamar = "
					UPDATE ".$wbasedato."_000178
					   SET Atuart = 'on',
						   Atufhr = '".date('Y-m-d')." ".date("H:i:s")."',
						   Atuurt = '".$wuse."',
						   Atuctl = '".$consultorio."'
					 WHERE Atutur = '".$turno."'
					";
					mysql_query($sqlLlamar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLlamar):</b><br>".mysql_error());

					// --> Registrar en el log el llamado
					$sqlRegLLamado = "
					INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,							Logusu,			Seguridad,			id)
												VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'llamadoReclasificacionTriage',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
					";
					mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());
				}
			}
			
			$respuesta['Mensaje'] = utf8_encode($respuesta['Mensaje']);
			echo json_encode($respuesta);
			return;
		}
		case 'cancelarLlamarPacienteAtencion':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');
			
			// --> Cancelar el turno
			$sqlCancelar = "
			UPDATE ".$wbasedato."_000178
			   SET Atullt = 'off',
				   Atufht = '0000-00-00 00:0:00',
				   Atuutr = ''
			 WHERE Atutur = '".$turno."'
			";
			$resCancelar = mysql_query($sqlCancelar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelar):</b><br>".mysql_error());

			// --> Registrar en el log la cancelacion del llamado
			$sqlRegLLamado = "
			INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,					Logusu,		Seguridad,				id)
										VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'cancelaLlamadoTriage',	'".$wuse."', 'C-".$wbasedato."',	NULL)
			";
			mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());

			echo json_encode($respuesta);
			return;
		}
		case 'cancelarLlamarPacienteAtencionRecla':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');
			
			// --> Cancelar el llamado
			$sqlCancelar = "
			UPDATE ".$wbasedato."_000178
			   SET Atuart = 'off',
				   Atufhr = '0000-00-00 00:0:00'
			 WHERE Atutur = '".$turno."'
			";
			$resCancelar = mysql_query($sqlCancelar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelar):</b><br>".mysql_error());

			// --> Registrar en el log la cancelacion del llamado
			$sqlRegLLamado = "
			INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,									Logusu,		Seguridad,				id)
										VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'cancelaLlamadoReclasificacionTriage',	'".$wuse."', 'C-".$wbasedato."',	NULL)
			";
			mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());

			echo json_encode($respuesta);
			return;
		}
		case 'cancelarTurno':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');
			
			// --> Validar que el paciente no haya sido llamado o que no este en triage
			$sqlValLla = "
			SELECT Descripcion
			  FROM ".$wbasedato."_000178, usuarios
			 WHERE Atutur = '".$turno."'
			   AND (Atullt = 'on' OR Atuetr = 'on' OR Atucta = 'on')
			   AND Atuutr = Codigo
			";
			$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
			if($rowValLla = mysql_fetch_array($resValLla))
			{
				$respuesta['Error'] 	= TRUE;
				$respuesta['Mensaje'] 	= "No se puede cancelar el turno, porque ya est&aacute siendo atendido por ".$rowValLla['Descripcion'];
			}
			else
			{
				// --> Inactivar el turno
				$sqlTur = "
				UPDATE ".$wbasedato."_000178
				   SET Atuest = 'off',
					   Atullt = 'off'
				 WHERE Atutur = '".$turno."'
				";
				mysql_query($sqlTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTur):</b><br>".mysql_error());

				// --> Registrar en el log la cancelacion del turno.
				$sqlCancelarTurno = "
				INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,							Logusu,			Seguridad,			id)
											VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'turnoCanceladoDesdeTriage',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
				";
				mysql_query($sqlCancelarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelarTurno):</b><br>".mysql_error());
			}

			echo json_encode($respuesta);
			return;
		}
		case 'guardarRelacionPacienteHistoriaTemp':
		{
			$respuesta = array("Error" => false, "Mensaje" => "");
			
			// --> Si ya existe relacion
			$sqlExiste = "
			SELECT id
			  FROM ".$wbasedato."_000204
			 WHERE Ahttur = '".$turno."'			    
			";
			$resExiste = mysql_query($sqlExiste, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlExiste):</b><br>".mysql_error());
			if($rowExiste = mysql_fetch_array($resExiste))
			{
				// --> Actualizar relacion
				$guardarRel = "
				  UPDATE ".$wbasedato."_000204
				   SET Medico = '".$wbasedato."',
				   Fecha_data = '".date('Y-m-d')."',
					Hora_data = '".date("H:i:s")."',
					   Ahtdoc = '".$documento."',
					   Ahttdo = '".$tipoDoc."',
					   Ahthte = '".$numHistoriaTemp."',
					   Ahttur = '".$turno."',
					   Ahtest = 'on',
					Seguridad = 'C-".$wuse."'
					 WHERE id = '".$rowExiste['id']."'					
				";
				mysql_query($guardarRel, $conex) or die("<b>ERROR EN QUERY MATRIX(guardarRel):</b><br>".mysql_error());
			}
			else
			{			
				// --> Guardar relacion
				$guardarRel = "
				  INSERT INTO ".$wbasedato."_000204
				   SET Medico = '".$wbasedato."',
				   Fecha_data = '".date('Y-m-d')."',
					Hora_data = '".date("H:i:s")."',
					   Ahtdoc = '".$documento."',
					   Ahttdo = '".$tipoDoc."',
					   Ahthte = '".$numHistoriaTemp."',
					   Ahttur = '".$turno."',
					   Ahtest = 'on',
					Seguridad = 'C-".$wuse."'					
				";
				mysql_query($guardarRel, $conex) or die("<b>ERROR EN QUERY MATRIX(guardarRel):</b><br>".mysql_error());
			}
			
			// --> Actualizar estado del turno
			$actualizarTurno = "
			UPDATE ".$wbasedato."_000178
			   SET Atucta = 'on',
			       Atufat = '".date('Y-m-d')." ".date("H:i:s")."',
				   Atuetr = 'off'				   
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($actualizarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarTurno):</b><br>".mysql_error());
			
			// --> Registrar en el log la asignacion del triage.
			$sqlAsigTriage = "
			INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,			id)
										VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'triageAsignado',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
			";
			mysql_query($sqlAsigTriage, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAsigTriage):</b><br>".mysql_error());
			
			echo json_encode($respuesta);
			return;
		}		
		case 'listaPacientesPendientesTriage':
		{			
			$respuesta = listaDePacientesEnEspera($filtroSalaDeEspera);
			
			echo json_encode($respuesta);
			return;
		}
		case 'listaDePacientesAtendidos':
		{
			$respuesta = array("Error" => false, "Html" => "");
			
			$respuesta = listaDePacientesAtendidos($fechaTriage);
			
			echo json_encode($respuesta);
			return;
		}
		case 'listaDePacientesCancelados':
		{
			$respuesta = array("Error" => false, "html" => "");
			
			$respuesta = listaDePacientesCancelados($fecha);
			
			echo json_encode($respuesta);
			return;
		}
		case 'apagarAlertaDePacienteEnTriage':
		{
			$respuesta = array("Error" => false, "Mensaje" => "");
			$actualizarTurno = "
			UPDATE ".$wbasedato."_000178
			   SET Atuetr = 'off'				   
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($actualizarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarTurno):</b><br>".mysql_error());
						
			echo json_encode($respuesta);
			return;
		}
		case 'actualizarNivelTriage':
		{
			$respuesta 						= array("Error" => false, "Mensaje" => "");
			$wbasedatoHce 					= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
			$forYcampoTriage				= consultarAliasPorAplicacion($conex, $wemp_pmla, "formularioYcampoTriage");
			$forYcampoTriage				= explode("-", $forYcampoTriage);
		
			// --> Obtener el valor del triage
			$respuestaHce		= obtenerDatoHce($historia, $ingreso, trim($forYcampoTriage[0]), array($forYcampoTriage[1]));
			$triage				= $respuestaHce[$forYcampoTriage[1]];
			if($triage != '')
			{
				$triage	= explode("-", $triage);
				$triage	= trim($triage[0])*1;				
				
				$actualizarTriage = "
				UPDATE ".$wbasedatoHce."_000022
				   SET Mtrtri = '0".$triage."'				   
				 WHERE Mtrhis = '".$historia."'
				   AND Mtring = '".$ingreso."'
				";
				mysql_query($actualizarTriage, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarTriage):</b><br>".mysql_error());		
			}
			$respuesta['Mensaje'] = $actualizarTriage;
			echo json_encode($respuesta);
			return;
		}
		case 'guardarPrioridad':
		{
			$respuesta = array("Error" => false, "Mensaje" => "");
			$actualizarTurno = "
			UPDATE ".$wbasedato."_000178
			   SET Atupri = '".$prioridad."'				   
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($actualizarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarTurno):</b><br>".mysql_error());
						
			echo json_encode($respuesta);
			return;
		}
		case 'selectorDePrioridades':
		{
			$respuesta = array("Error" => false, "Html" => "");
			
			$respuesta["Html"] = "
			<div align='center'>
				<table>
					<tr>
						<td align='center' style='font-size:10pt;font-family: verdana;font-weight:normal;'>
							&nbsp;<img width='15px' height='15px' id='imgAlerta' src='../../images/medical/sgc/Mensaje_alerta.png'>&nbsp;
							Seleccione la prioridad de atenci&oacuten:
						</td>
					</tr>
					<tr>
						<td align='center'>
							<SELECT id='selectPrioridad' style='border-radius: 4px;border:1px solid #AFAFAF' onChange='guardarPrioridad(\"".$turno."\")'>
								<option></option>
			";
			
			$sqlPrioridades = "
			SELECT Pricod, Prinom
			  FROM ".$wbasedato."_000206			   
			 WHERE Priest = 'on'
			";
			$resPrioridades = mysql_query($sqlPrioridades, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPrioridades):</b><br>".mysql_error());
			while($rowPrioridades = mysql_fetch_array($resPrioridades))
			{
				$respuesta["Html"].= "
				<option ".(($prioridad == $rowPrioridades['Pricod']) ? "SELECTED" : "")." value='".$rowPrioridades['Pricod']."'>".$rowPrioridades['Prinom']."</option>
				";
			}
			$respuesta["Html"].= "
							</SELECT>
						</td>
					</tr>
				</table>
			</div>
			";
			
			echo json_encode($respuesta);
			return;
		}
		case 'obtenerHistoriaTemporal':
		{
			$respuesta = array("Error" => false, "Historia" => "");
			
			// --> Obtener valor del consecutivo
			$sqlConsec = "
			SELECT Detval
			  FROM root_000051
			 WHERE Detemp = '".$wemp_pmla."'
			   AND Detapl = 'consecutivoHistoriaTemporalTriage'
			";
			$resConsec = mysql_query($sqlConsec, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConsec):</b><br>".mysql_error());
			if($rowConsec = mysql_fetch_array($resConsec))
			{
				if(trim($rowConsec['Detval']) != '')
				{
					$respuesta['Historia'] = trim($rowConsec['Detval'])+1;
					
					// --> Actualizar consecutivo
					$sqlActu = "
					UPDATE root_000051
					   SET Detval = '".$respuesta['Historia']."'
					 WHERE Detapl = 'consecutivoHistoriaTemporalTriage'
					";
					$resActu = mysql_query($sqlActu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActu):</b><br>".mysql_error());
					if(!$resActu)
						$respuesta['Error'] = true;
					
					$respuesta['Historia'] = $respuesta['Historia']."C".$puestoTrabajo;
				}
				else
					$respuesta['Error'] = true;
			}
			else
				$respuesta['Error'] = true;
			
			if(!$respuesta['Error'])
			{
				// --> Apagar alerta de llamado a triage
				$actualizarTurno = "
				UPDATE ".$wbasedato."_000178
				   SET Atullt = 'off',
					   Atuetr = 'on',
					   Atufit = '".date('Y-m-d')." ".date("H:i:s")."'				   
				 WHERE Atutur = '".$turno."'
				";
				mysql_query($actualizarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarTurno):</b><br>".mysql_error());
				
				// --> Registrar en el log el inicio del triage.
				$sqlIniTriage = "
				INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,			id)
											VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'inicioDeTriage',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
				";
				mysql_query($sqlIniTriage, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlIniTriage):</b><br>".mysql_error());
				
				// --> Registrar la historia temporal que se asigno.
				$sqlHisTriage = "
				INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,											Logusu,			Seguridad,			id)
											VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'HistoriaTemporal:".$respuesta['Historia']."',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
				";
				mysql_query($sqlHisTriage, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlHisTriage):</b><br>".mysql_error());
			}
			
			echo json_encode($respuesta);
			return;
		}
		case 'reactivarPaciente':
		{
			$actualizarTurno = "
			UPDATE ".$wbasedato."_000178
			   SET Atuest = 'on'
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($actualizarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarTurno):</b><br>".mysql_error());
			
			// --> Guardar log de reactivacion.
			$sqlLogReact = "
			INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,							Logusu,			Seguridad,			id)
										VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'reactivacionTurnoDesdeTriage',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
			";
			mysql_query($sqlLogReact, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogReact):</b><br>".mysql_error());
			
			return;
		}
		case 'actualizarNumeroDeDocumento':
		{
			$actualizarDocTurno = "
			UPDATE ".$wbasedato."_000178
			   SET Atutdo = '".$nuevoTipDoc."',
				   Atudoc = '".$nuevoDoc."' 	
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($actualizarDocTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarDocTurno):</b><br>".mysql_error());
			
			// --> Guardar log de actualizacion.
			$sqlLogActua = "
			INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,																									Logusu,			Seguridad,			id)
										VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'actualizacionDocumento(ANTERIOR:".$tipoDocAnt."-".$docAnt." NUEVO:".$nuevoTipDoc."-".$nuevoDoc.")',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
			";
			mysql_query($sqlLogActua, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogActua):</b><br>".mysql_error());
			
			return;
		}
		case 'iniciaTriageReclasificacion':
		{
			$respuesta = array("Error" => false, "Html" => "");
			
			// --> Apagar alerta de llamado a triage
			$actualizarTurno = "
			UPDATE ".$wbasedato."_000178
			   SET Atuart = 'off',
				   Atuetr = 'on'
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($actualizarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarTurno):</b><br>".mysql_error());
			
			// --> Registrar en el log el inicio del triage por reclasificacion.
			$sqlIniTriage = "
			INSERT INTO ".$wbasedato."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,								Logusu,			Seguridad,			id)
										VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'inicioDeTriagePorReclasificacion',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
			";
			mysql_query($sqlIniTriage, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlIniTriage):</b><br>".mysql_error());
			
			$respuesta["Html"] = $actualizarTurno;
			echo json_encode($respuesta);
			return;
		}
		// --> 	Actualiza el puesto de trabajo asociado a un usuario
		// 		Jerson trujillo, 2016-06-15.
		case 'cambiarPuestoTrabajo':
		{
			$usuario		= $wuse;
			$respuesta 		= array("Error" => FALSE, "Mensaje" => "");

			// --> Validar que el puesto de trabajo este disponible
			$sqlValPuesTra = "
			SELECT Descripcion
			  FROM ".$wbasedato."_000180, usuarios
			 WHERE Puecod = '".$puestoTrabajo."'
			   AND Pueusu != ''
			   AND Pueusu = Codigo
			";
			$resValPuesTra = mysql_query($sqlValPuesTra, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValPuesTra):</b><br>".mysql_error());
			if($respetarOcupacion == 'true' && $rowValPuesTra = mysql_fetch_array($resValPuesTra))
			{
				$respuesta["Error"] 	= TRUE;
				$respuesta["Mensaje"] 	= 'Este consultorio ya esta ocupado por <br>'.$rowValPuesTra['Descripcion'];
			}
			else
			{
				// --> Quitar cualquier puesto de trabajo asociado al usuario
				$sqlUpdatePues = "
				UPDATE ".$wbasedato."_000180
				   SET Pueusu = ''
				 WHERE Pueusu = '".$usuario."'
				";
				mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());

				if($puestoTrabajo != '')
				{
					// --> Asignar el nuevo puesto de trabajo
					$sqlUpdatePues = "
					UPDATE ".$wbasedato."_000180
					   SET Pueusu = '".$usuario."'
					 WHERE Puecod = '".$puestoTrabajo."'
					";
					mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());
				}
			}

			echo json_encode($respuesta);
			return;
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
	  <title>Triage</title>
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
	var interval;
	var relojTemp;
	var relojTempTriage;
	var blinkTd;
	var blinkImgReloj;
	var blinkAlertaPrio;
	var blinkTiempEspera;
	
	$(function(){
		// --> Activar tabs jaquery
		$( "#tabsTriage" ).tabs({
			heightStyle: "content"
		});
		
		// --> Si hay un turno que se está llamando
		if($("#turnoLlamadoPorEsteUsuario").val() != '')
		{
			var idTurno = $("#turnoLlamadoPorEsteUsuario").val();
			
			$(".botonLlamarPaciente").hide();
			$(".botonColgarPaciente").hide();
			$(".botonCancelarTurno").hide();
			$("#imgLlamar"+idTurno).hide();
			$("#botonColgar"+idTurno).show();
			$("#botonLlamando"+idTurno).show();
			$("#botonAdmitir"+idTurno).show();
		}
		
		// --> Buscadores
		$('#buscarPacientesEnEspera').quicksearch('#tablaListaEsperaTriage .find');
		$('#buscarPacientesAtendidos').quicksearch('#tablaListaAtendidosTriage .find');
		
		// --> Tooltip
		$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		
		activarRelojTemporizador();
		
		// --> Parametrización del datapicker
		cargar_elementos_datapicker();
		// --> Activar datapicker
		$("#fechaTriage").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				listaDePacientesAtendidos();
			}
		});
		$("#fechaTriage").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
		$("#fechaTriage").after(" ");
		
		// --> Activar datapicker
		$("#fechaCancelo").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				listaDePacientesCancelados();
			}
		});
		$("#fechaCancelo").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
		$("#fechaCancelo").after(" ");
		
		// --> Blink
		clearInterval(blinkTiempEspera);
			
		blinkTiempEspera = setInterval(function(){
			$("span[blink]").each(function(){
				$(this).css('visibility' , $(this).css('visibility') === 'hidden' ? '' : 'hidden');
			});
		}, 500);
	});
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
	//------------------------------------------------------------------------------------
	// --> Funcion que genera el llamado del paciente para que sea atendido en el triage
	//------------------------------------------------------------------------------------
	function llamarPacienteAtencion(turno)
	{
		if($("#puestoTrabajo").val() == "")
		{
			jAlert("<span style='color:red'>Primero debe seleccionar su puesto de trabajo actual.</span>", "Mensaje");
			$("#puestoTrabajo").css("border-color", "red");
			return;
		}
		
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'llamarPacienteAtencion',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno,
			consultorio:			$("#puestoTrabajo").val()
		}, function(respuesta){
			if(respuesta.Error)
			{
				jAlert("<span style='color:red'>"+respuesta.Mensaje+"</span>", "Mensaje");
				$(".botonLlamarPaciente").show();
				$(".botonEditarDocumento").show();
				$(".botonColgarPaciente").hide();
				$("#botonAdmitir"+turno).hide();
			}
			else
			{
				$(".botonLlamarPaciente").hide();
				$(".botonColgarPaciente").hide();
				$(".botonCancelarTurno").hide();
				$("#imgLlamar"+turno).hide();
				$("#botonColgar"+turno).show();
				$("#botonLlamando"+turno).show();
				$("#botonAdmitir"+turno).show();
				
				$(".botonCancelarEdicionDoc").each(function(){
					$(this).trigger('click');
				});
				setTimeout( function(){
					$(".botonEditarDocumento").hide();
				}, 100);
			}
		}, 'json');
	}
	//------------------------------------------------------------------------------------
	// --> Funcion que genera el llamado del paciente para que sea atendido en el triage
	//------------------------------------------------------------------------------------
	function llamarPacienteAtencionRecla(turno, historia, ingreso)
	{
		if($("#puestoTrabajo").val() == "")
		{
			jAlert("<span style='color:red'>Primero debe seleccionar su puesto de trabajo actual.</span>", "Mensaje");
			$("#puestoTrabajo").css("border-color", "red");
			return;
		}
		
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'llamarPacienteAtencionRecla',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno,
			historia:				historia,
			ingreso:				ingreso,
			consultorio:			$("#puestoTrabajo").val()
		}, function(respuesta){
			if(respuesta.Error)
			{
				jAlert("<span style='color:red'>"+respuesta.Mensaje+"</span>", "Mensaje");
				$("#imgLlamarRe").show();
				$("#botonColgarRe").hide();
				$("#botonLlamandoRe").hide();
				$("#botonAdmitirRe").hide();
			}
			else
			{
				$("#imgLlamarRe").hide();
				$("#botonColgarRe").show();
				$("#botonLlamandoRe").show();
				$("#botonAdmitirRe").show();
			}
		}, 'json');
	}	
	//-----------------------------------------------------------------------
	// --> Funcion que cancela el llamado del paciente 
	//-----------------------------------------------------------------------
	function cancelarLlamarPacienteAtencion(turno)
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'cancelarLlamarPacienteAtencion',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno
		}, function(respuesta){

			$(".botonLlamarPaciente").show();
			$(".botonCancelarTurno").show();
			$(".botonColgarPaciente").hide();
			$("#botonAdmitir"+turno).hide();
			$(".botonEditarDocumento").show();
		});
	}
	//-----------------------------------------------------------------------
	// --> Funcion que cancela el llamado a reclasificacion del paciente 
	//-----------------------------------------------------------------------
	function cancelarLlamarPacienteAtencionRecla(turno)
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'cancelarLlamarPacienteAtencionRecla',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno
		}, function(respuesta){

			$("#imgLlamarRe").show();
			$("#botonColgarRe").hide();
			$("#botonLlamandoRe").hide();
			$("#botonAdmitirRe").hide();
		});
	}
	//----------------------------------------------------
	// --> Funcion que cancela el turno de un paciente
	//----------------------------------------------------
	function cancelarTurno(turno)
	{
		jConfirm("<span style='color:red'>Est&aacute seguro que desea cancelar el turno "+turno+" ?</span>", 'Confirmar', function(respuesta) {
			if(respuesta)
			{
				$.post("triage.php",
				{
					consultaAjax:   		'',
					accion:         		'cancelarTurno',
					wemp_pmla:        		$('#wemp_pmla').val(),
					turno:					turno
				}, function(respuesta){
					if(respuesta.Error)
						jAlert("<span style='color:red'>"+respuesta.Mensaje+"</span>", "Mensaje");
					else
						$("#trTurno_"+turno).hide(500);
				}, 'json');
			}
		});
	}
	//----------------------------------------------------------------------------
	// --> Funcion que pinta un selector para asignarle la prioridad al paciente
	//----------------------------------------------------------------------------
	function selectorDePrioridades(turno, prioridad)
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'selectorDePrioridades',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno,
			prioridad:				prioridad
		}, function(respuesta){
				
			clearInterval(interval);			
			clearInterval(relojTempTriage);
			clearInterval(blinkImgReloj);
			
			$("#divFormularioHce").dialog( "destroy" );
			$("#divFormularioHce").html("<div>"+respuesta.Html+"</div>");
			$("#divFormularioHce").dialog({
				show:{
					effect: "blind",
					duration: 0
				},
				hide:{
					effect: "blind",
					duration: 0
				},
				width:  'auto',
				dialogClass: 'fixed-dialog',
				modal: true,
				buttons:[
				{ 
					text: "Cerrar",
					icons:{
							primary: "ui-icon-heart"
					},
					click: function(){
						$(this).dialog("close");
					}
				}],
				title: "Asignaci&oacuten de prioridad",
				close: function( event, ui ) {
					apagarIndicadorEnTriage(turno);
					clearInterval(blinkTd);
					if($("#selectPrioridad").val() == "")
						selectorDePrioridades(turno, prioridad);
					else
					{
						if(prioridad != "")
							listaDePacientesAtendidos();
						else
							listaPacientesPendientesTriage();
					}
				}
			});
			
			// --> Blink
			blinkTd = setInterval(function(){
				$("#imgAlerta").css('visibility' , $("#imgAlerta").css('visibility') === 'hidden' ? '' : 'hidden')
			}, 400);
			
		}, 'json');
	}	
	//----------------------------------------------------------------------------
	// --> Funcion que le asigna la prioridad al paciente
	//----------------------------------------------------------------------------
	function guardarPrioridad(turno)
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'guardarPrioridad',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno,
			prioridad:				$("#selectPrioridad").val()
		}, function(respuesta){
		}, 'json');
	}
	// --> Cerra la modal de escala de glasgow
	function cerrarModal()
	{
		$.unblockUI();
	}
	//----------------------------------------------------------------------
	// --> Funcion que abre el formulario hce para realizar el triage
	//----------------------------------------------------------------------
	function realizarTriage(turno, tipoDoc, documento, nombre, edad, categoria)
	{
		if($("#puestoTrabajo").val() == "")
		{
			jAlert("<span style='color:red'>Primero debe seleccionar su puesto de trabajo actual.</span>", "Mensaje");
			$("#puestoTrabajo").css("border-color", "red");
			return;
		}
		
		$("#botonAdmitir"+turno).hide();
		$("#botonColgar"+turno).hide();		
		
		// --> Obtener el consecutivo de historia temporal y guardar log del inicio del triage
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'obtenerHistoriaTemporal',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno,
			puestoTrabajo:			$("#puestoTrabajo").val()
		}, function(respuesta){
			
			$("#divFormularioHce").dialog("destroy");
			
			if(respuesta.Error)
			{
				jAlert("<span style='color:red'>Error obteniendo el número de historia temporal.<br>Por favor reporte la inconsistencia.</span>", "Mensaje");
				return;
			}
			
			var formTipoOrden 	= '000152'; 
			var numHistoriaTemp = 'TEMP'+$.trim(respuesta.Historia);		
			var urlform 		= '/matrix/hce/procesos/HCE.php?accion=M&ok=0&empresa=hce&origen='+$('#wemp_pmla').val()+'&wdbmhos=movhos&wformulario='+formTipoOrden+'&wcedula='+documento+'&wtipodoc='+tipoDoc+'&whis='+numHistoriaTemp+'&wing=1';
			
			turnoTemp = turno.split("-");
			
			infoPaciente = ""
			+"<fieldset align='center' style='padding:6px;'>"
			+"<legend class='fieldset'>Informaci&oacuten del paciente:</legend>"
			+"<table width=100% id='infoPacEnTriage'>"
				+"<tr>"
					+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
					+"<td class=fila1>Documento</td>"
					+"<td class=fila2>"+tipoDoc+"-"+documento+"</td>"
					+"<td class=fila1>Paciente</td><td class=fila2>"+nombre+"</td><td class=fila1>Edad</td><td class=fila2>"+edad+"</td><td class=fila1>Categora</td><td class=fila2>"+categoria+"</td>"
				+"</tr>"
			+"</table>"
			+"</fieldset>";
	
			// --> Cargar el iframe
			$("#divFormularioHce").html("<div align=center>"+infoPaciente+"</div><iframe id='frameFormularioTriage' name='frameFormularioTriage' src='"+urlform+"' height='600px' width='950px' scrolling=yes frameborder='0'></iframe>");
			
			// --> Validar si ya se guardo el formulario
			setTimeout( function(){	
				frame1 = $('#frameFormularioTriage').contents();
				$(frame1).contents().find("iframe").height(1200);
				
				// --> 	Cada segundo se valida si existe el elemento "tipoLGOK" con el texto "DATOS GRABADOS OK", si existen
				//		esto indica que se grabo el formulario con exito.
				interval = setInterval(function(){
					// var f=new Date();
					// cad=f.getHours()+":"+f.getMinutes()+":"+f.getSeconds(); 
					// console.log(cad);
					// --> Obtener triage
					// valorTriageTemp = $(frame1).contents().find("iframe").contents().find("[name=R54]:checked").val();
					// if(valorTriageTemp != undefined && valorTriageTemp != "")
						// valorTriageFinal = valorTriageTemp;
					
					frame1 = $('#frameFormularioTriage').contents();					
					var botonGrabadoOk 	= $(frame1).contents().find("iframe").contents().find("#tipoLGOK");
					var texto 			= $.trim($(botonGrabadoOk).text());
					
					// --> ESTO ES PARA HACERLE SEGUIMIENTO A UN ERROR 
					if(texto == "")
						console.clear();
					else
						console.log("->"+texto);
					// --> FIN SEGUIMIENTO
					
					if(texto != "" && texto.search("DATOS GRABADOS OK") >= 0)
					{
						console.log("Entro");						
						guardarRelacionPacienteHistoriaTemp(turno, tipoDoc, documento, numHistoriaTemp);
						clearInterval(interval);			
						clearInterval(relojTempTriage);
						clearInterval(blinkImgReloj);
						
						// --> Pintar selector de prioridades
						setTimeout( function(){
							selectorDePrioridades(turno, '');
						}, 0);
					}
				}, 1000);			
				
			}, 1000);
			
			var htmlRelojTemp = "<table width='100%'><tr>"
			+"<td style='font-family:verdana;font-size: 11pt;color: #4C4C4C;font-weight:bold'>Formulario Triage</td>"
			+"<td align='right' style='font-family:verdana;font-size: 10pt;color: #4C4C4C;font-weight:normal'><span id='spanRelojTriage' style='border-radius: 4px;'>&nbsp;Duraci&oacuten triage:&nbsp;<span id='relojTempTriage' cincoMinTem='86400000'></span>&nbsp;&nbsp;<img id='imgRelojTempTriage' width='15px' height='15px' src='../../images/medical/sgc/Clock-32.png'>&nbsp;</span></td>"
			+"</tr></table>";
			
						
			
			// --> Ventana dialog para cargar el iframe
			$("#divFormularioHce").dialog({
				show:{
					effect: "blind",
					duration: 0
				},
				hide:{
					effect: "blind",
					duration: 100
				},
				width:  'auto',
				dialogClass: 'fixed-dialog',
				modal: true,
				title: htmlRelojTemp,
				buttons:[
				{ 
					text: "Cerrar",
					icons:{
							primary: "ui-icon-heart"
					},
					click: function(){
						$(this).dialog("close");
					}
				}],
				close: function( event, ui ) {					
					clearInterval(interval);
					clearInterval(relojTempTriage);
					clearInterval(blinkImgReloj);
					listaPacientesPendientesTriage();
					apagarIndicadorEnTriage(turno);
				}
			});
			//$("#divFormularioHce").parent().css({"z-index":"999"}).next().css({"z-index":"998"});
				
			activarRelojTemporizadorTriage();
			
		}, 'json');
	}
	//------------------------------------------------------------------------------------
	// --> Funcion que abre el formulario hce para realizar el triage para reclasificaion
	//------------------------------------------------------------------------------------
	function realizarTriageReclasificacion(turno, tipoDoc, documento, historia, ingreso, prioridad, nombre, triageAnt, nomPrioridad)
	{
		if($("#puestoTrabajo").val() == "")
		{
			jAlert("<span style='color:red'>Primero debe seleccionar su puesto de trabajo actual.</span>", "Mensaje");
			$("#puestoTrabajo").css("border-color", "red");
			return;
		}
		
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'iniciaTriageReclasificacion',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno
		}, function(respuesta){
			
			//$("#divFormularioHce").dialog("close");
			$("#divFormularioHce").dialog( "destroy" );
			turnoTemp = turno.split("-");
			
			infoPaciente		= ""
			+"<fieldset align='center' style='padding:6px;'>"
			+"<legend class='fieldset'>Informaci&oacuten del paciente:</legend>"
			+"<table width=100%>"
				+"<tr>"
					+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td><td class=fila1>Documento</td><td class=fila2>"+tipoDoc+"-"+documento+"</td><td class=fila1>Paciente</td><td class=fila2>"+nombre+"</td><td class=fila1>Triage asignado:</td><td class=fila2>"+triageAnt+"</td><td class=fila1>Prioridad asignada:</td><td class=fila2>"+nomPrioridad+"</td>"
				+"</tr>"
			+"</table>"
			+"</fieldset>";
			
			var formTipoOrden 	= '000152';
			var urlform 		= '/matrix/hce/procesos/HCE.php?accion=M&ok=0&empresa=hce&origen='+$('#wemp_pmla').val()+'&wdbmhos=movhos&wformulario='+formTipoOrden+'&wcedula='+documento+'&wtipodoc='+tipoDoc+'&whis='+historia+'&wing='+ingreso+'';
			
			// --> Cargar el iframe
			$("#divFormularioHce").html("<div align=center>"+infoPaciente+"</div><iframe id='frameFormularioTriage' name='frameFormularioTriage' src='"+urlform+"' height='600px' width='950px' scrolling=yes frameborder='0'><span id='prueba'>Prueba</span></iframe>");
			
			// --> Validar si ya se guardo el formulario
			setTimeout( function(){	
				frame1 = $('#frameFormularioTriage').contents();
				$(frame1).contents().find("iframe").height(1200);
				
				// --> 	Cada segundo se valida si existe el elemento "tipoLGOK" con el texto "DATOS GRABADOS OK", si existen
				//		esto indica que se grabo el formulario con exito.
				interval = setInterval(function(){
										
					var botonGrabadoOk 	= $(frame1).contents().find("iframe").contents().find("#tipoLGOK");
					var texto 			= $.trim($(botonGrabadoOk).text());
					
					if(texto != "" && texto.search("DATOS GRABADOS OK") >= 0)
					{
						// --> Actualizar triage en hce_22
						actualizarNivelTriage(turno, historia, ingreso);
						
						clearInterval(interval);			
						clearInterval(relojTempTriage);
						clearInterval(blinkImgReloj);
						
						// --> Pintar selector de prioridades
						setTimeout( function(){
							selectorDePrioridades(turno, prioridad);
						}, 0);
					}
				}, 1000);			
				
			}, 1000);
			
			var htmlRelojTemp = "<table width='100%'><tr>"
			+"<td style='font-family:verdana;font-size: 11pt;color: #4C4C4C;font-weight:bold'>Formulario Triage</td>"
			+"<td align='right' style='font-family:verdana;font-size: 10pt;color: #4C4C4C;font-weight:normal'><span id='spanRelojTriage' style='border-radius: 4px;'>&nbsp;Duraci&oacuten triage:&nbsp;<span id='relojTempTriage' cincoMinTem='86400000'></span>&nbsp;&nbsp;<img id='imgRelojTempTriage' width='15px' height='15px' src='../../images/medical/sgc/Clock-32.png'>&nbsp;</span></td>"
			+"</tr></table>";
				
			// --> Ventana dialog para cargar el iframe
			$("#divFormularioHce").dialog({
				show:{
					effect: "blind",
					duration: 0
				},
				hide:{
					effect: "blind",
					duration: 100
				},
				width:  'auto',
				dialogClass: 'fixed-dialog',
				modal: true,
				title: htmlRelojTemp,
				buttons:[
				{ 
					text: "Cerrar",
					icons:{
							primary: "ui-icon-heart"
					},
					click: function(){
						$(this).dialog("close");
					}
				}],
				close: function( event, ui ) {					
					clearInterval(interval);
					clearInterval(relojTempTriage);
					clearInterval(blinkImgReloj);
					apagarIndicadorEnTriage(turno);
					listaDePacientesAtendidos();
				}
			});			
			$("#divFormularioHce").parent().css({"z-index":"999"}).next().css({"z-index":"998"});
			
			activarRelojTemporizadorTriage();
			
		});
	}
	
	//----------------------------------------------------------------------
	// --> Actualizarle el nivel de triage a un paciente en la hce_000022
	//----------------------------------------------------------------------
	function actualizarNivelTriage(turno, historia, ingreso)
	{
		// --> Si no es una historia temporal
		if(historia.search("TEMP") < 0)
		{
			$.post("triage.php",
			{
				consultaAjax:   		'',
				accion:         		'actualizarNivelTriage',
				wemp_pmla:        		$('#wemp_pmla').val(),
				turno:					turno,
				historia:				historia,
				ingreso:				ingreso
			}, function(respuesta){
			}, 'json');
		}
	}
	//----------------------------------------------------------------------
	// --> Apagar alerta de que el paciente está en triage
	//----------------------------------------------------------------------
	function apagarIndicadorEnTriage(turno)
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'apagarAlertaDePacienteEnTriage',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno
		}, function(respuesta){
		});
	}
	//------------------------------------------------------------------------------
	// --> Guardar informacion de la relacion del paciente y la historia temporal
	//------------------------------------------------------------------------------
	function guardarRelacionPacienteHistoriaTemp(turno, tipoDoc, documento, numHistoriaTemp)
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'guardarRelacionPacienteHistoriaTemp',
			wemp_pmla:        		$('#wemp_pmla').val(),
			numHistoriaTemp:		numHistoriaTemp,
			documento:				documento,
			tipoDoc:				tipoDoc,
			turno:					turno
		}, function(respuesta){
			
		}, 'json');
	}
	
	//----------------------------------------------------------------------
	// --> Pinta la lista de pacientes pendientes triage
	//----------------------------------------------------------------------
	function listaPacientesPendientesTriage()
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'listaPacientesPendientesTriage',
			wemp_pmla:        		$('#wemp_pmla').val(),
			filtroSalaDeEspera:		$("#filtroSalaDeEspera").val()
		}, function(respuesta){
			$("#listaEsperaTriage").html(respuesta.html);
			
			clearInterval(blinkAlertaPrio);
			
			// --> Mensaje de priorizacion
			if(respuesta.cantidad>5)
			{
				$("#msjPriorizacion").html("<img width='15px' height='15px' id='alertaPrio' src='../../images/medical/sgc/Mensaje_alerta.png'> Por favor realizar priorizaci&oacuten, <b>"+respuesta.cantidad+"</b> pacientes en espera.").show();
				
				// --> Blink
				blinkAlertaPrio = setInterval(function(){
					$("#alertaPrio").css('visibility' , $("#alertaPrio").css('visibility') === 'hidden' ? '' : 'hidden')
				}, 400);
			}
			else
			{
				$("#msjPriorizacion").html("").hide();
			}	
			
			// --> Tooltip
			$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			
			activarRelojTemporizador();
			
			// --> Si hay un turno que se está llamando
			if($("#turnoLlamadoPorEsteUsuario").val() != '')
			{
				var idTurno = $("#turnoLlamadoPorEsteUsuario").val();
				
				$(".botonLlamarPaciente").hide();
				$(".botonColgarPaciente").hide();
				$(".botonCancelarTurno").hide();
				$("#imgLlamar"+idTurno).hide();
				$("#botonColgar"+idTurno).show();
				$("#botonLlamando"+idTurno).show();
				$("#botonAdmitir"+idTurno).show();
			}
			
			$('#buscarPacientesEnEspera').quicksearch('#tablaListaEsperaTriage .find');
			
		}, 'json');
	}
	//----------------------------------------------------------------------
	// --> Pinta la lista de pacientes pendientes triage
	//----------------------------------------------------------------------
	function listaDePacientesAtendidos()
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'listaDePacientesAtendidos',
			wemp_pmla:        		$('#wemp_pmla').val(),
			fechaTriage:			$("#fechaTriage").val()
		}, function(respuesta){
			$("#listaConTriage").html(respuesta.html);
			// --> Tooltip
			$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			
			$('#buscarPacientesAtendidos').quicksearch('#tablaListaAtendidosTriage .find');
			
			activarRelojTemporizador();
			
			// --> Blink
			clearInterval(blinkTiempEspera);
			
			blinkTiempEspera = setInterval(function(){
				$("span[blink]").each(function(){
					$(this).css('visibility' , $(this).css('visibility') === 'hidden' ? '' : 'hidden');
				});
			}, 500);
			
			if($("#tablaListaAtendidosTriage").height() >= '500')
				$("#listaConTriage").css({"height":"510px", "overflow":"auto", "background":"none repeat scroll 0 0"});
			
		}, 'json');
	}
	//-----------------------------------------------------------------------
	// --> 	Reloj temporizador
	//		Jerson Trujillo, 2016-01-13
	//-----------------------------------------------------------------------
	function activarRelojTemporizador()
	{
		clearInterval(relojTemp);
		$("#relojTemp").text("00:00");
		$("#relojTemp").attr("cincoMinTem", "86400000");
		relojTemp = setInterval(function(){
			var cincoMin 	= new Date(parseInt($("#relojTemp").attr("cincoMinTem")));
			var cincoMinTem	= cincoMin.getTime();
			cincoMinTem += 1000;
			cincoMin.setTime(cincoMinTem);
			minuto	 	= ((String(cincoMin.getMinutes()).length == 1) ? "0"+cincoMin.getMinutes() : cincoMin.getMinutes());
			segundo 	= ((String(cincoMin.getSeconds()).length == 1) ? "0"+cincoMin.getSeconds() : cincoMin.getSeconds());

			var nuevoCincoMin = minuto+":"+segundo;
			$("#relojTemp").text(nuevoCincoMin);
			$("#relojTemp").attr("cincoMinTem", cincoMinTem);
		}, 1000);

		var tab = $("#tabsTriage").find("li[class*=ui-state-active]").attr("id");
		$("#tdBotonActualizar").html("");
		if(tab == "tab1")
			$("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="listaPacientesPendientesTriage()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
		else
			$("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="listaDePacientesAtendidos()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
	}
	//-----------------------------------------------------------------------
	// --> 	Reloj temporizador para el triage
	//		Jerson Trujillo, 2016-06-21
	//-----------------------------------------------------------------------
	function activarRelojTemporizadorTriage()
	{
		clearInterval(relojTempTriage);
		$("#relojTempTriage").text("00:00");
		$("#relojTempTriage").attr("cincoMinTem", "86400000");
		relojTempTriage = setInterval(function(){
			var cincoMin 	= new Date(parseInt($("#relojTempTriage").attr("cincoMinTem")));
			var cincoMinTem	= cincoMin.getTime();
			cincoMinTem += 1000;
			cincoMin.setTime(cincoMinTem);
			minuto	 	= ((String(cincoMin.getMinutes()).length == 1) ? "0"+cincoMin.getMinutes() : cincoMin.getMinutes());
			segundo 	= ((String(cincoMin.getSeconds()).length == 1) ? "0"+cincoMin.getSeconds() : cincoMin.getSeconds());

			var nuevoCincoMin = minuto+":"+segundo;
			$("#relojTempTriage").text(nuevoCincoMin);
			$("#relojTempTriage").attr("cincoMinTem", cincoMinTem);

			// --> Cuando el reloj quede en 5:00
			if($("#relojTempTriage").text() == '05:00')
			{
				$("#spanRelojTriage").css({"color":"#ffffff", "background-color":"red"});
				imagenReloj = $("#imgRelojTempTriage");
				// --> Blink
				blinkImgReloj = setInterval(function(){
					imagenReloj.css('visibility' , imagenReloj.css('visibility') === 'hidden' ? '' : 'hidden')
				}, 400);
			}
		}, 1000);
	}
	
	//----------------------------------------------------------------------------------
	//	--> Abre en una modal la impresion del triage
	//----------------------------------------------------------------------------------
	function imprimirTriage(documento, tipoDoc, historia, ingreso, nombrePaciente, fechaTriage)
	{
		var url 	= "/matrix/movhos/procesos/impresionTriage.php?empresa=hce&origen="+$("#wemp_pmla").val()+"&wcedula="+documento+"&wtipodoc="+tipoDoc+"&wdbmhos=movhos&whis="+historia+"&wing="+ingreso+"&nombrePaciente="+nombrePaciente+"&wfechai="+fechaTriage+"&wservicio=*&protocolos=0&CLASE=C&BC=1";
		
		$("#divImpresionTriage").html("<iframe src='"+url+"' width='750px' height='1200px' scrolling=yes frameborder='0'></iframe>");
		
		// --> Ventana dialog para cargar el iframe
		$("#divImpresionTriage").dialog("destroy");
		$("#divImpresionTriage").dialog({
			show:{
				effect: "blind",
				duration: 0
			},
			hide:{
				effect: "blind",
				duration: 100
			},
			width:  'auto',
			height:	'700',
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "",
			buttons:{
				"Cerrar": function() {
					$(this).dialog("close");
				},
				"Imprimir": function() {
					// --> Imprimir tiquete de turno.
					setTimeout(function(){
						var contenido	= "<html><body onload='window.print();window.close();'>";
						contenido 		= contenido+$("#divImpresionTriage").html()+"</body></html>";

						var windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=1,height=1,resizable=yes,screenX=1,screenY=1,personalbar=no,scrollbars=no";
						var ventana = window.open( "", "",  windowAttr );
						ventana.document.write(contenido);
						ventana.document.close();
					}, 1000);
				}
			},
			close: function( event, ui ) {	
			}
		});
			
	}
	//----------------------------------------------------------------------------------
	//	--> Enlace para ir a la historia clinica
	//----------------------------------------------------------------------------------
	function abrirHce(documento, tipoDoc, historia, ingreso, empresa)
	{
		wdbmhos	= (empresa == 01) ? "movhos" : "mhosidc";
		emp		= (empresa == 01) ? "hce" : "hceidc";
		
		var url 	= "/matrix/HCE/procesos/HCE_Impresion.php?empresa="+emp+"&origen="+empresa+"&wcedula="+documento+"&wtipodoc="+tipoDoc+"&wdbmhos="+wdbmhos+"&whis="+historia+"&wing="+ingreso+"&wservicio=*&protocolos=0&CLASE=C&BC=1";
		// alto		= screen.availHeight;
		// ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
		// ventana.document.open();
		// ventana.document.write("<span><b>CONSULTA DESDE TABLERO DE TRIAGE<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
		
		$("#divImpresionTriage").html("<iframe  src='"+url+"' height='600px' width='770px' scrolling=yes frameborder='0'></iframe>");
		
		// --> Ventana dialog para cargar el iframe
		$("#divImpresionTriage").dialog("destroy");
		$("#divImpresionTriage").dialog({
			show:{
				effect: "blind",
				duration: 0
			},
			hide:{
				effect: "blind",
				duration: 100
			},
			width:  'auto',
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "",
			buttons:{
				"Cerrar": function() {
					$(this).dialog("close");
				}
			},
			close: function( event, ui ) {	
			}
		});
	}
	
	//----------------------------------------------------------------------------------
	//	--> Enlace para ir a la historia clinica
	//----------------------------------------------------------------------------------
	function reclasificarPaciente(turno, documento, tipoDoc, nomPac, historia, ingreso, prioridad, triageAnt, nomPrioridad)
	{
		if($("#puestoTrabajo").val() == "")
		{
			jAlert("<span style='color:red'>Primero debe seleccionar su puesto de trabajo actual.</span>", "Mensaje");
			$("#puestoTrabajo").css("border-color", "red");
			return;
		}
		
		mensaje = '<span style="color:#2C5FB1">Est&aacute; seguro en reclasificar este paciente?</span>';
		jConfirm(mensaje, 'Confirmar', function(respuesta) {
			if(respuesta)
			{
				htmlOpcionLlamado = ""
				+"<br><table>"
					+"<tr class=fila2>"
						+"<td align='center'>"+nomPac+"</td>"
					+"</tr>"
					+"<tr style='font-size:9pt;padding:5px;font-family:verdana;color:#000000;'>"
						+"<td align='center'><br>Llamar paciente nuevamente a triage:<br>"
								+"<img id='imgLlamarRe' 		tooltip='si'	title='Llamar'				style='cursor:pointer;' 				src='../../images/medical/root/Call2.png'		onclick='llamarPacienteAtencionRecla(\""+turno+"\", \""+historia+"\", \""+ingreso+"\")'>"
								+"<img id='botonColgarRe' 		tooltip='si'	title='Cancelar llamado'	style='cursor:pointer;display:none' 	src='../../images/medical/root/call3.png' 		onclick='cancelarLlamarPacienteAtencionRecla(\""+turno+"\")'>"
								+"<img id='botonLlamandoRe' 												style='display:none' 					src='../../images/medical/ajax-loader1.gif'>&nbsp;&nbsp;&nbsp;"
								+"<img id='botonAdmitirRe' 		tooltip='si'	title='Realizar triage' 	style='cursor:pointer;display:none'		src='../../images/medical/root/grabar.png'		onclick='realizarTriageReclasificacion(\""+turno+"\", \""+tipoDoc+"\", \""+documento+"\", \""+historia+"\", \""+ingreso+"\", \""+prioridad+"\", \""+nomPac+"\", \""+triageAnt+"\", \""+nomPrioridad+"\")'>"
						+"</td>"
					+"</tr>"
				+"</table>";
						
				$("#divFormularioHce").dialog("destroy");
				$("#divFormularioHce").html(htmlOpcionLlamado)
				// --> Tooltip
				$('[tooltip=si]', $("#divFormularioHce")).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				
				$("#divFormularioHce").dialog({
					show:{
						effect: "blind",
						duration: 0
					},
					hide:{
						effect: "blind",
						duration: 100
					},
					width:  'auto',
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "Reclasificar paciente:",
					buttons:[
					{ 
						text: "Cerrar",
						icons:{
								primary: "ui-icon-heart"
						},
						click: function(){
							$(this).dialog("close");
						}
					}],
					close: function( event, ui ) {
						cancelarLlamarPacienteAtencionRecla(turno);
					}
				});
			}
		});
	}
	//-------------------------------------------------------------
	// --> Pintar lista de pacientes cancelados
	//-------------------------------------------------------------
	function listaDePacientesCancelados()
	{
		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'listaDePacientesCancelados',
			wemp_pmla:        		$('#wemp_pmla').val(),
			fecha:					$("#fechaCancelo").val()
		}, function(respuesta){
			$("#listaCancelados").html(respuesta.html);
			// --> Tooltip
			$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		}, 'json');
	}
	//-------------------------------------------------------------
	// --> Reactivar un turno que estaba cancelado
	//-------------------------------------------------------------
	function reactivarPaciente(turno)
	{
		jConfirm("<span style='color:#2A5DB0'>Est&aacute seguro en reactivar el paciente?</span>", 'Confirmar', function(respuesta) {
			if(respuesta)
			{
				$.post("triage.php",
				{
					consultaAjax:   		'',
					accion:         		'reactivarPaciente',
					wemp_pmla:        		$('#wemp_pmla').val(),
					turno:					turno
				}, function(respuesta){
					listaDePacientesCancelados();
				});
			}
		});
	}	
	//-------------------------------------------------------------
	// --> Actualiza el numero de documento asociado a un turno
	//-------------------------------------------------------------
	function editarDocumento(elemento, turno)
	{
		elemento 	= $(elemento);
		tipoDocAnt 	= elemento.attr("tipoDocAnt");
		docAnt 		= elemento.attr("docAnt");
		
		selectTipDoc 			= "<select id='actTipDoc_"+turno+"' style='font-size:11px'>";
		arrayTiposDeDocumentos 	= JSON.parse($("#arrayTiposDeDocumentos").val());
		jQuery.each( arrayTiposDeDocumentos, function( i, val ){
			selectTipDoc+= "<option tooltip='si2' title='"+val+"' value='"+i+"' "+((i == tipoDocAnt) ? "selected" : "")+">"+i+"</option>";
		});
		
		selectTipDoc+= "</select>";
			
		elemento.parent().prev().html(selectTipDoc+"&nbsp;<input type='text' id='actNumDoc_"+turno+"' value='"+docAnt+"'>");
		elemento.parent().attr("align", "center").html("<img src='../../images/medical/root/grabar16.png' width='12px' height='12px' style='cursor:pointer' tooltip='si2' title='Guardar' onClick='guardarEditarDocumento(\""+tipoDocAnt+"\", \""+docAnt+"\", \""+turno+"\")'>&nbsp;&nbsp;<img src='../../images/medical/hce/cancel.PNG' width='12px' height='12px' tooltip='si2' title='Cancelar' style='cursor:pointer' class='botonCancelarEdicionDoc' onClick='cancelarEditarDocumento(\""+tipoDocAnt+"\", \""+docAnt+"\", \""+turno+"\")'>");
		
		// --> Tooltip
		$('[tooltip=si2]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	}	
	//-------------------------------------------------------------
	// --> Guarda la edicion del docuento
	//-------------------------------------------------------------
	function guardarEditarDocumento(tipoDocAnt, docAnt, turno)
	{
		if($.trim($("#actNumDoc_"+turno).val()) == "")
		{
			$("#actNumDoc_"+turno).val(docAnt);
			return;
		}
		
		// --> Si no hay ningun cambio
		if($.trim($("#actTipDoc_"+turno).val()) == tipoDocAnt && $.trim($("#actNumDoc_"+turno).val()) == docAnt)
			return;
		else
		{
			jConfirm("<span style='color:#2A5DB0'>Est&aacute seguro de actualizar este documento?</span>", 'Confirmar', function(respuesta) {
				if(respuesta)
				{
					$.post("triage.php",
					{
						consultaAjax:   		'',
						accion:         		'actualizarNumeroDeDocumento',
						wemp_pmla:        		$('#wemp_pmla').val(),
						turno:					turno,
						nuevoTipDoc:			$.trim($("#actTipDoc_"+turno).val()),
						nuevoDoc:				$.trim($("#actNumDoc_"+turno).val()),
						tipoDocAnt:				tipoDocAnt,
						docAnt:					docAnt
					}, function(respuesta){
						cancelarEditarDocumento($("#actTipDoc_"+turno).val(), $("#actNumDoc_"+turno).val(), turno);
					});
				}
			});
		}
	}
	//-------------------------------------------------------------
	// --> Cancela la edicion del docuento
	//-------------------------------------------------------------
	function cancelarEditarDocumento(tipoDocAnt, docAnt, turno)
	{
		html = "<tr>"
					+"<td>"+tipoDocAnt+"-"+docAnt+"</td>"
					+"<td align='right'><img class='botonEditarDocumento' style='cursor:pointer' tipoDocAnt='"+tipoDocAnt+"' docAnt='"+docAnt+"' tooltip='si3' ondblclick='editarDocumento(this, \""+turno+"\")' title='Doble click para editar documento' src='../../images/medical/hce/mod.PNG'></td>"
				+"</tr>";
						
		$("#tablaEditarDocumento_"+turno).html(html);
		// --> Tooltip
		$('[tooltip=si3]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	}
	//-------------------------------------------------------------
	// --> Actualiza el usuario asociado a un puesto de trabajo
	//-------------------------------------------------------------
	function cambiarPuestoTrabajo(respetarOcupacion)
	{
		if($("#puestoTrabajo").val() == '' )
			$("#puestoTrabajo").css("border-color", "red");
		else
			$("#puestoTrabajo").css("border-color", "#AFAFAF");

		$.post("triage.php",
		{
			consultaAjax:   		'',
			accion:         		'cambiarPuestoTrabajo',
			wemp_pmla:        		$('#wemp_pmla').val(),
			puestoTrabajo:			$("#puestoTrabajo").val(),
			respetarOcupacion:		respetarOcupacion
		}, function(respuesta){
			if(respuesta.Error)
			{
				jConfirm("<span style='color:#2A5DB0'>"+respuesta.Mensaje+"\nDesea liberarlo?</span>", 'Confirmar', function(respuesta) {
					if(respuesta)
						cambiarPuestoTrabajo(false);
					else
						$("#puestoTrabajo").val($("#puestoTrabajo").attr("ventanillaActUsu"));
				});
			}
		}, 'json');
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
		#tooltip{font-family: verdana;font-weight:normal;color: #ffffff;font-size: 7pt;position:absolute;z-index:3000;border:1px solid #000000;background-color:#000000;padding:3px;opacity:1;border-radius: 4px;}
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
	encabezado("Triage", $wactualiz, 'clinica');
	
	// --> Maestro de puestos de trabajo para triage
	$sqlVentanillas	= "
	SELECT Puecod, Puenom, Pueusu
	  FROM ".$wbasedato."_000180
	 WHERE Puectr = 'on'
	   AND Pueest = 'on'
	";
	$resVentanillas = mysql_query($sqlVentanillas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVentanillas):</b><br>".mysql_error());
	while($rowVentanillas = mysql_fetch_array($resVentanillas))
	{
		$arrayVentanillas[$rowVentanillas['Puecod']] = $rowVentanillas['Puenom'];

		if($rowVentanillas['Pueusu'] == $wuse)
			$ventanillaActUsu = $rowVentanillas['Puecod'];
	}
	
	// --> Maestro de salas de espera
	$arraySalasEspera = array();
	$sqlSalaEsp = "
	SELECT Salcod, Salnom
	  FROM ".$wbasedato."_000182
	 WHERE Salest = 'on'
	";
	$resSalaEsp = mysql_query($sqlSalaEsp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalaEsp):</b><br>".mysql_error());
	while($rowSalaEsp = mysql_fetch_array($resSalaEsp))
		$arraySalasEspera[$rowSalaEsp['Salcod']] = $rowSalaEsp['Salnom'];
	
	// --> Maestro de tipos de documento
	$arrayTiposDeDocumentos = array();
	$sqlTipDoc = "SELECT Codigo, Descripcion
					FROM root_000007
				   WHERE Estado = 'on'
	";
	$resTipDoc = mysql_query($sqlTipDoc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipDoc):</b><br>".mysql_error());
	while($rowTipDoc = mysql_fetch_array($resTipDoc))
		$arrayTiposDeDocumentos[trim($rowTipDoc['Codigo'])] = utf8_encode(trim($rowTipDoc['Descripcion']));
	
	echo "
	<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>
	<input type='hidden' id='arrayTiposDeDocumentos' value='".json_encode($arrayTiposDeDocumentos)."'>
	<div id='tabsTriage' style='margin:4px'>
		<ul>
			<li width='30%' id='tab1'><a href='#tabListaEsperaTriage' 	onclick='listaPacientesPendientesTriage()'>Pacientes en espera de triage</a></li>
			<li width='30%' id='tab2'><a href='#tabListaConTriage' 		onclick='listaDePacientesAtendidos()'>Pacientes atendidos</a></li>
			<li width='30%' id='tab3'><a href='#tabListaCancelados'		onclick='listaDePacientesCancelados()'>Cancelados</a></li>
			<li width='40%'>
				<table width='100%' style='padding:6px;font-family: verdana;font-size: 10pt;color: #4C4C4C'>
					<tr>
						<td style='font-weight:normal;'>
							Ultima actualizaci&oacuten:&nbsp;<span id='relojTemp' cincoMinTem='86400000'></span>&nbsp;<img width='15px' height='15px' src='../../images/medical/sgc/Clock-32.png'>
						</td>
						<td style='font-weight:normal;' align='center' id='tdBotonActualizar'>
							&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style='cursor:pointer' onclick='listaPacientesPendientesTriage()'>Actualizar&nbsp;<img width='14px' height='14px' src='../../images/medical/sgc/Refresh-128.png' title='Actualizar listado.'></span>
						</td>
						<td style='font-weight:normal;' align='center'>
							&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
							<span style='font-family: verdana;font-weight:normal;font-size: 10pt;'>
								Consultorio:&nbsp;&nbsp;</b>
								<select id='puestoTrabajo' type='text' style='border-radius: 4px;border:1px solid #AFAFAF;width:200px' ventanillaActUsu='".$ventanillaActUsu."' onChange='cambiarPuestoTrabajo(true)'>
									<option value='' ".((trim($ventanillaActUsu) == "") ? "SELECTED='SELECTED'" : "" ).">Seleccione..</option>
								";
							foreach($arrayVentanillas as $codVentanilla => $nomVentanilla)
								echo "<option value='".$codVentanilla."' ".(($codVentanilla == $ventanillaActUsu) ? "SELECTED='SELECTED'" : "" ).">".$nomVentanilla."</option>";
				echo "			</select>
							</span>
						</td>
					</tr>
				</table>
			</li>
		</ul>
		<div id='tabListaEsperaTriage'>
			<table width='100%'>
				<tr>
					<td align='left'>
						<span style='font-family: verdana;font-size: 10pt;color: #4C4C4C'>
							<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarPacientesEnEspera' type='text' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150px'>
							&nbsp;&nbsp;|&nbsp;&nbsp;<b>Filtrar sala de espera:</background>
							<select id='filtroSalaDeEspera' onChange='listaPacientesPendientesTriage()' style='border-radius: 4px;border:1px solid #AFAFAF;width:100px'>
								<option value='%'>TODAS</option>";
							foreach($arraySalasEspera as $codigoSala => $nombreSala)
								echo "<option value='".$codigoSala."'>".$nombreSala."</option>";		
	echo "					</select>
						</span>
					</td>
					<td align='right'>
						<span style='font-family: verdana;font-size: 10pt;color:#000000;display:none' id='msjPriorizacion'>				
						</span>
					</td>
				</tr>
			</table>
			<div id='listaEsperaTriage'>";
				$respuesta = listaDePacientesEnEspera();
	echo		$respuesta['html'];
	echo "	</div>
		</div>
		<div id='tabListaConTriage'>
			<table width='100%'>
				<tr>
					<td align='left'>
						<span style='font-family: verdana;font-size: 10pt;color: #4C4C4C'>
							<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarPacientesAtendidos' type='text' placeholder=' Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150'>
							&nbsp;&nbsp;|&nbsp;
							<b>Fecha de triage:</b>&nbsp;&nbsp;</b><input id='fechaTriage' type='text' disabled='disabled' value='".date("Y-m-d")."' style='border-radius: 4px;border:1px solid #AFAFAF;width:100px'>
							&nbsp;&nbsp;|
						</span>
					</td>
					<td align='right'>
						<table style='border-radius: 4px;border:1px solid #AFAFAF;color:#000000;font-size:8pt;padding:1px;font-family:verdana;'>
							<tr><td style='background-color:#FFD1D1;color:#000000' class='encabezadoTabla' align='center' colspan='10'><b>Tiempos de espera</b></td></tr>
							<tr>
								<td class='fila2'><b>Triage I:</b> Atenci&oacuten inmediata </td>
								<td class='fila2'><b>Triage II:</b> 15-30 Minutos </td>
								<td class='fila2'><b>Triage III:</b> 1 Hora </td>
								<td class='fila2'><b>Triage IV:</b> 2 Horas </td>
								<td class='fila2'><b>Triage V:</b> 4 Horas</td></tr>
						</table>
					</td>
				</tr>
			</table>
			<div id='listaConTriage'>";
				$respuesta = listaDePacientesAtendidos(date("Y-m-d"));
	echo		$respuesta['html'];
	echo "	</div>				
		</div>
		<div id='tabListaCancelados'>
			<table width='100%'>
				<tr>
					<td align='left'>
						<span style='font-family: verdana;font-size: 10pt;color: #4C4C4C'>
							<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarPacientesCancelados' type='text' placeholder=' Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150'>
							&nbsp;&nbsp;|&nbsp;
							<b>Fecha:</b>&nbsp;&nbsp;</b><input id='fechaCancelo' type='text' disabled='disabled' value='".date("Y-m-d")."' style='border-radius: 4px;border:1px solid #AFAFAF;width:100px'>
							&nbsp;&nbsp;|
						</span>
					</td>
				</tr>
			</table>
			<div id='listaCancelados'>";
				$respuesta = listaDePacientesCancelados(date("Y-m-d"));
	echo		$respuesta['html'];
	echo "	</div>
		</div>
	</div>
	<div id='divFormularioHce' style='display:none' align='center'></div>	
	<div id='divImpresionTriage' style='display:none' align='center'></div>	
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
