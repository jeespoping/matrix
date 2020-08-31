/******************************************************************
 * Modificaciones:
 *
 * Febrero 21 de 2017	- Al cancelar un CTC ya no se borre el articulo asociado en la orden
 ******************************************************************/
/******************************************************************
 * AJAX
 ******************************************************************/

/******************************************************************
 * Realiza una llamada ajax a una pagina
 * 
 * met:		Medtodo Post o Get
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


/************************************************************************************************
 * Lista de campos obligatorios que se deben llenar antes de grabar el ctc
 ************************************************************************************************/
//Creo un objeto con los campos obligatorios, si esta vació no lo dejo grabar
obligatoriosCtcArts = {};

obligatoriosCtcArts[ 'tipoUsuario' ] = {
		val : true, 
		msg: "TIPO DE USUARIO" 
	};
	
obligatoriosCtcArts[ 'noAfiliacion' ] = {
		val : true, 
		msg: "No. DE AFILIACION" 
	};
	
obligatoriosCtcArts[ 'tipoSolicitud' ] = {
		val : true, 
		msg: "TIPO DE SOLICITUD" 
	};
	
	
obligatoriosCtcArts[ 'tipoAtencion' ] = {
		val : true, 
		msg: "TIPO DE ATENCION" 
	};

obligatoriosCtcArts[ 'principioActivoNoPos' ] = {
		val : true, 
		msg: "PRINCIPIO ACTIVO PARA EL MEDICAMENTO NO POS" 
	};
	
	
obligatoriosCtcArts[ 'posologiaNoPos' ] = {
		val : true, 
		msg: "POSOLOGIA PARA EL MEDICAEMNTO NO POS" 
	};
	
obligatoriosCtcArts[ 'presentacionNoPos' ] = {
		val : true, 
		msg: "PRESENTACION PARA EL MEDICAMENTO NO POS" 
	};
	
obligatoriosCtcArts[ 'ddNoPos' ] = {
		val : true, 
		msg: "DOSIS/DIAS PARA EL MEDICAMENTO NO POS" 
	};
	
obligatoriosCtcArts[ 'cantidadNoPos' ] = {
		val : true, 
		msg: "CANTIDAD PARA EL MEDICAMENTOS NO POS" 
	};
	
obligatoriosCtcArts[ 'tiempoTratamientoNoPos' ] = {
		val : true, 
		msg: "TIEMPO DE TRATAMIENTO PARA EL MEDICAMENTO NO POS",
		soloNumeros: true
	};
	
obligatoriosCtcArts[ 'categoriaFarmaceuticaNoPos' ] = {
		val : true, 
		msg: "CATEGORIA FARMACEUTICA PARA EL MEDICAMENTO NO POS" 
	};
	
// obligatoriosCtcArts[ 'registroInvimaNoPos' ] = {
		// val : true, 
		// msg: "REGISTRO INVIMA PARA EL MEDICAMENTO NO POS" 
	// };
	
obligatoriosCtcArts[ 'efectoTerapeuticoNoPos' ] = {
		val : true, 
		msg: "EFECTO TERAPEUTICO DESEADO AL TRATAMIENTO PARA EL MEDICAMENTO NO POS" 
	};
	
obligatoriosCtcArts[ 'tiempoRespuestaEsperado' ] = {
		val : true, 
		msg: "TIEMPO DE RESPUESTA ESPERADO PARA EL MEDICAMENTO NO POS" 
	};
	
obligatoriosCtcArts[ 'efectosSecundariosNoPos' ] = {
		val : true, 
		msg: "EFECTOS SECUNDARIOS Y POSIBLES RIESGOS AL TRATAMIENTO NO POS" 
	};
	
obligatoriosCtcArts[ 'descripcionCasoClinico' ] = {
		val : true, 
		msg: "DESCRIPCION DEL CASO CLINICO" 
	};	

obligatoriosCtcArts[ 'txtDxCIE10' ] = {
		val : true, 
		msg: "DEBE INGRESAR UN DIAGNOSTICO" 
	};

/************************************************************************************************/

function calcularTiempoTratamiento( canManejo ){
	return;	//...borrar
	var dosisDia = document.getElementById( "ddNoPos" ).value*1;
	
	if( dosisDia > 0 ){
		var tiempo = document.getElementById( "tiempoTratamientoNoPos" );
		tiempo.value = Math.ceil( document.getElementById( "cantidadNoPos" ).value*canManejo/dosisDia );
		
		// $( "b", tiempo.parentNode ).eq(0).html( tiempo.value );
	}
}

