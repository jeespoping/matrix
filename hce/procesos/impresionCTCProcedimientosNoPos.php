<?php
include_once("conex.php");  header("Content-Type: text/html;charset=ISO-8859-1"); ?>

<title>IMPRESION CTC PROCEDIMIENTOS NO POS</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
 <script src="../../../include/root/print.js" type="text/javascript"></script>
 
 <script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<!-- <script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script> -->	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<!-- <script type="text/javascript" src="../../../include/root/ui.datepicker.js"></script>-->
<script type="text/javascript" src="../../../include/root/burbuja.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
 
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		
 
 <script src="../../hce/procesos/ordenes.js?v=<?=md5_file('ordenes.js');?>" type="text/javascript"></script>

<style type="text/css">
.texto{

font-size:11px;

}

.procCancelado{background:#FFB5B5;font-size: 10pt;}
.detalles{
	font-family: verdana;
	font-size: 7pt;
	background-color: #bfbfbf;
	border-radius:3px;
	color: #0033FF;
	font-weight: bold;
	text-decoration: underline;
	cursor:pointer;
}

.presentacionMipres
{
	font-family: verdana;
	font-weight: bold;
	color:#0033ff;
}
</style>

<script>

//document.oncontextmenu = function(){return false}


window.onunload=function(){
window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
}


// $(document).ready(function () {
	// $("body").keypress(function (e) {
		
		// //Enter
		// if (e.which == 13) {
		 // return false;
		// }
		
		// //Tecla p
		// if (e.which == 112) {
		 // return false;
		// }
		
		// //F10
		// if (e.which == 0) {
		 // return false;
		// }
			
		// //Tecla s
		// if (e.which == 115) {
		 // return false;
		// }
		
		// //Tecla a
		// if (e.which == 97) {
		 // return false;
		// }
		
		// //Tecla c
		// if (e.which == 99) {
		 // return false;
		// }
	// });
// });

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




	
function verPrescripcionPorPacienteFec(historia,ingreso,tipoDocumento,documento,fecha)
{
	// alert("hey"+historia+" "+ingreso+" "+tipoDocumento+" "+documento+" "+fecha);
	
	// tipoDocumento="CC";
	// documento="1105614241";
	// fecha="2017-07-28";
	
	
	
	$.blockUI({ message: $('#msjEspere'),
		css: {
			top:  '35%', 
			left: '40%', 
			width: '30%',
			height: '20%',
			overflow: 'auto',
			cursor: 'auto'
		}
	});
	
	$.post("CTCmipres.php",
	{
		consultaAjax 	: '',
		accion			: 'consultarPrescripcionPacFec',
		historia		: historia,
		ingreso			: ingreso,
		tipoDocumento	: tipoDocumento,
		documento		: documento,
		fechaMipres		: fecha,
		general			: "off",
		wemp_pmla		: $('#wemp_pmla').val(),
		hora			: "",
		origen			: "reporteCTC"
	}
	, function(data) {
		
		$.unblockUI();
		
		// $( "#divReporteMipres" ).html(data);
		// console.log(data.length);
		
		if(data.length>0)
		{
			arrayCodPrescripcionMipres = data;
			// crear parametro, si no tiene consecutivo que muestre todo lo que esta guardado en la tabla
			
			abrirModalMipres(historia,ingreso,tipoDocumento,documento,fecha,arrayCodPrescripcionMipres)
		}
		else
		{
			jAlert("No se encontraron prescripciones en Mipres","Alerta");
		}
		

	},'json');
	
}


function iniciarTooltip(tooltip)
{
	//Tooltip
	var cadenaTooltip = $("#"+tooltip).val();
	
	cadenaTooltip = cadenaTooltip.split("|");
	
	for(var i = 0; i < cadenaTooltip.length-1;i++)
	{
		$( "#"+cadenaTooltip[i] ).tooltip();
	}
	
}

function abrirModalMipres(historia,ingreso,tipoDocumento,documento,fecha,codPrescMipres)
{
	$.post("CTCmipres.php",
	{
		consultaAjax 	: '',
		accion			: 'pintarPrescripcionMipres',
		wemp_pmla:				$('#wemp_pmla').val(),
		historia: 				historia,
		ingreso: 				ingreso,
		tipoDocumento: 			tipoDocumento,
		documento: 				documento,
		fechaMipres: 			fecha,
		general: 				"off",
		codPrescMipres: 		codPrescMipres,
		reporte:				"ctcProcedimientos"
	}
	, function(data) {
		
		// clearInterval(timer);
		
		$( "#dvAuxModalMipres" ).html( data );
		
				
		var canWidth = $(window).width()*0.8;
		if( $( "#dvAuxModalMipres" ).width()-50 < canWidth )
			canWidth = $( "#dvAuxModalMipres" ).width();

		var canHeight = $(window).height()*0.8;;
		if( $( "#dvAuxModalMipres" ).height()-50 < canHeight )
			canHeight = $( "#dvAuxModalMipres" ).height();

	
		$.blockUI({ message: $('#modalMipres'),
		css: {
			overflow: 'auto',
			cursor	: 'auto',
			width	: "95%",
			height	: "80%",
			left	: "2.5%",
			top		: '100px',
		} });
		
		
		
		
		iniciarTooltip("tooltipEstadoPrescripcion");
		iniciarTooltip("tooltipJMPro");
		iniciarTooltip("tooltipJMDis");
		iniciarTooltip("tooltipJMSer");
		iniciarTooltip("tooltipProcedimientosUtilizados");
		iniciarTooltip("tooltipProcedimientosDescartados");
		iniciarTooltip("tooltipProcedimientosDetalles");
		iniciarTooltip("tooltipDispositivosDetalles");
		iniciarTooltip("tooltipServiciosComplementariosDetalles");
		
		
	},'json');
	

}

function cerrarModal()
{
	$.unblockUI();
	// timer = setInterval('recargar()', 120000);
}


$(function() {
	
	 $("#wfecha_inicial").datepicker({
   
    });
	
	$("#wfecha_final").datepicker({
   
    });	
	
	$("#wfecha_inicialSinCTC").datepicker({
   
    });
	
	$("#wfecha_finalSinCTC").datepicker({
   
    });	
	
	$("#wfecha_inicialReimprimir").datepicker({
   
    });
	
	$("#wfecha_finalReimprimir").datepicker({
   
    });

	$("#wfecha_inicialMipres").datepicker({
		maxDate:new Date()
    });
	
	$("#wfecha_finalMipres").datepicker({
		maxDate:new Date()
    });		
	
});

// function recargar(){

// $.blockUI({ message:	'Espere...',
						// css: 	{
									// width: 	'auto',
									// height: 'auto'
								// }
				 // });

// setTimeout(function()
	  // {

		// $.unblockUI()
		// location.reload();
	  // }, 3000);
// }

function consultarMipresFecha(){
	
	fechaIniMipres = $("#wfecha_inicialMipres").val();
	fechaFinMipres = $("#wfecha_finalMipres").val();

	if(fechaIniMipres > fechaFinMipres)
	{
		alert("La fecha final debe ser mayor a la fecha inicial");
	}
	else
	{
		$("#imprimir").val("");
		
		$("#ctcprocedimientos").submit();
		
	}
}

function llenarCTC(historia,ingreso,tipOrden,nroOrden,nroItem,medico,usuario,wemp_pmla,accion,codExamen,id,wbasedatohce,fecha){
	// clearInterval(timer);
	
	if(accion=="R")
	{
		Marcar = confirm( "¿Confirma que realizará el CTC?" );
	}
	
	
	if(Marcar==true)
	{
		$.post("generarCTCProcedimientos.php",
		{
			wemp_pmla:    			wemp_pmla,
			historia:         		historia,
			ingreso:      	    	ingreso,
			fechaKardex: 	 		fecha,
			codExamen: 	 			codExamen,
			idExamen: 	 			id,
			tipOrden: 	 			tipOrden,
			nroOrden: 	 			nroOrden,
			nroItem: 	 			nroItem,
			medico: 	 			medico,
			wbasedatohce: 	 		wbasedatohce,
			accion:    				accion
			
			
			
		}, function(respuesta){
			
			//Creo el div que contendrá todos los ctc de procedimientos
			if( !document.getElementById('ctcProcedimientos') ){
				var divAux = document.createElement( "div" );
				
				divAux.innerHTML = "<div id='ctcProcedimientos' style='display:none'>"
				                 + "<INPUT TYPE='hidden' name='hiProcsNoPos' id='hiProcsNoPos' value=''>"
								 + "</div>";
				
				document.forms[0].appendChild(divAux.firstChild);
			}
			
			// document.getElementById('hiProcsNoPos').value += ',' + codExamen + '-' +cuentaExamenes;
			document.getElementById('hiProcsNoPos').value += ',' + codExamen + '-' +id;
			
			if( !document.getElementById('ctcProcTemp') ){
			
				var divAux = document.createElement( "div" );
				
				divAux.style.display = 'none';
				divAux.style.width = '80%';
				divAux.id = 'ctcProcTemp';
			}
			else{
				var divAux = document.getElementById('ctcProcTemp');
			}
			
			// divAux.innerHTML = ajax.responseText;
			divAux.innerHTML = respuesta;
		
			document.forms[0].appendChild(divAux);
		
			$.blockUI({ message: $('#ctcProcTemp'),
						css: {
							top:  '5%', 
							left: '10%', 
							width: '80%',
							height: '90%',
							overflow: 'auto',
							cursor: 'auto'
						}
			});
			
		});
	}
}


function marcarAccion(historia,ingreso,tipoOrden,nroOrden,nroItem,medico,usuario,wemp_pmla,accion){
	
	if(accion=="N")
	{
		Marcar = confirm( "¿Confirma que no se llenará el CTC?" );
	}
	else if(accion=="M")
	{
		Marcar = confirm( "¿Confirma que el medico llenará el CTC?" );
	}
	
	if(Marcar==true)
	{
		$.post("impresionCTCProcedimientosNoPos.php",
		{
			consultaAjax:   		'MarcarCTCAccion',
			historia:         		historia,
			ingreso:      	    	ingreso,
			tipoOrden: 	 			tipoOrden,
			nroOrden: 	 			nroOrden,
			nroItem: 	 			nroItem,
			medico: 	   			medico,
			usuario:    			usuario,
			wemp_pmla:    			wemp_pmla,
			accion:    				accion
			
			
			
		}, function(respuesta){
			
			timer = setInterval('recargar()', 8000);
		});
	}
	
}

function filtrarReimpresionCtc(){
	
	var fecha = $("#filtrosFechasCTC").val();
	
	$('div[id^=filtroFechasCTC]').each(function(){
		if(fecha==0)
		{
			$(this).show();
		}
		else
		{
			if( $(this).hasClass(fecha) )
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		}
	});
}

function consultarSinCTCFecha(){
	
	fechaIniSinCTC = $("#wfecha_inicialSinCTC").val();
	fechaFinSinCTC = $("#wfecha_finalSinCTC").val();
	
	if(fechaIniSinCTC > fechaFinSinCTC)
	{
		alert("La fecha final debe ser mayor a la fecha inicial");
	}
	else
	{
		$("#fecSinCTCInicial").val(fechaIniSinCTC);
		$("#fecSinCTCFinal").val(fechaFinSinCTC);
		$("#imprimir").val("");
		$("#ctcprocedimientos").submit();
	}
}

function consultar_fecha(tipo){
	
	if($("#wfecha_inicial").val() != ""){
		
		$("#tipo_consulta").val(tipo);
		$("#imprimir").val("");
		$("#ctcprocedimientos").submit();
		
	}else{
		
		if(tipo == 'fgen'){
		
			var texto = 'fecha de generación';
		}else{
		
			var texto = 'fecha de impresión';
		}
		alert("Debe seleccionar una "+texto+".");
	}
	
	
	
}


/******************************************************************
 * AJAX
 ******************************************************************/

/******************************************************************
 * Realiza una llamada ajax a una pagina
 * 
 * met:		Metodo Post o Get
 * pag:		Página a la que se realizará la llamada
 * param:	Parametros de la consulta
 * as:		Asincronro? true para asincrono, false para sincrono
 * fn:		Función de retorno del Ajax, no requerido si el ajax es sincrono
 *
 * Nota: 
 * - Si la llamada es GET las opciones deben ir con la pagina.
 * - Si el ajax es sincrono la funcion retorna la respuesta ajax (responseText)
 * - La funcion fn recibe un parametro, el cual es el objeto ajax
 ******************************************************************/
function consultasAjax( met, pag, param, as, fn ){
	
	this.metodo = met;
	this.parametros = param; 
	this.pagina = pag;
	this.asc = as;
	this.fnchange = fn; 

	try{
		this.ajax=nuevoAjax();

		this.ajax.open( this.metodo, this.pagina, this.asc );
		this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		this.ajax.send(this.parametros);

		if( this.asc ){
			var xajax = this.ajax;
//			this.ajax.onreadystatechange = this.fnchange;
			this.ajax.onreadystatechange = function(){ fn( xajax ) };
			
			if ( !estaEnProceso(this.ajax) ) {
				this.ajax.send(null);
			}
		}
		else{
			return this.ajax.responseText;
		}
	}catch(e){	}
}
/************************************************************************/

function boton_imp(){

	$(".printer").bind("click",function()
		{
		
			$(".areaimprimirCTC").printArea({			
				
				popClose: false,
				popTitle : 'CTCProcedimientosNoPos',
				popHt    : 500,
				popWd    : 1200,
				popX     : 200,
				popY     : 200,
				
				});
				
			
		});

}

/**
 *
 */
function consultarPrescripcionCTC( his, ing, art, div, id, ctcNoPos ){

	var vwemp_pmla = document.getElementById( "wemp_pmla" );
	
	if( true ){
									
		var parametros = "whistoria="+his+"&wingreso="+ing+"&pro="+art+"&ide="+id+"&ctcNoPos="+ctcNoPos+"&consultaAjax=&wtodos_ordenes=on";
		
		//hago la grabacion por ajax del articulo
		consultasAjax( "POST", "ordenes_imp.php?wemp_pmla="+vwemp_pmla.value, 
						parametros, 
						true, 
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
							
								//Esta función llena los datos del protocolo
								document.getElementById( div ).innerHTML = ajax.responseText+"<div style='page-Break-After:always'></div>";
								boton_imp();
								
							}
						}
					);
	}
}

