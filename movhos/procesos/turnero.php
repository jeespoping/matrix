<?php
include_once("conex.php");
//=========================================================================================================================================\\
//       	TURNERO URGENCIAS
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:	2015-06-23
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2015-06-23';
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
	$wbasedato	 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//------------------------------------------------------------------------------------
	//	--> Genera el html del tiquete del turno
	//------------------------------------------------------------------------------------
	function htmlTurno($turno, $tipDocumento, $numDocumento, $nombrePaciente, $reimpresion)
	{
		global $wemp_pmla;
		$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
		$wlogemp = $institucion->baseDeDatos;
		$html = "
		<table style='font-family: verdana;font-size:1rem;'>
			<tr>
				<td colspan='2' align='center'>
				<img src='../../images/medical/root/".$wlogemp.".jpg' width=125 heigth=76>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
					Es un placer servirle.
					<br><br>
				</td>
			</tr>
			<tr>
				<td >Turno:&nbsp;&nbsp;</td>
				<td align='right' style='font-size:2rem;'><b>".substr($turno, 7)."</b></td></tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>".$tipDocumento."&nbsp;&nbsp;&nbsp;".$numDocumento."</td>
			</tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>".ucwords(strtolower($nombrePaciente))."</td>
			</tr>
			<tr>
				<td colspan='2' align='center' style='font-size:0.8rem'>
					<br><b>Por favor conserve este tiquete hasta la consulta con el m&eacute;dico.</b>
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
			";
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

			// --> Validar si ya existe un turno asignado hoy, para el documento
			$sqlValTurno = " SELECT Atutur
							   FROM ".$wbasedato."_000178
							  WHERE Fecha_data 	= '".date("Y-m-d")."'
							    AND Atudoc 		= '".$numDocumento."'
							    AND Atutdo 		= '".$tipDocumento."'
								AND Atuest 		= 'on'
							  ORDER BY Atutur DESC
			";
			$resValTurno = mysql_query($sqlValTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValTurno):</b><br>".mysql_error());
			if($validarExisteTurno == 'true' && $rowValTurno = mysql_fetch_array($resValTurno))
			{
				$respuesta['yaExisteTurnoHoy'] 	= true;
				$respuesta['Turno'] 			= $rowValTurno['Atutur'];
				$respuesta['fichoTurno'] 		= htmlTurno($rowValTurno['Atutur'], $tipDocumento, $numDocumento, $nombrePaciente, true);
			}
			else
			{
				// --> Obtener la sala de espera del turnero
				$salaEspera = '';
				$sqlSala = "
				SELECT Tursal 
				  FROM ".$wbasedato."_000216
				 WHERE Turcod = '".$codigoTurnero."'
				   AND Turest = 'on'
				";
				$resSala = mysql_query($sqlSala, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSala):</b><br>".mysql_error());
				if($rowSala = mysql_fetch_array($resSala))
				{
					if(trim($rowSala['Tursal']) != '')
						$salaEspera = $rowSala['Tursal'];
				}	
				
				if($salaEspera == '')
				{
					// --> Obtener la sala de espera por defecto
					$sqlSalaDefecto = "
					SELECT Salcod
					  FROM ".$wbasedato."_000182
					 WHERE Salaps = 'on'
					   AND Salest = 'on'						 
					";
					$resSalaDefecto = mysql_query($sqlSalaDefecto, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalaDefecto):</b><br>".mysql_error());
					if($rowSalaDefecto = mysql_fetch_array($resSalaDefecto))
						$salaEspera = $rowSalaDefecto['Salcod'];
					else
						$salaEspera = '*';
				}
				
				// --> Validar si la categoria si puede ser seleccionada en este turnero
				if($salaEspera != '' && $salaEspera != '*')
				{
					// --> Obtener la sala en la que se puede seleccionar la categoria
					$sqlCatSal = "
					SELECT Catsal, Catnom, Salnom 
					  FROM ".$wbasedato."_000207 AS A INNER JOIN ".$wbasedato."_000182 AS B ON(Catsal = Salcod)
					 WHERE Catcod = '".$categoriaEmp."'	
                       AND Catsal != ''					 
					";
					$resCatSal = mysql_query($sqlCatSal, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCatSal):</b><br>".mysql_error());
					if($rowCatSal = mysql_fetch_array($resCatSal))
					{
						if($salaEspera != $rowCatSal['Catsal'])
						{
							$sqlHorario = "
							SELECT Salhif, Salhff  
							  FROM ".$wbasedato."_000182
							 WHERE Salcod = '".$salaEspera."'		   
							";
							$resHorario = mysql_query($sqlHorario, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlHorario):</b><br>".mysql_error());
							if($rowHorario = mysql_fetch_array($resHorario))
							{
								$horaActual 	= strtotime(date("Y-m-d H:i"));
								// --> Si está dentro del horario de funcionamiento de aplicacion de filtro de la sala
								if(strtotime(date("Y-m-d")." ".$rowHorario['Salhif']) <= $horaActual && $horaActual <= strtotime(date("Y-m-d")." ".$rowHorario['Salhff']))
								{
									// --> No se puede generar el turno
									$respuesta['Error'] 	= true;
									$respuesta['Mensaje'] 	= "	<span style='font-size:28px'>Para la categor&iacute;a ".$rowCatSal['Catnom'].", por favor genere su turno en<br>la sala de atenci&oacute;n ".$rowCatSal['Salnom'].".</span>";
									echo json_encode($respuesta);
									return;
								}
							}
						}
					}	
				}	
					
				// --> Bloquear tabla de turnos
				$sqlBloque = "
				LOCK TABLES ".$wbasedato."_000178 WRITE;
				";
				mysql_query($sqlBloque, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlBloque):</b><br>".mysql_error());
				
				// --> Cancelar turno existente
				// if($validarExisteTurno == 'false' && $turnoACancelar != '')
				// {
					// $sqlCancelarTur = "
					// UPDATE ".$wbasedato."_000178
					   // SET Atuest = 'off'
					 // WHERE Atutur = '".$turnoACancelar."'
					// ";
					// mysql_query($sqlCancelarTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelarTur):</b><br>".mysql_error());
				// }
				
				// --> Obtener el ultimo consecutivo
				$sqlObtConsec = " SELECT MAX(REPLACE(Atutur, '-', '')*1) AS turno
									FROM ".$wbasedato."_000178
								   WHERE Atutur LIKE '".date('ymd')."%'
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

					$nuevoTurno = date('ymd').'-'.$ultConsecutivo;					
					
					// --> Asignarle el turno al paciente
					$sqlAsigTur = "INSERT INTO ".$wbasedato."_000178 (Medico, 			Fecha_data, 			Hora_data, 				Atutur, 			Atudoc, 				Atutdo, 				Atuest, Atusea, 			Atunom,					Atueda,					Atuted,				Atuten, 				Seguridad, 		id)
															  VALUES ('".$wbasedato."', '".date('Y-m-d')."', 	'".date('H:i:s')."',	'".$nuevoTurno."', 	'".$numDocumento."',	'".$tipDocumento."', 	'on', 	'".$salaEspera."', 	'".$nombrePaciente."',	'".$edadPaciente."',	'".$tipoEdad."',	'".$categoriaEmp."',	'C-".$wuse."',	'')
					";
					$resObtConsec = mysql_query($sqlAsigTur, $conex);

					// --> Si ha ocurrido un error guardando el turno
					if(!$resObtConsec)
					{
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= "	<span style='font-size:20px'>Discúlpenos, a ocurrido un error asignando el turno.<br>Por favor contacte al personal de soporte.</span><br>
													<span style='font-size:10px'>(sqlAsigTur: ".mysql_error().')</span>';
					}
					// --> Genero el ficho del turno
					else
					{
						$respuesta['Turno'] 		= $nuevoTurno;
						$respuesta['fichoTurno'] 	= htmlTurno($nuevoTurno, $tipDocumento, $numDocumento, $nombrePaciente, false);
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
			}

			echo json_encode($respuesta);

			break;
			return;
		}
		case 'calcularEdad':
		{
			$respuesta 	= array('cantidad' => "", 'tipo' => "", 'tipoDoc' => "");
			$fecha 		= time() - strtotime($fechaNacimiento);
			$edadAnos 	= floor($fecha/31556926);
			
			if ($edadAnos > 0)
			{
				$respuesta['cantidad'] 	= $edadAnos;
				$respuesta['tipo'] 		= 'A';
				$respuesta['tipoDoc'] 	= (($edadAnos >= 18) ? 'CC' : '');				
			}
			else
			{
				$respuesta['tipoDoc'] 	= 'TI';
				
				$edadMeses = floor($fecha/(3600*24*30));
				if($edadMeses > 0)
				{
					$respuesta['cantidad'] 	= $edadMeses;
					$respuesta['tipo'] 		= 'M';
				}
				else
				{
					$edadDias 				= floor($fecha/(3600*24));
					$respuesta['cantidad'] 	= $edadDias;
					$respuesta['tipo'] 		= 'D';
				}
			}			
			
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
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/print.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	document.onkeydown 	= lector;
	permitirLeer	  	= true;
	
	$(document).ready(function(){
		
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
	});
	
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
						}
						// --> Sino, es una cedula
						else
						{						
							$("#numDocumento").val((arrayTextoLector[0]*1));
							$("#inputNombrePaciente").val(arrayTextoLector[3]+" "+arrayTextoLector[4]+" "+arrayTextoLector[1]+" "+arrayTextoLector[2]);					
							fechaNacimiento = $.trim(arrayTextoLector[6]+"-"+arrayTextoLector[7]+"-"+arrayTextoLector[8]);
						}
						$("#divMensaje").html("&nbsp;");
						$("#textoLector").val("");
						$('#tecladoFlotante').hide();
						
						// --> Obtener edad
						if(esFechaValida(fechaNacimiento))
						{
							$.post("turnero.php",
							{
								consultaAjax:   		'',
								accion:         		'calcularEdad',
								wemp_pmla:        		$('#wemp_pmla').val(),
								fechaNacimiento:		fechaNacimiento
							}, function(respuesta){
								$("#edadPaciente").val(respuesta.cantidad);
								$("[for=radio"+respuesta.tipo+"]").trigger("click");
								if($.trim(respuesta.tipoDoc) != '')
								{
									onClickTemp = $("[for=radio"+respuesta.tipoDoc+"]").attr("onClick");
									$("[for=radio"+respuesta.tipoDoc+"]").attr("onClick", "");
									$("[for=radio"+respuesta.tipoDoc+"]").trigger("click");
									$("[for=radio"+respuesta.tipoDoc+"]").attr("onClick", onClickTemp);
								}
							}, 'json');
						}
					}
					else
					{
						reiniciarPantalla();
						$("#divMensaje").html("Formato invalido, solo se permiten cedulas o tarjetas de identidad.");						
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
	//	--> Desplega el teclado 
	//-------------------------------------------------------------------
	function lectorCaracter(evento)	
	{
		//console.log(evento.key+"->"+evento.code);
		
		$("#numDocumento").css({"color":"#FCFCED"});
		if($("#numDocumento").val() != " ")
			$("#textoLector").val($("#textoLector").val()+$("#numDocumento").val());
		
		$("#numDocumento").val("");
		
		if(evento.code == "Tab")
		{
			$("#textoLector").val($("#textoLector").val()+"|");
			$("#numDocumento").focus();
		}
		
		if(evento.code == "Enter")
		{
			textoLector 		= $("#textoLector").val();
			arrayTextoLector 	= textoLector.split('|');
			
			// console.log(arrayTextoLector);			
			// return 0;
			
			$("#numDocumento").val((arrayTextoLector[0]*1));
			$("#inputNombrePaciente").val(arrayTextoLector[3]+" "+arrayTextoLector[4]+" "+arrayTextoLector[1]+" "+arrayTextoLector[2]);
			$("#textoLector").val("");
			$("#numDocumento").css({"color":"#000000"});
			$('#tecladoFlotante').hide();
			
			$.post("turnero.php",
			{
				consultaAjax:   		'',
				accion:         		'calcularEdad',
				wemp_pmla:        		$('#wemp_pmla').val(),
				fechaNacimiento:		$.trim(arrayTextoLector[6]+"-"+arrayTextoLector[7]+"-"+arrayTextoLector[8])
			}, function(respuesta){
				$("#edadPaciente").val(respuesta.cantidad);
				$("[for=radio"+respuesta.tipo+"]").trigger("click");
				if($.trim(respuesta.tipoDoc) != '')
					$("[for=radio"+respuesta.tipoDoc+"]").trigger("click");
			}, 'json');
		}
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
			$("#divMensaje").html("Debe seleccionar el tipo de documento.");
			return;
		}
		
		// --> Si no han ingresado el numero de documento
		if($("#numDocumento").val() == "")
		{
			$("#divMensaje").html("Debe ingresar el numero de documento.");
			return;
		}
		
		// --> Si no han ingresado el nombre
		if($.trim($("#inputNombrePaciente").val()) == "")
		{
			$("#divMensaje").html("Debe ingresar el nombre.");
			return;
		}
		
		// --> Si no han ingresado la edad
		if($.trim($("#edadPaciente").val()) == "")
		{
			$("#divMensaje").html("Debe ingresar la edad.");
			return;
		}
		
		// --> Si no han seleccionado el tipo de edad
		if($("[name=tipoEdad]:checked").val() == undefined)
		{
			$("#divMensaje").html("Debe seleccionar si la edad corresponde a: a&ntilde;os, meses o d&iacute;as.");
			return;
		}
		
		// --> Si no han seleccionado el tipo de entidad
		if($("[name=categoriaEmp]:checked").val() == undefined)
		{
			$("#divMensaje").html("Debe seleccionar la categor&iacute;a.");
			return;
		}
		
		$.post("turnero.php",
		{
			consultaAjax:   		'',
			accion:         		'generarTurno',
			wemp_pmla:        		$('#wemp_pmla').val(),
			tipDocumento:			$("[name=tipDocumento]:checked").val(),
			numDocumento:			$.trim($("#numDocumento").val()),			
			nombrePaciente:			$.trim($("#inputNombrePaciente").val()),
			edadPaciente:			$.trim($("#edadPaciente").val()),
			tipoEdad:				$("[name=tipoEdad]:checked").val(),
			categoriaEmp:			$("[name=categoriaEmp]:checked").val(),
			validarExisteTurno:		validarExisteTurno,
			turnoACancelar:			turnoACancelar,
			codigoTurnero:			$("#codigoTurnero").val()
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
		}, 6000);

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
		$("#edadPaciente").val("");
		$("[name=tipDocumento]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
		$("[name=categoriaEmp]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
		$("[name=tipoEdad]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
		$("#divMensaje").html("&nbsp;");
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
				$.post("turnero.php",
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
	//	--> Desplega el teclado 
	//-------------------------------------------------------------------
	function verTeclado(Elemento, origen)
	{
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
		$("[name="+nameRadios+"]").removeAttr("checked");
		$(elemento).prev().attr("checked", "checked");
	}
	
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
		[tipo=obligatorio]::-moz-placeholder {color:red; background:lightyellow;font-size:2rem}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:red; background:lightyellow;font-size:2rem}
		[tipo=obligatorio]:-moz-placeholder {color:red; background:lightyellow;font-size:2rem}

		.botonteclado {
			border: 			1px solid #9CC5E2;
			background-color:	#E3F1FA;
			width:				3.3rem;
			height:				3rem;
			font-size: 			4rem;
			font-weight: 		normal;
			border-radius:		0.4em;
		}
		.botonteclado2 {
			border: 			1px solid #333333;
			background-color:	#E3F1FA;
			width:				3.3rem;
			height:				3rem;
			font-size: 			4rem;
			font-weight: 		bold;
			border-radius:		0.4em;
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

	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	
	<!--<BODY style="overflow:hidden">-->
	<BODY style="overflow:hidden" id="bodyPrincipal">
	<?php
	
	$anchoAltoRadios = "width:2.5rem;height:2.1rem";
	
	$arrTipDoc = array();
	// --> Obtener maestro de tipos de documento
	$sqlTipDoc = "SELECT Codigo, Descripcion
					FROM root_000007
				   WHERE Codigo IN('CC', 'TI', 'RC', 'NU', 'CE', 'PA')
	";
	$resTipDoc = mysql_query($sqlTipDoc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipDoc):</b><br>".mysql_error());
	while($rowTipDoc = mysql_fetch_array($resTipDoc))
		$arrTipDoc[$rowTipDoc['Codigo']] = $rowTipDoc['Descripcion'];
	
	// --> Obtener maestro de categorias de pacientes
	$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$arrCategEmp 	 = array();
	$sqlCatEmp = "SELECT Catcod, Catnom, Catord
					FROM ".$wbasedato."_000207
				   WHERE Catest = 'on'
				ORDER BY Catord
	";
	$resCatEmp = mysql_query($sqlCatEmp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCatEmp):</b><br>".mysql_error());
	while($rowCatEmp = mysql_fetch_array($resCatEmp))
		$arrCategEmp[$rowCatEmp['Catcod']] = strtoupper($rowCatEmp['Catnom']);

	if(!isset($codigoTurnero) || trim($codigoTurnero) == '')
		$codigoTurnero = '*';
	
	// --> Pintar pantalla para asignar el turno
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	$wlogemp = $institucion->baseDeDatos;
	echo "
	<input type='hidden' id='wemp_pmla' 		value='".$wemp_pmla."'>
	<input type='hidden' id='codigoTurnero' 	value='".trim($codigoTurnero)."'>
	<input type='hidden' id='textoLector' 		value='' numTabs='0'>

	<div id='accordionPrincipal' align='center' style='margin: auto auto;'>
		<h1 style='font-size: 3rem;background:#75C3EB' align='center'>
		<img src='../../images/medical/root/".$wlogemp.".jpg' width=120 heigth=76>
			&nbsp;
			Es un placer servirle.
			&nbsp;
			<img width='125' heigth='100' src='../../images/medical/root/".$wlogemp.".jpg'>
		</h1>
		<div style='color:#000000;font-family: verdana;font-weight: normal;font-size: 2rem;' align='center'>
			<table style='width:80%;margin-top:0px;margin-bottom:2px;font-family: verdana;font-weight: normal;font-size: 2rem;'>
				<tr>
					<td id='divMensaje' colspan='2' style='padding:2px;color:#F79391;' align='center'>&nbsp;</td>
				</tr>
				<tr align='left'>
					<td colspan='2'>TIPO DE DOCUMENTO:</td>
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
								<label onClick='checkearRadio(this);obtenerNombrePaciente()' style='border-radius:0.4em;".$anchoAltoRadios."' for='radio".$codTipDoc."'>&nbsp;</label>&nbsp;&nbsp;".$nomTipDoc."
							</td>
							".(($x == 3) ? "</tr>" : "");
							
							$x = (($x == 3) ? 0 : $x);
						}
				echo "	</table>
					</td>
				</tr>			
				<tr>
					<td align='left'>DOCUMENTO:</td>
					<td><input id='numDocumento' 		type='text' tipo='obligatorio'	onClick='verTeclado(this, \"DOC\")' readonly style='margin-top:5px;background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:38rem;font-size: 2rem'></td>
				</tr>";
	echo "		<tr>
					<td align='left'>NOMBRE:</td>
					<td><input id='inputNombrePaciente' type='text' tipo='obligatorio'	onClick='verTeclado(this, \"NOM\")' readonly style='margin-top:5px;background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:38rem;font-size: 2rem'></td>
				</tr>	
				<tr>
					<td align='left'>EDAD:</td>
					<td class='radio' style='color:#333333;font-size: 1rem;'>
						<input id='edadPaciente'		type='text' tipo='obligatorio'  onClick='verTeclado(this, \"EDA\")' 			readonly style='margin-top:5px;background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:7rem;font-size: 2rem'>
						&nbsp;
						<input type='radio' name='tipoEdad' style='".$anchoAltoRadios."' value='A' id='radioA' />
						<label style='border-radius:0.4em;".$anchoAltoRadios."' for='radioA' onClick='checkearRadio(this)'>&nbsp;</label>&nbsp;&nbsp;A&ntilde;os
						<input type='radio' name='tipoEdad' style='".$anchoAltoRadios."' value='M' id='radioM' />
						<label style='border-radius:0.4em;".$anchoAltoRadios."' for='radioM' onClick='checkearRadio(this)'>&nbsp;</label>&nbsp;&nbsp;Meses
						<input type='radio' name='tipoEdad' style='".$anchoAltoRadios."' value='D' id='radioD' />
						<label style='border-radius:0.4em;".$anchoAltoRadios."' for='radioD' onClick='checkearRadio(this)'>&nbsp;</label>&nbsp;&nbsp;D&iacute;as
					</td>
				</tr>
				<tr>
					<td align='left'>CATEGOR&Iacute;A:</td>
				</tr>
				<tr>
					<td colspan='2' align='center'>			
						<table style='color:#333333;font-size: 1rem;margin-top:4px;margin-bottom:2px;' class='radio'>
						";
						$x = 0;
						foreach($arrCategEmp as $codCatEmp => $nomCatEmp)
						{
							$x++;
							echo (($x == 1) ? "<tr>" : "")."
							<td style='padding:2px'>
								&nbsp;&nbsp;
								<input type='radio' style='".$anchoAltoRadios."' name='categoriaEmp' value='".$codCatEmp."' id='radio".$codCatEmp."' />
								<label style='border-radius:0.4em;".$anchoAltoRadios."' for='radio".$codCatEmp."' onClick='checkearRadio(this)'>&nbsp;</label>&nbsp;&nbsp;".$nomCatEmp."
							</td>
							".(($x == 4) ? "</tr>" : "");

							$x = (($x == 4) ? 0 : $x);
						}
				echo "	</table>
					</td>
				</tr>
			</table>
			<div id='tecladoFlotante' style='display:none;z-index:10000;position: absolute;' elementoEscribir=''>
				<table style='background-color:#FFFFFF;border:1px solid #AFAFAF;margin-top:1.3rem;border-radius:0.4em;border-collapse: separate;border-spacing: 0.4rem 0.4rem;'>
					<tr>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='Q'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='W'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='E'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='R'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='T'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='Y'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='U'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='I'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='O'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='7'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='8'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='9'></td>
					</tr>
					<tr>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='A'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='S'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='D'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='F'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='G'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='H'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='J'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='K'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='L'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='4'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='5'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='6'></td>
					</tr>
					<tr>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='Z'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='X'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='C'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='V'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='B'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='N'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='M'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='Ñ'></td>
						<td><input class='botonteclado' type='button' onclick='teclado(this)' value='P'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='1'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='2'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='3'></td>
					</tr>
					<tr>
						<td colspan='7' align='center'><input class='botonteclado' type='button' style='width:18.4rem;' onclick='teclado(this)' value=' '></td>
						<td colspan='2' align='center'><input class='botonteclado2' type='button' id='botonBorrar' style='width:6.8rem;' onclick='teclado(this)' value='Borrar'></td>
						<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='0'></td>
					</tr>
				</table>
			</div>
			<table width='100%'>
				<tr>
					<td width='30%'></td>
					<td width='40%' align='center'><button id='botonAceptar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 3rem;' onclick='generarTurno(\"true\", \"\")'>Aceptar</button></td>
					<td width='30%' align='right'><button id='botonLimpiar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 1rem;' onclick='reiniciarPantalla()'>Limpiar</button></td>
				</tr>
			</table>
		</div>
	</div>
	<div id='fichoTurno' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'></div>
	<div id='fichoTurnoImp' style='display:none;'></div>
	<div id='msjReimpTurno' style='font-family: verdana;color:#F95C59;font-size:1.2rem;display:none;' align='center' >
			Usted ya tiene un turno asignado para hoy.
	</div>
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

//}//Fin de session
?>
