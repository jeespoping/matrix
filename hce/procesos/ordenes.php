<?php
include_once("conex.php");  header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<html>
<head>
<title>HCE - [ORDENES]</title>

<script>window.onerror=null</script>

<!-- JQUERY para los tabs -->
<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
<link type="text/css" href="../../../include/root/jquery.simpletree.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<link type='text/css' href='../../../include/root/matrix.css' rel='stylesheet'>		<!-- HCE -->
<link type='text/css' href='../../../include/root/burbuja.css' rel='stylesheet'>		<!-- HCE -->
<link type='text/css' href="../../../include/root/jquery.ui.timepicker.css"  rel="stylesheet" />


<link type='text/css' href='HCE.css' rel='stylesheet'>		<!-- HCE -->
<script type='text/javascript' src='HCE.js' ></script>		<!-- HCE -->

<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>	<!-- Acordeon -->
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<script type="text/javascript" src="../../../include/root/modernizr.custom.js"></script>

<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<!-- <script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script> -->	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type="text/javascript" src="../../../include/root/ui.datepicker.js"></script>
<script type="text/javascript" src="../../../include/root/burbuja.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>


<!-- Fin JQUERY para los tabs -->

<!-- Include de codigo javascript propio de mensajeria Kardex -->
<script type="text/javascript" src="../../../include/movhos/mensajeriaKardex.js?v=<?=md5_file('../../../include/movhos/mensajeriaKardex.js');?>"></script>
<script type="text/javascript" src="./generarCTCprocedimientos.js?v=<?=md5_file('generarCTCprocedimientos.js');?>"></script>
<script type="text/javascript" src="./generarCTCOrdenes.js?v=<?=md5_file('generarCTCOrdenes.js');?>"></script>

<script type="text/javascript" src="../../../include/movhos/alertas.js?v=<?=md5_file('../../../include/movhos/alertas.js');?>"></script>

<script type="text/javascript">
	$(document).ready(function(){  inicializarJquery();  });
</script>

<!-- Include de codigo javascript propio de la orden -->
<!-- <script type="text/javascript" src="ordenes.js"></script> -->
<script type="text/javascript" src="ordenes.js?v=<?=md5_file('ordenes.js');?>"></script>

<style>

/* STyle para jAlert Actualizar navegador */
#popup_container.actualizar {
    background: #E0FCEA none repeat scroll 0 0;
    border-color: #113f66;
    color: black;
    font-family: Verdana,serif;
}

#popup_container.actualizar h1 {
    background: #7ECBA5 none repeat scroll 0 0;
    border-color: #7ecb00;
    color: black;
    font-family: Verdana,serif;
}

#popup_container.actualizar #popup_content.alert{
	background-image: url("../../../include/root/alerta_confirm.gif");
}



/* Misc visuals
----------------------------------*/

/* Overlays */
.ui-widget-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}
.ui-resizable {
	position: relative;
}
.ui-resizable-handle {
	position: absolute;
	font-size: 0.1px;
	display: block;
}
.ui-resizable-disabled .ui-resizable-handle,
.ui-resizable-autohide .ui-resizable-handle {
	display: none;
}
.ui-resizable-n {
	cursor: n-resize;
	height: 7px;
	width: 100%;
	top: -5px;
	left: 0;
}
.ui-resizable-s {
	cursor: s-resize;
	height: 7px;
	width: 100%;
	bottom: -5px;
	left: 0;
}
.ui-resizable-e {
	cursor: e-resize;
	width: 7px;
	right: -5px;
	top: 0;
	height: 100%;
}
.ui-resizable-w {
	cursor: w-resize;
	width: 7px;
	left: -5px;
	top: 0;
	height: 100%;
}
.ui-resizable-se {
	cursor: se-resize;
	width: 12px;
	height: 12px;
	right: 1px;
	bottom: 1px;
}
.ui-resizable-sw {
	cursor: sw-resize;
	width: 9px;
	height: 9px;
	left: -5px;
	bottom: -5px;
}
.ui-resizable-nw {
	cursor: nw-resize;
	width: 9px;
	height: 9px;
	left: -5px;
	top: -5px;
}
.ui-resizable-ne {
	cursor: ne-resize;
	width: 9px;
	height: 9px;
	right: -5px;
	top: -5px;
}
.ui-selectable-helper {
	position: absolute;
	z-index: 100;
	border: 1px dotted black;
}
.ui-accordion .ui-accordion-header {
	display: block;
	cursor: pointer;
	position: relative;
	margin-top: 2px;
	padding: .5em .5em .5em .7em;
	min-height: 0; /* support: IE7 */
}


.ui-datepicker {
	width: 17em;
	padding: .2em .2em 0;
	display: none;
}
.ui-datepicker .ui-datepicker-header {
	position: relative;
	padding: .2em 0;
}
.ui-datepicker .ui-datepicker-prev,
.ui-datepicker .ui-datepicker-next {
	position: absolute;
	top: 2px;
	width: 1.8em;
	height: 1.8em;
}
.ui-datepicker .ui-datepicker-prev-hover,
.ui-datepicker .ui-datepicker-next-hover {
	top: 1px;
}
.ui-datepicker .ui-datepicker-prev {
	left: 2px;
}
.ui-datepicker .ui-datepicker-next {
	right: 2px;
}
.ui-datepicker .ui-datepicker-prev-hover {
	left: 1px;
}
.ui-datepicker .ui-datepicker-next-hover {
	right: 1px;
}
.ui-datepicker .ui-datepicker-prev span,
.ui-datepicker .ui-datepicker-next span {
	display: block;
	position: absolute;
	left: 50%;
	margin-left: -8px;
	top: 50%;
	margin-top: -8px;
}
.ui-datepicker .ui-datepicker-title {
	margin: 0 2.3em;
	line-height: 1.8em;
	text-align: center;
}
.ui-datepicker .ui-datepicker-title select {
	font-size: 1em;
	margin: 1px 0;
}
.ui-datepicker select.ui-datepicker-month-year {
	width: 100%;
}
.ui-datepicker select.ui-datepicker-month,
.ui-datepicker select.ui-datepicker-year {
	width: 49%;
}
.ui-datepicker table {
	width: 100%;
	font-size: .9em;
	border-collapse: collapse;
	margin: 0 0 .4em;
}
.ui-datepicker th {
	padding: .7em .3em;
	text-align: center;
	font-weight: bold;
	border: 0;
}
.ui-datepicker td {
	border: 0;
	padding: 1px;
}
.ui-datepicker td span,
.ui-datepicker td a {
	display: block;
	padding: .2em;
	text-align: right;
	text-decoration: none;
}
.ui-datepicker .ui-datepicker-buttonpane {
	background-image: none;
	margin: .7em 0 0 0;
	padding: 0 .2em;
	border-left: 0;
	border-right: 0;
	border-bottom: 0;
}
.ui-datepicker .ui-datepicker-buttonpane button {
	float: right;
	margin: .5em .2em .4em;
	cursor: pointer;
	padding: .2em .6em .3em .6em;
	width: auto;
	overflow: visible;
}
.ui-datepicker .ui-datepicker-buttonpane button.ui-datepicker-current {
	float: left;
}

/* with multiple calendars */
.ui-datepicker.ui-datepicker-multi {
	width: auto;
}
.ui-datepicker-multi .ui-datepicker-group {
	float: left;
}
.ui-datepicker-multi .ui-datepicker-group table {
	width: 95%;
	margin: 0 auto .4em;
}
.ui-datepicker-multi-2 .ui-datepicker-group {
	width: 50%;
}
.ui-datepicker-multi-3 .ui-datepicker-group {
	width: 33.3%;
}
.ui-datepicker-multi-4 .ui-datepicker-group {
	width: 25%;
}
.ui-datepicker-multi .ui-datepicker-group-last .ui-datepicker-header,
.ui-datepicker-multi .ui-datepicker-group-middle .ui-datepicker-header {
	border-left-width: 0;
}
.ui-datepicker-multi .ui-datepicker-buttonpane {
	clear: left;
}
.ui-datepicker-row-break {
	clear: both;
	width: 100%;
	font-size: 0;
}

/* RTL support */
.ui-datepicker-rtl {
	direction: rtl;
}
.ui-datepicker-rtl .ui-datepicker-prev {
	right: 2px;
	left: auto;
}
.ui-datepicker-rtl .ui-datepicker-next {
	left: 2px;
	right: auto;
}
.ui-datepicker-rtl .ui-datepicker-prev:hover {
	right: 1px;
	left: auto;
}
.ui-datepicker-rtl .ui-datepicker-next:hover {
	left: 1px;
	right: auto;
}
.ui-datepicker-rtl .ui-datepicker-buttonpane {
	clear: right;
}
.ui-datepicker-rtl .ui-datepicker-buttonpane button {
	float: left;
}
.ui-datepicker-rtl .ui-datepicker-buttonpane button.ui-datepicker-current,
.ui-datepicker-rtl .ui-datepicker-group {
	float: right;
}
.ui-datepicker-rtl .ui-datepicker-group-last .ui-datepicker-header,
.ui-datepicker-rtl .ui-datepicker-group-middle .ui-datepicker-header {
	border-right-width: 0;
	border-left-width: 1px;
}

/* Component containers
----------------------------------*/

.ui-widget-content {
	border: 1px solid #ffffff;
	background: #ffffff url(../../images/medical/hce/ui-bg_flat_75_ffffff_40x100.png) 50% top repeat-x;
	color: #ffffff;
}





/* Misc visuals
----------------------------------*/

/* Corner radius */
.ui-corner-all,
.ui-corner-top,
.ui-corner-left,
.ui-corner-tl {
	border-top-left-radius: 6px;
}
.ui-corner-all,
.ui-corner-top,
.ui-corner-right,
.ui-corner-tr {
	border-top-right-radius: 6px;
}
.ui-corner-all,
.ui-corner-bottom,
.ui-corner-left,
.ui-corner-bl {
	border-bottom-left-radius: 6px;
}
.ui-corner-all,
.ui-corner-bottom,
.ui-corner-right,
.ui-corner-br {
	border-bottom-right-radius: 6px;
}


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
		
.fondoAlertaConfirmar            
{
     background-color: #8181F7;
     color: #000000;
     font-size: 10pt;
}

.fondoAlertaEliminar            
{
     background-color: #F5D0A9;
     color: #000000;
     font-size: 10pt;
}
.tituloFamilia                     
{
     background-color: #B0C4DE; 
	 font-family: verdana;
	 font-size: 10pt;
	 overflow: hidden;
	 text-transform: uppercase;
	 font-weight: bold;
	 height: 21px;
	 border-top-color: #2A5DB0;
	 border-top-width: 1px;
	 border-left-color: #2A5DB0;
	 border-left-width: 1px;
	 border-right-color: #2A5DB0;
	 border-bottom-color: #2A5DB0;
	 border-bottom-width: 1px;
	 margin: 2pt;
}
.esCompuesta                                    
{
     background-color: #6495ED;
     color: #000000;
     font-size: 10pt;
}

.classIC{
	background-color: #F5DA81;
}

td{
	font-size: 10pt;
}
.opacar img {filter:alpha(opacity=50);-moz-opacity: 0.5;opacity: 0.5;}
.aclarar img {filter:alpha(opacity=100)3000;-moz-opacity: 1.0;opacity: 1.0;}

div.growlUI h1, div.growlUI h2 {
	color: white; padding: 5px 5px 5px 75px; text-align: left
}


#dvModalMedControl{
	background-color: #FAFAFA;
}

#dvModalMedControl .encabezadotabla{
	padding:10px;
}


#dvModalMedControl .clInfoPaciente{
	margin: 10px;
}

#dvMedControl{
	float:left;
	width: 300px;
	height: 100%;
	overflow: auto;
}

#dvMedControl span{
	float: left;
	margin: 10px;
	padding: 10px;
	border: 1px solid black;
	background: #E8EEF7;
	width: 250px;
	
	-webkit-box-shadow: 2px 2px 5px #999;
	-moz-box-shadow: 2px 2px 5px #999;
	filter: shadow(color=#999999, direction=135, strength=2);
	
	border-radius: 10px 10px 10px 10px;
	-moz-border-radius: 10px 10px 10px 10px;
	-webkit-border-radius: 10px 10px 10px 10px;
	border: 0px solid #000000;
}

#dvMedControl .selected{
	background: #C3D9FF;
}

#dvImpresionMedControl{
	float:left;
	width: 710px;
	height: 100%;
}

#dvModalMedControl .btnCerrar{
	
	height: 25px;
	background: #C3D9FF;
	width: 200px;
	font-weight: bold;
	
	-webkit-box-shadow: 2px 2px 5px #999;
	-moz-box-shadow: 2px 2px 5px #999;
	filter: shadow(color=#999999, direction=135, strength=2);
	
	border-radius: 10px 10px 10px 10px;
	-moz-border-radius: 10px 10px 10px 10px;
	-webkit-border-radius: 10px 10px 10px 10px;
	border: 0px solid #000000;
}












.mad-cell-1{
	width : calc( 100%/12 );
}
.mad-cell-2{
	width : calc( 100%/12*2 );
}
.mad-cell-3{
	width : calc( 100%/12*3 );
}
.mad-cell-4{
	width : calc( 100%/12*4 );
}
.mad-cell-5{
	width : calc( 100%/12*5 );
}
.mad-cell-6{
	width : calc( 100%/12*6 );
}
.mad-cell-7{
	width : calc( 100%/12*7 );
}
.mad-cell-8{
	width : calc( 100%/12*8 );
}
.mad-cell-9{
	width : calc( 100%/12*9 );
}
.mad-cell-10{
	width : calc( 100%/12*10 );
}
.mad-cell-11{
	width : calc( 100%/12*11 );
}
.mad-cell-12{
	width : 100%);
}




.mad, .mad-content{
	display: flex;
	flex-direction: column;
}

.mad-content{
	margin:5px;
}

.mad-actions,.mad-section-title{
	width : 100%;
}

.mad-title{
	font-size: 20pt;
    font-weight: bold;
    background-color: beige;
    margin: 0 0 10 0;
}

.mad-title-close mad-close{}

.mad-content{}

.mad-actions{}

.mad-accept{
	background-color: cornflowerblue;
	border: none;
	color: white;
	padding: 5px 10px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	margin: 4px 2px;
	cursor: pointer;
	width: 100px;
	
	border-radius: 5px 5px 5px 5px;
	-moz-border-radius: 5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
	border: 0px solid #000000;
}

.mad-add:hover, .mad-remove:hover, .mad-close:hover, .mad-accept:hover{
	font-weight: bold;
}

.mad-remove.mad-disabled:hover, .mad-close.mad-disabled:hover,.mad-accept.mad-disabled:hover{
	font-weight: normal;
}

.mad-remove, .mad-close{
	background-color: lightcoral;
	border: none;
	color: white;
	padding: 5px 10px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	margin: 4px 2px;
	cursor: pointer;
	width: 100px;
	
	border-radius: 5px 5px 5px 5px;
	-moz-border-radius: 5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
	border: 0px solid #000000;
}

.mad-add{
	background-color: limegreen;
	border: none;
	color: white;
	padding: 5px 10px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	margin: 4px 2px;
	cursor: pointer;
	width: 100px;
	
	border-radius: 5px 5px 5px 5px;
	-moz-border-radius: 5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
	border: 0px solid #000000;
}

.mad-row{
	display: flex;
	flex-direction: row;
	margin : 2px;
}

.mad-item-title{
	font-size: 10pt;
	font-weight: bold;
	padding: 5px;
}

.mad-item-title > label{
	width: 100%;
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	background: #C3D9FF;
}

.mad-item{
	padding: 5px;
}

.mad-item > select{
	width:100%;
	height:100%;
}

.mad-disabled{
	background-color: lightgray;
}




.clfamatctab{
	margin: 15px 0;
	border-collapse: collapse;
}

.clfamatctab > tbody > tr > td{
	border: 1px solid black;
	padding: 2px 5px;
}


</style>

</head>

<body>
<?php
/*BS'D
 * CONSULTA Y GENERACION DE ORDENES PARA HCE
 * Autor: Mauricio Sánchez Castaño.
 * /************************************************
	ADVERTENCIA: 
		NO USE funciones que activen el evento onBeforeUnload en lo operativo, ya que esto dispara la grabacion del orden.  Ej:
		->void(0) en los links use mejor href='#null'
	*************************************************
	*
	* Modificaciones:
	* Junio 30 de 2020 		Edwin	- Se muestra la fecha y hora de toma de muestra
	* Junio 23 de 2020 		Edwin	- Se muestra la justificación de cancelado en laboratorio bajo ele estado de la orden
	* Mayo 19 de 2020 		Edwin	- Se llama a la función cambioEstadoInteroperabilidad para que se haga el cambio de estados por interoperabilidad automáticamente. 
	*								  La función cambioEstadoInteroperabilidad se encuentra en el include funcionesGeneralesEnvioHL7 en interoperabilidad/procesos/
	* Mayo 12 de 2020 		Edwin	- Solo se permite tomar muestras en la fecha en que se realiza el examen, en caso contrario se muestra en tiempo faltante en que se puede tomar
	*								  muestras en días
	* Mayo 7 de 2020 		Edwin	- Se oculta el campo imprimir en el encabezado de la tabla de ordenes cuando el usuario no tiene el permiso correspondiente
	* Mayo 4 de 2020 		Edwin	- Se hacen cambios varios para la interoperabilidad con laboratorio por centro de costos y POCT
	* Febrero 14 de 2020 	Edwin	- Si un tipo de orden tiene interoperabilidad y el estudio es ofertado, no se permite mover los estados del estudio
    * Enero 22 de 2019		Edwin	- Se actuliza el tipo de protocolo de los articulos (kadpro de movhos_000054) del día anterior para que 
	*								  los articulos creados desde kardex pasen a ordenes.Esta acción se realiza con llamando a la función actualizarTipoProtocolo
	* Octubre 30 de 2019	Edwin 	- Se hacen modificaciones varias para interoperabilidad con ordenes (LABORATORIO, HIRUKO)
	* Julio 9 de 2018 Edwin 		- Los articulos anteriores en las pestañas de medicamentos se buscan por ajax
	* Julio 3 de 2018 Jessica		- Se comenta la información que contiene la auditoría ya que se consulta por ajax una vez se le de clic sobre la pestaña
	*								  de auditoría
	* Marzo 6 de 2018 Jessica		- Se agrega un parámetro en el llamado a la función llamarIframeAlerta() con la posición del div ya que los estilos se 
	*								  consultan en root_000051 en los scripts de alertas.
    * Febrero 13 de 2018	Edwin	- Lo medicamentos ordenados desde una ayuda diagnóstica o desde protocolo comienzan en la ronda actual.
    * Diciembre 26 de 2017 Jessica	- Cada vez que se abre ordenes se consulta el diagnostico en movhos_000243 para que en movhos_000053 quede actualizado.
    * Noviembre 27 de 2017 Jessica	- Si en la pestaña Ordenes hay procedimientos pendientes de lectura (Detpen=on) se muestran de otro color. 
	*								- Se envía el número de la pestaña como parámetro en la función mostrar_mensaje() y se agrega el campo oculto 
	*								  pestanasVistas para que en ordenes.js cada vez que haga clic en una pestaña se guarde en pestanasVistas y 
	*								  así poder marcar como leído solo lo que pertenezca a las pestañas vistas.
	* Agosto 14 de 2017 Jessica		- Para los ctc de responsables contributivos, al cerrar la modal de las prescripciones en mipres
	*								  se consume el web service del ministerio, se relaciona el consecutivo en movhos_000134 o movhos_000135
	*								  y se guarda la prescripcion en las tablas del grupo mipres.
	* Junio 8 de 2017 Jessica		- Se agrega el link para actualizar la firma electrónica si esta vencida la actual.
	* Abril 27 de 2017 Edwin MG		- Se muestra el formato de control a los médicos una vez grabe la orden y se obliga a ingresar un dx para dicha impresión si no se encuentra dx en la HCE.
	* Abril 03 de 2017 Edwin MG		- Se agrega prescripción pediatrica para los articulos antibióticos. Cuando un articulo antibiótico es compuesto, el médico puede
	*								  decidir si la prescripción es pediatica o no.
	* Enero 24 de 2017 Edwin MG		- Todo articulo ordenado desde urgencias queda como ENVIAR y se llama al proceso de cambio de ENVIAR a NO ENVIAR 
	*								  para articulos LEV e IC si el paciente esta en piso
	* Diciembre 5 de 2016 Jessica	- Se agrega programa de alergias y alertas y se comenta en la pestaña de Informacion General los antecedentes alergicos
	*								- Se agrega indicador de responsable eps contributivo
	* Septiembre 19 de 2016 Edwin	- Se agrega parametros versionMozila y validarBrowserOrdenes cómo campos ocultos
	* Agosto 03 de 2016 Edwin		- Se muestra alerta con color diferente para los antibioticos cuando estan por acabar por dosis máxima.
	* 								- Se agregar filtro antibiótico para los antibióticos (profilaxis y tratamiento)
	* Junio 02 de 2016 Edwin		- Se evita que los medicamentos de tipo liquido, indicados en movhos_000066 no les salga la pestaña de NPT.
	* Mayo 11 de 2016 Jessica		- Se añade la funcionalidad de procedimientos agrupados, modal que permite seleccionar varios procedimientos a la vez, 
	*									las acciones que toma la orden general deben aplicarse para cada uno de los procedimientos que internamente continuan 
	*									funcionando como siempre.
	*									Por cada procedimiento se pueden adicionar medicamentos y son obligatorios de acuerdo a la configuración de cada procedimiento.
										Cuando se cambia el estado a realizado, pendiente de resultado o cancelado se suspenden los medicamentos asociados.
										Si uno de los medicamentos tiene como minimo una aplicación el procedimiento no podrá ser cancelado.
	*	Mayo 04 de 2016 Jessica		- Para pedir los ctc por cambio de responsable se valida que no sea una empresa definida en empresasConfirmanCTC de root_000051
	*								- Se cambia el campo oculto entidad_responsable por el nit de la empresa(cliame_000024) y no el codigo del responsable.
	*	Enero 21 de 2015 Jessica	- Se llenan las cadenas $cadenaGuardadosSinCTC y $strExamenesPendientesCTC con los medicamentos y procedimientos ordenados y estan sin CTC
										por cambio de responsable.
	*	Julio 3 de 2015 Jonatan		- Se corrige el checkbox de impresion de ayudas diagnosticas ya que en algunos casos no se queria imprimir un examen y aun
										asi seguia saliendo.
	*	Julio 2 de 2015 Jonatan		- Si el estado del examen esta inactivo en el campo Eexenf, se inactivara en el seleccionador.
	*	Junio 6 de 2015 Jonatan		- Se muestra la observacion asociada a la dieta cuando las ordenes son de lectura.
	* 	Junio 1 de 2015 Jonatan 	- Se controla el acceso a las ordenes por medio de sesiones, por lo tanto no se podra ingresar a dos ordenes diferentes al mismo tiempo.
	*	Mayo 26 de 2015 Jonatan		- Se modifica el programa para que al ser abierto desde gestion de enfermeria como una modal tenga el mismo funcionamiento
										de una ventana, osea que guarde correctamente.
	*	Mayo 20 de 2015 Jonatan		- Se controla el boton de lenguaje americas para que pueda ser inactivado en cualquier momento, ademas se muestra
										un mensaje emergente avisando cuando se termina el ingreso de examenes por este medio.
									- Se cambiar algunos alert javascript por alerts jquery para que no se genere el cajon por defecto del navegador.
									- Se repara el areglo que muestra los examenes realizados para que la llave sea detfec y no ordfec.	
	*   Abril 1 de 2015 Jonatan		- Se muestra la informacion de medidas generales cuando el kardex no es editable.
	*	Marzo 30 de 2015 Edwin MG.	- Se permite editar los campos de antecedentes de la pestaña de información general.
	*  	Marzo 17 de 2015 Jonatan	- Permite ingresar a las ordenes despues de 30 minutos de estar abierto por otro usuario, parametros en la MinutosInactividadMaximoOrdenes, ademas se borrado
										el log de ingresos despues de 30 dias, parametro DiasLogOrdenes en la root_000051.
	*	Marzo 16 de 2015 Edwin MG.	- Las alertas se muestran al lado derecho
	* 	Marzo 12 de 2015 Jonatan	- Se controla la generacion de orden de control con un parametro en la tabla root_000051, MedicamentosControlAuto, si esta en on se generan ordenes de control
										automaticas sino seran manuales.
	*	Marzo 9 de 2015 Jonatan		 - Se aplica utf-8 a las sondas cateteres y drenes e interconsultas.
	* 	Febrero 26 de 2015 Edwin MG. - Permite grabar medicamentos a la misma fecha y hora siempre y cuando el otro medicamento este suspendido.
	*								 - La lista de medicamentos al seleccionar un LEV o una NUTRICION PARENTERAL se mejora. Si se escribe un número
	*								   en la dosis el checkbox se chulea automáticamente.
	*  	Febrero 25 de 2015 Jonatan.		- Se controla que al seleccionar la x roja por parte de la secretaria si se cierre la orden correctamente.
	*	Febrero 19 de 2015 Jonatan.		- Se muestran los examenes generados desde kardex en la pestaña de ordenes, los cuales pueden ser cambiados de estado.
	* 	Febrero 9 de 2015 Jonatan.		- Se hace control sobre las areas de texto de la pestaña de medidas generales para que segun el rol se puedan ver
										o  no, ademas se utilizara el campo Rrpnpe para el control de los nombres de las pestañas por rol.
	*   Febrero 5 de 2015  Jonatan - Edwin 
									- Se valida por rol si se muestra el medico tratante.
									- Se valida por rol si se muestra la mensajeria.
									- Se valida el lenguaje americas para que no permita repetir exmenes con el mismo nombre.
									- Se valida si el medico desea realizar el CTC de articulos o examenes para un paciente del IDC, se controla con un parametro en la tabla root_000051 (empresasConfirmanCTC).
									- Se guarda la auditoria si el medico decide no realizar CTC para el paciente.
									- Los articulos de lactario se muestran en las nutriciones.
									- En el historico se muestran los medicamentos.
									- Se corrige error al agregar medicamento.
									- Se corrige el Kadido.
									- El parpadeo de los med de central de mezclas se muestran con parpadeo violeta si no estan confirmados.
	*	Enero 16 de 2015:    Jonatan	- Se agrega el campo Kaddoa qen la pestaña de medicamentos, el cual permite marcar el medicamento como Dosis Adaptada.
	*	Diciembre 04 de 2014 Jonatan.	- Se corrige el registro de medicos tratantes para un paciente, estaban siendo registrados en la tabla hce_000022, 
											ahora se insertaran en la tabla 47 y 63(temporal). 
	*	Noviembre 18 de 2014    (Jonatan)  -> En la funcion que busca las ayudas diag se le envia el centro de costos donde esta el paciente para que 
											  muestre los realizados en caso de ser de urgencias.
	*   Noviembre 5 2014: 		(Jonatan)  Si el centro de costos en que se abre el programa es de urgencias toma la ronda par actual, sino toma la ronda par siguiente. 
	*	Mayo 24 de 2012			(Edwin MG) -> Se homologa Ordenes con Kardex
	*	Febrero 22 de 2011		(Edwin MG) -> Se corrige funcion obtenerVectorAplicacionMedicamentos
	*	Febrero 17 de 2011		(Edwin MG) -> Si esta en proceso de tralasado y fue entregao desde cirugia, solo pueden hacer el kardex del paciente
	*										  personal de cirguia o urgencias
	*	Febrero 15 de 2011		(Edwin MG) -> Si el paciente se encuentra en traslado no se puede realizar el kardex
	* 	2010-06-09:  (Msanchez):  Creado
	*
 */

if( !empty($hce) ){
	$wfecha = date( "Y-m-d" );
	$waccion = 'b';
}

$usuarioValidado = true;
$wactualiz = "Marzo 6 de 2018";

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false; 	
} else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;	
}

$nroDocumento = $wcedula;
$tipoDocumento = $wtipodoc;

/*****************************
 * INCLUDES
 ****************************/
include_once("./ordenes.inc.php");
/****************************************************************************************************************
/****************************************************************************************************************
 * PARAMETROS QUE SE NECESITAN
 *************************************************************************/
//Si en la url viene historia e ingreso, hara la consulta por estos dos criterios con la funcion consultarInfoPacienteOrdenHCEPorHistoriaIngreso,
//sino buscara por cedula.

if($historia !='' and $ingreso != ''){
	$paciente = consultarInfoPacienteOrdenHCEPorHistoriaIngreso($conex, $wbasedato, $historia, $ingreso);
}else{
	$paciente = consultarInfoPacienteOrdenHCE($tipoDocumento,$wcedula);
}

$diasDispensacion = consultarDiasDispensacion( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );

$wempresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );

//****************************************************************************/
	
//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
encabezado("Ordenes médicas",$wactualiz,"clinica");
	
if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";
	
	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} else {
	
	$_SESSION['wordenes'] = 1;
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	//Historial de modificaciones
	$mostrarAuditoria = true;

	//Fecha grabacion
	$fechaGrabacion = date("Y-m-d");
	
	//Base de datos, se generaliza de generar orden
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$imprimeMedicamentos = strtolower( trim( consultarAliasPorAplicacion($conex, $wemp_pmla, "imprimeMedicamentos") ) );
	$tiempo_cierre_automatico = consultarAliasPorAplicacion($conex, $wemp_pmla, "tiempoCierreAutomaticoOrdenes");
	$wentidades_confirmanCTC = consultarAliasPorAplicacion($conex, $wemp_pmla, "empresasConfirmanCTC");
	$medicamentosControlAuto = consultarAliasPorAplicacion( $conex, $wemp_pmla, "MedicamentosControlAuto" );
	$mensajelenguajeAmericas = consultarAliasPorAplicacion( $conex, $wemp_pmla, "mensajeLenguajeAmericas" );

	//Consulta de la información del usuario
//	$usuario = consultarUsuarioKardex($wuser);
//	var_dump($usuario);
	
	//Consultar si estan activas las ordenes para este servicio.	
	$sql_cco = "SELECT Ccoior
			  FROM {$wbasedato}_000011
			 WHERE Ccocod = '".$paciente->servicioActual."'";			   
	$res_cco = mysql_query( $sql_cco, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row_cco = mysql_fetch_array($res_cco);
	
	if($row_cco['Ccoior'] != 'on'){
	
		echo "<table align=center>";
		echo "<td>";
		echo '<span class="encabezadotabla"><br><br>';
		echo '<font size=3>En poco tiempo estaremos iniciando las ordenes electrónicas en este servicio.</font>';
		echo "</span><br><br>";
		echo "</td>";
		echo "</table>";
		
		echo "<table align=center>";
		echo "<tr><td align=center colspan=9><input type=button value='RETORNAR' onclick='cerrarModalHCE();'></td></tr>";
		echo "</table>";
		
		return false;
	
	}
	
	// Enero 22 de 2019. Se realiza llamado a la función actulizarTipoProtocolo para el día anterior ( date("Y-m-d", time() - 24*60*60 ) )
	// Esto con el fin de que todos los articulos pasen del día anterior al actual desde kardex
	if( tieneKardex($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, date( "Y-m-d", time() - 24*60*60 ) ) && !existeEncabezadoKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaGrabacion )){
		actualizarTipoProtocolo( $conex, $wemp_pmla, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, date("Y-m-d", time() - 24*60*60 ) );
	}
	
	actualizarTipoProtocolo( $conex, $wemp_pmla, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaGrabacion );
	actualizarFamiliaProductos( $conex, $wbasedato, $wcenmez, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaGrabacion );
	


 /*************************************************************************
  * FUNCIONES
  *************************************************************************/
  function consultarDAGenericas( $conex, $wbasedato, $wcenmez ){
  
	$val = array();
	
	$sql = "SELECT Artcod 
			  FROM {$wbasedato}_000068, {$wcenmez}_000002, {$wcenmez}_000001
			 WHERE artcod = arkcod
			   AND arttip = tipcod
			   AND tiptpr = arktip
			   AND artest =  'on'
			   AND arkest =  'on'
			   AND tipest =  'on'
			   AND tippro = 'on'
			   AND tipnco = 'on'
			   AND tiptpr like 'D%'
		";
			   
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $rows = mysql_fetch_array( $res ) ){
		$val[] = strtoupper( $rows[ 'Artcod' ] );
	}
	
	return $val;
  }
  
  function mostrar_resultado($wresultado)
     {
	  global $whce;
	  global $conex;
	  
	  $wmensaje=explode("@",$wresultado);       		 			 //Separo el encabezado del resto de la información, el '@' indica fin del encabezado
		 
	  //===========================================================================================================================
	  //* * * Encabezado * * *
	  //===========================================================================================================================
	  $wencabezado=explode("!",$wmensaje[0]);            			 //Separo las filas del encabezado, '!' indica fin de fila <tr>
	  
	  //echo "<center><table border=0>";		// Se comenta porque el fin de la tabla ya está definido donde se llama la función	// 2012-06-26
	  for ($k=0;$k < count($wencabezado); $k++)
	     {
		   $wnegrilla=false; 
		   if (($k+1)==count($wencabezado))                         //Si (k+1) == count, es porque es el nombre del Estudio
		      {
		       $wnegrilla=true;
		      } 
		     
		   $wlinea=explode(":",$wencabezado[$k]);
		   if (!isset($wlinea[1]))                                  //Si no hay ':' es porque es un Titulo
		      $wlinea=$wencabezado[$k];
		     else
		       $wlinea="<b>".$wlinea[0].":</b>&nbsp".$wlinea[1];
		      
		   echo "<tr>";
		   if ($wnegrilla)
		      echo "<td align=center colspan=3 class=tipoTA>".$wlinea."</td>";
		     else
		        echo "<td align=center colspan=3>".$wlinea."</td>"; 
		   echo "</tr>";
		 }
	  //===========================================================================================================================
	  
		 
	  //===========================================================================================================================
	  //* * * Detalle * * *
	  //===========================================================================================================================
	  echo "<tr class='encabezadoTabla'>";
	  echo "<th align=center>Descripción</th>";
	  echo "<th align=center>Valor Resultado</th>";
	  echo "<th align=center>Valor de Referencia</th>";
	  echo "</tr>";
	  
	  $wfilas=explode("!",$wmensaje[1]);        		 			 //La información diferente al encabezado, la separo en filas, como si fuera un registro
		 
	  for ($i=0;$i<count($wfilas);$i++)           
	     {
	      $wcolumnas=explode("$",$wfilas[$i]);     		 			 //Cada fila o registro lo separo en columnas o campos, '$' indica fin del campo
		  
	      if (isset($wclase) and $wclase == "fila1")
		    $wclase = "fila2";
		   else 
		      $wclase = "fila1";
	      
	      echo "<tr class='".$wclase."'>"; 
		  for ($j=0;$j<count($wcolumnas);$j++)
		     {
			  if (count($wcolumnas) == 1)                            //Si entra aca es porque es un SubTitulo
	             echo "<td align=left colspan=3 class='encabezadoTabla'><b>".$wcolumnas[$j]."</b></td>";          //Imprimo cada columna
	            else
	               echo "<td align=center colspan=3>".$wcolumnas[$j]."</td>";  //Imprimo cada columna 
		     }
	      echo "</tr>";	
	     }
	  // echo "</table></center>";	// Se comenta porque el fin de la tabla ya está definido donde se llama la función	// 2012-06-26
	  //===========================================================================================================================     
	 }    
  	 
     
  function traer_resultado($wtabla, $wcco, $whis, $wing, $wnor, $wite)
     {
	  global $whce;
	  global $conex;
	  
	 $q = " SELECT hl7rdo "
	      ."   FROM hce_".$wtabla
	      ."  WHERE hl7his = '".$whis."'"
	      ."    AND hl7ing = '".$wing."'"
	      ."    AND hl7des = '".trim($wcco)."'"
	      ."    AND hl7nor = '".$wnor."'"
	      ."    AND hl7nit = '".$wnor."-".$wite."'"
	      ."    AND hl7edo = 'R' "
	      ."    AND hl7est = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	
	  if ($wnum > 0)
	     {
		  $row = mysql_fetch_array($res);
		  
		  return $row[0];
	     }     
	    else
	       return "";    
     }       
     
     
	 
  //===========================================================================================================================================  
  //*******************************************************************************************************************************************
  
	
	
	/************************************************************************************************************************
	 * Febrero 15 de 2011
	 * 
	 * Modificacion: Febrero 17 de 2011
	 ************************************************************************************************************************/
//	if( $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && $usuario->centroCostos != $paciente->servicioAnterior ){
	if( false && $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && !($usuario->esCcoUrgencias || $usuario->esCcoCirugia) && esCcoIngreso( $conex, $wbasedato, $paciente->servicioAnterior ) ){
	//if( $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && !existeEncabezadoKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha) ){
		if( isset($editable) && $editable != "off" || !isset($editable) ){
			mensajeEmergente("El paciente esta en proceso de traslado.\\nDebe recibir el paciente para hacer el kardex.\\n\\nUSTED SE ENCUENTRA ASOCIADO A\\n$usuario->nombreCentroCostos($usuario->centroCostos)"); //Marzo 3 de 2011
			funcionJavascript("window.parent.cerrarModal();");
			exit;
		}
	}
	elseif( false && $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && ($usuario->esCcoUrgencias || $usuario->esCcoCirugia) ){
		$paciente->servicioActual = $paciente->servicioAnterior; 
	}
	/************************************************************************************************************************/
	
	//Parametros que siempre llegan
