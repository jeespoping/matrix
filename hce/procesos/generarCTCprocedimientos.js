//============================================================================
//								MODIFICACIONES
//============================================================================
// Febrero 21 de 2017	- Al cancelar un CTC ya no se borre el procedimiento asociado en la orden
// Enero 21 de 2016		- Se agregan modificaciones para guardar los procedimientos sin ctc desde ordenes y desde el reporte de impresion.
//============================================================================

/************************************************************************************************
 * Lista de campos obligatorios que se deben llenar antes de grabar el ctc
 ************************************************************************************************/
//Creo un objeto con los campos obligatorios, si esta vació no lo dejo grabar
camposObligatorios = {};

camposObligatorios[ 'fechaProcedimientoPrevio1' ] = {
		val : false, 
		msg: "",
		calendarZapatec: true
	};
	
camposObligatorios[ 'fechaProcedimientoPrevio2' ] = {
		val : false, 
		msg: "",
		calendarZapatec: true
	};

camposObligatorios[ 'nombreProcedimiento' ] = {
		val : true, 
		msg: "Nombre de procedimiento",
		calendarZapatec: false
	};

camposObligatorios[ 'cupsRegInvima' ] = {
		val: true,
		msg: "CUPS O REG. INVIMA",
		calendarZapatec: false
	}

camposObligatorios[ 'frecuenciaUso' ] = {
		val: true,
		msg: "FRECUENCIA DE USO",
		calendarZapatec: false
	}

camposObligatorios[ 'cantidadSolicitada' ] = {
		val: true,
		msg: "CANTIDAD SOLICITADA",
		calendarZapatec: false
	}

camposObligatorios[ 'diasTratamiento' ] = {
		val: true,
		msg: "DIAS DE TRATAMIENTO",
		calendarZapatec: false
	}

camposObligatorios[ 'justificacion' ] = {
		val: true,
		msg: "JUSTIFICACION PARA EL PROCEDIMIENTO NO POS",
		calendarZapatec: false
	}

camposObligatorios[ 'tipoAtencion' ] = {
		val: true,
		msg: "TIPO DE ATENCION",
		calendarZapatec: false
	}

camposObligatorios[ 'tipoServicio' ] = {
		val: true,
		msg: "TIPO DE SERVICIO",
		calendarZapatec: false
	}
	
camposObligatorios[ 'descripcionCasoClinico' ] = {
		val: true,
		msg: "DESCRIPCION DEL CASO CLINICO",
		calendarZapatec: false
	}
	
camposObligatorios[ 'propositoDeLoSolicitado' ] = {
		val: true,
		msg: "PROPOSITO DE LO SOLICITADO",
		calendarZapatec: false
	}
	
camposObligatorios[ 'opcionesPOS' ] = {
		val: true,
		msg: "EXISTEN OPCIONES POS",
		calendarZapatec: false
	}
	
// camposObligatorios[ 'razonesNoUsoPos' ] = {
		// val: true,
		// msg: "RAZONES PARA NO UTILIZARLAS",
		// calendarZapatec: false
	// }

camposObligatorios[ 'notaMedicaCTC' ] = {
		val: true,
		msg: "NOTA MEDICA CTC",
		calendarZapatec: false
	}	

/************************************************************************************************/

function cerrarModalCtc(){
	
	$.unblockUI(); 
	timer = setInterval('recargar()', 8000);
}

