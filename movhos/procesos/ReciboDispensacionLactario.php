<html>
<head>
<title>MATRIX - [RECIBO DE LACTARIO]</title>

<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript">

	var nombreFormulario = "ReciboDispensacionLactario.php";

	/******************************************************************************************************************************
	 *Redirecciona a la pagina inicial
	 ******************************************************************************************************************************/
	 function inicio(servicio){
 		document.location.href=nombreFormulario+'?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&servicio='+servicio;
	 }
	/******************************************************************************************************************************
	 *Redirecciona a la pagina inicial
	 ******************************************************************************************************************************/
	 function inicioRecibido(servicio,wfechai,wfechaf,wscausa){
	 	document.location.href=nombreFormulario+'?waccion=c&wemp_pmla='+document.forms.forma.wemp_pmla.value+'&servicio='+servicio+'&wfechai='+wfechai+'&wfechaf='+wfechaf+'&wscausa='+wscausa;
	 }
	/*****************************************************************************************************************************
	 *Consulta las historias y habitaciones de acuerdo a un servicio
	 ******************************************************************************************************************************/
	function consultarHabitaciones()
	{
		var servicio = document.getElementById('wsservicio').value;
		if(servicio && servicio != '' ){
			var contenedor = document.getElementById('cntHabitacion');
			var parametros = "";

			parametros = "consultaAjaxInclude=2&basedatos="+document.forms.forma.wbasedato.value+"&servicio=" + servicio+"&wemp_pmla="+document.forms.forma.wemp_pmla.value;

			try{
				$.blockUI({ message: $('#msjEspere') });
				ajax=nuevoAjax();

				ajax.open("POST", "../../../include/movhos/movhos.inc.php",true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);

				ajax.onreadystatechange=function()
				{
					if (ajax.readyState==4 && ajax.status==200)
					{
						contenedor.innerHTML=ajax.responseText;
					}
					$.unblockUI();
				}
				if ( !estaEnProceso(ajax) ) {
					ajax.send(null);
				}
			}catch(e){	}
		}
	}
	/*****************************************************************************************************************************
	 *
	 ******************************************************************************************************************************/
	function consultarRecibos()
	{
		var servicio = document.getElementById('wsservicio').value;
		var causa = document.getElementById('wscausa').value;
		var fInicial = document.getElementById('wfechai').value;
		var fFinal = document.getElementById('wfechaf').value;
		var servicioUsuario = document.getElementById('whserviciousr').value;

		if(servicio && servicio != '' ){
			var contenedor = document.getElementById('cntHabitacion');
			var parametros = "";

			parametros = "consultaAjaxInclude=5&basedatos="+document.forms.forma.wbasedato.value+"&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&servicio="+servicio+"&fechaInicial="+fInicial+"&fechaFinal="+fFinal+"&causa="+causa+"&servicioUsuario="+servicioUsuario;

			try{
				ajax=nuevoAjax();

				ajax.open("POST", "../../../include/movhos/movhos.inc.php",true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);

				ajax.onreadystatechange=function()
				{
					if (ajax.readyState==4 && ajax.status==200)
					{
						contenedor.innerHTML=ajax.responseText;
					}
				}
				if ( !estaEnProceso(ajax) ) {
					ajax.send(null);
				}
			}catch(e){	}
		}
	}
	 /*****************************************************************************************************************************
	  * Captura de onEnter con llamada a funcion parametrizada con validacion de numeros entera
	  ******************************************************************************************************************************/
	 function teclaEnterEntero(e,accion){
		var respuesta = validarEntradaEntera(e);
		var tecla = (document.all) ? e.keyCode : e.which;

		if(respuesta && tecla==13){
			eval(accion);
			return false;
		}
		return respuesta;
	 }

	/*****************************************************************************************************************************
	 * Captura de onEnter con llamada a funcion parametrizada con validacion de numeros entera
	 ******************************************************************************************************************************/
	function consultar(){
		var servicio = document.forms.forma.wsservicio.value;
		var historia = document.forms.forma.whistoria.value;
		var consecutivo = document.forms.forma.consecutivo.value;

		if(servicio != ''){
			document.location.href = nombreFormulario+'?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia+'&wservicio='+servicio+'&consecutivo='+consecutivo;
		} else {
			alert('No ha seleccionado el servicio');
		}
	}

	function consultarDetalleLactario(historia,ingreso,nomPaciente,habitacion,consecutivo,linea,servicio){
		$.blockUI({ message: $('#msjEspere') });
		if(historia != '' && ingreso != '') {
			var href = nombreFormulario+'?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia+'&wingreso='+ingreso+'&nombrePaciente='+nomPaciente+'&habitacion='+habitacion+'&consecutivo='+consecutivo+'&linea='+linea+'&wservicio='+servicio;

			document.location.href = href;
		} else {
			alert('No ha seleccionado el servicio');
		}
	}

	function consultarDetalleRecibido(historia,ingreso,nomPaciente,consecutivo,servicio,habitacion,wfechai,wfechaf,wscausa,wfechar){
		var causaFaltante = document.forms.forma.wscausa.value;
		$.blockUI({ message: $('#msjEspere') });
		if(historia != '' && ingreso != '') {
			var href = nombreFormulario+'?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=d&whistoria='+historia+'&wingreso='+ingreso+'&nombrePaciente='+nomPaciente+'&consecutivo='+consecutivo+'&servicio='+servicio+'&causa='+causaFaltante+'&habitacion='+habitacion+'&wfechai='+wfechai+'&wfechaf='+wfechaf+'&wscausa='+wscausa+'&wfechar='+wfechar;
			document.location.href = href;
		} else {
			alert('No ha seleccionado el servicio');
		}
	}
	/******************************************************************************************************************************
	 *
	 ******************************************************************************************************************************/
	function validarCantidad(id, origen){

		var maxRecibido = jQuery( $("#wtmaxrecibido"+id) );
		var recibido    = jQuery( $("#wtrecibido"+id) );
		var pendiente   = jQuery( $("#wtpendiente"+id) );
		var incremento  = jQuery( $("#whincremento"+id) );

		var valido = false;

		if(recibido && recibido.val() && !isNaN(recibido.val()) && maxRecibido && maxRecibido.val() && !isNaN(maxRecibido.val())){
			try{
				if(parseInt(recibido.val()) < 0){
					recibido.val($("#cantidadRecibidaAnterior"+id).val());
					alert("El valor recibido debe ser mayor o igual a cero.  Verifique línea "+id);
					return;
				}

				if(parseInt(recibido.val()) > parseInt(maxRecibido.val()) || (parseInt(recibido.val()) % incremento.val() != 0)){
					$("#tr"+id).addClass( "fila2" );
					$("#tdRec"+id).addClass( "fila2" );
					recibido.val($("#cantidadRecibidaAnterior"+id).val());
					pendiente.val( parseInt(maxRecibido.val()) - parseInt(recibido.val()) );
					alert("El valor recibido debe ser igual o inferior a la cantidad enviada y para este articulo debe ser en multiplos de "+incremento.val()+".  Verifique línea "+id);
					return;
				}
				valido = true;
			}catch(e){}
		}

		if(!valido){
			alert('El valor digitado no es válido, por favor verifique línea' +id);
			recibido.val( "0" );
				$("#tr"+id).addClass( "fila2" );
				$("#tdRec"+id).addClass( "fila2" );
		} else {
			 if(recibido.val()!=0){
				//Resaltar la fila
			 	$("#tr"+id).addClass( "fila2" );
			 	$("#tdRec"+id).addClass( "fondoRojo" );
			 } else {
				$("#tr"+id).addClass( "fila2" );
					$("#tdRec"+id).addClass( "fila2" );
			}
		}

		if(recibido.val() == maxRecibido.val()){
			$("#tdRec"+id).addClass( "fondoVerde" );
			$("#tr"+id).addClass( "fondoVerde" );
		}
		pendiente.val( parseInt(maxRecibido.val()) - parseInt(recibido.val()) );
		$("#cantidadRecibidaAnterior"+id).val(recibido.val());
		if( origen == "cons" && valido ){
			distribuirReciboEnDetalle( id, parseInt(recibido.val()) );
		}
	}

	 //en caso de querer entender el funcionamiento de esta función, haga visible los tr donde tipo='individual' y haga recibos en el consolidado para oberservar coom se mueven los individuales.
	function distribuirReciboEnDetalle( id, valorRecibido ){
		var codigoAfectado = $("#tr"+id).attr('codigo');
		var arregloSaldosDisponibles = new Array();
		var vinoCero = false;

		if( valorRecibido == 0 ){
			vinoCero = true;
		}

		$("tr[codigo^='"+codigoAfectado+"'][tipo='individual']").each(function(){
			var numLinea = $(this).attr( "numeroLinea" )
		$("#wtpendiente"+numLinea).val( $("#wtmaxrecibido"+numLinea).val() );
		$("#wtrecibido"+numLinea).val( "0" );
		});

		$("tr[codigo^='"+codigoAfectado+"'][tipo='individual']").each(function(){
			var linea =  new Object();
			var numLinea = $(this).attr( "numeroLinea" )
		linea.numLinea         = numLinea ;
		linea.pendienteRecibir = $("#wtpendiente"+numLinea).val();
			arregloSaldosDisponibles.push(linea);
		});

		var saldoEnRecibidoConsolidado = valorRecibido; //inicialmente el saldo que se repartirá en los individuales es todo lo recibido
													// esta variable es de control para asignar los valores
		for( var i in arregloSaldosDisponibles){

			if( arregloSaldosDisponibles[i].pendienteRecibir > 0){ //si esta linea individual tiene saldo disponible por recibir

				if( saldoEnRecibidoConsolidado > 0 ){		//si de lo recibido en el consolidado todavia hay saldo para aplicar en las lineas individuales
					var aux = saldoEnRecibidoConsolidado - arregloSaldosDisponibles[i].pendienteRecibir;

					if( aux >= 0 ){		// si es mayor el saldo del consolidado que lo que se necesita en la linea
						$("#wtrecibido"+arregloSaldosDisponibles[i].numLinea).val( arregloSaldosDisponibles[i].pendienteRecibir );
						$("#wtpendiente"+arregloSaldosDisponibles[i].numLinea).val("0");
						saldoEnRecibidoConsolidado = saldoEnRecibidoConsolidado - arregloSaldosDisponibles[i].pendienteRecibir;
					}
					if( aux < 0 ){ 		// si es mayor lo que se necesita en la linea individual que lo que hay en el saldo del consolidado
						$("#wtrecibido"+arregloSaldosDisponibles[i].numLinea).val( saldoEnRecibidoConsolidado );
						$("#wtpendiente"+arregloSaldosDisponibles[i].numLinea).val( $("#wtmaxrecibido"+arregloSaldosDisponibles[i].numLinea).val() - saldoEnRecibidoConsolidado );
						saldoEnRecibidoConsolidado = 0;
					}

				}else{

					if( vinoCero ){
						$("#wtrecibido"+arregloSaldosDisponibles[i].numLinea).val( "0" );
						$("#wtpendiente"+arregloSaldosDisponibles[i].numLinea).val( $("#wtmaxrecibido"+arregloSaldosDisponibles[i].numLinea).val() );
					}
					return false;
				}
			}else{

				if( vinoCero ){
						$("#wtrecibido"+arregloSaldosDisponibles[i].numLinea).val( "0" );
						$("#wtpendiente"+arregloSaldosDisponibles[i].numLinea).val( $("#wtmaxrecibido"+arregloSaldosDisponibles[i].numLinea).val() );
				}
			}
		}
	}

	function validarSeleccionCausa(id){
		var causa          = $("#wscausa"+id).val();
		var codigoAfectado = $("#tr"+id).attr('codigo');
		$("tr[codigo^='"+codigoAfectado+"'][tipo='individual']").each(function(){
			$(this).find("select").find("option[value='"+causa+"']").attr("selected", true);
		});
	}

	 /******************************************************************************************************************************
	 *
	 ******************************************************************************************************************************/
	 function grabarRecibo(){
//		 debugger;
		 if(confirm("¿Confirmar el recibo del carro de medicación?")){
	 		var wemp_pmla = document.forms.forma.wemp_pmla.value;

	 		var historia = document.forms.forma.whistoria.value;
	 		var ingreso = document.forms.forma.wingreso.value;
		 	var consecutivo = document.forms.forma.consecutivo.value;

		 	var arrCantidades = document.forms.forma.arrCantidades.value;
		 	var servicio = document.forms.forma.wservicio.value;

	 		document.getElementById("btnGrabar").disabled = true;

		 	//Recorro los campos con los valores de pedido
		 	var cont1 = 1;
	 		var causa = "";
		 	var datos = "";
		 	var limite = $("#limiteFilas").val();
		 	while( cont1 <= limite ){

	 			if(document.getElementById("wtrecibido"+cont1).value != ''){
	 			causa = document.getElementById("wscausa"+cont1).value;

		 		//Si la cantidad despachada y recibida son iguales, se ignora la causa de faltante
	 			if(document.getElementById("wtmaxrecibido"+cont1).value == document.getElementById("wtrecibido"+cont1).value){
		 			causa = "";
		 		}

	 			datos += document.getElementById("whcodigo"+cont1).value+"|";
	 			datos += document.getElementById("wtmaxrecibido"+cont1).value+"|";
	 			datos += document.getElementById("wtrecibido"+cont1).value+"|";
	 			datos += document.getElementById("whlinea"+cont1).value+"|";
	 			datos += causa+"|";
	 			datos += document.getElementById("whtablaorigen"+cont1).value+"|";
	 			datos += document.getElementById("whconsecutivoCargo"+cont1).value+"|";
	 			datos += document.getElementById("whidRegistro"+cont1).value+";";

	 			//Se debe obligar siempre a seleccionar una sin faltante.
	 			if(document.getElementById("wtmaxrecibido"+cont1).value != document.getElementById("wtrecibido"+cont1).value && document.getElementById("wscausa"+cont1).value == ''){
		 			alert("Debe seleccionar una causa de faltante para "+document.getElementById("whcodigo"+cont1).value);
		 			document.getElementById("btnGrabar").disabled = false;
		 			return;
		 		}
	 		}
	 		cont1++;
	 	}

	 	if(datos != ''){
	 		var href =nombreFormulario+'?wemp_pmla='+wemp_pmla+'&waccion=b&whistoria='+historia+'&wingreso='+ingreso+'&consecutivo='+consecutivo+'&wdatos='+datos+'&arrCantidades='+arrCantidades+'&servicio='+servicio;
	 		document.location.href = href;
	 	} else {
	 		alert("Nada para grabar.");
	 	}
	 	document.getElementById("btnGrabar").disabled = false;
		 }
	 }
	 /******************************************************************************************************************************
	  *
	  ******************************************************************************************************************************/
	 function actualizarRecibido(){
//		 debugger;
		 var elementos = document.getElementsByTagName("select");
		 var datos = "";
		 var wemp_pmla = document.forms.forma.wemp_pmla.value;

		 var historia = document.forms.forma.whistoria.value;
		 var ingreso = document.forms.forma.wingreso.value;
		 var consecutivo = document.forms.forma.consecutivo.value;
		 var servicio = document.forms.forma.servicio.value;

		 document.getElementById("btnGrabar").disabled = true;

		 for (var cont1 = 0; cont1 < elementos.length; cont1++){
		 	if(elementos[cont1] && !elementos[cont1].disabled){
			 	datos += document.getElementById("whcodigo"+ parseInt(cont1+1)).value+"|";
		 		datos += document.getElementById("whlinea"+parseInt(cont1+1)).value+"|";
		 		datos += document.getElementById("wscausa"+parseInt(cont1+1)).value+"|";
		 		datos += document.getElementById("whfrecibo"+parseInt(cont1+1)).value+"|";
		 		datos += document.getElementById("whhrecibo"+parseInt(cont1+1)).value+";";
		 	}
		 }

		 if(datos != ''){
		 	var href = nombreFormulario+'?wemp_pmla='+wemp_pmla+'&waccion=e&whistoria='+historia+'&wingreso='+ingreso+'&consecutivo='+consecutivo+'&wdatos='+datos+'&servicio='+servicio;
		 	document.location.href = href;
		 } else {
		 	alert("Nada para grabar.");
		 }
		 document.getElementById("btnGrabar").disabled = false;
	 }
	 /*****************************************************************************************************************************
	  * Captura de onEnter con llamada a funcion parametrizada
	  ******************************************************************************************************************************/
	 function teclaEnter(e,accion){
		accion = accion.toString();
		var respuesta = true;
	 	var tecla = (document.all) ? e.keyCode : e.which;

	 	if(tecla==13){
	 		eval(accion);
	 	}
	 	return respuesta;
	 }
	 /******************************************************************************************************************************
	 *
	 ******************************************************************************************************************************/
	 function validarArticulo(){
	 	var codigo = document.getElementById("wtarticulo").value.toUpperCase();
	 	var equivalente = false;
	 	var mensaje = "";

	 	//Si la longitud es de 13 caracteres se trata de una lectura de codigo, se debe buscar 7613030045786
	 	var parametros = "";
	 	parametros = "consultaAjaxInclude=01&basedatos="+document.forms.forma.wbasedato.value+"&codigoBarras="+codigo+"&wemp_pmla="+document.forms.forma.wemp_pmla.value;;

	 	try{
//	 		$.blockUI({ message: $('#msjEspere') });
	 		ajax=nuevoAjax();

	 		ajax.open("POST", "../../../include/movhos/movhos.inc.php",true);
	 		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	 		ajax.send(parametros);

	 		ajax.onreadystatechange=function(){
	 			if (ajax.readyState==4 && ajax.status==200){
	 					if(ajax.responseText != ''){
	 						codigo = ajax.responseText;
	 					}

	 					var existe = false;
	 					var cont1 = $("#limiteFilas").val()*1 + 1;

	 					//Consulta del articulo
	 					while(document.getElementById("whcodigo"+cont1)){
	 						if(codigo == document.getElementById("whcodigo"+cont1).value){
	 							existe = true;
	 							break;
	 						}
	 						cont1++;
	 					}

	 					var pendiente = document.getElementById("wtpendiente" + cont1);
	 					var maxRecibido = document.getElementById("wtmaxrecibido"+cont1);
	 					var recibido = document.getElementById("wtrecibido"+cont1);
	 					var incremento = document.getElementById("whincremento"+cont1);

	 					//Salto al focus del articulo
	 					if(existe){
//	 						document.getElementById("wtrecibido"+cont1).focus();
//	 						document.getElementById("wtrecibido"+cont1).select();

							//Si la cantidad recibida es menor o igual a la despachada
							recibido.value = parseInt(recibido.value) + 1*parseInt(incremento.value);
							if(recibido.value < maxRecibido.value){
								document.getElementById("tr"+cont1).className = "fila2";
								document.getElementById("tdRec"+cont1).className = "fondoRojo";
							} else {
								recibido.value = maxRecibido.value;
								document.getElementById("tdRec"+cont1).className = "fondoVerde";
								document.getElementById("tr"+cont1).className = "fondoVerde";
							}
	 						pendiente.value = parseInt(maxRecibido.value) - parseInt(recibido.value);
	 						validarCantidad( cont1, "cons" );
	 					} else {
	 						alert("El articulo no esta en la lista");
	 					}
 						document.getElementById("wtarticulo").focus();
 						document.getElementById("wtarticulo").select();
	 				}
	 			}
	 			if ( !estaEnProceso(ajax) ) {
	 				ajax.send(null);
	 			}
	 		}catch(e){	}
	 }
	 </script>
