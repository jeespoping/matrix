<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        REGISTRO DE ACOMPAÑANTES A LOS PACIENTES DE URGENCIAS
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2016-01-15';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
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
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];

	

	include_once("root/comun.php");
	


	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-------------------------------------------------------------------
	// -->	Pinta la lista de pacientes en urgencias
	//-------------------------------------------------------------------
	function obtenerListaDePacientes()
	{
		global $wuse;
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wbasedatoHce;
		global $arraySalas;
		global $arrayConductas;

		echo "
		<span style='font-family: verdana;font-size: 10pt;'>
			<b>Buscar:&nbsp;</b>
			<input id='buscarPaciente' type='text' onfocus='desactivarActualizar()' onblur='activarActualizar()' placeholder='Digite palabra a buscar' style='border-radius: 4px;border:1px solid #AFAFAF;width:300px'>
			&nbsp;&nbsp;|&nbsp;
			<b>Actualizar</b>&nbsp;<img width='14px' height='14px' src='../../images/medical/sgc/Refresh-128.png' style='cursor:pointer' title='Actualizar listado.' onclick='cargarListaPacientes()'>
		</span>
		<br><br>
		<table width='100%' align='center' id='tableListaPacientes'>
			<tr class='encabezadoTabla' align='center'>
				<td>Fecha<br>Hora de Ingreso</td><td>Turno</td><td>Historia</td><td>Paciente</td><td>Estado</td><td>Sala-Ubicaci&oacute;n</td><td>Especialidad</td><td colspan='2'>Acompa&ntilde;antes</td>
			</tr>
		";
		$colorFila		= "fila1";
		$sqlListaPac 	= "
		SELECT Ubihis, Ubiing, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) AS NombrePac, E.Fecha_data, E.Hora_data,
			   Habcpa, Habzon, Mtrcon, Mtrcur, Mtrmed, Mtrtri, Mtrfco, Mtrtur, Mtreme
		  FROM ".$wbasedato."_000011, ".$wbasedato."_000018, root_000036, root_000037,
			   ".$wbasedatoHce."_000022 AS E LEFT JOIN ".$wbasedato."_000020 AS F ON E.Mtrhis = F.Habhis AND E.Mtring = F.Habing
		 WHERE Ccourg = 'on'
		   AND Ubisac = Ccocod
		   AND Ubimue != 'on'
		   AND Ubiald != 'on'
		   AND Orihis = Ubihis
		   AND Oriing = Ubiing
		   AND Oriori = '".$wemp_pmla."'
		   AND Oriced = Pacced
		   AND Oritid = Pactid
		   AND Mtrhis = Ubihis
		   AND Mtring = Ubiing

		 ORDER BY CONCAT(E.Fecha_data, ' ', E.Hora_data) DESC ";

		$resListaPac = mysql_query($sqlListaPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlListaPac):</b><br>".mysql_error());
		while($rowListaPac = mysql_fetch_array($resListaPac))
		{
			$htmlTr 	= "";
			$mostrarTr 	= true;

			$htmlTr.= "
			<tr class='".$colorFila." find'>
				<td align='center'>".$rowListaPac['Fecha_data']."<br>".$rowListaPac['Hora_data']."</td>
				<td align='center'>".substr($rowListaPac['Mtrtur'], 7)."</td>
				<td align='center'>".$rowListaPac['Ubihis']."-".$rowListaPac['Ubiing']."</td>
				<td id='nomAcompa".$rowListaPac['Ubihis']."-".$rowListaPac['Ubiing']."'>&nbsp;<b>".ucwords(strtolower($rowListaPac['NombrePac']))."</b></td>";

				if($rowListaPac['Mtrcon'] != '')
					$htmlTr.= "<td>".ucfirst(strtolower($arrayConductas[$rowListaPac['Mtrcon']]))."</td>";
				else
				{
					$consultarSalaEspera = false;

					// --> Si el turno esta en consulta en este momento
					if($rowListaPac['Mtrcur'] == 'on')
					{
						$estado = "En consulta";

						// --> obtengo el puesto de trabajo del medico que lo esta atendiendo
						$sqlEnConsulta = "
						SELECT Puenom
						  FROM ".$wbasedato."_000180
						 WHERE Pueusu = '".$rowListaPac['Mtrmed']."'
						";
						$resEnConsulta = mysql_query($sqlEnConsulta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEnConsulta):</b><br>".mysql_error());
						if($rowEnConsulta = mysql_fetch_array($resEnConsulta))
							$rowListaPac['Habcpa'] = $rowEnConsulta['Puenom'];
					}
					else
					{
						// --> Si ya tiene un triagge asignado y no tiene fecha de consulta.
						if(trim($rowListaPac['Mtrtri'] != '') && $rowListaPac['Mtrfco'] == '0000-00-00')
						{
							// --> Indica que el turno esta en espera de consulta
							$estado 				= "Pendiente de consulta";
							$consultarSalaEspera 	= true;
						}
						elseif(trim($rowListaPac['Mtrtri'] == ''))
						{
							// --> Indica que el turno esta en espera de triagge
							$estado 				= "Pendiente de triage";
							$consultarSalaEspera 	= true;
						}

						if($consultarSalaEspera)
						{
							$sqlSalaEspera = "
							SELECT Salnom, Salext
							  FROM ".$wbasedato."_000178 AS A LEFT JOIN ".$wbasedato."_000182 AS B ON A.Atusea = B.Salcod
							 WHERE Atutur = '".$rowListaPac['Mtrtur']."'
							";
							$resSalaEspera = mysql_query($sqlSalaEspera, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalaEspera):</b><br>".mysql_error());
							if($rowSalaEspera = mysql_fetch_array($resSalaEspera))
							{
								$rowListaPac['Habcpa'] = 'Sala '.ucfirst(strtolower($rowSalaEspera['Salnom']));

								if($estado == 'Pendiente de triage' && $rowSalaEspera['Salext'] == 'on')
									$mostrarTr = false;
							}
						}
					}

					$htmlTr.= "<td>&nbsp;".$estado."</td>";
				}

				// --> Pintar ubicación
				$htmlTr.= "
				<td>".ucfirst(strtolower($arraySalas[$rowListaPac['Habzon']])).(($arraySalas[$rowListaPac['Habzon']] != '') ? "-" : "" ).ucfirst(strtolower($rowListaPac['Habcpa']))."</td>";

				// --> Pintar especialidad

				$sqlNomEspecialidad	= "
				SELECT Espnom
				  FROM ".$wbasedato."_000044
				 WHERE Espcod = '".$rowListaPac['Mtreme']."'
				";
				$resNomEspecialidad = mysql_query($sqlNomEspecialidad, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomEspecialidad):</b><br>".mysql_error());
				if($rowNomEspecialidad = mysql_fetch_array($resNomEspecialidad))
					$htmlTr.= "<td>&nbsp;".ucfirst(strtolower($rowNomEspecialidad['Espnom']))."</td>";
				else
					$htmlTr.= "<td align='center'></td>";

				$htmlTr.= "<td align='center'>";

				// --> Consultar acompañantes del paciente
				$primeraVez 		= true;
				$sqlAcompañantes 	= "
				SELECT Aconom, id
				  FROM ".$wbasedato."_000185
				 WHERE Acohis = '".$rowListaPac['Ubihis']."'
				   AND Acoing = '".$rowListaPac['Ubiing']."'
				   AND Acoest = 'on'
				";
				$resAcompañantes = mysql_query($sqlAcompañantes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAcompañantes):</b><br>".mysql_error());
				while($rowAcompañantes = mysql_fetch_array($resAcompañantes))
				{
					$title = "	<table>
									<tr class=fila1 align=center>
										<td>".$rowAcompañantes['Aconom']."</td>
									</tr>
									<tr class=fila2 align=center>
										<td style=color:RED><b>&iquest; Sale ?</b></td>
									</tr>
								</table>";
					$htmlTr.= (($primeraVez) ? "" : "&nbsp;&nbsp;")."<img style='cursor:pointer' id='imagenPersona".$rowAcompañantes['id']."' class='tooltip' title='".$title."' width='12px' heigth='16px' src='../../images/medical/root/personaOn.png' onClick='entrarSalirAcompanante(\"off\", \"".$rowAcompañantes['id']."\", \"\", \"\", \"".$rowAcompañantes['Aconom']."\");'>";
					$primeraVez = false;
				}

				$htmlTr.= "
				</td>
				<td align='center'>
					<img style='cursor:pointer' width='16px' heigth='16px' src='../../images/medical/root/Pencil.png' onClick='editarAcompanantes(\"".$rowListaPac['Ubihis']."\", \"".$rowListaPac['Ubiing']."\")'>
				</td>
			</tr>
			";

			if($mostrarTr)
			{
				$colorFila 	= (($colorFila == 'fila1') ? 'fila2' : 'fila1');
				echo $htmlTr;
			}
		}

		echo "
		</table>";
	}

