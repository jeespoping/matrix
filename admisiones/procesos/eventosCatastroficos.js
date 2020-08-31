function registrarEventos(){

	// var objJson = cearUrlPorCamposJson( $( "#eventosCatastroficos" ) );
	
	// objJson.accion = "guardarEventos";	//agrego un parametro más
	// objJson.wbasedato = $( "#wbasedato" ).val();
	// objJson.consultaAjax = "";
	
	
	// $.post("pruebas_unix.php",
		// objJson,
		// function(data){
			// //setDatos( data, $( "#tbId" ) );
			
			// if( data.error != '' ){
				// alert( data.error );
			// }
			// else{
				// if( data.mensaje != '' )
					// alert( data.mensaje );
			// }
		// },
		// "json"
	// );
}

/****************************************************************************************************
 * Dejo todos los campos de formulario de eventos catastróficos con sus valores por defecto
 ****************************************************************************************************/
function resetearEventosCatastroficos()
{
	$( "select,textarea,input[type=text],input[type=hidden],input[type=text]", $("#eventosCatastroficos") ).val( '' );
	
	$( "input[type=radio],input[type=checkbox]", $("#eventosCatastroficos") ).attr( 'checked', false );
	
	$( "td[name],input[type=checkbox]", $("#eventosCatastroficos") ).html( '' );
	
	var now = new Date();
	var hora = now.getHours();
	var minutos = now.getMinutes();
	var segundos = now.getSeconds();
	if (hora < 10) {hora='0'+hora}
	if (minutos < 10) {minutos='0'+minutos}
	if (segundos < 10) {segundos='0'+segundos}
	horaActual = hora + ":" + minutos + ":" + segundos;
	
	var contenedor = $( "#eventosCatastroficos" )[0];
	contenedor.lastInfo = '';
	
	$( "[name=det_Cathac_ux_evchor]" ).val( horaActual );
	$( "[name=det_Catfac_ux_evcfec]", contenedor ).val( $( "#fechaAct" ).val() );
		
	marcarAqua( $("#eventosCatastroficos"), 'msgError', 'campoRequerido' );
	// iniciarMarcaAqua( $("#eventosCatastroficos") );
	resetAqua( $("#eventosCatastroficos") );
	
	$( "[name=det_Catzon_ux_evczon]", contenedor ).val( 'U' );
	
	try{
		delete contenedor.lastInfo;	//Borro la propiedad
	}
	catch(e){}
}

/************************************************************************************************
 * Asigna el tipo de evento a un campo oculto
 ************************************************************************************************/
function asignarTipoEvento( tipoEvento ){
	
	$( "[name='det_ux_evccec']", $( "#eventosCatastroficos" ) ).val( tipoEvento );
}

/************************************************************************************************
 * Cierra el formulario de accidentes de transito
 ************************************************************************************************/
function cerrarEventosCatastroficos(){
	$.unblockUI();
	
	var contenedor = $( "#eventosCatastroficos" )[0];
	
	if( contenedor.lastInfo && contenedor.lastInfo != '' ){
	
		if( confirm( 'Desea cerrar el formulario\nLa información nueva ingresada no se tomará en cuenta' ) ){
			$.unblockUI();
			setDatos( contenedor.lastInfo, $( "#eventosCatastroficos" ) );
		}
	}
	else
	{	
			/* si hay algo chequeado solo cierra y conserva la relacion
				esto porque si se hace la relacion y despues desde el boton se abre la lista y no se hacen
				cambios la conserve
			*/
			if ($('#div_eventos_catastroficos').find('input[type=checkbox]').is(':checked'))
			{
				$.unblockUI();
			}
			else
			{
				$( "#ing_caiselOriAte" ).val('');
				resetearEventosCatastroficos();
				$( "#div_accidente_evento").css( {display: "none"} );
				$.unblockUI();
			}
	}
	
	//Muevo nuevamente el datepicker de jquery al formulario principal
	// $('#ui-datepicker-div').ready(function() {
		var popup = document.getElementById('forAdmisiones');
		var datePicker = document.getElementById('ui-datepicker-div');
		popup.appendChild(datePicker);
	// });
	
	//para poner el foco en el select de origen de la atencion cuando se cierre el blockui
	$("#ing_caiselOriAte").focus();
}

/************************************************************************************************
 * Guarda los datos de accidentes de transito
 ************************************************************************************************/
