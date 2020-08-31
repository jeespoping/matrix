function registrarAccidentes(){

	// var objJson = cearUrlPorCamposJson( $( "#accidentesTransito" ) );

	// objJson.accion = "guardarAccidentes";	//agrego un parametro más
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

function validarEstadoAseguramiento(obj){
	obj = jQuery(obj);
	var valor = obj.val();
	//Si es fantasma o vehiculo en fuga, algunos datos dejan de ser obligatorios
	if( valor == "F" || valor == "V" || valor == "N" ){

		$("#tabla_datos_propietario :input, #tabla_datos_conductor :input, #tabla_datos_vehiculo :input").each(function(){
			var msgerror = $(this).attr("msgerror");
			if( msgerror != undefined ){
				$(this).removeClass("campoRequerido");
				$(this).removeAttr("msgerror");
				$(this).attr("msgAqua", msgerror);
				this.aqAttr = "msgAqua";
				this.aqClase = "inputblank";
			}

			//Para quitar la dependencia
			var depend = $(this).attr("depend");
			if( depend != undefined ){
				$(this).removeAttr("depend");
				$(this).attr("depend2",depend);
			}
		});

		$( "#tabla_datos_propietario input" ).removeAttr( "msgerror" );
		resetAqua( $("#tabla_datos_propietario,#tabla_datos_conductor,#tabla_datos_vehiculo") );



		$.post("accidentesTransito.php",
			{accion      : 'cargarEntidadFosyga',
			consultaAjax: '',
			wbasedato   :$('#wbasedato').val(),
			wemp_pmla   :$('#wemp_pmla').val()
			},
			function(data){
				if( isJSON(data) == false ){
					alert("ERROR!\nRESPUESTA NO ESPERADA\n"+data);
					obj.val("");
					return;
				}
				data = $.parseJSON(data);

				if( data.error == 1 ){
					alert( data.mensaje );
					obj.val("");
				}
				else
				{
					$("[name=_ux_accasn]").val( data.des );
					$("[name=dat_Acccas_ux_acccas]").val( data.html );
					buscarPrimerResp();
				}
			}
		);

		if( $("#cambioConsorcio").val() == "on" )
			$("#div_ima_cambio_consorcio").hide();

	}else{
		$("#tabla_datos_propietario :input, #tabla_datos_conductor :input, #tabla_datos_vehiculo :input").each(function(){
			var msgerror = $(this).attr("msgAqua");
			if( msgerror != undefined ){
				$(this).addClass("campoRequerido");
				$(this).removeAttr("msgAqua");
				$(this).attr("msgerror", msgerror);
				this.aqAttr = "msgerror";
				this.aqClase = "campoRequerido";
			}

			var depend = $(this).attr("depend2");
			if( depend != undefined ){
				$(this).removeAttr("depend2");
				$(this).attr("depend",depend);
			}
		});
		resetAqua( $("#tabla_datos_propietario,#tabla_datos_conductor,#tabla_datos_vehiculo") );
	}
}

/**
 * Para mantener la información de lo último digitado, se crea en el contenedor accidentesTransito, el cual es un div;
 * una propiedad nueva, este es un objeto JSON que contiene toda la información guardada antes de abrir el formulario
 * Esto se hace para que cuando se abra por segunda vez el formulario, el usuario pueda volver a ver la información
 * que tenía antes de cerrar el formulario
 */

function resetearAccidentes()
{
	$( "select,textarea,input[type=text],input[type=hidden],input[type=text]", $("#accidentesTransito") ).val( '' );

	$( "select,textarea,input[type=text],input[type=text]", $("#accidentesTransito") ).attr( "disabled", false );

	$( "input[type=radio],input[type=checkbox]", $("#accidentesTransito") ).attr( 'checked', false );

	$( "td[name],input[type=checkbox]", $("#accidentesTransito") ).html( '' );

	//Dejo datos por defecto
	$( "#dat_Acczon", $( "#accidentesTransito" ) ).val( "U" );	//En zona el dato por defecto es Urbana

	var now = new Date();
	var hora = now.getHours();
	var minutos = now.getMinutes();
	var segundos = now.getSeconds();
	if (hora < 10) {hora='0'+hora}
	if (minutos < 10) {minutos='0'+minutos}
	if (segundos < 10) {segundos='0'+segundos}
	horaActual = hora + ":" + minutos + ":" + segundos;

	var contenedor = $( "#accidentesTransito" )[0];
	contenedor.lastInfo = '';

	$( "#dat_Acchor" ).val( horaActual );
	$( "#dat_Accfec", contenedor ).val( $( "#fechaAct" ).val() );


	marcarAqua( $("#accidentesTransito"), 'msgError', 'campoRequerido' );

	// iniciarMarcaAqua( $("#accidentesTransito") );
	resetAqua( $("#accidentesTransito") );
	$("select,textarea,input[type=text],input[type=text]", $("#accidentesTransito")).addClass("campoRequerido");

	try{
		delete contenedor.lastInfo;	//Borro la propiedad
	}
	catch(e){}
}

/************************************************************************************************
 * Cierra el formulario de accidentes de transito
 ************************************************************************************************/
function cerrarAccidentes(){


	var contenedor = $( "#accidentesTransito" )[0];

	if( contenedor.lastInfo && contenedor.lastInfo != '' ){

		if( confirm( 'Desea cerrar el formulario\nLa información nueva ingresada no se tomará en cuenta' ) ){
			$("[name='dat_Acccas_ux_acccas']").val( $("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal") );
			$("[name='_ux_accasn']").val( $("[name='_ux_accasn']").attr("empresaOriginal") );
			buscarPrimerResp();

			$.unblockUI();
			setDatos( contenedor.lastInfo, $( "#accidentesTransito" ) );
			if(  $("#cambioConsorcio").val() == "on"  ){
				$("#div_ima_cambio_consorcio").hide();
				$("#cambioConsorcio").val( "off" );
			}

		}
	}
	else{

		if(  $("#cambioConsorcio").val() == "on"  ){
			$("#div_ima_cambio_consorcio").hide();
			$("#cambioConsorcio").val( "off" );
		}

		$( "#ing_caiselOriAte" ).val('');
		resetearAccidentes();

		var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
		validarTipoResp(objetoRes[0]);
		reOrdenarResponsables();
		//validarTipoResp();//se agrega
		$( "#div_accidente_evento").css( {display: "none"} );

		$.unblockUI();
	}

	//Muevo nuevamente el datepicker de jquery al formulario principal
	// $('#ui-datepicker-div').ready(function() {
		var popup = document.getElementById('forAdmisiones');
		var datePicker = document.getElementById('ui-datepicker-div');
		popup.appendChild(datePicker);
	// });
}

/************************************************************************************************
 * Guarda los datos de accidentes de transito
 ************************************************************************************************/
function guardarAccidentes(){

	if( ( $("#cambioConsorcio").val() == "on" ) && ( $("#div_ima_cambio_consorcio").is(":visible") ) ){
		alert( "DEBE CAMBIAR EL CODIGO DE CONSORCIO" );
		return;
	}
	var a = $( '#frAcc' ).valid();
	// iniciarMarcaAqua( $( '#forAdmisiones' ) );
	if( a ){

		var validacion = validarCampos( $( "#accidentesTransito"), true );

		if( camposConError != undefined && camposConError[0] != undefined){
			//Si el detalle de la direccion del propietario es el unico campo que falta, se deja pasar
			if( (camposConError[0].name == 'dat_Accpdd_ux_acpdir' || camposConError[0].name == 'dat_Acccdd_ux_accdir') && camposConError.length == 1){
				validacion = true;
			}
		}

		restaurarMsgError( $( "#accidentesTransito" ) );

		if( validacion ){

			$.unblockUI();
			registrarAccidentes();

			var objJson = cearUrlPorCamposJson( $( "#infDatosAcc" ) );
			var contenedor = $( "#accidentesTransito" )[0];

			contenedor.lastInfo = objJson;

			//Si se guarda la información, muestro el div correspondiente con el botón para mostrar
			//el formulario de accidentes de transito
			$( "#div_accidente_evento").css( {display: ""} );	//muestro el div que tiene los botones para abrir los formularios
			$( "td", $( "#div_accidente_evento") ).eq(1).css( {display: "none"} );	//oculto el boton de eventos catastróficos
			$( "td", $( "#div_accidente_evento") ).eq(0).css( {display: ""} );	//oculto el boton de eventos catastróficos
			$( "td", $( "#div_accidente_evento") ).eq(2).css( {display: "none"} ); //se oculta el tercer boton que es la lista de eventos

			// $('#ui-datepicker-div').ready(function() {
				var popup = document.getElementById('forAdmisiones');
				var datePicker = document.getElementById('ui-datepicker-div');
				popup.appendChild(datePicker);
			// });
		}
		else{
			alert( "Faltan datos por ingresar." );
		}
	}
	else{
		alert( "Faltan datos por ingresar." );
	}

	iniciarMarcaAqua( $( '#forAdmisiones' ) );

	if( $("#dat_Accfec").val() != $("#dat_Accfec").attr("fechaOriginal") ){
		buscarPrimerResp();
	}
	if( !$("[name='pac_remradPacRem'][value='S']").is(":checked") ){
		$("#ing_vretxtValRem").attr("disabled", "disabled");
	}else{
		$("#ing_vretxtValRem").removeAttr("disabled");
	}
}

function mostrarAccidentesTransito(){

	//Reseteo los eventos catastróficos, esto por que no se puede tener eventos catastróficos
	//y accdientes de tránsito diligenciados a la vez
	resetearEventosCatastroficos();

	//Asigno los valores de la tabla de información demográfica
	$( "[name=historia]", $( "#accidentesTransito" ) ).html( $( "#ing_histxtNumHis" ).val()+"-"+$( "#ing_nintxtNumIng" ).val() );
	$( "[name=identificacion]", $( "#accidentesTransito" ) ).html( $( "#pac_tdoselTipoDoc" ).val()+" "+$( "#pac_doctxtNumDoc" ).val() );
	$( "[name=nombre]", $( "#accidentesTransito" ) ).html( $( "#pac_no1txtPriNom" ).val() + " " + $( "#pac_no2txtSegNom" ).val() + " " + $( "#pac_ap1txtPriApe" ).val() + " " + $( "#pac_ap2txtSegApe" ).val() );
	$( "[name=fecNac]", $( "#accidentesTransito" ) ).html( $( "#pac_fnatxtFecNac" ).val() );
	$( "[name=direccion]", $( "#accidentesTransito" ) ).html( $( "#pac_dirtxtDirRes" ).val() );

	$( "[name=dat_Acchis]", $( "#hiocultos", $( "#accidentesTransito" ) ) ).val( $( "#ing_histxtNumHis" ).val() );
	$( "[name=dat_Accing]", $( "#hiocultos", $( "#accidentesTransito" ) ) ).val( $( "#ing_nintxtNumIng" ).val() );

	$( "#accidentesTransito" ).css( {height: $(window).height()*0.95+'px'} );

	$( "#infDatosAcc" ).css( {height: $(window).height()*0.92+'px',
		overflow: "auto"
	} );

	$( "th", $( "#accidentesTransito" ) ).css( {fontWeight: "normal" } );

	// $( ".ui-helper-reset", $( "#accidentesTransito" ) ).css( "line-height", "0.5" );
	$( ".ui-helper-reset", $( "#accidentesTransito" ) ).css( "font-size", "95%" );

	$( ".ui-widget", $( "#accidentesTransito" ) ).css( "font-family", "verdana" );

	$.blockUI({
		message: $( "#accidentesTransito" ),
		css: { 	top: ($(window).height()*0.05) /2 + 'px',
				left: ($(window).width()*0.025) /2 + 'px',
				width: "90%",
				heightStyle: "content",
				textAlign: "left",
				cursor: ""
			}
	});

	//$( "div[acordeon]", $( "#accidentesTransito" ) ).accordion( "option", "active", false );
	//$( "div[acordeon]", $( "#accidentesTransito" ) ).eq(0).accordion( "option", "active", 0 );

	//$( "H3", $( "div[acordeon]" ) ).attr( "acclick", "false" );

	//Muevo el datepicker al div con id accidentesTransito
	//Esto para que no molesta en el blockUI el datepicker
	// $('#ui-datepicker-div').ready(function() {
		var popup = document.getElementById('accidentesTransito');
		var datePicker = document.getElementById('ui-datepicker-div');
		popup.appendChild(datePicker);
	// });

	// $( "#accidentesTransito [name=dat_Accase_ux_accase]" ).change();

	$( "[name=dat_Accap2_ux_acpap2]" ).removeAttr( "msgError" ).removeClass("campoRequerido");
	$( "[name=dat_Accno2_ux_acpno2]" ).removeAttr( "msgError" ).removeClass("campoRequerido");
	$( "[name=dat_Accca2_ux_acpac2]" ).removeAttr( "msgError" ).removeClass("campoRequerido");
	$( "[name=dat_Acccn2_ux_acpnc2]" ).removeAttr( "msgError" ).removeClass("campoRequerido");

	cargarAccidentesPaciente();

	validarObligatorios($("[name='dat_Acctid_ux_acptid']"));
}

function cargarAccidentesPaciente(){

	var documento = $( "#pac_doctxtNumDoc" ).val();
	var tipodoc = $( "#pac_tdoselTipoDoc" ).val();

	if( documento == "" || tipodoc == "" ){
		$("#lista_accidentes").html("");
		return;
	}

	if($("#tabla_lista_accidentes").length > 0){
		if( $("#accidente_previo").val() != "" ){ //ya se cargo la lista de accidentes
			return;
		}
	}

	$.post("accidentesTransito.php",
		{accion      : 'cargarAccidentesPaciente',
		consultaAjax: '',
		tipodoc     : tipodoc,
		documento   : documento,
		wbasedato   :$('#wbasedato').val(),
		wemp_pmla   :$('#wemp_pmla').val()
		},
		function(data){

			if( isJSON(data) == false ){
				alert("RESPUESTA NO ESPERADA\n"+data);
				return;
			}
			data = $.parseJSON(data);

			if( data.error == 1 ){
				alert( data.mensaje );
			}
			else
			{
				if( data.mensaje != '' ){
					alert( data.mensaje );
				}
				$("#lista_accidentes").html( data.html );
				if( $.trim( data.html ) == "" ){ //No trajo accidentes previos, resetear formulario
					nuevoAccidenteTransito();
				}
			}
			$("#dat_Accfec").attr("fechaOriginal", $( "#dat_Accfec" ).val() );
			$("[name='_ux_accasn']").attr("empresaOriginal", $( "[name='_ux_accasn']" ).val() );
			$("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal", $( "[name='dat_Acccas_ux_acccas']" ).val() );
		}
	);
}

function parpadear(){
	$('#div_ima_cambio_consorcio').fadeIn(500).delay(250).fadeOut(500, parpadear) ;
}

function cargarDatosAccidente(his,ing){

	$.post("admision_erp.php",
		{accion      : 'cargarDatosAccidente',
		consultaAjax: '',
		historia     : his,
		ingreso     : ing,
		wbasedato   :$('#wbasedato').val(),
		wemp_pmla   :$('#wemp_pmla').val()
		},
		function(data){

			if( isJSON(data) == false ){
				alert("RESPUESTA NO ESPERADA\n"+data);
				return;
			}
			data = $.parseJSON(data);
			if( data.error == 1 ){
				alert( data.mensaje );
			}
			else
			{
				if( data.mensaje != '' ){
					alert( data.mensaje );
				}
				$("#cambioConsorcio").val( data.infoing[0]['cambioConsorcio'] );
				setDatos( data.infoing[0], $( "#accidentesTransito" ), 'name' );
				resetAqua( $( "#accidentesTransito" ) );
				if( $("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
					$("input[name='dat_Accpol_ux_accpol']").val("");

				//Si no tiene aseguradora, puede ser Fantasma o Vehiculo en Fuga, y la funcion validarEstadoAseguramiento se encarga de traer la entidad fosyga
				//if( $.trim( $("[name=dat_Acccas_ux_acccas]").val() ) == "" ){

				//if( $("[name=dat_Accase_ux_accase]").val() == "V" || $("[name=dat_Accase_ux_accase]").val() == "F" || $("[name=dat_Accase_ux_accase]").val() == "N" ){
				if( $.trim( $("[name=dat_Acccas_ux_acccas]").val() ) == "" ){
					var objetoRes = $("[name=dat_Accase_ux_accase]").eq(0);
					validarEstadoAseguramiento(objetoRes[0]);
				}else{
					buscarPrimerResp();
				}

				$("#accidente_previo").val(ing);
				$( "select,textarea,input[type=text],input[type=hidden],input[type=text],input[type=radio]", $("#accidentesTransito") ).attr("disabled",true);
				$("#dat_Accpdd_ux_acpdir,#dat_Acccdd").each(function(){
					var msgerror = $(this).attr("msgerror");
					if( msgerror != undefined ){
						$(this).removeClass("campoRequerido");
						$(this).removeAttr("msgerror");
						$(this).attr("msgAqua", msgerror);
						this.aqAttr = "msgAqua";
						this.aqClase = "inputblank";
					}
				});
			}

			if( $("#cambioConsorcio").val() == "on" ){
				$("#div_ima_cambio_consorcio").show();
			}else{
				$("#div_ima_cambio_consorcio").hide();
			}
		}
	);
}

function nuevoAccidenteTransito(){
	//Quitar el checked a los radios de accidentes
	$("[name=sel_acc_prev]").attr("checked",false);
	//DESBLOQUEAR INPUTS

	$("#accidente_previo").val(""); //NO ES REINGRESO DE NINGUN ACCIDENTE
	$( "select,textarea,input[type=text],input[type=hidden],input[type=text],input[type=radio]", $("#accidentesTransito") ).attr("disabled",false);
	resetearAccidentes();
}

$(document).ready(function() {

	// $( "select" ).mouseover(function(){
		// alert( "Hola...." )
	// });

	$( "div[acordeon]" ).accordion({
		collapsible: true,
		heightStyle: "content"
	});

	$( "div[acordeon1]" ).accordion({
		collapsible: false,
		heightStyle: "content",
		icons: false
	});

	$( "#div_accidente_evento").css( {display: "none"} );
	parpadear();

	//2014-05-06 se cambia para evitar conflictos, la funcion de change se llevo a validarOrigenAte
	/*$( "#ing_caiselOriAte" ).on({
		click: function(){
			this.lastValue = this.value;
		},
		change: function(){

			if( this.lastValue == '02' || this.lastValue == '06' ){

				if( this.lastValue == '02' ){

					var contenedor = $( "#accidentesTransito" )[0];

					if( contenedor.lastInfo && contenedor.lastInfo != '' ){

						if( confirm( "Desea ignorar los cambios realizados en accidentes de tránsito?" ) ){
							resetearAccidentes();
							$( "#div_accidente_evento").css( {display: "none"} );
						}
						else{
							this.value = '02';
						}
					}
				}
				else{

					var contenedor = $( "#eventosCatastroficos" )[0];

					if( contenedor.lastInfo && contenedor.lastInfo != '' ){

						if( confirm( "Desea ignorar los cambios realizados en eventos catastróficos?" ) ){
							resetearEventosCatastroficos();
							$( "#div_accidente_evento").css( {display: "none"} );
						}
						else{
							this.value = '06';
						}
					}
				}
			}
		}
	});*/

	$( "#ing_caiselOriAte" ).on({
		click: function(){
			this.lastValue = this.value;
		}
	});

	// formatoCampos();

	// $( "h3", $( "#accidentesTransito" ) ).click(function( x ){
	/*$( "h3" ).click(function( x ){
		$( this ).attr( "acclick", "true" );
	}).focus(function(){

		var thisact = this;

		setTimeout( function(){

			if( $( thisact ).attr( "acclick" ) == "false" ){

				$( thisact ).attr( "acclick", "false" );

				if( $( thisact.parentNode ).attr( "acordeon" ) != undefined  ){
					var active = $( thisact.parentNode ).accordion( "option", "active" );

					if( active !== false ){
						//activado
						// alert( "Esta desactivado" );
					}
					else{
						//desactivado
						//alert( "Esta activado" );

						try{
							$( thisact.parentNode.previousSibling ).accordion( "option", "active", false );
							//$( thisact ).click();
							$( thisact.parentNode ).accordion( "option", "active", 0 );
							$( thisact ).attr( "acclick", "false" );

							var objs = $( "*", $( "div", thisact.parentNode ) );
							var len = objs.length;

							for( var x = 0; x < len; x++ ){

								switch( objs[x].tagName.toLowerCase() ){
									case 'select':
									case 'input':
									case 'textarea':
										objs[x].focus();
										return;
									break;
									default: break;
								}
							}
						}
						catch(e){
						}
					}
				}
			}
			setTimeout(function(){
				$( "[acclick]" ).attr( "acclick", "false" ) ;
			}, 100 );
		}, 100 );
	});*/

	resetearAccidentes();
	resetearEventosCatastroficos();

	/********************************************************************************
	 * Agosto 20 de 2013
	 ********************************************************************************/
	$( "[fecha]" ).on({

		change: function(){
			if( $( this ).val() != '' || $( this ).val() == $( this ).attr( this.aqAttr ) ){
				$( this ).removeClass( "campoRequerido" );
			}
		}
	});
	/********************************************************************************/

	setTimeout(function(){

		/*VIGENCIA: AL SELECCIONAR EL ANIO INICIAL, CALCULAR EL ANIO FINAL Y PONERLO*/
		$("[name=dat_Accvfi_ux_accfin]").datepicker( "destroy" );
		$("[name=dat_Accvfi_ux_accfin]").datepicker({
			dateFormat:"yy-mm-dd",
			fontFamily: "verdana",
			dayNames: [ "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo" ],
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
			dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			dayNamesShort: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
			changeMonth: true,
			changeYear: true,
			yearRange: "c-100:c+100",
			onClose: function( selectedDate ) {
				/*selectedDate = selectedDate.split("-");
				var anio = selectedDate[0];
				anio = parseInt(anio)+1;
				selectedDate = anio +"-"+selectedDate[1]+"-"+selectedDate[2];*/
				selectedDate = $.trim( selectedDate );
				$( "[name=dat_Accvff_ux_accffi]" ).attr("disabled", "disabled");
				$.ajax({
				        url: "accidentesTransito.php",
				       type: "POST",
				      async: false,
				       data: {
				                   accion: "calcularVigencia",
				              fechaInicio: selectedDate,
				               fechaFinal: ""
				            },
				      success: function(data){
				      	$( "[name=dat_Accvff_ux_accffi]" ).datepicker( "setDate", data.fechaFinal );
						$( "[name=dat_Accvff_ux_accffi]" ).removeClass("campoRequerido ");
				      },
				      dataType: "json"

				});
			}
		});

		/*VIGENCIA: AL INGRESAR MANUAL EL ANIO INICIAL, CALCULAR EL ANIO FINAL Y PONERLO*/
		/*$("[name=dat_Accvfi_ux_accfin]").focusout(function(){
				if( $("[name=dat_Accvff_ux_accffi]").val() == "" ){
					selectedDate = $.trim( selectedDate );
					$.ajax({
				        url: "accidentesTransito.php",
				       type: "POST",
				      async: false,
				       data: {
				                   accion: "calcularVigencia",
				              fechaInicio: selectedDate,
				               fechaFinal: ""
				            },
				      success: function(data){
				      	$( "[name=dat_Accvff_ux_accffi]" ).datepicker( "setDate", data.fechaFinal );
						$( "[name=dat_Accvff_ux_accffi]" ).removeClass("campoRequerido ");
				      },
				      dataType: "json"

				});
			}
		});*/

		/*VIGENCIA: AL SELECCIONAR EL ANIO FINAL, CALCULAR EL ANIO INCIAL Y PONERLO*/
		$("[name=dat_Accvff_ux_accffi]").datepicker( "destroy" );
		$("[name=dat_Accvff_ux_accffi]").datepicker({
			dateFormat:"yy-mm-dd",
			fontFamily: "verdana",
			dayNames: [ "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo" ],
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
			dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			dayNamesShort: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
			changeMonth: true,
			changeYear: true,
			yearRange: "c-100:c+100",
			onClose: function( selectedDate ) {
				/*selectedDate = selectedDate.split("-");
				var anio = selectedDate[0];
				anio = parseInt(anio)-1;
				selectedDate = anio +"-"+selectedDate[1]+"-"+selectedDate[2];
				$( "[name=dat_Accvfi_ux_accfin]" ).datepicker( "setDate", selectedDate );
				$( "[name=dat_Accvfi_ux_accfin]" ).removeClass("campoRequerido ");*/
				$.ajax({
				        url: "accidentesTransito.php",
				       type: "POST",
				      async: false,
				       data: {
				                   accion: "calcularVigencia",
				              fechaInicio: "",
				              fechaFinal : selectedDate
				            },
				      success: function(data){
				      	$( "[name=dat_Accvfi_ux_accfin]" ).datepicker( "setDate", data.fechaInicio );
						$( "[name=dat_Accvfi_ux_accfin]" ).removeClass("campoRequerido ");
				      },
				      dataType: "json"

				});

			}
		});


	}, 3000);


	// //Si el vehiculo es asegurdado se obliga a pedir la aseguradora, de lo contrario no
	// $( "#accidentesTransito [name=dat_Accase_ux_accase]" ).on({
		// change: function(){
			// if( $( this ).val() != 'A' ){
				// $( "[name=_ux_accasn],[name=dat_Accpol_ux_accpol],[name=dat_Accvfi_ux_accfin],[name=dat_Accvff_ux_accffi]" ).attr( "disabled", true );
				// $( "[name=_ux_accasn],[name=dat_Accpol_ux_accpol],[name=dat_Accvfi_ux_accfin],[name=dat_Accvff_ux_accffi],[name=dat_Acccas_ux_accres]" ).val( "" );
				// $( "[name=_ux_accasn],[name=dat_Accpol_ux_accpol],[name=dat_Accvfi_ux_accfin],[name=dat_Accvff_ux_accffi]" ).blur();
				// $( "[name=_ux_accasn],[name=dat_Accpol_ux_accpol],[name=dat_Accvfi_ux_accfin],[name=dat_Accvff_ux_accffi]" ).removeClass( "campoRequerido" );
			// }
			// else{

				// $( "[name=_ux_accasn],[name=dat_Accpol_ux_accpol],[name=dat_Accvfi_ux_accfin],[name=dat_Accvff_ux_accffi]" ).addClass( "campoRequerido" );
				// $( "[name=_ux_accasn],[name=dat_Accpol_ux_accpol],[name=dat_Accvfi_ux_accfin],[name=dat_Accvff_ux_accffi]" ).removeAttr( "disabled" );
				// $( "[name=_ux_accasn],[name=dat_Accpol_ux_accpol],[name=dat_Accvfi_ux_accfin],[name=dat_Accvff_ux_accffi]" ).blur();
			// }
		// }
	// });
})

function validarObligatorios(obj){
	selected = $(obj).find("option:selected").val();
	if( selected == "NI" ){
		$(obj).parent().parent().find("[aplicanit='no']").removeClass('campoRequerido');
		$(obj).parent().parent().find("[aplicanit='no']").attr('omitirRequerido', "si");
		$(obj).parent().parent().find("[aplicanit='no']").parent().hide();
		$("[name = 'dat_Accap1_ux_acpap1']").parent().attr("colspan","4");
		$("[name = 'dat_Accap1_ux_acpap1']").css("width","100%");
		$("[name = 'dat_Accap1_ux_acpap1']").attr("msgError","Nombre de la compañia");
		$("[titulo='propietario_nit']").show();
		$("[titulo='propietario_pn']").hide();
	}else{
		$(obj).parent().parent().find("[aplicanit='no']").removeAttr('omitirRequerido');
		$(obj).parent().parent().find("[aplicanit='no']").addClass('campoRequerido');
		$(obj).parent().parent().find("[aplicanit='no']").parent().show();
		$("[name = 'dat_Accap1_ux_acpap1']").parent().attr("colspan","1");
		$("[name = 'dat_Accap1_ux_acpap1']").css("width","100%");
		$("[name = 'dat_Accap1_ux_acpap1']").attr("msgError","Apellido 1");
		$("[titulo='propietario_nit']").hide();
		$("[titulo='propietario_pn']").show();
	}
}

function restaurarMsgError( contenedor ){

	$( "[restAqua]", contenedor ).each(function(x){
		$('#dat_Accpdd_ux_acpdir').attr( "msgError", $('#dat_Accpdd_ux_acpdir').attr( "restAqua" ) );
		$('#dat_Accpdd_ux_acpdir').removeAttr( "restAqua" );
	});

}

$(function(){
   $( '#frAcc' ).validate({
		rules :{
			dat_Accpdd_ux_acpdir : {
				required : {
					depends : function(element){
						if( $('#dat_Accpdi_ux_acpdir').val() == $('#dat_Accpdi_ux_acpdir').attr( "msgerror" ) && $('#dat_Accpdd_ux_acpdir').val() == $('#dat_Accpdd_ux_acpdir').attr( "msgerror" ) ){
							$('#dat_Accpdd_ux_acpdir').val( '' );
							$('#dat_Accpdd_ux_acpdir').attr( "restAqua", $('#dat_Accpdd_ux_acpdir').attr( "msgError" ) );
							$('#dat_Accpdd_ux_acpdir').removeAttr( "msgError" );
							$('#dat_Accpdd_ux_acpdir').removeClass( "camporequerido" );
						}
						return ($('#dat_Accpdi_ux_acpdir').val() == $('#dat_Accpdi_ux_acpdir').attr( "msgerror" ));
					}
				}
			},
			dat_Acccdd : {
				required : {
					depends : function(element){
						if( $('#dat_Acccdi').val() == $('#dat_Acccdi').attr( "msgerror" ) && $('#dat_Acccdd').val() == $('#dat_Acccdd').attr( "msgerror" ) ){
							$('#dat_Acccdd').val( '' );
						}
						return ($('#dat_Acccdi').val() == $('#dat_Acccdi').attr( "msgerror" ));
					}
				}
			},
			dat_Accdtd : {
				required : {
					depends : function(element){
						if( $('#dat_Accdir').val() == $('#dat_Accdir').attr( "msgerror" ) && $('#dat_Accdtd').val() == $('#dat_Accdtd').attr( "msgerror" ) ){
							$('#dat_Accdtd').val( '' );
						}
						return ($('#dat_Accdir').val() == $('#dat_Accdir').attr( "msgerror" ));
					}
				}
			}
		}, //rules
		messages : {
			dat_Accpdd_ux_acpdir : "Debe ingresar la direccion o el detalle de la direccion", //dir pac
			dat_Acccdd : "Debe ingresar la direccion o el detalle de la direccion", //dir resp pac
			dat_Accdtd : "Debe ingresar la direccion o el detalle de la direccion" //dir resp pac
		}, //messages
		errorClass : "errorMensajes"//,
		//validClass: "mensajeValido"
	});
});


function validarCamposCedResAccPropietario()
{
	var cedula1 = $('#pac_doctxtNumDoc').val();
	var cedula2 = $( '[name=dat_Accnid_ux_acpide]', $( "#accidentesTransito" ) ).val();

	if (cedula1 == cedula2)
	{
		$( "select,textarea,input[type=text],input[type=hidden],input[type=text]", $( '[name=dat_Accnid_ux_acpide]', $( "#accidentesTransito" ) )[0].parentNode.parentNode ).val( '' );

		var n1=$('#pac_no1txtPriNom').val();
		var n2=$('#pac_no2txtSegNom').val();
		var a1=$('#pac_ap1txtPriApe').val();
		var a2=$('#pac_ap2txtSegApe').val();

		$('[name=dat_Acctid_ux_acptid]', $( "#accidentesTransito" ) ).val($('#pac_tdoselTipoDoc').val()); //tipo documento
		$('[name=dat_Accnid_ux_acpide]', $( "#accidentesTransito" ) ).val($('#pac_doctxtNumDoc').val()); //cedula

		$('[name=dat_Accno1_ux_acpno1]', $( "#accidentesTransito" ) ).val(n1); //nombre 1
		$('[name=dat_Accno2_ux_acpno2]', $( "#accidentesTransito" ) ).val(n2); //nombre 2
		$('[name=dat_Accap1_ux_acpap1]', $( "#accidentesTransito" ) ).val(a1); //apellido 1
		$('[name=dat_Accap2_ux_acpap2]', $( "#accidentesTransito" ) ).val(a2); //apellido 2


		if( $('#pac_dirtxtDirRes').val() != '' && $('#pac_dirtxtDirRes').val() != $('#pac_dirtxtDirRes').attr( "msgError" ) )
			$('[name=dat_Accpdi_ux_acpdir]', $( "#accidentesTransito" ) ).val( $('#pac_dirtxtDirRes').val() ); //direccion

		if( $('#pac_dedtxtDetDir').val() != '' && $('#pac_dedtxtDetDir').val() != $('#pac_dedtxtDetDir').attr( "msgError" ) )
			$('[name=dat_Accpdd_ux_acpdir]', $( "#accidentesTransito" ) ).val( $('#pac_dedtxtDetDir').val() ); //detalle de la direcion

		if( $('#pac_dehtxtDepRes').val() != '' && $('#pac_dehtxtDepRes').val() != $('#pac_dehtxtDepRes').attr( "msgError" ) )
			$('[name=AccDepPropietario]', $( "#accidentesTransito" ) ).val( $('#pac_dehtxtDepRes').val() );//departamento residencia

		if( $('#pac_dehhidDepRes').val() != '' && $('#pac_dehhidDepRes').val() != $('#pac_dehhidDepRes').attr( "msgError" ) )
			$('[name=dat_Accpdp_ux_acpdep]', $( "#accidentesTransito" ) ).val( $('#pac_dehhidDepRes').val() );//codigo del departamento oculto

		if( $('#pac_muhtxtMunRes').val() != '' && $('#pac_muhtxtMunRes').val() != $('#pac_muhtxtMunRes').attr( "msgError" ) )
			$('[name=AccMunPropietario]', $( "#accidentesTransito" ) ).val($('#pac_muhtxtMunRes').val() );//municipio de residencia

		if( $('#pac_muhhidMunRes').val() != '' && $('#pac_muhhidMunRes').val() != $('#pac_muhhidMunRes').attr( "msgError" ) )
			$('[name=dat_Accpmn_ux_acpmun]', $( "#accidentesTransito" ) ).val( $('#pac_muhhidMunRes').val() );//codigo del municipio de residencia oculto



		if( $('#pac_movtxtTelMov').val() != '' && $('#pac_movtxtTelMov').val() != $('#pac_movtxtTelMov').attr( "msgError" ) ){
			$('[name=dat_Acctel_ux_acptel]').val( $('#pac_movtxtTelMov').val() ); //telefono
		}

		if( $('#pac_teltxtTelFij').val() != '' && $('#pac_teltxtTelFij').val() != $('#pac_teltxtTelFij').attr( "msgError" ) ){
			$('[name=dat_Acctel_ux_acptel]').val( $('#pac_teltxtTelFij').val() ); //telefono
		}

		resetAqua( $( "#accidentesTransito" ) );

		$( "[depend]", $( "#accidentesTransito" ) ).blur();
	}
	else //se realiza la busqueda de la cedula como responsable del usuario o como paciente
	{
		$.post("admision_erp.php",
			{
				accion      : 'consultarResponsable',
				consultaAjax: '',
				cedula2     : cedula2,
				wbasedato   :$('#wbasedato').val(),
				wemp_pmla   :$('#wemp_pmla').val()

			},
			function(data){
				if(data.error == 1)
				{
					alert(data.mensaje);
				}
				else
				{
					if(data && data.doc != '' ){
						$('[name=dat_Acctid_ux_acptid]', $( "#accidentesTransito" ) ).val(data.tdoc);
						$('[name=dat_Accnid_ux_acpide]', $( "#accidentesTransito" ) ).val(data.doc);

						$('[name=dat_Accno1_ux_acpno1]', $( "#accidentesTransito" ) ).val(data.no1);
						$('[name=dat_Accno2_ux_acpno2]', $( "#accidentesTransito" ) ).val(data.no2);
						$('[name=dat_Accap1_ux_acpap1]', $( "#accidentesTransito" ) ).val(data.ap1);
						$('[name=dat_Accap2_ux_acpap2]', $( "#accidentesTransito" ) ).val(data.ap2);


						$('[name=dat_Accpdi_ux_acpdir]', $( "#accidentesTransito" ) ).val(data.dir);
						$('[name=dat_Accpdd_ux_acpdir]', $( "#accidentesTransito" ) ).val(data.ddir);
						$('[name=dat_Accpdp_ux_acpdep]', $( "#accidentesTransito" ) ).val(data.dep);
						$('[name=AccDepPropietario]', $( "#accidentesTransito" ) ).val(data.ndep);
						$('[name=dat_Accpmn_ux_acpmun]', $( "#accidentesTransito" ) ).val(data.mun);
						$('[name=AccMunPropietario]', $( "#accidentesTransito" ) ).val(data.nmun);
						$('[name=dat_Acctel_ux_acptel]').val(data.mov);
						$('[name=dat_Acctel_ux_acptel]').val(data.tel);
						resetAqua( $( "#accidentesTransito" ) );
					}
				}
			},
			"json"
		);
	}
}

function validarCamposCedResAccConductor()
{
	var cedula1 = $('#pac_doctxtNumDoc').val();
	var cedula2 = $( '[name=dat_Acccni_ux_acpid2_ux_accced]', $( "#accidentesTransito" ) ).val();

	if (cedula1 == cedula2)
	{
		$( "select,textarea,input[type=text],input[type=hidden],input[type=text]", $( '[name=dat_Acccni_ux_acpid2_ux_accced]', $( "#accidentesTransito" ) )[0].parentNode.parentNode ).val( '' );

		var n1=$('#pac_no1txtPriNom').val();
		var n2=$('#pac_no2txtSegNom').val();
		var a1=$('#pac_ap1txtPriApe').val();
		var a2=$('#pac_ap2txtSegApe').val();

		$('[name=dat_Acccti_ux_acptic]', $( "#accidentesTransito" ) ).val($('#pac_tdoselTipoDoc').val()); //tipo documento
		$('[name=dat_Acccni_ux_acpid2_ux_accced]', $( "#accidentesTransito" ) ).val($('#pac_doctxtNumDoc').val()); //cedula

		$('[name=dat_Acccn1_ux_acpnc1]', $( "#accidentesTransito" ) ).val(n1); //nombre 1
		$('[name=dat_Acccn2_ux_acpnc2]', $( "#accidentesTransito" ) ).val(n2); //nombre 2
		$('[name=dat_Accca1_ux_acpac1]', $( "#accidentesTransito" ) ).val(a1); //apellido 1
		$('[name=dat_Accca2_ux_acpac2]', $( "#accidentesTransito" ) ).val(a2); //apellido 2


		if( $('#pac_dirtxtDirRes').val() != '' && $('#pac_dirtxtDirRes').val() != $('#pac_dirtxtDirRes').attr( "msgError" ) )
			$('[name=dat_Acccdi_ux_accdir]', $( "#accidentesTransito" ) ).val( $('#pac_dirtxtDirRes').val() ); //direccion

		if( $('#pac_dedtxtDetDir').val() != '' && $('#pac_dedtxtDetDir').val() != $('#pac_dedtxtDetDir').attr( "msgError" ) )
			$('[name=dat_Acccdd_ux_accdir]', $( "#accidentesTransito" ) ).val( $('#pac_dedtxtDetDir').val() ); //detalle de la direcion

		if( $('#pac_dehtxtDepRes').val() != '' && $('#pac_dehtxtDepRes').val() != $('#pac_dehtxtDepRes').attr( "msgError" ) )
			$('[name=AccConductordp]', $( "#accidentesTransito" ) ).val( $('#pac_dehtxtDepRes').val() );//departamento residencia

		if( $('#pac_dehhidDepRes').val() != '' && $('#pac_dehhidDepRes').val() != $('#pac_dehhidDepRes').attr( "msgError" ) )
			$('[name=dat_Acccdp]', $( "#accidentesTransito" ) ).val( $('#pac_dehhidDepRes').val() );//codigo del departamento oculto

		if( $('#pac_muhtxtMunRes').val() != '' && $('#pac_muhtxtMunRes').val() != $('#pac_muhtxtMunRes').attr( "msgError" ) )
			$('[name=AccConductorMun]', $( "#accidentesTransito" ) ).val($('#pac_muhtxtMunRes').val() );//municipio de residencia

		if( $('#pac_muhhidMunRes').val() != '' && $('#pac_muhhidMunRes').val() != $('#pac_muhhidMunRes').attr( "msgError" ) )
			$('[name=dat_Acccmn_ux_accmuc]', $( "#accidentesTransito" ) ).val( $('#pac_muhhidMunRes').val() );//codigo del municipio de residencia oculto



		if( $('#pac_movtxtTelMov').val() != '' && $('#pac_movtxtTelMov').val() != $('#pac_movtxtTelMov').attr( "msgError" ) ){
			$('[name=dat_Accctl_ux_acctel]').val( $('#pac_movtxtTelMov').val() ); //telefono
		}

		if( $('#pac_teltxtTelFij').val() != '' && $('#pac_teltxtTelFij').val() != $('#pac_teltxtTelFij').attr( "msgError" ) ){
			$('[name=dat_Accctl_ux_acctel]').val( $('#pac_teltxtTelFij').val() ); //telefono
		}

		resetAqua( $( "#accidentesTransito" ) );

		$( "[depend]", $( "#accidentesTransito" ) ).blur();
	}
	else //se realiza la busqueda de la cedula como responsable del usuario o como paciente
	{
		$.post("admision_erp.php",
			{
				accion      : 'consultarResponsable',
				consultaAjax: '',
				cedula2     : cedula2,
				wbasedato   :$('#wbasedato').val(),
				wemp_pmla   :$('#wemp_pmla').val()

			},
			function(data){
				if(data.error == 1)
				{
					alert(data.mensaje);
				}
				else
				{
					if(data && data.doc != '' ){
						$('[name=dat_Acccti_ux_acptic]', $( "#accidentesTransito" ) ).val(data.tdoc);
						$('[name=dat_Acccni_ux_acpid2_ux_accced]', $( "#accidentesTransito" ) ).val(data.doc);

						$('[name=dat_Acccn1_ux_acpnc1]', $( "#accidentesTransito" ) ).val(data.no1);
						$('[name=dat_Acccn2_ux_acpnc2]', $( "#accidentesTransito" ) ).val(data.no2);
						$('[name=dat_Accca1_ux_acpac1]', $( "#accidentesTransito" ) ).val(data.ap1);
						$('[name=dat_Accca2_ux_acpac2]', $( "#accidentesTransito" ) ).val(data.ap2);


						$('[name=dat_Acccdi_ux_accdir]', $( "#accidentesTransito" ) ).val(data.dir);
						$('[name=dat_Acccdi_ux_accdir]', $( "#accidentesTransito" ) ).val(data.ddir);
						$('[name=dat_Acccdp]', $( "#accidentesTransito" ) ).val(data.dep);
						$('[name=AccConductordp]', $( "#accidentesTransito" ) ).val(data.ndep);
						$('[name=dat_Acccmn_ux_accmuc]', $( "#accidentesTransito" ) ).val(data.mun);
						$('[name=AccConductorMun]', $( "#accidentesTransito" ) ).val(data.nmun);
						$('[name=dat_Accctl_ux_acctel]').val(data.mov);
						$('[name=dat_Accctl_ux_acctel]').val(data.tel);
						resetAqua( $( "#accidentesTransito" ) );
					}
				}
			},
			"json"
		);
	}
}