function grabarAjaxProcedimiento2( codExamen, nroOrden, item, idExamen, wemp_pmla,historia,ingreso,accion,medico){

	try{
	
		var respuestaAjax = "";
		
		var grabado = true;
	
		// var contenedorPaciente = document.getElementById( "dvInfoPacienteCTC" );
		
		//Primero, busco todos los campos del encabezado
		// var urlInfPaciente = cearUrlPorCampos( contenedorPaciente );
		var urlInfPaciente ="";
		var validaciones = "";
		
		if( document.getElementById( "hiProcsNoPos" ) ){		

			//Este campo tiene el codigo de cada medicamento No Pos separados por -
			var artsNoPos = document.getElementById( "hiProcsNoPos" ).value;
		
			var artsNoPos = artsNoPos.substr( 1 );
			
			var procs = artsNoPos.split( "," );
			
			for( var i = 0; i < procs.length; i++ ){
				
				var arts = procs[i].split( "-" );
				
				//Busco el div que contiene la información
				//el div esta formado por dv{codigoArticulo}Mostrar
				var contenedorInfoNoPos = document.getElementById( "dv" + arts[0] + "Mostrar-" + idExamen );
				
				if( contenedorInfoNoPos ){
					
					validaciones += validarCamposCTC( contenedorInfoNoPos );
					
					if( validaciones == "" ){
						//Lleno las variables faltantes para poder guardar bien los ctc
						$( "input[name=tipoOrden]", contenedorInfoNoPos ).val( codExamen );
						$( "input[name=nroOrden]", contenedorInfoNoPos ).val( nroOrden );
						$( "input[name=item]", contenedorInfoNoPos ).val( item );
						
						var urlDatosArt = cearUrlPorCampos( contenedorInfoNoPos );
						
						var parametros = urlInfPaciente+urlDatosArt+"&historia="+historia+"&ingreso="+ingreso+"&codigoProcedimiento="+arts[0]+"&accion="+accion+"&medico="+medico;
						
						//hago la grabacion por ajax del articulo
						rpAjax = consultasAjax( "POST", "generarCTCProcedimientos.php?wemp_pmla="+wemp_pmla+"&consultaAjax=10", parametros, false );
						
						if( rpAjax == "" ){
						
							contenedorInfoNoPos.style.display = "none";
							document.getElementById( "dv" + arts[0] ).style.display = "none";
							
							alert( "El ctc para el procedimiento " + arts[0] + " ha sido grabado" );
							timer = setInterval('recargar()', 8000);
							$.unblockUI(); 
						}
						// else
						// {
							// alert("Grabooooo");
						// }
						
					}
					else
					{
						alert( validaciones );
					
						validaciones = "";
						
						grabado = false;
					}
				
					
				}
			}
			
			
			// //Si respuesta es diferente a vacio muestro el mensaje ya que hay un error
			// if( respuestaAjax != "" ){
				// alert( "Error 8: " + respuestaAjax );
			// }
		}
	}
	catch(e){
		//alert( "Error 7: " + e );
	}
}

/******************************************
 * Agrega el calendario a un campo
 ******************************************/
function calendarioZapatec( campo, boton ){

	Zapatec.Calendar.setup(
		{
			weekNumbers:false,
			showsTime:false,
			timeFormat:'12',
			electric:false,
			inputField: campo,
			button: boton,
			ifFormat:'%Y-%m-%d',
			daFormat:'%Y/%m/%d'
		}
	);
}

/******************************************************************
 * Agrega un evento a un elemento
 ******************************************************************/
function addEvent(event, elem, func) {
	
	if (elem.addEventListener){  // W3C DOM
		elem.addEventListener(event,func,false);}
	else if (elem.attachEvent) { // IE DOM
		 var r = elem.attachEvent("on"+event, func);
		 return r;
	}
	else throw 'No es posible añadir evento';
}

function zIndexZapatec(){
	
	auxdivs = document.body.getElementsByTagName( "div" );
	auxdivs = document.body.childNodes[ document.body.childNodes.length-1 ];
	auxdivs.style.zIndex = 5000;
	
	if( navigator.appName !=  "Microsoft Internet Explorer"){
		var a1 = parseInt( auxdivs.style.top )+parseInt( document.body.scrollTop )*1+3;

		auxdivs.style.top = a1+"px";
		auxdivs.style.left = parseInt( parseInt( auxdivs.style.left ) + 3 )+"px";
		auxdivs.style.position = 'absolute';
	}
	else{
	}
}

