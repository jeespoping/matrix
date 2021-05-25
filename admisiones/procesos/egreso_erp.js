const CLASE_CHECKBOX_CBM = ".cbm_checkbox_class";
const MUERTE = "MUERTE";
const CAUSA_EGRESO_SELECT_ID = "#egr_caeselCauEgr";

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

$(document).ready(function () {

    //segmento de urgencias.

    $("#div_datos_ing_egr,#div_datos_diagnosticos,#div_datos_procedimientos,#div_datos_especialidades,#div_datos_servicios,#div_datos_expediente_fisico,#div_datos_autorizaciones,#div_datos_observaciones_generales").attr("acordeon", "");

    //formato para las tablas
    $("table", $("#div_egresos")).addClass("anchotabla");
    //se remueve la clase para que el ancho quede al 70% en las tablas pequeñas
    $("table", $("#div_datos_ingreso")).removeClass("anchotabla");
    $("table", $("#div_datos_diagnosticos")).removeClass("anchotabla");
    $("table", $("#div_datos_procedimientos")).removeClass("anchotabla");
    $("table", $("#div_datos_especialidades")).removeClass("anchotabla");
    $("table", $("#div_datos_servicios")).removeClass("anchotabla");
    $("table", $("#div_datos_expediente_fisico")).removeClass("anchotabla");
    $("table", $("#div_datos_autorizaciones")).removeClass("anchotabla");
    $("table", $("#div_datos_observaciones_generales")).removeClass("anchotabla");

    /************************************
     * Edwin
     ************************************/

    $("div[acordeon]").accordion({
        collapsible: true,
        heightStyle: "content",
        active: 1
    });

    $("div[acordeon1]").accordion({
        collapsible: false,
        heightStyle: "content",
        icons: false
    });
    /************************************/

    //$( "#div_datos_ing_egr,#div_datos_diagnosticos,#div_datos_procedimientos,#div_datos_especialidades,#div_datos_servicios,#div_datos_expediente_fisico" ).accordion( "option", "active", true );
    //$( "H3", $( "#div_datos_ing_egr,#div_datos_diagnosticos,#div_datos_procedimientos,#div_datos_especialidades,#div_datos_servicios,#div_datos_expediente_fisico" ) ).attr( "acclick", "false" );

    //para ponerle la clase reset a todos
    $("input[type=text],input[type=radio],input[type=checkbox],select,textarea", $("#div_egresos")).addClass("reset");

    //agregarle el atributo msgError
    $("input[type=text],input[type=radio],input[type=checkbox],select,textarea", $("#div_egresos")).each(function (x) {
        if (!$(this).attr("msgError")) {
            $(this).attr("msgError", "");
        }
    });

    $(":checkbox").removeAttr("aqua").removeAttr("msgerror").removeClass("reset");
    //quitar el atributo msgError a los campos que no son obligatorios
    $("#pac_tdoselTipoDoc").removeAttr("msgError");
    $("#pac_doctxtNumDoc").removeAttr("msgError");
    $("#pac_ap1txtPriApe").removeAttr("msgError");
    $("#pac_ap2txtSegApe").removeAttr("msgError");
    $("#pac_no1txtPriNom").removeAttr("msgError");
    $("#pac_no2txtSegNom").removeAttr("msgError");
    $("#egr_esttxtestan").removeAttr("msgError");
    $("#egr_histxtNumHis").removeAttr("msgError");
    $("#egr_ingtxtNumIng").removeAttr("msgError");
    $("#txtaObsDia").removeAttr("msgError");
    $("#txtaObsPro").removeAttr("msgError");
    $("#tabla_procedimiento").find(":input").removeAttr("msgError");
    $("#tabla_autorizaciones").find(":input").removeAttr("msgError");
    $("#div_datos_observaciones_generales").find(":input").removeAttr("msgError");
    $("input[name='ser_egrradio']").removeAttr("msgError");
    $("#input_buscador_servicios").removeAttr("msgError");

    //para colocar el atributo msgError para que sea requerido y la clase campoRequerido que es el css para los obligatorios
    marcarAqua('', 'msgError', 'campoRequerido');
    marcarAqua();
    //para que los coloque amarillos de entrada
    resetAqua();

    //para las horas por defecto cuando la cambian
    $("#ing_hintxtHorIng,#egr_hoetxtHorEgr").on({
        focus: function () {
            //Si es igual a vacío o a la mascara que tenga por defecto
            if ($(this).val() == '' || $(this).val() == '__:__:__') {
                $(this).val($("#horaAct").val());
            }
        }
    });
    /************************************************************************************************/
    //se colocan los campos necesarios readonly al iniciar PASAR A TRUE CUANDO SE TRAIGA DEL REPORTE
    $("#egr_histxtNumHis,#egr_ingtxtNumIng,#ing_feitxtFecIng,#ing_hintxtHorIng,#pac_doctxtNumDoc").attr("readonly", true);

    /*para que muestre el mensaje de tooltip*/
    $("#div_egresos img[title]").tooltip({tooltipClass: "tooltip"});
    buscarMedicos("tabla_diagnostico");
    buscarMedicos("tabla_procedimiento");
    buscarMedicos("tabla_especialidad");
    buscarAnestesiologos("tabla_procedimiento");
    buscarDiagnosticos2();
    buscarProcedimientos("tabla_procedimiento");
    buscarEspecialidades("tabla_especialidad");
    buscarServicios("tabla_servicio");
    buscarServiciosSinRestriccion("tabla_procedimiento");
    /*buscarServiciosSinRestriccion( "tabla_especialidad" );
buscarServiciosSinRestriccion( "tabla_diagnostico" );*/
    calcularEstancia("no");

    if ($("#consultar_egreso").val() == "0") {
        mostrarDatos();
    } else {
        mostrarDatosEgresos();
    }

    //validaciones fecha egreso la fecha de egreso no debe ser mayor a la fecha actual
    //$( "#egr_feetxtFecEgr" ).val($( "#fechaAct" ).val() ); //fecha egreso
    var dateActual = $("#fechaAct").val();

    //$( "#egr_feetxtFecEgr" ).datepicker( "option", "maxDate",  new Date( dateActual[0], dateActual[1], dateActual[2] ) ); //menor*/

    //--> fechaIngresoAdm

    if ($("#fechaIngresoAdm").val() != "" && $("#fechaIngresoAdm").val() != undefined) {
        fechaMinima = $("#fechaIngresoAdm").val();
    } else {
        fechaMinima = "0000-00-00";
    }

    //se ponen los textarea en blanco al arrancar
    $("#txtaObsDia").val("");

    //Se oculta todos los acordeones
    $("[acordeon]").accordion("option", "active", true);

    //se pone el primer acordeon abierto desde el inicio
    //$( "#div_datos_ing_egr" ).accordion( "option", "active", 0 );
    $("[acordeon]").accordion("option", "active", 0);

    formatoCampos();

    $("#egr_feetxtFecEgr").datepicker("destroy");
    $("#egr_feetxtFecEgr").datepicker({
        changeYear: true,
        reverseYearRange: true,
        changeMonth: true,
        minDate: fechaMinima,
        maxDate: dateActual
    });


    $("#ing_feitxtFecIng").datepicker("destroy");
    $("#ing_feitxtFecIng").datepicker({
        changeYear: true,
        reverseYearRange: true,
        changeMonth: true,
        minDate: fechaMinima,
        maxDate: dateActual
    });

    /**/
    // //se le quita el requerido al select de procedimientos de la primera fila
    $("select[name=pro_tip_selTipPro]").removeAttr("msgerror");
    $("select[name=pro_tip_selTipPro]").removeClass("campoRequerido");
    $("#input_buscador_servicios").quicksearch("#tbl_servicios_diagnostico tbody tr[tipo!='titulo']");
    var wemp_pmla = $("#wemp_pmla").val();
    var historia = $("#egr_histxtNumHis").val();
    var ingreso = $("#egr_ingtxtNumIng").val();
    $.ajax({
        url: "egreso_erp.php?wemp_pmla=" + wemp_pmla,
        type: "POST",
        async: false,
        data: {
            accion: "validarCirugiaSinLiquidar",
            consultaAjax: "si",
            historia: historia,
            ingreso: ingreso,
            wbasedato: $("#wbasedato").val()
        },
        success: function (data) {
            if (data.respuesta == "si") {
                alerta("El paciente tiene cirugias PENDIENTES por liquidar.");
            }
        },
        dataType: "json"
    });
    $("#input_buscador_servicios").removeClass("campoRequerido");
    $("#input_buscador_servicios").removeAttr("msgerror");
    $("#input_buscador_servicios").removeAttr("aqua");

    fechaIngresoAux = $("#ing_feitxtFecIng").val();
    fechaEgresoAux = $("#egr_feetxtFecEgr").val();
    $("[name='pro_fec_txtFecPro']").datepicker("destroy");
    $("[name='pro_fec_txtFecPro']").datepicker({
        changeYear: true,
        reverseYearRange: true,
        changeMonth: true,
        minDate: fechaIngresoAux,
        maxDate: fechaEgresoAux
    });
    consultandoAnulado = $("input[name='consultandoAnulado']").val();
    if (consultandoAnulado) {
        alerta("Este paciente tiene un egreso anulado, para reactivar  el egreso solo debe darle click en \"Actualizar Egreso\"");
        return;
    }

    if ($("#egreso_automatico").val() == "on" && $("#funcionarioRegistros").val() != "on") {
        inhabilitarInputs();
    }

});  //ready


function inhabilitarInputs() {

    $("select[egresoAutomatico!=no],textarea[egresoAutomatico!=no],input[type=text][egresoAutomatico!=no],input[type=hidden][egresoAutomatico!=no],input[type=text][egresoAutomatico!=no],input[type=radio][egresoAutomatico!=no], input[type='button'][egresoAutomatico!=no][id!='btnEgresar'][value!='Cerrar']", $("#div_egresos")).attr("disabled", true);
    $("img[egresoAutomatico!=no], span[egresoAutomatico!=no]", "#div_egresos").remove();
}


function alerta(txt) {

    $("#textoAlerta2").text(txt);
    $('#msjAlerta2').dialog({
        width: "auto",
        height: 250,
        modal: true,
        dialogClass: 'noTitleStuff'
    });
    $(".ui-dialog-titlebar").hide();
    setTimeout(function () {
        $('#msjAlerta2').dialog('destroy');
        $(".ui-dialog-titlebar").show();
    }, 3500);
}


function addFila2(tabla_referencia) {
    if (tabla_referencia == "tabla_servicio") {
        var primerServicio = $("#" + tabla_referencia + " >tbody >tr").eq(1).find("[name='ser_egrradio']");
        if ($(primerServicio).is(":checked")) {
            servicioChequeado = true;
        } else {
            servicioChequeado = false;
        }
    }
    if (tabla_referencia == "tabla_servicio")
        var clon = $("#" + tabla_referencia + " >tbody >tr").eq(1).clone(true);
    else
        var clon = $("#" + tabla_referencia + " >tbody >tr").eq(1).clone(false);


    //$("#"+tabla_referencia+" > tbody").append(clon);
    var cant = $("#" + tabla_referencia + " >tbody >tr").length;
    //cant--;
    //$("#"+tabla_referencia+" >tbody >tr").eq(cant).find("input,select").removeAttr("aqua");
    //$("#"+tabla_referencia+" >tbody >tr").eq(cant).find("input[type=text],input[type=hidden],select").val("");
    //resetAqua( $("#"+tabla_referencia) );
    var limite = 80; //Control para no bloquear el navegador en caso de error
    var existe = true;
    var name_previo = "";
    var cant_ori = cant;
    //ORDENAR EL NAME DE LOS RADIOS
    clon.find(":radio").each(function () {
        existe = true;
        var indexx = 0;
        while (existe == true && indexx <= limite) {
            var name_x = $(this).attr("name");
            if (name_x == "med_egrradio" || name_x == "med_traradio" || name_x == "med_meiradio" || name_x == "ser_egrradio") //A este radio no se le cambia el nombre
                return false;
            name_x = name_x + "" + cant;
            if ($(":radio[name=" + name_x + "]").length > 0 && name_previo != name_x) {
                existe = true;
                cant++;
            } else {
                existe = false;
                $(this).attr("name", name_x);
                name_previo = name_x;
            }
            indexx++;
        }
    });
    if (tabla_referencia == "tabla_diagnostico" || tabla_referencia == "tabla_especialidad") {
        clon.find("td[name='contenedor_servicios_ocultos']").html("");
    }

    $("#" + tabla_referencia + " > tbody").append(clon);
    cant = cant_ori
    $("#" + tabla_referencia + " >tbody >tr").eq(cant).find("input,select").removeAttr("aqua");
    if (tabla_referencia == "tabla_especialidad") {
        $("#" + tabla_referencia + " >tbody >tr").eq(cant).find("td[name='contenedor_servicios_ocultos']").removeAttr("medico");
    }
    $("#" + tabla_referencia + " >tbody >tr").eq(cant).find("input[type=text],input[type=hidden],select").val("");
    resetAqua($("#" + tabla_referencia));

    //para colocar el atributo msgError para que sea requerido y la clase campoRequerido que es el css para los obligatorios
    if (tabla_referencia != 'tabla_procedimiento' && tabla_referencia != 'tabla_personas_autorizadas' && tabla_referencia != 'tabla_personas_reclaman') {
        marcarAqua($("#" + tabla_referencia), 'msgError', 'campoRequerido'); //campos amarillos se le quita campo requerido
        //para que cuando se le de en el boton adicionar vuelva a poner los campos obligatorios en amarillo
        resetAqua($("#" + tabla_referencia));
    }
    if (tabla_referencia == 'tabla_diagnostico') {
        buscarDiagnosticos2();
        $("[name=dia_tip_selTipDia]").eq(cant).val("S");
        reOrdenarCorchetes("numerodiagnostico", "Dx");
        validacionPrinSecu2("fila_diagnosticos");
        buscarMedicos("tabla_diagnostico");
        //buscarServiciosSinRestriccion( tabla_referencia );
    } else if (tabla_referencia == 'tabla_procedimiento') {
        $("select[name=pro_tip_selTipPro]").removeAttr("msgError")
        $("select[name=pro_tip_selTipPro]").removeClass("campoRequerido");

        marcarAqua($("#" + tabla_referencia)); //campos amarillos se le quita campo requerido
        //para que cuando se le de en el boton adicionar vuelva a poner los campos obligatorios en amarillo
        resetAqua($("#" + tabla_referencia));
        buscarProcedimientos(tabla_referencia);
        reOrdenarCorchetes("numeroprocedimiento", "P");
        buscarMedicos("tabla_procedimiento");
        buscarAnestesiologos(tabla_referencia);
        validacionPrinSecu2("fila_procedimientos");
        addFechaPicker("[name=pro_fec_txtFecPro]:eq(" + (cant_ori - 1) + ")");
        buscarServiciosSinRestriccion(tabla_referencia);//--> autocompletar para el servicio de los procedimientos
    } else if (tabla_referencia == 'tabla_especialidad') {
        //reOrdenarCorchetes("numeroespecialista","P");
        buscarEspecialidades(tabla_referencia);
        buscarMedicos(tabla_referencia);
        validacionPrinSecu2("fila_especialidades");
        //buscarServiciosSinRestriccion( tabla_referencia );//--> autocompletar para el servicio de los especialidades
    } else if (tabla_referencia == 'tabla_servicio') {
        buscarServicios(tabla_referencia);
        $("input[type='radio'][name^='ser_egrradio']").attr("name", "ser_egrradio");
        if (servicioChequeado) {
            $("#" + tabla_referencia + " >tbody >tr").eq(1).find("[name='ser_egrradio']").attr("checked", servicioChequeado);
        }
    } else {
        marcarAqua($("#" + tabla_referencia));
        resetAqua($("#" + tabla_referencia));
    }
}