//	echo "Documento '$wcedula' '$wtipodoc'";

	if( $usuario->tieneFirmaVencida ){
		//mensajeEmergente("No tiene permisos para usar ordenes de hce.  No se encuentra registrado cómo médico.");
		
		echo "<table align=center>";
		echo "<tr>";
		echo "<td align='center'>";
		echo '<span class="encabezadotabla"><br><br>';
		echo '<font size=3>La firma electrónica se encuentra vencida.</font>';
		echo "</span><br><br>";
		echo '<span style="font-weight:bold;">Por favor actualícela en: <a href="/matrix/hce/procesos/PassHCE.php?empresa=hce">Actualizar Firma</a></span>';
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		
		die("");
	}
	
	if($usuario->pestanasKardex == ""){
		// mensajeEmergente("No tiene permisos para usar ordenes de hce.  Comuniquese por favor con el Area de Soporte.");
		//		funcionJavascript("cerrarModal();");
		//		die("");
		
		echo "<table align=center>";
		echo "<td>";
		echo '<span class="encabezadotabla"><br><br>';
		echo '<font size=3>El usuario no tiene configurado pestañas para usar ordenes (hce_000020, hce_000026, movhos_000011 ccopek)</font>';
		echo "</span><br><br>";
		echo "</td>";
		echo "</table>";
		
		die("");
	}
	
	if($usuario->esMedicoRolHCE && !$usuario->registradoMedico){
		//mensajeEmergente("No tiene permisos para usar ordenes de hce.  No se encuentra registrado cómo médico.");
		
		echo "<table align=center>";
		echo "<td>";
		echo '<span class="encabezadotabla"><br><br>';
		echo '<font size=3>No tiene permisos para usar ordenes de hce.  No se encuentra registrado cómo médico.</font>';
		echo "</span><br><br>";
		echo "</td>";
		echo "</table>";
		
		die("");
	}
	
	/*********************************************************************************************
	 * :::HCE:::Validacion de las empresas responsables
	 *********************************************************************************************/
	if($usuario->empresasAgrupadas != "*" && $usuario->empresasAgrupadas != ""){
		if(strpos($usuario->empresasAgrupadas,$paciente->numeroIdentificacionResponsable) === false){
			mensajeEmergente("La entidad responsable de este paciente no esta asociada a su rol.");
			die("");
		}
	}
	
	
	
	
	/****************************************************************************************
	 * Enero 22 de 2014
	 * Busco si el paciente tiene EPS o no
	 ****************************************************************************************/
	//Busco los tipos de empresa que son EPS
	$tiposEmpresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiposEmpresasEps" );
	
	//creo un IN para la consulta
	$list = explode( "-", $tiposEmpresa );
	
	$inEPS = '';
	
	foreach( $list as $key => $value ){
		$inEPS .= ",'$value'";
	}
	
	$inEPS = "IN( ".substr( $inEPS, 1 )." ) ";
			
	$sql = "SELECT 
				*
			FROM
				{$wbasedato}_000016 b
			WHERE
				inghis = '".$paciente->historiaClinica."'
				AND inging = '".$paciente->ingresoHistoriaClinica."'
				AND ingtip $inEPS
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	$esPacEPS = false;
	$esContributivo = false;
	if( $num > 0 ){
		$esPacEPS = true;
		
		$esContributivo = consultarSiContributivo($paciente->numeroIdentificacionResponsable);
		// var_dump($esContributivo);
	}
	/****************************************************************************************/
	
	//Si el usuario no es medico se verifica si puede modificar LEVs e IC
	if( !$usuario->esMedicoRolHCE ){
		$modificaIC = permisosModificarLEVsIC( $conex, $wbasedato, "IC", $usuario->codigoRolHCE, $paciente->servicioActual );
		$modificaIC = $modificaIC ? 'on':'off';
		$modificaLev = permisosModificarLEVsIC( $conex, $wbasedato, "LEV", $usuario->codigoRolHCE, $paciente->servicioActual );
		$modificaLev = $modificaLev ? 'on':'off';
	}
	else{
		//Si es un usuario medico, siempre puede modificar LEVs e IC
		$modificaLev = 'on';
		$modificaIC = 'on';
	}
	
	//Abril 17 de 2017
	$txtDiagHCE = trim( consultarDxs( $conex, $wemp_pmla, $wbasedatohce, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica ) );
	
	$frecIC = consultarAliasPorAplicacion( $conex, $wemp_pmla, "frecIC" );
	$frecLev = consultarAliasPorAplicacion( $conex, $wemp_pmla, "frecLev" );
	
	$tiempMinimoOrdenMedicamento = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiempoMinimoAntesOrdenarMedicamento" );
	$tiempoMinimoMarcarDANE = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiempoMinimoMarcarDANE" );
	$frecuenciaNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "frecuenciaNPT" );
	$condicionNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "condicionNPT" );
	$famNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "famNPT" );
	
	$versionMozilla = consultarAliasPorAplicacion( $conex, $wemp_pmla, "versionMozilla" );
	$validarBrowser = consultarAliasPorAplicacion( $conex, $wemp_pmla, "validarBrowserOrdenes" );
	
	$wipimpresoraga = consultarImpresoraGA( $conex, $wbasedato, $paciente->servicioActual );
	// 2012-06-27
	// Se adicionó accept-charset='utf-8' para que el formulario pueda codificar todos los caracteres correctamente
	// y no arroje algunas veces datos corrompidos que bloqueaban la grabación de ordenes
	// Formulario
	echo "<form name='forma' action='ordenes.php' method='post' accept-charset='utf-8'>";
	
	echo "<input type='hidden' name='pgr_origen' id='pgr_origen' value='".$pgr_origen."'/>";
	echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='hidden' name='wbasedato' id='wbasedato' value='".$wbasedato."'/>";
	echo "<input type='hidden' name='wbasedatohce' id='wbasedatohce' value='".$wbasedatohce."'/>";
	echo "<input type='hidden' name='wcenmez' id= 'wcenmez' value='".$wcenmez."'/>";
	echo "<input type='hidden' name='usuario' id='usuario' value='".$wuser."'/>";
	echo "<input type='hidden' name='centroCostosUsuario' name='centroCostosUsuario' value='".$usuario->centroCostos."'/>";
	echo "<input type='hidden' id='pacEPS' name='pacEPS' value='".( ($esPacEPS)? "on": "off" )."'/>";	//Indica si el paciente tiene EPS
	echo "<input type='hidden' id='imprimeMedicamentos' name='imprimeMedicamentos' value='".$imprimeMedicamentos."'/>";	//Indica si el paciente tiene EPS
	echo "<input type='hidden' id='enUrgencias' name='enUrgencias' value='".(( $paciente->enUrgencias ) ? "on" : "off")."'/>";	//Indica si el paciente tiene EPS
	echo "<input type='hidden' name='tiempo_cierre_automatico' id='tiempo_cierre_automatico' value='".$tiempo_cierre_automatico."'/>";
	// echo "<input type='hidden' name='entidad_responsable' id='entidad_responsable' value='".$paciente->numeroIdentificacionResponsable."'/>";
	echo "<input type='hidden' name='entidad_responsable' id='entidad_responsable' value='".consultarNitResponsable($wemp_pmla,$paciente->numeroIdentificacionResponsable)."'/>";
	echo "<input type='hidden' name='entidades_confirmanCTC' id='entidades_confirmanCTC' value='".$wentidades_confirmanCTC."'/>";
	echo "<input type='hidden' name='medicamentosControlAuto' id='medicamentosControlAuto' value='".$medicamentosControlAuto."'/>";
	echo "<input type='hidden' name='mensajelenguajeAmericas' id='mensajelenguajeAmericas' value='".$mensajelenguajeAmericas."'/>";
	echo "<input type='hidden' name='modificaIC' id='modificaIC' value='".$modificaIC."'/>";
	echo "<input type='hidden' name='modificaLev' id='modificaLev' value='".$modificaLev."'/>";
	echo "<input type='hidden' name='frecLev' id='frecLev' value='".$frecLev."'/>";
	echo "<input type='hidden' name='frecIC' id='frecIC' value='".$frecIC."'/>";
	echo "<input type='hidden' name='esMedico' id='esMedico' value='".($usuario->esMedicoRolHCE ? 'on': 'off')."'/>";
	echo "<input type='hidden' name='esEnfermera' id='esEnfermera' value='".($usuario->esEnfermeraRolHCE ? 'on': 'off')."'/>";
	echo "<input type='hidden' name='tiempoMinimoOrdenMedicamento' id='tiempoMinimoOrdenMedicamento' value='".$tiempMinimoOrdenMedicamento."'/>";
	echo "<input type='hidden' name='frecuenciaNPT' id='frecuenciaNPT' value='".$frecuenciaNPT."'/>";
	echo "<input type='hidden' name='condicionNPT' id='condicionNPT' value='".$condicionNPT."'/>";
	echo "<input type='hidden' name='famNPT' id='famNPT' value='".$famNPT."'/>";
	echo "<input type='hidden' name='versionMozilla' id='versionMozilla' value='".$versionMozilla."'/>";
	echo "<input type='hidden' name='validarBrowser' id='validarBrowser' value='".$validarBrowser."'/>";
	echo "<input type='hidden' name='esContributivo' id='esContributivo' value='".$esContributivo."'/>";
	echo "<input type='hidden' name='dxHCE' id='dxHCE' value='".$txtDiagHCE."'/>";	//Abril 17 de 2016
	echo "<input type='hidden' name='tiempoMinimoMarcarDANE' id='tiempoMinimoMarcarDANE' value='".$tiempoMinimoMarcarDANE."'/>";
	echo "<input type='hidden' name='pacienteDeAyudaDx' id='pacienteDeAyudaDx' value='".( $paciente->esDeAyudaDx ? 'on': 'off' )."'/>";
	echo "<input type='hidden' name='wipimpresoraga' id='wipimpresoraga' value='".$wipimpresoraga."'/>";
	
	
	pintarModalLEVS( $conex, $wbasedato, $wcenmez, $wbasedatohce, "LQ", $paciente->enUrgencias );
	pintarModalIC( $conex, $wbasedato, $wcenmez, $wbasedatohce, "IC", $paciente->enUrgencias );
	
	if( !$paciente->enUrgencias )
		cambiarEstadoDeDispensacionParaLEVIC( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
	
	
	
	
	//Agosto 22 de 2014
	//Condicion por defecto para liquidos endovenosos (LEV)
	$condicionLEV = consultarAliasPorAplicacion( $conex, $wemp_pmla, "condicionLEV" );
	echo "<input type='hidden' id='condicionLEV' name='condicionLEV' value='".$condicionLEV."'/>";	//Indica si el paciente tiene EPS
	
	//Condicion por defecto para liquidos INFUCIONES (IC)
	$condicionIC = consultarAliasPorAplicacion( $conex, $wemp_pmla, "condicionIC" );
	echo "<input type='hidden' id='condicionIC' name='condicionIC' value='".$condicionIC."'/>";	//Indica si el paciente tiene EPS
	
	echo "<input type='hidden' name='wespecialidad' value='$usuario->codigoEspecialidad'>";
	
	$centroCostosGrabacionTemp = $usuario->centroCostosGrabacion;
	if(!$usuario->esUsuarioLactario){
		if($usuario->esUsuarioCM || $usuario->esUsuarioSF){
			$centroCostosGrabacionTemp = "*";
		}
	}
	echo "<input type='hidden' name='centroCostosGrabacion' id='centroCostosGrabacion' value='".$centroCostosGrabacionTemp."'/>";
	echo "<input type='hidden' name='wfechagrabacion' id='wfechagrabacion' value='$fechaGrabacion'>";
	echo "<input type='hidden' name='whgrupos' id='whgrupos' value='$usuario->gruposMedicamentos'>";
	echo "<input type='hidden' name='wempresa' id='wempresa' value='$wempresa'>";	
	
	$datosUsuario = consultarUsuario($conex,$wuser);
	echo "<input type='HIDDEN' NAME= 'usuariodes' id='usuariodes' value='".$datosUsuario->descripcion."'/>";
	
	echo "<input type='hidden' name='wcedula' id='wcedula' value='$wcedula'>";
	echo "<input type='hidden' name='wtipodoc' id='wtipodoc' value='$wtipodoc'>";
	echo "<input type='hidden' name='whfirma' id='whfirma' value=''>";
	echo "<input type='hidden' name='frecuencia_elegida' id='frecuencia_elegida' value=''>";
	echo "<input type='hidden' name='dosis_medico_aux' id='dosis_medico_aux' value=''>";
	
	if($usuario->esUsuarioLactario){
		echo "<input type='hidden' name='whusuariolactario' id='whusuariolactario' value='on'>";
	} else {
		echo "<input type='hidden' name='whusuariolactario' id='whusuariolactario' value='off'>";
	}

	if(!isset($editable)){
		$editable="on";
	}
	echo "<input type='HIDDEN' NAME='editable' id='editable' value='".$editable."'/>";

	//Indicador de si es fecha actual
	if(isset($wfecha)){
		$esFechaActual = ($wfecha == $fechaGrabacion);
	}

	//Calcula la fecha del dia anterior.
	$fechaActualMilis 	= time();
	$ayerMilis			= time() - (24 * 60 * 60);
	$fechaAyer			= date("Y-m-d", $ayerMilis);
			
	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>"; 
    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...";
	echo "</div>";

	//Seccion de combinacion de articulos de nutricion y liquidos si aplica
	echo "<div id='modalArticulos' style='display:none;'>";
	echo "<table width='100%'>";

	echo "<tr>";
	echo "<td colspan=4 align=center class='encabezadoTabla'>";
	echo "<b>Selecci&oacute;n de componentes</b>";
	echo "</td>";
	echo "</tr>";

	echo "<tr><td colspan=4 class='fila1'>Insumos que componen <span id='articuloComponentes'></span></td></tr>";

	echo "<tr><td colspan=4 class='fila2' style=' width: 100%;'>";
	echo "<div id='listaComponentes' style='overflow-y: scroll; width: 100%; height: 300px;'></div>";
	echo "</td></tr>";

	echo "<input type='hidden' id='wcomponentesarticulo' name='wcomponentesarticulo' value=''>";
	echo "<input type='hidden' id='wcomponentesarticulocod' name='wcomponentesarticulocod' value=''>";

	echo "<input type='hidden' name='indiceArticuloComponentes' id='indiceArticuloComponentes' value=''>";
	echo "<input type='hidden' name='protocoloArticuloComponentes' id='protocoloArticuloComponentes' value=''>";
	
	echo "<tr>";
	echo "<td colspan=4>";
	echo "<div id='dvMsgConfiguracion' class='fondoamarillo' style='text-align:center;display:none;'></div>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan=2 align=right style='width:50%'>";
	echo "<input type='button' value='Grabar' onclick='cerrarModalArticulos();'>"; 
	echo "</td>";
	echo "<td colspan=2 align=left>";
	echo "<input type='button' value='Salir sin grabar' onclick='salirSinGrabarModalArticulosNPT();'>"; 
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	
	// Modal nutriciones
	echo "<div id='dvAuxModalNutriciones' style='display:none'></div>";
	
	
	if( isset($matrix) ){
		echo "<INPUT type='hidden' name='matrix' value='off'>";
	}
   /*****************************************************************************************************************************
     * MAESTROS OCULTOS.  SIRVEN PARA ASIGNAR LAS FILAS DINAMICAS... SE CARGAN en la accion b
     *****************************************************************************************************************************/
    if(isset($waccion) && $waccion == "b"){

		//Consulto las DA Genericas
		$daGenericas = consultarDAGenericas( $conex, $wbasedato, $wcenmez );
		echo "<script> var daGenericas = ".json_encode( $daGenericas ).";</script>";
		
		//Mayo 19 de 2020
		//Esta funcion se encuentra en el include funcionesGeneralesEnvioHL7
		//Se deja allí por que la idea es que también se haga el cambio de estados una vez alla traslado de pacientes
		cambioEstadoInteroperabilidad( $conex, $wbasedato, $wbasedatohce, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
	
    	//UNIDADES DE MEDIDA
    	echo "<div style='display:none'>";
    	echo "<select name='wmunidadesmedida' id='wmunidadesmedida' style='width:120' class='seleccion' style='display:block'>";
    	$colUnidades = consultarUnidadesMedida();
    	echo "<option value=''>Seleccione</option>";
    	foreach ($colUnidades as $unidad){
    		echo "<option value='".$unidad->codigo."'>$unidad->descripcion</option>";
    	}
    	echo "</select>";

    	//PERIODICIDADES
    	echo "<select id='wmperiodicidades'>";
    	echo "<option value=''>Seleccione</option>";
    	$colPeriodicidades = consultarPeriodicidades();
    	foreach ($colPeriodicidades as $periodicidad){
    		echo "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
			
			if( !empty( $periodicidad->dosisMax ) ){
				$jsobjPerDefecto .= ",$periodicidad->codigo : { dma: $periodicidad->dosisMax }";
			}
    	}
    	echo "</select>";
		
		//Se usa al mostrar las frecuencias del dextrometer y para los historicos de articulos
		$colCondicionesSuministroInsulinas = consultarPeriodicidades( 'I' );
		
		//Creo el obejto javascript para el manejo de dosis maxima por defecto
		if( !empty($jsobjPerDefecto) ){
			echo "<script>var dmaPorFrecuencia = { ".substr( $jsobjPerDefecto, 1 )." }</script>";
		}

    	//FORMAS FARMACEUTICAS
    	echo "<select id='wmfftica'>";
    	echo "<option value=''>Seleccione</option>";
    	// $colFormasFarmaceuticas = consultarFormasFarmaceuticas();	//Comenta las formas farmaceuticas, ya se debe mostrar siempre la unidad de medida
    	$colFormasFarmaceuticas = consultarUnidadesMedida();
    	foreach ($colFormasFarmaceuticas as $formasFarmaceutica){
    		echo "<option value='".$formasFarmaceutica->codigo."'>$formasFarmaceutica->descripcion</option>";
    	}
    	echo "</select>";

    	//VIAS ADMINISTRACION
    	echo "<select id='wmviaadmon'>";
    	echo "<option value=''>Seleccione</option>";
    	$colVias = consultarViasAdministracion();
    	foreach ($colVias as $via){
    		echo "<option value='".$via->codigo."'>$via->descripcion</option>";
    	}
    	echo "</select>";

    	//EXAMENES DE LABORATORIO
    	echo "<select id='wmexamenlab'>";
    	$examenesLaboratorio = consultarCentrosAyudasDiagnosticas($wbasedatohce);
    	$colServiciosExamenes = $examenesLaboratorio;

    	echo "<option value=''>Seleccione</option>";
    	foreach ($examenesLaboratorio as $examen){
    		echo "<option value='$examen->codigo|$examen->consecutivoOrden'>$examen->nombre - Consecutivo de orden: $examen->consecutivoOrden</option>";
    	}
    	echo "</select>";

    	//ESTADOS DEL EXAMEN
    	echo "<select id='wmestadosexamen'>";
    	$colEstadosExamen = consultarEstadosExamenes();
    	foreach ($colEstadosExamen as $estadoExamen){
    		echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
    	}
    	echo "</select>";

    	//ESTADOS DEL EXAMEN
    	echo "<select id='wmestadosexamen'>";
    	$consultarEstadosAyudasDx = consultarEstadosAyudasDx();
    	foreach ($consultarEstadosAyudasDx as $estadoExamen){
    		echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
    	}
    	echo "</select>";

    	//ESTADOS DEL EXAMEN DEL LABORATORIO
    	echo "<select id='wmestadosexamenlab'>";
    	$colEstadosExamenLab = consultarEstadosExamenesLaboratorio();
    	foreach ($colEstadosExamenLab as $estadoExamen){
    		echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
    	}
    	echo "</select>";

    	//CONDICIONES DE SUMINISTRO DE MEDICAMENTOS SIN IMPORTAR EL PROTOCOLO
    	echo "<select id='wmcondicionessuministro'>";
    	$colCondicionesSuministro = consultarCondicionesSuministroMedicamentos("");
    	echo "<option value=''>Seleccione</option>";
    	foreach ($colCondicionesSuministro as $condicion){
    		echo "<option value='$condicion->codigo'>$condicion->descripcion</option>";
			
			$jsobjConDefecto .= ",'$condicion->codigo' : { dma: ".($condicion->valDefecto != '' ? $condicion->valDefecto : "undefined" )." ";
			$jsobjConDefecto .= ",pdm: ".($condicion->permiteDma? "true" : "false" )." ";
			$jsobjConDefecto .= ",pdt: ".($condicion->permiteDtt? "true" : "false" )." }";
    	}
    	echo "</select>";
		
		//Creo el obejto javascript para el manejo de dosis maxima por defecto
		if( !empty($jsobjConDefecto) ){
			echo "<script>var dmaPorCondicionesSuministro = { ".substr( $jsobjConDefecto, 1 )." }</script>";
		}
    	 
    	//CONDICIONES DE SUMINISTRO DE MEDICAMENTOS SIN IMPORTAR EL PROTOCOLO
    	// echo "<select id='wmcondicionessuministro'>";
    	// $colCondicionesSuministroInsulinas = consultarCondicionesSuministroMedicamentos("I");
    	// echo "<option value=''>Seleccione</option>";
    	// foreach ($colCondicionesSuministro as $condicion){
    		// echo "<option value='$condicion->codigo'>$condicion->descripcion</option>";
    	// }
    	// echo "</select>";

    	echo "</div>";
    }
    /*****************************************************************************************************************************
     * FIN MAESTROS OCULTOS
     *****************************************************************************************************************************/

	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}
	
	//FC para hacer las acciones
	switch ($waccion){
		case 'b': //Cuando ya hay un kardex creado se muestra la pantalla de modificación			
			/*******************************************************
			 * EL KARDEX PUEDE SER EDITABLE (SOLO EL DE HOY) O DE SOLO LECTURA (CUALQUIER OTRA FECHA)
			 *******************************************************/
			$confirmaGeneracion = true;
			
			if(isset($paciente->historiaClinica) && isset($wfecha)){
//				$paciente = consultarInfoPacienteKardex($whistoria,"");  //Consulta de paciente por historia, sin ingerso

				//$usuario->centroCostos
				if(!$usuario->esUsuarioLactario){
					if($usuario->esUsuarioCM || $usuario->esUsuarioSF){
						$usuario->centroCostosGrabacion = "*";
					}
				}
//				echo "Centro costos grabacion modificado $usuario->centroCostosGrabacion"; 
				
				if(!empty($paciente->ingresoHistoriaClinica)){
					$kardexActual = consultarKardexPorFechaPaciente($wfecha,$paciente);
					
					//Si existe esta variable creo un campo hidden para poderla usar en
					//la funcion javascript confirmarGeneracion
					if( !empty( $_GET['programa'] ) ){
						echo "<input type='hidden' name='programa' id='programa' value='$et'>";
					}
						
					if(isset($whgrabado)){
						echo "<input type='hidden' name='whgrabado' value='$whgrabado'>";	
					}
//					echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='0'/>";
					
					/*********************************************************************************************************************
					 * SI NO ENCUENTRA ENCABEZADO EN LA FECHA ANTERIOR O NO ESTA MARCADO COMO GRABADO EL KARDEX DEL DIA ANTERIOR SE GRABA
					 *********************************************************************************************************************/
					$cargarDefinitivo = false;
					if(!existeEncabezadoKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer)){
						if(crearEncabezadoKardexAnterior($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer)){
							$cargarDefinitivo = true;
						}
					} else {
						if(!grabadoEncabezadoKardexFecha($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer)){
							if(marcarGrabacionKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer, "on")){
								$cargarDefinitivo = true;								
							}
						}	//Si hay articulos en la temporal carga lo anterior al definitivo
						elseif( hayArticulosEnTemporal( $conex, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$fechaAyer) ){
							$cargarDefinitivo = true;
						}
					}

					$esKardexNuevo = true;
					if(!empty($kardexActual->historiaClinica)){
						$esKardexNuevo = false;
					}
					
					/****************************************************************************************************************
					 * Si el kardex lo esta mirando otra persona, el kardex no se puede abrir. Si la persona quien lo estaba creando
					 * es la misma que el usuario el kardex se puede abrir
					 *
					 * Marzo 8 de 2011
					 ****************************************************************************************************************/
					
					//Se busca el tiempo maximo en el que se puede abrir ordenes por otro usuario si otro lo tiene bloqueado.				
					$tiempoInactividadMaximoOrdenes = consultarAliasPorAplicacion( $conex, $wemp_pmla, "MinutosInactividadMaximoOrdenes" );
					
					//Calculo cuanto ha sido el tiempo transcurrido desde la ultima apertura y la fecha y hora actual en la que estan abriendo.	
					$wtiempo_transcurrido_apertura = tiempo_transcurrido_apertura($conex, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica);
					
					if( !$esKardexNuevo ){
						//Si hay articulos en la tabla temporal, se verifica si la ordenes esta abierta y el tiempo transcurrido, si el tiempo transcurrido 
						//es mayor al tiempo de inactividad, permite abrir el programa, ademas se si el usuario que bloqueo es el mismo que lo abre, sino, muestra la alerta y se detiene el programa. 
						if(hayArticulosEnTemporal( $conex, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha)){
							if( $kardexActual->grabado == "off" and ($wtiempo_transcurrido_apertura <= $tiempoInactividadMaximoOrdenes) ){
								if( $kardexActual->usuarioQueModifica != $usuario->codigo ){
									mensajeEmergente("El kardex ya se ha creado pero se encuentra actualmente en uso. Por el usuario: ".$kardexActual->nombreUsuarioQueModifica);
									$_SESSION['wordenes'] = 0;
									funcionJavascript("cerrarModalHCE();");
									exit;
								}
							}
							else{
								$usuModifca = consultarUsuarioOrdenes( $usuario->usuarioQueModifica );
								
								if( $kardexActual->grabado == "off" && $usuModifca->esUsuarioLactario ){
									if( $kardexActual->usuarioQueModifica != $usuario->codigo ){
										mensajeEmergente("El kardex ya se ha creado pero se encuentra actualmente en uso. Por el usuario: ".$kardexActual->nombreUsuarioQueModifica);
										$_SESSION['wordenes'] = 0;
										funcionJavascript("cerrarModalHCE();");
										exit;
									}
								}
							}
						}
					}
					/****************************************************************************************************************/
					
					registrar_log_ingreso($conex, $wbasedato, $wcenmez, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);	
					
					//Dias Maximos en el log de ingreso a lsa ordenes.
					$diaslogordenes = consultarAliasPorAplicacion( $conex, $wemp_pmla, "DiasLogOrdenes" );					
					// Se aumenta el tiempo de borrado de datos en la tabla log_agenda a 30 dias, estaba en 15 dias. Jonatan Lopez 19 Diciembre 2013.
					borrarLogsAntiguos("".$wbasedato."_000170",$diaslogordenes);
					
					//Carga esquema de insulina si el usuario es de un centro de costos hospitalario
					cargarEsquemaDextrometer($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer,$wfecha);

					if($cargarDefinitivo){
						cargarArticulosADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer,@$esKardexNuevo,'');
						//cargarExamenesADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer,@$firmaDigital);
						cargarInfusionesADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
						cargarMedicoADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
						cargarDietasADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
						
						/********************************************************************************************
						 * Marzo 17 de 2011
						 ********************************************************************************************/
						//Registro de auditoria
						$auditoria = new AuditoriaDTO();

						$auditoria->historia = $paciente->historiaClinica;
						$auditoria->ingreso = $paciente->ingresoHistoriaClinica;
						$auditoria->descripcion = "Kardex recuperado automáticamente";
						$auditoria->fechaKardex = date("Y-m-d");
						$auditoria->mensaje = obtenerMensaje( "MSJ_KARDEX_RECUPERADO" );
						$auditoria->seguridad = $usuario->codigo;

						registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
						/********************************************************************************************/
							
						//Si hay una carga de detalle, se va directamente a la generacion
						$confirmaGeneracion = true;
//						funcionJavascript("confirmarGeneracion();");
					}
						
					if(!$esKardexNuevo){
						$confirmaGeneracion = true;
//						funcionJavascript("confirmarGeneracion();");
					} else {
						//No permite generar el kardex NUEVOS para dias anteriores
						if(!$esFechaActual){
							mensajeEmergente("No existe orden para la fecha seleccionada.");
							$confirmaGeneracion = false;
//							funcionJavascript("inicio();");
						}

						//Si el paciente se encuentra en alta definitiva no debe permitir modificaciones en el kardex
						if($paciente->altaDefinitiva == "on"){
//							echo "<br/><span class='subtituloPagina2'>El paciente se encuentra en alta definitiva, no puede crearse kardex.</span><br/>";
//							echo "<br/><center><input type='button' value='Regresar' id='regresar' onclick='inicio();'> | <input type=button value='Cerrar ventana' onclick='cerrarVentana();'><br/></center>";
						} else {
//							echo "<br/><center><input type='button' value='Regresar' id='regresar' onclick='inicio();'> | <input type='button' value='Confirmar generacion Kardex' onclick='confirmarGeneracion();'> | <input type=button value='Cerrar ventana' onclick='cerrarVentana();'><br/></center>";							
						}
					} 
				} else {
					$confirmaGeneracion = false;
					mensajeEmergente("No se pudo consultar el ultimo ingreso del paciente.  Verifique que la historia clínica fue digitada correctamente");
					funcionJavascript("inicio();");
				}//Fin existe ingreso de historia e informacion de paciente
			} else {
				$confirmaGeneracion = false;
				mensajeEmergente("Faltan parametros para realizar la consulta de la orden");
//				funcionJavascript("inicio();");
			}
			/***************************************************************************************************/
			
			if( $confirmaGeneracion ){
			$aplicaGraficaSuministro = true;
			$activarPestanas = true;			//**OJO TODO:  ESTO SE DEBE QUITAR
				
//			$paciente = consultarInfoPacienteKardex($whistoria,"");
			$kardexActual = consultarKardexPorFechaPaciente($wfecha, $paciente);

			if($kardexActual->grabado == 'off' && !$kardexActual->esAnterior){
//				mensajeEmergente("El kardex está siendo modificado en este momento.");		
//				funcionJavascript("inicio();");
			}
			
			//INDICADOR DE DESCUENTO DE DISPENSACION::
			$noAcumulaSaldoDispensacion = $kardexActual->noAcumulaSaldoDispensacion;
			$descontarDispensaciones = $kardexActual->descontarDispensaciones;

			//Kardex anterior
			if((empty($kardexActual->historiaClinica) || $kardexActual->esAnterior)){
				$kardex = new kardexDTO();

				$kardex->historia = $paciente->historiaClinica;
				$kardex->ingreso = $paciente->ingresoHistoriaClinica;
				$kardex->fechaCreacion = date("Y-m-d");	
				$kardex->horaCreacion = date("H:i:s");
				$kardex->estado = "on";
				$kardex->usuario = $wuser;
				$kardex->esAnterior = $kardexActual->esAnterior;
				$kardex->editable = true;
				
				//Trae los datos del encabezado del dia anterior
				$kardex->talla = $kardexActual->talla;
				$kardex->peso = $kardexActual->peso;
				$kardex->diagnostico = $kardexActual->diagnostico;
				$kardex->antecedentesAlergicos = $kardexActual->antecedentesAlergicos;
				$kardex->cuidadosEnfermeria = $kardexActual->cuidadosEnfermeria;
				$kardex->observaciones = $kardexActual->observaciones;
				$kardex->curaciones = $kardexActual->curaciones;		
				$kardex->terapiaRespiratoria = $kardexActual->terapiaRespiratoria;
				$kardex->sondasCateteres = $kardexActual->sondasCateteres;
				$kardex->interconsulta = $kardexActual->interconsulta;
				$kardex->consentimientos = $kardexActual->consentimientos;
				$kardex->medidasGenerales = $kardexActual->medidasGenerales;
				
				$kardex->obsDietas = $kardexActual->obsDietas;
				$kardex->procedimientos = $kardexActual->procedimientos;
				$kardex->dextrometer = $kardexActual->dextrometer;
				$kardex->cirugiasPendientes = $kardexActual->cirugiasPendientes;
				$kardex->terapiaFisica = $kardexActual->terapiaFisica;
				$kardex->rehabilitacionCardiaca = $kardexActual->rehabilitacionCardiaca;
				$kardex->antecedentesPersonales = $kardexActual->antecedentesPersonales;
				$kardex->aislamientos = $kardexActual->aislamientos;
				$kardex->grabado = "off";

				$kardex->centroCostos = $usuario->centroCostosGrabacion;
				$kardex->usuarioQueModifica = $usuario->codigo;
				
				$kardex->rutaOrdenMedica = $kardexActual->rutaOrdenMedica;

				if(empty($kardexActual->historiaClinica)){
					$kardex->esPrimerKardex = true;	
				} else {
					$kardex->esPrimerKardex = false;
				}
				$kardexActual = $kardex;
				
				$kardexActual->descontarDispensaciones = $descontarDispensaciones;
				$kardexActual->noAcumulaSaldoDispensacion = $noAcumulaSaldoDispensacion;
				
				$wfecha = date("Y-m-d");
			}

			//Si el paciente se encuentra en alta definitiva no debe permitir modificaciones en el kardex
			if($paciente->altaDefinitiva == "on" || (isset($editable) && $editable == "off")){
				$kardexActual->editable = false;
			}
						
			// funcionJavascript("window.onbeforeunload = salida;");
			funcionJavascript("window.onbeforeunload = salir_sin_grabar;");
			
			/************************************************************************************************************************
			 * Enero 26 de 2012
			 ************************************************************************************************************************/
			//Si el kardex es editable, la gestion de procedimientos realizados por la secretaria (Bitacora de procedimientos) son marcados como off
			if( $kardexActual->editable ){
				//Autorecuperacion de kardex anterior si no esta grabado on
				//Cuando realice una consulta del kardex debe apagarse la bandera de grabado
				marcarGrabacionKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, "off");
				
				marcandoLeidoBitacoraProcedimientos( $conex, $wbasedato, $usuario->codigo, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica );
			}
			/************************************************************************************************************************/

			$ccoRealizaEstudios = ccoRealizaEstudios( $conex, $wbasedato, $paciente->servicioActual );
			
			//Campos ocultos
			echo "<input type='hidden' name='whistoria' id='whistoria' value='$paciente->historiaClinica'>";
			echo "<input type='hidden' name='wingreso' id='wingreso' value='$paciente->ingresoHistoriaClinica'>";
			echo "<input type='hidden' name='wfecha' id='wfecha' value='$wfecha'>";
			echo "<input type='hidden' name='weditable' id='weditable' value='".($kardexActual->editable ? 'on': 'off' )."'>";
			echo "<input type='hidden' name='wservicio' id='wservicio' value='$paciente->servicioActual'>";
			echo "<input type='hidden' name='whora' id='whora' value='".date("H:i:s")."'>";
			echo "<input type='hidden' name='ccoRealizaEstudios' id='ccoRealizaEstudios' value='".( $ccoRealizaEstudios ? 'on' : 'off' )."'>";
			echo "<input type='hidden' name='nombreServicioActual' id='nombreServicioActual' value='".$paciente->nombreServicioActual."'>";
			
			$diaSiguiente = strtotime ( '+1 day' , strtotime ( $wfecha ) ) ;
			$diaSiguiente = date ( 'Y-m-d' , $diaSiguiente );
			
			echo "<input type='hidden' name='wdiaSiguiente' id='wdiaSiguiente' value='".$diaSiguiente."'>";
			
			if($kardexActual->noAcumulaSaldoDispensacion){
				echo "<input type='hidden' name='wkardexnoacumula' id='wkardexnoacumula' value='on'>";
			} else {
				echo "<input type='hidden' name='wkardexnoacumula' id='wkardexnoacumula' value='off'>";
			}

			//Indicador si es primer kardex o no. Es verdadero si no hay kardex del dia o del dia anterior.
			if($kardexActual->esPrimerKardex){
				echo "<input type='hidden' id='wkardexnuevo' value='S'>";			
			} else {
				echo "<input type='hidden' id='wkardexnuevo' value='N'>";
			}
			
			$estilosFrame = "";
			$posicionFrame = "position:absolute;top:68px;left:650px;z-index:99;";
			
			echo "<script>";
			echo "llamarIframeAlerta('".$paciente->historiaClinica."','".$paciente->ingresoHistoriaClinica."','".$wemp_pmla."','".$estilosFrame."',true,true,1,'".$posicionFrame."')";
			echo "</script>";
			
			echo "<table border=0>";
			echo "<tr>";
			echo "<td class='subtituloPagina2' width=350>";
//			echo '<span class="subtituloPagina2" nowrap align="center">';
			if($kardexActual->editable){
				if($cargarDefinitivo){
					echo "Crear orden del d&iacute;a ".$wfecha;
				} else {
					echo "Editar orden del d&iacute;a ".$wfecha;
				}
			} else {
				echo "Consultar orden del d&iacute;a $wfecha";	
			}

			$accionesPestana = consultarAccionesPestana( "3" );
			
			$cambiaConfirmado = false;
			$confirmaAutomaticamente = "";
			//Si un usuario puede leer y crear, el campo confirmado siempre al guardar quedará confirmado
			//Si puede crear pero no leer, siempre quedará desconfirmado
			//Ninguna de las anteriores siempre queda como estaba el kardex
			if( $usuario->firmaElectronicamente && $paciente->enUrgencias ){
				$confirmaAutomaticamente = "checked";
				echo "<INPUT type='hidden' id='hiNoParpadear' name='hiNoParpadear' value='on'>";
			}
			elseif( !empty( $accionesPestana["3.99"]->crear ) && $accionesPestana["3.99"]->leer === true && $accionesPestana["3.99"]->crear === true ){
				$confirmaAutomaticamente = "checked";
				echo "<INPUT type='hidden' id='hiNoParpadear' name='hiNoParpadear' value='on'>";
			}
			elseif( !empty( $accionesPestana["3.99"]->crear ) && $accionesPestana["3.99"]->crear === true && $accionesPestana["3.99"]->leer === false ){
				$cambiaConfirmado = true;
				$confirmaAutomaticamente = ( $kardexActual->confirmado == 'on' ) ? "checked" : "";
				echo "<INPUT type='hidden' id='hiNoParpadear' name='hiNoParpadear' value='on'>";
			}
			else{
				$confirmaAutomaticamente = ( $kardexActual->confirmado == 'on' ) ? "checked" : "";
				echo "<INPUT type='hidden' id='hiNoParpadear' name='hiNoParpadear' value='on'>";
			}
			
			echo "<INPUT type='hidden' id='hiCambiaConfimado' name='hiCambiaConfimado' value='".( $cambiaConfirmado ? "on": "off" )."'>";
			
