<?php
include_once("conex.php"); header('Content-type: text/html;charset=ISO-8859-1'); ?>

<?php
/*
Actualizaciones:
    2018-09-06: Jessica Madrid Mejía - Se agrega la creación del indice hising_idx al crear el formulario hce.
    2017-01-25: Se agrega el campo Encien (muestra encabeazado en impresion HCE).
    2016-05-25: Arleyda Insignares C. Se coloca el campo 'se firma' chuleado de manera predeterminada.
    2016-04-18: Se corrige validacion para la creacion de indices y se agrega la validacion para la creacion de los mismos, 
				ademas se la creación del indice conhising_idx cuando es una tabla de Matrix
    2014-05-19: Al crear las tablas se crea también el indice conhising_idx
	2014-05-15: Al crear las tablas se cambia el tipo de dato del campo movdat a TEXT, estaba en varchar(20000)
	2013-12-09: No se estaban guardando los servicios correctamente
	2013-08-20:	Se cambian las consultas relacionadas con cliame_000001 por movhos_000011 respecto a los servicios de ingreso
*/
	
	
//Para que en las solicitudes ajax no imprima <html><head> etc
if( isset($consultaAjax) == false ){	
?>
<html>
<head>
<title>Maestro Protolocos</title>
<script type="text/javascript" src="tabbed.js"></script>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>	
<script src="../../../include/root/toJson.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

<style>
.enlinea{
	display:inline-block;
	vertical-align: top;
	/*display: -moz-inline-stack;*/ /* FF2*/
	zoom: 1; /* IE7 (hasLayout)*/
	*display: inline; /* IE */
}
optgroup {color: gray;}
</style>
</head>

<script type="text/javascript">
	
	//Variable de estado que indica si estoy creando o editando un encabezado
	var editando_item = true; 
	var indice_viejo = 0;
	
	$(document).ready(function() {
		indice_viejo = $("#windmen").val();
		$(".solonumeros").keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});
		
		$('body').css('height','auto');
	});
		
	function nuevoEncabezado(){
		//Consultamos el nuevo codigo del encabezado
		var rango_superior = 245;
		var rango_inferior = 11;
		limpiarEncabezado();
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();

		if( $('#msjEspere').length == 0 ){
			construirDivEsperar();
		}
		
		$("#wnumero_servicios").val( 1 );
		var html_code = "<div align='left'><input type='checkbox' class='servicio' onclick='quitarServicio(this)' id='wservicio1' name='wservicio1' value='*' checked /><span>* - TODOS LOS HOSPITALARIOS</span><br></div>";
		$("#lista_servicios_agregados").append( html_code );
		
		
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Maestro_protocolos.php', { wemp_pmla: wemp_pmla, action: "consultarUltimoCodigoyOrdenimpresion",  consultaAjax: aleatorio} ,
			function(data) {
				data = $.trim( data );
				data = data.split("|||");
				if( (/^\d+$/).test(data[0]) ){
					$("#wformulario").val( data[0] );
					$("#wencoim").val( data[1] );
					//Valores por defecto
					$("#wencnfi").val( 950 );
					$("#wencnco").val( 600 );
					$("#wenchol").val( 24 );					
					$("#westado").attr( 'checked', true );
				}else{
					alert("Error al generar el nuevo codigo del formulario");
				}
				$.unblockUI();
			});
	}
	
	function limpiarEncabezado(){
		editando_item = false;
		$("#formulario_encabezado").find(':input:not(:button)').val('').attr('checked',false).attr("disabled",false);
		consultarOpcionesParaIndice();		
		$("#wesnodo").attr("checked",false).attr("disabled",false);
		$("#wformulario, #windmen").attr('disabled',true);
		$("#wencfir").attr('checked',true);
		$(".servicio").parent().remove();
	}	
	 
	//Funcion que se llama cuando se cierra la ventana modal de configuración del item, verifica que se hayan cumplido las condiciones necesarias
	function guardarEncabezado(){
		//Buscar que si lleno los obligatorios
		var cumple_obligatorios = true;
		
		if( $("#formulario_encabezado .obligatorio").length == 0 ){
			return;
		}
		
		$("#formulario_encabezado .obligatorio").each(function(){
			var input = $(this).parent().next().find(':input');
			if( input != undefined ){
				valor = input.val();
				if( valor == '' ){
					cumple_obligatorios = false;
					return false; //salir del each
				}
			}
		});
		
		//Si va a crear un nodo, solo es obligatorio el nombre y el indice en el menu
		if( $("#wesnodo").is(":checked") ){
			if( $("#wnompro").val() != "" ){
				cumple_obligatorios = true;
			}
		}
		
		if( cumple_obligatorios ){
			var columnasdelformulario = 0;
			var valores = new Array();
			
			var dateReg = /(^wservicio)/	
			//Guardar los campos ingresados
			//$("#formulario_encabezado input:not(:button)").each(function(){
			$("#formulario_encabezado").find(':input:not(:button)').each(function(){
				var obj = new Object();
				obj.clave = $(this).attr('id');
				
				if( $(this).is(':checkbox') &&  dateReg.test( obj.clave ) == false ){ //Que sea checkbox, pero que no sea de wservicio
						obj.valor = $(this).is(':checked');
				}else
					obj.valor = $(this).val();
					
				if( obj.clave == 'windmen'){
					var indice_nuevo = $("#indice_menu_nuevo").text();
					obj.valor = obj.valor + indice_nuevo;
				}
										
				if( isEmpty(obj.valor) ){	//Esto, porque si la variable es false y se hace el if ( obj.valor != '' ), toma el false como '' y no lo guarda			
				}else{
					valores.push( obj );
				}
			});

			var datosJson = $.toJSON( valores ); //convertir el arreglo de objetos en una variable json			
			//$("#formulario_encabezado").find(':input:not(:button)').val('').attr('checked',false);
			
			guardarServerEncabezado( datosJson ); //Dibujar el item con las configuraciones elegidas			
		}else{
			alert('Debe llenar los campos obligatorios antes de cerrar');
		}
	}
	
	function guardarServerEncabezado( valores_resp ){
		var datos = new Array();
		valores_resp = eval( valores_resp );	
		var dato = new Object();
		dato.indice_viejo = indice_viejo;
		dato.valores = valores_resp;			
		datos.push( dato );	

		if( datos.length == 0 )
			return;
		
		var datosJson = $.toJSON( datos );	
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
		if( $('#msjEspere').length == 0 ){
			construirDivEsperar();
		}
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Maestro_protocolos.php', { wemp_pmla: wemp_pmla, action: "guardarCambiosFormulario", editando: editando_item, datos: datosJson, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				data = $.trim( data );
				console.log( data );
				if( data == 'OK' || data == 'OKOK' ){
					alert("Guardado");
					limpiarEncabezado();
					//Recargar lista de formularios
					window.parent.parent.frames[2].location = $('#resetear_lista_formularios').val();
				}else if( isJson(data) ){
					data = eval('(' + data + ')');
					alert( data.msj );
				}else{
					alert("Error");
				}
			});
	}	
	
	function consultarOpcionesParaIndice(){
		var elegida = $("#indice_menu_select").val();
		if( elegida != 'RETORNAR' )
			$("#windmen").val( elegida );
		else
			$("#windmen").val( '' );
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
		if( $('#msjEspere').length == 0 ){
			construirDivEsperar();
		}
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Maestro_protocolos.php', { wemp_pmla: wemp_pmla, indice: elegida, action: "consultarOpcionesParaIndice", consultaAjax: aleatorio} ,
			function(data) {
				data = $.trim(data);
				//Si recibo codigo html que comienze con "<option"
				if( (/^<option/).test(data) ){
					$("#indice_menu_select").html( data );
					var ultimo = $("#indice_menu_select option:last").val();
					ultimo = parseInt(ultimo);
					if( (/^[0-9]/).test(ultimo) ){ //Si es un digito
						ultimo = ultimo.toString();
						if( elegida != "RETORNAR" ){
							ultimo = ultimo.substring( elegida.length, ultimo.length);
						}
						ultimo = parseInt(ultimo);
						ultimo++;
						ultimo = ultimo.toString();
						if( ultimo.toString().length == 1 )
							ultimo = "0"+ultimo+"";
						$("#indice_menu_nuevo").text( ultimo );
					}else{
						$("#indice_menu_nuevo").text( '01' );
					}
					
					//En ie6 el select opcion disabled no funciona, el siguiente codigo es para corregirlo
					if ( $.browser.msie ) {
						$('#indice_menu_select option:disabled').each(function(){
							 var texto = $(this).text();
							 $(this).replaceWith("<optgroup label='"+texto+"'>"+texto+"</optgroup>")
						});
					}

				}else if( data == "" ){
					$("#indice_menu_nuevo").text( '01' );
				}else{
					alert("Error");
				}
				$.unblockUI();
			}
		);
	
	}
	
	function configurarEsNodo( ele ){
		ele = jQuery( ele );
		var wformulario = $("#wformulario").val();
		if( ele.is(":checked") ){
			var indice = $("#windmen").val();			
			var nombre = $("#wnompro").val();
			$("#formulario_encabezado").find(':input:not(:button)').val('').attr('checked',false).attr("disabled",true);
			$("#windmen").val(indice);
			$("#wnompro").val(nombre);
			$("#wformulario").val(wformulario);
			$("#wesnodo").attr("checked",true);
			$("#wesnodo, #wnompro, #indice_menu_select").attr('disabled',false);
		}else{
			$("#formulario_encabezado").find(':input:not(:button)').val('').attr("disabled",false);
			$("#wformulario").val(wformulario);
			$("#wformulario, #windmen").attr('disabled',true);
		}
	}
	
	function bajar_indice(){
		var indice = $("#indice_menu_nuevo").text();
		if( indice == '' )
			return;
		if( indice.substring(0,1) == "0" )
			indice = indice.substring(1);		
		indice = parseInt( indice );
		if( indice > 1 ){			
			indice--;
			indice = ""+indice;
			if( indice.length == 1 )
				indice = "0"+indice;
			$("#indice_menu_nuevo").text(indice);		
		}
	}
	
	function subir_indice(){
		var indice = $("#indice_menu_nuevo").text();
		if( indice == '' )
			return;
		if( indice.substring(0,1) == "0" )
			indice = indice.substring(1);		
		indice = parseInt( indice );
		var opciones = $("#indice_menu_select option:last").index();
		opciones = parseInt(opciones);
		opciones--;
		if( indice <= opciones ){
			indice++;
			indice = ""+indice;
			if( indice.length == 1 )
				indice = "0"+indice;
			$("#indice_menu_nuevo").text(indice);		
		}
	}
	
	function quitarServicio(obj){
		if( $(".servicio").length > 1 ){
			obj = jQuery( obj );
			obj.parent().remove();
			var indice = 0;
			$(".servicio").each(function(){
				indice++;
				$(this).attr("name", "wservicio"+indice);
			});
			$("#wnumero_servicios").val( indice );
		}else{
			$(".servicio").eq(0).attr("checked",true);
		}
	}

	function agregar_servicios(){
		var codigo_elegido = $("#select_servicios").val();
		var texto_elegido = $("#select_servicios option:selected").text();
		if(codigo_elegido == "")
			return;
		var indice_servicio = $(".servicio").length;
		indice_servicio++;
		
		var existe = false;
		$(".servicio").each(function(){
			if( $(this).val() == codigo_elegido ){
				existe = true;
			}
		});
		if( existe == true )
			return;
		
		$("#wnumero_servicios").val( indice_servicio );
		var html_code = "<div align='left'><input type='checkbox' class='servicio' onclick='quitarServicio(this)' id='wservicio"+indice_servicio+"' name='wservicio"+indice_servicio+"' value='"+codigo_elegido+"' checked /><span>"+texto_elegido+"</span><br></div>";
		$("#lista_servicios_agregados").append( html_code );
	}
	
	//En ocaciones elimina el div sin razon aparente
	function construirDivEsperar(){
		var html_codigo = "<div id='msjEspere' style='display:none;'>";
		html_codigo+= '<br>';
		html_codigo+= "<img src='../../images/medical/ajax-loader5.gif'/>";
		html_codigo+= "<br><br> Por favor espere un momento ... <br><br>";
		html_codigo+= '</div>';
		$("#wemp_pmla").after(html_codigo);	
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
	
	function enter(){
	   document.forms.DetalleProtocolos.submit();
	}
	
	function cerrarVentana(){
      window.close()		  
    }	 
</script>

<body BGCOLOR=#DDDDDD>
<!-- Programa en PHP -->
<?php

}




