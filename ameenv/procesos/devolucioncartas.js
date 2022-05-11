let table;
let carta;
$(document).ready(function() {
    cargartabla();
    cargarbotones();
    manejo_eventselect();
    evento_input();
    config_toastr(); 
    boton_grabar();
} );

function cargartabla() {
    table=$('#tabla').DataTable( {
        ordering:false,
        language: {
        "decimal": "",
        "emptyTable": "No hay Facturas ",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ Facturas",
        "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
        "infoFiltered": "(Filtrado de _MAX_ total facturas)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Mostrar _MENU_ Facturas",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "search": "Buscar:",
        "zeroRecords": "Sin resultados encontrados",
        "paginate": {
            "first": "Primero",
            "last": "Ultimo",
            "next": "Siguiente",
            "previous": "Anterior"
        },
    "select": {
    "rows": "%d Facturas seleccionadas"
}
     },
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        } ],
        select: {
            style:    'multi',
            selector: 'td:first-child'
        }
    } );
}
function cargarbotones() {
    botonconsultar();
    botonselectedall();
}
function botonselectedall(){
    $('#all').change( function () {
        if($(this).is(':checked')) {
            table.rows().select().draw();
            $('.causal').attr('disabled',false);
    } else {
        table.rows().deselect().draw();
        $('.causal').attr('disabled',true);
    }
       
       
    } );
}
function cargar_info(json) {
    $(".info-carta").remove();
    $("#cantidad").append(`<td class="info-carta">${json.cantidad}</td>`);
    $("#valor").append(`<td class="info-carta">${json.valor}</td>`);
    $("#estado").append(`<td class="info-carta">${json.estado}</td>`);
    $("#empresa").append(`<td class="info-carta">${json.empresa}</td>`);
}

function botonconsultar() {
    $('#consultar').click( function () {
        let codigo=0;
         codigo= document.getElementById("codigo").value;
         table.clear().draw();
         $.post("devolucioncartasback.php",
         {
             accion:"cuatro",
             codigo:codigo
         },
         function(data){
            var json=JSON.parse(data);
            if(json.error){
                alert(json.error)
            }else{
                cargar_info(json);
              llenar_tabla(codigo);
              carta=codigo;
            }
            
         });
      });

       

}
function  llenar_tabla(codigo) {
    $.post("devolucioncartasback.php",
    {
        accion:"cinco",
        codigo:codigo
    },
    function(data){
       var json=JSON.parse(data);
       let limite=(json.length-1)
       for (let index =0; index <=limite; index++) {
           table.row.add( [
              '',
               json[index].cardoc,
               json[index].carsal,
               json[index].carhis,
               json[index].carpac,
               json[index].envdetest,
               `<input type="text" class="causal" id="causal" name="causal" value="" disabled>`
           ] ).draw(true);             
       }
    });

    
 


}
function manejo_eventselect() {
    table.on( 'select', function ( e, dt, type, indexes ) {
        table[ type ]( indexes ).nodes().to$().addClass( 'custom-selected' );
        $('.custom-selected > td > input').removeAttr("disabled")
        $('.custom-selected > td > input').css("color","black")
        $('.custom-selected > td > input').focus();
        table[ type ]( indexes ).nodes().to$().removeClass( 'custom-selected' );
    
} );
table.on( 'deselect', function ( e, dt, type, indexes ) {
    table[ type ]( indexes ).nodes().to$().addClass( 'custom-selected' );
    $('.custom-selected > td > input').removeClass( 'errorcausal' );
    $('.custom-selected > td > input').val('');
    $('.custom-selected > td > input').attr('disabled', 'disabled');
    table[ type ]( indexes ).nodes().to$().removeClass( 'custom-selected' );
    $(document).on('change','.causal',function(e) {
        e.stopPropagation();
    });
    

} );
    
}
function config_toastr() {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
}
function evento_input() {
    $(document).on('change','.causal',function(e) {
                   var  color="";
                    let data= { 
                        accion: "nueve", 
                        codigo: this.value.toUpperCase(), 
                    }
                        $.ajax({
                            type:'POST',
                            url:'devolucioncartasback.php',
                            data:data,
                            success: function(data){
                                var json=JSON.parse(data);
                                if(json.error){
                                    toastr["error"](json.error, "Info. Causal");
                                    debugger;
                                    color="1";
                                }else{
                                    toastr["info"](json.caucod + "-"+json.caunom, "Info. Causal");
                                    debugger;
                                    color="0";
                                }},
                                async:false
                            });
                            if(color=="0"){
                                $(this).removeClass("errorcausal");
                                $(this).css("color","green");
                            }else{
                                $(this).addClass("errorcausal");
                                $(this).css("color","red");
                            }   
                              
                    });
                            $(document).on("keydown",".causal", function (e) {
                               
                                var key = e.which || e.charCode || e.keyCode;
                               
                                if (key === 9) {
                                    if (e.preventDefault) {
                                        e.preventDefault();
                                    }
                                    return false;
                                }
                            });
                
}
function organizar_data() {
    let datos=table.rows('.selected').data();
    let data=[];
    let valores=datos.map((datos,index,dato) => {
        return [
          dato[index][1],
          dato[index][2],
          dato[index][3],
          dato[index][4],
          $('.selected > td > input')[index].value.toUpperCase()
        ]
    })
    return valores;
}
function object_array(data) {
    
    let cantidad=data.length-1;
    console.log(cantidad);
    let array=[];
    for (let index = 0; index <=cantidad; index++) {
        array.push(data[index]);
        
 }
   return array;
}
function buscar_error() {
    var existeerror = document.getElementsByClassName("errorcausal");
    
    if(existeerror.length>=1){
        return true;
    }else{
        return false;
    }
}
function  sincausal(datos) {
    let sincausal=false;
    for (let index = 0; index <=datos.length-1; index++) {
        if(datos[index][4]==""){
            sincausal=true;
        }
        
    }
    return sincausal;
}
function boton_grabar(){
    $('#grabar').click( function () {
        var opcion = confirm("esta seguro de guardar?");
        if (opcion) {
            let data=organizar_data();
            let sincausalvacio=sincausal(data);
            let n_seleccionado=table.rows('.selected').data().length;
            let objectArray=object_array(data);
            let hayerror=buscar_error();
            if(sincausalvacio || n_seleccionado<=0 || hayerror){
                alert("Revise causales correctas,causales vacias o no haber seleccionado ninguna factura");
            }else{
                console.log(objectArray);
                $.post("devolucioncartasback.php",
                {
                    accion:"diez",
                    carta:carta,
                    datos:objectArray

                },
                function(data){
                    alert("El numero de la devolucion guardada fue : "+data);
                   table.clear().draw();
                   $('#codigo').val('');
                  
                  
                   $(".info-carta").remove();
                   
                });
            }
        }
    })
}