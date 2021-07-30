var table;
var tableLog;
jQuery(document).ready(function() {
    
	  table = $("#table").DataTable({
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
				 
		tableLog = $("#tableLog").DataTable({
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
	
	$( "#dvTabs" ).tabs()
	
	setTimeout( recargar, 60*1000*5 );
	
	$( "#consultarLog" ).click(function(){
		
	
		$.ajax({
			type	: "POST",
			url		: "Autorizacion_Medicamentos_Backend.php?accion=consultarLog",
			dataType: "json",
			data	: {
						'historia' 		: $( "#logHis").val(),
						'ingreso' 		: $( "#logIng").val(),
						'wemp_pmla' 	: $( "#empresa").val(),
						'consultaAjax' 	: '',
					},
			success	: function ( data ) {
						console.log(data)
						
						tableLog.clear();
						// tableLog.rows.add( data ).draw();
						tableLog.rows.add( data.data ).draw();
					},
			error: function () {
						alert("error fatal");
					},
		});
	})
 
});

function recargar(){
	document.forms[0].submit();
}

function guardar(id){

	if( $('#autoriza'+id).val() == '' || $('#j_medico_autoriza'+id).val() == '' ){
		jAlert( "Debe ingresar una justificacion e indicar si autoriza o no el medicamento", "Alerta" );
		return;
	}
	else{
		var codigo_medicamento 				= $('#codigo'+id).text();
		var nombre_medicamento 				= $('#n_medicamento'+id).text();
		var codigo_medico_ordena 			= $('#c_medico_ordena'+id).text();
		var justificacion_medico_ordena 	= $('#j_medico_ordena'+id).text();
		var justificacion_medico_autoriza 	= $('#j_medico_autoriza'+id).val();
		var autoriza 						= $('#autoriza'+id).val();
		var historia 						= $('#historia'+id).val();
		var ingreso 						= $('#ingreso'+id).val();
		
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
		var wemp_pmla = $('#empresa').val();
		
		$.ajax({
			type	: "POST",
			url		: "Autorizacion_Medicamentos_Backend.php?accion=actualizar",
			data	: {
						'fila' 		: fila,
						'wemp_pmla' : wemp_pmla,
					},
			success	: function () {
						alert('Guardado exitosamente');
						$( "."+historia+"-"+ingreso+"-"+codigo_medicamento ).hide();
						$("#table").DataTable().draw();
					},
			error: function () {
						alert("error fatal");
					},
		});
	}
}