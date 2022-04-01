var startDate =  moment().format("YYYY-MM-DD");
var endDate = moment().format("YYYY-MM-DD"); 
$.datepicker.regional['esp'] = {
	closeText: 'Cerrar',
	prevText: 'Antes',
	nextText: 'Despues',
	monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
	'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
	monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
	'Jul','Ago','Sep','Oct','Nov','Dic'],
	dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
	dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
	dayNamesMin: ['D','L','M','M','J','V','S'],
	weekHeader: 'Sem.',
	dateFormat: 'yy-mm-dd',
	changeYear: true,
	changeMonth: true,
	yearRange: '-10:+10'
	};
$.datepicker.setDefaults($.datepicker.regional['esp']);

$(document).ready(function() {
   
	$("#wfecha_i").datepicker({
		showOn: "button",
		buttonImage: "../../images/medical/root/calendar.gif",
		buttonImageOnly: true,
		maxDate:"+2Y"
	  });

	  $("#wfecha_f").datepicker({
		showOn: "button",
		buttonImage: "../../images/medical/root/calendar.gif",
		buttonImageOnly: true,
		maxDate:"+2Y"
	  });

   
    var start = moment().subtract(29, 'days');
    var end = moment();

    var selectsede = '';
    if (localStorage.getItem('sede') != null) {
        selectsede = localStorage.getItem('sede');
    }


    $('#filtro').daterangepicker({
		"autoApply": true,
		startDate: moment(),
		endDate: moment(),
		ranges: {

			'Hoy': [moment(), moment()],
			'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Los ultimos 7 dias': [moment().subtract(6, 'days'), moment()],
			'Ultimos 30 dias': [moment().subtract(29, 'days'), moment()],
			'Este mes': [moment().startOf('month'), moment().endOf('month')],
			'El mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		},
		"locale": {
			"format": "YYYY-MM-DD",
			"separator": " - ",
			"applyLabel": "Aplicar",
			"cancelLabel": "Cancelar",
			"fromLabel": "From",
			"toLabel": "To",
			"customRangeLabel": "Modificar",
			"weekLabel": "W",
			"daysOfWeek": [
				"Do",
				"Lu",
				"Ma",
				"Mi",
				"Ju",
				"Vi",
				"Sa"
				],
				"monthNames": [
					"Enero",
					"Febrero",
					"Marzo",
					"Abril",
					"Mayo",
					"Junio",
					"Julio",
					"Agosto",
					"Septiembre",
					"Octubre",
					"Noviembre",
					"Disiembre"
					],
					"firstDay": 1
		},
	},
	function(start, end,label) {
		// Parse it to a moment
		var s = moment(start.toISOString());
		var e = moment(end.toISOString());
		startDate = s.format("YYYY-MM-DD");
		endDate = e.format("YYYY-MM-DD");
        

	});

    var table = $('#lactarios').DataTable( {
        language: {
            "decimal": "",
            "emptyTable": "No hay informacion ",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        scrollX: true,
		pageLength: 5,
        paging: true,
           
    	});

        $("#generar").click( function() {
		 $("#lactarios").DataTable().clear().draw();
			fechaini = $("#wfecha_i").val();
			fechafin = $("#wfecha_f").val();
            $.ajax({
                url : "Lactarios/backReporte.php",
                type: "POST",
                data: {"fecha_ini":fechaini, "fecha_fin":fechafin, "accion": "consultar", "wemp_pmla": "01","selectsede": selectsede },
                dataType: "JSON",
                success: function(respuesta)
                {
                    if (respuesta.status) {
                        $("#lactarios").DataTable().clear();
                        $.each(respuesta.datos, function (index, value) {
                            $('#lactarios').dataTable().fnAddData( 
                                [
                                    
                                    value.Enthis,
                                    value.Enting,
                                    value.Entnom,
                                    value.Entart,
                                    value.Entcan,
                                    value.Entusu,
                                    value.Enture,
                                    value.Entfec,
                                    value.Enthor,
									value.Entest
                                ]);
                        });
                    }
                   
                    
                    
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error en cargar datos');
                }
		    });
        } );
} );