/************************************************************************************************
 * Si hay un campo obligatorio y esta vacio, esta funcion devulve false, de lo contrario
 * devuelve true
 ************************************************************************************************/
function styleCamposObligatoriosCtcProcs( campo ){

	var val = true;

	try{
		if( camposObligatorios[ campo.name ] && camposObligatorios[ campo.name ].val == true ){
		
			//Pongo estylo a los campo obligatorios
			campo.style.backgroundColor = "#FFFFCC";
			//campo.style.fontWeight = "bold";
		}
		
		if( camposObligatorios[ campo.name ] && camposObligatorios[ campo.name ].calendarZapatec == true ){
			if( document.getElementById( 'btn' + campo.id ) ){
				addEvent( "click", document.getElementById( 'btn' + campo.id ), zIndexZapatec );
			}
			else{
				alert( "No se puede activar el calendario para el campo " + campo.name );
			}
		}
	}
	catch( e ){
		alert( "Error: " + e );
	}
	
	return val;
}

/********************************************************************************
 * Valida que los campos obligatorios no se encuentren vacios
 ********************************************************************************/
function stylerCamposCTCProcsObligatorios( contenedor ){
	
	try{
		var val = "";
		
		var tagBuscar = new Array( "INPUT", "TEXTAREA" );	//Array con los tags que se quieren buscar
		
		for( var j = 0; j < tagBuscar.length; j++ ){
		
			var elementos = contenedor.getElementsByTagName( tagBuscar[j] );
			
			if( elementos ){
				
				for( var i = 0; i < elementos.length; i++ ){
				
					styleCamposObligatoriosCtcProcs( elementos[ i ] );
				}
			}
		}
		
		return val.substr( 1 );	//le quito el & inicial
	}
	catch(e){
		alert( "Error: " + e );
	}
	
	return val;
}

/************************************************************************************************
 * Graba el ctc de procedimiento en la base de datos
 ************************************************************************************************/
function grabarAjaxProcedimiento( codExamen, nroOrden, item, idExamen, wemp_pmla,accion ){

	try{
	
		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;
		
		var respuestaAjax = "";
		
		var grabado = true;
	
		// var contenedorPaciente = document.getElementById( "dvInfoPacienteCTC" );
		
		//Primero, busco todos los campos del encabezado
		// var urlInfPaciente = cearUrlPorCampos( contenedorPaciente );
		var urlInfPaciente ="";
		var validaciones = "";
		
		if( document.getElementById( "hiProcsNoPos" ) ){		

			//Este campo tiene el codigo de cada medicamento No Pos separados por -
			var artsNoPos = document.getElementById( "hiProcsNoPos" ).value;
		
			var artsNoPos = artsNoPos.substr( 1 );
			
			var procs = artsNoPos.split( "," );
			
			for( var i = 0; i < procs.length; i++ ){
				
				var arts = procs[i].split( "-" );
				
				//Busco el div que contiene la información
				//el div esta formado por dv{codigoArticulo}Mostrar
				var contenedorInfoNoPos = document.getElementById( "dv" + arts[0] + "Mostrar-" + idExamen );
				
				if( contenedorInfoNoPos ){
				
					//Lleno las variables faltantes para poder guardar bien los ctc
					$( "input[name=tipoOrden]", contenedorInfoNoPos ).val( codExamen );
					$( "input[name=nroOrden]", contenedorInfoNoPos ).val( nroOrden );
					$( "input[name=item]", contenedorInfoNoPos ).val( item );
					
					var urlDatosArt = cearUrlPorCampos( contenedorInfoNoPos );
					
					// var parametros = urlInfPaciente+urlDatosArt+"&historia="+historia+"&ingreso="+ingreso+"&codigoProcedimiento="+arts[0];
					var parametros = urlInfPaciente+urlDatosArt+"&historia="+historia+"&ingreso="+ingreso+"&codigoProcedimiento="+arts[0]+"&accion="+accion;
					
					//hago la grabacion por ajax del articulo
					rpAjax = consultasAjax( "POST", "generarCTCProcedimientos.php?wemp_pmla="+wemp_pmla+"&consultaAjax=10", parametros, false );
					
					// if( rpAjax == "" ){
					
						// contenedorInfoNoPos.style.display = "none";
						// document.getElementById( "dv" + arts[0] ).style.display = "none";
						
						// //alert( "El ctc para el procedimiento " + arts[0] + " ha sido grabado" );
					// }
					
					//Si respuesta es diferente a vacio muestro el mensaje ya que hay un error
					if( rpAjax!= "" ){
						contenedorInfoNoPos.style.display = "none";
						document.getElementById( "dv" + arts[0] ).style.display = "none";
						
						//alert( "El ctc para el procedimiento " + arts[0] + " ha sido grabado" );
					}
					
					respuestaAjax += rpAjax;
				}
			}
			
			
			
			// //Si respuesta es diferente a vacio muestro el mensaje ya que hay un error
			// if( respuestaAjax != "" ){
				// alert( "Error 8: " + respuestaAjax );
			// }
		}
	}
	catch(e){
		//alert( "Error 7: " + e );
	}
}

