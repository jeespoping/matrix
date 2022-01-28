<?php
include_once("conex.php");
include_once("root/comun.php");
include_once("citas/funcionesAgendaCitas.php");
$wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;

$conex = obtenerConexionBD("matrix");

if(isset($consultaAjax) && $consultaAjax != "" )
{
	if (isset($accion) and $accion == 'actualizar') {
		//atendido cambia el campo asistida y atendido lo usan los que no tienen ayudas diagnosticas nuevas configuradas
		$respuesta = actualizar($caso, $solucionCitas, $est, $valCitas, $id);
		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'cancelar') {

		$respuesta = cancelAppointment($caso, $causa, $est, $id, $solucionCitas);
		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'actualizar1') {
		//no asiste
		$respuesta = saveNonAttendance($caso, $valCitas, $solucionCitas, $est, $causa, $id);
		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'demora') {

		$respuesta = setDelay($caso,$solucionCitas, $est, $campoCancela, $idRadio);
		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'autorizacion') {

		$respuesta = setAutorization();
		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'validarNoAsiste'){

		$respuesta = validateNonAttendance();
		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'cancelarTurno'){

		$respuesta = cancelTurn($turno, $solucionCitas);
		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'validarCancelacion'){
		$respuesta = validarCancelacion();

		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'quitarPacienteAgenda'){

		$respuesta = quitarPacienteAgenda();

		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'guardarUbicacionRecargaPagina') {

		$respuesta = guardarUbicacionRecargaPagina($ubicacion, $solucionCitas );
		echo json_encode($respuesta);
		return;
	}

	if (isset($accion) and $accion == 'guardarUsuarioRecargaPagina') {

		$respuesta = guardarUsuarioRecargaPagina($ubicacion, $solucionCitas );
		echo json_encode($respuesta);
		return;
	}

	if(isset($accion) and $accion == 'asociarTurnoCita'){

		$respuesta = asociarTurnoCita($turnoPac, $cedulaPacAgenda, $nombrePacAgenda, $cedulaPacSincita, $nombrePacSincita, $idAgenda);
		echo json_encode($respuesta);
		return;
	}

	if(isset($accion) and $accion == 'guardarAsociarTurnoCita'){

		$respuesta = guardarAsociarTurnoCita($cco, $cedulaPac, $turnoPac, $nombrePac, $idFilaAgenda, $caso, $cedulaSinCita);
		echo json_encode($respuesta);
		return;
	}

	if(isset($accion) and $accion == 'getOptionsLocations'){

		$respuesta = getOptionsLocations($cco, $rolSelected);
		echo json_encode($respuesta);
		exit();
	}

	if(isset($accion) and $accion == 'getListColumnsAllowed'){

		$respuesta = getListColumnsAllowed($cco, $rolSelected);
		echo json_encode($respuesta);
		exit();
	}

	if(isset($accion) and $accion == 'executeAction'){
		$userS = $_SESSION["user"];
		$userS = explode("-", $userS);
		$userS = $userS[1] != "" ? $userS[1] : "";

		$respuesta = executeAction($cco, $codeAction, $identification, $userS, $caso, $ubication, $namePac, $wemp_pmla, $conex, $valCitas, $idCita);
		echo json_encode($respuesta);
		exit();
	}

	if(isset($accion) and $accion == 'setListTurnWithoutApt'){

		$respuesta = setListTurnWithoutApt($cco, $valCitas, $caso,  $wemp_pmla, $wsw, $fest );
		echo json_encode($respuesta);
		exit();
	}

	if(isset($accion) and $accion == 'llamarPacienteAdmisionSinCita'){

		$respuesta = llamarPacienteAdmisionSinCita($cco, $cedulaPac, $turnoPac, $location);
		echo json_encode($respuesta);
		exit();
	}

	if(isset($accion) and $accion == 'apagarLlamarPacienteAdmisionSinCita'){

		$respuesta = apagarLlamarPacienteAdmisionSinCita($cco, $cedulaPac, $turno, $location);
		echo json_encode($respuesta);
		exit();
	}

	if(isset($accion) and $accion == 'loadUpdatedInformation'){

		$respuesta = loadUpdatedInformation($cco, $location, $caso, $valCitas, $arrDatosIn);
		echo json_encode($respuesta);
		exit();
	}

	if(isset($accion) and $accion == 'abrirHistoriaClinica'){

		$respuesta = abrirHistoriaClinica($cco, $identification);
		echo json_encode($respuesta);
		exit();
	}

	if(isset($accion) and $accion == 'admision'){

		$respuesta = admision($cco, $identification, $caso, $valCitas);
		echo json_encode($respuesta);
		exit();
	}
	
	if(isset($accion) and $accion == 'validarDisponibilidadUbicacion'){
	
		$respuesta = validarDisponibilidadUbicacion($solucionCitas, $monitor, $ubicacion);
		echo json_encode($respuesta);
		exit();
	}
	
	if (isset($accion) and $accion == 'liberarUbicaciones'){

		$respuesta = liberarUbicaciones($solucionCitas, $monitor, $ubicacion);
		echo json_encode($respuesta);
		return;
	}
	
	if (isset($accion) and $accion == 'reasignarUbicacion'){

		$respuesta = reasignarUbicacion($solucionCitas, $monitor, $ubicacion);
		echo json_encode($respuesta);
		return;
	}
	
	if (isset($accion) and $accion == 'cargarDatosIniciales'){

		$respuesta = cargarDatosIniciales($wemp_pmla, $solucionCitas);
		echo json_encode($respuesta);
		return;
	}

} else {
?>
<html>
<head>
<title>AGENDA CITAS</title>
<meta charset="utf-8">
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

<!-- Inicio estilos css -->
<style type="text/css">
	.blinkProcesoActual {
        background-color: #FF9664;
    }
	.btn-primary:hover {
		color: #fff;
		background-color: #286090;
		border-color: #204d74;
	}

	a {
		color: #337ab7;
		text-decoration: none;
	}

	body{
		width: 95%;
		height: 95%;
		font-size: 11px
	}

	.page-header {
		margin: -10px 0 20px;
	}

	table {
       border-collapse: separate;
       border-spacing: 2px;
    }
	.ui-multiselect { background:white; background-color:white; color: black; font-weight: normal; font-family: verdana; border-color: gray; border: 3px; height:20px; width:450px; overflow-x:hidden;text-align:left;font-size: 10pt;}

	.ui-multiselect-menu { background:white; background-color:white; color: black; font-weight: normal; font-size: 10pt;height: 450px;}

	.ui-multiselect-header { background:white; background-color:lightgray; color: black;font-weight: normal;}


    .ui-multiselect-checkboxes {
        max-height: 400px;
     }


</style>
<script type="text/javascript">
	//Variables globales 
	var arrAcciones = new Array();

	$(document).ready( function (){
		
		cargarDatosIniciales();		

		$('#slDoctor').multiselect({
                     numberDisplayed: 1,
                     selectedList:1,
                     height: "auto",
                     multiple:false
        }).multiselectfilter();
		
		var wvalorPerfil    = $("#valorPerfil").val();
		var wvalorUbicacion = $("#valorUbicacion").val();

        if (wvalorPerfil != '' && wvalorUbicacion != ''){
		   $("#selectRoles option[value="+ wvalorPerfil +"]").attr("selected",true);
	    }
		
		$("#selectRoles").change(function(){
			//Se llama la función que llena el selector de ubicaciones
			$.post("pantallaAdmision.php",
			{
				consultaAjax:   		'on',
				accion:         		'getOptionsLocations',
				wemp_pmla:        		$("#wemp_pmla").val(),
				cco:        			$("#solucionCitas").val(),
				rolSelected:        	 $(this).val()
				}, function(respuesta){					
					setOptionsLocations(respuesta);
					$("#locations option[value="+ wvalorUbicacion +"]").attr("selected",true);
			}, 'json');
		});
        

		$("#locations").change(function(){
			//Se llama la función que permite saber que columnas mostrar
			validarUbicacion();			
		});

		if (wvalorPerfil != '' && wvalorUbicacion != ''){
            $("#selectRoles").change();
            $("#locations").change();

		}

		var actualizarListaPacientes = setInterval(function() {

														var monitor = $("#monitores").val();
														var ubicacion = $("#locations").val();
														if(ubicacion != '') {
															var useNewSystem = $("#useNewSystem").val();
															if(useNewSystem == "on"){
																loadUpdatedInformation();
																getListWithoutAppointment();
													
													}
														}
													}, 10000);

		var blinkProcesoActual = setInterval(function() {
												$("td[blink=true]").toggleClass("blinkProcesoActual");
											}, 1000);



	});

	
	function cargarDatosIniciales(){	
		$.post("pantallaAdmision.php",
			{
				consultaAjax:   		'on',
				accion:         		'cargarDatosIniciales',
				wemp_pmla:        		$("#wemp_pmla").val(),
				solucionCitas:        	$("#solucionCitas").val()
				}, function(respuesta){
					arrAcciones = respuesta;					
			}, 'json');
	}
	

	function loadUpdatedInformation(){

		$.post("pantallaAdmision.php",
			{
				consultaAjax:   		'on',
				accion:         		'loadUpdatedInformation',
				wemp_pmla:        		$("#wemp_pmla").val(),
				cco:        			$("#solucionCitas").val(),
				rolSelected:     		$("#selectRoles").val(),
				location:     		    $("#locations").val(),
				caso:     		        $("#caso").val(),
				valCitas:     		    $("#valCitas").val(),
				arrDatosIn:     		arrAcciones
			}, function(respuesta){
               
				jQuery.each(respuesta.arrayDatos, function(){
										   
					//Quiere decir que ya se realizó la ultima acción de la ultima actividad por lo tanto el paciente desaparece de la lista
					if(this.deleteRow == true){
						$("#rwPacienteAgendado_" + this.identification).remove();
						$("tr[id^=rwPacienteAgendado_]").removeClass("fila1");
						$("tr[id^=rwPacienteAgendado_]").removeClass("fila2");
						var cont = 0;
						$("tr[id^=rwPacienteAgendado_]").each(function(){
							cont++;
							var css = (cont % 2 == 0) ? 'fila1': 'fila2';
							$(this).addClass(css);
							$(this).find("td").first().html(cont);
						});
					}else{
						$("tr#rwPacienteAgendado_"+this.identification).find("#tdAutorizacion_"+this.identification).attr("turno", this.turno);
						$("tr#rwPacienteAgendado_"+this.identification).find("#tdAutorizacion_"+this.identification).text(this.turno);
						$("tr#rwPacienteAgendado_"+this.identification).find("#tdAutorizacion_"+this.identification).css("background-color", "rgb(150, 255, 150)");
						                        
						
						if(this.actualAct == "" && this.arrActionFin.length == 0){
							$("#actualAction_"+this.identification).text("En espera de atenci\u00F3n");
						}else{
												
							if(this.concatenar !== ''){
								
                               if (this.actualAct !== this.concatAct && this.concatAct !== '')
							       $("#actualAction_"+this.identification).text(this.actualAct + '  ' + this.actualUbi + ' - '+ this.concatAct );

							   else
							   	   $("#actualAction_"+this.identification).text(this.actualAct + '  ' + this.actualUbi );
							}   	
							else
							{	
							   //alert(this.turno+'-'+this.identification+'-'+this.actualAct);

                               $("#actualAction_"+this.identification).text(this.actualAct + '  ' + this.actualUbi);
                            }
						}
						
						if(this.typeAction == "Llamar"){
							$("#actualAction_"+this.identification).attr("blink", "true");
							$("td[blink=true]").toggleClass("blinkProcesoActual");
						}else {
							$("#actualAction_"+this.identification).removeAttr("blink").removeClass("blinkProcesoActual");
						}

						//Esto es para validar que actividades se han finalizado completamente para poner el td en color verde y desactviar los selectores
						var identification = this.identification;
						jQuery.each(this.arrFinish, function(index, value){
							$("tr#rwPacienteAgendado_"+identification).find("#colAcitivy_"+value).css("background-color", "rgb(150, 255, 150)");
							$("tr#rwPacienteAgendado_"+identification).find("#colAcitivy_"+value).find("input").attr('disabled',true);
							$("tr#rwPacienteAgendado_"+identification).find("#colAcitivy_"+value).find("input").attr('checked',true);
							$("tr#rwPacienteAgendado_"+identification).find("#colAcitivy_"+value).find("img").removeAttr("onclick");
						});

						//Esto es un array con todas las accines individuales que ya han sido realizadas con el fin de que cuando la pagina se recargue queden seleccionadas.
						jQuery.each(this.arrActionFin, function(index, value){
							$("tr#rwPacienteAgendado_"+identification).find("#execAct_"+value).attr('checked',true);
						});

						//Se valida si el paciente tiene dos citas para deshabilitar la segunda fila1
						if($("tr#rwPacienteAgendado_"+identification).length > 1){
							$("tr#rwPacienteAgendado_"+identification).last().find("input").attr('disabled',true);
							$("tr#rwPacienteAgendado_"+identification).last().find("img").removeAttr("onclick");
							$("tr#rwPacienteAgendado_"+identification).last().find("#actualAction_"+identification).text("Doble Agenda");
						}
					}
				});

				// si la cedula correcta era la del turno sin cita obligatoriamente se debe hacer que se regargue la página
				if(respuesta.mostrarRecargar == true || respuesta.consultaRecargar == true){
					recargarPagina();
				}/* else { // 2018-03-01 Se desactiva el div para que recargue la pagina automáticamente
					$("#divRecargarPagina").css("display", "none");
				}*/
			}, 'json');
	}

	function getListWithoutAppointment(){
		$.post("pantallaAdmision.php",
			{
				consultaAjax:   		'on',
				accion:         		'setListTurnWithoutApt',
				wemp_pmla:        		$("#wemp_pmla").val(),
				cco:        			$("#solucionCitas").val(),
				valCitas:        		$("#valCitas").val(),
				caso:        			$("#caso").val(),
				wsw:        			$("#wsw").val(),
				fest:        			$("#fest").val()
				}, function(respuesta){
					$("#divListaPacientesConTurno").html(respuesta.html);
			}, 'json').done(
					function() {
						$(".filaDraggable").draggable({
							revert: true,
							helper: 'clone'
						});
					},
					function(){
						$(".filaDroppable").droppable({
							accept: ".filaDraggable",
							drop: function(ev, ui) {
								var turno          = ui.draggable.find("td[id^=tdrTurno_]").attr("turno");
								var cedulaSinCita  = ui.draggable.find("td[id^=tdrCedula_]").attr("cedula");
								var nombreSinCita  = ui.draggable.find("td[id^=tdrNombre_]").attr("nombre");
								var cedulaAgenda   = $(this).attr("cedula");
								var nombreAgenda   = $(this).attr("nombre");
								var idAgenda     = $(this).attr("idAgenda");

								if($(this).find("td[id^=tdAutorizacion_]").attr("turno") === '')
								{
									asociarTurnoCita(turno, cedulaSinCita, nombreSinCita, cedulaAgenda, nombreAgenda, idAgenda);
								}
							}
						});
					}
					);
	}

	function showColsAllowed(AllowedActivities){
		$(".AditonalColumns").hide();
		jQuery.each(AllowedActivities, function(){
			$("#ColTittle_"+this).show();
			$(".AditonalColumns."+this).show();
		});
	}

	function setOptionsLocations(locations){

		$("#tableLocations").css("display", "block");
		$('#locations').empty();
		$('<option/>', {
					'value': '',
					'text': 'Seleccione una ubicaci\u00F3n'
			}).appendTo('#locations');

			jQuery.each(locations, function(){

				$('<option/>', {
					'value': this.code,
					'text': this.description
				}).appendTo('#locations');
			});
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//Estas opcioens aplican en general para agendas que no usan turnero
	//
	function asistida( adicion,mostrarCausa )
	{
		var auxDiv = document.createElement( "div" );
		auxDiv.innerHTML = "<INPUT type='hidden' name='asistida' value='"+adicion+"'>";

		if (mostrarCausa == 'on')
		{
			$.blockUI({ message: $('#causa_demora') });
		}
		document.forms[0].appendChild( auxDiv.firstChild );
	}

	function cancela(id_radio1, idCampo)
	{
		var valorc = $('[name="'+id_radio1+'"]:checked').val();
		if (valorc!='I') {
				valorc = 'A';
		}

		mes_confirm = 'Confirma que desea cancelar la cita?';
		if(confirm(mes_confirm))
		{
			$.blockUI({ message: $('#causa_cancelacion') });

			id = idCampo;
			idRadio = id_radio1;
			accion = 'cancelar';
			est = valorc;
			func = respuestaAjaxCancela;

			//Busco el select de causa para el div correspondiente
			var contenedorCancela = document.getElementById( "causa_cancelacion" );

			campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );
		} else {
			$("#"+id_radio1).removeAttr("checked");
		}
	}

	//Función que marca las citas como no asistidas (cuando no usan turnero)
	function no_asiste(id_radio2, idCita)
	{
		var valora = $('[name="'+id_radio2+'"]:checked').val();

		if (valora!='off')
		{
			valora = 'off';
		}

		mes_confirm = 'Confirma que desea marcar la cita como no asistida?';
		if(confirm(mes_confirm))
		{
			$.blockUI({ message: $('#causa_noasiste') });

			idRadio 	  = id_radio2;
			id 	          = idCita;
			accion 		  = 'actualizar1';
			est 		  = valora;
			func		  = respuestaAjaxNoAsiste;
			caso 		  = $("#caso").val();

			//Busco el select de causa para el div correspondiente
			var contenedorCancela = document.getElementById( "causa_noasiste" );

			campoCancela = document.getElementById( "causa_noasiste" ).getElementsByTagName( "select" );
		} else {
			$("#"+id_radio2).removeAttr("checked");
		}
	}

	//2016-06-17
	//Función para cancelar los turnos que no tenian cita y que no se les va asignar
	//una para el dia actual o pacientes que solo toman turno para preguntar algo.
	function cancelarTurno(turno){
		//Existe la posibilidad de que la admisión no se finalice porque el paciente no requeria admision pero los demás procesos si
		var msj = "Est&aacute; seguro de anular este turno?";
		var solucionCitas = $("#solucionCitas").val();
		jConfirm(msj,"Confirmacion", function(r) {
			if(r) {
				$.post("pantallaAdmision.php",
				{
					consultaAjax:   	'on',
					accion:         	'cancelarTurno',
					turno: 				turno,
					solucionCitas: 		solucionCitas
				}, function(data){
					if(data.Error)
					{
						jAlert(data.Mensaje, "Alerta");
					}
					else
					{
						alert("El turno ha sido cancelado");
					}
				}, 'json').done(function () {

				});
			}else{
				$("#finPreparacion_"+idfila).attr("checked", false);
			}
		});
	}


	id = '';
	idRadio = '';
	accion = '';
	est = '';
	func = '';
	campoCancela = '';

	function abrirVentana( adicion, citas, solucion, wdoc, mostrarCausa, wtdo, wagendaMedica)
	{
		var auxDiv = document.createElement( "div" );
		auxDiv.innerHTML = "<INPUT type='hidden' name='admision' value='"+adicion+"'>";
		document.forms[0].appendChild( auxDiv.firstChild );
		//se le adiciona al div causa_demora unos parametros para poder consultarlos despues desde
		//otra funcion
		$('#causa_demora')[0].adicion = adicion;
		$('#causa_demora')[0].citas = citas;
		$('#causa_demora')[0].solucion = solucion;
		$('#causa_demora')[0].wdoc = wdoc;
		$('#causa_demora')[0].wtdo = wtdo;
		$('#causa_demora')[0].wagendaMedica = wagendaMedica;

		if (mostrarCausa == 'on') {
			$.blockUI({ message: $('#causa_demora') });
		} else {
			abrirVentanaAdmision(adicion, citas, solucion, wdoc, wtdo, wagendaMedica)
		}
	}

	function abrirVentanaAdmision(adicion, citas, solucion, wdoc, wtdo, wagendaMedica)
	{
		var ancho=screen.width;
		var alto=screen.availHeight;
		var path = "../../admisiones/procesos/admision_erp.php?wemp_pmla="+wemp_pmla+"&TipoDocumentoPacAm=" + wtdo + "&DocumentoPacAm=" + wdoc + "&TurnoCsAm=''&AgendaMedica=" + wagendaMedica + "&solucionCitas=" + solucion;
		window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
	}

	function asiste(id_radio, id, mostrarCausa)
	{
		var valora = $('[name="'+id_radio+'"]:checked').val();

		if (valora!='on') {
				valora = 'off';
		}

		if (mostrarCausa == 'off') {
			$.post("pantallaAdmision.php",
					{
						solucionCitas:  $('#solucionCitas').val(),
						consultaAjax:   'on',
						id:          	id,
						accion:         'actualizar',
						est: 			valora,
						caso:			$('#caso').val(),
						solucionCitas:	$('#solucionCitas').val(),
						valCitas:		$('#valCitas').val()
					}
					,function(data) {

					if(data.error == 1) {
					} else {
						document.location.reload(true);
					}
			});
		} else {
			$.blockUI({ message: $('#causa_demora') });

			idRadio 	  = id;
			accion		  = 'demora';
			est 		  = valora;
			func 		  = respuestaAjaxDemora;
			caso 		  = $("#caso");
			solucionCitas = $("#solucionCitas");

			//Busco el select de causa para el div correspondiente
			var contenedorCancela = document.getElementById( "causa_demora" );
			campoCancela = document.getElementById( "causa_demora" ).getElementsByTagName( "select" );
		}
	}

	function obtenerMonitor(elem){
		var href2 = $(elem).attr("href2");

		href2 = href2+'&monitor='+$("#monitor").val()+'&ubicacion='+$("#ubicacion").val();

		$(elem).attr("href",href2);
		$(elem).removeAttr("onclick");
		$(elem).trigger("click");
	}

	function asociarTurnoCita(turno, cedulaSinCita, nombreSinCita, cedulaAgenda, nombreAgenda, idAgenda)
	{
		$.post("pantallaAdmision.php",
		{
			consultaAjax:   		'on',
			accion:         		'asociarTurnoCita',
			turnoPac: 				turno,
			cedulaPacSincita: 		cedulaSinCita,
			nombrePacSincita: 		nombreSinCita,
			cedulaPacAgenda: 		cedulaAgenda,
			nombrePacAgenda: 		nombreAgenda,
			idAgenda: 				idAgenda

		}, function(data){
			if(data.Error) {
				jAlert(data.Mensaje, "Alerta");
			} else {
				$("#divAsociarTurnoCita").html(data.html);
			}
		}, 'json').done(function(){
				$("#divAsociarTurnoCita").dialog({
												height: 'auto',
												width: 'auto',
												modal: true
											}
										)
				});
	}

	function guardarAsociarTurnoCita(turno, cedulaSinCita, nombreSinCita, cedulaAgenda, nombreAgenda, idAgenda)
	{
		var seleccion_paciente = $("input:radio[name=opcionCorrectaTurnoSinCita]:checked").length;

		if(seleccion_paciente > 0) {
			var opcionPaciente = $("input:radio[name=opcionCorrectaTurnoSinCita]:checked").val();

			var cedulaPac      = $("#asociarCedula" + opcionPaciente).val();
			var nombrePac      = $("#asociarNombre" + opcionPaciente).val();

			//2016-04-07 Verónica Arismendy
			//se debe validar que los campos de la opción seleccionada estén completamente diligenciados
			if(cedulaPac != "" && nombrePac != "") {
				$("#msjErorOpcionCorrecta").html("");

				$.post("pantallaAdmision.php",
				{
					consultaAjax:   		'on',
					accion:         		'guardarAsociarTurnoCita',
					turnoPac: 				$("#asociarTurno" + opcionPaciente).val(),
					cedulaPac: 				cedulaPac,
					nombrePac: 				nombrePac,
					cedulaPacLog: 			$("#asociarTurnoPacAgenda").attr("documentoAgenda"),
					idFilaAgenda: 			idAgenda,
					ubicacion: 				$("#ubicacion").val(),
					cedulaSinCita:			cedulaSinCita,
					cco:					$("#solucionCitas").val(),
					caso:					$("#caso").val(),
				}, function(data){

					if(data.Error) {
						if(data.MensajeCitasen23 != '') {
							jAlert(data.MensajeCitasen23, "Alerta");
						} else if(data.MensajeCitasen09 != '') {
							jAlert(data.MensajeCitasen09, "Alerta");
						}
					} else {
						jAlert(data.MensajeCitasen09, "Alerta");
					}

				}, 'json').done(function(){
					$("#divAsociarTurnoCita").dialog("close");

					var turnoAsociado = $("#asociarTurno" + opcionPaciente).val().substr(7, 10);
					var cedulaAsociada = $("#asociarCedula" + opcionPaciente).val();
					var nomPacAsociado = $("#asociarNombre" + opcionPaciente).val();

                    //Elimino el turno en la tabla superior 'Turno sin Cita'
					$("#trTurno_" + turnoAsociado).remove();

                    //Asigno los atributos turno y cedula, los cuales entregan inf. del nuevo paciente
					$("#tdAutorizacion_" + cedulaAgenda).attr('turno',turnoAsociado);
					$("#tdAutorizacion_" + cedulaAgenda).attr('cedula',cedulaSinCita);

					$("#tdAutorizacion_" + cedulaAgenda).html("<b>" + turnoAsociado + "<b>").css("background-color", "#96FF96");
					$("#tdCedulaAgenda_" + cedulaAgenda).html(cedulaAsociada);
					$("#tdNomPacAgenda_" + cedulaAgenda).html(nomPacAsociado);

					$("td[blink=true]").toggleClass("blinkProcesoActual");

					if ($("tablaListaTurnos trTurno_").length < 1) {
						$("#tablaListaTurnos").hide();
					}

				});
			} else {
				$("#msjErorOpcionCorrecta").html("La opcion seleccionada como la correcta debe tener diligenciados <br> los campos de Documento y Nombre del Paciente.");
			}
		} else {
			jAlert("Se deben diligenciar todos los campos.", "Alerta");
		}
	}

	function cerrarAsociarTurnoCita()	{
		$("#divAsociarTurnoCita").dialog("close");
	}

	//para abrir la ventana de agenda
	function abrirVentanCitas(solucionCitas, wemp_pmla, caso, wsw, fest){
		var ventanaAgenda = window.open("../../citas/procesos/calendar.php?empresa="+solucionCitas+"&wemp_pmla="+wemp_pmla+"&caso="+caso+"&wsw="+wsw+"&fest="+fest+"&consultaAjax=","miventana","width=1000,height=750");
	}


	//Para que cuando se cierre la ventana de agenda se recargue está pagina.
	function postCerrarVentanaCitas(){

		$.post("pantallaAdmision.php",
			{
				consultaAjax:   	'on',
				accion:         	'guardarUsuarioRecargaPagina',
				solucionCitas: 		$("#solucionCitas").val(),
				ubicacion: 			$("#locations").val()
			}, function(data){

				if(!data.Error)
				{

					var ubicacion = $("#locations").val();
			        var monitores = $("#selectRoles").val();
					var ccosto    = $("#ccosto").val();
					var solucionCitas = $("#solucionCitas").val();
					var caso = $("#caso").val();
			        
					if(solucionCitas === "citasen") {
						location.href = "../../citas/procesos/pantallaAdmision.php?wemp_pmla="+wemp_pmla+"&caso="+caso+"&solucionCitas="+solucionCitas+"&ccosto="+ccosto+"&per="+monitores+"&ubn="+ubicacion;
					}

				}
			}, 'json').done(function () {


			});	
	
	}


	//Fecha creación: 2016-02-11
	//Autor: Eimer Castro
	//Esta función realiza el post para realizar la creación del registro en la tabla de logs citasen_000023
	//y adicionalmente realiza la impresión del turno para el paciente que tiene cita.
    function autorizacion(idAutorizacion, cedulaPaciente, nombrePaciente, solucionCitas) {
        $.post("pantallaAdmision.php",
        {
            consultaAjax:           'on',
            accion:                 'autorizacion',
            cedulaPac:         		cedulaPaciente,
            nombrePac:         		nombrePaciente,
            solucionCitas:     		solucionCitas
        }, function(data){

            if(data.Error) {
                alert(data.Mensaje);
            } else {
                imprimirTurno(data.fichoTurno);
                $("#" + idAutorizacion).attr("disabled", "disabled");
            }

        }, 'json');
    }

	function imprimir(wemp_pmla,caso,wsw,solucionCitas,slDoctor,valCitas,wfec)
	{
		var v = window.open( 'impresionAgenda.php?wemp_pmla='+wemp_pmla+'&caso='+caso+'&wsw='+wsw+'&solucionCitas='+solucionCitas+'&slDoctor='+slDoctor+'&valCitas='+valCitas+'&wfec='+wfec,'','scrollbars=1', 'width=300', 'height=300' );
	}

	function respuestaAjaxNoAsiste(data)
	{
		if(data.error == 1)	{

		} else {
			document.location.reload(true);
		}
	}

	function respuestaAjaxCancela(data)
	{
		if(data.error == 1) {
			alert("No se pudo realizar la cancelacion");
		} else {
			alert("Cita Cancelada"); // update Ok.
			document.location.reload(true);
		}
	}

	function respuestaAjaxDemora(data)
	{
		if(data.error == 1) {

		} else 
		   document.location.reload(true);
		
	}

	function llamarAjax()
	{
		var radioadmision = $('[name="rdAdmision"]:checked').val();
		var radioasistida = $('[name="rdAsistida"]:checked').val();

		if(radioadmision == "on" || radioasistida == "on") {
			if (radioadmision == "on") {
				abrirVentanaAdmision($('#causa_demora')[0].adicion, $('#causa_demora')[0].citas, $('#causa_demora')[0].solucion, $('#causa_demora')[0].wdoc, $('#causa_demora')[0].wtdo, $('#causa_demora')[0].wagendaMedica);
			}

			//agregar el select al form porque cuando se hace el submit jquery lo saca del form
			document.forms[0].appendChild( document.getElementById( "causa_demora" ).getElementsByTagName( "select" )[0] );
			document.forms[0].submit();
		} else {
			//Asigno el valor seleccionado de la causa
			tipo = campoCancela[0].options[ campoCancela[0].selectedIndex ].text;

			if( id != '' && accion != ''  && est != '' && func != '' && tipo !='' ){

				$.post("pantallaAdmision.php",
						{
							wemp_pmla:      $('#solucionCitas').val(),
							consultaAjax:   'on',
							id:          	id,
							accion:         accion,
							est: 			est,
							caso:			$('#caso').val(),
							solucionCitas:	$('#solucionCitas').val(),
							valCitas:	    $('#valCitas').val(),
							causa:			tipo
						}
						,func
					);
			}

			idRadio = '';
			accion = '';
			est = '';
			func = '';
			campoCancela = '';
			campoCancela.selectedIndex = 0;
		}
	}

	//Fecha creación: 2016-02-11
	//Autor: Eimer Castro
	//Esta función realiza la impresión del turno para el paciente que tiene cita.
    function imprimirTurno(fichoTurno)
    {
        setTimeout(function(){
            $("#fichoTurno").html("");
        }, 6000);

        // --> Imprimir tiquete de turno.
        setTimeout(function(){
            var contenido   = "<html><body onload='window.print();window.close();'>";
            contenido       = contenido + fichoTurno + "</body></html>";

            var windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=1,height=1,resizable=yes,screenX=1,screenY=1,personalbar=no,scrollbars=no";
            var ventana = window.open( "", "",  windowAttr );
            ventana.document.write(contenido);
            ventana.document.close();
        }, 1000);
    }


	function executeAction(codeAction, id, identification, activity, namePac="", idCita){
		
		var sendAction = false;
		var useTurn    = $("#CcoUseTurn").val();
		var cedulaant  = $("#tdCedulaAgenda_"+identification).html();
		var cedula     = $("#tdAutorizacion_"+identification).attr("cedula");

		if (cedulaant.trim() !== cedula.trim() )
			cedula = identification;

		if(useTurn == 'on' && $("#tdAutorizacion_"+identification).attr("turno") == ""){
			jAlert("Para relizar alguna acción el paciente debe primero tener un turno.", "Alerta");
			$("#"+id).prop("checked", false);
			return;

		}else{

			$.post("pantallaAdmision.php",
			{
				consultaAjax:   		'on',
				accion:         		'executeAction',
				cco:        			$("#solucionCitas").val(),
				codeAction:				codeAction,
				caso:					$("#caso").val(),
				idCita:                 idCita,
				identification:			cedula,
				namePac:				namePac,
				ubication:				$("#locations").val(),
				wemp_pmla:				$("#wemp_pmla").val(),
				valCitas:				$("#valCitas").val()
				}, function(objRespuesta){		

				if(objRespuesta.error === true){
					//Se quita el selected al radio o checkbox
					if(objRespuesta.removeSelected == true){
						$("tr#rwPacienteAgendado_"+identification).find("#"+id).prop("checked", false);
					}
					if(objRespuesta.message != ""){
						jAlert(objRespuesta.message, "Alerta");
					}
				} else {

					if(objRespuesta.typeAction == "Llamar"){
						$("tr#rwPacienteAgendado_"+identification).find("#colAcitivy_"+activity).attr("blink", "true");
						$("td[blink=true]").toggleClass("blinkProcesoActual");
						$('td[blink=true]').each(function() {
							$(this).toggleClass("blinkProcesoActual");
						});
					}else{
						$("tr#rwPacienteAgendado_"+identification).find("#colAcitivy_"+activity).removeAttr("blink").removeClass("blinkProcesoActual");
					}

					//Si no ocurreio un error y se ejecuta la acción se debe deshabilitar el checked anterior.
					if(objRespuesta.beforeAction != ""){
						$("tr#rwPacienteAgendado_"+identification).find("#execAct_"+objRespuesta.beforeAction).prop("disabled", true);
					}

					//Eso es para dos casos especificos de ventanas emergentes que se deben abrir cuando se selecciona la acción admisión o la acción ver historia clinica
					//El valor de actionJs esta configurado en la tabla de acciones (000032)
					if(objRespuesta.actionJs != "" && objRespuesta.actionJs == "abrirAdmision"){
						abrirAdmision(cedula);
					} else if (objRespuesta.actionJs != "" && objRespuesta.actionJs == "abrirHistoriaClinica"){
						abrirHistoriaClinica(cedula, id);
					}

					loadUpdatedInformation();
				}
			},"json");
		}
	}

	//FUNCIONES PARA PACIENTES CON CITA Y SIN TURNO
	function llamarPacienteAdmisionSinCita(idLlamadoAdmision, cedulaPaciente, nombrePaciente, solucionCitas){

		var idtd = idLlamadoAdmision.split("_", 2);
    	var turno = $("#tdOpcionesTurnoSinCita_" + idtd[1]).attr("turno");

	    $.post("pantallaAdmision.php",
	    {
	            consultaAjax:           'on',
	            accion:                 'llamarPacienteAdmisionSinCita',
	            idLlamadoAdmision: 		idLlamadoAdmision,
	            cedulaPac:         		cedulaPaciente,
	            nombrePac:         		nombrePaciente,
	            cco:     				solucionCitas,
	            location: 				$("#locations").val(),
	            turnoPac: 				turno
	    }, function(data){

	        if(data.Error){
				jAlert(data.Mensaje, "Alerta");
			} else {
				$("#tdrTurno_" + turno).attr("blink", "true");
				$("td[blink=true]").toggleClass("blinkProcesoActual");
	        }

	    }, 'json');
	}


	function apagarLlamarPacienteAdmisionSinCita(idApagarLlamadoAdmision, cedulaPaciente, nombrePaciente, solucionCitas) {

        var idtd = idApagarLlamadoAdmision.split("_", 2);
		var turno = $("#tdOpcionesTurnoSinCita_" + idtd[1]).attr("turno");

        $.post("pantallaAdmision.php",
        {
            consultaAjax:           	'on',
            accion:                 	'apagarLlamarPacienteAdmisionSinCita',
            idApagarLlamadoAdmision: 	idApagarLlamadoAdmision,
            cedulaPac:         			cedulaPaciente,
            nombrePac:         			nombrePaciente,
            cco:     					solucionCitas, 
            turno: 						turno,
			location: 					$("#locations").val(),
        }, function(data){

            if(data.Error) {
                jAlert(data.Mensaje, "Alerta");
            } else {
        		$("#tdrTurno_" + turno).removeAttr("blink").removeClass("blinkProcesoActual");
            }
        }, 'json');
    }

	//guarda los datos del usuario que acaba de recargar la pagina para no mostrarle más mensajes de actualizaciones
	function recargarPagina(){
		
	      $.post("pantallaAdmision.php",
			{
				consultaAjax:   	'on',
				accion:         	'guardarUbicacionRecargaPagina',
				solucionCitas: 		$("#solucionCitas").val(),
				ubicacion: 			$("#locations").val()
			}, function(data){
				
				if(!data.Error)
				{
					var ubicacion = $("#locations").val();
			        var monitores = $("#selectRoles").val();
					var ccosto    = $("#ccosto").val();
					var solucionCitas = $("#solucionCitas").val();
					var caso = $("#caso").val();
			        
					if(solucionCitas === "citasen") {
						location.href = "../../citas/procesos/pantallaAdmision.php?wemp_pmla="+wemp_pmla+"&caso="+caso+"&solucionCitas="+solucionCitas+"&ccosto="+ccosto+"&per="+monitores+"&ubn="+ubicacion;
					}

				}
			}, 'json').done(function () {


			});		
	}

	//----------------------------------------------------------------------------------
	// --> Enlace para ir a la historia clinica
	//----------------------------------------------------------------------------------
	function abrirHistoriaClinica(cedulaPac, id)
	{
		$.post("pantallaAdmision.php",
		{
					consultaAjax:   	'on',
					accion:         	'abrirHistoriaClinica',
					identification: 	cedulaPac,
					cco:	  	 		$("#solucionCitas").val()
			}, function(data){
				if(data.Error) {
					jAlert(data.Mensaje, "Alerta");
					//Se quita el selected al radio o checkbox
					$("tr#rwPacienteAgendado_"+cedulaPac).find("#"+id).prop("checked", false);
				} else {
					var historia = data.historia;
					var ingreso  = data.ingreso;
					var tipoDoc  = data.tipoDoc;
					var origen   = $("#wemp_pmla").val();

					var url = "/matrix/hce/procesos/HCE_iframes.php?empresa=hce&origen="+origen+"&wcedula="+cedulaPac+"&wtipodoc="+tipoDoc+"&wdbmhos=movhos&whis="+historia+"&wing="+ingreso+"&accion=F&ok=0";

					var ventanaHce = window.open(url,"miventana","width=auto,height=auto");
					$("#rdhistoria_clinica_"+cedulaPac).attr("checked", false);
				}
			}, 'json').done(function () {

		});
	}

	function abrirAdmision(identification) {
		var solucionCitas = $("#solucionCitas").val();
        $.post("pantallaAdmision.php",
        {
            consultaAjax:           'on',
            accion:                 'admision',
            identification:         identification,
            cco:     				solucionCitas,
            caso:     				$("#caso").val(),
            valCitas:     			$("#valCitas").val()
        }, function(data){

            if(data.Error) {
                jAlert(data.Mensaje, "Alerta");
                $("#" + idAdmision).prop("checked", false);
            } else {

				var path = "../../admisiones/procesos/admision_erp.php?wemp_pmla="+wemp_pmla+"&TipoDocumentoPacAm="+ data.typeDoc +"&DocumentoPacAm=" + identification + "&TurnoEnAm=" + data.Turno + "&AgendaMedica=on&solucionCitas=" + solucionCitas;
            	window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');

            }
        }, 'json');
    }

	 function validarUbicacion()
    {			

        var ubicacion = $("#locations").val();
        var monitor   = $("#selectRoles").val();
		var solucionCitas = $("#solucionCitas").val();

        if(monitor != '00') {
			if(ubicacion != "00"){	
				//Se debe validar primero si otro usuario tiene en uso el perfil y ubicación que se acaban de seleccionar			
				$.post("pantallaAdmision.php",
				{
					consultaAjax	:  'on',
					accion			:  'validarDisponibilidadUbicacion',	           
					solucionCitas	:  solucionCitas,
					ubicacion		:  ubicacion,
					monitor			:  monitor
				}, function(data){	
					if(data.Error) {
						jAlert(data.Mensaje, "Alerta");
					} else {	
						if(data.Ocupado){

							//Preguntar si desea reasignar la ubicación					
							jConfirm(data.Mensaje,"Confirmacion", function(r) {  
								if(r) {
									//Reasigna ubicacion
									$.post("pantallaAdmision.php",
									{
											consultaAjax:      'on',
											accion:            'reasignarUbicacion',
											solucionCitas	:  solucionCitas,
											ubicacion		:  ubicacion,
											monitor			:  monitor
									}, function(data){
											if(data.Error) {
												jAlert(data.Mensaje, "Alerta");
											} else {
												pintarCols();
											}
									}, 'json');
								}else{
									$("#locations").val("00");
								}
							});
						} else{
							pintarCols();						
						}				
					}	
				}, 'json');
			} else { 
				//Quiere decir que seleccino una opción vacía y se debe liberar cualquier ubicación que haya podido tener ocupada
				$.post("pantallaAdmision.php",
					{
					consultaAjax:      'on',
					accion:            'liberarUbicaciones',
					solucionCitas	:  solucionCitas,
					ubicacion		:  ubicacion,
					monitor			:  monitor
					}, function(data){
						if(data.Error)	{
							jAlert(data.Mensaje, "Alerta");
						}
					}, 'json');
			}
        } else {
           
		   //Ocultar columnas
        }		
    }
	
	function pintarCols(){

		$.post("pantallaAdmision.php",
			{
				consultaAjax:   		'on',
				accion:         		'getListColumnsAllowed',
				wemp_pmla:        		$("#wemp_pmla").val(),
				cco:        			$("#solucionCitas").val(),
				rolSelected:     		$("#selectRoles").val()
				}, function(respuesta){
					//call the function that show the columns allowed for the role selected.
					showColsAllowed(respuesta);
					getListWithoutAppointment();
					loadUpdatedInformation();
			}, 'json');
	}

</script>
</head>
<body>

<?php
    // - - - - - - - - - - - - - - - - - - - - - Modificaciones - - - - - - - - - - - - - - - -
    // 2021-11-18  Daniel CB.           Se realiza corrección de parametros 01 quemados.
	//
    // 2020-01-08  Arleyda Insignares C. Se modifica select que lista los médicos, a un multiselect de selección única para la utilización del filtro (busqueda por texto).
	//---------------------------------------------------------ACA TERMINAN LOS LLAMADOS AJAX
	//SON EL RESTO DE FUNCIONES QUE NO SE LLAMAN POR AJAX

	if(!isset($_SESSION['user']) ){
		echo "<br /><br /><br /><br />
			  <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
					  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
			  </div>";
		return;
	}

	//-----------------------------------------------------------INICIO VARIABLES ------------------------------------------------------------------
	$verListaTurnos 	= FALSE;

	// Verifica que sea una secretaria la que pueda ver los pacientes con turno y que no están en agenda
	if($monitor != '') {
		$verListaTurnos = TRUE;
	}
	
	if (!isset($fest))		{
		$fest = "off";
	}

	echo "<input type='HIDDEN' name= 'wemp_pmla' id= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'caso' id= 'caso' value='".$caso."'>";
	echo "<input type='HIDDEN' name= 'wsw' id= 'wsw' value='".@$wsw."'>";
	echo "<input type='HIDDEN' name= 'solucionCitas' id= 'solucionCitas' value='".$solucionCitas."'>";
	echo "<input type='HIDDEN' name= 'valCitas' id='valCitas' value='".@$valCitas."'>";
	echo "<input type='HIDDEN' name= 'monitor' id= 'monitor' value='".$monitor."'>";
	echo "<input type='hidden' name='permitirVerListaTurnos' id='permitirVerListaTurnos' value='".$verListaTurnos."'>";
	echo "<input type='HIDDEN' name= 'ubicacion' id= 'ubicacion' value='".$ubicacion."'>";
	echo "<input type='HIDDEN' name= 'wfec' id= 'wfec' value='".$wfec."'>";
	echo "<input type='HIDDEN' name='ccosto' id= 'ccosto' value='".$ccosto."'>";
	echo "<input type='HIDDEN' name='fest' id= 'fest' value='".$fest."'>";

	//Estos son input que solo tendrán valor en caso de que la página haya sido recargada al cerrar una url externa
	$valorPerfil = isset($per) ? $per : '';
	$valorUbicacion = isset($ubn) ? $ubn : '';
	$key = substr($user, 2, strlen($user));

	echo "<input type='HIDDEN' name='valorPerfil' id='valorPerfil' value='".$valorPerfil."'>";
	echo "<input type='HIDDEN' name='valorUbicacion' id='valorUbicacion' value='".$valorUbicacion."'>";

	//Se consulta la información del centro de costo para saber si usa turnero
	$arrCco = getInfoCentroCosto($solucionCitas);


	if(isset($arrCco["usaTurnero"]) && $arrCco["usaTurnero"] == 'on'){
		echo "<input type='HIDDEN' name='CcoUseTurn' id='CcoUseTurn' value='on'>";
	}else{
		echo "<input type='HIDDEN' name='CcoUseTurn' id='CcoUseTurn' value='off'>";
	}

	//se valida si el cnetro de costo esta registrado en la tabla root_000117 para que utilice la configuración de las nuevas ayudas diagnosticas
	if(isset($arrCco["estado"]) && $arrCco["estado"] == "on"){
		echo "<input type='hidden' name='useNewSystem' id='useNewSystem' value='on'>";
	}else{
		echo "<input type='hidden' name='useNewSystem' id='useNewSystem' value='off'>";
	}

	//-----------------------------------------------------------FIN VARIABLES -----------------------------------------------------------------------


	//------------------------------------------------------------ENCABEZADO--------------------------------------------------------------------------
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$wbasedato = strtolower( $institucion->baseDeDatos );

	$wentidad = $institucion->nombre;

	if ($wemp_pmla == 01) {
		encabezado("AGENDA MEDICA", "2021-11-18", $wbasedato );
	} else {
		encabezado("AGENDA MEDICA", "2021-11-18", "logo_".$wbasedato );
	}
		//---------------------------------------------------------FIN ENCABEZADO-----------------------------------------------------------------------------


		//----------------------------------------------------CORREGIR CEDULAS EN LA AGENDA-------------------------------------------------------------------
		updateWrongIdentifications($conex, $wemp_pmla, $solucionCitas, $valCitas, $caso);
		//--------------------------------------------------FIN CORREGIR CÉDULAS INCORRECTAS -----------------------------------------------------------------

		//--------------------------------------------------------SELECTOR DE ROLES Y UBICACIONES-------------------------------------------------------------
		$htmlSelectors = getSelectors($solucionCitas, $monitor, $conex, $wemp_pmla);
		//---------------------------------------------------------------FIN SELECTORES ----------------------------------------------------------------------
			//html con selectores de rol, ubicación y filtro por medico o equipo
		echo "
			<table class='encabezadotabla' align='center' width='40%'>

			<tr>
		";
		if($htmlSelectors != ""){
				echo "<td>Rol</td>
				<td>Ubicacion</td>
			";
		}
		
		
		
		

		//---------------------------------------------------- CREAR LISTADO DE MEDICOS O EQUIPOS ------------------------------------------------------------
		if (!isset($wfec)) {
			$wfec = date("Y-m-d");
		}

		$horaActSec=time();
		$horaAct=date("H:i", $horaActSec );

		if (!isset($valCitas)) {
			$valCitas = "off";
		}

		if ($caso ==1 and $solucionCitas=='citasca') {
			$tipoAtencion='on';
		} else {
			$tipoAtencion='off';
		}

		if( !isset( $ret ) ){
			$ret = 'off';
		}

		if( isset($asistida) ){
			marcarAsistida( $asistida, $causa, $solucionCitas );
		}

		if( isset($admision) )	{
			guardarCausaAdmision( $admision, $causa, $solucionCitas );
		}

		//Buscando el doctor por el que fue filtrado
		if( !isset( $slDoctor ) ){
			$nmFiltro = "% - Todos";
			$filtro   = '%';
			$slDoctor = "% - Todos";
		} else {
			$nmFiltro = $slDoctor;
			$exp      = explode( " - ", $slDoctor);
			$filtro   = $exp[0];
		}


		echo "<form name='pantalla' method=post>";
		echo "<br><br>";

		if ($caso == 2 and $valCitas=="on")
		{
			$sql = "SELECT
				Mednom, Medcod
			FROM
				{$wbasedato}_000051
			WHERE
				Medcid != ''
				AND Medest = 'on'
			ORDER BY Mednom";

		} else if ($caso == 3 or $caso == 1) {
			$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000003 where activo='A' ";

		} else if ($caso == 2 and $valCitas!="on") {
			$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000010 where activo='A' group by descripcion order by descripcion";
		}

		$res1 = executeQuery( $sql );

		if ($caso == 2)	{
			echo "	<td class='encabezadotabla' align=center>Filtro por Profesional</td>";			

		} else {
			echo "	<td class='encabezadotabla' align=center>Filtro por Equipo</td>";
		}

		echo "	</tr>";
		echo "	<tr>";

		if($htmlSelectors != ""){
			echo $htmlSelectors;
		}

        echo "  <td class='fila1' style='width: 600px;'><select id='slDoctor' name='slDoctor' multiple='multiple' style='width: 600px;height: 400px;' onchange='javascript: document.forms[0].submit();'>";
        // -> 2020-01-08   Se modifica select a multiselect para la utilización del filtro por texto clave
		//echo "	<td class='fila1'><select name='slDoctor' onchange='javascript: document.forms[0].submit();'>";
		echo "	<option>% - Todos</option>";

		for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ ){

			if ($caso == 2 and $valCitas=="on") {
				$rows['Medcod'] = trim( $rows['Medcod'] );
				$rows['Mednom'] = trim( $rows['Mednom'] );

				if( $slDoctor != trim( $rows['Medcod'] )." - ".trim( $rows['Mednom'] ) ) {
					echo "<option>{$rows['Medcod']} - {$rows['Mednom']}</option>";
				} else {
					echo "<option selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
				}

			} else if ($caso == 1 or $caso == 3) {
				$rows['codigo'] = trim( $rows['codigo'] );
				$rows['descripcion'] = trim( $rows['descripcion'] );

				if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) ) {
					echo "<option>{$rows['codigo']} - {$rows['descripcion']}</option>";
				} else {
					echo "<option selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
				}

			} else if ($caso == 2 and $valCitas!= "on") {
				$rows['codigo'] = trim( $rows['codigo'] );
				$rows['descripcion'] = trim( $rows['descripcion'] );

				if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) ) {
					echo "<option>{$rows['codigo']} - {$rows['descripcion']}</option>";
				} else {
					echo "<option selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
				}
			}
		}//for

		echo "</select>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		//-------------------------------------------------FIN LISTADO DE MEDICOS O EQUIPOS ------------------------------------------------------------


		//--------------------------------------LISTADO DE PACIENTES CON TURNO SIN CITA O CON LOS DATOS ERRONES-----------------------------------------------
		//se carga el contenido mediante ajax cuando se selecciona perfil y ubicación
		echo "<div id='divListaPacientesConTurno'> </div>";
		//----------------------------------------------------------FIN LISTADO-------------------------------------------------------------------------------

		//-------------------------------------- SELECTOR DE FECHA DE LA AGENDA  ----------------------------------
		$dia 	= 	date("l",strtotime( $wfec ));
		$diaNum =	date("d",strtotime( $wfec ));
		$mes 	= 	date("F",strtotime( $wfec ));
		$anio 	=	date("Y",strtotime( $wfec ));

		// Obtenemos y traducimos el nombre del día
		if ($dia=="Monday") $dia="Lunes";
		if ($dia=="Tuesday") $dia="Martes";
		if ($dia=="Wednesday") $dia="Mi&eacute;rcoles";
		if ($dia=="Thursday") $dia="Jueves";
		if ($dia=="Friday") $dia="Viernes";
		if ($dia=="Saturday") $dia="Sabado";
		if ($dia=="Sunday") $dia="Domingo";

		// Obtenemos y traducimos el nombre del mes
		if ($mes=="January") $mes="Enero";
		if ($mes=="February") $mes="Febrero";
		if ($mes=="March") $mes="Marzo";
		if ($mes=="April") $mes="Abril";
		if ($mes=="May") $mes="Mayo";
		if ($mes=="June") $mes="Junio";
		if ($mes=="July") $mes="Julio";
		if ($mes=="August") $mes="Agosto";
		if ($mes=="September") $mes="Septiembre";
		if ($mes=="October") $mes="Octubre";
		if ($mes=="November") $mes="Noviembre";
		if ($mes=="December") $mes="Diciembre";

		//tabla para navegar en las fechas de las citas
		$wfecAnt = date( "Y-m-d", strtotime($wfec) - 24*3600 );
		$wfecSig = date( "Y-m-d", strtotime($wfec) + 24*3600 );

		echo "<br>";
		//echo "<table border='0' align='center'>";
		//echo "<th class='encabezadotabla' colspan='3'>Seleccione la fecha:</th>";
		//echo "</table>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td><a href='javascript:' onclick='obtenerMonitor(this);' href2='../../citas/procesos/pantallaAdmision.php?solucionCitas=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&ccosto=".$ccosto."&caso=".$caso."&wsw=".@$wsw."&wfec=".$wfecAnt."&valCitas=".@$valCitas."&fest=".$fest."' title='Atras'><img src='../../images/medical/citas/atras.jpg' alt='Atras'  height='30' width='30' border=0/></a></td>";

		if ($wfec == date("Y-m-d"))	{
			$hoy = "Hoy:";
		} else {
			$hoy = "";
		}

		echo "<td class='fila1' ><font size='4'><b>".$hoy."</b> ".$dia." ".$diaNum." de ".$mes ." de ".$anio."</font></td>";
		echo "<td><a href='javascript:' onclick='obtenerMonitor(this);' href2='../../citas/procesos/pantallaAdmision.php?solucionCitas=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&ccosto=".$ccosto."&caso=".$caso."&wsw=".@$wsw."&wfec=".$wfecSig."&valCitas=".@$valCitas."&fest=".$fest."' title='Adelante'><img src='../../images/medical/citas/adelante.jpg' alt='Adelante'  height='30' width='30' border=0/></a></td>";
		echo "</tr>";
		echo "</table>";

		echo "<br><br>";
		//----------------------------------------------------FIN SELECTOR FECHA DE LA AGENDA -------------------------------------------------------------------


		//--------------------------------------------------ENLACE PARA IR A LA AGENDA DE CITAS------------------------------------------------------------------
		echo "<center>
				<a onclick='javascript:abrirVentanCitas(\"$solucionCitas\",\"$wemp_pmla\",\"$caso\",\"$wsw\",\"$fest\")'  style='color:#1e90ff;cursor:pointer'>Crear nueva cita</a>
			  </center>
		";
		//------------------------------------------------FIN ENLACE PARA IR A LA AGENDA DE CITAS------------------------------------------------------------------


		//----------------------------------------------------------DIV RECARGAR PAGINA----------------------------------------------------------------------------
		echo "<br>";
		echo "<div id='divRecargarPagina' style='display:none;  background-color: #f2dede; border-color: #ebccd1; color: #b94a48; border: 1px solid transparent;  border-radius: 4px; margin-bottom: 20px; padding: 15px' align='left'>
				<b><span>Es necesario recargar la paginá para que sean actualizados los datos de un paciente.</span></b>
				<input type='button' onClick='javascript:recargarPagina()' value='Recargar'>
			  </div>";
		//--------------------------------------------------------FIN DIV RECARGAR PAGINA----------------------------------------------------------------------------


		//-----------------------------------------------------TABLA PRINCIPAL DE CITAS ----------------------------------------------------------------------------
		echo "<br><div id='divTablaAgenda'>";

		//primero se consulta si hay citas para el día
		$res = getListAppointments($solucionCitas, $caso, $valCitas, $wfec, $filtro);
		$num = mysql_num_rows($res);

		if($num > 0){
			echo "<table id='tablaAgenda' align='center'>";

			//encabezado tabla
			$htmlHeadTable = getHeadPrincipalTable($caso , $tipoAtencion, $wfec, $valCitas, $solucionCitas);
			echo $htmlHeadTable;

			//Cuerpo tabla
			$htmlBodyTable = getBodyTable($res, $caso , $tipoAtencion, $wfec, $valCitas, $solucionCitas, $wbasedato);
			echo $htmlBodyTable;

			echo "</table>";
		} else {
			echo "<center>NO HAY CITAS ASIGNADAS PARA HOY</center>";
		}
		//--------------------------------------------------------FIN TABLA PRINCIPAL --------------------------------------------------------------------------------

		echo "<br>";

		//-------------------------------------------- ENLACE PARA CREAR CITA NUEVA ------------------------------------------------------------------------------------------
		echo "<center>
				<a onclick='javascript:abrirVentanCitas(\"$solucionCitas\",\"$wemp_pmla\",\"$caso\",\"$wsw\",\"$fest\")'  style='color:#1e90ff; cursor:pointer'>Crear nueva cita</a>
			</center>
		";

		//-------------------------------------------DIVS DE CAUSAS CANCELACIÓN, NO ASISTE, DEMORA.

		//div causa cancelacion
		echo "<div id='causa_cancelacion' style='display:none'>";
		echo "<center>";
		$tipo = "C";
		echo causas($tipo);
		echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
		echo "</center>";
		echo "</div>";

		//div causa no asiste
		echo "<div id='causa_noasiste' style='display:none'>";
		echo "<center>";
		$tipo = "NA";
		echo causas($tipo);
		echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
		echo "</center>";
		echo "</div>";

		//div causa demora
		echo "<div id='causa_demora' style='display:none'>";
		echo "<center>";
		$tipo = "DA";
		echo causas($tipo);
		echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
		echo "</center>";
		echo "</div>";

		if ($caso == 2 and $valCitas == "on") {
			echo "<br><br>";
			echo "<center><a href='../../IPS/Procesos/admision.php?ok=9&empresa=$wbasedato&wemp2=citascs' target='_blank'>Admision sin cita</a></center>";
		}
		

		echo "<br>";
		echo "<meta name='met' id='met' url=pantallaAdmision.php?solucionCitas=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&ccosto=".$ccosto."&caso=".$caso."&wsw=".@$wsw."&slDoctor=$slDoctor&valCitas=".$valCitas."&wfec=".$wfec."&fest=".$fest.">";

		echo "<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' />";
		echo "<br><br>";

		echo "<table align='left'><tr><td><a onclick='javascript:imprimir(\"$wemp_pmla\",\"$caso\",\"$wsw\",\"$solucionCitas\",\"$slDoctor\",\"$valCitas\",\"$wfec\")'><b>Imprimir</b></a></td></tr></table>";

		echo "</form>";
		echo "<div id='fichoTurno' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'></div>";
		echo "<div id='fichoTurnoImp' style='display:none;'></div>";

		echo "<div id='divAsociarTurnoCita' title='ASOCIAR PACIENTES CON TURNO Y SIN CITA' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'>
				</div>";
		echo "<br><br><br><br>";

		echo "<div id='div_reiniciar_procedimiento' style='display:none;background-color: #FFFFFF;border:1px dotted #AFAFAF;' align='center'>
		</div>";
		
		//Se cierra conexión de la base de datos :)
		//Se comenta cierre de conexion para la version estable de matrix :)
		//mysql_close($conex);
	}
?>
</body>
</html>