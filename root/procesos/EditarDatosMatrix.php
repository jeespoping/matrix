<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Tarjeta de dispositivo medico implantable
 * Fecha		:	2015-10-04
 * Por			:	Felipe Alvarez Sanchez
 * Descripcion	:
 * Condiciones  :
 *********************************************************************************************************

 Actualizaciones:
 *  2021-01-14	: Leandro Meneses
 *				  Se crea la funcion obtener_tooltip_relacion_ne para generar un tool tip con la descripción sobre los datos de una relacion ne
 *				  Se modifica la funcion permisostabla para que genere el tooltip en los campos de tipo relacion ne
 *  2021-01-13	: Luis F Meneses
 *				  Se congela el encabezado de la tabla principal al efectuar scroll vertical en la ventana.
 *  2021-01-12	: Leandro Meneses
 *				  Se modifica el formato que visualiza y valida los campos tipo hora para que permita mas de 24 horas
 *				  Se elimina la validacion que no permite ingreso de horas entre 25 y 29 horas
 *  2020-09-22	: Se adiciona ORDER BY ORDINAL_POSITION al query que obtiene los campos de las tablas 
 *				  para evitar que no se muestren los campos en desorden en mysql 8
 *  2020-05-26   : Freddy Saenz
 *                 1. Grabar en el log , el estado de como se encontraba el registro antes de editarlo
 *                 2. Corregir : abrir_tabla hace llamado al metodo PermisosTabla que cuando se abre la primera vez la ventana
 *                  trae todos los registros de la tabla ,que hace que el navegador no pueda abrir la ventana debido
 *                  a la cantidad de informacion , se le agrega un LIMIT 30 a esta busqueda inicial .

 * 2016-08-30	: Se agrega utf8_decode() al insertar y actualizar los registros para que se guarden 
 *				  correctamente las tildes.
 **********************************************************************************************************/

$wactualiz = "2020-09-22";
$wactualiz = "2021-01-21";

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
	<title>Editar Datos</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/smartpaginator.css" rel="stylesheet" /> <!-- Autocomplete -->

	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

	<style>

		.tborder
		{
			/*border: solid black;*/
		}
		.visibilidad
		{
			display:none;
		}

		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}

		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 16pt;
		}

		.ui-autocomplete{
			max-width: 	250px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}

		// --> Estilo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		/*Firefox*/
		[tipo=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}

        // Estilos para congelar encabezado de tabla en laparte superior al hacer scroll en la ventana.
        th, td {
            padding: 0.25rem;
        }        
        th {
            position: sticky !important;
            top: 0 !important; /* requerido para stickiness */
            background-color: #2a3ab0;
        }
	</style>
	<script>


	</script>
<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		cargar_datapicker();
		$( "#accordionopciones" ).accordion({
				collapsible: true,
				heightStyle: "content"
			});
		$(".msg_ne").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });


	});
	

	/****************************************************************************************
	 * Esta funcion muestra un tooltip para las leyendas
	 ****************************************************************************************/
	function mostrarTooltip( celda ){

		if( !celda.tieneTooltip ){
			$( "*", celda ).tooltip();
			celda.tieneTooltip = 1;
		}
	}