function detenerEventosCtc( evt ){

	if( !evt ){
		evt = event;
	}
	
	var key = evt.keyCode ? evt.keyCode : evt.which ;
			
	if (evt && evt.stopPropagation) 
		evt.stopPropagation();
	else if (evt) {
		evt.cancelBubble = true;
		evt.returnValue = false;
	}
	if (evt && evt.preventDefault) 
		evt.preventDefault(); //Para Mozilla Firefox

	evt.keyCode = 0;
	return false;
	
}

/*****************************************************************************************************************************
 *  Valida entrada entera
 ******************************************************************************************************************************/
function validarEntradaEntera2(e) { 
	
    tecla = (document.all) ? e.keyCode : e.which; 
    if (tecla==8 || tecla==13) 
    	return true; 
    
	//Solo digitos
    patron = /\d/;
    
    te = String.fromCharCode(tecla); 
    
    //return patron.test(te);
	if( !patron.test(te) ){
		detenerEventosCtc(e);
		return false;
	}
	else{
		return true
	}
}

function calcularCantidadArticulo( dosis, frecuencia, diasTto, canManejo, cmpCantidad ){
return;	//..... borrar
	/**********************************************************************
	 * Calculo la cantidad
	 **********************************************************************/
	var can = 0;
	var valValue = false;	//Solo inidica si se debe cambiar el valor 
	var valValue2 = true;	//Solo inidica si se debe cambiar el valor 
	
	for( var x in diasTto.objArts.inf ){
		
		if( diasTto.objArts.inf[x]['diasTto'] != '' ){
			var dTto = diasTto.objArts.inf[x]['diasTto'];
			valValue = Math.max( valValue, dTto );
		}
		else if( diasTto.objArts.inf[x]['dosisMaxima'] != '' ){
			var dTto = diasTto.objArts.inf[x]['dosisMaxima']*frecuencia[ diasTto.objArts.inf[x][ 'frecuencia' ] ]/24;
			valValue = dTto;
		}
		else{
			var dTto = diasTto.value;
			valValue = false;
			valValue2 = false;	//Solo inidica si se debe cambiar el valor 
		}
		
		can += Math.floor( dTto*24/frecuencia[ diasTto.objArts.inf[x][ 'frecuencia' ] ] )*diasTto.objArts.inf[x][ 'dosis' ];
	}
	
	if( valValue2 ){
		diasTto.value = dTto;
	}
	else{
		diasTto.value = Math.max( diasTto.value, dTto );
	}
	
	//La cantidad es tiempo por días de tratamiento
	//var can = Math.ceil( Math.floor( diasTto.value*24/frecuencia )*dosis/canManejo );
	
	//Busco el campo de cantidad
	//Primero busco el campo de id
	var dvCampo = document.getElementById( cmpCantidad );
	
	var inps = dvCampo.getElementsByTagName( "INPUT" );
	
	for( var i = 0; i < inps.length; i++ ){
	
		if( inps[i].name == 'cantidadNoPos' ){
			inps[i].value = Math.ceil( can/canManejo );
			break;
		}
	}
}

// /**
 // * Este es antes de los cambios de un solo ctc con HE
 // */
// function calcularCantidadArticulo( dosis, frecuencia, diasTto, canManejo, cmpCantidad ){

	// var can = Math.ceil( Math.floor( diasTto.value*24/frecuencia )*dosis/canManejo );
	
	// //Busco el campo de cantidad
	// //Primero busco el campo de id
	// var dvCampo = document.getElementById( cmpCantidad );
	
	// var inps = dvCampo.getElementsByTagName( "INPUT" );
	
	// for( var i = 0; i < inps.length; i++ ){
	
		// if( inps[i].name == 'cantidadNoPos' ){
			// inps[i].value = can;
			// break;
		// }
	// }
// }

/************************************************************************************************
 * Si hay un campo obligatorio y esta vacio, esta funcion devulve false, de lo contrario
 * devuelve true
 ************************************************************************************************/