function guardarEventosCatastrofios(){

	var a = $( '#frEvento' ).valid();

	if( a ){
		var validacion = validarCampos( $( "#eventosCatastroficos") );

		if( validacion ){
		// alert("guardar eventos"+consultaEvento);
		  //se coloca esta variable para saber si esta actualizando o guardando, esta variable esta llena si esta consultando
			if (informacionIngresos != "") 
			{
				confirmacion="Si modifica el evento todas las historias relacionadas con este, tomarian el cambio, ¿desea guardarlo?";
				if (confirm(confirmacion))
				{
					$.unblockUI();
					registrarEventos();
					
					var objJson = cearUrlPorCamposJson( $( "#eventosCatastroficos" ) );
					var contenedor = $( "#eventosCatastroficos" )[0];
					
					contenedor.lastInfo = objJson;
					
					//Si se guarda la información, muestro el div correspondiente con el botón para mostrar
					//el formulario de accidentes de transito
					$( "#div_accidente_evento").css( {display: ""} );	//muestro el div que tiene los botones para abrir los formularios
					$( "td", $( "#div_accidente_evento") ).eq(0).css( {display: "none"} );	//oculto el boton de eventos catastróficos
					$( "td", $( "#div_accidente_evento") ).eq(1).css( {display: "none"} );	//oculto el boton de eventos catastróficos
					$( "td", $( "#div_accidente_evento") ).eq(2).css( {display: ""} ); //muestra el boton de lista eventos
					
					//Muevo nuevamente el datepicker de jquery al formulario principal
					// $('#ui-datepicker-div').ready(function() {
						var popup = document.getElementById('forAdmisiones');
						var datePicker = document.getElementById('ui-datepicker-div');
						popup.appendChild(datePicker);
					// });
				}
			}
			else
			{
				
				$.unblockUI();
				registrarEventos();
				
				var objJson = cearUrlPorCamposJson( $( "#eventosCatastroficos" ) );
				var contenedor = $( "#eventosCatastroficos" )[0];
				
				contenedor.lastInfo = objJson;
				
				//Si se guarda la información, muestro el div correspondiente con el botón para mostrar
				//el formulario de accidentes de transito
				$( "#div_accidente_evento").css( {display: ""} );	//muestro el div que tiene los botones para abrir los formularios
				$( "td", $( "#div_accidente_evento") ).eq(0).css( {display: "none"} );	//oculto el boton de eventos catastróficos
				$( "td", $( "#div_accidente_evento") ).eq(1).css( {display: ""} );	//se muestra el boton de eventos catastróficos con la informacion del evento
				$( "td", $( "#div_accidente_evento") ).eq(2).css( {display: "none"} ); //se oculta la lista de eventos
				//Muevo nuevamente el datepicker de jquery al formulario principal
				// $('#ui-datepicker-div').ready(function() {
					var popup = document.getElementById('forAdmisiones');
					var datePicker = document.getElementById('ui-datepicker-div');
					popup.appendChild(datePicker);
				// });
			}
			
		}
		else{
			alert( "Faltan datos por ingresar." );
		}
	}
	else{
		alert( "Faltan datos por ingresar." );
	}
	
	//para poner el foco en el select de origen de la atencion cuando se cierre el blockui
	$("#ing_caiselOriAte").focus();
}

function mostrarEventosCatastroficos(){
// alert("entro mostrar eventos");
	// //mostrar siempre ese boton porque se oculta en mostrarDetalleEventosCatastroficos()
	// $("#btnGuardarEventosCatastroficos").show();
	
	//Reseteo los accidentes de tránsito, esto por que no se puede tener eventos catastróficos
	//y accdientes de tránsito diligenciados a la vez
	resetearAccidentes();

	//De aquí en adelante es lo que se requiere para abrir eventos catastroficos
	$( ".ui-helper-reset", $( "#eventosCatastroficos" ) ).css( "font-size", "95%" );
	
	$( ".ui-widget", $( "#eventosCatastroficos" ) ).css( "font-family", "verdana" );

	$.blockUI({ 
		message: $( "#eventosCatastroficos" ), 
		css: { 	top: ($(window).height()*0.2) /2 + 'px',
				left: ($(window).width()*0.2) /2 + 'px',
				width: "80%",
				heightStyle: "content",
				textAlign: "left",
				cursor: ""
			}
	});
	
	//Muevo el datepicker al div con id accidentesTransito
	//Esto para que no molesta en el blockUI el datepicker
	// $('#ui-datepicker-div').ready(function() {
		var popup = document.getElementById('eventosCatastroficos');
		var datePicker = document.getElementById('ui-datepicker-div');
		popup.appendChild(datePicker);
	// });
}

$(function(){
   $( '#frEvento' ).validate({
		rules :{
			detDirEvento : {
				required : {
					depends : function(element){
						if( $('#dirEvento').val() == $('#dirEvento').attr( "msgerror" ) && $('#detDirEvento').val() == $('#detDirEvento').attr( "msgerror" ) ){
							$('#detDirEvento').val( '' );
							$('#detDirEvento').attr( "restAqua", $('#detDirEvento').attr( "msgError" ) );
							$('#detDirEvento').removeAttr( "msgError" );
							$('#detDirEvento').removeClass( "camporequerido" );
						}
						return ($('#dirEvento').val() == $('#dirEvento').attr( "msgerror" ));
					}
				}
			}
		}, //rules
		messages : {
			detDirEvento : "Debe ingresar la direccion o el detalle de la direccion" //dir pac
		}, //messages
		errorClass : "errorMensajes",
		validClass: "mensajeValido"
	});
});