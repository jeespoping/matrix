<?php
include_once("conex.php");
header('Content-type: text/html; charset=ISO-8859-1');
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}

include_once("root/comun.php");

if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wlogoempresa = strtolower( $institucion->baseDeDatos );
$wactualiz = "(Febrero 22 de 2022)";

/************************************************************************************************************************

PROGRAMA: cargos_automaticos.php
Fecha de liberación: 04 Mayo 2021
Autor: Cidenet S.A - Iniciativa Cargos Automáticos
Versión Actual: 2022-02-22

OBJETIVO GENERAL: Este programa corresponde a la interfaz de configuración de cargos automáticos, el usuario puede listar
las configuraciones almacenadas, crear, editar o eliminar configuraciones de cargo automático, listado de registros del log.

************************************************************************************************************************/

/************************************************************************************************************************
 * Modificaciones
 * Febrero 22 de 2022 (Cidenet S.A) Cristhian Barros               - Se añaden comentarios de acuerdo a las recomendaciones de buenas prácticas, se corrige error en la función de guardar_cca y se modifica 
 * Febrero 15 de 2022 (Cidenet S.A) Cristhian Barros, Andrés Gallo - Se añaden los campos de tipo empresa, empresa, facturable, especialidad y tercero comodín para permitir la parametrización de cargos automáticos para honorarios.
 ************************************************************************************************************************/