function styleCamposObligatoriosCtcArts( campo ){

	var val = true;

	try{
		if( obligatoriosCtcArts[ campo.name ] && obligatoriosCtcArts[ campo.name ].val == true ){
		
			//Pongo estylo a los campo obligatorios
			campo.style.backgroundColor = "#FFFFCC";
			//campo.style.fontWeight = "bold";
		}
		
		// if( obligatoriosCtcArts[ campo.name ] && obligatoriosCtcArts[ campo.name ].soloNumeros && obligatoriosCtcArts[ campo.name ].soloNumeros == true ){
			// //if( document.getElementById( 'btn' + campo.id ) ){
				// addEvent( "keypress", campo, validarEntradaEntera2 );
			// //}
		// }
	}
	catch( e ){
		//alert( "Error: " + e );
	}
	
	return val;
}

/********************************************************************************
 * Valida que los campos obligatorios no se encuentren vacios
 ********************************************************************************/
function stylerCamposCTCArtsObligatorios( contenedor ){
	
	try{
		var val = "";
		
		var tagBuscar = new Array( "INPUT", "TEXTAREA" );	//Array con los tags que se quieren buscar
		
		for( var j = 0; j < tagBuscar.length; j++ ){
		
			var elementos = contenedor.getElementsByTagName( tagBuscar[j] );
			
			if( elementos ){
				
				for( var i = 0; i < elementos.length; i++ ){
				
					styleCamposObligatoriosCtcArts( elementos[ i ] );
				}
			}
		}
		
		return val.substr( 1 );	//le quito el & inicial
	}
	catch(e){
		//alert( "Error: " + e );
	}
	
	return val;
}

/************************************************************************************************
 * Graba el ctc de procedimiento en la base de datos
 ************************************************************************************************/
function grabarAjaxArticulos( codArt, protocolo, idx ){

	try{
	
		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;
		var wemp_pmla = document.forms.forma.wemp_pmla.value;
		var fecha = document.forms.forma.wfecha.value;
		
		var respuestaAjax = "";
		
		//Primero, busco todos los campos del encabezado
		var urlInfPaciente ="";
		var validaciones = "";
		
		
		if( document.getElementById( "hiArtsNoPos" ) ){

			//Este campo tiene el codigo de cada medicamento No Pos separados por -
			var artsNoPos = document.getElementById( "hiArtsNoPos" ).value;
		
			var artsNoPos = artsNoPos.substr( 1 );
			
			var arts = artsNoPos.split( "," );
		
			for( var i = 0; i < 1; i++ ){
			
				//var contenedorInfoNoPos = document.getElementById( "dv" + codArt + "-" +  protocolo + idx + "Mostrar" );
				
				//Busco el div que contiene toda la información
				var dvs = document.getElementById( "ctcArticulos" ).getElementsByTagName( "div" );
				
				for( var x = 0; x < dvs.length; x++ ){
					if( dvs[x].id.substr( 0, dvs[x].id.indexOf( "-" ) ).toLowerCase() == "dv"+codArt.toLowerCase() ){
						var contenedorInfoNoPos = dvs[x];
					}
				}
				
				if( contenedorInfoNoPos ){
				
					var fechaInicio = document.getElementById( 'wfinicio'+protocolo+idx ).value.split( " a las:" );
					
					var fin = fechaInicio[0];				//fecha de inicio
					var hin = fechaInicio[1]; 	
				
					//Lleno las variables faltantes para poder guardar bien los ctc
					// $( "input[name=tipoOrden]", contenedorInfoNoPos ).val( codExamen );
					// $( "input[name=nroOrden]", contenedorInfoNoPos ).val( nroOrden );
					// $( "input[name=item]", contenedorInfoNoPos ).val( item );
					
					
					/********************************************************************************
					 * Enero 20 de 2014
					 * A la posología le agrego la unidad
					 ********************************************************************************/
					// $( "[name=posologiaNoPos]",  contenedorInfoNoPos ).val( $( "[name=posologiaNoPos]",  contenedorInfoNoPos ).val()+" "+$( "[name=unPosologia]",  contenedorInfoNoPos ).val() )
					/********************************************************************************/
				
					var urlDatosArt = cearUrlPorCampos( contenedorInfoNoPos );
					
					
					
					/************************************************************************************
					 * Marzo 11 de 2013
					 ************************************************************************************/
					//Ahora creo una url con los parametros encontrados
					//Para ello recorro todo el objeto que requiero
					var paramsInfArts = "";
					
					var inCodArtsCTC = $( "[name=tiempoTratamientoNoPos]", contenedorInfoNoPos )[0].objArts;
					
					if( inCodArtsCTC.inf.length > 0 ){
						
						for( var i = 0; i < inCodArtsCTC.inf.length; i++ ){
							
							for( var x in inCodArtsCTC.inf[i] ){
								paramsInfArts += "&" + x + "[" + i + "]=" + inCodArtsCTC.inf[ i ][ x ];
							}
						}
					}
					/************************************************************************************/
								
					// var parametros = urlInfPaciente+urlDatosArt+"&historia="+historia+"&ingreso="+ingreso+"&articulo="+codArt+"&fin="+fin+"&hin="+hin+"&fechaKardex="+fecha;
					var parametros = urlInfPaciente+urlDatosArt+"&historia="+historia+"&ingreso="+ingreso+"&articulo="+codArt+"&fechaKardex="+fecha+paramsInfArts;
					
					//hago la grabacion por ajax del articulo
					rpAjax = consultasAjax( "POST", "generarCTCArticulosIDC.php?wemp_pmla="+wemp_pmla+"&consultaAjax=10", parametros, false );
					
					if( rpAjax == "" ){
							
						contenedorInfoNoPos.style.display = "none";
						//document.getElementById( "dv" + arts[i] ).style.display = "none";
						
						alert( "El ctc para el medicamento " + codArt + " ha sido grabado" );
						return;
					}
					
					//Si respuesta es diferente a vacio muestro el mensaje ya que hay un error
					if( respuestaAjax != "" ){
						alert( "Error: " + respuestaAjax );
					}
				}
			}
		}
	}
	catch(e){
		//alert( "Error: " + e );
	}
}



