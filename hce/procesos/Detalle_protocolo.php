<?php
include_once("conex.php"); header('Content-type: text/html;charset=ISO-8859-1'); ?>

<?php
//Para que en las solicitudes ajax no imprima <html><head> etc
if( isset($consultaAjax) == false ){	
?>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<title>Detalle Protocolos</title>
	<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>	
	<script src="../../../include/root/toJson.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	
	<style>
		.enlinea, .obligatorio{
			display:inline-block;
			vertical-align: top;
			/*display: -moz-inline-stack;*/ /* FF2*/
			zoom: 1; /* IE7 (hasLayout)*/
			*display: inline; /* IE */
		}
	</style>
	<style>
		/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
		.ui-datepicker {font-size:12px;}
		/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
		.ui-datepicker-cover {
			display: none; /*sorry for IE5*/
			display/**/: block; /*sorry for IE5*/
			position: absolute; /*must have*/
			z-index: -1; /*must have*/
			filter: mask(); /*must have*/
			top: -4px; /*must have*/
			left: -4px; /*must have*/
			width: 200px; /*must have*/
			height: 200px; /*must have*/
		}
	</style>
	
	<script>
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
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['esp']);
	
	</script>

<script type="text/javascript">
	
	var condiciones_item = new Object();
	var editando_item = false;
	var ejeY = 0;
	
	$(document).ready( function(){
		$("#wdetfac").datepicker({ minDate: 0 });
		$(".solonumeros").keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});
		$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
	});
	
    function Ampliarventana()
    { 
      window.open(window.location.href, "TIPO","width=2000,height=1000,scrollbars=YES") ;
      $( "#Cerrarventana" ).prop( "disabled", false );    
    }

	function nuevoCampo( cod_formulario ){
		//Consultamos el nuevo consecutivo		
		var rango_superior = 245;
		var rango_inferior = 11;
		cod_formulario = $.trim( cod_formulario );
		editando_item = false;
		ejeY = 70;
		cerrarConfigurarItem();
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Detalle_protocolo.php', { wemp_pmla: wemp_pmla, action: "consultarUltimoConsecutivo",  codigo_formulario: cod_formulario, consultaAjax: aleatorio} ,
			function(data) {
				data = $.trim( data );
				//if( (/^\d+$/).test(data) ){
				if( isJson(data) ){
					data = eval( '('+data+')' );
					$('#formulario_formato_item').show();
					//valores por defecto
					$("#wdetpro").val( cod_formulario ).attr('disabled',true);
					$("#wdetcon").val( data.ultimo_consecutivo ).attr('disabled',true);	
					$("#wdetorp").val( data.ultimo_orden_pantalla );
					$("#wdetcol").val("1");
					$("#wdetnse").val("1");
					$("#wdetfac").val( $("#fecha_server").val() );
					
					//2015-01-14$("#wdetvim").attr('checked',true);
					$("#wdetcpp").attr('checked',true);
					
					ejeY = 70;
					$("#wdetest").attr("checked",true);
				}else{
					alert("Error");				
				}
				$.unblockUI();
			});
	}
	
	function cerrarConfigurarItem(){
		$("#td_archivo_validacion").html('');				
		$("#td_campo_a_validar").html('');
		$("#formulario_formato_item").find(':input:not(:button)').val('').attr('checked',false)
		$("#formulario_formato_item").find(':input:not(:button)').attr('readonly',false);
		$("#formulario_formato_item").find(':input:not(:button)').attr('disabled',false);
		$("#titulo_formulario_item").text('Nuevo campo');
		$('#formulario_formato_item').hide();
		$(".obligatorio").remove();
		$("#wdetfac").val("");
		$("#wdettip").val("");
		
		//Llevar al ppio de la pagina
		ejeY = ejeY - 50;
		//Para que vaya al tr donde estaba
		$('html, body').animate({
			scrollTop: ejeY+'px',
			scrollLeft: '0px'
		},0);
	}

	function mostrarConsecutivo( ele, consecutivo, dettip ){
		var wdetpro = $("#wdetpro_global").val();
		var wemp_pmla = $("#wemp_pmla").val();
		editando_item = true;
		
		//Guardar la distancia Y donde esta el tr, para que cuando le den click en guardar o cerrar se ubique donde estaba
		ele = jQuery( ele );
		posicion = ele.offset();
		ejeY = posicion.top;
		
		$("#div_auxiliar").html('');
		$.blockUI({ message: $('#msjEspere') });
		$.post('Detalle_protocolo.php', { wemp_pmla: wemp_pmla, wformulario:'3', dettip:dettip, wdetpro:wdetpro, action: "mostrarCampo",  consecutivo: consecutivo, consultaAjax: 111} ,
			function(data) {
				$.unblockUI();
				$("#div_auxiliar").html(data);				
				$('#formulario_formato_item').show();
				$("#titulo_formulario_item").text('Editar campo');
				$("#wdetfac").datepicker();
				
				//Para que suba al inicio de la pagina
				$('html, body').animate({
					scrollTop: '0px',
					scrollLeft: '0px'
				},0);
	   
				$(".solonumeros").keyup(function(){
					if ($(this).val() !="")
						$(this).val($(this).val().replace(/[^0-9]/g, ""));
				});
			}
		);	
	}
	
	//Funcion que trae que campos del panel de configuracion son enable, disable y readonly
	function consultarCondicionesTipoItem(){
		var wemp_pmla = $("#wemp_pmla").val();
		var tipoItem = $("#wdettip").val();
		if( tipoItem == "" ){
			return;
		}
		
		//Las condiciones ya fueron cargadas antes?
		if( condiciones_item[ tipoItem ] != undefined ){
			parsearFormulario(   condiciones_item[ tipoItem ]   );
			return;
		}
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		//Realiza el llamado ajax con los parametros de busqueda
		$.post('Detalle_protocolo.php', { wemp_pmla: wemp_pmla, wformulario:'3', action: "consultarCondicionesItem", tipo: tipoItem, consultaAjax: aleatorio} ,
			function(data) {
				parsearFormulario( data );
			});
	}
	
	//FUncion que se encarga de asignar disable, enable, readonly y el codigo html a determinados campos del panel de configuracion
	function parsearFormulario( condiciones ){
		if( isJson( condiciones ) == false ){
			alert( "No se cargaron las condiciones para el formulario" );
			aplicando_configuracion = false;
			return;
		}
		$(".obligatorio").remove();
		condiciones = condiciones.replace("\\","");
         
		//Guardo en el objeto, las condiciones para un item(objeto) de este tipo
		var tipo = $("#wdettip").val();
		if( condiciones_item[ tipo ] == undefined ){
			condiciones_item[ tipo ] = condiciones;
		}		
		
		var i=0;		
		condiciones = eval( '('+condiciones+')' );
		
		var div_obligatorio = "<div class='obligatorio enlinea'><font color=0000FF><b>(*)</b></font></div>";
		var archivo_validacion_obligatorio = false;
		var existe_archivo_validacion = false;
		var existe_campo_validar = false;
		$.each( condiciones, function(ind, val){
			if( val.archivo_validacion ){
				$("#td_archivo_validacion").html( val.archivo_validacion );		
				existe_archivo_validacion = true;
			}
			if( val.campo_validar){
				$("#td_campo_a_validar").html( val.campo_validar );
				existe_campo_validar = true;
			}
			$("#"+ind).attr( 'enabled', true );
			$("#"+ind).attr( 'disabled', false );
			
			$("#"+ind).attr( val.atributo, true );
			if( val.obligatorio == 'on' ){
				$("#"+ind).parent().prev().append(div_obligatorio);
			}
			if( val.atributo == 'Disabled' && ind != 'wdetarc')
				$("#"+ind).val('');
			
			if( ind == "wdetarc" && val.obligatorio == 'on' )
				archivo_validacion_obligatorio = true;
				
		});
		if( existe_archivo_validacion == false){
			$("#td_archivo_validacion").html("");
		}
		if( existe_campo_validar == false ){
			$("#td_campo_a_validar").html("");
		}
		
		if( archivo_validacion_obligatorio == true && $("#td_archivo_validacion").html() != '' && $("#td_campo_a_validar").html() == '' )
			 $("#td_campo_a_validar").prev().append(div_obligatorio);
			 
		$("#wdetpro, #wdetcon").attr('disabled',true);
		
		//var width_div = $("#formulario_formato_item").width();
		//var body_width = $("body").width();
		//var pos_left = parseInt(body_width) - parseInt(width_div);

		//$.blockUI({  css: { width: width_div+'px', left: pos_left, top: '50'}, message: $('#formulario_formato_item') });
	}
	
	//Funcion que se llama cuando el select de "archivo de validacion" tiene un cambio, trae el codigo html del campo del archivo validacion
	function consultarCampoArchivoValidacion(){
		var wemp_pmla = $("#wemp_pmla").val();
		var archivo_validacion = $("#wdetarc").val();
		var tipo_item = $("#wdettip").val();
		
		if( archivo_validacion == "" ){
			return
		}		
		//muestra el mensaje de cargando
		//$.blockUI({ message: $('#msjEspere') });
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('Detalle_protocolo.php', { wemp_pmla: wemp_pmla, action: "consultarCampoArchivoVal", tipo: tipo_item, archivo_validacion: archivo_validacion, consultaAjax: aleatorio} ,
			function(data) {
				$("#td_campo_a_validar").html(data);				
			});
	}
	
	//Funcion que se llama cuando se cierra la ventana modal de configuración del item, verifica que se hayan cumplido las condiciones necesarias
	function guardarConfigurarItem(){
		//Buscar que si lleno los obligatorios
		var cumple_obligatorios = true;
		
		if( $("#formulario_formato_item .obligatorio").length == 0 ){
			return;
		}

		if($("#wdetlrb").val()<=0 && $("#wdetprs").is(':checked') == true){
			alert('Si Selecciona Radio Boton debe ingresar la cantidad');
			return;
		}
		
		$("#formulario_formato_item .obligatorio").each(function(){
			var input = $(this).parent().next().find(':input');
			
			if( input != undefined ){
				valor = input.val();
				if( valor == '' && input.attr('type') != "checkbox" ){					
					cumple_obligatorios = false;
					return false; //salir del each
				}
			}
		});
		columnasdelformulario = $("#numero_columnas_formulario").val();

		if( ($('#wdetcoa').val()*1) >  (columnasdelformulario*1)){
			alert("El numero de columnas que ocupa debe ser menor a "+columnasdelformulario);
			$('#wdetcoa').focus();
			return;
		}
		
		if( cumple_obligatorios ){
			var columnasdelformulario = 0;
			var valores = new Array();
			//Guardar los campos ingresados
			//$("#formulario_formato_item input:not(:button)").each(function(){
			$("#formulario_formato_item").find(':input:not(:button)').each(function(){
				var obj = new Object();
				obj.clave = $(this).attr('id');
				if( $(this).is(':checkbox') ){
						obj.valor = $(this).is(':checked');
				}else{
					var valorx  = $(this).val();
					if( isEmpty(valorx) == false ){
						valorx = valorx.replace(/"/gi, "&quot;");
					}
					obj.valor = valorx;				
				}
				if( isEmpty(obj.valor) ){	//Esto, porque si la variable es false y se hace el if ( obj.valor != '' ), toma el false como '' y no lo guarda			
				}else{
					valores.push( obj );
				}
			});
			
			/*var fila_datos = new Object();
			fila_datos.consecutivo = $("#wdetcon").val();
			fila_datos.orden = $("#wdetorp").val();
			fila_datos.tipo = $("#wdettip").val();
			fila_datos.descripcion = $("#wdetdes").val();
			fila_datos.estado = "off";
			if ( $("#wdetest").is(':checked') ){
				fila_datos.estado = "on";
			}*/
			
			var datosJson = $.toJSON( valores ); //convertir el arreglo de objetos en una variable json
			
			aplicando_configuracion = false;
			/*$("#td_archivo_validacion").html('');
			$("#td_campo_a_validar").html('');
			$("#formulario_formato_item").find(':input:not(:button)').val('').attr('checked',false)
			$("#formulario_formato_item").find(':input:not(:button)').attr('readonly',false);
			$("#formulario_formato_item").find(':input:not(:button)').attr('disabled',false);*/
			//$.unblockUI();
			
			guardarItemParaFormulario( datosJson ); //Dibujar el item con las configuraciones elegidas
			
		}else{
			alert('Debe llenar los campos obligatorios antes de cerrar');
		}
	}
	
	function guardarItemParaFormulario( valores_resp ){
		var datos = new Array();		
		valores_resp = eval( valores_resp );	
		var dato = new Object();
		dato.formulario = $("#wdetpro").val();
		dato.valores = valores_resp;			
		datos.push( dato );	

		if( datos.length == 0 )
			return;
		
		var datosJson = $.toJSON( datos );	

		/*if( editando_item == false ){
			crearFilaNuevoItem( );
		}else{
			editandoFila();
		}
		return;*/
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Detalle_protocolo.php', { wemp_pmla: wemp_pmla, action: "guardarCambiosFormulario", editando: editando_item, datos: datosJson, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				data = $.trim( data );
				if( data == 'OK' ){
					//Quitar el (*)
					$("#formulario_formato_item .obligatorio").remove();
					
					
					if( editando_item == false ){
						crearFilaNuevoItem();
						//alert("Guardado");
					}else{
						location.reload();
						//Dado que en la actualizacion del item se pudo haber cambiado el orden en pantalla
						//de los otros campos, se recarga la pantalla
						editandoFila();
						//alert("Actualizado");
					}
					
					ejeY = ejeY - 50;
					//Para que vaya al tr donde estaba
					$('html, body').animate({
						scrollTop: ejeY+'px',
						scrollLeft: '0px'
					},0);
				}else if( isJson(data) ){
					data = eval('(' + data + ')');
					alert( data.msj );
				}else{
					alert("Error");
				}
			});
	}

	function crearFilaNuevoItem(){
		
		var campos = $("#campos_select").val();
		campos = eval('('+campos+')');
		var nombres = $("#nombres_campos").val();
		nombres = eval('('+nombres+')');
		var consecutivo = $("#wdetcon").val();
		var tipo = $("#wdettip").val();
		var clase_rojo = "";
		$("#wdetdes").val();

		if ( $("#wdetest").is(':checked') == false ){
			clase_rojo = " fondorojo";
		}
		
		var clase_fila = "";
		var cantidad_tr = $("#lista_elementos tbody").find('tr').length;
		( cantidad_tr % 2 == 1 ) ? clase_fila = 'fila2' : clase_fila = 'fila1';

		var html_code = "<tr id='filacon_"+consecutivo+"' class='"+clase_fila+"' ondblclick='mostrarConsecutivo(this, \""+consecutivo+"\",\""+tipo+"\")'>";
		for(i in campos){
			var campo = $("#w"+campos[i]);
			var valor = campo.val();
			if( valor == undefined ) valor = '';
			if( campo.is(':checkbox') ){
				( campo.is(':checked') ) ? valor = 'on' : valor = 'off';
			}
			html_code+="<td title='"+nombres[i]+"' class='"+campos[i]+""+clase_rojo+"'>"+valor+"</td>";
		}
		html_code+="</tr>";
		
		//Agregar en donde corresponda segun el orden en pantalla. Si el orden no estaba se agrega al final
		var orden_nuevo = $("#wdetorp").val();
		orden_nuevo = parseInt(orden_nuevo);
		var agrego = false;
		$(".detorp").each(function(){
			var orden_fila = $(this).text();
			orden_fila = $.trim(orden_fila);
			orden_fila = parseInt(orden_fila);
			if( orden_fila == orden_nuevo ){
				$(this).parent().before( html_code );
				agrego = true;			
			}
			if( agrego == true ){
				orden_fila++;
				$(this).text( orden_fila );
			}
		});
		if( agrego == false ){
			$("#lista_elementos tbody").append(html_code);
		}
		
		//Restaurar el formulario
		$("#td_archivo_validacion").html('');
		$("#td_campo_a_validar").html('');
		$("#formulario_formato_item").find(':input:not(:button)').val('').attr('checked',false)
		$("#formulario_formato_item").find(':input:not(:button)').attr('readonly',false);
		$("#formulario_formato_item").find(':input:not(:button)').attr('disabled',false);
		$('#formulario_formato_item').hide();
	}
	
	function editandoFila(){
		var campos = $("#campos_select").val();
		campos = eval('('+campos+')');
		var nombres = $("#nombres_campos").val();
		nombres = eval('('+nombres+')');
		var consecutivo = $("#wdetcon").val();
		var tipo = $("#wdettip").val();
		var clase_rojo = "";
		$("#wdetdes").val();

		if ( $("#wdetest").is(':checked') == false ){
			clase_rojo = " fondorojo";
		}
		
		var clase_fila = "";
		var cantidad_tr = $("#lista_elementos tbody").find('tr').length;
		( cantidad_tr % 2 == 1 ) ? clase_fila = 'fila2' : clase_fila = 'fila1';

		var html_code = "<tr id='filacon_"+consecutivo+"' class='"+clase_fila+"' ondblclick='mostrarConsecutivo(this, \""+consecutivo+"\",\""+tipo+"\")'>";
		for(i in campos){
			var campo = $("#w"+campos[i]);
			var valor = campo.val();
			if( valor == undefined ) valor = '';
			if( campo.is(':checkbox') ){
				( campo.is(':checked') ) ? valor = 'on' : valor = 'off';
			}
			html_code+="<td title='"+nombres[i]+"' class='"+campos[i]+""+clase_rojo+"'>"+valor+"</td>";
		}
		html_code+="</tr>";
		
		//PENDIENTE: Agregar la fila en el orden que corresponde segun el campo "orden en pantalla"
		var fila_antes = $("#filacon_"+consecutivo);
		fila_antes.after( html_code );
		fila_antes.remove();
		$('#formulario_formato_item').hide();
		//Restaurar el formulario
		/*$("#td_archivo_validacion").html('');
		$("#td_campo_a_validar").html('');
		$("#formulario_formato_item").find(':input:not(:button)').val('').attr('checked',false);
		$("#formulario_formato_item").find(':input:not(:button)').attr('readonly',false);
		$("#formulario_formato_item").find(':input:not(:button)').attr('disabled',false);*/
	}
	
	function isJson(value) {
		try {
			eval('(' + value + ')');
			return true;
		} catch (ex) {
			return false;
		}
	}
	
	function isEmpty(obj) {
		
		if (typeof obj == 'undefined' || obj === null || obj === '') return true;
		
		if (typeof obj == 'number' && isNaN(obj)) return true;
		
		if (obj instanceof Date && isNaN(Number(obj))) return true;
		
		return false;		
	}
	

	function enter()
	{
	   document.forms.DetalleProtocolos.submit();
	}
	
	function leyenda()
	{
	   document.write("alert");
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     } 
	 
	 function nuevaventana(path)
	{
		window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
	} 

	function Seleccionarcantidad(objeto)
	{
		$( "#wdetlrb" ).focus();
	}