?>
<html>
    <head>
    <title>Configuraci&oacute;n Cargos Autom&aacute;ticos</title>
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>    
		
	<script src="../../../include/root/jquery-1.8.3/js/jquery-1.8.3.min.js" type="text/javascript"></script>
	<script src="../../../include/root/Tagify/magicsuggest-min.js" type="text/javascript"></script>		
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/Tagify/magicsuggest-min.css" />	
    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<!-- <script src="../../../include/root/jquery_1_7_2/js/jquery.dataTables.min.js" type="text/javascript"></script> -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
   <script type="text/javascript">
	
	/* INICIO DECLARACIONES FUNCIONES PRINCIPALES */
	
	var protocol = "<?php echo $_SERVER['REQUEST_SCHEME'].'://'; ?>";
	var _URL_AJAX = protocol+"<?php echo $_SERVER['SERVER_NAME']; ?>/matrix/cca/procesos/ajax_cargos_automaticos.php?wemp_pmla=<?php echo $wemp_pmla; ?>";
	
	function inicializarDatepickerFecha() {
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);
		
		$('#fecha').datepicker();
	}
	
	function traer_conceptos(muevenInventario)
	{
		$.post(_URL_AJAX,
		{
			consultaAjax:     '',
			accion:           'traer_conceptos',
			muevenInventario:	muevenInventario
		}, function (data) {
			cargar_conceptos(data);
		}, 'json');
	}
	
	function cargar_conceptos(ArrayValores)
	{
		var conceptos 	= new Array();
		var index		= -1;

		for (var cod_con in ArrayValores)
		{
			index++;
			conceptos[index] = {};
			conceptos[index].label  		= cod_con+'-'+ArrayValores[cod_con]['nombre'];
			conceptos[index].nombre 		= ArrayValores[cod_con]['nombre'];
			conceptos[index].valor			= cod_con;
			conceptos[index].modificaVal	= ArrayValores[cod_con]['modificaVal'];
		}

		$( "#busc_concepto_1" ).autocomplete({
			minLength: 	0,
			source: 	conceptos,
			select: 	function( event, ui ){
				$("#busc_concepto_1").val(ui.item.label);
				$("#busc_concepto_1").attr('valor', ui.item.valor);
				$("#busc_concepto_1").attr("nombre", ui.item.nombre);
				$("#busc_procedimiento_1" ).val('');
				elProcExc.clear(true);
				elProcOrden.clear(true);
				ValidarTipoConcepto(ui.item.valor);
				change_edit('concepto');
				return false;
			}
		});
		
	}
	
	//----------------------------------------------------------------------------------
	//	Controlar que el input no quede con basura, sino solo con un valor seleccionado
	//----------------------------------------------------------------------------------
	function limpiaAutocomplete(idInput)
	{			
		$( "#"+idInput ).val().replace(/ /gi, "");
		$( "#"+idInput ).val("");
		$( "#"+idInput ).attr("valor","");
		$( "#"+idInput ).attr("nombre","");
	}
	
	function validarCheckBoxProcOArt(grupInv) {
			
		document.getElementById("wccogra_1").innerHTML = '';
		var radiosTipoCargo = document.getElementsByName('radio');
		
		var str_tipo_cargo = '';
		
		radiosTipoCargo.forEach(function (el) {
			if(el.checked) {
				str_tipo_cargo = el.value;
			}
		});
		
		if(str_tipo_cargo == '') {
			jAlert('El campo "Tipo Cargo" es requerido.', 'Mensaje');
			$( "#busc_concepto_1" ).val('');
			$( "#busc_concepto_1" ).attr('valor', '');
			$( "#busc_concepto_1" ).attr('nombre', '');
			
			return;
		}
		
		if((str_tipo_cargo == 'orden' || str_tipo_cargo == 'aplicacion') && grupInv == 'on'){
			jAlert('En el tipo cargo "'+str_tipo_cargo+'", no se permite seleccionar un concepto con movimiento de inventario', 'Mensaje');
			$( "#busc_concepto_1" ).val('');
			$( "#busc_concepto_1" ).attr('valor', '');
			$( "#busc_concepto_1" ).attr('nombre', '');	
		}
		
		
		
		//document.getElementById("enc_exc_proc").style.display = (str_tipo_cargo == 'orden' && $("#tipo_orden").val() != "" && grupInv != 'on') ? 'table-row' : 'none';
		//document.getElementById("row_exc_proc").style.display = (str_tipo_cargo == 'orden' && $("#tipo_orden").val() != "" && grupInv != 'on') ? 'table-row' : 'none';
		
		if(str_tipo_cargo == 'evento' || str_tipo_cargo == 'dato' || str_tipo_cargo == 'aplicacion') {
			
			document.getElementById("input_pro").disabled = false;
			document.getElementById("input_insu").disabled = false; 
			
			document.getElementById("input_insu").checked = false; 
			document.getElementById("input_pro").checked = false;
			
			if(grupInv == 'on') {
				document.getElementById("input_pro").disabled = true;
				document.getElementById("input_insu").checked = true; 
			} else if(grupInv == 'off') {
				document.getElementById("input_insu").disabled = true;
				document.getElementById("input_pro").checked = true; 
			}
		}
		
	}
	
	function ValidarTipoConcepto(CodigoConcepto, ccoSelected = null)
	{
		document.getElementById('camp_tercero').value = '';

		// 2022/01/28

		document.getElementById('camp_tercero').setAttribute('valor', '');
		document.getElementById('camp_tercero').setAttribute('nombre', '');
		validarTerceroComodin(document.getElementById('camp_tercero').getAttribute('valor'));
								
		$.post(_URL_AJAX,
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'ObtenerElTipoDeConcepto',
			CodigoConcepto:	   CodigoConcepto

		}, function (data){
			if(data.Facturable == 'si')
			{
				datos_desde_concepto(CodigoConcepto, ccoSelected);
				// En esta parte validamos si el concepto tiene habilitado inventario
				
				if(data.Inv) {
					// Validamos cuando el concepto mueve inventario data.Inv = 'on' o cuando no mueve inventario data.Inv = 'off'
					document.getElementById('busc_concepto_1').setAttribute('tipo', data.Tipo);
					document.getElementById('camp_tercero').disabled = (data.Tipo == 'C' ? false : true);
					validarCheckBoxProcOArt(data.Inv);					
				}
			}
			else
			{
				$( "#busc_concepto_1" ).val('');
				$( "#busc_concepto_1" ).attr('valor', '');
				$( "#busc_procedimiento_1" ).val('');
				jAlert("El concepto est&aacute; configurado como no facturable.", "Mensaje");
			}
			
		}, 'json');
	}
	
	function datos_desde_concepto(cod_concepto, ccoSelected = null)
	{
		$.post(_URL_AJAX,
		{
			consultaAjax:     	'',
			wemp_pmla:        	$('#wemp_pmla').val(),
			accion:           	'datos_desde_concepto',
			wcodcon:		  	cod_concepto,
			wcodcco:			ccoSelected
			
		}, function (data){
			$("#wccogra_1").html(data.option_select);
			
		},'json');
	}
	
	
	
	function cargar_tipo_orden(codTipoOrden)
	{	
		$.post(_URL_AJAX,
		{
			consultaAjax:     	'',
			wemp_pmla:        	$('#wemp_pmla').val(),
			cod_tipo_orden:		codTipoOrden,
			accion:           	'obtener_array_tipo_orden'
			
		}, function (data){
			$("#tipo_orden").html(data);
		},'json');
	}	

	function cargar_procedimiento(ArrayValores)
	{
		var procedimientos 	= new Array();
		var index		  	= -1;
		for (var cod_pro in ArrayValores)
		{
			index++;
			procedimientos[index] = {};
			procedimientos[index].value  = cod_pro+'-'+ArrayValores[cod_pro];
			procedimientos[index].label  = cod_pro+'-'+ArrayValores[cod_pro];
			procedimientos[index].nombre = ArrayValores[cod_pro];
			procedimientos[index].valor  = cod_pro;
		}
		
		$( "#busc_procedimiento_1" ).autocomplete({
			minLength: 	0,
			source: 	procedimientos,
			select: 	function( event, ui ){
				$("#busc_procedimiento_1").val(ui.item.label);
				$("#busc_procedimiento_1").attr('valor', ui.item.codigo);
				$("#busc_procedimiento_1").attr("nombre", ui.item.nombre);
				change_edit('procedimiento', 1);
				return false;
			}
		});
		
	}
	/*
	function traer_conceptos(muevenInventario)
	{
		$.post(_URL_AJAX,
		{
			consultaAjax:     '',
			accion:           'traer_conceptos',
			muevenInventario:	muevenInventario
		}, function (data) {
			cargar_conceptos(data);
		}, 'json');
	}
	*/
	
	function traer_fhce()
	{
		if (document.getElementById("busc_formulario_hce_1").value.length>2)
		{
			$.post(_URL_AJAX,
			{
				name_hce:        $('#busc_formulario_hce_1').val(),
				consultaAjax:     '',
				accion:           'traer_formularios_hce',
			}, function (data) {
				cargar_fhce(data);
			}, 'json');
		}
	}
	
	function traer_insumos(el)
	{
		var codigo_concepto = document.getElementById('busc_concepto_1').getAttribute('valor');
		var type = (el === 'busc_procedimiento_1') ? 1 : 2;
		if(document.getElementById(el).value.length>2){
			$.post(_URL_AJAX,
			{
				consultaAjax:     '',
				accion:           'traer_insumos',
				name_insumo:  document.getElementById(el).value,
				codcon: codigo_concepto,
				type: type
			}, function (data) {				
				cargar_insumos(data, el);
			}, 'json');
		}
	}
	
 	function traer_procedimientos()
	{
		if(document.getElementById("busc_procedimiento_1").value.length > 2){
			$.post(_URL_AJAX,
			{
				consultaAjax:     '',
				accion:           'traer_procedimientos',
				name_proc:  document.getElementById("busc_procedimiento_1").value,
			}, function (data) {  
			   cargar_insumos(data, 'busc_procedimiento_1');
			}, 'json');
		}
	}
 
 	function cargar_insumos(ArrayValores, el)
	{
		var fhce 	= new Array();
		var index		  	= -1;
		for (var cod_ins in ArrayValores)
		{
			index++;
			fhce[index] = {};
			fhce[index].value  = cod_ins;
			fhce[index].label  = cod_ins+'-'+ArrayValores[cod_ins]['nombre'];
			fhce[index].nombre = ArrayValores[cod_ins]['nombre'];
			fhce[index].valor  = cod_ins;
		}
		
		//var el = "#busc_procedimiento_1";
		$( "#"+el ).autocomplete({
			minLength: 	0,
			source: 	fhce,
			select: 	function( event, ui ){
				$("#"+el).val(ui.item.label);
				$("#"+el).attr('valor', ui.item.valor);
				$("#"+el).attr("nombre", ui.item.nombre);
				change_edit('procedimiento', 1);
				return false;
			}
		});
	}
  
	function cargar_fhce(ArrayValores)
	{
		var fhce 	= new Array();
		var index		  	= -1;
		for (var cod_hce in ArrayValores)
		{
			index++;
			fhce[index] = {};
			fhce[index].value  = cod_hce;
			fhce[index].label  = cod_hce+'-'+ArrayValores[cod_hce]['nombre'];
			fhce[index].nombre = ArrayValores[cod_hce]['nombre'];
			fhce[index].valor  = cod_hce;
		}
		var el = "#busc_formulario_hce_1";
		$( el ).autocomplete({
			minLength: 	0,
			source: 	fhce,
			select: 	function( event, ui ){
				$(el).val(ui.item.label);
				$(el).attr('valor', ui.item.valor);
				$(el).attr("nombre", ui.item.nombre);
				change_edit('formulario_hce', 1);
				datos_desde_fhce(ui.item.valor);
				return false;
			}
		});
	}
	
	function datos_desde_fhce(cod_fhce, conSelected = null)
	{
		$.post(_URL_AJAX,
		{
			consultaAjax:     	'',
			wemp_pmla:        	$('#wemp_pmla').val(),
			accion:           	'datos_desde_fhce',
			wcodfhce:		  	cod_fhce,
			wconsecfhce:	    conSelected
			
		}, function (data){
			$("#wconfhce_1").html(data);
		},'json');

	}
 	
	function listado()
	{
		$.post(_URL_AJAX,
		{
			consultaAjax:     	'',
			accion:           	'listado',
			
		}, function (response){
			
			if(response.code) {
				$("#div-tabla-configs").html("");
				let html = '<table align="center"   id="mytable"  style="width: 100%;margin:auto">\
							<thead>\
								<tr id="tr_enc_det_concepto" class="encabezadoTabla" style="font-size: 10pt;" align="center">\
									<th>Concepto</th>\
									<th>Tipo Empresa - Empresa</th>\
									<th>Facturable</th>\
									<th>Cen. Costos</th>\
									<th>Medicamento/Insumo</th>\
									<th>Procedimiento/Examen</th>\
									<th>Proc. Excluidos</th>\
									<th>Articulo</th>\
									<th>Formulario HCE</th>\
									<th>Campo HCE</th>\
									<th>Tercero</th>\
									<th>Especialidad</th>\
									<th>Tipo de Cargo</th>\
									<th>Tipo Cen. Costos</th>\
									<th>Eliminar</th>\
									<th>Editar</th>\
								</tr>\
							</thead>\
							<tbody>';
				var data = response.data;
				var fila = "";
				var tr = "";
				let server = protocol+'<?php echo $_SERVER["SERVER_NAME"]; ?>';
				
				for (var id in data) {
					var tipo = "";
					if(data[id]['dato']=="on"){
						tipo = "dato";
					}else if(data[id]['evento']=="on"){
						tipo = "evento";
					}else if(data[id]['orden']=="on"){
						tipo = "orden"+(data[id]['ccator'] != '' ? ': ('+data[id]['ccator']+'-'+data[id]['tipo_orden']+')' : '');
					}else{
						tipo = "aplicacion";
					}
					
					fila = id%2 == 0 ? 'fila1' : 'fila2';
					
					tr += "<tr class='"+fila+"'>"
						  + "<td>"+data[id]['concepto']+"</td>"
						  + "<td>"+data[id]['responsable']+"</td>"
						  + "<td>"+data[id]['ccafac']+"</td>"
						  + "<td>"+data[id]['c_costos']+"</td>"
						  + "<td>"+data[id]['articuloapl']+"</td>"
						  + "<td>"+data[id]['procedimiento'].replace(',','<br>')+"</td>"
						  + "<td>"+data[id]['ccapex'].replace(',','<br>')+"</td>"
						  + "<td>"+data[id]['articulo']+"</td>"
						  + "<td>"+data[id]['hce']+"</td>"
						  + "<td>"+data[id]['consecutivo']+"</td>"
						  + "<td>"+data[id]['tercero']+"</td>"
						  + "<td>"+data[id]['ccaesp']+"</td>"
						  + "<td>"+tipo+"</td>"
						  + "<td>"+data[id]['cad_tipo_cco']+"</td>"
						  + "<td style='text-align: center'><button  onclick='eliminar(\""+id+"\")'><img src='"+server+"/matrix/images/medical/root/borrar.png' alt=''></button></td>"
						  + "<td style='text-align: center'><button  onclick='editar(\""+id+"\",\""+data[id]['concepto']+"\",\""+data[id]['c_costos']+"\",\""+data[id]['procedimiento']+"\",\""+data[id]['articulo']+"\",\""+data[id]['hce']+"\",\""+data[id]['consecutivo']+"\",\""+tipo+"\",\""+data[id]['tipo_cco']+"\",\""+data[id]['articuloapl']+"\",\""+data[id]['tipo_concepto']+"\",\""+data[id]['ccator']+"\",\""+data[id]['ccapex']+"\",\""+data[id]['tercero']+"\",\""+data[id]['ccafac']+"\",\""+data[id]['ccatem']+"\",\""+data[id]['ccaemp']+"\",\""+data[id]['ccaesp']+"\")'><img src='"+server+"/matrix/images/medical/root/grabar.png' alt=''></button></td>"
					+   "</tr>";
				}
				
				tr = tr == "" ? "<tr class='fila1'><td colspan='9' style='text-align: center;'>No existen registros en la base de datos.</td></tr>" : tr;
				
				html += tr+"</tbody><table>";
				
				$("#div-tabla-configs").append(html);
				
				$('#mytable').DataTable({
					"language": {"url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"}
				});
				$('#mytable').removeClass('dataTable');	
			} else {
				jAlert( response.msj, 'Alerta');
			}
		},'json');
	}
	
	function eliminar(id) {
		var wuse = document.getElementById('wuse').value;
		jConfirm('Seguro quieres eliminar esta configuracion?', 'Mensaje', function(e) { 
			if(e){
				$.post(_URL_AJAX,
				{
					consultaAjax:	'',
					wuse: wuse,
					id_cargo:		id,
					accion:			'eliminar',
					
				}, function (data){
					listado();
					jAlert(data.msj, 'Mensaje');
			 
				},'json');
			
			}
		});
	}
	
	var cargo_editado=0;
	
	var obj_cargo = {
		concepto: '',
		c_costos: '',
		procedimiento: '',
		articulo: '',
		hce : '',
		consecutivo: '',
		tipo: '',
		tipo_orden: '',
		tipo_cco: [],
		articuloapl: '',		
		tercero: '',
		procedimientoExc: [],
		facturable: '',
		tipo_empresa: '',
		empresa: '',
		especialidad: ''
	};

	
	function editar(id,concepto,c_costos,procedimiento,articulo,hce,consecutivo,tipo,tipo_cco, articuloapl, tipo_concepto, tipo_orden, proc_exc, tercero, facturable, tipo_empresa, empresa, especialidad) {
		jConfirm('Seguro quieres editar esta configuracion?', 'Mensaje', function(e) { 
			if(e){				
				tipo = tipo.split(':')[0];
				obj_cargo.concepto = concepto;
				obj_cargo.c_costos = c_costos;
				obj_cargo.procedimiento = procedimiento;
				obj_cargo.articulo = articulo;
				obj_cargo.hce = hce;
				obj_cargo.consecutivo = consecutivo;
				obj_cargo.tipo = tipo;
				obj_cargo.tipo_cco = tipo_cco.split(",");	
				obj_cargo.articuloapl = articuloapl;
				obj_cargo.tipo_orden = tipo_orden;
				obj_cargo.procedimientoExc = proc_exc.length > 0 ? proc_exc.split(',').sort() : [];
				obj_cargo.tercero = tercero;
				obj_cargo.facturable = facturable;
				obj_cargo.tipo_empresa = tipo_empresa;
				obj_cargo.empresa = empresa;
				/* NUEVO 2022-01-14 */
				obj_cargo.especialidad = especialidad;
				
				this.cargo_editado = id;
				
				openTab(event,'configurar');
				
				document.getElementById("button_guardar").innerText="Editar Configuracion";
				document.getElementById("button_guardar").disabled=true;		
			
				var inputConcepto = $("#busc_concepto_1");
				var inputArticuloApl = $("#busc_articulo_1");
				var inputProcedimientoOInsumo = $("#busc_procedimiento_1");
				var inputFormularioHCE = $("#busc_formulario_hce_1");
				
				var splitConcepto = '';
				var codigoConcepto = '';
				var nombreConcepto = '';
				
				var splitCco = '';
				var codigoCco = '';
				
				var splitProcedimiento = '';
				var codigoProcedimiento = '';
				var nombreProcedimiento = '';
				
				var splitArticulo = '';
				var codigoArticulo = '';
				var nombreArticulo = '';
				
				var splitFormularioHCE = '';
				var codigoFormularioHCE = '';
				var nombreFormularioHCE = '';
				
				var splitConsecFormularioHCE = '';
				var codigoConsecFormularioHCE = '';
			
				if(tipo == "dato") {
					$("#dato").prop("checked", true);
					activar('editar');
				} else if( tipo == "evento" ) {
					$("#evento").prop("checked", true);
					activar('editar');
				} else if(tipo=="orden") {
					$("#orden").prop("checked", true);
					desactivar('editar');
				} else {					
					$("#aplicacion").prop("checked", true);					
					desactivar('editar');					
				}
				
				/* NUEVO */
				var checksTcc = document.getElementsByName('check_tcc');			
				checksTcc.forEach(function (el) {
					if(tipo_cco.includes(el.value)) {
						el.checked = true;
					}
				});
				
				traer_tipos_empresa(tipo_empresa);
				traer_empresas(tipo_empresa, empresa);
				
				checked_facturable = facturable == 'si' ? true: false;
				$("#wfac").prop("checked", checked_facturable);
				
				
				splitConcepto = concepto.split('-');
				codigoConcepto = splitConcepto[0];
				nombreConcepto = splitConcepto[1];
				
				inputConcepto.val(concepto);
				inputConcepto.attr('valor', codigoConcepto);
				inputConcepto.attr('nombre', nombreConcepto);
				inputConcepto.attr('tipo', tipo_concepto);
				
				splitArticuloApll = articuloapl.split('-');
				codigoArticuloApl = splitArticuloApll[0];
				nombreArticuloApl = splitArticuloApll[1];
				
				inputArticuloApl.val(articuloapl);
				inputArticuloApl.attr('valor', codigoArticuloApl);
				inputArticuloApl.attr('nombre', nombreArticuloApl);
				
				splitCco = c_costos.split('-');
				codigoCco = splitCco[0];
				
				ValidarTipoConcepto(codigoConcepto, codigoCco);
				
				document.getElementById("col_label_proex").style.display = tipo_orden != '' ? 'table-cell' : 'none';
				document.getElementById("col_camp_proex").style.display = tipo_orden != '' ? 'table-cell' : 'none';
				document.getElementById("divChkTodosProc").style.display = tipo_orden != '' ? 'inline' : 'none';
				if(procedimiento != '') {
					document.getElementById('camp_tercero').value = tercero;
					document.getElementById('camp_tercero').setAttribute('valor', tercero.split('-')[0]);
					document.getElementById('camp_tercero').setAttribute('nombre', tercero.split('-')[1]);
					
					if(tipo=="orden"){
						
						cargar_tipo_orden(tipo_orden);						
						if(procedimiento == '*'){							
							document.getElementById('chkTodosProc').checked = true;
							document.getElementById('filterExcludeProcOrden').focus();
							elProcOrden.disable();
							elProcExc.enable();						
							elProcExc.setValue(proc_exc.length > 0 ? proc_exc.split(','): []);
						}
						else{
							elProcOrden.setValue(procedimiento.split(','));
							elProcOrden.enable();
							elProcExc.disable();
						}
					}else{
						$("#input_pro").prop("checked", true);					
						splitProcedimiento = procedimiento.split('-');
						codigoProcedimiento = splitProcedimiento[0];
						nombreProcedimiento = splitProcedimiento[1];
					
						inputProcedimientoOInsumo.val(procedimiento);
						inputProcedimientoOInsumo.attr('valor', codigoProcedimiento);
						inputProcedimientoOInsumo.attr('nombre', nombreProcedimiento);
					}
				} else if(articulo != '') {
					$("#input_insu").prop("checked", true);
					
					splitArticulo = articulo.split('-');
					codigoArticulo = splitArticulo[0];
					nombreArticulo = splitArticulo[1];
				
					inputProcedimientoOInsumo.val(articulo);
					inputProcedimientoOInsumo.attr('valor', codigoArticulo);
					inputProcedimientoOInsumo.attr('nombre', nombreArticulo);
				}
				
				if( hce != '' ) {
					splitFormularioHCE = hce.split('-');
					codigoFormularioHCE = splitFormularioHCE[0];
					nombreFormularioHCE = splitFormularioHCE[1];
				
					inputFormularioHCE.val(hce);
					inputFormularioHCE.attr('valor', codigoFormularioHCE);
					inputFormularioHCE.attr('nombre', nombreFormularioHCE);
					
					splitConsecFormularioHCE = consecutivo.split(' - ');
					codigoConsecFormularioHCE = splitConsecFormularioHCE[0];
					
					datos_desde_fhce(codigoFormularioHCE, codigoConsecFormularioHCE);
					
					var splitEspecialidad = '';
					var codigoEspecialidad = '';
					var nombreEspecialidad = '';
					var inputEspecialidad = $("#wesp");
					
					splitEspecialidad = especialidad.split('-');
					codigoEspecialidad = splitEspecialidad[0];
					nombreEspecialidad = splitEspecialidad[1];
				
					inputEspecialidad.val(especialidad);
					inputEspecialidad.attr('valor', codigoEspecialidad);
					inputEspecialidad.attr('nombre', nombreEspecialidad);
					
					validarTerceroComodin(tercero.split('-')[0]);
					
				}
				origenGeneral = 1;
			}
	   });
	}

	function guardar_cca() {
		var concepto = document.getElementById('busc_concepto_1').getAttribute('valor');
		var conceptoText = document.getElementById('busc_concepto_1').value;
		var centro_costos = document.getElementById('wccogra_1').value;
		var procedimiento_insumo = document.getElementById('busc_procedimiento_1').getAttribute('valor');
		var procedimiento_insumoText = document.getElementById('busc_procedimiento_1').value;
		var formulario_hce = document.getElementById('busc_formulario_hce_1').getAttribute('valor');
		var formulario_hceText = document.getElementById('busc_formulario_hce_1').value;
		var consecutivo_hce = document.getElementById('wconfhce_1').value;
		var wuse = document.getElementById('wuse').value;
		var articulo = document.getElementById('busc_articulo_1').value;
		var articuloVal = document.getElementById('busc_articulo_1').getAttribute('valor');
		var radiosTipoCargo = document.getElementsByName('radio');
		var radiosProcOIns = document.getElementsByName('radio_p');
		
		var checksTipoCco = document.getElementsByName('check_tcc');
		var tipoConcepto = 	document.getElementById('busc_concepto_1').getAttribute('tipo');	
		
		var msjErrores = '';
		var tipo_cargo = '';
		var procOInsu = '';
		
		var tipo_cco = '';
		var arrayProcExc = [];
		var procExc = [];
		var tipoOrden = '';
		var tercero = '';
		
		/* NUEVO 2021-12-23 */
		var facturable = document.getElementById("wfac").checked ? 'si' : 'no';
		var tipo_empresa = document.getElementById('wtemp').value;
		var empresa = document.getElementById('wemp').value;
		
		/* NUEVO 2022-01-14 */
		var especialidad = '';
		
		if(centro_costos=="0") {
			centro_costos='';
		}

		radiosTipoCargo.forEach(function (el) {
			if(el.checked) {
				tipo_cargo = el.value;
			}
			
			msjErrores = tipo_cargo == '' ? 'El campo "Tipo Cargo" es requerido. \n' : '';
		});
		if((tipo_cargo == 'aplicacion') && (articulo == '')) {
			msjErrores +=  'El campo "Medicamento/Insumo" es requerido.\n';
		
		}
		/* NUEVO */
		checksTipoCco.forEach(function (el) {
			if(el.checked) {
				tipo_cco += el.value+',';
			}
		});
		
		tipo_cco = tipo_cco.replace(/(^[,\s]+)|([,\s]+$)/g, '');		
		
		if(tipo_cco == '') {
			msjErrores += 'Debe seleccionar al menos un tipo de centro de costo. \n';
		}
		
		if(tipo_cargo == 'orden') {
			tipoOrden = document.getElementById('tipo_orden').value;			
			if(tipoOrden != '') {
				arrayProcExc = elProcExc.getValue();
				for(let i = 0; i < arrayProcExc.length; i++){
					procExc.push(arrayProcExc[i].split('-')[0]);
				}
				procExc = procExc.join();				
			}
		} else {
			procExc = '';
		}
		
		if(tipoConcepto == 'C'){				
			tercero = document.getElementById('camp_tercero').getAttribute('valor');
			terceroTexto = document.getElementById('camp_tercero').value;
			
			if(tercero == '*') {
				especialidad = document.getElementById('wesp').getAttribute('valor');
				especialidadTexto = trim(document.getElementById('wesp').value);	
				msjErrores +=  (especialidadTexto.length == '') ? 'El campo "Especialidad" es requerido.\n' : '';
			}
			msjErrores +=  (tercero == '' || terceroTexto.length == '') ? 'El campo "Tercero" es requerido.\n' : '';
			
		}
		
		if(concepto == '' || conceptoText == '') {
			msjErrores += 'El campo "Concepto" es requerido. \n';
		}
		
		radiosProcOIns.forEach(function (el) {
			if(el.checked) {
				procOInsu = el.value;
			}
		});		
		
		if(tipo_cargo == 'orden'){
			procedimiento_insumo = '';
			if(document.getElementById('chkTodosProc').checked == false){
				let listaproc = elProcOrden.getValue();
				for(let i = 0; i < listaproc.length; i++){
					listaproc[i] = (listaproc[i].split('-'))[0];
				}
				procedimiento_insumo = listaproc.join();
			}else
				procedimiento_insumo = '*';
			procedimiento_insumoText = '*';
		}	
		
		if(procOInsu == '' || procedimiento_insumo == '' || procedimiento_insumoText == '') {
			msjErrores += 'El campo "Procedimiento o Articulo" es requerido. \n';
		}		
		
		if((tipo_cargo == 'dato') && (formulario_hce == '' || consecutivo_hce == '' || formulario_hceText == '')) {
			msjErrores += formulario_hce == ''  ? 'El campo "Formulario HCE" es requerido.\n' : '';
			msjErrores += consecutivo_hce == ''  ? 'El campo "Campo HCE" es requerido.\n' : '';
		}
		if((tipo_cargo == 'evento') && (formulario_hce == '')) {
			msjErrores += formulario_hce == ''  ? 'El campo "Formulario HCE" es requerido.\n' : '';
		
		}
		
		/* 2021-12-23 */
		if(tipo_empresa == '') {
			msjErrores += 'El campo "Tipo Empresa" es requerido.\n';
		}
		
		if(empresa == '') {
			msjErrores += 'El campo "Empresa" es requerido.\n';
		}
		
		if(msjErrores != '') {
			jAlert(msjErrores, "Alerta");
			return;
		}	
		
		var listaCargos;
		var textoCCA = '';
		var validaCCA = false;
		
		if(tipo_cargo == 'evento' || tipo_cargo == 'dato') {
			$.post(_URL_AJAX,
			{
				consultaAjax:     	'',
				accion:           	'listado',
				formulario: formulario_hce
				
			}, function (response) {
				
				if(response.code) {	
					
					listaCargos = response.data;
					contador = 0;
					
					for (const cargo in listaCargos) {
						if(cargo != cargo_editado) {
							contador++;
							
							/* MODIFICADO 2021-12-23 */
							str_procedimiento = listaCargos[cargo].procedimiento.length > 20 ? listaCargos[cargo].procedimiento.substr(0,20) + '...' : listaCargos[cargo].procedimiento;
							str_responsable = listaCargos[cargo].responsable.length > 20 ? listaCargos[cargo].responsable.substr(0,20) + '...' : listaCargos[cargo].responsable;
							
							textoCCA += listaCargos[cargo].procedimiento ? '- <b title= "'+ listaCargos[cargo].procedimiento +'">Proc:</b> ' + str_procedimiento + ', <b title= "'+ listaCargos[cargo].responsable +'">Resp:</b>' + str_responsable + ', <b>Fact: </b> ' + listaCargos[cargo].ccafac +'\n\n' 
							         : '- Art: ' + listaCargos[cargo].articulo + ' , Campo HCE: ' + listaCargos[cargo].consecutivo + ', <b>Resp:</b>' + listaCargos[cargo].responsable + '\n\n';
						}
						
					}
					
					if(textoCCA != '') {		
						
						textoCCA = 'Actualmente el <b>Formulario HCE: '
									+ formulario_hce + '</b> ya cuenta con configuraciones de cargos autom&aacute;ticos ('
									+ contador +'): <br><br>'
									+ textoCCA + ' Realmente deseas agregar m&aacute;s configuraciones ?';
						jConfirm(textoCCA, 'Mensaje', function(e) { 
							if(e){								
								guardar_interno();
							}
						});
					}
					else{
						guardar_interno();
					}					
					
				} else {
					jAlert( response.msj, 'Alerta');
					return;
				}
			},'json');
		} else {
			guardar_interno()
		}
		
		function guardar_interno(){
					
			if(cargo_editado != 0) { 

				$.post(_URL_AJAX,
				{
					consultaAjax:     	'',
					wemp_pmla:        	$('#wemp_pmla').val(),
					accion:           	'edit_cargo_automatico',
					wuse: wuse,
					con: concepto,
					cco: centro_costos,
					procins: procedimiento_insumo,
					fhce: formulario_hce,
					confhce: consecutivo_hce,
					tc: tipo_cargo,
					poi: procOInsu,
					tcco: tipo_cco,
					articulo: articuloVal,
					tipo_orden: tipoOrden,
					tercero: tercero,
					proc_exc: procExc,
					/* NUEVO 2021-12-23*/
					facturable: facturable,
					tipo_empresa: tipo_empresa,
					empresa: empresa,
					/* NUEVO 2022-14-01*/
					especialidad: especialidad,
					id: cargo_editado
					
				}, function (data){
			  
					if(data.code) {
						limpiarFormulario();
						openTab(event,'listadoA');
					}
					jAlert(data.msj, "Mensaje");
				}, 'json');		   
			} else {
				$.post(_URL_AJAX,
				{
					consultaAjax:     	'',
					wemp_pmla:        	$('#wemp_pmla').val(),
					accion:           	'guardar_config_cargo_automatico',
					wuse: wuse,
					con: concepto,
					cco: centro_costos,
					procins: procedimiento_insumo,
					fhce: formulario_hce,
					confhce: consecutivo_hce,
					tc: tipo_cargo,
					tcco: tipo_cco,
					articulo: articuloVal,
					poi: procOInsu,
					tipo_orden: tipoOrden,
					tercero: tercero,
					proc_exc: procExc,
					/* NUEVO 2021-12-23 */
					facturable: facturable,
					tipo_empresa: tipo_empresa,
					empresa: empresa,
					/* NUEVO 2022-14-01*/
					especialidad: especialidad
					
				}, function (data){
					if(data.code) {
						limpiarFormulario();
						openTab(event,'listadoA');
					}
					jAlert(data.msj, "Mensaje");
				}, 'json');
			}
		}
	}
 
	function change_edit(section, origen = 0) {
		if(origen != 'editar') {	
			if(this.cargo_editado > 0) {
				var obj_cargo_editado;
				if(origen == 1){					
					var tipo_cargo = $('input[name="radio"]:checked').val();
					var tipo_cco = "";
					var proins = $('input[name="radio_p"]:checked').val();			
					var checksTipoCco = document.getElementsByName('check_tcc');
					var procedimiento = '';
					var procExc = [];
					checksTipoCco.forEach(function (el) {
						if(el.checked) {
							tipo_cco += el.value+',';
						}
					});	
					tipo_cco = tipo_cco.replace(/(^[,\s]+)|([,\s]+$)/g, '');
					if(tipo_cargo!='orden')
						procedimiento = proins == 'procedimiento' ? document.getElementById('busc_procedimiento_1').value : '';
					else{
						procExc = elProcExc.getValue()
						procedimiento =  document.getElementById('chkTodosProc').checked ? '*' : elProcOrden.getValue().sort().join();
					}
					obj_cargo_editado = {
												concepto: document.getElementById('busc_concepto_1').value,
												c_costos: document.getElementById('wccogra_1').value == "0" ? "" : $("#wccogra_1 option:selected").text(),
												procedimiento: procedimiento,
												articulo: proins == 'insumo' ? document.getElementById('busc_procedimiento_1').value : '',
												hce : document.getElementById('busc_formulario_hce_1').value,
												consecutivo: $("#wconfhce_1 option:selected").text() == 'Seleccione..' ? '' : $("#wconfhce_1 option:selected").text(),
												tipo: tipo_cargo,
												tipo_orden: document.getElementById('tipo_orden').value,
												tipo_cco: tipo_cco.split(','),
												articuloapl : document.getElementById('busc_articulo_1').value,
												tercero: document.getElementById('camp_tercero').value,
												procedimientoExc: procExc,
												/* NUEVO 2021-12-23 */
												facturable: document.getElementById("wfac").checked ? 'si' : 'no',
												tipo_empresa: document.getElementById('wtemp').value,
												empresa: document.getElementById('wemp').value,
												/* NUEVO 2022-14-01*/
												especialidad: document.getElementById('wesp').value,
											};
				}else{
					obj_cargo_editado = Object.assign({}, this.obj_cargo);
				}
														
				var pro='procedimiento';			
				if(this.obj_cargo.procedimiento==''){
				   pro='insumo';
				}
				var btn_editar_cca = document.getElementById('button_guardar');
				if(section == 'tipo_origen') {
					limpiarFormulario(false);
				}
				
				if(JSON.stringify(obj_cargo_editado).split('').sort().join('') === JSON.stringify(this.obj_cargo).split('').sort().join('')){
					btn_editar_cca.setAttribute('disabled', true);
				}else{
					btn_editar_cca.removeAttribute('disabled');
				}				
			} else {
				if(section == 'tipo_origen') {
					limpiarFormulario(false);
				}
			}
		}		
	}
	
	function limpiarFormulario(todo = true) {		
		if(todo) {
			document.getElementById('form_cca').reset();
			document.getElementById("busc_formulario_hce_1").removeAttribute('disabled');
			document.getElementById("wconfhce_1").removeAttribute('disabled');
		}
		$("#camp_tercero").autocomplete({ source: []});
		document.getElementById('camp_tercero').value = '';
		document.getElementById('camp_tercero').setAttribute('valor', '');
		document.getElementById('camp_tercero').setAttribute('nombre', '');
		document.getElementById('camp_tercero').disabled = true;
		
		document.getElementById("tipo_orden").innerHTML = '';
		document.getElementById("enc_label_procord").style.display = 'none';
		document.getElementById("row_camp_procord").style.display = 'none';
		
		document.getElementById("wconfhce_1").innerHTML = '';
		document.getElementById("wccogra_1").innerHTML = '';
		
		/* NUEVO 2021-12-23 */
		//document.getElementById('wfac').checked = false;
		document.getElementById("wtemp").innerHTML = options_select_tipo_empresa;
		document.getElementById("wemp").innerHTML = '';
		
		
		document.getElementById("busc_concepto_1").value = '';
		document.getElementById("busc_procedimiento_1").value = '';
		document.getElementById("busc_formulario_hce_1").value = '';
		
		limpiaAutocomplete('busc_concepto_1');
		limpiaAutocomplete('busc_procedimiento_1');
		limpiaAutocomplete('busc_formulario_hce_1');

		var checksTipoCco = document.getElementsByName('check_tcc');	
		checksTipoCco.forEach(function (el) {
			el.checked = false;
		});

		validarTerceroComodin(document.getElementById('camp_tercero').getAttribute('valor'));
	}    

    function cerrarVentanaPpal()
    {
        window.close();
    }
	
	function mostrarDetalleCargo(btn, indice)
    {  
		var src = btn.src.split("/");
		src = src[src.length - 1];
		if(src === "menos.PNG"){
			document.getElementById("det_cargo_"+indice).style.display = "none";
			btn.src = "../../images/medical/hce/mas.PNG";
			return;
		}
		var elm = document.getElementsByName("det_cargo");
		elm.forEach(function (el) {			
			el.style.display = "none";
		});
		var elmButton = document.getElementsByName("btn-det-cargo");
		elmButton.forEach(function (el) {			
			el.src = "../../images/medical/hce/mas.PNG";
		});
		btn.src = "../../images/medical/hce/menos.PNG";
		document.getElementById("det_cargo_"+indice).style.display = "block";
    }
	
    function openTab(evt, cityName, mostrar_msj_editar = false) {
		origenGeneral = 0;
		if(cityName == 'listadoA') {
			document.getElementById("input_pro").disabled = false; 
			document.getElementById("input_insu").disabled = false;
			listado();
            this.cargo_editado=0;
			 this.obj_cargo = {
				concepto: '',
				c_costos: '',
				procedimiento: '',
				articulo: '',
				hce : '',
				consecutivo: '',
				tipo: ''
			};
			limpiarFormulario();
			document.getElementById("button_guardar").innerText="Guardar Configuracion";
			document.getElementById("button_guardar").disabled=false;
     
		} else if( cityName == 'configurar') {
			document.getElementById("filaArticulo").style.display = "none";
			if(mostrar_msj_editar && this.cargo_editado!=0){
				
				jConfirm('Actualmente te encuentras editando una configuraci&oacute;n de cargos autom&aacute;ticos, &iquest;deseas realmente cancelar la edici&oacute;n?', 'Mensaje', function(e) { 
			if(e){
				document.getElementById("input_pro").disabled = false; 
				document.getElementById("input_insu").disabled = false;
				 this.cargo_editado=0;
					this.obj_cargo = {
					concepto: '',
					c_costos: '',
					procedimiento: '',
					articulo: '',
					hce : '',
					consecutivo: '',
					tipo: ''
				};
				limpiarFormulario();
			document.getElementById("button_guardar").innerText="Guardar Configuracion";
			document.getElementById("button_guardar").disabled=false;				
				}
			});
			
			}
			
		} else if ( cityName == 'log') {
			//traer_log();
			obtenerListadoLog();
		}
      
		var i, tabcontent, tablinks;
		tabcontent = document.getElementsByClassName("prueba");
		for (i = 0; i < tabcontent.length; i++) {
		  tabcontent[i].style.display = "none";
		}
		tablinks = document.getElementsByClassName("tablinks");
		for (i = 0; i < tablinks.length; i++) {
		  tablinks[i].className = tablinks[i].className.replace(" active", "");
		}
		document.getElementById(cityName).style.display = "block";
		if(evt) {
			evt.currentTarget.className += " active";
		}
            
	}        
	

	//validar el buscador de procedimientos e insumos 
	function validar() {
		if(document.getElementById('busc_concepto_1').getAttribute('valor')!="") {
			
			var radiosTipo = document.getElementsByName('radio_p');
			var str_tipo = '';
				
			radiosTipo.forEach(function (el) {
				if(el.checked) {
					str_tipo = el.value;
				}
			});
			
			if(str_tipo=="insumo"){
				traer_insumos('busc_procedimiento_1');
			} else {				
				traer_procedimientos();				
			}
		} else  {
			jAlert('El campo Concepto es requerido.', 'Mensaje');
		}
	}	
	
	function desactivar(origen = '') {
		var elSelected = $("input[type='radio'][name='radio']:checked").val();
		document.getElementById("busc_formulario_hce_1").setAttribute('disabled', true);
		document.getElementById("wconfhce_1").setAttribute('disabled', true);		
		document.getElementById("busc_formulario_hce_1").value = '';
		document.getElementById("busc_formulario_hce_1").setAttribute('valor', '');
		document.getElementById("busc_formulario_hce_1").setAttribute('nombre', '');
		document.getElementById("wconfhce_1").innerHTML = '';		
		var paramOrigen = origen == 'editar' ? origen : 1;
		change_edit('tipo_origen', paramOrigen);
		document.getElementById("input_pro").disabled = false; 
		document.getElementById("input_insu").disabled = false;
		document.getElementById("filaArticulo").style.display = (elSelected == 'aplicacion') ? "table-row" : "none";
		document.getElementById("filaTipoOrden").style.display = (elSelected == 'orden') ? "table-row" : "none";
		document.getElementById("busc_articulo_1").value = "";
		var radio_checked = '';
		//document.getElementById("col_label_terc").style.display = (elSelected == 'orden') ? "table-cell" : "none";		
		//document.getElementById("col_camp_terc").style.display = (elSelected == 'orden') ? "table-cell" : "none";	
		
		
		
		document.getElementById("col_camp_fhce").style.display = (elSelected == 'orden') || (elSelected == 'aplicacion') ? "none" : "table-cell";
		document.getElementById("col_camp_chce").style.display = (elSelected == 'orden') || (elSelected == 'aplicacion') ? "none" : "table-cell";
		document.getElementById("col_label_fhce").style.display = (elSelected == 'orden') || (elSelected == 'aplicacion') ? "none" : "table-cell";
		document.getElementById("col_label_chce").style.display = (elSelected == 'orden') || (elSelected == 'aplicacion') ? "none" : "table-cell";
		document.getElementById("col_label_proart").style.width = (elSelected != 'aplicacion') ? "30%" : "";
		
		document.getElementById("col_label_proart").colSpan = "8";
		document.getElementById("col_camp_proart").colSpan = "8";
		
		
		document.getElementById("col_label_proart").style.display = (elSelected != 'orden') ? "table-cell" : "none";
		document.getElementById("col_camp_proart").style.display = (elSelected != 'orden') ? "table-cell" : "none";
		
		//document.getElementById("col_label_cencos").style.width = (elSelected != 'orden') ? "20%" : "40%";
		document.getElementById("enc_label_procord").style.display = (elSelected == 'orden') ? "table-row" : "none";
		document.getElementById("row_camp_procord").style.display = (elSelected == 'orden') ? "table-row" : "none";
		document.getElementById("col_label_proex").style.display = 'none';
		document.getElementById("col_camp_proex").style.display = 'none';
		document.getElementById("divChkTodosProc").style.display = 'none';
		document.getElementById("chkTodosProc").checked = false;
		camposEstadoProc();
		if(document.querySelector('input[name="radio"]:checked')) {
			radio_checked = document.querySelector('input[name="radio"]:checked').value;
			if(radio_checked=="prescripcion"){
			document.getElementById("input_insu").checked = true;
			document.getElementById("input_pro").disabled = true;  
			}else if(radio_checked=="orden"){
				document.getElementById("input_pro").checked = true;
				document.getElementById("input_insu").disabled = true;
				if(origen != 'editar')
					cargar_tipo_orden('');
			}
		}				
		document.getElementById("busc_procedimiento_1").value = '';
		document.getElementById("busc_procedimiento_1").setAttribute('valor', '');
		 
	}

	//--------------------------------------------------------------
	function activar(origen = '') {
		var elSelected = $("input[type='radio'][name='radio']:checked").val();
		document.getElementById("busc_formulario_hce_1").removeAttribute('disabled');
		document.getElementById("wconfhce_1").removeAttribute('disabled');
		document.getElementById("input_pro").disabled = false;
		document.getElementById("input_insu").disabled = false;
		document.getElementById("input_insu").checked = false; 
		document.getElementById("input_pro").checked = false; 
		document.getElementById("wconfhce_1").disabled = false;
		
		//document.getElementById("col_label_terc").style.display = (elSelected == 'orden') ? "table-cell" : "none";
		//document.getElementById("col_camp_terc").style.display = (elSelected == 'orden') ? "table-cell" : "none";	
		
		document.getElementById("col_camp_fhce").style.display = (elSelected != 'orden') ? "table-cell" : "none";
		document.getElementById("col_camp_chce").style.display = (elSelected != 'orden') ? "table-cell" : "none";
		document.getElementById("col_label_fhce").style.display = (elSelected != 'orden') ? "table-cell" : "none";
		document.getElementById("col_label_chce").style.display = (elSelected != 'orden') ? "table-cell" : "none";
		document.getElementById("col_label_proart").colSpan = "";
		document.getElementById("col_camp_proart").colSpan = "";
		document.getElementById("col_label_proart").style.display = (elSelected != 'orden') ? "table-cell" : "none";
		document.getElementById("col_camp_proart").style.display = (elSelected != 'orden') ? "table-cell" : "none";
		//document.getElementById("col_label_cencos").style.width = (elSelected != 'orden') ? "20%" : "40%";
		document.getElementById("enc_label_procord").style.display = (elSelected == 'orden') ? "table-row" : "none";
		document.getElementById("row_camp_procord").style.display = (elSelected == 'orden') ? "table-row" : "none";		
		document.getElementById("chkTodosProc").checked = false;
		camposEstadoProc();
		var paramOrigen = origen == 'editar' ? origen : 1;
		change_edit('tipo_origen', paramOrigen);
		document.getElementById("busc_procedimiento_1").value = '';	
		document.getElementById("filaArticulo").style.display = "none";
		document.getElementById("filaTipoOrden").style.display = "none";
		document.getElementById("busc_articulo_1").value = "";
		var radio_checked = '';
		if(document.querySelector('input[name="radio"]:checked')) {
			radio_checked = document.querySelector('input[name="radio"]:checked').value;
			if(radio_checked=="evento"){
				document.getElementById("wconfhce_1").disabled = true;  
			}
		}
	}
	
	//----------------------------------------------------------
	function traer_log(){
		
		var tipo = document.getElementById('select_dos').value;
		var esCCA = '';
		
		$.post(_URL_AJAX,
		{
			consultaAjax:	'',
			wemp_pmla:		$('#wemp_pmla').val(),
			esCCA:			tipo,
			fecha: 			$('#fecha').val(),
			accion:			'obtener_logs',
			
		}, function (response) {
			$("#listado_log").html("");
			if(response.length>0) {
				
				var data = response;
				var fila = "";
				var tr = "";
				
				var des = '';
				var des2 = '';
				var err = ''; 
				for (var id in data) {
					
					err = data[id]['Logerr'] == null ? '' : data[id]['Logerr'];
					if(data[id]['Logtip'] != 'cca'){
					err = data[id]['Logerr'] == null ? '' : data[id]['Logerr']['error']+"-"+data[id]['Logerr']['mensaje'];
						
					}
					
					if(data[id]['Logtip']=='estancia'){
						try {
							cantidad=data[id]['Logdes']['estancias'];
							if(data[id]['Logerr']!=null){
								err = data[id]['Logerr']['mensaje'] == null ? '' : data[id]['Logerr']['mensaje']+" - ";
								if(err==''){
									err+="cantidad_Hab:"+(cantidad)+"-";
									for(var i=0;i<cantidad;i++){
										err += "Estancia: "+data[id]['Logerr']['estancia'+i]['idcargo']+"- respuesta: "+data[id]['Logerr']['estancia'+i]['respuesta'];
									}
								}
							}
						} catch(error) {
							console.log(error);
						}
						
						
						//des="doc: "+data[id]['Logdes']['wdoc']+"- nombre: "+data[id]['Logdes']['wno1']+" "+data[id]['Logdes']['wno2']+" "+data[id]['Logdes']['wap1']+"-Historia: "+data[id]['Logdes']['whistoria']+"-Ingreso: "+data[id]['Logdes']['wing']+
						//"-"+data[id]['Logdes']['wnomcon']+"-"+data[id]['Logdes']['wnomemp']+"-"+data[id]['Logdes']['wnprocedimiento'];
						des="doc: - nombre: "+data[id]['Logdes']['wno1']+" "+data[id]['Logdes']['wno2']+" "+data[id]['Logdes']['wap1']+"-Historia: "+data[id]['Logdes']['whistoria']+"-Ingreso: "+data[id]['Logdes']['wing']+
						"-"+data[id]['Logdes']['wnomcon']+"-"+data[id]['Logdes']['wnomemp']+"-"+data[id]['Logdes']['wnprocedimiento'];
					} else if(data[id]['Logdes'] != null && data[id]['Logtip'] == 'cca' && data[id]['Logdes']['ccato']!='') {
						des = data[id]['Logdes']['ccacon']+"-"
							  +data[id]['Logdes']['ccacup']+"-"
							  +data[id]['Logdes']['ccafhce']+"-"
							  +data[id]['Logdes']['ccato'];
					} else if(data[id]['Logdes'] != null && data[id]['Logtip'] != 'cca' && data[id]['Logdes']['ccato']!='') {
						des = data[id]['Logdes']['wnomcon']+"-"
							  +data[id]['Logdes']['wpronom']+"-"
							  +data[id]['Logdes']['wnomemp'];
					} else {
						des = '';
					}
					
					if(data[id]['Logdes2'] != null && data[id]['Logtip'] == 'cca' && data[id]['Logdes2']['ccato']!='') {
						des2 = data[id]['Logdes2']['ccacon']+"-"
							  +data[id]['Logdes2']['ccacup']+"-"
							  +data[id]['Logdes2']['ccafhce']+"-"
							  +data[id]['Logdes2']['ccato'];
					} else if(data[id]['Logdes2'] != null && data[id]['Logtip'] != 'cca' && data[id]['Logdes2']['ccato']!='') {
						des2 = data[id]['Logdes2']['wnomcon']+"-"
							  +data[id]['Logdes2']['wpronom']+"-"
							  +data[id]['Logdes2']['wnomemp'];
					} else {
						des2 = '';
					}
					
					fila = id%2 == 0 ? 'fila1' : 'fila2';
					
					tr += "<tr class='"+fila+"'>"
						  + "<td>"+data[id]['fecha']+"</td>"
						  + "<td>"+data[id]['hora']+"</td>"
						  + "<td>"+data[id]['usuario']+"</td>"
						  + "<td>"+des+"</td>"
						  + "<td>"+des2+"</td>"
						  + "<td>"+data[id]['tipoTransaccion']+"</td>"
						  + "<td>"+err+"</td>"
					+   "</tr>";
				}
			} else {
				tr = "<tr class='fila1'><td colspan='7' style='text-align: center;'>No existen registros.</td></tr>";
			}
			
			$("#listado_log").append(tr);
		},'json');
	}
	
	function guardar_estancia_test() {
		
		var whis = document.getElementById('whis');
		var wing = document.getElementById('wing');
		
		var msjErrores = '';
		
		if( whis.value == '') {
			msjErrores += 'El campo "Historia" es requerido. \n';
		}
		
		if(wing.value == '') {
			msjErrores += 'El campo "Ingreso" es requerido. \n';
		}

		if(msjErrores != '') {
			jAlert(msjErrores, "Alerta");
			return;
		}
				
		document.getElementById("btn_guardar_estancia").disabled = true;
		
		$.post(_URL_AJAX,
		{
			consultaAjax:     	'',
			wemp_pmla:        	$('#wemp_pmla').val(),
			accion:           	'guardar_estancia_test',
			whis: whis.value,
			wing: wing.value,
			
		}, function (data) {			
			
			if(data.code) {
				whis.value = '';
				wing.value = '';
				document.getElementById("btn_guardar_estancia").disabled = false;
			}
			
			jAlert(data.msj, "Mensaje");
		}, 'json');
		 
	}
	
	function obtenerListadoLog() {
		
		var div_tabla_log = document.getElementById('div_table_logs');
		var tipo = document.getElementById('select_dos').value;
		var esCCA = '';

		div_tabla_log.innerHTML = '<img class="loading" src="data:image/gif;base64,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AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA==	">';
		
		$.post(_URL_AJAX,
		{
			consultaAjax:	'',
			wemp_pmla:		$('#wemp_pmla').val(),
			esCCA:			tipo,
			fecha: 			$('#fecha').val(),
			accion:			'obtener_logs_html',
			
		}, function (response) {
			div_tabla_log.innerHTML = response;
			$('#table_logs').DataTable({
				"language": {"url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"}
			});
			$('#table_logs').removeClass('dataTable');
		});		
	}
	
	function traer_terceros(input)
	{
		parametro = input.value;
		if(parametro.length > 2) {
			$.post(_URL_AJAX,
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				parametro:        parametro,
				accion:           'traer_terceros',
				tipo_cargo:       $('input[name="radio"]:checked').val()
			}, function (data) {
				ArrayInfoTerceros  = eval('(' + data + ')');
				cargar_terceros(ArrayInfoTerceros);
			});
		}
	}
	
	function cargar_terceros(ArrayValores)
	{
		var index			= -1;
		var arrayTerceros	= new Array();
		
		for (var cod_ter in ArrayValores)
		{
			index++;
			arrayTerceros[index] = {};
			arrayTerceros[index].value  	= cod_ter;
			arrayTerceros[index].label  	= cod_ter+'-'+ArrayValores[cod_ter]['nombre'];
			arrayTerceros[index].name  	= ArrayValores[cod_ter]['nombre'];
			arrayTerceros[index].especialidades  = ArrayValores[cod_ter]['especialidad'];
		}
		
		$("#camp_tercero").autocomplete({
			minLength: 	0,
			source: 	arrayTerceros,
			select: 	function( event, ui ){
				$("#camp_tercero").val(ui.item.label);
				$("#camp_tercero").attr('valor', ui.item.value);
				$("#camp_tercero").attr('nombre', ui.item.name);
				change_edit('tercero', 1);
				validarTerceroComodin(document.getElementById('camp_tercero').getAttribute('valor'));
				//cargarSelectEspecialidades(ui.item.especialidades);

				//datos_desde_tercero('on');
				//verificarDisponibilidad();

				return false;
			}
		});
		//limpiaAutocomplete('busc_terceros_'+n);
	}

	/*function cargarSelectEspecialidades( cadena,)
	{
		var especialidades = cadena.split(",");
		var html_options = "";
		for( var i in especialidades ){
			var especialidad = especialidades[i].split("-");
			html_options+="<option value='"+especialidad[0]+"'>"+especialidad[1]+"</option>";
		}
		$("#camp_especialidad").html( html_options );
	}*/
	
	function crearEtiquetasOrdenes(el){
		let nameProcEx = document.getElementById("filterExcludeProc").value;
		let nameProcOrden = document.getElementById("filterExcludeProcOrden").value;
		if((el == 'pe' && nameProcEx.length>2) || (el == 'po' && nameProcOrden.length>2)) {
			
			$.post(_URL_AJAX,
			{
				consultaAjax:     '',
				accion:           'traer_procedimientos',
				name_proc:  el == 'pe' ? nameProcEx : nameProcOrden,
			}, function (data) {
				var arrayProc 	= new Array();
				var index		= -1;
				for (var cod_ins in data)
				{
					index++;										
					arrayProc[index] = cod_ins+'-'+data[cod_ins]['nombre'];					
				}
				//var ms1 = $('#autocompleteProc').magicSuggest({});
				
				if(el == 'pe')
					elProcExc.setData(arrayProc);
				else
					elProcOrden.setData(arrayProc);
			}, 'json');
			
		}
	}
	
	function traer_especialidades()
	{
		if (document.getElementById("wesp").value.length>2)
		{
			$.post(_URL_AJAX,
			{
				parametro:        $('#wesp').val(),
				consultaAjax:     '',
				accion:           'traer_especialidades',
			}, function (data) {
				cargar_especialidades(data);
			}, 'json');
		}
	}
	
	function cargar_especialidades(ArrayValores)
	{
		var esp	= new Array();
		var index		  	= -1;
		for (var cod in ArrayValores)
		{
			index++;
			esp[index] = {};
			esp[index].value  = cod;
			esp[index].label  = cod+'-'+ArrayValores[cod]['nombre'];
			esp[index].nombre = ArrayValores[cod]['nombre'];
			esp[index].valor  = cod;
		}
		
		$( "#wesp" ).autocomplete({
		
			minLength: 	0,
			source: 	esp,
			select: 	function( event, ui ){
				$("#wesp").val(ui.item.label);
				$("#wesp").attr('valor', ui.item.valor);
				$("#wesp").attr("nombre", ui.item.nombre);
				//change_edit('formulario_hce', 1);
				
				return false;
			}
		});
	}
	
	function  validarTerceroComodin(valor) {
		
		var display = valor == '*' ? 'table-row' : 'none';
		
		document.getElementsByClassName('especialidad')[0].style.display = display;
		document.getElementsByClassName('especialidad')[1].style.display = display;
	}
	
	/* FIN DECLARACIONES FUNCIONES PRINCIPALES */
	
	// elProcExc/elProcOrden: esta variable se usara en cada lugar donde se 
	// requiera acceder al elemento inicializado con los tags
	var elProcExc;
	var elProcOrden;
	var origenGeneral;//variable que indica si el llamado a change_edit es desde la carga del formulario o desde el cambio de los campos
	
	$(document).ready(function() {
		
		/* REALIZAMO EL LLAMADO DE TODAS LA FUNCIONES DE CARGA INICIAL */
		
		$('[data-toggle="tooltip"]').tooltip();
		traer_conceptos('%');
		traer_tipos_empresa();
		
		inicializarDatepickerFecha();
		elProcExc = $('#autocompleteProc').magicSuggest({          
		  data: [],
		  inputCfg: {
						'id' : 'filterExcludeProc',
						'oninput': 'crearEtiquetasOrdenes("pe")'
					},
			allowFreeEntries: false,
			maxDropHeight: 145,
			hideTrigger: true,
			maxSelection: null
        });
		elProcExc.disable();
		elProcOrden = $('#autocompleteProcOrden').magicSuggest({          
		  data: [],
		  inputCfg: {
						'id' : 'filterExcludeProcOrden',
						'oninput': 'crearEtiquetasOrdenes("po")'
					},
			allowFreeEntries: false,
			maxDropHeight: 145,
			hideTrigger: true,
			maxSelection: null
        });
		$("#tipo_orden").change(function() {			
			elProcOrden.clear(true);
			elProcExc.clear(true);
			document.getElementById('chkTodosProc').checked = false;
			let state = document.getElementById('chkTodosProc').checked;	
			//document.getElementById("enc_exc_proc").style.display = ($("#tipo_orden").val() != "" && $("#busc_concepto_1").val() != "") ? 'table-row' : 'none';
			//document.getElementById("row_exc_proc").style.display = ($("#tipo_orden").val() != "" && $("#busc_concepto_1").val() != "") ? 'table-row' : 'none';	
			//document.getElementById("busc_procedimiento_1").value =  $("#tipo_orden").val() != "" ? '*-TODOS' : '';
			//document.getElementById('busc_procedimiento_1').setAttribute('valor', $("#tipo_orden").val() != "" ? '*' : '');
			//document.getElementById("busc_procedimiento_1").disabled = $("#tipo_orden").val() != "" ? true : false;
			document.getElementById("col_label_proex").style.display = ($("#tipo_orden").val() != "") ? 'table-cell' : 'none';
			document.getElementById("col_camp_proex").style.display = ($("#tipo_orden").val() != "") ? 'table-cell' : 'none';
			document.getElementById("divChkTodosProc").style.display = ($("#tipo_orden").val() != "") ? 'inline' : 'none';
			document.getElementById('autocompleteProcOrden').focus();
			document.getElementById('filterExcludeProcOrden').focus();
			document.getElementById('filterExcludeProc').focus();	
			document.getElementById('tipo_orden').focus();
			elProcExc.disable();
			elProcOrden.enable();
			change_edit('tipo_orden', 1);
		});
		$( "#chkTodosProc" ).change(function() {		  
		  camposEstadoProc();
		  change_edit('todos_proc', 1);
		});
		$(elProcOrden).on('selectionchange', function(e,m){
		  change_edit('procedimiento', origenGeneral);
		});
		$(elProcExc).on('selectionchange', function(e,m){
		  change_edit('procedimientoExc', origenGeneral);
		});
    });
	
	function camposEstadoProc(){
		let state = document.getElementById('chkTodosProc').checked;
		if(state){
			elProcOrden.clear(true);
			elProcExc.enable();
			elProcOrden.disable(); 
		}
		else{
			elProcExc.clear(true);
			elProcOrden.clear(true);
			elProcExc.disable();
			elProcOrden.enable(); 
		}
	}

	/*$(function() {
        var ms1 = $('#autocompleteProc').magicSuggest({          
		  data: [],
		  inputCfg: {
						'id' : 'filterExcludeProc',
						'oninput': 'crearEtiquetasOrdenesExcluidas()'
					},
			allowFreeEntries: false
        });
      });*/
	  
	var options_select_tipo_empresa = '';
	function traer_tipos_empresa(temcod = null){
		$.post(_URL_AJAX,
		{
			consultaAjax:     	'',
			wemp_pmla:        	$('#wemp_pmla').val(),
			accion:           	'traer_tipos_empresa',
			temcod:		  		temcod,
			
		}, function (data){
			if(temcod === null) {
				options_select_tipo_empresa = data;
			}
			$("#wtemp").html(data);
		},'json');
	}
	
	function traer_empresas(temcod, empcod = null){
		$.post(_URL_AJAX,
		{
			consultaAjax:     	'',
			wemp_pmla:        	$('#wemp_pmla').val(),
			accion:           	'traer_empresas',
			temcod:		  		temcod,
			empcod:	    		empcod
			
		}, function (data){
			$("#wemp").html(data);
			//change_edit('tipo_empresa', 1);
		},'json');
	}

