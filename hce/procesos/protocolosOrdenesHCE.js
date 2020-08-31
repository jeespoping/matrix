/**********************************************************************************************************************************************************
 * Actualizaciones
 * Abril 26 de 2016 (Edwin MG): Se muestran mensajes correspondientes a configuración de HCE 
 * Septiembre 02 de 2015 (Edwin MG): Se corrige la carga de campos booleanos para los protocolos de HCE.
 * Junio 16 de 2014 (Jonatan Lopez): Se corrige la edicion de los protocolos, ya que se estaba perdiendo el codigo.
 * Mayo 22 de 2014 (Jonatan Lopez): Se agrega el campo de existe opcion pos si y existe opcion pos no, cuando se quiere registrar un CtcProcedimientos.
 * Septiembre 24 de 2013	(Edwin MG): Se corrige el tipo de protocolo al momento de actualizar los protocolos en el js.
 **********************************************************************************************************************************************************/

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
 * Agrega la funcion de buscada de medicamentos con autocomplete
 ************************************************************************************************/
function buscadorArticuloNoPos( fila ){

		$("[name=dat_Mreart]", fila ).autocomplete("protocolosOrdenesHCE.php?ajax=18&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&wcenmez="+wcenmez.value, {
			cacheLength:1,
			delay:0,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width:1000,
			autoFill:false,
			minChars: 2,
			formatItem: function(data, i, n, value) {
			
				//convierto el string en json
				eval( "var datos = "+data );
				
				return datos[0].label;	//Eso es lo que se muestra al usuario
				// return value;
			},
			formatResult: function(data, value){
	//    			debugger;
				eval( "var datos = "+data );
				return datos[0].value.cod;
			}
		}).result( function(event, item ){
			
			//La respuesta es un json
			//convierto el string en formato json
			eval( "var datos = "+item );
			
			setearDatosFilaArticulosReemplazar( datos, this );
		});
}

/************************************************************************************************
 * Agrega los datos necesarios a una fila
 *
 * Nota: datos es un objeto json, fila son los datos de la fila que se desean llenar
 ************************************************************************************************/
function setearDatosFilaArticulosReemplazar( datosFila, campo ){

	campo.value = datosFila[0].value.cod;
	campo._lastValue = campo.value;

	var fila = campo.parentNode.parentNode;

	//Busco los elementos que deseo llenar
	//Primero 
	fila.cells[2].innerHTML = datosFila[0].value.com;	//Nombre comercial
	
	//Agrego la unidad encontrada
	fila.cells[3].getElementsByTagName( "div" )[0].innerHTML = datosFila[0].value.uni;
	
	buscarPorName( "dat_Mrepre", fila )[0].value = datosFila[0].value.uni;
	buscarPorName( "dat_Mreest", fila )[0].value = "on";
}

/********************************************************************************
 * Busca un elemento con el nombre dato
 ********************************************************************************/
function buscarPorName( nombreBuscar, campo ){

	var elBuscado = new Array();

	if( !campo ){
		campo = document
	}
	
	//Busco todos los elementos
	var elementos = campo.getElementsByTagName( "*" );
	
	//Creo un array con todos los elementos que tengan el mismo nombre
	for( var i = 0; i < elementos.length; i++ ){
	
		if( elementos[i].name && elementos[i].name == nombreBuscar ){
			elBuscado[ elBuscado.length ] = elementos[i];
		}
	}
	
	return elBuscado;
}

/************************************************************************************************************
 * Carga los datos por defecto para el encabezado de un protocolo
 ************************************************************************************************************/
function cargarEncDefecto(indice){
	if( indice == undefined )
		indice=0;
	
	/********************************************************************************
	 * Cargando datos del encabezado del protocolo
	 ********************************************************************************/
	 if( indice == 0 ){
		//Primero cargo los datos de encabezado, siempre en vacío
		buscarPorName( "dat_Encnom", encProTipDes )[0].value = "";
		buscarPorName( "dat_Encnom", encProTipDes )[0].replaceValue = "";
		buscarPorName( "dat_Encnom", encProTipDes )[0]._lastValue = "";
	}
	
	setMed( [{value:{cod:"*", des:"Todos"}}], buscarPorName( "dat_Encmed", encProtocolo )[indice] );
	
	buscarPorName( "dat_Encesp", encProtocolo )[indice].value = "*";
	
	//Objeto Json con valores vacio
	setDx( [{value:{cod:"*", des: "Todos"}}], buscarPorName( "dat_Encdia", encProtocolo )[indice] );
	
	setDx( [{value:{cod:"*", des: "Todos"}}], buscarPorName( "dat_Enctra", encProtocolo )[indice] );
	buscarPorName( "dat_Enccco", encProtocolo )[indice].value = '*';
	
	buscarPorName( "dat_Encid", encProtocolo )[indice].value = '';
	
	setDx( [{value:{cod:"*", des: "Todos"}}], buscarPorName( "dat_Enccio", encProtocolo )[indice] );
}

/****************************************************************************************************************
 * Agrega una fila completa a una tabla con los campo necesarios
 * Este script siempre pone la fila nueva en primer lugar
 *
 * cmpTabla: Tabla a la que se le va a agregar la fila
 *
 * La primera fila siempre va a estar oculta, para facilidad de aumentar fila
 *
 * Nota: este script lo que hace es lo siguiente: Coge la primera fila y la replica (clona). luego la agrega
 *		 al final de la tabla. Por tal motivo se ocula la primera fila de todas.
 ****************************************************************************************************************/
function agregarFila( cmpTabla, indice ){
	
	if( indice == undefined || isNaN(indice) ) indice=1;
	
	try{
		var cloneFila = cmpTabla.rows[indice].cloneNode( true );
		cmpTabla.tBodies[0].appendChild( cloneFila );
		cloneFila.style.display = '';
		
		return cloneFila;
	}
	catch(e){
		alert(e);
		return false;
	}
}

/************************************************************************************
 * Borra un articulo
 ************************************************************************************/
function borrarArticulo( cmp ){

	if( cmp.parentNode.parentNode.parentNode.rows.length > 2 ){
		cmp.parentNode.parentNode.parentNode.removeChild( cmp.parentNode.parentNode );
	}
}

/************************************************************************************
 * Desactivar fila
 *
 * Nota: cmp es una fila
 ************************************************************************************/
function desactivarFila( cmp ){

	cmp.parentNode.parentNode.style.display = 'none';
	
	buscarPorName( "dat_Detest", cmp.parentNode.parentNode )[0].value = 'off';
}

/************************************************************************************
 * Eliminar fila
 *
 * Nota: cmp es una fila
 ************************************************************************************/
function eliminarFila( cmp ){
	if( confirm("¿Desea eliminar el registro?") ){
		cmp = jQuery(cmp);
		// cmp.parent().parent().remove();	
		cmp.parent().parent().css({display:'none'});
		$( "[name=dat_Detest]", cmp.parent().parent() ).val( 'off' )
	}
}

/********************************************************************************
 * Deja en un select los options permitidos para un medicamento
 *
 * Los cmpOptionsValue es un array. Son los options de acuerdo al value, que se deben dejar por
 * defecto.
 ********************************************************************************/
function dejarOptionsEnSelect( cmpSelect, cmpOptionsValue ){
	
	//valido que el campo si sea un select
	try{
		borrarOption = false;
	
		if( cmpSelect.tagName.toLowerCase() == "select" ){
			
			//Solo se puede hacer si cmpOptions tiene por lo menos un value a buscar
			if( cmpOptionsValue.length > 0 ){
			
				//busco los options que tengan el mismo value				
				var longitud = cmpSelect.options.length;
				
				for( var i = 0; i < cmpSelect.options.length; i++ ){
				
					borrarOption = true;
					
					//Recorro el array de valores por defecto par verificar que el options quede
					for( var j = 0; j < cmpOptionsValue.length; j++ ){
						
						//Si encuentra el valor, no se borra el option
						if( cmpOptionsValue[ j ].toLowerCase() == cmpSelect.options[ i ].value.toLowerCase() ){
							borrarOption = false;
							break;
						}
					}
					
					//Se borra el option si no se encuentra en la lista
					if( borrarOption ){
						cmpSelect.remove( i );
						i--;
					}
				}
			}
			
			//Dejo el campo seleccionado por defecto en vacio
			cmpSelect.selectedIndex = -1;
		}
	}
	catch(e){
		//alert( "Error: " + e);
	}
}

/************************************************************************************************
 * Esta funcion crea una url de la siguiente forma:
 *
 * campoName1=campoValue1&campoName1=campoValue1&campoName1=campoValue1...
 *
 * donde campoNameN es el nombre del campo y campoValueN es valor de un campo
 *
 * Cada campo se encuentra en contenedor, este puede ser un div, form, td, tr, table, etc...
 * En general un campo que puede contener entre sus etiquetas otros elementos html
 ************************************************************************************************/
function cearUrlPorCampos( contenedor ){
	
	var url;
	
	try{
		var url = "";
		
		var tagBuscar = new Array( "INPUT", "TEXTAREA", "SELECT" );	//Array con los tags que se quieren buscar
		
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
		alert( "Error: " + e );
		return false;
	}
}


/********************************************************************************************************
 * Para guardar los protocolos de ordenes se sigue los siguientes pasos
 *
 * 1. Grabar el encabezado
 * 2. Recorrer las tablas una a una para guardar el encabezado
 ********************************************************************************************************/