// function grabarAjaxArticulos( codArt, protocolo, idx ){

	// try{
	
		// var historia = document.forms.forma.whistoria.value;
		// var ingreso = document.forms.forma.wingreso.value;
		// var wemp_pmla = document.forms.forma.wemp_pmla.value;
		// var fecha = document.forms.forma.wfecha.value;
		
		// var respuestaAjax = "";
		
		// //Primero, busco todos los campos del encabezado
		// var urlInfPaciente ="";
		// var validaciones = "";
		
		
		// if( document.getElementById( "hiArtsNoPos" ) ){

			// //Este campo tiene el codigo de cada medicamento No Pos separados por -
			// var artsNoPos = document.getElementById( "hiArtsNoPos" ).value;
		
			// var artsNoPos = artsNoPos.substr( 1 );
			
			// var arts = artsNoPos.split( "," );
		
			// for( var i = 0; i < 1; i++ ){
			
				// var contenedorInfoNoPos = document.getElementById( "dv" + codArt + "-" +  protocolo + idx + "Mostrar" );
				
				// if( contenedorInfoNoPos ){
				
					// var fechaInicio = document.getElementById( 'wfinicio'+protocolo+idx ).value.split( " a las:" );
					
					// var fin = fechaInicio[0];				//fecha de inicio
					// var hin = fechaInicio[1]; 	
				
					// //Lleno las variables faltantes para poder guardar bien los ctc
					// // $( "input[name=tipoOrden]", contenedorInfoNoPos ).val( codExamen );
					// // $( "input[name=nroOrden]", contenedorInfoNoPos ).val( nroOrden );
					// // $( "input[name=item]", contenedorInfoNoPos ).val( item );
				
					// var urlDatosArt = cearUrlPorCampos( contenedorInfoNoPos );
								
					// var parametros = urlInfPaciente+urlDatosArt+"&historia="+historia+"&ingreso="+ingreso+"&articulo="+codArt+"&fin="+fin+"&hin="+hin+"&fechaKardex="+fecha;
					
					// //hago la grabacion por ajax del articulo
					// rpAjax = consultasAjax( "POST", "generarCTCArticulos.php?wemp_pmla="+wemp_pmla+"&consultaAjax=10", parametros, false );
					
					// if( rpAjax == "" ){
							
						// contenedorInfoNoPos.style.display = "none";
						// //document.getElementById( "dv" + arts[i] ).style.display = "none";
						
						// alert( "El ctc para el medicamento " + codArt + " ha sido grabado" );
						// return;
					// }
					
					// //Si respuesta es diferente a vacio muestro el mensaje ya que hay un error
					// if( respuestaAjax != "" ){
						// alert( "Error: " + respuestaAjax );
					// }
				// }
			// }
		// }
	// }
	// catch(e){
		// //alert( "Error: " + e );
	// }
// }


/************************************************************************************************
 * Si hay un campo obligatorio y esta vacio, esta funcion devulve false, de lo contrario
 * devuelve true
 ************************************************************************************************/