</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet"/>

<style type="text/css">
input[type="search"] {
    font-family: FontAwesome;
}

        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}

        .brdtop {
            border-top-style: solid; border-top-width: 2px;
            border-color: #2A5BD0;
        }
        .brdleft{
            border-left-style: solid; border-left-width: 2px;
            border-color: #2A5BD0;
        }
        .brdright{
            border-right-style: solid; border-right-width: 2px;
            border-color: #2A5BD0;
        }
        .brdbottom{
            border-bottom-style: solid; border-bottom-width: 2px;
            border-color: #2A5BD0;
        }

        .alto{
            height: 140px;
        }

        .vr
        {
            display:inline;
            height:50px;
            width:1px;
            border:1px inset;
            /*margin:5px*/
            border-color: #2A5BD0;
        }

        .bgGris1{
            background-color:#F6F6F6;
        }

        .tbold{
            font-weight:bold;
        }
        .parrafoTal{
            color: #676767;
            font-family: verdana;
        }

        .titulopagina2
        {
            border-bottom-width: 1px;
            border-color: <?=$bordemenu?>;
            border-left-width: 1px;
            border-top-width: 1px;
            font-family: verdana;
            font-size: 18pt;
            font-weight: bold;
            height: 30px;
            margin: 2pt;
            overflow: hidden;
            text-transform: uppercase;
        }


        /**************************/
        /* Estilos para los menús */
        /**************************/

        #nav, #nav ul{
            margin:0;
            padding:0;
            list-style-type:none;
            list-style-position:outside;
            position:relative;
            line-height:1.5em;
        }

        #nav a{
            display:block;
            padding:0px 8px;
            /*border:1px outset #BCBCBC;*/
            /*color:#212121;*/
            /*text-decoration:overline;*/
            background-color:#E4E4E4;
            height: 20px;
            border-top: 2px <?=$bordemenu?> solid;
            /*border-bottom: 3px #2A5DB0 solid;*/
        }
