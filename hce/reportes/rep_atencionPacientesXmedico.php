<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
{
	die('error');
}




$hoy = date("Y-m-d");
$hora = date("H:i:s");
$caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");

function encabezadoInforme( $wmedico, $nomMed, $wfechaini, $wfechafin )
{
	global $hoy;
	( !isset($wmedico) or ( trim( $wmedico ) == "" ) or ( trim( $wmedico ) == "%" ) ) ? $wmedico = "TODOS" : $wmedico = $wmedico;
	$encabezado .= "<table width='100%'>";
		$encabezado .= "<tr>"; 	
			$encabezado .= "<td align='center' width='10%' class='encabezadotabla'>M&Eacute;DICO:</td>";
			$encabezado .= "<td align='center' width='60%'class='fila1'>( {$wmedico} ){$nomMed}</td>";
			$encabezado .= "<td align='center' width='20%' class='encabezadotabla'>FECHA GENERACI&Oacute;N DEL REPORTE</td>";
			$encabezado .= "<td align='center' width='10%' class='fila1'>{$hoy}</td>";
		$encabezado .= "</tr>";
		$encabezado .= "<tr>"; 	
			$encabezado .= "<td align='center' class='encabezadotabla'>CONSULTAS ENTRE EL RANGO:</td>";
			$encabezado .= "<td align='center' class='fila1' colspan='3'>{$wfechaini} &nbsp;&nbsp;&nbsp; <b>A</b> &nbsp;&nbsp;&nbsp; {$wfechafin}</td>";
		$encabezado .= "</tr>";
	$encabezado .= "</table>"; 
	return( $encabezado );
}

function detalleInforme( $datos )
{
	$table = "";
	$table .= "<div align='center' class='fila1' style='width:100%;'>";
	$table .= "<br>";
	$table .= "<table style='border: 1px solid; border-color:#2A5DB0;' width='90%'>";
	$table .= "<tr class='encabezadotabla'>";
		$table .= "<td align='center' style='height:30;'>HISTORIA</td>";
		$table .= "<td align='center'>INGRESO</td>";
		$table .= "<td align='center'>FECHA, HORA<br>INICIO CONSULTA</td>";
		$table .= "<td align='center'>FECHA, HORA<br>FINALIZACION CONSULTA</td>";
		$table .= "<td align='center'>TIEMPO EN CONSULTA</td>";
	$table .= "</tr>";
	$i = 0;
		foreach( $datos as $keyHistoriaIngreso=>$datos2 )
		{
			//$tiempoEnProceso 	 = diferenciaTiempo( $datos['fechaCreacion'], $datos['horaCreacion'], $hoy, $hora); //tiempo en proceso.
			//$tiempoEnEtapaActual = diferenciaTiempo( $datos['fechaRecibo'], $datos['horaRecibo'], $hoy, $hora); //tiempo en proceso.
			( is_int( $i/2 ) ) ? $wclass="fila1" : $wclass="fila2";
			$i++;
			$auxiliar = explode( "-",$keyHistoriaIngreso );
			$historia = $auxiliar[0];
			$ingreso  = $auxiliar[1];
			
			$table .= "<tr class='{$wclass}' style='border: 1px solid; border-color:#2A5DB0;'>";
			$table .= "<td align='center' style='height:30;'>{$historia}</td>";
			$table .= "<td align='center'>{$ingreso}</td>";
			$table .= "<td align='center'>{$datos2['fechaInicio']}, {$datos2['horaInicio']}</td>";
			$table .= "<td  align='center'>{$datos2['fechaFinal']}, {$datos2['horaFinal']}</td>";
			$arrayAuxiliar = array( 'consultas'=>1, 'tiempoTotalAtencion'=>$datos2['tiempoAtencion'] );
			$table .= "<td  align='center'>".tiempoPromedio( $arrayAuxiliar )."</td>";
			$table .= "</tr>";
		}
	$table .= "</table>";
	$table .= "</div>";
	return( $table );
}