</script>

</head>

<body BGCOLOR="#ffffff">
<!-- Programa en PHP -->
<?php
	
	}





include_once("root/magenta.php");
include_once("root/comun.php");

$wactualiz = "2016-05-25";

//=============================================================================================================================================\\
//M O D I F I C A C I O N E S 
//=============================================================================================================================================\\
// 2016-05-25: Arleyda Insignares C. 
// -Se Modifica la funcion que asigna el numero de orden para que tenga en cuenta los inactivos.
// -Se habilita un campo input text para el tipo de dato Memo y de manera predeterminada debe 
//  sacar el texto 'admisiones', sin embargo el usuario podra modificar este texto.
// -Cuando el usuario escoja un tipo de dato radio boton debe diligenciar la cantidad de manera
//  obligatoria.
// Abril 25 de 2016 Jessica Madrid Mejía
// Se modifica el query de la función validarSiHayDatos() para que sea mas rápida la consulta
// ----------------------------------------------------------------------------------------------------------------------------------------------
// Abril 18 de 2016 Jessica Madrid Mejía
// Se agrega el campo detves (Validación especifica) en textarea
// ----------------------------------------------------------------------------------------------------------------------------------------------
//Abril 04 de 2013  Frederick Aguirre
//---------------------------------------------------------------------------------------------------------------------------------------------\\
//Se modifica el programa para recibir solicitudes AJAX
//Se evita hacer submit cuando se presionen los botones o seleccionen algunos campos
//La información que se cargaba en el submit se carga con AJAX
//Se realizan validaciones para evitar errores al modificar y crear nuevos campos
//Se cambia el aspecto visual del programa

//Enero 24 de 2013	Juan C. Hdez.
//---------------------------------------------------------------------------------------------------------------------------------------------\\
//Se modifica el campo "detved" para que no sea booleano si no texto, con las siguientes opciones T:odos, P:ediatricos, N:eonatos y A:dultos   \\
//=============================================================================================================================================\\

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wdbmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wfecha=date("Y-m-d");
$whora = (string)date("H:i:s");
$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

//=================================================================================================================================
//***************************************** D E F I N I C I O N   D E   F U N C I O N E S *****************************************
//================================================================================================================================

if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == 'consultarCampoArchivoVal' ){
		$respuesta = campoAValidarDelArchivoDeValidacion ( $_REQUEST['tipo'], $_REQUEST['archivo_validacion'] );
		echo $respuesta;
		return;
	}else if( $action == 'guardarCambiosFormulario' ){
		guardarItemParaFormulario( $_REQUEST['datos'], $_REQUEST['editando'] );
		return;
	}else if(  $action == 'consultarUltimoConsecutivo' ){
		$ultimo_consecutivo = consultarUltimoConsecutivo($_REQUEST['codigo_formulario']);
		$ultimo_orden_pantalla = consultarUltimoOrdenPantalla($_REQUEST['codigo_formulario']);
		// var_dump($ultimo_consecutivo);
		// var_dump($ultimo_orden_pantalla);
		$dato = array();
		$dato['ultimo_consecutivo'] = $ultimo_consecutivo;
		$dato['ultimo_orden_pantalla'] = $ultimo_orden_pantalla;
		echo json_encode( $dato );
		return;
	}
}

	function consultarUltimoConsecutivo( $cod_formulario ){
		global $conex;
		global $wbasedato;
		
		//Busco que consecutivo sigue en el protocolo
		$q = " SELECT MAX(detcon) "
			."   FROM ".$wbasedato."_000002 "
			."  WHERE detpro = '".$cod_formulario."'";
        
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		$cod = -1;
		if ($num > 0){
			$row = mysql_fetch_array($res);
			$cod = $row[0]+1;
		}  

		return $cod;
	}
	
	function consultarUltimoOrdenPantalla( $cod_formulario ){
		global $conex;
		global $wbasedato;
		
		//Busco que consecutivo sigue en el protocolo
		$q = " SELECT MAX(detorp) "
			."   FROM ".$wbasedato."_000002 "
			."  WHERE detpro = '".$cod_formulario."'";
			//."    AND detest = 'on'"; 2016-05-20 Se Modifica para tener en cuenta en el consecutivo campos inactivos
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		$cod = -1;
		if ($num > 0){
			$row = mysql_fetch_array($res);
			$cod = $row[0]+1;
		}  

		return $cod;
	}

	//$editando contiene el numero de consecutivo, detcon, si el numero existe significa que estoy editando
	function guardarItemParaFormulario($wdatos, $editando){

		global $conex;
		global $wbasedato;

		$wdetcon = 0;

		$wfecha = date('Y-m-d');
		$whora = date('H:i:s');
		$wdetpro;
		$wdetfac = date('Y-m-d');

		$wdatos = str_replace("\\t", "&#92;t", $wdatos);
		$wdatos = str_replace("\\n", "&#92;n", $wdatos);
		$wdatos = str_replace("\\", "", $wdatos);
		$wdatos = str_replace("\"[", "[", $wdatos);
		$wdatos = str_replace("]\"", "]", $wdatos);
		
		$wdatos = json_decode( $wdatos, true );
		//$wdetpro = $wdatos[0]['formulario'];

		/*if(  $editando == 'false'  ){
			//Busco que consecutivo sigue en el protocolo
			$q = " SELECT MAX(detcon) "
				."   FROM ".$wbasedato."_000002 "
				."  WHERE detpro = '".$wdetpro."'";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
		   
			if ($num > 0){
				$row = mysql_fetch_array($res);
				$wdetcon = $row[0]+1;
			}
		}else{
			$wdetcon = $editando;
		}*/

		foreach( $wdatos as $dato ){
			//Se inicializan todas las variables del formulario
			$wdetorp= '';		$wdettip= '';    $wdetdes= '';    $wdetarc= '';   $wdetcav= '';    $wdetvde= '';    $wdetnpa= '';    $wdetvim= '';    $wdetume= '';    $wdetcol= '';    $wdethl7= '';   $wdetjco= '';    $wdetsiv= '';    $wdetase= '';    $wdetved= '';    $wdetimp= '';    $wdetimc= '';    $wdetvco= '';    $wdetvcr= '';    $wdetobl= '';    $wdetdep= '';    $wdetcde= '';    $wdeturl= '';    $wdetfor= '';    $wdetcco= '';    $wdetcac= '';    $wdetnse= '';    $wdetfac= '';    $wdetest= '';    $wdetcoa= '';    $wdetprs= '';    $wdetalm= '';    $wdetanm= '';    $wdetlrb= '';    $wdetdde= '';    $wdetcbu= '';    $wdetnbu= '';    $wdettta= '';    $wdetcua= '';    $wdetccu= '';    $wdetcro= '';    $wdettii= '';    $wdetdpl= '';    $wdetvmi= '';    $wdetvma= '';    $wdetcpp= '';    $wdetcop= '';    $wdetves= '';

			//Asigno el valor a las variables anteriores.
			foreach( $dato['valores'] as $valor ){
				if( $valor['valor'] === true )
					$valor['valor'] = 'on';
				else if( $valor['valor'] === false )
					$valor['valor'] = 'off';

				//$valor['valor'] = str_replace("'","\"", $valor['valor']);
				$valor['valor'] = str_replace("'","\\'", $valor['valor']);
				$valor['valor'] = str_replace("&quot;","\"", $valor['valor']);
				$valor['valor'] = str_replace("&#92;","\\", $valor['valor']);
				$valor['valor'] = str_replace("NO APLICA","", $valor['valor']);
				${$valor['clave']} = utf8_decode($valor['valor']);
			}
			
			$wmaximo_orden_pantalla = "";
			$quer = "  SELECT MAX(detorp) "
					."   FROM ".$wbasedato."_000002 "
					."  WHERE detpro = '".$wdetpro."'";
					//."    AND detest ='on'";	
			$resq = mysql_query($quer,$conex);
			$numq = mysql_num_rows($resq);

			if ($numq > 0){
				$rowq = mysql_fetch_array($resq);
				$wmaximo_orden_pantalla = $rowq[0];
			}					
					
			//Si se esta actualizando un campo
			if( $editando == 'true' ){

				$whay="off";

				/*$whay=validarSiHayDatos($wdetpro, $wdetcon); */   //Abril 26 de 2011

				/*if ($whay=="off"){*/
				//$wdetfor = htmlentities( $wdetfor );


					if( $wdetorp > $wmaximo_orden_pantalla && $wmaximo_orden_pantalla != ""){
						$msj['msj'] = 'El Orden en pantalla que intenta ingresar no es valido';
						echo json_encode( $msj );
						return;
					}
				
					$worden_anterior = "";
					//2013-09-04
					$quer = "  SELECT detorp "
							."   FROM ".$wbasedato."_000002 "
							."  WHERE detpro = '".$wdetpro."'"
							."    AND detcon =".$wdetcon."";	
					$resq = mysql_query($quer,$conex);
					$numq = mysql_num_rows($resq);

					if ($numq > 0){
						$rowq = mysql_fetch_array($resq);
						$worden_anterior = $rowq[0];
					}

					$q= " UPDATE ".$wbasedato."_000002 SET detorp='".$wdetorp."', dettip='".$wdettip."', detdes='".$wdetdes."', detarc='".$wdetarc."', detcav='".$wdetcav."', detvde='".$wdetvde."', detnpa='".$wdetnpa."', detvim='".$wdetvim."', detume='".$wdetume."', detcol='".$wdetcol."', dethl7='".$wdethl7."', detjco='".$wdetjco."', detsiv='".$wdetsiv."', detase='".$wdetase."', detved='".$wdetved."', detimp='".$wdetimp."', detimc='".$wdetimc."', detvco='".$wdetvco."', detvcr='".$wdetvcr."', detobl='".$wdetobl."', detdep='".$wdetdep."', detcde='".$wdetcde."', deturl='".$wdeturl."', detfor='".$wdetfor."', detcco='".$wdetcco."', detcac='".$wdetcac."' ,detnse='".$wdetnse."', detfac='".$wdetfac."', detest='".$wdetest."', detcoa='".$wdetcoa."', detprs='".$wdetprs."', detalm='".$wdetalm."', detanm='".$wdetanm."', detlrb='".$wdetlrb."', detdde='".$wdetdde."', detcbu='".$wdetcbu."', detnbu='".$wdetnbu."', dettta='".$wdettta."', detcua='".$wdetcua."', detccu='".$wdetccu."', detcro='".$wdetcro."', dettii='".$wdettii."', detdpl='".$wdetdpl."', detvmi='".$wdetvmi."', detvma='".$wdetvma."', detcpp='".$wdetcpp."', detcop='".$wdetcop."', detves='".$wdetves."'"
						 ."   WHERE detpro= '".$wdetpro."' AND detcon =".$wdetcon."";	

					//$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					if( $res = mysql_query($q,$conex) ){
						echo "OK";

						//SI "ORDEN EN PANTALLA" DEL CAMPO QUE ACABO DE ACTUALIZAR YA EXISTIA, TODOS LOS CAMPOS DE AHI EN ADELANTE SE MUEVEN UNA POSICION
						if( $wdetest == 'on' ){
							//Busco que el orden en pantalla no exista
							$q3 = " SELECT detorp "
								."   FROM ".$wbasedato."_000002 "
								."  WHERE detpro = '".$wdetpro."'"
								."    AND detorp = ".$wdetorp.""
								."    AND detest = 'on'"
								."    AND detcon !=".$wdetcon."";	
							$res3 = mysql_query($q3,$conex);
							$num3 = mysql_num_rows($res3);

							if ($num3 > 0){
								/*$mensaje_json = array();
								$mensaje_json['msj'] = "Ya existe un campo con el mismo orden de pantalla activo, No se actualizo el campo";
								echo json_encode($mensaje_json);
								return;*/
									if( $worden_anterior >  $wdetorp ){
									$q4 = " UPDATE ".$wbasedato."_000002 "
										."     SET detorp = ( detorp + 1 ) "
										."   WHERE detpro = '".$wdetpro."'"
										."     AND detorp >= ".$wdetorp
										."     AND detorp < ".$worden_anterior
										."     AND detcon !=".$wdetcon."";
									$res4 = mysql_query($q4,$conex);
								}else{
									$q4 = " UPDATE ".$wbasedato."_000002 "
										."     SET detorp = ( detorp - 1 ) "
										."   WHERE detpro = '".$wdetpro."'"
										."     AND detorp > ".$worden_anterior
										."     AND detorp <= ".$wdetorp
										."     AND detcon !=".$wdetcon."";
									$res4 = mysql_query($q4,$conex);
								}
							}
						}
					}else{
						echo "NO";
					}
				/*}else{
					$msj['msj'] = 'El tipo de dato no puede ser cambiado';
					echo json_encode( $msj );
				}*/
			}else{
				//Se esta creando un campo nuevo
				if( $wdetorp > ($wmaximo_orden_pantalla+1) && $wmaximo_orden_pantalla != ""){
					$msj['msj'] = 'El Orden en pantalla que intenta ingresar no es valido';
					echo json_encode( $msj );
					return;
				}
				$q= " INSERT ".$wbasedato."_000002 (   Medico       ,   fecha_data,   hora_data,    detpro    ,    detcon    ,    detorp    ,    dettip    ,    detdes    ,    detarc    ,    detcav    ,    detvde    ,    detnpa    ,   detvim     ,   detume     ,   detcol     ,dethl7,   detjco     ,    detsiv    ,    detase    ,    detved    ,    detimp    ,    detimc    ,    detvco    ,    detvcr    ,    detobl    ,    detdep    ,    detcde    ,    deturl    ,    detfor    ,    detcco    ,    detcac    ,    detnse    ,    detfac    ,    detest    ,    detcoa    ,    detprs    ,    detalm    ,    detanm    ,    detlrb    ,    detdde    ,    detcbu    ,    detnbu    ,    dettta    ,    detcua    ,    detccu    ,    detcro    ,    dettii    ,    detdpl    ,    detvmi    ,    detvma    ,    detcpp    , 	detcop    , 		detves    , Seguridad         ) "
					 ."       VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wdetpro."','".$wdetcon."','".$wdetorp."','".$wdettip."','".$wdetdes."','".$wdetarc."','".$wdetcav."','".$wdetvde."','".$wdetnpa."','".$wdetvim."','".$wdetume."','".$wdetcol."','".$wdethl7."','".$wdetjco."','".$wdetsiv."','".$wdetase."','".$wdetved."','".$wdetimp."','".$wdetimc."','".$wdetvco."','".$wdetvcr."','".$wdetobl."','".$wdetdep."','".$wdetcde."','".$wdeturl."','".$wdetfor."','".$wdetcco."','".$wdetcac."','".$wdetnse."','".$wdetfac."','".$wdetest."','".$wdetcoa."','".$wdetprs."','".$wdetalm."','".$wdetanm."','".$wdetlrb."','".$wdetdde."','".$wdetcbu."','".$wdetnbu."','".$wdettta."','".$wdetcua."','".$wdetccu."','".$wdetcro."','".$wdettii."','".$wdetdpl."','".$wdetvmi."','".$wdetvma."','".$wdetcpp."','".$wdetcop."','".$wdetves."', 'C-".$wbasedato."') ";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				if( mysql_insert_id() ){
					echo "OK";
					//SI "ORDEN EN PANTALLA" DEL CAMPO QUE ACABO DE INSERTAR YA EXISTIA, TODOS LOS CAMPOS DE AHI EN ADELANTE SE MUEVEN UNA POSICION
					if( $wdetest == 'on' ){
						//Busco que el orden en pantalla no exista
						$q3 = " SELECT detorp "
							."   FROM ".$wbasedato."_000002 "
							."  WHERE detpro = '".$wdetpro."'"
							."    AND detorp = ".$wdetorp.""
							."    AND detest = 'on'"
							."    AND detcon !=".$wdetcon."";	
						$res3 = mysql_query($q3,$conex);
						$num3 = mysql_num_rows($res3);
					   
						if ($num3 > 0){
							/*$mensaje_json = array();
							$mensaje_json['msj'] = "Ya existe un campo con el mismo orden de pantalla activo, No se actualizo el campo";
							echo json_encode($mensaje_json);
							return;*/
							$q4 = " UPDATE ".$wbasedato."_000002 "
								."     SET detorp = ( detorp + 1 ) "
								."   WHERE detpro = '".$wdetpro."'"
								."     AND detorp >= ".$wdetorp.""
								."     AND detcon !=".$wdetcon."";	
							$res4 = mysql_query($q4,$conex);
						}
					}
				}
				else{
					echo "NO";
				}
			}
		}	
	}
		
