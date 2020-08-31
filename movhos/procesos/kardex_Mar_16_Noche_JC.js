/************************************************************************************************************************************************
 * Modificaciones
 * Marzo 16 de 2011 (Edwin M)	Se agrega control de horas para el calendario en medicamentos. La hora de inicio de un medicamento no puede ser inferior a dos horas.
 * Marzo 14 de 2011	(Edwin M)	Se agrega mensaje que muestra los medicamentos mal creados si se cierra por la X de la ventana
 * Marzo 10 de 2011	(Edwin M)	Se oculta la barra de titulo para el kardex, esto obliga a cerrar por el boton grabar Kardex.
 * Marzo 8 de 2011	(Edwin M)	Si el articulo se suspende y se puede confirmar deja de titilar
 * Marzo 7 de 2011	(Edwin M)	Para los medicamentos que se pueden confirmar y no estan confirmados, la fila parapdea (efecto blink).
 * 							    Se corrige la seleccion de vias al agregar un medicamento
 * Enero 12 de 2011 (Edwin M)	Se agrega control de mensajes al momento de realizar un reemplazo
 * Enero 05 de 2011	(Edwin M)	Se modifica el reemplazo del articulo en el perfil para articulos genericos. Cuando se realiza un reemplazo
 * 								en el perfil de un medicamento generico, se cambia la dosis a aplicar del articulo generico por el nuevo.
 * 14.May.10 - (Msanchez):  Corrección de la modificación del ultimo articulo con prioridad del perfil para priorizar.
 * 18.May.10 - (Msanchez):  Cambio para manipular las cantidades de CTC
 * 21.Sep.10 - (Msanchez):  Validacion de hora cero del dia actual
 ************************************************************************************************************************************************/
var grabando=false;

//Array doble esto es para validar los medicamentos y poder sacar el mensaje si el usuario cierra con la x
//[][0]	Codigo del articulo
//[][1] NOmbre del articulo
var articulosSinGrabar = Array();	//Indica que ariculos no se han grabado correctamente
var noMostarMsjError = false;

/******************************************************************************************************************************
 * Control de seleccion de fechas. Evita que se selecciona una fecha y hora antes de la fecha actual y antes de dos horas.
 * @param date
 * @param stringFecha
 * @return
 * 
 * Creado: Marzo 16 de 2011
 ******************************************************************************************************************************/
function alSeleccionarFecha( date, stringFecha ){

//	return;
	var now = new Date();
	
	var comparacion = compareDatesOnly(now, date.currentDate);
	
	var hours = date.currentDate.getHours();
	
	//Limita que no se seleccionen dias anteriores
	if (comparacion < 0) {
		desactivar = true;
	}
	
	var ano = date.currentDate.getYear();
	var mes = date.currentDate.getMonth();
	var dia = parseInt(date.currentDate.getDate())+1;
	var hora = date.currentDate.getHours();
	
	//Limita que no se seleccionen horas anteriores
	if (typeof(hours) != "undefined"){
		if ( (hours <= now.getHours() && now.getHours()-hours > 2 ) && comparacion == 0 ) {
			date.setDate( new Date(ano,mes,dia,hora,0,0) );
			alert( "La hora no esta permitida para la fecha seleccionada" );
			return true;
		}
	}
	
	if( date.dateClicked ){
		date.params.inputField.value = date.currentDate.print(date.params.ifFormat);
		date.callCloseHandler();
	}
}

