
// 2022-03-30 Luis F Meneses: No enviar dato en fecha de expedición en la función mostrarDatos.

var conFocus = "";	//Indica que elemento tiene focus, esto para saber cuando se esta usando el autocompletar que elemento tiene el foco para completar los elementos de busqueda (paramsExtras)
var hayCambios = false;	//Indica si se hizo cambios al escribir en el formulario, esto para que al dar click cosbre regresar a la agenda o iniciar pregunte si desea continuar
var consultaEvento = false;

var conteoInputs = 0;
var llenadoAutomatico = false;

var numeroTurnoTemporal;

$(document).ready(function () {
	codRespGlobal = "";
	idCodResp = "";

	//-------------------------------------------------------
	// --> Esto es para las funcionalidades de los turnos
	//-------------------------------------------------------
	$("#ing_seisel_serv_ing").on("change", function () {//2019-09-18
		console.log($(this).find("option:selected").attr("exigirDiagnosticoInicial"));
		if ($(this).find("option:selected").attr("exigirDiagnosticoInicial") == "off") {
			if ($("#ing_digtxtImpDia").val() == $("#ing_digtxtImpDia").attr("msgError"))
				$("#ing_digtxtImpDia").val("")
			if ($("#ing_meitxtMedIng").val() == $("#ing_meitxtMedIng").attr("msgError"))
				$("#ing_meitxtMedIng").val("")
			$("#ing_digtxtImpDia").removeAttr("msgError");
			$("#ing_digtxtImpDia").removeClass("campoRequerido");
			$("#ing_meitxtMedIng").removeAttr("msgError");
			$("#ing_meitxtMedIng").removeClass("campoRequerido");
		} else {
			$("#ing_digtxtImpDia").attr("msgError", "Digite la Impresion Diagnostica");
			$("#ing_digtxtImpDia").addClass("campoRequerido");
			marcarAqua('#ing_digtxtImpDia', 'msgError', 'campoRequerido');
			$("#ing_meitxtMedIng").attr("msgError", "Digite el medico de ingreso");
			$("#ing_meitxtMedIng").addClass("campoRequerido");
			marcarAqua('#ing_meitxtMedIng', 'msgError', 'campoRequerido');

		}
	});
	setTimeout(function () {
		// --> Activar el buscador de texto, para los turnos
		$('#buscardorTurno').quicksearch('#tablaListaTurnos .find');
		// --> Tooltip en la lista de turnos
		$('[tooltip=si]').tooltip({ track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		// --> Si existe un turno que ya haya sido llamado por este usuario, inhabilito los demas
		if ($("#turnoLlamadoPorEsteUsuario").val() != '') {
			var idTurno = $("#turnoLlamadoPorEsteUsuario").val();
			$(".botonLlamarPaciente").hide();
			$(".botonColgarPaciente").hide();
			$("#imgLlamar" + idTurno).hide();
			$("#imgLlamar" + idTurno).next().show();
			$("#imgLlamar" + idTurno).next().next().show();
			$("#botonAdmitir" + idTurno).show();
			$("#botonCancelar" + idTurno).show();
			$("#trTurno_" + idTurno).attr("classAnterior", $("#trTurno_" + idTurno).attr("class"));
			$("#trTurno_" + idTurno).attr("class", "fondoAmarillo");
		}
		// --> Solo se puede llamar al primer turno de la lista, para evitar que llamen en desorden
		else
			activarPrimerTurno();

		// --> Llamado automatico, para que la lista de turnos se este actualizando
		// --> 2018-05-29: El llamado automático se hará mientras no se esté haciendo una admisión, Jerson Trujillo.
		if ($("#permitirVerListaTurnos").val()) {
			setInterval(function () {
				if (!$("#radAdmision").is(":checked")) {
					listarPacientesConTurno();
				}
			}, 30000);
		}

	}, 500);
	//-------------------------------------------------------
	// --> Fin funcionalidades de los turnos
	//-------------------------------------------------------

	$('#tabla_responsables_1_2').hide(); //se oculta
	$('#tr_titulo_tercer_resp').hide(); //se oculta
	/************************************************************************************************
	 * Septiembre 02 de 2013
	 * Creo clon de los options de ORIGEN DE LA ATENCIÓN para poder quitar y agregarlos cada vez que
	 * se requiera. Esto se hace al seleccionar el radio button de ADMISION o PREADMISION.
	 ************************************************************************************************/
	optOrigenAtencion = $("option[value=02],option[value=06]", $("#ing_caiselOriAte")).clone();
	optServicioIngreso = $("option[value^=4]", $("#ing_seisel_serv_ing"));
	if (optServicioIngreso.length > 0) {
		// optServicioIngreso.html( optServicioIngreso.html().toUpperCase() );

		optServicioIngreso.each(function (x) {
			$(this).html($(this).html().toUpperCase());
		})
	}
	//$( "option[value^=4]", $( "#ing_seisel_serv_ing" ) ).remove();
	/************************************************************************************************/


	$("#div_datosIng_Per_Aco,#div_datos_acompañante,#div_datos_responsable,#div_datos_Pag_Aut,#div_otros_datos_ingreso,#div_accidente_evento,#div_ext_agendaPreadmision")
		.attr("acordeon", "");


	$("table", $("#div_admisiones")).addClass("anchotabla");
	$("table", $("#div_int_otros_datos_ing")).removeClass("anchotabla");
	$("table", $("#div_int_datos_acompañante")).removeClass("anchotabla");
	$("table", $("#tabla_eps")).removeClass("anchotabla");


	/************************************
	 * Edwin
	 ************************************/
	$("div[acordeon]").accordion({
		collapsible: true,
		heightStyle: "content"
	});

	$("div[acordeon1]").accordion({
		collapsible: false,
		heightStyle: "content",
		icons: false
	});
	/************************************/

	$("option").each(function () {

		if ($(this).html() != 'Seleccione...') {
			$(this).html($(this).html().toUpperCase());
		}
	});

	$("H3", $("#div_datos_acompañante,#div_datos_responsable,#div_datos_Pag_Aut,#div_otros_datos_ingreso,#div_accidente_evento")).attr("acclick", "false");

	//Agregar el atributo msgError a todos los input para que sean obligatorios
	$("input[type=text],input[type=radio],input[type=checkbox],select,textarea", $("#div_admisiones")).each(function (x) {
		if (!$(this).attr("msgError")) {
			$(this).attr("msgError", "");
		}
	});

	$("#div_ext_agendaPreadmision").accordion("option", "collapsible", false);

	/************************************************************************************************
	 * Agosto 15 de 2013
	 ************************************************************************************************/
	//Borro los atributos de msgerror para los radios de admisión y preadmisión
	$("input[type=radio]", document.getElementById("radAdmision").parentNode.parentNode).each(function (x) {
		$(this).removeAttr("msgerror");
	});
	/************************************************************************************************/

	//quitar el atributo msgError a los campos que no son obligatorios
	$("#ing_histxtNumHis").removeAttr("msgError");
	$("#ing_nintxtNumIng").removeAttr("msgError");
	$("#pac_cretxtCorResp").removeAttr("msgError");
	$("#pac_cortxtCorEle").removeAttr("msgError");
	$("#pac_eemtxtExt").removeAttr("msgError");
	var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
	$("#ing_poltxtNumPol" + prefijo_trEps).removeAttr("msgError");
	$("#txtEdad").removeAttr("msgError");
	$("#ing_ncotxtNumCon" + prefijo_trEps).removeAttr("msgError");
	$("#res_ffrtxtNumcon" + prefijo_trEps).removeAttr("msgError");
	$("#res_comtxtNumcon" + prefijo_trEps).removeAttr("msgError");
	$("#pac_ap2txtSegApe").removeAttr("msgError");	//Agosto 30 de 2013
	$("#pac_no2txtSegNom").removeAttr("msgError");	//Agosto 30 de 2013
	$("#pac_emptxtEmpLab").removeAttr("msgError");	//Agosto 30 de 2013
	$("#pac_temtxtTelTra").removeAttr("msgError");	//Agosto 30 de 2013
	//$( "#pac_pahtxtPaiRes" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$("#pac_prpntxtPaiResPer").removeAttr("msgError");	//Agosto 30 de 2013

	$("[name=ing_ordtxtNumAut]").removeAttr("msgError");	//Agosto 30 de 2013
	$("[name=ing_fhatxtFecAut]").removeAttr("msgError");	//Agosto 30 de 2013
	$("[name=ing_hoatxtHorAut]").removeAttr("msgError");	//Agosto 30 de 2013
	$("[name=ing_npatxtNomPerAut]").removeAttr("msgError");	//Agosto 30 de 2013
	$("[name=ing_cactxtcups]").removeAttr("msgError");	//Agosto 30 de 2013
	$("[name=ing_cachidcups]").removeAttr("msgError");	//Agosto 30 de 2013
	$("[name=ing_pcoselPagCom]").removeAttr("msgError");	//Agosto 30 de 2013
	$("#buscardorTurno").removeAttr("msgError");	//Agosto 30 de 2013
	$("#puestoTrabajo").removeAttr("msgError");	//Agosto 30 de 2013
	$("#pac_tle, #pac_dle").removeAttr("msgError");
	$("#chk_imprimirHistoria").removeAttr("msgError");

	marcarAqua('', 'msgError', 'campoRequerido');
	marcarAqua();

	formatoCampos();
	buscarPaises();
	buscarDepartamentos();
	buscarMunicipios();
	buscarBarrios();
	buscarOcupaciones();
	buscarAseguradoras('tabla_eps');
	buscarCUPS();
	buscarImpresionDiagnostica();
	buscarIpsRemite();
	buscarAseguradorasVehiculo();
	buscarMedicos();
	buscarTarifaParticular();
	// buscarSegundoResp();
	// buscarPrimerResp();
	resetear(true);

	// $("#bot_navegacion").css("display", "none");

	$("textarea,select,input").on({
		blur: function () {
			if ($(this).is(":button")) {
				return;
			}
			if (this.id != 'pac_doctxtNumDoc' && this.id != 'ing_histxtNumHis') {
				try {
					if (!verificandoLog) {
						llenarDatosLog();
					}
				} catch (e) { }
			}
		}
	});


	/************************************************************************************************
	 * Agosto 20 de 2013
	 *
	 * Para la fecha posible de ingreso se permite que sea mayor o igual a la fecha actual
	 * La fecha de nacimiento no puede ser mayor a la fecha actual
	 ************************************************************************************************/
	var dateActual = $("#fechaAct").val().split("-");

	/************************************************************************************************/

	/************************************************************************************************
	 * Agosto 30 de 2013
	 *
	 * - Para algunos campos, cuando reciba foco se pone la fecha actual por defecto
	 * - Para algunos campos al recibir un foco se pone la hora actual por defecto
	 ************************************************************************************************/
	$("#ing_feitxtFecIng,#ing_fhatxtFecAut").on({
		focus: function () {
			if ($(this).val() == '') {
				$(this).val($("#fechaAct").val());
			}
		}
	});

	$("#ing_hintxtHorIng,#ing_hoatxtHorAut").on({
		focus: function () {
			//Si es igual a vacío o a la mascara que tenga por defecto
			if ($(this).val() == '' || $(this).val() == '__:__:__') {
				$(this).val($("#horaAct").val());
			}
		}
	});


	/*PARA QUE LA LISTA DE RESPONSABLES SEA SORTABLE*/
	var fixHelper = function (e, ui) {
		ui.children().each(function () {
			$(this).width($(this).width());
		});
		return ui;
	};
	$("#tabla_eps > tbody").sortable({
		items: "> tr",
		helper: fixHelper,
		stop: function (event, ui) {
			reOrdenarResponsables();
			$("input[type='radio'][name='res_comtxtNumcon']").each(function e(i, item) {
				if (i == 0) {
					if ($(item).is(":checked")) {
						alerta(" Debe modificar la complementariedad");
					}
					$(item).attr("checked", false);
					$(item).attr("estadoAnterior", "off");
					$(item).parent().hide();

				} else {

					$(item).parent().show();
				}
			});
		},
	});
	/*FIN PARA QUE LA LISTA DE RESPONSABLES SEA SORTABLE*/

	//Al darle doble click, trae la cedula para llenar los demas campos
	$("#pac_crutxtNumDocRes").dblclick(function () {
		$(this).val($.trim($("#pac_doctxtNumDoc").val()));
		validarCamposCedRes();
	});

	$("#pac_ceatxtCelAco").dblclick(function () {
		$(this).val($.trim($("#pac_doctxtNumDoc").val()));
		validarCamposCedAco();
	});

	//consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
	//consultarAgendaAdmitidos( $( "#fechaAct" ).val(), 0 );

	$("#div_datosAdmiPreadmi").css({ display: "none" });
	$("#div_ext_agendaPreadmision").css({ display: "" });

	$("#radPreadmision").attr("checked", true);
	$("#txtAdmisionPreadmision").html("PREADMISION");


	$("#pac_fectxtFechaPosibleIngreso").attr("disabled", false);
	$("#pac_fectxtFechaPosibleIngreso").blur();

	//$( "[name=dat_Accvfi_ux_accfin]" ).datepicker( "option", "minDate", new Date( dateActual[0], dateActual[1]-1, dateActual[2] ) );
	//$( "#pac_fectxtFechaPosibleIngreso,#ing_feitxtFecIng" ).datepicker( "option", "minDate", new Date( dateActual[0], dateActual[1]-1, dateActual[2] ) ); -->2016-09-08
	$("#pac_fectxtFechaPosibleIngreso,#ing_feitxtFecIng").datepicker();
	$("[name='res_firtxtNumcon'][value='']").val($("#fechaAct").val());
	$("[name='res_ffrtxtNumcon'][value='']").val('0000-00-00');

	//Pongo limite de fecha máxima, la cual es la actual
	$("#pac_fnatxtFecNac").datepicker("option", "maxDate", "+0d");
	$("#pac_fedtxtFecExpDoc").datepicker("option", "maxDate", "+0d");
	$("#dat_Accfec").datepicker("option", "maxDate", "+0d");
	$("[name=dat_Accvfi_ux_accfin]").datepicker("option", "maxDate", "+0d");
	$("[name=dat_Accvfi_ux_accffi]").datepicker("option", "minDate", new Date(dateActual[0], dateActual[1] - 1, dateActual[2]));

	consultarAgendaPreadmision($("#fechaAct").val(), 0);
	consultarAgendaAdmitidos("", 0);

	$("select,textarea,input").change(function () {
		hayCambios = true;
	});
	//funcion si tiene un log guardado con datos de la admision
	verificarLogAdmision();
	$("[padm]").show();
	consultarConsecutivo();

	//Fechas, ingresar numeros y que vaya separando por guiones
	$("[fecha]").keyup(function (event) {
		if (event.which != 8) { //Diferente de back space
			if ($(this).val().length == 4) {
				$(this).val($(this).val() + "-");
			} else if ($(this).val().length == 7) {
				$(this).val($(this).val() + "-");
			}
		}
	});

	$("[fecha]").blur(function () {
		if (isDate($(this).val()) == false) {
			$(this).val("0000-00-00");
		}
	});


	//2014-08-12 Para que no permita ingresar un punto en estos campos, el atributo "alfabetico" ya tiene la restriccion de caracteres, excepto el punto
	$("#pac_ap1txtPriApe,#pac_ap2txtSegApe,#pac_no1txtPriNom,#pac_no2txtSegNom").keyup(function () {
		if ($(this).val() != "")
			$(this).val($(this).val().replace(/(\.)|^( )|[^[a-zA-Z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]]/g, ""));//2017-11-10

	});

	$("#pac_ap1txtPriApe,#pac_ap2txtSegApe,#pac_no1txtPriNom,#pac_no2txtSegNom").blur(function () {
		if ($(this).val() == " ") {
			$(this).val("");
			resetAqua($(this).parent());
		}
	});

	//2014-12-29 Para que no permita ingresar caracteres especiales en los documentos
	$("#pac_doctxtNumDoc, #pac_tdaselTipoDocRes").keyup(function () {
		if ($(this).val() != "") {//2015-05-22
			if ($("#pac_tdoselTipoDoc").find("option:selected").attr("alfanumerico") == "on") {
				$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			} else if ($("#pac_tdoselTipoDoc").find("option:selected").attr("alfanumerico") == "off") {
				$(this).val($(this).val().replace(/[^\d\ ]/g, ""));
			}
			tam = $(this).val().length;
			if (tam > 15) {
				$(this).val($(this).val().substring(0, tam - 1));
			}
		}
	});
	//2014-11-06 Para que no permita ingresar caracteres especiales en los documentos
	$("#pac_crutxtNumDocRes").keyup(function () {
		if ($(this).val() != "") {//2015-05-22
			$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			tam = $(this).val().length;
			if (tam > 15) {
				$(this).val($(this).val().substring(0, tam - 1));
			}
		}
	});

	///-->2015-06-02 para no permitir la escritura de la comilla simple( ' ),puesto que puede romper los inserts.
	$("input[type='text'],textarea").keyup(function () {
		if ($(this).val() != "") {//2015-05-22
			//$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			$(this).val($(this).val().replace("'", ""));
		}
	});

	setInterval(function () {
		var el = $("#tr_navegaResultados");
		if (el.hasClass('amarilloSuave')) {
			el.removeClass("amarilloSuave");
		} else {
			el.addClass("amarilloSuave");
		}
	}, 700);

	if ($("#AgendaMedica").val() == "on") {
		mostrarAdmisionDesdeAgendaMedica();
	}


	if ($("#search_historia").val() != '' && $("#search_ingreso").val() != '') {

		setTimeout(function () {
			iniciar();
			$("#ing_histxtNumHis").val($("#search_historia").val());
			$("#ing_nintxtNumIng").val($("#search_ingreso").val());
			mostrarDatos();
		}, 1000);
	}


	//para que oculte el tipo de atención inicialmente.
	validacionServicioIngreso("cargaInicial");
	$("#ing_seisel_serv_ing > option").click(function () {
		validacionServicioIngreso();
	});

	$("[name=dat_Accvff_ux_accffi]").click(function () {
		$(this).attr("disabled", "");
	});

	$("#select_doc_new").keyup(function () {
		if ($(this).val() != "") {//2015-05-22
			if ($("#select_tdoc_new").find("option:selected").attr("alfanumerico") == "on") {
				$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			} else if ($("#select_tdoc_new").find("option:selected").attr("alfanumerico") == "off") {
				$(this).val($(this).val().replace(/[^\d\ ]/g, ""));
			}
			$(this).val($(this).val().replace(/(\.)|(  )|[^\w\d\ \-]/g, ""));
			tam = $(this).val().length;
			if (tam > 11) {
				$(this).val($(this).val().substring(0, tam - 1));
			}
		}
	});

	$("#txt_jus_change_Doc").keyup(function () {
		if ($(this).val() != "")
			$(this).val($(this).val().replace(/(\')|(\")/g, ""));
	});


	// $('#iframeModalTablero').load(function() {
	// RunAfterIFrameLoaded();
	// });
	setInterval(() => {
		$("#pac_fedtxtFecExpDoc").removeClass("campoRequerido");
		$("#pac_fedtxtFecExpDoc").removeAttr("msgerror");
	}, 100);


});

function consultarSiPreanestesia() {

	var wbasedato = $("#wbasedato").val();
	var cedula = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc = $("#pac_tdoselTipoDoc").val();
	var centroCostos = $("#ing_seisel_serv_ing").val();
	$.post("admision_erp.php",
		{
			wbasedato: wbasedato,
			consultaAjax: '',
			accion: 'consultarSiPreanestesia',
			wemp_pmla: $("#wemp_pmla").val(),
			cedula: cedula,
			tipoDoc: tipoDoc,
			wcco: centroCostos

		}, function (data) {
			if (data.error == 1) {
			}
			else {
				$("#turno_preanestesia").html(data.turno);
				$("#div_preanestesia").dialog({
					modal: true,
					width: 'auto',
					title: "<div align='center'> <img src='../../images/medical/root/Advertencia.png'/> PREANESTESIA.</div>",
					show: { effect: "slide", duration: 600 },
					hide: { effect: "fold", duration: 600 },
					closeOnEscape: false,
					open: function (event, ui) {
						$(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
						$("#input_aux_prea").focus();
					},
					close: function (event, ui) {
						//listarPacientesConTurno();
					}
				});
			}
		},
		"json"
	);
}

function alerta(txt) {

	$("#textoAlerta2").text(txt);
	$('#msjAlerta2').dialog({
		width: "auto",
		height: 200,
		modal: true,
		dialogClass: 'noTitleStuff'
	});
	$(".ui-dialog-titlebar").hide();
	setTimeout(function () {
		$('#msjAlerta2').dialog('destroy');
		$(".ui-dialog-titlebar").show();
	}, 3000);
}

function CambiarEstadoDatosExtranjeros(obj) {
	if ($(obj).val() == "E") {
		var colombia = $("#cod_colombia").val();
		$("#pac_tle").attr("msgError", $("#pac_tle").attr("msgcampo"));
		$("#pac_dle").attr("msgError", $("#pac_dle").attr("msgcampo"));
		marcarAqua('#pac_tle', 'msgError', 'campoRequerido');
		marcarAqua('#pac_dle', 'msgError', 'campoRequerido');
		$("#pac_dle").addClass("campoRequerido");
		$("#pac_tle").addClass("campoRequerido");
		$(".tr_pacienteExtranjero").show();
		$("#pac_tle, #pac_dle").val("");
	} else {
		$("#pac_tle, #pac_dle").removeClass("campoRequerido");
		$("#pac_tle, #pac_dle").removeAttr("msgError");
		$(".tr_pacienteExtranjero").hide();
	}
}

function buscarPaises() {
	//Asigno autocompletar para la busqueda de paises
	$("#pac_pantxtPaiNac, #pac_pahtxtPaiRes").autocomplete("admision_erp.php?consultaAjax=&accion=consultarPais", {
		cacheLength: 1,
		delay: 300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width: 250,
		autoFill: false,
		minChars: 2,
		json: "json",
		formatItem: function (data, i, n, value) {

			//convierto el string en json
			eval("var datos = " + data);

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function (data, value) {
			//convierto el string en json
			eval("var datos = " + data);
			return datos[0].valor.des;
		}
	}).result(
		function (event, item) {

			// //La respuesta es un json
			// //convierto el string en formato json
			eval("var datos = " + item);

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			buscabarrio = false;

			if ($(this).attr("id") == "pac_pantxtPaiNac") {
				idDepTxt = "pac_deptxtDepNac";
				idDepid = "pac_dephidDepNac";
				idMunTxt = "pac_ciutxtMunNac";
				idMunid = "pac_ciuhidMunNac";
				$("#pac_deptxtDepNac, #pac_ciutxtMunNac").val(""); // Si se cambia el país, se pone vacio departamento y municipio de nacimiento
			} else {
				idDepTxt = "pac_dehtxtDepRes";
				idDepid = "pac_dehhidDepRes";
				idMunTxt = "pac_muhtxtMunRes";
				idMunid = "pac_muhhidMunRes";
				idBarTxt = "pac_bartxtBarRes";
				idBarid = "pac_barhidBarRes";
				buscabarrio = true;
				$("#pac_dehtxtDepRes, #pac_muhtxtMunRes, pac_bartxtBarRes").val(""); // Si se cambia el país, se pone en vacio departamento, municipio y barrio de residencia
			}

			if (datos[0].valor.cod != $("#cod_colombia").val()) {
				$("#" + idDepTxt).val("NO APLICA");
				$("#" + idDepTxt).attr("disabled", "disabled");
				$("#" + idDepTxt).removeClass("campoRequerido");
				$("#" + idDepid).val($("#dep_no_aplica").val());
				$("#" + idMunTxt).val("NO APLICA");
				$("#" + idMunTxt).attr("disabled", "disabled");
				$("#" + idMunTxt).removeClass("campoRequerido");
				$("#" + idMunid).val($("#mun_no_aplica").val());
				if (buscabarrio) {
					$("#" + idBarTxt).val("Sin Dato");
					$("#" + idBarTxt).attr("disabled", "disabled");
					$("#" + idBarTxt).removeClass("campoRequerido");
					$("#" + idBarid).val($("#bar_no_aplica").val());
					//--> solicitud de campos de dirección y teléfono locales para extranjeros
					$("#pac_tle").attr("msgError", $("#pac_tle").attr("msgcampo"));
					$("#pac_dle").attr("msgError", $("#pac_dle").attr("msgcampo"));
					marcarAqua('#pac_tle', 'msgError', 'campoRequerido');
					marcarAqua('#pac_dle', 'msgError', 'campoRequerido');
					$("#pac_dle").addClass("campoRequerido");
					$("#pac_tle").addClass("campoRequerido");
					$(".tr_pacienteExtranjero").show();
				}
			} else {
				$("#" + idDepTxt).val("");
				$("#" + idDepTxt).attr("disabled", false);
				$("#" + idDepTxt).addClass("campoRequerido");
				$("#" + idDepid).val("");
				$("#" + idMunTxt).val("");
				$("#" + idMunTxt).attr("disabled", false);
				$("#" + idMunTxt).addClass("campoRequerido");
				$("#" + idMunid).val("");
				if (buscabarrio) {
					$("#" + idBarTxt).val("S");
					$("#" + idBarTxt).attr("disabled", false);
					$("#" + idBarTxt).addClass("campoRequerido");
					$("#" + idBarid).val("");
					$("#pac_tle, #pac_dle").removeClass("campoRequerido");
					$("#pac_tle, #pac_dle").removeAttr("msgError");
					$(".tr_pacienteExtranjero").hide();
				}
			}
			$("#pac_tle, #pac_dle").val("");
			this.value = datos[0].valor.des;
			this._lastValue = datos[0].valor.des;
			this._lastCodigo = datos[0].valor.cod;
			$(this).removeClass("campoRequerido");
			$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
		}
	).on({
		change: function () {

			var cmp = this;

			setTimeout(function () {

				//Pregunto si la pareja es diferente
				if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
					|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
				) {
					alerta(" Digite un pa\u00EDs v\u00E1lido");
					$("input[type=hidden]", cmp.parentNode).val('');
					cmp.value = '';
					cmp.focus();
					//cmp.blur();
				}
			}, 200);
		},
		keypress: function (event) {
			if (event.which == 0) {
				console.log("CONSULTAR MAS");
			}
			console.log(event.which);
		}
	})
		;
}

function buscarDepartamentos() {
	//Asigno autocompletar para la busqueda de departamentos
	$("#pac_deptxtDepNac,#pac_dehtxtDepRes,#pac_dretxtDepResp,#AccConductordp,#AccDepPropietario,#Catdep,#Accdep").autocomplete("admision_erp.php?consultaAjax=&accion=consultarDepartamento",
		{
			extraParams: {
				codigoPais: function (campo) {
					return $("#" + $(conFocus).attr("srcPai")).val();
				},
				name_objeto: function (campo) {
					return $(conFocus).attr("name");
				}
			},

			cacheLength: 0,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 2,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				if ($(this).attr("id") == "pac_deptxtDepNac") {
					$("#pac_ciutxtMunNac").val(""); // Si se cambia el departamento, se pone vacio el municipio de nacimiento
				} else {
					$("#pac_muhtxtMunRes, #pac_bartxtBarRes").val(""); // Si se cambia el departamento, se pone vacio el municipio de nacimiento
				}

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).focus(function () {
			conFocus = this;
		}).on({
			change: function () {

				var cmp = this;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite un Departamento v\u00E1lido")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);
			}
		});
}

function buscarMunicipios() {
	//Asigno autocompletar para la busqueda de municipios
	$("#pac_ciutxtMunNac,#pac_muhtxtMunRes,#pac_mretxtMunResp,#AccMunPropietario,#AccConductorMun,#Catmun,#Accmun").autocomplete("admision_erp.php?consultaAjax=&accion=consultarMunicipio",
		{
			extraParams: {
				dep: function (campo) {
					return $("#" + $(conFocus).attr("srcDep")).val();
				}
			},
			cacheLength: 0,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 2,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				if ($(this).attr("id") == "pac_muhtxtMunRes") {
					$("#pac_bartxtBarRes").val(""); // Si se cambia el municipio, se pone vacio el barrio de residencia
				}

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).focus(function () {
			conFocus = this;
		}).on({
			change: function () {

				var cmp = this;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite un Municipio v\u00E1lido")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);

				//Pregunto si la pareja es diferente
				// if( ( this._lastValue && this._lastValue != this.value && this._lastCodigo ) || ( this._lastCodigo != $( "input[type=hidden]", this.parentNode ).val() ) ){
				// alert( "Digite un municipio valido" )
				// $( "input[type=hidden]", this.parentNode ).val( '' );
				// this.value = '';
				// }
				// if( this._lastValue ){
				// this.value = this._lastValue;
				// }
				// else{
				// this.value = "";
				// }
			}
		});
}


function buscarBarrios() {
	//Asigno autocompletar para la busqueda de barrios
	$("#pac_bartxtBarRes").autocomplete("admision_erp.php?consultaAjax=&accion=consultarBarrio",
		{
			extraParams: {
				mun: function (campo) {
					return $("#" + $(conFocus).attr("srcMun")).val();
				}
			},
			cacheLength: 0,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 2,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).focus(function () {
			conFocus = this;
		}).on({
			change: function () {

				var cmp = this;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite un Barrio v\u00E1lido")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);

				//Pregunto si la pareja es diferente
				// if( ( this._lastValue && this._lastValue != this.value && this._lastCodigo ) || ( this._lastCodigo != $( "input[type=hidden]", this.parentNode ).val() ) ){
				// alert( "Digite un barrio valido" )
				// $( "input[type=hidden]", this.parentNode ).val( '' );
				// this.value = '';
				// }
				// if( this._lastValue ){
				// this.value = this._lastValue;
				// }
				// else{
				// this.value = "";
				// }
			}
		});;
}

function buscarOcupaciones() {
	//Asigno autocompletar para la busqueda de ocuapciones
	$("#pac_ofitxtocu").autocomplete("admision_erp.php?consultaAjax=&accion=consultarOcupacion",
		{
			cacheLength: 1,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 3,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).on({
			change: function () {

				var cmp = this;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite una Ocupaci\u00F3n v\u00E1lida")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);

				//Pregunto si la pareja es diferente
				// if( ( this._lastValue && this._lastValue != this.value && this._lastCodigo ) || ( this._lastCodigo != $( "input[type=hidden]", this.parentNode ).val() ) ){
				// alert( "Digite una ocupacion valida" )
				// $( "input[type=hidden]", this.parentNode ).val( '' );
				// this.value = '';
				// }
				// if( this._lastValue ){
				// this.value = this._lastValue;
				// }
				// else{
				// this.value = "";
				// }
			}
		});
}

function buscarAseguradoras(tabla_referencia) {
	var wbasedato = $("#wbasedato").val();

	if (tabla_referencia != "") {
		// para saber si la tabla tiene filas o no
		trs = $("#" + tabla_referencia).find('tr[id$=tr_' + tabla_referencia + ']').length;
		var value_id = 0;

		//busca consecutivo mayor
		if (trs > 0) {
			id_mayor = 0;
			// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
			$("#" + tabla_referencia).find('tr[id$=tr_' + tabla_referencia + ']').each(function () {
				id_ = $(this).attr('id');
				id_splt = id_.split('_');
				id_this = (id_splt[0]) * 1;
				if (id_this >= id_mayor) {
					id_mayor = id_this;
				}
			});
			// id_mayor++;
			value_id = id_mayor + '_tr_' + tabla_referencia;

		}
		else { value_id = '1_tr_' + tabla_referencia; }

		codEsp = "#ing_cemtxtCodAse" + value_id;
	}

	//Asigno autocompletar para la busqueda de aseguradoras
	$(codEsp).autocomplete("admision_erp.php?consultaAjax=&wemp_pmla=" + $("#wemp_pmla").val() + "&accion=consultarAseguradora&wbasedato=" + wbasedato + "&origenConsulta=autoCompletar",
		{
			cacheLength: 1,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 3,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				/*console.log( "value Nuevo: "+datos[0].valor.des);
				console.log( "valor anterior: "+this._lastValue);*/
				this.value = datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).eq(0).val(datos[0].valor.cod);
				$("input[type=hidden]", this.parentNode).eq(1).val(datos[0].valor.des);

				//se manda el value_id porque al id se le concateno el consecutivo de filas

				llenarPlan(datos[0].valor.cod, "ing_plaselPlan" + value_id)
			}
		).on({
			change: function () {
				var cmp = this;
				consolidarResponsableSeleccionado(cmp);
			}
		});
}

function consolidarResponsableSeleccionado(cmp) {

	setTimeout(function () {

		//Pregunto si la pareja es diferente
		cmp.value = $.trim(cmp.value);
		if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
			|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
		) {
			alerta(" Digite una Aseguradora v\u00E1lida")
			$("input[type=hidden]", cmp.parentNode).val('');
			cmp.value = '';
			cmp.focus();
			// cmp.blur();
		}
	}, 200);
}

function buscarCUPS(contenedor, indice) {
	if (!contenedor) {
		contenedor = "div_admisiones";
	}
	if (!indice) {
		indice = 0;
	}
	//Asigno autocompletar para la busqueda de paises
	$("[name=ing_cactxtcups]:eq(" + indice + ")", $("#" + contenedor)).autocomplete("admision_erp.php?consultaAjax=&accion=consultarCUPS",
		{
			cacheLength: 1,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 3,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Si cup elegido ya esta en la lista, no aceptarlo
				$(this).parent().parent()

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.cod + "-" + datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$(this).next("input[type=hidden]").val(datos[0].valor.cod);
			}
		).on({
			change: function () {

				var cmp = this;
				if (cmp.value == "") {
					$(cmp).next("input[type='hidden'][name='ing_cachidcups']").val("");
					$(cmp).next("input[type='hidden'][name='ing_cachidcups']").next("input[type='hidden'][name='id_idcups']").val("");
					return;
				}
				setTimeout(function () {
					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $(this).next("input[type=hidden]").val())
					) {
						alerta(" Digite un C\u00F3digo CUP v\u00E1lido")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);
			}
		});
}

function buscarImpresionDiagnostica() {
	//Asigno autocompletar para la busqueda de impresiones diagnosticas
	$("#ing_digtxtImpDia").autocomplete("admision_erp.php?consultaAjax=&accion=consultarImpresionDiagnostica",
		{
			extraParams: {
				edad: function (campo) {

					var objEdad = calcular_edad_detalle($("#pac_fnatxtFecNac").val());

					var edadDec = objEdad.age + objEdad.month / 12 + objEdad.day / 365;

					return edadDec;
				},
				sexo: function (campo) {
					return $("[name=pac_sexradSex]:checked").val();
				}
			},
			cacheLength: 0,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 3,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);
				if (datos == 0)
					return false;

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {

				if ($.trim(data) == 0) {
					alerta("Por favor ingrese una fecha de nacimiento para consultar la impresion diagnostica");
					return false;
				}
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.cod + "-" + datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).on({
			change: function () {

				var cmp = this;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite un Diagn\u00F3stico v\u00E1lido")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);
			}
		});
}

function buscarTarifaParticular() {
	//Asigno autocompletar para la busqueda de impresiones diagnosticas
	$("#ing_tartxt").autocomplete("admision_erp.php?consultaAjax=&accion=buscarTarifaParticular&wbasedato=" + $("#wbasedato").val(),
		{
			cacheLength: 0,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 1,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);
				if (datos == 0)
					return false;

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {

				if ($.trim(data) == 0) {
					alerta("No se han encontrado datos");
					return false;
				}
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.cod + "-" + datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).on({
			change: function () {

				var cmp = this;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite una tarifa v\u00E1lida")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);
			}
		});
}

function resetear(inicio) {
	//Variable para saber si esta en modo consulta o no
	modoConsulta = false;

	//todos los que tengan la clase reset se ponen en blanco
	$("#div_admisiones").find(":input").each(function () {
		if ($(this).hasClass('reset')) {
			$(this).val("");
		}

	});

	// iniciarMarcaAqua();
	//para mostrar la fecha actual
	var now = new Date();
	var hora = now.getHours();
	var minutos = now.getMinutes();
	var segundos = now.getSeconds();
	if (hora < 10) { hora = '0' + hora }
	if (minutos < 10) { minutos = '0' + minutos }
	if (segundos < 10) { segundos = '0' + segundos }
	horaActual = hora + ":" + minutos + ":" + segundos;


	//datos por defecto a iniciar
	$("#pac_zonselZonRes").val('U');
	//$( "#pac_trhselTipRes" ).val( 'N' );
	$("#ing_lugselLugAte").val('1');
	$("#pac_tdoselTipoDoc").val('CC');
	$("#pac_tdaselTipoDocRes").val('CC');
	$("#pac_fnatxtFecNac").val($("#fechaAct").val()); //fecha aut
	calcular_edad($("#pac_fnatxtFecNac").val()); //pac_fnatxtFecNac
	$("#ing_feitxtFecIng").val($("#fechaAct").val()); //fecha ing
	$("input[name='res_firtxtNumcon']").val($("#fechaAct").val()); //fecha ing
	// $( "#ing_hintxtHorIng" ).val($( "#horaAct" ).val() ); //hora ing
	$("#ing_hintxtHorIng").val(horaActual);
	$("#ing_fhatxtFecAut").val($("#fechaAct").val()); //fecha aut
	$("#ing_hoatxtHorAut").val(horaActual); //hora aut

	valorDefecto = $("#ing_claselClausu>option[defecto='on']").val();

	if (valorDefecto == undefined)
		$("#ing_claselClausu").val(1);
	else
		$("#ing_claselClausu").val(valorDefecto);
	$("#pac_fnatxtFecNac").val("");
	$("#pac_petselPerEtn").val(6);

	resetAqua();


	$("#bot_navegacion").css("display", "none"); //se oculta el div de navegacion de resultados
	$("#bot_navegacion1").css("display", "none"); //se oculta el div de navegacion de resultados

	//2014-05-06validarTipoResp('');
	var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
	validarTipoResp(objetoRes[0]);

	//se borran todos los label de error que se encuentren
	$("label").remove();
	//se pone el estado del paciente en blanco como es un span se le envia .html
	$("#spEstPac").html("");

	$("#div_accidente_evento").css({ display: "none" });
	//se ponen los campos readonly false para que se puedan llenar
	$('#pac_doctxtNumDoc').attr("readonly", false);
	$('#ing_histxtNumHis').attr("readonly", false);
	$('#ing_nintxtNumIng').attr("readonly", false);

	//Al iniciar datos se borra el log
	if (!inicio) {
		borrarLog($("#key"));
	}

	$("#radAdmision").attr("checked", true);
	$("#pac_fectxtFechaPosibleIngreso").val($("#pac_fectxtFechaPosibleIngreso").attr("msgerror"));
	$("[name=radPreAdmi]:checked").click();

	ultimaPreadmisionCargada = '';

	//Agosto 23 de 2013
	$("#ing_seisel_serv_ing").change();
	$("#cargoDatosConsulta").val("off");
	//
	//$("#div_topes").html("");

	// --> Limpiar el numero del turno, 2015-11-24: Jerson trujillo.
	$("#numTurnoPaciente").attr("valor", "").html("SIN TURNO!!!");

	$("#wfiniAdm").val("");
	$("#whiniAdm").val("");
}

// -------------------------------------------------------------------
// --> Guarda en varible temporal el numero del turno, jerson trujillo
// -------------------------------------------------------------------
function conservarNumeroDeTurno() {
	numeroTurnoTemporal = $("#numTurnoPaciente").attr("valor");
}

function validarOrigenAte(obj) {
	objq = jQuery(obj);
	var origen = objq.val();

	//Para controlar si desea cancelar accidente de transito o evento catastrofico
	if (obj.lastValue == '02' || obj.lastValue == '06') {

		if (obj.lastValue == '02') {
			var contenedor = $("#accidentesTransito")[0];
			if (contenedor.lastInfo && contenedor.lastInfo != '') {
				if (confirm("Desea ignorar los cambios realizados en accidente de tr\u00E1nsito?")) {
					resetearAccidentes();
					$("#div_accidente_evento").css({ display: "none" });
					reOrdenarResponsables();
				}
				else {
					obj.value = '02';
					return;
				}
			}
		}
		else {
			var contenedor = $("#eventosCatastroficos")[0];
			if (contenedor.lastInfo && contenedor.lastInfo != '') {
				if (confirm("Desea ignorar los cambios realizados en eventos catastr\u00F3ficos?")) {
					resetearEventosCatastroficos();
					$("#div_accidente_evento").css({ display: "none" });
				}
				else {
					obj.value = '06';
					return;
				}
			}
		}
	}

	//var origen = $("#ing_caiselOriAte").val();
	if (origen == '02') {
		mostrarAccidentesTransito();
		//20140225$("#div_datos_autorizacion").css("display", "none");
		//reOrdenarResponsables();
	}
	else if (origen == '06') {
		listarEventosCatastroficos();
		//20140225$("#div_datos_autorizacion").css("display", "none");
	}
	if (origen != '06' && origen != '02') {
		//20140225$('#div_datos_autorizacion').css("display", "");
	}
	//validarTipoResp('');
	var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
	validarTipoResp(objetoRes[0]);
}

function calcular_edad(fecha, cmp) {
	var today = new Date();
	var birthDate = new Date($("#pac_fnatxtFecNac").datepicker("getDate"));
	var age = today.getFullYear() - birthDate.getFullYear();
	var m = today.getMonth() - birthDate.getMonth();
	if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
		age--;
	}
	$("#txtEdad").val(age);

	//Si tiene menos de 18 no puede tener documento tipo cedula
	if (cmp) {
		if (age < 18 && $("#pac_tdoselTipoDoc").val() == "CC") {

			//Se muestra mensaje
			alerta(" El paciente debe ser mayor de edad.");

			//Se borra la fecha de nacimiento
			$("#pac_fnatxtFecNac").val('');

			//Se borra la Edad
			$("#txtEdad").val('');

			//Muestro de nuevo el calendario
			setTimeout(function () {
				$("#pac_fnatxtFecNac").focus();
				$("#pac_fnatxtFecNac").click();
			}, 200
			)
		}

		$("#pac_fnatxtFecNac").focus();
		$("#pac_fnatxtFecNac").removeClass("campoRequerido");
	}
}

function validarPacienteRem() {

	var pacRem = $("input[name=pac_remradPacRem]:checked").val();
	if (pacRem == 'S') {
		$("#pac_iretxtIpsRem").attr("disabled", false);
		$("#pac_cactxtCodAce").attr("disabled", false);
		$("#ing_vretxtValRem").removeAttr("disabled");
	}
	else {
		$("#pac_iretxtIpsRem").attr("disabled", true);
		$("#pac_cactxtCodAce").attr("disabled", true);
		$("#ing_vretxtValRem").val("");
		$("#ing_vretxtValRem").attr("disabled", "disabled");
	}

	resetAqua($("#div_int_datos_personales"));
}

function validarTipoResp(tipoResponsable) {

	var tipResp = $(tipoResponsable).val();
	//console.log("Validar respon: "+tipResp);
	var origen = $("#ing_caiselOriAte").val();
	var servicio = $("#ing_seisel_serv_ing").val();

	auxx = jQuery(tipoResponsable);
	var filaResponsable = auxx.parents("tr[id$=tr_tabla_eps]");
	if (!filaResponsable) {
		var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
		filaResponsable = $("#" + prefijo_trEps);
	}

	//2014-10-20 esconder los datos solicitados para particulares y mostrar los que se ocultan cuando es particular
	filaResponsable.find(".dato_esconder_particulares").show().children().attr("disabled", false);
	filaResponsable.find(".dato_particulares").hide().children().attr("disabled", true);

	/* llamado desde validarOrigenAte()
	diferente a accidente de transito y a evento catastrofico, no se a seleccionado empresa
	*/
	if ((tipResp == '' || tipResp == 'E') && origen != '02' && origen != '06') {
		$('#tabla_responsables_1_2').css('display', 'none');
		$('#tr_titulo_tercer_resp').css('display', 'none'); //se oculta
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").attr("disabled", false);
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_firtxtNumcon],[id^=res_ffrtxtNumcon]", tipoResponsable.parentNode.parentNode).attr("disabled", false);
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).addClass("campoRequerido");
		//resetAqua( $( "#div_int_pag_aut" ) );
		$("[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_ffrtxtNumcon],[id^=res_comtxtNumcon]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido"); //si se descomentan agregarlos donde se adiciona la clase
		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop").attr("disabled", true);
		resetAqua($("#div_int_pag_aut"));
	}
	/* llamado desde validarOrigenAte()
	igual a accidente de transito y a evento catastrofico, no se a seleccionado empresa
	*/
	else if ((tipResp == '' || tipResp == 'E') && (origen == '02')) //empresa y es accidente de transito o evento catastrofico
	{
		$('#tabla_responsables_1_2').css('display', '');
		$('#tr_titulo_tercer_resp').css('display', '');
		//Los datos del pagador siempre deben estar activas el primer tr de txt de la tabla_eps
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon]", tipoResponsable.parentNode.parentNode).attr("disabled", false);
		//el primer tr de txt de la tabla_eps
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).addClass("campoRequerido");
		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#ing_vretxtValRem").attr("disabled", false);
		buscarPrimerResp();
		resetAqua($("#div_int_pag_aut"));
	}
	/*
	si es particular
	*/
	else if (tipResp == 'P' && origen != '02' && origen != '06') {

		//Los datos del pagador siempre deben estar activas se hace al primer tr de txt de la tabla_eps
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon]", tipoResponsable.parentNode.parentNode).attr("disabled", true);
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").attr("disabled", true);
		$("[id^=ing_cemtxtCodAse]", tipoResponsable.parentNode.parentNode).val('');
		$("[id^=ing_cemhidCodAse]", tipoResponsable.parentNode.parentNode).val('');
		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#ing_vretxtValRem").attr("disabled", true);

		//se remueve la clase requerida a los que estan deshabilitados al primer tr de txt de la tabla_eps
		//$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido");
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido");
		$("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido");

		$('#tabla_responsables_1_2').css('display', 'none');
		$('#tr_titulo_tercer_resp').css('display', 'none');

		filaResponsable.find(".dato_esconder_particulares").hide().children().attr("disabled", true);
		filaResponsable.find(".dato_particulares").show().children().attr("disabled", false);
		resetAqua($("#div_int_pag_aut"));
	}
	else if (tipResp == 'P' && (origen == '02')) {
		$('#tabla_responsables_1_2').css('display', ''); //se muestra
		$('#tr_titulo_tercer_resp').css('display', ''); //se muestra

		//se remueve clase campo requerido
		filaResponsable.find("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_ffrtxtNumcon],[id^=res_comtxtNumcon]").removeClass("campoRequerido").attr("disabled", true);
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").removeClass("campoRequerido").attr("disabled", true);;
		//se deshabilitan
		$("[id^=ing_cemtxtCodAse]", tipoResponsable.parentNode.parentNode).val('');
		$("[id^=ing_cemhidCodAse]", tipoResponsable.parentNode.parentNode).val('');
		$("[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).val('');

		//se habilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#ing_vretxtValRem").attr("disabled", false);
		buscarPrimerResp();

		filaResponsable.find(".dato_esconder_particulares").hide().children().attr("disabled", true);
		filaResponsable.find(".dato_particulares").show().children().attr("disabled", false);
		resetAqua($("#div_int_pag_aut"));
	}
	else if (tipResp == 'E' && origen == '06') {
		$('#tabla_responsables_1_2').css('display', 'none');
		$('#tr_titulo_tercer_resp').css('display', 'none'); //se oculta

		//se adiciona la clase requerida a los que estan deshabilitados
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").removeClass("campoRequerido").attr("disabled", true);

		//resetAqua( $( "#div_int_pag_aut" ) );
		filaResponsable.find("[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_ffrtxtNumcon],[id^=res_comtxtNumcon]").removeClass("campoRequerido"); //si se descomentan agregarlos donde se adiciona la clase

		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#dat_Accre2hidCodRes2").attr("disabled", true);
		//se quitan porque ya el segundo responsable pasa a ser valor remitido #restxtCodRes, #re2txtCodRes2, #re2hidtopRes2

		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon]", tipoResponsable.parentNode.parentNode).attr("disabled", false);
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).addClass("campoRequerido");
		resetAqua($("#div_int_pag_aut"));
	}
	else if (tipResp == 'P' && origen == '06') //Cambiar
	{
		$('#tabla_responsables_1_2').css('display', 'none');
		$('#tr_titulo_tercer_resp').css('display', 'none'); //se oculta
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").attr("disabled", true);
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_firtxtNumcon],[id^=res_ffrtxtNumcon]", tipoResponsable.parentNode.parentNode).attr("disabled", true);

		$("[id^=ing_cemtxtCodAse]", tipoResponsable.parentNode.parentNode).val('');
		$("[id^=ing_cemhidCodAse]", tipoResponsable.parentNode.parentNode).val('');

		//se adiciona la clase requerida a los que estan deshabilitados
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").removeClass("campoRequerido");
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_ffrtxtNumcon],[id^=res_comtxtNumcon]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido");

		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#ing_vretxtValRem").attr("disabled", true);

		filaResponsable.find(".dato_esconder_particulares").hide().children().attr("disabled", true);
		filaResponsable.find(".dato_particulares").show().children().attr("disabled", false);

		resetAqua($("#div_int_pag_aut"));
	}
}

function llenarPlan(valor, selectHijo) {
	$.ajax(
		{
			url: "admision_erp.php",
			context: document.body,
			type: "POST",
			data:
			{
				consultaAjax: '',
				accion: 'llenarSelectPlan',
				wbasedato: $("#wbasedato").val(),
				valor: valor
			},
			async: false,
			dataType: "json",
			success: function (data) {

				if (data.error == 1) {

				}
				else {
					$("#" + selectHijo).html(data.html); // update Ok.
					//$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				}
			}
		});
}

function getNombresCamposError() {
	var mensajes = new Array();

	if (camposConError != undefined) {
		var campo = "";
		for (var i = 0; i < camposConError.length; i++) {
			campo = camposConError[i];
			campo = jQuery(campo);
			if (campo.attr("msgcampo") != "") {
				if (mensajes.indexOf(campo.attr("msgcampo")) == -1) {
					mensajes.push(campo.attr("msgcampo"));
				}
			}
		}
	}
	var cadena = "";
	if (mensajes.length > 0) {
		for (var i = 0; i < mensajes.length; i++) {
			cadena = cadena + "-" + mensajes[i] + "\n";
		}
	}
	return cadena;
}

var intentos = 0;

function enviarDatos(automatico = '') {

	cambioDeCausaAacc = false;
	cambiarAaccTran = "off";
	if ($("#ing_caiselOriAte").val() != $("#codCausaInicial").val() && modoConsulta && $("#ing_caiselOriAte").val() == $("#codCausaAccTrans").val()) {
		cambioDeCausaAacc = true;
		cambiarAaccTran = "on";
	}
	// var filasRes=$('#tabla_eps >tbody >tr').length-2;
	// alert(filasRes);
	/*informacionIngresos == data, numRegistrosIng trae = historia(posicion) e ingreso(valor) traidos de mostrarDatos()
	informacionIngresos en la posicion de la historia que se esta mostrando con su respectivo valor y cuyo valor sea igual ==
	al ingreso que se esta mostrando y && informacionIngresos en la posicion infoIng e infoIng en la posicion actual(posAct) que
	que esta dentro de informacionIngresos todo lo anterior es:(el ingreso que se esta mostrando en pantalla )	y que este on
	*/
	/* 1.Si NO está en modo consulta se guarda la admisón
	2.Si esta en modoConsulta se pregunta si es el último ingreso, de ser así entonces se actualiza la admisión
	*/
	//2020-06-13
	//if (!modoConsulta || (modoConsulta && informacionIngresos.ultimoIngreso[$( "#ing_histxtNumHis" ).val()] == $( "#ing_nintxtNumIng" ).val() && informacionIngresos.infoing[ informacionIngresos.posAct ].pac_act == 'on' ) || ( modoConsulta && cambioDeCausaAacc ) )
	if (true) {

		var datosLlenos = $('#forAdmisiones').valid();
		iniciarMarcaAqua($('#forAdmisiones'));


		if (datosLlenos) {
			var validacion = validarCampos($("#div_admisiones"));
			//se agrega validacion para estos datos que no son requeridos pero esta enviando el mensaje
			if ($("#ing_poltxtNumPol").val() == $("#ing_poltxtNumPol").attr("msgAqua")) { $("#ing_poltxtNumPol").val(''); }

			if ($("#ing_ncotxtNumCon").val() == $("#ing_ncotxtNumCon").attr("msgAqua")) { $("#ing_ncotxtNumCon").val(''); }

			if ($("#ing_ordtxtNumAut").val() == $("#ing_ordtxtNumAut").attr("msgAqua")) { $("#ing_ordtxtNumAut").val(''); }

			if ($("#ing_npatxtNomPerAut").val() == $("#ing_npatxtNomPerAut").attr("msgAqua")) { $("#ing_npatxtNomPerAut").val(''); }

			$("[name=ing_tpaselTipRes]").each(function () {
				if ($(this).val() == "") {
					validacion = false;
					return false;
				}
			});

			$("[name='ing_poltxtNumPol']").each(function () {
				//alert(" joder pero porque no funciona: "+$(this).val()+" atributo aqua: "+$(this).attr("msgAqua") );
				if ($(this).val() == $(this).attr("msgAqua")) {
					$(this).val('');
				}
			});
			//alert("222 joder pero porque no funciona: "+$(this).val()+" atributo aqua: "+$(this).attr("msgAqua") );

			if (validacion) {
				//A todos los campos que tengan marca de agua y esten deshabilitado, les borro la marca de agua(msgerror)
				$("[aqua]:disabled").each(function () {
					if ($(this).val() == $(this).attr(this.aqAttr)) {
						$(this).val('');
					}
				});
				$("[aqua]").each(function () {
					if ($(this).val() == $(this).attr(this.aqAttr)) {
						$(this).val('');
					}
				});

				var objJson = cearUrlPorCamposJson($("#div_admisiones"), 'id');
				objJson = cearUrlPorCamposJson($("#div_admisiones"), 'ux', objJson);
				objJson = cearUrlPorCamposJson($("#accidentesTransito"), 'name', objJson);
				objJson = cearUrlPorCamposJson($("#eventosCatastroficos"), 'name', objJson);
				//para guardar la relacion evento - historia -- todo lo que tenga id lo envia
				objJson = cearUrlPorCamposJson($("#div_eventos_catastroficos"), 'id', objJson);
				//se envia si el checkbox esta chequeado para saber si hace la relacion o guarda un evento nuevo

				if ($("#div_eventos_catastroficos [id^=chkagregar]").is(':checked')) {
					objJson.relEvento = 'on';
				}
				else {
					objJson.relEvento = 'off';
				}
				//se colocan porque en las validaciones de tarifa solo se hacen para el primer resp
				var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
				objJson.ing_tpaselTipRes = $("#ing_tpaselTipRes" + prefijo_trEps).val();
				objJson.ing_cemhidCodAse = $("#ing_cemhidCodAse" + prefijo_trEps).val();


				objJson.accion = "guardarDatos";	//agrego un parametro más
				objJson.intentos = intentos;	//reintentos
				objJson.wbasedato = $("#wbasedato").val();
				objJson.consultaAjax = "";
				objJson.historia = $("#ing_histxtNumHis").val();
				objJson.ingreso = $("#ing_nintxtNumIng").val();
				objJson.documento = $.trim($("#pac_doctxtNumDoc").val());
				documentoAnular = $.trim($("#pac_doctxtNumDoc").val());
				objJson.tipodoc = $("#pac_tdoselTipoDoc").val();
				objJson.cambioConsorcio = $("#cambioConsorcio").val();
				tipodocAnular = $("#pac_tdoselTipoDoc").val();
				objJson.habilitarPreanestesiaAD = $("#asociarPreanestesia").val();

				// --> Nuevo parametro, numero del turno. Jerson Trujillo.
				objJson.turno = $("#numTurnoPaciente").attr("valor");
				objJson.wfiniAdm = $("#wfiniAdm").val();
				objJson.whiniAdm = $("#whiniAdm").val();
				objJson.solucionCitas = $("#solucionCitas").val();//->2016-02-26
				objJson.logturCit = $("#TurnoEnAm").val();
				/*console.log( objJson.solucionCitas );
				return;*/

				objJson.wemp_pmla = $("#wemp_pmla").val();
				objJson.pacienteNN = $("#pac_tdoselTipoDoc > option:selected").attr("docXhis");
				objJson.modoConsulta = modoConsulta;
				objJson.cambiarAaccTran = cambiarAaccTran;

				var esAccTransito = $("#tabla_responsables_1_2").is(":visible");
				if ($("[name=dat_Acccas_ux_acccas]").val() != "") {
					esAccTransito = true;
				}
				var indice_res = 0;

				/*Responsables para la tabla 000205*/
				//objJson.responsables = {};
				objJson.responsables1 = {};

				//En la primera posicion del arr de responsables siempre va el responsable de transito
				if (esAccTransito) {
					if ($("#dat_AccreshidCodRes24").val() == "") {
						alerta("No existe aseguradora SOAT, por favor verifique los datos del accidente.");
						return;
					}
					objJson.responsables1[0] = {};
					objJson.responsables1[0].res_tdo = "NIT";//2014-10-22
					objJson.responsables1[0].ing_cemhidCodAse = $("#dat_AccreshidCodRes24").val();
					objJson.responsables1[0].res_nom = $("#restxtCodRes").val();//2014-10-22
					objJson.responsables1[0].ing_tpaselTipRes = "E";
					objJson.responsables1[0].ing_plaselPlan = "00";
					if ($("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
						$("input[name='dat_Accpol_ux_accpol']").val("");
					objJson.responsables1[0].ing_poltxtNumPol = $("input[name=dat_Accpol_ux_accpol]").val();
					objJson.responsables1[0].ing_ncotxtNumCon = "";
					objJson.responsables1[0].ing_ordtxtNumAut = "";
				}

				$("tr[id$=_tr_tabla_eps]").each(function (index) {
					if (esAccTransito)
						indice_res = index + 1; //En la posicion 0 esta el responsable transito
					else
						indice_res = index;
					//objJson.responsables[ index ] = cearUrlPorCamposJson(  this , 'id' );
					objJson.responsables1[indice_res] = cearUrlPorCamposJson(this, 'name');

					//2014-10-22 Si es particular, el codigo del responsable pasa a ser el numero de documento que se ingreso
					if (objJson.responsables1[indice_res].ing_tpaselTipRes == "P") {
						objJson.responsables1[indice_res].ing_cemhidCodAse = objJson.responsables1[indice_res].res_doc;
					} else {
						//Si no es particular, el tipo de documento sera NIT, y el nombre el que corresponde al campo ing_cemtxtCodAse
						objJson.responsables1[indice_res].res_tdo = "NIT";
						objJson.responsables1[indice_res].res_nom = objJson.responsables1[indice_res].ing_cemtxtCodAse;
					}

					//objJson.responsables[ index ].cups = {};
					objJson.responsables1[indice_res].cups = {};
					objJson.responsables1[indice_res].cupsids = {};
					$(this).find("[name=ing_cachidcups]").each(function (index2) {
						if ($(this).val() != "") {
							objJson.responsables1[indice_res].cups[index2] = $(this).val();
							objJson.responsables1[indice_res].cupsids[index2] = $(this).next("input[type='hidden']").val();
						}
					});

					if (index == 0) {
						/*para el responsable 2 de accidente*/
						objJson.codAseR2 = $("[id^=ing_cemhidCodAse1]", this).val();
					}

					if (index == 1) {
						//para responsable 3 de accidente
						objJson.tipoEmpR3 = $("[id^=ing_tpaselTipRes]", this).val();
						//cuando sea empresa
						if ($("id^=ing_tpaselTipRes", this).val() == 'P') {
							objJson.codAseR3 = $("#pac_crutxtNumDocRes").val();
							objJson.nomAseR3 = $("#pac_nrutxtNomRes").val();
						}
						else {
							objJson.codAseR3 = $("[id^=ing_cemhidCodAse]", this).val();
							objJson.nomAseR3 = $("[id^=ing_cemtxtCodAse]", this).val();
						}
					}
				});
				/*Fin Responsables para la tabla 000205*/

				//DATOS DE LOS RESPONSABLES QUE VIAJAN A UNIX
				//objJson = cearUrlPorCamposJson( $("#1_tr_tabla_eps"), 'ux', objJson );


				/*Guardar los topes por responsable*/
				objJson.topes = {};
				objJson.topesId = {};

				$("tr[id$=_tr_tabla_topes]").each(function (index) {
					//console.log($(this).html()+"\n\n");
					objJson.topes[index] = cearUrlPorCamposJson(this, 'name');

				});
				//return;
				/*Fin Guardar los topes por responsable*/

				/****************************************************************
				 * Agosto 15 de 2013
				 *
				 * Si está activo preadmisión se guarda el dato como preadmisión
				 ****************************************************************/
				if ($("[name=radPreAdmi]:checked").val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR') {
					objJson.accion = "guardarDatosPreadmision";	//agrego un parametro más
					objJson.modoConsulta = false;
				}
				/****************************************************************/

				/********************************************************
				 * Septiembre 19 de 2013
				 ********************************************************/
				//Busco los campos que son depends y están vacios con propiedad ux
				$("[depend][ux]").each(function () {
					if ($(this).val() == '' || ($(this).val() == $(this).attr(this.aqAttr))) {
						objJson[$(this).attr("ux")] = $("#" + $(this).attr("depend")).val();
					}
				});
				/********************************************************/

				//A todos los campos que tengan marca de agua y esten deshabilitado, le pongo la marca de agua
				$("[aqua]:disabled").each(function () {
					if ($(this).val() == '') {
						$(this).val($(this).attr(this.aqAttr));
					}
				});

				//RESPONSABLE QUE VIAJA A UNIX
				objJson = cearUrlPorCamposJson($("tr[id$=_tr_tabla_eps]").eq(0), 'ux', objJson);

				if (esAccTransito) {
					objJson._ux_mreemp_ux_pacemp_ux_accemp = "E";
					objJson._ux_pacres_ux_mreres = $("#dat_AccreshidCodRes24").val();
					objJson._ux_mrepla = "00";
					if ($("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
						$("input[name='dat_Accpol_ux_accpol']").val("");
					objJson._ux_pacpol = $("input[name=dat_Accpol_ux_accpol]").val();
				}

				if (objJson._ux_pacpol == "Digite la Poliza")
					objJson._ux_pacpol = "";

				for (var iii in objJson.responsables1[0]) {
					objJson[iii] = objJson.responsables1[0][iii];
				}

				//2014-10-22 Siempre tiene que existir el primer responsable
				//2014-10-22if( objJson.responsables1[ 0 ].ing_tpaselTipRes == "E" ){
				if (objJson.responsables1[0].ing_cemhidCodAse == "") {
					alerta("NO existe primer responsable, por favor verifique.");
					return;
				}

				//2014-10-22
				$.blockUI({ message: "Por favor espere..." });
				$.post("admision_erp.php",
					objJson,
					function (data) {
						if (automatico != "on")
							$.unblockUI();
						if (isJSON(data) == false) {
							alerta("RESPUESTA NO ESPERADA\n" + data);
							return;
						}
						data = $.parseJSON(data);

						if (data.error == 1) {
							alerta(data.mensaje);
						}
						else {

							if (data.mensaje != '') {
								//Se oculta todos los acordeones
								//$( "[acordeon]" ).accordion( "option", "active", false );

								//Se muestra el acordeon de DATOS DE INGRESO - DATOS PERSONALES
								//$( "#div_datosIng_Per_Aco" ).accordion( "option", "active", 0 );

								try {
									window.scrollTo(0, 0);
								} catch (e) { }

								if (data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios") {

									if (objJson.accion == 'guardarDatosPreadmision') {
										var esadmision = 'no';
									}
									else {
										var esadmision = 'si';
									}
									//alert("inserto los topes");
									$(".grabarCuandoAdmite").each(function () {


										//grabartoperesponsable($whistoria,$wingreso,$responsable,$insertar,$activo);
										activo = 'on';
										$.post("admision_erp.php",
											{
												accion: "insertarTopes",
												consultaAjax: '',
												whistoria: data.historia,
												wingreso: data.ingreso,
												responsable: $(this).attr('responsable'),
												wemp_pmla: $('#wemp_pmla').val(),
												tema: $('#tema').val,
												insertar: $(this).val(),
												activo: activo,
												esadmision: esadmision,
												documento: $.trim($("#pac_doctxtNumDoc").val()),
												tipodocumento: $("#pac_tdoselTipoDoc").val()

											}, function (data) {
												//alert("entro1");
											});


									});





									//location.reload();

									if ($("#soportesautomaticos").val() == 'on') {




										//-- se agregavalidacion de soportes digitalizados
										//-- si la validacion de digitalizacion , si ya la digilitalizacion esta encendida
										//---si ya el centro de costos esta en on para la digitalizacion
										//---si ya la empresa hace la digtalizacion   no se piden soportes
										//-- parametro de digitalizacion apagado , pido soportes(programa viejo)
										if ($("#parametroDigitalizacion").val() == 'on') {

											// si el parametro esta encendido tengo que mirar igual si la empresa y el centro de costos piden digitalizacion




											var responsable1 = objJson.responsables1[0].ing_cemhidCodAse;
											var Empresadigitalizacion;
											var todosdigitalizacion;
											var ccodigitalizacion;
											var data2 = '';
											$.ajax(
												{
													url: "admision_erp.php",
													context: document.body,
													type: "POST",
													data:
													{
														accion: "empresahacedigitalizacion",
														consultaAjax: '',
														wemp_pmla: $('#wemp_pmla').val(),
														tema: $('#tema').val,
														empresa: responsable1
													},
													async: false,
													success: function (data) {
														if (data == '') {
															Empresadigitalizacion = 'off';
														}
														else {
															Empresadigitalizacion = 'on';
															if (data == '*') {

																todosdigitalizacion = 'si';
															}
															else {

																todosdigitalizacion = 'no';
																ccodigitalizacion = data.split(',');
															}

														}
														data2 = data;

													}
												});


											var ccoingreso = $("#ing_seisel_serv_ing").val();
											ccoingreso = ccoingreso.split('-');


											if (Empresadigitalizacion == 'on') {
												if (data2 == '*') {
													//alert(" empresa en on y * no muestre soportes");
													setTimeout(function () {
														//se llenan los campos de historia,ingreso,documento despues de guardar
														$("#ing_histxtNumHis").val(data.historia);
														$("#ing_nintxtNumIng").val(data.ingreso);
														$("#pac_doctxtNumDoc").val(data.documento);
														//se ponen documento,historia,ingreso readonly
														$('#pac_doctxtNumDoc').attr("readonly", true);
														$('#ing_histxtNumHis').attr("readonly", true);
														$('#ing_nintxtNumIng').attr("readonly", true);



														//Si se registró muestro se imprime el sticker
														if (data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios") {
															var edad = calcular_edad_detalle($("#pac_fnatxtFecNac").val());


															var wtip = 0;

															if (edad.age == 0 && edad.month <= 6) {
																wtip = 2;
															}
															else if (edad.age <= 12) {
																wtip = 1;
															}

															if (data.mensaje != "Se actualizo correctamente") {
																try {

																	imprimirHistoria = $("#chk_imprimirHistoria").is(":checked");
																	wbasedatoImp = $("#wbasedatoImp").val();
																	if (imprimirHistoria) {
																		winSticker = window.open("../../ips/reportes/r001-admision.php?wpachi=" + data.historia + "&wingni=" + data.ingreso + "&empresa=" + wbasedatoImp);

																	}
																	wemp_pmla = $('#wemp_pmla').val();
																	if ($("#imprimirSticker").val() == "on") {
																		winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wemp_pmla=" + wemp_pmla + "&wtip=" + wtip + "&whis=" + data.historia, '', 'fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');
																		winSticker.onload = function () {
																			$("input:radio[value=" + wtip + "]", winSticker.document).attr("checked", true);
																		}
																	}
																	//}

																	//Checkeo el radio button correspondiente de la ventana emergente
																} catch (err) {
																	alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																}
															}

															hayCambios = false;
														}

														alerta(data.mensaje);


														if (data.mensaje == "Se actualizo correctamente") {
															hayCambios = false;
															mostrarPreadmision();
														}
														else {
															//Se Inicia el formulario

															if ($("#AgendaMedica").val() == "on") {

															}




														}

														hayCambios = false;
														location.reload();

													}, 200

													);
													//location.reload();
												}
												else {
													if ($.inArray(ccoingreso[1], ccodigitalizacion) != -1) {
														//alert("empresa en on y cco "+ccoingreso[1]+" no muestre soportes");
														setTimeout(function () {
															//se llenan los campos de historia,ingreso,documento despues de guardar
															$("#ing_histxtNumHis").val(data.historia);
															$("#ing_nintxtNumIng").val(data.ingreso);
															$("#pac_doctxtNumDoc").val(data.documento);
															//se ponen documento,historia,ingreso readonly
															$('#pac_doctxtNumDoc').attr("readonly", true);
															$('#ing_histxtNumHis').attr("readonly", true);
															$('#ing_nintxtNumIng').attr("readonly", true);



															//Si se registró muestro se imprime el sticker
															if (data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios") {
																var edad = calcular_edad_detalle($("#pac_fnatxtFecNac").val());


																var wtip = 0;

																if (edad.age == 0 && edad.month <= 6) {
																	wtip = 2;
																}
																else if (edad.age <= 12) {
																	wtip = 1;
																}

																if (data.mensaje != "Se actualizo correctamente") {
																	try {

																		imprimirHistoria = $("#chk_imprimirHistoria").is(":checked");
																		wbasedatoImp = $("#wbasedatoImp").val();
																		if (imprimirHistoria) {
																			winSticker = window.open("../../ips/reportes/r001-admision.php?wpachi=" + data.historia + "&wingni=" + data.ingreso + "&empresa=" + wbasedatoImp);

																		}
																		wemp_pmla = $('#wemp_pmla').val();
																		if ($("#imprimirSticker").val() == "on") {
																			winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wemp_pmla=" + wemp_pmla + "&wtip=" + wtip + "&whis=" + data.historia, '', 'fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');
																			winSticker.onload = function () {
																				$("input:radio[value=" + wtip + "]", winSticker.document).attr("checked", true);
																			}
																		}
																		//}

																		//Checkeo el radio button correspondiente de la ventana emergente
																	} catch (err) {
																		alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																	}
																}

																hayCambios = false;
															}

															alerta(data.mensaje);


															if (data.mensaje == "Se actualizo correctamente") {
																hayCambios = false;
																mostrarPreadmision();
															}
															else {
																//Se Inicia el formulario

																if ($("#AgendaMedica").val() == "on") {

																}

																location.reload();


															}

															hayCambios = false;
															location.reload();

														}, 200
														);

													}
													else {
														//alert("empresa en on y muestre");

														crearListaAutomatica(data.historia, data.ingreso, '', '', data.documento, data.mensaje);

													}
												}

											}
											else {
												//alert("muestre soportes");
												crearListaAutomatica(data.historia, data.ingreso, '', '', data.documento, data.mensaje);
											}




										}
										else {
											crearListaAutomatica(data.historia, data.ingreso, '', '', data.documento, data.mensaje);

										}


										//--
									} else {
										if (data.mensaje == "Se actualizo correctamente") {
											alerta(data.mensaje);
											hayCambios = false;
											mostrarPreadmision();
										} else {//2020-03-20
											alerta(data.mensaje);
											hayCambios = false;
											if (data.historia != '') {
												var edad = calcular_edad_detalle($("#pac_fnatxtFecNac").val());

												// var edad = $( "#txtEdad" ).val();
												var wtip = 0;

												if (edad.age == 0 && edad.month <= 6) {
													wtip = 2;
												}
												else if (edad.age <= 12) {
													wtip = 1;
												}
												try {
													//Abro el programa de sticker
													//crearListaAutomatica(data.historia , data.ingreso);
													//winSticker = window.open( "../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia );
													wemp_pmla = $('#wemp_pmla').val();
													winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wemp_pmla=" + wemp_pmla + "&wtip=" + wtip + "&whis=" + data.historia, '', 'fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');

													//Checkeo el radio button correspondiente de la ventana emergente
													winSticker.onload = function () {
														$("input:radio[value=" + wtip + "]", winSticker.document).attr("checked", true);
													}
												} catch (err) {
													alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
												}
											}
											mostrarPreadmision();
										}
									}

								}
								else {
									if (objJson.accion == 'guardarDatosPreadmision') {
										$(".grabarCuandoAdmite").each(function () {


											//grabartoperesponsable($whistoria,$wingreso,$responsable,$insertar,$activo);
											activo = 'on';
											$.post("admision_erp.php",
												{
													accion: "insertarTopes",
													consultaAjax: '',
													whistoria: data.historia,
													wingreso: data.ingreso,
													responsable: $(this).attr('responsable'),
													wemp_pmla: $('#wemp_pmla').val(),
													tema: $('#tema').val,
													insertar: $(this).val(),
													activo: activo,
													esadmision: esadmision,
													documento: $.trim($("#pac_doctxtNumDoc").val()),
													tipodocumento: $("#pac_tdoselTipoDoc").val()

												}, function (data) {
													//alert("entro1");
												});


										});
										//alert("grabo preadmision");
										location.reload();
									}
								}

								if (data.error != 4) {
									//se borran los trs de la tabla_eps menos la primera
									$("#tabla_eps").find("tr[id$='_tr_tabla_eps']").remove();
									var wbasedato = $("#wbasedato").val();
									var wemp_pmla = $("#wemp_pmla").val();
									addFila('tabla_eps', wbasedato, wemp_pmla);
									//se ponen los valores de ese tr en blanco
									$("[id^=ing_tpaselTipRes],[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", $('#tabla_eps >tbody >tr').eq(2)).val("");
									setTimeout(function () {
										borrarLog($("#key"));
									}, 1100);
								}

							}

							//Al guardar los datos se borra el log
							//borrarLog( $( "#key" ) );
							/*se pone ese div en blanco para que cuando guarde una admsion con
							evento catastrofico, si guarda otra inmediatamente muestre la lista
							de eventos en blanco.
							*/
							$("#div_contenedor").html("");
							//$( "#div_contenedor" ).empty();

							//se pone el div que contiene los topes en blanco
							// $("[id^=div_cont_tabla_topes]").html(""); //#div_cont_tabla_topes que empiece
							$("#div_contenedor_topes").html(""); //#div_cont_tabla_topes que empiece
						}
					}
				);
			}
			else {
				var campos = getNombresCamposError();
				alerta(" Hay datos incompletos, por favor verifique los campos de color amarillo \n " + campos);
			}
		}
		else {
			alerta(" Hay datos incompletos, por favor verifique los campos de color amarillo");
		}

	}
	else {
		alerta("Solo se permite actualizar el ultimo ingreso");
	}
}

/**/
$(function () {
	$('#forAdmisiones').validate({
		rules: {
			pac_cortxtCorEle: {
				required: false, //para validar campo vacio
				email: true  //para validar formato email
			},
			pac_cretxtCorResp: {
				required: false, //para validar campo vacio
				email: true  //para validar formato email
			},
			//movil del paciente
			pac_movtxtTelMov: {
				required: {
					depends: function (element) {

						if ($('#pac_teltxtTelFij').val() == $('#pac_teltxtTelFij').attr("msgerror") && $('#pac_movtxtTelMov').val() == $('#pac_movtxtTelMov').attr("msgerror")) {
							$('#pac_movtxtTelMov').val('');
						}

						return ($('#pac_teltxtTelFij').val() == $('#pac_teltxtTelFij').attr("msgerror"));

					}

				}
			},
			//fijo del paciente
			pac_teltxtTelFij: {
				required: {
					depends: function (element) {
						// return ($('#pac_movtxtTelMov').val() == '');
						// alert( $('#pac_movtxtTelMov').val() == $('#pac_movtxtTelMov').attr( "msgerror" ) && $('#pac_teltxtTelFij').val() == $('#pac_teltxtTelFij').attr( "msgerror" ) );
						if ($('#pac_movtxtTelMov').val() == $('#pac_movtxtTelMov').attr("msgerror") && $('#pac_teltxtTelFij').val() == $('#pac_teltxtTelFij').attr("msgerror")) {
							$('#pac_teltxtTelFij').val('');
						}

						return ($('#pac_movtxtTelMov').val() == $('#pac_movtxtTelMov').attr("msgerror"));
					}
				}
			},
			//direccion del paciente
			pac_dedtxtDetDir: {
				required: {
					depends: function (element) {

						if ($('#pac_dirtxtDirRes').val() == $('#pac_dirtxtDirRes').attr("msgerror") && $('#pac_dedtxtDetDir').val() == $('#pac_dedtxtDetDir').attr("msgerror")) {
							$('#pac_dedtxtDetDir').val('');
						}

						return ($('#pac_dirtxtDirRes').val() == $('#pac_dirtxtDirRes').attr("msgerror"));

					}

				}

			},
			//detalle dir del paciente
			pac_dirtxtDirRes: {
				required: {
					depends: function (element) {
						// return ($('#pac_dedtxtDetDir').val() == '');
						if ($('#pac_dedtxtDetDir').val() == $('#pac_dedtxtDetDir').attr("msgerror") && $('#pac_dirtxtDirRes').val() == $('#pac_dirtxtDirRes').attr("msgerror")) {
							$('#pac_dirtxtDirRes').val('');
						}

						return ($('#pac_dedtxtDetDir').val() == $('#pac_dedtxtDetDir').attr("msgerror"));

					}

				}

			},
			// direccion del resp pac
			pac_ddrtxtDetDirRes: {
				required: {
					depends: function (element) {

						if ($('#pac_drutxtDirRes').val() == $('#pac_drutxtDirRes').attr("msgerror") && $('#pac_ddrtxtDetDirRes').val() == $('#pac_ddrtxtDetDirRes').attr("msgerror")) {
							$('#pac_ddrtxtDetDirRes').val('');
						}
						return ($('#pac_drutxtDirRes').val() == $('#pac_drutxtDirRes').attr("msgerror"));

					}

				}

			},
			//detalle dir del resp pac
			pac_drutxtDirRes: {
				required: {
					depends: function (element) {
						// return ($('#pac_ddrtxtDetDirRes').val() == '');
						if ($('#pac_ddrtxtDetDirRes').val() == $('#pac_ddrtxtDetDirRes').attr("msgerror") && $('#pac_drutxtDirRes').val() == $('#pac_drutxtDirRes').attr("msgerror")) {
							$('#pac_drutxtDirRes').val('');
						}
						return ($('#pac_ddrtxtDetDirRes').val() == $('#pac_ddrtxtDetDirRes').attr("msgerror"));

					}

				}

			},
			//movil resp pac
			pac_mortxtNumResp: {
				required: {
					depends: function (element) {

						if ($('#pac_trutxtTelRes').val() == $('#pac_trutxtTelRes').attr("msgerror") && $('#pac_mortxtNumResp').val() == $('#pac_mortxtNumResp').attr("msgerror")) {
							$('#pac_mortxtNumResp').val('');
						}
						return ($('#pac_trutxtTelRes').val() != $('#pac_trutxtTelRes').attr("msgerror"));


					}

				}

			},
			//fijo resp pac
			pac_trutxtTelRes: {
				required: {
					depends: function (element) {

						if ($('#pac_mortxtNumResp').val() == $('#pac_mortxtNumResp').attr("msgerror") && $('#pac_trutxtTelRes').val() == $('#pac_trutxtTelRes').attr("msgerror")) {
							$('#pac_trutxtTelRes').val('');
						}
						return ($('#pac_mortxtNumResp').val() != $('#pac_mortxtNumResp').attr("msgerror"));
					}

				}

			}

		}, //rules
		messages: {
			pac_cortxtCorEle: {
				// required : "Debe ingresar el email",
				email: "Debe ingresar un email valido"
			},
			pac_cretxtCorResp: {
				// required : "Debe ingresar el email",
				email: "Debe ingresar un email valido"
			},

			pac_movtxtTelMov: "Debe ingresar el telefono fijo o el telefono movil", //movil pac
			pac_teltxtTelFij: "Debe ingresar el telefono fijo o el telefono movil", //fijo pac
			pac_dedtxtDetDir: "Debe ingresar la direccion o el detalle de la direccion", //dir pac
			pac_dirtxtDirRes: "Debe ingresar la direccion o el detalle de la direccion", //detalle dir pac
			pac_ddrtxtDetDirRes: "Debe ingresar la direccion o el detalle de la direccion", //dir resp pac
			pac_drutxtDirRes: "Debe ingresar la direccion o el detalle de la direccion", //detalle dir resp pac
			pac_mortxtNumResp: "Debe ingresar el telefono fijo o el telefono movil", //movil resp SI SE DESCOMENTA MIRAR LA ,
			pac_trutxtTelRes: "Debe ingresar el telefono fijo o el telefono movil" //fijo resp




		}, //messages
		errorClass: "errorMensajes"
		// validClass: "mensajeValido"
	});
});
/**/

function crearlista() {

	var flujo = '';
	var plan = '';
	$(".radioplan").each(function () {

		if ($(this).is(":checked")) {

			plan = $(this).val();
		}
	});


	$(".radioflujo").each(function () {

		if ($(this).is(":checked")) {

			flujo = $(this).val();

		}
	});

	if (plan != '' && flujo != '') {

		crearListaAutomatica($("#historiamodal").val(), $("#ingresomodal").val(), flujo, plan);
	}


}

function crearListaAutomatica(whistoria, wingreso, wflujo = '', wplan = '', documento, mensaje) {


	$.post("admision_erp.php",
		{
			accion: "crearListaAutomatica",
			consultaAjax: '',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,
			whistoria: whistoria,
			wingreso: wingreso,
			wflujo: wflujo,
			wplan: wplan
		}, function (data) {
			if (data.html == 'abrirModal') {

				$("#divPlanyFlujo").html(data.contenidoDiv + "<div id='datoppales'><input type='hidden' id='datosppaleshistoria' value='" + whistoria + "'><input type='hidden' id='datosppalesingreso' value='" + wingreso + "'><input type='hidden' id='datosppalesdocumento' value='" + documento + "'><input type='hidden' id='datosppalesmensaje' value='" + mensaje + "'>").show().dialog({
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "<div align='center' style='font-size:10pt'>Seleccion de Flujo y Plan</div>",
					width: "auto",
					height: "300",
					closeOnEscape: false,
					open: function (event, ui) { $(".ui-dialog-titlebar-close", ui.dialog).hide(); }

				});

				$("#divPlanyFlujo").css({
					width: 'auto', //probably not needed
					height: 'auto'
				});

				$("#divPlanyFlujo").dialog({ dialogClass: 'hide-close' });

			} else {
				// cambio de camilo


				if ($("#divPlanyFlujo").is(":visible")) {

				} else {
					location.reload();
				}

			}

			if (data.exito == 'si') {
				$("#divPlanyFlujo").css({
					width: 'auto', //probably not needed
					height: 'auto'
				});
				$("#divplanesyflujos").html('');
				$("#divexito").html('');
				$("#divexito").html(data.tabla + "<br><br><center><table><tr><td nowrap=nowrap><input   type='button' value='Enviar a siguiente nivel' onclick='enviarnivelsiguiente()'><input   type='button' value='Cerrar' onclick='onloadDesdeSoportes()'></td></tr></table></center>");
			}
		}, 'json');



}

function onloadDesdeSoportes() {

	historia = $("#datosppaleshistoria").val();
	ingreso = $("#datosppalesingreso").val();
	documento = $("#datosppalesdocumento").val();
	mensaje = $("#datosppalesmensaje").val();
	mensaje = $("#datosppalesmensaje").val();


	var u = 0;
	var variable = '';

	$(".checkboxlista").each(function () {


		if ($(this).attr('checked')) {

		}
		else {
			u++;
			if (u == 1) {
				variable += ' AND ( delsop="' + $(this).attr('soporte') + '"';
			}
			else
				variable += ' OR delsop="' + $(this).attr('soporte') + '"';

		}

	});
	if (u != 0) {
		variable += ")";
	}

	//alert(variable);

	$.post("admision_erp.php",
		{
			accion: "naautomatico",
			consultaAjax: '',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,
			whistoria: historia,
			wingreso: ingreso,
			opciones: variable
		}, function (data) {

			//alert(data);

		});

	setTimeout(function () {
		//se llenan los campos de historia,ingreso,documento despues de guardar
		$("#ing_histxtNumHis").val(historia);
		$("#ing_nintxtNumIng").val(ingreso);
		$("#pac_doctxtNumDoc").val(documento);
		//se ponen documento,historia,ingreso readonly
		$('#pac_doctxtNumDoc').attr("readonly", true);
		$('#ing_histxtNumHis').attr("readonly", true);
		$('#ing_nintxtNumIng').attr("readonly", true);



		//Si se registró muestro se imprime el sticker
		if (historia != '' && mensaje != "No se actualizo porque no se registraron cambios") {
			var edad = calcular_edad_detalle($("#pac_fnatxtFecNac").val());


			var wtip = 0;

			if (edad.age == 0 && edad.month <= 6) {
				wtip = 2;
			}
			else if (edad.age <= 12) {
				wtip = 1;
			}

			if (mensaje != "Se actualizo correctamente") {
				try {

					imprimirHistoria = $("#chk_imprimirHistoria").is(":checked");
					wbasedatoImp = $("#wbasedatoImp").val();
					if (imprimirHistoria) {
						winSticker = window.open("../../ips/reportes/r001-admision.php?wpachi=" + historia + "&wingni=" + ingreso + "&empresa=" + wbasedatoImp);

					}

					if ($("#imprimirSticker").val() == "on") {
						wemp_pmla = $('#wemp_pmla').val();
						winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wemp_pmla=" + wemp_pmla + "&wtip=" + wtip + "&whis=" + historia, '', 'fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');
						winSticker.onload = function () {
							$("input:radio[value=" + wtip + "]", winSticker.document).attr("checked", true);
						}
					}
					//}

					//Checkeo el radio button correspondiente de la ventana emergente
				} catch (err) {
					alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
				}
			}

			hayCambios = false;
		}

		alerta(mensaje);


		if (mensaje == "Se actualizo correctamente") {
			hayCambios = false;
			mostrarPreadmision();
		}
		else {
			//Se Inicia el formulario

			if ($("#AgendaMedica").val() == "on") {

			}
			if ($("#soportesautomaticos").val() == 'on') {

			}
			else {
				location.reload();
			}

		}

		hayCambios = false;


		if ($("#AgendaMedica").val() == "on") {
			$("#divPlanyFlujo").dialog('close');
			window.close();
			// return;
		}
		location.reload();
		$("#divPlanyFlujo").dialog('close');



	}, 200);


}

function enviarnivelsiguiente() {
	historia = $("#datosppaleshistoria").val();
	ingreso = $("#datosppalesingreso").val();

	$.post("admision_erp.php",
		{
			accion: "enviarnivelsiguiente",
			consultaAjax: '',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,
			whis: historia,
			wing: ingreso,
		}, function (data) {

			alert("los soportes fueron enviados al siguiente nivel");
			onloadDesdeSoportes();

		});

}

function mostrarDatos(documento, ingreso, tipoDocumento) {

	if (documento) {
		iniciar();
		// $( "#radAdmision" ).attr( "checked", true );
		// $( "#radAdmision" ).click();
		mostarOcultarDatosPreadmisiones(false);
		$("#pac_doctxtNumDoc").val(documento);
		$("#pac_tdoselTipoDoc").val(tipoDocumento);
	}

	if (ingreso == undefined || ingreso == "")
		ingreso = $("#ing_nintxtNumIng").val();

	if ($("input[name='btRegistrarActualizar']").attr("actualiza") == "on") {
		$("input[name='btRegistrarActualizar']").attr("disabled", false);
	} else {
		$("input[name='btRegistrarActualizar']").attr("disabled", true);
	}
	$("input[name='btRegistrarActualizar']").val("Actualizar");

	$("#radAdmision").attr("checked", true);
	$("#radAdmision").click();

	//Variable para saber si esta en modo consulta o no
	modoConsulta = true;

	var objJson = cearUrlPorCamposJson($("#div_admisiones"), 'id');

	objJson.accion = "mostrarDatosAlmacenados";	//agrego un parametro más
	objJson.wbasedato = $("#wbasedato").val();
	objJson.consultaAjax = "";
	objJson.historia = $("#ing_histxtNumHis").val();
	objJson.ingreso = ingreso;
	objJson.documento = $.trim($("#pac_doctxtNumDoc").val());
	objJson.wemp_pmla = $("#wemp_pmla").val();
	objJson.mostrarTipoServicio = $("#mostrarTipoServicio").val();

	/*validacion de todos los input para saber si tienen el mesaje de error
	y si lo tiene se envia vacio*/
	$('input').each(function (n) {
		var id = this.id;
		var valor = $("#" + id).val();
		// var valormsgerror = $("#"+id).attr( "msgerror" );
		if (this.aqAttr)	//Solo si su valor es igual a la marca de agua, ya se mensaje de error(msgerror) o no
		{
			var valormsgerror = $("#" + id).attr(this.aqAttr);	//Se Busca la marca de agua

			if (valor == valormsgerror) {
				objJson[id] = '';
			}
		}
		// No enviar dato en fecha de expedición.
		if (id.trim().toUpperCase() == "PAC_FEDTXTFECEXPDOC")
			objJson[id] = '';
	});

	//Si el documento esta vació mando el numero de documento vacio
	if ($.trim($("#pac_doctxtNumDoc").val()) == '' || $.trim($("#pac_doctxtNumDoc").val()) == $("#pac_doctxtNumDoc").attr("msgerror")) {
		objJson.documento = objJson.pac_doctxtNumDoc;
	}

	$.blockUI({ message: "Por favor espere..." });

	$.post("admision_erp.php",
		objJson,
		function (data) {
			$.unblockUI();
			if (isJSON(data) == false) {
				alerta("RESPUESTA NO ESPERADA\n" + data);
				return;
			}
			data = $.parseJSON(data);

			if (data.error == 1) {
				alerta(data.mensaje);
			}
			else {
				if (data.mensaje != '')
					alerta(data.mensaje);


				if (data.infoing) {
					informacionIngresos = data;
					informacionIngresos.regTotal = data.infoing.length;
					informacionIngresos.posAct = data.infoing.length - 1;

					if (informacionIngresos.regTotal > 0) {
						$("#bot_navegacion").css("display", "");
						$("#bot_navegacion1").css("display", "");
					}
					else {
						$("#bot_navegacion").css("display", "none");
						$("#bot_navegacion1").css("display", "none");
					}

					navegacionIngresos(0);

					//Despues de que consulte se borra el log
					borrarLog($("#key"));
					//se colocan los campos cedula,historia,ingreso read only para que no se puedan modificar
					$("#pac_doctxtNumDoc").attr("readonly", true);
					$("#ing_histxtNumHis").attr("readonly", true);
					$("#ing_nintxtNumIng").attr("readonly", true);

					$("[name=ing_cemtxtCodAse]").each(function () { //Quitar el color amarillo cuando ya tiene valor
						if ($(this).val() != "") {
							resetAqua($(this).parent());
						}
					});

					//Agosto 23 de 2013
					$("#ing_seisel_serv_ing").change();
					if ($("#pac_dle").val() != "") {
						$(".tr_pacienteExtranjero").show();
					}
					validacionServicioIngreso("cargaDatosAlmacenados");
					hayCambios = false;
				}
				$("#pac_tafselTipAfi > option[value!='']").remove();
				option = $("#pac_tafselTipAfi_original > option[value='" + data.infopac.pac_taf + "']").clone();
				$(option).attr("selected", "selected");
				$("#pac_tafselTipAfi").append(option);
				$("#pac_tafselTipAfi").removeAttr('campoRequerido');
			}
		}
	);

}

var informacionIngresos = '';

function navegacionIngresos(incremento) {

	var data = informacionIngresos;

	if (data.posAct + incremento < informacionIngresos.regTotal && data.posAct + incremento >= 0) {
		data.posAct = data.posAct + incremento;

		$("#tabla_eps").find("tr[id$='_tr_tabla_eps']").remove();
		var wbasedato = $("#wbasedato").val();
		var wemp_pmla = $("#wemp_pmla").val();
		addFila('tabla_eps', wbasedato, wemp_pmla);

		// setDatos( data.infopac, $( "#div_admisiones" ), 'id' )  ;
		setDatos(data.infoing[data.posAct], $("#div_admisiones"), 'id');
		setDatos(data.infoing[data.posAct], $("#accidentesTransito"), 'name');
		setDatos(data.infoing[data.posAct], $("#eventosCatastroficos"), 'name');
		/* se llena el div_contenedor con la lista de eventos que se trae de mostrarDatosAlmacenados que esta en el array
			data.infoing en el resultado actual y la posicion htmlEventos*/
		$("#div_contenedor").html(data.infoing[data.posAct]['htmlEventos']);

		calcular_edad(data.infoing[data.posAct].pac_fna);
		validarPacienteRem();


		//Muestra datos para el navegador inferior
		$("#spTotalReg").html(data.numRegistrosPac);// numero de registros encontrados en la busqueda
		$("#spTotalIng").html(data.numRegistrosIng[data.infoing[data.posAct].pac_his]); //total ingresos encontrados
		$("#spRegAct").html(data.numPosicionHistorias[data.infoing[data.posAct].pac_his] + 1); //resultado actual

		$("#spHisAct").html(data.infoing[data.posAct].pac_his); //historia del registro actual
		$("#spIngAct").html(data.infoing[data.posAct].ing_nin);	//ingreso actual del registro actual
		$("#spTotalIng1").html(data.numRegistrosIng[data.infoing[data.posAct].pac_his]); //total ingresos por historia

		//Muestra datos para el navegador superior
		$("#spTotalReg1").html(data.numRegistrosPac);// numero de registros encontrados en la busqueda
		$("#spTotalIng1").html(data.numRegistrosIng[data.infoing[data.posAct].pac_his]); //total ingresos encontrados
		$("#spRegAct1").html(data.numPosicionHistorias[data.infoing[data.posAct].pac_his] + 1); //resultado actual

		$("#spHisAct1").html(data.infoing[data.posAct].pac_his); //historia del registro actual
		$("#spIngAct1").html(data.infoing[data.posAct].ing_nin);	//ingreso actual del registro actual
		$("#spTotalIng11").html(data.numRegistrosIng[data.infoing[data.posAct].pac_his]); //total ingresos por historia

		$("#spEstPac").removeClass("estadoInactivo estadoActivo");//se le quita antes la clase que tiene para colocarle la nueva
		var estPac = data.infoing[data.posAct].pac_act;//se trae el estado del paciente
		if (estPac == 'off') {
			estPac = "INACTIVO";
			$("#spEstPac").addClass("estadoInactivo");
		}
		else {
			estPac = "ACTIVO";
			$("#spEstPac").addClass("estadoActivo");
		}
		$("#spEstPac").html(estPac); //se envia Estado del paciente

		if ($("#ing_caiselOriAte").val() == '02') {
			$("#div_accidente_evento").css({ display: "" });
			$("td", $("#div_accidente_evento")).eq(0).css({ display: "" });	//Mostrar accidentes de transito
			$("td", $("#div_accidente_evento")).eq(1).css({ display: "none" });	//oculto el boton de eventos catastróficos
			$("td", $("#div_accidente_evento")).eq(2).css({ display: "none" }); //oculto el boton de listar eventos catastróficos

			var objJson = cearUrlPorCamposJson($("#infDatosAcc"));
			var contenedor = $("#accidentesTransito")[0];


			if (data.infoing[data.posAct].dat_Accrei) {
				$("#accidente_previo").val(data.infoing[data.posAct].dat_Accrei);
			}

			contenedor.lastInfo = objJson;

			var objetoRes = $("[name=dat_Accase_ux_accase]").eq(0);
			validarEstadoAseguramiento(objetoRes[0]);
		}
		else if ($("#ing_caiselOriAte").val() == '06') {

			//se crea una variable global para saber si esta consultando eventos
			consultaEvento = true;
			$("#div_accidente_evento").css({ display: "" });
			$("td", $("#div_accidente_evento")).eq(0).css({ display: "none" });	//oculto el boton de accidentes de transito
			$("td", $("#div_accidente_evento")).eq(1).css({ display: "" });	//mostrar el boton de eventos catastróficos
			$("td", $("#div_accidente_evento")).eq(2).css({ display: "" }); //se muestra el tercer boton

			var objJson = cearUrlPorCamposJson($("#eventosCatastroficos"));
			var contenedor = $("#eventosCatastroficos")[0];

			contenedor.lastInfo = objJson;
		}
		else {
			$("#div_accidente_evento").css({ display: "none" });
		}
		$("#codCausaInicial").val($("#ing_caiselOriAte").val());
		//para que cuando sea empresa muestre habilitados los datos de autorizacion
		//---validarTipoResp('');

		resetAqua();
		//se simula el onblur para quitar el requerido cuando el que depende esta lleno
		$("[depend]").blur();


		/** para mostrar responsables**/
		if (data.infoing[data.posAct]['responsables'] === undefined) {

		}
		else {  //trae datos de los responsables

			for (var i = 0; i < data.infoing[data.posAct]['responsables'].length - 1; i++) {
				addFila('tabla_eps', $("#wbasedato").val(), $("#wemp_pmla").val());
				//addResponsable();
			}

			for (var i = 0; i < data.infoing[data.posAct]['responsables'].length; i++) {
				var responsables = data.infoing[data.posAct]['responsables'][i];
				var fila = $("#tabla_eps")[0].rows[1 + i];
				setDatos(responsables, fila, 'name');

				llenarPlan($("#ing_cemhidCodAse" + (i * 1 + 1) + "_tr_tabla_eps").val(), 'ing_plaselPlan' + (i * 1 + 1) + "_tr_tabla_eps");
				$("#ing_plaselPlan" + (i * 1 + 1) + "_tr_tabla_eps").val(responsables.ing_plaselPlan).removeClass("campoRequerido");


				//Construir la cantidad de cups necesarios y llevarles el valor
				if (responsables.cups != undefined) {
					//console.log( "Con cups "+ responsables.ing_cemtxtCodAse  );
					var idUltTr = (i + 1) + "_tr_tabla_eps";
					for (var ii = 0; ii < responsables.cups.length; ii++) {
						//Se crea el input text (nombre del cup) y el input hide (codigo del cup) dentro del tr
						if (ii != 0)
							agregarCUPS(idUltTr);
						var lastTr = $("#" + idUltTr);
						lastTr.find("input[name=ing_cactxtcups]").eq(ii).val(responsables.cups[ii].codigo + "-" + responsables.cups[ii].nombre); //nombre del cup
						lastTr.find("input[name=ing_cachidcups]").eq(ii).val(responsables.cups[ii].codigo); //codigo del cup
						lastTr.find("input[name=id_idcups]").eq(ii).val(responsables.cups[ii].id); //codigo del cup
					}
				} else {
					//console.log( "sin cups "+ responsables.ing_cemtxtCodAse  );
				}

				var objetoRes = $("[name=ing_tpaselTipRes]").eq(i);
				if (objetoRes != undefined) validarTipoResp(objetoRes[0]);
			}
			resetAqua($("#tabla_eps"));
			reOrdenarResponsables();
			$("#tabla_eps > tbody").find("input[type='radio'][name='res_comtxtNumcon']:checked").attr("estadoAnterior", "on");
			$("#tabla_eps > tbody").find("input[type='radio'][name='res_comtxtNumcon']").each(function () {

			})
		}
		/**Fin datos responsables**/

		/** para mostrar topes**/
		if (data.infoing[data.posAct]['topes'] === undefined) {

		}
		else {  //trae datos de los topes
			var topes = "";
			var fila = "";

			for (var k = 0; k < data.infoing[data.posAct]['responsables'].length; k++) {
				var agregarFila = false;
				for (var i = 0, j = 0; i < data.infoing[data.posAct]['topes'].length; i++) {
					//codigo responsable
					var resp = data.infoing[data.posAct]['topes'][i]['top_reshidTopRes'];
					//para traer todo el objeto que contiene ese codigo de responsable
					var objResp = $("#tabla_eps input[value='" + resp + "']");
					//extraer el id
					var idResp = objResp[0].id;

					if (resp == data.infoing[data.posAct]['responsables'][k]['ing_cemhidCodAse']) {
						if (j == 0) {
							//mostrarDivTopes1(idResp);
						}


						if (j > 0) {
							//console.log( "está entrando a agregar fila para tope" );
							if (data.infoing[data.posAct]['topes'][i]['total'] == 'off' && agregarFila == true) {
								idCodResp = idResp;
								addFila('tabla_topes', wbasedato, wemp_pmla.value);
							}
						}

						if (data.infoing[data.posAct]['topes'][i]['total'] == 'off') {
							/*//console.log("TOPES TOTAL OFF "+idResp);
							topes = data.infoing[ data.posAct ]['topes'][i];
							//fila = $( "#tabla_topes", $("#div_cont_tabla_topes"+idResp ) )[0].rows[2+j];
							//Fila siempre es la ultima
							var tam = $( "#tabla_topes", $("#div_cont_tabla_topes"+idResp ) )[0].rows.length;
							fila = $( "#tabla_topes", $("#div_cont_tabla_topes"+idResp ) )[0].rows[tam-1];
							setDatos( topes, fila, 'name' ) ;
							agregarFila = true;*/
						}
						else {
							//console.log("TOPES TOTAL ON "+idResp);
							/*Para mostrar los datos de total de tope y reconocido*/
							//topes = data.infoing[ data.posAct ]['topes'][i];
							//fila = $( "#_tr_tabla_topes" ,$("#div_cont_tabla_topes"+idResp ));
							//setDatos( topes, fila, 'name' ) ;
							/*Fin Para mostrar los datos de total de tope y reconocido*/
						}
						j++;
					}
					//REVIZAR REVIZAR
					// resetAqua( $("#tabla_topes") );
					$("#div_cont_tabla_topes" + idResp).find("[name='top_rectxtValRec']").on({
						keyup: function () {
							$(this).val($(this).val().replace(/[^0-9]/, ""));

							if ($(this).val().length > 3) {
								$(this).val($(this).val().substring(0, $(this).val().length - 1));
							} else if ($(this).val().length == 3) {
								if ($(this).val() * 1 > 100) {
									$(this).val('100');
								}
							}
						}
					});

					$("#div_cont_tabla_topes" + idResp).find("[name='top_toptxtValTop']").on({
						blur: function () {
							$(this).val($(this).val().replace(/[^0-9]/, ""));

							if ($(this).val() * 1 <= 100) {
								alert("El valor ingresado debe ser mayor a 100 puesto que no se refiere a un porcentaje")
								$(this).val("");
							}
						}
					});

				} //for externo
			}
		}
		/**Fin datos topes**/

		$("[name=ing_plaselPlan]").each(function () {
			if ($(this).val() != "") {
				$(this).removeClass("campoRequerido");
			}
		});

	}
}

//validacion de la cedula del responsable
function validarCamposCedAco() {
	var cedula1 = $.trim($('#pac_doctxtNumDoc').val());
	var cedula2 = $('#pac_ceatxtCelAco').val();

	if (cedula1 == cedula2) {
		llenadoAutomatico = true;
		var n1 = $('#pac_no1txtPriNom').val();
		var n2 = $('#pac_no2txtSegNom').val();
		var a1 = $('#pac_ap1txtPriApe').val();
		var a2 = $('#pac_ap2txtSegApe').val();

		$('#pac_ceatxtCelAco').val($.trim($('#pac_doctxtNumDoc').val())); //cedula
		$('#pac_noatxtNomAco').val(n1 + " " + n2 + " " + a1 + " " + a2); //nombre
		//$('#pac_prutxtParRes').val('Ninguno'); //parentesco
		$('#pac_diatxtDirAco').val($('#pac_dirtxtDirRes').val()); //direccion

		$('#pac_mretxtMunResp').val($('#pac_muhtxtMunRes').val());//municipio de residencia
		$('#pac_mrehidMunResp').val($('#pac_muhhidMunRes').val());//hidden municipio residencia

		$('#pac_teatxtTelAco').val($('#pac_teltxtTelFij').val()); //telefono


		resetAqua($("#div_int_datos_acompañante"));
		//Simular onblur para todos los campos con atributo depend en el div de datos del responsable
		$("[depend]", $("#div_int_datos_acompañante")).blur();
		llenadoAutomatico = false;
		llenarDatosLog(true);
	}
}

function validarCamposCedRes() {

	var cedula1 = $.trim($('#pac_doctxtNumDoc').val());
	var cedula2 = $('#pac_crutxtNumDocRes').val();

	if (cedula1 == cedula2) {
		llenadoAutomatico = true;
		var n1 = $('#pac_no1txtPriNom').val();
		var n2 = $('#pac_no2txtSegNom').val();
		var a1 = $('#pac_ap1txtPriApe').val();
		var a2 = $('#pac_ap2txtSegApe').val();

		$('#pac_tdaselTipoDocRes').val($('#pac_tdoselTipoDoc').val()); //tipo documento
		$('#pac_crutxtNumDocRes').val($.trim($('#pac_doctxtNumDoc').val())); //cedula
		$('#pac_nrutxtNomRes').val(n1 + " " + n2 + " " + a1 + " " + a2); //nombre
		$('#pac_prutxtParRes').val('Ninguno'); //parentesco
		$('#pac_drutxtDirRes').val($('#pac_dirtxtDirRes').val()); //direccion
		$('#pac_ddrtxtDetDirRes').val($('#pac_dedtxtDetDir').val()); //detalle de la direcion
		$('#pac_dretxtDepResp').val($('#pac_dehtxtDepRes').val());//departamento residencia
		$('#pac_drehidDepResp').val($('#pac_dehhidDepRes').val()); //hidden departamento residencia

		$('#pac_mretxtMunResp').val($('#pac_muhtxtMunRes').val());//municipio de residencia
		$('#pac_mrehidMunResp').val($('#pac_muhhidMunRes').val());//hidden municipio residencia

		$('#pac_trutxtTelRes').val($('#pac_teltxtTelFij').val()); //telefono
		$('#pac_mortxtNumResp').val($('#pac_movtxtTelMov').val());//movil
		$('#pac_cretxtCorResp').val($('#pac_cortxtCorEle').val()); //correo


		resetAqua($("#div_int_datos_responsable"));
		//Simular onblur para todos los campos con atributo depend en el div de datos del responsable
		$("[depend]", $("#div_int_datos_responsable")).blur();
		llenadoAutomatico = false;
		llenarDatosLog(true);
	}
	else //se realiza la busqueda de la cedula como responsable del usuario o como paciente
	{
		$.post("admision_erp.php",
			{
				accion: 'consultarResponsable',
				consultaAjax: '',
				cedula2: cedula2,
				wbasedato: $('#wbasedato').val(),
				wemp_pmla: $('#wemp_pmla').val(),
				tema: $('#tema').val,
			},
			function (data) {
				if (data.error == 1) {
					alerta(data.mensaje);
				}
				else {
					if (data.doc != '') {

						//$('#pac_tdaselTipoDocRes').val(data.tdoc);
						$('#pac_tdaselTipoDocRes>option[value=' + data.tdoc + ']').attr("selected", true);

						$('#pac_crutxtNumDocRes').val(data.doc);
						$('#pac_nrutxtNomRes').val(data.nom);
						$('#pac_drutxtDirRes').val(data.dir);
						$('#pac_ddrtxtDetDirRes').val(data.ddir);
						$('#pac_drehidDepResp').val(data.dep);
						$('#pac_mrehidMunResp').val(data.mun);
						$('#pac_trutxtTelRes').val(data.tel);
						$('#pac_mortxtNumResp').val(data.mov);
						if (data.ema != "NULL") $('#pac_cretxtCorResp').val(data.ema);
						$('#pac_prutxtParRes').val(data.pare);

						$('#pac_dretxtDepResp').val(data.ndep);
						$('#pac_mretxtMunResp').val(data.nmun);

						resetAqua($("#div_int_datos_responsable"));
						//Simular onblur para todos los campos con atributo depend en el div de datos del responsable
						$("[depend]", $("#div_int_datos_responsable")).blur();
					}

				}



			},
			"json"
		);
	}
}

function validacionServicioIngreso(origen = "") {
	//parte de configuracion
	var servicio = $('#ing_seisel_serv_ing').val();
	var mostrarTipoServicio = $('#mostrarTipoServicio').val();
	var tipoAtencionSelected = $('#pac_tamselClausu > option:selected').val();
	//|| ( mostrarTipoServicio == "on" && origen != "" && (tipoAtencionSelected != "" && tipoAtencionSelected != undefined ) )
	if ((servicio == '5' || (mostrarTipoServicio == "on" && origen == "") || (mostrarTipoServicio == "on" && origen != "" && (tipoAtencionSelected != "" && tipoAtencionSelected != undefined)))) {
		if (mostrarTipoServicio == "on" && origen == "cargaDatosAlmacenados" && (tipoAtencionSelected != "" && tipoAtencionSelected != undefined)) {
			entro = true;
		}
		if (mostrarTipoServicio == "on" && origen == "") {
			entro = false;
			$.ajax({
				url: 'admision_erp.php',
				type: "POST",
				data: {
					consultaAjax: "on",
					wemp_pmla: $("#wemp_pmla").val(),
					accion: "consultarTipoServicio",
					wbasedato: $('#wbasedato').val(),
					codServicio: servicio,
					tipoAtencionSelected: $("#pac_tamselClausu > option:selected").val()
				},
				success: function (data) {
					if (data.error * 1 == 3) {
						entro = false;
						$("#td_tipoServicioTitulo").css("display", "none");
						$("#td_tipoServicioSelect").css("display", "none");
						$("#pac_tamselClausu").css("display", "none");
					} else {
						entro = true;
						$("#pac_tamselClausu>td").remove();
					}
					$("#pac_tamselClausu").html(data.html);
				},
				async: false,
				dataType: "json"
			});
		}
		if ((mostrarTipoServicio == "on" && entro) || servicio == '5') {
			$("#td_tipoServicioTitulo").css("display", "");
			$("#td_tipoServicioSelect").css("display", "");
			$("#pac_tamselClausu").css("display", "");
		} else {
			$("#td_tipoServicioTitulo").css("display", "none");
			$("#td_tipoServicioSelect").css("display", "none");
			$("#pac_tamselClausu").css("display", "none");
		}

	}
	else {
		$("#td_tipoServicioTitulo").css("display", "none");
		$("#td_tipoServicioSelect").css("display", "none");
		$("#pac_tamselClausu").css("display", "none");
	}

}

function realizarIngreso() {
	var llenarIng = $("input[name=realizarIng]:checked").val();
	if (llenarIng == 'S') {
		$('#div_datos_responsable,#div_datos_Pag_Aut,#div_otros_datos_ingreso').slideDown('fast');
	}
	else {
		$("#div_datos_responsable,#div_datos_Pag_Aut,#div_otros_datos_ingreso").slideUp('fast');
	}
}


objsCadenas = '';

//llenar datos constantemente por se se presenta un fallo de energia
function llenarDatosLog(obligar) {

	if (obligar == undefined) {
		if (llenarTablaLog == false)
			return;

		//para que no llene el log si se cargan datos del formulario automaticamente
		if (llenadoAutomatico == true) {
			return;
		}
		//Solo llena el log cada que llene 5 campos
		if (conteoInputs < 4) {
			conteoInputs++;
			return;
		}
	}
	conteoInputs = 0;

	if (modoConsulta == false) {
		var objJson = cearUrlPorCamposJson($("#div_admisiones"), 'id');
		objJson = cearUrlPorCamposJson($("#accidentesTransito"), 'name', objJson);
		objJson = cearUrlPorCamposJson($("#eventosCatastroficos"), 'name', objJson);
		// var contenedor = $( "#div_admisiones" )[0];

		// contenedor.infoDigitada = objJson;

		objJson.ing_plaselPlan = {};
		//para el campo de planes creo todos los options
		$("option", $("#ing_plaselPlan")).each(function () {

			if (!objJson.ing_plaselPlan.options) {
				objJson.ing_plaselPlan.options = new Array();
			}

			var index = objJson.ing_plaselPlan.options.length;
			objJson.ing_plaselPlan.options[index] = {};

			objJson.ing_plaselPlan.options[index].des = $(this).html();

			if ($(this).attr("value") != 'Seleccione...') {
				objJson.ing_plaselPlan.options[index].val = $(this).attr("value");
			}
			else {
				objJson.ing_plaselPlan.options[index].val = "";
			}

		});

		objJson.ing_plaselPlan.value = $("#ing_plaselPlan").val();

		// var cadena =JSON.stringify(objJson); //para pasar json a string NO SIRVIO EN IE
		var cadena = $.toJSON(objJson); //para pasar json a string
		// JSON.parse(string); //para pasar de string a json
		// alert(cadena);

		if (objsCadenas != cadena) {

			$.post("admision_erp.php",
				{
					accion: 'llenarTablaDatosLog',
					consultaAjax: '',
					cadena: cadena,
					wbasedato: $('#wbasedato').val(),
					wemp_pmla: $('#wemp_pmla').val(),
					tema: $('#tema').val,
				},
				function (data) {
					if (data.error == 1) {
						alerta(data.mensaje);
					}
					else {
						// alert(data.mensaje);
						objsCadenas = cadena;
					}

				},
				"json"
			);
		}
	}
}

function verificarLogAdmision() {
	//para traer la informacion si tiene guardado un log

	$.post("admision_erp.php",
		{
			accion: 'traerTablaDatosLog',
			consultaAjax: '',
			key: $('#key').val(),
			wbasedato: $('#wbasedato').val(),
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val(),
		},
		function (data) {
			if (data.error == 1) {
				alerta(data.mensaje);
			}
			else {
				if (data.html != '') {
					confirmacion = 'Tiene una admision guardada parcialmente, desea recuperarla?';
					if (confirm(confirmacion)) {
						verificandoLog = true;
						// $.blockUI({message: "Cargando datos..." });
						// var objSon=JSON.parse(data.html); //para pasar de string a json
						eval("var objSon=" + data.html);
						setDatos(objSon, $("#div_admisiones"), 'id');
						setDatos(objSon, $("#accidentesTransito"), 'name');
						setDatos(objSon, $("#eventosCatastroficos"), 'name');

						if ($("#ing_caiselOriAte").val() == '02') {
							$("#div_accidente_evento").css({ display: "" });
							$("td", $("#div_accidente_evento")).eq(1).css({ display: "none" });	//oculto el boton de eventos catastróficos
							$("td", $("#div_accidente_evento")).eq(0).css({ display: "" });	//oculto el boton de eventos catastróficos

							var objJson = cearUrlPorCamposJson($("#infDatosAcc"));
							var contenedor = $("#accidentesTransito")[0];

							contenedor.lastInfo = objJson;
						}
						else if ($("#ing_caiselOriAte").val() == '06') {
							$("#div_accidente_evento").css({ display: "" });
							$("td", $("#div_accidente_evento")).eq(0).css({ display: "none" });	//oculto el boton de eventos catastróficos
							$("td", $("#div_accidente_evento")).eq(1).css({ display: "" });	//oculto el boton de eventos catastróficos

							var objJson = cearUrlPorCamposJson($("#eventosCatastroficos"));
							var contenedor = $("#eventosCatastroficos")[0];

							contenedor.lastInfo = objJson;
						}
						else {
							$("#div_accidente_evento").css({ display: "none" });
						}
						//para que se ejecuten los blur de los elementos del formulario porque cuando se trae el log los vacios y no son requeridos los deja amarillos
						$("textarea,select,input").each(function () {
							if (true || $(this).attr("depend") == '') {
								$(this).blur();
							}
						});

						//para que cuando sea empresa muestre habilitados los datos de autorizacion
						//2014-05-06 validarTipoResp('');
						var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
						//console.log("es aqui "+objetoRes[0].value);
						validarTipoResp(objetoRes[0]);

						// $("#ing_poltxtNumPol").removeClass("campoRequerido"); //si se descomentan agregarlos donde se adiciona la clase
						// $("#ing_ncotxtNumCon").removeClass("campoRequerido"); //si se descomentan agregarlos donde se adiciona la clase

						//validar si el paciente es remitido
						validarPacienteRem();



						borrarLog(key);
						resetAqua($("#div_admisiones,#accidentesTransito,#eventosCatastroficos"));

						// mostrarAdmision();
						// $( "#radAdmision" ).attr( "checked", true );
						// opcionAdmisionPreadmision();

						if ($("[name=radPreAdmi]:checked").val().toUpperCase() == "ADMISION") {
							// resetear();
							$("#radAdmision").attr("checked", true);
							opcionAdmisionPreadmision();

							if ($("input[name='btRegistrarActualizar']").attr("graba") == "on") {
								$("input[name='btRegistrarActualizar']").attr("disabled", false);
							} else {
								$("input[name='btRegistrarActualizar']").attr("disabled", true);
							}

							$("input[name='btRegistrarActualizar']").val("Admitir");
						}
						else {
							$("#radPreadmision").attr("checked", true);
							mostarOcultarDatosPreadmisiones(false);
							$("#txtAdmisionPreadmision").html("PREADMISION");

							if ($("input[name='btRegistrarActualizar']").attr("graba") == "on") {
								$("input[name='btRegistrarActualizar']").removeAttr("disabled");
							} else {
								$("input[name='btRegistrarActualizar']").attr("disabled", true);
							}

							$("input[name='btRegistrarActualizar']").val("Preadmitir");
						}

						// $.unblockUI();
					}
					else {
						borrarLog(key);
					}
				}
			}

			verificandoLog = false;
		},
		"json"
	);
}

function borrarLog(key) {
	$.ajax(
		{
			url: "admision_erp.php",
			context: document.body,
			type: "POST",
			data:
			{
				accion: 'borrarTablaDatosLog',
				consultaAjax: '',
				key: $('#key').val(),
				wbasedato: $('#wbasedato').val(),
				wemp_pmla: $('#wemp_pmla').val(),
				tema: $('#tema').val,
			},
			async: false,
			dataType: "json",
			success: function (data) {
				if (data.error == 1) {
					alerta(data.mensaje);
				}
				else {
					// $("#"+selectHijo).html(data.html); // update Ok.
					//$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				}
			}
		});
}

function buscarIpsRemite() {
	var wbasedato = $("#wbasedato").val();
	//Asigno autocompletar para la busqueda de ips que remite
	$("#pac_iretxtIpsRem").autocomplete("admision_erp.php?consultaAjax=&accion=consultarIpsRemite&wbasedato=" + wbasedato,
		{
			cacheLength: 1,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 3,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);

			}
		).on({
			change: function () {


				var cmp = this;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite una IPS v\u00E1lida")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);

				//Pregunto si la pareja es diferente
				// if( ( this._lastValue && this._lastValue != this.value && this._lastCodigo ) || ( this._lastCodigo != $( "input[type=hidden]", this.parentNode ).val() ) ){
				// alert( "Digite una ips que remite valida" )
				// $( "input[type=hidden]", this.parentNode ).val( '' );
				// this.value = '';
				// }


				// if( this._lastValue ){
				// this.value = this._lastValue;
				// }
				// else{
				// this.value = "";
				// }
			}
		});
}

function opcionAdmisionPreadmision() {
	$("[name=radPreAdmi]:checked").each(function (x) {
		if (this.value == 'admision') {
			$("#pac_fectxtFechaPosibleIngreso").attr("disabled", true);
			$("#pac_fectxtFechaPosibleIngreso").removeClass("campoRequerido");
			$("#txtAdmisionPreadmision").html("ADMISION");

			mostarOcultarDatosPreadmisiones(false);

			//Agrego los elementos al select ORIGEN DE LA ATENCION
			if (optOrigenAtencion && optOrigenAtencion != '') {
				var auxClone = optOrigenAtencion.clone();

				if ($("option[value=02]", $("#ing_caiselOriAte")).length == 0) {
					var aux = document.createElement("option");

					var a = document.getElementById("ing_caiselOriAte");
					a.options.add(aux, 2);

					aux.innerHTML = auxClone[0].innerHTML;
					aux.value = auxClone[0].value;
				}

				if ($("option[value=06]", $("#ing_caiselOriAte")).length == 0) {
					var aux = document.createElement("option");

					var a = document.getElementById("ing_caiselOriAte");
					a.options.add(aux, 6);

					aux.innerHTML = auxClone[1].innerHTML;
					aux.value = auxClone[1].value;
				}
			}

			if (optServicioIngreso && optServicioIngreso != '') {
				var auxClone = optServicioIngreso.clone();

				if ($("option[value^=4]", $("#ing_seisel_serv_ing")).length == 0) {
					for (var iii = 0; iii < auxClone.length; iii++) {
						var aux = document.createElement("option");
						var a = document.getElementById("ing_seisel_serv_ing");
						a.options.add(aux, 3);
						aux.innerHTML = auxClone[iii].innerHTML;
						aux.value = auxClone[iii].value;
					}
				}
			}
			$("[padm]").css({ display: "none" });
		}
		else {
			$("#pac_fectxtFechaPosibleIngreso").attr("disabled", false);
			$("#txtAdmisionPreadmision").html("PREADMISION");

			if ($("#pac_fectxtFechaPosibleIngreso").val() == '' || $("#pac_fectxtFechaPosibleIngreso").val() == $("#pac_fectxtFechaPosibleIngreso").attr("msgerror")) {
				$("#pac_fectxtFechaPosibleIngreso").addClass("campoRequerido");
			}

			mostarOcultarDatosPreadmisiones(true);

			$("option[value^=4]", $("#ing_seisel_serv_ing")).remove();

			//Si no hay nada en agenda de preadmision traigo los datos del día en la agenda de preadmisión
			if ($("#dvAgendaPreadmision").html() == '') {
				consultarAgendaPreadmision($("#fechaAct").val(), 0);
			}

			$("[padm]").css({ display: "" });
		}
	});
}

/****************************************************************************************************
 * Agosto 20 de 2013
 *
 * Oculta o muestra los datos necesarios al dar click sobre admision o preadmisión
 * También se usa al dar click sobre el botón ingresar en la agenda
 ****************************************************************************************************/
function mostarOcultarDatosPreadmisiones(ocultar) {
	if (ocultar) {

		/************************************************************************************************
		 * Agosto 20 de 2013
		 ************************************************************************************************/

		//Muestro la agenda de preadmisiones
		// $( "#dvAgendaPreadmision" ).css( { display: "" } );
		$("#div_datosAdmiPreadmi").css({ display: "none" });

		//oculto todo los divs y solo dejo el div de agenda de preadmisiones
		$("#div_int_datos_personales,[id!=div_datosIng_Per_Aco][acordeon]", $("#div_admisiones")).css({ display: "none" });
		$("#div_ext_agendaPreadmision").css({ display: "" });
		// $( "center",$( "#datos_ingreso") ).eq(2).css( { display: "none" } );
		$("#datos_ingreso center").last().css({ display: "none" });
		$("div[name='div_botones']").css({ display: "none" });
		/************************************************************************************************/
	}
	else {
		/********************************************************************************
		 * Agsoto 20 de 2013
		 ********************************************************************************/

		//Oculto la agenda de preadmisiones
		// $( "#dvAgendaPreadmision" ).css( {display:"none"} );
		$("#div_datosAdmiPreadmi").css({ display: "" });

		//oculto todo los divs y solo dejo el div de agenda de preadmisiones
		$("#div_int_datos_personales,[id!=div_datosIng_Per_Aco][acordeon]", $("#div_admisiones")).css({ display: "" });
		$("#div_ext_agendaPreadmision").css({ display: "none" });
		// $( "center",$( "#datos_ingreso") ).eq(2).css( { display: "" } );
		$("#datos_ingreso center").last().css({ display: "" });
		$("div[name='div_botones']").css({ display: "" });
		/********************************************************************************/

		/************************************************************************************************
		 * Agosto 21 de 2013
		 ************************************************************************************************/
		if ($("#ing_caiselOriAte").val() == '02' || $("#ing_caiselOriAte").val() == '06') {
			$("#div_accidente_evento").css({ display: "" });
		}
		else {
			$("#div_accidente_evento").css({ display: "none" });
		}
		/************************************************************************************************/
	}

	hayCambios = false;
}

/************************************************************************************************
 * Agosto 20 de 2013
 ************************************************************************************************/
//Indica cual fue la último paciente que fue cargado con preadmisión
//Esto para validar que no se vuelva a preguntar si desea cargar los datos
var ultimaPreadmisionCargada = '';
/************************************************************************************************/

/************************************************************************************************
 * Agosto 15 de 2013
 ************************************************************************************************/
function mostrarDatosPreadmision(documento) {
	if (documento) {
		$("#pac_doctxtNumDoc").val(documento);
		// $( "input[name='btRegistrarActualizar']" ).val( "Actualizar" );
	}

	//No haga el llamado si el documento está vacio
	if ($.trim($("#pac_doctxtNumDoc").val()) == '')
		return;


	$.blockUI({ message: "Por favor espere..." });

	//Variable para saber si esta en modo consulta o no
	modoConsulta = true;


	var objJson = cearUrlPorCamposJson($("#div_admisiones"), 'id');

	objJson.accion = "mostrarDatosAlmacenadosPreadmision";	//agrego un parametro más
	objJson.wbasedato = $("#wbasedato").val();
	objJson.consultaAjax = "";
	objJson.historia = $("#ing_histxtNumHis").val();
	objJson.ingreso = $("#ing_nintxtNumIng").val();
	objJson.documento = $.trim($("#pac_doctxtNumDoc").val());
	objJson.wemp_pmla = $("#wemp_pmla").val();

	/*validacion de todos los input para saber si tienen el mesaje de error
	y si lo tiene se envia vacio*/
	$('input').each(function (n) {
		var id = this.id;
		var valor = $("#" + id).val();
		var valormsgerror = $("#" + id).attr("msgerror");

		if (valor == valormsgerror) {
			objJson[id] = '';
		}
	});

	$.post("admision_erp.php",
		objJson,
		function (data) {

			$.unblockUI();

			if (data.error == 1) {
				alerta(data.mensaje);
			}
			else {
				if (data.mensaje != '')
					alerta(data.mensaje);


				if (data.infoing) {
					if ($("#radPreadmision").is(":checked")) {
						if (ultimaPreadmisionCargada != data.infoing[0].pac_doc && (documento || confirm("El paciente tiene preadmisión. Desea traer los datos?"))) {
							informacionIngresos = data;
							informacionIngresos.regTotal = data.infoing.length;
							informacionIngresos.posAct = data.infoing.length - 1;

							navegacionIngresosPreadmision(0);

							$("#radPreadmision").attr("checked", true);
							$("#radPreadmision").click();

							mostarOcultarDatosPreadmisiones(false);

							//Despues de que consulte se borra el log
							borrarLog($("#key"));
							//se colocan los campos cedula,historia,ingreso read only para que no se puedan modificar
							$("#pac_doctxtNumDoc").attr("readonly", true);
							$("#ing_histxtNumHis").attr("readonly", true);
							$("#ing_nintxtNumIng").attr("readonly", true);

							ultimaPreadmisionCargada = $.trim($("#pac_doctxtNumDoc").val());
							if ($("input[name='btRegistrarActualizar']").attr("actualiza") == "on") {
								$("input[name='btRegistrarActualizar']").attr("disabled", false);
							} else {
								$("input[name='btRegistrarActualizar']").attr("disabled", true);
							}
							$("input[name='btRegistrarActualizar']").val("Actualizar");
						}
					} else {
						if (confirm("El paciente tiene preadmisión. Desea traer los datos para admitir?")) {
							informacionIngresos = data;
							informacionIngresos.regTotal = data.infoing.length;
							informacionIngresos.posAct = data.infoing.length - 1;

							delete informacionIngresos.infoing[0].pac_fec; //Para que no cambie la fecha de ingreso
							delete informacionIngresos.infoing[0].ing_fei; //Para que no cambie la fecha de ingreso
							delete informacionIngresos.infoing[0].ing_sei; //Para que no cambie el servicio de ingreso
							delete informacionIngresos.infoing[0].ing_des; //Para que no cambie el destino
							delete informacionIngresos.infoing[0].ing_hin; //Para que no cambie la hora de ingreso

							navegacionIngresosPreadmision(0);
						} else {
							mostrarDatosDemograficos();
						}
					}
				}
				else {
					mostrarDatosDemograficos();
				}
				/*if( $("#pac_trhselTipRes > option:selected").val() == "E" ){
					$(".tr_pacienteExtranjero").show();
				}*/
				modoConsulta = false;
			}
		},
		"json"
	);
}

function navegacionIngresosPreadmision(incremento) {
	var data = informacionIngresos;

	if (data.posAct + incremento < informacionIngresos.regTotal && data.posAct + incremento >= 0) {
		data.posAct = data.posAct + incremento;
		setDatos(data.infoing[data.posAct], $("#div_admisiones"), 'id');
		calcular_edad(data.infoing[data.posAct].pac_fna);
		validarPacienteRem();
		var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
		llenarPlan($("#ing_cemhidCodAse" + prefijo_trEps).val(), 'ing_plaselPlan' + prefijo_trEps);
		$("#ing_plaselPlan" + prefijo_trEps).val(data.infoing[data.posAct].ing_pla);
		$("#spEstPac").removeClass("estadoInactivo estadoActivo");//se le quita antes la clase que tiene para colocarle la nueva

		var estPac = data.infoing[data.posAct].pac_act;//se trae el estado del paciente
		$("#spEstPac").html('');
		//var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
		//validarTipoResp(objetoRes[0]);

		setDatos(data.infoing[data.posAct], $("#accidentesTransito"), 'name');
		if ($("#ing_caiselOriAte").val() == '02') {
			$("#div_accidente_evento").css({ display: "" });
			$("td", $("#div_accidente_evento")).eq(0).css({ display: "" });	//Mostrar accidentes de transito
			$("td", $("#div_accidente_evento")).eq(1).css({ display: "none" });	//oculto el boton de eventos catastróficos
			$("td", $("#div_accidente_evento")).eq(2).css({ display: "none" }); //oculto el boton de listar eventos catastróficos

			if (data.infoing[data.posAct].dat_Accrei) {
				$("#accidente_previo").val(data.infoing[data.posAct].dat_Accrei);
			}

			var objJson = cearUrlPorCamposJson($("#infDatosAcc"));
			var contenedor = $("#accidentesTransito")[0];

			contenedor.lastInfo = objJson;

			/*if( $("#ing_vretxtValRem").val() == "" )
				$("#ing_vretxtValRem").val(0);//campo obligatorio, para que deje pasar*/
		}

		/** para mostrar responsables**/
		if (data.infoing[data.posAct]['responsables'] === undefined) {

		}
		else {  //trae datos de los responsables
			for (var i = 0; i < data.infoing[data.posAct]['responsables'].length - 1; i++) {
				addFila('tabla_eps', $("#wbasedato").val(), $("#wemp_pmla").val());
			}

			for (var i = 0; i < data.infoing[data.posAct]['responsables'].length; i++) {
				var responsables = data.infoing[data.posAct]['responsables'][i];
				var fila = $("#tabla_eps")[0].rows[1 + i];
				setDatos(responsables, fila, 'name');
				llenarPlan($("#ing_cemhidCodAse" + (i * 1 + 1) + "_tr_tabla_eps").val(), 'ing_plaselPlan' + (i * 1 + 1) + "_tr_tabla_eps");
				$("#ing_plaselPlan" + (i * 1 + 1) + "_tr_tabla_eps").val(responsables.ing_plaselPlan);

				//Construir la cantidad de cups necesarios y llevarles el valor
				if (responsables.cups != undefined) {
					var idUltTr = (i + 1) + "_tr_tabla_eps";
					for (var ii = 0; ii < responsables.cups.length; ii++) {
						//Se crea el input text (nombre del cup) y el input hide (codigo del cup) dentro del tr
						if (ii != 0)
							agregarCUPS(idUltTr);
						var lastTr = $("#" + idUltTr);
						lastTr.find("input[name=ing_cactxtcups]").eq(ii).val(responsables.cups[ii].codigo + "-" + responsables.cups[ii].nombre); //nombre del cup
						lastTr.find("input[name=ing_cachidcups]").eq(ii).val(responsables.cups[ii].codigo); //codigo del cup
					}
				} else {

				}
				resetAqua($("#tabla_eps"));
				var objetoRes = $("[name=ing_tpaselTipRes]").eq(i);
				if (objetoRes != undefined) validarTipoResp(objetoRes[0]);
			}
			reOrdenarResponsables();
		}
		/**Fin datos responsables**/


		/*var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
		validarTipoResp(objetoRes[0]);*/

		/** para mostrar topes**/
		if (data.infoing[data.posAct]['topes'] === undefined) {

		}
		/*else
		{  //trae datos de los topes
			var topes="";
			var fila="";

			console.log( data );
			for ( var k=0; k<data.infoing[ data.posAct ]['responsables'].length;k++)
			{
				var agregarFila = false;
				for ( var i=0,j=0; i<data.infoing[ data.posAct ]['topes'].length;i++)
				{
					//codigo responsable
					var resp = data.infoing[ data.posAct ]['topes'][i]['top_reshidTopRes'];
					//para traer todo el objeto que contiene ese codigo de responsable
					console.log( "responsable:"+resp );
					$( "#tabla_eps input" ).each(function(){
						console.log( $(this).val() );
					});
					var objResp= $( "#tabla_eps input[value='"+resp+"']" );
					//extraer el id
					var idResp =objResp[0].id;

					if( resp == data.infoing[ data.posAct ]['responsables'][k]['ing_cemhidCodAse'] )
					{
						if(j == 0){
							//mostrarDivTopes1(idResp);
						}

						if( j > 0 ){
							if (data.infoing[ data.posAct ]['topes'][i]['total'] == 'off' && agregarFila == true)
							{
								idCodResp = idResp;
								addFila('tabla_topes',wbasedato.value,wemp_pmla.value);
							}
						}

						if (data.infoing[ data.posAct ]['topes'][i]['total'] == 'off')
						{
						}
						else
						{

						}
						j++;
					}
				} //for externo
			}
		}*/
		/**Fin datos topes**/


		resetAqua();
		//se simula el onblur para quitar el requerido cuando el que depende esta lleno
		$("[depend]").blur();


	}
}

/**************************************************************************************************
 * Agosto 16 de 2013
 *
 * Realiza el ajax para consultar los datos de preadmisión
 **************************************************************************************************/
function consultarAgendaPreadmision(fecha, incremento) {
	var objJson = {};	//Creo el objeto
	objJson.accion = "consultarPreadmision";	//agrego un parametro más
	objJson.wbasedato = $("#wbasedato").val();
	objJson.consultaAjax = "";
	objJson.wemp_pmla = $("#wemp_pmla").val();
	objJson.fecha = fecha;
	objJson.incremento = incremento;
	objJson.consulta = $("#perfil_consulta").val();

	$.blockUI({ message: "Espere un momento por favor..." });

	$.post("admision_erp.php",
		objJson,
		function (data) {
			$.unblockUI();
			if (data.error == 1) {
				alerta(data.mensaje);
			}
			else {
				if (data.mensaje != '')
					alerta(data.mensaje);

				if (data.html) {
					$("#dvAgendaPreamdisionDatos").html(data.html);

					$("#dvAgendaPreamdisionDatos [fecha]").datepicker({
						dateFormat: "yy-mm-dd",
						fontFamily: "verdana",
						dayNames: ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
						monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
						dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
						dayNamesShort: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
						monthNamesShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
						changeMonth: true,
						changeYear: true,
						yearRange: "c-100:c+100"
					});
					if ($("input[name='btRegistrarActualizar']").attr("graba") == "on") {
						$("input[name='btRegistrarActualizar']").removeAttr("disabled");

					} else {
						$("input[name='btRegistrarActualizar']").attr("disabled", true);
					}
				}
			}
		},
		"json"
	);
}

/**************************************************************************************************
 * Agosto 16 de 2013
 *
 * Cancela un registro de preadmision
 **************************************************************************************************/
function cancelarPreadmision(campo, fecha, incremento, tdo, doc) {
	if (confirm("Desea cancelar la preadmisión?")) {
		var objJson = {};	//Creo el objeto
		objJson.accion = "cancelarPreadmision";	//agrego un parametro más
		objJson.wbasedato = $("#wbasedato").val();
		objJson.consultaAjax = "";
		objJson.wemp_pmla = $("#wemp_pmla").val();
		objJson.fecha = fecha;
		objJson.incremento = incremento;
		objJson.tdo = tdo;
		objJson.doc = doc;

		$.post("admision_erp.php",
			objJson,
			function (data) {

				if (data.error == 1) {
					alerta(data.mensaje);
				}
				else {
					if (data.mensaje != '')
						alerta(data.mensaje);

					consultarAgendaPreadmision(fecha, incremento);
				}
			},
			"json"
		);
	}
	else {
		campo.checked = false;
	}
}


/************************************************************************
 * Agosto 16 de 2013
 ************************************************************************/
function ingresarPreadmision(campo, tdoc, doc) {



	$.blockUI({ message: "Por favor espere" });

	$("#pac_doctxtNumDoc").val(doc);

	//Variable para saber si esta en modo consulta o no
	modoConsulta = true;

	var objJson = cearUrlPorCamposJson($("#div_admisiones"), 'id');

	objJson.accion = "mostrarDatosAlmacenadosPreadmision";	//agrego un parametro más
	objJson.wbasedato = $("#wbasedato").val();
	objJson.consultaAjax = "";
	objJson.historia = $("#ing_histxtNumHis").val();
	objJson.ingreso = $("#ing_nintxtNumIng").val();
	objJson.documento = $.trim($("#pac_doctxtNumDoc").val());
	objJson.wemp_pmla = $("#wemp_pmla").val();

	$.post("admision_erp.php",
		objJson,
		function (data) {
			if (isJSON(data) == false) {
				alerta("RESPUESTA NO ESPERADA\n" + data);
				return;
			}
			data = $.parseJSON(data);

			if (data.error == 1) {
				alerta(data.mensaje);
			}
			else {
				if (data.mensaje != '')
					alerta(data.mensaje);

				if (data.infoing) {

					informacionIngresos = data;
					informacionIngresos.regTotal = data.infoing.length;
					informacionIngresos.posAct = data.infoing.length - 1;

					navegacionIngresosPreadmision(0);

					//Despues de que consulte se borra el log
					borrarLog($("#key"));
					//se colocan los campos cedula,historia,ingreso read only para que no se puedan modificar
					$("#pac_doctxtNumDoc").attr("readonly", true);
					$("#ing_histxtNumHis").attr("readonly", true);
					$("#ing_nintxtNumIng").attr("readonly", true);

					modoConsulta = false;

					$("#radAdmision").attr("checked", true);
					$("#pac_fectxtFechaPosibleIngreso").attr("disabled", true);
					// $( "#radAdmision" ).click();


					/*informacionIngresos == data, numRegistrosIng trae = historia(posicion) e ingreso(valor) traidos de 	)
					informacionIngresos en la posicion de la historia que se esta mostrando con su respectivo valor y cuyo valor sea igual ==
					al ingreso que se esta mostrando y && informacionIngresos en la posicion infoIng e infoIng en la posicion actual(posAct) que
					que esta dentro de informacionIngresos todo lo anterior es:(el ingreso que se esta mostrando en pantalla )	y que este on
					*/
					/* 1.Si NO está en modo consulta se guarda la admisón
					2.Si esta en modoConsulta se pregunta si es el último ingreso, de ser así entonces se actualiza la admisión
					*/
					//      1.                   2.
					//Procedo a guardar los datos
					if (true) {

						$("#ing_seisel_serv_ing").change();
						//--> se cambia la fecha y hora de la preadmision por la fecha de hoy
						$("#ing_feitxtFecIng").val($("#fechaAct").val());
						$("#ing_hintxtHorIng").val($("#horaAct").val());

						var datosLlenos = $('#forAdmisiones').valid();
						iniciarMarcaAqua($('#forAdmisiones'));
						//if( datosLlenos ) //2014-08-12, esta validacion se hizo al guardar la preadmision, en este punto sobra
						if (true) {
							var validacion = validarCampos($("#div_admisiones"));

							//if( validacion )
							if (true) {
								//A todos los campos que tengan marca de agua y esten deshabilitado, les borro la marca de agua(msgerror)
								$("[aqua]:disabled").each(function () {
									if ($(this).val() == $(this).attr(this.aqAttr)) {
										$(this).val('');
									}
								});

								//A todos los campos que tengan marca de agua no obligatorios
								$("[aqua]").each(function () {
									if ($(this).val() == $(this).attr("msgaqua")) {
										$(this).val('');
									}
								});
								var objJson = cearUrlPorCamposJson($("#div_admisiones"), 'id');
								objJson = cearUrlPorCamposJson($("#div_admisiones"), 'ux', objJson);

								objJson = cearUrlPorCamposJson($("#accidentesTransito"), 'name', objJson);//2014-07-22

								//se colocan porque en las validaciones de tarifa solo se hacen para el primer resp
								var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
								objJson.ing_tpaselTipRes = $("#ing_tpaselTipRes" + prefijo_trEps).val();
								objJson.ing_cemhidCodAse = $("#ing_cemhidCodAse" + prefijo_trEps).val();

								objJson.accion = "guardarDatos";	//agrego un parametro más
								objJson.wbasedato = $("#wbasedato").val();
								objJson.consultaAjax = "";
								objJson.historia = $("#ing_histxtNumHis").val();
								objJson.ingreso = $("#ing_nintxtNumIng").val();
								objJson.documento = $.trim($("#pac_doctxtNumDoc").val());
								objJson.tipodoc = $("#pac_tdoselTipoDoc").val();
								objJson.wemp_pmla = $("#wemp_pmla").val();
								objJson.modoConsulta = modoConsulta;

								/*Guardar los responsables*/
								var esAccTransito = $("#tabla_responsables_1_2").is(":visible");
								if ($("[name=dat_Acccas_ux_acccas]").val() != "") {
									esAccTransito = true;
								}
								var indice_res = 0;
								objJson.responsables1 = {};

								//En la primera posicion del arr de responsables siempre va el responsable de transito
								if (esAccTransito) {
									if ($("#dat_AccreshidCodRes24").val() == "") {
										alerta("No existe aseguradora SOAT, por favor verifique los datos del accidente.");
										return;
									}
									objJson.responsables1[0] = {};
									objJson.responsables1[0].res_tdo = "NIT";//2014-10-22
									objJson.responsables1[0].ing_cemhidCodAse = $("#dat_AccreshidCodRes24").val();
									objJson.responsables1[0].res_nom = $("#restxtCodRes").val();//2014-10-22
									objJson.responsables1[0].ing_tpaselTipRes = "E";
									objJson.responsables1[0].ing_plaselPlan = "00";
									if ($("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
										$("input[name='dat_Accpol_ux_accpol']").val("");
									objJson.responsables1[0].ing_poltxtNumPol = $("input[name=dat_Accpol_ux_accpol]").val();
									objJson.responsables1[0].ing_ncotxtNumCon = "";
									objJson.responsables1[0].ing_ordtxtNumAut = "";
								}

								$("tr[id$=_tr_tabla_eps]").each(function (index) {
									if (esAccTransito)
										indice_res = index + 1; //En la posicion 0 esta el responsable transito
									else
										indice_res = index;

									objJson.responsables1[indice_res] = cearUrlPorCamposJson(this, 'name');

									//2014-10-22 Si es particular, el codigo del responsable pasa a ser el numero de documento que se ingreso
									if (objJson.responsables1[indice_res].ing_tpaselTipRes == "P") {
										objJson.responsables1[indice_res].ing_cemhidCodAse = objJson.responsables1[indice_res].res_doc;
									} else {
										//Si no es particular, el tipo de documento sera NIT, y el nombre el que corresponde al campo ing_cemtxtCodAse
										objJson.responsables1[indice_res].res_tdo = "NIT";
										objJson.responsables1[indice_res].res_nom = objJson.responsables1[indice_res].ing_cemtxtCodAse;
									}

									objJson.responsables1[indice_res].cups = {};
									$(this).find("[name=ing_cachidcups]").each(function (index2) {
										if ($(this).val() != "") {
											objJson.responsables1[indice_res].cups[index2] = $(this).val();
										}
									});
									if (index == 0) {
										/*para el responsable 2 de accidente*/
										objJson.codAseR2 = $("[id^=ing_cemhidCodAse1]", this).val();
									}
									if (index == 1) {
										//para responsable 3 de accidente
										objJson.tipoEmpR3 = $("[id^=ing_tpaselTipRes]", this).val();
										//cuando sea empresa
										if ($("id^=ing_tpaselTipRes", this).val() == 'P') {
											objJson.codAseR3 = $("#pac_crutxtNumDocRes").val();
											objJson.nomAseR3 = $("#pac_nrutxtNomRes").val();
										}
										else {
											objJson.codAseR3 = $("[id^=ing_cemhidCodAse]", this).val();
											objJson.nomAseR3 = $("[id^=ing_cemtxtCodAse]", this).val();
										}
									}
								});

								//DATOS DE LOS RESPONSABLES QUE VIAJAN A UNIX
								//objJson = cearUrlPorCamposJson( $("#1_tr_tabla_eps"), 'ux', objJson );

								/*Guardar los topes por responsable*/
								objJson.topes = {};

								/*$( "tr[id$=_tr_tabla_topes]" ).each(function( index )
								{
									//

									objJson.topes[ index ] = cearUrlPorCamposJson(  this , 'name' );
								});*/

								objJson.topes = {};
								//---aqui iria nueva funcion para guardar los topes
								for (var x in objJson.responsables1) {


									$.ajax(
										{
											url: "admision_erp.php",
											context: document.body,
											type: "POST",
											data:
											{
												accion: "traertopespreadmision",
												consultaAjax: '',
												wemp_pmla: $('#wemp_pmla').val(),
												tema: $('#tema').val,
												documento: $.trim($("#pac_doctxtNumDoc").val()),
												tipodocumento: $("#pac_tdoselTipoDoc").val()
											},
											async: false,
											dataType: "json",
											success: function (data) {

												objJson.topes = data;
												objJson.topesPreadmision = true;
											}
										});

								}


								/*Fin Guardar los topes por responsable*/

								//A todos los campos que tengan marca de agua y esten deshabilitado, le pongo la marca de agua
								$("[aqua]:disabled").each(function () {
									if ($(this).val() == '') {
										$(this).val($(this).attr(this.aqAttr));
									}
								});

								/********************************************************
								 * Septiembre 19 de 2013
								 ********************************************************/
								//Busco los campos que son depends y están vacios con propiedad ux
								$("[depend][ux]").each(function () {
									if ($(this).val() == '' || ($(this).val() == $(this).attr(this.aqAttr))) {
										objJson[$(this).attr("ux")] = $("#" + $(this).attr("depend")).val();
									}
								});
								/********************************************************/

								//RESPONSABLE QUE VIAJA A UNIX
								objJson = cearUrlPorCamposJson($("tr[id$=_tr_tabla_eps]").eq(0), 'ux', objJson);
								//objJson = cearUrlPorCamposJson( $( "tr[id$=_tr_tabla_eps]" ).eq(0), 'name', objJson );

								if (esAccTransito) {
									objJson._ux_mreemp_ux_pacemp_ux_accemp = "E";
									objJson._ux_pacres_ux_mreres = $("#dat_AccreshidCodRes24").val();
									objJson._ux_mrepla = "00";
									if ($("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
										$("input[name='dat_Accpol_ux_accpol']").val("");
									objJson._ux_pacpol = $("input[name=dat_Accpol_ux_accpol]").val();
								}




								for (var iii in objJson.responsables1[0]) {
									objJson[iii] = objJson.responsables1[0][iii];
								}

								//2014-10-22 Siempre tiene que existir el primer responsable
								//2014-10-22if( objJson.responsables1[ 0 ].ing_tpaselTipRes == "E" ){
								if (objJson.responsables1[0].ing_cemhidCodAse == "") {
									alerta("NO existe primer responsable, por favor verifique.");
									return;
								}

								//2014-10-22}
								objJson.esPreAdmicion = "on";


								// console.log(objJson);

								//objJson.accion ='grabardatoprueba';
								// alert("hola");


								$.unblockUI();



								$.post("admision_erp.php",


									objJson,
									function (data) {

										if (isJSON(data) == false) {
											alerta("RESPUESTA NO ESPERADA\n" + data);
											return;
										}
										data = $.parseJSON(data);

										if (data.error == 1) {
											alerta(data.mensaje);
										}
										else {
											//Al guardar los datos se borra el log
											//borrarLog( $( "#key" ) );
											if (data.mensaje != '') {
												$("#radAdmision").attr("checked", true);
												$("#pac_fectxtFechaPosibleIngreso").attr("disabled", true);
												// $( "#pac_fectxtFechaPosibleIngreso" ).addClass( "campoRequerido" );
												$("#radAdmision").click();

												//Se oculta todos los acordeones
												//$( "[acordeon]" ).accordion( "option", "active", false );

												//Se muestra el acordeon de DATOS DE INGRESO - DATOS PERSONALES
												//$( "#div_datosIng_Per_Aco" ).accordion( "option", "active", 0 );

												try {
													window.scrollTo(0, 0);
												} catch (e) { }


												if (data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios") {

													if ($("#soportesautomaticos").val() == 'on')//preadmision
													{

														//-- se agregavalidacion de soportes digitalizados
														//-- si la validacion de digitalizacion , si ya la digilitalizacion esta encendida
														//---si ya el centro de costos esta en on para la digitalizacion
														//---si ya la empresa hace la digtalizacion   no se piden soportes
														//-- parametro de digitalizacion apagado , pido soportes(programa viejo)
														if ($("#parametroDigitalizacion").val() == 'on') {

															// si el parametro esta encendido tengo que mirar igual si la empresa y el centro de costos piden digitalizacion

															var responsable1 = objJson.responsables1[0].ing_cemhidCodAse;
															var Empresadigitalizacion;
															var todosdigitalizacion;
															var ccodigitalizacion;
															var data2;
															$.ajax(
																{
																	url: "admision_erp.php",
																	context: document.body,
																	type: "POST",
																	data:
																	{
																		accion: "empresahacedigitalizacion",
																		consultaAjax: '',
																		wemp_pmla: $('#wemp_pmla').val(),
																		tema: $('#tema').val,
																		empresa: responsable1
																	},
																	async: false,
																	success: function (data) {
																		if (data == '') {
																			Empresadigitalizacion = 'off';
																		}
																		else {
																			Empresadigitalizacion = 'on';
																			if (data == '*') {

																				todosdigitalizacion = 'si';
																			}
																			else {

																				todosdigitalizacion = 'no';
																				ccodigitalizacion = data.split(',');
																			}

																		}
																		data2 = data;

																	}
																});


															var ccoingreso = $("#ing_seisel_serv_ing").val();
															ccoingreso = ccoingreso.split('-');


															if (Empresadigitalizacion == 'on') {
																if (data2 == '*') {
																	//alert(" empresa en on y * no muestre soportes");
																	setTimeout(function () {

																		//se llenan los campos de historia,ingreso,documento despues de guardar
																		$("#ing_histxtNumHis").val(data.historia);
																		$("#ing_nintxtNumIng").val(data.ingreso);
																		$("#pac_doctxtNumDoc").val(data.documento);
																		//se ponen documento,historia,ingreso readonly
																		$('#pac_doctxtNumDoc').attr("readonly", true);
																		$('#ing_histxtNumHis').attr("readonly", true);
																		$('#ing_nintxtNumIng').attr("readonly", true);

																		if (data.error == 4) {
																			alerta("Debe egresar y volver a ingresar");
																		}

																		alerta(data.mensaje + ".\nCon historia " + data.historia + "-" + data.ingreso);

																		//Si se registró muestro se imprime el sticker
																		alert(data.historia);
																		if (data.historia != '') {
																			alert("entro*" + data.historia);
																			var edad = calcular_edad_detalle($("#pac_fnatxtFecNac").val());

																			// var edad = $( "#txtEdad" ).val();
																			var wtip = 0;

																			if (edad.age == 0 && edad.month <= 6) {
																				wtip = 2;
																			}
																			else if (edad.age <= 12) {
																				wtip = 1;
																			}
																			try {
																				//Abro el programa de sticker
																				//crearListaAutomatica(data.historia , data.ingreso);
																				//winSticker = window.open( "../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia );
																				wemp_pmla = $('#wemp_pmla').val();
																				winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wemp_pmla=" + wemp_pmla + "&wtip=" + wtip + "&whis=" + data.historia, '', 'fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');

																				//Checkeo el radio button correspondiente de la ventana emergente
																				winSticker.onload = function () {
																					$("input:radio[value=" + wtip + "]", winSticker.document).attr("checked", true);
																				}
																			} catch (err) {
																				alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																			}
																		}

																		//Se Inicia el formulario
																		resetear2();

																		// $( "#radPreadmision" ).attr( "checked", true );
																		// $( "#pac_fectxtFechaPosibleIngreso" ).val( $( "#pac_fectxtFechaPosibleIngreso" ).attr( "msgerror" ) );
																		// $( "[name=radPreAdmi]:checked" ).click();

																		$("#radPreadmision").attr("checked", true);
																		$("#pac_fectxtFechaPosibleIngreso").attr("disabled", false);
																		$("#pac_fectxtFechaPosibleIngreso").addClass("campoRequerido", false);

																		campo.parentNode.parentNode.style.display = 'none';

																		// consultarAgendaPreadmision( fecha, incremento );
																		if ($("#soportesautomaticos").val() == 'on') {
																			//mostrarPreadmision();
																			//consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
																		}
																		else {
																			mostrarPreadmision();
																			consultarAgendaPreadmision($("#fechaAct").val(), 0);
																		}
																		location.reload();
																	}, 200
																	);

																}
																else {
																	if ($.inArray(ccoingreso[1], ccodigitalizacion) != -1) {
																		//alert("empresa en on y cco "+ccoingreso[1]+" no muestre soportes");
																		setTimeout(function () {

																			//se llenan los campos de historia,ingreso,documento despues de guardar
																			$("#ing_histxtNumHis").val(data.historia);
																			$("#ing_nintxtNumIng").val(data.ingreso);
																			$("#pac_doctxtNumDoc").val(data.documento);
																			//se ponen documento,historia,ingreso readonly
																			$('#pac_doctxtNumDoc').attr("readonly", true);
																			$('#ing_histxtNumHis').attr("readonly", true);
																			$('#ing_nintxtNumIng').attr("readonly", true);

																			if (data.error == 4) {
																				alerta("Debe egresar y volver a ingresar");
																			}

																			alerta(data.mensaje + ".\nCon historia " + data.historia + "-" + data.ingreso);

																			//Si se registró muestro se imprime el sticker

																			if (data.historia != '') {

																				var edad = calcular_edad_detalle($("#pac_fnatxtFecNac").val());

																				// var edad = $( "#txtEdad" ).val();
																				var wtip = 0;

																				if (edad.age == 0 && edad.month <= 6) {
																					wtip = 2;
																				}
																				else if (edad.age <= 12) {
																					wtip = 1;
																				}
																				try {
																					//Abro el programa de sticker
																					//crearListaAutomatica(data.historia , data.ingreso);
																					//winSticker = window.open( "../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia );
																					wemp_pmla = $('#wemp_pmla').val();
																					winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wemp_pmla=" + wemp_pmla + "&wtip=" + wtip + "&whis=" + data.historia, '', 'fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');

																					//Checkeo el radio button correspondiente de la ventana emergente
																					winSticker.onload = function () {
																						$("input:radio[value=" + wtip + "]", winSticker.document).attr("checked", true);
																					}
																				} catch (err) {
																					alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																				}
																			}

																			//Se Inicia el formulario
																			resetear2();


																			$("#radPreadmision").attr("checked", true);
																			$("#pac_fectxtFechaPosibleIngreso").attr("disabled", false);
																			$("#pac_fectxtFechaPosibleIngreso").addClass("campoRequerido", false);

																			campo.parentNode.parentNode.style.display = 'none';

																			// consultarAgendaPreadmision( fecha, incremento );
																			if ($("#soportesautomaticos").val() == 'on') {
																				//mostrarPreadmision();
																				//consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
																			}
																			else {
																				mostrarPreadmision();
																				consultarAgendaPreadmision($("#fechaAct").val(), 0);
																			}
																			location.reload();
																		}, 200
																		);

																	}
																	else {
																		//alert("empresa en on y muestre");
																		crearListaAutomatica(data.historia, data.ingreso, '', '', data.documento, data.mensaje);

																	}
																}

															}
															else {
																//alert("muestre soportes");
																crearListaAutomatica(data.historia, data.ingreso, '', '', data.documento, data.mensaje);
															}

															// DIGITALIZACION DE SOPORTES
															// debe mover la carpeta de preadmision a admision
															moverSoportesPreadmision(data.historia, data.ingreso, $("#pac_tdoselTipoDoc").val(), $.trim($("#pac_doctxtNumDoc").val()), $("#ing_feitxtFecIng").val());



														}
														else {
															crearListaAutomatica(data.historia, data.ingreso, '', '', data.documento, data.mensaje);

														}


													} else {

														if (data.mensaje == "Se actualizo correctamente") {
															alerta(data.mensaje);
															hayCambios = false;
															mostrarPreadmision();
														} else {//2020-03-20
															alerta(data.mensaje);
															hayCambios = false;
															if (data.historia != '') {
																var edad = calcular_edad_detalle($("#pac_fnatxtFecNac").val());

																// var edad = $( "#txtEdad" ).val();
																var wtip = 0;

																if (edad.age == 0 && edad.month <= 6) {
																	wtip = 2;
																}
																else if (edad.age <= 12) {
																	wtip = 1;
																}
																try {
																	//Abro el programa de sticker
																	//crearListaAutomatica(data.historia , data.ingreso);
																	//winSticker = window.open( "../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia );
																	wemp_pmla = $('#wemp_pmla').val();
																	winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wemp_pmla=" + wemp_pmla + "&wtip=" + wtip + "&whis=" + data.historia, '', 'fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');

																	//Checkeo el radio button correspondiente de la ventana emergente
																	winSticker.onload = function () {
																		$("input:radio[value=" + wtip + "]", winSticker.document).attr("checked", true);
																	}
																} catch (err) {
																	alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																}
															}
															mostrarPreadmision();
														}
													}

												}
											}
										}
									}
								);
							}
							else {
								var campos = getNombresCamposError();
								alerta(" Hay datos incompletos, por favor verifique los campos de color amarillo \n" + campos);
							}
						}
						else {
							alerta(" Hay datos incompletos, por favor verifique los campos de color amarillo");
						}
					}
					else {
						alerta("Solo se permite actualizar el ultimo ingreso");
					}
					//Fin de guardar datos

				}
			}
		}
	);
}


function resetear2(inicio) {
	//Variable para saber si esta en modo consulta o no
	modoConsulta = false;


	$("select,textarea,input[type=text],input[type=hidden]", $("#div_admisiones")).val('');
	$("input[type=radio],input[type=checkbox]", $("#div_admisiones")).attr('checked', false);

	// iniciarMarcaAqua();
	//para mostrar la fecha actual
	var now = new Date();
	var hora = now.getHours();
	var minutos = now.getMinutes();
	var segundos = now.getSeconds();
	if (hora < 10) { hora = '0' + hora }
	if (minutos < 10) { minutos = '0' + minutos }
	if (segundos < 10) { segundos = '0' + segundos }
	horaActual = hora + ":" + minutos + ":" + segundos;


	//datos por defecto a iniciar
	$("#pac_zonselZonRes").val('U');
	//$( "#pac_trhselTipRes" ).val( 'N' );
	$("#ing_lugselLugAte").val('1');
	$("#pac_tdoselTipoDoc").val('CC');
	$("#pac_tdaselTipoDocRes").val('CC');
	$("#pac_fnatxtFecNac").val($("#fechaAct").val()); //fecha aut
	calcular_edad($("#pac_fnatxtFecNac").val()); //pac_fnatxtFecNac
	$("#ing_feitxtFecIng").val($("#fechaAct").val()); //fecha ing
	$("input[type='res_firtxtNumcon']").val($("#fechaAct").val()); //fecha ing
	// $( "#ing_hintxtHorIng" ).val($( "#horaAct" ).val() ); //hora ing
	$("#ing_hintxtHorIng").val(horaActual);
	$("#ing_fhatxtFecAut").val($("#fechaAct").val()); //fecha aut
	$("#ing_hoatxtHorAut").val(horaActual); //hora aut

	valorDefecto = $("#ing_claselClausu>option[defecto='on']").val();
	if (valorDefecto == undefined)
		$("#ing_claselClausu").val(1);
	else
		$("#ing_claselClausu").val(valorDefecto);
	$("#pac_petselPerEtn").val(6);
	$("#pac_fnatxtFecNac").val("");

	resetAqua();

	$("#bot_navegacion").css("display", "none"); //se oculta el div de navegacion de resultados
	$("#bot_navegacion1").css("display", "none"); //se oculta el div de navegacion de resultados

	//2014-05-06 validarTipoResp('');
	var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
	//console.log("es aqui "+objetoRes[0].value);
	validarTipoResp(objetoRes[0]);
	//se borran todos los label de error que se encuentren
	$("label").remove();
	//se pone el estado del paciente en blanco como es un span se le envia .html
	$("#spEstPac").html("");

	$("#div_accidente_evento").css({ display: "none" });
	//se ponen los campos readonly false para que se puedan llenar
	$('#pac_doctxtNumDoc').attr("readonly", false);
	$('#ing_histxtNumHis').attr("readonly", false);
	$('#ing_nintxtNumIng').attr("readonly", false);

	//Al iniciar datos se borra el log
	if (!inicio) {
		borrarLog($("#key"));
	}

	// $( "#radAdmision" ).attr( "checked", true );
	// $( "#pac_fectxtFechaPosibleIngreso" ).val( $( "#pac_fectxtFechaPosibleIngreso" ).attr( "msgerror" ) );
	// $( "[name=radPreAdmi]:checked" ).click();

	ultimaPreadmisionCargada = '';
}


/************************************************************************
 * Agosto 21 de 2013
 * Calcula la edad de un paciente con años, meses y días
 ************************************************************************/
function calcular_edad_detalle(fecha) {
	var objEdad = {};

	var fecha = fecha.split("-");

	var today = new Date();
	var birthDate = new Date(fecha[0], fecha[1] - 1, fecha[2], 0, 0, 0, 0);
	objEdad.age = today.getFullYear() - birthDate.getFullYear();
	objEdad.month = today.getMonth() - birthDate.getMonth();
	objEdad.day = today.getDate() - birthDate.getDate();

	if (objEdad.month < 0 || (objEdad.month === 0 && today.getDate() < birthDate.getDate())) {
		if (objEdad.age > 0)
			objEdad.age--;
	}

	if (objEdad.month < 0) {
		objEdad.month = 12 + objEdad.month;
	}

	if (objEdad.day < 0) {

		if (objEdad.month > 0)
			objEdad.month--;

		objEdad.day = 30 + objEdad.day;
	}

	return objEdad;
}

/****************************************************************************************************************
 * Agosto 26 de 2013
 * Deja todos los campos del formulario en blanco
 ****************************************************************************************************************/
function iniciar() {
	if (hayCambios) {
		if (!confirm("Perder&aacute; la informaci&oacute;n digitada. Desea continuar?")) {
			return;
		}
	}
	$("#tabla_eps").find("tr[id$='_tr_tabla_eps']").remove();
	var wbasedato = $("#wbasedato").val();
	var wemp_pmla = $("#wemp_pmla").val();
	var accion_consultar = $("#accion_consultar").val();

	addFila('tabla_eps', wbasedato, wemp_pmla);

	// $( "input:hidden,input:text,select,textarea", $( "#div_admisiones" ) ).val('');
	$("input[type=hidden][id!='codCausaAccTrans'],input[type=text],select,textarea", $("#div_admisiones")).val('');
	$("input:radio,input:checkbox,select,textarea", $("#div_admisiones")).attr("checked", false);

	$("#pac_doctxtNumDoc,#ing_histxtNumHis,#ing_nintxtNumIng").attr("readonly", false);

	$("#bot_navegacion1,#bot_navegacion").css({ display: "none" });

	resetAqua($("#div_admisiones"));

	$("#spEstPac").html('');

	ultimaPreadmisionCargada = '';

	$("#txtAdmisionPreadmision").html('');

	$("#tabla_responsables_1_2,#tr_titulo_tercer_resp").hide();
	$("#div_datos_autorizacion,tabla_eps").show();
	// $( "#ing_ordtxtNumAut,#ing_fhatxtFecAut,#ing_hoatxtHorAut,#ing_npatxtNomPerAut,#ing_cactxtcups,#ing_pcoselPagCom" ).attr( "disabled", false );
	// $( "#ing_fhatxtFecAut" ).val($( "#fechaAct" ).val() ); //fecha aut
	// $( "#ing_hoatxtHorAut" ).val( horaActual); //hora aut
	$("#ing_fhatxtFecAut,#ing_hoatxtHorAut").removeClass("campoRequerido");
	$("#div_accidente_evento").hide();

	//se borra todo lo del div para que cuanse se abra la lista este en blanco
	$("#div_contenedor").html("");

	if ($("input[name='btRegistrarActualizar']").attr("graba") == "on") {
		$("input[name='btRegistrarActualizar']").attr("disabled", false);
	} else {
		$("input[name='btRegistrarActualizar']").attr("disabled", true);
	}
	$("input[name='btRegistrarActualizar']").val("Admitir");

	var wbasedato = $("#wbasedato").val();
	var wemp_pmla = $("#wemp_pmla").val();

	//se borran los trs de la tabla_eps menos la primera fila


	//se borran los trs de la tabla_topes menos la primera fila
	$("#tabla_topes").find("tr[id$='_tr_tabla_topes']:not([id='1_tr_tabla_topes'])").remove();

	//se ponen los valores de ese tr en blanco
	$("[id^=ing_tpaselTipRes],[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", $('#tabla_eps >tbody >tr').eq(2)).val("");


	//se pone el div de especialidades oculto
	$("#div_mensaje_PerEspeciales").css("display", "none");

	//Consultar el numero de historia
	consultarConsecutivo();
	nuevoAccidenteTransito();

	$("#numTurnoPaciente").attr("valor", "").html("SIN TURNO!!!");
	if (accion_consultar == "on") {
		$("#accion_consultar").val(accion_consultar)
		$("input[type='button'][name='btRegistrarActualizar']").attr("disabled", "disabled");
		$("input[type='button'][name='btRegistrarActualizar']").hide();
	}
}

function consultarConsecutivo() {
	var objJson = {};	//Creo el objeto
	objJson.accion = "consultarConsecutivo";	//agrego un parametro más
	objJson.wbasedato = $("#wbasedato").val();
	objJson.consultaAjax = "";
	objJson.wemp_pmla = $("#wemp_pmla").val();

	$.blockUI({ message: "Espere un momento por favor..." });

	$.post("admision_erp.php",
		objJson,
		function (data) {
			$.unblockUI();
			if (!isNaN(data)) {
				$("#ing_histxtNumHis").attr("placeholder", data);
			}
		}
	);

}

window.onbeforeunload = function () {
	borrarLog($("#key"));
}

function ocultarMostrarPacientesIngresados(num) {
	$("#dvAgendaAmdisionDatos").toggle();
}


function mostrarAdmision() {
	resetear();
	$("#radAdmision").attr("checked", true);
	$("#radPreadmision").attr("checked", false);
	opcionAdmisionPreadmision();

	hayCambios = false;
	if ($("input[name='btRegistrarActualizar']").attr("graba") == "on") {
		$("input[name='btRegistrarActualizar']").attr("disabled", false);
		var fechaHora;
		fechaHora = consultarFechaHoraActual();
		$("#wfiniAdm").val(fechaHora.fecha);
		$("#whiniAdm").val(fechaHora.hora);
	} else {
		$("input[name='btRegistrarActualizar']").attr("disabled", true);
	}
	$("input[name='btRegistrarActualizar']").val("Admitir");

	//llamarModalNoFlujo();

}
// funcion que consulta en las tablas de flujos que pacientes no tienen asignado flujo en los ultimos 3 dias , este parametro se puede modificar
function llamarModalNoFlujo() {

	$.post("admision_erp.php",
		{
			accion: "llamarModalNoFlujo",
			consultaAjax: '',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,

		}, function (data) {
			//alert (data.contenidoDiv);
			if (data.html == 'abrirModal') {

				$("#divfaltantesporflujo").html(data.contenidoDiv).show().dialog({
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "<div align='center' style='font-size:10pt'>Lista de Pacientes sin flujo</div>",
					width: "auto",
					height: "400"

				});

				$("#divfaltantesporflujo").css({
					width: 'auto', //probably not needed
					height: 'auto'
				});

				//$( "#divfaltantesporflujo" ).dialog({ dialogClass: 'hide-close' });

			}


		}, 'json');



}

function actualizarEstadoSoporte(valor, soporte, whistoria, wingreso) {


	if ($("#checkboxsoporte_" + soporte).is(':checked')) {
		valor = 's';
	}
	else {
		valor = 'na';
	}
	$.post("admision_erp.php",
		{
			accion: "actualizarEstadoSoporte",
			consultaAjax: '',
			wsoporte: soporte,
			whistoria: whistoria,
			wingreso: wingreso,
			westado: valor,
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,

		}, function (data) {

			//alert(data);

		});

}

function cerrarsinflujo() {

	$("#divfaltantesporflujo").dialog('close');

}

function crearflujo(historia, ingreso) {
	//alert(historia+"---"+ingreso);
	crearListaAutomatica(historia, ingreso, '', '');

}

function prepararParaConsulta() {
	mostrarAdmision();
	iniciar();
	$("#accion_consultar").val("on");
	$("input[type='button'][name='btRegistrarActualizar']").attr("disabled", "disabled");
	$("input[type='button'][name='btRegistrarActualizar']").hide();
}

function verificarDocumento() {
	if ($("#accion_consultar").val() != "on") {
		conservarNumeroDeTurno();
		mostrarDatosPreadmision();
		consultarClientesEspeciales();
		consultarSiActivo();
		consultarSiRechazado();
		consultarSiPreanestesia();
	}
}

function verificarTriageUrgencias() {

	var wbasedato = $("#wbasedato").val();
	var cedula = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc = $("#pac_tdoselTipoDoc").val();
	var centroCostos = $("#ing_seisel_serv_ing").val();
	if (tipoDoc == "" && cedula == "")
		return;
	$("#div_des_triage").show();
	if ($("#permitirVerListaTurnos")) {
		//---> consultar triage urgencias del paciente

		$.ajax({
			url: "admision_erp.php",
			type: "post",
			async: false,
			data: {

				consultaAjax: "",
				accion: "verificarTriageUrgencias",
				tipoDoc: tipoDoc,
				documento: cedula,
				wbasedato: wbasedato,
				wemp_pmla: $("#wemp_pmla").val()
			},
			success: function (respuesta) {
				$("#div_des_triage").attr("title", respuesta);
				$("#div_des_triage").tooltip({ track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			}
		});

	} else {
		return;
	}
}

function consultarFechaHoraActual() {
	var datos;
	$.ajax({
		url: "admision_erp.php",
		type: 'post',
		async: false,
		data: {
			consultaAjax: '',
			accion: 'consultarFechaHoraActual'
		},
		success: function (data) {
			datos = data;
		},
		dataType: "json"
	});
	return (datos);
}

var llenarTablaLog = true;
function mostrarPreadmision() {
	if (hayCambios) {
		if (!confirm("Perder\u00E1 la informaci\u00F3n digitada. Desea continuar?")) {
			return;
		}
	}

	//alert("hola");
	//2013-03-25
	llenarTablaLog = false;
	location.reload(); $("#accion_consultar").val("on");
	return;
	//alert("sale");

	resetear();
	resetearAccidentes();
	resetearEventosCatastroficos();

	$("#radPreadmision").attr("checked", true);
	$("#radAdmision").attr("checked", false);
	opcionAdmisionPreadmision();
	$("#bot_navegacion1").css("display", "none");
	$("#bot_navegacion").css("display", "none");

	consultarAgendaPreadmision($("#fecActAgenda").html(), 0);
	consultarAgendaAdmitidos($("#fecActAdmitidos").html(), 0);
}

/**************************************************************************************************
 * Septiembre 10 de 2013
 *
 * Realiza el ajax para consultar los datos de admitidos
 **************************************************************************************************/
function consultarAgendaAdmitidos(fecha, incremento) {
	var objJson = {};	//Creo el objeto
	objJson.accion = "consultarAdmitidos";	//agrego un parametro más
	objJson.wbasedato = $("#wbasedato").val();
	objJson.tema = $('#tema').val();
	objJson.consultaAjax = "";
	objJson.wemp_pmla = $("#wemp_pmla").val();
	objJson.cco_usuario = $("#cco_usuario").val();
	objJson.fecha = fecha;
	objJson.incremento = incremento;
	objJson.consulta = $("#perfil_consulta").val();
	objJson.filtrarCcoAyuda = $("#filtrarCcoAyuda").val();

	$.blockUI({ message: "Espere un momento por favor..." });

	$.post("admision_erp.php",
		objJson,
		function (data) {
			$.unblockUI();
			if (data.error == 1) {
				alerta(data.mensaje);
			} else if (data.error == 2) {
				alerta(data.mensaje);
				return;
			}
			else {
				if (data.mensaje != '')
					alerta(data.mensaje);

				if (data.html) {
					$("#dvAgendaAmdisionDatos").html(data.html);

					$("#dvAgendaAmdisionDatos [fecha]").datepicker({
						dateFormat: "yy-mm-dd",
						fontFamily: "verdana",
						dayNames: ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
						monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
						dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
						dayNamesShort: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
						monthNamesShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
						changeMonth: true,
						changeYear: true,
						yearRange: "c-100:c+100"
					});
				}
			}


		},
		"json"
	);
}

/*****************************************************************************************
 * Septiembre 02 de 2013
 * Creo clones de los options de ORIGEN E LA ATENCIÓN, solo los de accidentes de transito
 * y evento catastróficos
 *****************************************************************************************/
optOrigenAtencion = '';
/*****************************************************************************************/

/********************************************************************************************************************************
 * Anula una admisión
 ********************************************************************************************************************************/
function anularAdmision(historia, ingreso, tipoDoc, cedula, automatico = '') {

	continuar = false;
	if (automatico == "on") {
		continuar = true;
	} else {
		if (confirm("Desea anular la admisión?")) {
			continuar = true;
		} else {
			continuar = false;
			return;
		}
	}
	if (continuar) {

		var objJson = {};	//Creo el objeto
		objJson.accion = "anularAdmision";	//agrego un parametro más
		objJson.wbasedato = $("#wbasedato").val();
		objJson.consultaAjax = "";
		objJson.wemp_pmla = $("#wemp_pmla").val();
		objJson.historia = historia;
		objJson.ingreso = ingreso;
		objJson.tipoDoc = tipoDoc;
		objJson.cedula = cedula;

		if (automatico != "on")
			$.blockUI({ message: "Espere un momento por favor..." });

		$.ajax({
			type: "POST",
			url: "admision_erp.php",
			data: objJson,
			async: false,
			success: function (data) {
				if (automatico != "on")
					$.unblockUI();
				if (data.mensaje != '' && automatico != "on")
					alert(data.mensaje);
				if (automatico != "on") {
					consultarAgendaPreadmision($("#fecActAgenda").html(), 0);
					consultarAgendaAdmitidos($("#fecActAdmitidos").html(), 0);
				}

			},
			dataType: "json"
		});

	}
}

function imprimirHistoria(historia, ingreso) {
	wbasedatoImp = $("#wbasedatoImp").val();
	winSticker = window.open("../../ips/reportes/r001-admision.php?wpachi=" + historia + "&wingni=" + ingreso + "&empresa=" + wbasedatoImp);
	consultarAgendaPreadmision($("#fecActAgenda").html(), 0);
	consultarAgendaAdmitidos($("#fecActAdmitidos").html(), 0);
}

function mostrarDatosDemograficos() {
	//No haga el llamado si el documento está vacio
	if ($.trim($("#pac_doctxtNumDoc").val()) == '')
		return;

	//Variable para saber si esta en modo consulta o no
	modoConsulta = true;
	llenadoAutomatico = true;

	var objJson = {};

	objJson.accion = "mostrarDatosDemograficos";	//agrego un parametro más
	objJson.wbasedato = $("#wbasedato").val();
	objJson.consultaAjax = "";
	objJson.pac_tdo = $("#pac_tdoselTipoDoc").val();
	objJson.pac_doc = $.trim($("#pac_doctxtNumDoc").val());
	objJson.wemp_pmla = $("#wemp_pmla").val();

	$.post("admision_erp.php",
		objJson,
		function (data) {

			$.unblockUI();

			if (isJSON(data) == false) {
				alerta("RESPUESTA NO ESPERADA\n" + data);
				modoConsulta = false;
				llenadoAutomatico = false;
				return;
			}
			//console.log('data'+ data);
			data = $.parseJSON(data);



			if (data.error == 1) {
				alerta(data.mensaje);
			}
			else {
				if (data.mensaje != '')
					alerta(data.mensaje);

				if (data.infoing) {
					if (true) {
						informacionIngresos = data;
						informacionIngresos.regTotal = data.infoing.length;
						informacionIngresos.posAct = data.infoing.length - 1;
						$("#cargoDatosConsulta").val("on");

						navegacionIngresosPreadmision(0);
						cambioCobertura($("#pac_tusselCobSal"));
					}
				} else {
					if ($("#cargoDatosConsulta").val() == "on") {
						resetear();


						// --> Limpiar el numero del turno, 2015-11-24: Jerson trujillo.
						if (numeroTurnoTemporal != "") {
							$("#numTurnoPaciente").attr("valor", numeroTurnoTemporal).html(numeroTurnoTemporal);
							numeroTurnoTemporal = "";
						}
						else
							$("#numTurnoPaciente").attr("valor", "").html("SIN TURNO!!!");

					} else {

					}
				}

				modoConsulta = false;
				llenadoAutomatico = false;
				llenarDatosLog(true);
			}
		}
	);
}



function buscarAseguradorasVehiculo() {
	var wbasedato = $("#wbasedato").val();
	//Asigno autocompletar para la busqueda de aseguradoras
	$("[name=_ux_accasn]").autocomplete("admision_erp.php?consultaAjax=&accion=consultarAseguradoraVehiculo&wbasedato=" + wbasedato,
		{
			cacheLength: 1,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 3,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.des;
				this._lastValue = this.value;
				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).on({
			change: function () {

				var cmp = this;
				var buscarDatosResponsable = true;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite una Aseguradora v\u00E1lida")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					} else {

						if ($("[name='dat_Acccas_ux_acccas']").val() != $("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal")) {
							if ($.trim($("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal")) != "") {//--> si original = vacio, no verifica saldo unix
								var habilitadoParaCambiar = validarFacturacionUnix($("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal"));
								if (!habilitadoParaCambiar) {
									alerta(" El responsable original tiene facturas activas en unix, no se puede realizar el cambio");
									$("[name='dat_Acccas_ux_acccas']").val($("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal"));
									$("[name='_ux_accasn']").val($("[name='_ux_accasn']").attr("empresaOriginal"));
									buscarDatosResponsable = false;
								}
							}
						}
						if (buscarDatosResponsable)//--> para que no repita llamados si en realidad continua el original
							buscarPrimerResp();
					}
				}, 200);

			}
		});

}

function validarFacturacionUnix(codigoResponsableOriginal) {

	var historia = $("#ing_histxtNumHis").val();
	var ingreso = $("#ing_nintxtNumIng").val();
	var respuesta = false;

	$.ajax({
		url: "admision_erp.php",
		type: "POST",
		data:
		{
			consultaAjax: '',
			accion: 'validarFacturacionUnix',
			historia: historia,
			ingreso: ingreso,
			responsable: codigoResponsableOriginal,
			wemp_pmla: $("#wemp_pmla").val(),
			wbasedato: $("#wbasedato").val()
		},
		async: false,
		success: function (data) {
			//return( data.respuesta );
			if (data.error == 1) {

				if (data.mensaje != '') {
					alerta(data.mensaje);
				}
				respuesta = false;
			}
			respuesta = data.respuesta;

		},
		dataType: "json"
	});
	return (respuesta);
}

function buscarSegundoResp() {
	var wbasedato = $("#wbasedato").val();  //el medcid que une la tabla 10 con la 51 en citascs faltan la otras

	$.post("admision_erp.php",
		{
			wbasedato: wbasedato,
			consultaAjax: '',
			accion: 'consultarSegundoResp'

		}
		, function (data) {
			if (data.error == 1) {
				if (data.mensaje != '') {
					alerta(data.mensaje);
				}
			}
			else {
				$("#re2txtCodRes2").val(data.nom);
				$("#dat_Accre2hidCodRes2").val(data.cod);
				$("#re2hidtopRes2").val(data.topeS); //valor tope
				$("#re2txtCodRes2").removeAttr("msgerror");
				$("#re2txtCodRes2").removeClass("campoRequerido");
				$("#re2txtCodRes2").attr("readonly", true);


			}
		},
		"json"
	);
}
//busca primer responsable de accidente de transito
function buscarPrimerResp() {

	var wbasedato = $("#wbasedato").val();
	var asegu = $("[name=dat_Acccas_ux_acccas]").val(); //campo aseguradora del formulario de accidentes [name=_ux_accasn]
	if (asegu == "") return;

	$.post("admision_erp.php",
		{
			wbasedato: wbasedato,
			consultaAjax: '',
			accion: 'consultarPrimerResp',
			asegu: asegu,
			fechaAccidente: $("#dat_Accfec").val()

		}, function (data) {
			if (data.error == 1) {
				if (data.mensaje != '') {
					alerta(data.mensaje);
				}
			}
			else {

				if (data.mensaje != '') {
					alerta(data.mensaje);  // update Ok.
				}
				$("#restxtCodRes").val(data.nom);
				$("#dat_AccreshidCodRes24").val(data.cod24); //codigo de la tabla 24
				// $("#dat_Acc_cashidCodRes193").val(data.cod193); //codigo de la tabla 193
				$("#dat_AcctartxtTarRes").val(data.tar);
				$("#dat_AccvsmtxtSalMin").val(data.vsm);
				$("#dat_AcctoptxtValTop").val(data.tope);

				$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop").removeAttr("msgerror");
				$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop").removeClass("campoRequerido");
				$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop").attr("readonly", true);

				//2014-02-25 COMO VIENE POR SOAT, SE DEBE MOSTRAR COMO PRIMER RESPONSABLE
				$('#tabla_responsables_1_2').show();
				$('#tr_titulo_tercer_resp').show();
				$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,ing_vretxtValRem").attr("disabled", false);
				reOrdenarResponsables(); //Para poner R1,R2,R3...
			}
		},
		"json"

	);

}

function listarEventosCatastroficos() {
	//si no viene vacio el div_contenedor que muestre lo que ya tiene
	if ($("#div_contenedor").html() != '') {
		$.blockUI(
			{
				message: $("#div_contenedor"),
				css: {
					top: ($(window).height() * 0.5) / 5 + 'px',
					left: ($(window).width() * 0.5) / 5 + 'px',
					width: "50%",
					heightStyle: "content",
					textAlign: "left",
					cursor: ""
				}
			}
		);
		if (informacionIngresos != "") {
			//codigo del evento despues de consultar la información del paciente ya registrada en BD
			var codEven = informacionIngresos.infoing[informacionIngresos.posAct].det_Catcod
			if (codEven) {
				//$("#chkagregar_"+codEven )[0] = document.getElementById( 'chkagregar_"+codEven' )
				//sin el [0] es todo el objeto, con el [0] es el elemento (this)
				seleccionarCheckbox($("#chkagregar_" + codEven)[0], codEven);
			}
		}
	}
	else {
		$.post("admision_erp.php",
			{
				consultaAjax: '',
				accion: 'listaEventos',
				wbasedato: $("#wbasedato").val()

			}
			, function (data) {
				if (data.error == 1 && data.mensaje != '') {
					alerta(data.mensaje);
				}
				else {
					$("#div_contenedor").html(data.html);
					//lo que retorna el data.html se le pone al blockui
					$.blockUI(
						{
							message: $("#div_contenedor"),
							css: {
								top: ($(window).height() * 0.5) / 5 + 'px',
								left: ($(window).width() * 0.5) / 5 + 'px',
								width: "50%",
								heightStyle: "content",
								textAlign: "left",
								cursor: ""
							}
						}
					);

					// if(informacionIngresos != "")
					// {
					// //codigo del evento despues de consultar la información del paciente ya registrada en BD
					// var codEven = informacionIngresos.infoing[ informacionIngresos.posAct ].det_Catcod
					// if ( codEven )
					// {
					// //$("#chkagregar_"+codEven )[0] = document.getElementById( 'chkagregar_"+codEven' )
					// //sin el [0] es todo el objeto, con el [0] es el elemento (this)
					// seleccionarCheckbox( $("#chkagregar_"+codEven )[0] ,codEven);
					// }
					// }

				}
			},
			"json"
		);
	}

	consultaEvento = false;

}

function mostrarDetalleEventosCatastroficos(codigo) {
	var modoConsultaEvento = true;

	$.post("admision_erp.php",
		{
			consultaAjax: '',
			accion: 'mostrarDetalleEvento',
			wbasedato: $("#wbasedato").val(),
			codigo: codigo

		}
		, function (data) {
			if (data.error == 1 && data.mensaje != '') {
				alerta(data.mensaje);
			}
			else {

				//se llena el formulario antes
				setDatos(data.infoing[0], $("#eventosCatastroficos"), 'name');

				//muestra el formulario
				mostrarEventosCatastroficos();
				//se le quita la clase campo requerido
				$("textarea,input,select", $("#eventosCatastroficos")).removeClass("campoRequerido");
				if (modoConsultaEvento = true) {
					//se oculta el boton de guardar
					$("#btnGuardarEventosCatastroficos").hide();
					//se cambia el value del boton de que cierra los eventos.
					$("#btnCerrarEventosCatastroficos").val("Salir");
				}
				if (consultaEvento == true) {
					//se oculta el boton de guardar por si va a modificar el evento del ultimo ingreso
					$("#btnGuardarEventosCatastroficos").show();
				}
			}
		},
		"json"
	);
	modoConsultaEvento = false;
}

function cancelarEvento(evt) {

	evt.stopPropagation(); //detener la propagacion del evento

}

function seleccionarCheckbox(check, cod) {
	var aux = $(check).is(":checked"); //se pregunta el estado viene apenas para hacer la relacion
	$('#div_eventos_catastroficos').find('input[type=checkbox]').attr('checked', false); // se deschequean todos

	if (aux) //si viene chequeado lo chequea porque antes se deschequearon todos
	{
		$(check).attr('checked', true);
		$("[id=hidcodEvento]").val(cod); //se llena el hidden con el codigo del evento chequeado
	}
	else //esta mostrando una relacion ya hecha, se esta consultando
	{
		if (consultaEvento == true) {
			$(check).attr('checked', true);
		}
	}

}

function guardarRelacionHistoriaEvento() {
	var validar = false;

	if ($('#div_eventos_catastroficos').find('input[type=checkbox]').is(':checked')) {
		validar = true;
	}

	if (validar) {
		$.unblockUI();
	}
	else {
		alerta("Debe seleccionar un Evento Catastrofico");
	}

	//Si se guarda la información, muestro el div correspondiente con el botón para mostrar
	//el formulario de eventos catastroficos
	$("#div_accidente_evento").css({ display: "" });	//muestro el div que tiene los botones para abrir los formularios
	$("td", $("#div_accidente_evento")).eq(0).css({ display: "none" });	//oculto el boton de accidentes de transito
	$("td", $("#div_accidente_evento")).eq(1).css({ display: "none" });	//oculto el boton de eventos catastróficos
	$("td", $("#div_accidente_evento")).eq(2).css({ display: "" });  //muestro el nuevo boton que me lleva a la lista de eventos

	//para poner el foco en el select de origen de la atencion cuando se cierre el blockui
	$("#ing_caiselOriAte").focus();
}

function quitarRelacion() {
	$('#div_eventos_catastroficos').find('input[type=checkbox]').attr('checked', false); // se deschequean todos
}

function buscarCcoTopes(tabla_referencia) {
	var wbasedato = $("#wbasedato").val();
	var wemp_pmla = $("#wemp_pmla").val();

	if (tabla_referencia != "") {
		// para saber si la tabla tiene filas o no
		trs = $("#" + tabla_referencia, $("#div_cont_tabla_topes" + idCodResp)).find('tr[id$=tr_' + tabla_referencia + ']').length;
		var value_id = 0;

		//busca consecutivo mayor
		if (trs > 0) {
			id_mayor = 0;
			// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
			$("#" + tabla_referencia, $("#div_cont_tabla_topes" + idCodResp)).find('tr[id$=tr_' + tabla_referencia + ']').each(function () {
				id_ = $(this).attr('id');
				id_splt = id_.split('_');
				id_this = (id_splt[0]) * 1;
				if (id_this >= id_mayor) {
					id_mayor = id_this;
				}
			});
			// id_mayor++;
			value_id = id_mayor + '_tr_' + tabla_referencia;

		}
		else { value_id = '1_tr_' + tabla_referencia; }

		codEsp = "#top_ccotxtCcoTop" + value_id;
	}

	//Asigno autocompletar para la busqueda de paises
	$("[id^=top_ccotxtCcoTop]", $("#div_cont_tabla_topes" + idCodResp)).autocomplete("admision_erp.php?consultaAjax=&accion=consultarCcoTopes&wbasedato=" + wbasedato + "&wemp_pmla=" + wemp_pmla,
		{
			cacheLength: 1,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 1,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {

				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.des;
				this._lastValue = datos[0].valor.des;
				this._lastCodigo = datos[0].valor.cod;

				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).on({
			change: function () {

				var cmp = this;

				setTimeout(function () {

					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite un Centro de costo válida")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 200);

			}
		})
		;
}
objglobal = {};
function guardartopes(codRespGlobal, DivTopes) {


	objglobal[codRespGlobal] = {};

	// armar vector para grabar
	//--recorro todos los topes principales.

	// envio un texto con _ para saber que es un nuevo registro
	var historia = ($("#ing_histxtNumHis").val());

	var ingreso = ($("#ing_nintxtNumIng").val());


	$(".campoObligatorio").removeClass('campoObligatorio');

	var insertar = '';
	cumplevalidacion = 'si';
	var mensaje = '';
	$(".trtopeppal").each(function () {
		codigotope = $(this).attr("codigotope");
		//alert(codigotope);
		var haydetalle = 'no';
		var haydetalle2 = 'no';


		$(".detalletope_" + codigotope).each(function () {
			var clasificacion = $(this).attr("clasificacion");

			// detalles
			//--valortopedetalle_

			if ($("#valortopedetalle_" + codigotope + "_" + clasificacion).val().replace(/,/g, "") > 0 || $("#porcentajetopedetalle_" + codigotope + "_" + clasificacion).val() > 0) {
				//alert($("#porcentajetopedetalle_"+codigotope+"_"+clasificacion).val());
				haydetalle = 'si';
				var ccodetalle = $("#selectccotopes_" + codigotope + "_" + clasificacion).val();
				var valordetalle = $("#valortopedetalle_" + codigotope + "_" + clasificacion).val();
				var porcentajedetalle = $("#porcentajetopedetalle_" + codigotope + "_" + clasificacion).val();


				$("#tipo_empresa").addClass('campoObligatorio');

				if ($("#diariotopedetalle_" + codigotope + "_" + clasificacion).is(":checked")) {
					var diario = 'on';
				}
				else {
					var diario = 'off';
				}
				$("#fechatopedetalle_" + codigotope + "_" + clasificacion).removeClass('campoObligatorio');
				if ($("#fechatopedetalle_" + codigotope + "_" + clasificacion).val() == '') {
					$("#fechatopedetalle_" + codigotope + "_" + clasificacion).addClass('campoObligatorio');
					cumplevalidacion = 'no';

				}
				$("#porcentajetopedetalle_" + codigotope + "_" + clasificacion).removeClass('campoObligatorio');
				if ($("#porcentajetopedetalle_" + codigotope + "_" + clasificacion).val() == '') {
					$("#porcentajetopedetalle_" + codigotope + "_" + clasificacion).addClass('campoObligatorio');
					cumplevalidacion = 'no';
				}

				var fechatopedetalle = $("#fechatopedetalle_" + codigotope + "_" + clasificacion).val();

				insertar = insertar + "_" + historia + ":" + ingreso + ":" + codigotope + ":" + clasificacion + ":" + ccodetalle + ":" + valordetalle + ":" + porcentajedetalle + ":" + diario + ":" + fechatopedetalle; // construyo el detalle de los topes alert("grabo");
				// historia ingreso responsable tconcepto Topcla  cco valor  porcentaje diario saldo estado y fecha

			}


		});
		//---medios


		if (($("#valortopeppal_" + codigotope).val().replace(/,/g, "") > 0 || $("#porcentajetopeppal_" + codigotope).val() > 0) && (haydetalle == 'no')) {
			var clasificaciong = $(this).attr("clasificacion");
			var ccog = $("#selectccotopesppal_" + codigotope).val();
			var valorg = $("#valortopeppal_" + codigotope).val();
			var porcentajeg = $("#porcentajetopeppal_" + codigotope).val();

			if ($("#diariotopeppal_" + codigotope).is(":checked")) {
				var diariog = 'on';
			}
			else {
				var diariog = 'off';
			}
			//alert("grabo un principal");
			$("#fechatopeppal_" + codigotope).removeClass('campoObligatorio');
			if ($("#fechatopeppal_" + codigotope).val() == '') {
				$("#fechatopeppal_" + codigotope).addClass('campoObligatorio');
				cumplevalidacion = 'no';

			}
			$("#porcentajetopeppal_" + codigotope).removeClass('campoObligatorio');
			if ($("#porcentajetopeppal_" + codigotope).val() == '') {
				$("#porcentajetopeppal_" + codigotope).addClass('campoObligatorio');


			}


			var fechag = $("#fechatopeppal_" + codigotope).val();
			insertar = insertar + "_" + historia + ":" + ingreso + ":" + codigotope + ":" + clasificaciong + ":" + ccog + ":" + valorg + ":" + porcentajeg + ":" + diariog + ":" + fechag; // construyo el detalle de los topes alert("grabo");

		}


	});



	if ($("#spEstPac").text() == 'ACTIVO') {
		var activo = 'on';
	}
	else
		var activo = 'off';

	//------ general
	if (insertar == '') {

		if ($("#valortopegeneral").val().replace(/,/g, "") > 0 || $("#porcentajegeneral").val() > 0) {
			// veo el tope general
			if ($("#diariotopegeneral").is(":checked")) {
				var diariog = 'on';
			}
			else {
				var diariog = 'off';
			}

			$("#fechatopegeneral").removeClass('campoObligatorio');
			if ($("#fechatopegeneral").val() == '') {
				$("#fechatopegeneral").addClass('campoObligatorio');
				cumplevalidacion = 'no';

			}
			$("#porcentajegeneral").removeClass('campoObligatorio');
			if ($("#porcentajegeneral").val() == '') {
				$("#porcentajegeneral").addClass('campoObligatorio');
				cumplevalidacion = 'no';
			}

			$("#valortopegeneral").removeClass('campoObligatorio');
			if ($("#valortopegeneral").val() == '') {
				$("#valortopegeneral").addClass('campoObligatorio');
				cumplevalidacion = 'no';
			}


			insertar = insertar + "_" + historia + ":" + ingreso + ":*:*:" + $("#selectccogeneral").val() + ":" + $("#valortopegeneral").val() + ":" + $("#porcentajegeneral").val() + ":" + diariog + ":" + $("#fechatopegeneral").val(); // construyo el detalle de los topes alert("grabo");
		}
	}


	var esadmision = 'si';
	if ($("[name=radPreAdmi]:checked").val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR') {
		esadmision = 'no';
	}




	if ($("[name=radPreAdmi]:checked").val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR') {
		//alert("entro");
		if ($("input[name='btRegistrarActualizar']").val() == 'Preadmitir') {
			if (cumplevalidacion == 'si') {
				$("#divtabletopes_" + codRespGlobal + "  input ").each(function () {

					if ($(this).attr('type') == "checkbox") {

						if ($(this).attr('checked')) {
							if (!objglobal[codRespGlobal])
								objglobal[codRespGlobal] = {};

							objglobal[codRespGlobal][$(this).attr('id')] = 'on';
						}
					}
					else {
						if ($(this).attr('type') != "button") {
							if ($(this).val() != '') {
								if (!objglobal[codRespGlobal])
									objglobal[codRespGlobal] = {};

								objglobal[codRespGlobal][$(this).attr('id')] = $(this).val();
								//alert($(this).attr('id')+"----"+$(this).val());
							}
						}
					}
				});


				$("#divtabletopes_" + codRespGlobal + "  select ").each(function () {
					if (!objglobal[codRespGlobal])
						objglobal[codRespGlobal] = {};

					objglobal[codRespGlobal][$(this).attr('id')] = $(this).val();
				});



				if ($("#topeGrabar_" + codRespGlobal).length == 0) {
					$("#Divresponsablestopes").append("<input class='grabarCuandoAdmite'  responsable='" + codRespGlobal + "' id='topeGrabar_" + codRespGlobal + "'  type='hidden' value='" + insertar + "'>");
					$("#Divresponsablestopes").append("<input id='htmlresponsable" + codRespGlobal + "' type='hidden' value='" + $("#divtabletopes_" + codRespGlobal).html() + "'>");
					$("#Divresponsablestopes").append("<input id='htmlresponsablevector" + codRespGlobal + "' type='hidden' >");

				}
				else {
					$("#topeGrabar_" + codRespGlobal).remove()
					$("#htmlresponsable" + codRespGlobal).remove();
					$("#htmlresponsablevector" + codRespGlobal).remove();
					$("#Divresponsablestopes").append("<input class='grabarCuandoAdmite'  responsable='" + codRespGlobal + "' id='topeGrabar_" + codRespGlobal + "'  type='hidden' value='" + insertar + "'>");
					$("#Divresponsablestopes").append("<input id='htmlresponsable" + codRespGlobal + "' type='hidden' value='" + $("#divtabletopes_" + codRespGlobal).html() + "'>");
					$("#Divresponsablestopes").append("<input id='htmlresponsablevector" + codRespGlobal + "' type='hidden' >");

				}
				DivTopes.dialog('destroy');
			}
			else {
				alert("faltan campos por llenar");
			}



		}
		else {
			if (cumplevalidacion == 'si') {
				//alert("es preadmision 1");
				//alert($.trim($("#pac_doctxtNumDoc").val()));
				//alert($("#pac_tdoselTipoDoc").val());

				/****************************************************************
				 * Agosto 15 de 2013
				 *
				 * Si está activo preadmisión se guarda el dato como preadmisión
				 ****************************************************************/



				$.post("admision_erp.php",
					{
						accion: "insertarTopes",
						consultaAjax: '',
						whistoria: historia,
						wingreso: ingreso,
						responsable: codRespGlobal,
						wemp_pmla: $('#wemp_pmla').val(),
						tema: $('#tema').val,
						insertar: insertar,
						activo: activo,
						esadmision: esadmision,
						documento: $.trim($("#pac_doctxtNumDoc").val()),
						tipodocumento: $("#pac_tdoselTipoDoc").val()

					}, function (data) {
						//alert("grabacion exitosa");
						//alert(data);
						//alert(data.sql);
						DivTopes.dialog('destroy');
					});
			}
			else {

				alert("Faltan campos por llenar");

			}

		}





	}
	else {
		// historia vacia y es admision  ( osea que no hay una admision en la tabla 101)


		if (historia == '') {

			if (cumplevalidacion == 'si') {


				$("#divtabletopes_" + codRespGlobal + "  input ").each(function () {

					if ($(this).attr('type') == "checkbox") {

						if ($(this).attr('checked')) {
							if (!objglobal[codRespGlobal])
								objglobal[codRespGlobal] = {};

							objglobal[codRespGlobal][$(this).attr('id')] = 'on';
						}
					}
					else {
						if ($(this).attr('type') != "button") {
							if ($(this).val() != '') {
								if (!objglobal[codRespGlobal])
									objglobal[codRespGlobal] = {};

								objglobal[codRespGlobal][$(this).attr('id')] = $(this).val();
								//alert($(this).attr('id')+"----"+$(this).val());
							}
						}
					}
				});


				$("#divtabletopes_" + codRespGlobal + "  select ").each(function () {
					if (!objglobal[codRespGlobal])
						objglobal[codRespGlobal] = {};

					objglobal[codRespGlobal][$(this).attr('id')] = $(this).val();
				});



				if ($("#topeGrabar_" + codRespGlobal).length == 0) {

					$("#Divresponsablestopes").append("<input class='grabarCuandoAdmite'  responsable='" + codRespGlobal + "' id='topeGrabar_" + codRespGlobal + "'  type='hidden' value='" + insertar + "'>");
					$("#Divresponsablestopes").append("<input id='htmlresponsable" + codRespGlobal + "' type='hidden' value='" + $("#divtabletopes_" + codRespGlobal).html() + "'>");
					$("#Divresponsablestopes").append("<input id='htmlresponsablevector" + codRespGlobal + "' type='hidden' >");

				}
				else {

					$("#topeGrabar_" + codRespGlobal).remove()
					$("#htmlresponsable" + codRespGlobal).remove();
					$("#htmlresponsablevector" + codRespGlobal).remove();
					$("#Divresponsablestopes").append("<input class='grabarCuandoAdmite'  responsable='" + codRespGlobal + "' id='topeGrabar_" + codRespGlobal + "'  type='hidden' value='" + insertar + "'>");
					$("#Divresponsablestopes").append("<input id='htmlresponsable" + codRespGlobal + "' type='hidden' value='" + $("#divtabletopes_" + codRespGlobal).html() + "'>");
					$("#Divresponsablestopes").append("<input id='htmlresponsablevector" + codRespGlobal + "' type='hidden' >");


				}
				DivTopes.dialog('destroy');
			}
			else {
				alert("Faltan campos por llenar");
			}

		}
		else {


			if (cumplevalidacion == 'si') {
				//alert("entrooo por aquiiiii");

				/****************************************************************
				 * Agosto 15 de 2013
				 *
				 * Si está activo preadmisión se guarda el dato como preadmisión
				 ****************************************************************/

				$.post("admision_erp.php",
					{
						accion: "insertarTopes",
						consultaAjax: '',
						whistoria: historia,
						wingreso: ingreso,
						responsable: codRespGlobal,
						wemp_pmla: $('#wemp_pmla').val(),
						tema: $('#tema').val,
						insertar: insertar,
						activo: activo,
						esadmision: esadmision,
						documento: $.trim($("#pac_doctxtNumDoc").val()),
						tipodocumento: $("#pac_tdoselTipoDoc").val()

					}, function (data) {
						//alert("grabacion exitosa");
						DivTopes.dialog('destroy');
					});
			}
			else {

				alert("Faltan campos por llenar");

			}

		}

	}

}
function recalculartopes2() {
	var historia = ($("#ing_histxtNumHis").val());
	var ingreso = ($("#ing_nintxtNumIng").val());

	if ($("#spEstPac").text() == 'ACTIVO') {
		var activo = 'on';
	}
	else
		var activo = 'off';

	if (activo == 'on') {

		$.post("admision_erp.php",
			{
				accion: "recalculartopes2",
				consultaAjax: '',
				whistoria: historia,
				wingreso: ingreso,
				responsable: $("#responsabletope").val(),
				wemp_pmla: $('#wemp_pmla').val(),
				tema: $('#tema').val,
				activo: activo

			}, function (data) {


				$("#div_recalculartopes").html("");
				$("#div_recalculartopes").html(data);
				//$("#div_recalculartopes").show(data);
				//alert(data);


			}).done(function () {
				$.blockUI(
					{
						message: $("#div_contenedor"),
						css: {
							top: ($(window).height() * 0.5) / 5 + 'px',
							left: ($(window).width() * 0.5) / 5 + 'px',
							width: "50%",
							heightStyle: "content",
							textAlign: "left",
							cursor: ""
						}
					}
				);
				var i = 0;
				//alert(i);

				$(".tdrecalculartope").each(function (i) {
					//alert($(".tdrecalculartope").length);
					i++;
					var idcargo = $(this).attr('idcargo');
					var responsable = $(this).attr('responsable');
					var tipoingreso = $(this).attr('tipoingreso');
					var tipopaciente = $(this).attr('tipopaciente');
					var centrocostos = $(this).attr('centrocostos');
					var log = $(this).attr('log');
					var permitir = $(this).attr('pergrabarcargo');

					//alert(idcargo+'---'+responsable+'---'+tipoingreso+'---'+tipopaciente+'---'+centrocostos+'---'+log+'---'+permitir);

					// alert("entro");
					$.post("admision_erp.php",
						{
							consultaAjax: '',
							wemp_pmla: $('#wemp_pmla').val(),
							tema: $('#tema').val,
							accion: 'regrabarCargo',
							idCargo: idcargo,
							responsble: responsable,
							tipoIngreso: tipoingreso,
							tipoPaciente: tipopaciente
						}, function (data) {

							if (data.Regrabado) {
								//alert("idCargo:"+idcargo+" "+data.MsjGrabado+" "+data.MsjAnulado);
							}
							else {
								alert(" Error en el cargo " + idcargo + " no se pueden actualizar los topes");
							}

							if (i == $(".tdrecalculartope").length) {
								$.unblockUI();
							}

						}, 'json');


				});

			});
	}
	else {
		alert("paciente inactivo no puedo re calcular topes");
	}


}


function mostrarDivTopes(codResp, obj) {
	// recibe el id del campo que tiene el cod del responsable


	codRespGlobal = $("#" + codResp).val();
	idCodResp = codResp;
	//si ya se habia abierto antes el div de topes y habian digitado algo

	if ($("#" + codResp).val() != '') {
		var esadmision = 'si';
		if ($("[name=radPreAdmi]:checked").val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR') {
			esadmision = 'no';
		}

		$.post("admision_erp.php",
			{
				consultaAjax: '',
				accion: 'mostrarTopes',
				wbasedato: $("#wbasedato").val(),
				id: codResp,
				responsable: codRespGlobal,
				wemp_pmla: $("#wemp_pmla").val(),
				historia: $("#ing_histxtNumHis").val(),
				ingreso: $("#ing_nintxtNumIng").val(),
				documento: $.trim($("#pac_doctxtNumDoc").val()),
				tipodocumento: $("#pac_tdoselTipoDoc").val(),
				esadmision: esadmision


			}
			, function (data) {
				//alert("nada");
				if (data.error == 1 && data.mensaje != '') {
					alert(data.mensaje);
				}
				else {

					if ($("[name=radPreAdmi]:checked").val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR') {
						if ($("input[name='btRegistrarActualizar']").val() == 'Preadmitir') {

							if ($("#htmlresponsable" + codRespGlobal).length == 0) {
								//alert("abro nuevo");
								$("#div_topes").html("");
								$("#div_topes").append(data.html);
							}
							else {
								//alert("abro existente");
								$("#div_topes").html("");
								$("#div_topes").append("<div id='divtabletopes_" + codRespGlobal + "'>" + $("#htmlresponsable" + codRespGlobal).val() + "</div>");
								$("#divtabletopes_" + codRespGlobal + "  input ").each(function () {




									if ($(this).attr('type') == "checkbox") {
										//alert($( this ).val( 'type' ))
										if (objglobal[codRespGlobal][$(this).attr('id')]) {
											$(this).attr('checked', true);
											// $(this).val(objglobal[codRespGlobal][ $(this).attr('id') ]);
										}
									}
									else {

										if (objglobal[codRespGlobal][$(this).attr('id')]) {
											//alert("entro pr aqui");
											$(this).val(objglobal[codRespGlobal][$(this).attr('id')]);
										}
									}
								});

								$("#divtabletopes_" + codRespGlobal + "  select ").each(function () {

									if (objglobal[codRespGlobal][$(this).attr('id')]) {
										$(this).val(objglobal[codRespGlobal][$(this).attr('id')]);
									}

								});
							}
						}
						else {

							$("#div_topes").html("");
							$("#div_topes").append(data.html);


						}

					}
					else {
						if ($("#ing_histxtNumHis").val() == '') {

							if ($("#htmlresponsable" + codRespGlobal).length == 0) {
								//alert("abro nuevo");
								$("#div_topes").html("");
								$("#div_topes").append(data.html);
							}
							else {
								//alert("abro existente");
								$("#div_topes").html("");
								$("#div_topes").append("<div id='divtabletopes_" + codRespGlobal + "'>" + $("#htmlresponsable" + codRespGlobal).val() + "</div>");
								$("#divtabletopes_" + codRespGlobal + "  input ").each(function () {




									if ($(this).attr('type') == "checkbox") {
										//alert($( this ).val( 'type' ))
										if (objglobal[codRespGlobal][$(this).attr('id')]) {
											$(this).attr('checked', true);
											// $(this).val(objglobal[codRespGlobal][ $(this).attr('id') ]);
										}
									}
									else {

										if (objglobal[codRespGlobal][$(this).attr('id')]) {
											//alert("entro pr aqui");
											$(this).val(objglobal[codRespGlobal][$(this).attr('id')]);
										}
									}
								});

								$("#divtabletopes_" + codRespGlobal + "  select ").each(function () {

									if (objglobal[codRespGlobal][$(this).attr('id')]) {
										$(this).val(objglobal[codRespGlobal][$(this).attr('id')]);
									}

								});
							}
						}
						else {

							$("#div_topes").html("");
							$("#div_topes").append(data.html);
						}
					}


					//buscarCcoTopes('tabla_topes');
					//buscarClasificacionConceptosFac('tabla_topes');

					//-----
					var w = $(window).width();
					var h = $(window).height();
					$('html, body').animate({ scrollTop: 0 });
					//-----------------
					sleep(500).then(() => {
						$('#div_topes').dialog({
							width: w,
							height: h,
							dialogClass: 'fixed-dialog',
							modal: true,
							title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;INGRESO DE TOPES Y RECONOCIDOS',
							close: function (event, ui) {
								//limpiarPantalla();
							},
							buttons: {
								"Guardar": function () {
									guardartopes(codRespGlobal, $(this));


								},
								"Salir sin guardar": function () {

									$(this).dialog("destroy");
								}
							}
						});


						$(".datepickertopes").removeClass('hasDatepicker');
						$(".datepickertopes").datepicker({
							closeText: 'Cerrar',
							prevText: 'Antes',
							nextText: 'Despues',
							monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
								'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
							monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
								'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
							dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
							dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
							dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
							weekHeader: 'Sem.',
							dateFormat: 'yy-mm-dd',
							yearSuffix: ''
						});


						$(".tablaTopes").find(".valortope").on({
							keyup: function () {
								$(this).val($(this).val().replace(/[^0-9]/, ""));
								num = $(this).val().replace(/\,/g, '');
								num = num.replace(/\./g, '');
								num = num.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g, '$1,');
								num = num.split('').reverse().join('').replace(/^[\,]/, '');
								$(this).val(num);
							}
						});

						$(".tablaTopes").find(".portope").on({
							keyup: function () {
								$(this).val($(this).val().replace(/[^0-9]/, ""));
								if ($(this).val().length > 3) {
									$(this).val($(this).val().substring(0, $(this).val().length - 1));
								}
								else if ($(this).val().length == 3) {
									if ($(this).val() * 1 > 100) {
										$(this).val('100');
									}
								}
							}
						});

						//---------------------
						$(".validaciontope").keyup(function () {
							validacionTopeGeneral();
						});
						$(".validaciontope").click(function () {
							if ($(this).attr('Type') == 'checkbox')
								validacionTopeGeneral();
						});

						//--------------------
						$(".validaciontope2").keyup(function () {
							validacionTopexConcepto($(this));
						});
						$(".validaciontope2").click(function () {
							if ($(this).attr('Type') == 'checkbox')
								validacionTopexConcepto();
						});


						//-----------
						$(".validaciontope3").keyup(function () {
							validacionTopexClasificacion($(this));
						});
						$(".validaciontope3").click(function () {
							if ($(this).attr('Type') == 'checkbox')
								validacionTopexClasificacion();
						});


						//
					});

					$(".trtopeppal").each(function () {
						var tope = $(this).attr("codigotope");
						if ($(".detalletope_" + tope).length == 0) {
							//alert("entro"+tope);
							$("#detallartope_" + tope).hide();
						}
					});

				}
			},
			"json"
		).done(function () {
			//--recorrer los inputs si tienen un dato realizar  validacion para bloquear los inputs donde no se puede grabar
			var validaciongeneral = false;
			$(".validaciontope").each(function () {
				if ($(this).val() != '') {
					if ($(this).attr('type') == 'checkbox') {
						if ($(this).attr('checked')) {
							validaciongeneral = true;
							return false;
						}

					}
					else {
						validaciongeneral = true;
						return false;
					}

				}
			});


			if (validaciongeneral) {
				validacionTopeGeneral();
			}
			else {

				var validacionesTopexClasificacion = false;
				$(".validaciontope3").each(function () {
					if ($(this).val() != '') {
						if ($(this).attr('type') == 'checkbox') {
							if ($(this).attr('checked')) {
								validacionesTopexClasificacion = true;
								return false;
							}

						}
						else {
							validacionesTopexClasificacion = true;
							return false;
						}

					}
				});

				if (validacionesTopexClasificacion) {

					$(".validaciontope3").each(function () {
						if ($(this).val() != '') {
							validacionTopexClasificacion($(this));
						}
					});
				}
				else {

					$(".validaciontope2").each(function () {
						if ($(this).val() != '') {
							validacionTopexConcepto($(this));
						}
					});
				}


			}

		});
	}
	else {
		alert(" ** Debe ingresar el nombre de la Aseguradora");
	}

}

//--- Valida los inputs del tope principal (tipo concepto '*-todas' ,  clasificacion '*-Todas' )
function validacionTopeGeneral() {

	var quito = 'no';
	$(".validaciontope").each(function () {
		//alert($(this).val());
		if ($(this).val() == '') {


		}
		else {

			if ($(this).attr('type') == "checkbox") {
				if ($(this).attr('checked')) {
					//alert($(this).val());
					// desabilito los otros detalles
					$(".tdminimo").addClass('disabler');
					$(".tdmedio").addClass('disabler');
					$(".validaciontope2").attr('disabled', 'disabled');
					$(".validaciontope3").attr('disabled', 'disabled');

					quito = 'si';
					return;

				}

			}
			else {
				$(".tdminimo").addClass('disabler');
				$(".tdmedio").addClass('disabler');
				$(".validaciontope2").attr('disabled', 'disabled');
				$(".validaciontope3").attr('disabled', 'disabled');

				quito = 'si';
				return;
			}
		}
	})
	//alert(quito)
	if (quito == 'no') {
		$(".tdminimo").removeClass('disabler');
		$(".tdmedio").removeClass('disabler');
		$(".validaciontope2").removeAttr('disabled');
		$(".validaciontope3").removeAttr('disabled');

	}

}
//--- Valida los inputs del tope(tipo concepto 'xxxxxx-especifica' ,  clasificacion '*-Todas' )
function validacionTopexConcepto(elementoinput) {


	var codigo = elementoinput.attr('attrvalor');
	var quito = 'no';
	$(".validaciontope2_" + codigo).each(function () {
		if ($(this).val() == '') {


		}
		else {
			if ($(this).attr('type') == "checkbox") {
				if ($(this).attr('checked')) {
					$(".tdminimo_" + codigo).addClass('disabler');
					$(".tdgeneral").addClass('disabler');
					$(".validaciontope3_" + codigo).attr('disabled', 'disabled');
					$(".validaciontope").attr('disabled', 'disabled');
					//validaciontope3_
					quito = 'si';
					return;
				}

			}
			else {
				$(".tdminimo_" + codigo).addClass('disabler');
				$(".tdgeneral").addClass('disabler');
				$(".validaciontope3_" + codigo).attr('disabled', 'disabled');
				$(".validaciontope").attr('disabled', 'disabled');

				quito = 'si';
				return;

			}
		}

	});

	if (quito == 'no') {

		$(".tdminimo_" + codigo).removeClass('disabler');
		$(".validaciontope3_" + codigo).removeAttr('disabled');
		// para ver si no hay ningun campo lleno en toda la tabla
		var quitodos = 'no';
		$(".validaciontope2").each(function () {
			if ($(this).val() == '') {


			}
			else {
				if ($(this).attr('type') == "checkbox") {
					if ($(this).attr('checked')) {
						quitodos = 'si';
					}
				}
				else {
					quitodos = 'si';
				}
			}

		});
		if (quitodos == 'no') {
			$(".tdgeneral").removeClass('disabler');
			$(".validaciontope").removeAttr('disabled');
		}


	}

}

//--- Valida los inputs del tope (tipo concepto 'xxxx' ,  clasificacion 'xxxxx' )
function validacionTopexClasificacion(elementoxclasificacion) {
	var codigo = elementoxclasificacion.attr('attrvalor');
	var quito = 'no';
	$(".validaciontope3_" + codigo).each(function () {
		//alert($(this).val());
		if ($(this).val() == '') {


		}
		else {

			if ($(this).attr('type') == "checkbox") {
				if ($(this).attr('checked')) {
					//alert($(this).val());
					// desabilito los otros detalles
					$(".tdgeneral").addClass('disabler');
					$(".tdmedio_" + codigo).addClass('disabler');
					$(".validaciontope2_" + codigo).attr('disabled', 'disabled');
					$(".validaciontope").attr('disabled', 'disabled');

					quito = 'si';
					return;

				}

			}
			else {
				$(".tdgeneral").addClass('disabler');
				$(".tdmedio_" + codigo).addClass('disabler');
				$(".validaciontope2_" + codigo).attr('disabled', 'disabled');
				$(".validaciontope").attr('disabled', 'disabled');

				quito = 'si';
				return;
			}
		}
	})
	//alert(quito)
	if (quito == 'no') {

		$(".tdmedio_" + codigo).removeClass('disabler');
		$(".validaciontope2_" + codigo).removeAttr('disabled');

		var quitodos = 'no';
		$(".validaciontope3").each(function () {
			if ($(this).val() == '') {


			}
			else {
				if ($(this).attr('type') == "checkbox") {
					if ($(this).attr('checked')) {
						quitodos = 'si';
					}
				}
				else {
					quitodos = 'si';
				}
			}

		});

		if (quitodos == 'no') {
			$(".tdgeneral").removeClass('disabler');
			$(".validaciontope").removeAttr('disabled');
		}

	}



}

function addFila(tabla_referencia, wbasedato, wemp_pmla) {
	if (tabla_referencia == "tabla_topes")
		// alert("entro"+tabla_referencia);
		var ubicacion = "";

	if (tabla_referencia == "tabla_eps") {
		accion = 'adicionar_fila';
		// para saber si la tabla tiene filas o no
		trs = $("#" + tabla_referencia).find('tr[id$=tr_' + tabla_referencia + ']').length;
	}
	else {
		accion = 'adicionar_fila_tope';
		// para saber si la tabla tiene filas o no, toca decirle a que div-contatenado con la asiguradora pertenece
		trs = $("#" + tabla_referencia, $("#div_cont_tabla_topes" + idCodResp)).find('tr[id$=tr_' + tabla_referencia + ']').length;
	}
	// para saber si la tabla tiene filas o no
	// trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;
	var value_id = 0;

	//busca consecutivo mayor
	if (trs > 0) {
		id_mayor = 0;
		// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
		if (tabla_referencia == "tabla_eps") {
			ubicacion = "#" + tabla_referencia;
		}
		else {
			// ubicacion = "#"+tabla_referencia,$("#div_cont_tabla_topes"+idCodResp);
			ubicacion = "#div_cont_tabla_topes" + idCodResp + " #" + tabla_referencia;
		}
		$(ubicacion).find('tr[id$=tr_' + tabla_referencia + ']').each(function () {
			id_ = $(this).attr('id');
			id_splt = id_.split('_');
			id_this = (id_splt[0]) * 1;
			if (id_this >= id_mayor) {
				id_mayor = id_this;
			}
		});
		id_mayor++;
		value_id = id_mayor + '_tr_' + tabla_referencia;

	}
	else { value_id = '1_tr_' + tabla_referencia; }

	$.ajax(
		{
			url: "admision_erp.php",
			context: document.body,
			type: "POST",
			data: {
				accion: accion,
				consultaAjax: '',
				id_fila: value_id,
				tabla_referencia: tabla_referencia,
				wbasedato: wbasedato,
				wemp_pmla: wemp_pmla

			},
			async: false,
			dataType: "json",
			success: function (data) {
				if (data.error == 1) {
					if (data.mensaje != '') {
						alerta(data.mensaje);
					}
				}
				else {	 // alert(data.html);
					if (tabla_referencia == "tabla_eps") {
						$("#" + tabla_referencia + " > tbody").append(data.html);
						buscarAseguradoras('tabla_eps');

						marcarAqua("#" + value_id, 'msgError', 'campoRequerido');
						resetAqua($("#" + value_id));
						reOrdenarResponsables();
						$("#tabla_eps > tbody").sortable("refresh");
						//console.log("adicionando fila")
						$("#tabla_eps > tbody").find("input[type='radio'][name='res_comtxtNumcon']").eq(0).attr("checked", false);
						$("#tabla_eps > tbody").find("input[type='radio'][name='res_comtxtNumcon']").eq(0).parent().hide();
					}
					else {
						// alert("div_cont_tabla_topes"+idCodResp);
						$("#" + tabla_referencia + " > tbody", $("#div_cont_tabla_topes" + idCodResp)).append(data.html);
						buscarClasificacionConceptosFac('tabla_topes');
						buscarCcoTopes('tabla_topes');
						//se llena el hidden de responsable para mandarlo con el array
						// $("[name=top_reshidTopRes]", $( "#"+value_id ) ).val($("#"+codResp).val());

						$("[name=top_reshidTopRes]", $("#" + value_id, $("#div_cont_tabla_topes" + idCodResp))).val(codRespGlobal);
					}

				}
			}
		});
}

function removerFila(id_fila, wbasedato, tabla_referencia) {
	var wemp_pmla = $("#wemp_pmla").val();
	var id_eliminar = $("#" + id_fila + "_bd").val();

	if (tabla_referencia == "tabla_eps" && $("tr[id$=tr_tabla_eps]").length == 1) {
		alert(" ** Debe existir al menos un responsable");
		return;
	}


	acc_confirm = 'Confirma que desea eliminar?';
	if (confirm(acc_confirm)) {
		if (id_eliminar != '') {
			$.post("admision_erp.php",
				{
					accion: 'eliminar_planes',
					consultaAjax: '',
					id_eliminar: id_eliminar,
					wbasedato: wbasedato
				},
				function (data) {
					if (data.error == 1) {
						if (data.mensaje != '') {
							alerta(data.mensaje);
						}
					}
					else {
						if (data.mensaje != '') {
							alerta(data.mensaje);
							$("#" + id_fila).empty();
							$("#" + id_fila).remove();

						}
					}
				},
				"json"
			);
		}
		else {
			if (tabla_referencia == 'tabla_topes') {
				$("#div_cont_tabla_topes" + idCodResp).find("#" + id_fila).remove();
			} else {
				$("#" + tabla_referencia).find("#" + id_fila).remove();
			}

			if (tabla_referencia == "tabla_eps") {
				reOrdenarResponsables(); //Para ordenar R1,R2,R3
			}
		}
	}
	//}

	if (id_fila == "1_tr_" + tabla_referencia && tabla_referencia == "tabla_topes") {
		addFila(tabla_referencia, wbasedato, wemp_pmla)
	}
}

function buscarClasificacionConceptosFac(tabla_referencia) {
	var wbasedato = $("#wbasedato").val();
	var wemp_pmla = $("#wemp_pmla").val();

	if (tabla_referencia != "") {
		// para saber si la tabla tiene filas o no
		trs = $("#" + tabla_referencia, $("#div_cont_tabla_topes" + idCodResp)).find('tr[id$=tr_' + tabla_referencia + ']').length;
		var value_id = 0;

		//busca consecutivo mayor
		if (trs > 0) {
			id_mayor = 0;
			// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
			$("#" + tabla_referencia, $("#div_cont_tabla_topes" + idCodResp)).find('tr[id$=tr_' + tabla_referencia + ']').each(function () {
				id_ = $(this).attr('id');
				id_splt = id_.split('_');
				id_this = (id_splt[0]) * 1;
				if (id_this >= id_mayor) {
					id_mayor = id_this;
				}
			});
			// id_mayor++;
			value_id = id_mayor + '_tr_' + tabla_referencia;

		}
		else { value_id = '1_tr_' + tabla_referencia; }

		codEsp = "#top_clatxtClaTop" + value_id;
	}
	//Asigno autocompletar para la busqueda de paises
	$("[id^=top_clatxtClaTop]", $("#div_cont_tabla_topes" + idCodResp)).autocomplete("admision_erp.php?consultaAjax=&accion=consultarClasificacinConceptos&tipo=02&wbasedato=" + wbasedato + "&wemp_pmla=" + wemp_pmla,
		{
			extraParams: {
				tipo: function (campo) {
					//para buscar el valor del select de la fila
					var tr = conFocus.parentNode.parentNode;
					// var tdd= $( "#"+ $( conFocus ).prop("tagName"));
					var valor = $("select", tr).val();
					return valor;
				}
			},
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 1,
			cacheLength: 0,
			json: "json",
			formatItem: function (data, i, n, value) {

				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				//convierto el string en json
				eval("var datos = " + data);

				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {
				// //La respuesta es un json
				// //convierto el string en formato json
				eval("var datos = " + item);

				//Guardo el ultimo valor que selecciona el usuario
				//Esto en una propiedad inventada
				this.value = datos[0].valor.des;
				this._lastValue = datos[0].valor.des;
				this._lastCodigo = datos[0].valor.cod;

				$(this).removeClass("campoRequerido");

				$("input[type=hidden]", this.parentNode).val(datos[0].valor.cod);
			}
		).focus(function () {
			conFocus = this;
		}).on({

			change: function () {
				var cmp = this;

				if (cmp._lastValue && cmp._lastCodigo) {
					setTimeout(function () {
						//Pregunto si la pareja es diferente
						if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
							|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
						) {
							alerta(" Digite una Clasificación válida")
							$("input[type=hidden]", cmp.parentNode).val('');
							cmp.value = '';
							cmp.focus();
							// cmp.blur();
						}
					}, 500);
				} else {
					alerta(" Digite una Clasificación válida")
					$("input[type=hidden]", cmp.parentNode).val('');
					cmp.value = '';
				}


				// if( this._lastValue ){
				// this.value = this._lastValue;
				// }
				// else{
				// this.value = "";
				// }
			}
		})
		;
}

function salir(div, noesblockui) {
	if (noesblockui == undefined)
		noesblockui = false;

	var alMenosUno = false;
	if ($("#" + div).find(":input").length > 0) {
		$("#" + div).find(":input:not(:hidden,:button)").each(function () {
			if ($(this).is(':checkbox')) {
				if ($(this).is(':checked')) {
					alMenosUno = true;
					return false;
				}
			}
			else if ($(this).prop("tagName") == "SELECT") {
				if ($(this).val() != '') {
					alMenosUno = true;
					return false;
				}
			}
			else {
				var valorx = $(this).val();
				if (isEmpty(valorx) == true) {
					valorx = $(this).text();
				}
				if (isEmpty(valorx) == false) {
					alMenosUno = true;
					return false;
				}
			}
		});
	}

	if (alMenosUno == false) {
		// alert("no hay nada!");
		//---$.unblockUI();
	}
	else {
		if (confirm('La información nueva ingresada no se tomará en cuenta')) {
			// var nuevaInfoDivTablaTopes =$( "#div_cont_tabla_topes"+idCodResp ).find( $("#"+div).html() );
			// var nuevaInfoDivTablaTopes = $("#"+div).html();
			var nuevaInfoDivTablaTopes = cearUrlPorCamposJson($("#" + div)[0], 'id');
			// if ($( "#div_cont_tabla_topes"+idCodResp )[0].anteriorHTML != nuevaInfoDivTablaTopes)
			if ($("#" + div)[0].anteriorJSON && $("#" + div)[0].anteriorJSON != nuevaInfoDivTablaTopes) {
				$("#" + div)[0].innerHTML = $("#" + div)[0].anteriorHTML;
				setDatos($("#" + div)[0].anteriorJSON, $("#" + div), 'id');
			}
			else {
				if (div.match(/div_cont_tabla_topes/g)) {
					//console.log("A TOPES NO SE LE QUITA NADA");
				} else {
					//console.log("SE LE QUITA TODO");
					$("input:not([type=button],[name=top_reshidTopRes]),select", $("#" + div)).val('');
				}
			}
			//---$.unblockUI();
		}
	}
	$.unblockUI();

}

function isEmpty(obj) {
	if (typeof obj == 'undefined' || obj === null || obj === '') return true;
	if (typeof obj == 'number' && isNaN(obj)) return true;
	if (obj instanceof Date && isNaN(Number(obj))) return true;
	return false;
}

function guardarTopePorResp() {
	// se le manda al div_contenedor_topes lo que hay en div_topes y dentro de eso el div que empieza en div_cont_tabla_topes
	try {
		// div que contiene la tabla de topes ya existia, miro que tiene
		if ($("#div_cont_tabla_topes" + idCodResp).length > 0) {
			//Guardo la informacion en el mismo div
			// var auxDiv = $( "[id^=div_cont_tabla_topes]", $( "#div_topes" ) );
			var auxDiv = $("#div_cont_tabla_topes" + idCodResp);
			auxDiv[0].anteriorHTML = auxDiv[0].innerHTML;
			auxDiv[0].anteriorJSON = cearUrlPorCamposJson(auxDiv[0], 'id');
		}

		$("#div_contenedor_topes")[0].appendChild($("[id^=div_cont_tabla_topes]", $("#div_topes"))[0])


	}
	catch (e) { }


	$.unblockUI();

}

function consultarClientesEspeciales() {

	var wbasedato = $("#wbasedato").val();
	var cedula = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc = $("#pac_tdoselTipoDoc").val();

	if (cedula == "" || tipoDoc == "")
		return;

	$.post("admision_erp.php",
		{
			wbasedato: wbasedato,
			consultaAjax: '',
			accion: 'consultaClientes',
			cedula: cedula,
			tipoDoc: tipoDoc

		}, function (data) {
			if (data.error == 1) {
				if (data.mensaje != '') {
					alerta(data.mensaje);
				}
			}
			else {
				if (data.mensaje != '') {
					$("#div_mensaje_PerEspeciales").css("display", "");
					$("#div_mensaje_PerEspeciales").html(data.mensaje)  // update Ok.
					$("#div_mensaje_PerEspeciales").parent().show('blind', {}, 500);
					$("#div_mensaje_PerEspeciales").effect("pulsate", {}, 10000);
				}
			}
		},
		"json"

	);
}

function consultarSiActivo() {

	var wbasedato = $("#wbasedato").val();
	var cedula = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc = $("#pac_tdoselTipoDoc").val();

	if (tipoDoc == "" || cedula == "")
		return;

	$.post("admision_erp.php",
		{
			wbasedato: wbasedato,
			consultaAjax: '',
			accion: 'consultarSiActivo',
			wemp_pmla: $("#wemp_pmla").val(),
			cedula: cedula,
			tipoDoc: tipoDoc

		}, function (data) {
			if (data.error == 1) {
				if (data.mensaje != '')
					alert(data.mensaje);
				$("#pac_doctxtNumDoc").val("");
				resetAqua($("#pac_doctxtNumDoc").parent());
			}
			else {
				if (data.mensaje != '') {
					alerta(data.mensaje);
				}
				$("#ing_histxtNumHis").val(data.his);
				$("#ing_nintxtNumIng").val(data.ing);

				var estPac = data.estado;
				if (estPac == 'off') {
					estPac = "INACTIVO";
					$("#spEstPac").addClass("estadoInactivo");
				}
				else {
					estPac = "ACTIVO";
					$("#spEstPac").addClass("estadoActivo");
				}
				$("#spEstPac").html(estPac); //se envia Estado del paciente
			}
		},
		"json"
	);
}

function consultarSiRechazado() {

	var wbasedato = $("#wbasedato").val();
	var cedula = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc = $("#pac_tdoselTipoDoc").val();
	var centroCostos = $("#ing_seisel_serv_ing").val();

	if (tipoDoc == "" || cedula == "")
		return;

	$.post("admision_erp.php",
		{
			wbasedato: wbasedato,
			consultaAjax: '',
			accion: 'consultarSiRechazado',
			wemp_pmla: $("#wemp_pmla").val(),
			cedula: cedula,
			tipoDoc: tipoDoc,
			wcco: centroCostos

		}, function (data) {
			if (data.error == 1) {
				if (data.mensaje != '')
					alerta(data.mensaje);
				$("#pac_doctxtNumDoc").val("");
				resetear();
				resetAqua($("#pac_doctxtNumDoc").parent());
			}
			else {
				if (data.mensaje != '') {
					alerta(data.mensaje);
				}
				$("#ing_histxtNumHis").val(data.his);
				$("#ing_nintxtNumIng").val(data.ing);
			}
		},
		"json"
	);
}

function valNumero(id) {
	$("#" + id).on({
		keypress: function (e) {
			var key = e.keyCode || e.which;

			if (key != 9 && key != 8) {
				if (String.fromCharCode(key).search(/[0-9]/g) == -1) {
					e.preventDefault();
				}
			}
		}
	});
}

function valPorcentaje(obj) {
	var valor = $(obj).val();
	if ($.trim(valor) == "") {
		$(obj).val("100");
	}
	if (valor < 0 || valor > 100) {
		alert(" ** Debe ingresar un valor de entre 0 y 100");
		$(obj).val("");
	}
}

function valRepetidosTopes(id_tr) {

	id_tr_splt = id_tr.split('.');
	id_tr = (id_tr_splt[1]);

	//se pone el fondo de la fila en el color normal
	$("#" + id_tr, $("#div_cont_tabla_topes" + idCodResp)).css("background-color", "");

	var campo1 = $("#top_tcoselTipCon" + id_tr, $("#div_cont_tabla_topes" + idCodResp)).val();
	var campo2 = $("#top_clahidClaTop" + id_tr, $("#div_cont_tabla_topes" + idCodResp)).val();
	var campo3 = $("#top_ccohidCcoTop" + id_tr, $("#div_cont_tabla_topes" + idCodResp)).val();

	if (campo2 == '*') { campo2 = 'k' }
	if (campo3 == '*') { campo3 = 'k' }

	// alert(campo1+campo2+campo3);
	var buscar = campo1 + campo2 + campo3;

	$("#hdd_" + id_tr, $("#div_cont_tabla_topes" + idCodResp)).val(buscar);

	if ($("input:hidden[id^=hdd_][value = '" + buscar + "']", $("#div_cont_tabla_topes" + idCodResp)).length > 1) {
		alert(" ** Se encuentran valores repetidos de Tipo de Concepto, Clasificacion y Centro de Costo");
		$("#" + id_tr, $("#div_cont_tabla_topes" + idCodResp)).css("background-color", "yellow");
		$("#btnGuardarTopResp", $("#div_cont_tabla_topes" + idCodResp)).attr("disabled", "disabled");
	}
	else {
		$("#btnGuardarTopResp", $("#div_cont_tabla_topes" + idCodResp)).removeAttr("disabled");
	}
}

function agregarCUPS(tr_contenedor) {

	//console.log("agregar cups a "+tr_contenedor);
	var nomid = tr_contenedor;
	var nomtr = tr_contenedor;
	tr_contenedor = $("#" + tr_contenedor);

	var count = tr_contenedor.find("[name=ing_cactxtcups]").length;
	nomid += count;
	var input = "<div><input type='text' style='width:200px;' name='ing_cactxtcups' id='ing_cactxtcups" + nomid + "' class='reset'  msgError='Digite el Codigo o el nombre'>";
	input += "<input type='hidden' name='ing_cachidcups' id='ing_cachidcups" + nomid + "' >";
	input += "<input type='hidden' name='id_idcups' id='id_idcups" + nomid + "' >";
	input += "<img border='0' style='width:15;' src='../../images/medical/root/borrar.png' onClick='eliminarCup(this, \"ing_cactxtcups" + nomid + "\",\"ing_cachidcups" + nomid + "\" );'></div>";

	//console.log("addcups");
	//Se busca el td_contenedor de los cups
	var td_contenedor = tr_contenedor.find("[name=ing_cactxtcups]:eq(0)").parent();
	td_contenedor.append(input);

	buscarCUPS(nomtr, count);

}

function eliminarCup(obj, id_cup_auto, id_cup_hide) {
	obj = jQuery(obj);
	obj.parent().remove(); //Elimina el div que contiene todo lo relacionado al cup
}

function reOrdenarResponsables() {
	var ind = 1;
	$(".numeroresponsable:visible").each(function () {
		$(this).text("R" + ind);
		ind++;
		//var texto = $(this).text();
		//texto = texto.substring(1); //Para quitar la R
	});
}

function isJSON(data) {
	var isJson = false
	try {
		// this works with JSON string AND JSON object, not sure about others
		var json = $.parseJSON(data);
		isJson = typeof json === 'object';
	} catch (ex) {
		//console.error('data is not JSON');
	}
	return isJson;
}

var global_flag_medicos = true;

function buscarMedicos() {

	var wbasedato = $("#wbasedato").val();
	var wemp_pmla = $("#wemp_pmla").val();
	var filtraEspecialidadClinica = $("#filtraEspecialidadClinica").val();
	$("#ing_meitxtMedIng").autocomplete("admision_erp.php?consultaAjax=&accion=consultarMedico&wbasedato=" + wbasedato + "&filtraEspecialidadClinica=" + filtraEspecialidadClinica + "&wemp_pmla=" + wemp_pmla,
		{
			cacheLength: 1,
			delay: 300,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width: 250,
			autoFill: false,
			minChars: 3,
			json: "json",
			formatItem: function (data, i, n, value) {
				//convierto el string en json
				eval("var datos = " + data);
				return datos[0].usu;	//Eso es lo que se muestra al usuario
			},
			formatResult: function (data, value) {
				if (global_flag_medicos == true) {
					//Para ubicar la lista con los resultados encima del input y no debajo, si en 500milisegundos no hay lista, no funciona
					//porque con esta version de autocompletar no hay posibilidades de detectar cuando se despliega la lista
					global_flag_medicos = false;
					setTimeout(function () {
						if ($(".ac_results").length > 0) {
							var oldTop = $(".ac_results").offset().top;
							var newTop = oldTop - $(".ac_results").height() - 25;
							$(".ac_results").css("top", newTop);
						}
					}, 500);
				}
				//convierto el string en json
				eval("var datos = " + data);
				return datos[0].valor.des;
			}
		}).result(
			function (event, item) {
				eval("var datos = " + item);
				//Guardo el ultimo valor que selecciona el usuario
				//this.parentNode.parentNode El tr que contiene el input
				$("input[type=text]", this.parentNode.parentNode).eq(0).val(datos[0].valor.des).removeClass("inputblank");;
				this._lastValue = this.value;
				global_flag_medicos = true;
				$("input[type=hidden]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
				$("input[type=text]", this.parentNode.parentNode).removeClass("campoRequerido");
				//se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar
			}
		).on({
			change: function () {
				var cmp = this;
				global_flag_medicos = true;
				setTimeout(function () {
					//Pregunto si la pareja es diferente
					if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
						|| (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
					) {
						alerta(" Digite un m\u00e9dico v\u00e1lido")
						$("input[type=hidden]", cmp.parentNode).val('');
						cmp.value = '';
						cmp.focus();
					}
				}, 200);
			},
			keyup: function () {
				global_flag_medicos = true;
			}
		});
}


function isDate(controlName) {
	var isValid = true;
	var format = "yy-mm-dd";
	try {
		jQuery.datepicker.parseDate(format, controlName, null);
	}
	catch (error) {
		isValid = false;
	}

	return isValid;
}

function completarParticular(obj) {
	obj = jQuery(obj);

	if (obj.val() == "" || obj.val() == $.trim($("#pac_doctxtNumDoc").val())) {
		obj.val($.trim($("#pac_doctxtNumDoc").val()));
		var fila = obj.parent().parent();
		fila.find("[name=res_tdo]").val($("#pac_tdoselTipoDoc").val());
		var n1 = $("#pac_no1txtPriNom").val();
		var n2 = $("#pac_no2txtSegNom").val();
		var a1 = $("#pac_ap1txtPriApe").val();
		var a2 = $("#pac_ap2txtSegApe").val();
		fila.find("[name=res_nom]").val(n1 + " " + n2 + " " + a1 + " " + a2);
		resetAqua(fila);
	}
}

function ponerPreadmitirEnBotones() {
	mostarOcultarDatosPreadmisiones(false);
	$("input[name='btRegistrarActualizar']").val('Preadmitir');
}
//-----------------------------------------------------------------------------------------------
// --> 	Funcion que reliza el llamado para una nueva admision a un paciente que llego con turno
//-----------------------------------------------------------------------------------------------
function mostrarAdmisionDesdeTurno(turno, tipoDocumento, documento) {
	if ($("#puestoTrabajo").val() == "") {
		alert("Primero debe seleccionar su puesto de trabajo actual.");
		$("#puestoTrabajo").css("border-color", "red");
		return;
	}
	// --> Primero apago la alerta del llamado en el monitor
	$.post("admision_erp.php",
		{
			consultaAjax: '',
			accion: 'apagarAlertaDeLlamado',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,
			turno: turno
		}, function (respuesta) {

			// --> Abro la admision, con el tipo y documento del paciente.
			mostrarAdmision();
			setTimeout(function () {
				$("#pac_doctxtNumDoc").val(documento);
				$("#pac_tdoselTipoDoc").val(tipoDocumento);
				$("#numTurnoPaciente").html(turno);
				$("#numTurnoPaciente").attr("valor", turno);
				mostrarDatosPreadmision();
				consultarClientesEspeciales();
				consultarSiActivo();
				consultarSiRechazado();
				verificarTriageUrgencias();
			}, 500);
		});
}

function mostrarAdmisionDesdeAgendaMedica() {//2016-02-26

	var tipoDocumento = $("#TipoDocumentoPacAm").val();
	var documento = $("#DocumentoPacAm").val();
	var turno = $("#TurnoEnAm").val();

	mostrarAdmision();
	setTimeout(function () {
		$("#pac_doctxtNumDoc").val(documento);
		$("#pac_tdoselTipoDoc").val(tipoDocumento);
		$("#numTurnoPaciente").html(turno);
		$("#numTurnoPaciente").attr("valor", turno);
		mostrarDatosPreadmision();
		consultarClientesEspeciales();
		consultarSiActivo();
		consultarSiRechazado();
	}, 500);
}
//-----------------------------------------------------------------------
// --> Funcion que genera el llamado del paciente para que sea atendido
//-----------------------------------------------------------------------
function llamarPacienteAtencion(turno, elemento) {
	if ($("#puestoTrabajo").val() == "") {
		alert("Primero debe seleccionar su puesto de trabajo actual.");
		$("#puestoTrabajo").css("border-color", "red");
		return;
	}

	$.post("admision_erp.php",
		{
			consultaAjax: '',
			accion: 'llamarPacienteAtencion',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,
			turno: turno,
			ventanilla: $("#puestoTrabajo").val()
		}, function (respuesta) {
			if (respuesta.Error) {
				alert(respuesta.Mensaje);
				$(".botonLlamarPaciente").show();
				$(".botonColgarPaciente").hide();
				$("#botonAdmitir" + turno).hide();
				//$("#imgLlamar"+turno).hide();
				$("#trTurno_" + turno).attr("class", $("#trTurno_" + turno).attr("classAnterior"));

				activarPrimerTurno();
			}
			else {
				$(".botonLlamarPaciente").hide();
				$(".botonColgarPaciente").hide();
				$("#" + elemento).hide();
				$("#" + elemento).next().show();
				$("#" + elemento).next().next().show();
				$("#botonAdmitir" + turno).show();
				$("#trTurno_" + turno).attr("classAnterior", $("#trTurno_" + turno).attr("class"));
				$("#trTurno_" + turno).attr("class", "fondoAmarillo");
			}
		}, 'json');
}
//-----------------------------------------------------------------------
// --> Funcion que cancela el llamado del paciente para que sea atendido
//-----------------------------------------------------------------------
function cancelarLlamarPacienteAtencion(turno) {
	$.post("admision_erp.php",
		{
			consultaAjax: '',
			accion: 'cancelarLlamarPacienteAtencion',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,
			turno: turno
		}, function (respuesta) {

			$(".botonLlamarPaciente").show();
			$(".botonColgarPaciente").hide();
			$("#botonAdmitir" + turno).hide();
			// $("#botonCancelar"+idTurno).show();
			$("#trTurno_" + turno).attr("class", $("#trTurno_" + turno).attr("classAnterior"));

			activarPrimerTurno();
		});
}
//----------------------------------------------------
// --> Funcion que cancela el turno de un paciente
//----------------------------------------------------
function cancelarTurno(turno) {
	if ($("#puestoTrabajo").val() == "") {
		alert("Primero debe seleccionar su puesto de trabajo actual.");
		$("#puestoTrabajo").css("border-color", "red");
		return;
	}

	if (confirm("¿ Esta seguro que desea cancelar el turno " + turno + " ?")) {
		$.post("admision_erp.php",
			{
				consultaAjax: '',
				accion: 'cancelarTurno',
				wemp_pmla: $('#wemp_pmla').val(),
				tema: $('#tema').val,
				turno: turno
			}, function (respuesta) {
				if (respuesta.Error)
					alert(respuesta.Mensaje);
				else {
					$("#trTurno_" + turno).hide(500, function () {
						listarPacientesConTurno();
					});
				}
			}, 'json');
	}
}
//----------------------------------------------------
// --> Mostrar en ventana modal los turno cancelados
//----------------------------------------------------
function verTurnosCancelados() {
	$.post("admision_erp.php",
		{
			consultaAjax: '',
			accion: 'verTurnosCancelados',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val(),
		}, function (respuesta) {
			$("#divTurnosCancelados").html(respuesta).dialog({
				modal: true,
				width: 'auto',
				//title	: "<div align='left'>Fecha ingreso: <input type='text' id='fechaTurnosCancel' style='width:110px' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Turnos cancelados y sin finalizar admision.</div>",
				title: "<div align='center'>Turnos cancelados y sin finalizar admision.</div>",
				show: { effect: "slide", duration: 600 },
				hide: { effect: "fold", duration: 600 },
				close: function (event, ui) {
					listarPacientesConTurno();
				}
			});
			cargar_elementos_datapicker();
			$("#fechaTurnosCancel").datepicker({
				showOn: "button",
				buttonImage: "../../images/medical/root/calendar.gif",
				buttonImageOnly: true,
				maxDate: "+0D"
			});
			$("#fechaTurnosCancel").next().css({ "cursor": "pointer" }).attr("title", "Seleccione");
			$("#fechaTurnosCancel").after("&nbsp;");
		});
}
//--------------------------------------------------------
//	--> Activar datapicker
//---------------------------------------------------------
function cargar_elementos_datapicker() {
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
			'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
			'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
		dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
		dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
		dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);
}
//----------------------------------------------------
// --> Mostrar en ventana modal los turno cancelados
//----------------------------------------------------
function habilitarTurno(turno, elemento) {
	if (confirm("¿ Esta seguro que desea habilitar el turno " + turno + " ?")) {
		$.post("admision_erp.php",
			{
				consultaAjax: '',
				accion: 'habilitarTurno',
				wemp_pmla: $('#wemp_pmla').val(),
				tema: $('#tema').val,
				turno: turno
			}, function (respuesta) {
				$(elemento).parent().parent().hide(500, function () {
					$(elemento).parent().parent().remove();
				});
			});
	}
}
//----------------------------------------------------
// --> Recargar lista de pacientes con turnos
//----------------------------------------------------
function listarPacientesConTurno() {
	$.post("admision_erp.php",
		{
			consultaAjax: '',
			accion: 'listarPacientesConTurno',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val()
		}, function (html) {
			//puestoTrabaSelec = $("#puestoTrabajo").val();
			$("#divListaPacientesConTurno").html(html);
			//$("#puestoTrabajo").val(puestoTrabaSelec);
			(($("#puestoTrabajo").val() == '') ? $("#puestoTrabajo").css("border-color", "red") : '');


			// --> Activar el buscador de texto, para los turnos
			$('#buscardorTurno').quicksearch('#tablaListaTurnos .find');
			// --> Tooltip en la lista de turnos
			$('[tooltip=si]').tooltip({ track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			// --> Si existe un turno que ya haya sido llamado por este usuario, inhabilito los demas
			if ($("#turnoLlamadoPorEsteUsuario").val() != '') {
				var idTurno = $("#turnoLlamadoPorEsteUsuario").val();
				$(".botonLlamarPaciente").hide();
				$(".botonColgarPaciente").hide();
				$("#imgLlamar" + idTurno).hide();
				$("#imgLlamar" + idTurno).next().show();
				$("#imgLlamar" + idTurno).next().next().show();
				$("#botonAdmitir" + idTurno).show();
				$("#trTurno_" + idTurno).attr("classAnterior", $("#trTurno_" + idTurno).attr("class"));
				$("#trTurno_" + idTurno).attr("class", "fondoAmarillo");
			}
			// --> Solo se puede llamar al primer turno de la lista, para evitar que llamen en desorden
			else
				activarPrimerTurno();
		});
}
//--------------------------------------------------------------------------------------------------
// --> Solo se puede llamar al primer turno de la lista, para evitar que llamen en desorden
//--------------------------------------------------------------------------------------------------
function activarPrimerTurno() {
	return;
	var primero = true;
	$(".botonLlamarPaciente:visible").each(function () {
		if (primero)
			primero = false;
		else
			$(this).hide();
	});
}

//-------------------------------------------------------------
// --> Actualiza el usuario asociado a un puesto de trabajo
//-------------------------------------------------------------
function cambiarPuestoTrabajo(respetarOcupacion) {
	if ($("#puestoTrabajo").val() == '')
		$("#puestoTrabajo").css("border-color", "red");
	else
		$("#puestoTrabajo").css("border-color", "#AFAFAF");

	$.post("admision_erp.php",
		{
			consultaAjax: '',
			accion: 'cambiarPuestoTrabajo',
			wemp_pmla: $('#wemp_pmla').val(),
			tema: $('#tema').val,
			puestoTrabajo: $("#puestoTrabajo").val(),
			respetarOcupacion: respetarOcupacion
		}, function (respuesta) {
			if (respuesta.Error) {
				if (confirm(respuesta.Mensaje + "\nDesea liberarla?"))
					cambiarPuestoTrabajo(false);
				else
					$("#puestoTrabajo").val($("#puestoTrabajo").attr("ventanillaActUsu"));
			}
		}, 'json');
}

function cambiarTipoDocumento(obj) {

	$(obj).parent().next("td").find("input").val("");
	if ($("option:selected", obj).attr("docxhis") == "on") {

		$(obj).parent().next("td").find("input").val($("#ing_histxtNumHis").attr("placeholder"));
	}
	$(obj).parent().next("td").find("input").removeClass("campoRequerido");
	$(obj).parent().next("td").find("input").focus();
}

function cambiarEstadoComplementariedad(obj) {
	if ($(obj).attr("estadoAnterior") == "on") {
		$(obj).attr("checked", false);
		$(obj).attr("estadoAnterior", "off");
	} else {
		$(obj).attr("estadoAnterior", "on");
	}
}

function filtrarPorCco(select) {
	var seleccionado = $(select).find("option:selected").val();
	if (seleccionado == "") {
		$("tr[tipo='tr_admitidos']").show();
	} else {
		$("tr[tipo='tr_admitidos'][ccoingresopaciente='" + seleccionado + "']").show();
		$("tr[tipo='tr_admitidos'][ccoingresopaciente!='" + seleccionado + "']").hide();
	}
}

/*************** INICIO FUNCIONES PARA EGRESO DE PACIENTE ********************/
$(document).ready(function () { $("#historia_alta").val(''); });

function modal_alta_paciente_otro_servicio() {
	$("#historia_alta").val('');
	$("#div_info_alta").hide();
	$("#div_info_alta").html("");
	// fnModalLoading();
	fnModalSeleccionarHistoria();
}

function fnModalSeleccionarHistoria() {
	$("#div_iniciar_alta_otro_ss").dialog({
		"closeOnEscape": false,
		show: {
			effect: "blind",
			duration: 100
		},
		hide: {
			effect: "blind",
			duration: 100
		},
		height: 500,
		// maxHeight: 400,
		width: 400,//'auto',
		buttons: {
			"Cerrar": function () {
				$(this).dialog("close");
				// fnModalLoading_Cerrar();
			}
		},
		dialogClass: 'fixed-dialog',
		modal: true,
		title: "Consultar historia activa para iniciar el alta",
		beforeClose: function (event, ui) {
			$(".bloquear_todo").removeAttr("disabled");
		},
		create: function () {
			$(this).closest('.ui-dialog').on('keydown', function (ev) {
				if (ev.keyCode === $.ui.keyCode.ESCAPE) {
					$("#div_iniciar_alta_otro_ss").dialog('close');
					// fnModalLoading_Cerrar();
				}
			});
		}
	}).on("dialogopen", function (event, ui) {
		//
	});
}

var wbasedatoCliame_alta = '';
var wbasedatoMovhos_alta = '';
var wbasedatoHce_alta = '';
function consultarHistoriaPendienteDeAlta() {
	$("#div_info_alta").html("");
	$("#div_info_alta").hide();
	var historia_alta = $("#historia_alta").val();
	if (historia_alta.replace(/ /gi, "") != '') {
		var obJson = parametrosComunes();
		obJson['consultaAjax'] = '';
		obJson['accion'] = 'consultar_historia_activa';
		obJson['historia_alta'] = historia_alta;
		// obJson['ccoUrgencias']  = document.forms.forma.codCco.value;

		fnModalLoading();

		$.post("admision_erp.php", obJson,
			function (data) {
				if (data.error == 1) {
					fnModalLoading_Cerrar();
					jAlert(data.mensaje, "Mensaje");
				}
				else {
					$("#div_info_alta").html(data.html);
					$("#div_info_alta").show();
				}
				return data;
			}, "json").done(function (data) {
				wbasedatoCliame_alta = data.wbasedatoCliame;
				wbasedatoMovhos_alta = data.wbasedatoMovhos;
				wbasedatoHce_alta = data.wbasedatoHce;
				fnModalLoading_Cerrar();
			}).fail(function (xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
	}
	else {
		jAlert("Debe escribir una historia", "Mensaje");
	}
}

function altaPacienteOtroServicio(historia_alta, wing, usuario, nombre) {
	jConfirm('Dar&aacute; de <span style="font-weight:bold;color:red;">ALTA DEFINITIVA</span> a ' + nombre + ', Esta seguro?', 'Alta definitiva', function (r) {
		if (r) {
			fnModalLoading();
			var obJson = parametrosComunes();
			obJson['consultaAjax'] = '11';
			obJson['operacion'] = 'marcaraltadefinitiva';
			obJson['basedatos'] = wbasedatoMovhos_alta;
			obJson['basedatoshce'] = wbasedatoHce_alta;
			obJson['paciente'] = historia_alta;
			obJson['ingreso'] = wing;
			obJson['seguridad'] = usuario;
			obJson['wcubiculo_ocupado'] = '';
			obJson['turno'] = '';
			obJson['desde'] = 'altaPacienteOtroServicio';

			$.post("../../hce/procesos/agenda_urgencias_por_especialidad.php", obJson,
				function (data) {
					if (data.replace(/ /gi, "") != 'ok') {
						jAlert(data, 'ALERTA');
						$('#chk_dar_alta_otro_ss').removeAttr('checked');
					}
					else {
						jAlert("El paciente " + nombre + ' ha sido dado de alta definitiva. <br><br><span style="font-weight:bold;color:red;">RECUERDE REALIZAR EL EGRESO</span>', 'ALERTA');
						// $('#div_chk_alta').remove();
						// $('#btn_egresar').show();
					}
					$("#popup_container").find(":input[type=button]").css({ "width": "100px" });
					return data;
				}).done(function (data) {
					fnModalLoading_Cerrar();
					consultarHistoriaPendienteDeAlta();
				}).fail(function (xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
		}
		else {
			$('#chk_dar_alta_otro_ss').removeAttr('checked');
		}
	});
	$("#popup_container").find(":input[type=button]").css({ "width": "100px" });
}

function abrirEgresarPaciente(path) {
	$("#btn_egresar").hide(50);
	window.open(path, '', 'fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
}

/**
 * [parametrosComunes: Genera un json con las variables más comunes que se deben enviar en los llamados ajax, evitando tener que crear los mismos parámetros de envío
 *                     en cada llamado ajax de forma manual.]
 * @return {[type]} [description]
 */
function parametrosComunes() {
	var obJson = {};
	obJson['wemp_pmla'] = $("#wemp_pmla").val();
	// obJson['wbasedato_HCE'] = $("#wbasedatohce_alta").val();
	// obJson['wbasedato']     = $("#wbasedato_alta").val();
	obJson['consultaAjax'] = '';
	return obJson;
}

// sleep time expects milliseconds
function sleep(time) {

	return new Promise((resolve) => setTimeout(resolve, time));
}

function detallartope(tope) {
	var tope = $(tope).attr('attrtope');
	//alert(tope);
	if ($(".detalletope_" + tope).length == 0) {
		alert("No hay detalle para este tipo de concepto");
	}
	else
		$(".detalletope_" + tope).toggle();
}

function abrirTableroDigitalizacion(historia, ingreso, tipoDocumento, documento, empresa, fecha) {
	var w = $(window).width();
	var h = $(window).height();


	$('html, body').animate({ scrollTop: 0 });


	// Usage! 3-

	sleep(500).then(() => {
		var html = '<iframe id="iframeModalTablero" src="../../ips/procesos/tableroDigitalizacion.php?AbiertoDesdeAdmision=si&DesdeAdmisionHistoria=' + historia + '&DesdeAdmisionIngreso=' + ingreso + '&DesdeAdmisionTipoDocumento=' + tipoDocumento + '&DesdeAdmisionDocumento=' + documento + '&DesdeAdmisionResponsable=' + empresa + '&DesdeAdmisionFecha=' + fecha + '" width="100%" height="100%" frameborder="0" allowtransparency="true"></iframe>';

		$("#modaliframe").html(html).show().dialog({
			// dialogClass: 'fixed-dialog',
			modal: true,
			title: "<div align='center' style='font-size:10pt'>Soportes Facturacion </div>",
			height: h,
			width: w,
			position: {
				my: "left top",
				at: "right top",
				of: window
			}
		});
	});
}

function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown) {
	var msj_extra = '';
	msj_extra = (mensaje != '') ? "<br>" + mensaje : mensaje;
	jAlert($("#failJquery").val() + msj_extra, "Mensaje");
	$("#div_error_interno").html(xhr.responseText);
	// console.log(xhr);
	// jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
	fnModalLoading_Cerrar();
	$(".bloquear_todo").removeAttr("disabled");
}


/**
 * [fnModalLoading: Es función se encarga de mostrar una ventana modal cada vez que se hace un llamado ajax con el fin de bloquear la página web hasta que se
 *                    se genere una respuesta y evitar que el usuario genere más eventos (click) sin terminar la petición anterior y evitar problemas
 *                    en la veracidad de datos]
 * @return {[type]} [description]
 */
function fnModalLoading() {
	$("#div_loading").dialog({
		show: {
			effect: "blind",
			duration: 100
		},
		hide: {
			effect: "blind",
			duration: 100
		},
		height: 'auto',
		// maxHeight: 600,
		width: 'auto',//800,
		// buttons: {
		//     "Cerrar": function() {
		//       $( this ).dialog( "close" );
		//     }},
		dialogClass: 'fixed-dialog',
		modal: true,
		title: "Consultando ...",
		beforeClose: function (event, ui) {
			//
		},
		create: function () {
			$(this).closest('.ui-dialog').on('keydown', function (ev) {
				if (ev.keyCode === $.ui.keyCode.ESCAPE) {
					$("#div_loading").dialog('close');
				}
			});
		},
		"closeOnEscape": false,
		"closeX": false
	}).on("dialogopen", function (event, ui) {
		//
	});
}

/**
 * [fnModalLoading_Cerrar: complemento a la función fnModalLoading, esta se encarga de cerrar la ventana modal]
 * @return {[type]} [description]
 */
function fnModalLoading_Cerrar() {
	if ($("#div_loading").is(":visible")) {
		$("#div_loading").dialog('close');
	}
}

function soloNumeros(evt) {
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	// alert(charCode);
	if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36 && charCode != 46) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
	{
		return false;
	}
	return true;
}

function validarEnterConsultar(evt) {
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode == 13) {
		$("#btn_consultar_hitoria_alta").trigger("onclick");
	}
}
// mirar
function cambioCobertura(e) {

	var hijos = $(e).find("option:selected").attr("hijos");
	var arrayHijos = hijos.split(",")
	$("#pac_tafselTipAfi > option[value!='']").remove();

	arrayHijos.forEach(function (key) {
		auxArbol = key.split("|");
		hijoDirecto = auxArbol[0];
		nietos = auxArbol[1];
		option = $("#pac_tafselTipAfi_original > option[codigoRelacion='" + hijoDirecto + "']").clone();
		hijos = $(option).attr("hijos", nietos);
		$("#pac_tafselTipAfi").append(option);
	});
	return;
}

function cambioTipoAfiliacion(e) {

	var hijos = $(e).find("option:selected").attr("hijos");
	var arrayHijos = hijos.split("_")
	$("[name='ing_pcoselPagCom']").eq(0).find("option[value!='']").remove();

	arrayHijos.forEach(function (key) {
		option = $("#ing_pcoselPagCom_original").eq(0).find("option[codigoRelacion='" + key + "']").clone();
		$("[name='ing_pcoselPagCom']").eq(0).append(option);
	});
	return;

}


function solicitarCambioDocumento(obj, historia, ingreso, tipoDocumentoAnterior, documentoAnterior, nombre) {

	tipoDocumento = tipoDocumentoAnterior;
	documento = documentoAnterior;

	$("#select_tdoc_new").find("option[value='" + tipoDocumento + "']").attr("selected", true);
	$("#select_doc_new").val(documentoAnterior);
	$("#div_mensaje_solitudCD").hide();

	$("#span_his_actual").html(historia + " - " + ingreso);
	$("#span_doc_actual").html(tipoDocumento + " - " + documento);
	$("#span_nom_actual").html(nombre);
	$("#div_doc_change").dialog({
		"closeOnEscape": false,
		show: {
			effect: "blind",
			duration: 100
		},
		hide: {
			effect: "blind",
			duration: 100
		},
		height: 650,
		// maxHeight: 400,
		width: 1000,//'auto',
		buttons: {
			"Enviar solicitud": function () {
				enviarSolicitudCambioDocumento(historia, ingreso, tipoDocumentoAnterior, documentoAnterior);
				parpadear(1);
			},
			"Cerrar Sin enviar": function () {
				$(obj).removeAttr("checked");
				$("#span_mensajeCD").html("");
				$("#txt_jus_change_Doc").val("");
				$("#div_mensaje_solitudCD").hide();
				$(this).dialog("close");
			}
		},
		dialogClass: 'fixed-dialog',
		modal: true,
		title: "SOLICITAR CAMBIO DE DOCUMENTO"
	});
}
function parpadear(cantidad) {

	if (cantidad <= 15) {
		cantidad++;
		$('#div_mensaje_solitudCD').fadeIn(500).delay(250).fadeOut(500, parpadear(cantidad));
	} else {
		return;
	}
}

function enviarSolicitudCambioDocumento(historia, ingreso, tipoDocumentoAnterior, documentoAnterior) {

	var nuevoTipoDoc = $("#select_tdoc_new option:selected").val();
	var documentoNuevo = $("#select_doc_new").val();
	var justificacion = $("#txt_jus_change_Doc").val();

	$.ajax({
		url: "admision_erp.php",
		context: document.body,
		type: "POST",
		data:
		{
			consultaAjax: '',
			accion: 'solicitarCambioDocumento',
			wbasedato: $("#wbasedato").val(),
			wemp_pmla: $("#wemp_pmla").val(),
			wtdocant: tipoDocumentoAnterior,
			wdocant: documentoAnterior,
			whistoria: historia,
			wingreso: ingreso,
			wtdocnue: nuevoTipoDoc,
			wdocnuev: documentoNuevo,
			justificacion: justificacion
		},
		async: false,
		success: function (respuesta) {
			$("#span_mensajeCD").html(respuesta);
		}
	});
}

function asociarPreanestesia(obj) {

	if ($(obj).html() == "SI") {
		$("#asociarPreanestesia").val("on");
	} else {
		$("#asociarPreanestesia").val("off");
	}
	$("#div_preanestesia").dialog("close");
	$("#div_preanestesia").dialog("destroy");
}

function inhabilitarkeypress() {
	if (e == 13) {
		return false;
	}
}
/*************** FIN FUNCIONES PARA EGRESO DE PACIENTE ********************/