function guardarProtocoloOrdenes(){

	try{
	
		$.blockUI({ message: $('#msgGuardaProtocolo') });
		
		//Consulto el nombre del protocolo a crear
		var encPro = encProTipDes.getElementsByTagName( "input" );		
		var protocolo = "";		
		for( var i = 0; i < encPro.length; i++ ){
			if( encPro[i].name == 'dat_Encnom' ){
				protocolo = encPro[i].value;
				break;
			}
		}
	
		if( protocolo != "" ){
			
			
			//verifico que todos los campos de encabezado esten llenos
			var validar = true;
			
			//Borro todos los campos de las tablas de movimiento
			var arCmp =  new Array( "SELECT", "INPUT" );
			
			for( var i = 0; i < arCmp.length && validar; i++ ){
			
				var cmps = document.getElementById( "encProtocolo" ).getElementsByTagName( arCmp[i] );	
			console.log( cmps );
				for( var j = 0; j < cmps.length; j++ ){
					
					//Todos los campos a validar comienzan con dat_
					if( cmps[j].name.substr(0,4) == "dat_" && ( cmps[j].name != "dat_Encid" && cmps[j].name != "dat_Enctip" && cmps[j].name != "dat_Encest" && cmps[j].name != "dat_Encpro" && cmps[j].name != "dat_Enccodigo" && cmps[j].name != "dat_Encndi") ){
						
				
						//Si esta vacio un campo no busco los datos
						if( cmps[j].tagName.toLowerCase() == "select" || ( cmps[j].tagName.toLowerCase() == "input" && cmps[j].type.toLowerCase() != "checkbox" )){
							if( cmps[j].value == '' ){
							
							
								validar = false;
								break;
							}
						}
					}
				}
			}
			
			if( validar ){
			
				//No permito grabar si no hay por los menos un item a grabar(articulo, procedimiento o examen)
				//con codigo
				var tieneRegistros = false;
				
				//Borro todos los campos de las tablas de movimiento
				var arIdTablas =  new Array( "tabPesMedicamentos", "tabPesCtcMedicamentos", "tabPesProcedimientos", "tabPesCtcProcedimientos", "tabHCE" );
				
				for( var i = 0; i < arIdTablas.length; i++ ){
				
					var tb = document.getElementById( arIdTablas[ i ] );
					
					var inText = buscarPorName( "dat_Detcod", tb );
					
					for( var j = 1; j < inText.length; j++ ){
						if( inText[j].value != '' ){
							tieneRegistros = true;
						}
					}
					
					var inText = buscarPorName( "dat_Detid", tb );
					
					for( var j = 1; j < inText.length; j++ ){
						if( inText[j].value != '' ){
							tieneRegistros = true;
						}
					}
				}
				
				if( tieneRegistros ){
			
					//El nombre del encabezado puede cambiar
					var auxEncNom = buscarPorName( "dat_Encnom", encProTipDes )[0];
					
					var auxValue = auxEncNom.value;
					
					if( auxEncNom.replaceValue && auxEncNom.replaceValue != '' ){
						auxValue = auxEncNom.value;
						auxEncNom.value = auxEncNom.replaceValue;
					}
			
					/*var urlDatosArt = cearUrlPorCampos( encProtocolo );
					urlDatosArt+="&"+cearUrlPorCampos( encProTipDes );
								
					var parametros = urlDatosArt;*/
					
					auxEncNom.value = auxValue;
					
					/************************* GUARDANDO EN UN JSON TODOS LOS DATOS DEL ENCABEZADO *******************/
					var valoresFilaEnc = new Array(); //En cada posicion contiene las claves POR FILA		
					
					var objTipDes = new Object();					
					$("#encProTipDes").each(function(){
						if( $(this).find(":input").length > 0 ){
							$(this).find(":input").each(function(){
								if( $(this).attr('name') != undefined ){ //Si no tiene "name" es porque no se desea guardar
									var clave = $(this).attr('name');	
									//Esto es para que guarde el código del articulo o el examen, 
									//segun corresponda cuando es un CTC
									if( this.name == "dat_Encnom" && $( "#dvEditorProtocolos [name=slTipPro]" ).val().substr(0,3).toLowerCase() == "ctc"  ){
										var valorx  = this.replaceValue;
									}
									else{
										var valorx  = $(this).val();
									}									
									// var valorx  = $(this).val();
									if( isEmpty(valorx) == false ){
										valorx = valorx.replace(/"/gi, "&quot;");
									}			
									objTipDes[clave] = valorx;										
								}
							});
						}	
					});
					
					objTipDes['dat_Enctip'] = $("input[name=dat_Enctip]").val();
					
					//AQUI SE CREA UNA URL PARA MANDAR AL SERVER, QUE CONTIENE TODOS LOS ENCABEZADOS QUE SE VAN A GUARDAR
					$("#tabla_editor .filaEnc").each(function(){
						var arr_fila = new Object();
						$("> td", $(this)).each(function(){ //Los td hijos (no nietos, tds internos) de cada fila
							if( $(this).find(":input").length > 0 ){								
								$(this).find(":input").each(function(){
									if( $(this).attr('name') != undefined ){ //Si no tiene "name" es porque no se desea guardar
										var clave = $(this).attr('name');
										if( $(this).is(':checkbox') ){
											arr_fila[clave] = "off";
											if( $(this).is(':checked') ){
												arr_fila[clave] = "on";
											}
										}else{
											var valorx  = $(this).val();
											if( isEmpty(valorx) == false ){
												valorx = valorx.replace(/"/gi, "&quot;");
											}	
											arr_fila[clave] = valorx;
										}
									}
								});
							}
						});
						jQuery.extend(arr_fila, objTipDes); //Merge en arr_fila con objTipDes, para agregar el tipo y la descripcion en cada fila
						valoresFilaEnc.push(arr_fila);
					});
					/************************* FIN ----- GUARDANDO EN UN JSON TODOS LOS DATOS DEL ENCABEZADO *******************/
					$.unblockUI();
					
					/************************* GUARDANDO EN UN JSON TODOS LOS DATOS DEL DETALLE *******************/
					var valoresFilaDet = new Array(); //En cada posicion contiene las claves POR FILA
					
					//Tablas cuyo id comience por "tab"
					$("#programasGenerales table[id^=tab]").each(function(){
						$(this).find("> tbody > tr").each(function(){
							var arr_fila = new Object();
							var cantidad_de_tds = $(this).find("> td").length;							
							cantidad_de_tds--;
							$(this).find("> td").each(function(index){ //Los td hijos (no nietos, tds internos) de cada fila
								if( $(this).find(":input").length > 0 ){
									$(this).find(":input").each(function(){
										if( $(this).attr('name') != undefined ){ //Si no tiene "name" es porque no se desea guardar
											var clave = $(this).attr('name');
											//Si es la ultima columna de la fila y es un protocolo de HCE,
											//El name no es necesariamente la clave, si hay radios, la clave es la clase
											if(  $("#encProTipDes [name=slTipPro]").val() == "prHCE" && index == cantidad_de_tds && $(this).is(':radio')){
												clave = $(this).attr('class');												
											}
											if( $(this).is(':checkbox') ){
												arr_fila[clave] = "off";
												if( $(this).is(':checked') ){
													arr_fila[clave] = "on";
												}
											}else if($(this).is(':radio')){
												if( $(this).is(':checked') )
													arr_fila[clave] = $(this).val();										
											}else{
												var valorx  = $(this).val();
												if( isEmpty(valorx) == false ){
													valorx = valorx.replace(/"/gi, "&quot;");
												}			
												arr_fila[clave] = valorx;
											}
										}
									});
								}
							});							
							valoresFilaDet.push(arr_fila);
						});	
						//console.log("fin tabla!");
					});
					
					
					//return false;
					/************************* FIN ------ GUARDANDO EN UN JSON TODOS LOS DATOS DEL DETALLE *******************/
					
					var datosJson = Object();
					datosJson.encabezados = valoresFilaEnc;
					datosJson.detalle = valoresFilaDet;
					
					datosJson = $.toJSON( datosJson );
										
					var nombreProtocolo = $("#encProTipDes [name=dat_Encnom]").val();
					var tipoProtocolo = $("input[name=dat_Enctip]").val();
					
					$.post('protocolosOrdenesHCE.php', {  wnombre: nombreProtocolo, 
															wtipo: tipoProtocolo, 
														wemp_pmla: wemp_pmla.value, 
														wbasedato: wbasedato.value,
															 whce: whce.value, 
														  wcenmez: wcenmez.value, 
														     ajax: "1",
														   wdatos: datosJson,
														   
												     consultaAjax: ''
													 
													 } ,
					function(data) {
						
						$.unblockUI();
						
						mostrarMsg = false;
						if(data){
							
							$( ".filaEnc" ).removeClass( "fondorojo" );
						
							for( var i = 0; i < data.length; i++ ){
								if( data[i] == -1 ){
									$( ".filaEnc" ).eq( i ).addClass( "fondorojo" );
									mostrarMsg = true;
								}
								else{
									if( $( ".filaEnc [name=dat_Encid]" ).eq( i ).val( data[i] ) == "" ){
										$( ".filaEnc [name=dat_Encid]" ).eq( i ).val( data[i] );
									}
								}
							}
							
							if( mostrarMsg ){
								alert( "Los encabezado en rojo ya existen" );
							}
							else{
								var input = document.createElement("input");
								input.type = "text";
								input.value= "%";
								alCambiarSearch( input );
								nuevoPrgOrdenes();
								moBuscador();
							}
						}
						else{
							var input = document.createElement("input");
							input.type = "text";
							input.value= "%";
							alCambiarSearch( input );
							nuevoPrgOrdenes();
							moBuscador();
						}
						
						
						// moBuscador();
						
					}, "json" );					
				}
				else{
					alert( "Debe agregar por lo menos un articulo o procedimiento para grabar" );
					$.unblockUI();
				}
			}
			else{
				alert( "Debe llenar los datos de encabezado" );
				$.unblockUI();
			}
		}
		else{
			alert( "Debe ingresar un nombre de protocolo" );
			$.unblockUI();
		}
	}
	catch(e){
		alert( "Error: " + e );
	}
}

function guardarDetalleProtocolos( ajax ){

	if ( ajax.readyState==4 && ajax.status==200 ){
	
		var auxEncId = buscarPorName( "dat_Encid", document.getElementById( "encProtocolo" ) )[0]
		
		if( ( ajax.responseText == '' && auxEncId.value > 0 ) || ( ajax.responseText > 0  && auxEncId.value == '' ) ){
		
			if( auxEncId.value == '' ){
				buscarPorName( "dat_Encid", document.getElementById( "encProtocolo" ) )[0].value = ajax.responseText;
			}
		
			//Consulto el nombre del protocolo a crear
			var encPro = encProTipDes.getElementsByTagName( "input" );			
			var protocolo = "";			
			for( var i = 0; i < encPro.length; i++ ){
				if( encPro[i].name == 'dat_Encnom' ){
					if( encPro[i].replaceValue && encPro[i].replaceValue != '' ){
						protocolo = encPro[i].replaceValue;
					}
					else{
						protocolo = encPro[i].value;
					}					
					break;
				}
			}
			
			if( protocolo != "" ){
				
				//Busco las tablas a guardar
				var tablas = document.getElementsByTagName( 'table' );
				
				//Cada tabla que contenga informacion que se desea guardar su id comienza con tab
				//Busco dichas tablas y cuento cuantas filas hay por guardar
				//No se cuenta la primera fila por que esa nunca contiene informacion
				//La primera fila de las tablas es una ayuda para crear facilmente la fila siguiente.
				
				//Calculando total de registros a guardar
				totalRegistros = 0;
				
				for( var i = 0; i < tablas.length; i++ ){
					
					if( tablas[i].id.substr(0,3) == 'tab' ){
						totalRegistros += totalRegistros = tablas[i].rows.length-2;
					}
				}
				
				//Empiezo a guardar cada registro
				for( var i = 0; i < tablas.length; i++ ){
					
					//Solo en las tablas que comienzan con tab
					if( tablas[i].id.substr(0,3) == 'tab' ){
					
						//Y solo si la tabla tiene mas de 2 filas
						if( tablas[i].rows.length > 2 ){
							
							for( var j = 2; j < tablas[i].rows.length; j++ ){
							
								//Recorro las filas que se van a grabar
								var urlDatosArt = cearUrlPorCampos( tablas[i].rows[j] );
								
								if( tablas[i].id != 'tabHCE' ){
									
									if( tablas[i].id != 'tabCTCReemplazp' ){
										//agregro los parametros faltanstes
										var parametros = urlDatosArt+"&dat_Detpes="+tablas[i].id.substr(6)+"&dat_Detpro="+protocolo+"&Encid="+buscarPorName( "dat_Encid", document.getElementById( "encProtocolo" ) )[0].value;
										
										//console.log(parametros);
										
										return false;
										
										
										//hago la grabacion por ajax del articulo
										var rpAjax = consultasAjax( "POST", "protocolosOrdenesHCE.php?wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&ajax=2", parametros, false, mensajesGrabar );
									}
									else{	//Aquí guardo la información de los medicamentos que reemplazan el medicamentos NO POS UTILIZADO
										//agregro los parametros faltanstes
										var parametros = urlDatosArt+"&dat_Metpro="+protocolo+"&Encid="+buscarPorName( "dat_Encid", document.getElementById( "encProtocolo" ) )[0].value;
					
										//hago la grabacion por ajax del articulo
										var rpAjax = consultasAjax( "POST", "protocolosOrdenesHCE.php?wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&ajax=17", parametros, false, mensajesGrabar );
									}
								}
								else{
									//agregro los parametros faltanstes
									var parametros = urlDatosArt+"&dat_Detpro="+protocolo+"&Encid="+buscarPorName( "dat_Encid", document.getElementById( "encProtocolo" ) )[0].value;
				
									//hago la grabacion por ajax del articulo
									var rpAjax = consultasAjax( "POST", "protocolosOrdenesHCE.php?wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&ajax=12", parametros, false, mensajesGrabar );
								}
								
								if( rpAjax > 0 ){
									
									if( tablas[i].id != 'tabCTCReemplazp' ){
										buscarPorName( "dat_Detid", tablas[i].rows[j] )[0].value = rpAjax;
									}
									else{
										buscarPorName( "dat_Mreid", tablas[i].rows[j] )[0].value = rpAjax;
									}
								}
							}
						}
					}
				}
			}
		}
		else{
			alert( ajax.responseText );
		}
	}
	
	$.unblockUI();
}

function mensajesGrabar( ajax ){

	if ( ajax.readyState==4 && ajax.status==200 ){
		
		totalRegistros--;
		
		if( totalRegistros == 0 ){
			alert( "Termino de grabar" );
		}
	}
}

/************************************************************************************************
 * Agrega la funcion de buscada de medicamentos con autocomplete
 ************************************************************************************************/
function buscadorArticulo( fila ){

		// $("#restultAjax").flushCache();
    	// $("#restultAjax").unbind("result");
    	// $("#restultAjax").setOptions({url:"protocolosOrdenesHCE.php?ajax=3&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&art="+restultAjax.value+"&wcenmez="+wcenmez.value });

		//$("#restultAjax").autocomplete("protocolosOrdenesHCE.php?ajax=3&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&wcenmez="+wcenmez.value, {
		$("[name=dat_Detcod]", fila ).autocomplete("protocolosOrdenesHCE.php?ajax=3&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&wcenmez="+wcenmez.value, {
			cacheLength:1,
			delay:0,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width:1000,
			autoFill:false,
			minChars: 2,
			formatItem: function(data, i, n, value) {
			
				//convierto el string en json
				eval( "var datos = "+data );
				
				return datos[0].label;	//Eso es lo que se muestra al usuario
				// return value;
			},
			formatResult: function(data, value){
	//    			debugger;
				eval( "var datos = "+data )
				return datos[0].value.cod;
			}
		}).result( function(event, item ){
			
			//La respuesta es un json
			//convierto el string en formato json
			eval( "var datos = "+item );			
			setearDatosFilaArticulos( datos, this );
			$("#dat_Enccodigo").val(datos[0].value.cod);
		});
}

/************************************************************************************************
 * Agrega los datos necesarios a una fila
 *
 * Nota: datos es un objeto json, fila son los datos de la fila que se desean llenar
 ************************************************************************************************/
function setearDatosFilaArticulos( datosFila, campo ){

	campo.value = datosFila[0].value.cod;
	campo._lastValue = campo.value;

	var fila = campo.parentNode.parentNode

	//Busco los elementos que deseo llenar
	//Primero 
	fila.cells[2].innerHTML = datosFila[0].value.com;	//Nombre comercial
	
	//Agrego la unidad encontrada
	//
	fila.cells[3].getElementsByTagName( "div" )[0].innerHTML = datosFila[0].value.uni;
	
	//Clono el campo de vias por defecto
	var auxSlVia = buscarPorName( "dat_Detvia", fila.parentNode.rows[1] );
	
	var auxSlVia = auxSlVia[0].cloneNode( true );
	
	//Borro el campo vias
	var slVias = buscarPorName( "dat_Detvia", fila );
	var tdSl = slVias[0].parentNode;
	slVias[0].parentNode.removeChild( slVias[0] );
	
	//Agrego el select clonado
	tdSl.appendChild( auxSlVia );
	
	//Busco el select de vias, esto devuelve un array de elementos
	var slVias = buscarPorName( "dat_Detvia", fila );
	
	//En la fila siempre hay solo un elemento Detvia
	//Dejo solo las vias que puede soportar el articulo
	dejarOptionsEnSelect( slVias[0], datosFila[0].value.vias.split(",") );
	
	//Si al dejar las vias por defecto, solo hay una opcion, la dejo seteada
	if( slVias[0].options.length == 1 ){
		slVias[0].selectedIndex = 0;
	}
	
	//Busco el select de frecuencias, esto devuelve un array de elementos
	var slFre = buscarPorName( "dat_Detfre", fila );
	
	//Lo dejo sin seleccionar nada
	slFre[0].options.selectedIndex = -1;
	
	//Busco el select de frecuencias, esto devuelve un array de elementos
	var slCnd = buscarPorName( "dat_Detcnd", fila );
	
	//Lo dejo sin seleccionar nada
	slCnd[0].options.selectedIndex = -1;
}

/************************************************************************************************************
 * Agrega una fila nueva a llenar para la pestaña articulos del programa ordenes
 ************************************************************************************************************/
function agregarArticulo(){

	var filaNueva = agregarFila( tabPesMedicamentos );
	
	//Despues de insertar la fila se agrega la funcion de busqueda de medicamentos por autocomplete
	buscadorArticulo( filaNueva );
	
	buscarPorName( "dat_Detcod", filaNueva )[0].onchange = alCambiarConsultaCodigo;
	
	//Busco el select de vias, esto devuelve un array de elementos
	var slVias = buscarPorName( "dat_Detvia", filaNueva );
	
	//Lo dejo sin seleccionar nada
	slVias[0].options.selectedIndex = -1;
	
	//Busco el select de frecuencias, esto devuelve un array de elementos
	var slFre = buscarPorName( "dat_Detfre", filaNueva );
	
	//Lo dejo sin seleccionar nada
	slFre[0].options.selectedIndex = -1;
	
	//Busco el select de frecuencias, esto devuelve un array de elementos
	var slCnd = buscarPorName( "dat_Detcnd", filaNueva );
	
	//Lo dejo sin seleccionar nada
	slCnd[0].options.selectedIndex = -1;
	
	return filaNueva;
}





/************************************************************************************************************
 * Agrega una fila nueva a llenar para la pestaña articulos del programa ordenes
 ************************************************************************************************************/
function agregarCtcArticulo(){

	var filaNueva = agregarFila( tabPesCtcMedicamentos );
	
	//Despues de insertar la fila se agrega la funcion de busqueda de medicamentos por autocomplete
	buscadorCtcArticulo( filaNueva );
	
	buscarPorName( "dat_Detcod", filaNueva )[0].onchange = alCambiarConsultaCodigo;
	
	var filaAux = agregarFila( document.getElementById( "tabCTCReemplazp" ) );
	
	buscadorArticuloNoPos( filaAux );
	
	buscarPorName( "dat_Mreart", filaAux )[0].onchange = alCambiarConsultaCodigo;
	
	return filaNueva;
}

/************************************************************************************************
 * Agrega la funcion de buscada de medicamentos con autocomplete
 ************************************************************************************************/
function buscadorCtcArticulo( fila ){

		// $("#restultAjax").flushCache();
    	// $("#restultAjax").unbind("result");
    	// $("#restultAjax").setOptions({url:"protocolosOrdenesHCE.php?ajax=3&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&art="+restultAjax.value+"&wcenmez="+wcenmez.value });

		//$("#restultAjax").autocomplete("protocolosOrdenesHCE.php?ajax=3&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&wcenmez="+wcenmez.value, {
		$("[name=dat_Detcod]", fila ).autocomplete("protocolosOrdenesHCE.php?ajax=4&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&wcenmez="+wcenmez.value, {
			cacheLength:1,
			delay:0,
			max: 100,
			scroll: false,
			scrollHeight: 500,
			matchSubset: false,
			matchContains: true,
			width:1000,
			autoFill:false,
			minChars: 2,
			formatItem: function(data, i, n, value) {
			
				//convierto el string en json
				eval( "var datos = "+data );
				
				return datos[0].label;	//Eso es lo que se muestra al usuario
				// return value;
			},
			formatResult: function(data, value){
	//    			debugger;
				eval( "var datos = "+data )
				// return datos[0].value.cod;
				return '';
			}
		}).result( function(event, item ){
			
			//La respuesta es un json
			//convierto el string en formato json
			eval( "var datos = "+item );
			
			this.value = datos[0].value.cod;
			$("#dat_Enccodigo").val(datos[0].value.cod);
			setearDatosFilaArticulosCtc( datos, this );
			
			setCtcArtEnc( datos, buscarPorName( "dat_Encnom", document.getElementById( "encProTipDes" ) )[0] );
			
			validarDatosCTC( this );
		
			this.lastValue = datos;
			this.setDatos = function( datos, campo ){
				
				setCtcArtEnc( datos, buscarPorName( "dat_Encnom", document.getElementById( "encProTipDes" ) )[0] );
				buscarPorName( "dat_Encnom", document.getElementById( "encProTipDes" ) )[0].lastValue = datos;
				
				setearDatosFilaArticulosCtc( datos, campo );
			}
		});
}

/******************************************************************************************
 * Setea los datos para la tabla de CTc articulo
 ******************************************************************************************/
function setearDatosFilaArticulosCtc( datosFila, campo ){

	if( datosFila ){
		campo.value = datosFila[0].value.cod;
		
		campo._lastValue = campo.value;

		var fila = campo.parentNode.parentNode;

		//Busco los elementos que deseo llenar
		//Primero 
		fila.cells[2].innerHTML = datosFila[0].value.gen;	//Nombre comercial
	}
	else{
		campo.value = "";
		
		campo._lastValue = "";

		var fila = campo.parentNode.parentNode;

		//Busco los elementos que deseo llenar
		//Primero 
		fila.cells[2].innerHTML = "";	//Nombre comercial
	}
}



/******************************************************************************************
 * Setea el buscador de procedimientos
 ******************************************************************************************/
function buscadorProcedimiento( fila ){

	$("[name=dat_Detcod]", fila ).autocomplete("protocolosOrdenesHCE.php?ajax=5&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		autoFill:false,
		minChars: 3,
		formatItem: function(data, i, n, value) {
		
			//convierto el string en json
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult: function(data, value){

			eval( "var datos = "+data )
			return datos[0].value.cod;
		}
	}).result( function(event, item ){
		
		//La respuesta es un json
		//convierto el string en formato json
		eval( "var datos = "+item );
		$("#dat_Enccodigo").val(datos[0].value.cod);
		this.value = datos[0].value.cod;
		
		setearDatosFilaProcedimientos( datos, this );
	});
}

function setearDatosFilaProcedimientos( datosFila, campo ){

	campo.value = datosFila[0].value.cod;
	
	campo._lastValue = campo.value;

	var fila = campo.parentNode.parentNode;
	//Busco los elementos que deseo llenar
	//Primero 
	fila.cells[2].innerHTML = datosFila[0].value.des;	//Nombre comercial

}

/************************************************************************************************************
 * Agrega una fila nueva a llenar para la pestaña procedimientos del programa ordenes
 ************************************************************************************************************/
function agregarProcedimiento( tabla ){

	var filaNueva = agregarFila( tabPesProcedimientos );
	
	//Despues de insertar la fila se agrega la funcion de busqueda de medicamentos por autocomplete
	buscadorProcedimiento( filaNueva );
	
	buscarPorName( "dat_Detcod", filaNueva )[0].onchange = alCambiarConsultaCodigo;
	
	return filaNueva;
}



/******************************************************************************************
 * Setea el buscador de procedimientos
 ******************************************************************************************/
function buscadorCtcProcedimiento( fila ){

	$("[name=dat_Detcod]", fila ).autocomplete("protocolosOrdenesHCE.php?ajax=6&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
		cacheLength:0,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		autoFill:false,
		minChars: 3,
		formatItem: function(data, i, n, value) {
		
			//convierto el string en json
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult: function(data, value){

			eval( "var datos = "+data )
			return datos[0].value.cod;
		}
	}).result( function(event, item ){
		
		//La respuesta es un json
		//convierto el string en formato json
		eval( "var datos = "+item );
		$("#dat_Enccodigo").val(datos[0].value.cod);
		this.value = datos[0].value.cod;
		
		setearDatosFilaCtcProcedimientos( datos, this );
		
		setCtcProcs( datos, buscarPorName( "dat_Encnom", encProTipDes )[0] )
		
		validarDatosCTC( this );
		
		this.lastValue = datos;
		this.setDatos = function( datos, campo ){
			
			setCtcProcs( datos, buscarPorName( "dat_Encnom", document.getElementById( "encProTipDes" ) )[0] );
			buscarPorName( "dat_Encnom", document.getElementById( "encProTipDes" ) )[0].lastValue = datos;
			
			setearDatosFilaCtcProcedimientos( datos, campo );
		}
	});
}

function setearDatosFilaCtcProcedimientos( datosFila, campo ){

	if( datosFila ){
		campo.value = datosFila[0].value.cod;
		
		campo._lastValue = campo.value;
		
		var fila = campo.parentNode.parentNode;

		//Busco los elementos que deseo llenar
		fila.cells[2].innerHTML = datosFila[0].value.des;	//Nombre comercial
	}
	else{
		campo.value = "";
		
		campo._lastValue = campo.value;
		
		var fila = campo.parentNode.parentNode;

		//Busco los elementos que deseo llenar
		fila.cells[2].innerHTML = "";	//Nombre comercial
	}
}

/************************************************************************************************************
 * Agrega una fila nueva a llenar para la pestaña procedimientos del programa ordenes
 ************************************************************************************************************/
function agregarCtcProcedimiento(){

	var filaNueva = agregarFila( tabPesCtcProcedimientos );
	
	//Despues de insertar la fila se agrega la funcion de busqueda de medicamentos por autocomplete
	buscadorCtcProcedimiento( filaNueva );
	
	buscarPorName( "dat_Detcod", filaNueva )[0].onchange = alCambiarConsultaCodigo;
	
	return filaNueva;
}


function setCco( datos, campo ){

	var auxDivs = campo.getElementsByTagName( "div" );
	
	//El valor a llenar es el div que siempre esta en posicion 1
	auxDivs[0].innerHTML = datos[0].value.nom
}


function setDx( datos, campo ){

	campo.value = datos[0].value.cod;
	campo._lastValue = campo.value;

	var auxDivs = campo.parentNode.getElementsByTagName( "div" );
	
	//El valor a llenar es el div que siempre esta en posicion 1
	auxDivs[0].innerHTML = datos[0].value.des;
}

function setMed( datos, campo ){

	campo.value = datos[0].value.cod;

	campo._lastValue = campo.value;
	
	var auxDivs = campo.parentNode.getElementsByTagName( "div" );
	
	//El valor a llenar es el div que siempre esta en posicion 1
	auxDivs[0].innerHTML = datos[0].value.des;
	
	//agrego el campo todos
	//datos[0].value.esp[ datos[0].value.esp.length ] = '*';
	
	//NO SE QUE HACE ESTE PARTE DEL CODIGO, slCloneEspecialidades
	//Clono las especialidades
	var auxSlEsp = slCloneEspecialidades.cloneNode( true );
	auxSlEsp.value = buscarPorName( "dat_Encesp", campo.parentNode.parentNode )[0].value;
	
	//Borro el campo actual de especialidades
	//campo.parentNode.parentNode.cells[3].innerHTML = '';
	var fila = campo.parentNode.parentNode;
	$(fila).find("[name=dat_Encesp]").parent().html(auxSlEsp);
	
	
	//Agrego el clone
	//campo.parentNode.parentNode.cells[3].appendChild( auxSlEsp );
	
	//Busco el campo especialidades
	if( datos[0].value.cod != '*' ){
		dejarOptionsEnSelect( buscarPorName( "dat_Encesp", campo.parentNode.parentNode )[0], datos[0].value.esp );
	}
	
	if( auxSlEsp.options.length == 1 ){
		auxSlEsp.selectedIndex = 0;
	}
}

function setPro( datos, campo ){
	campo.value = datos[0].value.cod;

	var auxDivs = campo.parentNode.getElementsByTagName( "div" );
	
	//El valor a llenar es el div que siempre esta en posicion 1
	auxDivs[0].innerHTML = datos[0].value.des;
}

/****************************************************************************************************
 * Carga un protocolo ya existente
 ****************************************************************************************************/
function cargarProtocolos( proid ){
	
	try{

		$.blockUI({ message: $( "#msgCargaProtocolo" )});
		moBuscardoract = false;
		if( !proid )
			var idCargarProtocolos1 = document.getElementById( "idCargarProtocolos" );
		else{
			var idCargarProtocolos1 = document.getElementById( "idCargarProtocolos" );
			idCargarProtocolos1.value = proid;
			idCargarProtocolos1.aCargar = proid;
			
			//moBuscador();
			moBuscardoract = true;
		}
		
		if( idCargarProtocolos1.value != '' ){
			var parametros = "protocolo=" + idCargarProtocolos1.aCargar;
			
			//hago la grabacion por ajax del articulo
			consultasAjax( "POST", "protocolosOrdenesHCE.php?wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value+"&wcenmez="+wcenmez.value+"&ajax=10", parametros, true, procesoCargarRespAjax );
		}
		else{
			$.unblockUI();
		}
	}
	catch(e){
		alert( "Error: " + e );
	}
}

function procesoCargarRespAjax( ajax ){
	
	if ( ajax.readyState==4 && ajax.status==200 ){
	
		if( ajax.responseText != '' ){
		
			if( moBuscardoract ){
				moBuscador();
				moBuscardoract = false;
			}
		
			try{
				eval( "var datos = "+ajax.responseText );
				
				cargarDatosProtocolos( datos );
				
				document.getElementById( "idCargarProtocolos" ).value = '';
			}
			catch(e){
			}
		}
	}
	
	$.unblockUI();
}



/********************************************************************************
 * Carga todos los datos necesarios a partir de un objeto JSON
 *
 * Nota: datos es un objeto JSON
 ********************************************************************************/
function cargarDatosProtocolos( datos ){

	nuevoPrgOrdenes();
	
	/********************************************************************************
	 * Cargando datos del encabezado del protocolo
	 ********************************************************************************/
	//Primero cargo los datos de encabezado
	buscarPorName( "dat_Encnom", encProTipDes )[0].value = datos[0].value.nom;
	buscarPorName( "dat_Encnom", encProTipDes )[0]._lastValue = datos[0].value.nom;
	
	if( datos[0].value.med != '' ){
		setMed( datos[0].value.med, buscarPorName( "dat_Encmed", encProtocolo )[0] );
	}
	else{
		//Objeto Json con valores vacio
		setMed( [{value:{cod:"", des:""}}], buscarPorName( "dat_Encmed", encProtocolo )[0] );
	}
	
	buscarPorName( "dat_Encesp", encProtocolo )[0].value = datos[0].value.esp;
	
	if( datos[0].value.dia != '' ){
		setDx( datos[0].value.dia, buscarPorName( "dat_Encdia", encProtocolo )[0] );
	}
	else{
		//Objeto Json con valores vacio
		setDx( [{value:{cod:"", des:""}}], buscarPorName( "dat_Encdia", encProtocolo )[0] );
	}
	
	if( datos[0].value.tra != '' ){
		setDx( datos[0].value.tra, buscarPorName( "dat_Enctra", encProtocolo )[0] );
	}
	else{
		//Objeto Json con valores vacio
		setDx( [{value:{cod:"", des:""}}], buscarPorName( "dat_Enctra", encProtocolo )[0] );
	}
	
	if( datos[0].value.cio != '' ){
		setDx( datos[0].value.cio, buscarPorName( "dat_Enccio", encProtocolo )[0] );
	}
	else{
		//Objeto Json con valores vacio
		setDx( [{value:{cod:"", des:""}}], buscarPorName( "dat_Enccio", encProtocolo )[0] );
	}
	
	if( datos[0].value.ped == 'on' ){
		buscarPorName( "dat_Encped", encProtocolo )[0].checked = true;
	}
	else{
		buscarPorName( "dat_Encped", encProtocolo )[0].checked = false;
	}
	
	if( datos[0].value.rec == 'on' ){
		buscarPorName( "dat_Encrec", encProtocolo )[0].checked = true;
	}
	else{
		buscarPorName( "dat_Encrec", encProtocolo )[0].checked = false;
	}
	
	if( datos[0].value.ndi == 'on' ){
		buscarPorName( "dat_Encndi", encProtocolo )[0].checked = true;
	}
	else{
		buscarPorName( "dat_Encndi", encProtocolo )[0].checked = false;
	}
	
	buscarPorName( "dat_Enccco", encProtocolo )[0].value = datos[0].value.cco;
	buscarPorName( "dat_Encid", encProtocolo )[0].value = datos[0].value.id;
		
	var infoAdicional = "<strong style='color:blue;font-size:12pt'>ADVERTENCIA</strong><br><br>";
	
	/********************************************************************************
	 * Cargando el detalle del protocolo
	 ********************************************************************************/
	 
	//Primero busco las pestañas a llenar
	//recorro los datos en el campo pes( pestaña ) de datos
	for( indexPes in datos[0].pes ){

		//Busco la tabla correspondiente en la que se desea agregar el medicamento
		//Esto agregar un item( articulo, ctc de articulos, procedimientos o ctc de procedimientos) a la tabla correspondiente

		switch( indexPes ){
			
			case 'Medicamentos':
			
				document.getElementById( "prOrdenes" ).style.display = '';
				$("#programasGenerales").tabs().show();
				$("#programasGenerales").tabs('select', 0);
				
				//Busco el tipo de protocolo que se va a crear
				buscarPorName( "slTipPro", encProTipDes )[0].value = 'prOrdenes';
			
				for( i in datos[0].pes[ indexPes ] ){
				
					if( datos[0].pes[ indexPes ][i].cod[0].msg != "" ){
						if( $( "#dvMsg" ).html() == "" )
							$( "#dvMsg" ).html( infoAdicional + datos[0].pes[ indexPes ][i].cod[0].msg );
						else
							$( "#dvMsg" ).html( $( "#dvMsg" ).html()+"<br>"+datos[0].pes[ indexPes ][i].cod[0].msg );
						$( "#dvMsg" ).css({ display: "" });
					}
					
					var fila = agregarArticulo();
					
					setearDatosFilaArticulos( datos[0].pes[ indexPes ][i].cod, buscarPorName( "dat_Detcod", fila )[0] );
					
					buscarPorName( "dat_Detdos", fila )[0].value = datos[0].pes[ indexPes ][i].dos;
					buscarPorName( "dat_Detfre", fila )[0].value = datos[0].pes[ indexPes ][i].fre;
					buscarPorName( "dat_Detvia", fila )[0].value = datos[0].pes[ indexPes ][i].via;
					buscarPorName( "dat_Detcnd", fila )[0].value = datos[0].pes[ indexPes ][i].cnd;
					buscarPorName( "dat_Detobs", fila )[0].value = datos[0].pes[ indexPes ][i].obs;
					buscarPorName( "dat_Detid", fila )[0].value  = datos[0].pes[ indexPes ][i].id;
					$("#dat_Enccodigo").val(datos[0].pes[ indexPes ][i].cod_oculto);
				}
			break;
			
			case 'CtcMedicamentos':
			
				if( datos[0].pes[ indexPes ][0].cod[0].msg != "" ){
					$( "#dvMsg" ).html( infoAdicional + datos[0].pes[ indexPes ][0].cod[0].msg );
					$( "#dvMsg" ).css({ display: "" });
				}
			
				//Primero cargo los datos de encabezado
				buscarPorName( "dat_Encnom", encProTipDes )[0].value = datos[0].pes[ indexPes ][0].cod[0].value.gen;
				buscarPorName( "dat_Encnom", encProTipDes )[0].replaceValue = datos[0].value.nom;
				buscarPorName( "dat_Encnom", encProTipDes )[0]._lastValue = datos[0].pes[ indexPes ][0].cod[0].value.gen;
				
				document.getElementById( "ctcArts" ).style.display = '';
				$("#programasGenerales").tabs().show();
				$("#programasGenerales").tabs('select', 2);
				
				//Busco el tipo de protocolo que se va a crear
				buscarPorName( "slTipPro", encProTipDes )[0].value = 'ctcArts';
			
				for( i in datos[0].pes[ indexPes ] ){
				
					var fila = agregarCtcArticulo();
					$("#dat_Enccodigo").val(datos[0].pes[ indexPes ][i].cod_oculto);
					//Seteo los datos del articulo CTC
					setearDatosFilaArticulosCtc( datos[0].pes[ indexPes ][i].cod, buscarPorName( "dat_Detcod", fila )[0] );
					
					buscarPorName( "dat_Detete", fila )[0].value = datos[0].pes[ indexPes ][i].ete;
					buscarPorName( "dat_Detese", fila )[0].value = datos[0].pes[ indexPes ][i].ese;
					buscarPorName( "dat_Dettre", fila )[0].value = datos[0].pes[ indexPes ][i].tre;
					buscarPorName( "dat_Detbib", fila )[0].value = datos[0].pes[ indexPes ][i].bib;
					buscarPorName( "dat_Detiin", fila )[0].value = datos[0].pes[ indexPes ][i].iin;
					buscarPorName( "dat_Detid", fila )[0].value  = datos[0].pes[ indexPes ][i].id;
					
					var filaAux = document.getElementById( "tabCTCReemplazp" ).rows[2];
					
					//Seteo los datos del medicamento de reemplazo
					setearDatosFilaArticulosReemplazar( datos[0].pes[ indexPes ][i].rem, buscarPorName( "dat_Mreart", filaAux )[0] );
														
					buscarPorName( "dat_Mrepos", filaAux )[0].value = datos[0].pes[ indexPes ][i].rem[0].value.pos;
					buscarPorName( "dat_Mrepre", filaAux )[0].value = datos[0].pes[ indexPes ][i].rem[0].value.pre;
					buscarPorName( "dat_Mreddi", filaAux )[0].value = datos[0].pes[ indexPes ][i].rem[0].value.ddi;
					buscarPorName( "dat_Mrecan", filaAux )[0].value = datos[0].pes[ indexPes ][i].rem[0].value.can;
					buscarPorName( "dat_Mretto", filaAux )[0].value = datos[0].pes[ indexPes ][i].rem[0].value.dto;
					buscarPorName( "dat_Mreid", filaAux )[0].value  = datos[0].pes[ indexPes ][i].rem[0].value.id;				
					
				}
			break;
			
			
			case 'Procedimientos':
			
				document.getElementById( "prOrdenes" ).style.display = '';
				$("#programasGenerales").tabs().show();
				$("#programasGenerales").tabs('select', 0);
				
				//Busco el tipo de protocolo que se va a crear
				buscarPorName( "slTipPro", encProTipDes )[0].value = 'prOrdenes';
			
				for( i in datos[0].pes[ indexPes ] ){
				
					if( datos[0].pes[ indexPes ][i].cod[0].msg != "" ){
						if( $( "#dvMsg" ).html() == "" )
							$( "#dvMsg" ).html( infoAdicional + datos[0].pes[ indexPes ][i].cod[0].msg );
						else
							$( "#dvMsg" ).html( $( "#dvMsg" ).html()+"<br>"+datos[0].pes[ indexPes ][i].cod[0].msg );
						$( "#dvMsg" ).css({ display: "" });
					}
					
					var fila = agregarProcedimiento();
					
					setearDatosFilaProcedimientos( datos[0].pes[ indexPes ][i].cod, buscarPorName( "dat_Detcod", fila )[0] );
					
					buscarPorName( "dat_Detjus", fila )[0].value = datos[0].pes[ indexPes ][i].jus;			
					buscarPorName( "dat_Detid", fila )[0].value  = datos[0].pes[ indexPes ][i].id;
					buscarPorName( "dat_Detops", fila )[0].value = datos[0].pes[ indexPes ][i].ops;
					buscarPorName( "dat_Detopn", fila )[0].value = datos[0].pes[ indexPes ][i].opn;
					$("#dat_Enccodigo").val(datos[0].pes[ indexPes ][i].cod_oculto);
					
				}
			break;
			
			case 'CtcProcedimientos':
			
				if( datos[0].pes[ indexPes ][0].cod[0].msg != "" ){
					$( "#dvMsg" ).html( infoAdicional + datos[0].pes[ indexPes ][0].cod[0].msg );
					$( "#dvMsg" ).css({ display: "" });
				}
			
				//Primero cargo los datos de encabezado
				buscarPorName( "dat_Encnom", encProTipDes )[0].value = datos[0].pes[ indexPes ][0].cod[0].value.des;
				buscarPorName( "dat_Encnom", encProTipDes )[0].replaceValue = datos[0].value.nom;
				buscarPorName( "dat_Encnom", encProTipDes )[0]._lastValue = datos[0].pes[ indexPes ][0].cod[0].value.des;
			
				$("#programasGenerales").tabs().show();
				$("#programasGenerales").tabs('select', 3);
			
				document.getElementById( "ctcProcs" ).style.display = '';
				
				//Busco el tipo de protocolo que se va a crear
				buscarPorName( "slTipPro", encProTipDes )[0].value = 'ctcProcs';
			
				for( i in datos[0].pes[ indexPes ] ){

					
					var fila = agregarCtcProcedimiento();
					try{
						setearDatosFilaCtcProcedimientos( datos[0].pes[ indexPes ][i].cod, buscarPorName( "dat_Detcod", fila )[0] );												
						buscarPorName( "dat_Detjus", fila )[0].value = datos[0].pes[ indexPes ][i].jus;		
						buscarPorName( "dat_Detops", fila )[0].value = datos[0].pes[ indexPes ][i].ops;		
						buscarPorName( "dat_Detopn", fila )[0].value = datos[0].pes[ indexPes ][i].opn;
						buscarPorName( "dat_Detid", fila )[0].value  = datos[0].pes[ indexPes ][i].id;
						$("#dat_Enccodigo").val(datos[0].pes[ indexPes ][i].cod_oculto);
					}
					catch(e){}
					
				}

			break;
		}
	}
	
	//Cargando datos de HCE
	if( datos[0].hce ){
	
		document.getElementById( "prHCE" ).style.display = '';
		$("#programasGenerales").tabs().show();
		$("#programasGenerales").tabs('select', 1);
		
		//Busco el tipo de protocolo que se va a crear
		buscarPorName( "slTipPro", encProTipDes )[0].value = 'prHCE';
	
		for( var x in datos[0].hce ){
			
			var fila = agregarCampoHCE(true);					
			setearDatosFilaHCE( datos[0].hce[x].cod, buscarPorName( "dat_Detcod", fila )[0] );
			
			//buscarPorName( "dat_Detcod", fila )[0].value = datos[0].hce[x].cod;
			buscarPorName( "dat_Detid", fila )[0].value  = datos[0].hce[x].id;
			buscarPorName( "dat_Detcmp", fila )[0].value  = datos[0].hce[x].cmp;
			
			var pos = 0;
			for( var i = 0; i < datos[0].hce[x].cod[0].value.cmp.length; i++ ){
				if( datos[0].hce[x].cod[0].value.cmp[i].con == datos[0].hce[x].cmp ){
					pos = i;
					break;
				}
			}
			
			//Si hay algún mensaje que mostrar se muestra
			//Por campo se el mensaje se encuentra en la lista de opciones para la tabla
			if( datos[0].hce[x].cod[0].value.cmp[ pos ].msg != "" ){
				$( "#dvMsg" ).html( $( "#dvMsg" ).html() + datos[0].hce[x].cod[0].value.cmp[ pos ].msg );
				$( "#dvMsg" ).css({ display: "" });
			}
			
			
			//----buscarPorName( "dat_Detcmp", fila )[0].onchange( buscarPorName( "dat_Detcmp", fila )[0], datos[0].hce[x].val );
			setTipoDatoHCE( buscarPorName( "dat_Detcmp", fila )[0], datos[0].hce[x].val );
			//----buscarPorName( "dat_Detval", fila )[0].value  = datos[0].hce[x].val;
		}
	}
	
	//Septiembre 24 de 2013
	$("[name=dat_Enctip]").val( datos[0].value.tip );
}

/************************************************************************************************************
 * Borrando los datos de la tabla
 ************************************************************************************************************/
function nuevoPrgOrdenes(){

	//Borro todos los campos de las tablas de movimiento
	var arIdTablas =  new Array( "tabPesMedicamentos", "tabPesCtcMedicamentos", "tabPesProcedimientos", "tabPesCtcProcedimientos", "tabHCE", "tabCTCReemplazp" );
	
	for( var i = 0; i < arIdTablas.length; i++ ){
	
		var tb = document.getElementById( arIdTablas[ i ] );
		
		for( var j = 2; j < tb.rows.length; ){
			borrarArticulo( tb.rows[j].cells[0].firstChild );
		}
	}
	
	$(".filaEnc").not(":first").remove();//Eliminar todos los encabezados excepto el primero
	
	/********************************************************************************
	 * Cargando datos del encabezado del protocolo
	 ********************************************************************************/
	 
	//Primero cargo los datos de encabezado
	buscarPorName( "dat_Encnom", encProTipDes )[0].value = "";
	buscarPorName( "dat_Encnom", encProTipDes )[0].replaceValue = "";
	buscarPorName( "dat_Encnom", encProTipDes )[0]._lastValue = "";
	
	setMed( [{value:{cod:"", des:""}}], buscarPorName( "dat_Encmed", encProtocolo )[0] );
	
	buscarPorName( "dat_Encesp", encProtocolo )[0].value = "";

	//Objeto Json con valores vacio
	setDx( [{value:{cod:"", des:""}}], buscarPorName( "dat_Encdia", encProtocolo )[0] );
	
	//Objeto Json con valores vacio
	setDx( [{value:{cod:"", des:""}}], buscarPorName( "dat_Enctra", encProtocolo )[0] );
	
	//Objeto Json con valores vacio
	// setPro( [{value:{cod:"", des:""}}], buscarPorName( "dat_Encpro", encProtocolo )[0] );
	
	buscarPorName( "dat_Enccco", encProtocolo )[0].value = '';
	
	setDx( [{value:{cod:"", des:""}}], buscarPorName( "dat_Enccio", encProtocolo )[0] );
	
	buscarPorName( "dat_Encid", encProtocolo )[0].value = '';
	
	buscarPorName( "slTipPro", encProTipDes )[0].selectedIndex = -1;
	
	buscarPorName( "dat_Encped", encProtocolo )[0].checked = false;
	
	buscarPorName( "dat_Encrec", encProtocolo )[0].checked = false;
	
	
	document.getElementById( "ctcProcs" ).style.display = 'none';
	document.getElementById( "ctcArts" ).style.display = 'none';
	document.getElementById( "prOrdenes" ).style.display = 'none';
	document.getElementById( "prHCE" ).style.display = 'none';
	
	$("#programasGenerales").tabs().hide();
	
	$( "#dvMsg" ).html("");
	$( "#dvMsg" ).css({display: "none"});
}

function alCambiarConsultaCodigo(){

	if( true || this.value.length <= 6 ){
		if( this._lastValue ){
			this.value = this._lastValue;
		}
		else{
			this.value = "";
		}
	}
}

function agregarCampoHCE( automatico ){
	
	if( automatico != true ){
		if( $("#tabHCE tr:visible:last").find("input[name=dat_Detcod]").val() == "" ){
			alert("Debe terminar el item antes de agregar uno nuevo");
			return;
		}
	}
	
	var indi = $("#tabHCE").find("> tbody > tr").length; //La cantidad de items que hay
	indi--;
	
	var filaPrev = $("#tabHCE tr.filappal:last");
	var filaNueva = agregarFila(tabHCE, indi);
	
	try{
		//Se agrega la vble "infoAdd" de la fila anterior a la nueva fila 
		var info = buscarPorName( "dat_Detcmp", filaPrev[0] )[0].infoAdd;
		buscarPorName( "dat_Detcmp", filaNueva )[0].infoAdd = info;
	}catch(e){}
	
	filaNueva.cells[4].innerHTML = "";
	filaNueva.cells[5].innerHTML = "";
	
	//Despues de insertar la fila se agrega la funcion de busqueda de medicamentos por autocomplete
	buscadorTabHCE( filaNueva );
	buscarPorName( "dat_Detcod", filaNueva )[0].onchange = alCambiarConsultaCodigo;
	buscarPorName( "dat_Detid", filaNueva )[0].value = "";
	
	return filaNueva;
}

function buscadorTabHCE( fila ){

	$("[name=dat_Detcod]", fila ).autocomplete("protocolosOrdenesHCE.php?ajax=11&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		autoFill:false,
		minChars: 3,
		formatItem: function(data, i, n, value) {
		
			//convierto el string en json
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult: function(data, value){
			
			eval( "var datos = "+data )
			return datos[0].value.cod;
		}
	}).result( function(event, item ){
		
		//La respuesta es un json
		//convierto el string en formato json
		eval( "var datos = "+item );
		$("#dat_Enccodigo").val(datos[0].value.cod);
		this.value = datos[0].value.cod;
		this._lastValue = datos[0].value.cod	//new 2013-01-22
		
		setearDatosFilaHCE( datos, this );
	});
}

function setearDatosFilaHCE( datos, campo ){

	campo.value = datos[0].value.cod;	
	campo._lastValue = campo.value;
	
	var fila = campo.parentNode.parentNode;
	//Busco los elementos que deseo llenar
	//Primero 
	fila.cells[2].innerHTML = datos[0].value.des;	//Nombre comercial
	
	fila.cells[3].innerHTML = datos[0].value.cmp;	//Nombre comercial
	
	//Creo el select de los objetos
	var slCampos = "<SELECT name='dat_Detcmp' onChange='setTipoDatoHCE( this )'>";	
	slCampos += "<option></option>"
	for( x in datos[0].value.cmp ){
		var style = "";
		if( datos[0].value.cmp[ x ].est != 'on' ){
			style = "style='display:none;'"
		}
		slCampos += "<option "+style+" value="+datos[0].value.cmp[ x ].cod+">"+datos[0].value.cmp[ x ].con+" - "+datos[0].value.cmp[ x ].des+"</option>" 
	}	
	slCampos += "</SELECT>";
	
	fila.cells[3].innerHTML = slCampos;	
	
	//Agrego objeto JSON de los datos con los que armo el select para usar despues
	buscarPorName( "dat_Detcmp", fila )[0].infoAdd = datos[0].value.cmp;
}


function setTipoDatoHCE( cmp, valor ){
	
	/************************************************************************************************
	 * No se permite para un mismo campo dos valores por defecto
	 * Busco que el campo no sea de la tabla no sea igual que el que seleccionan
	 ************************************************************************************************/
	if( cmp.selectedIndex > 0 ){
		var slCmp = buscarPorName( "dat_Detcmp", document.getElementById( "tabHCE" ) );
		
		//Recorro todos los campo que sean distintos al select actual y que tengan el mismo value
		for( var i = 0; i < slCmp.length; i++ ){
		
			if( slCmp[i] != cmp && slCmp[i].value == cmp.options[ cmp.selectedIndex ].value ){
				
				//verifico que la tabla seleccionada sea igual
				var inCodAct = buscarPorName( "dat_Detcod", cmp.parentNode.parentNode );
				var inCodBus = buscarPorName( "dat_Detcod", slCmp[i].parentNode.parentNode );
				var inCodEst = buscarPorName( "dat_Detest", slCmp[i].parentNode.parentNode );
				
				
				if( inCodEst[0].value == 'on' && inCodAct[0].value == inCodBus[0].value ){
					cmp.selectedIndex = -1;
					cmp.parentNode.parentNode.cells[4].innerHTML = "";
					alert( "El campo seleccionado ya esta en la lista.\nNo puede repetir campo para la misma tabla " );
					return;
				}
			}
		}
	}
	/************************************************************************************************/
	
	var j = cmp.selectedIndex-1;
	
	if( j >= 0 ){
		cmp.parentNode.parentNode.cells[4].innerHTML = cmp.infoAdd[ j ].tip;
	}
	else{
		cmp.parentNode.parentNode.cells[4].innerHTML = "";
	}
	
	//Algunos campos tipo Seleccion estan guardados en la bd como (codigo)-(descripcion), a continuación se le quita la descripcion
	if( valor != undefined && j >= 0 && (cmp.infoAdd[ j ].tip == "Seleccion")){
		valor = valor.split("-");
		valor = valor[0];
	}
	
	//return;
	//Dependiendo la tabla y el tipo de dato elegido, se debe cargar el contenido del valor, 
	//ej: Si el tipo de dato es un select, se deben cargar las opciones que puede tener el campo
	var campo = jQuery(cmp);
	var fila = campo.parent().parent();
	
	var cod_tabla = fila.find("input[name=dat_Detcod]").val();
	var cod_campo = campo.val();
	var tipo_dato = cmp.infoAdd[ j ].tip;
	
	//console.log("Consultar Valor de: Tabla->"+cod_tabla+", campo->"+cod_campo+", tipo->"+tipo_dato);
	
	var wemp_pmla = $("#wemp_pmla").val();
	var whce = $("#whce").val();
	var wtabla = $("#whce").val();
	var whce = $("#whce").val();
	
	$.post('protocolosOrdenesHCE.php', { 	wemp_pmla: 	wemp_pmla,
									ajax : '20',
									whce : whce,
									wtabla : cod_tabla,
									wcampo : cod_campo,
									},
	function(data) {
		fila.find("td").eq(5).html( data );
		if( valor != undefined ){ //Si le vamos a llevar un valor predefinido
			var objeto = fila.find("[name=dat_Detval]");
			if( objeto.length > 0 ){ //Si existe un elemento con name=dat_Detval (Los type=radio no tienen ese name)
				var tipoObjeto = objeto.prop("tagName");
				if(  tipoObjeto == "TEXTAREA" )
					objeto.html(valor);
				else if( tipoObjeto == "SELECT")
					objeto.val(valor);
				else if( tipoObjeto == "INPUT" ){
					objeto.text(valor);
					objeto.val(valor);
					if( objeto[0].type.toUpperCase() == "CHECKBOX" ){
						if( valor == "on" )
							objeto.attr("checked",true);
						else
							objeto.attr("checked",false);
					}
				}
			}
			else{
					//Es tipo radio
				fila.find(".dat_Detval[value="+valor+"]").attr("checked",true);
			}
			
		}
	});
}

/******************************************************************************
 * 
 ******************************************************************************/
function setBuscadorPorTipoProtocolo( cmp ){   //Al cambiar el tipo de protocolo

	document.getElementById( "ctcProcs" ).style.display = 'none';
	document.getElementById( "ctcArts" ).style.display = 'none';
	document.getElementById( "prOrdenes" ).style.display = 'none';
	document.getElementById( "prHCE" ).style.display = 'none';
	
	var auxSlIndex = cmp.selectedIndex;
	
	nuevoPrgOrdenes();
	cmp.selectedIndex = auxSlIndex;

	//Dejo en blanco el nombre del protocolo
	buscarPorName( "dat_Encnom", encProTipDes )[0].value = '';
	
	try{
		buscarPorName( "dat_Encnom", encProTipDes )[0].replaceValue = '';
	}
	catch(e){}
	
	$("#programasGenerales").tabs().hide();	

	$("[name=dat_Encnom]", encProTipDes ).flushCache();
	$("[name=dat_Encnom]", encProTipDes ).unbind( "result" );
	$("[name=dat_Encnom]", encProTipDes ).autocomplete( "disable" );
	
	/************************************************************************************************
	 * Esto se hace para poder eliminar completamente el autocomplete que tenía asociado
	 ************************************************************************************************/
	//clono el objeto
	var auxInClone = buscarPorName( "dat_Encnom", encProTipDes )[0].cloneNode( true );	
	
	var fila = cmp.parentNode.parentNode;
	$(fila).find("[name=dat_Encnom]").parent().html(auxInClone);	
	//Elimino el objeto que acabo de clonar
	//encProtocolo.cells[1].removeChild( buscarPorName( "dat_Encnom", encProtocolo )[0] );	
	//Agrego el objeto clonado
	//encProtocolo.cells[1].appendChild( auxInClone );
	/************************************************************************************************/	
	
	
	switch( cmp.options[ cmp.selectedIndex ].value ){
	
		case 'prOrdenes':
			document.getElementById( "prOrdenes" ).style.display = '';
			$("#programasGenerales").tabs().show();
			$("#programasGenerales").tabs('select', 0);
			
			//Busco el tipo de protocolo que se va a crear
			buscarPorName( "slTipPro", encProTipDes )[0].value = 'prOrdenes';
			
			buscarPorName( "dat_Enctip", encProtocolo )[0].value = 'Ordenes';
			
			cargarEncDefecto();
		break;
		
		case 'prHCE':
			document.getElementById( "prHCE" ).style.display = '';
			$("#programasGenerales").tabs().show();
			$("#programasGenerales").tabs('select', 1);
			
			//Busco el tipo de protocolo que se va a crear
			buscarPorName( "slTipPro", encProTipDes )[0].value = 'prHCE';
			
			buscarPorName( "dat_Enctip", encProtocolo )[0].value = 'HCE';
			
			cargarEncDefecto();
		break;
		
		case 'ctcArts':
			
			cargarEncDefecto();
			
			buscarPorName( "dat_Encnom", encProTipDes )[0].onchange = alCambiarConsultaCodigo;
			
			document.getElementById( "ctcArts" ).style.display = '';
			$("#programasGenerales").tabs().show();
			$("#programasGenerales").tabs('select', 2);
			
			//Busco el tipo de protocolo que se va a crear
			buscarPorName( "slTipPro", encProTipDes )[0].value = 'ctcArts';
					
			$("[name=dat_Encnom]", encProTipDes ).autocomplete("protocolosOrdenesHCE.php?ajax=4&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&wcenmez="+wcenmez.value, {
				cacheLength:1,
				delay:0,
				max: 100,
				scroll: false,
				scrollHeight: 500,
				matchSubset: false,
				matchContains: true,
				width:1000,
				autoFill:false,
				minChars: 2,
				formatItem: function(data, i, n, value) {
				
					//convierto el string en json
					eval( "var datos = "+data );
					
					return datos[0].label;	//Eso es lo que se muestra al usuario
					// return value;
				},
				formatResult: function(data, value){
		//    			debugger;
					eval( "var datos = "+data )
					return datos[0].value.cod;
				}
			}).result( function(event, item ){
				
				//La respuesta es un json
				//convierto el string en formato json
				eval( "var datos = "+item );
				$("#dat_Enccodigo").val(datos[0].value.cod);
				setCtcArtEnc( datos, this );
				
				var filaNueva = document.getElementById( "tabPesCtcMedicamentos" ).rows[2];
				
				setearDatosFilaArticulosCtc( datos, buscarPorName( "dat_Detcod", filaNueva )[0] );
				
				//setearDatosFilaArticulosCtc( datos[0].pes[ indexPes ][i].cod, buscarPorName( "dat_Detcod", fila )[0] );
				
				validarDatosCTC( this );
		
				this.lastValue = datos;
				this.setDatos = function( datos, campo ){ 
					setCtcArtEnc( datos, campo );
					setearDatosFilaArticulosCtc( datos, buscarPorName( "dat_Detcod", document.getElementById( "tabPesCtcMedicamentos" ).rows[2] )[0] );
				}
			});
			
			agregarCtcArticulo();
		break;
		
		case 'ctcProcs':
		
			cargarEncDefecto();
		
			buscarPorName( "dat_Encnom", encProTipDes )[0].onchange = alCambiarConsultaCodigo;
			
			$("#programasGenerales").tabs().show();
			$("#programasGenerales").tabs('select', 3);
		
			document.getElementById( "ctcProcs" ).style.display = '';
			
			//Busco el tipo de protocolo que se va a crear
			buscarPorName( "slTipPro", encProTipDes )[0].value = 'ctcProcs';
			
			//Buscador de procedimientos para el encabezado
			$("[name=dat_Encnom]", encProTipDes ).autocomplete("protocolosOrdenesHCE.php?ajax=6&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
				cacheLength:1,
				delay:300,
				max: 100,
				scroll: false,
				scrollHeight: 500,
				matchSubset: false,
				matchContains: true,
				width:1000,
				autoFill:false,
				minChars: 3,
				formatItem: function(data, i, n, value) {
					
					//convierto el string en json
					eval( "var datos = "+data );			
					
					return datos[0].label;	//Eso es lo que se muestra al usuario
					// return value;
				},
				formatResult: function(data, value){

					eval( "var datos = "+data );						
					return datos[0].value.cod;
				}
			}).result( function(event, item ){
				
				//La respuesta es un json
				//convierto el string en formato json
				eval( "var datos = "+item );				
				$("#dat_Enccodigo").val(datos[0].value.cod);
				
				setCtcProcs( datos, this );
				
				var filaNueva = document.getElementById( "tabPesCtcProcedimientos" ).rows[2];
				
				setearDatosFilaCtcProcedimientos( datos, buscarPorName( "dat_Detcod", filaNueva )[0] );		
						
				
				validarDatosCTC( this );
		
				this.lastValue = datos;
				this.setDatos = function( datos, campo ){ 
					setCtcProcs( datos, campo );
					setearDatosFilaCtcProcedimientos( datos, buscarPorName( "dat_Detcod", document.getElementById( "tabPesCtcProcedimientos" ).rows[2] )[0] );
				}
			});
			
			agregarCtcProcedimiento();
		
		break;
		
		default: break;
	}
}

/******************************************************************
 * Pone valor por defecto al campo nombre del encabezado del protocolo
 ******************************************************************/
function setCtcProcs( datos, cmp ){
	
	if( datos ){
		cmp.value = datos[0].value.des;
		
		cmp._lastValue = cmp.value;
					
		cmp.replaceValue = datos[0].value.cod;
	}
	else{
		cmp.value = "";
		
		cmp._lastValue = "";
					
		cmp.replaceValue = "";
	}
}

/**
 *
 */
function setCtcArtEnc( datos, cmp ){
	
	if( datos ){
		cmp.value = datos[0].value.gen;
		
		cmp._lastValue = cmp.value;
					
		//Creo este campo que indica que se debe reemplazar el nombre por otro
		cmp.replaceValue = datos[0].value.cod;
	}
	else{
		cmp.value = "";
		
		cmp._lastValue = "";
					
		//Creo este campo que indica que se debe reemplazar el nombre por otro
		cmp.replaceValue = "";
	}
}

/****************************************************************************************************
 * Si se elige el protocolo CTC, ya sea de articulo o procedimiento, se valida que el encabezado
 * no exista, ya que para estos el encabezado es unico
 ****************************************************************************************************/
function validarDatosCTC( campo ){

	//verifico que todos los campos de encabezado esten llenos
	var validar = true;
	
	//Borro todos los campos de las tablas de movimiento
	var arCmp =  new Array( "SELECT", "INPUT" );
	
	for( var i = 0; i < arCmp.length && validar; i++ ){
	
		var cmps = document.getElementById( "encProtocolo" ).getElementsByTagName( arCmp[i] );
		
		for( var j = 2; j < cmps.length; j++ ){
			
			//Todos los campos a validar comienzan con dat_
			if( cmps[j].name.substr(0,4) == "dat_" && ( cmps[j].name != "dat_Encid" && cmps[j].name != "dat_Enctip" && cmps[j].name != "dat_Encest" && cmps[j].name != "dat_Encpro" ) ){
				
				//Si esta vacio un campo no busco los datos
				if( cmps[j].tagName.toLowerCase() == "select" 
					|| ( cmps[j].tagName.toLowerCase() == "input" && cmps[j].type.toLowerCase() != "checkbox" ) 
				){
					if( cmps[j].value == '' ){
						validar = false;
						break;
					}
				}
			}
		}
	}
	
	//Si todos los datos estan seteados, busco el articulo o procedimiento que requiera
	if( validar ){
	
		switch( buscarPorName( "slTipPro", document.getElementById( "encProTipDes" ) )[0].value ){
			
			case 'ctcArts':
			case 'ctcProcs':
			
				//Creo la URL
				var auxCmp = buscarPorName( "dat_Encnom", document.getElementById( "encProTipDes" ) )[0];
				
				var  auxValue = auxCmp.value;
				auxCmp.value = auxCmp.replaceValue;
				
				var parametros = cearUrlPorCampos( document.getElementById( "encProtocolo" ) );
				parametros+= "&"+cearUrlPorCampos( document.getElementById( "encProTipDes" ) );
				
				auxCmp.value = auxValue;
				
				var auxLastValue = campo.lastValue;
				
				//Busco si existe el registro
				consultasAjax( "POST", "protocolosOrdenesHCE.php?wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value+"&wcenmez="+wcenmez.value+"&ajax=14", 
					parametros, 
					true, 
					function( ajax ){
						
						//Proceso la respuesta ajax
						if( ajax.readyState==4 && ajax.status==200 ){
						
							if( ajax.responseText != '' ){
								
								eval( "var datos = " + ajax.responseText );
								
								//Se pregunta si se quiere cargar el protocolo siempre y cuando el protocolo este vació
								if( buscarPorName( "dat_Encid", document.getElementById( "encProtocolo" ) )[0].value != datos[0].value.id && 
									confirm( "El protocolo ya existe. Desea cargarlo?\nLos cambios realizados no se efecturán." ) 
								){	
									cargarDatosProtocolos( datos );
								}
								else{
									// campo.value = auxLastValue;
									// campo.lastValue = auxLastValue;
									
									campo.setDatos( auxLastValue, campo );
								}
							}
							else{
								return;
								//Si la respuesta es nula es por que no hay datos a cargar
								//Se debe borrar datos de encabezado (solo el id)
								//Esto por si ya hubo un dato cargado
								var auxCmp = buscarPorName( "dat_Encid", document.getElementById( "encProtocolo" ) )[0];
								auxCmp.value = '';
								
								//Antes de borrar los datos si ya tiene un id significa que cargo algo y por tanto
								//los campos por defecto se quedan vacios
								var auxCtcP = buscarPorName( "dat_Detid", document.getElementById( "tabPesCtcProcedimientos" ).rows[2] )[0];
								
								if( auxCtcP ){
									if( auxCtcP.value != '' ){
										var auxCmp = buscarPorName( "dat_Detjus", document.getElementById( "tabPesCtcProcedimientos" ).rows[2] )[0];
										auxCmp.value = '';
									}
									
									//Tambien en los datos de movimiento se hace igual, borrando los datos que hay por defecto
									auxCtcP.value = '';
								}
								
								
								var auxCtcM = buscarPorName( "dat_Detid", document.getElementById( "tabPesCtcMedicamentos" ).rows[2] )[0];
								
								if( auxCtcM ){
								
									if( auxCtcM.value != '' ){
										var auxCmp = buscarPorName( "dat_Detese", document.getElementById( "tabPesCtcMedicamentos" ).rows[2] )[0];
										auxCmp.value = '';
										
										var auxCmp = buscarPorName( "dat_Detete", document.getElementById( "tabPesCtcMedicamentos" ).rows[2] )[0];
										auxCmp.value = '';
									}
									
									//Tambien en los datos de movimiento se hace igual, borrando los datos que hay por defecto
									auxCtcM.value = '';
								}
							}
						}
					}
				);
				
			break;
			
			default: break;
		}
	}
}

/************************************************************************************************
 * Cuando cambia uno de los campos se llama a esta fucion
 ************************************************************************************************/
function alCambiar( campo ){

	//Si el campo esta vacio deja el último valor seleccionado
	if( campo.tagName.toLowerCase() == 'input' ){
		if( campo._lastValue ){
			campo.value = campo._lastValue;
		}
		else{
			campo.value = "";
		}
	}

	//verifico que todos los campos de encabezado esten llenos
	var validar = true;
	
	//Borro todos los campos de las tablas de movimiento
	var arCmp =  new Array( "SELECT", "INPUT" );
	
	for( var i = 0; i < arCmp.length && validar; i++ ){
	
		var cmps = document.getElementById( "encProtocolo" ).getElementsByTagName( arCmp[i] );
		
		for( var j = 2; j < cmps.length; j++ ){
			
			//Todos los campos a validar comienzan con dat_
			if( cmps[j].name.substr(0,4) == "dat_" && ( cmps[j].name != "dat_Encid" && cmps[j].name != "dat_Enctip" && cmps[j].name != "dat_Encest" && cmps[j].name != "dat_Encpro" ) ){
				
				//Si esta vacio un campo no busco los datos
				if( cmps[j].tagName.toLowerCase() == "select" 
					|| ( cmps[j].tagName.toLowerCase() == "input" && cmps[j].type.toLowerCase() != "checkbox" ) 
				){
					if( cmps[j].value == '' ){
						validar = false;
						break;
					}
				}
			}
		}
	}
	
	if( validar ){
		validarDatosCTC( campo );
	}
}

/************************************************************************************
 * Guarda el ultimo valor del campo
 ************************************************************************************/
function guardarUltimoValue( cmp ){
	cmp.lastValue = cmp.value;
}

/****************************************************************************************************
 * Evento al cargar el formulario
 ****************************************************************************************************/
window.onload = function(){
	//hago globales campos comunes
	wemp_pmla = document.getElementById( 'wemp_pmla' ); //echo "<INPUT type='hidden' id='wemp_pmla' value='$wemp_pmla'>";
	wbasedato = document.getElementById( 'wbasedato' );
	wcenmez = document.getElementById( 'wcenmez' );
	whce = document.getElementById( 'whce' );
	ordenes = document.getElementById( 'ordenes' );
	
	$("#tabsprincipal").tabs();
	//Creo los tabs correspondientes
	$("#programasGenerales").tabs(); //JQUERY:  Activa los tabs para las secciones del kardex
	$("#prgOrdenes").tabs(); //JQUERY:  Activa los tabs para las secciones del kardex
	
	$("#programasGenerales").tabs('select', -1 );
	$("#prgOrdenes").tabs('select', -1 );	
	
	//Buscador de procedimientos para el encabezado
	$( "#idCargarProtocolos" ).autocomplete("protocolosOrdenesHCE.php?ajax=13&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		autoFill:false,
		minChars: 3,
		formatItem: function(data, i, n, value) {
		
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult: function(data, value){
		
			eval( "var datos = "+data );
			
			return datos[0].value.nom;	//Eso es lo que se muestra al usuario
			// return value;
			
			//return value;
		}
	}).result( function(event, item ){
	
		eval( "var datos = "+item );
		
		this.aCargar = datos[0].value.vid;
			
		return datos[0].value.nom;	//Eso es lo que se muestra al usuario
	});
	
	$("#programasGenerales").tabs().hide();
	
	//creo un clon del select de especialidades, este clone es global
	slCloneEspecialidades = buscarPorName( "dat_Encesp", document.getElementById( "encProtocolo" ) )[0].cloneNode( true );
	
	//Cargo todos los protocolos por defecto
	buscarPorName( "dat_Encnom", document.getElementById( "bcProtocolosAvanzado" ) )[0].value = '%';
	alCambiarSearch( buscarPorName( "dat_Encnom", document.getElementById( "bcProtocolosAvanzado" ) )[0] );
	buscarPorName( "dat_Encnom", document.getElementById( "bcProtocolosAvanzado" ) )[0].value = '';	
	
	buscadorArticuloNoPos( document.getElementById( "tabCTCReemplazp" ).rows[1] )
	
	//Cargar los autocomplete y las funciones de la fila del encabezado de la fila 0
	llevarFuncionesAlEncabezado(0);
}

function llevarFuncionesAlEncabezado( indice ){
	wemp_pmla = document.getElementById( 'wemp_pmla' ); //echo "<INPUT type='hidden' id='wemp_pmla' value='$wemp_pmla'>";
	wbasedato = document.getElementById( 'wbasedato' );
	wcenmez = document.getElementById( 'wcenmez' );
	whce = document.getElementById( 'whce' );
	ordenes = document.getElementById( 'ordenes' );
	
	if( indice == undefined )
		indice=0;

	//Asigno autocompletar para la busqueda de medicos (especialistas de la salud)
	$("[name=dat_Encmed]", encProtocolo ).eq(indice).autocomplete("protocolosOrdenesHCE.php?ajax=9&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		autoFill:false,
		minChars: 3,
		formatItem: function(data, i, n, value) {
		
			//convierto el string en json
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult: function(data, value){

			eval( "var datos = "+data )
			return datos[0].value.cod;
		}
	}).result( function(event, item ){
		
		//La respuesta es un json
		//convierto el string en formato json
		eval( "var datos = "+item );
		
		this.value = datos[0].value.cod;
		
		setMed( datos, this );
		
		validarDatosCTC( this );
		
		this.lastValue = datos;
		this.setDatos = setMed;
	});
	
	//Asigno autocompletar para la busqueda de diagnosticos en el encabezado
	$("[name=dat_Encdia]", encProtocolo ).eq(indice).autocomplete("protocolosOrdenesHCE.php?ajax=8&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		autoFill:false,
		minChars: 3,
		formatItem: function(data, i, n, value) {
		
			//convierto el string en json
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult: function(data, value){

			eval( "var datos = "+data )
			return datos[0].value.cod;
		}
	}).result( function(event, item ){
		
		//La respuesta es un json
		//convierto el string en formato json
		eval( "var datos = "+item );
		
		this.value = datos[0].value.cod;
		
		setDx( datos, this );
				
		validarDatosCTC( this );
		
		this.lastValue = datos;
		this.setDatos = setDx;
	});
	
	//Asigno autocompletar para la busqueda de tratamientos en el encabezado
	$("[name=dat_Enctra]", encProtocolo ).eq(indice).autocomplete("protocolosOrdenesHCE.php?ajax=19&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		autoFill:false,
		minChars: 3,
		formatItem: function(data, i, n, value) {
		
			//convierto el string en json
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult: function(data, value){

			eval( "var datos = "+data )
			return datos[0].value.cod;
		}
	}).result( function(event, item ){
		
		//La respuesta es un json
		//convierto el string en formato json
		eval( "var datos = "+item );
		
		this.value = datos[0].value.cod;
		
		setDx( datos, this );
				
		validarDatosCTC( this );
		
		this.lastValue = datos;
		this.setDatos = setDx;
	});
	
	//Asigno autocompletar para la busqueda de diagnosticos en el encabezado
	$("[name=dat_Enccio]", encProtocolo ).eq(indice).autocomplete("protocolosOrdenesHCE.php?ajax=16&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+whce.value, {
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		autoFill:false,
		minChars: 3,
		formatItem: function(data, i, n, value) {
		
			//convierto el string en json
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult: function(data, value){

			eval( "var datos = "+data )
			return datos[0].value.cod;
		}
	}).result( function(event, item ){
		
		//La respuesta es un json
		//convierto el string en formato json
		eval( "var datos = "+item );
		
		this.value = datos[0].value.cod;
		
		setDx( datos, this );
				
		validarDatosCTC( this );
		
		this.lastValue = datos;
		this.setDatos = setDx;
	});
	
	//Creo función para devolver datos
	buscarPorName( "dat_Encesp", document.getElementById( "encProtocolo" ) )[indice].setDatos = function( datos, campo ){
		campo.value = datos;
	};
	
	//Creo función para devolver datos
	buscarPorName( "dat_Enccco", document.getElementById( "encProtocolo" ) )[indice].setDatos = function( datos, campo ){
		campo.value = datos;
	};

}

function alCambiarSearch( campo ){
	
	if( !campo.timeSearchIni )
		campo.timeSearchIni = new Date();
	
	if( ( campo.type && campo.type.toLowerCase() == 'checkbox' ) || campo.value.length >=  2 || campo.value == '*' || campo.value == '%' ){
		//Creo la url para buscar los protocolos segun los parametros ingresado
		var urlDatosArt = cearUrlPorCampos( document.getElementById( "bcProtocolosAvanzado" ) );
									
		var parametros = urlDatosArt;
		
		//hago la grabacion por ajax del articulo
		consultasAjax( "POST", "protocolosOrdenesHCE.php?wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&ajax=15"+"&wcenmez="+wcenmez.value+"&whce="+whce.value, 
						parametros, 
						true, 
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								//Esta función llena los datos del protocolo
								document.getElementById( "resultSearch" ).innerHTML = ajax.responseText;
							}
						}
					);
	}
	
	campo.timeSearchIni = new Date();
}

/***************************************************************************
 * Se mueve a la pestaña editor o buscador
 ***************************************************************************/
function moBuscador(){
	
	/*var moProtocolo = document.getElementById( "bcProtocolosAvanzado" );

	if( moProtocolo.style.display == 'none' ){
		moProtocolo.style.display = '';
		document.getElementById( "btBuscador" ).innerHTML = 'Ir a editar protocolos';
		dvEditorProtocolos.style.display = 'none';
		document.getElementById( "btNuevoProtocolo" ).style.display = 'none';
	}
	else{
		moProtocolo.style.display = 'none';
		document.getElementById( "btBuscador" ).innerHTML = 'Buscar protocolos';
		dvEditorProtocolos.style.display = '';
		document.getElementById( "btNuevoProtocolo" ).style.display = '';
	}*/
	
	var tabActivo = $( "#tabsprincipal" ).tabs( "option", "active" );
	if( tabActivo == 0 ){
		$( "#tabsprincipal" ).tabs( "option", "active", 1 );
	}else{
		$( "#tabsprincipal" ).tabs( "option", "active", 0 );
	}

}

function soloEnteros(campo, e){

	var keynum;
	var keychar;
	var numcheck;
	
	if(window.event){ 	/*/ IE*/
		keynum = e.keyCode
	}
	else if(e.which){ 	/*/ Netscape/Firefox/Opera/*/
		keynum = e.which
	}

	if( (keynum>=35 && keynum<=37) ||keynum==8||keynum==9||keynum==46||keynum==39 ){
	
		if( keynum == 46 ){
			if( campo.value.indexOf( '.' ) > -1 ){
				return false;
			}
			else{
				return true;
			}
		}
		
		return true;
	}

	if( (keynum>=48&&keynum<=57) ){
		return true;
	}
	else{
		return false;
	}
}

/********************************************************************************
 * setea el campo especialista de la salud como todos
 ********************************************************************************/
function setTodosMed(cmp){
	var fila = cmp.parentNode.parentNode;
	//Busco el campo correcto que se va a setear
	var campo = buscarPorName( "dat_Encmed", fila )[0];

	if( campo.value != '*' ){
		var datos = [{value:{cod:"*", des:"Todos"}}];
			
		setMed( datos, campo );
		
		validarDatosCTC( campo );
	}
}

/********************************************************************************
 * Setea el campo de diagnostico como todos
 ********************************************************************************/
function setTodosEsp(cmp){
	var fila = cmp.parentNode.parentNode;
	var campo = buscarPorName( "dat_Encdia", fila )[0];
	
	if( campo.value != '*' ){
		var datos = [{value:{cod:"*", des:"Todos"}}];
		
		setDx( datos, campo );
		
		validarDatosCTC( campo );
	}
	
}

/********************************************************************************
 * Setea el campo de tratamiento como todos
 ********************************************************************************/
function setTodosTra(cmp){
	var fila = cmp.parentNode.parentNode;
	var campo = buscarPorName( "dat_Enctra", fila )[0];
	
	if( campo.value != '*' ){
		var datos = [{value:{cod:"*", des:"Todos"}}];
		
		setDx( datos, campo );
		
		validarDatosCTC( campo );
	}
	
}

/************************************************************************************
 * Marzo 19 de 2013
 *
 * Setea en todos el campo CIE-O
 ************************************************************************************/
function setTodosCcio(cmp){
	var fila = cmp.parentNode.parentNode;
	var campo = buscarPorName( "dat_Enccio", cmp )[0];
	
	if( campo.value != '*' ){
		var datos = [{value:{cod:"*", des:"Todos"}}];
		
		setDx( datos, campo );
		
		validarDatosCTC( campo );
	}
}

/**
 *
 */
function eliminarArtReemplazo( cmp ){

	var fila = cmp.parentNode.parentNode;

	setearDatosFilaArticulosReemplazar( [{value:{cod:"", uni:"", com: ""}}],  buscarPorName( "dat_Mreart", fila )[0] );
	
	buscarPorName( "dat_Mrepos", fila )[0].value = "";
	buscarPorName( "dat_Mrepre", fila )[0].value = "";
	buscarPorName( "dat_Mreddi", fila )[0].value = "";
	buscarPorName( "dat_Mrecan", fila )[0].value = "";
	buscarPorName( "dat_Mretto", fila )[0].value = "";
	buscarPorName( "dat_Mreest", fila )[0].value = "off";
}

function agregarFilaEnc(){

	//Se reemplaza el código para agregar una nueva fila
	var filaNueva = $("#tabla_editor tr:last").clone(false);
	filaNueva = filaNueva[0];
	tabla_editor.tBodies[0].appendChild( filaNueva );
	filaNueva.style.display = '';
	//Despues de insertar la fila se agrega la funcion de busqueda por autocomplete para todos los campos correspondientes
	//buscadorTabHCE( filaNueva );
	var indiceFilaNueva = $(".filaEnc").length;
	indiceFilaNueva--;
	//console.log("cargardefecto con:"+indiceFilaNueva);
	cargarEncDefecto(indiceFilaNueva);
	$(".filaEnc").eq( indiceFilaNueva ).removeClass( "fondorojo" );
	llevarFuncionesAlEncabezado(indiceFilaNueva);
}

function eliminarEnc(cmp){
	
	$.post('protocolosOrdenesHCE.php', 
		{  	  wbasedato: $( "#wbasedato" ).val(), 
				     id: $( "[name=dat_Encid]", jQuery(cmp).parent().parent() ).val(),
				   ajax: 21,
		   consultaAjax: ''
		},
		function( data ){
			var input = document.createElement("input");
			input.type = "text";
			input.value= "%";
			alCambiarSearch( input );
			nuevoPrgOrdenes();
			moBuscador();
		}, 
		"json" 
	);	//fin post
	
	if( $(".filaEnc").length > 1 ){
		cmp = jQuery(cmp);
		cmp.parent().parent().remove();
	}
}

function isEmpty(obj) {	
	if (typeof obj == 'undefined' || obj === null || obj === '') return true;
	
	if (typeof obj == 'number' && isNaN(obj)) return true;
	
	if (obj instanceof Date && isNaN(Number(obj))) return true;
	
	return false;		
}
	
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