/*#nav a{
    display:block;
    padding:0px 8px;
    border:1px outset #BCBCBC;
    color:#212121;
    text-decoration:none;
    background-color:#E4E4E4;
}*/

        #nav a:hover{
            background-color:#CCCCCC;
            color:#333333;
        }

        #nav li{
            float:left;
            position:relative;
        }

        #nav ul {
            position:absolute;
            display:none;
            width:12em;
            top:1.5em;
        }

        #nav li ul a{
            width:25em;
            height:auto;
            float:left;
            text-align:left;
        }

        #nav ul ul{
            top:auto;
        }

        #nav li ul ul {
            left:12em;
            margin:0px 0 0 10px;
        }

        #nav li:hover ul ul, #nav li:hover ul ul ul, #nav li:hover ul ul ul ul{
            display:none;
        }
        #nav li:hover ul, #nav li li:hover ul, #nav li li li:hover ul, #nav li li li li:hover ul{
            display:block;
        }
 /* Style the tab */
.tab {
overflow: hidden;
border: 1px solid #ccc;
background-color: #f1f1f1;
}

  
/* Style the buttons that are used to open the tab content */
.tab button {
background-color: inherit;
float: left;
border: none;
outline: none;
cursor: pointer;
padding: 14px 16px;
transition: 0.3s;
}