/**********************************************************************
 * muestra u oculta un campo segun su id
 **********************************************************************/
function mostrar( campo ){
	
	if( campo.style.display == 'none' ){
		campo.style.display = '';
	}
	else{
		campo.style.display = 'none';
	}
}

/************************************************************************
 * Busca la fila siguiente para mostrar o ocultar la fila
 * Por tanto campo es una Fila
 ************************************************************************/
function mostrarFila( campo ){

	var tabla = campo.parentNode;
	var index = campo.rowIndex;

	mostrar( tabla.rows[ index+1 ] );
}

function pulsar(e) {
	tecla=(document.all) ? e.keyCode : e.which;
  if(tecla==13) return false;
}


function limpiarbusqueda(){

 $.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
$('input#id_search').val('');
location.reload();

}


function recargar(){

$.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
				 
setTimeout(function() 
	  {
	   
		$.unblockUI()
		location.reload();
	  }, 3000);
}

$(function() {

	$( ".desplegable" ).accordion({
			collapsible: true,
			active:0,
			heightStyle: "content",
			icons: null
	});
	
	//Permite que al escribir en el campo buscar, se filtre la informacion del grid	
	$('input#id_search').quicksearch('div#accordion');
		
});

$(document).ready(function()
	{
	
	boton_imp();
			
	});
</script>

<?php
if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");

/**************************************************************************************************************
 * Impresion de formulas de control
 *
 * Fecha de creación:	2012-10-09
 * Por:					Edwin Molina Grisales
 **************************************************************************************************************/
/**************************************************************************************************************
 * DESCRIPCION:
 *
 * Al grabar las ordenes médicas se genera una orden de impresión para los medicamentos de control que el médico
 * halla ordenada.
 *
 * La orden debe ir por la cantidad segun el perfil, es decir la cantidad requerida hasta el día siguiente a la
 * hora de corte.
 *
 * ESPECIFICACIOENS:
 *
 * - El programa graba los articulos de control que el medico halla ordenado para un paciente.
 * - No se permite mandar mas de un medicamento de control a la vez con la misma frecuencia (???)
 * - 
 **************************************************************************************************************/
 /*		Modificaciones
 
  **************************************************************************************************************
- Abril 1 de 2019 Edwin MG: 	Del query de cambio de responsable se elimina la tabla 47 de hce, ya que se encontraba repetida y sin usort
								haciendo que el query fuera muy lento
**************************************************************************************************************
- Diciembre 18 de 2017 Jessica: Se agrega el llamado a la función consultarDiagnosticoPaciente() de comun.php
								que devuelve la lista de los diagnósticos actuales del paciente
**************************************************************************************************************
- Agosto 14 de 2017 Jessica:	En la seccion de procedimientos diligenciados en Mipres permite visualizar la información de la prescripción
								consumiendo los web service que dispone el ministerio (https://www.minsalud.gov.co/Paginas/Mipres.aspx 
								en la sección Documentos técnicos) 
								https://www.minsalud.gov.co/Documentos%20y%20Publicaciones/MIPRES%20NoPBS%20-%20Documentaci%C3%B3n%20WEB%20SERVICES%20Versi%C3%B3n%203.1.pdf
								https://wsmipres.sispro.gov.co/WSMIPRESNOPBS/Swagger/ui/index
**************************************************************************************************************
- Marzo 1 de 2017 Jessica:		Se agrega seccion de procedimientos diligenciados en Mipres (marcados en movhos_000134 como externos)
**************************************************************************************************************
  - Junio 16 de 2016 Jessica:		Se modifica el titulo NOTA MEDICA por PROCEDIMIENTO O TECNOLOGÍA NO POS ORDENADA
**************************************************************************************************************
- Mayo 12 de 2016 Jessica:		Se agrega filtro de fechas para reimprimir los ctc
**************************************************************************************************************
- Mayo 10 de 2016 Jessica:		Se corrige el query de procedimientos sin ctc por cambio de responsable para que no sea tan lento 
**************************************************************************************************************
- Mayo 5 de 2016 Jessica:		Se valida si el responsable no sea igual a una empresa definida en empresasConfirmanCTC de root_000051
  **************************************************************************************************************
- Enero 21 de 2016 Jessica:		Se agrega al reporte la seccion Procedimientos sin ctc por cambio de responsable que permite llenar los ctc 
								o marcarlos como no realizar
 **************************************************************************************************************
 * Marzo 25 de 2015 Jonatan:	Se agrega al pie de la impresion la palabra "Firmado electrónicamente".
 
 
 /
 
/****************************************************************************************************************
 * 												FUNCIONES
 ****************************************************************************************************************/
function consultarNotaMedicaHCE( $conex, $whce, $tabla, $campo, $his, $ing, $id ){

	$val = "";

	$sql = "SELECT Movdat 
			FROM
				".$whce."_".$tabla."
			WHERE
				movpro = '".$tabla."'
				AND movcon = '".$campo."'
				AND movhis = '".$his."'
				AND moving = '".$ing."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $rows = mysql_fetch_array( $res ) ){
		
		$notaMed = explode("]:",$rows[ 'Movdat' ]);
		$notaMedId = explode("[",$notaMed[0]);
		if($notaMedId[1]==$id)
		{
			$val = $notaMed[1];
			return $val;
		}
		
	}
	
	// return $val;
}
 
function grabarRegistroCTCAccion($historia,$ingreso,$tipoOrden,$nroOrden,$nroItem,$medico,$usuario,$wemp_pmla,$accion)
{
	global $conex;
	global $wbasedato;
	global $wusuario;

	$wfecha = date('Y-m-d');
	$whora = date("H:i:s");
		
	$sql = "SELECT *
			  FROM ".$wbasedato."_000135
			 WHERE Ctchis='".$historia."' 
			   AND Ctcing='".$ingreso."' 
			   AND Ctctor='".$tipoOrden."'
			   AND Ctcnro='".$nroOrden."'
			   AND Ctcite='".$nroItem."'
			   AND Ctcest='on' ;";
		
	$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	$num = mysql_num_rows( $res );
	
	if( $num > 0 )
	{
		$sql = "UPDATE ".$wbasedato."_000135
				   SET Ctcacc = '".$accion."', 
						Ctcacu = '".$usuario."',
						Ctcacf = '".$wfecha."',
						Ctcach = '".$whora."'
				 WHERE Ctchis='".$historia."' 
				   AND Ctcing='".$ingreso."' 
				   AND Ctctor='".$tipoOrden."'
				   AND Ctcnro='".$nroOrden."'
				   AND Ctcite='".$nroItem."'
				   AND Ctcest='on';";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$filas_actualizadas = mysql_affected_rows();
		
		if( $filas_actualizadas > 0 ){
			$Mensaje= "Se ha actualizado correctamente la accion para el CTC";
		}
		else{
			$Mensaje= "Error actualizando la accion para el CTC: ".mysql_errno();
			echo "<script> alert(".$Mensaje."); </script>";
		}
	}
	else
	{
		$realizado="off";
		
		$queryInsert = " INSERT INTO ".$wbasedato."_000135
						(Medico,Fecha_data,Hora_data,Ctchis,Ctcing,Ctctor,Ctcnro,Ctcite,Ctcest,Ctcmed,Ctcacc,Ctcacr,Ctcacu,Ctcacf,Ctcach,Seguridad) 
							 VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$historia."','".$ingreso."','".$tipoOrden."','".$nroOrden."','".$nroItem."','on','".$medico."','".$accion."','".$realizado."','".$usuario."','".$wfecha."','".$whora."','C-".$wbasedato."');";
							 
		$resultado2 = mysql_query($queryInsert,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryInsert." - ".mysql_error());

		if(mysql_affected_rows()==1)
		{
			$Mensaje= "Se ha registrado correctamente la accion para el CTC";
		}
		else
		{
			$Mensaje= "Error registrando la accion para el CTC: ".mysql_errno();
			echo "<script> alert(".$Mensaje."); </script>";
		}
		
		
	}
	
	echo $Mensaje;
	// echo "<script>timer = setInterval('recargar()', 1000);</script>";
	
}

function dimensionesImagen($idemed)
{
	global $altoimagen;
	global $anchoimagen;

	if(file_exists('../../images/medical/hce/Firmas/'.$idemed.'.png')) {
		// Obtengo las propiedades de la imagen, ancho y alto
		list($widthimg, $heightimg) = getimagesize('../../images/medical/hce/Firmas/'.$idemed.'.png');
	} else {
		$widthimg = '181';
		$heightimg = '27';
	}
	
	$anchoimagen = '181';
	
	//$altoimagen = floor( (181 * $heightimg/$widthimg) );
	$altoimagen = $heightimg + floor( ( $anchoimagen-$widthimg )*($heightimg/$widthimg) );
	
	// if($altoimagen>54)
		// $altoimagen = 54;
}
 
function consultarDatosTablaHCE( $conex, $whce, $tabla, $campo, $his, $ing ){

	$val = "";

	$sql = "SELECT Movdat 
			FROM
				{$whce}_{$tabla}
			WHERE
				movpro = '$tabla'
				AND movcon = '$campo'
				AND movhis = '$his'
				AND moving = '$ing'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Movdat' ];
	}
	
	return $val;
}