function registrarUsuarioQueCierra(  ){
	
	var usuario = document.getElementById( "usuario" ).value
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
		
	parametros = "consultaAjaxKardex=28&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&his="+historia+"&ing="+ingreso+"&usu="+usuario; 

	try{
//		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
//		alert( ajax.responseText );
		
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

/******************************************************************************
 * Se hace un efecto de pulso para que la fila parpadee
 * - Esto aplica para todas la tablas en la que se muestra los medicamentos
 * 
 * Marzo 7 de 2011
 ******************************************************************************/
function inicializarPulso(){
	
	var numCelda = 8;
	var numCeldaNombre = 1;
	
	//Creo el array con las tablas disponibles que permiten el parpadeo
	var tbTablas = Array( "tbDetalleN", "tbDetalleQ", "tbDetalleA" );
	
	for( var i = 0; i < tbTablas.length; i++ ){
		
		var tb = document.getElementById( tbTablas[i] );
		
		if( tb ){
		
			for( var numFila = 1; numFila < tb.rows.length; numFila++ ){
				
				if( tb.rows[ numFila ].className.toLowerCase() != "suspendido" ){	//Marzo 8 de 2011
				
					var ckButton = tb.rows[ numFila ].cells[ numCelda ].firstChild;
					
					if( ckButton && ckButton.tagName ){
						
						//guradando el nombre de la clase original
						//Se agrega atributo originalClass al campo
						if( !tb.rows[ numFila ].cells[numCeldaNombre].originalClass && tb.rows[ numFila ].cells[numCeldaNombre].className != '' ){
							tb.rows[ numFila ].cells[numCeldaNombre].originalClass = tb.rows[ numFila ].cells[numCeldaNombre].className 
						}
						
						if( !ckButton.disabled && !ckButton.checked ){
							
							if( tb.rows[ numFila ].cells[numCeldaNombre].className == tb.rows[ numFila ].cells[numCeldaNombre].originalClass ){
								
								tb.rows[ numFila ].cells[numCeldaNombre].className = "fondoAlertaConfirmar";
								tb.rows[ numFila ].cells[numCelda].className = "fondoAlertaConfirmar";
							}
							else{
								tb.rows[ numFila ].cells[numCeldaNombre].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
								tb.rows[ numFila ].cells[numCelda].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
							}
						}
						else{
							tb.rows[ numFila ].cells[numCeldaNombre].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
							tb.rows[ numFila ].cells[numCelda].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
						}
					}
				}
				else{
					tb.rows[ numFila ].cells[numCeldaNombre].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
					tb.rows[ numFila ].cells[numCelda].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
				}
			}
		}
	}
}


/*****************************************************************************************************************************
 * Inicializa jquery
 ******************************************************************************************************************************/
function inicializarJquery(){
	
	setInterval( "inicializarPulso()", 500 ); //Marzo 7 de 2011
	
	$("#tabs").tabs({ fx: {opacity: 'toggle' }, select: function(event, ui) { if(fixedMenu2 && fixedMenu2 != 'undefined') fixedMenu2.hide(); } }); //JQUERY:  Activa los tabs para las secciones del kardex
	
	$("#tabs").tabs('select', 2);
	 	
	//Para ocultar los medicamentos anteriores
	$("#btnOcultar").click(function () {
      $("#medAnt").toggle("slow");
    }); 

    if(document.getElementById("fixeddiv")){
    	$("#fixeddiv").draggable();
    }	
     
     if(document.getElementById("fixeddiv2")){
    	 $("#fixeddiv2").draggable();
     }

     //Ciclo para colocar en funcionamiento los tooltips
     var cont1 = 0, cont2 = 0;
     var protocolos = new Array('N','A','U','Q');
     var tipo = "";

     while(cont2 < protocolos.length){
    	 tipo = protocolos[cont2];
	     while(document.getElementById("tr"+tipo+cont1)){
    		 $('#tr'+tipo+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
    		 cont1++;
     	}
	    cont1 = 0;
	    cont2++;
     }

   //Simpletree Arbol 
 	var simpleTreeCollection = $('.simpleTree').simpleTree({
 		autoclose: true,
 		drag: false,
 		afterClick:function(node){
 			var elemento = $('span:first',node);
 			
 			var indice = elemento.parent().attr("id");
 			var texto = elemento.text();

 			/*Si el elemento seleccionado es una hoja proceso la peticion, una rama no tiene efecto en este caso
 			* Esto se logra con el id
 			*/
 			if(indice.substring(0,1) == "H"){
 				seleccionHojaAccionesComplementarias(indice,texto);
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
 	
    enfocarInicio();

    if(document.getElementById("whgrabado") && document.getElementById("whgrabado").value != ''){
    	$.growlUI('','Se ha grabado el kardex');
    }
    
    $("#tabs").css('display','block');
    $("#msjInicio").css('display','none');
}
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function intercalarExamenAnterior(idElemento){
    $("#ex"+idElemento).toggle("normal");
}  
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function intercalarMedicamentoAnterior(idElemento,tipoProtocolo){
	$("#med"+tipoProtocolo+idElemento).toggle("normal");
}
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function confirmarGeneracion(){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var whgrabado = document.forms.forma.whgrabado;
	
	if(historia && ingreso){
		
		//Deshabilita el boton de generacion
		var boton = document.getElementById("btnConfirmar");
		if(boton){
			boton.disabled = true;
			boton.value = "Procesando, por favor espere...";
		}
		
		if(whgrabado && whgrabado.value != ''){
			document.location.href = 'generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha+'&whgrabado='+whgrabado.value+'&editable='+document.forms.forma.editable.value;
		} else {
			document.location.href = 'generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha+'&editable='+document.forms.forma.editable.value;
		}
	} else {
		alert("No se encontró historia, ingreso y fecha en los parametros de entrada.");		
	}
}
/******************************************************************************************************************************
 *Redirecciona a la pagina inicial del kardex
 ******************************************************************************************************************************/
function inicio(servicio){
	var esEditable = document.forms.forma.editable;
	
	if(document.getElementById('wthistoria')){
		document.getElementById('wthistoria').value = '';	
	}
	
	if(esEditable && esEditable.value != ''){
		document.location.href='generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&editable='+document.forms.forma.editable.value+'&wsservicio='+servicio;
	} else {
		document.location.href='generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&wsservicio='+servicio;
	}
}
/******************************************************************************************************************************
 *Enfoca al campo inicial
 ******************************************************************************************************************************/
function enfocarInicio(){
	if(document.getElementById("wthistoria")){
		document.getElementById("wthistoria").focus();
	}
}
/******************************************************************************************************************************
 * Marca TODOS los articulos como aprobados
 ******************************************************************************************************************************/
function marcarAprobacionArticulos(estado){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;
	var estadoAprobacion = estado ? "on" : "off";
	
	var codigosArticulos = "";
	
	for(var indice = 1;document.getElementById('wchkare'+indice);indice++){
		if(document.getElementById('wchkare'+indice).disabled == false){
			document.getElementById('wchkare'+indice).checked = estado;
		
			//Extrae el codigo del articulo actual 
		 	var codigoArticulo = document.getElementById('wnmmedact'+indice);
		 	var cd = "";
		 	if(codigoArticulo.tagName == 'INPUT'){
		 		cd = codigoArticulo.value.split("-");
		 	} else {
		 		cd = codigoArticulo.innerHTML.split("-");
		 	}
		 	codigosArticulos += cd[0]+";"+cd[1]+";"+document.getElementById('whfinicio'+indice).value.split(" a las:")[0]+";"+document.getElementById('whfinicio'+indice).value.split(" a las:")[1]+"|";
		}
	}
	grabarEstadoAprobacionArticulos(historia,ingreso,fecha,codigosArticulos,estadoAprobacion,usuario);
}
/******************************************************************************************************************************
* Marca un los articulo como aprobado
******************************************************************************************************************************/
function marcarAprobacionArticulo(idxElemento){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;
	var estadoAprobacion = document.getElementById('wchkare'+idxElemento).checked ? "on" : "off";
	
	var codigosArticulos = "";
	
	//Extrae el codigo del articulo actual 
 	var codigoArticulo = document.getElementById('wnmmedact'+idxElemento);
 	var cd = "";
 	
 	if(codigoArticulo.tagName == 'INPUT'){
 		cd = codigoArticulo.value.split("-");
 	} else {
 		cd = codigoArticulo.innerHTML.split("-");
 	}
 	codigosArticulos = cd[0]+";"+cd[1]+";"+document.getElementById('whfinicio'+idxElemento).value.split(" a las:")[0]+";"+document.getElementById('whfinicio'+idxElemento).value.split(" a las:")[1]+"|";
 	
	grabarEstadoAprobacionArticulos(historia,ingreso,fecha,codigosArticulos,estadoAprobacion,usuario);
}
/******************************************************************************************************************************
 *Valida que la diferencia de fechas entre el servidor y el cliente no sea mas de media hora
 ******************************************************************************************************************************/
function validarFechayHoraLocal(strFechaServidor,strHoraServidor){
	var valido = true;
	var fechaObj = new Date();
	var fechaJs = new Date();
	
	var fechaObjSvr = strFechaServidor.split("-");
	
	fechaObj.setFullYear(fechaObjSvr[0]);
	fechaObj.setMonth(eval(fechaObjSvr[1]-1)); 
	fechaObj.setDate(fechaObjSvr[2]);
	
	var horaObj = strHoraServidor.split(":");
	
	fechaObj.setHours(horaObj[0], horaObj[1], horaObj[2], 0); 
	
	var diferencia = fechaObj.getTime() - fechaJs.getTime();
	var minutosDiferencia = Math.floor( diferencia / (1000 * 60));
	
	if(Math.abs(minutosDiferencia) > 30){
		valido = false;
	}

	if(!valido){
		alert("Recuerde que la fecha y hora del equipo local deben ser las actuales.  Son " + Math.abs(minutosDiferencia) + " minutos de diferencia");
	}
}
/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
function consultarKardex(){
	var historia = document.forms.forma.whistoria.value;
	var esFechaValida = esFechaMenorIgualAActual(document.forms.forma.wfecha.value);
	var whgrabado = document.getElementById("whgrabado");

	//Digitó historia
	if(!historia || historia == ''){
		alert("Debe especificar una historia clínica");
		return;
	} 

	if(esFechaValida){
		if(whgrabado && whgrabado.value != ''){
			document.location.href = 'generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia+'&wfecha='+document.forms.forma.wfecha.value+'&whgrabado='+whgrabado.value+'&editable='+document.forms.forma.editable.value;
		} else {
			document.location.href = 'generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia+'&wfecha='+document.forms.forma.wfecha.value+'&editable='+document.forms.forma.editable.value;
		}
	} else {
		alert("La fecha ingresada debe ser igual o anterior a la fecha actual");
	}
}
/*****************************************************************************************************************************
 *Detecta el cierre de la ventana del kardex para evitar que se pierdan los datos
 ******************************************************************************************************************************/
function salida(){
	if(document.getElementById("fixeddiv2")){
		if(!document.getElementById("wconfdisp").checked){
			//alert("Se grabará y cerrará el kardex, tenga en cuenta que este NO HA SIDO confirmado.");
		}
		
		registrarUsuarioQueCierra();
		
		/******************************************************************************
		 * Marzo 15 de 2011 
		 ******************************************************************************/
		
		$.blockUI({ message: $('#msjEspere') });
		
		var msj = "";
		
		noMostarMsjError = true;
		var a = grabarKardexSalida();
		noMostarMsjError = false;
		grabando = false;
		
		$.unblockUI();		
		
		switch(a){
			case 1:
				if( articulosSinGrabar.length > 0 ){
					
					msj = "¡¡¡ *** A T E N C I O N *** !!!\n\n";
					msj += "No se grabarán los siguientes articulos\n";
					msj += "por que no se llenaron todos los datos correctamente\n\n";
					
					for( var i = 0; i < articulosSinGrabar.length; i++ ){
						if( articulosSinGrabar[i] != "" ){
							msj += articulosSinGrabar[i]+"\n";
						}
						else{
							msj += "Articulo sin definir."+"\n";
						}
					}
					
					msj += "\nSELECCIONE CANCELAR PARA CORREGIR";
				}
				break;
				
			case 2:
				msj = "¡¡¡ *** A T E N C I O N *** !!!\n\n";
				msj += "LOS DATOS NO ESTAN CORRECTOS PARA LIQUIDOS ENDOVENOSOS\n";
				msj += "\nSELECCIONE CANCELAR PARA CORREGIR";
				break;
			
			case 3:
				msj = "¡¡¡ *** A T E N C I O N *** !!!\n\n";
				msj += "LOS DATOS NO ESTAN CORRECTOS PARA EXAMENES\n";
				msj += "\nSELECCIONE CANCELAR PARA CORREGIR";
				break;
			
			default:
				var msj = "--DEBE SALIR DESPUES DE CONFIRMAR Y GRABAR KARDEX--**HAGA CLICK EN CANCELAR**";
		}
		
		return msj;
		/******************************************************************************/
		
	}
}
/*****************************************************************************************************************************
 *Detecta el cierre de la ventana del kardex para evitar que se pierdan los datos
 ******************************************************************************************************************************/
function marcarKardexConfirmado(){
	var confirmadoArriba = document.getElementById("wcconf");
	var confirmadoAbajo = document.getElementById("wconfdisp");
	
	if(confirmadoArriba){
		confirmadoAbajo.checked = confirmadoArriba.checked;
	}
}
/*****************************************************************************************************************************
 * Comparacion de fechas anteriores para calendario
 ******************************************************************************************************************************/
 function compareDatesOnly(date1, date2) {
		var year1 = date1.getYear();
		var year2 = date2.getYear();
		var month1 = date1.getMonth();
		var month2 = date2.getMonth();
		var day1 = date1.getDate();
		var day2 = date2.getDate();

		if (year1 > year2) {
			return -1;
		}
		if (year2 > year1) {
			return 1;
		}

		//years are equal
		if (month1 > month2) {
			return -1;
		}
		if (month2 > month1) {
			return 1;
		}

		//years and months are equal
		if (day1 > day2) {
			return -1;
		}
		if (day2 > day1) {
			return 1;
		}

		//days are equal
		return 0;


		/* Can't do this because of timezone issues
		var days1 = Math.floor(date1.getTime()/Date.DAY);
		var days2 = Math.floor(date2.getTime()/Date.DAY);
		return (days1 - days2);
		*/
	}
/*****************************************************************************************************************************
 * FUNCIONES Y METODOS
 ******************************************************************************************************************************/
 /*****************************************************************************************************************************
 * Invocación generica del calendario para fecha inicial de suministro
 ******************************************************************************************************************************/
function calendario(idx,tipoProtocolo){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicio'+tipoProtocolo+idx,button:'btnFecha'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});	//Marzo 16 de 2011	
}
/*****************************************************************************************************************************
 * Invocación generica del calendario para fecha final de suministro
 ******************************************************************************************************************************/
function calendario2(idx){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'24',electric:false,inputField:'wffinal'+idx,button:'btnFechaFin'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
}
/*****************************************************************************************************************************
 * Invocacion generica del calendario para la fecha de realización examen
 ******************************************************************************************************************************/
function calendario3(idx){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfsol'+idx,button:'btnFechaSol'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',dateStatusFunc:fechasDeshabilitadas});	
}
/*****************************************************************************************************************************
 * Invocacion generica del calendario para la fecha de realización examen
 ******************************************************************************************************************************/
function calendario4(idx){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfliq'+idx,button:'btnFechaLiq'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',dateStatusFunc:fechasDeshabilitadas});	
}
/*****************************************************************************************************************************
 * Detiene la ejecucion de un evento
 ******************************************************************************************************************************/
function stopEvent(e){
    if (!e) e = window.event;
    try{
    	if (e.stopPropagation) {
    		e.stopPropagation();
    		e.preventDefault();
    	} else {
    		e.returnValue = false;
    	}
    }catch(e){}
}
/*****************************************************************************************************************************
  * Invocacion generica del calendario para la fecha de realización examen
  ******************************************************************************************************************************/
function fechasDeshabilitadas(date, year, month, day, hours, minutes){
//	debugger;
	var now = new Date();
	var desactivar = false;
	var comparacion = compareDatesOnly(now, date);
	
	//Limita que no se seleccionen dias anteriores
	if (comparacion < 0) {
		desactivar = true;
	}
	
	var ano = now.getYear();
	var mes = now.getMonth();
	var dia = now.getDay();
	
	//Limita que no se seleccionen horas anteriores
	if (typeof(hours) != "undefined"){
//		debugger;
//		if ((hours <= now.getHours()) && comparacion < 0) {
		if ((hours <= now.getHours() && now.getHours()-hours > 2 ) && comparacion <= 0) {
//			date.currentDate.setHours( hours+2 );
//			date.setDate( new Date(2011,2,13 ) );
		}
	}
	
//	if( compareDatesOnly( new Date( 2010, 10, 5 ), date) == 0 ){
//		desactivar = false;
//	}
	
	return desactivar;
} 
/*****************************************************************************************************************************
 * Validacion de que las fechas y horas del servidor y del cliente no difieran mas de media hora
 ******************************************************************************************************************************/
function validarDiferenciaFechasServidorCliente(fechaServidor, horaServidor){
	
	var valido = true;
	var fechaObj = new Date();
	var fechaJs = new Date();
	
	var fechaObjSvr = fechaServidor.split("-");
	
	fechaObj.setFullYear(fechaObjSvr[0]);
	fechaObj.setMonth(eval(fechaObjSvr[1]-1)); 
	fechaObj.setDate(fechaObjSvr[2]);
	
	var horaObj = horaServidor.split(":");
	
	fechaObj.setHours(horaObj[0], horaObj[1], horaObj[2], 0); 
	
	var diferencia = fechaObj.getTime() - fechaJs.getTime();
	var minutosDiferencia = Math.floor( diferencia / (1000 * 60));
	
	if(Math.abs(minutosDiferencia) > 30){
		valido = false;
	}
	return valido;
}

/*****************************************************************************************************************************  
 * Carga de archivo de imagen de formula medica
 ******************************************************************************************************************************/
function cargarArchivo(){
	var formulario = document.getElementById('file_upload_form');
	var vinculo = document.getElementById('lkRuta');
		
	if(formulario){
		formulario.target = "upload_target";
		if(vinculo){
			vinculo.innerHTML = "";
		}
	}
	formulario.submit();
}
/*****************************************************************************************************************************
 * Captura de onEnter con llamada a funcion parametrizada con validacion de numeros entera
 ******************************************************************************************************************************/
function teclaEnterEntero(e,accion){
	var respuesta = validarEntradaEntera(e);
	var tecla = (document.all) ? e.keyCode : e.which;
	
	if(respuesta && tecla==13){
		eval(accion);	
//		this[accion]();
	}	
	return respuesta;
}
 /*****************************************************************************************************************************
  * Captura de onEnter con llamada a funcion parametrizada
  ******************************************************************************************************************************/
 function teclaEnter(e,accion){
	var respuesta = true;
 	var tecla = (document.all) ? e.keyCode : e.which;
 	
 	if(tecla==13){
 		eval(accion);	
// 		this[accion]();
 	}	
 	return respuesta;
 }
/*****************************************************************************************************************************
 * Seleccion de un componente para adicionarse a la infusión
 ******************************************************************************************************************************/
function seleccionReemplazo(valor){
	if(valor != null){
		elementosDetalle = valor.toString();
		fixedMenu.show();
	}else {
		alert("No se capturó identificador de medicamento a modificar");
	}
}
/*****************************************************************************************************************************
 * Seleccion de un elemento del arbol de acciones complementarias
 * Las ramas hasta el momento son:
 * 
 * 1.Sondas cateteres y drenes  - Codigo 01 	
 * 2.Cuidados de enfermeria		- Codigo 02
 * 3.Aislamientos				- Codigo 03
 * 4.Curaciones					- Codigo 04
 * 5.Terapias					- Codigo 05
 * 6.Interconsultas				- Codigo 06
 ******************************************************************************************************************************/
function seleccionHojaAccionesComplementarias(codigo,descripcion){
	
	//Blanquear color del foco
	document.getElementById("txtSondas").style.background = "#FFFFFF";
	document.getElementById("txCuidados").style.background = "#FFFFFF";
	document.getElementById("txAislamientos").style.background = "#FFFFFF";
	document.getElementById("txtCuraciones").style.background = "#FFFFFF";
	document.getElementById("txTerapia").style.background = "#FFFFFF";
	document.getElementById("txtInterconsulta").style.background = "#FFFFFF";
	
	var elemento = "";
	var nodos = codigo.substring(1).split("-");
	var separador = "*";
	
	switch (nodos[0]){
		case '01':
			elemento = document.getElementById("txtSondas");
			break;
		case '02':
			elemento = document.getElementById("txCuidados");
			break;
		case '03':
			elemento = document.getElementById("txAislamientos");
			break;
		case '04':
			elemento = document.getElementById("txtCuraciones");
			break;
		case '05':
			elemento = document.getElementById("txTerapia");
			break;
		case '06':
			elemento = document.getElementById("txtInterconsulta");
			break;
		default:
			break;
	}
	
	if(elemento){
		//Resalto el color de fondo del elemento
		elemento.style.background = "#CCFFCC";
		
		if(elemento.value.indexOf(descripcion)==-1){
			elemento.value += "\n\r" + separador + descripcion;
		}
	}
}
/*****************************************************************************************************************************
 * Seleccion de un componente para adicionarse a la infusión
 ******************************************************************************************************************************/
function expandirRama(objeto,codigo){
	//Blanquear color del foco
	document.getElementById("txtSondas").style.background = "#FFFFFF";
	document.getElementById("txCuidados").style.background = "#FFFFFF";
	document.getElementById("txAislamientos").style.background = "#FFFFFF";
	document.getElementById("txtCuraciones").style.background = "#FFFFFF";
	document.getElementById("txTerapia").style.background = "#FFFFFF";
	document.getElementById("txtInterconsulta").style.background = "#FFFFFF";
	
	objeto.style.background = "#CCFFCC";
	
	var elemento = document.getElementById(codigo);
	
	if(elemento){
		var hijos = elemento.children;
		var imagen = hijos[0];
		imagen.click();
	}
}
/*****************************************************************************************************************************
 * Apertura del movimiento de articulos
 ******************************************************************************************************************************/
function abrirMovimientoArticulos(tipoProtocolo){
	limpiarBuscador();
	return fixedMenu2.show(tipoProtocolo);
}
/*****************************************************************************************************************************
 * Seleccion de un componente para adicionarse a la infusión
 ******************************************************************************************************************************/
function seleccionarComponente(codigo, nombre){
	if(cuentaInfusiones > 0){
		var idx = cuentaInfusiones-1;
		var elemento = document.getElementById("wtxtcomponentes"+idx);
		if(elemento){		
		
			//Valido que el componente no se encuentre previamente en la infusion
			var existe = false;
			
			if(!existe){
				var infusion = document.getElementById('wtxtcomponentes' + idx);
  				var componente = document.createElement('option');
				
				componente.setAttribute('value',codigo);
				if(esIE){
					componente.setAttribute('text',nombre.replace(/_/g," "));
				} else {
					componente.innerHTML = nombre.replace(/_/g," ");
				}
				
				try {
    				infusion.add(componente, null); //No es IE
  				}catch(ex){
    				infusion.add(componente); //IE
  				}
			} else {
				alert('El articulo ya se encuentra en la lista o no es duplicable');
			}
		}else{
			alert('No se encontro elemento a agregar.');
		}
	}else{
		alert('Aun no ha agregado el primer articulo');
	}
}
function seleccionHabitacionPaciente(){
	var nroHistoria = document.getElementById('wselhab') ? document.getElementById('wselhab').value : '';

	if(nroHistoria && nroHistoria != ''){
		if(document.getElementById('whhistoria')){
			document.getElementById('whhistoria').innerHTML = nroHistoria;
		}
//		consultarKardex();
	}
}
/*****************************************************************************************************************************
*
******************************************************************************************************************************/
function grabarEstadoAprobacionArticulos(historia,ingreso,fecha,codigosArticulos,estadoAprobacion,codUsuario){
	var parametros = ""; 
				
	parametros = "consultaAjaxKardex=26&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha+"&codigosArticulos="+codigosArticulos
				+"&codUsuario="+codUsuario+"&estadoAprobacion="+estadoAprobacion;
		
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200){
				if(ajax.responseText == "1"){
					$.unblockUI();
					$.growlUI('','Aprobación/desaprobación realizada exitosamente');
				}
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){}	
}
/*****************************************************************************************************************************
 *Consulta las historias y habitaciones de acuerdo a un servicio
 ******************************************************************************************************************************/
function consultarHabitaciones()
{
	var contenedor = document.getElementById('cntHabitacion');
	var parametros = ""; 
				
	parametros = "consultaAjaxKardex=25&basedatos="+document.forms.forma.wbasedato.value+"&servicio=" + document.getElementById('wsservicio').value; 
		
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
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
/*****************************************************************************************************************************
 * 
 *****************************************************************************************************************************/
function irAKardex(historia){
	document.forms.forma.wthistoria.value = historia;
	consultarKardex();	
}
 /*****************************************************************************************************************************
  * Consultar medicos por especialidad
  *****************************************************************************************************************************/
function consultarMedicosEspecialidad(){
	var codEspecialidad = document.getElementById("wselesp").value;
	
	var parametros = "";
	
	parametros = "consultaAjaxKardex=19&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&especialidad="+codEspecialidad;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function() 
		{
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				var col = document.getElementById("cntSelMedicos");
				col.innerHTML = ajax.responseText;
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Tipo de cambio en la pestaña y seccion
 ******************************************************************************************************************************/
function marcarCambio(tipo,indice){
	var bandera = document.getElementById("wmodificado"+tipo+indice);
	
	if(bandera && bandera.value){
		bandera.value = "S";
	}
}

/*****************************************************************************************************************************
 * Consulta el esquema dextrometer seleccionado
 *****************************************************************************************************************************/
function consultarEsquemaInsulina(){
	var codEsquema = document.getElementById("wdexesquema");
	var codInsulina = document.getElementById("wdexins");
	var codFrecuencia = document.getElementById("wdexfrecuencia");
	
	if(codEsquema && codEsquema.value && codFrecuencia && codFrecuencia.value){
//		if(document.getElementById("btnEsquema")){
//			document.getElementById("btnEsquema").disabled = false;
//		}
		consultarEsquemaInsulinaElemento(codEsquema.value);
	}
//	else {
//		document.getElementById("btnEsquema").disabled = true;
//	}
}
/*****************************************************************************************************************************
 * Grabar esquema dextrometer
 *****************************************************************************************************************************/
function grabarEsquemaDextrometer(){
	var codArticulo = document.getElementById("wdexins");
	var frecuencia = document.getElementById("wdexfrecuencia");
	var codEsquema = document.getElementById("wdexesquema");
	var codEsquemaAnt = document.getElementById("whdexesquemaant");
	
	var usuario = document.forms.forma.usuario.value;	
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;	
	var fechaKardex = document.forms.forma.wfechagrabacion.value;
	
	if(codEsquema){
		var actualizaIntervalos = !(codEsquema.value == codEsquemaAnt.value); 
	
		if(!frecuencia || frecuencia.value == '' && !codEsquema || codEsquema.value == '' && !codArticulo || codArticulo.value == ''){
			return;
		}
	
		if(!frecuencia || frecuencia.value == ''){
			alert("Debe especificar una frecuencia");
			return;
		}
	
		//Valido que hayan ingresado las dosis y unidad de dosis del esquema y la via, las observaciones son opcionales
		var cont1 = 0;
		var dosis = "";
		var uDosis = "";
		var via = "";
		var obs = "";
	
		while(document.getElementById("wdexint"+cont1)){
			if(document.getElementById("wdexint"+cont1) && document.getElementById("wdexint"+cont1).value != ''
				&& document.getElementById("wdexseludo"+cont1) && document.getElementById("wdexseludo"+cont1).value != ''
				&& document.getElementById("wdexselvia"+cont1) && document.getElementById("wdexselvia"+cont1).value != ''){

			dosis += document.getElementById("wdexint"+cont1).value+"|";
			uDosis += document.getElementById("wdexseludo"+cont1).value+"|";
			via += document.getElementById("wdexselvia"+cont1).value+"|";
			obs += document.getElementById("wdexobs"+cont1).value+"|";
			} else {
				alert("Por favor revise los valores del esquema.  Debe especificar un valor de dosis, unidad y via. Linea: "+eval(cont1+1));
				return;
			}
			cont1++;
		}
		grabarEsquemaDextrometerElemento(historia, ingreso, fechaKardex, codArticulo.value, frecuencia.value, codEsquema.value, dosis, uDosis, via, obs, usuario, actualizaIntervalos);
	}
}
/*****************************************************************************************************************************
 * Seleccion de un medicamento de la lista consultada para llevarlo al detalle
 ******************************************************************************************************************************/
function quitarEsquemaDextrometer(){
	var codArticulo = document.getElementById("wdexins");
	var frecuencia = document.getElementById("wdexfrecuencia");
	var codEsquema = document.getElementById("wdexesquema");
	var usuario = document.forms.forma.usuario.value;
	
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;		
	
	var fechaKardex = document.forms.forma.wfechagrabacion.value;
	
	if(!frecuencia || frecuencia.value == ''){
		alert("No se ha especificado una frecuencia o condicion");
		return
	}
	
	eliminarEsquemaDextrometerElemento(historia, ingreso, fechaKardex, codArticulo.value, usuario);
}
/*****************************************************************************************************************************
 * Seleccion de un medicamento de la lista consultada para llevarlo al detalle
 * 
 * Se agregaron los siguientes protocolos:
 * 
 * 1.  Normal.  
 * 2.  Analgesias.
 * 3.  Nutriciones.
 * 4.  Quimioterapia.
 ******************************************************************************************************************************/
function seleccionarMedicamento(codigo, nombre, origen, grupo, forma, unidad, pos, unidadFraccion, cantFracciones, vencimiento, diasVencimiento, dispensable, duplicable, diasMaximos, dosisMaximas, via, tipoProtocolo){
	var tipoProtocoloAux = "";
	var i = 0, idx = 0;
	
	tipoProtocoloAux = tipoProtocolo;
	
	switch (tipoProtocolo) {
		case 'N':
			idx = elementosDetalle-1;
		
			posicionActual = elementosDetalle;
//			tipoProtocoloAux = "";
		break;
		case 'A':
			idx = elementosAnalgesia-1;
		
			posicionActual = elementosAnalgesia;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		case 'U':
			idx = elementosNutricion-1;
		
			posicionActual = elementosNutricion;
//			tipoProtocoloAux = tipoProtocolo;		
		break;
		case 'Q':
			idx = elementosQuimioterapia-1;
		
			posicionActual = elementosQuimioterapia;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		default:
//			idx = elementosDetalle-1;
			idx = elementosDetalle;
		
			posicionActual = elementosDetalle;
		break;
	}
	
	var elementoAnteriorDetalle = document.getElementById("wnmmed"+tipoProtocoloAux+idx);
	var cntDetalleKardex = document.getElementById("detKardex" + tipoProtocoloAux);
	
	var idTabla = "tbDetalle" + tipoProtocoloAux;
	
	stopEvent(window.onbeforeunload);
	
	if(posicionActual > 0){
		var elemento = elementoAnteriorDetalle;
		forma = forma.toString();
		
		if(elemento){		
			//Valido que el articulo no se encuentre previamente en la lista
			var existe = false;
			var adicionar = false;
			var cont1 = 0;
			var item = null;
			var cd = "";
			var cdItem = "";
			
			while(cont1 < elementosDetalle){
				item = document.getElementById("wnmmed"+tipoProtocoloAux+cont1);
										
				if(item){
					if(item.tagName != 'DIV'){
						cd = item.value.split("-");
					} else { 
						cd = item.innerHTML.split("-");
					}
					cdItem = cd[0];
					
					if(cdItem == codigo){
						existe = true;
						break;	
					}					
				}
				cont1++;
			}
			
			//Si el articulo es duplicable se anula la validacion anterior
			if(duplicable == 'on' && existe){
				adicionar = false;
			}
			
			if(!adicionar){
				if(elemento.tagName != 'DIV'){
					//En esta seccion se dan los posibles avisos
					//Grupo de control
					if(grupo == 'CTR'){
						alert("Este medicamento se encuentra en el grupo de control.  Requiere formula de control");
						elemento.value = codigo + "-" + origen + "-" + nombre.replace(/_/g," ") + " (de control)";
					} else {
						elemento.value = codigo + "-" + origen + "-" + nombre.replace(/_/g," ");
					}
					
					//No pos
					if(pos == 'N'){
						alert("Este medicamento es NO POS");
					}
					
					//Si no tiene fracciones en la unidad de manejo ni unidad de fraccion se avisa
					if(unidadFraccion == '' || cantFracciones == ''){
						alert('El articulo con codigo ' + codigo + ' no tiene unidad de fraccion o fracciones por unidad de manejo.  Por favor notifique a servicio farmaceutico.');
					}
					
					if(origen != 'CM' && document.getElementById("wchkconf"+tipoProtocoloAux+idx)){
						document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = true;
					} else {
						if(document.getElementById("wchkconf"+tipoProtocoloAux+idx))
							document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = false;
					}
					
					//Unidad dosis
					if(document.getElementById("wudosis"+tipoProtocoloAux+idx)){
						document.getElementById("wudosis"+tipoProtocoloAux+idx).value = unidadFraccion;
					}	
					
					//Unidad manejo
					if(document.getElementById("wcundmanejo"+tipoProtocoloAux+idx)){
						document.getElementById("wcundmanejo"+tipoProtocoloAux+idx).value = unidad;
					}
					
					//Cantidad maxima de fracciones en la unidad de manejo
					if(document.getElementById("wdosis"+tipoProtocoloAux+idx)){
						document.getElementById("wdosis"+tipoProtocoloAux+idx).value = cantFracciones;
					}			
					
					//Oculto para maximo de unidades del articulo
					if(document.getElementById("whcmanejo"+tipoProtocoloAux+idx)){
						document.getElementById("whcmanejo"+tipoProtocoloAux+idx).value = cantFracciones;
					}
					
					//Oculto vence
					if(document.getElementById("whvence"+tipoProtocoloAux+idx)){
						document.getElementById("whvence"+tipoProtocoloAux+idx).value = vencimiento;
					}
					
					//La forma farmaceutica se graba igual, mas no se muestra en el kardex
					if(document.getElementById("wfftica"+tipoProtocoloAux+idx)){
						document.getElementById("wfftica"+tipoProtocoloAux+idx).value = forma;
					}
					
					//Oculto dias vencimiento 
					if(document.getElementById("whdiasvence"+tipoProtocoloAux+idx)){
						document.getElementById("whdiasvence"+tipoProtocoloAux+idx).value = diasVencimiento;
					}
					
					//Oculto dispensable 
					if(document.getElementById("whdispensable"+tipoProtocoloAux+idx)){
						document.getElementById("whdispensable"+tipoProtocoloAux+idx).value = dispensable;
					}
					
					//Oculto duplicable
					if(document.getElementById("whduplicable"+tipoProtocoloAux+idx)){
						document.getElementById("whduplicable"+tipoProtocoloAux+idx).value = duplicable;
					}
					
					//Asigno dias maximos
					if(document.getElementById("wdiastto"+tipoProtocoloAux+idx) && parseInt(diasMaximos) > 0){
						document.getElementById("wdiastto"+tipoProtocoloAux+idx).value = diasMaximos;
					} else {
						document.getElementById("wdiastto"+tipoProtocoloAux+idx).value = '';
					}
					
					//Asigno dosis maximas
					if(document.getElementById("wdosmax"+tipoProtocoloAux+idx) && parseInt(dosisMaximas) > 0){
						document.getElementById("wdosmax"+tipoProtocoloAux+idx).value = dosisMaximas;
					} else {
						document.getElementById("wdosmax"+tipoProtocoloAux+idx).value = '';
					}
					
					//Asigno via
					if(document.getElementById("wviadmon"+tipoProtocoloAux+idx)){
						document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = via;
					} else {
						document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = '';
					}
					
					//Vias
//					debugger;
					document.getElementById("wviadmon"+tipoProtocoloAux+idx).innerHTML = "";
					if(via != ''){
						var opcionesMaestro = document.getElementById("wmviaadmon").options;			
					
						var cont1 = 0;
						var splVia = via.split(",");
						
						/************************************************************************************
						 * Marzo 7 de 2011
						 ************************************************************************************/
						//Si tiene mas de una via se agregar un campo mas en blanco
						//para que agregue el option 'seleccione'
						if( splVia.length > 1 ){
							splVia = (","+via).split(",");
						}
						
						var opcionTmp = null;
						
						while(cont1 < opcionesMaestro.length){
							
							for( var i = 0 ; i < splVia.length; i++ ){
								
								if( splVia[i] == opcionesMaestro[cont1].value ){
									
									opcionTmp = document.createElement("option");
									
									opcionTmp.text = opcionesMaestro[cont1].text;
									opcionTmp.value = opcionesMaestro[cont1].value;
									
									document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
								}
							}
							
							cont1++;
						}
						/************************************************************************************/

//						while(cont1 < opcionesMaestro.length){
//							opcionTmp = document.createElement("option");		
//							if(via.indexOf(opcionesMaestro[cont1].value) != -1){
//								document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
//							} 
//							
////							opcionTmp.innerText = opcionesMaestro[cont1].text;
//							opcionTmp.text = opcionesMaestro[cont1].text;
//							opcionTmp.value = opcionesMaestro[cont1].value;
//							
//							cont1++;
//						}
					} 
					else {
						var opcionesMaestro = document.getElementById("wmviaadmon").options;			
						
						var cont1 = 0;
						var splVia = via.split(",");
						var opcionTmp = null;

						while(cont1 < opcionesMaestro.length){
							opcionTmp = document.createElement("option");		
							document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
							
//							opcionTmp.innerText = opcionesMaestro[cont1].text;
							opcionTmp.text = opcionesMaestro[cont1].text;	//Marzo 7 de 2011
							opcionTmp.value = opcionesMaestro[cont1].value;
							
							cont1++;
						}
					}
					
					if(existe){
						alert('YA EXISTE');
					}					
				}else{
					alert('Debe agregar un nuevo articulo.');
				}
			} else {
				alert('El articulo ya se encuentra en la lista.  No se puede seleccionar');
			}
		}else{
			alert('No se encontro elemento a agregar.');
		}	
	}else{
		alert('Aun no ha agregado el primer articulo');
	}
}

/*****************************************************************************************************************************
 *Punto de entrada de la generación del kardex 
 ******************************************************************************************************************************/
function enter() { 
	var historia = document.forms.forma.whistoria.value;
	var valido = true;
		
	//Validacion fecha
	if(historia == ''){
		alert('Debe especificar la historia clinica del paciente');
		valido = false;
	}
		
	if(valido){
		document.forms.forma.submit();
	}
}
/*****************************************************************************************************************************
 *Invoca acción de grabación del kardex, del encabezado y paso de las temporales a las tablas definitivas. 
 ******************************************************************************************************************************/
function grabarKardex(){
	
	articulosSinGrabar = Array();
	
	if(!grabando){
		grabando=true;
		var valido = true;
		var mensaje = '';
		
		window.onbeforeunload = '';
		
		if(valido){
			var conf = document.getElementById('wconfdisp').checked == true ? 'on' : 'off';
			var rutaImagenOrdenMedica = ''; 
	
			if(document.getElementById("ordenActual")){
				rutaImagenOrdenMedica = document.getElementById("ordenActual").value;
			}
					
			if(rutaImagenOrdenMedica == '' && typeof(upload_target) != 'undefined' && upload_target.document.getElementById('hdRutaOrden')){
				var ordenIframe = upload_target.document.getElementById('hdRutaOrden').value;
				if(ordenIframe != ''){
					rutaImagenOrdenMedica = ordenIframe;
				} 
			}
			
			//El encabezado del kardex se encuentra dividido en las pestañas
			var pestanasActivas = document.getElementById("hpestanas").value;
			
			document.forms.forma.action = 'generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value
							+'&waccion=c'
							+'&whistoria='+document.forms.forma.whistoria.value
							+'&ingreso='+document.forms.forma.wingreso.value
							+'&wfecha='+document.forms.forma.wfecha.value
							+'&wfechagrabacion='+document.forms.forma.wfechagrabacion.value
							+'&primerKardex='+document.getElementById("wkardexnuevo").value
							+'&rutaOrdenMedica='+rutaImagenOrdenMedica
							+'&editable='+document.forms.forma.editable.value
							+'&confirmado='+conf
							+'&wsservicio='+document.forms.forma.wservicio.value;
			
			/*
			if(pestanasActivas.indexOf('1') >= 0 || pestanasActivas.indexOf('*') >= 0){
				document.forms.forma.action += '&talla='+document.forms.forma.txTalla.value
							+'&peso='+document.forms.forma.txPeso.value
							+'&diagnostico=' + reemplazarTodo(document.forms.forma.txDiag.value,"\r","|")
							+'&alergias='+ reemplazarTodo(document.forms.forma.txAlergias.value,"\r","|")
							+'&antecedentesPersonales=' + reemplazarTodo(document.forms.forma.txAntecedentesPersonales.value,"\r","|");
	
			}
			*/
			/*
			if(pestanasActivas.indexOf('5') >= 0 || pestanasActivas.indexOf('*') >= 0){
				document.forms.forma.action += '&cuidadosEnfermeria=' + reemplazarTodo(document.forms.forma.txCuidados.value,"\r","|")
							+'&terapiaRespiratoria=' + reemplazarTodo(document.forms.forma.txTerapia.value,"\r","|")						
							+'&observaciones=' + reemplazarTodo(document.forms.forma.txObservaciones.value,"\r","|")
							+'&curaciones=' + reemplazarTodo(document.forms.forma.txtCuraciones.value,"\r","|")
							+'&sondasCateteres=' + reemplazarTodo(document.forms.forma.txtSondas.value,"\r","|")
							+'&interconsulta=' + reemplazarTodo(document.forms.forma.txtInterconsulta.value,"\r","|")
							+'&consentimientos=' + reemplazarTodo(document.forms.forma.txtConsentimientos.value,"\r","|")
							+'&preparacionAlta=' + reemplazarTodo(document.forms.forma.txtPrepalta.value,"\r","|")
							+'&obsDietas=' + reemplazarTodo(document.forms.forma.txtObsDietas.value,"\r","|")
							+'&mezclas=' + reemplazarTodo(document.forms.forma.txtMezclas.value,"\r","|")
							+'&dextrometer=' + reemplazarTodo(document.forms.forma.txtDextrometer.value,"\r","|")
							+'&cirugiasPendientes=' + reemplazarTodo(document.forms.forma.txtCirugiasPendientes.value,"\r","|")
							+'&terapiaFisica=' + reemplazarTodo(document.forms.forma.txTerapiaFisica.value,"\r","|")
							+'&rehabilitacionCardiaca=' + reemplazarTodo(document.forms.forma.txRehabilitacionCardiaca.value,"\r","|")						
							+'&aislamientos=' + reemplazarTodo(document.forms.forma.txAislamientos.value,"\r","|");
			}
			*/
			$.blockUI({ message: $('#msjEspere') });
	//		debugger;
			
			//Grabacion automatica de las pestanas
			var arrProtocolos = new Array("N","A","U","Q","2","4");
			var tipoProtocolo = "", nomContenedor = "", tabla = "";
			var pelo = "",celdas = "", celda = "", componentes = "", modificado = "", limiteIteraciones = "";
			
			for(var cont2 = 0; cont2 < arrProtocolos.length; cont2++){
				tipoProtocolo = arrProtocolos[cont2];
				//Variador del metodo
				switch (tipoProtocolo){
					case 'N':
					case 'A':
					case 'U':
					case 'Q':
						nomContenedor = "tbDetalle";
						tabla = document.getElementById(nomContenedor+tipoProtocolo);
						if(tabla){
							limiteIteraciones = tabla.rows.length;
						}else{
							limiteIteraciones = 0;
						}
					break;
					case '2':
						nomContenedor = "tbDetInfusiones";
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones = tabla.rows.length;
						}else{
							limiteIteraciones = 0;
						}
					break;
					case '4':
						if(document.getElementById("cuentaExamenes")){
							limiteIteraciones = document.getElementById("cuentaExamenes").value;
						} else {
							limiteIteraciones = 0;
						}
						
						//Contabilizacion de cantidad de examenes totales, existentes y nuevos
						var incremento = eval(limiteIteraciones)-1;
						
						if(incremento < 0){
							incremento = 0;
						}
						
						while(document.getElementById("trEx"+incremento)){
							incremento++;
						}
						limiteIteraciones = incremento;
					break;
				}
//				debugger;
				//Iteracion por fila
				for(var cont1 = 0; cont1 < limiteIteraciones; cont1++){
					var modificado = document.getElementById("wmodificado" + tipoProtocolo + cont1);
						
					//Variador del metodo
					switch (tipoProtocolo){
						case 'N':
						case 'A':
						case 'U':
						case 'Q':
							if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){ //Articulo existente
								if(document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName == "DIV"){
									if(!grabarArticuloSinValidacion(cont1,tipoProtocolo)){
										$.unblockUI();
										grabando=false;
										return;
									}
								} else {
									if(document.getElementById("wnmmed"+tipoProtocolo+cont1) != null && document.getElementById("wnmmed"+tipoProtocolo+cont1) != 'undefined' && document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != "DIV"){
										if(!grabarArticulo(cont1,tipoProtocolo)){
											$.unblockUI();
											grabando=false;
											return;
										}
									}	
								}
							} else {  //Articulo nuevo
								if(document.getElementById("wnmmed"+tipoProtocolo+cont1) != null && document.getElementById("wnmmed"+tipoProtocolo+cont1) != 'undefined' && document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != "DIV"){
									if(!grabarArticulo(cont1,tipoProtocolo)){
										$.unblockUI();
										grabando=false;
										return;
									}
								}
							}
						break;	
						case '2':						
							var indiceInfusion = document.getElementById("windiceliq"+cont1);
							
							if(indiceInfusion && indiceInfusion != null && indiceInfusion != 'undefined'){
								modificado = document.getElementById("wmodificado" + tipoProtocolo + indiceInfusion.value);
								if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){
									indiceInfusion = indiceInfusion.value;
									if(!grabarInfusion(indiceInfusion,tipoProtocolo)){
										$.unblockUI();
										grabando=false;
										return;
									}
								}
							}
						break;
						case '4':
							modificado = document.getElementById("wmodificado" + tipoProtocolo + cont1);
							if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){
								if(!grabarExamen(cont1,tipoProtocolo)){
									$.unblockUI();
									grabando=false;
									return;
								}
							}
						break;
					}
				}
			}
			
			//Grabacion automatica del dextrometer
			grabarEsquemaDextrometer();
			
//			document.location.href = cadena;
//			document.forms.forma.action = cadena;
			document.forms.forma.submit();
		} else {
			alert(mensaje);
		}	
	} 
}

/*****************************************************************************************************************************
 * Invoca acción de grabación del kardex, del encabezado y paso de las temporales a las tablas definitivas. 
 *
 * Marzo 15 de 2011
 * Copia de la funcion grabarKardex. Esta funcion difiere de grabar kardex en cuanto que esta busca todos los posibles errores 
 * al momento de grabar kardex
 ******************************************************************************************************************************/
function grabarKardexSalida(){
//	alert("grabar Salida.....");
	articulosSinGrabar = Array();
	error = 0;
	
	if(!grabando){
		grabando=true;
		var valido = true;
		var mensaje = '';
		
//		window.onbeforeunload = '';
		
		if(valido){
			var conf = document.getElementById('wconfdisp').checked == true ? 'on' : 'off';
			var rutaImagenOrdenMedica = ''; 
	
			if(document.getElementById("ordenActual")){
				rutaImagenOrdenMedica = document.getElementById("ordenActual").value;
			}
					
			if(rutaImagenOrdenMedica == '' && typeof(upload_target) != 'undefined' && upload_target.document.getElementById('hdRutaOrden')){
				var ordenIframe = upload_target.document.getElementById('hdRutaOrden').value;
				if(ordenIframe != ''){
					rutaImagenOrdenMedica = ordenIframe;
				} 
			}
			
			//El encabezado del kardex se encuentra dividido en las pestañas
			var pestanasActivas = document.getElementById("hpestanas").value;
			
			document.forms.forma.action = 'generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value
							+'&waccion=c'
							+'&whistoria='+document.forms.forma.whistoria.value
							+'&ingreso='+document.forms.forma.wingreso.value
							+'&wfecha='+document.forms.forma.wfecha.value
							+'&wfechagrabacion='+document.forms.forma.wfechagrabacion.value
							+'&primerKardex='+document.getElementById("wkardexnuevo").value
							+'&rutaOrdenMedica='+rutaImagenOrdenMedica
							+'&editable='+document.forms.forma.editable.value
							+'&confirmado='+conf
							+'&wsservicio='+document.forms.forma.wservicio.value;
			
//			$.blockUI({ message: $('#msjEspere') });
	//		debugger;
			
			//Grabacion automatica de las pestanas
			var arrProtocolos = new Array("N","A","U","Q","2","4");
			var tipoProtocolo = "", nomContenedor = "", tabla = "";
			var pelo = "",celdas = "", celda = "", componentes = "", modificado = "", limiteIteraciones = "";
			
			for(var cont2 = 0; cont2 < arrProtocolos.length; cont2++){
				tipoProtocolo = arrProtocolos[cont2];
				//Variador del metodo
				switch (tipoProtocolo){
					case 'N':
					case 'A':
					case 'U':
					case 'Q':
						nomContenedor = "tbDetalle";
						tabla = document.getElementById(nomContenedor+tipoProtocolo);
						if(tabla){
							limiteIteraciones = tabla.rows.length;
						}else{
							limiteIteraciones = 0;
						}
					break;
					case '2':
						nomContenedor = "tbDetInfusiones";
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones = tabla.rows.length;
						}else{
							limiteIteraciones = 0;
						}
					break;
					case '4':
						if(document.getElementById("cuentaExamenes")){
							limiteIteraciones = document.getElementById("cuentaExamenes").value;
						} else {
							limiteIteraciones = 0;
						}
						
						//Contabilizacion de cantidad de examenes totales, existentes y nuevos
						var incremento = eval(limiteIteraciones)-1;
						
						if(incremento < 0){
							incremento = 0;
						}
						
						while(document.getElementById("trEx"+incremento)){
							incremento++;
						}
						limiteIteraciones = incremento;
					break;
				}
//				debugger;
				//Iteracion por fila
				for(var cont1 = 0; cont1 < limiteIteraciones; cont1++){
					var modificado = document.getElementById("wmodificado" + tipoProtocolo + cont1);
						
					//Variador del metodo
					switch (tipoProtocolo){
						case 'N':
						case 'A':
						case 'U':
						case 'Q':
							if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){ //Articulo existente
								if(document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName == "DIV"){
									if(!grabarArticuloSinValidacion(cont1,tipoProtocolo)){
//										$.unblockUI();
										grabando=false;
										error = 1;
									}
								} else {
									if(document.getElementById("wnmmed"+tipoProtocolo+cont1) != null && document.getElementById("wnmmed"+tipoProtocolo+cont1) != 'undefined' && document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != "DIV"){
										if(!grabarArticulo(cont1,tipoProtocolo)){
//											$.unblockUI();
											grabando=false;
											error = 1;
										}
									}	
								}
							} else {  //Articulo nuevo
								if(document.getElementById("wnmmed"+tipoProtocolo+cont1) != null && document.getElementById("wnmmed"+tipoProtocolo+cont1) != 'undefined' && document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != "DIV"){
									if(!grabarArticulo(cont1,tipoProtocolo)){
//										$.unblockUI();
										grabando=false;
										error = 1;
									}
								}
							}
						break;	
						case '2':						
							var indiceInfusion = document.getElementById("windiceliq"+cont1);
							
							if(indiceInfusion && indiceInfusion != null && indiceInfusion != 'undefined'){
								modificado = document.getElementById("wmodificado" + tipoProtocolo + indiceInfusion.value);
								if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){
									indiceInfusion = indiceInfusion.value;
									if(!grabarInfusion(indiceInfusion,tipoProtocolo)){
//										$.unblockUI();
										grabando=false;
										error = 2;
									}
								}
							}
						break;
						case '4':
							modificado = document.getElementById("wmodificado" + tipoProtocolo + cont1);
							if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){
								if(!grabarExamen(cont1,tipoProtocolo)){
//									$.unblockUI();
									grabando=false;
									error = 3;
								}
							}
						break;
					}
				}
			}
			
			//Grabacion automatica del dextrometer
			grabarEsquemaDextrometer();
			
