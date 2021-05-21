<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	ENCUESTA AL PACIENTE
 * Fecha		:	2018-08-02
 * Por			:	Felipe Alvarez Sanchez
 * Descripcion	:
 * Condiciones  :
 **********************************************************************************************************/
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//
// 2019-04-15 Jessica Madrid 	- Se modifica la función cerrarEncuesta() ya que al verificar que se hubieran contestado las preguntas
// 								  de la encuesta seleccionada si había una encuesta con consecutivo similar hacía la validación sobre 
// 								  esas preguntas de otras encuestas.
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	$wactualiz = "2019-04-15";
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\

if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

if( isset($consultaAjax) == false ){

?>
	<html>
	<head>
	<title>Encuestas a Pacientes</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	
	
	<script   src="../../../include/gentelella/vendors/jquery/dist/jquery.min.js" ></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" >
	<link rel="stylesheet" href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css">

	<link rel="stylesheet" href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="../../../include/gentelella/build/css/custom.css">
	

	
	
	
	
	<script   src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js" ></script>
	
	
	
    <!-- jQuery Smart Wizard -->
    <link href="../../../include/gentelella/vendors/jQuery-Smart-Wizard/dist/css/smart_wizard.css" rel="stylesheet" type="text/css">
    <link href="../../../include/gentelella/vendors/jQuery-Smart-Wizard/dist/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css">
	<script src="../../../include/gentelella/vendors/jQuery-Smart-Wizard/dist/js/jquery.smartWizard.js"  ></script>
	
	<!-- PNotify -->
	<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
	<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
	<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
	<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.brighttheme.css" rel="stylesheet">
	<!-- PNotify -->
	<script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.js" type="text/rocketscript"></script>
	<script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.buttons.js" type="text/rocketscript"></script>
	<script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.nonblock.js" type="text/rocketscript"></script>



	<style>

.modal-header {
    background-color: #337AB7;
    padding:16px 16px;
    color:#FFF;
    border-bottom:2px dashed #337AB7;
 }







	</style>
	<script>


	</script>
<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		
			    $.post("EncuestasPacientes.php",
				{
					consultaAjax:     '',
					wemp_pmla:        $("#wemp_pmla").val(),
					accion:           'traerppal',
					buscar:			  '',
					wbasedato:		  $("#wbasedato").val(),
					cco:			  $("#ccoautocompletar").attr('valor')
					
					

				},function(data) {
				   
					$("#divmain").html(data);

				}).done(function(){
					
						$('.panel ').click(function(e) {
							$(".panel").hide()
							//alert($(this).attr('historia'));
							$('#modalEncuesta').modal('show');
							var historia = $(this).attr('historia');
							var ingreso  = $(this).attr('ingreso');
							var documento  = $(this).attr('documento');
							var nombre  = $(this).attr('nombre');
							var habitacion = $(this).attr('habitacion');
							var wempresa = $(this).attr('wempresa');
							var wcco = $(this).attr('wcco');
							var wtipoempresa = $(this).attr('wtipoempresa');
							$("#historia").val(historia);
							$("#ingreso").val(ingreso);
							construirContenidoModal(historia, ingreso,documento,nombre,habitacion,wempresa,wtipoempresa,wcco );	
						});
						
						
						$("#modalEncuesta").on("hidden.bs.modal", function () {
							// cierro la encuesta para que si el paciente no le da terminar ella quede guardada.
							cerrarEncuesta($("#encuesta").val(),'no');
							$('.panel').show();
							$("#contenidoModal").html("");
							
				
						});
						
						crear_autocomplete("ccoautocompletar", "hidden_cco");
					
					
				});
		
			
			
			
		
	});
	function buscarcco(valorini, nomini)
	{
		//alert($("#ccoautocompletar").attr('valor'));
		$.post("EncuestasPacientes.php",
				{
					consultaAjax:     '',
					wemp_pmla:        $("#wemp_pmla").val(),
					accion:           'traerppal',
					buscar:			  $("#buscador").val(),
					wbasedato:		  $("#wbasedato").val(),
					cco:			  $("#ccoautocompletar").attr('valor')
					
					

				},function(data) {
				    $("#divmain").html("");
					$("#divmain").html(data);

				}).done(function(){
					
						$('.panel ').click(function(e) {
							$(".panel").hide()
							//alert($(this).attr('historia'));
							$("#contenidoModal").html("");
							$('#modalEncuesta').modal('show');
							var historia = $(this).attr('historia');
							var ingreso  = $(this).attr('ingreso');
							var documento  = $(this).attr('documento');
							var nombre  = $(this).attr('nombre');
							var habitacion = $(this).attr('habitacion');
							$("#historia").val(historia);
							$("#ingreso").val(ingreso);
							var wempresa = $(this).attr('wempresa');
							var wcco = $(this).attr('wcco');
							var wtipoempresa = $(this).attr('wtipoempresa');
							
							construirContenidoModal(historia, ingreso,documento,nombre,habitacion ,wempresa,wtipoempresa,wcco);	
						});
						
						
						$("#modalEncuesta").on("hidden.bs.modal", function () {
							$('.panel').show();
							$("#contenidoModal").html("");
				
						});
					// alert(valorini);
					crear_autocomplete("ccoautocompletar", "hidden_cco" ,nomini , valorini  );
					if (nomini!='')
					{
						$("#ccoautocompletar").val(nomini);
					}
				});
		
	}
	
	
	function crear_autocomplete(campo,HiddenArray,nomIni,codIni)
	{
		
		$("#"+campo).val(nomIni);
		//$("#"+campo).val(codIni);
		$("#"+campo).attr("valor",codIni);
		$("#"+campo).attr("nombre",nomIni);
	
		var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
		
	 

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}
		
		 
			
			$( "#"+campo ).autocomplete({
				minLength: 	3,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+campo ).val(ui.item.label);
					$( "#"+campo ).attr('valor', ui.item.value);
					$( "#"+campo ).attr('nombre', ui.item.name);
					buscarcco(ui.item.value,ui.item.name);
					return false;
				}
			});			
			
			


	}
	
	
	function buscar()
	{
		var valorini= $("#ccoautocompletar").attr('valor');
		var nomini =  $("#ccoautocompletar").attr('nombre');
		// alert("1"+valorini);
		 $.post("EncuestasPacientes.php",
				{
					consultaAjax:     '',
					wemp_pmla:        $("#wemp_pmla").val(),
					accion:           'traerppal',
					buscar:			  $("#buscador").val(),
					wbasedato:		  $("#wbasedato").val(),
					cco:			  $("#ccoautocompletar").attr('valor')
					
					

				},function(data) {
				    $("#divmain").html("");
					$("#divmain").html(data);

				}).done(function(){
					
						$('.panel ').click(function(e) {
							$(".panel").hide()
							//alert($(this).attr('historia'));
							$("#contenidoModal").html("");
							$('#modalEncuesta').modal('show');
							var historia = $(this).attr('historia');
							var ingreso  = $(this).attr('ingreso');
							var documento  = $(this).attr('documento');
							var nombre  = $(this).attr('nombre');
							var habitacion = $(this).attr('habitacion');
							$("#historia").val(historia);
							$("#ingreso").val(ingreso);
							var wempresa = $(this).attr('wempresa');
							var wcco = $(this).attr('wcco');
							var wtipoempresa = $(this).attr('wtipoempresa');
							
							construirContenidoModal(historia, ingreso,documento,nombre,habitacion ,wempresa,wtipoempresa,wcco);	
						});
						
						
						$("#modalEncuesta").on("hidden.bs.modal", function () {
							$('.panel').show();
							$("#contenidoModal").html("");
				
						});
						
						crear_autocomplete("ccoautocompletar", "hidden_cco", nomini , valorini );
						
					
					
				});
		
	}
	
	function insertar_encuesta(encuesta)
	{
		$.post("EncuestasPacientes.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'insertar_encuesta',
			historia:		  $('#historia').val(),
			ingreso:		  $('#ingreso').val(),
			encuesta:		  encuesta,
			wbasedato:		  $("#wbasedato").val()
			

		},function(data) {
	       
			//alert(data);

		});
	}
	
	function construirContenidoModal(his, ing,documento,nombre,habitacion,wempresa,wtipoempresa,wcco)
	{
		
		//alert(wempresa+"---"+wcco+"ppp"+wtipoempresa);
		$("#contenidoModal").html("");
		$.post("EncuestasPacientes.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'traerEncuestas',
			historia:		  his,
			ingreso:		  ing,
			wbasedato:		  $("#wbasedato").val(),
			wempresa:		  wempresa,
			wtipoempresa: 	  wtipoempresa,
			wcco:			  wcco

		},function(data) {
	        
			var html ='<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">'
						+'<ul class="nav nav-tabs tabs-left info">'
							+'<li class="active"><a href="#informacionGeneral"  data-toggle="tab"><h1>Información General</h1></a>'
							+'</li>'
							+data.encabezado
						+'</ul>'
					+'</div>'
					+'<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">'
					  +'<div class="tab-content">'
						+'<div class="tab-pane active" id="informacionGeneral">'
						  +'<br><p><h1><b>Nombre:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+nombre+' </h1><br><h1><b>Historia:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+his+'-'+ing+'</h1> <br><h1><b>Documento: </b>'+documento+'</h1> <br><h1><b>Habitación: </b>&nbsp;'+habitacion+'</h1> </p>'
						+'</div>'
						+data.tabs
					  +'</div>'
					+'</div>'
				    +'<div class="clearfix"></div>';
			
			
			// agrego el contenido a la modal
			
			$("#contenidoModal").html(html);

		},'json').done(function(){
			
			$(".contenedorwizardppal").hide();
			
			
			// Step show event 
            $(".contenedorwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
               //alert("You are on step "+stepNumber+" now");
               if(stepPosition === 'first'){
                   $("#prev-btn").addClass('disabled');
               }else if(stepPosition === 'final'){
                   $("#next-btn").addClass('disabled');
               }else{
                   $("#prev-btn").removeClass('disabled');
                   $("#next-btn").removeClass('disabled');
               }
            });
            
            // Toolbar extra buttons
			var btnterminar = $('<button class="btn-lg btn-warning"></button>').text('Terminar').addClass('btn btn-info').on('click', function(){ 
								
									//alert($("#encuesta").val()); 
									cerrarEncuesta($("#encuesta").val(),'si');
							   });
       
            // Smart Wizard
            $('.contenedorwizard').smartWizard({ 
                    selected: 0, 
                    theme: 'default',
                    transitionEffect:'fade',
                    showStepURLhash: true,
					keyNavigation: false, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
					autoAdjustHeight: false, 
					contentCache: false,
					lang: {  // Language variables
						next: 'Siguiente', 
						previous: 'Anterior'
					},
                    toolbarSettings: {toolbarPosition: 'bottom',
                                      toolbarExtraButtons: [btnterminar]
                                    }
            });
			// reseteo el contenedor
			$('.contenedorwizard').smartWizard("reset");
                                         
            
           
			
			 $(".sw-btn-next").addClass('btn-lg btn-primary');
			 $(".sw-btn-prev").addClass('btn-lg btn-primary');
			 $("#btnterminar").addClass('btn-lg btn-warning');
           
         
            $('.contenedorwizard').smartWizard("theme", "default");
			
		
       
            
		});
		
	}
	
	
	/* 

    function onFinishCallback(objs, context){
        if(validateAllSteps()){
            $('form').submit();
        }
    }

    // Your Step validation logic
    function validateSteps(stepnumber){
        var isStepValid = true;
        // validate step 1
        if(stepnumber == 1){
            // Your step validation logic
            // set isStepValid = false if has errors
        }
        // ...      
    }
    function validateAllSteps(){
        var isStepValid = true;
        // all step validation logic     
        return isStepValid;
    }          
*/
	
	function verEncuesta(encuesta)
	{
		//alert(encuesta);
		//oculto el video
		$("#encuesta").val(encuesta);
		$("#frameEncuesta_"+encuesta).hide();
		//muestro el wizard
		$("#contenedorwizardppal_"+encuesta).show();
		$("#buttonEncuesta_"+encuesta).hide();
		$("#buttonVideo_"+encuesta).show();
		insertar_encuesta(encuesta);
		
		
	}
	
	function verVideo(encuesta)
	{
		//alert(encuesta);
		//oculto el video
		$("#frameEncuesta_"+encuesta).show();
		//muestro el wizard
		$("#contenedorwizardppal_"+encuesta).hide();
		$("#buttonEncuesta_"+encuesta).show();
		$("#buttonVideo_"+encuesta).hide();
		
		
	}
	
	
	
	
	function ClicRadioEscala(id,rotulo,encuesta,Forgco,Forcom,Descod,valor)
	{
		
		$.post("EncuestasPacientes.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'grabaPregunta',
			historia:		  $('#historia').val(),
			ingreso:		  $('#ingreso').val(),
			encuesta:		  encuesta,
			grupocompetencia: Forgco,
			competencia:	  Forcom,
			descriptor :      Descod,
			id06:			  id,
			rotulo:			  rotulo,
			valor:			  valor,
			wbasedato:		  $("#wbasedato").val()

		},function(data) {
	       
			//alert(data);

		});
		
		
	}
	
	function cerrarEncuesta (encuesta, mostrarMensaje)
	{
		
		var encuesta = $('#encuesta').val();
		
		if(encuesta=='')
		{
			return;
		}
		
		var numeropreguntas = $("#numeropreguntas_"+encuesta).val();
		
		//iCheck_'.$encuesta.'
		var group = {};
		var mensaje ='';
		var validacion = false;
		var contestadas = 0;
		$('input[name^="iCheck_'+encuesta+'_"]').each(function (index) {
			var name = this.name;
			var pregunta =$(this).attr('pregunta');
			if (!group[name]) 
			{
				group[name] = true;
				if($("input:radio[name="+name+"]:checked").length == 0)
				{
					mensaje = mensaje +' '+pregunta;
					validacion = true;
					
				}
				else
				{
					contestadas++;
				}
				
			}
		});
		
		if(contestadas !=numeropreguntas)
		{
			new PNotify({
                                  title: 'Error',
                                  text: 'No completo la evaluación , ingrese de nuevo y terminela',
                                  type: 'warning',
                                  styling: 'bootstrap3',
								  delay: 600
						});
			return;
		}
		
		
		if(mostrarMensaje=='no')
			validacion = false;
			
		
		if(validacion)
		{
		
		
			new PNotify({
                                  title: '',
                                  text: 'Faltan las siguientes preguntas por cerrar:'+mensaje,
                                  type: 'warning',
                                  styling: 'bootstrap3',
								  delay: 600
						});
			//alert("Faltan las siguientes preguntas por cerrar:"+mensaje);
		
		}
		else
		{
			$.post("EncuestasPacientes.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'terminarEncuesta',
				historia:		  $('#historia').val(),
				ingreso:		  $('#ingreso').val(),
				encuesta:		  $('#encuesta').val()

			},function(data) {
				  
			    // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-info');
				// $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-danger');
				// $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-success');
				// $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-warning');
				// $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").addClass('panel-warning');
			   for(var indice in data) 
			   {
				  
				  $("#prueba_"+$('#historia').val()+"-"+$('#ingreso').val()+"_"+indice).html(data[indice]+'%');
				 
				  //alert(data[indice])
				  if (data[indice]<70)
				  {
					  //alert("entro")
					  // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-info');
					  // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-danger');
					  // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-success');
					  // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").addClass('panel-danger');// el panel va en rojo
				  }
				  else
				  {
					  //alert("entro")
					  // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-info');
					  // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-danger');
					  // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-success');
					  // $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").addClass('panel-succes');// el panel va en rojo
				  }
			   }
				
			   //prueba_76007-9				
			   
			  var colorpanel='warning';
			  var completo=true;
			  var valor = 0;
			  var ganadas = true;
			  $(".calificacionprueba_"+$('#historia').val()+"-"+$('#ingreso').val()).each(function (){
				  valor = $(this).html() 
				  valor = valor.replace("%", "");
				  if(valor*1 >=70)
				  {
					// alert(valor*1); 
				  }
				  else
				  {
					 if(isNaN(valor))
					 {
						 //alert("letras");
						 //no ha hecho todas las encuestas
						 completo=false;
					 }
					 else{
						//alert("menor"+valor*1);
						ganadas = false;						
					 }
				  }
				  
				  
			  });
			  
			  if(completo && ganadas)
			  {
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-info');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-danger');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-success');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-warning');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").addClass('panel-success');// el panel va en verde
			  }
			  
			  if(completo && !ganadas)
			  {
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-info');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-danger');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-success');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-warning');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").addClass('panel-danger');// el panel va en rojo 
			  }
			  
			  if(!completo)
			  {
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-info');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-danger');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-success');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").removeClass('panel-warning');
				  $("#panelppal_"+$('#historia').val()+"-"+$('#ingreso').val()+"").addClass('panel-warning');// el panel va en amarillo 
			  }
			  
			  if(mostrarMensaje!='no')
			  {
				new PNotify({
                                  title: '',
                                  text: 'Encuesta cerrada con exito',
                                  type: 'success',
                                  styling: 'bootstrap3',
								  delay: 600
				});
			  }

			},'JSON');
		}
		
	}
	




