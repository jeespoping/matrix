<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:	2014-11-10
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='';
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
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	

	include_once("root/comun.php");
	

	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	include_once("actfij/funciones_activosFijos.php");

	$conex 		= obtenerConexionBD("matrix");
	$wbasedato 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'activosFijos');
	$wfecha		= date("Y-m-d");
    $whora 		= date("H:i:s");

	// $formula = "(2.7<3)?(2.7+((0.3<1)?0.3:1)):2.7";
	// eval('$formulaEval='.$formula.';');
	// echo '--->'.$formulaEval;

//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-------------------------------------------------------------------
	//	--> Acordeon principal donde se pinta la ejecucion de un proceso
	//-------------------------------------------------------------------
	function ventanaEjecucion()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		echo "
		<div id='accordionEjecucion' align='left' style='font-family: verdana;font-weight: normal;font-size: 10pt;'>
			<h1 style='font-size: 11pt;'>&nbsp;&nbsp;&nbsp;Ejecución de procesos</h1>
			<div align='center' id='contenidoEjec'>
				<div id='divCuadroMeses'>";
					mesesAbiertos();
		echo "	</div>";

		// --> Pintar lista de procesos a ejecutar
		$arrayLisPro = array();
		$sqlLisPro = "SELECT Subcod, Subdes
						FROM ".$wbasedato."_000030
					   WHERE Subeme = 'on'
					     AND Subest = 'on'
					ORDER BY Subord
		";
		$resListPro = mysql_query($sqlLisPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLisPro):</b><br>".mysql_error());
		while($rowListPro = mysql_fetch_array($resListPro))
			$arrayLisPro[$rowListPro['Subcod']]= utf8_encode($rowListPro['Subdes']);

		echo "	<br>
				<table width='100%'><tr><td align='left' style='color:#48B0E5;font-size:15px'>Procesos a ejecutar:</td></tr></table>
				<table id='tablaProcesosMensuales'>
					<tr>";
					foreach($arrayLisPro as $cod => $nom)
		echo "			<td><input type='checkbox' disabled='disabled' id='procesoMensual_".$cod."'></td><td><b>".$nom.".</b>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "		</tr>
				</table>";

		// --> Pintar pantalla para mostrar información de la ejecución
		echo "	<br>
				<table>
					<tr>
						<td>
							<fieldset align='center' style='padding:15px;width:750px;background-color: #FFFFFF' >
								<legend class='fieldset'>Resultados</legend>
								<div id='consola' style='font-family: verdana;font-size: 8pt;height:220px;overflow:auto;background:none repeat scroll 0 0;'>
								</div>
							</fieldset>
							<br>
						</td>
					</tr>
					<tr><td id='td_progressbar' align='right'></td></tr>
					<tr>
						<td align='center'>
							<button style='font-family: verdana;font-weight:bold;font-size: 9pt;' id='botonCorrerEjecucion' onclick='iniciarEjecucion()'>Ejecutar</button>
							<button style='display:none;font-family: verdana;font-weight:bold;font-size: 9pt;' id='botonGuardarEjecucion' onclick='guardarResultados()'>Guardar resultados</button>
						</td>
					</tr>
				</table>
			</div>
		</div>";
	}

	//-------------------------------------------------------------------
	//	--> Pintar meses abiertos
	//-------------------------------------------------------------------
	function mesesAbiertos($añoSeleccionado='')
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;

		$array_meses	= array( '01'=>'Ene', '02'=>'Feb', '03'=>'Mar', '04'=>'Abr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Ago', '09'=>'Sep', '10'=>'Oct', '11'=>'Nov', '12'=>'Dic');
		$año  			= (($añoSeleccionado != '') ? $añoSeleccionado : date("Y"));

		$periodosCerrados	= obtenerPeriodosCerrados();
		$infoPerAct 		= traerPeriodoActual();
		// --> Pintar tabla para seleccionar meses
		echo '
		<table width="100%">
			<tr>
				<td colspan="6" align="left" style="color:#48B0E5;font-size:15px">Seleccione el periodo:</td>
				<td colspan="6" style="font-size: 11px;" align="right"><b>Periodo actual en el sistema</b>: '.$infoPerAct['ano'].'-'.$infoPerAct['mes'].'</td>
			</tr>
			<tr>
				<td class="cuadroMes" style="cursor:default;font-size: 12px;" colspan="12">
					<select id="año_sel" onChange="mesesAbiertos(this)" style="width:70px;cursor:pointer;background: #E3F1FA; border: 0px ;color: #000000;font-weight: bold;outline: medium none;padding: 1px;text-align: center;">';
						$año_inicio = $año-2;
						for($x=$año_inicio; $x <= $año+2; $x++)
							echo "<option ".(($año==$x)? 'SELECTED':'').">".$x."</option>";
		echo '		</select>
				</td>
			</tr>
			<tr id="listaMeses">';
			foreach($array_meses as $numMes => $nomMes)
				echo '
				<td align="center" class="cuadroMes" style="cursor:'.((array_key_exists($año.'-'.$numMes, $periodosCerrados)) ? 'default' : 'pointer').';font-size: 10px;width:8.3%" id="'.$año.'-'.$numMes.'" onClick="seleccionPeriodo(this)" tipo="'.((array_key_exists($año.'-'.$numMes, $periodosCerrados)) ? 'cerrado' : 'abierto').'">
					'.$nomMes.'<br>
					<img style="margin:3px" width="18" height="18" '.((array_key_exists($año.'-'.$numMes, $periodosCerrados)) ? 'src="../../images/medical/sgc/circuloGris.png"' : 'src="../../images/medical/sgc/circuloVerde.png"').'">
				</td>';
		echo'
			</tr>
		</table>';
	}
	//-------------------------------------------------------------------
	//	--> obtiene un array de periodos ya cerrados
	//-------------------------------------------------------------------
	function obtenerPeriodosCerrados()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$arrayPeriodosCerrados = array();
		// --> Obtener los procesos ya cerrados
		$sqlProCerr   = " SELECT Cieano, Ciemes, Cieest
							FROM ".$wbasedato."_000035
						   WHERE Cieest = 'on'
		";
		$resProCerr = mysql_query($sqlProCerr, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlProCerr):</b><br>".mysql_error());
		while($rowProCerr = mysql_fetch_array($resProCerr))
			$arrayPeriodosCerrados[$rowProCerr['Cieano']."-".$rowProCerr['Ciemes']] = '';

		return $arrayPeriodosCerrados;
	}

