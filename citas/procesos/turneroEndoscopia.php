<?php
include_once("conex.php");
//=========================================================================================================================================\\
//          TURNERO URGENCIAS
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:                Eimer Castro
//FECHA DE CREACION:    2016-03-09
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
            $wactualiz='2018-09-19';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//	2018-09-19, Jerson Trujillo: Se agrega la funcion toggleFullScreen() de javascript para que al primer toque de la pantalla
//				esta se ponga en modo pantalla completa.
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------
//  EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

    $wuse           = 'Turnero';    //$user_session[1];
    

    include_once("root/comun.php");
    

    $conex          = obtenerConexionBD("matrix");
    //$wbasedato      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    //$wbasedato      = $solucionCitas;   //consultarAliasPorAplicacion($conex, $wemp_pmla, 'citasen');
    $wfecha         = date("Y-m-d");
    $whora          = date("H:i:s");


	//Verónica Arismendy Para obtener la descripción de agenda citas en root_00051 con el parámetro agenda y el value que llega por url a este archivo.
	$sqlObtenerDescripcion = "SELECT Detdes
			FROM root_000051 r
			WHERE r.Detapl = 'citas'
			AND r.Detval = '".$solucionCitas."'
	";
	
	$result = mysql_query($sqlObtenerDescripcion, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlObtenerDescripcion."):</b><br>".mysql_error());
	$row = mysql_fetch_assoc($result);		

	if(isset($row["Detdes"]) && $row["Detdes"] != ""){
		$descripcionPrograma = false;
	}	else{
		$descripcionPrograma = false;
	}	

//=====================================================================================================================================================================
//      F U N C I O N E S    G E N E R A L E S    P H P
//=====================================================================================================================================================================

    //------------------------------------------------------------------------------------
    //  --> Genera el html del tiquete del turno
    //------------------------------------------------------------------------------------
    function htmlTurno($turno, $cedulaPac, $nombrePac, $reimpresion)
    {
        $html = "
        <table style='font-family: verdana;font-size:1rem;'>
            <tr>
                <td colspan='2' align='center'>
                    <img width='118' heigth='58' src='../../images/medical/root/logo_Clinica.jpg'>
                </td>
            </tr>
            <tr>
                <td colspan='2' align='center'>
                    Usted ser&aacute; atendido de acuerdo a la hora de su cita.
                    <br><br>
                </td>
            </tr>
            <tr>
                <td >Turno:&nbsp;&nbsp;</td>
                <td align='right' style='font-size:2rem;'><b>".substr($turno, 7)."</b></td></tr>
            <tr>
                <td style='padding-bottom:3px;' colspan='2'>Documento: &nbsp;&nbsp;&nbsp;".$cedulaPac."</td>
            </tr>
            <tr>
                <td style='padding-bottom:3px;' colspan='2'>".ucwords(strtolower($nombrePac))."</td>
            </tr>
            <tr>
                <td colspan='2' align='center' style='font-size:0.8rem'>
                    <br><b>Por favor conserve este tiquete hasta finalizar la atenci&oacute;n.</b>
                </td>
            </tr>
            <tr>
                <td colspan='2' align='center' style='font-size:0.7rem'>
                    ".(($reimpresion) ? "<b>(Reimpresión)</b>" : "")." Fecha: ".date('Y-m-d')." &nbsp;Hora: ".date('g:i:s a')."
                </td>
            </tr>
        </table>";

        return $html;
    }
	
	function getPrefixTables($prefix, $conex){
		
		$sql = "SELECT descripcion FROM 
					root_000117
				WHERE nombreCc = '".$prefix."'";
		$res = mysql_query($sql, $conex);
		$row = mysql_fetch_assoc($res);
				
		$newPrefix = substr($row["descripcion"],0,3);
		
		return strtolower($newPrefix);
	}

