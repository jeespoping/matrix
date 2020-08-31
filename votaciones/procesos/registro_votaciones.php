<?php
include_once("conex.php");
session_start();

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");
      
include_once("root/comun.php");


$conex = obtenerConexionBD("matrix");


if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}



$key = substr($user, 2, strlen($user)); //se eliminan los dos primeros digitos

if(is_numeric($key))
{	
	if(strlen($key) == 7 AND "'".substr($key, 2)."'" != "'".$wemp_pmla."'")
	{

		$wemp_pmla1=(substr($key, 0,2)); //el wemp_pmla son los dos primeros digitos
	    $key2 = substr( $key, -5 );
	}
	else
	{
		$wemp_pmla1=$wemp_pmla;
		$key2 = substr( $key, -5 );
	}

}
else
{
	$key2=$key;
	$wemp_pmla1=$wemp_pmla;
}

mysql_select_db("matrix") or die("No se selecciono la base de datos");   

$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "votaciones" ); //Trae el nombre para el contro de la base de datos del fondos de empleados.
$wlogoempresa = strtolower( $institucion->baseDeDatos );
$wusuario = substr($user,2,7);


function cerrar_votacion($codigo,$aplicacion){
	
	global $wbasedato;
	global $conex;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');
	
	$q =   " UPDATE ".$wbasedato."_000001 "
		  ."   SET Votact = 'off'"
		  ."  WHERE Votapl = '".$aplicacion."'"
		  ."	AND Votcod = '".$codigo."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	$update = mysql_affected_rows();	
	
	if($update > 0){		
		$datamensaje['mensaje'] = "La votacion ha sido cerrada.";		
	}else{
		$datamensaje['mensaje'] = "No se cerró la votación.";
		$datamensaje['error'] = 1;
	}
	
	echo json_encode($datamensaje);
    return;
}


function grabar_votacion($wemp_pmla, $wusuario, $descripcion, $empresa, $fecha_inicio, $fecha_fin, $voto_blanco, $columnas_tarjeton, $con_suplente, $ver_suplente, $fecha_max_inscripcion, $fecha_ini_inscripcion,$reporte_votaciones, $aprobar_inscripciones, $habilitar_inscripciones, $temas, $video_obligatorio,$horaInicioVotaciones,$horaFinalVotaciones,$horaInicioInscripciones,$horaFinalInscripciones){
	
	global $wbasedato;
	global $conex;
	
	$wfecha = date("Y-m-d");
    $whora  = date("H:i:s");

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');
	
	$q =   " SELECT MAX(Votcod*1) as codigo "
		  ."   FROM ".$wbasedato."_000001 "
		  ."  WHERE Votapl = '".$empresa."' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	$row = mysql_fetch_array($res);
	$consecutivo = ($row['codigo']*1) + 1;
	
	$estado_inscripcion = 'off';
	
	if($habilitar_inscripciones == 'on'){
		
		$estado_inscripcion = 'on';
	}
	
	$q = " INSERT INTO ".$wbasedato."_000001 (       Medico    ,        Fecha_data,  Hora_data,   Votdes,      Votcod,         Votapl     ,         Votini     ,     Votfin     ,        Votbla     , 		 Votcol          , 		Votsup        		 ,            Votins           ,  		Votfmi              , 	       Votain            , 				Vothin              ,  Votest, Votact,         Votvob           ,	       Votein         ,			Vothii			,				Vothci		   ,			Vothiv		 ,			Vothcv		  ,  Seguridad     ) "
					  ."               VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$descripcion."','".$consecutivo."','".$empresa."' , '".$fecha_inicio."','".$fecha_fin."', '".$voto_blanco."', '".$columnas_tarjeton."', '".$con_suplente."', '".$fecha_ini_inscripcion."','".$fecha_max_inscripcion."','".$aprobar_inscripciones."','".$habilitar_inscripciones."'    ,  'on'  ,  'on' , '".$video_obligatorio."' ,'".$estado_inscripcion."','".$horaInicioInscripciones."','".$horaFinalInscripciones."','".$horaInicioVotaciones."','".$horaFinalVotaciones."','C-".$wusuario."')";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	foreach($temas as $key => $value){
		
		$dato_tema = explode("-",$value);
		$nombre_tema = $dato_tema[0];
		$video_tema = $dato_tema[1];
		
		$q = " INSERT INTO ".$wbasedato."_000002 (   Medico  ,        Fecha_data,  Hora_data,   Temapl         ,       Temcvo     ,          Temdes               ,     Temvid      ,  Temest, Seguridad     ) "
					  ."               VALUES ('".$wbasedato."','".$wfecha."','".$whora."',  '".$empresa."'    ,'".$consecutivo."','".utf8_decode($nombre_tema)."','".$video_tema."',   'on' ,'C-".$wusuario."')";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
		
	}
	
	$datamensaje['mensaje'] = "Votacion registrada";
	
	echo json_encode($datamensaje);
    return;
	
}

