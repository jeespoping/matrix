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
        $("#codigopersona").val("");
    }

    //Hago el llamado a la función que busca las personas
    $.post("medidas.php",
        {
            action   : "buscarPersona",
            codigoPersona : $("#codigopersona").val(),
            wemp_pmla : $("#wemp_pmla").val(),
            tipoBusqueda : $("#tipobusqueda").val(),
            codigoCentroCosto : $("#codigocentrocosto").val(),
            limpiar : bLimpiar
        },
        function(response)
        {
            //Obtengo los datos de respuesta
            var dataJson = JSON.parse(response);
            
            //Reemplazo el select de personas
            $("#personasselect").html(dataJson.html);

            //Desbloqueo la pantalla
            $.unblockUI();
            return false;
        }
    );
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