function addFechaPicker(selectorjquery) {
    fechaIngresoAux = $("#ing_feitxtFecIng").val();
    fechaEgresoAux = $("#egr_feetxtFecEgr").val();
    $(selectorjquery).removeClass("hasDatepicker");
    $(selectorjquery).removeAttr("id");
    $(selectorjquery).datepicker({
        dateFormat: "yy-mm-dd",
        fontFamily: "verdana",
        dayNames: ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
        monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        dayNamesShort: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        monthNamesShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        changeMonth: true,
        changeYear: true,
        yearRange: "c-100:c+100",
        minDate: fechaIngresoAux,
        maxDate: fechaEgresoAux
    });
}

function reOrdenarCorchetes(claseOrdenar, letra) {
    var ind = 1;
    $("." + claseOrdenar).each(function () {
        $(this).text(letra + "" + ind);
        ind++;
    });
}

function removerFila2(obj, filaPrincipal, tablaReferencia) {
    acc_confirm = 'Confirma que desea eliminar?';
    if (confirm(acc_confirm)) {
        var cantidad = $("#" + tablaReferencia + " >tbody >tr").length;
        if (cantidad == 2) {
            $("." + filaPrincipal).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#" + tablaReferencia));
            return;
        }
        obj = jQuery(obj);
        obj.parents("." + filaPrincipal).remove();

        validacionPrinSecu2(filaPrincipal);
    }
}

function buscarMedicos(tabla_referencia) {
    var wbasedato = $("#wbasedato").val();
    var aplicacion = $("#aplicacion").val();
    var wemp_pmla = $("#wemp_pmla").val();

    var claseFilaPrincipal = "";
    var selectorInputs = "";
    if (tabla_referencia == "tabla_diagnostico") {
        claseFilaPrincipal = "fila_diagnosticos";
        var cantidadMedicos = $("." + claseFilaPrincipal).length;
        cantidadMedicos--;
        selectorInputs = "[name=Desesm_txtDesesm]:eq(" + cantidadMedicos + "),[name=dia_med_txtCodMed]:eq(" + cantidadMedicos + "),[name=DesMed_txtDesMed]:eq(" + cantidadMedicos + ")";
    } else if (tabla_referencia == "tabla_procedimiento") {
        claseFilaPrincipal = "fila_procedimientos";
        var cantidadMedicos = $("." + claseFilaPrincipal).length;
        cantidadMedicos--;
        selectorInputs = "[name=Desesm_txtCodesm]:eq(" + cantidadMedicos + "),[name=pro_med_txtCodMed]:eq(" + cantidadMedicos + "),[name=DesMed_txtDesMedP]:eq(" + cantidadMedicos + ")";
    } else if (tabla_referencia == "tabla_especialidad") {
        claseFilaPrincipal = "fila_especialidades";
        var cantidadMedicos = $("." + claseFilaPrincipal).length;
        cantidadMedicos--;
        selectorInputs = "[name=esp_med_txtCodEsp]:eq(" + cantidadMedicos + "),[name=DesMed_txtDesEsp]:eq(" + cantidadMedicos + ")";
    } else {
        return;
    }

    //Asigno autocompletar para la busqueda de medicos
    $("#" + tabla_referencia).find(selectorInputs).autocomplete("egreso_erp.php?consultaAjax=&accion=consultarMedico&wbasedato=" + wbasedato + "&aplicacion=" + aplicacion + "&wemp_pmla=" + wemp_pmla,
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
                return datos[0].usu;    //Eso es lo que se muestra al usuario
            },
            formatResult: function (data, value) {
                //convierto el string en json
                eval("var datos = " + data);
                return datos[0].valor.des;
            }
        }).result(
        function (event, item) {
            eval("var datos = " + item);
            //Guardo el ultimo valor que selecciona el usuario
            //this.parentNode.parentNode El tr que contiene el input
            $("input[type=text]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
            $("input[type=text]", this.parentNode.parentNode).eq(1).val(datos[0].valor.des).removeClass("inputblank");
            ;
            if ($(this).parents("#tabla_especialidad").length > 0) {
                $("input[type=text]", this.parentNode.parentNode).eq(3).val(datos[0].valor.desesp).removeClass("inputblank");
                $("input[type=text]", this.parentNode.parentNode).eq(2).val(datos[0].valor.codesp).removeClass("inputblank");
            } else {
                $("input[type=text]", this.parentNode.parentNode).eq(2).val(datos[0].valor.desesp).removeClass("inputblank");
                ;
            }
            this._lastValue = this.value;
            $("input[type=hidden]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
            $("input[type=hidden]", this.parentNode.parentNode).eq(1).val(datos[0].valor.codesp);
            $("input[type=text]", this.parentNode.parentNode).removeClass("campoRequerido");
            $("[name='contenedor_servicios_ocultos']", $(this).parent().parent().next("tr")).attr("medico", datos[0].valor.cod);//-->esto funciona para los diagnósticos

            //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar
            //--> aca se va a agregar a especialidades, para facilitar el trabajo. y evitar trabajo doble
            if ($(this).attr("name") != "DesMed_txtDesEsp" && $(this).attr("name") != "esp_med_txtCodEsp") {//--> si es una busqueda distinta al formulario de especialidades, se agragan automáticamente a este listado siempre y cuando no esté repetido.
                //medicoVeces = $("input[name='esp_med_txtCodEsp'][value='"+ datos[0].valor.cod+"']").length; //--> se cuenta cuantas veces está el médico en la lista de especialidades por medio de su código
                medicoVeces = 0;
                $("input[name='esp_med_txtCodEsp']").each(function () {
                    documentoMedico = $(this).val();
                    if (documentoMedico == datos[0].valor.cod) {
                        medicoVeces = 1;
                    }
                });
                if (medicoVeces == 0 || medicoVeces == undefined) {//--> SI EL CENTRO DE COSTOS NO SE HA GUARDADO EN LA LISTA DE SERVICIOS VISITADOS, LO AGREGO AUTÓMATICAMENTE
                    if ($(".fila_especialidades").length == 1 && $(".fila_especialidades").eq(0).find("input[name='esp_med_hidCodEsp']").val() == "") {

                    } else {
                        $("#spn_tabla_especialidad").click();
                    }
                    camposEspecialidades = ($(".fila_especialidades").length) - 1;

                    //--> codigo del médico
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='esp_med_txtCodEsp']").val(datos[0].valor.cod);
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='esp_med_txtCodEsp']").removeClass("campoRequerido");
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='esp_med_hidCodEsp']").val(datos[0].valor.cod);

                    //--> nombre del médico
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='DesMed_txtDesEsp']").val(datos[0].valor.des);
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='DesMed_txtDesEsp']").removeClass("campoRequerido");

                    //--> codigo especialidad
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='esp_cod_txtCodEsp']").val(datos[0].valor.codesp);
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='esp_cod_txtCodEsp']").removeClass("campoRequerido");
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='esp_cod_hidCodEsp']").val(datos[0].valor.codesp);

                    //--> descripcion especialidad
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='DesEsp_txtDesEsp']").val(datos[0].valor.desesp);
                    $(".fila_especialidades").eq(camposEspecialidades).find("input[name='DesEsp_txtDesEsp']").removeClass("campoRequerido");

                    $(".fila_especialidades").eq(camposEspecialidades).find("[name='contenedor_servicios_ocultos']").attr("medico", datos[0].valor.cod);
                    if ($(this).attr("name") == "DesMed_txtDesMedP" || $(this).attr("name") == "pro_med_txtCodMed") {
                        $("input[type=text][name='proSer_txtDesSer']", $(this).parent().parent().next("tr").next("tr").next("tr").next("tr")).attr("medico", datos[0].valor.cod);
                    }
                }
            }

        }
    ).on({
        change: function () {
            var cmp = this;
            setTimeout(function () {
                if (cmp.aqAttr == undefined)
                    cmp.aqAttr = "";
                //Pregunto si la pareja es diferente
                if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
                    || (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
                ) {
                    alerta("Digite un m\u00e9dico v\u00E1lido")
                    $("input[type=hidden]", cmp.parentNode).val('');
                    cmp.value = '';
                    cmp.focus();
                    //cmp.blur();
                }
            }, 200);
        }
    });
}