if(isset($consultaAjax))
	{
	
	switch($consultaAjax){

		case 'grabar_votacion':  
					{					
					echo grabar_votacion($wemp_pmla, $wusuario, $descripcion, $empresa, $fecha_inicio, $fecha_fin, $voto_blanco, $columnas_tarjeton, $con_suplente, $ver_suplente, $fecha_max_inscripcion, $fecha_ini_inscripcion,$reporte_votaciones, $aprobar_inscripciones, $habilitar_inscripciones, $temas, $video_obligatorio,$horaInicioVotaciones,$horaFinalVotaciones,$horaInicioInscripciones,$horaFinalInscripciones);
					}					
		break;

		case 'cerrar_votacion':
		{
			echo cerrar_votacion($codigo,$aplicacion);
			
		}
		
		default: break;
		
		}
	return;
	}

?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
<title>Configuración votaciones</title>
<meta charset="utf-8">
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />


<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/jquery-ui-timepicker-addon.js" type="text/javascript" ></script>

<style type="text/css">
/* ToolTip classses */ 
.tooltip {
display: inline-block;    
}
.tooltip .tooltiptext {
    margin-left:9px;
    width : 320px;
    visibility: hidden;
    background-color: #FFF;
    border-radius:4px;
    border: 1px solid #aeaeae;
    position: absolute;
    z-index: 1;
    padding: 5px;
    margin-top : -15px; /* according to application */ 
   opacity: 0;
    transition: opacity 1s;
}
.tooltip .tooltiptext::after {
    content: " ";
    position: absolute;
    top: 5%;
    right: 100%; /* To the left of the tooltip */
    margin-top: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: transparent #aeaeae transparent transparent;
}


.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

</style>
<script type="text/javascript">

function cerrarVentana(){
  window.close();	
 }

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
 
$( function() {
	var start = new Date(),
	end = new Date(),
	start2, end2;

end.setDate(end.getDate() + 365);


// ------------------------------------------------------


$('#datetime_inicio_inscripcion').datetimepicker({
	minDate: start,
    minDateTime: start,
    minTime: start,
    controlType: 'select',
	oneLine: true,
	showTime	: false,
	hourText	: "Hora",
	minuteText	: "Minuto",
	dateFormat	: "yy-mm-dd",
	timeFormat	: 'HH:mm',
	separator	: ' a las: ',
	beforeShow : function() {
        inicializarFechas("inscripcion","inicio",start);
    }
});

$('#datetime_final_inscripcion').datetimepicker({
	minDate: start,
    minDateTime: start,
    minTime: start,
    controlType: 'select',
	oneLine: true,
	showTime	: false,
	hourText	: "Hora",
	minuteText	: "Minuto",
	dateFormat	: "yy-mm-dd",
	timeFormat	: 'HH:mm',
	separator	: ' a las: ',
	beforeShow : function() {
        inicializarFechas("inscripcion","final",start);
    }
});

$('#datetime_inicio_votacion').datetimepicker({
	minDate: start,
    minDateTime: start,
    minTime: start,
    controlType: 'select',
	oneLine: true,
	showTime	: false,
	hourText	: "Hora",
	minuteText	: "Minuto",
	dateFormat	: "yy-mm-dd",
	timeFormat	: 'HH:mm',
	separator	: ' a las: ',
	beforeShow : function() {
        inicializarFechas("votacion","inicio",start);
    }
});

$('#datetime_final_votacion').datetimepicker({
	minDate: start,
    minDateTime: start,
    minTime: start,
    controlType: 'select',
	oneLine: true,
	showTime	: false,
	hourText	: "Hora",
	minuteText	: "Minuto",
	dateFormat	: "yy-mm-dd",
	timeFormat	: 'HH:mm',
	separator	: ' a las: ',
	beforeShow : function() {
        inicializarFechas("votacion","final",start);
    }
});


	
	var estado = $("#estado").val();
	
	//Inactiva los cajones si hay una votacion activa
	if(estado == 'Activa'){
		$("#votacion_activa").css("background-color", "yellow"); 
		$("#votacion_activa").html("Tiene una votación activa en este momento, no puede registrar una nueva.");
		$("input").prop('disabled', true);
		$("select").prop('disabled', true);
		$("#cerrar_votacion").prop('disabled', false);
		$("#btncerrar").removeAttr('disabled');

	}

  } );