/* Change background color of buttons on hover 
  */
.tab button:hover {
background-color: #ddd;

  }

/* Create an active/current tablink class */
.tab button.active 
  {
    background: #62bbe8;
    color: #fff;
}
.prueba{
display:none;
padding: 6px 12px;
border: 1px solid #ccc;
border-top: none;
}
/* Style the tab content */
.tabcontent {
display: none;
padding: 6px 12px;
border: 1px solid #ccc;
border-top: none;
} 
#search {

  width: 70%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}
#search2 {

  width: 60%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}
.message{
	background-color: white;
	color: black;
	border: 2px solid #4CAF50;font-weight: bold;border-radius: 12px;
}
#select_dos {
     background: transparent;
     border: 1px solid;
     font-size: 14px;
     height: 30px;
     padding: 5px;
     width: 250px;
	 border-radius: 12px;
  }

	.loading{
		width: 65px;
		height: 65px;
	}
    </style>
</head>
<body style="width: 80%;">
    <center>
    <div id="contenedor_centrado">
<?php

$nombre_tema = 'CONFIGURACI&Oacute;N CARGOS AUTOM&Aacute;TICOS';
encabezado("<div class='titulopagina2'>".$nombre_tema."</div>", $wactualiz, $wlogoempresa);

?>
<a href="../manuales/cargos_automaticos.pdf" onclick="window.open(this.href);return false" style="cursor : pointer;    float: right; padding-bottom: 5px;">Manual de Usuario</a>
<a href="../manuales/cargos_automaticos_tec.pdf" onclick="window.open(this.href);return false" style="cursor : pointer;    float: right; padding-bottom: 5px; padding-right: 15px;">Manual Tecnico</a>
<div style="clear: both;"></div>