//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'obtenerActivos':
		{
			$respuesta = obtenerArrayActivosEjecutar($periodo);
			echo json_encode($respuesta);
			break;
		}
		case 'mesesAbiertos':
		{
			$html = mesesAbiertos($año);
			echo $html;
			break;
		}
		case 'ejecutarProcesoPorActivo':
		{
			$arrayFormulas = json_decode(str_replace('\\', '',  $arrayFormulas), TRUE);
			$respuesta = ejecutarProcesoPorActivo($activo, $arrayFormulas, $periodo);
			echo json_encode($respuesta);
			break;
		}
		case 'obtenerActivosSinIngreso':
		{
			$respuesta		= array("hayActivos" => false, "html");
			$infoPeriodo 	= explode('-', $periodo);

			$respuesta['html'] = "
			<div align='center'>
			<br>
			<table width='95%'>
				<tr class='encabezadoTabla' style='font-weight:bold'><td align='center'>#</td><td align='center'>Registro</td><td align='center'>Nombre</td></tr>
			";

			$colorF = 'fila1';
			$cont	= 1;

			$sqlActSinIng = " SELECT Actreg, Actnom
								FROM ".$wbasedato."_000001
							   WHERE Actano = '".$infoPeriodo[0]."'
							     AND Actmes = '".$infoPeriodo[1]."'
								 AND Actact = 'off'
			";
			$resActSinIng = mysql_query($sqlActSinIng, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActSinIng):</b><br>".mysql_error());
			while($rowActSinIng = mysql_fetch_array($resActSinIng))
			{
				$colorF 					= (($colorF == 'fila2') ? 'fila1' : 'fila2');
				$respuesta['hayActivos'] 	= true;
				$respuesta['html'].= "
				<tr class='".$colorF."'>
					<td>".$cont++."</td><td>".$rowActSinIng['Actreg']."</td><td>".$rowActSinIng['Actnom']."</td>
				</tr>
				";
			}
			$respuesta['html'].= "
			</table><br>
			</div>";

			echo json_encode($respuesta);
			break;
		}
		case 'ejecutarProcesosQuerys':
		{
			$infoPeriodo 	= explode('-', $periodo);
			$perSiguiente 	= traerperiodoSiguiente($infoPeriodo[0], $infoPeriodo[1]);
			$mesSiguiente  	= $perSiguiente['mes'];
			$anoSiguiente	= $perSiguiente['ano'];
			$respuesta  	= array('Errores' => array());

			$valores 	= array("\n", "\t");
			$arrQuerys 	= str_replace($valores, '',  $arrQuerys);
			$arrQuerys 	= json_decode(str_replace("\\", '',  $arrQuerys), TRUE);

			// $camposAfectados 	= str_replace($valores, '',  $camposAfectados);
			// $camposAfectados 	= json_decode(str_replace("\\", '',  $camposAfectados), TRUE);
			// print_r($camposAfectados);
			// return;

			// --> Borrar todas las ejecuciones ya realizadas para el mismo periodo
			$sqlBorrar = "DELETE FROM ".$wbasedato."_000038
						   WHERE Apmano = '".$infoPeriodo[0]."'
						     AND Apmmes = '".$infoPeriodo[1]."' ";
			mysql_query($sqlBorrar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlBorrar):</b><br>".mysql_error());

			if(count($arrQuerys) > 0)
			{
				// --> Recorrer el array de querys y ejecutarlos
				foreach($arrQuerys as $activo => $arrQuerysPorProceso)
				{
					foreach($arrQuerysPorProceso as $codProceso => $querys)
					{
						if(count($querys) > 0)
						{
							$infoProceso 	= explode('-', $codProceso);
							$infoActivo 	= explode('-', $activo);

							// --> Insertar registro de que el activo ejecutó el proceso.
							$sqlEjecPro = " INSERT INTO ".$wbasedato."_000038  (Medico, 			Fecha_data, 	Hora_data, 		Apmano, 				Apmmes, 				Apmsub, 				Apmreg, 				Apmcom, 				Apmeje, Apmest, Seguridad)
																		VALUES ('".$wbasedato."', 	'".$wfecha."', 	'".$whora."', 	'".$infoPeriodo[0]."',	'".$infoPeriodo[1]."',	'".$infoProceso[1]."', '".$infoActivo[0]."',	'".$infoActivo[1]."',	'off', 	'on',	'C-".$wbasedato."')
							";
							$resEjecPro = mysql_query($sqlEjecPro, $conex);
							if(!$resEjecPro)
								$respuesta["Errores"][] = "<b>Activo:<b> ".$activo." <b>Error:</b> guardando datos, ".mysql_error().", en el query ".$sqlEjecPro."";

						}

						foreach($querys as $sqlQuery)
						{
							$resQuery = mysql_query($sqlQuery, $conex);
							if(!$resQuery)
								$respuesta["Errores"][] = "<b>Activo:<b> ".$activo." <b>Error:</b> guardando datos, ".mysql_error().", en el query ".$sqlQuery."";
						}
					}
				}

				if(count($respuesta["Errores"]) == 0)
				{
					// --> Replicar registros
					replicarDatos($infoPeriodo[0], $infoPeriodo[1]);
					//replicarTablas($infoPeriodo[0], $infoPeriodo[1]);
					//return;

					// --> Cerrar periodo y abrir el nuevo periodo
					$sqlCerrarPer = "UPDATE ".$wbasedato."_000035
										SET Cieest = 'on'
									  WHERE Cieano = '".$infoPeriodo[0]."'
										AND Ciemes = '".$infoPeriodo[1]."'
					";
					mysql_query($sqlCerrarPer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCerrarPer):</b><br>".mysql_error());

					$perSiguiente = traerperiodoSiguiente($infoPeriodo[0], $infoPeriodo[1]);
					$mesSiguiente = $perSiguiente['mes'];
					$anoSiguiente = $perSiguiente['ano'];


					// --> Consultar si el periodo ya existe
					$sqlExistePer = "SELECT COUNT(*)
									   FROM ".$wbasedato."_000035
									  WHERE Cieano = '".$anoSiguiente."'
										AND Ciemes = '".$mesSiguiente."'
					";
					$resExistePer = mysql_query($sqlExistePer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlExistePer):</b><br>".mysql_error());
					$rowExistePer = mysql_fetch_array($resExistePer);
					if($rowExistePer[0] > 0)
					{
						// --> Si el periodo existe, actualizarlo
						$sqlActuPer = "UPDATE ".$wbasedato."_000035
										  SET Cieest = 'off'
										WHERE Cieano = '".$anoSiguiente."'
										  AND Ciemes = '".$mesSiguiente."'
						";
						$resActuPer = mysql_query($sqlActuPer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActuPer):</b><br>".mysql_error());
					}
					// --> Sino existe lo creo
					else
					{
						if(!$resActuPer)
						{
							$sqlAbrirPer = "INSERT INTO ".$wbasedato."_000035
														(Medico, 			Fecha_data, 	Hora_data, 		Cieano, 				Ciemes, 				Cieest,	Seguridad, 			id)
												 VALUES ('".$wbasedato."',	'".$wfecha."',	'".$whora."', 	'".$anoSiguiente."',	'".$mesSiguiente."',	'off',	'C-".$wbasedato."', '')

							";
							mysql_query($sqlAbrirPer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAbrirPer):</b><br>".mysql_error());
						}
					}
				}
			}

			echo json_encode($respuesta);
			break;
		}
	}
	return;
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
	  <title>...</title>
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

	var url_add_params 		= addUrlCamposCompartidosTalento();
	var arrQuerys			= new Object();
	var camposAfectados		= new Object();

	$(function(){

		// --> Activar Acordeones
		$("#accordionEjecucion").accordion({
			heightStyle: "auto"
		});
		// --> Activar el buscador de texto, para las variables
		$('#buscarVariable').quicksearch('#tablaListaVariables .find');
		// --> Tooltip
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

	});
	//----------------------------------------------------------------------------------
	//	--> Seleccionar periodo
	//----------------------------------------------------------------------------------
	function seleccionPeriodo(elemento)
	{
		elemento = $(elemento);

		$("#listaMeses td").attr('class', 'cuadroMes');
		$("#listaMeses td").attr('periodoSel', '');

		elemento.attr('class', 'cuadroMesSeleccionado');
		elemento.attr('periodoSel', 'si');

		$("#tablaProcesosMensuales").find("input[type=checkbox]").removeAttr("checked");

		if(elemento.attr("tipo") == "abierto")
		{
			$("#tablaProcesosMensuales").find("input[type=checkbox]").attr("checked", "checked");
			$("#botonCorrerEjecucion").removeAttr("disabled");
		}
		else
			$("#botonCorrerEjecucion").attr("disabled", "disabled");

		$("#botonCorrerEjecucion").show();
		$("#botonGuardarEjecucion").hide();
	}

	//----------------------------------------------------------------------------------
	//	--> Funcion pinta los meses abiertos al cambiar de año
	//----------------------------------------------------------------------------------
	function mesesAbiertos(elemento)
	{
		añoSeleccionado = $(elemento).val();
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			accion		:   'mesesAbiertos',
			año			:	añoSeleccionado
		}, function(respuesta){
			$("#divCuadroMeses").html(respuesta);
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Realiza el llamado a la ejecución de los procesos, mostrando primero cuales
	//		activos no tienen ingreso aun
	//----------------------------------------------------------------------------------
	function iniciarEjecucion()
	{
		// --> Primero consulto si hay activos aun sin hacerles ingreso en el sistema
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			accion:         'obtenerActivosSinIngreso',
			periodo:		$("#listaMeses").find('[periodoSel=si]').attr("id")
		}, function(respuesta){
			if(respuesta.hayActivos)
			{
				// --> Abrir ventana de dialog para mostrar los activos sin ingreso
				$(respuesta.html).dialog({
					show:{
						effect: "blind",
						duration: 100
					},
					hide:{
						effect: "blind",
						duration: 100
					},
					dialogClass: 'fixed-dialog',
					modal: 	true,
					width:	320,
					buttons: {
						"Cancelar": function(){
							$( this ).dialog( "close" );
						},
						"Continuar": function(){
							$( this ).dialog( "close" );
							setTimeout(function(){
								correrEjecucion();
							}, 300);
						}
					},
					title: " <span class='ui-icon ui-icon-alert' style='float:left;'></span>&nbsp;Activos pendientes de ingreso",
					close: function( event, ui ) {
					}
				});
			}
			else
			{
				correrEjecucion();
			}
		}, 'json');
	}
	//----------------------------------------------------------------------------------
	//	--> Funcion que hace el llamado a la ejecucion de los procesos
	//----------------------------------------------------------------------------------
	function correrEjecucion()
	{
		crearProgress();
		// --> Validar que hayan seleccionado un periodo
		if($("#listaMeses").find('[periodoSel=si]').attr("id") == undefined)
		{
			$("#consola").html("- No ha seleccionado ningún periodo");
			return;
		}

		$("#consola").html("- Inicio de la ejecución para "+$("#listaMeses").find('[periodoSel=si]').attr("id")+"...");
		$("#consola").html($("#consola").html()+"<br>- Consultando activos.");

		// --> Primero obtengo un array con todos los activos a ejecutar
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			accion:         'obtenerActivos',
			periodo:		$("#listaMeses").find('[periodoSel=si]').attr("id")
		}, function(respuesta){
			// --> Si hay un error
			if(respuesta.error)
				$("#consola").html($("#consola").html()+"<br>&nbsp;&nbsp;&nbsp;>"+respuesta.mensaje);
			else
			{
				listaActivos 	= respuesta.ListaActivos;
				error 			= false;

				$("#consola").html($("#consola").html()+"<br>- Validando información y calculando procesos.");

				// --> Recorrer cada activo para validar y calcular los procesos y formulas
				for (var regActivo in listaActivos)
				{
					$.ajax(
					{
						url		: "<?=$URL_AUTOLLAMADO?>?"+url_add_params,
						context	: document.body,
						type	: "POST",
						data	:
						{
							consultaAjax:   '',
							accion:         'ejecutarProcesoPorActivo',
							activo:			regActivo,
							arrayFormulas:	JSON.stringify(respuesta.arrayFormulas),
							periodo:		$("#listaMeses").find('[periodoSel=si]').attr("id")
						},
						async	: false,
						dataType: "json",
						success	: function(data){

							// --> Si hay errores, mostrarlos en la consola
							if(data.Errores.length > 0)
							{
								error = true;
								for (y in data.Errores)
									$("#consola").html($("#consola").html()+"<br>&nbsp;&nbsp;&nbsp;> <b>Inconsistencia!!!:</b> "+data.Errores[y]);
							}
							else
							{
								var hayQuerys = false;
								for (key in data.Querys)
									hayQuerys = true;

								if(hayQuerys)
								{
									arrQuerys[regActivo] 	= data.Querys;
									camposAfectados 		= data.camposAfectados;
								}

								$("#consola").html($("#consola").html()+"<br>&nbsp;&nbsp;> <b>Resultados para el activo:</b>&nbsp;"+regActivo+", "+listaActivos[regActivo]);
								// --> Previsualisar formulas
								hayFormulas = false;
								for (z in data.Formulas)
								{
									// --> Nombre del proceso
									$("#consola").html($("#consola").html()+"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>-&nbsp;"+data.nomProceso[z]+":</b>");

									// --> Pintar formulas
									for(x in data.Formulas[z])
									{
										hayFormulas = true;
										$("#consola").html($("#consola").html()+"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>"+(x*1+1)+")</b> &nbsp;<span style='color: #2674CE'>"+data.Formulas[z][x].nomFormula+":</span>&nbsp;"+data.Formulas[z][x].formula);
									}
								}

								if(!hayFormulas)
									$("#consola").html($("#consola").html()+"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbspNo hay fórmulas para aplicar.");
							}
							// --> Cargar barra de progreso
							progress(respuesta.totalElementos);
						}
					});
				}
				//JSON.stringify(arrQuerys)
				if(!error)
				{
					$("#botonCorrerEjecucion").hide();
					$("#botonGuardarEjecucion").show();
				}
			}
		}, 'json');

		$("#contenidoEjec").height(530);
	}
	//-------------------------------------------------------------------
	//	--> Guardar resultados y cerrar periodo
	//-------------------------------------------------------------------
	function guardarResultados()
	{
		if(confirm("¿Está seguro que quiere guardar los resultados?"))
		{
			$("#consola").html($("#consola").html()+"<br>- Guardando resultados.");
			$("#consola").html($("#consola").html()+"<br>- Cerrando periodo.");

			// --> Realizo la ejecucion de los querys
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax	:   '',
				accion			:   'ejecutarProcesosQuerys',
				arrQuerys		:	JSON.stringify(arrQuerys),
				camposAfectados	:	JSON.stringify(camposAfectados),
				periodo			:	$("#listaMeses").find('[periodoSel=si]').attr("id")
			}, function(respEjecuQuerys){

				// --> Si ocurrio algun error
				if(respEjecuQuerys.Errores.length > 0)
					for (z in respEjecuQuerys.Errores)
						$("#consola").html($("#consola").html()+"<br>&nbsp;&nbsp;&nbsp;> "+respEjecuQuerys.Errores[z]);
				else
				{
					$("#consola").html($("#consola").html()+"<br>- Ejecución finalizada exitosamente.");
					mesesAbiertos($("año_sel"));
					$("#botonCorrerEjecucion").show();
					$("#botonGuardarEjecucion").hide();
					arrQuerys	= new Object();
				}
			}, 'json');
		}
	}

	//-------------------------------------------------------------------
	//	--> CREAR LA BARRA DE PROGRESO
	//-------------------------------------------------------------------
	function crearProgress()
	{
		$("#td_progressbar").html("<div id='progressbar' style='width:300px;height:20px;' align='center' ><div align='center' id='progressbar2' class='progress-label'>Proceso de validación...</div></div>");
		progressbar 	= $( "#progressbar" ),
		progressLabel 	= $( "#progressbar2" );
		progressbar.progressbar({
			value: false,
			change: function(){
				var porcentajeCompletado = progressbar.progressbar( "value" );
				porcentajeCompletado = parseInt(porcentajeCompletado);
				progressLabel.text( " Ejecutando... "+porcentajeCompletado+" %");
			},
			complete: function() {
				progressLabel.text( "100% Proceso finalizado!" );
			}
		});
		$("#td_progressbar").show();
		$("#progressbar2").show();
	}
	//-------------------------------------------------------------------
	//	--> AUMENTA LA BARRA DE PROGRESO
	//-------------------------------------------------------------------
	function progress(cantidad)
	{
		var val 	= progressbar.progressbar( "value" ) || 0;
		var cant 	= 100/cantidad;
		progressbar.progressbar("value", val + cant);
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
		.cuadroMes{
			cursor:pointer;background: #E3F1FA; border: 1px solid #62BBE8;color: #000000;font-weight: normal;outline: medium none;margin: 1px; padding: 2px;text-align: center;
		}
		.cuadroMesSeleccionado{
			cursor:pointer;background: #62BBE8; border: 1px solid #2694E8;color: #FFFFFF;font-weight: bold;outline: medium none;margin: 1px; padding: 2px;text-align: center;
		}
		.ui-progressbar {
		position: relative;
		}
		.progress-label {
			position:absolute;z-index:5000
			left: 50%;
			top: 4px;
			font-family: verdana;
			font-weight:normal;
			color: #2694e8;
			font-size: 9pt;
		}

		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 6pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}
		.ui-effects-transfer { border: 2px dotted gray; border-color:#74DE3D; }
		// --> Estylo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=valorCalculado]::-webkit-input-placeholder {color:gray; background:#FCEAED;font-size:8pt}
		/*Firefox*/
		[tipo=otro]::-moz-placeholder {color:#000000; background:#E5F6FF;font-size:8pt}
		[tipo=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		[tipo=valorCalculado]::-moz-placeholder {color:#000000; background:#FCEAED;font-size:8pt}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=valorCalculado]:-ms-input-placeholder {color:gray; background:#FCEAED;font-size:8pt}
		[tipo=valorCalculado]:-moz-placeholder {color:gray; background:#FCEAED;font-size:8pt}

	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	echo "<input type='hidden' id='hiddenFechaActual' value='".$wfecha."'>";
	echo '
	<table width="70%">
		<tr>
			<td valign="top" id="">';
				ventanaEjecucion();
	echo '	</td>
		</tr>
	</table>
	';
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