function inicializarFechas(tipo,accion,start)
{
	var inicioInscripciones = $("#datetime_inicio_inscripcion").datepicker("getDate");
	var finalInscripciones = $("#datetime_final_inscripcion").datepicker("getDate");
	var inicioVotacion = $("#datetime_inicio_votacion").datepicker("getDate");
	var finalVotacion = $("#datetime_final_votacion").datepicker("getDate");
	
	if(tipo=="votacion")
	{
		if(accion=="inicio")
		{
			// fecha minima debe ser mayor que la fecha final del cierre de inscripciones y fecha inicial de inscripciones
			var inicialVotacion = "";
			if(finalInscripciones!=null)
			{
				inicialVotacion = finalInscripciones;
			}
			else if(inicioInscripciones!=null)
			{
				inicialVotacion = inicioInscripciones;
			}
			
			if(inicialVotacion != "")
			{
				$("#datetime_inicio_votacion").datetimepicker( "option", "minDate", inicialVotacion );	
				$("#datetime_inicio_votacion").datetimepicker( "option", "minDateTime", inicialVotacion );	
				$("#datetime_inicio_votacion").datetimepicker( "option", "minTime", inicialVotacion );	
			}
			
			// fecha maxima debe ser menor a la fecha final de votaciones
			var maxinicialVotacion = "";
			if(finalVotacion!=null)
			{
				maxinicialVotacion = finalVotacion;
			}
			
			if(maxinicialVotacion != "")
			{
				$("#datetime_inicio_votacion").datetimepicker( "option", "maxDate", maxinicialVotacion );	
				$("#datetime_inicio_votacion").datetimepicker( "option", "maxDateTime", maxinicialVotacion );	
				$("#datetime_inicio_votacion").datetimepicker( "option", "maxTime", maxinicialVotacion );	
			}
			
			
		}
		else
		{
			// fecha minima debe ser mayor a la fecha de inicio de las votaciones o mayor a la fecha de cierre de las inscripciones
			var finalVotacion = "";
			if(inicioVotacion!=null)
			{
				finalVotacion = inicioVotacion;
			}
			else if(finalInscripciones!=null)
			{
				finalVotacion = finalInscripciones;
			}
			else if(inicioInscripciones!=null)
			{
				finalVotacion = inicioInscripciones;
			}
			
			if(finalVotacion != "")
			{
				$("#datetime_final_votacion").datetimepicker( "option", "minDate", finalVotacion );	
				$("#datetime_final_votacion").datetimepicker( "option", "minDateTime", finalVotacion );	
				$("#datetime_final_votacion").datetimepicker( "option", "minTime", finalVotacion );	
			}
		}
	}
	else
	{
		if(accion=="inicio")
		{
			// fecha minima fecha actual
			
			// fecha maxima menor a la fecha y hora de cierre de inscripcion o la fecha y hora de inicio de las votaciones, si no tiene 
			var finalInscripcion = "";
			if(finalInscripciones!=null)
			{
				finalInscripcion = finalInscripciones;
			}
			else if(inicioVotacion!=null)
			{
				finalInscripcion = inicioVotacion;
			}
			
			if(finalInscripcion != "")
			{
				$("#datetime_inicio_inscripcion").datetimepicker( "option", "maxDate", finalInscripcion );	
				$("#datetime_inicio_inscripcion").datetimepicker( "option", "maxDateTime", finalInscripcion );	
				$("#datetime_inicio_inscripcion").datetimepicker( "option", "maxTime", finalInscripcion );	
			}
			
			
		}
		else
		{
			var minfinalInscripcion = "";
			if(inicioInscripciones!=null)
			{
				minfinalInscripcion = inicioInscripciones;
			}
			
			if(minfinalInscripcion != "")
			{
				// fecha minima debe ser la fecha mayor a la fecha de inscripcion o igual a la actual
				$("#datetime_final_inscripcion").datetimepicker( "option", "minDate", minfinalInscripcion );	
				$("#datetime_final_inscripcion").datetimepicker( "option", "minDateTime", minfinalInscripcion );	
				$("#datetime_final_inscripcion").datetimepicker( "option", "minTime", minfinalInscripcion );
			}
				
			
			// la fecha maxima de inscripcion debe ser menor a la fecha inicio de votacion o la fecha actual
			var maxfinalInscripcion = "";
			if(finalVotacion!=null)
			{
				maxfinalInscripcion = finalVotacion;
			}
			
			if(maxfinalInscripcion != "")
			{
				$("#datetime_final_inscripcion").datetimepicker( "option", "maxDate", maxfinalInscripcion );	
				$("#datetime_final_inscripcion").datetimepicker( "option", "maxDateTime", maxfinalInscripcion );	
				$("#datetime_final_inscripcion").datetimepicker( "option", "maxTime", maxfinalInscripcion );	
			}
		}
	}
}
  
