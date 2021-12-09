<html>
<head>
<title>MATRIX - [JQUERY COMPONENTES]</title>

<!--JQUERY-->

<!-- Hojas de estilo -->

<!-- Nucleo jquery -->
<!--<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />	-->

<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.resizable.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.datepicker.css" rel="stylesheet"/>

<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" /> <!-- Tooltip -->
<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
<link type="text/css" href="../../../include/root/jquery.simpletree.css" rel="stylesheet" /> <!-- Simpletree -->
<link type="text/css" href="../../../include/root/jquery.jTPS.css" rel="stylesheet" /> <!-- Modal nativo -->

<!-- Nucleos -->
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script> 	 <!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery-ui-1.7.2.custom.min.js"></script> 	<!--  Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script> <!-- Block UI -->
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>	<!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script> <!-- Pesta�as -->
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>	<!-- Arrastrable -->
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>	<!-- Acordeon -->
<script type="text/javascript" src="../../../include/root/ui.dialog.js"></script>	<!-- Modal nativo -->
<script type="text/javascript" src="../../../include/root/ui.resizable.js"></script>	<!-- Redimensionable -->

<!-- Dependencias de plugins -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script> <!-- Tooltip, Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script> <!-- Tooltip -->

<!-- Plugins -->
<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.maskedinput.js" ></script> <!-- Masked input -->
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>  <!-- Simpletree -->
<script type="text/javascript" src="../../../include/root/jquery.jTPS.js"></script>	<!-- Datagrid -->
<!--  -->
<!--  -->
<!--  -->
<!--  -->

<!--Fin JQUERY-->

<script type="text/javascript">

var arregloTemporal = new Array();
var indiceTemporal = "";

$(document).ready(function(){

	//Inicio de pesta�as
    $("#tabs").tabs(); 

	//Inicio de acordeon
    $("#accordion").accordion({ collapsible: true });

	//Inicio de arrastrable iframe
	//$("#flotante").resizable();
    
	//Inicio de arrastrable
//    $("#flotante").draggable();

  	//Inicio de arrastrable
//    $("#flotanteIframe").resizable().parent().draggable();
//  	$("#flotanteIframe").resizable();

	//Inicio de arrastrable
	$("#flotanteIframe").resizable();
	
	$("#flotanteIframe").draggable();
    
    //Inicio de tooltip
    $("#txtTooltip").tooltip({ 
    	track: true, 
        delay: 0, 
        showURL: false, 
        showBody: " - ", 
        extraClass: "globo", 
        fixPNG: true, 
        opacity: 0.95, 
        left: -120 
    }); 

    //Inicio de autocomplete
    var datos = "Mauricio Macsanz Maria Mauro Mario Martha Mariela Arriba Autocompletar Autos Marca Mora Miro Mira Cosa Coso Mond� Melon Dulce Vida Alegr�a Eladio".split(" ");    
    $("#auto1").focus().autocomplete(datos);

	//http://localhost/matrix/root/procesos/autocompletar.php?q=a
	$("#auto2").autocomplete('autocompletar.php?consulta='+document.getElementById("T1").value, {
		max: 100,
		scroll: true,
		scrollHeight: 500,
		matchContains: true,
		width:500,
		autoFill:true,
		formatResult: function(data, value) {
			return value;
		}
	}).result(function(event, item) {

		  var elemento = document.getElementById("selAuto");
		  var componente = document.createElement('option');

		  componente.setAttribute('value',item);
		  if(esIE){
			 componente.setAttribute('text',item);
			 elemento.add(componente); //IE
		  } else {
			 componente.innerHTML = item;
			 elemento.add(componente, null); //No es IE
		  }
		
		  $("#auto2").focus();
		  $("#auto2").select();
	});

	//Masked input
	$.mask.definitions['H']='[012]';
    $.mask.definitions['N']='[012345]';
    $.mask.definitions['n']='[0123456789]';
    
	$("#fechaMatrix").mask("9999-99-99");   
	$("#celular").mask("(3H9) 999-9999");   
	$("#hora").mask("Hn:Nn:Nn");   

	//Simpletree Arbol 
	var simpleTreeCollection = $('.simpleTree').simpleTree({
		autoclose: true,
		drag: false,
		afterClick:function(node){
			var elemento = $('span:first',node);
			
			var indice = elemento.parent().attr("id");
			var texto = elemento.text();

			if(indice.substring(0,1) == "H"){
				alert("Hoja seleccionada: "+ texto + " Id:" + indice);
			}
		},
		afterDblClick:function(node){
			//alert("text-"+$('span:first',node).text());
		},
		afterMove:function(destination, source, pos){
			//alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
		},
		afterAjax:function()
		{	
			//alert('Loaded');
		},
		animate:true
		//,docToFolderConvert:true
	});

	//Datagrid
	$('#demoTable').jTPS( {perPages:[12], scrollStep:1, scrollDelay:30, perPageText:'Cantidad por p�gina ', perPageShowing: 'Mostrando'} ); 

	// bind mouseover for each tbody row and change cell (td) hover style
    $('#demoTable tbody tr:not(.stubCell)').bind('mouseover mouseout',
            function (e) {
                    // hilight the row
                    e.type == 'mouseover' ? $(this).children('td').addClass('hilightRow') : $(this).children('td').removeClass('hilightRow');
            }
    );

});