//			echo "</span>";
			echo "</td>";
			
			// $confirmaAutomaticamente = 'checked';	//Siempre queda confiramdo las ordenes Febrero 02 de 2015
			echo "<td width='25%' align='right'>";
			if($kardexActual->editable)
			{
				if($usuario->firmaElectronicamente)
					echo "<div id='btnCerrarVentana1'><br /><input type=button value='Salir sin grabar' onclick='salir_sin_grabar();'><br/></div>";
				else
					echo "<div id='btnCerrarVentana1'><br /><input type=button value='Salir sin grabar' onclick='salir_sin_grabar();'><br/></div>";
			} 
			else 
			{
				echo "<br/><input type=button value='Regresar' onclick='salir_sin_grabar();'><br/>";
			}
			echo "</td>";
					
			echo "<td width='50%' align='right'>";
 			if($kardexActual->editable){
 				if(!$usuario->firmaElectronicamente){
 					
 					if(true || $kardexActual->confirmado == "on"){
						if( $usuario->esEnfermeraRolHCE )
							echo "<input type='checkbox' name='wcconf' id='wcconf' onClick='marcarKardexConfirmado();' $confirmaAutomaticamente style='display:none'>";
						else
							echo "<input type='checkbox' name='wcconf' id='wcconf' onClick='marcarKardexConfirmado();' $confirmaAutomaticamente style='display:none'>";
 					}
 					else{
 						echo "<input type='checkbox' name='wcconf' id='wcconf' onClick='marcarKardexConfirmado();'>";
 					}
					echo "<div id='btnGrabar1' onclick='grabarKardex();' style='cursor:pointer;'><table width='100%'><tbody><tr><td align=left><img src='/matrix/images/medical/hce/ok.png'></td></tr></tbody></table></div>";
 					echo "&nbsp;|&nbsp;";
 					echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='abrirModalHCE();'>Vistas Asociadas</A>";
 				} 
				elseif( $paciente->enUrgencias ){
					echo "<input type='checkbox' name='wcconf' id='wcconf' onClick='marcarKardexConfirmado();' $confirmaAutomaticamente style='display:none'>";
					echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='abrirModalHCE();'>Vistas Asociadas</A>";
				}
				else {
 					echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='abrirModalHCE();'>Vistas Asociadas</A>";
 				}
 			} 

			echo "</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br>";

			//El encabezado es comun a todas las secciones del kardex
			echo "<table align='center'>";

			echo "<tr>";

			echo "<td class='fila1'>Historia cl&iacute;nica</td>";
			echo "<td class='fila2'>";
			echo $paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica;
			echo "</td>";
			
			$tipo = $color = '';
			clienteMagenta( $paciente->documentoIdentidad, $paciente->tipoDocumentoIdentidad, $tipo, $color );

			if( empty($tipo) ){
				echo "<td class='fila1' align=center rowspan=2><b><font size=3>Paciente</font></b></td>";
				echo "<td class='fila2' align=center colspan=3 rowspan=2><b><font size=3>";
				echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
				echo "</font></b></td>";
			}
			else{
				echo "<td class='fila1' align=center rowspan=2><b><font size=2>Paciente</font></b></td>";
				echo "<td class='fila2' align=center colspan=2 rowspan=2><b><font size=3>";
				echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
				echo "</font></b></td>";
				echo "<td class='fila1' align=center><b><font style='font-size:10pt'>";
				echo "Afinidad:";
				echo "</font></b></td>";
			}
				
			echo "</tr>";

			echo "<tr>";

			//Servicio actual y habitacion
			echo "<td class='fila1'>Servicio y Habitaci&oacute;n actual</td>";
			echo "<td class='fila2'>";
			echo "$paciente->nombreServicioActual - $paciente->habitacionActual";
			echo "</td>";
			
			if( !empty($tipo) ){
				echo "<td bgcolor='$color' align=center><b><font size=2 color='white'>";
				echo $tipo;
				echo "</font></b></td>";				
			}

			//Enfermera(o) que genera
			echo "<tr>";
			echo "<td class='fila1'>Usuario que actualiza (Codigo y nombre del Rol)</td>";
			echo "<td class='fila2'>";
			echo "$usuario->codigo - $usuario->descripcion. <br>$usuario->nombreCentroCostos ($usuario->codigoRolHCE-$usuario->nombreRolHCE)";
			echo "</td>";
			
			echo "<td class='fila1'>Fecha y hora de generaci&oacute;n</td>";
			echo "<td class='fila2'>";
			echo "".$kardexActual->fechaCreacion." - ".$kardexActual->horaCreacion;
			echo "</td>";

			echo "<td class='fila1'>Fecha y hora de ingreso a la instituci&oacute;n</td>";
			echo "<td class='fila2'>";
			echo "$paciente->fechaIngreso - $paciente->horaIngreso";
			echo "</td>";
			
			echo "</tr>";
			
			echo "<tr>";

			//Valor de la edad
			$vecAnioNacimiento = explode("-",$paciente->fechaNacimiento);
			echo "<td class='fila1'>Edad</td>";
			echo "<td class='fila2'>";
			echo $paciente->edadPaciente;
			echo "</td>";

			echo "<td class='fila1'>Ultimo mvto hospitalario</td>";
			
			if($paciente->altaDefinitiva == 'on'){
				echo "<td class='articuloControl'>";
			} else {
				echo "<td class='fondoAmarillo'>";
			}
			echo $paciente->ultimoMvtoHospitalario;						
			echo "</td>";
						
			//Calculo de dias de hospitalizcion desde ingreso
			$diaActual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$fecha = explode("-",$paciente->fechaIngreso);
			$diaIngreso = mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);

			$diasHospitalizacion = ROUND(($diaActual - $diaIngreso)/(60*60*24));

			echo "<td class='fila1'>D&iacute;as de hospitalizaci&oacute;n</td>";
			echo "<td class='fila2'>";
			echo "".$diasHospitalizacion;
			echo "</td>";
			
			echo "<td colspan=2>&nbsp;</td>";
			
			echo "</tr>";
			
			echo "<tr>";		
			
			//Responsable
			echo "<td class='fila1'>Entidad responsable</td>";
			echo "<td class='fila2'>";
			echo "$paciente->numeroIdentificacionResponsable - $paciente->nombreResponsable";
			echo "</td>";
			
			//Fecha y hora de ingreso al servicio actual
			echo "<td class='fila1'>Fecha de ingreso al servicio actual</td>";
			echo "<td class='fila2'>";
			echo "$paciente->fechaHoraIngresoServicio";						
			echo "</td>";
			
			echo "<td class='fila1'>";
			
			//Boton de vistas asociadas
//			echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='abrirModalHCE();'>Vistas Asociadas</A>";
			
			echo "</td>";
			echo "<td class='fila2'>&nbsp;";
//			echo "<a href='#null' onclick='return fixedMenu.show();'>Alergias</a>";						
			echo "</td>";
			
			echo "</tr>";
			
			echo "<tr>";
			echo "<td height=30 colspan=6>&nbsp;</td>";
			echo "</tr>";
			
			echo "</table>";
			
			/************************************************
			 * Agrengando mensajeria
			 ************************************************/
			//Campo oculto que indica de que programa se abrio
			$indicePestana = "12";
			$accionesPestana = consultarAccionesPestana($indicePestana);			
			$accion = @$accionesPestana[$indicePestana.".1"];
			
			$lectura_mensajeria = "";
			
			//Consulta si el rol que ingresa puede ver la mensajeria, si no la puede ver la oculta.
			if(!$accion->leer){
			
				$lectura_mensajeria = ';display:none';
				
				}
				
				echo "<INPUT type='hidden' id='mesajeriaPrograma' value='Ordenes'>";
				 
				echo "<table style='width:80%;font-size:10pt $lectura_mensajeria' align='center'>";
				
				echo "<tr><td class='encabezadotabla' align='center' colspan='3'>Mensajer&iacute;a Kardex</td></tr>";
				
				echo "<tr>";
				
				//Area para escribir
				echo "<td style='width:45%;' rowspan='2'>";
				// echo "<textarea id='mensajeriaKardex' onKeyPress='return validarEntradaAlfabetica(event);' style='width:100%;height:80px'></textarea>";
				echo "<textarea id='mensajeriaKardex' style='width:100%;height:80px'></textarea>";	//Noviembre 21 de 2011
				echo "</td>";
				
				//Boton Enviar mensaje
				echo "<td align='center' style='width:10%'>";
				echo "<input type='button' onClick=\"enviandoMensaje()\" value='Enviar' style='width:100px'>";
				echo "</td>";
				
				//Mensajes
				echo "<td style='width:45%' rowspan='2'>";
				echo "<div id='historicoMensajeria' style='overflow:auto;font-size:10pt;height:80px'>";
				echo "</div>";
				echo "</td>";
				
				echo "</tr>";
				
				echo "<tr>";
				echo "<td align='center'><b>Mensajes sin leer: </b><div id='sinLeer'></div></td>";
				echo "</tr>";
				
				echo "</table>";
			
			/****************************************************************/
				
			/***************************
			 * Movimiento de articulos
			 ***************************/
			if($kardexActual->editable){
				echo "<div id='fixeddiv2' style='position:absolute;display:none;z-index:200;width:450px;height:425px;left:200px;top:10px;padding:5px;background:#FFFFFF;border:2px solid #2266AA'>";
				echo "<table>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='encabezadoTabla'>";
				echo "<b>Buscador de medicamentos</b>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";

				echo "<td class='fila1'>C&oacute;digo</td>";
				echo "<td class='fila2'>";
				echo "<INPUT TYPE='text' NAME='wcodmed' id='wbcodmed' SIZE=20  class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarMedicamento();\");'>";
				echo "</td>";

				echo "</tr>";

				echo "<tr>";
				echo "<td class='fila1'>Nombre</td>";
				echo "<td class='fila2'>";
				echo "<INPUT TYPE='text' NAME='wnommed' id='wbnommed' SIZE=20 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarMedicamento();\");'>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='fila1'>";
				echo "<b>Parametros de consulta</b>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='fila2'>";
				echo "N.Genérico<input type='radio' id='wtipoart' name='wtipoart' value='G'>&nbsp;N.Comercial<input type='radio' id='wtipoart' name='wtipoart' value='C' checked>";
				echo " | ";

				echo "<select id='wunidadmed' name='wunidadmed' class='seleccionNormal'>";

				echo "<option value='%'>Cualquier unidad de medida</option>";
				foreach ($colUnidades as $unidad){
					echo "<option value='".$unidad->codigo."'>$unidad->codigo - $unidad->descripcion</option>";
				}

				echo "</select>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='fila1'>";
				echo "<b>Protocolo</b>";
				echo "</td>";
				echo "</tr>";
					
				echo "<tr>";
				echo "<td colspan=4 align=center class='fila2'>";
				echo "Normal<input type='radio' id='wtipoprot' name='wtipoprot' value='$protocoloNormal' checked onClick='limpiarBuscador();'>&nbsp;|&nbsp;";
				echo "Analgesia<input type='radio' id='wtipoprot' name='wtipoprot' value='$protocoloAnalgesia' onClick='limpiarBuscador();'>&nbsp;|&nbsp;";
				echo "Nutrici&oacute;n<input type='radio' id='wtipoprot' name='wtipoprot' value='$protocoloNutricion' onClick='limpiarBuscador();'>&nbsp;|&nbsp;";
				echo "Quimioterapia<input type='radio' id='wtipoprot' name='wtipoprot' value='$protocoloQuimioterapia' onClick='limpiarBuscador();'>";
					
				echo "</td>";
				echo "</tr>";
					
				echo "<tr><td colspan=4 align=center>";
				echo "<input type='button' value='Consultar' onclick='consultarMedicamento();'>&nbsp;|&nbsp;<input type='button' value='Agregar medicamento' onclick='agregarArticulo(\"detKardexAdd\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar' onclick='return fixedMenu2.hide();'>";
				echo "</td></tr>";

				echo "<tr>";
				echo "<td colspan=4 class='fila2'>";
				echo "<img id='imgCodMed' style='display:none' src='../../images/medical/ajax-loader5.gif'>";
				echo "<div id='cntMedicamento' style='overflow-y: scroll; width: 100%; height: 160px;'>";
				echo "</div>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 class='fila2'>";
				echo "<span><b>NOTA: </b>Realice su búsqueda específica, este buscador retornará hasta cien resultados</span>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td class='fila1'>";
				echo "Grupos de medicamentos";
				echo "</td>";
				echo "<td class='fila2' width='350px'>";
				echo $usuario->gruposMedicamentos;
				echo "</td>";
				echo "</tr>";
					
				echo "</table>";
				echo "</div>";
			}
				
			/*****************
			 * INICIO DE LA DIVISIÓN POR PESTAÑAS.
			 *****************/
			$indicePestana = "1";
			$vecPestanaGrabacion = array();
			
			
			/************************************************************************************************
			 * Agosto 4 de 2014
			 * Prueba de articulos
			 ************************************************************************************************/
			$pesMedicamentosBD = consultarAliasPorAplicacion( $conex, $wemp_pmla, "pesMedicamentos" );
			$lenguajeAmericas = consultarAliasPorAplicacion( $conex, $wemp_pmla, "lenguajeAmericas" );			
			$pesMedicamentosBD .= ";LEV E INFUSIONES-LQ-LQ";

			$expPesMedicamentosBD = explode( ";", $pesMedicamentosBD );
			
			$indexArPes = 0;
			foreach( $expPesMedicamentosBD as $keyPesMed => $valuePesMed ){
				
				$expDatos = explode( "-", $valuePesMed );
				
				if( $indexArPes > 0 ){
					$arTiposProtocolos[] = explode( ",", $expDatos[1] );
					$arPesMedicamentos[] = count($arPesMedicamentos) + 11;
					$arNomPes[] = $expDatos[0];	//Nombre pestaña
					$arTiposArticulos[] = explode( ",", $expDatos[2] );
				}
				else{
					$arTiposProtocolos[] = Array( 0 => "N" );
					$arPesMedicamentos[] = 3;
					$arNomPes[] = "Medicamentos";	//Nombre pestaña
					$arTiposArticulos[] = explode( ",", $expDatos[2] );
				}
				
				$indexArPes++;
			}
			/************************************************************************************************/
			
			//Mensaje de espera
			echo "<div id='msjInicio' align=center>";
			echo "<img src='../../images/medical/ajax-loader5.gif'/>Cargando las pestañas, por favor espere...";
			echo "</div>";

			echo "<input type=hidden id='pestanasVistas' name='pestanasVistas' value=''>";
			echo "<input type=hidden id=hpestanas value='$usuario->pestanasKardex'>";
			
			echo "<div id='tabs' class='ui-tabs' style='display:none'>";				//Inicio de lo que va a ir encerrado en las pestañas
			echo "<ul>";
			if($usuario->pestanasKardex == "*"){
				/******************************************************************************
				 * Las pestañas se fragmentan de la siguiente manera:
				 * 
				 * 1.  Codigo
				 * 2.  Nombre
				 * 3.  Puede grabar
				 ******************************************************************************/
				if($activarPestanas){
					$vecPestanas = explode(";",$usuario->pestanasHCE);
//					var_dump($vecPestanas);
										
					foreach($vecPestanas as $pestana){
						$vecPestanaElemento = explode("|",$pestana);
						
						if($vecPestanaElemento[0] != ''){
							echo "<li><a href='#fragment-$vecPestanaElemento[0]' onclick='mostrar_mensaje($vecPestanaElemento[0]);'><span>$vecPestanaElemento[1]</span></a></li>";
							$vecPestanaGrabacion[$vecPestanaElemento[0]] = ($vecPestanaElemento[2] == 'on');
						}
					}
				}
			}
			echo "</ul>";
			
			if(count($vecPestanaGrabacion) == 0){
				mensajeEmergente("No tiene configurados pestañas para usar ordenes.");
				die("");
			}
	
			//PESTAÑA DE INFORMACION DEMOGRAFICA
			if(isset($vecPestanaGrabacion[$indicePestana])){
				/************************************************************************************************************************
				 * CONSULTA DE ACCIONES POR CADA PESTAÑA 
				 ************************************************************************************************************************/
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				echo "<div id='fragment-1'>";

				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";					
				
				echo "<table align='center'>";

				echo "<tr>";
				
				if($kardexActual->talla == "")
				{
					// consecutivo 136
					$kardexActual->talla = consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "tallaHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
				}
				
				
				if($kardexActual->peso == "")
				{
					$esAdulto = consultarSiAdulto($paciente->fechaNacimiento);
					
					// Validar edad del paciente, si es menor a 15 años, 2 meses y 15 dias consecutivo 135 o si es mayor 134
					$parametroPeso = "";
					if($esAdulto)
					{
						$parametroPeso = "pesoHCE";
					}
					else
					{
						$parametroPeso = "pesoHCEpediatrico";
					}
					
					// $kardexActual->peso = consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "pesoHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
					$kardexActual->peso = consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, $parametroPeso, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
				}
				
				
				//Talla
				echo "<td class='fila1'><b>Talla (cm.)&nbsp;</b>";
				//Consulta del nombre de la accion
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					
					// $kardexActual->talla = consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "tallaHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
					// crearCampo("1","txTalla",@$accionesPestana[$indicePestana.".1"],array("maxlength"=>"3","size"=>"5","class"=>"textoNormal","onKeyPress"=>"return validarEntradaEntera(event)","readonly"=>""),$kardexActual->talla);
					
					crearCampo("1","txTalla",@$accionesPestana[$indicePestana.".1"],array("maxlength"=>"3","size"=>"5","class"=>"textoNormal","onKeyPress"=>"return validarEntradaEntera(event)"),$kardexActual->talla);					
					
//					echo "<input type=text name=txTalla class='textoNormal' maxlength=4 size=5 value='$kardexActual->talla' idGlobal='{$tablaIDAcciones[$indicePestana."-"."txTalla"]}'/>";
				} else {
					echo "$kardexActual->talla";
				}
				echo "</td>";

				//Peso
				echo "<td class='fila1'><b>Peso (kg.)&nbsp;</b>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					// $kardexActual->peso = consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "pesoHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
					// crearCampo("1","txPeso",@$accionesPestana[$indicePestana.".2"],array("maxlength"=>"5","size"=>"5","class"=>"textoNormal", "readonly"=>""),$kardexActual->peso);
					
					crearCampo("1","txPeso",@$accionesPestana[$indicePestana.".2"],array("maxlength"=>"5","size"=>"5","class"=>"textoNormal","onKeyPress"=>"return validarEntradaDecimal(event)"),$kardexActual->peso);					
					
//					echo "<input type=text name=txPeso class='textoNormal' maxlength=5 size=5 value='$kardexActual->peso' idGlobal='{$tablaIDAcciones[$indicePestana."-"."txPeso"]}'/>";
				} else {
					echo "Peso (kg.)&nbsp;$kardexActual->peso";
				}
				echo "</td>";

				// echo "<td class='fila1'>&nbsp;</td>";
				echo "</tr>";

				//Diagnostico actual
				echo "<tr>";
				echo "<td align=center class='fila1'>";
				echo "<b>Diagn&oacute;stico actual</b></br>";
				
				/****************************************************************************************************
				 * Marzo 30 de 2015
				 ****************************************************************************************************/
				// $kardexActual->diagnostico = consultarDxs( $conex, $wemp_pmla, $wbasedatohce, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
				$txtDiagHCE = trim( consultarDxs( $conex, $wemp_pmla, $wbasedatohce, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica ) );
				
				// if( trim($kardexActual->diagnostico) == "" || ( trim($kardexActual->diagnostico) != "" && trim($kardexActual->diagnostico) == $txtDiagHCE ) ){
					$kardexActual->diagnostico = $txtDiagHCE;
				// }
				/****************************************************************************************************/
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2",'txDiag',@$accionesPestana[$indicePestana.".3"],array("cols"=>"60","rows"=>"8"),"$kardexActual->diagnostico");
//					echo "<textarea name='txDiag' cols=40 rows=8 idGlobal='{$tablaIDAcciones[$indicePestana."-"."txDiag"]}'>$kardexActual->diagnostico</textarea>";
				} else {
					echo "<textarea name='txDiag' cols=60 rows=8 readonly>$kardexActual->diagnostico</textarea>";
				}
				echo "<br/>&nbsp;</td>";

				// //Antecedentes alergicos
				// echo "<td align=center class='fila1'>";
				// echo "<b>Antecedentes al&eacute;rgicos y alertas</b></br>";
				// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
				
					// /****************************************************************************************************
					 // * Marzo 30 de 2015
					 // ****************************************************************************************************/
					// // $kardexActual->antecedentesAlergicos = consultarCamposHCEHistoricoAelergicos( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "antecedentesAlergicosHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
					// $antecedentesAlergicosHCE = trim( consultarCamposHCEHistoricoAelergicos( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "antecedentesAlergicosHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica ) );
				
					// if( trim($kardexActual->antecedentesAlergicos) == "" || ( trim($kardexActual->antecedentesAlergicos) != "" && trim($kardexActual->antecedentesAlergicos) == $antecedentesAlergicosHCE ) ){
						// $kardexActual->antecedentesAlergicos = $antecedentesAlergicosHCE;
					// }
					// /****************************************************************************************************/
				
					// crearCampo("2","txAlergias",@$accionesPestana[$indicePestana.".4"],array("cols"=>"40","rows"=>"8"),"$kardexActual->antecedentesAlergicos");