function traer_campos_obligatorios($wdettipo){
		global $conex;
		global $wbasedato;
		global $wvariables;
		global $wcar_obl;
		global $wvalidacion;
		global $wobligatorio;
		global $wnom_variables;
		global $wcampos_select;

	   
	   //con esto defino global todas las variables que estan definidas en la tabla 000010
		for ($i=0;$i<count($wvariables);$i++){
		   global ${$wvariables[$i]};
		}
	   
		$q = " SELECT tipdat, tippan, tipobl, tipvar "
			."   FROM ".$wbasedato."_000010 "
			."  WHERE tipdat = '".$wdettipo."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);
	   
		if ($num > 0 ){
			$row= mysql_fetch_array($res);
		  
			$wvalores=explode("-",$row[1]);     //Aca se separa el campo de cada uno de los estados de los campos en pantalla (Enabled, Disabled, Readonly)
		  
			for ($i=0;$i<count($wvalores);$i++){
				switch ($wvalores[$i]){
					case "E":   
						$wvalidacion[$i]="Enabled";  
						break;
					case "D":
					    $wvalidacion[$i]="Disabled";
						break;
					case "R":
						$wvalidacion[$i]="Readonly";
						break; 
				}
			}
			
			$wvalores=explode("-",$row[2]);	  //Aca se separa el indicador de obligatoriedad para cada uno de los campos en pantalla
			for ($i=0;$i<count($wvalores);$i++){
				$wobligatorio[$i]=$wvalores[$i];
				//echo "<input type='HIDDEN' id='wobligatorio[".$i."]' value='".$wobligatorio[$i]."'>";

				if ($wobligatorio[$i]=="on")
					$wcar_obl[$i]="<font color=0000FF>(*)</font>";          //Este caracter lo debe mostrar en la pantalla en cada campo obligatorio
				else
					$wcar_obl[$i]=""; 
				//echo "<input type='HIDDEN' id='wcar_obl[".$i."]' value='".$wcar_obl[$i]."'>";     
			} 
			
			$wvalores=explode("-",$row[3]);	  //Aca se separan los nombres de las variables que tiene cada campo de la pantalla en este programa.
			for ($i=0;$i<count($wvalores);$i++){
				$wvariables[$i]=$wvalores[$i];
				//echo "<input type='HIDDEN' id='wvariables[".$i."]' value='".$wvariables[$i]."'>";
			}
			
			$resultado = array();
			$i=0;
			foreach($wvariables as $variable){
				if ( array_key_exists( $variable, $resultado) == false )
					$resultado[ $variable ] = array();
					
				$item = array();
				$item['atributo'] =$wvalidacion[ $i ];
				$item['obligatorio'] = $wobligatorio[ $i ];
				
				if( $variable == 'wdetarc' ){
					$item['archivo_validacion'] =   consultarArchivoValidacion( $wdettipo ) ;
				}
				if( $variable == 'wdetcav' ){
					$item['campo_validar'] = campoAValidarDelArchivoDeValidacion( $wdettipo );
				}
				
				$resultado[ $variable ] = $item;
				$i++;			
			}
			
			echo json_encode ( $resultado );
			//evaluar_campos_boleanos();
		}
	}
	
	function consultarArchivoValidacion($wdettipo){
			
		global $conex;
		global $wbasedato;
		
			$textoRetornar = '';
		
			switch ($wdettipo){
				case "Seleccion":{
					$wdetarc="000012";  //On Ojo si esto cambia por $wbasedato_000012 ==>HCE_000012     
					$textoRetornar.= "<input type='text' id='wdetarc' value='".$wdetarc."' >";   
				}   
				break;
				case "Memo":{
					$wdetarc="";
					$textoRetornar.= "<input type='text' id='wdetarc' value='".$wdetarc."' >";
				}   
				break;
				case "PassWord":
				case "Referencia":{
					$q = " SELECT encpro, encdes "
					."  FROM ".$wbasedato."_000001 "
					."  WHERE encest = 'on' "
					."  ORDER BY 1 ";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res);   
					
					$textoRetornar.= "<SELECT id='wdetarc' onchange='consultarCampoArchivoValidacion()'>";                      //$wdetarc
					$textoRetornar.= "<option value=''>&nbsp;</option>";  
					for ($j=1;$j<=$num;$j++){
						$row = mysql_fetch_array($res);   
						$textoRetornar.= "<option value='".$row[0]."'>".$row[0]."_".utf8_encode($row[1])."</option>";
					}
					$textoRetornar.= "</SELECT>";  
				}   
				break; 
				case "Tabla":{
					$q = " SELECT medico, codigo, nombre "
					."   FROM formulario "
					."  WHERE activo = 'A' "
					."  ORDER BY 1,2 ";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res);
					$textoRetornar.= "<SELECT id='wdetarc' onchange='consultarCampoArchivoValidacion()' >";                      //$wdetarc
					$textoRetornar.= "<option value=''>&nbsp;</option>";   
					
					for ($j=1;$j<=$num;$j++){ 
						$row = mysql_fetch_array($res);   
						$textoRetornar.= utf8_encode("<option value='".$row[0]."_".$row[1]."'>".$row[0]."_".$row[1]."_".$row[2]."</option>");
					}
					$textoRetornar.= "</SELECT>";
				}
				break;
				default:{
					unset($wdetarc);
				}
				break; 
			}
			
			return $textoRetornar;
	}

	function campoAValidarDelArchivoDeValidacion( $wdettipo, $wdetarc='' ){
	
		global $conex;
		global $wbasedato;
		
		//CAMPO A VALIDAR DEL ARCHIVO DE VALIDACION
		$textoRetornar = '';
			switch ($wdettipo){
				case "Seleccion":
				{
					$q = " SELECT msetab, msedes "
					."   FROM ".$wbasedato."_000014 "
					."  WHERE mseest = 'on' "
					."  ORDER BY 1 ";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res);
					
					$textoRetornar.= "<SELECT id='wdetcav'  >";                                     //$wdetcav
					$textoRetornar.= "<option value=''>&nbsp;</option>";  
					for ($j=1;$j<=$num;$j++){ 
						$row = mysql_fetch_array($res);   
						$textoRetornar.= "<OPTION value='".trim($row[0])."'>".$row[0]."_".utf8_encode($row[1])."</option>";
					}
					$textoRetornar.= "</SELECT>";
				}
				break;
				case "Memo":
				case "PassWord":
				case "Referencia":
				{
					if (isset($wdetarc) and trim($wdetarc)!="")
					{
						$wdetarc1 = explode("_",$wdetarc);
						$q = " SELECT detcon, detdes "
						."   FROM ".$wbasedato."_000002 "
						."  WHERE detpro = '".$wdetarc1[0]."'"
						."    AND detest = 'on' "
						."  ORDER BY 1 ";
						$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);
						
						$textoRetornar.= "<SELECT id='wdetcav' >";                                     //$wdetcav
						$textoRetornar.= "<option value=''>&nbsp;</option>";  
						for ($j=1;$j<=$num;$j++)
						{ 
							$row = mysql_fetch_array($res);   
							$textoRetornar.= "<OPTION value='".trim($row[0])."'>".trim($row[0])."_".($row[1])."</option>";
						}
						$textoRetornar.= "</SELECT>";
					}
					else{
						
					}  
				}
				break;   
				case "Tabla":
				{
					if (isset($wdetarc) && $wdetarc != '')
					{
						$wdetarc1 = explode("_",$wdetarc);  
						
						$q = " SELECT campo, descripcion "
						."   FROM det_formulario "
						."  WHERE medico = '".trim($wdetarc1[0])."'"
						."    AND codigo = '".trim($wdetarc1[1])."'"
						."    AND activo = 'A' "
						."  ORDER BY 1 ";
						$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);

						$textoRetornar.= "<SELECT id='wdetcav' >";                                     //$wdetcav
						$textoRetornar.= "<option value=''>&nbsp;</option>";  
						for ($j=1;$j<=$num;$j++)
						{ 
							$row = mysql_fetch_array($res);   
							$textoRetornar.= "<OPTION value='".$row[0]."'>".$row[0]."_".($row[1])."</option>";
						}
						$textoRetornar.= "</SELECT>";
					}
					else
					{
					}  
				}
				break;	
				default:
				{
				}
				break;      
			}	
			
			return $textoRetornar;
	}


