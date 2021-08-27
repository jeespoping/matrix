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
//-------------------------------------------------------------------------------------------------------------------------------------------- 
// 2020-01-13 Arleyda Insignares C.
//            Se adiciona una columna 'información de citas', la cual muestra dicha información para los 
//            servicios que tengan habilitado el campo Serbus en la tabla cliame_000298.
//            Se adiciona la acción redireccionar turno, la cual toma el turno del paciente y lo encola 
//            en otro servicio.
			$wactualiz='2020-01-13';
//-----------------------------------------------------------------------------------------------------  
//--------------------------------------------------------------------------------------------------------
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
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];	

	include_once("root/comun.php");
	

	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$wbasmovhos     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wfecha			= date("Y-m-d");   
    $whora 			= date("H:i:s");

//=====================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================
	
	//---------------------------------------------------------
	// --> Pintar lista de pacientes pendientes de atencion
	//---------------------------------------------------------
	function listaDePacientesEnEspera($servicio, $subServicio)
	{
		global $wbasedato;
		global $wbasmovhos;
		global $wemp_pmla;
		global $conex;
		global $wuse;
		global $tema;
		global $redir;
	
		$respuesta 						= array("html" => "", "cantidad" => 0);
		$turnoConLlamadoEnVentanilla 	= "";
		$turnoEnProcesoAtencion 		= "";
		$enlazarConAdmision 			= false; 
		
		if($servicio == "" || $servicio == "null")
		{
			$servicio 		= array("");
			$conServicio 	= false; 
		}
		else
		{
			$servicio 		= json_decode(str_replace('\\', '', $servicio));
			$conServicio 	= true;
		}
		
		$inServicios	= "";
		foreach($servicio as $codS)
			$inServicios.= (($inServicios == "") ? "'".$codS."'" : ", '".$codS."'");
		
		$inSubServicios	= "";
		if($subServicio != "" && $subServicio != "null")
		{
			$subServicio	= json_decode(str_replace('\\', '', $subServicio));
			foreach($subServicio as $codS)
				$inSubServicios.= (($inSubServicios == "") ? "'".$codS."'" : ", '".$codS."'");
		}
		
		// --> Consultar si hay campos adicionales a mostrar segun el servicio.
		$infoCampAdi = array();
		$SelCitas    = 0;
		
		$sqlCamposSer = "
			SELECT Sercod, Sercan, Sercat, Seradm, Serbus  
			  FROM ".$wbasedato."_000298
			 WHERE Sertem = '".$tema."'
			   AND Sercod IN(".$inServicios.")
			 ORDER BY Serbus Desc 
		";
		$resCamposSer = mysql_query($sqlCamposSer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCamposSer):</b><br>".mysql_error());		
		while($rowCamposSer = mysql_fetch_array($resCamposSer))
		{
			$serCod = $rowCamposSer['Sercod']; 

			if($rowCamposSer['Serbus'] == 'on')
               $SelCitas = 1;
			
			if($rowCamposSer['Seradm'] == 'on')
				$infoCampAdi[$serCod]['enlazarConAdmision'] = true;
			else
				$infoCampAdi[$serCod]['enlazarConAdmision'] = false;
			
			if($rowCamposSer['Sercan'] != '' && $rowCamposSer['Sercat'] != '')
			{
				$infoCampAdi[$serCod]['tituloCampoAdi'] = $rowCamposSer['Sercan'];
				
				$style			= "border-radius: 4px;border:1px solid #AFAFAF;";
				switch($rowCamposSer['Sercat'])
				{
					// --> Alfanumerico
					case '0':
					{
						$htmlCampoAdi = "<input type='text' style='".$style."width:100px' nombreCampoAd='".$rowCamposSer['Sercan']."' onBlur='guardarCampoAdi(this)'>";
						break;
					}
					// --> Entero
					case '1':
					{
						$htmlCampoAdi = "<input type='text' tipoCampoAd='entero' nombreCampoAd='".$rowCamposSer['Sercan']."' style='".$style."width:100px' onBlur='guardarCampoAdi(this)'>";
						break;
					}
					// --> Fecha
					case '3':
					{
						$htmlCampoAdi = "<input type='text' tipoCampoAd='fecha' nombreCampoAd='".$rowCamposSer['Sercan']."'style='".$style."width:80px' readonly='readonly'>";
						break;
					}
					// --> Texarea
					case '4':
					{
						$htmlCampoAdi = "<textarea style='".$style."width:180px' nombreCampoAd='".$rowCamposSer['Sercan']."' onBlur='guardarCampoAdi(this)'></textarea></td>";
						break;
					}
					// --> Hora
					case '11':
					{
						$htmlCampoAdi = "<input type='text' tipoCampoAd='hora' nombreCampoAd='".$rowCamposSer['Sercan']."' style='".$style."width:80px' readonly='readonly'>";
						break;
					}
				}
				
				$infoCampAdi[$serCod]['htmlCampoAdi'] = $htmlCampoAdi;
			}
		}
		
		if (isset($redir))
			$nrocols = 4;
		else
			$nrocols = 3;

		if ($SelCitas == 1)
			$campoCita = "<td class='encabezadoTabla'>Información Citas</td>";
		else
			$campoCita ="";

		$respuesta['html'] = "
		<div style=''>
		<table id='tablaListaEsperaAtencion' style='width:100%' align='center'>
			<tr align='center'>
				<td class='encabezadoTabla'>Fecha Hora Turno</td>
				<td class='encabezadoTabla'>En espera<br><span style='font-family: verdana;font-weight:normal;font-size: 7pt;'>H:m:s</span></td>
				<td class='encabezadoTabla'>Turno</td>
				<td class='encabezadoTabla'>Documento</td>
				<td class='encabezadoTabla'>Nombre</td>
				<td class='encabezadoTabla'>Servicio</td>
				".$campoCita."
				<td class='encabezadoTabla'>Sub-Servicio</td>
				<td class='encabezadoTabla'>Usuario prioritario</td>
				<td class='encabezadoTabla'>Campo Adicional</td>
				<td class='encabezadoTabla' colspan=".$nrocols." align='center'>Acciones</td>
			</tr>";
		
		// --> Obtener lista de pacientes atendidos
		$sqlTurnos = "
		SELECT A.Fecha_data, A.Hora_data, A.id, Turtur, Turdoc, Turtdo, Turnom, Turllv, Turpat, Turull, Turvca, Turtse, Sernom, Connom, Conpri,
			   Tursec, Secnom
		  FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000298 AS B ON(Turtem = Sertem AND Turtse = Sercod)
				INNER JOIN ".$wbasedato."_000299 AS C ON(Turupr = Concod)
				LEFT  JOIN ".$wbasedato."_000309 AS D ON(Tursec = Seccod)
		 WHERE Turtem = '".$tema."'
		   AND Turest = 'on'
		   AND Turate != 'on'
		   AND Turtse IN(".$inServicios.")
		   ".(($inSubServicios != "") ? "
		   AND Tursec IN(".$inSubServicios.")
		   " : "")."
		 ORDER BY A.id ASC
		";
		$respuesta['sqlTurnos'] = $sqlTurnos;
		//echo 'listur '.$sqlTurnos;
		
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
		$coloFila	= 'fila2';
		
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			$serCod = $rowTurnos['Turtse'];
			
			// --> El turno ya tiene llamado a la ventanilla.
			if($rowTurnos['Turllv'] == 'on') 
			{
				if( $rowTurnos['Turull'] == $wuse )
					$turnoConLlamadoEnVentanilla = $rowTurnos['Turtur'];
				else
					continue;
			}
			
			// --> El turno esta en proceso de atencion.
			if($rowTurnos['Turpat'] == 'on')
			{	
				if($rowTurnos['Turull'] == $wuse)
					$turnoEnProcesoAtencion = $rowTurnos['Turtur'];
				else
					continue;
			}
			
			if($rowTurnos['Turvca'] != '')
			{
				$valorCampAd = json_decode($rowTurnos['Turvca'], true);
				$valorCampAd = $valorCampAd["valor"];
			}
			else
				$valorCampAd = "";
			
			$horaTurno	= new DateTime($rowTurnos['Hora_data']);
			$coloFila 	= (($coloFila == 'fila2') ? 'fila1' : 'fila2');
			$turno 		= substr($rowTurnos['Turtur'], 7);
			$turno 		= substr($turno, 0, 2)." ".substr($turno, 2, 5);
            
            //En caso de poseer la variable redir en la url, deberá adicionar proceso de redireccionamiento de turno
            $redireccion ='';
			if (isset($redir))
			{
			   $redireccion = "
			   			<td align='center' >
					       <img id='botonRedireccionar".$rowTurnos['Turtur']."' style='cursor:pointer;' tooltip='si' 	class='botonRedireccionar' title='Redireccionar turno' src='../../images/medical/root/flecha_agregar.jpg' onclick='redireccionarTurno(\"".$rowTurnos['Turtur']."\",\"".$rowTurnos['Turtdo']."\",\"".$rowTurnos['Turdoc']."\",\"".$rowTurnos['Turnom']."\")'> 
						</td>
			   ";	
			}

			if ($SelCitas == 1)
				$resCitas = consultarInformacioncitas($conex,$rowTurnos['Turdoc'],$rowTurnos['Fecha_data'],$wbasedato,$wbasmovhos);
			else
				$resCitas = "";
			
			$respuesta['html'].= "
			<tr class='".$coloFila." find' id='trTurno_".$rowTurnos['Turtur']."'>
				<td align='center'>".$rowTurnos['Fecha_data']." - ".$horaTurno->format('h:i:s a')."</td>				
				<td align='center'>".gmdate("H:i:s", (strtotime(date("Y-m-d H:i:s"))-strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data'])))."</td>				
				<td align='center'><b>".$turno."</b></td>
				<td>
					<table width='100%' class='".$coloFila."' id='tablaEditarDocumento_".$rowTurnos['Turtur']."'>
						<tr>
							<td>".$rowTurnos['Turtdo']."-".$rowTurnos['Turdoc']."</td>
							<td align='right'><img class='botonEditarDocumento' style='cursor:pointer' tipoDocAnt='".$rowTurnos['Turtdo']."' docAnt='".$rowTurnos['Turdoc']."' tooltip='si' ondblclick='editarDocumento(this, \"".$rowTurnos['Turtur']."\")' title='Doble click para editar documento' src='../../images/medical/hce/mod.PNG'></td>
						</tr>
					</table>
				</td>
				<td>".utf8_encode($rowTurnos['Turnom'])."</td>
				<td>".utf8_encode($rowTurnos['Sernom'])."</td>
				".$resCitas."
				<td align='center'>".utf8_encode($rowTurnos['Secnom'])."</td>
				<td align='center'>".(($rowTurnos['Conpri'] == 'on') ? utf8_encode($rowTurnos['Connom']) : "")."</td>
				".(($infoCampAdi[$serCod]['htmlCampoAdi'] != '') ? "<td align='center' id='tdCampoAd".$rowTurnos['Turtur']."' idReg='".$rowTurnos['id']."' valorCamp='".$valorCampAd."' tooltip='si' title='".$infoCampAdi[$serCod]['tituloCampoAdi']."'>".$infoCampAdi[$serCod]['htmlCampoAdi']."</td>" : "<td align='center'></td>")."
				<td align='center' >
					<img id='imgLlamar".$rowTurnos['Turtur']."' 	style='cursor:pointer;' 				class='botonLlamarPaciente' width='20' heigth='20' tooltip='si' title='Llamar' 				src='../../images/medical/root/Call2.png'		onclick='llamarPacienteAtencion(\"".$rowTurnos['Turtur']."\")'>
					<img id='botonColgar".$rowTurnos['Turtur']."' 	style='cursor:pointer;display:none' 	class='botonColgarPaciente' width='20' heigth='20' tooltip='si' title='Cancelar llamado'  	src='../../images/medical/root/call3.png'		onclick='cancelarLlamarPacienteAtencion(\"".$rowTurnos['Turtur']."\")'>
					<img id='botonLlamando".$rowTurnos['Turtur']."' style='display:none' 					class='botonColgarPaciente' 																src='../../images/medical/ajax-loader1.gif'>
					<span id='enProcesoAtencion".$rowTurnos['Turtur']."' style='display:none;font-size:7pt;color:#E1017B' class='botonColgarPaciente'>Atenci&oacute;n en proceso...</span>
				</td>
				<td align='center' >
					<img id='botonIniciar".$rowTurnos['Turtur']."' 		style='cursor:pointer;display:none'  width='20' height='20' tooltip='si' title='Iniciar atenci&oacute;n' 	src='../../images/medical/sgc/play2.png' 	onclick='iniciarAtencion(\"".$rowTurnos['Turtur']."\")'>
					<img id='botonFinalizar".$rowTurnos['Turtur']."' 	style='cursor:pointer;display:none'  width='20' height='20' tooltip='si' title='Finalizar atenci&oacute;n ".(($infoCampAdi[$serCod]['enlazarConAdmision']) ? "e iniciar admisi&oacute;n" : "")."' src='../../images/medical/sgc/stop2.png' onclick='finalizarAtencion(\"".$rowTurnos['Turtur']."\", \"".$infoCampAdi[$serCod]['enlazarConAdmision']."\", \"".$rowTurnos['Turtdo']."\", \"".$rowTurnos['Turdoc']."\")'>
				</td>
				<td align='center' >
					<img id='botonCancelar".$rowTurnos['Turtur']."' style='cursor:pointer;' tooltip='si' 	class='botonCancelarTurno' title='Cancelar turno' src='../../images/medical/eliminar1.png' onclick='cancelarTurno(\"".$rowTurnos['Turtur']."\")'>
				</td>".$redireccion."
			</tr>
			";
			
			$respuesta['cantidad']++;
		}
		if(mysql_num_rows($resTurnos) == 0)
			if(!$conServicio)
				$respuesta['html'].= "<tr><td colspan='12' align='center' class='fila2' style='color:red'><b>¡Primero debe seleccionar el servicio y el cub&iacute;culo!</b></td></tr>";
			else
				$respuesta['html'].= "<tr><td colspan='12' align='center' class='fila2'>Sin registros</td></tr>";
		
		$respuesta['html'].= "
		</table>
		<input type='hidden' id='turnoLlamadoPorEsteUsuario' 	value='".$turnoConLlamadoEnVentanilla."'>
		<input type='hidden' id='turnoEnProcesoAtencion' 		value='".$turnoEnProcesoAtencion."'>
		<br>
		</div>
		";
		
		return $respuesta;
	}

	//---------------------------------------------------------
	// --> Pintar lista de pacientes atendidos
	//---------------------------------------------------------
	function listaDePacientesAtendidos($fechaAtencion, $servicio)
	{
		global $wbasedato;
		global $wbasmovhos;
		global $wemp_pmla;
		global $conex;
		global $wuse;
		global $tema;

		
		$respuesta = array("html" => "");
			
		
		if($servicio == "" || $servicio == "null")
		{
			$servicio 		= array("");
			$conServicio 	= false; 
		}
		else
		{
			$servicio 		= json_decode(str_replace('\\', '', $servicio));
			$conServicio 	= true;
		}
		
		$inServicios	= "";
		foreach($servicio as $codS)
			$inServicios.= (($inServicios == "") ? "'".$codS."'" : ", '".$codS."'");


		// --> Seleccionar unidades con busqueda para citas en 'on'		
		$SelCitas    = 0;
		
		$sqlCamposSer = "
			SELECT Sercod, Sercan, Sercat, Seradm, Serbus  
			  FROM ".$wbasedato."_000298
			 WHERE Sertem = '".$tema."'
			   AND Sercod IN(".$inServicios.")
			 ORDER BY Serbus Desc 
		";
		$resCamposSer = mysql_query($sqlCamposSer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCamposSer):</b><br>".mysql_error());		
		while($rowCamposSer = mysql_fetch_array($resCamposSer))
		{
			if($rowCamposSer['Serbus'] == 'on')
               $SelCitas = 1;
        }

        if ($SelCitas == 1)
			$campoCita = "<td class='encabezadoTabla' rowspan='2'>Información Citas</td>";
		else
			$campoCita = "";

        $respuesta['html'] = "
		<div style=''>
		<table id='tablaListaAtendidos' style='width:100%' align='center'>
			<tr align='center'>
				<td class='encabezadoTabla' rowspan='2'>Turno</td>
				<td class='encabezadoTabla' rowspan='2'>Documento</td>
				<td class='encabezadoTabla' rowspan='2'>Nombre</td>
				<td class='encabezadoTabla' rowspan='2'>Servicio</td>
				".$campoCita."
				<td class='encabezadoTabla' rowspan='2'>Sub-servicio</td>
				<td class='encabezadoTabla' rowspan='2'>Prioritario</td>
				<td class='encabezadoTabla' rowspan='2'>Tom&oacute; turno</td>
				<td class='encabezadoTabla' colspan='3'>Atendido</td>
				<td class='encabezadoTabla' rowspan='2'>Tiempo de espera</td>
			</tr>
			<tr align='center' rowspan='2'>
				<td class='encabezadoTabla'>Hora</td>
				<td class='encabezadoTabla'>Cub&iacute;culo</td>
				<td class='encabezadoTabla'>Usuario</td>
			</tr>";


		
		// --> Obtener lista de pacientes que ya se han atendido
		$sqlTurnos = "
		SELECT Turtur, Turdoc, Turtdo, Turnom, A.Fecha_data, A.Hora_data, Turhpa, Sernom, Connom, Puenom, Descripcion, Secnom 
		  FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000298 AS B ON(Turtem = Sertem AND Turtse = Sercod)
				INNER JOIN ".$wbasedato."_000299 AS C ON(Turupr = Concod)
				INNER JOIN ".$wbasedato."_000301 AS D ON(Turtem = Puetem AND Turven = Puecod)
				INNER JOIN usuarios ON(Turull = Codigo)
				LEFT  JOIN ".$wbasedato."_000309 AS E ON(Tursec = Seccod) 
		 WHERE Turtem = '".$tema."' 
		   AND A.Fecha_data  = '".$fechaAtencion."'
		   AND Turest = 'on'
		   AND Turate = 'on'
		   AND Turtse IN (".$inServicios.")			   			   
		 ORDER BY A.id ASC
		";
		
		$respuesta['sqlTurnos'] = $sqlTurnos;
		
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
		$coloFila	= 'fila2';		
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{

			if ($SelCitas == 1)
				$resCitas = consultarInformacioncitas($conex,$rowTurnos['Turdoc'],$rowTurnos['Fecha_data'],$wbasedato,$wbasmovhos);
			else
				$resCitas = "";

			$fhTomoTurno	= new DateTime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']);
			$fechaAtencion	= new DateTime($rowTurnos['Turhpa']);			
			$coloFila 		= (($coloFila == 'fila2') ? 'fila1' : 'fila2');
			$tiempoEspera 	= strtotime($rowTurnos['Turhpa'])-strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']);
			$turno 			= substr($rowTurnos['Turtur'], 7);
			$turno 			= substr($turno, 0, 2)." ".substr($turno, 2, 5);
			
			$respuesta['html'].= "
			<tr class='".$coloFila." find' id='trTurno_".$rowTurnos['Turtur']."'>			
				<td align='center'><b>".$turno."</b></td>
				<td>".$rowTurnos['Turtdo']."-".$rowTurnos['Turdoc']."</td>
				<td nowrap>".utf8_encode($rowTurnos['Turnom'])."</td>
				<td>".utf8_encode(strtoupper($rowTurnos['Sernom']))."</td>
				".$resCitas."
				<td>".utf8_encode(strtoupper($rowTurnos['Secnom']))."</td>
				<td>".utf8_encode(strtoupper($rowTurnos['Connom']))."</td>
				<td align='center'>".$fhTomoTurno->format('h:i:s a')."</td>
				<td align='center'>".$fechaAtencion->format('h:i:s a')."</td>				
				<td align='center'>".$rowTurnos['Puenom']."</td>				
				<td align='center'>".$rowTurnos['Descripcion']."</td>	
				<td align='center'>".gmdate("H:i:s", $tiempoEspera)."</td>
			</tr>
			";
		}
		
		if(mysql_num_rows($resTurnos) == 0)
			$respuesta['html'].= "<tr><td colspan='10' align='center' class='fila2'>Sin registros</td></tr>";
		
		$respuesta['html'].= "
		</table>
		</div>
		";
		
		return $respuesta;
	}

	
	//-----------------------------------------------------------------------------------
	// --> Pintar lista de turnos cancelados
	//-----------------------------------------------------------------------------------
	function listaDePacientesCancelados($fecha, $servicio)
	{
		global $wbasedato;
		global $wemp_pmla;
		global $conex;
		global $wuse;
		global $tema;
		
		$respuesta = array("html" => "");
			
		$respuesta['html'] = "
		<div style=''>
		<table id='tablaListaCancelados' style='width:100%' align='center'>
			<tr align='center'>
				<td class='encabezadoTabla' rowspan='2'>Turno</td>
				<td class='encabezadoTabla' rowspan='2'>Documento</td>
				<td class='encabezadoTabla' rowspan='2'>Nombre</td>
				<td class='encabezadoTabla' rowspan='2'>Servicio</td>
				<td class='encabezadoTabla' rowspan='2'>Prioritario</td>
				<td class='encabezadoTabla' rowspan='2'>Tom&oacute; turno</td>
				<td class='encabezadoTabla' colspan='2'>Cancelado</td>
				<td class='encabezadoTabla' rowspan='2'>Reactivar</td>
			</tr>
			<tr align='center' rowspan='2'>
				<td class='encabezadoTabla'>Hora</td>
				<td class='encabezadoTabla'>Usuario</td>
			</tr>";
		
		if($servicio == "" || $servicio == "null")
		{
			$servicio 		= array("");
			$conServicio 	= false; 
		}
		else
		{
			$servicio 		= json_decode(str_replace('\\', '', $servicio));
			$conServicio 	= true;
		}
		
		$inServicios	= "";
		foreach($servicio as $codS)
			$inServicios.= (($inServicios == "") ? "'".$codS."'" : ", '".$codS."'");
		
		// --> Obtener lista de pacientes que ya se han atendido
		$sqlTurnos = "
		SELECT Turtur, Turdoc, Turtdo, Turnom, A.Fecha_data, A.Hora_data, Sernom, Connom 
		  FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000298 AS B ON(Turtem = Sertem AND Turtse = Sercod)
				INNER JOIN ".$wbasedato."_000299 AS C ON(Turupr = Concod)
		 WHERE Turtem = '".$tema."' 
		   AND A.Fecha_data  = '".$fecha."'
		   AND Turest != 'on'
		   AND Turtse IN(".$inServicios.")	   
		 ORDER BY A.id ASC
		";
		$respuesta['sql'] = $sqlTurnos;
		
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
		$coloFila	= 'fila2';		
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			$fhTomoTurno	= new DateTime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']);			
			$coloFila 		= (($coloFila == 'fila2') ? 'fila1' : 'fila2');
			$turno 			= substr($rowTurnos['Turtur'], 7);
			$turno 			= substr($turno, 0, 2)." ".substr($turno, 2, 5);
			
			// --> Consultar en el log hora y usuario que canceló
			$sqlCancelo = "
			SELECT Fecha_data, Hora_data, Descripcion
			  FROM ".$wbasedato."_000303 INNER JOIN usuarios ON(Logusu = Codigo)
			 WHERE Logtem = '".$tema."' 
			   AND Logtur = '".$rowTurnos['Turtur']."'
			   AND Logacc = 'turnoCancelado' 
			 ORDER BY id DESC
			 LIMIT 1
			";
			$resCancelo = mysql_query($sqlCancelo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelo):</b><br>".mysql_error());
			if($rowCancelo = mysql_fetch_array($resCancelo))
			{
				$usuCancelo 	= $rowCancelo['Descripcion'];
				$horaCancelo 	= new DateTime($rowCancelo['Fecha_data']." ".$rowCancelo['Hora_data']);
			}	
			
			$respuesta['html'].= "
			<tr class='".$coloFila." find' id='trTurno_".$rowTurnos['Turtur']."'>			
				<td align='center'><b>".$turno."</b></td>
				<td>".$rowTurnos['Turtdo']."-".$rowTurnos['Turdoc']."</td>
				<td nowrap>".utf8_encode($rowTurnos['Turnom'])."</td>
				<td>".utf8_encode(strtoupper($rowTurnos['Sernom']))."</td>
				<td>".utf8_encode(strtoupper($rowTurnos['Connom']))."</td>
				<td align='center'>".$fhTomoTurno->format('h:i:s a')."</td>			
				<td align='center'>".$horaCancelo->format('h:i:s a')."</td>				
				<td align='center'>".$usuCancelo."</td>	
				<td align='center'><img style='cursor:pointer;' width='18' height='18' tooltip='si' title='Reactivar turno' src='../../images/medical/root/grabar.png' onclick='reactivarPaciente(\"".$rowTurnos['Turtur']."\", \"".$turno."\")'></td>
			</tr>
			";
		}
		
		if(mysql_num_rows($resTurnos) == 0)
			$respuesta['html'].= "<tr><td colspan='10' align='center' class='fila2'>Sin registros</td></tr>";
		
		$respuesta['html'].= "
		</table>
		</div>
		";
		
		return $respuesta;		
	}

	//-----------------------------------------------------------------------------------
	// --> Seleccionar lista de citas asignadas
	//-----------------------------------------------------------------------------------
	function consultarInformacioncitas($conex,$cedula,$wfechacon,$wcliame,$wmovhos)
	{
          //Recorrer en la tabla de servicios consultando código del centro de costos 
          //para buscar el prefijo e iniciar la búsqueda
          
          $restem    = "";
          $arrCitas  = array();
          $resultado = "<td><center>";
          $buscitas  = 0;

          $sqlPrefijo = "SELECT Sercod,Sercdc
                         FROM  ".$wcliame."_000298
                        WHERE    Serbus  = 'on' 
                          AND    Sercdc != ''
                          AND    Serest  = 'on' 
                        GROUP BY Sercdc";

          $resPrefijo = mysqli_query($conex, $sqlPrefijo) or die("<b>ERROR EN QUERY MATRIX():</b><br>".mysqli_error());

          while( $rowPre = mysqli_fetch_assoc($resPrefijo) )
          {
                //buscar centro de costos
                $sqlCentrocos =  " SELECT Ccocod,Cconom,Ccocip,Ccococ  
                                  FROM ".$wmovhos."_000011                        
                                  WHERE Ccocod = '".$rowPre['Sercdc']."'
                                    AND Ccocip !='' ";

                $resCentro  =  mysqli_query($conex,$sqlCentrocos) or die ("Error: en el query:  - ".mysqli_error());
              
                if( $rowCentro = mysqli_fetch_assoc($resCentro) )
                {

                    $prefijo = $rowCentro['Ccocip'];
                    $nomcen  = $rowCentro['Cconom'];
                    $tabla   = $rowCentro['Ccococ'];

                    $resTabexiste =  mysqli_query($conex,"SHOW TABLES LIKE '".$prefijo."_".$tabla."'");

                    if( $resTabexiste && $resTabexiste->num_rows>0)
                    {

	                    //Busco las citas
	                    //buscar centro de costos
	                    $sqlCitas =  " SELECT P9.Cedula,P9.Nom_pac,P9.Hi,P9.Cod_equ,P10.Descripcion
	                                      FROM ".$prefijo."_".$tabla." P9  
	                                      INNER JOIN ".$prefijo."_000010 P10
	                                         ON P9.Cod_equ = P10.Codigo                   
	                                      WHERE P9.Cedula = '".$cedula."'
	                                        AND P9.Activo = 'A'
	                                        AND P9.Fecha  ='".$wfechacon."'
	                                      GROUP BY P9.Fecha,P9.Hi  ";

	                    $resCitas  =  mysqli_query($conex,$sqlCitas) or die ("Error: en el query:  - ".mysqli_error());

	                    if( $resCitas && $resCitas->num_rows>0)
	                    {
	     
	                        while($rowCitas = mysqli_fetch_assoc($resCitas))
	                        {                           
	                            
	                            $restem = "<span id='spProfesional' data-html='true' title='Profesional:"."\n".strtoupper($rowCitas["Descripcion"])."'>".substr($rowCitas['Hi'],0,2).":".substr($rowCitas['Hi'],2,2)." - ".$nomcen."<span>";

	                            $arrCitas[$rowCitas['Hi']] = $restem;

	                        }
	                    }
                    }

                }
          
          }
          
          //Reordenar array resultado
          asort($arrCitas);

          foreach( $arrCitas as $cod=>$nom )
          {
	             if ($buscitas == 1)        
	                 $resultado .= "<hr style='border-top: 0px'>";

	             $resultado .= $nom;

	             $buscitas   = 1;
          }

          $resultado .= "</center></td>";

          if ($buscitas == 0)
              $resultado ='<td><center>Sin cita</center></td>';

          return $resultado;
	}

	//---------------------------------------------------------
	// --> Realizar un registro en el log de movimientos
	//---------------------------------------------------------	
	function guardarLog($turno, $accion, $tema)
	{
		global $wbasedato;
		global $conex;
		global $wuse;
		
		$sqlRegLLamado = "
		INSERT INTO ".$wbasedato."_000303 (Medico,				Fecha_data,				Hora_data,				Logtem,			Logtur,			Logacc,			Logusu,			Seguridad,			id)
									VALUES('".$wbasedato."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$tema."',	'".$turno."', 	'".$accion."',	'".$wuse."', 	'C-".$wbasedato."',	NULL)
		";
		mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());
	}

	//------------------------------------------------------------------------------------
	//	--> Genera el html del tiquete del turno
	//------------------------------------------------------------------------------------
	function htmlTurno($turno, $tipDocumento, $numDocumento, $nombrePaciente, $reimpresion, $nomServicio, $nomTema, $nomServicioSecundario, $desPiso)
	{
		$turno = substr($turno, 7);
		$turno = substr($turno, 0, 2)." ".substr($turno, 2, 5);  

		if ($desPiso !== '')
		{
			$nomPiso = "<tr><td align='right' style='font-size:2rem;'><b>".$desPiso."</b></td></tr>";
		}
		else
		{
			$nomPiso = "";
		}
			
		$html = "
		<table style='font-family: verdana;font-size:1rem;'>
			<tr>
				<td colspan='2' align='center'>
					<img width='118' heigth='58' src='../../images/medical/root/logoClinicaGrande.png'>
					<br>
					".utf8_encode($nomTema)."
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
					Es un placer servirle
					<br><br>
				</td>
			</tr>
			<tr>
				<td >Turno:&nbsp;&nbsp;</td>
				<td align='right' style='font-size:2rem;'><b>".$turno."</b></td></tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>".$tipDocumento."&nbsp;&nbsp;&nbsp;".$numDocumento."</td>
			</tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>".ucwords(strtolower($nombrePaciente))."</td>
			</tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>Servicio: ".ucwords(strtolower($nomServicio))." ".ucwords(strtolower($nomServicioSecundario))."</td>
			</tr>
			".$nomPiso."
			<tr>
				<td colspan='2' align='center' style='font-size:0.8rem'>
					<br><b>Por favor conserve este tiquete hasta que sea atendido.</b>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center' style='font-size:0.7rem'>
					".(($reimpresion) ? "<b>(Reimpresi&oacute;n)</b>" : "")." Fecha: ".date('Y-m-d')." &nbsp;Hora: ".date('g:i:s a')."
				</td>
			</tr>
		</table>";
		
		return $html;
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

			// --> Validar que el paciente no este siendo llamado en este momento
			$sqlValLla = "
			SELECT Descripcion
			  FROM ".$wbasedato."_000304, usuarios
			 WHERE Turtem = '".$tema."'
			   AND Turtur = '".$turno."'
			   AND (Turllv = 'on' OR Turpat = 'on' OR Turate = 'on')
			   AND Turull != '".$wuse."'
			   AND Turull = Codigo
			   AND Turest = 'on'
			";
			$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
			if($rowValLla = mysql_fetch_array($resValLla))
			{
				$respuesta['Error'] 	= TRUE;
				$respuesta['Mensaje'] 	= "El turno ya est&aacute; siendo llamado por:<br>".utf8_encode($rowValLla['Descripcion']);
			}
			else
			{
				// --> Validar que el usuario no esté llamando a otro turno en el mismo momento
				$sqlValLla2 = "
				SELECT Turtur
				  FROM ".$wbasedato."_000304 
				 WHERE Turtem = '".$tema."' 
				   AND Fecha_data = '".date("Y-m-d")."'
				   AND (Turllv = 'on' OR Turpat = 'on')
				   AND Turull = '".$wuse."'
				   AND Turest = 'on'
				";
				$resValLla2 = mysql_query($sqlValLla2, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla2):</b><br>".mysql_error());
				if($rowValLla2 = mysql_fetch_array($resValLla2))
				{
					$turno 		= substr($rowValLla2['Turtur'], 7);
					$turno 		= substr($turno, 0, 2)." ".substr($turno, 2, 5);
					$respuesta['Error'] 	= TRUE;
					$respuesta['Mensaje'] 	= "Para poder llamar a otro turno primero debe terminar el<br>proceso de atenci&oacute;n con el turno: <b>".$turno."</b>";
				}
				else
				{
					// --> realizar el llamado
					$sqlLlamar = "
					UPDATE ".$wbasedato."_000304
					   SET Turllv = 'on',
						   Turhll = '".date('Y-m-d')." ".date("H:i:s")."',
						   Turull = '".$wuse."',
						   Turven = '".$puestoTrabajo."'
					 WHERE Turtem = '".$tema."' 
					   AND Turtur = '".$turno."'
					";
					mysql_query($sqlLlamar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLlamar):</b><br>".mysql_error());

					// --> Registrar en el log el llamado
					guardarLog($turno, "llamadoVentanilla", $tema);
				}
			}

			echo json_encode($respuesta);
			return;
		}		
		case 'cancelarLlamarPacienteAtencion':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');
			
			// --> Cancelar el turno
			$sqlCancelar = "
			UPDATE ".$wbasedato."_000304
			   SET Turllv = 'off',
				   Turhll = '0000-00-00 00:0:00',
				   Turull = '',
				   Turven = ''
			 WHERE Turtem = '".$tema."' 
			   AND Turtur = '".$turno."'
			";
			$resCancelar = mysql_query($sqlCancelar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelar):</b><br>".mysql_error());

			// --> Registrar en el log la cancelacion del llamado
			guardarLog($turno, "llamadoCancelado", $tema);			

			echo json_encode($respuesta);
			return;
		}
		case 'cancelarTurno':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');
			
			// --> Validar que el paciente no haya sido llamado 
			$sqlValLla = "
			SELECT Descripcion
			  FROM ".$wbasedato."_000304, usuarios
			 WHERE Turtem = '".$tema."' 
			   AND Turtur = '".$turno."'
			   AND (Turllv = 'on' OR Turpat = 'on' OR Turate = 'on')
			   AND Turull = Codigo
			";
			$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
			if($rowValLla = mysql_fetch_array($resValLla))
			{
				$respuesta['Error'] 	= TRUE;
				$respuesta['Mensaje'] 	= "No se puede cancelar el turno, porque ya est&aacute; siendo atendido por ".$rowValLla['Descripcion'];
			}
			else
			{
				// --> Inactivar el turno
				$sqlTur = "
				UPDATE ".$wbasedato."_000304
				   SET Turest = 'off',
					   Turllv = 'off'
				 WHERE Turtem = '".$tema."' 
				   AND Turtur = '".$turno."'
				";
				mysql_query($sqlTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTur):</b><br>".mysql_error());

				// --> Registrar en el log la cancelacion del turno.
				guardarLog($turno, "turnoCancelado", $tema);	
			}

			echo json_encode($respuesta);
			return;
		}
		case 'iniciarAtencion':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');			
			
			// --> Marcar el inicio de la atencion
			$sqlTur = "
			UPDATE ".$wbasedato."_000304
			   SET Turllv = 'off',
			       Turpat = 'on',
				   Turate = 'off',
				   Turhpa = '".date('Y-m-d')." ".date("H:i:s")."'
			 WHERE Turtem = '".$tema."' 
			   AND Turtur = '".$turno."'
			";
			mysql_query($sqlTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTur):</b><br>".mysql_error());

			// --> Registrar en el log la cancelacion del turno.
			guardarLog($turno, "inicioAtencion", $tema);	

			echo json_encode($respuesta);
			return;
		}
		case 'finalizarAtencion':
		{
			$respuesta = array('Error' => FALSE, 'Mensaje' => '');			
			
			// --> Marcar el fin de la atencion
			$sqlTur = "
			UPDATE ".$wbasedato."_000304
			   SET Turpat = 'off',
			       Turate = 'on',
				   Turhfa = '".date('Y-m-d')." ".date("H:i:s")."'
			 WHERE Turtem = '".$tema."' 
			   AND Turtur = '".$turno."'
			";
			mysql_query($sqlTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTur):</b><br>".mysql_error());

			// --> Registrar en el log la cancelacion del turno.
			guardarLog($turno, "finAtencion", $tema);	

			echo json_encode($respuesta);
			return;
		}		
		case 'listaPacientesPendientesAtencion':
		{			
			$respuesta = listaDePacientesEnEspera($servicio, $subServicio);
			
			echo json_encode($respuesta);
			return;
		}
		case 'listaDePacientesAtendidos':
		{
			$respuesta = array("Error" => false, "Html" => "");
			
			$respuesta = listaDePacientesAtendidos($fechaAtencion, $servicio);
			
			echo json_encode($respuesta);
			return;
		}
		case 'listaDePacientesCancelados':
		{
			$respuesta = array("Error" => false, "html" => "");
			
			$respuesta = listaDePacientesCancelados($fecha, $servicio);
			
			echo json_encode($respuesta);
			return;
		}
		case 'reactivarPaciente':
		{
			$actualizarTurno = "
			UPDATE ".$wbasedato."_000304
			   SET Turest = 'on'
			 WHERE Turtem = '".$tema."' 
			   AND Turtur = '".$turno."'
			";
			mysql_query($actualizarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarTurno):</b><br>".mysql_error());
			
			// --> Guardar log de reactivacion.
			guardarLog($turno, "turnoReactivado", $tema);	
			
			return;
		}
		case 'actualizarNumeroDeDocumento':
		{
			$actualizarDocTurno = "
			UPDATE ".$wbasedato."_000304
			   SET Turtdo = '".$nuevoTipDoc."',
				   Turdoc = '".$nuevoDoc."' 	
			 WHERE Turtem = '".$tema."' 
			   AND Turtur = '".$turno."'
			";
			mysql_query($actualizarDocTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(actualizarDocTurno):</b><br>".mysql_error());
			
			// --> Guardar log de actualizacion.
			guardarLog($turno, "actualizacionDocumento(ANTERIOR:".$tipoDocAnt."-".$docAnt." NUEVO:".$nuevoTipDoc."-".$nuevoDoc.")", $tema);
			
			return;
		}
		// --> 	Actualiza el puesto de trabajo asociado a un usuario
		case 'cambiarPuestoTrabajo':
		{
			$usuario		= $wuse;
			$respuesta 		= array("Error" => FALSE, "Mensaje" => "");

			// --> Validar que el puesto de trabajo este disponible
			$sqlValPuesTra = "
			SELECT Descripcion
			  FROM ".$wbasedato."_000301, usuarios
			 WHERE Puetem = '".$tema."' 
			   AND Puecod = '".$puestoTrabajo."'
			   AND Pueusu != ''
			   AND Pueusu = Codigo
			";
			$resValPuesTra = mysql_query($sqlValPuesTra, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValPuesTra):</b><br>".mysql_error());
			if($respetarOcupacion == 'true' && $rowValPuesTra = mysql_fetch_array($resValPuesTra))
			{
				$respuesta["Error"] 	= TRUE;
				$respuesta["Mensaje"] 	= utf8_encode('Este cub&iacuteculo ya est&aacute ocupado por <br>'.$rowValPuesTra['Descripcion']);
			}
			else
			{
				// --> Quitar cualquier puesto de trabajo asociado al usuario
				$sqlUpdatePues = "
				UPDATE ".$wbasedato."_000301
				   SET Pueusu = '',
				       Pueser = '',
				       Puesse = ''
				 WHERE Pueusu = '".$usuario."'
				";
				mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());

				if($puestoTrabajo != '')
				{
					$subServicio = ($subServicio == 'null') ? "" : $subServicio;
					
					// --> Asignar el nuevo puesto de trabajo
					$sqlUpdatePues = "
					UPDATE ".$wbasedato."_000301
					   SET Pueusu = '".$usuario."',
				           Pueser = '".$servicio."',
				           Puesse = '".$subServicio."'
					 WHERE Puetem = '".$tema."' 
					   AND Puecod = '".$puestoTrabajo."'
					";
					mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());
					
					// --> Guardar log del cambio de puesto
					$sqlLog = "
					INSERT INTO ".$wbasedato."_000300
					        SET Medico 		= 'cliame',
						        Fecha_data 	= '".date("Y-m-d")."',
						        Hora_data 	= '".date("H:i:s")."',
								Logtem 		= '".$tema."',
								Logusu 		= '".$usuario."',
								Logpue 		= '".$puestoTrabajo."',
								Logser 		= '".$servicio."',
								Seguridad	= 'C-".$wuse."'
					";
					mysql_query($sqlLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLog):</b><br>".mysql_error());
				}
			}

			echo json_encode($respuesta);
			return;
		}
		case 'cambiarSubServicio':
		{
			$subServicio = ($subServicio == 'null') ? "" : $subServicio;
					
			// --> Guardar los subservicios seleccionados
			$sqlUpdatePues = "
			UPDATE ".$wbasedato."_000301
			   SET Puesse = '".$subServicio."'
			 WHERE Puetem = '".$tema."' 
			   AND Puecod = '".$puestoTrabajo."'
			";
			mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());
					
			return;
			break;
		}
		case 'guardarCampoAdi':
		{
			$valor = str_replace('\\', '', $_POST['valor']);
			
			$sqlCampoAd = "
			UPDATE ".$wbasedato."_000304
			   SET Turvca = '".$valor."'
			 WHERE id = '".$id."'			   
			";
			$res = mysql_query($sqlCampoAd, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCampoAd):</b><br>".mysql_error());
			$respuesta["msj"] = $res;
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'cubiculosAsociadosAlServicio':
		{
			$usuario		= $wuse;
			
			if( $servicio == "" || $servicio == "null" )
			{
				$servicio 		= array("");
				$conServicio 	= false; 
			}
			else
			{
				$servicio 		= json_decode(str_replace('\\', '', $servicio));
				$conServicio 	= true;
			}
			
			$inServicios	= "";
			foreach($servicio as $codS)
				$inServicios.= (($inServicios == "") ? "'".$codS."'" : ", '".$codS."'");
						
			$tieneSubSer 	= 'off';
			$selectSubSer	= "";			
			$sqlSubSer 		= "
			SELECT Seccod, Secnom
			  FROM ".$wbasedato."_000310 INNER JOIN ".$wbasedato."_000309 ON(Rsssec = Seccod)
			 WHERE Rsstem = '".$tema."' 
			   AND Rssser IN(".$inServicios.")
			   AND Rssest = 'on'
			";
			$resSubSer = mysql_query($sqlSubSer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSubSer):".$sqlSubSer."</b><br>".mysql_error());
			while($rowSubSer = mysql_fetch_array($resSubSer))
			{
				$tieneSubSer 	= 'on';
				$selectSubSer.="<option value='".$rowSubSer['Seccod']."'>".$rowSubSer['Secnom']."</option>";	
			}
			$respuesta["tieneSubSer"]  = $tieneSubSer;
			$respuesta["selectSubSer"] = $selectSubSer;
			
			$selectCub		= "<option value=''>Seleccione..</option>";			
			$sqlCubiculos 	= "
			SELECT Puecod, Puenom
			  FROM ".$wbasedato."_000302 INNER JOIN ".$wbasedato."_000301 ON(Rsptem = Puetem AND Rsppue = Puecod)
			 WHERE Rsptem = '".$tema."' 
			   AND Rspser IN(".$inServicios.")
			   AND Pueest = 'on'
			   AND Puecon = 'off'
			 GROUP BY Puecod		   
			";
			$resCubiculos = mysql_query($sqlCubiculos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCubiculos):</b><br>".mysql_error());
			while($rowCubiculos =mysql_fetch_array($resCubiculos))
			{
				$selectCub.="<option value='".$rowCubiculos['Puecod']."'>".$rowCubiculos['Puenom']."</option>";	
			}
			$respuesta["selectCub"] = $selectCub;
			
			// --> Quitar cualquier puesto de trabajo asociado al usuario
			$sqlUpdatePues = "
			UPDATE ".$wbasedato."_000301
			   SET Pueusu = '',
				   Pueser = ''
			 WHERE Pueusu = '".$usuario."'
			";
			mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());
			
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
	  <title>Sala de atenci&oacute;n PAP</title>
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
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		
		<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
		<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
		<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
		<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
	
	<script type="text/javascript">
//=====================================================================================================================================  
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================
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
			$(".botonEditarDocumento").hide();
			$("#imgLlamar"+idTurno).hide();
			$("#botonColgar"+idTurno).show();
			$("#botonLlamando"+idTurno).show();
			$("#botonIniciar"+idTurno).show();
		}
		
		// --> Si hay un turno que se está en proceso de atencion
		if($("#turnoEnProcesoAtencion").val() != '')
		{
			var idTurno = $("#turnoEnProcesoAtencion").val();
			
			$("#botonFinalizar"+idTurno).show();
			$("#botonFinalizar"+idTurno).parent().parent().attr("class", "fondoAmarillo");
			$("#botonFinalizar"+idTurno).parent().parent().find("table").attr("class", "fondoAmarillo");
			$("#tdCampoAd"+idTurno).children().show();
			$("#botonLlamando"+idTurno).show();
			$("#enProcesoAtencion"+idTurno).show();
			$("#botonIniciar"+idTurno).hide();
			$("#botonColgar"+idTurno).hide();
			$(".botonLlamarPaciente").hide();
			$(".botonCancelarTurno").hide();
			$(".botonEditarDocumento").hide();
		}
		
		// --> Buscadores
		$('#buscarPacientesEnEspera').quicksearch('#tablaListaEsperaAtencion .find');
		$('#buscarPacientesAtendidos').quicksearch('#tablaListaAtendidos .find');
		
		// --> Tooltip
		$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		
		activarRelojTemporizador();
		
		// --> Parametrización del datapicker
		cargar_elementos_datapicker();
		// --> Activar datapicker
		$("#fechaAtencion").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				listaDePacientesAtendidos();
			}
		});
		$("#fechaAtencion").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
		$("#fechaAtencion").after(" ");
		
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
		
		listaPacientesPendientesAtencion();
		
		// --> Si el usuario ya tenia un servicio y una ventanilla seleccionadas
		if($("#servicioActUsu").val() != '')
		{
			cambiarServicio();
			// setTimeout( function(){
				// subServicioActUsu = JSON.parse($("#subServicioActUsu").val());
				// for (i in subServicioActUsu)
				// {					
					// $("#ui-multiselect-selectSubServicio-option-"+subServicioActUsu[i]).attr("aria-selected", "true");
				// }
			// }, 1000);
			
			setTimeout( function(){
				$("#puestoTrabajo").val($("#ventanillaActUsu").val());
				cambiarPuestoTrabajo(true);
			}, 500);			
		}
		
		$('#selectServicio').multiselect({
			position: {
				my: 'left bottom',
				at: 'left top'
			},
			selectedText: "# Selec.",
			minWidth: 130,
			checkAllText: 'Todos',
			uncheckAllText: 'Ninguno',
			noneSelectedText: 'Seleccione',
			header: false
		});//.multiselectfilter();
		$('.ui-multiselect-checkboxes li').css({"font-size":"0.7em"});
		
		nombresSerSeleccionados();
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
	// --> Funcion que genera el llamado del paciente para que sea atendido 
	//------------------------------------------------------------------------------------
	function llamarPacienteAtencion(turno)
	{
		if($("#puestoTrabajo").val() == "")
		{
			jAlert("<span style='color:red'>Primero debe seleccionar su puesto de trabajo actual.</span>", "Mensaje");
			$("#puestoTrabajo").css("border-color", "red");
			return;
		}
		
		$.post("salaDeAtencionTurnero.php",
		{
				consultaAjax:   		'',
				accion:         		'llamarPacienteAtencion',
				wemp_pmla:        		$('#wemp_pmla').val(),
				turno:					turno,
				puestoTrabajo:			$("#puestoTrabajo").val(),
				tema:					$("#tema").val()			
		}, function(respuesta){
			if(respuesta.Error)
			{
				jAlert("<span style='color:red'>"+respuesta.Mensaje+"</span>", "Mensaje");
				$(".botonLlamarPaciente").show();
				$(".botonEditarDocumento").show();
				$(".botonColgarPaciente").hide();
				$("#botonIniciar"+turno).hide();
			}
			else
			{
				$(".botonLlamarPaciente").hide();
				$(".botonColgarPaciente").hide();
				$(".botonCancelarTurno").hide();
				$("#imgLlamar"+turno).hide();
				$("#botonColgar"+turno).show();
				$("#botonLlamando"+turno).show();
				$("#botonIniciar"+turno).show();
				
				$(".botonCancelarEdicionDoc").each(function(){
					$(this).trigger('click');
				});
				setTimeout( function(){
					$(".botonEditarDocumento").hide();
				}, 100);
			}
		}, 'json');
	}
	//-----------------------------------------------------------------------
	// --> Funcion que cancela el llamado del paciente 
	//-----------------------------------------------------------------------
	function cancelarLlamarPacienteAtencion(turno)
	{
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'cancelarLlamarPacienteAtencion',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno,
			tema:					$("#tema").val()
		}, function(respuesta){

			$(".botonLlamarPaciente").show();
			$(".botonCancelarTurno").show();
			$(".botonColgarPaciente").hide();
			$("#botonIniciar"+turno).hide();
			$(".botonEditarDocumento").show();
		});
	}
	//----------------------------------------------------
	// --> Funcion que cancela el turno de un paciente
	//----------------------------------------------------
	function cancelarTurno(turno)
	{
		jConfirm("<span style='color:red'>Est&aacute; seguro que desea cancelar el turno "+turno+" ?</span>", 'Confirmar', function(respuesta) {
			if(respuesta)
			{
				$.post("salaDeAtencionTurnero.php",
				{
					consultaAjax:   		'',
					accion:         		'cancelarTurno',
					wemp_pmla:        		$('#wemp_pmla').val(),
					turno:					turno,
					tema:					$("#tema").val()
				}, function(respuesta){
					if(respuesta.Error)
						jAlert("<span style='color:red'>"+respuesta.Mensaje+"</span>", "Mensaje");
					else
						$("#trTurno_"+turno).hide(500);
				}, 'json');
			}
		});
	}
	// --> Cerra la modal de escala de glasgow
	function cerrarModal()
	{
		$.unblockUI();
	}
	//----------------------------------------------------------------------
	// --> Iniciar la atencion del turno
	//----------------------------------------------------------------------
	function iniciarAtencion(turno)
	{
		if($("#puestoTrabajo").val() == "")
		{
			jAlert("<span style='color:red'>Primero debe seleccionar su puesto de trabajo actual.</span>", "Mensaje");
			$("#puestoTrabajo").css("border-color", "red");
			return;
		}				
		
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'iniciarAtencion',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno,
			tema:					$("#tema").val()
		}, function(respuesta){
			$("#botonFinalizar"+turno).parent().parent().attr("class", "fondoAmarillo");
			$("#botonFinalizar"+turno).parent().parent().find("table").attr("class", "fondoAmarillo");
			$("#botonFinalizar"+turno).show();
			$("#tdCampoAd"+turno).children().show();
			$("#enProcesoAtencion"+turno).show();			
			$("#botonIniciar"+turno).hide();
			$("#botonColgar"+turno).hide();
			$(".botonLlamarPaciente").hide();
			$(".botonCancelarTurno").hide();
			$(".botonEditarDocumento").hide();
		}, 'json');
	}
	//----------------------------------------------------------------------
	// --> Finaliza la atencion del turno
	//----------------------------------------------------------------------
	function finalizarAtencion(turno, enlazarConAdmision, tipDoc, doc)
	{		
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'finalizarAtencion',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno,
			tema:					$("#tema").val()
		}, function(respuesta){
			listaPacientesPendientesAtencion();
			
			if(enlazarConAdmision)
			{
				setTimeout( function(){
					ruta = "/matrix/admisiones/procesos/admision_erp.php?wemp_pmla="+$('#wemp_pmla').val()+"&TipoDocumentoPacAm="+tipDoc+"&DocumentoPacAm="+doc+"&AgendaMedica=on";
					window.open(ruta,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
				}, 500);
			}
			
		}, 'json');
	}	

	//----------------------------------------------------------------------
	// --> Abrir ventana modal redireccionar turno
	//----------------------------------------------------------------------
	function redireccionarTurno(turno,tipdoc,documento,nombre)
	{
	   $('#txtTipoDocu').val(tipdoc);
	   $('#txtDocumen').val(documento);
	   $('#txtNombre').val(nombre);
	   $('#txtTurno').val(turno);
       
       $("#selprioritario").prop("selectedIndex", 1);
       $('#modalRedireccionar').show();
	}

	//----------------------------------------------------------------------
	// --> Grabar turno reasignado a otro servicio
	//----------------------------------------------------------------------
	function grabarRedireccionarturno()
	{
		var turno = $("#txtTurno").val();		
 
        // --> Validar que seleccione el servicio
        if($("#seltiposervicio").val() == '' || $("#selprioritario").val() == '')
        {        	
        	jAlert("<span style='color:red'>Primero debe seleccionar el servicio y prioridad</span>", "Mensaje");
        	return 0;
        }

        //Validar que la fecha no sea de dias anteriores
        var today  = new Date();
    	var year   = today.getFullYear()

    	var fectur = year.toString().substr(-2)+''+("0" + (today.getMonth() + 1)).slice(-2)+''+pad(today.getDate(),2);
    	
        if(fectur !== turno.substr(0,6) )
        {
    	   jAlert("<span style='color:red'>No es posible redireccionar dias anteriores</span>", "Mensaje");
    	   return 0;
        }
        

		jConfirm("<span style='color:red'>Est&aacute; seguro que desea cancelar el turno "+turno+" ?</span>", 'Confirmar', function(respuesta) {
				if(respuesta)
				{
					$.post("salaDeAtencionTurnero.php",
					{
						consultaAjax:   '',
						accion:         'cancelarTurno',
						wemp_pmla:      $('#wemp_pmla').val(),
						turno:			turno,
						tema:			$("#tema").val()
					}, function(respuesta){
						if(respuesta.Error)
							jAlert("<span style='color:red'>"+respuesta.Mensaje+"</span>", "Mensaje");
						else
							//Grabar nuevo turno
							$.post("turneroLobby.php",
							{
								consultaAjax:   		'',
								async:  		        false,
								accion:         		'generarTurno',
								wemp_pmla:        		$('#wemp_pmla').val(),
								tipDocumento:			$('#txtTipoDocu').val(),
								numDocumento:			$('#txtDocumen').val(),			
								nombrePaciente:			$.trim($('#txtNombre').val()),
								tipoServicio:			$("#seltiposervicio").val(),
								servicioSecundario:		'',
								nomServicioSecundario:	'',
								usuarioPreferencial:	$("#selprioritario").val(),
								validarExisteTurno:		true,
								turnoACancelar:			$('#txtTurno').val(),
								codigoTurnero:			"",
								tema:					$("#seltiposervicio option:selected").attr('tema')
							}, function(data){
					           imprimirTurno(data.fichoTurno,$('#txtTurno').val());
					           $('#modalRedireccionar').hide();
							}, 'json');
                            
						    listaPacientesPendientesAtencion();
					}, 'json');
				}
			});

	}

	//----------------------------------------------------------------------
    // --> Imprimir tiquete de turno.
    //----------------------------------------------------------------------
	function imprimirTurno(fichoTurno,turnoACancelar)
	{

		setTimeout(function(){
			var contenido = "<html><body>";

			if (turnoACancelar != ''){
			    var res = fichoTurno.replace('Turno:','Turno Anterior: '+turnoACancelar+' - Actual: ');
			    fichoTurno = res;
			}
			
			contenido 	= contenido + fichoTurno + "</body></html>";
			$("#modalTurnocon").html(contenido);
			$("#modalTurno").show();
		}, 1000);
	}

	//----------------------------------------------------------------------
	// --> Pinta la lista de pacientes pendientes de atencion
	//----------------------------------------------------------------------
	function listaPacientesPendientesAtencion()
	{
		$("#puestoTrabajo").show();
		
		if($.trim($("#selectSubServicio").html()) != "")
			$("#contSubServicio").show();
		
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'listaPacientesPendientesAtencion',
			wemp_pmla:        		$('#wemp_pmla').val(),
			servicio:				JSON.stringify($("#selectServicio").val()),			
			subServicio:			JSON.stringify($("#selectSubServicio").val()),
			tema:					$("#tema").val(),
			redir:					$("#redir").val(),
			
		}, function(respuesta){
			$("#listaEsperaAtencion").html(respuesta.html);
						
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
				$("#botonIniciar"+idTurno).show();
			}
			
			// --> Si hay un turno que se está en proceso de atencion
			if($("#turnoEnProcesoAtencion").val() != '')
			{
				var idTurno = $("#turnoEnProcesoAtencion").val();
				
				$("#botonFinalizar"+idTurno).show();
				$("#botonFinalizar"+idTurno).parent().parent().attr("class", "fondoAmarillo");
				$("#botonFinalizar"+idTurno).parent().parent().find("table").attr("class", "fondoAmarillo");
				$("#tdCampoAd"+idTurno).children().show();
				$("#botonLlamando"+idTurno).show();
				$("#enProcesoAtencion"+idTurno).show();
				$("#botonIniciar"+idTurno).hide();
				$("#botonColgar"+idTurno).hide();
				$(".botonLlamarPaciente").hide();
				$(".botonCancelarTurno").hide();
				$(".botonEditarDocumento").hide();
			}
			
			$('#buscarPacientesEnEspera').quicksearch('#tablaListaEsperaAtencion .find');
			
			$("#listaEsperaAtencion input[tipoCampoAd]").each(function(){
				
				$(this).val($(this).parent().attr("valorCamp"));
				
				var tipoCam = $(this).attr("tipoCampoAd");
				
				switch(tipoCam)
				{
					case 'entero':
					{
						$(this).keyup(function(){
							if ($(this).val() !="")
								$(this).val($(this).val().replace(/[^0-9.]/g, ""));
						});
						break;
					}
					case 'fecha':
					{
						// --> Activar datapicker
						$(this).datepicker({
							showOn: "button",
							buttonImage: "../../images/medical/root/calendar.gif",
							buttonImageOnly: true,
							onSelect: function(){
								guardarCampoAdi($(this));
							}
						});
						$(this).next().css({"cursor": "pointer"}).attr("title", "Seleccione");
						$(this).after(" ");
						break;
					}
					case 'hora':
					{
						$(this).timepicker({
							showPeriodLabels: false,
							hourText: 'Hora',
							minuteText: 'Minuto',
							amPmText: ['AM', 'PM'],
							closeButtonText: 'Aceptar',
							nowButtonText: 'Ahora',
							deselectButtonText: 'Deseleccionar',
							defaultTime: 'now',
							onClose: function(){
								guardarCampoAdi($(this));
							}
						});
						break;
					}
				}
			});
			
		}, 'json');
	}
	//----------------------------------------------------------------------
	// --> Guardar el valor del campo adicional en la BD
	//----------------------------------------------------------------------
	function guardarCampoAdi(elemento)
	{
		if($(elemento).val() == "")
			return;	
		
		var arrValor 	= new Object();
		arrValor.titulo = $(elemento).attr("nombreCampoAd");
		arrValor.valor 	= $(elemento).val();
		
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'guardarCampoAdi',
			wemp_pmla:        		$('#wemp_pmla').val(),
			valor:        			JSON.stringify(arrValor),
			id:						$(elemento).parent().attr("idReg")
		}, function(respuesta){
		}, 'json');
		return;
	}
	//----------------------------------------------------------------------
	// --> Pinta la lista de pacientes ya atendidos
	//----------------------------------------------------------------------
	function listaDePacientesAtendidos()
	{
		$("#puestoTrabajo").hide();
		$("#contSubServicio").hide();
		
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'listaDePacientesAtendidos',
			wemp_pmla:        		$('#wemp_pmla').val(),
			fechaAtencion:			$("#fechaAtencion").val(),
			servicio:				JSON.stringify($("#selectServicio").val()),
			tema:					$("#tema").val()
		}, function(respuesta){
			$("#listaPacAtendidos").html(respuesta.html);
			// --> Tooltip
			$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			
			$('#buscarPacientesAtendidos').quicksearch('#tablaListaAtendidos .find');
			
			activarRelojTemporizador();
			
			// --> Blink
			clearInterval(blinkTiempEspera);
			
			blinkTiempEspera = setInterval(function(){
				$("span[blink]").each(function(){
					$(this).css('visibility' , $(this).css('visibility') === 'hidden' ? '' : 'hidden');
				});
			}, 500);
			
			if($("#tablaListaAtendidos").height() >= '500')
				$("#listaPacAtendidos").css({"height":"510px", "overflow":"auto", "background":"none repeat scroll 0 0"});
			
		}, 'json');
	}
	//-----------------------------------------------------------------------
	// --> 	Reloj temporizador
	//		Jerson Trujillo, 2016-01-13
	//-----------------------------------------------------------------------
	function activarRelojTemporizador()
	{		
		clearInterval(relojTemp);
		// --> Incializar contador en 60 segundos
		$("#relojTemp").text("01:00");
		$("#relojTemp").attr("cincoMinTem", "86460000");	
		
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
			{
				var tab = $("#tabsTriage").find("li[class*=ui-state-active]").attr("id");
				$("#tdBotonActualizar").html("");
				if(tab == "tab1")
					listaPacientesPendientesAtencion();
				if(tab == "tab2")
					listaDePacientesAtendidos();
				if(tab == "tab3")
					listaDePacientesCancelados();
			}
		}, 1000);

		var tab = $("#tabsTriage").find("li[class*=ui-state-active]").attr("id");
		$("#tdBotonActualizar").html("");
		if(tab == "tab1")
			$("#tdBotonActualizar").html('<span style="cursor:pointer" onclick="listaPacientesPendientesAtencion()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
		if(tab == "tab2")
			$("#tdBotonActualizar").html('<span style="cursor:pointer" onclick="listaDePacientesAtendidos()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
		if(tab == "tab3")
			$("#tdBotonActualizar").html('<span style="cursor:pointer" onclick="listaDePacientesCancelados()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
	}
	//-------------------------------------------------------------
	// --> Pintar lista de pacientes cancelados
	//-------------------------------------------------------------
	function listaDePacientesCancelados()
	{
		$("#puestoTrabajo").hide();
		$("#contSubServicio").hide();
		
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'listaDePacientesCancelados',
			wemp_pmla:        		$('#wemp_pmla').val(),
			fecha:					$("#fechaCancelo").val(),
			servicio:				JSON.stringify($("#selectServicio").val()),
			tema:					$("#tema").val()
		}, function(respuesta){
			$("#listaCancelados").html(respuesta.html);
			// --> Tooltip
			$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			activarRelojTemporizador();
			$('#buscarPacientesCancelados').quicksearch('#tablaListaCancelados .find');
		}, 'json');
	}
	//-------------------------------------------------------------
	// --> Reactivar un turno que estaba cancelado
	//-------------------------------------------------------------
	function reactivarPaciente(turno, turNom)
	{
		jConfirm("<span style='color:#2A5DB0'>Est&aacute; seguro en reactivar el turno "+turNom+"?</span>", 'Confirmar', function(respuesta) {
			if(respuesta)
			{
				$.post("salaDeAtencionTurnero.php",
				{
					consultaAjax:   		'',
					accion:         		'reactivarPaciente',
					wemp_pmla:        		$('#wemp_pmla').val(),
					turno:					turno,
					tema:					$("#tema").val()
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
			jConfirm("<span style='color:#2A5DB0'>Est&aacute; seguro de actualizar este documento?</span>", 'Confirmar', function(respuesta) {
				if(respuesta)
				{
					$.post("salaDeAtencionTurnero.php",
					{
						consultaAjax:   		'',
						accion:         		'actualizarNumeroDeDocumento',
						wemp_pmla:        		$('#wemp_pmla').val(),
						turno:					turno,
						nuevoTipDoc:			$.trim($("#actTipDoc_"+turno).val()),
						nuevoDoc:				$.trim($("#actNumDoc_"+turno).val()),
						tipoDocAnt:				tipoDocAnt,
						docAnt:					docAnt,
						tema:					$("#tema").val()
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
	
	function nombresSerSeleccionados()
	{
		serviciosSeleccionados = "";
		var cont = 0;
		$("[id^=ui-multiselect-selectServicio-option]").each(function(){
			if($(this).attr("aria-selected") == "true")
			{
				serviciosSeleccionados+= ((serviciosSeleccionados == "") ? "" : ", ")+$(this).next().html();
				// serviciosSeleccionados+= ((serviciosSeleccionados == "") ? "" : ", ")
				// serviciosSeleccionados+= (cont == 2) ? "<br>" : "";
				// cont 				   = ((cont == 2) ? 0 : cont+1);
				// serviciosSeleccionados+= $(this).next().html();
				// console.log(cont);
			}
		});
		$("#serSele").html(serviciosSeleccionados);
		
		subServiciosSeleccionados = "";
		$("[id^=ui-multiselect-selectSubServicio-option]").each(function(){
			if($(this).attr("aria-selected") == "true")
				subServiciosSeleccionados+= ((subServiciosSeleccionados == "") ? "" : ", ")+$(this).next().html();
		});
		$("#subSele").html(subServiciosSeleccionados);
		
		cubiculoSeleccionado = "";
		if($("#puestoTrabajo").val() != "")
			cubiculoSeleccionado = $("#puestoTrabajo option:selected").text();
		
		$("#cubSele").html(cubiculoSeleccionado);
	}
	
	//-------------------------------------------------------------
	// --> Seleccionar un nuevo servicio de trabajo
	//-------------------------------------------------------------
	function cambiarServicio()
	{
		if($("#selectServicio").val() == '' || $("#selectServicio").val() == undefined)
		{
			$("#selectServicio").css("border-color", "red");
			$("#selectSubServicio").css("border-color", "red");
			$("#puestoTrabajo").css("border-color", "red");
		}
		else
		{
			$("#selectServicio").css("border-color", "#AFAFAF");
			$("#selectSubServicio").val("");
			
			var tab = $("#tabsTriage").find("li[class*=ui-state-active]").attr("id");
			$("#tdBotonActualizar").html("");
			if(tab == "tab1")
				listaPacientesPendientesAtencion();
			if(tab == "tab2")
				listaDePacientesAtendidos();
			if(tab == "tab3")
				listaDePacientesCancelados();
			
		}

		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax	:	'',
			accion			:	'cubiculosAsociadosAlServicio',
			wemp_pmla		:	$('#wemp_pmla').val(),
			servicio		:	JSON.stringify($("#selectServicio").val()),
			tema			:	$("#tema").val()
		}, function(respuesta){
			$("#selectSubServicio").html(respuesta.selectSubSer);
			
			if($.trim(respuesta.tieneSubSer) == 'on')
				$("#contSubServicio").show();
			else
				$("#contSubServicio").hide();
			
			$("#puestoTrabajo").html(respuesta.selectCub);

			$('#selectSubServicio').multiselect("destroy");
			
			$('#selectSubServicio').multiselect({
				position: {
					my: 'left bottom',
					at: 'left top'
				},
				selectedText: "# Selec.",
				minWidth: 130,
				noneSelectedText: 'Seleccione',
				header: false
			});
			$('.ui-multiselect-checkboxes li').css({"font-size":"0.6em"});
			
			nombresSerSeleccionados();
		
		}, 'json');
	}
	//-------------------------------------------------------------
	// --> 
	//-------------------------------------------------------------
	function cambiarSubServicio()
	{
		$("#selectSubServicio").css("border-color", "#AFAFAF");
		
		$("#tdBotonActualizar").html("");
		listaPacientesPendientesAtencion();
		
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'cambiarSubServicio',
			wemp_pmla:        		$('#wemp_pmla').val(),
			subServicio:			JSON.stringify($("#selectSubServicio").val()),
			puestoTrabajo:			$("#puestoTrabajo").val(),
			tema:					$("#tema").val()
		}, function(respuesta){
		});
		
		nombresSerSeleccionados();
	}
	//-------------------------------------------------------------
	// --> convertir a string adicionando '0' a la izquierda
	//-------------------------------------------------------------
	function pad(str, max) 
	{
	  str = str.toString();
	  return str.length < max ? pad("0" + str, max) : str;
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
		
		$.post("salaDeAtencionTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'cambiarPuestoTrabajo',
			wemp_pmla:        		$('#wemp_pmla').val(),
			puestoTrabajo:			$("#puestoTrabajo").val(),
			servicio:				JSON.stringify($("#selectServicio").val()),
			subServicio:			JSON.stringify($("#selectSubServicio").val()),
			respetarOcupacion:		respetarOcupacion,
			tema:					$("#tema").val()
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
			else
			{
				nombresSerSeleccionados();
				$("#puestoTrabajo").attr("ventanillaActUsu", $("#puestoTrabajo").val());
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
		.fila1{
			background-color: 	#C3D9FF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fila2{
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
		#modalRedireccionar{
			background-color: white;
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 35%;
            top: 25%;
            width: 30%; 
            height: 250px; 
            overflow: auto;             
            border-radius: 2%;
        }
        #modalTurno{
			background-color: white;
			display: none; 
            position: fixed; 
            z-index: 1; 
            left: 30%;
            top: 17%;
            width: 40%; 
            height: 450px; 
            overflow: auto;             
            border-radius: 2%;
        }
        .modal-header{
	        height: 40px; 
	        font-size: 9px;
        }
        body{
            width: 100%;
        }
        #btncerrar, #btngrabar{
			 width: 70px; 
             height: 30px; 
			 border-radius:10px;
			 text-decoration:none;
			 font-family:'Roboto',sans-serif;
			 font-weight:300;
			 color:#FFFFFF;
			 background-color:#4eb5f1;
			 text-align:center;
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
	<BODY width=100%>
	<?php
	// --> Consultar nombre del tema
	$nomTema = "";
	if($tema != '')
	{
		$sqlTema = "
		SELECT Codnom
		  FROM ".$wbasedato."_000305
		 WHERE Codtem = '".$tema."'
		   AND Codest = 'on'
		";
		$resTema = mysql_query($sqlTema, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTema):</b><br>".mysql_error());
		if($rowTema = mysql_fetch_array($resTema))
			$nomTema = $rowTema['Codnom'];
		else
		{
			echo '
			<div style="color: #676767;font-family: verdana;background-color: #E4E4E4;font-size:15px" >
				[?] El tema '.$tema.' no existe.
			</div>';
			return;
		}	
	}	
		
		
	if(!isset($tema) || trim($tema) == '')
	{
		echo '
		<div style="color: #676767;font-family: verdana;background-color: #E4E4E4;font-size:15px" >
            [?] Falta la variable "tema", la cual define el area de trabajo del turnero.
        </div>';
		return;
	}
	else
	{
		echo "<input type='hidden' id='tema' value='".$tema."'>";
		echo "<input type='hidden' id='redir' value='".$redir."'>";
	}
	
	// -->	ENCABEZADO
	encabezado($nomTema, $wactualiz, 'clinica');
	
	$ventanillaActUsu 	= "";
	$servicioActUsu 	= "";
	
	// --> Consultar el servicio y la ventanilla actual del usuario
	$sqlSerAct = "
	SELECT Puecod, Pueser, Puesse
	  FROM ".$wbasedato."_000301
	 WHERE Puetem = '".$tema."'
	   AND Pueusu = '".$wuse."'
	   AND Pueest = 'on'	 
	";
	$resSerAct = mysql_query($sqlSerAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSerAct):</b><br>".mysql_error());
	if($rowSerAct = mysql_fetch_array($resSerAct))
	{
		$ventanillaActUsu 		= $rowSerAct['Puecod'];
		$servicioActUsu 		= json_decode($rowSerAct['Pueser'], true);
		$subServicioActUsu 		= (($rowSerAct['Puesse'] == '') ? array() : json_decode($rowSerAct['Puesse'], true));
	}
	
	echo "<input type='hidden' id='ventanillaActUsu' 	value='".$ventanillaActUsu."'>";
	echo "<input type='hidden' id='servicioActUsu' 		value='".json_encode($servicioActUsu)."'>";
	echo "<input type='hidden' id='subServicioActUsu' 	value='".json_encode($subServicioActUsu)."'>";
	
	// --> Maestro de servicios
	$arrayServicios = array();
	$sqlServicios 	= "
	SELECT Sercod, Sernom, Serord
	  FROM ".$wbasedato."_000298
	 WHERE Sertem = '".$tema."' 
	   AND Serest = 'on'
	 ORDER BY Serord
	";


	$resServicios = mysql_query($sqlServicios, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlServicios):</b><br>".mysql_error());
	while($rowServicios = mysql_fetch_array($resServicios))
		$arrayServicios[$rowServicios['Sercod']] = ($rowServicios['Sernom']);

    // --> Seleccionar los temas para redireccionamiento de turno
	$strTema=$tema.',';
	$sqlTema = "
	SELECT Codtem,Codnom
	  FROM ".$wbasedato."_000305
	 WHERE Codaso = '".$tema."'
	   AND Codest = 'on'
	";
	$resTema = mysql_query($sqlTema, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTema):</b><br>".mysql_error());
	while ($rowTema = mysql_fetch_array($resTema))
		   $strTema .= "'".$rowTema['Codtem']."',";

	$strTema = substr_replace($strTema ,"", -1);

    if ($strTema !== '')
    {
		// --> Maestro todos los servicios para redireccionar turno
		$Serviciotot = "<option value=''>Seleccione..</option>";
		$sqlServiciotot = "
		SELECT Sercod, Sernom, Serord, Sertem
		  FROM ".$wbasedato."_000298
		 WHERE Serest = 'on'
		   AND Sertem in (".$strTema.")
		 ORDER BY Serord
		";

		$resServicios = mysql_query($sqlServiciotot, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlServiciotot):</b><br>".mysql_error());
		while($rowServicios = mysql_fetch_array($resServicios))
			  $Serviciotot .="<option tema='".$rowServicios['Sertem']."' value='".$rowServicios['Sercod']."'>".($rowServicios['Sernom'])."</option>";	
    }
	
	// --> Obtener maestro de condiciones especiales
	$selCondiciones = "<option value=''>Seleccione..</option>";
	$sqlCondiciones = "
	SELECT Concod, Connom, Conord, Conpri
	  FROM ".$wbasedato."_000299
     WHERE Conest = 'on'
	 ORDER BY Conord
	";
	$resCondiciones = mysql_query($sqlCondiciones, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCondiciones):</b><br>".mysql_error());
	while($rowCondiciones = mysql_fetch_array($resCondiciones))
	{
		$selCondiciones .="<option value='".$rowCondiciones['Concod']."'>".($rowCondiciones['Connom'])."</option>";	
		
/*		$arrCondiciones[$rowCondiciones['Concod']] = strtoupper($rowCondiciones['Connom']);
		if($rowCondiciones['Conpri'] != 'on')
			$prioritarioDefault = $rowCondiciones['Concod'];*/
	}
	
	// --> Maestro de puestos de trabajo
	$arrayVentanillas 	= array();
	
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
			<li width='30%' id='tab1'><a href='#tabListaEspera' 		onclick='listaPacientesPendientesAtencion()' 	style='font-size:10pt'>Turnos en espera</a></li>
			<li width='30%' id='tab2'><a href='#tabListaAtendidos' 		onclick='listaDePacientesAtendidos()' 			style='font-size:10pt'>Atendidos</a></li>
			<li width='30%' id='tab3'><a href='#tabListaCancelados'		onclick='listaDePacientesCancelados()' 			style='font-size:10pt'>Cancelados</a></li>
			<li width='40%'>
				<table width='100%' style='padding:6px;font-family: verdana;font-size: 10pt;color: #4C4C4C'>
					<tr>
						<td style='font-weight:normal;' align='center' id='tdBotonActualizar'>
							<span style='cursor:pointer' onclick='listaPacientesPendientesAtencion()'>Actualizar&nbsp;<img width='14px' height='14px' src='../../images/medical/sgc/Refresh-128.png' title='Actualizar listado.'></span>
						</td>
						<td style='font-weight:normal;'>
							&nbsp;&nbsp;
							<span id='relojTemp' cincoMinTem='86400000'></span>&nbsp;<img width='15px' height='15px' src='../../images/medical/sgc/Clock-32.png'>
						</td>
						<td style='font-weight:normal;' align='center'>
							&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
							<span style='font-family: verdana;font-weight:normal;font-size: 10pt;'>
								Servicio:&nbsp;</b>
								<select id='selectServicio' multiple='multiple' style='border-radius: 4px;border:1px solid #AFAFAF;width:130' onChange='cambiarServicio()'>									
								";							
							foreach($arrayServicios as $codServicio => $nomServicio)
								echo "<option value='".$codServicio."' ".((in_array($codServicio, $servicioActUsu)) ? "SELECTED='SELECTED'" : "" ).">".$nomServicio."</option>";
				echo "			</select>
								&nbsp;
								Cub&iacute;culo:&nbsp;&nbsp;</b>
								<select id='puestoTrabajo' type='text' style='border-radius: 4px;border:1px solid #AFAFAF;width:100px' ventanillaActUsu='".$ventanillaActUsu."' onChange='cambiarPuestoTrabajo(true)'>
									<option value='' ".((trim($ventanillaActUsu) == "") ? "SELECTED='SELECTED'" : "" ).">Seleccione..</option>
								";
							foreach($arrayVentanillas as $codVentanilla => $nomVentanilla)
								echo "<option value='".$codVentanilla."' ".(($codVentanilla == $ventanillaActUsu) ? "SELECTED='SELECTED'" : "" ).">".$nomVentanilla."</option>";
				echo "			</select>
								<span id='contSubServicio' style='display:none'>
									&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;
									Sub-servicio:&nbsp;</b>
									<select  id='selectSubServicio' multiple='multiple' style='border-radius: 4px;border:1px solid #AFAFAF;width:100' subServicioActUsu='".$subServicioActUsu."' onChange='cambiarSubServicio()'>
									</select>
								</span>
							</span>
						</td>
					</tr>
				</table>
			</li>
		</ul>
		<div id='tabListaEspera'>
			<table width='100%' style='font-family: verdana;font-size: 10pt;color: #4C4C4C;border-spacing:10px;'>
				<tr>
					<td align='left' width='20%'>
						<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarPacientesEnEspera' type='text' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150px'>
					</td>
					<td width='35%' style='border-radius: 4px;border:1px solid #aed0ea;background-color:#deedf7'>		
						<span style='color:#2779aa'>Servicio seleccionado: </span><span id='serSele' style='color:#009900'></span>
					</td>
					<td width='15%' style='border-radius: 4px;border:1px solid #aed0ea;background-color:#deedf7'>
						<span style='color:#2779aa'>Cub&iacute;culo: </span><span id='cubSele' style='color:#009900'></span>
					</td>
					<td width='30%' style='border-radius: 4px;border:1px solid #aed0ea;background-color:#deedf7'>
						<span style='color:#2779aa'>Sub-servicio: </span><span id='subSele' style='color:#009900'></span>
					</td>
				</tr>
			</table>
			<br>
			<div id='listaEsperaAtencion'>";
				// $respuesta = listaDePacientesEnEspera("%");
	echo	$respuesta['html'];
	echo "	</div>
		</div>
		<div id='tabListaAtendidos'>
			<table width='100%'>
				<tr>
					<td align='left'>
						<span style='font-family: verdana;font-size: 10pt;color: #4C4C4C'>
							<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarPacientesAtendidos' type='text' placeholder=' Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150'>
							&nbsp;&nbsp;|&nbsp;
							<b>Fecha de atenci&oacute;n:</b>&nbsp;&nbsp;</b><input id='fechaAtencion' type='text' disabled='disabled' value='".date("Y-m-d")."' style='border-radius: 4px;border:1px solid #AFAFAF;width:100px'>
						</span>
					</td>
				</tr>
			</table>
			<div id='listaPacAtendidos'>";
				// $respuesta = listaDePacientesAtendidos(date("Y-m-d"));
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
				//$respuesta = listaDePacientesCancelados(date("Y-m-d"));
	echo		$respuesta['html'];
	echo "	</div>
		</div>
	</div>
    <div class='modal' id='modalTurno' name='modalTurno'  align='center'>
    	<div class='encabezadoTabla'>
                  <p class='modal-title text-center' id='alertModalLabel' style='font-size:15px;vertical-align: middle;height: 10px'>Turno redireccionado</p>
        </div>
        <div class='modal-content' id='modalTurnocon'>             
        </div><br>
        <div class='encabezadoTabla modal-footer' style='height: 40px'>
	       <input type='button' id='btncerrar' class='button3' value='Cerrar' onclick='$(\"#modalTurno\").hide();'>
    	</div>
    </div>    
    <div class='modal' id='modalRedireccionar' name='modalRedireccionar'  align='center'>
        <div class='modal-content'>
            <div class='encabezadoTabla'>
                  <p class='modal-title text-center' id='alertModalLabel' style='font-size:15px;vertical-align: middle;height: 10px'>Redireccionar servicio</p>
            </div>
            <div class='modal-body' >
              <div class='form-group'>
                  <br><label class='fila1'>Favor seleccionar</label><br><br>
                  <div class='fila2'>Tipo de Servicio&nbsp;&nbsp;&nbsp;&nbsp;<select id='seltiposervicio' class='form-control' type='text' style='border-radius: 4px;border:1px solid #AFAFAF;width:100px'>".$Serviciotot."</select>
				 </div>
                  <div class='fila2'>Usuario prioritario  <select id='selprioritario' class='form-control' type='text' style='border-radius: 4px;border:1px solid #AFAFAF;width:100px'>".$selCondiciones."</select>
                 </div><br>
              </div>
            <br>
            <div id='lbturno' style='border-radius: 4px;border:1px solid #AFAFAF;color:red;'></div>
            </div><br>
            <div class='modal-footer'>
               <button type='button' id='btngrabar' style='border-radius: 4px;border:1px solid #AFAFAF;' onclick='grabarRedireccionarturno()'>Grabar</button>
               <button type='button' id='btncerrar' style='border-radius: 4px;border:1px solid #AFAFAF;' onclick='$(\"#modalRedireccionar\").hide();'>Cerrar</button>
            </div>
        </div>
        <input type='hidden' id='txtTipoDocu' value=''>
        <input type='hidden' id='txtDocumen' value=''>
        <input type='hidden' id='txtNombre' value=''>
        <input type='hidden' id='txtTurno' value=''>
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