//			document.location.href = cadena;
//			document.forms.forma.action = cadena;
//			document.forms.forma.submit();
//			$.unblockUI();
			return error;
		} else {
			alert(mensaje);
		}	
	} 
}
/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
function consultarEsquemaInsulinaElemento(codEsquema){
	var contenedor = document.getElementById('cntEsquema');
	var parametros = "";
		
	parametros = "consultaAjaxKardex=05&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo="+codEsquema; 

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				contenedor.innerHTML=ajax.responseText;	
				
				var cntEsquema = document.getElementById("cntEsquema");

				if(cntEsquema){
					cntEsquema.style.display = 'block';
				}
			} 
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * ----------OPERACIONES DE INTERFAZ DE USUARIO----------------
 ******************************************************************************************************************************/
function resetArticulo(){ document.forms.forma.wnmmed.value = ''; }
/*****************************************************************************************************************************
 * Elimina un componente del multiselect de las infusiones
 ******************************************************************************************************************************/
function quitarComponente(idx){
  var elSel = document.getElementById('wtxtcomponentes' + idx);
  
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
    if (elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}
/*****************************************************************************************************************************
 * Limpia el resultado de los buscadores
 ******************************************************************************************************************************/
function limpiarBuscador(){
	var resultados = document.getElementById('cntMedicamento');
	
	resultados.innerHTML = "";
}
/*****************************************************************************************************************************
 * Crea dinámicamente una fila en el detalle de medicamentos
 * 
 * Dependiendo del tipo de protocolo se agregará a una lista u otra, los protocolos actuales son:
 * 
 * 1. Normal, se agrega en la lista convencional
 * 2. Analgesia: Contenedor detAnalgesia
 ******************************************************************************************************************************/
function agregarArticulo(){
	
	var idx = 0;
	var tipoProtocolo = "";
	var cntDetalleKardex = "";
	
	var idFilaNueva = "", idColumnaArticulo = "", idCampoArticulo = "", idCampoDosis = "", idUnidadManejo = "";
	var idDuplicable = "", idDispensable = "", idDiasVencimiento = "", idFormaFarmaceutica = "", idOcultoFracciones = "", idOcultoVence = "", idCantidadUnidadManejo = ""; 
	var idUnidadDosis = "", idPeriodicidad = "", idCondicion = "", idVia = "", idFechaInicio = "", idBtnFechaInicio = "", idChkConfirmacion = "", idChkNoEnviar = "";
	var idDiasTratamiento = "", idDosisMaximas = "", idObservaciones = "", idIndiceMovimiento = "";

	var elementoAnteriorDetalle = 0; 
	var posicionActual = 0;

	var tipoProtocoloAux = "";
	
	for (i=0;i<document.forms.forma.wtipoprot.length;i++){ 
    	if (document.forms.forma.wtipoprot[i].checked){
    		tipoProtocolo = document.forms.forma.wtipoprot[i].value;
    		break; 
    	}
    }
	
	//Buscador de medicamentos flotante kardex
//	if(document.getElementById("fixeddiv2")){
		//fixedMenu2.show();
//	}

	tipoProtocoloAux = tipoProtocolo;
	switch (tipoProtocolo) {
		case 'N':
			idx = elementosDetalle-1;
		
			posicionActual = elementosDetalle;
//			tipoProtocoloAux = tipoProtocolo;
		break;
	case 'A':
			idx = elementosAnalgesia-1;
		
			posicionActual = elementosAnalgesia;
//			tipoProtocoloAux = tipoProtocolo;
		break;
	case 'U':
			idx = elementosNutricion-1;
		
			posicionActual = elementosNutricion;
//			tipoProtocoloAux = tipoProtocolo;		
		break;
	case 'Q':
			idx = elementosQuimioterapia-1;
		
			posicionActual = elementosQuimioterapia;
//			tipoProtocoloAux = tipoProtocolo;
		break;
	default:
		break;
	}
	
	elementoAnteriorDetalle = document.getElementById("wnmmed"+tipoProtocoloAux+idx);
	cntDetalleKardex = document.getElementById("detKardex" + tipoProtocoloAux);
	
	idTabla = "tbDetalle" + tipoProtocoloAux;		
	
	idFilaNueva = 'tr'+ tipoProtocoloAux + posicionActual;
	
	idColumnaArticulo = 'wcolmed' + tipoProtocoloAux + posicionActual;
	idCampoArticulo = 'wnmmed'+ tipoProtocoloAux + posicionActual;
	idCampoDosis = 'wdosis' + tipoProtocoloAux + posicionActual;
	idUnidadManejo = 'wcundmanejo' + tipoProtocoloAux + posicionActual;
	idDuplicable = 'whduplicable' + tipoProtocoloAux + posicionActual;
	idDispensable = 'whdispensable' + tipoProtocoloAux + posicionActual;
	idDiasVencimiento = 'whdiasvence' + tipoProtocoloAux + posicionActual;
	idFormaFarmaceutica = 'wfftica' + tipoProtocoloAux + posicionActual;
	idOcultoFracciones = 'whcmanejo' + tipoProtocoloAux + posicionActual;
	idOcultoVence = 'whvence' + tipoProtocoloAux + posicionActual;
	idCantidadUnidadManejo = 'wcumanejo' + tipoProtocoloAux + posicionActual;
	
	idUnidadDosis = 'wudosis' + tipoProtocoloAux + posicionActual;
	idPeriodicidad = 'wperiod' + tipoProtocoloAux + posicionActual;
	idCondicion = 'wcondicion' + tipoProtocoloAux + posicionActual;
	idVia = 'wviadmon' + tipoProtocoloAux + posicionActual;
	idFechaInicio = 'wfinicio' + tipoProtocoloAux + posicionActual;
	idBtnFechaInicio = 'btnFecha' + tipoProtocoloAux + posicionActual;
	idChkConfirmacion = 'wchkconf' + tipoProtocoloAux + posicionActual;
	idChkNoEnviar = 'wchkdisp' + tipoProtocoloAux + posicionActual;
	idDiasTratamiento = 'wdiastto' + tipoProtocoloAux + posicionActual;
	idDosisMaximas = 'wdosmax' + tipoProtocoloAux + posicionActual;
	idObservaciones = 'wtxtobs' + tipoProtocoloAux + posicionActual;
	
	idIndiceMovimiento = "tr" + tipoProtocoloAux;
	
	if(!elementoAnteriorDetalle || elementoAnteriorDetalle.value != '' || posicionActual == 0){

		//Fecha y hora actual para colocar automaticamente la fecha y hora actual 2009-05-10 a las:10:00
		var fechaActual = new Date();
			
		diaActual = fechaActual.getDate();
		mesActual = fechaActual.getMonth() + 1;
		anioActual= fechaActual.getFullYear();
		
		horaActual = fechaActual.getHours();
		
		if(horaActual % 2 != 0){
			fechaActual.setHours(fechaActual.getHours() + 1);
		} else {
			fechaActual.setHours(fechaActual.getHours() + 2);
		}
		horaActual = fechaActual.getHours();
		
		if(mesActual && mesActual.toString().length == 1){
			mesActual = "0" + mesActual.toString();
		}
		
		if(diaActual && diaActual.toString().length == 1){
			diaActual = "0" + diaActual.toString();
		}
		
		fechaCompuesta = anioActual + "-" + mesActual + "-" + diaActual + " a las:" + horaActual + ":00";
		
		//Fila nueva
		var fila = document.createElement("tr");
		fila.setAttribute('id',idFilaNueva);		
	
		//Columnas nuevas
		var columna1 = document.createElement("td");		//Acciones
		var columna2 = document.createElement("td");		//Articulo	
		var columna3 = document.createElement("td");		//Dosis
		var columna4 = document.createElement("td");		//Periodicidad
		var columna5 = document.createElement("td");		//Condición de suministro
		var columna6 = document.createElement("td");		//Forma farmaceutica
		var columna7 = document.createElement("td");		//Fecha y hora de inicio
		var columna8 = document.createElement("td");		//Dosis maxima
		var columna9 = document.createElement("td");		//Via administracion
		var columna10 = document.createElement("td");		//Confirmar
		var columna11 = document.createElement("td");		//Dias tratamiento
		var columna12 = document.createElement("td");		//Observaciones
		var columna13 = document.createElement("td");		//Unidad de manejo
		var columna14 = document.createElement("td");		//No dispensar

		//Centradas
		columna10.setAttribute('align','center');
		columna14.setAttribute('align','center');
		
		columna2.setAttribute('id',idColumnaArticulo);
		
		/******************************************************************************
		CONTENIDO DE LAS COLUMNAS
		*******************************************************************************/
		//Link de grabar
		var link1 = document.createElement("a");
		link1.setAttribute('href','#null');
		if(!esIE){
			link1.setAttribute('onClick','javascript:grabarArticulo('+ posicionActual +',"'+tipoProtocolo+'");');
		}else{
			link1.onclick  = new Function('evt','javascript:grabarArticulo('+ posicionActual +',"'+tipoProtocolo+'");');
		}
	
		var img1 = document.createElement("img");
		img1.setAttribute('src','../../images/medical/root/grabar.png');

		link1.appendChild(img1);
	
		//Link de borrar	
		var link2 = document.createElement("a");
		link2.setAttribute('href','#null');
		if(!esIE){
			link2.setAttribute('onClick','javascript:quitarArticulo('+ posicionActual +',"'+tipoProtocolo+'");');
		}else{
			link2.onclick  = new Function('evt','javascript:quitarArticulo('+ posicionActual +',"'+tipoProtocolo+'");');
		}
	
		var img2 = document.createElement("img");
		img2.setAttribute('src','../../images/medical/root/borrar.png');

		link2.appendChild(img2);
	
		//Input de articulo
		var articulo = document.createElement("input");
		articulo.setAttribute('type','text');
		articulo.setAttribute('name',idCampoArticulo);
		articulo.setAttribute('id',idCampoArticulo);	
		articulo.setAttribute('size','60');
		articulo.setAttribute('readOnly','readOnly');
		articulo.className = 'campo2';
		if(!esIE){
			articulo.setAttribute('style','border:0px');
		}else{
			articulo.style.setAttribute('border','0px');
		}		
	
		//Dosis a aplicar
		var dosis = document.createElement("input");
		dosis.setAttribute('type','text');
		dosis.setAttribute('name',idCampoDosis);
		dosis.setAttribute('id',idCampoDosis);	
		dosis.setAttribute('size','7');
		dosis.setAttribute('maxLength','7');
		dosis.className = 'campo2';   //validarEntradaDecimal(event);
		if(!esIE){
			dosis.setAttribute('onKeyPress', 'return validarEntradaDecimal(event);');
		}else{
			dosis.onkeypress = new Function('evt','return validarEntradaDecimal(event);');
		}
		
		//Unidad de manejo
		var unidadManejo = document.createElement("input");
		unidadManejo.setAttribute('type','hidden');
		unidadManejo.setAttribute('name',idUnidadManejo);
		unidadManejo.setAttribute('id',idUnidadManejo);
		
		//Total de fracciones dentro de la unidad de manejo Oculto
		var ocultoFracciones = document.createElement("input");
		ocultoFracciones.setAttribute('type','hidden');
		ocultoFracciones.setAttribute('name',idOcultoFracciones);
		ocultoFracciones.setAttribute('id',idOcultoFracciones);
		
		//Vence
		var vence = document.createElement("input");
		vence.setAttribute('type','hidden');
		vence.setAttribute('name',idOcultoVence);
		vence.setAttribute('id',idOcultoVence);
		
		//Forma farmaceutica oculta
		var formaFtica = document.createElement("input");
		formaFtica.setAttribute('type','hidden');
		formaFtica.setAttribute('name',idFormaFarmaceutica);
		formaFtica.setAttribute('id',idFormaFarmaceutica);
		
		
		//Dias vencimiento
		var diasVencimiento = document.createElement("input");
		diasVencimiento.setAttribute('type','hidden');
		diasVencimiento.setAttribute('name',idDiasVencimiento);
		diasVencimiento.setAttribute('id',idDiasVencimiento);

		//Es dispensable
		var dispensable = document.createElement("input");
		dispensable.setAttribute('type','hidden');
		dispensable.setAttribute('name',idDispensable);
		dispensable.setAttribute('id',idDispensable);

		//Es duplicable
		var duplicable = document.createElement("input");
		duplicable.setAttribute('type','hidden');
		duplicable.setAttribute('name',idDuplicable);
		duplicable.setAttribute('id',idDuplicable);
		
		//Cantidad de unidad de manejo
		var cantidadUnidadManejo = document.createElement("input");
		cantidadUnidadManejo.setAttribute('type','text');
		cantidadUnidadManejo.setAttribute('name',idCantidadUnidadManejo);
		cantidadUnidadManejo.setAttribute('id',idCantidadUnidadManejo);	
		cantidadUnidadManejo.setAttribute('size','4');
		cantidadUnidadManejo.setAttribute('disabled','disabled');
		cantidadUnidadManejo.setAttribute('maxLength','4');
		cantidadUnidadManejo.setAttribute('value','1');
		cantidadUnidadManejo.className = 'campo2';
		if(!esIE){
			cantidadUnidadManejo.setAttribute('onKeyPress', 'return validarEntradaDecimal(event);');
		}else{
			cantidadUnidadManejo.onkeypress = new Function('evt','return validarEntradaDecimal(event);');
		}
		
		/***********************************************
		LLENA LOS SELECTS PARA UBICARLOS EN LA FILA 
		************************************************/
		var unidadDosis = document.createElement("select");
		var periodicidad = document.createElement("select");
		var condicion = document.createElement("select");
		var viaAdmon = document.createElement("select");
		
		unidadDosis.setAttribute('id',idUnidadDosis);		
		unidadDosis.className = 'seleccion';
		unidadDosis.setAttribute('disabled','disabled');
		periodicidad.setAttribute('id',idPeriodicidad);
		periodicidad.className = 'seleccion';		
		condicion.setAttribute('id',idCondicion);
		condicion.className = 'seleccion';
		viaAdmon.setAttribute('id',idVia);
		viaAdmon.className = 'seleccion';
		
		//Diferencias de navegadores
		if(!esIE){
			unidadDosis.innerHTML = document.getElementById("wmunidadesmedida").innerHTML;
			periodicidad.innerHTML = document.getElementById("wmperiodicidades").innerHTML;
			condicion.innerHTML = document.getElementById("wmcondicionessuministro").innerHTML;
			viaAdmon.innerHTML = document.getElementById("wmviaadmon").innerHTML;
			
			unidadDosis.setAttribute('style','border:120px');
		}else {		
			unidadDosis.style.setAttribute('width','120px');
			unidadDosis.style.setAttribute('style','font-family: verdana; color: black');
			
			//Unidades dosis
			var opcionesMaestro = document.getElementById("wmunidadesmedida").options;			
			var cont1 = 0;
			var opcionTmp = null;
			
			while(opcionesMaestro[cont1]){
				opcionTmp = document.createElement("option");		
				unidadDosis.options.add(opcionTmp);
				
				opcionTmp.innerText = opcionesMaestro[cont1].text;
				opcionTmp.value = opcionesMaestro[cont1].value;
				
				cont1++;
			}
			
			//Periodicidades
			opcionesMaestro = document.getElementById("wmperiodicidades").options;
			var cont1 = 0;
			var opcionTmp = null;
			
			while(opcionesMaestro[cont1]){
				opcionTmp = document.createElement("option");		
				periodicidad.options.add(opcionTmp);
				
				opcionTmp.innerText = opcionesMaestro[cont1].text;
				opcionTmp.value = opcionesMaestro[cont1].value;
				
				cont1++;
			}
			
			//Condiciones de suministro
			
			opcionesMaestro = document.getElementById("wmcondicionessuministro").options;
			var cont1 = 0;
			var opcionTmp = null;
			
			while(opcionesMaestro[cont1]){
				opcionTmp = document.createElement("option");		
				condicion.options.add(opcionTmp);
				
				opcionTmp.innerText = opcionesMaestro[cont1].text;
				opcionTmp.value = opcionesMaestro[cont1].value;
				
				cont1++;
			}
						
			//Vias de administración
			opcionesMaestro = document.getElementById("wmviaadmon").options;
			var cont1 = 0;
			var opcionTmp = null;
			
			while(opcionesMaestro[cont1]){
				opcionTmp = document.createElement("option");		
				viaAdmon.options.add(opcionTmp);
				
				opcionTmp.innerText = opcionesMaestro[cont1].text;
				opcionTmp.value = opcionesMaestro[cont1].value;
				
				cont1++;
			}
		}	

		//Fecha y hora de administracion
		var fini = document.createElement("input");
		fini.setAttribute('id',idFechaInicio);
		fini.setAttribute('name',idFechaInicio);
		fini.setAttribute('type','text');
		fini.setAttribute('size','25');
		fini.setAttribute('value',fechaCompuesta);
		fini.setAttribute('readOnly','readonly');
		fini.className = 'campo2';
		
		var btnFini = document.createElement("input");
		btnFini.setAttribute('type','button');
		btnFini.setAttribute('id',idBtnFechaInicio);
		btnFini.setAttribute('name',idBtnFechaInicio);
		btnFini.setAttribute('alt','Haga doble click para desplegar el calendario');
		btnFini.setAttribute('value','*');
		
		if(!esIE){	
			btnFini.setAttribute('onClick', 'javascript:calendario('+posicionActual+',"'+tipoProtocolo+'");');
		}else {						
			btnFini.onclick = new Function('evt','javascript:calendario('+posicionActual+',"'+tipoProtocolo+'")');
		}	
		
		//Confirmado para central de mezclas
		var chkConf = document.createElement("input");
		chkConf.setAttribute('id',idChkConfirmacion);
		chkConf.setAttribute('type','checkbox');
		chkConf.disabled = true;
		
		//No se envia el articulo
		var chkDisp = document.createElement("input");
		chkDisp.setAttribute('id',idChkNoEnviar);
		chkDisp.setAttribute('type','checkbox');
				
		//Dias de tratamiento
		var diasTto = document.createElement("input");
		diasTto.setAttribute('id',idDiasTratamiento);
		diasTto.setAttribute('type','text');
		diasTto.setAttribute('size','3');
		diasTto.setAttribute('maxLength','3');
		diasTto.className = 'campo2';
		
		if(!esIE){	
			diasTto.setAttribute('onKeyPress', 'return validarEntradaEntera(event);');
		}else {						
			diasTto.onkeypress = new Function('evt','return validarEntradaEntera(event);');
		}
		
		//Dosis maxima
		var dosMax = document.createElement("input");
		dosMax.setAttribute('id',idDosisMaximas);
		dosMax.setAttribute('type','text');
		dosMax.setAttribute('size','6');
		dosMax.setAttribute('maxLength','6');
		dosMax.className = 'campo2';
		
		if(!esIE){	
			dosMax.setAttribute('onKeyPress', 'return validarEntradaEntera(event);');
		}else {						
			dosMax.onkeypress = new Function('evt','return validarEntradaEntera(event);');
		}
		
		//Observaciones
		var observaciones = document.createElement("textarea");
		observaciones.setAttribute('id',idObservaciones);
		observaciones.setAttribute('rows','3');
		observaciones.setAttribute('cols','30');
		
//		if(!esIE){
//			observaciones.setAttribute('onKeyPress', 'return validarEntradaAlfabetica(event);');
//		}else{
//			observaciones.onkeypress = new Function('evt','return validarEntradaAlfabetica(event);');
//		}
		
		/*******************************************************************************
		ANEXAR CONTENIDO A LAS COLUMNAS
		********************************************************************************/
//		columna1.appendChild(link1);	
		columna1.appendChild(link2);
		columna2.appendChild(articulo);
		columna3.appendChild(dosis);
		columna3.appendChild(unidadDosis);
		columna4.appendChild(periodicidad);
		columna5.appendChild(condicion);
		columna7.appendChild(fini);
		columna7.appendChild(btnFini);
		columna8.appendChild(dosMax);
		columna9.appendChild(viaAdmon);
		columna10.appendChild(chkConf);
		columna11.appendChild(diasTto);
		columna12.appendChild(observaciones);
		columna14.appendChild(chkDisp);
	
		/*******************************************************************************
		ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
		********************************************************************************/
		fila.appendChild(columna1);		
		fila.appendChild(columna2);		
		fila.appendChild(columna14);
		fila.appendChild(columna3);
		fila.appendChild(columna4);		
		fila.appendChild(columna9);
		fila.appendChild(columna7);
		fila.appendChild(columna5);
		fila.appendChild(columna10);
		fila.appendChild(columna11);
		fila.appendChild(columna8);
		fila.appendChild(columna12);

		//Anexo los campos hidden antes de anexar la fila
		cntDetalleKardex.appendChild(unidadManejo);
		cntDetalleKardex.appendChild(ocultoFracciones);
		cntDetalleKardex.appendChild(vence);
		cntDetalleKardex.appendChild(diasVencimiento);
		cntDetalleKardex.appendChild(dispensable);
		cntDetalleKardex.appendChild(duplicable);
		cntDetalleKardex.appendChild(formaFtica);
		
		//ANEXAR LA FILA A LA TABLA
//		cntDetalleKardex.appendChild(fila);	
		
		//Incremento del indice segun la lista
		switch (tipoProtocolo) {
			case 'N':
				elementosDetalle++;
				break;
			case 'A':
				elementosAnalgesia++;
				break;
			case 'U':
				elementosNutricion++;
				break;
			case 'Q':
				elementosQuimioterapia++;
				break;
			default:
				break;
		}
		
		posicionActual++;
		
		var indice = 0;
		
		//Mueve al principio el articulo
		while(document.getElementById(idIndiceMovimiento+indice)){
			indice++;
		}
		
		//ANEXAR LA FILA A LA TABLA
		cntDetalleKardex.insertBefore(fila, cntDetalleKardex.firstChild);
	} else {
	}
	
	//Posiciona el cursor sobre el texto del cajon del codigo
	if(document.getElementById("wbnommed")){
		document.getElementById("wbnommed").focus();
		document.getElementById("wbnommed").select();
	}
}
/*****************************************************************************************************************************
 * Crea dinámicamente una fila nueva para agregar una infusión
 ******************************************************************************************************************************/
function agregarInfusion(){
	var puedeAgregar = true;
	var cantFilas = document.getElementById("tbDetInfusiones").rows.length;
	
	//Fecha actual automatica 
	var fechaActual = new Date();
		
	diaActual = fechaActual.getDate();
	mesActual = fechaActual.getMonth() + 1;
	anioActual= fechaActual.getFullYear();
	
	if(mesActual && mesActual.toString().length == 1){
		mesActual = "0" + mesActual.toString();
	}
	
	if(diaActual && diaActual.toString().length == 1){
		diaActual = "0" + diaActual.toString();
	}
	
	fechaCompuesta = anioActual + "-" + mesActual + "-" + diaActual;
	
	//Verifica si se selecciono previamente el examen
	if(cuentaInfusiones > 0){
		var idx = cuentaInfusiones-1;
		var componentes = document.getElementById('wtxtcomponentes'+idx);
		
		if(componentes && componentes.length){
			if(componentes.length == 0){
				puedeAgregar = false;	
			}
		}
	}

	if(puedeAgregar){
		var cntDetalle = document.getElementById("detInfusiones");

	cuentaInfusiones++;
	
	//Fila nueva
	var fila = document.createElement("tr");
	fila.setAttribute('id','trIn'+cuentaInfusiones);		
	
	//Columnas nuevas
	var columna1 = document.createElement("td");		//Acciones
	var columna2 = document.createElement("td");		//Fecha de solicitud
	var columna3 = document.createElement("td");		//Componentes de la infusión
	var columna4 = document.createElement("td");		//Observaciones infusión

	columna2.setAttribute('align','center');
	
	/******************************************************************************
	CONTENIDO DE LAS COLUMNAS
	*******************************************************************************/
	//Link de grabar
	var link1 = document.createElement("a");
	link1.setAttribute('href','#null');
	if(!esIE){
		link1.setAttribute('onClick', 'javascript:grabarInfusion('+ cuentaInfusiones +');');
	}else{
		link1.onclick  = new Function('evt','javascript:grabarInfusion('+ cuentaInfusiones +');');
	}
	
	var img1 = document.createElement("img");
	img1.setAttribute('src','../../images/medical/root/grabar.png');

	link1.appendChild(img1);
	
	//Link de borrar	
	var link2 = document.createElement("a");
	link2.setAttribute('href','#null');
	if(!esIE){
		link2.setAttribute('onClick','javascript:quitarInfusion('+ cuentaInfusiones +');');
	}else{
		link2.onclick  = new Function('evt','javascript:quitarInfusion('+ cuentaInfusiones +');');
	}
	
	var img2 = document.createElement("img");
	img2.setAttribute('src','../../images/medical/root/borrar.png');

	link2.appendChild(img2);
	
	//Ocultos
	var oculto1 = document.createElement("input");
	oculto1.setAttribute('type','hidden');
	oculto1.setAttribute('name','wmodificado2'+cuentaInfusiones);
	oculto1.setAttribute('id','wmodificado2'+cuentaInfusiones);
	oculto1.setAttribute('value','S');

	var oculto2 = document.createElement("input");
	oculto2.setAttribute('type','hidden');
	oculto2.setAttribute('name','windiceliq'+parseInt(cantFilas-1));
	oculto2.setAttribute('id','windiceliq'+parseInt(cantFilas-1));
	oculto2.setAttribute('value',cuentaInfusiones);
	
	//Anexo de fecha solicitado examen
	var fliq = document.createElement("input");
	fliq.setAttribute('id','wfliq'+cuentaInfusiones);
	fliq.setAttribute('name','wfliq'+cuentaInfusiones);
	fliq.setAttribute('type','text');
	fliq.setAttribute('size','10');
	fliq.setAttribute('value',fechaCompuesta);
	fliq.setAttribute('readOnly','readonly');
	fliq.className = 'campo2';
		
	var btnfliq = document.createElement("input");
	btnfliq.setAttribute('type','button');
	btnfliq.setAttribute('id','btnFechaLiq'+cuentaInfusiones);
	btnfliq.setAttribute('name','btnFechaLiq'+cuentaInfusiones);
	btnfliq.setAttribute('alt','Haga doble click para desplegar el calendario');
	btnfliq.setAttribute('value','*');
		
	if(!esIE){	
		btnfliq.setAttribute('onClick', 'javascript:calendario4('+cuentaInfusiones+');');
	}else {						
		btnfliq.onclick = new Function('evt','javascript:calendario4('+cuentaInfusiones+')');
	} 
	
	//Select multiple de componentes
	var componentes = document.createElement("select");
	componentes.setAttribute('id','wtxtcomponentes' + cuentaInfusiones);
	componentes.setAttribute('multiple','multiple');
	componentes.setAttribute('size','5');
	if(!esIE){
		componentes.setAttribute('onDblClick', 'javascript:quitarComponente('+cuentaInfusiones+');');
	}else{
		componentes.ondblclick  = new Function('evt','quitarComponente('+cuentaInfusiones+');');
	}

	//Observaciones
	var observaciones = document.createElement("textarea");
	observaciones.setAttribute('id','wobscomponentes' + cuentaInfusiones);
	observaciones.setAttribute('rows','2');
	observaciones.setAttribute('cols','60');
	/*
	if(!esIE){
		observaciones.setAttribute('onKeyPress', 'return validarEntradaAlfabetica(event);');
	}else{
		observaciones.onkeypress = new Function('evt','return validarEntradaAlfabetica(event);');
	}*/
		
	/*******************************************************************************
	ANEXAR CONTENIDO A LAS COLUMNAS
	********************************************************************************/
//	columna1.appendChild(link1);
	columna1.appendChild(link2);
	columna1.appendChild(oculto1);
	columna1.appendChild(oculto2);
	columna2.appendChild(fliq);
	columna2.appendChild(btnfliq);
	columna3.appendChild(componentes);
	columna4.appendChild(observaciones);
	
	/*******************************************************************************
	ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
	********************************************************************************/
	fila.appendChild(columna1);
	fila.appendChild(columna2);
	fila.appendChild(columna3);
	fila.appendChild(columna4);
	
	//ANEXAR LA FILA A LA TABLA
	cntDetalle.appendChild(fila);
	
	cuentaInfusiones++;
	} else {
		alert('Por favor ingrese los componentes del liquido endovenoso antes de crear uno nueva.');
	}
}
/*****************************************************************************************************************************
 * Agrega dinámicamente una nueva fila para un examen de laboratorio nuevo
 ******************************************************************************************************************************/
function agregarExamen(){
	var puedeAgregar = true;
	
	if(puedeAgregar){
	var cntDetalle = document.getElementById("detExamenes");

	//Fecha actual automatica 
	var fechaActual = new Date();
		
	diaActual = fechaActual.getDate();
	mesActual = fechaActual.getMonth() + 1;
	anioActual= fechaActual.getFullYear();
	
	if(mesActual && mesActual.toString().length == 1){
		mesActual = "0" + mesActual.toString();
	}
	
	if(diaActual && diaActual.toString().length == 1){
		diaActual = "0" + diaActual.toString();
	}
	
	fechaCompuesta = anioActual + "-" + mesActual + "-" + diaActual;
	
	//Fila nueva
	var fila = document.createElement("tr");
	fila.setAttribute('id','trEx'+cuentaExamenes);		
	
	//Columnas nuevas
	var columna1 = document.createElement("td");		//Acciones
	var columna2 = document.createElement("td");		//Examenes
	var columna3 = document.createElement("td");		//Observaciones
	var columna4 = document.createElement("td");		//Estado
	var columna5 = document.createElement("td");		//Fecha solicitado examen

	columna5.setAttribute('align','center');
	
	/******************************************************************************
	CONTENIDO DE LAS COLUMNAS
	*******************************************************************************/
	//Link de grabar
	var link1 = document.createElement("a");
	link1.setAttribute('href','#null');
	if(!esIE){
		link1.setAttribute('onClick','javascript:grabarExamen('+ cuentaExamenes +');');
	}else{
		link1.onclick  = new Function('evt','javascript:grabarExamen('+ cuentaExamenes +');');
	}
	
	var img1 = document.createElement("img");
	img1.setAttribute('src','../../images/medical/root/grabar.png');

	link1.appendChild(img1);
	
	//Link de borrar	
	var link2 = document.createElement("a");
	link2.setAttribute('href','#null');
	if(!esIE){
		link2.setAttribute('onClick','javascript:quitarExamen('+ cuentaExamenes +');');
	}else{
		link2.onclick  = new Function('evt','javascript:quitarExamen('+ cuentaExamenes +');');
	}
	
	var img2 = document.createElement("img");
	img2.setAttribute('src','../../images/medical/root/borrar.png');

	link2.appendChild(img2);
	
	//Examen y estado del examen
	var tipoExamen = document.createElement("select");
	var estadosExamen = document.createElement("select");
	
	tipoExamen.setAttribute('id','wexamenlab'+cuentaExamenes);
	tipoExamen.className = 'seleccion';
	estadosExamen.setAttribute('id','westadoexamen'+cuentaExamenes);
	estadosExamen.className = 'seleccion';

	if(!esIE){
		var elemento = document.getElementById("wmexamenlab");
		var elemento2 = document.getElementById("wmestadosexamenlab");
								
		tipoExamen.innerHTML = document.getElementById("wmexamenlab").innerHTML;
		estadosExamen.innerHTML = document.getElementById("wmestadosexamenlab").innerHTML;
	} else {
		//Tipo examen
		var opcionesMaestro = document.getElementById("wmexamenlab").options;			
		var cont1 = 0;
		var opcionTmp = null;
			
		while(opcionesMaestro[cont1]){
			opcionTmp = document.createElement("option");		
			tipoExamen.options.add(opcionTmp);
			
			opcionTmp.innerText = opcionesMaestro[cont1].text;
			opcionTmp.value = opcionesMaestro[cont1].value;
			
			cont1++;
		}
			
		//Estados del examen
		var opcionesMaestro = document.getElementById("wmestadosexamenlab").options;			
		var cont1 = 0;
		var opcionTmp = null;
			
		while(opcionesMaestro[cont1]){
			opcionTmp = document.createElement("option");		
			estadosExamen.options.add(opcionTmp);
			
			opcionTmp.innerText = opcionesMaestro[cont1].text;
			opcionTmp.value = opcionesMaestro[cont1].value;
			
			cont1++;
		}
	}
	
	var observaciones = document.createElement("textarea");
	observaciones.setAttribute('id','wtxtobsexamen' + cuentaExamenes);
	observaciones.setAttribute('rows','2');
	observaciones.setAttribute('cols','60');
	if(!esIE){
		observaciones.setAttribute('onKeyPress', 'return validarEntradaAlfabetica(event);');
	}else{
		observaciones.onkeypress = new Function('evt','return validarEntradaAlfabetica(event);');
	}
		
	//Anexo de fecha solicitado examen
	var fsol = document.createElement("input");
	fsol.setAttribute('id','wfsol'+cuentaExamenes);
	fsol.setAttribute('name','wfsol'+cuentaExamenes);
	fsol.setAttribute('type','text');
	fsol.setAttribute('size','10');
	fsol.setAttribute('value',fechaCompuesta);
	fsol.setAttribute('readOnly','readonly');
	fsol.className = 'campo2';
		
	var btnFsol = document.createElement("input");
	btnFsol.setAttribute('type','button');
	btnFsol.setAttribute('id','btnFechaSol'+cuentaExamenes);
	btnFsol.setAttribute('name','btnFechaSol'+cuentaExamenes);
	btnFsol.setAttribute('alt','Haga doble click para desplegar el calendario');
	btnFsol.setAttribute('value','*');
		
	if(!esIE){	
		btnFsol.setAttribute('onClick', 'javascript:calendario3('+cuentaExamenes+');');
	}else {						
		btnFsol.onclick = new Function('evt','javascript:calendario3('+cuentaExamenes+')');
	} 
	
	//Ocultos
	var oculto = document.createElement("input");
	oculto.setAttribute('type','hidden');
	oculto.setAttribute('name','wmodificado4'+cuentaExamenes);
	oculto.setAttribute('id','wmodificado4'+cuentaExamenes);
	oculto.setAttribute('value','S');
	
	/*******************************************************************************
	ANEXAR CONTENIDO A LAS COLUMNAS
	********************************************************************************/
//	columna1.appendChild(link1);	
	columna1.appendChild(link2);
	columna2.appendChild(oculto);
	columna2.appendChild(tipoExamen);
	columna3.appendChild(observaciones);
	columna4.appendChild(estadosExamen);
	columna5.appendChild(fsol);
	columna5.appendChild(btnFsol);
	
	/*******************************************************************************
	ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
	********************************************************************************/
	fila.appendChild(columna1);
	fila.appendChild(columna2);
	fila.appendChild(columna3);	
	fila.appendChild(columna5);
	fila.appendChild(columna4);
	
	//ANEXAR LA FILA A LA TABLA
	cntDetalle.appendChild(fila);	
	
	cuentaExamenes++;
	} else {
		alert('Por favor ingrese la información del examen antes de agregar uno nuevo.');
	}
}
/*****************************************************************************************************************************
 * Valida antes de llamar la adicion de medico tratante
 ******************************************************************************************************************************/
function adicionarMedico(){	
	if(document.forms.forma.wselmed && document.forms.forma.wselmed.value != ''){
		var selMedico = document.getElementById("wselmed");
		var idMedico = selMedico.value;
		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;		
		var fechaKardex = document.forms.forma.wfechagrabacion.value;
		var usuario = document.forms.forma.usuario.value;
		
		var vecDatosMedico = selMedico.value.split("-");
		var tipoDocumento = vecDatosMedico[0];
		var nroDocumento = vecDatosMedico[1];
		var nombreMedico = vecDatosMedico[2];
		var codigoEspecialidad = vecDatosMedico[3];
			
		var interconsultante = document.getElementById("wchkmedint").checked ? "on" : "off";
		
		insertarMedicoTratante(idMedico, tipoDocumento, nroDocumento, historia, ingreso, fechaKardex, interconsultante, usuario, codigoEspecialidad, nombreMedico);
	} else {
		alert("Debe seleccionar un médico");
	}
}
/*****************************************************************************************************************************
 * Validación de la grabación de una nueva dieta
 ******************************************************************************************************************************/
function adicionarDieta(){
	if(document.forms.forma.wseldieta && document.forms.forma.wseldieta.value != ''){
		var selDieta = document.getElementById("wseldieta");
		var idDieta = selDieta.value;
		var nombreDieta = selDieta.options[selDieta.selectedIndex].text;
		var colDietas = document.forms.forma.colDietas;
		var fecha = document.forms.forma.wfechagrabacion.value;
		
		//Historia e ingreso
		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;
		
		var usuario = document.forms.forma.usuario.value;
		var cntDietas = document.getElementById('cntDietas');
			
		var elementoValidar = document.getElementById("Die"+idDieta);
		
		if((colDietas[idDieta] == null || colDietas[idDieta] == '') && !elementoValidar){
			cntDietas.innerHTML += "<span id='Die"+idDieta+"' class=vinculo>" + '<a onClick="javascript:quitarDieta('+"'"+idDieta+"'"+');" href="#null">' + nombreDieta + "</a><br/></span>";
			colDietas[idDieta] = nombreDieta;
			
			//Seccion ajax para hacer la insercion silenciosa
			insertarDietaKardex(idDieta, historia, ingreso, fecha, usuario);
		} else {
			alert("La dieta ya se encuentra asociada");
		}
	} else {
		alert("Debe seleccionar una dieta.");
	}
}
 /*****************************************************************************************************************************
 * Validación de la eliminación de una dieta
 ******************************************************************************************************************************/
 function quitarDieta(codigo){
	var fecha = document.forms.forma.wfecha.value;
	
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	
	var usuario = document.forms.forma.usuario.value;	
	
	if(confirm("Desea quitar la dieta seleccionada?")){
		eliminarDietaElemento(historia,ingreso,fecha,usuario,codigo);		
	}
	return false;
}
/*****************************************************************************************************************************
 * Valida la grabación de una infusión
 ******************************************************************************************************************************/
function grabarInfusion(idxElemento){
	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfechagrabacion.value;
	
	var fechaSolicitud = document.getElementById('wfliq'+idxElemento).value;	
	var componentes = document.getElementById('wtxtcomponentes'+idxElemento);	
	var obsComponentes = document.getElementById('wobscomponentes'+idxElemento).value;	
	var usuario = document.forms.forma.usuario.value;
	
	//Se separan los codigos de los componentes separados por ;
	var strComponentes = "";
	var cont1 = 0;
	
	while(cont1 < componentes.length){
		strComponentes += componentes.options[cont1].value+"-"+componentes.options[cont1].text+";";
		cont1++;
	}
	
	if(strComponentes == ""){
		strComponentes = componentes.value;
	}
	
	/*****
	VALIDACION DE CAMPOS OBLIGATORIOS
	******/
	var valido = true;
	
	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		grabarInfusionElemento(historia,ingreso,fecha,strComponentes,idxElemento,obsComponentes,usuario,idxElemento,fechaSolicitud);
	}
	return valido;
}
/*****************************************************************************************************************************
 * Validación antes de quitar un articulo de la lista de medicamentos
 ******************************************************************************************************************************/