function buscarAnestesiologos(tabla_referencia) {
    var wbasedato = $("#wbasedato").val();
    var aplicacion = $("#aplicacion").val();
    var wemp_pmla = $("#wemp_pmla").val();
    var claseFilaPrincipal = "";
    var selectorInputs = "";
    if (!tabla_referencia) return;
    if (tabla_referencia == "tabla_procedimiento") {
        claseFilaPrincipal = "fila_procedimientos";
        var cantidadMedicos = $("." + claseFilaPrincipal).length;
        cantidadMedicos--;
        selectorInputs = "[name=pro_ane_txtCodMed]:eq(" + cantidadMedicos + "),[name=DesAne_txtDesMed]:eq(" + cantidadMedicos + ")";
    } else {
        return;
    }

    //Asigno autocompletar para la busqueda de medicos
    $("#" + tabla_referencia).find(selectorInputs).autocomplete("egreso_erp.php?consultaAjax=&accion=consultarAnestesiologo&wbasedato=" + wbasedato + "&aplicacion=" + aplicacion + "&wemp_pmla=" + wemp_pmla,
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
                return datos[0].usu;    //Eso es lo que se muestra al usuario
            },
            formatResult: function (data, value) {
                //convierto el string en json
                eval("var datos = " + data);
                return datos[0].valor.des;
            }
        }).result(
        function (event, item) {
            eval("var datos = " + item);
            //Guardo el ultimo valor que selecciona el usuario
            //this.parentNode.parentNode El tr que contiene el input
            $("input[type=text]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
            $("input[type=text]", this.parentNode.parentNode).eq(1).val(datos[0].valor.des).removeClass("inputblank");

            this._lastValue = this.value;
            $("input[type=hidden]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
            $("input[type=text]", this.parentNode.parentNode).removeClass("campoRequerido");
            //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar

        }
    ).on({
        change: function () {
            var cmp = this;
            setTimeout(function () {
                if (cmp.aqAttr == undefined)
                    cmp.aqAttr = "";
                //Pregunto si la pareja es diferente
                if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
                    || (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
                ) {
                    alerta("Digite un anestesiologo v\u00E1lido")
                    $("input[type=hidden]", cmp.parentNode).val('');
                    cmp.value = '';
                    cmp.focus();
                    //cmp.blur();
                }
            }, 200);
        }
    });
}

dxIngreso = false;

function buscarDiagnosticos2() {
    var cantidad_diagnosticos = $(".fila_diagnosticos").length;
    var sexoPaciente = "";

    if ($("#sexoAdm").val().toUpperCase() == "F" || $("#sexoAdm").val().toUpperCase() == "M") {
        sexoPaciente = $("#sexoAdm").val().toUpperCase();
    }
    cantidad_diagnosticos--;
    //Asigno autocompletar para la busqueda de diagnosticos
    $("#tabla_diagnostico").find("[name=dia_cod_txtCodDia]:eq(" + cantidad_diagnosticos + "),[name=DesDia_txtDesDia]:eq(" + cantidad_diagnosticos + ")").autocomplete("egreso_erp.php?consultaAjax=&accion=consultarDiagnostico&sexoPaciente=" + sexoPaciente,
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
                return datos[0].usu;    //Eso es lo que se muestra al usuario
            },
            formatResult: function (data, value) {
                console.log("ALERT 2");
                console.log(this);
                console.log(data);
                console.log(value);
                //convierto el string en json
                eval("var datos = " + data);
                return datos[0].valor.des;
            }
        }).result(
        function (event, item) {
            // //La respuesta es un json        // //convierto el string en formato json
            eval("var datos = " + item);
            //Guardo el ultimo valor que selecciona el usuario
            //this.parentNode.parentNode El tr que contiene el input
            $("input[type=text]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
            $("input[type=text]", this.parentNode.parentNode).eq(1).val(datos[0].valor.des);
            this._lastValue = this.value;
            $("input[type=hidden]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);

            //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar
            $("input[type=text]", this.parentNode.parentNode).removeClass("campoRequerido");
        }
    ).on({
        change: function () {
            var cmp = this;

            setTimeout(function () {
                //Pregunto si la pareja es diferente
                if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
                    || (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
                ) {
                    alerta("Digite un Diagn\u00f3stico v\u00E1lido");
                    $("input[type=hidden]", cmp.parentNode).val('');
                    cmp.value = '';
                    cmp.focus();
                }

                //se agrego dentro del settimeout para que de el tiempo suficiente para cambiar el valor en el hidden
                /**Valicacion dignostico repetido**/
                var mensaje = "";
                var codDiagnostico = "";
                var codigos = new Array();
                var cont = 0;

                //busca dentro del div_datos_diagnosticos los campos hidden que comiencen con dia_cod
                $("#div_datos_diagnosticos").find(":hidden[name=dia_codhidCodDia]").each(function () {
                    if ($(this).val() != '') {
                        codigos[cont] = $(this).val();
                        cont++;
                    }
                });
                var repetidos = countRepeated(codigos);
                mensaje = "El diagnostico:\n";
                for (var i in repetidos) {
                    if (repetidos[i] > 1) {
                        mensaje += i + " se encuentra repetido " + repetidos[i] + " veces por favor verifique\n";
                    }
                }
                /** Fin Diagnostico repetido**/

                //si hay mensaje muestra el div
                if (mensaje != '' && repetidos[i] > 1) {
                    $("#divMenDiag").css("display", "");
                    $("#divMenDiag").html(mensaje);

                    //de la fila actual se ponen los dos primeros input en vacio cuando el diag esta repetido y el hidden
                    $("input[type=text]", cmp.parentNode.parentNode).eq(0).val("");
                    $("input[type=text]", cmp.parentNode.parentNode).eq(1).val("");
                    cmp._lastValue = cmp.value;
                    $("input[type=hidden]", cmp.parentNode.parentNode).eq(0).val("");
                } else {
                    $("#divMenDiag").css("display", "none");
                }


            }, 200);
        }
    });
}

function buscarProcedimientos(tabla_referencia) {

    if (!tabla_referencia) return;

    var wbasedato = $("#wbasedato").val();
    var aplicacion = $("#aplicacion").val();

    var claseFilaPrincipal = "";
    var selectorInputs = "";
    if (tabla_referencia == "tabla_procedimiento") {
        claseFilaPrincipal = "fila_procedimientos";
        var cantidadMedicos = $("." + claseFilaPrincipal).length;
        cantidadMedicos--;
        selectorInputs = "[name=pro_cod_txtCodPro]:eq(" + cantidadMedicos + "),[name=ProDes_txtProDes]:eq(" + cantidadMedicos + ")";
    }
    //Asigno autocompletar para la busqueda de diagnosticos
    $("#" + tabla_referencia).find(selectorInputs).autocomplete("egreso_erp.php?consultaAjax=&accion=consultarProcedimiento&wbasedato=" + wbasedato,
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
                return datos[0].usu;    //Eso es lo que se muestra al usuario
            },
            formatResult: function (data, value) {
                //convierto el string en json
                eval("var datos = " + data);
                return datos[0].valor.des;
            }
        }).result(
        function (event, item) {

            // //La respuesta es un json            // //convierto el string en formato json
            eval("var datos = " + item);
            //Guardo el ultimo valor que selecciona el usuario

            //this.value = datos[0].valor.cod
            $("input[type=text]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
            $("input[type=text]", this.parentNode.parentNode).eq(1).val(datos[0].valor.des).removeClass("inputblank");
            this._lastValue = this.value;
            $("input[type=hidden]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);

            //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar
            $("input[type=text]", this.parentNode.parentNode).removeClass("campoRequerido");
            if ($(this).attr("name").substring(0, 3).toUpperCase() == "PRO") {
                $("[name^='pro_fec_']", $(this).parent().parent()).addClass('campoRequerido');
                //para colocar el atributo msgError para que sea requerido y la clase campoRequerido que es el css para los obligatorios
                $("[name^='pro_fec_']", $(this).parent().parent()).attr('msgError', 'fecha obligatoria');
            }
        }
    ).on({
        change: function () {

            var cmp = this;

            setTimeout(function () {

                //Pregunto si la pareja es diferente
                if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
                    || (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
                ) {
                    alerta("Digite un Procedimiento v\u00E1lido")
                    $("input[type=hidden]", cmp.parentNode).val('');
                    cmp.value = '';
                    cmp.focus();
                    // cmp.blur();
                    tabla_referencia = ''; //se agrega
                }
                //para verificar que se ejecute esta parte desde la tabla procedimiento
                //var tabla = cmp.parentNode.parentNode.parentNode.parentNode;

                //se agrego dentro del settimeout para que de el tiempo suficiente para cambiar el valor en el hidden
                /**Valicacion dignostico repetido**/
                var mensaje = "";
                var codDiagnostico = "";
                var codigos = new Array();
                var cont = 0;

                //busca dentro del div_datos_procedimientos los campos hidden que comiencen con pro_cod
                $("#div_datos_procedimientos").find(":hidden[name=pro_cod_hidCodPro]").each(function () {
                    if ($(this).val() != '') {
                        codigos[cont] = $(this).val() + "_" + $(this).parent().parent().find("input[name='pro_fec_txtFecPro']").val();//2020-03-25
                        cont++;
                    }
                });
                var repetidos = countRepeated(codigos);

                mensaje = "El procedimiento:\n";
                for (var i in repetidos) {
                    // mensaje += i + " => " + repetidos[i] + " veces\n";
                    if (repetidos[i] > 1) {
                        i = i.split("_");
                        i = i[0];
                        mensaje += i + " se encuentra repetido " + repetidos[i] + " veces por favor verifique\n";
                    }
                }
                /** Fin Procedimiento repetido**/

                //si hay mensaje muestra el div
                if (mensaje != '' && repetidos[i] > 1) {
                    $("#divMenProc").css("display", "");
                    $("#divMenProc").html(mensaje);

                    //de la fila actual se ponen los dos primeros input en vacio cuando el proc esta repetido y el hidden
                    $("input[type=text]", cmp.parentNode.parentNode).eq(0).val("");
                    $("input[type=text]", cmp.parentNode.parentNode).eq(1).val("");
                    cmp._lastValue = cmp.value;
                    $("input[type=hidden]", cmp.parentNode.parentNode).eq(0).val("");
                } else {
                    $("#divMenProc").css("display", "none");
                }

            }, 200);
        }
    });
}

function buscarEspecialidades(tabla_referencia) {
    var wbasedato = $("#wbasedato").val();
    var aplicacion = $("#aplicacion").val();
    var wemp_pmla = $("#wemp_pmla").val();

    var claseFilaPrincipal = "";
    var selectorInputs = "";
    if (!tabla_referencia) return;
    if (tabla_referencia == "tabla_especialidad") {
        claseFilaPrincipal = "fila_especialidades";
        var cantidadMedicos = $("." + claseFilaPrincipal).length;
        cantidadMedicos--;
        selectorInputs = "[name=esp_cod_txtCodEsp]:eq(" + cantidadMedicos + "),[name=DesEsp_txtDesEsp]:eq(" + cantidadMedicos + ")";
    }

    //Asigno autocompletar para la busqueda de diagnosticos
    $("#" + tabla_referencia).find(selectorInputs).autocomplete("egreso_erp.php?consultaAjax=&accion=consultarEspecialidad&wbasedato=" + wbasedato + "&aplicacion=" + aplicacion + "&wemp_pmla=" + wemp_pmla,
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

                return datos[0].usu;    //Eso es lo que se muestra al usuario
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
            //this.value = datos[0].valor.cod
            $("input[type=text]", this.parentNode.parentNode).eq(2).val(datos[0].valor.cod).removeClass("campoRequerido");
            $("input[type=text]", this.parentNode.parentNode).eq(3).val(datos[0].valor.des).removeClass("campoRequerido");
            this._lastValue = this.value;
            $("input[type=hidden]", this.parentNode.parentNode).eq(2).val(datos[0].valor.cod);

        }
    ).on({
        change: function () {

            var cmp = this;

            setTimeout(function () {
                //Pregunto si la pareja es diferente
                if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
                    || (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
                ) {
                    alerta("Digite una Especialidad válida")
                    $("input[type=hidden]", cmp.parentNode).val('');
                    cmp.value = '';
                    cmp.focus();
                    // cmp.blur();
                    tabla_referencia = ''; //agregado
                }

                //se agrego dentro del settimeout para que de el tiempo suficiente para cambiar el valor en el hidden
                /**Valicacion especialidad repetido**/
                var mensaje = "";
                var codDiagnostico = "";
                var codigos = new Array();
                var cont = 0;

                //busca dentro del div_datos_especialidades los campos hidden que comiencen con dia_cod
                /*$("#div_datos_especialidades").find(":hidden[id^=esp_cod]").each(function(){
                if ($( this ).val() != '')
                {
                    codigos[cont]= $( this ).val();
                    cont++;
                }
            }); *///2014-05-28 para que deje poner varias especialidades
                // alert(codigos.toString());
                // var x=codigos.length;
                // alert(x);

                var repetidos = countRepeated(codigos);

                mensaje = "La especialidad:\n";
                for (var i in repetidos) {
                    // mensaje += i + " => " + repetidos[i] + " veces\n";
                    if (repetidos[i] > 1) {
                        mensaje += i + " se encuentra repetida " + repetidos[i] + " veces por favor verifique\n";
                    }
                }
                /** Fin Especialidad repetida**/

                //si hay mensaje muestra el div
                if (mensaje != '' && repetidos[i] > 1) {
                    $("#divMenEspe").css("display", "");
                    $("#divMenEspe").html(mensaje);

                    //de la fila actual se ponen los dos primeros input en vacio cuando el espe esta repetido y el hidden
                    $("input[type=text]", cmp.parentNode.parentNode).eq(2).val("");
                    $("input[type=text]", cmp.parentNode.parentNode).eq(3).val("");
                    cmp._lastValue = cmp.value;
                    $("input[type=hidden]", cmp.parentNode.parentNode).eq(2).val("");
                } else {
                    $("#divMenEspe").css("display", "none");
                }
            }, 200);
        }
    });
}

function buscarServicios(tabla_referencia) {
    var wbasedato = $("#wbasedato").val();
    var aplicacion = $("#aplicacion").val();
    if (!tabla_referencia) return;
    var selectorInputs = "";
    if (tabla_referencia == "tabla_servicio") {
        claseFilaPrincipal = "fila_servicios";
        var cantidadMedicos = $("." + claseFilaPrincipal).length;
        cantidadMedicos--;
        selectorInputs = "[name=ser_cod_txtCodSer]:eq(" + cantidadMedicos + "),[name=DesSer_txtDesSer]:eq(" + cantidadMedicos + ")";
    }
    /**************/
    //Asigno autocompletar para la busqueda de diagnosticos
    $("#" + tabla_referencia).find(selectorInputs).autocomplete("egreso_erp.php?consultaAjax=&accion=consultarServicio&wbasedato=" + wbasedato + "&aplicacion=" + aplicacion,
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
                return datos[0].usu;    //Eso es lo que se muestra al usuario
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
            //this.value = datos[0].valor.cod
            $("input[type=text]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
            $("input[type=text]", this.parentNode.parentNode).eq(1).val(datos[0].valor.des);
            this._lastValue = this.value;
            $("input[type=hidden]", this.parentNode.parentNode).eq(0).val(datos[0].valor.cod);
            $("input[type='radio'][name='ser_egrradio']", this.parentNode.parentNode).val(datos[0].valor.cod);

            //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar
            $("input[type=text]", this.parentNode.parentNode).removeClass("campoRequerido");
        }
    ).on({
        change: function () {

            var cmp = this;
            setTimeout(function () {
                //Pregunto si la pareja es diferente
                if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
                    || (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
                ) {
                    alerta("Digite un Servicio v\u00E1lido")
                    $("input[type=hidden]", cmp.parentNode).val('');
                    cmp.value = '';
                    cmp.focus();
                    // cmp.blur();
                    tabla_referencia = ''; //agregado
                }
                //para verificar que se ejecute esta parte desde la tabla diagnostico
                //var tabla = cmp.parentNode.parentNode.parentNode.parentNode;

                //se agrego dentro del settimeout para que de el tiempo suficiente para cambiar el valor en el hidden
                /**Valicacion servicio repetido**/
                var mensaje = "";
                var codDiagnostico = "";
                var codigos = new Array();
                var cont = 0;

                //busca dentro del div_datos_servicios los campos hidden que comiencen con dia_cod
                $("#div_datos_servicios").find(":hidden[name=ser_cod_hidCodSer]").each(function () {
                    if ($(this).val() != '') {
                        codigos[cont] = $(this).val();
                        cont++;
                    }
                });
                var repetidos = countRepeated(codigos);

                mensaje = "El Servicio:\n";
                for (var i in repetidos) {
                    if (repetidos[i] > 1) {
                        mensaje += i + " se encuentra repetido " + repetidos[i] + " veces por favor verifique\n";
                    }
                }
                /** Fin servicio repetido**/
                //si hay mensaje muestra el div
                if (mensaje != '' && repetidos[i] > 1) {
                    $("#divMenServ").css("display", "");
                    $("#divMenServ").html(mensaje);

                    //de la fila actual se ponen los dos primeros input en vacio cuando el diag esta repetido y el hidden
                    $("input[type=text]", cmp.parentNode.parentNode).eq(0).val("");
                    $("input[type=text]", cmp.parentNode.parentNode).eq(1).val("");
                    cmp._lastValue = cmp.value;
                    $("input[type=hidden]", cmp.parentNode.parentNode).eq(0).val("");
                } else {
                    $("#divMenServ").css("display", "none");
                }
            }, 200);
        }
    });
}

function buscarServiciosSinRestriccion(tabla_referencia) {
    var wbasedato = $("#wbasedato").val();
    var aplicacion = $("#aplicacion").val();
    if (!tabla_referencia) return;
    var selectorInputs = "";
    if (tabla_referencia == "tabla_procedimiento") {
        claseFilaPrincipal = "fila_procedimientos";
        var cantidadMedicos = $("." + claseFilaPrincipal).length;
        cantidadMedicos--;
        selectorInputs = "[name=proSer_txtDesSer]:eq(" + cantidadMedicos + ")";
    }
    /*if( tabla_referencia == "tabla_especialidad"){
claseFilaPrincipal = "fila_especialidades";
var cantidadMedicos = $("."+claseFilaPrincipal).length;
cantidadMedicos--;
selectorInputs= "[name=espSer_txtDesSer]:eq("+cantidadMedicos+")";
}*/
    /*if( tabla_referencia == "tabla_diagnostico"){
claseFilaPrincipal = "fila_diagnosticos";
var cantidadMedicos = $("."+claseFilaPrincipal).length;
cantidadMedicos--;
selectorInputs= "[name=diaSer_txtdiaSer]:eq("+cantidadMedicos+")";
}*/
    /**************/
    //Asigno autocompletar para la busqueda de diagnosticos
    $("#" + tabla_referencia).find(selectorInputs).autocomplete("egreso_erp.php?consultaAjax=&accion=consultarServicio&wbasedato=" + wbasedato + "&aplicacion=" + aplicacion,
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
                return datos[0].usu;    //Eso es lo que se muestra al usuario
            },
            formatResult: function (data, value) {
                //convierto el string en json
                eval("var datos = " + data);
                return datos[0].valor.cod + "-" + datos[0].valor.des;
            }
        }).result(
        function (event, item) {

            // //La respuesta es un json
            // //convierto el string en formato json
            eval("var datos = " + item);
            $("input[type=hidden]", this.parentNode).eq(0).val(datos[0].valor.cod);
            this._lastValue = this.value;
            if ($(this).attr("name") == "proSer_txtDesSer") {
                codigoMedico = $(this).attr("medico");
                //--> la agregada del detalle en la zona de especialidades.
                contenedorServiciosEspecialidad = $("td[nombreCampos='see_ser_hidCodSer'][name='contenedor_servicios_ocultos'][medico='" + codigoMedico + "']");
                if (contenedorServiciosEspecialidad != undefined) {
                    cantidadServiciosMedico = $(":hidden[name='see_ser_hidCodSer'][value='" + datos[0].valor.cod + "']", contenedorServiciosEspecialidad).length
                    if (cantidadServiciosMedico == 0) {
                        cantidadServiciosMedico = $(":hidden[name='see_ser_hidCodSer'][value='" + datos[0].valor.cod + "']", contenedorServiciosEspecialidad).length
                        nuevoServicio = "<input type='hidden' id='see_ser_hidCodSer' name='see_ser_hidCodSer' value='" + datos[0].valor.cod + "' >";
                        $(contenedorServiciosEspecialidad).html($(contenedorServiciosEspecialidad).html() + nuevoServicio);
                    }
                }
            }
            //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar
            $("input[type=text]", this.parentNode).removeClass("campoRequerido");

            //-->Se agrega el servicio como visitado:
            cantidad = $("#div_datos_servicios").find(":hidden[name='ser_cod_hidCodSer'][value='" + datos[0].valor.cod + "']").length;
            //alert( "el centro de costos: "+datos[0].valor.cod+" está "+cantidad+" veces;" );
            if (cantidad == 0) {//--> SI EL CENTRO DE COSTOS NO SE HA GUARDADO EN LA LISTA DE SERVICIOS VISITADOS, LO AGREGO AUTÓMATICAMENTE
                if ($(".fila_servicios").length == 1 && $(".fila_servicios").eq(0).find("input[name='ser_cod_hidCodSer']").val() == "") {
                } else {
                    $("#spn_tabla_servicio").click();
                }

                camposServicios = ($(".fila_servicios").length) - 1;
                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_txtCodSer']").val(datos[0].valor.cod);
                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_txtCodSer']").removeClass("campoRequerido");
                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_hidCodSer']").val(datos[0].valor.cod);
                $(".fila_servicios").eq(camposServicios).find("input[name='ser_egrradio']").val(datos[0].valor.cod);
                $(".fila_servicios").eq(camposServicios).find("input[name='DesSer_txtDesSer']").val(datos[0].valor.des);
                $(".fila_servicios").eq(camposServicios).find("input[name='DesSer_txtDesSer']").removeClass("campoRequerido");
            }

        }
    ).on({
        change: function () {

            var cmp = this;
            setTimeout(function () {
                //Pregunto si la pareja es diferente
                if (((cmp._lastValue && cmp._lastValue != cmp.value) || (!cmp._lastValue && cmp.value != $(cmp).attr(cmp.aqAttr)))
                    || (cmp._lastCodigo && cmp._lastCodigo != $("input[type=hidden]", cmp.parentNode).val())
                ) {
                    alerta("Digite un Servicio v\u00E1lido")
                    $("input[type=hidden]", cmp.parentNode).val('');
                    cmp.value = '';
                    cmp.focus();
                    // cmp.blur();
                    tabla_referencia = ''; //agregado
                }
            }, 200);
        }
    });
}