include_once("root/comun.php");

$wactualiz = "2017-01-25";


//=================================
//Declaracion de variables globales
//=================================
global $wusuario;
global $wbasedato;
global $wfecha;
global $whora;
global $wok;
global $wformulario;
global $wnompro;
global $wtipuso;
global $wtipfor;
global $wtipimp;
global $wjerar;
global $wurl;
global $walerta;
global $wenccol;
global $wencnfi;
global $wencnco;
global $windmen;
global $wencvis;
global $wencmax;
global $wencfir;
global $wenctra;
global $wencenl;
global $wenchol;
global $wencmod;
global $wencoim;
global $wencoco;
global $westado;
global $wencien;

//=================================

$wbasedato= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
//$wbasecliame= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$wbasemovhos= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wfecha=date("Y-m-d");   
$whora = (string)date("H:i:s");
$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user)); 


if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if(  $action == 'consultarUltimoCodigoyOrdenimpresion' ){
		limpiarIndices();
		$ultimo_codigo = consultarUltimoConsecutivo();
		$ultimo_orden_impresion = consultarUltimoOrdenImpresion();
		echo $ultimo_codigo."|||".$ultimo_orden_impresion;
		return;
	}else if ( $action == 'guardarCambiosFormulario' ){
		if( $_REQUEST['editando'] == 'false' ){
			guardarNuevoFormulario( $_REQUEST['datos'] );
		}else{
			modificarEncabezado( $_REQUEST['datos'] );
		}
		echo "OK";
		return;
	}else if ( $action == 'consultarOpcionesParaIndice' ){
		$datos = consultarListaDeArbolParaIndice( $_REQUEST['indice'] );
		echo "<option value=''>&nbsp;</option>";
		echo "<option value='RETORNAR'><---RETORNAR</option>";
		foreach ($datos as $pos=>$dato){
		$disabled="";
		if( $dato['nodo'] != 'on' ){
			$disabled = "disabled='disabled'";
		}
			echo "<option value='".$dato['codigo']."' ".$disabled.">".$dato['codigo']."  - ".$dato['descripcion']."</option>";
		}		
		return;
	}
}


//=================================================================================================================================
//***************************************** D E F I N I C I O N   D E   F U N C I O N E S *****************************************
//=================================================================================================================================
/*
function leading_zero( $aNumber, $intPart, $floatPart=NULL, $dec_point=NULL, $thousands_sep=NULL) 
  {        
   $formattedNumber = $aNumber;
   if (!is_null($floatPart)) 
     {    //without 3rd parameters the "float part" of the float shouldn't be touched
      $formattedNumber = number_format($formattedNumber, $floatPart, $dec_point, $thousands_sep);
     }
  if ($intPart > floor(log10($formattedNumber)))
    $formattedNumber = str_repeat("0",($intPart + -1 - floor(log10($formattedNumber)))).$formattedNumber;
  return $formattedNumber;
  }
*/

function consultarUltimoOrdenImpresion(){
		global $conex;
		global $wbasedato;
		
		$q = " SELECT MAX(Encoim) "
			."   FROM ".$wbasedato."_000001 "
			."  WHERE Encoim != '9999'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		$cod = 0;
		if ($num > 0){
			$row = mysql_fetch_array($res);
			$cod = $row[0]+1;
		} 
		return $cod;
	}

function consultarUltimoConsecutivo(){
	global $wbasedato;
	global $conex;
	
	$wformulario = "1";
	
	//Busco que consecutivo sigue en el protocolo
	$q = " SELECT encpro as codigo "
		 ."  FROM ".$wbasedato."_000001 "
		 ." ORDER BY encpro DESC LIMIT 1";
		 
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
   
	if ($num > 0){
		$row = mysql_fetch_array($res);
		$wformulario = $row[0]+1;
	}
	
	while ( strlen($wformulario) < 6 ){
		$wformulario = "0".$wformulario;			
	}
	
	return $wformulario;	
}
 
//======================================================================================================================================
function grabar_solo_HCE(){  
	global $conex;
    global $wbasedato;
    global $wusuario;
    global $wfecha;
    global $whora;

    global $wformulario;
    global $wnompro;
    global $wtipuso;
    global $wtipfor;
    global $wtipimp;
    global $walerta;
    global $wenccol;
	global $wencnfi;
	global $wencnco;
	global $windmen;
	global $wencvis;
    global $wencmax;
    global $wencfir;
    global $wenctra;
    global $wencenl;
    global $wenchol;
    global $wencmod;
    global $wencrhi;
    global $wencsca;
	global $wencoim;
	global $wencoco;
	global $wencien;
    global $westado;
	global $wnumero_servicios;
	     
	//Inserto un registro en la tabla HCE_000001 como encabezado   
	$q= " INSERT INTO ".$wbasedato."_000001 (   Medico       ,   Fecha_data ,   Hora_data,   encpro         ,   encdes      ,   enctus      ,   enctfo     ,   enctim     ,   encale     ,  enccol    ,   encnfi   ,  encnco    ,   encest     ,   encvis     ,   encmax     ,   encfir     ,   enctra     ,   encenl     ,  enchol    ,   encrhi     ,   encsca     ,  encoim    ,  encoco    ,  encien    , Seguridad        ) "
	   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$wformulario."','".$wnompro."' ,'".$wtipuso."' ,'".$wtipfor."','".$wtipimp."','".$walerta."',".$wenccol.",".$wencnfi.",".$wencnco.",'".$westado."','".$wencvis."','".$wencmax."','".$wencfir."','".$wenctra."','".$wencenl."',".$wenchol.",'".$wencrhi."','".$wencsca."',".$wencoim.",".$wencoco.",'".$wencien."', 'C-".$wusuario."') ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() . " tabla 000001");

	if ($wencmod=="")
	   $wencmod="off";
	
	crear_tabla_modificada();
	
	//Inserto el formulario en la opciones del menu
	$q= " INSERT INTO ".$wbasedato."_000009 (   Medico       ,   Fecha_data ,   Hora_data,   precod     ,   predes     , prenod ,     preurl         , preest,    premod     , seguridad        ) "
	   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$windmen."','".$wnompro."', 'off'  ,'F=".$wformulario."','on'   , '".$wencmod."', 'C-".$wusuario."') ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() . " tabla 000009-1");
	
	//2013-05-07
	/*//Inserto el formulario en la tabla de Formularios por Servicio
	$q= " INSERT INTO ".$wbasedato."_000037 (   Medico       ,   Fecha_data ,   Hora_data,   forcod     , Forser, Forest , seguridad        ) "
	   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$windmen."', '*'   , 'off'  , 'C-".$wusuario."') ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());*/
	
	$q= " INSERT INTO formulario (              Medico        ,   codigo         ,   nombre     , tipo, activo ) "
	   ."                 VALUES ('".strtolower($wbasedato)."','".$wformulario."','".$wnompro."', 'C' , 'A'    )";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() . " tabla formulario");
	
	echo "OK";
}
  