function quitarArticulo(idxElemento, tipoProtocolo){
	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;
	
	//Se envia fecha y hora de inicio en caso de que el articulo sea duplicable
	if(document.getElementById('whfinicio'+tipoProtocolo+idxElemento)){
		var fechaInicio = document.getElementById('whfinicio'+tipoProtocolo+idxElemento);
		var fh = fechaInicio.value.split("a las:");
		var fini = fh[0];
		var hini = fh[1];
	}
	
	var descripcionArticulo = "";
	
	if(codigoArticulo.tagName == 'INPUT'){
		descripcionArticulo = codigoArticulo.value;
	} else {
		descripcionArticulo = codigoArticulo.innerHTML;
	}
	
	var cd = descripcionArticulo.split("-");
	codigoArticulo = cd[0];
	
	if(codigoArticulo == ''){
		var cntDetalleKardex = document.getElementById("detKardex"+tipoProtocolo);
		var filaEliminar = document.getElementById("tr"+tipoProtocolo+idxElemento);
		cntDetalleKardex.removeChild(filaEliminar);
		elementosDetalle--;
	} else {
		if( confirm("Esta seguro de eliminar el articulo "+descripcionArticulo + "?") ){		
			eliminarArticuloElemento(historia,ingreso,fecha,codigoArticulo,usuario,idxElemento,fini,hini,tipoProtocolo);
			elementosDetalle--;
		}
	}
}
/*****************************************************************************************************************************
 * Validación antes de quitar una infusión
 ******************************************************************************************************************************/