/************************************************************************************************
 * Si hay un campo obligatorio y esta vacio, esta funcion devulve false, de lo contrario
 * devuelve true
 ************************************************************************************************/
function validarCampoObligatorio( campo ){

	var val = true;

	try{
		if( camposObligatorios[ campo.name ] && camposObligatorios[ campo.name ].val == true ){
		
			if( campo.value == "" ){
				val = false;
			}
		}
	}
	catch( e ){
		alert( "Error 6: " + e );
	}
	
	return val;
}

/****************************************************************************************
 * crear una url donde el nombre de la variable del campo y su valor es value del campo
 * &campoName = campoName.value
 ****************************************************************************************/
function cearUrlPorCampos( contenedor ){
	
	var url;
	
	try{
		var url = "";
		
		var tagBuscar = new Array( "INPUT", "TEXTAREA" );	//Array con los tags que se quieren buscar
		
		for( var j = 0; j < tagBuscar.length; j++ ){
		
			var elementos = contenedor.getElementsByTagName( tagBuscar[j] );
			
			if( elementos ){
				
				for( var i = 0; i < elementos.length; i++ ){
				
					if( elementos[i].name != '' ){
				
						switch( elementos[i].type.toLowerCase() ){
							
							case 'checkbox':
								if( elementos[i].checked == true ){
									url += "&"+elementos[i].name + "=on";
								}
								else{
									url += "&"+elementos[i].name + "=off";
								}
								break;
							
							case 'radio':
								if( elementos[i].checked == true ){
									url += "&"+elementos[i].name + "=" + elementos[i].value;
								}
								break;
							
							default: url += "&"+elementos[i].name + "=" + encodeURIComponent( elementos[i].value );
								break;
						}
					}
				}
			}
		}
		
		return url.substr( 1 );	//le quito el & inicial
	}
	catch(e){
		alert( "Error 5: " + e );
		return false;
	}
}

/************************************************************************************
 * graba todos los datos a la base de datos
 ************************************************************************************/