function validarSiHayDatos($wdetpro, $wdetcon)
  {
   global $conex;	  
   global $wbasedato;
   
   // //Busco si el tipo de campo con el consecutivo y formulario ya tiene datos
   // $q = " SELECT COUNT(*) "
       // ."   FROM ".$wbasedato."_".$wdetpro
	   // ."  WHERE movpro = '".$wdetpro."'"
	   // ."    AND movcon = ".$wdetcon;
	
	// $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
    // $row= mysql_fetch_array($res);	
	
	// if ($row[0] > 0)
	  // return "on";
    // else
       // return "off";
	
	//Busco si el tipo de campo con el consecutivo y formulario ya tiene datos
    $q = " SELECT * "
       ."   FROM ".$wbasedato."_".$wdetpro
	   ."  WHERE movpro = '".$wdetpro."'"
	   ."    AND movcon = ".$wdetcon
	   ."    LIMIT 1";
	   
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   $num = mysql_num_rows($res);
   
   if ($num > 0)
	  return "on";
     else
       return "off";	 
  }
  
function validar_campos()
  {
   global $conex;	  
   global $wbasedato;
   global $wvariables;
   global $wcar_obl;
   global $wvalidacion;
   global $wobligatorio;
   global $wnom_variables;
   
   global $wok;
   
   //On
   //var_dump($wnom_variables);
   
   
   //con esto defino global todas las variables que estan definidas en la tabla 000010
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global ${$wvariables[$i]};
      } 
   
   $wnr=count($wobligatorio);
   
   if (isset($wdettip) and $wdettip!="")
      {
       $wok=true;
	   
       for ($i=0;$i<$wnr;$i++)
	      {
		   if ($wobligatorio[$i]=="on")
		      {
			   if (!isset(${$wvariables[$i]}) or trim(${$wvariables[$i]})=="") 
			      {
				   $wok=false;
				   
				   echo '<script language="javascript">';
				     echo 'alert ("El campo : '.$wvariables[$i].' esta vacio")';
				   echo '</script>';
				  }
			  }
		   /*echo "<input type='HIDDEN' name='wvalidacion[".$i."]'    value='".$wvalidacion[$i]."'>";
		   echo "<input type='HIDDEN' name='wobligatorio[".$i."]'   value='".$wobligatorio[$i]."'>";
		   echo "<input type='HIDDEN' name='wvariables[".$i."]'     value='".$wvariables[$i]."'>";
		   echo "<input type='HIDDEN' name='wcar_obl[".$i."]'       value='".$wcar_obl[$i]."'>";  
		   echo "<input type='HIDDEN' name='wnom_variables[".$i."]' value='".$wnom_variables[$i]."'>";  */
		  }
	  }
     else
        {
	     echo '<script language="javascript">';
	         echo 'alert ("Debe seleccionar un tipo de dato, antes de Grabar")';
	     echo '</script>';
	    }  
  }

  
function evaluar_campos_boleanos()
  {
   global $wvariables;
   global $wbasedato;
   global $conex;
	  	  	  
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global ${$wvariables[$i]};
	         
	   //Averiguo por cada campo si es BOOLEANO y si si, establezco checked o no.   
	   $q = " SELECT COUNT(*) "
	       ."   FROM det_formulario "
	       ."  WHERE medico      = '".$wbasedato."'"
	       ."    AND codigo      = '000002'"
	       ."    AND descripcion = '".substr($wvariables[$i],1,strlen($wvariables[$i]))."'"
	       ."    AND tipo        = '10' "
	       ."    AND activo      = 'A' ";
	   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	   $row= mysql_fetch_array($res);
	   
	   if ($row[0] > 0)
	     {
		  if (isset(${$wvariables[$i]}) and trim(${$wvariables[$i]})=="on")
		     {
			  ${$wvariables[$i]}="checked";      //Con esto se chulea el campo en pantalla
		     }  
		    else
		        ${$wvariables[$i]}="uncheked"; 
		 }    
	  }    
  }	  
    
//=================================================================================================================================
function grabar()
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   global $wvariables;
   
   global $wok;
   
   //con esto defino global todas las variables que estan definidas en la tabla 000010
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global ${$wvariables[$i]};
      }
   
   global $wdetpro;
   
   
   if ($wok==true)
     {
	   for ($i=0;$i<count($wvariables);$i++)
      	  {   
		   //Averiguo por cada campo si es BOOLEANO y si si, establezco checked o no, grabo un 'on' o un 'off'.   
		   $q = " SELECT COUNT(*) "
		       ."   FROM det_formulario "
		       ."  WHERE medico      = '".$wbasedato."'"
		       ."    AND codigo      = '000002'"
		       ."    AND descripcion = '".substr($wvariables[$i],1,strlen($wvariables[$i]))."'"
		       ."    AND tipo        = '10' "
		       ."    AND activo      = 'A' ";
		   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   $row= mysql_fetch_array($res);
		   
		   if ($row[0] > 0)
		     {
			  if (${$wvariables[$i]}==true)
			     {
			      ${$wvariables[$i]}="on";      //Con esto se chulea el campo en pantalla
			     }  
			    else
			        ${$wvariables[$i]}="off"; 
			 }
          }		        
	    
       //Busco que consecutivo sigue en el protocolo
	   $q = " SELECT MAX(detcon) "
	       ."   FROM ".$wbasedato."_000002 "
	       ."  WHERE detpro = '".$wdetpro."'";
	   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   if ($num > 0)
	      {
		   $row = mysql_fetch_array($res);
		   
		   if ($wdetcon > ($row[0]+1))
		      $wdetcon = $row[0]+1;
		  }    
		  
		switch (trim($wdettip))
		  {
			case "Seleccion":
			   $wdetarc = "000012";
		       $wdetcav1=explode("_",$wdetcav);
		       $wdetcav=$wdetcav1[0]; 
		       
		       break;
		       
		    case "Referencia":
		       $wdetarc1 = explode("_",$wdetarc);
			   $wdetarc  = $wdetarc1[0];
			   
			   $wdetcav1 = explode("_",$wdetcav);
		       $wdetcav  = $wdetcav1[0];
		       
		       break;
		     
		    default:
			   if (trim($wdetarc) != "" and trim($wdetarc) != "_")
			      {
				   $wdetarc1 = explode("_",$wdetarc);
				   $wdetarc  = $wdetarc1[0]."_".$wdetarc1[1];
			      }
				 else
					$wdetarc = "";
					
			   if ($wdetcav != "" and trim($wdetcav) != "_")
			      {	  
				   $wdetcav1 = explode("_",$wdetcav);
				   $wdetcav  = $wdetcav1[0];
				  }
				 else
					$wdetcav = ""; 
		  }		  
		  

	  //Inserto el registro en la tabla de Configuracion del protocolo   
	  $q= " INSERT INTO ".$wbasedato."_000002 (   Medico       ,   fecha_data,   hora_data,    detpro    ,    detcon    ,    detorp    ,    dettip    ,    detdes    ,    detarc    ,    detcav    ,    detvde    ,    detnpa    ,   detvim     ,   detume     , detcol  ,  dethl7 ,   detjco     ,    detsiv    ,    detase    ,    detved    ,    detimp    ,    detimc    ,    detvco    ,    detvcr    ,    detobl    ,    detdep    , detcde    ,    deturl    ,    detfor    ,    detcco    ,    detcac    ,    detnse    ,    detfac    ,    detest    ,    detcoa    ,    detprs    ,    detalm    ,    detanm    ,    detlrb    ,    detdde    ,    detcbu    ,    detnbu    ,    dettta    ,    detcua    ,    detccu    ,    detcro    ,    dettii    ,    detdpl    ,    detvmi    ,    detvma    ,    detcpp,    detcop    ,    detves    , Seguridad         ) "
		 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wdetpro."','".$wdetcon."','".$wdetorp."','".$wdettip."','".$wdetdes."','".$wdetarc."','".$wdetcav."','".$wdetvde."','".$wdetnpa."','".$wdetvim."','".$wdetume."','".$wdetcol."','".$wdethl7."','".$wdetjco."','".$wdetsiv."','".$wdetase."','".$wdetved."','".$wdetimp."','".$wdetimc."','".$wdetvco."','".$wdetvcr."','".$wdetobl."','".$wdetdep."','".$wdetcde."','".$wdeturl."','".$wdetfor."','".$wdetcco."','".$wdetcac."','".$wdetnse."','".$wdetfac."','".$wdetest."','".$wdetcoa."','".$wdetprs."','".$wdetalm."','".$wdetanm."','".$wdetlrb."','".$wdetdde."','".$wdetcbu."','".$wdetnbu."','".$wdettta."','".$wdetcua."','".$wdetccu."','".$wdetcro."','".$wdettii."','".$wdetdpl."','".$wdetvmi."','".$wdetvma."','".$wdetcpp."','".$wdetcop."','".$wdetves."', 'C-".$wbasedato."') ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	 }    
  }	  
//=================================================================================================================================
function insert_orden_pantalla()
  {
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wok;
   global $wcodpro;
   global $wdetpro;
   global $wdetorp;
   global $wnompro;
   global $wdetcon;
   global $wtipuso;
   global $wtipfor;
   global $wtipimp;
   global $walert;
   global $westado;
   
   global $worpaux;	   //Aca esta el orden en pantalla que tenia cuando se consulto el registro  
	  
   //Si entra por aca es porque se esta es grabando, entonces averiguo si el orden a poner ya existe o es menor a alguno que 
   //ya exista, para hacer la reorganizacion
   $q = " SELECT COUNT(*) "
       ."   FROM ".$wbasedato."_000002 "
       ."  WHERE detpro = '".$wdetpro."'"
       ."    AND detorp = ".$wdetorp
       ."    AND detest = 'on' ";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);
   $row= mysql_fetch_array($res);
   
   if ($row[0]>0)   //Si existe el orden, aumento la secuencia
      {
	   $q = " UPDATE ".$wbasedato."_000002 "
	       ."    SET detorp = detorp + 1 "
	       ."  WHERE detpro = '".$wdetpro."'"
	       ."    AND detest = 'on' "
	       ."    AND detorp >=  ".$wdetorp;
	   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());      
	  }
  }	  


function cambiar_orden_pantalla()
  {
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wok;
   global $wcodpro;
   global $wdetpro;
   global $wdetorp;
   global $wdetcon;
   global $wtipuso;
   global $wtipfor;
   global $wtipimp;
   global $walert;
   global $westado;
   
   global $worpaux;	   //Aca esta el orden en pantalla que tenia cuando se consulto el registro
	  
   
   if ($wdetorp != $worpaux)
     {
	  if ($wdetorp < $worpaux)
        {
	      //Busco si el Orden en Pantalla NUEVO existe en la tabla, porque si no, no hay que incrementar los otros campos  
	      $q = " SELECT COUNT(*) "
	          ."   FROM ".$wbasedato."_000002 "
	          ."  WHERE detpro = '".$wdetpro."'"
	          ."    AND detorp = ".$wdetorp       //Orden nuevo
	          ."    AND detest = 'on' ";
	      $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());     
	      $row = mysql_fetch_array($res);    
	   
	      if ($row[0] > 0)  //Indica que si existe el orden debe incrementarse
	         {
		      $q = " DELETE FROM ".$wbasedato."_000002 "
		          ."  WHERE detpro = '".$wdetpro."'"
		          ."    AND detorp = ".$worpaux;
		      $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		         
		        
			  $q = " UPDATE ".$wbasedato."_000002 "
			      ."    SET detorp = detorp + 1 "
			      ."  WHERE detpro = '".$wdetpro."'"
			      ."    AND detest = 'on' "
			      ."    AND detorp < ".$worpaux
			      ."    AND detorp >=  ".$wdetorp;
			  $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 } 
        }
       else
          {
	       if ($wdetorp > $worpaux and trim($worpaux) != "")
	          {  
		       $q = " UPDATE ".$wbasedato."_000002 "
			       ."    SET detorp  = detorp - 1 "
			       ."  WHERE detpro  = '".$wdetpro."'"
			       ."    AND detest  = 'on' "
			       ."    AND detorp > ".$worpaux
			       ."    AND detorp <=  ".$wdetorp;
			   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  }
	         else
	           insert_orden_pantalla();   //por aca entra solo si es nuevo
	      }         
     }
  } 

function modificar()
  {
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   global $wvariables;
   
   
   //con esto defino global todas las variables que estan definidas en la tabla 000010
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global ${$wvariables[$i]};
      }
   
   global $wok;
   	  
   if ($wok==true)
     {
	  cambiar_orden_pantalla();   
	     
	  //Primero se borra y luego se graba.
	  $q = " DELETE FROM ".$wbasedato."_000002 "
	      ."  WHERE detpro = '".$wdetpro."'"
	      ."    AND detcon = ".$wdetcon;
	  $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  
	  grabar();
	 }     
  } 
  
  
