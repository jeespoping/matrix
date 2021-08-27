/** Divs constants **/
const EGRESO_ICON = "#egreso_icon";
const INGRESO_ICON = "#ingreso_icon";
const REPLICAR_ICON = "#replicar_icon";

const EGRESO_TIME = "time_egreso";
const INGRESO_TIME = "time_ingreso";
const REPLICAR_TIME = "time_replicar";

/** Buttons constants **/
const BTN_EJECUTAR_ID = "#btn_ejecutar";
const EGRESO_BTN_ID = "#EGRESO_BTN_ID";
const INGRESO_BTN_ID = "#INGRESO_BTN_ID";
const REPLICAR_BTN_ID = "#REPLICAR_BTN_ID";

/** Icons constants **/
const ICON_SYNC = "fas fa-sync text-default";
const ICON_WARNING = "fas fa-exclamation-triangle";
const ICON_SUCCESS = "far fa-check-circle";

/** Messages constans **/
const EGRESO_MENSAJES = "#egreso_mensajes";
const EGRESO_MENSAJES_HISTORIAS = "#egreso_mensajes_historias";
const INGRESO_MENSAJES = "#ingreso_mensajes";
const INGRESO_MENSAJES_HISTORIAS = "#ingreso_mensajes_historias";
const REPLICAR_MENSAJES = "#replicar_mensajes";
const REPLICAR_MENSAJES_HISTORIAS = "#replicar_mensajes_historias";

/**Data storage**/
let dataEgresos = null;
let dataIngresos = null;
let dataReplicar = null;

/** Time executed **/
let timeEgresosProcess = null;
let timeIngresosProcess = null;
let timeReplicarProcess = null;

$("#btn_ejecutar").click(function () {
    let confirmar = confirm("Desea ejecutar los procesos automaticos de servicios domiciliarios?");

    if (confirmar) {
        /***Deshabilitar boton y cambiar mensaje para ejecutar procesos**/
        $(BTN_EJECUTAR_ID).val("Ejecutando procesos");
        $(BTN_EJECUTAR_ID).attr("disabled", true);

        /** Parametros para los procesos **/
        let jsonParameters = {
            wemp_pmla: $('#wemp_pmla').val(),
            wcco0: $('#wcco0').val(),
            whisconsultada: $('#whisconsultada').val(),
            cemp: $('#cemp').val()
        };

        var str = JSON.stringify(jsonParameters, null, 2); // spacing level = 2
        // console.log(str);

        reiniciarIcon(EGRESO_ICON, EGRESO_MENSAJES, EGRESO_MENSAJES_HISTORIAS, EGRESO_BTN_ID, EGRESO_TIME);
        reiniciarIcon(INGRESO_ICON, INGRESO_MENSAJES, INGRESO_MENSAJES_HISTORIAS, INGRESO_BTN_ID, INGRESO_TIME);
        reiniciarIcon(REPLICAR_ICON, REPLICAR_MENSAJES, REPLICAR_MENSAJES_HISTORIAS, REPLICAR_BTN_ID, REPLICAR_TIME);

        /** Ejecutar los procesos **/
        ejecutarEgresosConteo(jsonParameters);

    }
});


/**
 * Reinicia el icon para dejar su estado inicial
 * @param iconId id de la etiqueta icon
 * @param mensajeId id de la etiqueta div
 */
function reiniciarIcon(iconId, mensajeId, mensajeIdHistoria, buttonId = null, timerId = null) {
    $(iconId).removeAttr('class');
    $(iconId).attr('class', ICON_SYNC);
    $(mensajeId).html(null);
    $(mensajeIdHistoria).html(null);
    $(buttonId).addClass("invisible");
    $('#' + timerId).html('0 segundos');
}

/**
 * Muestra el tiempo transcurrido desde el llamado de esta función y lo pone en un div
 * @param idDivTiempo
 */
function mostrarTiempoProgreso(idDivTiempo) {

    var horas = 0;
    var minutos = 0;
    var segundos = 0;

    var divTiempo = document.getElementById(idDivTiempo);

    var funcionInterval = window.setInterval(function () {

        segundos++;

        if (segundos >= 60) {
            segundos = 0;
            minutos++;
        }

        if (minutos >= 60) {
            minutos = 0;
            horas++;
        }

        let tiempo = "";
        if (horas > 0) tiempo += horas + " horas ";
        if (minutos > 0) tiempo += minutos + " minutos ";
        tiempo += segundos + " segundos ";

        divTiempo.innerHTML = tiempo;

    }, 1000);

    return funcionInterval;

}