function grabarCtcProcedimiento( historia, ingreso, fechaKardex, wemp_pmla, idExamen,cadenaExamSinCTC,cadenaCTCExamGuardado ){
	
	try{
		var respuestaAjax = "";
		
		var grabado = true;
	
		// var contenedorPaciente = document.getElementById( "dvInfoPacienteCTC" );
		
		//Primero, busco todos los campos del encabezado
		// var urlInfPaciente = cearUrlPorCampos( contenedorPaciente );
		var urlInfPaciente ="";
		var validaciones = "";
		
		//Este campo tiene el codigo de cada medicamento No Pos separados por -
		var artsNoPos = document.getElementById( "hiArtsNoPos1" ).value;
		
		var arts = artsNoPos.split( "-" );
		
		for( var i = 0; i < arts.length; i++ ){
			
			//Busco el div que contiene la información
			//el div esta formado por dv{codigoArticulo}Mostrar
			var contenedorInfoNoPos = document.getElementById( "dv" + arts[i] + "Mostrar-" + idExamen );
			
			validaciones += validarCamposCTC( contenedorInfoNoPos );
			
			if( document.getElementById( "dv" + arts[i] )  ){
			
				if( validaciones == "" ){
				
					// var urlDatosArt = cearUrlPorCampos( contenedorInfoNoPos );
					
					// var parametros = urlInfPaciente+urlDatosArt+"&historia="+historia+"&ingreso="+ingreso+"&codigoProcedimiento="+arts[i];
					
					// //hago la grabacion por ajax del articulo
					// rpAjax = consultasAjax( "POST", "generarCTCProcedimientos.php?wemp_pmla=01&consultaAjax=10", parametros, false );
					
					// if( rpAjax == "" ){
					
						// contenedorInfoNoPos.style.display = "none";
						// document.getElementById( "dv" + arts[i] ).style.display = "none";
						
						// alert( "El ctc para el procedimiento " + arts[i] + " ha sido grabado" );
					// }
					
					// respuestaAjax += rpAjax;
				}
				else{
					alert( validaciones );
					
					validaciones = "";
					
					grabado = false;
				}
			}
		}
		
		
		//Si respuesta es diferente a vacio muestro el mensaje ya que hay un error
		if( respuestaAjax != "" ){
			alert( "Error: " + respuestaAjax );
		}
		else{
			// window.parent.cerrarModal();
			if( grabado ){
			
				try{
					document.getElementById('ctcProcedimientos' ).appendChild( document.getElementById( "dv" + arts[0] + "Mostrar-" + idExamen ) );


					//Quito el examen de la cadena de articulos sin ctc
					cadena = cadenaExamSinCTC;
					cadenaActual = '';
					var filasNoPos = cadena.split( ";" );

					if(filasNoPos.length-1 > 0)
					{
						for( var i = 0; i < filasNoPos.length; i ++ ){
							
							var arrNoPos = filasNoPos[i].split( "," );
							
							if(arrNoPos[1]==idExamen)
							{
								cadenaCTCExamGuardado = cadenaCTCExamGuardado+ arrNoPos[0]+","+arrNoPos[1]+","+arrNoPos[2]+","+arrNoPos[3]+","+arrNoPos[4]+";";
							}
							else
							{
								cadenaActual = cadenaActual+filasNoPos[i]+';';
							}
						}
						
						cadenaActual = cadenaActual.replace(";;",";");
						cadenaExamSinCTC = cadenaActual;
					}
					// cerrarVentanaCtc( historia, ingreso, fechaKardex, wemp_pmla );
					// $.growlUI('Formulario diligenciado', 'Debe firmar la orden!'); 

					cerrarVentanaCtc( historia, ingreso, fechaKardex, wemp_pmla,cadenaExamSinCTC,cadenaCTCExamGuardado );
					
					if(cadenaExamSinCTC == '')
					{
						$.growlUI('Formulario diligenciado', 'Debe firmar la orden!'); 
					}

					
				}
				catch(e){
					alert( "Error 1: " + e );
				}
			}
		}
	}
	catch(e){
		alert( "Error 2: " + e );
	}
}

/****************************************************************************************
 * Permite una selección única por fila para los campos radios que se encuentren en ella
 ****************************************************************************************/