// //					echo "<textarea name='txAlergias' cols=40 rows=8 idGlobal='{$tablaIDAcciones[$indicePestana."-"."txAlergias"]}'>$kardexActual->antecedentesAlergicos</textarea>";
				// }else{
					// echo "<textarea name='txAlergias' cols=40 rows=8 readonly>$kardexActual->antecedentesAlergicos</textarea>";
				// }
				// echo "<br/>&nbsp;</td>";
					
				//Antecedentes personales
				echo "<td align=center class='fila1'>";
				echo "<b>Antecedentes personales</b></br>";
				
				/****************************************************************************************************
				 * Marzo 30 de 2015
				 ****************************************************************************************************/
				// $kardexActual->antecedentesPersonales = consultarCampoHCELabelValor( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "antecedentesPersonalesHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
				$txAntecedentesPersonalesHCE = trim( consultarCampoHCELabelValor( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "antecedentesPersonalesHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica ) );
				
				if( trim($kardexActual->antecedentesPersonales) == "" || ( trim($kardexActual->antecedentesPersonales) != "" && trim($kardexActual->antecedentesPersonales) == $txAntecedentesPersonalesHCE ) ){
					$kardexActual->antecedentesPersonales = $txAntecedentesPersonalesHCE;
				}
				/****************************************************************************************************/
				
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){					
					crearCampo("2",'txAntecedentesPersonales',@$accionesPestana[$indicePestana.".5"],array("cols"=>"60","rows"=>"8" ),"$kardexActual->antecedentesPersonales");
//					echo "<textarea name='txAntecedentesPersonales' cols=40 rows=8 idGlobal='{$tablaIDAcciones[$indicePestana."-"."txAntecedentesPersonales"]}'>$kardexActual->antecedentesPersonales</textarea>";
				} else {
					echo "<textarea name='txAntecedentesPersonales' cols=60 rows=8 readonly>$kardexActual->antecedentesPersonales</textarea>";
				}
				echo "<br/>&nbsp;</td>";				

				echo "</tr>";
				echo "</td>";
				echo "</tr>";
				
				//Medicamentos de consumo habitual
				echo "<tr>";
				// echo "<td align=center class='fila1' colspan=3>";
				echo "<td align=center class='fila1' colspan=2>";
				echo "<br><b>Medicamentos de consumo habitual</b></br>";
				
				$datosMedConsuHab = consultarMedConsuHabitual( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "MedicamentosConsumoHabitual", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
				
				$array_datosMedConsuHab = explode("*",$datosMedConsuHab); //Creo un arreglo con el valor de los med. de consu. habitual.
				unset($array_datosMedConsuHab[0]); //Se elimina la posicion 0 porque no contien valores para mostrar.
				
				$wtabla_medConsumoHabitual .= "<table >";
				$wtabla_medConsumoHabitual .= "<tr class=encabezadotabla><td>Medicamento</td><td>Dosis</td><td>Vía</td><td>Frecuencia</td><td>Indicación</td><td>Horario</td><td>Decisión</td><td>Observaciones</td><tr>";
				
				$array_final_mca = array();
				$datos_medConsumoHab = array();
				
				foreach($array_datosMedConsuHab as $key => $value){
					
					$datos_medConsumoHab = explode("|",$value);
					
					foreach($datos_medConsumoHab as $key1 => $value1){
												
						if( !array_key_exists( $key, $array_final_mca ) ){
							
							if($datos_medConsumoHab[2] == 'Seleccione'){
								$datos_medConsumoHab[2] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							
							if($datos_medConsumoHab[3] == 'Seleccione'){
								$datos_medConsumoHab[3] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							
							if($datos_medConsumoHab[4] == 'Seleccione'){
								$datos_medConsumoHab[4] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							
							if($datos_medConsumoHab[6] == 'Seleccione'){
								$datos_medConsumoHab[6] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							
							$array_via = explode("-", $datos_medConsumoHab[2]);
							$array_frecuencia = explode("-", $datos_medConsumoHab[3]);
							$array_decision = explode("-", $datos_medConsumoHab[6]);
							
							$array_final_mca[$key] = array('medicamento'=>$datos_medConsumoHab[0],'dosis'=>$datos_medConsumoHab[1], 'via'=>$datos_medConsumoHab[2], 'frecuencia'=>$datos_medConsumoHab[3], 'indicaciones'=>$datos_medConsumoHab[4],'horario'=>$datos_medConsumoHab[5],'decision'=>$datos_medConsumoHab[6], 'observaciones'=>$datos_medConsumoHab[7]); 
							$wtabla_medConsumoHabitual .= "<tr class=fila2><td>$datos_medConsumoHab[0]</td><td>$datos_medConsumoHab[1]</td><td>$array_via[1]</td><td>$array_frecuencia[1]</td><td>$datos_medConsumoHab[4]</td><td>$datos_medConsumoHab[5]</td><td>$array_decision[1]</td><td>$datos_medConsumoHab[7]</td></tr>"; 
												
						}					
					
					}				
					
				}				
				
				$wtabla_medConsumoHabitual .= "</table>";				
				
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					
					echo "<div>$wtabla_medConsumoHabitual</div>";

				} else {
					echo "<div>$wtabla_medConsumoHabitual</div>";
				}
				echo "<br/>&nbsp;</td>";
				echo "</tr>";
				echo "</table>";

				echo "<div align='center'>";
					
				//Muestra las alergias de los dias para eliminar
				$colAlergias = consultarAlergias($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);
				$colAlergias = array();	//Se deja siempre en blanco, ya que las alergías solo pueden ser editadas desde HCE
				if(count($colAlergias) > 0){
					echo "<table>";
					echo "<thead>";
					echo "<tr class=encabezadoTabla>";
					echo "<td colspan=3 align=center>Retiro de alergias anteriores</td>";
					echo "</tr>";
						
					echo "<tr class=encabezadoTabla align=center>";

					echo "<td>Fecha de registro</td>";
					echo "<td>Descripcion</td>";
					echo "<td>Accion</td>";

					echo "</tr>";
					echo "</thead>";

					$clase="fila1";

					echo "<tbody id='detAlergias'>";
						
					foreach ($colAlergias as $alergia){
						if($clase=="fila1"){
							$clase = "fila2";
						} else {
							$clase = "fila1";
						}

						echo "<tr class=$clase id='trAle$alergia->descripcion'>";

						echo "<td>$alergia->descripcion</td>";
						echo "<td>$alergia->observacion</td>";
						echo "<td align='center'>";
						crearCampo("4","",@$accionesPestana[$indicePestana.".6"],array("onClick"=>"quitarAlergia('$alergia->descripcion');"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' alt='Quitar alergia'/>");
//						echo "<a href='#null' onclick='quitarAlergia("."\"$alergia->descripcion"."\");'><img src='../../images/medical/root/borrar.png' alt='Quitar alergia'></a>";
						echo "</td>";

						echo "</tr>";
					}
					echo "</tbody>";
					echo "</table>";
				}
				echo "</div>";

				echo "</div>";
			}
			
			$indicePestana = "2";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-2'>";
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";					
				
				$cont1 = 0;
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<table align='center'>";
//					echo "<tr>";
//
//					echo "<td class='fila1'>C&oacute;digo</td>";
//					echo "<td class='fila2'>";
//					echo "<INPUT TYPE='text' NAME='wcodcom' SIZE=10 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarComponente()\");'>";
//					echo "</td>";
//
//					echo "<td rowspan=2 colspan=2 class='fila2'>";
//					echo "<img id='imgCodCom' style='display:none' src='../../images/medical/ajax-loader5.gif'>";
//					echo "<div id='cntComponente' style='overflow-y: scroll; width: 430px; height: 160px;'>";
//					echo "</div>";
//					echo "</td>";
//
//					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 align=center  class='encabezadoTabla'>";
					echo "<b>Consulta</b>";
					echo "</td>";
					echo "</tr>";
						
					echo "<tr>";
					echo "<td class='fila1'>Componente</td>";
					echo "<td class='fila2'>";
					crearCampo("1","wnomcom",@$accionesPestana[$indicePestana.".1"],array("size"=>"60","class"=>"textoNormal","onBlur"=>"this.value=''"),"");
//					echo "<INPUT TYPE='text' NAME='wnomcom' id='wnomcom' SIZE=60 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarComponente()\");' onBlur='this.value=\"\"'>&nbsp;|&nbsp;";
					echo "&nbsp;|&nbsp;";
					crearCampo("3","",@$accionesPestana[$indicePestana.".2"],array("size"=>"60","onClick"=>"agregarInfusion();"),"Ordenar...");
//					echo "<input type='button' value='Ordenar...' onClick='agregarInfusion();' >";
					echo "</td>";
					echo "</tr>";
						
						
//					echo "<tr>";
//					echo "<td colspan=4 align=center class='fila2'>";
//					echo "Nombre Genérico<input type='radio' id='wtipocom' name='wtipocom' value='G'>&nbsp;Nombre Comercial<input type='radio' id='wtipocom' name='wtipocom' value='C' checked>";
//					echo " | ";
//					echo "Unidad de medida&nbsp;";
//					echo "<select id='wunidadcom' name='wunidadcom' class='seleccionNormal'>";
//						
//					$colUnidades = consultarUnidadesMedida();
//						
//					echo "<option value='%'>Cualquier unidad de medida</option>";
//					foreach ($colUnidades as $unidad){
//						echo "<option value='".$unidad->codigo."'>$unidad->codigo - $unidad->descripcion</option>";
//					}
//
//					echo "</select>";
//						
//					echo "</td>";
//					echo "</tr>";
						
//					echo "<tr><td colspan=4 align=center>";
//					echo "<input type='button' value='Consultar' onclick='consultarComponente();' >&nbsp;|&nbsp;";
//					echo "<input type='button' value='Ordenar programa' onclick='agregarInfusion();' >";
//					echo "</td></tr>";
					echo "</table>";
					$cont1++;
				}

				echo '<br><span class="subtituloPagina2" align="center">';
				echo "Programas actuales";
				echo "</span>";
				echo "<br>";
				echo "<br>";
					
				echo "<table align='center' border=0 id='tbDetInfusiones'>";

				echo "<tr align='center'>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<td class='encabezadoTabla'>";
					echo "Acciones";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.7' id='wacc$indicePestana.7' value='".accionesATexto(@$accionesPestana[$indicePestana.".7"])."'>";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.3' id='wacc$indicePestana.3' value='".accionesATexto(@$accionesPestana[$indicePestana.".3"])."'>";
					echo "</td>";
					echo "<td class='encabezadoTabla'>";
					echo "Fecha de solicitud";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.4' id='wacc$indicePestana.4' value='".accionesATexto(@$accionesPestana[$indicePestana.".4"])."'>";
					echo "</td>";
					echo "<td class='encabezadoTabla'>";
					echo "Componentes";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.5' id='wacc$indicePestana.5' value='".accionesATexto(@$accionesPestana[$indicePestana.".5"])."'>";
					echo "</td>";
					echo "<td class='encabezadoTabla'>";
					echo "Observaciones";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.6' id='wacc$indicePestana.6' value='".accionesATexto(@$accionesPestana[$indicePestana.".6"])."'>";
					echo "</td>";
				}else{
					echo "<td class='encabezadoTabla'>Fecha de solicitud</td>";
					echo "<td class='encabezadoTabla'>Componentes</td>";
					echo "<td class='encabezadoTabla'>Observaciones</td>";
				}
				echo "</tr>";

				echo "<tbody id='detInfusiones'>";

				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana] && $kardexActual->esAnterior && $esFechaActual){
					//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
					$colTemporal = consultarInfusionesTemporalKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					if(count($colTemporal) == 0){
						cargarInfusionesAnteriorATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$fechaGrabacion);
					}
				}

				//1. Consulta de estructura temporal.
				$componentesInfusion = consultarInfusionesTemporalKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				$cuentaInfusiones = count($componentesInfusion);
				$contInfusiones = 0;

				if($cuentaInfusiones == 0){
					$componentesInfusion = consultarInfusionesDefinitivoKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					$cuentaInfusiones = count($componentesInfusion);

					if($cuentaInfusiones > 0 && $esFechaActual){
						//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
						cargarInfusionesATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$wfecha);
					}
				}

				$mayorIdInfusiones = $cuentaInfusiones;
				$cont1 = 0;
				foreach ($componentesInfusion as $infusion){
					if($cont1 % 2 == 0){
						echo "<tr id='trIn$infusion->codigo' class='fila1'>";
					} else {
						echo "<tr id='trIn$infusion->codigo' class='fila2'>";
					}

					if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						echo "<td align='center'>";
//						echo "<a href='#null' onclick='grabarInfusion($infusion->codigo);'><img src='../../images/medical/root/grabar.png'/></a>";
						crearCampo("4","",@$accionesPestana[$indicePestana.".3"],array("onClick"=>"quitarInfusion($infusion->codigo);"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' />");
//						echo "<a href='#null' onclick='quitarInfusion($infusion->codigo);'><img src='../../images/medical/root/borrar.png'/></a>";
						
						echo "<INPUT TYPE='hidden' name='wmodificado$indicePestana$infusion->codigo' id='wmodificado$indicePestana$infusion->codigo' value='N'>";
						echo "<INPUT TYPE='hidden' name='windiceliq$contInfusiones' id='windiceliq$contInfusiones' value='$infusion->codigo'>";
						
						echo "</td>";
					}
						
					//Fecha de solicitado examen
					echo "<td align=center>";
					
					echo "<INPUT TYPE='text' name='wfliq$infusion->codigo' id='wfliq$infusion->codigo' SIZE=10 readonly class='campo2' value='$infusion->fecha' onChange='marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>";
					if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						crearCampo("3","btnFechaLiq$infusion->codigo",@$accionesPestana[$indicePestana.".4"],array("size"=>"60","onClick"=>"calendario4($infusion->codigo);"),"*");
//						echo "<input type='button' id='btnFechaLiq$infusion->codigo' onclick='calendario4($infusion->codigo);' height=20 value='*'>";
					}
					echo "</td>";

					//Componentes de la infusion en forma de textarea
					echo "<td>";
					crearCampo("2","wtxtcomponentes$infusion->codigo",@$accionesPestana[$indicePestana.".5"],array("cols"=>"65","rows"=>"5","readonly"=>"readonly","onChange"=>"marcarCambio('$indicePestana','$infusion->codigo');"),str_replace(';',"\r\n",$infusion->descripcion));
//					echo "<textarea id=wtxtcomponentes$infusion->codigo cols=65 rows=5 readonly onChange='marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>".str_replace(';',"\r\n",$infusion->descripcion)."</textarea>";
					echo "</td>";

					//Componentes de la infusion en forma de textarea
					echo "<td>";
					crearCampo("2","wobscomponentes$infusion->codigo",@$accionesPestana[$indicePestana.".6"],array("cols"=>"65","rows"=>"5","onChange"=>"marcarCambio('$indicePestana','$infusion->codigo');"),str_replace(';',"\r\n",$infusion->observacion));
//					echo "<textarea id=wobscomponentes$infusion->codigo cols=65 rows=5 onChange='marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>".str_replace(';',"\r\n",$infusion->observacion)."</textarea></td>";
					echo "</tr>";

					if(intval($mayorIdInfusiones) < intval($infusion->codigo)){
						$mayorIdInfusiones = intval($infusion->codigo+1);
					}

					$contInfusiones++;
				}

				echo "</tbody>";
				echo "</table>";
				echo "</div>";
			}
			
			
				$indicePestana = 3;
				if(isset($vecPestanaGrabacion[$indicePestana]))
				{
					echo "<div id='fragment-$indicePestana'>";

					$accionesPestana = consultarAccionesPestana($indicePestana);
					// $accionesPestana = consultarAccionesPestana(3);

					//Indicador para javascript de puede grabar la pestaña
					$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
					echo "<input type=hidden id='pestana' value='$indicePestana'>";

					$elementosActuales = 0;
					$colDetalle = array();
					$colArticulo = array();

					//Quemó el valor de la pestaña para no repetir los protocolos
					if( $indicePestana == 3 && $usuario->firmaElectronicamente ){
						$listaProtocolos = generarListaProtocolos('wprotocolo',$usuario->codigo,$paciente->servicioActual,'Medicamentos');
						
						vista_generarConvencion($listaProtocolos);		//Genera la muestra de convenciones de los articulos
					}else{
						
						$listaProtocolos = "";
						vista_generarConvencion($listaProtocolos);
					}

					
					// if($usuario->firmaElectronicamente && $kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					
					//El usuario debe poder firmar la orden para que el buscador de articulos de muestre
					if( $kardexActual->editable ){
					
						if( true || $vecPestanaGrabacion[$indicePestana] ){
						
							//Solo se muestra el buscador si el usuario firma electrónicamente
							$styleDivNuevoBuscador = "display:none;";
							if( $usuario->firmaElectronicamente && $vecPestanaGrabacion[$indicePestana] ){
								$styleDivNuevoBuscador = "";
							}
							
							$esEditable = $kardexActual->editable;
							
							if( $esEditable ){
								$eventosQuitarTooltip = " onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'";	//Creo los eventos que quitan el tooltip si el kardex es editable
							}

							echo "<div class='fondoAmarillo' style='border: 1px solid #333333; width:150% !important; width:110%; height:110px;$styleDivNuevoBuscador'>";
							echo "<table align='left' border='0' width='100%'>";
							echo "<tr class='fondoAmarillo'>";
							
							echo "<td align='left'>";
							
							echo "<table id='nuevoBuscador'>";
							echo "<tr class='encabezadotabla' align='center'>";
							echo "<td width='100'>Grabar</td>";
							echo "<td width='250'>Medicamento(*)</td>";
							echo "<td>Presentaci&oacute;n(*)</td>";
							echo "<td width='100'>Dosis(*)</td>";
							echo "<td>Unidad de medida(*)</td>";
							echo "<td width='100'>Frecuencia(*)</td>";						
							echo "<td width='100'>Vía(*)</td>";
							echo "<td width='100'>Fecha y hora incio(*)</td>";
							echo "<td width='100'>Condición</td>";
							echo "<td width='100'>Días tto.</td>";
							echo "<td width='100'>Dosis máx.</td>";
							echo "<td width='100'>Observaciones</td>";
							echo "<td width='100'>Grabar</td>";
							echo "</tr>";
							
							echo "<tr align='center'>";
							
							// Boton para el submit
							echo "<td><input type='button' name='btnGrabar4' value='OK' onClick='eleccionMedicamento()' /></td>";

							// Nombre
							echo "<td>";
							//echo "<INPUT TYPE='text' NAME='wnombremedicamento' id='wnombremedicamento' SIZE=100 class='textoNormal' onBlur='this.value=\"\"'>";
							// Llama a la función autocompletarParaBusqueMedicamentosPorFamilia en ordenes.js
							crearCampo("1","wnombrefamilia",@$accionesPestana[$indicePestana.".1"],array( "size"=>"50","class"=>"textoNormal"),"");

							//echo "<input type='button' value='Movimiento de articulos' onclick='abrirMovimientoArticulos(\"N\");'>";
							
							list( $famLEV, $famIC ) = explode( "-", consultarAliasPorAplicacion( $conex, $wemp_pmla, "famLEVIC" ) );
							
							// $famNPT = consultarAliasPorAplicacion( $conex, $wemp_pmla, "famNPT" );
							
							//Auto llenado de LEVS e INF
							echo "<br>";
							echo "<INPUT type='button' value='LEV' onClick='autoLEVeIC( \"$famLEV\", this, \"LEV\" )' style='width:75px;'> ";
							echo "<INPUT type='button' value='INFUSION CONTINUA' onClick='autoLEVeIC( \"$famIC\", this, \"IC\"  )' style='width:150px;'> ";
							
							if($accionesPestana["3.N16"]->crear)
							{
								echo "<INPUT type='button' id='botonNPT' name='botonNPT' value='NPT' onClick='autoNPT( \"$famNPT\", this, \"NPT\"  )' style='width:50px;'> ";
							}
							
							echo "</td>";
							echo "</td>";
							
							// Presentacion
							echo "<td>";
							crearCampo("6","wpresentacion",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"","onChange"=>"filtrarMedicamentosPorCampo('presentacion')")," &nbsp; &nbsp; &nbsp; ");
							echo "</td>";
							
							// Dosis
							echo "<td>";
							//crearCampo("1","wdosisfamilia",@$accionesPestana[$indicePestana.".1"],array("size"=>"3","class"=>"textoNormal","onChange"=>"eleccionMedicamento(this.value)","onKeyPress"=>"eleccionPreviaMedicamento(this, event)"),"");
							crearCampo("1","wdosisfamilia",@$accionesPestana[$indicePestana.".1"],array("size"=>"3","class"=>"textoNormal", "onkeypress"=>"return validarEntradaDecimal(event);"),"");
							echo "</td>";
							
							// Unidad de medida
							echo "<td>";
							crearCampo("6","wunidad",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"","onChange"=>"filtrarMedicamentosPorCampo('unidad')")," &nbsp; &nbsp; &nbsp; ");
							echo "</td>";
							
							
							//Frecuencia
							$equivalenciaPeriodicidad = 0;
							echo "<td $eventosQuitarTooltip>";
							$opcionesSeleccion = "<option value='' selected>Seleccione</option>";
							foreach ($colPeriodicidades as $periodicidad){
								$opcionesSeleccion .= "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
							}
							
							// Se adiciona la opción de horario especial
							$opcionesSeleccion .= "<option value='H.E.'>H.E.</option>";
							crearCampo("6","wfrecuencia",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"","onChange"=>"eleccionFrecuencia(this)"),"$opcionesSeleccion");
							echo "<input type='hidden' id='wequdosis$articulo->tipoProtocolo$contArticulos' value='$equivalenciaPeriodicidad'>";
							echo "</td>";
							
							
							//Via administracion
							/* 2012-11-02
							// Se comenta porque ya se va a generar dinamicamente segun la familia seleccionada
							// Ya no se muestran todas las vias sino las asociadas a la familia
							echo "<td $eventosQuitarTooltip>";
							$opcionesSeleccion = "<option value='' selected>Seleccione</option>";
							foreach ($colVias as $via){
								$opcionesSeleccion .= "<option value='".$via->codigo."'>$via->descripcion</option>";
							}
							crearCampo("6","wadministracion",@$accionesPestana[$indicePestana."1"],array("class"=>"seleccion","onBlur"=>""),"$opcionesSeleccion");
							*/
							
							echo "<td $eventosQuitarTooltip>";
							crearCampo("6","wadministracion",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"")," &nbsp; &nbsp; &nbsp; ");
							echo "</td>";

							//Fecha y hora inicio
							// Encuentro la hora de inicio par siguiente
							$horParActInicial = floor(date("H" )/2) * 2;
							$horIniAdmInicial = "$horParActInicial:00:00";
							$ccoUrgencias = consultarCcoUrgencias();
							//Si el centro de costos es de urgencias toma la ronda actual, sino toma la ronda par siguiente. Jonatan 5 Nov 2014
							if($usuario->centroCostos != $ccoUrgencias && !$paciente->esDeAyudaDx ) {
								
								$fecIniAdmInicial = strtotime(date("Y-m-d $horIniAdmInicial")) + (60*60*2);	
								
								//Si falta menos de $tiempMinimoOrdenMedicamento para una ronda
								//Se pone para la ronda siguiente
								if( $fecIniAdmInicial <= time() + $tiempMinimoOrdenMedicamento*60 ){
									$fecIniAdmInicial += 2*60*60;
								}
							}
							else{
								$fecIniAdmInicial = strtotime(date("Y-m-d $horIniAdmInicial"));	
								
							}
													
							$fecIniAdmInicial = date("Y-m-d \a \l\a\s:H:i", $fecIniAdmInicial);

							echo "<td $eventosQuitarTooltip>";

							echo "<INPUT TYPE='hidden' NAME='whfinicioN999' id='whfinicioN999' SIZE=22 readonly class='campo2' value='$fecIniAdmInicial'>";

							echo "<INPUT TYPE='text' NAME='wfinicioaplicacion' id='wfinicioaplicacion' SIZE=25 readonly class='campo2' value='$fecIniAdmInicial'>";
							crearCampo("3","btnFechaN999",@$accionesPestana[$indicePestana.".N999"],array("onClick"=>"calendario5(999,'N');"),"*");
							//				echo "<input type='button' id='btnFecha$articulo->tipoProtocolo$contArticulos' onclick='calendario($contArticulos,\"$articulo->tipoProtocolo\");' value='*'>";
							echo "</td>";

							//Condicion de suministro
							echo "<td onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'>";
							$opcionesSeleccion = "<option value='' selected>Seleccione</option>";
							foreach ($colCondicionesSuministro as $condicion){
								$styleCondicion = "";
								if( $condicion->permiteDva ){
									$styleCondicion = "style='display:none'";
								}
								$opcionesSeleccion .= "<option $styleCondicion value='".$condicion->codigo."'>$condicion->descripcion</option>";
							}
							crearCampo("6","wcondicionsum",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"","onChange"=>"agregarDosisMaxPorCondicion( '', '', this )"),"$opcionesSeleccion");
							echo "&nbsp;</td>";
							
							//Dias tratamiento, debe mostrarse en un alt la fecha de terminación y los dias restantes
							$diasFaltantes = 0;
							$fechaFinal = "-";

							echo "<td $eventosQuitarTooltip>";
							crearCampo("1","wdiastratamiento",@$accionesPestana[$indicePestana.".1"],array("size"=>"3","maxlength"=>"3","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onKeyUp"=>"inhabilitarDosisMaximaBusc( this,'wdosismaxima' );"),"");
							echo "&nbsp;</td>";

							//Dosis máximas
							echo "<td $eventosQuitarTooltip>";
							crearCampo("1","wdosismaxima",@$accionesPestana[$indicePestana.".1"],array("size"=>"6","maxlength"=>"6","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onKeyUp"=>"inhabilitarDiasTratamientoBusc( this,'wdiastratamiento');"),"");
							echo "&nbsp;</td>";

							//Observaciones
							echo "<td $eventosQuitarTooltip>";
							crearCampo("2","wtxtobservasiones",@$accionesPestana[$indicePestana.".1"],array("cols"=>"40","rows"=>"2"),"");
							echo "</td>";
							
							// Boton para el submit
							echo "<td><input type='button' name='btnGrabar' value='OK' onClick='eleccionMedicamento()' /></td>";
							echo "</tr>";
							echo "</table>";
							
							echo "<div id='regletaGrabacion' align='left' style='display:none;padding-left:90px;'>";
							echo "<table>";
							echo "<tr class='encabezadoTabla'>";
							//echo "<td class=fila2 colspan='2'>";

							echo "<td> &nbsp; <b>Ronda</b> &nbsp; </td>";
							echo "<td>08</td>";
							echo "<td>10</td>";
							echo "<td>12</td>";
							echo "<td>14</td>";
							echo "<td>16</td>";
							echo "<td>18</td>";
							echo "<td>20</td>";
							echo "<td>22</td>";
							echo "<td>24</td>";
							echo "<td>02</td>";
							echo "<td>04</td>";
							echo "<td>06</td>";

							echo "</tr>";
							echo "<tr>";
							echo "<td> &nbsp; <b>Dosis</b> &nbsp; </td>";
							
							/*********************************************************
							 * 	GRAFICA DE LOS HORARIOS DE SUMINISTRO DE MEDICAMENTOS.
							 *
							 *
							 * 1.  Si la fecha de inicio es menor o igual a la de consulta del kardex se muestra la info
							 * 2.  Si se encuentra suspendido no se muestra la grafica
							 *********************************************************/
							//Grafica de los horarios ... 24 horas del dia.  Debe convertirse a horas cada periodicidad
							foreach ($colPeriodicidades as $periodicidad){
								if($periodicidad->codigo == $articulo->periodicidad){
									$horasPeriodicidad = intval($periodicidad->equivalencia);
									break;
								}
							}

							$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),date("Y-m-d", $fecIniAdmInicial),date("H:i", $fecIniAdmInicial),$horasPeriodicidad);
							
							$horaArranque = 8;
							echo "<input type='hidden' name='horaArranque' id='horaArranque' value='".$horaArranque."'>";
							
							$aplicaGraficaSuministro = true;

							$cont1 = 1;
							$cont2 = $horaArranque;   //Desplazamiento desde la hora inicial
							$caracterMarca = "*";
							$claseGrafica = "";

							$claseGrafica = "fondoVerde";

							while($cont1 <= 24){
								if(isset($arrAplicacion[$cont2]) && $arrAplicacion[$cont2] == $caracterMarca && $aplicaGraficaSuministro){
									echo "<td align='center' onMouseOver='mostrarTooltip(this, $cont2)'>";
									echo "<input name='dosisRonda$cont2' id='dosisRonda$cont2' onkeypress='return validarEntradaDecimal(event);' value='' size='2'>";
									echo "</td>";
								} else {
									echo "<td onMouseOver='mostrarTooltip(this, $cont2)'>&nbsp;</td>";
								}

								if($cont2 == 24){
									$cont2 = 0;
								}

								$cont1++;
								$cont2++;

								if($cont2 % 2 != 0){
									$cont2++;
								}
								if($cont1 % 2 != 0){
									$cont1++;
								}

								if($cont2 == $horaArranque){
									break;
								}
							}
							echo "</td>";
							echo "</tr>";
							echo "</table>";
							echo "</div>";
							
							echo "</td>";
							echo "</tr>";
							echo "</table>";
							echo "<br /><br />";
							echo "</div>";
						}
					}
							
					echo "<br>";
					echo "<table align='center' border='0' id='tbDetalleAddN'>";
	
					/////////////////////////////////////////
					// Encabezado articulos agregados
					echo "<tr align='center' class='encabezadoTabla' id='trEncabezadoTbAdd' style='display:none;'>";
					echo "<td>Acciones</td>";
					echo "<td>Medicamento<span class='obligatorio'>(*)</span></td>";
					echo "<td style='display:none'>Protocolo</td>";
					echo "<td>No enviar</td>";
					echo "<td>Dosis a aplicar<span class='obligatorio'>(*)</span></td>";
					echo "<td>Frecuencia<span class='obligatorio'>(*)</span></td>";
					echo "<td>Via<span class='obligatorio'>(*)</span></td>";
					echo "<td>Fecha y hora inicio<span class='obligatorio'>(*)</span></td>";
					echo "<td>Condici&oacute;n</td>";
					echo "<td>Cnf.</td>";
					echo "<td>Filtro<br>Antibi&oacute;ticos</td>";
					echo "<td>Dias tto.</td>";
					echo "<td>Dosis máx.</td>";
					echo "<td>Observaciones</td>";
					echo "</tr>";
					/////////////////////////////////////////

					echo "<tbody id='detKardexAddN'>";
					echo "</tbody>";

					echo "</table>";
	
					echo "<br>";
					echo "<div id='tabsMedicamentos' class='ui-tabs' style='display:'>";				//Inicio de lo que va a ir encerrado en las pestañas
					echo "<ul>";
					foreach( $arNomPes as $keyNom => $valueNom ){
						echo "<li><a href='#fragment-".str_replace( " ", "", $valueNom )."'><span>$valueNom</span></a></li>";
					}		
					echo "</ul>";
					
					if( $usuario->esEnfermeraRolHCE ){
						//Si no es médico
						$esDePiso = empty($esDeAyuda) || $esDeAyuda == 'off' ? true: false;
					}
					else{
						$esDePiso = !$paciente->esDeAyudaDx;
					}
					
					$colDetalleLQ = array();	//Guarda todos los articulos que son LEV
					foreach( $arNomPes as $keyNom => $valueNom ){
						foreach( $arTiposProtocolos[ $keyNom ] as $key => $value )
						{
						
							echo "<div id='fragment-".str_replace( " ", "", $valueNom )."'>";
							
							/****************************************************************************************
							 * Cómo es una pestaña de medicamentos se duplica todos lo datos iguales a la pestaña de medicamentos con cambio de protocolo
							 ****************************************************************************************/
							// echo "<pre>"; var_dump( $accionesPestana  ); echo "</pre>";
							if( $keyNom > 0 ){
								$accionesPestana2 = Array();
								foreach( $accionesPestana as $keyAcc => &$valueAcc ){

									if( substr($keyAcc, 0, 3) == "3.N" ){
										$accionesPestana2[ $indicePestana.".".$value.substr($keyAcc,3) ] = $valueAcc;
									}
									else{
										$accionesPestana2[ $indicePestana.".".substr($keyAcc,2) ] = $valueAcc;
									}
								}
							}
							else{
								$accionesPestana2 = $accionesPestana;
							}
							/************************************************************************************/
						
							//Se busca todos los articulos según el tipo de protocolo
							//Los LEV son articulo especiales y por tanto no se pueden buscar directamente
							//por tal motivo posteriormente se busca dichos articulos
							if( $value != 'LQ' ){
								realizarMovimientosArticulos($kardexActual, $paciente, $esFechaActual, $wfecha, $fechaGrabacion, $value, $elementosActuales, $colDetalle, $esDePiso );
								cargarLEVICAHistorial( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer );
							}
							
							consultarLEV( $colDetalleLQ, $colDetalle );
							$elementosActuales = count($colDetalle);
							
							//Acomodo las variables necesarias para poder mostrar los LEV
							if( $value == 'LQ' ){
								$colDetalle = $colDetalleLQ;
								$elementosActuales = count($colDetalle);
								
								vista_desplegarListaArticulosLEV( $conex, $wbasedato, $wbasedatohce, $wemp_pmla, $colDetalle, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $fechaGrabacion, $accionesPestana2,$indicePestana, $usuario, $value,$kardexActual->editable );
							}

							if( $value == 'N' && !in_array( "LC", $arTiposArticulos[$keyNom] ) ){
								consultarLactario( $colDetalleLTR, $colDetalle );
								$elementosActuales = count($colDetalle);
							}
							
							// if( $value == 'N' && !in_array( "U", $arTiposArticulos[$keyNom] ) ){
								
								// $pos = -1;
								// foreach( $arTiposProtocolos as $keyTiposProtocolos => $valueTiposProtocolos ){
									// if( in_array( "U", $valueTiposProtocolos ) ){
										// $pos = $keyTiposProtocolos;
									// }
								// }
								
								// consultarArticulosPestana( &$colDetalleLTR, &$colDetalle, $arTiposArticulos[ $pos ] );
								// $elementosActuales = count($colDetalle);
							// }
							
							// var_dump($colDetalle);
							if( $value != 'N' && in_array( "LC", $arTiposArticulos[$keyNom] ) ){
								
								if( count($colDetalleLTR) > 0 ){
								
									foreach( $colDetalleLTR as $keyLTR => &$valueLTR ){
										$valueLTR->tipoProtocolo = $value;
									}
								
									$colDetalle = array_merge( $colDetalle, $colDetalleLTR );
									$elementosActuales = count($colDetalle);
								}
							}
							
							vista_desplegarListaArticulos($colDetalle,$elementosActuales,$value,$kardexActual->editable,$colUnidades,array_merge( $colPeriodicidades, $colCondicionesSuministroInsulinas ),$colVias,$colCondicionesSuministro,$accionesPestana2,$indicePestana);
							
							$colArticulo = array_merge( $colArticulo, $colDetalle );
							// var_dump($colArticulo);
							// -------------------------------------------------------------------------
							//			 MEDICAMENTOS SIN CTC POR CAMBIO DE RESPONSABLE
							// -------------------------------------------------------------------------

							// Se define la cadena de medicamentos grabados sin ctc
							$cadenaGuardadosSinCTC="";
							if($strPendientesCTC != "")
							{
								$cadenaGuardadosSinCTC=$strPendientesCTC;
							}
							
							if( $elementosActuales == 0 ){
								$elementosActuales++;
							}
							
							switch($value)
							{
								case 'N':
									echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='".($elementosActuales*2)."'/>";
									break;
								case 'A':
									echo "<input type='HIDDEN' name='elementosAnalgesia' id=elementosAnalgesia value='".($elementosActuales*2)."'/>";
									break;
								case 'U':
									echo "<input type='HIDDEN' name='elementosNutricion' id=elementosNutricion value='".($elementosActuales*2)."'/>";
									break;
								case 'Q':
									echo "<input type='HIDDEN' name='elementosQuimioterapia' id=elementosQuimioterapia value='".($elementosActuales*2)."'/>";
									break;
								case 'LQ':
									echo "<input type='HIDDEN' name='elementosLev' id=elementosLev value='".($elementosActuales*2)."'/>";
									break;
								default:
									echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='".($elementosActuales*2)."'/>";
									break;
							}
							
							
							// //Detalle de medicamentos anteriores
							// $colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, $value);
							// $cantidadElementosAnteriores = count($colDetalleAnteriorKardex);
							
							// consultarLEV( &$colDetalleAnteriorLQ, &$colDetalleAnteriorKardex );
							// $cantidadElementosAnteriores = count($colDetalleAnteriorKardex);
							
							// if( $value == 'LQ' ){
								// $colDetalleAnteriorKardex = $colDetalleAnteriorLQ;
								// $cantidadElementosAnteriores = count($colDetalleAnteriorKardex);
							// }

							// if($cantidadElementosAnteriores > 0){
								// if( $value == 'LQ' ){
									// vista_desplegarListaArticulosLEVHistorial( $conex, $wbasedato, $wbasedatohce, $wemp_pmla, $colDetalleAnteriorKardex, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $fechaGrabacion, $accionesPestana2,$indicePestana, $usuario, $value,$kardexActual->editable );
								// }
								// else{
									// // $funcionOnclickAbrirNPT = consultarSiEsNutricionNPT($wbasedato,$articulo->historia,$articulo->ingreso,$articulo->consultarCodigoArticulo(),$articulo->idOriginal);
				
									// vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,$value,$colUnidades,array_merge( $colPeriodicidades, $colCondicionesSuministroInsulinas ),$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias, $wbasedato);
								// }
							// } else {
								// echo '<br><span class="subtituloPagina2" align="center">';
								// echo "No hay medicamentos anteriores";
								// echo "</span>";
								// echo "<div id='medAnt'>";
								// echo "</div>";
							// }
							
							
							
							
							$cantidadElementosAnteriores = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, $value);
							
							// consultarLEV( &$colDetalleAnteriorLQ, &$colDetalleAnteriorKardex );
							// $cantidadElementosAnteriores = count($colDetalleAnteriorKardex);
							
							// if( $value == 'LQ' ){
								// $colDetalleAnteriorKardex = $colDetalleAnteriorLQ;
								// $cantidadElementosAnteriores = count($colDetalleAnteriorKardex);
							// }

							if($cantidadElementosAnteriores > 0){
								if( $value == 'LQ' ){
									//Muestra el detalle de medicamentos del kardex consultado
									echo '<span class="subtituloPagina2" align="center" style="cursor:pointer;" onclick="ver_articulos_anteriores( this, \''.$value.'\');">';
									echo "<img style='display:none' src='../../images/medical/ajax-loader5.gif'\>Detalle de articulos anteriores (Ver)";
									echo "</span><br><br>";
									echo "<div id='lista_articulos_anterioresLQ' style='display:none'>";
									echo "</div>";
								}
								else{
									//Muestra el detalle de medicamentos del kardex consultado
									echo '<span class="subtituloPagina2" align="center" style="cursor:pointer;" onclick="ver_articulos_anteriores( this, \''.$value.'\');">';
									echo "<img style='display:none' src='../../images/medical/ajax-loader5.gif'\>Detalle de articulos anteriores (Ver)";
									echo "</span><br><br>";
									echo "<div id='lista_articulos_anteriores$value' style='display:none;'>";
									echo "</div>";
								}
							}
							else {
								echo '<br><span class="subtituloPagina2" align="center">';
								echo "No hay medicamentos anteriores";
								echo "</span>";
								echo "<div id='medAnt'>";
								echo "</div>";
							}
							
							echo "</div>";	//fin div de pestaña configurada de medicamentos
						}
					}
					
					echo "</div>"; //Fin div id de tabsMedicamentos
					
					if($esPacEPS==true && $cadenaGuardadosSinCTC!="")
					{
						echo "<INPUT TYPE='hidden' name='cadenaGuardadosSinCTCO' id='cadenaGuardadosSinCTCO' value='".$cadenaGuardadosSinCTC."'>";
					}
						

					/**************************************************************************************************************
					 * Junio 19 de 2012
					 *
					 * Si no hay articulos para mostrar y hay medicamentos el día anterior que se estan activos, doy la opcion 
					 * de traer medicamentos del día anterior
					 **************************************************************************************************************/
					// if( $kardexActual->editable ){
					
						// if( empty( $colDetalle ) ){
						
							// $conMedicamentos = tieneMedicamentosActivos( $conex, $wbasedato, $wcenmez, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $usuario->centroCostosGrabacion, date( "Y-m-d", strtotime( "$fechaGrabacion 00:00:00" ) - 24*3600 ) );
							
							// if( $conMedicamentos ){
								// echo "<table align='center' id='tbCargarMedicamentosAnteriores'>";
								// echo "<tr><td>";
								// echo "<br><INPUT type='button' onClick='cargarMedicamentosAnteriores($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,\"".date( "Y-m-d", strtotime( "$fechaGrabacion 00:00:00" ) - 24*3600 )."\",\"$usuario->centroCostosGrabacion\");' value='Medicamentos del d&iacute;a anterior'>";
								// echo "<tr><td>";
								// echo "</table>";
							// }
						// }
					// }
					/**************************************************************************************************************/
					
					if( $imprimeMedicamentos == 'on' and $editable != 'off'){
					
					$qind = " SELECT Karegr
								FROM ".$wbasedato."_000053
							   WHERE Karhis = '".$paciente->historiaClinica."'
								 AND Karing = '".$paciente->ingresoHistoriaClinica."'
							   ORDER BY Fecha_data DESC ";
					$resind = mysql_query($qind, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qind . " - " . mysql_error());
					$rowind = mysql_fetch_array($resind);				
										
					//echo "<textarea name='windicaciones' id='windicaciones' style='display:none;' cols='100' rows='7'>".$rowind['Karegr']."</textarea>";
						
					echo "<br /><div align='center'><input type=button value='Enviar a Centro de Impresi&oacute;n' onclick='grabarKardex(\"cenimpexa\")'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=button value='Imprimir' onclick='grabarKardex(\"impexa\")'></div>";
					echo "<div id='linkcenimpmed' style='display:none;'></div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					
					}

					// //Detalle de medicamentos anteriores
					// $colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, "N");
					// $cantidadElementosAnteriores = count($colDetalleAnteriorKardex);

					// if($cantidadElementosAnteriores > 0){
						// vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,"N",$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias);
					// } else {
						// echo '<br><span class="subtituloPagina2" align="center">';
						// echo "No hay medicamentos anteriores";
						// echo "</span>";
						// echo "<div id='medAnt'>";
						// echo "</div>";
					// }
					echo "</div>";
				}
			
		}
			if( $kardexActual->editable )
				cargarProcedimientosDetalleATemporal( $conex, $wbasedatohce, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha );
			
			$indicePestana = "4";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-4'>";
				
				$TiposOrdenesAgrupadas = consultarTiposOrdenesAgrupadas();
				
				?>
				<script>
					TiposOrdenesAgrupadas = <?php echo json_encode( $TiposOrdenesAgrupadas ); ?>
				</script>
				<?php

				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";
				
				if($usuario->firmaElectronicamente && $kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					
					$optionsSelTipoServicio = "<option value='%' selected>Todos</option>";
					
					// $tiposDeAyudaDxs = tiposAyudasDiagnosticas( $conex, $wempresa );
					$tiposDeAyudaDxs = tiposAyudasDiagnosticas( $conex, $wempresa, $paciente->servicioActual );
					
					$tiposDeAyudaDxs = explode( "|", $tiposDeAyudaDxs );
					
					foreach( $tiposDeAyudaDxs as $key => $value ){
						 list( $codigo, $descripcion ) = explode( "-", $value );
						 $optionsSelTipoServicio .= "<option value='$codigo'>$descripcion</option>";
					}
					
					$listaProtocolos = generarListaProtocolos('wprotocolo_ayd',$usuario->codigo,$paciente->servicioActual,'Procedimientos');
					echo "<div align='center'>";
					echo "<div class='fondoAmarillo' style='border: 1px solid #333333; width:100% !important; width:77%; height:70px;'>";
					echo "<table align='left' border='0' width='100%'>";
					echo "<tr class='fondoAmarillo'>";
					
					echo "<td width='35%' align='left' style='border-right:1px solid #333;'>";
						echo "<table align='left' height='100%'>";
						echo "<tr>";
						echo "<td colspan=2 align=center class='encabezadoTabla'>";
						echo "<b>Protocolos</b>";
						echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align='left' valign='bottom' style='font-size:10pt'>";
						echo $listaProtocolos;
						echo "</td>";
						echo "<td>";
						echo " &nbsp; <input type='button' name='btnImport' value='Importar protocolo' onclick='eleccionMedicamento(1)'>";
						echo "</td>";
						echo "</tr>";
						echo "</table>";
					echo "</td>";

					//echo "<td width='10%' style='border-left:2px solid #333;'>&nbsp;</td>";

					echo "<td width='20%'>";
						echo "<table align='right' height='100%'>";
						echo "<tr>";
						echo "<td align=center class='encabezadoTabla'>";
						echo "<b>Tipo de orden</b>";
						echo "</td>";
						echo "</tr>";
						echo "<tr height='41'>";
						echo "<td> &nbsp; ";
						// crearCampo("6","wselTipoServicio",@$accionesPestana[$indicePestana.".1"],array("class"=>"textoNormal", "onChange"=>"autocompletarParaConsultaDiagnosticas();"),$optionsSelTipoServicio);
						// crearCampo("6","wselTipoServicio",@$accionesPestana[$indicePestana.".1"],array("class"=>"textoNormal", "onChange"=>"autocompletarParaConsultaDiagnosticas();"),$optionsSelTipoServicio);
						crearCampo("6","wselTipoServicio",@$accionesPestana[$indicePestana.".1"],array("class"=>"textoNormal", "onChange"=>"validarTipoOrdenAgrupada();"),$optionsSelTipoServicio);
						echo " &nbsp; </td>";
						echo "</tr>";
						echo "</table>";
					echo "</td>";

					echo "<td width='45%'>";
						echo "<table align='center' height='100%'>";
						echo "<tr>";
						echo "<td colspan=3 align=center  class='encabezadoTabla'>";
						echo "<b>Ayuda o procedimiento</b>";
						echo "</td>";
						echo "</tr>";
						echo "<tr height='41'>";
						echo "<td></td>";
						echo "<td valign='top'> ";
						crearCampo("1","wnomproc",@$accionesPestana[$indicePestana.".1"],array("size"=>"60","class"=>"textoNormal"),"");
						
						if($lenguajeAmericas == "on"){
							echo "<td valign='top'> &nbsp; <img id='imgAddExam' src='../../images/medical/hce/add_blue.png' width='14' height='14' border='0' onclick='agregarNuevoExamen();'> &nbsp; </td>";
						}
						
	//					echo "<INPUT TYPE='text' NAME='wnomproc' id='wnomproc' SIZE=60 class='textoNormal' onBlur='this.value=\"\"'>";
						//echo "&nbsp;|&nbsp;";
						//echo "<input type='button' value='Ordenar...' onclick='movimientoExamenes();'>";
						echo " </td>";
						echo "</tr>";
						echo "</table>";
					echo "</td>";
					
					echo "</tr>";

					echo "</table>";
					echo "</div>";
					echo "</div>";
					
//					echo "<div align='center'><input type='button' onClick='movimientoExamenes();' value='Ordenar...'></div>";
					echo "<br/>";
				}
				
				//Examenes de historia clinica
				$datosAdicionales = Array();
				$colExamenesHistoria = consultarOrdenesHCE($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$datosAdicionales, "", $usuario->centroCostos );
				$cuentaExamenesHistoria = count($colExamenesHistoria);
				$contExamenes = 0;
				$contProtocolosExamenes = 0;
				
				$contOrdenes = 0;
				
				$imprimirUrl = false;
				$url = true;
				
				$verAnt = "";
				
				foreach( $datosAdicionales as $keyCco => $valueCco ){
					
					foreach( $valueCco as $keyNroOrden => $valueNroOrden ){
						
						if( @$valueNroOrden['Anteriores'] > 0 ){
							$imprimirUrl = true;
							
							// Se agrega wcco=$paciente->servicioActual requerido como parámetro en el script	// 2012-06-26
							$url = $valueNroOrden['Programa']."?wemp_pmla=$wemp_pmla&wcco=$paciente->servicioActual&whis=$paciente->historiaClinica&wing=$paciente->ingresoHistoriaClinica";
						}
					}
				}
				
				// 2012-07-03
				// Se comenta porque ya se van amostrar las ordenes realizadas en formato de pestañas
				/*
				if( $imprimirUrl ){
					$verAnt = "<font size='5'><a onClick=\"abrirVentanaVerAnteriores( '$url' );\" style='cursor:hand' onMouseOver='this.style.color=\"blue\";' onMouseOut='this.style.color=\"black\";'>Ver ordenes realizadas</a></font>";
				}
				*/
				
				echo '<div class="subtituloPagina2" style="overflow:visible;">';
				
				// 2012-07-03
				// Se comenta porque ya se van a mostrar las ordenes realizadas en formato de pestañas
				/*
				echo "<table align=center width='100%'>";
				echo "<tr>";
				echo "<td colspan='3'>";
				*/
				echo "<div id='tabs2' class='ui-tabs'>";				//Inicio de lo que va a ir encerrado en las pestañas
				echo "<ul>";
				echo "<li><a href='#fragment-pendientes'><span>Ordenes del día y pendientes</span></a></li>";
				echo "<li><a href='#fragment-realizadas'><span>Ordenes realizadas<span id='pvr'></span><span id='pvc'></span></span></a></li>";
				echo "</ul>";
				
				// 2012-07-03
				// Se comenta porque ya se van a mostrar las ordenes realizadas en formato de pestañas
				/*
				echo "</td>"; 
				
				echo "<td style='width:30%' align=right>$verAnt</td>";
				
				echo "</tr>";
				echo "</table>";
				
				echo "<br>";
				*/


				// 2012-07-03
				/********************************************************
				 ********************************************************
				 ** Inicio contenedor de pestaña de ordenes pendientes **
				 ********************************************************
				 ********************************************************/

				 echo "<div id='fragment-pendientes'>";

				/***************************
				 * Movimiento de examenes
				 ***************************/
				if($kardexActual->editable){
					echo "<div id='movExamenes' style='position:absolute;display:none;z-index:200;width:450px;height:360px;left:21px;top:10px;padding:5px;background:#FFFFFF;border:2px solid #2266AA'>";
					echo "<table>";

					echo "<tr>";
					echo "<td colspan=4 align=center class='encabezadoTabla'>";
					echo "<b>Buscador de ayudas diagnosticas</b>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";

					echo "<td class='fila1' nowrap>Unidad que realiza</td>";
					echo "<td class='fila2'>";
						
					echo "<select id='wservexamen' name='wservexamen' class='seleccionNormal' onChange='consultarServicioExamen();'>";

					echo "<option value=''>Seleccione</option>";
					foreach ($colServiciosExamenes as $servicio){
						echo "<option value='".$servicio->codigo."|$servicio->consecutivoOrden'>$servicio->codigo - $servicio->nombre</option>";
					}

					echo "</select>";
					echo "</td>";

					echo "</tr>";

					echo "<tr>";
					echo "<td class='fila1'>Descripcion</td>";
					echo "<td class='fila2'>";
					echo "<INPUT TYPE='text' NAME='wnomayu' id='wnomayu' SIZE=20 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarAyudasDiagnosticas();\");'>";
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td colspan=4 align=center class='fila2'>";
					echo "<b>&nbsp;</b>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 align=center class='fila1'>";
					echo "<b>Consecutivo de orden para el servicio: &nbsp;<span id='wconsserv'></span></b>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 align=center class='fila2'>";
					echo "<b>&nbsp;</b>";
					echo "</td>";
					echo "</tr>";
					
					echo "<tr><td colspan=4 align=center>";
					echo "<input type='button' value='Consultar' onclick='consultarAyudasDiagnosticas();'>&nbsp;|&nbsp;<input type='button' value='Cerrar' onclick='return movExamenes.hide();'>";
					echo "</td></tr>";

					echo "<tr>";
					echo "<td colspan=4 class='fila2'>";
					echo "<img id='imgCodMed' style='display:none' src='../../images/medical/ajax-loader5.gif'>";
					echo "<div id='cntExamenes' style='overflow-y: scroll; width: 100%; height: 160px;'>";
					echo "</div>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 class='fila2'>";
					echo "<span><b>NOTA: </b>Realice su búsqueda específica, este buscador retornará hasta cien resultados</span>";
					echo "</td>";
					echo "</tr>";

					echo "</table>";
					echo "</div>";
				}

				/*Accion de actualizacion de observaciones
				 * Accion de cancelación de orden
				 */
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.2' id='wacc$indicePestana.2' value='".accionesATexto(@$accionesPestana[$indicePestana.".2"])."'>";//Grabar observaciones				
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.3' id='wacc$indicePestana.3' value='".accionesATexto(@$accionesPestana[$indicePestana.".3"])."'>";//Cancelar orden
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.4' id='wacc$indicePestana.4' value='".accionesATexto(@$accionesPestana[$indicePestana.".4"])."'>";//Campo observaciones orden

				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.5' id='wacc$indicePestana.5' value='".accionesATexto(@$accionesPestana[$indicePestana.".5"])."'>";//Eliminar ayuda o procedimiento
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.6' id='wacc$indicePestana.6' value='".accionesATexto(@$accionesPestana[$indicePestana.".6"])."'>";//Justificacion
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.7' id='wacc$indicePestana.7' value='".accionesATexto(@$accionesPestana[$indicePestana.".7"])."'>";//Resultado				
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.8' id='wacc$indicePestana.8' value='".accionesATexto(@$accionesPestana[$indicePestana.".8"])."'>";//Fecha realizacion
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.9' id='wacc$indicePestana.9' value='".accionesATexto(@$accionesPestana[$indicePestana.".9"])."'>";//Estado de ayuda o procedimiento
				
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.10' id='wacc$indicePestana.10' value='".accionesATexto(@$accionesPestana[$indicePestana.".10"])."'>";//Grabacion automatica
				
				
				/*Agrupacion por centros de costos y ordenes
				 * Si no existen ordenes pendientes se ubica el contenedor solo
				 */				
				if($cuentaExamenesHistoria == 0){
					echo "<div id='cntOtrosExamenes'>";   //Contenedor de agrupacion de examenes por centros de costos	
				} else {
					echo "<div id='cntOtrosExamenes'>";
				}			
						
				
				$whis = $paciente->historiaClinica;
				$wing = $paciente->ingresoHistoriaClinica;
				
				echo "<center>";
				mostrar_examenes_kardex($conex, $wbasedato, $whis, $wing, $wuser);
				echo "</center>";
				
				
				echo "	<table align='right' style='border: 1px solid black;border-radius: 5px;'>
							<tr>
							<td align='center' style='font-size:8pt'><b>Convenciones</b></td>
							</tr>
							<tr>
							".pintarConvencionProcAgrupados()."
							<td><span style='background-color:#3CB648;border-radius:3px;font-size:7pt;vertical-align:top;'>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style='font-size:7pt;vertical-align:top;'>&nbsp;Nuevo o modificado&nbsp;&nbsp;</span></td>
							</tr>
						</table>
						<br><br><br>";
				
				///////////////////////////////////////////////////////////////////
				// 2012-07-10
				// Encabezado de búsqueda rápida de ordenes
				echo "<center>";
				echo "<table>";
				echo "<tr>";
				echo "<td colspan='6' class='fila2' align=center><b>Filtro para la b&uacute;squeda de procedimientos</b></td>";				
				echo "</tr>";
				echo "<tr>";
				// Buscar por cualquier criterio.
				echo "<td valign='middle' colspan=6><input class='textoNormal' type='text' placeholder='Buscar' name='wprocedimiento2' id='wprocedimiento2' value='".$wprocedimiento2."' onkeypress='return pulsar(event);' size='100'></td>";		
				echo "</tr>";
				echo "</table>";
				echo "</center>";
				echo "<center><table id='examPendientes' style='width: 1500px;'>";				
				echo "<tr class='encabezadoTabla'>";
				echo "<td align='center'><b>Orden</b></td>";
				if($editable != 'off' && $vecPestanaGrabacion[$indicePestana] ){
					echo "<td align='center'><b>Imprimir<br><input type=checkbox onclick='marcar_todos()' id='marcar_all' title='Marcar Todos'></b></td>";
				//echo "<td align='center'><b>Eliminar</b></td>";
				}
				echo "<td align='center'><b>Fecha a<br>realizar</b></td>";				
				echo "<td align='center'><b>Tipo de orden</b></td>";
				echo "<td align='center'><b>Procedimiento</b></td>";
				echo "<td align='center'><b>Justificación</b></td>";				
				echo "<td align='center'><b>Estado</b></td>";
				echo "<td align='center'><b>Muestra<br>tomada por</b></td>";
				echo "<td align='center' nowrap><b>Bitacora de Gestiones</b></td>";
				echo "</tr>";

				echo "<tbody id='encabezadoExamenes'>";
				echo "</tbody>";

				//echo "</table>";
				///////////////////////////////////////////////////////////////////

				//AQUI
				// pintarTrProcedimientosAgrupados($whis,$wing,$accionesPestana,$indicePestana,$kardexActual->editable);
				pintarTrProcedimientosAgrupados($whis,$wing,$accionesPestana,$indicePestana,$kardexActual->editable,$paciente->servicioActual,$colArticulo);
				
				
				$centroCostosExamenes = "";
				$consecutivoOrdenExamen = "";

				$caso = "";
				$pasoPorAqui = false;
				$huboOrdenes = false;
				foreach ($colExamenesHistoria as $examen){
//					
					if( $centroCostosExamenes != $examen->tipoDeOrden ){
						$caso = "1";
					}
					if($centroCostosExamenes == $examen->tipoDeOrden && $consecutivoOrdenExamen == $examen->numeroDeOrden){
						$caso = "4";
					}
					if($centroCostosExamenes == $examen->tipoDeOrden && $consecutivoOrdenExamen != $examen->numeroDeOrden){
						$caso = "3";
					}
					
					/************************************************************************************************************
					 * Queda así para el switch:
					 * - Opcion 1: Crea el encabezado del Servicio
					 * - Opcion 2: Crea la orden
					 * - Cualquier otra opción no hace nada
					 ************************************************************************************************************/
					//Inicio switch
					switch ($caso){
						
						case '1':
							
							$contOrdenes = 0;
							
							if($contExamenes != 0){
							
								if( $huboOrdenes ){
									//Cierre de orden
									echo "</tr>";
								
								}			
								
								//Cierro centro de costos							
								echo "</tbody>";
								
							}
							$pasoPorAqui = false;
							$huboOrdenes = false;
							
							echo "<div id='".$examen->tipoDeOrden."' style='display:block'>";

							//Crear tabla
							if( true ){
								
								echo "<tbody id='detExamenes".$examen->tipoDeOrden."' class='find'>";

								$pasoPorAqui = false;
							}
							
							
						case '2':
						case '3':
							$fechaHoy = date("Y-m-d");
							
							//Verifica si el estado del examen se puede mostrar.
							$westado_ordenes = estados_ordenes($wemp_pmla, $wbasedato, $conex, $examen->estadoExamen );
							
							if( $westado_ordenes == 'on' || $examen->fechaARealizar == $fechaHoy ){
								
								//Cierre de tabla
								if( $caso != 1 ){
									
									if( $pasoPorAqui ){
										echo "</tbody>";
										//echo "</table>";
									}

									//Cierre de orden
									if($huboOrdenes){
										
										echo "</tr>";
									}
								}
								
								$huboOrdenes = true;
								//Crear orden
								
								// if($tdNumeroOrden != "")
								// {
									// echo "td ---- 1 ";
								// }
								// $rowspanNumOrden = 0;
								$ocultar = "";
								if(isset($arrayParaCrearJsAgrupados))
								{
									$clavesArrayAgrupados = array_keys($arrayParaCrearJsAgrupados);
								
									for($c=0;$c<count($clavesArrayAgrupados);$c++)
									{
										$clave = explode("-",$clavesArrayAgrupados[$c]);
										$buscar = $clave[0]."-".$clave[1];
										$clavesArrayAgrupados[$c]=$clave[0]."-".$clave[1];
									}
									
									$cantOrdenes = array_count_values ( $clavesArrayAgrupados );
								
									
									if (array_key_exists($examen->tipoDeOrden."-".$examen->numeroDeOrden."-".$examen->nroItem, $arrayParaCrearJsAgrupados)) 
									{
										$ocultar = "none";
										
										$rowspanNumOrden = $datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes'] - $cantOrdenes[$examen->tipoDeOrden."-".$examen->numeroDeOrden];
										
										if($datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']>1 && $rowspanNumOrden>0)
										{
											// if($tdNumeroOrden != "")
											// {
												// echo "td ---- 1 ";
												// $tdNumeroOrden = "";
											// }
											// else
											// {
												// $tdNumeroOrden = "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='".$rowspanNumOrden."' class=fila1 nowrap>&nbsp;&nbsp;&nbsp;
												 
												// <a href='#null' onclick='intercalarElemento(\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\");'>
												// <b>&nbsp;&nbsp;&nbsp;<u>Orden Nro. <br>".$examen->numeroDeOrden."</u></b></a>
												// <div id=\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\" class='fila2'>
												// <div style='display:none'>
												// <br>Observaciones actuales: <br>
												// <textarea rows='4' cols='80' readonly>$examen->observacionesOrden</textarea>						
												// </div>
												// </div>
												// </td>"; 
											// }
											
											$pintado=0;
											$tdNumeroOrden = "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='".$rowspanNumOrden."' class=fila1 nowrap>&nbsp;&nbsp;&nbsp;
											 
											<a href='#null' onclick='intercalarElemento(\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\");'>
											<b>&nbsp;&nbsp;&nbsp;<u>Orden Nro. <br>".$examen->numeroDeOrden."</u></b></a>
											<div id=\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\" class='fila2'>
											<div style='display:none'>
											<br>Observaciones actuales: <br>
											<textarea rows='4' cols='80' readonly>$examen->observacionesOrden</textarea>						
											</div>
											</div>
											</td>"; 
											
										}
									}
								}
									
								
								if($contExamenes % 2 == 0){
									echo "<tr align=center id='trEx$contExamenes' class=fila1 style='display:".$ocultar."'>";
								} else {
									echo "<tr align=center id='trEx$contExamenes' class=fila2 style='display:".$ocultar."'>";
								}
								// var_dump($cantOrdenes);
								// var_dump("------------");
								
								if(isset($cantOrdenes[$examen->tipoDeOrden."-".$examen->numeroDeOrden]))
								{
									$rowspanNumOrden = $datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes'] - $cantOrdenes[$examen->tipoDeOrden."-".$examen->numeroDeOrden];
									// var_dump("------------+".$rowspanNumOrden);
									if($rowspanNumOrden>0)
									{
										if($contOrdenes % 2 == 0){
										echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='".$rowspanNumOrden."' class=fila1 nowrap>&nbsp;&nbsp;&nbsp;";
										}
										else{
											echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='".$rowspanNumOrden."' class=fila2 nowrap>&nbsp;&nbsp;&nbsp;";
										}
									}
									else
									{
										if($contOrdenes % 2 == 0){
										echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila1 nowrap>&nbsp;&nbsp;&nbsp;";
										}
										else{
											echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila2 nowrap>&nbsp;&nbsp;&nbsp;";
										}
									}
								}
								else
								{
									if($contOrdenes % 2 == 0){
									echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila1 nowrap>&nbsp;&nbsp;&nbsp;";
									}
									else{
										echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila2 nowrap>&nbsp;&nbsp;&nbsp;";
									}
								}
								
								
								
								// $rowspanNumOrden = $datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes'] - $cantOrdenes[$examen->tipoDeOrden."-".$examen->numeroDeOrden];
								// var_dump("------------+".$rowspanNumOrden);
								// if($rowspanNumOrden>0)
								// {
									// if($contOrdenes % 2 == 0){
									// echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='".$rowspanNumOrden."' class=fila1 nowrap>&nbsp;&nbsp;&nbsp;";
									// }
									// else{
										// echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='".$rowspanNumOrden."' class=fila2 nowrap>&nbsp;&nbsp;&nbsp;";
									// }
								// }
								// else
								// {
									// if($contOrdenes % 2 == 0){
									// echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila1 nowrap>&nbsp;&nbsp;&nbsp;";
									// }
									// else{
										// echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila2 nowrap>&nbsp;&nbsp;&nbsp;";
									// }
								// }
								// // if($contOrdenes % 2 == 0){
									// // echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila1 nowrap>&nbsp;&nbsp;&nbsp;";
								// // }
								// // else{
									// // echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila2 nowrap>&nbsp;&nbsp;&nbsp;";
								// // }
								$contOrdenes++;
								
								if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
									
									$puedeEliminar = true;									
									if( $usuario->permisosPestanas[$indicePestana]['modifica'] ){
										if( $usuario->codigo != $examen->creadorOrden ){
											$puedeEliminar = false;
										}
									}
									
									// if( $puedeEliminar ){
										// crearCampo("4","",@$accionesPestana[$indicePestana.".3"],array("onClick"=>"cancelarOrden('$examen->tipoDeOrden','$examen->numeroDeOrden');"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' />");
									// }
								}
								
								echo "<a href='#null' onclick='intercalarElemento(\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\");' title='".( !empty( $examen->anexadaAOrden ) ? '<center>Orden anexada a<br>'.$examen->anexadaAOrden."</center>" : '' )."' class='msg_tooltip'>";
								echo "<b>&nbsp;&nbsp;&nbsp;<u>Orden Nro. <br>".$examen->numeroDeOrden."</u></b></a>";
								
								if( $examen->anexarOrden ){
									echo "<div><a href='#null' class='anexar-orden' onclick='anexarOrden( this, \"".$examen->tipoDeOrden."\",\"".$examen->numeroDeOrden."\");'><span style='color:red;'>Anexar</span></a></div>";
								}
								
								if( !empty( $examen->url ) ){
									
									$classBlink = '';
									$attrPLE = '';
									if( $examen->pendienteLecturaMedico ){
										$classBlink = 'blink';
										$attrPLE 	= "ple='".$examen->tipoDeOrden."-".$examen->numeroDeOrden."'";
									}
									
									// echo "<div><a $attrPLE href='#null' class='blink-".$examen->tipoDeOrden."-".$examen->numeroDeOrden." ver-orden $classBlink' onClick='abrirVentanaVerAnteriores(\"".$examen->url."\", \"".$examen->tipoDeOrden."\",\"".$examen->numeroDeOrden."\")'><span style='color:green;'>Ver resultado</span></a></div>";
								}
								
								echo "<div id=\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\" class='fila2'>";
								echo "<div style='display:none'>";
								echo "<br>Observaciones actuales: <br>";
								echo "<textarea rows='4' cols='80' readonly>$examen->observacionesOrden</textarea>";								
								echo "</div>";
								echo "</div>";
								echo "</td>";
								
								$consecutivoOrdenExamen = $examen->numeroDeOrden;
							}

						break;
						
						case '4': break;

					}//Fin switch
					
					//Verifica si el estado del examen se puede mostrar.
					$westado_ordenes = estados_ordenes($wemp_pmla, $wbasedato, $conex, $examen->estadoExamen );	
					
					//Crear tabla
					if( false && ($caso == 1 || $caso == 3) && $westado_ordenes == 'on' ){
						
						//echo "<table align='center'>";
						echo "<tr align='center'>";
	
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							echo "<td class='encabezadoTabla'>";
							echo "Acciones";
							echo "</td>";
						}
						
						echo "<tbody id='detExamenes".$examen->tipoDeOrden."' class='find'>";
						
						$pasoPorAqui = true;
					}
					
					//Verifica si el estado del examen se puede mostrar.
					$westado_ordenes = estados_ordenes($wemp_pmla, $wbasedato, $conex, $examen->estadoExamen );							
					
					if( false || $westado_ordenes == 'on'){
						
						// -------------------------------------------------------------------------
						//			 		EXAMENES SIN CTC POR CAMBIO DE RESPONSABLE
						// -------------------------------------------------------------------------

						// Se define la cadena de examenes grabados sin ctc

						$ExamenNoPos="";
						if($esPacEPS==true)
						{
							$ExamenNoPos=consultarExamenNoPos($conex,$examen->codigoExamen,$examen->tipoDeOrden);
							$conCTC = false;

							if( $ExamenNoPos == true )
							{
								$conCTC = procTieneCTC( $conex, $wbasedato, $examen->historia, $examen->ingreso, $examen->tipoDeOrden, $examen->numeroDeOrden, $examen->nroItem);
								
								// $responsableEmpresa = explode("-",$paciente->numeroIdentificacionResponsable); //responsable
								$responsableEmpresa = consultarNitResponsable($wemp_pmla,$paciente->numeroIdentificacionResponsable);
								
								$responsableEmpresasConfirman = explode(",",$wentidades_confirmanCTC);
								
								$responsableConCtcNoObligatorio = false;
								for($b=0;$b < count($responsableEmpresasConfirman);$b++)
								{
									if($responsableEmpresa == $responsableEmpresasConfirman[$b])
									{
										$responsableConCtcNoObligatorio = true;
										break;
									}
								}
								
								//Procedimiento no tiene CTC
								// if(!$conCTC)
								if(!$conCTC && $responsableConCtcNoObligatorio == false)
								{
									$pedirCTC = pedirCTCExamenesGrabados( $conex, $wbasedato, $examen->tipoDeOrden, $examen->numeroDeOrden, $examen->nroItem,$usuario->codigo);
									
									if($pedirCTC)
									{
										$strExamenesPendientesCTC .= $examen->codigoExamen.','.$contExamenes.','.$examen->tipoDeOrden.','.$examen->numeroDeOrden.','.$examen->nroItem.";";
									}
								}
							}
						}
						
						
						$ocultar = "";
						if(isset($arrayParaCrearJsAgrupados))
						{
							$existeProc = array_key_exists($examen->tipoDeOrden."-".$examen->numeroDeOrden."-".$examen->nroItem, $arrayParaCrearJsAgrupados);
						
							if ($existeProc) 
							{
								$ocultar = "none";
							}
						}
						
						if( !($caso == 1 || $caso == 3) ){
							
							//Examen
							if($contExamenes % 2 == 0){
								echo "<tr id='trEx$contExamenes' class='fila1' align='center' style='display:".$ocultar."'>";
							} else {
								echo "<tr id='trEx$contExamenes' class='fila2' align='center' style='display:".$ocultar."'>";
							}
						}
						
						if(!$existeProc)
						{
							if ($pintado==0) 
							{
								echo $tdNumeroOrden;
								$pintado++;
							}
						}
	
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							
							$puedeEliminar = true;									
							if( $usuario->permisosPestanas[$indicePestana]['modifica'] ){
								if( $usuario->codigo != $examen->creadorItem ){
									$puedeEliminar = false;
								}
							}
							
							if($examen->imprimirExamen!='on')
							{
								$claseImagen = 'opacar';
								$claseAlterna = 'aclarar';
							}
							else
							{
								$claseImagen = 'aclarar';
								$claseAlterna = 'opacar';
							}
							
							echo "<td>";
							
							$chkImprimir = "";
							if( $examen->imprimirExamen == 'on' ){
								$chkImprimir = "checked";
							}
														
							echo "<div id='imgImprimir".$contExamenes."' style='display:inline'><img width='18' height='18' src='../../images/medical/hce/icono_imprimir.png' border='0'/><br /><input type='checkbox' id='imprimir_examen' name='imprimir_examen' $chkImprimir onClick='marcarImpresionExamen(this,\"".$examen->tipoDeOrden."\",\"".$examen->numeroDeOrden."\",\"".$examen->codigoExamen."\",\"".$examen->fechaARealizar."\",\"".$examen->nroItem."\");' /></div>";
							echo "</td>";
							
							// echo "<td>";
							// if( $puedeEliminar ){
								// crearCampo("4","",@$accionesPestana[$indicePestana.".5"],array("onClick"=>"quitarExamen('$contExamenes','','off');"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' />");
							// }
							// echo "</td>";
						}
						
						$permiteCambiarFecha = $examen->permiteModificarFecha( $conex, $wbasedato );
	
						//Fecha de solicitado examen
						echo "<td>";
						if( $kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							
							if( $permiteCambiarFecha ){
								//echo "<INPUT TYPE='text' id='wfsol$contExamenes' NAME='wfsol$contExamenes' cod_examen='$examen->codigoExamen' SIZE=10 readonly class='campo2 calendariofecha' value='$examen->fechaARealizar' onChange='marcarCambio(\"$indicePestana\",\"$contExamenes\");'>";
								crearCampo("1","wfsol$contExamenes",@$accionesPestana[$indicePestana.".8"],array("SIZE"=>"10","onChange"=>"marcarCambio('$indicePestana','$contExamenes');","cod_examen"=>"$examen->codigoExamen","readonly"=>"readonly","class"=>"campo2 calendariofecha"),"$examen->fechaARealizar");
							}
							else{
								crearCampo("1","wfsol$contExamenes",@$accionesPestana[$indicePestana.".8"],array("SIZE"=>"10","onChange"=>"marcarCambio('$indicePestana','$contExamenes');","cod_examen"=>"$examen->codigoExamen","readonly"=>"readonly","class"=>"campo2", 'style'=>'border: 0px;background-color:transparent;font-size:10pt;text-align:center;'),"$examen->fechaARealizar");
							}
		
						} else {
							echo "$examen->fechaARealizar&nbsp;";
						}
						
						if( !empty( $examen->horaCita ) && $examen->horaCita != '00:00:00' ){
							echo "<span style='font-weight:bold;'>".$examen->horaCita."</span>";
						}
						
						if( !empty( $examen->urlImagenPorEstudio ) ){
									
							$classBlink = '';
							$attrPLE = '';
							if( $examen->pendienteLecturaEstudioCancelado ){
								$classBlink = 'blink';
								$attrPLE 	= "ple='".$examen->tipoDeOrden."-".$examen->numeroDeOrden."-".$examen->nroItem."'";
							}
							
							// echo "<div><a $attrPLE href='#null' class='blink-".$examen->tipoDeOrden."-".$examen->numeroDeOrden." ver-orden $classBlink' onClick='marcarLeidoEstudioCancelado( this, \"".$examen->urlImagenPorEstudio."\" );'><span style='color:green;'>Ver imagen</span></a></div>";
						}
						
						if( !empty( $examen->urlReportePorEstudio ) ){
									
							$classBlink = '';
							$attrPLE = '';
							if( $examen->pendienteLecturaEstudioCancelado ){
								$classBlink = 'blink';
								$attrPLE 	= "ple='".$examen->tipoDeOrden."-".$examen->numeroDeOrden."-".$examen->nroItem."'";
							}
							
							// echo "<div><a $attrPLE href='#null' class='blink-".$examen->tipoDeOrden."-".$examen->numeroDeOrden." ver-orden $classBlink' onClick='marcarLeidoEstudioCancelado( this, \"".$examen->urlReportePorEstudio."\" )'><span style='color:green;'>Ver reporte</span></a></div>";
						}
						
						echo "</td>";
						
						//Hora de solicitud examen
						// echo "<td>";
						// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							// //echo "<INPUT TYPE='text' id='whsol$contExamenes' NAME='whsol$contExamenes' SIZE=10 readonly class='campo2 calendariohora' value='$examen->horaARealizar' onChange='marcarCambio(\"$indicePestana\",\"$contExamenes\");'>";
							// crearCampo("1","whsol$contExamenes",@$accionesPestana[$indicePestana.".11"],array("SIZE"=>"10","onChange"=>"marcarCambio('$indicePestana','$contExamenes');","cod_examen"=>"$examen->codigoExamen","readonly"=>"readonly","class"=>"campo2 calendariofecha"),"$examen->horaARealizar");
						// } else {
							// echo "$examen->horaARealizar&nbsp;";
						// }
						// echo "</td>";
						
						
						// Columna de tipo de servicio
						echo "<td>".$examen->nombreCentroCostos."</td>";	
						$pendiente_lectura = "";
						if( permiteLecturaOrdenesPendientes( $conex, "01", $usuario->codigoRolHCE ) ){
							if($examen->pendienteLectura=="on"){				
								$pendiente_lectura = "style='background-color:#3CB648'";
							}
						}
						
						$muestras = "";
						if( count($examen->muestras) > 0 ){
							 $muestras .= "<table>";
							 $muestras .= "<tr>";
							 $muestras .= "<td style=\"border:1px solid black;background-color:#ccc;\"><b>Muestra</b></td>";
							 $muestras .= "<td style=\"border:1px solid black;background-color:#ccc;\"><b>Sitio anat&oacute;mico</b></td>";
							 $muestras .= "</tr>";
							 
							 foreach( $examen->muestras as $value ){
								$muestras .= "</tr>";
								$muestras .= "<td>".$value['origen']['descripcion']."</td>";
								$muestras .= "<td>".$value['sitioAnatomico']['descripcion']."</td>";
								$muestras .= "</tr>";
							 }
							 
							 $muestras .= "</table>";
						}
						
						//Columna de procedimiento
						echo "<td align='center' class='msg_tooltip' ".$pendiente_lectura." title='".$muestras ."'>";						
						echo " $examen->nombreExamen ";
						
						if(isset($examen->protocoloPreparacion) && !empty($examen->protocoloPreparacion)){
							$contenido = str_replace("\r\n","<br>",$examen->protocoloPreparacion);
							
							echo "<span id='$indicePestana-$contProtocolosExamenes' title=' - $contenido'>";
							echo "<img src='../../images/medical/root/info.png' border='0' />";
							echo "</span>";
							$contProtocolosExamenes++;
						}
						
						//Ocultos
						if($wfecha == $examen->fecha){
							echo "<input type=hidden name='wmodificado$indicePestana$contExamenes' id='wmodificado$indicePestana$contExamenes' value='S'>";	
						} else {
							echo "<input type=hidden name='wmodificado$indicePestana$contExamenes' id='wmodificado$indicePestana$contExamenes' value='N'>";
						}					
						echo "<input type=hidden id='wnmexamen$contExamenes' value='$examen->nombreExamen'>";
						echo "<input type=hidden id='hexcco$contExamenes' value='$examen->tipoDeOrden'>";
						echo "<input type=hidden id='hexcod$contExamenes' value='$examen->codigoExamen'>";
						echo "<input type=hidden id='hexcons$contExamenes' value='$examen->numeroDeOrden'>";
						echo "<input type=hidden id='hexnroitem$contExamenes' value='$examen->nroItem'>";
						echo "<input type=hidden id='hiReqJus$contExamenes' value='$examen->requiereJustificacion'>";
						echo "<input type=hidden id='hiFormHce$contExamenes' value='$examen->firmHCE'>";
						
						
						//Array procedimientos agrupados
						if( isset( $arrayParaCrearJsAgrupados[ $examen->tipoDeOrden."-".$examen->numeroDeOrden."-".$examen->nroItem ] ) ){
							//Creo el array que sirve para crear el objeto de javascript procAgrupados
							$tipoOrdenAgruNum = $arrayParaCrearJsAgrupados[ $examen->tipoDeOrden."-".$examen->numeroDeOrden."-".$examen->nroItem ];
							$procedimientosAgrupados[ $tipoOrdenAgruNum[0] ][] = array(
								'codigo' => $examen->codigoExamen,
								'consecutivo' => $contExamenes,
								'tipo' => $tipoOrdenAgruNum[1],
								'cantidad' => $tipoOrdenAgruNum[2],
								'justificacion' => $tipoOrdenAgruNum[3],
								'estadoProced' => $tipoOrdenAgruNum[4],
								'medicamentos' => (object)$tipoOrdenAgruNum[5],
								'imprimir' => $tipoOrdenAgruNum[6]
							);
						
						
						}
						
						echo "</td>";
	// var_dump($tipoOrdenAgruNum);
	// var_dump($procedimientosAgrupados);
						//Justificacion
						echo "<td>";
						if($examen->requiereJustificacion != 'on' ){
							crearCampo("2","wtxtjustexamen$contExamenes",@$accionesPestana[$indicePestana.".6"],array("cols"=>"35","rows"=>"2","onChange"=>"marcarCambio('$indicePestana','$contExamenes');"),"$examen->justificacion");
						}
						else{
							crearCampo("2","wtxtjustexamen$contExamenes",@$accionesPestana[$indicePestana.".6"],array("cols"=>"35","rows"=>"2","onChange"=>"marcarCambio('$indicePestana','$contExamenes');", "class"=>"fondoAmarillo"),"$examen->justificacion");
						}
							echo "</td>";
						echo "</td>";
							
						//Estado del examen
						if($examen->tipoDeOrden == $codigoAyudaHospitalaria){
							echo "<td>";
							$opcionesSeleccion = "";
														
							foreach ($consultarEstadosAyudasDx as $estadoExamen){
								if($examen->estadoExamen!=$estadoExamen->codigo)
									$opcionesSeleccion .= "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
								else
									$opcionesSeleccion .= "<option value='$estadoExamen->codigo' selected>$estadoExamen->descripcion</option>";
							}
							
							crearCampo("6","westadoexamen$contExamenes",@$accionesPestana[$indicePestana.".9"],array("class"=>"campo2","onChange"=>"marcarCambio('$indicePestana','$contExamenes');"),"$opcionesSeleccion");
		
							echo "</td>";	
							
						}
						else {
							// echo "<td class=fondoAmarillo>";
							// 2012-07-13
							//Si el rol puede modificar estado del examen
							// if(puedeCambiarEstado()) {

								// $estadoActual = $examen->estadoExamen;
								// $descripcionEstadoActual = consultarDescripcionEstado($estadoActual);

								// $opcionesSeleccion = "";								
								
								// //Consulto el estado que debe estar inactivo para las enfermeras
								// $estados_por_rol_enf = estados_por_rol_enf();
								
								// foreach ($consultarEstadosAyudasDx as $estadoExamen){
									// $disabled = "";
									
									// //Si el estado no esta en el arreglo se inactivara la seleccion.
									// if($estados_por_rol_enf[$estadoExamen->codigo] == ''){
										// $disabled = "disabled";
									// }
																											
									// if($estadoExamen->codigo!=$estadoActual){
										// $opcionesSeleccion .= "<option value='$estadoExamen->codigo' $disabled>$estadoExamen->descripcion</option>";
										
									// }else{										
										
										// $opcionesSeleccion .= "<option value='$estadoExamen->codigo' selected $disabled>$estadoExamen->descripcion</option>";
										
										// }
								// }
								
								// crearCampo("6","westadoexamen$contExamenes",@$accionesPestana[$indicePestana.".9"],array("class"=>"campo2","onChange"=>"marcarCambio('$indicePestana','$contExamenes');"),"$opcionesSeleccion");
								
								
							// } else {
								// echo "<input type='hidden' name='westadoexamen$contExamenes' id='westadoexamen$contExamenes' value='$examen->estadoExamen'>";
								
							// }
							
							$classCeldaEstado = "fondoAmarillo";
							
							$opcionesSeleccion = "";
							$estadoActual = $examen->estadoExamen;
							//Se recorre la tabla movhos_45 que contiene los estados de los examenes.	
							foreach ($consultarEstadosAyudasDx as $estadoExamen){
								
								//Se busca dentro del arreglo de permisos o acciones de la pestaña el codigo del estado que viene del campo Eexcpe ($estadoExamen->codigo_permiso).
								$permisoEstado = @$accionesPestana[$indicePestana.".".$estadoExamen->codigo_permiso];
								
								$estado_opcion = "disabled";
								
								//Si el rol tiene permisos y puede leer entonces el estado de la opcion estara activo.								
								if(count($permisoEstado) > 0 and $permisoEstado->leer == true){									
									$estado_opcion = "";
								}
								
								/****************************************************************************************************
								 * Si no permite cancelar examen, se impide modificar el estado cancelar
								 ****************************************************************************************************/
								$permiteModificarEstado = $examen->permiteModificarEstado( $conex, $wbasedato );
								$permiteCancelar 		= $examen->permiteCancelarExamen( $conex, $wbasedato );
								
								if( $examen->esCupOfertado ){
									$permiteModificarEstado = false;
								}
								
								if( empty( $examen->estadoExterno ) ){
									$permiteModificarEstado = true;
								}
								
								if( !$permiteModificarEstado ){
									
									if( $estadoExamen->esCancelado ){
										
										if( !$permiteCancelar ){
											$estado_opcion = "disabled";
										}
									}
									else{
										$estado_opcion = "disabled";
									}
								}
								/***************************************************************************************************/
								
								if($estadoExamen->codigo!=$estadoActual){
									
										$opcionesSeleccion .= "<option value='$estadoExamen->codigo' $estado_opcion>$estadoExamen->descripcion</option>";
											
								}
								else{
									if($estadoExamen->esRealizado ){
										$classCeldaEstado = "fondoVerde";
									}
									
									if($estadoExamen->esCancelado){
										$classCeldaEstado = "fondoRojo";
									}
										
									$opcionesSeleccion .= "<option value='$estadoExamen->codigo' selected $estado_opcion>$estadoExamen->descripcion</option>";
										
								}
							
							}
							
							echo "<td class=$classCeldaEstado>";
							
							crearCampo("6","westadoexamen$contExamenes",@$accionesPestana[$indicePestana.".9"],array("class"=>"campo2","onChange"=>"marcarCambio('$indicePestana','$contExamenes');"),"$opcionesSeleccion");
							
							if( !empty( $examen->descripcionEstadoExterno ) ){
								
								$classBlink = '';
								if( $examen->pendienteLecturaMedico && ( $examen->estadoExterno == 'P' || $examen->estadoExterno == 'F' ) )
									$classBlink = 'blink';
								
								if( $examen->pendienteLecturaEstudioCancelado ){
									echo "<span class='blink' onmouseover='marcarLeidoEstudioCancelado( this );'  plecancelado='".$examen->tipoDeOrden."-".$examen->numeroDeOrden."-".$examen->nroItem."' style='display:block;font-weight:bold;'>".$examen->descripcionEstadoExterno."</span>";
								}
								else{
									echo "<span class='blink-".$examen->tipoDeOrden."-".$examen->numeroDeOrden." ".$classBlink."' style='display:block;font-weight:bold;'>".$examen->descripcionEstadoExterno."</span>";
								}
							}
							
							if( !empty( $examen->justificacionOrdenCancelada ) || !empty( $examen->comentarios ) ){
								
								$estadoPorInteroperabilidadEsCancelado = estadoPorInteroperabilidadEsCancelado( $conex, $wbasedato, $examen->estadoExterno );
								
								$icon_jus = '../../images/medical/root/info.png';
								$jus_add	= '';
							
								if( $estadoPorInteroperabilidadEsCancelado ){
									$icon_jus = '../../images/medical/sgc/Mensaje_alerta.png';
									$jus_add 	= "Cancelado en ".$examen->nombreCentroCostos."<br>";
								}
								
								echo "<span style='display:block;font-weight:bold;' class='msg_tooltip' title='".$jus_add.$examen->justificacionOrdenCancelada.( ( !empty( $examen->justificacionOrdenCancelada ) && !empty( $examen->comentarios ) ) ? "<br>" : '' ).$examen->comentarios."'><img src='".$icon_jus."' width='20px'/></span>";
							}
							
							echo "</td>";
						}
						
						
						/*********************************************************************************************************************
						 * TOMA DE MUESTRAS
						 *********************************************************************************************************************/
						echo "<td>"; 
						if( $examen->solicitaUsuarioTomaMuestra ){
							if( empty( $examen->usuarioTomaMuestra ) ){
								
								if( time() > strtotime( $examen->fechaARealizar." 00:00:00" ) ){
									crearCampo("5","wusuariotomamuestra$contExamenes",@$accionesPestana[$indicePestana.".16"],array("class"=>"campo2","onChange"=>"marcarCambioTomaMuestra('$indicePestana','$contExamenes',".( $examen->realizaUnidad ? '1' : '0' ).",0);"),"");
								}
								else{
									$datetime1 			= new DateTime( date("Y-m-d 00:00:00") );
									$datetime2 			= new DateTime( $examen->fechaARealizar );
									$dias_toma_muestra 	= $datetime1->diff($datetime2);
									
									if( $dias_toma_muestra->d == 1 ){
										echo "<b>En ".$dias_toma_muestra->d." d&iacute;a</b>";
									}
									else{
										echo "<b>En ".$dias_toma_muestra->d." d&iacute;as</b>";
									}
								}
							}
							else{	
								if( $examen->realizaUnidad ){
									// echo $examen->usuarioTomaMuestra;
									echo '<span onclick="imprimirSticker( \''.$contExamenes.'\',1,1 );">';
									echo '<img src="/matrix/images/medical/movhos/checkmrk.ico" width="25px" style="cursor:pointer;" title="<span style=font-size:10pt;>'.$examen->usuarioTomaMuestra.'</span>">';
									echo '<img src="../../images/medical/hce/icono_imprimir.png" width="25px" style="cursor:pointer;">';
									echo '</span>';
								}
								else{
									echo '<img src="/matrix/images/medical/movhos/checkmrk.ico" width="25px" style="cursor:pointer;" title="<span style=font-size:10pt;>'.$examen->usuarioTomaMuestra.'</span>">';
								}
							}
						}
						echo "</td>";
						/*********************************************************************************************************************/
						
						/************************************************************************************************************************
						 * Enero 26 de 2012
						 * Pinto la bitacora de procedimientos
						 ************************************************************************************************************************/
						// echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='60' onkeypress='return validarEntradaAlfabetica(event);' onChange='marcarCambio(\"$indicePestana\",\"$contExamenes\");'>$examen->observaciones</textarea></td>";
						if( !empty($examen->bitacoraGestion) && count( $examen->bitacoraGestion ) > 0 ){

							echo "<td style='font-size:8pt;'>";
							echo "<div style='overflow:auto; height:80px;'>";
							// var_dump($examen->bitacoraGestion);
							echo "<table width=320px>";
							$clase="fila1";	
							foreach( $examen->bitacoraGestion as $keyBitacora => $valueBitacora ){
								if($clase=="fila1"){
									$clase = "fila2";
								} else {
									$clase = "fila1";
								}
								echo "<tr class=$clase><td style='font-size:10pt'>";
								echo str_replace( "\n", "<br>", htmlentities( $valueBitacora['bitacora'] ) );
								echo "<br><p style='font-size:6pt; font-weight: bold;'>".$valueBitacora['fecha']." por ".$valueBitacora['usuario']." - ".$valueBitacora['nombre']."</p>";
								echo "</td></tr>";
								

								
							}
							echo "</table>";
							echo "</div>";
							echo "</td>";
						}
						else{
							echo "<td></td>";
						}
												
						echo "</tr>";
						
						$contExamenes++;
					}
					
					//Fin filas