function quitarInfusion(idxElemento){
	var historia = document.forms.forma.whistoria.value;
	var fecha = document.forms.forma.wfecha.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;
	
	if( confirm("Esta seguro de quitar el liquido endovenoso # "+ idxElemento + "?") ){
		eliminarInfusionElemento(historia,ingreso,fecha,usuario,idxElemento);
	}
}
/*****************************************************************************************************************************
 * Validación antes de quitar un examen de laboratorio
 ******************************************************************************************************************************/
function quitarExamen(idxElemento){
	var codigoExamen = document.getElementById('wexamenlab'+idxElemento).value;
	var fecha = document.forms.forma.wfecha.value;
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;
	
	if( confirm("Esta seguro de eliminar el examen?") ){
		eliminarExamenElemento(historia,ingreso,codigoExamen,fecha,usuario,idxElemento);
	}
}
/*****************************************************************************************************************************
 * Validación de la grabación de un medicamento
 *****************************************************************************************************************************/
function grabarArticulo(idxElemento,tipoProtocolo){
	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fechaKardex = document.forms.forma.wfechagrabacion.value;
	var centroCostosGrabacion = document.forms.forma.centroCostosGrabacion.value;
	var prioridad = document.forms.forma.whusuariolactario.value;
	
	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);	
	var datosArticulos = "";
	var cantDosis = document.getElementById('wdosis'+tipoProtocolo+idxElemento);
	var unidadDosis = document.getElementById('wudosis'+tipoProtocolo+idxElemento);	
	var periodicidad = document.getElementById('wperiod'+tipoProtocolo+idxElemento);
	
	var equivHorasFrecuencia = periodicidad.value.substring(1,periodicidad.value.length);
	var condicion = document.getElementById('wcondicion'+tipoProtocolo+idxElemento);
	var formaFtica = document.getElementById('wfftica'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var horaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var via = document.getElementById('wviadmon'+tipoProtocolo+idxElemento);
	var confirmar = document.getElementById('wchkconf'+tipoProtocolo+idxElemento);
	var diasTto = document.getElementById('wdiastto'+tipoProtocolo+idxElemento);
	var dosMax = document.getElementById('wdosmax'+tipoProtocolo+idxElemento);
	var observacion = document.getElementById('wtxtobs'+tipoProtocolo+idxElemento);
	var usuario = document.forms.forma.usuario.value;	
	var primerKardex = (document.getElementById("wkardexnuevo") && document.getElementById("wkardexnuevo").value == 'S' ? "S" : "N");
	
	var unidadManejo = document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento) ? document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento).value : '';
	var vence = (document.getElementById('whvence'+tipoProtocolo+idxElemento) && document.getElementById('whvence'+tipoProtocolo+idxElemento).value == 'on' ? true : false);
	var cantFracciones = document.getElementById('whcmanejo'+tipoProtocolo+idxElemento) ? document.getElementById('whcmanejo'+tipoProtocolo+idxElemento).value : '';
	
	var noDispensar = document.getElementById('wchkdisp'+tipoProtocolo+idxElemento);
	
	var cantGrabar = 0;
	var cantidadManejo = 1;		//Este es el valor base de dispensacion	
	var dosisSuministrar = 0;	
	
	equivHorasFrecuencia = parseFloat(equivHorasFrecuencia);
	
	//Codigo articulo
	var cd = "";
	if(codigoArticulo.tagName == 'INPUT'){
		datosArticulos = codigoArticulo.value;
		cd = codigoArticulo.value.split("-");
	} else {
		datosArticulos = codigoArticulo.innerHTML;
		cd = codigoArticulo.innerHTML.split("-");
	}
	
	codigoArticulo = cd[0];
	var origenArticulo = cd[1];
	
	//Extraccion de la fecha y hora inicial de administracion
	var fh = fechaInicio.value.split("a las:");
	fechaInicio = fh[0];
	horaInicio = fh[1];
	
	//Conversion a hora entero para la hora
	var vecHora = horaInicio.split(":");
	var intHoraInicio = vecHora[0];
	
	/*****
	VALIDACION DE CAMPOS OBLIGATORIOS
	******/
	var valido = true;
	
	//Codigo articulo
	if(!codigoArticulo || codigoArticulo == ''){
		if( !noMostarMsjError )
			alert('Debe ingresar el codigo del articulo: '+ datosArticulos);
		valido = false;
	}
	
	//Dosis
	if(valido && (!cantDosis || cantDosis.value == '')){
		if( !noMostarMsjError )
			alert('Debe ingresar la cantidad de dosis a suministrar: '+ datosArticulos);
		valido = false;
	}
	
	if(valido && eval(cantDosis.value) <= 0){
		if( !noMostarMsjError )
			alert('La cantidad de dosis debe ser mayor que cero: '+ datosArticulos);
		valido = false;
	}
	
	//Unidad de Dosis
	if(valido && (!unidadDosis || unidadDosis.value == '')){
		if( !noMostarMsjError )
			alert('Debe seleccionar la unidad de dosis a suministrar: '+ datosArticulos);
		valido = false;
	}
	
	//Periodicidad o frecuencia
	if(valido && (!periodicidad || periodicidad.value == '')){
		if( !noMostarMsjError )
			alert('Debe seleccionar la frecuencia con la que se debe suministrar: '+ datosArticulos);
		valido = false;
	}
	
	//Forma farmaceutica
	if(valido && (!formaFtica || formaFtica.value == '')){
		formaFtica.value = '00';
//		alert('Debe ingresar la forma farmaceutica');
//		valido = false;
	}
	
	//Fecha de inicio del tratamiento
	if(valido && (!fechaInicio || fechaInicio == '')){
		if( !noMostarMsjError )
			alert('Debe ingresar la fecha de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}
	
	//Hora de inicio del tratamiento
	if(valido && (!horaInicio || horaInicio == '')){
		if( !noMostarMsjError )
			alert('Debe ingresar la hora de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}
	
	//Via de administracion
	if(valido && (!via || via.value == '')){
		if( !noMostarMsjError )
			alert('Debe seleccionar la vía de administración: '+ datosArticulos);
		valido = false;
	}
	
	//La fecha de inicio debe ser mayor o igual a la del kardex
	if(valido && (!esFechaMayorIgual(fechaInicio,fechaKardex))){
		if( !noMostarMsjError )
			alert('La fecha de inicio de administración del artículo debe ser mayor o igual a la fecha actual: '+ datosArticulos);
		valido = false;
	}
	
	var horaValidacion = new Date().getHours();
	
//	var fechaObj1 = new Date();
//	
//	var anioObj1 = fecha1.substring(0,4);
//	var mesObj1 = fecha1.substring(5,7);
//	var diaObj1 = fecha1.substring(8,10);
	
//	var fechaString = ;
	
//	fechaObj1.setFullYear(anioObj1);
//	fechaObj1.setMonth(eval(mesObj1-1)); 
//	fechaObj1.setDate(diaObj1);
	
	if(valido && horaValidacion >= 6){
		if(intHoraInicio != 0){
			if(intHoraInicio < (horaValidacion - 2)){
//				valido = false;
			}
		}
	}
	
	//Validacion de fechas. No se permite fecha actual y cero horas :D
	var valHoraInicio = intHoraInicio; 
	var vecFechaInicio = fechaInicio.split("-");
	var fval = new Date();
	var fcomp = new Date().getHours();
	fcomp = fcomp - 2;
	fval.setHours(fcomp);
	
	if(intHoraInicio && intHoraInicio.substring(0,1) == "0"){
		valHoraInicio = intHoraInicio.substring(1,2);
	}
	
	var valDiaInicio = vecFechaInicio[2];
	if(valDiaInicio && valDiaInicio.substring(0,1) == "0"){
		valDiaInicio = valDiaInicio.substring(1,2);
	}
	
	if(valido && (parseInt(valDiaInicio)-parseInt(new Date().getDate())==0)){
		if(parseInt(valHoraInicio) == 0 && parseInt(new Date().getHours()) != 0){
			alert('La fecha de inicio de administración del artículo debe ser mayor o igual a la fecha actual: '+ datosArticulos);
			valido = false;
		}
	}
	
	var fechaInicioAnterior = fechaInicio;
	var horaInicioAnterior = horaInicio;
	
	//Checkbuttons
	var artConfirmado = false;
	var artNoDispensar = false;
	
	if(confirmar && confirmar.checked){
		artConfirmado = true;
	}
	
	if(noDispensar && noDispensar.checked){
		artNoDispensar = true;
	}
	
	//Validacion de que no se encuentre un mismo articulo con la misma fecha y hora de inicio
	var datosArticulo = "";
	var codigoValidacionArticulo = "";
	var fechaHoraValidacionArticulo = "";
	
	for(var indiceArticulo=0;document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo);indiceArticulo++){
		if(indiceArticulo != idxElemento){
			if(document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).tagName == 'DIV'){
				datosArticulo = document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).innerHTML;
			} else {
				datosArticulo = document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).value;
			}
			codigoValidacionArticulo = datosArticulo.split("-");
			fechaHoraValidacionArticulo = document.getElementById('whfinicio'+tipoProtocolo+indiceArticulo) ? document.getElementById('whfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:") : document.getElementById('wfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:");
			
			if(codigoValidacionArticulo[0] == codigoArticulo && fechaHoraValidacionArticulo[0] == fechaInicioAnterior && fechaHoraValidacionArticulo[1].split(":")[0] == horaInicioAnterior.split(":")[0]){
				alert("El articulo "+datosArticulo+" ya se encuentra en la lista con la misma fecha y hora de inicio, NO SE PUEDE GRABAR");
				valido = false;
				break;
			}
		}
	}
	
	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		grabarArticuloElemento(historia,ingreso,fechaKardex,codigoArticulo,cantDosis.value,unidadDosis.value,periodicidad.value,formaFtica.value,fechaInicio,horaInicio,via.value,artConfirmado,diasTto.value,observacion.value,origenArticulo,usuario,condicion.value,dosMax.value,cantGrabar,unidadManejo,cantidadManejo,primerKardex,equivHorasFrecuencia,fechaInicioAnterior,horaInicioAnterior,artNoDispensar,tipoProtocolo,centroCostosGrabacion,prioridad,idxElemento);
	}
	else{
//		alert( "Holaaaaaaa11111......"+datosArticulos );
		window.onbeforeunload = salida;
		articulosSinGrabar[ articulosSinGrabar.length ] = datosArticulos;	//código del articulo
	}
	
	return valido;
}
 /*****************************************************************************************************************************
  * Validación de la grabación de un medicamento para evitar validacion del dia actual contra inicio del tratamiento
  ******************************************************************************************************************************/ 
