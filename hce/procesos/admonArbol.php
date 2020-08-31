<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	admonArbol.php
 * Fecha		:	2013-10-29
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Programa para gestionar el Arbol de formularios de HCE, permite editar nodos y formularios y clonar formularios.
 * Condiciones  :   
 *********************************************************************************************************
 Actualizaciones:
    2016-06-22: Arleyda Insignares C.
                -Se Desactiva Eliminado de Carpetas, solo se podran eliminar formularios
                -Se modifica la Eliminación de Formularios para que permita eliminar los clonados.
                -Por motivo de Error, Se cambia funcion 'seleccionArbol' para que muestre el Modal de configuración con la descripción
                 correspondiente y se agrega función cancelar() para limpiar los campos.
                -Se desactiva validación de servicios configurados.                 
	2016-06-07: Jessica, Se valida que las opciones tengan servicios configurados para poder eliminar y se agrega Alert indicando que 
				despues de clonar debe configurar los permisos por rol y los servicios.
	2013-12-02: Frederick, Al clonar un indice, se deja de clonar los permisos de los roles de hce_000021 y los servicios en hce_000037
 **********************************************************************************************************/
 $wactualiz = "2016-06-22";
 
if(!isset($_SESSION['user'])){
	"error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.

if(! isset($_REQUEST['action'] )){
?>
<html>
	<head>
	<title>Administracion Arbol HCE</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	
	<link rel="stylesheet" href="../../../include/root/dynatree/ui.dynatree.css" />
	<script src="../../../include/root/dynatree/jquery.dynatree.js" type="text/javascript"></script>
	
	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	
	
	<style>
		span.custom1 a
		{
			background-color: #ffffbb;
			color: maroon;
		}
		span.icono span.dynatree-icon
		{
			background-position: 0 0;
			background-image: url('../../images/medical/hce/mod.png');
		}
		
		ul.dynatree-container img {
			border-style: none;
			height: 9px;
			margin-left: 3px;
			vertical-align: middle;
			width: 9px;
		}
		
		ul.dynatree-container{
			background-color:#E8EEF7;
		}

		.caja_flotante_subir{
			position: absolute;
			width:100%;
			top: 0;
			height: 100px;
		}
		.caja_flotante_bajar{			
			width:100%;
			height: 100px;
		}
	</style>
	<script>
	//************cuando la pagina este lista...**********//
		$(document).ready(function() {
			//var posicion_query = $("#tree").offset();
			//$(".caja_flotante_bajar").css('marginTop', posicion_query.top + $(".caja_flotante_bajar").height()   );	
			$("body").height( 'auto' );		
			
			$(window).scroll(function() {				 
			   //$(".caja_flotante_bajar").css('marginTop', $(window).scrollTop() + $(window).height() - ( $(".caja_flotante_bajar").height() + 10 ) );	
				 $(".caja_flotante_subir").css('marginTop', $(window).scrollTop()  );			 
			 });		

			$('.caja_flotante_bajar').each(function () {
				var hovered = false;
				var loop = window.setInterval(function () {
					if (hovered && mouseDown) {
						posicion = $(this).offset();
						$('.dynatree-container').animate({
							scrollTop: ($(".dynatree-container").scrollTop() + 30) + "px",
						},0);
					}
				}, 100);
				$(this).hover(
					function () {
						hovered = true;
					},
					function () {
						hovered = false;
					}
				);
			});
			$('.caja_flotante_subir').each(function () {
				var hovered = false;
				var loop2 = window.setInterval(function () {
					if (hovered && mouseDown) {
						posicion = $(this).offset();
						$('.dynatree-container').animate({
							scrollTop: ($(".dynatree-container").scrollTop() - 30) + "px",
						},0);
					}
				}, 100);
				$(this).hover(
					function () {
						hovered = true;
					},
					function () {
						hovered = false;
					}
				);
			});
			//-------------------<!--PARA HACER SCROLL AUTOMATICO-->-----------------//
								
			$("body").mousedown(function() { 
			  mouseDown = true;
			});
			$("body").mouseup(function() {
			  mouseDown = false;
			});
			
			hacerDynatree();
		});
		
		var gblDatosArbol = "";
		var gblNodo;		
		var mouseDown = 0;
		var ultimoIndice = "";
		var arr_datos_drag = new Object({'node': '', 'sourceNode':'', 'hitMode':''});
		
		function hacerDynatree(){
		
		    var wemp_pmla = $("#wemp_pmla").val();
		    var desnodo ='';
			$("#tree").dynatree({
				minExpandLevel:2,
				fx: { height: "toggle", duration: 200 },
				onClick: function(node, event) {
					gblNodo = node;
					gblDatosArbol = node.data;
					// 2016-06-22 Se adiciona funcion ajax para consultar el nombre del Nodo, debido a que el objeto tree algunas veces lo devuelve
					// en blanco
				    $.post('admonArbol.php', { wemp_pmla: wemp_pmla, async: false,	action: 'consultarNodo',  wnodo: node.data.codigo, consultaAjax: ''} ,
				      function(respuesta) {
					  desnodo = $.trim(respuesta);
					  seleccionArbol( node.data,desnodo); //Cada que se le da click a un elemento del arbol, se llama a la funcion seleccionArbol con los datos del elemento
				    });
					
				},
				dnd: {
					onDragStart: function(node) {
						return true;
					},
					autoExpandMS: 1000,
					preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
					onDragEnter: function(node, sourceNode) {
						return "before";
					},
					onDragOver: function(node, sourceNode, hitMode) {
						// Prevent dropping a parent below it's own child
						if(node.isDescendantOf(sourceNode)){
							return false;
						}	
						// Prohibit creating childs in non-folders (only sorting allowed)
						if( !node.data.isFolder && hitMode === "over" ){
							return "before";
						}
					},
					onDrop: function(node, sourceNode, hitMode, ui, draggable) {
						if( node.data.nodo == 'off' && hitMode == 'over' ){	//Esta condicion es para que los formularios no se conviertan en nodos
							return false;
						}
						//Se inician los datos necesarios cuando se realiza el drag and drop, nodo destino, nodo origen y el hitMode
						arr_datos_drag.node = node;
						arr_datos_drag.sourceNode = sourceNode;
						arr_datos_drag.hitMode = hitMode;						
						$.blockUI({ message: $('#panelAlerta') }); //En el div, estan los botones de accion que van a la funcion clonarMoverNodo en caso de aceptar
						return;						
					}
				}
			}); 
		}
		
		function clonarMoverNodo(accion){
			$.unblockUI();
			node = arr_datos_drag.node;
			sourceNode = arr_datos_drag.sourceNode;
			hitMode = arr_datos_drag.hitMode;
						
			var copynode;
			var viejoIndice = sourceNode.data.codigo;			
			var indice_junto = node.data.codigo;
			var nuevoIndice = 0;
			var copiar = false;
			indice_junto = parseInt( $.trim(indice_junto) );
			nuevoIndice = indice_junto;
						
			if( accion == 'mover' ){
				var hacerScroll = false;
				if( hitMode == 'before' ){
					if( node.getParent() == sourceNode.getParent() ){
						( parseInt(viejoIndice) < parseInt(nuevoIndice) )? nuevoIndice-- : nuevoIndice;
					}
				}else{
					return;
				}
				var descripcionantes = sourceNode.data.descripcion;
				sourceNode.setTitle( "----\>"+nuevoIndice+"-"+descripcionantes );
				sourceNode.data.addClass = "custom1"; //Para "iluminar" el nodo creado
				//Se procede a mover en la ubicacion seleccionada
				sourceNode.move(node, hitMode);
			}else{
				//Clonar el indice
				copiar = true;
				var nuevadescripcion = '';
				if( hitMode != 'before' ){
					return;
				}
				var descripcionantes = sourceNode.data.descripcion;
				nuevadescripcion= "----\>"+nuevoIndice+"-"+descripcionantes;

				//Se procede a copiar en la ubicacion seleccionada
				if(sourceNode) {
					copynode = sourceNode.toDict(true, function(dict){
						dict.title = nuevadescripcion;
						dict.addClass = 'custom1'; //Para resaltarlo
						delete dict.key; // Remove key, so a new one will be created
					});
				}else{
					return false;
				}			
				node.parent.addChild(copynode, node);				
			}			
			setTimeout( function(){
				if( confirm("Desea guardar el cambio?") ){
					var tipo = ( copiar == true ) ? "clonar" : "mover";
					guardarCambioIndice(tipo, viejoIndice, nuevoIndice);
					ultimoIndice = nuevoIndice;
				}else{
					ultimoIndice = nuevoIndice;
					recargarArbol(nuevoIndice);
				}
			}, 1500 );
		}
		
		function abrirPanelConf(){
			$.blockUI({ message: $('#panelconfiguracion') }); 
		}
		
		function guardarCambioIndice(wtipocambio, indiceviejo, indicenuevo){

			var wemp_pmla = $("#wemp_pmla").val();
			var accion = "moverindice";		
			if( wtipocambio == "clonar" ){
				accion = "clonarindice";
			}
			
			if( $('#msjEspere').length == 0 ){
				construirDivEsperar();
			}
			$.blockUI({ message: $('#msjEspere') });
			$.post('admonArbol.php', { wemp_pmla: wemp_pmla, action: accion,  indicenuevo: indicenuevo,
										indiceviejo: indiceviejo,consultaAjax: ''} ,
				function(data) {
					$.unblockUI();
					data = $.trim(data);
					if( data == 'OK' ){
						
						// 2016-06-07: Se agrega Alert indicando que despues de clonar debe configurar los permisos por rol y los servicios.
						if( wtipocambio == "clonar" ){
							jAlert("Debe registrar los permisos por rol y los servicios para que quede configurado correctamente","ALERTA");
						}
						
						restablecer_pagina();
						recargarArbol(indicenuevo);						
						window.parent.parent.frames[2].location = $('#resetear_lista_formularios').val(); //Recargar arbol del panel de configuracion
					}else{
						alert("Error en la respuesta:\n "+data);
					}
				}
			);
		}
		
		function seleccionArbol( datos,desnodo ){
			restablecer_pagina();
			$("#wnompro").val(desnodo);
			$("#windice").val( datos.codigo );
			$("#wencpro").attr("checked", false );
			$("#westado").attr("checked", false );			
		}
		
		function recargarArbol( indiceMostrar ){

			var wemp_pmla = $("#wemp_pmla").val();
			if( $('#msjEspere').length == 0 ){
				construirDivEsperar();
			}
			$.blockUI({ message: $('#msjEspere') });
			$.post('admonArbol.php', { wemp_pmla: wemp_pmla, action: "cargarArbol", indicemostrar: indiceMostrar, consultaAjax: ''} ,
				function(data) {
					$.unblockUI();
					$("#tree").dynatree("destroy"); //Obligatorio, si no se destruye primero no se puede volver a crear
					$("#tree").html( data );
					hacerDynatree();
					/*ultimoIndice = "10905";
					setTimeout( function(){
						var node = $("#tree").dynatree("getTree").selectKey( ultimoIndice );
						if( node != null ){
							node.visitParents( function (node) {
							   node.toggleExpand();
							}, true);
						}else{
							alert("nodo null:-"+ultimoIndice+"-");
						}
					},5000 );*/
				}
			);
		}
		
        function cancelar(){
            $.unblockUI();
        	$("#wnompro").val('');
        	$("#windice").val('');
			var checkedmodal = ( datos.modal == 'on' ) ? true : false;
			$("#wencpro").attr("checked", checkedmodal );
			var checkedestado = ( datos.estado == 'on' ) ? true : false;
			$("#westado").attr("checked", checkedestado );			
        }

		function guardar(){
			var descripcion = $("#wnompro").val();
			var esModal = $("#wencpro").is(':checked');
			var estado = $("#westado").is(':checked');
			var wemp_pmla = $("#wemp_pmla").val();
			
			if( descripcion == '' ){
				return;
			}
			( esModal == true ) ? esModal = 'on' : esModal = 'off';
			( estado == true ) ? estado = 'on' : estado = 'off';
			if( $('#msjEspere').length == 0 ){
				construirDivEsperar();
			}
			$.blockUI({ message: $('#msjEspere') });
			$.post('admonArbol.php', { wemp_pmla: wemp_pmla, action: "guardar", descripcion: descripcion, estado: estado, esmodal: esModal,
										indice: gblDatosArbol.codigo, url: gblDatosArbol.url, nodo: gblDatosArbol.nodo, consultaAjax: ''} ,
				function(data) {
					$.unblockUI();
					data = $.trim(data);
					if( data == 'OK' ){									
						var tituloMostrar = gblNodo.data.title.replace(gblNodo.data.descripcion ,descripcion);
						gblNodo.data.descripcion = descripcion;		
						gblNodo.setTitle( tituloMostrar );
						gblNodo.data.modal = esModal;
						gblNodo.data.estado = estado;
						restablecer_pagina();
						window.parent.parent.frames[2].location = $('#resetear_lista_formularios').val(); //Recargar arbol del panel de configuracion
					}else{
						alert("Error en la respuesta:\n "+data);
					}
				}
			);
		}

		function eliminarNodo( nodo ){
			setTimeout( function(){
				var confirma = confirm("Desea eliminar el indice "+nodo+" - "+gblDatosArbol.descripcion+"?");
				if( !confirma ) return;
				
				var wemp_pmla = $("#wemp_pmla").val();
				
				if( $('#msjEspere').length == 0 ){
					construirDivEsperar();
				}
				$.blockUI({ message: $('#msjEspere') });
				$.post('admonArbol.php', { wemp_pmla: wemp_pmla, action: 'eliminarNodo',  indice: nodo, consultaAjax: ''} ,
					function(data) {
						$.unblockUI();
						data = $.trim(data);
						if( data == 'OK' ){
							restablecer_pagina();
							recargarArbol(nodo);
							window.parent.parent.frames[2].location = $('#resetear_lista_formularios').val(); //Recargar arbol del panel de configuracion
						}else{
							alert("Error en la respuesta:\n "+data);
						}
					}
				);	
			}, 100 );
		}
		
		function restablecer_pagina(){			
			$("#indice_menu_selectclon, #indice_menu_select").html( $("#opcionesindiceoriginal").html() );
			$("#windmenclon, #windmen, #wnompro").val("");
			$("#indice_menu_nuevo, #indice_menu_nuevoclon").html("");
			$("#wencpro, #checkclonar").attr("checked", false );
		}
		
		function construirDivEsperar(){
			var html_codigo = "<div id='msjEspere' style='display:none;'>";
			html_codigo+= '<br>';
			html_codigo+= "<img src='../../images/medical/ajax-loader5.gif'/>";
			html_codigo+= "<br><br> Por favor espere un momento ... <br><br>";
			html_codigo+= '</div>';
			$("#wemp_pmla").after(html_codigo);	
		}
		
		function alerta( txt ){
			$("#textoAlerta").text( txt );
			$.blockUI({ message: $('#msjAlerta') });
				setTimeout( function(){
								$.unblockUI();
							}, 1600 );
		}
	</script>

	</head>
    <body>
<?php
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");
include_once("movhos/movhos.inc.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$whcebasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == 'guardar' ){
		guardar( $_REQUEST['nodo'], $_REQUEST['url'], $_REQUEST['descripcion'], $_REQUEST['indice'], $_REQUEST['esmodal'], $_REQUEST['estado'] );
	}else if( $action == 'cargarArbol' ){
		dibujarArbol(@$_REQUEST['indicemostrar']);
	}else if($action == 'clonarindice' ){
		clonar($_REQUEST['indiceviejo'], $_REQUEST['indicenuevo']);
		echo "OK";
	}else if($action == 'moverindice' ){
		ordenar_indices($_REQUEST['indicenuevo'], $_REQUEST['indiceviejo']);
		echo "OK";
	}else if( $action == 'eliminarNodo' ){
		eliminar_nodo( $_REQUEST['indice'] );
		echo "OK";
	}else if( $action == 'consultarNodo' ){
		consultar_Nodo( $_REQUEST['wnodo'] );
	}	

	return;
}
//FIN*LLAMADOS*AJAX******************************************************************

    function consultar_Nodo($wnodo)
    {

    	global $conex;
		global $wbasedato;

    	$wdescripcion = '';
        $q  = "  SELECT Precod,Predes
			  FROM ".$wbasedato."_000009
			  WHERE Precod = '".$wnodo."' ";

	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
	    if ($num > 0){
		    $row = mysql_fetch_assoc($res);
		    $wdescripcion = $row['Predes'];
	    }  		

	    echo $wdescripcion;
    }
    

	function consultarTiponodo($opcion)
	{
		global $conex;
		global $wbasedato;	
		$activareliminado = "off";
		
		//Se Adiciona control para Evitar la eliminación de Carpetas que tengan formularios hijos
		$qServic = "  SELECT COUNT(Precod)
				FROM ".$wbasedato."_000009
			   WHERE Precod LIKE '".$opcion."%' ";

		/*$qServic = "  SELECT COUNT(Precod)
						FROM ".$wbasedato."_000009
				   LEFT JOIN ".$wbasedato."_000037
						  ON ".$wbasedato."_000009.Precod = ".$wbasedato."_000037.Forcod
					   WHERE Precod LIKE '".$opcion."%'
						 AND  ISNULL(Forcod)
						 AND  ISNULL(Forser)
						 AND  ISNULL(Forest)";*/
		
		$resServic=  mysql_query($qServic,$conex) or die ("Error talhuma_000013: ".mysql_errno()." - en el query: ".$qServic." - ".mysql_error());
		$rowServic = mysql_fetch_array($resServic);
		
		if($rowServic[0] > 1)
		{
			$activareliminado = "on";
		}

		// Verificar el campo Prenod para identificar si es una carpeta
		$qNodo = "  SELECT Prenod
				FROM ".$wbasedato."_000009
			   WHERE Precod LIKE '".$opcion."%' AND Prenod='on'";
 		
 		$res = mysql_query($qNodo,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qNodo." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0)
		{
			$activareliminado = "on";
		}

		return $activareliminado;
	}
	
	function eliminar_nodo( $windice ){
		global $conex;
		global $wbasedato;	
		
		$q = " DELETE FROM ".$wbasedato."_000009 "
			  ."  WHERE Precod = '".$windice."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$q = " DELETE FROM ".$wbasedato."_000021 "
			  ."  WHERE Rararb = '".$windice."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query:  ".$q." - ".mysql_error());
		
		$q = " DELETE FROM ".$wbasedato."_000037 "
			  ."  WHERE Forcod = '".$windice."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$indice = $windice;
		if( strlen( $windice ) > 2  ){
			$indice = substr($windice, 0, -2);
		}
		
		//Se buscan todos los formularios o nodos que comiencen con el prefijo del indice viejo
		$q4 = "SELECT Precod as codigo "
			."   FROM ".$wbasedato."_000009 "
			."  WHERE Precod regexp '^".$indice."'"
			." ORDER BY 1";	
			
		$res4 = mysql_query($q4,$conex);	
		while( $row4 = mysql_fetch_assoc($res4) ){
			$codigo = intval($row4['codigo']);
			if( $codigo > intval($windice) && strlen($row4['codigo']) == strlen($windice) ){
				$q1 = "UPDATE ".$wbasedato."_000009 SET precod = REPLACE(precod, '".$codigo."', '".($codigo-1)."') WHERE precod REGEXP '^".$codigo."'";
				$res1 = mysql_query($q1,$conex);
				$q2 = "UPDATE ".$wbasedato."_000021 SET rararb = REPLACE(rararb, '".$codigo."', '".($codigo-1)."') WHERE rararb REGEXP '^".$codigo."'";
				$res2 = mysql_query($q2,$conex);
				$q3 = "UPDATE ".$wbasedato."_000037 SET forcod = REPLACE(forcod, '".$codigo."', '".($codigo-1)."') WHERE forcod REGEXP '^".$codigo."'";
				$res3 = mysql_query($q3,$conex);
			}
		}	  
	}
	
	//$wformulario es el campo preurl, si es un nodo su valor es 'on', en caso contrario su valor es F=000XYZ
	function guardar( $esNodo, $wformulario, $wnompro, $windmen, $wencpro, $westado ){
		global $conex;
		global $wbasedato;		
	
		$wformulario = str_replace("F=", "", $wformulario);
		$wnompro = utf8_decode( $wnompro );		
		
		$wherePreurl = " Preurl = 'F=".$wformulario."'";
		if( $esNodo == 'on' ) $wherePreurl = " Prenod = 'on'";
		
		$q = "UPDATE ".$wbasedato."_000009 "
		." 		 SET Predes = '".$wnompro."', Premod= '".$wencpro."', Preest='".$westado."'"
		."     WHERE ".$wherePreurl
		."       AND Precod = '".$windmen."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		
		if( $esNodo == 'off' ){
		
			$q = " UPDATE ".$wbasedato."_000001 "
			."        SET Encdes = '".$wnompro."'"
			."      WHERE Encpro = '".$wformulario."'";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		
			$q = " UPDATE formulario "
			." 		  SET nombre = '".$wnompro."' "
			."  	WHERE medico = '".$wbasedato."'"
			."    	  AND codigo = '".$wformulario."'";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		}
		echo "OK";
	}
	
	function clonar($windmen, $windclonar){
		global $conex;	  
		global $wbasedato;
		
		if( $windclonar == '' )
			return;
		$indicemen = -1; $indiceclon = -2;
		if( strlen( $windmen ) > 2  ){
			$indicemen = substr($windmen, 0, -2); //$indice = 101
		}
		if( strlen( $windclonar ) > 2  ){
			$indiceclon = substr($windclonar, 0, -2); //$indice = 101
		}
				
		if( $indicemen == $indiceclon && intval( $windmen ) > intval( $windclonar ) ){
			$windmen = intval( $windmen );
			$windmen++;
		}
		
		ordenar_indices( $windclonar );
		//Inserto el formulario en la opciones del menu
		$q= " INSERT INTO ".$wbasedato."_000009 (   Medico       ,   Fecha_data ,   Hora_data,  					 precod     						,	 predes     , prenod ,     preurl         , preest,    premod     , seguridad        ) "
		   ."                            SELECT 	Medico       ,   Fecha_data ,   Hora_data,   REPLACE(precod, '".$windmen."', '".$windclonar."')      ,   predes     , prenod ,     preurl         , preest,    premod     , seguridad         "
		   ." 							   FROM ".$wbasedato."_000009 "
		   ." 							  WHERE Precod REGEXP '^".$windmen."'";
		$res = mysql_query($q,$conex);

		
		//Inserto el formulario en la opciones del menu
		//2013-12-02 ---->No clonar los permisos de los roles
		/*$q= " INSERT INTO ".$wbasedato."_000021 (   Medico       ,   Fecha_data ,   Hora_data,   rarcod,   					rararb    							  ,      rarcon, rargra   , rarimp , rarpro, rarest, rarusu, Seguridad   ) "
		   ."                            SELECT 	Medico       ,   Fecha_data ,   Hora_data,   rarcod,  REPLACE(rararb, '".$windmen."', '".$windclonar."')      ,      rarcon, rargra   , rarimp , rarpro, rarest, rarusu, Seguridad    "
		   ." 							   FROM ".$wbasedato."_000021 "
		   ." 							  WHERE rararb REGEXP '^".$windmen."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());*/
		//Inserto el formulario en la opciones del menu
		//2013-12-02 ---->No clonar los servicios
		/*$q= " INSERT INTO ".$wbasedato."_000037 (   Medico       ,   Fecha_data ,   Hora_data,  					 forcod     					     ,   forser     , forest ,	 seguridad        ) "
		   ."                            SELECT 	Medico       ,   Fecha_data ,   Hora_data,   REPLACE(forcod, '".$windmen."', '".$windclonar."')      ,   forser     , forest ,	 seguridad         "
		   ." 							   FROM ".$wbasedato."_000037 "
		   ." 							  WHERE forcod REGEXP '^".$windmen."'";*/
		//2013-12-02 ---->Se clona con * por defecto
		$q= " INSERT INTO ".$wbasedato."_000037 (   Medico       ,   Fecha_data ,         Hora_data,          forcod     , Forser, Forest , seguridad        ) "
		."                            VALUES ('".$wbasedato."','".date('Y-m-d')."' ,'".date('H:i:s')."','".$windclonar."',  '*'   , 'off'  , 'C-".$wusuario."') ";
		$res = mysql_query($q,$conex);
	}
	
	function ordenar_indices( $windmen, $indice_viejo = -1 ){
		global $conex;
		global $wbasedato;
		
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

	function dibujarArbol($indice_a_mostrar=''){
	
		global $wemp_pmla;
		global $wbasedato;
		global $conex;
		
		$caracteres = array( "á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü","\\","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??","?£", "°","?");
		$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-" ,"a","e","i","o","u","A","E","I","O","U","A","S","", "", "N", "N", "U", "", "");
		$repetidosEnArbol = array();
		$query = "SELECT count(*) as cant, REPLACE(Preurl,'F=','') as url
					FROM ".$wbasedato."_000009
				GROUP BY preurl
				HAVING cant > 1";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0){
			while( $row = mysql_fetch_assoc($err) ){
				array_push( $repetidosEnArbol, $row['url'] );
			}
		}
		$query = "SELECT Precod as codigo, Predes as descripcion, REPLACE(Preurl,'F=','') as url, Prenod as nodo, Premod as modal, Preest as estado
					FROM ".$wbasedato."_000009 				  
				ORDER BY Precod ";

		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0){
			$arrTodosDatos = array();
			$arrArbol = array();
			$arrNodos = array(); //Aqui se apila el length del codigo del nodo
			$lengthCodigo = -1;
			while( $row = mysql_fetch_assoc($err) ){
				$row['descripcion']=utf8_encode($row['descripcion']);
				array_push( $arrTodosDatos, $row );
			}

			$arrArbol["1"] = $arrTodosDatos[0];
			$nivel = 3;
			agregarHijos("1", $nivel, $arrArbol["1"]['hijos'], $arrTodosDatos, $repetidosEnArbol);
			
			echo "<ul id='treeData' >";
			$htmlcode = imprimirListaHijos( $arrArbol, $sonCopias, $indice_a_mostrar );
			echo $htmlcode;
			echo "</ul>";
		
			$arrNodos = array(); //Aqui se apila el length del codigo del nodo
			$lengthCodigo = -1;
			/*echo "<ul id='treeData' >";
			foreach($arrTodosDatos as $row ){
				$row['descripcion'] = htmlentities( $row['descripcion'] );
				$lengthCodigo = strlen( $row['codigo'] );
				while( $lengthCodigo <= end($arrNodos) ){ //Cerrar ul y desapilar
					echo "</ul>";
					array_pop( $arrNodos );
				}
				$row['key'] = $row['codigo'];
				$json_data = json_encode( $row );
				if( $row['nodo'] == "on" ){
					$claseexpanded = "";
					if( $lengthCodigo <= strlen( $indice_a_mostrar ) ){
						if( $row['codigo'] == substr($indice_a_mostrar, 0, $lengthCodigo) )
							$claseexpanded = "expanded";
					}
					echo "<li class='folder ".$claseexpanded."' data='".$json_data."'>".$row['codigo']." - ".$row['descripcion']."<img src='../../images/medical/hce/mod.png' width='9px' height='9px' onclick='abrirPanelConf()' />
							<ul>";
					array_push( $arrNodos, strlen( $row['codigo'] ) );
				}else{
					$img_eliminar = "";
				    if( in_array($row['url'], $repetidosEnArbol) )
						$img_eliminar = "<img src='../../images/medical/hce/del.png' width='9px' height='9px' onclick='eliminarNodo(\"".$row['codigo']."\")' />";
					echo "<li data='".$json_data."'>".$row['codigo']." - ".$row['descripcion']."<img src='../../images/medical/hce/mod.png' width='9px' height='9px' onclick='abrirPanelConf()' />".$img_eliminar;
				}
			}
			echo "</ul>";*/
		}
	}
	
	function agregarHijos($codigoNodo, $nivel, &$arrParaGuardar, $arrTodosDatos, $hojasRepetidas){
		foreach( $arrTodosDatos as $dato ){
			$aux = substr($dato['codigo'], 0, strlen($codigoNodo));			
			if( $aux == $codigoNodo && strlen($dato['codigo']) == $nivel ){
				if( $dato['nodo'] == "on" ){
					$arrParaGuardar[$dato['codigo']] = $dato;					
					agregarHijos($dato['codigo'], $nivel+2, $arrParaGuardar[$dato['codigo']]['hijos'], $arrTodosDatos,$hojasRepetidas);
				}else{
					( in_array($dato['url'], $hojasRepetidas) )? $dato['tieneclon'] = "on" : $dato['tieneclon'] = "off";
					$arrParaGuardar[ $dato['codigo'] ] = $dato;
				}
			}
		}
		return;
	}
	
	function imprimirListaHijos( $arrPadre, &$todosCopia, $indice_a_mostrar ){
		$todosCopia = true;
		
		$codigoHtml = "";
		foreach( $arrPadre as $nodoHijo ){
			$claseexpanded = '';
			$json_data = json_encode( $nodoHijo );
			$img_eliminar = "";										
			if( $nodoHijo['nodo'] == "on" ){
				if( count($nodoHijo['hijos']) > 0 ){
					$lengthCodigo = strlen( $nodoHijo['codigo'] );
					if( $lengthCodigo <= strlen( $indice_a_mostrar ) && $indice_a_mostrar != ''){
						if( $nodoHijo['codigo'] == substr($indice_a_mostrar, 0, $lengthCodigo) )
							$claseexpanded = "expanded";
					}					
					$todosCopiaaux = true;
					$html_res = imprimirListaHijos( $nodoHijo['hijos'], $todosCopiaaux, $indice_a_mostrar );
					if( $todosCopiaaux == true )
						$img_eliminar = "<img src='../../images/medical/hce/del.png' width='9px' height='9px' onclick='eliminarNodo(\"".$nodoHijo['codigo']."\")' />";													
					if( $todosCopiaaux == false )
						$todosCopia = false;
					
					// 2016-06-07: Se valida que las opciones tengan nodos hijos configurados para poder eliminar.
					if($img_eliminar != "")
					{
						$eliminarcarpeta = consultarTiponodo($nodoHijo['codigo']);
						
						if($eliminarcarpeta == "on")
						{
							$img_eliminar = "";	
						}
					}
					
					
					$codigoHtml.= "<li class='folder ".$claseexpanded."' data='".$json_data."'>".$nodoHijo['codigo']." - ".utf8_decode($nodoHijo['descripcion'])."<img src='../../images/medical/hce/mod.png' width='9px' height='9px' onclick='abrirPanelConf()' />".$img_eliminar;
					$codigoHtml.= "<ul>";
					$codigoHtml.= $html_res;
					$codigoHtml.= "</ul>";
				}else{
					$img_eliminar = "<img src='../../images/medical/hce/del.png' width='9px' height='9px' onclick='eliminarNodo(\"".$nodoHijo['codigo']."\")' />";								
					
					// 2016-06-07: Se valida que las opciones tengan nodos hijos configurados para poder eliminar.
					if($img_eliminar != "")
					{
						$eliminarcarpeta = consultarTiponodo($nodoHijo['codigo'],"nodo");
						
						if($eliminarcarpeta=="on")
						{
							$img_eliminar = "";	
						}
					}
					
					$codigoHtml.= "<li class='folder' data='".$json_data."'>".$nodoHijo['codigo']." - ".utf8_decode($nodoHijo['descripcion'])."<img src='../../images/medical/hce/mod.png' width='9px' height='9px' onclick='abrirPanelConf()' />".$img_eliminar;							
				}
			}else{
				if( $nodoHijo['tieneclon'] == 'on' ){
					$img_eliminar = "<img src='../../images/medical/hce/del.png' width='9px' height='9px' onclick='eliminarNodo(\"".$nodoHijo['codigo']."\")' />";				
				}else{
					$img_eliminar = "<img src='../../images/medical/hce/del.png' width='9px' height='9px' onclick='eliminarNodo(\"".$nodoHijo['codigo']."\")' />";

					//$todosCopia = false;  // 2016-06-22 Se desactiva esta validacion porque un formulario clonado tambien puede ser eliminado
				}
				
				// 2016-06-07: Se valida que las opciones tengan nodos hijos, de ser así no de podrá eliminar.
				if($img_eliminar != "")
				{
					$eliminarcarpeta = consultarTiponodo($nodoHijo['codigo'],"nodo");
					
					if($eliminarcarpeta=="on")
					{
						$img_eliminar = "";	
					}
				}
				
				$codigoHtml.= "<li data='".$json_data."'>".$nodoHijo['codigo']." - ".utf8_decode($nodoHijo['descripcion'])."<img src='../../images/medical/hce/mod.png' width='9px' height='9px' onclick='abrirPanelConf()' />".$img_eliminar;
			}
		}
		return $codigoHtml;
	}
	
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		global $wbasedato;
		global $conex;
		
		$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
		$empresa = strtolower($institucion->baseDeDatos);
		encabezado("ADMINISTRACION DEL ARBOL HCE", $wactualiz, $empresa );				
	
		echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."' />";		
		echo "<input type='hidden' id='resetear_lista_formularios' value='configuracion.php?accion=F&ok=0&wemp_pmla=".$wemp_pmla."' />";
		
		echo "<div id='tree' style='width:600px; height:430px; margin-left:350px;'>";
		dibujarArbol();		
		echo "</div>";	
		echo '<div class="caja_flotante_bajar" style="align:center;"></div>';
		
		echo "<div id='panelconfiguracion' style='display: none;'>";
		echo "<table>";
		echo "<tr class='encabezadotabla'>
				<td colspan=2 align='center'><font size=4>CONFIGURACION</font></td>
			</tr>";
		echo "<tr class='fila1'>";
		echo "<td><b>Indice</b></td>";
		echo "<td><input name='windice' id='windice' type='text' value='' disabled></td>";
		echo "</tr>";
		echo "<tr class='fila1'>";
		echo "<td><b>Nombre</b></td>";
		echo "<td><input name='wnompro' id='wnompro' type='text' size='54' value=''></td>";
		echo "</tr>";
		echo "<tr class='fila1'>";
		echo "<td><b>Es modal</b></td>";
		echo "<td><input name='wencpro' id='wencpro' type='checkbox'></td>";
		echo "</tr>";
		echo "<tr class='fila1'>";
		echo "<td><b>Estado</b></td>";
		echo "<td><input name='westado' id='westado' type='checkbox'></td>";
		echo "</tr>";
		echo "<tr><td class='fila1' align='center' colspan=2>
				<input type='button' value='Guardar' onclick='guardar()' />&nbsp;&nbsp;&nbsp;
				<input type='button' value='Cancelar' onclick='cancelar()' />
			</td></tr>";

		echo "</table>";	
		echo "</div>";
		
		echo "<div id='panelAlerta' style='display: none;'>";
		echo "<table>";
		echo "<tr class='encabezadotabla'>
				<td colspan=2 align='center'><font size=4>ALERTA</font></td>
			</tr>";
		echo "<tr class='fila1'>";
		echo "<td align='center' colspan=2 ><b>Por favor seleccione la accion que desea realizar</b></td>";
		echo "</tr>";
		echo "<tr><td class='fila1' align='center' colspan=2>
				<input type='button' value='Clonar' onclick='clonarMoverNodo(\"clonar\")' />&nbsp;&nbsp;&nbsp;
				<input type='button' value='Mover' onclick='clonarMoverNodo(\"mover\")' />&nbsp;&nbsp;&nbsp;
				<input type='button' value='Cancelar' onclick='$.unblockUI();' />
			</td></tr>";

		echo "</table>";	
		echo "</div>";
		echo "<br><br>";
		echo '<div align="center">';
		echo "<a id='enlace_retornar' onclick='restablecer_pagina()' href='#' >RETORNAR</a><br><br>";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "<br><br>"; 
		echo "<br><br>";
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		//Mensaje de alertas
		echo "<div id='msjAlerta' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/root/Advertencia.png'/>";
		echo "<br><br><div id='textoAlerta'></div><br><br>";
		echo '</div>';
		echo '</div>';
		echo '<div class="caja_flotante_subir" style="align:center;"></div>';
		
	}

	vistaInicial();
?>
    </body>
</html>