//					$consecutivoOrdenExamen = $examen->numeroDeOrden;
					$centroCostosExamenes = $examen->tipoDeOrden; 
//					$contExamenes++;
					
				} //FIN FOREACH $colExamenesHistoria as $examen

				if($esPacEPS==true && $strExamenesPendientesCTC!="")
				{
					// echo "<script> alert('Debe llenar los CTC de los articulos no pos grabados'); </script>";
					// echo "<script> abrirCTCMultipleParaArticulosGrabados('$cadenaGuardadosSinCTC',''); </script>";
					
					// echo "<script> var cadenaGuardadosSinCTC = $cadenaGuardadosSinCTC; alert(cadenaGuardadosSinCTC);</script>";
					
					echo "<INPUT TYPE='hidden' name='cadenaGuardadosSinCTCO' id='cadenaExamenesGuardadosSinCTCO' value='".$strExamenesPendientesCTC."'>";
				}

				$auxContExamenes = $contExamenes;
				
				if($cuentaExamenesHistoria != 0){
					
					if( $pasoPorAqui ){
						//Cierre de tabla
						echo "</tbody>";
						//echo "</table>";
					}

					
					if( $huboOrdenes ){
						//Cierre de orden
						echo "</span>";
						echo "</div>";
					}			

					//Cierro centro de costos
					//echo "</span>";
					//echo "</table>";
					echo "</div>";
					echo "</div>";
				}
				else
				{
					echo "</div>";
				}
				
				echo "</table>";
				
				echo "<br>";
			
				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */				
				// $contExamenesPend=$contExamenes;
				$contExamenesPend=-1;
				$contExamenes = 0;

				$fechaTmp = "";
				
				// 2012-07-03
				echo "</div>";
				
				echo "</div>";
				
				/********************************************************
				 **  Fin contenedor de pestaña de ordenes pendientes   **
				 ********************************************************/


				
				// 2012-07-03
				/********************************************************
				 ********************************************************
				 ** Inicio contenedor de pestaña de ordenes realizadas **
				 ********************************************************
				 ********************************************************/
				echo "<div id='fragment-realizadas'>";

				  echo "<div name='detOrdenesRealizadas' id='detOrdenesRealizadas'>";
				  $whis = $paciente->historiaClinica;
				  $wing = $paciente->ingresoHistoriaClinica;
				  $wcco = $paciente->servicioActual;
				  

				  $dias = 21;
				  $fechaord = date("Y-m-d");
				  if(!isset($wfecini))
					$wfecini = date("Y-m-d", strtotime("$fechaord -$dias day"));
				  if(!isset($wfecfin))
					$wfecfin = $fechaord;
				  
				  if(!isset($wtiposerv))
					$wtiposerv = "";
					
				  if(!isset($wprocedimiento))
					$wprocedimiento = "";
					
				  $westadodet = 'Realizado';

				  // Muestra la lista de ordenes según los parámetros enviados
				  // mostrarDetalleOrdenes( $wemp_pmla,$wempresa,$wbasedato,$whis,$wing,$wfecini,$wfecfin,$wtiposerv,$wprocedimiento,$westadodet );				  
				  mostrarDetalleOrdenes( $wemp_pmla,$wempresa,$wbasedato,$whis,$wing,$wfecini,$wfecfin,$wtiposerv,$wprocedimiento,$westadodet,$contExamenesPend );				  
				  
				  echo "</div>";

				// 2012-07-03
				echo "</div>";
				/********************************************************
				 **  Fin contenedor de pestaña de ordenes realizadas   **
				 ********************************************************/

				
				//if( $huboOrdenes )
					echo "</div>";
				
				echo "</div>";
				
				if($editable != 'off'){
				echo "<br /><div align='center'><input type=button value='Enviar a Centro de Impresi&oacute;n' onclick='grabarKardex(\"cenimppro\")'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=button value='Imprimir' onclick='grabarKardex(\"imppro\")'></div>";
				
				echo "<div align='center' id='linkcenimpexam' style='display:none;'></div>";
				}
				
				echo "</div>";

				
				if (isset($mayorIdInfusiones))
				   echo "<input type='HIDDEN' name='cuentaInfusiones' id='cuentaInfusiones' value='".$mayorIdInfusiones."'/>";
			}
			
			//echo "</div>";

			$indicePestana = "5";
			// 2012-07-09
			// Se comenta porque se elimina la pestaña Pendientes
			/*
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-5'>";

				
				//Acciones complementarias
				echo '<span class="subtituloPagina2" align="center">';
				echo "Acciones complementarias y pendientes";
				echo "</span><br><br>";
				
				echo "<table align='center'>";

				echo "<tr>";

				//Acciones predefinidas del arbol para pendientes
				echo "<td rowspan=5 valign=top>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<div class=textoNormal align=left>";
					$colClases = consultarClases("1");

					echo "'".@$accionesPestana[$indicePestana."R$clase->codigo"]."'";
					echo "<ul class='simpleTree' id='arbol1'>";
					echo "<li class='root' id='1'><span>Acciones predefinidas</span>";
					echo "<ul>";
					foreach ($colClases as $clase){
						if($clase->codigo != ''){
							$ramaArbol = "<li id='R$clase->codigo'><span>$clase->descripcion</span>";
							$ramaArbol .= "<ul class='ajax'>";
							$ramaArbol .= "<li id='ajax$clase->codigo'>{url:../../../include/movhos/kardex.inc.php?tree_id=1&consultaAjaxKardex=24&nivelA=$clase->codigo&basedatos=$wbasedato}</li>";
							$ramaArbol .= "</ul>";
							$ramaArbol .= "</li>";
							
							crearCampo("7","ajax$clase->codigo",@$accionesPestana[$indicePestana.".R$clase->codigo"],array(),"$ramaArbol");
							
//							echo "<li id='R$clase->codigo'><span>$clase->descripcion</span>";
//							echo "<ul class='ajax'>";
//							echo "<li id='ajax$clase->codigo'>{url:../../../include/movhos/kardex.inc.php?tree_id=1&consultaAjaxKardex=24&nivelA=$clase->codigo&basedatos=$wbasedato}</li>";
//							echo "</ul>";
//							echo "</li>";
						}
					}
					echo "</ul>";
					echo "</li>";
					echo "</ul>";

					echo "</div>";
				}
				echo "</td>";
				//Cuidados enfermería
				echo "<td width='550' class='fila1' colspan=3 align=center>Cuidados de enfermer&iacute;a";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2",'txCuidados',@$accionesPestana[$indicePestana.".R02"],array("cols"=>"100","rows"=>"8","onFocus"=>"expandirRama(this,'R02');"),"$kardexActual->cuidadosEnfermeria");
//					echo "<textarea name=txCuidados id=txCuidados rows=8 cols=100 onFocus='expandirRama(this,\"R02\");'>$kardexActual->cuidadosEnfermeria</textarea>";
				} else {
					echo "<textarea name=txCuidados rows=8 cols=100 readonly>";
					echo $kardexActual->cuidadosEnfermeria;
					echo "</textarea>";
				}
				echo "</td>";

				//Sondas y cateteres
				/*
				echo "<td width='210' class='fila1'>Sondas, cateteres y drenes";

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txtSondas id=txtSondas rows=8 cols=30 onFocus='expandirRama(this,\"R01\");'>";
					echo $kardexActual->sondasCateteres;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtSondas rows=8 cols=30 readonly>";
					echo $kardexActual->sondasCateteres;
					echo "</textarea>";
				}
				echo "</td>";
				*/
				
				/*
				//Aislamientos
				echo "<td width='20' class='fila1'>Aislamientos";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txAislamientos id=txAislamientos rows=8 cols=30 onFocus='expandirRama(this,\"R03\");'>";
					echo $kardexActual->aislamientos;
					echo "</textarea>";
				} else {
					echo "<textarea name=txAislamientos rows=8 cols=30 readonly>";
					echo $kardexActual->aislamientos;
					echo "</textarea>";
				}
				echo "</td>";
				*/
				
				/*
				echo "</tr>";

				echo "<tr>";
				*/

				//Curaciones
				/*
				echo "<td width='20' class='fila1'><label for='wcodmed'>Curaciones";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txtCuraciones id=txtCuraciones rows=8 cols=30 onFocus='expandirRama(this,\"R04\");'>";
					echo $kardexActual->curaciones;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtCuraciones rows=8 cols=30 readonly>";
					echo $kardexActual->curaciones;
					echo "</textarea>";
				}
				echo "</td>";
				*/