function grabarArticuloSinValidacion(idxElemento,tipoProtocolo){
	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fechaKardex = document.forms.forma.wfechagrabacion.value;
	var centroCostosGrabacion = document.forms.forma.centroCostosGrabacion.value;
	var prioridad = document.forms.forma.whusuariolactario.value;
	
	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);	
	var datosArticulos = "";
	var cantDosis = document.getElementById('wdosis'+tipoProtocolo+idxElemento);
	var unidadDosis = document.getElementById('wudosis'+tipoProtocolo+idxElemento);	
	var periodicidad = document.getElementById('wperiod'+tipoProtocolo+idxElemento);
	var equivHorasFrecuencia = periodicidad.value.substring(1,periodicidad.value.length);
	var condicion = document.getElementById('wcondicion'+tipoProtocolo+idxElemento);
	var formaFtica = document.getElementById('wfftica'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var horaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var via = document.getElementById('wviadmon'+tipoProtocolo+idxElemento);
	var confirmar = document.getElementById('wchkconf'+tipoProtocolo+idxElemento);
	var diasTto = document.getElementById('wdiastto'+tipoProtocolo+idxElemento);
	var dosMax = document.getElementById('wdosmax'+tipoProtocolo+idxElemento);
	var observacion = document.getElementById('wtxtobs'+tipoProtocolo+idxElemento);
	var usuario = document.forms.forma.usuario.value;
	var primerKardex = (document.getElementById("wkardexnuevo") && document.getElementById("wkardexnuevo").value == 'S' ? "S" : "N");
	
	var unidadManejo = document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento).value;
	var vence = (document.getElementById('whvence'+tipoProtocolo+idxElemento).value == 'on' ? true : false);
	var cantFracciones = document.getElementById('whcmanejo'+tipoProtocolo+idxElemento).value;
	
	var noDispensar = document.getElementById('wchkdisp'+tipoProtocolo+idxElemento);
	
	var cantGrabar = 0;
	var cantidadManejo = 1;		//Este es el valor base de dispensacion	
	var dosisSuministrar = 0;
	
	equivHorasFrecuencia = parseFloat(equivHorasFrecuencia);
	
	//Extraccion de la fecha y hora inicial de administracion
	var vecFechaAnt = document.getElementById("whfinicio"+tipoProtocolo+idxElemento).value.split("a las:");
	var fechaInicioAnterior = vecFechaAnt[0];
	var horaInicioAnterior = vecFechaAnt[1];
	
	//Conversion a hora entero para la hora
	var vecHora = horaInicioAnterior.split(":");
	var intHoraInicio = vecHora[0];
	
	//Extraccion del codigo articulo propiamente dicho
	var cd = "";
	if(codigoArticulo.tagName == 'INPUT'){
		datosArticulos = codigoArticulo.value;
		cd = codigoArticulo.value.split("-");
	} else {
		datosArticulos = codigoArticulo.innerHTML;
		cd = codigoArticulo.innerHTML.split("-");
	}
	
	codigoArticulo = cd[0];
	var origenArticulo = cd[1];
	
	//Extraccion de la fecha y hora inicial de administracion
	var fh = fechaInicio.value.split("a las:");
	fechaInicio = fh[0];
	horaInicio = fh[1];
	
	/*****
	VALIDACION DE CAMPOS OBLIGATORIOS
	******/
	var valido = true;
	
	//Codigo articulo
	if(!codigoArticulo || codigoArticulo == ''){
		if( !noMostarMsjError )
			alert('Debe ingresar el codigo del articulo: '+datosArticulos);
		valido = false;
	}
	
	//Dosis
	if(valido && (!cantDosis || cantDosis.value == '')){
		if( !noMostarMsjError )
			alert('Debe ingresar la cantidad de dosis a suministrar: '+datosArticulos);
		valido = false;
	}
	
	if(valido && eval(cantDosis.value) <= 0){
		if( !noMostarMsjError )
			alert('La cantidad de dosis debe ser mayor que cero: '+datosArticulos);
		valido = false;
	}
	
	//Unidad de Dosis
	if(valido && (!unidadDosis || unidadDosis.value == '')){
		if( !noMostarMsjError )
			alert('Debe seleccionar la unidad de dosis a suministrar: '+datosArticulos);
		valido = false;
	}
	
	//Periodicidad o frecuencia
	if(valido && (!periodicidad || periodicidad.value == '')){
		if( !noMostarMsjError )
			alert('Debe seleccionar la frecuencia con la que se debe suministrar: '+datosArticulos);
		valido = false;
	}
	
	//Forma farmaceutica
	if(valido && (!formaFtica || formaFtica.value == '')){
		formaFtica.value = '00';
//		alert('Debe ingresar la forma farmaceutica');
//		valido = false;
	}
	
	//Fecha de inicio del tratamiento
	if(valido && (!fechaInicio || fechaInicio == '')){
		if( !noMostarMsjError )
			alert('Debe ingresar la fecha de inicio del tratamiento: '+datosArticulos);
		valido = false;
	}
	
	//Hora de inicio del tratamiento
	if(valido && (!horaInicio || horaInicio == '')){
		if( !noMostarMsjError )
			alert('Debe ingresar la hora de inicio del tratamiento: '+datosArticulos);
		valido = false;
	}
	
	//Via de administracion
	if(valido && (!via || via.value == '')){
		if( !noMostarMsjError )
			alert('Debe seleccionar la vía de administración: '+datosArticulos);
		valido = false;
	}
	
	//Checkbuttons
	var artConfirmado = false;
	var artNoDispensar = false;
	
	if(confirmar && confirmar.checked){
		artConfirmado = true;
	}
	
	if(noDispensar && noDispensar.checked){
		artNoDispensar = true;
	}
	
	//Validacion de que no se encuentre un mismo articulo con la misma fecha y hora de inicio
	var datosArticulo = "";
	var codigoValidacionArticulo = "";
	var fechaHoraValidacionArticulo = "";
	
	for(var indiceArticulo=0;document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo);indiceArticulo++){
		if(indiceArticulo != idxElemento){
			if(document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).tagName == 'DIV'){
				datosArticulo = document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).innerHTML;
			} else {
				datosArticulo = document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).value;
			}
			codigoValidacionArticulo = datosArticulo.split("-");
			fechaHoraValidacionArticulo = document.getElementById('whfinicio'+tipoProtocolo+indiceArticulo) ? document.getElementById('whfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:") : document.getElementById('wfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:");
			
			if(codigoValidacionArticulo[0] == codigoArticulo && fechaHoraValidacionArticulo[0] == fechaInicioAnterior && fechaHoraValidacionArticulo[1].split(":")[0] == horaInicioAnterior.split(":")[0]){
				alert("El articulo "+datosArticulo+" ya se encuentra en la lista con la misma fecha y hora de inicio, NO SE PUEDE GRABAR");
				valido = false;
				break;
			}
		}
	}
	
	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		grabarArticuloElemento(historia,ingreso,fechaKardex,codigoArticulo,cantDosis.value,unidadDosis.value,periodicidad.value,formaFtica.value,fechaInicio,horaInicio,via.value,artConfirmado,diasTto.value,observacion.value,origenArticulo,usuario,condicion.value,dosMax.value,cantGrabar,unidadManejo,cantidadManejo,primerKardex,equivHorasFrecuencia,fechaInicioAnterior,horaInicioAnterior,artNoDispensar,tipoProtocolo,centroCostosGrabacion,prioridad,idxElemento);
	}
	else{
//		alert( "Holaaaaaaa222222......"+datosArticulos );
		articulosSinGrabar[ articulosSinGrabar.length ] = datosArticulos;	//código del articulo
		window.onbeforeunload = salida;
	}
	
	return valido;
}
 /*****************************************************************************************************************************
  * Validación de la grabación de un medicamento unicamente para uso del perfil
  ******************************************************************************************************************************/
 function grabarArticuloPerfil(idxElemento){
	 
 	//Variables de la fila a actualizar
 	var historia = document.forms.forma.whistoria.value;
 	var ingreso = document.forms.forma.wingreso.value;
 	var fechaKardex = document.forms.forma.wfechagrabacion.value;
 	
 	var codigoArticulo = document.getElementById('wnmmedact'+idxElemento);
 	var codigoArticuloNuevo = document.getElementById('wnmmed'+idxElemento);
 	
 	var diasTto = document.getElementById('wdiastto'+idxElemento);
 	var dosisMaximas = document.getElementById('wdosmax'+idxElemento);
 	var observacion = document.getElementById('wtxtobs'+idxElemento);
 	var via = document.getElementById('wviadmon'+idxElemento);
 	
 	var fechaInicio = document.getElementById('whfinicio'+idxElemento);
 	var prioridad = (document.getElementById('wchkpri'+idxElemento) && document.getElementById('wchkpri'+idxElemento).checked == true) ? "on" : "off";
 	
 	var usuario = document.forms.forma.usuario.value;
 	
 	var unidadDosis = document.getElementById('wudosis'+idxElemento).value; 
 	var formaFarm = document.getElementById('wfftica'+idxElemento).value; 
 	var autorizadoCtc = document.getElementById('wautctc'+idxElemento);
 	
 	var origenArticuloNuevo = "";
 	
 	if(autorizadoCtc){
 		autorizadoCtc = autorizadoCtc.value;
 	} else {
 		autorizadoCtc = "";
 	}
 	
 	//Extrae el codigo del articulo actual 
 	var cd = "";
 	if(codigoArticulo.tagName == 'INPUT'){
 		cd = codigoArticulo.value.split("-");
 	} else {
 		cd = codigoArticulo.innerHTML.split("-");
 	}
 	
 	codigoArticulo = cd[0];
 	var origenArticulo = cd[1];
 	
 	//Extraccion de la fecha y hora inicial de administracion
 	var fh = fechaInicio.value.split("a las:");
 	var fechaInicio = fh[0];
 	var horaInicio = fh[1];
 	
 	//Extrae el codigo del articulo nuevo 
 	var cdNuevo = "";
 	if(codigoArticuloNuevo){
 		if(codigoArticuloNuevo.tagName == 'INPUT'){
 			cdNuevo = codigoArticuloNuevo.value.split("-");
 		} else {
 			cdNuevo = codigoArticuloNuevo.innerHTML.split("-");
 		}
 		codigoArticuloNuevo = cdNuevo[0];
 		origenArticuloNuevo = cdNuevo[1];
 	}
 	
 	/*****	
 	VALIDACION DE CAMPOS OBLIGATORIOS
 	******/
 	var valido = true;
 	
 	//Codigo articulo actual
 	if(!codigoArticulo || codigoArticulo == ''){
 		if( !noMostarMsjError )
 		alert('No se capturó el codigo del articulo actual');
 		valido = false;
 	}
 	
 	//Si se va a reemplazar el articulo se debe avisar
	if(valido && (codigoArticuloNuevo && codigoArticuloNuevo != '')){	
		if(confirm('Esta seguro de reemplazar el artículo ' + codigoArticulo+'-'+cd[2]+ ' con ' + document.getElementById('wnmmed'+idxElemento).value + '?')){
			//Valido que el articulo que voy a reemplazar no este en la lista de articulos actual
			
			var temp = "";
			var cont1 = 1;
			var elemento = document.getElementById("wnmmedact"+cont1);
			
			while(elemento){				
				
				if(elemento && elemento.innerHTML != ''){
					temp = elemento.innerHTML.split("-");
					/*
					if(temp[0] == codigoArticuloNuevo){
						alert("No se puede reemplazar el articulo " + cd[2] + " por el articulo " + cdNuevo[2] + " este ya se encuentra en la lista de artículos.");
						valido = false;
						break;
					}
					*/
				}				
				cont1++;
				elemento = document.getElementById("wnmmedact"+cont1);
			}
			
		} else {
			valido = false;
		}
	}
 	
 	/***
 	GRABACION DEL ARTICULO
 	***/
 	if(valido){
 		grabarArticuloPerfilElemento(historia,ingreso,fechaKardex,codigoArticulo,codigoArticuloNuevo,diasTto.value,observacion.value,usuario,idxElemento,unidadDosis,formaFarm,origenArticuloNuevo,via.value,dosisMaximas.value,prioridad,fechaInicio,horaInicio,autorizadoCtc);
 	}
 }