/**
 * Habilita el botón para ejecutar el proceso nuevamente
 */
function habilitarBotonEjecutar() {
    /** Al ejecutar las ordenes habilitamos el botón  ***/
    $(BTN_EJECUTAR_ID).val("Ejecutar procesos");
    $(BTN_EJECUTAR_ID).attr("disabled", false);
}

function ejecutarEgresosConteo(parametros) {

    $(EGRESO_MENSAJES).html("Ejecutando...");
    $(EGRESO_ICON).addClass('fa-spin');

    /** Iniciar contador de tiempo **/
    timeEgresosProcess = mostrarTiempoProgreso(EGRESO_TIME);

    parametros.mostrar_conteo = 1;
    $.ajax({
        type: 'POST',
        url: 'egrConsultarPacientes.php',
        data: parametros,
        success: function (data) {
            data = JSON.parse(data);
            let mensaje = "<li>Total historias: " + data + "</li>";
            $(EGRESO_MENSAJES_HISTORIAS).append(mensaje);
            parametros.mostrar_conteo = 0;
            /** Ejecutamos el siguiente proceso**/
            ejecutarEgresos(parametros);
        },
        error: function (xhr, status, errorThrown) {
            $(EGRESO_ICON).removeAttr('class');
            $(EGRESO_ICON).attr('class', 'fas fa-times text-danger');
            habilitarBotonEjecutar();
            /** Detener contador de tiempo **/
            clearInterval(timeEgresosProcess);
        }
    });

}

function ejecutarEgresos(parametros) {
    $.ajax({
        type: 'POST',
        url: 'egrConsultarPacientes.php',
        data: parametros,
        success: function (data) {
            console.log("DATA EGRESOS: ");
            console.log(data);
            data = JSON.parse(data);
            $(EGRESO_ICON).removeAttr('class');
            $(EGRESO_ICON).attr('class', ICON_SUCCESS + ' text-success');
            $(EGRESO_MENSAJES).html("Finalizado");

            /** Asignar la variable del data de egresos para llenar el modal **/
            dataEgresos = data;
            let totalFinalizados = dataEgresos.finalizados.length;
            let totalFallidos = dataEgresos.fallidos.length;

            let botonFinalizados = '<button type="button" onclick="mostrar_modal(this.id)"  class="btn btn-link" ' +
                'data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="EGRESO_FINALIZADOS_BTN_ID" >' +
                totalFinalizados +
                '</button>';
            let botonFallidos = '<button type="button" onclick="mostrar_modal(this.id)" class="btn btn-link" ' +
                'data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="EGRESO_FALLIDOS_BTN_ID">' +
                totalFallidos +
                '</button>';


            let mensajeFallidos = "<li> Finalizados: " + botonFinalizados + "</li>";
            let mensajeFinalizados = "<li>Fallidos: " + botonFallidos + "</li>";
            $(EGRESO_MENSAJES_HISTORIAS).append(mensajeFallidos);
            $(EGRESO_MENSAJES_HISTORIAS).append(mensajeFinalizados);

            /** Si se encontraron historias clinicas con errores **/
            if (data['fallidos'].length) {
                $(EGRESO_ICON).attr('class', ICON_WARNING + ' text-warning');
                $(EGRESO_MENSAJES).append("Finalizado con advertencias");
                // $(EGRESO_BTN_ID).removeClass("invisible");
            }

            /** Detener contador de tiempo **/
            clearInterval(timeEgresosProcess);

            /** Ejecutamos el siguiente proceso**/
            ejecutarIngresoConteo(parametros);
            //ejecutarOrdenesConteo(parametros);
        },
        error: function (xhr, status, errorThrown) {
            $(EGRESO_ICON).removeAttr('class');
            $(EGRESO_ICON).attr('class', 'fas fa-times text-danger');
            habilitarBotonEjecutar();
            /** Detener contador de tiempo **/
            clearInterval(timeEgresosProcess);
        }
    });
}

function ejecutarIngresoConteo(parametros) {

    $(INGRESO_MENSAJES).html("Ejecutando...");
    $(INGRESO_ICON).addClass('fa-spin');

    /** Iniciar contador de tiempo **/
    timeIngresosProcess = mostrarTiempoProgreso(INGRESO_TIME);

    parametros.mostrar_conteo = 1;
    $.ajax({
        type: 'POST',
        url: 'IngHistorias.php',
        data: parametros,
        success: function (data) {
            console.log("DATA INGRESOS: ");
            console.log(data);
            data = JSON.parse(data);

            let mensaje = "<li>Total historias: " + data + "</li>";
            $(INGRESO_MENSAJES_HISTORIAS).append(mensaje);
            parametros.mostrar_conteo = 0;
            /** Ejecutamos el siguiente proceso**/
            ejecutarIngresos(parametros);
        },
        error: function (xhr, status, errorThrown) {
            $(INGRESO_ICON).removeAttr('class');
            $(INGRESO_ICON).attr('class', 'fas fa-times text-danger');
            habilitarBotonEjecutar();
            /** Detener contador de tiempo **/
            clearInterval(timeIngresosProcess);
        }
    });

}