function evaluar_actividad_campos($wdettip)
  {
   global $conex;	  
   global $wbasedato;
   global $wvariables;
   global $wcar_obl;
   global $wvalidacion;
   global $wobligatorio;
   
   //con esto defino global todas las variables que estan definidas en la tabla 000010
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global ${$wvariables[$i]};
      }
   
   $q = " SELECT tipdat, tippan, tipobl, tipvar "
       ."   FROM ".$wbasedato."_000010 "
       ."  WHERE tipdat = '".$wdettip."'";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   $num = mysql_num_rows($res);
   
   
   if ($num > 0 )
     {
	  $row= mysql_fetch_array($res);
	  
	  $wvalores=explode("-",$row[1]);     //Aca se separa el campo de cada uno de los estados de los campos en pantalla (Enabled, Disabled, Readonly)
	  
	  for ($i=0;$i<count($wvalores);$i++)
	    {
		 switch ($wvalores[$i])
		  {
		   case "E":   
		     $wvalidacion[$i]="Enabled";  
		     break;
		   case "D":
		     $wvalidacion[$i]="Disabled";
		     break;
		   case "R":
		     $wvalidacion[$i]="Readonly";
		     break; 
		     
		  // echo "<input type='HIDDEN' name='wvalidacion[".$i."]' value='".$wvalidacion[$i]."'>";   
		  }
		}
		
	  $wvalores=explode("-",$row[2]);	  //Aca se separa el indicador de obligatoriedad para cada uno de los campos en pantalla
	  for ($i=0;$i<count($wvalores);$i++)
	    {
     	 $wobligatorio[$i]=$wvalores[$i];
     	// echo "<input type='HIDDEN' name='wobligatorio[".$i."]' value='".$wobligatorio[$i]."'>";
     	 
     	 if ($wobligatorio[$i]=="on")
     	    $wcar_obl[$i]="<div class='obligatorio'><font color=0000FF>(*)</font></div>";          //Este caracter lo debe mostrar en la pantalla en cada campo obligatorio
     	   else
     	      $wcar_obl[$i]=""; 
     	 //echo "<input type='HIDDEN' name='wcar_obl[".$i."]' value='".$wcar_obl[$i]."'>";     
     	} 
	  $wvalores=explode("-",$row[3]);	  //Aca se separan los nombres de las variables que tiene cada campo de la pantalla en este programa.
	  for ($i=0;$i<count($wvalores);$i++)
	    {
		 $wvariables[$i]=$wvalores[$i];
     	// echo "<input type='HIDDEN' name='wvariables[".$i."]' value='".$wvariables[$i]."'>";
     	}
      evaluar_campos_boleanos();
     }
  }	  
  
function consultar()
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wvariables;
   global $wobligatorio;
   global $wvalidacion;
   global $wcar_obl;
   
   global $wcampos_select;
   
   global $wdetpro;
   global $wbotgra;  
   global $wbotmod;
   
   global $consecutivo;
   global $wcampos;
   global $dettip;
   
   $wdettip = $dettip;
   $wdetcon = $consecutivo;
   
   //global $wtip;     //Tipo de dato que se consulto
   global $worpaux;  //Orden en pantalla que se consulto
   
      
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global ${$wvariables[$i]};
	  }
      
   if (isset($wdetpro))
      {
	   //Traigo todos los campos de la tabla 000002 que estan en el arreglo "$wcampos_select"
	   $q = " SELECT ".$wcampos_select
	       ."   FROM ".$wbasedato."_000002 "
	       ."  WHERE detpro = '".$wdetpro."'"
	       ."    AND detcon = '".$consecutivo."'"
	       ."  ORDER BY 1 ";
		 
	   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
       $num = mysql_num_rows($res);    

       if ($num > 0)
         {
	      $row = mysql_fetch_array($res);
		  
		  array_map('utf8_encode', $row);
		  
	      $wcol= mysql_num_fields($res);    //Total de campos de trae la tupla
	      
	      $wtip=$row[3];                    //Tipo de dato que se consulto
	      $q = " SELECT tipvar "
	          ."   FROM ".$wbasedato."_000010 "
	          ."  WHERE tipdat = '".$wtip."'";
	      $resvar = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $numvar = mysql_num_rows($resvar);
          if ($numvar > 0)
             {
	          $rowvar = mysql_fetch_array($resvar);   
	          $wcampos=explode("-",$rowvar[0]);    //Variables
	          
	          for ($j=0;$j<$wcol;$j++)
	   	         {
		   	      ${$wcampos[$j]}=$row[$j];  
		   	     // echo "<INPUT TYPE='hidden' NAME='".$$wcampos[$j]."' VALUE = '".$row[$j]."'>";
		   	     }
	         }    
   	      $worpaux=$row['detorp'];  //Aca llevo el consecutivo con el se realizo la consulta, por si se modifica saber si se inserta o se borra.  
	   	  //echo "<INPUT TYPE='hidden' NAME=worpaux VALUE = '".$worpaux."'>";
		 // echo "<INPUT TYPE='hidden' NAME=wtip VALUE = '".$wtip."'>";
	   	  
	   	  $wbotmod="ENABLED";  
   		  $wbotgra="DISABLED";
   	     } 
   	    else
   	       {
	   	    iniciar();   
	   	    $wbotmod="DISABLED";  
   		    $wbotgra="ENABLED";   
   	       }      
   	   //echo "<input type='HIDDEN' name=wformulario  value='".$wdetpro."'>"; 
   	  }
   	 
   if (isset($wdettip)) 
      {
       evaluar_actividad_campos($wdettip);
      } 
  } 
  
   
function iniciar()
  {
   global $conex;
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wformulario;
   global $wvariables;
   global $wnom_variables;
   global $wobligatorio;
   global $wvalidacion;
   global $wcar_obl;
   
   global $wbotmod;
   global $wbotgra;
   
   
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global ${$wvariables[$i]};
	   
	   if ($i == 0)   //Para el campo "detpro"
	      {
		   ${$wvariables[$i]}=$wformulario; 
		  }
	     else
	        {
	         ${$wvariables[$i]}="";  
	        }
	  }
	  
   //Busco que consecutivo sigue en el protocolo
   $q = " SELECT MAX(detcon), MAX(detorp) "
       ."   FROM ".$wbasedato."_000002 "
       ."  WHERE detpro = '".$wdetpro."'";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);
   
   if ($num > 0)
      {
	   $row = mysql_fetch_array($res);
	   
	   for ($i=0;$i<count($wvariables);$i++)
	      {
		   if ($wvariables[$i] == "wdetcon")
		      {
			   ${$wvariables[$i]}=$row[0]+1;
			  } 
			  if ($wvariables[$i] == "wdetorp")
		      {
			   ${$wvariables[$i]}=$row[1]+1;
			  }
		  } 
	  }
   $wbotmod="DISABLED";  
   $wbotgra="ENABLED"; 
  }
  

function mostrar_grilla(){
	global $conex;
	global $wbasedato;
	global $wusuario;
	global $wfecha;
	global $whora;   
	global $wvariables;
	global $wnom_variables;   
	global $wcampos_select;
	global $num_campos;   
	global $wcancol;
	global $wcanfil;
	global $wdetpro;
	global $wformulario;
	global $wdbmovhos;
	global $wemp_pmla;
	
	if( $wdetpro == "")
		return;
   
	for ($i=0;$i<count($wvariables);$i++){
		global ${$wvariables[$i]};
	}
	
	
	$indices_para_mostrar = array(1,2,3,5,29);	
	$wcampos_select2 = "";
	$array_campos_select = explode( ",",$wcampos_select );
	foreach( $indices_para_mostrar as $indice ){
		$wcampos_select2.=$array_campos_select[ $indice ].",";
	}
	$wcampos_select2 = substr($wcampos_select2, 0, -1);
	
	$caracteres = array( "á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","\\","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??","?£", "°");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ", "", "N", "N", "U", "");
	$wnom_variables_aux = str_replace( $caracteres, $caracteres2, $wnom_variables );

	echo "<input type='hidden' id='campos_select' value='".json_encode($array_campos_select)."' />";
	echo "<input type='hidden' id='nombres_campos' value='".json_encode($wnom_variables_aux)."' />";
	   
    //Traigo todos los campos del formulario wdetpro
	$q = " SELECT ".$wcampos_select
		."   FROM ".$wbasedato."_000002 "
		."  WHERE detpro = '".$wdetpro."'"
		."  ORDER BY detorp ";
	
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
   
	$num_campos = $num;
   
   //<a target='_top' href='../../hce/procesos/HCE.php?accion=W1&ok=0&empresa=".$wbasedato."&wcedula=1&wtipodoc=CC&wformulario=".$wformulario."&whis=1&wing=1&wsex=A&width=900'>Previsualizar formulario</a>
	echo "<div>	
	<a onclick='nuevaventana(\"../../hce/procesos/HCE.php?accion=W1&origen=".$wemp_pmla."&wdbmhos=".$wdbmovhos."&ok=0&empresa=".$wbasedato."&wcedula=1&wtipodoc=CC&wformulario=".$wformulario."&whis=1&wing=1&wsex=A&width=900\")' >Previsualizar formulario</a>
	</div>";
	echo "<br>";
	echo "<div style='float:left;cursor: pointer;'>";
	echo "<span><font size=4>Lista de elementos</font></span>";
	echo "<br>";
	//echo "<center>";
	echo "<div style='align:left;'><input type='button' value='Nuevo' onclick='nuevoCampo(\"".$wdetpro." \")' /></div>";
		
	//echo "</center>";
	echo "<table id='lista_elementos'>";

	echo "<tr class=encabezadoTabla>";

	//Aca imprimo los titulos o nombres de las columnas.
	// for ($i=0;$i<count($wvariables);$i++){
		// echo "<th>".$wnom_variables[$i]."</th>";
	// }
	/*foreach( $indices_para_mostrar as $indice ){
		echo "<th>".$wnom_variables[$indice]."</th>";
	}*/
	$jj=0;
	foreach( $array_campos_select as $indice ){
		echo "<th>".$wnom_variables[$jj]."</th>";
		$jj++;
	}
	echo "</tr>";
	$wcancol = count($wnom_variables);   //Cantidad de campos que tiene la tabla 000002 para la configuracion de c/formulario
	$wcanfil = $num;                     //Cantidad de campos (metadata) que tiene configurado el protocolo o formulario

	//Inicializo las variables en Javascript
	echo "<script language='Javascript'>";
	echo "wcancol=".$wcancol.";";
	echo "wcanfil=".$wcanfil.";";
	echo "</script>"; 
	if ($num > 0){
		
		for ($i=1;$i<=$num;$i++){
			if (is_integer($i/2))
				$wclass="fila1";
			else
				$wclass="fila2";

			$row  = mysql_fetch_array($res);       
			$wcol = mysql_num_fields($res);
			$wclassFila="";

			echo "<tr ondblclick='mostrarConsecutivo(this, \"".$row['detcon']."\",\"".$row['dettip']."\")' class='".$wclass."' id='filacon_".$row['detcon']."'>";    
			for ($j=0;$j<($wcol);$j++){
				//onclick='javascript:cerrarModal();
				//'javascript:evaluarEnvio(\"".$fila."\"".","."\"".$wpatron."\");'
				//echo "<td onmouseover='javascript:cargarTooltip(\"".$wnom_variables[$j]."-".$i."\");' title='".$wnom_variables[$j]."' id='".$wnom_variables[$j]."-".$i."'>".$row[$j]."</td>";
				//echo "<td onmouseover='javascript:cargarTooltip(\"".$j."-".$i."\");' title='".$wnom_variables[$j]."' id='".$j."-".$i."'>".$row[$j]."</td>";
				
				//echo "<td ondblclick='mostrarConsecutivo(\"".$row['detcon']."\",\"".$row['dettip']."\")' title='".$wnom_variables[  $indices_para_mostrar[$j]  ]."' id='".$i."-".$j."'>".$row[$j]."</td>";
				
				( $row['detest'] == 'off' )? $wclassFila = " class='fondorojo ".$array_campos_select[ $j ]."' " : $wclassFila = "class='".$array_campos_select[ $j ]."'";
				
				echo "<td ".$wclassFila." title='".$wnom_variables[ $j ]."'>".$row[$j]."</td>";
			}
			echo "</tr>";      
		}
		echo "</table>";
		echo "</div>";       
	}        
  }  
  
  
