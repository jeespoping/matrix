<?php
include_once("conex.php");
if(!isset($consultaAjax))
{
	if(!isset($_SESSION['user']))
	{
	    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;font-size:12pt;text-align:center;" >
	                <br><br>[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.<br><br><br>
	            </div>';
	    return;
	}

	header("Content-Type: text/html;charset=ISO-8859-1");

?>
<html>
<head>
<title>MATRIX - [AGENDA ASIGNACION ESPECIALIDAD]</title>
	<!-- <script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script> -->
	<script type='text/javascript' src='../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js'></script>

    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

	<link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet' />
	<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet' />

	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>

<script type='text/javascript'>
function cargaToolTip()
{
	var cont1 = 1;
	while(document.getElementById('wide'+cont1))
	{
		 $('#wide'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		 cont1++;
	}
	// --> Tooltip
	$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
}


 function atencionAutorizada(his, ing, e){


	 var sin_aut = "";

	 $( ".atencion_autorizada" ).each(function( index ) {
				 if ($(this).is(':checked')) {
				 }else{
					 sin_aut = sin_aut+$(this).attr('his_ing')+" "+$(this).val()+"\n";
				 }
		});

	 jAlert("Los siguientes pacientes no tiene marcado la atenci?n autorizada:"+"\n\n"+sin_aut, "ALERTA");

	 if ($(e).is(':checked')) {

		 $(".atencion_aut_"+his+ing).css("background-color", "");
		 var estado_aut = "on";

	 }else{

		 $(".atencion_aut_"+his+ing).css("background-color", "ECB782");
		 var estado_aut = "off";
	 }

	 $.ajax({
				url: "agenda_urgencias_por_especialidad.php",
				type: "POST",
				data:{
					wemp_pmla		: $("#wemp_pmla").val(),
					consultaAjax 	: 'atencionAutorizada',
					operacion 		: '',
					basedatoshce	: $("#wbasedatohce").val(),
					whis			: his,
					wing			: ing,
					estado_aut		: estado_aut
				},
				dataType: "json",
				async: false,
				success:function(data_json) {

					if (data_json.error == 1)
					{

					}
					else{

					}
				}

			});

 }
 //Ventanana emergente para el registro de usuarios
 //Se cambia a jquery esta funcion para que funcione de forma mas optima al permitir interactuar con la interfaz.
  function asignarEspecialidad(i, turno)
    {

    var historia = $("#wpaciente"+i).val();
	var ingreso = $("#wingreso"+i).val();

    var wesp_ing_sala = $("#wespec_ing_sala").val(); //Especialidad ingreso a sala
    var wing_sala_cond = $("#wing_sala_cond").val(); //Conducta de ingreso a sala
    var wing_sala_triage = $("#wing_sala_triage").val(); //Triage ingreso a sala
    var wing_sala_triage_desc = $("#wing_sala_triage_desc").val(); //Descripcion del nivel de triage
    var wesp_asoc_ing_sala = $("#wesp_asoc_ing_sala").val(); //Especialidad asociada a ingreso a sala

	var id = historia+ingreso;
	var wespecialidad_datos = document.getElementById('wespecialidad'+i).value.split("-");
	var wespecialidad = wespecialidad_datos[0];
	var triage_automatico = wespecialidad_datos[1];

		$.post("agenda_urgencias_por_especialidad.php",
		{
			consultaAjax:      'validarSiPacienteYaEstaEnLLamado',
			wemp_pmla:			document.forms.forma.wemp_pmla.value,
			basedatos:			document.forms.forma.wbasedato.value,
			basedatoshce:		document.forms.forma.wbasedatohce.value,
			turno:				turno
		}, function(respuesta){
			if(respuesta.Error)
				alert(respuesta.Mensaje);
			else
			{
				$.post("agenda_urgencias_por_especialidad.php",
				{
					consultaAjax:      '10',
					wemp_pmla:			document.forms.forma.wemp_pmla.value,
					basedatos:			document.forms.forma.wbasedato.value,
					basedatoshce:		document.forms.forma.wbasedatohce.value,
					especialidad:		wespecialidad,
					paciente:			document.getElementById('wpaciente'+i).value,
					ingreso:			document.getElementById('wingreso'+i).value,
					servicio:			document.getElementById('wservicio'+i).value,
					seguridad:			document.getElementById('wseguridad').value,
					wespec_triage_auto:	triage_automatico

				}
				,function(data) {
						if(data.error == 1)
						{
						alert(data.mensaje);
						return;

						}
						else
						{
						//Asigna la palabra nivel de triage 1 al paciente si selecciona emergencia
						$('#'+id).html(data.descrip_triage);
						}
					},
					"json"
				);
			}
		}, 'json');
    }

// Llamado ajax para dar de alta al paciente
function altaPaciente(i, turno)
{


	if(confirm("Realmente desea dar de alta el paciente"))
	{
		var parametros = "consultaAjax=11&basedatos="+document.forms.forma.wbasedato.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&codcco="+document.forms.forma.codCco.value+"&paciente=" +document.getElementById('wpaciente'+i).value+"&ingreso=" +document.getElementById('wingreso'+i).value+"&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&wcubiculo_ocupado="+document.getElementById('wcubiculo_ocupado'+i).value+"&seguridad="+document.getElementById('wseguridad').value+"&turno="+turno;

		try
		{

			var ajax = nuevoAjax();

			ajax.open("POST", "agenda_urgencias_por_especialidad.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if(ajax.responseText.trim()!="ok")
				alert(ajax.responseText.trim());

		}catch(e){	}
	}
	else
	{
		document.getElementById('alta'+i).checked = false;
		return false;
	}
	agendaUrgencias();
}

// Llamado ajax para dar de alta por muerte al paciente
function muertePaciente(i)
{
	if(confirm("Confirme la muerte del paciente"))
	{
		var parametros = "consultaAjax=12&basedatos="+document.forms.forma.wbasedato.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&codcco="+document.forms.forma.codCco.value+"&paciente=" +document.getElementById('wpaciente'+i).value+"&ingreso=" +document.getElementById('wingreso'+i).value+"&seguridad=" +document.getElementById('wseguridad').value+"&wcubiculo_ocupado="+document.getElementById('wcubiculo_ocupado'+i).value;

		try
		{
			var ajax = nuevoAjax();

			ajax.open("POST", "agenda_urgencias_por_especialidad.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if(ajax.responseText.trim()!="ok")
				alert(ajax.responseText.trim());

		}catch(e){	}

	}
	else
	{
		document.getElementById('muerte'+i).checked = false;
		return false;
	}
	agendaUrgencias();
}

// Llamado ajax para activar de nuevo un paciente dado de alta
function activarPaciente(i)
{
	if(confirm("Realmente desea activar el paciente"))
	{
		var parametros = "consultaAjax=15&basedatos="+document.forms.forma.wbasedato.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&codcco="+document.forms.forma.codCco.value+"&paciente=" +document.getElementById('wpaciente'+i).value+"&ingreso=" +document.getElementById('wingreso'+i).value+"&seguridad=" +document.getElementById('wseguridad').value;

		try
		{
			var ajax = nuevoAjax();
			ajax.open("POST", "agenda_urgencias_por_especialidad.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if (ajax.readyState==4 && ajax.status==200)
			{
				if(ajax.responseText.trim()=="activo")
					alert('El paciente ya se encuentra activo con otro ingreso');
				if(ajax.responseText.trim()=="inactivounix")
					alert('El paciente no se puede activar porque no est? activo en Unix');
			}

		}catch(e){	}

	}
	else
	{
		document.getElementById('muerte'+i).checked = false;
		return false;
	}
	agendaUrgencias();
}

// Llamado ajax para activar de nuevo un paciente dado de alta
function reasignarPaciente(i)
{
	if(confirm("Realmente desea reasignar la historia del paciente"))
	{
		var parametros = "consultaAjax=16&basedatos="+document.forms.forma.wbasedato.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&codcco="+document.forms.forma.codCco.value+"&paciente=" +document.getElementById('wpaciente'+i).value+"&ingreso=" +document.getElementById('wingreso'+i).value+"&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&seguridad=" +document.getElementById('wseguridad').value;

		try
		{
			var ajax = nuevoAjax();
			ajax.open("POST", "agenda_urgencias_por_especialidad.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if (ajax.readyState==4 && ajax.status==200)
			{
				if(ajax.responseText.trim()=="no-reasignar")
					alert('El paciente tiene m?s de 1 ingreso. No se puede reasignar este n?mero de historia');
			}

		}catch(e){	}

	}
	else
	{
		document.getElementById('reasignar'+i).checked = false;
		return false;
	}
	agendaUrgencias();
}

// Llamado ajax que permite mostrar la lista de pacientes en urgencias
function agendaUrgencias()
{
	var parametros = "";

	parametros = "consultaAjax=13&basedatos="+document.forms.forma.wbasedato.value+"&modoConsulta="+$("#modoConsulta").val()+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&ccoCodigo="+document.forms.forma.codCco.value+"&esUrgencias="+document.forms.forma.esUrgencias.value+"&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&seguridad=" +document.getElementById('wseguridad').value;

	try
	{
		try {
			$.blockUI({ message: $('#msjEspere') });
		} catch(e){ }

		var ajax = nuevoAjax();

		ajax.open("POST", "agenda_urgencias_por_especialidad.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			var contenedor = document.getElementById('agendUrg');
			if (ajax.readyState==4 && ajax.status==200)
			{
				contenedor.innerHTML=ajax.responseText.trim();
				cargaToolTip();

				// --> Blink a la alerta de superacion del tope de la sala
				var mensajeAtencion = setInterval(function(){
					$("[blinkTope=si]").css('visibility' , $("[blinkTope=si]").css('visibility') === 'hidden' ? '' : 'hidden')
				}, 400);

				// --> 	Si es modo consulta, deshabilitar todos los campos
				// 		2015-11-25, jerson trujillo.
				if($("#modoConsulta").val() == "on")
					$("input[type=text], input[type=checkbox], select").attr("disabled", "disabled");
			}
			try {
				$.unblockUI();
			} catch(e){ }
		}

		if ( !estaEnProceso(ajax) )
		{
			ajax.send(null);
		}
	}catch(e){	}
}

// Llamado ajax para ingresar el paciente a la lista de pacientes en urgencia
/*
// Se usaba para el ingreso del paciente por campo de texto
// Ver actualizaci?n de Abril 20 de 2011
function ingresarPacienteUrg()
{
	var parametros = "consultaAjax=14&basedatos="+document.forms.forma.wbasedato.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&ccoCodigo="+document.forms.forma.codCco.value+"&whistoria=" +document.getElementById('whistoria').value+"&wemp_pmla="+document.forma.wemp_pmla.value+"&seguridad=" +document.getElementById('wseguridad').value;

	try
	{
		var ajax = nuevoAjax();

		ajax.open("POST", "agenda_urgencias_por_especialidad.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if(ajax.responseText=='no-urg')
		{
			alert('El paciente no se encuentra en urgencias o el n?mero de la historia ingresada no existe.');
		}
		else if(ajax.responseText=='existente')
		{
			alert('El paciente ya se ha ingresado');
		}
		else
		{
			agendaUrgencias();
		}

		document.getElementById('whistoria').value = '';

		if ( !estaEnProceso(ajax) )
		{
			ajax.send(null);
		}
	}catch(e){	}
}
*/

// Llama a la funci?n de ingreso de paciente a urgencias cuando se teclea enter en el textbox
document.onkeypress=function(e)
{
	var esIE=(document.all);
	var esNS=(document.layers);
	tecla=(esIE) ? event.keyCode : e.which;
	if(tecla==13)
	{
		ingresarPaciente();
	}
}

// Valida la historia cl?nica digitada para el ingreso del paciente
function ingresarPaciente()
{
	var historia = document.forms.forma.whistoria.value;
	var valido = true;
	var mensaje = "";

	//validaciones
	if(historia == '')
	{
		mensaje += "Debe especificar una historia cl?nica \n\r";
		document.forms.forma.whistoria.value = '';
		valido = false;
	}
	else
	{
		//Prueba el patron regular... empezar por 1-9, seguido de digitos
		var reg = new RegExp("^[1-9]+[0-9]*");
    	if(!reg.test(historia))
		{
			mensaje += 'Debe especificar un numero de historia compuesta por digitos.  Sin cero adelante. \n\r';
			document.forms.forma.whistoria.value = '';
			valido = false;
    	}

		//Historia cero no se permite
		if(historia == '0')
		{
			mensaje += 'Debe especificar una historia cl?nica diferente de cero \n\r';
			document.forms.forma.whistoria.value = '';
			valido = false;
		}
	}

	if(!valido)
	{
		alert("Error: \n\r" + mensaje);
		return false;
	}
	else
	{
		if(confirm("Desea ingresar el paciente a urgencias?"))
		{
			ingresarPacienteUrg();
		}
		else
		{
			return false;
		}
	}
}
//-----------------------------------------------------------------------------------------------------
//	--> Funcion que hace el llamado para asignarle al paciente una sala de espera fisica de urgencias
//		Jerosn trujillo, 2015-07-22
//-----------------------------------------------------------------------------------------------------
function asignarSalaEspera(turno)
{
	if($("#selectSalaDeEspera-"+turno).val() != '' )
	{
		$.post("agenda_urgencias_por_especialidad.php",
		{
			consultaAjax:   	'asignarSalaEspera',
			wemp_pmla:        	$('#wemp_pmla').val(),
			basedatos:			document.forms.forma.wbasedato.value,
			basedatoshce:		document.forms.forma.wbasedatohce.value,
			turno:				turno,
			salaEspera:			$("#selectSalaDeEspera-"+turno).val()
		}, function(respuesta){
			if(respuesta.mostrarAlerta)
			{
				$("#selectSalaDeEspera-"+turno).val(respuesta.nuevaSala);
				alert(respuesta.mensaje);
			}
		}, 'json');
	}
}
//-----------------------------------------------------------------------------------------------------
//	--> Funcion que hace el llamado para asignarle al paciente una clasificacion
//		Jerosn trujillo, 2015-08-19
//-----------------------------------------------------------------------------------------------------
function asignarClasificacion(turno)
{
	if($("#selectClasificacion-"+turno).val() != '' )
	{
		$.post("agenda_urgencias_por_especialidad.php",
		{
			consultaAjax:   	'asignarClasificacion',
			wemp_pmla:        	$('#wemp_pmla').val(),
			basedatos:			document.forms.forma.wbasedato.value,
			turno:				turno,
			clasificacionSelec:	$("#selectClasificacion-"+turno).val()
		}, function(respuesta){
		}, 'json');
	}
}
//-----------------------------------------------------------------------------------------------------
//	--> Funcion que asigna el genero medico que el paciente quiere que lo atienda
//		Jerosn trujillo, 2015-09-22
//-----------------------------------------------------------------------------------------------------
function asignarGeneroMedicoQueDebeAtender(Idgenero,historia,ingreso)
{
	genero = $("#generoMedicoAtender"+Idgenero).val();

	$.post("agenda_urgencias_por_especialidad.php",
	{
		consultaAjax:   		'asignarGeneroMedicoQueDebeAtender',
		wemp_pmla:        		$('#wemp_pmla').val(),
		basedatos:				document.forms.forma.wbasedato.value,
		wbasedatohce:			document.forms.forma.wbasedatohce.value,
		historia:				historia,
		ingreso:				ingreso,
		generoMedicoAtender:	genero
	}, function(respuesta){

	}, 'json');

}

window.onload = function() { agendaUrgencias(); }

</script>
<style type="text/css">
	.fila1Local
	{
		background-color: #C3D9FF;
		color: #000000;
		font-size: 9pt;
		padding:1px;
	}
	.fila2Local
	{
		background-color: #E8EEF7;
		color: #000000;
		font-size: 9pt;
		padding:1px;
	}
	.fondoAmarilloLocal
	{
		background-color: #ffffcc;
		color: #000000;
		font-size: 9pt;
		padding:1px;
	}
</style>
</head>

<body>

<?php
include_once("conex.php");

/*
 **************************** AGENDA URGENCIAS ***************************************************
 ****************************** DESCRIPCI?N ***************************************************
 * Permite el registro de pacientes en urgencias.
 * Lista los pacientes ingresados y permite asignar el m?dico tratante de cada uno
 * Muestra el estado en que se encuentran los pacientes tratados
 * Permite establecer estado de alta o muerte para el paciente en urgencias
 *************************************************************************************************
 * Autor: John M. Cadavid. G.
 * Fecha creacion: 2011-02-16
 *************************************************************************************************
 * MODIFICACIONES
  *************************************************************************************************
 * 2021-12-18: Sebasti?n Nevado
                Se hace modificacion para llevar las funciones esCirugia, y consultarCcoUrgencias a comun.
  *************************************************************************************************
 * 2020-04-02: Edwin MG
                Al actualizar el genero se realiza de acuerdo a la historia e ingreso y no por el turno (consulta ajax asignarGeneroMedicoQueDebeAtender)
 *************************************************************************************************
 * 2019-03-05: Arleyda I.C.
                Migraci?n realizada
 * 2018-05-23: Jonatan Lopez
				Se muestra alerta con los pacientes que no tienen marcada la atencion autorizada, ademas se agrega color de fondo naranja con los no marcados.
  *************************************************************************************************
 * 2018-03-12: Jonatan Lopez
				Se agrega columna atencion autoriza que contiene un checkbox el cual se debe marcar para que el medico pueda atender al paciente, quiere
				decir para que se liste en el programa Sala_de_espera_por_Especialidad.php
  *************************************************************************************************
 * 2017-11-07: Jonatan Lopez
				Se agrega la funcion cancelarPedidoInsumos para que al momento de dar alta definitiva al paciente se cancele el pedido de Insumos.
  *************************************************************************************************
 * 2017-09-19: Jonatan Lopez
 *				- Se valida si el paciente tiene insumos pendientes por aplicar o devolver, ademas se muestran los auxiliares que tienen esos saldos.
  *************************************************************************************************
 * 2017-04-25:	Edwar Jaramillo.
 * 				- Se actualiz? versi?n del jquery de 1.3 a 1.7 con el fin de usar ventanas dialog de jquery y plugin jAlert.
 * 				- Modificaci?n en la validaci?n de sesi?n de usuario al cargar el programa.
 * 				- Inicializaci?n de algunas variables que generaban warnings.
  *************************************************************************************************
 * 2015-08-24:	Jerson trujillo. Se agregan nuevas columnas a la lista de pacientes activos (Turno, clasificacion y sala de espera)
				Esto es para la implementacion del sistema del turnero, el cual permite hacer un seguimiento al paciente en toda su
				estancia en urgencias por un numero de turno. La clasificacion y sala de espera se asignan automaticamente dependiendo
				del responsable del paciente, pero con la opcion de ser modificadas si se desea.
  *************************************************************************************************
 * 2015-08-19 - El listado de especialidades ya no dependera de la tabla de medicos sino de la tabla de especialidades con el campo Espaur activo. (Jonatan)
  *************************************************************************************************
 * 2015-07-15 - Se libera el cubiculo del paciente con la historia e ingreso y no con el codigo del cubiculo. (Jonatan)
  *************************************************************************************************
 * 2015-06-23 - Se guarda en la tabla log_agenda cuando el paciente no es liberado del cubiculo. (Jonatan)
  *************************************************************************************************
 * 2015-03-26 - Se libera el cubiculo cuando el paciente tiene ingreso mayor en matrix que en unix, osea cuando hay borrado de registros. (Jonatan)
  *************************************************************************************************
 * 2015-03-19 - Verifica si el paciente tiene saldo pendiente por aplicar o descartar. (Jonatan)
 *************************************************************************************************
 * 2014-12-04 - Se corrige la busqueda en el array de cubiculos, para que el key del array sea historia
				e ingreso juntos y haga la busqueda correctamente. (Jonatan)
 *************************************************************************************************
 * 2014-12-01 - Se limpia la sala y la asignacion de cubiculo para los pacientes que son reactivados. (Jonatan)
			  - Se muestra la ubicacion del paciente asi no tenga cubiculo asignado. (Jonatan)
 *************************************************************************************************
 * 2014-11-28 - Se libera el paciente de la tabla movhos_000020 si seleccionaron muerte, ademas se actualizan los datos de la tabla
				movhos_000020 habfal, habhal. (Jonatan)
 *************************************************************************************************
 * 2014-11-12 - Si el paciente tiene cubiculo o camilla asignado se mostrara en pantalla, ademas al seleccionar alta definitiva se liberar?. (Jonatan)
 *************************************************************************************************
 * 2013-10-16 - Se valida si la especialidad seleccionada es de triage automatico, si es asi actualizara los datos en la tabla hce_000022,
 *				segun lo que venga del campo Espcfg de la tabla movhos_000044, con respecto al codigo de la especialidad. - Jonatan
 *************************************************************************************************
 * 2013-06-21 - Se agreg? la codici?n AND Espcex = 'on' en el query de la funci?n consultarEspecialidades
 *************************************************************************************************
 * 2013-06-20 - Se cre? el par?metro esUrgencias que determina si el centro de costo consultado es urgencias o no
 * 				este par?metro se usa en la funci?n consultarEspecialidades para determinar el filtro que se pone
 *				en la consulta de especialidades - Mario Cadavid
 *************************************************************************************************
 * 2013-06-18 - Se agreg? la condici?n para definir si existe el par?metro $wservicio, si es as?, en vez de consultar
 * 				el centro de costo de urgencias, como es por defecto, se consulta el servicio que se haya enviado por URL, de este modo
 *				el script ya aplica no solo para urgencias sino tambi?n en general para otros centros de costo - Mario Cadavid
 *************************************************************************************************
 * 2013-05-22 - En las funci?n agendaPacientesUrgencias se comenta el llamado a la funci?n actualizarPacientesUnix
 * 				de modo que este programa ya no escanear? los pacientes desde Unix para ingresarlos en Matrix
 *				ni dar? de alta en matrix los pacientes que no est?n activos en Unix. Este proceso ya se realizar?
 * 				desde un cron job, esto porque este proceso de escaneo esaba haciendo muy lenta la ejecuci?n y carga
 * 				de este script - Mario Cadavid
 *************************************************************************************************
 * 2013-05-10 - En las funciones muertePacienteUrgencias y altaPacienteUrgencias se agreg? la consulta para saber
 * 				si el cco de ingreso del paciente es hospitalario (se busca en la tabla 000022 de historia clinica electronica (Mtrcci) )
 *				y no tiene cco anterior registrado, (se busca en la tabla 000018 de movimiento hospitalario (Ubisan) )
 * 				Si es as? no se debe registrar el egreso en la tabla 000033 de movimiento hospitalario
 * 				Esto porque hubo un paciente al que le registraron por error cco de ingreso 1180 en vez de poner 1800
 *				lo cual hizo que al dar de alta al paciente se afectara los indicadores de egresos de cco hospitalarios - Mario Cadavid
 *************************************************************************************************
 * 2013-04-25 - Cuando se da de alta o muerte, se cancela la solicitud de dieta con la funcion
 *				cancelar_pedido_alimentacion - Frederick Aguirre
 *************************************************************************************************
 * 2013-04-23 - En la funci?n activarPacienteUrgencias se agreg? la asignaci?n Mtrmed='' en el query de actualizaci?n
 * 				de la tabla 000022 de historia clinica electronica. Esto porque cuando un m?dico ya habia tomado un paciente
 *				y ?ste es dado de alta, cuando se reactiva el paciente ningun otro medico lo pod?a tomar pues quedaba inactivo
 *				el bot?n Ir a historia en el programa de sala de espera.
 *************************************************************************************************
 * 2013-03-27 - En la funci?n actualizarPacientesUnix se incluy? la consulta a la tabla insercco de Unix cuando
 * 				no se encuentra la relaci?n de cco - servicio en la tabla 11 de movimiento hospitalario
 *************************************************************************************************
 * 2013-03-21 - En la funci?n activarPacienteUrgencias se agreg? Ubialp='off' en el query de actualizaci?n de la tabla 000018 de movimiento hospitalario
 *              Esto porque se solicit? que cuando se re-active un alta se quite el alta en proceso si la ten?a
 *************************************************************************************************
 * 2013-03-12 - Se modifica la funcion asignarEspecialidadUrgencias para que al momento de seleccionar la especialidad Ingreso Directo el paciente
 *              quede en conducta observacion, especialidad asociada medicina general, nivel de triage 2 y sin medico asociado. - Jonatan Lopez
 *************************************************************************************************
 * 2013-03-06 - En la funci?n consultarPacienteUnix se adicion? los campos direccion y municipio a la clase paciente
 *				En las funciones actualizarResponsablePaciente e insertarResponsablePaciente se adicion? la grabaci?n
 *				en los nuevos campos Ingdir e Ingmun de la tabla 000016 de movimiento hospitalario, grabaci?n que se hace
 *				desde los campos direccion y municipio a la clase paciente. - Mario Cadavid
 * *************************************************************************************************
 * 2013-02-26 - Se agrega la funcion BorrarAltasMuertesAntesDeAgregarNueva que elimina las altas y muertes de un paciente y actualiza el indicador
 *				Esto para garantizar que un paciente solo tenga una alta o una muerte en movhos33
 *				Se agregan condiciones a dos querys que eliminaban todos los egresos del paciente de movhos33 - Frederick Aguirre
 *************************************************************************************************
 * 2013-02-22 - En las funciones reasignarPacienteUrgencias y altaPacienteurgencias se agreg? una consulta para definir
 *				si los registros de historia e ingreso que se van a borrar tiene registros de movimiento hospitalario, si los tiene,
 * 				no se borra. En la funci?n altaPacieteUrgencias esto aplica cuando $tipo_alta == "borrado" - Mario Cadavid
 *************************************************************************************************
 * 2013-02-18 - En las funciones altaPacienteUrgencias y muertePacienteUrgencias ya no se modifican los campos
 *				Mtrtra y seguridad en la tabla hce_000022, tampoco se modifica los campos Ubialp, Ubifap y Ubihap
 *				en la tabla movhos_000018. En la funci?n activarPacienteUrgencias solo se dej? modificando los campos
 *				Ubimue='off', Ubiald='off', Ubifad='0000-00-00', Ubihad='00:00:00', Ubiuad=''; es decir solo lo que tenga
 *				que ver con el alta.  - Mario Cadavid
 *				En la funci?n borraIngresosMayores se agreg? una consulta para definir si los registros de historia e ingreso
 *				que se van a borrar tiene registros de movimiento hospitalario, si los tiene, no se borra. Esto porque se present?
 *				la inactivaco?n en unix de una histor?a cuyo paciente estaba ya hospitalizado, lo que hizo que el sistema tomara
 *				como mayor el ingreso actual y borrara sus registros, volviendolos a ingresar cuando reactivaron la historia en unix
 *				pero en el reingreso se perdieron los datos de movimiento hospitalario - Mario Cadavid
 *************************************************************************************************
 * 2013-02-08 - Se adicion? la funci?n obtenerRegistrosFila que permite almacenar todos los datos de una fila
 *				de la base de datos en una cadena tipo string. Esta funci?n sirve para guardar en la tabla
 * 				log_agenda los datos de una fila de la base de datos antes de ?sta ser actualizada o borrada.
 *			 	Estos se debi? hacer porque se borr? el ingreso de 4 historias que estaban activas y en hospitalizaci?n,
 *				es decir, con registros en la tabla movhos_000020. Guardando el dato de la fila antes de ser borrada
 *  			se podr? saber porque se borr? y determinar si alguien le di? de alta o cambi? alg?n dato que hace
 *				que este programa no tome la historia como activa en Matrix - Mario Cadavid
 *************************************************************************************************
 * 2013-02-04 - Al seleccionar especialidad emergencia tambien se registra la fecha y hora de triage - Jonatan Lopez
 *************************************************************************************************
 * 2013-01-31 - En la asignacion de especialidad se asocia al paciente Nivel 1 de triage si selecciona
 *				emergencia, ademas al seleccionar esta especialidad se iniciara la consulta- Jonatan Lopez
 *************************************************************************************************
 * 2012-12-20 - En la funci?n altaPacienteUrgencias se incluyo la actualizaci?n de tabla 000020 de movimiento
 * 				hospitalario, de modo que desocupe la habitaci?n, si la tiene, del paciente dado de alta - Mario Cadavid
 *************************************************************************************************
 * 2012-12-04 - Se agreg? la columna M?dico en la lista de Pacientes Activos - Mario Cadavid
 *************************************************************************************************
 * 2012-07-18 - Se adicion? la grabaci?n del centro de costo de ingreso (Mtrcci) en la tabla hce_000022 - Mario Cadavid
 *************************************************************************************************
 * 2012-06-14 - Se quitaron espacios en codigo html que pudieran afectar la respuesta ajax ya que las funciones javascript
 *				validan si la respuesta es igual a "ok" para definir si muestra un mensaje de advertencia o no y estaba
 *				llegando ok pero con un salto de linea - Mario Cadavid
 *************************************************************************************************
 * 2012-04-11 - Se unieron los dos scripts (agenda_urgencias_por_especialidad.php y auxAgendaUrgencias_por_especialidad.php) en uno solo
 *				Se modifico la consulta principal en la funci?n consultarPacienteUnix para que no se frenara
 *				el script si existe algun registro Nulo, sto por el cambio al nuevo servidor - Mario Cadavid
 *************************************************************************************************
 * 2012-03-16 - Se adicion? una condici?n m?s antes de borrar un ingreso, de modo que primero se consulte
 *				inpaci y si el ingreso a borrar es mayor que el ingreso actual en inpaci se borra (if($ingreso>$ing_act))
 *				sino se da alta normal - Mario Cadavid
 *************************************************************************************************
 * 2012-02-20 - En la funci?n "actualizarDatosPacientes" se adicion? el llamado a la funci?n
 *				"borrarHistoriaDiferenteUnix" ya que cuando en unix se hacia un cambio despu?s de
 *				haber ingresado el paciente y esto implicaba datos duplicidad de datos en un registro ya existente
 *				en root_000037 el sistema no borraba este registro y causaba error de clave duplicada - Mario Cadavid
 *************************************************************************************************
 * 2012-01-24 - En las funciones "actualizarAltaPacientesUnix" y "actualizarPacientesUnix" se modific?
 *				el query de consulta de pacientes activos en Unix de modo que solo consulte inpac
 *				y no las dem?s tablas (insercco,inemp) ya que no se necesitan datos de estas
 *				y al consultarlas se estabn trayendo algunas historias duplicadas - Mario Cadavid
 *************************************************************************************************
 *************************************************************************************************
 * 2012-01-19 - En la funci?n "altaPacienteUrgencias" se agreg? la validaci?n de la fecha de egreso
 *				en Unix. Si fecha de egreso en unix es igual a la fecha actual no se da de alta
 *				Se cre? la funci?n "actualizarDatosPacientes" que actualiza los datos de los pacientes
 *				activos en cl?nica, siempre y cuando se encuentren estos datos diferentes en Unix - Mario Cadavid
 *************************************************************************************************
 * 2011-11-29 - Se modific? la funci?n ingresarPacientesUrgencias para que tenga en cuenta cuando
 *				la historia y/o c?dula en matrix no corresponde con las de Unix. Para esto tambi?n
 *				se creo la funci?n borrarHistoriaDiferenteUnix  - Mario Cadavid
 *************************************************************************************************
 * 2011-11-28 - Se agreg? la condici?n si mysql_affected_rows() antes de grabar en log_agenda para garantizar
 *				que si se ejecuto la acci?n que se graba en la tabla de log_agenda - Mario Cadavid
 *************************************************************************************************
 * 2011-11-27 - Se agreg? grabaci?n en la tabla de log_agenda para todas las acciones que se
 *				ejecuten en el sistema, no solo para las de borrado como estaba
 *				En la funci?n borraIngresosMayores se cambi? Ubiing >= ".$ingreso." por
 *				Ubiing*1 >= ".$ingreso." para que hiciera la comparaci?n correctamente - Mario Cadavid
 *************************************************************************************************
 * 2011-11-25 - Cuando se llama la funci?n borraIngresosMayores, se estaba llevando el ingreso de Matrix
 *				se cambi? para que lleve el ingreso de unix - Mario Cadavid
 *************************************************************************************************
 * 2011-11-23 - En el Query de la funci?n obtenerCcoMatrix se adicion? la condici?n de que el
 *				centro de costo sea de ingreso (ccoing='on') para que no se ingresen pacientes a
 *				centros de costos que no son de ingreso - Mario Cadavid
 *************************************************************************************************
 * 2011-11-11 - Se agreg? la columna de Afinidad del paciente tanto en la lista de pacientes activos
 *				como en la lista de pacientes inactivos - Mario Cadavid
 *************************************************************************************************
 * 2011-10-31 - Se modificaron las funciones insertarIngresoPaciente y actualizarIngresoPaciente de modo que
 *				cuando la adici?n o edici?n en la tabla root_000037 saque error por clave duplicada,
 *				borre los registros duplicados e inserte o actualice los datos del paciente que se traen desde Unix
 *				Se creo la funci?n esCirugia para verificar si el centro de costo del paciente es cirugia de modo que en
 *				proceso de traslado quede en 'on', es decir, poner Ubiptr de la tabla movhos_000018 en 'on' - Mario Cadavid
 *************************************************************************************************
 * 2011-10-27 - La consulta de pacientes de Unix se hizo general para que se consulte e ingresen todos los
 *				pacientes activos desde unix sin importar el servicio pues se decidi? que desde este script
 *				de urgencias se ingresen todos los pacientes activos de Unix e igual para la alta automatica
 *				de los pacientes que ya no esten en Unix y no tengan conducta asociada - Mario Cadavid
 *************************************************************************************************
 * 2011-10-26 - Se adicion? la funci?n borraIngresosMayores y se modific? la funci?n ingresarPacientesUrgencias
 *				esto para preveer la situaci?n en la que una historia o ingreso es reasignado en Unix
 *				de modo que en matrix se borren los ingresos mayores a los de Unix e igual se actualicen
 *				los datos en las tablas root_000036 y root_000037 en caso de un cambio de c?dula para la historia - Mario Cadavid
 *************************************************************************************************
 * 2011-08-25 - Cuando se escanea para ingreso de pacientes desde Unix se adicion? la funci?n
 *				actualizarDatosPacienteTablaUnica para que actualice los datos de la tabla root_000036
 *				con los que se traen de Unix - Mario Cadavid
 *************************************************************************************************
 * 2011-08-17 - En la consulta de m?dicos se incluyeron las condiciones para meduma diferente de '' y 'NO APLICA' - Mario Cadavid
 *************************************************************************************************
 * 2011-08-11 - Se agrego al LOG que guarde tambien el borrado de la tabla  movhos_000016
 *				Se modific? las consultas que agregan pacientes nuevos a la agenda para que traiga de
 *				Unix no solo los pacientes a partir de ayer sino todos los que esten en Unix
 *				asignados a urgencias, ver campos comentados asi: 	//  AND pacfec >= '".$ayer."'"; - Mario Cadavid
 *************************************************************************************************
 * 2011-07-06 - Se creo una tabla para LOG (log_agenda) para guardar las acciones de borrado de la tabla movhos_000018 - Mario Cadavid
 *************************************************************************************************
 * 2011-06-08 - Se cambio la asignaci?n por m?dico a asignaci?n por especialidad - Mario Cadavid
 *************************************************************************************************
 * 2011-05-13 - Se cambio el query para consultar m?dico asignado en la funci?n agendaUrgencias
 *				ya que estaba tomando pacientes con m?dico en blanco "", y mostraba m?dico
 *				con c?digo en blanco ""
 *				Se cambi? la funci?n actualizarAltaPacientesUnix ya que no se estaba recorriendo
 *				el arreglo de forma correcta, se cambi? la funci?n while ($j < count ($altas_unix))
 *				por foreach ($altas_unix as $j => $value) - Mario Cadavid
 *************************************************************************************************
 * 2011-04-28 - Cuando es alta autom?tica, se cambio la funci?n <altaPacienteUrgencias>
 *				para que verifique si en Unix sigue activa la historia, si es asi no le da alta
 *				Se activaron los checbox de actas y muertes para conductas de alta
 *				Se inactiva checbox de muerte si conducta es alta y viceversa - Mario Cadavid
 *************************************************************************************************
 * 2011-04-25 - Modificaci?n en el ingreso de pacientes a urgencias, se quito el campo
 *				de texto donde se ingresaba la historia cl?nica, ya se toma de Unix los
 *				pacientes en urgencias actualiz?ndose la lista autom?ticamente - Mario Cadavid
 *************************************************************************************************
 * 2011-03-04 - Modificaci?n en el proceso de ingreso del paciente a urgencias para validar los siguientes casos:
 * 				Si un usuario ya est? en movhos 18 pero aun no esta en hce 22, registrarlo en hce 22
 * 				antes se asumia como ya ingresado y no se registraba en hce 22
 *				Validar ingreso de paciente no solo por historia sino tambien por n?mero de ingreso, por ejemplo:
 *				si un usuario est? registrado con ingreso 1 y vuelven y lo entran y tiene ya en UNIX ingreso 2
 *				se debe dar de alta automaticamente el ingreso 1 y adicionarlo a la agenda de urgencias con ingreso 2
 *				Validaci?n al activar un paciente dado de alta, si ya est? activo con otro ingreso no lo deja activar
 *				En reasignar si el paciente tiene mas de un ingreso no deja reasiganar su n?mero de historia - Mario Cadavid
 *************************************************************************************************
 * 2011-02-23 - Adici?n de try catch al ejecutar blockUI debido a que en algunas versiones viejas de IE sacaba un error
 * 				y no dejaba ejecutar la p?gina. Tambi?n se adicion? el evento onload al final del javascript - Mario Cadavid
 *************************************************************************************************
 * 2011-02-22 - Adici?n de columnas Activar y Reasignar historia para pacientes dados de alta - Mario Cadavid
 */


 /***************************************************************************************
 * LOS SIGUIENTES CAMBIOS CORRESPONDEN CUANDO EL PROGRAMA ESTABA DIVIDIDO EN 2 SCRIPTS	*
 ****************************************************************************************/
 /*
 **************************** AUXILIAR DE AGENDA URGENCIAS ************************************
 ****************************** DESCRIPCI?N ***************************************************
 * Contiene las funciones principales que usa el script agenda_urgencias_por_especialidad.php
 * Estas funciones se llaman desde AJAX
 *************************************************************************************************
 * Autor: John M. Cadavid. G.
 * Fecha creacion: 2011-02-16
 *************************************************************************************************
 * MODIFICACIONES
 *************************************************************************************************
 * 2012-02-20 - En la funci?n "actualizarDatosPacientes" se adicion? el llamado a la funci?n
 *				"borrarHistoriaDiferenteUnix" ya que cuando en unix se hacia un cambio despu?s de
 *				haber ingresado el paciente y esto implicaba datos duplicidad de datos en un registro
 *				ya existente en root_000037 el sistema no borraba este registro y causaba error de clave duplicada
 *************************************************************************************************
 * 2012-01-24 - En las funciones "actualizarAltaPacientesUnix" y "actualizarPacientesUnix" se modific?
 *				el query de consulta de pacientes activos en Unix de modo que solo consulte inpac
 *				y no las dem?s tablas (insercco,inemp) ya que no se necesitan datos de estas
 *				y al consultarlas se estabn trayendo algunas historias duplicadas
 *************************************************************************************************
 * 2012-01-19 - En la funci?n "altaPacienteUrgencias" se agreg? la validaci?n de la fecha de egreso
 *				en Unix. Si fecha de egreso en unix es igual a la fecha actual no se da de alta
 *				Se cre? la funci?n "actualizarDatosPacientes" que actualiza los datos de los pacientes
 *				activos en cl?nica, siempre y cuando se encuentren estos datos diferentes en Unix
 *************************************************************************************************
 * 2011-11-29 - Se modific? la funci?n ingresarPacientesUrgencias para que tenga en cuenta cuando
 *				la historia y/o c?dula en matrix no corresponde con las de Unix. Para esto tambi?n
 *				se creo la funci?n borrarHistoriaDiferenteUnix
 *************************************************************************************************
 * 2011-11-28 - Se agreg? la condici?n si mysql_affected_rows() antes de grabar en log_agenda
 *				para garantizar que si se ejecuto la acci?n que se graba en la tabla de log_agenda
 *************************************************************************************************
 * 2011-11-27 - Se agreg? grabaci?n en la tabla de log_agenda para todas las acciones que se
 *				ejecuten en el sistema, no solo para las de borrado como estaba
 *				En la funci?n borraIngresosMayores se cambi? Ubiing >= ".$ingreso." por
 *				Ubiing*1 >= ".$ingreso." para que hiciera la comparaci?n correctamente
 *************************************************************************************************
 * 2011-11-25 - Cuando se llama la funci?n borraIngresosMayores, se estaba llevando el ingreso de Matrix
 *				se cambi? para que lleve el ingreso de unix
 *************************************************************************************************
 * 2011-11-23 - En el Query de la funci?n obtenerCcoMatrix se adicion? la condici?n de que el
 *				centro de costo sea de ingreso (ccoing='on') para que no se ingresen pacientes a
 *				centros de costos que no son de ingreso
 *************************************************************************************************
 * 2011-11-11 - Se agreg? la columna de Afinidad del paciente tanto en la lista de pacientes activos
 *				como en la lista de pacientes inactivos
 *************************************************************************************************
 * 2011-10-31 - Se modificaron las funciones insertarIngresoPaciente y actualizarIngresoPaciente de modo que
 *				cuando la adici?n o edici?n en la tabla root_000037 saque error por clave duplicada,
 *				borre los registros duplicados e inserte o actualice los datos del paciente que se traen desde Unix
 *				Se creo la funci?n esCirugia para verificar si el centro de costo del paciente es cirugia
 *				de modo que En proceso de traslado quede en 'on', es decir, poner Ubiptr de la tabla movhos_000018 en 'on'
 *************************************************************************************************
 * 2011-10-27 - La consulta de pacientes de Unix se hizo general para que se consulte e ingresen todos los
 *				pacientes activos desde unix sin importar el servicio pues se decidi? que desde este script
 *				de urgencias se ingresen todos los pacientes activos de Unix e igual para la alta automatica
 *				de los pacientes que ya no esten en Unix y no tengan conducta asociada
 *************************************************************************************************
 * 2011-10-26 - Se adicion? la funci?n borraIngresosMayores y se modific? la funci?n ingresarPacientesUrgencias
 *				esto para preveer la situaci?n en la que una historia o ingreso es reasignado en Unix
 *				de modo que en matrix se borren los ingresos mayores a los de Unix e igual se actualicen
 *				los datos en las tablas root_000036 y root_000037 en caso de un cambio de c?dula para la historia
 *************************************************************************************************
 * 2011-08-25 - Cuando se escanea para ingreso de pacientes desde Unix se adicion? la funci?n
 *				actualizarDatosPacienteTablaUnica para que actualice los datos de la tabla root_000036
 *				con los que se traen de Unix
 *************************************************************************************************
 * 2011-08-17 - En la consulta de m?dicos se incluyeron las condiciones para meduma diferente de '' y 'NO APLICA'
 *************************************************************************************************
 * 2011-08-11 - Se agrego al LOG que guarde tambien el borrado de la tabla  movhos_000016
 *				Se modific? las consultas que agregan pacientes nuevos a la agenda para que traiga de
 *				Unix no solo los pacientes a partir de ayer sino todos los que esten en Unix
 *				asignados a urgencias, ver campos comentados asi: 	//  AND pacfec >= '".$ayer."'";
 *************************************************************************************************
 * 2011-07-06 - Se creo una tabla para LOG (log_agenda) para guardar las acciones de borrado de la tabla movhos_000018
 *************************************************************************************************
 * 2011-06-08 - Se cambio la asignaci?n por m?dico a asignaci?n por especialidad
 *************************************************************************************************
 * 2011-05-13 - Se cambio el query para consultar m?dico asignado en la funci?n agendaUrgencias
 *				ya que estaba tomando pacientes con m?dico en blanco "", y mostraba m?dico
 *				con c?digo en blanco ""
 *				Se cambi? la funci?n actualizarAltaPacientesUnix ya que no se estaba recorriendo
 *				el arreglo de forma correcta, se cambi? la funci?n while ($j < count ($altas_unix))
 *				por foreach ($altas_unix as $j => $value)
 *************************************************************************************************
 * 2011-04-28 - Cuando es alta autom?tica, se cambio la funci?n <altaPacienteUrgencias>
 *				para que verifique si en Unix sigue activa la historia, si es asi no le da alta
 *				Se activaron los checbox de actas y muertes para conductas de alta
 *				Se inactiva checbox de muerte si conducta es alta y viceversa
 *************************************************************************************************
 * 2011-04-25 - Modificaci?n en el ingreso de pacientes a urgencias, se quito el campo
 *				de texto donde se ingresaba la historia cl?nica, ya se toma de Unix los
 *				pacientes en urgencias actualiz?ndose la lista autom?ticamente.
 *************************************************************************************************
 * 2011-03-04 - Modificaci?n en el proceso de ingreso del paciente a urgencias para validar los siguientes casos:
 * 				Si un usuario ya est? en movhos 18 pero aun no esta en hce 22, registrarlo en hce 22
 * 				antes se asumia como ya ingresado y no se registraba en hce 22
 *				Validar ingreso de paciente no solo por historia sino tambien por n?mero de ingreso, por ejemplo:
 *				si un usuario est? registrado con ingreso 1 y vuelven y lo entran y tiene ya en UNIX ingreso 2
 *				se debe dar de alta automaticamente el ingreso 1 y adicionarlo a la agenda de urgencias con ingreso 2
 *				Validaci?n al activar un paciente dado de alta, si ya est? activo con otro ingreso no lo deja activar
 *				En reasignar si el paciente tiene mas de un ingreso no deja reasiganar su n?mero de historia
 *************************************************************************************************
 * 2011-02-23 - Adici?n de try catch al ejecutar blockUI debido a que en algunas versiones viejas de IE
 * 				sacaba un error y no dejaba ejecutar la p?gina. Tambi?n se adicion? el evento onload al final del javascript
 *************************************************************************************************
 * 2011-02-22 - Adici?n de columnas Activar y Reasignar historia para pacientes dados de alta
 *
 */



 // Retorna el c?digo y nombre del centro de costos de urgencias
 // Se debe reemplazar por consultarCentrocoUrgencias que est? en el comun.
 function consultarCcoUrgencias()
 {
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod, Cconom
		FROM
			".$wbasedato."_000011
		WHERE
			Ccourg = 'on'; ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = new centroCostosDTO();

	if($filas > 0)
	{
		$fila = mysql_fetch_row($res);

		$cco->codigo = $fila[0];
		$cco->nombre = $fila[1];
	}
	return $cco;
 }

include_once("root/comun.php");
include_once("movhos/movhos.inc.php");
/********************************************************************************************
****************************** INICIO APLICACI?N ********************************************
********************************************************************************************/
$wbasedato = "";
$wactualiz = "Diciembre 19 de 2021";

if (is_null($selectsede)){
    $selectsede = consultarsedeFiltro();
}

// Validaci?n de usuario
if (!isset($user))
{
	if (!isset($_SESSION['user']))
	{
		session_register("user");
	}
	$user="";
}

//Codigo de usuario que ingreso al sistema
if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
else
	$wuser="";

$usuario = new Usuario();

$usuario->codigo = $wuser;

//Variable para determinar la empresa
if(!isset($wemp_pmla))
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Valida codigo de usuario en sesion si no esta registrado el sistema termina la ejecucion
    if (!array_key_exists("user",$_SESSION))
    {
        terminarEjecucion("<div align='center'>Usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.</div>");
    }
    else
    {
        //Conexion base de datos Matrix
        $conex = obtenerConexionBD("matrix");
        $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
        $winstitucion = $institucion->nombre;
		$nombreCco = "";

        $wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
        $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

        //Consulto el codigo y nombre del centro de costo de urgencias
        $ccoUrgencias = consultarCentrocoUrgencias($wbasedato, $selectsede);

        //Formulario (forma)
        echo "<form name='forma' action='' method='post'>";

        echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
        echo "<input type='hidden' name='wbasedato' id='wbasedato' value='".$wbasedato."'>";
        echo "<input type='hidden' name='wbasedatohce' id='wbasedatohce' value='".$wbasedatohce."'>";
        echo "<input type='HIDDEN' id='sede' name='selectsede' value='".$selectsede."'>";

        echo "<input type='hidden' id='modoConsulta' value='".((isset($modoConsulta) && $modoConsulta == 'on') ? 'on' : 'off')."'>";


        // 2013-06-18
		// Si se ha enviado un servicio en el par?metro $wservicio, se toma este como centro de costo de script
		// y las consultas a pacientes se har?n filtrando por este centro de costos, no por urgencias como es por defecto
		if(isset($wcco) && $wcco!='')
		{
			echo "<input type='hidden' name='codCco' value='".$wcco."'>";

			if($ccoUrgencias->Codigo == $wcco)
			{
				$esUrgencias = 'on';
				$nombreCco = strtoupper($ccoUrgencias->nombre);
			}
			else
			{
				$esUrgencias = 'off';
				$centroCosto = consultarCentroCosto($conex, $wcco, $wbasedato);
				$nombreCco = strtoupper($centroCosto->nombre);
			}
		}
		else
		{
			echo "<input type='hidden' name='codCco' value='".$ccoUrgencias->codigo."'>";
			$esUrgencias = 'on';
			$nombreCco = strtoupper($ccoUrgencias->nombre);
		}

		$wnivel_triage      = (!isset($wnivel_triage)) 		? '':$wnivel_triage;
		$wespecialidad_auto = (!isset($wespecialidad_auto)) ? '':$wespecialidad_auto;
		$wespec_ing_sala    = (!isset($wespec_ing_sala)) 	? '':$wespec_ing_sala;
		$wing_sala_cond     = (!isset($wing_sala_cond)) 	? '':$wing_sala_cond;
		$wtriage_ing_sala   = (!isset($wtriage_ing_sala)) 	? '':$wtriage_ing_sala;
		$wing_sala_triage   = (!isset($wing_sala_triage)) 	? '':$wing_sala_triage;
		$wesp_asoc_ing_sala = (!isset($wesp_asoc_ing_sala)) ? '':$wesp_asoc_ing_sala;

        echo "<input type='hidden' name='esUrgencias' value='".$esUrgencias."'>";

        echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wuser."'>";
        //echo "<input type='hidden' name='conex' id='conex' value=".$conex.">";
        echo "<input type='hidden' name='wtriage_auto' id='wtriage_auto' value='".$wnivel_triage."'>";
        echo "<input type='hidden' name='wespecialidad_auto' id='wespecialidad_auto' value='".$wespecialidad_auto."'>";
        echo "<input type='hidden' name='wespec_ing_sala' id='wespec_ing_sala' value='".$wespec_ing_sala."'>";
        echo "<input type='hidden' name='wing_sala_cond' id='wing_sala_cond' value='".$wing_sala_cond."'>";
        echo "<input type='hidden' name='wing_sala_triage_desc' id='wing_sala_triage_desc' value='".$wtriage_ing_sala."'>";
        echo "<input type='hidden' name='wing_sala_triage' id='wing_sala_triage' value='".$wing_sala_triage."'>";
        echo "<input type='hidden' name='wesp_asoc_ing_sala' id='wesp_asoc_ing_sala' value='".$wesp_asoc_ing_sala."'>";
        echo "<input type='hidden' name='waccion'>";

        //Mensaje de espera
        echo "<div id='msjEspere' name='msjEspere' style='display:none;'>";
        echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br /> Por favor espere un momento ... <br /><br />";
        echo "</div>";

        // Definici?n del encabezado del aplicativo
        encabezado("ASIGNACION ESPECIALIDAD ".$nombreCco, $wactualiz, "clinica", true, false);

        //Botones "Actualizar" y "Cerrar ventana"
        echo "<br /><p align='center'><input type='button' value='Actualizar' onclick='javascript:agendaUrgencias();'> &nbsp; | &nbsp; <input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";

        // Aca la funci?n anterior pinta la lista de pacientes en Urgencias con sus opciones
        echo "<div name='agendUrg' id='agendUrg'>";
        echo "</div>";

        // Establece que la p?gina se refresca autom?ticamente cada 40 segundos
        echo "<script> timer = setInterval('agendaUrgencias()', 120000); </script>";

        echo "</form>";

        //Botones "Actualizar" y "Cerrar ventana"
        echo "<br /><p align='center'><input type='button' value='Actualizar' onclick='javascript:agendaUrgencias();'> &nbsp; | &nbsp; <input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";

        echo "</div><br />"; //Cierre div id=page

    }

}

if(isset($consultaAjax))
{
header("Content-Type: text/html;charset=ISO-8859-1");

include_once("root/comun.php");
include_once("root/magenta.php");
include_once("movhos/movhos.inc.php");

$conex = obtenerConexionBD("matrix");
// --> 2015-12-21: Jerson Trujillo, La conexion a unix se realizar? solo en las funciones que lo necesiten.
//conexionOdbc($conex, $basedatos, $conexUnix, 'facturacion');

class medicosUrgencias
{
	var $codigo;
	var $nombre;
}

class especialidadesUrgencias
{
	var $codigo;
	var $nombre;
}

// Retorna un arreglo con los m?dicos actualmente asiganados a urgencias
function consultarMedicosUrgencias($wbasedato)
{
	global $conex;

	$q1=  "	SELECT Meduma, Medno1, Medno2, Medap1, Medap2 "
		 ."   FROM ".$wbasedato."_000048 "
		 ."  WHERE Medurg = 'on' "
		 ."	   AND Medest = 'on' "
		 ."    AND Meduma != '' "
		 ."    AND Meduma != ' ' "
		 ."    AND Meduma != 'NO APLICA' "
		 ."  ORDER BY Medno1, Medno2, Medap1, Medap2";
	$res1 = mysql_query($q1,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
  	$num1 = mysql_num_rows($res1);

  	$coleccion = array();

  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$med = new medicosUrgencias();
  			$row1 = mysql_fetch_array($res1);

  			$med->codigo = $row1[0];
  			$med->nombre = $row1[1]." ".$row1[2]." ".$row1[3]." ".$row1[4];

  			$coleccion[] = $med;
  		}
  	}
  	return $coleccion;
}

// Retorna un arreglo con las especialidades actualmente asiganadas a urgencias
function consultarEspecialidades($wbasedato,$esUrgencias)
{
	global $conex;

	// 2013-06-20
	if($esUrgencias!='on')
	{
		$q1=  "	SELECT Espcod, Espnom, Esptau "
			 ."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 "
			 ."  WHERE Medest = 'on' "
			 ."    AND Meduma != '' "
			 ."    AND Meduma != ' ' "
			 ."    AND Meduma != 'NO APLICA' "
			 ."	   AND Medesp = Espcod "
			 ."	   AND Espcex = 'on' "
			 ."  GROUP BY Medesp "
			 ."  ORDER BY Espnom ASC ";
	}
	else
	{
		$q1=  "	SELECT Espcod, Espnom, Esptau "
			 ."   FROM ".$wbasedato."_000044 "
			 ."  WHERE Espaur = 'on'"
			 ."  ORDER BY Espnom ASC ";
	}

	$res1 = mysql_query($q1,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
  	$num1 = mysql_num_rows($res1);

  	$coleccion = array();

  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$med = new especialidadesUrgencias();
  			$row1 = mysql_fetch_array($res1);

  			$med->codigo = $row1[0];
  			$med->nombre = $row1[1];
			$med->triage_automatico = $row1[2];

  			$coleccion[] = $med;
  		}
  	}
  	return $coleccion;
}

/********************************************************************************************
* VERIFICA SI LA HISTORIA DEL PACIENTE SE ENCUENTRA REGISTRADA EN URGENCIAS DE DB UNIX		*
********************************************************************************************/
function consultarPacienteUnix($pacienteConsulta)
{
	global $conexUnix;
	$paciente = new pacienteDTO();

	$q = " SELECT pacnom, pacap1, pacap2, pacnum, pacfec, pachor, pachab, paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, pactel, pacdir, pacmun
		     FROM inpac, insercco
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."'
			  AND serccoser = pacser
			  AND pacap2 is not null
			  AND pachab is not null

			UNION

			SELECT pacnom, pacap1, ' ' AS pacap2, pacnum, pacfec, pachor, pachab, paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, pactel, pacdir, pacmun
		     FROM inpac, insercco
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."'
			  AND serccoser = pacser
			  AND pacap2 is null
			  AND pachab is not null

			UNION

		   SELECT pacnom, pacap1, pacap2, pacnum, pacfec, pachor, ' ', paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, pactel, pacdir, pacmun
		     FROM inpac, insercco
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."'
			  AND serccoser = pacser
			  AND pacap2 is not null
			  AND pachab is null

			UNION

			SELECT pacnom, pacap1, ' ' AS pacap2, pacnum, pacfec, pachor, ' ', paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, pactel, pacdir, pacmun
		     FROM inpac, insercco
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."'
			  AND serccoser = pacser
			  AND pacap2 is null
			  AND pachab is null
			  ";
	//		  AND pacser = '04'";			// 2011-10-27

	$rs = odbc_do($conexUnix,$q) or die (odbc_errormsg());
	//odbc_fetch_row($rs);

	if ($arr_paciente = odbc_fetch_array($rs))
	{

		$municipio = "";
		$tipo_responsable = "";

		//Busco el nombre del municipio
		$muncod = trim($arr_paciente['pacmun']);
		$qmun = "  SELECT munnom
					 FROM inmun
					WHERE muncod = '".trim($muncod)."'
				  ";
		$rsmun = odbc_do($conexUnix,$qmun) or die (odbc_errormsg());
		$arr_municipio = odbc_fetch_array($rsmun);
		$municipio = $arr_municipio['munnom'];

		// $numreg = odbc_num_rows($rsmun);
		// if($numemp>0)
			// $municipio = trim(odbc_result($rsmun,1));
		// else
			// $municipio = "";


		// Busco el tipo de responsable
		$codResponsable = trim($arr_paciente['paccer']);
		$qemp = "  SELECT emptip
					 FROM inemp
					WHERE empcod = '".$codResponsable."'
					  AND emptip is not null
				  ";
		$rsemp = odbc_do($conexUnix,$qemp) or die (odbc_errormsg());
		$arr_responsable = odbc_fetch_array($rsemp);
		$tipo_responsable = $arr_responsable['emptip'];

		// $numreg = odbc_num_rows($rsemp);
		// if($numreg>0)
			// $tipo_responsable = trim(odbc_result($rsemp,1));
		// else
			// $tipo_responsable = "";


		$nombre = explode(" ",trim($arr_paciente['pacnom']));
		$paciente->nombre1 = $nombre[0];

		if(isset($nombre[1]) && !isset($nombre[2]))
		{
			$paciente->nombre2 = $nombre[1];
		}
		elseif(isset($nombre[1]) && isset($nombre[2]))
		{
			$paciente->nombre2 = $nombre[1]." ".$nombre[2];
		}
		elseif(!isset($nombre[1]) && isset($nombre[2]))
		{
			$paciente->nombre2 = $nombre[2];
		}
		else
		{
			$paciente->nombre2 = "";
		}

		$paciente->apellido1 = trim($arr_paciente['pacap1']);
		$paciente->apellido2 = trim($arr_paciente['pacap2']);
		$paciente->historiaClinica = trim($pacienteConsulta->historiaClinica);
		$paciente->ingresoHistoriaClinica = trim($arr_paciente['pacnum']);
		$paciente->fechaIngreso = str_replace("/","-",trim($arr_paciente['pacfec']));
		$paciente->horaIngreso = str_replace(".",":",trim($arr_paciente['pachor'])).":00";
		$paciente->habitacionActual = "";
		$paciente->numeroIdentificacionResponsable = $codResponsable;
		$paciente->nombreResponsable = trim($arr_paciente['pacres']);
		$paciente->tipoDocumentoIdentidad = trim($arr_paciente['pactid']);
		$paciente->documentoIdentidad = trim($arr_paciente['pacced']);
		$paciente->fechaNacimiento = trim($arr_paciente['pacnac']);
		$paciente->genero = trim($arr_paciente['pacsex']);
		$paciente->deHospitalizacion = trim($arr_paciente['pachos']);
		$paciente->servicioActual = trim($arr_paciente['serccocco']);
		$paciente->tipoResponsable = $tipo_responsable;
		$paciente->telefono = trim($arr_paciente['pactel']);
		$paciente->direccion = trim($arr_paciente['pacdir']);
		$paciente->municipio = $municipio;

		if(!isset($paciente->tipoResponsable))
		{
			$paciente->tipoResponsable = "02";
		}
		else
		{
			if($paciente->tipoResponsable == '' || empty($paciente->tipoResponsable))
			{
				$paciente->tipoResponsable = "02";
			}
		}
	}

	return $paciente;
}

// 2013-02-08
// Consulta los datos de una fila seg?n el query $qlog y convierte esta fila en un String
// separando cada campo por el caracter |
function obtenerRegistrosFila($qlog)
{
	global $conex;

	$reslog = mysql_query($qlog, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qlog . " - " . mysql_error());
	$rowlog = mysql_fetch_row($reslog);
	$datosFila = implode("|", $rowlog);
	return $datosFila;
}

/********************************************************************************************
* FUNCIONES UTILIZADAS EN EL REGISTRO DEL PACIENTE QUE ENTRA A URGENCIAS					*
********************************************************************************************/

//Existe un registro del paciente en la tabla 36 de root
function existeEnTablaUnicaPacientes($paciente)
{
	global $conex;

	$esta = false;

	$q = " SELECT *
		  	 FROM root_000036
		    WHERE Pacced = '".$paciente->documentoIdentidad."'
			  AND Pactid = '".$paciente->tipoDocumentoIdentidad."'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0)
	{
		$esta = true;
	}
	return $esta;
}

//Ingresa los datos en la tabla 36 de root
function insertarPacienteTablaUnica($paciente,$seguridad)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	$q = "INSERT INTO
			root_000036
				(medico,fecha_data,hora_data,Pacced,Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Pactid,Seguridad)
			VALUES
				('root','$paciente->fechaIngreso','$paciente->horaIngreso', '$paciente->documentoIdentidad', '$paciente->nombre1', '".$paciente->nombre2."', '".$paciente->apellido1."', '".$paciente->apellido2."', '".$paciente->fechaNacimiento."', '".$paciente->genero."', '$paciente->tipoDocumentoIdentidad', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de grabaci?n en tabla root_000036
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$paciente->documentoIdentidad."', '".$paciente->tipoDocumentoIdentidad."', 'Grabacion tabla root_000036', '".$seguridad."', 'Auto')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Actualiza documento del paciente en la tabla 36 de root
function actualizarDocumentoPacienteTablaUnica($pacienteAnterior, $pacienteNuevo)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM root_000036
			   WHERE Pacced = '".$pacienteAnterior->documentoIdentidad."'
				 AND Pactid = '".$pacienteAnterior->tipoDocumentoIdentidad."' ";
	$registrosFila = obtenerRegistrosFila($qlog);

	$q = "UPDATE
			root_000036
		SET
			Pacced = '".$pacienteNuevo->documentoIdentidad."',
			Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE
			Pacced = '".$pacienteAnterior->documentoIdentidad."'
			AND Pactid = '".$pacienteAnterior->tipoDocumentoIdentidad."' ";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci?n en tabla root_000036
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->documentoIdentidad."', '".$pacienteAnterior->tipoDocumentoIdentidad."', 'Actualizacion tabla root_000036', 'root', 'Nuevo documento ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Actualiza datos del paciente en la tabla 36 de root
function actualizarDatosPacienteTablaUnica($pacienteAnterior, $pacienteNuevo)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM root_000036
			   WHERE Pacced = '".$pacienteNuevo->documentoIdentidad."'
				 AND Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'";
	$registrosFila = obtenerRegistrosFila($qlog);

	$q = "UPDATE
			root_000036
		SET
			Pacno1 = '".$pacienteNuevo->nombre1."',
			Pacno2 = '".$pacienteNuevo->nombre2."',
			Pacap1 = '".$pacienteNuevo->apellido1."',
			Pacap2 = '".$pacienteNuevo->apellido2."',
			Pacnac = '".$pacienteNuevo->fechaNacimiento."',
			Pacsex = '".$pacienteNuevo->genero."'
		WHERE
			Pacced = '".$pacienteNuevo->documentoIdentidad."'
			AND Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci?n en tabla root_000036
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->documentoIdentidad."', '".$pacienteNuevo->tipoDocumentoIdentidad."', 'Actualizacion tabla root_000036', 'root', 'Nuevos datos ".$pacienteNuevo->nombre1." ".$pacienteNuevo->nombre2." ".$pacienteNuevo->apellido1." ".$pacienteNuevo->apellido2." | ".$pacienteNuevo->fechaNacimiento." | ".$pacienteNuevo->genero." ', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Existe un registro del paciente en la tabla 37 de root
function existeEnTablaIngresos($paciente,$origen)
{
	global $conex;

	$esta = false;

	$q = "SELECT
				*
		  	FROM
		  		root_000037
			WHERE
				Orihis = '".$paciente->historiaClinica."'
				AND Oriori = '".$origen."'";


	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0)
	{
		$esta = true;
	}
	return $esta;
}

// En tabla root_000037 borra la historia asociada en matrix a la c?dula de unix
// siempre y cuando esta historia sea diferente a la asociada en Unix
function borrarHistoriaDiferenteUnix($paciente, $wemp_pmla, $seguridad)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	$q = "SELECT *
		  	FROM root_000037
		   WHERE Oriced = '".$paciente->documentoIdentidad."'
		     AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
			 AND Orihis != '".$paciente->historiaClinica."'
			 AND Oriori = '".$wemp_pmla."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0)
	{

		$registrosFila = obtenerRegistrosFila($q);

		$q = " DELETE FROM root_000037
				WHERE Oriced = '".$paciente->documentoIdentidad."'
				  AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
				  AND Orihis != '".$paciente->historiaClinica."'
				  AND Oriori = '".$wemp_pmla."'";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Borrado tabla root_000037', '".$seguridad."', 'Historia diferente unix ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
}

//Ingresa los datos en la tabla 37 de root
function insertarIngresoPaciente($paciente, $wemp_pmla, $seguridad)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	$q = "INSERT INTO root_000037
			( medico,fecha_data,hora_data,Oriced,Orihis,Oriing,Oriori,Oritid,Seguridad)
		VALUES
			('root','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->documentoIdentidad."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$wemp_pmla."', '".$paciente->tipoDocumentoIdentidad."', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de grabaci?n en tabla root_000037
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Grabacion tabla root_000037', '".$seguridad."', 'Auto ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	// Si ocurri? error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Oriced = '".$paciente->documentoIdentidad."'
				  AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
				  AND Oriori = '".$wemp_pmla."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = " DELETE FROM root_000037
				WHERE Oriced = '".$paciente->documentoIdentidad."'
				  AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
				  AND Oriori = '".$wemp_pmla."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Borrado tabla root_000037', '".$seguridad."', 'Clave duplicada ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		$q = "INSERT INTO root_000037
				( medico,fecha_data,hora_data,Oriced,Orihis,Oriing,Oriori,Oritid,Seguridad)
			VALUES
				('root','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->documentoIdentidad."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$wemp_pmla."', '".$paciente->tipoDocumentoIdentidad."', 'C-".$seguridad."' )";
		$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de grabaci?n en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Grabacion tabla root_000037', '".$seguridad."', 'Clave duplicada ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
}

//Actualiza los datos en la tabla 37 de root
function actualizarIngresoPaciente($pacienteAnterior, $pacienteNuevo, $origen)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM root_000037
			   WHERE Orihis = '".$pacienteNuevo->historiaClinica."'
				 AND Oriori = '".$origen."';";
	$registrosFila = obtenerRegistrosFila($qlog);

	$q = "UPDATE
			root_000037
		SET
			Oriing = '".$pacienteNuevo->ingresoHistoriaClinica."',
			Oriced = '".$pacienteNuevo->documentoIdentidad."',
			Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE
			Orihis = '".$pacienteNuevo->historiaClinica."'
			AND Oriori = '".$origen."';";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci?n en tabla root_000037
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Actualiza ingreso ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	// Si ocurri? error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Oriced = '".$pacienteNuevo->documentoIdentidad."'
					 AND Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
					 AND Oriori = '".$origen."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "DELETE FROM root_000037
					WHERE Oriced = '".$pacienteNuevo->documentoIdentidad."'
					  AND Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
					  AND Oriori = '".$origen."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Borrado tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Orihis = '".$pacienteNuevo->historiaClinica."'
					 AND Oriori = '".$origen."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "UPDATE
				root_000037
			SET
				Oriing = '".$pacienteNuevo->ingresoHistoriaClinica."',
				Oriced = '".$pacienteNuevo->documentoIdentidad."',
				Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
			WHERE
				Orihis = '".$pacienteNuevo->historiaClinica."'
				AND Oriori = '".$origen."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de actualizacion en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

	}
}

//Actualiza el documento del paciente en la tabla 37 de root
function actualizarDocumentoPacienteTablaIngresos($pacienteAnterior, $pacienteNuevo,$wemp_pmla)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM root_000037
			   WHERE Orihis = '".$pacienteAnterior->historiaClinica."'
				 AND Oriori = '".$wemp_pmla."' ";
	$registrosFila = obtenerRegistrosFila($qlog);

	$q = "UPDATE
			root_000037
		SET
			Oriced = '".$pacienteNuevo->documentoIdentidad."',
			Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE
			Orihis = '".$pacienteAnterior->historiaClinica."'
			AND Oriori = '".$wemp_pmla."' ";
	//		AND Oriing = '".$pacienteAnterior->ingresoHistoriaClinica."'
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci?n en tabla root_000037
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Actualiza documento paciente ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	// Si ocurri? error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Oriced = '".$pacienteNuevo->documentoIdentidad."'
					 AND Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
					 AND Oriori = '".$wemp_pmla."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "DELETE FROM root_000037
					WHERE Oriced = '".$pacienteNuevo->documentoIdentidad."'
					  AND Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
					  AND Oriori = '".$wemp_pmla."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Borrado tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Orihis = '".$pacienteAnterior->historiaClinica."'
					 AND Oriori = '".$wemp_pmla."' ";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "UPDATE
				root_000037
			SET
				Oriced = '".$pacienteNuevo->documentoIdentidad."',
				Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
			WHERE
				Orihis = '".$pacienteAnterior->historiaClinica."'
				AND Oriori = '".$wemp_pmla."' ";
		//		AND Oriing = '".$pacienteAnterior->ingresoHistoriaClinica."'
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de grabacion en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Grabacion tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
}

//Existe un registro del paciente en la tabla 16 de movhos
function existeEnTablaResponsables($pacienteMatrix, $wemp_pmla)
{
	global $conex;
	global $wbasedato;

	$esta = false;

	$q = "SELECT
				*
		  	FROM
		  		".$wbasedato."_000016
			WHERE
				Inghis = '".$pacienteMatrix->historiaClinica."'
				AND Inging = '".$pacienteMatrix->ingresoHistoriaClinica."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0)
	{
		$esta = true;
	}
	return $esta;
}

//Ingresa los datos en la tabla 22 de hce
function registrarIngresoPaciente($ingreso,$seguridad)
{
	global $conex;
	global $wbasedatohce;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	$q = "	SELECT Mtrhis
			FROM ".$wbasedatohce."_000022
			WHERE Mtrhis = '".$ingreso->historiaClinica."'
			AND Mtring = '".$ingreso->ingresoHistoriaClinica."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num==0)
	{
		$q = "INSERT INTO
				".$wbasedatohce."_000022
					(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrcci,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcur,Seguridad)
				VALUES
					('HCE','".$fecha."','".$hora."','".$ingreso->historiaClinica."','".$ingreso->ingresoHistoriaClinica."','".$ingreso->servicioActual."','','on','off','','off','C-".$seguridad."')";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de garabaci?n en tabla hce_000022
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Auto')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Si ocurri? error por clave duplicada
		$error_sql = mysql_errno();
		if(isset($error_sql) && $error_sql=="1062")
		{
			// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			$qlog = " SELECT *
						FROM ".$wbasedatohce."_000022
					   WHERE Mtrhis = '".$ingreso->historiaClinica."'
						 AND Mtring = '".$ingreso->ingresoHistoriaClinica."'; ";
			$registrosFila = obtenerRegistrosFila($qlog);

			$q = "DELETE FROM ".$wbasedatohce."_000022
						WHERE Mtrhis = '".$ingreso->historiaClinica."'
						  AND Mtring = '".$ingreso->ingresoHistoriaClinica."';";
			$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla root_000037 por clave duplicada
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Borrado tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Clave duplicada ".$ingreso->historiaClinica."-".$ingreso->ingresoHistoriaClinica."', '".$registrosFila."')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			$q = "INSERT INTO
					".$wbasedatohce."_000022
						(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrcci,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcur,Seguridad)
					VALUES
						('HCE','".$fecha."','".$hora."','".$ingreso->historiaClinica."','".$ingreso->ingresoHistoriaClinica."','".$ingreso->servicioActual."','','on','off','','off','C-".$seguridad."')";

			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de garabaci?n en tabla hce_000022
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
								   VALUES
										  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Auto')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}

	}
	else
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM ".$wbasedatohce."_000022
				   WHERE Mtrhis = '".$ingreso->historiaClinica."'
					 AND Mtring = '".$ingreso->ingresoHistoriaClinica."'";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "	UPDATE ".$wbasedatohce."_000022
				SET Fecha_data='".$fecha."', Hora_data='".$hora."', Mtrmed='', Mtrest='on', Mtrtra='off', Mtretr='', Mtrcur='off', Mtrcci='".$ingreso->servicioActual."', Seguridad='C-".$seguridad."'
				WHERE Mtrhis = '".$ingreso->historiaClinica."'
				AND	Mtring = '".$ingreso->ingresoHistoriaClinica."'";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de actualizaci?n en tabla hce_000022
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Auto', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
}

//Ingresa los datos en la tabla 16 de movhos
function insertarResponsablePaciente($paciente, $wemp_pmla, $seguridad)
{
	global $conex;
	global $wbasedato;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	$q = "INSERT INTO ".$wbasedato."_000016
			(medico,Fecha_data,Hora_data,Inghis,Inging,Ingres,Ingnre,Ingtip,Ingtel,Ingdir,Ingmun,Seguridad)
		VALUES
			('movhos','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$paciente->numeroIdentificacionResponsable."', '".$paciente->nombreResponsable."', '".$paciente->tipoResponsable."', '".$paciente->telefono."', '".$paciente->direccion."', '".$paciente->municipio."', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de garabaci?n en tabla movhos_000016
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
						   VALUES
								  ('".$fecha."', '".$hora."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Grabacion tabla ".$wbasedato."_000016', '".$seguridad."', 'Auto')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Actualiza los datos en la tabla 16 de movhos
function actualizarResponsablePaciente($pacienteAnterior, $pacienteNuevo)
{
	global $conex;
	global $wbasedato;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM ".$wbasedato."_000016
			   WHERE Inghis = '".$pacienteAnterior->historiaClinica."'
				 AND Inging = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
	$registrosFila = obtenerRegistrosFila($qlog);

	$q = "UPDATE
			".$wbasedato."_000016
		SET
			Ingres = '".$pacienteNuevo->numeroIdentificacionResponsable."',
			Ingnre = '".$pacienteNuevo->nombreResponsable."',
			Ingtip = '".$pacienteNuevo->tipoResponsable."',
			Ingtel = '".$pacienteNuevo->telefono."',
			Ingdir = '".$pacienteNuevo->direccion."',
			Ingmun = '".$pacienteNuevo->municipio."'
		WHERE
			Inghis = '".$pacienteAnterior->historiaClinica."'
			AND Inging = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci?n en tabla movhos_000016
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fecha."', '".$hora."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wbasedato."_000016', 'root', 'Auto ".$pacienteNuevo->numeroIdentificacionResponsable." ".$pacienteNuevo->nombreResponsable."', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Ingresa los datos en la tabla 18 de movhos
function grabarIngresoPaciente($ingreso,$seguridad)
{
	global $conex;
	global $wbasedato;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	$q = "INSERT INTO
			".$wbasedato."_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,Ubiptr,Seguridad)
		VALUES
			('movhos','".$ingreso->fechaIngreso."','".$ingreso->horaIngreso."','".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', '".$ingreso->servicioActual."', '".$ingreso->servicioAnterior."', '".$ingreso->habitacionActual."',  '".$ingreso->habitacionAnterior."','".$ingreso->altaEnProceso."', '".$ingreso->altaDefinitiva."', '".$ingreso->fechaAltaProceso."','".$ingreso->horaAltaProceso."', '".$ingreso->fechaAltaDefinitiva."', '".$ingreso->horaAltaDefinitiva."', '".$ingreso->enProcesoTraslado."', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de grabaci?n en tabla movhos_000018
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
						   VALUES
								  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla ".$wbasedato."_000018', '".$seguridad."', 'Auto')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	// Si ocurri? error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM ".$wbasedato."_000018
				   WHERE Ubihis = '".$ingreso->historiaClinica."'
					 AND Ubiing = '".$ingreso->ingresoHistoriaClinica."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "DELETE FROM ".$wbasedato."_000018
					WHERE Ubihis = '".$ingreso->historiaClinica."'
					  AND Ubiing = '".$ingreso->ingresoHistoriaClinica."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Borrado tabla ".$wbasedato."_000018', '".$seguridad."', 'Clave duplicada ".$ingreso->historiaClinica."-".$ingreso->ingresoHistoriaClinica."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		$q = "INSERT INTO
				".$wbasedato."_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,Ubiptr,Seguridad)
			VALUES
				('movhos','".$ingreso->fechaIngreso."','".$ingreso->horaIngreso."','".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', '".$ingreso->servicioActual."', '".$ingreso->servicioAnterior."', '".$ingreso->habitacionActual."',  '".$ingreso->habitacionAnterior."','".$ingreso->altaEnProceso."', '".$ingreso->altaDefinitiva."', '".$ingreso->fechaAltaProceso."','".$ingreso->horaAltaProceso."', '".$ingreso->fechaAltaDefinitiva."', '".$ingreso->horaAltaDefinitiva."', '".$ingreso->enProcesoTraslado."', 'C-".$seguridad."' )";
		$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de grabaci?n en tabla movhos_000018
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla ".$wbasedato."_000018', '".$seguridad."', 'Auto')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

	}
}

// Verifica si el paciente ya ha sido ingresao
function pacienteIngresado($paciente)
{
	global $conex;
	global $wbasedato;
	$es = false;

	$q = "SELECT
				*
		 	FROM
		 		".$wbasedato."_000018
			WHERE
				Ubihis = '".$paciente->historiaClinica."'
				AND Ubiing   = '".$paciente->ingresoHistoriaClinica."'
			";

	$err = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}

// Verifica si el paciente ya ha sido ingresado a la tabla 22 de HCE
function pacienteIngresadoHce($paciente)
{
	global $conex;
	global $wbasedatohce;

	$es = false;

	$q = "SELECT
				*
		 	FROM
		 		".$wbasedatohce."_000022
			WHERE
				Mtrhis = '".$paciente->historiaClinica."'
				AND Mtring   = '".$paciente->ingresoHistoriaClinica."'
			";

	$err = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}

// Verifica si el centro de costos del paciente es cirugia
// Para determinar si movhos_000018.Ubiptr = on (En proceso de traslado)
// Se debe reemplazar por consultarCentrocoUrgencias que est? en el comun.
function esCirugia($cco)
{
	global $conex;
	global $wbasedato;

	$es = false;

	$q = "SELECT
				Ccocod
		 	FROM
		 		".$wbasedato."_000011
			WHERE
				Ccocod = '".$cco."'
				AND Ccocir   = 'on'
			";

	$err = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}

//Nivel de triage (nombre)
function niveltriage($wtri, $wbasedatohce)
{

global $conex;


$q =       " SELECT Trinom"
		 . "   FROM ".$wbasedatohce."_000040 "
		 . "  WHERE Tricod = '".$wtri."' "
		 ."     AND triest = 'on'";
$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$row = mysql_fetch_array($res);

return $row[0];

}

function configuracion_especialidad($wespecialidad, $basedatoshce, $basedatos)
{
	global $conex;

	$q = "	SELECT Espcfg
			  FROM ".$basedatos."_000044
			 WHERE Espcod = '".$wespecialidad."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	return $row['Espcfg'];

}
/********************************************************************************************/


/*********************************************************************************************
 **************************	FUNCIONES DE LLAMADO AJAX *************************************
 ********************************************************************************************/

// Asigna el m?dico tratante a un paciente en urgencias
function asignarEspecialidadUrgencias($basedatos,$basedatoshce,$especialidad,$paciente,$ingreso,$servicio,$seguridad, $wemp_pmla, $wespec_triage_auto)
{
	global $conex;

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'ok'=>'', 'descrip_triage'=>'');
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	$wtriage_auto = '';
	$wmtrcur = 'off';
	$wmtrfco = '0000-00-00';
	$wmtrhco = '00:00:00';
	$wmtrsala = "";

	//Valido si la especialidad seleccionada es de triage automatico, si es asi entregara los datos necesarios a cada variable segun lo que venga del
	//campo Espcfg con respecto al codigo de la especialidad //Jonatan 16 Octubre 2013
	 if($wespec_triage_auto == 'on')
		{
		$wdatos_especialidad = configuracion_especialidad($especialidad, $basedatoshce, $basedatos); //Trae la configuracion de la especialidad del campo Espcfg de la tabla ".$wbasedato."_000044.
		$wdatos_especialidad_array = explode("-",$wdatos_especialidad);
		$wtriage_auto =  $wdatos_especialidad_array[1]; // Nivel de triage automatico
		$wmtrfco = $fecha; //Fecha actual por defecto, fecha de consulta.
		$wmtrhco = $hora; //Hora actual por defecto, hora de consulta.
        $wfinitriage = $fecha; //Fecha actual por defecto, fecha de triage.
        $whorainitriage = $hora; //Hora actual por defecto, hora de triage.
		$wconducta = $wdatos_especialidad_array[0]; // conducta asociada a la especialidad.
		$especialidad = $wdatos_especialidad_array[2]; // Especialidad asociada.
		$wmtrcur = $wdatos_especialidad_array[3]; //Inicio de la consulta, esto determina si el paciente lo toma un medico o no.
		$wmtrsala = $wdatos_especialidad_array[4]; //Sala donde se ubicar? de forma automatica al paciente.
        $wmftco = $fecha; // Fecha de terminacion de la consulta.
        $wmhtco  = $hora; // Hora de terminacion de la consulta.
		$descrip_nivel = niveltriage($wtriage_auto, $basedatoshce); //Descripcion del nivel de traige.
	    $datamensaje['descrip_triage'] = $descrip_nivel; //Se envia este dato para que lo muestre en la interfaz, al seleccionar la especialidad.
		}
	else
		{
		$wmtrcur = "off"; //Sale automaticamente de la consulta.
		$wmtrfco = '0000-00-00'; // Fecha de inicio de la consulta.
		$wmtrhco = '00:00:00'; //Hora de inicio de la consulta.
		$wfinitriage = '0000-00-00'; // Fecha de triage
		$whorainitriage = '00:00:00'; // Hora de triage
		$wmftco = '0000-00-00'; // Fecha de terminacion de la consulta.
		$wmhtco  = '00:00:00'; // Hora de terminacion de la consulta.
		}

	//Verifica si el paciente se encuentra en la sala de espera.
	$q = "	SELECT Mtrhis
			FROM ".$basedatoshce."_000022
			WHERE Mtrhis = '".$paciente."'
			AND Mtring = '".$ingreso."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);


	if($num==0)
	{
		$q = "INSERT INTO
				".$basedatoshce."_000022
					(Medico,Mtrfam,Mtrham,Mtrhis,Mtring,Mtreme,Mtrest,Mtrtra,Mtretr,Mtrcci,Mtrcur,Mtrtri,Seguridad)
				VALUES
					('".$basedatoshce."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$especialidad."','on','on','".$especialidad."','".$servicio."','off','".$wtriage_auto."','C-".$seguridad."')";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		if($res)
		{
			$datamensaje['ok'] = "ok";
		}
		else
			{
			$datamensaje['error'] = 1;
			$datamensaje['mensaje'] = "No se pudo realizar la asignaci?n. \n Error: ".$res;
			}
	}
	else
	{
		$q = "	UPDATE ".$basedatoshce."_000022
				SET Mtrfam='".$fecha."', Mtrham='".$hora."', Mtreme='".$especialidad."', Mtrest='on', Mtrtra='on', Mtretr='".$especialidad."', Mtrcur='".$wmtrcur."', Mtrtri = '".$wtriage_auto."', Mtrfco = '".$wmtrfco."', Mtrhco = '".$wmtrhco."', Mtrftr = '".$wfinitriage."', Mtrhtr = '".$whorainitriage."', Mtrcon = '".$wconducta."', Mtrftc = '".$wmftco."', Mtrhtc = '".$wmhtco."', Mtrsal = '".$wmtrsala."'
				WHERE Mtrhis = '".$paciente."'
				AND	Mtring = '".$ingreso."'";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		if($res)
		{
			$datamensaje['ok'] = "ok";
		}
		else
			{
			$datamensaje['error'] = 1;
			$datamensaje['mensaje'] = "No se pudo realizar la asignaci?n. \n Error: ".$res;
			}
	}

	 echo json_encode($datamensaje);
     return;

}

// Borra los ingresos de una historia mayores al ingreso actual de Unix
function borraIngresosMayores($basedatos,$basedatoshce,$paciente,$ingreso,$wemp_pmla,$seguridad,$bandera,$fechaIngresoUnix,$horaIngresoUnix)
{
	global $conex;
	global $conexUnix;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	//Consulto los ingresos mayores al ingreso actual en unix
	$qmay = "	SELECT Fecha_data, Hora_data, Ubiing, Ubisac
				FROM ".$basedatos."_000018
				WHERE Ubihis = '".$paciente."'
				AND   Ubiing*1 >= ".$ingreso." ";
	$resmay = mysql_query($qmay, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmay . " - " . mysql_error());
	$nummay = mysql_num_rows($resmay);

	$ingreso_unix = $ingreso;

	// Si encontr? ingresos comience el borrado
	if($nummay>0)
	{
	  while($rowmay = mysql_fetch_array($resmay))
	  {
		$ingreso = $rowmay['Ubiing'];
		$fechaIngreso = $rowmay['Fecha_data'];
		$horaIngreso = $rowmay['Hora_data'];
		$ccoActual = $rowmay['Ubisac'];

		// 2013-02-18
		// Cosulto si el paciente tiene registros de movimiento hospitalario
		$qeyr = " SELECT Eyrhis
					FROM ".$basedatos."_000017
				   WHERE Eyrhis = '".$paciente."'
					 AND Eyring = '".$ingreso."'
					 AND Eyrest = 'on'";
		$reseyr = mysql_query($qeyr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qeyr . " - " . mysql_error());
		$numeyr = mysql_num_rows($reseyr);

		// Si el paciente no ha tenido movimiento hospitalario si se puede hacer el borrado
		if($numeyr==0)
		{
			$numant=0;
			if($ingreso==$ingreso_unix)
			{
				$horaUnix = date( "Y-m-d H:i:s", strtotime( $fechaIngresoUnix." ".$horaIngresoUnix ) - 10*60 );
				$fecha = explode(" ",$horaUnix);
				$fechaIngresoUnix = $fecha[0];
				$horaIngresoUnix = $fecha[1];

				// Valida si Fecha y hora registrados en Matrix son anteriores que los registrados en Unix
				// Si es menor quiere decir que el ingreso fue reasignado en Unix pero no se borr? de Matrix
				// Entonces se debe borrar

				//Consulto si tiene fecha hora de ingreso anterior a la de unix
				$qant = "	SELECT Fecha_data
							FROM ".$basedatos."_000018
							WHERE Ubihis = '".$paciente."'
							AND   Ubiing = '".$ingreso."'
							AND   (
									(  Fecha_data < '".$fechaIngresoUnix."')
									OR
									(  Fecha_data = '".$fechaIngresoUnix."'
									   AND Hora_data < '".$horaIngresoUnix."'
									)
								   ) ";
				$resant = mysql_query($qant, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qant . " - " . mysql_error());
				$numant = mysql_num_rows($resant);
			}

			if($ingreso!=$ingreso_unix || $numant>0)
			{
				$fechaLog = date('Y-m-d');
				$horaLog = date('H:i:s');

				// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
				$qlog = " SELECT *
							FROM ".$basedatos."_000016
						   WHERE Inghis = '".$paciente."'
							 AND Inging = '".$ingreso."'";
				$registrosFila = obtenerRegistrosFila($qlog);

				//Borro registro en tabla 16 de Movhos
				$q = "	DELETE
						  FROM ".$basedatos."_000016
						 WHERE Inghis = '".$paciente."'
						   AND Inging = '".$ingreso."'";
				$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de borrado en tabla Movhos 16
					$q = "	INSERT INTO log_agenda
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
									   VALUES
											  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000016', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
					$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}

				// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
				$qlog = " SELECT *
							FROM ".$basedatos."_000018
						   WHERE Ubihis = '".$paciente."'
							 AND Ubiing = '".$ingreso."'";
				$registrosFila = obtenerRegistrosFila($qlog);

				//Borro registro en tabla 18 de Movhos
				$q = "	DELETE
						  FROM ".$basedatos."_000018
						 WHERE Ubihis = '".$paciente."'
						   AND Ubiing = '".$ingreso."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de borrado en tabla Movhos 18
					$q = "	INSERT INTO log_agenda
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
									   VALUES
											  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000018', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
					$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}

				// Cosulto si el paciente ya est? registrado en la tabla 22 de Hce
				$q = "	SELECT *
						FROM ".$basedatoshce."_000022
						WHERE Mtrhis = '".$paciente."'
						AND Mtring = '".$ingreso."'";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$num = mysql_num_rows($res);

				if($num>0)
				{
					$registrosFila = obtenerRegistrosFila($q);

					$q = "	DELETE
							  FROM ".$basedatoshce."_000022
							 WHERE Mtrhis = '".$paciente."'
							   AND Mtring = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de borrado en tabla hce 22
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedatohce."_000022', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
						$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}
				}

				// 2013-02-26
				// Con esta funci?n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
				BorrarAltasMuertesAntesDeAgregarNueva($conex, $basedatos, $paciente, $ingreso, $bandera);

				// Como ya se borro el egreso se comentan las siguientes l?neas

				// Cosulto si el paciente ya est? registrado en la tabla 33 de Movhos
				// $q2 = "	SELECT *
						// FROM ".$basedatos."_000033
						// WHERE Historia_clinica = '".$paciente."'
						// AND Num_ingreso = '".$ingreso."'
						// AND Servicio = '".$ccoActual."' ";
				// $res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				// $num2 = mysql_num_rows($res2);

				// if($num2>0)
				// {
					// $registrosFila = obtenerRegistrosFila($q2);

					// $q = "	DELETE
							// FROM ".$basedatos."_000033
							// WHERE Historia_clinica = '".$paciente."'
							// AND Num_ingreso = '".$ingreso."'
							// AND Servicio = '".$ccoActual."' ";
					// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					// $num_affect = mysql_affected_rows();
					// if($num_affect>0)
					// {
						// //Guardo LOG de borrado en tabla Movhos 33
						// $q = "	INSERT INTO log_agenda
												  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   // VALUES
												  // ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000033', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
						// $resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					// }
				// }
			}
		}
	  }
	}
}