function ejecutarIngresos(parametros) {
    $(INGRESO_ICON).addClass('fa-spin');
    $(INGRESO_MENSAJES).html("Ejecutando...");
    $.ajax({
        type: 'POST',
        url: 'IngHistorias.php',
        data: parametros,
        success: function (data) {
            console.log(data);
            data = JSON.parse(data);
            $(INGRESO_ICON).removeAttr('class');
            $(INGRESO_ICON).attr('class', ICON_SUCCESS + ' text-success');
            $(INGRESO_MENSAJES).html("Finalizado");
            /** Asignar la variable del data de egresos para llenar el modal en caso de errores**/
            dataIngresos = data;
            let totalFinalizados = dataIngresos.finalizados.length;
            let totalFallidos = dataIngresos.fallidos.length;

            let botonFinalizados = '<button type="button" onclick="mostrar_modal(this.id)"  class="btn btn-link" ' +
                'data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="INGRESO_FINALIZADOS_BTN_ID" >' +
                totalFinalizados +
                '</button>';
            let botonFallidos = '<button type="button" onclick="mostrar_modal(this.id)" class="btn btn-link" ' +
                'data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="INGRESO_FALLIDOS_BTN_ID">' +
                totalFallidos +
                '</button>';

            let mensajeFallidos = "<li>Finalizados: " + botonFinalizados + "</li>";
            let mensajeFinalizados = "<li>Fallidos: " + botonFallidos + "</li>";
            $(INGRESO_MENSAJES_HISTORIAS).append(mensajeFallidos);
            $(INGRESO_MENSAJES_HISTORIAS).append(mensajeFinalizados);

            /** Si se encontraron historias clinicas con errores **/
            if (data['fallidos'].length) {
                $(INGRESO_ICON).attr('class', ICON_WARNING + ' text-warning');
                $(INGRESO_MENSAJES).html("Finalizado con advertencias");
                //$(INGRESO_BTN_ID).removeClass("invisible");
            }

            /** Detener contador de tiempo **/
            clearInterval(timeIngresosProcess);

            /** Ejecutamos el siguiente proceso**/
            ejecutarOrdenesConteo(parametros);
        },
        error: function (xhr, status, errorThrown) {
            $(INGRESO_ICON).removeAttr('class');
            $(INGRESO_ICON).attr('class', 'fas fa-times text-danger');
            habilitarBotonEjecutar();
            /** Detener contador de tiempo **/
            clearInterval(timeIngresosProcess);
        }
    });
}

function ejecutarOrdenesConteo(parametros) {

    $(REPLICAR_MENSAJES).html("Ejecutando...");
    $(REPLICAR_ICON).addClass('fa-spin');

    /** Iniciar contador de tiempo **/
    timeReplicarProcess = mostrarTiempoProgreso(REPLICAR_TIME);

    parametros.mostrar_conteo = 1;
    $.ajax({
        type: 'POST',
        url: 'generarKardexAutomaticosSD.php',
        data: parametros,
        success: function (data) {
            data = JSON.parse(data);

            let mensaje = "<li>Total historias: " + data + "</li>";
            $(REPLICAR_MENSAJES_HISTORIAS).append(mensaje);
            parametros.mostrar_conteo = 0;
            /** Ejecutamos el siguiente proceso**/
            ejecutarReplicarOrden(parametros);
        },
        error: function (xhr, status, errorThrown) {
            $(REPLICAR_ICON).removeAttr('class');
            $(REPLICAR_ICON).attr('class', 'fas fa-times text-danger');
            habilitarBotonEjecutar();
            /** Detener contador de tiempo **/
            clearInterval(timeReplicarProcess);
        }
    });

}