function agregarServicioAutomaticamente(codigo, $) {

}

function resetear(inicio) {
    $("#btnEgresar").val("Egresar");
    //Variable para saber si esta en modo consulta o no
    modoConsulta = false;
    consultaEgreso = false;

    //todos los que tengan la clase reset se ponen en blanco
    $("#div_egresos").find(":input[type=text],:input[type=hidden],textarea,select").each(function () {
        if ($(this).hasClass('reset')) {
            $(this).val("");
        }
    });

    $("#div_egresos").find("checkbox,radio").each(function () {
        $(this).attr('checked', false);
    });
    //se ponen estos radio por defecto no chequeados
    $('input[name="egr_uexradUbiExp"]').attr('checked', false);
    $('input[name="egr_comradCon"]').attr('checked', false);

    // iniciarMarcaAqua();
    //para mostrar la fecha actual
    var now = new Date();
    var hora = now.getHours();
    var minutos = now.getMinutes();
    var segundos = now.getSeconds();
    if (hora < 10) {
        hora = '0' + hora
    }
    if (minutos < 10) {
        minutos = '0' + minutos
    }
    if (segundos < 10) {
        segundos = '0' + segundos
    }
    horaActual = hora + ":" + minutos + ":" + segundos;

    //datos por defecto a iniciar
    $("#ing_feitxtFecIng").val($("#fechaAct").val()); //fecha ingreso

    //validaciones fecha inicio atencion
    $("#egr_fiatxtFecInA").val($("#fechaAct").val()); //fecha inicio atencion
    //resetear la validacion de fecha inicio de atencion para que permita cualquier fecha
    //var dateActual = $( "#egr_fiatxtFecInA" ).val().split( "-" );
    //$( "#egr_fiatxtFecInA" ).datepicker( "option", "minDate", "" ); //menor

    //validaciones fecha egreso
    $("#egr_feetxtFecEgr").val($("#fechaAct").val()); //fecha egreso
    var dateActual = $("#fechaAct").val().split("-");
    $("#egr_feetxtFecEgr").datepicker("option", "minDate", new Date(dateActual[0], dateActual[1] - 1, dateActual[2])); //menor

    $("#egr_ftatxtFecTeA").val($("#fechaAct").val()); //fecha fin atencion
    $("#ing_hintxtHorIng").val(horaActual); //hora ingreso
    $("#egr_hoetxtHorEgr").val(horaActual); //hora egreso
    $("#egr_cexselCauExt").val(''); //causa externa
    $("#pac_tdoselTipoDoc").val('CC'); //tipo de documento
    $("#egr_caeselCauEgr").val(''); //causa egreso
    $("#egr_caeselCauEgr").val(''); //tipo de diagnostico principal

    //para que cuando se le de en el boton iniciar vuelva a poner los campos obligatorios en amarillo
    resetAqua();
    //se llama a calcular estacia
    calcularEstancia("no");

    $("#bot_navegacion").css("display", "none"); //se oculta el div de navegacion de resultados
    $("#bot_navegacion1").css("display", "none"); //se oculta el div de navegacion de resultados

    /*busque dentro de la tabla los tds con el id que terminan en _tr_tabla_diagnostico
y luego se simula el clic en el span de adicionar y adiciona una fila que queda de primera*/
    //para tabla diagnostico
    var i = 0;
    $("#tabla_diagnostico").find(".fila_diagnosticos").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_diagnostico"));
        }
        i++;
    });

    i = 0;
    $("#tabla_procedimiento").find(".fila_procedimientos").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_procedimiento"));
        }
        i++;
    });

    i = 0;
    $("#tabla_especialidad").find(".fila_especialidades").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_procedimiento"));
        }
        i++;
    });

    i = 0;
    $("#tabla_servicio").find(".fila_servicios").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_servicio"));
        }
        i++;
    });
    i = 0;
    $("#tabla_personas_autorizadas").find(".fila_personas_autorizadas").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_personas_autorizadas"));
        }
        i++;
    });
    i = 0;
    $("#tabla_personas_reclaman").find(".fila_personas_reclaman").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_personas_reclaman"));
        }
        i++;
    });

    //el div de error se oculta
    $("#divMenDiag").css("display", "none");
    $("#divMenProc").css("display", "none");
    $("#divMenEspe").css("display", "none");

    //se quita el readonly de los campos
    $("#pac_doctxtNumDoc").attr("readonly", false);
    $("#egr_histxtNumHis").attr("readonly", false);
    $("#egr_ingtxtNumIng").attr("readonly", false);
    $("#ing_feitxtFecIng").attr("readonly", false);
    $("#ing_hintxtHorIng").attr("readonly", false);

    //Se oculta todos los acordeones
    //$( "[acordeon]" ).accordion( "option", "active", false );

    //se pone el primer acordeon abierto desde el inicio
    //$( "#div_datos_ing_egr" ).accordion( "option", "active", 0 );
}

function diferenciaDias() {
    //Obtiene los datos del formulario
    CadenaFecha1 = $('#egr_feetxtFecEgr').val().split("-"); //fecha egreso
    CadenaFecha2 = $('#ing_feitxtFecIng').val().split("-"); //fecha ingreso

    //formato que llega año-mes-dia
    //formato para calculo dia-mes-año
    CadenaFecha1Formato = CadenaFecha1[2] + "-" + CadenaFecha1[1] + "-" + CadenaFecha1[0];
    CadenaFecha2Formato = CadenaFecha2[2] + "-" + CadenaFecha2[1] + "-" + CadenaFecha2[0];

    //Obtiene dia, mes y año
    var fecha1 = new fecha(CadenaFecha1Formato);
    var fecha2 = new fecha(CadenaFecha2Formato);

    //Obtiene objetos Date
    var miFecha1 = new Date(fecha1.anio, fecha1.mes - 1, fecha1.dia);
    var miFecha2 = new Date(fecha2.anio, fecha2.mes - 1, fecha2.dia);

    var horaIngreso = $("#ing_hintxtHorIng").val().split(":");
    var horaEgreso = $("#egr_hoetxtHorEgr").val().split(":");
    miFecha1.setHours(horaEgreso[0]);
    miFecha1.setMinutes(horaEgreso[1]);
    miFecha2.setHours(horaIngreso[0]);
    miFecha2.setMinutes(horaIngreso[1]);

    //Resta fechas y redondea
    var diferencia = miFecha1.getTime() - miFecha2.getTime();
    var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
    var dias_float = diferencia / (1000 * 60 * 60 * 24);
    return (dias_float);
}

function calcularEstancia(cambioFecha) {
    //dias_float = diferenciaDias();
    //Obtiene los datos del formulario
    CadenaFecha1 = $('#egr_feetxtFecEgr').val().split("-"); //fecha egreso
    CadenaFecha2 = $('#ing_feitxtFecIng').val().split("-"); //fecha ingreso

    //formato que llega año-mes-dia
    //formato para calculo dia-mes-año
    CadenaFecha1Formato = CadenaFecha1[2] + "-" + CadenaFecha1[1] + "-" + CadenaFecha1[0];
    CadenaFecha2Formato = CadenaFecha2[2] + "-" + CadenaFecha2[1] + "-" + CadenaFecha2[0];

    //Obtiene dia, mes y año
    var fecha1 = new fecha(CadenaFecha1Formato);
    var fecha2 = new fecha(CadenaFecha2Formato);

    //Obtiene objetos Date
    var miFecha1 = new Date(fecha1.anio, fecha1.mes - 1, fecha1.dia);
    var miFecha2 = new Date(fecha2.anio, fecha2.mes - 1, fecha2.dia);

    var horaIngreso = $("#ing_hintxtHorIng").val().split(":");
    var horaEgreso = $("#egr_hoetxtHorEgr").val().split(":");
    miFecha1.setHours(horaEgreso[0]);
    miFecha1.setMinutes(horaEgreso[1]);
    miFecha2.setHours(horaIngreso[0]);
    miFecha2.setMinutes(horaIngreso[1]);

    //Resta fechas y redondea
    var diferencia = miFecha1.getTime() - miFecha2.getTime();
    var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
    var dias_float = diferencia / (1000 * 60 * 60 * 24);
    var num_digitos = dias_float.toString().length;
    num_digitos = num_digitos + 2;
    var indexpunto = dias_float.toString().indexOf(".");
    if (indexpunto > 0) {
        num_digitos = indexpunto + 2;
    }
    dias_float = dias_float.toPrecision(num_digitos);
    var segundos = Math.floor(diferencia / 1000);
    // alert ('La diferencia es de ' + dias + ' dias,\no ' + segundos + ' segundos.')
    $("#egr_esttxtestan").val(dias_float);
    if ($("#egr_caeselCauEgr").val() != "" && cambioFecha == "si") {//--> 2014-11-25 tiene que validar la causa del egreso cuando se modifique alguna de las fechas
        validarTiempoEgreso($("#egr_caeselCauEgr"));
    }

}

function fecha(cadena) {

    //Separador para la introduccion de las fechas
    var separador = "-";
    //Separa por dia, mes y año
    if (cadena.indexOf(separador) != -1) {
        var posi1 = 0;
        var posi2 = cadena.indexOf(separador, posi1 + 1);
        var posi3 = cadena.indexOf(separador, posi2 + 1);
        this.dia = cadena.substring(posi1, posi2);
        this.mes = cadena.substring(posi2 + 1, posi3);
        this.anio = cadena.substring(posi3 + 1, cadena.length);
    } else {
        this.dia = 0;
        this.mes = 0;
        this.anio = 0;
    }
}

var modoConsulta = false;
var consultaEgreso = false;