</head>
<body>
<?php
include_once("conex.php");
include_once("root/comun.php");
/*********************************************************
 * Recibo de carro de dispensacion
 *
 * Fecha: Marzo 16 de 2010
 * Autor: Msanchez
 * Version: 1.0
 *
	* Cambios:
 * 2021-11-04 (Daniel CB) Se realiza modificación de parametros quemados y se agrega el global wemp_pmla en la función consultarListadoPendienteReciboLactario.
 * 
 * 2013-12-12 ( Camilo ZZ) se modificó el script para que muestre en pantalla el consolidado( la suma de los cargos del mismo artículo y del mismo número de documento),
 * 			   adicionalmente se crearon funciones js para que lo que se reciba en ese consolidado quede distribuído de manera correcta dentro de los inputs que mantienen la información de los cargos
 *			   individuales. De tal manera que se muestre en conjunto pero se guarde en la tabla correcta y en los registros correctos
 * 2013-04-24: (Edwin MG)	Cambio enlaces de consulta ajax de include/Movhos/ a include/movhos/.
 * 2010-05-03: (Msanchez)  En la consulta del detalle se verifica tambien los cargos realizados al servicio seleccionado
 * 2011-01-11 (Mario Cadavid):  Modificacion del Query por fallas en los resultados de las consultas. Se dejó de consultar la tabla 000003 y 000020
 * en la consulta de recibos por fecha, consultando ya solo las tablas 000093 y 000018 para hacer más rápida la consulta y dar los resultados correctos
 * Funciones afectadas: consultarPacientesConRecibidosPorFecha y consultarPacientesConRecibosPorFechaLactario
 * 2012-05-03 (Mario Cadavid):  En las funciones consultarArticulosPendientesReciboPaciente y consultarListadoPendienteReciboLactario se modificó el query
 * principal quitando los SELECT anidados. Se incluyó la variable $articulosIncluidos que permite comparar el IN del query contra un Array y no contra un
 * SELECT. En la función consultarArticulosPendientesReciboPaciente se quita la consulta de cantidad por defecto que estaba con un SELECT anidado dentro del
 * Query principal y se hace un query por fuera que cosulta si el artículo maneja cantidad por defecto y obtiene esta cantidad
*********************************************************/
include_once("movhos/movhos.inc.php");