function tiempoPromedio( $datos )
{
	$numListas = $datos['consultas'];
	$segundosT = $datos['tiempoTotalAtencion']/$numListas;
	$horas     = 0;
	$minutos   = 0;
	$segundos  = 0;
	
		
	$promedioDias = floor( @$segundosT/86400 );
	$mod_hora	  = $segundosT%86400;
	
    $promedioHora = floor($mod_hora/3600);
 
	$mod_minuto   = $mod_hora%3600;
	
	$promedioMins = floor($mod_minuto/60);

	$mod_segundo  = $mod_minuto%60;
	$promediosegs = floor( @$mod_segundo );
	
	if( $promedioMins > 60 )
	{
		$promedioHora += floor( $promedioMins/60 );
		$promedioMins  = ($promedioMins%60);
	}
	
	if( $promediosegs > 60 )
	{
		$promedioMins += floor( $promediosegs/60 );
		$promediosegs  = ($promediosegs%60);
	}
	
	( $promedioDias > 0 ) ? $respuesta = "".$promedioDias." dias" : $respuesta = ""; 
	
	return( $respuesta." ".$promedioHora.":".$promedioMins.":".$promediosegs );
	
}

function buscarEnUsuarios( $codigo )
{
	global $conex;
	$query = "SELECT codigo codigo, descripcion nombre
				FROM usuarios
			   WHERE codigo = '{$codigo}'
			     AND activo = 'A'";
	$rs	   = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$nombre = $row['nombre'];
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );
		return( $nombre );
	}
} 
/**-----------------------------------------------------------------------------------------------CONSULTAS AJAX--------------------------------------------------------------------------------------------------**/

