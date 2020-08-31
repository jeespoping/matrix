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

	$conex 		= obtenerConexionBD("matrix");
	$wbasedato 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'activosFijos');
	$wfecha		= date("Y-m-d");
    $whora 		= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-------------------------------------------------------------------
	//	-->	Pinta la lista de los activos
	//-------------------------------------------------------------------
	function listarActivos()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		$arrayActivos = array();

		// --> Consultar activos
		$sqlLista = "SELECT Actreg, Actnom, Actpla, Actest
					   FROM ".$wbasedato."_000001
					  WHERE Actact = 'on'
		";
		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLista):</b><br>".mysql_error());
		while($rowLista = mysql_fetch_array($resLista))
		{
			// --> Codificar tildes y caracteres especiales
			foreach($rowLista as $indice => &$valor)
				$valor = utf8_encode($valor);

			// --> Armar array de lista de activos
			$arrayActivos[$rowLista['Actreg']] = $rowLista;
		}

		$colorFila = "fila1";

		echo "
		<div id='accordionListaActivos' align='center'>
			<h1 style='font-size: 11pt;' align='left'>&nbsp;&nbsp;&nbsp;&nbsp;Lista de activos</h1>
			<div style='font-family: verdana;font-weight: normal;' align='left'>
				<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
					Buscar:&nbsp;</b>
					<input id='buscarActivo' type='text' tipo='obligatorio' placeholder='Digite el nombre del activo' style='border-radius: 4px;border:1px solid #AFAFAF;width:210px'>
				</span>
				<br><br>
				<div style='height:310px;overflow:auto;background:none repeat scroll 0 0;'>
					<table width='100%' id='listaDeActivos'>";
				foreach($arrayActivos as $registro => $infoAct)
				{
					$arrInfoAct	= json_encode($infoAct);
					$colorFila 	= (($colorFila == 'fila1') ? 'fila2' : 'fila1' );
			echo "		<tr style='font-size: 9pt;' class='find'>
							<td width='30%'title='Ver relaciones' onClick='verRelacionesActivo(\"".$registro."\")' class='tooltip' style='border-bottom:1px solid #BFBFBF;cursor:pointer'>- ".$registro."</td>
							<td title='Ver relaciones' onClick='verRelacionesActivo(\"".$registro."\")' class='tooltip' style='border-bottom:1px solid #BFBFBF;cursor:pointer'>".$infoAct['Actnom']."</td>
							<td><img title='+ Agregar' infoActivo='".$arrInfoAct." 'onClick='agregarActivo(this)' class='tooltip' style='cursor:pointer' width='15' height='15' src='../../images/medical/root/grabar.png'></td>
							<td></td>
						</tr>";
				}
			echo "	</table>
				</div>
			</div>
		</div>
		";
	}
	//------------------------------------------------------------------------
	//	-->	Pinta los activos que estan relacionados a un activo seleccionado
	//------------------------------------------------------------------------
	function relacionDelActivo($registro='')
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		$arrayActRel = array();

		if($registro != '')
		{
			// --> Consultar informacion basica del activo
			$sqlAct = "SELECT ".$wbasedato."_000001.*, Grunom
						 FROM ".$wbasedato."_000001, ".$wbasedato."_000008
						WHERE Actreg = '".$registro."'
			";
			$resAct = mysql_query($sqlAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAct):</b><br>".mysql_error());
			if($rowAct = mysql_fetch_array($resAct))
			{
				$infoActivo = $rowAct;
				// --> Codificar tildes y caracteres especiales
				foreach($infoActivo as $indice => &$valor)
					$valor = utf8_encode($valor);
			}

			// --> Consultar activos relacionados
			$sqlActRel = "SELECT Relare, Actnom, Actpla, Actest
							FROM ".$wbasedato."_000031, ".$wbasedato."_000001
						   WHERE Relact = '".$registro."'
							 AND Relare = Actreg
			";
			$resActRel = mysql_query($sqlActRel, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActRel):</b><br>".mysql_error());
			while($rowActRel = mysql_fetch_array($resActRel))
			{
				// --> Codificar tildes y caracteres especiales
				foreach($rowActRel as $indice => &$valor)
					$valor = utf8_encode($valor);

				$arrayActRel[$rowActRel['Relare']] = $rowActRel;
			}
		}

		echo "
		<div id='accordionRelaciones' align='left' style='font-family: verdana;font-weight: normal;font-size: 10pt;'>
			<h1 style='font-size: 11pt;'>&nbsp;&nbsp;&nbsp;Activos relacionados</h1>
			<div>";

		// --> Pintar informacion basica del activo
		echo "	<fieldset align='center' style='padding:15px;'>
					<legend class='fieldset'>Información del activo</legend>
					<div>
						<table width='100%'>
							<tr>
								<td class='fila1'>Nombre:</td>
								<td class='fila2'>".((isset($infoActivo)) ? $infoActivo['Actnom'] : "")."</td>
								<td class='fila1'>N° Registro:</td>
								<td class='fila2' id='activoSeleccionado'>".((isset($infoActivo)) ? $infoActivo['Actreg'] : "")."</td>
								<td class='fila1'>Placa:</td>
								<td class='fila2'>".((isset($infoActivo)) ? $infoActivo['Actpla']: "")."</td>
								<td class='fila1'>Estado:</td>
								<td class='fila2'>".((isset($infoActivo)) ? $infoActivo['Actest']: "")."</td>
								<td class='fila1'>Grupo:</td>
								<td class='fila2'>".((isset($infoActivo)) ? $infoActivo['Grunom']: "")."</td>
							</tr>
						</table>
					</div>
				</fieldset>";

		// --> Pintar activos relacionados
		echo "	<br>
				<fieldset align='center' style='padding:15px;'>
					<legend class='fieldset'>Lista de activos relacionados</legend>
					<div style='height:190px;overflow:auto;background:none repeat scroll 0 0;'>
						<table width='100%' id='listaDeActivosRelacionados'>
							<tr align='center'><td class='encabezadoTabla'>N° Registro</td><td class='encabezadoTabla'>Nombre</td><td class='encabezadoTabla'>Placa</td><td class='encabezadoTabla'>Estado</td><td></td></tr>";
					if(count($arrayActRel)== 0)
		echo "				<tr id='trNoHayActivos'><td colspan='4' align='center'><b>No hay activos relacionados...</b></td></tr>";
					foreach($arrayActRel as $regAct => $infoAct)
		echo "				<tr registro='".$infoAct['Relare']."'>
								<td class='fila2'>".$infoAct['Relare']."</td>
								<td class='fila2'>".$infoAct['Actnom']."</td>
								<td class='fila2'>".$infoAct['Actpla']."</td>
								<td class='fila2'>".$infoAct['Actest']."</td>
								<td ><img src='../../images/medical/eliminar1.png' title='Quitar relación' onclick='quitarRelacion(\"".$infoAct['Relare']."\")' style='cursor:pointer;'>
							</tr>";
		echo "			</table>
					</div>
				</fieldset>
				<br>
			</div>
		</div>
		";
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
		case 'verRelacionesActivo':
		{
			$html = relacionDelActivo($registro);
			echo $html;
			break;
		}
		case 'agregarActivo':
		{
			$sqlAgregar = "
			INSERT INTO ".$wbasedato."_000031
					    (Medico, 			Fecha_data, 	Hora_data, 		Relact, 		Relare,					Seguridad, 		id)
			VALUES		('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$activo."',	'".$activoAgregar."',	'C-".$wuse."',	'')
			";
			mysql_query($sqlAgregar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAgregar):</b><br>".mysql_error());
			break;
		}
		case 'quitarActivo':
		{
			$sqlQuitar = "DELETE FROM ".$wbasedato."_000031
						   WHERE Relact = '".$activo."'
						     AND Relare = '".$activoQuitar."' ";
			mysql_query($sqlQuitar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlQuitar):</b><br>".mysql_error());
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

	var url_add_params = addUrlCamposCompartidosTalento();

	$(function(){
		// --> Activar Acordeones

		$("#accordionListaActivos").accordion({
			heightStyle: "fill"
		});

		$("#accordionRelaciones").accordion({
			heightStyle: "fill"
		});

		// --> Activar el buscador de texto, para los campos con formula
		$('#buscarActivo').quicksearch('#listaDeActivos .find');

		// --> Activar el buscador de texto, para las variables
		$('#buscarVariable').quicksearch('#tablaListaVariables .find');

		// --> Tooltip
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

	});
	//----------------------------------------------------------------------------------
	//	--> Ver relaciones del activo
	//----------------------------------------------------------------------------------
	function verRelacionesActivo(registro)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			accion:         'verRelacionesActivo',
			registro:		registro
		}, function(html){
			$("#relacionDelActivo").html(html);

			$("#accordionRelaciones").accordion({
				heightStyle: "fill"
			});
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Agregar un activo a la relación
	//----------------------------------------------------------------------------------
	function agregarActivo(elemento)
	{

		elemento 		= $(elemento);
		var infoActivo 	= new Object();
		infoActivo		= JSON.parse(elemento.attr("infoActivo"));
		var yaExiste	= false;
		nuevoActivo 	= "<tr registro='"+infoActivo.Actreg+"'>"
							+"<td class='fila2'>"+infoActivo.Actreg+"</td>"
							+"<td class='fila2'>"+infoActivo.Actnom+"</td>"
							+"<td class='fila2'>"+infoActivo.Actpla+"</td>"
							+"<td class='fila2'>"+infoActivo.Actest+"</td>"
							+"<td><img src='../../images/medical/eliminar1.png' title='Quitar relación' onclick='quitarRelacion(\""+infoActivo.Actreg+"\");' style='cursor:pointer;'></td>"
						+"</tr>";

		if($("#activoSeleccionado").text() == '')
		{
			alert("Seleccione primero un activo");
			return;
		}

		if($("#activoSeleccionado").text() == infoActivo.Actreg)
			return;

		$("#listaDeActivosRelacionados tr").each(function(){
			if($(this).attr("registro") == infoActivo.Actreg)
			{
				alert("Este activo ya esta relacionado", "div_mensajes");
				yaExiste = true;
			}
		});

		$("#trNoHayActivos").remove();

		if (!yaExiste)
		{
			// --> Agregar el activo en la interface
			$("#listaDeActivosRelacionados").append(nuevoActivo);

			// --> Agregar el activo en la tabla de la BD
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   '',
				accion:         'agregarActivo',
				activo:			$("#activoSeleccionado").text(),
				activoAgregar:	infoActivo.Actreg
			}, function(html){
			});

			// --> Efecto de transferencia
			var options = {};
			options 	= { to: "tr[registro="+infoActivo.Actreg+"]", className: "ui-effects-transfer" };
			elemento.effect('transfer', options, 600, "");
			$("tr[registro="+infoActivo.Actreg+"]").hide();
			setTimeout(function() {
				$("tr[registro="+infoActivo.Actreg+"]").show();
			},590);
		}
	}
	//----------------------------------------------------------------------------------
	//	--> Quitar una relacion del activo
	//----------------------------------------------------------------------------------
	function quitarRelacion(activoRel)
	{
		if(confirm("¿Está seguro que desea quitar este activo?"))
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   '',
				accion:         'quitarActivo',
				activo:			$("#activoSeleccionado").text(),
				activoQuitar:	activoRel
			}, function(html){
				$("tr[registro="+activoRel+"]").hide(400, function(){
					$(this).remove();
				});
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
	echo '
	<table width="100%" cellspacing="10">
		<tr>
			<td width="35%" valign="top" id="listaActivos">';
				listarActivos();
	echo '	</td>
			<td width="65%" valign="top" id="relacionDelActivo">';
				relacionDelActivo();
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