/****************************************************************************************************
 * Cambia el estado de la impresion
 ****************************************************************************************************/
function cambiarEsadoImpresionPorId( $conex, $wbasedato, $id, $usuario ){

	$val = false;

	$sql = "UPDATE 
				{$wbasedato}_000135
			SET
				Ctcimp = 'on',
				Ctcuim = '$usuario',
				Ctcfim = '".date( "Y-m-d" )."',
				Ctchim = '".date( "H:i:s" )."'
			WHERE
				id = '$id'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows( ) > 0 ){
		$val = true;
	}
	
	return $val;
}
 
/**********************************************************************
 * Consulta la informacion de un medico
 **********************************************************************/
function consultarInformacionMedico( $conex, $wbasedato, $codigo ){

	$val = false;

	$sql = "SELECT
				a.*, Espcod, Espnom
			FROM
				{$wbasedato}_000048 a, {$wbasedato}_000044 b
			WHERE
				Meduma = '$codigo'
				AND Medest = 'on'
				AND SUBSTRING_INDEX( medesp, '-', 1 ) = espcod
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	
	return $val;
}
 
/****************************************************************************************************************
 * Cambia el estado de la impresion segun el parametro estado
 ****************************************************************************************************************/
function cambiarEstadoImpresion( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $codMedico, $estado ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000135
			SET
				Ctrimp = '$estado'
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrmed = '$codMedico'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}


/****************************************************************************************************************
 * Cambia el estado del registro segun el campo estado. on para activarlo, off para desactivarlo
 ****************************************************************************************************************/
function cambiarEstadoRegistro( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $codMedico, $estado ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000135
			SET
				Ctrest = '$estado'
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrmed = '$codMedico'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}

// Función que retorna la edad con base en la fecha de nacimiento
function calcularEdad($fechaNacimiento) 
{
	$ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($aa - $ann)/360;
	$meses=(($aa - $ann) % 360)/30;
	if ($ann1<1){
		$dias1=(($aa - $ann) % 360) % 30;
		// $wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		$wedad=(string)(integer)$meses." mes(es) ";
	} else {
		$dias1=(($aa - $ann) % 360) % 30;
		//$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		$wedad=(string)(integer)$ann1." a&ntilde;o(s) ";
	}

	return $wedad;
}		



/****************************************************************************************************************
 * 												FIN DE FUNCIONES
 ****************************************************************************************************************/



include_once("root/comun.php");



$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));
  
$wuser1=explode("-",$user);
$wusuario=trim($wuser1[1]);

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$whce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );

$institucion = consultarInstitucionPorCodigo($conex,$wemp_pmla);

