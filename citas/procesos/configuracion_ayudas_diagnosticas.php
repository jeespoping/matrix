<?php
include_once("conex.php");
	session_start();
	

	include_once("root/comun.php");
	include_once("citas/funcionesAgendaCitas.php");
	
	$wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
	

	$conex = obtenerConexionBD("matrix");

	/////////////////////////////////////////////////////////////////FUNCIONES PHP LLAMADAS POR AJAX
	
	if(isset($consultaAjax)){
		
		if(isset($action) && $action === "loadInformationDivs"){
			$arrayResults = array();
			$centroCosto = "citasen";
			$arrayResults["listRoles"] = getListRoles($centroCosto);
			$arrayResults["listLocation"] = getListLocations($centroCosto);
			$arrayResults["listActivities"] = getListActivities($centroCosto);
			$arrayResults["listActions"] = getListActions($centroCosto);
			$arrayResults["optionsRoles"] = getOptionsRoles($centroCosto);
			$arrayResults["optionsLocations"] = getOptionsLocations($centroCosto);
			$arrayResults["listRolLocations"] = getListRolLocations($centroCosto);
			$arrayResults["optionsActions"] = getOptionsActions($centroCosto);
			$arrayResults["optionsActivities"] = getOptionsActivities($centroCosto);			
			$arrayResults["listRelActivityActions"] = getListRelActivityActions($centroCosto);
			$arrayResults["table"] = getDesignFinalTable($centroCosto);
			$arrayResults["listCco"] = getListCco();
			$arrayResults["listCcoOptions"] = getListCcoOptions();
			
			echo json_encode($arrayResults);
			exit();
		}	
			
		if(isset($action) && $action === "saveRol"){			
			$params = array();
			parse_str($valueForm, $params);	
			$response = saveRol("citasen", $params);

			echo json_encode($response);
			exit();
		}	
		
		if(isset($action) && $action === "saveLct"){			
			$params = array();
			parse_str($valueForm, $params);			
			$response = saveLct("citasen", $params);

			echo json_encode($response);
			exit();
		}	
		
		if(isset($action) && $action === "saveNewAct"){			
			$params = array();
			parse_str($valueForm, $params);			
			$response = saveNewAct("citasen", $params, $Procod);

			echo json_encode($response);
			exit();
		}
		
		if(isset($action) && $action === "saveTypeAction"){		
			
			$params = $_POST;				
			$params["file"] = isset($_FILES["Actimg"]) ? $_FILES["Actimg"] : "";
			$response = saveTypeAction("citasen", $params);

			echo json_encode($response);
			exit();
		}
		
		if(isset($action) && $action === "saveRelRolLct"){			
			$params = array();
			parse_str($valueForm, $params);			
			$response = saveRelRolLct("citasen", $params);

			echo json_encode($response);
			exit();
		}
				
		if(isset($action) && $action === "saveRelActAcn"){			
			$params = array();
			parse_str($valueForm, $params);			
			$response = saveRelActAcn("citasen", $params);

			echo json_encode($response);
			exit();
		}
		
		
		if(isset($action) && $action === "createTable"){			
				
			$response = createPrincipalTable("citasen");

			echo json_encode($response);
			exit();
		}
		
		if(isset($action) && $action === "getLocationRol"){		

			$response = getOptionsLocations($cco, $rol);

			echo json_encode($response);
			return;
		}
		
		if(isset($action) && $action === "getOptionsRelActions"){		

			$response = getOptionsRelActions($cco, $valAct);
			
			echo json_encode($response);
			return;
		}
		
		if(isset($action) && $action === "updatePrincipalTable"){		

			$response = updatePrincipalTable($cco);
			
			echo json_encode($response);
			return;
		}
		
		if(isset($action) && $action === "saveCco"){
			$params = array();			
			parse_str($valueForm, $params);	
			$response = saveCco($params);
			
			echo json_encode($response);
			return;
		}
		
	} else {
		
		if(!isset($_SESSION['user']) ){
			echo "<br /><br /><br /><br />
					  <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
						  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
					 </div>";
			return;
		}
		
		$wtitulo = "CONFIGURACIÓN SISTEMA DE AYUDAS DIAGNÓSTICAS";
		encabezado($wtitulo, $wactualiz, 'clinica');
		
		//vAriables a usar en todo el archivo
		$user = $_SESSION['user'];

		
		

?>

<html>
<head>
<style type="text/css">
	.fila3 {	
		background-color: #f4d03f;
		color: #000000;
		font-size: 10pt;
	}
	body {
		width: 97%
	}
	
</style>
<!--scripts necesarios -->
<title>CONFIGURACIÓN </title>
	
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
	<link type="text/css" href="../../Jquery/ui.all.css" rel="stylesheet" />
  	
	<script type="text/javascript" src="../../Jquery/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="../../Jquery/ui.core.js"></script>
	<script type="text/javascript" src="../../Jquery/ui.tabs.js"></script>
	<script type="text/javascript" src="../../Jquery/ui.draggable.js"></script>
	
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/bootstrap.min.js"></script>

	<!-- script css del archivo-->
	<link type="text/css" rel="stylesheet" href="../../../include/citas/configuracion_ayudas.css">
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

	<script type="text/javascript">
		$(document).ready(function(){
			
			//Se carga el menú de centros de costo
						
			$("#tabs").tabs(); //JQUERY:  Activa los tabs para las secciones del kardex
					
			//Guardar nuevo rol
			$('#btnSaveRol').click(function(){
				if($("#Moncod").val() != "" && $("#Mondes").val() != "" && $("#cod_cco").val() != ""){
					
					var valueForm = $("#formNewRol").serialize();
					$.post("configuracion_ayudas_diagnosticas.php",
					{
						consultaAjax:   		'on',
						action:         		'saveRol',
						valueForm:         		valueForm,
						wemp_pmla:        		$("#wemp_pmla").val(),
						cco:        			$("#cod_cco").val()		
						}, function(respuesta){
							var objRespuesta = $.parseJSON(respuesta);
							
							if(objRespuesta.error === true){
								alert(objRespuesta.message);
							}else{
								//Se limpia el formulario
								clearForm("formNewRol");
								loadInformationDivs();
							}
					});	
				}else{
					alert("Recuerde que siempre debe seleccionar primero un centro de costo y llenar todos los datos del formulario");
				}
							
			});	

			//Guardar nueva ubicación
			$('#btnSaveLct').click(function(){
				if($("#Ubicod").val() != "" && $("#Ubides").val() != "" && $("#cod_cco").val() != ""){
					
					var valueForm = $("#formNewLocation").serialize();
					$.post("configuracion_ayudas_diagnosticas.php",
					{
						consultaAjax:   		'on',
						action:         		'saveLct',
						valueForm:         		valueForm,
						wemp_pmla:        		$("#wemp_pmla").val(),
						cco:        			$("#cod_cco").val()		
						}, function(respuesta){
							var objRespuesta = $.parseJSON(respuesta);
							
							if(objRespuesta.error === true){
								alert(objRespuesta.message);
							}else{
								//Se limpia el formulario
								clearForm("formNewLocation");
								loadInformationDivs();
							}
					});		
				}else{
					alert("Recuerde que siempre debe seleccionar primero un centro de costo y llenar todos los datos del formulario");
				}				
			});	

			//ENVIA FORMULARIO PARA GUARDAR UNA NUEVA ACTIVIDAD
			$('#btnSaveNewAct').click(function(){
				if($("#Procod").val() != "" && $("#Prodes").val() != "" && $("#cod_cco").val() != ""){
					
					var valueForm = $("#formNewActivity").serialize();
					$.post("configuracion_ayudas_diagnosticas.php",
					{
						consultaAjax:   		'on',
						action:         		'saveNewAct',
						valueForm:         		valueForm,
						wemp_pmla:        		$("#wemp_pmla").val(),
						cco:        			$("#cod_cco").val(),	
						Procod:        			$("#Procod").val()
						}, function(respuesta){
							var objRespuesta = $.parseJSON(respuesta);
							
							if(objRespuesta.error === true){
								alert(objRespuesta.message);
							}else{
								//Se limpia el formulario
								clearForm("formNewActivity");
								$('#selBeforeActivity_0').empty();
								$("#RolesActivity").html("");
								$("#moreActivities").html("");								
								loadInformationDivs();
							}
					});	
				}else{
					alert("Recuerde que siempre debe seleccionar primero un centro de costo y llenar todos los datos del formulario");
				}				
			});	
			
			//Guardar Nueva accion			
			 $("#formTypeAction").on("submit", function(e){
				if($("#Actcod").val() != "" && $("#Actnma").val() != "" && $("#Actdes").val() != "" && $("#cod_cco").val() != ""){
					
					e.preventDefault();
					var f = $(this);
					var formData = new FormData(document.getElementById("formTypeAction"));
					var inputFileImage = document.getElementById("Actimg");
			
					formData.append("Actimg", inputFileImage.files[0]);
					formData.append("consultaAjax", "on");
					formData.append("action", "saveTypeAction");
				
					$.ajax({
						url: "configuracion_ayudas_diagnosticas.php",
						type: "post",
						dataType: "html",
						data: formData,
						cache: false,
						contentType: false,
					processData: false
					})
						.done(function(respuesta){
							var objRespuesta = $.parseJSON(respuesta);
							
							if(objRespuesta.error === true){
								alert(objRespuesta.message);
							}else{
								//Se limpia el formulario
								clearForm("formTypeAction");
								loadInformationDivs();
							}
						});
				}else{
					alert("Recuerde que siempre debe seleccionar primero un centro de costo y llenar todos los datos del formulario");
				}	
			});
			
			//Guard la relacion rol ubicacion
			$('#btnSaveRelRolLct').click(function(){
				if($("#selRol").val() != "" && $('.rolSel').prop('checked') ){
					
					var valueForm =$("#formRelRolLct").serialize();;
					
					$.post("configuracion_ayudas_diagnosticas.php",
					{
						consultaAjax:   		'on',
						action:         		'saveRelRolLct',
						valueForm:         		valueForm,
						wemp_pmla:        		$("#wemp_pmla").val(),
						cco:        			$("#cod_cco").val()		
						}, function(respuesta){
							var objRespuesta = $.parseJSON(respuesta);
							
							if(objRespuesta.error === true){
								alert(objRespuesta.message);
							}else{							
								loadInformationDivs();
							}
					});	
				}else{
					alert("Recuerde que siempre debe seleccionar primero un centro de costo y llenar todos los datos del formulario");
				}				
			});	
			
			
			//GUARDA LA RELACIÓN ENTRE ACTIVIDAD y ACCION
			$('#btnSaveRelActAcn').click(function(){
				if($("#Raccod").val() != "" && $("#selActivity").val() != "" && $("#selOrder").val() != "" && $("#Racnam").val() != "" 
				&& ($('input:radio[name=Racacn]:checked').val() != undefined) && $("#cod_cco").val() != ""){
					
					var valueForm =$("#formRelActAcn").serialize();;
					
					$.post("configuracion_ayudas_diagnosticas.php",
					{
						consultaAjax:   		'on',
						action:         		'saveRelActAcn',
						valueForm:         		valueForm,
						wemp_pmla:        		$("#wemp_pmla").val(),
						cco:        			$("#cod_cco").val()		
						}, function(respuesta){
							var objRespuesta = $.parseJSON(respuesta);
							
							if(objRespuesta.error === true){
								alert(objRespuesta.message);
							}else{		
								clearForm("formRelActAcn");						
								loadInformationDivs();
							}
					});	
				}else{
					alert("Recuerde que siempre debe seleccionar primero un centro de costo y llenar todos los datos del formulario");
				}				
			});	
			
			//Guarda un nuevo centro de costo
			$('#btnSaveCco').click(function(){
				if($("#nombreCc").val() != "" && $("#descripcion").val() != ""){
					
					var valueForm = $("#formNewCco").serialize();;
					
					$.post("configuracion_ayudas_diagnosticas.php",
					{
						consultaAjax:   		'on',
						action:         		'saveCco',
						valueForm:         		valueForm,
						wemp_pmla:        		$("#wemp_pmla").val()
						}, function(respuesta){
							var objRespuesta = $.parseJSON(respuesta);
							
							if(objRespuesta.error === true){
								alert(objRespuesta.message);
							}else{		
								clearForm("formNewCco");						
								loadInformationDivs();
							}
					});	
				}else{
					alert("Recuerde ingresar todos los datos");
				}				
			});	
			
			$('#selOrder').change(function(){
				var val= $(this).val();
				
				if(val == 1){
					$("#selBeforeAction").attr("disabled", true);
				}else{
					$("#selBeforeAction").attr("disabled", false);
				}
			});	
			
			//GUARDA LA RELACIÓN ENTRE ACTIVIDAD y ACCION
			$('#selRol').change(function(){
				$(".rolSel").attr('checked', false);
				
				$.post("configuracion_ayudas_diagnosticas.php",
				{
					consultaAjax:   		'on',
					action:         		'getLocationRol',
					rol:         			$('#selRol').val(),
					wemp_pmla:        		$("#wemp_pmla").val(),
					cco:        			$("#cod_cco").val()		
					}, function(respuesta){							
						jQuery.each(respuesta, function(){						
							$("#rol"+this.code).attr('checked', true);
						});	
				}, 'json');					
			});	
						
			
			//Realiza la petición para crear la tabla principal
			/*$('#btnCreateTable').click(function(){
				
				alert("dasdad");
				$.post("configuracion_ayudas_diagnosticas.php",
				{
					consultaAjax:   		'on',
					action:         		'createTable',
					wemp_pmla:        		$("#wemp_pmla").val(),
					cco:        			$("#cod_cco").val()		
					}, function(respuesta){
						var objRespuesta = $.parseJSON(respuesta);
						
						if(objRespuesta.error === true){
							alert(objRespuesta.message);
						}else{							
							alert("Se ha creado la tabla")
						}
				});				
			});	*/
			
			//AGREGA ACTIVIDADES DE FORMA DINÁMICA PARA EL CAMPO (ANTERIOR ACTIVIDAD) EN EL FORMULARIO CREAR ACTIVIDAD
			$('#addActivities').click(function(){
								
				var cont = $("#contActivities").val()*1;
				var newCont = parseInt(cont+1);
				
				setSelectOperator(newCont);
				createSelect(newCont);
				
				$("#contActivities").val(newCont);				
			});	
			
			//
			$('#selActivity').change(function(){
			
				$.post("configuracion_ayudas_diagnosticas.php",
				{
					consultaAjax:   		'on',
					action:         		'getOptionsRelActions',
					wemp_pmla:        		$("#wemp_pmla").val(),
					cco:        			$("#cod_cco").val(),
					valAct:					$(this).val()
					}, function(respuesta){
						$('#selBeforeAction').empty();
						setOptionBeforeAction(respuesta);
						
				}, 'json');					
			
			});	
			
			$( "#updatePrincipalTable" ).live( "click", function() {
				$.post("configuracion_ayudas_diagnosticas.php",
					{
						consultaAjax:   		'on',
						action:         		'updatePrincipalTable',
						wemp_pmla:        		$("#wemp_pmla").val(),
						cco:        			$("#cod_cco").val()
						}, function(respuesta){
						
							alert(respuesta.message);													
				}, 'json');		
			});
			
			$('#clearRol').click(function(){
				clearForm("formNewRol");
				$("#Moncod").prop('disabled', false);
			});
			$('#clearLocation').click(function(){
				clearForm("formNewLocation");
				$("#Ubicod").prop('disabled', false);
			});
			$('#clearRelRolLoc').click(function(){
				clearForm("formRelRolLct");
			});			
			$('#clearAct').click(function(){
				clearForm("formNewActivity");
				$("#Procod").prop('disabled', false);
			});
			$('#clearTypeAct').click(function(){
				clearForm("formTypeAction");
				$("#Actcod").prop('disabled', false);
			});
			$('#clearRelActionAct').click(function(){
				clearForm("formRelActAcn");
				$("#Raccod").prop('disabled', false);
			});	
			
			$('#clearCco').click(function(){
				clearForm("formNewCco");
			});	
			
			
		});
		
		function createSelect(cont, valueSel=""){			
			
			$('<select/>', {
					'id': 'selBeforeActivity_'+cont,				
					'name': 'selBeforeActivity_'+cont,				
			}).appendTo('#moreActivities');
															
			$('#selBeforeActivity_0').find('option').clone().appendTo('#selBeforeActivity_'+cont);
			
			$('<br/>', {}).appendTo('#moreActivities');
			
			if(valueSel != "") {
				$("#selBeforeActivity_"+cont+" option[value="+ valueSel +"]").attr("selected",true);
			}
		}
			
		function setSelectOperator(cont, valueSel=""){
			var id = 'operator_'+cont;
			
			$('<select/>', {
					'id': id,				
					'name': id,				
			}).appendTo('#moreActivities');
			
			$('<option/>', {
					'value': 'AND',
					'text': 'Y'
			}).appendTo('#'+id);
				
			$('<option/>', {
					'value': 'OR',
					'text': 'O'
			}).appendTo('#'+id);	

			if(valueSel != "") {
				$("#operator_"+cont+" option[value="+ valueSel +"]").attr("selected",true);
			}			
		}
			
		function clearForm(nameForm){
			$('#'+nameForm).each (function(){
				this.reset();
			});
		}
		
		function loadInformationDivs(CodeCco, nameCco=""){
		
			if(nameCco != ""){
				$("#cod_cco").val(nameCco);
			}
						
			$.post("configuracion_ayudas_diagnosticas.php",
			{
			consultaAjax:   		'on',
			action:         		'loadInformationDivs',
			wemp_pmla:        		$("#wemp_pmla").val(),
			cco:        			CodeCco			
			}, function(respuesta){
				clearSelectCheckbox();
				clearSelectRelActAcc();
				$("#RolesActivity").html("");
				
				var objRespuesta = $.parseJSON(respuesta);
					
				//carga div listado de roles
				setTableRoles(objRespuesta.listRoles);
					
				//Carga div listado de ubicaciones
				setTableLocations(objRespuesta.listLocation);
					
				//Carga div listado de actividades
				setTableActivities(objRespuesta.listActivities);
					
				//Carga div listado de acciones
				setTableActons(objRespuesta.listActions);
					
				//Carga div listado de acciones
				setTableRolLocations(objRespuesta.listRolLocations);
				
				//Carga div listado de acciones
				setTableRelActivityAction(objRespuesta.listRelActivityActions);
									
				//para cargar selectores de formulario Relacion rol ubicacion
				createOptionsSelecRolUbication(objRespuesta.optionsRoles, objRespuesta.optionsLocations);	

				setOptionsActionsActivities(objRespuesta.optionsActions, objRespuesta.optionsActivities);
				
				setOptionsBeforeActivities(objRespuesta.optionsActivities);	
				
				setPrincipalTable(objRespuesta.table);	
				
				setRolesActivity(objRespuesta.optionsRoles);
				
				setTableCco(objRespuesta.listCco);
				
				setOptionsCco(objRespuesta.listCcoOptions);
				
			});
		}
		
		function setOptionsCco(ccoOptions){
			
			jQuery.each(ccoOptions, function(){	
				$('<option/>', {
					'value': this.code + "-" + this.description,
					'text': this.description
				}).appendTo('#selCco');
			});
		}
		
		function setRolesActivity(roles){
			jQuery.each(roles, function(){
				var container = $('#RolesActivity');
				var inputs = container.find('input');
				var id = this.code;
				
				$('<input />', { type: 'checkbox', id: 'rolAct'+id, 'class':'rolActSel', name: 'rolesActivity[]', value: this.code }).appendTo(container);
				$('<label />', { 'for': '', text: this.description }).appendTo(container);
				$('<br />', {}).appendTo(container);
				
			});
		}
		
		function setPrincipalTable(html){
			$("#designPrincipalTable").html(html);
		}
		
		function setTableCco(html){
			$("#listCco").html(html);
		}
						
		function createOptionsSelecRolUbication(roles, locations){
			
			jQuery.each(roles, function(){				
				$('<option/>', {
					'value': this.code,
					'text': this.description
				}).appendTo('#selRol');
			});
			
			jQuery.each(locations, function(){
				var container = $('#optionLocations');
				var inputs = container.find('input');
				var id = this.code;
				
				$('<input />', { type: 'checkbox', id: 'rol'+id, 'class':'rolSel', name: 'locations[]', value: this.code }).appendTo(container);
				$('<label />', { 'for': 'rol'+id, text: this.description }).appendTo(container);
				$('<br />', {}).appendTo(container);
				
			});	
		}
		
		
		function clearSelectCheckbox(){
			$('#selRol').empty();
			$('#optionLocations').html("");
		}
				
		function clearSelectRelActAcc(){
			$('#selActivity').empty();
			$('#optionsActions').html("");
			$("#selBeforeAction").empty();
		}
		
		function setTableRoles(htmlTable){
			$("#listRoles").html(htmlTable);
		}
		
		function setTableLocations(htmlTable){
			$("#listLocations").html(htmlTable);
		}
		
		function setTableActivities(htmlTable){
			$("#listActivities").html(htmlTable);
		}
		
		function setTableActons(htmlTable){
			$("#listActions").html(htmlTable);
		}
		
		function setTableRolLocations(htmlTable){
			$("#listRelRolLocations").html(htmlTable);
		}
		
		function setTableRelActivityAction(htmlTable){
			$("#listRelActivityAction").html(htmlTable);
		}
				
		function setOptionsActionsActivities(actions, activities){
			var container = $('#optionsActions');
			
			jQuery.each(actions, function(){
				
				var inputs = container.find('input');
				var id = this.code;
							
				$('<input />', { type: 'radio', id: 'action'+id, name: 'Racacn', value: this.code }).appendTo(container);		
				$('<label />', { 'for': 'cb'+id, text: this.description }).appendTo(container);			
				if(this.url != ""){
					$('<img />', { 'src': this.url, width:'30px', height:'30px'}).appendTo(container);
				}
				$('<br />', {}).appendTo(container);						
			});	
					
			$('<option/>', {
					'value': '',
					'text': 'Seleccione'
			}).appendTo('#selActivity');				
				
			jQuery.each(activities, function(){		
			
				$('<option/>', {
					'value': this.code,
					'text': this.description
				}).appendTo('#selActivity');
			});			
		}
		
		function setOptionBeforeAction(actions){
			$('<option/>', {
					'value': '',
					'text': 'Seleccione'
			}).appendTo('#selBeforeAction');
				
			jQuery.each(actions, function(){				
				$('<option/>', {
					'value': this.code,
					'text': this.description
				}).appendTo('#selBeforeAction');
			});
		}
		
		function setOptionsBeforeActivities(activities){
			$('<option/>', {
					'value': '',
					'text': 'Seleccione'
			}).appendTo('#selBeforeActivity_0');
									
			jQuery.each(activities, function(){		
			
				$('<option/>', {
					'value': this.code,
					'text': this.description
				}).appendTo('#selBeforeActivity_0');				
			});	
		}
		
		function updateRol(id, code, description, status){
			
			$("#idUpdate").val(id);			
			$("#Moncod").val(code);
			$("#Moncod").prop('disabled', true);
			$("#Mondes").val(description);
			
			if(status == "on"){
				$("input[name=Monest][value='on']").prop("checked",true);
			}else{
				$("input[name=Monest][value='off']").prop("checked",true);
			}			
		}
		
		function updateLct(id, code, description, status, showMonitor){
			
			$("#idUpdateLct").val(id);			
			$("#Ubicod").val(code);
			$("#Ubicod").prop('disabled', true);
			$("#Ubides").val(description);
			
			if(status == "on"){
				$("input[name=Ubiest][value='on']").prop("checked",true);
			}else{
				$("input[name=Ubiest][value='off']").prop("checked",true);
			}	

			if(showMonitor == "on"){
				$("input[name=Ubimta][value='on']").prop("checked",true);
			}else{
				$("input[name=Ubimta][value='off']").prop("checked",true);
			}			
		}
		
		
		function updateTypeAction(id, code, name, description, type, status){
			
			$("#idUpdateTypeAction").val(id);			
			$("#Actcod").val(code);
			$("#Actcod").prop('disabled', true);
			$("#Actnma").val(name);
			$("#Actdes").val(description);
			$("#Acttpa").val(type);
					
			if(status == "on"){
				$("input[name=Actest][value='on']").prop("checked",true);
			}else{
				$("input[name=Actest][value='off']").prop("checked",true);
			}			
		}
		
		function updateRelActivityAction(id, activity, order, name, action, befaction, status, code, aditionalAction, Ractex, Racejs, Racfpr){
			$("#idUpdateRelActionAct").val(id);			
			$("#selActivity").val(activity);
			$("#selOrder").val(order);
			$("#Racnam").val(name);		
			$("#selBeforeAction").val(befaction);
			$("#Raccod").val(code);
			$("#Raccod").prop('disabled', true);
			$("#Racead").val(aditionalAction);
			$("#Ractex").val(Ractex);
			$("#Racejs").val(Racejs);
					
			//Tipo de acción 
			$("#action"+action).prop("checked",true);	
			
			//Estado de la acción
			if(status == "on"){
				$("input[name=Racest][value='on']").prop("checked",true);
			}else{
				$("input[name=Racest][value='off']").prop("checked",true);
			}	
			
			//La acción puede terminar el proceso			
			if(Racfpr == "on"){
				$("input[name=Racfpr][value='on']").prop("checked",true);
			}else{
				$("input[name=Racfpr][value='off']").prop("checked",true);
			}	
		}
		
		
		function updateActivity(id,code, description, status, codesRoles, befactions, showReport){
			
			$("#moreActivities").html("");	
			$("#selBeforeActivity_0 [value=''").attr('selected',true);	
			
			$("#idUpdateActivity").val(id);
			$("#Procod").val(code);
			$("#Procod").prop('disabled', true);
			$("#Prodes").val(description);
			
			//befactions
			var bfrActivities = befactions.split(",");	
			var cont = 0;
			var cont2 = 0;
			jQuery.each(bfrActivities, function(index){						
				
				if(index === 0){					
					$("#selBeforeActivity_0 [value="+ this +"]").attr("selected",true);	
					cont = 1;
				}else{
					if((this == 'AND') || (this == 'OR')){
						setSelectOperator(cont, this);
					}else{
						createSelect(cont, this);	
						cont2 = parseInt(cont2+1);	
						cont = parseInt(cont+1);						
					}
				}	

				//Se configura el input oculto que contabiliza cuantos selectores fueron agregados
				$("#contActivities").val(cont2);
			});	
								
			//roles
			$(".rolActSel").prop("checked", false);	
			
			var roles = codesRoles.split(", ");
			jQuery.each(roles, function(){						
				$("#rolAct"+this).prop("checked", true);
			});				
			
			if(status == "on"){
				$("input[name=Proest][value='on']").prop("checked",true);
			}else{
				$("input[name=Proest][value='off']").prop("checked",true);
			}	
			
			if(showReport == "on"){
				$("input[name=Promer][value='on']").prop("checked",true);
			}else{
				$("input[name=Promer][value='off']").prop("checked",true);
			}	
			
		}
		
		function updateCco (id, codigo, estado, camillero, turnero, tiempo){
			
			if(estado == "on"){
				$("input[name=estado][value='on']").prop("checked",true);
			}else{
				$("input[name=estado][value='off']").prop("checked",true);
			}	
			
			if(camillero == "on"){
				$("input[name=SolicitaCamillero][value='on']").prop("checked",true);
			}else{
				$("input[name=SolicitaCamillero][value='off']").prop("checked",true);
			}

			if(turnero == "on"){
				$("input[name=UsaTurnero][value='on']").prop("checked",true);
			}else{
				$("input[name=UsaTurnero][value='off']").prop("checked",true);
			}
		}
		
		function crearTable(){	
			var msj = "Esta seguro de crear la tabla principal";
			
			jConfirm(msj,"Confirmacion", function(r) {  
				$.post("configuracion_ayudas_diagnosticas.php",
				{
					consultaAjax:   		'on',
					action:         		'createTable',
					wemp_pmla:        		$("#wemp_pmla").val(),
					cco:        			$("#cod_cco").val()		
					}, function(respuesta){
						var objRespuesta = $.parseJSON(respuesta);
							
						if(objRespuesta.error === true){
								alert(objRespuesta.message);
						}else{					
								loadInformationDivs();
						}
				});	
			});
		}
		
		
	</script>

</head>
<body>

<!--inputs ocultos con variables necesarios-->
<input type="hidden" id="wemp_pmla" value="<?php echo $wemp_pmla; ?>">	
<input type="hidden" id="cod_cco" value="">	

<div class="principalCompleto">
	<div class="principal">	
		<div class="row">
			<div class="partea">			
							
				<div id = 'divgeneral'>
					<span  class="encabezadotabla"> CENTROS DE COSTO</span><br>
					
					<?php
						//Se carga como menú los centros de costos que se puedne congigurar
						getCc();						
					?>				    
				</div>
			</div>
			<div class="parteb">
				<div id="tabs">
					<ul>
						<li><a href='#F_roles'><span>Perfiles</span></a></li>
						<li><a href='#F_location'><span>Ubicaciones</span></a></li>
						<li><a href='#F_reltrollct'><span>Ubicaciones X perfil</span></a></li>
						<li><a href='#F_activities'><span>Actividades</span></a></li>
						<li><a href='#F_actions'><span>Acciones</span></a></li>
						<li><a href='#F_relactactv'><span>Acciones X actividad</span></a></li>									
						<li><a href='#F_createTable'><span>Crear tabla principal</span></a></li>									
						<li><a href='#F_confCco'><span>Configurar centros de costo</span></a></li>									
					</ul>
								
					<br>
					
					<!-- div donde se muestra la información de la pestaña perfiles -->
					<div id="F_roles">
						<div class="partea2">
						<form id="formNewRol">					
							<table align="center" width="40%">
								<tbody>
									<tr class="encabezadotabla">
										<td colspan="2" align="center"><font size="4">Crear perfil</font></td>
									</tr>
									<tr>
										<td class="encabezadotabla" width="40%">Código</td>
										<td class="fila1" width="auto" width="60%"><input type="text" id="Moncod" name="Moncod"></td>
									</tr>
									<tr>
										<td class="encabezadotabla" width="80px">Nombre</td>
										<td class="fila1" width="auto"><input type="text" id="Mondes"  name="Mondes"></td>
									</tr>
									<tr>
										<td class="encabezadotabla">Estado</td>
										<td class="fila1">											
											<input type="radio" name="Monest" value="on"> Activo <br>
											<input type="radio" name="Monest" value="off"> Inactivo
										</td>
									</tr>
									<tr>	
										<input type="hidden" name="idUpdate" id="idUpdate">									
										<td class="encabezadotabla" align="center" colspan="2">
											<input type="button" id="clearRol" value="Limpiar">
											<input type="button" id="btnSaveRol" value="Guardar">											
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						</div>
						<div class="parteb2">
							<!-- El contenido se carga mediante ajax-->
							<div id="listRoles">		
							</div>
						</div>
					</div>

					<!-- div donde se muestra la información de la pestaña ubicaciones -->
					<div id="F_location">
						<div class="partea2">
							<form id="formNewLocation">					
								<table align="center" width="80%">
									<tbody>
										<tr class="encabezadotabla">
											<td colspan="2" align="center"><font size="4">Crear ubicación</font></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="50%">Código</td>
											<td class="fila1" width="50%"><input type="text" id="Ubicod" name="Ubicod"></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Nombre</td>
											<td class="fila1" width="auto"><input type="text" id="Ubides" name="Ubides"></td>
										</tr>
										<tr>
											<td class="encabezadotabla">Estado</td>
											<td class="fila1">										
												<input type="radio" name="Ubiest" value="on"> Activo <br>
												<input type="radio" name="Ubiest" value="off"> Inactivo
											</td>
										</tr>
										<tr>
											<td class="encabezadotabla">Mostrar u ocultar ubicación en televisor</td>
											<td class="fila1">
												<input type="radio" name="Ubimta" value="on"> Mostrar <br>
												<input type="radio" name="Ubimta" value="off"> Ocultar
											</td>
										</tr>
										<tr>
											<input type="hidden" name="idUpdateLct" id="idUpdateLct">	
											<td class="encabezadotabla" align="center" colspan="2">
												<input type="button" id="clearLocation" value="Limpiar">
												<input type="button" id="btnSaveLct" value="Guardar">
											</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
						<div class="parteb2">	
							<!-- El contenido se carga mediante ajax-->
							<div id="listLocations">	
							</div>
						</div>
					</div>

					<!-- div donde se configura las ubicaciones que puede tener un rol -->
					<div id="F_reltrollct">
						<div class="partea2">
							<form id="formRelRolLct">
								<table align="center">
									<tr class="encabezadotabla">
										<td colspan="2" align="center"><font size="4">Relacionar rol con ubicación</font></td>
									</tr>
									<tr>
										<td class="encabezadotabla">Perfil</td>
										<td class="fila1">
											<!-- El contenido se carga mediante ajax-->
											<select id="selRol" name="selRol">
												<option value="">Seleccione</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="encabezadotabla">Ubicaciones</td>
										<td class="fila1">
											<!-- El contenido se carga mediante ajax-->
											<div id="optionLocations">
											</div>
										</td>
									</tr>
									<tr>										
										<td class="encabezadotabla" align="center" colspan="2">
											<input type="button" id="clearRelRolLoc" value="Limpiar">
											<input type="button" id="btnSaveRelRolLct" value="Guardar">
										</td>
									</tr>
								</table>								
							</form>
						</div>
						<div class="parteb2">	
							<!-- El contenido se carga mediante ajax-->
							<div id="listRelRolLocations">	
							</div>
						</div>
					</div>

					<div id="F_activities">
						<div class="partea2">
							<form id="formNewActivity">					
								<table align="center" width="80%">
									<tbody>
										<tr class="encabezadotabla">
											<td colspan="2" align="center"><font size="4">Crear actividad</font></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="50%">Código</td>
											<td class="fila1" width="auto"><input type="text" id="Procod" name="Procod"></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="50%">Nombre</td>
											<td class="fila1" width="auto"><input type="text" id="Prodes" name="Prodes"></td>
										</tr>
										<tr>
											<td class="encabezadotabla">Actividad anterior a validar</td>
											<td class="fila1">
												<input type="hidden" id="contActivities" name="contActivities" value="0">
												
												<select id="selBeforeActivity_0" name="selBeforeActivity_0">
												</select>
												
												<span id="addActivities"><input type="button" value="+ adicionar" ></span>
												
												<!-- El contenido se carga mediante ajax-->
												<div id="moreActivities">
												</div>
											</td>
										</tr>
										<tr>
											<td class="encabezadotabla">Roles que realizan la actividad</td>
											<td class="fila1">											
												<div id='RolesActivity'>
												</div>
											</td>
										</tr>
										<tr>
											<td class="encabezadotabla">Estado</td>
											<td class="fila1">											
												<input type="radio" name="Proest" value="on"> Activo <br>
												<input type="radio" name="Proest" value="off"> Inactivo
											</td>
										</tr>
										<tr>
											<td class="encabezadotabla">Se muestra en el reporte</td>
											<td class="fila1">											
												<input type="radio" name="Promer" value="on"> Si <br>
												<input type="radio" name="Promer" value="off"> No
											</td>
										</tr>
										
										<tr>										
											<td class="encabezadotabla" align="center" colspan="2">
												<input type="hidden" name="idUpdateActivity" id="idUpdateActivity">	
												<input type="button" id="clearAct" value="Limpiar">
												<input type="button" id="btnSaveNewAct" value="Guardar">
											</td>
										</tr>
										
									</tbody>
								</table>
							</form>
						</div>
						<div class="parteb2">	
							<!-- El contenido se carga mediante ajax-->
							<div id="listActivities">	
							</div>
						</div>
					</div>

					<div id="F_actions">
						<div class="partea2">
							<form id="formTypeAction" enctype="multipart/form-data" method="post">					
								<table align="center">
									<tbody>
										<tr class="encabezadotabla">
											<td colspan="2" align="center"><font size="4">Crear tipo de acción</font></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Código</td>
											<td class="fila1" width="auto"><input type="text" id="Actcod" name="Actcod"></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Nombre</td>
											<td class="fila1" width="auto"><input type="text" id="Actnma" name="Actnma"></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Tipo</td>
											<td class="fila1">
												<select id="Acttpa" name="Acttpa">											
													<option value="radio">Radio</option>
													<option value="checkbox">Checkbox</option>
													<option value="imagen">Imagen</option>
												</select>
											</td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Descripción</td>
											<td class="fila1" width="auto"><input type="text" id="Actdes" name="Actdes"></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Icono</td>
											<td class="fila1" width="auto"><input type="file" id="Actimg" name="Actimg"></td>
										</tr>
										<tr>
											<td class="encabezadotabla">Estado</td>
											<td class="fila1">											
												<input type="radio" name="Actest" value="on"> Activo <br>
												<input type="radio" name="Actest" value="off"> Inactivo
											</td>
										</tr>	
										<tr>										
											<td class="encabezadotabla" align="center" colspan="2">
												<input type="hidden" name="idUpdateTypeAction" id="idUpdateTypeAction">	
												<input type="button" id="clearTypeAct" value="Limpiar">
												<input type="submit" id="btnSaveTypeAction" value="Guardar">
											</td>
										</tr>									
									</tbody>
								</table>
							</form>
						</div>
						<div class="parteb2">
							<!-- El contenido se carga mediante ajax-->
							<div id="listActions">	
							</div>
						</div>
					</div>

					<div id="F_relactactv">
						<div class="partea2">
							<form id="formRelActAcn">
								<table align="center" width="97%">
									<tbody>
										<tr class="encabezadotabla">
											<td colspan="2" align="center"><font size="4">Crear acción</font></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Código</td>
											<td class="fila1" width="auto"><input type="text" id="Raccod" name="Raccod"></td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="50%">Actividad a la que pertenece</td>
											<td class="fila1" width="50%">
												<!-- El contenido se carga mediante ajax-->
												<select id="selActivity" name="Racact">
													<option value="">Seleccione</option>
												</select>
											</td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Orden</td>
											<td class="fila1" width="auto">
												<select id="selOrder" id="Racord" name="Racord">
													<option value="">Seleccione</option>
													<option value="1">1</option>
													<option value="2">2</option>
													<option value="3">3</option>
													<option value="4">4</option>
													<option value="5">5</option>
													<option value="6">6</option>
													<option value="7">7</option>
													<option value="8">8</option>
													<option value="9">9</option>
													<option value="10">10</option>
												</select>
											</td>
										</tr>
										<tr>
											<td class="encabezadotabla" width="80px">Nombre</td>
											<td class="fila1" width="auto"><input type="text" id="Racnam" name="Racnam" size="50px"></td>
										</tr>
										<tr>
											<td class="encabezadotabla">Tipo de acción</td>
											<td class="fila1">
												<!-- El contenido se carga mediante ajax-->
												<div id="optionsActions">
												</div>
											</td>
										</tr>	
										<tr>
										<td class="encabezadotabla">Acción anterior a validar</td>
											<td class="fila1">
												<!-- El contenido se carga mediante ajax-->
												<select id="selBeforeAction" name="Racbac">
												</select>
											</td>
										</tr>
										
										<tr>
											<td class="encabezadotabla">Acciones adicionales a ejecutar <br>* Si es más de una acción separelas con ;</td>
											<td class="fila1">										
												<textarea id="Racead" name="Racead" rows='4'></textarea>
											</td>										
										</tr>
										<tr>
											<td class="encabezadotabla">Acción javascript adicional</td>
											<td class="fila1">										
												<input type="text" id="Racejs" name="Racejs"  size="50px">
											</td>										
										</tr>
										<tr>
											<td class="encabezadotabla">Texto descriptivo del estado del proceso</td>
											<td class="fila1">										
												<input type='text' id="Ractex" name="Ractex" size="50px">
											</td>										
										</tr>
										<tr>
											<td class="encabezadotabla">Está acción puede finalizar el proceso</td>
											<td class="fila1">											
												<input type="radio" name="Racfpr" value="on"> Si <br>
												<input type="radio" name="Racfpr" value="off"> No
											</td>
										</tr>								
										<tr>
											<td class="encabezadotabla">Estado</td>
											<td class="fila1">											
												<input type="radio" name="Racest" value="on"> Activo <br>
												<input type="radio" name="Racest" value="off"> Inactivo
											</td>
										</tr>
										<tr>										
											<td class="encabezadotabla" align="center" colspan="2">
												<input type="hidden" name="idUpdateRelActionAct" id="idUpdateRelActionAct">	
												<input type="button" id="clearRelActionAct" value="Limpiar">
												<input type="button" id="btnSaveRelActAcn" value="Guardar">
											</td>
										</tr>										
									</tbody>
								</table>
							</form>
						</div>
						<div class="parteb2">
							<!-- El contenido se carga mediante ajax-->
							<div id="listRelActivityAction">	
							</div>					
						</div>					
					</div>
					
					<div id="F_createTable">
						<!-- El contenido se carga mediante ajax-->
						<div id="designPrincipalTable">
						</div>										
					</div>
					
					<div id="F_confCco">
						<div class="partea2">
						<form id="formNewCco">					
							<table align="center" width="80%">
								<tbody>
									<tr class="encabezadotabla">
										<td colspan="2" align="center"><font size="4">Agregar centro de costo</font></td>
									</tr>								
									<tr>
										<td class="encabezadotabla" width="40%">Centro de costo</td>
										<td class="fila1" width="60%">
											<select id="selCco" name="selCco">
											</select>
										</td>
									</tr>
									<tr>
										<td class="encabezadotabla" width="40%">Prefijo</td>
										<td class="fila1" width="60%">
											<input type="text" name="nombreCc" id="nombreCc"
										</td>
									</tr>
									<tr>
										<td class="encabezadotabla">Usa turnero</td>
										<td class="fila1">											
											<input type="radio" name="usaTurnero" value="on"> Activo <br>
											<input type="radio" name="usaTurnero" value="off"> Inactivo
										</td>
									</tr>
									
									<tr>
										<td class="encabezadotabla">Solicita camillero</td>
										<td class="fila1">											
											<input type="radio" name="solicitaCamillero" value="on"> Activo <br>
											<input type="radio" name="solicitaCamillero" value="off"> Inactivo
										</td>
									</tr>
									<tr>
										<td class="encabezadotabla">Estado</td>
										<td class="fila1">											
											<input type="radio" name="estado" value="on"> Activo <br>
											<input type="radio" name="estado" value="off"> Inactivo
										</td>
									</tr>									
									<tr>
										<td class="encabezadotabla">Mostrar en televisor hasta </td>
										<td class="fila1">											
											<select name="TiempoMonitor" id="TiempoMonitor">
												<option value="1">1 Hora después de finalizada la atención</option>
												<option value="1">2 Horas después de finalizada la atención</option>
												<option value="1">3 Horas después de finalizada la atención</option>
											</select>
										</td>
									</tr>									
									<tr>	
										<input type="hidden" name="idUpdateCco" id="idUpdateCco">									
										<td class="encabezadotabla" align="center" colspan="2">
											<input type="button" id="clearCco" value="Limpiar">
											<input type="button" id="btnSaveCco" value="Guardar">											
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						</div>
						<div class="parteb2">
							<!-- El contenido se carga mediante ajax-->
							<div id="listCco">		
							</div>
						</div>
															
					</div>
				</div>		
			</div>
		</div>
	</div>				 

</body>
</html>

<?php } ?>