function mostrarFormularioCampo(){
	
   global $conex;
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wformulario;
   global $wvariables;
   global $wnom_variables;
   global $wobligatorio;
   global $wvalidacion;
   global $wcar_obl;
   
   global $wbotmod;
   global $wbotgra;
   global $wdettip;
   global $wdetcon;
   global $wdetase;
   global $wdetved;
   
   global $wemp_pmla;
   
   global $num_columnas_form;
   
   	for ($i=0;$i<count($wvariables);$i++){
		global ${$wvariables[$i]};
		
		${$wvariables[$i]} = str_replace( '\'', '&#39;', ${$wvariables[$i]} );
		
		if( ${$wvariables[$i]} == "NO APLICA" && $wvalidacion[$i] == "Disabled")
			${$wvariables[$i]} = "";
	}
	
	if( $wformulario != "" ){
		$q = " SELECT Enccol "
			."   FROM ".$wbasedato."_000001 "
			."  WHERE Encpro = '".$wformulario."'"
			."  LIMIT 1 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0){
			$row = mysql_fetch_array($res);   
			echo "<input type='hidden' id='numero_columnas_formulario' value='".$row[0]."' />";
			$num_columnas_form = $row[0];
		}
	}
	//Envio los arreglos en el http	 
	/*for ($i=0;$i<count($wvariables);$i++)
	{
		echo "<input type='HIDDEN' name='wvariables[".$i."]' value='".$wvariables[$i]."'>"; 
		echo "<input type='HIDDEN' name='wvalidacion[".$i."]' value='".$wvalidacion[$i]."'>";  
		echo "<input type='HIDDEN' name='wobligatorio[".$i."]' value='".$wobligatorio[$i]."'>";
		echo "<input type='HIDDEN' name='wcar_obl[".$i."]' value='".$wcar_obl[$i]."'>"; 
		echo "<input type='HIDDEN' name='wnom_variables[".$i."]' value='".$wnom_variables[$i]."'>";    
	} */ 
		       	   
	     	
	    //====================================================================================================================
	    //====================================================================================================================
		//====================================================================================================================
		//DESDE ACA SE COMIENZA EL AREA DE LA PRESENTACION
		//====================================================================================================================
		//====================================================================================================================
		//====================================================================================================================
		      
		//echo "<table>";
		echo "<div id='formulario_formato_item' style='display:none;'>";
		echo "<span><font size=4 id='titulo_formulario_item'>Nuevo campo</font></span>";
		echo "<table><tr><td><input type='button' name='btnampliar' value='Ampliar Vista' onClick='Ampliarventana()'></td></tr></table>";
		echo "<table width='98%' height='422' border='0'>";
   
		$k=0;

		//====== 1ra Linea ======
		echo "<tr class=fila1>"; 
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";

		( ${$wvariables[$k]} != '' )? $wvalidacion[$k] = 'disabled' : $wvalidacion[$k] = 'enabled';
		echo "<td><input type='text' name='".$wvariables[$k]."' id='".$wvariables[$k]."' value='".${$wvariables[$k]}."' ".$wvalidacion[$k]." size=10></td>";     
		$k++;

		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		( ${$wvariables[$k]} != '' )? $wvalidacion[$k] = 'disabled' : $wvalidacion[$k] = 'enabled';
		echo "<td><input type='text' name='".$wvariables[$k]."' id='".$wvariables[$k]."' value='".${$wvariables[$k]}."' ".$wvalidacion[$k]." size=4></td>";     
		$k++;

		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input type='text' name='".$wvariables[$k]."' id='".$wvariables[$k]."' value='".${$wvariables[$k]}."' ".$wvalidacion[$k]." size=4></td>";     
		$k++;

		//TIPO DE DATO
		$q = " SELECT tipdat "
		    ."   FROM ".$wbasedato."_000010 "
		    ."  ORDER BY 1 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td>";
				
		global $consecutivo;
		
		$disabled_tipo_dato = "";
		$whay="off";	
		if( $consecutivo != '' )
			$whay=validarSiHayDatos($wformulario, $consecutivo);
		if ($whay=="on"){
			$disabled_tipo_dato = " disabled ";
		}				
				
		//echo "<SELECT name='wdettip' id='wdettip' ".$wvalidacion[$k]." onchange='consultarCondicionesTipoItem()'>";                          
		echo "<SELECT name='wdettip' id='wdettip' onchange='consultarCondicionesTipoItem()' ".$disabled_tipo_dato." >";                       
	
		echo "<OPTION value=''>&nbsp;</option>";   
		
		for ($j=1;$j<=$num;$j++)
		{
			$row = mysql_fetch_array($res);
			if(isset($wdettip) && $wdettip == $row[0] ){
				echo "<OPTION SELECTED>".$row[0]."</OPTION>";
			}else{
				echo "<OPTION>".$row[0]."</OPTION>";
			}			
		}
		echo "</SELECT></td>";
		$k++;
		
		
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td> <input name='wdetume' id='wdetume' type='text' value='".$wdetume."' ".$wvalidacion[$k]." size='5'></td>";  //$wunimed
		$k++;
		echo "</tr>";

		//====== 2da Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td colspan=3><input type='text' name='wdetdes' id='wdetdes' value='".$wdetdes."' ".$wvalidacion[$k]." size=40></td>";     //$wdetdes
		$k++;

		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdetcol' class='solonumeros' id='wdetcol' type='text' value='".$wdetcol."' ".$wvalidacion[$k]." size='2'></td>";  //$wdetcol
		$k++;
		echo "<td><b><font color=3232CD>".$wcar_obl[$k]."</font>".$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdethl7' id='wdethl7' type='checkbox' ".$wdethl7." ".$wvalidacion[$k]."></td>";               //HL7
		$k++;
		 
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdetjco' id='wdetjco' type='checkbox' ".$wdetjco." ".$wvalidacion[$k]."></td>";              //Join Commission
		$k++;
		echo "</tr>";
		
		
		//====== 3ra Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td colspan=3> <input name='wdetnpa' id='wdetnpa' type='text' value='".$wdetnpa."' ".$wvalidacion[$k]." size='40'></td>"; //$wnompan
		$k++;
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdetsiv' id='wdetsiv' type='checkbox' ".$wdetsiv." ".$wvalidacion[$k]."></td>";         //Sivigila
		$k++;
		//////
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><SELECT name='wdetase' id='wdetase' ".$wvalidacion[$k]."'>";                       
		if (isset($wdetase) and $wdetase!="")   
		   {
			switch ($wdetase)
			  {
			   case "F":   
		          {
			       echo "<option SELECTED>F</option>";   
		           echo "<option>M</option>";
		           echo "<option>A</option>";
	              } 
		          break; 
		       case "M":   
		          {
			       echo "<option SELECTED>M</option>";   
		           echo "<option>F</option>";
		           echo "<option>A</option>";
	              } 
		          break;
		       case "A":   
		          {
			       echo "<option SELECTED>A</option>";   
		           echo "<option>F</option>";
		           echo "<option>M</option>";
	              } 
		          break;
	          }         
		   }
		  else 
		     {
			  echo "<option>A</option>";   
		      echo "<option>M</option>";
		      echo "<option>F</option>";
		     } 
		echo "</SELECT></td>";
		//////
		$k++;
		
		//===========================================================
		//Enero 24 de 2013
		//===========================================================
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		///echo "<td><input name='wdetved' type='checkbox' ".$wdetved." ".$wvalidacion[$k]."></td>";         //Valida Edad
		echo "<td><SELECT name='wdetved' id='wdetved' ".$wvalidacion[$k].">";     
		$arr_rangoedad = array();
		$qre = " SELECT Raecod "
			."   FROM ".$wbasedato."_000041 "
			."  WHERE Raeest = 'on'";
		$resre = mysql_query($qre,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numre = mysql_num_rows($resre);
		
		if ($numre > 0){
			while( $row = mysql_fetch_array($resre) )
				array_push( $arr_rangoedad, $row[0]);
		}	
		
		$wdetved = trim( $wdetved );
		if( in_array($wdetved, $arr_rangoedad) ){
			echo "<option>".$wdetved."</option>";
		}else{ //Por defecto es T
			$wdetved = 'T';
			echo "<option>".$wdetved."</option>";
		}				
		foreach( $arr_rangoedad as $raedc ){
			if( $raedc != $wdetved )
				echo "<option>".$raedc."</option>";				
		}
		echo "</SELECT></td>";
		$k++;
		echo "</tr>";
		
		
		//====== 4ta Linea ======
		echo "<tr class=fila1>";
		//==================================================================================================================
		//ARCHIVO DE VALIDACION
		echo "<td><b>".$wcar_obl[$k]."Archivo de<br>Validaci&oacute;n</b></td>";
		switch ($wdettip)
		   {
			case "Seleccion":
			   {
				$wdetarc="000012";  //On Ojo si esto cambia por $wbasedato_000012 ==>HCE_000012     
				echo "<td id='td_archivo_validacion' colspan=5><input type='text' name='wdetarc' id='wdetarc' value='".$wdetarc."' ".$wvalidacion[$k]."></td>";   
			   }   
			   break;
		    case "Memo":
		       {		       	    
		       		$q = " SELECT Dettip,Detarc"
		            ."  FROM ".$wbasedato."_000002 "
		            ."  WHERE detpro = '".$wdetpro."' " 
		            ."  AND detcon = '".$consecutivo."' "
		            ."  AND dettip ='Memo'" ;
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    $num = mysql_num_rows($res);

					if ($num>0){
						while( $row= mysql_fetch_array($res) ){
							   $wdetarc=$row['Detarc'];
					        }
				    	}
				    else
				    	{$wdetarc="";}					
					echo "<td id='td_archivo_validacion' colspan=5><input type='text' name='wdetarc' id='wdetarc' value='".$wdetarc."'></td>";   
			   }
			   break;
			case "PassWord":
			case "Referencia":
			   {
				$q = " SELECT encpro, encdes "
				    ."   FROM ".$wbasedato."_000001 "
			        ."  WHERE encest = 'on' "
			        ."  ORDER BY 1 ";
			    $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);   
				   
				echo "<td id='td_archivo_validacion' colspan=5><SELECT name='wdetarc' id='wdetarc' onchange='consultarCampoArchivoValidacion()' ".$wvalidacion[$k].">";                      //$wdetarc
					
				if (isset($wdetarc))   
				   {
					$wdetarc1 = explode("_",$wdetarc);
				       
				    $q = " SELECT encpro, encdes "
				        ."   FROM ".$wbasedato."_000001 "
				        ."  WHERE encpro = '".$wdetarc1[0]."'"
				        ."    AND encest = 'on' "
				        ."  ORDER BY 1 ";
				    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    $row1 = mysql_fetch_array($res1);
				            
				    echo "<OPTION value='".$row1[0]."' SELECTED>".$row1[0]."_".$row1[1]."</option>";   
				   }else{
						echo "<option value=''>&nbsp;</option>";
				   }
				   
					while( $row = mysql_fetch_array($res)){   
						echo "<option value='".$row['encpro']."'>".$row['encpro']."_".utf8_encode($row['encdes'])."</option>";
					}
				echo "</SELECT></td>";  
			   }   
			   break; 
			case "Tabla":
		       {
			    $q = " SELECT medico, codigo, nombre "
			        ."   FROM formulario "
			        ."  WHERE activo = 'A' "
			        ."  ORDER BY 1 ";
				$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
			    echo "<td id='td_archivo_validacion' colspan=5><SELECT name='wdetarc' id='wdetarc' onchange='consultarCampoArchivoValidacion()' ".$wvalidacion[$k].">";                      //$wdetarc
				
				if (isset($wdetarc))   
				   {
					$arreglo = explode("_", $wdetarc);					
					echo "<OPTION value='".$arreglo[0]."_".$arreglo[1]."' SELECTED>".$wdetarc."</option>";
				   }
				for ($j=1;$j<=$num;$j++)
				   {
					$row = mysql_fetch_array($res);   
					echo "<OPTION value='".$row[0]."_".$row[1]."'>".$row[0]."_".$row[1]."_".$row[2]."</option>";
				   }
				echo "</SELECT></td>";
	           }
	           break;
	        default:
	           {
	            unset($wdetarc);
	            unset($wdetcav);
	            echo "<td id='td_archivo_validacion' colspan=5>&nbsp</td>";
               }
               break; 
		   }		
		$k++;
		
		//CAMPO A VALIDAR DEL ARCHIVO DE VALIDACION
		switch ($wdettip)
		   {
			case "Seleccion":
			   {
				$q = " SELECT msetab, msedes "
				    ."   FROM ".$wbasedato."_000014 "
				    ."  WHERE mseest = 'on' "
				    ."  ORDER BY 1 ";
				$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				
				echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
				echo "<td id='td_campo_a_validar' colspan=3><SELECT name='wdetcav' id='wdetcav' ".$wvalidacion[$k].">";                                     //$wdetcav
				if (isset($wdetcav) and ($wdetcav!=""))   
				   {
				    $wdetcav1 = explode("_",$wdetcav);
				       
				    $q1 = " SELECT msetab, msedes "
				         ."   FROM ".$wbasedato."_000014 "
				         ."  WHERE msetab = '".$wdetcav1[0]."'"
				         ."  ORDER BY 1 ";
				    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    $num1 = mysql_num_rows($res1);
				    if ($num1>0)
				      {
				       $row1= mysql_fetch_array($res1);
				       echo "<OPTION value='".trim($row1[0])."' SELECTED>".$row1[0]."_".$row1[1]."</option>";
			          } 
				   }
			  
				for ($j=1;$j<=$num;$j++)
				   { 
					$row = mysql_fetch_array($res);   
					echo "<OPTION value='".$row[0]."'>".$row[0]."_".$row[1]."</option>";
				   }
			    echo "</SELECT></td>";
			   }
			   break;
			case "Memo":
			case "PassWord":
			case "Referencia":
			   {
				if (isset($wdetarc1[0]) and trim($wdetarc1[0])!="")
			      {
					$q = " SELECT detcon, detdes "
					    ."   FROM ".$wbasedato."_000002 "
					    ."  WHERE detpro = '".$wdetarc1[0]."'"
					    ."    AND detest = 'on' "
					    ."  ORDER BY 1 ";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res);
					
					echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
					echo "<td id='td_campo_a_validar' colspan=3><SELECT name='wdetcav' id='wdetcav' ".$wvalidacion[$k].">";                                     //$wdetcav
					
					if (isset($wdetcav) and ($wdetcav!=""))   
					   {
					    $wdetcav1 = explode("_",$wdetcav);
					       
					    $q1 = " SELECT detcon, detdes "
					         ."   FROM ".$wbasedato."_000002 "
					         ."  WHERE detpro = '".$wdetarc1[0]."'"
					         ."    AND detcon = '".$wdetcav1[0]."'"
					         ."  ORDER BY 1 ";
					    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					    $num1 = mysql_num_rows($res1);
					    if ($num1>0)
					      {
					       $row1= mysql_fetch_array($res1);
					       echo "<OPTION value='".$row1[0]."' SELECTED>".$row1[0]."_".$row1[1]."</option>";
				          } 
					   }else{
						echo "<option value=''>&nbsp;</option>";
					   }
				  
					for ($j=1;$j<=$num;$j++)
					   { 
						$row = mysql_fetch_array($res);   
						echo "<OPTION value='".$row[0]."'>".$row[0]."_".$row[1]."</option>";
					   }
				   echo "</SELECT></td>";
				  }
				 else
				    {
					 echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
					 echo "<td id='td_campo_a_validar' colspan=3>&nbsp</td>";
					}  
			   }
			   break;   
			case "Tabla":
			      {
			       if (isset($wdetarc))
				      {
					    $wdetarc1 = explode("_",$wdetarc);  
					      
						$q = " SELECT campo, descripcion "
						    ."   FROM det_formulario "
						    ."  WHERE medico = '".trim($wdetarc1[0])."'"
						    ."    AND codigo = '".trim($wdetarc1[1])."'"
						    ."    AND activo = 'A' "
						    ."  ORDER BY 1 ";
						$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);
						
						echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
						echo "<td id='td_campo_a_validar' colspan=3><SELECT name='wdetcav' id='wdetcav' ".$wvalidacion[$k].">";                                     //$wdetcav
						if (isset($wdetcav) and (trim($wdetcav)!=""))   
						   {
						    $wdetcav1 = explode("_",$wdetcav);
						       
						    $q1 = " SELECT campo, descripcion "
						         ."   FROM det_formulario "
						         ."  WHERE medico = '".trim($wdetarc1[0])."'"
						         ."    AND codigo = '".trim($wdetarc1[1])."'"
						         ."    AND campo  = '".trim($wdetcav1[0])."'"
						         ."    AND activo = 'A' "
						         ."  ORDER BY 1 ";
						    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
						    $num1 = mysql_num_rows($res1);
						    if ($num1>0)
						      {
						       $row1= mysql_fetch_array($res1);
						       echo "<OPTION value='".$row1[0]."' SELECTED>".$row1[0]."_".$row1[1]."</option>";
					          } 
						   }
					  
						for ($j=1;$j<=$num;$j++)
						   { 
							$row = mysql_fetch_array($res);   
							echo "<OPTION value='".$row[0]."'>".$row[0]."_".$row[1]."</option>";
						   }
					   echo "</SELECT></td>";
					  }
					 else
					    {
						 echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
						 echo "<td id='td_campo_a_validar' colspan=3>&nbsp</td>";
						}  
			      }
			      break;	
			default:
	           {
	            echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
	            echo "<td id='td_campo_a_validar' colspan=3>&nbsp</td>";
               }
               break;      
		   }	
		$k++;
		
		//====== 5ta Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."Valor por<br>Defecto</b></td>";
		echo "<td colspan=3><input name='wdetvde' id='wdetvde' type='text' value='".$wdetvde."' ".$wvalidacion[$k]." size='40'></td>"; //$wvaldef
		$k++;
		
		echo "<td><b>".$wcar_obl[$k]."Presentaci&oacute;n<br>Continua</b></td>";
		echo "<td><input name='wdetimc' id='wdetimc'  type='checkbox' ".$wdetimc." ".$wvalidacion[$k]."></td>";         //Imprime y muestra el dato luego de la descripción
		$k++;

		echo "<td><b>".$wcar_obl[$k]."Validaci&oacute;n<br>Complementaria</b></td>";
		echo "<td><input name='wdetvco' id='wdetvco' type='checkbox' ".$wdetvco." ".$wvalidacion[$k]."></td>";         //Validacion Complementaria
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Validaci&oacute;n<br>Complementaria<br>Restrictiva</b></td>";
		echo "<td><input name='wdetvcr' id='wdetvcr' type='checkbox' ".$wdetvcr." ".$wvalidacion[$k]."></td>";         //Val. Compl. Restrictiva
		$k++;
		echo "</tr>";
		//==================================================================================================================
		
		
		//====== 6ta Linea ======
		echo "<tr class=fila1>"; 
		//2015-01-14 se cambia detvim por un select
		//echo "<td><b>".$wcar_obl[$k]."Imprime o no<br>Imprime</b></td>";
		//echo "<td colspan=3><input name='wdetvim' id='wdetvim' type='checkbox' ".$wdetvim." ".$wvalidacion[$k]."></td>";
		
		echo "<td><b>".$wcar_obl[$k]."Valor de Impresion</b></td>";
		
		$codigoVI = consultarAliasPorAplicacion($conex, $wemp_pmla, 'valorImpresionHCE');
		$q1 = "  SELECT subcodigo as cod, descripcion as des
				   FROM det_selecciones
				  WHERE medico = '".$wbasedato."'
					AND codigo = '".$codigoVI."'
					AND activo = 'A'
			   ORDER BY 1 ";
		$res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num1 = mysql_num_rows($res1);
		echo "<td colspan=3>
				<select name='wdetvim' id='wdetvim' ".$wvalidacion[$k].">";
		echo "		<option value=''>&nbsp;</option>";
		
		if ($num1>0)
		{
			$arr_vals = array();
			while( $row1= mysql_fetch_array($res1) ){
				$arr_vals[ $row1['cod'] ] = $row1['des'];
			}
			
			if( array_key_exists( $wdetvim, $arr_vals ) == true ){
				echo "<option value='".$wdetvim."' selected>".$arr_vals[$wdetvim]."</option>";
			}			
			foreach( $arr_vals as $codigx => $descgx ){
				if( $wdetvim != $codigx )
					echo "<option valor='$wdetvim' value='".$codigx."'>".$descgx."</option>";
			}
			
		
			/*while( $row1= mysql_fetch_array($res1) ){
				if( $wdetvim == $row1['cod'] )
					echo "<option value='".$row1['cod']."' selected>".$row1['des']."</option>";
				else
					echo "<option valor='$wdetvim' value='".$row1['cod']."'>".$row1['des']."</option>";
			}*/
		}		
		echo "	</select></td>";
		
		
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Obligatorio</b></td>";
		echo "<td> <input name='wdetobl' id='wdetobl' type='checkbox' ".$wdetobl." ".$wvalidacion[$k]."></td>";       //Obligatorio
		$k++;

		echo "<td><b>".$wcar_obl[$k]."Depende de</b></td>";
		echo "<td><input name='wdetdep' id='wdetdep' type='checkbox' ".$wdetdep." ".$wvalidacion[$k]."></td>";       //Depende
		$k++;
		echo "<td><b>".$wcar_obl[$k]."N&uacute;mero del<br>Campo del<br>que Depende</b></td>";
		echo "<td><input name='wdetcde' id='wdetcde' class='solonumeros' type='text' value='".$wdetcde."' ".$wvalidacion[$k]." size='2'></td>";  //# Campo que Depende
		$k++;
		echo "</tr>";
		   
        
		//====== 7ma Linea ======
		echo "<tr class=fila1>"; 
		echo "<td><b>".$wcar_obl[$k]."URL (Direcci&oacute;n)</b></td>";
		echo "<td colspan=3><input name='wdeturl' id='wdeturl' type='text' value='".$wdeturl."' ".$wvalidacion[$k]." size='40'></td>"; //URL
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Campos del<br>Conjunto</b></td>";
		echo "<td><input name='wdetcco' id='wdetcco' type='text' value='".$wdetcco."' ".$wvalidacion[$k]." size='10'></td>"; //Campos Conjunto
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Caracter del<br>Conjunto</b></td>";
		echo "<td><input name='wdetcac' id='wdetcac' type='text' value='".$wdetcac."' ".$wvalidacion[$k]." size='2'></td>";  //Caracter Conjunto
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Nivel de<br>Seguridad</b></td>";
		echo "<td><input name='wdetnse' id='wdetnse' type='text' value='".$wdetnse."' ".$wvalidacion[$k]." size='2'></td>";  //Nivel de Seguridad
		$k++;
        echo "</tr>";
		
		
		//====== 8va Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."Formula</b></td>";
		
	//	$wdetfor = str_replace( '\'', '&#39;', $wdetfor );
			
		//echo "<td colspan=3> <input name='wdetfor' id='wdetfor' type='text' value='".($wdetfor)."' ".$wvalidacion[$k]." size='40'></td>"; 
		//if( $wvalidacion[$k] == 'Disabled' )
		//	$wvalidacion[$k] = "readonly";
		echo "<td colspan=3> <textarea rows=3 cols=30 name='wdetfor' id='wdetfor' ".$wvalidacion[$k].">".($wdetfor)."</textarea></td>"; 
		$k++;
        if ($wdetfac=="")
		   $wdetfac=$wfecha;
		echo "<td><b>".$wcar_obl[$k]."Fecha de<br>Activaci&oacute;n</b></td>";
		echo "<td><input name='wdetfac' id='wdetfac' type='text' value='".$wdetfac."' ".$wvalidacion[$k]." size='10'></td>";
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Estado</b></td>";
		echo "<td><input name='wdetest' id='wdetest' type='checkbox' ".$wdetest." ".$wvalidacion[$k]."></td>";
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Columnas<br>que ocupa</b></td>";
		echo "<td><input name='wdetcoa' class='solonumeros msg_tooltip' title='Maximo ".$num_columnas_form."' id='wdetcoa' type='text' value='".$wdetcoa."' ".$wvalidacion[$k]." size='2'></td>";
		$k++;
		echo "</tr>";

		
		//====== 9na Linea ======
		echo "<tr class=fila1>";
		//Dato del Campo que depende
		////////////////////////////
		if (isset($wdetcde) and trim($wdetcde)!="")
		   { 
		   $q = " SELECT dettip, detarc, detcav "
		        ."   FROM ".$wbasedato."_000002 "
		        ."  WHERE detpro = '".$wdetpro."'"
		        ."    AND detcon = ".$wdetcde
		        ."    AND detest = 'on' ";
		    $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($res);
			
			$wtip_dat=$row[0];
			$warc_val=$row[1];
			$wcam_val=$row[2];
		   }
		
		if (isset($wtip_dat))
		   {        
		    ////////////////////////
			switch ($wtip_dat)
			   {
				case "Seleccion":
				   {
					//traigo todas opciones de la tabla de seleccion   
					$q = " SELECT selcda, selnda "
					    ."   FROM ".$wbasedato."_000012 "
					    ."  WHERE seltab = '".$wcam_val."'"    //Este campo equivale a la tabla de seleccion, que todas quedan en la 000012
					    ."    AND selest = 'on' "
					    ."  ORDER BY 1 ";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res);
					
					echo "<td><b>".$wcar_obl[$k]."Dato del Campo<br>que depende</b></td>";
					echo "<td colspan=3><SELECT name='wdetdde' id='wdetdde' ".$wvalidacion[$k]." >";                                    
					if (isset($wdetdde) and (trim($wdetdde)!=""))   
					   {
					    $wdetdde1 = explode("_",$wdetdde);
					       
					    $q1 = " SELECT selcda, selnda "
					         ."   FROM ".$wbasedato."_000012 "
					         ."  WHERE seltab = '".$wcam_val."'"
					         ."    AND selcda = '".trim($wdetdde1[0])."'"
					         ."  ORDER BY 1 ";
					    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					    $num1 = mysql_num_rows($res1);
					    if ($num1>0)
					      {
					       $row1= mysql_fetch_array($res1);
					       echo "<OPTION value='".$row1[0]."' SELECTED>".$row1[0]."_".$row1[1]."</option>";
				          } 
					   }
				  
					for ($j=1;$j<=$num;$j++)
					   { 
						$row = mysql_fetch_array($res);   
						echo "<OPTION value='".$row[0]."'>".$row[0]."_".$row[1]."</option>";
					   }
				    echo "</SELECT></td>";
				   }
				   break;
				case "Tabla":
				      {
				       if (isset($warc_val))
					      {
						    $warc_val1 = explode("_",$warc_val);  
						    //$wcam_val1 = explode("-",$wcam_val);
						    //$wdetdde1  = explode("-",$wdetdde);
						      
						    //Traigo el nombre del campo del cual se deben desplegar los datos
						    $q = " SELECT descripcion "
						        ."   FROM det_formulario "
						        ."  WHERE medico = '".trim($warc_val1[0])."'"
							    ."    AND codigo = '".trim($warc_val1[1])."'"
							    ."    AND campo  = '".trim($wcam_val)."'"
							    ."    AND activo = 'A' ";
							$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num = mysql_num_rows($res);
							if ($num>0)
							   {
								$row= mysql_fetch_array($res);  
								 
								$q = " SELECT ".$row[0]                                         //Campo del cual salen los datos
								    ."   FROM ".trim($warc_val1[0])."_".trim($warc_val1[1])     //Conforman el archivo del cual salen los datos
								    ."  ORDER BY 1 ";
								$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
								$num = mysql_num_rows($res);
								
								echo "<td><b>".$wcar_obl[$k]."Dato del Campo<br>que depende</b></td>";
								echo "<td colspan=3><SELECT name='wdetdde' id='wdetdde' ".$wvalidacion[$k].">";                                    
								if (isset($wdetdde) and (trim($wdetdde)!=""))    
								   {
								    $q1 = " SELECT ".$row[0]
								         ."   FROM ".trim($warc_val1[0])."_".trim($warc_val1[1])
								         ."  WHERE ".$row[0]." = '".$wdetdde."'"
								         ."  ORDER BY 1 ";
								    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
								    $num1 = mysql_num_rows($res1);
								    
								    if ($num1>0)
								      {
								       $row1= mysql_fetch_array($res1);
								       echo "<OPTION SELECTED>".$row1[0]."</option>";
							          } 
								   }
							  
								for ($j=1;$j<=$num;$j++)
								   { 
									$row = mysql_fetch_array($res);   
									echo "<OPTION>".$row[0]."</option>";
								   }
							    echo "</SELECT></td>";
						       } 
						  }
					  }
				      break;	
				default:
		           {
		            echo "<td><b>".$wcar_obl[$k]."Dato del Campo<br>que depende</b></td>";
					
					echo "<td colspan=3><input name='wdetdde' id='wdetdde' type='text' value='".$wdetdde."' ".$wvalidacion[$k]."><img src=/matrix/images/medical/TCX/tic.png alt='PAsooooo'></td>";           
				   }
	               break;      
			   }
			//////////////////////////////////////////   
           }  //fin del if isset($wtip_dat)
          else
            {
	         echo "<td><b>".$wcar_obl[$k]."Dato del Campo<br>que depende</b></td>";
			 echo "<td colspan=3><input name='wdetdde' id='wdetdde' type='text' value='".$wdetdde."' ".$wvalidacion[$k]." size='40'></td>";   
            }     
		$k++;
        echo "<td><b>".$wcar_obl[$k]."Radio Bot&oacute;n<br>Seleccion</b></td>";
		echo "<td> <input name='wdetprs' id='wdetprs' type='checkbox' ".$wdetprs." ".$wvalidacion[$k]." onclick='Seleccionarcantidad(this);'></td>";        
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Cantidad Listas<br>Radio Bot&oacute;n</b></td>";
		echo "<td><input name='wdetlrb' id='wdetlrb' type='text' value='".$wdetlrb."' ".$wvalidacion[$k]." size=1></td>";            
		$k++;
		echo "<td><b>Se imprime<br>Label</b></td>";
		echo "<td><input name='wdetimp' id='wdetimp' type='checkbox' ".$wdetimp." ".$wvalidacion[$k]."></td>";               
		$k++;
		echo "</tr>";	
		
		//====== 10ma Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."Campos de<br>Busquedad(,)</b></td>";
		echo "<td colspan=3> <input name='wdetcbu' id='wdetcbu' type='text' value='".$wdetcbu."' ".$wvalidacion[$k]." size='40'></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Tipo<br>Tabla (S/M)</b></td>";
		echo "<td><input name='wdettta' id='wdettta' type='text' value='".$wdettta."' ".$wvalidacion[$k]." size=1></td>";            
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Alto Memo</b></td>";
		echo "<td><input name='wdetalm' class='solonumeros' id='wdetalm' type='text' value='".$wdetalm."' ".$wvalidacion[$k]." size='2'></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Ancho Memo</b></td>";
		echo "<td><input name='wdetanm' class='solonumeros' id='wdetanm' type='text' value='".$wdetanm."' ".$wvalidacion[$k]." size='2'></td>";            
		$k++;
		echo "</tr>";
		
		//====== 11va Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."Nombres<br>Campos de<br>Busqueda(,)</b></td>";
		echo "<td colspan=3> <input name='wdetnbu' id='wdetnbu' type='text' value='".$wdetnbu."' ".$wvalidacion[$k]." size='40'></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Cualificable</b></td>";
		echo "<td><input name='wdetcua' id='wdetcua' type='checkbox' ".$wdetcua." ".$wvalidacion[$k]." size=1></td>"; 
		
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Caracteres<br>Cualificables</b></td>";
		echo "<td><input name='wdetccu' id='wdetccu' type='text' value='".$wdetccu."' ".$wvalidacion[$k]." size=6></td>";            
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Cronologico</b></td>";
		echo "<td><input name='wdetcro' id='wdetcro' type='checkbox' ".$wdetcro." ".$wvalidacion[$k]."></td>";               
		$k++;
		echo "</tr>";
		
		//====== 12va Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."'Tip'<br>Informativo</b></td>";
		echo "<td colspan=3> <input name='wdettii' id='wdettii' type='text' value='".$wdettii."' ".$wvalidacion[$k]." size='40'></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Tabla<br>Desplegable</b></td>";
		echo "<td><input name='wdetdpl' id='wdetdpl' type='checkbox' ".$wdetdpl." ".$wvalidacion[$k]."></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Valor M&iacute;nimo</b></td>";
		echo "<td><input name='wdetvmi' id='wdetvmi' type='text' value='".$wdetvmi."' ".$wvalidacion[$k]." size=5></td>";            
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Valor M&aacute;ximo</b></td>";
		echo "<td><input name='wdetvma' id='wdetvma' type='text' value='".$wdetvma."' ".$wvalidacion[$k]." size=5></td>";            
		$k++;
		echo "</tr>";
		
		//====== 13va Linea ======
		echo "<tr class=fila1>"; 
		echo "<td><b>".$wcar_obl[$k]."Permite<br>Copiar/Pegar</b></td>";
		echo "<td><input name='wdetcpp' id='wdetcpp' type='checkbox' ".$wdetcpp." ".$wvalidacion[$k]."></td>";
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Mostrar<br>Colapsado</b></td>";
		echo "<td><input name='wdetcop' id='wdetcop' type='checkbox' ".$wdetcop." ".$wvalidacion[$k]."></td>";
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Validaci&oacute;n <br>espec&iacute;fica</b></td>";
		echo "<td colspan=3><textarea  name='wdetves' id='wdetves' ".$wvalidacion[$k]." cols=30 rows=3>".$wdetves."</textarea></td>";
		echo "<td colspan=2>&nbsp;</td>";
		// echo "<td colspan=6>&nbsp;</td>";
		echo "</tr>";
		$k++;
		
		echo "</table>";
		
		echo "<center>";
		echo "<input type='button' value='Guardar' onclick='guardarConfigurarItem();' />";
		echo "<input type='button' value='Cerrar' onclick='cerrarConfigurarItem();' />";
		echo "</center>";
		
		echo "</div>";
		
		echo "<br><br><br>";
		/*echo "<table>";
		echo "<div align='center'>";   
		echo "<p>";
		echo "<input type='submit' name='Iniciar'   value='Iniciar'>";
		echo "&nbsp&nbsp;|&nbsp"; 
		echo "<input type='submit' name='Grabar'    value='Grabar' ".$wbotgra.">";
		echo "&nbsp;|&nbsp"; 
		echo "<input type='submit' name='Modificar' value='Modificar' ".$wbotmod.">";
		echo "&nbsp;|&nbsp"; 
		echo "<input type='submit' name='Consultar' value='Consultar'>";
		echo "&nbsp;&nbsp;|&nbsp;&nbsp";
		echo "<input type='submit' name='Salir'     value='Salir' onclick='cerrarVentana()'>";
		echo "</p>";
		echo "</div>";
		echo "</table>";	*/
	
}
//=================================================================================================================================
//***************************** T E R M I N A   L A   D E F I N I C I O N   D E   F U N C I O N E S *******************************
//=================================================================================================================================
//$wformulario = "";
 if( isset($consultaAjax) == false){

echo "<form name='DetalleProtocolos' method='post' action=''>";
//encabezado("Encabezado del Protocolo",$wactualiz, "clinica");
//echo "<div align='center'>&nbsp<br>";
//echo "<CENTER>";

 echo "<div style='display:none'><a href='#div_auxiliar' id='enlace_inicio' >Inicio</a></div>";
}
if( isset($wdetpro) ){
	$wformulario = $wdetpro;	
}