if( $consultaAjax ){	//si hay ajax

	switch($consultaAjax){

		case 'MarcarCTCAccion':
		{
			echo  grabarRegistroCTCAccion($historia,$ingreso,$tipoOrden,$nroOrden,$nroItem,$medico,$usuario,$wemp_pmla,$accion);
		}
		break;
		
		case 'consultarParametrosCTC':
		{
			echo  consultarParametrosCTC($historia,$ingreso,$articulo,$medico,$usuario,$wemp_pmla,$accion);
		}
		break;

		default: break;

		}
	return;
	
}
else{	//si no hay ajax

	include_once("root/montoescrito.php");

	echo "<form id='ctcprocedimientos'>";
	
	if( $imprimir == "" ){
	
		$wactualiz = "Diciembre 18 de 2017";
		
		encabezado("IMPRESION FORMULARIOS CTC DE PROCEDIMIENTOS",$wactualiz, "clinica");
		
		
		echo "</br>";
		echo "</br>";
		echo "<center>";
			echo "<table>";
				echo "<tr>";
					// echo "<td class=encabezadotabla><font size=5><b>FORMULARIOS CTC DE PROCEDIMIENTOS PENDIENTES DE IMPRIMIR</b></font></td>";
					echo "<td  bgcolor='#C2C9C2'><font size=5><b>FORMULARIOS CTC DE PROCEDIMIENTOS PENDIENTES DE IMPRIMIR</b></font></td>";
				echo "</tr>";
			echo "</table>";
		echo "</center>";
		
		//Busca los cco que se tienen medicamentos a imprimir
	    $sql = "SELECT
					Ubihac, Ubisac, Cconom, Ctchis, Ctcing , Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*
				FROM
					{$wbasedato}_000135 a, {$whce}_000017 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f
				  WHERE  ctcimp = 'off'
					AND ctcest = 'on'
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = codigo
					AND ubisac = ccocod
					AND orihis = ubihis
					AND oritid = pactid
					AND oriced = pacced
					AND nuevo = 'on'
					AND oriori = '$wemp_pmla'
				UNION
				SELECT
					Ubihac, Ubisac, Cconom, Ctchis, Ctcing , Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*
				FROM
					{$wbasedato}_000135 a, {$whce}_000047 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f
				  WHERE  ctcimp = 'off'
					AND ctcest = 'on'
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = codigo
					AND ubisac = ccocod
					AND orihis = ubihis
					AND oritid = pactid
					AND oriced = pacced
					AND nuevo = 'off'
					AND oriori = '$wemp_pmla'
				ORDER BY
					ubisac, ubihac
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );	
		
		if( $num > 0 ){
		
			echo "<br>";
			echo "<center>";
			echo "<table>";
			echo "<tr>";
			echo "<td class=encabezadotabla>Buscar</td>";		
			echo "<td class=encabezadotabla><input id='id_search' type='text' value='' name='search' onkeypress='return pulsar(event);'></td>";			
			echo "<td><img width='auto' width='15' height='15' border='0' onclick='limpiarbusqueda();' title='Reiniciar Búsqueda' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></td>";
			echo "</tr>";
			echo "</table>";
			echo "</center>";
			echo "<br>";
				
			$ccoAnt = '';
		
			$total = 0;
			$totalAImprimir = 0;
		
		
			$rows = mysql_fetch_array( $res );
			$ccoAnt = $rows[ 'Ubisac' ];
			
			for( $i = 0;; ){
				
				if( $ccoAnt == $rows[ 'Ubisac' ] ){
					
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'hab' ] = $rows[ 'Ubihac' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'nom' ] = $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ];;
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'tot' ]++;
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'his' ] = $rows[ 'Ctchis' ];
				
					$cconom = $rows[ 'Cconom' ];
					$total++;
					$totalAImprimir++;
					$rows = mysql_fetch_array( $res );
				}
				elseif( $total > 0 ){
					
					echo "<div id='accordion' class='desplegable'>";
					
					echo "<h3>$cconom</h3>";
					
					$total = 0;
					$ccoAnt = $rows[ 'Ubisac' ];
					$i++;
					
					//creo una fila mas con la información del paciente que se quiere imprimir
					if( true ){
						
						echo "<div>";
					
						echo "<table align='center'>";
						
						echo "<tr class='encabezadotabla' align='center'>";
						echo "<td>Habitaci&oacute;n</td>";
						echo "<td>Historia</td>";
						echo "<td>Nombre</td>";
						echo "<td>Cantidad</td>";
						echo "<td style='width:75'>Impresion por<br>paciente</td>";
						
						echo "</tr>";
						
						$k = 0;
						
						foreach( $pacientes as $keyPacientes => $hisPacientes ){
														
								$class2 = "fila".($k%2+1)."";
							
								echo "<tr class='$class2'>";
								
								echo "<td align='center'>";
								echo $hisPacientes[ 'hab' ];
								echo "</td>";
								
								echo "<td align='center'>";
								echo $keyPacientes;
								echo "</td>";
								
								echo "<td>";
								echo $hisPacientes[ 'nom' ];
								echo "</td>";
								
								echo "<td align='center'>";
								echo $hisPacientes[ 'tot' ];
								echo "</td>";
								
								echo "<td align='center'><a href='impresionCTCProcedimientosNoPos.php?wemp_pmla=$wemp_pmla&imprimir=on&historia={$hisPacientes[ 'his' ]}' target='_blank' onclick='recargar();'>Imprimir</a></td>";
								
								echo "</tr>";
								
								$k++;
						
						}
						
						echo "</table>";
						echo "</div>";
					}
					
					// $pacientes = "";	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
					$pacientes = array();	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
					
					echo "</div>";
					if( !$rows ){
						break;
					}
				}
			}
						
		}
		else{
			echo "<center><b>NO SE ENCONTRARON CTC PARA IMPRIMIR</b></center>";
		}
		
		
		
		// ---------------------------
		
		// PROCEDIMIENTOS SIN CTC POR CAMBIO DE RESPONSABLE
		$aplicacion = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion" );
		echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "<hr>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";
						// echo "<td class=encabezadotabla><font size=5><b>PROCEDIMIENTOS SIN CTC POR CAMBIO DE RESPONSABLE</b></font></td>";
						echo "<td bgcolor='#F2FFA0'><font size=5><b>PROCEDIMIENTOS SIN CTC POR CAMBIO DE RESPONSABLE</b></font></td>";
						// echo "<td class=encabezadotabla><font size=5><b>PROCEDIMIENTOS SIN CTC</b></font></td>";
						echo "<input type=hidden id='fecSinCTCInicial' name='fecSinCTCInicial' value=''>";
						echo "<input type=hidden id='fecSinCTCFinal' name='fecSinCTCFinal' value=''>";
						// echo "<input type=hidden id='tipo_consulta' name='tipo_consulta' value=''>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
		
		
		if($wfecha_inicialSinCTC == ""){
			$wfecha_inicialSinCTC = date('Y-m-d');
			
		}
		
		if($wfecha_finalSinCTC == ""){
			$wfecha_finalSinCTC = date('Y-m-d');
			
		}
		
		$rangoFechaSinCTC="";
		if($fecSinCTCInicial != "" & $fecSinCTCFinal != "")
		{
			$rangoFechaSinCTC = "'".$fecSinCTCInicial."' AND '".$fecSinCTCFinal."'";
		}
		else
		{
			$fecSinCTCInicial = date('Y-m-d');
			$fecSinCTCFinal = date('Y-m-d');
			
			$rangoFechaSinCTC = "'".$fecSinCTCInicial."' AND '".$fecSinCTCFinal."'";
		}
		
		echo "<br>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";										
						echo "<td colspan=2 class=encabezadotabla align=center><b>Buscar:</b></td>";
					echo "</tr>";					
					
					echo "<tr>";
						echo "<td class=fila1 align=left>Fecha inicial:</td><td align=left><input type=text name='wfecha_inicialSinCTC' id='wfecha_inicialSinCTC' readonly='readonly' value='".$fecSinCTCInicial."'></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td class=fila2 align=left>Fecha final:</td><td align=left><input type=text name='wfecha_finalSinCTC' id='wfecha_finalSinCTC' readonly='readonly' value='".$fecSinCTCFinal."'></td>";						
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan=2 align=center><input type='button' id='btn_consultar_fecha' onclick='consultarSinCTCFecha();' value='Generar'></td>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
		
		
		
		
		$tipoEmpresa= consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiposEmpresasEps" );
		
		$empresasConCTC= explode("-",$tipoEmpresa);
		$cadenaEmpresasConCTC="";
		for($r=0;$r<count($empresasConCTC);$r++)
		{
			$cadenaEmpresasConCTC .= "'".$empresasConCTC[$r]."',";
		}
		$cadenaEmpresasConCTC = substr($cadenaEmpresasConCTC,0,-1);
		
		
		//Empresas que confirman si llenan el CTC
		$wentidades_confirmanCTC = consultarAliasPorAplicacion($conex, $wemp_pmla, "empresasConfirmanCTC");
		
		$responsableEmpresasConfirman = explode(",",$wentidades_confirmanCTC);
				
		//Abril 1 de 2019 - Este query tenía repetida la tabla hce_000047 y sin usar, por tal motivo se quita del query
		$queryCambioResponsable = "SELECT Ordhis,Ording,Dettor,Detnro,Detite,Detcod,Ingtip,Detusu,Rolcod,Roldes,Rolmed,b.Descripcion,Detfec, Resfir   
									 FROM ".$whce."_000027,".$whce."_000028,".$wbasedato."_000016,".$whce."_000019,".$whce."_000020,".$whce."_000047 a, usuarios b, ".$aplicacion."_000205	
									WHERE Detfec BETWEEN ".$rangoFechaSinCTC."
									  AND Ordhis=Inghis 
									  AND Ording=Inging
									  AND Ingtip IN (".$cadenaEmpresasConCTC.")
									  AND Ordtor = Dettor
									  AND Ordnro = Detnro
									  AND Detcod = a.Codigo
									  AND a.NoPos='on'
									  AND Detusu = Usucod
									  AND Rolcod = Usurol
									  AND b.Codigo = Detusu
									  AND Reshis = Ordhis								  									  									 
									  AND Resing = Ording								  									  									 
									  AND Resnit = Ingres			 									 
									  AND Detfec >= Resfir	

									UNION
									
								   SELECT Ordhis,Ording,Dettor,Detnro,Detite,Detcod,Ingtip,Detusu,Rolcod,Roldes,Rolmed,b.Descripcion,Detfec, Resfir   
									 FROM ".$whce."_000027, ".$wbasedato."_000159, ".$wbasedato."_000016,".$whce."_000019,".$whce."_000020,".$whce."_000047 a, usuarios b, ".$aplicacion."_000205	
									WHERE Detfec BETWEEN ".$rangoFechaSinCTC."
									  AND Ordhis=Inghis 
									  AND Ording=Inging
									  AND Ingtip IN (".$cadenaEmpresasConCTC.")
									  AND Ordtor = Dettor
									  AND Ordnro = Detnro
									  AND Detcod = a.Codigo
									  AND a.NoPos='on'
									  AND Detusu = Usucod
									  AND Rolcod = Usurol
									  AND b.Codigo = Detusu
									  AND Reshis = Ordhis								  									  									 
									  AND Resing = Ording								  									  									 
									  AND Resnit = Ingres			 									 
									  AND Detfec >= Resfir;";
									 
		
		$resultadoCambioResponsable = mysql_query( $queryCambioResponsable, $conex ) or die( mysql_errno()." - Error en el query $queryCambioResponsable - ".mysql_error() );
		$cantidaRegistros = mysql_num_rows($resultadoCambioResponsable);
		
		
		
		echo "</br>";
		echo "</br>";
				
		echo "<center>";

		echo "<INPUT type='hidden' id='wemp_pmla' value='$wemp_pmla'>";
		
		$ProcedimientosSinCTCyCambioResponsable = array();
		$posSinCTC = 0;
		if($cantidaRegistros > 0)
		{
			while ($rowCambioResponsable = mysql_fetch_array($resultadoCambioResponsable)) 
			{
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['his']=$rowCambioResponsable['Ordhis'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['ing']=$rowCambioResponsable['Ording'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['tip']=$rowCambioResponsable['Dettor'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['nro']=$rowCambioResponsable['Detnro'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['ite']=$rowCambioResponsable['Detite'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['pro']=$rowCambioResponsable['Detcod'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['emp']=$rowCambioResponsable['Ingtip'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['usu']=$rowCambioResponsable['Detusu'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['rol']=$rowCambioResponsable['Rolcod'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['roldes']=$rowCambioResponsable['Roldes'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['medgen']=$rowCambioResponsable['Rolmed'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['medico']=$rowCambioResponsable['Descripcion'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['fec']=$rowCambioResponsable['Detfec'];
				$ProcedimientosSinCTCyCambioResponsable[$posSinCTC]['fir']=$rowCambioResponsable['Resfir'];
				
				$posSinCTC++;
			}
			
			
			$SinCTCyCambioResponsable=array();
			$arrayCco=array();
			$cantSinCTC=0;
			for($r=0;$r<count($ProcedimientosSinCTCyCambioResponsable);$r++)
			{
				$queryProcedimientoCTC = "SELECT * 
											FROM ".$wbasedato."_000135
										   WHERE Ctchis = '".$ProcedimientosSinCTCyCambioResponsable[$r]['his']."'
											 AND Ctcing = '".$ProcedimientosSinCTCyCambioResponsable[$r]['ing']."'
											 AND Ctctor = '".$ProcedimientosSinCTCyCambioResponsable[$r]['tip']."'
											 AND Ctcnro = '".$ProcedimientosSinCTCyCambioResponsable[$r]['nro']."'
											 AND Ctcite = '".$ProcedimientosSinCTCyCambioResponsable[$r]['ite']."'
											 AND Ctcest='on';";
									   
									   
				$resultadoMedicamentoCTC = mysql_query( $queryProcedimientoCTC, $conex ) or die( mysql_errno()." - Error en el query $queryProcedimientoCTC - ".mysql_error() );
				$cantidadRegistrosCTC = mysql_num_rows($resultadoMedicamentoCTC);
		
				
				if($cantidadRegistrosCTC == 0)
				{
					$querySinCTC = "  SELECT Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Descripcion,Ingres,Ingnre 	
										FROM ".$whce."_000017, ".$wbasedato."_000018, ".$wbasedato."_000011, root_000036, root_000037, ".$wbasedato."_000016				  
										WHERE Ubihis = '".$ProcedimientosSinCTCyCambioResponsable[$r]['his']."' 
										AND Ubiing = '".$ProcedimientosSinCTCyCambioResponsable[$r]['ing']."' 
										AND Ccocod = Ubisac
										AND Orihis = '".$ProcedimientosSinCTCyCambioResponsable[$r]['his']."' 					
										AND Oriing = '".$ProcedimientosSinCTCyCambioResponsable[$r]['ing']."' 
										AND Oriced = Pacced
										AND Oritid = Pactid  
										AND Codigo = '".$ProcedimientosSinCTCyCambioResponsable[$r]['pro']."'
										AND Nuevo = 'on' 					
										AND Oriori = '".$wemp_pmla."'
										AND Inghis = '".$ProcedimientosSinCTCyCambioResponsable[$r]['his']."'
										AND Inging = '".$ProcedimientosSinCTCyCambioResponsable[$r]['ing']."'
														
										UNION 				

										SELECT Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Descripcion,Ingres,Ingnre 	
										FROM ".$whce."_000047, ".$wbasedato."_000018, ".$wbasedato."_000011, root_000036, root_000037, ".$wbasedato."_000016				  
										WHERE Ubihis = '".$ProcedimientosSinCTCyCambioResponsable[$r]['his']."' 
										AND Ubiing = '".$ProcedimientosSinCTCyCambioResponsable[$r]['ing']."' 
										AND Ccocod = Ubisac
										AND Orihis = '".$ProcedimientosSinCTCyCambioResponsable[$r]['his']."' 					
										AND Oriing = '".$ProcedimientosSinCTCyCambioResponsable[$r]['ing']."' 
										AND Oriced = Pacced
										AND Oritid = Pactid  
										AND Codigo = '".$ProcedimientosSinCTCyCambioResponsable[$r]['pro']."'
										AND Nuevo = 'off' 					
										AND Oriori = '".$wemp_pmla."'
										AND Inghis = '".$ProcedimientosSinCTCyCambioResponsable[$r]['his']."'
										AND Inging = '".$ProcedimientosSinCTCyCambioResponsable[$r]['ing']."';";
					
					$resultadoSinCTC = mysql_query( $querySinCTC, $conex ) or die( mysql_errno()." - Error en el query $querySinCTC - ".mysql_error() );
					$cantidadSinCTC = mysql_num_rows($resultadoSinCTC);
					
					if($cantidadRegistrosCTC == 0)
					{
						while ($rowSinCTC = mysql_fetch_array($resultadoSinCTC)) 
						{
							
							$empNoConfirma = false;				

							$queryEmp = " SELECT Empnit 
											FROM ".$aplicacion."_000024 
										   WHERE Empcod='".$rowSinCTC['Ingres']."';";
							
							$resEmp = mysql_query( $queryEmp, $conex ) or die( mysql_errno()." - Error en el query $queryEmp - ".mysql_error() );
							$numEmp = mysql_num_rows($resEmp);					
							
							if($numEmp > 0)
							{
								$rowEmp = mysql_fetch_array($resEmp);
								
								for($c=0;$c<count($responsableEmpresasConfirman);$c++)
								{
									if($rowEmp['Empnit']==$responsableEmpresasConfirman[$c])
									{
										$empNoConfirma = true;
										break;
									}
								}
							}

							if($empNoConfirma == false)
							{
								$his=$ProcedimientosSinCTCyCambioResponsable[$r]['his'];
								$ing=$ProcedimientosSinCTCyCambioResponsable[$r]['ing'];
								$pro=$ProcedimientosSinCTCyCambioResponsable[$r]['pro'];
								
								$arrayCco[$cantSinCTC] = $rowSinCTC['Ubisac']."|".$rowSinCTC['Cconom'];
								
								
								if(array_key_exists($his."-".$ing."|".$pro,$SinCTCyCambioResponsable))
								{
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Canpro']++;
								}
								else
								{
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Ubisac']=$rowSinCTC['Ubisac'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Cconom']=$rowSinCTC['Cconom'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Ubihac']=$rowSinCTC['Ubihac'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Ordhis']=$ProcedimientosSinCTCyCambioResponsable[$r]['his'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Ording']=$ProcedimientosSinCTCyCambioResponsable[$r]['ing'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Dettor']=$ProcedimientosSinCTCyCambioResponsable[$r]['tip'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Detnro']=$ProcedimientosSinCTCyCambioResponsable[$r]['nro'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Detite']=$ProcedimientosSinCTCyCambioResponsable[$r]['ite'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Detcod']=$ProcedimientosSinCTCyCambioResponsable[$r]['pro'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Canpro']= 1;
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Pacno1']=$rowSinCTC['Pacno1'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Pacno2']=$rowSinCTC['Pacno2'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Pacap1']=$rowSinCTC['Pacap1'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Pacap2']=$rowSinCTC['Pacap2'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Descri']=$rowSinCTC['Descripcion'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Ingres']=$rowSinCTC['Ingres'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Ingnre']=$rowSinCTC['Ingnre'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Detusu']=$ProcedimientosSinCTCyCambioResponsable[$r]['usu'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Rolcod']=$ProcedimientosSinCTCyCambioResponsable[$r]['rol'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Roldes']=$ProcedimientosSinCTCyCambioResponsable[$r]['roldes'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Rolmed']=$ProcedimientosSinCTCyCambioResponsable[$r]['medgen'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Mednom']=$ProcedimientosSinCTCyCambioResponsable[$r]['medico'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Detfec']=$ProcedimientosSinCTCyCambioResponsable[$r]['fec'];
									$SinCTCyCambioResponsable[$his."-".$ing."|".$pro]['Resfir']=$ProcedimientosSinCTCyCambioResponsable[$r]['fir'];
					
								}
																													
								$cantSinCTC++;
							}
						}
					}
				}
			}
			
			sort($SinCTCyCambioResponsable);
			sort($arrayCco);
			
			$arrayCco=array_unique($arrayCco);
			
			$wfecha = date('Y-m-d');
			
			$UsuariosAccionesHabilitadas = consultarAliasPorAplicacion($conex, $wemp_pmla, "UsuariosRealizanCTCAcciones");
			
			$UsuariosAccHab=explode(",",$UsuariosAccionesHabilitadas);
			
			$MostrarAcciones="off";
			for($i=0;$i<count($UsuariosAccHab);$i++)
			{
				if($UsuariosAccHab[$i]==$wusuario)
				{
					$MostrarAcciones="on";
					break;
				}
			}
			
			foreach($arrayCco as $key2 => $cco)
			{
				$filasRowspan=1;
				  if($MostrarAcciones == "on")
				  {
					 $filasRowspan=2; 
				  }
				
				$ccodes=explode("|",$cco);
				echo "<div id='accordion' class='desplegable' >";

					echo "<h3 align='left'>".$ccodes[1]."</h3>";
					echo "<div>";
						// echo '<table  style="width: 1200px;">';
						echo '<table align="center" style="width: 785px;">';
							echo "<tr class='encabezadotabla' align='center'>";
								echo "<td rowspan='".$filasRowspan."'>Habitaci&oacute;n</td>";
								echo "<td rowspan='".$filasRowspan."'>Historia</td>";
								echo "<td rowspan='".$filasRowspan."'>Nombre</td>";
								echo "<td rowspan='".$filasRowspan."'>Procedimiento</td>";
								echo "<td rowspan='".$filasRowspan."'>Cantidad</td>";
								echo "<td rowspan='".$filasRowspan."'>Responsable</td>";
								echo "<td rowspan='".$filasRowspan."'>Ordenado por</td>";
								echo "<td rowspan='".$filasRowspan."'>Rol</td>";
								echo "<td rowspan='".$filasRowspan."' bgcolor='#CAFFC8' style='color:#000000'><b>Fecha inicio <br> responsable <br></b></td>";
								if($MostrarAcciones == "on")
								{
									echo "<td colspan='2'>Accion
										<span id='info' title='Solo podrá marcar la acción Realizar si la orden fue hecha por un medico, de lo contrario deberá marcar No realizar'>
											<img src='../../images/medical/root/info.png' border='0' />
										</span>
									 </td>";
							echo "</tr>";
							echo "<tr class='encabezadotabla' align='center'>
									<td>Realizar</td>
									<td>No realizar</td>
								  </tr>";
								}
				
							$fila_lista = "Fila1";
							$registrosPorHistoria=1;
						
							$rowspanHistoria=array();
							$rowspanProcedimiento=array();
							
							foreach($SinCTCyCambioResponsable as $key => $value)
							{
								
								if($ccodes[0]==$value['Ubisac'])
								{
									$Accion="";
									
									$queryAccionCTC = " SELECT Ctcacc
														  FROM ".$wbasedato."_000135 
														 WHERE Ctchis='".$value['Ordhis']."' 
														   AND Ctcing='".$value['Ording']."' 
														   AND Ctctor='".$value['Dettor']."'
														   AND Ctcnro='".$value['Detnro']."'
														   AND Ctcite='".$value['Detite']."'
														   AND Ctcacr='off' ;";

									$resultadoAccionCTC = mysql_query( $queryAccionCTC, $conex ) or die( mysql_errno()." - Error en el query $queryAccionCTC - ".mysql_error() );
									$cantidadAccionCTC = mysql_num_rows($resultadoAccionCTC);
									
									$rowAccionCTC = mysql_fetch_array($resultadoAccionCTC);
									$Accion=$rowAccionCTC[0];

									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";
									
								$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

									echo "<tr class='".$fila_lista."'>
											  <td align='center'>".$value['Ubihac']."</td>
											  <td>".$value['Ordhis']."-".$value['Ording']."</td>
											  <td>".$value[ 'Pacno1' ]." ".$value[ 'Pacno2' ]." ".$value[ 'Pacap1' ]." ".$value[ 'Pacap2' ]."</td>
											  <td>".$value['Detcod']."-".utf8_decode($value['Descri'])."</td>
											  <td>".$value['Canpro']."</td>
											  <td>".$value['Ingres']." - ".$value['Ingnre']."</td>
											  <td>".$value['Mednom']."</td>
											  <td>".$value['Rolcod']." - ".$value['Roldes']."</td>";
											  echo "<td align='center' bgcolor='#DDFEDC'>".$value['Resfir']."</td>";
											    if($MostrarAcciones == "on")
												{
													echo"
													  <td colspan='1' align='center'>
														<input 
															type='radio' 
															name='AccionCTC|".$value['Ordhis']."-".$value['Ording']."(".$value['Dettor']."-".$value['Detnro']."-".$value['Detite'].")'
															value='R' 
															".(($value['Rolmed'] != 'on') ? 'disabled="disabled"': '')." 
															".(($Accion == 'R') ? 'checked="checked"': '')." 
															onClick='llenarCTC(\"".$value['Ordhis']."\",\"".$value['Ording']."\",\"".$value['Dettor']."\",\"".$value['Detnro']."\",\"".$value['Detite']."\",\"".$value['Detusu']."\",\"".$wusuario."\",\"".$wemp_pmla."\",\"R\",\"".$value['Detcod']."\",\"".$key."\",\"".$wbasedatohce."\",\"".$value['Detfec']."\");'
														><br>
													  </td>

													  <td colspan='1' align='center'>
														<input 
															type='radio' 
															name='AccionCTC|".$value['Ordhis']."-".$value['Ording']."(".$value['Dettor']."-".$value['Detnro']."-".$value['Detite'].")'
															value='N' 
															".(($Accion == 'N') ? 'checked="checked"': '')." 
															onClick='marcarAccion(\"".$value['Ordhis']."\",\"".$value['Ording']."\",\"".$value['Dettor']."\",\"".$value['Detnro']."\",\"".$value['Detite']."\",\"".$value['Detusu']."\",\"".$wusuario."\",\"".$wemp_pmla."\",\"N\");'
														><br>
													  </td>";
												}
											  
									echo "</tr>";
								}
							} 
							
						echo '</table>';
					echo "</div>";	
				echo "</div>";
				
			}
		}
		else
		{
			echo "<center><br><br><b>NO SE ENCONTRARON PROCEDIMIENTOS SIN CTC POR CAMBIO DE RESPONSABLE</b></center>";
		}
		

		
		echo "</center>";
		
		
		// ---------------------------

		//** CTC DE PROCEDIMIENTOS IMPRESOS
				
				if($wfecha_inicial == ""){
					$wfecha_inicial_aux = date('Y-m-d');			
				}else{
					$wfecha_inicial_aux = $wfecha_inicial;
				}
				
				if($wfecha_final == ""){
					$wfecha_final_aux = date('Y-m-d');			
				}else{
					$wfecha_final_aux = $wfecha_final;	
				}
				
				echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<hr>";
					echo "<center>";
						echo "<table>";
							echo "<tr>";
								// echo "<td class=encabezadotabla><font size=5><b>FORMULARIOS CTC DE PROCEDIMIENTOS IMPRESOS</b></font></td>";
								echo "<td bgcolor='#C2C9C2'><font size=5><b>FORMULARIOS CTC DE PROCEDIMIENTOS IMPRESOS</b></font></td>";
								echo "<input type=hidden id='tipo_consulta' name='tipo_consulta' value=''>";
							echo "</tr>";
						echo "</table>";
					echo "</center>";
					
					echo "<br>";
					echo "<center>";
						echo "<table>";
							echo "<tr>";										
								echo "<td colspan=2 class=encabezadotabla align=center><b>Buscar:</b></td>";
							echo "</tr>";					
							
							echo "<tr>";
								echo "<td class=fila1 align=left>Fecha inicial:</td><td align=left><input type=text name='wfecha_inicial' id='wfecha_inicial' value='".$wfecha_inicial_aux."'></td>";
							echo "</tr>";
							echo "<tr>";
								echo "<td class=fila2 align=left>Fecha final:</td><td align=left><input type=text name='wfecha_final' id='wfecha_final' value='".$wfecha_final_aux."'></td>";						
							echo "</tr>";
							echo "<tr>";
								echo "<td colspan=2 align=center><input type='button' id='btn_consultar_fecha' onclick='consultar_fecha(\"fgen\");' value='Por fecha de generacion'><input type='button' onclick='consultar_fecha(\"fimp\");' value='Por fecha de impresion'></td>";
							echo "</tr>";
						echo "</table>";
					echo "</center>";
				
				
				if($wfecha_inicial == ""){
					$wfecha_inicial = date('Y-m-d');
					
				}
				
				if($wfecha_final == ""){
					$wfecha_final = date('Y-m-d');
					
				}
					
				if(isset($tipo_consulta) and $tipo_consulta == 'fgen'){
					switch($tipo_consulta){
						case 'fgen' : $filtro_fecha = " AND Ctcfge BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
						break;
						
						case 'fimp' :  $filtro_fecha = " AND Ctcfim BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
						break;
						}
					
				}else{
				
					$filtro_fecha = " AND Ctcfim BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
				}
				
				
				$sql_imp = "SELECT
							Ubihac, Ubisac, Cconom, Ctchis, Ctcing, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*
						FROM
							{$wbasedato}_000135 a, {$whce}_000017 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f
						  WHERE ctcimp = 'on'
							AND ctcest = 'on'
							AND ubihis = ctchis
							AND ubiing = ctcing
							AND ctcpro = codigo
							AND ubisac = ccocod
							AND orihis = ubihis					
							AND oritid = pactid
							AND oriced = pacced
							AND nuevo = 'on'
							AND oriori = '$wemp_pmla'
							$filtro_fecha
						UNION
						SELECT
							Ubihac, Ubisac, Cconom, Ctchis, Ctcing, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*
						FROM
							{$wbasedato}_000135 a, {$whce}_000047 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f
						  WHERE ctcimp = 'on'
							AND ctcest = 'on'
							AND ubihis = ctchis
							AND ubiing = ctcing
							AND ctcpro = codigo
							AND ubisac = ccocod
							AND orihis = ubihis					
							AND oritid = pactid
							AND oriced = pacced
							AND nuevo = 'off'
							AND oriori = '$wemp_pmla'
							$filtro_fecha
						ORDER BY
							ubisac, ubihac
						";
						
				$res_imp = mysql_query( $sql_imp, $conex ) or die( mysql_errno()." - Error en el query $sql_imp - ".mysql_error() );
				$num_imp = mysql_num_rows( $res_imp );		
				
				if( $num_imp > 0 ){	
				
					$ccoAnt_imp = '';
				
					$total_imp = 0;
					$totalAImprimir_imp = 0;
				
				
					$rows_imp = mysql_fetch_array( $res_imp );
					$ccoAnt_imp = $rows_imp[ 'Ubisac' ];
					
					for( $i_imp = 0; ;  ){
						
						if( $ccoAnt_imp == $rows_imp[ 'Ubisac' ] ){
						
							@$pacientes_imp[ $rows_imp[ 'Ctchis' ]."-".$rows_imp[ 'Ctcing' ]  ][ 'hab' ] = $rows_imp[ 'Ubihac' ];
							@$pacientes_imp[ $rows_imp[ 'Ctchis' ]."-".$rows_imp[ 'Ctcing' ]  ][ 'nom' ] = $rows_imp[ 'Pacno1' ]." ".$rows_imp[ 'Pacno2' ]." ".$rows_imp[ 'Pacap1' ]." ".$rows_imp[ 'Pacap2' ];
							@$pacientes_imp[ $rows_imp[ 'Ctchis' ]."-".$rows_imp[ 'Ctcing' ]  ][ 'tot' ]++;
							@$pacientes_imp[ $rows_imp[ 'Ctchis' ]."-".$rows_imp[ 'Ctcing' ]  ][ 'his' ] = $rows_imp[ 'Ctchis' ];
							@$pacientes_imp[ $rows_imp[ 'Ctchis' ]."-".$rows_imp[ 'Ctcing' ]  ][ 'med' ] = $rows_imp[ 'Artcom' ];
							
							@$pacientes_imp[ $rows_imp[ 'Ctchis' ]."-".$rows_imp[ 'Ctcing' ]  ][ 'ing' ] = $rows_imp[ 'Ctcing' ]; //nuevo
						
							$cconom_imp = $rows_imp[ 'Cconom' ];
							$total_imp++;
							$totalAImprimir_imp++;
							$rows_imp = mysql_fetch_array( $res_imp );
						}
						elseif( $total_imp > 0 ){	
						
							echo "<div id='accordion' class='desplegable'>";
							
							echo "<h3>$cconom_imp</h3>";					
							
							$total_imp = 0;
							$ccoAnt_imp = $rows_imp[ 'Ubisac' ];
							$i_imp++;
							
							//creo una fila mas con la información del paciente que se quiere imprimir
							if( true ){
								
								echo "<div>";
							
								echo "<table align='center'>";
								
								echo "<tr class='encabezadotabla' align='center'>";
								echo "<td>Habitaci&oacute;n</td>";
								echo "<td>Historia</td>";
								echo "<td>Nombre</td>";
								echo "<td>Cantidad</td>";
								echo "<td>Imprimir</td>";
								
								
								echo "</tr>";
								
								$k_imp = 0;
								//print_r($pacientes_imp);
								foreach( $pacientes_imp as $keyPacientes_imp => $hisPacientes_imp ){
									
									$class2 = "fila".($k_imp%2+1)."";
								
									echo "<tr class='$class2'>";
									
									echo "<td align='center'>";
									echo $hisPacientes_imp[ 'hab' ];
									echo "</td>";
									
									echo "<td align='center'>";
									echo $keyPacientes_imp;
									echo "</td>";
									
									echo "<td>";
									echo $hisPacientes_imp[ 'nom' ];
									echo "</td>";
									
									echo "<td align='center'>";
									echo $hisPacientes_imp[ 'tot' ];
									echo "</td>";
									
									// echo "<td align='center'><a href='impresionCTCProcedimientosNoPos.php?wemp_pmla=$wemp_pmla&imprimir=on&historia={$hisPacientes_imp[ 'his' ]}&reimprimir=on' target='_blank' onclick='recargar();'>Imprimir</a></td>";							
									echo "<td align='center'><a href='impresionCTCProcedimientosNoPos.php?wemp_pmla=$wemp_pmla&imprimir=on&historia={$hisPacientes_imp[ 'his' ]}&ingreso={$hisPacientes_imp[ 'ing' ]}&reimprimir=on' target='_blank' onclick='recargar();'>Imprimir</a></td>";							
									
									echo "</tr>";
									
									$k++;
								}
								
								echo "</table>";
								echo "</div>";
							}
							
							// $pacientes_imp = "";	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
							$pacientes_imp = array();	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
							
							echo "</div>";
							if( !$rows_imp ){
							
								break;
							}
						}				
					}			 
				}
				
		// ---------------------------
		
		// ---------------------------------------------------
		// 		CTC MEDICAMENTOS DILIGENCIADOS EN MIPRES
		// ---------------------------------------------------
		
		
		if($wfecha_inicialMipres == ""){
			$wfecha_inicialMipres = date('Y-m-d');
			
		}
		
		if($wfecha_finalMipres == ""){
			$wfecha_finalMipres = date('Y-m-d');
			
		}
		
		$rangoFechaMipres = "'".$wfecha_inicialMipres."' AND '".$wfecha_finalMipres."'";
			
		echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "<hr>";
			echo "<br><br>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";
						echo "<td bgcolor='#C2C9C2'><font size=5><b>CTC PROCEDIMIENTOS DILIGENCIADOS EN MIPRES</b></font></td>";
						echo "<input type=hidden id='tipo_consulta' name='tipo_consulta' value=''>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
			
			echo "<br>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";										
						echo "<td colspan=2 class=encabezadotabla align=center><b>Buscar:</b></td>";
					echo "</tr>";					
					
					echo "<tr>";
						echo "<td class=fila1 align=left>Fecha inicial:</td><td align=left><input type=text name='wfecha_inicialMipres' id='wfecha_inicialMipres' readonly='readonly' value='".$wfecha_inicialMipres."'></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td class=fila2 align=left>Fecha final:</td><td align=left><input type=text name='wfecha_finalMipres' id='wfecha_finalMipres' readonly='readonly' value='".$wfecha_finalMipres."'></td>";						
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan=2 align=center><input type='button' id='btn_consultar_fechaMipres' onclick='consultarMipresFecha();' value='Generar'></td>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
		
		
										
		$qCTCcontributivo = " SELECT a.Fecha_data,Ctchis,Ctcing,Ctctor,Ctcnro,Ctcite,Ctcmed,Ctcacc,Detcod,Detfec,Detesi,Ubihac,Ubisac,Cconom,Pacno1,Pacno2,Pacap1,Pacap2,Pacced,Pactid,b.Descripcion AS NombreProc,Ingres,Ingnre,c.Descripcion AS NombreMedico,Rolcod,Roldes,Ctcmip
								FROM ".$wbasedato."_000135 a, hce_000028,".$wbasedato."_000018,".$wbasedato."_000011,root_000036,root_000037,hce_000047 b,".$wbasedato."_000016,usuarios c,hce_000019,hce_000020
							   WHERE Ctcacc IN ('E','EM') 
								 AND a.Fecha_data BETWEEN ".$rangoFechaMipres."
								 AND Ctcest='on' 
								 AND Ctctor=Dettor
								 AND Ctcnro=Detnro
								 AND Ctcite=Detite
								 AND Detest='on'
								 AND ubihis = ctchis 
								 AND ubiing = ctcing 
								 AND ccocod = ubisac 
								 AND orihis = ubihis 
								 AND oriing = ubiing 
								 AND oritid = pactid 
								 AND oriced = pacced 
								 AND oriori = '".$wemp_pmla."' 
								 AND Detcod = b.Codigo
								 AND Inghis = Ctchis 
								 AND Inging = Ctcing  
								 AND c.codigo = Ctcmed 
								 AND Ctcmed = Usucod
								 AND Usurol = Rolcod
								 
								 UNION
								 
							  SELECT a.Fecha_data,Ctchis,Ctcing,Ctctor,Ctcnro,Ctcite,Ctcmed,Ctcacc,Detcod,Detfec,Detesi,Ubihac,Ubisac,Cconom,Pacno1,Pacno2,Pacap1,Pacap2,Pacced,Pactid,b.Descripcion AS NombreProc,Ingres,Ingnre,c.Descripcion AS NombreMedico,Rolcod,Roldes,Ctcmip 
								FROM ".$wbasedato."_000135 a, ".$wbasedato."_000159,".$wbasedato."_000018,".$wbasedato."_000011,root_000036,root_000037,hce_000047 b,".$wbasedato."_000016,usuarios c,hce_000019,hce_000020
							   WHERE Ctcacc IN ('E','EM') 
								 AND a.Fecha_data BETWEEN ".$rangoFechaMipres."
								 AND Ctcest='on' 
								 AND Ctctor=Dettor
								 AND Ctcnro=Detnro
								 AND Ctcite=Detite
								 AND Detest='on'
								 AND ubihis = ctchis 
								 AND ubiing = ctcing 
								 AND ccocod = ubisac 
								 AND orihis = ubihis 
								 AND oriing = ubiing 
								 AND oritid = pactid 
								 AND oriced = pacced 
								 AND oriori = '".$wemp_pmla."' 
								 AND Detcod = b.Codigo
								 AND Inghis = Ctchis 
								 AND Inging = Ctcing  
								 AND c.codigo = Ctcmed 
								 AND Ctcmed = Usucod
								 AND Usurol = Rolcod";
								
								
		$resCTCcontributivo=  mysql_query($qCTCcontributivo,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qCTCcontributivo." - ".mysql_error());
		$numCTCcontributivo = mysql_num_rows($resCTCcontributivo);	
		
		$arrayCTCmipres = array();
		$arrayPacientes = array();
		$arrayCcosto = array();
		$contadorMipres = 0;
		if($numCTCcontributivo > 0)
		{
			echo "	<table align='right' style='border: 1px solid black;border-radius: 5px;'>
							<tr>
							<td align='center' style='font-size:8pt'><b>Convenciones</b></td>
							</tr>
							<tr>
							<td><span class='procCancelado' style='border-radius:3px'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:7pt;vertical-align:top;'>&nbsp;Cancelado&nbsp;&nbsp;</span></td>
							</tr>
						</table>
						<br><br><br>";
						
			while($rowCTCcontributivo = mysql_fetch_array($resCTCcontributivo))
			{
				$arrayCcosto[$rowCTCcontributivo['Ubisac']] = $rowCTCcontributivo['Cconom'];
				
				$arrayCTCmipres[$contadorMipres]['historia'] = $rowCTCcontributivo['Ctchis'];
				$arrayCTCmipres[$contadorMipres]['ingreso']  = $rowCTCcontributivo['Ctcing'];
				$arrayCTCmipres[$contadorMipres]['nombre']   = $rowCTCcontributivo['Pacno1']." ".$rowCTCcontributivo['Pacno2']." ".$rowCTCcontributivo['Pacap1']." ".$rowCTCcontributivo['Pacap2'];
				$arrayCTCmipres[$contadorMipres]['servicio'] = $rowCTCcontributivo['Cconom'];
				$arrayCTCmipres[$contadorMipres]['cco']   		= $rowCTCcontributivo['Ubisac'];
				$arrayCTCmipres[$contadorMipres]['habitacion']    = $rowCTCcontributivo['Ubihac'];
				$arrayCTCmipres[$contadorMipres]['documento']     = $rowCTCcontributivo['Pacced'];
				$arrayCTCmipres[$contadorMipres]['tipoDocumento'] = $rowCTCcontributivo['Pactid'];
				$arrayCTCmipres[$contadorMipres]['codResponsable'] = $rowCTCcontributivo['Ingres'];
				$arrayCTCmipres[$contadorMipres]['responsable'] = $rowCTCcontributivo['Ingnre'];
				$arrayCTCmipres[$contadorMipres]['tipoOrden'] = $rowCTCcontributivo['Ctctor'];
				$arrayCTCmipres[$contadorMipres]['NroOrden'] = $rowCTCcontributivo['Ctcnro'];
				$arrayCTCmipres[$contadorMipres]['NroItem'] = $rowCTCcontributivo['Ctcite'];
				$arrayCTCmipres[$contadorMipres]['codProc'] = $rowCTCcontributivo['Detcod'];
				$arrayCTCmipres[$contadorMipres]['nombreProcedimiento'] = $rowCTCcontributivo['NombreProc'];
				$arrayCTCmipres[$contadorMipres]['fechaCTC'] = $rowCTCcontributivo['Fecha_data'];
				$arrayCTCmipres[$contadorMipres]['fechaProcedimiento'] = $rowCTCcontributivo['Detfec'];
				$arrayCTCmipres[$contadorMipres]['estado'] = $rowCTCcontributivo['Detesi'];
				$arrayCTCmipres[$contadorMipres]['usuario'] = $rowCTCcontributivo['Ctcmed'];
				$arrayCTCmipres[$contadorMipres]['nombreMedico'] = $rowCTCcontributivo['NombreMedico'];
				$arrayCTCmipres[$contadorMipres]['rolMedico'] = $rowCTCcontributivo['Rolcod']." - ".$rowCTCcontributivo['Roldes'];
				$arrayCTCmipres[$contadorMipres]['accionCTC'] = $rowCTCcontributivo['Ctcacc'];
				$arrayCTCmipres[$contadorMipres]['mipres'] = $rowCTCcontributivo['Ctcmip'];
				
				$contadorMipres++;
			}
			
		}
		else
		{
			echo "<center><br><br><b>NO SE ENCONTRARON CTC DILIGENCIADOS EN MIPRES</b></center>";
		}		
		
		
		if(count($arrayCcosto)>0)
		{
			foreach($arrayCcosto as $cco => $ccoNombre)
			{
				echo "<div id='accordion' class='desplegable' style='width:1450px'>";

					echo "<h3 align='left'>".$ccoNombre."</h3>";
					echo "<div>";
						echo '<table  style="width: 1200px;">';
							echo "<tr class='encabezadotabla' align='center'>";
								echo "<td colspan='5'>Datos del paciente</td>";
								echo "<td colspan='3'>Procedimiento</td>";
								echo "<td colspan='2'>Ordenado por:</td>";
								echo "<td rowspan='2'>Cambio de responsable</td>";
								echo "<td rowspan='2'>MIPRES</td>";
							echo "</tr>";
							
							echo "<tr class='encabezadotabla' align='center'>";
								echo "<td>Habitaci&oacute;n</td>";
								echo "<td>Historia</td>";
								echo "<td>Nombre</td>";
								echo "<td>Documento</td>";
								echo "<td>Responsable</td>";
								echo "<td>Fecha de la orden</td>";
								echo "<td>Descripcion</td>";
								echo "<td>Fecha a realizar</td>";
								echo "<td>Medico</td>";
								echo "<td>Rol</td>";
								
								
								
								echo "</tr>";
						
						foreach($arrayCTCmipres as $keyCTCmipres => $valueCTCmipres)
						{
							if($valueCTCmipres['cco']==$cco)
							{
								if ($fila_lista=='Fila1')
									$fila_lista = "Fila2";
								else
									$fila_lista = "Fila1";
								
								if($valueCTCmipres['estado']=="C")
								{
									$fila_lista = "procCancelado";
								}
								
								$accionCTCmipres = "NO";
								if($valueCTCmipres['accionCTC']=="EM")
								{
									$accionCTCmipres = "SI";
								}
								
								$onclickMipres = "onclick='verPrescripcionPorPacienteFec(\"".$valueCTCmipres['historia']."\",\"".$valueCTCmipres['ingreso']."\",\"".$valueCTCmipres['tipoDocumento']."\",\"".$valueCTCmipres['documento']."\",\"".$valueCTCmipres['fechaCTC']."\");'";
								$mipres = "<span class='presentacionMipres'>Ver prescripciones en mipres</span>";
								if($valueCTCmipres['mipres']!="")
								{
									$onclickMipres = "onclick='abrirModalMipres(\"".$valueCTCmipres['historia']."\",\"".$valueCTCmipres['ingreso']."\",\"".$valueCTCmipres['tipoDocumento']."\",\"".$valueCTCmipres['documento']."\",\"".$valueCTCmipres['fechaCTC']."\",\"".$valueCTCmipres['mipres']."\");'";
									$mipres = "<span class='presentacionMipres'>".$valueCTCmipres['mipres']."</span>";
								}
								
								
								
								echo "<tr class='".$fila_lista."' align='center'>";
									echo "<td>".$valueCTCmipres['habitacion']."</td>";
									echo "<td>".$valueCTCmipres['historia']."-".$valueCTCmipres['ingreso']."</td>";
									echo "<td>".$valueCTCmipres[ 'nombre' ]."</td>";
									echo "<td>".$valueCTCmipres['tipoDocumento']." ".$valueCTCmipres['documento']."</td>";
									echo "<td>".$valueCTCmipres['codResponsable'].' - '.$valueCTCmipres['responsable']."</td>";
									echo "<td>".$valueCTCmipres[ 'fechaCTC' ]."</td>";
									echo "<td>".utf8_decode($valueCTCmipres[ 'nombreProcedimiento' ])."</td>";
									echo "<td>".$valueCTCmipres[ 'fechaProcedimiento' ]."</td>";
									echo "<td>".$valueCTCmipres[ 'nombreMedico' ]."</td>";
									echo "<td>".$valueCTCmipres[ 'rolMedico' ]."</td>";
									echo "<td>".$accionCTCmipres."</td>";
									echo "<td align='center' ".$onclickMipres." style='cursor:pointer;'>".$mipres."</td>";
									
								echo "</tr>";
								
							}
						}
						echo '</table>';
					echo "</div>";	
				echo "</div>";
			}
		}
		
		// Modal mipres
		echo "<div id='dvAuxModalMipres' style='display:none'></div>";
		
		echo "<br>";
		echo "<br>";
		
		echo "	<div id='msjEspere' style='display:none;' align='center'>
					<br><br><br>
					<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...
				</div>";
		
		// ---------------------------------------------------
		
		echo "<INPUT type='hidden' value='on' name='imprimir' id='imprimir'>";
		echo "<INPUT type='hidden' value='$wemp_pmla' name='wemp_pmla' id='wemp_pmla'>";
		
		echo "<br>";
		echo "<table align='center'>";
		echo "<tr>";		
		echo "<td>";
		echo "<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
	}
	else{
		
		
		$control_impresion = "ctcimp = 'off'";
		
		if($reimprimir=='on'){
				
				//Log de reimpresion de medicamento de control.
				$sql = "INSERT INTO {$wbasedato}_000165(     Medico   ,            Fecha_data  ,      Hora_data         ,    Impusu  ,   Impori,  Impest ,  Seguridad    )
							                         VALUES (  '$wbasedato', '".date('Y-m-d')."',  '".date('H:i:s')."'  , '$wusuario',   'CTCProcedimientosNoPos', 'on', 'C-$wusuario' )";
							
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );	

				$control_impresion = "ctcimp = 'on'";
				
			}
		
		//Si la historia es vacia, significa que imprime todo
		if( empty( $historia ) ){
			$historia = '%';
		}
		
		//Si el cco es vacio, significa que imprime todo
		if( empty( $cco ) ){
			$cco = '%';
		}
		
		//Si el ingreso es vacio, significa que imprime todo
		if( empty( $ingreso ) ){
			$ingreso = '%';
		}
		
		//Se quita la agrupacion por el campo ctcpro Jonatan, para que pueda cambiarle el estado de impresion a todos los registros.
		//Consulto los articulos a imprimir
		$sql = "SELECT
					Codigo, Descripcion, Codcups, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ubisac, Ubihac, a.*, Ingtel, Ingnre, a.Fecha_data as fecCTCp, Ctcdcc
				FROM
					{$wbasedato}_000135 a, {$whce}_000017 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000016 g
				  WHERE $control_impresion
					AND	ctcest = 'on'
					AND orihis = ctchis
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND inghis = ubihis
					AND inging = ubiing
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = Codigo
					AND nuevo = 'on'
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco'
				UNION
				SELECT
					Codigo, Descripcion, Codcups, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ubisac, Ubihac, a.*, Ingtel, Ingnre, a.Fecha_data as fecCTCp, Ctcdcc
				FROM
					{$wbasedato}_000135 a, {$whce}_000047 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000016 g
				  WHERE $control_impresion
					AND	ctcest = 'on'
					AND orihis = ctchis
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND inghis = ubihis
					AND inging = ubiing
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = Codigo
					AND nuevo = 'off'
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco' 
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		// $qFechasCTC = "SELECT DISTINCT(Fecha_data)
						 // FROM ".$wbasedato."_000135
						// WHERE ctchis = '".$historia."' 
						  // AND ctcing = '".$ingreso."' 
						  // AND ctcest = 'on' 
						  // AND ctcimp = 'on'; " ;
		$qFechasCTC = "SELECT DISTINCT(Fecha_data)
						 FROM ".$wbasedato."_000135
						WHERE ctchis = '".$historia."' 
						  AND ctcest = 'on' 
						  AND ctcimp = 'on'; " ;
						  
		$resFechasCTC = mysql_query($qFechasCTC, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qFechasCTC . " - " . mysql_error());
		$numFechasCTC = mysql_num_rows($resFechasCTC);
		
		$arrayFechasCTC = array();
		if($numFechasCTC > 0)
		{
			while ($rowFechasCTC = mysql_fetch_array($resFechasCTC) )
			{
				$arrayFechasCTC[] = $rowFechasCTC[0];
			}
		}
		
		echo "<br>";
		echo "<center>";
			echo "<table>";
				echo "<tr>";										
					echo "<td  class=encabezadotabla align=center><b>Filtrar CTC por fecha:</b></td>";
				echo "</tr>";					
				
				echo "<tr>";
					echo "<td align=center class=fila1>";
						echo "<select id='filtrosFechasCTC' onChange='filtrarReimpresionCtc();'>";
							echo "<option value='0'>Todos</option>";
						foreach($arrayFechasCTC as $valueFechasCTC)
						{
							echo "<option value='".$valueFechasCTC."'>".$valueFechasCTC."</option>";
						}
						echo "</select>";
					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</center>";
		
		
		
		
		echo "<p align=center><input type='button' class='printer' value='Imprimir'><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";
		
		echo "<div class='areaimprimirCTC'>";
		echo "<div style='width:20cm' align='center'>";
		
		echo "<INPUT type='hidden' name='wemp_pmla' id='wemp_pmla' value='$wemp_pmla'>";
		
		for( $i = 0; $rowsCTC = mysql_fetch_array( $res ); $i++ ){
					
			//Consulto la información del medico
			$rowsMed = consultarInformacionMedico( $conex, $wbasedato, $rowsCTC[ 'Ctcmed' ] );
			$nombreMedico = $rowsMed[ 'Medno1' ]." ".$rowsMed[ 'Medno2' ]." ".$rowsMed[ 'Medap1' ]." ".$rowsMed[ 'Medap2' ];
			$especialidadMedico = $rowsMed[ 'Espnom' ];
			$nroDocumentoMedico = $rowsMed[ 'Meddoc' ];
			$registroMedico = $rowsMed[ 'Medreg' ];
			
			dimensionesImagen($rowsMed['Meddoc']);
			
			//$firmaMedico = $infoMedico[ 'Firma' ]; //			
			if(file_exists("../../images/medical/hce/Firmas/{$rowsCTC[ 'Ctcmed' ]}.png"))
				$firmaMedico = "<img src='../../images/medical/hce/Firmas/{$rowsCTC[ 'Ctcmed' ]}.png' width='$anchoimagen' heigth='$altoimagen'>";	//$infoMedico[ 'Firma' ]; //*****Aun no se sabe la tabla de firma
			else
				$firmaMedico = "";
			/************************************************************************************************/
			
			
			
			/************************************************************************************************
			 * Busco todos los datos necesarios antes de la impresión
			 ************************************************************************************************/
			$nombrePaciente = $rowsCTC[ 'Pacno1' ]." ".$rowsCTC[ 'Pacno2' ];
			$apellido1Paciente = $rowsCTC[ 'Pacap1' ];
			$apellido2Paciente = $rowsCTC[ 'Pacap2' ];
			
			$nroDocumento =  $rowsCTC[ 'Pacced' ];
			$tipDocumento =  $rowsCTC[ 'Pactid' ];
			
			$telefonoPaciente =  $rowsCTC[ 'Ingtel' ];
			$responsablePaciente =  $rowsCTC[ 'Ingnre' ];
			$edadPaciente = calcularEdad($rowsCTC[ 'Pacnac' ]);
			
			$historia = $rowsCTC[ 'Ctchis' ];
			$ingreso = $rowsCTC[ 'Ctcing' ];
			$fecCTCp = $rowsCTC[ 'fecCTCp' ];
			
			$fechaSolicitud = date("Y-m-d");	//$rowsCTC[ 'Ctcfge' ];
			
			// $diagnosticoCie10 = strip_tags( str_ireplace( "</OPTION>", "<br>", consultarDatosTablaHCE( $conex, $whce, "000051", 156, $historia, $ingreso ) ), "<br>" );
			$diagnosticoCie10 = consultarDiagnosticoPaciente($conex,$wbasedato,$historia,$ingreso,false);
			$diagnosticoCie10 = str_replace( "\n","<br>", $diagnosticoCie10);
			
			$descripcionCasoClinico = consultarDatosTablaHCE( $conex, $whce, "000051", 4, $historia, $ingreso );
			
			//Si el caso clinico de la tabla movhos135 es vacio, mostrara el de la historia.
			if($rowsCTC[ 'Ctcdcc' ] == ''){
			
				$descripcionCasoClinico = $descripcionCasoClinico;
			
			}else{
			
				$descripcionCasoClinico = $rowsCTC[ 'Ctcdcc' ];
			}
			
			
			$fechaProcedimiento1 = $rowsCTC[ 'Ctcpp1' ];
			$razonProcedimiento1 = $rowsCTC[ 'Ctcrp1' ];
			
			$fechaProcedimiento2 = $rowsCTC[ 'Ctcpp2' ];
			$razonProcedimiento2 = $rowsCTC[ 'Ctcrp2' ];
			
			$nombreProcedimiento = $rowsCTC[ 'Descripcion' ];
			$cups = $rowsCTC[ 'Codcups' ];
			
			$frecuenciaUso = $rowsCTC[ 'Ctcfus' ];
			$cantidadSolicitada = $rowsCTC[ 'Ctccas' ];
			$diasTratamiento = $rowsCTC[ 'Ctcdtt' ];
			
			$justificacion = $rowsCTC[ 'Ctcjus' ];
			
			
			$notaMedica = consultarNotaMedicaHCE( $conex, $whce, "000243", 6, $historia, $ingreso,$rowsCTC[ 'id' ] );
			
			/************************************************************************************
			 * tipo de atención
			 ************************************************************************************/
			$ambulatorio = "";
			$hospitalario = "";
			$urgencias = "";
			
			switch( strtoupper( $rowsCTC[ 'Ctctat' ] ) ){
				
				case 'HOSPITALARIO':{
					$ambulatorio = "";
					$hospitalario = "X";
					$urgencias = "";
				} break;
				
				case 'AMBULATORIO': {
					$ambulatorio = "X";
					$hospitalario = "";
					$urgencias = "";
				} break;

				case 'URGENCIAS': {
					$ambulatorio = "";
					$hospitalario = "";
					$urgencias = "X";
				} break;				
			}
			/************************************************************************************/
			
			/************************************************************************************
			 * Tipo de servicio
			 ************************************************************************************/
			$servicioUnico = "";
			$servicioConRepeticion = "";
			$servicioSucesivo = "";
			
			switch( strtoupper( $rowsCTC[ 'Ctctse' ] ) ){
				
				case 'UNICOCONREPETICION':{
					$servicioUnico = "";
					$servicioConRepeticion = "X";
					$servicioSucesivo = "";
				} 
				break;
				
				case 'SUCESIVO': {
					$servicioUnico = "";
					$servicioConRepeticion = "";
					$servicioSucesivo = "X";
				} 
				break;
				
				case 'UNICO': {
					$servicioUnico = "X";
					$servicioConRepeticion = "";
					$servicioSucesivo = "";
				} 
				break;
			}
			/************************************************************************************/
			
			
			/******************************************************************************************
			 * Proposito de lo solicitado
			 ******************************************************************************************/
			$promocion = "";
			$prevencion = "";
			$diagnostico = "";
			$tratamiento = "";
			$rehabilitacion = "";
			
			switch( strtoupper( $rowsCTC[ 'Ctcpso' ] ) ){
			
				case 'PROMOCION':{
					$promocion = "X";
				} 
				break;
				
				case 'PREVENCION': {
					$prevencion = "X";
				} 
				break;
				
				case 'DIAGNOSTICO': {
					$diagnostico = "X";
				} 
				break;
				
				case 'TRATAMIENTO': {
					$tratamiento = "X";
				} 
				break;
				
				case 'REHABILITACION': {
					$rehabilitacion = "X";
				} 
				break;
			}
			/******************************************************************************************/
			
			
			/****************************************************************************************************
			 * Impresion del formulario
			 ****************************************************************************************************/
			
			 
			 $dato_fecha = explode("-",$fecCTCp);
			 
			 $diaOrden = $dato_fecha[2];
			 $mesOrden = $dato_fecha[1];
			 $anioOrden = $dato_fecha[0];
			
			?>
			<div id="filtroFechasCTC_<?php echo $fecCTCp; ?>" class="<?php echo $fecCTCp; ?>">
			<table width="672" border="0" style="border-collapse:collapse" cellpadding="0" >
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td width="165"><img src='../../images/medical/root/<?php echo $institucion->baseDeDatos; ?>.jpg' width="148" heigth="53"></td>
					<td width="367" align="center">
						<p align="center"><h2>JUSTIFICACION INSUMOS Y PROCEDIMIENTOS NO POS</h2></p>
						<b>Fecha </b> &nbsp; <table width="240" border="1" style="border-collapse:collapse;border-style:solid;height:21px" cellpadding="0"><tr><td align="center"><b>D&iacute;a </b> </td><td align="center"><?php echo $diaOrden; ?> </td><td align="center"><b>Mes </b> </td><td align="center"><?php echo $mesOrden; ?> </td><td align="center"><b> A&ntilde;o </b> </td><td align="center"><?php echo $anioOrden; ?> </td></tr></table><br />
					</td>
				  </tr>
				</table>				
				<br /><br />
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td width="270" colspan="3"><b>Nombre del paciente</b></td>
					<td width="221" colspan="3"><b>Primer Apellido</b></td>
					<td width="221" colspan="2"><b>Segundo Apellido</b></td>
				  </tr>
				  <tr>
					<td colspan="3" class="texto"><?php echo $nombrePaciente; ?></td>
					<td colspan="3" class="texto"><?php echo $apellido1Paciente; ?></td>
					<td colspan="2" class="texto"><?php echo $apellido2Paciente; ?></td>
				  </tr>
				  <tr>
					<td width="100"><b>Nro Identificaci&oacute;n </b></td>
					<td colspan="3" class="texto"><?php echo $nroDocumento; ?></td>
					<td width="80" ><b>Tipo Id </b></td>
					<td class="texto"><?php echo $tipDocumento; ?></td>
					<td width="120"><b>Nro Historia Cl&iacute;nica </b></td>
					<td class="texto"><?php echo $historia." - ".$ingreso; ?></td>
				  </tr>
				  <tr>
					<td><b>Edad </b></td>
					<td class="texto"><?php echo $edadPaciente; ?></td>
					<td><b>EPS </b></td>
					<td colspan="3" class="texto"><?php echo $responsablePaciente; ?></td>
					<td><b>Tel&eacute;fono </b></td>
					<td class="texto"><?php echo $telefonoPaciente; ?></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td width="112"><b>Diagnóstico: </b></td>
					<td width="600" colspan="7" class="texto"><?php echo $diagnosticoCie10;?></td>
				  </tr>
				  <tr>
					<td width="162" colspan="2">Solicitud de Tratamiento: </td>
					<td width="150" align="center"> Ambulatorio: </td>
					<td width="33" align="center" class="texto"> <?php echo $ambulatorio;?> </td>
					<td width="150" align="center"> Hospitalario: </td>
					<td width="33" align="center" class="texto"> <?php echo $hospitalario;?> </td>
					<td width="150" align="center"> Urgencias: </td>
					<td width="34" align="center" class="texto"> <?php echo $urgencias;?> </td>
				  </tr>
				</table>
				</td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td><b>Caso Clínico:</b></td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td height="31px" class="texto"><?php echo $descripcionCasoClinico;?></td>
				  </tr>
				</table>
				</td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>


			  <tr>
				<td><b>PROCEDIMIENTO O INSUMO NO POS SOLICITADO</b></td>
			  </tr>
			  <tr>
				<td style="border-style:solid;border-top-width:1px;border-left-width:1px;border-right-width:1px;border-bottom-width:1px">
				  <table width="712" border="0">
					<tr>
					  <td width="420" class="texto">Nombre: <b><?php echo $nombreProcedimiento;?></b></td>
					  <td width="282" class="texto">CUPS ó Reg. INVIMA: <b><?php echo $cups;?></b></td>
					</tr>
				  </table>      
				</td>
			  </tr>
				  
			  <tr>
				<td>&nbsp;</td>
			  </tr>

			  <tr>
				<td><b>JUSTIFICACION PARA EL USO DEL PROCEDIMIENTO O INSUMO NO POS SOLICITADO</b></td>
			  </tr>
			  <tr>
				<td style="border-style:solid;border-top-width:1px;border-left-width:1px;border-right-width:1px;border-bottom-width:1px" class="texto">
					<?php echo $justificacion;?>
				</td>
			  </tr>
			  
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td><b>OPCIONES POS (DESCRIBIRLAS)</b></td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td width="162">Existen Opciones POS: </td>
					<td width="70" align="center"> No: </td>
					<td width="30" align="center"> X </td>
					<td width="70" align="center"> Si: </td>
					<td width="30" align="center">  </td>
					<td width="350" align="center">  </td>
				  </tr>
				  <tr>
					<td colspan="6"> 
						Razones para no utilizarlas:<br />
						<?php echo '';?> 
					</td>
				  </tr>
				</table>
				</td>
			  </tr>

			  
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td><b>PROCEDIMIENTO O TECNOLOG&IacuteA NO POS ORDENADA</b></td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td height="31px" class="texto"><?php echo $notaMedica;?></td>
				  </tr>
				</table>
				</td>
			  </tr>
			  
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse">
				  <tr>
					<td width="322"><p>Nombre Médico: <b><?php echo $nombreMedico;?></b></p>
					  <p>Documento identidad: <b><?php echo $nroDocumentoMedico;?></b></p>
					  <p>Especialidad: <b><?php echo $especialidadMedico;?></b></p>
					  <p>Registro Médico: <b><?php echo $registroMedico;?></b></p></td>
					<td width="184" align="center">
					<table width="165" height="120" border="0">
					  <tr>
						<td width="182" height="93" style="border-bottom-width:1px;border-bottom-style:solid">&nbsp;<b><?php echo $firmaMedico;?></b></td>
					  </tr>
					  <tr>
						<td align="center">Firma y sello</td>
					  </tr>
					</table></td>
					<td width="184">
					Fecha de Recibo:
					  <table width="165" height="101" border="0" cellspacing="0" align="center">
					  <tr>
						<td height="70" style="border-bottom-width:1px;border-bottom-style:solid">&nbsp;</td>
					  </tr>
					  <tr>
						<td height="23" align="center">Firma Recibo</td>
					  </tr>
					</table></td>
				  </tr>
				</table>
				</td>
			  </tr>			 
			</table>			
			<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
			  <tbody>
				<tr>
				  <td style="text-align:center;" calss="descripcion">
					<b>- Firmado electrónicamente -</b>
				  </td>
				</tr>
			  </tbody>
			</table>
			<?php
			 
			/****************************************************************************************************/
			
			echo "<div style='page-Break-After:always'></div>";
			
			//marco como impreso el articulo
			cambiarEsadoImpresionPorId( $conex, $wbasedato, $rowsCTC[ 'id' ], $wusuario );
			echo "<div id='dv{$rowsCTC[ 'Ctchis' ]}_{$rowsCTC[ 'Ctcing' ]}_{$rowsCTC[ 'Ctcpro' ]}'>";
			echo "</div>";
			echo "<script>";
			echo "consultarPrescripcionCTC( '{$rowsCTC[ 'Ctchis' ]}', '{$rowsCTC[ 'Ctcing' ]}', '{$rowsCTC[ 'Ctcpro' ]}', 'dv{$rowsCTC[ 'Ctchis' ]}_{$rowsCTC[ 'Ctcing' ]}_{$rowsCTC[ 'Ctcpro' ]}', '$nroDocumentoMedico', 'ctcNoPos');";
			echo "</script>";
			
			echo "</div>";
		}
		
			echo "</div>";
			
		echo "</div>";
		
	}
	
	echo "</form>";
}


?>