if( $consultaAjax == "generarInforme" )
{
	/** RECEPCIÓN DE VARIABLES **/
	$wfechaIni = $_POST['wfechaIni'];
	$wfechaFin = $_POST['wfechaFin'];
	$wmedico   = $_POST['wmedico'];
	
	$wmedico   = explode( ",", $wmedico );
	$nomMed	   = $wmedico[1];
	$wmedico   = $wmedico[0];
	$error	   = 1;
	$medicos   = array();
	$datos	   = array();
	$sumatorias= array();
	$arrOrdenar= array(); //arreglo cuya función es solo ordenar
	
	
	( trim( $wmedico )  == "" OR trim( $wmedico ) == "%" ) ? $condicionMedico  = " Mtrmed <> '' " : $condicionMedico  = " Mtrmed = '{$wmedico}' ";
	( trim( $wmedico )  == "" OR trim( $wmedico ) == "%" ) ? $condicionMedico2 = " Meduma <> '' " : $condicionMedico2 = " Meduma = '{$wmedico}' ";
	
	$query = "SELECT Meduma codigo, medno1 nombre1, medno2 nombre2, medap1 apellido1, medap2 apellido2
				FROM {$wbasedato}_000048
			   WHERE {$condicionMedico2}
			     AND Medurg = 'on'
			     AND Medest = 'on'";
	$rs	   = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$nombre = $row['nombre1']." ".$row['nombre2']." ".$row['apellido1']." ".$row['apellido2'];
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );
		$medicos[$row['codigo']]['nombre'] = $nombre;
	}	
	
	$query = "SELECT  Mtrmed codigo, Mtrhis historia, Mtring ingreso, Mtrfco fechaInicio, Mtrhco horaInicio, Mtrftc fechaFinal, Mtrhtc horaFinal, TIMESTAMPDIFF( SECOND, CONCAT( Mtrfco,' ', Mtrhco ), CONCAT( Mtrftc, ' ', Mtrhtc ) ) tiempoAtencion
				FROM {$whce}_000022 a
					 INNER JOIN 
					 {$wbasedato}_000011 b on ( Mtrcci = Ccocod AND Ccourg = 'on')
			   WHERE {$condicionMedico}
				 AND Mtrfco BETWEEN '{$wfechaIni}' and '{$wfechaFin}'
				 AND Mtrftc <> '0000-00-00'
				 AND Mtrhtc <> '00:00:00'
				 AND Mtrcur = 'off'
			   ORDER BY tiempoAtencion";

	$rs   = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) ){
		
		$datos[$row['codigo']][$row['historia']."-".$row['ingreso']]['fechaInicio']    = $row['fechaInicio'];
		$datos[$row['codigo']][$row['historia']."-".$row['ingreso']]['horaInicio']     = $row['horaInicio'];
		$datos[$row['codigo']][$row['historia']."-".$row['ingreso']]['fechaFinal']     = $row['fechaFinal'];
		$datos[$row['codigo']][$row['historia']."-".$row['ingreso']]['horaFinal']      = $row['horaFinal'];
		$datos[$row['codigo']][$row['historia']."-".$row['ingreso']]['tiempoAtencion'] = $row['tiempoAtencion'];
		$sumatorias[$row['codigo']]['tiempoTotalAtencion'] += $row['tiempoAtencion'];
		$arrOrdenar[$row['codigo']] += $row['tiempoAtencion'] * 1;
		
		( isset( $sumatorias[$row['codigo']]['consultas'] ) ) ? $sumatorias[$row['codigo']]['consultas']++ : $sumatorias[$row['codigo']]['consultas'] = 1;
		
	}
	
	foreach( $arrOrdenar as $keyCodigo=>$auxiliar )
	{
		//$arrOrdenar[$keyCodigo] = $arrOrdenar[$keyCodigo]/ $sumatorias[$keyCodigo]['consultas'];
		$arrOrdenar[$keyCodigo] = $sumatorias[$keyCodigo]['consultas'];
	}
	
	arsort( $arrOrdenar );
	
	$html .= "<br><span class='subtituloPagina2'>REPORTE DE CONSULTAS POR M&Eacute;DICO URGENCIAS</span><br><br>";
	$html .= "<br><div align='left'>".encabezadoInforme( $wmedico, $nomMed, $wfechaIni, $wfechaFin )."</div><br>";
	$html .= "<table width='100%' style='border: 1px solid; border-color:#2A5DB0;'>";
	$html .= "<tr class='encabezadotabla'>";
		$html .= "<td align='center'> CODIGO <br> M&Eacute;DICO </td>";
		$html .= "<td align='center'> NOMBRE </td>";
		$html .= "<td align='center'> PACIENTES ATENDIDOS </td>";
		$html .= "<td align='center'> PROMEDIO TIEMPO DE CONSULTA <br>( HH:mm:ss )</td>";
	$html .= "</tr>";
	$i = 0;
	//foreach( $datos as $keyCodigo=>$info ){
	foreach( $arrOrdenar as $keyCodigo=>$aux ){
		$info = $datos[$keyCodigo];
		( is_int( $i/2 ) ) ? $wclass="fila1" : $wclass="fila2";
		$i++;
		$error = 0;
		( trim( $medicos[$keyCodigo]['nombre'] ) == "" ) ? $nomMedico = buscarEnUsuarios( $keyCodigo ) : $nomMedico = $medicos[$keyCodigo]['nombre'];
		$html .= "<tr class='{$wclass}' style='cursor:pointer;' classOriginal='{$wclass}' onclick='mostrarDetalle( \"{$keyCodigo}\" , this );'>";
			$html .= "<td align='center' height='30px'> {$keyCodigo} </td>";
			$html .= "<td> {$nomMedico} </td>";
			$html .= "<td align='center'> {$sumatorias[$keyCodigo]['consultas']} </td>";
			$html .= "<td align='center'> ".tiempoPromedio( $sumatorias[$keyCodigo] )."</td>";
		$html .= "</tr>";
		$html .= "<tr class='fila1' name='fila_detalle' id='detalle_{$keyCodigo}' mostrando='n' style='display:none;'>";
			$html .= "<td align='center' colspan='4'>".detalleInforme( $info )."<br><br></td>"; ///
		$html .= "</tr>";
		
	}
	$html .= "</table><br><br>";
	
	$data = array( "html"=>$html, "error"=>$error );
	echo json_encode( $data );
	return;
}
/**----------------------------------------------------------------------------------------------FIN CONSULTAS AJAX-----------------------------------------------------------------------------------------------**/
?>
<html>
<header>
	<title> REPORTE CONSULTAS POR MÉDICO URGENCIAS </title>
</header>
<style>
	
	.botona{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			border:0px;
			width:180px;
			height:30px;
			margin-left: 1%;
			cursor: pointer;
		 }
	
	/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
		.ui-datepicker {font-size:12px;}
		/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
		.ui-datepicker-cover {
			display: none; /*sorry for IE5*/
			display/**/: block; /*sorry for IE5*/
			position: absolute; /*must have*/
			z-index: -1; /*must have*/
			filter: mask(); /*must have*/
			top: -4px; /*must have*/
			left: -4px; /*must have*/
			width: 200px; /*must have*/
			height: 200px; /*must have*/
		}
