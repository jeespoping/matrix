<?php
include_once("conex.php");
//=========================================================================================================================================\\
//       	MONITOR DE TURNOS URGENCIAS
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:	2015-07-01
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2021-07-05';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                    
//--------------------------------------------------------------------------------------------------------------------------------------------
//	2021-07-05 Luis F Meneses: Se aplican nuevos estilos (/matrix/ips/procesos/CartDig.css)
//	2018-08-06 Jerson Trujillo: Las consultas de los turnos ya no se haran 24 horas antes, sino 12 horas antes.

//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


// if(!isset($_SESSION['user']))
// {
    // echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                // [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            // </div>';
    // return;
// }
// else
// {
	// $user_session 	= explode('-',$_SESSION['user']);
	// $wuse 			= $user_session[1];
	$wuse 			= 'MONITOR';
	

	include_once("root/comun.php");
	

	$conex 			= obtenerConexionBD("matrix");
	$wbasedato	 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//------------------------------------------------------------------------------------
	//	--> Consulta si hay alertas y genera el html para pintarlas
	//------------------------------------------------------------------------------------
	function obtenerAlertas()
	{
		global $wbasedato;
		global $conex;
		global $monitorSala;

		$respAlertas 	= array("hayAlertas" => false, "htmlAlertas" => "");
		$primeraVez	 	= true;
		$arrayId 		= array();

		/*
		// ANTES DE APLICAR NUEVO ESTILO
		$htmlAlertas = "
		<table width='100%' style='color:#000000;font-family: verdana;font-weight: normal;font-size: 4rem;'>
			<tr style='background-color: #2a5db0;color:#ffffff;font-weight:bold;'>
				<td align='center'>Turno</td>
				<td align='center'>Por favor pasar a:</td>
			</tr>
		";
		*/

		$htmlAlertas = "<table id = 'tablaAlerta'>";

		// --> 	Consultar turnos de maximo 24 horas atras.
		// 		1 SELECT: Alerta de llamado para el triage
		// 		2 SELECT: Alerta de rellamado para el triage
		// 		3 SELECT: Alerta de llamado para la admisión
		// 		4 SELECT: Alerta de llamado para la consulta
		$sqlAlertas = "
		SELECT A.Fecha_data, A.Hora_data, Atutur, Puenom
		  FROM ".$wbasedato."_000178 AS A, ".$wbasedato."_000180
		 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
		   AND Atuest = 'on'
		   AND Atullt = 'on'
	   "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
		   AND Atuctl = Puecod
		 UNION
		SELECT A.Fecha_data, A.Hora_data, Atutur, Puenom
		  FROM ".$wbasedato."_000178 AS A, ".$wbasedato."_000180
		 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
		   AND Atuest = 'on'
		   AND Atuart = 'on'
	   "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
		   AND Atuctl = Puecod
		 UNION
		SELECT A.Fecha_data, A.Hora_data, Atutur, Puenom
		  FROM ".$wbasedato."_000178 AS A, ".$wbasedato."_000180
		 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
		   AND Atuest = 'on'
		   AND Atullv = 'on'
	   "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
		   AND Atuven = Puecod
		 UNION
		SELECT A.Fecha_data, A.Hora_data, Atutur, Puenom
		  FROM ".$wbasedato."_000178 AS A, ".$wbasedato."_000180
		 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
		   AND Atuest = 'on'
		   AND Atullc = 'on'
	   "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
		   AND Atucon = Puecod
		";
		$resAlertas = mysql_query($sqlAlertas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAlertas):</b><br>".mysql_error());
		while($rowAlertas = mysql_fetch_array($resAlertas))
		{
			// --> Solo mostrar turnos de maximo 12 horas atras
			if(strtotime($rowAlertas['Fecha_data']." ".$rowAlertas['Hora_data']) < strtotime('-12 hours'))
				continue;
			
			if($primeraVez)
			{
				$primeraVez 				= false;
				$respAlertas['hayAlertas'] 	= true;
			}
			$colorFila = (($colorFila == '#DCE5F2') ? '#F2F5F7;' : '#DCE5F2');

			/*
			$htmlAlertas.= "
				<tr style='background-color:".$colorFila."'>
					<td align='center'>".substr($rowAlertas['Atutur'], 7)."</td>
					<td align='center'>".$rowAlertas['Puenom']."</td>
				</tr>
			";
			*/
			// Cada alerta tiene su título con el turno
			// y en otra fila con el mensaje
			$htmlAlertas.= "
				<tr>
					<td class='tdAzul' width='100%'>TURNO ".substr($rowAlertas['Atutur'], 7)."</td>
				</tr>
				<tr>
					<td class='tdNormal' width='100%'>Por favor pasar a:<br>".$rowAlertas['Puenom']."</td>
				</tr>
			";
		}

		$htmlAlertas.= "
		</table>
		";

		$respAlertas['htmlAlertas'] = $htmlAlertas;

		return $respAlertas;
	}
	//------------------------------------------------------------------------------------
	//	--> Pinta la lista de turnos con su correspondiente estado
	//------------------------------------------------------------------------------------
	function listarTurnos($numPagina)
	{
		global $wbasedato;
		global $conex;
		global $monitorSala;
		global $arraySalas;
		global $numRegistrosPorPagina;

		// ENCABEZADO ANTERIOR
		/*
		$html 		= "
		<div style='background-color:#FFFFFF;color:#E2007A;font-family: verdana;font-weight: normal;font-size: 2.2rem;'>
			La atenci&oacuten ser&aacute de acuerdo a la clasificaci&oacuten por prioridad.
		</div>
		<table width='100%' style='background-color:#DBDBDB;cellspacing:0.1rem;color:#000000;font-family: verdana;font-weight: normal;font-size: 2.5rem;'>
			<tr align='center' style='background-color:#D1ECF9;font-size: 3.2rem;'>
				<td width='10%'>Turno</td>
				<td width='35%'>Estado</td>
				<td width='20%'>Sala</td>
				<td width='35%'>Ubicaci&oacuten</td>
			</tr>
		";
		*/

		$html = "<center>
		<table id='tablaTurnos'>
			<tr>
				<th width='15%'>TURNO</td>
				<th width='35%'>ESTADO</td>
				<th width='20%'>SALA</td>
				<th width='30%'>UBICACI&Oacute;N</td>
			</tr>
		";

		// --> Consultar turnos de maximo 24 horas atras.  movhos_000178
		$sqlTurnos = "
		SELECT Atutur, Atuetr, Atucta, Atupad, Atuadm, Fecha_data, Hora_data
		  FROM ".$wbasedato."_000178
		 WHERE Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
		   AND Atuest = 'on'
		   AND Atuaor != 'on'
		   "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
		 ORDER BY REPLACE(Atutur, '-', '')*1 ASC
		";
		
		// HABILITAR PARA PRUEBAS.
		/*
		$sqlTurnos = "
		SELECT Atutur, Atuetr, Atucta, Atupad, Atuadm, Fecha_data, Hora_data
		  FROM ".$wbasedato."_000178
		 WHERE Fecha_data >= '2021-06-01'
		 ORDER BY REPLACE(Atutur, '-', '')*1 ASC
		";
		*/
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());

		$colorFila		= '#F2F5F7;';
		$arrFilasTur 	= array();
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			// --> Solo mostrar turnos de maximo 12 horas atras
			if(strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']) < strtotime('-12 hours'))
				continue;
			
			// --> Si no tiene triage
			if($rowTurnos['Atucta'] != 'on')
			{
				if($rowTurnos['Atuetr'] == 'on')
					$respuesta["estado"] = "En triage";
				else
					$respuesta["estado"] = "Pendiente de triage";
			}
			else
			{
				if($rowTurnos['Atupad'] == 'on' && $rowTurnos['Atuadm'] != 'on')
					$respuesta["estado"] = "En admisi&oacute;n";
				elseif($rowTurnos['Atuetr'] == 'on')
					$respuesta["estado"] = "En triage";	// --> En triage por reasignacion
					else
						$respuesta	= obtenerEstadoUbicacionTurno($rowTurnos['Atutur']);
			}
			
			// PARA PRUEBAS. COMENTARIAR PARA PRODUCCIÓN ------------
			/*
			if (random_int(1, 3) == 2)
			{
				$respuesta["altaOmuerte"] = false ;
				if (random_int(1, 3) == 2)
					$respuesta["enAltaMedica"] = true ;
			}
			*/
			// -----------------------------------------------------
			
			// --> Si el turno no ha sido dado de alta, muerte u hospitalizacion Y no se genero ningun error.
			if(!$respuesta["altaOmuerte"] && !$respuesta["error"])
			{
				$colorFila 		= (($colorFila == '#FFFFFF') ? '#F2F5F7;' : '#FFFFFF');
				// --> Cuando el estado sea alta debo mostrarlo de color verde.
				$colorFila 		= (($respuesta["enAltaMedica"]) ? '#C2E8CE;' : $colorFila);
				/*
				$arrFilasTur[] = "
					<tr style='background-color:".$colorFila."'>
						<td align='center' style=''>
							".substr($rowTurnos['Atutur'], 7)."
						</td>
						<td align='left' style='".(($respuesta["enAltaMedica"]) ? 'background-color:#C2E8CE;' : '')."'>
							&nbsp;".utf8_encode($respuesta["estado"])."
						</td>
						<td align='left' style=''>
							&nbsp;".utf8_encode((($respuesta["sala"] != '') ? $respuesta["sala"] : ""))."
						</td>
						<td align='left' style=''>
							&nbsp;".utf8_encode((($respuesta["ubicacion"] != '') ? $respuesta["ubicacion"] : ""))."
						</td>
					</tr>
				";
				*/
				$clase = ($respuesta["enAltaMedica"]?'tdAzul':'tdNormal');
				$arrFilasTur[] = "
					<tr>
						<td class='$clase'>".substr($rowTurnos['Atutur'], 7)."</td>
						<td class='$clase'>".utf8_encode($respuesta["estado"])."</td>
						<td class='$clase'>".utf8_encode((($respuesta["sala"] != '') ? $respuesta["sala"] : ""))."</td>
						<td class='$clase'>".utf8_encode((($respuesta["ubicacion"] != '') ? $respuesta["ubicacion"] : ""))."</td>
					</tr>
				";
				
				
			}
		}

		// --> Paginar, (numRegistrosPorPagina) registros por pagina
		$totalPaginas 			= ((int)(count($arrFilasTur)/$numRegistrosPorPagina))+((count($arrFilasTur)%$numRegistrosPorPagina > 0) ? 1 : 0);
		$totalPaginas			= (($totalPaginas == 0) ? 1 : $totalPaginas);

		// --> Si se sobrepasa el numero de paginas, vuelve y se inicia con la pagina 1
		if($numPagina+1 > $totalPaginas)
		{
			$rangoIni 	= 0;
			$rangoFin	= $numRegistrosPorPagina-1;
			$numPagina	= 1;
		}
		else
		{
			$rangoIni 	= ($numPagina*$numRegistrosPorPagina);
			$rangoFin 	= $rangoIni+($numRegistrosPorPagina-1);
			$numPagina	= $numPagina+1;
		}

		if(count($arrFilasTur) > 0)
			for($x = $rangoIni; $x <= $rangoFin ; $x++)
				$html.= $arrFilasTur[$x];

		// --> Pintar ultimo turno atendido en la ventanilla
		/*$sqlUltTur = "
		SELECT Atutur
		  FROM ".$wbasedato."_000178
		 WHERE Atuest = 'on'
		   AND Atufll != '0000-00-00'
		   AND Atuhll != '00:00:00'
		 ORDER BY CONCAT(Atufll, ' ', Atuhll) DESC;
		";
		$resUltTur 	= mysql_query($sqlUltTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUltTur):</b><br>".mysql_error());
		// --> Solo pinto el primer resultado
		if($rowUltTur = mysql_fetch_array($resUltTur))
		{
			$colorFila 		= (($colorFila == '#DCE5F2') ? '#F2F5F7;' : '#DCE5F2');
			$html.= "
				<tr style='background-color:".$colorFila.";color:#EB5B4D'>
					<td width='34%' align='center' style='border-bottom:1px solid #BFBFBF;padding:0.0em'>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						".$rowUltTur['Atutur']."
					</td>
					<td width='66%' align='center' style='border-bottom:1px solid #BFBFBF;padding:0.0em'>
						Ultimo turno llamado a la ventanilla
					</td>
				</tr>
			";
		}*/

		$html.= "</table>";

		$respuesta['html'] 			= $html;
		$respuesta['totalPaginas'] 	= $totalPaginas;
		$respuesta['numPagina'] 	= $numPagina;

		return $respuesta;
	}
	//------------------------------------------------------------------------------------
	//	--> Obtener el estado de un paciente
	//------------------------------------------------------------------------------------
	function obtenerEstadoUbicacionTurno($turno)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $arraySalas;

		$basedatoshce	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
		$infoTurnoHce22	= array();
		$respuesta 		= array("altaOmuerte" => false, "enAltaMedica" => false, "error" => false, "estado" => "En sala de espera", "ubicacion" => "", "sala" => "");

		// --> Obtener toda la informacion del turno de la hce_000022
		$sqlInfo22 = "
		SELECT A.*, B.Medtri
		  FROM ".$basedatoshce."_000022 AS A LEFT JOIN ".$wbasedato."_000048 AS B ON A.Mtrmed = Meduma
		 WHERE Mtrtur = '".$turno."'
		   AND Mtrest = 'on'
		";
		$resInfo22 = mysql_query($sqlInfo22, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo22):</b><br>".mysql_error());
		if($rowInfo22 = mysql_fetch_array($resInfo22, MYSQL_ASSOC))
			$infoTurnoHce22 = $rowInfo22;
		else
		{
			$respuesta["estado"] = "Pendiente de admisi&oacute;n";
			return $respuesta;
		}

		// --> Si el paciente tiene una entrega en la 17 no lo muestro, ya que me indica que el paciente va para hospitalizacion.
		$sqlEntrega = "
		SELECT Eyrnum
		  FROM ".$wbasedato."_000017
		WHERE Eyrhis = '".$infoTurnoHce22['Mtrhis']."'
		  AND Eyring = '".$infoTurnoHce22['Mtring']."'
		  AND Eyrsor = '".$infoTurnoHce22['Mtrcci']."'
		  AND Eyrtip = 'Entrega'
		  AND Eyrest = 'on'
		";
		$resEntrega = mysql_query($sqlEntrega, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEntrega):</b><br>".mysql_error());
		if($rowEntrega = mysql_fetch_array($resEntrega))
		{
			$respuesta["altaOmuerte"] = true;
			return $respuesta;
		}

		// --> Cosultar si el turno tiene muerte o alta definitiva.
		$sqlMuerteAlta = "
		SELECT id
		  FROM ".$wbasedato."_000018
		WHERE Ubihis  = '".$infoTurnoHce22['Mtrhis']."'
		  AND Ubiing  = '".$infoTurnoHce22['Mtring']."'
		  AND (Ubiald = 'on' OR Ubimue = 'on')
		";
		$resMuerteAlta = mysql_query($sqlMuerteAlta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMuerteAlta):</b><br>".mysql_error());
		if(mysql_fetch_array($resMuerteAlta))
		{
			$respuesta["altaOmuerte"] = true;
			return $respuesta;
		}

		// --> Si el turno tiene una conducta asociada
		if(trim($infoTurnoHce22['Mtrcon'] != ''))
		{
			$sqlConducta = "
			SELECT Condes, Conmue, Conalt
			  FROM ".$basedatoshce."_000035
			 WHERE Concod = '".$infoTurnoHce22['Mtrcon']."'
			";

			$resConducta = mysql_query($sqlConducta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConducta):</b><br>".mysql_error());
			if($rowConducta = mysql_fetch_array($resConducta))
			{
				// --> Si es conducta de muerte
				if($rowConducta['Conmue'] == 'on')
					$respuesta["altaOmuerte"] = true;
				else
				{
					$respuesta["estado"] 		= ucfirst(strtolower($rowConducta['Condes']));

					// --> Si el estado es de alta.
					$respuesta["enAltaMedica"] 	= (($rowConducta['Conalt'] == 'on') ? TRUE : FALSE);

					// --> Obtener ubicacion
					$sqlUbicacion = "
					SELECT Habcpa, Habzon
					  FROM ".$wbasedato."_000020
					 WHERE Habhis = '".$infoTurnoHce22['Mtrhis']."'
					   AND Habing = '".$infoTurnoHce22['Mtring']."'
					   AND Habcub = 'on'
					";
					$resUbicacion = mysql_query($sqlUbicacion, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUbicacion):</b><br>".mysql_error());
					if($rowUbicacion = mysql_fetch_array($resUbicacion))
					{
						$respuesta["sala"] 		= ucfirst(strtolower($arraySalas[$rowUbicacion['Habzon']]));
						$respuesta["ubicacion"] = ucfirst(strtolower($rowUbicacion['Habcpa']));
					}
				}
			}

			return $respuesta;
		}

		// --> Si el turno esta en consulta en este momento
		if($infoTurnoHce22['Mtrcur'] == 'on')
		{
			$respuesta["estado"] = "En consulta ".(($infoTurnoHce22['Medtri'] == 'on') ? "triage" : "");

			// --> obtengo el puesto de trabajo del medico que lo esta atendiendo
			$sqlEnConsulta = "
			SELECT Puenom
			  FROM ".$wbasedato."_000180
			 WHERE Pueusu = '".$infoTurnoHce22['Mtrmed']."'
			";
			$resEnConsulta = mysql_query($sqlEnConsulta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEnConsulta):</b><br>".mysql_error());
			if($rowEnConsulta = mysql_fetch_array($resEnConsulta))
				$respuesta["ubicacion"] = ucfirst(strtolower($rowEnConsulta['Puenom']));

			return $respuesta;
		}

		// --> Si ya tiene un triagge asignado y no tiene fecha de consulta.
		if(trim($infoTurnoHce22['Mtrtri'] != '') && $infoTurnoHce22['Mtrfco'] == '0000-00-00')
		{
			// --> Indica que el turno esta en espera de consulta
			$respuesta["estado"] 	= "Pendiente de consulta";
			return $respuesta;
		}
		elseif(trim($infoTurnoHce22['Mtrtri'] == ''))
		{
			// --> Indica que el turno esta en espera de triagge
			$respuesta["estado"] 	= "Pendiente de triage";
			return $respuesta;
		}

		return $respuesta;
	}