function ejecutarReplicarOrden(parametros) {
    $.ajax({
        type: 'POST',
        url: 'generarKardexAutomaticosSD.php',
        data: parametros,
        success: function (data) {
            // console.log(data);
            data = JSON.parse(data);
            $(REPLICAR_ICON).removeAttr('class');
            $(REPLICAR_ICON).attr('class', ICON_SUCCESS + ' text-success');
            $(REPLICAR_MENSAJES).html("Finalizado");

            /** Asignar la variable del data de egresos para llenar el modal en caso de errores**/
            dataReplicar = data;
            let totalFinalizados = dataReplicar.finalizados.length;
            let totalFallidos = dataReplicar.fallidos.length;

            let botonFinalizados = '<button type="button" onclick="mostrar_modal(this.id)"  class="btn btn-link" ' +
                'data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="REPLICAR_FINALIZADOS_BTN_ID" >' +
                totalFinalizados +
                '</button>';
            let botonFallidos = '<button type="button" onclick="mostrar_modal(this.id)" class="btn btn-link" ' +
                'data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="REPLICAR_FALLIDOS_BTN_ID">' +
                totalFallidos +
                '</button>';

            let mensajeFallidos = "<li>Finalizados: " + botonFinalizados + "</li>";
            let mensajeFinalizados = "<li>Fallidos: " + botonFallidos + "</li>";
            $(REPLICAR_MENSAJES_HISTORIAS).append(mensajeFallidos);
            $(REPLICAR_MENSAJES_HISTORIAS).append(mensajeFinalizados);

            /** Si se encontraron historias clinicas con errores **/
            if (data['fallidos'].length) {
                $(REPLICAR_ICON).attr('class', ICON_WARNING + ' text-warning');
                $(REPLICAR_MENSAJES).html("Finalizado con advertencias");
                $(REPLICAR_BTN_ID).removeClass("invisible");
            }

            /** Detener contador de tiempo **/
            clearInterval(timeReplicarProcess);
            habilitarBotonEjecutar();
        },
        error: function (xhr, status, errorThrown) {
            $(REPLICAR_ICON).removeAttr('class');
            $(REPLICAR_ICON).attr('class', 'fas fa-times text-danger');
            habilitarBotonEjecutar();
            /** Detener contador de tiempo **/
            clearInterval(timeReplicarProcess);
        }
    });
}


function mostrar_modal(idModal) {
    // let idModal = $(this).attr('id');

    console.log("idModal = " + idModal);

    dataModal = null;
    switch (idModal) {
        case "EGRESO_FINALIZADOS_BTN_ID":
            dataModal = dataEgresos.finalizados;
            break;
        case "EGRESO_FALLIDOS_BTN_ID":
            dataModal = dataEgresos.fallidos;
            break;
        case "INGRESO_FINALIZADOS_BTN_ID":
            dataModal = dataIngresos.finalizados;
            break;
        case "INGRESO_FALLIDOS_BTN_ID":
            dataModal = dataIngresos.fallidos;
            break;
        case "REPLICAR_FINALIZADOS_BTN_ID":
            dataModal = dataReplicar.finalizados;
            break;
        case "REPLICAR_FALLIDOS_BTN_ID":
            dataModal = dataReplicar.fallidos;
            break;
    }
    llenarModal(dataModal);
};

function llenarModal(data) {

    let contenidoTabla = "";

    if (data && data.length) {
        contenidoTabla += "<table class='table table-bordered table-responsive'>";
        contenidoTabla += "<thead class='encabezadoTabla'>";
        contenidoTabla += "<th class='text-center'>Historia</th>";
        contenidoTabla += "<th class='text-center'>Ingreso</th>";
        contenidoTabla += "<th class='text-center'>Tipo</th>";
        contenidoTabla += "<th class='text-center'>Documento</th>";
        contenidoTabla += "<th class='text-center'>Paciente</th>";
        contenidoTabla += "<th>Descripci&oacute;n</th>";
        contenidoTabla += "</thead>";


        for (let i = 0; i < data.length; i++) {

            let urlHistoria = data[i].url != null ? "<a href='" + data[i].url + "' target='_blank'>" + data[i].historia + "</a>" : data[i].historia;
            let matrixClass = '';

            matrixClass = i % 2 ? 'Fila1' : 'Fila2';

            contenidoTabla += "<tr class='" + matrixClass + "'>";
            contenidoTabla += "<td>" + urlHistoria + "</td>";
            contenidoTabla += "<td>" + data[i].ingreso + "</td>";
            contenidoTabla += "<td>" + data[i].tipo_documento + "</td>";
            contenidoTabla += "<td>" + data[i].documento + "</td>";
            contenidoTabla += "<td>" + data[i].paciente + "</td>";
            contenidoTabla += "<td>" + data[i].descripcion + "</td>";
            contenidoTabla += "</tr>";
        }

        contenidoTabla += "</table>";
    } else {
        contenidoTabla = "No se encontraron datos";
    }

    $("#modal_content_id").html(contenidoTabla);
}