/*
				//Terapia respiratoria, fisica y rehabilitacion cardiaca
				echo "<td width='180' class='fila1'>";
				echo "<label for='wcodmed'>Terapias";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txTerapia id=txTerapia rows=8 cols=30 onFocus='expandirRama(this,\"R05\");'>";
					echo $kardexActual->terapiaRespiratoria;
					echo "</textarea>";
				} else {
					echo "<textarea name=txTerapia rows=8 cols=30 readonly>";
					echo $kardexActual->terapiaRespiratoria;
					echo "</textarea>";
				}
				echo "</td>";
				
				echo "</tr>";
				
				//Interconsulta
				echo "<td width='180' class='fila1'>Interconsulta";

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txtInterconsulta id=txtInterconsulta rows=8 cols=30 onFocus='expandirRama(this,\"R06\");'>";
					echo $kardexActual->interconsulta;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtInterconsulta rows=8 cols=30 readonly>";
					echo $kardexActual->interconsulta;
					echo "</textarea>";
				}
				echo "</td>";
					
				//Cirugias pendientes
				echo "<td width='180' class='fila1'>Cirug&iacute;as pendientes";

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txtCirugiasPendientes rows=8 cols=30>";
					echo $kardexActual->cirugiasPendientes;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtCirugiasPendientes rows=8 cols=30 readonly>";
					echo $kardexActual->cirugiasPendientes;
					echo "</textarea>";
				}
				echo "</td>";
*/
				/*
				echo "</tr>";

				//Observaciones generales
				echo "<td width='550' class='fila1' colspan=3 align=center>Observaciones generales";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2",'txObservaciones',@$accionesPestana[$indicePestana.".2"],array("cols"=>"100","rows"=>"8"),"$kardexActual->observaciones");
//					echo "<textarea name=txObservaciones rows=8 cols=100>$kardexActual->observaciones</textarea>";
				} else {
					echo "<textarea name=txObservaciones rows=8 cols=100 readonly>";
					echo $kardexActual->observaciones;
					echo "</textarea>";
				}
				echo "</td>";
					
				echo "</tr>";
				echo "</table>";
				echo "</div>";
			}
			*/
			
			$indicePestana = "6";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-6'>";
				
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";

				//Consulta de los valores actuales para el dextrometer
				$esquemaInsulina = consultarEsquemaInsulina($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				
				if(!isset($esquemaInsulina->codigo)){
					$esquemaInsulina = consultarEsquemaInsulina($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$fechaAyer);
				}
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";

				//Insulina
				$colInsulinas = consultarArticulosEspeciales("",$articuloInsulina);
				echo "<tr><td class='fila1' width=170>Insulina</td>";
				echo "<td class='fila2' align='center'>";

				$opcionesSeleccion = "<option value='' data-vias=''>Seleccione</option>";
				foreach ($colInsulinas as $insulina){
					
					//Consulto la información por familia
					$infoFamlila = consultarInfoFamiliaPorArticulo( $conex, $wbasedato, $insulina->codigo );
					
					if($esquemaInsulina->codigo == $insulina->codigo){
						$opcionesSeleccion .= "<option value='".$insulina->codigo."' data-familia='".$infoFamlila['familia'][ 'Famnom' ]."' data-vias='".$insulina->vias."' data-unidad='".$insulina->unidadDeMedida."' selected>$insulina->codigo - $insulina->nombre</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$insulina->codigo."' data-familia='".$infoFamlila['familia'][ 'Famnom' ]."' data-vias='".$insulina->vias."' data-unidad='".$insulina->unidadDeMedida."'>$insulina->codigo - $insulina->nombre</option>";
					}
				}
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("6","wdexins",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccionNormal","style"=>"height: 25px;", "onchange"=>"seleccionarInsulina( this )"),"$opcionesSeleccion");
//					echo "<select id='wdexins' class='seleccionNormal'>";
				} else {
					crearCampo("6","wdexins",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccionNormal","style"=>"height: 25px;","disabled"=>""),"$opcionesSeleccion");
//					echo "<select id='wdexins' class='seleccionNormal' disabled>";
				}
				

				echo "</select>";
				echo "</td>";
				echo "</tr>";
				
				//Frecuencia del procedimiento
				echo "<tr><td class='fila1' width=270>Frecuencia o condici&oacute;n</td>";
				echo "<td class='fila2' align='center'>";

				$opcionesSeleccion = "<option value=''>Seleccione</option>";
				foreach ($colCondicionesSuministroInsulinas as $condicion){
					if($esquemaInsulina->frecuencia == $condicion->codigo){
						$opcionesSeleccion .= "<option value='".$condicion->codigo."' selected data-condicion='".$condicion->condicion."'>$condicion->descripcion</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$condicion->codigo."' data-condicion='".$condicion->condicion."'>$condicion->descripcion</option>";
					}
				}

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					// crearCampo("6","wdexfrecuencia",@$accionesPestana[$indicePestana.".2"],array("class"=>"seleccionNormal","style"=>"height: 25px;","onchange"=>"adicionarArticuloInsulina()"),"$opcionesSeleccion");
					crearCampo("6","wdexfrecuencia",@$accionesPestana[$indicePestana.".2"],array("class"=>"seleccionNormal","style"=>"height: 25px;","onchange"=>""),"$opcionesSeleccion");
//					echo "<select id='wdexfrecuencia' class='seleccionNormal'>";
				} else {
					crearCampo("6","wdexfrecuencia",@$accionesPestana[$indicePestana.".2"],array("class"=>"seleccionNormal","style"=>"height: 25px;","disabled"=>""),"$opcionesSeleccion");
//					echo "<select id='wdexfrecuencia' class='seleccionNormal' disabled>";
				}

			
				echo "</td>";
				echo "</tr>";
					
				//Esquema dextrometer de los predefinidos
				echo "<tr><td class='fila1' width=270>Esquema dextrometer predefinido</td>";
				echo "<td class='fila2' align='center'>";
				
				echo "<input type=hidden name=whdexesquemaant id=whdexesquemaant value='$esquemaInsulina->codEsquema'>";
				
				$opcionesSeleccion = "<option value=''>Ninguno</option>";
				foreach (consultarMaestroEsquemasInsulina() as $esquema){
					if($esquemaInsulina->codEsquema == $esquema->codigo){
						$opcionesSeleccion .= "<option value='".$esquema->codigo."' selected>Esquema $esquema->codigo</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$esquema->codigo."'>Esquema $esquema->codigo</option>";
					}
				}

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					
					$disabledEsquemaDex = "";
					if( empty( $esquemaInsulina->codigo ) ){
						$disabledEsquemaDex = "disabled";
					}
					
					crearCampo("6","wdexesquema",@$accionesPestana[$indicePestana.".3"],array("class"=>"seleccionNormal","onChange"=>" consultarEsquemaInsulina();","$disabledEsquemaDex" => $disabledEsquemaDex ),"$opcionesSeleccion");
//					echo "<select id='wdexesquema' class='seleccionNormal' onChange='consultarEsquemaInsulina();'>";
				}
				else{
					crearCampo("6","wdexesquema",@$accionesPestana[$indicePestana.".3"],array("class"=>"seleccionNormal","disabled"=>"","onChange"=>"consultarEsquemaInsulina();"),"$opcionesSeleccion");
//					echo "<select id='wdexesquema' class='seleccionNormal' onChange='consultarEsquemaInsulina();' disabled>";
				}

			
				echo "</td>";
				echo "</tr>";
					
				//Observaciones dextrometer
				echo "<tr><td class='fila1' colspan=2 align=center>Observaciones esquema dextrometer</td>";
				echo "<tr>";
				echo "<td colspan=2 class=fila2 align=center>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					// crearCampo("2",'txtDextrometer',@$accionesPestana[$indicePestana.".4"],array("cols"=>"40","rows"=>"5","onchange"=>"adicionarArticuloInsulina();"),"$kardexActual->dextrometer");
					crearCampo("2",'txtDextrometer',@$accionesPestana[$indicePestana.".4"],array("cols"=>"40","rows"=>"5","onchange"=>";"),"$kardexActual->dextrometer");
//					echo "<textarea name=txtDextrometer rows=5 cols=40>$kardexActual->dextrometer</textarea>";
				} else {
					echo "<textarea name=txtDextrometer id=txtDextrometer rows=5 cols=40 readonly>$kardexActual->dextrometer</textarea>";
				}
				echo "</td>";
				echo "</tr>";
				
				//Consulta del esquema actual si existe
				$intervalosEsquema = consultarIntervalosDextrometer($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				
				
				echo "<tr><td align=center colspan=4>";
				if(count($intervalosEsquema) > 0){
						echo "<br>";
						crearCampo("3","btnQuitarEsquema",@$accionesPestana[$indicePestana.".5"],array("onClick"=>"borrarEsquemaDextrometer('on');"),"Quitar esquema");
				}
				echo "<INPUT type='button' id='btConfirmaDextrometer' value='Confirmar Dextrometer' onClick='adicionarArticuloInsulina();' style='display:none'>";
				echo "</td></tr>";
				echo "</table>";
		
				
				if(count($intervalosEsquema) == 0){
					$intervalosEsquema = consultarIntervalosDextrometer($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,date("Y-m-d"));
				}
				
				if(count($intervalosEsquema) > 0){
					echo "<br>";
					echo "<center>";
					echo '<span class="subtituloPagina2">';
					echo 'Esquema actual dextrometer';
					echo "</span>";
					echo "</center>";
					echo "<br>";
					
					echo "<div id='cntEsquemaActual'>";
					echo "<table align=center>";
					echo "<tr class=encabezadoTabla align=center><td>M&iacute;nimo</td><td>M&aacute;ximo</td><td>Dosis</td><td>V&iacute;a</td><td>Observaciones</td></tr>";
					
					$clase = "fila2";
					$conDosIns = 0;
					foreach ($intervalosEsquema as $intervalo){
						if($intervalo->minimo != ''){
							if($clase=="fila1"){
								$clase = "fila2";
							} else {
								$clase = "fila1";
							}

							echo "<tr class=$clase align=center>";
							echo "<td>$intervalo->minimo</td>";
							echo "<td>$intervalo->maximo</td>";
							echo "<td>";
							echo "$intervalo->dosis ";
							echo "<input type='hidden' id='wdexintact$conDosIns' name='wdexintact$conDosIns' value='$intervalo->dosis'> ";
							
								
							foreach ($colUnidades as $unidad){
								if($unidad->codigo == $intervalo->unidadDosis){
									echo ucfirst(strtolower($unidad->descripcion));
								}
							}
							echo "</td>";

							echo "<td>";
							foreach ($colVias as $via){
								if($via->codigo == $intervalo->via){
									echo $via->descripcion;
								}
							}
							echo "</td>";
							echo "<td align='left'>$intervalo->observaciones</td>";
							
							$conDosIns++;
						}
					}
					echo "</table>";
					echo "</div>";
				}
				
				//Con ajax consulto la tabla de intervalos
				echo "<br>";
				echo "<div id=cntEsquema align=center>";
				echo "</div>";
				
				echo "</div>";
			}
			
			$indicePestana = "7";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-7'>";
				
				// $accionesPestana = consultarAccionesPestana($indicePestana);
				
				// //Indicador para javascript de puede grabar la pestaña
				// $estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				// echo "<input type=hidden id='pestana' value='$indicePestana'>";
				
				// if($mostrarAuditoria){
					// echo "<input type='hidden' name='wauditoria' value='1'>";

					// echo "<table align='center' border=0>";

					// echo "<tr align='center' class='encabezadoTabla'>";
					// echo "<td>Usuario</td>";
					// echo "<td>Fecha y hora</td>";
					// echo "<td>Mensaje</td>";
					// echo "<td>Referencia</td>";
					// echo "</tr>";

					// $historialCambios = consultarHistorialCambiosKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);

					// $cont1 = 0;
					// foreach($historialCambios as $historia){
						// if($cont1 % 2 == 0){
							// echo "<tr class='fila1'>";
						// } else {
							// echo "<tr class='fila2'>";
						// }

						// echo "<td>$historia->usuario</td>";
						// echo "<td>$historia->fecha - $historia->hora</td>";
						// echo "<td>$historia->mensaje</td>";
						// echo "<td>$historia->descripcion</td>";
							
						// echo "</tr>";
							
						// $cont1++;
					// }
						
					// echo "</table>";
				// }
				echo "</div>";
			}
			/*PESTAÑA DE ANALGESIAS Y NUTRICIONES
			$indicePestana = "8";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-8'>";				

				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";
				
				$elementosActuales = 0;
				$colDetalle = array();
				
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<div align=center>";
					crearCampo("3","",@$accionesPestana[$indicePestana.".1"],array("onClick"=>"abrirMovimientoArticulos('A,U');"),"Ordenar...");
//					echo "<input type='button' value='Ordenar' onclick='abrirMovimientoArticulos(\"A,U\");'>";
					echo "</div>";
				}

				vista_generarConvencion();		//Genera la muestra de convenciones de los articulos
				
				//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
				realizarMovimientosArticulos($kardexActual->editable && $vecPestanaGrabacion[$indicePestana], $kardexActual->esAnterior, $esFechaActual, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha, $fechaGrabacion, $protocoloAnalgesia, &$elementosActuales, &$colDetalle);

				//Subtitulo
				echo '<span class="subtituloPagina2">';
				echo 'Analgesia';
				echo "</span>";
				echo "<br>";
				echo "<br>";
				
				//Despliega la vista de la tabla de articulos para el protocolo normal
				vista_desplegarListaArticulos($colDetalle,$elementosActuales,$protocoloAnalgesia,$kardexActual->editable && $vecPestanaGrabacion[$indicePestana],$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro,$accionesPestana,$indicePestana);
				
				//Detalle de medicamentos anteriores
				$colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$protocoloAnalgesia);
				$cantidadElementosAnteriores = count($colDetalleAnteriorKardex);

				if($cantidadElementosAnteriores > 0){
					vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,$protocoloAnalgesia,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias);
				} else {
					echo '<br><span class="subtituloPagina2" align="center">';
					echo "No hay analgesias anteriores";
					echo "</span>";
					echo "<div id='medAnt'>";
					echo "</div>";
				}

				echo "<br>";
				echo "<br>";
				
				//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
				realizarMovimientosArticulos($kardexActual->editable && $vecPestanaGrabacion[$indicePestana], $kardexActual->esAnterior, $esFechaActual, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha, $fechaGrabacion, $protocoloNutricion, &$elementosActuales, &$colDetalle);

				//Subtitulo
				echo '<span class="subtituloPagina2">';
				echo 'Nutriciones';
				echo "</span>";
				echo "<br>";
				echo "<br>";
				
				//Despliega la vista de la tabla de articulos para el protocolo normal
				vista_desplegarListaArticulos($colDetalle,$elementosActuales,$protocoloNutricion,$kardexActual->editable && $vecPestanaGrabacion[$indicePestana],$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro,$accionesPestana,$indicePestana);
				
				//Detalle de medicamentos anteriores
				$colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, $protocoloNutricion);
				$cantidadElementosAnteriores = count($colDetalleAnteriorKardex);

				if($cantidadElementosAnteriores > 0){
					vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,$protocoloNutricion,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias);
				} else {
					echo '<br><span class="subtituloPagina2" align="center">';
					echo "No hay nutriciones anteriores";
					echo "</span>";
					echo "<div id='medAnt'>";
					echo "</div>";
				}
				echo "</div>";
			}
			*/
			/* PESTAÑA DE QUIMIO
			$indicePestana = "9";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-9'>";
				
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";
				
				$elementosActuales = 0;
				$colDetalle = array();
				
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){ 
					echo "<div align=center>";
					crearCampo("3","",@$accionesPestana[$indicePestana.".1"],array("onClick"=>"abrirMovimientoArticulos('Q');"),"Ordenar...");
//					echo "<input type='button' value='Ordenar' onclick='abrirMovimientoArticulos(\"Q\");'>";
					echo "</div>";
				}

				vista_generarConvencion();		//Genera la muestra de convenciones de los articulos
				
				//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
				realizarMovimientosArticulos($kardexActual, $paciente, $esFechaActual, $wfecha, $fechaGrabacion, $protocoloQuimioterapia, &$elementosActuales, &$colDetalle);

				//Despliega la vista de la tabla de articulos para el protocolo normal
				vista_desplegarListaArticulos($colDetalle,$elementosActuales,$protocoloQuimioterapia,$kardexActual->editable && $vecPestanaGrabacion[$indicePestana],$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro,$accionesPestana,$indicePestana);
				
				//Detalle de medicamentos anteriores
				$colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, $protocoloQuimioterapia);
				$cantidadElementosAnteriores = count($colDetalleAnteriorKardex);

				if($cantidadElementosAnteriores > 0){
					vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,$protocoloQuimioterapia,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias);
				} else {
					echo '<br><span class="subtituloPagina2" align="center">';
					echo "No hay medicamentos de quimioterapia anteriores";
					echo "</span>";
					echo "<div id='medAnt'>";
					echo "</div>";
				}
				echo "</div>";
			}
			*/
			
			$indicePestana = "10";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-10'>";
				
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";



				//////////////////////////////////////////////////////////////////////////////////////
				// 2012-07-09
				// Se adicionó este código que viene de la pestaña Pendientes ya que ésta se quitó

				//$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				//$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				//echo "<input type=hidden id='pestana' value='$indicePestana'>";
				
				if( isset($accionesPestana[$indicePestana.".49"]) && $accionesPestana[$indicePestana.".49"]->leer == false ){
					echo "<table align='center' style='display:none'>";
				}
				else{
					echo "<table align='center'>";
				}
				echo "<tr>";

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<td class='fila1'>M&eacute;dicos</td>";
					echo "<td align=center class='fila2'>";

					//Seleccion de especialidad
					$especialidades = consultarEspecialidades();
					$opcionesSeleccion = "<option value=''>Seleccionar especialidad...</option>";
					foreach ($especialidades as $especialidad){
						$opcionesSeleccion .= "<option value='$especialidad->codigo'>".$especialidad->descripcion."</option>";
					}
					crearCampo("6","wselesp",@$accionesPestana[$indicePestana.".4"],array("class"=>"seleccionNormal","onchange"=>"consultarMedicosEspecialidad();"),"$opcionesSeleccion");
//					echo "<select name='wselesp' id='wselesp' class='seleccionNormal' onchange='consultarMedicosEspecialidad();'>";
//					echo "<option value=''>Seleccionar especialidad...</option>";
//					foreach ($especialidades as $especialidad){
//						echo "<option value='$especialidad->codigo'>".$especialidad->descripcion."</option>";
//					}
//					echo "</select>";

					echo "<br/><br/>";

					//Seleccion de medico
					$medicos = consultarMedicos();
					$opcionesSeleccion = "<option value=''>Seleccionar medico...</option>";
					foreach ($medicos as $medico){
						$opcionesSeleccion .= "<option value='".$medico->tipoDocumento."-".$medico->numeroDocumento."-".$medico->apellido1." ".$medico->apellido2.", ".$medico->nombre1." ".$medico->nombre2."-".$medico->codigoEspecialidad."'>".$medico->apellido1." ".$medico->apellido2.", ".$medico->nombre1." ".$medico->nombre2."</option>";
					}
					echo "<span id='cntSelMedicos'>";
					crearCampo("6","wselmed",@$accionesPestana[$indicePestana.".4"],array("class"=>"seleccionNormal"),"$opcionesSeleccion");
					echo "</span>";
						
//					echo "<span id='cntSelMedicos'>";
//					echo "<select name='wselmed' id='wselmed' class='seleccionNormal'>";
//					echo "<option value=''>Seleccionar medico...</option>";
//					foreach ($medicos as $medico){
//						echo "<option value='".$medico->usuarioMatrix."'>".$medico->apellido1." ".$medico->apellido2.", ".$medico->nombre1." ".$medico->nombre2."</option>";
//					}
//					echo "</select>";
//					echo "</span>";
						
					echo "<br>";
					echo "Tratante";
					crearCampo("5","wchkmedtra",@$accionesPestana[$indicePestana.".4"],array(),"");
//					echo "<input type=checkbox id=wchkmedtra>";
					echo "</td>";
					echo "<td class='fila1'>";
					crearCampo("3","",@$accionesPestana[$indicePestana.".4"],array("onClick"=>"adicionarMedico();"),"Agregar >>");
//					echo "<input type='button' onclick='adicionarMedico();' value='Agregar >>'>";
					echo "</td>";
				}
				echo "<td class='fila2'><b>M&eacute;dicos tratantes actuales</b>";

				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */
				
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana] && $kardexActual->esAnterior && $esFechaActual){
					//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
					$colTemporal = consultarMedicosTratantesTemporalKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					if(count($colTemporal) == 0){
						cargarMedicosAnteriorATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$fechaGrabacion);
					}
				}
				$cantMedicos = 0;
				$colMedicos = consultarMedicosTratantesTemporalKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				$cantMedicos = count($colMedicos);

				if($cantMedicos == 0){
					//1.2.1. Consulta de estructura definitiva
					$colMedicos = consultarMedicosTratantesDefinitivoKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					$cantMedicos = count($colMedicos);

					if($kardexActual->editable && $cantMedicos > 0  && $esFechaActual){
						//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
						cargarMedicosATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$wfecha);
					}
				}

				if($kardexActual->editable)
					$colMedicos = consultarMedicosTratantesHCE($paciente->historiaClinica,$paciente->ingresoHistoriaClinica);

				//Listado de medicos
				echo "<table align='center'>";
				echo "<tr>";
				echo "<td>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<div id='cntMedicos'>";		//Contenedor de listado de medicos
					foreach($colMedicos as $medico){ 
//						if(!empty($medico->id)){
							if($medico->tratante == 'on'){
								echo "<span id='Med$medico->numeroDocumento' class='vinculo'>";
//								echo "<a href='#null' onclick=quitarMedico('$medico->usuarioMatrix');>$medico->nombre1 $medico->nombre2 $medico->apellido1 $medico->apellido2 (Tratante)</a>";
								crearCampo("8","",@$accionesPestana[$indicePestana.".5"],array("onClick"=>"quitarMedico('$medico->numeroDocumento');"),"$medico->nombre1 $medico->nombre2 $medico->apellido1 $medico->apellido2 (Tratante)");
								echo "<br/></span>";
							} else {
								echo "<span id='Med$medico->numeroDocumento' class='vinculo'>";
//								echo "<a href='#null' onclick=quitarMedico('$medico->usuarioMatrix');>$medico->nombre1 $medico->nombre2 $medico->apellido1 $medico->apellido2</a>";
								crearCampo("8","",@$accionesPestana[$indicePestana.".5"],array("onClick"=>"quitarMedico('$medico->numeroDocumento');"),"$medico->nombre1 $medico->nombre2 $medico->apellido1 $medico->apellido2");
								echo "<br/></span>";
							}
//						}
					}
					echo "</div>";
				} else {
					foreach ($colMedicos as $medico) {
						if(!empty($medico->id)){
							echo $medico->nombre1." ".$medico->nombre2." ".$medico->apellido1." ".$medico->apellido2."<br>";
						}
					}
				}
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				echo "</tr>";

				echo "</table>";

				echo "<br/>";
				echo "<br/>";

				//Los campos abiertos que se dejan de usar se sustituirán por hiddens
//				echo "<input type=hidden name=txtPrepalta value=''>";
//				echo "<input type=hidden name=txtMezclas value=''>";
				echo "<input type=hidden name=txTerapiaFisica value=''>";
				echo "<input type=hidden name=txRehabilitacionCardiaca value=''>";

				//////////////////////////////////////////////////////////////////////////////////////


				
				echo "<table align='center'>";
				echo "<tr>";

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<td class='fila1'>Dieta</td>";
					echo "<td align=center class='fila2'>";
						
					$opcionesSeleccion = "<option value=''>Seleccione</option>";
					$dietas = consultarDietas();
					foreach ($dietas as $dieta){
						$opcionesSeleccion .= "<option value='".$dieta->codigo."'>".$dieta->descripcion."</option>";
					}
					crearCampo("6","wseldieta",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccionNormal"),"$opcionesSeleccion");

					echo "<td class='fila1'>";
					crearCampo("3","",@$accionesPestana[$indicePestana.".1"],array("onClick"=>"adicionarDieta();"),"Agregar >>");
					echo "</td>";
				}
				echo "<td class='fila2' align='center'><b>Dietas del paciente y observaciones nutricionales</b>";

				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana] && $kardexActual->esAnterior && $esFechaActual){
					//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
					$colTemporal = consultarDietasTemporalPaciente($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					if(count($colTemporal) == 0){
						cargarDietasAnteriorATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$fechaGrabacion);
					}
				}

				//1. Consulta de estructura temporal.
				$colDietas = consultarDietasTemporalPaciente($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				$cantDietas = count($colDietas);

				if($cantDietas == 0){
					//1.2.1. Consulta de estructura definitiva
					$colDietas = consultarDietasDefinitivoPaciente($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					$cantDietas = count($colDietas);

					if( $kardexActual->editable && $cantDietas > 0 && $esFechaActual){
						//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
						cargarDietasATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$wfecha);
					}
				}

				echo "<input type='hidden' name='colDietas'>";

				//Listado de dietas
				echo "<table align='center'>";
				echo "<tr>";
				echo "<td>";
				echo "<div id='cntDietas'>"; //Contenedor de listado de dietas
				foreach ($colDietas as $dieta){
					if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						if(!empty($dieta->codigoDieta)){
							echo "<span id='Die$dieta->codigoDieta' class='vinculo'>";
//							echo "<a href='#null' onclick=quitarDieta('$dieta->codigoDieta');>$dieta->descripcionDieta</a>";
							crearCampo("8","",@$accionesPestana[$indicePestana.".3"],array("onClick"=>"quitarDieta('$dieta->codigoDieta');"),"$dieta->descripcionDieta");
							echo "<br/></span>";
						}
					} else {
						if(!empty($dieta->codigoDieta)){
							echo "<br><center><b>".$dieta->descripcionDieta."</b></center><br>";
							
							if($kardexActual->obsDietas != ''){
							echo "<div id='cntDietasObs' style='width:360px'>";
							echo "<b>Observaciones:</b><br> ".$kardexActual->obsDietas;
							echo "</div>";
							}
							
						}
					}
				}
				echo "</div>";
				echo "</td>";
				//Observaciones de las dietas
				echo "<td class=fila>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
				crearCampo("2",'txtObsDietas',@$accionesPestana[$indicePestana.".2"],array("cols"=>"40","rows"=>"5"),"$kardexActual->obsDietas");
				}
