﻿<?php
include_once("conex.php");
//====================================================================================================\\
//          MONITOR DE TURNOS CITAS ENDOSCOPIA
//====================================================================================================\\
//DESCRIPCION:          Este script permite mostrar en la pantalla de un televisor los llamados a
//                      atención y las fases de los procesos en los cuales se encuentra un paciente en
//                      en un procedimiento como lo es Endoscopia
//AUTOR:                Eimer Castro
//FECHA DE CREACION:    2016-03-09
//-------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//                  
//	2021-07-05 Luis F Meneses: Se aplican nuevos estilos (/matrix/ips/procesos/CartDig.css)
//  2018-03-12   Arleyda Insignares C.  Se adiciona 'group by' en la consulta de lista de turnos, para
//                                      que los pacientes con doble agenda, se listen una sola vez.
//  2018-03-05   Arleyda Insignares C.  Se adiciona inner join a la tabla 000009, para que cuando una 
//                                      cita sea cancelada, pueda ser excluida de la lista de turnos.
//  2018-02-19   Arleyda Insignares C.  Se adiciona campo 'Raclis' en tabla 000032. Cuando esté en on
//                                      no será mostrado en el listado de Estados. 
//                         
//  2018-02-13   Arleyda Insignares C.  Se adiciona filtro para que no sean visualizados los registros
//  con el campo 'estado' en 'off'.
//  Se modifica mensaje inicial para el paciente que está en espera de Atención
//-------------------------------------------------------------------------------------------------------
    $wactualiz='2021-07-05';
//-------------------------------------------------------------------------------------------------------
//  EJECUCION DEL SCRIPT
//-------------------------------------------------------------------------------------------------------
    $wuse           = 'MONITOR';
    

    include_once("root/comun.php");
    include_once("citas/funcionesAgendaCitas.php");
    
    $wbasedato      = isset($solucionCitas) ? $solucionCitas : "citasen";
    $wfecha         = date("Y-m-d");
    $whora          = date("H:i:s");
	