function validarCampoObligatorioCtcArts( campo ){

	var val = true;

	try{
		if( obligatoriosCtcArts[ campo.name ] && obligatoriosCtcArts[ campo.name ].val == true ){
		
			if( campo.value == "" ){
				val = false;
			}
		}
	}
	catch( e ){
		alert( "Error: " + e );
	}
	
	return val;
}


/**
 * crear una url donde el nombre de la variable del campo y su valor es value del campo
 * &campoName = campoName.value
 */
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
							
							default: url += "&"+elementos[i].name + "=" + elementos[i].value;
								break;
						}
					}
				}
			}
		}
		
		return url.substr( 1 );	//le quito el & inicial
	}
	catch(e){
		//alert( "Error: " + e );
		return false;
	}
}

/**********************************************************************
 * muestra u oculta un campo segun su id
 **********************************************************************/
function mostrarCtcArts( id ){
	
	var campo = document.getElementById( id );
	
	if( campo.style.display == 'none' ){
		campo.style.display = '';
	}
	else{
		campo.style.display = 'none';
	}
}

/************************************************************************************
 * graba todos los datos a la base de datos
 ************************************************************************************/
function grabarCtcArticulos( historia, ingreso, fechaKardex, wemp_pmla, protocolo, idx ){
	
	try{
	
		var articulo = "";
	
		var grabado = true;
		
		var respuestaAjax = "";
		var validaciones = "";
		
		//var contenedorPaciente = document.getElementById( "dvInfoPacienteCTC" );
		
		//Primero, busco todos los campos del encabezado
		//var urlInfPaciente = cearUrlPorCampos( contenedorPaciente );
		
		//Este campo tiene el codigo de cada medicamento No Pos separados por -
		var artsNoPos = document.getElementById( "hiArtsNoPos" ).value;
		
		var arts = artsNoPos.split( "," );
		
		for( var i = 1; i < arts.length; i++ ){
			
			//Busco el div que contiene la información
			//el div esta formado por dv{codigoArticulo}Mostrar
			
			var contenedorInfoNoPos = document.getElementById( "dv" + arts[i] + "Mostrar" );
			
			if( contenedorInfoNoPos ){
			
				//validaciones += validarCamposCTCArts( contenedorPaciente );
				validaciones += validarCamposCTCArts( contenedorInfoNoPos );
			
				if( document.getElementById( "dv" + arts[i] ) ){
					
					articulo = arts[i];
					
					if( true || document.getElementById( "dv" + arts[i] ).style.display != "none" ){
					
						if( validaciones == "" ){
						
							// var urlDatosArt = cearUrlPorCampos( contenedorInfoNoPos );
							
							// var parametros = urlInfPaciente+urlDatosArt+"&historia="+historia+"&ingreso="+ingreso+"&articulo="+arts[i];
							
							// //hago la grabacion por ajax del articulo
							// rpAjax = consultasAjax( "POST", "generarCTCArticulos.php?wemp_pmla="+wemp_pmla+"&consultaAjax=10", parametros, false );
							
							// if( rpAjax == "" ){
									
								// contenedorInfoNoPos.style.display = "none";
								// document.getElementById( "dv" + arts[i] ).style.display = "none";
								
								// alert( "El ctc para el medicamento " + arts[i] + " ha sido grabado" );
							// }
							
							// respuestaAjax += rpAjax;
						}
						else{
							alert( validaciones );
							
							validaciones = "";
							
							grabado = false;	//Indica si todos los medicamento fueron grabados
						}
					}
				}
			}
		}
		
		
		//Si respuesta es diferente a vacio muestro el mensaje ya que hay un error
		if( respuestaAjax != "" ){
			alert( "Error: " + respuestaAjax );
		}
		else{
			if( grabado ){
				document.getElementById( 'ctcArticulos' ).appendChild( document.getElementById( "dv" + articulo + "Mostrar" ) );
			
				cerrarVentanaCtcArts( historia, ingreso, fechaKardex, wemp_pmla, protocolo, idx );
			}
		}
	}
	catch(e){
		//alert( "Error: " + e );
	}
}

/************************************************************************************************************
 * Solo permite seleccionar un campo radio por fila y pone el valor del campo seleccionado en un campo hidden
 ************************************************************************************************************/
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
		//alert( "Error: " + e );
	}
}