function enviarDatos() {

    if (informacionIngresos.numRegistrosIng == undefined) {
        alerta("ERROR. No existen registros del paciente");
        return;
    }
    console.log("informacionIngresos = " + JSON.stringify(informacionIngresos, null, 4));


    if ($("input[type='hidden'][ux='_ux_egrseg']").val() == "") {//-> si el servicio de egreso para unix, está vacio pongo el que viene por defecto de la 18
        $("input[type='hidden'][ux='_ux_egrseg']").val($("input[name='ser_egrradio']:checked").val())
    }

    if (!respuestaValidaCBM()) {
        alerta("Debe selecionar una causa basica de muerte (CBM) debido a que la causa de egreso es muerte ");
        return;
    }

    mensajeProcedimientoPrincipal = "";
    console.log("antes de entrar al ajax ");
    if (!modoConsulta || (informacionIngresos.numRegistrosIng[$("#egr_histxtNumHis").val()] == $("#egr_ingtxtNumIng").val() && informacionIngresos.infoing[informacionIngresos.posAct].pac_act == 'off') && informacionIngresos.numRegistrosIng === undefined) {
        console.log("dentro del ajax");

        var datosLlenos = $('#forEgresos').valid();
        var mensajeError = "";
        var faltanServicios = " debe tener m\xednimo un servicio de origen ";
        var diagsSinSer = 0;
        var espSinSer = 0;
        var procSinServicio = 0;
        if (!($("#egresoUrgencias").val() == "on" && $("#egreso_automatico").val() == "on" && $("#funcionarioRegistros").val() != "on")) {
            iniciarMarcaAqua($('#forEgresos'));
        }


        if (datosLlenos) {
            console.log("datos llenos: 1 ");
            var validacion = validarCampos($("#div_egresos"));
            //-->  todos los diagnósticos deben tener mínimo un servicio
            if (validacion) {
                console.log("datos llenos: 2 ");
                $("td[name='contenedor_servicios_ocultos']").each(function () {
                    servicios = $(this).find("input[type='hidden'][name$='_hidCodSer'][value!='']").length;
                    if (servicios == 0 || servicios == undefined) {
                        validacion = true;
                        if ($(this).attr("nombreCampos") == "sed_ser_hidCodSer") {
                            diagsSinSer++;
                        } else {
                            espSinSer++;
                        }
                        //$(this).parent().find("td[tipo='td_adicionar']").addClass("faltantes");
                    }
                });
                // se miran los procedimientos agregados, y si tiene alguno, se verifica que este tenga un centro de costos asociado
                $("input[name='pro_cod_txtCodPro'][value!='']").each(function () {
                    servicio = $(this).parent().parent().nextAll("tr").eq(5).find("input[type='hidden'][name='pro_ser_hidCodSer']").val();
                    if ($.trim(servicio) == "") {
                        validacion = false;
                        procSinServicio++;
                    }
                })

                // si tiene algun procedimiento verificamos que alguno de ellos sea principal
                mensajeProcedimientoPrincipal = "";
                procedimientosAguardar = $("input[name='pro_cod_txtCodPro'][value!='']").length;
                if (procedimientosAguardar > 0) {
                    procedimientosPrincipales = $("[name='pro_tip_selTipPro']>option:selected[value='P']").length;
                    if (procedimientosPrincipales <= 0) {
                        validacion = false;
                        mensajeProcedimientoPrincipal = " Alguno de los procedimientos debe ser el principal";
                    }
                }
            }
            if (diagsSinSer == 0 && espSinSer == 0) {
                faltanServicios = "";
                faltanSerDiag = "";
                faltanSerEsp = "";
            }
            if (diagsSinSer > 0) {
                faltanServicios = "Cada diagnostico " + faltanServicios;
            }
            if (espSinSer > 0) {
                if (diagsSinSer > 0) {
                    faltanServicios = " Cada Especialidad y " + faltanServicios;
                } else {
                    faltanServicios = "Cada Especialidad " + faltanServicios;
                }
            }

            if (procSinServicio > 0) {
                if (diagsSinSer == 0 && espSinSer == 0) {
                    faltanServicios = "Cada Procedimiento debe tener un servicio asociado";
                } else {
                    faltanServicios = faltanServicios + ", Cada Procedimiento Debe tener un servicio asociado ";
                }
            }

            var diasIngreso_egreso = diferenciaDias();

            // validacion=true;
            // var validacionDiag = validacionDiagnosticos();
            console.log("datos llenos: 3 " + validacion + " diasIngreso_egreso" + diasIngreso_egreso);
            if (validacion && diasIngreso_egreso >= 0) {

                $("#tabla_personas_autorizadas").find("[name=dau_tip]").val("1");
                $("#tabla_personas_reclaman").find("[name=dau_tip]").val("2");

                //A todos los campos que tengan marca de agua y esten deshabilitado, les borro la marca de agua(msgerror)
                $("[aqua]:disabled").each(function () {
                    if ($(this).val() == $(this).attr(this.aqAttr)) {
                        $(this).val('');
                    }
                });

                var objJson = cearUrlPorCamposJson($("#div_egresos"), 'id');
                objJson = cearUrlPorCamposJson($("#div_egresos"), 'ux', objJson);

                objJson.accion = "guardarDatos";    //agrego un parametro más
                objJson.wbasedato = $("#wbasedato").val();
                objJson.consultaAjax = "";
                objJson.historia = $("#egr_histxtNumHis").val();
                objJson.ingreso = $("#egr_ingtxtNumIng").val();
                objJson.documento = $("#pac_doctxtNumDoc").val();
                objJson.tipodoc = $("#pac_tdoselTipoDoc").val();
                objJson.wemp_pmla = $("#wemp_pmla").val();
                objJson.cco_egreso = $("#cco_egreso").val();

                /*Diagnosticos*/
                objJson.diagnosticos = {};
                objJson.servDianosticos = {};
                objJson.servEspecialidad = {};


                $(".fila_diagnosticos").each(function (index) {
                    objJson.diagnosticos[index] = cearUrlPorCamposJson(this, 'name');
                    objJson.servDianosticos[index] = {};
                    $(this).find("td[name='contenedor_servicios_ocultos']").find("input[type='hidden']").each(function (numdiagnostico) {
                        objJson.servDianosticos[index][numdiagnostico] = cearUrlPorCamposJson(this, 'name');
                    })
                });
                /*Fin Diagnosticos*/

                /*Procedimientos*/
                objJson.procedimientos = {};

                $(".fila_procedimientos").each(function (index) {
                    objJson.procedimientos[index] = cearUrlPorCamposJson(this, 'name');
                });
                /*Fin Procedimientos*/

                /*Especialidades*/
                objJson.especialidades = {};

                $(".fila_especialidades").each(function (index) {
                    objJson.especialidades[index] = cearUrlPorCamposJson(this, 'name');
                    objJson.servEspecialidad[index] = {};
                    $(this).find("td[name='contenedor_servicios_ocultos']").find("input[type='hidden']").each(function (numespecialidad) {
                        objJson.servEspecialidad[index][numespecialidad] = cearUrlPorCamposJson(this, 'name');
                    })
                });
                /*Fin Especialidades*/

                /*Personas autorizadas*/
                objJson.personasautorizadas = {};

                $(".fila_personas_autorizadas").each(function (index) {
                    objJson.personasautorizadas[index] = cearUrlPorCamposJson(this, 'name');
                });
                /*Fin Personas autorizadas*/

                /*Personas reclaman*/
                objJson.personasreclaman = {};

                $(".fila_personas_reclaman").each(function (index) {
                    objJson.personasreclaman[index] = cearUrlPorCamposJson(this, 'name');
                });
                /*Fin Personas reclaman*/

                /*Servicios*/
                objJson.servicios = {};


                $(".fila_servicios").each(function (index) {
                    objJson.servicios[index] = cearUrlPorCamposJson(this, 'name');
                    if ($(this).find("[name='ser_egrradio']").is(":checked")) {
                        objJson.servicios[index]['ser_egrradio'] = "on";
                    } else {
                        objJson.servicios[index]['ser_egrradio'] = "off";
                    }
                });
                /*Fin Servicios*/

                objJson.diagnosticosux = {};
                /***************************************************
                 * Enviar datos a unix
                 ***************************************************/
                $(".fila_diagnosticos").each(function (index) {
                    objJson.diagnosticosux[index] = cearUrlPorCamposJson($("[ux][value!='']", this), 'ux');
                });

                objJson.procedimientosux = {};

                $(".fila_procedimientos").each(function (index) {
                    objJson.procedimientosux[index] = cearUrlPorCamposJson($("[ux][value!='']", this), 'ux');
                });

                objJson.especialidadesux = {};

                $(".fila_especialidades").each(function (index) {
                    objJson.especialidadesux[index] = cearUrlPorCamposJson($("[ux][value!='']", this), 'ux');
                });
                /**************Fin de enviar datos unix***************************************************/

                //A todos los campos que tengan marca de agua y esten deshabilitado, le pongo la marca de agua
                $("[aqua]:disabled").each(function () {
                    if ($(this).val() == '') {
                        $(this).val($(this).attr(this.aqAttr));
                    }
                });

                //--> para guardar el servicio de egreso
                /* var servicioEgresoEncontrado = false;
            objJson.servicioEgreso       = {};
            $("input[name='ser_egrradio']:checked").each(function( index ){
            servicioEgresoEncontrado = true;
            objJson.servicioEgreso[index] = cearUrlPorCamposJson( $( "[ux][value!='']", this ), 'ux' );
            return;
            });

            if( !servicioEgresoEncontrado ){
            objJson.servicioEgreso[0] = cearUrlPorCamposJson( $( "[ux][value!='']", $("#cco_egreso").parent() ), 'ux' );
            }*/


                //-->
                /*$.post("egreso_erp.php",
            objJson,
            async   : true,
            function(data){

                if( isJSON(data) == false ){
                    alert("RESPUESTA NO ESPERADA\n"+data);
                    return;
                }
                data = $.parseJSON(data);

                if( data.error == 1 )
                {
                    if (data.mensaje != '')
                    {
                        alert( data.mensaje );
                    }
                }
                else
                {
                    if( data.mensaje != '' )
                    {

                        alert( data.mensaje );
                        //Se oculta todos los acordeones
                        $( "[acordeon]" ).accordion( "option", "active", false );

                        //Se muestra el acordeon de DATOS DE INGRESO - DATOS EGRESO
                        $( "#div_datos_ing_egr" ).accordion( "option", "active", 0 );
                        // //se llenan los campos de historia,ingreso,documento despues de guardar
                        // $("#ing_histxtNumHis").val(data.historia);
                        // $("#ing_nintxtNumIng").val(data.ingreso);
                        // $("#pac_doctxtNumDoc").val(data.documento);
                        // //se ponen documento,historia,ingreso readonly
                        // $('#pac_doctxtNumDoc').attr("readonly", true);
                        // $('#ing_histxtNumHis').attr("readonly", true);
                        // $('#ing_nintxtNumIng').attr("readonly", true);
                    }
                    // //Al guardar los datos se borra el log
                    // borrarLog( $( "#key" ) );
                }
            }
            );*/
                $("#btnEgresar").attr("disabled", true);
                $("#btnAnular").attr("disabled", true);

                console.log("objJson = " + JSON.stringify(objJson, null, 4));

                $.ajax({
                    url: "egreso_erp.php",
                    type: "POST",
                    async: false,
                    data: objJson,

                    success: function (data) {
                        if (isJSON(data) == false) {
                            alerta("RESPUESTA NO ESPERADA\n" + data);
                            return;
                        }
                        data = $.parseJSON(data);

                        if (data.error == 1) {
                            if (data.mensaje != '') {
                                alerta($.trim(data.mensaje));
                            }
                        } else {
                            if (data.mensaje != '') {

                                //Se oculta todos los acordeones
                                $("[acordeon]").accordion("option", "active", false);

                                //Se muestra el acordeon de DATOS DE INGRESO - DATOS EGRESO
                                $("#div_datos_ing_egr").accordion("option", "active", 0);
                                // //se llenan los campos de historia,ingreso,documento despues de guardar
                                // $("#ing_histxtNumHis").val(data.historia);
                                // $("#ing_nintxtNumIng").val(data.ingreso);
                                // $("#pac_doctxtNumDoc").val(data.documento);
                                // //se ponen documento,historia,ingreso readonly
                                // $('#pac_doctxtNumDoc').attr("readonly", true);
                                // $('#ing_histxtNumHis').attr("readonly", true);
                                // $('#ing_nintxtNumIng').attr("readonly", true);
                                setTimeout(function () {
                                    alerta($.trim(data.mensaje));
                                }, 1000);

                            }
                            // //Al guardar los datos se borra el log
                            // borrarLog( $( "#key" ) );
                        }
                        $("#btnEgresar").attr("disabled", false);
                        $("#btnAnular").attr("disabled", false);
                    }
                });


            }//validacion
            else {
                if (diasIngreso_egreso >= 0 && $.trim(faltanServicios) != "" || !validacion) {

                    console.log('x');
                    var campos = getNombresCamposError();
                    mensajeError = "Hay datos incompletos, por favor verifique los campos de color amarillo ----> \n" + campos;
                } else if (diasIngreso_egreso < 0) {
                    mensajeError = "Fecha y Hora de egresos Incorrectos.";
                }
                if (faltanServicios != "") {
                    mensajeError = faltanServicios;
                }

                if (mensajeProcedimientoPrincipal != "" && mensajeProcedimientoPrincipal != undefined) {
                    mensajeError += "\n " + mensajeProcedimientoPrincipal + " \n";
                }
                alerta(mensajeError);
            } //validacion false
        }//datos llenos
        else {
            mensajeError = "Hay datos incompletos, por favor verifique los campos de color amarillo";
            alerta(mensajeError);
        } //datos llenos
    } //validacion modo consulta
    else {
        alerta("Solo se permite actualizar el ultimo ingreso");
    }
}

function validacionPrinSecu(campo, divRefer, prefijo, tipo, divMens) { //alert(campo+"-"+divRefer+"-"+prefijo+"-"+tipo+"-"+divMens);
    var valorSelect = 0;
    var diagOk = true;
    var idSelect = "";
    var mensaje = "";
    /**Validacion diagnostico principal**/

    $("#" + divRefer).find("select[id^=" + prefijo + "]").children('option:selected').each(function () {
        if ($(this).val() == "P") {
            valorSelect++;

        }
    });

    id = $(campo).attr("id"); //id del select que lo llamo
    if (valorSelect == 1) {
        diagOk = true;
    } else if (valorSelect == 0) {
        diagOk = false;
        mensaje = "Debe seleccionar al menos un " + tipo + " principal";
    } else if (valorSelect > 1) {
        diagOk = false;
        mensaje = "Debe seleccionar solo un " + tipo + " principal";
        $("#" + id).val("");
    }
    //si esta en seleccione no muestra el mensaje
    if ($("#" + id).val() == "") {
        $("#" + divMens).css("display", "none");
    }
    /** Fin Diagnostico principal**/

    //si hay mensaje muestra el div
    if (mensaje != '') {
        $("#" + divMens).css("display", "");
        $("#" + divMens).html(mensaje);
    } else {
        $("#" + divMens).css("display", "none");
    }
    return diagOk;
}

function validacionPrinSecu2(div_fila, campo) {
    var llegoCampo = false;
    if (campo != undefined) {
        campo = jQuery(campo);
        llegoCampo = true;
    }
    var cantidadPrincipal = 0;
    var cantidadFilas = 0;
    var auxiliar;
    $("." + div_fila).find(".principalsecundario").each(function () {
        cantidadFilas++;
        if (cantidadFilas == 1)
            auxiliar = $(this);
        if ($(this).val() == "P")
            cantidadPrincipal++;
        if (llegoCampo == false) {
            campo = $(this);
            //llegoCampo = true;
        }
    });
    //alert("campos: " + campo);

    if (cantidadPrincipal == 0) {
        auxiliar.val("P");
    } else if (cantidadPrincipal == 1 && cantidadFilas > 1) {
        campo.val("S").removeClass("campoRequerido");
    } else if (cantidadPrincipal == 1 && cantidadFilas == 1) {
        campo.val("P").removeClass("campoRequerido");
    } else if (cantidadPrincipal > 1) {
        //Quitarle el principal al otro, y poner el nuevo principal de primero
        if (llegoCampo == true && campo.val() == "P") { //Si el valor asignado fue manual
            var tablaPrincipal = campo.parents(".fila_principal").parent();
            tablaPrincipal.find(".principalsecundario").val("S"); //llevar valor secundario a todos
            campo.val("P");
            var filaPrincipal = campo.parents(".fila_principal");
            filaPrincipal.insertAfter(tablaPrincipal.find("tr:first")); //Pongo el principal de primero
        }
    }
    if (div_fila == "fila_diagnosticos") {
        servicioEgreso = $("input[name='ser_egrradio']:checked").val();
        tablaDiaPpal = $("select[name='dia_tip_selTipDia']>option[value='P']:selected").parent().parent().parent().parent().parent();
        contenedorServiciosOscultosDiagnosticoPpal = $("td[name='contenedor_servicios_ocultos']", tablaDiaPpal);
        nombreCampoAux = $(contenedorServiciosOscultosDiagnosticoPpal).attr("nombrecampos");
        serAux = $("[value='" + servicioEgreso + "']", contenedorServiciosOscultosDiagnosticoPpal).length;
        if ((serAux == 0) && servicioEgreso != "" && servicioEgreso != undefined) {
            var serEgreso = "<input id=" + nombreCampoAux + " type='hidden' name='" + nombreCampoAux + "' value='" + servicioEgreso + "'>";
            $(contenedorServiciosOscultosDiagnosticoPpal).html($(contenedorServiciosOscultosDiagnosticoPpal).html() + serEgreso);
        }
    }
}

