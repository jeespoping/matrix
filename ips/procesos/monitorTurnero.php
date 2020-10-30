<?php
include_once("conex.php");
//=========================================================================================================================================\\
//       	MONITOR DEL TURNERO DEL LOBBY
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:	2018-01-16
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//---------------------------------------------------------------------------------------------------------2020-01-13 Arleyda Insignares C.
//         Se crea el campo 'codaso' en cliame_000305 para indicar que un tema está asociado a otro y de 
//         esta forma el monitor podrá llamar un paciente que en el turno indique un servicio pero esté 
//         siendo llamado de un servicio diferente.
//         Se crea el campo 'codlog' en cliame_000305 para indicar el nombre del logo que debe mostrar en 
//         la vista al usuario.
//
			$wactualiz='2020-01-13';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------


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
	

	$conex 					= obtenerConexionBD("matrix");
	$wbasedato	 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$wfecha					= date("Y-m-d");
    $whora 					= date("H:i:s");
	$numRegistrosPorPagina 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'numRegistrosPorPaginaParaTurneroLobby');


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
		global $tema;
		global $temaso;

		$respAlertas = array("hayAlertas" => false, "htmlAlertas" => "");
		$primeraVez	 = true;
		$arrayId 	 = array();
		$conadi      = '';

		$htmlAlertas = "
		<table width='100%' style='color:#000000;font-family: verdana;font-weight: normal;font-size: 4rem;'>
			<tr style='background-color: #2a5db0;color:#ffffff;font-weight:bold;'>
				<td align='center'>Turno</td>
				<td align='center'>Por favor pasar a:</td>
				<td align='center'></td> 
			</tr>
		";


		if ($temaso != '')
		{
	        $conadi = "UNION SELECT A.Fecha_data, A.Hora_data, Turtur, Puenom, Conpri
						  FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000301 ON(Turven = Puecod)
						       INNER JOIN ".$wbasedato."_000299 ON(Turupr = Concod)
						 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
						   AND Turtem = '".$temaso."'
						   AND Turest = 'on'
						   AND Puecon = 'on'
						   AND Puetem = '".$tema."'
						   AND Turllv = 'on'";
		}

	
		$sqlAlertas = "
		SELECT A.Fecha_data, A.Hora_data, Turtur, Puenom, Conpri
		  FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000301 ON(Turtem = Puetem AND Turven = Puecod)
		       INNER JOIN ".$wbasedato."_000299 ON(Turupr = Concod)
		 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
		   AND Turtem = '".$tema."'
		   AND Turest = 'on'
		   AND Turllv = 'on'
		   ".$conadi."
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

			$colorFila 		= (($colorFila == '#DCE5F2') ? '#F2F5F7;' : '#DCE5F2');
			$turno 			= substr($rowAlertas['Turtur'], 7);
			$turno 			= substr($turno, 0, 2)." ".substr($turno, 2, 5);
			
			$htmlAlertas.= "
				<tr style='background-color:".$colorFila."'>
					<td align='center'>".$turno."</td>
					<td align='center'>".utf8_encode($rowAlertas['Puenom'])."</td>
					".(($rowAlertas['Conpri'] == 'on') ? "<td align='center' style='font-size:3rem;color:#D83933'>Aten. Prioritaria</td>" : "")."
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
		global $numRegistrosPorPagina;
		global $tema;
		global $temaso;

		//<div style='background-color:#FFFFFF;color:#E2007A;font-family: verdana;font-weight: normal;font-size: 2.2rem;'>
			// La atención será de acuerdo a la clasificación por prioridad.
		// </div>
		$html 		= "
		
		<table width='100%' style='background-color:#DBDBDB;cellspacing:0.1rem;color:#000000;font-family: verdana;font-weight: normal;font-size: 3.5rem;'>
			<tr align='center' style='background-color:#D1ECF9;font-size: 4rem;'>
				<td width='20%'>Turno</td>
				<td width='40%'>Ubicaci&oacute;n</td>
				<td width='40%'>Aten. Prioritaria</td>
			</tr>
		";

		// --> Consultar turnos de maximo 24 horas atras.
		$sqlTurnos = "
		SELECT A.Fecha_data, A.Hora_data, Turtur, Conpri, Puenom
		  FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000299 ON(Turupr = Concod)
		       INNER JOIN ".$wbasedato."_000301 ON(Turtem = Puetem AND Turven = Puecod)
		 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
		   AND Turtem = '".$tema."'
		   AND Turest = 'on'
		   AND Turpat = 'on'
		 ORDER BY REPLACE(Turtur, '-', '')*1 ASC
		";
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
		
		

		$colorFila		= '#F2F5F7;';
		$arrFilasTur 	= array();
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			// --> Solo mostrar turnos de maximo 12 horas atras
			if(strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']) < strtotime('-12 hours'))
				continue;
			
			$colorFila 		= (($colorFila == '#FFFFFF') ? '#F2F5F7;' : '#FFFFFF');
			$turno 			= substr($rowTurnos['Turtur'], 7);
			$turno 			= substr($turno, 0, 2)." ".substr($turno, 2, 5);
			
			$arrFilasTur[] = "
				<tr style='background-color:".$colorFila."'>
					<td align='center' style=''>
						".$turno."
					</td>
					<td align='center'>
						&nbsp;".utf8_encode($rowTurnos["Puenom"])."
					</td>
					<td align='center' style=''>
						&nbsp;".utf8_encode((($rowTurnos["Conpri"] == 'on') ? "SI" : ""))."
					</td>
				</tr>
			";
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

		$html.= "</table>";

		$respuesta['html'] 			= $html;
		$respuesta['totalPaginas'] 	= $totalPaginas;
		$respuesta['numPagina'] 	= $numPagina;

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
	  <title>Monitor Turnero</title>
	</head>
		<meta charset="UTF-8">
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	var height = 0;
	var width  = 0;
	var intervalSet;

	$(function(){

		$("#accordionPrincipal").accordion({
			collapsible: false,
			heightStyle: "content"
		});
		$( "#accordionPrincipal" ).accordion( "option", "icons", {} );

		$("#divContenido").css({"padding": "0.1em"});

		// --> Ajustar la vista a la resolucion de la pantalla
		obtenerResolucioPantalla();
		width1 		= width*0.99;
		height1 	= height*0.99;

		if(width1 > 0 && height1 > 0)
			$("#accordionPrincipal").css({"width":width1});
		else
			$("#accordionPrincipal").css({"width": "99 %"});

		// --> Llamado automatico, para que el monitor este actualizando
		setInterval(function(){
			actualizarMonitor();
		}, 10000);

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
		$.post("monitorTurnero.php",
		{
			consultaAjax:   		'',
			accion:         		'actualizarMonitor',
			wemp_pmla:        		$('#wemp_pmla').val(),
			monitorSala:       		$('#monitorSala').val(),
			numPagina:				$("#numPagina").val(),
			numRegistrosPorPagina:	$("#numRegistrosPorPagina").val(),
			tema:					$("#tema").val(),
			temaso:					$("#temaso").val(),
		}, function(respuesta){

			// --> Mostrar en ventana emergente las alertas generadas
			if(respuesta.hayAlertas)
			{
				$("#ventanaAlertas").html(respuesta.htmlAlertas);
				$("#ventanaAlertas").dialog({
					modal	: true,
					title	: "<div align='center' id='barraAtencion' style='font-size: 4rem;color:#D83933'>&iexcl;Atenci&oacute;n!</div>",
					width	: width*0.9,
					height	: height*0.7,
					show	: { effect: "slide", duration: 400 },
					hide	: { effect: "fold", duration: 400 }
				});
				// --> Blink al mensaje de "¡Atencion!"
				var mensajeAtencion = setInterval(function(){
					$("#barraAtencion").css('visibility' , $("#barraAtencion").css('visibility') === 'hidden' ? '' : 'hidden')
				}, 400);

				// --> Sonido de alerta
				var sonidoAlerta = setInterval(function(){
					$("#sonidoAlerta")[0].play();
				}, 2000);

				// --> Cerrar la ventana emergente automaticamente despues de 7 segundos
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
			obtenerResolucioPantalla();
			height1 	= (height*0.99)-$("#encabezado").height();

			// --> Actualizar la lista de turnos, con el efecto de paginacion
			// $("#divContenido").hide('fade', 800, function(){
				// $(this).html(respuesta.htmlListaTurnos).height(height1).effect( "slide", {}, 1000, function(){
					// $(this).show();
				// });
			// });

			// --> Actualizar la lista de turnos, con el efecto de paginacion
			$("#divContenido").hide('fade', 800, function(){
				$(this).html(respuesta.htmlListaTurnos).height(height1).show( "blind", {}, 1200)
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
	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY style="overflow:hidden">
	<?php
	// --> Consultar nombre del tema
	$nomTema = "";
	$nomlogo = "";

	if($tema != '')
	{
		$sqlTema = "
		SELECT Codnom,Codlog,Codaso
		  FROM ".$wbasedato."_000305
		 WHERE Codtem = '".$tema."'
		   AND Codest = 'on'
		";
		$resTema = mysql_query($sqlTema, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTema):</b><br>".mysql_error());
		if($rowTema = mysql_fetch_array($resTema))
		{
			$nomTema = $rowTema['Codnom'];
			$nomlogo = $rowTema['Codlog'];
			$temaso  = $rowTema['Codaso'];
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
		echo "<input type='hidden' id='temaso' value='".$temaso."'>";
	}
	
	// --> Pintar pantalla para asignar el turno
	// Desactivar logo - <img width='75' heigth='40' src='../../images/medical/root/Logo_MatrixAzulClaro.png'>
	echo "
	<input type='hidden' id='wemp_pmla' 				value='".$wemp_pmla."'>
	<input type='hidden' id='monitorSala' 				value='".((isset($monitorSala)) ? $monitorSala : '*')."'>
	<input type='hidden' id='numRegistrosPorPagina' 	value='".$numRegistrosPorPagina."'>
	<input type='hidden' id='numPagina' 				value='1'>
	<input type='hidden' id='totalPaginas' 				value='1'>

	<div id='accordionPrincipal' align='center' style='margin: auto auto;'>
		<h1 id='encabezado' align='center' style='background:#75C3EB'>
			<table width='100%' style='font-size: 4rem;color:#ffffff;font-family: verdana;font-weight:bold;'>
				<tr>
					<td align='left' 	width='15%'>
						<img width='200' heigth='120' src='../../images/medical/root/".$nomlogo."'>
					</td>
					<td align='center' 	width='70%'>
						".$nomTema."
					</td>
					<td id='msjPagina' width='10%' style='font-weight:normal;font-size:2.2rem;color:#000000' align='right'>
					</td>
					<td width='5%'>
						
					</td>
				</tr>
			</table>
		</h1>
		<div id='divContenido'align='center'>";
			$respListaTurnos = listarTurnos(1);
			echo $respListaTurnos['html'];
	echo "
		</div>
	</div>
	<div id='ventanaAlertas' style='display:none' align='center'></div>
	<audio id='sonidoAlerta'><source type='audio/mp3' src='../../images/medical/root/alertaMensaje.mp3' ></audio>
	";
		
		
		//Se cierra conexión de la base de datos :)
		mysql_close($conex);
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
