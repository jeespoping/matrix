<?php
include_once("conex.php");
/***********************************************************************************************************
 * Programa		:	Administraci�n de empresas hce
 * Fecha		:	2013-06-14
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	
 * Condiciones  :  
 ***********************************************************************************************************
 
 Actualizaciones:
 2016-06-17:  Arleyda Insignares C. Se Adiciona en tabla 'Entidades' opci�n para adicionar Nit a la 
             agrupaci�n seleccionada    
			
 **********************************************************************************************************/
 
$wactualiz = "2016-06-17";
 
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

if( isset($consultaAjax) == false ){
	
?>
	<html>
	<head>
	<title>Administrador empresas HCE</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
			$("#lista_agrupaciones option").eq(0).attr('selected',true); 
		});
		
		$("#lista_agrupaciones").change(function() {
			realizarConsultaEntidades();
		});		
		$("#entidades input").attr("disabled",true);		
		$("#tr_estado_agrupacion").hide();		
		$("#div_tabla_filtros").hide();
		
		//Cuando presionen la tecla Enter dentro del input "buscar"
		$("#buscador").on("keyup", function(e) {
			if(e.which == 13){
				filtrarConBusqueda( $(this).val() );				
			}
		});
	});
	
	function guardar_estado_agrupacion( obj ){
		var codigoGrupo = $("#lista_agrupaciones").val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		obj = jQuery(obj);
		
		estado = obj.is(':checked');
		if( estado == true )
			estado='on';
		else
			estado='off';
		
		if( codigoGrupo == "" ){
			return;
		}
		$.blockUI({ message: $('#msjEspere') });
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.get('EmpresasHCE.php', { wemp_pmla: wemp_pmla, action: "guardarEstadoAgrupacion", grupo: codigoGrupo, estado:estado, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				if( data == 'OK' ){
					$("#lista_agrupaciones option:selected").attr('estado', estado); //Le cambio el atributo "estado" a la opcion del select
					if( estado == 'on' )
						alerta("La agrupaci�n ha sido ACTIVADA");
					else
						alerta("La agrupaci�n ha sido INACTIVADA");					
				}else{
					alert('Error al cambiar el estado de la agrupaci�n \n '+data);
				}
			});
	}
	
	function eliminar_agrupacion(){
		
	}
	
	function filtrarConBusqueda( valor ){
		
		valor = $.trim( valor );
		valor = valor.toUpperCase();
		if( valor.length < 4 ){
			alerta("Ingrese al menos 4 caracteres para realizar la busqueda");
			return;
		}
		$("#div_tabla_filtros").show();
		
		$.blockUI({ message: $('#msjEspere') });
		
		var identificaciones_entidades = new Array();
		
		var patt1 = new RegExp( valor , "g" );

		$('.parabuscar').each(function(){
			texto = $(this).text();
			texto = $.trim(texto);		
			if ( patt1.test( texto ) ) {				
				identificador = $(this).attr("empresa");
				if( inArray( identificador, identificaciones_entidades) == false) {
					identificaciones_entidades.push( identificador );
				}
			}
		});
		var i=0;
		var html_codigo = "";
		for( i in identificaciones_entidades ){
			html_codigo+="<tr class='fila1'>";
			$("#entidades [empresa="+identificaciones_entidades[i]+"]").each(function(){
				html_codigo+="<td empresa='"+identificaciones_entidades[i]+"'>"+$(this).html()+"</td>";
			});
			html_codigo+="</tr>";		
		}
		$("#tabla_filtros tr:not(.noborrar)").remove();
		$("#tabla_filtros").append( html_codigo );
		
		$("#tabla_filtros input:checked").parent().addClass('fondoAmarillo');
		$("#tabla_filtros input:checked").parent().next().addClass('fondoAmarillo');
		$("#tabla_filtros input:checked").parent().next().next().addClass('fondoAmarillo');
		$("#tabla_filtros input:checked").parent().next().next().next().addClass('fondoAmarillo');
		$.unblockUI();
	}
	
	//Funcion que se activa cuando se presiona el enlace "retornar"
	function restablecer_pagina(){
		//$("#lista_agrupaciones option").eq(0).attr('selected',true); 
		$("#entidades input").attr("disabled",true);
		$("#tabla_filtros input").attr("disabled",true);
		$("#form_nueva_agrupacion").hide( 'drop', {}, 500 ); 
		$("#enlace_retornar").hide(); 
		$("#entidades .entidad").attr('checked', false );
		$("#tabla_filtros .entidad").attr('checked', false );
		$("#entidades .fondoAmarillo").removeClass('fondoAmarillo');
		$("#tabla_filtros .fondoAmarillo").removeClass('fondoAmarillo');
		$("#tr_estado_agrupacion").hide();
		borrarEntidadesElegidas();
		borrarEntidadesFiltradas();
	}
	
	//funcion que luego de elegir el centro de costos, me trae los pacientes que se encuentran en el
	function realizarConsultaEntidades(){
	
		var wemp_pmla = $("#wemp_pmla").val();
		var codigoGrupo = $("#lista_agrupaciones").val();
		restablecer_pagina();
		
		if( codigoGrupo == "" ){
			$("#entidades input").attr("disabled",true);
			$("#tabla_filtros input").attr("disabled",true);
			$("#tr_estado_agrupacion").hide();
			return;
		}
		$("#buscador").focus();
		var estado = $("#lista_agrupaciones option:selected").attr('estado');
		if( estado == 'on')
			$("#grupo_activo").attr("checked",true);
		else
			$("#grupo_activo").attr("checked",false);
		
		$("#tr_estado_agrupacion").show();
		
		$("#entidades input").attr("disabled",false);
		$("#tabla_filtros input").attr("disabled",false);
		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		$("#enlace_retornar").fadeIn('slow');
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.get('EmpresasHCE.php', { wemp_pmla: wemp_pmla, action: "mostrarListaEntidades", grupo: codigoGrupo, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				parsearEntidades( data );
			});
	}	
	
	function parsearEntidades( entidades ){
		if( isJson( entidades ) == false ){
			alerta("No se cargaron los datos del grupo");
			restablecer_pagina();
			return;
		}
		borrarEntidadesElegidas();
		
		entidades = eval( '('+entidades+')' );
		
		$.each( entidades, function(ind, val){
			val = $.trim( val );
			$("input[value="+val+"]").attr("checked",true);
			var empresa = $("#entidades input[value="+val+"]").parent().attr("empresa");
			$("[empresa="+empresa+"]").addClass('fondoAmarillo');
			agregarEntidadElegidas( empresa );
		});	
		nueva_Entidad();
	}
	
	function elegir_entidad( ele ){
		ele = jQuery( ele );
		var valor = ele.val();
		var empresa = ele.parent().attr("empresa");
		if( ele.is(':checked') ){
			$("[empresa="+empresa+"]").addClass('fondoAmarillo');
			agregarEntidadElegidas( empresa );
			$("input[value="+valor+"]").attr("checked", true);	
		}else{
			$("[empresa="+empresa+"]").removeClass('fondoAmarillo');
			quitarEntidadElegidas( empresa );
			$("input[value="+valor+"]").attr("checked", false);
		}
	}
	
	function agregarEntidadElegidas( codigo_empresa ){
	
			html_codigo ="<tr class='fila1' empresaelegida="+codigo_empresa+">";
			$("#entidades [empresa="+codigo_empresa+"]").each(function(){
					html_codigo+="<td class='fondoAmarillo' empresa='"+codigo_empresa+"' >"+$(this).html()+"</td>";					
			});
			html_codigo+="</tr>";
			$("#tabla_seleccionadas").append( html_codigo );
	}
	
	function quitarEntidadElegidas( codigo_empresa ){
		$("#tabla_seleccionadas").find("tr[empresaelegida="+codigo_empresa+"]").remove();
	}
	

	function nueva_Entidad(){
		var totcolumna = document.getElementById('entidades').rows[0].cells.length;  
		html_codigo = "<tr class='fila1'><td class='fondoAmarillo'><input type='checkbox' class='entidadnue' id='chknuentidad' name='chknuentidad' onclick='elegir_entidad(this)' CHECKED></td><td class='parabuscar'><input type='text' id='txtnuentidad' empresa='adicion' onblur='BuscarEntidad(this.value)'></td><td><input type='text' id='txtdesentidad' name='txtdesentidad' size='40px' readonly></td><td><input type='text' id='txtnitentidad' name='txtnitentidad' readonly></td></tr> " ;
	    $("#tabla_seleccionadas").append(html_codigo);
	}

   function BuscarEntidad(objvalor){
   	    if (objvalor!='')
   	    {
   	    	// Buscar si el c�digo ya existe en la tabla de Entidades
   	    	var vencontro= 0;
   	    	$("#entidades").find(".entidad:checked").each(function(){
			
				cadena= $(this).val();
				if(objvalor==cadena)
				{ alerta('El codigo ya se encuentra seleccionado');
                  vencontro=1;
		        }
		    });
		    if (vencontro==1){
		    	$("#txtnuentidad").val('');
		        return;
		        }
			
	        //Realiza el llamado ajax con los parametros de busqueda para traer nit y descripcion de la Entidad
			var wemp_pmla = $("#wemp_pmla").val();
			$.get('EmpresasHCE.php', { consultaAjax:  true, wemp_pmla: wemp_pmla, action: "consultarEntidad", codentidad: objvalor} ,
			function(data) {
				$.unblockUI();
				data = $.trim(data);
				if ( data != '' ){
				   var vresultado = data.split(';');
				   $("#txtnitentidad").val(vresultado[0]);
				   $("#txtdesentidad").val(vresultado[1]);
				}
				else{
				   $("#txtnitentidad").val('');
				   $("#txtdesentidad").val('');
				   alerta('La Entidad no Existe');
				}
			});		
	    }
   }

	function nueva_agrupacion(){
		$("#entidades .entidad").attr('checked', false );
		$("#lista_agrupaciones option").eq(0).attr('selected',true); 
		$("#entidades input").attr("disabled",true);
		$("#entidades .fondoAmarillo").removeClass('fondoAmarillo');
		$("#form_nueva_agrupacion").show( 'drop', {}, 500 );
		$("#tr_estado_agrupacion").hide();
		borrarEntidadesElegidas();
		$('html, body').animate({
			scrollTop: '0px',
			scrollLeft: '0px'
		},0);
	}
	
	function guardarEntidades(){
		var cadena = "";
		var ind=0;
		var codigoGrupo = $("#lista_agrupaciones").val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		var agregadas = new Array();
		
		$("#entidades").find(".entidad:checked").each(function(){
				if(ind>0)
					cadena+=",";
				
				cadena+= ""+$(this).val();
				ind++;
		});

        if ($('input:checkbox[name=chknuentidad]:checked').val()=='on')
        	{ 
        		cadena+= ","+$("#txtnuentidad").val();
        	}

		var grupo = $("#lista_agrupaciones").val();
		if( grupo == "" ){
			return;
		}
		
		$.blockUI({ message: $('#msjEspere') });
		$("#enlace_retornar").fadeIn('slow');
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.get('EmpresasHCE.php', { wemp_pmla: wemp_pmla, action: "guardarEntidades", grupo: codigoGrupo, entidades: cadena, consultaAjax: aleatorio} ,
		function(data) {
			$.unblockUI();
			data = $.trim(data);
			if( data == 'OK' ){
				alerta("Exito al guardar");
			}else{
				alerta("Ha ocurrido un error al guardar");
				restablecer_pagina();	
			}			
		});		
	}
	
	function borrarEntidadesFiltradas(){
		$("#tabla_filtros tr:not(.noborrar)").remove();	
		$("#div_tabla_filtros").hide();
	}
	
	function borrarEntidadesElegidas(){
		$("#tabla_seleccionadas tr:not(.noborrar)").remove();	
	}
	
	function guardar_nueva_agrupacion(){
	
		var descripcion_nueva_agrupacion = $("#descripcion_nueva_agrupacion").val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		$.blockUI({ message: $('#msjEspere') });
		$("#enlace_retornar").fadeIn('slow');
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.get('EmpresasHCE.php', { wemp_pmla: wemp_pmla, action: "guardarNuevaEntidad", descripcion: descripcion_nueva_agrupacion, consultaAjax: aleatorio} ,
		function(data) {
			$.unblockUI();
			if( data == 'NO' ){
				alerta("Error al guardar la nueva agrupacion");
			}else if( data.length < 4 ){
					var opcion = "<option value='"+data+"'>"+descripcion_nueva_agrupacion+"</option>";
					$("#lista_agrupaciones").append( opcion );
					$("#lista_agrupaciones").val( data );
					$("#entidades input").attr("disabled",false);
					$("#form_nueva_agrupacion").hide( 'drop', {}, 500 ); 
			}else{
				alerta("Error al guardar la nueva agrupacion");
			}			
		});
	}
	
	function cancelar_nueva_agrupacion(){
		$("#form_nueva_agrupacion").hide( 'drop', {}, 500 );	
	}
	
	function recargarEntidades(){
		var opcion = $("#select_filtrar").val();		
		var wemp_pmla = $("#wemp_pmla").val();		
		$.blockUI({ message: $('#msjEspere') });
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.get('EmpresasHCE.php', { wemp_pmla: wemp_pmla, action: "ordenarEntidades", parametro: opcion, consultaAjax: aleatorio} ,
		function(data) {
			$.unblockUI();
			if( data == 'NO' ){
				alerta("Error al guardar la nueva agrupacion");
			}else{
				$("#div_tabla_entidades").html(data);
				if( $("#lista_agrupaciones").val() == '' )
					$("#entidades input").attr("disabled",true);
				//Todos los que esten seleccionados en la tabla elegidos se deben recargar en la tabla entidades
				$("#tabla_seleccionadas .entidad").each(function(){
					var empresa = $(this).parent().attr("empresa");
					var valor = $(this).val();
					$("#div_tabla_entidades [empresa="+empresa+"]").addClass('fondoAmarillo');
					$("#div_tabla_entidades input[value="+valor+"]").attr("checked", true);	
				});
			}			
		});
	}
	
	function inArray(needle, haystack) {
		var length = haystack.length;
		for(var i = 0; i < length; i++) {
			if(typeof haystack[i] == 'object') {
				if(arrayCompare(haystack[i], needle)) return true;
			} else {
				if(haystack[i] == needle) return true;
			}
		}
		return false;
	}

	function isJson(value) {
		try {
			eval('(' + value + ')');
			return true;
		} catch (ex) {
			return false;
		}
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
   
<?php
	
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");
include_once("movhos/movhos.inc.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wmovhosbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcliamebasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "mostrarListaEntidades"){
		consultarFormulariosAgrupacion( $_REQUEST['grupo'] );
	}else if( $action == "guardarEntidades"){
		guardarEntidades( $_REQUEST['grupo'], $_REQUEST['entidades'] );
	}else if( $action == 'guardarNuevaEntidad' ){
		guardarAgrupacion( $_REQUEST['descripcion'] );
	}else if( $action == 'ordenarEntidades' ){
		mostrarTablaEntidades( $_REQUEST['parametro'] );
	}else if( $action == 'guardarEstadoAgrupacion' ){
		cambiarEstadoAgrupacion( $_REQUEST['grupo'], $_REQUEST['estado'] );
	}else if( $action == 'consultarEntidad' ){
		consultarEntidades( $_REQUEST['codentidad'] );
	}	
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************
	
    function consultarEntidades($codentidad){

		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		global $wcliamebasedato;

        $respuesta ='';
		//Busco que nit y descripcion Entidad
		$q= " SELECT Empcod as codigo, Empnit as nit, Empnom as descripcion, id as empresa "
			."	FROM ".$wcliamebasedato."_000024 "
			." WHERE Empcod = '".$codentidad."'";

		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0){
			$row =  mysql_fetch_assoc($res);
			$respuesta = $row['nit'].';';
			$respuesta .= $row['descripcion'];
		}
		echo $respuesta;
    }

	function cambiarEstadoAgrupacion( $wgrupo, $westado ){
		
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = "UPDATE ".$wbasedato."_000025 "
		." SET Empest = '".$westado."' "
		."  WHERE Empcod = '".$wgrupo."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		
		$actualizados = mysql_affected_rows();
		if( $actualizados > 0 ){
			echo "OK";
		}else{
			echo "No se pudo actualizar";
		}
	}
	
	function guardarEntidades($wcodgrupo, $wentidades){
		global $conex;
        global $wbasedato;
		global $wmovhosbasedato;
		global $wemp_pmla;
		
		$q = "    UPDATE ".$wbasedato."_000025 
					 SET Empemp = '".$wentidades."'
				   WHERE Empcod = '".$wcodgrupo."' ";
		$resl2 = mysql_query($q, $conex);	
		$actualizados = mysql_affected_rows();
		if( $resl2 > 0 ){
			echo "OK";
		}else{
			echo "No se actualizo";
		}
	}
	
	function guardarAgrupacion($wdescripcion){
		
		global $conex;
        global $wbasedato;
		global $wmovhosbasedato;
		global $wemp_pmla;
		global $wusuario;
		
		$wcod_grupo = "1";
		//Busco que codigo sigue en las agrupaciones
		$q = " SELECT Empcod as codigo "
			 ."  FROM ".$wbasedato."_000025 "
			 ." ORDER BY Empcod DESC LIMIT 1";

		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0){
			$row = mysql_fetch_array($res);
			$wcod_grupo = $row[0]+1;
		}
		while ( strlen($wcod_grupo) < 3 ){
			$wcod_grupo = "0".$wcod_grupo;			
		}
		
		$q = "    INSERT INTO ".$wbasedato."_000025 
								  (Medico, Fecha_data, Hora_data, Empcod, Empdes, Empemp, Empest, Seguridad) 
						   VALUES
								  ('".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$wcod_grupo."', '".$wdescripcion."', '', 'on',  'C-".$wusuario."')";
		$resl2 = mysql_query($q, $conex);
		
		$guardo = mysql_insert_id();
		
		if( $guardo ){
			echo $wcod_grupo;
		}else{
			echo "NO";
		}
	}
	
	function mostrarTablaEntidades($wparametro='descripcion'){
		global $conex;
        global $wbasedato;
		global $wmovhosbasedato;
		global $wcliamebasedato;
		global $wemp_pmla;
		
		/*$q= " SELECT Epscod as codigo, Epsnit as nit, Epsnom as descripcion, id as empresa "
			."	FROM ".$wmovhosbasedato."_000049 "
			."  ORDER BY ".$wparametro." ";*/
		$q= " SELECT Empcod as codigo, Empnit as nit, Empnom as descripcion, id as empresa "
			."	FROM ".$wcliamebasedato."_000024 "
			."  ORDER BY ".$wparametro." ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		

		$ind = 1;

		echo "<div id='div_tabla_entidades'>";
		echo "<center>";
		echo "<input type='button' value='Nuevo' onclick='nueva_agrupacion()' />";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type='button' value='Guardar' onclick='guardarEntidades()' />";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "</center>";
		echo "<br><br>";
		
		echo "<table id='entidades' align='center'>";
		echo "<tr><td colspan=8 align='left'>";
		echo "Ordernar por:";
		//El select con las opciones por las que se puede ordenar la tabla, IMPORTANTE: las opciones tienen como value el alias para la consulta
		echo "<select id='select_filtrar' onchange='recargarEntidades()'>";
		if( $wparametro == 'descripcion' ){
			echo "<option value='descripcion' selected>Descripci�n</option>";
			echo "<option value='codigo'>C�digo</option>";
			echo "<option value='nit'>NIT</option>";
		}else if( $wparametro == 'codigo' ){
			echo "<option value='codigo' selected>C�digo</option>";
			echo "<option value='descripcion'>Descripci�n</option>";			
			echo "<option value='nit'>NIT</option>";
		}else{
			echo "<option value='nit' selected>NIT</option>";
			echo "<option value='codigo'>C�digo</option>";
			echo "<option value='descripcion'>Descripci�n</option>";	
		}
		echo "</select>";
		echo "</td></tr>";
		echo "<tr class='encabezadotabla'><td colspan=8 align='center'><font size=5><b>LISTA DE ENTIDADES</b></font></td></tr>";
		echo "<tr class='fila2' style='font-weight:bold;'><td align='center'>Sel.</td><td align='center'>Codigo</td><td align='center'>Entidad</td><td align='center'>Nit</td><td align='center'>Sel.</td><td align='center'>Codigo</td><td align='center'>Entidad</td><td align='center'>Nit</td></tr>";		
		
        if ($num > 0){
			$cierratr = false;
			
			while( $row = mysql_fetch_assoc($res) ){
				if( $ind == 1 ){
					echo "<tr class='fila1'>";
					$cierratr = false;
				}
				echo "<td empresa='".$row['empresa']."'><input type='checkbox' class='entidad' onclick='elegir_entidad(this)' value='".trim($row['codigo'])."' /></td><td class='parabuscar' empresa='".$row['empresa']."'>".$row['codigo']."</td><td class='parabuscar' empresa='".$row['empresa']."'>".$row['descripcion']."</td><td class='parabuscar' empresa='".$row['empresa']."'>".$row['nit']."</td>";
				if( $ind  == 2 ){
					echo "</tr>";
					$ind = 0;
				}
				$ind++;
			}
		}

		echo "</table>";
		echo "<br><br>";
		
		echo "<center>";
		echo "<input type='button' value='Nuevo' onclick='nueva_agrupacion()' />";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type='button' value='Guardar' onclick='guardarEntidades()'/>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "</center>";
		
		echo "</div>";

	}
	
	function consultarListaAgrupaciones(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q= " SELECT Empcod as codigo, Empdes as nombre, Empest as estado
				FROM ".$wbasedato."_000025
			   WHERE Empcod != '*'";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0){
			while( $row = mysql_fetch_assoc($res) ){
				array_push( $result, $row );			
			}
		}
		
		return $result;	
	}

	function consultarFormulariosAgrupacion( $codAgrupacion ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Empemp as empresas"
			."   FROM ".$wbasedato."_000025 "
			."  WHERE empcod = '".$codAgrupacion."'";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0){
			$row = mysql_fetch_assoc($res);
			$result = explode( ",", $row['empresas'] );
		}
		echo json_encode($result);
	}
	
	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		
		$entidades = consultarListaAgrupaciones();
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		encabezado("ADMINISTRACI�N DE EMPRESAS HCE", $wactualiz, "clinica");
		
		echo '<div style="width: 100%">';
		
		$width_sel = " width: 95%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
		//------------TABLA DE PARAMETROS-------------
		echo '<table align="center">';
		echo "<tr>";
		echo '<td class="encabezadotabla" width="80px">Agrupaci�n</td>';
		//LISTA DE ENTIDADES
		echo '<td class="fila1" width="auto">';
		echo "<div align='center'>";
		echo "<select id='lista_agrupaciones' class='ui-corner-all' align='center' style='".$width_sel." margin:5px;'>";
		echo "<option></option>";
		foreach($entidades as $pos =>$entidad)
			echo '<option value="'.$entidad['codigo'].'" estado="'.$entidad['estado'].'">'.$entidad['nombre'].'</option>';
		echo '</select>';
		echo '</div>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>";
		echo "Buscar";
		echo "</td>";
		echo "<td class='fila1' align='center'>";
		echo "<input type='text' id='buscador' style='".$width_sel." margin:5px;'/>";
		echo "</td>";
		echo "</tr>";
		echo "<tr id='tr_estado_agrupacion'>";
		echo "<td class='fila1'  colspan=2 align='center'>";
		echo "<input type='checkbox' id='grupo_activo' onclick='guardar_estado_agrupacion(this)'/>Activo ";
		echo "</td>";
		//echo "<td class='fila1' align='center'>";
		//echo "<input type='button' onclick='eliminar_agrupacion()' value='Eliminar Agrupacion' />";
		//echo "</td>";
		echo "</tr>";
		echo "</table>";
		//FIN LISTA ENTIDADES
		
		//------------FIN TABLA DE PARAMETROS-------------

		echo "<br>"; 
		echo "<center>";
		echo "<div id='form_nueva_agrupacion' style='display:none; background-color: #E8EEF7; width: 300px;'>";
		echo '<font size="4" color="#000000"><b>Nueva Agrupaci�n</b></font>';
		echo "<br><br><br>";
		echo "<label>Descripci�n</label>&nbsp;&nbsp;";
		echo "<input type='text' id='descripcion_nueva_agrupacion' />";
		echo "<br><br>";
		echo "<input type='button' value='Guardar' onclick='guardar_nueva_agrupacion()' />";
		echo "<input type='button' value='Cancelar' onclick='cancelar_nueva_agrupacion()' />";
		echo "</div>";
		echo "</center>";
		echo "<br>";
		echo "<br>";
		
		echo "<center>";
		echo "<div id='div_tabla_filtros'>";
		echo "<table id='tabla_filtros'>";
		echo '<tr class="encabezadotabla noborrar"><td align="center" colspan="4"><font size="3"><b>ENTIDADES FILTRADAS</b></font></td></tr>';
		echo '<tr style="font-weight:bold;" class="fila2 noborrar">';
		echo '<td align="center">Sel.</td><td align="center">Codigo</td><td align="center">Nit</td><td align="center">Entidad</td>';
		echo "</tr>";
		echo "</table>";
		echo "<input type='button' value='Borrar B�squeda' onclick='borrarEntidadesFiltradas()' />";
		echo "<br><br>";
		echo "</div>";
		echo "</center>";
		
		echo "<center>";
		echo "<table id='tabla_seleccionadas'>";
		echo '<tr class="encabezadotabla noborrar"><td align="center" colspan="4"><font size="3"><b>ENTIDADES DEL GRUPO</b></font></td></tr>';
		echo '<tr style="font-weight:bold;" class="fila2 noborrar">';
		echo '<td align="center">Sel.</td><td align="center">Codigo</td><td align="center">Entidad</td><td align="center">Nit</td>';
		echo "</tr>";
		echo "</table>";
		echo "<br><br>";
		echo "</center>";
		
		mostrarTablaEntidades();
		
		//------FIN FORMULARIO------
		echo "</div>";//Gran contenedor
		echo '<center>';
		echo "<br><br>";
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
	
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
		echo '</center>';
	}
	
?>
 <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>