function crear_tabla_modificada(){
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   global $wformulario;
   
	//Esta tabla se crea con solo definir el encabezado de un formulario
	$q= " CREATE TABLE IF NOT EXISTS ".$wbasedato."_".$wformulario." ( Medico     varchar(8)     NOT NULL, "
	."                                                   Fecha_data date           NOT NULL, "
	."                                                   Hora_data  time           NOT NULL, "  
	."                                                   movpro     varchar(10)    NOT NULL, "
	."                                                   movcon     int            NOT NULL, "
	."                                                   movhis     varchar(20)    NOT NULL, "
	."                                                   moving     varchar(20)    NOT NULL, "
	."                                                   movtip     varchar(20)    NOT NULL, "
	."                                                   movdat     TEXT NOT NULL, "
	."                                                   movusu     varchar(20)    NOT NULL, "
	."                                                   Seguridad  varchar(10)    NOT NULL, "
	."                                                   id         bigint         NOT NULL auto_increment, primary key(id) )";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  
	  
	$q = "SHOW  INDEX  from ".$wbasedato."_".$wformulario;
	// $q = "SHOW  INDEX  from ".$wbasedato."_".$wformulario." WHERE key_name = 'PRIMARY' AND Column_name='id'" ;
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	
	// if ($num == 0){
	if ($num == 1){
		//Se crea el indice para la tabla anterior
		$q= " CREATE UNIQUE INDEX fechorproconhising_idx ON ".$wbasedato."_".$wformulario." ( Fecha_data , "
		."                                                                      Hora_data  , "
		."                                                                      movpro     , "
		."                                                                      movcon     , "
		."                                                                      movhis     , "
		."                                                                      moving     ) ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$q= " CREATE INDEX conhising_idx ON ".$wbasedato."_".$wformulario." ( movcon     , "
			."                                                                      movhis     , "
			."                                                                      moving     ) ";
		$res = mysql_query($q,$conex);
		
		$q= " CREATE INDEX hising_idx ON ".$wbasedato."_".$wformulario." ( movhis , moving ) ";
		$res = mysql_query($q,$conex);
	
	}
	  

	$q = " SELECT COUNT(*) "
	."   FROM root_000030 "
	."  WHERE Dic_usuario    = '".$wbasedato."'"
	."    AND Dic_formulario = '".$wformulario."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	if ($row[0] == 0)
	{
		//Inserto los registros de la tabla del 'Diccionario de datos de MATRIX   
		$q= " INSERT INTO root_000030 (Medico ,   Fecha_data ,   Hora_data,              Dic_usuario   ,   Dic_formulario , Dic_campo , Dic_descripcion    , Dic_comentario     , Seguridad )  "
		."                  VALUES ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0001'    , 'Cod. protocolo'   , 'codigo protocolo' , '".$wbasedato."'     ), "
		."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0002'    , 'Consecutivo Campo', 'consecutivo campo', '".$wbasedato."'     ), "
		."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0003'    , 'Historia'         , 'historia'  	      , '".$wbasedato."'     ), "
		."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0004'    , 'Ingreso'          , 'ingreso'  		  , '".$wbasedato."'     ), "
		."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0005'    , 'Tipo de Dato'     , 'tipo de dato'     , '".$wbasedato."'     ), "
		."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0006'    , 'Dato'             , 'dato'             , '".$wbasedato."'     ), "
		."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0007'    , 'Usuario'          , 'usuario'          , '".$wbasedato."'     )  ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() . " tabla root_30");
	}
}  
    
//======================================================================================================================================
function guardarNuevoFormulario( $wdatos ){
	global $conex;	  
	global $wbasedato;
	global $wusuario;
	global $wfecha;
	global $whora;

	global $wformulario;
	global $wnompro;
	global $wtipuso;
	global $wtipfor;
	global $wtipimp;
	global $walerta;
	global $wenccol;
	global $wencnfi;
	global $wencnco;
	global $windmen;
	global $wencvis;
	global $wencmax;
	global $wencfir;
	global $wenctra;
	global $wencenl;
	global $wenchol;
	global $wencmod;
	global $wencrhi;
	global $wencsca;
	global $wencoim;
	global $wencoco;
	global $wencien;
	global $westado;
	global $wnumero_servicios;
	
	$arr_servicios = array();
	
	$wesnodo = "";	
	$wdatos = str_replace("\\", "", $wdatos);
	$wdatos = str_replace("\"[", "[", $wdatos);
	$wdatos = str_replace("]\"", "]", $wdatos);	
	$wdatos = json_decode( $wdatos, true );
	foreach( $wdatos as $dato ){
		foreach( $dato['valores']  as $valor ){
			if( $valor['valor'] === true )
				$valor['valor'] = 'on';
			else if( $valor['valor'] === false )
				$valor['valor'] = 'off';
				
			${$valor['clave']} = utf8_decode( $valor['valor'] );		

			if(preg_match('/wservicio/i',$valor['clave']))
				array_push( $arr_servicios, $valor['valor'] );			
		}
	}

	ordenar_indices();
	
	//No se va a crear un formulario, sino un NODO
	if( $wesnodo == 'on' ){
		//Inserto el formulario en la opciones del menu
		$q= " INSERT INTO ".$wbasedato."_000009 (   Medico       ,   Fecha_data ,   Hora_data,   precod     ,   predes     , prenod ,     preurl         , preest,    premod     , seguridad        ) "
		   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$windmen."','".$wnompro."', 'on'  ,'on','on'   , '".$wencmod."', 'C-".$wusuario."') ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() . " tabla 000009-2");
			
		//2013-05-07
		/*//Inserto el formulario en la tabla de Formularios por Servicio
		$q= " INSERT INTO ".$wbasedato."_000037 (   Medico       ,   Fecha_data ,   Hora_data,   forcod     , Forser, Forest , seguridad        ) "
		   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$windmen."', '*'   , 'off'  , 'C-".$wusuario."') ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		*/
		return;
	}
	//Inserto el formulario en la tabla de Formularios por Servicio
	foreach($arr_servicios as $servicio){
		if( $servicio != '' ){
			$q= " INSERT INTO ".$wbasedato."_000037 (   Medico       ,   Fecha_data ,   Hora_data,   forcod     , Forser, Forest , seguridad        ) "
				."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$windmen."', '".$servicio."'   , 'on'  , 'C-".$wusuario."') ";
			$res = mysql_query($q,$conex);
		}
	}
	
	grabar_solo_HCE();
	//OJO - Aca comienza a grabar en las tablas MATRIX, por eso se separo en dos grabaciones, porque al modificar solo se modifica la definicion en HCE no en MATRIX.

	$query = "SELECT count(*) FROM det_formulario WHERE medico = '".strtolower($wbasedato)."' and codigo = '".$wformulario."'";
	$res = mysql_query($query,$conex);
	$num = mysql_num_rows($res);
	if( $num == 0 ){
		//Inserto los registros de la tabla de movimientos de cada formulario en la tabla 'det_formulario' de Matrix   
		$q= " INSERT INTO det_formulario (              Medico        ,   codigo     , campo   , descripcion, tipo, posicion, comentarios, activo ) "
		."                     VALUES ('".strtolower($wbasedato)."','".$wformulario."', '0001'  , 'movpro'   ,  0  , 1       , ''         , 'A' ),   "
		."                            ('".strtolower($wbasedato)."','".$wformulario."', '0002'  , 'movcon'   ,  1  , 2       , ''         , 'A' ),   "
		."                            ('".strtolower($wbasedato)."','".$wformulario."', '0003'  , 'movhis'   ,  0  , 3       , ''         , 'A' ),   "
		."                            ('".strtolower($wbasedato)."','".$wformulario."', '0004'  , 'moving'   ,  0  , 4       , ''         , 'A' ),   "
		."                            ('".strtolower($wbasedato)."','".$wformulario."', '0005'  , 'movtip'   ,  0  , 5       , ''         , 'A' ),   "
		."                            ('".strtolower($wbasedato)."','".$wformulario."', '0006'  , 'movdat'   ,  0  , 6       , ''         , 'A' ),   "
		."                            ('".strtolower($wbasedato)."','".$wformulario."', '0007'  , 'movusu'   ,  0  , 7       , ''         , 'A' )    ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() . " tabla det_formulario");
	}


	//Esta tabla se crea con solo definir el encabezado de un formulario
	$q= " CREATE TABLE IF NOT EXISTS ".$wbasedato."_".$wformulario." ( Medico     varchar(8)     NOT NULL, "
	."                                                   Fecha_data date           NOT NULL, "
	."                                                   Hora_data  time           NOT NULL, "  
	."                                                   movpro     varchar(10)    NOT NULL, "
	."                                                   movcon     int            NOT NULL, "
	."                                                   movhis     varchar(20)    NOT NULL, "
	."                                                   moving     varchar(20)    NOT NULL, "
	."                                                   movtip     varchar(20)    NOT NULL, "
	."                                                   movdat     TEXT NOT NULL, "
	."                                                   movusu     varchar(20)    NOT NULL, "
	."                                                   Seguridad  varchar(10)    NOT NULL, "
	."                                                   id         bigint         NOT NULL auto_increment, primary key(id) )";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$q = "SHOW  INDEX  from ".$wbasedato."_".$wformulario." WHERE key_name = 'fechorproconhising_idx'" ;
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	if($num == 0)
	{
		//Se crea el indice para la tabla anterior
		$q= " CREATE UNIQUE INDEX fechorproconhising_idx ON ".$wbasedato."_".$wformulario." ( Fecha_data , "
						."                                                                      Hora_data  , "
						."                                                                      movpro     , "
						."                                                                      movcon     , "
						."                                                                      movhis     , "
						."                                                                      moving     ) ";
						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}
	
	

	//2016-04-18
	$q = "SHOW  INDEX  from ".$wbasedato."_".$wformulario." WHERE key_name = 'conhising_idx'" ;
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	if($num == 0)
	{
		$q= " CREATE INDEX conhising_idx ON ".$wbasedato."_".$wformulario." ( movcon     , "
			."                                                                      movhis     , "
			."                                                                      moving     ) ";
		$res = mysql_query($q,$conex);
	}

	//2018-09-06
	$q = "SHOW  INDEX  from ".$wbasedato."_".$wformulario." WHERE key_name = 'hising_idx'" ;
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	if($num == 0)
	{
		$q= " CREATE INDEX hising_idx ON ".$wbasedato."_".$wformulario." ( movhis, moving ) ";
		$res = mysql_query($q,$conex);
	}
	


	//ESTO YA LO INSERTA LA FUNCION grabar_solo_HCE()
	//Inserto los registros de la tabla del 'Diccionario de datos de MATRIX   
	/*$q= " INSERT INTO root_000030 (Medico ,   Fecha_data ,   Hora_data,              Dic_usuario   ,   Dic_formulario , Dic_campo , Dic_descripcion    , Dic_comentario     , Seguridad )  "
	."                  VALUES ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0001'    , 'Cod. protocolo'   , 'codigo protocolo' , '".$wbasedato."'     ), "
	."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0002'    , 'Consecutivo Campo', 'consecutivo campo', '".$wbasedato."'     ), "
	."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0003'    , 'Historia'         , 'historia'  	      , '".$wbasedato."'     ), "
	."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0004'    , 'Ingreso'          , 'ingreso'  		  , '".$wbasedato."'     ), "
	."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0005'    , 'Tipo de Dato'     , 'tipo de dato'     , '".$wbasedato."'     ), "
	."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0006'    , 'Dato'             , 'dato'             , '".$wbasedato."'     ), "
	."                         ('root' ,'".$wfecha."' ,'".$whora."','".strtolower($wbasedato)."','".$wformulario."', '0007'    , 'Usuario'          , 'usuario'          , '".$wbasedato."'     )  ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	*/
}

/***
 * Funcion dedicada a borrar consecutivos de tablas que se crearon y que por error no se creó la opción de formulario
 * */
function limpiarIndices(){
	global $conex;
	global $wbasedato;

	$result = array();

	$query = "SELECT h.Encpro FROM {$wbasedato}_000001 h LEFT OUTER JOIN {$wbasedato}_000009 h2 ON concat('F=',h.Encpro) = h2.Preurl WHERE h2.Preurl is NULL";
	$res = mysql_query($query,$conex);
	$num = mysql_num_rows($res);

	if ( $num > 0) 
	{
		while( $row = mysql_fetch_assoc( $res, MYSQL_ASSOC ) )
		{
			$q1 = "DELETE FROM {$wbasedato}_000001 WHERE Encpro = '{$row['Encpro']}'";
			$rq1 = mysql_query($q1,$conex);

			$q2 = "SHOW  INDEX  from {$wbasedato}_{$row['Encpro']}";
			$rq2 = mysql_query($q2,$conex);
			$num = mysql_num_rows($rq2);
			if ( $num > 0) 
			{
				$q3 = "DROP TABLE IF EXISTS {$wbasedato}_{$row['Encpro']}";
				$rq3 = mysql_query($q3,$conex);
			}

			$q4 = "DELETE FROM root_000030 WHERE Dic_Usuario = '{$wbasedato}' AND Dic_Formulario = '{$row['Encpro']}'";
			$rq4 = mysql_query($q4,$conex);

			$q5 = "DELETE FROM formulario WHERE medico = '{$wbasedato}' AND codigo = '{$row['Encpro']}'";
			$rq5 = mysql_query($q5,$conex);

			$q6 = "DELETE FROM det_formulario WHERE medico = '{$wbasedato}' AND codigo = '{$row['Encpro']}'";
			$rq6 = mysql_query($q6,$conex);
		}
	}
}

    
//=======================================================================================================================================
function modificarEncabezado( $wdatos ){
	global $conex;	  
	global $wbasedato; 
	global $wusuario;
	global $wfecha;
	global $whora;

	global $wformulario;
	global $windmen;
	global $wnompro;
	global $wtipuso;
	global $wtipfor;
	global $wtipimp;
	global $walerta;
	global $wenccol;
	global $wencnfi;
	global $wencnco;
	global $wencvis;
	global $wencmax;
	global $wencfir;
	global $wenctra;
	global $wencenl;
	global $wenchol;
	global $wencmod;
	global $wencrhi;
	global $wencsca;
	global $wencoim;
	global $wencoco;
	global $westado;	
	global $wnumero_servicios;
	global $wencien;
	
	$wdatos = str_replace("\\", "", $wdatos);
	$wdatos = str_replace("\"[", "[", $wdatos);
	$wdatos = str_replace("]\"", "]", $wdatos);
	$wdatos = json_decode( $wdatos, true );
	
	$indice_viejo = "";
	

	$arr_servicios = array();
	foreach( $wdatos as $dato ){
		$indice_viejo = $dato['indice_viejo'];
		foreach( $dato['valores']  as $valor ){
			if( $valor['valor'] === true )
				$valor['valor'] = 'on';
			else if( $valor['valor'] === false )
				$valor['valor'] = 'off';
			${$valor['clave']} = utf8_decode( $valor['valor'] );
			
			if(preg_match('/wservicio/i',$valor['clave']))
				array_push( $arr_servicios, $valor['valor'] );
		}
	}
			
	$q = "DELETE FROM ".$wbasedato."_000037 "		
	   ."  WHERE forcod = '".$indice_viejo."'";	   
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   
	foreach($arr_servicios as $servicio){
		if( $servicio != '' ){
			$q= " INSERT INTO ".$wbasedato."_000037 (   Medico       ,   Fecha_data ,   Hora_data,   forcod     , Forser, Forest , seguridad        ) "
				."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$indice_viejo."', '".$servicio."'   , 'on'  , 'C-".$wusuario."') ";
			$res = mysql_query($q,$conex);
		}
	}
	
		
		ordenar_indices($indice_viejo);
   
   $q = " UPDATE ".$wbasedato."_000001 "
		." SET  encdes = '".$wnompro."',   enctus = '".$wtipuso."',   enctfo = '".$wtipfor."',   enctim = '".$wtipimp."',   encale = '".$walerta."',
				enccol = ".$wenccol.",   encnfi = ".$wencnfi.",  encnco = ".$wencnco.",   encest = '".$westado."',   encvis = '".$wencvis."',   
				encmax = '".$wencmax."',   encfir = '".$wencfir."',   enctra = '".$wenctra."',   encenl = '".$wencenl."',  enchol = ".$wenchol.",
				encrhi = '".$wencrhi."',   encsca = '".$wencsca."',  encoim = ".$wencoim.",  encoco = ".$wencoco.",  Encien = '".$wencien."'  "
	   ."WHERE  encpro = '".$wformulario."'";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   //OJO
    /*$q = "UPDATE ".$wbasedato."_000009 "
		." SET precod = '".$windmen."',   predes = '".$wnompro."', prenod = 'off', preest = '".$westado."',  premod = '".$wencmod."'  "
	   ."  WHERE preurl = 'F=".$wformulario."'"
	   ."    AND precod = '".$indice_viejo."'";
*/
		$q = "UPDATE ".$wbasedato."_000009 "
			." SET predes = '".$wnompro."', prenod = 'off', preest = 'on',  premod = '".$wencmod."'  "
		   ."  WHERE preurl = 'F=".$wformulario."'"
		   ."    AND precod = '".$windmen."'";
	   
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
     
	//El siguiente query ya se ejecuta en la funcion ordenar_indices
   /*$q = " UPDATE ".$wbasedato."_000037 "
			." SET forcod = '".$windmen."', Forser = '*', Forest = 'off'  "
		   ."  WHERE forcod = '".$indice_viejo."'" ;
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());*/

     
    $q = " UPDATE formulario "
		." SET  nombre = '".$wnompro."' "
	   ."  WHERE medico = '".$wbasedato."'"
	   ."    AND codigo = '".$wformulario."'";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   
   //grabar_solo_HCE();
} 

function ordenar_indices( $indice_viejo = -1 ){
	global $conex;
	global $wbasedato;
	global $wusuario;

	global $wformulario;
	global $windmen;
	global $wnompro;
	global $wtipuso;
	global $wtipfor;
	global $wtipimp;
	global $walerta;
	global $wenccol;
	global $wencnfi;
	global $wencnco;
	global $wencvis;
	global $wencmax;
	global $wencfir;
	global $wenctra;
	global $wencenl;
	global $wenchol;
	global $wencmod;
	global $wencrhi;
	global $wencsca;
	global $wencoim;
	global $wencoco;
	global $westado; 
	global $wencien; 
	
	if( $indice_viejo != $windmen ){  //Si vamos a cambiar el indice del formulario
		
		$windiceaux = $indice_viejo;
		
		$subirenelmismonivel = false;
		$cambioIndiceViejo = "";
		
		$q3 = "SELECT  * "
			."   FROM  ".$wbasedato."_000009 "
			."  WHERE  precod = '".$windmen."' ";
		$res3 = mysql_query($q3,$conex);
		$num3 = mysql_num_rows($res3);
		
		//Existe algun formulario con el indice nuevo?
		if ( $num3 > 0 ){
			$indice = $windmen;
			if( strlen( $windmen ) > 2  ){
				$indice = substr($windmen, 0, -2);			
			}
			$indauxv = $indice_viejo;
			
			//2013-09-24
			if( $indice_viejo != -1 ){
				//Se quita en codigo viejo y se le pone "aux" para que no se presenten problemas de codigos duplicados
				$windiceaux = "aux";			
				$q1 = "UPDATE ".$wbasedato."_000009 SET precod = REPLACE(precod, '".$indice_viejo."', '".$windiceaux."') WHERE precod REGEXP '^".$indice_viejo."'";
				$res1 = mysql_query($q1,$conex);
				$q2 = "UPDATE ".$wbasedato."_000021 SET rararb = REPLACE(rararb, '".$indice_viejo."', '".$windiceaux."') WHERE rararb REGEXP '^".$indice_viejo."'";
				$res2 = mysql_query($q2,$conex);
				$q3 = "UPDATE ".$wbasedato."_000037 SET forcod = REPLACE(forcod, '".$indice_viejo."', '".$windiceaux."') WHERE forcod REGEXP '^".$indice_viejo."'";
				$res3 = mysql_query($q3,$conex);
				if( strlen( $indauxv ) > 2  ){
					$indauxv = substr($indauxv, 0, -2);			
				}
				if( $indice == $indauxv && intval( $indice_viejo ) < intval( $windmen ) ){
					$subirenelmismonivel = true;
				}
			}
			
			//Se buscan todos los formularios o nodos que comiencen con el prefijo
			$q4 = "SELECT precod as codigo "
				."   FROM ".$wbasedato."_000009 "
				."  WHERE precod regexp '^".$indice."'";
				//."    AND predes != '".$wnompro."' "			
				//." ORDER BY 1 desc";
			if( $subirenelmismonivel == false )
				$q4.= " ORDER BY 1 desc";
			else
				$q4.= " ORDER BY 1 asc";
				
			$res4 = mysql_query($q4,$conex);
			$padre_indiceViejo = "";
			if( strlen( $indice_viejo ) > 2  ){
				$padre_indiceViejo = substr($indice_viejo, 0, -2);
			}
			while( $row4 = mysql_fetch_assoc($res4) ){
				$codigo = intval($row4['codigo']);					
				if( $row4['codigo'] == $padre_indiceViejo ){
					$cambioIndiceViejo = $codigo;
				}
				if( $subirenelmismonivel == false ){
					if( $codigo >= intval($windmen) && strlen($row4['codigo']) == strlen($windmen) ){
						$q1 = "UPDATE ".$wbasedato."_000009 SET precod = REPLACE(precod, '".$codigo."', '".($codigo+1)."') WHERE precod REGEXP '^".$codigo."'";
						$res1 = mysql_query($q1,$conex);
						$q2 = "UPDATE ".$wbasedato."_000021 SET rararb = REPLACE(rararb, '".$codigo."', '".($codigo+1)."') WHERE rararb REGEXP '^".$codigo."'";
						$res2 = mysql_query($q2,$conex);
						$q3 = "UPDATE ".$wbasedato."_000037 SET forcod = REPLACE(forcod, '".$codigo."', '".($codigo+1)."') WHERE forcod REGEXP '^".$codigo."'";
						$res3 = mysql_query($q3,$conex);
						if( $row4['codigo'] == $padre_indiceViejo ) $cambioIndiceViejo++;
					}
				}else{
					if( $codigo <= intval($windmen) && $codigo > intval($indice_viejo) && strlen($row4['codigo']) == strlen($windmen) ){
						$q1 = "UPDATE ".$wbasedato."_000009 SET precod = REPLACE(precod, '".$codigo."', '".($codigo-1)."') WHERE precod REGEXP '^".$codigo."'";
						$res1 = mysql_query($q1,$conex);
						$q2 = "UPDATE ".$wbasedato."_000021 SET rararb = REPLACE(rararb, '".$codigo."', '".($codigo-1)."') WHERE rararb REGEXP '^".$codigo."'";
						$res2 = mysql_query($q2,$conex);
						$q3 = "UPDATE ".$wbasedato."_000037 SET forcod = REPLACE(forcod, '".$codigo."', '".($codigo-1)."') WHERE forcod REGEXP '^".$codigo."'";
						$res3 = mysql_query($q3,$conex);
						if( $row4['codigo'] == $padre_indiceViejo ) $cambioIndiceViejo--;
					}
				}
			}
		}
		//ESTO INDICA QUE SE ESTA HACIENDO UNA MODIFICACION DEL INDICE
		//IMPLICA:
		//1. ACTUALIZAR TODOS LOS HIJOS CON EL NUEVO INDICE
		//2. DESAPARECER LOS "HUECOS" QUE PUDIERON HABER QUEDADO AL MOVER UN INDICE EXISTENTE
		if( $indice_viejo != -1 ){
			$q1 = "UPDATE ".$wbasedato."_000009 SET precod = REPLACE(precod, '".$windiceaux."', '".$windmen."') WHERE precod REGEXP '^".$windiceaux."'";
			$res1 = mysql_query($q1,$conex);
			$q2 = "UPDATE ".$wbasedato."_000021 SET rararb = REPLACE(rararb, '".$windiceaux."', '".$windmen."') WHERE rararb REGEXP '^".$windiceaux."'";
			$res2 = mysql_query($q2,$conex);
			$q3 = "UPDATE ".$wbasedato."_000037 SET forcod = REPLACE(forcod, '".$windiceaux."', '".$windmen."') WHERE forcod REGEXP '^".$windiceaux."'";
			$res3 = mysql_query($q3,$conex);
			//al haber actualizado queda un hueco...				
			$indice = $indice_viejo;
			if( strlen( $indice_viejo ) > 2  ){
				$indice = substr($indice_viejo, 0, -2); //$indice = 101
			}
			if( $cambioIndiceViejo != "" ){
				$indice_viejo = substr($indice_viejo, strlen($cambioIndiceViejo));  
				$indice_viejo = $cambioIndiceViejo."".$indice_viejo;
				$indice = $cambioIndiceViejo."";
			}
			
			if( $subirenelmismonivel == true )
				return;
			//Se buscan todos los formularios o nodos que comiencen con el prefijo del indice viejo
			$q4 = "SELECT precod as codigo "
				."   FROM ".$wbasedato."_000009 "
				."  WHERE precod regexp '^".$indice."'"
				//."    AND predes != '".$wnompro."' "
				." ORDER BY 1";
				
			//echo "<br>--->Busco los que comiencen en ".$indice."   ".$q4;
				
			$res4 = mysql_query($q4,$conex);	
			while( $row4 = mysql_fetch_assoc($res4) ){
				$codigo = intval($row4['codigo']);
				if( $codigo > intval($indice_viejo) && strlen($row4['codigo']) == strlen($indice_viejo) ){
					$q1 = "UPDATE ".$wbasedato."_000009 SET precod = REPLACE(precod, '".$codigo."', '".($codigo-1)."') WHERE precod REGEXP '^".$codigo."'";
					$res1 = mysql_query($q1,$conex);
					$q2 = "UPDATE ".$wbasedato."_000021 SET rararb = REPLACE(rararb, '".$codigo."', '".($codigo-1)."') WHERE rararb REGEXP '^".$codigo."'";
					$res2 = mysql_query($q2,$conex);
					$q3 = "UPDATE ".$wbasedato."_000037 SET forcod = REPLACE(forcod, '".$codigo."', '".($codigo-1)."') WHERE forcod REGEXP '^".$codigo."'";
					$res3 = mysql_query($q3,$conex);
				}
			}
		}
	}
}

//=======================================================================================================================================
function buscar_sgte_tabla(){
   global $conex;	  
   global $wbasedato;
   global $wformulario;
   global $wtabaux;	  
	  
   $q = " show tables ";
   $res = mysql_query($q,$conex);
   $num = mysql_num_rows($res);
	
   $wtabaux=0;
   for ($i=0;$i<$num;$i++)
    {
	 $row = mysql_fetch_array($res);
	 $wtab=explode("_",$row[0]);
	 
	 if (strtolower($wtab[0])==strtolower($wbasedato))   
	   {
	 	if (strval($wtab[1]) > strval($wtabaux) and strval($wtab[1]) > 50)
		   $wtabaux=strval($wtab[1]);
		  else
		     $wtabaux=50; 
	   }
	}
   //===========================================================================================================================
   $wformulario = leading_zero(($wtabaux+1), 6, 0);   
}
  
  
//=======================================================================================================================================
function consultar(){
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wok;
   global $wformulario;
   global $wnompro;
   global $wtipuso;
   global $wtipfor;
   global $wtipimp;
   global $walerta;
   global $wenccol;
   global $wencnfi;
   global $wencnco;
   global $windmen;
   global $wencvis;
   global $wencmax;
   global $wencfir;
   global $wenctra;
   global $wencenl;
   global $wenchol;
   global $wencmod;
   global $wencrhi;
   global $wencsca;
   global $wencoim;
   global $wencoco;
   global $westado;	  
   global $wencien;	  
   
   
   //Aca comienza la parte de despliegue de la pantalla (Mostrar) 
   if (isset($wformulario) && $wformulario != "")     
	{
	   $q = " SELECT Predes, Enctus, Enctim, Enctfo, Encale, Encest, Enccol, Encnfi, Encnco, Encvis, Encmax, Encfir, Enctra, Encenl, "
	       ."        Enchol, Encrhi, Encsca, Encoim, Encoco, Precod, premod, Encien " 
	       ."   FROM ".$wbasedato."_000001,".$wbasedato."_000009 "
	       ."  WHERE encpro = '".$wformulario."'"
		   ."    AND INSTR(preurl,encpro) > 0 "
		   ."    AND precod = '".$windmen."'";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);	
		    
	   $wnompro = $row[0];   //Nombre del formulario
	   $wtipuso = $row[1];   //Tipo de uso
	   $wtipimp = $row[2];   //Modo de Impresión
	   $wtipfor = $row[3];   //Tipo de formulario
	   $walerta = $row[4];   //Indica que es un formulario de Alertas (Solo debe existir 1 en toda la HCE)
	   $westado = $row[5];   //Estado
	   $wenccol = $row[6];   //Numero de columnas del formulario
	   $wencnfi = $row[7];   //Alto en pixeles
	   $wencnco = $row[8];   //Ancho en pixeles
	   $wencvis = $row[9];   //Visible en el Arbol
	   $wencmax = $row[10];  //Maximizable
	   $wencfir = $row[11];  //Se firma
	   $wenctra = $row[12];  //Solo por el tratante
	   $wencenl = $row[13];  //Enlace con otros software 
	   $wenchol = $row[14];  //Tiempo de holgura para firmar
	   $wencrhi = $row[15];  //Hace parte del Resumen de Historia para imprimir
	   $wencsca = $row[16];  //Imprime Firma Scaniada
	   $wencoim = $row[17];  //Orden en que se imprime en toda la HCE
	   $wencoco = $row[18];  //Orden cuando se consulta toda la HCE
	   $windmen = $row[19];  //Orden que ocupa en el Arbol
	   $wencmod = $row[20];  //
	   $wencien = $row[21];  //Muestra encabezado en impresion HCE
	   
		//imprimirFormulario();
	   
	   /*echo "<script language='Javascript'>";
	   echo "document.MaestroProtocolos.wformulario.value ='".$wformulario."'; ";
   	   echo "document.MaestroProtocolos.wnompro.value ='".$wnompro."'; ";
   	   echo "document.MaestroProtocolos.wtipuso.value ='".$wtipuso."'; ";
   	   echo "document.MaestroProtocolos.wtipfor.value ='".$wtipfor."'; ";
   	   echo "document.MaestroProtocolos.wtipimp.value='".$wtipimp."'; ";
         
	   if ($walerta=='on')
          echo "document.MaestroProtocolos.walerta.checked; ";
         else
            echo "document.MaestroProtocolos.walerta.unchecked; "; 
       	
	   echo "document.MaestroProtocolos.windmen.value='".$windmen."'; ";
       echo "document.MaestroProtocolos.wencnfi.value='".$wencnfi."'; ";
       echo "document.MaestroProtocolos.wencnco.value='".$wencnco."'; ";
       echo "document.MaestroProtocolos.wencmax.value='".$wencmax."'; ";
       echo "document.MaestroProtocolos.wencfir.value='".$wencfir."'; ";
       echo "document.MaestroProtocolos.wenctra.value='".$wenctra."'; ";
       echo "document.MaestroProtocolos.wencenl.value='".$wencenl."'; ";
       echo "document.MaestroProtocolos.wenchol.value='".$wenchol."'; ";
       if ($wencmod=='on')
          echo "document.MaestroProtocolos.wencmod.checked; ";
         else
            echo "document.MaestroProtocolos.walerta.unchecked; ";
       
       if ($westado=='on')
          echo "document.MaestroProtocolos.westado.value=checked; ";
         else
            echo "document.MaestroProtocolos.westado.unchecked; "; 
    		
       echo "document.MaestroProtocolos.wencrhi.value='".$wencrhi."'; ";
       echo "document.MaestroProtocolos.wencsca.value='".$wencsca."'; ";
	   echo "document.MaestroProtocolos.wencoim.value='".$wencoim."'; ";
       echo "document.MaestroProtocolos.wencoco.value='".$wencoco."'; ";
       echo "</script>";*/
	}     
}
 
  
   
