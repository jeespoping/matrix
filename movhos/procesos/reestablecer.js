/** Buttons constants **/
const BTN_EJECUTAR_ID = "#btn_ejecutar";

$("#btn_ejecutar").click(function () {
    let confirmar = confirm("Desea ejecutar los procesos automaticos de servicios domiciliarios?");

    if (confirmar) {
        /***Deshabilitar boton y cambiar mensaje para ejecutar procesos**/
        $(BTN_EJECUTAR_ID).val("Ejecutando proceso");
        $(BTN_EJECUTAR_ID).attr("disabled", true);

        /** Parametros para los procesos **/
        let jsonParameters = {
            wemp_pmla: $('#wemp_pmla').val()
        };

        /** Ejecutar los procesos **/
        ejecutarEgresos(jsonParameters);

    }
});


/**
 * Habilita el botón para ejecutar el proceso nuevamente
 */
function habilitarBotonEjecutar() {
    /** Al ejecutar las ordenes habilitamos el botón  ***/
    $(BTN_EJECUTAR_ID).val("Ejecutar proceso");
    $(BTN_EJECUTAR_ID).attr("disabled", false);
}

function ejecutarEgresos(parametros) {
    $.ajax({
        type: 'POST',
        url: 'ReestablecerFunctions.php',
        data: parametros,
        success: function (result) {
            console.log(result);
            if(result.error != 'error'){
                console.log("Agregados...");
                habilitarBotonEjecutar();
                $('#cargarDatos').html("Datos agregados en Inpaci"); 
            }else{
                console.log("No agregados...");
                habilitarBotonEjecutar();
                $('#cargarDatos').html("Datos no agregados en Inpaci: " + result.mensaje); 
            }
        },
        error: function (xhr, status, errorThrown) {
            console.log('Error en el llamado...');
            habilitarBotonEjecutar();
            $('#cargarDatos').html("Error en el llamado..."); 
        }
    });
}