//=============================================================================================
//      F U N C I O N E S    G E N E R A L E S    P H P
//=============================================================================================

    //------------------------------------------------------------------------------------
    //  --> Consulta si hay alertas y genera el html para pintarlas
    //------------------------------------------------------------------------------------
    function obtenerAlertas()
    {
        global $wbasedato;
        global $conex;
        global $monitorSala;
        global $wfecha;
        global $whora;

        $respAlertas    = array("hayAlertas" => false, "htmlAlertas" => "");
        $primeraVez     = true;
		$newPrefix = getPrefixTables($wbasedato);

		$sqlTurnos = "SELECT l.".$newPrefix."tur,  ".$newPrefix."acp, c.RacNam, t.Actnma, m.Ubides, l.".$newPrefix."els as llamadoSinCita, l.".$newPrefix."ecs as colgadoSinCita
						FROM ".$wbasedato."_000023 l
						LEFT JOIN ".$wbasedato."_000032 c on c.Raccod = l.".$newPrefix."acp
						LEFT JOIN ".$wbasedato."_000031 t on t.Actcod = c.Racacn
						LEFT JOIN ".$wbasedato."_000027 m on m.Ubicod = l.".$newPrefix."ubi
                        INNER JOIN {$wbasedato}_000009 CE09 ON (l.".$newPrefix."doc = replace( replace( CE09.cedula, '\t', '' ) , ' ', '' )  AND l.fecha_data = CE09.Fecha)
						WHERE l.Fecha_data = '".date("Y-m-d")."'
                        AND CE09.Activo = 'A'
						AND l.".$newPrefix."fpr = 'off'
                        AND l.".$newPrefix."est = 'on' ";
        $resTurnosAlertas  = mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());

        $colorFila      = '#F2F5F7;';
        $html_array = array();

		$cantidadFilas = 0;
        if($numRowsTurnosAlertas = mysql_num_rows($resTurnosAlertas)) {
			/*
            $htmlAlertas = "
                <table width='100%' style='color:#000000;font-family: verdana;font-weight: normal;font-size: 4rem;'>
                <tr style='background-color: #2a5db0;color:#ffffff;font-weight:bold;'>
                    <td align='center'># Atenci&oacute;n</td>
                    <td align='center'>Por favor pasar a:</td>
                </tr>
            ";
			*/
			$htmlAlertas = "<table id = 'tablaAlerta'>";

            while($rowTurnosAlertas = mysql_fetch_array($resTurnosAlertas)) {

				if($primeraVez){
                    $primeraVez                 = false;
                    $respAlertas['hayAlertas']  = true;
                }

				$colorFila = (($colorFila == '#DCE5F2') ? '#F2F5F7;' : '#DCE5F2');

				//Espera de Admision
                if(($rowTurnosAlertas['RacNam'] != '' && $rowTurnosAlertas['Actnma'] == "Llamar") || ($rowTurnosAlertas['llamadoSinCita'] == "on" && $rowTurnosAlertas['colgadoSinCita']  == "off" )) {
                    /*
					$htmlAlertas.= "<tr style='background-color:".$colorFila.";'>
                                        <td align='center'>Turno ".substr($rowTurnosAlertas[$newPrefix.'tur'], 7)."</td>
                                        <td align='center'>". utf8_encode($rowTurnosAlertas['Ubides']) . " </td>
                                    </tr>
                                    ";
					*/
					// Cada alerta tiene su título con el turno
					// y un otra fila con el mensaje                    
					$htmlAlertas.= "
						<tr>
							<td class='tdAzul' width='100%'>TURNO ".substr($rowTurnosAlertas[$newPrefix.'tur'], 7)."</td>
						</tr>
						<tr>
							<td class='tdNormal' width='100%'>Por favor pasar a:<br>".utf8_encode($rowTurnosAlertas['Ubides'])."</td>
						</tr>
					";			
					
					$cantidadFilas++;
                }
            }
			 $htmlAlertas.= "</table>";
        } else {
            $htmlAlertas = "";
        }


        if (!$cantidadFilas > 0) {
            $primeraVez = false;
            $respAlertas['hayAlertas']  = false;
        }

		//$respAlertas['hayAlertas']  = true;
		//$htmlAlertas = $sqlTurnos;

		$respAlertas['htmlAlertas'] = $htmlAlertas;

        return $respAlertas;
    }

    //------------------------------------------------------------------------------------
    //  --> Pinta la lista de turnos con su correspondiente estado
    //------------------------------------------------------------------------------------
    function listarTurnos($numPagina)
    {
        global $wbasedato;
        global $conex;
        global $monitorSala;
        global $arrayUbicaciones;
        global $numRegistrosPorPagina;
        global $wfecha;

        $respuesta = array('html' => '', 'totalPaginas' => '', 'numPagina' => '');
		$newPrefix = getPrefixTables($wbasedato);

		/*
        $html       = "
        <div style='background-color:#FFFFFF;color:#E2007A;font-family: verdana;font-weight: normal;font-size: 2.2rem;'>
            La atenci&oacute;n ser&aacute; de acuerdo a la hora programada de la cita.
        </div>";
		*/
		
        $html = "<center><table id='tablaTurnos'>
            <tr>
                <th width='25%'>TURNO</th>
                <th width='75%'>ESTADO</th>
            </tr>
        ";

        $fecha_actual = date('Y-m-d');
		
		// COMENTAR. Quemado para pruebas --------
		//$fecha_actual = '2020-10-23'; 
		// ----------------------------
		
		$infoMaxTimeAcc = getConfigurationCcpTiempoMaximo($wbasedato);

		//Se consultan los turnos del día
        $sqlTurnos = "  SELECT {$newPrefix}acp, cts23.{$newPrefix}tur, cts23.{$newPrefix}doc, c.RacNam, c.Raclis,
                        c.Ractex, c.Raccod, c.Racact,  cts23.idSolcam, cts23." . $newPrefix . "acp as ultimaAccion, 
                        cts23." . $newPrefix . "hua as horaUltimaAccion
					    FROM   {$wbasedato}_000023 cts23
					           LEFT JOIN {$wbasedato}_000032 c ON c.Raccod = cts23.{$newPrefix}acp
                               INNER JOIN {$wbasedato}_000009 CE09 ON (cts23.".$newPrefix."doc = replace( replace( CE09.cedula, '\t', '' ) , ' ', '' )  AND cts23.fecha_data = CE09.Fecha)
				        WHERE   cts23.Fecha_data = '{$fecha_actual}'
    						AND cts23.{$newPrefix}fpr = 'off'                         
                            AND cts23.{$newPrefix}est = 'on' 
                            AND CE09.Activo = 'A'
                            AND c.Raclis = 'off' 
                        GROUP BY  cts23.{$newPrefix}tur 
                        ORDER by cts23." . $newPrefix . "hua desc  ";
		//echo $sqlTurnos;
        $resTurnos  = mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());

        $colorFila  = '#F2F5F7;';
		$html_array = array();

        if($numTurnos = mysql_num_rows($resTurnos)) {

            $contadorFilasEstados = 0;
            while($rowTurnos = mysql_fetch_array($resTurnos)) {		
				$mostrarEnListado = true;
				
				if(isset($row["idSolcam"]) && $row["idSolcam"] != 0){
					$sqlCam = "SELECT fecha_llegada, hora_llegada FROM cencam_000003 WHERE id = ".$row["idSolcam"];
					$resCam = executeQuery($sqlCam);
					$rowCam = mysql_fetch_assoc($resCam);

					if($rowCam["fecha_llegada"] != "0000-00-00" && $rowCam["hora_llegada"] != "00:00:00"){
						$mostrarEnListado = false;
					}
				}else if($rowTurnos["ultimaAccion"] != "" && in_array($rowTurnos["ultimaAccion"] , $infoMaxTimeAcc["arrCCoFinPro"])){
					//Se le suma el tiempo configurado para el centro de costo a la hora en que se realizó la ultima acitividad 
					$nuevafecha = strtotime ( '+' . $infoMaxTimeAcc["maxTime"].' hour' , strtotime ( $rowTurnos["horaUltimaAccion"] ) ) ;
					$nuevafecha = date ( 'H:i:s' , $nuevafecha );
					
					if(date("H:i:s") >= $nuevafecha){
						$mostrarEnListado = false;									
					}
				} else{
					$mostrarEnListado = true;
				}
			
				if($mostrarEnListado){
					$turno = $rowTurnos[$newPrefix.'tur'] != "" ? $rowTurnos[$newPrefix.'tur'] : $rowTurnos[$newPrefix.'doc'] ;
					//Espera de Admision
					if($rowTurnos['RacNam'] != '') {
						$texto = $rowTurnos["Ractex"] != "" ? $rowTurnos['Ractex'] : $rowTurnos['RacNam'];
						/*
						$html_array[] .= "<tr style='background-color:".$colorFila."'>
										<td align='center'>"	.substr($rowTurnos[$newPrefix.'tur'], 7).		"</td>
										<td align='left'>"		. utf8_encode($texto).                "</td>
									</tr>
								";
						*/
						$html_array[] .= "<tr>
										<td class='tdNormal'>"	.substr($rowTurnos[$newPrefix.'tur'], 7).		"</td>
										<td class='tdNormal'>"		. utf8_encode($texto).                "</td>
									</tr>
								";
						$contadorFilasEstados++;

					} else {
						/*
						$html_array[] .= "<tr style='background-color:".$colorFila."'>
										<td align='center'>".substr($rowTurnos[$newPrefix.'tur'], 7)."</td>
										<td align='left'>En espera de Atención</td>
									</tr>
								";
						*/
						$html_array[] .= "<tr>
										<td class='tdNormal'>".substr($rowTurnos[$newPrefix.'tur'], 7)."</td>
										<td class='tdNormal'>En espera de Atención</td>
									</tr>
								";
					}
				}
				
            }
        }

		// --> Paginar, (numRegistrosPorPagina) registros por pagina
		$totalPaginas 			= ((int)(count($html_array)/$numRegistrosPorPagina))+((count($html_array)%$numRegistrosPorPagina > 0) ? 1 : 0);
		$totalPaginas			= (($totalPaginas == 0) ? 1 : $totalPaginas);

		// --> Si se sobrepasa el numero de paginas, vuelve y se inicia con la pagina 1
		if($numPagina+1 > $totalPaginas) {
			$rangoIni 	= 0;
			$rangoFin	= $numRegistrosPorPagina-1;
			$numPagina	= 1;
		} else {
			$rangoIni 	= ($numPagina*$numRegistrosPorPagina);
			$rangoFin 	= $rangoIni+($numRegistrosPorPagina-1);
			$numPagina	= $numPagina+1;
		}

		if(count($html_array) > 0) {
			for($x = $rangoIni; $x <= $rangoFin ; $x++) {
				$html.= $html_array[$x];
			}
		}

        $html.= "</table>";

        $respuesta['html']          = $html;
        $respuesta['totalPaginas']  = $totalPaginas;
        $respuesta['numPagina']     = $numPagina;

        return $respuesta;
    }