//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R 	P O S T    J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'actualizarMonitor':
		{
			$arraySalas						= json_decode(str_replace('\\', '', $arraySalas), true);
			$respuesta 						= array("htmlListaTurnos" => "", "hayAlertas" => false, "htmlAlertas");

			$respListaTurnos			 	= listarTurnos($numPagina);
			$respuesta['htmlListaTurnos'] 	= $respListaTurnos['html'];
			$respuesta['numPagina'] 		= $respListaTurnos['numPagina'];
			$respuesta['totalPaginas'] 		= $respListaTurnos['totalPaginas'];

			$respAlertas 					= obtenerAlertas();
			$respuesta['hayAlertas'] 		= $respAlertas['hayAlertas'];
			$respuesta['htmlAlertas'] 		= $respAlertas['htmlAlertas'];

			echo json_encode($respuesta);
			break;
			return;
		}
		return;
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
	  <title></title>
	</head>
		<meta charset="UTF-8">
		<!--
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		-->
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<link rel='stylesheet' href='/matrix/ips/procesos/CartDig.css'/>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	var height = 0;
	var width  = 0;
	var intervalSet;

	$(function(){

		/*
		$("#accordionPrincipal").accordion({
			collapsible: false,
			heightStyle: "content"
		});
		*/
		//$( "#accordionPrincipal" ).accordion( "option", "icons", {} );

		//$("#divContenido").css({"padding": "0.1em"});

		// --> Ajustar la vista a la resolucion de la pantalla
		//obtenerResolucioPantalla();
		//width1 		= width*0.99;
		//height1 	= height*0.99;

		/*
		if(width1 > 0 && height1 > 0)
			$("#accordionPrincipal").css({"width":width1});
		else
			$("#accordionPrincipal").css({"width": "99 %"});
		*/

		// --> Llamado automatico, para que el monitor este actualizando
		setInterval(function(){
			actualizarMonitor();
		}, 11000);

	});
	//-------------------------------------------------------------------
	//	--> Funcion que obtiene la resolucion de la pantalla
	//-------------------------------------------------------------------
	function obtenerResolucioPantalla()
	{
		
		if (self.screen){     // for NN4 and IE4
			width 	= screen.width;
			height 	= screen.height
		}
		else
		{
			if (self.java){   // for NN3 with enabled Java
				var jkit = java.awt.Toolkit.getDefaultToolkit();
				var scrsize = jkit.getScreenSize();
				width 	= scrsize.width;
				height 	= scrsize.height;
			}
		}
		
	}

	//---------------------------------------------------------
	// 	--> Funcion encargada de estar actualizando el monitor
	//---------------------------------------------------------
	function actualizarMonitor()
	{
		$.post("monitorUrgencias.php",
		{
			consultaAjax:   		'',
			accion:         		'actualizarMonitor',
			wemp_pmla:        		$('#wemp_pmla').val(),
			monitorSala:       		$('#monitorSala').val(),
			arraySalas:				$("#arraySalas").val(),
			numPagina:				$("#numPagina").val(),
			numRegistrosPorPagina:	$("#numRegistrosPorPagina").val()
		}, function(respuesta){

			// --> Mostrar en ventana emergente las alertas generadas
			if(respuesta.hayAlertas)
			{
				$("#ventanaAlertas").html(respuesta.htmlAlertas);
				$("#ventanaAlertas").dialog({
					modal	: true,
					title	: "<div id='barraAtencion' style='width:70%; margin: 0 auto; text-align:center; font-size: 50px; color:rgb(255,0,0); background-color:rgba(255,255,255,0.5)'>Atenci&oacuten!</div>",
					show	: { effect: "slide", duration: 400 },
					hide	: { effect: "fold", duration: 400 },
					closeText: " (x)",
					dialogClass: 'ventanaAlertas'
				});
								
				$("#ventanaAlertas").dialog("widget").position({
					my: 'center',
					at: 'center',
					/*of: $(this)*/
				});
				
				
				// --> Blink al mensaje de "¡Atencion!"
				var mensajeAtencion = setInterval(function(){
					$("#barraAtencion").css('visibility' , $("#barraAtencion").css('visibility') === 'hidden' ? '' : 'hidden')
				}, 700);

				// --> Sonido de alerta				
				var sonidoAlerta = setInterval(function(){
					$("#sonidoAlerta")[0].play();
				}, 2000);

				// --> Cerrar la ventana emergente automaticamente despues de 15 segundos
				setTimeout(function(){
					clearInterval(mensajeAtencion);
					$("#ventanaAlertas").html("");
					$("#ventanaAlertas").dialog("close");
				}, 7000);  // 7000   120000

				// --> Apagar el sonido de alerta
				setTimeout(function(){
					 clearInterval(sonidoAlerta);
				}, 6000);
			}

			// --> Ajustar la vista a la resolucion de la pantalla
			//obtenerResolucioPantalla();
			//height1 	= (height*0.99)-$("#encabezado").height();
			//height1 	= (height*0.99)-$("#titTurnos").height();
			//alert (height1);
			

			// --> Actualizar la lista de turnos, con el efecto de paginacion
			// $("#divContenido").hide('fade', 800, function(){
				// $(this).html(respuesta.htmlListaTurnos).height(height1).effect( "slide", {}, 1000, function(){
					// $(this).show();
				// });
			// });

			// --> Actualizar la lista de turnos, con el efecto de paginacion
			/*
			$("#divContenido").hide('fade', 800, function(){
				$(this).html(respuesta.htmlListaTurnos).height(height1).show( "blind", {}, 1200)
			});
			*/
			$("#divContenido").hide('fade', 800, function(){
				$(this).html(respuesta.htmlListaTurnos).show( "blind", {}, 1200)
			});

			// --> Numero de pagina actual
			$("#numPagina").val(respuesta.numPagina);
			$("#totalPaginas").val(respuesta.totalPaginas);

			// --> Mensaje de pagina
			$("#msjPagina").html("Pag. "+respuesta.numPagina+"/"+respuesta.totalPaginas);

		}, 'json');
	}
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>