function validar_unidad($art){

	global $wbasedato;
	global $conex;

	$dividir = 1;

	//Si la unidad de la tabla 26 es igual a la unidad de la tabla 115 entonces tomara la concentracion de la tabla 115.
	$q = "  SELECT Relcon
			  FROM ".$wbasedato."_000026, ".$wbasedato."_000115
			 WHERE Relart = Artcod
			   AND Reluni = Artuni
			   AND Relart = '".$art."'" ;
	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);

	if($num > 0){

		$dividir = $row['Relcon'];

	}else{

		//Revisar si la unidad de presentacion es diferente de la unidad de fracccion, ademas se revisa si la fraccion es igual a 1,
		//en este caso se tomara la concentracion (Ej: un PUFF es igual a una DO)
		$q = "  SELECT Relcon
			      FROM ".$wbasedato."_000059, ".$wbasedato."_000115
				 WHERE Relart = Defart
			       AND Relpre != Deffru
			       AND Relart = '".$art."'
			       AND Deffra = '1'" ;
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$row = mysql_fetch_array($res);

		if($num > 0){

			$dividir = $row['Relcon'];

		}
	}

	return $dividir;

}


function buscar_saldo_pendiente($wtip, $whis, $wing)
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	global $wartnom;
	global $wartuni;
	global $wunides;


	$control_saldo = "";

	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos

	 $q = " SELECT spaart, spauen-spausa, id, spacco "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis                            = '" . $whis . "'"
		. "    AND spaing                            = '" . $wing . "'"
		. "    AND ROUND((spauen-spausa),3) > 0 "
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num >= 1)
	{

		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);

			$validar_unidad = validar_unidad($row[0]);

			//Validar si el saldo actual tiene unidades completas, en ese caso no permite la entrega del paciente.
			if(floor($row[1]/$validar_unidad) >= 1){

				$control_saldo = "total";

			}
			elseif( ($row[1]/$validar_unidad - floor($row[1]/$validar_unidad)) > 0){

				$control_saldo = "descarte";
			}
		}
	}

	return $control_saldo;
}