global $grabar;
global $modificar;
global $iniciar;

global $wbotmod;   //Sirve para indicar cuando se prende el boton GRABAR o cuando se apaga
global $wbotgra;   //Sirve para indicar cuando se prende el boton MODIFICAR o cuando se apaga

global $wtip;      //Tipo de dato, que se debe llenar al consultar un registro


if (!isset($wdettip))
 {
  $wvalidacion = array();  //Este arreglo sirve para guardar el estado que debe tener cada campo de la pantalla segun el tipo de dato
                         //estos datos son obtenidos de la tabla 000010
  $wobligatorio= array();  //Aca se almacena la obligatoriedad o no de cada campo de la pantalla segun el tipo de dato
  $wvariables  = array();  //SE alamcenan los nombres de los campos en pantalla para validarlos en este programa
  $wcar_obl    = array();
  
  global $wvariables;
  global $wobligatorio;
  global $wvalidacion;
  global $wcar_obl;
  global $wnom_variables;
  global $wcampos_select;
  global $num_columnas_form;
  
  
  $wok=true;
  
  //Traigo todos los nombres de variables, almacenados en la tabla 000010, el MAX es para que solo traiga un registro, porque todos los registros deben de tener
  //las mismas variables
  $q = " SELECT tipvar, MAX(id) "
      ."   FROM ".$wbasedato."_000010 "
      ."  GROUP BY 1 ";
  $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
        
  if ($num > 0)
    {
     $row = mysql_fetch_array($res);   
 
     $wvar=explode("-",$row[0]);
     
     $wcampos_select="";
     
     
     for ($i=0;$i<count($wvar);$i++)
       {
	    if ($i>1) ${$wvar[$i]} = "";    //Esto inicializa todas las variables, excepto codigo y el consecutivo
	    
	    $wvalidacion[$i]     = "Enabled";
        $wobligatorio[$i]    = "On";
        $wvariables[$i]      = $wvar[$i];
        $wcar_obl[$i]        = "";
        $wnom_variables[$i]  = "";
         
        global ${$wvar[$i]};

        if ($i == 0) 
           ${$wvar[$i]}=$wformulario;  //Aca debe venir el campo wdetpro 
        
              
        if ($wcampos_select=="")
	       $wcampos_select = substr($wvariables[$i],1,strlen($wvariables[$i]));                       //Aca llevo los campos que se seleccionan segun estan en la tabla 000010
	      else 
	   	     $wcampos_select= $wcampos_select.",".substr($wvariables[$i],1,strlen($wvariables[$i]));
	    
        $row = mysql_fetch_array($res);
		}
       
       //Estos son los nombres de los campos en Pantalla  
	   $wnom_variables[0]="C&oacute;digo";
	   $wnom_variables[1]="Consecutivo";
	   $wnom_variables[2]="Orden Pantalla";
	   $wnom_variables[3]="Tipo Dato";
	   $wnom_variables[4]="Unidad Medida";
	   $wnom_variables[5]="Descripci&oacute;n";
	   $wnom_variables[6]="Columna Sangria";
	   $wnom_variables[7]="HL7";
	   $wnom_variables[8]="Joint Comission";
	   $wnom_variables[9]="Nombre Pantalla";
	   $wnom_variables[10]="Sivigila";
	   $wnom_variables[11]="Aplica al Sexo";
	   $wnom_variables[12]="Valida Edad";
	   $wnom_variables[13]="Tabla";
	   $wnom_variables[14]="Campo Tabla";
	   $wnom_variables[15]="Vr Defecto";
	   $wnom_variables[16]="Presentaci&oacute;n Continua";
	   $wnom_variables[17]="Validaci&oacute;n Complementaria";
	   $wnom_variables[18]="V.Comp.Restrictiva";
	   $wnom_variables[19]="Vr Imprime";
	   $wnom_variables[20]="Obligatorio";
	   $wnom_variables[21]="Depende";
	   $wnom_variables[22]="Campo Depende";
	   $wnom_variables[23]="URL";
	   $wnom_variables[24]="Campos Conjunto";
	   $wnom_variables[25]="Caracter Conjunto";
	   $wnom_variables[26]="Nivel Seguridad";
	   $wnom_variables[27]="Formula";
	   $wnom_variables[28]="Fecha Activaci&oacute;n";
	   $wnom_variables[29]="Estado";
	   $wnom_variables[30]="Columnas que Ocupa";
	   $wnom_variables[31]="Dato del Campo que Depende";
	   $wnom_variables[32]="Radio Buton Seleccion";
	   $wnom_variables[33]="Alto Campo Memo";
	   $wnom_variables[34]="Ancho Campo Memo y Texto";
	   $wnom_variables[35]="Campos de Busqueda Tabla";
	   $wnom_variables[36]="Tipo de Tabla (S/M)";
	   $wnom_variables[37]="Listas en el radio Bot&oacute;n";
	   $wnom_variables[38]="Se Imprime";
	   $wnom_variables[39]="Nombres Campos de Busqueda";
	   $wnom_variables[40]="Cualificable 'Tabla' ";
	   $wnom_variables[41]="Caracter Cualificador";
	   $wnom_variables[42]="Cronologico";
	   $wnom_variables[43]="'Tip' Informativo";
	   $wnom_variables[44]="Tabla Desplegable";
	   $wnom_variables[45]="Valor M&iacute;nimo";
	   $wnom_variables[46]="Valor M&aacute;ximo";
	   $wnom_variables[47]="Permite Copiar/Pegar";
	   $wnom_variables[48]="Mostrar Colapsado";
	   $wnom_variables[49]="Validaci&oacute;n espec&iacute;fica";
    }       
 }
 
    //Para que se vaya a las funciones de los llamados ajax y no imprima todo el formulario
 if( isset($consultaAjax) ){
	if( $action	== 'mostrarCampo' ){
		consultar();
		mostrarFormularioCampo();
	}else if ( $action == 'consultarCondicionesItem' ){
		traer_campos_obligatorios( $tipo );
	}
	return;
 }
 


 if( isset( $wparametro) ){
//--switch ($wparametro)
//--  {
//--    case "campos":
//--	   {
  	    //***************************************************************************************************************
		//*********   A C A   C O M I E N Z A   E L   B L O Q U E   P R I C I P A L   D E L   P R O G R A M A   *********
		//***************************************************************************************************************

		//Se evalua el boton presionado
		/*if (isset($Grabar) or isset($Modificar) or isset($Consultar) or isset($Iniciar))
		   {
			 if (isset($Grabar))
			   {
				validar_campos();
				insert_orden_pantalla();
				grabar();
				
				if ($wok==true)
				   {
					?>	    
				      <script> alert ("El Registro fue Grabado"); </script>
				    <?php
				    iniciar();
			       } 
			    
			    evaluar_campos_boleanos();  
			    
			    //
			    //Actualizo la grilla
			    //
			    echo "<script language=javascript>";
				   echo "top.principal.grilla.location.reload()";
				echo "</script>";
				//*************************************************
		       }	
		          
			 if (isset($Modificar))
			   {
			    $whay="off"; 
			
			    if ($wtip != $wdettip)                                     //Abril 26 de 2011
				   {
				    $whay=validarSiHayDatos($wdetpro, $wdetcon, $wtip);    //Abril 26 de 2011
				   }
				
				if ($whay=="off")                                          //Abril 26 de 2011
				   { 
					validar_campos();
					if ($wok==true) 
					   { 
						modificar();
						//*************************************************
						//Actualizo la grilla
						//*************************************************
						echo "<script language=javascript>";
							echo "top.principal.grilla.location.reload()";
						echo "</script>";
						//*************************************************
					   }
				   }
				  else                                                    //Abril 26 de 2011
					{
					 ?>
					   <script> alert ("Existen historías con este tipo de dato, NO se puede modificar, inactivelo y cree uno nuevo"); </script>
					 <?php
					} 
				iniciar();				   
		       }
		       
		     if (isset($Consultar))
			   { 
				 consultar();
			   }    
		       
		     if (isset($Iniciar))
			   { iniciar();
			   }  
		   } //fin del if (Grabar or Modificar or Consultar or Borrar)
		  else
		    {*/
			  if (isset($wdettip) and trim($wdettip) != ""){
			     evaluar_actividad_campos($wdettip);
			  }
			    //else
			       //iniciar(); 
			 //}
		    echo "<div id='div_auxiliar' >";
			mostrarFormularioCampo();
			echo "</div>";
//--	   }
//--	   break;   
//--   	case "grilla":
//--   	   {
	   	global $wvariables;
	   	global $wformulario;
		global $num_columnas_form;
	   	
		
	   	if ($wformulario != "") {
			iniciar();
			
			mostrar_grilla();   
		}else{
			echo "<font size=4>Por favor seleccione un formulario para interactuar con sus campos.</font>";
		}
//--       }	 
//--       break;
   }
   
   
   //Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';

 
 //for ($i=0;$i<count($wvariables);$i++)
  //  {
     //echo "<INPUT TYPE='hidden' NAME=wvariables[".$i."] VALUE = '".$wvariables[$i]."'>";
   // }  
   
 //if (isset($worpaux)) 
    //echo "<INPUT TYPE='hidden' NAME=worpaux VALUE = '".$worpaux."'>"; 
 
 //if (isset($wtip)) 
   // echo "<INPUT TYPE='hidden' NAME=wtip VALUE = '".$wtip."'>"; 
	
 echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."' />";
 echo "<input type='HIDDEN' name='wdetpro_global' id='wdetpro_global' value='".$wformulario."' />";   
 echo "<input type='hidden' id='fecha_server' value='".date('Y-m-d')."' />";
 //echo "<INPUT TYPE='hidden' NAME=wbotmod VALUE = '".$wbotmod."'>";
 //echo "<INPUT TYPE='hidden' NAME=wbotgra VALUE = '".$wbotgra."'>";
 //echo "<INPUT TYPE='hidden' NAME=wcampos_select VALUE = '".$wcampos_select."'>";
?>
</form>
</body>
</html>
