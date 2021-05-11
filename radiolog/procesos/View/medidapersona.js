/**
 * Función para búsqueda de personas
 * @by: sebastian.nevado
 * @date: 2021/04/27
 */
function buscarPersona(bLimpiar)
{
    //Bloqueo la pantalla
    $.blockUI({ message:	'Espere...',
        css: 	{
                    width: 	'auto',
                    height: 'auto'
                }
    });

    //Si envío el parámtro en verdadero, limpio el campo de búsqueda
    if(bLimpiar == true)
    {
        $("#busquedacentrocosto").val("");
        $("#codigocentrocosto").val("");
        $("#busquedapersona").val("");
        $("#codigopersona").val("");
    }

    $.unblockUI();
}

/**
 * Función para cerrar la ventana
 * @by: sebastian.nevado
 * @date: 2021/05/06
 */
function cerrarVentana()
{ 
    if (confirm("Est\u00e1 seguro de salir?") == true)
        window.close();
    else
        return false;
}


/**
 * Función para eliminar medida por personal
 * @by: sebastian.nevado
 * @date: 2021/05/06
 */
 function eliminarMedidaPersonal(iIdMedidaxPersona)
 { 
     if (confirm("Est\u00e1 seguro de eliminar la medida?") == true)
     {
        //Bloqueo la pantalla
        $.blockUI({ message:	'Espere...',
        css: 	{
                    width: 	'auto',
                    height: 'auto'
                }
        });

        //Hago el llamado a la función que busca las personas
        $.post("medidas.php",
            {
                action   : "deleteMedidaPersona",
                idmedidaxpersona : iIdMedidaxPersona,
                wemp_pmla : $("#wemp_pmla").val()
            },
            function(response)
            {
                //Obtengo los datos de respuesta
                var dataJson = JSON.parse(response);
                console.log(dataJson);

                //Desbloqueo la pantalla
                $.unblockUI();

                //Refresco la pantalla
                location.reload();
                return false;
            }
        );
     }
     else
     {
        return false;
     }
 }

$( function() {
    // Single Select
    $( "#busquedacentrocosto" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
                url: "medidas.php",
                type: 'post',
                dataType: "json",
                data: {
                    busqueda: request.term,
                    wemp_pmla : $("#wemp_pmla").val(),
                    action   : "buscarCentroCosto"
                },
                success: function( data ) {
                response( data );
                }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#busquedacentrocosto').val(ui.item.label); // display the selected text
            $('#codigocentrocosto').val(ui.item.value); // save selected id to input
            
            $("#busquedapersona").val("");
            $("#codigopersona").val("");
            return false;
        },
            focus: function(event, ui){
            $( "#busquedacentrocosto" ).val( ui.item.label );
            $( "#codigocentrocosto" ).val( ui.item.value );
            return false;
        },
        search: function(event, ui) {
            $('#busquedacentrocosto')._addClass( "ui-autocomplete-loading" );
        },
        open: function(event, ui) {
            $('#busquedacentrocosto')._addClass( "ui-autocomplete-loading" );
        }
    });

    $( "#busquedacentrocosto" ).change(function() {
        var busqueda = $( "#busquedacentrocosto" ).val();
        if(busqueda == ''){
            $("#codigocentrocosto").val("");
            buscarPersona(true);
        }
    });


    $( "#busquedapersona" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
                url: "medidas.php",
                type: 'post',
                dataType: "json",
                data: {
                    busquedapersona: request.term,
                    wemp_pmla : $("#wemp_pmla").val(),
                    action   : "buscarPersona",
                    tipobusqueda : "all",
                    codigocentrocosto : $("#codigocentrocosto").val(),
                },
                success: function( data ) {
                response( data );
                }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#busquedapersona').val(ui.item.label); // display the selected text
            $('#codigopersona').val(ui.item.value); // save selected id to input
            buscarPersona(false);
            return false;
        },
        focus: function(event, ui){
            $( "#busquedapersona" ).val( ui.item.label );
            $( "#codigopersona" ).val( ui.item.value );
            return false;
        },
        search: function(event, ui) {
            $('#busquedapersona')._addClass( "ui-autocomplete-loading" );
        },
        open: function(event, ui) {
            $('#busquedapersona')._addClass( "ui-autocomplete-loading" );
        }
    });

    $( "#busquedapersona" ).change(function() {
        var busqueda = $( "#busquedapersona" ).val();
        if(busqueda == ''){
            $("#codigopersona").val("");
        }
    });
});