//===================================================================================================
//      F I N    F U N C I O N E S   P H P
//===================================================================================================

//===================================================================================================
//      F I L T R O S    D E    L L A M A D O S     P O R   P O S T    J Q U E R Y      O   A J A X
//===================================================================================================
if(isset($accion))
{
    switch($accion)
    {
        case 'actualizarMonitor':
        {
            $arrayUbicaciones               = json_decode(str_replace('\\', '', $arrayUbicaciones), true);
            $respuesta                      = array("htmlListaTurnos" => "", "hayAlertas" => false, "htmlAlertas");

            $respListaTurnos                = listarTurnos($numPagina);
            $respuesta['htmlListaTurnos']   = $respListaTurnos['html'];
            $respuesta['numPagina']         = $respListaTurnos['numPagina'];
            $respuesta['totalPaginas']      = $respListaTurnos['totalPaginas'];

            $respAlertas                    = obtenerAlertas();
            $respuesta['hayAlertas']        = $respAlertas['hayAlertas'];
            $respuesta['htmlAlertas']       = $respAlertas['htmlAlertas'];

            echo json_encode($respuesta);
            break;
            return;
        }
        return;
    }
}
//===============================================================================================
//      F I N   F I L T R O S   A J A X
//================================================================================================

//================================================================================================
//  I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=================================================================================================
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
//===========================================================================================================================
//      F U N C I O N E S    G E N E R A L E S   J A V A S C R I P T
//===========================================================================================================================
    var height = 0;
    var width  = 0;
    var intervalSet;

    $(function(){
		/*
        $("#accordionPrincipal").accordion({
            collapsible: false,
            heightStyle: "content"
        });
        $( "#accordionPrincipal" ).accordion( "option", "icons", {} );

        $("#divContenido").css({"padding": "0.1em"});

        // --> Ajustar la vista a la resolucion de la pantalla
        obtenerResolucioPantalla();
        width1      = width*0.99;
        height1     = height*0.99;

        if(width1 > 0 && height1 > 0)
            $("#accordionPrincipal").css({"width":width1});
        else
            $("#accordionPrincipal").css({"width": "99 %"});
		*/
        // --> Llamado automatico, para que el monitor este actualizando
        setInterval(function(){
            actualizarMonitor();
        }, 10000);

    });
    //-------------------------------------------------------------------
    //  --> Funcion que obtiene la resolucion de la pantalla
    //-------------------------------------------------------------------
    function obtenerResolucioPantalla()
    {
        if (self.screen){     // for NN4 and IE4
            width   = screen.width;
            height  = screen.height
        }
        else
        {
            if (self.java){   // for NN3 with enabled Java
                var jkit = java.awt.Toolkit.getDefaultToolkit();
                var scrsize = jkit.getScreenSize();
                width   = scrsize.width;
                height  = scrsize.height;
            }
        }
    }

    //---------------------------------------------------------
    //  --> Funcion encargada de estar actualizando el monitor
    //---------------------------------------------------------
    function actualizarMonitor()
    {
        $.post("monitorCitasEndoscopia.php",
        {
            consultaAjax:           '',
            accion:                 'actualizarMonitor',
            wemp_pmla:              $('#wemp_pmla').val(),
            monitorSala:            $('#monitorSala').val(),
            arrayUbicaciones:       $("#arrayUbicaciones").val(),
            numPagina:              $("#numPagina").val(),
            numRegistrosPorPagina:  $("#numRegistrosPorPagina").val()
        }, function(respuesta){

            // --> Mostrar en ventana emergente las alertas generadas
            if(respuesta.hayAlertas)
            {
                $("#ventanaAlertas").html(respuesta.htmlAlertas);
                $("#ventanaAlertas").dialog({
                    modal   : true,
					title	: "<div id='barraAtencion' style='width:70%; margin: 0 auto; text-align:center; font-size: 50px; color:rgb(255,0,0); background-color:rgba(255,255,255,0.5)'>Atenci&oacuten!</div>",
                    show    : { effect: "slide", duration: 400 },
                    hide    : { effect: "fold", duration: 400 },
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
                }, 400);

                // --> Sonido de alerta
                var sonidoAlerta = setInterval(function(){
                    $("#sonidoAlerta")[0].play();
                }, 2000);

                // --> Cerrar la ventana emergente automaticamente despues de 15 segundos
                setTimeout(function(){
                     clearInterval(mensajeAtencion);
                    $("#ventanaAlertas").html("");
                    $("#ventanaAlertas").dialog("close");
                }, 7000);

                // --> Apagar el sonido de alerta
                setTimeout(function(){
                     clearInterval(sonidoAlerta);
                }, 6000);
            }

            // --> Ajustar la vista a la resolucion de la pantalla
			/*
            obtenerResolucioPantalla();
            height1     = (height*0.99)-$("#encabezado").height();
			*/
			
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
//=============================================================================================
//  F I N  F U N C I O N E S  J A V A S C R I P T
//=============================================================================================
    </script>


<!--===========================================================================================================
    E S T I L O S
===========================================================================================================-->

<!--==========================================================================================================
    F I N   E S T I L O S
============================================================================================================-->

<!--==========================================================================================================
    I N I C I O   B O D Y
============================================================================================================-->
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

    $codSala = (!isset($codSala)) ? '': $codSala;
	$infoCc = getInfoCentroCosto($wbasedato);

    //Consultar el maestro de ubicaciones
    $arrayUbicaciones = array();

    $sqlUbicaciones   = " SELECT Ubicod, Ubides
                              FROM ".$wbasedato."_000027
                             WHERE Ubiest = 'on' ";
    $resUbicaciones = mysql_query($sqlUbicaciones, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUbicaciones):</b><br>".mysql_error());
    while($rowUbicaciones = mysql_fetch_array($resUbicaciones))
        $arrayUbicaciones[$rowUbicaciones['Ubicod']] = $rowUbicaciones['Ubides'];

    //Cantidad de registros por página
	//Se consulta el valor configurado
	$sqlConsultaCantRegistros = "SELECT Salnrp cantidadFilas
								 FROM ".$wbasedato."_000029
								 WHERE Salcod = '".$codSala."'
	";
	$resSala  = mysql_query($sqlConsultaCantRegistros, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConsultaCantRegistros):</b><br>".mysql_error());
	$rowSala = mysql_fetch_array($resSala);

    $numRegistrosPorPagina = isset($rowSala["cantidadFilas"]) ? $rowSala["cantidadFilas"] : 8;
	$numRegistrosPorPagina = 14;  // por ahora fijo en 
	
    // --> Pintar pantalla para asignar el turno
    echo "
    <input type='hidden' id='wemp_pmla'                 value='".$wemp_pmla."'>
    <input type='hidden' id='monitorSala'               value='".((isset($monitorSala)) ? $monitorSala : '*')."'>
    <input type='hidden' id='numRegistrosPorPagina'     value='".$numRegistrosPorPagina."'>
    <input type='hidden' id='arrayUbicaciones'          value='".json_encode($arrayUbicaciones)."'>
    <input type='hidden' id='numPagina'                 value='1'>
    <input type='hidden' id='totalPaginas'              value='1'>

    <div id='accordionPrincipal' align='center' style='margin: auto auto;'>
        <h1 id='encabezado' align='center' style='padding:0;'>";
	// ENCABEZADO AMTERIOR
	/*
	echo "<table width='100%' style='font-size: 4rem;color:#ffffff;font-family: verdana;font-weight:bold;'>
                <tr>
                    <td align='left'    width='15%'>
                        <img width='125' heigth='61' src='../../images/medical/root/logoClinicaGrande.png'>
                    </td>
                    <td align='center'  width='70%'>
						ATENCIÓN ".utf8_encode($infoCc["descripcion"])."
                    </td>
                    <td id='msjPagina' width='10%' style='font-weight:normal;font-size:2.2rem;color:#000000' align='right'>
                    </td>
                    <td width='5%'>
                        <img width='120' heigth='100' src='../../images/medical/root/Logo_MatrixAzulClaro.png'>
                    </td>
                </tr>
            </table>";
	*/
    echo "<table id='titTurnos'>";
	echo "<tr><td id='tdTitLogo'></td>";
	echo "<td id='tdTitDescrip'>&nbsp;ATENCI&Oacute;N<br>ENDOSCOPIA</td>";
	echo "<td id='tdTitFecha' align=right colspan=2>".$diasem."<br>".$wfechaG."</td></tr>";
	echo "</table>";			
			
	echo "</h1>
        <div id='divContenido' width='100%' align='center'>";
    $respListaTurnos = listarTurnos(1);
    echo $respListaTurnos['html'];
    echo "
        </div>
    </div><br>";
	echo "<center><table id='lineaInf'><tr><td colspan='100%'></td></tr></table>
	<div id='ventanaAlertas' style='display:none' align='center'></div>
	<audio id='sonidoAlerta'><source type='audio/mp3' src='../../images/medical/root/alertaMensaje.mp3' ></audio>
	";

    ?>
    </body>
<!--===================================================================
    F I N   B O D Y
=======================================================================-->
    </html>
    <?php
//=====================================================================
//  F I N  E J E C U C I O N   N O R M A L
//=====================================================================
}

//}//Fin de session
?>