function countRepeated(array) {
    var r = arguments[1] || [], i = 0;
    for (; i < array.length; i++) {
        if (Object.prototype.hasOwnProperty.call(array, i)) {
            if (array[i] instanceof Array) {
                r = countRepeated(array[i], r);
            } else {
                if (r[array[i]])
                    r[array[i]]++;
                else
                    r[array[i]] = 1;
            }
        }
    }
    return r;
}

//mostrar datos para antes del egreso
function mostrarDatos() {
    var aplicacion = $("#aplicacion").val();
    if (aplicacion == "") {
        $("#tabla_observacion_diagnostico").css("display", "none");
        $("#tabla_observacion_procedimiento").css("display", "none");
    } else {
        $("#tabla_observacion_diagnostico").css("display", "");
        $("#tabla_observacion_procedimiento").css("display", "");
    }
    //Variable para saber si esta en modo consulta o no
    modoConsulta = true;

    var objJson = cearUrlPorCamposJson($("#div_egresos"), 'id');

    objJson.accion = "mostrarDatosAlmacenados"; //agrego un parametro más
    objJson.wbasedato = $("#wbasedato").val();
    objJson.aplicacion = $("#aplicacion").val();
    objJson.consultaAjax = "";
    objJson.historia = $("#egr_histxtNumHis").val();
    objJson.ingreso = $("#egr_ingtxtNumIng").val();
    objJson.documento = $("#pac_doctxtNumDoc").val();
    objJson.priApe = $("#pac_ap1txtPriApe").val();
    objJson.segApe = $("#pac_ap2txtSegApe").val();
    objJson.priNom = $("#pac_no1txtPriNom").val();
    objJson.segNom = $("#pac_no2txtSegNom").val();
    objJson.wemp_pmla = $("#wemp_pmla").val();
    objJson.cco_egreso = $("#cco_egreso").val();
    objJson.mostrarSalida = $("#mostrarSalida").val();

    /*validacion de todos los input para saber si tienen el mesaje de error
y si lo tiene se envia vacio*/
    $('input').each(function (n) {
        var id = this.id;
        var valor = $("#" + id).val();
        // var valormsgerror = $("#"+id).attr( "msgerror" );
        if (this.aqAttr)   //Solo si su valor es igual a la marca de agua, ya se mensaje de error(msgerror) o no
        {
            var valormsgerror = $("#" + id).attr(this.aqAttr);  //Se Busca la marca de agua

            if (valor == valormsgerror) {
                objJson[id] = '';
            }
        }
    });

    //Si el documento esta vació mando el numero de documento vacio
    if ($("#pac_doctxtNumDoc").val() == '' || $("#pac_doctxtNumDoc").val() == $("#pac_doctxtNumDoc").attr("msgerror")) {
        objJson.documento = objJson.pac_doctxtNumDoc;
    }

    $.blockUI({message: "Por favor espere..."});
    $.post("egreso_erp.php",
        objJson,
        function (data) {
            if (isJSON(data) == false) {
                alerta("RESPUESTA NO ESPERADA\n" + data);
                return;
            }
            data = $.parseJSON(data);
            if (data.error == 1) {
                alerta(data.mensaje);
                $.unblockUI();
            } else {
                $.unblockUI();
                if (data.mensaje != '')
                    alerta(data.mensaje);


                if (data.infoing) {
                    informacionIngresos = data;
                    informacionIngresos.regTotal = data.infoing.length;
                    informacionIngresos.posAct = data.infoing.length - 1;
                    if (informacionIngresos.regTotal > 0) {
                        $("#bot_navegacion").css("display", "");
                        $("#bot_navegacion1").css("display", "");
                    } else {
                        $("#bot_navegacion").css("display", "none");
                        $("#bot_navegacion1").css("display", "none");
                    }
                    navegacionIngresos(0);

                    //se colocan los campos cedula,historia,ingreso read only para que no se puedan modificar
                    $("#pac_doctxtNumDoc").attr("readonly", true);
                    $("#egr_histxtNumHis").attr("readonly", true);
                    $("#egr_ingtxtNumIng").attr("readonly", true);
                    $("#ing_feitxtFecIng").attr("readonly", true);
                    $("#ing_hintxtHorIng").attr("readonly", true);

                    modoConsulta = false;

                    /** Habilitamos o deshabilitamos los checkboxs de causa basica de muerte **/
                    habilitarDeshabilitarCBM(null, data.infoing[0].egr_caeselCauEgr_text);
                }
            }
        }
    );
}


var informacionIngresos = '';