function iniciar(){
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wok;
   global $wformulario;
   global $wnompro;
   global $wtipuso;
   global $wtipfor;
   global $wtipimp;
   global $walerta;
   global $wenccol;
   global $wencnfi;
   global $wencnco;
   global $windmen;
   global $wencvis;
   global $wencmax;
   global $wencfir;
   global $wenctra;
   global $wencenl;
   global $wenchol;
   global $wencmod;
   global $wencrhi;
   global $wencsca;
   global $wencoim;
   global $wencoco;
   global $westado;	  
   global $wencien;	  

   buscar_sgte_tabla();
   
   $wnompro="";
   $wenccol="";
   $wencnfi="950";
   $wencnco="600";
   $windmen="";
   $walerta="off";
   $westado="on";
}  

function consultarListaDeArbolParaIndice( $prefijo='1'){
	global $conex;	  
	global $wbasedato; 
	
	if( $prefijo == 'RETORNAR' || $prefijo=='')
		$prefijo = 1;
	
	$tamano=3;
	if( $prefijo != 1 && $tamano == 3 )
		$tamano = strlen($prefijo) + 2;
   
	$q= "SELECT Precod as codigo, Predes as descripcion, prenod as nodo"
	."	   FROM ".$wbasedato."_000009  "
	."    WHERE Preest = 'on' "
	."      AND precod like '".$prefijo."%' "
	."      AND length(precod) = ".$tamano." "
	." ORDER BY Precod	";	
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$result = array();
	
	while( $row = mysql_fetch_assoc($res) ){
		array_push( $result, $row );	
	}
	return $result;
}

