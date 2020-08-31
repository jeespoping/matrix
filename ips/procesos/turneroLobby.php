<?php
include_once("conex.php");
//=========================================================================================================================================\\
//       	TURNERO DEL LOBBY
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:	2018-01-16
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------
// 2020-02-21 Arleyda Insignares C.
//            Se adiciona botón atras para ser llamado desde otro programa 'Visualizar Turnero x piso', para usar varios 
//            turneros por piso.
// 2020-01-13 Arleyda Insignares C.
//            Se crea el campo 'codlog' en cliame_000305 para indicar el nombre del logo que debe mostrar 
//            en la vista al usuario.                                                
			$wactualiz='2020-01-13';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


/*if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{*/
	//$user_session 	= 			explode('-',$_SESSION['user']);
	$wuse 			= 'Turnero';	//$user_session[1];
	

	include_once("root/comun.php");
	

	$conex 			= obtenerConexionBD("matrix");
	$wbasedato	 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//------------------------------------------------------------------------------------
	//	--> Consultar los servicios dependientes
	//------------------------------------------------------------------------------------
	function serviciosDependietes($codServicio)
	{
		global $wemp_pmla;
		global $conex;
		global $wbasedato;
		global $tema;
		
		$arrServicios = array();
		$sqlServicios = "
		SELECT Seccod, Secnom
		  FROM ".$wbasedato."_000298 INNER JOIN ".$wbasedato."_000310 ON(Sertem = Rsstem AND Sercod = Rssser AND Rssest = 'on')
			   INNER JOIN ".$wbasedato."_000309 ON(Rsssec = Seccod AND Secest = 'on') 
		 WHERE Sertem = '".$tema."'
		   AND Sercod = '".$codServicio."'
		   AND Serest = 'on'
		";
		$resServicios = mysql_query($sqlServicios, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlServicios):</b><br>".mysql_error());
		while($rowServicios = mysql_fetch_array($resServicios))
			$arrServicios[$rowServicios['Seccod']] = strtoupper($rowServicios['Secnom']);
		
		return $arrServicios;
	}
	
	//------------------------------------------------------------------------------------
	//	--> Genera el html del tiquete del turno
	//------------------------------------------------------------------------------------
	function htmlTurno($turno, $tipDocumento, $numDocumento, $nombrePaciente, $reimpresion, $nomServicio, $nomTema, $nomServicioSecundario, $desPiso, $nomlogo)
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
					<img width='118' heigth='58' src='../../images/medical/root/".$nomlogo."' style='background-color: rgb(50,50,50);'>
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
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R 	P O S T    J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'obtenerNombrePaciente':
		{
			$respuesta = array("nombrePac" => '');

			$sqlNomPac = "
			SELECT CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2, ' ') AS nombrePac
			  FROM root_000036
			 WHERE Pacced = '".$numDocumento."'
			   AND Pactid = '".$tipDocumento."'
			 UNION
			SELECT CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2, ' ') AS nombrePac
			  FROM ".$wbasedato."_000100
			 WHERE Pacdoc = '".$numDocumento."'
			   AND Pactdo = '".$tipDocumento."'
			";
			$respuesta['sqlNomPac'] = $sqlNomPac;
			$resNomPac = mysql_query($sqlNomPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomPac):</b><br>".mysql_error());
			if($rowNomPac = mysql_fetch_array($resNomPac))
				$respuesta['nombrePac'] = $rowNomPac['nombrePac'];

			echo json_encode($respuesta);

			break;
			return;
		}
		case 'generarTurno':
		{
			$respuesta = array('Error' => false, 'yaExisteTurnoHoy' => false, 'Mensaje' => '', 'Turno' => '', 'fichoTurno' => '');

			// --> Obtener el prefijo del tipo de servicio
			$prefijo = "";
			$desPiso = "";
			$sqlPrefijo = " 
			SELECT Serpre, Sernom, Serpis
			  FROM ".$wbasedato."_000298
			 WHERE Sertem = '".$tema."' 
			   AND Sercod = '".$tipoServicio."'
			";
			$resPrefijo = mysql_query($sqlPrefijo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPrefijo):</b><br>".mysql_error());

			if($rowPrefijo = mysql_fetch_array($resPrefijo))
			{
				$prefijo 		= $rowPrefijo['Serpre'];
				$nomServicio 	= utf8_encode($rowPrefijo['Sernom']);
				$desPiso        = $rowPrefijo['Serpis'];
			}

		
			$nomTema = "";
			$sqlTema = "
			SELECT Codnom,Codlog
			  FROM ".$wbasedato."_000305
			 WHERE Codtem = '".$tema."'
			   AND Codest = 'on'
			";
			$resTema = mysql_query($sqlTema, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTema):</b><br>".mysql_error());
			if( $rowTema = mysql_fetch_array($resTema) )
			{
				$nomTema = $rowTema['Codnom'];
				$nomlogo = $rowTema['Codlog'];
			}
		
			// --> Bloquear tabla de turnos
			$sqlBloque = "
			LOCK TABLES ".$wbasedato."_000304 WRITE;
			";
			mysql_query($sqlBloque, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlBloque):</b><br>".mysql_error());
					
			// --> Obtener el ultimo consecutivo
			$sqlObtConsec = " 
			SELECT MAX(REPLACE(Turtur, '-".$prefijo."', '')*1) AS turno
			  FROM ".$wbasedato."_000304
			 WHERE Turtem = '".$tema."' 
			   AND Turtur LIKE '".date('ymd')."%'
			   AND Turtse = '".$tipoServicio."'
			";
			$resObtConsec = mysql_query($sqlObtConsec, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtConsec):</b><br>".mysql_error());
			if($rowObtConsec = mysql_fetch_array($resObtConsec))
			{
				$fechaUltiConse = substr($rowObtConsec['turno'], 0, 6);
				$ultConsecutivo = (($fechaUltiConse == date('ymd')) ? substr($rowObtConsec['turno'], 6) : 0);
				$ultConsecutivo	= ($ultConsecutivo*1)+1;
				// --> Asignar ceros a la izquierda hasta completar 3 digitos
				while(strlen($ultConsecutivo) < 3)
					$ultConsecutivo = '0'.$ultConsecutivo;

				$nuevoTurno = date('ymd').'-'.$prefijo.$ultConsecutivo;
				
                if (isset($turnoACancelar) && $turnoACancelar != '')
					// --> Asignarle el turno al paciente con redireccionamiento
					$sqlAsigTur = "INSERT INTO ".$wbasedato."_000304 (Medico, 			Fecha_data, 			Hora_data, 				Turtem,			Turtur, 			Turdoc, 				Turtdo, 				Turest, Turnom,					Turtse,					Tursec,						Turupr, 		Turred,			Seguridad, 		id)
															  VALUES ('".$wbasedato."', '".date('Y-m-d')."', 	'".date('H:i:s')."',	'".$tema."',	'".$nuevoTurno."', 	'".$numDocumento."',	'".$tipDocumento."', 	'on', 	'".$nombrePaciente."',	'".$tipoServicio."',	'".$servicioSecundario."',	'".$usuarioPreferencial."',	'".$turnoACancelar."','C-".$wuse."',	'')
					";

                else
					// --> Asignarle el turno al paciente
					$sqlAsigTur = "INSERT INTO ".$wbasedato."_000304 (Medico, 			Fecha_data, 			Hora_data, 				Turtem,			Turtur, 			Turdoc, 				Turtdo, 				Turest, Turnom,					Turtse,					Tursec,						Turupr, 					Seguridad, 		id)
															  VALUES ('".$wbasedato."', '".date('Y-m-d')."', 	'".date('H:i:s')."',	'".$tema."',	'".$nuevoTurno."', 	'".$numDocumento."',	'".$tipDocumento."', 	'on', 	'".$nombrePaciente."',	'".$tipoServicio."',	'".$servicioSecundario."',	'".$usuarioPreferencial."',	'C-".$wuse."',	'')
					";
				
				$resObtConsec = mysql_query($sqlAsigTur, $conex);

				// --> Si ha ocurrido un error guardando el turno
				if(!$resObtConsec)
				{
					$respuesta['Error'] 	= true;
					$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, a ocurrido un error asignando el turno.<br>Por favor contacte al personal de soporte.</span><br>
												<span style='font-size:10px'>(sqlAsigTur: ".mysql_error().')</span>';
				}
				// --> Genero el ficho del turno
				else
				{

					$respuesta['Turno'] 		= $nuevoTurno;
					$respuesta['fichoTurno'] 	= htmlTurno($nuevoTurno, $tipDocumento, $numDocumento, $nombrePaciente, false, $nomServicio, $nomTema, $nomServicioSecundario, $desPiso, $nomlogo);
				}
			}
			else
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= 'Error: El turno no se ha podido asignar.';
			}
			
			// --> Desbloquear tabla
			$sqlBloque = "
			UNLOCK TABLES
			";
			mysql_query($sqlBloque, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlBloque):</b><br>".mysql_error());		

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
	  <title>Turnero</title>
	</head>
		<meta charset="UTF-8">
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script src="../../../include/root/print.js" type="text/javascript"></script>
	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	document.oncontextmenu = function(){return false}
	document.onkeydown 	= lector;
	permitirLeer	  	= true;
	destroyDialog 		= "";
	
	$(document).ready(function(){
		window.focus();
		
		document.addEventListener("click", function(e) {			
			toggleFullScreen();		
		}, false);

	});
	
	
		
	
	$(function(){  
  
		$("#accordionPrincipal").accordion({
			collapsible: false
		});
		$( "#accordionPrincipal" ).accordion( "option", "icons", {} );

		$(".radio").buttonset();

		ajustarResolucioPantalla();

		// --> Activar teclado numerico
		$(".botonteclado").parent().hide();
		$("#botonBorrar").css("width","6.8rem").parent().attr("colspan", "2");
		
		// --> Esto es para cerrar el teclado cuando se de click en un area fuera de este. 
		$(document).click(function(e){
			elemenClick = e.target;
			clase 		= $(elemenClick).attr("class");
			if(clase != "botonteclado" && clase != "botonteclado2" && e.target.id != 'tecladoFlotante' && e.target.id != 'numDocumento' && e.target.id != 'inputNombrePaciente' && e.target.id != 'edadPaciente')
			{	
				$('#tecladoFlotante').hide();
			}
        });
		
		// $("[table=tipoServicio] tr").eq(2).find("td").css({"padding-top":"15px"});
		$("#tableTipoServicio tr").eq(1).find("td").each(function(){
			$(this).css({"padding-top":"18px"});
		});
		
	});
	
	function toggleFullScreen() {
		videoElement = document.getElementById("bodyPrincipal");
		if (!document.mozFullScreen && !document.webkitFullScreen) {
			if (videoElement.mozRequestFullScreen) {
				videoElement.mozRequestFullScreen();
			} else {
				videoElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
			}
		}
	}

	function cerrarTurnero()
    {
    	yo = window;
        $( "#modalTurnero", yo.parent.document ).hide();
    }
  
	
	// --> Prueba
	function lector(event) 
	{
		event 	= event || window.event;
		cadena 	= event.key+"->"+event.keyCode;
		
		// console.log(cadena);
		
		// --> Solo entra si es una letra, un numero, tab o enter
		if((event.keyCode >= 48 && event.keyCode <= 90) || event.key == "Tab" || event.key == "Enter")
		{
			switch(event.key)
			{
				case "Tab":
				{					
					$("#textoLector").val($("#textoLector").val()+"|");
					event.cancelBubble 	= true;
					event.returnValue 	= false;
					return false;
				}
				case "Enter":
				{
					// --> 	Despues de un enter, procedo a validar que sea una lectura desde una cedula o TI
					//		Condiciones: Que sea una array de 13 elementos y que el primero elemento se tipo numerico
					textoLector 		= $("#textoLector").val();
					arrayTextoLector 	= textoLector.split('|');
					
					if(arrayTextoLector.length == 13 && Number.isInteger(arrayTextoLector[0]*1))
					{
						primerCaracterDelsegundoTab = arrayTextoLector[1].substr(0,1);
						
						// --> Si el primer caracter del segundo tab es un entero, indica que es una tarjeta de identidad
						if(Number.isInteger(primerCaracterDelsegundoTab*1))
						{
							$("#numDocumento").val($.trim(arrayTextoLector[0]+primerCaracterDelsegundoTab));
							
							segundoApellido = $.trim(arrayTextoLector[1].substr(1,arrayTextoLector[1].length));
							$("#inputNombrePaciente").val($.trim(arrayTextoLector[3])+" "+$.trim(arrayTextoLector[4])+" "+segundoApellido+" "+$.trim(arrayTextoLector[2]));					
							
							ano 			= arrayTextoLector[6].substr(1,arrayTextoLector[6].length)+arrayTextoLector[7].substr(0,1);
							mes 			= arrayTextoLector[7].substr(1,1)+arrayTextoLector[8].substr(0,1);
							dia 			= arrayTextoLector[8].substr(1,1)+arrayTextoLector[9].substr(0,1);							
							fechaNacimiento = $.trim(ano+"-"+mes+"-"+dia);
							
							tipoDoc		= 'TI';
							onClickTemp = $("[for=radio"+tipoDoc+"]").attr("onClick");
							$("[for=radio"+tipoDoc+"]").attr("onClick", "");
							$("[for=radio"+tipoDoc+"]").trigger("click");
							$("[for=radio"+tipoDoc+"]").attr("onClick", onClickTemp);
						}
						// --> Sino, es una cedula
						else
						{						
							$("#numDocumento").val((arrayTextoLector[0]*1));
							$("#inputNombrePaciente").val(arrayTextoLector[3]+" "+arrayTextoLector[4]+" "+arrayTextoLector[1]+" "+arrayTextoLector[2]);					
							fechaNacimiento = $.trim(arrayTextoLector[6]+"-"+arrayTextoLector[7]+"-"+arrayTextoLector[8]);
							
							tipoDoc		= 'CC';
							onClickTemp = $("[for=radio"+tipoDoc+"]").attr("onClick");
							$("[for=radio"+tipoDoc+"]").attr("onClick", "");
							$("[for=radio"+tipoDoc+"]").trigger("click");
							$("[for=radio"+tipoDoc+"]").attr("onClick", onClickTemp);
						}
						//$("#divMensaje").html("&nbsp;");
						$("#textoLector").val("");
						$('#tecladoFlotante').hide();
						
						// --> Obtener edad
						// if(esFechaValida(fechaNacimiento))
						// {
							// $.post("turneroLobby.php",
							// {
								// consultaAjax:   		'',
								// accion:         		'calcularEdad',
								// wemp_pmla:        		$('#wemp_pmla').val(),
								// fechaNacimiento:		fechaNacimiento
							// }, function(respuesta){
								// $("#edadPaciente").val(respuesta.cantidad);
								// $("[for=radio"+respuesta.tipo+"]").trigger("click");
								// if($.trim(respuesta.tipoDoc) != '')
								// {
									// onClickTemp = $("[for=radio"+respuesta.tipoDoc+"]").attr("onClick");
									// $("[for=radio"+respuesta.tipoDoc+"]").attr("onClick", "");
									// $("[for=radio"+respuesta.tipoDoc+"]").trigger("click");
									// $("[for=radio"+respuesta.tipoDoc+"]").attr("onClick", onClickTemp);
								// }
							// }, 'json');
						// }
					}
					else
					{
						reiniciarPantalla();
						//$("#divMensaje").html("Formato invalido, solo se permiten cedulas o tarjetas de identidad.");
						mensajeAlerta("Formato invalido, solo se permiten cedulas o tarjetas de identidad.");						
					}
					
					$("#textoLector").val("");
					event.cancelBubble 	= true;
					event.returnValue 	= false;
					return false;
					
				}
				default:
				{
					$("#textoLector").val($("#textoLector").val()+event.key);
					break;
				}
			}
			
		}
		else
		{
			//return;
			event.cancelBubble 	= true;
			event.returnValue 	= false;
			return false;
		}
	}
	//-------------------------------------------------------------------
	// --> Fecha válida
	//-------------------------------------------------------------------
	function esFechaValida(fecha)	
	{
		valida = true;
		if(fecha != "")
		{
			fecha 		= fecha.split("-");
			ano			= fecha[0];
			mes			= fecha[1];
			dia			= fecha[2];
		}
		else
			valida = false;	
		
		return valida;
	}

	//-------------------------------------------------------------------
	//	--> Funcion que ajusta la vista a la resolucion de la pantalla
	//-------------------------------------------------------------------
	function ajustarResolucioPantalla()
	{
		var height = 0;
		var width  = 0;
		if (self.screen){     // for NN4 and IE4
			width 	= screen.width;
			height 	= screen.height
		}
		else
			if (self.java){   // for NN3 with enabled Java
				var jkit = java.awt.Toolkit.getDefaultToolkit();
				var scrsize = jkit.getScreenSize();
				width 	= scrsize.width;
				height 	= scrsize.height;
			}

		width 	= width*0.99;
		height 	= height*0.90;


		if(width > 0 && height > 0)
			$("#accordionPrincipal").css({"width":width});
		else
			$("#accordionPrincipal").css({"width": "100 %"});

		$("body").height($("#accordionPrincipal").height()*0.98);
	}

	//-------------------------------------------------------------------
	//	--> Funcion que genera el turno
	//-------------------------------------------------------------------
	function generarTurno(validarExisteTurno, turnoACancelar)
	{
		// --> Si no han seleccionado el tipo de documento
		if($("[name=tipDocumento]:checked").val() == undefined)
		{
			//$("#divMensaje").html("Debe seleccionar el tipo de documento.");
			mensajeAlerta("Debe seleccionar el tipo de documento.");	
			return;
		}
		
		// --> Si no han ingresado el numero de documento
		if($("#numDocumento").val() == "")
		{
			//$("#divMensaje").html("Debe ingresar el numero de documento.");
			mensajeAlerta("Debe ingresar el numero de documento.");	
			return;
		}
		
		// --> Si no han ingresado el nombre
		if($.trim($("#inputNombrePaciente").val()) == "")
		{
			//$("#divMensaje").html("Debe ingresar el nombre.");
			mensajeAlerta("Debe ingresar el nombre.");	
			return;
		}
		
		// --> Si no han seleccionado el tipo de servicio
		if($("[name=tipoServicio]:checked").val() == undefined)
		{
			//$("#divMensaje").html("Debe seleccionar el tipo de servicio.");
			mensajeAlerta("Debe seleccionar el tipo de servicio.");	
			return;
		}
		
		// --> Si no han seleccionado usuario preferencial
		if($("[name=usuarioPreferencial]:checked").val() == undefined)
		{
			//$("#divMensaje").html("Debe seleccionar si es usuario prioritario.");
			//mensajeAlerta("Debe seleccionar si es usuario prioritario.");	
			//return;
			usuarioPreferencial = $("#prioritarioDefault").val()
		}
		else
			usuarioPreferencial = $("[name=usuarioPreferencial]:checked").val()
		
		$("#botonAceptar").prop("disabled",true).html("<img  src='../../images/medical/ajax-loader11.gif'>");
			
		$.post("turneroLobby.php",
		{
			consultaAjax:   		'',
			accion:         		'generarTurno',
			wemp_pmla:        		$('#wemp_pmla').val(),
			tipDocumento:			$("[name=tipDocumento]:checked").val(),
			numDocumento:			$.trim($("#numDocumento").val()),			
			nombrePaciente:			$.trim($("#inputNombrePaciente").val()),
			tipoServicio:			$("[name=tipoServicio]:checked").val(),
			servicioSecundario:		$("[name=tipoServicio]:checked").attr("servicioSec"),
			nomServicioSecundario:	$("[name=tipoServicio]:checked").attr("nomServicioSec"),
			usuarioPreferencial:	usuarioPreferencial,
			validarExisteTurno:		validarExisteTurno,
			turnoACancelar:			turnoACancelar,
			codigoTurnero:			$("#codigoTurnero").val(),
			tema:					$("#tema").val()
		}, function(data){

			if(data.Error)
			{
				var cerrarVentana = setTimeout(function(){
					$("#msjReimpTurno").dialog("close");
					reiniciarPantalla();
				}, 12000);
					
				$("#msjReimpTurno").html("<img width='20' heigth='20' src='../../images/medical/sgc/Mensaje_alerta.png'>&nbsp;"+data.Mensaje);
				$("#msjReimpTurno").dialog({
					title: "<div align='left'>&nbsp;</div>",
					width: 'auto',
					modal: true,
					close: function( event, ui ) {
						reiniciarPantalla();
						clearTimeout(cerrarVentana);
					},
					buttons:{
						"Cerrar": function(){
							$(this).dialog("close");
							reiniciarPantalla();
							clearTimeout(cerrarVentana);
						}
					}
				});
			}
			else
			{
				if(data.yaExisteTurnoHoy)
				{
					// --> Si no se selecciona ninguna opcion en la ventana, cerrarla automaticamente a los 12 segundos.
					var cerrarVentana = setTimeout(function(){
						$("#msjReimpTurno").dialog("close");
						// --> Limpiar campos
						reiniciarPantalla();
					}, 12000);

					// --> Si existe un turno asignado para hoy, para el mismo documento.
					$("#msjReimpTurno").dialog({
						title: "<div align='left'><img width='20' heigth='20' src='../../images/medical/sgc/Mensaje_alerta.png'></div>",
						width: 'auto',
						modal: true,
						close: function( event, ui ) {
							reiniciarPantalla();
							clearTimeout(cerrarVentana);
						},
						buttons:{
							"Reimprimir turno": function(){
								imprimirTurno(data.fichoTurno);
								$(this).dialog("close");
								clearTimeout(cerrarVentana);
							},
							"Pedir nuevo turno": function(){
								generarTurno(false, data.Turno);
								clearTimeout(cerrarVentana);
							},
							"Salir": function(){
								$(this).dialog("close");
								reiniciarPantalla();
								clearTimeout(cerrarVentana);
							}
						}
					});
				}
				else
					imprimirTurno(data.fichoTurno);
			}

			setTimeout(function(){
				$("#botonAceptar").prop("disabled",false).html("Aceptar");
			}, 6000);

		}, 'json');
	}

	//---------------------------------------------------------------------------------------
	//	--> Función que pinta una vista previa del turno y lo imprime automaticamente
	//---------------------------------------------------------------------------------------
	function imprimirTurno(fichoTurno)
	{
		// --> Mostrar en pantalla el ficho del turno
		$("#fichoTurno").html(fichoTurno+"<br>").dialog({
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "<div align='center' style='font-size:1.2rem'>Por favor tome su tiquete.<br>En un momento ser&aacute; atendido.</div>",
			width: "23rem"
		});

		setTimeout(function(){
			$("#fichoTurno").html("");
			$("#fichoTurno").dialog("close");
			$("#msjReimpTurno").dialog("close");
		}, 5000);

		reiniciarPantalla();

		// --> Imprimir tiquete de turno.
		setTimeout(function(){
			var contenido	= "<html><body onload='window.print();window.close();'>";
			contenido 		= contenido + fichoTurno + "</body></html>";

			var windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=1,height=1,resizable=yes,screenX=1,screenY=1,personalbar=no,scrollbars=no";
			var ventana = window.open( "", "",  windowAttr );
			ventana.document.write(contenido);
			ventana.document.close();
		}, 1000);
	}

	//-------------------------------------------------------------------
	//	--> Reiniciar pantalla para permitir ingresar un nuevo turno
	//-------------------------------------------------------------------
	function reiniciarPantalla()
	{
		// --> Limpiar campos
		$("#numDocumento").val("");
		$("#inputNombrePaciente").val("");
		$("[name=tipDocumento]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
		$("[name=tipoServicio]:checked").removeAttr("checked").next().css({"border": "1px solid #21d386", "background": "#9fddc3 url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x"});
		$("[name=usuarioPreferencial]:checked").removeAttr("checked").next().css({"border": "1px solid #f75d5d", "background": "#ffbfbf url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x"});
		//$("#divMensaje").html("&nbsp;");
		$("#textoLector").val("");
		$('#tecladoFlotante').hide();
	}

	//-------------------------------------------------------------------
	//	--> Funcion que obtiene el nombre del paciente
	//-------------------------------------------------------------------
	function obtenerNombrePaciente()
	{
		if($("[name=tipDocumento]:checked").val() != undefined && $("#numDocumento").val() != '')
		{
			setTimeout(function(){
				$.post("turneroLobby.php",
				{
					consultaAjax:   		'',
					accion:         		'obtenerNombrePaciente',
					wemp_pmla:        		$('#wemp_pmla').val(),
					numDocumento:			$("#numDocumento").val(),
					tipDocumento:			$("[name=tipDocumento]:checked").val()
				}, function(data){
					data.nombrePac = $.trim(data.nombrePac);
					nombrePac = ((data.nombrePac != '') ? data.nombrePac : "" );
					$("#inputNombrePaciente").val(nombrePac);
				}, 'json');
			}, 200);
		}
	}
	//-------------------------------------------------------------------
	//	--> Teclado
	//-------------------------------------------------------------------
	function teclado(Elemento)
	{
		input 		= $("#"+$("#tecladoFlotante").attr("elementoEscribir"));
		var valor 	= input.val();

		if($(Elemento).val() == "Borrar")
			valor = valor.substr(0, valor.length - 1)
		else
			valor = valor+$(Elemento).val();

		input.val(valor);
		
		// --> Obtener nombre del paciente solo cuando se digite el numero de documento
		if($("#tecladoFlotante").attr("elementoEscribir") == 'numDocumento')
			obtenerNombrePaciente();
	}
	//-------------------------------------------------------------------
	//	--> Muestra el mensaje de alerta
	//-------------------------------------------------------------------
	function mensajeAlerta(mensaje)
	{
		clearTimeout(destroyDialog);
		$( "#dialog-message" ).dialog( "destroy" );
		$( "#dialog-message" ).text(mensaje).dialog({
			modal: true,
			width: 900,
			height: 200,
			position: {at: "center top", of: window }
		}).prev().css("display", "none");
		destroyDialog = setTimeout(function(){
			$( "#dialog-message" ).dialog( "destroy" );
		}, 3000);
	}
	//-------------------------------------------------------------------
	//	--> Desplega el teclado 
	//-------------------------------------------------------------------
	function verTeclado(Elemento, origen)
	{
		// --> Si no han seleccionado el tipo de documento
		if($("[name=tipDocumento]:checked").val() == undefined)
		{
			// $("#divMensaje").html("Primero debe seleccionar el tipo de documento.");
			mensajeAlerta("Primero seleccione el tipo de documento.");
			return;
		}

		switch(origen)
		{
			// --> Mostrar teclado para ingresar el numero de documento
			case 'DOC':
				valTipoDoc = $("[name=tipDocumento]:checked").val();
				// --> Mostrar solo teclado numerico
				if( valTipoDoc == undefined || valTipoDoc == "CC" || valTipoDoc == "TI" || valTipoDoc == "NU")
				{					
					$(".botonteclado").parent().hide();
					$(".botonteclado2").parent().show();
				}
				// --> Mostrar teclado alfanumerico
				else
				{
					$(".botonteclado").parent().show();
					$(".botonteclado2").parent().show();
					$("#botonBorrar").parent().show();
				}
				//$("#botonBorrar").css("width","6.8rem").parent().attr("colspan", "2");
				break;				
			// --> Mostrar teclado para ingresar el nombre
			case 'NOM':
				$(".botonteclado").parent().show();
				$(".botonteclado2").parent().hide();
				$("#botonBorrar").parent().show();
				break;
			// --> Mostrar teclado la edad
			case 'EDA':
				$(".botonteclado").parent().hide();
				$(".botonteclado2").parent().show();
				break;				
		}
		Elemento 		= $(Elemento);
		var posicion 	= $(Elemento).position();
		$('#tecladoFlotante').css({'left':posicion.left,'top':posicion.top+28}).show(400);
		$('#tecladoFlotante').attr("elementoEscribir", Elemento.attr("id"))
	}
	//-------------------------------------------------------------------
	//	--> 
	//-------------------------------------------------------------------
	function checkearRadio(elemento)
	{
		nameRadios = $(elemento).prev().attr("name");
		servicio   = $(elemento).prev().val();
		$("[name="+nameRadios+"]").removeAttr("checked");
		$(elemento).prev().attr("checked", "checked");
		
		if($(elemento).attr("lab") == "tipDocumento")
		{
			verTeclado($("#numDocumento"), "DOC");
			
			setTimeout(function(){
				var posicion 	= $("#numDocumento").position();
				$('#tecladoFlotante').css({'left':posicion.left,'top':posicion.top+28}).show(400);
				$('#tecladoFlotante').attr("elementoEscribir", $("#numDocumento").attr("id"));
			}, 500);
			
		}
		
		if($(elemento).attr("lab") == "tipoServicio")
		{			
			if($(elemento).attr("serDep") != undefined)
			{
				serDependiente 		= JSON.parse($(elemento).attr("serDep"));
				
				if(Object.keys(serDependiente).length > 0)
				{
					anchoAltoRadios 	= "width:14rem;height:4.7rem;font-size:1.2rem;color:#000000;font-weight:normal;";
					htmlSerDep = ""
							   +" <table width='100%'><tr><td align='right'><button style='cursor:pointer;border-radius:3px;border:1px solid #AFAFAF;color:#333333;font-family: verdana;font-size: 1rem;' onclick='$.unblockUI();$(\"[name=tipoServicio]:checked\").removeAttr(\"checked\");'><b>X</b></button></td></tr></table>"
							   +"<br><table width='97%' style='color:#333333;font-size: 1rem;margin-top:10px;margin-bottom:10px;' class='radio'>"
							   + "<tr>";
					var x = 0;
					jQuery.each( serDependiente, function( i, val ){
						
						x++;
						htmlSerDep += ((x == 1) ? "<tr style='padding:2px'>" : "");
						
						htmlSerDep += ""
						+"		<td align='center' valign='center'>&nbsp;&nbsp;"
						+"			<input type='radio' style='"+anchoAltoRadios+"' name='tipoServicio' value='"+servicio+"' servicioSec='"+i+"' nomServicioSec='"+val+"' id='radio"+i+"secundario' />"
						+"			<label lab='tipoServicio' serDep='[]' style='border: 1px solid #21d386;background:#9fddc3 url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x;border-radius:0.4em;"+anchoAltoRadios+"' for='radio"+i+"secundario' onClick='checkearRadio(this)'>"+val+"</label>&nbsp;&nbsp;"
						+"		</td>";
						
						htmlSerDep += ((x == 4) ? "</tr>" : "");

						x = ((x == 4) ? 0 : x);
							
					});
					
					htmlSerDep+= "</table>";
					
					$("#divSerDependiente").html(htmlSerDep+"<br>");					
					$("#divSerDependiente .radio").buttonset();					
					
					$.blockUI({ message: $("#divSerDependiente"), 
						css:{ 
							left: 	($(window).width() - 1000 )/2 +'px', 
							top: 	($(window).height() - $("#divSerDependiente").height() )/2 +'px',
							width: 	'1000px'
						} 
					});
					
					// $("[lab=tipoServicio]").css({"border": "1px solid #21d386", "background": "#9fddc3 url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x"});
				}
				else
				{
					$("[lab=tipoServicio]").css({"border": "1px solid #21d386", "background": "#9fddc3 url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x"});
					$(elemento).css({"border": "1px solid #0c7f4b", "background": "#14ba72 url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x"});			
					
					setTimeout(function(){
						$.unblockUI();
					}, 600);
				}				
			}	
		}
		
		if($(elemento).attr("lab") == "tipoUsuPri")
		{
			$("[lab=tipoUsuPri]").css({"border": "1px solid #f75d5d", "background": "#ffbfbf url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x"});
			$(elemento).css({"border": "1px solid #ff1c1c", "background": "#f77171 url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x"});
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
		// --> Estylo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:red; background:lightyellow;font-size:2rem}
		/*Firefox*/
		[tipo=obligatorio]::-moz-placeholder {color:red; background:lightyellow;}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:red; background:lightyellow;font-size:2rem}
		[tipo=obligatorio]:-moz-placeholder {color:red; background:lightyellow;font-size:2rem}

		.botonteclado {
			border: 			1px solid #9CC5E2;
			background-color:	#E3F1FA;
			width:				5rem;
			height:				4.7rem;
			font-weight: 		normal;
			border-radius:		0.5rem;
		}
		.botonteclado2 {
			border: 			1px solid #333333;
			background-color:	#E3F1FA;
			width:				5rem;
			height:				4.7rem;
			font-weight: 		bold;
			border-radius:		0.5rem;
		}
		.botonteclado:hover {
			position: 			relative;
			top:	 			1px;
			left: 				1px;
			background-color:	#75C3EB;
			color:				#ffffff;
		}
		.botonteclado2:hover {
			position: 			relative;
			top:	 			1px;
			left: 				1px;
			background-color:	#75C3EB;
			color:				#ffffff;
		}
		
		/* make the video stretch to fill the screen in WebKit */
		:-webkit-full-screen #bodyPrincipal {
			width: 		100%;
			height: 	100%;
		}

		:fullscreen
		:-ms-fullscreen,
		:-webkit-full-screen,
		:-moz-full-screen {
		   overflow: auto !important;
		}

		/* Al poner el curso encima (hover) */

		.btn:hover {  background: #099fc4;  background-image: -webkit-linear-gradient(top, #2079b0, lightgray);
		  background-image: -moz-linear-gradient(top, #099fc4, lightgray);
		  background-image: -ms-linear-gradient(top, #099fc4, lightgray);
		  background-image: -o-linear-gradient(top, #099fc4, lightgray);
		  background-image: linear-gradient(to bottom, #099fc4, lightgray);  text-decoration: none;
		}

	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->

	<BODY style="overflow:hidden" id="bodyPrincipal">
	<?php
	
	// --> Consultar nombre del tema
	$nomTema = "";
	if($tema != '')
	{
		$sqlTema = "
				SELECT Codnom,Codlog
				  FROM ".$wbasedato."_000305
				 WHERE Codtem = '".$tema."'
				   AND Codest = 'on'
				";
		$resTema = mysql_query($sqlTema, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTema):</b><br>".mysql_error());
		if( $rowTema = mysql_fetch_array($resTema) )
		{
			$nomTema = $rowTema['Codnom'];
			$nomlogo = $rowTema['Codlog'];
		}
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
	}	
	
	// $anchoAltoRadios = "width:2.5rem;height:2.1rem";
	$anchoAltoRadios = "width:14rem;height:4.7rem;font-size:1.2rem;color:#000000;font-weight:normal;";
	
	$arrTipDoc = array();
	// --> Obtener maestro de tipos de documento
	$sqlTipDoc = "SELECT Codigo, Descripcion
					FROM root_000007
				   WHERE Codigo IN('CC', 'TI', 'RC', 'CE', 'PA')
	";
	$resTipDoc = mysql_query($sqlTipDoc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipDoc):</b><br>".mysql_error());
	while($rowTipDoc = mysql_fetch_array($resTipDoc))
		$arrTipDoc[$rowTipDoc['Codigo']] = $rowTipDoc['Descripcion'];
	
	// --> Obtener maestro de tipos de sevicio
	$arrServicios = array();
	$sqlServicios = "
	SELECT Sercod, Sernom, Serord
	  FROM ".$wbasedato."_000298
     WHERE Sertem = '".$tema."'
	   AND Serest = 'on'
	 ORDER BY Serord
	";
	$resServicios = mysql_query($sqlServicios, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlServicios):</b><br>".mysql_error());
	while($rowServicios = mysql_fetch_array($resServicios))
		$arrServicios[$rowServicios['Sercod']] = strtoupper($rowServicios['Sernom']);

	if(!isset($codigoTurnero) || trim($codigoTurnero) == '')
		$codigoTurnero = '*';
	
	// --> Obtener maestro de condiciones especiales
	$arrCondiciones = array();
	$sqlCondiciones = "
	SELECT Concod, Connom, Conord, Conpri
	  FROM ".$wbasedato."_000299
     WHERE Conest = 'on'
	 ORDER BY Conord
	";
	$resCondiciones = mysql_query($sqlCondiciones, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCondiciones):</b><br>".mysql_error());
	while($rowCondiciones = mysql_fetch_array($resCondiciones))
	{
		$arrCondiciones[$rowCondiciones['Concod']] = strtoupper($rowCondiciones['Connom']);
		if($rowCondiciones['Conpri'] != 'on')
		   $prioritarioDefault = $rowCondiciones['Concod'];
	}
	
	if(!isset($codigoTurnero) || trim($codigoTurnero) == '')
		$codigoTurnero = '*';
	
	// --> Pintar pantalla para asignar el turno
	//Se desactiva segundo logo - <img width='120' heigth='100' src='../../images/medical/root/Logo_MatrixAzulClaro.png'>
	echo "
	<input type='hidden' id='wemp_pmla' 		value='".$wemp_pmla."'>
	<input type='hidden' id='codigoTurnero' 	value='".trim($codigoTurnero)."'>
	<input type='hidden' id='textoLector' 		value='' numTabs='0'>

	<div id='accordionPrincipal' align='center' style='margin: auto auto;background-color: white;'>
		<h1 style='font-size: 2.2rem;background:#75C3EB;font-family:verdana;' align='center'>
			<img width='130' heigth='61' src='../../images/medical/root/".$nomlogo."' >
			&nbsp;
			Es un placer servirle.
			&nbsp;
			
			<br>
			<span style='font-size:1.2rem'>
				".$nomTema."
			</span>
		</h1>
		<div style='color:#000000;font-family: verdana;font-weight: normal;font-size: 1.5rem;' align='center'>
			<table style='width:90%;margin-top:0px;margin-bottom:2px;font-family: verdana;font-weight: normal;font-size: 1.7rem;'>
				<tr align='left'>
					<td colspan='2' style='padding-top:10px;'>TIPO DE DOCUMENTO:</td>
				</tr>
				<tr>
					<td align='center' colspan='2'>
						<table style='color:#333333;font-size:1rem;margin-top:4px;margin-bottom:2px;' class='radio'>
						";
						$x = 0;
						foreach($arrTipDoc as $codTipDoc => $nomTipDoc)
						{
							$x++;
							echo (($x == 1) ? "<tr>" : "")."
							<td style='padding:2px'>
								&nbsp;&nbsp;
								<input type='radio' style='".$anchoAltoRadios."' name='tipDocumento' value='".$codTipDoc."' id='radio".$codTipDoc."' />
								<label lab='tipDocumento' valign='center' onClick='checkearRadio(this);obtenerNombrePaciente()' style='border-radius:0.4em;".$anchoAltoRadios.";' for='radio".$codTipDoc."'>".$nomTipDoc."</label>&nbsp;&nbsp;
							</td>
							".(($x == 6) ? "</tr>" : "");
							
							$x = (($x == 6) ? 0 : $x);
						}
				echo "	</table>
					</td>
				</tr>			
				<tr>
					<td align='left'>DOCUMENTO:</td>
					<td><input id='numDocumento' 		type='text' tipo='obligatorio'	onClick='verTeclado(this, \"DOC\")' readonly style='background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:42rem;font-size: 2rem'></td>
				</tr>";
	echo "		<tr>
					<td align='left'>NOMBRE:</td>
					<td style='padding-bottom:15px;'>
						<input id='inputNombrePaciente' type='text' tipo='obligatorio'	onClick='verTeclado(this, \"NOM\")' readonly style='margin-top:5px;background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:42rem;font-size: 2rem'>
					</td>
				</tr>
				<tr>
					<td colspan='2' align='left' style='padding-top:10px;border-top:1px solid #AFAFAF;'>
						TIPO DE SERVICIO:
					</td>
				</tr>
				<tr>
					<td colspan='2' align='center' style='padding-bottom:15px;'>			
						<table id='tableTipoServicio' style='color:#333333;font-size: 1rem;margin-top:4px;margin-bottom:2px;' class='radio'>
						";
						$x 		= 0;
						foreach($arrServicios as $codServicio => $nomServicio)
						{
							// --> Consultar si el servicio tiene servicios dependientes
							$serDependientes = serviciosDependietes($codServicio);
							$x++;
							echo (($x == 1) ? "<tr>" : "")."
							<td>
								&nbsp;&nbsp;
								<input type='radio' style='".$anchoAltoRadios."' name='tipoServicio' value='".$codServicio."' servicioSec='' id='radio".$codServicio."' />
								<label lab='tipoServicio' serDep='".json_encode($serDependientes)."' style='border: 1px solid #21d386;background:#9fddc3 url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x;border-radius:0.4em;".$anchoAltoRadios."' for='radio".$codServicio."' onClick='checkearRadio(this)'>".$nomServicio."</label>&nbsp;&nbsp;
							</td>
							".(($x == 4) ? "</tr>" : "");
							
							$x = (($x == 4) ? 0 : $x);
						}
				echo "	</table>
					</td>
				</tr>
				<tr>
					<td colspan='2' align='left' style='padding-top:10px;border-top:1px solid #AFAFAF;'>
						USUARIO PRIORITARIO:
					</td>
				</tr>
				<tr>
					<td colspan='2' align='center'>
						<input type='hidden' id='prioritarioDefault' value='".$prioritarioDefault."'>	
						<table style='color:#333333;font-size: 1rem;margin-top:4px;margin-bottom:2px;' class='radio'>
						";
						$x = 0;
						foreach($arrCondiciones as $codCondi => $nomCondi)
						{
							$x++;
							echo (($x == 1) ? "<tr style='padding:2px'>" : "")."
							<td style='padding:2px'>
								&nbsp;&nbsp;
								<input type='radio' style='".$anchoAltoRadios."' name='usuarioPreferencial' value='".$codCondi."' id='radio2".$codCondi."' />
								<label lab='tipoUsuPri' style='border: 1px solid #f75d5d;background:#ffbfbf url(images/ui-bg_glass_80_d7ebf9_1x400.png) 50% 50% repeat-x;border-radius:0.4em;".$anchoAltoRadios."' for='radio2".$codCondi."' onClick='checkearRadio(this)'>".$nomCondi."</label>&nbsp;&nbsp;
							</td>
							".(($x == 5) ? "</tr>" : "");

							$x = (($x == 5) ? 0 : $x);
						}
				echo "	</table>
					</td>
				</tr>
			</table>
			<div id='tecladoFlotante' style='display:none;z-index:10000;position: absolute;' elementoEscribir=''>
				<table style='background-color:#FFFFFF;border:1px solid #AFAFAF;margin-top:1.3rem;border-radius:0.4em;border-collapse: separate;border-spacing: 0.4rem 0.4rem;'>
					<tr>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='Q'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='W'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='E'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='R'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='T'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='Y'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='U'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='I'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='O'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='7'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='8'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='9'></td>
					</tr>
					<tr>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='A'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='S'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='D'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='F'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='G'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='H'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='J'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='K'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='L'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='4'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='5'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='6'></td>
					</tr>
					<tr>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='Z'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='X'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='C'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='V'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='B'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='N'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='M'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='&Ntilde;'></td>
						<td><input class='botonteclado' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='P'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='1'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='2'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='3'></td>
					</tr>
					<tr>
						<td colspan='7' align='center'><input class='botonteclado' type='button' style='width:28rem;font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value=' '></td>
						<td colspan='2' align='center'><input class='botonteclado2' type='button' id='botonBorrar' style='width:20rem;font-size:1.8em;font-weight:normal;' onclick='teclado(this)' value='Borrar'></td>
						<td><input class='botonteclado2' type='button' style='font-size:2.3em;font-weight:normal;' onclick='teclado(this)' value='0'></td>
					</tr>
				</table>
			</div>
			<br>
			<table width='100%'>
				<tr>
					<td width='2%'></td>";
					if (isset($atras)){
					    echo "<td width='30%' align='center'><button id='botonRegresar' class='btn' style='border-color:white;margin-top:0rem;color:#333333;font-family: verdana;font-size: 3rem;' onclick='cerrarTurnero();'>Regresar</button></td>";
                    }
                    else
						{
						echo "<td width='30%'></td>";
					}
                    echo "
					<td width='40%' align='center'><button id='botonAceptar' class='btn' style='border-color: white;margin-top:0rem;color:#333333;font-family: verdana;font-size: 4rem;' onclick='generarTurno(\"true\", \"\")'>Aceptar</button></td>
					<td width='30%' align='right'><button id='botonLimpiar' style='border-color: white;margin-top:0rem;color:#333333;font-family: verdana;font-size: 1.5rem;' onclick='reiniciarPantalla()'>Reiniciar pantalla</button></td>
				</tr>
			</table>
		</div>
	</div>
	<div id='fichoTurno' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'></div>
	<div id='divSerDependiente' style='display:none;background-color: #FFFFFF;cursor:default' align='center'></div>
	<div id='fichoTurnoImp' style='display:none;'></div>
	<div id='msjReimpTurno' style='font-family: verdana;color:#F95C59;font-size:1.2rem;display:none;' align='center' >
			Usted ya tiene un turno asignado para hoy.
	</div>
	";
	$styleMsj = "color: #856404; background-color: #fff3cd;border-color: #ffeeba";

	echo '
	<div id="dialog-message" style="'.$styleMsj.';font-size:3rem" align="center"></div>';

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

//}//Fin de session
?>