/***********************************
 * Clases
 ***********************************/
class PacientePendienteRecibo{
	var $historia = "";
	var $ingreso = "";
	var $nombre = "";
	var $servicio = "";
	var $habitacion = "";

	var $pacienteDatos = "";
}

class ArticulosPendienteRecibo{
	var $idRegistro = "";
	var $numero = "";
	var $linea = "";

	var $historia = "";
	var $ingreso = "";
	var $codigoArticulo = "";
	var $tipo = "";
	var $nombreArticulo = "";
	var $cantidadDespachada = "";
	var $cantidadRecibida = "";
	var $presentacion = "";
	var $incremento = "";
	var $saldoAcumulado = "";
}

class DetalleRecibo{
	var $historia = "";
	var $ingreso = "";
	var $consecutivoCargo = "";
	var $lineaCargo = "";
	var $articulo = "";
	var $cantidadDespachada = "";
	var $cantidadRecibida = "";
	var $causaFaltante = "";
	var $usuarioRecibe = "";
	var $fechaRecibo = "";
	var $horaRecibo = "";
}

class RegistroArticulo{
	var $campo1 = "";
	var $campo2 = "";
	var $campo3 = "";
	var $campo4 = "";
}

/***********************************
 * Funciones
 ***********************************/
function consultarGruposPorCcoUsuario($codigoUsuario,&$centroCostosUsuario){
	global $conex;
	global $wbasedato;

	$coleccion = array();

	$gruposVisibles = "";

	$q = "SELECT Cc FROM root_000025 WHERE Empleado = '$codigoUsuario';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0){
		$info = mysql_fetch_array($res);

		$centroCostosUsuario = $info['Cc'];

		//Consulta de los grupos que no se permiten visualizar.
		$q2 = "SELECT Ccogka FROM {$wbasedato}_000011 WHERE Ccocod  = '".$info['Cc']."';";
		$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q2 - " . mysql_error());
		$num2 = mysql_num_rows($res2);

		if ($num2 > 0){
			$info2 = mysql_fetch_array($res2);

			$gruposVisibles = $info2['Ccogka'];
		}
	}
	return $gruposVisibles;
}

function centrosCostosRecibenCarro(){
	global $conex;
	global $wbasedato;

	$coleccion = array();

	$q = "SELECT
			Ccocod,Cconom
		FROM
			".$wbasedato."_000011
		WHERE
			Ccorec = 'on'
			AND Ccoest = 'on'
			AND DATE_FORMAT(NOW(),'%Y-%m-%d %T') >= CONCAT(Fecha_data,' ',Hora_data)
		ORDER BY
			Ccocod";

	//	echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		while ($cont1 < $num){
			$cont1++;

			$obj = new centroCostosDTO();

			$info = mysql_fetch_array($res);

			$obj->codigo = $info['Ccocod'];
			$obj->nombre = $info['Cconom'];

			$coleccion[] = $obj;

		}
	}
	return $coleccion;
}
/*******************************************************************************************************************************************************************
 * 2010-05-07: Msanchez
 *
 * Se debe considerar lo siguiente para la consulta de los articulos cargados:
 *
 * -Los articulos que se muestran en el carro son todos aquellos que tengan el campo Fdedis en 'on'.
 * -El servicio que carga se encuentra en Fencco (movhos_000002) y el servicio al que se carga se encuentra en cada registro del detalle en Fdeser (movhos_000003)
 * -Cuando son articulos del servicio farmacéutico, las cantidades se reciben tal cual como se registran en la tabla (movhos_000003)
 *
 * Ej. Fdenum  Fdeart  Fdeari  Fdecan
 * 	   ------  ------  ------  ------
 *    1184865  H2AB05  H2AB05  4
 *    1184865  A12CA2  A12CA2  8
 *
 *    Estos dos articulos se reciben independientemente en el carro.
 *
 * -Cuando los articulos son de la central de mezclas se agrupa por Fdeari y por Fdenum, haciendo que cada uno de los componentes del articulo Fdeari (los Fdeart)
 * sin importar la cantidad, compongan un articulo por numero de documento.
 *
 * Ej. Fdenum  Fdeart  Fdeari  Fdecan
 * 	   ------  ------  ------  ------
 *    1184859  J1CB18  DA0126  2
 *    1184859  E1AB11  DA0126  1
 *
 *    Este es un tazocin de la central de mezclas en minibolsa, se agrupan los articulos E1AB11 y J1CB18 sin importar la cantidad y este constituye UNA (1) cantidad,
 *    estos dos articulos se reciben en el carro como si fueran UNO solo.
 *
 *******************************************************************************************************************************************************************/