function navegacionIngresos(incremento) {
    var wbasedato = $("#wbasedato").val();
    var wemp_pmla = $("#wemp_pmla").val();
    var aplicacion = $("#aplicacion").val();

    /*codigo para eliminar las filas antes de la navegacion*/
    var i = 0;
    $("#tabla_diagnostico").find(".fila_diagnosticos").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_diagnostico"));
        }
        i++;
    });
    i = 0;
    $("#tabla_procedimiento").find(".fila_procedimientos").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_procedimiento"));
        }
        i++;
    });
    i = 0;
    $("#tabla_especialidad").find(".fila_especialidades").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_especialidad"));
        }
        i++;
    });
    i = 0;
    $("#tabla_servicio").find(".fila_servicios").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_servicio"));
        }
        i++;
    });
    i = 0;
    $("#tabla_personas_autorizadas").find(".fila_personas_autorizadas").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_personas_autorizadas"));
        }
        i++;
    });
    i = 0;
    $("#tabla_personas_reclaman").find(".fila_personas_reclaman").each(function () {
        if (i > 0) {
            $(this).remove();
        } else {
            $(this).find("input[type=text],input[type=hidden],select").val("");
            resetAqua($("#tabla_personas_reclaman"));
        }
        i++;
    });

    //Para que deje por defecto la opción Principal
    validacionPrinSecu2("fila_diagnosticos");
    validacionPrinSecu2("fila_procedimientos");
    validacionPrinSecu2("fila_especialidades");
    /**/

    var data = informacionIngresos;

    if (data.posAct + incremento < informacionIngresos.regTotal && data.posAct + incremento >= 0) {
        data.posAct = data.posAct + incremento;

        // setDatos( data.infopac, $( "#div_admisiones" ), 'id' )  ;
        setDatos(data.infoing[data.posAct], $("#div_egresos"), 'id');

        //para mostrar complicaciones en datos basicos
        $("[value=" + informacionIngresos.infoing[data.posAct].egr_comradCon + "]", $("#div_int_ing_egr")).attr("checked", true)

        //para mostrar la ubicacion del expediente fisico
        $("[value=" + informacionIngresos.infoing[data.posAct].egr_uexradUbiExp + "]", $("#tabla_expediente")).attr("checked", true)

        /** para mostrar diagnosticos**/
        if (data.infoing[data.posAct]['diagnosticos'] === undefined) {

        } else {
            if (consultaEgreso == false) //consulta datos antes del egreso
            {
                //para consultar informacion antes del egreso
                /*if ( (modoConsulta==true && aplicacion == "") || $("#ccoAyuda").val() == "on" || $("#egresoUrgencias").val() == "on" ) //clisur
            {*/
                for (var i = 0; i < data.infoing[data.posAct]['diagnosticos'].length - 1; i++) {
                    addFila2('tabla_diagnostico');
                }

                setTimeout(function () {

                    for (var i = 0; i < data.infoing[data.posAct]['diagnosticos'].length; i++) {

                        var diagnosticos = data.infoing[data.posAct]['diagnosticos'][i];
                        var fila = $("#tabla_diagnostico")[0].rows[1 + i];

                        setDatos(diagnosticos, fila, 'name');
                        resetAqua($("#tabla_diagnostico"));

                        $(fila).find("[name='contenedor_servicios_ocultos']").attr("medico", data.infoing[data.posAct]['diagnosticos'][i]['dia_med']);

                        if (data.infoing[data.posAct]['diagnosticos'][i]['servicios'] !== undefined) {
                            for (var l = 0; l < data.infoing[data.posAct]['diagnosticos'][i]['servicios'].length; l++) {
                                $(fila).find("td[name='contenedor_servicios_ocultos']").each(function () {
                                    var aux = $("[name='sed_ser_hidCodSer'][value='" + data.infoing[data.posAct]['diagnosticos'][i]['servicios'][l].Sed_ser + "']", this).length;
                                    if (aux == 0) {
                                        servicioNuevo = "<input type='hidden' id='sed_ser_hidCodSer' name='sed_ser_hidCodSer' value='" + data.infoing[data.posAct]['diagnosticos'][i]['servicios'][l].Sed_ser + "' >";
                                        $(this).html($(this).html() + servicioNuevo);
                                    }
                                });
                            }
                        }

                    }
                }, 0);
                //}
                /*else if (modoConsulta==true && aplicacion != "") //cliame
            {

            for ( var i=0; i<data.infoing[ data.posAct ]['diagnosticos'].length;i++)
            {
                var diagnosticos = data.infoing[ data.posAct ]['diagnosticos'][i]["txtaObsDia"]+"\n";
                ///$( "#txtaObsDia" ).val( $( "#txtaObsDia" ).val()+diagnosticos.toLowerCase() );2018-09-04 esto ya no es necesario
            }
            }*/
            } else {  //trae datos de egresos ya realizados para cliame y clisur
                for (var i = 0; i < data.infoing[data.posAct]['diagnosticos'].length - 1; i++) {
                    addFila2('tabla_diagnostico');
                }
                setTimeout(function () {
                    for (var i = 0; i < data.infoing[data.posAct]['diagnosticos'].length; i++) {
                        var diagnosticos = data.infoing[data.posAct]['diagnosticos'][i];
                        var fila = $("#tabla_diagnostico")[0].rows[1 + i];
                        setDatos(diagnosticos, fila, 'name');
                        $(fila).find("[name='contenedor_servicios_ocultos']").attr("medico", data.infoing[data.posAct]['diagnosticos'][i]['dia_med']);
                        resetAqua($("#tabla_diagnostico"));
                        if (data.infoing[data.posAct]['diagnosticos'][i]['servicios'] !== undefined) {
                            for (var l = 0; l < data.infoing[data.posAct]['diagnosticos'][i]['servicios'].length; l++) {
                                $(fila).find("td[name='contenedor_servicios_ocultos']").each(function () {
                                    if ($.trim(data.infoing[data.posAct]['diagnosticos'][i]['servicios'][l].Sed_ser) != "" && data.infoing[data.posAct]['diagnosticos'][i]['servicios'][l].Sed_ser != undefined) {
                                        var aux = $("[name='sed_ser_hidCodSer'][value='" + data.infoing[data.posAct]['diagnosticos'][i]['servicios'][l].Sed_ser + "']", this).length;
                                        if (aux == 0) {
                                            servicioNuevo = "<input type='hidden' id='sed_ser_hidCodSer' name='sed_ser_hidCodSer' value='" + data.infoing[data.posAct]['diagnosticos'][i]['servicios'][l].Sed_ser + "' >";
                                            $(this).html($(this).html() + servicioNuevo);
                                        }
                                    }
                                });
                            }
                        }
                    }
                }, 0);
            }
            $("input[type='hidden'][name='sed_ser_hidCodSer'][value='']").remove();
        }

        /** fin mostrar diagnosticos**/

        /** para mostrar procedimientos**/
        if (data.infoing[data.posAct]['procedimientos'] === undefined) {

        } else {
            if (consultaEgreso == false) //consulta datos antes del egreso
            {
                if (modoConsulta == true && aplicacion == "") {
                    for (var i = 0; i < data.infoing[data.posAct]['procedimientos'].length - 1; i++) {
                        //addFila('tabla_procedimiento',"",2,wbasedato,wemp_pmla);  //REVISAR
                        addFila2('tabla_procedimiento');
                    }

                    setTimeout(function () {
                        for (var i = 0; i < data.infoing[data.posAct]['procedimientos'].length; i++) {
                            var procedimientos = data.infoing[data.posAct]['procedimientos'][i];
                            var fila = $("#tabla_procedimiento")[0].rows[1 + i];
                            setDatos(procedimientos, fila, 'name');
                            $(fila).find("[name='pro_ser_hidCodSer']").attr("medico", data.infoing[data.posAct]['procedimientos'][i]['pro_med']);
                            resetAqua($("#tabla_procedimiento"));
                        }
                    }, 0);
                } else if (modoConsulta == true && aplicacion != "") {
                    for (var i = 0; i < data.infoing[data.posAct]['procedimientos'].length - 1; i++) {
                        //addFila('tabla_procedimiento',"",2,wbasedato,wemp_pmla);  //REVISAR
                        addFila2('tabla_procedimiento');
                    }
                    arrayProcedimientos = new Array();
                    for (var i = 0; i < data.infoing[data.posAct]['procedimientos'].length; i++) {
                        var procedimientos = data.infoing[data.posAct]['procedimientos'][i]["txtaObsPro"];
                        if (procedimientos == undefined) {
                            procedimientos = "";
                        } else {
                            procedimientos = procedimientos + "\n";
                        }
                        $("#txtaObsPro").val($("#txtaObsPro").val() + procedimientos.toLowerCase());
                        var procedimientos = data.infoing[data.posAct]['procedimientos'][i];
                        var fila = $("#tabla_procedimiento")[0].rows[1 + i];
                        setDatos(procedimientos, fila, 'name');
                        $(fila).find("[name='pro_ser_hidCodSer']").attr("medico", data.infoing[data.posAct]['procedimientos'][i]['pro_med']);

                        if (data.infoing[data.posAct]['procedimientos'][i]['pro_ser'] !== undefined) {
                            //-->Se agrega el servicio como visitado:
                            servicioAux = data.infoing[data.posAct]['procedimientos'][i]['pro_ser'];
                            encontrado = jQuery.inArray(servicioAux, arrayProcedimientos);

                            if (encontrado === -1) {
                                posicionAux = arrayProcedimientos.length;
                                arrayProcedimientos[posicionAux] = servicioAux;
                                if ($(".fila_servicios").length == 1 && $(".fila_servicios").eq(0).find("input[name='ser_cod_hidCodSer']").val() == "") {
                                } else {
                                    $("#spn_tabla_servicio").click();
                                }

                                camposServicios = ($(".fila_servicios").length) - 1;
                                var datoServicio = String(data.infoing[data.posAct]['procedimientos'][i]['proSer']);
                                datoServicio = datoServicio.split("-");
                                codServicio = datoServicio[0];
                                desServicio = datoServicio[1];
                                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_txtCodSer']").val(codServicio);
                                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_txtCodSer']").removeClass("campoRequerido");
                                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_hidCodSer']").val(codServicio);
                                $(".fila_servicios").eq(camposServicios).find("input[name='ser_egrradio']").val(codServicio);
                                $(".fila_servicios").eq(camposServicios).find("input[name='DesSer_txtDesSer']").val(desServicio);
                                $(".fila_servicios").eq(camposServicios).find("input[name='DesSer_txtDesSer']").removeClass("campoRequerido");
                                var aux = {};
                                aux.ser_cod = codServicio;
                                aux.Desser = desServicio;
                                var posicion = data.infoing[data.posAct]['servicios'].length;
                                data.infoing[data.posAct]['servicios'][posicion] = aux;
                            }
                        }
                        resetAqua($("#tabla_procedimiento"));
                    }
                }
            } else {
                for (var i = 0; i < data.infoing[data.posAct]['procedimientos'].length - 1; i++) {
                    addFila2('tabla_procedimiento');
                }

                setTimeout(function () {
                    for (var i = 0; i < data.infoing[data.posAct]['procedimientos'].length; i++) {
                        var procedimientos = data.infoing[data.posAct]['procedimientos'][i];
                        var fila = $("#tabla_procedimiento")[0].rows[1 + i];
                        setDatos(procedimientos, fila, 'name');
                        $(fila).find("[name='pro_ser_hidCodSer']").attr("medico", data.infoing[data.posAct]['procedimientos'][i]['pro_med']);
                        resetAqua($("#tabla_procedimiento"));
                    }
                }, 0);
            }
        }

        /** fin mostrar procedimientos**/

        /** para mostrar especialidades**/
        if (data.infoing[data.posAct]['especialidades'] === undefined) {

        } else {
            for (var i = 0; i < data.infoing[data.posAct]['especialidades'].length - 1; i++) {
                addFila2('tabla_especialidad');  //REVISAR
            }

            setTimeout(function () {
                for (var i = 0; i < data.infoing[data.posAct]['especialidades'].length; i++) {
                    var especialidad = data.infoing[data.posAct]['especialidades'][i];
                    var fila = $("#tabla_especialidad")[0].rows[1 + i];
                    setDatos(especialidad, fila, 'name');
                    resetAqua($("#tabla_especialidad"));
                    if (data.infoing[data.posAct]['especialidades'][i]['servicios'] !== undefined) {
                        $(fila).find("td[name='contenedor_servicios_ocultos']").attr("medico", data.infoing[data.posAct]['especialidades'][i]['esp_med'])
                        for (var l = 0; l < data.infoing[data.posAct]['especialidades'][i]['servicios'].length; l++) {
                            $(fila).find("td[name='contenedor_servicios_ocultos']").each(function () {
                                servicioNuevo = "<input type='hidden' id='see_ser_hidCodSer' name='see_ser_hidCodSer' value='" + data.infoing[data.posAct]['especialidades'][i]['servicios'][l].See_ser + "' >";
                                $(this).html($(this).html() + servicioNuevo);
                            });
                        }
                    }
                }
                $("input[type='hidden'][name='see_ser_hidCodSer'][value='']").remove();
            }, 0);
        }

        /** fin mostrar especialidades**/

        /** para mostrar servicios**/
        if (data.infoing[data.posAct]['servicios'] === undefined) {

        } else {
            var j = $("input[name='ser_cod_hidCodSer'][value!='']").length;

            filaServicioEgreso = 0;
            serviciosEgresoEncontrado = false;
            var servicioEgreso = $("#cco_egreso").val();
            var serEgresoBD = false;

            j = $("input[name='ser_cod_hidCodSer'][value!='']").length;

            for (var i = 0; i < data.infoing[data.posAct]['servicios'].length - 1; i++) {
                addFila2('tabla_servicio');  //REVISAR
            }
            setTimeout(function () {
                j = $("input[name='ser_cod_hidCodSer'][value!='']").length + 1;
                for (var i = 0; i < data.infoing[data.posAct]['servicios'].length; i++) {
                    indiceAux = j + i;
                    var servicio = data.infoing[data.posAct]['servicios'][i];
                    console.log("servicio buscado:" + servicio.ser_cod);
                    var cantidad = $("#div_datos_servicios").find(":hidden[name='ser_cod_hidCodSer'][value='" + servicio.ser_cod + "']").length;
                    console.log("cantidad" + cantidad);
                    if (cantidad == 0) {
                        var fila = $("#tabla_servicio")[0].rows[indiceAux];
                        setDatos(servicio, fila, 'name');
                        resetAqua($("#tabla_servicio"));
                        if (servicio.ser_cod == servicioEgreso) {
                            serviciosEgresoEncontrado = true;
                        }
                        $("input[type='radio'][name^='ser_egrradio']", fila).val(servicio.ser_cod)
                        if (servicio.ser_egrradio == "on") {
                            serEgresoBD = true;
                            $("input[type='radio'][name^='ser_egrradio']", fila).attr("checked", true);

                        } else {
                            $("input[type='radio'][name^='ser_egrradio']", fila).attr("checked", false);
                        }
                    } else {
                        if ($("#tabla_servicio")[0].rows[indiceAux] !== undefined) {
                            $("#tabla_servicio")[0].rows[indiceAux].remove();
                            j = j - 1;
                        }
                    }
                }
                if (!serEgresoBD) {
                    $("input[type='radio'][name^='ser_egrradio']").attr("name", "ser_egrradio");
                    $("input[name='ser_cod_hidCodSer'][value!='" + servicioEgreso + "']").parent().parent().find("input[type='radio'][name='ser_egrradio']").attr("checked", false);
                    $("input[name='ser_cod_hidCodSer'][value='" + servicioEgreso + "']").parent().parent().find("input[type='radio'][name='ser_egrradio']").attr("checked", true);
                }
                //--> agregar automáticamente servicio de egreso si no lo encontró
                //--> sino encuentro el servicio de egreso administrativo(traido de la 18, pregunto si ya hay alguno marcado, si es así entonces no lo modifico)
                if (!serviciosEgresoEncontrado) {
                    servicioEgreso = $("input[name='ser_egrradio']:checked").val();
                    if (servicioEgreso == undefined) {
                        $("input[type='checkbox'][name='chk_servicio_dia'][value='" + $("#cco_egreso").val() + "']").each(function () {
                            $(this).attr("checked", true);
                            fila = agregarQuitarMultiplesServiciosConsolidar(this, "", "");
                            $(".fila_servicios").eq(fila).find("input[name='ser_egrradio']").val(servicioEgreso);
                            $(".fila_servicios").eq(fila).find("input[name='ser_egrradio']").attr("checked", true);
                        });
                    } else {
                    }
                }
                servicioEgreso = $("input[name='ser_egrradio']:checked").val();
                tablaDiaPpal = $("select[name='dia_tip_selTipDia']>option[value='P']:selected").parent().parent().parent().parent().parent();
                contenedorServiciosOscultosDiagnosticoPpal = $("td[name='contenedor_servicios_ocultos']", tablaDiaPpal);
                nombreCampoAux = $(contenedorServiciosOscultosDiagnosticoPpal).attr("nombrecampos");
                serAux = $("[value='" + servicioEgreso + "']", contenedorServiciosOscultosDiagnosticoPpal).length;
                if ((serAux == 0) && servicioEgreso != "" && servicioEgreso != undefined) {
                    var serEgreso = "<input id=" + nombreCampoAux + " type='hidden' name='" + nombreCampoAux + "' value='" + servicioEgreso + "'>";
                    $(contenedorServiciosOscultosDiagnosticoPpal).html($(contenedorServiciosOscultosDiagnosticoPpal).html() + serEgreso);
                }

            }, 0);

        }
        /** fin mostrar servios**/


        /** para mostrar personas autorizadas**/
        if (data.infoing[data.posAct]['personasautorizadas'] === undefined) {

        } else {
            for (var i = 0; i < data.infoing[data.posAct]['personasautorizadas'].length - 1; i++) {
                addFila2('tabla_personas_autorizadas');  //REVISAR
            }
            setTimeout(function () {
                for (var i = 0; i < data.infoing[data.posAct]['personasautorizadas'].length; i++) {
                    var servicio = data.infoing[data.posAct]['personasautorizadas'][i];
                    var fila = $("#tabla_personas_autorizadas")[0].rows[1 + i];
                    setDatos(servicio, fila, 'name');

                    resetAqua($("#tabla_personas_autorizadas"));
                }
            }, 0);
        }
        /** fin mostrar personas autorizadas**/

        /** para mostrar personas que reclaman**/
        if (data.infoing[data.posAct]['personasreclaman'] === undefined) {

        } else {
            for (var i = 0; i < data.infoing[data.posAct]['personasreclaman'].length - 1; i++) {
                addFila2('tabla_personas_reclaman');  //REVISAR
            }
            setTimeout(function () {
                for (var i = 0; i < data.infoing[data.posAct]['personasreclaman'].length; i++) {
                    var servicio = data.infoing[data.posAct]['personasreclaman'][i];
                    var fila = $("#tabla_personas_reclaman")[0].rows[1 + i];
                    setDatos(servicio, fila, 'name');

                    resetAqua($("#tabla_personas_reclaman"));
                }
            }, 0);
        }
        /** fin mostrar personas que reclaman**/

        calcularEstancia();

        var dateActual = $("#ing_feitxtFecIng").val().split("-");
        //                                                                                                  año             mes(0-11)       dia
        $("#egr_fiatxtFecInA").datepicker("option", "minDate", new Date(dateActual[0], dateActual[1] - 1, dateActual[2])); //menor

        //validaciones fecha egreso que sea mayor o igual a la fecha de ingreso
        var fechaIngreso = $("#ing_feitxtFecIng").val().split("-");
        $("#egr_feetxtFecEgr").datepicker("option", "minDate", new Date(fechaIngreso[0], fechaIngreso[1] - 1, fechaIngreso[2])); //menor

        //Muestra datos para el navegador inferior
        $("#spTotalReg").html(data.numRegistrosPac);// numero de registros encontrados en la busqueda
        $("#spTotalIng").html(data.numRegistrosIng[data.infoing[data.posAct].pac_his]); //total ingresos encontrados
        $("#spRegAct").html(data.numPosicionHistorias[data.infoing[data.posAct].pac_his] + 1); //resultado actual

        $("#spHisAct").html(data.infoing[data.posAct].pac_his); //historia del registro actual
        $("#spIngAct").html(data.infoing[data.posAct].ing_nin); //ingreso actual del registro actual
        $("#spTotalIng1").html(data.numRegistrosIng[data.infoing[data.posAct].pac_his]); //total ingresos por historia

        //Muestra datos para el navegador superior
        //$("#spTotalReg1").html(data.numRegistrosPac);// numero de registros encontrados en la busqueda
        $("#spTotalIng1").html(data.numRegistrosIng[data.infoing[data.posAct].pac_his]); //total ingresos encontrados
        $("#spRegAct1").html(data.numPosicionHistorias[data.infoing[data.posAct].pac_his] + 1); //resultado actual

        //$("#spHisAct1").html( data.infoing[ data.posAct ].pac_his); //historia del registro actual
        //$("#spIngAct1").html( data.infoing[ data.posAct ].ing_nin );  //ingreso actual del registro actual
        $("#spTotalIng11").html(data.numRegistrosIng[data.infoing[data.posAct].pac_his]); //total ingresos por historia


        resetAqua();
    }
}

//mostrar datos de egresos ya realizados
function mostrarDatosEgresos() {
    //se cambia el value del boton
    //if( $("#activacionEgresoAnulado").val() == "off" ){
    $("#btnEgresar").val("Actualizar Egreso");
    //}
    //se ocultan la tabla que contiene los textarea
    $("#tabla_observacion_diagnostico").css("display", "none");
    $("#tabla_observacion_procedimiento").css("display", "none");

    //Variable para saber si esta en modo consulta o no
    modoConsulta = true;
    consultaEgreso = true;

    var objJson = cearUrlPorCamposJson($("#div_egresos"), 'id');

    objJson.accion = "mostrarDatosAlmacenadosEgreso";   //agrego un parametro más
    objJson.wbasedato = $("#wbasedato").val();
    objJson.consultaAjax = "";
    objJson.historia = $("#egr_histxtNumHis").val();
    objJson.ingreso = $("#egr_ingtxtNumIng").val();
    objJson.documento = $("#pac_doctxtNumDoc").val();
    objJson.priApe = $("#pac_ap1txtPriApe").val();
    objJson.segApe = $("#pac_ap2txtSegApe").val();
    objJson.priNom = $("#pac_no1txtPriNom").val();
    objJson.segNom = $("#pac_no2txtSegNom").val();
    objJson.wemp_pmla = $("#wemp_pmla").val();
    objJson.mostrarSalida = $("#mostrarSalida").val();

    /*validacion de todos los input para saber si tienen el mesaje de error
y si lo tiene se envia vacio*/
    $('input').each(function (n) {
        var id = this.id;
        var valor = $("#" + id).val();
        // var valormsgerror = $("#"+id).attr( "msgerror" );
        if (this.aqAttr)   //Solo si su valor es igual a la marca de agua, ya se mensaje de error(msgerror) o no
        {
            var valormsgerror = $("#" + id).attr(this.aqAttr);  //Se Busca la marca de agua

            if (valor == valormsgerror) {
                objJson[id] = '';
            }
        }
    });

    //Si el documento esta vació mando el numero de documento vacio
    if ($("#pac_doctxtNumDoc").val() == '' || $("#pac_doctxtNumDoc").val() == $("#pac_doctxtNumDoc").attr("msgerror")) {
        objJson.documento = objJson.pac_doctxtNumDoc;
    }

    $.blockUI({message: "Por favor espere..."});

    $.post("egreso_erp.php",
        objJson,
        function (data) {

            if (isJSON(data) == false) {
                alerta("RESPUESTA NO ESPERADA " + data);
            }
            data = $.parseJSON(data);

            if (data.error == 1) {
                alerta(data.mensaje);
                $.unblockUI();
            } else {
                $.unblockUI();

                if (data.mensaje != '')
                    alerta(data.mensaje);

                if (data.infoing) {
                    informacionIngresos = data;
                    informacionIngresos.regTotal = data.infoing.length;
                    informacionIngresos.posAct = data.infoing.length - 1;

                    if (informacionIngresos.regTotal > 0) {
                        $("#bot_navegacion").css("display", "");
                        $("#bot_navegacion1").css("display", "");
                    } else {
                        $("#bot_navegacion").css("display", "none");
                        $("#bot_navegacion1").css("display", "none");
                    }
                    navegacionIngresos(0);
                    //se colocan los campos cedula,historia,ingreso read only para que no se puedan modificar
                    $("#pac_doctxtNumDoc").attr("readonly", true);
                    $("#egr_histxtNumHis").attr("readonly", true);
                    $("#egr_ingtxtNumIng").attr("readonly", true);
                    $("#ing_feitxtFecIng").attr("readonly", true);
                    $("#ing_hintxtHorIng").attr("readonly", true);

                    modoConsulta = false;

                    /** Habilitamos o deshabilitamos los checkboxs de causa basica de muerte **/
                    habilitarDeshabilitarCBM(null, data.infoing[0].egr_caeselCauEgr_text);
                }


            }
        }
    );
}