function seleccionUnicaRadioPorFila( campoRadio, idCampoHidden ){

	try{
		var radiosFila = campoRadio.parentNode.parentNode.getElementsByTagName( "INPUT" );
		
		for( var j = 0; j < radiosFila.length; j++ ){
			
			if( radiosFila[j].type.toLowerCase() == "radio" && campoRadio != radiosFila[j] ){
				radiosFila[j].checked = false;
				
			}
			else if(  radiosFila[j].type.toLowerCase() == "hidden" && radiosFila[j].name == idCampoHidden  ){
				var campoHidden = radiosFila[j];
			}
		}
		
		campoRadio.checked = true;
		
		campoHidden.value = campoRadio.value;
	}
	catch(e){
		alert( "Error 4: " + e );
	}
}

/********************************************************************************
 * Valida que los campos obligatorios no se encuentren vacios
 ********************************************************************************/
function validarCamposCTC( contenedor ){
	
	try{
		var val = "";
		
		var tagBuscar = new Array( "INPUT", "TEXTAREA" );	//Array con los tags que se quieren buscar
		
		for( var j = 0; j < tagBuscar.length; j++ ){
		
			var elementos = contenedor.getElementsByTagName( tagBuscar[j] );
			
			if( elementos ){
				
				for( var i = 0; i < elementos.length; i++ ){
				
					var validar = validarCampoObligatorio( elementos[ i ] );
				
					if( !validar ){
						val += "\nEl campo " + camposObligatorios[ elementos[ i ].name ].msg + " es obligatorio";
					}
				}
			}
		}
		
		return val.substr( 1 );	//le quito el & inicial
	}
	catch(e){
		alert( "Error 3: " + e );
	}
	
	return val;
}

/******************************************************************************************************
 * Al cerrar la orden llama a la generación de formatos de impresion para medicamentos de control
 ******************************************************************************************************/

function cerrarVentanaCtc( historia, ingreso, fechaKardex, wemp_pmla,cadenaExamSinCTC,cadenaCTCExamGuardado ){
	
	$.unblockUI(); 
	
	if(cadenaExamSinCTC != '') 
	{ 
		//Quitar articulo de la cadena
		abrirCTCMultipleParaExamenesGrabados(cadenaExamSinCTC,cadenaCTCExamGuardado); 
	}
	else 
	{ 
		setTimeout( "abrirCTCMultiple()", 500 );
	}

	//Esta función es de HCE.js
	// activarModalIframe("","nombreIframe","../../movhos/procesos/impresionMedicamentosControl.php?wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fechaKardex+"&consultaAjax=10","-1","0" );
}

function eliminarProcedimientoNoPos( historia, ingreso, fechaKardex, wemp_pmla, idExamen ){

	//Toca consultar la tabla a la cual pertenece el medicamento
	//Si el medicamento pertenece a la tabla examPendientesImp es de alta
	//en caso contrario no
	
	var prefijoAlta = '';
	
	var codExamen = document.getElementById( 'hexcco'+idExamen );
	
	if( !codExamen ){
		prefijoAlta = "Imp";
	}
	
	// var padre = codExamen.parentNode;
	
	// while( padre && padre.tagName.toLowerCase() != "table" ){
		
		// padre = padre.parentNode;
		
		// if( padre.tagName.toLowerCase() == "table" ){
		
			// if( padre.id && padre.id == "examPendientesImp" ){
				// prefijoAlta == "imp";
			// }
		// }
	// }
	
	if( wemp_pmla != 10 ){
		
		if( quitarExamen( idExamen, prefijoAlta, 'on', true ) ){
			cerrarVentanaCtc( historia, ingreso, fechaKardex, wemp_pmla );
		}
	}
	else{
		cerrarVentanaCtc( historia, ingreso, fechaKardex, wemp_pmla );
	}
}

function razonPorDefecto( cmp, texto )
{	
	if( cmp.checked ){
		var tbl = cmp.parentNode.parentNode.parentNode;
		
		$( "#razonesNoUsoPos", tbl ).val( texto );
	}
	
	seleccionUnicaRadioPorFila( cmp, 'opcionesPOS' );
}


