function consultarListadoPendienteReciboLactario($servicio,$grupos){
	global $conex;
	global $wbasedato;
	global $wemp_pmla;

	$col = array();

	$consulta = "";
	$pacientes = false;

	//*******************************Consulta de los codigos de cargos que se encuentren habilitados y disponibles en la tabla de centros de costos.
	$tiposCargo = "(";

	$q3 = "SELECT DISTINCT Ccofca FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccofca != '' AND Ccofca != 'NO APLICA'";
//	echo $q3;
	$res3 = mysql_query($q3, $conex);

	while($rs3 = mysql_fetch_array($res3)){
		$tiposCargo .= "'".$rs3['Ccofca']."',";
	}
	$tiposCargo .= "'')";
	//********************************

	$consulta = "<table width='100%'>";
	$consulta .= "<tr class=encabezadoTabla align=center>";
	$consulta .= "<td>Fecha del cargo</td>";
	$consulta .= "<td>Habitacion</td>";
	$consulta .= "<td>Historia</td>";
	$consulta .= "<td>Paciente</td>";
	$consulta .= "<td>Articulos</td>";
	$consulta .= "</tr>";

	// 2012-05-03
	//*******************************Consulta de los artículos a tener en cuenta de la tabla 26
	$articulosIncluidos = "(";

	$q7 = "SELECT DISTINCT Artcod FROM {$wbasedato}_000026 WHERE Artest='on' AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $grupos";
	$res7 = mysql_query($q7, $conex);

	while($rs7 = mysql_fetch_array($res7)){
		if(strpos($rs7['Artcod'],$articulosIncluidos) === false){
			$articulosIncluidos .= "'".str_replace(",","','",$rs7['Artcod'])."',";
		}
	}
	$articulosIncluidos .= "'')";
	//********************************

	//********************************Consulta las lineas de cargos realizadas para el paciente en los tipos de cargo anteriores
	$historias = "";
	$q4 = "SELECT Habhis, Habing, Habcod FROM {$wbasedato}_000020 WHERE Habest = 'on' AND Habdis = 'off' AND Habhis != '' AND Habing != '' ORDER BY Habcod";
	//echo $q4;
	$res4 = mysql_query($q4, $conex);

	while($rs4 = mysql_fetch_array($res4)){
//		$historias .= "'".$rs4['Habhis']."-".$rs4['Habing']."'";

		//********************************Consulta de los encabezados de cargos, extraigo solo los consecutivos del cargo
		$arrNumeros = array();
		$q5="SELECT Fennum, Fenlin, Fenfec, COUNT(*)
 			   FROM  (SELECT Fennum, Fenlin, Fenfec
						FROM
							{$wbasedato}_000002, {$wbasedato}_000003
						WHERE
							Fenhis = '".$rs4['Habhis']."'
							AND Fening = '".$rs4['Habing']."'
							AND Fenest = 'on'
							AND Fennum = Fdenum
							AND Fdeart IN ".$articulosIncluidos."
							AND Fdedis = 'on'
							AND Fdeest = 'on'
							AND (Fdecan-Fdecad - fdecar) > 0
							AND Fenfue IN $tiposCargo
						  GROUP BY Fennum, Fenlin
						UNION
						 SELECT
							 Fennum, Fenlin, Fenfec
						 FROM
							{$wbasedato}_000002, {$wbasedato}_000143
						WHERE
							Fenhis = '".$rs4['Habhis']."'
							AND Fening = '".$rs4['Habing']."'
							AND Fenest = 'on'
							AND Fennum = Fdenum
							AND Fdeart IN ".$articulosIncluidos."
							AND Fdedis = 'on'
							AND Fdeest = 'on'
							AND (Fdecan-Fdecad - fdecar) > 0
							AND Fenfue IN $tiposCargo
						  GROUP BY Fennum, Fenlin) a
			  WHERE fennum IS NOT NULL
			    AND fenlin IS NOT NULL
			    AND fenfec IS NOT NULL
			  GROUP BY Fennum, Fenlin";

        //echo '<pre> con '.$q5;

		$res5 = mysql_query($q5, $conex);
		$clase = "";
		$cont5 = 0;

		while($rs5 = mysql_fetch_array($res5)){

			$cont5++;

			if($clase == 'fila2'){
				$clase = 'fila1';
			} else {
				$clase = 'fila2';
			}
			$consulta = $consulta."<tr class='$clase'>";

			//*************************Paciente
			$q2 = "SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced FROM root_000036, root_000037 WHERE oriced = pacced AND orihis = '".$rs4['Habhis']."' AND oriing = '".$rs4['Habing']."' AND Oriori = '".$wemp_pmla;
//			echo $q2;
			$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el queryas: " . $q2 . " - " . mysql_error());
			$paciente = mysql_fetch_array($res2);
			//*************************

			$consulta = $consulta."<td align=center>".$rs5['Fenfec']." (".$rs5['Fennum'].")</td>";
			$consulta = $consulta."<td align=center>".$rs4['Habcod']."</td>";
			$consulta = $consulta."<td align=center>".$rs4['Habhis']."-".$rs4['Habing']."</td>";

			$nomPaciente = utf8_decode($paciente['pacno1'])." ".utf8_decode($paciente['pacno2'])." ".utf8_decode($paciente['pacap1'])." ".utf8_decode($paciente['pacap2']);
			$consulta = $consulta."<td>$nomPaciente</td>";

			$consulta = $consulta."<td align=center><a href='#null' onClick='javascript:consultarDetalleLactario(\"{$rs4['Habhis']}\",\"{$rs4['Habing']}\",\"$nomPaciente\",\"{$rs4['Habcod']}\",\"{$rs5['Fennum']}\",\"\",\"$servicio\");'>Ver</a></td>";

			$consulta = $consulta."</tr>";
			$pacientes = true;
		}
//		var_dump($arrNumeros);
		//********************************

	}
	$consulta = $consulta."</table>";
	$consulta = $consulta."<div align=center><br><input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></div>";

	return $consulta;
}

function consultarListadoRecibidoPaciente($historia,$ingreso,$consecutivo,$servicio,$causa){
	global $conex;
	global $wbasedato;
	global $wcenmez;

	$coleccion = array();

	//Rechis  Recing  Recnum  Recart  Reclin  Reccad  Recure  Recfre  Rechre  Reccaf  Reccar
	$q = "SELECT
			Recnum,Reclin,Recart,Reccad,Reccar,Recure,Recure,Recfre,Rechre,Reccaf
		FROM
		{$wbasedato}_000093
		WHERE
			Rechis = '$historia'
			AND Recing = '$ingreso'
			AND Recnum = '$consecutivo'
			AND Reccaf LIKE '$causa'
		ORDER BY
			Reclin,Recart;";

		//	echo $q;

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0)
		{
			$cont1 = 0;
			while ($cont1 < $num){
				$cont1++;

				$obj = new DetalleRecibo();

				$info = mysql_fetch_array($res);

				//Rechis  Recing  Recnum  Recart  Reclin  Reccad  Recure  Recfre  Rechre  Reccaf  Reccar
				$obj->numero			= $info['Recnum'];
				$obj->linea				= $info['Reclin'];
				$obj->historia 			= $historia;
				$obj->ingreso 			= $ingreso;
				$obj->codigoArticulo	= $info['Recart'];
				$obj->cantidadDespachada= $info['Reccad'];
				$obj->cantidadRecibida	= $info['Reccar'];
				$obj->causaFaltante		= $info['Reccaf'];
				$obj->fechaRecibo		= $info['Recfre'];
				$obj->horaRecibo		= $info['Rechre'];
				$obj->habitacion		= "";

				//*************************Descripcion del articulo
				$q2 = "SELECT Artcom, Artuni FROM ".$wbasedato."_000026 WHERE Artcod = '".$info['Recart']."'";
				$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				$info2 = mysql_fetch_array($res2);
				$num2 = mysql_num_rows($res2);

				if($num2 == 0){
					$q2 = "SELECT Artcom, Artuni FROM ".$wcenmez."_000002 WHERE Artcod = '".$info['Recart']."'";
					$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
					$info2 = mysql_fetch_array($res2);
				}
				//*************************

				//*************************Descripcion de la unidad
				$q3 = "SELECT Unides FROM ".$wbasedato."_000027 WHERE Unicod = '".$info2['Artuni']."'";
				$res3 = mysql_query($q3,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
				$info3 = mysql_fetch_array($res3);
				//*************************

				//*************************Descripcion de la unidad
				$q4 = "SELECT Descripcion FROM usuarios WHERE codigo = '".$info['Recure']."'";
				$res4 = mysql_query($q4,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q4 . " - " . mysql_error());
				$info4 = mysql_fetch_array($res4);
				//*************************

				$obj->usuarioRecibe = $info4['Descripcion'];
				$obj->nombreArticulo= $info2['Artcom'];
				$obj->presentacion	= $info2['Artuni']." - ".$info3['Unides'];

				$coleccion[] = $obj;
			}
		}
		return $coleccion;
}

function consultarCausasFaltantes(){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Caucod,Caudes
		FROM
			".$wbasedato."_000094
		WHERE
			Cauest = 'on'
		ORDER BY
			Caudes";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		while ($cont1 < $num){
			$cont1++;

			$obj = new RegistroSimple();

			$info = mysql_fetch_array($res);

			$obj->codigo 		= $info['Caucod'];
			$obj->descripcion	= $info['Caudes'];

			$coleccion[] = $obj;
		}
	}
	return $coleccion;
}

/**********************************************************************
 * Listado de medicos del sistema
 * UPDATE `movhos_000011` SET Ccorec = 'off'
 * UPDATE `movhos_000011` SET Ccorec = 'on' WHERE Ccohos = 'on'
 **********************************************************************/