function buscar_saldo_pendiente_insumos($whis, $wing)
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$array_control_saldo = array();

	$query =    "SELECT Caraux, SUM(Carcca - Carcap - Carcde) as saldo_insumos, Descripcion
				   FROM ".$wbasedato."_000227, usuarios 
				  WHERE Carhis = '".$whis."'
					AND Caring = '".$wing."'
					AND Carcca - Carcap - Carcde > 0
					AND Carest = 'on'
					AND Caraux = Codigo
			   GROUP BY Caraux ";
	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
	$num = mysql_num_rows($res);
		
	if($num > 0){
				
		while($row = mysql_fetch_array($res)){
		
			$array_control_saldo[$row['Caraux']] = $row;
			
		}
		
	}
	
	return $array_control_saldo;
}

function calcularDiasEstancia( $conex, $wmovhos, $historia, $ingreso, $cco, $fechaEgreso, $horaEgreso )
{
	$tiempoEstancia = 0;
	
	if( empty( $fechaEgreso ) )
	{
		$fechaEgreso	= date("Y-m-d");
		$horaEgreso		= date("H:i:s");
	}
	
	
	$fechaIngreso = '';
	
	//por defecto la fecha y hora de la instituci?n es la fecha y hora de ingreso
	//Ya que si el paciente no tiene m?s movimientos y debe tomar esta fecha y hora de ingreso
	
	//Consulto la fecha y hora de ingreso del paciente al instituci?n
	$sql = "SELECT Fecha_data, Hora_data
			  FROM ".$wmovhos."_000018
			 WHERE Ubihis = '".$historia."'
			   AND Ubiing = '".$ingreso."'";
			
	$resFH = mysql_query( $sql, $conex ) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	
	if( $rowsFH = mysql_fetch_array( $resFH ) )
	{
		$fechaIngreso = $rowsFH['Fecha_data'];
		$horaIngreso  = $rowsFH['Hora_data'];
	}
	
	
	//Se busca si el paciente ha tenido ingresos a otros centros de costos
	//De ser as? se debe tomar este como fecha y hora de ingreso
	$q=" SELECT Fecha_ing, Hora_ing
		   FROM ".$wmovhos."_000032 
		  WHERE Historia_clinica = '".$historia."'
		    AND Num_ingreso      = '".$ingreso."'
		    AND Servicio         = '".$cco."'
		  ORDER BY 1 DESC, 2 DESC
		  LIMIT 1
		  ";
		
	$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	
	if( $rowdia = mysql_fetch_array($err) ){
		$fechaIngreso = $rowdia['Fecha_ing'];
		$horaIngreso  = $rowdia['Hora_ing'];
	}
	
	if( !empty($fechaIngreso) ){
		
		$datetime1 		= new DateTime( $fechaIngreso." ".$horaIngreso );
		$datetime2		= new DateTime( $fechaEgreso." ".$horaEgreso );
		$timeDiff 		= $datetime1->diff($datetime2);
		
		//Calculo de dias de estancias contada en d?as a dos decimales
		$tiempoEstancia = round( $timeDiff->days + $timeDiff->h/24 + $timeDiff->i/(24*60), 2 );
	}
	
	return $tiempoEstancia;
}