//=======================================================================================================================================================
//      F I N    F U N C I O N E S   P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//      F I L T R O S    D E    L L A M A D O S     P O R   P O S T    J Q U E R Y      O   A J A X
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

			//Verónica Arismendy si no encuentra la cédula en root_00036 se busca en la agenda del día
			$wfecha = date("Y-m-d");
			if($respuesta['nombrePac'] == ""){
				$sql = "SELECT				
						nom_pac				
					FROM
						".$wbasedato."_000009 b
					WHERE
						fecha = '".$wfecha."'
						AND Cedula = '".$numDocumento."'						
					";					

					$result = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sql."):</b><br>".mysql_error());
					if($rowResult = mysql_fetch_array($result)){
						$respuesta['nombrePac'] = utf8_encode($rowResult['nom_pac']);
					}						
			}
		
            echo json_encode($respuesta);

            break;
            return;
        }
        case 'generarTurno':
        {
			$prefix = getPrefixTables($wbasedato, $conex);
            $respuesta = array('Error' => false, 'yaExisteTurnoHoy' => false, 'Mensaje' => '', 'Turno' => ''
				, 'fichoTurno' => '', 'solicitarNuevo' => false, 'msjSolicitarNuevo' => '');

            // --> Validar si ya existe un turno asignado hoy, para el documento
            $sqlValTurno = " SELECT ".$prefix."tur
                               FROM " . $wbasedato . "_000023
                              WHERE Fecha_data  = '" . date("Y-m-d")."'
                                AND ".$prefix."doc      = '" . $numDocumento."'
                                AND ".$prefix."tip      = '" . $tipDocumento."'
								AND ".$prefix."est      = 'on'
                              ORDER BY ".$prefix."tur DESC";
 
            $resValTurno = mysql_query($sqlValTurno, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlValTurno):</b><br>".mysql_error());
			
            if($validarExisteTurno == 'true' && $rowValTurno = mysql_fetch_array($resValTurno)) {	
			
                $respuesta['yaExisteTurnoHoy']  = true;
                $respuesta['Turno']             = $rowValTurno[$prefix.'tur'];
                $respuesta['fichoTurno']        = htmlTurno($rowValTurno[$prefix.'tur'], $numDocumento, $nombrePaciente, true);
            } else {
                // --> Obtener el ultimo consecutivo
                $sqlObtConsec = " SELECT MAX(REPLACE(".$prefix."tur, '-E', '')*1) AS turno
                                    FROM " . $wbasedato . "_000023
                                   WHERE ".$prefix."tur LIKE '".date('ymd')."%'";

                $resObtConsec = mysql_query($sqlObtConsec, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlObtConsec):</b><br>".mysql_error());
				
                if($rowObtConsec = mysql_fetch_array($resObtConsec)) {
					
                    $fechaUltiConse = substr($rowObtConsec['turno'], 0, 6);
                    $ultConsecutivo = (($fechaUltiConse == date('ymd')) ? substr($rowObtConsec['turno'], 6) : 0);
                    $ultConsecutivo = ($ultConsecutivo*1)+1;
                    // --> Asignar ceros a la izquierda hasta completar 3 digitos
                    while(strlen($ultConsecutivo) < 3)
                        $ultConsecutivo = '0'.$ultConsecutivo;

                    $nuevoTurno     = date('ymd').'-E'.$ultConsecutivo;

                    // --> Asignarle el turno al paciente
                    $usuario = explode("-", $_SESSION['user']);
                    $wuse = $usuario[1];
										
					//Verónica Arismendy
					////Antes de hacer el insert del turno nuevo se deben inactivar los turnos que haya tenido antes
					$sqlInactivarTurnosViejos = "UPDATE
													" . $wbasedato . "_000023
												SET 
													  ".$prefix."est = 'off'
												WHERE
													Fecha_data  = '" . date("Y-m-d")."'
													AND ".$prefix."doc      = '" . $numDocumento."'
													AND ".$prefix."tip      = '" . $tipDocumento."'
					";
					
					$resCancelarTurno = mysql_query($sqlInactivarTurnosViejos, $conex);
					
					//Lueo se hace el insert del nuevo turno
                    $sqlAsigTur = " INSERT INTO " . $wbasedato . "_000023 (Medico, Fecha_data, Hora_data, ".$prefix."tur, ".$prefix."tip, ".$prefix."doc, ".$prefix."hau, ".$prefix."eau, ".$prefix."est, Seguridad, id)
                                                        VALUES ('" . $wbasedato . "', '".date('Y-m-d')."', '".date('H:i:s')."', '".$nuevoTurno."', '".$tipDocumento."', '".$numDocumento."', '".date('H:i:s')."', 'on', 'on', 'C-".$wuse."', '')";
                    $resObtConsec = mysql_query($sqlAsigTur, $conex);

                    // --> Si ha ocurrido un error guardando el turno
                    if(!$resObtConsec) {
                        $respuesta['Error']     = true;
                        $respuesta['Mensaje']   = " <span style='font-size:20px'>Disc&uacute;lpenos, a ocurrido un error asignando el turno.<br>Por favor contacte al personal de soporte.</span><br>
                                                    <span style='font-size:10px'>($sqlAsigTur: ".mysql_error().')</span>';
                    } else {	
						// --> Genero el ficho del turno					
                        $respuesta['Turno']         = $nuevoTurno;
                        $respuesta['fichoTurno']    = htmlTurno($nuevoTurno, $numDocumento, $nombrePaciente, false);
                    }
                } else {
                    $respuesta['Error']     = true;
                    $respuesta['Mensaje']   = 'Error: El turno no se ha podido asignar.';
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
//      F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//  I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
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
//      F U N C I O N E S    G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	document.onkeydown 	= lector;

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

        $("#radio").buttonset();
        ajustarResolucioPantalla();

        // --> Activar teclado numerico
        $(".botonteclado").parent().hide();
        $("#botonBorrar").css("width","6.8rem").parent().attr("colspan", "2");
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

	
	function lector(event) 
	{				
		event 	= event || window.event;
		cadena 	= event.key+"->"+event.keyCode;
				
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
						$("#esLector").val("true");
						primerCaracterDelsegundoTab = arrayTextoLector[1].substr(0,1);
						
						// --> Si el primer caracter del segundo tab es un entero, indica que es una tarjeta de identidad
						if(Number.isInteger(primerCaracterDelsegundoTab*1))
						{
							$("#numDocumento").val($.trim(arrayTextoLector[0]+primerCaracterDelsegundoTab));
							
							segundoApellido = $.trim(arrayTextoLector[1].substr(1,arrayTextoLector[1].length));
							nombrePaciente = $.trim(arrayTextoLector[3])+" "+$.trim(arrayTextoLector[4])+" "+segundoApellido+" "+$.trim(arrayTextoLector[2])
							$("#divMensaje").html(nombrePaciente);					
							$("#nombrePaciente").val(nombrePaciente);
							tipoDoc = "TI";
							
						}
						// --> Sino, es una cedula
						else
						{						
							$("#numDocumento").val((arrayTextoLector[0]*1));
							nombrePaciente = arrayTextoLector[3]+" "+arrayTextoLector[4]+" "+arrayTextoLector[1]+" "+arrayTextoLector[2];
							$("#divMensaje").html(nombrePaciente);					
							$("#nombrePaciente").val(nombrePaciente);
							tipoDoc = "CC";							
						}
						
						$("[for=radio"+tipoDoc+"]").click();	
						
						$("#textoLector").val("");	
						$("#esLector").val("");						
					}
					else
					{
						$("#esLector").val("");
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
    //  --> Funcion que ajusta la vista a la resolucion de la pantalla
    //-------------------------------------------------------------------
    function ajustarResolucioPantalla()
    {
        var height = 0;
        var width  = 0;
        if (self.screen){     // for NN4 and IE4
            width   = screen.width;
            height  = screen.height
        }
        else
            if (self.java){   // for NN3 with enabled Java
                var jkit = java.awt.Toolkit.getDefaultToolkit();
                var scrsize = jkit.getScreenSize();
                width   = scrsize.width;
                height  = scrsize.height;
            }

        width   = width*0.99;
        height  = height*0.90;

        if(width > 0 && height > 0)
            $("#accordionPrincipal").css({"width":width, "height":height});
        else
            $("#accordionPrincipal").css({"width": "100 %", "height": "100%"});
    }

    //-------------------------------------------------------------------
    //  --> Funcion que genera el turno
    //-------------------------------------------------------------------
    function generarTurno(validarExisteTurno, turnoACancelar)
    {
        // --> Si no han ingresado el numero de documento
        if($("#numDocumento").val() == "")
        {
            $("#divMensaje").html("Debe ingresar el numero de documento.");
            return;
        }

        // --> Si no han seleccionado el tipo de documento
        if($("[name=tipDocumento]:checked").val() == undefined)
        {
            $("#divMensaje").html("Debe seleccionar el tipo de documento.");
            return;
        }

        $.post("turneroEndoscopia.php",
        {
            consultaAjax:           '',
            accion:                 'generarTurno',
            wemp_pmla:              $('#wemp_pmla').val(),
            wbasedato:              $('#wbasedato').val(),
            numDocumento:           $("#numDocumento").val(),
            tipDocumento:           $("[name=tipDocumento]:checked").val(),
            nombrePaciente:         $("#nombrePaciente").val(),
            validarExisteTurno:     validarExisteTurno,
            turnoACancelar:         turnoACancelar
        }, function(data){

            if(data.Error)
                $("#divMensaje").html(data.Mensaje);
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
                    if(data.solicitarNuevo){
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
					}else{
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
								"Salir": function(){
									$(this).dialog("close");
									reiniciarPantalla();
									clearTimeout(cerrarVentana);
								}
							}
						});
					}
					
					
                }
                else
                    imprimirTurno(data.fichoTurno);
            }

        }, 'json');
    }

    //---------------------------------------------------------------------------------------
    //  --> Función que pinta una vista previa del turno y lo imprime automaticamente
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
            var contenido   = "<html><body onload='window.print();window.close();'>";
            contenido       = contenido + fichoTurno + "</body></html>";

            var windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=1,height=1,resizable=yes,screenX=1,screenY=1,personalbar=no,scrollbars=no";
            var ventana = window.open( "", "",  windowAttr );
            ventana.document.write(contenido);
            ventana.document.close();
        }, 1000);
    }

    //-------------------------------------------------------------------
    //  --> Reiniciar pantalla para permitir ingresar un nuevo turno
    //-------------------------------------------------------------------
    function reiniciarPantalla()
    {
        // --> Limpiar campos
        $("#numDocumento").val("");
        $("[name=tipDocumento]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
        $("#nombrePaciente").val("");
        $("#divMensaje").html("&nbsp;");

        // --> Activar teclado numerico
        $(".botonteclado").parent().hide();
        $("#botonBorrar").css("width","6.8rem").parent().attr("colspan", "2");
    }

    //-------------------------------------------------------------------
    //  --> Funcion que obtiene el nombre del paciente
    //-------------------------------------------------------------------
    function obtenerNombrePaciente()
    {		
		if($("[name=tipDocumento]:checked").val() != undefined && $("#numDocumento").val() != '')
		{						
			var htmlDiv = $("#divMensaje").html();
			var esLector = $("#esLector").val();
			if(htmlDiv.length >= 10 && esLector != ""){	
				$("#textoLector").val("");			
				$("#botonAceptar").click();	
			}else{				
				setTimeout(function(){
				$.post("turneroEndoscopia.php",
				{
							consultaAjax:           '',
							accion:                 'obtenerNombrePaciente',
							wemp_pmla:              $('#wemp_pmla').val(),
							wbasedato:              $('#wbasedato').val(),
							numDocumento:           $("#numDocumento").val(),
							tipDocumento:           $("[name=tipDocumento]:checked").val()
						}, function(data){
							nombrePac = ((data.nombrePac != '') ? data.nombrePac : "&nbsp;" );
							$("#divMensaje").html(nombrePac);
							$("#nombrePaciente").val(data.nombrePac);
						}, 'json');
				}, 200);
			}
		}
		else
		{
			nombrePac = "&nbsp;";
			$("#divMensaje").html(nombrePac);
			$("#nombrePaciente").val(nombrePac);
		}
		//}
    }
    //-------------------------------------------------------------------
    //  --> Teclado
    //-------------------------------------------------------------------
    function teclado(Elemento)
    {
        var valDoc = $("#numDocumento").val();

        if($(Elemento).val() == "Borrar")
            valDoc = valDoc.substr(0, valDoc.length - 1)
        else
            valDoc = valDoc+$(Elemento).val();

        $("#numDocumento").val(valDoc);
        obtenerNombrePaciente();
    }
    //-------------------------------------------------------------------
    //  --> Activa un teclado segun el tipo de documento
    //-------------------------------------------------------------------
    function activarTeclado(Elemento)
    {
        $(Elemento).prev().attr("checked", "checked");
        var valTipoDoc = $(Elemento).prev().val();

        // --> Mostrar solo teclado numerico
        if( valTipoDoc == "CC" || valTipoDoc == "TI" || valTipoDoc == "NU")
        {
            $(".botonteclado").parent().hide();
            $("#botonBorrar").css("width","6.8rem").parent().attr("colspan", "2");
        }
        // --> Mostrar teclado alfanumerico
        else
        {
            $(".botonteclado").parent().show();
            $("#botonBorrar").css("width","18.4rem").parent().attr("colspan", "10");
        }
    }

//=======================================================================================================================================================
//  F I N  F U N C I O N E S  J A V A S C R I P T
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
            border:             1px solid #9CC5E2;
            background-color:   #E3F1FA;
            width:              3.3rem;
            height:             3rem;
            font-size:          4rem;
            font-weight:        normal;
            border-radius:      0.4em
        }
        .botonteclado2 {
            border:             1px solid #333333;
            background-color:   #E3F1FA;
            width:              3.3rem;
            height:             3rem;
            font-size:          4rem;
            font-weight:        bold;
            border-radius:      0.4em
        }
        .botonteclado:hover {
            position:           relative;
            top:                1px;
            left:               1px;
            background-color:   #75C3EB;
            color:              #ffffff;
			
        }
        .botonteclado2:hover {
            position:           relative;
            top:                1px;
            left:               1px;
            background-color:   #75C3EB;
            color:              #ffffff;
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
    <BODY style="overflow:hidden" id="bodyPrincipal">
    <?php
    $arrTipDoc = array();
    // --> Obtener maestro de tipos de documento
    $sqlTipDoc = "SELECT Codigo, Descripcion
                    FROM root_000007
                   WHERE Codigo IN('CC', 'TI', 'RC', 'NU', 'CE', 'PA')
    ";
    $resTipDoc = mysql_query($sqlTipDoc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipDoc):</b><br>".mysql_error());
    while($rowTipDoc = mysql_fetch_array($resTipDoc))
        $arrTipDoc[$rowTipDoc['Codigo']] = $rowTipDoc['Descripcion'];

    // --> Pintar pantalla para asignar el turno
    echo "
    <input type='hidden' id='wemp_pmla'         value='".$wemp_pmla."'>
    <input type='hidden' id='wbasedato'         value='".$solucionCitas."'>
    <input type='hidden' id='nombrePaciente'    value=''>

	
	<input type='hidden' id='textoLector' 		value='' numTabs='0'>
	<input type='hidden' id='esLector'>
	
    <div id='accordionPrincipal' align='center' style='margin: auto auto;'>
        <h1 style='font-size: 3rem;background:#75C3EB' align='center'>
            <img width='125' heigth='61' src='../../images/medical/root/logoClinicaGrande.png'>
            &nbsp;
            Es un placer servirle.
            &nbsp;
            <img width='120' heigth='100' src='../../images/medical/root/Logo_MatrixAzulClaro.png'>
        </h1>
        <div style='color:#333333;font-family: verdana;font-weight: normal;font-size: 3rem;' align='center'>
            <table style='font-size: 1.3rem;margin-top:4px;margin-bottom:2px;' id='radio'>
            ";
            $x = 0;
            foreach($arrTipDoc as $codTipDoc => $nomTipDoc)
            {
                $x++;
                echo (($x == 1) ? "<tr>" : "")."
                <td style='padding:2px'>
                    &nbsp;&nbsp;
                    <input type='radio' name='tipDocumento' value='".$codTipDoc."' id='radio".$codTipDoc."' />
                    <label onClick='activarTeclado(this);obtenerNombrePaciente()' style='border-radius:0.4em;width:2.5rem;height:2.3rem' for='radio".$codTipDoc."'>&nbsp;</label>&nbsp;&nbsp;".$nomTipDoc."
                </td>
                ".(($x == 3) ? "</tr>" : "");

                $x = (($x == 3) ? 0 : $x);
            }
    echo "  </table>
            <div id='divMensaje' style='padding:2px;color:#F79391;'>&nbsp;</div>
            Documento:<input id='numDocumento' type='text' tipo='obligatorio'  disabled='disabled' style='background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:40%;font-size: 2.4rem'>
            <br>
			<table width='100%'>
				<tr>
					<td width='40%'></td>
					<td width='20%' align='center'>
						<div id='tecladoFlotante' >				
							<table style='border:1px solid #AFAFAF;margin-top:1.3rem;border-radius:0.4em;border-collapse: separate;border-spacing: 0.4rem 0.4rem;'>
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
									<td colspan='10' align='center'><input class='botonteclado2' type='button' id='botonBorrar' style='width:18.4rem;' onclick='teclado(this)' value='Borrar'></td>
									<td><input class='botonteclado2' type='button' onclick='teclado(this)' value='0'></td>
								</tr>
							</table>
						</div>
					</td>
					<td width='40%' align='left' valign='bottom'>
						<button id='botonAceptar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 3rem;' onclick='generarTurno(\"true\", \"\")'>Aceptar</button>
					</td>
				</tr>
			</table>            
        </div>
    </div>
    <div id='fichoTurno' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'></div>
    <div id='fichoTurnoImp' style='display:none;'></div>
    <div id='msjReimpTurno' style='font-family: verdana;font-size:1.2rem;display:none;' align='center' >
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
//  F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
}

//}//Fin de session
?>