/*****************************************************************************************************************************
 * Validación para realizar la grabación de un examen
 ******************************************************************************************************************************/
function grabarExamen(idxElemento){
	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfechagrabacion.value;
	var codExamen = document.getElementById('wexamenlab'+idxElemento).value;
	var nomExamen = document.getElementById('wexamenlab'+idxElemento).text;
	var observaciones = document.getElementById('wtxtobsexamen'+idxElemento).value;
	var estadoExamen = document.getElementById('westadoexamen'+idxElemento).value;
	var fechaDeSolicitado = document.getElementById('wfsol'+idxElemento).value;
	var usuario = document.forms.forma.usuario.value;
	var existe = false;

	/*****
	VALIDACION DE CAMPOS OBLIGATORIOS
	******/
	var valido = true;
	
	if(codExamen == ''){
		alert("Debe especificar un examen");
		valido = false;
	}
	
	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		if(!existe){
			grabarExamenElemento(codExamen,nomExamen,historia,ingreso,fecha,observaciones,estadoExamen,fechaDeSolicitado,usuario,idxElemento);
		} else {
			alert('El examen ya se encuentra en la lista.  Por favor seleccione otro');
		}
	}
	return valido;
}


 /*****************************************************************************************************************************
 * ----------OPERACIONES DE SELECCION Y CONSULTA-----------
 ******************************************************************************************************************************/
function consultarMedicamento(){	
	var contenedor = document.getElementById('cntMedicamento');
	var tipoMedida = document.getElementById("wunidadmed");
	
	var tipoArticulo = ""; 
	var tipoProtocolo = "";
		
	var ccostos = '',grupos = '';
	
	if(document.forms.forma.centroCostosUsuario){
		ccostos = document.forms.forma.centroCostosUsuario.value;
	}
	
	if(document.forms.forma.whgrupos){
		grupos = document.forms.forma.whgrupos.value;
	}
	
	var parametros = "";
		
 	var i = 0;
 	//Nombre (C)omercial o (G)enerico
   	for (i=0;i<document.forms.forma.wtipoart.length;i++){ 
    	if (document.forms.forma.wtipoart[i].checked){
    		tipoArticulo = document.forms.forma.wtipoart[i].value;
      		break; 
    	}
    } 
    
   	for (i=0;i<document.forms.forma.wtipoprot.length;i++){ 
    	if (document.forms.forma.wtipoprot[i].checked){
    		tipoProtocolo = document.forms.forma.wtipoprot[i].value;
      		break; 
    	}
    } 
   	
	/*Tipos de protocolos
	 * N:  Normal, el articulo se consulta normalmente.  (maestro de la central y maestro del servicio)
	 * A:  Analgesia, el articulo se agrega a los protocolos de analgesia (tipo especial de articulo A)
	 * U:  nUtricion, el articulo se agrega a los protocolos de nutricion (tipo especial de articulos U)
	 * Q:  Quimioterapia, el articulo se agrega a los protocolos de quimioterapia (tipo especial de articulos Q)
	 */
	if(document.forms.forma.wnommed.value == ''){
		parametros = "consultaAjaxKardex=02&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo="+document.forms.forma.wcodmed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos+"&tipoProtocolo="+tipoProtocolo; 
	} else {
		parametros = "consultaAjaxKardex=03&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre="+document.forms.forma.wnommed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos+"&tipoProtocolo="+tipoProtocolo;
	}

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){
				contenedor.innerHTML=ajax.responseText;				
			} 
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

function consultarMedicamentoPerfil(){
	var contenedor = document.getElementById('cntMedicamento');
	var imagen = document.getElementById('imgCodMed');
	
	var ccostos = '',grupos = '*';
	
	if(document.forms.forma.centroCostosUsuario){
		ccostos = document.forms.forma.centroCostosUsuario.value;
	}
	
	var parametros = "";
		
	imagen.style.display = "block";
		
 	var i = 0;
   	for (i=0;i<document.forms.forma.wtipoart.length;i++){ 
    	if (document.forms.forma.wtipoart[i].checked) 
      		break; 
    } 
    	
    var tipoArticulo = document.forms.forma.wtipoart[i].value;   	 
	var tipoMedida = document.getElementById("wunidadmed");
		
	if(document.forms.forma.wnommed.value == ''){
		parametros = "consultaAjaxKardex=02&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo="+document.forms.forma.wcodmed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos; 
	} else {
		parametros = "consultaAjaxKardex=03&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre="+document.forms.forma.wnommed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos;
	}

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){ 
				contenedor.innerHTML=ajax.responseText;				
				imagen.style.display = "none";
			} 
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
 /*****************************************************************************************************************************
 * ----------OPERACIONES DE SELECCION Y CONSULTA-----------
 ******************************************************************************************************************************/
function consultarComponente(){
	var contenedor = document.getElementById('cntComponente');
	var imagen = document.getElementById('imgCodCom');
	var parametros = "";
		
//	imagen.style.display = "block";
		
 	var i = 0;
   	for (i=0;i<document.forms.forma.wtipocom.length;i++){ 
    	if (document.forms.forma.wtipocom[i].checked) 
      		break; 
    } 
    	
    var tipoArticulo = document.forms.forma.wtipocom[i].value;   	 
	var tipoMedida = document.getElementById("wunidadcom");
		
	if(document.forms.forma.wnomcom.value == ''){
		parametros = "consultaAjaxKardex=09&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo=" + document.forms.forma.wcodcom.value+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value; 
	} else {
		parametros = "consultaAjaxKardex=10&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre=" + document.forms.forma.wnomcom.value+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value;
	}

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){ 
				contenedor.innerHTML=ajax.responseText;				
				imagen.style.display = "none";
			} 
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
 /*****************************************************************************************************************************
 * ----------OPERACIONES DE INSERCION Y MODIFICACION-----------
 ******************************************************************************************************************************/
/*****************************************************************************************************************************
 * Llamada ajax para inserción del esquema dextrometer
 ******************************************************************************************************************************/