// Establece el estado de alta para un paciente en urgencias
function altaPacienteUrgencias($basedatos,$basedatoshce,$paciente,$ingreso,$wemp_pmla,$seguridad,$bandera, $wcubiculo_ocupado='', $turno='')
{
	global $conex;
	global $conexUnix;
	global $wbasedato;
    $wbasedato = $basedatos;
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	//Verifica si el paciente tiene saldo pendiente en medicamentos por aplicar o descartar.
	$validar_saldo = buscar_saldo_pendiente('NoApl', $paciente, $ingreso);

	if($validar_saldo != ''){

		return "El paciente tiene medicamentos pendientes por aplicar o descartar, no se puede dar de alta, favor comunicarse con la enfermera encargada.";
	}
	
	//Verifica si el paciente tiene saldo pendiente en insumos por aplicar.
	$validar_saldo_insumos = buscar_saldo_pendiente_insumos($paciente, $ingreso);

	if(count($validar_saldo_insumos) > 0){
		
		foreach($validar_saldo_insumos as $key => $value){
			
			$auxiliares .=  $value['Descripcion']."\n";
		}
		
		return "El paciente tiene insumos pendientes por aplicar o devolver, no se puede dar de alta, favor comunicarse con la(s) auxiliar(es) encargada(s): \n\n".$auxiliares."\n";
		
	}
	
	// Se consulta si el paciente sigue activo en Unix
	$qact = "SELECT COUNT(*)
			   FROM inpac
			  WHERE pachis = '".$paciente."'
				AND pacnum = ".$ingreso."";
	$rs_act = odbc_do($conexUnix,$qact);
	odbc_fetch_row($rs_act);
	$campos = odbc_result($rs_act,1);

	// Si no est? activo en Unix seg?n inpac
	// mira en inpaci si est? inactivo con el mismo ingreso
	// Sino el ingreso fue cancelado y se borra ingreso en Matrix
	if(!$campos || $campos==0)
	{
		// Se consulta si el paciente tiene el mismo ingreso en inpaci (Pacientes inactivos)
		$qin = "SELECT COUNT(*)
				   FROM inpaci
				  WHERE pachis = '".$paciente."'
					AND pacnum = ".$ingreso."";
		$rs_in = odbc_do($conexUnix,$qin);
		odbc_fetch_row($rs_in);
		$fields = odbc_result($rs_in,1);
	}

	// Si se identific? ingreso diferente al que traemos en inpaci
	// El alta debe ser con borrado de ingreso en Matrix
	if(isset($fields) && $fields==0 && $bandera!="Ingreso")
	{
		// Consulto el ingreso actual en inpaci
		$qcomp = " SELECT pacing
				     FROM inpaci
				    WHERE pachis = '".$paciente."'";
		$rs_comp = odbc_do($conexUnix,$qcomp);
		odbc_fetch_row($rs_comp);
		$ing_act = odbc_result($rs_comp,1);

		if($ingreso>$ing_act)
			$tipo_alta = "borrado";
		else
			$tipo_alta = "normal";
	}
	else
	{
		// Si el alta es autom?tica y se encontraron registros en inpac no se da de alta
		// El que este en inpac y no en urgencias quiere decir que fue trasladado a otro centro de costos
		// Notese que si el alta no es automatica deja dar de alta asi este activo en inpac
		// El operador puede volver a activar el paciente si se equivoc? al dar de alta
		if(isset($bandera) && $bandera=="auto" && isset($campos) && $campos>0)
			$tipo_alta = "noalta";
		else
			$tipo_alta = "normal";
	}

	// 2012-01-19
	// Si fecha de egreso en unix es igual a la fecha actual y el paciente a?n aparece en la tabla de
	// Estado de habitaciones (movhos_000020) con cama asignada, entonces la historia no se debe dar de alta
	// Esto porque normalmente el paciente es egresado y el registro de egreso en Unix se hace al siguiente d?a
	// Si se egresa el mismo d?a en matrix puede haber inconsistencias en cuanto a la ocupaci?n de camas
	$qegr = "SELECT egregr
			   FROM inmegr
			  WHERE egrhis = '".$paciente."'
				AND egrnum = ".$ingreso."";
	$rs_egr = odbc_do($conexUnix,$qegr);
	odbc_fetch_row($rs_egr);
	$fecha_egreso = odbc_result($rs_egr,1);

	if($fecha_egreso == $fecha)
	{
		//Consulto si el paciente a?n tiene cama asignada
		$qhab = "	SELECT Habhis, Habing
					FROM ".$basedatos."_000020
					WHERE Habhis = '".$paciente."'
					AND Habing = '".$ingreso."'";
		$reshab = mysql_query($qhab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qhab . " - " . mysql_error());
		$numhab = mysql_num_rows($reshab);
		// Si tiene cama asignada y fecha de egreso en unix es igual a la fecha actual
		if($numhab>0)
			$tipo_alta = "noalta";
	}

	// Consulto si el paciente est? en la tabla de Historias No Automaticas
	// Si est? en esta tabla indica que no se puede dar de alta autom?ticamente
	$qhna = "	SELECT Hnahis, Hnaing
				FROM ".$basedatos."_000140
				WHERE Hnahis = '".$paciente."'
				AND Hnaing = '".$ingreso."'
				AND Hnaest = 'on'";
	$reshna = mysql_query($qhna, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qhna . " - " . mysql_error());
	$numhna = mysql_num_rows($reshna);
	// Si est? en tabla de Historia no autom?tica y el alta es autom?tica
	if($numhna>0 && isset($bandera) && $bandera=="auto")
		$tipo_alta = "noalta";

	// Inicia el proceso de alta despu?s de validar si realmente se va a dar de alta
	if($tipo_alta != "noalta")
	{
		if($tipo_alta == "normal")
		{
			//Consulto si el paciente tiene conducta asignada
			$qcon = "	SELECT Mtrhis, Mtring, Mtrcon
						FROM ".$basedatoshce."_000022
						WHERE Mtrhis = '".$paciente."'
						AND Mtring = '".$ingreso."'
						AND Mtrcon != ''
						AND Mtrcon != 'NO APLICA'
						AND Mtrest = 'on'";
			$rescon = mysql_query($qcon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcon . " - " . mysql_error());
			$rowcon = mysql_fetch_array($rescon);
			$numcon = mysql_num_rows($rescon);
			// Si tiene conducta asiganada y es alta autom?tica no se da de alta
			if($numcon>0 && isset($bandera) && $bandera=="auto")
			{
				return "no-alta";
			}
			else
			{

				//Consulto si el paciente est? en proceso de traslado
				$qptr = "	SELECT Ubihis, Ubiing, Ubiptr, Eyrsor, Eyrsde, Cconom, Ubisac
							FROM ".$basedatos."_000017, ".$basedatos."_000018, ".$basedatos."_000011
							WHERE Ubihis = '".$paciente."'
							AND Ubiing = '".$ingreso."'
							AND Ubiptr = 'on'
							AND Ubihis = Eyrhis
							AND Ubiing = Eyring
							AND Eyrest = 'on'
							AND Eyrsde = Ccocod ";
				$resptr = mysql_query($qptr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qptr . " - " . mysql_error());
				$rowptr = mysql_fetch_array($resptr);
				$numptr = mysql_num_rows($resptr);

				// Si est? en proceso de traslado no se puede dar de alta
				if($numptr>0)
				{
					return "El paciente no se puede dar de alta debido a que est? en proceso de traslado para el servicio ".$rowptr['Cconom'];
				}
				else
				{
					//Consulto datos en tabla 18 de movhos
					$qubi = "	SELECT Fecha_data, Hora_data, Ubisac
								FROM ".$basedatos."_000018
								WHERE Ubihis = '".$paciente."'
								AND Ubiing = '".$ingreso."'";
					$resubi = mysql_query($qubi, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qubi . " - " . mysql_error());
					$rowubi = mysql_fetch_array($resubi);
					$numubi = mysql_num_rows($resubi);

					//Consulto el centro de costo actual del paciente
					$qcen = "	SELECT *
								  FROM ".$basedatos."_000018
								 WHERE Ubihis = '".$paciente."'
								   AND Ubiing = '".$ingreso."'";
					$rescen = mysql_query($qcen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcen . " - " . mysql_error());
					$rowcen = mysql_fetch_array($rescen);

					$registrosFila = obtenerRegistrosFila($qcen);

					//Se libera el cubiculo en el que estaba el paciente.
					$q = " UPDATE ".$wbasedato."_000020 "
						."    SET habhis = '', "
						."        habing = '', "
						."        habfal = '".$fecha."', "
						."        habhal = '".$hora."', "
						."        habdis = 'on' "
						."  WHERE habhis = '".$paciente."'
							  AND habing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num_affect_cub = mysql_affected_rows();

					if($num_affect_cub > 0)
					{
						//Guardo LOG de actualizaci?n alta en tabla log_agenda
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedato."_000020', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."|".$wcubiculo_ocupado."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}else{

						//Registra en la tabla log_agenda si el cubiculo no se actualizo.
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'No se actualizo la tabla ".$wbasedato."_000020', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."|Cubiculo asignado = ".$wcubiculo_ocupado."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					}


					//Actualizo tabla 18 de Movhos asignandole los parametros del alta
					$q = "	UPDATE ".$basedatos."_000018
							   SET Ubiald = 'on',
								   Ubifad = '".$fecha."',
								   Ubihad = '".$hora."',
								   Ubiuad = '".$seguridad."'
							 WHERE Ubihis = '".$paciente."'
							   AND Ubiing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de actualizaci?n alta en tabla log_agenda
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedato."_000018', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}

					//Consulto el c?digo de la conducta de alta en la tabla 35 de HCE
					$qalt = "	SELECT Concod
								FROM ".$basedatoshce."_000035
								WHERE Conalt = 'on'
								AND Conadm = 'on'";
					$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
					$rowalt = mysql_fetch_array($resalt);
					$conducta = $rowalt['Concod'];

					// Cosulto si el paciente ya est? registrado en la tabla 22 de Hce
					$q = "	SELECT Mtrhis
							FROM ".$basedatoshce."_000022
							WHERE Mtrhis = '".$paciente."'
							AND Mtring = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);

					if($num==0)
					{
						$q = "INSERT INTO
								".$basedatoshce."_000022
									(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrcci,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcon,Mtrcur,Seguridad)
								VALUES
									('".$basedatoshce."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$rowcen['Ubisac']."','','on','off','','".$conducta."','off','C-".$seguridad."')";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de grabaci?n alta en tabla hce_000022
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Alta ".$bandera."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}
					else
					{
						// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
						$qlog = " SELECT *
									FROM ".$basedatoshce."_000022
								   WHERE Mtrhis = '".$paciente."'
									 AND Mtring = '".$ingreso."'";
						$registrosFila = obtenerRegistrosFila($qlog);

						$q = "	UPDATE ".$basedatoshce."_000022
								SET Mtrest='on', Mtrcon='".$conducta."', Mtrcur='off'
								WHERE Mtrhis = '".$paciente."'
								AND	Mtring = '".$ingreso."'";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de actualizaci?n alta en tabla hce_000022
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}

						// --> 	Apagar las alertas que existan, generadas en el monitor del turnero.
						//		Jerson trujillo, 2015-10-13
						if(trim($turno) != '')
						{
							$sqlApagAlerta = "
							UPDATE ".$basedatos."_000178
							   SET Atullc = 'off',
								   Atullv = 'off'
							 WHERE Atutur = '".$turno."'
							";
							mysql_query($sqlApagAlerta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlApagAlerta):</b><br>".mysql_error());
						}
					}

					// 2013-02-26
					// Con esta funci?n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
					BorrarAltasMuertesAntesDeAgregarNueva($conex, $basedatos, $paciente, $ingreso, "Egreso existente");

					//2013-04-25
					//Cancelar el servicio de dietas
					cancelar_pedido_alimentacion($paciente, $ingreso, $rowubi['Ubisac'], "Cancelar", 'movhos');					
					cancelarPedidoInsumos($conex, $wbasedato, $paciente, $ingreso); //Noviembre 1 de 2017 Jonatan

					// Como ya se borro el egreso se comentan las siguientes l?neas

					// Cosulto si el paciente ya est? registrado en la tabla 33 de Movhos
					// $q = "	SELECT *
							// FROM ".$basedatos."_000033
							// WHERE Historia_clinica = '".$paciente."'
							// AND Num_ingreso = '".$ingreso."'
							// AND Servicio = '".$rowcen['Ubisac']."'
							// AND Tipo_egre_serv = 'ALTA'";
					// $resegr = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					// $numegr = mysql_num_rows($resegr);


					// 2013-05-09
					// Cosulto si el cco de ingreso del paciente registrado en la tabla 000022 de historia clinica electronica (Mtrcci) es hospitalario
					// y en la tabla 000018 de movimiento hospitalario no tiene cco anterior (Ubisan)
					// Si es as? no se debe registrar el egreso en la tabla 000033 de movimiento hospitalario
					$q = "	SELECT Ubihis
							FROM ".$basedatos."_000018, ".$basedatoshce."_000022, ".$basedatos."_000011
							WHERE Ubihis = '".$paciente."'
							AND Ubiing = '".$ingreso."'
							AND Ubihis = Mtrhis
							AND Ubiing = Mtring
							AND Mtrcci = Ccocod
							AND Ccohos = 'on'
							AND Ccoing != 'on'
							AND (TRIM(Ubisan) = '' OR TRIM(Ubisan) = 'NO APLICA')";
					$resegr = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$numegr = mysql_num_rows($resegr);

					$registraEgreso = true;
					if($numegr>0)
						$registraEgreso = false;

					if($registraEgreso)
					{
						$tiempoEstancia = calcularDiasEstancia( $conex, $basedatos, $paciente, $ingreso, $rowcen['Ubisac'], $fecha, $hora );
					
						//Registro el egreso en la tabla 33 de Movhos
						$q = "	INSERT INTO
								".$basedatos."_000033
									(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
								VALUES
									('".$basedatos."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$rowcen['Ubisac']."','1','".$fecha."','".$hora."','ALTA','".$tiempoEstancia."','C-".$seguridad."')";
						$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de grabaci?n egreso en tabla movhos_000033
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla ".$wbasedato."_000033', '".$seguridad."', 'Alta ".$bandera."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}
					// else
					// {
						// $registrosFila = obtenerRegistrosFila($q);

						// $q = "	UPDATE ".$basedatos."_000033
								// SET Fecha_data='".$fecha."', Hora_data='".$hora."', Fecha_egre_serv='".$fecha."', Hora_egr_serv='".$hora."', Seguridad='C-".$seguridad."'
								// WHERE Historia_clinica = '".$paciente."'
								// AND Num_ingreso = '".$ingreso."'
								// AND Servicio = '".$rowcen['Ubisac']."'
								// AND Tipo_egre_serv = 'ALTA'";
						// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						// $num_affect = mysql_affected_rows();
						// if($num_affect>0)
						// {
							// //Guardo LOG de actualizaci?n alta en tabla hce_000022
							// $q = "	INSERT INTO log_agenda
													  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   // VALUES
													  // ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla movhos_000033', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							// $resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						// }
					// }


					if($res1)
						return "ok";
					else
						return "Ocurri? un error en el proceso. \n Error: ".$res1;
				}
			}
		}
		elseif($tipo_alta == "borrado")
		{
			//Consulto si el paciente est? en proceso de traslado
			$qptr = "	SELECT Ubihis, Ubiing, Ubiptr, Eyrsor, Eyrsde, Cconom
						FROM ".$basedatos."_000017, ".$basedatos."_000018, ".$basedatos."_000011
						WHERE Ubihis = '".$paciente."'
						AND Ubiing = '".$ingreso."'
						AND Ubiptr = 'on'
						AND Ubihis = Eyrhis
						AND Ubiing = Eyring
						AND Eyrest = 'on'
						AND Eyrsde = Ccocod ";
			$resptr = mysql_query($qptr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qptr . " - " . mysql_error());
			$rowptr = mysql_fetch_array($resptr);
			$numptr = mysql_num_rows($resptr);

			// Si est? en proceso de traslado no se puede dar de alta
			if($numptr>0)
			{
				return "El paciente no se puede dar de alta debido a que est? en proceso de traslado para el servicio ".$rowptr['Cconom'];
			}
			else
			{

				// 2013-02-22
				// Cosulto si el paciente tiene registros de movimiento hospitalario
				$qeyr = " SELECT Eyrhis
							FROM ".$basedatos."_000017
						   WHERE Eyrhis = '".$paciente."'
							 AND Eyring = '".$ingreso."'
							 AND Eyrest = 'on'";
				$reseyr = mysql_query($qeyr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qeyr . " - " . mysql_error());
				$numeyr = mysql_num_rows($reseyr);

				// Si el paciente no ha tenido movimiento hospitalario si se puede hacer el borrado
				if($numeyr==0)
				{
					//Consulto datos en tabla 18 de movhos
					$qubi = "	SELECT Fecha_data, Hora_data, Ubisac
								FROM ".$basedatos."_000018
								WHERE Ubihis = '".$paciente."'
								AND Ubiing = '".$ingreso."'";
					$resubi = mysql_query($qubi, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qubi . " - " . mysql_error());
					$rowubi = mysql_fetch_array($resubi);
					$numubi = mysql_num_rows($resubi);

					if($numubi>0)
					{
						// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
						$qlog = " SELECT *
									FROM root_000037
								   WHERE Orihis = '".$paciente."'
									 AND Oriing = '".$ingreso."'
									 AND Oriori = '".$wemp_pmla."'";
						$registrosFila = obtenerRegistrosFila($qlog);

						//Actualizo tabla 37 de root
						$q = "	UPDATE root_000037
								SET Fecha_data='".$rowubi['Fecha_data']."', Hora_data='".$rowubi['Hora_data']."', Oriing=Oriing-1
								WHERE Orihis = '".$paciente."'
								AND	Oriing = '".$ingreso."'
								AND	Oriori = '".$wemp_pmla."'";
						$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de actualizaci?n en tabla root_000037
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla root_000037', '".$seguridad."', 'Alta ".$bandera." Oriing-1', '".$registrosFila."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}

					$fechaLog = date('Y-m-d');
					$horaLog = date('H:i:s');

					// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
					$qlog = " SELECT *
								FROM ".$basedatos."_000016
							   WHERE Inghis = '".$paciente."'
								 AND Inging = '".$ingreso."'";
					$registrosFila = obtenerRegistrosFila($qlog);

					//Borro registro en tabla 16 de Movhos
					$q = "	DELETE
							  FROM ".$basedatos."_000016
							 WHERE Inghis = '".$paciente."'
							   AND Inging = '".$ingreso."'";
					$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de borrado en tabla Movhos 16
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000016', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}

					// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
					$qlog = " SELECT *
								FROM ".$basedatos."_000018
							   WHERE Ubihis = '".$paciente."'
								 AND Ubiing = '".$ingreso."'";
					$registrosFila = obtenerRegistrosFila($qlog);


					//Borro registro en tabla 18 de Movhos
					$q = "	DELETE
							  FROM ".$basedatos."_000018
							 WHERE Ubihis = '".$paciente."'
							   AND Ubiing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());


					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de borrado en tabla Movhos 18
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000018', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
						$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}

					// Cosulto si el paciente ya est? registrado en la tabla 22 de Hce
					$q = "	SELECT *
							FROM ".$basedatoshce."_000022
							WHERE Mtrhis = '".$paciente."'
							AND Mtring = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);

					if($num>0)
					{
						$registrosFila = obtenerRegistrosFila($q);

						$q = "	DELETE
								  FROM ".$basedatoshce."_000022
								 WHERE Mtrhis = '".$paciente."'
								   AND	Mtring = '".$ingreso."'";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de borrado en tabla hce 22
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   VALUES
													  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}

					//Se libera el cubiculo en el que estaba el paciente.
					$q = " UPDATE ".$wbasedato."_000020 "
						."    SET habhis = '', "
						."        habing = '', "
						."        habfal = '".$fecha."', "
						."        habhal = '".$hora."', "
						."        habdis = 'on' "
						."  WHERE habhis = '".$paciente."'
							  AND habing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num_affect_cub = mysql_affected_rows();

					if($num_affect_cub > 0)
					{
						//Guardo LOG de actualizaci?n alta en tabla movhos_000018
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedato."_000020', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."|".$wcubiculo_ocupado."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}


					// 2013-02-26
					// Con esta funci?n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
					BorrarAltasMuertesAntesDeAgregarNueva($conex, $basedatos, $paciente, $ingreso, "Alta ".$bandera);

					//2013-04-25
					//Cancelar el servicio de dietas
					cancelar_pedido_alimentacion($paciente, $ingreso, $rowubi['Ubisac'], "Cancelar", 'movhos');
					cancelarPedidoInsumos($conex, $wbasedato, $paciente, $ingreso); //Noviembre 1 de 2017 Jonatan
					// Como ya se borro el egreso se comentan las siguientes l?neas

					// // Cosulto si el paciente ya est? registrado en la tabla 33 de Movhos
					// $q = "	SELECT *
							// FROM ".$basedatos."_000033
							// WHERE Historia_clinica = '".$paciente."'
							// AND Num_ingreso = '".$ingreso."'
							// AND	Tipo_egre_serv = 'ALTA'";
					// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					// $num = mysql_num_rows($res);

					// if($num>0)
					// {
						// $registrosFila = obtenerRegistrosFila($q);

						// $q = "	DELETE
								  // FROM ".$basedatos."_000033
								 // WHERE Historia_clinica = '".$paciente."'
								   // AND	Num_ingreso = '".$ingreso."'
								   // AND	Tipo_egre_serv = 'ALTA'";
						// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						// $num_affect = mysql_affected_rows();
						// if($num_affect>0)
						// {
							// //Guardo LOG de borrado en tabla hce 22
							// $q = "	INSERT INTO log_agenda
													  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   // VALUES
													  // ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000033', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							// $resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						// }
					// }

					if($res1)
						return "ok";
					else
						return "Ocurri? un error en el proceso. \n Error: ".$res1;
				}
			}
		}
	}
	else
	{
		return "El paciente no se puede dar de alta porque a?n est? activo en el sistema";
	}
}