<div class="tab ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
	<button class="ui-state-default ui-corner-top ui-tabs-anchor tablinks " role="tab" tabindex="-1" aria-controls="bcProtocolosAvanzado" aria-labelledby="ui-id-2" aria-selected="false" onclick="openTab(event,'listadoA')" >LISTADO</button>
  	<button class="ui-state-default ui-corner-top ui-tabs-anchor tablinks active" role="tab" tabindex="-1" aria-controls="bcProtocolosAvanzado" aria-labelledby="ui-id-1" aria-selected="false "onclick="openTab(event,'configurar',true)" >CONFIGURACI&Oacute;N</button>
	<!-- <button class="ui-state-default ui-corner-top ui-tabs-anchor tablinks" role="tab" tabindex="-1" aria-controls="bcProtocolosAvanzado" aria-labelledby="ui-id-1" aria-selected="false "onclick="openTab(event,'estancia')" >ESTANCIA</button> -->
	<button class="ui-state-default ui-corner-top ui-tabs-anchor tablinks " role="tab" tabindex="-1" aria-controls="bcProtocolosAvanzado" aria-labelledby="ui-id-1" aria-selected="false "onclick="openTab(event,'log')" >LOG</button>
</div>
<div id="listadoA" class="prueba ui-tabs-panel ui-widget-content ui-corner-bottom"  style="display: none;">
<br>
<br>
  <tr align='center'>
  			<td align='center' style='98%'>
  	<div id = "div-tabla-configs">
	
	</div>
    </td>
  </tr>