function imprimirFormulario(){
	
global $conex;	  
global $wbasedato;
global $wbasecliame;
global $wbasemovhos;
global $wusuario;
global $wfecha;
global $whora;

global $wok;
global $wformulario;
global $wnompro;
global $wtipuso;
global $wtipfor;
global $wtipimp;
global $walerta;
global $wenccol;
global $wencnfi;
global $wencnco;
global $windmen;
global $wencvis;
global $wencmax;
global $wencfir;
global $wenctra;
global $wencenl;
global $wenchol;
global $wencmod;
global $wencrhi;
global $wencsca;
global $wencoim;
global $wencoco;
global $westado;  
global $wencien;  

$obligatorio = "<div class='obligatorio enlinea'><font color=0000FF>(*)</font></div>";
  
//echo "<table width='98%' height='322' border='0'>";
echo "<table>";
//=================================================================================================================
//CODIGO Y NOMBRE DEL PROTOCOLO
echo "<tr class=fila1>"; 
echo "<td colspan=1><b>C&oacute;digo</b>".$obligatorio."</td>";
echo "<td colspan=7><input type='text' name='wformulario' id='wformulario' value='".$wformulario."' disabled></td>";
echo "</tr>";
echo "<tr class=fila1>"; 
echo "<td colspan=1><b>Nombre</b>".$obligatorio."</td>";
echo "<td colspan=7><input name='wnompro' id='wnompro' type='text' size='130' value='".$wnompro."'></td>";
echo "</tr>";

//=================================================================================================================
//TIPO DE USO
$q = " SELECT tuscod, tusdes "
    ."   FROM ".$wbasedato."_000006 "
    ."  WHERE tusest = 'on' ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
$num = mysql_num_rows($res);
  
echo "<tr class=fila1>";
echo "<td colspan=1><b>Tipo de Uso</b></td>";
echo "<td colspan=7><SELECT name='wtipuso' id='wtipuso'>";
if (isset($wtipuso))   
{
	$wtipuso1 = explode("-",$wtipuso);

	$q = " SELECT tuscod, tusdes "
	."   FROM ".$wbasedato."_000006 "
	."  WHERE tuscod = '".$wtipuso1[0]."'"
	."    AND tusest = 'on' "
	."  ORDER BY 1 ";
	$res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row1= mysql_fetch_array($res1);

	echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]." </option>";   
}
for ($j=1;$j<=$num;$j++)
{ 
	$row = mysql_fetch_array($res);   
	echo "<OPTION>".$row[0]."-".$row[1]." </option>";
}
echo "</SELECT></td>";
echo "</tr>";
//=================================================================================================================


