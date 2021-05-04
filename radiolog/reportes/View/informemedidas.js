/**
 * Funcion para búsqueda de personas en informes
 * @by: sebastian.nevado
 * @date: 2021/05/04
 */
 function buscarPersonaInforme(bLimpiar, informe)
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
     $.post("informeMedidas.php",
         {
             action             : "buscarPersona",
             codigoPersona      : $("#codigopersona").val(),
             wemp_pmla          : $("#wemp_pmla").val(),
             tipoBusqueda       : $("#tipobusqueda").val(),
             codigoCentroCosto  : $("#codigocentrocosto").val(),
             limpiar            : bLimpiar,
             informe            : informe
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