<p></p> 
</div>
<!-- TAB CONFIGURACIÓN -->
<input type="hidden" value="<?php echo $wemp_pmla; ?>" id="wemp_pmla" />
<div id="configurar" class="prueba ui-tabs-panel ui-widget-content ui-corner-bottom" style="display: block;">
	<?php
		$session = explode('-', $_SESSION['user']);
	?>
	<input type="hidden" value="<?php echo $session[1]; ?>" id="wuse" />
	<form name="form_cca" id="form_cca">
		<table style="width: 100%;">
			<tr align='center'>
				<td align='center' style='98%'>
					<table align='center' id='tabla_ingreso_cargos' style="width: 100%;">
						<tr>
							<td align=center colspan='6' class='encabezadoTabla'><b>I N G R E S O &nbsp&nbsp&nbsp D E &nbsp&nbsp&nbsp C A R G O S &nbsp&nbsp&nbsp AUTOMATICOS</b></td>
						</tr>
						<tr class='encabezadoTabla'  align="center">
							<td>
								Tipo Cargo
							</td>							
							<td class="fila2 cargo_cargo" colspan="5" align="left" >
								Evento <input name="radio" value="evento" id="evento" type="radio" onclick="activar();"/>
								Dato <input name="radio" value="dato" id="dato" type="radio" onclick="activar();" />
								Orden <input name="radio" value="orden" id="orden" type="radio" onclick="desactivar();" />
								Aplicaci&oacute;n <input name="radio" value="aplicacion" id="aplicacion" type="radio" onclick="desactivar();" />
							</td>
						</tr>
						<tr class='encabezadoTabla'  align="center" style = "display:none" id="filaArticulo">
							<td>
								Medicamento/Insumo
							</td>							
							<td class="fila2 cargo_cargo" colspan="5" align="left" >
								<input type='text' name = 'articulo' id='busc_articulo_1' valor='' nombre='' onchange="change_edit('articulo', 1)" oninput="traer_insumos('busc_articulo_1')" size='60'  style="width: 97%;text-transform:uppercase;"/>
							</td>
						</tr>
						<tr class='encabezadoTabla'  align="center" style = "display:none" id="filaTipoOrden">
							<td>
								Tipo Orden
							</td>							
							<td class="fila2 cargo_cargo" colspan="5" align="left" >
								<select name="tipoorden" id='tipo_orden' style='width: 100%;'>									
								</select>
							</td>
						</tr>
						<tr class='encabezadoTabla'  align="center">
							<td>
								Tipo Centro Costos
							</td>
							<td class="fila2 cargo_cargo" colspan="5" align="left" >
								Hospitalizaci&oacute;n <input name="check_tcc" value="H" id="hospitalizacion" type="checkbox" onclick="change_edit('tipo_cco', 1);" />
							    Domiciliaria <input name="check_tcc" value="D" id="domicilaria" type="checkbox" onclick="change_edit('tipo_cco', 1);" />
								Ayudas <input name="check_tcc" value="A" id="ayudas" type="checkbox" onclick="change_edit('tipo_cco', 1);" />
								Urgencia <input name="check_tcc" value="U" id="urgencias" type="checkbox" onclick="change_edit('tipo_cco', 1);" />
								Cirug&iacute;a <input name="check_tcc" value="Cx" id="cirugia" type="checkbox" onclick="change_edit('tipo_cco', 1);" />
							</td>
						</tr>
						<!-- NUEVO 2021-12-22 -->
						<tr class='encabezadoTabla'  align="center">
							<td>
								Tipo Empresa
							</td>
							<td class="fila2" align='left' id="col_tipo_emp">
								<select name='wtemp' id='wtemp'  onchange="traer_empresas(this.value); change_edit('tipo_empresa', 1);" style='width:100%;text-transform:uppercase;'></select>
							</td>
						</tr>
						<tr class='encabezadoTabla'  align="center">
							<td>
								Empresa
							</td>
							<td class="fila2" align='left' id="col_emp">
								<select name='wemp' id='wemp' onchange="change_edit('empresa', 1);" style='width:100%;text-transform:uppercase;'></select>
							</td>
						</tr>
						<tr class='encabezadoTabla'  align="center">
							<td>
								Facturable
							</td>
							<td class="fila2 cargo_cargo" colspan="5" align="left" >
								<input type="checkbox" name="check_wfac" id="wfac"  onclick="change_edit('facturable', 1);" checked/>
							</td>
						</tr>
						<!-- FIN NUEVO -->
						<tr id='tr_enc_det_concepto' class='encabezadoTabla fila-encabezado-1'  align='center' style="width:100%">
							<td style="width:30%"> Concepto </td>
							<td style="width:40%" id = "col_label_cencos"> Cen.Costos que realiza</td>
							<td style="width:30%; display: table-cell" id="col_label_terc"> Tercero </td>
						</tr>
						<tr class='fila2 cargo_cargo fila-detalle-1'>
							<td align='left' >
								<input type='text' name="con" id='busc_concepto_1' value='' valor='' onchange="change_edit('concepto', 1);" nombre='' size='21' style="text-transform:uppercase; width: 97%;" >
							</td>
							<td align='left'>
								<select name="cco" id='wccogra_1' style='width: 100%;' onchange="change_edit('centro_costo', 1)"></select>
							</td>
							<td align='left' id="col_camp_terc" style='display: table-cell;'>
								<input type='text' name="colter" id='camp_tercero' value='' valor='' nombre='' oninput="traer_terceros(this)" size='30' style="width: 97%;" disabled/>
							</td>
						</tr>
						<tr class="encabezadoTabla fila-encabezado-2" align='center'>
							<td style="width:30%" id="col_label_proart">
								Procedimiento/Examen <input name="radio_p" value='procedimiento' id="input_pro" onclick="change_edit('proc_o_insu', 1);" type="radio" /> 
								Articulo <input name="radio_p" value='insumo' onclick="change_edit('proc_o_insu', 1);" id="input_insu" type="radio" />								
							</td>							
							<td style="width:40%" id="col_label_fhce"> Formulario HCE </td>
							<td style="width:30%" id="col_label_chce"> Campo HCE </td>
						</tr>
						<tr class="fila-detalle-2">
							<td align='left' id="col_camp_proart">
								<input type='text' name="procoins" id='busc_procedimiento_1' value='' valor='' nombre='' oninput="validar()"  size='30'  style="width: 99%;text-transform:uppercase;"/>
							</td>
							<td align='left' id="col_camp_fhce">
								<input type='text' name="fhce" id='busc_formulario_hce_1' value='' valor='' nombre='' oninput="traer_fhce()" size='30' style="width: 97%;" />
							</td>
							<td align='left' id="col_camp_chce">
								<select name='confhce' id='wconfhce_1' oninput="traer_fhce();" onchange="change_edit('consecutivo_hce', 1);" style='width:100%;text-transform:uppercase;'></select>
							</td>
						</tr>
						<tr class="encabezadoTabla fila-encabezado-2 especialidad" align='center' style="display: none;">
							<td colspan="3" id="col_label_fhce"> Especialidad </td>
						</tr>
						<tr class="fila-detalle-2 especialidad" style="display: none;">
							<td colspan="3" align='left' id="col_camp_chce">
								<input type='text' name="wesp" id='wesp' value='' valor='' nombre='' oninput="traer_especialidades()" size='30' style="width: 100%;text-transform:uppercase;" />
							</td>
						</tr>
						<tr id = "enc_label_procord" style = "display:none;">							
							<td colspan="8">
								<table style="width:100%">
									<tr class="encabezadoTabla">
										<td align=center style="width:50%">Procedimiento <div id = "divChkTodosProc" style = "display:none">(<label>Todos<input id = "chkTodosProc" type="checkbox"/></label>)</div></td>
										<td id = "col_label_proex" align=center style="width:50%; display:none">Procedimientos Excluidos</td>
									</tr>
								</table>	
							</td>
						</tr>
						<tr class='fila2 cargo_cargo' id = "row_camp_procord" style = "display:none;">							
							<td colspan="8">
								<table style="width:100%">
									<tr>
										<td style="width:50%;vertical-align:top;">
											<div id="autocompleteProcOrden"></div>
										</td>
										<td id = "col_camp_proex" style="width:50%; vertical-align:top; display:none">
											<div id="autocompleteProc"></div>
										</td>	
									</tr>
								</table>
							</td>
						</tr>			
					</table>
				</td>
			</tr>
		</table>
	</form>
	<br>      
	<br>
	<table align='center'>
		<tr>
			<td align='center' colspan=9>
				<button type='button' id="button_guardar" onclick='guardar_cca()' >Guardar Configuraci&oacute;n</button>
			</td>
		</tr>
	</table>
