let tabla;
$( document ).ready(function() {
    boton_consultar()
    cargartabla();
});
function cargar_info(json) {
    $(".info-carta").remove();
    $("#cantidad").append(`<td class="info-carta">${json.cantidad}</td>`);
    $("#valor").append(`<td class="info-carta">${json.valor}</td>`);
    $("#estado").append(`<td class="info-carta">${json.estado}</td>`);
    $("#empresa").append(`<td class="info-carta">${json.empresa}</td>`);
}
function  cargartabla() {
  let data= `accion=cinco&codigo=${codigo}`;
    tabla=$("#tabla").DataTable({
		
        language:{
                       "sProcessing":     "Procesando...",
                       "sLengthMenu":     "Mostrar _MENU_ Registros",
                       "sZeroRecords":    "No se encontraron resultados",
                       "sEmptyTable":     "Ningun dato disponible en esta tabla",
                       "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                       "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                       "infoFiltered": "",
                       "sInfoPostFix":    "",
                       "sSearch":         "Buscar:",
                       "sUrl":            "",
                       "sInfoThousands":  ",",
                       "sLoadingRecords": "Cargando...",
                       "oAria": {
                           "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                           "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }},  
             
    });
}
function  boton_consultar() {
    $( "#consultar" ).on( "click", function() {
        let codigo=0;
         codigo= document.getElementById("codigo").value;
         tabla.clear().draw();
         alert(codigo)
         $.post("enviocartas.php",
         {
             accion:"cuatro",
             codigo:codigo
         },
         function(data){
            var json=JSON.parse(data);
            if(json.error){
                alert(json.error)
            }else{
                cargar_info(json)
                llenar_tabla(codigo)
            }
            
         });
      });
     
    
}
function  llenar_tabla(codigo) {
         $.post("enviocartas.php",
         {
             accion:"cinco",
             codigo:codigo
         },
         function(data){
            var json=JSON.parse(data);
            let limite=(json.length-1)
            for (let index =0; index <=limite; index++) {
                tabla.row.add( [
                    json[index].cardoc,
                    json[index].carsal,
                    json[index].carhis,
                    json[index].carpac,
                    json[index].envdetest,
                ] ).draw(true);             
            }
         });
      
     
    
}