// Establece el estado de alta por muerte para un paciente en urgencias
function muertePacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad,$wcubiculo_ocupado)
{
	global $conex;
	global $wbasedato;
    $wbasedato = $basedatos;
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	//Consulto si el paciente est? en proceso de traslado
	$qptr = "	SELECT Ubihis, Ubiing, Ubiptr, Eyrsor, Eyrsde, Cconom
				FROM ".$basedatos."_000017, ".$basedatos."_000018, ".$basedatos."_000011
				WHERE Ubihis = '".$paciente."'
				AND Ubiing = '".$ingreso."'
				AND Ubiptr = 'on'
				AND Ubihis = Eyrhis
				AND Ubiing = Eyring
				AND Eyrest = 'on'
				AND Eyrsde = Ccocod ";
	$resptr = mysql_query($qptr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qptr . " - " . mysql_error());
	$rowptr = mysql_fetch_array($resptr);
	$numptr = mysql_num_rows($resptr);

	// Si est? en proceso de traslado no se puede dar de alta
	if($numptr>0)
	{
		return "El paciente no se puede dar de alta por muerte debido a que est? en proceso de traslado para el servicio ".$rowptr['Cconom'];
	}
	else
	{

		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM ".$basedatos."_000018
				   WHERE Ubihis = '".$paciente."'
					 AND Ubiing = '".$ingreso."'";
		$registrosFila = obtenerRegistrosFila($qlog);

		//Se libera el cubiculo en el que estaba el paciente.
		$q = " UPDATE ".$wbasedato."_000020 "
			."    SET habhis = '', "
			."        habing = '', "
			."        habfal = '".$fecha."', "
			."        habhal = '".$hora."', "
			."        habdis = 'on' "
			."  WHERE habhis = '".$paciente."'
				  AND habing = '".$ingreso."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num_affect_cub = mysql_affected_rows();

		if($num_affect_cub > 0)
		{
			//Guardo LOG de actualizaci?n alta en tabla movhos_000018
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedato."_000020', '".$seguridad."', 'Muerte Manual', '".$registrosFila."|".$wcubiculo_ocupado."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		//Actualizo tabla 18 de Movhos asignandole los parametros del alta
		$q = "	UPDATE ".$basedatos."_000018
				SET Ubimue='on', Ubiald='on', Ubifad='".$fecha."', Ubihad='".$hora."', Ubiuad='".$seguridad."'
				WHERE Ubihis = '".$paciente."'
				AND	Ubiing = '".$ingreso."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de actualizacion en tabla movhos 18 - alta por muerte
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedato."_000018', '".$seguridad."', 'Alta por muerte', '".$registrosFila."')";
			$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		//Consulto el c?digo de la conducta de alta en la tabla 35 de HCE
		$qalt = "	SELECT Concod
					FROM ".$basedatoshce."_000035
					WHERE Conmue = 'on'
					AND Conadm = 'on'";
		$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
		$rowalt = mysql_fetch_array($resalt);
		$conducta = $rowalt['Concod'];

		// Cosulto si el paciente ya est? registrado en la tabla 22 de Hce
		$q = "	SELECT Mtrhis
				FROM ".$basedatoshce."_000022
				WHERE Mtrhis = '".$paciente."'
				AND Mtring = '".$ingreso."'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		if($num==0)
		{
			$q = "INSERT INTO
					".$basedatoshce."_000022
						(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrcci,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcon,Mtrcur,Seguridad)
					VALUES
						('".$basedatoshce."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$codcco."','','on','off','','".$conducta."','off','C-".$seguridad."')";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de grabacion en tabla hce 22 - alta por muerte
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Alta por muerte')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
		else
		{
			// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			$qlog = " SELECT *
						FROM ".$basedatoshce."_000022
					   WHERE Mtrhis = '".$paciente."'
						 AND Mtring = '".$ingreso."'";
			$registrosFila = obtenerRegistrosFila($qlog);

			$q = "	UPDATE ".$basedatoshce."_000022
					SET Mtrest='on', Mtrcon='".$conducta."', Mtrcur='off'
					WHERE Mtrhis = '".$paciente."'
					AND	Mtring = '".$ingreso."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de actualizacion en tabla hce 22 - alta por muerte
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Alta por muerte', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}

		// 2013-02-26
		// Con esta funci?n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
		BorrarAltasMuertesAntesDeAgregarNueva($conex, $basedatos, $paciente, $ingreso, "Alta por muerte");


		//2013-04-25
		//Cancelar el servicio de dietas
		cancelar_pedido_alimentacion($paciente, $ingreso, $codcco, "Muerte", 'movhos');
		// Como ya se borro el egreso se comentan las siguientes l?neas

		// // Cosulto si el paciente ya est? registrado en la tabla 33 de Movhos
		// $q = "	SELECT *
				// FROM ".$basedatos."_000033
				// WHERE Historia_clinica = '".$paciente."'
				// AND Num_ingreso = '".$ingreso."'
				// AND Servicio = '".$codcco."'
				// AND Tipo_egre_serv = 'MUERTE'";
		// $resegr = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		// $numegr = mysql_num_rows($resegr);

		// if($numegr==0)
		// {


		// 2013-05-09
		// Cosulto si el cco de ingreso del paciente registrado en la tabla 000022 de historia clinica electronica (Mtrcci) es hospitalario
		// y en la tabla 000018 de movimiento hospitalario no tiene cco anterior (Ubisan)
		// Si es as? no se debe registrar el egreso en la tabla 000033 de movimiento hospitalario
		$q = "	SELECT Ubihis
				FROM ".$basedatos."_000018, ".$basedatoshce."_000022, ".$basedatos."_000011
				WHERE Ubihis = '".$paciente."'
				AND Ubiing = '".$ingreso."'
				AND Ubihis = Mtrhis
				AND Ubiing = Mtring
				AND Mtrcci = Ccocod
				AND Ccohos = 'on'
				AND Ccoing != 'on'
				AND (TRIM(Ubisan) = '' OR TRIM(Ubisan) = 'NO APLICA')";
		$resegr = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numegr = mysql_num_rows($resegr);

		$registraEgreso = true;
		if($numegr>0)
			$registraEgreso = false;

		if($registraEgreso)
		{
			$tiempoEstancia = calcularDiasEstancia( $conex, $basedatos, $paciente, $ingreso, $codcco, $fecha, $hora );
		
			//Registro el egreso en la tabla 33 de Movhos
			$q = "	INSERT INTO
					".$basedatos."_000033
						(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
					VALUES
						('".$basedatos."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$codcco."','1','".$fecha."','".$hora."','MUERTE','".$tiempoEstancia."','C-".$seguridad."')";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de grabaci?n egreso en tabla movhos_000033
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla ".$wbasedato."_000033', '".$seguridad."', 'Alta por muerte')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
		// else
		// {
			// $registrosFila = obtenerRegistrosFila($q);

			// $q = "	UPDATE ".$basedatos."_000033
					// SET Fecha_data='".$fecha."', Hora_data='".$hora."', Fecha_egre_serv='".$fecha."', Hora_egr_serv='".$hora."', Seguridad='C-".$seguridad."'
					// WHERE Historia_clinica = '".$paciente."'
					// AND Num_ingreso = '".$ingreso."'
					// AND Servicio = '".$codcco."'
					// AND Tipo_egre_serv = 'MUERTE'";
			// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			// $num_affect = mysql_affected_rows();
			// if($num_affect>0)
			// {
				// //Guardo LOG de actualizaci?n alta en tabla movhos_000033
				// $q = "	INSERT INTO log_agenda
										  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   // VALUES
										  // ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla movhos_000033', '".$seguridad."', 'Alta por muerte', '".$registrosFila."')";
				// $resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			// }
		// }

		if($res1)
			return "ok";
		else
			return "Ocurri? un error en el proceso. \n Error: ".$res1;
	}
}

// Vuelve y activa un paciente dado de alta
function activarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad)
{
	global $conex;
	global $conexUnix;
	$respuesta = '';


	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	// Cosulto si el paciente est? en agenda urgencias
	$qagn = "	SELECT Ubihis
				FROM ".$basedatos."_000018, ".$basedatoshce."_000022
				WHERE Ubihis = '".$paciente."'
				AND Ubimue != 'on'
				AND Ubiald != 'on'
				AND Ubisac = '".$codcco."'
				AND Ubihis = Mtrhis
				AND Ubiing = Mtring ";
	$resagn = mysql_query($qagn, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qagn . " - " . mysql_error());
	$numagn = mysql_num_rows($resagn);

	if($numagn==0)
	{
		// Se consulta si el paciente sigue activo en Unix
		$qact = "SELECT COUNT(*)
				   FROM inpac
				  WHERE pachis = '".$paciente."'
					AND pacnum = ".$ingreso."";
		$rs_act = odbc_do($conexUnix,$qact);
		odbc_fetch_row($rs_act);
		$campos = odbc_result($rs_act,1);

		// Si est? activo en Unix seg?n inpac
		if(isset($campos) & $campos>0)
		{
			// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			$qlog = " SELECT *
						FROM ".$basedatos."_000018
					   WHERE Ubihis = '".$paciente."'
						 AND Ubiing = '".$ingreso."'";
			$registrosFila = obtenerRegistrosFila($qlog);

			//Actualizo tabla 18 de Movhos de modo que quite los registros de alta
			$q = "	UPDATE ".$basedatos."_000018
					SET Ubimue='off', Ubiald='off', Ubialp='off', Ubifad='0000-00-00', Ubihad='00:00:00', Ubiuad=''
					WHERE Ubihis = '".$paciente."'
					AND	Ubiing = '".$ingreso."'";
			$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de actualizacion en tabla movhos 18 - Activacion paciente
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedato."_000018', '".$seguridad."', 'Activacion paciente', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			// Cosulto si el paciente ya est? registrado en la tabla 22 de Hce
			$q = "	SELECT *
					FROM ".$basedatoshce."_000022
					WHERE Mtrhis = '".$paciente."'
					AND Mtring = '".$ingreso."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			// Si est? registrado active de nuevo los regisros del paciente
			if($num>0)
			{
				$registrosFila = obtenerRegistrosFila($q);

				$q = "	UPDATE ".$basedatoshce."_000022
						SET Mtrest='on', Mtrcur='off', Mtrcon='', Mtrmed='', Mtrcua='', Mtrsal=''
						WHERE Mtrhis = '".$paciente."'
						AND	Mtring = '".$ingreso."'";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de actualizacion en tabla hce 22 - Activacion paciente
					$q = "	INSERT INTO log_agenda
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
									   VALUES
											  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Activacion paciente', '".$registrosFila."')";
					$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
			}

			// 2013-02-26
			// Con esta funci?n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
			BorrarAltasMuertesAntesDeAgregarNueva($conex, $basedatos, $paciente, $ingreso, "Activacion paciente");

			// Como ya se borro el egreso se comentan las siguientes l?neas

			// // Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			// $qlog = " SELECT *
						// FROM ".$basedatos."_000033
					   // WHERE Historia_clinica = '".$paciente."'
						 // AND Num_ingreso = '".$ingreso."'
						 // AND Tipo_egre_serv='ALTA'";//2013-02-25, eliminaba todos los egresos
			// $registrosFila = obtenerRegistrosFila($qlog);

			// //Borro el registro de egreso en la tabla 33 de Movhos
			// $q = "	DELETE FROM ".$basedatos."_000033
					// WHERE Historia_clinica = '".$paciente."'
					// AND Num_ingreso = '".$ingreso."'
					// AND Tipo_egre_serv='ALTA'";//2013-02-25, eliminaba todos los egresos
			// $res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			// $num_affect = mysql_affected_rows();
			// if($num_affect>0)
			// {
				// //Guardo LOG de borrado en tabla movhos 33 - Activacion paciente
				// $q = "	INSERT INTO log_agenda
										  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   // VALUES
										  // ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000033', '".$seguridad."', 'Activacion paciente', '".$registrosFila."')";
				// $resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			// }

			if ($res1)
				$respuesta = "ok";
			else
				$respuesta = "Ocurri? un error en el proceso. \n Error: ".$res1;
		}
		else
		{
			$respuesta = "inactivounix";
		}
	}
	else
	{
		$respuesta = "activo";
	}

	return $respuesta;
}

// Borra los registros del paciente para que su ingreso pueda ser reasignado
function reasignarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$wemp_pmla,$seguridad)
{
	global $conex;

	// Cosulto si el paciente est? en agenda urgencias

	$qtra = "	SELECT Ubihis
				FROM ".$basedatos."_000018, ".$basedatoshce."_000022
				WHERE Ubihis = '".$paciente."'
				AND Ubiing = '".$ingreso."'
				AND Ubisac = '".$codcco."'
				AND Ubihis = Mtrhis
				AND Ubiing = Mtring
				AND Mtrfco <> '0000-00-00'
				AND Mtrfco <> '' ";
	$restra = mysql_query($qtra, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qtra . " - " . mysql_error());
	$numtra = mysql_num_rows($restra);

	if($numtra==0)
	{

		// 2013-02-22
		// Cosulto si el paciente tiene registros de movimiento hospitalario
		$qeyr = " SELECT Eyrhis
					FROM ".$basedatos."_000017
				   WHERE Eyrhis = '".$paciente."'
					 AND Eyring = '".$ingreso."'
					 AND Eyrest = 'on'";
		$reseyr = mysql_query($qeyr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qeyr . " - " . mysql_error());
		$numeyr = mysql_num_rows($reseyr);

		// Si el paciente no ha tenido movimiento hospitalario si se puede reasignar la historia
		if($numeyr==0)
		{
			$fechaLog = date('Y-m-d');
			$horaLog = date('H:i:s');

			// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			$qlog = " SELECT *
						FROM ".$basedatos."_000016
					   WHERE Inghis = '".$paciente."'
						 AND Inging = '".$ingreso."'";
			$registrosFila = obtenerRegistrosFila($qlog);

			// Borra registro en tabla 16 de Movhos
			$q = "	DELETE FROM ".$basedatos."_000016
					WHERE Inghis = '".$paciente."'
					AND	Inging = '".$ingreso."'";
			$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla Movhos 16
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000016', '".$seguridad."', 'Reasignacion paciente', '".$registrosFila."')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			$qlog = " SELECT *
						FROM ".$basedatos."_000018
					   WHERE Ubihis = '".$paciente."'
						 AND Ubiing = '".$ingreso."'";
			$registrosFila = obtenerRegistrosFila($qlog);

			// Borra registro en tabla 18 de Movhos
			$q = "	DELETE FROM ".$basedatos."_000018
					WHERE Ubihis = '".$paciente."'
					AND	Ubiing = '".$ingreso."'";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla Movhos 18
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000018', '".$seguridad."', 'Reasignacion paciente', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			$qlog = " SELECT *
						FROM root_000037
					   WHERE Orihis = '".$paciente."'
						 AND Oriing = '".$ingreso."'
						 AND Oriori = '".$wemp_pmla."'";
			$registrosFila = obtenerRegistrosFila($qlog);

			// Borra registro de la tabla 37 de Root
			$q = "	DELETE FROM	root_000037
					WHERE Orihis = '".$paciente."'
					AND	Oriing = '".$ingreso."'
					AND	Oriori = '".$wemp_pmla."'";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla Root 37
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla root_000037', '".$seguridad."', 'Reasignacion paciente', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}


			// 2013-02-26
			// Con esta funci?n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
			BorrarAltasMuertesAntesDeAgregarNueva($conex, $basedatos, $paciente, $ingreso, "Reasignacion paciente");

			// Como ya se borro el egreso se comentan las siguientes l?neas

			// // Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			// $qlog = " SELECT *
						// FROM ".$basedatos."_000033
					   // WHERE Historia_clinica = '".$paciente."'
						 // AND Num_ingreso = '".$ingreso."'
						 // AND Tipo_egre_serv like 'MUERTE'"; //2013-02-25, eliminaba todos los egresos
			// $registrosFila = obtenerRegistrosFila($qlog);

			// // Borra registro de egreso en la tabla 33 de Movhos
			// $q = "	DELETE FROM	".$basedatos."_000033
					// WHERE Historia_clinica = '".$paciente."'
					// AND	Num_ingreso = '".$ingreso."'
					// AND Tipo_egre_serv like 'MUERTE'";//2013-02-25, eliminaba todos los egresos
			// $res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			// $num_affect = mysql_affected_rows();
			// if($num_affect>0)
			// {
				// //Guardo LOG de borrado en tabla Movhos 33
				// $q = "	INSERT INTO log_agenda
										  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   // VALUES
										  // ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000033', '".$seguridad."', 'Reasignacion paciente', '".$registrosFila."')";
				// $resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			// }

			// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			$qlog = " SELECT *
						FROM ".$basedatoshce."_000022
					   WHERE Mtrhis = '".$paciente."'
						 AND Mtring = '".$ingreso."'";
			$registrosFila = obtenerRegistrosFila($qlog);

			// Borra registro en tabla 22 de Hce
			$q = "	DELETE FROM ".$basedatoshce."_000022
					WHERE Mtrhis = '".$paciente."'
					AND	Mtring = '".$ingreso."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla Hce 22
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedatohce."_000022', '".$seguridad."', 'Reasignacion paciente', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			if($res1)
				return "ok";
			else
				return "Ocurri? un error en el proceso. \n Error: ".$res1;
		}
		else
		{
			return "no-reasignar";
		}
	}
	else
	{
		return "no-reasignar";
	}
}

/********************************************************************************
* CONSULTA EN UNIX LOS PACIENTES QUE NO ESTAN EN URGENCIAS Y LOS DA DE ALTA		*
*********************************************************************************/
function actualizarAltaPacientesUnix($basedatos,$basedatoshce,$wemp_pmla,$seguridad,$listados)
{
	global $conex;
	global $conexUnix;

	$ayer = date("Y-m-d",time()-86400);

	// Se consultan los pacientes activos en Unix
	$qact = "SELECT pachis, pacnum
			   FROM inpac ";
		//	   , insercco,outer inemp			// 2012-01-24
		//	  WHERE serccoser = pacser			// 2012-01-24
		//		AND paccer = empcod";			// 2013-01-24
		//		AND pacser = '04'";				// 2011-10-27
		//  	AND pacfec >= '".$ayer."'";		// 2011-10-27
	$rs_act = odbc_do($conexUnix,$qact);

	$k = 0;
	$listados_unix = array();
	// Se asigna la lista de pacientes de urgencias en Unix
	while (odbc_fetch_row($rs_act))
	{
		$listados_unix[$k] = odbc_result($rs_act,1)."-".odbc_result($rs_act,2);
		$k++;
	}

	// Se obtiene los registros que estan en lista de pacientes activos en el programa
	// pero ya no est?n como pacientes activos en Unix
	$altas_unix = array_diff($listados,$listados_unix);
	$conting=0;
	// Se da de alta los pacientes que ya no estan activos en Unix
	foreach ($altas_unix as $j => $value)
	{
		if(isset($altas_unix[$j]) && $altas_unix[$j]!="")
		{
			$paciente_alta = explode("-",$altas_unix[$j]);
			$historia_paciente = $paciente_alta[0];
			$ingreso_paciente = $paciente_alta[1];;
			//echo "<br>ALTA: ".$altas_unix[$j];
			altaPacienteUrgencias($basedatos,$basedatoshce,$historia_paciente,$ingreso_paciente,$wemp_pmla,"Movhos","auto");
			$conting++;
		}
	}
}

/********************************************************************************************
* CONSULTA EN UNIX LOS PACIENTES EN URGENCIAS Y ACTUALIZA EL LISTADO DE PACIENTES ACTIVOS 	*
********************************************************************************************/
function actualizarPacientesUnix($basedatos,$basedatoshce,$wemp_pmla,$seguridad,$listados)
{
	global $conex;
	global $conexUnix;

	if($conexUnix)
	{
		// Se llama a la funcion para dar de alta a los pacientes que no est?n en urgencias de Unix
		actualizarAltaPacientesUnix($basedatos,$basedatoshce,$wemp_pmla,$seguridad,$listados);

		// Se llama a la funcion para actualizar datos de pacientes en cl?nica
		actualizarDatosPacientes($basedatos,$wemp_pmla,$seguridad,$listados);

		$ayer = date("Y-m-d",time()-86400);

		// Se consultan los pacientes activos en urgencias de Unix
		$qact = "SELECT pachis, pacnum, pacced, pactid, pacser
				   FROM inpac ";
			//	   , insercco,outer inemp			// 2012-01-24
			//	  WHERE serccoser = pacser			// 2012-01-24
			//	    AND paccer = empcod";			// 2012-01-24
			//		AND pacser = '04'";				// 2011-10-27
			//		AND pacfec >= '".$ayer."'";		// 2011-10-27
		$rs_act = odbc_do($conexUnix,$qact);
		$conting = 0;

		// Ciclo para actualizar el listado de pacientes activos en urgencias
		while (odbc_fetch_row($rs_act))
		{
			$codCco="";
			$historia_paciente = odbc_result($rs_act,1);
			$ingreso_paciente = odbc_result($rs_act,2);
			$cco_paciente = odbc_result($rs_act,5);
			$paciente_unix = $historia_paciente."-".$ingreso_paciente;
			//echo "INGRESO: ".$paciente_unix."<br>";
			// Si la historia cl?nica obtenida de Unix no est? en listado de pacientes, ingr?sela.
			if (!in_array($paciente_unix,$listados))
			{
				$codCco = obtenerCcoMatrix($basedatos,$cco_paciente,$wemp_pmla,$seguridad);
				// Si existe relaci?n de cco - servicio (ccoseu vac?o) en la tabla 11 de movimiento hospitalario
				if($codCco!="" && $codCco!=" ")
				{
					ingresarPacientesUrgencias($basedatos,$basedatoshce,$codCco,$historia_paciente,$wemp_pmla,$seguridad);
				}
				else
				{
					// Se busca la relaci?n de cco - servicio en la tabla insercco de Unix
					$qser = " SELECT serccocco
								FROM insercco
							   WHERE serccoser = '".$cco_paciente."'
							     AND serccocco is not null
							   UNION
							  SELECT ' ' AS serccocco
								FROM insercco
							   WHERE serccoser = '".$cco_paciente."'
								 AND serccocco is null
							";
					$rs_ser = odbc_do($conexUnix,$qser);

					if(odbc_fetch_row($rs_ser))
					{
						$codCco = odbc_result($rs_ser,1);
						if($codCco!=' ')
							ingresarPacientesUrgencias($basedatos,$basedatoshce,$codCco,$historia_paciente,$wemp_pmla,$seguridad);
					}
				}
				$conting++;
			}
		}
	}
}

// Borra los registros anteriores a $ndias en la tabla de $tblog
function borrarLogsAntiguos($tblog,$ndias)
{
	global $conex;

	$nseg = 86400*$ndias;
	$antiguos = date("Y-m-d",time()-$nseg);

	// Borra registros antiguos de la tabla de log
	$q = "	DELETE FROM ".$tblog."
			WHERE Fecha < '".$antiguos."'";
	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/*********************************************************************
************** LISTADO DE PACIENTES INGRESADOS  **********************
*********************************************************************/

function agendaPacientesUrgencias($basedatos,$basedatoshce,$ccoCodigo,$esUrgencias,$wemp_pmla,$seguridad)
{

// Validaci?n de usuario
if (!isset($user))
{
	if (!array_key_exists("user",$_SESSION))
	{
		session_register("user");
	}
	$user="";
}

//Codigo de usuario que ingreso al sistema
if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
else
	$wuser = "";

$usuario = new Usuario();

$usuario->codigo = $wuser;

//Variable para determinar la empresa
if(!isset($wemp_pmla))
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Valida codigo de usuario en sesion si no esta registrado el sistema termina la ejecucion
if (!array_key_exists("user",$_SESSION) || !isset($seguridad) || $seguridad=="")
{
	terminarEjecucion("<div align='center'>Usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.</div>");
}
else
{
	global $conex;

// 2013-05-21
// SE COMENTA PORQUE EL ESCANER DE PACIENTES DESDE UNIX YA SE VA A HACER POR MEDIO DE UN CRON
/*
	// Consulta de pacientes activos en cl?nica
	$q = "SELECT DISTINCT
		a.Fecha_data fing, a.Hora_data hing, Ubihis, Pactid, Pacced, Pacno1, Pacno2, Pacap1, Pacap2, Ingres, Ingnre, Ubiing
	FROM
		".$basedatos."_000018 a, ".$basedatos."_000016, root_000036, root_000037
	WHERE
			Ubimue != 'on'
		AND Ubiald != 'on'
		AND Ubihis = Inghis
		AND Ubiing = Inging
		AND Ubihis = Orihis
		AND Oriori = '".$wemp_pmla."'
		AND Oriced = Pacced
		AND Oritid = Pactid
	GROUP BY Ubihis
	ORDER BY fing DESC, hing DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	$listados = array();
	$i=0;
	while ($i<$num)
	{
		// Arreglo para guardar las historias de los pacientes activos en urgencias
		$listados[$i] = $row['Ubihis']."-".$row['Ubiing'];
		// Arreglo para guardar los ingresos de los pacientes activos en urgencias
		//$listadosing[$i] = $row['Ubiing'];
		$i++;
		$row = mysql_fetch_array($res);
	}

	// Borra los registros antiguos en la tabla de log
	borrarLogsAntiguos("log_agenda",15);

	// Trae desde Unix todos los pacientes activos que deben ser ingresados a Matrix
	actualizarPacientesUnix($basedatos,$basedatoshce,$wemp_pmla,$seguridad,$listados);
*/


	/**********************************************************************************
	 ******* INICIA SECCI?N DEL LISTADO DE PACIENTES ACTIVOS HOY **********************
	 *********************************************************************************/

	// --> Obtener maestro de clasificaciones: Jerson trujillo 2015-08-19
	$wbasedatoCliame 		= consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$clasificacionGenDefecto= '';
	$salaGenDefecto			= '';
	$arrayClasificaciones 	= array();
	$sqlObtenerClasific 	= "
	SELECT Clacod, Clanom, Clasal, Cladef
	  FROM ".$wbasedatoCliame."_000246
	 WHERE Claest = 'on'
	 ORDER BY Clanom
	";
	$resObtenerClasific = mysql_query($sqlObtenerClasific, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerClasific):</b><br>".mysql_error());
	while($rowObtenerClasific = mysql_fetch_array($resObtenerClasific))
	{
		$arrayClasificaciones[$rowObtenerClasific['Clacod']]['nombre']		= $rowObtenerClasific['Clanom'];
		$arrayClasificaciones[$rowObtenerClasific['Clacod']]['salaDefec']	= $rowObtenerClasific['Clasal'];
		// --> Obtener la clasificacion y sala por defecto.
		if($rowObtenerClasific['Cladef'] == 'on')
		{
			$clasificacionGenDefecto	= $rowObtenerClasific['Clacod'];
			$salaGenDefecto				= $rowObtenerClasific['Clasal'];
		}
	}

    $ccoUr = consultarCentrocoUrgencias($basedatos, $selectsede);

	// --> Obtener maestro de salas de espera: Jerson trujillo 2015-07-21
	$arraySalasEspera = array();
	$sqlObtenerSalas = "
	SELECT Salcod, Salnom, Salcap, Salaps
	  FROM ".$basedatos."_000182
	 WHERE Salest = 'on' AND Salcco = '".$ccoUr->codigo."'
	 ORDER BY Salpri
	";
	$resObtenerSalas = mysql_query($sqlObtenerSalas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerSalas):</b><br>".mysql_error());
	while($rowObtenerSalas = mysql_fetch_array($resObtenerSalas))
	{
		$arraySalasEspera[$rowObtenerSalas['Salcod']]['Nombre'] 	= $rowObtenerSalas['Salnom'];
		$arraySalasEspera[$rowObtenerSalas['Salcod']]['Capacidad'] 	= (($rowObtenerSalas['Salcap'] != '') ? $rowObtenerSalas['Salcap'] : 0);

		if($rowObtenerSalas['Salaps'] == 'on')
			$salaDefectoPorSuperacion = $rowObtenerSalas['Salcod'];
	}

	// Consulta de pacientes activos en urgencias
	$q = "SELECT DISTINCT
		a.Fecha_data fing, a.Hora_data hing, Ubihis, Pactid, Pacced, Pacno1, Pacno2, Pacap1, Pacap2, Ingres, Ingnre, Ubiing, b.Fecha_data fingb, b.Hora_data hingb, Mtrtri, Ubisac,
		Mtrsal, Mtrtur, Mtrgme, TURNOS.Atusea, TURNOS.Atucla, Mtraut
	FROM
		".$basedatos."_000018 a, ".$basedatos."_000016, root_000036, root_000037, ".$basedatoshce."_000022 b LEFT JOIN ".$basedatos."_000178 AS TURNOS ON b.Mtrtur = TURNOS.Atutur
	WHERE
		Ubisac = '".$ccoCodigo."'
		AND Ubimue != 'on'
		AND Ubiald != 'on'
		AND Ubihis = Inghis
		AND Ubiing = Inging
		AND Ubihis = Orihis
		AND Oriori = '".$wemp_pmla."'
		AND Oriced = Pacced
		AND Oritid = Pactid
		AND Ubihis = Mtrhis
		AND Ubiing = Mtring
	GROUP BY Ubihis
	ORDER BY fing DESC, hing DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$i=1;

		$array_zonas = array();

		$q_zon = "SELECT *
					FROM ".$basedatos."_000169
				   WHERE Areest = 'on'" ;
		$res_zon = mysql_query($q_zon);

		while($row_zon = mysql_fetch_assoc($res_zon)){

			if(!array_key_exists($row_zon['Arecod'], $array_zonas)){

				$array_zonas[$row_zon['Arecod']] =  $row_zon;

			}

		}

		$datos_his_cub = array();

		$q_cub = "SELECT habcod, habhis, habing, habcpa, habzon
					FROM ".$basedatos."_000020
				   WHERE Habcub = 'on'" ;
		$res_cub = mysql_query($q_cub, $conex);

		while($row_cub = mysql_fetch_assoc($res_cub)){

			if(!array_key_exists(trim($row_cub['habhis'].$row_cub['habing']), $datos_his_cub)){

				$datos_his_cub[$row_cub['habhis'].$row_cub['habing']] =  $row_cub;

			}

		}

		// --> Convenciones
		echo "
		<table width='100%' style='font-size:11px;'>
			<tr>
				<td width='80%'></td><td style='border:1px solid #CECECE;' align='center'>Convenciones</span></td>
			</tr>
			<tr>
				<td width='80%'></td><td style='border:1px solid #7DA554;background-color:#D7F4BA;' align='center'>
					<img width='14px' height='14px' src='../../images/medical/sgc/Mensaje_alerta.png'>
					Superaci?n de la capacidad de la sala <span></span>
				</td>
			</tr>
			<tr>
				<td width='80%'></td><td style='border:1px solid #7DA554;background-color:#ECB782;' align='center'>
					<img width='14px' height='14px' src='../../images/medical/sgc/Mensaje_alerta.png'>				
					Atenci?n sin autorizar <span></span>
				</td>
			</tr>
		</table><br>";
		echo "<table border=0 cellspacing=2 cellpadding=0 align=center>";

		//Titulo lista de pacientes
		echo "<tr class='fila1'>";
		echo "<td colspan=15 align='center'><strong> &nbsp; PACIENTES ACTIVOS &nbsp; </strong></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=9>&nbsp;</td>";
		echo "</tr>";
		if($num>0) {
			echo "<tr>";
			echo "<td colspan='9' class='textoMedio'><strong>N&uacute;mero de pacientes: ".$num."</strong></td>";
			echo "</tr>";
		}

		//Encabezado lista de pacientes
		echo "<tr class='encabezadoTabla'>";
		echo "<td rowspan='2' align=center>Turno</td>";
		echo "<td colspan='2' align=center>Ingreso Unix</td>";
		echo "<td rowspan='2' align=center>&nbsp;Historia&nbsp;</td>";
		echo "<td rowspan='2' align=center>&nbsp;Paciente&nbsp;</td>";
		echo "<td rowspan='2' align=center>&nbsp;M&eacute;dico&nbsp;</td>";
		echo "<td rowspan='2' align=center>&nbsp;Especialidad&nbsp;</td>";
		echo "<td rowspan='2' align=center>Genero Medico</td>";
		echo "<td rowspan='2' align=center>&nbsp;Conducta&nbsp;</td>";
		echo "<td rowspan='2' align=center>&nbsp;Nivel de<br />Triage&nbsp;</td>";
		echo "<td rowspan='2' align=center>&nbsp;Atenci?n<br>Autorizada&nbsp;</td>";
		echo "<td rowspan='2' align=center>&nbsp;Ubicaci?n</td>";
		echo "<td rowspan='2' align=center>&nbsp;Afinidad&nbsp;</td>";
		echo "<td rowspan='2' align=center>&nbsp;Clasificaci?n&nbsp;</td>";
		echo "<td rowspan='2' align=center>Sala de espera</td>";
		echo "<td rowspan='2' align=center>&nbsp;Alta&nbsp;</td>";
		echo "<td rowspan='2' align=center>&nbsp;Muerte&nbsp;</td>";
		echo "</tr>";
		echo "
			<tr class='encabezadoTabla'>
				<td align=center>Fecha</td>
				<td align=center>Hora</td>
			</tr>";

		//$medicos = consultarMedicosUrgencias($basedatos);
		//$cantidadMedicos = count($medicos);

		$especialidades 		= consultarEspecialidades($basedatos,$esUrgencias);
		$cantidadEspecialidades = count($especialidades);
		//Ciclo para recorrer todos los registros de la consulta
		while ($i<=$num)
		{
			$mostraAlertaSuperacion	= false;
			$atencion_solo_lectura = "";
			$atencion_autorizada = "";
			$class_atencion_aut = "";
			
			if (is_int ($i/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila

			$wcod_cubiculo = "";
			$wdes_cubiculo = "";
			$zona = "";

			// Variable que me define si inactivo o no el select de m?dico y los checkbox de alta y muerte
			$inacselect = '';
			$inacselectalt = '';
			$inacselectmue = '';

			// 2012-12-04
			// Consulto si el paciente tiene m?dico tratante asociado
			$qmed =	 " SELECT Mtrcur, Meduma, Medno1, Medno2, Medap1, Medap2 "
					." FROM ".$basedatoshce."_000022, ".$basedatos."_000048 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtrmed != '' "
					." AND Mtrmed != 'NO APLICA' "
					." AND Mtrmed = Meduma "
			//		." AND Medseu LIKE '%".$servicio."%' "
					." AND Medest = 'on' ";
			$resmed = mysql_query($qmed, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmed . " - " . mysql_error());
			$rowmed = mysql_fetch_array($resmed);

			// Consulto si el paciente tiene especialidad asociada
			$qmtr =	 " SELECT Mtrhis, Mtring, Espcod, Mtrcur "
					." FROM ".$basedatoshce."_000022, ".$basedatos."_000044 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtreme != '' "
					." AND Mtreme != 'NO APLICA' "
					." AND Mtreme = Espcod "
					." AND Mtrest = 'on' ";

			$resmtr = mysql_query($qmtr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmtr . " - " . mysql_error());
			$rowmtr = mysql_fetch_array($resmtr);

			// Si Mtrcur en on el paciente est? siendo atendido, inactivo fila
			if($rowmtr['Mtrcur'] && $rowmtr['Mtrcur']=='on')
			{
				$wcf = 'fondoAmarillo';
				$inacselect = ' disabled';
				$inacselectalt = ' disabled';
				$inacselectmue = ' disabled';
			}

			// Consulto si el paciente tiene conducta asociada
			$qcon =	 " SELECT Condes, Conadm, Conalt, Conmue "
					." FROM ".$basedatoshce."_000022, ".$basedatoshce."_000035 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtrcon = Concod ";

			$rescon = mysql_query($qcon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcon . " - " . mysql_error());
			$rowcon = mysql_fetch_array($rescon);

			// Si tiene condcuta que no sea de admisi?n, inactivo fila
			if($rowcon['Condes'] && $rowcon['Condes']!='' && $rowcon['Conadm']!='on')
			{
				$wcf = 'fondoAmarillo';
				$inacselect = ' disabled';
				$inacselectalt = ' disabled';
				$inacselectmue = ' disabled';
			}
			else
			{
				if($rowcon['Conalt']=='on')
				{
					$inacselectmue = ' disabled';
					$inacselect = ' disabled';
				}

				if($rowcon['Conmue']=='on')
				{
					$inacselectalt = ' disabled';
					$inacselect = ' disabled';

				}
			}

			$auxres = explode('-',$row['Ingres']);
			//Se imprime los valores de cada fila
			echo "<tr class=".$wcf.">";
			echo "<td align='center'><b>".substr($row['Mtrtur'], 4)."</b></td>";
			echo "<td align=left>&nbsp;".$row['fing']."&nbsp;</td>";
			echo "<td align=left>&nbsp;".$row['hing']."&nbsp;</td>";
			echo "<td align=left>";
			echo "<span id='wide".$i."' title='Identificaci&oacute;n: <br>".$row['Pactid']." ".$row['Pacced']."<br><br>Responsable: <br>".$row['Ingnre']." <br>Cod. ".$row['Ingres']."'>";
			echo $row['Ubihis']."-".$row['Ubiing'];
			echo "</span></td>";
			echo "<td align=left>&nbsp;".$row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2']."&nbsp;</td>";

			// 2012-12-04
			// Columna de m?dico
			echo "<td align=left>";
			echo "<input type='text' name='wmedico".$i."' id='wmedico".$i."' value='".$rowmed['Medno1']." ".$rowmed['Medno2']." ".$rowmed['Medap1']." ".$rowmed['Medap2']."' readonly>";
			echo "</td>";

			// Columna de especialidad
			echo "<td align=left>";
			echo "<select name='wespecialidad".$i."' id='wespecialidad".$i."' onchange='asignarEspecialidad(".$i.", \"".$row['Mtrtur']."\")'".$inacselect.">";
			echo "<option value='0'> -- seleccione -- </option>";
			foreach ($especialidades as $especialidad)
			{
				if($rowmtr && $especialidad->codigo==$rowmtr['Espcod'])
					echo "<option value=".$especialidad->codigo."-".$especialidad->triage_automatico." selected>".$especialidad->nombre."</option>";
				else
					echo "<option value=".$especialidad->codigo."-".$especialidad->triage_automatico.">".$especialidad->nombre."</option>";
			}
			"</select>";
			echo "<input type='hidden' name='wpaciente".$i."' id='wpaciente".$i."' value='".$row['Ubihis']."'>";
			echo "<input type='hidden' name='wingreso".$i."' id='wingreso".$i."' value='".$row['Ubiing']."'>";
			echo "<input type='hidden' name='wservicio".$i."' id='wservicio".$i."' value='".$row['Ubisac']."'>";

			$zona          = (array_key_exists($row['Mtrsal'], $array_zonas)) ? $array_zonas[$row['Mtrsal']]['Aredes']:''; //Variable que contiene la zona asociada al paciente.
			$wcod_cubiculo = (array_key_exists($row['Ubihis'].$row['Ubiing'], $datos_his_cub)) ? $datos_his_cub[$row['Ubihis'].$row['Ubiing']]['habcod']:''; //Codigo de la ubicacion.
			$wdes_cubiculo = (array_key_exists($row['Ubihis'].$row['Ubiing'], $datos_his_cub)) ? $datos_his_cub[$row['Ubihis'].$row['Ubiing']]['habcpa']:''; //Descripcion de la ubicacion.

			echo "<input type='hidden' name='wcubiculo_ocupado".$i."' id='wcubiculo_ocupado".$i."' value='".$wcod_cubiculo."'>";
			echo "</td>";

			// --> Genero medico
			echo "
			<td>
				<select id='generoMedicoAtender".$i."' onChange='asignarGeneroMedicoQueDebeAtender($i,\"".$row['Ubihis']."\",\"".$row['Ubiing']."\")'>
					<option value=''  ".(($row['Mtrgme'] == '') ? "SELECTED" : "" )."></option>
					<option value='F' ".(($row['Mtrgme'] == 'F') ? "SELECTED" : "" ).">Femenino</option>
					<option value='M' ".(($row['Mtrgme'] == 'M') ? "SELECTED" : "" ).">Masculino</option>
					<option value='C' ".(($row['Mtrgme'] == 'C') ? "SELECTED" : "" ).">COVID19</option>
				</select>
			</td>";

			// Columna de conducta
			echo "<td align=left>";
			if($rowcon['Condes'] && $rowcon['Condes']!='')
				echo "<input type='text' name='wconducta".$i."' id='wconducta".$i."' style='text-align: center; font-weight: bold;' size='21' value='".$rowcon['Condes']."' readonly>";
			elseif($rowmtr['Mtrcur'] && $rowmtr['Mtrcur']=='on')
				echo "<input type='text' name='wconducta".$i."' id='wconducta".$i."' style='text-align: center; font-weight: bold;' size='21' value='En consulta' readonly>";
			else
				echo "<input type='text' name='wconducta".$i."' id='wconducta".$i."' style='text-align: center' size='24' value=' -- sin asignar -- ' readonly>";
			echo "</td>";


			// Consulto si el paciente tiene nivel de triage asociado
			if($row['Mtrtri'] && $row['Mtrtri']!="" && $row['Mtrtri']!=" ")
			{
				$qtri =	 " SELECT Tricod, Trinom	 "
						." FROM ".$basedatoshce."_000040 "
						." WHERE Tricod = '".$row['Mtrtri']."' "
						." AND Triest = 'on' ";
				$restri = mysql_query($qtri, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qtri . " - " . mysql_error());
				$rowtri = mysql_fetch_array($restri);
				$nivel_triage = $rowtri['Trinom'];
			}
			else
			{
				$nivel_triage = "";
			}

			// Columna de triage
			echo "<td id='".$row['Ubihis'].$row['Ubiing']."' align=center>&nbsp;".$nivel_triage."&nbsp;</td>";

			if($row['Mtraut']=='on'){

				$atencion_autorizada = "checked";

			}else{

				$class_atencion_aut = "background-color:ECB782;";

			}

			// Columna de atencion autorizada
			echo "<td id='".$row['Ubihis'].$row['Ubiing']."' class='atencion_aut_".$row['Ubihis'].$row['Ubiing']."' align=center style='".$class_atencion_aut."'>&nbsp;<input type='checkbox' class='atencion_autorizada' value='".$row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2']."' his_ing='".$row['Ubihis']."-".$row['Ubiing']."' ".$atencion_autorizada." onChange='atencionAutorizada(\"".$row['Ubihis']."\", \"".$row['Ubiing']."\", this)' ".$inacselect.">&nbsp;</td>";

			//Cubiculo
			echo "<td align=center>&nbsp;<b>".$zona."</b><br>".$wdes_cubiculo."&nbsp;</td>";

			// Columna de afinidad
			// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
			$wafin = clienteMagenta($row['Pacced'],$row['Pactid'],$wtpa,$wcolorpac);
			if ($wafin)
			 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			else
			  echo "<td> &nbsp; </td>";

			// --> Si no se le ha asignado ninguna clasificacion al turno.
			$clasificSelect = '';
			$salaEspeSelect = trim($row['Atusea']);
			if($row['Atucla'] == '' && $row['Mtrtur'] != '')
			{
				// --> Obtener la clasificacion por defecto de la entidad responsable.
				$sqlObtClasDef = "
				SELECT Empcla, Clasal
				  FROM ".$wbasedatoCliame."_000024, ".$wbasedatoCliame."_000246
				 WHERE Empcod = '".$row['Ingres']."'
				   AND Empcla = Clacod
				   AND Claest = 'on'
				";
				$resObtClasDef = mysql_query($sqlObtClasDef, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtClasDef):</b><br>".mysql_error());
				if($rowObtClasDef = mysql_fetch_array($resObtClasDef))
				{
					$clasificSelect = $rowObtClasDef['Empcla'];
					if($salaEspeSelect == '*' || $salaEspeSelect == '')
						$salaEspeSelect = $rowObtClasDef['Clasal'];
				}
				else
				{
					$clasificSelect = $clasificacionGenDefecto;
					$salaEspeSelect = $salaGenDefecto;
				}

				// --> 	Obtener el numero de pacientes que hay en la sala
				//		(Pendientes de triage y pendientes de consulta)
				$sqlNumPac = "
				SELECT COUNT(*) AS Cantidad
				  FROM ".$basedatos."_000178 AS A INNER JOIN ".$basedatoshce."_000022 AS B ON (A.Atutur = B.Mtrtur) 
				 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
				   AND Atusea = '".$salaEspeSelect."' 
				   AND Mtrest = 'on'
				   AND (Mtrftr = '0000-00-00' OR Mtrfco = '0000-00-00')
				   AND Mtrcur != 'on'
				";

				$resNumPac = mysql_query($sqlNumPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNumPac):</b><br>".mysql_error());
				$rowNumPac = mysql_fetch_array($resNumPac);

				// --> Si ya no hay mas capacidad de personas en la sala, se asigna el paciente en la sala por defecto
				if($rowNumPac['Cantidad'] >= $arraySalasEspera[$salaEspeSelect]['Capacidad'])
				{
					$nombreSalaDebiaEstar	= $arraySalasEspera[$salaEspeSelect]['Nombre'];
					$salaEspeSelect 		= (($salaDefectoPorSuperacion != '') ? $salaDefectoPorSuperacion : $salaEspeSelect);
					$mostraAlertaSuperacion	= true;
				}
				// --> Asignarle la clasificacion y la sala por defecto al paciente
				$sqlAsigCla = "
				UPDATE ".$basedatos."_000178
				   SET Atucla = '".$clasificSelect."',
					   Atusea = '".$salaEspeSelect."'
				 WHERE Atutur = '".$row['Mtrtur']."'
				";
				mysql_query($sqlAsigCla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAsigCla):</b><br>".mysql_error());
			}
			else
				$clasificSelect = $row['Atucla'];

			// --> Seleccionador de clasificacion: Jerson trujillo 2015-08-18
			echo "
				<td>
					<select id='selectClasificacion-".$row['Mtrtur']."' ".(($row['Mtrtur'] == '') ? "disabled='disabled'" : "")." onChange='asignarClasificacion(\"".$row['Mtrtur']."\");'>
						<option value=''></option>";
			foreach($arrayClasificaciones as $codClasif => $infoClasif)
				echo "	<option ".(($codClasif == $clasificSelect) ? "SELECTED" : "")." value='".$codClasif."' salaDefecto='".$infoClasif['salaDefec']."'>".$infoClasif['nombre']."</option>";
			echo "	</select>
				</td>";

			// --> Seleccionador de salas de espera: Jerson trujillo 2015-07-21
			if($mostraAlertaSuperacion)
				echo "<td class='tooltip' align='center' style='border:1px solid #7DA554;background-color:#D7F4BA;' title='La sala ".$nombreSalaDebiaEstar." se encuentra llena.<br>El paciente sera ubicado en la sala ".$arraySalasEspera[$salaEspeSelect]['Nombre']."'>
						<img blinkTope='si' width='22px' height='22px' src='../../images/medical/sgc/Mensaje_alerta.png'>";
			else
				echo "<td>";

			echo "	<select id='selectSalaDeEspera-".$row['Mtrtur']."' ".(($row['Mtrtur'] == '') ? "disabled='disabled'" : "")." onChange='asignarSalaEspera(\"".$row['Mtrtur']."\");'>
						<option value=''></option>";
			foreach($arraySalasEspera as $codSala => $infoSala)
				echo "	<option ".(($codSala == $salaEspeSelect) ? "SELECTED" : "")." value='".$codSala."'>".$infoSala['Nombre']."</option>";
			echo "	</select>
				</td>";

			echo "<td align=center>&nbsp;<input type='checkbox' onclick='altaPaciente(".$i.", \"".$row['Mtrtur']."\");' name='alta".$i."' id='alta".$i."' value='".$row['Ubihis']."'".$inacselectalt.">&nbsp;</td>";
			echo "<td align=center>&nbsp;<input type='checkbox' onclick='muertePaciente(".$i.");' name='muerte".$i."' id='muerte".$i."' value='".$row['Ubihis']."'".$inacselectmue.">&nbsp;</td>";
			echo "</tr>";

			//Obtengo la siguiente fila
			$row = mysql_fetch_array($res);
			$i++;
		}

		echo "</table>";
	}
	else
	{
		echo "<br /><p align='center'>No se encontraron pacientes activos</p><p>&nbsp;</p>";
	}

	echo "<p>&nbsp;</p>";

	/*******************************************************************************************
	 ******* INICIA SECCI?N DE PACIENTES INACTIVOS DADOS DE ALTA EN LOS ?LTIMOS 2 D?AS *********
	 *******************************************************************************************/

	$ayer = date("Y-m-d",time()-86400);
	$hoy = date("Y-m-d");

	// Consulta de pacientes inactivos dados de alta en los ?ltimos 2 d?as
	$q = "SELECT DISTINCT
		a.Fecha_data fing, a.Hora_data hing, Ubihis, Pactid, Pacced, Pacno1, Pacno2, Pacap1, Pacap2, Ingres, Ingnre, Ubiing, Ubimue, Ubifad, Ubihad, Ubiuad
	FROM
		".$basedatos."_000018 a, ".$basedatos."_000016, root_000036, root_000037, ".$basedatoshce."_000022 b
	WHERE
		Ubisac = '".$ccoCodigo."'
		AND Ubifad >= '".$ayer."'
		AND Ubifad != '0000-00-00'
		AND Ubiald = 'on'
		AND Ubihis = Inghis
		AND Ubiing = Inging
		AND Ubihis = Orihis
		AND Oriori = '".$wemp_pmla."'
		AND Oriced = Pacced
		AND Oritid = Pactid
		AND Ubihis = Mtrhis
		AND Ubiing = Mtring
	ORDER BY Ubifad DESC, Ubihad DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	echo "<table border=0 cellspacing=2 cellpadding=0 align=center>";

	//Titulo lista de pacientes
	echo "<tr class='fila1'>";
	echo "<td colspan=9 align='center'><strong> &nbsp; PACIENTES DADOS DE ALTA EN LOS ?LTIMOS 2 D?AS &nbsp; </strong></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan=8>&nbsp;</td>";
	echo "</tr>";
	if($num>0) {
		echo "<tr>";
		echo "<td colspan='8' class='textoMedio'><strong>N&uacute;mero de pacientes: ".$num."</strong></td>";
		echo "</tr>";
	}

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$auxi = $i;
		$i=1;

		//Encabezado lista de pacientes
		echo "<tr class='encabezadoTabla'>";
		echo "<td align=center>&nbsp;Fecha Ing.&nbsp;<br>&nbsp;Unix&nbsp;</td>";
		echo "<td align=center>&nbsp;Hora Ing.&nbsp;<br>&nbsp;Unix&nbsp;</td>";
		echo "<td align=center>&nbsp;Historia&nbsp;</td>";
		echo "<td align=center>&nbsp;Paciente&nbsp;</td>";
		echo "<td align=center>&nbsp;M&eacute;dico&nbsp;</td>";
		echo "<td align=center>&nbsp;Conducta&nbsp;</td>";
		echo "<td align=center>&nbsp;Afinidad&nbsp;</td>";
		echo "<td align=center>&nbsp;Activar&nbsp;</td>";
		echo "<td align=center>&nbsp;Reasignar&nbsp;<br>&nbsp;Historia&nbsp;</td>";
		echo "</tr>";

		//Ciclo para recorrer todos los registros de la consulta
		while ($i<=$num)
		{

			if (is_int ($i/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila

			// Consulto si el paciente tiene m?dico tratante asociado
			$qmtr =	 " SELECT Mtrhis, Mtring, Meduma, Medno1, Medno2, Medap1, Medap2 "
					." FROM ".$basedatoshce."_000022, ".$basedatos."_000048 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtrmed != '' "
					." AND Mtrmed != 'NO APLICA' "
					." AND Mtrmed = Meduma "
					." AND Medurg = 'on' "
					." AND Medest = 'on' ";

			$resmtr = mysql_query($qmtr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmtr . " - " . mysql_error());
			$rowmtr = mysql_fetch_array($resmtr);

			// Consulto si el paciente tiene conducta asociada
			$qcon =	 " SELECT Condes "
					." FROM ".$basedatoshce."_000022, ".$basedatoshce."_000035 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtrcon = Concod ";

			$rescon = mysql_query($qcon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcon . " - " . mysql_error());
			$rowcon = mysql_fetch_array($rescon);

			// Consulto el usuario que di? de alta al paciente
			$quad =	 " SELECT Codigo, Descripcion "
					." FROM usuarios "
					." WHERE Codigo = '".$row['Ubiuad']."'";

			$resuad = mysql_query($quad, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $quad . " - " . mysql_error());
			$rowuad = mysql_fetch_array($resuad);

			$auxres = explode('-',$row['Ingres']);
			//Se imprime los valores de cada fila
			echo "<tr class=".$wcf.">";
			echo "<td align=left>&nbsp;".$row['fing']."&nbsp;</td>";
			echo "<td align=left>&nbsp;".$row['hing']."&nbsp;</td>";
			echo "<td align=left>";
			echo "<span id='wide".$auxi."' title='Identificaci&oacute;n: <br>".$row['Pactid']." ".$row['Pacced']."<br><br>Responsable: <br>".$row['Ingnre']." <br>Cod. ".$row['Ingres']."<br><br>Fecha alta: ".$row['Ubifad']." <br>Hora alta: ".$row['Ubihad']."<br>Di&oacute; de alta: ".$rowuad['Descripcion']."'>";
			echo "&nbsp;".$row['Ubihis']." - ".$row['Ubiing']."&nbsp;";
			echo "</span></td>";
			echo "<td align=left>&nbsp;".$row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2']."&nbsp;</td>";
			echo "<input type='hidden' name='wpaciente".$auxi."' id='wpaciente".$auxi."' value='".$row['Ubihis']."'>";
			echo "<input type='hidden' name='wingreso".$auxi."' id='wingreso".$auxi."' value='".$row['Ubiing']."'>";
			// Columna de m?dico
			echo "<td align=left>&nbsp;".$rowmtr['Medno1']." ".$rowmtr['Medno2']." ".$rowmtr['Medap1']." ".$rowmtr['Medap2']."&nbsp;</td>";
			// Columna de conducta
			echo "<td align=center>&nbsp;".$rowcon['Condes']."&nbsp;</td>";

			// Columna de afinidad
			// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
			$wafin = clienteMagenta($row['Pacced'],$row['Pactid'],$wtpa,$wcolorpac);
			if ($wafin)
			 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			else
			  echo "<td> &nbsp; </td>";

			// Columna de reactivaci?n de pacientes
			echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:activarPaciente(".$auxi.");' name='activar".$auxi."' id='activar".$auxi."' value='".$row['Ubihis']."'>&nbsp;</td>";
			// Columna de reasignaci?n de pacientes
			$desacReasignar = '';
			if($row['Ubiing']>1)
				$desacReasignar = 'disabled';
			echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:reasignarPaciente(".$auxi.");' name='reasignar".$auxi."' id='reasignar".$auxi."' value='".$row['Ubihis']."' ".$desacReasignar.">&nbsp;</td>";
			echo "</tr>";

			//Obtengo la siguiente fila
			$row = mysql_fetch_array($res);
			$i++;
			$auxi++;
		}

	}
	else
	{
		echo "<tr>";
		echo "<td colspan=8><p align='center'>No se encontraron pacientes dados de alta en los ?ltimos 2 d?as</p></td>";
		echo "</tr>";
	}

	echo "</table>";

  }

}

//ANTES DE INSERTAR UNA ALTA O UNA MUERTE PARA UN PACIENTE SE CONSULTA SI YA TUVO ALTA O MUERTE Y SE ELIMINAN
function BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $whis, $wing, $bandera)
{
	$user_session = explode('-',$_SESSION['user']);
	$seguridad = $user_session[1];

	$q = "	SELECT *
			FROM ".$wbasedato."_000033
			WHERE Historia_clinica = '".$whis."'
			AND Num_ingreso = '".$wing."'
			AND Tipo_egre_serv REGEXP 'MUERTE MAYOR A 48 HORAS|MUERTE MENOR A 48 HORAS|ALTA|MUERTE' ";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	$arregloDatos = array();

	if ($num > 0)
	{
		while($row = mysql_fetch_assoc($res))
		{
			$result = array();
			$result['fecha'] = $row['Fecha_data'];
			$result['cco'] = $row['Servicio'];
			$result['egreso'] = $row['Tipo_egre_serv'];
			array_push( $arregloDatos, $result );
		}
	}

	if( count( $arregloDatos )  > 0 )
	{

		foreach( $arregloDatos as $dato )
		{

			$wfecha = $dato['fecha'];
			$wccoCod = $dato['cco'];
			$wtipoEgresoABorrar = $dato['egreso'];

			$q = " SELECT * "
				."   FROM ".$wbasedato."_000038 "
				."  WHERE Fecha_data = '".$wfecha."'"
				."    AND Cieser = '".$wccoCod."'";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			$row = mysql_fetch_assoc($res);


			$existe_en_la_67 = false;
			$q67 = " SELECT * "
				."   FROM ".$wbasedato."_000067 "
				."  WHERE Fecha_data = '".$wfecha."'"
				."    AND Habhis = '".$whis."'"
				."    AND Habing = '".$wing."'";

			$res67 = mysql_query($q67,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num67 = mysql_num_rows($res67);
			if( $num67 > 0 ){
				$existe_en_la_67 = true;
			}

			$cant_egresos = $row['Cieegr'];
			$cant_camas_ocupadas = $row['Cieocu'];
			$cant_camas_disponibles = $row['Ciedis'];
			$muerteMayor = $row['Ciemmay'];
			$muerteMenor = $row['Ciemmen'];
			$egresosAlta = $row['Cieeal'];
			//Restamos uno al motivo de egreso que tenia el paciente

			if(preg_match('/ALTA/i',$wtipoEgresoABorrar))
			{
				$egresosAlta--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			else if(preg_match('/MAYOR/i',$wtipoEgresoABorrar)) //Muerte mayor
			{
				$muerteMayor--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			else if(preg_match('/MENOR/i',$wtipoEgresoABorrar))
			{ // Muerte menor
				$muerteMenor--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}

			$query_para_log = "	SELECT *
				FROM ".$wbasedato."_000033
				WHERE Historia_clinica = '".$whis."'
				AND Num_ingreso = '".$wing."'
				AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$registrosFila = obtenerRegistrosFila($query_para_log);

			$q ="	DELETE FROM ".$wbasedato."_000033
					 WHERE Historia_clinica = '".$whis."'
					   AND Num_ingreso = '".$wing."'
					   AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$res = mysql_query($q,$conex);

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{

				$q = " UPDATE ".$wbasedato."_000038 "
					."    SET Ciemmay = '".$muerteMayor."',"
					."  	  Ciemmen = '".$muerteMenor."',"
					."  	  Cieeal = '".$egresosAlta."',"
					."  	  Cieegr = '".$cant_egresos."',"
					."  	  Cieocu = '".$cant_camas_ocupadas."',"
					."  	  Ciedis = '".$cant_camas_disponibles."'"
					."  WHERE Fecha_data = '".$wfecha."'"
					."    AND Cieser = '".$wccoCod."'"
					."  LIMIT 1 ";

				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				//Guardo LOG de borrado en tabla movhos 33 - Activacion paciente
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."', '".$wing."', 'Borrado tabla ".$wbasedato."_000033', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
	}
}

// Retorna el c?digo del centro de costo en Matrix
function obtenerCcoMatrix($basedatos,$codCco,$wemp_pmla,$seguridad)
{
	global $conex;
	$cco = "";
	$conex = obtenerConexionBD("matrix");

	// Consulto si hay un c?digo asociado en la tabla 11 de movhos
	$qcco = "	SELECT Ccocod
				  FROM ".$basedatos."_000011
				 WHERE Ccoseu  = '".$codCco."'
				   AND Ccoing = 'on'
				   AND Ccoest = 'on'";
	$rescco = mysql_query($qcco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcco . " - " . mysql_error());
	$numcco = mysql_num_rows($rescco);
	if($numcco>0)
	{
		$rowcco = mysql_fetch_array($rescco);
		$cco = $rowcco['Ccocod'];
	}
	return $cco;
}

// Actualiza los datos de los pacientes en cl?nica
function actualizarDatosPacientes($basedatos,$wemp_pmla,$seguridad,$listados)
{
	global $conex;

	for($i=0;$i<count($listados);$i++)
	{
		$paciente = new pacienteDTO();

		// Obtengo historia cl?nica e ingreso de paciente
		$datos_paciente = explode("-",$listados[$i]);
		$whistoria = $datos_paciente[0];
		$wingreso = $datos_paciente[1];

		$paciente->historiaClinica  = $whistoria;

		$pacienteUnix = consultarPacienteUnix($paciente);  //Paciente Unix
		if(isset($pacienteUnix->nombre1) && isset($pacienteUnix->ingresoHistoriaClinica))
		{
			// Consulta si existe el paciente en las tablas root_000036, root_000037
			// Con base en histora y ?ltimo ingreso
			$pacienteMatrix = consultarInfoPacientePorHistoria($conex, $paciente->historiaClinica,$wemp_pmla);

			$pacienteEnTablaUnica = false;	// Indica si tipo identificacion e identificaci?n existen en tabla root_000036
			$pacienteEnTablaIngresos = false;	// Indica si historia y origen existen en tabla root_000037

			// En tabla root_000037 borro historia asociada en matrix a la c?dula de unix
			// siempre y cuando esta historia sea diferente a la asociada en Unix
			borrarHistoriaDiferenteUnix($pacienteUnix, $wemp_pmla, $seguridad);

			if(isset($pacienteMatrix->documentoIdentidad))
			{
				// Se comenta porque no es necesario usar las funciones que usan esta variable
				// 2011-11-29
				//if($pacienteMatrix->documentoIdentidad != $pacienteUnix->documentoIdentidad || $pacienteMatrix->tipoDocumentoIdentidad != //$pacienteUnix->tipoDocumentoIdentidad)
				//{
					//$mismoDocumentoIdentidad = false;
				//}
			}
			else
			{
				$pacienteMatrix->historiaClinica = $whistoria;
				$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
				$pacienteMatrix->documentoIdentidad = $pacienteUnix->documentoIdentidad;
				$pacienteMatrix->tipoDocumentoIdentidad = $pacienteUnix->tipoDocumentoIdentidad;
			}

			// Consulto por historia y origen si existe en tabla root_000037
			$pacienteEnTablaIngresos = existeEnTablaIngresos($pacienteUnix, $wemp_pmla);
			// Consulto por tipo identificacion e identificaci?n si existe en tabla root_000036
			$pacienteEnTablaUnica = existeEnTablaUnicaPacientes($pacienteUnix);

			//Ingreso de datos en tabla 36 de root
			if(!$pacienteEnTablaUnica)
			{
				insertarPacienteTablaUnica($pacienteUnix,$seguridad);
			}
			else
			{
				actualizarDatosPacienteTablaUnica($pacienteMatrix,$pacienteUnix);
			}

			//Ingreso de datos en tabla 37 de root
			if(!$pacienteEnTablaIngresos)
			{
				insertarIngresoPaciente($pacienteUnix, $wemp_pmla, $seguridad);
			}
			else
			{
				actualizarIngresoPaciente($pacienteMatrix, $pacienteUnix, $wemp_pmla);
			}

		}

	}
}

// Ingresa un paciente a la lista de pacientes en urgencias
function ingresarPacientesUrgencias($basedatos,$basedatoshce,$codCco,$whistoria,$wemp_pmla,$seguridad)
{
	global $conex;

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	// Grabaci?n de ingreso de paciente

	$conex = obtenerConexionBD("matrix");

	$paciente = new pacienteDTO();

	$paciente->historiaClinica = $whistoria;

	$pacienteUnix = consultarPacienteUnix($paciente);  //Paciente Unix
	if(isset($pacienteUnix->nombre1) && isset($pacienteUnix->ingresoHistoriaClinica))
	{
		// Consulta si existe el paciente en las tablas root_000036, root_000037 y movhos_000018
		// Con base en histora y ?ltimo ingreso
		$pacienteMatrix = consultarInfoPacientePorHistoria($conex, $paciente->historiaClinica,$wemp_pmla);

		$ingresoAnterior = "";

		if(!$pacienteMatrix || !isset($pacienteMatrix->historiaClinica))
		{
			$pacienteMatrix = $pacienteUnix;
		}
		else
		{
			if(isset($pacienteMatrix->ingresoHistoriaClinica))
			{
				$ingresoAnterior = $pacienteMatrix->ingresoHistoriaClinica;
			}
			else
			{
				$pacienteMatrix->ingresoHistoriaClinica = $ingresoAnterior;
			}

			if($pacienteUnix)
			{
				// Si el ingreso encontrado en Matrix (movhos_000018) es menor
				if($pacienteMatrix->ingresoHistoriaClinica=="")
				{
					$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
				}
				elseif((($pacienteMatrix->ingresoHistoriaClinica*1)) < (($pacienteUnix->ingresoHistoriaClinica)*1))
				{
					if(isset($pacienteMatrix->historiaClinica) && !empty($pacienteMatrix->historiaClinica))
					{
						// Consulto si el ingreso no tiene alta definitiva
						$qalt = "	SELECT Ubiald
									FROM ".$basedatos."_000018
									WHERE Ubihis  = '".$pacienteMatrix->historiaClinica."'
									AND	  Ubiing = '".$pacienteMatrix->ingresoHistoriaClinica."'
									AND	  Ubiald != 'on'";
						$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
						$numalt = mysql_num_rows($resalt);
						// Define si le doy de alta o no 1=no tiene alta 0=tiene alta
						// Si no tiene alta definitiva le doy de alta
						if($numalt>0)
							@altaPacienteUrgencias($basedatos,$basedatoshce,$pacienteMatrix->historiaClinica,$pacienteMatrix->ingresoHistoriaClinica,$wemp_pmla,"Movhos","Ingreso");

						$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
						//$pacienteMatrix->nombre1 = "";
					}
				}
				elseif((($pacienteMatrix->ingresoHistoriaClinica)*1) >= (($pacienteUnix->ingresoHistoriaClinica)*1))
				{
					if(isset($pacienteMatrix->historiaClinica) && !empty($pacienteMatrix->historiaClinica))
					{
						borraIngresosMayores($basedatos,$basedatoshce,$pacienteMatrix->historiaClinica,$pacienteUnix->ingresoHistoriaClinica,$wemp_pmla,"Movhos","BorradoIngresoMayor",$pacienteUnix->fechaIngreso,$pacienteUnix->horaIngreso);

						$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
						//$pacienteMatrix->nombre1 = "";
					}
				}
			}
		}

		// Si ya se encuentra admitido va a consultar la informaci?n
		// La guia es la tabla 18, si esta ahi, YA SE CONSIDERA INGRESADO
		if(!isset($pacienteMatrix->historiaClinica) || empty($pacienteMatrix->historiaClinica))
		{
			$pacienteMatrix->historiaClinica = $pacienteUnix->historiaClinica;
		}

		$pacienteConResponsablePaciente = false;	// Indica si historia e ingreso existen en tabla movhos_000016
		// Consulto por historia e ingreso si existe en tabla movhos_000016
		$pacienteConResponsablePaciente = existeEnTablaResponsables($pacienteUnix, $wemp_pmla);

		//Ingreso de datos en tabla 16 de movhos
		if(!$pacienteConResponsablePaciente)
		{
			insertarResponsablePaciente($pacienteUnix, $wemp_pmla, $seguridad);
		}
		else
		{
			actualizarResponsablePaciente($pacienteMatrix, $pacienteUnix);
		}

		// Busca por historia e ingreso en la tabla movhos_000018 si existen registros
		$pacienteIngresado = pacienteIngresado($pacienteMatrix);
		// Busca por historia e ingreso en la tabla hce_000022 si existen registros
		$pacienteIngresadoHce = pacienteIngresadoHce($pacienteMatrix);


		if(!isset($pacienteMatrix->ingresoHistoriaClinica) or !$pacienteIngresado or !$pacienteIngresadoHce)
		{
			if(!isset($pacienteMatrix->ingresoHistoriaClinica) or !$pacienteIngresado)
			{
				$pacienteEnTablaUnica = false;	// Indica si tipo identificacion e identificaci?n existen en tabla root_000036
				$pacienteEnTablaIngresos = false;	// Indica si historia y origen existen en tabla root_000037

				// Se comenta porque no es necesario usar las funciones que usan esta variable - 2011-11-29
				//$mismoDocumentoIdentidad = true;	// Indica si paciente unix y paciente matrix tienen el mismo documento

				// En tabla root_000037 borro historia asociada en matrix a la c?dula de unix
				// siempre y cuando esta historia sea diferente a la asociada en Unix
				borrarHistoriaDiferenteUnix($pacienteUnix, $wemp_pmla, $seguridad);

				// Si exite documento de identidad en matrix verifico que sea el mismo de Unix
				if(isset($pacienteMatrix->documentoIdentidad))
				{
					// Se comenta porque no es necesario usar las funciones que usan esta variable
					/* 2011-11-29
					if($pacienteMatrix->documentoIdentidad != $pacienteUnix->documentoIdentidad || $pacienteMatrix->tipoDocumentoIdentidad != $pacienteUnix->tipoDocumentoIdentidad)
					{
						$mismoDocumentoIdentidad = false;
					}
					*/
				}
				else
				{
					$pacienteMatrix->historiaClinica = $whistoria;
					$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
					$pacienteMatrix->documentoIdentidad = $pacienteUnix->documentoIdentidad;
					$pacienteMatrix->tipoDocumentoIdentidad = $pacienteUnix->tipoDocumentoIdentidad;
				}

				// Consulto por historia y origen si existe en tabla root_000037
				$pacienteEnTablaIngresos = existeEnTablaIngresos($pacienteUnix, $wemp_pmla);
				// Consulto por tipo identificacion e identificaci?n si existe en tabla root_000036
				$pacienteEnTablaUnica = existeEnTablaUnicaPacientes($pacienteUnix);

				// Actualiza documento de identidad en tabla root_000037
				// Se comenta porque la actuailzaci?n de documento en tabla root_000037 se realiza en las siguientes funciones
				/* 2011-11-29
				if(!$mismoDocumentoIdentidad)
					actualizarDocumentoPacienteTablaIngresos($pacienteMatrix,$pacienteUnix,$wemp_pmla);
				*/

				// Actualiza documento de identidad en tabla root_000036
				// Se comenta porque la actuailzaci?n de documento en tabla root_000036 se realiza en las siguientes funciones
				/* 2011-11-29
				if(!$mismoDocumentoIdentidad)
					actualizarDocumentoPacienteTablaUnica($pacienteMatrix,$pacienteUnix);
				*/

				//Ingreso de datos en tabla 36 de root
				if(!$pacienteEnTablaUnica)
				{
					insertarPacienteTablaUnica($pacienteUnix,$seguridad);
				}
				else
				{
					actualizarDatosPacienteTablaUnica($pacienteMatrix,$pacienteUnix);
				}

				//Ingreso de datos en tabla 37 de root
				if(!$pacienteEnTablaIngresos)
				{
					insertarIngresoPaciente($pacienteUnix, $wemp_pmla, $seguridad);
				}
				else
				{
					//$pacienteMatrix->ingresoHistoriaClinica = $ingresoAnterior;
					actualizarIngresoPaciente($pacienteMatrix, $pacienteUnix, $wemp_pmla);
					$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
				}
			}

			//Proceso de movimiento hospitalario
			$ingresoPaciente = new ingresoPacientesDTO();

			$ingresoPaciente->historiaClinica = $pacienteUnix->historiaClinica;
			$ingresoPaciente->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
			$ingresoPaciente->servicioActual = $codCco;
			$ingresoPaciente->habitacionActual = "";

			$ingresoPaciente->fechaIngreso = $pacienteUnix->fechaIngreso;
			$ingresoPaciente->horaIngreso = $pacienteUnix->horaIngreso;

			$ingresoPaciente->fechaAltaProceso = "0000-00-00";
			$ingresoPaciente->horaAltaProceso = "00:00:00";
			$ingresoPaciente->fechaAltaDefinitiva = "0000-00-00";
			$ingresoPaciente->horaAltaDefinitiva = "00:00:00";

			if(esCirugiaUnificada($codCco, $basedatos))
				$ingresoPaciente->enProcesoTraslado = "on";
			else
				$ingresoPaciente->enProcesoTraslado = "off";
			$ingresoPaciente->altaDefinitiva = "off";
			$ingresoPaciente->altaEnProceso = "off";

			$ingresoPaciente->usuario = "A-".$basedatos;

			//Grabar ingreso paciente
			$ingresoPaciente->servicioAnterior = "";
			$ingresoPaciente->habitacionAnterior = "";

			if(!$pacienteIngresado or !$pacienteIngresadoHce)
			{
				if(!$pacienteIngresado)
					grabarIngresoPaciente($ingresoPaciente, $seguridad);

				if(!$pacienteIngresadoHce)
					registrarIngresoPaciente($ingresoPaciente, $seguridad);

				return 'ok';
			}
			else
			{
				return 'existente';
			}

		}
		else
		{
			return 'existente';
		}
	}
	else
	{
		return 'no-urg';
	}
}

	// Llamado a las funciones seg?n el par?metro pasado por medio de ajax
	switch($consultaAjax)
	{
		case 10:
			echo asignarEspecialidadUrgencias($basedatos,$basedatoshce,$especialidad,$paciente,$ingreso,$servicio,$seguridad, $wemp_pmla, $wespec_triage_auto);
		break;
		case 11:
		{
			// --> 2015-12-21: Jerson Trujillo, La conexion a unix se realizar? solo en las funciones que lo necesite.
			conexionOdbc($conex, $basedatos, $conexUnix, 'facturacion');
			echo altaPacienteUrgencias($basedatos,$basedatoshce,$paciente,$ingreso,$wemp_pmla,$seguridad,"Manual", $wcubiculo_ocupado, $turno);
			liberarConexionOdbc($conexUnix);
		}
		break;
		case 12:
			echo muertePacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad, $wcubiculo_ocupado);
		break;
		case 13:
			echo agendaPacientesUrgencias($basedatos,$basedatoshce,$ccoCodigo,$esUrgencias,$wemp_pmla,$seguridad);
		break;
		/*
		// Se usaba para el ingreso de paciente manualmente por el campo de texto
		// Ver actualizaci?n de Abril 20 de 2011
		case 14:
			ingresarPacientesUrgencias($basedatos,$basedatoshce,$ccoCodigo,$whistoria,$conexUnix,$wemp_pmla,$seguridad);
		break;
		*/
		case 15:
		{
			// --> 2015-12-21: Jerson Trujillo, La conexion a unix se realizar? solo en las funciones que lo necesite.
			conexionOdbc($conex, $basedatos, $conexUnix, 'facturacion');

			echo activarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad);
			liberarConexionOdbc($conexUnix);
			break;
		}
		case 16:
			echo reasignarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$wemp_pmla,$seguridad);
		break;
		// --> 	Le asigna una sala de espera fisica a un paciente, relacionado por el turno.
		//		Jerson Trujillo, 2015-07-22.
		case 'asignarSalaEspera':
		{
			$mostraAlertaSuperacion	= false;

            $ccoUr = consultarCentrocoUrgencias($basedatos, $selectsede);

			// --> Obtener maestro de salas de espera
			$arraySalasEspera = array();
			$sqlObtenerSalas = "
			SELECT Salcod, Salnom, Salcap, Salaps
			  FROM ".$basedatos."_000182
			 WHERE Salest = 'on' AND Salcco = '".$ccoUr->codigo."'
			";

			$resObtenerSalas = mysql_query($sqlObtenerSalas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerSalas):</b><br>".mysql_error());
			while($rowObtenerSalas = mysql_fetch_array($resObtenerSalas))
			{
				$arraySalasEspera[$rowObtenerSalas['Salcod']]['Nombre'] 	= $rowObtenerSalas['Salnom'];
				$arraySalasEspera[$rowObtenerSalas['Salcod']]['Capacidad'] 	= (($rowObtenerSalas['Salcap'] != '') ? $rowObtenerSalas['Salcap'] : 0);

				if($rowObtenerSalas['Salaps'] == 'on')
					$salaDefectoPorSuperacion = $rowObtenerSalas['Salcod'];
			}

			// --> 	Obtener el numero de pacientes que hay en la sala
			//		(Pendientes de triage y pendientes de consulta)
			$sqlNumPac = "
			SELECT COUNT(*) AS Cantidad
			  FROM ".$basedatoshce."_000022 AS A INNER JOIN ".$basedatos."_000178 AS B ON A.Mtrtur = B.Atutur AND B.Atusea = '".$salaEspera."', ".$basedatos."_000018
			 WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
			   AND Mtrest = 'on'
			   AND (Mtrftr = '0000-00-00' OR Mtrfco = '0000-00-00')
			   AND Mtrcur != 'on'
			   AND Ubihis = Mtrhis
			   AND Ubiing = Mtring
			   AND Ubialp != 'on'
			   AND Ubiald != 'on'
			";

			$resNumPac = mysql_query($sqlNumPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNumPac):</b><br>".mysql_error());
			$rowNumPac = mysql_fetch_array($resNumPac);

			// --> Si ya no hay mas capacidad de personas en la sala, se asigna el paciente en la sala por defecto
			if($rowNumPac['Cantidad'] >= $arraySalasEspera[$salaEspera]['Capacidad'])
			{
				$salaEsperaAnt			= $salaEspera;
				$salaEspera 			= (($salaDefectoPorSuperacion != '') ? $salaDefectoPorSuperacion : $salaEspera);
				$mostraAlertaSuperacion	= true;
				$mensaje				= "La sala ".$arraySalasEspera[$salaEsperaAnt]['Nombre']." se encuentra llena.\nEl paciente sera ubicado en la sala ".$arraySalasEspera[$salaEspera]['Nombre'].".";
			}

			$sqlAsigSal = "
			UPDATE ".$basedatos."_000178
			   SET Atusea = '".$salaEspera."'
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($sqlAsigSal, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAsigSal):</b><br>".mysql_error());

			$respuesta['mostrarAlerta'] 	= $mostraAlertaSuperacion;
			$respuesta['mensaje'] 			= $mensaje;
			$respuesta['nuevaSala'] 		= $salaEspera;
			echo json_encode($respuesta);

			break;
			return;
		}
		// --> 	Le asigna una clasificacion de atencion al paciente, relacionado por el turno.
		//		Jerson Trujillo, 2015-07-22.
		case 'asignarClasificacion':
		{
			$sqlAsigCla = "
			UPDATE ".$basedatos."_000178
			   SET Atucla = '".$clasificacionSelec."'
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($sqlAsigCla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAsigCla):</b><br>".mysql_error());
			break;
			return;
		}
		// --> 	Le asigna una clasificacion de atencion al paciente, relacionado por el turno.
		//		Jerson Trujillo, 2015-07-22.
		case 'asignarGeneroMedicoQueDebeAtender':
		{
			$sqlAsigGenMed = "
			UPDATE ".$wbasedatohce."_000022
			   SET Mtrgme = '".$generoMedicoAtender."'
			 WHERE Mtrhis = '".$historia."'
			   AND Mtring = '".$ingreso."'
			";

			mysql_query($sqlAsigGenMed, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAsigGenMed):</b><br>".mysql_error());
			break;
			return;
		}
		// --> 	Validar si el paciente ya esta siendo llamado a consulta.
		//		Jerson Trujillo, 2015-07-22.
		case 'validarSiPacienteYaEstaEnLLamado':
		{
			$respuesta = array("Error", FALSE => "Mensaje", "");

			$sqlValLla = "
			SELECT Descripcion
			  FROM ".$basedatos."_000178 AS A LEFT JOIN usuarios AS B ON A.Atuulc = B.Codigo
			 WHERE Atutur = '".$turno."'
			   AND Atullc = 'on'
			";
			$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
			if($rowValLla = mysql_fetch_array($resValLla))
			{
				$respuesta['Error'] 	= TRUE;
				$respuesta['Mensaje'] 	= "No se puede modificar la especialidad del paciente porque esta\nsiendo llamado a consulta por ".$rowValLla['Descripcion'].".";
			}
			echo json_encode($respuesta);
			break;
			return;
		}
		default :
			break;

		case 'atencionAutorizada':
		{
			$respuesta = array("Error", FALSE => "Mensaje", "");
			$wuser = substr($_SESSION['user'], (strpos($_SESSION['user'], "-") + 1), strlen($_SESSION['user']));

			$sqlAtencioAut = "UPDATE ".$basedatoshce."_000022
								   SET Mtraut = '".$estado_aut."', Mtrfat = '".date('Y-m-d')."', Mtrhat = '".date('H:i:s')."', Mtruau = '".$wuser."'
								 WHERE Mtrhis = '".$whis."' 
								   AND Mtring = '".$wing."' ";
			mysql_query($sqlAtencioAut, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAtencioAut):</b><br>".mysql_error());

			echo json_encode($respuesta);

		break;
		}

	}

//Liberacion de conexion Matrix
liberarConexionBD($conex);

//Liberacion de conexion Unix
//liberarConexionOdbc($conexUnix);
}
if(!isset($consultaAjax)) { ?>
<script>
    $(document).on('change','#selectsede',function(){
        window.location.href = "agenda_urgencias_por_especialidad.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val()
    });
</script>
</body>
</html>
<?php
include_once("conex.php"); } ?>
