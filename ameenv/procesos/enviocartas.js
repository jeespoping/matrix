let datosfactura=[];
let tabla;


$(document).ready(function(){
   
        gestion_datatable();
        evento_empresa();
        evento_consultarfactura();
        boton_eliminar();
        boton_grabar();
   
});

function boton_eliminar() {
    $(document).on('click','.eliminar',function(e){
        debugger;
        var opcion = confirm("Desea eliminar la factura?");
    if (opcion == true) {
        
	    if($(this).data('index')==1 && sessionStorage.getItem('contador')==0 ){

            tabla.row(0).remove().draw();
            sessionStorage.removeItem('contador')
            return false;
        }
        
            tabla.row($(this).data('index')).remove().draw();
             tabla.draw(true);
       
        if(sessionStorage.getItem('contador')==0 ){

            tabla.row(0).remove().draw();
            sessionStorage.removeItem('contador')
            return false;
        }
    /*    if($(this).data('index')==sessionStorage.getItem('contador')){
            tabla.row($(this).data('index')).remove().draw();
        }
        if($(this).data('index')>0 && sessionStorage.getItem('contador')==0 ){

            tabla.row(0).remove().draw();
            return false;
        }*/
        
        $('#tabla').DataTable().draw();
        tabla.order( [[ 0, 'asc' ]] ).draw( false );
        let contadoractual=parseInt(sessionStorage.getItem('contador'))
      //  sessionStorage.setItem('contador',(contadoractual-1))
        let datos=[];
        var data = tabla.rows().data();
        var datalen=(data.length-1);
        sessionStorage.setItem('contador',datalen);
         for (let index = 0; index <= datalen; index++) {
             datos[index]=index;
         }
        
       $("tbody>tr>td>button").each(function(i){
         
          var titulo = $(this).attr('data-index',datos[i]);
    
       });
        if(contadoractual=parseInt(sessionStorage.getItem('contador'))<0){
            sessionStorage.setItem('contador',0)
            tabla.row(0).remove().draw();

        }
    }
  
    });

/*       let botoneseliminar= document.querySelectorAll(".eliminar");
botoneseliminar.forEach(function(check){
 debbuger;
check.addEventListener('click', checkIndex);
})
function checkIndex(event){

}*/
}

function boton_grabar() {
    $(document).on('click', '#grabar',  function(  ){
        var opcion = confirm("esta seguro de guardar?");
        if (opcion) {
           let datos=datos_guardar();
           $.post("enviocartas.php",
                {
                accion: "tres",
                nit: sessionStorage.getItem('nit'),
                datos:datos
                },
                function(data){
                   alert("El numero de la carta guardada fue"+data);
                   tabla.clear().draw();
                   $('#nit').val('');
                   $('#factura').val('');
                   $('#nombre').val('');
                   sessionStorage.removeItem('nit');
                   sessionStorage.removeItem('contador');

                });
        }else{
           
        }
    }); 
}
function  datos_guardar() {
    let datos=[];
    let registros =(tabla.rows().data().length - 1)
    for (let index = 0; index <= registros; index++) {
        datos.push(tabla.rows().data()[index])
    }
  
    return datos     
}


function gestion_datatable() {
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
        rowReorder: false,
        ordering: false,
        select: true,
        "columnDefs": [
            { className: "my_class" }
          ]
        
    });
   
}

function evento_empresa() {
    $("#nit").change(function(){
        let nit=0;	
         nit= document.getElementById("nit").value;
        
    $.post("enviocartas.php",
    {
    accion: "uno",
    nit: nit
    },
    function(data){
        sessionStorage.setItem('nit',nit)
        var json=JSON.parse(data);
        if(json.error){
            alert(json.error)
            $("#nit").val("");
            $("#factura").prop('disabled', true);
        }else{
            $("#nombre").val(json.empnom);
            $("#factura").prop('disabled', false);
        }
    });

        
    });
}
function consultar_tabla(n_factura) {
    let datos=[];
    let registros =(tabla.rows().data().length - 1)
    for (let index = 0; index <= registros; index++) {
        datos.push(parseInt(tabla.rows().data()[index][0]))
    }
    let encontro=datos.includes(n_factura) 
    return encontro     
    
}

function evento_consultarfactura() {
    $("#factura").change(function(){
        let factura=0;	
         factura= document.getElementById("factura").value;
        if(consultar_tabla(parseInt(factura))){
            alert("la factura ingresada ya se encuentra en la tabla")
        }else{
            $.post("enviocartas.php",
            {
            accion: "dos",
            nit:sessionStorage.getItem('nit'),
            factura:factura
            },
            function(data){
                var json=JSON.parse(data);
                if(json.error){
                    alert(json.error);
                }else{ 
                    if(sessionStorage.getItem('contador')===null || parseInt(sessionStorage.getItem('contador'))===-1  ){
                        sessionStorage.setItem('contador',0)
                    } else{
                        let contadoractual=parseInt(sessionStorage.getItem('contador'))
                         sessionStorage.setItem('contador',(contadoractual+1))}
                    tabla.row.add( [
                        json.cardoc,
                        json.carval,
                        json.carhis,
                        json.carpac,
                        `<button type="button" class=" btn btn-danger eliminar" data-index="${parseInt(sessionStorage.getItem('contador'))}" >Eliminar</button> `
                    ] ).draw(true);
                     console.log(tabla.rows().data())
                     $('#factura').val('');
                  
                }
               
                
            });
        
        }
        
  
        
    });
}






  

  