function grabarEsquemaDextrometerElemento(historia, ingreso, fechaKardex, codInsulina, frec, codEsquema, dosis, uDosis, via, obs, usuario, actualizaIntervalos){
	
	var actualizaInt = "";
	
	if(actualizaIntervalos == true){
		actualizaInt = "on";
	} else {
		actualizaInt = "off";
	}
	
	var parametros = ""; 
		
	parametros = "consultaAjaxKardex=22&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
					+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&fecha="+fechaKardex
					+"&codInsulina="+codInsulina+"&frecuencia="+frec+"&codEsquema="+codEsquema+"&arrDosis="+dosis
					+"&arrUDosis="+uDosis+"&arrVia="+via+"&arrObservaciones="+obs+"&actualizaIntervalos="+actualizaInt;
	
	var mensaje = "";
	
	try{
//		$.blockUI({ message: $('#msjEspere') });		
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		switch (ajax.responseText) {
			case '1':  
				mensaje = "Esquema dextrometer modificado con exito";
				if(actualizaIntervalos){
					if(document.getElementById('cntEsquemaActual')){
						document.getElementById('cntEsquemaActual').style.display = "none";
					}
				}
				
				if(document.getElementById('btnQuitarEsquema')){
					document.getElementById('btnQuitarEsquema').disabled = false;
				}
				
				break;
			default:
				mensaje = "No se pudo grabar el esquema dextrometer";
				break;
		}
		
//		$.unblockUI();
//		$.growlUI('',mensaje);
		
		/*
		ajax.onreadystatechange=function()
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				switch (ajax.responseText) {
					case '1':  
						mensaje = "Esquema dextrometer modificado con exito";
						break;
					default:
						mensaje = "No se pudo grabar el esquema dextrometer";
						break;
				}
			}
			
			if(actualizaIntervalos){
				if(document.getElementById('cntEsquemaActual')){
					document.getElementById('cntEsquemaActual').style.display = "none";
				}
			}
			
			if(document.getElementById('btnQuitarEsquema')){
				document.getElementById('btnQuitarEsquema').disabled = false;
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		*/
		
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

/*****************************************************************************************************************************
 * Llamada ajax para inserción del medico tratante
 ******************************************************************************************************************************/
function insertarMedicoTratante(idRegistro, tipoDocumento, nroDocumento, historia, ingreso, fechaKardex, interconsultante, usuario, codigoEspecialidad,nombreMedico){
	var contenedor = document.getElementById('cntMedicos');
	var parametros = ""; 
		
	parametros = "consultaAjaxKardex=06&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoDocumento="+tipoDocumento
			+"&numeroDocumento="+nroDocumento+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&idRegistro="+idRegistro
			+"&fecha="+fechaKardex+"&interconsultante="+interconsultante+"&codigoEspecialidad="+codigoEspecialidad;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{
				switch (ajax.responseText) {
					case '1':	
						alert('El medico tratante ya se encontraba asociado');
						break;
					case '2':
						var subIdMedico = tipoDocumento+nroDocumento+codigoEspecialidad;
						
						if(interconsultante == "on"){
							contenedor.innerHTML += "<span id='Med"+subIdMedico+"' class='vinculo'><a href='#null' onClick='javascript:quitarMedico("+"\""+tipoDocumento+"-"+nroDocumento+"-"+codigoEspecialidad+"\""+");'>" + nombreMedico + "(Interconsulta)</a><br/></span>";
						} else {
							contenedor.innerHTML += "<span id='Med"+subIdMedico+"' class='vinculo'><a href='#null' onClick='javascript:quitarMedico("+"\""+tipoDocumento+"-"+nroDocumento+"-"+codigoEspecialidad+"\""+");'>" + nombreMedico + "</a><br/></span>";
						}
						break;
					default:
						alert('No se pudo asociar el medico tratante');
						break;
				}
				$.unblockUI();
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
 /*****************************************************************************************************************************
 * Llamada de inserción de dieta nueva en el kardex
 ******************************************************************************************************************************/
function insertarDietaKardex(idRegistro, historia, ingreso, fechaKardex, usuario){
	var contenedor = document.getElementById('cntMedicamento');
	var parametros = ""; 
		
	parametros = "consultaAjaxKardex=14&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
			+"&codUsuario="+usuario+"&idRegistro="+idRegistro+"&fecha="+fechaKardex;
		
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){ 
				contenedor.innerHTML=ajax.responseText;
			} 
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
 /*****************************************************************************************************************************
 * Llamada ajax para la grabación de una nueva infusión
 ******************************************************************************************************************************/
 function grabarInfusionElemento(historia,ingreso,fecha,strComponentes,consecutivo,obsComponentes,usuario,idElemento,fechaSolicitud){
	var parametros = "";
		
	parametros = "consultaAjaxKardex=11&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&fecha="+fecha+"&componentes="+strComponentes+"&consecutivo="+consecutivo+"&codUsuario="+usuario+"&observaciones="+obsComponentes+"&fechaSolicitud="+fechaSolicitud;

	var mensaje = "";
					
	try{
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",false); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		//
		if(ajax.responseText == '0'){
			mensaje = "No se pudo grabar el liquido endovenoso";
		}
		
		if(ajax.responseText == '1'){
			var fila = document.getElementById('trIn'+idElemento);
			fila.className = 'fila1';
			
			mensaje = "El liquido endovenoso se ha grabado correctamente";
		}
		
		if(ajax.responseText == '2'){
			mensaje = "El liquido endovenoso se ha modificado correctamente";
		}
		
		/*
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				if(ajax.responseText == '0'){
					mensaje = "No se pudo grabar el liquido endovenoso";
				}
				
				if(ajax.responseText == '1'){
					var fila = document.getElementById('trIn'+idElemento);
					fila.className = 'fila1';
					
					mensaje = "El liquido endovenoso se ha grabado correctamente";
				}
				
				if(ajax.responseText == '2'){
					mensaje = "El liquido endovenoso se ha modificado correctamente";
				}
			}
		}
		*/
		if (!estaEnProceso(ajax)) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamada ajax para la inserción o modificación de un articulo
 ******************************************************************************************************************************/
function grabarArticuloElemento(historia,ingreso,fechaKardex,cdArt,cntDosis,unDosis,per,fftica,fini,hini,via,conf,dtto,obs,origenArticulo,usuario,condicion,dosMax,cantGrabar,unidadManejo,cantidadManejo,primerKardex,horasFrecuencia,fechaInicioAnt,horaInicioAnt,noDispensar,tipoProtocolo,centroCostosGrabacion,prioridad,idElemento){
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=01&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&codArticulo="+cdArt+"&cantDosis="+cntDosis+"&unDosis="+unDosis+"&per="+per+"&fmaFtica="+fftica+"&fini="+fini+"&hini="+hini+"&dosMax="+dosMax
		+"&via="+via+"&conf="+conf+"&dtto="+dtto+"&obs="+obs+"&codUsuario="+usuario+"&fechaKardex="+fechaKardex+"&origenArticulo="+origenArticulo+"&condicion="+condicion+"&cantGrabar="+cantGrabar
		+"&unidadManejo="+unidadManejo+"&cantidadManejo="+cantidadManejo+"&primerKardex="+primerKardex+"&horasFrecuencia="+horasFrecuencia+"&fIniAnt="+fechaInicioAnt.replace(" ","")+"&hIniAnt="+horaInicioAnt
		+"&noDispensar="+noDispensar+"&tipoProtocolo="+tipoProtocolo+"&centroCostosGrabacion="+centroCostosGrabacion+"&prioridad="+prioridad;

	try{
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",false);  
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		//No avanza hasta llegar la respuesta
		respuesta = ajax.responseText.split("*");
		
		switch(respuesta[0]){
			case '0':
				mensaje = "No se pudo guardar la informacion del articulo";
				alert(mensaje);
				break;
			case '1':
				var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);
				
				var texto = ""; 
				if(elemento.tagName == "DIV"){
					texto = elemento.innerHTML;	
				} else {
					texto = elemento.value;
				}
				var col = document.getElementById("wcolmed"+tipoProtocolo+idElemento);
				
				if(col){
					col.innerHTML = "<div id='wnmmed"+tipoProtocolo+idElemento+"' class='campo2'>"+texto+"</div>";
				}
				
				//Resalto lo grabado
				var fila = document.getElementById('tr'+tipoProtocolo+idElemento);
				fila.className = 'fila1';
				
				mensaje = "El articulo se ha creado correctamente"; 
				break;
			case '2':
				var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);
				var texto = ""; 
					
				if(elemento.tagName == "DIV"){
					texto = elemento.innerHTML;	
				} else {
					texto = elemento.value;
				}
				
				var col = document.getElementById("wcolmed"+tipoProtocolo+idElemento);

				if(col && texto != "undefined"){
					col.innerHTML = "<div id='wnmmed"+tipoProtocolo+idElemento+"' class='campo2'>"+texto+"</div>";
				}
				
				mensaje = "El articulo se ha modificado correctamente";
				break;
			case '3':
				mensaje = "El articulo no se puede modificar si se encuentra suspendido.";
				alert(mensaje);
				break;
			case '4':
				mensaje = "El articulo se modificó correctamente, tenga en cuenta que ya estaba dispensado completamente.";
				var diferencia = (respuesta[1] ? respuesta[1] : 0) - (respuesta[2] ? respuesta[2] : 0);
				mensaje += "\n-Se genero un ";
				mensaje += (diferencia >= 0) ? "sobrante de " : "faltante de ";
				mensaje += Math.abs(diferencia);
				alert(mensaje);
				break;
			default:
				mensaje = "No especificado: "+ajax.responseText;
				alert(mensaje);
				break;
		}
		
		//Para las fechas anteriores cuando se crean los articulos
		if(document.getElementById("whfinicio"+tipoProtocolo+idElemento)){
			document.getElementById("whfinicio"+tipoProtocolo+idElemento).value = fini + " a las:" + hini;
		}
		
		/*AJAX SINCRONICO NO NECESITA ESTE EVENTO, TKS, WILLY
		ajax.onreadystatechange=function() 
		{
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				respuesta = ajax.responseText.split("*");
				$.unblockUI();
				switch(respuesta[0]){
					case '0':
						mensaje = "No se pudo guardar la informacion del articulo";
						alert(mensaje);
						break;
					case '1':
						var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);
						
						var texto = ""; 
						if(elemento.tagName == "DIV"){
							texto = elemento.innerHTML;	
						} else {
							texto = elemento.value;
						}
						var col = document.getElementById("wcolmed"+tipoProtocolo+idElemento);
						
						if(col){
							col.innerHTML = "<div id='wnmmed"+tipoProtocolo+idElemento+"' class='campo2'>"+texto+"</div>";
						}
						
						//Resalto lo grabado
						var fila = document.getElementById('tr'+tipoProtocolo+idElemento);
						fila.className = 'fila1';
						
						mensaje = "El articulo se ha creado correctamente"; 
//						$.growlUI('',mensaje);
						break;
					case '2':
						var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);
						var texto = ""; 
							
						if(elemento.tagName == "DIV"){
							texto = elemento.innerHTML;	
						} else {
							texto = elemento.value;
						}
						
						var col = document.getElementById("wcolmed"+tipoProtocolo+idElemento);

						if(col && texto != "undefined"){
							col.innerHTML = "<div id='wnmmed"+tipoProtocolo+idElemento+"' class='campo2'>"+texto+"</div>";
						}
						
						mensaje = "El articulo se ha modificado correctamente";
//						$.growlUI('',mensaje);
						break;
					case '3':
						mensaje = "El articulo no se puede modificar si se encuentra suspendido.";
						alert(mensaje);
						break;
					case '4':
						mensaje = "El articulo se modificó correctamente, tenga en cuenta que ya estaba dispensado completamente.";
						var diferencia = (respuesta[1] ? respuesta[1] : 0) - (respuesta[2] ? respuesta[2] : 0);
						mensaje += "\n-Se genero un ";
						mensaje += (diferencia >= 0) ? "sobrante de " : "faltante de ";
						mensaje += Math.abs(diferencia);
						alert(mensaje);
						break;
					default:
						mensaje = "No especificado: "+ajax.responseText;
						alert(mensaje);
						break;
				}
				
				//Para las fechas anteriores cuando se crean los articulos
				if(document.getElementById("whfinicio"+tipoProtocolo+idElemento)){
					document.getElementById("whfinicio"+tipoProtocolo+idElemento).value = fini + " a las:" + hini;
				}			
			}
		}
		*/
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}	
}
 /*****************************************************************************************************************************
  * Llamada ajax para la modificación de un articulo en el perfil farmacologico
  ******************************************************************************************************************************/
 function grabarArticuloPerfilElemento(historia,ingreso,fechaKardex,cdArt,cdArtNuevo,dtto,obs,usuario,idElemento,unidadDosis,formaFarm,origen,via,dosmax,prioridad,fechaInicio,horaInicio,autorizadoCtc){
 	var parametros = "";
 	var mensaje = "";
 	
 	if(cdArtNuevo && cdArtNuevo != ''){ 	
 		parametros = "consultaAjaxKardex=18&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
 			+"&codArticulo="+cdArt+"&dtto="+dtto+"&obs="+obs+"&codUsuario="+usuario+"&fechaKardex="+fechaKardex+"&codArticuloNuevo="+cdArtNuevo+"&unidadDosis="+unidadDosis
 			+"&formaFarm="+formaFarm+"&origen="+origen+"&fechaInicio="+fechaInicio+"&horaInicio="+horaInicio;
 	} else {
 		parametros = "consultaAjaxKardex=17&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
			+"&codArticulo="+cdArt+"&dtto="+dtto+"&obs="+obs+"&codUsuario="+usuario+"&fechaKardex="+fechaKardex+"&via="+via+"&dosisMaximas="+dosmax+"&prioridad="+prioridad+"&fechaInicio="+fechaInicio
			+"&horaInicio="+horaInicio+"&autorizadoCtc="+autorizadoCtc;
 	}	
 	
 	try{
 		$.blockUI({ message: $('#msjEspere') });
 		ajax=nuevoAjax();
 		
 		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true); 
 		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 		ajax.send(parametros);

 		ajax.onreadystatechange=function() 
 		{
 			if (ajax.readyState==4 && ajax.status==200)
 			{ 
 				var respuesta = ajax.responseText.split("|");
 				
 				switch(respuesta[0]){
 					case '0':
 						mensaje = "No se pudo modificar el artículo.";	
 						break;
 					case '1': 	
 						var elemento = document.getElementById("wnmmed"+idElemento);
 						var fftica = document.getElementById("wffticaact"+idElemento);
 						var dosis = document.getElementById("wudosisact"+idElemento);
						var texto = elemento.value;
						var opcionesMaestro = "";
						
						//Actualizo en pantalla los articulos
						var col = document.getElementById("wnmmedact"+idElemento);
						col.innerHTML = texto;
 						
						if(formaFarm && formaFarm != ''){
							opcionesMaestro = document.getElementById("wmfftica").options;
							var cont1 = 0;
							while(opcionesMaestro[cont1]){
								if(opcionesMaestro[cont1].value == formaFarm){
									fftica.innerHTML = opcionesMaestro[cont1].text;
									break;
								}
								cont1++;
							}
						} else {
							fftica.innerHTML = "Sin forma farmaceutica";							
						}
						
						if(unidadDosis && unidadDosis != ''){
							opcionesMaestro = document.getElementById("wmunidadesmedida").options;
							var cont1 = 0;
							
							while(opcionesMaestro[cont1]){
								if(opcionesMaestro[cont1].value == unidadDosis){
									dosis.innerHTML = opcionesMaestro[cont1].text;
									
									/******************************************************
									 * Enero 05 de 2011
									 ******************************************************/
									var auxDiv = document.createElement( "div" );
									auxDiv.innerHTML = respuesta[1];
									
									dosis.parentNode.appendChild( auxDiv.firstChild );
									dosis.parentNode.removeChild( dosis.parentNode.firstChild );
									dosis.parentNode.appendChild( dosis  );
									/******************************************************/
									
									break;
								}
								cont1++;
							}
						} else {
							dosis.innerHTML = "Sin unidad de medida";							
						}
						
						/******************************************************
						 * Enero 06 de 2011
						 ******************************************************/
						//Recalculando datos cantidad pendiente día anteriro, Cantidad día Actual, Total a grabar
						if( dosis.parentNode.parentNode ){
							
							var fila = dosis.parentNode.parentNode;
							
							var auxDiv = document.createElement( "div" );
							
							fila.cells[4].removeChild( fila.cells[4].firstChild );
							fila.cells[5].removeChild( fila.cells[5].firstChild );
							fila.cells[6].removeChild( fila.cells[6].firstChild );
							
							auxDiv.innerHTML = respuesta[3];
							fila.cells[4].appendChild( auxDiv.firstChild );
							
							auxDiv.innerHTML = respuesta[2];
							fila.cells[5].appendChild( auxDiv.firstChild );
							
							auxDiv.innerHTML = parseInt(fila.cells[4].innerHTML)+parseInt(fila.cells[5].innerHTML);
							fila.cells[6].appendChild( auxDiv.firstChild );
							
							//Colocando la aprobación del recgente
							fila.cells[12].firstChild.checked = true;
							
							
//							var fila = dosis.parentNode.parentNode;
//							fila.cells[4].innerHTML = respuesta[3];
//							fila.cells[5].innerHTML = respuesta[2];
//							fila.cells[6].innerHTML = parseInt(fila.cells[4].innerHTML)+parseInt(fila.cells[5].innerHTML);
						}
						/******************************************************/
						
						elemento.value = "";
						mensaje = "El artículo se ha reemplazado correctamente.";
 						break;
 					case '2':
 						//Calculo de la diferencia de ctc 						
 						var autorizado = document.getElementById("wautctc"+idElemento);
 						var usado = document.getElementById("wusadoctc"+idElemento);
 						var disponible = document.getElementById("wdispctc"+idElemento);
 	
 						if(respuesta[1]){
 							usado.value = respuesta[1];
 						}
 						
 						if(autorizado && autorizado.value != '' && !isNaN(parseFloat(usado.value))){
 							if(parseFloat(autorizado.value) >= parseFloat(usado.value)){
 								disponible.value = parseFloat(autorizado.value) - parseFloat(usado.value); 	
 							} else {
 								disponible.value = parseFloat(autorizado.value) - parseFloat(usado.value);	
 							}
 						} 						
 						
 						mensaje = "El artículo se ha modificado correctamente.";
 						break;
 					case '3':
 						mensaje = "El artículo no se puede modificar si se encuentra suspendido.";							
 						break;
 					case '5':	//Enero 12 de 2011
 						mensaje = "No se pudo modificar el articulo.\nYa existe el artículo con la misma fecha y hora de inicio";
 						break;
 					default:
 						mensaje = "No especificado: "+ajax.responseText;
 						break;	
 				}
 			}
 			$.unblockUI();
 			$.growlUI('',mensaje); 			
 		}
 		if ( !estaEnProceso(ajax) ) {
 			ajax.send(null);
 		}
 	}catch(e){ }	
 }
/*****************************************************************************************************************************
 * Llamada ajax para suspender o activar un articulo del detalle de medicamentos
 ******************************************************************************************************************************/
function suspenderArticulo(idxElemento,tipoProtocolo){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfechagrabacion.value;
	var usuario = document.forms.forma.usuario.value;
	var fila = document.getElementById('tr'+tipoProtocolo+idxElemento);
	var estaSuspendido = 'on';
	
	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);

	//Extraccion de la fecha y hora inicial de administracion
	var fh = fechaInicio.value.split("a las:");
	fechaInicio = fh[0];
	horaInicio = fh[1];
	
	//Extraccion del codigo articulo propiamente dicho
	var cd = "";
	if(codigoArticulo.tagName == 'INPUT'){
		cd = codigoArticulo.value.split("-");
	} else {
		cd = codigoArticulo.innerHTML.split("-");
	}
	
	//Alterno la suspension del medicamento
	if(fila.className == 'suspendido'){
		estaSuspendido = 'off';
	} else {
		estaSuspendido = 'on';		
	}
	
	codigoArticulo = cd[0];
	
	//Confirmacion de suspension
	if(confirm("¿Desea cambiar la suspensión/activación de la administración del medicamento?")){
	
	//Llamada AJAX
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=16&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&codigoArticulo="+codigoArticulo+"&codUsuario="+usuario+"&fecha="+fecha+"&estado="+estaSuspendido+"&fechaInicio="+fechaInicio+"&horaInicio="+horaInicio
		+"&tipoProtocolo="+tipoProtocolo;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				if(ajax.responseText == '1'){
					if(estaSuspendido == 'on'){						
						fila.className = 'suspendido';
						mensaje = "El medicamento se ha suspendido.";
					} else {						
						fila.className = '';
						mensaje = "El medicamento se ha activado.";
					}
				} else {
					mensaje = "No se pudo modificar estado suspension: " + ajax.responseText;
				}
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}
	}	
}
/*****************************************************************************************************************************
 * Llamada a ajax para realizar la grabación de un examen nuevo
 ******************************************************************************************************************************/
function grabarExamenElemento(codExamen,nomExamen,historia,ingreso,fecha,observaciones,estadoExamen,fechaDeSolicitado,usuario,idElemento){
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=7&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&fecha="+fecha+"&codigoExamen="+codExamen+"&observaciones="+observaciones+"&estado="+estadoExamen+"&codUsuario="+usuario+"&nombreExamen="+nomExamen
		+"&fechaDeSolicitado="+fechaDeSolicitado;

	try{
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",false); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		//
		if(ajax.responseText == '0'){
			mensaje = "No se pudo grabar el examen";
		} 
		
		if(ajax.responseText == '1'){
			//Resalto lo grabado
			var fila = document.getElementById('trEx'+idElemento);
			fila.className = 'fila1';
			
			mensaje = "El examen se ha grabado correctamente";
		}
		
		if(ajax.responseText == '2'){
			mensaje = "El examen se ha modificado correctamente";
		}
		/*
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				if(ajax.responseText == '0'){
					mensaje = "No se pudo grabar el examen";
				} 
				
				if(ajax.responseText == '1'){
					//Resalto lo grabado
					var fila = document.getElementById('trEx'+idElemento);
					fila.className = 'fila1';
					
					mensaje = "El examen se ha grabado correctamente";
				}
				
				if(ajax.responseText == '2'){
					mensaje = "El examen se ha modificado correctamente";
				}
			}
//			$.unblockUI();
			$.growlUI('',mensaje);
		}
		*/
		if (!estaEnProceso(ajax)) {
			ajax.send(null);
		}
	}catch(e){	}
}

/*****************************************************************************************************************************
 * Llamada a ajax para realizar la grabación la aprobación del regente
 ******************************************************************************************************************************/
function grabarAprobacionRegente11(idElemento){
	var parametros = "";
	
	var historia = document.forms.forma.whistoria.value;
  	var ingreso = document.forms.forma.wingreso.value;
  	var fechaKardex = document.forms.forma.wfechagrabacion.value;
  	var usuario = document.forms.forma.usuario.value;
  	var estado = document.getElementById('wchkapr').checked ? "on" : "off";
  	
  	var mensaje = "";
  	
 	parametros = "consultaAjaxKardex=20&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
			+"&codUsuario="+usuario+"&fecha="+fechaKardex+"&estado="+estado;
	 	
 	try{
 		$.blockUI({ message: $('#msjEspere') });
 		ajax=nuevoAjax();
	 		
 		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true); 
 		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 		ajax.send(parametros);

 		ajax.onreadystatechange=function() 
 		{
 			if (ajax.readyState==4 && ajax.status==200)
 			{ 
 				switch(ajax.responseText){
 					case '0':
 						mensaje = "No se pudo grabar la aprobación del regente";	
 						break;
 					case '1': 	
 						if(document.getElementById('wchkapr').checked){
 							mensaje = "El perfil se aprobó por el regente.";
 						} else {
 							mensaje = "El perfil NO se aprobó por el regente.";
 						}
						break;
	 				default:
	 					mensaje = "No especificado: "+ajax.responseText;
	 					break;	
	 			}
	 		}
 			$.unblockUI();
 			$.growlUI('',mensaje);
	 	}
	 	if ( !estaEnProceso(ajax) ) {
	 		ajax.send(null);
		}
 	}catch(e){ }	
 }
 /*****************************************************************************************************************************
 * -----------OPERACIONES DE ELIMINACION---------------
 ******************************************************************************************************************************/
 function quitarMedico(idxMedico){	 
	var fecha = document.forms.forma.wfecha.value;
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;
	
	if(confirm("Desea quitar el medico seleccionado?")){
		eliminarMedicoElemento(historia,ingreso,fecha,usuario,idxMedico);
	}
	return false;
}
 /*****************************************************************************************************************************
  * Elimina una alergia por fecha
  ******************************************************************************************************************************/
function quitarAlergia(fecha){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;
		
	if(confirm("Desea quitar la alergia seleccionada?")){
		eliminarAlergiaElemento(historia,ingreso,fecha,usuario);
	}
}
 /*****************************************************************************************************************************
 * Llamado a ajax para la eliminación de un medico tratante
 ******************************************************************************************************************************/
function eliminarMedicoElemento(historia,ingreso,fecha,usuario,idxMedico){
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=13&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
			+"&idRegistro="+idxMedico+"&codUsuario="+usuario;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){
				var prueba = ajax.responseText;
				if(ajax.responseText == 1){
					var cntMedicos = document.getElementById("cntMedicos");
					var filaEliminar = document.getElementById("Med"+reemplazarTodo(idxMedico,"-",""));
				 
				 	cntMedicos.removeChild(filaEliminar);
				 	
				 	mensaje = "El medico tratante se ha retirado con exito";
				 }
			 
			 //No pudo eliminar
			 if(ajax.responseText == 0){
				 mensaje = 'No se pudo eliminar el medico';
			 }
		}
		$.unblockUI();
		$.growlUI('',mensaje);
	}
	if ( !estaEnProceso(ajax) ) {
		ajax.send(null);
	}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamado a ajax para la eliminación de una alergia
 ******************************************************************************************************************************/
function eliminarAlergiaElemento(historia,ingreso,fecha,usuario){
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=21&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
			+"&codUsuario="+usuario+"&descripcion=";

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){
				if(ajax.responseText == 1){
					
					var cntDetalleAlergias = document.getElementById("detAlergias");
					var filaEliminar = document.getElementById("trAle"+fecha);
					 
					cntDetalleAlergias.removeChild(filaEliminar);
					mensaje = 'La alergia ha sido eliminada con exito';
				 }
			 
				//No pudo eliminar
				if(ajax.responseText == 0){
					mensaje = 'No se pudo modificar la alergia';
				}
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamado ajax para función de eliminación de dieta
 ******************************************************************************************************************************/
function eliminarDietaElemento(historia,ingreso,fecha,usuario,codigoDieta){
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=15&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
			+"&idRegistro="+codigoDieta+"&codUsuario="+usuario;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){
				//Eliminado correctamente
				if(ajax.responseText == 1){
					var cntDietas = document.getElementById("cntDietas");
					var filaEliminar = document.getElementById("Die"+codigoDieta);
				 	cntDietas.removeChild(filaEliminar);
				 	
				 	var colDietas = document.forms.forma.colDietas;	
					colDietas[codigoDieta] = '';
					mensaje = 'La dieta se ha retirado con exito';
				 }
				 
			 //No pudo eliminar
			 if(ajax.responseText == 0){
				 mensaje = 'No se pudo eliminar la dieta';
			 }
		}
		$.unblockUI();
		$.growlUI('',mensaje);
	}
	if ( !estaEnProceso(ajax) ) {
		ajax.send(null);
	}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamada ajax para eliminación del articulo 
 ******************************************************************************************************************************/
function eliminarArticuloElemento(historia,ingreso,fecha,cdArt,usuario,idx,fini,hini,tipoProtocolo){
	var parametros = "consultaAjaxKardex=04&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
			+"&historia="+historia+"&ingreso="+ingreso+"&codArticulo="+cdArt+"&fecha="+fecha+"&codUsuario="+usuario+"&fechaInicio="+fini+"&horaInicio="+hini
			+"&tipoProtocolo="+tipoProtocolo;
					
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{
				var cntDetalleKardex = document.getElementById("detKardex"+tipoProtocolo);
				var filaEliminar = document.getElementById("tr"+tipoProtocolo+idx);
				 
				cntDetalleKardex.removeChild(filaEliminar);
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}	
}
/*****************************************************************************************************************************
 * Llamada ajax para eliminación de una infusión
 ******************************************************************************************************************************/
function eliminarInfusionElemento(historia,ingreso,fecha,usuario,idx){
	var parametros = "";	
		
	parametros = "consultaAjaxKardex=12&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha+"&codUsuario="+usuario+"&consecutivo="+idx;

	try{
		ajax = nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{
				var cntDetalleKardex = document.getElementById("detInfusiones");
				var filaEliminar = document.getElementById("trIn"+idx);
				 
				cntDetalleKardex.removeChild(filaEliminar);
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}	
}
/*****************************************************************************************************************************
 * Llamada ajax para eliminar un examen de laboratorio
 ******************************************************************************************************************************/
function eliminarExamenElemento(historia,ingreso,codigoExamen,fecha,usuario,idx){
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=8&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
			+"&codExamen="+codigoExamen+"&codUsuario="+usuario;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				//Eliminado correctamente
				if(ajax.responseText == 1){
					var cntExamenes = document.getElementById("detExamenes");
					var filaEliminar = document.getElementById("trEx"+idx);
				 
				 	cntExamenes.removeChild(filaEliminar);
				 }
				 
				 //No pudo eliminar
				 if(ajax.responseText == 0){
					 mensaje = 'No se pudo eliminar el examen';
				 }
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}	
}
/*****************************************************************************************************************************
 * Llamada ajax para eliminar un esquema dextrometer
 ******************************************************************************************************************************/
function eliminarEsquemaDextrometerElemento(historia, ingreso, fechaKardex, codArticulo, usuario){
	var parametros = "";
	var mensaje = "";
	
	parametros = "consultaAjaxKardex=23&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fechaKardex
			+"&codInsulina="+codArticulo+"&codUsuario="+usuario;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/Movhos/kardex.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{
				//Eliminado correctamente
				if(ajax.responseText == 1){
					var cntEsquema = document.getElementById("cntEsquema");
					var cntEsquemaActual = document.getElementById("cntEsquemaActual");

					if(cntEsquema){
						cntEsquema.style.display = 'none';
					}
					
					if(cntEsquemaActual){
						cntEsquemaActual.style.display = 'none';
					}
					
					//Limpio los valores y anulo el boton quitar
					if(document.getElementById('btnQuitarEsquema')){
						document.getElementById('btnQuitarEsquema').disabled = true;
					}
					
					//Valor de la insulina
					if(document.getElementById('wdexins')){
						document.getElementById('wdexins').value = '';
					}
					
					//Valor de la frecuencia
					if(document.getElementById('wdexfrecuencia')){
						document.getElementById('wdexfrecuencia').value = '';
					}
					
					//Valor del esquema de insulina
					if(document.getElementById('wdexesquema')){
						document.getElementById('wdexesquema').value = '';
					}
					
					//Valor del esquema de insulina
//					if(document.getElementById('btnEsquema')){
//						document.getElementById('btnEsquema').disabled = true;
//					}
				 }
				 
				 //No pudo eliminar
				 if(ajax.responseText == 0){
					 mensaje = 'No se pudo eliminar el esquema';
				 }
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}	
}