//				echo "<textarea name=txtObsDietas rows=5 cols=40>$kardexActual->obsDietas</textarea>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";
					
				echo "</tr>";
					
				echo "</table>";
				echo "</td>";
					
				echo "</tr>";
				echo "</table>";
				
				//Medidas generales
				echo '<span class="subtituloPagina2" align="center">';
				echo "Medidas generales";
				echo "</span><br><br>";
				
				echo "<table align='center'>";

				echo "<tr>";

				//Arbol de medidas generales
				echo "<td rowspan=7 valign=top>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<div class=textoNormal align=left>";
					$colClases = consultarClases("2");

					echo "<ul class='simpleTree' id='arbol2'>";
					echo "<li class='root' id='2'><span>Medidas</span>";
					echo "<ul>";
					foreach ($colClases as $clase){
						if($clase->codigo != ''){						
							
								$ramaArbol = "";
								$ramaArbol .= "<li id='R$clase->codigo'><span>$clase->descripcion</span>";
								$ramaArbol .= "<ul class='ajax'>";
								$ramaArbol .= "<li id='ajax$clase->codigo'>{url:../../../include/movhos/kardex.inc.php?wemp_pmla=$wemp_pmla&tree_id=1&consultaAjaxKardex=24&nivelA=$clase->codigo&basedatos=$wbasedato}</li>";
								$ramaArbol .= "</ul>";
								$ramaArbol .= "</li>";								
								crearCampo("7","ajax$clase->codigo",@$accionesPestana[$indicePestana.".R$clase->codigo"],array(),"$ramaArbol");
								
	//							echo "<li id='R$clase->codigo'><span>$clase->descripcion</span>";
	//							echo "<ul class='ajax'>";
	//							echo "<li id='ajax$clase->codigo'>{url:../../../include/movhos/kardex.inc.php?tree_id=1&consultaAjaxKardex=24&nivelA=$clase->codigo&basedatos=$wbasedato}</li>";
	//							echo "</ul>";
	//							echo "</li>";
						}
					}
					echo "</ul>";
					echo "</li>";
					echo "</ul>";

					echo "</td>";
					
					$mult = 1;
					
					//Permisos para las medidas generales
					$accion = $accionesPestana[$indicePestana.".R07"];					
					if(count($accion) > 0 and $accion->leer){
						
						//Medidas generales editable
						if(!$accion->actualizar){
							
							$editar = "readonly";
						}
						
						echo "<tr>";					
						echo "<td width='220' class='fila1' colspan=3 align=center>Medidas generales";
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							crearCampo("2",'txMedidas',@$accionesPestana[$indicePestana.".R07"],array($editar=>$editar,"style"=>"width:800;height:200","onFocus"=>"expandirRama(this,'R07');"),"$kardexActual->medidasGenerales");
						} 
						echo "</td>";
						echo "</tr>";
					
					}else{
						
						echo "<tr style='display:none'>";					
						echo "<td width='220' class='fila1' colspan=3 align=center>Medidas generales";
						echo "<textarea name=txMedidas style='width:800;height:200' >";
						echo $kardexActual->medidasGenerales;
						echo "</textarea>";
						echo "</td>";
						echo "</tr>";
						
					}
					
					//Permisos para sondas, cateteres y drenes.
					$accion = $accionesPestana[$indicePestana.".R01"];					
					if(count($accion) > 0 and $accion->leer){
						
						//Sondas, cateteres y drenes editable
						if(!$accion->actualizar){
							
							$editar = "readonly";
						}
					
						//Sondas y cateteres
						echo "<tr>";						
						echo "<td width='220' class='fila1' align=center colspan=2>";
						echo "Sondas, cateteres y drenes";
		
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							crearCampo("2",'txtSondas',@$accionesPestana[$indicePestana.".R01"],array($editar=>$editar,"style"=>"width:100%;height:200","onFocus"=>"expandirRama(this,'R01');"),"$kardexActual->sondasCateteres");
						} 
						
						echo "</td>";
						echo "</tr>";
					
					}else
						{
						
						echo "<tr style='display:none'>";					
						echo "<td width='220' class='fila1' colspan=3 align=center>Sondas, cateteres y drenes";
						echo "<textarea name=txtSondas style='width:800;height:200' >";
						echo $kardexActual->sondasCateteres;
						echo "</textarea>";
						echo "</td>";
						echo "</tr>";
						
					}
					
					//Permisos para Cuidados enfermería
					$accion = $accionesPestana[$indicePestana.".R02"];				
					if(count($accion) > 0 and $accion->leer){
						
						//Cuidados enfermería editable
						if(!$accion->actualizar){
							
							$editar = "readonly";
						}
						
						//Cuidados enfermería
						echo "<tr>";
						echo "<td width='220' class='fila1' colspan=3 align=center>Cuidados de enfermer&iacute;a";
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							crearCampo("2",'txCuidados',@$accionesPestana[$indicePestana.".R02"],array($editar=>$editar,"style"=>"width:800;height:200","onFocus"=>"expandirRama(this,'R02');"),"$kardexActual->cuidadosEnfermeria");	
						} 
						echo "</td>";
						echo "</tr>";
					}else{
						
						echo "<tr style='display:none'>";					
						echo "<td width='220' class='fila1' colspan=3 align=center>Cuidados de enfermer&iacute;a";
						echo "<textarea name=txCuidados style='width:800;height:200' >";
						echo $kardexActual->cuidadosEnfermeria;
						echo "</textarea>";
						echo "</td>";
						echo "</tr>";
						
						
					}
					
					//Permisos para Cuidados enfermería
					$accion = $accionesPestana[$indicePestana.".R10"];			
					if(count($accion) > 0 and $accion->leer){
						
						//Observaciones generales editable
						if(!$accion->actualizar){
							
							$editar = "readonly";
						}
						
						//Observaciones generales
						echo "<tr>";
						echo "<td width='220' class='fila1' align=center>Observaciones";
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							crearCampo("2",'txObservaciones',@$accionesPestana[$indicePestana.".R10"],array($editar=>$editar,"style"=>"width:800;height:230"),"$kardexActual->observaciones\n$kardexActual->curaciones");
						} 
						echo "</td>";					
						echo "</tr>";
					}else{
						
						echo "<tr style='display:none'>";					
						echo "<td width='220' class='fila1' colspan=3 align=center>Observaciones";
						echo "<textarea name=txObservaciones style='width:800;height:200' >";
						echo $kardexActual->observaciones.$kardexActual->curaciones;
						echo "</textarea>";
						echo "</td>";
						echo "</tr>";
						
					}
					
					//Decisiones
					// echo "<tr>";
					// echo "<td colspan=2 width='".(270*$mult)."' class='fila1'>Decisiones";
					// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						// crearCampo("2",'txtConsentimientos',@$accionesPestana[$indicePestana.".R08"],array("style"=>"width:100%;height:140","onFocus"=>"expandirRama(this,'R08');"),"$kardexActual->consentimientos");
					// } else {
						// echo "<textarea name=txtConsentimientos style='width:100%;height:140' readonly>";
						// echo $kardexActual->consentimientos;
						// echo "</textarea>";
					// }
					// echo "</td>";
					// echo "</tr>";
					
					//Procedimientos
					// echo "<tr>";
					// echo "<td width='".(270*$mult)."' class='fila1'>Procedimientos";
					// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						// //crearCampo("2",'txProcedimientos',@$accionesPestana[$indicePestana.".R09"],array("style"=>"width:100%;height:140","onFocus"=>"expandirRama(this,'R09');"),"$kardexActual->procedimientos");
					// } else {
						// echo "<textarea name=txProcedimientos style='width:100%;height:140' readonly>$kardexActual->procedimientos</textarea>";
					// }
					// echo "</td>";					
					// echo "</tr>";					
					
					//Aislamientos
					// echo "<tr>";					
					// echo "<td width='270' class='fila1'>Aislamientos";
					// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						// crearCampo("2",'txAislamientos',@$accionesPestana[$indicePestana.".R03"],array("style"=>"width:100%;height:140","onFocus"=>"expandirRama(this,'R03');"),"$kardexActual->aislamientos");
					// } else {
						// echo "<textarea name=txAislamientos style='width:100%;height:140' readonly>";
						// echo $kardexActual->aislamientos;
						// echo "</textarea>";
					// }
					// echo "</td>";					
					// echo "</tr>";					
					
					// //Terapia respiratoria, fisica y rehabilitacion cardiaca
					//echo "<tr>";
					// echo "<td width='180' class='fila1'>";
					// echo "<label for='wcodmed'>Terapias";
					// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						// crearCampo("2",'txTerapia',@$accionesPestana[$indicePestana.".R05"],array("cols"=>"30","rows"=>"8","onFocus"=>"expandirRama(this,'R05');"),"$kardexActual->terapiaRespiratoria");
					// } else {
						// echo "<textarea name=txTerapia rows=8 cols=30 readonly>";
						// echo $kardexActual->terapiaRespiratoria;
						// echo "</textarea>";
					// }
					// echo "</td>";
					//echo "<tr>";
					
					//Curaciones
					// echo "<tr>";
					// echo "<td width='270' class='fila1'><label for='wcodmed'>Curaciones";
					// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						// crearCampo("2",'txtCuraciones',@$accionesPestana[$indicePestana.".R04"],array("style"=>"width:100%;height:140","onFocus"=>"expandirRama(this,'R04');"),"$kardexActual->curaciones");
					// } else {
						// echo "<textarea name=txtCuraciones style='width:100%;height:140' readonly>";
						// echo $kardexActual->curaciones;
						// echo "</textarea>";
					// }
					// echo "</td>";					
					// echo "</tr>";
										
					
					// //Cirugias pendientes
					// echo "<td width='180' class='fila1' rowspan='".$rowspanpend."'>Cirug&iacute;as pendientes";
					// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						// crearCampo("2",'txtCirugiasPendientes',@$accionesPestana[$indicePestana.".3"],array("cols"=>"30","rows"=>"$rowspend"),"$kardexActual->cirugiasPendientes");
					// } else {
						// echo "<textarea name=txtCirugiasPendientes rows=8 cols=30 readonly>";
						// echo $kardexActual->cirugiasPendientes;
						// echo "</textarea>";
					// }
					// echo "</td>";
					// echo "</tr>";
					
						
						//Interconsulta
						// echo "<td width='180' class='fila1'>";

						// echo "Interconsulta";
						
						// if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							// crearCampo("2",'txtInterconsulta',@$accionesPestana[$indicePestana.".R06"],array("cols"=>"30","rows"=>"8","onFocus"=>"expandirRama(this,'R06');"),"$kardexActual->interconsulta");	
						// } else {
							// echo "<textarea name=txtInterconsulta rows=8 cols=30 readonly>";
							// echo $kardexActual->interconsulta;
							// echo "</textarea>";
						// }
						// echo "</td>";

						 
					
						// echo "<input type=hidden name=txtInterconsulta value='".$kardexActual->interconsulta."'>";
						// echo "<input type=hidden name=txtSondas value='".$kardexActual->sondasCateteres."'>";
					
				echo "</table>";					
				echo "</div>";					
					
				}
				else{
					
				echo "<div>";	
				echo "<table>";	
				//Medidas generales	
				echo "<tr>";					
				echo "<td width='220' class='fila1' colspan=3 align=center>Medidas generales";
				echo "<textarea name=txMedidas style='width:800;height:200' readonly >";
				echo $kardexActual->medidasGenerales;
				echo "</textarea>";
				echo "</td>";
				echo "</tr>";
				
				//Sondas, cateteres y drenes.
				echo "<tr>";					
				echo "<td width='220' class='fila1' colspan=3 align=center>Sondas, cateteres y drenes";
				echo "<textarea name=txtSondas style='width:800;height:200' readonly >";
				echo $kardexActual->sondasCateteres;
				echo "</textarea>";
				echo "</td>";
				echo "</tr>";
				
				//Cuidados de enfermeria
				echo "<tr>";					
				echo "<td width='220' class='fila1' colspan=3 align=center>Cuidados de enfermer&iacute;a";
				echo "<textarea name=txCuidados style='width:800;height:200' readonly>";
				echo $kardexActual->cuidadosEnfermeria;
				echo "</textarea>";
				echo "</td>";
				echo "</tr>";
				
				//Observaciones
				echo "<tr>";					
				echo "<td width='220' class='fila1' colspan=3 align=center>Observaciones";
				echo "<textarea name=txObservaciones style='width:800;height:200' readonly>";
				echo $kardexActual->observaciones.$kardexActual->curaciones;
				echo "</textarea>";
				echo "</td>";
				echo "</tr>";
				
				echo "</table>";
				echo "<td>";
			
				echo "</table>";					
				echo "</div>";
				
				}
										
				
			}


			$indicePestana = "11";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-11'>";

				$accionesPestana = consultarAccionesPestana($indicePestana);

				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";

				$elementosActuales = 0;
				$colDetalle = array();
				$colArticulo = array();

				echo '<script> actualizaImpresion("'.$paciente->historiaClinica.'","'.$paciente->ingresoHistoriaClinica.'","'.$wfecha.'"); </script>';

				if($usuario->firmaElectronicamente && $kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					
					$esEditable = $kardexActual->editable;
					
					if( $esEditable ){
						$eventosQuitarTooltip = " onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'";	//Creo los eventos que quitan el tooltip si el kardex es editable
					}

					echo "<br>";
					
					echo "<h3>Medicamentos</h3>";
					

					/*********************************************************************************************************
					***************************** INICIO SECCION DE MEDICAMENTOS *********************************************/
					
					//echo "<div align=center>";
						
					//echo "<table>";
					// echo "<tr>";
					// echo "<td class=fila1 width=220px>Buscar medicamento</td>";
					// echo "<td class=fila2>";
					// //echo "<INPUT TYPE='text' NAME='wnombremedicamento' id='wnombremedicamento' SIZE=100 class='textoNormal' onBlur='this.value=\"\"'>";
					// crearCampo("1","wnombremedicamento",@$accionesPestana[$indicePestana.".1"],array("size"=>"100","class"=>"textoNormal","onBlur"=>"this.value=''"),"");
					// //echo "<input type='button' value='Movimiento de articulos' onclick='abrirMovimientoArticulos(\"N\");'>";
					// echo "</td>";
					// echo "</table>";
					// echo "</div>";
					
					
					//Nuevo buscador de medicamentos
					// echo "<tr>";
					// echo "<td class=fila2>";
					

					echo "<div class='fondoAmarillo' style='border: 1px solid #333333; width:130% !important; width:90%; height:110px;'>";
					echo "<table align='left' border='0' width='100%'>";
					echo "<tr class='fondoAmarillo'>";
					
					echo "<td align='left'>";
					
					echo "<table id='nuevoBuscadorImp'>";
					echo "<tr class='encabezadotabla' align='center'>";
					echo "<td width='100'>Grabar</td>";
					echo "<td width='250'>Medicamento(*)</td>";
					echo "<td>Presentaci&oacute;n(*)</td>";
					echo "<td width='100'>Dosis(*)</td>";
					echo "<td>Unidad de medida(*)</td>";					
					echo "<td width='100'>Frecuencia(*)</td>";
					echo "<td width='100'>Vía(*)</td>";
					echo "<td width='100'>Fecha y hora incio(*)</td>";
					echo "<td width='100' style='display:none'>Condición</td>";
					echo "<td width='100' style='display:none'>Días tto.</td>";
					echo "<td width='100'>Cantidad total de dosis</td>";
					echo "<td width='100'>Observaciones</td>";
					echo "<td width='100'>Grabar</td>";
					echo "</tr>";
					
					echo "<tr align='center'>";
					
					// Boton para el submit
					echo "<td><input type='button' name='btnGrabar5' value='OK' onClick='eleccionMedicamentoAlta()' /></td>";

					// Nombre
					echo "<td>";
					//echo "<INPUT TYPE='text' NAME='wnombremedicamento' id='wnombremedicamento' SIZE=100 class='textoNormal' onBlur='this.value=\"\"'>";
					// Llama a la función autocompletarParaBusqueMedicamentosPorFamiliaAlta en ordenes.js
					crearCampo("1","wnombrefamiliaimp",@$accionesPestana[$indicePestana.".1"],array( "size"=>"50","class"=>"textoNormal"),"");

					//echo "<input type='button' value='Movimiento de articulos' onclick='abrirMovimientoArticulos(\"N\");'>";
					echo "</td>";
					
					// Presentacion
					echo "<td>";
					crearCampo("6","wpresentacionimp",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"","onChange"=>"filtrarMedicamentosPorCampo('presentacion','imp')")," &nbsp; &nbsp; &nbsp; ");
					echo "</td>";
					
					// Dosis
					echo "<td>";
					//crearCampo("1","wdosisfamilia",@$accionesPestana[$indicePestana.".1"],array("size"=>"3","class"=>"textoNormal","onChange"=>"eleccionMedicamento(this.value)","onKeyPress"=>"eleccionPreviaMedicamento(this, event)"),"");
					crearCampo("1","wdosisfamiliaimp",@$accionesPestana[$indicePestana.".1"],array("onKeyPress"=>"return validarEntradaDecimal(event);","size"=>"3","class"=>"textoNormal"),"");
					echo "</td>";

					// Unidad de medida
					echo "<td>";
					crearCampo("6","wunidadimp",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"","onChange"=>"filtrarMedicamentosPorCampo('unidad','imp')")," &nbsp; &nbsp; &nbsp; ");
					echo "</td>";					

					//Frecuencia
					$equivalenciaPeriodicidad = 0;
					echo "<td $eventosQuitarTooltip>";
					$opcionesSeleccion = "<option value='' selected>Seleccione</option>";
					foreach ($colPeriodicidades as $periodicidad){
						$opcionesSeleccion .= "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
					}
					
					// Se adiciona la opción de horario especial
					//$opcionesSeleccion .= "<option value='H.E.'>H.E.</option>";
					crearCampo("6","wfrecuenciaimp",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>""),"$opcionesSeleccion");
					echo "<input type='hidden' id='wequdosisimp$articulo->tipoProtocolo$contArticulos' value='$equivalenciaPeriodicidad'>";
					echo "</td>";
					
					//Via administracion
					// 2012-11-02
					// Se comenta porque ya se va a generar dinamicamente segun la familia seleccionada
					// Ya no se muestran todas las vias sino las asociadas a la familia
					// echo "<td $eventosQuitarTooltip>";
					// $opcionesSeleccion = "<option value='' selected>Seleccione</option>";
					// foreach ($colVias as $via){
						// $opcionesSeleccion .= "<option value='".$via->codigo."'>$via->descripcion</option>";
					// }
					// 
					
					// crearCampo("6","wadministracionimp",@$accionesPestana[$indicePestana."1"],array("class"=>"seleccion","onBlur"=>""),"$opcionesSeleccion");
					
					
					echo "<td $eventosQuitarTooltip>";
					crearCampo("6","wadministracionimp",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"")," &nbsp; &nbsp; &nbsp; ");
					echo "</td>";

					//Fecha y hora inicio
					// Encuentro la hora de inicio par siguiente
					$horParActInicial = floor(date("H")/2) * 2;
					$horIniAdmInicial = "$horParActInicial:00:00";
					$fecIniAdmInicial = strtotime(date("Y-m-d $horIniAdmInicial")) + (60*60*2);

					$fecIniAdmInicial = date("Y-m-d \a \l\a\s:H:i", $fecIniAdmInicial);

					echo "<td $eventosQuitarTooltip>";

					echo "<INPUT TYPE='hidden' NAME='whfinicioN888' id='whfinicioN888' SIZE=22 readonly class='campo2' value='$fecIniAdmInicial'>";

					echo "<INPUT TYPE='text' NAME='wfinicioaplicacionimp' id='wfinicioaplicacionimp' SIZE=25 readonly class='campo2' value='$fecIniAdmInicial'>";
					crearCampo("3","btnFechaimpN888",@$accionesPestana[$indicePestana.".N888"],array("onClick"=>"calendario6(888,'N');"),"*");
					//				echo "<input type='button' id='btnFecha$articulo->tipoProtocolo$contArticulos' onclick='calendario($contArticulos,\"$articulo->tipoProtocolo\");' value='*'>";

					echo "</td>";

					//Condicion de suministro
					echo "<td onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );' style='display:none'>";
					$opcionesSeleccion = "<option value='' selected>Seleccione</option>";
					foreach ($colCondicionesSuministro as $condicion){
						$opcionesSeleccion .= "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
					}
					crearCampo("6","wcondicionsumimp",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>""),"$opcionesSeleccion");
					echo "</td>";
					
					//Dias tratamiento, debe mostrarse en un alt la fecha de terminación y los dias restantes
					$diasFaltantes = 0;
					$fechaFinal = "-";

					echo "<td $eventosQuitarTooltip style='display:none'>";
					crearCampo("1","wdiastratamientoimp",@$accionesPestana[$indicePestana.".1"],array("size"=>"3","maxlength"=>"3","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onKeyUp"=>"inhabilitarDosisMaxima( this,'', '' );"),"");
					echo "</td>";

					//Dosis máximas
					echo "<td $eventosQuitarTooltip>";
					crearCampo("1","wcantidadaltaimp",@$accionesPestana[$indicePestana.".1"],array("onKeyPress"=>"return validarEntradaEntera(event);","size"=>"14","class"=>"campo2"),"");

					echo "<INPUT TYPE='hidden' NAME='wdosismaximaimp' id='wdosismaximaimp' value=''>";
					
					echo "</td>";

					//Observaciones
					echo "<td $eventosQuitarTooltip>";
					crearCampo("2","wtxtobservasionesimp",@$accionesPestana[$indicePestana.".1"],array("cols"=>"40","rows"=>"2","onKeyPress"=>"return validarEntradaAlfabetica(event);"),"");
					echo "</td>";
					
					// Boton para el submit
					echo "<td><input type='button' name='btnGrabar6' value='OK' onClick='eleccionMedicamentoAlta()' /></td>";
					echo "</tr>";
					echo "</table>";

					echo "</td>";
					echo "</tr>";
					echo "</table>";
					echo "</div><br />";
					
				}
					
				//crearCampo("3","",@$accionesPestana[$indicePestana.".1"],array("onClick"=>"abrirMovimientoArticulos('N');"),"Ordenar...");
//						echo "<input type='button' value='Ordenar...' onclick='abrirMovimientoArticulos(\"N\");'>";
					
//				}

				//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
				realizarMovimientosArticulosAlta($kardexActual, $paciente, $esFechaActual, $wfecha, $fechaGrabacion, "%", $elementosActualesAlta, $colDetalleAlta, $elementosActualesAltaAux, $colDetalleAltaAux);
				
				
				
				
				//				var_dump($accionesPestana);
				//Despliega la vista de la tabla de articulos para el protocolo normal
				vista_desplegarListaArticulosAlta($colDetalleAltaAux,$elementosActualesAltaAux,"N",$kardexActual->editable,$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro,$accionesPestana,$indicePestana);
				
				/************************************** FIN SECCION DE MEDICAMENTOS **************************************
				**********************************************************************************************************/
				
				
				/*********************************************************************************************************
				***************************** INICIO SECCION DE EXAMENES Y PROCEDIMIENTOS ********************************/
				
				echo "<hr>";
				echo "<h3>Otras Ordenes</h3>";

				echo "<div id='cntExamenesImp'>";
				
				if( $usuario->firmaElectronicamente && $kardexActual->editable && $vecPestanaGrabacion[$indicePestana])
				{
					
					$optionsSelTipoServicio = "<option value='%' selected>Todos</option>";
					
					// $tiposDeAyudaDxs = tiposAyudasDiagnosticas( $conex, $wempresa );
					$tiposDeAyudaDxs = tiposAyudasDiagnosticas( $conex, $wempresa, $paciente->servicioActual );
					
					$tiposDeAyudaDxs = explode( "|", $tiposDeAyudaDxs );
					
					foreach( $tiposDeAyudaDxs as $key => $value ){
						 list( $codigo, $descripcion ) = explode( "-", $value );
						 $optionsSelTipoServicio .= "<option value='$codigo'>$descripcion</option>";
					}
					
					$listaProtocolos = generarListaProtocolos('wprotocolo_ayd',$usuario->codigo,$paciente->servicioActual);
					
					// echo "<table align='center'>";
					// echo "<tr>";
					// echo "<td>";
					
					echo "<div id='cntExamenesImp' class='fondoAmarillo' style='border: 1px solid #333333; width:70% !important; width:50%; height:70px;'>";
					echo "<table align='center'>";
					echo "<tr class='fondoAmarillo'>";
					echo "<td>";
						
						echo "<table align='center'>";
						echo "<tr>";
						echo "<td colspan=4 align=center class='encabezadoTabla'>";
						echo "<b>Tipo de orden</b>";
						echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>";
						crearCampo("6","wselTipoServicioImp",@$accionesPestana[$indicePestana.".1"],array("class"=>"textoNormal", "onChange"=>"autocompletarParaConsultaDiagnosticasAlta();"),$optionsSelTipoServicio);
						echo "</td>";
						echo "</tr>";
						echo "</table>";
					echo "</td>";

					echo "<td>";
						echo "<table align='center'>";
						echo "<tr>";
						echo "<td colspan=4 align=center  class='encabezadoTabla'>";
						echo "<b>Ayuda o procedimiento</b>";
						echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td></td>";
						echo "<td>";
						// crearCampo("1","wnomprocimp",@$accionesPestana[$indicePestana.".1"],array("size"=>"60","class"=>"textoNormal","onBlur"=>"this.value=''"),"");
						crearCampo("1","wnomprocimp",@$accionesPestana[$indicePestana.".1"],array("size"=>"60","class"=>"textoNormal"),"");
						
						if($lenguajeAmericas == "on"){
							echo "<td valign='top'> &nbsp; <img id='imgAddExamImp' src='../../images/medical/hce/add_blue.png' width='14' height='14' border='0' onclick='agregarNuevoExamenAlta();'> &nbsp; </td>";
						}
	//					echo "<INPUT TYPE='text' NAME='wnomproc' id='wnomproc' SIZE=60 class='textoNormal' onBlur='this.value=\"\"'>";
						//echo "&nbsp;|&nbsp;";
						//echo "<input type='button' value='Ordenar...' onclick='movimientoExamenes();'>";
						echo "</td>";
						echo "</tr>";
						echo "</table>";
					
					echo "</td>";
					echo "</tr>";
					echo "</table>";
					echo "</div>";
					
//					echo "<div align='center'><input type='button' onClick='movimientoExamenes();' value='Ordenar...'></div>";
					echo "<br/>";
				}

				$detalt = true;
				//Examenes de historia clinica
				$datosAdicionalesImp = Array();
				$colExamenesHistoriaImp = consultarOrdenesHCE($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$datosAdicionalesImp,$detalt, "");
				$cuentaExamenesHistoriaImp = count($colExamenesHistoriaImp);
				$contExamenesImp = $auxContExamenes;
				$contProtocolosExamenesImp = 0;
				
				$contOrdenesImp = 0;
				
				
				echo "<table id='examPendientesImp'>";
				echo "<tr class='encabezadoTabla'>";
				echo "<td style='display:none'><b>Orden</b></td>";
				echo "<td><b>Quitar</b></td>";
				echo "<td align=center><b>Imprimir<br><input type=checkbox onclick='marcar_todos_proc_alta()' id='marcarTodosProcAlta' title='Marcar Todos'></b></td>";
				echo "<td><b>Fecha</b></td>";
				echo "<td><b>Tipo de orden</b></td>";
				echo "<td><b>Procedimiento</b></td>";
				echo "<td><b>Justificación</b></td>";
				echo "<td style='display:none'><b>Estado</b></td>";
				echo "</tr>";
				//echo "</table>";
				///////////////////////////////////////////////////////////////////

				echo "<tbody id='encabezadoExamenesImp'>";
				echo "</tbody>";

				$centroCostosExamenes = "";
				$consecutivoOrdenExamen = "";

				$caso = "";
				$pasoPorAqui = false;
				$huboOrdenes = false;
				foreach ($colExamenesHistoriaImp as $examen){
//					if($centroCostosExamenes != $examen->tipoDeOrden && $consecutivoOrdenExamen != $examen->numeroDeOrden){
					if( $centroCostosExamenes != $examen->tipoDeOrden ){
						$caso = "1";
					}
					if($centroCostosExamenes == $examen->tipoDeOrden && $consecutivoOrdenExamen == $examen->numeroDeOrden){
						$caso = "4";
					}
					if($centroCostosExamenes == $examen->tipoDeOrden && $consecutivoOrdenExamen != $examen->numeroDeOrden){
						$caso = "3";
					}
					
					/************************************************************************************************************
					 * Queda así para el switch:
					 * - Opcion 1: Crea el encabezado del Servicio
					 * - Opcion 2: Crea la orden
					 * - Cualquier otra opción no hace nada
					 ************************************************************************************************************/
					//Inicio switch
					switch ($caso){
						
						case '1':
							
							$contOrdenesImp = 0;
							
							if($contExamenesImp != 0){
								
								if( $pasoPorAqui ){
									//Cierre de tabla
									//echo "</tbody>";
									//echo "</table>";
								}
								
								if( $huboOrdenes ){
									//Cierre de orden
									echo "</tr>";
									//echo "</div>";
								}			
								
								//Cierro centro de costos
								//echo "</span>";
								echo "</tbody>";
								//echo "</table>";
								//echo "</div>";
							}
							$pasoPorAqui = false;
							$huboOrdenes = false;

							echo "<div id='".$examen->tipoDeOrden."Imp' style='display:block'>";

							//Crear tabla
							if( true ){

								echo "<tbody id='detExamenes".$examen->tipoDeOrden."Imp'>";

								$pasoPorAqui = false;
							}
							
							
						case '2':
						case '3':
							$fechaHoy = date("Y-m-d");
							
							//Verifica si el estado del examen se puede mostrar.
							$westado_ordenes = estados_ordenes($wemp_pmla, $wbasedato, $conex, $examen->estadoExamen );
							
							//$datosAdicionalesImp
							if( $westado_ordenes == 'on' || $examen->fechaARealizar == $fechaHoy ){
								
								//Cierre de tabla
								if( $caso != 1 ){
									
									if( $pasoPorAqui ){
										echo "</tbody>";
										//echo "</table>";
									}

									//Cierre de orden
									if($huboOrdenes){
										
										echo "</tr>";
									}
								}
								
								$huboOrdenes = true;
								//Crear orden

								if($contExamenesImp % 2 == 0){
									echo "<tr align=center id='trExImp$contExamenesImp' class=fila1>";
								} else {
									echo "<tr align=center id='trExImp$contExamenesImp' class=fila2>";
								}
								
								if($contOrdenesImp % 2 == 0){
									echo "<td style='display:none' id=delImp$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionalesImp[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila1>";
								}
								else{
									echo "<td style='display:none' id=delImp$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionalesImp[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila2>";
								}
								$contOrdenesImp++;
								
								if($kardexActual->editable && $vecPestanaGrabacion[4]){
									
									$puedeEliminar = true;									
									if( $usuario->permisosPestanas[4]['modifica'] ){
										if( $usuario->codigo != $examen->creadorOrden ){
											$puedeEliminar = false;
										}
									}
									
									// if( $puedeEliminar ){
										// crearCampo("4","",@$accionesPestana["4.3"],array("onClick"=>"cancelarOrden('$examen->tipoDeOrden','$examen->numeroDeOrden');"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' />");
									// }
								}
								
								//echo "<a href='#null' onclick=intercalarElemento(\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\");>";
								echo "<b>&nbsp;&nbsp;&nbsp;<u>Orden Nro. ".$examen->numeroDeOrden."</u></b>";
								//echo "</a>";
								echo "<div id=\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."imp\" class='fila2'>";
								
								echo "<div style='display:none'>";
								echo "<br>Observaciones actuales: <br>";
								echo "<textarea id='observacionesimp$contExamenesImp' rows='4' cols='80' readonly>$examen->observacionesOrden</textarea>";
								
								/*echo "<br><br>Agregar observaciones: <br>";
								crearCampo("2","wtxtobsexamen$examen->tipoDeOrden$examen->numeroDeOrden",@$accionesPestana[$indicePestana.".4"],array("cols"=>"80","rows"=>"4","onKeyPress"=>"return validarEntradaAlfabetica(event);"),"");
								*/
								echo "</div>";
								
								echo "</div>";
								echo "</td>";
								
								$consecutivoOrdenExamen = $examen->numeroDeOrden;
							}

						break;
						
						case '4': break;

					}//Fin switch
					
					//Verifica si el estado del examen se puede mostrar.
					$westado_ordenes = estados_ordenes($wemp_pmla, $wbasedato, $conex, $examen->estadoExamen );
					
					//Crear tabla
					if( false && ($caso == 1 || $caso == 3) && $westado_ordenes == 'on' ){
						
						//echo "<table align='center'>";
						echo "<tr align='center'>";
	
						if($kardexActual->editable && $vecPestanaGrabacion[4]){
							echo "<td class='encabezadoTabla'>";
							echo "Imprimir";
							echo "</td>";
						}


						echo "<tbody id='detExamenes".$examen->tipoDeOrden."Imp'>";
						
						$pasoPorAqui = true;
					}
					
					//Verifica si el estado del examen se puede mostrar.
					$westado_ordenes = estados_ordenes($wemp_pmla, $wbasedato, $conex, $examen->estadoExamen );
					
					if( false || $westado_ordenes == 'on' ){
						
						if( !($caso == 1 || $caso == 3) ){
							
							//Examen
							if($contExamenesImp % 2 == 0){
								echo "<tr id='trExImp$contExamenesImp' class='fila1' align='center'>";
							} else {
								echo "<tr id='trExImp$contExamenesImp' class='fila2' align='center'>";
							}
						}
	
						if($kardexActual->editable && $vecPestanaGrabacion[4]){
							echo "<td align=center>";
							
							$puedeEliminar = true;									
							if( $usuario->permisosPestanas[4]['modifica'] ){
								if( $usuario->codigo != $examen->creadorItem ){
									$puedeEliminar = false;
								}
							}

							$chkimpexamen = "checked";
							if($examen->imprimirExamen!='on')
								 $chkimpexamen = "";

							/*crearCampo("5","wchkimpexamen$contExamenesImp",@$accionesPestana["4.5"],array($chkimpexamen=>"","style"=>"display:none","onChange"=>"marcarImpresionExamen(this,'".$examen->tipoDeOrden."','".$examen->numeroDeOrden."','".$examen->codigoExamen."','".$examen->fechaARealizar."');"),"");*/

							//echo "<input type='hidden' id='wexamenalta$contExamenesImp' name='wexamenalta$contExamenesImp' value='on'>";
							
							echo "<script> marcarCambio(\"4\",\"$contExamenesImp\"); </script>";
							
							// echo "<div id='imgDelImp".$contExamenesImp."' style='display:inline' title='Click para imprimir este procedimiento'><img onClick='quitarFilaAltaExamen(\"trExImp".$contExamenesImp."\",\"wchkimpexamen$contExamenesImp\"); marcarImpresionExamen(\"quitar\",\"".$examen->tipoDeOrden."\",\"".$examen->numeroDeOrden."\",\"".$examen->codigoExamen."\",\"".$examen->fechaARealizar."\",\"\");' src='../../images/medical/root/borrar.png' width='17' height='17' border='0'/> &nbsp;&nbsp; </div>";
							
							//echo "<div id='imgDelImp".$contExamenesImp."' style='display:inline' title='Click para imprimir este procedimiento'><img onClick='quitarExamen(\"$contExamenesImp\",\"Imp\",\"off\");' src='../../images/medical/root/borrar.png' width='17' height='17' border='0'/> &nbsp;&nbsp; </div>";
							
							echo "</td>";
							echo "<td>";
							echo "<div style='display:inline'><img width='18' height='18' border='0' src='../../images/medical/hce/icono_imprimir.png'><br>";							
							crearCampo("5","wchkimpexamen$contExamenesImp",@$accionesPestana[$indicePestana.".N22"],array($chkimpexamen=>"","style"=>"display:inline", "OnClick"=>"imprimirOrdenAlta('$paciente->historiaClinica','$paciente->ingresoHistoriaClinica','$examen->tipoDeOrden','$examen->numeroDeOrden','$examen->nroItem','wchkimpexamen$contExamenesImp')"),"");
							echo "</div>";
							echo "</td>";
						}
	
						//Fecha de solicitado examen
						echo "<td>";
						if($kardexActual->editable && $vecPestanaGrabacion[4]){
							echo "<INPUT TYPE='text' id='wfsolimp$contExamenesImp' NAME='wfsol$contExamenesImp' SIZE=10 readonly class='campo2' cod_examen='$examen->codigoExamen' value='$examen->fechaARealizar' onChange='marcarCambio(\"4\",\"$contExamenesImp\");'>";
							crearCampo("3","btnFechaSolImp$contExamenesImp",@$accionesPestana[$indicePestana.".N23"],array("height"=>"20","onClick"=>"calendario3imp($contExamenesImp);"),"*");
	//						echo "<INPUT TYPE='button' id='btnFechaSol$contExamenesImp' onClick='calendario3($contExamenesImp);' height=20 value='*'>";
							
							echo "<input type='hidden' name='wfsolori$contExamenesImp' id='wfsolori$contExamenesImp' value='$examen->fechaARealizar' />";
							
	
						} else {
							echo "$examen->fechaARealizar&nbsp;";
						}
						echo "</td>";

						// Columna de tipo de servicio
						echo "<td>".$examen->nombreCentroCostos."</td>";	
						
						//Columna de datos
						echo "<td>";
						echo "$examen->nombreExamen";
						
						if(isset($examen->protocoloPreparacion) && !empty($examen->protocoloPreparacion)){
							$contenido = str_replace("\r\n","<br>",$examen->protocoloPreparacion);
							
							echo "<span id='imp4-$contProtocolosExamenesImp' title=' - $contenido'>";
							echo "<img src='../../images/medical/root/info.png' border='0' />";
							echo "</span>";
							$contProtocolosExamenesImp++;
						}
						
						//Ocultos
						if($wfecha == $examen->fecha){
							echo "<input type=hidden name='wmodificado4$contExamenesImp' id='wmodificado4$contExamenesImp' value='S'>";	
						} else {
							echo "<input type=hidden name='wmodificado4$contExamenesImp' id='wmodificado4$contExamenesImp' value='N'>";
						}					
						echo "<input type=hidden id='wnmexamenimp$contExamenesImp' value='$examen->nombreExamen'>";
						echo "<input type=hidden id='hexccoimp$contExamenesImp' value='$examen->tipoDeOrden'>";
						echo "<input type=hidden id='hexcodimp$contExamenesImp' value='$examen->codigoExamen'>";
						echo "<input type=hidden id='hexconsimp$contExamenesImp' value='$examen->numeroDeOrden'>";
						echo "<input type=hidden id='hexnroitemimp$contExamenesImp' value='$examen->nroItem'>";

						echo "<input type=hidden id='wnmexamen$contExamenesImp' value='$examen->nombreExamen'>";
						echo "<input type=hidden id='hexcco$contExamenesImp' value='$examen->tipoDeOrden'>";
						echo "<input type=hidden id='hexcod$contExamenesImp' value='$examen->codigoExamen'>";
						echo "<input type=hidden id='hexcons$contExamenesImp' value='$examen->numeroDeOrden'>";
						echo "<input type=hidden id='hexnroitem$contExamenesImp' value='$examen->nroItem'>";

						echo "<input type=hidden id='wtxtjustexamen$contExamenesImp' value='$examen->nroItem'>";
						
						echo "</td>";
	
						//Justificacion
						echo "<td>";
						crearCampo("2","wtxtjustexamenimp$contExamenesImp",@$accionesPestana["4.6"],array("cols"=>"40","rows"=>"2","onChange"=>"marcarCambio('4','$contExamenesImp');"),"$examen->justificacion");
						echo "<input type='hidden' name='wtxtjustexamenori$contExamenesImp' id='wtxtjustexamenori$contExamenesImp' value='$examen->justificacion' />";
						echo "</td>";
	//					echo "<td><textarea id='wtxtjustexamen$contExamenesImp' rows='2' cols='40' onChange='marcarCambio(\"4\",\"$contExamenesImp\");'>$examen->justificacion</textarea></td>";
						
						//Resultado
						/*echo "<td style='display:none'>";
						crearCampo("2","wtxtobsexamen$contExamenesImp",@$accionesPestana[4.".7"],array("cols"=>"40","rows"=>"2","readonly"=>"readonly","style"=>"display:none"),"$examen->resultadoExamen");*/
						echo "</td>";
	//					echo "<td><textarea id='wtxtobsexamen$contExamenesImp' rows='2' cols='40' readonly>$examen->resultadoExamen</textarea></td>";
							
						//Estado del examen
						if($examen->tipoDeOrden == $codigoAyudaHospitalaria){
							echo "<td style='display:none'>";
							$opcionesSeleccion = "";
							/*
							foreach ($colEstadosExamenLab as $estadoExamen){
								if($estadoExamen->codigo == $examen->estadoExamen){
									$opcionesSeleccion .= "<option value='".$estadoExamen->codigo."' selected>$estadoExamen->descripcion</option>";
								} else {
									$opcionesSeleccion .= "<option value='".$estadoExamen->codigo."'>$estadoExamen->descripcion</option>";
								}
							}
							*/
							
							foreach ($consultarEstadosAyudasDx as $estadoExamen){
								if($examen->estadoExamen!=$estadoExamen->codigo)
									$opcionesSeleccion .= "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
								else
									$opcionesSeleccion .= "<option value='$estadoExamen->codigo' selected>$estadoExamen->descripcion</option>";
							}
							
							crearCampo("6","westadoexamenimp$contExamenesImp",@$accionesPestana["4.9"],array("class"=>"campo2","onChange"=>"marcarCambio('4','$contExamenesImp');"),"$opcionesSeleccion");
//							echo "<select name='westadoexamen$contExamenesImp' id='westadoexamen$contExamenesImp' class='campo2' onChange='marcarCambio(\"4\",\"$contExamenesImp\");'>";
//
//							foreach ($colEstadosExamenLab as $estadoExamen){
//								if($estadoExamen->codigo == $examen->estadoExamen){
//									echo "<option value='".$estadoExamen->codigo."' selected>$estadoExamen->descripcion</option>";
//								}else{
//									echo "<option value='".$estadoExamen->codigo."'>$estadoExamen->descripcion</option>";
//								}
//							}
//							echo "</select>";
							echo "</td>";						
						} else {
							echo "<td style='display:none' class=fondoAmarillo>";
							// 2012-07-13
							// Si el rol puede modificar estado del examen
							if(puedeCambiarEstado()) {

								$estadoActual = $examen->estadoExamen;
								$descripcionEstadoActual = consultarDescripcionEstado($estadoActual);

								$opcionesSeleccion = "";
								
								/*
								$opcionesSeleccion .= "<option value='".$estadoActual."' selected>$descripcionEstadoActual</option>";
								foreach ($colEstadosExamen as $estadoExamen){
									if($estadoExamen->codigo != $estadoActual){
										$opcionesSeleccion .= "<option value='".$estadoExamen->codigo."'>$estadoExamen->descripcion</option>";
									}
								}
								*/
								
								foreach ($consultarEstadosAyudasDx as $estadoExamen){
									if($estadoExamen->codigo!=$estadoActual)
										$opcionesSeleccion .= "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
									else
										$opcionesSeleccion .= "<option value='$estadoExamen->codigo' selected>$estadoExamen->descripcion</option>";
								}
								
								crearCampo("6","westadoexamenimp$contExamenesImp",@$accionesPestana["4.9"],array("class"=>"campo2","onChange"=>"marcarCambio('4','$contExamenesImp');"),"$opcionesSeleccion");
							} else {
								echo "<input type='hidden' name='westadoexamenimp$contExamenesImp' id='westadoexamenimp$contExamenesImp' value='$examen->estadoExamen'>";
								echo $examen->estadoExamen;
							}
							echo "</td>";
						}
						echo "</tr>";
						
						$contExamenesImp++;
					}
					
					//Fin filas
//					$consecutivoOrdenExamen = $examen->numeroDeOrden;
					$centroCostosExamenes = $examen->tipoDeOrden; 
//					$contExamenesImp++;
					
				} //FIN FOREACH $colExamenesHistoriaImp as $examen

				// echo "<input type='HIDDEN' name='cuentaExamenes' id='cuentaExamenes' value='".(count($colExamenesHistoria)+$contExamenesImp)."'/>";
				
				if($cuentaExamenesHistoriaImp != 0){
					
					if( $pasoPorAqui ){
						//Cierre de tabla
						echo "</tbody>";
						//echo "</table>";
					}

					
					if( $huboOrdenes ){
						//Cierre de orden
						echo "</span>";
						echo "</div>";
					}			

					//Cierro centro de costos
//					echo "</span>";
					//echo "</table>";
					echo "</div>";
					echo "</div>";
				}
				
				echo "</table>";

				echo "</div>";
				
				echo "<br>";
				
				/******************************* FIN SECCION DE EXAMENES Y PROCEDIMIENTOS ********************************
				**********************************************************************************************************/
				
				/********************************************************************************************************
				******************************* INICIO SECCION DE INDICACIONES AL EGRESO ********************************/
				
				$qind = " SELECT Karegr
							FROM ".$wbasedato."_000053
						   WHERE Karhis = '".$paciente->historiaClinica."'
							 AND Karing = '".$paciente->ingresoHistoriaClinica."'
						   ORDER BY Fecha_data DESC ";

				$resind = mysql_query($qind, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qind . " - " . mysql_error());
				$rowind = mysql_fetch_array($resind);
				
				echo "<hr>";
				echo "<h3>Indicaciones al egreso</h3>";
				crearCampo("2","windicaciones",@$accionesPestana[$indicePestana.".N24"],array("cols"=>"100","rows"=>"7"),$rowind['Karegr']);
				//echo "<textarea name='windicaciones' id='windicaciones' cols='100' rows='7'>".$rowind['Karegr']."</textarea>";
				
				echo "<br>";
				echo "<br>";
				
				/********************************* FIN SECCION DE INDICACIONES AL EGRESO *********************************
				**********************************************************************************************************/
				
				
				echo "<br /><div align='center'><input type=button value='Enviar a Centro de Impresi&oacute;n' onclick='grabarKardex(\"cenimpalt\")'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=button value='Imprimir' onclick='grabarKardex(\"impalt\")'></div>";

				echo "<div id='linkcenimp' style='display:none;'></div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				
				
				echo "</div>";
			}

			echo "<input type='HIDDEN' name='cuentaExamenes' id='cuentaExamenes' value='".(count($colExamenesHistoria)+$contExamenesImp)."'/>";
		
			echo "<center>";
			if($kardexActual->editable)
			{				
				echo "<hr>";
				
				if($kardexActual->editable){
					echo "<div align=center>";
					echo "<table>";
					if($usuario->firmaElectronicamente){
						echo "<tr>";
						echo "<td height=30 class='fila2'> &nbsp; <input type='checkbox' name='wigualmanejo' id='wigualmanejo'> Igual manejo &nbsp; &nbsp; </td>";
						echo "<td height=30 class='fila1'> &nbsp; &nbsp; Firma digital &nbsp; </td>";
						echo "<td height=30 class='fila2'><input type='password' name='pswFirma' size=40 maxlength=80 id='pswFirma' value='' class=tipo3 onKeyUp='validarFirmaDigitalHCE();'></td>";
						echo "<td id='tdEstadoFirma' height=30 width='150' class='fila1'>  </td>";
						echo "<td height=30 class='fila2' colspan=3>";

						echo "<div style='display:none'><input type='checkbox' name='wconfdisp' id='wconfdisp' onClick='marcarKardexConfirmado();' $confirmaAutomaticamente disabled='disabled'>&nbsp;|&nbsp;</div>";

						echo "<div style='display:none' id='btnGrabar1' onclick='grabarKardex();' style='cursor:pointer;'><img src='/matrix/images/medical/hce/ok.png'></div>";
						//crearCampo("2","windicaciones",@$accionesPestana[$indicePestana.".N24"],array("cols"=>"100","rows"=>"7"),$rowind['Karegr']);
						echo "<div id='btnGrabarAux' style='cursor:pointer;'><img src='/matrix/images/medical/hce/ok.png' onclick='validarFirmaDigitalVaci()'></div>";					

						echo "&nbsp;</td>";
						echo "</tr>";
						echo "</table>";
						echo "</div>";
						echo "<div id='btnCerrarVentana'><br /><input type=button value='Salir sin grabar' onclick='salir_sin_grabar();'><br/></div>";
					} else {

						echo "<tr>";
						echo "<td height=30>";						
						if(true || $kardexActual->confirmado == "on"){
							echo "<input type='checkbox' name='wconfdisp' id='wconfdisp' onClick=\" document.getElementById('wcconf').checked = this.checked; document.getElementById('wcconf').onclick();\" $confirmaAutomaticamente style='display:none'>";
						}
						else{
							echo "<input type='checkbox' name='wconfdisp' id='wconfdisp' onClick=\" document.getElementById('wcconf').checked = this.checked; document.getElementById('wcconf').onclick();\">";
						}
						
						echo "<div id='btnGrabar1' align=center onclick='grabarKardex();' style='cursor:pointer;'><img src='/matrix/images/medical/hce/ok.png'></div>";
						echo "<input type='hidden' id='con_firma' value=''>";
						echo "&nbsp;</td>";
						echo "</tr>";
						echo "</table>";
						echo "</div>";
						echo "<div id='btnCerrarVentana'><br /><input type=button value='Salir sin grabar' onclick='salir_sin_grabar();'><br/></div>";
					} 
				}
			} 
			else 
			{
				if( isset($matrix) )
				{
					echo "<br/><input type=button value='Regresar' onclick='salir_sin_grabar();' ><br/>";
				}
				else
				{
					// echo "<br/><input type=button value='Salir sin grabar' onclick='salir_sin_grabar();' ><br/>";
					echo "<br/><input type=button value='Salir sin grabar' onclick='salir_sin_grabar();' ><br/>";
				}
			}