</style>
<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
<script>
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
</script>
<script>

	var input_medico;
	var wbasedato;
	var whce;
	var wemp_pmla;
	var fechaInicial;
	var fechaFinal;
	
	$(document).ready(function(){
	
		input_medico = $( "#input_medico" );
		wemp_pmla	 = $("#wemp_pmla").val();
		wbasedato	 = $("#wbasedato").val();
		whce		 = $("#whce").val();
		fechaInicial = $("#fecha_ini");
		fechaFinal	 = $("#fecha_fin");
		
		medicos_nombres_array = new Array();
		var datosMedicos = eval( $("#array_medicos").val() );
		for( i in datosMedicos ){
			medicos_nombres_array.push( datosMedicos[i] );
		}
		input_medico.autocomplete({
				source: medicos_nombres_array, minLength : 1
				//select: function( event, ui ) { buscarRolesDeProceso() }
		});
		$('.ui-corner-all').css('fontSize', '11px');
		
		
		$( "#fecha_ini" ).datepicker( {
						 showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
				buttonImageOnly: true,
						maxDate: "+0D"
			} );
		
		$( "#fecha_fin" ).datepicker( {
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
				buttonImageOnly: true,
						maxDate: "+0D"
			} );
	});
	
	function generarInforme()
	{
		$("#div_respuestas").html( "" );
		$.ajax({
				 url:	"rep_atencionPacientesXmedico.php",
				type:	"POST",
				data:	{
							consultaAjax: "generarInforme",
							   wemp_pmla: wemp_pmla,
							   wbasedato: wbasedato,
								    whce: whce,
							     wmedico: input_medico.val(),
							   wfechaIni: fechaInicial.val(),
							   wfechaFin: fechaFinal.val()
						},
			  success: function( data )
						{
							  if( data.error*1 == 0 )
							  {
								 $("#div_respuestas").html( data.html );
								 $('#div_respuestas').hide();
								 $('#div_menu_principal').hide();
								 $("#div_respuestas").show();
								 $("div[name='div_retornar']").show();
														 
							  }else
								{
									$.blockUI({ 
										message: $("#div_sinDatos"),
										css: { left: '25%',
												top: '20%',
											  width: '35%',
											 height: '30%'
											 }
								  });
								}
						},
			 dataType:	"json"
		});
	}

	function retornar()
	{
		 $('#div_respuestas').hide();
		 $("div[name='div_retornar']").hide();
		 $('#div_menu_principal').show();
		 input_medico.val("%, TODOS");
	}

	function mostrarDetalle( codigo, padre )
	{
		tr    = jQuery($( "#detalle_"+codigo ));
		padre2= jQuery( $(padre) );
		if( tr.attr( "mostrando" ) == "s" )
		{
			claseOriginal = $(padre).attr( "classOriginal" );
			$(padre).removeClass( "botona" ).addClass( claseOriginal );
			tr.attr( "mostrando", "n" );
			tr.hide();
			return;
		}else
			{
				claseOriginal = $(padre2).attr( "classOriginal" );
				padre2.removeClass( claseOriginal ).addClass( "botona" );
				tr.attr( "mostrando", "s" );
				tr.show();
			}
		//oculta los que se estén mostrando
		$("tr[name='fila_detalle'][mostrando='s'][id!='detalle_"+codigo+"']").each(function(){
				$(this).attr( "mostrando", "n" );
				$(this).hide();
				claseOriginal = $(this).prev().attr( "classOriginal" );
				$(this).prev().removeClass( claseOriginal ).addClass( "fila1" );
		});

	}
		
	function cerrarAviso( )
	{
		$.unblockUI();
	}

	function valorDefecto( accion, input )
	{
		input2 = jQuery( input );
		if( accion == "on" && $.trim(input2.val()) == " %, TODOS " )
			{
				input2.val( "" );
			}
		if( accion == "off" && $.trim(input2.val()) == "" )
			{
				input2.val( "%, TODOS" );
			}
	}
	
</script>
<body>
<?php
include_once('root/comun.php');