function consultarFuentes(){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Ccofca
		FROM
			".$wbasedato."_000011
		WHERE
			Ccoest = 'on'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0){
		$cont1 = 0;
		while ($cont1 < $num){
			$cont1++;

			$medico = new medicoDTO();

			$info = mysql_fetch_array($res);

			$medico->tipoDocumento = $info['Medtdo'];
			$medico->numeroDocumento = $info['Meddoc'];
			$medico->nombre1 = $info['Medno1'];
			$medico->nombre2 = $info['Medno2'];
			$medico->apellido1 = $info['Medap1'];
			$medico->apellido2 = $info['Medap2'];
			$medico->registroMedico = $info['Medreg'];
			$medico->telefono = $info['Medtel'];
			$medico->codigoEspecialidad = $info['Medesp'];
			$medico->id = $info['id'];

			$coleccion[] = $medico;

		}
	}
	return $coleccion;
}
function grabarDetalle($col,$arregloId){
	global $wbasedato;
	global $conex;

	$vecItems = explode(";",$arregloId);
	$codigoAnterior = "";
	$nuevoCodigo    = "";

	foreach ($col as $articulo){

		$nuevoCodigo = $articulo->consecutivoCargo."-".$articulo->articulo;

		if( $codigoAnterior != $nuevoCodigo ){
			$q = "INSERT INTO ".$wbasedato."_000093(Medico,Fecha_data,Hora_data,Rechis,Recing,Recnum,Recart,Reccad,Recure,Recfre,Rechre,Reccaf,Reccar,Reclin,Reccde,Seguridad) VALUES
			('movhos','".date("Y-m-d")."','".date("H:i:s")."','".$articulo->historia."','".$articulo->ingreso."','".$articulo->consecutivoCargo."','".$articulo->articulo."','$articulo->cantidadDespachada','$articulo->usuarioRecibe','$articulo->fechaRecibo','$articulo->horaRecibo','$articulo->causaFaltante','$articulo->cantidadRecibida','','$articulo->cantidadDevuelta','A-$articulo->usuarioRecibe')";

		}else{
			$q = "UPDATE ".$wbasedato."_000093
			         SET Reccar = Reccar + '{$articulo->cantidadRecibida}',
			             Reccad = Reccad + '{$articulo->cantidadDespachada}'
			       WHERE Rechis = '{$articulo->historia}'
   					 AND Recing = '{$articulo->ingreso}'
   					 AND Recnum = '{$articulo->consecutivoCargo}'
			         AND Recart = '{$articulo->articulo}'";
		}
		$codigoAnterior = $nuevoCodigo;
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q - ". mysql_error());


		if($articulo->cantidadRecibida > 0){
			$recibidoTotal = $articulo->cantidadRecibida;

			//Itero sobre los articulos
			foreach($vecItems as $artId){
				$vecArticulo = explode("|",$artId);
				if(isset($vecArticulo[1]) && $vecArticulo[1] != '' && $vecArticulo[1] == $articulo->articulo && isset($vecArticulo[5]) && $vecArticulo[5] != '' && $vecArticulo[5] == $articulo->consecutivoCargo){
					if($vecArticulo[2] != "3"){
						if(($recibidoTotal-$vecArticulo[3]) >= 0 || $articulo->cantidadDespachada == $articulo->cantidadRecibida){
							if($recibidoTotal > 0){		//Actualizo
								if( $vecArticulo[6] == $articulo->lineaCargo ){

									$q2 = "UPDATE ".$wbasedato."_".$vecArticulo[4]." SET Fdedis = 'off', Fdecar = Fdecar + ".$articulo->cantidadRecibida." WHERE id = '{$vecArticulo[0]}';";
									$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q2 - ". mysql_error());
									$recibidoTotal -= $vecArticulo[3];
								}
							}

						} else {

							if( $vecArticulo[6] == $articulo->lineaCargo ){
									$q2 = "UPDATE ".$wbasedato."_".$vecArticulo[4]." SET Fdecar = Fdecar + ".$articulo->cantidadRecibida	." WHERE id = '{$vecArticulo[0]}';";
									$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q2 - ". mysql_error());
							}

						}
					} else {
						$q2 = "UPDATE ".$wbasedato."_".$vecArticulo[4]." SET Fdedis = 'off' WHERE Fdenum = '$articulo->consecutivoCargo' AND Fdeari='$articulo->articulo';";
						$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q2 - ". mysql_error());
					}
				}
			}
		}
	}
}

//$articulo es de tipo DetalleRecibo
function actualizarCausaFaltante($articulo){
	global $wbasedato;
	global $conex;

	$q = "UPDATE ".$wbasedato."_000093 SET
				Reccaf = '$articulo->causaFaltante'
			WHERE
				Rechis = '$articulo->historia'
				AND Recing = '$articulo->ingreso'
				AND Recnum = '$articulo->consecutivoCargo'
				AND Recart = '$articulo->articulo'
				AND Recfre = '$articulo->fechaRecibo'
				AND Rechre = '$articulo->horaRecibo';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/*******************************************************************************************************************************************************************
 * 2010-04-21: Msanchez
 *
 * Se debe considerar lo siguiente para la consulta de los articulos cargados:
 *
 * -Los articulos que se muestran en el carro son todos aquellos que tengan el campo Fdedis en 'on'.
 * -El servicio que carga se encuentra en Fencco (movhos_000002) y el servicio al que se carga se encuentra en cada registro del detalle en Fdeser (movhos_000003)
 * -Cuando son articulos del servicio farmacéutico, las cantidades se reciben tal cual como se registran en la tabla (movhos_000003)
 *
 * Ej. Fdenum  Fdeart  Fdeari  Fdecan
 * 	   ------  ------  ------  ------
 *    1184865  H2AB05  H2AB05  4
 *    1184865  A12CA2  A12CA2  8
 *
 *    Estos dos articulos se reciben independientemente en el carro.
 *
 * -Cuando los articulos son de la central de mezclas se agrupa por Fdeari y por Fdenum, haciendo que cada uno de los componentes del articulo Fdeari (los Fdeart)
 * sin importar la cantidad, compongan un articulo por numero de documento.
 *
 * Ej. Fdenum  Fdeart  Fdeari  Fdecan
 * 	   ------  ------  ------  ------
 *    1184859  J1CB18  DA0126  2
 *    1184859  E1AB11  DA0126  1
 *
 *    Este es un tazocin de la central de mezclas en minibolsa, se agrupan los articulos E1AB11 y J1CB18 sin importar la cantidad y este constituye UNA (1) cantidad,
 *    estos dos articulos se reciben en el carro como si fueran UNO solo.
 *
 *******************************************************************************************************************************************************************/
function consultarArticulosPendientesReciboPaciente($historia,$ingreso,$consecutivo,$servicio){
	global $conex;
	global $wbasedato;
	global $wcenmez;

	$tipo = "";

	//*******************************Consulta de los grupos de la tabla 11 que sean distintos al servicio seleccionado
	$tieneGruposExcluidos = false;
	$gruposExcluidos = "(";

	$q6 = "SELECT DISTINCT Ccogka FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccocod = '$servicio' AND Ccogka != '*'";
//	echo $q6;
	$res6 = mysql_query($q6, $conex);

	while($rs6 = mysql_fetch_array($res6)){
		$tieneGruposExcluidos = true;
		$gruposExcluidos .= "'".str_replace(",","','",$rs6['Ccogka'])."',";
	}
	$gruposExcluidos .= "'')";
//	echo $gruposExcluidos;
	//********************************

	// 2012-05-03
	//*******************************Consulta de los artículos a tener en cuenta de la tabla 26
	$articulosIncluidos = "(";

	$q7 = "SELECT DISTINCT Artcod FROM {$wbasedato}_000026 WHERE Artest='on' AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposExcluidos";
	$res7 = mysql_query($q7, $conex);

	while($rs7 = mysql_fetch_array($res7)){
		if(strpos($rs7['Artcod'],$articulosIncluidos) === false){
			$articulosIncluidos .= "'".str_replace(",","','",$rs7['Artcod'])."',";
		}
	}
	$articulosIncluidos .= "'')";
	//********************************


	$coleccion = array();

	//Medico  Fecha_data  Hora_data  Fdenum  Fdelin  Fdeart  Fdeari  Fdecan  Fdeubi  Fdeinf  Fdeinh  Fdeinu  Fdelot  Fdeest  Fdeser  Fdedis  Seguridad  id
	//AND Fdeart NOT IN (SELECT Artcod FROM {$wbasedato}_000026 WHERE Artest='on' AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposExcluidos)
	$q = "SELECT *
			FROM (
				SELECT
					{$wbasedato}_000003.id, Fdenum, Fdelin, UPPER(Fdeart) Fdeart, UPPER(Fdeari) Fdeari, (Fdecan-Fdecad -Fdecar) Fdecan, Fencco, '000003' tablaOrigen, Fdelot
				FROM
					{$wbasedato}_000002, {$wbasedato}_000003
				WHERE
					Fennum = '$consecutivo'
					AND Fennum = Fdenum
					AND Fdeart IN ".$articulosIncluidos."
					AND Fdeest = 'on'
					AND Fdedis = 'on'
					AND (Fdecan-Fdecad) > 0
				UNION
				SELECT
					{$wbasedato}_000143.id, Fdenum, Fdelin, UPPER(Fdeart) Fdeart, UPPER(Fdeari) Fdeari, (Fdecan-Fdecad -Fdecar) Fdecan, Fencco, '000143' tablaOrigen, Fdelot
				FROM
					{$wbasedato}_000002, {$wbasedato}_000143
				WHERE
					Fennum = '$consecutivo'
					AND Fennum = Fdenum
					AND Fdeart IN ".$articulosIncluidos."
					AND Fdeest = 'on'
					AND Fdedis = 'on'
					AND (Fdecan-Fdecad) > 0
				)a
			ORDER BY a.Fdeari, a.Fdeart";

	//echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		while ($cont1 < $num){
			$cont1++;

			$obj = new ArticulosPendienteRecibo();

			$info = mysql_fetch_array($res);

			//Atributos que cambian
			$articulo = "";					//Codigo del articulo puede ser Fdeart o Fdeari
			$cantidad = "";					//Cantidad despachada puede ser Fdecan o la agrupacion de uno o mas articulos bajo un mismo Fdeari y Fdenum

			//				echo "<br>".$info['Fdeari']."i";

			//Proceso de los articulos
			if(trim($info['Fdelot']) == ""){
				$articulo = $info['Fdeart'];
				$cantidad = $info['Fdecan'];

				$obj->tipo = "1";
			} else {
				if(strpos($info['Fdeari'],"N*") === false){
					//*************************Consulta del codigo de barras
					$q5 = "SELECT Artcod FROM ".$wbasedato."_000009 WHERE Artcba = '".$info['Fdeari']."'";
					$res5 = mysql_query($q5,$conex) or die ("Error: ".mysql_errno()." - en el query: $q5 - ".mysql_error());
					$num5 = mysql_num_rows($res5);
					//*************************

					if($num5 > 0){ //Es codigo de barras legible en la tabla 9
						$articulo = $info['Fdeart'];
						$cantidad = $info['Fdecan'];

						$obj->tipo = "2";
					} else { // Agrupo los articulos
						$articulo = $info['Fdeari'];
						$cantidad = "0";

						$obj->tipo = "3";
					}
				} else {
					$articulo = $info['Fdeart'];
					$cantidad = $info['Fdecan'];

					$obj->tipo = "4";
				}
			}


			// 2012-05-03
			// Se cosulta si el artículo maneja cantidad por defecto y se obtiene esta cantidad
			$qcde = "	SELECT Arecde
						  FROM {$wbasedato}_000008
						 WHERE Areces = '".$info['Fdeart']."'
						   AND Arecco = '".$info['Fencco']."';";
			$rescde = mysql_query($qcde, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcde . " - " . mysql_error());
			$numcde = mysql_num_rows($rescde);
			$infocde = mysql_fetch_array($rescde);

			if($numcde>0)
				$incremento = $infocde['Arecde'];
			else
				$incremento = '1';

			//Ubihis,Ubiing,Ubisac,Ubihac
			//Fdenum,Fdelin,Fdeart,Fdecan
			$obj->numero			= $info['Fdenum'];
			$obj->linea				= $info['Fdelin'];
			$obj->historia 			= $historia;
			$obj->ingreso 			= $ingreso;
			$obj->codigoArticulo	= strtoupper($articulo);
			$obj->cantidadDespachada= $cantidad;
//			$obj->habitacion		= $info['Ubihac'];
			$obj->idRegistro		= $info['id'];
			$obj->incremento		= $incremento;
			$obj->tablaOrigen		= $info['tablaOrigen'];

			//*************************Descripcion del articulo
			$q2 = "SELECT Artcom, Artuni FROM ".$wbasedato."_000026 WHERE Artcod = '".$articulo."'";
			$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$info2 = mysql_fetch_array($res2);
			$num2 = mysql_num_rows($res2);

			if($num2 == 0){
				$q2 = "SELECT Artcom, Artuni FROM ".$wcenmez."_000002 WHERE Artcod = '".$articulo."'";
				$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				$info2 = mysql_fetch_array($res2);
			}
			//*************************

			//*************************Descripcion de la unidad
			$q3 = "SELECT Unides FROM ".$wbasedato."_000027 WHERE Unicod = '".$info2['Artuni']."'";
			$res3 = mysql_query($q3,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
			$info3 = mysql_fetch_array($res3);
			//*************************

			$obj->nombreArticulo= $info2['Artcom'];
			$obj->presentacion	= $info2['Artuni']." - ".$info3['Unides'];
			$coleccion[] = $obj;
		}
	}
	return $coleccion;
}
/*****************
 * Inicio
 *****************/