// 			echo "<input type='button' value='Regresar' id='regresar' onclick='inicio();'>";
			echo "</center>";
			echo "</div>";
			
			echo "</br>";
			echo "</br>";
			
			echo "<div id='dvAuxProcedimientosAgrupados' style='display:none'></div>";
			
			/************************************************************************************************************
			 * Modal filtro antibiotico
			 ************************************************************************************************************/
			echo "<div id='dvFiltroAntibiotico' style='display:none'>";
			echo "<center class='fondoAmarillo'><b>Seleccione la opci&oacute;n indicada para el manejo</b></center>";
			echo "<br>";
			echo "<center><b id='bTituloFiltroAntibiotico'>Aquí va el articulo</b></center>";
			echo "<br>";
			echo "<center>Frecuencia: <b id='bFrecuencia'>Aquí va la frecuencia</b></center>";
			echo "<center>Condici&oacute;n: <b id='bCondicion'>Aquí va la condición</b></center>";
			echo "<br>";
			echo "<table align='center'>";
			

			echo "<tr class='encabezadoTabla'>";
			echo "<td colspan='2'>Filtro antibi&oacute;tico</td>";
			echo "<td align='center'>D&iacute;as de<br>tratamiento</td>";
			echo "<td align='center'>Dosis m&aacute;xima</td>";
			echo "</tr>";

			echo "<tr>";
			echo "<td class='fila1'><input type='radio' name='ckFiltroAntibiotico' id='ckFiltroProfilaxis'></td>";
			echo "<td class='fila1'>Profilaxis</td>";
			echo "<td align='center' rowspan='2' class='fila2'><input type='text' id='inFiltroDtto' style='width:75px' onKeyPress='return validarEntradaEntera(event);'></td>";
			echo "<td align='center' rowspan='2' class='fila2'><input type='text' id='inFiltroDmax' style='width:75px' onKeyPress='return validarEntradaEntera(event);'></td>";
			echo "</tr>";

			echo "<tr>";
			echo "<td class='fila1'><input type='radio' name='ckFiltroAntibiotico' id='ckFiltroTratamiento'></td>";
			echo "<td class='fila1'>Tratamiento</td>";
			echo "</tr>";

			echo "<tr class='encabezadoTabla' id='trPrescripcionPediatrica'>";
			echo "<td class=fila1 colspan=2>Es prescripci&oacute;n pedi&aacute;trica?</td>";
			echo "<td class=fila2>S&iacute;<input type='radio' name='rdPrescripcionPediatrica' id='rdPediatricoSi' value='on'></td>";
			echo "<td class=fila2>No <input type='radio' name='rdPrescripcionPediatrica'  id='rdPediatricoNo' value='off'></td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<div>";
			echo "<br>";
			echo "<table style='width:100%'>";

			echo "<tr style='width:100%'>";
			echo "<td align='right'><input type='button' id='inFiltroGrabar' value='Grabar' sytle='width:100px'></td>";
			echo "<td align='left'><input type='button' id='inFiltroCerrar' value='Cerrar' sytle='width:100px'></td>";
			echo "</tr>";

			echo "</table>";
			echo "</div>";
			
			echo "</div>";
			echo "<div id='dvModalFiltroAntibiotico' style='display:none'></div>";
			/************************************************************************************************************/
			
			
			/************************************************************************************************************
			 * MODAL CONSULTAR DX CIE10
			/************************************************************************************************************/
			echo "<div id='dvCIE10' style='display:none'>";
			echo "<div class='encabezadotabla' style='margin:10px;font-size:14pt;text-align:center'>INGRESE UN DIAGNOSTICO</div>";
			echo "No se encuentra dx en la HCE. Ingrese un dx para los medicamentos de Control.";
			echo "<br>";
			echo "<input type=text name='inCIE10' id='inCIE10' value='".trim( $txtDiagHCE )."' style='width:400px;'>";
			echo "<input type=hidden name='inCopyCIE10' id='inCopyCIE10' value=''>";
			echo "<br>";
			echo "<input type=button id='btCerrarDx' name='btCerrarDx' value='Grabar' style='width:100px;'>";
			echo "</div>";
			//Nota: La función del boton Cerrar se encuentra en la función inicializar jquery
			/************************************************************************************************************/
			
			
			/************************************************************************************************************
			 * MODAL MEDICAMENTOS DE CONTROL A IMPRIMIR
			/************************************************************************************************************/
			echo "<div id='dvModalMedControl' style='display:none;overflow:auto;'>";
			
			//Titulo
			echo "<div class='encabezadotabla'>IMPRESION MEDICAMENTOS DE CONTROL</div>";
			
			//Informacion paciente
			echo "<div class='clInfoPaciente'>";
			// echo "<b>".$paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2."</b> hubicado en <b>".$paciente->habitacionActual."</b> con historia <b>".$paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica."</b>";
			
			echo "<b>".$paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2."</b> ";
			echo "ubicado en <b>".$paciente->nombreServicioActual."</b> ";
			echo !empty( $paciente->habitacionActual ) ? "hab. <b>".$paciente->habitacionActual."</b> " : "";
			echo "con historia <b>".$paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica."</b>";
			
			echo "</div>";
			
			//Contenido
			echo "<div style='margin:10px;width:100%;height:530px;'>";
			
			//Medicamentos de control
			echo "<div id='dvMedControl'>";
			echo "</div>";
			
			//Impresion
			echo "<div id='dvImpresionMedControl'>";
			echo "</div>";
			
			echo "</div>";	//Fin contenido
			echo "<div style='width:300px;margin:0 auto;'><input class='btnCerrar' type='button' value='Cerrar' onClick='$.unblockUI();'></div>";
			echo "</div>";
			/************************************************************************************************************/
			
			
			//marca los registros cómo leídos según el usuario
			//marcarRegistrosLeidos( $conex, $wbasedato, $wbasedatohce, $usuario->codigo, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
			
			/****************************************************************************************************************
			 * Octubre 18 de 2012
			 ****************************************************************************************************************/
			if( !empty($programa) ){
				echo "<input type='hidden' name='programa' id='programa' value='on'>";
			}
			/****************************************************************************************************************/
			
			$textoParaJustificacionPorTarifa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "textoJusParaArtsSinTarifa" );
	
			echo "<div id='question' style='display:none; cursor: default'>
					<h1 style='margin: 0 auto 20;background: #2A5DB0;'>ARTICULO SIN CONVENIO</h1>
					$textoParaJustificacionPorTarifa
					<textarea id='taJusParaArtsSinTarifas' style='width: 100%;margin: 20 0 20;'></textarea>
					<input type='button' id='btnAceptarAST' value='Aceptar' style='width: 200;height: 40;background: #218838;border: none;font-weight: bold;' />
					<input type='button' id='btnCerrarAST' value='Cerrar' style='width: 60px;height: 40px;background: #c82333;border: none;font-weight: bold;color: white;' />
				</div>";
			
		break;
		case 'c':		//Actualización del kardex
			$mensaje = "";
			$indicePestana = 1;	
			/**
			 * CONSULTA DEL MODO DE GRABACION DE CADA PESTAÑA.
			 */
			$vecPestanas = explode(";",$usuario->pestanasHCE);

			foreach($vecPestanas as $pestana){
				$vecPestanaElemento = explode("|",$pestana);

				if($vecPestanaElemento[0] != ''){
					$vecPestanaGrabacion[$vecPestanaElemento[0]] = ($vecPestanaElemento[2] == 'on');
				}
			}

			// 2012-06-27
			/************************************************
			 * Agrengando mensajeria
			 ************************************************/
			//Campo oculto que indica de que programa se abrio
			
			echo "<INPUT type='hidden' id='mesajeriaPrograma' value='Ordenes'>";
			 
			$kardexGrabar = new kardexDTO();

			//Captura de parametros. Encabezado del kardex
			$kardexGrabar->historia = $paciente->historiaClinica;
			$kardexGrabar->ingreso = $paciente->ingresoHistoriaClinica;
			$kardexGrabar->fechaCreacion = $wfecha;
			$kardexGrabar->horaCreacion = date("H:i:s");
			$kardexGrabar->fechaGrabacion = $wfechagrabacion;
			$kardexGrabar->usuario = $wuser;
			$kardexGrabar->confirmado = $confirmado;
			$kardexGrabar->esPrimerKardex = $primerKardex;
			$kardexGrabar->rutaOrdenMedica = $rutaOrdenMedica;
			$kardexGrabar->centroCostos = $usuario->centroCostosGrabacion;			
			$kardexGrabar->usuarioQueModifica = $usuario->codigo;
			$kardexGrabar->firmaDigital = $firmaDigital;
			$kardexGrabar->noAcumulaSaldoDispensacion = $wkardexnoacumula;

			if(isset($vecPestanaGrabacion[$indicePestana]) && $vecPestanaGrabacion[$indicePestana]){
				$kardexGrabar->talla = $txTalla;
				$kardexGrabar->peso = $txPeso;
				$kardexGrabar->diagnostico = str_replace("|",chr(13),utf8_decode( $txDiag) );
				$kardexGrabar->antecedentesAlergicos = str_replace("|",chr(13), utf8_decode( $txAlergias) );
				$kardexGrabar->antecedentesPersonales = str_replace("|",chr(13), utf8_decode( $txAntecedentesPersonales ) );
			}

			$indicePestana = 5;
			// if(isset($vecPestanaGrabacion[$indicePestana]) && $vecPestanaGrabacion[$indicePestana]){
				//$kardexGrabar->observaciones = str_replace("|",chr(13),$txObservaciones);
				// $kardexGrabar->cuidadosEnfermeria = str_replace("|",chr(13),$txCuidados);	//pertenece a la pestaña 10 Medidas generales
			//}
			
			$indicePestana = 6;
			if(isset($vecPestanaGrabacion[$indicePestana]) && $vecPestanaGrabacion[$indicePestana]){
				$kardexGrabar->dextrometer = str_replace("|",chr(13),utf8_decode( $txtDextrometer ) );
			}
			
			$indicePestana = 10;
			if(isset($vecPestanaGrabacion[$indicePestana]) && $vecPestanaGrabacion[$indicePestana]){
			//$kardexGrabar->obsDietas = str_replace("'","\'",$kardexGrabar->obsDietas);
				$kardexGrabar->consentimientos = trim( str_replace("'","\'", str_replace("|",chr(13),utf8_decode( $txtConsentimientos ) ) ) );
				$kardexGrabar->medidasGenerales = trim( str_replace("'","\'", str_replace("|",chr(13), utf8_decode( $txMedidas ) ) ) );
				$kardexGrabar->procedimientos = trim( str_replace("'","\'", str_replace("|",chr(13),utf8_decode( $txProcedimientos ) ) ) );
				$kardexGrabar->terapiaRespiratoria = trim( str_replace("'","\'", str_replace("|",chr(13),utf8_decode( $txTerapia ) ) ) );
				$kardexGrabar->curaciones = trim( str_replace("'","\'", str_replace("|",chr(13),utf8_decode( @$txtCuraciones ) ) ) );	//Esta sin uso en ordenes
				$kardexGrabar->sondasCateteres = trim( str_replace("|",chr(13), utf8_decode(@$txtSondas) ) );	//Esta sin uso en ordenes
				$kardexGrabar->interconsulta = trim( str_replace("|",chr(13), utf8_decode( @$txtInterconsulta ) ) );
				$kardexGrabar->obsDietas = trim( str_replace("'","\'",str_replace("|",chr(13),utf8_decode( $txtObsDietas ) )) );
				$kardexGrabar->cirugiasPendientes = trim( str_replace("'","\'", str_replace("|",chr(13),utf8_decode( $txtCirugiasPendientes ) ) ) );
				$kardexGrabar->terapiaFisica = trim( str_replace("'","\'", str_replace("|",chr(13),@$txTerapiaFisica) ) );
				$kardexGrabar->rehabilitacionCardiaca = trim( str_replace("|",chr(13),@$txRehabilitacionCardiaca) );
				$kardexGrabar->aislamientos = trim( str_replace("'","\'", str_replace("|",chr(13),utf8_decode( $txAislamientos ) ) ) );
				$kardexGrabar->cuidadosEnfermeria = trim( str_replace("|",chr(13),utf8_decode( $txCuidados) ) );
				$kardexGrabar->observaciones = str_replace("|",chr(13),utf8_decode($txObservaciones)); //Observaciones en medidas generales.
			}
			
			$kardexGrabar->indicaciones = $windicaciones;
			
			echo "<input type='hidden' name='whistoria' value='$kardexGrabar->historia'>";
			echo "<input type='hidden' name='wingreso' value='$kardexGrabar->ingreso'>";
			echo "<input type='hidden' name='wfecha' value='$wfecha'>";
			echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='0'/>";

			if(!existeEncabezadoKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha)){
				crearKardex($kardexGrabar);	
				$mensaje = "El kardex ha sido creado con éxito";
			} else {
				//Actualiza SOLO encabezado
				actualizarKardex($kardexGrabar,$vecPestanaGrabacion);
				$mensaje = "El kardex ha sido actualizado con éxito";
			}
			
			$esPrimerKardex = false;
			if($primerKardex == "S"){
				$esPrimerKardex = true;	
			}
			
			//marca los registros cómo leídos según el usuario
			marcarRegistrosLeidos( $conex, $wbasedato, $wbasedatohce, $usuario->codigo, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $pestanasVistas );
			
			//Carga de temporal a definitivo de los componentes del kardex, para todos, debe borrarse lo anterior
			cargarInfusionesADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion);
			cargarArticulosADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$esPrimerKardex,$firmaDigital);
			//cargarExamenesADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$firmaDigital);
			cargarDietasADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$firmaDigital);
			actualizarUsuarioDextrometer( $conex, $wbasedato,$paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$usuario->codigo,$firmaDigital);
			cargarMedicoADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$wfechagrabacion);
			eliminarDatoTemporalProcedimiento( $wbasedato,$wbasedatohce, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
			 
			/**
			 * HCE
			 */
			//Grabación de alertas via ordenes
			if(isset($kardexGrabar->antecedentesAlergicos) && $kardexGrabar->antecedentesAlergicos != ''){
				grabarAlertaHCE($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$kardexGrabar->antecedentesAlergicos,$kardexGrabar->usuario);
			}
			 
			//Si la orden está firmada, incremento los consecutivos de los centros de costos
			if(isset($kardexGrabar->firmaDigital) && !empty($kardexGrabar->firmaDigital)){
//				incrementarConsecutivoCentroCostos($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
			}
				
			echo "</form>";
				
			//Si el kardex se ha grabado este hidden se cargará en las paginas
			echo "<input type='hidden' name='whgrabado' value='on'>";
				
			echo '<span class="subtituloPagina2" align="center">';
			echo 'La orden médica se ha grabado correctamente... Regresando a la consulta';
			echo "</span><br><br>";
			
			$_SESSION['wordenes'] = 0;
//			echo "<div align='center'><input type=button value='Cerrar ventana' onclick='cerrarModalHCE();'></div>";
//			echo "Quitar linea........."; return;

			if( isset($editable) && $editable != 'on' ){
				funcionJavascript("inicio();");			
			}
			else{
				
				$medicamentosControlAuto = consultarAliasPorAplicacion( $conex, $wemp_pmla, "MedicamentosControlAuto" );
				
				if($medicamentosControlAuto == 'on'){
					if( empty( $inCIE10 ) )
						$inCIE10inCIE10 = '';
					funcionJavascript( 'activarModalIframe("../../movhos/procesos/impresionMedicamentosControl.php?wemp_pmla='.$wemp_pmla.'&historia='.$paciente->historiaClinica.'&ingreso='.$paciente->ingresoHistoriaClinica.'&fechaKardex='.$wfechagrabacion.'&consultaAjax=10&dxCIE10='.$inCIE10.'")' );
				}
				
				if($pgr_origen == 'gestionEnfermeria'){
					
					funcionJavascript("cerrarModalGestion();");
					
				}else{
				
					funcionJavascript("window.parent.cerrarModal();");
				
				}

				// 2012-10-31
				// Llama el programa para validación de grabación de CTC y medicamentos de control
				//funcionJavascript( 'activarModalIframe("","nombreIframe","generarCTCparaHCE.php?wemp_pmla='.$wemp_pmla.'&historia='.$paciente->historiaClinica.'&ingreso='.$paciente->ingresoHistoriaClinica.'&fechaKardex='.$wfechagrabacion.'","-1","0")' );
			
			}
			
//			if($kardexGrabar->confirmado == "on"){
//				funcionJavascript("inicio();");
//			} else {
//				funcionJavascript("consultarKardex();");	
//			}

			/****************************************************************************************************************
			 * Octubre 18 de 2012
			 ****************************************************************************************************************/

			if( !empty($programa) && $programa == 'on' ){

				echo "<input type='hidden' name='programa' id='programa' value='$programa'>";
				// funcionJavascript("inicio(\"$wsservicio&programa=on\");");
				funcionJavascript("window.close();");
			}
			else{
					// funcionJavascript("inicio(\"$wsservicio\");");
			}
			/****************************************************************************************************************/

			break;
		default:
		
			if( !empty($programa) && $programa == 'on' ){
				?>
					<script>
						window.close();
					</script>
				<?php
			}
		
			echo "<table border=0>";
			echo "<tr>";
			echo "<td class='subtituloPagina2' width=350>";
			echo "Consulta o generación de orden médica";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br>";

			//El encabezado es comun a todas las secciones del kardex
			echo "<table align='center'>";

			echo "<tr>";

			echo "<td class='fila1'>Historia cl&iacute;nica</td>";
			echo "<td class='fila2'>";
			echo $paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica;
			echo "</td>";

			echo "<td class='fila1' align=center rowspan=2><b><font size=3>Paciente</font></b></td>";
			echo "<td class='fila2' align=center colspan=3 rowspan=2><b><font size=3>";
			echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
			echo "</font></b></td>";

			echo "</tr>";

			echo "<tr>";

			//Servicio actual y habitacion
			echo "<td class='fila1'>Servicio y Habitaci&oacute;n actual</td>";
			echo "<td class='fila2'>";
			echo "$paciente->nombreServicioActual - $paciente->habitacionActual";
			echo "</td>";

			//Enfermera(o) que genera
			echo "<tr>";
			echo "<td class='fila1'>Usuario que actualiza (Codigo y nombre del Rol)</td>";
			echo "<td class='fila2'>";
			echo "$usuario->codigo - $usuario->descripcion. <br>$usuario->nombreCentroCostos ($usuario->codigoRolHCE-$usuario->nombreRolHCE)";
			echo "</td>";
			
			echo "<td class='fila1'>Fecha y hora de generaci&oacute;n</td>";
			echo "<td class='fila2'>";
			if(isset($kardexActual)){
				echo "".$kardexActual->fechaCreacion." - ".$kardexActual->horaCreacion;
			} else {
				echo "<br>";
			}
			echo "</td>";

			echo "<td class='fila1'>Fecha y hora de ingreso a la instituci&oacute;n</td>";
			echo "<td class='fila2'>";
			echo "$paciente->fechaIngreso - $paciente->horaIngreso";
			echo "</td>";
			
			echo "</tr>";
			
			echo "<tr>";

			//Valor de la edad
			$vecAnioNacimiento = explode("-",$paciente->fechaNacimiento);
			echo "<td class='fila1'>Edad</td>";
			echo "<td class='fila2'>";
			echo round(date("Y")-$vecAnioNacimiento[0]);
			echo "</td>";

			echo "<td class='fila1'>Ultimo mvto hospitalario</td>";
			
			if($paciente->altaDefinitiva == 'on'){
				echo "<td class='articuloControl'>";
			} else {
				echo "<td class='fondoAmarillo'>";
			}
			echo $paciente->ultimoMvtoHospitalario;						
			echo "</td>";
						
			//Calculo de dias de hospitalizcion desde ingreso
			$diaActual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$fecha = explode("-",$paciente->fechaIngreso);
			$diaIngreso = mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);

			$diasHospitalizacion = ROUND(($diaActual - $diaIngreso)/(60*60*24));

			echo "<td class='fila1'>D&iacute;as de hospitalizaci&oacute;n</td>";
			echo "<td class='fila2'>";
			echo "".$diasHospitalizacion;
			echo "</td>";
			
			echo "<td colspan=2>&nbsp;</td>";
			
			echo "</tr>";
			
			echo "<tr>";		
			
			//Responsable
			echo "<td class='fila1'>Entidad responsable</td>";
			echo "<td class='fila2'>";
			echo "$paciente->numeroIdentificacionResponsable - $paciente->nombreResponsable";			
			echo "</td>";
			
			//Fecha y hora de ingreso al servicio actual
			echo "<td class='fila1'>Fecha de ingreso al servicio actual</td>";
			echo "<td class='fila2'>";
			echo "$paciente->fechaHoraIngresoServicio";						
			echo "</td>";
			
			echo "<td class='fila1'>";
			
			//Boton de vistas asociadas
//			echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='abrirModalHCE();'>Vistas Asociadas</A>";
			
			echo "</td>";
			echo "<td class='fila2'>&nbsp;";
//			echo "<a href='#null' onclick='return fixedMenu.show();'>Alergias</a>";						
			echo "</td>";
			
			echo "</tr>";
				
			echo "<tr>";
			echo "<td height=30 colspan=6>&nbsp;</td>";
			echo "</tr>";
			
			echo "</table>";
			
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Fecha de consulta de orden';
			echo "</span>";
			echo "<br>";
			echo "<br>"; 

			//Por fecha generacion kardex
			echo "<tr>";
			echo "<td class='fila2' align='center' width=250>";
			campoFecha("wfecha");
			echo "</td></tr>";

			//Si la fecha del servidor difiere de la del equipo donde se esta digitando el kardex
			$fechaActual = date("Y-m-d");
			$horaActual = date("H:i:s");
			
			funcionJavascript("validarFechayHoraLocal('".$fechaActual."','".$horaActual."');");
			
			echo "<tr><td align=center colspan=4><br><input id='btnConsultar' type=button value='Consultar o generar' onclick='consultarKardex();'></td>";
//			echo "<td align=center colspan=><br><input id='' type=button value='Cerrar ordenes' onclick='cerrarModal();'></td>";
			echo "</tr>";
			echo "</table>";
			break;			
	}
	liberarConexionBD($conex);
}

?>
<script type="text/javascript">

<?php
if( !empty( $procedimientosAgrupados ) && count($procedimientosAgrupados) > 0 ){
?>
	procAgrupados = <?php echo json_encode( $procedimientosAgrupados ); ?>
<?php
}
?>


//Marzo 16 de 2015
// if(document.getElementById("fixeddiv")){
	// $("#fixeddiv")[0].style.right = parseInt(window.innerWidth-$("#fixeddiv")[0].offsetWidth-document.body.offsetWidth*0.1-30)+"px";
	// $("#fixeddiv").draggable();
// }	

var elementosDetalle = 0, elementosAnalgesia = 0, elementosNutricion = 0, elementosQuimioterapia = 0, elementosLev = 0, cuentaExamenes = 0, cuentaInfusiones = 0; if(document.forms.forma.elementosKardex) elementosDetalle = document.forms.forma.elementosKardex.value; if(document.forms.forma.elementosAnalgesia) elementosAnalgesia = document.forms.forma.elementosAnalgesia.value; if(document.forms.forma.elementosNutricion)elementosNutricion = document.forms.forma.elementosNutricion.value; if(document.forms.forma.elementosQuimioterapia)elementosQuimioterapia = document.forms.forma.elementosQuimioterapia.value; if(document.forms.forma.elementosLev)elementosLev = document.forms.forma.elementosLev.value; if(document.forms.forma.cuentaExamenes)cuentaExamenes = document.forms.forma.cuentaExamenes.value; if(document.forms.forma.cuentaInfusiones)cuentaInfusiones = document.forms.forma.cuentaInfusiones.value; if(document.getElementById("fixeddiv")) { fixedMenuId = "fixeddiv"; var fixedMenu = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId) : document.all ? document.all[fixedMenuId] : document.layers[fixedMenuId]}; fixedMenu.computeShifts = function() { fixedMenu.shiftX = fixedMenu.hasInner ? pageXOffset : fixedMenu.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu.shiftX += fixedMenu.targetLeft > 0 ? fixedMenu.targetLeft : (fixedMenu.hasElement ? document.documentElement.clientWidth : fixedMenu.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu.targetRight - fixedMenu.menu.offsetWidth; fixedMenu.shiftY = fixedMenu.hasInner ? pageYOffset : fixedMenu.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu.shiftY += fixedMenu.targetTop > 0 ? fixedMenu.targetTop : (fixedMenu.hasElement ? document.documentElement.clientHeight : fixedMenu.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu.targetBottom - fixedMenu.menu.offsetHeight }; fixedMenu.moveMenu = function() { fixedMenu.computeShifts(); if(fixedMenu.currentX != fixedMenu.shiftX || fixedMenu.currentY != fixedMenu.shiftY) { fixedMenu.currentX = fixedMenu.shiftX; fixedMenu.currentY = fixedMenu.shiftY; if(document.layers) { fixedMenu.menu.left = fixedMenu.currentX; fixedMenu.menu.top = fixedMenu.currentY }else { fixedMenu.menu.style.left = fixedMenu.currentX + "px"; fixedMenu.menu.style.top = fixedMenu.currentY + "px" } }fixedMenu.menu.style.right = ""; fixedMenu.menu.style.bottom = "" }; fixedMenu.floatMenu = function() { fixedMenu.moveMenu(); setTimeout("fixedMenu.floatMenu()", 20) }; fixedMenu.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu.init = function() { if(fixedMenu.supportsFixed())fixedMenu.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu.menu : fixedMenu.menu.style; fixedMenu.targetLeft = parseInt(a.left); fixedMenu.targetTop = parseInt(a.top); fixedMenu.targetRight = parseInt(a.right); fixedMenu.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu.addEvent(window, "onscroll", fixedMenu.moveMenu); fixedMenu.floatMenu() } }; fixedMenu.addEvent(window, "onload", fixedMenu.init); fixedMenu.hide = function() { fixedMenu.menu.style.display = "none"; return false }; fixedMenu.show = function() { fixedMenu.menu.style.display = "block"; return false } }if(document.getElementById("fixeddiv2")) { fixedMenuId2 = "fixeddiv2"; var fixedMenu2 = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId2) : document.all ? document.all[fixedMenuId2] : document.layers[fixedMenuId2]}; fixedMenu2.computeShifts = function() { fixedMenu2.shiftX = fixedMenu2.hasInner ? pageXOffset : fixedMenu2.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu2.shiftX += fixedMenu2.targetLeft > 0 ? fixedMenu2.targetLeft : (fixedMenu2.hasElement ? document.documentElement.clientWidth : fixedMenu2.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu2.targetRight - fixedMenu2.menu.offsetWidth; fixedMenu2.shiftY = fixedMenu2.hasInner ? pageYOffset : fixedMenu2.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu2.shiftY += fixedMenu2.targetTop > 0 ? fixedMenu2.targetTop : (fixedMenu2.hasElement ? document.documentElement.clientHeight : fixedMenu2.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu2.targetBottom - fixedMenu2.menu.offsetHeight }; fixedMenu2.moveMenu = function() { fixedMenu2.computeShifts(); if(fixedMenu2.currentX != fixedMenu2.shiftX || fixedMenu2.currentY != fixedMenu2.shiftY) { fixedMenu2.currentX = fixedMenu2.shiftX; fixedMenu2.currentY = fixedMenu2.shiftY; if(document.layers) { fixedMenu2.menu.left = fixedMenu2.currentX; fixedMenu2.menu.top = fixedMenu2.currentY }else { fixedMenu2.menu.style.left = fixedMenu2.currentX + "px"; fixedMenu2.menu.style.top = fixedMenu2.currentY + "px" } }fixedMenu2.menu.style.right = ""; fixedMenu2.menu.style.bottom = "" }; fixedMenu2.floatMenu = function() { fixedMenu2.moveMenu(); setTimeout("fixedMenu2.floatMenu()", 20) }; fixedMenu2.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu2.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu2.init = function() { if(fixedMenu2.supportsFixed())fixedMenu2.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu2.menu : fixedMenu2.menu.style; fixedMenu2.targetLeft = parseInt(a.left); fixedMenu2.targetTop = parseInt(a.top); fixedMenu2.targetRight = parseInt(a.right); fixedMenu2.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu2.addEvent(window, "onscroll", fixedMenu2.moveMenu); fixedMenu2.floatMenu() } }; fixedMenu2.addEvent(window, "onload", fixedMenu2.init); fixedMenu2.hide = function() { if(fixedMenu2.menu.style.display != "none")fixedMenu2.menu.style.display = "none"; return false }; fixedMenu2.show = function(a) { document.getElementById("wtipoprot"); var b = 0; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)document.forms.forma.wtipoprot[b].disabled = true; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)if(a.indexOf(document.forms.forma.wtipoprot[b].value) != -1) { document.forms.forma.wtipoprot[b].checked = true; document.forms.forma.wtipoprot[b].disabled = false }fixedMenu2.menu.style.display = "block"; return false } };

if(document.getElementById("movExamenes")){ 

	fixedMenuId2 = 'movExamenes'; 

	var movExamenes = { hasInner: typeof(window.innerWidth) == 'number', hasElement: document.documentElement != null && document.documentElement.clientWidth, menu: document.getElementById ? document.getElementById(fixedMenuId2) : document.all ? document.all[fixedMenuId2] : document.layers[fixedMenuId2] };

	movExamenes.computeShifts = function() { movExamenes.shiftX = movExamenes.hasInner ? pageXOffset : movExamenes.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; if (movExamenes.targetLeft > 0) movExamenes.shiftX += movExamenes.targetLeft; else { movExamenes.shiftX += (movExamenes.hasElement ? document.documentElement.clientWidth : movExamenes.hasInner ? window.innerWidth - 20	: document.body.clientWidth) - movExamenes.targetRight- movExamenes.menu.offsetWidth; } movExamenes.shiftY = movExamenes.hasInner ? pageYOffset : movExamenes.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; if (movExamenes.targetTop > 0) movExamenes.shiftY += movExamenes.targetTop; else {	 movExamenes.shiftY += (movExamenes.hasElement ? document.documentElement.clientHeight : movExamenes.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - movExamenes.targetBottom - movExamenes.menu.offsetHeight; } };

    movExamenes.moveMenu = function(){	 movExamenes.computeShifts(); if (movExamenes.currentX != movExamenes.shiftX || movExamenes.currentY != movExamenes.shiftY) { movExamenes.currentX = movExamenes.shiftX; movExamenes.currentY = movExamenes.shiftY; if (document.layers) { movExamenes.menu.left = movExamenes.currentX; movExamenes.menu.top = movExamenes.currentY;} else { movExamenes.menu.style.left = movExamenes.currentX + 'px'; movExamenes.menu.style.top = movExamenes.currentY + 'px'; }} movExamenes.menu.style.right = ''; movExamenes.menu.style.bottom = ''; };

    movExamenes.floatMenu = function() { movExamenes.moveMenu(); setTimeout('movExamenes.floatMenu()', 20); };

 // addEvent designed by Aaron Moore
 	movExamenes.addEvent = function(element, listener, handler){ if(typeof element[listener] != 'function' || typeof element[listener + '_num'] == 'undefined') { element[listener + '_num'] = 0;if (typeof element[listener] == 'function')
		 {
			 element[listener + 0] = element[listener];
			 element[listener + '_num']++;
		 }
		 element[listener] = function(e)
		 {
			 var r = true;
			 e = (e) ? e : window.event;
			 for(var i = 0; i < element[listener + '_num']; i++)
				 if(element[listener + i](e) === false)
					 r = false;
			 return r;
		 }
	 }

	 //if handler is not already stored, assign it
	 for(var i = 0; i < element[listener + '_num']; i++)
		 if(element[listener + i] == handler)
			 return;
	 element[listener + element[listener + '_num']] = handler;
	 element[listener + '_num']++;
 };

 movExamenes.supportsFixed = function()
 {
	 var testDiv = document.createElement("div");
	 testDiv.id = "testingPositionFixed";
	 testDiv.style.position = "fixed";
	 testDiv.style.top = "0px";
	 testDiv.style.right = "0px";
	 document.body.appendChild(testDiv);
	 var offset = 1;
	 if (typeof testDiv.offsetTop == "number"
		 && testDiv.offsetTop != null 
		 && testDiv.offsetTop != "undefined")
	 {
		 offset = parseInt(testDiv.offsetTop);
	 }
	 if (offset == 0)
	 {
		 return true;
	 }

	 return false;
 };

 movExamenes.init = function()
 {
	 if (movExamenes.supportsFixed())
		 movExamenes.menu.style.position = "fixed";
	 else
	 {
		 var ob = document.layers ? movExamenes.menu : movExamenes.menu.style;

		 movExamenes.targetLeft = parseInt(ob.left);
		 movExamenes.targetTop = parseInt(ob.top);
		 movExamenes.targetRight = parseInt(ob.right);
		 movExamenes.targetBottom = parseInt(ob.bottom);

		 if (document.layers)
		 {
			 menu.left = 0;
			 menu.top = 0;
		 }
		 movExamenes.addEvent(window, 'onscroll', movExamenes.moveMenu);
		 movExamenes.floatMenu();
	 }
 };

 movExamenes.addEvent(window, 'onload', movExamenes.init);

 movExamenes.hide = function()
 {
	 if(movExamenes.menu.style.display != 'none'){
		 movExamenes.menu.style.display='none';
	 }
	 return false;
 }

 movExamenes.show = function(tipos)
 {
//	 debugger;
	 movExamenes.menu.style.display='block';
	 return false;
 }
}
</script>
</body>
</html>