//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	A J A X
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'editarAcompanantes':
		{
			// --> Ingresar nuevo acompañante
			echo "
			<br>
			<fieldset align='center' id='' style='padding:15px;'>
				<legend class='fieldset'>Ingresar nuevo acompañante</legend>
				<table width='100%'>
					<tr>
						<td class='fila1'>Documento:&nbsp;</td>
						<td class='fila2' align='center'>
							&nbsp;<input type='text' id='numDocumento' placeholder='Digite el número del documento' style='width:250px;font-size: 10pt'>&nbsp;
						</td>
					</tr>
					<tr>
						<td class='fila1'>Nombre:&nbsp;</td>
						<td class='fila2' align='center'>
							&nbsp;<input type='text' id='nombreAcomp' placeholder='Digite el nombre del acompañante' style='width:250px;font-size: 10pt'>&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='2' align='center'>
							<button style='font-family: verdana;font-weight:bold;font-size: 9pt;cursor:pointer' onclick='registrarAcompanante(\"".$historia."\", \"".$ingreso."\")'>
								Ingresar&nbsp;
								<img style='cursor:pointer' width='16px' heigth='16px' src='../../images/medical/root/grabar.png'>
							</button>
						</td>
					</tr>
				</table>
			</fieldset>
			";

			// --> Pintar acompañantes
			echo "
			<br>
			<fieldset align='center' id='' style='padding:15px;'>
				<legend class='fieldset'>Historial de acompañantes</legend>
				<table width='100%'>
					<tr align='center' style='font-weight:bold' class='encabezadoTabla'><td width='30%'>Documento</td><td width='45%'>Nombre</td><td>Acción</td><td>Log	</td></tr>";

			// --> Consultar los acompañantes del paciente
			$sqlAcompañantes = "
			SELECT Aconom, Acodoc, Acoest, id, Acolog
			  FROM ".$wbasedato."_000185
			 WHERE Acohis = '".$historia."'
			   AND Acoing = '".$ingreso."'
			";
			$resAcompañantes = mysql_query($sqlAcompañantes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAcompañantes):</b><br>".mysql_error());
			while($rowAcompañantes = mysql_fetch_array($resAcompañantes))
			{
				if($rowAcompañantes['Acoest'] == 'on')
				{
					$boton = "
					<button style='font-family: verdana;font-weight:bold;font-size: 7pt;cursor:pointer' onClick='entrarSalirAcompanante(\"off\", \"".$rowAcompañantes['id']."\", \"".$historia."\", \"".$ingreso."\", \"".$rowAcompañantes['Aconom']."\")'>
						Sale
						<img style='cursor:pointer' width='10px' heigth='10px' src='../../images/medical/eliminar1.png'>&nbsp;&nbsp;
					</button>
					";
				}
				else
				{
					$boton = "
					<button style='font-family: verdana;font-weight:bold;font-size: 7pt;cursor:pointer' onClick='entrarSalirAcompanante(\"on\", \"".$rowAcompañantes['id']."\", \"".$historia."\", \"".$ingreso."\", \"".$rowAcompañantes['Aconom']."\")'>
						Entra
						<img style='cursor:pointer' width='10px' heigth='10px' src='../../images/medical/root/grabar.png'>
					</button>
					";
				}

				if($rowAcompañantes['Acolog'] != '')
					$arrayLog = json_decode($rowAcompañantes['Acolog'], true);
				else
					$arrayLog = array("ENTRO" => array(), "SALIO" => array());

				$log = "
				<table>
					<tr align=center class=encabezadoTabla style=font-size:8pt><td>#</td><td>Entró</td><td>Salió</td></tr>
				";

				foreach($arrayLog['ENTRO'] as $clave => $valor)
				{
					$colorFila1 = (($colorFila1 == 'fila2') ? 'fila1' : 'fila2');
					$log.= "<tr class=".$colorFila1." style=font-size:8pt><td>".($clave+1)."</td><td>".$valor."</td><td>".$arrayLog['SALIO'][$clave]."</td></tr>";
				}

				$log.= "</table>";

				$colorFila 	= (($colorFila == 'fila2') ? 'fila1' : 'fila2');

				echo "
					<tr class='".$colorFila."'>
						<td class='tooltip' title='Doble click para editar.' campo='Acodoc' ondblclick='editarDatoAcompanante(this, \"".$rowAcompañantes['id']."\")' style='cursor:pointer'>".$rowAcompañantes['Acodoc']."</td>
						<td class='tooltip' title='Doble click para editar.' campo='Aconom' ondblclick='editarDatoAcompanante(this, \"".$rowAcompañantes['id']."\")' style='cursor:pointer'>".$rowAcompañantes['Aconom']."</td>
						<td align='center'>".$boton."</td>
						<td align='center'>
							<img style='cursor:help' class='tooltip' title='".$log."' width='23px' heigth='23px' src='../../images/medical/root/Search.png'>
						</td>
					</tr>";
			}

			if(mysql_num_rows($resAcompañantes) < 1)
				echo "<tr><td class='fila2' colspan='3' align='center'>El paciente aún no ha tenido acompañantes</td></tr>";

			echo "
				</table>
			</fieldset>
			";

			break;
			return;
		}
		case 'registrarAcompanante':
		{
			$Log 			= array("ENTRO" => array(), "SALIO" => array());
			$Log["ENTRO"][]	= date("Y-m-d H:i:s");
			$Log			= json_encode($Log);

			$sqlRegistrar 	= "
			INSERT INTO ".$wbasedato."_000185 (Medico, 				Fecha_data, 			Hora_data, 				Acohis, 			Acoing, 		Aconom,				Acodoc, 				Acoest, Acolog, 	Seguridad)
										VALUES('".$wbasedato."', 	'".date("Y-m-d")."', 	'".date("H:i:s")."',	'".$historia."',	'".$ingreso."',	'".$nombreAcomp."', '".$numDocumento."',	'on', 	'".$Log."', 'C-".$wbasedato."')
			";
			mysql_query($sqlRegistrar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegistrar):</b><br>".mysql_error());

			break;
			return;
		}
		case 'cargarListaPacientes':
		{
			$arraySalas 	= json_decode(str_replace('\\', '', $arraySalas), true);
			$arrayConductas = json_decode(str_replace('\\', '', $arrayConductas), true);
			obtenerListaDePacientes();
			break;
			return;
		}
		case 'entrarSalirAcompanante':
		{
			// --> Consultar log
			$sqlLog = "
			SELECT Acolog
			  FROM ".$wbasedato."_000185
			 WHERE id = '".$idRegistro."'
			";
			$resLog = mysql_query($sqlLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLog):</b><br>".mysql_error());
			$rowLog = mysql_fetch_array($resLog);
			$log	= json_decode($rowLog['Acolog'], true);

			if($movimiento == 'on')
				$log["ENTRO"][] = date("Y-m-d H:i:s");
			else
				$log["SALIO"][] = date("Y-m-d H:i:s");


			$sqlRegEntrSali = "
			UPDATE ".$wbasedato."_000185
			   SET Acoest = '".$movimiento."',
			       Acolog = '".json_encode($log)."'
			 WHERE id = '".$idRegistro."'
			";
			mysql_query($sqlRegEntrSali, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegEntrSali):</b><br>".mysql_error());

			break;
			return;
		}
		case 'actualizarDatoAcompanante':
		{
			$sqlActNom = "
			UPDATE ".$wbasedato."_000185
			   SET ".$campo." = '".$textoAcompa."'
			 WHERE id = '".$idRegistro."'
			";
			mysql_query($sqlActNom, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActNom):</b><br>".mysql_error());

			break;
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
	  <title>Registro de acompa&ntilde;antes, urgencias</title>
	</head>

	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
	<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	var actualizarLista;
	$(function(){

		// --> Activar acordeon
		$("#accordionListaPac").accordion({
			heightStyle: "fill"
		});

		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

		// --> Activar el buscador de texto
		$('#buscarPaciente').quicksearch('#tableListaPacientes .find');

		activarActualizar();

	});

	//----------------------------------------------------------------
	// --> Permite editar el nombre de un acompañante
	//----------------------------------------------------------------
	function editarDatoAcompanante(elemento, idRegistro)
	{
		textoCampo = $(elemento).text();
		$(elemento).html("<input type='text' style='width:250px;font-size: 10pt' onblur='actualizaDatoAcompanante(this, "+idRegistro+", \""+textoCampo+"\")' value='"+textoCampo+"'>");
		$(elemento).children().focus();
	}
	//----------------------------------------------------------------
	// --> Actualiza el nombre de un acompañante
	//----------------------------------------------------------------
	function actualizaDatoAcompanante(elemento, idRegistro, textoAcompaAnterior)
	{
		textoAcompa = $(elemento).val();
		if(textoAcompa != '')
		{
			$.post("registroAcompanantesUrgencias.php",
			{
				consultaAjax:   		'',
				accion:         		'actualizarDatoAcompanante',
				wemp_pmla:        		$('#wemp_pmla').val(),
				textoAcompa:			textoAcompa,
				idRegistro:				idRegistro,
				campo:					$(elemento).parent().attr("campo")
			}, function(respuesta){
				$(elemento).parent().html(textoAcompa);
			});
		}
		else
			$(elemento).parent().html(textoAcompaAnterior);
	}
	//----------------------------------------------------------------
	// --> Actualizacion de la lista de pacientes
	//----------------------------------------------------------------
	function activarActualizar()
	{
		actualizarLista = setInterval(function(){
			cargarListaPacientes();
		}, 80000);
	}
	//----------------------------------------------------------------
	// --> Desactivar actualizacion de la lista de pacientes
	//----------------------------------------------------------------
	function desactivarActualizar()
	{
		clearInterval(actualizarLista);
	}
	//----------------------------------------------------------------
	// -->	Editar los acompañantes del paciente
	//----------------------------------------------------------------
	function editarAcompanantes(historia, ingreso)
	{
		$.post("registroAcompanantesUrgencias.php",
		{
			consultaAjax:   		'',
			accion:         		'editarAcompanantes',
			wemp_pmla:        		$('#wemp_pmla').val(),
			historia:				historia,
			ingreso:				ingreso
		}, function(respuesta){
			desactivarActualizar();
			$("#divEditarAcompanantes").html(respuesta).dialog({
				title: "<div align='left' style='font-weight:normal'><b>Paciente:</b>&nbsp&nbsp&nbsp<span style='color:#2A5DB0;font-weight:bold'>"+$("#nomAcompa"+historia+"-"+ingreso).text()+"</span></div>",
				width: 600,
				modal: true,
				close: function( event, ui ) {
					cargarListaPacientes();
					activarActualizar();
				}
			});

			// --> Regex de solo enteros
			// $("#numDocumento").keyup(function(){
				// if ($(this).val() !="")
					// $(this).val($(this).val().replace(/[^0-9]/g, ""));
			// });

			// --> Tooltip
			setTimeout(function() {
				$('.tooltip', $("#divEditarAcompanantes")).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			}, 200);
		});
	}

	//----------------------------------------------------------------
	// -->	Registrar un nuevo acompañante para el paciente
	//----------------------------------------------------------------
	function registrarAcompanante(historia, ingreso)
	{
		if($("#nombreAcomp").val() == '' || $("#numDocumento").val() == '')
			return;

		$.post("registroAcompanantesUrgencias.php",
		{
			consultaAjax:   		'',
			accion:         		'registrarAcompanante',
			wemp_pmla:        		$('#wemp_pmla').val(),
			historia:				historia,
			ingreso:				ingreso,
			nombreAcomp:			$("#nombreAcomp").val(),
			numDocumento:			$("#numDocumento").val()
		}, function(respuesta){
			editarAcompanantes(historia, ingreso);
		});
	}
	//----------------------------------------------------------------
	// -->	Pinta la lista de pacientes
	//----------------------------------------------------------------
	function cargarListaPacientes()
	{
		$.post("registroAcompanantesUrgencias.php",
		{
			consultaAjax:   		'',
			accion:         		'cargarListaPacientes',
			wemp_pmla:        		$('#wemp_pmla').val(),
			arraySalas:				$("#arraySalas").val(),
			arrayConductas:			$("#arrayConductas").val()
		}, function(respuesta){
			var busqueda = $("#buscarPaciente").val();
			$("#divListaPacientes").hide()
			$("#divListaPacientes").html(respuesta);

			// --> Activar el buscador de texto
			$('#buscarPaciente').quicksearch('#tableListaPacientes .find');
			$("#buscarPaciente").val(busqueda).trigger("keyup");
			setTimeout(function() {
				$("#divListaPacientes").show();
				$("#divListaPacientes").height($("#divListaPacientes").height()+5);
			}, 100);

			// --> Tooltip
			$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		});
	}

	//----------------------------------------------------------------
	// -->	Registrar la entrada o salida de un acompañante
	//----------------------------------------------------------------
	function entrarSalirAcompanante(movimiento, idRegistro, historia, ingreso, nombreAcomp)
	{
		if(confirm(nombreAcomp+".\nConfirmar "+((movimiento=='on') ? "entrada" : "salida")+"?"))
		{
			$.post("registroAcompanantesUrgencias.php",
			{
				consultaAjax:   		'',
				accion:         		'entrarSalirAcompanante',
				wemp_pmla:        		$('#wemp_pmla').val(),
				idRegistro:				idRegistro,
				movimiento:				movimiento
			}, function(respuesta){
				if(historia != '' && ingreso != '')
					editarAcompanantes(historia, ingreso);
				else
					$("#imagenPersona"+idRegistro).remove();
			});
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
		.fila1
		{
			background-color: #C3D9FF;
			color: #000000;
			font-size: 9pt;
			padding:2px;
			font-family: verdana;
		}
		.fila2
		{
			background-color: #E8EEF7;
			color: #000000;
			font-size: 9pt;
			padding:3px;
			font-family: verdana;
		}

		.encabezadoTabla {
			background-color: #2a5db0;
			color: #ffffff;
			font-size: 9pt;
			padding:3px;
			font-family: verdana;
		}
		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:3px;
		}
		.bordeCurvo{
			-moz-border-radius: 0.4em;
			-webkit-border-radius: 0.4em;
			border-radius: 0.4em;
		}

		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 6pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}
		.ui-effects-transfer { border: 2px dotted gray; border-color:#74DE3D; }

		// --> Estylo para los placeholder
		/*Firefox*/
		[tipo=otro]::-moz-placeholder {color:#000000; background:#E5F6FF;font-size:8pt}
		::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	// --> Consultar maestro de salas
	$arraySalas 	= array();
	$arrayConductas = array();

	$sqlConductas = "
	SELECT Concod, Condes
	  FROM ".$wbasedatoHce."_000035
	 WHERE Conest = 'on'
	";
	$resConductas = mysql_query($sqlConductas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConductas):</b><br>".mysql_error());
	while($rowConductas = mysql_fetch_array($resConductas))
		$arrayConductas[$rowConductas['Concod']] = $rowConductas['Condes'];

	$sqlSalas 	= "
	SELECT Arecod, Aredes
	  FROM ".$wbasedato."_000169
	 WHERE Areest = 'on' ";
	$resSalas = mysql_query($sqlSalas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalas):</b><br>".mysql_error());
	while($rowSalas = mysql_fetch_array($resSalas))
		$arraySalas[$rowSalas['Arecod']] = $rowSalas['Aredes'];

	// --> Variables hidden
	echo "
	<input type='hidden' id='wemp_pmla' 				value='".$wemp_pmla."'>
	<input type='hidden' id='arraySalas' 				value='".json_encode($arraySalas)."'>
	<input type='hidden' id='arrayConductas' 			value='".json_encode($arrayConductas)."'>
	";

	// -->	ENCABEZADO
	encabezado("Registro de acompa&Ntilde;antes, urgencias.", $wactualiz, 'clinica');
	echo "
	<br>
	<div align='center'>
		<table width='90%'>
			<tr>
				<td>
					<div id='accordionListaPac' align='left' style='font-family: verdana;font-weight: normal;font-size: 10pt;'>
						<h1 style='font-size: 11pt;'>&nbsp;&nbsp;&nbsp;Pacientes en urgencias</h1>
						<div id='divListaPacientes' style='font-family: verdana;font-weight: normal;' align='left'>";
							obtenerListaDePacientes();
	echo "					<br>
						</div>
					</div>
					<br>
				</td>
			</tr>
		</table><br>
		<input type='button' value='Cerrar Ventana' onclick='window.close()'>
		<br>
	</div>
	<div id='divEditarAcompanantes'></div>
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