function cerrar_votacion(codigo, aplicacion, wemp_pmla){
	
	
	$.blockUI({ message:	'Cargando...',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });
		
		$.post("registro_votaciones.php",
				{
					consultaAjax:       'cerrar_votacion', 								
					wemp_pmla:			wemp_pmla,
					codigo:				codigo,
					aplicacion:			aplicacion								

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						alert(data_json.mensaje);
						$.unblockUI();
						return;
					}
					else
					{       
															
						alert(data_json.mensaje);						
						location.reload();
						
						return;
					}

			},
			"json"
		);
	
}

function ver_detalle_votacion(aplicacion, codigo_votacion){
	
	if($("#detalle_"+aplicacion+"_"+codigo_votacion).is(":visible")){
		$("#mas_detalle_"+aplicacion+"_"+codigo_votacion).html("[+]");
		$("#detalle_"+aplicacion+"_"+codigo_votacion).hide("slow");
	}else{
		$("#mas_detalle_"+aplicacion+"_"+codigo_votacion).html("[-]");
		$("#detalle_"+aplicacion+"_"+codigo_votacion).show("slow");
	}
	
}


function ver_votaciones(){
	
	if($("#ver_votaciones_registradas").is(":visible")){
		$("#mas").html("[+]");
		$("#ver_votaciones_registradas").hide("slow");
	}else{
		$("#mas").html("[-]");
		$("#ver_votaciones_registradas").show("slow");
	}
	
}  
  
function eliminar_tema(tema)  {
	
	$("#tema"+tema).remove();
	
}
 
var nextinput = 0;
function AgregarTema(){
	
	nextinput++;
	campo = '<li id="tema'+nextinput+'"><span><img src="../../images/medical/root/borrar.png" width=11 heigth=11 title="Eliminar tema" onclick="eliminar_tema('+nextinput+');"></span> Tema '+nextinput+':<input type="text" size="20" class= "temas" id="campo' + nextinput + '" name="campo' + nextinput + '"/>&nbsp;&nbsp;Nombre del video sin extensión '+nextinput+':<input type="text" size="20" class= "video_asociado"  id="video' + nextinput + '" name="video' + nextinput + '"/><span class="tooltip">&nbsp;&nbsp;<img src="../../images/medical/root/info.png" width=11 heigth=11><span class="tooltiptext">Debe ser el nombre del video sin la extensión. </span> </span></li>';
	$("#campos").append(campo);
}
  
 function validar_aprobacion(){
	 
	 var habilitar_inscripciones = $("#habilitar_inscripciones").val();
	 
	 if(habilitar_inscripciones == 'off'){
		
		$("#aprobar_inscripciones").val("off");
		$("#datetime_final_inscripcion").attr("disabled","disabled");		
		$("#datetime_inicio_inscripcion").attr("disabled","disabled");	
		
	 }else{
		 
		$("#aprobar_inscripciones").val("on");			
		$("#datetime_final_inscripcion").removeAttr("disabled");	
		$("#datetime_inicio_inscripcion").removeAttr("disabled");	
	 }
	 
 }
  
function justNumbers(e)
        {
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46))
        return true;
         
        return /\d/.test(String.fromCharCode(keynum));
        }  
 

 function isEmptyJSON(obj) {
	 
  for(var i in obj) { return false; }
  return true;

}

