var table;
jQuery(document).ready(function() {
    
	  table=$("#table").DataTable({
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
		 }}
	 });
	
	
 
});
   function guardar(id){
	
	if( $('#autoriza'+id).val() == '' ){
		alert( "Indique si autoriza o no el medicamentos" );
		return;
	}
	
	alert('Guardado exitosamente');
      var codigo_medicamento = $('#codigo'+id).text();
      var nombre_medicamento = $('#n_medicamento'+id).text();
      var codigo_medico_ordena = $('#c_medico_ordena'+id).text();
      var justificacion_medico_ordena = $('#j_medico_ordena'+id).text();
      var justificacion_medico_autoriza =$('#j_medico_autoriza'+id).val();
      var autoriza =$('#autoriza'+id).val();
	  var historia =$('#historia'+id).val();
      var ingreso =$('#ingreso'+id).val();
      var fila = {
        codigo_medicamento,
        nombre_medicamento,
        codigo_medico_ordena,
        justificacion_medico_ordena,
        justificacion_medico_autoriza,
        autoriza,
		historia,
		ingreso,
      };
      console.log(fila);
	 
	  $('#codigo'+id).hide();
	  $('#n_medicamento'+id).hide();
	  $('#c_medico_ordena'+id).hide();
	  $('#j_medico_ordena'+id).hide();
	  $('j_medico_autoriza'+id).hide();
	  $('#autoriza'+id).hide();
	  $('#n_medico'+id).hide();
	  $('#j_medico_autoriza'+id).hide();
	   $('#enviar'+id).hide();
	      $('#tr'+id).hide();
		 //  table.ajax.reload(null,false);
	  var wemp_pmla =$('#empresa').val();
	  console.log(wemp_pmla);
	$.ajax({
    type: "POST",
    url: "Autorizacion_Medicamentos_Backend.php",
    data: {'fila':fila,'wemp_pmla':wemp_pmla},
    success: function () {
		$( "."+historia+"-"+ingreso+"-"+codigo_medicamento ).hide();
		$("#table").DataTable().draw();
	},

    error: function () {
      alert("error fatal");
    },
  });

  
	  
	  
	  
	  
	}