//=================================================================================================================
//TIPO DE FORMULARIO
$q = " SELECT tfocod, tfodes "
    ."   FROM ".$wbasedato."_000007 "
    ."  WHERE tfoest = 'on' ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
$num = mysql_num_rows($res);
  
echo "<tr class=fila1>";
echo "<td colspan=1><b>Tipo de Formulario</b></td>";
echo "<td colspan=7><SELECT name='wtipfor' id='wtipfor' >";
if (isset($wtipfor))   
{
	$wtipfor1 = explode("-",$wtipfor);

	$q = " SELECT tfocod, tfodes "
	."   FROM ".$wbasedato."_000007 "
	."  WHERE tfocod = '".$wtipfor1[0]."'"
	."    AND tfoest = 'on' "
	."  ORDER BY 1 ";
	$res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row1= mysql_fetch_array($res1);

	echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]." </option>";   
}
for ($j=1;$j<=$num;$j++)
{ 
	$row = mysql_fetch_array($res);   
	echo "<OPTION>".$row[0]."-".$row[1]." </option>";
}
echo "</SELECT></td>";
echo "</tr>";
//=================================================================================================================


//=================================================================================================================
//MODO DE IMPRESION
$q = " SELECT timcod, timdes "
    ."   FROM ".$wbasedato."_000008 "
    ."  WHERE timest = 'on' ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