function grabar_votacion(wemp_pmla)	{
	
	
	var datetimeInicioVotacion = $("#datetime_inicio_votacion").val();
	var datetimeFinalVotacion = $("#datetime_final_votacion").val();
	var datetimeInicioInscripcion = $("#datetime_inicio_inscripcion").val();
	var datetimeFinalInscripcion = $("#datetime_final_inscripcion").val();
	
	datetimeInicioVotacion = datetimeInicioVotacion.split(" a las: ");
	datetimeFinalVotacion = datetimeFinalVotacion.split(" a las: ");
	datetimeInicioInscripcion = datetimeInicioInscripcion.split(" a las: ");
	datetimeFinalInscripcion = datetimeFinalInscripcion.split(" a las: ");
	
	var fecha_inicio = datetimeInicioVotacion[0];
	var hora_inicio = datetimeInicioVotacion[1]+":00";
	
	var fecha_fin = datetimeFinalVotacion[0];
	var hora_fin = datetimeFinalVotacion[1]+":00";
	
	var fecha_ini_inscripcion = datetimeInicioInscripcion[0];
	var hora_ini_inscripcion = datetimeInicioInscripcion[1]+":00";
	
	var fecha_max_inscripcion = datetimeFinalInscripcion[0];
	var hora_max_inscripcion = datetimeFinalInscripcion[1]+":00";
	
	
	var sin_desc = 'off';
	var descripcion = $("#descripcion").val();
	var empresa = $("#empresa").val();
	var voto_blanco = $( "#voto_blanco option:selected" ).val();	
	var columnas_tarjeton = $("#columnas_tarjeton").val();
	var con_suplente = $( "#con_suplente option:selected" ).val();	
	var aprobar_inscripciones = $("#aprobar_inscripciones").val();
	var video_obligatorio = $("#video_obligatorio").val();
	var habilitar_inscripciones =  $( "#habilitar_inscripciones option:selected" ).val();		
	
	var temas = {};	
	var video = {};	
	
	$('.temas').each(function(x) {      
        
		y = x+1;
		$("#video"+y).val();
    
		temas[x] = $(this).val()+"-"+$("#video"+y).val();
	
		if( temas[x] == ''){			
			sin_desc = 'on';
		}
    });	
	
	
	if(descripcion == ''){		
		alert("Debe ingresar una descripción.");
		return;
	}
	
	if(empresa == ''){		
		alert("Debe seleccionar una empresa.");
		return;
	}
		
	if (con_suplente == ''){
		alert("Debe seleccionar si las votaciones son con suplente.");
		return;
	}
	
	if(sin_desc == 'on'){
		alert("Uno de los temas no tiene descripción.");
		return;
	}
	
	if(isEmptyJSON(temas)){		
		alert("Debe asociar almenos un tema a la votación.");
		return;
	}
	
	
	$.blockUI({ message:	'Cargando...',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });
		
		$.post("registro_votaciones.php",
				{
					consultaAjax:       		'grabar_votacion', 
					wemp_pmla:					wemp_pmla,					
					descripcion:				descripcion,
					empresa:					empresa,
					fecha_inicio:				fecha_inicio,
					fecha_fin:					fecha_fin,
					voto_blanco:				voto_blanco,
					columnas_tarjeton:			columnas_tarjeton,
					con_suplente:				con_suplente,				
					fecha_max_inscripcion:		fecha_max_inscripcion,	
					fecha_ini_inscripcion:		fecha_ini_inscripcion,
					aprobar_inscripciones:		aprobar_inscripciones,
					habilitar_inscripciones:	habilitar_inscripciones,
					video_obligatorio:			video_obligatorio,
					temas:						temas,
					videos:						video,					
					horaInicioVotaciones:		hora_inicio,					
					horaFinalVotaciones:		hora_fin,					
					horaInicioInscripciones:	hora_ini_inscripcion,					
					horaFinalInscripciones:		hora_max_inscripcion					

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						alert(data_json.mensaje);
						$.unblockUI();
						return;
					}
					else
					{       
															
						alert(data_json.mensaje);						
						$.unblockUI();						
						location.reload();
						
						return;
					}

			},
			"json"
		);

}



</script>
<BODY>
<?php