function userDetails(){
	console.log('prueba2');
  return 'prueba2';
}
	//Funcionalidad para cuando se llama la opción desde una ventana de modal Mavila 23-10-2020 :)
	function abrir_tabla_modal(wemp_pmla, wtabla, wusuariotabla, wnombreopc, wid, widregistro, wcampo, woperacion){

		if	(wemp_pmla != ''){

		
			var i=0;
			var j=0;
			var parametros = new Array();
			var campos = new Array();
			var operaciones = new Array();


			// armando busqueda
			var verificacion = true ;
			parametros.push(widregistro);
			campos.push(wcampo);
			operaciones.push(woperacion);
			//------------------------------

			var busqueda='';
			for (k=0 ; k < campos.length ; k++){
				if (k==0){
					busqueda = busqueda +" "+campos[k]+" = '"+parametros[k]+"'";
				}else{
					busqueda = busqueda +" AND "+campos[k]+" = '"+parametros[k]+"'";
				}
			}
			
			$.post("EditarDatosMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        wemp_pmla,
				accion:           'abrir_tabla',
				tablappal:		  wtabla,
				wusuariotabla:	  wusuariotabla,
				wnombreopc:	  	  wnombreopc,    
				parametro: 		  widregistro,
				wbusqueda:        busqueda,
				campobuscar: 	  wcampo,
				wid:			  wid,
				campos:           JSON.stringify(campos),
				parametros:		  JSON.stringify(parametros),
				operaciones:	  JSON.stringify(operaciones)

			},function(data) {

				$("#divtabla").show();
				$("#primeradiv").hide();
				$("#div_nuevo_registro").html('');
				$("#div_nuevo_registro").remove();
				$("#divtabla").html(data.html);
				$("#oculta").html("");
				$("#oculta").html(data.oculto);
					//****************************************************************
					//* formatea hora para un campo
					//****************************************************************/
					//Masked input
					
					//Se cambia la mascara para que permita mas de 24 Horas 12-01-2021
					//$.mask.definitions['H']='[012]';
					$.mask.definitions['H']='[0123456789]';
					$.mask.definitions['N']='[012345]';
					$.mask.definitions['n']='[0123456789]';

					$(".hora1").mask("HH:Nn:Nn");


					//Se elimina la validaciocion que solo permite 24 horas 12-01-2021
					// $(".hora1").keyup(function(){

						// if ( $(this).val().substring(0,1) == "2" && $(this).val().substring(0,2)*1 > 23 )
						// {
							// $(this).val( "2_:__:__" );
							// $(this).caret(1);
						// }
					// });
					//---------------------------------------
					//---------------------------------------

					$('.entero').keyup(function(){
						if ($(this).val() !="")
						$(this).val($(this).val().replace(/[^0-9]/g, ""));
					});


					$('.real').focusout(function(){
						if ($(this).val() !=""){
							//$(this).val($(this).val().replace(/[^0-9|\.]/g, ""));
							var regEx = /(^[0]\.{1}[0-9]+$)|(^[1-9]+\.{1}[0-9]+$)|(^[1-9]+[0-9]*$)|(^[0]$)/;
							if ( regEx.test( $(this).val() ) == false )
							{

								$(this).val("");


							}
						}
					});

				if($("#cambioinicioyfin").val() =='on')
				{
					$("#mostrandodatos").html("Registros de: 1 a "+ $("#cambiofinal").val());
				}

				cuantos = data.cuantos ;
			}, 'json').done(function(){

				$('#paginador').smartpaginator({

					totalrecords: cuantos,
					recordsperpage: 30,
					length: 7,
					next: 'Sig',
					prev: 'Atras',
					first: 'Inicio',
					last: 'Ulti',
					go: 'Ir',
					theme: 'black',
					controlsalways: true,
					onchange:

					function (newPage) {

							$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
									css: 	{
												width: 	'auto',
												height: 'auto'
											}
							});

							$.ajax({
								url: "EditarDatosMatrix.php",
								type: "POST",
								data:{

									consultaAjax: 		'',
									wemp_pmla:      	$('#wemp_pmla').val(),
									accion:           	'mostrar_registros',
									wtabla:				wtabla,
									wusuario:			$("#wusuariotabla").val(),
									parametro: 			$("#parametro_buscar").val(),
									campobuscar: 		$("#select_buscador_campo").val(),
									wprincipio:			(newPage*30) - 30,
									wfinal:				30,
									wnombreopc:	  	    $("#wtituloopcion").val(),
									wid:				id

								},
								async: false,
								dataType: "json",
								success:function(data_json) {

									if (data_json.error == 1)
									{
										alert(data_json.mensaje);
										return;
									}
									else{

										$('#datos').html(data_json.html);
										$.unblockUI();
									}
								}
							});

					}
				});


			});
		}



	}

	function abrir_tabla(wtabla,nombreopc,id)
	{
		
		$.post("EditarDatosMatrix.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'abrir_tabla',
			tablappal:		  wtabla,
			wusuariotabla:	  $("#wusuariotabla").val(),
			wnombreopc:	  	  nombreopc,
			wid:			  id

		},function(data) {

			$("#divtabla").show();
			$("#primeradiv").hide();
			$("#div_nuevo_registro").html('');
			$("#div_nuevo_registro").remove();
			$("#divtabla").html(data.html);
			$("#oculta").html("");
			$("#oculta").html(data.oculto);
				//****************************************************************
				//* formatea hora para un campo
				//****************************************************************/
				//Masked input

				//Se cambia la mascara para que permita mas de 24 Horas 12-01-2021 
				//$.mask.definitions['H']='[012]';
				$.mask.definitions['H']='[0123456789]';
				$.mask.definitions['N']='[012345]';
				$.mask.definitions['n']='[0123456789]';
				$(".msg_ne").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });


				$(".hora1").mask("HH:Nn:Nn");

				//Se elimina la validaciocion que solo permite 24 horas 12-01-2021
				// $(".hora1").keyup(function(){

					// if ( $(this).val().substring(0,1) == "2" && $(this).val().substring(0,2)*1 > 23 )
					// {
						// $(this).val( "2_:__:__" );
						// $(this).caret(1);
					// }
				// });
				//---------------------------------------
				//---------------------------------------

				 $('.entero').keyup(function(){
					if ($(this).val() !="")
					$(this).val($(this).val().replace(/[^0-9]/g, ""));
				 });


				$('.real').focusout(function(){
					if ($(this).val() !=""){
						//$(this).val($(this).val().replace(/[^0-9|\.]/g, ""));
						var regEx = /(^[0]\.{1}[0-9]+$)|(^[1-9]+\.{1}[0-9]+$)|(^[1-9]+[0-9]*$)|(^[0]$)/;
						if ( regEx.test( $(this).val() ) == false )
						{

							$(this).val("");


						}
					}
				});

			if($("#cambioinicioyfin").val() =='on')
			{
				$("#mostrandodatos").html("Registros de: 1 a "+ $("#cambiofinal").val());
			}

			cuantos = data.cuantos ;
		}, 'json').done(function(){

			$('#paginador').smartpaginator({

				totalrecords: cuantos,
				recordsperpage: 30,
				length: 7,
				next: 'Sig',
				prev: 'Atras',
				first: 'Inicio',
				last: 'Ulti',
				go: 'Ir',
				theme: 'black',
				controlsalways: true,
				onchange:

				function (newPage) {

						$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });

						$.ajax({
							url: "EditarDatosMatrix.php",
							type: "POST",
							data:{

								consultaAjax: 		'',
								wemp_pmla:      	$('#wemp_pmla').val(),
								accion:           	'mostrar_registros',
								wtabla:				wtabla,
								wusuario:			$("#wusuariotabla").val(),
								parametro: 			$("#parametro_buscar").val(),
								campobuscar: 		$("#select_buscador_campo").val(),
								wprincipio:			(newPage*30) - 30,
								wfinal:				30,
								wnombreopc:	  	    $("#wtituloopcion").val(),
								wid:				id

							},
							async: false,
							dataType: "json",
							success:function(data_json) {

								if (data_json.error == 1)
								{
									alert(data_json.mensaje);
									return;
								}
								else{

									$('#datos').html(data_json.html);
									$.unblockUI();
								}
							}
						});

				}
            });


		});




	}
	
	
	
	function agregar_filtro ()
	{
		
		
		var r= $("#table_filtros tr").length; /* To get the total rows in the table */
		if((r-2) < ($("#tr_ppal_0").find($('#select_buscador_campo option')).size() - 1) )
		{
		
			var select 		= $("#selectppal").html();
			var parametro 	= $("#td_parametro_buscar").html();
			var operacion   = $("#td_operacion").html();
			$("#table_filtros tr").eq(r-2).after('<tr class="fila1" id="tr_ppal_'+(r-2)+'" ><td></td><td >'+select+'</td><td>'+operacion+'</td><td>'+parametro+'</td><td><input type="button" onclick="eliminar_filtro($(this).parent().parent())" value="-" title="Eliminar filtro"></td></tr>');
			//alert(r-2);
			/*$("#tr_ppal_"+(r-2)).find($('#select_buscador_campo')).val('');
			$("#tr_ppal_"+(r-2)).find($('#select_operacion')).val('igual');
			$("#tr_ppal_"+(r-2)).find($('#select_operacion')).val('');*/
			$( "#select_buscador_campo", $( "#tr_ppal_"+(r-2) ) ).val('');
			$( "#select_operacion", $( "#tr_ppal_"+(r-2) ) ).val('');
			$( "#parametro_buscar", $( "#tr_ppal_"+(r-2) ) ).val('');
		}
		else
		{
			alert("Supero el Maximo de Filtros Posible");
		}
	}
	
	function  eliminar_filtro(n)
	{
		$(n).remove();
		
	}

	function  cerrar_ventana()
	{
		top.close();
	}
	
	
	function crear_autocomplete(campo,HiddenArray,codIni,nomIni,consulta,where)
	{

		//----
		// llenar el campo autocompletar
		
		$.post("EditarDatosMatrix.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'llenarcampoAutocompletar',
			consulta:		  consulta,
			where:		 	  where,
			term:			  codIni

		},function(data) {

			$("#"+campo).val(data);
			$("#"+campo).attr("valor",codIni);
			$("#"+campo).attr("nombre",data);


		});
		//--------------------------------------------
		
			
			$( "#"+campo ).autocomplete({
				minLength: 	2,
				source: 	"EditarDatosMatrix.php?consultaAjax=''&wemp_pmla="+$('#wemp_pmla').val()+"&accion=consulta_buscador&consulta="+consulta+"&where="+where+"",
				select: 	function( event, ui ){
					//alert(JSON.stringify(ui));
					$( "#"+campo ).val(ui.item.label);
					//alert("label :"+ui.item.label+"valor :"+ui.item.cod+"nombre :"+ui.item.name);
					$( "#"+campo ).attr('valor', ui.item.cod);
					$( "#"+campo ).attr('nombre', ui.item.name);
					
					return false;
				}
			});
			


	}
	
	
		function crear_autocomplete(campo,HiddenArray,codIni,nomIni,consulta,where)
	{

		//----
		// llenar el campo autocompletar
		
		$.post("EditarDatosMatrix.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'llenarcampoAutocompletar',
			consulta:		  consulta,
			where:		 	  where,
			term:			  codIni

		},function(data) {

			$("#"+campo).val(data);
			$("#"+campo).attr("valor",codIni);
			$("#"+campo).attr("nombre",data);


		});
		//--------------------------------------------
		
			
			$( "#"+campo ).autocomplete({
				minLength: 	2,
				source: 	"EditarDatosMatrix.php?consultaAjax=''&wemp_pmla="+$('#wemp_pmla').val()+"&accion=consulta_buscador&consulta="+consulta+"&where="+where+"",
				select: 	function( event, ui ){
					//alert(JSON.stringify(ui));
					$( "#"+campo ).val(ui.item.label);
					//alert("label :"+ui.item.label+"valor :"+ui.item.cod+"nombre :"+ui.item.name);
					$( "#"+campo ).attr('valor', ui.item.cod);
					$( "#"+campo ).attr('nombre', ui.item.name);
					
					return false;
				}
			});
			


	}
	
	
	function crear_autocomplete9(campo,HiddenArray,codIni,nomIni)
	{

		//alert("entro");
		$("#"+campo).val(nomIni);
		$("#"+campo).val(codIni);
		$("#"+campo).attr("valor",codIni);
		$("#"+campo).attr("nombre",nomIni);
		// alert(nomIni);
		// alert(codIni);
		//alert("pongo valor inicial");
		// alert(HiddenArray);
		var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
		
		//--Consulto si tiene el atributo de cuantos y si lo tiene miro cual es su valor y dependiendo de eso establesco la propiedad del autocompletar
		//--minlength
		if( ($("#"+HiddenArray).attr('cuantos')*1)> 1000 )
		{
			var minimoparabusqueda = 5;
		}
		else
		{
			var minimoparabusqueda = 0;
		}
		
		
		//--

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
				minLength: 	minimoparabusqueda,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+campo ).val(ui.item.label);
					$( "#"+campo ).attr('valor', ui.item.value);
					$( "#"+campo ).attr('nombre', ui.item.name);

					return false;
				}
			});			
			/*
			$( "#"+campo ).autocomplete({
				minLength: 	1,
				source: 	"EditarDatosMatrix.php?consultaAjax=''&wemp_pmla="+$('#wemp_pmla').val()+"&accion=consulta_buscador&consulta="+consulta+"&where="+where+"",
				select: 	function( event, ui ){
					//alert(JSON.stringify(ui));
					$( "#"+campo ).val(ui.item.label);
					$( "#"+campo ).attr('valor', ui.item.cod);
					$( "#"+campo ).attr('nombre', ui.item.name);
					
					return false;
				}
			});
			*/


	}


	function cargar_datapicker()
	{
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
	}

	function onclickcheckgrabar(id)
	{

		if($("#new_input_"+id+":checked").length ==1)
		{
			$("#divcheck_"+id).html('on');

		}
		else
		{
			$("#divcheck_"+id).html('off');

		}



	}

	// Funcion que pone los campos de texto (areas de texto) para asi editar el valor de los campos
	function editar(id)
	{

		$("#tr_"+id+" td").each(function() {

				var campo = $(this).attr('editar');
				if(campo =="si")
				{

					//---si se edita la relacion Ne
					if($(this).attr('tipo')=='18' )
					{

						//alert("entro");
						
						var nombre = 'imput_'+$(this).attr("nombrecampo")+'_'+id;
						var HiddenArray = 'hidden_'+$(this).attr("nombrecampo");
						
						$(this).html('<input id="imput_'+$(this).attr("nombrecampo")+'_'+id+'" type="text">');
						
						// el ultimo parametro es la consulta
						var consulta = $("#"+HiddenArray+"").val();
						var where    = $("#"+HiddenArray+"").attr('where') ;
						
						//campo,HiddenArray,codIni,nomIni,consulta,where
						//alert($(this).attr('valor'));
						
						
						crear_autocomplete(nombre,HiddenArray,$(this).attr('valor'),$(this).attr('nombre'), consulta, where);

					}
					else if($(this).attr('tipo')=='9' ) // si se edita  la relacion
					{
				
						var nombre = 'imput_'+$(this).attr("nombrecampo")+'_'+id;
						var HiddenArray = 'hidden_'+$(this).attr("nombrecampo");
						$(this).html('<input id="imput_'+$(this).attr("nombrecampo")+'_'+id+'" type="text">');
						crear_autocomplete9(nombre,HiddenArray,$(this).attr('valor'),$(this).attr('nombre'));

					}
					else if($(this).attr('tipo')=='5' ) // si se edita una seleccion
					{

						var nombre = "imput_"+$(this).attr("nombrecampo")+"_"+id;
						var selectselecciones = "<select id='imput_"+$(this).attr("nombrecampo")+"_"+id+"'>"+$("#div_selecciones_"+$(this).attr("nombrecampo")).html()+"</select>";
						$(this).html(selectselecciones);
						$("#"+nombre).val($(this).attr('valor'));

					}
					else if ( $(this).attr('tipo') == '10') // si se edita un booleano
					{

						var booleano = $(this).attr('valor');

						if (booleano=='on')
						{
							$(this).html('on<input id="imput_'+$(this).attr("nombrecampo")+'_'+id+'" checked type="radio" name="tipoestado_'+$(this).attr("nombrecampo")+'_'+id+'" value="on">off<input type="radio" name="tipoestado_'+$(this).attr("nombrecampo")+'_'+id+'"  id="imput_'+$(this).attr("nombrecampo")+'_'+id+'" value="off">');
						}
						else
						{
							$(this).html('on<input id="imput_'+$(this).attr("nombrecampo")+'_'+id+'" type="radio" name="tipoestado_'+$(this).attr("nombrecampo")+'_'+id+'" value="on">off<input checked type="radio" name="tipoestado_'+$(this).attr("nombrecampo")+'_'+id+'"  id="imput_'+$(this).attr("nombrecampo")+'_'+id+'" value="off">');
						}

					}
					else if($(this).attr('tipo') == '3') // si se edita un tipo fecha
					{

						var texto 	= $(this).html();
						var texarea = "<input size='10' id='imput_"+$(this).attr('nombrecampo')+"_"+id+"'  value='"+texto+"' >";
						$(this).html(texarea);
						$("#imput_"+$(this).attr('nombrecampo')+"_"+id).datepicker({
							showOn: "button",
							buttonImage: "../../images/medical/root/calendar.gif",
							buttonImageOnly: true,
							defaultTime: 'now'
						}).attr("disabled","disabled");

					}
					else if( $(this).attr('tipo') == '11') // si se edita un campo hora
					{

							var texto 	= $(this).html();
							var texarea = "<input class='hora' id='imput_"+$(this).attr('nombrecampo')+"_"+id+"' value='"+texto+"' >";
							$(this).html(texarea);


					}
					else if( $(this).attr('tipo') == '1') // si se edita un tipo entero
					{

							var texto 	= $(this).html();
							var texarea = "<input class='entero'  size='8' id='imput_"+$(this).attr('nombrecampo')+"_"+id+"' value='"+texto+"' >";
							$(this).html(texarea);


					}
					else if( $(this).attr('tipo') == '2') // si se edita un tipo Real
					{

							var texto 	= $(this).html();
							var texarea = "<input size='8' class='real' id='imput_"+$(this).attr('nombrecampo')+"_"+id+"' value='"+texto+"' >";
							$(this).html(texarea);


					}
					else if($(this).attr('tipo') == '6' || $(this).attr('tipo') == '7' || $(this).attr('tipo') == '8' || $(this).attr('tipo') == '12' || $(this).attr('tipo') == '13' || $(this).attr('tipo') == '14' || $(this).attr('tipo') == '15' || $(this).attr('tipo') == '16' || $(this).attr('tipo') == '17')
					{
						var texto 	= $(this).html();
						var texarea = "<div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)<br></div></div><textarea disabled='disabled' id='imput_"+$(this).attr('nombrecampo')+"_"+id+"' rows='2' cols='20' >"+texto+"</textarea>";
						$(this).html(texarea);
					}
					else
					{
						var texto 	= $(this).attr('valor');
						//alert(texto);
						var texarea = "<textarea id='imput_"+$(this).attr('nombrecampo')+"_"+id+"' rows='3' cols='20' >"+texto+"</textarea>";
						$(this).html(texarea);


					}

					if($(this).attr('nombrecampo')=='id' || $(this).attr('nombrecampo')=='Id' || $(this).attr('nombrecampo')=='ids' || $(this).attr('nombrecampo')=='Ids' || $(this).attr('nombrecampo')=='Fecha_data' || $(this).attr('nombrecampo')=='fecha_data' || $(this).attr('nombrecampo')=='Seguridad' || $(this).attr('nombrecampo')=='seguridad' || $(this).attr('nombrecampo')=='Hora_data' || $(this).attr('nombrecampo')=='hora_data' || $(this).attr('nombrecampo')=='Medico' || $(this).attr('nombrecampo')=='medico')
					{

						$(this).html('');
						var texarea = "<input size='10' id='imput_"+$(this).attr('nombrecampo')+"_"+id+"'  value='"+texto+"' >";
						$(this).html(texarea);
						var nombre = 'imput_'+$(this).attr('nombrecampo')+'_'+id;
						$("#"+nombre).attr("disabled","disabled");

					}

					//excepciones para los campos reservados de las tablas matrix



					$("#div_operacione_"+id).html("<div id='grabar_"+id+"' style='cursor: pointer;' title='Grabar' onclick='grabar("+id+")'><img  width='14' height='14' src='../../images/medical/root/grabar16.png' >&nbsp;<font style='color: #235a81'>Guardar</font></div><br><div id='ocultar_"+id+"' style='cursor: pointer;' title='cancelar' onclick='ocultar("+id+")'><img width='14' height='14'  src='../../images/medical/hce/cancel.PNG'>&nbsp;<font style='color: #235a81'>Cancelar</font></div>");
					$("#div_operacione2_"+id).html("<div id='grabar2_"+id+"' style='cursor: pointer;' title='Grabar' onclick='grabar("+id+")'><img  width='14' height='14' src='../../images/medical/root/grabar16.png' >&nbsp;<font style='color: #235a81'>Guardar</font></div><br><div id='ocultar2_"+id+"' style='cursor: pointer;' title='cancelar' onclick='ocultar("+id+")'><img width='14' height='14'  src='../../images/medical/hce/cancel.PNG'>&nbsp;<font style='color: #235a81'>Cancelar</font></div>");

				}
		});

		//****************************************************************
		//* formatea hora para un campo
		//****************************************************************/
		//Masked input
		
		//Se cambia la mascara para que permita mas de 24 Horas 12-01-2021 
		//$.mask.definitions['H']='[012]';
		$.mask.definitions['H']='[0123456789]';	
		$.mask.definitions['N']='[012345]';
		$.mask.definitions['n']='[0123456789]';

		$(".hora").mask("HH:Nn:Nn");

		//Se elimina la validaciocion que solo permite 24 horas 12-01-2021
		// $(".hora").keyup(function(){

			// if ( $(this).val().substring(0,1) == "2" && $(this).val().substring(0,2)*1 > 23 )
			// {
				// $(this).val( "2_:__:__" );
				// $(this).caret(1);
			// }
		// });



		$('.entero').keyup(function(){
			if ($(this).val() !="")
			$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});


		$('.real').focusout(function(){
			if ($(this).val() !=""){
				//$(this).val($(this).val().replace(/[^0-9|\.]/g, ""));
				var regEx = /(^[0]\.{1}[0-9]+$)|(^[1-9]+\.{1}[0-9]+$)|(^[1-9]+[0-9]*$)|(^[0]$)/;
				if ( regEx.test( $(this).val() ) == false )
				{
					$(this).val("");
					var aux = $(this);
					alert("El dato ingresado no es de tipo real");
					setTimeout(function(){ aux.focus();},500);
				}
			}
		});

	}

	function inicio()
	{
		var wprincipio 	= 0;
		var wfinal		= 30;
		$("#wprincipio").val(wprincipio);
		$("#wfinal").val(wfinal);

		$.post("EditarDatosMatrix.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'pasar_pagina',
			tablappal:		  $("#tablacompleta").val(),
			wprincipio:		  wprincipio,
			wfinal:	  		  wfinal,
			wusuariotabla:	  $("#wusuariotabla").val(),
			parametro:		  $("#parametro_buscar").val(),
			campobuscar:	  $("#select_buscador_campo").val()

		},function(data) {
			//alert("hola");
			$("#ppal").html(data);


		});


	}

	function ffinal()
	{
		// var wprincipio 	= 0;
		// var wfinal		= 30;

		$("#wprincipio").val(wprincipio);
		$("#wfinal").val($("#whasta").val());

		if($("#whasta").val() < 30)
		{

		}
		else
		{
			$("#wprincipio").val((($("#whasta").val() - 30)*1));


			$.post("EditarDatosMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'pasar_pagina',
				tablappal:		  $("#tablacompleta").val(),
				wprincipio:		  $("#wprincipio").val(),
				wfinal:	  		  $("#wfinal").val(),
				wusuariotabla:	  $("#wusuariotabla").val()

			},function(data) {

				$("#ppal").html(data);

			});

		}


	}

	function regresar_menu()
	{
		$("#divtabla").hide();
		$("#primeradiv").show();
		$("#oculta").html('');
		$("#div_nuevo_registro").html('');
		$("#div_nuevo_registro").remove();
	}

	function grabar(id)
	{
		var set_update='';
		var vector_cambios = new Array();
		// var vector_cambios_nombre = new Array();
		$("#tr_"+id+" td").each(function() {
				var campo = $(this).attr('editar');
				if(campo =="si")
				{

					if($(this).attr('tipo')=='10')
					{
						set_update += $(this).attr('nombrecampo')+" = '"+$("#imput_"+$(this).attr('nombrecampo')+"_"+id+":checked").val()+"' ,";
						vector_cambios[$(this).attr('nombrecampo')] = $("#imput_"+$(this).attr('nombrecampo')+"_"+id+":checked").val();

					}
					else if($(this).attr('tipo')=='18' )
					{
						set_update += $(this).attr('nombrecampo')+" = '"+$("#imput_"+$(this).attr('nombrecampo')+"_"+id).attr("valor")+"' ,";
						vector_cambios[$(this).attr('nombrecampo')] = $("#imput_"+$(this).attr('nombrecampo')+"_"+id).attr("valor");
						// alert($("#imput_"+$(this).attr('nombrecampo')+"_"+id).attr("valor"));
						// alert($("#imput_"+$(this).attr('nombrecampo')+"_"+id).attr("nombre"));
						// vector_cambios_nombre[$(this).attr('nombrecampo')] = $("#imput_"+$(this).attr('nombrecampo')+"_"+id).attr("nombre");
					}
					else if($(this).attr('tipo')=='9' )
					{
						set_update += $(this).attr('nombrecampo')+" = '"+$("#imput_"+$(this).attr('nombrecampo')+"_"+id).attr("nombre")+"' ,";
						vector_cambios[$(this).attr('nombrecampo')] = $("#imput_"+$(this).attr('nombrecampo')+"_"+id).attr("nombre");
						// vector_cambios_nombre[$(this).attr('nombrecampo')] = $("#imput_"+$(this).attr('nombrecampo')+"_"+id).attr("nombre");
					}
					else
					{
						set_update += $(this).attr('nombrecampo')+" = '"+$("#imput_"+$(this).attr('nombrecampo')+"_"+id).val()+"' ,";
						vector_cambios[$(this).attr('nombrecampo')] = $("#imput_"+$(this).attr('nombrecampo')+"_"+id).val();

					}
				}
		});



		$.post("EditarDatosMatrix.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'grabar_tabla',
			tablappal:		  $("#tablacompleta").val(),
			wset_update:	  set_update,
			wid:			  id

		},function(data) {

			if(data=='1')
			{
				var clave=0;
				for (clave in 	vector_cambios )
				{
					$("#td_"+clave+"_"+id).attr('nombre' , $("#imput_"+clave+"_"+id).attr('nombre'));
					$("#td_"+clave+"_"+id).html(vector_cambios[clave]);
					$("#td_"+clave+"_"+id).attr('valor', vector_cambios[clave]);

					$("#td_"+clave+"_"+id).attr('nombre' , $("#imput_"+clave+"_"+id).attr('nombre'));

					$("#div_operacione_"+id).html("<div id='editar_"+id+"' style='cursor: pointer;' title='Editar' onclick='editar("+id+")'><img   src='../../images/medical/hce/mod.PNG' >&nbsp;<font style='color: #235a81'>Editar</font></div>");
					$("#div_operacione2_"+id).html("<div id='editar2_"+id+"' style='cursor: pointer;' title='Editar' onclick='editar("+id+")'><img   src='../../images/medical/hce/mod.PNG' >&nbsp;<font style='color: #235a81'>Editar</font></div>");

				}
			}

		});


	}

	function siguiente()
	{

		var whasta 		= ($("#whasta").val() *1);


		if( ($("#wprincipio").val() *1) +30 < whasta)
		{
			var wprincipio 	= ($("#wprincipio").val() *1) +30;
			var wfinal		= ($("#wfinal").val() * 1) +30;
			$("#wprincipio").val(wprincipio);
			$("#wfinal").val(wfinal);

			$.post("EditarDatosMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'pasar_pagina',
				tablappal:		  $("#tablacompleta").val(),
				wprincipio:		  wprincipio,
				wfinal:	  		  wfinal,
				wusuariotabla:	  $("#wusuariotabla").val(),
				parametro:		  $("#parametro_buscar").val(),
				campobuscar:	  $("#select_buscador_campo").val(),
				whasta:			  $("#whasta").val() *1

			},function(data) {

				$("#ppal").html(data);

			});
		}
		else
		{


		}

	}

	function anterior ()
	{

		// alert($("#wprincipio").val());
		if(($("#wprincipio").val() *1) -(30) >= 0  )
		{
			var wprincipio 	= ($("#wprincipio").val() *1) -30;
			var wfinal		= ($("#wfinal").val() * 1) -30;


			$("#wprincipio").val(wprincipio);
			$("#wfinal").val(wfinal);

			$.post("EditarDatosMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'pasar_pagina',
				tablappal:		  $("#tablacompleta").val(),
				wprincipio:		  wprincipio,
				wfinal:	  		  wfinal,
				wusuariotabla:	  $("#wusuariotabla").val(),
				parametro:		  $("#parametro_buscar").val(),
				campobuscar:	  $("#select_buscador_campo").val(),

			},function(data) {

				$("#ppal").html(data);

			});
		}
		else
		{

		}

	}

	function ocultar(id)
	{

		var set_update='';
		var vector_cambios = new Array();
		$("#tr_"+id+" td").each(function() {
				var campo = $(this).attr('editar');
				if(campo =="si")
				{
					vector_cambios[$(this).attr('nombrecampo')] = $(this).attr('valor');

				}
		});



		var clave=0;
		for (clave in 	vector_cambios )
		{
			$("#td_"+clave+"_"+id).html(vector_cambios[clave]);
			$("#td_"+clave+"_"+id).attr('valor', vector_cambios[clave]);
			$("#div_operacione_"+id).html("<div id='editar_"+id+"' style='cursor: pointer;' title='Editar' onclick='editar("+id+")'><img   src='../../images/medical/hce/mod.PNG' >&nbsp;<font style='color: #235a81'>Editar</font></div>");
			$("#div_operacione2_"+id).html("<div id='editar2_"+id+"' style='cursor: pointer;' title='Editar' onclick='editar("+id+")'><img   src='../../images/medical/hce/mod.PNG' >&nbsp;<font style='color: #235a81'>Editar</font></div>");

		}



	}
	
	function validar_cambio (e)
	{
		var valor = $(e).val();
		var i=0;
		$(".buscador_campo").each(function(){
			
			if($(this).val()==valor)
			{
				i++;
				if(i>=2)
				{
					$(e).val('');
					//alert('El campo ya fue seleccionado para otro filtro');
					jAlert("<span style='color:#4C99F5'>El campo ya fue seleccionado para otro filtro</span>", "Mensaje");
				}
			}
			
		});
		
	}

	function buscar_dato()
	{
		//var auc = $("#wid_tabla").val();
		//alert(auc);
		
		var i=0;
		var j=0;
		var parametros = new Array();
		var campos = new Array();
		var operaciones = new Array();
		
		
		// armando busqueda
		var verificacion = true ;
		$(".parametro").each(function(){
			parametros.push( $(this).val() );
		});
		
		
		$(".buscador_campo").each(function(){
			
			$(this).removeClass('campoObligatorio');
			if($(this).val()=='')
			{
				verificacion=false;
				$(this).addClass('campoObligatorio');
			}
			campos.push( $(this).val() );
		});
		
		$(".operacion").each(function(){
			operaciones.push( $(this).val() );
			
		});
		//------------------------------
		
		var busqueda='';
		for (k=0 ; k < campos.length ; k++)
		{
			if (k==0)
			{
				busqueda = busqueda +" "+campos[k]+" = '"+parametros[k]+"'";
			}
			else
			{
				busqueda = busqueda +" AND "+campos[k]+" = '"+parametros[k]+"'";
			}
		}
		
		//JSON.stringify(campos);
	
		if(verificacion)
		{

			$.post("EditarDatosMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'abrir_tabla',
				tablappal:		  $("#tablacompleta").val(),
				wusuariotabla:	  $("#wusuariotabla").val(),
				wnombreopc:	  	  $("#wtituloopcion").val(),
				parametro: 		  $("#parametro_buscar").val(),
				wbusqueda:        busqueda,
				campobuscar: 	  $("#select_buscador_campo").val(),
				wid:			  $("#wid_tabla").val(),
				campos:           JSON.stringify(campos),
				parametros:		  JSON.stringify(parametros),
				operaciones:	  JSON.stringify(operaciones)


			},function(data) {

				$("#divtabla").show();
				$("#primeradiv").hide();

				$("#divtabla").html(data.html);
				$("#oculta").html(data.oculto);

				if($("#cambioinicioyfin").val() =='on')
				{
					$("#mostrandodatos").html("Registros de: 1 a "+ $("#cambiofinal").val());
				}
				cuantos = data.cuantos ;
			}, 'json').done(function(){

				$('#paginador').smartpaginator({

					totalrecords: cuantos,
					recordsperpage: 30,
					length: 7,
					next: 'Sig',
					//Se modifica para evitar caracteres extraños Mavila 23-10-2020 :)
					prev: 'Atrás',
					first: 'Inicio',
					last: 'Ulti',
					go: 'Ir',
					theme: 'black',
					controlsalways: true,
					onchange:

					function (newPage) {

							$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
									css: 	{
												width: 	'auto',
												height: 'auto'
											}
							 });

							$.ajax({
								url: "EditarDatosMatrix.php",
								type: "POST",
								data:{

									consultaAjax: 		'',
									wemp_pmla:      	$('#wemp_pmla').val(),
									accion:           	'mostrar_registros',
									wtabla:				$("#tablacompleta").val(),
									wusuario:			$("#wusuariotabla").val(),
									parametro: 			$("#parametro_buscar").val(),
									campobuscar: 		$("#select_buscador_campo").val(),
									wprincipio:			(newPage*30) - 30,
									wfinal:				30,
									wnombreopc:	  	    $("#wtituloopcion").val(),
									wid:				$("#wid_tabla").val(),
									campos:             JSON.stringify(campos),
									parametros:		    JSON.stringify(parametros),
									operaciones:	    JSON.stringify(operaciones),
									wbusqueda:		    busqueda

								},
								async: false,
								dataType: "json",
								success:function(data_json) {

									if (data_json.error == 1)
									{
										alert(data_json.mensaje);
										return;
									}
									else{

										$('#datos').html(data_json.html);
										$.unblockUI();
									}
								}
							});

					}
				});


			});
		}
		else
		{
			//jAlert("Debe seleccionar un campo para la busqueda");
			jAlert("<span style='color:#4C99F5'>Debe seleccionar un campo para la busqueda</span>", "Mensaje");
		}

	}

	function limpiar_dato()
	{
		var nombreopc = $("#wtituloopcion").val();
		var wtabla	   = $("#tablacompleta").val();
		var wid       = $("#wid_tabla").val();
		abrir_tabla(wtabla,nombreopc,wid);

	}


	function nuevo_dato()
	{

		
		$( "#div_nuevo_registro" ).dialog({

			height: 600,
			width:  700,
			modal: true,
			title: "Crear Nuevo Registro"
		});

		//--> quito la classobligatorios
		$("#tablaNuevoRegisto").find("[tipo=obligatorio]").each(function(){
			$(this).removeClass('campoObligatorio');
		});

		// --> seteo los campos
		$("#tablaNuevoRegisto").find("[tipo=obligatorio]").each(function(){
			if($(this).attr('borrar')=='no')
			{

			}
			else
			{
				$(this).val("");
			}
		});

		// --> seteo los divestado
		$("#tablaNuevoRegisto").find("[condicion=divestado]").each(function(){
			$(this).html("off");
		});

		//--> seteo los checbox
		$("#tablaNuevoRegisto").find("input[type='checkbox']").each(function(){
			$(this).attr('checked', false);
			$(this).removeAttr("checked");
		});

		// inicializo los tipo 18
		$("#tablaNuevoRegisto").find("[tipo_campo=18]").each(function(){
			$(this).attr("valor","");
			$(this).attr("nombre","");
			$(this).val("");
			var nombre = $(this).attr("id");
			var HiddenArray = 'hidden_'+$(this).attr("nombrecampo");
			//$(this).html('<input id="imput_'+$(this).attr("nombrecampo")+'_'+id+'" type="text">');
			var consulta = $("#"+HiddenArray+"").val();
			var where    = $("#"+HiddenArray+"").attr('where') ;
			crear_autocomplete(nombre,HiddenArray,'','',consulta,where);
			
		});

		$("#tablaNuevoRegisto").find("[tipo_campo=9]").each(function(){
			$(this).attr("valor","");
			$(this).attr("nombre","");
			$(this).val("");
			var nombre = $(this).attr("id");
			var HiddenArray = 'hidden_'+$(this).attr("nombrecampo");
		//$(this).html('<input id="imput_'+$(this).attr("nombrecampo")+'_'+id+'" type="text">');
			crear_autocomplete9(nombre,HiddenArray);
		});



		$("#tablaNuevoRegisto").find("[tipo_campo=3]").each(function(){
			$(this).datepicker({
				showOn: "button",
				buttonImage: "../../images/medical/root/calendar.gif",
				buttonImageOnly: true,
				defaultTime: 'now'
			});
		});





		$('.buscadores').on({
			focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{
					$(this).val("");
					$(this).attr("valor","");
					$(this).attr("nombre","");
				}
				else
				{
					$(this).val($(this).attr("nombre"));
				}
			}
		});






		$("#div_nuevo_registro").show();

	}

	function cerrardialog()
	{
		$("#div_nuevo_registro" ).dialog('close');
	}

	function grabarNuevoRegistro(wtabla)
	{
		var permitirGuardar = true;
		var mensaje ;
		$('#tablaNuevoRegisto .campoObligatorio').removeClass('campoObligatorio');
		campos ='';

		// --> Validacion de campos obligatorios
		$("#tablaNuevoRegisto").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{


				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
				//campos += "-"+$(this).attr('nombrecampo');

			}

		});


		if(permitirGuardar == false)
		{
			alert(mensaje);

		}
		else
		{


				if($(".cargando").length > 0)
				{
					// Si ya hay uno cargando entonces no haga nada hasta que termine
					return;
				}

				$(".btn_loading").attr("disabled","disabled");
				$("#botongrabarventanamodal").addClass("cargando");
				$("#botongrabarventanamodal").html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >');


			var str_insertar ='';
			var str_values   ='';
			str_insertar = "INSERT INTO "+wtabla+" (";

			$("#tablaNuevoRegisto").find("[campo=grabar]").each(function(){
				if($(this).attr('eschecbox')=='on')
				{
					// if($(this+":checked").length ==1)
					if($(this).is(':checked'))
					{
						var dato='on';
					}
					else
					{
						var dato = 'off';
					}

				}
				else if($(this).attr("tipo_campo")=="18")
				{
						var dato = $(this).attr("valor");

				}
				else if ($(this).attr("tipo_campo")=="9")
				{
						var dato = $(this).attr("valor");

				}
				else
				{
					var dato = $(this).val();
				}
				dato = dato.replace(/\'/gi, "");
				dato = dato.replace(/\"/gi, "");
				dato = dato.replace(/\\/gi, "");
				str_insertar +=($(this).attr('nombrecampo'))+",";
				str_values   +="'"+dato+"',";
			});




			// $("#tablaNuevoRegisto").find("input[type='checkbox']").each(function(){
			// $(this).attr('checked', false);
			// $(this).removeAttr("checked");
			// });

			str_values   = str_values.substring(0, str_values.length-1);
			str_insertar = str_insertar.substring(0, str_insertar.length-1);
			str_insertar +=") VALUES ("+str_values+")";



			$.post("EditarDatosMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'grabar_nuevo_registro',
				winsertar:		  str_insertar

			},function(data) {
				if(data.error == 1)
				{
					alert("Error en el query");
				}
				else
				{
					alert("Guardado correctamente");
					$("#div_nuevo_registro" ).dialog('close');
					abrir_tabla(wtabla , $("#wtituloopcion").val(),$("#wid_tabla").val());

				}
			},"json").done(function(){
						var this_btn_x = $(".cargando");
						$(this_btn_x).removeClass("cargando");
						$(this_btn_x).html("Grabar");
						$(".btn_loading").removeAttr("disabled");
					});
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

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if(isset($accion))
{
	switch($accion)
	{

		
		
		case "consulta_buscador":
		{
			
			$where 	  = str_replace("\'","",$where);
			$where	  = str_replace(",","",$where);
			$where    = explode ("'-'", $where);
			
			
			//SELECT Artcod as ppal , Concat(Artcod,'-',Artcom) as seleccionado FROM movhos_000026
			for ($k=0; $k < count($where) ; $k++)
			{
					
					if($k==0)
					{
						//$wherenuevo .= $where[$k]." LIKE '%".$term."%'" ;
						$parametros .= $where[$k] ;
					}
					else
					{						
						//$wherenuevo .= " OR ".$where[$k]."  LIKE  '%".$term."%'" ;
						$parametros .= ",'-',".$where[$k];
					}
			}
			
			if($k>0)
			{
				$parametros = "  CONCAT(".$parametros.")  LIKE '%".$term."%'" ;
			}
			$consulta = str_replace("\'","'",$consulta);
			
			$query = $consulta ."   WHERE  ".$wherenuevo."  ".$parametros." ";

			
			$res = 	mysql_query($query,$conex) or die ("Error 3.1: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

			$o=0;
			$arr_relacion_ne  = array();
			while($row = mysql_fetch_array($res))
			{
				$ppal = trim($row['ppal']);
				if(!array_key_exists($ppal, $arr_relacion_ne))
				{
					$arr_relacion_ne[$ppal] = array();
				}
				$arr_relacion_ne[$ppal]['value'] = utf8_encode(trim($row['seleccionado']));
				$arr_relacion_ne[$ppal]['name']  = utf8_encode(trim($row['seleccionado']));
				$arr_relacion_ne[$ppal]['cod']   = $ppal;
			
			}
		
			$arr_relacion_ne['*']['value']	= 'TODOS';
			$arr_relacion_ne['*']['name']	= 'TODOS';
			$arr_relacion_ne['*']['cod']	= '*';
			$arr_relacion_ne['']['value']	= 'NO APLICA';
			$arr_relacion_ne['']['name']	= 'NO APLICA';
			$arr_relacion_ne['']['cod']	= '';
			echo json_encode($arr_relacion_ne);
			break;
		}
		case "llenarcampoAutocompletar":
		{
			$where 	  = str_replace("\'","",$where);
			$where	  = str_replace(",","",$where);
			$where    = explode ("'-'", $where);
			for ($k=0; $k < count($where) ; $k++)
			{
					if($k==0)
						$wherenuevo .= $where[$k]." = '".$term."'" ;
					//else	
						//$wherenuevo .= " OR ".$where[$k]."  =  '".$term."'" ;
			}
			
			$consulta = str_replace("\'","'",$consulta);
			$query = $consulta ."   WHERE  ".$wherenuevo."";
		
			$res = 	mysql_query($query,$conex) or die ("Error 3.2: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

			$o=0;
			while($row = mysql_fetch_array($res))
			{
				//$arr_relacion_ne[trim($row['ppal'])] = trim($row['seleccionado']);
				$nombrecompleto = trim(utf8_encode($row['seleccionado']));
				//$arr_relacion_ne['codigo'] = trim(utf8_encode($row['seleccionado']));
				// $arr_relacion_ne[utf8_encode(trim($row['ppal']))] = utf8_encode(trim($row['ppal']));
				// $arr_relacion_ne[utf8_encode(trim($row['ppal']))] = trim(utf8_encode($row['seleccionado']));
				$o++;
			}
			if( $term =='*')
			{
				$nombrecompleto ='*-TODOS';
			}
			if($nombrecompleto =='')
			{
				$nombrecompleto ='NO APLICA';
				
			}
			echo $nombrecompleto;
			//echo json_encode($arr_relacion_ne);
			break;
		}
		case "abrir_tabla":
		{
			$wbusqueda = str_replace("\\", "", $wbusqueda );
		
			echo abrir_tabla ($tablappal,$wusuariotabla,$wnombreopc,$parametro,$campobuscar,$wid,$wbusqueda,$campos,$parametros,$operaciones);
			break;
		}
		case "traer_relacion_ne" :
		{
			echo traer_relacion_ne($comentario,$tabla,$valorp,$wid,$wnombrecampo);
			break;

		}
		case "grabar_tabla" :
		{
			grabar_tabla($tablappal,$wset_update,$wid);
			break;

		}
		case "busca_dato" :
		{
			busca_dato($tablappal,$parametro,$campobuscar,$wusuariotabla);
			break;
		}
		case "mostrar_registros" :
		{

			$buscador=1;
			$data_array	= array("mensaje"=>"","error"=>0,"html"=>'');
			$resp =array();
			$resp=PermisosTabla($wtabla, $wusuario,$parametro, $campobuscar,$wprincipio, $wfinal,$whasta,$buscador,$wnombreopc,$wid,$wbusqueda,$campos,$parametros,$operaciones);
			$data_array['html']=utf8_encode($resp['html']);
			$data_array['oculto']=utf8_encode($resp['oculto']);
			echo json_encode($data_array);
			break;
		}
		case "pasar_pagina" :
		{
			// $parametro 	 = '';
			// $campobuscar = '';
			echo PermisosTabla($tablappal, $wusuariotabla,$parametro,$campobuscar,$wprincipio, $wfinal ,$whasta);
			break;
		}
		case "pasar_pagina_final" :
		{
			$parametro 	 = '';
			$campobuscar = '';
			global $conex;
			global $wempla;
			$idName = findIdName($tablappal);
			$select_count_tabla = "SELECT  Max({$idName}) as maximo
									 FROM ".$tablappal." ";

			$res = 	mysql_query($select_count_tabla,$conex) or die ("Error 3.3: ".mysql_errno()." - en el query: ".$select_count_tabla." - ".mysql_error());

			$maximo = 0;
			$cuantos = 0;
			if($row = mysql_fetch_array($res))
			{
				$maximo = $row['maximo'];
			}
			$wprincipio = ($maximo*1) -30;
			if($wprincipio < 0)
			{
					$select_count_tabla = "SELECT  count({$idName}) as cuantos
											FROM ".$tablappal." ";
					$res = 	mysql_query($select_count_tabla,$conex) or die ("Error 3.4: ".mysql_errno()." - en el query: ".$select_count_tabla." - ".mysql_error());
					if($row = mysql_fetch_array($res))
					{
						$cuantos = $row['cuantos'];
					}

					$wprincipio = ($maximo*1) - ($cuantos * 1);
			}
			$wfinal = ($maximo*1);
			echo PermisosTabla($tablappal, $wusuariotabla,$parametro,$campobuscar,$wprincipio, $wfinal,$whasta);
			break;
		}
		case "grabar_nuevo_registro" :
		{
			$data= array("mensaje"=>"","error"=>0);
			global $conex;
			global $wempla;
			global $wusuario;
			$insert_tabla = str_replace("\\","",$winsertar);
			$insert_tabla = utf8_decode($insert_tabla);
			if($res = mysql_query($insert_tabla,$conex))
			{

				$wfecha = date("Y-m-d");
				$whora = date("H:i:s");
				$insert_tabla = str_replace('\'','"',$insert_tabla);


				$update_log ="INSERT  root_000106 (Medico ,Fecha_data 	 ,  Hora_data 	, 	Logope 		, Logins			,     Logusu		, Seguridad)
											VALUES	  ('root' , '".$wfecha."' , 	'".$whora."',   'Insert'  	, '".$insert_tabla."' ,  '".$wusuario."'  	, 'C-".$wusuario."')";

				mysql_query($update_log,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$update_log." - ".mysql_error());

			}
			else
			{
				$data["mensaje"]= ("Error 3.5: ".mysql_errno()." - en el query: ".$select_count_tabla." - ".mysql_error());
				$data["error"] = 1;
			}
			echo json_encode($data);
			break;
		}
	}
	return;

}

function findIdName($tabla){
	global $conex;
	$q = "SELECT COLUMN_NAME
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE table_name = '$tabla'
				ORDER BY ORDINAL_POSITION;";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	$idReturn = "id";
	if ($num > 0)
	{
		while ($rs = mysql_fetch_assoc($res)){
            
            if ($rs['COLUMN_NAME'] == 'id') $idReturn = $rs['COLUMN_NAME'];
            elseif ($rs['COLUMN_NAME'] == 'ids')$idReturn = $rs['COLUMN_NAME'];
        }
		
	} 
	return $idReturn;
}

function abrir_tabla ($wtabla,$wusuario,$wnombreopc,$parametro,$campobuscar,$wid,$wbusqueda='',$campos='',$parametros='',$operaciones='')
{

	global $conex;
	global $wempla;

	$wprincipio = 0;
	$wfinal = 30;
	//<input type='hidden' id='wusuariotabla' value='".$wusuario."'>
	$html .= "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>
			  <input type='hidden' id='tablacompleta' value='".$wtabla."'>
			  <input type='hidden' id='wid_tabla' value='".$wid."'>
			  <input type='hidden' id='wprincipio' value='".$wprincipio."'>
			  <input type='hidden' id='wfinal' value='".$wfinal."'>
			  <input type='hidden' id='wtituloopcion' value='".$wnombreopc."'>";

					$auxwtabla = explode("_", $wtabla);
					$nom_tabla = $auxwtabla[0];
					$num_tabla = $auxwtabla[1];

					$html .="<fieldset align='center' id='' style='padding:15px;'><legend class='fieldset'>Edicion de datos ".$wnombreopc."</legend>";
					$html .="<table align='center' width='100%'>";
					$html .="<tr><td align='center'><input type='button' value='Regresar al Menu' onclick='regresar_menu()'></td></tr>";
					$html .= "<tr><td><div id='ppal' align='center'>";
					$buscador = 0;
					$resp = array();
					$resp = PermisosTabla($wtabla, $wusuario,$parametro,$campobuscar,$wprincipio, $wfinal,$whasta,$buscador,$wnombreopc,$wid,$wbusqueda,$campos,$parametros,$operaciones);
					$html .= utf8_encode($resp['html']);
					$html .=  "</div></td></tr></table></fieldset>";

					//$html .="<div>".$array_data['html']."</div>";

					$data= array("mensaje"=>"","error"=>0);
					$data['html'] = $html;
					$data['cuantos'] = $resp['cuantos'];
					$data['oculto'] = utf8_encode($resp['oculto']);
					return json_encode($data);




}

function busca_dato($tablappal,$parametro,$campobuscar,$wusuariotabla)
{
	global $conex;
	global $wempla;
	$wprincipio =0;
	$wfinal = 0;

	$buscador=1;
	$data_array	= array("mensaje"=>"","error"=>0,"html"=>'');
	$data_array['html']=PermisosTabla($tablappal, $wusuariotabla,$parametro,$campobuscar,$wprincipio, $wfinal,$whasta,$buscador);
	$data_array['html']=utf8_encode(PermisosTabla($tablappal, $wusuariotabla,$parametro,$campobuscar,$wprincipio, $wfinal,$whasta,$buscador));
	echo json_encode($data_array);

	$buscador=1;
	$data_array	= array("mensaje"=>"","error"=>0,"html"=>'');
	$data_array['html']=utf8_encode(PermisosTabla($wtabla, $wusuario,$parametro, $campobuscar,$wprincipio, $wfinal,$whasta,$buscador));
	echo json_encode($data_array);
	// break;


}




function grabar_tabla($tablappal,$wset_update,$wid)
{



	global $conex;
	global $wempla;
	global $wusuario;

	$wset_update = substr ($wset_update, 0, strlen($wset_update) - 1);
	$wset_update = str_replace('\\','',$wset_update);
	// 22 Mayo 2020 Freddy Saenz

	$valSelect = "";//valores del select , como estaba la bd antes.
	$idName = findIdName($tablappal);
//aqui se puede hacer el select de como estaba el registro en la base de datos
	$qselect = "SELECT * FROM ".$tablappal."  WHERE {$idName} ='".$wid."' ";
	$vcamposmodificados = "";
	$vvaloresnuevos = utf8_decode( $wset_update );

	if ($resselect = 	mysql_query($qselect,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qselect." - ".mysql_error()))
	{


		while ( $rowsQuery = mysqli_fetch_array($resselect , MYSQLI_ASSOC) )//solo el arreglo asociativo , no traer las claves numereicas :0,1,2,...
		{

			
			foreach ( $rowsQuery as $key => $value) 
			{
				if ($valSelect != ""){
					$valSelect .= " , ";
				}
				$vvaloresanteriores  = "$key = '".$value."'";//dejar asi con espacios entre igual ,para poder buscar en la cadena
				//de valores nuevos o actualizados $vvaloresnuevos
				$valSelect .= " $vvaloresanteriores ";//cadena de la forma campo = valor
				$vvaloresanteriores = str_replace('\\','',$vvaloresanteriores);//quitar la comilla sencilla .
				$poscampo = strpos( $vvaloresnuevos  , $vvaloresanteriores );//buscar vvaloresanteriores en vvaloresnuevos
				if ( $poscampo === false ){// este campo no esta lo que indica que fue modificado (no se encontro en el valor anterior)
					//TRIPLE IGUAL PORQUE LA POSICION CERO LA DEVUELVE COMO FALSO.
					if  ( $vcamposmodificados != "" ){
						$vcamposmodificados .= " , ";
					}
					$vcamposmodificados .= $vvaloresanteriores;
				}
				//para al final crear una cadena de la forma UPDATE Tabla SET campo = valor , ...
				
			}



		}



	}


	if ( $valSelect != "" ){
		if ($vcamposmodificados == ""){
			$vcamposmodificados = " NO HUBO CAMBIOS ";
		}

		$valSelect = "REGISTRO ANTES DE LA MODIFICACION :\r\nUPDATE  ".$tablappal." SET ".utf8_decode($valSelect)."  WHERE {$idName} = '".$wid."' ";//aqui esta el registro , original , antes de ser modificado
		$valSelect = "CAMPOS MODIFICADOS: $vcamposmodificados \r\n".$valSelect;
		$valSelect .= "\r\n" ;
		$valSelect .= "========= ========= =========\r\nREGISTRO MODIFICADO\r\n" ;
	}
//Fin Modificacion 22 Mayo 2020


	$update_tabla = "UPDATE  ".$tablappal." SET ".utf8_decode($wset_update)."  WHERE {$idName} ='".$wid."' ";

	$resultado = 0;

	if ($res = 	mysql_query($update_tabla,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$update_tabla." - ".mysql_error()))
	{
		$user_session = explode('-',$_SESSION['user']);
		$user_session = (count($user_session) > 1)? $user_session[1] : $user_session[0];
		$wuse = $user_session;
		$wfecha = date("Y-m-d");
		$whora = date("H:i:s");
		

		if ( $valSelect != "" ){//guardar el registro anterior
			$update_tabla = $valSelect . $update_tabla;
		}
		$update_tabla = str_replace('\'','"',$update_tabla);
		

		$update_log ="INSERT  root_000106 (Medico ,Fecha_data 	 ,  Hora_data 	, 	Logope 		, Logins			,     Logusu		, Seguridad)
									VALUES	  ('root' , '".$wfecha."' , 	'".$whora."',   'Update'  	, '".$update_tabla."' ,  '".$wusuario."'  	, 'C-".$wusuario."')";

		mysql_query($update_log,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$update_log." - ".mysql_error());
		$resultado = 1;
	}

	echo $resultado;

}

function traer_hidden($comentario,$tabla,$valor,$wid,$wnombrecampo)
{

	global $conex;
	global $wempla;


	$datos_ne  = array();
	$datos_ne  = explode("-", $comentario);
	$num_datos = count($datos_ne);


	// construyo where
	for($j=2 ; $j< $num_datos ; $j ++)
	{
		$campos .= $datos_ne[$j].",";
		$campoppal = $datos_ne[2];

	}
	
	
	$campos = substr ($campos, 0, strlen($campos) - 1);

	// construyo where
	for($j=3 ; $j< $num_datos ; $j ++)
	{
		$campos_dos .= $datos_ne[$j].",";
		$campoppaldos = $datos_ne[3];

	}
	$campos_dos = substr ($campos_dos, 0, strlen($campos_dos) - 1);



	$donde = "AND campo IN ( ".$campos.")";
	$donde_dos = "AND campo IN ( ".$campos_dos.")";
	$arr_relacion_ne = array();


	// del mismo grupo de tablas
	if((($num_datos*1) - ($datos_ne[0]*1)) == 2)
	{
		$select_campos_tabla = "SELECT campo,descripcion
								  FROM det_formulario
								 WHERE medico = '".$tabla."'
								   AND codigo = '".$datos_ne[1]."'
								   ".$donde."" ;

		$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 3.6: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
		$r=0;
		while($row = mysql_fetch_array($res))
		{
			
			// if($r==0)
				// $ppal = $row['descripcion'];
			
			if($row['campo'] == $campoppal)
			{
				$campos_selectprimero .= $row['descripcion'].",'-',";
				$ppal = $row['descripcion'];
			}
			else
			{
				$campos_select .= $row['descripcion'].",'-',";
			}
			
			$r++;
		}
		$campos_select = $campos_selectprimero."".$campos_select;
		$campos_select = substr ($campos_select, 0, strlen($campos_select) - 5);

		// query para hacer el select
		$query = "SELECT ".$ppal." as ppal , Concat(".$campos_select.") as seleccionado FROM ".$tabla."_".$datos_ne[1]."";

		

		//$res = 	mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

		$o=0;
		/*while($row = mysql_fetch_array($res))
		{
			//$arr_relacion_ne[trim($row['ppal'])] = trim($row['seleccionado']);
			$arr_relacion_ne[utf8_encode(trim($row['ppal']))] = trim(utf8_encode($row['seleccionado']));
			
		}*/
		
		/*$arr_relacion_ne['*']	= 'TODOS';
		$arr_relacion_ne['']	= 'NO APLICA';*/

	}
	if(($num_datos*1) - ($datos_ne[0]*1) == 3)
	{
		$select_campos_tabla = "SELECT campo,descripcion
								  FROM det_formulario
								 WHERE medico = '".$datos_ne[1]."'
								   AND codigo = '".$datos_ne[2]."'
								   ".$donde_dos."" ;


		$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 3.7: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
		$r=0;
		while($row = mysql_fetch_array($res))
		{
			
			// if($r==0)
				// $ppal = $row['descripcion'];
			
			if($row['campo'] == $campoppaldos)
			{
				
				$campos_selectprimero .= $row['descripcion'].",'-',";
				$ppal = $row['descripcion'];
			}
			else
			{
				
				$campos_select .= $row['descripcion'].",'-',";
			}
			
			$r++;
		}
		$campos_select = $campos_selectprimero."".$campos_select;
		$campos_select = substr ($campos_select, 0, strlen($campos_select) - 5);

		// query para hacer el select
		$query = "SELECT ".$ppal." as ppal , Concat(".$campos_select.") as seleccionado FROM ".$datos_ne[1]."_".$datos_ne[2]."";

		//$res = 	mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

		
		$o=0;
		/*
		while( ($row = mysql_fetch_array($res)))
		{
			//$arr_relacion_ne[trim($row['ppal'])] = trim($row['seleccionado']);
			$txt = trim(preg_replace('/[\"]+/', '', $row['seleccionado']));
			$txt = trim(preg_replace('/[\']+/', '', $row['seleccionado']));
			$arr_relacion_ne[utf8_encode(trim($row['ppal']))] = trim(utf8_encode($txt));
			$o++;
		}
		*/
		
		/*$arr_relacion_ne['*']	= 'TODOS';
		$arr_relacion_ne['']	= 'NO APLICA';*/

	
	}
	
	$arr_relacion_ne['query'] = $query;
	$arr_relacion_ne['campos'] = $select_campos_tabla;
	$arr_relacion_ne['principal'] = $campoppal;
	$arr_relacion_ne['camposwhere'] = $campos_select;
	
	return $arr_relacion_ne;

}

function traer_hidden_tipo9($comentario,$tabla,$valor,$wid,$wnombrecampo)
{




	global $conex;
	global $wempla;


	$datos_ne  = array();
	$datos_ne  = explode("-", $comentario);
	$num_datos = count($datos_ne);


	// construyo where
	for($j=2 ; $j< $num_datos ; $j ++)
	{
		$campos .= $datos_ne[$j].",";

	}
	$campos = substr ($campos, 0, strlen($campos) - 1);

	// construyo where
	for($j=3 ; $j< $num_datos ; $j ++)
	{
		$campos_dos .= $datos_ne[$j].",";

	}
	$campos_dos = substr ($campos_dos, 0, strlen($campos_dos) - 1);



	$donde = "AND campo IN ( ".$campos.")";
	$donde_dos = "AND campo IN ( ".$campos_dos.")";
	$arr_relacion_ne = array();


	// del mismo grupo de tablas
	if((($num_datos*1) - ($datos_ne[0]*1)) == 2)
	{
		$select_campos_tabla = "SELECT campo,descripcion
								  FROM det_formulario
								 WHERE medico = '".$tabla."'
								   AND codigo = '".$datos_ne[1]."'
								   ".$donde."" ;

		$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 3.8: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
		$r=0;
		while($row = mysql_fetch_array($res))
		{
			$campos_select .= $row['descripcion'].",'-',";
			if($r==0)
				$ppal = $row['descripcion'];

			$r++;
		}
		$campos_select = substr ($campos_select, 0, strlen($campos_select) - 5);

		// query para hacer el select
		 $query = "SELECT Concat(".$campos_select.") as ppal , Concat(".$campos_select.") as seleccionado
					FROM ".$tabla."_".$datos_ne[1]."";



		$res = 	mysql_query($query,$conex) or die ("Error 3.9: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

		
		
		
		while($row = mysql_fetch_array($res))
		{
			$arr_relacion_ne[utf8_encode(trim($row['ppal']))] = trim(utf8_encode($row['seleccionado']));
			//$arr_relacion_ne[trim($row['ppal'])] = trim($row['seleccionado']);
		}
		
		$arr_relacion_ne['*']	= 'TODOS';
		$arr_relacion_ne['']	= 'NO APLICA';


	}
	if(($num_datos*1) - ($datos_ne[0]*1) == 3)
	{
		$select_campos_tabla = "SELECT campo,descripcion
								  FROM det_formulario
								 WHERE medico = '".$datos_ne[1]."'
								   AND codigo = '".$datos_ne[2]."'
								   ".$donde_dos."" ;

		$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 3.10: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
		$r=0;
		while($row = mysql_fetch_array($res))
		{
			$campos_select .= $row['descripcion'].",'-',";
			if($r==0)
				$ppal = $row['descripcion'];

			$r++;
		}

		$campos_select = substr ($campos_select, 0, strlen($campos_select) - 5);

		// query para hacer el select
		$query = "SELECT Concat(".$campos_select.")as ppal , Concat(".$campos_select.") as seleccionado
					FROM ".$datos_ne[1]."_".$datos_ne[2]."";

		$res = 	mysql_query($query,$conex) or die ("Error 3.11: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

		
		while($row = mysql_fetch_array($res))
		{
			$arr_relacion_ne[utf8_encode(trim($row['ppal']))] = trim(utf8_encode($row['seleccionado']));

			//$arr_relacion_ne[trim($row['ppal'])] = trim($row['seleccionado']);
		}
		
		$arr_relacion_ne['*']	= 'TODOS';
		$arr_relacion_ne['']	= 'NO APLICA';


	}

	return $arr_relacion_ne;

}


// Obtiene la descripción del dato en la tabla relacionada asociado a una relación ne  Leandro Meneses 2021-01-14
function obtener_tooltip_relacion_ne($comentario,$tabla,$valor)
{
	
	// $comentario contienes la información necesaria para traer los datos de la taba relacionada
	
	global $conex;
	global $wempla;


	$datos_ne  = array();
	// el parámetro comnetario para las relaciones ne tiene la sisguinete estructura N-PREFIjO TABLA RELACIONADA-CONSECUTIVO TABLA RELACIONADA-CAMPO CODIGO-CAMPO DESCRIPCION1-CAMPODESCRIPCION2...
	// donde:
	//		N es el numero de campos relacionados incluyendo el código
	//		Si no tiene prefijo quiere decir que se trabaja con el médico activo
	//		Los  campos relacionados corresponden al codigo nobre en la tabal det_formulario
	// ej : 2-000045-001-002
	//		5-movhos-000045-001-002-003-004-005
	$datos_ne  = explode("-", $comentario);
	$num_datos = count($datos_ne);


	// construyo where suponienndo que $comentario no contiene el prefijo de la tabla
	for($j=1 ; $j< $num_datos ; $j ++)
	{
		$campos .= $datos_ne[$j].",";

	}
	$campos = substr ($campos, 0, strlen($campos) - 1);

	// construyo where suponienndo que $comentario contiene el prefijo de la tabla
	for($j=2 ; $j< $num_datos ; $j ++)
	{
		$campos_dos .= $datos_ne[$j].",";

	}
	$campos_dos = substr ($campos_dos, 0, strlen($campos_dos) - 1);


	$donde = "AND campo IN ( ".$campos.") ";
	$donde_dos = "AND campo IN ( ".$campos_dos.") ";

	// del mismo grupo de tablas, $comentario no contiene el prefijo de la tabla
	// el total de datos que llegan en $comentario es igual al numero de campos relacionados + 2 (numero de campos + tabla)
	if((($num_datos*1) - ($datos_ne[0]*1)) == 2)
	{
		$select_campos_tabla = "SELECT campo,descripcion
								  FROM det_formulario
								 WHERE medico = '".$tabla."'
								   AND codigo = '".$datos_ne[1]."'
								   ".$donde."" ;

		$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 3.12: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
		$r=0;
		while($row = mysql_fetch_array($res))
		{
			$campos_select .= $row['descripcion'].",'-',";
			if($r==0)
				$ppal = $row['descripcion'];

			$r++;
		}
		$campos_select = substr ($campos_select, 0, strlen($campos_select) - 5);

		// query para hacer el select
		$query = "SELECT ".$ppal." as ppal , Concat(".$campos_select.") as seleccionado
					FROM ".$tabla."_".$datos_ne[1]." where ".$ppal."= '".$valor."' ";


		$res = 	mysql_query($query,$conex) or die ("Error 3.13: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());



		while($row = mysql_fetch_array($res))
		{
			return $row['seleccionado'];
		}

	}
	// diferente grupo de tablas, $comentario contiene el prefijo de la tabla
	// el total de datos que llegan en $comentario es igual al numero de campos relacionados + 3 (numero de campos + prefijo + tabla)
	if(($num_datos*1) - ($datos_ne[0]*1) == 3)
	{
		$select_campos_tabla = "SELECT campo,descripcion
								  FROM det_formulario
								 WHERE medico = '".$datos_ne[1]."'
								   AND codigo = '".$datos_ne[2]."'
								   ".$donde_dos."" ;

		$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 3.14: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
		$r=0;
		while($row = mysql_fetch_array($res))
		{
			$campos_select .= $row['descripcion'].",'-',";
			if($r==0)
				$ppal = $row['descripcion'];

			$r++;
		}

		$campos_select = substr ($campos_select, 0, strlen($campos_select) - 5);

		// query para hacer el select
		$query = "SELECT ".$ppal." as ppal , Concat(".$campos_select.") as seleccionado
					FROM ".$datos_ne[1]."_".$datos_ne[2]." where ".$ppal."= '".$valor."' ";


		$res = 	mysql_query($query,$conex) or die ("Error 3.15: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());


		while($row = mysql_fetch_array($res))
		{
			return $row['seleccionado'];
		}
		return "NA";

	}

}

function traer_relacion_ne($comentario,$tabla,$valor,$wid,$wnombrecampo)
{
	global $conex;
	global $wempla;


	$datos_ne  = array();
	$datos_ne  = explode("-", $comentario);
	$num_datos = count($datos_ne);


	// construyo where
	for($j=1 ; $j< $num_datos ; $j ++)
	{
		$campos .= $datos_ne[$j].",";

	}
	$campos = substr ($campos, 0, strlen($campos) - 1);

	// construyo where
	for($j=2 ; $j< $num_datos ; $j ++)
	{
		$campos_dos .= $datos_ne[$j].",";

	}
	$campos_dos = substr ($campos_dos, 0, strlen($campos_dos) - 1);


	$donde = "AND campo IN ( ".$campos.")";
	$donde_dos = "AND campo IN ( ".$campos_dos.")";

	// del mismo grupo de tablas
	if((($num_datos*1) - ($datos_ne[0]*1)) == 2)
	{
		$select_campos_tabla = "SELECT campo,descripcion
								  FROM det_formulario
								 WHERE medico = '".$tabla."'
								   AND codigo = '".$datos_ne[1]."'
								   ".$donde."" ;

		$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 3.12: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
		$r=0;
		while($row = mysql_fetch_array($res))
		{
			$campos_select .= $row['descripcion'].",'-',";
			if($r==0)
				$ppal = $row['descripcion'];

			$r++;
		}
		$campos_select = substr ($campos_select, 0, strlen($campos_select) - 5);

		// query para hacer el select
		$query = "SELECT ".$ppal." as ppal , Concat(".$campos_select.") as seleccionado
					FROM ".$tabla."_".$datos_ne[1]."";

		$select .="<select id='imput_".$wnombrecampo."_".$wid."'  tipo='obligatorio'  campo='grabar' nombrecampo='".$wnombrecampo."'>";

		$res = 	mysql_query($query,$conex) or die ("Error 3.13: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

		$select .= "<option value=''  >Seleccione...</option>";

		while($row = mysql_fetch_array($res))
		{
			if( $row['ppal'] == $valor)
				$seleccionado = 'selected';
			else
				$seleccionado = '';
				$select .= "<option value='".$row['ppal']."' ".$seleccionado." >".utf8_encode($row['seleccionado'])."</option>";
		}
		$select .="</select>";


		$select .="<input type='text' id='buscador'>";
	}
	// diferente grupo de tablas
	if(($num_datos*1) - ($datos_ne[0]*1) == 3)
	{
		$select_campos_tabla = "SELECT campo,descripcion
								  FROM det_formulario
								 WHERE medico = '".$datos_ne[1]."'
								   AND codigo = '".$datos_ne[2]."'
								   ".$donde_dos."" ;

		$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 3.14: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
		$r=0;
		while($row = mysql_fetch_array($res))
		{
			$campos_select .= $row['descripcion'].",'-',";
			if($r==0)
				$ppal = $row['descripcion'];

			$r++;
		}

		$campos_select = substr ($campos_select, 0, strlen($campos_select) - 5);

		// query para hacer el select
		$query = "SELECT ".$ppal." as ppal , Concat(".$campos_select.") as seleccionado
					FROM ".$datos_ne[1]."_".$datos_ne[2]."";
		$select .="<select id='imput_".$wnombrecampo."_".$wid."'  tipo='obligatorio'  campo='grabar' nombrecampo='".$wnombrecampo."'>";

		$res = 	mysql_query($query,$conex) or die ("Error 3.15: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

		$select .= "<option value=''  >Seleccione...</option>";

		while($row = mysql_fetch_array($res))
		{
			if( $row['ppal'] == $valor)
				$seleccionado = 'selected';
			else
				$seleccionado = '';
				$select .= "<option value='".$row['ppal']."' ".$seleccionado." >".utf8_encode($row['seleccionado'])."</option>";
		}
		$select .="</select>";
	}

	return $select;


}

function traer_seleccion($vector_comentario_completos, $wtabla)
{
	global $conex;
	global $wempla;


	$var_aux = $vector_comentario_completos;
	$var_aux  = explode("-", $var_aux);
	$wtabla  = explode("_", $wtabla);
	$num_datos = count($var_aux);




	$select_seleccion = " SELECT subcodigo , descripcion
							FROM det_selecciones
							WHERE  codigo= '".$var_aux[0]."'
							  AND  activo='A'
							  AND Medico = '".$wtabla[0]."'";

	$res = 	mysql_query($select_seleccion,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_seleccion." - ".mysql_error());
	$html ="<option value =''>Seleccione</option>";

	while($row = mysql_fetch_array($res))
	{
		$html.="<option value='".$row['subcodigo']."-".$row['descripcion']."'>".$row['subcodigo']."-".$row['descripcion']."</option>";

	}

	return $html;
}
function PermisosTabla($wtabla, $wusuario,$parametro, $campobuscar,$wprincipio, $wfinal,$whasta,$buscador,$wnombreopc,$wid,$wbusqueda,$campos,$parametros,$operaciones)
{

		global $conex;
		global $wempla;

		$trae_tabla = false; // inicializar parametro
		$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'');

		//$html .="<div>Usuario:".$wusuario."-tabla".$wtabla."</div>";
		//---------------------------------------------------------
		// Se buscan los campos que el usuario puede editar
		$select_permisos = "SELECT Tabcam,Tabcvi,Tabpgr
							  FROM root_000105
							 WHERE Tabusu = '".$wusuario."'
							   AND Tabtab = '".$wtabla."' 
							   AND id = '".$wid."'";


		$res = 	mysql_query($select_permisos,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_permisos." - ".mysql_error());

		if($row = mysql_fetch_array($res))
		{
			$trae_tabla 			= true;
			$campos_editables_comas = $row['Tabcam'];
			$campos_editables_comas_aux = $campos_editables_comas;
			$campos_visibles_comas 	= $row['Tabcvi'];
			$visible_todos			= $row['Tabcvi'];
			$permiso_grabar			= $row['Tabpgr'];
		}

		$campos_editables_comas = explode(",", $campos_editables_comas);
		for($k=0; $k < count($campos_editables_comas); $k++)
		{
			$campos_editables[$campos_editables_comas[$k]] = $campos_editables_comas[$k];

		}
		//print_r($campos_editables);


		$campos_visibles_comas = explode(",", $campos_visibles_comas);
		for($k=0; $k <= count($campos_visibles_comas); $k++)
		{
			$campos_visibles[$campos_visibles_comas[$k]] = $campos_visibles_comas[$k];
		}
		//-----------------------------------------------------------
		//-----------------------------------------------------------

		// si hay resultados
		if($trae_tabla)
		{
			//----------------------------------------------------------
			// selecciona los nombres de los campos de una tabla

			$select_campos_tabla ="	SELECT COLUMN_NAME
									  FROM INFORMATION_SCHEMA.COLUMNS
									 WHERE table_name = '".$wtabla."'
									 ORDER BY ORDINAL_POSITION";

			$res = 	mysql_query($select_campos_tabla,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select_campos_tabla." - ".mysql_error());
			$i=0;
			$x=0;
			//-----------------------------------------
			//-vectores de campos editables y visibles con permiso
			//-se utilizan basicamente para la parte de edicion
			$vector_campos					=array();
			$vector_tipo_campos				=array();
			$vector_comentario				=array();
			$vector_nombre_diccionario		=array();
			//------------------------------------------
			//-vectores de la tabla completa
			//-se utilizan para la parte de grabacion de nuevos registros
			$vector_campos_completos		=array();
			$vector_tipo_campos_completos	=array();
			$vector_comentario_completos	=array();
			$vector_diccionario_completos	=array();


			while ($row = mysql_fetch_array($res))
			{

				$auxwtabla = explode("_", $wtabla);
				$nom_tabla = $auxwtabla[0];
				$num_tabla = $auxwtabla[1];
				$numero_campo = '';
				$tipo_campo='';
				$comentario ='';
				$nombre_diccionario = '';

				if($visible_todos == '*' OR array_key_exists($row['COLUMN_NAME'],$campos_visibles))
				{

					//----------------------------------------------------
					// buscar campo en det_formulario
					$selec_det_formulario=    " SELECT campo, tipo, comentarios
												  FROM det_formulario
												 WHERE medico = '".$nom_tabla."'
												   AND codigo = '".$num_tabla."'
												   AND descripcion = '".$row['COLUMN_NAME']."' ";

					$res_det_formulario = 	mysql_query($selec_det_formulario,$conex) or die ("Error 2.1: ".mysql_errno()." - en el query: ".$selec_det_formulario." - ".mysql_error());



					if($row_selec_det_formulario = mysql_fetch_array($res_det_formulario))
					{
						$numero_campo 	= $row_selec_det_formulario['campo'];
						$tipo_campo		= $row_selec_det_formulario['tipo'];
						$comentario		= $row_selec_det_formulario['comentarios'];
					}
					//-------------------------------------------------
					//-------------------------------------------------

					//----------------------------------------------------
					// buscar nombre  en diccionario de datos
					if($numero_campo!='')
					{

						$selec_diccionario_datos = "SELECT Dic_Descripcion
													  FROM root_000030
													 WHERE Dic_Usuario = '".$nom_tabla."'
													   AND Dic_Formulario = '".$num_tabla."'
													   AND Dic_Campo = '".$numero_campo."' ";

						$res_diccionario_datos  = 	mysql_query($selec_diccionario_datos,$conex) or die ("Error 2.2: ".mysql_errno()." - en el query: ".$selec_diccionario_datos." - ".mysql_error());

						if($row_diccionario_datos = mysql_fetch_array($res_diccionario_datos))
						{
							$nombre_diccionario = $row_diccionario_datos['Dic_Descripcion'];

						}


					}
					//------------------------------------
					//-----------------------------------------------------------

					//------------


					$td_encabezado 	.= "<th>".$nombre_diccionario."<br><br>(".$row['COLUMN_NAME'].")</th>";
					$campos_tabla  	.= $row['COLUMN_NAME'].",";
					$vector_campos[$i]	 			= $row['COLUMN_NAME'];
					$vector_tipo_campos[$i] 		= $tipo_campo;
					$vector_comentario[$i]  		= $comentario;
					$vector_nombre_diccionario[$i]  = $nombre_diccionario;
					$i++;

					$vector_campos_completos[$x]		= $row['COLUMN_NAME'];
					$vector_tipo_campos_completos[$x]	= $tipo_campo;
					$vector_comentario_completos[$x]	= $comentario;
					$vector_diccionario_completos[$x]	= $nombre_diccionario;
					$x++;

				}
				else
				{

					if($permiso_grabar=='on')
					{
						//----------------------------------------------------
						// buscar campo en det_formulario
						$selec_det_formulario=    " SELECT campo, tipo, comentarios
													  FROM det_formulario
													 WHERE medico = '".$nom_tabla."'
													   AND codigo = '".$num_tabla."'
													   AND descripcion = '".$row['COLUMN_NAME']."' ";

						$res_det_formulario = 	mysql_query($selec_det_formulario,$conex) or die ("Error 2.1: ".mysql_errno()." - en el query: ".$selec_det_formulario." - ".mysql_error());



						if($row_selec_det_formulario = mysql_fetch_array($res_det_formulario))
						{
							$numero_campo 	= $row_selec_det_formulario['campo'];
							$tipo_campo		= $row_selec_det_formulario['tipo'];
							$comentario		= $row_selec_det_formulario['comentarios'];
						}
						//-------------------------------------------------
						//-------------------------------------------------

						//----------------------------------------------------
						// buscar nombre  en diccionario de datos
						if($numero_campo!='')
						{

							$selec_diccionario_datos = "SELECT Dic_Descripcion
														  FROM root_000030
														 WHERE Dic_Usuario = '".$nom_tabla."'
														   AND Dic_Formulario = '".$num_tabla."'
														   AND Dic_Campo = '".$numero_campo."' ";

							$res_diccionario_datos  = 	mysql_query($selec_diccionario_datos,$conex) or die ("Error 2.2: ".mysql_errno()." - en el query: ".$selec_diccionario_datos." - ".mysql_error());

							if($row_diccionario_datos = mysql_fetch_array($res_diccionario_datos))
							{
								$nombre_diccionario = $row_diccionario_datos['Dic_Descripcion'];

							}


						}
						//------------------------------------
						//-----------------------------------------------------------

						//--------
						$vector_campos_completos[$x]		= $row['COLUMN_NAME'];
						$vector_tipo_campos_completos[$x]	= $tipo_campo;
						$vector_comentario_completos[$x]	= $comentario;
						$vector_diccionario_completos[$x]	= $nombre_diccionario;
						$x++;
					}



				}
			}
			$numero_de_campos = $i;
			$campos_tabla = substr ($campos_tabla, 0, strlen($campos_tabla) - 1); // se le quita el ultimo caracter

			//---------------------------------------
			//-construccion del buscador
			$vector = array();
			$html .= "<div id='div_hidden' >";
			for($j=0;$j<count($vector_campos_completos);$j++)
			{


				switch ($vector_tipo_campos_completos[$j]) {

					case 18:
							$wid = 1;
							$valor = '' ;
							$vector[$vector_campos_completos[$j]]= traer_hidden($vector_comentario_completos[$j],$nom_tabla,$valor,$wid,$vector_campos_completos[$j]);
							$cuantos = count($vector[$vector_campos_completos[$j]]);
							//$html.= '<input cuantos="'.$cuantos.'" type="hidden" id="hidden_'.$vector_campos_completos[$j].'" value=\''.json_encode($vector[$vector_campos_completos[$j]]).'\' >';
							
							$html.= '<input cuantos="'.$cuantos.'" type="hidden" id="hidden_'.$vector_campos_completos[$j].'" value="'.$vector[$vector_campos_completos[$j]]['query'].'" principal="'.$vector[$vector_campos_completos[$j]]['principal'].'" campos="'.$vector[$vector_campos_completos[$j]]['campos'].'" where ='.$vector[$vector_campos_completos[$j]]['camposwhere'].'>';
							break;
					case 9:
							$wid = 1;
							$valor = '' ;
							$vector[$vector_campos_completos[$j]]= traer_hidden_tipo9($vector_comentario_completos[$j],$nom_tabla,$valor,$wid,$vector_campos_completos[$j]);
							$html.= "<input type='hidden' id='hidden_".$vector_campos_completos[$j]."' value='".json_encode($vector[$vector_campos_completos[$j]])."'>";
							break;

					case 5:
						  	$wid = 1;
							$valor = '' ;
							//traer_seleccion($vector_comentario_completos, $wtabla);
							//$html.= "<input type='text' id='div_selecciones_".$vector_campos_completos[$j]."' value='".traer_seleccion($vector_comentario_completos[$j], $wtabla)."'>";
							$html.= "<div  style='display:none' id='div_selecciones_".$vector_campos_completos[$j]."' >".traer_seleccion($vector_comentario_completos[$j], $wtabla)."</div>";
							break;

					default:
							$html.= "";

				}
			}
			//print_r($vector);
			$html .= "</div>";
			
			$tempData = str_replace("\\", "",$campos);
			$campos_query = json_decode($tempData);
			$tempData = str_replace("\\", "",$operaciones);
			$operaciones_query = json_decode($tempData);
			$tempData = str_replace("\\", "",$parametros);
			$parametros_query = json_decode($tempData);
			$entro ='no';
			// echo "----------".count($campos_query);
			for ($y=0 ; $y < count($campos_query) ; $y++ )
			{
				$entro = 'si';
				$operacion_a_poner ='';
				$simbolos ='';
				if($operaciones_query[$y] == 'igual' )
				{
					$operacion_a_poner = '=';
				}
				if($operaciones_query[$y] == 'diferente')
				{
					$operacion_a_poner = '!=';
					
				}
				if($operaciones_query[$y] == 'like')
				{
					$operacion_a_poner = 'like';
					$simbolos ='%';
					
				}
				
				if($y==0)
				{
					$html_query.=" ".$campos_query[$y]." ".$operacion_a_poner." '".$simbolos."".$parametros_query[$y]."".$simbolos."'";
				}
				else
				{
					$html_query.=" AND ".$campos_query[$y]." ".$operacion_a_poner." '".$simbolos."".$parametros_query[$y]."".$simbolos."'";
					
				}
				
				
				
			}
			/*if($buscador=='' AND $entro=='no')
			{
				$html.="<br><br><table align='center' id='table_filtros'><tr class='encabezadoTabla'><td colspan='5' align='center'>Buscar Registro2</td></tr>";
				$html.="<tr class='fila1'><td><input type='button' value='+' title='Agregar filtro' onclick='agregar_filtro()' style='cursor:pointer'></td><td id='selectppal'><select numero='1'  class='buscador_campo' id='select_buscador_campo'>";
				$seleccionadorbuscador = '';
				$html.="<option value=''  >Seleccione...</option>";
				for($j=0;$j<count($vector_campos);$j++)
				{
					if($campobuscar == $vector_campos[$j])
						$seleccionadorbuscador ='selected';
					else
						$seleccionadorbuscador = '';

						$html.="<option value='".$vector_campos[$j]."' ".$seleccionadorbuscador."  >".$vector_nombre_diccionario[$j]."(".$vector_campos[$j].") </option>";
				}
				
				$html.="<td id='td_operacion'><select  class='operacion'><option value='igual' selected>=</option>
																	  <option value='like' >Like</option>
																	  <option value='diferente'>!=</option>
															 </select>
														</td>";
				$html.="</td>
							</select>
									<td id='td_parametro_buscar' ><input size='40' numero='1' type='text' class='parametro' id='parametro_buscar' value='".$parametro."'></td>
									<td>&nbsp;&nbsp;</td>
									</tr>
									<tr class='fila1' >
									<td colspan='5' align='center'>
									<input type='button' value='Buscar' onclick='buscar_dato()'>
									<input type='button' value='Limpiar' onclick='limpiar_dato()'>";
									if($permiso_grabar=='on')
									{
										$html.="<input type='button' value='Nuevo' onclick='nuevo_dato()'>";
									}
									$html.="</td>";
							$html.="</tr>
						</table>";
			}*/
			if($buscador=='')
			{
				
				if(count($campos_query)>0)
				{
					$html.="<br><br><table align='center' id='table_filtros'><tr class='encabezadoTabla'><td colspan='5' align='center'>Buscar Registro </td></tr>";
					for($s=0 ; $s < count($campos_query) ; $s++)
					{
						
						if($s==0)
						{
							$html.="<tr class='fila1' id='tr_ppal_".$s."'><td><input type='button' value='+' title='Agregar filtro' onclick='agregar_filtro()' style='cursor:pointer'></td><td id='selectppal'><select   class='buscador_campo' id='select_buscador_campo' onchange='validar_cambio(this)'>";
						}
						else
						{
							$html.="<tr class='fila1' id='tr_ppal_".$s."'><td></td><td id='selectppal'><select   class='buscador_campo' id='select_buscador_campo' onchange='validar_cambio(this)'>";
				
						}
						
						$seleccionadorbuscador = '';
						$html.="<option value=''  >Seleccione...</option>";
						for($j=0;$j<count($vector_campos);$j++)
						{
							if($campos_query[$s] == $vector_campos[$j])
								$seleccionadorbuscador ='selected';
							else
								$seleccionadorbuscador = '';

							$html.="<option value='".$vector_campos[$j]."' ".$seleccionadorbuscador."  >".$vector_nombre_diccionario[$j]."(".$vector_campos[$j].") </option>";
						}
					
						$html.="<td id='td_operacion'><select  class='operacion' id='select_operacion'>";
						$selected_like 		='';
						$selected_igual 	='';
						$selected_diferente ='';
						if( $operaciones_query[$s] == 'like')
						{
								$selected_like 	='selected';
						}
						else if ($operaciones_query[$s] == 'igual')
						{
								$selected_igual ='selected';
						}
						else if ($operaciones_query[$s] == 'diferente')
						{
								$selected_diferente ='selected';
						}
						$html.="<option value='igual' ".$selected_igual.">=</option>
								<option value='like' ".$selected_like.">Like</option>
								<option value='diferente' ".$selected_diferente.">!=</option>
								</select>
								</td>";
						$html.="</td>
									</select>
									<td id='td_parametro_buscar' ><input size='40' numero='1' type='text' class='parametro' id='parametro_buscar' value='".$parametros_query[$s]."'></td>";
						
						if($s==0)
						{
							$html.="<td>&nbsp;&nbsp;</td>";
						}
						else
						{
							$html.="<td><input type='button' onclick='eliminar_filtro($(this).parent().parent())' value='-' title='Eliminar filtro'></td>";
						}
						
						$html.="</tr>";
											
						$html.="</tr>";
					}
					$html .= "<tr class='fila1' >
						<td colspan='5' align='center'>
						<input type='button' value='Buscar' onclick='buscar_dato()'>
						<input type='button' value='Limpiar' onclick='limpiar_dato()'>";
						if($permiso_grabar=='on')
						{
							$html.="<input type='button' value='Nuevo' onclick='nuevo_dato()'>";
						}
				$html.="</td>";
				$html.="</table>";
				}
				else
				{
					$html.="<br><br><table align='center' id='table_filtros'><tr class='encabezadoTabla'><td colspan='5' align='center'>Buscar Registro</td></tr>";
					$html.="<tr class='fila1' id='tr_ppal_0'><td><input type='button' value='+' title='Agregar filtro' onclick='agregar_filtro()' style='cursor:pointer'></td><td id='selectppal'><select   class='buscador_campo' id='select_buscador_campo' onchange='validar_cambio(this)'>";
					$seleccionadorbuscador = '';
					$html.="<option value=''  >Seleccione...</option>";
					for($j=0;$j<count($vector_campos);$j++)
					{
						if($campobuscar == $vector_campos[$j])
							$seleccionadorbuscador ='selected';
						else
							$seleccionadorbuscador = '';

							$html.="<option value='".$vector_campos[$j]."' ".$seleccionadorbuscador."  >".$vector_nombre_diccionario[$j]."(".$vector_campos[$j].") </option>";
					}
					
					$html.="<td id='td_operacion'><select  class='operacion' id='select_operacion'><option value='igual' selected>=</option>
																		  <option value='like' >Like</option>
																		  <option value='diferente'>!=</option>
																 </select>
															</td>";
					$html.="</td>
								</select>
										<td id='td_parametro_buscar' ><input size='40' numero='1' type='text' class='parametro msg_ne' onMouseover='mostrarTooltip( this );' title='parametro' id='parametro_buscar' value='".$parametro."'></td>
										<td>&nbsp;&nbsp;</td>
										</tr>
										<tr class='fila1' >
										<td colspan='5' align='center'>
										<input type='button' value='Buscar' onclick='buscar_dato()'>
										<input type='button' value='Limpiar' onclick='limpiar_dato()'>";
										if($permiso_grabar=='on')
										{
											$html.="<input type='button' value='Nuevo' onclick='nuevo_dato()'>";
										}
										$html.="</td>";
								$html.="</tr></table>";
					
				}
				
				
				
			}
		
		
			$html .= "<br><div id='paginador' style='margin: auto;'></div>";
			//$html .= "<div id='datos' style='overflow-x:scroll;overflow-y:hidden;width:90%;height:90%'>";
			$html .= "<div id='datos' >";




			//--------------------------------------------------------------
			//--------------------------------------------------------------

			//--------------------------------------------------------------
			//--Construccion de tabla
			//--
			//-- Aqui se encuentra el numero de filas y la funcion editar y guardar
			$cambioinicioyfin="off";
			$html .="
						<br><div style='/*overflow-x:scroll;overflow-y:hidden;width:1600px*/'>
                                <table align='center' style='position: relative; border-collapse: collapse;'>
								<tr align='center' class='encabezadoTabla'><td colspan='".(($numero_de_campos*1)+3)."'>".$wnombreopc."</td></tr>
                                <thead>
								<tr class='encabezadoTabla' align='center'>
								<th>#</th>
								<th></th>
									".$td_encabezado."
							   <th></th></tr></thead>";
			//totalizo los datos de la tabla

			$idName = findIdName($wtabla);
			if($wbusqueda=='')
			{
				if($whasta =='')
				{
					//Modificacion para que no traiga todos los datos, lo que hace el navegador no cargue ninguna informacion
					// Freddy Saenz 22 Mayo 2020
					$qcta = "SELECT count(*) as cuenta  FROM ".$wtabla ;
					$rescta = 	mysql_query($qcta,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$seqctalect_tabla." - ".mysql_error());
					$rowsquery = mysql_fetch_array($rescta);
					$contador =  $rowsquery['cuenta'];
					$select_tabla = "SELECT ".$campos_tabla."
									   FROM ".$wtabla." LIMIT 30 ";//22 MAYO 2020

					$res = 	mysql_query($select_tabla,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_tabla." - ".mysql_error());
					//$contador = mysql_num_rows($res);
					$lohago = 'si';
				}
				else
				{
					$contador = $whasta;
					$lohago = 'no';
				}
				$select_tabla = "SELECT ".$campos_tabla." , {$idName}
								   FROM ".$wtabla."
								  ORDER BY {$idName}
								  LIMIT ".$wprincipio.", ".$wfinal."";

			}
			else
			{


					//echo $html_query;
					/*$select_tabla = "SELECT ".$campos_tabla."
									   FROM ".$wtabla."
									  WHERE ".$campobuscar." like '%".$parametro."%' ";*/
					
					$select_tabla = "SELECT ".$campos_tabla."
									   FROM ".$wtabla."
									  WHERE ".$html_query."";

					$res = 	mysql_query($select_tabla,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_tabla." - ".mysql_error());
					$contador = mysql_num_rows($res);


					/*
					$select_tabla = "SELECT ".$campos_tabla." , id
									   FROM ".$wtabla."
								      WHERE ".$campobuscar." like '%".$parametro."%'
								   ORDER BY id
								      LIMIT ".$wprincipio.", ".$wfinal."";*/
					$select_tabla = "SELECT ".$campos_tabla." , {$idName}
								       FROM ".$wtabla."
								      WHERE ".$html_query."
								   ORDER BY {$idName}
								      LIMIT ".$wprincipio.", ".$wfinal."";

				$cambioinicioyfin="on";

			}

			//echo $select_tabla;

			$res = 	mysql_query($select_tabla,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_tabla." - ".mysql_error());
			$i=0;
			$numerador_filas = $wprincipio;
			while($row = mysql_fetch_array($res))
			{
				if (($i%2)==0)
					$wcf="fila1";  // color de fondo de	la fila
				else
					$wcf="fila2"; // color de fondo de la fila


				$html .="<tr class='".$wcf."' id='tr_".$row[$idName]."' >
							<td align='center'>
								".($numerador_filas+1)."
							</td>
							<td nowrap='nowrap'>
								<div  id='div_operacione_".$row[$idName]."'  >
									<div id='editar_".$row[$idName]."'  style='cursor: pointer;' title='Editar' onclick='editar(".$row[$idName].");'><img  src='../../images/medical/hce/mod.PNG'>&nbsp;<font style='color: #235a81'>Editar</font></div>
								</div>
							</td>";
				$numerador_filas = $numerador_filas+1;

				for($j=0; $j<$numero_de_campos ;$j++)
				{

					if (array_key_exists($vector_campos[$j],$campos_editables))
					{
						$atributo = "editar='si'";
					}
					else
					{
						$atributo = "editar='no'";
					}

					if ($campos_editables_comas_aux == '*' )
					{
						$atributo = "editar='si'";
					}


					if($vector_tipo_campos[$j]=='18')
					{
						// se adiciona el atributo title a donde se lleva la descripcion del registro de la tabla relacionada para que se muestre como tooltip. Leandro Meneses 2021-01-14
						$html .="<td nowrap='nowrap' id='td_".$vector_campos[$j]."_".$row[$idName]."' ".$atributo."  nombrecampo='".$vector_campos[$j]."' tipo ='".$vector_tipo_campos[$j]."' comentario='".$vector_comentario[$j]."' tablappal='".$nom_tabla."' valor='".$row["".$vector_campos[$j].""]."' posicion='".$vector_campos[$j]."' nombre='".$vector[$vector_campos[$j]][$row["".$vector_campos[$j].""]]."' class='msg_ne' onMouseover='mostrarTooltip( this );' title='".obtener_tooltip_relacion_ne($vector_comentario[$j],$nom_tabla, $row["".$vector_campos[$j].""])."'  >".substr($row["".$vector_campos[$j].""],0,30)." </td>";
						
					}
					elseif($vector_tipo_campos[$j]=='9')
					{
						$html .="<td nowrap='nowrap' id='td_".$vector_campos[$j]."_".$row[$idName]."' ".$atributo."  nombrecampo='".$vector_campos[$j]."' tipo ='".$vector_tipo_campos[$j]."' comentario='".$vector_comentario[$j]."' tablappal='".$nom_tabla."' valor='".$row["".$vector_campos[$j].""]."' posicion='".$vector_campos[$j]."' nombre='".$vector[$vector_campos[$j]][$row["".$vector_campos[$j].""]]."' >".substr($row["".$vector_campos[$j].""],0,30)."</td>";

					}
					elseif($vector_tipo_campos[$j]=='10')
					{
						$html .="<td nowrap='nowrap' id='td_".$vector_campos[$j]."_".$row[$idName]."' ".$atributo."  nombrecampo='".$vector_campos[$j]."' tipo ='".$vector_tipo_campos[$j]."' comentario='".$vector_comentario[$j]."' tablappal='".$nom_tabla."' valor='".$row["".$vector_campos[$j].""]."' posicion='".$vector_campos[$j]."' nombre='".$vector[$vector_campos[$j]][$row["".$vector_campos[$j].""]]."' >".substr($row["".$vector_campos[$j].""],0,30)."</td>";

					}elseif($vector_tipo_campos[$j]=='3')
					{
						$html .="<td nowrap='nowrap'  id='td_".$vector_campos[$j]."_".$row[$idName]."' ".$atributo."  nombrecampo='".$vector_campos[$j]."' tipo ='".$vector_tipo_campos[$j]."' comentario='".$vector_comentario[$j]."' tablappal='".$nom_tabla."' valor='".$row["".$vector_campos[$j].""]."' posicion='".$vector_campos[$j]."' nombre='".$vector[$vector_campos[$j]][$row["".$vector_campos[$j].""]]."' >".substr($row["".$vector_campos[$j].""],0,30)."</td>";

					}
					else
					{
						$html .="<td nowrap='nowrap' id='td_".$vector_campos[$j]."_".$row[$idName]."' ".$atributo." nombrecampo='".$vector_campos[$j]."' tipo ='".$vector_tipo_campos[$j]."' valor='".$row["".$vector_campos[$j].""]."' >".substr($row["".$vector_campos[$j].""],0,30)."</td>";
					}



				}
				$html .="<td nowrap='nowrap'>
								<div  id='div_operacione2_".$row[$idName]."'  >
									<div id='editar2_".$row[$idName]."'  style='cursor: pointer;' title='Editar' onclick='editar(".$row[$idName].");'><img  src='../../images/medical/hce/mod.PNG'>&nbsp;<font style='color: #235a81'>Editar</font></div>
								</div>
							</td></tr>";
				$i++;
			}
			if($cambioinicioyfin =='on')
			{
				$wprincipio=0;
				$wfinal=$i;
			}
			if(($i*1) < 30)
			{
				$wprincipio=0;
				$wfinal=$i;
				$cambioinicioyfin = 'on';

			}
			$html .="</tr></table><br><br><br></div><input type='hidden' value='".$i."'><input type='hidden'  id='cambioinicioyfin' value='".$cambioinicioyfin."'  ><input type='hidden' id='cambioinicio' value='".$wprincipio."'><input type='hidden' id='cambiofinal' value='".$wfinal."'><input type='hidden' id='whasta' value='".$contador."'>";
			//----------------------------------------
			//----------------------------------------
			$html .="</div>";

			//----Si tiene permiso para crear nuevo registro
			$html2 ='';
			if($buscador=='')
			{
				if($permiso_grabar)
				{

					$html2 .= "<div id='div_nuevo_registro' style='display : none'>
								<br><br><br>
								<table align='center' id='tablaNuevoRegisto'>
									 ";
										for($j=0;$j<count($vector_campos_completos);$j++)
										{

											$html2.= "<tr>";
											if($vector_campos_completos[$j] != $idName && $vector_campos_completos[$j]!= 'ID' && $vector_campos_completos[$j]!='Id') //ojo
											{

												$html2.="<td class='encabezadoTabla' nowrap='nowrap'>".$vector_diccionario_completos[$j]." <br> (".$vector_campos_completos[$j].")</td>";

												// los siguientes  campos vienen  desabilitados por defecto : fecha_data, hora_data , Medico, Seguridad
												if($vector_campos_completos[$j]=='Fecha_data' || $vector_campos_completos[$j]=='fecha_data'  || $vector_campos_completos[$j]=='Fecha_Data')
												{
													$html2.= "<td class='fila1'><input type='text' disabled='disabled' tipo='obligatorio' borrar='no' campo='grabar'   id='new_input_Fecha_data' nombrecampo='".$vector_campos_completos[$j]."' value='".date("Y-m-d")."'></td>";
												}
												else if($vector_campos_completos[$j]=='Hora_data' || $vector_campos_completos[$j]=='Hora_Data' || $vector_campos_completos[$j]=='hora_data' )
												{
													$html2.= "<td class='fila1'><input type='text' disabled='disabled'  tipo='obligatorio' borrar='no' campo='grabar'   id='new_input_Hora_data' nombrecampo='".$vector_campos_completos[$j]."' value='".(string)date("H:i:s")."'></td>";
												}
												else if($vector_campos_completos[$j]=='Seguridad' || $vector_campos_completos[$j]=='seguridad')
												{
													$html2.= "<td class='fila1'><input  type='text' tipo='obligatorio'  borrar='no' campo='grabar' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled' nombrecampo='".$vector_campos_completos[$j]."' value='C-".$wusuario."' ></td>";
												}
												else if($vector_campos_completos[$j]=='Medico' || $vector_campos_completos[$j]=='medico')
												{
													$html2.= "<td class='fila1'><input  type='text' tipo='obligatorio' borrar='no' campo='grabar' id='new_input_".$vector_campos_completos[$j]."'  disabled='disabled' nombrecampo='".$vector_campos_completos[$j]."'  value='".$nom_tabla."' ></td>";
												}
												else
												{
													// apartir de aqui se pintan los campos segun el tipo de dato que traigan de la tabla det_formulario
													switch ($vector_tipo_campos_completos[$j]) {
													case 0:
															// 0 tipo caracter, representado con un imput
															$html2.= "<td class='fila1' ><input type='text' tipo='obligatorio' campo='grabar' id='new_input_".$vector_campos_completos[$j]."' nombrecampo='".$vector_campos_completos[$j]."'>";
															$html2.= "<img   src='../../images/medical/root/info.png' style = 'cursor : pointer' title='Caracteres: \nAcepta letras, numeros, simbolos, espacios. Digite lo que desea guardar\nen este campo' ></td>";
															break;
													case 18:
															// autocompletar 
															$wid = "editar";
															$valor = '' ;
															$html2.= "<td class='fila1'><input tipo='obligatorio' class='buscadores' id='imput_".$vector_campos_completos[$j]."_".$wid."' type='text' tipo_campo='18' nombrecampo='".$vector_campos_completos[$j]."' campo='grabar'>";
															$html2.= "<img   src='../../images/medical/root/info.png' style ='cursor : pointer' title='Autocompletar:\nEste campo se llena digitando una parte de  la  palabra con\nla que se  desea llenar. Recuerde  este campo no es abierto \ny debe llenarse con datos ya estipulados, estos datos hacen \nreferencia a otro maestro' ></td>";
															break;
													case 9:
															// relacion
															$wid = "editar";
															$valor = '' ;
															$html2.= "<td class='fila1'><input tipo='obligatorio' class='buscadores' id='imput_".$vector_campos_completos[$j]."_".$wid."' type='text' tipo_campo='9' nombrecampo='".$vector_campos_completos[$j]."' campo='grabar'>";
															$html2.= "<img   src='../../images/medical/root/info.png'  style ='cursor : pointer' title='Autocompletar:\nEste campo se llena digitando una parte de  la  palabra con\nla que se  desea llenar. Recuerde  este campo no es abierto \ny debe llenarse con datos ya estipulados, estos datos hacen \nreferencia a otro maestro'></td>";
															break;
													case 10:
															// tipo booleano
															$html2.= "<td class='fila1' ><div style='display: inline'><input type='checkbox'  eschecbox='on' campo='grabar'  id='new_input_".$vector_campos_completos[$j]."' nombrecampo='".$vector_campos_completos[$j]."' onclick='onclickcheckgrabar(\"".$vector_campos_completos[$j]."\")' value='off'></div><div condicion='divestado' id='divcheck_".$vector_campos_completos[$j]."' style='display: inline' >off</div>";
															$html2.= "<img   align='right' src='../../images/medical/root/info.png' style ='cursor : pointer' title='Booleano: \nSolo se llena con los siguientes estados On u Off.\nDe click en la casilla segun el estado que necesite'></td>";
															break;

													case 3:
															// fecha
															$wfecha = date("Y-m-d");
															$html2.= "<td class='fila1'><input type='text' disabled='disabled' tipo=obligatorio  campo='grabar'   id='new_input_".$vector_campos_completos[$j]."' nombrecampo='".$vector_campos_completos[$j]."'  tipo_campo='3'>";
															$html2.= "<img   src='../../images/medical/root/info.png' style ='cursor : pointer' title='Fecha:\nDebe seleccionar una fecha'></td>";
															break;
													case 11:
															// hora
															$whora = date("H:i:s");
															$html2.= "<td class='fila1'><input type='text'  tipo=obligatorio  campo='grabar' class='hora1'  id='new_input_".$vector_campos_completos[$j]."' nombrecampo='".$vector_campos_completos[$j]."'  tipo_campo='11' value='".$whora."'>";
															$html2.= "<img   src='../../images/medical/root/info.png' style ='cursor : pointer' title='Hora:\nDebe seleccionar una hora'></td>";
															break;
													case 1:
															// Entero
															$html2.= "<td class='fila1'><input type='text' id='new_input_".$vector_campos_completos[$j]."'  tipo='obligatorio' campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' class='entero'>";
															$html2.= "<img   src='../../images/medical/root/info.png' style ='cursor : pointer' title='Entero:\nEste campo solo acepta numeros Enteros, es decir,\nnumeros positivos o negativos sin punto decimal '></td>";
															break;
													case 2:
															// Real
															$html2.= "<td class='fila1'><input type='text' id='new_input_".$vector_campos_completos[$j]."'  tipo='obligatorio' campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' class='real'>";
															$html2.= "<img   src='../../images/medical/root/info.png' style ='cursor : pointer' title='Real:\nSolo acepta numeros reales'></td>";
															break;
													case 5:
															// Seleccion
															$wid = "editar";
															$valor = '' ;
															$html2.= "<td class='fila1'  ><select id='new_input_".$vector_campos_completos[$j]."'  tipo='obligatorio' campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."'>".traer_seleccion($vector_comentario_completos[$j], $wtabla, $valor)."</select>";
															$html2.= "<img   src='../../images/medical/root/info.png' style ='cursor : pointer' title='Seleccion:\n Trae datos de una seleccion previamente creada.\nDebe seleccionar el registro con el que desea llenar'></td>";
															break;
													case 6:
															// formula
															$html2.= "<td class='fila1'><div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)</div><input type='text' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled'   campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' >";
															$html2.= "</td>";
															break;
													case 7:
															// grafico
															$html2.= "<td class='fila1'><div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)</div><input type='text' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled'   campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' >";
															$html2.= "</td>";
															break;
													case 8:
															// automatico
															$html2.= "<td class='fila1'><div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)</div><input type='text' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled'   campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' >";
															$html2.= "</td>";
															break;
													case 12:
															// algoritmico
															$html2.= "<td class='fila1'><div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)</div><input type='text' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled'   campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' >";
															$html2.= "</td>";
															break;
													case 13:
															// titulo
															$html2.= "<td class='fila1'><div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)</div><input type='text' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled'   campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' >";
															$html2.= "</td>";
															break;
													case 14:
															// hipervinculo
															$html2.= "<td class='fila1'><div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)</div><input type='text' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled'   campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' >";
															$html2.= "</td>";
															break;
													case 15:
															// algoritmico_m
															$html2.= "<td class='fila1'><div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)</div><input type='text' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled'   campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' >";
															$html2.= "</td>";
															break;
													case 16:
															// protegido
															$html2.= "<td class='fila1'><div style='font-size: 10px; color: red'>(Este tipo de dato no es <br> editable por este programa)</div><input type='text' id='new_input_".$vector_campos_completos[$j]."' disabled='disabled'   campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."' >";
															$html2.= "</td>";
															break;
													default:
															$html2.= "<td class='fila1'><input type='text' id='new_input_".$vector_campos_completos[$j]."'  tipo='obligatorio' campo='grabar' nombrecampo='".$vector_campos_completos[$j]."' tipo_campo='".$vector_tipo_campos_completos[$j]."'></td>";

													}
												}


											}
											$html2.="</tr>";
										}

					$html2.="<tr><td colspan='2' align='center'><input type='button' id='botongrabarventanamodal' value='Grabar' class='btn_loading' onclick='grabarNuevoRegistro( \"".$wtabla."\")'><input type='button' value='Cancelar' onclick='cerrardialog()'></td></tr>";

					$html2.=	"   </table>
								<br><br><br>
							  </div>";




				}
			}
			$data = array();
			$data['html']=$html;
			$data['cuantos']=$contador;
			$data['oculto']=$html2;
			return $data;



		}
}

?>
 <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php encabezado("Editar Datos Tabla ".$nombreformulario."", $wactualiz, "clinica"); ?>
			<?php
					$user_session = explode('-',$_SESSION['user']);
					$user_session = (count($user_session) > 1)? $user_session[1] : $user_session[0];
					//$user_session = (strlen($user_session) > 5) ? substr($user_session,-5): $user_session;


					$wusuario = $user_session;
					$html .="<div id='primeradiv'><input type='hidden' id='wusuariotabla' value='".$wusuario."'>";

					// selecciona los maestros que tiene configurado el usuario y son organizados por los nombres de sus tablas , quedando asi agrupados por grupos
					// de tablas matrix 
					$select_tablas = "SELECT Tabopc,Tabtab,id
										FROM root_000105
									   WHERE Tabusu = '".$wusuario."'
										 AND Tabest = 'on' 
								    ORDER BY Tabtab" ;

					if($res = mysql_query($select_tablas,$conex))
					{
						$html .= "<br><br>
										<div width='95%' id='accordionopciones'>
											<h3>Opciones</h3>
												<div><table align='center'>
										<tr class='encabezadoTabla'>
											<td>#</td>
											<td>Nombre de opcion</td>
											<td>Tabla matrix</td>
											<td></td>
										</tr>";


						$k=1;
						while($row = mysql_fetch_array($res))
						{

							if (is_int ($k/2))
							   {
								$wcf="fila1";  // color de fondo de la fila
							   }
							else
							   {
								$wcf="fila2"; // color de fondo de la fila
							   }
							$html .="<tr class='".$wcf."'>";
							$html .="<td>".$k."</td>";
							$html .="<td>".$row['Tabopc']."</td>";
							$html .="<td>".$row['Tabtab']."</td>";
							$html .="<td><input style='cursor : pointer' type='button' onclick='abrir_tabla(\"".$row['Tabtab']."\" , \"".$row['Tabopc']."\" , \"".$row['id']."\" )' value='Ir'></td>";
							$html .="</tr>";
							$k++;
						}
						$html .="</table></div></div>";
					}
					else
					{
						$html .="<div>No tiene asignado ningun permiso para acceder a los maestros de Matrix</div>";
					}

					$html.="<br><br><table align='center'><tr><td><input type='button' value='Cerrar' onclick='cerrar_ventana()'></td></tr></table></div>";

				//	$html .="<br><br><div id='divtabla' style='overflow-x:scroll;overflow-y:hidden;width:90%;height:90%' ></div>";
					$html .="<br><br><div id='divtabla' ></div>";
					$html .="<br><br><div id='oculta'></div>";
					echo $html;
			?>
			
			<?php //FUNCIONALIADAD PARA RECIBIRL LA INFORMACION CUANDO SE UTILIZA ARCHIVO EN VENTANA MODAL
					//Se utilia en los siguientes archivos: 
					// listaDeEspera.php --> Para editar los datos de los pacientes y los examenes 
					//Favor continuar si lo utiliza continuar con esta documentación :) ?>
			<?php  if (isset($_GET['waction'])){ 
				$waction = $_GET['waction']; 
				if ($waction != ''){
					//datos recibidos = `action=abrir_tabla_modal&tablappal=citaslc_000032&wusuariotabla=03150&wnombreopc=Lista de espera&wid=434`
					if (isset($_GET['wemp_pmla'])){ 
						$wemp_pmla_op = $_GET['wemp_pmla'];
					}
					if (isset($_GET['wtabla'])){ 
						$wtabla = $_GET['wtabla'];
					}
					if (isset($_GET['wusuariotabla'])){ 
						$wusuariotabla = $_GET['wusuariotabla'];
					}
					if (isset($_GET['wnombreopc'])){ 
						$wnombreopc = $_GET['wnombreopc'];
					}
					if (isset($_GET['wid'])){ 
						$wid = $_GET['wid'];
					}
					if (isset($_GET['widregistro'])){
						$widregistro = $_GET['widregistro'];
					}
					if (isset($_GET['wcampo'])){
						$wcampo = $_GET['wcampo'];
					}
					if (isset($_GET['woperacion'])){
						$woperacion = $_GET['woperacion'];
					}		
					?>
					<script languaje="javascript">
						var wemp_pmla_op = '<?php echo $wemp_pmla_op; ?>';
						var wtabla = '<?php echo $wtabla; ?>';
						var wusuariotabla = '<?php echo $wusuariotabla; ?>';
						var wnombreopc= '<?php echo $wnombreopc; ?>';
						var wid= '<?php echo $wid; ?>';
						var widregistro= '<?php echo $widregistro; ?>';	
						var wcampo= '<?php echo $wcampo; ?>';	
						var woperacion= '<?php echo $woperacion; ?>';

						//abrir_tabla_modal(wtabla, wopcon, wid, widregistro);
						abrir_tabla_modal(wemp_pmla_op, wtabla, wusuariotabla, wnombreopc, wid, widregistro, wcampo, woperacion);
					</script>
				<?php } ?>
				
			<?php } ?> 
 </body>
</html>