</div> 
<!-- FIN TAB CONFIGURACIÓN -->
<div id="log" class="prueba ui-tabs-panel ui-widget-content ui-corner-bottom"  style="display: none;">
	<br>
	<br>
	<div class="row" style="display:block;">
		 <fieldset>
			<legend>Par&aacute;metros de B&uacute;squeda</legend>
			<select id="select_dos" class="form-control form-control-sm">
			  <option value="" selected>(seleccione) Tipo log</option>
			  <option value="cca">Configuraci&oacute;n Cargo Autom&aacute;tico</option>
			  <option value="ccadat">Cargo Autom&aacute;tico (Dato)</option>
			  <option value="ccaeve">Cargo Autom&aacute;tico (Evento)</option>
			  <option value="ccaord">Cargo Autom&aacute;tico (Orden)</option>
			  <option value="ccapre">Cargo Autom&aacute;tico (Aplicaci&oacute;n)</option>
			  <option value="estancia">Cargo Autom&aacute;tico (Estancia)</option>
			</select>
			<input type="text" name="fecha" value="" size="21" id="fecha" placeholder="Fecha"   />
			<input type='button' value='Obtener' onclick='obtenerListadoLog();'>
		 </fieldset>
	</div>
	<br>
	<br>
	<div id="div_table_logs"></div>
</div>
<!-- TAB CONFIGURACIÓN -->
<div id="estancia" class="prueba ui-tabs-panel ui-widget-content ui-corner-bottom"  style="display: none;">
	<form name="form_estancia_test" id="form_estancia_test">
		<table>
			<tr align='center'>
				<td align='center' style='98%'>
					<table align='center' id='tabla_ingreso_cargos'>
						<tr>
							<td align="center" colspan="4" class="encabezadoTabla"><b>FORMULARIO TEST - ESTANCIA PACIENTES</b></td>
						</tr>
						<tr class="encabezadoTabla"  align="center">
							<td> Historia </td>
							<td class="fila2 cargo_cargo" align="left" >
								<input type='number' name="whis" id="whis" />
							</td>
							<td> Ingreso </td>
							<td class="fila2 cargo_cargo" align="left" >
								<input type='number' name="wing" id="wing" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<br>      
	<br>
	<table align='center'>
		<tr>
			<td align='center' colspan="4">
				<button type='button' id="btn_guardar_estancia" onclick='guardar_estancia_test()'>Grabar Estancia</button>
			</td>
		</tr>
	</table>
</div>
<br>
	<table align='center'>
		<tr>
			<td align='center' colspan=9>
				<input type='button' value='Cerrar Ventana' onclick='cerrarVentanaPpal();'>
			</td>
		</tr>
	</table>	
</html>				