</script>
</head>

<?php

}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



$conex = obtenerConexionBD("matrix");


$user_session = explode('-',$_SESSION['user']);
$user_session = (count($user_session) > 1)? $user_session[1] : $user_session[0];
//$user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;


$wusuario = $user_session;

function traer_cco ()

{
	global $wbasedatoMov;
	global $conex;
	// query para hacer el select
	$query = "SELECT  	Ccocod as 	ppal , Cconom as seleccionado
				FROM ".$wbasedatoMov."_000011 ";

	$res = 	mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

	
	while($row = mysql_fetch_array($res))
	{
		$arr_relacion_ne[utf8_encode(trim($row['ppal']))] = trim(utf8_encode($row['ppal'].'-'.$row['seleccionado']));

		
	}
	
	
	return $arr_relacion_ne;
}


//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if(isset($accion))
{
	switch($accion)
	{
		case "traerppal":
		{
			
			$wbasedato 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'evapac');
			$wbasedatoMov 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			$wbasedatocliame 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			
			if($buscar=='')
			{
				
				if ($cco !='' and $cco !='*')
				{
					$cco = "AND Habcco='".$cco."'";
				}
				else
				{
					$cco ="";
				}
				$select_tablas = "SELECT Habcod,Habcco,Habhis,Habing,Habcpa , Oriced , Pacno1 ,	Pacno2 ,	Pacap1 ,	Pacap2,Pactid ,Ingcem
									FROM ".$wbasedatoMov."_000020 
									LEFT JOIN root_000037 ON (Orihis = Habhis  AND Oriing = Habing )
									LEFT JOIN root_000036 ON (Pacced = Oriced)
									LEFT JOIN ".$wbasedatocliame."_000101 ON ( Orihis = Inghis   AND Oriing = Ingnin )
								   WHERE Habest ='on'
									 AND Habhis !=''
									 AND Habcub !='on' ".$cco."
								   ORDER BY Habcco
							 
							   " ;
				
			}
			else
			{
				
				if ($cco !='' and $cco !='*')
				{
					$cco = "AND Habcco='".$cco."'";
				}
				else
				{
					$cco ="";
				}
				$select_tablas = "SELECT Habcod,Habcco,Habhis,Habing,Habcpa , Oriced , Pacno1 ,	Pacno2 ,	Pacap1 ,	Pacap2,Pactid,Ingcem
									FROM ".$wbasedatoMov."_000020 
									LEFT JOIN root_000037 ON (Orihis = Habhis  AND Oriing = Habing )
									LEFT JOIN root_000036 ON (Pacced = Oriced)
									LEFT JOIN ".$wbasedatocliame."_000101 ON ( Orihis = Inghis   AND Oriing = Ingnin )									
								   WHERE Habest ='on'
									 AND Habhis !=''
									 AND Habcub !='on'
									 AND Habcpa LIKE '%".$buscar."%'
									 ".$cco."
								   ORDER BY Habcco
							 
							   " ;
			}
			//-------
			$ArrayEncuestas = array();	
			//Encgen Encuesta aplica a todos	Encest Estado	Encdia 	Enchfo 	Enchca	Enchva 	Enccco 	Enctem 	Encemp Encafi 	Encpor
			$select = "SELECT  Enccod, Encdes, Encvid, Encenc, Encgen,Encest 
						 FROM  ".$wbasedato."_000060
						 WHERE Encest !='off'
						 ";
			
			$res = mysql_query($select,$conex);
							 
			while($row = mysql_fetch_array($res))
			{
				$ArrayEncuestas[$row['Encenc']]=$row['Encdes'];
			}
			
			
			// aqui hare unas consultas generales para la programacion de encuestas, es decir , si  para algun centro de costos aplica las encuestas 
			// o para alguna empresa especifica o para un tipo de empresa
			
			$ArrayEncuestasgeneral = array();	
			//Encgen Encuesta aplica a todos	Encest Estado	Encdia 	Enchfo 	Enchca	Enchva 	Enccco 	Enctem 	Encemp Encafi 	Encpor
			$select = "SELECT  Enccod, Encdes, Encvid, Encenc, Encgen,Encest,Enccco 
						 FROM  ".$wbasedato."_000060
						 WHERE Encgen ='on'
						   AND Encest !='off'
						   AND Encdia ='*'
						   AND Enchfo ='*'
						   AND Enchca ='*'
						   AND Enchva ='*'
						   AND Enccco ='*'
						   AND Enctem ='*'
						   AND Encemp ='*'
						   AND Encafi ='*'";
			
			$res = mysql_query($select,$conex);
							 
			while($row = mysql_fetch_array($res))
			{
				$ArrayEncuestasgeneral[$row['Encenc']]=$row['Enccod'];
			}
			
			
			$ArrayEncuestascco = array();	
			//Encgen Encuesta aplica a todos	Encest Estado	Encdia 	Enchfo 	Enchca	Enchva 	Enccco 	Enctem 	Encemp Encafi 	Encpor
			$select = "SELECT  Enccod, Encdes, Encvid, Encenc, Encgen,Encest,Enccco 
						 FROM  ".$wbasedato."_000060
						 WHERE Encgen !='on'
						   AND Encest !='off'
						   AND Encdia ='*'
						   AND Enchfo ='*'
						   AND Enchca ='*'
						   AND Enchva ='*'
						   AND Enccco !='*'
						   AND Enctem ='*'
						   AND Encemp ='*'
						   AND Encafi ='*'";
			
			$res = mysql_query($select,$conex);
							 
			while($row = mysql_fetch_array($res))
			{
				$ArrayEncuestascco[$row['Encenc']]=$row['Enccco'];
			}
			
			
			$ArrayEncuestasempresa = array();	
			//Encgen Encuesta aplica a todos	Encest Estado	Encdia 	Enchfo 	Enchca	Enchva 	Enccco 	Enctem 	Encemp Encafi 	Encpor
			$select = "SELECT  Enccod, Encdes, Encvid, Encenc, Encgen,Encest,Enccco ,Encemp
						 FROM  ".$wbasedato."_000060
						 WHERE Encgen ='on'
						   AND Encest !='off'
						   AND Encdia ='*'
						   AND Enchfo ='*'
						   AND Enchca ='*'
						   AND Enchva ='*'
						   AND Enccco ='*'
						   AND Enctem ='*'
						   AND Encemp !='*'
						   AND Encafi ='*'";
			
			$res = mysql_query($select,$conex);
							 
			while($row = mysql_fetch_array($res))
			{
				$ArrayEncuestasempresa[$row['Encenc']]=$row['Encemp'];
			}
			
			
			
			
			
			
			//---------
			//$classfondo='info';
			
			
			
			if($res = mysql_query($select_tablas,$conex))
			{
				//--Abro div 
				
				
				$html.="<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'></div>";
				$html.='<div class="title_right">
							<div >
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" >
										<center>
										<div style="border:1px solid #CCD0D7;" align="center">
											<table width="100%" align="center"><tr><td>
											<span style="background-color:#d9edf7">Pendiente Evaluar</span>
											<span style="background-color:#fcf8e3">Sin terminar</span>
											<span style="background-color:#f2dede">Resultados no satisfactorios</span>
											<span style="background-color:#dff0d8">Satisfactorio</span>
											</td></tr></table>
										</div>
										</center>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" ><input type="text" id="ccoautocompletar" style="border-radius:20px" class="form-control" placeholder="Centro de costos"  ></div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 pull-right top_search">
										<div class="input-group">
											<input type="text" id="buscador" class="form-control" placeholder="Buscar..." value="'.$buscar.'" >
												<span class="input-group-btn">
													<button class="btn btn-default" onclick="buscar()" type="button">Ir!</button>
												</span>
										</div>
									</div>
							 </div>
						</div>
							<br>
							<br><br>
						 <div class="">
				<div class="row">';
				//--------------while principal que lista  los pacientes 
				while($row = mysql_fetch_array($res))
				{
					$classfondo='info';
					$nombreEncuestas ='';
					$porcentajebajo='no';
					$arraycalificacion = array();
					
					// se le debe sumar al array encuestas las encuestas que ya hizo , para que si cambia de centro de costos o de condicion siga aun con las encuestas 
					// realizadas 
					
					// consulto las encuestas que tiene el paciente
					$ArrayEncuestasactuales= array();
					$selecencuestasactuales = " SELECT  *
												  FROM  ".$wbasedato."_000049 
												 WHERE  Enchis = '".$row['Habhis']."-".$row['Habing']."'";
					
					
					//echo $selecencuestasactuales;
					$resactuales = mysql_query($selecencuestasactuales,$conex);
					while($rowactuales = mysql_fetch_array($resactuales))
					{
						$ArrayEncuestasactuales[$rowactuales['Encenc']] = 's'; 
					}
					
					
					// las hizo todas
					$yarealizadas=true;
					$almenosuna=false;
					$ganotodas=true;
					$aux='';
					//------- se recorren las encuestas que tiene cada paciente
					$k=0;
					foreach ($ArrayEncuestas as $clave=>$value)
					{
						
						
						if(($ArrayEncuestasgeneral[$clave]) or  ($ArrayEncuestascco[$clave] and  $ArrayEncuestascco[$clave] == $row['Habcco'] ) or ($ArrayEncuestasempresa[$clave] and  $ArrayEncuestasempresa[$clave] == $row['Ingcem'] ) or ($ArrayEncuestasactuales[$clave]))
						{
						
							$select_encuestas = " SELECT Encenc,Encese 
													FROM ".$wbasedato."_000049 
												   WHERE Enchis = '".$row['Habhis']."-".$row['Habing']."'
													 AND Encenc = '".$clave."'
													 AND Encese ='cerrado'";
							
							
							$resencuestas = mysql_query($select_encuestas,$conex);
							//---- se  consulta las evaluaciones ya realizadas del paciente
							
							if($rowencuestas = mysql_fetch_array($resencuestas))
							{
								// al menos una encuesta hecha
								$almenosuna=true;
								if($rowencuestas['Encese'] =='pendiente')
								{
									$classfondo ='warning';
								}
								else
								{
									$qtot =   "SELECT  Tottot 
												 FROM  ".$wbasedato."_000035  
												WHERE  Totcdo ='".$row['Habhis']."-".$row['Habing']."'
												  AND  Totcod ='".$clave."'";
									
									$restot = mysql_query($qtot,$conex);
									$aux = $aux."--".$qtot;
									/// se consultan las calificaciones de cada encuesta del paciente
									if($rowtot= mysql_fetch_array($restot))
									{
										$arraycalificacion[$clave]=$rowtot['Tottot'];
										if($rowtot['Tottot']*1 < 70)
										{
											$classfondo ='danger';
											$porcentajebajo ='si';
											$ganotodas=false;
										}
										else
										{
											$classfondo ='success';
										}
									}
									else
									{
										$classfondo ='danger';
										$porcentajebajo ='si';
									}
									
								}
							}
							else
							{
								// si ya las hizo todas esto es deberia ser true como no encuentra en la consulta se viene por aca
								$yarealizadas=false;
							}
							
							if($porcentajebajo=='si')
							{
								$classfondo = 'danger';
							}
							
							if(!$yarealizadas)
								$classfondo = 'warning';
							
							if($yarealizadas and $ganotodas )
							{
								$classfondo = 'success';
							}
							
							if(!$almenosuna)
								$classfondo = 'info';
							
							$k++;
							if (is_int ($k/2))
							{
								$wcf="fila1";  // color de fondo de la fila
							}
							else
						    {
								$wcf="fila2"; // color de fondo de la fila
						    }
							
							
							
							
							$nombreEncuestas .='<li>
													<div class="text-small " style="border-bottom:1px dashed #BFBFBF;">&nbsp;
														
														<span >
															<i class="fa fa-edit"></i>&nbsp;'.$value.'
														</span>
															<div class="pull-right calificacionprueba_'.$row['Habhis'].'-'.$row['Habing'].'" id="prueba_'.$row['Habhis'].'-'.$row['Habing'].'_'.$clave.'" >'.(($arraycalificacion[$clave]) ? $arraycalificacion[$clave].'%' : '').'&nbsp;</div>&nbsp;
															<span class="pull-right">
														</span>
													</div>
													
												</li>';
						}
					}
					
					$nombreCompleto='';
					$nombreCompleto= $row['Pacno1'].' '.$row['Pacno2'].' '.$row['Pacap1'].' '.$row['Pacap2']; 
					$nombre = '';
					if( $row['Pacno1']== '' AND $row['Pacno2'] =='' AND $row['Pacap1']=='' AND $row['Pacap2']=='' )
					{
						$nombre = 'SIN NOMBRE';
					}
					else
					{
						if( ($row['Pacno1']!= '' OR  $row['Pacno1']!= '.')  AND ($row['Pacap1']!='' OR $row['Pacap1']!='.') )
								$nombre = $row['Pacno1'].' '.$row['Pacap1']; 
						else
								$nombre = $row['Pacno2'].' '.$row['Pacap1'].' '.$row['Pacap2'];
					}
					$html.='
			
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<div id="panelppal_'.$row['Habhis'].'-'.$row['Habing'].'" class="panel panel-'.$classfondo.'" style="cursor:pointer;" historia="'.$row['Habhis'].'"  wtipoempresa="no"  wempresa="'.$row['Ingcem'].'"  wcco="'.$row['Habcco'].'"   habitacion="'.$row['Habcpa'].'" ingreso="'.$row['Habing'].'"  nombre="'.$nombreCompleto.'"  documento="'.$row['Oriced'].'">
										<!-- este cierra -->
										<div class="panel-heading"><h1><strong><p align="center"  style="color: #5d5e60;">'.substr($row['Habcpa'], 0, 16).'</p></strong></h1></div>
											
											<div class="x_panel">
												<!--div content-->
												<div class="x_content">
													<!--abrir ul-->
													<ul class="list-unstyled">
													  <li class="media event">
														<a class="pull-left border-aero profile_thumb">
														  <i class="fa fa-user aero"></i>
														</a>
														<div class="media-body">
														  <h3><strong>'.substr($nombre, 0, 17).'</strong></h3>
														</div>
													  </li>
													 '.$nombreEncuestas.'
													</ul>
												  <!--cierro ul-->
												</div>
												<!--div x_content-->
											</div>
											<!--div x_panel-->
								</div>
						</div>';
								
				}
				
				$html .='</div></div>';
				//	cierro div
				
									
			}						
			else
			{
				$html .="<div>No tiene asignado ningun permiso para acceder a los maestros de Matrix</div>";
			}
			

			
			$html.='
			<!-- Abro modalEncuesta -->
			<div id="modalEncuesta" class="modal fullscreen-modal fade" tabindex="-1" role="dialog"  >
			
			<!-- Abro modal fade -->
			<div class="modal fade bs-example-modal-lg in" style="display: block;">
			<!-- Abro modal_dialog -->
			
					<!-- Abro x_panel -->
					<div class="x_panel">
			  
						 
			  
						  <!--Abro modal-content-->
						  <div class="modal-content">
							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">×</span>
								</button>
							  </div>
							  <div class="x_content" id="contenidoModal"></div>
						  </div>
						  <!--cierro modal-content-->
					</div>
					<!-- Cierro x_panel -->
			
			<!-- cierro modal_dialog -->
		  </div>
		   <!-- Cierro modal fade -->
		  </div>
		  <!-- Cierro modalEncuesta -->
		  ';
		echo $html;
			
		}
		break;
		// traigo las encuenstas correspondientes a cada paciente
		case "traerEncuestas":
		{
			$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'evapac');
			$wnumprueba ='1' ; //estipular el numero de la prueba 
			$select = "SELECT ".$wbasedato."_000060.Enccod, ".$wbasedato."_000060.Encdes, ".$wbasedato."_000060.Encvid, ".$wbasedato."_000060.Encenc, ".$wbasedato."_000060.Encgen,".$wbasedato."_000060.Encest 
				         FROM ".$wbasedato."_000060 LEFT JOIN ".$wbasedato."_000049 ON (".$wbasedato."_000060.Encenc = ".$wbasedato."_000049.Encenc AND ".$wbasedato."_000049.Enchis='".$historia."-".$ingreso."' ) 
						 WHERE  ((".$wbasedato."_000060.Enccco='".$wcco."' AND Encgen ='off' AND ".$wbasedato."_000060.Encest !='off' AND ".$wbasedato."_000060.Encdia ='*' AND Enchfo ='*' AND Enchca ='*' AND Enchva ='*'  AND ".$wbasedato."_000060.Enctem ='*' AND ".$wbasedato."_000060.Encemp ='*' AND ".$wbasedato."_000060.Encafi ='*' ))
						    OR  ( ".$wbasedato."_000060.Encemp ='".$wempresa."' AND Encgen ='off' AND ".$wbasedato."_000060.Encest !='off' AND ".$wbasedato."_000060.Encdia ='*' AND Enchfo ='*' AND Enchca ='*' AND Enchva ='*' AND ".$wbasedato."_000060.Enccco ='*' AND ".$wbasedato."_000060.Enctem ='*'  AND ".$wbasedato."_000060.Encafi ='*' )
							OR  ( Encgen ='on' AND ".$wbasedato."_000060.Encest !='off' AND ".$wbasedato."_000060.Encdia ='*' AND Enchfo ='*' AND Enchca ='*' AND Enchva ='*' AND ".$wbasedato."_000060.Enccco ='*' AND ".$wbasedato."_000060.Enctem ='*' AND ".$wbasedato."_000060.Encemp ='*' AND ".$wbasedato."_000060.Encafi ='*')
							OR  (".$wbasedato."_000049.Encenc !='null')
							GROUP by ".$wbasedato."_000060.Enccod";
			
			
			$res = 	mysql_query($select,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
			$r=0;
			$encabezadotabs ='';
			$tabs ='';
			
			
			
			
			
			while($row = mysql_fetch_array($res))
			{
			
				$encuesta = $row['Encenc'];
				$encabezadotabs .='<li><a href="#div_'.$row['Enccod'].'" data-toggle="tab"><h2>'.utf8_encode($row['Encdes']).'</h2></a></li>';
				
				$selectpreguntas = "SELECT Forgco,Forcom,Fordes,Forord ,Desdes,Destip,Desngr,Descod,".$wbasedato."_000006.id
								      FROM ".$wbasedato."_000006 , ".$wbasedato."_000005 
									 WHERE Forfor ='".$encuesta."'
									   AND Forest ='on'
									   AND Fordes = Descod
									   AND Desest != 'off'
									   LIMIT 10";
				
				$respreguntas	 = 	mysql_query($selectpreguntas,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$selectpreguntas." - ".mysql_error());
				$conteopreguntas = mysql_num_rows( $respreguntas );
				
				$tabs.='<div class="tab-pane" id="div_'.$row['Enccod'].'">
							<div><div class="col-lg-9 col-md-9 col-sm-9 col-xs-9"><h2>'.utf8_encode($row['Encdes']).'</h2></div><div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><button id="buttonVideo_'.$encuesta.'" type="button" class="btn btn-primary btn-lg pull-right" onclick="verVideo('.$encuesta.')" style ="display : none">Ver Video</button></div></div>
								
								<br><center><div id="frameEncuesta_'.$encuesta.'" ><iframe width="600" height="338" src="'.$row['Encvid'].'"   frameborder="0" allowfullscreen></iframe><input type="hidden" id="numeropreguntas_'.$encuesta.'" value="'.$conteopreguntas.'"></center>
							<br>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" ><button id="buttonEncuesta_'.$encuesta.'" type="button" class="btn btn-primary btn-lg pull-left" onclick="verEncuesta('.$encuesta.')">Realizar Encuesta</button></div>';
							
				 
				 
				$tabs.='<div>';	
				//--------Traer las preguntas
				
				$pasos='';
				$preguntas ='';
				$r=1;
				$isdone = 0;
				//-----------------------------
				
				//while de las preguntas 
				while($rowpreguntas = mysql_fetch_array($respreguntas))
				{
					
					
					$pasos.='<li><a href="#step-'.$encuesta.'-'.$r.'">'.$r.'</a></li>';
					$tipo_pregunta ='';
					if ($rowpreguntas['Destip']=='04' )
					{
						
						$wnumprueba ='1' ; //estipular el numero de la prueba 
						//-----------
						// traigo las respuestas si existen
							$qexiste =  " SELECT Evacal 
											FROM ".$wbasedato."_000007 
										 WHERE Evafor = '".$rowpreguntas['id']."'
										   AND Evaevo = '".$historia."-".$ingreso."' 
										   AND Evaevr = '".$wbasedato."'
										   AND Evanup = '1'
										   AND Evaper = '01' ";
								   
							$resexiste = mysql_query($qexiste,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qexiste." - ".mysql_error());
							//$row = mysql_fetch_array($res);
							$calificacionanterior = '';
							if($rowexiste = mysql_fetch_array($resexiste))
							{
								$calificacionanterior = $rowexiste['Evacal'];
								
							}
							

						//------------
						
						//-- Tipo de descriptor Escala
						//-- Toma sus datos de la tabla 000049
						$vectorescala = array();
						$qescala = 		   "  SELECT 	Notcod, Notdes, Notval, Notima, Notcar  
												FROM  ".$wbasedato."_000047 
											   WHERE  Notgru = '".$rowpreguntas['Desngr']."' 
										    ORDER BY  Notord ";

						$resescala = mysql_query($qescala,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qescala." - ".mysql_error());

						while($rowescala =mysql_fetch_array($resescala))
						{
							$vectorescala[$rowescala['Notcod']]= array ('nombre'=> $rowescala['Notdes'],'valor'=> $rowescala['Notval'],'ruta'=> $rowescala['Notima'],'caracteristica'=> $rowescala['Notcar']);
						}

						$tipo_pregunta .='<input '.$habilitado.'  size="5" maxlength="3" style="text-align:center" type="hidden" name="text" id="'.$encuesta.'-'.$rowpreguntas["Forgco"].'-'.$rowpreguntas["Forcom"].'-'.$row1["Descod"].'" onchange="validacampo(this)" onblur="grabadato(this,\''.$rowpreguntas["id"].'\',\''.$wnumprueba.'\',\''.$numrow.'\',\''.$numrowcom.'\',\''.$Narr_gcompetencia[$i][1].'\')" onFocus="tomavaloractual(this)" value="'.$arr_calificaciones[''.$encuesta.'-'.$rowpreguntas["Forgco"].'-'.$rowpreguntas["Forcom"].'-'.$row1["Descod"].''].'">';
						$tipo_pregunta .='<table><tr>';
						$e=0;
						foreach ($vectorescala as $key => $valor )
						{
						  if($e%2==0)
							$color='blue';
						  else
							$color='black';

						  if($valor['ruta']=='')
						  {
							 $tipo_pregunta .='<td name="tdtxt-'.$encuesta.'-'.$rowpreguntas["Forgco"].'-'.$rowpreguntas["Forcom"].'-'.$rowpreguntas["Descod"].'" align="center"  >&nbsp;&nbsp;'.$valor["nombre"].'&nbsp;&nbsp;</td>';
						  }
						  else
						  {
							  $tipo_pregunta .='<td name="tdtxt-'.$encuesta.'-'.$rowpreguntas["Forgco"].'-'.$rowpreguntas["Forcom"].'-'.$rowpreguntas["Descod"].'" >';
							  $tipo_pregunta .='<img width="32" height="33" src="'.$valor["ruta"].'" />';
							  $tipo_pregunta .='</td>';
						  }
						  $e++;
						}
						$tipo_pregunta .='</tr>';
						$tipo_pregunta .='<tr>';
						$t=1;
						foreach ($vectorescala as $key => $valor )
						{

						  if($calificacionanterior == $valor['valor'])
						  {
							  $condicion = 'checked="checked"';
						  }
						  else
						  {
							   $condicion = "";
						  }
								 
						  $tipo_pregunta .='<td align="center">';
						  $tipo_pregunta .=' <div class="radio">
												<label>			
												  <input type="radio"  required="" class="classRadio"  pregunta="'.$r.'" name="iCheck_'.$encuesta.'_'.$rowpreguntas["Descod"].'" onClick="ClicRadioEscala(\''.$rowpreguntas["id"].'\',\''.$valor["nombre"].'\' ,\''.$encuesta.'\',\''.$rowpreguntas['Forgco'].'\',\''.$rowpreguntas["Forcom"].'\',\''.$rowpreguntas["Descod"].'\',\''.$valor['valor'].'\')"  '.$condicion.'  >  
												</label>
											 </div>';
						  $tipo_pregunta .='</td>';
						  $t++;

						}

						$tipo_pregunta .='</tr></table>';
					}
					else if ($rowpreguntas['Destip']=='06')
					{
						
						$wnumprueba ='1' ; //estipular el numero de la prueba 
						//-----------
						// traigo las respuestas si existen
						$qexiste =  "   SELECT Evacal ,Evadat
									  FROM ".$wbasedato."_000007 
									 WHERE Evafor = '".$rowpreguntas['id']."'
									   AND Evaevo = '".$historia."-".$ingreso."' 
									   AND Evaevr = '".$wbasedato."'
									   AND Evanup = '1'
									   AND Evaper = '01' ";
							   
						$resexiste = mysql_query($qexiste,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qexiste." - ".mysql_error());
						//$row = mysql_fetch_array($res);
						$calificacionanterior = '';
						if($rowexiste = mysql_fetch_array($resexiste))
						{
							$calificacionanterior = $rowexiste['Evadat'];
							
						}
						
						//-- Campo tipo Seleccion Multiple
						//-- Trae sus valores de la tabla 000047
						$vectorescala = array();
						$qescala = "  SELECT 	Notcod, Notdes, Notval, Notima, Notcar  "
								 . "    FROM  ".$wbasedato."_000047 "
								 . "   WHERE  Notgru = '".$rowpreguntas['Desngr']."' "
								 . "ORDER BY  Notord ";

						$resescala = mysql_query($qescala,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qescala." - ".mysql_error());
						
						//$tipo_pregunta .= $qescala;
						
						while($rowescala =mysql_fetch_array($resescala))
						{
							$vectorescala[$rowescala['Notcod']]= array ('nombre'=> $rowescala['Notdes'],'valor'=> $rowescala['Notval'],'ruta'=> $rowescala['Notima'],'caracteristica'=> $rowescala['Notcar'],'codigo'=> $rowescala['Notcod']);
						}
						
							
						$tipo_pregunta .= "<table align='left'>";
						$e=0;
						
						foreach ($vectorescala as $key => $valor )
						{

						  if($calificacionanterior == $valor['nombre'])
						  {
							  $condicion = 'checked="checked"';
						  }
						  else
						  {
							   $condicion = "";
						  }
						  
						  if(utf8_encode($valor['nombre'])!='')
						  {
							  $tipo_pregunta .='<tr align="left"><td>';
							  $tipo_pregunta .='<label>
													  <p><input type="radio"  pregunta="'.$r.'" name="iCheck_'.$encuesta.'_'.$rowpreguntas["Descod"].'" onClick="ClicRadioEscala(\''.$rowpreguntas["id"].'\',\''.utf8_encode($valor["nombre"]).'\' ,\''.$encuesta.'\',\''.$rowpreguntas['Forgco'].'\',\''.$rowpreguntas["Forcom"].'\',\''.$rowpreguntas["Descod"].'\',\''.$valor['valor'].'\')"  '.$condicion.'  >&nbsp;'.utf8_encode($valor['codigo']).'&nbsp;<em>'.utf8_encode($valor['nombre']).'</em></p>
												</label>
												 ';
							  $tipo_pregunta .='</td></tr>';
							  $t++;
						  }

						}

						$tipo_pregunta .= "</table>";
						

					}
					
					
					
					$preguntas .='<div id="step-'.$encuesta.'-'.$r.'" class="">
									<h1><p class="lead">'.$r.'-&nbsp;'.utf8_encode($rowpreguntas['Desdes']).'</p></h1>
									<h2><small>'.$tipo_pregunta.'</small></h2>
									
								
								  </div>';
					$r++;
					
				}
				
				
				$tabs.='<br><div class="container contenedorwizardppal" id="contenedorwizardppal_'.$encuesta.'">
						<!-- SmartWizard html -->
						<div id="smartwizard_'.$encuesta.'" class="contenedorwizard">
							<ul>
								'.$pasos.'
							</ul>
							
							<div>								
								'.$preguntas.' 
																
									
									
									
								<br> 
								<br> 
								<br> 
								
							</div>
						</div> 
					</div>';
					
				//antes el boton de terminar encuesta estaba dentro de la zon de las preguntas
				//<button id="buttonCerrarEncuesta_'.$encuesta.'" type="button" class="btn btn-warning btn-lg pull-right" onclick="cerrarEncuesta('.$encuesta.')">Terminar</button>
				$tabs.='</div></div>';	
				$encuesta++;
				
			}	
			
			//--------
			
			$lootro ='';
			//----------
			$arr_html['encabezado']	= $encabezadotabs;
			$arr_html['tabs']		= $tabs;
			$arr_html['lootro']		= $lootro;
			
			echo json_encode($arr_html);
			
			
		}
		break;
		
		case 'grabaPregunta':
		{
			$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'evapac');
			//$valor ='1';
			$fecha = date("Y-m-d");
			$hora = date("H:i:s");
			$wano =  substr($fecha, 0, 4);
			if($valor!="")
			{
				
				//  primero miro si hay  ya respuesta para esta pregunta.
				$q =  " SELECT COUNT(Evafor) AS sumatoria
						  FROM ".$wbasedato."_000007 
						 WHERE Evafor = '".$id06."'
						   AND Evaevo = '".$historia."-".$ingreso."' 
						   AND Evaevr = '".$wbasedato."'
						   AND Evanup = '1'
						   AND Evaper = '01' ";
				
				//echo $q;
				
				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row = mysql_fetch_array($res);

				if($row['sumatoria']==0)
				{

					echo "Inserto";

					$q	= 	 " INSERT INTO ".$wbasedato."_000007  (Evaevo,Evafor,Evafco,Evagco,Evacom,Evades,Evanup,Evaevr,Evafec,Evacal,Evaper,Evaano,Evadat,Medico,Fecha_data,Hora_data,Seguridad) "
							." VALUES ('".$historia."-".$ingreso."', "
							."         '".$id06."' , "
							."         '".$encuesta."' , "
							."         '".$grupocompetencia."' , "
							."         '".$competencia."' , "
							."         '".$descriptor."' , "
							."         '1' , "
							."         '".$wbasedato."',"
							."         '".$fecha."',"
							."         '".$valor."' ,"
							."         '01',"
							."         '".$wano."',"
							."         '".$rotulo."',"
							."         '".$wbasedato."',"
							."         '".$fecha."',"
							."         '".$hora."',"
							."		   'C-".$wbasedato."')";
					//echo $q;
					
					$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				}
				else
				{
					 echo "modifico";
					 echo $q = "UPDATE ".$wbasedato."_000007 "
						. "   SET Evacal= '".$valor."', "
						. "       Evadat= '".$rotulo."' "
						. " WHERE Evafor = '".$id06."' "
						. "   AND Evaevo = '".$historia."-".$ingreso."'"
						."    AND Evaevr = '".$wbasedato."'"
						. "   AND Evanup = '1' "
						."    AND Evaano = '".$wano."' "
						."    AND Evaper = '01' ";

					 
					$res = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					//echo $q;

				}
			}
			
		}
		break;
		
		case 'terminarEncuesta' :
		{
				$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'evapac');
				//--------------
				// Calcular el total de la encuesta
				//---------- Se tienen en cuenta las preguntas de tipo 06 que son las cuantificables de la evaluacion
				$select ="SELECT Destip, Evanup ,	Evacal 
							FROM ".$wbasedato."_000007 ,  ".$wbasedato."_000005 
						   WHERE  Evaevo ='".$historia."-".$ingreso."' 
							 AND  Evaevr ='".$wbasedato."'	
							 AND  Evafco ='".$encuesta."' 
							 AND  Descod = Evades ";
				
				$res = mysql_query($select,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
				
				$conteo=0;
				$conteobuenas=0;
				while($row = mysql_fetch_array($res))
				{
					 if($row['Destip'] =='06')
					 {
						if($row['Evacal'] ==1)
						{
						  $conteobuenas++;
						}
						$conteo++; 
					 }
				}
				
				// calculo el porcentaje
				$porcentaje = ($conteobuenas/$conteo) * 100 ;
				//echo $select;
				//------------------
				
				$selecttipo  = "SELECT  Fortip 
								  FROM  ".$wbasedato."_000002 
								 WHERE  Forcod ='".$encuesta."'";
				$res = mysql_query($selecttipo,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$selecttipo." - ".mysql_error());
				
				$conteo=0;
				$conteobuenas=0;
				while($row = mysql_fetch_array($res))
				{
					$tipo = $row['Fortip'];
				}
				
				
				//echo "total preguntas: ".$conteo." buenas:".$conteobuenas." porcentaje ".$porcentaje;
				
				// Cambiar
				
				//$valor ='1';
				$fecha = date("Y-m-d");
				$hora = date("H:i:s");
				$wano =  substr($fecha, 0, 4);
				
				$select = "SELECT * 
							 FROM ".$wbasedato."_000032   
							WHERE Mcauco = '".$historia."-".$ingreso."'
				              AND Mcafor = '".$encuesta."' ";
							  
				$res = mysql_query($select,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
				
				$conteo=0;
				$conteobuenas=0;
				//while($row = mysql_fetch_array($res))
				if($row = mysql_fetch_array($res))
				{
					    
						$q =   "UPDATE  ".$wbasedato."_000035  
								   SET  Tottot ='".$porcentaje."'
								 WHERE  Totcdo ='".$historia."-".$ingreso."'
								   AND  Totcod ='".$encuesta."'";
						
						$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			
					
						
						$q =   "UPDATE  ".$wbasedato."_000049  
								   SET  Encese ='cerrado'
								 WHERE  Enchis ='".$historia."-".$ingreso."'
								   AND  Encenc ='".$encuesta."'";
						
						$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			
				}
				else
				{
				
						$q	= 	 "INSERT INTO ".$wbasedato."_000032  (Mcaano,Mcaper,Mcaucr,Mcanpu,Mcafor,Mcauco,Mcatfo,Medico,Fecha_data,Hora_data,Seguridad) "
									." VALUES ('".$wano."', "
									."         '01' , "
									."         '".$wbasedato."' , "
									."         '1' , "
									."         '".$encuesta."' , "
									."         '".$historia."-".$ingreso."' , "
									."         '".$tipo."' ,
											   '".$wbasedato."' ,
											   '".$fecha."',
											   '".$hora."' ,
											   'C-".$wbasedato."' ) ";

						$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						//echo $q;
						
						$q	= 	 " INSERT INTO ".$wbasedato."_000035  (Totcdr,Totcdo,Totano,Totper,Tottip,Totcod,Tottot,Medico,Fecha_data,Hora_data,Seguridad) "
									." VALUES ('".$wbasedato."', "
									."         '".$historia."-".$ingreso."' , "
									."         '".$wano."' , "
									."         '01' , "
									."         'formulario' , "
									."         '".$encuesta."' , "
									."         '".$porcentaje."' ,"
									."         '".$wbasedato."' ,"
									."		   '".date('Y-m-d')."', "
									."		   '".$hora."' ,"
									."         'C-".$wbasedato."' ) ";

						$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						//echo $q;
						
						$q =   "UPDATE  ".$wbasedato."_000049  
								   SET  Encese ='cerrado'
								 WHERE  Enchis ='".$historia."-".$ingreso."'
								   AND  Encenc ='".$encuesta."'";
						
						$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				}
				
				
				$vector_resultados = array();
				$qtot =   "SELECT  Tottot ,Totcod
							 FROM  ".$wbasedato."_000035  
							WHERE  Totcdo ='".$historia."-".$ingreso."'";
										
				$restot = mysql_query($qtot,$conex);
				/// se consultan las calificaciones de cada encuesta del paciente
				while($rowtot= mysql_fetch_array($restot))
				{
					$vector_resultados[$rowtot['Totcod']]=$rowtot['Tottot'];
				}
				echo json_encode($vector_resultados);
				
		}
		
		break;
		case 'insertar_encuesta':
		{
			
			$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'evapac');
			$q = " INSERT INTO ".$wbasedato."_000049 "
			   . "            ( Encced		,Encenc				,Enchis							,Encing			,Encno1				,Encno2			,Encap1			,Encap2			,Enceda				,Encent				,Encdia	,Enctel				,Enchab			,Encafi			,Enccco			,Encfec				,Encest		,Seguridad , Medico) "
			   . "      VALUES('".$wced."'	,'".$encuesta."'	,'".$historia."-".$ingreso."'	,'".$ingreso."'	,'".$wno1."'		,'".$wno2."'	,'".$wap1."'	,'".$wap2."'	,'".$wedad."'		,'".$wentidad."'	,'nose'	,'".$wtelefono."'	,'".$whcod."'	,'".$wtpa."'	,'".$wcco."'	,'".$wfechaing."'	,'on'		,'C-".$wbasedato."', '".$wbasedato."')" ;

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
			//echo $q;
			
			
			
		}
		break;
	}
	return;

}



?>
 <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
				$user_session = explode('-',$_SESSION['user']);
				$user_session = (count($user_session) > 1)? $user_session[1] : $user_session[0];
				//$user_session = (strlen($user_session) > 5) ? substr($user_session,-5): $user_session;


				$wusuario = $user_session;
				$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'evapac');
				$wbasedatoMov 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
				//$wbasedatoRoot 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'root');
				$html .="<input type='hidden' id='wusuariotabla' value='".$wusuario."'>";
				$html .="<input type='hidden' id='wbasedatoMov' value='".$wbasedatoMov."'>";
				$html .="<input type='hidden' id='historia' value=''>";
				$html .="<input type='hidden' id='ingreso' value=''>";
				$html .="<input type='hidden' id='encuesta' value=''>";	
				$html .="<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";	
				$html.='<div class="right_col" role="main"  id="divmain"></div>';
				
				$vector = traer_cco();
				$html.= "<input type='hidden' id='hidden_cco' value='".json_encode($vector)."'>";
				//$html =  traerprogramappal($wemp_pmla);
				echo $html;



			?>
    </body>
</html>