function anularEgreso() {

    if (!confirm("¿Desea anular el egreso?")) {
        return;
    }

    var objJson = cearUrlPorCamposJson($("#div_int_ing_egr"), 'ux');
    objJson.accion = 'anularEgreso';
    objJson.wemp_pmla = $("#wemp_pmla").val();
    objJson.consultaAjax = '';
    objJson.historia = $("#egr_histxtNumHis").val();
    objJson.ingreso = $("#egr_ingtxtNumIng").val();
    objJson.wbasedato = $("#wbasedato").val();
    objJson.diagnosticos = {};

    $(".fila_diagnosticos").each(function (index) {
        objJson.diagnosticos[index] = cearUrlPorCamposJson($("[ux][value!='']", this), 'ux');
    });

    objJson.procedimientos = {};

    $(".fila_procedimientos").each(function (index) {
        objJson.procedimientos[index] = cearUrlPorCamposJson($("[ux][value!='']", this), 'ux');
    });

    objJson.especialidades = {};

    $(".fila_especialidades").each(function (index) {
        objJson.especialidades[index] = cearUrlPorCamposJson($("[ux][value!='']", this), 'ux');
    });

    $.post("egreso_erp.php",
        objJson,
        function (data) {
            if (isJSON(data) == false) {
                alerta("RESPUESTA NO ESPERADA " + data);
            }
            data = $.parseJSON(data);
            if (data.error == 1 || data.error == 8) {
                alerta(data.mensaje);
            } else {
                if (data.mensaje != '') {
                    alerta(data.mensaje);
                }
            }
        }
    );
}

function ejecutar2(obj) {
    obj = jQuery(obj);
    var path = obj.attr("url");
    path = path.replace("<DOC>", $("#pac_doctxtNumDoc").val());
    path = path.replace("<TDOC>", $("#pac_tdoselTipoDoc").val());
    path = path.replace("<HIS>", $("#egr_histxtNumHis").val());
    window.open(path, '', 'fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
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

            if (mensajes[i] == undefined) {
                mensajes[i] = "Servicios por Diagn\xf3stico";
            }
            cadena = cadena + "-" + mensajes[i] + "\n( check en lista)";
        }
    }
    return cadena;
}

function isJSON(data) {
    var isJson = false
    try {
        // this works with JSON string AND JSON object, not sure about others
        var json = $.parseJSON(data);
        isJson = typeof json === 'object';
    } catch (ex) {

    }
    return isJson;
}

//--> 2014-11-25 se agregó esta función para que se valide en las causas de egreso la cantidad de dias de estancia y la causa del egreso
function validarTiempoEgreso(obj) {

    var codigoCausa = $(obj).val();
    var signo = codigoCausa.substring(0, 1);
    var limite = codigoCausa.substring(1, codigoCausa.length);
    calcularEstancia("no");
    var limiteEnDias = limite / 24;
    var diasEstancia = $("#egr_esttxtestan").val();
    if (signo == "+") {
        if (diasEstancia * 1 < limiteEnDias) {
            $(obj).find("option[value='']").attr("selected", true);
            $(obj).addClass("campoRequerido");
            alerta("El egreso fue anterior a las " + limite + " Horas");
            $("#egr_esttxtestan").focus();
        }
    } else if (signo == "-") {
        if (diasEstancia * 1 >= limiteEnDias) {
            $(obj).find("option[value='']").attr("selected", true);
            $(obj).addClass("campoRequerido");
            alerta("El egreso fue posterior a las " + limite + " Horas");
            $("#egr_esttxtestan").focus();
        }
    } else {
        return;
    }
}

function mostrarServiciosDiag(obj) {
    //--> buco los servicios elegidos para este diagnostico
    $("input[type='checkbox'][name='chk_servicio_dia']").attr("checked", false);
    $("input[type='checkbox'][name='chk_servicio_dia']").parent().removeClass("chkSeleccionado");
    $("input[type='checkbox'][name='chk_servicio_dia']").parent().addClass("fila1");

    $(obj).parent().parent().find("td[name='contenedor_servicios_ocultos']").attr("actualizandose", "on");

    var nombreCampo = $(obj).parent().parent().find("td[name='contenedor_servicios_ocultos']").attr("nombreCampos");

    $(obj).parent().parent().find("input[type='hidden'][name='" + nombreCampo + "']").each(function () {
        $("input[type='checkbox'][name='chk_servicio_dia'][value='" + $(this).val() + "']").attr("checked", true);
        $("input[type='checkbox'][name='chk_servicio_dia'][value='" + $(this).val() + "']").parent().addClass("chkSeleccionado");
    });

    $("#div_servicios_diagnostico").dialog({
        title: " Registro de diagn&oacute;stico por servicio",
        modal: true,
        closeOnEscape: false,
        buttons: {
            Ok: function () {
                $(this).dialog("close");
                var codigoMedico = $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").attr("medico");
                var camposModificar = $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").attr("nombreCampos");
                $("td[name='contenedor_servicios_ocultos'][actualizandose='on']>input").remove();
                checkeados = 0;
                //---> se agregan los servicios seleccionados en el contenedor que se está actualizando
                $("input[type='checkbox'][name='chk_servicio_dia']:checked").each(function () {
                    checkeados++;
                    agregarQuitarMultiplesServiciosConsolidar(this, codigoMedico, camposModificar);

                });
                if (checkeados > 0 && $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").prev("td").hasClass("faltantes")) {
                    $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").prev("td").removeClass("faltantes");
                }
                $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").attr("actualizandose", "off");
                $("#tbl_servicios_diagnostico>tr").show();
            }
        },
        closeOnEscape: false,
        show: {
            effect: "blind",
            duration: 500
        },
        hide: {
            effect: "blind",
            duration: 500
        },
        height: 600,
        width: 900,
        rezisable: true
    });
    $(".ui-dialog-titlebar-close").hide();//-->oculto la x de cerrar, por defecto
}

function agregarQuitarMultiplesServiciosConsolidar(obj, codigoMedico, camposModificar) {

    var servicio = $(obj).val();
    var descrip_Serv = $(obj).parent().next("td").next("td").html();
    var nombreCampo = $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").attr("nombreCampos");
    serviciosElegidos = $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").find("input[name='" + nombreCampo + "'][value='" + servicio + "']").length;
    camposServicios = "";

    if (camposModificar != "see_ser_hidCodSer" && camposModificar != "") {
        //--> la agregada del detalle en la zona de especialidades.
        contenedorServiciosEspecialidad = $("td[nombreCampos='see_ser_hidCodSer'][name='contenedor_servicios_ocultos'][medico='" + codigoMedico + "']");
        if (contenedorServiciosEspecialidad != undefined) {
            cantidadServiciosMedico = $(":hidden[name='see_ser_hidCodSer'][value='" + servicio + "']", contenedorServiciosEspecialidad).length
            if (cantidadServiciosMedico == 0) {
                cantidadServiciosMedico = $(":hidden[name='see_ser_hidCodSer'][value='" + servicio + "']", contenedorServiciosEspecialidad).length
                nuevoServicio = "<input type='hidden' id='see_ser_hidCodSer' name='see_ser_hidCodSer' value='" + servicio + "' >";
                $(contenedorServiciosEspecialidad).html($(contenedorServiciosEspecialidad).html() + nuevoServicio);
            }
        }
    }

    //--> para agregar en servicios visitados
    if (serviciosElegidos > 0) {//-->YA SELECCIONADO
        if ($(obj).is(":checked")) {
            $(obj).parent().addClass("chkSeleccionado");
            return;
        } else {
            $("td[actualizandose='on']").find("input[name='" + nombreCampo + "'][value='" + servicio + "']").remove();
            $(obj).parent().removeClass("chkSeleccionado");
        }
    } else {
        if ($(obj).is(":checked")) {
            $(obj).parent().addClass("chkSeleccionado");
            nuevoServicio = "<input type='hidden' id='" + nombreCampo + "' name='" + nombreCampo + "' value='" + servicio + "' >";
            $("td[actualizandose='on']").html($("td[actualizandose='on']").html() + nuevoServicio);
            //-->Se agrega el servicio como visitado:


            cantidad = $("#div_datos_servicios").find(":hidden[name='ser_cod_hidCodSer'][value='" + servicio + "']").length;
            //alert( "el centro de costos: "+datos[0].valor.cod+" está "+cantidad+" veces;" );
            if (cantidad == 0) {//--> SI EL CENTRO DE COSTOS NO SE HA GUARDADO EN LA LISTA DE SERVICIOS VISITADOS, LO AGREGO AUTÓMATICAMENTE
                if ($(".fila_servicios").length == 1 && $(".fila_servicios").eq(0).find("input[name='ser_cod_hidCodSer']").val() == "") {
                } else {
                    $("#spn_tabla_servicio").click();
                }

                camposServicios = ($(".fila_servicios").length) - 1;

                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_txtCodSer']").val(servicio);
                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_txtCodSer']").removeClass("campoRequerido");
                $(".fila_servicios").eq(camposServicios).find("input[name='ser_cod_hidCodSer']").val(servicio);
                $(".fila_servicios").eq(camposServicios).find("input[name='ser_egrradio']").val(servicio);
                $(".fila_servicios").eq(camposServicios).find("input[name='DesSer_txtDesSer']").val(descrip_Serv);
                $(".fila_servicios").eq(camposServicios).find("input[name='DesSer_txtDesSer']").removeClass("campoRequerido");
            }
        } else {
            $(obj).parent().removeClass("chkSeleccionado");
        }
        if (camposServicios != undefined)
            return (camposServicios);
    }
}

function agregarQuitarMultiplesServicios(obj) {
    var servicio = $(obj).val();
    var nombreCampo = $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").attr("nombreCampos");
    serviciosElegidos = $("td[name='contenedor_servicios_ocultos'][actualizandose='on']").find("input[name='" + nombreCampo + "'][value='" + servicio + "']").length;
    if (serviciosElegidos > 0) {//-->YA SELECCIONADO
        if ($(obj).is(":checked")) {
            $(obj).parent().addClass("chkSeleccionado");
            return;
        } else {
            $(obj).parent().removeClass("chkSeleccionado");
            //$(obj).parent().addClass("fila1");
        }
    } else {
        if ($(obj).is(":checked")) {
            $(obj).parent().addClass("chkSeleccionado");
        } else {
            $(obj).parent().removeClass("chkSeleccionado");
        }
    }
}

function seleccionarComoServicioEgreso(obj) {
    var servicioEgreso = $(obj).val();
    $("input[type='hidden'][name='servicioEgreso']").val(servicioEgreso);
    $("input[type='hidden'][name='servicioEgreso']").each(function () {
        if ($(this).val() != servicioEgreso) {
            $(this).removeAttr("checked");
        }
    });
    servicioEgreso = $("input[name='ser_egrradio']:checked").val();
    tablaDiaPpal = $("select[name='dia_tip_selTipDia']>option[value='P']:selected").parent().parent().parent().parent().parent();
    contenedorServiciosOscultosDiagnosticoPpal = $("td[name='contenedor_servicios_ocultos']", tablaDiaPpal);
    nombreCampoAux = $(contenedorServiciosOscultosDiagnosticoPpal).attr("nombrecampos");
    serAux = $("[value='" + servicioEgreso + "']", contenedorServiciosOscultosDiagnosticoPpal).length;
    if ((serAux == 0) && servicioEgreso != "" && servicioEgreso != undefined) {
        var serEgreso = "<input id=" + nombreCampoAux + " type='hidden' name='" + nombreCampoAux + "' value='" + servicioEgreso + "'>";
        $(contenedorServiciosOscultosDiagnosticoPpal).html($(contenedorServiciosOscultosDiagnosticoPpal).html() + serEgreso);
    }
}

/**
 * Permite simular el comportamiento de un radio button pero con checbox
 * Se debe poner la clase "checkbox-unico" a los checkbox que requieren este funcionamiento
 * @param checkbox elemento checkbox que se está marcando/desmarcando
 */
function marcarUnicoCheckbox(checkbox) {

    /** Al marcar este queda checkeado así que verificamos si no lo está para quitar la checqueada y dejarlo en blanco**/
    if (!$(checkbox).prop('checked')) {
        $(checkbox).prop('checked', false);
    }
    /** En caso que se marque un checkbox desmarcamos los demás de la misma clase **/
    else {
        $(".checkbox-unico").prop('checked', false);
        $(checkbox).prop('checked', true);
    }

}

/**
 * Permite habilitar o deshabilitar los checkboxs de causa basica de muerte (CBM)
 * Esta función lo hace obteniendo el valor de un select o enviando el texto de un option para realizarlo
 * @param selectId id del select que tendrá el evento llamar la función
 * @param selectedOption texto de la opción
 */
function habilitarDeshabilitarCBM(selectId = null, selectedOption = null) {
    if (selectId != null) {
        /** Obtenemos el valor de la opción seleccionada**/
        var selectedtText = $('#' + selectId + ' option:selected').text().toUpperCase();
    } else {
        var selectedtText = selectedOption;
    }

    /** Buscar el valor de MUERTE en la opción seleccionada **/
    var buscarValor = selectedtText.search(MUERTE);

    /** Si se selecciona una causa de egreso que contiene la palabra muerte
     *  entonces habilitamos los checkboxs **/

    if (buscarValor >= 0) {
        $(CLASE_CHECKBOX_CBM).removeAttr('disabled');
    } else {
        $(CLASE_CHECKBOX_CBM).attr('disabled', true);
        $(CLASE_CHECKBOX_CBM).prop('checked', false);
    }
}

/**
 * Valida que se marque alguna causa basica de muerte en caso de que la causa de egreso sea muerte
 * @returns {boolean}
 */
function respuestaValidaCBM() {

    var valoresMarcados = true;

    /** Obtenemos el valor del select que tiene la causa de egreso para verificar si es MUERTE***/
    var selectedtText = $(CAUSA_EGRESO_SELECT_ID + ' option:selected').text().toUpperCase();

    /** Buscar el valor de MUERTE en la opción seleccionada **/
    var buscarValor = selectedtText.search(MUERTE);

    /** Si se selecciona una causa de egreso que contiene la palabra muerte
     *  buscamos que algún checkbox esté chequeado**/
    if (buscarValor >= 0) {
        var checkeados = 0;

        $(CLASE_CHECKBOX_CBM).each(function () {
            if ($(this).attr('checked')) {
                checkeados++;
            }
        });

        /** Si no existen valores marcados cambiamos el valor de la respuesta **/
        if (checkeados == 0) {
            valoresMarcados = false;
        }
    }

    return valoresMarcados;
}