$num = mysql_num_rows($res);
  
echo "<tr class=fila1>";
echo "<td colspan=1><b>Modo de Impresión</b></td>";
echo "<td colspan=7><SELECT name='wtipimp' id='wtipimp' >";
if (isset($wtipimp))   
{
	$wtipimp1 = explode("-",$wtipimp);

	$q = " SELECT timcod, timdes "
	."   FROM ".$wbasedato."_000008 "
	."  WHERE timcod = '".$wtipimp1[0]."'"
	."    AND timest = 'on' "
	."  ORDER BY 1 ";
	$res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row1= mysql_fetch_array($res1);

	echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]." </option>";   
}
for ($j=1;$j<=$num;$j++)
{ 
	$row = mysql_fetch_array($res);   
	echo "<OPTION>".$row[0]."-".$row[1]." </option>";
}
echo "</SELECT></td>";
echo "</tr>";
//=================================================================================================================


//=================================================================================================================
//ALERTAS, NRO DE COLUMNAS, Ancho Y Alto
echo "<tr class=fila1>";
echo "<td><b>Es de Alertas</b></td>";
echo "<td>";
if (isset($walerta) and $walerta=="on")
	echo "<input type='checkbox' name='walerta' id='walerta' CHECKED>";
else
    echo "<input type='checkbox' name='walerta' id='walerta'>";
echo "</td>";
echo "<td><b>Nro de Columnas</b>".$obligatorio."</td>";
echo "<td><input type='text' size='5' class='solonumeros' name='wenccol' id='wenccol' value='".$wenccol."'></td>";
echo "<td><b>Alto en Pixeles</b>".$obligatorio."</td>";
echo "<td><input type='text' size='5' class='solonumeros' name='wencnfi' id='wencnfi' value='".$wencnfi."'></td>";
echo "<td><b>Ancho en Pixeles</b>".$obligatorio."</td>";
echo "<td><input type='text' size='5' class='solonumeros' name='wencnco' id='wencnco' value='".$wencnco."'></td>";
echo "</tr>";
//=================================================================================================================


//=================================================================================================================
//Visible, Maximizada, Firma y Tratante
echo "<tr class=fila1>";
echo "<td><b>Visible en Arbol</b></td>";
echo "<td>";
if (isset($wencvis) and $wencvis=="on")
   echo "<input type='checkbox' name='wencvis' id='wencvis' CHECKED>";
  else
     echo "<input type='checkbox' name='wencvis' id='wencvis'>";
echo "</td>";

echo "<td><b>Maximizada</b></td>";
if (isset($wencmax) and $wencmax=="on")
   echo "<td><input type='checkbox' name='wencmax' id='wencmax' CHECKED></td>";
  else
     echo "<td><input type='checkbox' name='wencmax' id='wencmax'></td>";
   
echo "<td><b>Se Firma</b></td>";
if (isset($wencfir) and $wencfir=="on")
   echo "<td><input type='checkbox' name='wencfir'  id='wencfir' CHECKED></td>";
  else
     echo "<td><input type='checkbox' name='wencfir' id='wencfir' ></td>"; 
     
echo "<td><b>Solo Tratante</b></td>";
if (isset($wenctra) and $wenctra=="on")
   echo "<td><input type='checkbox' name='wenctra' id='wenctra' CHECKED></td>";
  else
     echo "<td><input type='checkbox' name='wenctra' id='wenctra'></td>"; 
echo "</tr>";
//=================================================================================================================


//=================================================================================================================
//ESTADO y INDICE EN EL MENU

$datos = consultarListaDeArbolParaIndice();
$options = "<option value=''>&nbsp;</option>";
$ultimo = "101";
foreach ($datos as $pos=>$dato){
	$disabled="";
	if( $dato['nodo'] != 'on' ){
		$disabled = "disabled='disabled'";
	}
	$options.="<option value='".$dato['codigo']."' ".$disabled.">".$dato['codigo']." - ".$dato['descripcion']."</option>";
	$ultimo = $dato['codigo'];
}
$ultimo = $ultimo+1;
if( $windmen != "" )
	$ultimo = "";
echo "<tr class=fila1>";
echo "<td><b>Indice en el Menu</b></td>";
echo "<td><input type='text' size='10' name='windmen' id='windmen' value='".$windmen."' disabled>";
echo " --> <span id='indice_menu_nuevo'>".$ultimo."</span>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<img src='../../images/medical/hce/menos.png' onclick='bajar_indice()' />";
echo "&nbsp;";
echo "<img src='../../images/medical/hce/mas.png' onclick='subir_indice()' />";
echo "<br>";
echo "<select onchange='consultarOpcionesParaIndice()' id='indice_menu_select'>".$options."</select>";
echo "</td>";

echo "<td><b>Enlace con Otro software</b></td>";
echo "<td>";
if (isset($wencenl) and $wencenl=="on")
   echo "<input type='checkbox' name='wencenl' id='wencenl' CHECKED>";
  else
     echo "<input type='checkbox' name='wencenl' id='wencenl'>";
echo "</td>";
     
echo "<td><b>Tiempo para Firmar (En días)</b>".$obligatorio."</td>";
echo "<td>";
echo "<input type='text' class='solonumeros' size='5' name='wenchol' id='wenchol' value='".$wenchol."'>";
echo "</td>";
echo "<td><b>Es modal</b></td>";
echo "<td>";
if (isset($wencmod) and $wencmod=="on")
   echo "<input type='checkbox' name='wencmod' id='wencmod' CHECKED>";
  else
     echo "<input type='checkbox' name='wencmod' id='wencmod'>";
echo "</td>";
echo "</tr>";


echo "<tr class=fila1>";

echo "<td><b>Se imprime en<br>Resumen de Hria</b></td>";
echo "<td>";
if (isset($wencrhi) and $wencrhi=="on")
   echo "<input type='checkbox' name='wencrhi' id='wencrhi' CHECKED>";
  else
     echo "<input type='checkbox' name='wencrhi' id='wencrhi'>";
echo "</td>";

echo "<td><b>Imp.Firma Escaneada</b></td>";
echo "<td>";
if (isset($wencsca) and $wencsca=="on")
   echo "<input type='checkbox' name='wencsca' id='wencsca' CHECKED>";
  else
     echo "<input type='checkbox' name='wencsca' id='wencsca'>";