function menuPpal( $wemp_pmla, $wbasedato, $hce, $hoy )
{
	$menu  = "<span class='subtituloPagina2'>Parámetros de consulta</span>";
	$menu .= "<br><br>";
	$menu .= "<table width='600'>";
		$menu .= "<tr><td class='fila1' width='20%'>M&Eacute;DICO: </td><td class='fila2' colspan='2'><input type='text' id='input_medico'  onfocus='valorDefecto( \"on\", this );' onblur='valorDefecto( \"off\", this );' size='60' value='%, TODOS'></td><tr>";
	$menu .= "</table>";
	$menu .= "<table width='600'>";
		$menu .= "<tr><td class='fila1' width='20%'>PERIODO: </td><td class='fila2' align='center'><b>Fecha Inicial </b><br><input type='text' id='fecha_ini' value='{$hoy}'></td><td align='center' class='fila2'><b>Fecha Inicial </b><br><input type='text' id='fecha_fin' value='{$hoy}' ></td></td><tr>";
	$menu .= "</table>";
	$menu .= "<br><br>";
	$menu .= "<table>";
	$menu .= "<tr><td><input type='button' id='btn_consultar' name='btn_consultar' value='CONSULTAR' onclick='generarInforme()'></td></tr>";
	$menu .= "</table>";
	return($menu);
}

function inicializarArreglosParaAutocompletes( $conex, $wemp_pmla, $wbasedato, $whce )
{
	$medicos = array();
	array_push( $medicos, "%, TODOS" );
	$query = "SELECT Meduma codigo, medno1 nombre1, medno2 nombre2, medap1 apellido1, medap2 apellido2
				FROM {$wbasedato}_000048
			   WHERE Meduma <> ''
			     AND Medurg = 'on'
			     AND Medest = 'on'";
	$rs	   = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$nombre = $row['nombre1']." ".$row['nombre2']." ".$row['apellido1']." ".$row['apellido2'];
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );
		array_push( $medicos, $row['codigo'].", ".$nombre);
	}
	return( $medicos );
}

$wactualiz 	= "2013-04-17";
$wbasedato  = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$whce	    = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
$medicos 	= inicializarArreglosParaAutocompletes( $conex, $wemp_pmla, $wbasedato, $whce );
$medicos 	= json_encode( $medicos );


encabezado( "REPORTE CONSULTAS POR MÉDICO URGENCIAS",$wactualiz, "clinica" );	


/**VARIABLES GLOBALES**/
echo "<input type='hidden' id='wemp_pmla' value='{$wemp_pmla}'>";
echo "<input type='hidden' id='wbasedato' value='{$wbasedato}'>";
echo "<input type='hidden' id='whce' value='{$whce}'>";
echo "<input type='hidden' id='array_medicos' value='{$medicos}'>";

echo "<form style='height:80%;' align='center'>";
/**-----------------------------------------------------------------------------MAQUETA INICIAL (MENÚ Y RESPUESTAS AJAX)----------------------------------------------------------------------------------**/
echo "<div id='div_menu_principal' align='center'>".menuPpal( $wemp_pmla, $wbasedato, $hce, $hoy )."</div>";
echo "<br>";
echo "<div align='center' name='div_retornar' style='display:none; width:100%;'><input type='button' value='RETORNAR' onclick='retornar();'></div>";
echo "<br>";
//echo "<div  class='fila2' id='div_respuestas' align='center' style='overflow-y:scroll; height:500px;  border: 1px solid; border-color:#2A5DB0;'></div>";
echo "<div  class='fila2' id='div_respuestas' align='center' style=' display:none; border: 1px solid; border-color:#2A5DB0;'></div>";
echo "<br>";
echo "<div align='center' name='div_retornar' style='display:none; width:100%;'><input type='button' value='RETORNAR' onclick='retornar();'></div>";
echo "<br><br>";
echo "<div align='center'><input type='button' value='CERRAR' onclick='window.close();'></div>";
echo "<div id='div_sinDatos' align='center' class='fila2' style='cursor:default; display:none; repeat scroll 0 0; position:relative; width:100%; height:98%; overflow:auto;'>";
	echo "<table width='100%'>";
		echo "<tr><td class='botona' align='center'> AVISO </td></tr>";
		echo "<tr><td align='center'> <span class='subtituloPagina2'><br>NO EXISTEN DATOS QUE CUMPLAN CON LA CONSULTA REALIZADA</span><br><br> <input type='button' value='ACEPTAR' onclick='cerrarAviso();'></td></tr>";
	echo "</table>";
echo "</div>";
echo "</form>";

?>
</body>
</html>