function inicio(){
	document.location.href='fichasJQuery.php?wemp_pmla='+document.forms.forma.wemp_pmla.value;	
}

function terminoGrabar(){
	$.unblockUI();
}

function grabar(){
//	debugger;
	var html = document.getElementById("msjEspere").innerHTML;

	var pare = window.parent;
	var pare2 = pare.self.document;
	var pare3 = pare2.getElementById("msjEsperePadre");
	
	pare3.innerHTML = html;
	 
	pare.$.blockUI({ message: pare3 });	
//	setTimeout(pare.$.unblockUI(),5000);
}

function quitarComponente(idx){
  var elSel = document.getElementById('selAuto');
  
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
    if (elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}

function chequeoTodos(){
	var cont1 = 1;
	var estado = document.getElementById('chkTodos').checked;

	while(document.getElementById("chk"+cont1)){	
		document.getElementById("chk"+cont1).checked = estado;
		cont1++;
	}
}

function validaFecha(valor)
{
   //que no existan elementos sin escribir
   if(valor.indexOf("_") == -1)
   {
      var hora = valor.split(":")[0];
      if(parseInt(hora) > 23 )
      {
           $("#hora").val("");
      }
   }
}

//IdElmento es el id del div, indice de cada check
function activarModal(idElemento,indice){
	var cont1 = 0;
	var temporal2 = "", mensaje = "";

	$.blockUI({ 
			message: $('#'+idElemento), 
			css: { width: '275px' }
			});

	var acumulador = document.getElementById(indice).value;
	var valores = acumulador.split(",");
	
	while(document.getElementById(indice+cont1.toString())){
		temporal2 = document.getElementById(indice+cont1.toString()).checked;
//		temporal = arregloTemporal[cont1];
		temporal = valores[cont1] == "on" ? true : false;
			
		mensaje += temporal2 ? " on" : " off";
	
		document.getElementById(indice+cont1.toString()).checked = temporal;
	
		mensaje += temporal ? " on" : " off";	
		mensaje += "<br>";
			
		cont1++;
	}
}

function activarModalIframe(titulo,nombre,url,alto,ancho){
	var html = "" +
	"<table cellpadding=1 cellspacing=1 width='100%' style='cursor:default'>" +
	"<tr height='20' class='encabezadoTabla'>" +
	"<td>" +
    "<b>" + titulo + "</b>" +
    "</td>" +
	"<td align='center'>" +
    "<img src='../../../include/root/cerrar.gif' title='Cerrar' onclick='javascript:cerrarModal();' style='cursor:hand'>" +
   	"</td>" +
    "</tr>" +
    "</tr><tr><td>&nbsp;</td></tr>" +
    "<tr><td colspan=2 class='textoNormal'>" +

    "<iframe name='" + nombre + "' src='" + url + "' width='100%' height='" + (parseInt(alto) - 30) + "' width='" + ancho + "' frameborder='0'></iframe>" +
    
    "</td></tr></table>";
    
    $.blockUI({ 
			message: html, 
			css: { width: ancho + 'px' },
			centerX: false, 
		    centerY: false,
		    forceIframe: true,
		    allowBodyStretch: false
	});	
}

function mostrarFlotante(){
//	debugger;
	var elemento = document.getElementById("flotanteIframe");

	var titulo = document.getElementById("txtTituloDiv").value;
	var nombre = document.getElementById("txtNombreDiv").value;
	var url = document.getElementById("txtUrlDiv").value;
	var alto = document.getElementById("txtAltoDiv").value;
	var ancho = document.getElementById("txtAnchoDiv").value;
	
	var html = "<center><p><b>"+titulo+"</b></p>";	
	html += "<iframe name='" + nombre + "' src='" + url + "' width='98%' height='90%' height='" + (parseInt(alto) - 30) + "px' width='" + ancho + "px' frameborder='0'></iframe>";
	html += "<center><input type='button' value='Ocultar' onClick='javascript:ocultarFlotante();' /></center>";

	elemento.innerHTML = html;

	elemento.style.width = (parseInt(ancho) + 10)+'px';
	elemento.style.height = (parseInt(alto) + 10)+'px';
	
	$('#flotanteIframe').show();	
}

function ocultarFlotante(){
	$('#flotanteIframe').hide();
}

function cerrarModal(){
	$.unblockUI({onUnblock: function(){ //if(arreglo && arreglo != 'undefined'){
//		respuestaUnblock(idElemento,arreglo);
	//}
	}});
}

function respuestaUnblock(idElemento, arreglo){
//	alert('onUnblock'+ idElemento);
	//Machetin pinguin!
	var temporal=""; 
	var temporal2="";
	var mensaje="";

	var acumulador = "";
	
	var setter = new Array();
	var cont1 = 0;

	while(document.getElementById(idElemento+cont1.toString())){
		temporal2 = document.getElementById(idElemento+cont1.toString()).checked;
		temporal = arreglo[cont1];
		
		mensaje += temporal2 ? " on" : " off";

		document.getElementById(idElemento+cont1.toString()).checked = temporal;

		acumulador += temporal ? "on," : "off,";
		
		mensaje += temporal ? " on" : " off";
		mensaje += "<br>";
		
		cont1++;
	}	
	
	arregloTemporal = arreglo;
	document.getElementById(idElemento).value = acumulador.substring(0,acumulador.length-1);
	
	indiceTemporal = idElemento;
	$.growlUI('Risposta de la monda', mensaje);
}

function evaluarEnvio(idElemento){
	var mensaje="";
	var cont1 = 0;
	var arreglo = document.getElementById(idElemento);
	var setter = new Array();
	arreglo.value = "";
	
	while(document.getElementById(idElemento+cont1.toString())){
		mensaje += idElemento + cont1.toString();
		mensaje += document.getElementById(idElemento+cont1.toString()).checked ? " on\n\r" : " off\n\r";

		arreglo.value += document.getElementById(idElemento+cont1.toString()).checked ? "on," : "off,";

		setter[cont1] = document.getElementById(idElemento+cont1.toString()).checked;
		
		cont1++;
	}

	arreglo.value = arreglo.value.substring(0,arreglo.value.length-1);
	alert(arreglo.value);

	$.unblockUI({onUnblock: function(){ 
			if(arreglo != 'undefined'){
				respuestaUnblock(idElemento,setter);
			}
			}});
}

function abrirModalNativo(){
    $("#dialog").dialog({
    	bgiframe: true,
    	modal: true,
    	buttons: {
    		Ok: function(){
    			$(this).dialog('close');
    		}
    	}
    });
}

function avisoGrowl(){
	var titulo = document.getElementById('tituloGrowl').value;
	var texto = document.getElementById('textoGrowl').value;
	
	$.growlUI(titulo,texto);
}

</script>

<!-- Fin estilos locales -->
<style>
	#demoTable thead th {
    	white-space: nowrap;
    	overflow-x:hidden;
    	padding: 3px;
    }
    
    #demoTable tbody td {
    	padding: 3px;
    }