<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<body>
	<?php
	
	$wfecha=date("Y-m-d");
	$year = (integer)substr($wfecha,0,4);
	$month = (integer)substr($wfecha,5,2);
	$day = (integer)substr($wfecha,8,2);
	$nomdia=mktime(0,0,0,$month,$day,$year);
	$nomdia = strftime("%w",$nomdia);
	switch ($nomdia)
	{
		case 0:
			$diasem = "Domingo";
			break;
		case 1:
			$diasem = "Lunes";
			break;
		case 2:
			$diasem = "Martes";
			break;
		case 3:
			$diasem = "Mi&eacute;rcoles";
			break;
		case 4:
			$diasem = "Jueves";
			break;
		case 5:
			$diasem = "Viernes";
			break;
		case 6:
			$diasem = "S&aacute;bado";
			break;
	}
	switch ($month)
	{
		case 1:
			$monthN = "enero";
			break;
		case 2:
			$monthN = "febrero";
			break;
		case 3:
			$monthN = "marzo";
			break;
		case 4:
			$monthN = "abril";
			break;
		case 5:
			$monthN = "mayo";
			break;
		case 6:
			$monthN = "junio";
			break;
		case 7:
			$monthN = "julio";
			break;
		case 8:
			$monthN = "agosto";
			break;
		case 9:
			$monthN = "septiembre";
			break;
		case 10:
			$monthN = "octubre";
			break;
		case 11:
			$monthN = "noviembre";
			break;
		case 12:
			$monthN = "diciembre";
			break;
	}
	$wfechaG=$day." de ".$monthN." de ".$year;
	
	// --> Consultar maestro de salas
	$arraySalas = array();

	$sqlSalas 	= "
	SELECT Arecod, Aredes
	  FROM ".$wbasedato."_000169
	 WHERE Areest = 'on' ";
	$resSalas = mysql_query($sqlSalas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalas):</b><br>".mysql_error());
	while($rowSalas = mysql_fetch_array($resSalas))
		$arraySalas[$rowSalas['Arecod']] = $rowSalas['Aredes'];

	// --> Consultar el numero de filas que se deben mostrar por pagina en el monitor
	$sqlNumRegistrosPorPagina 	= "
	SELECT Salnrp
	  FROM ".$wbasedato."_000182
	 WHERE Salcod = '".$monitorSala."'
	 ";
	$resNumRegistrosPorPagina = mysql_query($sqlNumRegistrosPorPagina, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNumRegistrosPorPagina):</b><br>".mysql_error());
	if($rowNumRegistrosPorPagina = mysql_fetch_array($resNumRegistrosPorPagina))
		$numRegistrosPorPagina = (($rowNumRegistrosPorPagina['Salnrp'] != '') ? $rowNumRegistrosPorPagina['Salnrp'] : 5);
	else
		$numRegistrosPorPagina = 6;

	$numRegistrosPorPagina = 11;	// POR AHORA FIJO

	// --> Pintar pantalla para asignar el turno
	echo "
	<input type='hidden' id='wemp_pmla' 				value='".$wemp_pmla."'>
	<input type='hidden' id='monitorSala' 				value='".((isset($monitorSala)) ? $monitorSala : '*')."'>
	<input type='hidden' id='numRegistrosPorPagina' 	value='".$numRegistrosPorPagina."'>
	<input type='hidden' id='arraySalas' 				value='".json_encode($arraySalas)."'>
	<input type='hidden' id='numPagina' 				value='1'>
	<input type='hidden' id='totalPaginas' 				value='1'>";

	//echo "<div id='accordionPrincipal' align='center' style='margin: auto auto;'>
	//	<h1 id='encabezado' align='center' style='background:#75C3EB'>";

	//echo "<center><div id='accordionPrincipal' width='100%'";

	// TÍTULO ANTERIOR	
	/*
    echo "<table width='100%' style='font-size: 4rem;color:#ffffff;font-family: verdana;font-weight:bold;'>
				<tr>
					<td align='left' 	width='15%'>
						<img width='153' heigth='75' src='../../images/medical/root/logoClinicaGrande.png'>
					</td>
					<td align='center' 	width='70%'>
						Atenci&oacuten urgencias
					</td>
					<td id='msjPagina' width='10%' style='font-weight:normal;font-size:2.2rem;color:#000000' align='right'>
					</td>
					<td width='5%'>
						<img width='66' heigth='30' src='../../images/medical/root/Logo_MatrixAzulClaro.png'>
					</td>
				</tr>
			</table>";
	*/
    echo "<table id='titTurnos'>";
	echo "<tr><td id='tdTitLogo'></td>";
	echo "<td id='tdTitDescrip'>&nbsp;ATENCI&Oacute;N<br>URGENCIAS</td>";
	echo "<td id='tdTitFecha' align=right colspan=2>".$diasem."<br>".$wfechaG."</td></tr>";
	echo "</table><br>";

	echo "<div id='divContenido' style='width:100%;text-align:left;'>";
	$respListaTurnos = listarTurnos(1);
	echo $respListaTurnos['html'];
	echo "</div>"; // divContenido
	//echo "</div>divacordf"; // accordionPrincipal

	echo "<center><table id='lineaInf'><tr><td colspan='100%'></td></tr></table>
	<div id='ventanaAlertas' style='display:none' align='center'></div>
	<audio id='sonidoAlerta'><source type='audio/mp3' src='../../images/medical/root/alertaMensaje.mp3' ></audio>
	";
	?>
	</body>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
}

//}//Fin de session
?>