$wactualiz = " 2021-11-04";
$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
encabezado("Recibo de lactario",$wactualiz,"clinica");

if(!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} else {
	//Conexion base de datos
	$conex = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

	//Forma
	echo "<form name='forma' action='ReciboDispensacionLactario.php?wemp_pmla=".$wemp_pmla."' method='post'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
	echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...";
	echo "</div>";

	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}

	//FC para hacer las acciones
	switch ($waccion){
		case 'a':		//Consulta de detalle por paciente con historia e ingreso
			if(isset($whistoria) && $whistoria != '' && isset($wingreso) && $wingreso != ''){

				$arrCantidades = "";
				$cantidadDespachada = 0;

				echo "<input type='HIDDEN' NAME='whistoria' value='".$whistoria."'/>";
				echo "<input type='HIDDEN' NAME='wingreso' value='".$wingreso."'/>";
				echo "<input type='HIDDEN' NAME='consecutivo' value='".$consecutivo."'/>";
				echo "<input type='HIDDEN' NAME='wservicio' value='".$wservicio."'/>";

				$col = consultarArticulosPendientesReciboPaciente($whistoria,$wingreso,$consecutivo,$wservicio);
				$colCausas = consultarCausasFaltantes();

				if(count($col) > 0){
					//Ingreso de fecha de consulta
					echo '<span class="subtituloPagina2">';
					echo "Detalle pendiente recibo para $whistoria - $wingreso. <b>$nombrePaciente</b>.  Habitación: $habitacion";
					echo "</span>";
					echo "<br>";
					echo "<br>";

					//Convenciones
					echo "<div align=right>";
					echo "<table>";
					echo "<tr align=center>";
					echo "<td class='fila2' width=100>Sin modificar</td>";
					echo "<td class='fondoRojo' width=100>Recibido parcialmente</td>";
					echo "<td class='fondoVerde' width=100>Recibido completamente</td>";
					echo "</tr>";
					echo "</table>";
					echo "</div>";
					echo "<br>";

					$clase                     = "";
					$i                         = 1;
					$codigo                    = "";

					/**variables para consolidar*/
					$codigoAnteriorConsolidado = "";
					$codigoActualConsolidado   = "";

					$arrCantidades = "";
					$arrArticulosContenedor  = array();
					$arrArticulos            = array();
					$arrArticulosConsolidado = array();


					foreach ($col as $elemento){

						/** codigo para diferenciar elementos **/
						$codigoActualConsolidado = $elemento->codigoArticulo.$elemento->numero;
						$codigo = $elemento->codigoArticulo.$elemento->numero.$elemento->linea;

						/** Esta asignación se hace directa porque son los datos individuales de cada registro**/
						$cantidadDespachada    = $elemento->cantidadDespachada;
						$cantidadDevuelta      = $elemento->cantidadDevuelta;
						$cantidadSinDevolucion = $elemento->cantidadSinDevolucion;
						$cantidadRecibida      = $elemento->cantidadRecibida;


						if( $codigoActualConsolidado == $codigoAnteriorConsolidado ){
							$cantidadDespachadaConsolidado    += $elemento->cantidadDespachada;
							$cantidadDevueltaConsolidado      += $elemento->cantidadDevuelta;
							$cantidadSinDevolucionConsolidado += $elemento->cantidadSinDevolucion;
							@$cantidadRecibidaConsolidado      +=(integer)$elemento->cantidadRecibida;
						}else{
							$cantidadDespachadaConsolidado    = $elemento->cantidadDespachada;
							$cantidadDevueltaConsolidado      = $elemento->cantidadDevuelta;
							$cantidadSinDevolucionConsolidado = $elemento->cantidadSinDevolucion;
							$cantidadRecibidaConsolidado      = $elemento->cantidadRecibida;
						}



						$arrCantidades .= "{$elemento->idRegistro}|{$elemento->codigoArticulo}|{$elemento->tipo}|{$elemento->cantidadDespachada}|{$elemento->tablaOrigen}|{$elemento->numero}|{$elemento->linea};";

						$registro            = new RegistroArticulo();
						$registroConsolidado = new RegistroArticulo();


						$registro->codigo  = $codigo;
						$registroConsolidado->codigo  = $codigoActualConsolidado;


						$articulo = $elemento->codigoArticulo." - ".$elemento->nombreArticulo;
						$presentacion = $elemento->presentacion;


						$registro->campo1            = $elemento->codigoArticulo." - ".$elemento->nombreArticulo;
						$registroConsolidado->campo1 = $elemento->codigoArticulo." - ".$elemento->nombreArticulo;
						$registro->campo2            = $elemento->presentacion;
						$registroConsolidado->campo2 = $elemento->presentacion;
						$registro->campo3            = intval($cantidadDespachada) - intval( $cantidadRecibida );
						$registroConsolidado->campo3 = intval($cantidadDespachadaConsolidado) - intval( $cantidadRecibidaConsolidado );
						$registro->campo4            = $elemento->incremento;
						$registroConsolidado->campo4 = $elemento->incremento;
						$registro->campo5            = $elemento->tablaOrigen;
						$registroConsolidado->campo5 = $elemento->tablaOrigen;
						$registro->campo6            = $elemento->idRegistro;
						$registroConsolidado->campo6 = $elemento->idRegistro;
						$registro->campo7            = $elemento->linea;
						$registroConsolidado->campo7 = $elemento->linea;
						$registro->campo8            = $elemento->numero;
						$registroConsolidado->campo8 = $elemento->numero;

						$arrArticulos[$codigo]                             = $registro;
						$arrArticulosConsolidado[$codigoActualConsolidado] = $registroConsolidado;

						$codigoAnteriorConsolidado = $codigoActualConsolidado;

						$i++;
					}
					$arrArticulosContenedor['individuales'] = $arrArticulos;
					$arrArticulosContenedor['consolidado']  = $arrArticulosConsolidado;
					$numeroIndividuales = count( $arrArticulosContenedor['individuales'] );

					echo "<input type='HIDDEN' NAME='arrCantidades' value='".$arrCantidades."'/>";
					echo "<input type='hidden' id='limiteFilas' value='".count( $arrArticulosContenedor['individuales'] )."'>";

					echo "<table align=center>";

					//Salto al articulo
					echo "<tr>";
					echo "<td class=fila1 colspan=4>Buscar articulo en esta lista</td>";
					echo "<td class=fila2 colspan=3><INPUT TYPE='text' id='wtarticulo' NAME='wtarticulo' value='' SIZE=10 onkeypress='return teclaEnter(event,"."\"validarArticulo();\");' class='textoNormal'></td>";
					echo "</tr>";

					echo "<tr class=encabezadoTabla align=center>";

					echo "<td>#</td>";
					echo "<td>Articulo</td>";
					echo "<td>Presentacion</td>";
					echo "<td>Cantidad <br>Despachada</td>";
					echo "<td>Cantidad <br>Recibida</td>";
					echo "<td>Cantidad <br>Pendiente</td>";
					echo "<td>Causa de faltante</td>";

					echo "</tr>";
					$i = 1;
					//Despliegue del proceso del agrupamiento
					if(count($arrArticulosContenedor['individuales'])>0){

							foreach ($arrArticulosContenedor['individuales'] as $elem){

								echo "<tr class=fila2 id='tr$i' tipo='individual' codigo='$elem->codigo' numeroLinea='$i' style='display:none;'>";

								$vecArticulo = explode(" - ",$elem->campo1);
								echo "<td>{$i}";
								echo "<INPUT TYPE='hidden' id='whcodigo$i' NAME='whcodigo$i' value='".$vecArticulo[0]."'>";
								echo "<INPUT TYPE='hidden' id='whlinea$i' NAME='whlinea$i' value='$elem->campo7'>";
								echo "<INPUT TYPE='hidden' id='whincremento$i' NAME='whincremento$i' value='$elem->campo4'>";
								echo "<INPUT TYPE='hidden' id='whtablaorigen$i' NAME='whtablaorigen$i' value='$elem->campo5'>";
								echo "<INPUT TYPE='hidden' id='whidRegistro$i' NAME='whidRegistro$i' value='$elem->campo6'>";
								echo "<INPUT TYPE='hidden' id='whconsecutivoCargo$i' NAME='whconsecutivoCargo$i' value='$elem->campo7'>";
								echo "</td>";
								echo "<td>$elem->campo1</td>";
								echo "<td>$elem->campo2</td>";

								//Uso exclusivo para nutriciones y dosis adaptadas
								if($elem->campo3 == 0){
									$elem->campo3 = 1;
								}

								echo "<td align=center><INPUT TYPE='text' id='wtmaxrecibido$i' NAME='wtmaxrecibido$i' value='$elem->campo3' SIZE=5 class='fila2' readonly></td>";
								echo "<td id='tdRec$i' align=center><INPUT TYPE='text' id='wtrecibido$i' NAME='wtrecibido$i' value='0' SIZE=5 maxlength=5 onBlur='javascript:validarCantidad($i, \"ind\");' onKeyPress='return validarEntradaEntera(event);' class='textoNormal'></td>";
								echo "<td align=center><INPUT TYPE='text' id='wtpendiente$i' NAME='wtpendiente$i' value='$elem->campo3' SIZE=5 class='fila2' readonly></td>";

								echo "<td>";
								echo "<select id='wscausa$i' onChange='javascript:validarSeleccionCausa($i);'>";
								echo "<option value=''>Sin faltante</option>";
								foreach ($colCausas as $elemento1){
									echo "<option value=$elemento1->codigo>$elemento1->descripcion</option>";
								}
								echo "</select>";
								echo "</td>";

								echo "</tr>";
								$i++;
							}
						}
						if(count($arrArticulosContenedor['consolidado'])>0){
							foreach ($arrArticulosContenedor['consolidado'] as $elem){
								echo "<tr class=fila2 id='tr$i' tipo='consolidado' codigo='$elem->codigo' numeroLinea='$i'>";

								$vecArticulo = explode(" - ",$elem->campo1);
								$numeroFila  = $i - $numeroIndividuales;
								echo "<td>{$numeroFila}";
								echo "<INPUT TYPE='hidden' id='whcodigo$i' NAME='whcodigo$i' value='".$vecArticulo[0]."'>";
								echo "<INPUT TYPE='hidden' id='whlinea$i' NAME='whlinea$i' value=''>";
								echo "<INPUT TYPE='hidden' id='whincremento$i' NAME='whincremento$i' value='$elem->campo4'>";
								echo "<INPUT TYPE='hidden' id='whtablaorigen$i' NAME='whtablaorigen$i' value='$elem->campo5'>";
								echo "</td>";
								echo "<td>$elem->campo1</td>";
								echo "<td>$elem->campo2</td>";

								//Uso exclusivo para nutriciones y dosis adaptadas
								if($elem->campo3 == 0){
									$elem->campo3 = 1;
								}

								echo "<td align=center><INPUT TYPE='text' id='wtmaxrecibido$i' NAME='wtmaxrecibido$i' value='$elem->campo3' SIZE=5 class='fila2' readonly></td>";
								echo "<td id='tdRec$i' align=center><INPUT TYPE='text' id='wtrecibido$i' NAME='wtrecibido$i' value='0' SIZE=5 maxlength=5 onBlur='javascript:validarCantidad($i, \"cons\");' onKeyPress='return validarEntradaEntera(event);' class='textoNormal'></td>";
								echo "<td align=center><INPUT TYPE='text' id='wtpendiente$i' NAME='wtpendiente$i' value='$elem->campo3' SIZE=5 class='fila2' readonly></td>";
								echo "<INPUT TYPE='hidden' id='cantidadRecibidaAnterior$i' NAME='cantidadRecibidaAnterior$i' value='0'>";

								echo "<td>";
								echo "<select id='wscausa$i' onChange='javascript:validarSeleccionCausa($i);'>";
								echo "<option value=''>Sin faltante</option>";
								foreach ($colCausas as $elemento1){
									echo "<option value=$elemento1->codigo>$elemento1->descripcion</option>";
								}
								echo "</select>";
								echo "</td>";

								echo "</tr>";
								$i++;
							}
					 }

					echo "<tr><td align=center colspan=7><br><input type='button' id='btnGrabar' value='Grabar recibo' onClick='javascript:grabarRecibo();'>&nbsp;|&nbsp;<input type='button' value='Regresar' onClick='javascript:inicio(\"$wservicio\");'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr>";

					echo "</table>";
				} else {
					mensajeEmergente("No hay pendientes de recibo para el servicio.");
				}
			} else {
				mensajeEmergente("No se capturó historia e ingreso.");
			}
			break;
		case 'b':		//Grabacion del recibo del carro de dispensacion
			if(isset($whistoria) && $whistoria != '' && isset($wingreso) && $wingreso != '' && isset($wdatos) && $wdatos != '' && isset($consecutivo) && $consecutivo != ''){
				echo "<input type='HIDDEN' NAME='arrCantidades' value='".$arrCantidades."'/>";

				$vecArticulos = explode(";",$wdatos);  //Division por articulo

				$fechaRecibo 	= date("Y-m-d");
				$horaRecibo 	= date("H:i:s");

				//Iteracion sobre todos los articulos
				foreach ($vecArticulos as $articulo){
					$vecParametros = explode("|",$articulo);  //Division por datos

					if($vecParametros[0] != ''){
						$detalle = new DetalleRecibo();

						$detalle->historia 			= $whistoria;
						$detalle->ingreso 			= $wingreso;
						$detalle->consecutivoCargo 	= $consecutivo;
						$detalle->articulo 			= $vecParametros[0];
						$detalle->cantidadDespachada= $vecParametros[1];
						$detalle->cantidadRecibida 	= $vecParametros[2];
						$detalle->lineaCargo		= $vecParametros[3];
						$detalle->causaFaltante 	= $vecParametros[4];
						$detalle->tablaOrigen    	= $vecParametros[5];
						$detalle->idRegistro    	= $vecParametros[6];
						$detalle->fechaRecibo 		= $fechaRecibo;
						$detalle->horaRecibo 		= $horaRecibo;
						$detalle->usuarioRecibe		= $wuser;

						$col[] = $detalle;
					}
				}
				/*echo "<pre>";
					print_r( $col );
				echo "</pre>";
				return;*/
				//Grabar detalle
				grabarDetalle($col,$arrCantidades);

				//M3n54j3 de confirmacion
				echo '<div align=center><span class="subtituloPagina2" align=center>';
				echo "Se ha grabado el recibo correctamente.  Historia $whistoria - $wingreso";
				echo "</span>";
				echo "<br>";
				echo "<br>";
			} else {
				mensajeEmergente("No hay articulos para recibir.");
			}

			echo "<input type='button' value='Regresar' onClick='javascript:inicio(\"$servicio\");'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></div>";
			break;
		case 'c':   //Recibos de carros de dispensacion por servicio
			//Muestra la pantalla inicial
			//Cuerpo de la pagina

			//Consulta del centro de costos del usuario
			$centroCostosUsuario = "";

			$gruposConsulta = "(";
			$gruposConsulta .= "'".str_replace(",","','",consultarGruposPorCcoUsuario("01156",$centroCostosUsuario))."',";
			$gruposConsulta .= "'')";

			echo "<input type=hidden id='whserviciousr' name='whserviciousr' value='$centroCostosUsuario'>";

			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Consulta carros recibidos';
			echo "</span>";
			echo "<br>";
			echo "<br>";

			//Fecha inicial
			echo "<tr>";
			echo "<td class='fila1'>Fecha inicial</td>";
			echo "<td class='fila2' align='center'>";
			if (!isset($wfechai) or $wfechai == '')
			 	$wfechai = date("Y-m-d");
 			campoFechaDefecto("wfechai",$wfechai);
			echo "</td></tr>";

			//Fecha final
			echo "<tr>";
			echo "<td class='fila1'>Fecha final</td>";
			echo "<td class='fila2' align='center'>";
			if (!isset($wfechaf) or $wfechaf == '')
			 	$wfechaf = date("Y-m-d");
			campoFechaDefecto("wfechaf",$wfechaf);
			echo "</td></tr>";

			//Causas de faltante
			$colCausas = consultarCausasFaltantes();
			echo "<tr><td class='fila1' width=170>Causa de faltante</td>";
			echo "<td class='fila2' align='center'>";
			echo "<select id='wscausa' NAME='wscausa' class='textoNormal'>";
			echo "<option value='%'>Todos</option>";
			foreach ($colCausas as $causa) {
				if(isset($wscausa) && !empty($wscausa) && $wscausa == $causa->codigo){
					echo "<option value=".$causa->codigo." selected>".$causa->descripcion."</option>";
				} else {
					echo "<option value=".$causa->codigo.">".$causa->descripcion."</option>";
				}
			}
			echo "</select>";
			echo "</td>";
			echo "</tr>";

			//Servicio
			$centrosCostos = centrosCostosRecibenCarro();
			echo "<tr><td class='fila1' width=170>Servicio</td>";
			echo "<td class='fila2' align='center'>";
			if(isset($servicio) && !empty($servicio)){
				echo "<select id='wsservicio' NAME='wsservicio' onchange='javascript:consultarRecibos();' class='textoNormal' value='$servicio'>";
			} else {
				echo "<select id='wsservicio' NAME='wsservicio' onchange='javascript:consultarRecibos();' class='textoNormal'>";
			}
			echo "<option value=''>Seleccione</option>";
			foreach ($centrosCostos as $centroCostosHospitalario) {
				if(isset($servicio) && !empty($servicio) && $servicio == $centroCostosHospitalario->codigo){
					echo "<option value=".$centroCostosHospitalario->codigo." selected>".$centroCostosHospitalario->codigo."-".$centroCostosHospitalario->nombre."</option>";
				} else {
					echo "<option value=".$centroCostosHospitalario->codigo.">".$centroCostosHospitalario->codigo."-".$centroCostosHospitalario->nombre."</option>";
				}
			}
			echo "</select>";
			echo "</td>";
			echo "</tr>";

			//Habitacion - paciente
			echo "<tr>";
			echo "<td align='center' colspan=2>";
			echo "<div id='cntHabitacion'>";
			echo "</div>";
			echo "</tr>";

			echo "<tr><td align=center colspan=4><br><input type=button value='Consultar' onclick='javascript:consultarRecibos();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</table>";

			if(isset($servicio) && !empty($servicio)){
				funcionJavascript("try {consultarRecibos();} catch(ex){}");
			}
			break;
		case 'd' :	//Detalle de lo recibido para un paciente
			if(isset($whistoria) && $whistoria != '' && isset($wingreso) && $wingreso != ''){
				$existeActivo = false;

				echo "<input type='HIDDEN' NAME='whistoria' value='".$whistoria."'/>";
				echo "<input type='HIDDEN' NAME='wingreso' value='".$wingreso."'/>";
				echo "<input type='HIDDEN' NAME='consecutivo' value='".$consecutivo."'/>";
				echo "<input type='HIDDEN' NAME='habitacion' value='".$habitacion."'/>";
				echo "<input type='HIDDEN' NAME='servicio' value='".$servicio."'/>";

				$col = consultarListadoRecibidoPaciente($whistoria,$wingreso,$consecutivo,$servicio,$causa);

				$colCausas = consultarCausasFaltantes();

				if(count($col) > 0){
					//Ingreso de fecha de consulta
					echo '<span class="subtituloPagina2">';
					echo "Consulta de articulos recibidos.  Habitaci&oacute;n $habitacion, Paciente <b>$nombrePaciente</b> ($whistoria - $wingreso)";
					echo "</span>";
					echo "<br>";
					echo "<br>";

					echo "<table align=center>";

					echo "<tr class=encabezadoTabla>";

					echo "<td>#</td>";
					echo "<td>Articulo</td>";
					echo "<td>Presentacion</td>";
					echo "<td>Usuario que recibio</td>";
					echo "<td>Fecha recibo</td>";
					echo "<td>Hora recibo</td>";
					echo "<td>Cantidad<br>Despachada</td>";
					echo "<td>Cantidad<br>Recibida</td>";
					echo "<td>Causal de faltante</td>";

					echo "</tr>";

					$clase = "";
					$i = 1;
					foreach ($col as $elemento){
						$clase = ($clase == "fila1") ? "fila2" : "fila1";

						echo "<tr class=$clase>";

						echo "<td>$i";
						echo "<INPUT TYPE='hidden' id='whcodigo$i' NAME='whcodigo$i' value='".$elemento->codigoArticulo."'>";		//Codigo del articulo
						echo "<INPUT TYPE='hidden' id='whlinea$i' NAME='whlinea$i' value='".$elemento->linea."'>";					//Linea de cargo
						echo "<INPUT TYPE='hidden' id='whfrecibo$i' NAME='whfrecibo$i' value='".$elemento->fechaRecibo."'>";		//Fecha de recibo
						echo "<INPUT TYPE='hidden' id='whhrecibo$i' NAME='whhrecibo$i' value='".$elemento->horaRecibo."'>";			//Hora de recibo

						echo "</td>";
						echo "<td>$elemento->codigoArticulo - $elemento->nombreArticulo</td>";
						echo "<td>$elemento->presentacion</td>";
						echo "<td>$elemento->usuarioRecibe</td>";
						echo "<td align=center>$elemento->fechaRecibo</td>";
						echo "<td align=center>$elemento->horaRecibo</td>";
						echo "<td align=center>$elemento->cantidadDespachada</td>";
						echo "<td align=center>$elemento->cantidadRecibida</td>";

						echo "<td>";
						if($elemento->cantidadDespachada == $elemento->cantidadRecibida || ($elemento->cantidadDespachada != $elemento->cantidadRecibida && $elemento->causaFaltante == '')){
							echo "<select id='wscausa$i' onChange='javascript:validarSeleccionCausa($i);' disabled>";
							echo "<option value=''>Sin faltante</option>";
						} else {
							echo "<select id='wscausa$i' onChange='javascript:validarSeleccionCausa($i);'>";
							$existeActivo = true;
						}
						foreach ($colCausas as $item){
							if($item->codigo == $elemento->causaFaltante){
								echo "<option value=$item->codigo selected>$item->descripcion</option>";
							} else {
								echo "<option value=$item->codigo>$item->descripcion</option>";
							}
						}
						echo "</select>";
						echo "</td>";

						echo "</tr>";
						$i++;
					}
					echo "<tr><td align=center colspan=10><br>";
					if($existeActivo){
						echo "<input type='button' id='btnGrabar' value='Actualizar causas faltantes' onClick='javascript:actualizarRecibido();'>&nbsp;|&nbsp;";
					}
					echo "<input type='button' value='Regresar' onClick='javascript:inicioRecibido(\"$servicio\",\"$wfechai\",\"$wfechaf\",\"$wscausa\");'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr>";

					echo "</table>";
				} else {
					mensajeEmergente("No hay pendientes de recibo para el servicio.");
				}
			} else {
				mensajeEmergente("No se capturó historia e ingreso.");
			}
			break;
		case 'e':		//Grabacion del recibo del carro de dispensacion
			if(isset($whistoria) && $whistoria != '' && isset($wingreso) && $wingreso != '' && isset($wdatos) && $wdatos != '' && isset($consecutivo) && $consecutivo != ''){
				$vecArticulos = explode(";",$wdatos);  //Division por articulo

				//Iteracion sobre todos los articulos
				foreach ($vecArticulos as $articulo){
					$vecParametros = explode("|",$articulo);  //Division por datos

					if($vecParametros[0] != ''){
						$detalle = new DetalleRecibo();

						$detalle->historia 			= $whistoria;
						$detalle->ingreso 			= $wingreso;
						$detalle->consecutivoCargo 	= $consecutivo;
						$detalle->articulo 			= $vecParametros[0];
						$detalle->lineaCargo		= $vecParametros[1];
						$detalle->causaFaltante 	= $vecParametros[2];
						$detalle->fechaRecibo 		= $vecParametros[3];
						$detalle->horaRecibo 		= $vecParametros[4];

						actualizarCausaFaltante($detalle);
					}
				}

				//M3n54j3 de confirmacion
				echo '<div align=center><span class="subtituloPagina2" align=center>';
				echo "Se han actualizado las causas de faltante correctamente.  Historia $whistoria - $wingreso";
				echo "</span>";
				echo "<br>";
				echo "<br>";
			} else {
				mensajeEmergente("Verifique los parametros de consulta.");
			}

			echo "<input type='button' value='Regresar' onClick='javascript:inicioRecibido(\"$servicio\",\"$wfechai\",\"$wfechaf\",\"$wscausa\");'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></div>";
			break;
		default:
			//echo consultarGruposPorCcoUsuario($wuser); OJO TODO: *************
			$centroCostosUsuario = "";

			$gruposConsulta = "(";
			$gruposConsulta .= "'".str_replace(",","','",consultarGruposPorCcoUsuario("01156",$centroCostosUsuario))."',";
			$gruposConsulta .= "'')";

			echo "<input type=hidden id='wsservicio' name='wsservicio' value='$centroCostosUsuario'>";

			echo '<span class="subtituloPagina2">';
			echo 'Recibos pendientes del lactario';
			echo "</span>";
			echo "<br>";
			echo "<br>";

			echo consultarListadoPendienteReciboLactario($centroCostosUsuario,$gruposConsulta);
			break;
	}
}
?>