echo "</td>";

echo "<td><b>Orden Imprimir</b>".$obligatorio."</td>";
echo "<td>";
	
if (isset($wencoim) and $wencoim!="")
   echo "<input type='text' class='solonumeros' name='wencoim' id='wencoim' size='5' value='".$wencoim."'>";
  else
     echo "<input type='text' class='solonumeros' name='wencoim' id='wencoim' size='5'>";
echo "</td>";

echo "<td><b>Orden Consulta</b>".$obligatorio."</td>";
echo "<td>";
if (isset($wencoco) and $wencoco!="")
   echo "<input type='text' class='solonumeros' name='wencoco' id='wencoco' size='5' value='".$wencoco."'>";
  else
     echo "<input type='text' class='solonumeros' name='wencoco' id='wencoco' size='5'>";
echo "</td>";
echo "</tr>";

echo "<tr class=fila1>";

echo "<td><b>Estado</b></td>";
echo "<td colspan=7>";
if (isset($westado) and $westado=="on")
   echo "<input type='checkbox' name='westado' id='westado' CHECKED>";
  else
     echo "<input type='checkbox' name='westado' id='westado'>";
echo "</td>";
echo "</tr>";

echo "<tr class=fila1>";
echo "<td><b>Es nodo</b></td>";
echo "<td colspan=1>";
	echo "<input type='checkbox' name='wesnodo' id='wesnodo' onclick='configurarEsNodo(this)' disabled/>";
echo "</td>";

$checkImprimeEncabezado = "";
if(isset($wencien) && $wencien == "on")
{
	$checkImprimeEncabezado = "checked";
}
echo "<td><b>Se imprime encabezado</b></td>";
echo "<td colspan=5>";
	echo "<input type='checkbox' name='wencien' id='wencien' ".$checkImprimeEncabezado.">";
echo "</td>";
echo "</tr>";


//***********************SERVICIOS PARA LOS QUE ES PERMITIDO VISUALIZAR EL FORMULARIO***************************************

/*echo "<tr class=fila1>";
echo "<td colspan=8>";
echo "<b>Servicios para los que es permitido visualizar el formulario</b>";
echo "</td>";
echo "</tr>";*/
echo "<tr class=encabezadotabla>";
echo "<td colspan=8 align='center'>";
echo "Lista de Servicios";	
echo "</td>";
echo "</tr>";

echo "<tr class=fila1>";
echo "<td colspan=8 align='center'>";
echo "<select id='select_servicios' onchange='agregar_servicios()'> ";
echo "<option value=''>Seleccione</option>";
echo "<option value='*'>* - TODOS LOS HOSPITALARIOS</option>";
/*$q = " SELECT seicod as servicio, seides as nombre "	//2013-08-20
	."   FROM ".$wbasecliame."_000001 "
	."  WHERE seiest = 'on' ";*/
	
//centros de costo que sean de urgencias u hospitalarios o de ayuda o de cirugia o de ingreso o de admisiones
$q = " SELECT Ccoseu as servicio, Cconom as nombre "
	."   FROM ".$wbasemovhos."_000011 "
	."  WHERE Ccoest = 'on' "
	."    AND ( Ccourg = 'on' "
	."     OR Ccohos = 'on' "
	."     OR Ccoayu = 'on' "
	."     OR Ccocir = 'on' "
	."     OR Ccoing = 'on' "
	."     OR Ccoadm = 'on' )"
	."    AND Ccoseu != '' "
	."    AND Ccoseu != 'NO APLICA' "
	."	  GROUP BY Ccocod "
	."    ORDER BY 2 ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
$num = mysql_num_rows($res); 
if( $num > 0 ){
	$i = 0;
	while( $row = mysql_fetch_assoc($res) ){
		echo "<option value='".$row['servicio']."' >".$row['servicio']." - ".$row['nombre']."</option>";
	}
}
echo "</select>";
echo "<br><br>";
echo "</td>";
echo "</tr>";

echo "<tr class=fila1>";
echo "<td colspan=8 align='center'>";
echo "<div id='lista_servicios_agregados' style='width:300px;'>";
if( !isset($wnumero_servicios))
	$wnumero_servicios = 0;

	/*$q = " SELECT forser as servicio, seides as nombre "	//2013-08-20
		."   FROM ".$wbasedato."_000037 LEFT JOIN  ".$wbasecliame."_000001 ON (seicod = forser and seiest = 'on')"
		."  WHERE forcod = '".$windmen."' ";	*/
		
	$q = " SELECT forser as servicio, Cconom as nombre "
		."   FROM ".$wbasedato."_000037 LEFT JOIN  ".$wbasemovhos."_000011 ON (Ccoseu = forser and Ccoest = 'on')"
		."  WHERE forcod = '".$windmen."' ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	if( $num > 0 ){
		$i = 0;
		while( $row = mysql_fetch_assoc($res) ){
			echo "<div align='left'>";
			echo "<input type='checkbox' checked value='".$row['servicio']."' name='wservicio".($i+1)."' id='wservicio".($i+1)."' onclick='quitarServicio(this)' class='servicio' />";
			if( $row['servicio'] == '*' ) $row['nombre'] = "TODOS LOS HOSPITALARIOS";
			echo "<span>".$row['servicio']." - ".$row['nombre']."</span><br>";
			echo "</div>";
			$wnumero_servicios++;		
			$i++;
		}
	}else{
		$i = 0;
		echo "<div align='left'>";
		echo "<input type='checkbox' checked value='*' name='wservicio".($i+1)."' id='wservicio".($i+1)."' onclick='quitarServicio(this)' class='servicio' />";
		echo "<span>* - TODOS LOS HOSPITALARIOS</span><br>";
		echo "</div>";
		$wnumero_servicios++;
	}
	echo "<input type='hidden' name='wnumero_servicios' id='wnumero_servicios' value='".$wnumero_servicios."' />";
echo "</div>";
echo "</td>";
echo "</tr>";
//=================================================================================================================
echo "</table>";	
}
//=================================================================================================================================
//***************************** T E R M I N A   L A   D E F I N I C I O N   D E   F U N C I O N E S *******************************
//=================================================================================================================================

echo "<form name='MaestroProtocolos' method='post' action=''>";
echo "<div align='center'><font color='#000099' size='5'><strong>&nbsp</strong></font><br></div>";


//***************************************************************************************************************
//*********   A C A   C O M I E N Z A   E L   B L O Q U E   P R I C I P A L   D E L   P R O G R A M A   *********
//***************************************************************************************************************

global $Modificar;

//Se evalua el boton presionado
/*if (isset($Grabar) or isset($Modificar) or isset($Consultar) or isset($Iniciar))
{
	if (isset($Grabar))
	{
		validar_campos();
		if ($wok)
		{
			grabar($wok);
			?>	    
			<script> alert ("El Registro fue Grabado"); </script>
			<?php
		} 
	}	
          
	if (isset($Modificar))
	{
		validar_campos();
		if ($wok==true) 
		{ 
			modificar(); 
			?>	    
			<script> alert ("El Registro fue Modificado"); </script>
			<?php
		} 
	}
       
	if (isset($Consultar))
	{ consultar(); }    

	if (isset($Iniciar))
	{ iniciar(); }   
} //fin del if (Grabar or Modificar or Consultar or Borrar)
else
{*/
	consultar();   //Entra por aca la primera vez que se ingresa a la pantalla
//}    
echo "<div id='formulario_encabezado'>";
imprimirFormulario();
echo "</div>";

echo "<div align='center'>";   
/*echo "<p>";
echo "<input type='submit' name='Iniciar'   value='Iniciar'>";
echo "&nbsp&nbsp;|&nbsp"; 
echo "<input type='submit' name='Grabar'    value='Grabar'>";
echo "&nbsp;|&nbsp"; 
echo "<input type='submit' name='Modificar' value='Modificar'>";
echo "&nbsp;|&nbsp"; 
echo "<input type='submit' name='Consultar' value='Consultar'>";
echo "&nbsp;&nbsp;|&nbsp;&nbsp";
echo "<input type='submit' name='Salir' value='Salir' onclick='cerrarVentana()'>";
echo "</p>";*/
echo "<input type='button' id='btn_nuevo_encabezado' onclick='nuevoEncabezado()' value='Nuevo'>";
echo "&nbsp;&nbsp;|&nbsp;&nbsp";
echo "<input type='button' id='btn_guardar_encabezado' onclick='guardarEncabezado()'  value='Guardar'>";
/*echo "&nbsp;&nbsp;|&nbsp;&nbsp";
echo "<input type='button' id='btn_limpiar_encabezado' onclick='limpiarEncabezado()'  value='Limpiar'>";*/
echo "</div>";

echo "<div id='msjEspere' style='display:none;'>";
echo '<br>';
echo "<img src='../../images/medical/ajax-loader5.gif'/>";
echo "<br><br> Por favor espere un momento ... <br><br>";
echo '</div>';
echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."' />";
echo "<input type='hidden' id='resetear_lista_formularios' value='configuracion.php?accion=F&ok=0&wemp_pmla=".$wemp_pmla."' />";
?>
</form>
</body>
</html>