//==========================================================================================================================================
//PROGRAMA				      :Registro de nuevas votaciones.                                                                  
//AUTOR				          :Jonatan Lopez Aguirre.                                                                        
//FECHA CREACION			  :Abril 19 de 2016 
//-----------------------------------------------------------------------------------------------------------------------------------------            

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz="Febrero 28 de 2018";
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2018-02-28		Jessica 	- Se agrega la hora a las fechas de inicio y cierre de inscripciones y votaciones.              
//
//--------------------------------------------------------------------------------------------------------------------------------------------
 

encabezado("Configuración votaciones",$wactualiz, $wlogoempresa);

//Configuracion de la votacion
$q_conf_vot =    " SELECT * "
				."   FROM ".$wbasedato."_000001 "
			    ."	WHERE Votapl = '".$aplicacion."'";
$res_conf_vot = mysql_query($q_conf_vot,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_conf_vot." - ".mysql_error());

$array_conf_vot = array();

	while($row_conf_vot = mysql_fetch_assoc($res_conf_vot))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_conf_vot['Votcod'], $array_conf_vot))
        {
            $array_conf_vot[$row_conf_vot['Votcod']] = $row_conf_vot;
        }

    }

$q_temas =   " SELECT * "
			."   FROM ".$wbasedato."_000002 "
			."	WHERE Temapl = '".$aplicacion."'"			
			."	  AND Temest = 'on'";
$res_temas = mysql_query($q_temas,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_temas." - ".mysql_error());

$array_temas = array();

	while($row_temas = mysql_fetch_assoc($res_temas))
    {
       			
            $array_temas[$row_temas['Temcvo']][] = $row_temas;
       

    }

echo "<center>";
if(count($array_conf_vot)>0){
	

echo "<div onclick=ver_votaciones(); style='cursor:pointer;'><b><span id=mas>[+]</span> Ver votaciones registradas</b></div><br>";
echo "<div id='ver_votaciones_registradas' style='border-style: groove; border-width: 1px; display:none;'>";

foreach($array_conf_vot as $key => $value){
	
	$voto_blanco = $value['Votbla'] == 'on' ? 'Si' : 'No';
	$suplente = $value['Votsup'] == 'on' ? 'Si' : 'No';
	$inscripcion = $value['Vothin'] == 'on' ? 'Si' : 'No';
	$estado = $value['Votact'] == 'on' ? 'Activa' : 'Inactiva';
	
	if($estado == 'Activa'){
		echo "<input type=hidden value='".$estado."' id='estado'>";
	}
	
	echo "<br><span class=fila1 onclick='ver_detalle_votacion(\"".$aplicacion."\", \"".$value['Votcod']."\");' style='cursor:pointer;'><b><span id='mas_detalle_".$aplicacion."_".$value['Votcod']."''>[+]</span>".$value['Votdes']."</b></span>";
	echo "<div id='detalle_".$aplicacion."_".$value['Votcod']."' style='display:none;'>";
	echo "<table>";
	echo "<tr class=fila1>";
	echo "<td><b>Fecha y hora de inicio votación:</b></td><td>".$value['Votini']." a las: ".$value['Vothiv']."</td>";
	echo "</tr>";
	echo "<tr class=fila2>";
	echo "<td><b>Fecha y hora de cierre votación:</b></td><td>".$value['Votfin']." a las: ".$value['Vothcv']."</td>";
	echo "</tr>";
	echo "<tr class=fila1>";
	echo "<td><b>Voto en blanco:</b></td><td>".$voto_blanco."</td>";
	echo "</tr>";
	echo "<tr class=fila2>";
	echo "<td><b>Columnas:</b></td><td>".$value['Votcol']."</td>";
	echo "</tr>";
	echo "<tr class=fila1>";
	echo "<td><b>Con suplente:</b></td><td>".$suplente."</td>";
	echo "</tr>";
	echo "<tr class=fila2>";
	echo "<td><b>Con inscripcion:</b></td><td>".$inscripcion."</td>";
	echo "</tr>";	
	echo "<tr class=fila1>";
	echo "<td><b>Fecha y hora de inicio inscripción:</b></td><td>".$value['Votins']." a las: ".$value['Vothii']."</td>";
	echo "</tr>";
	echo "<tr class=fila2>";
	echo "<td><b>Fecha y hora de cierre inscripción:</b></td><td>".$value['Votfmi']." a las: ".$value['Vothci']."</td>";
	echo "</tr>";
	echo "<tr class=fila1>";
	
	echo "<td><b>Estado:</b></td><td $clase_abierta>".$estado." $inactivar</td>";
	echo "</tr>";
	
	if($estado == 'Activa'){
		echo "<tr bgcolor=#c2e77a><td><font size=2><b>Cerrar votación:</b></font></td><td><input type=checkbox id='cerrar_votacion' onclick='cerrar_votacion(\"".$value['Votcod']."\", \"".$value['Votapl']."\", \"$wemp_pmla\" );'></td></tr>";
		
	}
	
	echo "</table><br>";
	
	echo "<table>";
	echo "<tr class=fila1>";
	echo "<td><b>Temas asociados</b></td>";
	echo "</tr>";
	
	$j = 0;
	
	foreach($array_temas[$key] as $key1 => $value1){
		$class = "class='fila".(($j%2)+1)."'";
		$j++;

		echo "<tr $class><td>".utf8_encode($value1['Temdes'])."</td></tr>";
	}
	
	echo "</table><hr>";
	echo "</div><br><br>";

}

echo "</div>";
}