function elminarArticuloArts( cmp, historia, ingreso, fechaKardex, wemp_pmla, protocolo, idx ){
	
	// var eliminar = quitarArticulo( idx, protocolo, document.getElementById( "wperiod" + protocolo + idx ), true );
	
	// $( "[name=tiempoTratamientoNoPos]", divAux )[0].objArts;
	
	cmp.objArts;
	
	var eliminar = false;
	
	if( confirm( "Si cierra el CTC los medicamentos no serán grabados en la orden médica." ) ){
		
		eliminar = true;
		
		// for( var i = 0; i < cmp.objArts.idxs.length; i++ ){
			// quitarArticulo( cmp.objArts.idxs[i].substr(1), cmp.objArts.idxs[i][0], document.getElementById( "wperiod" + cmp.objArts.idxs[i] ),'detKardexAddN', true );
		// }
	}
	
	if( eliminar ){
		cerrarVentanaCtcArts( historia, ingreso, fechaKardex, wemp_pmla, protocolo, idx );
	}
}

/************************************************************************
 * Cierra una ventana modal
 ************************************************************************/
function cerrarVentanaCtcArts( historia, ingreso, fechaKardex, wemp_pmla, protocolo, idx ){
	
	$.unblockUI(); 
	
	//Busco si el articulo tenía horario especial
	//Si tiene horario especial verifico si hay todavía rondas por grabar
	try{
		var hayMasHE = false;
	
		for( var i = 2; i <= 24; i += 2 ){
			
			if( document.getElementById( 'dosisRonda' + i ) && document.getElementById( 'dosisRonda' + i ).value != '' ){
			
				hayMasHE = true;
				
				break;
			}
		}
		if( false && !hayMasHE ){
			
			var tbNuevoBuscador = document.getElementById( "nuevoBuscador" );
				
			document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
			document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo
		
			//Quito los valores de los campos de busqueda y adición de medicamentos
			document.getElementById('wnombrefamilia').value = '';			
			document.getElementById('wpresentacionunidad').selectedIndex = -1;			
			document.getElementById('wpresentacionfamilia').selectedIndex = -1;			
			document.getElementById('wdosisfamilia').value = '';			
			document.getElementById('wperiodo').selectedIndex = -1;			
			document.getElementById('wadministracion').selectedIndex = -1;			
			document.getElementById('wcondicionsum').selectedIndex = -1;			
			document.getElementById('wdiastratamiento').value = '';			
			document.getElementById('wdosismaxima').value = '';			
			document.getElementById('wtxtobservasiones').value = '';			

			//Quito los valores de la regleta de grabacion
			document.getElementById('dosisRonda2').value = '';			
			document.getElementById('dosisRonda4').value = '';			
			document.getElementById('dosisRonda6').value = '';			
			document.getElementById('dosisRonda8').value = '';			
			document.getElementById('dosisRonda10').value = '';			
			document.getElementById('dosisRonda12').value = '';			
			document.getElementById('dosisRonda14').value = '';			
			document.getElementById('dosisRonda16').value = '';			
			document.getElementById('dosisRonda18').value = '';			
			document.getElementById('dosisRonda20').value = '';			
			document.getElementById('dosisRonda22').value = '';			
			document.getElementById('dosisRonda24').value = '';
			
			abrirCTCMultiple();			

		}
		else{
			// abrirCTCMultiple();
			// setTimeout( "eleccionMedicamento()", 500 );
			setTimeout( "abrirCTCMultiple()", 500 );
		}
	}
	catch(e){
		//alert( "Error: " + e );
	}
}

/********************************************************************************
 * Valida que los campos obligatorios no se encuentren vacios
 ********************************************************************************/
function validarCamposCTCArts( contenedor ){
	
	try{
		var val = "";
		
		var tagBuscar = new Array( "INPUT", "TEXTAREA" );	//Array con los tags que se quieren buscar
		
		for( var j = 0; j < tagBuscar.length; j++ ){
		
			var elementos = contenedor.getElementsByTagName( tagBuscar[j] );
			
			if( elementos ){
				
				for( var i = 0; i < elementos.length; i++ ){
				
					var validar = validarCampoObligatorioCtcArts( elementos[ i ] );
				
					if( !validar ){
						val += "\nEl campo " + obligatoriosCtcArts[ elementos[ i ].name ].msg + " es obligatorio";
					}
				}
			}
		}
		
		return val.substr( 1 );	//le quito el & inicial
	}
	catch(e){
		//alert( "Error: " + e );
	}
	
	return val;
}