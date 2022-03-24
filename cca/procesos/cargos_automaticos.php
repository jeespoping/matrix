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
$wactualiz = "(Febrero 15 de 2022)";

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
			
		procExc = '';
		if(tipo_cargo == 'orden') {
			tipoOrden = document.getElementById('tipo_orden').value;			
			if(tipoOrden != '') {
				arrayProcExc = elProcExc.getValue();
				for(let i = 0; i < arrayProcExc.length; i++){
					procExc.push(arrayProcExc[i].split('-')[0]);
				}
				procExc = procExc.join();				
			}
			
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
				name_hce:        $('#wesp').val(),
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
    </style>
</head>
<body style="width: 80%;">
    <center>
    <div id="contenedor_centrado">
<?php

$nombre_tema = 'CONFIGURACI&Oacute;N CARGOS AUTOM&Aacute;TICOS';
encabezado("<div class='titulopagina2'>".$nombre_tema."</div>", $wactualiz, $wlogoempresa);

?>
<a href="../manuales/manual_cargos_automaticos.pdf" onclick="window.open(this.href);return false" style="cursor : pointer;    float: right; padding-bottom: 5px;">Manual de Usuario</a>
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