echo "<br>";
echo "<div id='votacion_activa'></div>";
echo "<table style='text-align: left;' class='tituloPagina' border='0' cellpadding='2' cellspacing='2'>
  <tbody>     
	<tr class='fila2'>
      <td>Descripción:</td>
      <td><input id='descripcion' type='text'><input type=hidden value='".$aplicacion."' id='empresa'></td>
    </tr> 	
	<tr class='fila1'>
      <td>Fecha y hora de inicio votación:</td>
      <td><input id='datetime_inicio_votacion' class='clsdatetime' readonly type='text'></td>
    </tr>
	<tr class='fila2'>
      <td>Fecha y hora de cierre votación:</td>
      <td><input id='datetime_final_votacion' class='clsdatetime' readonly type='text'></td>
    </tr>
    <tr class='fila1'>
      <td>Con voto en blanco:</td>
      <td><select id='voto_blanco'>
		  <option value=''></option>
		  <option value='on'>Si</option>
		  <option value='off'>No</option> 
		  </select>
	 </td>
    </tr>
    <tr class='fila2'>
      <td>Columnas tarjeton:</td>
      <td><input id='columnas_tarjeton' onkeypress='return justNumbers(event);' value='2'></td>
    </tr>
    <tr class='fila1'>
      <td>Con suplente:</td>
      <td><select id='con_suplente'>
		  <option value=''></option>
		  <option value='on'>Si</option>
		  <option value='off'>No</option> 
		  </select>
	  </td>
    </tr> 
	<tr class='fila2'>
      <td>Habilitar inscripciones:</td>
      <td><select id='habilitar_inscripciones' onchange='validar_aprobacion();'>
		  <option value=''></option>
		  <option value='on'>Si</option>
		  <option value='off'>No</option> 
		  </select>
	  </td>
    </tr>
	<tr class='fila1'>
      <td>Fecha y hora de inicio inscripción:</td>
      <td><input id='datetime_inicio_inscripcion' class='clsdatetime' readonly disabled type='text'></td>
    </tr>
	<tr class='fila2'>
      <td>Fecha y hora de cierre de inscripción:</td>
      <td><input id='datetime_final_inscripcion' class='clsdatetime' readonly disabled type='text'></td>
    </tr>
    <tr class='fila1' style='display:none;'>
      <td>Aprobar inscritos:</td>
      <td><input type=hidden id='aprobar_inscripciones' value=''>		 
	  </td>
    </tr> 
	<tr class='fila1'>
      <td>Ver video obligatorio para votar:</td>
      <td><select id='video_obligatorio'>
		  <option value=''></option>
		  <option value='on'>Si</option>
		  <option value='off'>No</option> 
		  </select>		 
	  </td>
    </tr>  
     
  </tbody>
</table>";


echo "<table class='textoNormal'>
		<tr>
		<td><br><b>TEMAS ASOCIADOS A LA VOTACION </b><a href='#' onclick='AgregarTema();'>Agregar Tema</a></td>
		</tr>		
	</table>";
	
echo "<table>
		
		<div id='campos'></div>
	</table>";

echo "</center>";
echo "<center>
	<div><br><input type=button value=Grabar onclick='grabar_votacion(\"$wemp_pmla\");'><br><br><input type=button id=btncerrar onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";


?>
</BODY>
</html>