</style>

<!-- Fin estilos locales -->
</head>

<body>

<?php
include_once("conex.php");
/**
 * 
 * @param $conex
 * @param $wbasedato
 * @return unknown_type
 */

function consultarClases(){
	global $conex;
	global $wbasedato;
	
	$q = "SELECT 
			Clacod, Clades  
		FROM 
			".$wbasedato."_000072 
		WHERE
			Claest = 'on';";
	
//	echo $q;
	
	$coleccion = array();
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$cont1 = 0;
	if($num > 0 ){
		while($cont1 < $num)
		{
			$info = mysql_fetch_array($res);

			$registro = new RegistroSimple();

			$registro->codigo = $info['Clacod'];
			$registro->descripcion = $info['Clades'];

			$cont1++;

			$coleccion[] = $registro;
		}
	}
	return $coleccion;
}

//Includes
include_once("root/comun.php");
$wactualiz = " 1.2 02-Feb-10 @Msanchez";

$usuarioValidado = true;

$conex = obtenerConexionBD("matrix");
$wbasedato = "movhos";

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
encabezado("Utilidades de Jquery",$wactualiz,"clinica");

if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}else{
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	//Base de datos, se generaliza de generar kardex
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	//Forma
	echo "<form name='forma' action='fichasJQuery.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";

	//Mensaje que aparece cuando se graba
	echo "<div id='msjEsperePadre' style='display:none;'>"; 
    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento..."; 
    echo "<a href='javascript:window.parent.$.unblockUI();'>Cerrar</a>";
	echo "</div>";
	
	//Mensaje que aparece cuando se graba
	echo "<div id='msjEspere' style='display:none;'>"; 
    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento..."; 
    echo "<a href='javascript:window.parent.$.unblockUI();'>Cerrar</a>";
	echo "</div>";
	
	//Mensaje del tooltip
	echo "<div id='tooltip' style='display: none; top: 1078px; left: 119px; right: auto;'>";
	echo "<h3>Informacion adicional</h3>";
	echo "Esto es una ampliaci�n de la informaci�n adicional....";
	echo "</div>";

	//Indicador de si es fecha actual
	echo "Pesta�as";
	echo "<div id='tabs'>";				//Inicio de lo que va a ir encerrado en las pesta�as
		
	//Orden y nombre de las pesta�as (tabs)
	echo "<ul>";
	echo "<li><a href='#fragment-1'><span>Acordeon</span></a></li>";
	echo "<li><a href='#fragment-2'><span>Cuadro arrastrable</span></a></li>";
	echo "<li><a href='#fragment-3'><span>Bloqueo de pagina</span></a></li>";
	echo "<li><a href='#fragment-4'><span>Tooltip</span></a></li>";	
	echo "<li><a href='#fragment-5'><span>Autocomplete</span></a></li>";
	echo "<li><a href='#fragment-6'><span>Masked input</span></a></li>";
	echo "<li><a href='#fragment-7'><span>Utilidades html</span></a></li>";
	echo "<li><a href='#fragment-8'><span>Simpletree</span></a></li>";
	echo "<li><a href='#fragment-10'><span>Modal</span></a></li>";
	echo "<li><a href='#fragment-11'><span>Datagrid</span></a></li>";
	echo "</ul>";

	echo "<div id='fragment-1'>";
	echo "Acordeon";
	
	//Acordeon
	echo "<div id='accordion'>";
	echo "<h3><a href='#'>Section 1</a></h3>";
	echo "<div>";
	echo "<p>";
	echo "Ultrices a, suscipit eget, quam. Integer";
	echo "ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit";
	echo "amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut";
	echo "odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.";
	echo "</p>";
	echo "</div>";
	
	echo "<h3><a href='#'>Section 2</a></h3>";
	echo "<div>";
	echo "<p>";
	echo "Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet";
	echo "purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor";
	echo "velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In";
	echo "suscipit faucibus urna.";
	echo "</p>";
	echo "</div>";
	
	echo "<h3><a href='#'>Section 3</a></h3>";
	echo "<div>";
	echo "<p>";
	echo "Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis.";
	echo "Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero";
	echo "ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis";
	echo "lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui.";
	echo "</p>";
	echo "<ul>";
	echo "<li>List item one</li>";
	echo "<li>List item two</li>";
	echo "<li>List item three</li>";
	echo "</ul>";
	echo "</div>";
	
	echo "<h3><a href='#'>Section 4</a></h3>";
	echo "<div>";
	echo "<p>";
	echo "Cras dictum. Pellentesque habitant morbi tristique senectus et netus";
	echo "et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in";
	echo "faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia";
	echo "mauris vel est.";
	echo "</p>";
	echo "<p>";
	echo "Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus.";
	echo "Class aptent taciti sociosqu ad litora torquent per conubia nostra, per";
	echo "inceptos himenaeos.";
	echo "</p>";
	echo "</div>";
 	echo "</div>";
	echo "</div>";
		
	echo "<div id='fragment-2'>";
//	echo "Cuadro arrastrable simple";
//	
//	echo "<br>";
//	echo "<div id='flotante' style='position:absolute;z-index:99;width:200px;height:50px;padding:5px;background:#FFFFCC;border:2px solid #FFD700'>";
//	echo "<p>";
//	echo "Arrastre este cuadro dentro de la pantalla...";
//	echo "</p>";	
//	echo "</div>";
	
	echo "Cuadro arrastrable simple&nbsp;&nbsp;<input type='button' value='Ver' onClick='javascript:mostrarFlotante();'><br>";
	
	echo "Titulo&nbsp;<input type=text id='txtTituloDiv' value='Titulo'>&nbsp;&nbsp;";
	echo "Nombre frame&nbsp;<input type=text id='txtNombreDiv' value='nmFrame'>&nbsp;&nbsp;";
	echo "Url&nbsp;<input type=text id='txtUrlDiv' value='http://200.24.5.118/matrix/f1.php'>&nbsp;&nbsp;";
	echo "Alto&nbsp;<input type=text id='txtAltoDiv' value='400'>&nbsp;&nbsp;";
	echo "Ancho&nbsp;<input type=text id='txtAnchoDiv' value='750'><br>";
	
	echo "<br>";
	echo "<div id='flotanteIframe' style='background:#FFFFCC;'>";
	echo "Aqui va el contenido del iframe&nbsp;";
	echo "</div>";
	
	echo "</div>";

	echo "<div id='fragment-3'>";
	echo "Bloqueo de pagina mientras ocurre una accion";
	echo "<br>";
	echo "<input type='button' id='btnGrabar' value='Simular un proceso de 5 segundos' onClick='javascript:grabar();'>";
	
	echo "<input type=checkbox id=chk346>";
	echo "</div>";

	echo "<div id='fragment-4'>";
	echo "<span id='txtTooltip' title='Advertencia. - Recuerde que el numero maximo es m�simo!.  Como que esto si va a funcionar...Integer dignissim consequat lectus. Integer dignissim consequat lectus'>Pase sobre este texto y ver� lo que pasa...</span>";
	echo "</div>";

	echo "<div id='fragment-5'>";
	echo "Muestra del uso de autocomplete, solo variable javascript:&nbsp;&nbsp;";
	echo "<input type='text' id='auto1'/>";	
	echo "<br>";
	echo "<br>";
	
	echo "Este autocomplete va a la base de datos de matrix:&nbsp;&nbsp;";
	$query="SELECT Codigo, Descripcion FROM root_000011 WHERE Descripcion LIKE var ORDER BY Descripcion;";
	echo "<input type='hidden' id='T1' value='$query'>";
	
	echo "<input type='text' id='auto2' size=30/>";
	echo "<br>";

	echo "<select id=selAuto multiple=multiple size=7 onDblClick='javascript:quitarComponente();'>";
	
	echo "</select>";
	
	echo "<br>";
	echo "<br>";
	
	echo "</div>";
		
	echo "<div id='fragment-6'>";
	
	echo "Campos enmascarados";
	echo "Fecha formato matrix: <input type=text id=fechaMatrix>";
	echo "<br>";
	echo "Numero celular: <input type=text id=celular>";
	echo "<br>";
	echo "Hora: <input type=text id=hora onBlur='javascript:validaFecha(this.value);'>";
	echo "<br>";
	
	echo "</div>";
	
	echo "<div id='fragment-7'>";
	echo "Activar/inactivar todos checks:: <input type=checkbox id=chkTodos onClick='javascript:chequeoTodos();'><br/>";
	echo "<input type=checkbox id=chk1>";
	echo "<input type=checkbox id=chk2>";
	echo "<input type=checkbox id=chk3>";
	echo "<input type=checkbox id=chk4>";
	echo "<input type=checkbox id=chk5>";
	echo "<input type=checkbox id=chk6>";
	echo "<input type=checkbox id=chk7>";
	echo "<input type=checkbox id=chk8>";
	
	echo "<br>";
	
	echo "<h3>Mensaje Growl</h3>";
	echo "Titulo mensaje: <input type=text id=tituloGrowl value='Cambie el titulo'>";
	echo "Texto mensaje: <input type=text id=textoGrowl value='Cambie el textooooo'>";
	
	echo "<input type=button value='Activar Mensaje Growl' onClick='javascript:avisoGrowl();'>";
	
	echo "<h3>Division con scroll?</h3>";
	
	echo "<div style='width: 100px;height: 200px;overflow-y: scroll;overflow-x: scroll;'>";
	echo"#

    * You need PHP 5.2.0 or newer, with session support (see FAQ 1.31) and the Standard PHP Library (SPL) extension.
    * To support uploading of ZIP files, you need the PHP zip extension.
    * For proper support of multibyte strings (eg. UTF-8, which is currently default), you should install mbstring and ctype extensions.
    * You need GD2 support in PHP to display inline thumbnails of JPEGs ('image/jpeg: inline') with their original aspect ratio
    * When using the 'cookie' authentication method, the mcrypt extension is strongly suggested for most users and is required for 64�bit machines. Not using mcrypt will cause phpMyAdmin to load pages significantly slower.
    * To support upload progress bars, see FAQ 2.9.

	# MySQL 5.0 or newer (details);";
	echo "</div>";
	
	echo "</div>";

	echo "<div id='fragment-8'>";
	
	$colClases = consultarClases($conex, $wbasedato);
	
	echo "<ul class='simpleTree'>";
		echo "<li class='root' id='1'><span>Seleccione</span>";
			echo "<ul>";
				foreach ($colClases as $clase){
					echo "<li id='R$clase->codigo'><span>$clase->descripcion</span>";
						echo "<ul class='ajax'>";
							echo "<li id='ajax$clase->codigo'>{url:../../../include/movhos/kardex.inc.php?tree_id=1&consultaAjaxKardex=24&nivelA=$clase->codigo&basedatos=$wbasedato}</li>";					
						echo "</ul>";
					echo "</li>";
				}
			echo "</ul>";
		echo "</li>";
	echo "</ul>";
	echo "</div>";
	
	echo "<div id='fragment-10'>";
	echo "Modal jquery.. Tomando los divs de ventana modal 1<br><br>";
	
	echo "<input type='button' id='btnModalA1' name='btnModalA1' value='Modal div en pagina 1' onClick='javascript:activarModal(\"servA1\",\"a\");'>";
	echo "<br>";
	//display: none;
	echo "<div id='servA1' style='display: none;cursor: default'>"; 
	echo "<table cellpadding=1 cellspacing=1 width='100%'>";
	echo "<tr height='20' class='encabezadoTabla'>";
	echo "<td >";
    echo "<b>Opciones del servicio A</b>";
    echo "</td>";    
	echo "<td align='center'>";
    echo "<img src='../../../include/root/cerrar.gif' title='Cerrar' onclick='javascript:cerrarModal();' style='cursor:hand'>";
    echo "</td>";    
    echo "</tr>";
    echo "</tr>";
    echo "<tr><td>&nbsp;</td></tr>";
    echo "<tr><td colspan=2 class='textoNormal'>";
    
    echo "<input type='hidden' name='a' id='a'>";
    
    echo "Opcion 1&nbsp;<input type='checkbox' id='a0' name='z' value='V1'/><input type=text value='15'><br>";
    echo "Opcion 2&nbsp;<input type='checkbox' id='a1' name='z' value='V2'/><input type=text value='16'><br>";
    echo "Opcion 3&nbsp;<input type='checkbox' id='a2' name='z' value='V3'/><input type=text value='17'><br>";    
    echo "Opcion 4&nbsp;<input type='checkbox' id='a3' name='z' value='V4'/><input type=text value='18'><br>";
    echo "Opcion 5&nbsp;<input type='checkbox' id='a4' name='z' value='V5'/><input type=text value='19'><br>";

    echo "<input type='button' onClick='javascript:evaluarEnvio(\"a\");' value='Verificar'>";
    
    echo "</td></tr></table>";
	echo "</div>";
	
	echo "<input type='button' id='btnModalB1' name='btnModalB1' value='Modal Modal div en pagina 2' onClick='javascript:activarModal(\"servB1\",\"b\");'>";
	echo "<br>";
	echo "<div id='servB1' style='display: none;cursor: default'>";
	echo "<table cellpadding=1 cellspacing=1 width='100%'>";
	echo "<tr height='20' class='encabezadoTabla'>";
	echo "<td >";
    echo "<b>Opciones del servicio B</b>";
    echo "</td>";
	echo "<td align='center'>";
    echo "<img src='../../../include/root/cerrar.gif' title='Cerrar' onclick='javascript:cerrarModal();' style='cursor:hand'>";
    echo "</td>";    
    echo "</tr>";
    echo "</tr><tr><td>&nbsp;</td></tr>";
    echo "<tr><td colspan=2 class='textoNormal'>";
    
    echo "<input type='hidden' name='b' id='b'>";
    
    echo "Opcion 1&nbsp;<input type='checkbox' id='b0' name='y' value='V9' checked/><br>"; 
    echo "Opcion 2&nbsp;<input type='checkbox' id='b1' name='y' value='V10'/><br>";
    echo "Opcion 3&nbsp;<input type='checkbox' id='b2' name='y' value='V11'/><br>";
    echo "Opcion 4&nbsp;<input type='checkbox' id='b3' name='y' value='V12'/><br>";
    echo "Opcion 5&nbsp;<input type='checkbox' id='b4' name='y' value='V13'/><br>";
    echo "Opcion 6&nbsp;<input type='checkbox' id='b5' name='y' value='V14'/><br>";
    echo "Opcion 7&nbsp;<input type='checkbox' id='b6' name='y' value='V15'/><br>";
    echo "Opcion 8&nbsp;<input type='checkbox' id='b7' name='y' value='V16'/><br>";
    
    echo "<input type='button' onClick='javascript:evaluarEnvio(\"b\");' value='Verificar'>";
    
    echo "</td></tr></table>";
	echo "</div>";
	
	echo "<input type='button' id='btnModalC' name='btnModalC' value='Modal iframe 1' onClick='javascript:activarModalIframe(\"Prueba iframe\",\"nombreIframe\",\"http://www.google.com\",\"500\",\"600\");'>";
	echo "<br>";
	echo "<input type='button' id='btnModalD' name='btnModalD' value='Modal iframe 2' onClick='javascript:activarModalIframe(\"Prueba iframe\",\"nombreIframe\",\"http://200.24.5.118/matrix/f1.php\",\"500\",\"600\");'>";
	echo "<br>";

	//Hacer submit para ver como llegan los valores
	echo "<input type='submit' value='Enviar formulario'>";
	
	if(isset($a) && $a != ""){
		echo "Llego la variable a!!: ".var_dump($a);
	}
	
	if(isset($b) && $b != ""){
		echo "Llego la variable b!!: ".var_dump($b);
	}
		
//	echo "<input type='button' value='Abrir modal nativa' onClick='javascript:abrirModalNativo();'/>";
	
//	echo "<div id='dialog' title='Basic modal dialog'>";
//	echo "<p>Adding the modal overlay screen makes the dialog look more prominent because it dims out the page content.</p>";
//	echo "</div>";
	
	echo "</div>";
	
	echo "<div id='fragment-11'>";
	
	echo "<table id='demoTable' class='jTPS' style='font-family: Tahoma; font-size: 9pt; border: 1px solid #ccc;' cellspacing='1' width='700'>
        <thead>
                <tr style='cursor:hand; cursor:pointer;'>
                        <th sort='index'>ucode</th>
                        <th sort='date'>Date</th>
                        <th sort='description'>Room Type</th>
                        <th sort='beds'>Beds</th>
                        <th sort='maxGuests'>Occupancy</th>
                        <th sort='average'>Nightly Avg</th>
                        <th sort='total'>Total</th>
                        <th sort='accion'>Link</th>
                </tr>
        </thead>
          <tbody>";
	
	for($cont1 = 1;$cont1 <= 100; $cont1++){
	    echo "<tr height=35>
    	         <td align='right'>$cont1-&#x602; \u0602</td>
                 <td>$cont1-d</td>
                 <td>1 Bdrm Condo K (96 left)</td>
                 <td>1</td>
                 <td>4 max</td>
                 <td>$".($cont1+100)."</td>
                 <td align='right'>$676.00</td>
                 <td align='right'><a href='//www.google.com' target='_blank'>Prueba</a></td>
             </tr>";
	}
    
    echo "</tbody>";
    echo "<tfoot class='nav'>
                <tr>
                        <td colspan=8>
                                <div class='pagination'>P</div>
                                <div class='paginationTitle'>P&aacute;gina</div>
                                <div class='selectPerPage'>d</div>
                                <div class='status'>Mostrando</div>
                        </td>
                </tr>
        </tfoot>
</table>";
	
	echo "<br>";
	echo "<br>";
	echo "Esta es la tabla que se carga:";
	echo "<br>";
	echo "<br>";
	
	//Tabla normal
	echo "<table style='font-family: Tahoma; font-size: 9pt; border: 1px solid #ccc;' cellspacing='0' width='700'>
        <thead>
                <tr>
                        <th sort='index'>ucode</th>
                        <th sort='date'>Date</th>
                        <th sort='description'>Room Type</th>
                        <th sort='beds'>Beds</th>
                        <th sort='maxGuests'>Occupancy</th>
                        <th sort='average'>Nightly Avg</th>
                        <th sort='total'>Total</th>
                </tr>
        </thead>
        <tbody>";
	
	for($cont1 = 0;$cont1 < 40; $cont1++){
	    echo "<tr height=35>
    	         <td align='right'>$cont1-&#x602; \u0602</td>
                 <td>$cont1-d</td>
                 <td>1 Bdrm Condo K (96 left)</td>
                 <td>1</td>
                 <td>4 max</td>
                 <td>$169.00</td>
                 <td align='right'>$676.00</td>
             </tr>";
	}
    
    echo "</tbody>";
    
    echo "<tfoot class='nav'>
                <tr>
                        <td colspan=7>
                                <div class='pagination'></div>
                                <div class='paginationTitle'>Page</div>
                                <div class='selectPerPage'></div>
                                <div class='status'></div>
                        </td>
                </tr>
        </tfoot>
</table>";
	echo "</div>";
	
	echo "</div>";
}
?>
</body>
</html>