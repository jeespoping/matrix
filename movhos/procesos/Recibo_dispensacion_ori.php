<html>
<head>
<title>MATRIX - [RECIBO CARRO DE MEDICACION]</title>

<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript">
	/******************************************************************************************************************************
	 *Redirecciona a la pagina inicial
	 ******************************************************************************************************************************/
	 function inicio(servicio){
 		document.location.href='Recibo_dispensacion.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&servicio='+servicio;
	 }
	/******************************************************************************************************************************
	 *Redirecciona a la pagina inicial
	 ******************************************************************************************************************************/
	 function inicioRecibido(servicio,wfechai,wfechaf,wscausa){
	 	document.location.href='Recibo_dispensacion.php?waccion=c&wemp_pmla='+document.forms.forma.wemp_pmla.value+'&servicio='+servicio+'&wfechai='+wfechai+'&wfechaf='+wfechaf+'&wscausa='+wscausa;	 		
	 }
	/*****************************************************************************************************************************
	 *Consulta las historias y habitaciones de acuerdo a un servicio
	 ******************************************************************************************************************************/
	function consultarHabitaciones(){		
		var servicio = document.getElementById('wsservicio').value;
		if(servicio && servicio != '' ){
			var contenedor = document.getElementById('cntHabitacion');
			var parametros = ""; 
						
			parametros = "consultaAjaxInclude=2&basedatos="+document.forms.forma.wbasedato.value+"&servicio=" + servicio; 
			
			try{
				$.blockUI({ message: $('#msjEspere') });
				ajax=nuevoAjax();
				
				ajax.open("POST", "../../../include/Movhos/movhos.inc.php",true);
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
	function consultarRecibos(){		
//		debugger;
		var servicio = document.getElementById('wsservicio').value;
		var causa = document.getElementById('wscausa').value;
		var fInicial = document.getElementById('wfechai').value;
		var fFinal = document.getElementById('wfechaf').value;
		
		if(servicio && servicio != '' ){
			var contenedor = document.getElementById('cntHabitacion');
			var parametros = ""; 
						
			parametros = "consultaAjaxInclude=3&basedatos="+document.forms.forma.wbasedato.value+"&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&servicio="+servicio+"&fechaInicial="+fInicial+"&fechaFinal="+fFinal+"&causa="+causa; 
				
			try{
				$.blockUI({ message: $('#msjEspere') });
				ajax=nuevoAjax();
				
				ajax.open("POST", "../../../include/Movhos/movhos.inc.php",true);
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
	  * Captura de onEnter con llamada a funcion parametrizada con validacion de numeros entera
	  ******************************************************************************************************************************/
	 function teclaEnterEntero(e,accion){
	 	var respuesta = validarEntradaEntera(e);
	 	var tecla = (document.all) ? e.keyCode : e.which;
	 	
	 	if(respuesta && tecla==13){
	 		eval(accion);	
//	 		this[accion]();
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
//			alert('Recibo_dispensacion.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a'+'&whistoria='+historia+'&wservicio='+servicio);
			document.location.href = 'Recibo_dispensacion.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia+'&wservicio='+servicio+'&consecutivo='+consecutivo+'&wfechai='+wfechai+'&wfechaf='+wfechaf+'&wscausa='+wscausa+'&wsservicio='+wsservicio;
		} else {
			alert('No ha seleccionado el servicio');
		}
	}

	function consultarDetalle(historia,ingreso,nomPaciente,habitacion,consecutivo,linea,servicio,wfechai,wfechaf,wscausa){
		$.blockUI({ message: $('#msjEspere') });
		if(historia != '' && ingreso != '') {
			var href = 'Recibo_dispensacion.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia+'&wingreso='+ingreso+'&nombrePaciente='+nomPaciente+'&habitacion='+habitacion+'&consecutivo='+consecutivo+'&linea='+linea+'&wservicio='+servicio+'&wfechai='+wfechai+'&wfechaf='+wfechaf+'&wscausa='+wscausa;

			document.location.href = href;
		} else {
			alert('No ha seleccionado el servicio');
		}
	}

	function consultarDetalleRecibido(historia,ingreso,nomPaciente,consecutivo,servicio,habitacion,wfechai,wfechaf,wscausa,wfechar){
		var causaFaltante = document.forms.forma.wscausa.value;
		$.blockUI({ message: $('#msjEspere') });		
		if(historia != '' && ingreso != '') {
			var href = 'Recibo_dispensacion.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=d&whistoria='+historia+'&wingreso='+ingreso+'&nombrePaciente='+nomPaciente+'&consecutivo='+consecutivo+'&servicio='+servicio+'&causa='+causaFaltante+'&habitacion='+habitacion+'&wfechai='+wfechai+'&wfechaf='+wfechaf+'&wscausa='+wscausa+'&wfechar='+wfechar;
			document.location.href = href;
		} else {
			alert('No ha seleccionado el servicio');
		}
	}
	/******************************************************************************************************************************
	 *
	 ******************************************************************************************************************************/
	 function validarCantidad(id){
		 var maxRecibido = document.getElementById("wtmaxrecibido"+id);
		 var recibido = document.getElementById("wtrecibido"+id);
		 var pendiente = document.getElementById("wtpendiente"+id);
		 var incremento = document.getElementById("whincremento"+id);
		 
		 var valido = false;
		 
		 if(recibido && recibido.value && !isNaN(recibido.value) && maxRecibido && maxRecibido.value && !isNaN(maxRecibido.value)){
			 try{
				if(parseInt(recibido.value) < 0){
					recibido.value = 0;
					alert("El valor recibido debe ser mayor o igual a cero.  Verifique l�nea "+id);
					return;
				}

				if(parseInt(recibido.value) > parseInt(maxRecibido.value) || (parseInt(recibido.value) % incremento.value != 0)){
					recibido.value = 0;
					document.getElementById("tr"+id).className = "fila2";
		 			document.getElementById("tdRec"+id).className = "fila2";
		 			pendiente.value = parseInt(maxRecibido.value) - parseInt(recibido.value);
					alert("El valor recibido debe ser igual o inferior a la cantidad enviada y para este articulo debe ser en multiplos de "+incremento.value+".  Verifique l�nea "+id);
					return;
				}
		 		valido = true;		 
			 }catch(e){}
		 } 

		 if(!valido){
			 alert('El valor digitado no es v�lido, por favor verifique l�nea' +id);
			 recibido.value = 0;
 			 document.getElementById("tr"+id).className = "fila2";
 			 document.getElementById("tdRec"+id).className = "fila2";
		 } else {
			 if(recibido.value!=0){
				//Resaltar la fila
			 	document.getElementById("tr"+id).className = "fila2";
			 	document.getElementById("tdRec"+id).className = "fondoRojo";
			 } else {
				document.getElementById("tr"+id).className = "fila2";
	 			document.getElementById("tdRec"+id).className = "fila2";
			 }
		 }

		 if(recibido.value == maxRecibido.value){
			//Resaltar la fila en fondoAmarillo
			document.getElementById("tdRec"+id).className = "fondoVerde";
			document.getElementById("tr"+id).className = "fondoVerde";
		 }
		 //
		 pendiente.value = parseInt(maxRecibido.value) - parseInt(recibido.value);

		 //
	 }
	 
	 function validarSeleccionCausa(id){
		 var maxRecibido = document.getElementById("wtmaxrecibido"+id);
		 var recibido = document.getElementById("wtrecibido"+id);
		 var causa = document.getElementById("wscausa"+id);
	 }

	 /******************************************************************************************************************************
	 *
	 ******************************************************************************************************************************/
	 /*
	 function grabarRecibo(){
//		 debugger;
		 if(confirm("�Confirmar el recibo del carro de medicaci�n?")){
	 		var wemp_pmla = document.forms.forma.wemp_pmla.value;

	 		var historia = document.forms.forma.whistoria.value;
	 		var ingreso = document.forms.forma.wingreso.value;

		 	var arrCantidades = document.forms.forma.arrCantidades.value;
		 	var servicio = document.forms.forma.wservicio.value;
		 	
	 		document.getElementById("btnGrabar").disabled = true;
	 	
		 	//Recorro los campos con los valores de pedido	
		 	var cont1 = 1;
	 		var causa = "";
		 	var datos = "";
	 	
		 	while(document.getElementById("wtrecibido"+cont1)){
	 		
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
	 			datos += document.getElementById("whconsecutivocargo"+cont1).value+"|";	 			
	 			datos += document.getElementById("whcantidaddev"+cont1).value+"|";
	 			datos += document.getElementById("whcantidadsindev"+cont1).value+";";

	 			//Se debe obligar siempre a seleccionar una sin faltante.
	 			if(document.getElementById("wtmaxrecibido"+cont1).value != document.getElementById("wtrecibido"+cont1).value && document.getElementById("wscausa"+cont1).value == ''){
		 			alert("Debe seleccionar una causa de faltante para "+document.getElementById("whcodigo"+cont1).value+", linea "+cont1);
		 			document.getElementById("btnGrabar").disabled = false;
		 			return;
		 		}
	 		}
	 		cont1++;
	 	}

	 	if(datos != ''){
	 		var href ='Recibo_dispensacion.php?wemp_pmla='+wemp_pmla+'&waccion=b&whistoria='+historia+'&wingreso='+ingreso+'&wdatos='+datos+'&arrCantidades='+arrCantidades+'&servicio='+servicio;
//	 		alert(href);
	 		document.location.href = href;
	 	} else {
	 		alert("Nada para grabar.");		
	 	}
	 	document.getElementById("btnGrabar").disabled = false;
		}
	 }
*/
	 function grabarRecibo(){
//		 debugger;
		 if(confirm("�Confirmar el recibo del carro de medicaci�n?")){
	 		var wemp_pmla = document.forms.forma.wemp_pmla.value;

	 		var historia = document.forms.forma.whistoria.value;
	 		var ingreso = document.forms.forma.wingreso.value;

		 	var arrCantidades = document.forms.forma.arrCantidades.value;
		 	var wdatos = document.forms.forma.wdatos.value;
		 	
		 	var servicio = document.forms.forma.wservicio.value;
		 	
	 		document.getElementById("btnGrabar").disabled = true;
	 	
		 	//Recorro los campos con los valores de pedido	
		 	var cont1 = 1;
	 		var causa = "";
		 	var datos = "";
	 	
		 	while(document.getElementById("wtrecibido"+cont1)){
	 		
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
	 			datos += document.getElementById("whconsecutivocargo"+cont1).value+"|";	 			
	 			datos += document.getElementById("whcantidaddev"+cont1).value+"|";
	 			datos += document.getElementById("whcantidadsindev"+cont1).value+";";

	 			//Se debe obligar siempre a seleccionar una sin faltante.
	 			if(document.getElementById("wtmaxrecibido"+cont1).value != document.getElementById("wtrecibido"+cont1).value && document.getElementById("wscausa"+cont1).value == ''){
		 			alert("Debe seleccionar una causa de faltante para "+document.getElementById("whcodigo"+cont1).value+", linea "+cont1);
		 			document.getElementById("btnGrabar").disabled = false;
		 			return;
		 		}
	 		}
	 		cont1++;
	 	}

	 	if(datos != ''){
		 	document.forms.forma.wdatos.value = datos;
		 	document.forms.forma.waccion.value = 'b';
		 	
//	 		var href ='Recibo_dispensacion.php?wemp_pmla='+wemp_pmla+'&waccion=b&whistoria='+historia+'&wingreso='+ingreso+'&wdatos='+datos+'&arrCantidades='+arrCantidades+'&servicio='+servicio;
//	 		alert(href);
//	 		document.location.href = href;

		 	document.forms.forma.submit();
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
		 	var href ='Recibo_dispensacion.php?wemp_pmla='+wemp_pmla+'&waccion=e&whistoria='+historia+'&wingreso='+ingreso+'&consecutivo='+consecutivo+'&wdatos='+datos+'&servicio='+servicio;
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
	 	parametros = "consultaAjaxInclude=01&basedatos="+document.forms.forma.wbasedato.value+"&codigoBarras="+codigo; 
	 			
	 	try
		{
//	 		$.blockUI({ message: $('#msjEspere') });
	 		ajax=nuevoAjax();
	 		
	 		ajax.open("POST", "../../../include/Movhos/movhos.inc.php",true);
	 		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	 		ajax.send(parametros);
	 			
	 		ajax.onreadystatechange=function()
			{ 
	 			if (ajax.readyState==4 && ajax.status==200)
				{
					if(ajax.responseText != '')
					{
						codigo = ajax.responseText;
					}
					
					var existe = false;
					var descontado = 0;
					var cont1 = 1;
					var contArr = 0;
					var registros = new Array();

					//Consulta del articulo
					while(document.getElementById("whcodigo"+cont1)){
						if(codigo == document.getElementById("whcodigo"+cont1).value){
							//Existe = true si el medicamento existe en la tabla 000009
							existe = true;
							registros[contArr] = cont1;
							contArr = contArr + 1;
							//break;
						}
						cont1++;
					}		

					var pendiente;
					var maxRecibido;
					var recibido;
					var incremento;
					 
					if(existe)
					{
						for (i=0;i<contArr;i++)
						{ 
							cont1 = registros[i];

							pendiente = document.getElementById("wtpendiente" + cont1);
							maxRecibido = document.getElementById("wtmaxrecibido"+cont1);
							recibido = document.getElementById("wtrecibido"+cont1);
							incremento = document.getElementById("whincremento"+cont1);
						
							//Salto al focus del articulo
							if(pendiente.value>0 && descontado==0)
							{
								//document.getElementById("wtrecibido"+cont1).focus();
								//document.getElementById("wtrecibido"+cont1).select();
								
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
								descontado = 1;
							} 
						}
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
/*********************************************************
 * Recibo de carro de dispensacion
 *
 * Fecha: Marzo 16 de 2010
 * Autor: Msanchez
 * Version: 1.0
 * 
 * Cambios:
 * 03-May-10: (Msanchez)  En la consulta del detalle se verifica tambien los cargos realizados al servicio seleccionado
 * 26-May-10 (Msanchez).  Se modifica para que soporte la inclusion unicamente de los grupos de medicamentos que tenga asociados en la tabla 11 y diferenciacion del lactario ccolac.
 * 2011-01-11 (Mario Cadavid):  Modificacion del Query por fallas en los resultados de las consultas. Se dej� de consultar la tabla 000003 y 000020
 * en la consulta de recibos por fecha, consultando ya solo las tablas 000093 y 000018 para hacer m�s r�pida la consulta y dar los resultados correctos
 * Funciones afectadas: consultarPacientesConRecibidosPorFecha y consultarPacientesConRecibosPorFechaLactario
 * 2011-10-20 (Mario Cadavid):  En la funci�n "consultarListadoPendienteReciboPaciente" se modific� la consulta de pendientes de recibo de modo que
 * el campo "incremento" tome el valor de 1 si el articulo permite cantidad variable, campo Arecva en la tabla movhos_000008 esta en "on", 
 * esto porque no estaba dejando recibir cantidad 1 para un articulo cuya cantidad por defecto es 2, campo Arecde en la tabla movhos_000008, 
 * pero si el articulo permite cantidad variable debe dejar recibir cualquier cantidad independiente de la cantidad por defecto para el articulo,
 * en la consulta de pendiente de recibo lo que se adicion� fue " AND Arecva!='on'"
 * 2012-05-02 (Mario Cadavid):  En la funci�n consultarListadoPendienteReciboPaciente se modific� el query principal quitando los SELECT anidados
 * Se incluy� la variable $articulosExcluidos que permite comparar el NOT IN del query contra un Array y no contra un SELECT
 * En la funci�n consultarListadoPendienteReciboPaciente se quita la consulta de cantidad por defecto que estaba con un SELECT anidado dentro del Query 
 * principal y se hace un query por fuera que cosulta si el art�culo maneja cantidad por defecto y obtiene esta cantidad
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
	var $cantidadSinDevolucion = "";
	var $cantidadRecibida = "";
	var $presentacion = "";
	var $incremento = "";
	var $saldoAcumulado = "";
	var $cantidadDevuelta = "";
	
	var $fechaCargo = "";
	var $horaCargo = "";
}

class DetalleRecibo{
	var $historia = "";
	var $ingreso = "";
	var $consecutivoCargo = "";
	var $lineaCargo = "";
	var $articulo = "";
	var $cantidadDespachada = "";
	var $cantidadRecibida = "";
	var $cantidadDevuelta = "";
	var $cantidadSinDevolucion = "";
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
	var $campo5 = "";
	var $campo6 = "";
	var $campo7 = "";
	var $campo8 = "";
	var $campo9 = "";
	var $campo10 = "";
}
/***********************************
 * Funciones
 ***********************************/
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
 * 2010-04-21: Msanchez
 * 
 * Se debe considerar lo siguiente para la consulta de los articulos cargados:
 * 
 * -Los articulos que se muestran en el carro son todos aquellos que tengan el campo Fdedis en 'on'.
 * -El servicio que carga se encuentra en Fencco (movhos_000002) y el servicio al que se carga se encuentra en cada registro del detalle en Fdeser (movhos_000003)
 * -Cuando son articulos del servicio farmac�utico, las cantidades se reciben tal cual como se registran en la tabla (movhos_000003)
 * 
 * Ej. Fdenum  Fdeart  Fdeari  Fdecan  
 * 	   ------  ------  ------  ------
 *    1184865  H2AB05  H2AB05  4
 *    1184865  A12CA2  A12CA2  8
 *    
 *    Estos dos articulos se reciben independientemente en el carro.
 *    
 * -Cuando los articulos son de la central de mezclas se agrupa por Fdeari y por Fdenum, haciendo que cada uno de los componentes 
 * del articulo Fdeari (los Fdeart) sin importar la cantidad, compongan un articulo por numero de documento.
 * 
 * Ej. Fdenum  Fdeart  Fdeari  Fdecan  
 * 	   ------  ------  ------  ------
 *    1184859  J1CB18  DA0126  2
 *    1184859  E1AB11  DA0126  1
 *    
 *    Este es un tazocin de la central de mezclas en minibolsa, se agrupan los articulos E1AB11 y J1CB18 sin importar la cantidad 
 *    y este constituye UNA (1) cantidad, estos dos articulos se reciben en el carro como si fueran UNO solo.
 *     
 *******************************************************************************************************************************************************************/
function consultarListadoPendienteReciboPaciente($historia,$ingreso,$servicio,$consecutivo){
	global $conex;
	global $wbasedato;

	$tipo = "";
	
	//*******************************Consulta de los grupos de la tabla 11 que sean distintos al servicio seleccionado
	$tieneGruposExcluidos = false;
	$gruposExcluidos = "(";
	
	$q6 = "SELECT DISTINCT Ccogka FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccolac = 'on' AND Ccogka != '*';";
	$res6 = mysql_query($q6, $conex);
	
	while($rs6 = mysql_fetch_array($res6)){
		$tieneGruposExcluidos = true;
		if(strpos($rs6['Ccogka'],$gruposExcluidos) === false){
			$gruposExcluidos .= "'".str_replace(",","','",$rs6['Ccogka'])."',";
		}
	}
	$gruposExcluidos .= "'')";
	//********************************
	
	// 2012-05-02
	//*******************************Consulta de los art�culos de la tabla 26 excluidos
	$articulosExcluidos = "(";	
	
	$q7 = "SELECT DISTINCT Artcod FROM {$wbasedato}_000026 WHERE Artest='on' AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposExcluidos";
	$res7 = mysql_query($q7, $conex);
	
	while($rs7 = mysql_fetch_array($res7)){
		if(strpos($rs7['Artcod'],$articulosExcluidos) === false){
			$articulosExcluidos .= "'".str_replace(",","','",$rs7['Artcod'])."',";
		}
	}
	$articulosExcluidos .= "'')";
	//********************************
	
	$coleccion = array();
	
	//Medico  Fecha_data  Hora_data  Fdenum  Fdelin  Fdeart  Fdeari  Fdecan  Fdeubi  Fdeinf  Fdeinh  Fdeinu  Fdelot  Fdeest  Fdeser  Fdedis  Seguridad  id
	$q = "		SELECT
					{$wbasedato}_000003.id, Fdenum, {$wbasedato}_000002.Fecha_data, {$wbasedato}_000002.hora_data, Fdelin, UPPER(Fdeart) Fdeart, UPPER(Fdeari) Fdeari, (Fdecan-Fdecad) Fdecan, Fdecad, Fdecan despachado, Fencco
				FROM 
					{$wbasedato}_000002, {$wbasedato}_000003
				WHERE 
					Fennum IN $consecutivo
					AND Fennum = Fdenum
					AND Fdeart NOT IN $articulosExcluidos
					AND Fdeest = 'on'										
					AND Fdedis = 'on'
					AND (Fdecan-Fdecad) > 0
			ORDER BY Fdeart;";
		 // en la 3 campo fdeari, fdelot, si lote difte de vacio
	//echo $q."<br>";

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
			if(strtoupper($info['Fdeart']) == strtoupper($info['Fdeari'])){
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


			// 2012-05-02
			// Se cosulta si el art�culo maneja cantidad por defecto y se obtiene esta cantidad
			$qcde = "	SELECT Arecde 
						  FROM {$wbasedato}_000008
						 WHERE Areces = '".$info['Fdeart']."'    
						   AND Arecco = '".$info['Fencco']."'
						   AND Arecva!='on';";
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
			$obj->idRegistro		= $info['id'];
			$obj->incremento		= $incremento;
			$obj->fechaCargo		= $info['Fecha_data'];
			$obj->horaCargo			= $info['hora_data'];
			$obj->cantidadDevuelta 	= $info['Fdecad'];
			$obj->cantidadSinDevolucion	= $info['despachado'];
			
			//*************************Descripcion del articulo
			$q2 = "SELECT Artcom, Artuni FROM ".$wbasedato."_000026 WHERE Artcod = '".$articulo."'";
			$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$info2 = mysql_fetch_array($res2);
			$num2 = mysql_num_rows($res2);

			if($num2 == 0){
				$q2 = "SELECT Artcom, Artuni FROM cenpro_000002 WHERE Artcod = '".$articulo."'";
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

			//*************************Verificacion de que el articulo tenia ya un recibo previo
			$saldoAnterior = "";
			$q4="SELECT Reccad, Reccar, (Reccad-Reccar) saldo FROM ".$wbasedato."_000093 WHERE Rechis = '$historia' AND Recing = '$ingreso' AND Recnum = '{$info['Fdenum']}' AND Recart = '{$info['Fdeart']}' GROUP BY Reccad, Reccar ORDER BY Id DESC LIMIT 1;";
			//				echo $q4;
			$res4 = mysql_query($q4, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q4 - " . mysql_error());
			if (mysql_num_rows($res4) > 0){
				$info4 = mysql_fetch_array($res4);
				$saldoAnterior = $info4['saldo'];
			}
			
			if(!empty($saldoAnterior)){
				$obj->saldoAcumulado = $saldoAnterior;
			}
			$coleccion[] = $obj;
		}
	}
	return $coleccion;
}

function consultarListadoRecibidoPaciente($historia,$ingreso,$fechare,$servicio,$causa){
	global $conex;
	global $wbasedato;

	$coleccion = array();

	//Rechis  Recing  Recnum  Recart  Reclin  Reccad  Recure  Recfre  Rechre  Reccaf  Reccar
	$q = "SELECT
			Recnum,Reclin,Recart,Reccad,Reccar,Recure,Recure,Recfre,Rechre,Reccaf
		FROM 
		{$wbasedato}_000093
		WHERE 
			Rechis = '$historia'  
			AND Recing = '$ingreso'
			AND Recfre = '$fechare'
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
					$q2 = "SELECT Artcom, Artuni FROM cenpro_000002 WHERE Artcod = '".$info['Recart']."'";
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
//	var_dump($vecItems);
	
	foreach ($col as $articulo){
		$q = "INSERT INTO ".$wbasedato."_000093(Medico,Fecha_data,Hora_data,Rechis,Recing,Recnum,Recart,Reccad,Recure,Recfre,Rechre,Reccaf,Reccar,Reclin,Reccde,Seguridad) VALUES
			('movhos','".date("Y-m-d")."','".date("H:i:s")."','".$articulo->historia."','".$articulo->ingreso."','".$articulo->consecutivoCargo."','".$articulo->articulo."','$articulo->cantidadDespachada','$articulo->usuarioRecibe','$articulo->fechaRecibo','$articulo->horaRecibo','$articulo->causaFaltante','$articulo->cantidadRecibida','','$articulo->cantidadDevuelta','A-$articulo->usuarioRecibe')";
//		echo $q."<br>";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q - " . mysql_error());

		//Actualizacion de marca de recibido en la tabla 2 SOLO si la cantidad recibida es igual a la despachada o sea recibe la totalidad
		if($articulo->cantidadRecibida > 0){

			$recibidoTotal = $articulo->cantidadRecibida;
			
			//Itero sobre los articulos
			foreach($vecItems as $artId){
				$vecArticulo = explode("|",$artId);

				if(isset($vecArticulo[1]) && $vecArticulo[1] != '' && $vecArticulo[1] == $articulo->articulo && isset($vecArticulo[4]) && $vecArticulo[4] != '' && $vecArticulo[4] == $articulo->consecutivoCargo){

					if($vecArticulo[2] != "3"){
						if(($recibidoTotal-$vecArticulo[3]) >= 0 || $articulo->cantidadDespachada == $articulo->cantidadRecibida){
							if($recibidoTotal > 0){		//Actualizo
								$q2 = "UPDATE ".$wbasedato."_000003 SET Fdedis = 'off' WHERE id = '{$vecArticulo[0]}';";
//								echo $q2."<br>";
								$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q2 - ". mysql_error());
							} else {
								break;
							}
							$recibidoTotal -= $vecArticulo[3];
						} else {
							break;
						}
					} else {
						$q2 = "UPDATE ".$wbasedato."_000003 SET Fdedis = 'off' WHERE Fdenum = '$articulo->consecutivoCargo' AND Fdeari='$articulo->articulo';";
//						echo $q2."<br>";
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
/*****************
 * Inicio
 *****************/
$wactualiz = "  Mayo 2 de 2012 ";
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
encabezado("Carro de medicaci�n",$wactualiz,"clinica");

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
	
	//Forma
	echo "<form name='forma' action='Recibo_dispensacion.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
	echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br /> Por favor espere un momento ... <br /><br />";
	echo "</div>";

	//Estrategia de FC con par�metro waccion
	if(!isset($waccion)){
		$waccion = "";
	}

	//FC para hacer las acciones
	switch ($waccion){
		case 'a':		//Consulta de detalle por paciente con historia e ingreso
			if(isset($whistoria) && $whistoria != '' && isset($wingreso) && $wingreso != ''){
				$arrCantidades = "";
				
				$cantidadDespachada 	= 0;
				$cantidadDevuelta 		= 0;
				$cantidadSinDevolucion 	= 0;
				
				$contadorArticulos = 1;
				
				echo "<input type='HIDDEN' NAME='whistoria' value='".$whistoria."'/>";
				echo "<input type='HIDDEN' NAME='wingreso' value='".$wingreso."'/>";
				echo "<input type='HIDDEN' NAME='consecutivo' value='".$consecutivo."'/>";
				echo "<input type='HIDDEN' NAME='wservicio' value='".$wservicio."'/>";
				echo "<input type='HIDDEN' NAME='servicio' value='".$wservicio."'/>";

				//Expansi�n de los numeros de cargos realizados
				$vecConsecutivos = explode("|",$consecutivo);

				//Ingreso de fecha de consulta
				echo '<span class="subtituloPagina2">';
				echo "Detalle pendiente recibo para $whistoria - $wingreso. <b>$nombrePaciente</b>.  Habitaci�n: $habitacion";
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

				echo "<table align=center>";

				//Salto al articulo
				echo "<tr>";
				echo "<td class=fila1 colspan=4>Buscar articulo en esta lista</td>";
				echo "<td class=fila2 colspan=4><INPUT TYPE='text' id='wtarticulo' NAME='wtarticulo' value='' SIZE=10 onkeypress='return teclaEnter(event,"."\"validarArticulo();\");' class='textoNormal'></td>";
				echo "</tr>";

				echo "<tr><td>&nbsp;</td></tr>";
				
				echo "<tr class=encabezadoTabla align=center>";

				echo "<td>#</td>";
				echo "<td>Cargo</td>";
				echo "<td>Articulo</td>";
				echo "<td>Presentacion</td>";
				echo "<td>Cantidad <br>Despachada</td>";
				echo "<td>Cantidad <br>Recibida</td>";
				echo "<td>Cantidad <br>Pendiente</td>";
				echo "<td>Causa de faltante</td>";

				echo "</tr>";

				$consecutivoComas = "(";
				$consecutivoComas .= "'".str_replace("|","','",$consecutivo)."'";
				$consecutivoComas .= ")";
				
				$col = consultarListadoPendienteReciboPaciente($whistoria,$wingreso,$wservicio,$consecutivoComas);

				$colCausas = consultarCausasFaltantes();
				if(count($col) > 0){

					$clase = "";
					$codigo = "";
					$consecutivo = "";
					$arrCantidades = "";
						
					$arrArticulos = array();
						
					//sort($col);
						
					foreach ($col as $elemento){
						
						$cantidadDespachada += intval($elemento->cantidadDespachada);
						$cantidadDevuelta += intval($elemento->cantidadDevuelta);
						$cantidadSinDevolucion += intval($elemento->cantidadSinDevolucion);

						$arrCantidades .= "{$elemento->idRegistro}|{$elemento->codigoArticulo}|{$elemento->tipo}|{$elemento->cantidadDespachada}|{$elemento->numero}|{$elemento->cantidadDevuelta};";

						$registro = new RegistroArticulo();

						//Cuando llega otro codigo se inhabilita 
						if($codigo != '' && $codigo != $elemento->codigoArticulo.$elemento->numero){ 
							$cantidadDespachada = $elemento->cantidadDespachada;
							$cantidadDevuelta = $elemento->cantidadDevuelta;
							$cantidadSinDevolucion = $elemento->cantidadSinDevolucion;
						}
						
						$articulo = "$elemento->codigoArticulo - $elemento->nombreArticulo";
						$presentacion = "$elemento->presentacion";
						$registro->campo1 = "$elemento->codigoArticulo - $elemento->nombreArticulo";
						$registro->campo2 = $elemento->presentacion;

						if(isset($elemento->saldoAcumulado) && $elemento->saldoAcumulado != ''){
							//$registro->campo3 = intval($cantidadDespachada)-intval($elemento->saldoAcumulado);
							$registro->campo3 = intval($elemento->saldoAcumulado);
						} else {
							$registro->campo3 = intval($cantidadDespachada);
						}
						
						$registro->campo4 = $elemento->incremento;
						$registro->campo5 = $elemento->numero;
						$registro->campo6 = $elemento->fechaCargo;
						$registro->campo7 = $elemento->horaCargo;
						$registro->campo8 = $cantidadDevuelta;
						$registro->campo9 = $cantidadSinDevolucion;
						$registro->campo10= $elemento->tipo;

						$arrArticulos[$elemento->codigoArticulo.$elemento->numero] = $registro;

						$codigo = $elemento->codigoArticulo.$elemento->numero;
					}
//					var_dump($arrArticulos);
					echo "<input type='HIDDEN' NAME='arrCantidades' value='".$arrCantidades."'/>";
					echo "<input type='HIDDEN' NAME='waccion' value=''/>";
					echo "<input type='HIDDEN' NAME='wdatos' value=''/>";

					//Despliegue del proceso del agrupamiento
					if(count($arrArticulos)>0){

						foreach ($arrArticulos as $elem){

							echo "<tr class=fila2 id='tr$contadorArticulos'>";

							$vecArticulo = explode(" - ",$elem->campo1);
								
							echo "<td>$contadorArticulos";
							echo "<INPUT TYPE='hidden' id='whcodigo$contadorArticulos' NAME='whcodigo$contadorArticulos' value='".$vecArticulo[0]."'>";
							echo "<INPUT TYPE='hidden' id='whlinea$contadorArticulos' NAME='whlinea$contadorArticulos' value=''>";
							echo "<INPUT TYPE='hidden' id='whincremento$contadorArticulos' NAME='whincremento$contadorArticulos' value='$elem->campo4'>";
							echo "<INPUT TYPE='hidden' id='whconsecutivocargo$contadorArticulos' NAME='whconsecutivocargo$contadorArticulos' value='$elem->campo5'>";
							echo "<INPUT TYPE='hidden' id='whcantidaddev$contadorArticulos' NAME='whcantidaddev$contadorArticulos' value='$elem->campo8'>";
							echo "<INPUT TYPE='hidden' id='whcantidadsindev$contadorArticulos' NAME='whcantidadsindev$contadorArticulos' value='$elem->campo9'>";
							echo "</td>";
							echo "<td>$elem->campo6 - $elem->campo7 ($elem->campo5)</td>";
							echo "<td>$elem->campo1</td>";
							echo "<td>$elem->campo2</td>";
								
							//Uso exclusivo para nutriciones y dosis adaptadas
//							echo "$elem->campo10";
							if($elem->campo3 == 0 || $elem->campo10 == "3"){
								$elem->campo3 = 1;
							}
							echo "<td align=center><INPUT TYPE='text' id='wtmaxrecibido$contadorArticulos' NAME='wtmaxrecibido$contadorArticulos' value='$elem->campo3' SIZE=5 class='fila2' readonly></td>";
							echo "<td id='tdRec$contadorArticulos' align=center><INPUT TYPE='text' id='wtrecibido$contadorArticulos' NAME='wtrecibido$contadorArticulos' value='0' SIZE=5 maxlength=5 onBlur='javascript:validarCantidad($contadorArticulos);' onKeyPress='return validarEntradaEntera(event);' class='textoNormal'></td>";
							echo "<td align=center><INPUT TYPE='text' id='wtpendiente$contadorArticulos' NAME='wtpendiente$contadorArticulos' value='$elem->campo3' SIZE=5 class='fila2' readonly></td>";

							echo "<td>";
							echo "<select id='wscausa$contadorArticulos' onChange='javascript:validarSeleccionCausa($contadorArticulos);'>";
							echo "<option value=''>Sin faltante</option>";
							foreach ($colCausas as $elemento1){
								echo "<option value=$elemento1->codigo>$elemento1->descripcion</option>";
							}
							echo "</select>";
							echo "</td>";

							echo "</tr>";
							$contadorArticulos++;
						}
					}
				} else {
					mensajeEmergente("No hay pendientes de recibo para el servicio.");
				}
				echo "</table>";
				echo "<div align=center><br><input type='button' id='btnGrabar' value='Grabar recibo' onClick='javascript:grabarRecibo();'>&nbsp;|&nbsp;<input type='button' value='Regresar' onClick='javascript:inicio(\"$wservicio\");'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></div>";
			} else {
				mensajeEmergente("No se captur� historia e ingreso.");
			}
			break;
		case 'b':		//Grabacion del recibo del carro de dispensacion
			if(isset($whistoria) && $whistoria != '' && isset($wingreso) && $wingreso != '' && isset($wdatos) && $wdatos != '' && isset($arrCantidades) && $arrCantidades != ''){
				echo "<input type='HIDDEN' NAME='arrCantidades' value='".$arrCantidades."'/>";
				
				$vecArticulos = explode(";",$wdatos);  //Division por articulo

				$fechaRecibo 	= date("Y-m-d");
				$horaRecibo 	= date("H:i:s");

//				echo "<br>$wdatos<br>";
//				echo "<br>$arrCantidades<br>";
				
				//Iteracion sobre todos los articulos
				foreach ($vecArticulos as $articulo){
					$vecParametros = explode("|",$articulo);  //Division por datos

					if($vecParametros[0] != ''){
						$detalle = new DetalleRecibo();

						$detalle->historia 			= $whistoria;
						$detalle->ingreso 			= $wingreso;
						$detalle->articulo 			= $vecParametros[0];
						$detalle->cantidadDespachada= $vecParametros[1];
						$detalle->cantidadRecibida 	= $vecParametros[2];
						$detalle->lineaCargo		= $vecParametros[3];
						$detalle->causaFaltante 	= $vecParametros[4];
						$detalle->consecutivoCargo 	= $vecParametros[5];
						$detalle->cantidadDevuelta 	= $vecParametros[6];
						$detalle->cantidadSinDevolucion = $vecParametros[7];

						$detalle->fechaRecibo 		= $fechaRecibo;
						$detalle->horaRecibo 		= $horaRecibo;
						$detalle->usuarioRecibe		= $wuser;

						$col[] = $detalle;
					}
				}
//				var_dump($col);
				
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

				$col = consultarListadoRecibidoPaciente($whistoria,$wingreso,$wfechar,$servicio,$causa);

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
				mensajeEmergente("No se captur� historia e ingreso.");
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
			//Muestra la pantalla inicial
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Par�metros de consulta';
			echo "</span>";
			echo "<br>";
			echo "<br>";
				
			//Servicio
			$centrosCostos = centrosCostosRecibenCarro();
			echo "<tr><td class='fila1' width=170>Servicio</td>";
			echo "<td class='fila2' align='center' width=170>";

			if(isset($servicio) && !empty($servicio)){
				echo "<select id='wsservicio' NAME='wsservicio' onchange='javascript:consultarHabitaciones();' class='textoNormal' value='$servicio'>";
			} else {
				echo "<select id='wsservicio' NAME='wsservicio' onchange='javascript:consultarHabitaciones();' class='textoNormal'>";
			}
			echo "<option value=''>Seleccione</option>";
			
			foreach ($centrosCostos as $centroCostosHospitalario){
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
			
			if(isset($servicio) && !empty($servicio)){
				echo consultarPacientesConPendientePorRecibir($wbasedato,$servicio);
			}
			
			echo "</div>";
			echo "</tr>";

			echo "</table>";
						
			echo "<div align=center><br><input type=button value='Consultar' onclick='javascript:consultarHabitaciones();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></div	>";
			
			break;
	}
}
?>