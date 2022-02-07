/* JAVASCRIPT ORDENES PARA HCE
 * 
 * MODIFICACIONES
 * Junio 9 de 2020		Se hacen cambios varios para imprimir las ordenes en modo de consulta
 * Junio 5 de 2019		Se valida el tiempo de recarga de la mensajería kardex
 * Agosto 18 de 2016.	Se agrega trim a la respuesta ajax cuando se consulta un protocolo de ordenes de examenes y/o procedimientos.
 * Abril 11 de 2016.	Se corrige variable en la función marcarImpresionExamen. El consecutivo para averiguar el item del procedimiento no era correcto.
 * Abril 01 de 2016.	Al elminar un articulo desminuía el contador de los elementos, esto hacía que los datos no se enviarán correctamente por el ajax para ser guardados.
 * Septiembre 30 de 2015.	Se corrige error que no permitía grabar correctamente cuando se eliminaba un medicamento.
 * Abril 23 de 2014 	Se agrega la instruccion cacheLength:0 del autocomplete para que al momento de escribir no se quede con lo ultimo de desplegó,
						sino que siempre vaya a la función y muestre información nueva. Jonatan Lopez.
 * Abril 10 de 2014		Se realizan cambios varios para los procedimientos
 * Abril 1 de 2014		Se corrige la url del ajax que busca medicamentos y se agrega mensaje cuando se busca un procedimiento nuevo no homologado
 * Marzo 12 de 2014		Se corrige la busqueda del checbox de impresión por jquery, generaba error en algunas ocasiones
 * Febrero 25 de 2014	Se corrige el nro de item para los examenes y procedimientos
 * Mayo 5 de 2011		Al agregar un examen o procedimiento, todos los examenes son colapsados
 */

//Variables que indican si hay examenes o procedimientos a imprimir
var hayArticulosCTCImp = false; 
var hayArticulosImp = false; 
var hayProcedimientosCTCImp = false; 
var hayProcedimientosImp = false;
 
var justificacionUltimoExamenAgregado = '';

var cantidadAnterior = new Array();

var esLiquidoEndovenoso = false;

var arCTCArticulos = {};


/**************************************************************************************************************
 * Indica si hay articulos a imprimir y activa los botones correspondientes
 **************************************************************************************************************/
function hayArticulosAImprimir(){
	
	/********************************************************************************
	 * Busco cuales medicamentos están para imprimir
	 * Se verifica si es pos o no pos
	 * De haber un medicamento a imprimir se activa el boton correspondiente
	 ********************************************************************************/
	
	hayArticulosImp = false; 
	var cmpChkImp = $( "input:not([id^=wchkimpwhartpro])[id^=wchkimp]:checked,[id^=whistchkimp]:checked" );
	if( cmpChkImp.length > 0 ){
		hayArticulosImp = true; 
	}
	
	//De los articulos que están marcados para imprimir hay que buscar cuales tienen CTC
	//Para ello recorro todos los campos que estan marcados para imprimir
	//Luego busco si es Pos y tiene CTC
	hayArticulosCTCImp = false; 
	cmpChkImp.each(function(index){
		
		var beginId = this.id.substr( 0, 7 );
		
		//formo los id a buscar
		var lastId = "";
		var strArtPos = "";
		var strArtCTC = "";
		if( beginId == "wchkimp" ){
			lastId = this.id.substr( 7 );
			strArtPos = "wespos"+lastId;
			strArtCTC = "wtienectc"+lastId;
		}
		else{
			lastId = this.id.substr( 11 );
			strArtPos = "whistespos"+lastId;
			strArtCTC = "whisttienectc"+lastId;
		}
		
		//Si es No Pos y tiene CTC hay articulos con CTC a imprimir
		if( $( "#"+strArtPos ).val() != 'on' && $( "#"+strArtCTC ).val() == 'on' ){
			hayArticulosCTCImp = true;
			return;
		}
	});
	
	
	
	//Reviso si hay procedimientos a imprimir
	hayProcedimientosImp = false; 
	var cmpProChkImp = $( "[id^=imprimir_examen]:checked" );
	if( cmpProChkImp.length > 0 ){
		hayProcedimientosImp = true; 
	}
	
	//De los articulos que están marcados para imprimir hay que buscar cuales tienen CTC
	//Para ello recorro todos los campos que estan marcados para imprimir
	//Luego busco si es Pos y tiene CTC
	hayProcedimientosCTCImp = false; 
	cmpProChkImp.each(function(index){
		
		var lastId = this.id.substr( 15 );
		
		//Si es No Pos y tiene CTC hay articulos con CTC a imprimir
		if( $( "#wproespos"+lastId ).val() != 'on' && $( "#wprotienectc"+lastId ).val() == 'on' ){
			hayProcedimientosCTCImp = true;
			return;
		}
	})
	
	//Muestro los botones correspondientes
	//Boton Imprimir
	if( hayArticulosImp ){
		$( "#btnImpArt" ).css({display:""});
	}
	else{
		$( "#btnImpArt" ).css({display:"none"});
	}
	
	//Muestro los botones correspondientes
	//Boton Imprimir CTC para articulos
	if( hayArticulosCTCImp ){
		$( "#btnImpArtCTC" ).css({display:""});
	}
	else{
		$( "#btnImpArtCTC" ).css({display:"none"});
	}
	
	//Muestro los botones correspondientes
	//Boton Imprimir examenes
	if( hayProcedimientosImp ){
		$( "#btnImpPro" ).css({display:""});
	}
	else{
		$( "#btnImpPro" ).css({display:"none"});
	}
	
	//Muestro los botones correspondientes
	//Boton Imprimir CTC para examenes
	if( hayProcedimientosCTCImp ){
		$( "#btnImpProCTC" ).css({display:""});
	}
	else{
		$( "#btnImpProCTC" ).css({display:"none"});
	}
}
 
/*****************************************************************************************************************************
 * Actualiza el estado de alta del articulo enviado
 ******************************************************************************************************************************/
function marcarImpresionHist( campo,historia,ingreso,articulo,fecha,ido )
{
	var parametros = "";
	
	var imprimir = campo.checked ? "on":"off";
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
		
	parametros = "consultaAjaxKardex=56&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&estado="+imprimir+"&whis="+historia+"&wing="+ingreso+"&codigoArticulo="+articulo+"&wfecha="+fecha+"&ido="+ido+"&wemp_pmla="+wemp_pmla; 

	try{
		var ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var respuesta=ajax.responseText;	
				if(respuesta!='1'){
					alert("No se pudo actualizar el artículo");
					if(campo.checked == true)
						campo.checked = false;
					else
						campo.checked = true;
				}
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}	
	
	hayArticulosAImprimir();
} 


/**
 * Ajax que graba un examen de la tabla temporal a la tabla de detalle
 */
function grabarExamenADetalle( contExamen, tipoProtocolo )
{
		
		var wemp_pmla = document.forms.forma.wemp_pmla.value;
		// var tipoOrden = $('#hexcco'+contExamen).val();	
		// var nroOrden = $('#hexcons'+contExamen).val();
		// var numeroItem = $('#hexnroitem'+contExamen).val();
				
		$.ajax({
				url: "ordenesidc.inc.php",
				type: "POST",
				data:{
					consultaAjaxKardex:       	'55', 
					wemp_pmla:					wemp_pmla,
					his:						$('#whistoria').val(),
					ing:						$('#wingreso').val()
				},
				async: false,
				success:function(data_json) {
				
					if (data_json.error == 1)
					{
						alert(data_json.mensaje);
						return;
					}
					else{}
				}
			}
		);
} 


/******************************************************************************************
 * Cambia el titulo del nuevo buscador para que no quede como obligatorio
 ******************************************************************************************/
function cambiarTituloNuevoBuscador(){
	
	$( "[cTit]" ).each(function(){
		if( $( "#wckInterno" )[0].checked ){
			//Si la cadena tiene (*) se quita
			if( $( this ).html().indexOf( "(*)" ) > 0 ){
				$( this ).html( $( this ).html().substr( 0, $( this ).html().length - 3 ) )
			}
		}
		else{
			if( $( this ).html().indexOf( "(*)" ) == -1 ){
				$( this ).html( $( this ).html() + "(*)" );
			}
		}
	});
	
	if( $( "#wckInterno" )[0].checked ){
		//<input id="wdosisfamilia" class="textoNormal" type="text" value="" size="3" name="wdosisfamilia">
		var auxDosis = document.getElementById( "wdosisfamilia" );
		
		var cmpTD = auxDosis.parentNode;
		
		var newCmp = document.createElement( "input" );
		newCmp.type = 'text';
		newCmp.size = 3;
		newCmp.id = "wdosisfamilia";
		newCmp.name = "wdosisfamilia";
		newCmp.className = "textoNormal";
		
		//remuevo el campo que había
		cmpTD.removeChild( auxDosis );
		
		//agrego el campo nuevo
		cmpTD.appendChild( newCmp );
		
		//Oculto la unidad de medida tanto de encabezado como de select
		document.getElementById( "wunidad" ).parentNode.style.display = "";					//oculta celda de la unidad
		document.getElementById( "nuevoBuscador" ).rows[0].cells[4].style.display = "";		//oculta encabezado
		document.getElementById( "nuevoBuscador" ).rows[0].cells[5].innerHTML = "Dosis/Día";
	}
	else{
		//<input id="wdosisfamilia" class="textoNormal" type="text" value="" size="3" name="wdosisfamilia">
		var auxDosis = document.getElementById( "wdosisfamilia" );
		
		var cmpTD = auxDosis.parentNode;
		
		var newCmp = document.createElement( "textarea" );	
		newCmp.id = "wdosisfamilia";
		newCmp.name = "wdosisfamilia";
		newCmp.className = "textoNormal";
		
		//remuevo el campo que había
		cmpTD.removeChild( auxDosis );
		
		//agrego el campo nuevo
		cmpTD.appendChild( newCmp );
		
		//Oculto la unidad de medida tanto de encabezado como de select
		document.getElementById( "wunidad" ).parentNode.style.display = "none";					//oculta celda de la unidad
		document.getElementById( "nuevoBuscador" ).rows[0].cells[4].style.display = "none";		//oculta encabezado
		document.getElementById( "nuevoBuscador" ).rows[0].cells[5].innerHTML = "Dosis(*)";
	}
}

/*********************************************************************************
 * Encuentra la posicion en Y de un elemento
 *********************************************************************************/
function findPosY(obj)
{
	var curtop = 0;
	if(obj.offsetParent)
    	while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }

  
/*********************************************************************************
 * Encuentra la posicion en X de un elemento
 *********************************************************************************/
function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1)
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }


function ocultar(){
	var divTitle = document.getElementById( "dvTitle" );
	divTitle.style.display = 'none';
}

function mostrar( campo ){

	try{
		clearInterval( interval );
	}
	catch(e){}

	var divTitle = document.getElementById( "dvTitle" );
	
	divTitle.innerHTML = campo.title;

	divTitle.style.display = '';
	divTitle.style.position = 'absolute';
	divTitle.style.top = parseInt( findPosY(campo) )- parseInt( campo.offsetHeight );
	divTitle.style.left = findPosX( campo );
	divTitle.style.background = "#FFFFDF";
	divTitle.style.borderStyle = "solid";
	divTitle.style.borderWidth = "1px";

	interval = setTimeout( "ocultar()", 3000 );		
}



// function ocultar(){
	// var divTitle = document.getElementById( "dvTitle" );
	// divTitle.style.display = 'none';
// }

function mostrarDialogDiv( campo ){

	try{
		clearInterval( interval );
	}
	catch(e){}

	var divTitle = document.getElementById( "dialog" );
	
	//divTitle.innerHTML = campo.title;

	divTitle.style.display = '';
	divTitle.style.position = 'absolute';
	divTitle.style.top = parseInt( findPosY(campo) )+ parseInt( campo.offsetHeight );
	divTitle.style.left = findPosX( campo );
	// divTitle.style.background = "#FFFFDF";
	// divTitle.style.borderStyle = "solid";
	// divTitle.style.borderWidth = "1px";

	//interval = setTimeout( "ocultar()", 3000 );		
	
	divTitle.parentNode.style.height = divTitle.offsetHeight+5;
}

// Cuando es adición múltiple de medicamentos esta función se encarga de abrir los formatos CTC
// de los medicamentos NO POS que puedan haber en esta adición múltiple
// Enero 30 de 2012
function abrirCTCMultiple()
{
	var filasNoPos = strPendientesCTC.split( "\r\n" );

	if(filasNoPos.length-1 > 0)
	{
		var arrNoPos = filasNoPos[0].split( "|" );
		var cadenaReemplazar = filasNoPos[0]+"\r\n";
		
		strPendientesCTC = strPendientesCTC.replace(cadenaReemplazar,"")
		if(arrNoPos[0]=='procedimiento')
			mostrarCtcProcedimientos(  arrNoPos[1],  arrNoPos[2] );	
		else
		{
			var historia = document.forms.forma.whistoria.value;
			var ingreso = document.forms.forma.wingreso.value;
			var wemp_pmla = document.forms.forma.wemp_pmla.value;
			// var fecha = document.forms.forma.wfecha.value;
			
			
			var strTratamientos;
			var parametros = ""; 
			var responsableEsEPS = 1;

			parametros = "consultaAjaxKardex=48&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&whistoria="+historia+"&wingreso="+ingreso;


			try{
				// Ajax que consulta si el responsable del paciente es una EPS
				ajax1=nuevoAjax();
				ajax1.open("POST", "ordenesidc.inc.php",true);
				ajax1.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax1.send(parametros);
				
				ajax1.onreadystatechange=function() 
				{ 
					if (ajax1.readyState==4 && ajax1.status==200)
					{
						// Retorna el resultado de la consulta que define si la empresa responsable es EPS
						responsableEsEPS = parseInt(ajax1.responseText);

						// Si el responsable del paciente es una EPS se abre el formulario CTC
						if(responsableEsEPS > 0)
						{
							parametros = "consultaAjaxKardex=45&basedatos="+document.forms.forma.wbasedato.value+"&wart="+arrNoPos[1]+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 
							try
							{
								// Ajax que consulta los tratamientos existentes y devuelve el array de estos
								ajax=nuevoAjax();
								ajax.open("POST", "ordenesidc.inc.php",true);
								ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
								ajax.send(parametros);
								
								ajax.onreadystatechange=function() 
								{ 
									if (ajax.readyState==4 && ajax.status==200)
									{ 
										strTratamientos = ajax.responseText;
										//console.log(strTratamientos);
										
										document.getElementById('codigoCtc').value = arrNoPos[1];
										document.getElementById('tipoProtocoloAuxCtc').value = arrNoPos[2];
										document.getElementById('idxCtc').value = arrNoPos[3];
										//document.getElementById('tratamientoCtc').value = 'JMCG';
										//$("#tratamientoCtc").trigger("focus");
										// setTimeout('',2000);
										// $("#tratamientoCtc").triggerHandler("focus");
										nombreArticuloAlert = $( "#wnmmed"+arrNoPos[2]+arrNoPos[3] ).val().substr( $( "#wnmmed"+arrNoPos[2]+arrNoPos[3] ).val().indexOf( "-" )+1 )
										
										
										prompt2('Informacion CTC','El medicamento ' + nombreArticuloAlert + ' es NO POS. Ingrese el tratamiento asociado para el paciente', 'abrirCtcArticulos',strTratamientos);

										$.blockUI({ message: $('#prompt'),
											css: {
												top:  '20%', 
												left: '30%', 
												width: '40%',
												height: '30%',
												overflow: 'auto',
												cursor: 'auto'
											}
										});		
										
									} 
								}
								if ( !estaEnProceso(ajax) ) {
									ajax.send(null);
								}
							}catch(e){	}
						}
					} 
				}
				if ( !estaEnProceso(ajax1) ) {
					ajax1.send(null);
				}
			}catch(e){}	
		
		
			// mostrarCtcArticulos2( arrNoPos[1], arrNoPos[2], arrNoPos[3], arrNoPos[4] );
		}
	}
}

// Cuando es adición por Horario Especial esta función se encarga de abrir la modal de artículos
// tantas veces como medicamentos que requieren insumos se detecten
// Abril 9 de 2013
function abrirModalMultiple()
{
	var filasModal = strPendientesModal.split( "\r\n" );

	if(filasModal.length-1 > 0)
	{
		var arrModal = filasModal[0].split( "|" );
		var cadenaReemplazar = filasModal[0]+"\r\n";
		
		strPendientesModal = strPendientesModal.replace(cadenaReemplazar,"")
		if(arrModal[0]!='')
		{
			// 					  codigo,    nomComercial,   nomGenerico, tipoMedLiq,    esGenerico,   idx,       componentesTipo);
			abrirModalArticulos( arrModal[1],  arrModal[2],  arrModal[3],  arrModal[4],  arrModal[5],  arrModal[6],  arrModal[7] );
		}
	}
}

function cerrarFormHCE(contExamen,basedatohce,formTipoOrden,historia,ingreso)
{
	//alert(cuentaExamenes+','+basedatohce+','+formTipoOrden+','+historia+','+ingreso)

	// Consulto si el formulario ha sido diligenciado en la historia clínica electrónica
	parametros = "consultaAjaxKardex=50&basedatoshce="+basedatohce+"&formTipoOrden="+formTipoOrden+"&historia="+historia+"&ingreso="+ingreso+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 

	try{
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function()
		{ 
			if (ajax.readyState==4 && ajax.status==200){
				var formDiligenciado = ajax.responseText;
				// totalExamenesAntesGrabar = formDiligenciado;
				totalExamenesDespuesGrabar = formDiligenciado;
				// if(formDiligenciado!='ok')
				if( totalExamenesDespuesGrabar == totalExamenesAntesGrabar )
				{
					if(confirm("Si sale de este formulario sin grabar, el examen será eliminado de la orden?"))
					{					
						$.unblockUI();
						//contExamen--;
						quitarExamen(contExamen,'','on','hce');
					}
				}
				else
				{
					$.unblockUI();
					$( "#hiFormHce" + contExamen ).val( totalExamenesDespuesGrabar );
					
					var aux1 = $( "#hiReqJus"+contExamen ).val();
					$( "#hiReqJus"+contExamen ).val( 'off' );
					grabarExamen( contExamen, false );
					$( "#hiReqJus"+contExamen ).val( aux1 );
				}
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}	

}

/*****************************************************************************************************
 * Marzo 12 de 2013
 *
 * Busco los cambios que hallan del CTC y en caso de algún cambio se muestra nuevamente la información 
 * del CTC para que el medico visualice los cambios
 *****************************************************************************************************/
function buscarCambiosCTC( dvCodArt ){

	//$( "[name=tiempoTratamientoNoPos]", divAux )[0].objArts;
	var val = false;
	
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
				if( dvs[x].id.substr( 0, dvs[x].id.indexOf( "-" ) ).toLowerCase() == "dv"+dvCodArt.toLowerCase() ){
					var contenedorInfoNoPos = dvs[x];
				}
			}
			
			//Aquí debo buscar el campo que contiene los objetos para compara cambios
			if( contenedorInfoNoPos ){
				
				var objs = $( "[name=tiempoTratamientoNoPos]", contenedorInfoNoPos )[0].objArts;
				
				for( var x in objs.idxs ){
					
					var idAux = objs.idxs[ x ];
					
					if( document.getElementById("wperiod"+idAux) ){					
					
						var inCodArtsCTC = objs;
						
						if( inCodArtsCTC.inf[ x ].frecuencia != document.getElementById("wperiod"+idAux).value ){
							inCodArtsCTC.inf[ x ].frecuencia = document.getElementById("wperiod"+idAux).value;
							val = true;
						}
						
						if( inCodArtsCTC.inf[ x ].dosis != document.getElementById("wdosis"+idAux).value ){
							inCodArtsCTC.inf[ x ].dosis = document.getElementById("wdosis"+idAux).value
							val = true;
						}
						
						if( inCodArtsCTC.inf[ x ].dosisMaxima != document.getElementById("wdosmax"+idAux).value ){
							inCodArtsCTC.inf[ x ].dosisMaxima = document.getElementById("wdosmax"+idAux).value
							val = true;
						}
						
						if( inCodArtsCTC.inf[ x ].diasTto != document.getElementById("wdiastto"+idAux).value ){
							inCodArtsCTC.inf[ x ].diasTto = document.getElementById("wdiastto"+idAux).value
							val = true;
						}
						
						//inCodArtsCTC.inf[ x ].canManejo = document.getElementById("whcmanejo"+idAux).value;
						// var origen = document.getElementById( "wnmmed"+idAux ).value.split( "-" )[1];
						// inCodArtsCTC.inf[ x ].origen = origen;
						
						var fechaHoraInicio = document.getElementById( 'wfinicio'+idAux ).value.split( " a las:" );
						inCodArtsCTC.inf[ x ].fin = fechaHoraInicio[0];		//Fecha de inicio del medicamento
						inCodArtsCTC.inf[ x ].hin = fechaHoraInicio[1];		//Hora de inicio del medicamento
					}
					else{
						val = true;
					}
				}
				
				//Busco diferencias por cantidad de articulos
				//Busco todos los articulos que tengan el mismo codigo
				//Para ello busco todo los input
				
				var tbsContenedores = new Array( "tbDetalleAddN" );
				
				var canArticulos = 0;
				
				for( var x = 0; x < tbsContenedores.length; x++ ){
					
					var tbContenedor = $( "#"+tbsContenedores[x] )[0];	//Busco la tabla que contiene los medicamentos
					
					/****************************************************************************************************
					 * Busco todos los input de la tabla contenedor
					 * y los guardo en un array
					 ****************************************************************************************************/
					var inCodigos = tbContenedor.getElementsByTagName( "input" );
					
					for( var i = 0; i < inCodigos.length; i++ ){
					
						//Si comineza el id con wnmmed, significa que es un campo con codigo del articulo
						if( inCodigos[i].id.substr( 0, 6 ) == "wnmmed" ){
							
							//Solo busco aquellos que tengan el mismo codigo
							//El codigo esta separado por un guion (-)
							if( inCodigos[i].value.substr( 0, inCodigos[i].value.indexOf( "-" ) ) == dvCodArt ){
								canArticulos++;
							}
						}
					}
				}
				
				//Si canArticulos es 0, significa que NO hay articulos a grabar
				if( canArticulos > 0 ){
					if( canArticulos != objs.idxs.length ){
						val = true;
					}
				}
				else{
					val = false;
				}
				/****************************************************************************************************/
			}
		}
	}
	
	return val;
}

//Noviembre 14 de 2012
//Copia de mostrarCtcArticulos
function mostrarCtcArticulos2( codArticulo, protocolo, id, tratamiento, deAlta  ){
	var idx = protocolo+id;

	if(deAlta && deAlta=="on")
		var txtImp = "Imp";
	else
		var txtImp = "";
	
	//Se verfica si tiene horario especial, si tiene horario especial solo se muestra
	//el ctc si es el último medicamento
	var hayMasHE = 0;
	
	for( var i = 2; i <= 24; i += 2 ){
		
		if( document.getElementById( 'dosisRonda' + i ) && document.getElementById( 'dosisRonda' + i ).value != '' ){
		
			hayMasHE++;
		}
	}
	
	//Si es uno significa que es el ultimo y si es 0 significa que no tenía horario especial
	if( hayMasHE == 1 || hayMasHE == 0 ){	
	
		//Creo un objeto con todas las propiedades necesarias
		var inCodArtsCTC = { art: '', 
							 inf: [],
							 idxs:[]
						   };
						   
		// var tbsContenedores = new Array( "tbDetalleAddN", "tbDetalleAddImpN" );
		var tbsContenedores = new Array( "tbDetalleAddN" );
		
		for( var x = 0; x < tbsContenedores.length; x++ ){
		
			//Buscar todos los medicamentos que tengan el mismo código
			var tbContenedor = $( "#"+tbsContenedores[x] )[0];	//Busco la tabla que contiene los medicamentos
		
			/****************************************************************************************************
			 * Busco todos los input de la tabla contenedor
			 * y los guardo en un array
			 ****************************************************************************************************/
			var inCodigos = tbContenedor.getElementsByTagName( "input" );
			
			for( var i = 0; i < inCodigos.length; i++ ){
			
				//Si comineza el id con wnmmed, significa que es un campo con codigo del articulo
				if( inCodigos[i].id.substr( 0, 6 ) == "wnmmed" ){
					
					//Solo busco aquellos que tengan el mismo codigo
					//El codigo esta separado por un guion (-)
					if( inCodigos[i].value.substr( 0, inCodigos[i].value.indexOf( "-" ) ) == codArticulo ){
						
						//Guardo el campo en un array
						inCodArtsCTC.art = codArticulo;		//Codigo del articulo
						
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length ] = {};
						
						var idAux = inCodigos[i].id.substr( 6 );
						
						inCodArtsCTC.idxs[ inCodArtsCTC.idxs.length ] = idAux;
						
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].frecuencia = document.getElementById("wperiod"+idAux).value;;
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].dosis = document.getElementById("wdosis"+idAux).value;
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].canManejo = document.getElementById("whcmanejo"+idAux).value;
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].dosisMaxima = document.getElementById("wdosmax"+idAux).value;	//Esto es cantidad
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].cantidad = document.getElementById("wdosmax"+idAux).value;	//Esto es cantidad
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].diasTto = document.getElementById("wdiastto"+idAux).value;
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].posologia = document.getElementById("wposologia"+idAux).value;
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].unidadPosologia = document.getElementById("wunidadposologia"+idAux).value;
						
						var origen = document.getElementById( "wnmmed"+idAux ).value.split( "-" )[1];
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].origen = origen;
						
						var fechaHoraInicio = document.getElementById( 'wfinicio'+idAux ).value.split( " a las:" );
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].fin = fechaHoraInicio[0];		//FEcha de inicio del medicamento
						inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].hin = fechaHoraInicio[1];		//Hora de inicio del medicamento
					}
				}
			}
			/****************************************************************************************************/
		}
		
		/****************************************************************************************************
		 * Verifico que no exista un CTC para el articulo 
		 * Si ya tiene un CTC debo abrir el formulario encontrado y modificar los campos que sean necesarios
		 ****************************************************************************************************/
		divEncontrado = false;
		
		var dvCtcAux = document.getElementById('ctcArticulos');
		
		if( dvCtcAux ){
			
			var dvs = dvCtcAux.getElementsByTagName( "div" );
			
			for( var i = 0; i < dvs.length; i++ ){
				if( dvs[i].id.substr( 0, dvs[i].id.indexOf( "-" ) ) == "dv"+codArticulo ){
					divEncontrado = dvs[i];
				}
			}
		}
		/****************************************************************************************************/
		
		
		//Ahora creo una url con los parametros encontrados
		//Para ello recorro todo el objeto que requiero
		var parametros = "";
		
		if( inCodArtsCTC.inf.length > 0 ){
			
			for( var i = 0; i < inCodArtsCTC.inf.length; i++ ){
				
				for( var x in inCodArtsCTC.inf[i] ){
					parametros += "&" + x + "[" + i + "]=" + inCodArtsCTC.inf[ i ][ x ];
				}
			}
		}
		
		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;
		var fecha = document.forms.forma.wfecha.value;
		var wemp_pmla= document.forms.forma.wemp_pmla.value;
					 
		parametros = "wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fecha+"&codArticulo="+codArticulo+"&idx="+idx + parametros +"&protocolo="+protocolo+"&id="+id+"&tratamiento="+tratamiento+"&tiempoTratamiento="+$( "#wdiastto"+idAux ).val();
		try{
			
			ajax=nuevoAjax();
			
			ajax.open("POST", "./generarCTCArticulosIDC.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if( ajax.responseText != '' ){
			
				if( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) ){
					document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ).parentNode.removeChild( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) );
				}			
				
				//Creo el div que contendrá todos los ctc de procedimientos
				if( !document.getElementById('ctcArticulos') ){
					var divAux = document.createElement( "div" );
					
					divAux.innerHTML = "<div id='ctcArticulos' style='display:none'>"
									 + "<INPUT TYPE='hidden' name='hiArtsNoPos' id='hiArtsNoPos' value=''>"
									 + "</div>";
					
					document.forms[0].appendChild(divAux.firstChild);
				}
				
				document.getElementById('hiArtsNoPos').value += ',' + codArticulo + '-' +idx;
				
				if( !document.getElementById('ctcArtsTemp') ){
				
					var divAux = document.createElement( "div" );
					
					divAux.style.display = 'none';
					divAux.style.width = '80%';
					divAux.id = 'ctcArtsTemp';
				}
				else{
					var divAux = document.getElementById('ctcArtsTemp');
				}
				
				divAux.innerHTML = ajax.responseText;
				
				//Si ya había un CTC igualo todos los campos al Ctc encontrado
				if( divEncontrado ){
				
					//Campos que necesito igualar
					var camposBuscar = new Array( "select", "input", "textarea" );
					
					//Recorro el array de los campos segun las etiquetas
					for( var i = 0; i < camposBuscar.length; i++ ){
						
						//Busco todas las etiquetas segun el array
						var varCmps = divAux.getElementsByTagName( camposBuscar[i] ); 
						
						//Recorro cada uno de los campos encontrados
						for( var j = 0; j < varCmps.length; j++ ){
							
							//Si el campo tiene un name
							//Significa que lo debo igualar al CTC que ya había encontrado
							//A excepción de posología y dosis por día
							if( varCmps[j].name != '' && varCmps[j].name != "posologiaNoPos" && varCmps[j].name != "ddNoPos" ){
								
								if( camposBuscar[i] != 'input' 
									|| camposBuscar[i] == 'input' 
									&& ( varCmps[j].type.toLowerCase() != "radio" && varCmps[j].type.toLowerCase() != "checkbox" )
								){
									//Busco el campo con el  mismo nombre en el CTC Anterior e igualos sus valores
									varCmps[j].value = $( "[name="+varCmps[j].name+"]", divEncontrado ).val();
								}
								else{
									
									switch( varCmps[j].type.toLowerCase() ){
										
										case 'radio': 
										
											$( "[name="+varCmps[j].name+"]", divEncontrado ).each(
												function(k){
													if( varCmps[j].value == $(this).val() ){
														varCmps[j].checked = $(this).is(':checked');
													}
												}
											);
										
											// if( varCmps[j].checked && varCmps[j].value == $( "[name="+varCmps[j].name+"]", divEncontrado ).val() ){
												// varCmps[j].checked = true;
											// }
										break;
										
										case 'checkbox': 
											varCmps[j].checked = $( "[name="+varCmps[j].name+"]", divEncontrado )[0].checked;
										break;
									}
								}
							}
						}
					}
					
					//Despues de replicar los datos, borra el div anterior
					try{
						divEncontrado.parentNode.removeChild(divEncontrado);
					}
					catch(e){}
				}
				
				
				
				//Busco el campo Tiempo de tratamiento para agregar el objeto con el que se calcula la cantidad
				$( "[name=tiempoTratamientoNoPos]", divAux )[0].objArts = inCodArtsCTC;
				$( "input[value=Cerrar]", divAux )[0].objArts = inCodArtsCTC;
				
				document.forms[0].appendChild(divAux);
			
				//agrego el medicamento que tiene CTC a la variable global
				arCTCArticulos[ codArticulo ] = inCodArtsCTC.idxs.length;	//lo igualo con el total de articulos a grabar
				
				//Si ya había un CTC igualo todos los campos al Ctc encontrado
				if( divEncontrado ){
				
					//Recalculo la cantidad
					$( "[name=tiempoTratamientoNoPos]", divAux )[0].onchange();
				}
				else{
					/****************************************************************
					 * Permite solo numeros para un campo
					 ****************************************************************/
					
					$( "[name=cantidadNoPos]", divAux ).keypress(function(e){
							var key = e.keyCode || e.which;
							
							if( key != 9 && key != 8 ){
								if( String.fromCharCode(key).search( /[0-9]/g ) == -1 ){
									e.preventDefault();
								}
							}
						}
					);
					
					// $( "[name=ddNoPos],[name=posologiaNoPos]", divAux ).keypress(function(e){
							// var key = e.keyCode || e.which;
							
							// if( key != 9 && key != 8 ){
								// if( String.fromCharCode(key).search( /^[0-9]*\.?[0-9]*$/ ) == -1 ){
									// e.preventDefault();
								// }
							// }
						// }
					// );
				}
				
				//Creo el autocomplete para el Cie10
				$( "[name=txtDxCIE10]", divAux ).autocomplete("ordenesidc.inc.php?consultaAjaxKardex=53&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value, 
				{
					// extraParams: {
						// cie10: function( campo ){
							// return $( "#"+ $( conFocus ).attr( "srcPai" ) ).val(); 
						// } 
					// },
					cacheLength:0,
					delay:0,
					max: 100,
					scroll: false,
					scrollHeight: 500,
					matchSubset: false,
					matchContains: true,
					width:1000,
					minChars: 3,
					autoFill:false,
					json: "json",
					formatItem: function(data, value){
						eval( "var datos = "+data );
						return datos[0].valor.cod + "-" + datos[0].valor.des;
					},
					formatResult: function(data, value){
						//convierto el string en json
						eval( "var datos = "+data );
						
						return datos[0].valor.cod + "-" + datos[0].valor.des;
					}
				}).result(
					function(event, item ){
						
						// //La respuesta es un json
						// //convierto el string en formato json
						eval( "var datos = "+item );
						
						//Guardo el ultimo valor que selecciona el usuario
						//Esto en una propiedad inventada
						this.value = datos[0].valor.cod + "-" + datos[0].valor.des;
					}
				)
			
				$.blockUI({ message: $('#ctcArtsTemp'),
							css: {
								top:  '5%', 
								left: '10%', 
								width: '80%',
								height: '90%',
								overflow: 'auto',
								cursor: 'auto'
							}
				});
			}
					
		}
		catch(e){ 
			//alert( "Error: " + e );
			$.unblockUI();
		}
	}
}

//Noviembre 14 de 2012
function mostrarCtcArticulos( codArticulo, protocolo, id  ){
	var idx = protocolo+id;

	var parametros = "";
	
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	
	//Busco datos adicionales
	var frecuencia = document.getElementById("wperiod"+idx).value;
	var dosis = document.getElementById("wdosis"+idx).value;
	var canManejo = document.getElementById("whcmanejo"+idx).value;
	var dosisMaxima = document.getElementById("wdosmax"+idx).value;
	var diasTto = document.getElementById("wdiastto"+idx).value;
	var med = document.getElementById( "wnmmed"+idx ).value;
    med = med.split( "-" );
	
	var fechaInicio = document.getElementById( 'wfinicio'+idx ).value.split( " a las:" );
	
	var fin = fechaInicio[0];				//fecha de inicio
	var hin = fechaInicio[1]; 				//hora de inicio
	
	// generarCTCprocedimientos.php?wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fechaKardex
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	parametros = "wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fecha+"&codArticulo="+codArticulo+"&idx="+idx
	             +"&frecuencia="+frecuencia
				 +"&dosis="+dosis
				 +"&canManejo="+canManejo
				 +"&dosisMaxima="+dosisMaxima
				 +"&diasTto="+diasTto
				 +"&protocolo="+protocolo
				 +"&id="+id
				 +"&origen="+med[1]        //Este es el origen del medicamento (SF O CM)
				 +"&fin="+fin
				 +"&hin="+hin;
	
	try{
		
		ajax=nuevoAjax();
		
		ajax.open("POST", "./generarCTCArticulosIDC.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if( ajax.responseText != '' ){
		
			if( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) ){
				document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ).parentNode.removeChild( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) );
			}			
			
			//Creo el div que contendrá todos los ctc de procedimientos
			if( !document.getElementById('ctcArticulos') ){
				var divAux = document.createElement( "div" );
				
				divAux.innerHTML = "<div id='ctcArticulos' style='display:none'>"
				                 + "<INPUT TYPE='hidden' name='hiArtsNoPos' id='hiArtsNoPos' value=''>"
								 + "</div>";
				
				document.forms[0].appendChild(divAux.firstChild);
			}
			
			document.getElementById('hiArtsNoPos').value += ',' + codArticulo + '-' +idx;
			
			if( !document.getElementById('ctcArtsTemp') ){
			
				var divAux = document.createElement( "div" );
				
				divAux.style.display = 'none';
				divAux.style.width = '80%';
				divAux.id = 'ctcArtsTemp';
			}
			else{
				var divAux = document.getElementById('ctcArtsTemp');
			}
			
			divAux.innerHTML = ajax.responseText;
		
			document.forms[0].appendChild(divAux);
		
			$.blockUI({ message: $('#ctcArtsTemp'),
						css: {
							top:  '5%', 
							left: '10%', 
							width: '80%',
							height: '90%',
							overflow: 'auto',
							cursor: 'auto'
						}
			});
		}
				
	}
	catch(e){ 
		//alert( "Error: " + e );
		$.unblockUI();
	}
}


//Noviembre 08 de 2012
function mostrarCtcProcedimientos( codExamen, cuentaExamenes ){
	var parametros = "";
	
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	
	// generarCTCprocedimientos.php?wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fechaKardex
	parametros = "wemp_pmla=10&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fecha+"&codExamen="+codExamen+"&idExamen="+cuentaExamenes;
		
	try{
		
		var ajax=nuevoAjax();
		
		ajax.open("POST", "generarCTCProcedimientos.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		if( ajax.responseText != '' ){
			
			//Creo el div que contendrá todos los ctc de procedimientos
			if( !document.getElementById('ctcProcedimientos') ){
				var divAux = document.createElement( "div" );
				
				divAux.innerHTML = "<div id='ctcProcedimientos' style='display:none'>"
				                 + "<INPUT TYPE='hidden' name='hiProcsNoPos' id='hiProcsNoPos' value=''>"
								 + "</div>";
				
				document.forms[0].appendChild(divAux.firstChild);
			}
			
			document.getElementById('hiProcsNoPos').value += ',' + codExamen + '-' +cuentaExamenes;
			
			if( !document.getElementById('ctcProcTemp') ){
			
				var divAux = document.createElement( "div" );
				
				divAux.style.display = 'none';
				divAux.style.width = '80%';
				divAux.id = 'ctcProcTemp';
			}
			else{
				var divAux = document.getElementById('ctcProcTemp');
			}
			
			divAux.innerHTML = ajax.responseText;
		
			document.forms[0].appendChild(divAux);
		
			$.blockUI({ message: $('#ctcProcTemp'),
						css: {
							top:  '5%', 
							left: '10%', 
							width: '80%',
							height: '90%',
							overflow: 'auto',
							cursor: 'auto'
						}
			});
			
			// $( "input[name=fechaProcedimientoPrevio2]", divAux ).datepicker({
					// dateFormat: "yy-mm-dd",
					// showOn: "button",
					// buttonText: "..."
				// });
			
			// $( "input[name=fechaProcedimientoPrevio1]", divAux ).datepicker({
					// dateFormat: "yy-mm-dd",
					// showOn: "button",
					// buttonText: "..."
				// });
			
			
			// try{
				// document.getElementById( "ui-datepicker-div" ).style.zIndex = "10000000000";
			// }
			// catch(e){
				// alert( "Error: " + e );
			// }
		}
				
	}
	catch(e){ 
		//alert( "Error: " + e );
		$.unblockUI();
	}
}

/**********************************************************************
 * Junio 27 de 2012
 *
 * Determina si una variable ha sido declarada
 * Cumple la misma función que isset en PHP
 **********************************************************************/
function isset(variable_name) 
{
	try {
		 if (typeof(eval(variable_name)) != 'undefined')
		 if (eval(variable_name) != null)
		 return true;
	 } catch(e) { }
	return false;
}


/**********************************************************************
 * Octubre 17 de 2012
 *
 * Quita espacios en blanco al principio y final de una cadena
 * Cumple la misma función que trim en PHP
 **********************************************************************/
function trim (myString)
{
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
}


/**********************************************************************
 * marzo 1 de 2013
 *
 * Abre la ventana especificada
 **********************************************************************/
function abrir_ventana ( pagina, wemp_pmla, historia, ingreso, diagnostico, tipoimp ) {
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=670, height=470, top=20, left=40";
	pagina += "?wemp_pmla="+wemp_pmla+"&whistoria="+historia+"&wingreso="+ingreso+"&wdiagnostico="+diagnostico+"&tipoimp="+tipoimp;
	window.open(pagina,"",opciones);
}

/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function traeJustificacionHCE(campoChk,campoDestino, cmpPadre ){
	
	if(!cmpPadre )
		var campoJustificacion = document.getElementById(campoDestino);
	else{
		var campoJustificacion = $( "#"+campoDestino, $("#"+cmpPadre ) )[0];
	}

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;		
	
	if(campoChk.checked == false)
	{
		if(confirm('Desea borrar el texto de la justificación?'))
		{
			campoJustificacion.value = '';
		}
		else
		{
			campoChk.checked = true;
		}
		return false;
	}
	
	var parametros = "consultaAjaxKardex=46&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&whistoria="+historia+"&wingreso="+ingreso; 

	try{
		//$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){
				if(ajax.responseText!="")
				{
					if(campoChk.checked)
					{
						if(campoJustificacion.value=='' || campoJustificacion.value==' ')
							campoJustificacion.value = ajax.responseText;
						else
							campoChk.checked = false;
					}
					else
					{
						campoJustificacion.value = '';
					}
				}
				else
				{	
					alert('No se encontró resumen de historía clínica para el paciente');
					campoChk.checked = false;
				}
			} 
			//$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}


/**********************************************************************
 * Junio 19 de 2012
 *
 * Carga articulos del kardex del día anterior no suspendidos
 **********************************************************************/
function cargarMedicamentosAnteriores(historia,ingreso,fecha,cco){

	var parametros = ""; 

	parametros = "consultaAjaxKardex=32&wbasedato="+document.forms.forma.wbasedato.value+"&wcenmez=cenpro&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha+"&wemp_pmla="+document.forms.forma.wemp_pmla.value;
				+"&cco="+cco
		
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "./ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{
			if (ajax.readyState==4 && ajax.status==200){

				if(ajax.responseText != ""){
				
					var listaArticulos = ajax.responseText.split( "@" );
					
					for( var i = 0; i < listaArticulos.length; i++ ){
						
						//Creo la fila para agregar el articulo
						agregarArticulo( "detKardexAdd", false, datosArticulo[ 0 ] );
						
						//Busco cada uno de los datos de los medicamentos
						var datosArticulo = listaArticulos[i].split( "','" );
						
						var codigo = datosArticulo[ 0 ];
						var nombre = datosArticulo[ 1 ];
						var origen = datosArticulo[ 2 ];
						var grupo = datosArticulo[ 3 ];
						var forma = datosArticulo[ 4 ];
						var unidad = datosArticulo[ 5 ];
						var pos = datosArticulo[ 6 ];
						var unidadFraccion  = datosArticulo[ 7 ];
						var cantFracciones  = datosArticulo[ 8 ];
						var vencimiento  = datosArticulo[ 9 ];
						var diasVencimiento = datosArticulo[ 10 ];
						var dispensable = datosArticulo[ 11 ];
						var duplicable = datosArticulo[ 12 ];
						var diasMaximos = datosArticulo[ 13 ];
						var dosisMaximas = datosArticulo[ 14 ];
						var via	= datosArticulo[ 15 ];
						var tipoProtocolo = datosArticulo[ 16 ];
						var noEnviar = datosArticulo[ 17 ];
						
						//Agrego la fila con los datos correspondientes
						seleccionarMedicamento(codigo, nombre, origen, grupo, forma, unidad, pos, unidadFraccion, cantFracciones, vencimiento, diasVencimiento, dispensable, duplicable, diasMaximos, dosisMaximas, via, tipoProtocolo, noEnviar )
						
						var indice = elementosDetalle-1;
						
						//Seteo cada uno de los campos a sus valores por defecto
						document.getElementById( "wdosis"+tipoProtocolo+indice ).value = datosArticulo[ 20 ];
						document.getElementById( "wperiod"+tipoProtocolo+indice ).value = datosArticulo[ 21 ];
						document.getElementById( "wviadmon"+tipoProtocolo+indice ).value = datosArticulo[ 26 ];
						//alert( "....finicio: "+datosArticulo[ 18 ] + " a las:"+datosArticulo[ 19 ] );
						document.getElementById( "wfinicio"+tipoProtocolo+indice ).value = datosArticulo[ 18 ] + " a las:"+datosArticulo[ 19 ];
						document.getElementById( "wcondicion"+tipoProtocolo+indice ).value = datosArticulo[ 22 ];
						document.getElementById( "wchkconf"+tipoProtocolo+indice ).checked = datosArticulo[ 23 ] == 'on' ? true: false;
						document.getElementById( "wdiastto"+tipoProtocolo+indice ).value = datosArticulo[ 24 ];
						document.getElementById( "wdosmax"+tipoProtocolo+indice ).value = datosArticulo[ 25 ];
						
						//Oculto el boton para traer los medicamentos del día anterior
						var auxTb = document.getElementById( "tbCargarMedicamentosAnteriores" );
						auxTb.style.display = 'none';
						
						document.getElementById("btnCerrarVentana").style.display = 'none';
						
					}
				}
				$.unblockUI();
			}
		}		
	}catch(e){ 
		$.unblockUI(); 
		alert(e);
	}
	
}


function agregarDosisMaxPorCondicion( tipo, indice, campo ){

	/********************************************************************************
	 * Junio 13 de 2012
	 *
	 * Si una condicicon de suministro tiene dosis maxima por defecto se llena 
	 * la dosis maxima
	 ********************************************************************************/
	if( campo && campo.id.substr(0,10) == "wcondicion" ){
		
		//Consulto valor por defecto de la dosis maxima
		var condicion = document.getElementById( "wcondicion"+tipo+indice ).value;
		var valDma = dmaPorCondicionesSuministro[ condicion ].dma;	//Dosis maxima	
		
		//Si existe un valor por defecto de dosis maxima
		if( valDma ){
			if( valDma > 0 ){
				document.getElementById( "wdosmax"+tipo+indice ).value = valDma;
				
				//Borro dias de tratamiento por si hay algo escrito, 
				//ya que dosis maximas y dias de tratamiento son mutuamente excluyentes
				document.getElementById( "wdiastto"+tipo+indice ).value = '';
				
				inhabilitarDiasTratamiento( document.getElementById( "wdosmax"+tipo+indice ), tipo, indice )
				//document.getElementById( "wdiastto"+tipo+indice ).enabled = true;
			}
		}
	}
	/********************************************************************************/
}

/************************************************************************/

//Array doble esto es para validar los medicamentos y poder sacar el mensaje si el usuario cierra con la x
//[][0]	Codigo del articulo
//[][1] NOmbre del articulo
var articulosSinGrabar = Array();	//Indica que ariculos no se han grabado correctamente
var noMostarMsjError = false;


function quitarTooltip( campo ){
	campo.parentNode.originalTooltipText = campo.parentNode.tooltipText;
	campo.parentNode.tooltipText = null
}

function reestablecerTooltip( campo ){
	campo.parentNode.tooltipText = campo.parentNode.originalTooltipText;
}

/********************************************************************************
 * Mayo 30 de 2012
 *
 * Esto permite cambiar el valor del tooltip cuando esta en consulta el kardex
 ********************************************************************************/
var titleHoras = '';
 
function mostrarTitleTooltip(){
	
	if( titleHoras != '' ){
		var a = titleHoras;
		titleHoras = '';
		return a;
	}
	else{
		return this.tooltipText;
	}
}

function mostrarTooltip( celda, valor ){
	
	titleHoras = valor;
}
/********************************************************************************/


// Permite intercalar la clase de un elemento contenedor por otra
function quitarFilaAlta(fila,checkimp)
{
	$('#'+fila).hide(); 
	document.getElementById(checkimp).checked = false;
}

function quitarFilaAltaExamen(fila,checkimp)
{
	$('#'+fila).hide(); 
	document.getElementById(checkimp).checked = false;
}

// Permite intercalar la clase de un elemento contenedor por otra
function cambiarClase(contenedor,clase1,clase2)
{
	//$('#'+contenedor).toggleClass(claseAlterna);
	//var claseImprimir = $('#'+contenedor).attr('class');
	//if(claseImprimir=='opacar')
	if( $('#'+contenedor).hasClass(clase1) )
	{
		$('#'+contenedor).removeClass(clase1);
		$('#'+contenedor).addClass(clase2);
	}
	else
	{
		$('#'+contenedor).removeClass(clase2);
		$('#'+contenedor).addClass(clase1);
	}
}

/********************************************************************************
 * Deshabilita el campo de dias de tratamiento si dosis maximas tiene algun valor
 ********************************************************************************/
function inhabilitarDiasTratamiento( campo, tipo, id ){
	
	if(isset(document.getElementById( 'wdiastto'+tipo+id )))
	{
		var inDtto = document.getElementById( 'wdiastto'+tipo+id );

		if( campo.value != '' ){
			inDtto.readOnly = true;
		}
		else{
			inDtto.readOnly = false;
		}
	}
}

/********************************************************************************
 * Deshabilita el campo de dosis maxima si dias de tratamiento tiene algun valor
 ********************************************************************************/
function inhabilitarDosisMaxima( campo, tipo, id ){

	if(isset(document.getElementById( 'wdosmax'+tipo+id )))
	{
		var inDosisMaximas = document.getElementById( 'wdosmax'+tipo+id );
		
		if( campo.value != '' ){
			inDosisMaximas.readOnly = true;
		}
		else{
			inDosisMaximas.readOnly = false;
		}
	}
}

function eleccionFrecuencia(frecuencia){

		if(frecuencia=='H.E.')
		{
			document.getElementById( "regletaGrabacion" ).style.display = '';
			document.getElementById( "wdosisfamilia" ).value = '';
			document.getElementById( "wdosisfamilia" ).disabled = true;
		}
		else
		{
			document.getElementById( "regletaGrabacion" ).style.display = 'none';
			if(!esLiquidoEndovenoso)
				document.getElementById( "wdosisfamilia" ).disabled = false;
		}
}

function eleccionPreviaMedicamento(campo, e){
	
	var evento = e || window.event;
	
	var k = e.keyCode || e.which;

	if( k == 13 ){
		eleccionMedicamento();
	}

	campo.onblur();
}

function calcularDifDias(fecha,dif){ 
	var f=new Array(),i; 
	var milisegundos=parseInt(24*60*60*1000)*dif; 
	fecha=fecha.split("/").reverse(); 
	fecha=new Date(fecha.join("/")); 
	var tiempo=fecha.getTime(); 
	fecha.setTime(parseInt(tiempo+milisegundos)); 
	f[2]=fecha.getDate(); 
	f[1]=fecha.getMonth()+1; 
	f[0]=fecha.getFullYear(); 
	if(f[1]<10) 
		f[1]="0"+f[1]; 
	if(f[2]<10) 
		f[2]="0"+f[2];
	return f.join("-"); 
}

function calcularFechaInicio(fechaInicioFija,cont2) 
{
	var auxFecha = new Array(),strFecha = new Array(),auxStrFecha = new Array();
	var auxFecha2 = "";
	auxFecha = fechaInicioFija.split(":");
	if(cont2 >= auxFecha[1]*1)
	{
		fechaInicioFija = auxFecha[0]+":"+cont2+":"+auxFecha[2];
	}
	else
	{
		strFecha = auxFecha[0].split(" ");
		auxStrFecha = strFecha[0].split("-");
		auxFecha2 = auxStrFecha[2]+"/"+auxStrFecha[1]+"/"+auxStrFecha[0];
		fechaInicioFija = calcularDifDias(auxFecha2,'1');
		fechaInicioFija = fechaInicioFija + " " + strFecha[1] + " " + strFecha[2] + ":" + cont2 + ":" + auxFecha[2];
	}
	//alert("cont2: "+cont2+" - fecIni: "+fechaInicioFija);
	return fechaInicioFija;
}


// Esta variable definía si se mostraba los mensajes de validación de datos
// Estaba condicionada para no mostrar el mensaje la segunda vez que ocurriera
// Se comenta porque si no se cumple la condición siempre se debe mostrar el mensje de validación
// var mostrarAviso = 1;

// Determina si la adición del medicamento es por horario especial
var horarioEspecial = false;
// Si contHorarioEspecial = 1; asi sea adición por horario especial
// se saca mensaje 'El medicamento ya existe. Desea agregarlo?'
var contHorarioEspecial = 0;

// Cadena que contendrá los artículos de los que ya se ha mostrado el mensaje que son NO POS
var msjNoPos = '';

// Guardará los datos de artículos y procedimientos NO POS cuando se ingresan por adición múltiple, de modo
// que se pueda llamar al final la ventana modal del formulario CTC para cada artículo o procedimiento NO POS
strPendientesCTC = "";

// Guardará los datos de artículos DA cuando se ingresan por horario especial
strPendientesModal = "";


// Determina si la adición de medicamentos es múltiple (por ejemplo: Importar protocolo o Insumos Liquidos Endovenosos)
var adicionMultiple = false;

function eleccionMedicamento(porProtocolo)
{
	
	var articulo;
	var periodo;
	var administracion;
	var condicion;
	var presentacion;
	var medida;

	var parametros = "";

	if(!isset(porProtocolo))
		porProtocolo = 0;
	
	// Se reviza si hay algun detalle de familia desplegado y se oculta
	ocultarDetalleFliaAnterior();

	// Si no es adición de medicamentos por protocolo haga la validación de datos
	if(porProtocolo!=1)
	{
		//////////////////////////////////////////////////////////////////////
		//////// ASIGNACIÓN DE VARIABLES PARA LOS DATOS INGRESADOS ///////////

		// Asigno variables dadas en el formulario de ingreso
		var nombreFamilia = document.getElementById( "wnombrefamilia" ).value;		// Familia de medicamentos
		var presentaciones = document.getElementById( "wpresentacion" );			// Presentaciones o formas farmacéuticas
		var unidades = document.getElementById( "wunidad" );						// Unidades de medida
		var dosis = document.getElementById( "wdosisfamilia" ).value;				// Dosis a aplicar
		var frecuencias = document.getElementById( "wfrecuencia" );					// Frecuencias
		var viasAdministracion = document.getElementById( "wadministracion" );		// Vías de administración
		var fechaInicio = document.getElementById( "wfinicioaplicacion" ).value;	// Fecha y hora de inicio de aplicación
		var condiciones = document.getElementById( "wcondicionsum" );				// Condiciones de suministro
		var diasTto = document.getElementById( "wdiastratamiento" ).value;			// Días de tratamiento
		var dosisMax = document.getElementById( "wdosismaxima" ).value;				// Dosis máxima
		var posologia = document.getElementById( "wposologia" ).value;				// Posología
		var unidadPosologia = document.getElementById( "wunidadposologia" ).value;	// Unidad de medida Posología
		var observaciones = document.getElementById( "wtxtobservasiones" ).value;	// Observaciones
		var tipoManejo = document.getElementById( "wckInterno" ).checked;				// Tipo de manejo
		
		if( tipoManejo ){
			tipoManejo = 'on';
		}
		else{
			tipoManejo = 'off';
		}
		
		// Obtengo los indices seleccionados en los campos desplegables (select's)
		var selPresentacion = presentaciones.selectedIndex;
		var selUnidad = unidades.selectedIndex;
		var selFrecuencia = frecuencias.selectedIndex;
		var selViaAdministracion = viasAdministracion.selectedIndex;
		var selCondicion = condiciones.selectedIndex;

		// Se asigna el valor a la variable para la presentación o forma farmacéutica
		if(selPresentacion!=-1)
			presentacion = document.getElementById('wpresentacion').options[selPresentacion].value;
		else
			presentacion = "";
			
		// Se asigna el valor a la variable para la unidad de medida
		if(selUnidad!=-1)
			medida = document.getElementById('wunidad').options[document.getElementById( 'wunidad' ).selectedIndex].value;
		else
			medida = "";

		// Se asigna el valor a la variable para la unidad de medida
		if(selFrecuencia!=-1)
			periodo = document.getElementById('wfrecuencia').options[selFrecuencia].value;
		else
			periodo = "";

			
		// Se asigna el valor a la variable para la vía de administración
		if(selViaAdministracion!=-1)
			administracion = document.getElementById('wadministracion').options[selViaAdministracion].value;
		else
			administracion = "";

		// Se asigna el valor a la variable para la condición de suministro
		if(selCondicion!=-1)
			condicion = document.getElementById('wcondicionsum').options[selCondicion].value;
		else
			condicion = "";

		// Se usarán si el medicamento es líquido endovenoso
		periodoLQ = periodo;
		administracionLQ = administracion;
		fechaInicioLQ = fechaInicio;
		condicionLQ = condicion;
		diasTtoLQ = diasTto;
		dosisMaxLQ = dosisMax;
		observacionesLQ = observaciones;
		posologiaLQ = posologia;
		unidadPosologiaLQ = unidadPosologia;

		////// FIN ASIGNACIÓN DE VARIABLES PARA LOS DATOS INGRESADOS /////////
		//////////////////////////////////////////////////////////////////////		
		
		
		// arRelacionesUnidad = arRelUnidad;
		// arArticulosSeleccionados = arArticulos;

		//////////////////////////////////////////////////////////////////////
		///////// VALIDACIÓN DE LOS DATOS INGRESADOS /////////////////////////

		// Si no se ha ingresado el nombre de la familia o artículo, muestre el mensaje correspondiente
		if( document.getElementById( "wnombrefamilia" ).value == "" || document.getElementById( "wnombrefamilia" ).value == " " )
		{
			alert("Debe ingresar el nombre del artículo");
			document.getElementById( "wnombrefamilia" ).focus();
			return; 
		}

		// Validación presentación o forma farmacéutica
		if(selPresentacion>-1)
		{
			var presentacionMed = presentaciones.options[selPresentacion].text;
			if(presentacionMed=="" || presentacionMed==" ")
			{
				alert("Debe seleccionar una presentación");
				document.getElementById( "wpresentacion" ).focus();
				document.getElementById( "wpresentacion" ).select();
				return false;
			}	
		}
		else
		{
			alert("Debe seleccionar una presentación");
			document.getElementById( "wpresentacion" ).focus();
			document.getElementById( "wpresentacion" ).select();
			return false;
		}

		// Validación unidad de medida
		if(selUnidad>-1)
		{
			var unidMedida = unidades.options[selUnidad].text;
			if(unidMedida=="" || unidMedida==" ")
			{
				alert("Debe seleccionar una unidad de medida");
				document.getElementById( "wunidad" ).focus();
				document.getElementById( "wunidad" ).select();
				return false;
			}	
		}
		else
		{
			alert("Debe seleccionar una unidad de medida");
			document.getElementById( "wunidad" ).focus();
			document.getElementById( "wunidad" ).select();
			return false;
		}
		
		//Si la dosis es vacio no agregar
		if( dosisMax == "" ){
			alert("Debe ingresar la cantidad");
			return; 
		}

		if( document.getElementById('wckInterno').checked == false ){
		
			//Si la dosis es vacio no agregar
			if( dosis == "" && periodo != "H.E." && !esLiquidoEndovenoso ){
				alert("Debe ingresar la dosis");
				return; 
			}

			// Validación de frecuencia
			if(selFrecuencia>-1)
			{
				var frecuencia = frecuencias.options[selFrecuencia].text;
				if(frecuencia=="" || frecuencia==" ")
				{
					alert("Debe seleccionar la frecuencia");
					document.getElementById( "wfrecuencia" ).focus();
					document.getElementById( "wfrecuencia" ).select();
					return false;
				}	
			}
			else
			{
				alert("Debe seleccionar la frecuencia");
				document.getElementById( "wfrecuencia" ).focus();
				// document.getElementById( "wfrecuencia" ).select();
				return false;
			}

			// Validación de la vía de administración
			if(selViaAdministracion>-1)
			{
				var viaAdministracion = viasAdministracion.options[selViaAdministracion].text;
				if(viaAdministracion=="" || viaAdministracion==" ")
				{
					alert("Debe seleccionar una vía de administración");
					document.getElementById( "wadministracion" ).focus();
					document.getElementById( "wadministracion" ).select();
					return false;
				}	
			}
			else
			{
				alert("Debe seleccionar una vía de administración");
				document.getElementById( "wadministracion" ).focus();
				// document.getElementById( "wadministracion" ).select();
				return false;
			}
		}
		
		//Si tiempo de tratamiento es obligatorio
		var auxTD = $( "#nuevoBuscador > tbody > tr > td:contains(tto.)" );
		if( auxTD.html().substr(-3) == "(*)" && $( "#wdiastratamiento" ).val() == ""  ){
			alert( "Los días de tratamiento es obligatorio." );
			return false;
		}

		///////// FIN VALIDACIÓN DE LOS DATOS INGRESADOS /////////////////////
		//////////////////////////////////////////////////////////////////////
	
		//Se declaran las variables globales
		//arArticulosSeleccionados;
		arRelacionesUnidad;
		arRelacionesPresentacion;
		arRelacionesViaAdmon;
		
		// 2012-12-10 
		// Se comenta porque estas variables no tienen uso
		// var sel = arRelacionesUnidad[ selUnidad ];		// Unidad de medida
		// var sel2 = arRelacionesPresentacion[ selPresentacion ];	// Presentacion
		// var sel4 = arRelacionesViaAdmon[ selViaAdministracion ];	// Via administracion
		
		// 2012-12-14
		// alert("eleccionMedicamento: arRelacionesUnidad="+arRelacionesUnidad.toSource());
		// alert("eleccionMedicamento: selUnidad="+selUnidad);

		// alert("arRelacionesUnidad. "+arRelacionesUnidad.toSource());
		// alert("arArticulosSeleccionados: "+arArticulosSeleccionados.toSource());
		//alert("selUnidad: "+selUnidad);
	
		var art = "" ;
		
		// 2012-12-14
		/*
		// Se comenta porque ya esta validación se va a hacer en PHP por medio de AJAX
		for( var i = 0; i < arRelacionesUnidad[ selUnidad ].length; i++ )
		{
			
			var pos = arRelacionesUnidad[ selUnidad ][i]-1;
			// alert("selUnidad: "+selUnidad+" - i: "+i+" - pos: "+pos);
			if( arArticulosSeleccionados[ pos ][1]*1 == dosis*1 ){
				art = arArticulosSeleccionados[ pos ][0];
				break;
			}
			else if( arArticulosSeleccionados[ pos ][1]*1 > dosis*1 ){
				art = arArticulosSeleccionados[ pos ][0];
				break;
			}
			else{
				art = arArticulosSeleccionados[ pos ][0];
			}
			
			// 2012-12-14
			alert("Art. "+art);
		}
		*/
	}
	// 2013-01-14
	// Si es adición de medicamentos por protocolo
	else
	{

		/*
		// Ya no es necesario usarlas como array porque el ciclo se hace abajo cuando se agrega cada artículo
		var articulo = new Array();
		var periodo = new Array();
		var administracion = new Array();
		var condicion = new Array();
		var dosis = new Array();
		var observaciones = new Array();
		*/

		if(document.forms.forma.wprotocolo.value!="" && document.forms.forma.wprotocolo.value!=" ")
			var valorProtocolo = document.forms.forma.wprotocolo.value;
		else if(document.forms.forma.wprotocolo_ayd.value!="" && document.forms.forma.wprotocolo_ayd.value!=" ")
			var valorProtocolo = document.forms.forma.wprotocolo_ayd.value;
		else
			var valorProtocolo = document.forms.forma.wprotocolo.value;

		parametros = "consultaAjaxKardex=37&wemp_pmla="+document.forms.forma.wemp_pmla.value
					 +"&basedatos="+document.forms.forma.wbasedato.value
					 +"&protocolo="+valorProtocolo;

		try {
			ajax=nuevoAjax();
			ajax.open("POST", "ordenesidc.inc.php",false); 
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if( ajax.responseText != '' )
			{
				if(ajax.responseText != "No se encontraron coincidencias")
				{
					var protocolos = ajax.responseText.split( "\\" );

					/*
					for (var ipro=0;ipro<protocolos.length-1;ipro++)
					{
						var protocolo = protocolos[ipro].split( "|" );
						articulo[ipro] = protocolo[0];				// Dosis a aplicar
						periodo[ipro] = protocolo[1];				// Frecuencias
						administracion[ipro] = protocolo[2];		// Vías de administración
						condicion[ipro] = protocolo[3];				// Condiciones de suministro
						dosisMax[ipro] = protocolo[4];				// Dosis máxima
						observaciones[ipro] = protocolo[5];			// Observaciones
						
						nombreFamilia = '';		// Familia de medicamentos
						presentacion = '';			// Presentaciones o formas farmacéuticas
						medida = '';						// Unidades de medida
						dosis = '';				// Dosis a aplicar
						fechaInicio = '';	// Fecha y hora de inicio de aplicación
						diasTto = '';			// Días de tratamiento
					}
					*/
				}
			}
			
		}catch(e){	}
	}

	document.getElementById( "trEncabezadoTbAdd" ).style.display = '';

	//buscando el medicamento
	var mensaje = "";

	// 2013-01-14
	// Si no es adición de medicamentos por protocolo
	if(porProtocolo!=1)
	{
		parametros = "consultaAjaxKardex=35&wemp_pmla="+document.forms.forma.wemp_pmla.value
					 +"&basedatos="+document.forms.forma.wbasedato.value
					 +"&cenmez="+document.forms.forma.wcenmez.value
					 +"&ccoPaciente="+document.forms.forma.wservicio.value
					 +"&q="+encodeURIComponent( nombreFamilia )
					 +"&pre="+presentacion
					 +"&med="+medida
					 +"&dos="+dosis
					 +"&adm="+administracion;

					 
		try{
			
			ajax=nuevoAjax();
			
			ajax.open("POST", "ordenesidc.inc.php",false); 
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);
			
			if( ajax.responseText != '' ){
				if(ajax.responseText != "No se encontraron coincidencias"){

					var item = ajax.responseText.split( "|" );
					
	//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));
					
					/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
					 * Argumentos:
					 * 
					 * 0: Como se muestra en el autocomplete
					 * 1: Codigo del articulo
					 * 2: Nombre comercial del articulo
					 * 3: Nombre genérico del articulo
					 * 4: Tipo protocolo
					 * 5: (M)edicamento o (L)iquido
					 * 6: Es generico
					 * 7: Origen
					 * 8: Grupo de medicamento
					 * 9: Forma farmaceutica
					 * 10:Unidad
					 * 11:POS
					 * 12:Unidad de fraccion
					 * 13:Cantidad de fracciones
					 * 14:Vencimiento
					 * 15:Dias de estabilidad
					 * 16:Es dispensable
					 * 17:Es duplicable
					 * 18:Dias maximos sugeridos
					 * 19:Dosis maximas sugeridas
					 * 20:Via
					 * 21:Abre ventana parametrizada ----
					 * 22:Cantidad de dosis
					 * 23:Unidad de dosis
					 * 24:No enviar
					 * 25:Frecuencia
					 * 26:Vias de administracion
					 * 27:Condicion de suministro
					 * 28:Confirmada preparacion
					 * 29:Dias maximos tratamiento
					 * 30:Dosis maximas tratamiento
					 * 31:Observaciones adicionales
					 * 32:Componentes del tipo
					 * 33:Nombre personalizado del articulo
					 * 34:Observaciones
					 * 35:Tipo de manejo (Interno/Externo)
					 */
					 
					var codigoArticulo = item[1];
					var nombreComercial = item[2];
					var nombreGenerico = item[3];
					var tipoProtocolo = item[4];
					var tipoMedicamentoLiquido = item[5];
					var esGenerico = item[6];
					var origen = item[7];
					var grupoMedicamento = item[8];
					// 2012-08-02
					// Se cambia para que tome el valor seleccionado en la adición del medicamento y no el de la tabla de articulos
					var formaFarmaceutica = presentacion; //item[9];
					var unidadMedida = item[10];
					var pos = item[11];
					var unidadFraccion = item[12];
					var cantidadFraccion = dosis;	//item[13];
					var vencimiento = item[14];
					var diasEstabilidad = item[15];
					var dispensable = item[16];
					var duplicable = item[17];
					var diasMaximosSugeridos = item[18];
					var dosisMaximasSugeridas = item[19];
					var viaAdministracion = administracion;
					var abreVentanaFija = item[21];
					var cantidadDosisFija = item[22];
					var unidadDosisFija = medida;		//item[23];
					var noEnviarFija = item[24];
					var frecuenciaFija = periodo;
					var viasAdministracionFija = item[26];
					var condicionSuministroFija = condicion;
					var confirmadaPreparacionFija = item[28];
					var diasMaximosFija = diasTto;
					var dosisMaximasFija = dosisMax;
					var observacionesFija = item[34]+observaciones;
					var componentesTipo = item[32];
					var nombrePersonalizadoDelArticulo = item[33];
					// var tipoManejo = item[35];
					// var unidadPosologia = '';
					// var posologia = '';
					//var oncologico = item[36];
					fechaInicioFija = fechaInicio;
					
					// if(dosis!=cantidadDosisFija)
					// {
						// if(codigoArticulo.substr(0,2)=='DA')
							// codigoArticulo = "DA0000";
						// if(codigoArticulo.substr(0,2)=='NU')
							// codigoArticulo = "NU0000";
						// if(codigoArticulo.substr(0,2)=='QT')
							// codigoArticulo = "QT0000";
						
						// // if(codigoArticulo.substr(0,2)=='DA' && dosis==1000)
							// // codigoArticulo = "DA0025";
					// }
					
					var	noEnviar = item[33];	//Abril 25 de 2011
					
					if( esGenerico.length > 0 ){
						duplicable = 'on';
					}

					agregarArticulo( "detKardexAdd", false, codigoArticulo );

					seleccionarArticulo(codigoArticulo,reemplazarTodo(nombreComercial," ","_"),reemplazarTodo(nombreGenerico," ","_"),origen,grupoMedicamento,formaFarmaceutica,unidadMedida,pos,unidadFraccion,cantidadFraccion,vencimiento,diasEstabilidad,dispensable,duplicable,diasMaximosSugeridos,dosisMaximasSugeridas,viaAdministracion,tipoProtocolo,tipoMedicamentoLiquido,esGenerico,abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija,condicionSuministroFija,confirmadaPreparacionFija,diasMaximosFija,dosisMaximasFija,observacionesFija,componentesTipo,noEnviar,tipoManejo,unidadPosologia,posologia);
				
					document.getElementById("btnCerrarVentana").style.display = 'none';

					this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
				}
			}
			
			if (!estaEnProceso(ajax)) {
				ajax.send(null);
			}
		}catch(e){	}

	}
	// Si es adición de medicamentos por protocolo
	else
	{
		adicionMultiple =  true;
		//var codigoArticulo = '';
		for (ipro=0;ipro<protocolos.length-1;ipro++)
		{
			var protocolo = protocolos[ipro].split( "|" );

			if(protocolo[6]=='Medicamentos')
			{
				articulo = protocolo[0];				// Codigo del articulo
				periodo = protocolo[1];					// Frecuencias
				administracion = protocolo[2];			// Vías de administración
				condicion = protocolo[3];				// Condiciones de suministro
				var dosis = protocolo[4];				// Dosis
				var observaciones = protocolo[5];		// Observaciones

				presentacion = '';			// Presentaciones o formas farmacéuticas
				medida = '';				// Unidades de medida
				var fechaInicio = '';		// Fecha y hora de inicio de aplicación
				var dosisMax = '';			// Dosis a aplicar
				var diasTto = '';			// Días de tratamiento
						
				parametros = "consultaAjaxKardex=36&wemp_pmla="+document.forms.forma.wemp_pmla.value
							 +"&basedatos="+document.forms.forma.wbasedato.value
							 +"&cenmez="+document.forms.forma.wcenmez.value
							 +"&ccoPaciente="+document.forms.forma.wservicio.value
							 +"&q="+articulo
							 +"&dos="+dosis
							 +"&adm="+administracion;
			
				try{
					
					ajax=nuevoAjax();
					
					ajax.open("POST", "ordenesidc.inc.php",false); 
					ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					ajax.send(parametros);
					
					if( ajax.responseText != '' ){
						if(ajax.responseText != "No se encontraron coincidencias"){

							var item = ajax.responseText.split( "|" );
							
			//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));
							
							/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
							 * Argumentos:
							 * 
							 * 0: Como se muestra en el autocomplete
							 * 1: Codigo del articulo
							 * 2: Nombre comercial del articulo
							 * 3: Nombre genérico del articulo
							 * 4: Tipo protocolo
							 * 5: (M)edicamento o (L)iquido
							 * 6: Es generico
							 * 7: Origen
							 * 8: Grupo de medicamento
							 * 9: Forma farmaceutica
							 * 10:Unidad
							 * 11:POS
							 * 12:Unidad de fraccion
							 * 13:Cantidad de fracciones
							 * 14:Vencimiento
							 * 15:Dias de estabilidad
							 * 16:Es dispensable
							 * 17:Es duplicable
							 * 18:Dias maximos sugeridos
							 * 19:Dosis maximas sugeridas
							 * 20:Via
							 * 21:Abre ventana parametrizada ----
							 * 22:Cantidad de dosis
							 * 23:Unidad de dosis
							 * 24:No enviar
							 * 25:Frecuencia
							 * 26:Vias de administracion
							 * 27:Condicion de suministro
							 * 28:Confirmada preparacion
							 * 29:Dias maximos tratamiento
							 * 30:Dosis maximas tratamiento
							 * 31:Observaciones adicionales
							 * 32:Componentes del tipo
							 * 33:Nombre personalizado del articulo
							 */
							 
							var codigoArticulo = item[1];
							var nombreComercial = item[2];
							var nombreGenerico = item[3];
							var tipoProtocolo = item[4];
							var tipoMedicamentoLiquido = item[5];
							var esGenerico = item[6];
							var origen = item[7];
							var grupoMedicamento = item[8];
							var formaFarmaceutica = $.trim(item[9]);
							var unidadMedida = item[10];
							var pos = item[11];
							var unidadFraccion = item[12];
							var cantidadFraccion = dosis;	//item[13];
							var vencimiento = item[14];
							var diasEstabilidad = item[15];
							var dispensable = item[16];
							var duplicable = item[17];
							var diasMaximosSugeridos = item[18];
							var dosisMaximasSugeridas = item[19];
							var viaAdministracion = administracion;
							var abreVentanaFija = item[21];
							var cantidadDosisFija = item[22];
							var unidadDosisFija = item[23];
							var noEnviarFija = item[24];
							var frecuenciaFija = periodo;
							var viasAdministracionFija = item[26];
							var condicionSuministroFija = condicion;
							var confirmadaPreparacionFija = item[28];
							var diasMaximosFija = diasTto;
							var dosisMaximasFija = dosisMax;
							var observacionesFija = item[34]+observaciones;
							var componentesTipo = item[32];
							var nombrePersonalizadoDelArticulo = item[33];
							var tipoManejo = item[35];
							var unidadPosologia = '';
							var posologia = '';
							fechaInicioFija = fechaInicio;

							// if(dosis!=cantidadDosisFija)
							// {
								// if(codigoArticulo.substr(0,2)=='DA')
									// codigoArticulo = "DA0000";
								// if(codigoArticulo.substr(0,2)=='NU')
									// codigoArticulo = "NU0000";
								// if(codigoArticulo.substr(0,2)=='QT')
									// codigoArticulo = "QT0000";
								
								// // if(codigoArticulo.substr(0,2)=='DA' && dosis==1000)
									// // codigoArticulo = "DA0025";
							// }
							
							var	noEnviar = item[33];	//Abril 25 de 2011
							
							if( esGenerico.length > 0 ){
								duplicable = 'on';
							}
						
							agregarArticulo( "detKardexAdd", false, codigoArticulo );
							
							seleccionarArticulo(codigoArticulo,reemplazarTodo(nombreComercial," ","_"),reemplazarTodo(nombreGenerico," ","_"),origen,grupoMedicamento,formaFarmaceutica,unidadMedida,pos,unidadFraccion,cantidadFraccion,vencimiento,diasEstabilidad,dispensable,duplicable,diasMaximosSugeridos,dosisMaximasSugeridas,viaAdministracion,tipoProtocolo,tipoMedicamentoLiquido,esGenerico,abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija,condicionSuministroFija,confirmadaPreparacionFija,diasMaximosFija,dosisMaximasFija,observacionesFija,componentesTipo,noEnviar,tipoManejo,unidadPosologia,posologia);
						
							document.getElementById("btnCerrarVentana").style.display = 'none';

							this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
						}
					}
					
					if (!estaEnProceso(ajax)) {
						ajax.send(null);
					}
				}catch(e){	}
		
			}
			else if(protocolo[6]=='Procedimientos')
			{
			
				var aydCodigo = protocolo[0];				// Codigo ayuda diagnostica
				var aydJustificacion = protocolo[7];		// Justificacion ayuda diagnostica
				parametros = "consultaAjaxKardex=38&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&ayd_cod="+aydCodigo+"&especialidad="+document.forms.forma.wespecialidad.value;

				try {
					ajax=nuevoAjax();
					ajax.open("POST", "ordenesidc.inc.php",false); 
					ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					ajax.send(parametros);

					if( ajax.responseText != '' )
					{
						//Agosto 18 de 2016. Se agrega el trim a la respuesta ajax.
						var aydItem = $.trim( ajax.responseText ).split( "|" );
					}
				}catch(e){	}

				seleccionarAyudaDiagnostica(aydItem[1],aydItem[2],aydItem[3],aydItem[4],aydItem[5],aydItem[6],aydItem[7],aydItem[8],aydItem[9],aydItem[10],aydItem[11],aydItem[12],aydJustificacion);

				document.getElementById("btnCerrarVentana").style.display = 'none';

				this.onfocus = function(){
					if( justificacionUltimoExamenAgregado ){
						justificacionUltimoExamenAgregado.focus();
						justificacionUltimoExamenAgregado = '';
					}
				};

			}
		
		}
	}

	//Una vez agregado el medicamento borro los datos de la consulta
	var tbNuevoBuscador = document.getElementById( "nuevoBuscador" );
	
	document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
	document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo
	
	/* 2012-11-02
	// Se comenta porque se va a obtener el elemento por el nombre 
	// ya que la nueva estructura no permite obtenerlo por posición en la tabla
	tbNuevoBuscador.rows[1].cells[0].firstChild.value = '';				//Borro el valor de Nombre articulo
	tbNuevoBuscador.rows[1].cells[1].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la presentacion
	tbNuevoBuscador.rows[1].cells[2].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las unidades
	tbNuevoBuscador.rows[1].cells[3].firstChild.value = '';				//Quito el valor escrito de la dosis
	tbNuevoBuscador.rows[1].cells[4].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la frecuencia
	tbNuevoBuscador.rows[1].cells[5].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las via de administracion
	tbNuevoBuscador.rows[1].cells[7].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la condicion
	tbNuevoBuscador.rows[1].cells[8].firstChild.value = '';				//Quito el valor escrito de los dias de tratmiento
	tbNuevoBuscador.rows[1].cells[9].firstChild.value = '';				//Quito el valor escrito de la dosis maxima
	tbNuevoBuscador.rows[1].cells[10].firstChild.value = '';			//Quito el valor escrito de las observaciones
	*/

	//Quito los valores de los campos de busqueda y adición de medicamentos
	document.getElementById('wnombrefamilia').value = '';			
	document.getElementById('wpresentacion').selectedIndex = -1;			
	document.getElementById('wunidad').selectedIndex = -1;			
	document.getElementById('wdosisfamilia').value = '';			
	document.getElementById('wfrecuencia').selectedIndex = -1;			
	document.getElementById('wadministracion').selectedIndex = -1;			
	document.getElementById('wcondicionsum').selectedIndex = -1;			
	document.getElementById('wdiastratamiento').value = '';			
	document.getElementById('wdosismaxima').value = '';			
	document.getElementById('wtxtobservasiones').value = '';			
	document.getElementById('wposologia').value = '';			
	document.getElementById('wunidadposologia').value = '';			
	
	document.getElementById('wprotocolo').value = '';

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

	if(strPendientesCTC!="")
			abrirCTCMultiple();

	if(strPendientesModal!="")
		abrirModalMultiple();
			
	adicionMultiple = false;

	// 2013-01-21
	//tbNuevoBuscador.rows[1].cells[10].firstChild.onblur();
}


function eleccionMedicamentoAlta()
{
	
	var articulo;
	var periodo;
	var administracion;
	var condicion;
	var presentacion;
	var medida;

	var parametros = "";

	// Se reviza si hay algun detalle de familia desplegado y se oculta
	// ocultarDetalleFliaAnterior();

	//////////////////////////////////////////////////////////////////////
	//////// ASIGNACIÓN DE VARIABLES PARA LOS DATOS INGRESADOS ///////////

	// Asigno variables dadas en el formulario de ingreso
	var nombreFamilia = document.getElementById( "wnombrefamiliaimp" ).value;		// Familia de medicamentos
	var presentaciones = document.getElementById( "wpresentacionimp" );			// Presentaciones o formas farmacéuticas
	var unidades = document.getElementById( "wunidadimp" );						// Unidades de medida
	var dosis = document.getElementById( "wdosisfamiliaimp" ).value;				// Dosis a aplicar
	var frecuencias = document.getElementById( "wfrecuenciaimp" );					// Frecuencias
	var viasAdministracion = document.getElementById( "wadministracionimp" );		// Vías de administración
	var fechaInicio = document.getElementById( "wfinicioaplicacionimp" ).value;	// Fecha y hora de inicio de aplicación
	var condiciones = document.getElementById( "wcondicionsumimp" );				// Condiciones de suministro
	var diasTto = document.getElementById( "wdiastratamientoimp" ).value;			// Días de tratamiento
	var dosisMax = document.getElementById( "wdosismaximaimp" ).value;				// Dosis máxima
	var cantidadAlta = document.getElementById( "wcantidadaltaimp" ).value;				// Cantidad ordenada para el alta
	var posologia = document.getElementById( "wposologiaimp" ).value;				// Posologia
	var unidadPosologia = document.getElementById( "wunidadposologiaimp" ).value;	// Unidad posologia
	
	var observaciones = document.getElementById( "wtxtobservasionesimp" ).value;	// Observaciones
	
	// Obtengo los indices seleccionados en los campos desplegables (select's)
	var selPresentacion = presentaciones.selectedIndex;
	var selUnidad = unidades.selectedIndex;
	var selFrecuencia = frecuencias.selectedIndex;
	// var selViaAdministracion = viasAdministracion.selectedIndex;
	// var selCondicion = condiciones.selectedIndex;

	// Se asigna el valor a la variable para la presentación o forma farmacéutica
	if(selPresentacion!=-1)
		presentacion = document.getElementById('wpresentacionimp').options[selPresentacion].value;
	else
		presentacion = "";
		
	// Se asigna el valor a la variable para la unidad de medida
	if(selUnidad!=-1)
		medida = document.getElementById('wunidadimp').options[document.getElementById( 'wunidadimp' ).selectedIndex].value;
	else
		medida = "";

	// Se asigna el valor a la variable para la unidad de medida
	if(selFrecuencia!=-1)
		periodo = document.getElementById('wfrecuenciaimp').options[selFrecuencia].value;
	else
		periodo = "";

		
	// Se asigna el valor a la variable para la vía de administración
	// if(selViaAdministracion!=-1)
		// administracion = document.getElementById('wadministracionimp').options[selViaAdministracion].value;
	// else
		// administracion = "";

	administracion = document.getElementById( "wadministracionimp" ).value;		// Vías de administración
		
		
	// // Se asigna el valor a la variable para la condición de suministro
	// if(selCondicion!=-1)
		// condicion = document.getElementById('wcondicionsumimp').options[selCondicion].value;
	// else
		// condicion = "";

	condicion = document.getElementById( "wcondicionsumimp" ).value;				// Condiciones de suministro
		
		
	// Se usarán si el medicamento es líquido endovenoso
	periodoLQ = periodo;
	administracionLQ = administracion;
	fechaInicioLQ = fechaInicio;
	condicionLQ = condicion;
	diasTtoLQ = diasTto;
	dosisMaxLQ = dosisMax;
	cantidadAltaLQ = cantidadAlta;
	observacionesLQ = observaciones;
	unidadPosologiaLQ = unidadPosologia;
	posologiaLQ = posologia;

	
	////// FIN ASIGNACIÓN DE VARIABLES PARA LOS DATOS INGRESADOS /////////
	//////////////////////////////////////////////////////////////////////		
	
	
	// arRelacionesUnidad = arRelUnidad;
	// arArticulosSeleccionados = arArticulos;

	//////////////////////////////////////////////////////////////////////
	///////// VALIDACIÓN DE LOS DATOS INGRESADOS /////////////////////////

	// Si no se ha ingresado el nombre de la familia o artículo, muestre el mensaje correspondiente
	if( document.getElementById( "wnombrefamiliaimp" ).value == "" || document.getElementById( "wnombrefamiliaimp" ).value == " " )
	{
		alert("Debe ingresar el nombre del artículo");
		document.getElementById( "wnombrefamiliaimp" ).focus();
		return; 
	}

	// Validación presentación o forma farmacéutica
	if(selPresentacion>-1)
	{
		var presentacionMed = presentaciones.options[selPresentacion].text;
		if(presentacionMed=="" || presentacionMed==" ")
		{
			alert("Debe seleccionar una presentación");
			document.getElementById( "wpresentacionimp" ).focus();
			document.getElementById( "wpresentacionimp" ).select();
			return false;
		}	
	}
	else
	{
		alert("Debe seleccionar una presentación");
		document.getElementById( "wpresentacionimp" ).focus();
		document.getElementById( "wpresentacionimp" ).select();
		return false;
	}

	// Validación unidad de medida
	if(selUnidad>-1)
	{
		var unidMedida = unidades.options[selUnidad].text;
		if(unidMedida=="" || unidMedida==" ")
		{
			alert("Debe seleccionar una unidad de medida");
			document.getElementById( "wunidadimp" ).focus();
			document.getElementById( "wunidadimp" ).select();
			return false;
		}	
	}
	else
	{
		alert("Debe seleccionar una unidad de medida");
		document.getElementById( "wunidadimp" ).focus();
		document.getElementById( "wunidadimp" ).select();
		return false;
	}
	
	//Si la dosis es vacio no agregar
	if( dosisMax == "" ){
		alert("Debe ingresar la cantidad");
		return; 
	}

	//Si la dosis es vacio no agregar
	if( dosis == "" && periodo != "H.E." && !esLiquidoEndovenoso ){
		alert("Debe ingresar la dosis");
		return; 
	}

	// Validación de frecuencia
	if(selFrecuencia>-1)
	{
		var frecuencia = frecuencias.options[selFrecuencia].text;
		if(frecuencia=="" || frecuencia==" ")
		{
			alert("Debe seleccionar la frecuencia");
			document.getElementById( "wfrecuenciaimp" ).focus();
			document.getElementById( "wfrecuenciaimp" ).select();
			return false;
		}	
	}
	else
	{
		alert("Debe seleccionar la frecuencia");
		document.getElementById( "wfrecuenciaimp" ).focus();
		document.getElementById( "wfrecuenciaimp" ).select();
		return false;
	}

	// Validación de la vía de administración
	/*
	if(selViaAdministracion>-1)
	{
		var viaAdministracion = viasAdministracion.options[selViaAdministracion].text;
		if(viaAdministracion=="" || viaAdministracion==" ")
		{
			alert("Debe seleccionar una vía de administración");
			document.getElementById( "wadministracionimp" ).focus();
			document.getElementById( "wadministracionimp" ).select();
			return false;
		}	
	}
	else
	{
		alert("Debe seleccionar una vía de administración");
		document.getElementById( "wadministracionimp" ).focus();
		document.getElementById( "wadministracionimp" ).select();
		return false;
	}
	*/

	///////// FIN VALIDACIÓN DE LOS DATOS INGRESADOS /////////////////////
	//////////////////////////////////////////////////////////////////////

	//Se declaran las variables globales
	//arArticulosSeleccionados;
	arRelacionesUnidad;
	arRelacionesPresentacion;
	//arRelacionesViaAdmon;
	
	// 2012-12-10 
	// Se comenta porque estas variables no tienen uso
	// var sel = arRelacionesUnidad[ selUnidad ];		// Unidad de medida
	// var sel2 = arRelacionesPresentacion[ selPresentacion ];	// Presentacion
	// var sel4 = arRelacionesViaAdmon[ selViaAdministracion ];	// Via administracion
	
	// 2012-12-14
	// alert("eleccionMedicamento: arRelacionesUnidad="+arRelacionesUnidad.toSource());
	// alert("eleccionMedicamento: selUnidad="+selUnidad);

	// alert("arRelacionesUnidad. "+arRelacionesUnidad.toSource());
	// alert("arArticulosSeleccionados: "+arArticulosSeleccionados.toSource());
	//alert("selUnidad: "+selUnidad);

	var art = "" ;
	
	// 2012-12-14
	/*
	// Se comenta porque ya esta validación se va a hacer en PHP por medio de AJAX
	for( var i = 0; i < arRelacionesUnidad[ selUnidad ].length; i++ )
	{
		
		var pos = arRelacionesUnidad[ selUnidad ][i]-1;
		// alert("selUnidad: "+selUnidad+" - i: "+i+" - pos: "+pos);
		if( arArticulosSeleccionados[ pos ][1]*1 == dosis*1 ){
			art = arArticulosSeleccionados[ pos ][0];
			break;
		}
		else if( arArticulosSeleccionados[ pos ][1]*1 > dosis*1 ){
			art = arArticulosSeleccionados[ pos ][0];
			break;
		}
		else{
			art = arArticulosSeleccionados[ pos ][0];
		}
		
		// 2012-12-14
		alert("Art. "+art);
	}
	*/


	document.getElementById( "trEncabezadoTbAddImp" ).style.display = '';

	//buscando el medicamento
	var mensaje = "";


	parametros = "consultaAjaxKardex=35&wemp_pmla="+document.forms.forma.wemp_pmla.value
				 +"&basedatos="+document.forms.forma.wbasedato.value
				 +"&cenmez="+document.forms.forma.wcenmez.value
				 +"&ccoPaciente="+document.forms.forma.wservicio.value
				 +"&q="+encodeURIComponent( nombreFamilia )
				 +"&pre="+presentacion
				 +"&med="+medida
				 +"&dos="+dosis
				 +"&adm="+administracion;

	try{
		
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",false); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		if( ajax.responseText != '' ){
			if(ajax.responseText != "No se encontraron coincidencias"){

				var item = ajax.responseText.split( "|" );
				
//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));
				
				/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
				 * Argumentos:
				 * 
				 * 0: Como se muestra en el autocomplete
				 * 1: Codigo del articulo
				 * 2: Nombre comercial del articulo
				 * 3: Nombre genérico del articulo
				 * 4: Tipo protocolo
				 * 5: (M)edicamento o (L)iquido
				 * 6: Es generico
				 * 7: Origen
				 * 8: Grupo de medicamento
				 * 9: Forma farmaceutica
				 * 10:Unidad
				 * 11:POS
				 * 12:Unidad de fraccion
				 * 13:Cantidad de fracciones
				 * 14:Vencimiento
				 * 15:Dias de estabilidad
				 * 16:Es dispensable
				 * 17:Es duplicable
				 * 18:Dias maximos sugeridos
				 * 19:Dosis maximas sugeridas
				 * 20:Via
				 * 21:Abre ventana parametrizada ----
				 * 22:Cantidad de dosis
				 * 23:Unidad de dosis
				 * 24:No enviar
				 * 25:Frecuencia
				 * 26:Vias de administracion
				 * 27:Condicion de suministro
				 * 28:Confirmada preparacion
				 * 29:Dias maximos tratamiento
				 * 30:Dosis maximas tratamiento
				 * 31:Observaciones adicionales
				 * 32:Componentes del tipo
				 * 33:Nombre personalizado del articulo
				 */
				 
				var codigoArticulo = item[1];
				var nombreComercial = item[2];
				var nombreGenerico = item[3];
				var tipoProtocolo = item[4];
				var tipoMedicamentoLiquido = item[5];
				var esGenerico = item[6];
				var origen = item[7];
				var grupoMedicamento = item[8];
				// 2012-08-02
				// Se cambia para que tome el valor seleccionado en la adición del medicamento y no el de la tabla de articulos
				var formaFarmaceutica = presentacion; //item[9];
				var unidadMedida = item[10];
				var pos = item[11];
				var unidadFraccion = item[12];
				var cantidadFraccion = dosis;	//item[13];
				var vencimiento = item[14];
				var diasEstabilidad = item[15];
				var dispensable = item[16];
				var duplicable = item[17];
				var diasMaximosSugeridos = item[18];
				var dosisMaximasSugeridas = item[19];
				var viaAdministracion = administracion;
				var abreVentanaFija = item[21];
				var cantidadDosisFija = item[22];
				var unidadDosisFija = medida;		//item[23];
				var noEnviarFija = item[24];
				var frecuenciaFija = periodo;
				var viasAdministracionFija = item[26];
				var condicionSuministroFija = condicion;
				var confirmadaPreparacionFija = item[28];
				var diasMaximosFija = diasTto;
				var dosisMaximasFija = dosisMax;
				var cantidadAltaFija = cantidadAlta;
				var observacionesFija = item[34]+observaciones;
				var componentesTipo = item[32];
				var nombrePersonalizadoDelArticulo = item[33];
				var tipoManejo = item[35];
				var unidadPosologia = '';
				var posologia = '';
				fechaInicioFija = fechaInicio;
				
				// if(dosis!=cantidadDosisFija)
				// {
					// if(codigoArticulo.substr(0,2)=='DA')
						// codigoArticulo = "DA0000";
					// if(codigoArticulo.substr(0,2)=='NU')
						// codigoArticulo = "NU0000";
					// if(codigoArticulo.substr(0,2)=='QT')
						// codigoArticulo = "QT0000";
					
					// // if(codigoArticulo.substr(0,2)=='DA' && dosis==1000)
						// // codigoArticulo = "DA0025";
				// }
				
				var	noEnviar = item[33];	//Abril 25 de 2011
				
				if( esGenerico.length > 0 ){
					duplicable = 'on';
				}
				
				var deAlta = "on";
				
				agregarArticulo( "detKardexAddImp", true, codigoArticulo );
				
				seleccionarArticulo(codigoArticulo,reemplazarTodo(nombreComercial," ","_"),reemplazarTodo(nombreGenerico," ","_"),origen,grupoMedicamento,formaFarmaceutica,unidadMedida,pos,unidadFraccion,cantidadFraccion,vencimiento,diasEstabilidad,dispensable,duplicable,diasMaximosSugeridos,dosisMaximasSugeridas,viaAdministracion,tipoProtocolo,tipoMedicamentoLiquido,esGenerico,abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija,condicionSuministroFija,confirmadaPreparacionFija,diasMaximosFija,cantidadAltaFija,observacionesFija,componentesTipo,noEnviar,tipoManejo,unidadPosologia,posologia,deAlta);
				
				document.getElementById("btnCerrarVentana").style.display = 'none';

				this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
			}
		}
		
		if (!estaEnProceso(ajax)) {
			ajax.send(null);
		}
	}catch(e){	}


	//Una vez agregado el medicamento borro los datos de la consulta
	var tbNuevoBuscador = document.getElementById( "nuevoBuscadorImp" );
	
	// document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
	// document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo
	
	/* 2012-11-02
	// Se comenta porque se va a obtener el elemento por el nombre 
	// ya que la nueva estructura no permite obtenerlo por posición en la tabla
	tbNuevoBuscador.rows[1].cells[0].firstChild.value = '';				//Borro el valor de Nombre articulo
	tbNuevoBuscador.rows[1].cells[1].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la presentacion
	tbNuevoBuscador.rows[1].cells[2].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las unidades
	tbNuevoBuscador.rows[1].cells[3].firstChild.value = '';				//Quito el valor escrito de la dosis
	tbNuevoBuscador.rows[1].cells[4].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la frecuencia
	tbNuevoBuscador.rows[1].cells[5].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las via de administracion
	tbNuevoBuscador.rows[1].cells[7].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la condicion
	tbNuevoBuscador.rows[1].cells[8].firstChild.value = '';				//Quito el valor escrito de los dias de tratmiento
	tbNuevoBuscador.rows[1].cells[9].firstChild.value = '';				//Quito el valor escrito de la dosis maxima
	tbNuevoBuscador.rows[1].cells[10].firstChild.value = '';			//Quito el valor escrito de las observaciones
	*/

	//Quito los valores de los campos de busqueda y adición de medicamentos
	document.getElementById('wnombrefamiliaimp').value = '';			
	document.getElementById('wpresentacionimp').selectedIndex = -1;			
	document.getElementById('wunidadimp').selectedIndex = -1;			
	document.getElementById('wdosisfamiliaimp').value = '';			
	document.getElementById('wfrecuenciaimp').selectedIndex = -1;			
	// document.getElementById('wadministracionimp').selectedIndex = -1;			
	document.getElementById('wadministracionimp').value = '';			
	// document.getElementById('wcondicionsumimp').selectedIndex = -1;			
	document.getElementById('wcondicionsumimp').value = '';			
	document.getElementById('wdiastratamientoimp').value = '';			
	document.getElementById('wdosismaximaimp').value = '';			
	document.getElementById('wcantidadaltaimp').value = '';			
	document.getElementById('wtxtobservasionesimp').value = '';			
	document.getElementById('wposologiaimp').value = '';			
	document.getElementById('wunidadposologiaimp').value = '';			

	//document.getElementById('wprotocoloimp').value = '';

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

	if(strPendientesCTC!="")
			abrirCTCMultiple();
	
	if(strPendientesModal!="")
		abrirModalMultiple();
	
	adicionMultiple = false;

	// 2013-01-21
	//tbNuevoBuscador.rows[1].cells[10].firstChild.onblur();
}





function eleccionMedicamentosInsumos(strInsumos)
{
	// Se reviza si hay algun detalle de familia desplegado y se oculta
	ocultarDetalleFliaAnterior();
	
	var articulo;
	var periodo;
	var administracion;
	var condicion;
	var dosis;
	var observaciones;
	var presentacion;
	var medida;
	var fechaInicio;
	var dosisMax;
	var diasTto;
	var insumoDispensable;

	
	document.getElementById( "trEncabezadoTbAdd" ).style.display = '';

	//buscando el medicamento
	var parametros = "";
	var mensaje = "";

	var filasInsumo = strInsumos.split( "\r\n" );

	//var codigoArticulo = '';
	for (ipro=0;ipro<filasInsumo.length-1;ipro++)
	{
		var arrInsumo = filasInsumo[ipro].split( " - " );

		articulo = arrInsumo[0];				// Dosis a aplicar
		periodo = periodoLQ;				// Frecuencias
		administracion = administracionLQ;		// Vías de administración
		condicion = condicionLQ;				// Condiciones de suministro
		dosis = arrInsumo[1];				// Dosis
		observaciones = observacionesLQ;			// Observaciones
		insumoDispensable = arrInsumo[2];
		presentacion = '';			// Presentaciones o formas farmacéuticas
		medida = '';						// Unidades de medida
		fechaInicio = fechaInicioLQ;	// Fecha y hora de inicio de aplicación
		dosisMax = dosisMaxLQ;				// Dosis a aplicar
		diasTto = diasTtoLQ;			// Días de tratamiento
		unidadPosologia = unidadPosologiaLQ;				// Dosis a aplicar
		posologia = posologiaLQ;				// Dosis a aplicar
		
		
		
		parametros = "consultaAjaxKardex=36&wemp_pmla="+document.forms.forma.wemp_pmla.value
					 +"&basedatos="+document.forms.forma.wbasedato.value
					 +"&cenmez="+document.forms.forma.wcenmez.value
					 +"&ccoPaciente="+document.forms.forma.wservicio.value
					 +"&q="+articulo
					 +"&dos="+dosis
					 +"&adm="+administracion;
	
	
		try{
			
			ajax=nuevoAjax();
			
			ajax.open("POST", "ordenesidc.inc.php",false); 
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);
			
			if( ajax.responseText != '' ){
				if(ajax.responseText != "No se encontraron coincidencias"){

					var item = ajax.responseText.split( "|" );
	//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));
					
					/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
					 * Argumentos:
					 * 
					 * 0: Como se muestra en el autocomplete
					 * 1: Codigo del articulo
					 * 2: Nombre comercial del articulo
					 * 3: Nombre genérico del articulo
					 * 4: Tipo protocolo
					 * 5: (M)edicamento o (L)iquido
					 * 6: Es generico
					 * 7: Origen
					 * 8: Grupo de medicamento
					 * 9: Forma farmaceutica
					 * 10:Unidad
					 * 11:POS
					 * 12:Unidad de fraccion
					 * 13:Cantidad de fracciones
					 * 14:Vencimiento
					 * 15:Dias de estabilidad
					 * 16:Es dispensable
					 * 17:Es duplicable
					 * 18:Dias maximos sugeridos
					 * 19:Dosis maximas sugeridas
					 * 20:Via
					 * 21:Abre ventana parametrizada ----
					 * 22:Cantidad de dosis
					 * 23:Unidad de dosis
					 * 24:No enviar
					 * 25:Frecuencia
					 * 26:Vias de administracion
					 * 27:Condicion de suministro
					 * 28:Confirmada preparacion
					 * 29:Dias maximos tratamiento
					 * 30:Dosis maximas tratamiento
					 * 31:Observaciones adicionales
					 * 32:Componentes del tipo
					 * 33:Nombre personalizado del articulo
					 */

					// Si el insumo esta marcado como no dispensable se marca como no enviar
					if(trim(insumoDispensable)=="" || trim(insumoDispensable)=="off")
						item[24] = "on";
					 
					var codigoArticulo = item[1];
					var nombreComercial = item[2];
					var nombreGenerico = item[3];
					var tipoProtocolo = item[4];
					var tipoMedicamentoLiquido = 'M';
					var esGenerico = item[6];
					var origen = item[7];
					var grupoMedicamento = item[8];
					var formaFarmaceutica = $.trim(item[9]);
					var unidadMedida = item[10];
					var pos = item[11];
					var unidadFraccion = item[12];
					var cantidadFraccion = dosis;	//item[13];
					var vencimiento = item[14];
					var diasEstabilidad = item[15];
					var dispensable = item[16];
					var duplicable = item[17];
					var diasMaximosSugeridos = item[18];
					var dosisMaximasSugeridas = item[19];
					var viaAdministracion = administracion;
					var abreVentanaFija = 'N';
					var cantidadDosisFija = item[22];
					var unidadDosisFija = item[23];
					var noEnviarFija = item[24];
					var frecuenciaFija = periodo;
					var viasAdministracionFija = item[26];
					var condicionSuministroFija = condicion;
					var confirmadaPreparacionFija = item[28];
					var diasMaximosFija = diasTto;
					var dosisMaximasFija = dosisMax;
					var observacionesFija = item[34]+observaciones;
					var componentesTipo = item[32];
					var nombrePersonalizadoDelArticulo = item[33];
					var tipoManejo = item[35];
					var unidadPosologia = '';
					var posologia = '';
					fechaInicioFija = fechaInicio;

					// if(dosis!=cantidadDosisFija)
					// {
						// if(codigoArticulo.substr(0,2)=='DA')
							// codigoArticulo = "DA0000";
						// if(codigoArticulo.substr(0,2)=='NU')
							// codigoArticulo = "NU0000";
						// if(codigoArticulo.substr(0,2)=='QT')
							// codigoArticulo = "QT0000";
						
						// // if(codigoArticulo.substr(0,2)=='DA' && dosis==1000)
							// // codigoArticulo = "DA0025";
					// }
					
					var	noEnviar = item[33];	//Abril 25 de 2011
					
					if( esGenerico.length > 0 ){
						duplicable = 'on';
					}
				
					agregarArticulo( "detKardexAdd", false, codigoArticulo );
					
					seleccionarArticulo(codigoArticulo,reemplazarTodo(nombreComercial," ","_"),reemplazarTodo(nombreGenerico," ","_"),origen,grupoMedicamento,formaFarmaceutica,unidadMedida,pos,unidadFraccion,cantidadFraccion,vencimiento,diasEstabilidad,dispensable,duplicable,diasMaximosSugeridos,dosisMaximasSugeridas,viaAdministracion,tipoProtocolo,tipoMedicamentoLiquido,esGenerico,abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija,condicionSuministroFija,confirmadaPreparacionFija,diasMaximosFija,dosisMaximasFija,observacionesFija,componentesTipo,noEnviar,tipoManejo,unidadPosologia,posologia);
					
					document.getElementById("btnCerrarVentana").style.display = 'none';

					this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
				}
			}
			
			if (!estaEnProceso(ajax)) {
				ajax.send(null);
			}
		}catch(e){	}
		
	}

	//Una vez agregado el medicamento borro los datos de la consulta
	var tbNuevoBuscador = document.getElementById( "nuevoBuscador" );
	
	document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
	document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo
	
	/* 2012-11-02
	// Se comenta porque se va a obtener el elemento por el nombre 
	// ya que la nueva estructura no permite obtenerlo por posición en la tabla
	tbNuevoBuscador.rows[1].cells[0].firstChild.value = '';				//Borro el valor de Nombre articulo
	tbNuevoBuscador.rows[1].cells[1].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la presentacion
	tbNuevoBuscador.rows[1].cells[2].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las unidades
	tbNuevoBuscador.rows[1].cells[3].firstChild.value = '';				//Quito el valor escrito de la dosis
	tbNuevoBuscador.rows[1].cells[4].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la frecuencia
	tbNuevoBuscador.rows[1].cells[5].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las via de administracion
	tbNuevoBuscador.rows[1].cells[7].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la condicion
	tbNuevoBuscador.rows[1].cells[8].firstChild.value = '';				//Quito el valor escrito de los dias de tratmiento
	tbNuevoBuscador.rows[1].cells[9].firstChild.value = '';				//Quito el valor escrito de la dosis maxima
	tbNuevoBuscador.rows[1].cells[10].firstChild.value = '';			//Quito el valor escrito de las observaciones
	*/

	//Quito los valores de los campos de busqueda y adición de medicamentos
	document.getElementById('wnombrefamilia').value = '';			
	document.getElementById('wpresentacion').selectedIndex = -1;			
	document.getElementById('wunidad').selectedIndex = -1;			
	document.getElementById('wdosisfamilia').value = '';			
	document.getElementById('wfrecuencia').selectedIndex = -1;			
	document.getElementById('wadministracion').selectedIndex = -1;			
	document.getElementById('wcondicionsum').selectedIndex = -1;			
	document.getElementById('wdiastratamiento').value = '';			
	document.getElementById('wdosismaxima').value = '';			
	document.getElementById('wtxtobservasiones').value = '';			
	document.getElementById('wposologia').value = '';			
	document.getElementById('wunidadposologia').value = '';			

	document.getElementById('wprotocolo').value = '';

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

	if(strPendientesCTC!="")
			abrirCTCMultiple();
	
	if(strPendientesModal!="")
		abrirModalMultiple();
				
	adicionMultiple = false;

	// 2013-01-21
	//tbNuevoBuscador.rows[1].cells[10].firstChild.onblur();
}



// Variables globales que ayudará a llevar la sección anterior desplegada
var filaIdAnt = "";
var divIdAnt = "";
var filaTituloIdAnt = "";
var claseFilaAnt = "";

// 2012-11-08
/*************************************************************************
* Función que permite desplegar el detalle de la familia de medicamentos *
**************************************************************************/
function mostrarDetalleFlia( campo, filaId, divId, filaTituloId, claseFila )
{
	if (document.getElementById)
	{ 
		//se obtiene el id de la fila que contiene el detalle de la familia de medicamentos
		var el = document.getElementById(filaId); 
		
		if(el.style.display == 'none')
		{
			
			if(filaIdAnt != "" && document.getElementById(filaIdAnt).display != 'none')
			{
				// Se oculta la fila desplegada anteriormente
				document.getElementById(filaIdAnt).style.display = 'none'; 

				// Se reestablece la clase original de la fila anterior
				document.getElementById(filaTituloIdAnt).className = claseFilaAnt;
			}

			if(divIdAnt != "" && document.getElementById(divIdAnt).display != 'none')
			{
				// Se oculta el div desplegado anteriormente
				document.getElementById(divIdAnt).style.display = 'none';
			}
			
			// Se le asigna una clase especial a la fila del titulo de la familia de medicamentos
			// Esto para que cambie de color cuando el detalle de la familia está desplegado
			document.getElementById(filaTituloId).className = 'tituloFamilia';
			
			// Despliega el detalle de la familia de medicamentos
			el.style.display = '';

			// Se obtiene el <div> que muestra el detalle de la familia de medicamentos
			var divTitle = document.getElementById(divId);
			
			// Despliega el <div> que muestra el detalle de la familia de medicamentos
			divTitle.style.display = '';
			divTitle.style.position = 'absolute';
			
			// Espacio entre el encabezado y el detalle de la familia cuando se despliega
			divTitle.style.top = parseInt( findPosY(campo) )+ parseInt( campo.offsetHeight ) + 4;
			divTitle.style.left = 0; //findPosX( campo );
			
			// Define el alto de la fila que contiene el detalle de la familia de medicamentos
			divTitle.parentNode.style.height = divTitle.offsetHeight;	

			// Defino valores de la ultima fila desplegada para cuando se despliegue 
			// una nueva poder ocultar esta que pasa a ser la anterior
			filaIdAnt = filaId;
			divIdAnt = divId;
			claseFilaAnt = claseFila;
			filaTituloIdAnt = filaTituloId;
		}
		else
		{
			// Se reestablece la clase original de la fila del titulo de la familia de medicamentos
			document.getElementById(filaTituloId).className = claseFila;
			
			// Se oculta el div del detalle de la familia de medicamentos
			el.style.display = 'none'; 

			// Se oculta el div que muestra el detalle de la familia de medicamentos
			var divTitle = document.getElementById(divId);
			divTitle.style.display = 'none';
			
		}
	}
}

// 2012-11-16
/*************************************************************
* Función que oculta el último detalle de familia desplegado *
**************************************************************/
function ocultarDetalleFliaAnterior( )
{
	if(filaIdAnt != "" && document.getElementById(filaIdAnt).display != 'none')
	{
		// Se oculta la fila desplegada anteriormente
		document.getElementById(filaIdAnt).style.display = 'none'; 

		// Se reestablece la clase original de la fila anterior
		document.getElementById(filaTituloIdAnt).className = claseFilaAnt;
	}

	if(divIdAnt != "" && document.getElementById(divIdAnt).display != 'none')
	{
		// Se oculta el div desplegado anteriormente
		document.getElementById(divIdAnt).style.display = 'none';
	}
}

/************************************************************
 * Crea options nuevos para una selecion
 * 
 * @param slCampos	Como tipo select
 * @param opciones	Array
 * @return
 ************************************************************/	
function creandoOptions( slCampos, opciones ){

	//options debe ser un array
	if( slCampos.tagName.toLowerCase() == "select" ){

		//Borrando los options anteriores
		var numOptions = slCampos.options.length;
		
		for( var i = 0; i <  numOptions; i++ ){
			slCampos.removeChild( slCampos.options[0] );
		}

		//agrengando options
		for( var i = 0; i < opciones.length; i++ ){
			var auxOpt = document.createElement( "option" );
			slCampos.options.add( auxOpt, 0 );
			auxOpt.innerHTML = opciones[i];

			slCampos.options.selectedIndex = 0;
		}
	}
}

/************************************************************
 * Crea options nuevos para una selecion
 * Se diferencia de la anterior en que aca se establece 
 * un valor para el parametro value
 * 
 * @param slCampos	Como tipo select
 * @param opciones	Array
 * @return
 ************************************************************/	
function creandoOptionsValue( slCampos, opciones ){

	//options debe ser un array
	if( slCampos.tagName.toLowerCase() == "select" ){

		//Borrando los options anteriores
		var numOptions = slCampos.options.length;
		
		for( var i = 0; i <  numOptions; i++ ){
			slCampos.removeChild( slCampos.options[0] );
		}

		//agrengando options
		for( var i = 0; i < opciones.length; i++ ){
			var auxOpt = document.createElement( "option" );
			var valorOpt = opciones[i].split("-");
			auxOpt.value = valorOpt[0];
			slCampos.options.add( auxOpt, 0 );
			auxOpt.innerHTML = valorOpt[1];
			//alert(opciones[i]);

			slCampos.options.selectedIndex = 0;
		}
	}
}


/************************************************************************************************************************
 * Autocompletar para la busqueda de medicamentos por familias
 * @return
 ************************************************************************************************************************/
function autocompletarParaBusqueMedicamentosPorFamilia(){
	// Se limpia cache del campo
	$("#wnombrefamilia").flushCache();
	$("#wnombrefamilia").unbind("result");

	// Se hace el llamado AJAX para traer los resultados a la función autocomplete
	$("#wnombrefamilia").setOptions({url:"ordenesidc.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value });
	
	$("#wnombrefamilia").autocomplete("ordenesidc.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value, {
		cacheLength:0,
		delay:0,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		minChars: 2,
		autoFill:false,
		formatResult: function(data, value){
			return value;
		}
	}).result(function(event, item) { 
    	
		//creando array de resultados
		var arDosis = new Array();
		var arUnidades = new Array();
		var arPresentaciones = new Array();
		var arViasAdmon = new Array();
		var arArticulos = new Array();
		
		// Indices para los diferentes arrays
		var j = -1;
		var k = 0;
		var l = -1;
		var m = 0;
		var n = -1;
		var p = 0;
		var q = -1;
		var r = 0;
		
		// Arrays de relación con los articulos
		var arRelDosis = new Array(); 
		var arRelUnidad = new Array(); 
		var arRelPresentacion = new Array(); 
		var arRelViaAdmon = new Array(); 

		esLiquidoEndovenoso = false;
		var valorOncologico = "on";
		var valorInterno = "on";
		
		for( var i = 0; i < item.length; i++ ){

			// Si viene precedido de @ es unidad y se asigna al array arUnidades
			if( item[i].substr(0,1) == "@" ){
				codigoUnidad = trim(item[i].substr(1));
				arUnidades[ arUnidades.length ] = codigoUnidad+"-"+item[++i];
				j++;
				k = 0;
			}
			
			// Si viene precedido de & es presentacion (forma farmaceutica) y se asigna al array arPresentaciones
			if( item[i].substr(0,1) == "&" ){
				codigoFtica = trim(item[i].substr(1));
				arPresentaciones[ arPresentaciones.length ] = codigoFtica+"-"+item[++i];
				l++;
				m = 0;
			}

			// Si viene precedido de # es via de administracion y se asigna al array arViasAdmon
			if( item[i].substr(0,1) == "#" ){
				codigoVia = trim(item[i].substr(1));
				arViasAdmon[ arViasAdmon.length ] = codigoVia+"-"+item[++i];
				n++;
				p = 0;
			}

			// Si viene precedido de $ es dosis y se asigna al array arDosis
			if( item[i].substr(0,1) == "$" ){
				valorDosis = trim(item[i].substr(1));
				arDosis[ arDosis.length ] = valorDosis;
				q++;
				r = 0;
			}
			
			// var valorOncologico = "on";
			// Si viene precedido de $ es dosis y se asigna al array arDosis
			if( item[i].substr(0,1) == "?" ){
				valorOncologico = trim(item[i].substr(1));
			}
			
			
			// Si viene precedido de $ es dosis y se asigna al array arDosis
			if( item[i].substr(0,1) == "*" ){
				valorInterno = trim(item[i].substr(1));
			}
			
			// Si viene precedido de - es artículo, comienza la asignación de los arreglos de relación y del arreglo arArticulos
			if( item[i].substr(0,1) == "-" ){
				
				var pos = arArticulos.length;
				
				if( !arRelUnidad[j] ){
					arRelUnidad[j] = new Array(); 
				}
				
				if( !arRelPresentacion[l] ){
					arRelPresentacion[l] = new Array(); 
				}
				
				if( !arRelViaAdmon[n] ){
					arRelViaAdmon[n] = new Array(); 
				}

				if( !arRelDosis[q] ){
					arRelDosis[q] = new Array(); 
				}
				
				if( !arArticulos[ pos ] ){
					arArticulos[ pos ] = new Array();
					arArticulos[ pos ][0] = "";
					arArticulos[ pos ][1] = "";
				}
				
				arRelUnidad[j][k] = arArticulos.length;
				arRelPresentacion[l][m] = arArticulos.length;
				arRelViaAdmon[n][p] = arArticulos.length;
				arRelDosis[q][r] = arArticulos.length;
				arArticulos[ pos ][0] = item[i++].substr( 1 );
				arArticulos[ pos ][1] = item[i];
				k++;
				m++;
				p++;
				r++;
				
				if(arArticulos[ pos ][0]=='LQ0000')
					esLiquidoEndovenoso = true;
			}
			
			//Si el medicamento es POS, dejo obligatorio el campo tto de tratamiento
			if( item[i].substr(0,1) == "=" ){
				
				var valorPos = item[i].substr(1);
				var auxTD = $( "#nuevoBuscador > tbody > tr > td:contains(tto.)" );
				if( valorPos != 'on' ){
					if( auxTD.html().indexOf( "(*)" ) < 0 )
						auxTD.html( auxTD.html() + "(*)" );
				}
				else{
					auxTD.html( auxTD.html().substr( 0, 9 ) );
				}
			}
		}		
		
		if(arViasAdmon.length==0)
			arViasAdmon[0] = '00-Sin via';
			
		//$('#wfrecuencia').val(null);
		if(valorOncologico.toLowerCase()!='on')
		{
			$("#wfrecuencia .onc").hide();
			$("#wfrecuencia .cli").show();
		}
		else
		{
			$("#wfrecuencia .cli").hide();
			$("#wfrecuencia .onc").show();
		}
		
		
		// 2012-12-14
		// alert("autocompletarParaBusqueMedicamentosPorFamilia: arRelUnidad="+arRelUnidad.toSource());
		// alert(arUnidades.toSource());
		// alert(arRelPresentacion.toSource());
		// alert(arPresentaciones.toSource());
		// alert(arArticulos.toSource());

		// 2012-07-09
		// Se adiciona seleccione al select de presentación de medicamentos
		//arUnidades[ arUnidades.length ] = "-seleccione-";
		
		/////////////////////////////////////////////////////////////////////////////////////////
		// Se reestablece valores vacio para los campos que hayan sido llenados
		document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
		
		if(!esLiquidoEndovenoso)
		{
			document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo
			document.getElementById( "wdosisfamilia" ).value = '';			
		}
		else
		{
			document.getElementById( "wdosisfamilia" ).value = '';			//Desactivo el campo de dosis 
			document.getElementById( "wdosisfamilia" ).disabled = true;			//Desactivo el campo de dosis 
		}
		
		
		var posnombre = '';
		
		if(document.getElementById('wdosisfamilia'))
		{
			if(arDosis.length>1)
			{
				document.getElementById('wdosisfamilia'+posnombre).value = '';
				$("#wdosisfamilia"+posnombre).trigger('dblclick');
				$("#wdosisfamilia"+posnombre).flushCache();
				$("#wdosisfamilia"+posnombre).unbind("result");
				
				$("#wdosisfamilia"+posnombre).autocomplete( arDosis , 
				{
						max: 10,
						scroll: false,
						scrollHeight: 500,
						matchContains: false,
						width:70,
						minChars: 0,
						autoFill:false,
						delay:200,
						selectFirst:false
				});	

				$("#wdosisfamilia"+posnombre).focus(function() { 
					$(this).trigger('keydown');
					$(this).trigger('click'); 
					$(this).trigger('click'); 
				});
			
			}
			else if(arDosis.length==1)
			{
				document.getElementById('wdosisfamilia'+posnombre).value = arDosis[ arDosis.length - 1 ];
			}
		}
		

		//Quito los valores de los campos de busqueda y adición de medicamentos
		document.getElementById('wpresentacion').selectedIndex = -1;			
		document.getElementById('wunidad').selectedIndex = -1;			
		document.getElementById('wfrecuencia').selectedIndex = -1;			
		document.getElementById('wadministracion').selectedIndex = -1;			
		document.getElementById('wcondicionsum').selectedIndex = -1;			
		document.getElementById('wdiastratamiento').value = '';			
		document.getElementById('wdosismaxima').value = '';			
		document.getElementById('wtxtobservasiones').value = '';			
		document.getElementById('wposologia').value = '';			
		document.getElementById('wunidadposologia').value = '';			

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
		/////////////////////////////////////////////////////////////////////////////////////////

		// Quedan definidos los arrays derelación y de artículos que también se usaran en otras funciones
		arRelacionesUnidad = arRelUnidad;
		arRelacionesPresentacion = arRelPresentacion;
		arRelacionesViaAdmon = arRelViaAdmon;
		arArticulosSeleccionados = arArticulos;

		// 2012-12-14
		// alert("autocompletarParaBusqueMedicamentosPorFamilia: arRelacionesUnidad="+arRelacionesUnidad.toSource());
		
		// Se asigna dinámicamente las opciones a los selects wunidad, wpresentacion, wadministracion 
		creandoOptionsValue( document.getElementById( "wunidad" ), arUnidades.reverse() );
		creandoOptionsValue( document.getElementById( "wpresentacion" ), arPresentaciones.reverse() );
		creandoOptionsValue( document.getElementById( "wadministracion" ), arViasAdmon.reverse() );
					
		// 2012-07-09
		// Para que muestre por defecto el select vacio
		if(arUnidades.length>1)
			document.getElementById( "wunidad" ).options.selectedIndex = -1;
		
		if(arPresentaciones.length>1)
			document.getElementById( "wpresentacion" ).options.selectedIndex = -1;

		if(arViasAdmon.length>1)
			document.getElementById( "wadministracion" ).options.selectedIndex = -1;
		
		document.getElementById('wckInterno').checked = false;
		if( valorInterno == 'on' ){
			document.getElementById('wckInterno').checked = true;
		}
		
		document.getElementById('wckInterno').onclick();

		// Se reviza si hay algun detalle de fmailia desplegado y se oculta
		ocultarDetalleFliaAnterior();

		//creando array de medicamentos
	});
}


/************************************************************************************************************************
 * Autocompletar para la busqueda de medicamentos por familias en la pestaña Alta
 * @return
 ************************************************************************************************************************/
function autocompletarParaBusqueMedicamentosPorFamiliaAlta(){
	
	// Se limpia cache del campo
	$("#wnombrefamiliaimp").flushCache();
	$("#wnombrefamiliaimp").unbind("result");

	// Se hace el llamado AJAX para traer los resultados a la función autocomplete
	$("#wnombrefamiliaimp").setOptions({url:"ordenesidc.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value });
	
	$("#wnombrefamiliaimp").autocomplete("ordenesidc.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value, {
		cacheLength:0,
		delay:0,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		minChars: 2,
		autoFill:false,
		formatResult: function(data, value){
			return value;
		}
	}).result(function(event, item) { 
    	
		//creando array de resultados
		var arDosis = new Array();
		var arUnidades = new Array();
		var arPresentaciones = new Array();
		var arViasAdmon = new Array();
		var arArticulos = new Array();
		
		// Indices para los diferentes arrays
		var j = -1;
		var k = 0;
		var l = -1;
		var m = 0;
		var n = -1;
		var p = 0;
		var q = -1;
		var r = 0;
		
		// Arrays de relación con los articulos
		var arRelDosis = new Array(); 
		var arRelUnidad = new Array(); 
		var arRelPresentacion = new Array(); 
		var arRelViaAdmon = new Array(); 

		esLiquidoEndovenoso = false;
		
		for( var i = 0; i < item.length; i++ ){
			
			// Si viene precedido de @ es unidad y se asigna al array arUnidades
			if( item[i].substr(0,1) == "@" ){
				codigoUnidad = trim(item[i].substr(1));
				arUnidades[ arUnidades.length ] = codigoUnidad+"-"+item[++i];
				j++;
				k = 0;
			}
			
			// Si viene precedido de & es presentacion (forma farmaceutica) y se asigna al array arPresentaciones
			if( item[i].substr(0,1) == "&" ){
				codigoFtica = trim(item[i].substr(1));
				arPresentaciones[ arPresentaciones.length ] = codigoFtica+"-"+item[++i];
				l++;
				m = 0;
			}

			// Si viene precedido de # es via de administracion y se asigna al array arViasAdmon
			if( item[i].substr(0,1) == "#" ){
				codigoVia = trim(item[i].substr(1));
				arViasAdmon[ arViasAdmon.length ] = codigoVia+"-"+item[++i];
				n++;
				p = 0;
			}

			// Si viene precedido de $ es dosis y se asigna al array arDosis
			if( item[i].substr(0,1) == "$" ){
				valorDosis = trim(item[i].substr(1));
				arDosis[ arDosis.length ] = valorDosis;
				q++;
				r = 0;
			}

			// Si viene precedido de - es artículo, comienza la asignación de los arreglos de relación y del arreglo arArticulos
			if( item[i].substr(0,1) == "-" ){
				
				var pos = arArticulos.length;
				
				if( !arRelUnidad[j] ){
					arRelUnidad[j] = new Array(); 
				}
				
				if( !arRelPresentacion[l] ){
					arRelPresentacion[l] = new Array(); 
				}
				
				if( !arRelViaAdmon[n] ){
					arRelViaAdmon[n] = new Array(); 
				}

				if( !arRelDosis[q] ){
					arRelDosis[q] = new Array(); 
				}
				
				if( !arArticulos[ pos ] ){
					arArticulos[ pos ] = new Array();
					arArticulos[ pos ][0] = "";
					arArticulos[ pos ][1] = "";
				}
				
				arRelUnidad[j][k] = arArticulos.length;
				arRelPresentacion[l][m] = arArticulos.length;
				arRelViaAdmon[n][p] = arArticulos.length;
				arRelDosis[q][r] = arArticulos.length;
				arArticulos[ pos ][0] = item[i++].substr( 1 );
				arArticulos[ pos ][1] = item[i];
				k++;
				m++;
				p++;
				r++;
				
				if(arArticulos[ pos ][0]=='LQ0000')
					esLiquidoEndovenoso = true;
			}
		}
		
		// 2012-12-14
		// alert("autocompletarParaBusqueMedicamentosPorFamilia: arRelUnidad="+arRelUnidad.toSource());
		// alert(arUnidades.toSource());
		// alert(arRelPresentacion.toSource());
		// alert(arPresentaciones.toSource());
		// alert(arArticulos.toSource());

		// 2012-07-09
		// Se adiciona seleccione al select de presentación de medicamentos
		//arUnidades[ arUnidades.length ] = "-seleccione-";
		
		/////////////////////////////////////////////////////////////////////////////////////////
		// Se reestablece valores vacio para los campos que hayan sido llenados
		//document.getElementById( "regletaGrabacionimp" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
		
		if(!esLiquidoEndovenoso)
		{
			document.getElementById( "wdosisfamiliaimp" ).disabled = false;			//Activo el campo de dosis si está inactivo
			document.getElementById( "wdosisfamiliaimp" ).value = '';			
		}
		else
		{
			document.getElementById( "wdosisfamiliaimp" ).value = '';			//Desactivo el campo de dosis 
			document.getElementById( "wdosisfamiliaimp" ).disabled = true;			//Desactivo el campo de dosis 
		}

		//Quito los valores de los campos de busqueda y adición de medicamentos
		document.getElementById('wpresentacionimp').selectedIndex = -1;			
		document.getElementById('wunidadimp').selectedIndex = -1;			
		document.getElementById('wfrecuenciaimp').selectedIndex = -1;			
		//document.getElementById('wadministracionimp').selectedIndex = -1;			
		//document.getElementById('wcondicionsumimp').selectedIndex = -1;			
		document.getElementById('wdiastratamientoimp').value = '';			
		document.getElementById('wdosismaximaimp').value = '';			
		document.getElementById('wtxtobservasionesimp').value = '';			
		document.getElementById('wunidadposologia').value = '';			
		document.getElementById('wposologia').value = '';			
		
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
		/////////////////////////////////////////////////////////////////////////////////////////

		// Quedan definidos los arrays derelación y de artículos que también se usaran en otras funciones
		arRelacionesUnidad = arRelUnidad;
		arRelacionesPresentacion = arRelPresentacion;
		//arRelacionesViaAdmon = arRelViaAdmon;
		arArticulosSeleccionados = arArticulos;

		// 2012-12-14
		// alert("autocompletarParaBusqueMedicamentosPorFamilia: arRelacionesUnidad="+arRelacionesUnidad.toSource());
		
		// Se asigna dinámicamente las opciones a los selects wunidad, wpresentacion, wadministracion 
		creandoOptionsValue( document.getElementById( "wunidadimp" ), arUnidades.reverse() );
		creandoOptionsValue( document.getElementById( "wpresentacionimp" ), arPresentaciones.reverse() );
		//creandoOptionsValue( document.getElementById( "wadministracionimp" ), arViasAdmon.reverse() );
					
		// 2012-07-09
		// Para que muestre por defecto el select vacio
		if(arUnidades.length>1)
			document.getElementById( "wunidadimp" ).options.selectedIndex = -1;
		
		if(arPresentaciones.length>1)
			document.getElementById( "wpresentacionimp" ).options.selectedIndex = -1;

		// if(arViasAdmon.length>1)
			// document.getElementById( "wadministracionimp" ).options.selectedIndex = -1;

		// Se reviza si hay algun detalle de fmailia desplegado y se oculta
		// ocultarDetalleFliaAnterior();

		//creando array de medicamentos
	});
}



/************************************************************************************************************************
 * Filtrar medicamentos por familia
 * tipoConsulta: define si se va a filtrar con base en presentación o con base en presntacion y unidad
 * @return
 ************************************************************************************************************************/
function filtrarMedicamentosPorCampo(tipoConsulta,posnombre){

	//debugger;
	
	if(!posnombre)
		posnombre = '';
	
	// Vaiable que va a tener los datos del resultado AJAX
	var item = "";
	
	// Si se ha seleccinado alguna presentación
	if(document.getElementById('wpresentacion'+posnombre).selectedIndex!='-1')
	{
		// Asigno la presentacion seleccionada
		var presentacionFiltro = document.getElementById('wpresentacion'+posnombre).options[ document.getElementById('wpresentacion'+posnombre).selectedIndex ].value;

		// Si se ha seleccionado una unidad
		if(tipoConsulta=='unidad' && document.getElementById('wunidad'+posnombre).selectedIndex!='-1')
			var unidad = document.getElementById('wunidad'+posnombre).options[ document.getElementById('wunidad'+posnombre).selectedIndex ].value;
		else
			var unidad = '%';

		// definición de parámetros para el llamado AJAX
		var parametros = "consultaAjaxKardex=34&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value+"&familia="+document.getElementById('wnombrefamilia'+posnombre).value+"&presentacion="+presentacionFiltro+"&unidad="+unidad;
		//alert(parametros);

		var mensaje = "";

		// Llamado AJAX para obtener los datos de búsqueda de medicamentos
		// Con los nuevos filtros de presentación y/o unidad
		try{
			ajax=nuevoAjax();
			
			ajax.open("POST", "ordenesidc.inc.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);
			
			// Asigno el resultado que se traen con el llamado AJAX
			var item = ajax.responseText;

			if ( !estaEnProceso(ajax) ) {
				ajax.send(null);
			}
		}catch(e){	}
		

		// Formo el array de los resultados para la busquedad de medicamentos
		var item = item.split('|');
	
		
		// Creando array de resultados
		var arDosis = new Array();
		var arUnidades = new Array();
		var arPresentaciones = new Array();
		var arViasAdmon = new Array();
		var arArticulos = new Array();

		// Indices para los diferentes arrays
		var j = -1;
		var k = 0;
		var l = -1;
		var m = 0;
		var n = -1;
		var p = 0;
		var q = -1;
		var r = 0;

		// Arrays de relación con los articulos
		var arRelDosis = new Array(); 
		var arRelUnidad = new Array(); 
		var arRelPresentacion = new Array(); 
		var arRelViaAdmon = new Array(); 

		for( var i = 0; i < item.length; i++ ){
			
			// Si viene precedido de @ es unidad y se asigna al array arUnidades
			if( item[i].substr(0,1) == "@" ){
				codigoUnidad = trim(item[i].substr(1));
				arUnidades[ arUnidades.length ] = codigoUnidad+"-"+item[++i];
				j++;
				k = 0;
			}
			
			// Si viene precedido de & es presentacion (forma farmaceutica) y se asigna al array arPresentaciones
			if( item[i].substr(0,1) == "&" ){
				codigoFtica = trim(item[i].substr(1));
				arPresentaciones[ arPresentaciones.length ] = codigoFtica+"-"+item[++i];
				l++;
				m = 0;
			}

			// Si viene precedido de # es via de administracion y se asigna al array arViasAdmon
			if( item[i].substr(0,1) == "#" ){
				codigoVia = trim(item[i].substr(1));
				arViasAdmon[ arViasAdmon.length ] = codigoVia+"-"+item[++i];
				n++;
				p = 0;
			}

			// Si viene precedido de $ es dosis y se asigna al array arDosis
			if( false && item[i].substr(0,1) == "$" ){
				valorDosis = trim(item[i].substr(1));
				arDosis[ arDosis.length ] = valorDosis;
				q++;
				r = 0;
			}
			
			// Si viene precedido de - es artículo, comienza la asignación de los arreglos de relación y del arreglo arArticulos
			if( item[i].substr(0,1) == "-" ){
				
				var pos = arArticulos.length;
				
				if( !arRelUnidad[j] ){
					arRelUnidad[j] = new Array(); 
				}
				
				if( !arRelPresentacion[l] ){
					arRelPresentacion[l] = new Array(); 
				}
				
				if( !arRelViaAdmon[n] ){
					arRelViaAdmon[n] = new Array(); 
				}

				if( !arRelDosis[q] ){
					arRelDosis[q] = new Array(); 
				}
				
				if( !arArticulos[ pos ] ){
					arArticulos[ pos ] = new Array();
					arArticulos[ pos ][0] = "";
					arArticulos[ pos ][1] = "";
				}
				
				arRelUnidad[j][k] = arArticulos.length;
				arRelPresentacion[l][m] = arArticulos.length;
				arRelViaAdmon[n][p] = arArticulos.length;
				arRelDosis[q][r] = arArticulos.length;
				arArticulos[ pos ][0] = item[i++].substr( 1 );
				arArticulos[ pos ][1] = item[i];
				k++;
				m++;
				p++;
				r++;
			}
		}
		
		if(document.getElementById('wdosisfamilia'))
		{
			if(arDosis.length>1)
			{
				document.getElementById('wdosisfamilia'+posnombre).value = '';
				$("#wdosisfamilia"+posnombre).trigger('dblclick');
				$("#wdosisfamilia"+posnombre).flushCache();
				$("#wdosisfamilia"+posnombre).unbind("result");
				
				$("#wdosisfamilia"+posnombre).autocomplete( arDosis , 
				{
						max: 10,
						scroll: false,
						scrollHeight: 500,
						matchContains: false,
						width:70,
						minChars: 0,
						autoFill:false,
						delay:200,
						selectFirst:false
				});	

				$("#wdosisfamilia"+posnombre).focus(function() { 
					$(this).trigger('keydown');
					$(this).trigger('click'); 
					$(this).trigger('click'); 
				});
			
			}
			else if(arDosis.length==1)
			{
				document.getElementById('wdosisfamilia'+posnombre).value = arDosis[ arDosis.length - 1 ];
			}
		}
			
		/*
		if(document.getElementById('wdosisfamilia')){
			
			$("#wdosisfamilia").flushCache();
			$("#wdosisfamilia").unbind("result");
		
			$("#wdosisfamilia").setOptions({arDosis});
			
			$("#wdosisfamilia").autocomplete(arDosis, {
				max: 100,
				scroll: false,
				scrollHeight: 500,
				matchContains: true,
				width:500,
				minChars: 3,
				autoFill:false,
				formatResult: function(data, value) {
					return value;
				}
			}).result(function(event, item) {

			});
		}
		*/
			
			
		
		
		
		// 2012-07-09
		// Se adiciona seleccione al select de presentación de medicamentos
		//arUnidades[ arUnidades.length ] = "-seleccione-";
		

		// Quedan definidos los arrays de relación y de artículos que también se usaran en otras funciones
		//arRelacionesPresentacion = arRelPresentacion;		// Las presentaciones solo se definen en la seleccion de la familia
		if(tipoConsulta=='presentacion')	// Se filtra unidades solo si lo que se selecciono fue presentacion
			arRelacionesUnidad = arRelUnidad;
		arRelacionesViaAdmon = arRelViaAdmon;
		//arArticulosSeleccionados = arArticulos;

		// 2012-12-14
		// alert("filtrarMedicamentosPorCampo: arRelacionesUnidad="+arRelacionesUnidad.toSource());
		
		// Se asigna dinámicamente las opciones a los selects wunidad, wpresentacion, wadministracion 
		//creandoOptionsValue( document.getElementById( "wpresentacion" ), arPresentaciones.reverse() );		// Las presentaciones solo se definen en la seleccion de la familia
		if(tipoConsulta=='presentacion')		// Se filtra unidades solo si lo que se selecciono fue presentacion
			creandoOptionsValue( document.getElementById( "wunidad"+posnombre ), arUnidades.reverse() );		
		//creandoOptionsValue( document.getElementById( "wadministracion"+posnombre ), arViasAdmon.reverse() );
					
		// 2012-07-09
		// Para que muestre por defecto el select vacio
		if(arUnidades.length>1)
			document.getElementById( "wunidad"+posnombre ).options.selectedIndex = -1;
		
		if(arPresentaciones.length>1)
			document.getElementById( "wpresentacion"+posnombre ).options.selectedIndex = -1;

		// if(arViasAdmon.length>1)
			// document.getElementById( "wadministracion"+posnombre ).options.selectedIndex = -1;

		// Se reviza si hay algun detalle de familia desplegado y se oculta
		ocultarDetalleFliaAnterior();

		//creando array de medicamentos
	}
}

/************************************************************************************
 * Actualiza los mensjaes sin leer cuando se actualiza la mensajeria
 ************************************************************************************/
function alActualizarMensajeria(){
	
	var campo = document.getElementById( "sinLeer" );	
	campo.innerHTML = mensajeriaSinLeer;	
}

/**********************************************************************
 * Octubre 11 de 2011
 **********************************************************************/
function enviandoMensaje(){

	if( document.getElementById( 'mensajeriaKardex' ).value != '' ){
		enviarMensaje( document.getElementById( 'mensajeriaKardex' ), document.getElementById( 'mesajeriaPrograma' ).value,document.forms.forma.whistoria.value,document.forms.forma.wingreso.value,document.getElementById( "usuario" ).value, document.forms.forma.wbasedato.value );
	}
}

/**********************************************************************
 * Octubre 11 de 2011
 **********************************************************************/
function marcarLeido( campo, id ){
		
	//campo es una tabla que tiene toda la informacion que se muestra
	//Con dos fila
	//La primera fila tiene dos celdas y la segunda 1
	
	marcandoLeido( document.forms.forma.wbasedato.value, id, document.getElementById( "usuario" ).value );
	
	document.getElementById( "sinLeer" ).innerHTML = document.getElementById( "sinLeer" ).innerHTML-1;
	
	//quitando blinks
	for( var i = 0; i < campo.rows.length; i++ ){
		
		fila = campo.rows[i];
		
		for( var j = 0; j < fila.cells.length; j++ ){
			
			celda = fila.cells[j];
			
			if( celda.firstChild.tagName.toLowerCase() == "blink" ){
			
				var aux = celda.firstChild.innerHTML;
				
				celda.innerHTML = aux;
			}
		}
	}
}

/******************************************************************************************************************************
 * Control de seleccion de fechas. Evita que se selecciona una fecha y hora antes de la fecha actual y antes de dos horas.
 * @param date
 * @param stringFecha
 * @return
 * 
 * Creado: Marzo 16 de 2011
 ******************************************************************************************************************************/
function alSeleccionarFecha( date, stringFecha ){
	
	var now = new Date();
	
	var comparacion = compareDatesOnly(now, date.currentDate);
	
	var hours = date.currentDate.getHours();
	
	//Limita que no se seleccionen dias anteriores
	if (comparacion < 0) {
		desactivar = true;
	}
	
	var ano = date.currentDate.getFullYear();
	var mes = date.currentDate.getMonth();
	var dia = parseInt(date.currentDate.getDate())+1;
	var hora = date.currentDate.getHours();
	
	//Limita que no se seleccionen horas anteriores
	if (typeof(hours) != "undefined"){
	
		/****************************************************************************************
		 * Marzo 13 de 2012
		 *
		 * Si el medicamento es nuevo se permite colocar la hora a partir de la ronda actual,
		 * de lo contrario solo se permite a partir de la siguiente ronda
		 ****************************************************************************************/
		if( !date.params.inputField.parentNode.parentNode.className || date.params.inputField.parentNode.parentNode.className == '' ){	//Si el articulo es nuevo
			// alert( "Nuevo" );
			if ( (hours <= now.getHours() && now.getHours()-hours > 1 ) && comparacion == 0 ) {	//Marzo 6 de 2012
				date.setDate( new Date(ano,mes,dia,hora,0,0) );
				alert( "La hora no esta permitida para la fecha seleccionada" );
				return true;
			}
			else if( !date.dateClicked && now.getDate() + 1 == dia - 1 && now.getMonth() == mes && now.getFullYear() == ano && now.getHours()-hours <= 1 ){
				date.setDate( new Date(ano,mes,dia-2,hora,0,0) );
				return true;
			}
		}
		else{	//Si el articulo es viejo
			if ( (hours <= now.getHours() /*&& now.getHours()-hours > 1*/ ) && comparacion == 0 ) {	//Marzo 6 de 2012
				// alert( "Viejo" );
				date.setDate( new Date(ano,mes,dia,hora,0,0) );
				alert( "La hora no esta permitida para la fecha seleccionada" );
				return true;
			}
			else if( !date.dateClicked && now.getDate() + 1 == dia - 1 && now.getMonth() == mes && now.getFullYear() == ano && now.getHours()-hours <= 1 ){
				date.setDate( new Date(ano,mes,dia-2,hora,0,0) );
				return true;
			}
		}
	}
	
	if( date.dateClicked ){
			date.params.inputField.value = date.currentDate.print(date.params.ifFormat);
			if( date.params.inputField.onchange ){
				date.params.inputField.onchange();
			}
		date.callCloseHandler();
	}
	else{
		date.params.inputField.ultimaFechaSeleccionada = date.currentDate;
	}
}

/**************************************************************************************************
 * Abre una ventana maximizada con la url correspondiente
 **************************************************************************************************/
function abrirVentanaVerAnteriores( url ){

	var ancho=screen.width;
	var alto=screen.availHeight;
	var v = window.open( url,'','scrollbars=1, width='+ancho+', height='+alto );
	v.moveTo(0,0);
}

/******************************************************************************
 * Se hace un efecto de pulso para que la fila parpadee
 * - Esto aplica para todas la tablas en la que se muestra los medicamentos
 * 
 * Marzo 7 de 2011
 ******************************************************************************/
function inicializarPulso(){
	
	var numCelda = 9;
	var numCeldaNombre = 1;
	
	//Creo el array con las tablas disponibles que permiten el parpadeo
	var tbTablas = Array( "tbDetalleN", "tbDetalleQ", "tbDetalleA", "tbDetalleAddN", "tbDetalleAddQ", "tbDetalleAddA" );
	
	for( var i = 0; i < tbTablas.length; i++ ){
		
		var tb = document.getElementById( tbTablas[i] );

		if( tb ){
		
			for( var numFila = 1; numFila < tb.rows.length; numFila++ ){

				if( isset(tb.rows[ numFila ].cells[ numCeldaNombre ]) && ( tb.rows[ numFila ].cells[ numCeldaNombre ].className.toLowerCase() == "fondoalertaeliminar"
					|| tb.rows[ numFila ].cells[ numCeldaNombre ].originalClass && tb.rows[ numFila ].cells[ numCeldaNombre ].originalClass.toLowerCase() == "fondoalertaeliminar" 
				) ){	//Abril 1 de 2011
					
					if( !tb.rows[ numFila ].cells[numCeldaNombre].originalClass && tb.rows[ numFila ].cells[numCeldaNombre].className != '' ){
						tb.rows[ numFila ].cells[numCeldaNombre].originalClass = tb.rows[ numFila ].cells[numCeldaNombre].className; 
					}
					
					if( tb.rows[ numFila ].cells[numCeldaNombre].className == tb.rows[ numFila ].cells[numCeldaNombre].originalClass ){
						
						tb.rows[ numFila ].cells[numCeldaNombre].className = tb.rows[ numFila ].className;
//						tb.rows[ numFila ].cells[numCelda].className = "fila1";
					}
					else{
						tb.rows[ numFila ].cells[numCeldaNombre].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
//						tb.rows[ numFila ].cells[numCelda].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
					}
				}
				else if( tb.rows[ numFila ].className.toLowerCase() != "suspendido" && document.getElementById( "hiNoParpadear" ).value == 'on' ){	//Marzo 8 de 2011
				
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
					if(isset(tb.rows[ numFila ].cells[numCeldaNombre]))
						tb.rows[ numFila ].cells[numCeldaNombre].className = tb.rows[ numFila ].cells[numCeldaNombre].originalClass;
					if(isset(tb.rows[ numFila ].cells[numCelda]))	
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
	
	hayArticulosAImprimir();
	
	//Agrego función click para que no se pueda modificar el check de manejo interno
	$( "[id^=wchkint]", $( "#tbDetalleN" ) ).click(
		function(){
			this.checked = !this.checked;
		}
	);

	$("#wfrecuencia .onc").hide();
	$("#wfrecuencia .cli").hide();
	
	$("#tabs").tabs({ fx: {opacity: 'toggle' }, select: function(event, ui) { if(fixedMenu2 && fixedMenu2 != 'undefined') fixedMenu2.hide(); } }); //JQUERY:  Activa los tabs para las secciones del kardex

	$("#tabs2").tabs({ fx: {opacity: 'toggle' }, select: function(event, ui) { if(fixedMenu2 && fixedMenu2 != 'undefined') fixedMenu2.hide(); } }); 
	
	$("#tabs").tabs('select', 0);

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
     
     if(document.getElementById("movExamenes")){
    	 $("#movExamenes").draggable();
     }

	 
     //Ciclo para colocar en funcionamiento los tooltips
     var cont1 = 0, cont2 = 0;
     while(document.getElementById("trFil"+cont1)){
    	$('#trFil'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
//    		 document.getElementById('tr'+tipo+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
    	 cont1++;
     }

	$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
	 
//     var protocolos = new Array('%','N','A','U','Q');
//     var tipo = "";
//     debugger;
//
//     while(cont2 < protocolos.length){
//    	 tipo = protocolos[cont2];
//	    cont1 = 0;
//	    cont2++;
//     }

	//Tooltips de protocolo de ayudas y procedimientos
     cont1 = 0;
     tipo = "4-"; 
     while(document.getElementById(tipo+cont1)){
		 $('#'+tipo+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -140 });
		 cont1++;
 	 }

     //Simpletree Arbol
     var indiceArbol = 1;     
     if(!document.getElementById("arbol"+indiceArbol)){
    	 indiceArbol = 2;
     }
     while(document.getElementById("arbol"+indiceArbol)){
 	 var simpleTreeCollection = $('#arbol'+indiceArbol).simpleTree({
 		 	autoclose: true,
 		 	drag: false,
 		 	afterClick:function(node){
 				var elemento = $('span:first',node);
 			
 				var indice = elemento.parent().attr("id");
 				var monda = elemento.parent().parent();
 				var monda2 = node;
 				var texto = elemento.text();

 				/*Si el elemento seleccionado es una hoja proceso la peticion, una rama no tiene efecto en este caso
 				 * Esto se logra con el id
 				 */
 				if(indice.substring(0,1) == "H"){
 					seleccionHojaAccionesComplementarias(indice,texto,this);
 				}
 			},
 			afterDblClick:function(node){ /*alert("text-"+$('span:first',node).text());*/ },
 			afterMove:function(destination, source, pos){ /*alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);*/},
 			afterAjax:function(){ /*alert('Loaded');*/ },
 			animate:true,//,docToFolderConvert:true
 			identificador:indiceArbol
 		});
 		indiceArbol++;
    }
 	
    enfocarInicio();

    if(document.getElementById("whgrabado") && document.getElementById("whgrabado").value != ''){
    	$.growlUI('','Se ha grabado la orden');
    }
    $("#tabs").css('display','block');
    $("#tabs2").css('display','block');
    $("#msjInicio").css('display','none');
    
    $("#acExamenes").accordion({ collapsible: true });
    
    //Autocomplete para LEV
    if(document.getElementById('wnomcom')){
    	$("#wnomcom").autocomplete("ordenesidc.inc.php?consultaAjaxKardex=30&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&descripcion="+document.getElementById('wnomcom').value, {
    		max: 100,
    		scroll: true,
    		scrollHeight: 500,
    		matchContains: true,
    		width:500,
    		autoFill:false,
    		minChars: 3,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {
    		alert( item );
    		seleccionarComponente("",reemplazarTodo(item," ","_"));
		
//    		this.focus();
//    		this.select();
    	});
    }
    
    //Autocomplete para Ayudas y procedures
    if(document.getElementById('wnomproc')){
    	$("#wnomproc").autocomplete("ordenesidc.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&descripcion="+document.getElementById('wnomproc').value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value, {
    		cacheLength:0,
			max: 100,
    		scroll: false,
    		scrollHeight: 500,
    		matchContains: true,
    		width:500,
    		minChars: 3,
    		autoFill:false,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {
//    		seleccionarAyudaDiagnostica(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria);
    		//var vecItem = item.split(",");
    		
    		// Por ac{a pasa cuando el tipo de orden seleccionada es "Todos"
			
			seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);
		
    		this.onfocus = function(){
//    			alert( justificacionUltimoExamenAgregado );
    			if( justificacionUltimoExamenAgregado ){
    				justificacionUltimoExamenAgregado.focus();
    				justificacionUltimoExamenAgregado = '';
    			}
    		};
//    		this.focus();
//    		this.select();
    	});
    }
    
    // Se comenta porque no afecta la funcionalidad del autocompletar - 2012-11-14
	//Autocomplete para busqueda de medicamentos por familia
	if(document.getElementById('wnombrefamilia')){
    	$("#wnombrefamilia").autocomplete("ordenesidc.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.forms.forma.wcenmez.value, {
			cacheLength:0,
			delay:0,
    		max: 100,
    		scroll: false,
    		scrollHeight: 500,
    		matchContains: true,
			matchSubset:false,
    		width:1000,
    		minChars: 2,
    		autoFill:false,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {

    		this.focus();
    		this.select();
    	});
    }
    
    //Componente de autocompletar para busqueda de medicamentos
    if(document.getElementById('wnombremedicamento')){
    	$("#wnombremedicamento").autocomplete("./ordenesidc.inc.php?consultaAjaxKardex=30&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&ccoPaciente="+document.forms.forma.wservicio.value, {
    		max: 100,
    		scroll: true,
    		scrollHeight: 500,
    		matchContains: true,
    		width:1000,
    		autoFill:false,
    		minChars: 4,
    		formatResult: function(data, value){
//    			debugger;
				return value;
			}
    	}).result(function(event, item) {
//    		debugger;
    		if(item != "No se encontraron coincidencias"){
//    		seleccionarComponente("",reemplazarTodo(item," ","_"));
    		
    		/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
			 * Argumentos:
			 * 
			 * 0: Como se muestra en el autocomplete
			 * 1: Codigo del articulo
			 * 2: Nombre comercial del articulo
			 * 3: Nombre genérico del articulo
			 * 4: Tipo protocolo
			 * 5: (M)edicamento o (L)iquido
			 * 6: Es generico
			 * 7: Origen
			 * 8: Grupo de medicamento
			 * 9: Forma farmaceutica
			 * 10:Unidad
			 * 11:POS
			 * 12:Unidad de fraccion
			 * 13:Cantidad de fracciones
			 * 14:Vencimiento
			 * 15:Dias de estabilidad
			 * 16:Es dispensable
			 * 17:Es duplicable
			 * 18:Dias maximos sugeridos
			 * 19:Dosis maximas sugeridas
			 * 20:Via
			 * 21:Abre ventana parametrizada ----
			 * 22:Cantidad de dosis
			 * 23:Unidad de dosis
			 * 24:No enviar
			 * 25:Frecuencia
			 * 26:Vias de administracion
			 * 27:Condicion de suministro
			 * 28:Confirmada preparacion
			 * 29:Dias maximos tratamiento
			 * 30:Dosis maximas tratamiento
			 * 31:Observaciones adicionales
			 * 32:Componentes del tipo
			 * 33:Nombre personalizado del articulo
			 */
    		var codigoArticulo = item[1];
    		var nombreComercial = item[2];
    		var nombreGenerico = item[3];
    		var tipoProtocolo = item[4];
    		var tipoMedicamentoLiquido = item[5];
    		var esGenerico = item[6];
    		var origen = item[7];
    		var grupoMedicamento = item[8];
    		var formaFarmaceutica = item[9];
    		var unidadMedida = item[10];
    		var pos = item[11];
    		var unidadFraccion = item[12];
    		var cantidadFraccion = item[13];
    		var vencimiento = item[14];
    		var diasEstabilidad = item[15];
    		var dispensable = item[16];
    		var duplicable = item[17];
    		var diasMaximosSugeridos = item[18];
    		var dosisMaximasSugeridas = item[19];
    		var viaAdministracion = item[20];
    		var abreVentanaFija = item[21];
    		var cantidadDosisFija = item[22];
    		var unidadDosisFija = item[23];
    		var noEnviarFija = item[24];
    		var frecuenciaFija = item[25];
    		var viasAdministracionFija = item[26];
    		var condicionSuministroFija = item[27];
    		var confirmadaPreparacionFija = item[28];
    		var diasMaximosFija = item[29];
    		var dosisMaximasFija = item[30];
    		var observacionesFija = item[31];
    		var componentesTipo = item[32];
    		var nombrePersonalizadoDelArticulo = item[33];
			var unidadPosologia = '';
			var posologia = '';
    		
    		var	noEnviar = item[33];	//Abril 25 de 2011
    		
    		if( esGenerico.length > 0 ){
    			duplicable = 'on';
    		}
    		
    		agregarArticulo( "detKardex", false, codigoArticulo );
    		
    		seleccionarArticulo(codigoArticulo,reemplazarTodo(nombreComercial," ","_"),reemplazarTodo(nombreGenerico," ","_"),origen,grupoMedicamento,
    				formaFarmaceutica,unidadMedida,pos,unidadFraccion,cantidadFraccion,vencimiento,diasEstabilidad,
    				dispensable,duplicable,diasMaximosSugeridos,dosisMaximasSugeridas,viaAdministracion,tipoProtocolo,tipoMedicamentoLiquido,
    				esGenerico,abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija,
    				condicionSuministroFija,confirmadaPreparacionFija,diasMaximosFija,dosisMaximasFija,observacionesFija,componentesTipo,noEnviar,"",unidadPosologia,posologia);
		
			this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
    		}
						
    		this.focus();
    		this.select();
    	});
    }

	// 2012-07-03
	// Inicializa accordion para los exámenes realizados
	$("#acExamenes").accordion({ collapsible: true });
	$("#null").accordion({ collapsible: true });

   //Autocompletar para la busqueda de medicamentos por familias
    autocompletarParaBusqueMedicamentosPorFamilia();
	autocompletarParaBusqueMedicamentosPorFamiliaAlta();
    
	// mostrarMensajeConfirmarKardex();	//Agosto 25 de 2011
	
	mensajeriaActualizarSinLeer = alActualizarMensajeria;
	
	consultarHistoricoTextoProcesado( document.forms.forma.wbasedato.value, document.forms.forma.wemp_pmla.value, document.forms.forma.whistoria.value, document.forms.forma.wingreso.value, document.getElementById( 'mesajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria' ) );	//Octubre 11 de 2011
	
	mensajeriaTiempoRecarga = consultasAjax( "POST", "../../../include/movhos/mensajeriaKardex.php", "consultaAjax=4&wemp="+document.forms.forma.wemp_pmla.value, false );	
	mensajeriaTiempoRecarga = mensajeriaTiempoRecarga*60000;	//El tiempo que se consulta esta en minutos
	
	if( !mensajeriaTiempoRecarga || mensajeriaTiempoRecarga == 0 || isNaN( mensajeriaTiempoRecarga ) )
			mensajeriaTiempoRecarga = 10*60000;
	
	setInterval( "mensajeriaActualizar()", mensajeriaTiempoRecarga );
}

/****************************************************************************************************************************************************************
 * Inicializa el autocompletar de jquery para procedimientos y ayudas diangosticos segun el filttro seleccionado, esta función se llama cada vez que
 * se cambia el filtro de tipo de servicio
 * @return
 * 
 * Enero 24 de 2011
 ****************************************************************************************************************************************************************/
function autocompletarParaConsultaDiagnosticas(){
	
	var tipoServicioSel = document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value;
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;	
	
	parametros = "consultaAjaxKardex=47&basedatoshce="+document.forms.forma.wbasedatohce.value+"&wtipo="+tipoServicioSel+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 

	try{
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		formTipoOrden = "";
		ajax.onreadystatechange=function()
		{ 
			if (ajax.readyState==4 && ajax.status==200){
				// var formTipoOrden = ajax.responseText;
				formTipoOrden = ajax.responseText;

				if(formTipoOrden!="" && formTipoOrden!=" ")
				{
					
					//////////////////////////////////////////////////////////

					// Consulto si el formulario ha sido diligenciado en la historia clínica electrónica
					parametros = "consultaAjaxKardex=50&basedatoshce="+document.forms.forma.wbasedatohce.value+"&formTipoOrden="+formTipoOrden+"&historia="+historia+"&ingreso="+ingreso+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 

					try{
						ajax=nuevoAjax();
					
						ajax.open("POST", "ordenesidc.inc.php",true);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);
						
						ajax.onreadystatechange=function()
						{ 

							if (ajax.readyState==4 && ajax.status==200){
								var formDiligenciado = ajax.responseText;
								
								if(true || formDiligenciado!='ok')
								{
									totalExamenesAntesGrabar = formDiligenciado;
									parametros = "consultaAjaxKardex=49&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value; 

									try{
										ajax=nuevoAjax();
									
										ajax.open("POST", "ordenesidc.inc.php",true);
										ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
										ajax.send(parametros);
										
										ajax.onreadystatechange=function()
										{ 
											if (ajax.readyState==4 && ajax.status==200){
												var strItem = ajax.responseText;
												
												var item = strItem.split(',');

												seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);
												
												this.onfocus = function(){
													if( justificacionUltimoExamenAgregado ){
														justificacionUltimoExamenAgregado.focus();
														justificacionUltimoExamenAgregado = '';
													}
												};

												document.getElementById("btnCerrarVentana").style.display = 'none';

												
											} 
										}
										if ( !estaEnProceso(ajax) ) {
											ajax.send(null);
										}
									}catch(e){	}	
												
					
								}
								// else
								// {
									// alert("La orden ya ha sido ingresada");
								// }
								
								var select = $('#wselTipoServicio');
								select.val($('option:first', select).val());

							} 
						}
						if ( !estaEnProceso(ajax) ) {
							ajax.send(null);
						}
					}catch(e){	}	
						
					//////////////////////////////////////////////////////////


					var urlform = 'HCE.php?accion=M&ok=0&empresa='+document.forms.forma.wbasedatohce.value+'&origen='+document.forms.forma.wemp_pmla.value+'&wdbmhos='+document.forms.forma.wbasedato.value+'&wformulario='+formTipoOrden+'&wcedula='+document.forms.forma.wcedula.value+'&wtipodoc='+document.forms.forma.wtipodoc.value+"&wemp_pmla="+document.forms.forma.wemp_pmla.value;
					
					//window.open(urlform,'_blank');
					$.blockUI({ message: $('<iframe src="'+urlform+'" width="950px" height="95%" scrolling="yes" frameborder="0" align="center"></iframe><div align="center"><input type="button" name="cerrarvtnhce" id="cerrarvtnhce" onClick="cerrarFormHCE('+cuentaExamenes+',\''+document.forms.forma.wbasedatohce.value+'\',\''+formTipoOrden+'\',\''+historia+'\',\''+ingreso+'\')" value="Cerrar ventana" /></div>'),
								css: {
									top:  '5%', 
									left: '10%', 
									width: '950px',
									height: '90%',
									overflow: 'auto',
									cursor: 'auto'
								}
					});
					
				}
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}	

	//Autocomplete para Ayudas y procedures
    if(document.getElementById('wnomproc')){
    	
		$("#wnomproc").flushCache();
    	$("#wnomproc").unbind("result");
	
		// 2012-06-27
		// Se comenta y se reemplaza por la siguiente línea debido a que estaba trayende 2 valores por cada selección que se hacia
		// Cuando se desplegaba las opciones y se seleccionaba y daba 'Enter' en una, se agregaba ésta y la anterior a las ordenes
		// Esto pasaba cuando se daba 'Enter', no pasaba si se hacia clic
		// $("#wnomproc").setOptions({url:"ordenesidc.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value});
    	$("#wnomproc").setOptions({url:"ordenesidc.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&especialidad="+document.forms.forma.wespecialidad.value});
    	
    	$("#wnomproc").autocomplete("ordenesidc.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value, {
    		cacheLength:0,
			max: 100,
    		scroll: false,
    		scrollHeight: 500,
    		matchContains: true,
    		width:500,
    		minChars: 3,
    		autoFill:false,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {
//    		seleccionarAyudaDiagnostica(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria);
    		//var vecItem = item.split(",");
    		seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);
			
    		this.onfocus = function(){
    			if( justificacionUltimoExamenAgregado ){
    				justificacionUltimoExamenAgregado.focus();
    				justificacionUltimoExamenAgregado = '';
    			}
    		};
//    		this.focus();
//    		this.select();

			document.getElementById("btnCerrarVentana").style.display = 'none';

    	});
    }
}

function autocompletarParaConsultaDiagnosticasAlta(){
	
	//Autocomplete para Ayudas y procedures
    if(document.getElementById('wnomprocimp')){
    	
		$("#wnomprocimp").flushCache();
    	$("#wnomprocimp").unbind("result");
	
		// 2012-06-27
		// Se comenta y se reemplaza por la siguiente línea debido a que estaba trayende 2 valores por cada selección que se hacia
		// Cuando se desplegaba las opciones y se seleccionaba y daba 'Enter' en una, se agregaba ésta y la anterior a las ordenes
		// Esto pasaba cuando se daba 'Enter', no pasaba si se hacia clic
		// $("#wnomprocimp").setOptions({url:"ordenesidc.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value});
    	$("#wnomprocimp").setOptions({url:"ordenesidc.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&especialidad="+document.forms.forma.wespecialidad.value});
    	
    	$("#wnomprocimp").autocomplete("ordenesidc.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicioImp').options[ document.getElementById('wselTipoServicioImp').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value, {
    		max: 100,
    		scroll: false,
    		scrollHeight: 500,
    		matchContains: true,
    		width:500,
    		minChars: 3,
    		autoFill:false,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {
//    		seleccionarAyudaDiagnostica(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria);
    		//var vecItem = item.split(",");
    		
    		seleccionarAyudaDiagnosticaAlta(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);
			
    		this.onfocus = function(){
    			if( justificacionUltimoExamenAgregado ){
    				justificacionUltimoExamenAgregado.focus();
    				justificacionUltimoExamenAgregado = '';
    			}
    		};
//    		this.focus();
//    		this.select();

			document.getElementById("btnCerrarVentana").style.display = 'none';

    	});
    }
}


/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function cerrarModalHCE(){
	window.parent.parent.parent.$.unblockUI();
}
/******************************************************************************************************************************
 *Redirecciona a la pagina inicial del kardex
 ******************************************************************************************************************************/
function inicio(){
	var nroDocumento = document.forms.forma.wcedula.value;
	var tipoDocumento = document.forms.forma.wtipodoc.value;
	
	document.location.href='ordenesidc.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&wcedula='+nroDocumento+'&wtipodoc='+tipoDocumento+'&wfecha='+document.forms.forma.wfecha.value;	
}
/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function agregarSeccionAcordeon(idAcordeon){
	$('#accordion').append('<h3><a href="#">Laboratorio</a></h3><div><p>Hemoclasificacion</p></div>').accordion('destroy').accordion();
	
}
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function abrirModalHCE(){ 
	var titulo = "Vistas asociadas";
	var nombre = "modalOrdenes";
	
	if(parent.parent.demograficos.document.all.txtformulario){
		parent.parent.demograficos.document.all.txtformulario.value = "ORDENES MEDICAS";
	}
	
	//Parametros que se requieren en la URL
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;	
	var empresa = document.forms.forma.wempresa.value;
	
	var nroDocumento = document.forms.forma.wcedula.value;
	var tipoDocumento = document.forms.forma.wtipodoc.value;
	
	var url = "http://localhost/matrix/hce/procesos/HCE.php?accion=W2&ok=0";
	
	url += "&empresa="+empresa;
	url += "&wcedula="+nroDocumento;
	url += "&wtipodoc="+tipoDocumento;
	url += "&whis="+historia;
	url += "&wing="+ingreso;
	url += "&wtitframe=no";
	
	var alto = "400";
	var ancho = "400";
	
	mostrarFlotante(titulo,nombre,url,alto,ancho);
	
}
/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function cerrarModalHCE(){
	window.parent.parent.parent.$.unblockUI();
}
/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function deshabilitarBotonesFirmaDigitalHCE(estado){
//	document.getElementById('wcconf').disabled = estado;
	document.getElementById('wconfdisp').disabled = estado;
	//document.getElementById('btnGrabar1').disabled = estado;
	
    if (estado) {
        $('#btnGrabar1').hide();
        $('#btnGrabarAux').show();
    } else {
        $('#btnGrabar1').show();
        $('#btnGrabarAux').hide();
    }   
	//document.getElementById('btnGrabar2').disabled = estado;
	
	if(estado){
		document.getElementById('whfirma').value = 'N';
	} else {
		document.getElementById('whfirma').value = 'S';
	}
}
/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function validarFirmaDigitalHCE(){
	var firma = document.getElementById('pswFirma').value;
	var usuario = document.forms.forma.usuario.value;
	
	deshabilitarBotonesFirmaDigitalHCE(true);
//	debugger;
	if(firma != "" && firma.length > 0 ){
		
		firma = hex_sha1(firma);
		
		//Consulta ajax para validacion de firma digital
		validarFirmaDigital(usuario,firma);
	} else {
		document.getElementById('tdEstadoFirma').className = 'fondorojo';
		document.getElementById('tdEstadoFirma').innerHTML = "Sin firma digital";
		
		deshabilitarBotonesFirmaDigitalHCE(true);
	}
}
/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function consultarAyudasDiagnosticas(){
	var contenedor = document.getElementById('cntExamenes');
	var unidadRealiza = document.getElementById('wservexamen');
	var nombreAyuda = document.getElementById('wnomayu');
	
	var vecServicio = unidadRealiza.value.split('|');

	var parametros = "consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre="+nombreAyuda.value+"&unidadRealiza="+vecServicio[0]+"&especialidad="+document.forms.forma.wespecialidad.value; 

		try{
			$.blockUI({ message: $('#msjEspere') });
			ajax=nuevoAjax();
		
			ajax.open("POST", "ordenesidc.inc.php",true);
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
 * 
 ******************************************************************************************************************************/
function seleccionarAyudaDiagnostica(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria, requiereJustificacion, noPos, generaOrdenIndividual, aydJustificacion ){

	
	//Busco si ya existe el tipo de orden
	var auxTipoOrden = "del"+centroCostos;
	
	if( $( "[id^="+auxTipoOrden+"]" ).length > 0 && generaOrdenIndividual != 'on' ){
		//Si Existe tipo de orden la creo
		consecutivoOrden = $( "[id^="+auxTipoOrden+"]" ).eq( 0 ).attr( "id" ).substr( auxTipoOrden.length );
	}
	

	justificacionUltimoExamenAgregado = '';
	
	var esHospitalario = esAyudaHospitalaria == "on" ? true : false;
	
	document.getElementById('wnomproc').value = '';

	var iHTMLExamen = "";
	
	var tablaContenedora = document.getElementById( "examPendientes" );

	//Verifico que exista la unidad diagnostica
	var elementoCentroCostos = document.getElementById("detExamenes"+centroCostos);
	var elementoOrden = document.getElementById("del"+centroCostos+""+consecutivoOrden);
	var contenedor = document.getElementById("cntOtrosExamenes");
	
	var crearOrden = false;
	var crearCco = false;
	var crearTabla = false;
	var crearExamen = true;
	
	//Existe centro costos
	if(!document.getElementById("detExamenes"+centroCostos)){
		crearCco = true;
	}
	
	//Existe orden
	if( generaOrdenIndividual != 'on' )
	{
		if(!document.getElementById("del"+centroCostos+""+consecutivoOrden)){
			crearOrden = true;
		}
	}
	else
	{	
		//Se comenta el while ya que estaba creando un bucle infinito cuando la procedimiento genera orden individual. Jonatan Lopez 23 Abril 2014. 
		//while( document.getElementById("del"+centroCostos+""+consecutivoOrden) ){
		  // consecutivoOrden++;
			crearOrden = true;
		//}
	}
	
	
	if(elementoCentroCostos){
		if(!elementoOrden){
			crearOrden = true;
			crearExamen = true;
		}
	} else {
		crearCco = true;
		crearOrden = true;
		crearExamen = true;
	}

	//CREACION DE LOS ELEMENTOS
	if(!crearOrden){
		iHTMLOrden = "";
	}
	if(!crearExamen){
		iHTMLExamen = "";
	}
	
	if(crearCco){
		var subContenedor = document.createElement("tbody");
		subContenedor.setAttribute('id','detExamenes'+centroCostos);
		
		//Link
		if(esHospitalario){
			//subContenedor.innerHTML += "<br><a href='#null' onClick='javascript:intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>HOSPITALARIAS</u></center></font></b></a>";
		} else {
			//subContenedor.innerHTML += "<br><a href='#null' onClick='javascript:intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>"+nombreCentroCostos+"</u></center></font></b></a>";
		}
		
		iHTMLOrden = "";
		iHTMLExamen = "";
		
		//Concatenar examenes
		//var texto = "<div id='"+centroCostos+"'><br>"+tablaContenedora+"<tr id=del"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+iHTMLExamen+"</table>"+"</div>";			
		var texto = "<tr id='trEx"+cuentaExamenes+"'><td id=del"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+iHTMLExamen+"</td>"+"</tr>";			
		
		//Clausura de span
		subContenedor.innerHTML += texto;
		subContenedor.innerHTML = "";
		
		tablaContenedora.appendChild(subContenedor);
	} else {
		
		if(crearOrden){
			//document.getElementById(centroCostos).innerHTML += iHTMLOrden;
		}
		
		if(false && crearTabla){
			//document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += tablaContenedora+iHTMLExamen;
		} else {
			//document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += iHTMLExamen;
		}
	}
	
	try{
		$( elementoCentroCostos ).show();
	}
	catch(e){}
	//$("#acExamenes").accordion( "activate" , 1 )
	
	//Creo el examen en la tabla correspondiente
	agregarExamen(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, 0, crearOrden,requiereJustificacion, noPos, aydJustificacion );

	document.getElementById("btnCerrarVentana").style.display = 'none';
	
	return false;
}

function seleccionarAyudaDiagnosticaAlta(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria, requiereJustificacion, noPos, aydJustificacion ){

	justificacionUltimoExamenAgregado = '';
	
	var esHospitalario = esAyudaHospitalaria == "on" ? true : false;

	var iHTMLExamen = "";

	var tablaContenedora = document.getElementById( "examPendientesImp" );

	//Verifico que exista la unidad diagnostica
	var elementoCentroCostos = document.getElementById("detExamenes"+centroCostos+"Imp");
	var elementoOrden = document.getElementById("delImp"+centroCostos+""+consecutivoOrden);
	var contenedor = document.getElementById("cntOtrosExamenes");
	
	var crearOrden = false;
	var crearCco = false;
	var crearTabla = false;
	var crearExamen = true;
	
	//Existe centro costos
	if(!document.getElementById("detExamenes"+centroCostos+"Imp")){
		crearCco = true;
	}
	
	//Existe orden
	if(!document.getElementById("delImp"+centroCostos+""+consecutivoOrden)){
		crearOrden = true;
	}
	
	//Existe tabla contenedora
	// se comenta porque la tabla contenedora siempre va a existir
	/*
	if(!document.getElementById("detExamenes"+centroCostos+""+consecutivoOrden)){
		crearTabla = true;
	}
	*/
	
	if(elementoCentroCostos){
		if(!elementoOrden){
			crearOrden = true;
			crearExamen = true;
		}
	} else {
		crearCco = true;
		crearOrden = true;
		crearExamen = true;
	}

	//CREACION DE LOS ELEMENTOS
	if(!crearOrden){
		iHTMLOrden = "";
	}
	if(!crearExamen){
		iHTMLExamen = "";
	}
	
	if(crearCco){
		var subContenedor = document.createElement("tbody");
		subContenedor.setAttribute('id','detExamenes'+centroCostos+"Imp");
		
		//Link
		if(esHospitalario){
			//subContenedor.innerHTML += "<br><a href='#null' onClick='javascript:intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>HOSPITALARIAS</u></center></font></b></a>";
		} else {
			//subContenedor.innerHTML += "<br><a href='#null' onClick='javascript:intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>"+nombreCentroCostos+"</u></center></font></b></a>";
		}
		
		iHTMLOrden = "";
		iHTMLExamen = "";
		
		//Concatenar examenes
		//var texto = "<div id='"+centroCostos+"'><br>"+tablaContenedora+"<tr id=del"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+iHTMLExamen+"</table>"+"</div>";			
		var texto = "<tr id='trExImp"+cuentaExamenes+"'><td id=delImp"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+iHTMLExamen+"</td>"+"</tr>";			
		
		//Clausura de span
		subContenedor.innerHTML += texto;
		
		tablaContenedora.appendChild(subContenedor);
	} else {
		
		if(crearOrden){
			//document.getElementById(centroCostos).innerHTML += iHTMLOrden;
		}
		
		if(false && crearTabla){
			//document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += tablaContenedora+iHTMLExamen;
		} else {
			//document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += iHTMLExamen;
		}
	}
	
	try{
		$( elementoCentroCostos ).show();
	}
	catch(e){}
	//$("#acExamenes").accordion( "activate" , 1 )
	
	//Creo el examen en la tabla correspondiente
	agregarExamenAlta(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, 0, crearOrden,requiereJustificacion, noPos, aydJustificacion );

	document.getElementById("btnCerrarVentana").style.display = 'none';
	
	return false;
}

/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function cancelarOrden(centroCostos,consecutivoOrden, cmp ){
	consecutivoOrden = cmp.parentNode.id.substr( centroCostos.length+3 );
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;
	
	if( confirm("Esta seguro de cancelar la orden "+consecutivoOrden+"?")){
		cancelarOrdenElemento(historia,ingreso,fecha,usuario,centroCostos,consecutivoOrden);
	}	
}

/*****************************************************************************************************************************
 * Intercala ordenes desplegadas ya desplegar
 ******************************************************************************************************************************/
function intercalarOrdenAnterior(idElemento)
   {
    //$("#"+idElemento).toggle("normal");
    
    if ( document.getElementById(idElemento).style.display=='')
       {
    	document.getElementById(idElemento).style.display='none';
       }
      else
        {
	     document.getElementById(idElemento).style.display='';
        }
    
    //<!--$("#ex"+idElemento).toggle("normal"); -->
   }  


/*****************************************************************************************************************************
 * Efecto accordion en examenes realizados
 ******************************************************************************************************************************/
   function recarga()   
     {
	  var dvAux = document.createElement( "div" );

	  dvAux.innerHTML = "<INPUT type='hidden' name='mostrar'>";
      dvAux.firstChild.value = document.getElementById( "Ordenes" ).innerHTML;
      document.forms[0].appendChild( dvAux.firstChild );
      document.forms[0].submit();    
     }      


/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function grabarOrden(centroCostos,consecutivoOrden){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;
	var observacionesOrden = "";  //document.getElementById("wtxtobsexamen"+centroCostos+""+consecutivoOrden).value;
	
	grabarOrdenElemento(historia,ingreso,fecha,usuario,centroCostos,consecutivoOrden,observacionesOrden);
}
/*****************************************************************************************************************************
 * Intercala cualquier elemento en pantalla
 ******************************************************************************************************************************/
function intercalarElemento(idElemento){
	$("#"+idElemento).toggle("normal");
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
			if(filaIdAnt != "" && document.getElementById(filaIdAnt).display != 'none')
			{
				// Se oculta la fila desplegada anteriormente
				document.getElementById(filaIdAnt).style.display = 'none'; 

				// Se reestablece la clase original de la fila anterior
				document.getElementById(filaTituloIdAnt).className = claseFilaAnt;
			}

			if(divIdAnt != "" && document.getElementById(divIdAnt).display != 'none')
			{
				// Se oculta el div desplegado anteriormente
				document.getElementById(divIdAnt).style.display = 'none';
			}
			

	$( document.getElementById( "med%"+idElemento ) ).toggle("normal");
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
			document.location.href = 'ordenesidc.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha+'&whgrabado='+whgrabado.value;
		} else {
			document.location.href = 'ordenesidc.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha;
		}
	} else {
		alert("No se encontró historia, ingreso y fecha en los parametros de entrada.");		
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
 * Este metodo encapsula la creación dinámica de cada campo, segun las acciones asociadas y su tipo.
 * 
 * Orden de la serializacion de acciones:
 * 
 * 1. Leer
 * 2. Actualizar
 * 3. Borrar
 * 4. Crear
 * 
 * ::::::::::::::Parámetros::::::::::::::
 * String Tipo		: Permite evaluar el tipo de campo se va a generar
 * String Id		: Id y name especificos del campo
 * String idAcion	: Id del campo hidden que contiene la secuencia del CRUD
 * Array atributos	: Atributos del campo:  onClick, readonly, class...
 * String valor		: Valor del campo
 * 
 * El valor de retorno es el innerHTML
 *****************************************************************************************************************************/
function crearCampo(tipo,id,idAccion,atributos,valor){
	var acciones = "S,S,S,S";
	var elemento = "";
	var clave = "";
	var vecAcciones = new Array();

	//Vector de acciones
	var wacciones = document.getElementById("wacc"+idAccion);
	if(wacciones){
		vecAcciones = wacciones.value.split(",");
	} else {
		vecAcciones = acciones.split(",");
	}
	
	switch(tipo){
		case '1':		//Texto
			elemento = "<input type=text name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += " value=\""+valor+"\" ";
			
			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " readonly=readonly ";
			}
			
			elemento += "/>";
			break;
		case '2':		//Textarea
			elemento = "<textarea name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " readonly=readonly ";
			}
			
			//Valor
			elemento += ">"+valor+"";
			elemento += "</textarea>";
			
			break;
		case '3':		//Boton
			elemento = "<input type='button' name='"+id+"' id='"+id+"' ";
			
			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += " value='"+valor+"' ";
				
			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " disabled ";
			}
			elemento += "/>";
			
			break;
		case '4':		//Link
			elemento = "<a href='#null' name='"+id+"' id='"+id+"' ";
			
			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += ">"+valor+"";
			elemento += "</a>";
				
			//Lectura
			if(vecAcciones[0] == "N"){
				elemento = " ";
			}
			break;
		case '5':		//Checkbox
			elemento = "<input type=checkbox name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += " value=\""+valor+"\" ";
			
			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " disabled ";
			}
			
			elemento += "/>";
			break;
		case '6':		//Select
			elemento = "<select name='"+id+"' id='"+id+"' ";
			
			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " disabled ";
			}
			
			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			elemento += ">";
			
			//Valor
			elemento += " "+valor+" ";
			elemento += "</select>";
			
			break;
		case '7':		//Rama de arbol
			break;
		case '8':		//Link que al usarse en modo lectura sale unicamente el contenido del valor
			elemento = "<a href='#null' name='"+id+"' id='"+id+"' ";
			
			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += ">"+valor+"";
			elemento += "</a>";
				
			//Lectura
			if(vecAcciones[0] == "N"){
				elemento = valor;
			}
			break;
	}
	return elemento;
}
/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
function filtroAcciones(idAccion,funcion){
	var acciones = "S,S,S,S";
	var elemento = "";
	var clave = "";
	var vecAcciones = new Array();
	var retorno = "false";

	//Vector de acciones
	var wacciones = document.getElementById("wacc"+idAccion);
	if(wacciones){
		vecAcciones = wacciones.value.split(",");
	} else {
		vecAcciones = acciones.split(",");
	}
	
	//Lectura
	if(vecAcciones[0] == "S"){
		retorno = eval(funcion);
	}
	
	return retorno;
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
/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
function consultarKardex(){
	var nroDocumento = document.forms.forma.wcedula.value;
	var tipoDocumento = document.forms.forma.wtipodoc.value;
	
	var esFechaValida = esFechaMenorIgualAActual(document.forms.forma.wfecha.value);
	var whgrabado = document.getElementById("whgrabado");

	//Digitó historia
	if(!nroDocumento || nroDocumento == ''){
		alert("Debe especificar un número de documento de paciente válido.");
		return;
	} 

	if(esFechaValida){
		var boton = document.getElementById("btnConsultar");
		
		if(boton){
			boton.disabled = true;
			boton.value = "Procesando, por favor espere...";
		}
		
		if(whgrabado && whgrabado.value != ''){
			document.location.href = 'ordenesidc.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&wcedula='+nroDocumento+'&wtipodoc='+tipoDocumento+'&wfecha='+document.forms.forma.wfecha.value+'&whgrabado='+whgrabado.value;
		} else {
			document.location.href = 'ordenesidc.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&wcedula='+nroDocumento+'&wtipodoc='+tipoDocumento+'&wfecha='+document.forms.forma.wfecha.value;
		}
	} else {
		alert("La fecha ingresada debe ser igual o anterior a la fecha actual");
	}
}
/*****************************************************************************************************************************
 *Detecta el cierre de la ventana del kardex para evitar que se pierdan los datos
 ******************************************************************************************************************************/
function salida(){
	if(true || document.getElementById("fixeddiv2")){
		if(true || !document.getElementById( "wcconf" ).checked){
//			alert( "Se grabará y cerrará la orden, tenga en cuenta que este NO HA SIDO confirmada." );
		}
		
		// grabarKardex();
		salir_sin_grabar();
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
		var year1 = date1.getFullYear();
		var year2 = date2.getFullYear();
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
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicio'+tipoProtocolo+idx,button:'btnFecha'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});
}

function calendarioimp(idx,tipoProtocolo){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicioimp'+tipoProtocolo+idx,button:'btnFechaimp'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});
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
	
	return false;
}
function calendario3imp(idx){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfsolimp'+idx,button:'btnFechaSolImp'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',dateStatusFunc:fechasDeshabilitadas});
	
	return false;
}

/*****************************************************************************************************************************
 * Invocacion generica del calendario para la fecha de realización examen
 ******************************************************************************************************************************/
function calendario4(idx){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfliq'+idx,button:'btnFechaLiq'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',dateStatusFunc:fechasDeshabilitadas});	
}

function calendario5(idx,tipoProtocolo){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicioaplicacion',button:'btnFecha'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});
}

function calendario6(idx,tipoProtocolo){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicioaplicacionimp',button:'btnFechaimp'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});
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
	var now = new Date();
	var desactivar = false;
	var comparacion = compareDatesOnly(now, date);
	
	//Limita que no se seleccionen dias anteriores
	if (comparacion < 0) {
		desactivar = true;
	}
	
	//Limita que no se seleccionen horas anteriores
	if (typeof(hours) != "undefined") {
		if ((hours <= now.getHours()) && comparacion < 0) {
			desactivar = true;
		}
	}
	
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
 * ---ARBOL 1---
 * 1.Sondas cateteres y drenes  - Codigo 01 	
 * 2.Cuidados de enfermeria		- Codigo 02
 * 3.Aislamientos				- Codigo 03
 * 4.Curaciones					- Codigo 04
 * 5.Terapias					- Codigo 05
 * 6.Interconsultas				- Codigo 06
 * ---ARBOL 2---
 * 7.Medidas generales			- Codigo 07
 * 8.Decisiones					- Codigo 08
 * 9.Procedimientos				- Codigo 09
 *
 ******************************************************************************************************************************/
function seleccionHojaAccionesComplementarias(codigo,descripcion,objArbol){	
	
	var fechaActual = new Date();
	diaActual = fechaActual.getDate();
	mesActual = fechaActual.getMonth() + 1;
	anioActual= fechaActual.getFullYear();
	
	fechaActual = anioActual+"-"+mesActual+"-"+diaActual;
		
	switch(objArbol.identificador){
		case 1:
			//Blanquear color del foco
			document.getElementById("txCuidados").style.background = "#FFFFFF";
			
			var elemento = "";
			var nodos = codigo.substring(1).split("-");
			var separador = "*";
			
			switch (nodos[0]){
			case '02':
				elemento = document.getElementById("txCuidados");
				break;
			default:
				break;
			}
			
			if(elemento){
				//Resalto el color de fondo del elemento
				elemento.style.background = "#CCFFCC";
				
				var separadorFecha = "******"+fechaActual+"*******";
				if(elemento.value.indexOf(separadorFecha)==-1){
					elemento.value += "\n\r" + separadorFecha;
				}

				if(elemento.value.indexOf(descripcion)==-1){
					elemento.value += "\n\r" + separador + descripcion;
				}
			}
			break;
		case 2:
			
			//Blanquear color del foco
			document.getElementById("txtSondas").style.background = "#FFFFFF";
			document.getElementById("txMedidas").style.background = "#FFFFFF";
			document.getElementById("txtConsentimientos").style.background = "#FFFFFF";
			document.getElementById("txProcedimientos").style.background = "#FFFFFF";
			document.getElementById("txAislamientos").style.background = "#FFFFFF";
			document.getElementById("txTerapia").style.background = "#FFFFFF";
			document.getElementById("txtInterconsulta").style.background = "#FFFFFF";
			document.getElementById("txtCuraciones").style.background = "#FFFFFF";
			
			var elemento = "";
			var nodos = codigo.substring(1).split("-");
			var separador = "*";
			
			switch (nodos[0]){
			case '01':
				elemento = document.getElementById("txtSondas");
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
			case '07':
				elemento = document.getElementById("txMedidas");
				break;
			case '08':
				elemento = document.getElementById("txtConsentimientos");
				break;
			case '09':
				elemento = document.getElementById("txProcedimientos");
				break;
			default:
				break;
			}
			
			if(elemento){
				//Resalto el color de fondo del elemento
				elemento.style.background = "#CCFFCC";

				var separadorFecha = "******"+fechaActual+"*******";
				if(elemento.value.indexOf(separadorFecha)==-1){
					elemento.value += "\n\r" + separadorFecha;
				}

				if(elemento.value.indexOf(descripcion)==-1){
					elemento.value += "\n\r" + separador + descripcion;
				}
			}
			break;
	}
}
/*****************************************************************************************************************************
 * Seleccion de un componente para adicionarse a la infusión
 ******************************************************************************************************************************/
function expandirRama(objeto,codigo){
	
	//---Arbol 1
	//Blanquear color del foco
	if(document.getElementById("txtSondas")) document.getElementById("txtSondas").style.background = "#FFFFFF";
	if(document.getElementById("txCuidados")) document.getElementById("txCuidados").style.background = "#FFFFFF";
	if(document.getElementById("txAislamientos")) document.getElementById("txAislamientos").style.background = "#FFFFFF";
	if(document.getElementById("txtCuraciones")) document.getElementById("txtCuraciones").style.background = "#FFFFFF";
	if(document.getElementById("txTerapia")) document.getElementById("txTerapia").style.background = "#FFFFFF";
	if(document.getElementById("txtInterconsulta")) document.getElementById("txtInterconsulta").style.background = "#FFFFFF";
	
	//---Arbol 2
	//Blanquear color del foco
	if(document.getElementById("txMedidas")) document.getElementById("txMedidas").style.background = "#FFFFFF";
	if(document.getElementById("txtConsentimientos")) document.getElementById("txtConsentimientos").style.background = "#FFFFFF";
	if(document.getElementById("txProcedimientos")) document.getElementById("txProcedimientos").style.background = "#FFFFFF";

	var elemento = document.getElementById(codigo);
	
	if(elemento){
		var hijos = elemento.children;
		var imagen = hijos[0];

		objeto.style.background = "#CCFFCC";
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
 * Apertura del movimiento de examenes
 ******************************************************************************************************************************/
function movimientoExamenes(){
	limpiarBuscadorExamenes();
	return movExamenes.show();
}
/*****************************************************************************************************************************
 * Seleccion de un componente para adicionarse a la infusión
 ******************************************************************************************************************************/
function seleccionarComponente(codigo, nombre){
//	debugger;
	if(cuentaInfusiones > 0){
		var idx = cuentaInfusiones-1;
		var elemento = document.getElementById("wtxtcomponentes"+idx);
		if(elemento){		
			alert( "codigo: "+codigo+"   nombre: "+nombre );
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
		alert('Debe crear una nueva orden de programa antes');
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
 *Consulta las historias y habitaciones de acuerdo a un servicio
 ******************************************************************************************************************************/
function consultarHabitaciones()
{
	var contenedor = document.getElementById('cntHabitacion');
	var parametros = ""; 
				
	parametros = "consultaAjaxKardex=25&basedatos="+document.forms.forma.wbasedato.value+"&servicio=" + document.getElementById('wsservicio').value+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 
		
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",true);
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
 ******************************************************************************************************************************/
function validarFirmaDigital(usuario,firma){
	var contenedor = document.getElementById('tdEstadoFirma');
	var parametros = ""; 
	parametros = "consultaAjaxKardex=26&basedatos="+document.forms.forma.wbasedato.value+"&usuarioHce=" + usuario + "&firma=" + firma+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 
	try{
		//$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				var clase = "";
				var mensaje = "";
				var campoFirma = "";
				
				if(ajax.responseText != ''){
					
					switch(ajax.responseText){
						case '1':
							clase = "fondoVerde";
							campoFirma = firma;
							mensaje = "Firmado digitalmente";
							
							deshabilitarBotonesFirmaDigitalHCE(false);
							break;
						case '2':
							clase = "fondoRojo";
							campoFirma = "";
							mensaje = "Firma no reconocida";
							break;
						default:
							clase = "fondoRojo";
							campoFirma = "";
							mensaje = "Sin firma digital";
							break;
					}
				
					//Resultado
					contenedor.className = clase;					
					contenedor.innerHTML = mensaje;
					//document.getElementById('pswFirma').value = campoFirma;
					document.getElementById('whfirma').value = campoFirma;
				}
			} 
			//$.unblockUI();
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
		
		ajax.open("POST", "ordenesidc.inc.php",true);
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
 * Consulta el esquema dextrometer seleccionado
 *****************************************************************************************************************************/
function consultarEsquemaInsulina(){
	
	var codEsquema = document.getElementById("wdexesquema");
	var codInsulina = document.getElementById("wdexins");
	var codFrecuencia = document.getElementById("wdexfrecuencia");
	
	if(true || codEsquema && codEsquema.value && codFrecuencia && codFrecuencia.value){
//		if(document.getElementById("btnEsquema")){
//			document.getElementById("btnEsquema").disabled = false;
//		}
		consultarEsquemaInsulinaElemento(codEsquema.value);
		
		document.getElementById("btnQuitarEsquema").disabled = false;
	} 
//	else {
//		document.getElementById("btnEsquema").disabled = true;
//	}
	if(codEsquema && codEsquema.value == ""){
		
		quitarEsquemaDextrometer();
		
		document.getElementById("cntEsquema").innerHTML = "";
		document.getElementById("cntEsquemaActual").innerHTML = "";
	}
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
	
		/*
		if(!frecuencia || frecuencia.value == '' && !codEsquema || codEsquema.value == '' && !codArticulo || codArticulo.value == ''){
			return;
		}
	
		if(!frecuencia || frecuencia.value == ''){
			alert("Debe especificar una frecuencia");
			return;
		}
		*/
	
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
	
	//Junio 13 de 2012, quito esta validacion
	// if(!frecuencia || frecuencia.value == ''){
		// alert("No se ha especificado una frecuencia o condicion");
		// return
	// }
	
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
function seleccionarMedicamento(codigo, nombre, origen, grupo, forma, unidad, pos, unidadFraccion, cantFracciones, vencimiento, diasVencimiento, dispensable, duplicable, diasMaximos, dosisMaximas, via, tipoProtocolo, noEnviar ){
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
	var cntDetalleKardex = document.getElementById("detKardeximp" + tipoProtocoloAux);
	
	var idTabla = "tbDetalle" + tipoProtocoloAux;
	
//	stopEvent(window.onbeforeunload);
	
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
						elemento.value = codigo + "-" + nombre.replace(/_/g," ") + " (de control)";
					} else {
						elemento.value = codigo + "-" + nombre.replace(/_/g," ");
					}
					
					
					// Se establece si la cadena msjNoPos ya tiene el codigo del artículo actual
					var avisoNoPos = msjNoPos.indexOf(codigo)
					msjNoPos += codigo+',';
					
					//No pos
					if(avisoNoPos == -1 && pos == 'N'){
						alert("Este medicamento es NO POS");
					}
					
					//Vence?
//					if(vence){
//						alert("vence");
//					}
					
					//Si no tiene fracciones en la unidad de manejo ni unidad de fraccion se avisa
					if( (unidadFraccion == '' || cantFracciones == '') && !esLiquidoEndovenoso){
						alert('El articulo con codigo ' + codigo + ' no tiene unidad de fraccion o fracciones por unidad de manejo.  Por favor notifique a servicio farmaceutico.');
					}
					
					if(origen != 'CM' && document.getElementById("wchkconf"+tipoProtocoloAux+idx)){
						document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = true;
					} else {
						if(document.getElementById("wchkconf"+tipoProtocoloAux+idx))
							document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = false;
					}
					
					//Abril 25 de 2011
					//No enviar
					if( noEnviar == 'on' ){
						if(document.getElementById("wchkdisp"+tipoProtocoloAux+idx)){
							document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = true;
						}
					}
					else{
						if(document.getElementById("wchkdisp"+tipoProtocoloAux+idx)){
							document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = false;
						}
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
						var opcionTmp = null;

						while(cont1 < opcionesMaestro.length){
							opcionTmp = document.createElement("option");		
							if(via.indexOf(opcionesMaestro[cont1].value) != -1){
								document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
							} 
							
							opcionTmp.innerText = opcionesMaestro[cont1].text;
							opcionTmp.value = opcionesMaestro[cont1].value;
							
							cont1++;
						}
					} else {
						var opcionesMaestro = document.getElementById("wmviaadmon").options;			
						
						var cont1 = 0;
						var splVia = via.split(",");
						var opcionTmp = null;

						while(cont1 < opcionesMaestro.length){
							opcionTmp = document.createElement("option");		
							document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
							
							opcionTmp.innerText = opcionesMaestro[cont1].text;
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
function grabarKardex(wimprimir){

		if(!wimprimir)
			wimprimir = "";
		
		grabando=true;
		var valido = true;
		var mensaje = '';
		
		//Busco si hay cambios en los CTC
		//Si hay cambio en alguno se debe mostrar nuevamente el CTC al medico para que lo revise
		var x = '';
		
		var mostrarCTC = false;
	    for( x in arCTCArticulos ){
		   //document.getElementById("wnmmed"+tipoProtocolo+cont1).value
		   //document.getElementById("wnmmed"+tipoProtocolo+cont1).value.split( "-" )[0]
		   if( buscarCambiosCTC( x ) ){
			   //Se deja quemado los ultimos dos campo, ya que con los últimos cambios no se requieren
			   strPendientesCTC += 'articulo|'+x+'|N|0\r\n';
			   mostrarCTC = true;
		   }
	    }
	   
	    if( mostrarCTC ){
		   abrirCTCMultiple();
		   return;
	    }
		
		//		window.onbeforeunload = '';
		try{
			if( $( "[name=editable]" ).val() != 'off' ){
				
				//Validacion de firma electronica
				if(document.getElementById('pswFirma')){
					if(document.getElementById('pswFirma').value != '' && $( "#tdEstadoFirma" ).hasClass( "fondoVerde" ) ){
						valido = true;
					} else {
						valido = false;
						mensaje = 'No se puede grabar la orden sin firma digital.';
					}
				}
			}
			else{
				valido = false;
			}
		}
		catch(e){
			valido = false;
		}
	
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
			
			var indicaciones = '';
			if(isset(document.getElementById("windicaciones")))
				indicaciones = document.getElementById("windicaciones").value
			
			//El encabezado del kardex se encuentra dividido en las pestañas
			var pestanasActivas = document.getElementById("hpestanas").value;
			
			document.forms.forma.action = 'ordenesidc.php?wemp_pmla='+document.forms.forma.wemp_pmla.value
						+'&waccion=c'
						+'&wcedula='+document.forms.forma.wcedula.value
						+'&wtipodoc='+document.forms.forma.wtipodoc.value
						+'&wfecha='+document.forms.forma.wfecha.value
						+'&wfechagrabacion='+document.forms.forma.wfechagrabacion.value
						+'&primerKardex='+document.getElementById("wkardexnuevo").value						
						+'&rutaOrdenMedica='+rutaImagenOrdenMedica
						+'&confirmado='+conf
						+'&firmaDigital='+document.getElementById("whfirma").value
						+'&windicaciones='+indicaciones;
			
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
			
			//Grabacion automatica de las pestanas
//			var arrProtocolos = new Array("%","N","A","U","Q","2","4");
			var arrProtocolos = new Array("N","%","2","4");
			var tipoProtocolo = "", nomContenedor = "", tabla = "";
			var pelo = "",celdas = "", celda = "", componentes = "", modificado = "", limiteIteraciones = "";
			for(var cont2 = 0; cont2 < arrProtocolos.length; cont2++){
				tipoProtocolo = arrProtocolos[cont2];
				//Variador del metodo
				switch (tipoProtocolo){
					case 'N':
					case 'A':
					case 'U':
					case '%':
					case 'Q':
						nomContenedor = "tbDetalle"+tipoProtocolo;
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones = tabla.rows.length;
						}else{
							limiteIteraciones = 0;
						}
						nomContenedor = "tbDetalleAdd"+tipoProtocolo;
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones += tabla.rows.length;
						}
						
						limiteIteraciones = elementosDetalle;
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
						limiteIteraciones = cuentaExamenes;
					break;
				}
				
				//Iteracion por fila
				for(var cont1 = 0; cont1 < limiteIteraciones; cont1++){
					var modificado = document.getElementById("wmodificado" + tipoProtocolo + cont1);
					
					//Variador del metodo
					switch (tipoProtocolo){
						case 'N':
						case 'A':
						case 'U':
						case '%':
						case 'Q':
							if( document.getElementById("wnmmed"+tipoProtocolo+cont1) ){
								if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){ //Articulo existente
									if(document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != null){
										if(document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName == "DIV"){
											if(!filtroAcciones(tipoProtocolo+".15","grabarArticuloSinValidacion('"+cont1+"','"+tipoProtocolo+"')")){
												$.unblockUI();
												grabando=false;
												return;
											}
										} else {
											if(document.getElementById("wnmmed"+tipoProtocolo+cont1) != null && document.getElementById("wnmmed"+tipoProtocolo+cont1) != 'undefined' && document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != "DIV"){
												if(!filtroAcciones(tipoProtocolo+".15","grabarArticulo('"+cont1+"','"+tipoProtocolo+"')")){
													$.unblockUI();
													grabando=false;
													return;
												}
											}	
										}
									}
								} else {  //Articulo nuevo
									if(document.getElementById("wnmmed"+tipoProtocolo+cont1) != null && document.getElementById("wnmmed"+tipoProtocolo+cont1) != 'undefined' && document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != "DIV"){
										if(!filtroAcciones(tipoProtocolo+".15","grabarArticulo('"+cont1+"','"+tipoProtocolo+"')")){
											$.unblockUI();
											grabando=false;
											return;
										}
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
									if(!filtroAcciones(tipoProtocolo+".7","grabarInfusion('"+indiceInfusion+"','"+tipoProtocolo+"')")){
										$.unblockUI();
										grabando=false;
										return;
									}
								}
							}
						break;
						case '4':
							
							if(isset(document.getElementById("wmodificado" + tipoProtocolo + cont1)))
							{
								modificado = document.getElementById("wmodificado" + tipoProtocolo + cont1);
								if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){
									if(!filtroAcciones(tipoProtocolo+".10","grabarExamen('"+cont1+"','"+tipoProtocolo+"')")){
										$.unblockUI();
										grabando=false;
										return;
									}
									// else{
										// grabarExamenADetalle( cont1, tipoProtocolo );
									// }
							 	}
							}
						break;
					}
				}
			}

			/*
			if(wimprimir=='imp')
			{
				grabarIndicaciones(document.getElementById('windicaciones').value, document.forms.forma.wfecha.value, document.forms.forma.whistoria.value, document.forms.forma.wingreso.value);
			}
			*/
			
			grabarExamenADetalle( cont1, tipoProtocolo );
			
			var historia = document.forms.forma.whistoria.value;
			var ingreso = document.forms.forma.wingreso.value;
			
			var parametros = "consultaAjaxKardex=54&basedatos="+document.forms.forma.wbasedato.value
							 +"&whis="+historia
							 +"&wing="+ingreso
							 +"&firmaDigital="+hex_sha1( $( "#pswFirma" ).val() )
							 +"&wemp_pmla="+document.forms.forma.wemp_pmla.value;

			try{
				var ajax=nuevoAjax();
			
				ajax.open("POST", "ordenesidc.inc.php",true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				
				ajax.onreadystatechange=function(){
					if (ajax.readyState==4 && ajax.status==200){
						var respuesta=ajax.responseText;	
						
					} 
				}
				if ( !estaEnProceso(ajax) ) {
					ajax.send(null);
				}
			}catch(e){	}	
		}
		
		$.unblockUI();
		
		var txDiag = '';
		
		//Grabacion automatica del dextrometer
		filtroAcciones("6.5","grabarEsquemaDextrometer()"); 
//		grabarEsquemaDextrometer();

		var tabId = '';

		// Llamo a la ventana de impresión de la orden
		if(wimprimir=='imp' || wimprimir=='impart' || wimprimir=='impexa' || wimprimir=='impctc')
		{
			$( "#tabs > ul > li > a[href]" ).each(
				function()
				{
					if( !$( $( this ).attr( "href" ) ).hasClass( "ui-tabs-hide" ) )
					{
						tabId = $( this ).attr( "href" );
						return;
					}
				}
			)
			
			//setTimeout('',2000);
			if( $( "[name=editable]" ).val() == 'off' || valido )
			{
				
				if( wimprimir != "impctc" )
				{
				
					if( tabId == '#fragment-3' )
					{
						wimprimir = 'impart';
						if( hayProcedimientosImp && confirm( "Quiere imprimir ordenes de procedimientos y examenes?" ) )
						{
							wimprimir = 'imp';
						}
						
						// if( hayProcedimientosImp && confirm( "Quiere imprimir ordenes de procedimientos y examenes?" ) )
						// {
							// wimprimir = 'imp';
						// }
						// else
						// {
							// wimprimir = 'impart';
						// }
					}
					else
					{
						wimprimir = 'imppro';
						if( hayArticulosImp && confirm( "Quiere imprimir también los medicamentos?" ) )
						{
							wimprimir = 'imp';
						}
						
						// if( confirm( "Quiere imprimir también los medicamentos?" ) )
						// {
							// wimprimir = 'imp';
						// }
						// else
						// {
							// wimprimir = 'imppro';
						// }
					}
				}
				else{
				
					if( tabId == '#fragment-3' )
					{
						wimprimir = 'impctcart';
						if( hayProcedimientosCTCImp && confirm( "Quiere imprimir el CTC de ordenes de procedimientos y examenes?" ) )
						{
							wimprimir = 'impctc';
						}
						
						// if( confirm( "Quiere imprimir el CTC de ordenes de procedimientos y examenes?" ) )
						// {
							// wimprimir = 'impctc';
						// }
						// else
						// {
							// wimprimir = 'impctcart';
						// }
					}
					else
					{
						wimprimir = 'impctcpro';
						if( hayArticulosCTCImp && confirm( "Quiere imprimir también el CTC de medicamentos?" ) )
						{
							wimprimir = 'impctc';
						}
						
						// if( confirm( "Quiere imprimir también el CTC de medicamentos?" ) )
						// {
							// wimprimir = 'impctc';
						// }
						// else
						// {
							// wimprimir = 'impctcpro';
						// }
					}
					
				}
			
				abrir_ventana("ordenesidc_imp.php", document.forms.forma.wemp_pmla.value, document.forms.forma.whistoria.value, document.forms.forma.wingreso.value,txDiag,wimprimir+"&wemp_pmla="+document.forms.forma.wemp_pmla.value);
			}
			else
				alert("Debe firmar digitalmente para poder realizar la impresión de a orden");
		}
		else
		{
			if(grabando && valido)
				document.forms.forma.submit();
		}
		
		//Grabacion final
//		alert("Grabar todo");
		//document.location.href = cadena;
}
/*****************************************************************************************************************************
 * Tipo de cambio en la pestaña y seccion
 ******************************************************************************************************************************/
function marcarCambio(tipo,indice, campo ){
	var bandera = document.getElementById("wmodificado"+tipo+indice);
	
	if(bandera && bandera.value){
		bandera.value = "S";
	}
	
	/********************************************************************************
	 * Junio 13 de 2012
	 *
	 * Si una condicicon de suministro tiene dosis maxima por defecto se llena 
	 * la dosis maxima
	 ********************************************************************************/
	 
	agregarDosisMaxPorCondicion( tipo, indice, campo );
	/********************************************************************************/
	
	hayArticulosAImprimir();
}


/*****************************************************************************************************************************
 * Actualiza los medicamentos y procedimientos al estado de listos para impresion
 ******************************************************************************************************************************/
function actualizaImpresion( historia,ingreso,fecha )
{
	var parametros = "";
	
	parametros = "consultaAjaxKardex=42&basedatos="+document.forms.forma.wbasedato.value+"&whis="+historia+"&wing="+ingreso+"&wfec="+fecha+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 

	try{
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var respuesta=ajax.responseText;
				if(respuesta!='1'){
					//alert("No se pudo actualizar el artículo");
				}
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}	
	
}


/*****************************************************************************************************************************
 * Actualiza el estado de alta del articulo enviado
 ******************************************************************************************************************************/
function marcarImpresion( campo,historia,ingreso,articulo,fecha,finicio,hinicio )
{
	var parametros = "";
	var articuloAlta = "off";
	
	/*
	if(campo.checked==true)
		articuloAlta = "on";
	*/
	if(campo=='quitar')
		articuloAlta = "off";
		
	parametros = "consultaAjaxKardex=39&basedatos="+document.forms.forma.wbasedato.value+"&articuloAlta="+articuloAlta+"&whis="+historia+"&wing="+ingreso+"&codigoArticulo="+articulo+"&wfecha="+fecha+"&wfecini="+finicio+"&wfecfin="+hinicio+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 

	try{
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var respuesta=ajax.responseText;	
				if(respuesta!='1'){
					alert("No se pudo actualizar el artículo");
					if(campo.checked == true)
						campo.checked = false;
					else
						campo.checked = true;
				}
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}	
	
}


/*****************************************************************************************************************************
 * Establece si un medicamento es de manejo intero o no
 ******************************************************************************************************************************/
function marcarManejoInterno( campo,historia,ingreso,articulo,fecha,finicio,hinicio )
{
	var parametros = "";
	var articuloInterno = "off";
	
	if(campo.checked==true)
		articuloInterno = "on";
		
	parametros = "consultaAjaxKardex=43&basedatos="+document.forms.forma.wbasedato.value+"&articuloInterno="+articuloInterno+"&whis="+historia+"&wing="+ingreso+"&codigoArticulo="+articulo+"&wfecha="+fecha+"&wfecini="+finicio+"&wfecfin="+hinicio+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 

	try{
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var respuesta=ajax.responseText;	
				if(respuesta!='1'){
					alert("No se pudo actualizar el artículo");
					if(campo.checked == true)
						campo.checked = false;
					else
						campo.checked = true;
				}
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}	
	
}


/*****************************************************************************************************************************
 * Actualiza el estado de alta del articulo enviado
 ******************************************************************************************************************************/
function marcarImpresionExamen( campo,tipo_orden,numero_orden,examen,fecha,contExamenes, item )
{

	var parametros = "";
	//var examenAlta = "off";
	var imprimirExamen = "off";

	if(!item)
		item = $( "#hexnroitem"+campo.id.substr( "imprimir_examen".length ) ).val()*1;
		//item = $( "#hexnroitem"+campo.parentNode.parentNode.id.substr(-1) ).val()*1;
	/*
	if(contExamenes!="")
	{
		var claseImprimir = $('#imgImprimir'+contExamenes).attr('class');
		
		if(claseImprimir == 'opacar aclarar' || claseImprimir == 'aclarar')
			imprimirExamen = "on";
	}
	*/
	
	/*
	if(campo!='' && campo.checked==true)
		examenAlta = "on";
	*/
	
	if(campo!='' && campo.checked==true)
		imprimirExamen = "on";
	
	if(campo=='quitar')
		imprimirExamen = "off";
	
	
	parametros = "consultaAjaxKardex=40&basedatos="+document.forms.forma.wbasedato.value+"&imprimirExamen="+imprimirExamen+"&wcodigo_examen="+examen+"&wfecha="+fecha+"&wtipo_orden="+tipo_orden+"&wnumero_orden="+numero_orden+"&item="+item+"&wemp_pmla="+document.forms.forma.wemp_pmla.value; 

	try{
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var respuesta=ajax.responseText;	
				if(respuesta!='1'){
					alert("No se pudo actualizar la orden");
					if(campo=='quitar')
					{
						$('#'+fila).show();
					}
					else
					{
						cambiarClase('imgImprimir'+contExamenes,'opacar','aclarar');
					}

					/*
					if(campo!='')
					{
						if(campo.checked == true)
							campo.checked = false;
						else
							campo.checked = true;
					}
					*/
				}
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}	
	
	
	hayArticulosAImprimir();
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
	
		ajax.open("POST", "ordenesidc.inc.php",true);
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
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function confirmarGeneracion(){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	
	if(historia && ingreso){
		document.location.href = 'ordenesidc.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha;
	} else {
		alert("No se encontró historia, ingreso y fecha en los parametros de entrada.");		
	}
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
 * Limpia el resultado del buscador de examenes
 ******************************************************************************************************************************/
function limpiarBuscadorExamenes(){
	var resultados = document.getElementById('cntExamenes').innerHTML = "";
}
/*****************************************************************************************************************************
 * Limpia el resultado del buscador de examenes
 ******************************************************************************************************************************/
function consultarServicioExamen(){
	var elemento = document.getElementById('wservexamen');
	var consOrden = document.getElementById('wconsserv');
	var vecParametros = "";  
	
	consOrden.innerHTML = '';
	if(elemento && elemento.value != '' && consOrden){
		vecParametros = elemento.value.split("|");
		
		consOrden.innerHTML = vecParametros[1]; 
	}
}

/*****************************************************************************************************************************
 * Crea dinámicamente una fila en el detalle de medicamentos
 * 
 * Dependiendo del tipo de protocolo se agregará a una lista u otra, los protocolos actuales son:
 * 
 * 1. Normal, se agrega en la lista convencional
 * 2. Analgesia: Contenedor detAnalgesia
 ******************************************************************************************************************************/
function agregarArticulo( detKardexContenedor, deAlta, codigoArticulo ){
	
	if(!isset(deAlta))
	{
		var deAlta = false;
		var contenedorArticulo = 'detKardexAddN';
	}
	else
	{
		var contenedorArticulo = 'detKardexAddImpN';
	}
	
	contenedorArticulo = 'detKardexAddN';
	
	var idx = 0;
	var tipoProtocolo = "%";
	var cntDetalleKardex = "";
	
	var idFilaNueva = "", idColumnaArticulo = "", idCampoArticulo = "", idCampoDosis = "", idUnidadManejo = "";
	var idDuplicable = "", idDispensable = "", idDiasVencimiento = "", idFormaFarmaceutica = "", idOcultoFracciones = "", idOcultoVence = "", idCantidadUnidadManejo = ""; 
	var idUnidadDosis = "", idPeriodicidad = "", idCondicion = "", idVia = "", idFechaInicio = "", idBtnFechaInicio = "", idChkConfirmacion = "", idChkNoEnviar = "";
	var idDiasTratamiento = "", idDosisMaximas = "", idObservaciones = "", idIndiceMovimiento = "";

	var elementoAnteriorDetalle = 0; 
	var posicionActual = 0;

	var tipoProtocoloAux = "";
	
	var whis = document.getElementById('whistoria').value;
	var wing = document.getElementById('wingreso').value;	
	
	/*
	for (i=0;i<document.forms.forma.wtipoprot.length;i++){ 
    	if (document.forms.forma.wtipoprot[i].checked){
    		tipoProtocolo = document.forms.forma.wtipoprot[i].value;
    		break; 
    	}
    }
	*/
	
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
			idx = elementosDetalle-1;

			posicionActual = elementosDetalle;
		break;
	}
	
	tipoProtocoloAux = "N";
	
	elementoAnteriorDetalle = document.getElementById("wnmmed"+tipoProtocoloAux+idx);
	cntDetalleKardex = document.getElementById(detKardexContenedor + tipoProtocoloAux);
	
	idTabla = "tbDetalle%";		
	
//	idFilaNueva = 'tr'+ tipoProtocoloAux + posicionActual;
	idFilaNueva = 'trFil' + posicionActual;
	
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

	idCampoUnidadPosologia = 'wunidadposologia' + tipoProtocoloAux + posicionActual;
	idCampoPosologia = 'wposologia' + tipoProtocoloAux + posicionActual;
	
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
	idChkManejoInterno = 'wchkint' + tipoProtocoloAux + posicionActual;
	idChkImprimir = 'wchkimp' + tipoProtocoloAux + posicionActual;
	idModificado = 'wmodificado' + tipoProtocoloAux + posicionActual;
	//idChkObservaciones = 'wchkobs' + tipoProtocoloAux + posicionActual;
	
	idArtProtocolo = 'whartpro' + tipoProtocoloAux + posicionActual;
	
	idImprimir = 'wimp' + tipoProtocoloAux + posicionActual;
	
	
	var idArtPos = 'wespos' + tipoProtocoloAux + posicionActual;
	var idArtCtc = 'wtienectc' + tipoProtocoloAux + posicionActual;
	
	idIndiceMovimiento = "tr" + tipoProtocoloAux;
	
	if(!elementoAnteriorDetalle || elementoAnteriorDetalle.value != '' || posicionActual == 0){

		/******************************************************************************************
		 * Noviembre 21 de 2011
		 ******************************************************************************************/
		//Fecha y hora actual para colocar automaticamente la fecha y hora actual 2009-05-10 a las:10:00
		var fechaActual = new Date();
		
		horaActual = fechaActual.getHours();
		
		if(horaActual % 2 != 0){
			//fechaActual.setHours(fechaActual.getHours() + 1);
			fechaActual = new Date( fechaActual.getTime() + 1000*3600 );	//Creo la fecha y hora con una hora de adelanto
		} else {
			//fechaActual.setHours(fechaActual.getHours() + 2);
			fechaActual = new Date( fechaActual.getTime() + 2000*3600 );	//Creo la fecha y hora con dos horas de adelanto
		}
		
		diaActual = fechaActual.getDate();
		mesActual = fechaActual.getMonth() + 1;
		anioActual= fechaActual.getFullYear();
		
		horaActual = fechaActual.getHours();
		
		var cont1 = 0;
		while(cont1 < elementosDetalle){
			var item = document.getElementById("wnmmed"+tipoProtocoloAux+cont1);
									
			if(item){
				if(item.tagName != 'DIV'){
					cd = item.value.split("-");
				} else { 
					cd = item.innerHTML.split("-");
				}
				cdItem = cd[0];
				
				if(cdItem == codigoArticulo){
					existe = true;
						
						
						var fechorInicial = document.getElementById( "whfinicio"+tipoProtocoloAux+cont1 );
						if( fechorInicial ){
							fechorInicial = fechorInicial.value.split( " a las:" );
							fechaInicial = fechorInicial[0].split( "-" );
							horaFinal = fechorInicial[1].split(":");
						}
						
						var fechorInicial2 = document.getElementById( "wfinicio"+tipoProtocoloAux+cont1 );
						if( fechorInicial2 ){
							fechorInicial2 = fechorInicial2.value.split( " a las:" );
							fechaInicial2 = fechorInicial2[0].split( "-" );
							horaFinal2 = fechorInicial2[1].split( ":" );
						}
						
						if( fechorInicial && fechaInicial[0] == anioActual && fechaInicial[1] == mesActual && fechaInicial[2] == diaActual ){
							
							if( horaActual == horaFinal[0] ){
								horaActual = (horaActual+2)%24;
								cont1 = 0;
								continue;
							}
						}
						
						if( fechorInicial2 && fechaInicial2[0] == anioActual && fechaInicial2[1] == mesActual && fechaInicial2[2] == diaActual ){
							if( horaActual == horaFinal2[0] ){
								horaActual = (horaActual+2)%24;
								cont1 = 0;
								continue;
							}
						}
						
						
						
					// break;	
				}					
			}
			cont1++;
		}
		/******************************************************************************************/
		
		
		if(mesActual && mesActual.toString().length == 1){
			mesActual = "0" + mesActual.toString();
		}
		
		if(diaActual && diaActual.toString().length == 1){
			diaActual = "0" + diaActual.toString();
		}
		
		if( horaActual < 10 ){
			horaActual = "0"+horaActual;
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
		var columna15 = document.createElement("td");		//Nombre protocolo
		var columna16 = document.createElement("td");		//Manejo Interno
		var columna17 = document.createElement("td");		//Posologia
		var columna18 = document.createElement("td");		//Unidad posologia
		var columna19 = document.createElement("td");		//Traer justificacion historia clinica
		var columna20 = document.createElement("td");		//Imprimir

		//Centradas
		columna1.setAttribute('align','center');
		columna10.setAttribute('align','center');
		columna14.setAttribute('align','center');
		columna15.setAttribute('align','center');
		columna16.setAttribute('align','center');
		columna19.setAttribute('align','center');
		columna20.setAttribute('align','center');
		
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
	
//		debugger;
		//Link de borrar
		var atr = new Array();
		atr['onClick'] = "javascript:quitarArticulo('"+posicionActual+"','"+tipoProtocolo+"','','"+contenedorArticulo+"');";
		
		var link2 = crearCampo("4","",tipoProtocolo+".2",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");
//		var link2 = document.createElement("a");
//		link2.setAttribute('href','#null');
//		if(!esIE){
//			link2.setAttribute('onClick','javascript:quitarArticulo('+ posicionActual +',"'+tipoProtocolo+'");');
//		}else{
//			link2.onclick  = new Function('evt','javascript:quitarArticulo('+ posicionActual +',"'+tipoProtocolo+'");');
//		}
//	
//		var img2 = document.createElement("img");
//		img2.setAttribute('src','../../images/medical/root/borrar.png');
//
//		link2.appendChild(img2);
	
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
		var atr = new Array();
		atr['size'] = "7";
		atr['maxLength'] = "7";
		atr['class'] = "campo2";
		atr['onKeyPress'] = "return validarEntradaDecimal(event);";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";

		var dosis = crearCampo("1",idCampoDosis,tipoProtocolo+".5",atr,"");

		var posologia = crearCampo("1",idCampoPosologia,tipoProtocolo+".5",atr,"");
		var unidadPosologia = crearCampo("1",idCampoUnidadPosologia,tipoProtocolo+".5",atr,"");
		
//		var dosis = document.createElement("input");
//		dosis.setAttribute('type','text');
//		dosis.setAttribute('name',idCampoDosis);
//		dosis.setAttribute('id',idCampoDosis);	
//		dosis.setAttribute('size','7');
//		dosis.setAttribute('maxLength','7');
//		dosis.className = 'campo2';   //validarEntradaDecimal(event);
//		if(!esIE){
//			dosis.setAttribute('onKeyPress', 'return validarEntradaDecimal(event);');
//		}else{
//			dosis.onkeypress = new Function('evt','return validarEntradaDecimal(event);');
//		}

		//Indica si fue modificado
		// var bandera = document.getElementById("wmodificado"+tipo+indice);
		var modificado = document.createElement("input");
		modificado.setAttribute('type','hidden');
		modificado.setAttribute('name',idModificado);
		modificado.setAttribute('id',idModificado);
		modificado.value = 'N';
		
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
		
		//Unidad de medida
		/*
		var unidadDosis = document.createElement("input");
		unidadDosis.setAttribute('type','text');
		unidadDosis.setAttribute('name',idUnidadDosis);
		unidadDosis.setAttribute('id',idUnidadDosis);
		unidadDosis.setAttribute('size','10');
		unidadDosis.setAttribute('readOnly','readOnly');
		unidadDosis.className = 'campo2';
		*/

		//Forma farmaceutica
		/*
		var formaFtica = document.createElement("input");
		formaFtica.setAttribute('type','text');
		formaFtica.setAttribute('name',idFormaFarmaceutica);
		formaFtica.setAttribute('id',idFormaFarmaceutica);
		formaFtica.setAttribute('size','10');
		formaFtica.setAttribute('readOnly','readOnly');
		formaFtica.className = 'campo2';
		*/
		
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
		
		//Protocolo del articulo
		var artProtocolo = document.createElement("input");
		artProtocolo.setAttribute('type','hidden');
		artProtocolo.setAttribute('name',idArtProtocolo);
		artProtocolo.setAttribute('id',idArtProtocolo);

		//Protocolo del articulo
		var artImprimir = document.createElement("input");
		artImprimir.setAttribute('type','hidden');
		artImprimir.setAttribute('name',idImprimir);
		artImprimir.setAttribute('id',idImprimir);
		if(deAlta)
			artImprimir.setAttribute('value','on');
		else
			artImprimir.setAttribute('value','off');

		//Checkbox de impresion hidden
		var chkimp = document.createElement("input");
		chkimp.setAttribute('id','wchkimp'+idArtProtocolo);
		chkimp.setAttribute('name','wchkimp'+idArtProtocolo);
		chkimp.setAttribute('type','checkbox');
		chkimp.setAttribute('style','display:none');
		
		//Checkbox de impresion es elque se muestra al usuario
		var chkImprimir = document.createElement("input");
		chkImprimir.setAttribute('id',idChkImprimir );
		chkImprimir.setAttribute('name',idChkImprimir);
		chkImprimir.setAttribute('type','checkbox');
		// chkImprimir.setAttribute('onClick','this.checked=!this.checked');
		// $( chkImprimir ).attr( 'onClick','this.checked=!this.checked' );
		$( chkImprimir ).attr( 'onClick','marcarCambio("'+tipoProtocoloAux+'","'+posicionActual+'")');
		// chkImprimir.setAttribute('onClick','marcarCambio("'+tipoProtocoloAux+'","'+posicionActual+'")');
		// chkimp.setAttribute('style','display:none');
		
		// var bandera = document.getElementById("wmodificado"+tipo+indice);
		
		//Checkbox de manejo interno
		var chkint = document.createElement("input");
		chkint.setAttribute('id','wchkint'+idArtProtocolo);
		chkint.setAttribute('name','wchkint'+idArtProtocolo);
		chkint.setAttribute('type','checkbox');
		//chkint.setAttribute('checked',true);
		//chkint.setAttribute('style','display:none');

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
		
		//Protocolo del articulo
		var artEsPos = document.createElement("input");
		artEsPos.setAttribute('type','hidden');
		artEsPos.setAttribute('name',idArtPos);
		artEsPos.setAttribute('id',idArtPos);
		artEsPos.setAttribute('value','on');
		
		//Protocolo del articulo
		var artTieneCtc = document.createElement("input");
		artTieneCtc.setAttribute('type','hidden');
		artTieneCtc.setAttribute('name',idArtCtc);
		artTieneCtc.setAttribute('id',idArtCtc);
		artTieneCtc.setAttribute('value','off');
		
		/***********************************************
		LLENA LOS SELECTS PARA UBICARLOS EN LA FILA 
		************************************************/
		
		var unidadDosis = document.createElement("select");
//		var periodicidad = document.createElement("select");
//		var condicion = document.createElement("select");
//		var viaAdmon = document.createElement("select");
		
		unidadDosis.setAttribute('id',idUnidadDosis);		
		unidadDosis.className = 'seleccion';
		unidadDosis.setAttribute('disabled','disabled');
//		periodicidad.setAttribute('id',idPeriodicidad);
//		periodicidad.className = 'seleccion';		
//		condicion.setAttribute('id',idCondicion);
//		condicion.className = 'seleccion';
//		viaAdmon.setAttribute('id',idVia);
//		viaAdmon.className = 'seleccion';
		
		//Diferencias de navegadores
		if(!esIE){
			unidadDosis.innerHTML = document.getElementById("wmunidadesmedida").innerHTML;
//			periodicidad.innerHTML = document.getElementById("wmperiodicidades").innerHTML;
//			condicion.innerHTML = document.getElementById("wmcondicionessuministro").innerHTML;
//			viaAdmon.innerHTML = document.getElementById("wmviaadmon").innerHTML;
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
//			opcionesMaestro = document.getElementById("wmperiodicidades").options;
//			var cont1 = 0;
//			var opcionTmp = null;
//			
//			while(opcionesMaestro[cont1]){
//				opcionTmp = document.createElement("option");		
//				periodicidad.options.add(opcionTmp);
//				
//				opcionTmp.innerText = opcionesMaestro[cont1].text;
//				opcionTmp.value = opcionesMaestro[cont1].value;
//				
//				cont1++;
//			}
			
			//Condiciones de suministro
//			opcionesMaestro = document.getElementById("wmcondicionessuministro").options;
//			var cont1 = 0;
//			var opcionTmp = null;
//			
//			while(opcionesMaestro[cont1]){
//				opcionTmp = document.createElement("option");		
//				condicion.options.add(opcionTmp);
//				
//				opcionTmp.innerText = opcionesMaestro[cont1].text;
//				opcionTmp.value = opcionesMaestro[cont1].value;
//				
//				cont1++;
//			}
						
			//Vias de administración
//			opcionesMaestro = document.getElementById("wmviaadmon").options;
//			var cont1 = 0;
//			var opcionTmp = null;
//			
//			while(opcionesMaestro[cont1]){
//				opcionTmp = document.createElement("option");		
//				viaAdmon.options.add(opcionTmp);
//				
//				opcionTmp.innerText = opcionesMaestro[cont1].text;
//				opcionTmp.value = opcionesMaestro[cont1].value;
//				
//				cont1++;
//			}
		}
		
		
		
		
		
		
		var formaFtica = document.createElement("select");
//		var periodicidad = document.createElement("select");
//		var condicion = document.createElement("select");
//		var viaAdmon = document.createElement("select");
		
		formaFtica.setAttribute('id',idFormaFarmaceutica);		
		formaFtica.className = 'seleccion';
		formaFtica.setAttribute('disabled','disabled');
//		periodicidad.setAttribute('id',idPeriodicidad);
//		periodicidad.className = 'seleccion';		
//		condicion.setAttribute('id',idCondicion);
//		condicion.className = 'seleccion';
//		viaAdmon.setAttribute('id',idVia);
//		viaAdmon.className = 'seleccion';

		//Diferencias de navegadores
		if(!esIE){
			formaFtica.innerHTML = document.getElementById("wmfftica").innerHTML;
//			periodicidad.innerHTML = document.getElementById("wmperiodicidades").innerHTML;
//			condicion.innerHTML = document.getElementById("wmcondicionessuministro").innerHTML;
//			viaAdmon.innerHTML = document.getElementById("wmviaadmon").innerHTML;
			formaFtica.setAttribute('style','border:120px');

		}else {		
			formaFtica.style.setAttribute('width','120px');
			formaFtica.style.setAttribute('style','font-family: verdana; color: black');
			
			//Unidades dosis
			var opcionesMaestro = document.getElementById("wmfftica").options;			
			var cont1 = 0;
			var opcionTmp = null;
			
			while(opcionesMaestro[cont1]){
				opcionTmp = document.createElement("option");		
				formaFtica.options.add(opcionTmp);
				
				opcionTmp.innerText = opcionesMaestro[cont1].text;
				opcionTmp.value = opcionesMaestro[cont1].value;
				
				cont1++;
			}	
			//wselfftica.add(elOptNew, elOptOld); // standards compliant; doesn't work in IE
			
		}
		

		
		//Agrego funcion para colocar dosis maxima por condicion
		// if(!esIE){
			// condicion.setAttribute('onChange', 'javascript:agregarDosisMaxPorCondicion("'+tipoProtocolo+'",'+posicionActual+', this );');
		// }else {
			// condicion.onchange = new Function('evt','javascript:agregarDosisMaxPorCondicion("'+tipoProtocolo+'",'+posicionActual+', this )');
		// }		
		
		var atr = new Array();
		atr['class'] = "seleccion";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		var periodicidad = crearCampo("6",idPeriodicidad,tipoProtocolo+".7",atr,document.getElementById("wmperiodicidades").innerHTML);

		var atr = new Array();
		atr['class'] = "seleccion";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		var viaAdmon = crearCampo("6",idVia,tipoProtocolo+".8",atr,document.getElementById("wmviaadmon").innerHTML);
		
		var atr = new Array();
		atr['class'] = "seleccion";
		atr['onChange'] = 'javascript:agregarDosisMaxPorCondicion(\''+tipoProtocoloAux+'\','+posicionActual+', this )';
		atr['onClick'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		var condicion = crearCampo("6",idCondicion,tipoProtocolo+".10",atr,document.getElementById("wmcondicionessuministro").innerHTML);
		
		//Fecha y hora de administracion
		var fini = document.createElement("input");
		fini.setAttribute('id',idFechaInicio);
		fini.setAttribute('name',idFechaInicio);
		fini.setAttribute('type','text');
		fini.setAttribute('size','25');
		fini.setAttribute('value',fechaCompuesta);
		fini.setAttribute('readOnly','readonly');
		fini.className = 'campo2';
		
		var atr = new Array();
		atr['onClick'] = "javascript:calendario('"+posicionActual+"','"+tipoProtocoloAux+"');";
		
		var btnFini = crearCampo("3",idBtnFechaInicio,tipoProtocolo+".9",atr,"*");
		
//		var btnFini = document.createElement("input");
//		btnFini.setAttribute('type','button');
//		btnFini.setAttribute('id',idBtnFechaInicio);
//		btnFini.setAttribute('name',idBtnFechaInicio);
//		btnFini.setAttribute('alt','Haga doble click para desplegar el calendario');
//		btnFini.setAttribute('value','*');
//		
//		if(!esIE){	
//			btnFini.setAttribute('onClick', 'javascript:calendario('+posicionActual+',"'+tipoProtocolo+'");');
//		}else {						
//			btnFini.onclick = new Function('evt','javascript:calendario('+posicionActual+',"'+tipoProtocolo+'")');
//		}	
		
		//Confirmado para central de mezclas
		var atr = new Array();
		var chkConf = crearCampo("5",idChkConfirmacion,tipoProtocolo+".11",atr,"");
		
//		var chkConf = document.createElement("input");
//		chkConf.setAttribute('id',idChkConfirmacion);
//		chkConf.setAttribute('type','checkbox');
		
		//No se envia el articulo
		var atr = new Array();
		var chkDisp = crearCampo("5",idChkNoEnviar,tipoProtocolo+".4",atr,"");
		
//		var chkDisp = document.createElement("input");
//		chkDisp.setAttribute('id',idChkNoEnviar);
//		chkDisp.setAttribute('type','checkbox');
				
		//Dias de tratamiento
		var atr = new Array();
		atr['size'] = "3";
		atr['maxLength'] = "3";
		atr['class'] = "campo2";
		atr['onKeyPress'] = "return validarEntradaEntera(event);";
		atr['onKeyUp'] = "inhabilitarDosisMaxima( this,\'"+tipoProtocoloAux+"\', "+posicionActual+" );";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		
		// if(!esIE){	
			// diasTto.setAttribute('onKeyUp', "inhabilitarDosisMaxima( this,\""+tipoProtocolo+"\", "+posicionActual+" );");
		// }else{
			// diasTto.onkeypress = new Function('evt',"return inhabilitarDosisMaxima( this,\""+tipoProtocolo+"\", "+posicionActual+" );");
		// }

		var diasTto = crearCampo("1",idDiasTratamiento,tipoProtocolo+".12",atr,"");
		
//		var diasTto = document.createElement("input");
//		diasTto.setAttribute('id',idDiasTratamiento);
//		diasTto.setAttribute('type','text');
//		diasTto.setAttribute('size','3');
//		diasTto.setAttribute('maxLength','3');
//		diasTto.className = 'campo2';
//		
//		if(!esIE){	
//			diasTto.setAttribute('onKeyPress', 'return validarEntradaEntera(event);');
//		}else {						
//			diasTto.onkeypress = new Function('evt','return validarEntradaEntera(event);');
//		}
		
		//Dosis maxima
		var atr = new Array();
		atr['size'] = "6";
		atr['maxLength'] = "6";
		atr['class'] = "campo2";
		atr['onKeyPress'] = "return validarEntradaEntera(event);";
		atr['onKeyUp'] = "inhabilitarDiasTratamiento( this,\'"+tipoProtocoloAux+"\',"+posicionActual+");";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		
		var dosMax = crearCampo("1",idDosisMaximas,tipoProtocolo+".13",atr,"");
		
		// if(!esIE){	
			// dosMax.setAttribute('onKeyUp', "inhabilitarDiasTratamiento( this,\""+tipoProtocolo+"\","+posicionActual+");");
		// }else {						
			// dosMax.onkeyup = new Function('evt',"return inhabilitarDiasTratamiento( this,\""+tipoProtocolo+"\","+posicionActual+");");
		// }
		
//		var dosMax = document.createElement("input");
//		dosMax.setAttribute('id',idDosisMaximas);
//		dosMax.setAttribute('type','text');
//		dosMax.setAttribute('size','6');
//		dosMax.setAttribute('maxLength','6');
//		dosMax.className = 'campo2';
//		
//		if(!esIE){	
//			dosMax.setAttribute('onKeyPress', 'return validarEntradaEntera(event);');
//		}else {						
//			dosMax.onkeypress = new Function('evt','return validarEntradaEntera(event);');
//		}
		
		//Observaciones
		var atr = new Array();
		atr['rows'] = "2";
		atr['cols'] = "40";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		
		var observaciones = crearCampo("2",idObservaciones,tipoProtocolo+".14",atr,"");
		
//		var observaciones = document.createElement("textarea");
//		observaciones.setAttribute('id',idObservaciones);
//		observaciones.setAttribute('rows','2');
//		observaciones.setAttribute('cols','10');
		
//		if(!esIE){
//			observaciones.setAttribute('onKeyPress', 'return validarEntradaAlfabetica(event);');
//		}else{
//			observaciones.onkeypress = new Function('evt','return validarEntradaAlfabetica(event);');
//		}


		var atr = new Array();
		atr['onchange'] = "javascript:marcarManejoInterno(this,'"+whis+"','"+wing+"','"+codigoArticulo+"','"+anioActual+"-"+mesActual+"-"+diaActual+"','"+anioActual+"-"+mesActual+"-"+diaActual+"','"+horaActual+":00:00')";
		atr['onClick'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		var chkInt = crearCampo("5",idChkManejoInterno,tipoProtocolo+".16",atr,"");
		
		/*
		var atr = new Array();
		atr['onClick'] = "traeJustificacionHCE(this,'"+idObservaciones+"');";
		var chkObs = crearCampo("5",idChkObservaciones,tipoProtocolo+".16",atr,"");
		*/
		
		/*******************************************************************************
		ANEXAR CONTENIDO A LAS COLUMNAS
		********************************************************************************/
//		columna1.appendChild(link1);	
//		columna1.appendChild(link2);
		columna1.innerHTML += link2;
		columna2.appendChild(articulo);
//		columna3.appendChild(dosis);
		columna3.innerHTML += dosis;
		columna3.appendChild(unidadDosis);
		columna3.appendChild(formaFtica);
//		columna4.appendChild(periodicidad);
		columna4.innerHTML += periodicidad;
//		columna5.appendChild(condicion);
		columna5.innerHTML += condicion;
		columna7.appendChild(fini);
//		columna7.appendChild(btnFini);
		columna7.innerHTML += btnFini;
//		columna8.appendChild(dosMax);
		columna8.innerHTML += dosMax;
//		columna9.appendChild(viaAdmon);
		columna9.innerHTML += viaAdmon;
//		columna10.appendChild(chkConf);
		columna10.innerHTML += chkConf;
//		columna11.appendChild(diasTto);
		columna11.innerHTML += diasTto;
//		columna12.appendChild(observaciones);
		columna12.innerHTML += observaciones;
//		columna14.appendChild(chkDisp);
		columna14.innerHTML += chkDisp;
		columna16.innerHTML += chkInt;
		columna17.innerHTML += posologia;
		columna18.innerHTML += unidadPosologia;
		//columna19.innerHTML += chkObs;
		
		/*******************************************************************************
		ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
		********************************************************************************/

		
		
		
		fila.appendChild(columna1);		
		fila.appendChild(columna20);
		fila.appendChild(columna16);
		fila.appendChild(columna2);	
		fila.appendChild(columna15);
		fila.appendChild(columna14);
		fila.appendChild(columna3);
		fila.appendChild(columna8);
		fila.appendChild(columna9);
		fila.appendChild(columna17);		
		fila.appendChild(columna18);
		fila.appendChild(columna4);		
		fila.appendChild(columna7);
		fila.appendChild(columna11);
		fila.appendChild(columna5);
		fila.appendChild(columna10);
		fila.appendChild(columna12);
		//fila.appendChild(columna19);
		

		//Anexo los campos hidden antes de anexar la fila
		cntDetalleKardex.appendChild(unidadManejo);
		cntDetalleKardex.appendChild(ocultoFracciones);
		cntDetalleKardex.appendChild(vence);
		cntDetalleKardex.appendChild(diasVencimiento);
		cntDetalleKardex.appendChild(dispensable);
		cntDetalleKardex.appendChild(duplicable);
		cntDetalleKardex.appendChild(artProtocolo);
		cntDetalleKardex.appendChild(artImprimir);
		cntDetalleKardex.appendChild(chkimp);
		cntDetalleKardex.appendChild(modificado);
		
		cntDetalleKardex.appendChild(artEsPos);
		cntDetalleKardex.appendChild(artTieneCtc);
		//cntDetalleKardex.appendChild(formaFtica);
		
		chkImprimir.checked = true;
		columna20.appendChild(chkImprimir);
		
		columna15.style.display = "none";


		
		if(deAlta==true)
		{
			columna5.style.display = "none";
			columna7.style.display = "none";
			columna9.style.display = "none";
			columna11.style.display = "none";
			columna14.style.display = "none";
			columna10.style.display = "none";
			document.getElementById('wchkimp'+idArtProtocolo).checked = true;	
		}
		else
		{	
			columna7.style.display = "none";
			columna14.style.display = "none";
			columna10.style.display = "none";
			document.getElementById('wchkimp'+idArtProtocolo).checked = true;	
		}

		document.getElementById('wchkimp'+idArtProtocolo).style.display="none";
		
		//document.getElementById('wchkint'+idArtProtocolo).checked = true;
		
		
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
				elementosDetalle++;
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
		try{
			$( document.getElementById( idChkManejoInterno ) ).click(
				function(){
					this.checked = !this.checked;
				}
			);
		}
		catch(e){}
		
	} else {
		//alert("Debe ingresar la información del articulo actual antes de adicionar uno nuevo");
	}
	
	//Posiciona el cursor sobre el texto del cajon del codigo
	/*
	if(document.getElementById("wbnommed")){
		document.getElementById("wbnommed").focus();
		document.getElementById("wbnommed").select();
	}
	*/
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
	var atr = new Array();
	atr['onClick'] = "javascript:quitarInfusion('"+cuentaInfusiones +"');";
	
	var link2 = crearCampo("4","","2.3",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");
//	var link2 = document.createElement("a");
//	link2.setAttribute('href','#null');
//	if(!esIE){
//		link2.setAttribute('onClick','javascript:quitarInfusion('+ cuentaInfusiones +');');
//	}else{
//		link2.onclick  = new Function('evt','javascript:quitarInfusion('+ cuentaInfusiones +');');
//	}
//	
//	var img2 = document.createElement("img");
//	img2.setAttribute('src','../../images/medical/root/borrar.png');

//	link2.appendChild(img2);

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
		
	var atr = new Array();
	atr['onClick'] = "javascript:calendario4('"+cuentaInfusiones +"');";
	
	var btnfliq = crearCampo("3","btnFechaLiq"+cuentaInfusiones,"2.4",atr,"*");
	
//	var btnfliq = document.createElement("input");
//	btnfliq.setAttribute('type','button');
//	btnfliq.setAttribute('id','btnFechaLiq'+cuentaInfusiones);
//	btnfliq.setAttribute('name','btnFechaLiq'+cuentaInfusiones);
//	btnfliq.setAttribute('value','*');
//		
//	if(!esIE){	
//		btnfliq.setAttribute('onClick', 'javascript:calendario4('+cuentaInfusiones+');');
//	}else {						
//		btnfliq.onclick = new Function('evt','javascript:calendario4('+cuentaInfusiones+')');
//	} 
	
	//Select multiple de componentes
	var atr = new Array();
	atr['onDblClick'] = "javascript:quitarComponente('"+cuentaInfusiones +"');";
	atr['multiple'] = "multiple";
	atr['size'] = "5";

	var componentes = crearCampo("6","wtxtcomponentes"+cuentaInfusiones,"2.5",atr,"");
	
//	var componentes = document.createElement("select");
//	componentes.setAttribute('id','wtxtcomponentes' + cuentaInfusiones);
//	componentes.setAttribute('multiple','multiple');
//	componentes.setAttribute('size','5');
//	if(!esIE){
//		componentes.setAttribute('onDblClick', 'javascript:quitarComponente('+cuentaInfusiones+');');
//	}else{
//		componentes.ondblclick  = new Function('evt','quitarComponente('+cuentaInfusiones+');');
//	}

	//Observaciones
	var atr = new Array();
	atr['rows'] = "5";
	atr['cols'] = "65";

	var observaciones = crearCampo("2","wobscomponentes"+cuentaInfusiones,"2.6",atr,"");
	
//	var observaciones = document.createElement("textarea");
//	observaciones.setAttribute('id','wobscomponentes' + cuentaInfusiones);
//	observaciones.setAttribute('rows','2');
//	observaciones.setAttribute('cols','60');
//	if(!esIE){
//		observaciones.setAttribute('onKeyPress', 'return validarEntradaAlfabetica(event);');
//	}else{
//		observaciones.onkeypress = new Function('evt','return validarEntradaAlfabetica(event);');
//	}
		
	/*******************************************************************************
	ANEXAR CONTENIDO A LAS COLUMNAS
	********************************************************************************/
//	columna1.appendChild(link1);
//	columna1.appendChild(link2);
	columna1.innerHTML += link2;
	columna1.appendChild(oculto1);
	columna1.appendChild(oculto2);
	columna2.appendChild(fliq);
//	columna2.appendChild(btnfliq);
	columna2.innerHTML += btnfliq;
//	columna3.appendChild(componentes);
	columna3.innerHTML += componentes;
//	columna4.appendChild(observaciones);
	columna4.innerHTML += observaciones;
	
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


// Compara dos fechas y retorna true si fecha >= fecha2 
function compare_dates(fecha, fecha2)  
  {  
    var xMonth=fecha.substring(3, 5);  
    var xDay=fecha.substring(0, 2);  
    var xYear=fecha.substring(6,10);  
    var yMonth=fecha2.substring(3, 5);  
    var yDay=fecha2.substring(0, 2);  
    var yYear=fecha2.substring(6,10);  
    if (xYear> yYear)  
    {  
        return(true)  
    }  
    else  
    {  
      if (xYear == yYear)  
      {   
        if (xMonth> yMonth)  
        {  
            return(true)  
        }  
        else  
        {   
          if (xMonth == yMonth)  
          {  
            if (xDay>= yDay)  
              return(true);  
            else  
              return(false);  
          }  
          else  
            return(false);  
        }  
      }  
      else  
        return(false);  
    }  
}  


/*****************************************************************************************************************************
 * Agrega un nuevo registro al Maestro de Estudios y Ayudas Diagnóstcas por medio de AJAX
 ******************************************************************************************************************************/
function agregarNuevoExamen()
{
	var nonmbreExamen = document.getElementById('wnomproc').value;
	var tipoServicio = document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value;
	
	if(tipoServicio=='' || tipoServicio==' ' || tipoServicio=='%')
	{
		alert('Debe seleccionar un tipo de orden para poder agregar una nueva ayuda o procedimiento');
		return false;
	}

	if(nonmbreExamen=='' || nonmbreExamen==' ')
	{
		alert('El nombre de la ayuda o procedimiento no puede estar vacío');
		return false;
	}
	
	
	parametros = "consultaAjaxKardex=44&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+tipoServicio+"&descripcion="+nonmbreExamen+"&especialidad="+document.forms.forma.wespecialidad.value; 

	//alert(parametros);
	
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			$.unblockUI();
			if (ajax.readyState==4 && ajax.status==200){
				var arrItem = ajax.responseText;

				document.getElementById('wnomproc').value = '';
				
				if(arrItem && arrItem!="")
				{
					//    		seleccionarAyudaDiagnostica(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria);
						//var vecItem = item.split(",");
						
					var item = arrItem.split("|");
					
					if(item[0] != "000" ){
						seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);
						
						document.getElementById('wnomproc').onfocus = function(){
							if( justificacionUltimoExamenAgregado ){
								justificacionUltimoExamenAgregado.focus();
								justificacionUltimoExamenAgregado = '';
							}
						};
				//    		this.focus();
				//    		this.select();

						document.getElementById("btnCerrarVentana").style.display = 'none';
					}
					else{
						//muestro mensaje de error
						alert( item[1] );
					}
				}

			} 
			
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}


/*****************************************************************************************************************************
 * Agrega dinámicamente una nueva fila para un examen de laboratorio nuevo
 ******************************************************************************************************************************/
function agregarExamen(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, nroItem, crearOrden, requiereJustificacion, noPos, aydJustificacion ){
	
	if(!aydJustificacion)
		aydJustificacion = "";
	
	var aux = document.createElement("div");
	aux.innerHTML = nombreExamen.toUpperCase();
	
	//HTML de la orden
	var iHTMLOrden = "";//"<tr id='del"+centroCostos+""+consecutivoOrden+"'>";
	
	var atr = new Array();
	atr['onClick'] = "javascript:cancelarOrden('"+centroCostos+"','"+consecutivoOrden+"', this );";
	iHTMLOrden += crearCampo("4","","4.3",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");
	
	iHTMLOrden += "<a href='#null' onclick=javascript:intercalarElemento(\""+centroCostos+""+consecutivoOrden+"\"); style='font-size:10pt'>";
	iHTMLOrden += "<b>&nbsp;&nbsp;&nbsp;<u>Orden Nro. "+consecutivoOrden+"</u></b></a>&nbsp;&nbsp;&nbsp;";
	
	iHTMLOrden += "<div id=\""+centroCostos+""+consecutivoOrden+"\" class='fila2'>";
	//iHTMLOrden += "<div style='display:none'><br>Observaciones de la orden: <br>";
	
	var atr = new Array();
	atr['rows'] = "2";
	atr['cols'] = "60";
	atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";

	//iHTMLOrden += crearCampo("2","wtxtobsexamen"+centroCostos+""+consecutivoOrden,"4.4",atr,".");
	
//	iHTMLOrden += "</div></td></tr>";
	
	var idContenedor = centroCostos+""+consecutivoOrden;
	var idContenedor = centroCostos;
	var puedeAgregar = true;
	var esHospitalario = false;
	
	if(puedeAgregar){
		
		var cntDetalle = document.getElementById("detExamenes"+idContenedor);
	
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
		

		/*******************************************************************************************
		 * verificando si hay un examen igual para el mismo día
		 *******************************************************************************************/
//			debugger;
		
		var inicioFila = 1;
		//var tabla = encabezadoOrden.parentNode.parentNode;
		var tabla = document.getElementById( "examPendientes" );
		//var totalExamenes = tabla.rows[1].cells[0].rowSpan;
		var totalExamenes = tabla.rows.length - 3;	// se resta 3 porque se omiten las filas de busqueda rápida y encabezado
		var countRowSpan = 1;
		
		for( var i = 0; i < totalExamenes; i++ ){
			
			inicioFila = i + 3;

			var numFilas = tabla.rows[inicioFila].cells[0].rowSpan;
			
			if( countRowSpan > 1 )
			{
				var indiceCelda = 3;
				var indiceCeldaFecha = 1;
			}
			else
			{
				var indiceCelda = 4;
				var indiceCeldaFecha = 2;
			}

			if((countRowSpan > 1))
				countRowSpan--;
			
			if(numFilas>1)
				countRowSpan = numFilas;

			if(isset(tabla.rows[inicioFila].cells[indiceCeldaFecha]))
			{
				// Obtengo la fecha actual
				var fechaActualOrden = anioActual+"-"+mesActual+"-"+diaActual;
				// Obtengo el campo fecha de la fila actual
				var campoFecha = tabla.rows[inicioFila].cells[indiceCeldaFecha].childNodes[0];

				//var procedimientoFila = tabla.rows[inicioFila].cells[indiceCelda].childNodes[1].value;
				
				// Obtengo el número de campos de la celda actual del procedimiento
				var totalCampos = tabla.rows[inicioFila].cells[indiceCelda].childNodes.length;
				
				for( var j = 0; j < totalCampos; j++ )
				{
					campo = tabla.rows[inicioFila].cells[indiceCelda].childNodes[j];
					
					if( campo.id && campo.id.indexOf( "hexcod" ) > -1 ){
						//alert("Dato: "+campoFecha.value+" - "+fechaActualOrden+" - "+campo.value+" - "+codExamen);
						if( campo.value == codExamen && campoFecha.value == fechaActualOrden){
							if(formTipoOrden=="" || formTipoOrden==" "){
								if(!confirm( "El examen ya existe para el mismo día." ))
									return false;
							}
							i = totalExamenes;
							j = totalCampos;
						}
					}
				}
			}
		}
		
		/*******************************************************************************************/

		//Fila nueva
		var fila = document.createElement("tr");
		fila.setAttribute('id','trEx'+cuentaExamenes);		
		//fila.setAttribute('class','encabezadoTabla');		
		
		/*
		// columnas anteriores
		var columna7 = document.createElement("td");		//Nro de orden
		var columna1 = document.createElement("td");		//Acciones
		var columna2 = document.createElement("td");		//Examenes
		var columna3 = document.createElement("td");		//Observaciones
		var columna4 = document.createElement("td");		//Estado
		var columna5 = document.createElement("td");		//Fecha solicitado examen
		var columna6 = document.createElement("td");		//Justificacion
		*/
	
		//Columnas nuevas
		var columna7 = document.createElement("td");		//Nro de orden
		var columna1 = document.createElement("td");		//Imprimir
		var columna8 = document.createElement("td");		//Eliminar
		var columna2 = document.createElement("td");		//Fecha solicitado examen
		var columna3 = document.createElement("td");		//Tipo de servicio
		var columna4 = document.createElement("td");		//Procedimiento
		var columna5 = document.createElement("td");		//Justificacion
		var columna6 = document.createElement("td");		//Estado

		columna6.setAttribute('style','display:none');
		
		columna2.setAttribute('align','center');
		columna2.setAttribute('nowrap','nowrap');
		columna2.setAttribute('style','display:none');
		
		/******************************************************************************
		CONTENIDO DE LAS COLUMNAS
		*******************************************************************************/
		//Link de borrar	
		var atr = new Array();
		atr['onClick'] = "javascript:quitarExamen('"+cuentaExamenes+"','','on');";
		var link2 = crearCampo("4","","4.5",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");
		
		//Link de borrar	
		var atrImp = new Array();

		atrImp['onClick'] = "javascript:marcarImpresionExamen(this,'"+centroCostos+"','"+consecutivoOrden+"','"+codExamen+"','"+fechaCompuesta+"','"+nroItem+"');";
		atrImp['checked'] = "checked";

		var chkImp = crearCampo("5","imprimir_examen"+cuentaExamenes,"5.4",atrImp,"");
		var linkImp = crearCampo("4","","4.5",atrImp,"<img src='../../images/medical/hce/icono_imprimir.png' border='0' width='17' height='17'>");
		
		//Si la ayuda o procedimiento es hospitalario llega tambien por defecto con estado pendiente
		if(centroCostos == "H"){
			esHospitalario = true;
		}
		
		//Examen y estado del examen
		
		var atr = new Array();
		atr['rows'] = "2";
		atr['cols'] = "40";
		// atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";
		
		var justificacion = crearCampo("2","wtxtjustexamen"+cuentaExamenes,"4.6",atr,aydJustificacion);
		var checkJustificacion = "<div style='float:right;font-size:10px'>Traer resumen historia clínica <input type='checkbox' name='chkJust' id='chkJust' style='width:16px;line-height:16px' onClick='traeJustificacionHCE(this,\"wtxtjustexamen"+cuentaExamenes+"\");'></div>";
		
		var atr = new Array();
		atr['rows'] = "2";
		atr['cols'] = "40";
		atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";
		atr['readonly'] = "readonly";
		
		//var observaciones = crearCampo("2","wtxtobsexamen"+cuentaExamenes,"4.7",atr,"");
		var tipoDeServicio = nombreCentroCostos;
			
		//Anexo de fecha solicitado examen
		var fsol = document.createElement("input");
		fsol.setAttribute('id','wfsol'+cuentaExamenes);
		fsol.setAttribute('name','wfsol'+cuentaExamenes);
		//fsol.setAttribute('type','text');
		fsol.setAttribute('type','hidden');
		fsol.setAttribute('size','10');
		fsol.setAttribute('value',fechaCompuesta);
		fsol.setAttribute('readOnly','readonly');
		fsol.className = 'campo2';
			
		var atr = new Array();
		atr['onClick'] = "javascript:calendario3('"+cuentaExamenes+"');";
		
		var btnFsol = crearCampo("3","btnFechaSol"+cuentaExamenes,"4.8",atr,"*");
		
		//Texto
		var examen = document.createElement("input");
		examen.setAttribute('id','wnmexamen'+cuentaExamenes);
		examen.setAttribute('name','wnmexamen'+cuentaExamenes);
		examen.setAttribute('type','text');
		examen.setAttribute('value',reemplazarTodo(nombreExamen,"_"," "));
		examen.setAttribute('size','70');
		examen.setAttribute('readOnly','readonly');
		examen.className = 'campo2';
		
		examen.value = aux.innerHTML;
		
		//Ocultos
		var oculto = document.createElement("input");
		oculto.setAttribute('type','hidden');
		oculto.setAttribute('name','wmodificado4'+cuentaExamenes);
		oculto.setAttribute('id','wmodificado4'+cuentaExamenes);
		oculto.setAttribute('value','S');
		
		//Codigo del centro de costos
		var oculto1 = document.createElement("input");
		oculto1.setAttribute('type','hidden');
		oculto1.setAttribute('name','hexcco'+cuentaExamenes);
		oculto1.setAttribute('id','hexcco'+cuentaExamenes);
		oculto1.setAttribute('value',centroCostos);
		
		//Codigo del examen
		var oculto2 = document.createElement("input");
		oculto2.setAttribute('type','hidden');
		oculto2.setAttribute('name','hexcod'+cuentaExamenes);
		oculto2.setAttribute('id','hexcod'+cuentaExamenes);
		oculto2.setAttribute('value',codExamen);
		
		//Consecutivo de la orden
		var oculto3 = document.createElement("input");
		oculto3.setAttribute('type','hidden');
		oculto3.setAttribute('name','hexcons'+cuentaExamenes);
		oculto3.setAttribute('id','hexcons'+cuentaExamenes);
		oculto3.setAttribute('value',consecutivoOrden);
		
		//Consecutivo del item
		var oculto5 = document.createElement("input");
		oculto5.setAttribute('type','hidden');
		oculto5.setAttribute('name','hexnroitem'+cuentaExamenes);
		oculto5.setAttribute('id','hexnroitem'+cuentaExamenes);
		oculto5.setAttribute('value',0);
		
		//Ocultos
		var oculto4 = document.createElement("input");
		oculto4.setAttribute('type','hidden');
		oculto4.setAttribute('name','wmodificado4'+cuentaExamenes);
		oculto4.setAttribute('id','wmodificado4'+cuentaExamenes);
		oculto4.setAttribute('value','S');
		
		//Requiere Justificacion
		var oculto6 = document.createElement("input");
		oculto6.setAttribute('type','hidden');
		oculto6.setAttribute('name','hiReqJus'+cuentaExamenes);
		oculto6.setAttribute('id','hiReqJus'+cuentaExamenes);
		oculto6.setAttribute('value',requiereJustificacion);
		
		//Requiere Justificacion
		var oculto7 = document.createElement("input");
		oculto7.setAttribute('type','hidden');
		oculto7.setAttribute('name','hiReqJus'+cuentaExamenes);
		oculto7.setAttribute('id','hiReqJus'+cuentaExamenes);
		oculto7.setAttribute('value',requiereJustificacion);

		// Hidden que define si es de alta
		var altaexamen = document.createElement("input");
		altaexamen.setAttribute('id','wexamenalta'+cuentaExamenes);
		altaexamen.setAttribute('name','wexamenalta'+cuentaExamenes);
		altaexamen.setAttribute('type','hidden');
		altaexamen.setAttribute('value','off');
		
		
		//Requiere Justificacion
		var oculto8 = document.createElement("input");
		oculto8.setAttribute('type','hidden');
		oculto8.setAttribute('name','hiFormHce'+cuentaExamenes);
		oculto8.setAttribute('id','hiFormHce'+cuentaExamenes);
		oculto8.setAttribute('value','');
		
		//Campo oculta que indica si tiene ctc
		var oculto9 = document.createElement("input");
		oculto9.setAttribute('type','hidden');
		oculto9.setAttribute('name','wprotienectc'+cuentaExamenes);
		oculto9.setAttribute('id','wprotienectc'+cuentaExamenes);
		oculto9.setAttribute('value', noPos == 'on' ? 'on' : 'off' );
		
		//Campo oculta que indica si tiene ctc
		var oculto10 = document.createElement("input");
		oculto10.setAttribute('type','hidden');
		oculto10.setAttribute('name','wproespos'+cuentaExamenes);
		oculto10.setAttribute('id','wproespos'+cuentaExamenes);
		oculto10.setAttribute('value', noPos == 'on' ? 'off' : 'on' );

		
		//Campo de estado, por defecto viene pendiente
		var textoEstado = document.createTextNode("Pendiente");
		/*******************************************************************************
		ANEXAR CONTENIDO A LAS COLUMNAS
		********************************************************************************/
	//	columna1.appendChild(link1);	
	//	columna1.appendChild(link2);
		columna7.innerHTML = iHTMLOrden;
		columna1.innerHTML += linkImp;
		columna1.innerHTML += chkImp;
		columna1.align = "center";
		columna8.innerHTML += link2;
		columna8.align = "center";
	//	columna2.appendChild(tipoExamen);
		columna4.appendChild(examen);
		columna4.appendChild(oculto);
		columna4.appendChild(oculto1);
		columna4.appendChild(oculto2);
		columna4.appendChild(oculto3);
		columna4.appendChild(oculto5);
		columna4.appendChild(oculto6);
		columna4.appendChild(oculto7);
		columna4.appendChild(oculto8);
		columna4.appendChild(oculto9);
		columna4.appendChild(oculto10);
		columna4.appendChild(altaexamen);
	//	columna3.appendChild(observaciones);
		columna3.innerHTML += tipoDeServicio;
	//	columna4.appendChild(estadosExamen);
		columna6.appendChild(textoEstado);
		columna2.appendChild(fsol);
	//	columna5.appendChild(btnFsol);
	//	columna2.innerHTML += btnFsol;
	//	columna6.appendChild(justificacion);
		columna5.innerHTML += justificacion;
		columna5.innerHTML += checkJustificacion;
		
		
		//Oculto campo de resultado
		//columna3.style.display='none';
		
		/*******************************************************************************
		ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
		********************************************************************************/
		if( crearOrden ){
			columna7.align = 'center';
			columna7.rowSpan = 1;
			columna7.id = "del"+centroCostos+consecutivoOrden;
			fila.appendChild(columna7);
	//		fila.id = "del"+centroCostos+consecutivoOrden;
		}
		
		fila.appendChild(columna1);
		fila.appendChild(columna8);
		fila.appendChild(columna2);
		fila.appendChild(columna3);	
		fila.appendChild(columna4);
		fila.appendChild(columna5);
		fila.appendChild(columna6);
		
		var encabezadoTabla = document.getElementById( "encabezadoExamenes" );
		
		//ANEXAR LA FILA A LA TABLA
		cntDetalle.insertBefore( fila,cntDetalle.firstChild );
	//	cntDetalle.appendChild(fila);
		
		if( !crearOrden ){
			/*******************************************************************************************
			 * Si no se va a crear la fila correspondiente a orden se debe buscar la fila que tenga la 
			 * cabecera de la orden y aumentar el rowspan de la primera fila
			 *******************************************************************************************/
			
			//fila del encabezado de la orden
			var encabezadoOrden = document.getElementById( "del"+centroCostos+consecutivoOrden );
			
			
			if( encabezadoOrden.rowSpan == 0 ){
				encabezadoOrden.rowSpan = 1;
			}
			
			fila.insertBefore( encabezadoOrden, fila.firstChild );
			
			encabezadoOrden.rowSpan = encabezadoOrden.rowSpan+1;
		}
		else{
			//fila del encabezado de la orden
			var encabezadoOrden = document.getElementById( "del"+centroCostos+consecutivoOrden );
			fila.insertBefore( encabezadoOrden, fila.firstChild );
		}

		cuentaExamenes++;

		examen.style.width = "100%";		
		
		justificacionUltimoExamenAgregado = columna5.firstChild;
		
		var hermanoActual = cntDetalle.parentNode.parentNode.parentNode;
		if(isset(hermanoActual) && hermanoActual)
			var hermanoAnterior = hermanoActual.previousSibling;
		if(isset(hermanoAnterior) && hermanoAnterior)
			var hermanoMayor = hermanoAnterior.previousSibling;
		
		var contenedorOrdenesPorCco = document.getElementById( "cntOtrosExamenes" );
		
		try{
			contenedorOrdenesPorCco.insertBefore( hermanoActual, contenedorOrdenesPorCco.firstChild );
			contenedorOrdenesPorCco.insertBefore( hermanoAnterior, contenedorOrdenesPorCco.firstChild );
			contenedorOrdenesPorCco.insertBefore( hermanoMayor, contenedorOrdenesPorCco.firstChild );
			
		//	columna6.firstChild.focus();
			
			//debugger;
			for( i = 0; i < contenedorOrdenesPorCco.childNodes.length; i++ ){
				
				if( contenedorOrdenesPorCco.childNodes[i].tagName == "DIV" && contenedorOrdenesPorCco.childNodes[i].id != '' && contenedorOrdenesPorCco.childNodes[i].id != centroCostos ){
					contenedorOrdenesPorCco.childNodes[i].style.display = 'none';
				}
				
			}
		}
		catch(e){
		}

		// var clone = $('#detExamenes'+idContenedor).clone();
		// $('#detExamenes'+idContenedor).remove();
		// clone.insertAfter('#encabezadoExamenes');
		
		$('#detExamenes'+idContenedor).insertAfter('#encabezadoExamenes');

		if(noPos=="on")
		{
			if(!adicionMultiple)
			{
				mostrarCtcProcedimientos( codExamen, cuentaExamenes-1 ); //Noviembre 08 de 2012
			}
			else
			{
				strPendientesCTC += 'procedimiento|'+codExamen+'|'+parseInt( cuentaExamenes-1 )+'\r\n';
			}
		}
		
		oculto6.value = 'off';
		grabarExamen( cuentaExamenes-1, false );
		oculto6.value = requiereJustificacion;
		
		hayArticulosAImprimir();
		
	} else {
		alert('Por favor ingrese la información del examen antes de agregar uno nuevo.');
	}
}

/*****************************************************************************************************************************
 * Agrega dinámicamente una nueva fila para un examen de laboratorio nuevo
 ******************************************************************************************************************************/
function agregarExamenAlta(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, nroItem, crearOrden, requiereJustificacion, noPos, aydJustificacion ){
	
	if(!aydJustificacion)
		aydJustificacion = "";
	
	var aux = document.createElement("div");
	aux.innerHTML = nombreExamen;
	
	//HTML de la orden
	var iHTMLOrden = "";//"<tr id='del"+centroCostos+""+consecutivoOrden+"'>";
	
	
	iHTMLOrden += "<div id=\""+centroCostos+""+consecutivoOrden+"imp\" class='fila2'>";
	//iHTMLOrden += "<div style='display:none'><br>Observaciones de la orden: <br>";
	
	var atr = new Array();
	atr['rows'] = "2";
	atr['cols'] = "60";
	atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";

	//iHTMLOrden += crearCampo("2","wtxtobsexamen"+centroCostos+""+consecutivoOrden,"4.4",atr,".");
	
//	iHTMLOrden += "</div></td></tr>";
	
	var idContenedor = centroCostos+""+consecutivoOrden;
	var idContenedor = centroCostos;
	var puedeAgregar = true;
	var esHospitalario = false;
	
	if(puedeAgregar){
		
		var cntDetalle = document.getElementById("detExamenes"+idContenedor+"Imp");
	
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
		

		/*******************************************************************************************
		 * verificando si hay un examen igual para el mismo día
		 *******************************************************************************************/
//			debugger;
		
		var inicioFila = 1;
		//var tabla = encabezadoOrden.parentNode.parentNode;
		var tabla = document.getElementById( "examPendientesImp" );
		//var totalExamenes = tabla.rows[1].cells[0].rowSpan;
		var totalExamenes = tabla.rows.length - 2;	// se resta 2 porque se omiten la fila encabezado
		var countRowSpan = 1;
		
		for( var i = 0; i < totalExamenes; i++ ){
			
			inicioFila = i + 2;

			var numFilas = tabla.rows[inicioFila].cells[0].rowSpan;
			
			if( countRowSpan > 1 )
			{
				var indiceCelda = 2;
				var indiceCeldaFecha = 0;
			}
			else
			{
				var indiceCelda = 3;
				var indiceCeldaFecha = 1;
			}

			if((countRowSpan > 1))
				countRowSpan--;
			
			if(numFilas>1)
				countRowSpan = numFilas;

			if(isset(tabla.rows[inicioFila].cells[indiceCeldaFecha]))
			{
				
				// Obtengo la fecha actual
				var fechaActualOrden = anioActual+"-"+mesActual+"-"+diaActual;
				// Obtengo el campo fecha de la fila actual
				var campoFecha = tabla.rows[inicioFila].cells[indiceCeldaFecha].childNodes[0];

				//var procedimientoFila = tabla.rows[inicioFila].cells[indiceCelda].childNodes[1].value;
				
				// Obtengo el número de campos de la celda actual del procedimiento
				var totalCampos = tabla.rows[inicioFila].cells[indiceCelda].childNodes.length;
				
				for( var j = 0; j < totalCampos; j++ )
				{
					campo = tabla.rows[inicioFila].cells[indiceCelda].childNodes[j];
					
					if( campo.id && campo.id.indexOf( "hexcod" ) > -1 ){
						//alert("Dato: "+campoFecha.value+" - "+fechaActualOrden+" - "+campo.value+" - "+codExamen);
						if( campo.value == codExamen && campoFecha.value == fechaActualOrden){
							if(!confirm( "El examen ya existe para el mismo día." ))
								return false;
							i = totalExamenes;
							j = totalCampos;
						}
					}
				}
			}
		}
		
		/*******************************************************************************************/

		//Fila nueva
		var fila = document.createElement("tr");
		fila.setAttribute('id','trExImp'+cuentaExamenes);		
		//fila.setAttribute('class','encabezadoTabla');		
		
		/*
		// columnas anteriores
		var columna7 = document.createElement("td");		//Nro de orden
		var columna1 = document.createElement("td");		//Acciones
		var columna2 = document.createElement("td");		//Examenes
		var columna3 = document.createElement("td");		//Observaciones
		var columna4 = document.createElement("td");		//Estado
		var columna5 = document.createElement("td");		//Fecha solicitado examen
		var columna6 = document.createElement("td");		//Justificacion
		*/
	
		//Columnas nuevas
		var columna7 = document.createElement("td");		//Nro de orden
		//var columna1 = document.createElement("td");		//Acciones
		var columna2 = document.createElement("td");		//Fecha solicitado examen
		var columna3 = document.createElement("td");		//Tipo de servicio
		var columna4 = document.createElement("td");		//Procedimiento
		var columna5 = document.createElement("td");		//Justificacion
		//var columna6 = document.createElement("td");		//Estado
	
		columna7.setAttribute('align','center');
		columna2.setAttribute('align','center');
		columna2.setAttribute('nowrap','nowrap');
		columna3.setAttribute('align','center');
		
		/******************************************************************************
		CONTENIDO DE LAS COLUMNAS
		*******************************************************************************/
		//Link de borrar	
		var atr = new Array();
		atr['onClick'] = "javascript:quitarExamen('"+cuentaExamenes+"','Imp','on');";
		
		var link2 = crearCampo("4","","4.5",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");
		
		//Si la ayuda o procedimiento es hospitalario llega tambien por defecto con estado pendiente
		if(centroCostos == "H"){
			esHospitalario = true;
		}
		
		//Examen y estado del examen
		
		var atr = new Array();
		atr['rows'] = "2";
		atr['cols'] = "40";
		atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";
		
		var justificacion = crearCampo("2","wtxtjustexamenimp"+cuentaExamenes,"4.6",atr,aydJustificacion);
		
		var justificacionaux = document.createElement("input");
		justificacionaux.setAttribute('id','wtxtjustexamen'+cuentaExamenes);
		justificacionaux.setAttribute('name','wtxtjustexamen'+cuentaExamenes);
		justificacionaux.setAttribute('type','hidden');
		justificacionaux.setAttribute('value',aydJustificacion);
		
		
		var atr = new Array();
		atr['rows'] = "2";
		atr['cols'] = "40";
		atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";
		atr['readonly'] = "readonly";
		
		//var observaciones = crearCampo("2","wtxtobsexamen"+cuentaExamenes,"4.7",atr,"");
		var tipoDeServicio = nombreCentroCostos;
			
		//Anexo de fecha solicitado examen
		var fsol = document.createElement("input");
		fsol.setAttribute('id','wfsolimp'+cuentaExamenes);
		fsol.setAttribute('name','wfsolimp'+cuentaExamenes);
		fsol.setAttribute('type','text');
		fsol.setAttribute('size','10');
		fsol.setAttribute('value',fechaCompuesta);
		fsol.setAttribute('readOnly','readonly');
		fsol.className = 'campo2';

		var fsolaux = document.createElement("input");
		fsolaux.setAttribute('id','wfsol'+cuentaExamenes);
		fsolaux.setAttribute('name','wfsol'+cuentaExamenes);
		fsolaux.setAttribute('type','hidden');
		fsolaux.setAttribute('value',fechaCompuesta);
		
		var atr = new Array();
		atr['onClick'] = "javascript:calendario3('"+cuentaExamenes+"');";
		
		var btnFsol = crearCampo("3","btnFechaSol"+cuentaExamenes,"4.8",atr,"*");
		
		//Checkbox de impresion
		var chkimp = document.createElement("input");
		chkimp.setAttribute('id','wchkimpexamen'+cuentaExamenes);
		chkimp.setAttribute('name','wchkimpexamen'+cuentaExamenes);
		chkimp.setAttribute('type','checkbox');
		chkimp.setAttribute('style','display:none');

		// Hidden que define si es de alta
		var altaexamen = document.createElement("input");
		altaexamen.setAttribute('id','wexamenalta'+cuentaExamenes);
		altaexamen.setAttribute('name','wexamenalta'+cuentaExamenes);
		altaexamen.setAttribute('type','hidden');
		altaexamen.setAttribute('value','on');

		//Texto
		var examen = document.createElement("input");
		examen.setAttribute('id','wnmexamenimp'+cuentaExamenes);
		examen.setAttribute('name','wnmexamenimp'+cuentaExamenes);
		examen.setAttribute('type','text');
		examen.setAttribute('value',reemplazarTodo(nombreExamen,"_"," "));
		examen.setAttribute('size','70');
		examen.setAttribute('readOnly','readonly');
		examen.className = 'campo2';
		
		examen.value = aux.innerHTML;

		var examenaux = document.createElement("input");
		examenaux.setAttribute('id','wnmexamen'+cuentaExamenes);
		examenaux.setAttribute('name','wnmexamen'+cuentaExamenes);
		examenaux.setAttribute('type','hidden');
		examenaux.setAttribute('value',reemplazarTodo(nombreExamen,"_"," "));

		
		//Ocultos
		var oculto = document.createElement("input");
		oculto.setAttribute('type','hidden');
		oculto.setAttribute('name','wmodificado4'+cuentaExamenes);
		oculto.setAttribute('id','wmodificado4'+cuentaExamenes);
		oculto.setAttribute('value','S');
		
		//Codigo del centro de costos
		var oculto1 = document.createElement("input");
		oculto1.setAttribute('type','hidden');
		oculto1.setAttribute('name','hexccoimp'+cuentaExamenes);
		oculto1.setAttribute('id','hexccoimp'+cuentaExamenes);
		oculto1.setAttribute('value',centroCostos);
		
		//Codigo del examen
		var oculto2 = document.createElement("input");
		oculto2.setAttribute('type','hidden');
		oculto2.setAttribute('name','hexcodimp'+cuentaExamenes);
		oculto2.setAttribute('id','hexcodimp'+cuentaExamenes);
		oculto2.setAttribute('value',codExamen);
		
		//Consecutivo de la orden
		var oculto3 = document.createElement("input");
		oculto3.setAttribute('type','hidden');
		oculto3.setAttribute('name','hexconsimp'+cuentaExamenes);
		oculto3.setAttribute('id','hexconsimp'+cuentaExamenes);
		oculto3.setAttribute('value',consecutivoOrden);
		
		//Consecutivo del item
		var oculto5 = document.createElement("input");
		oculto5.setAttribute('type','hidden');
		oculto5.setAttribute('name','hexnroitemimp'+cuentaExamenes);
		oculto5.setAttribute('id','hexnroitemimp'+cuentaExamenes);
		oculto5.setAttribute('value',0);
		
		//Ocultos
		var oculto4 = document.createElement("input");
		oculto4.setAttribute('type','hidden');
		oculto4.setAttribute('name','wmodificado4'+cuentaExamenes);
		oculto4.setAttribute('id','wmodificado4'+cuentaExamenes);
		oculto4.setAttribute('value','S');
		
		//Requiere Justificacion
		var oculto6 = document.createElement("input");
		oculto6.setAttribute('type','hidden');
		oculto6.setAttribute('name','hiReqJus'+cuentaExamenes);
		oculto6.setAttribute('id','hiReqJus'+cuentaExamenes);
		oculto6.setAttribute('value',requiereJustificacion);
		
		//Campo de estado, por defecto viene pendiente
		var textoEstado = document.createTextNode("Pendiente");
		/*******************************************************************************
		ANEXAR CONTENIDO A LAS COLUMNAS
		********************************************************************************/
	//	columna1.appendChild(link1);	
	//	columna1.appendChild(link2);
		columna7.innerHTML = link2;
		columna7.appendChild(chkimp);
		columna7.appendChild(altaexamen);
	//	columna1.appendChild(chkimp);
	//	columna1.align = "center";
	//	columna2.appendChild(tipoExamen);
		columna4.appendChild(examen);
		columna4.appendChild(examenaux);
		columna4.appendChild(oculto);
		columna4.appendChild(oculto1);
		columna4.appendChild(oculto2);
		columna4.appendChild(oculto3);
		columna4.appendChild(oculto5);
		columna4.appendChild(oculto6);
	//	columna3.appendChild(observaciones);
		columna3.innerHTML += tipoDeServicio;
	//	columna4.appendChild(estadosExamen);
	//	columna6.appendChild(textoEstado);
		columna2.appendChild(fsol);
		columna2.appendChild(fsolaux);
	//	columna5.appendChild(btnFsol);
		columna2.innerHTML += btnFsol;
	//	columna6.appendChild(justificacion);
		columna5.innerHTML += justificacion;
		columna5.appendChild(justificacionaux);
		
		//Oculto campo de resultado
		//columna3.style.display='none';
		
		/*******************************************************************************
		ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
		********************************************************************************/
		/*
		if( crearOrden ){
			columna7.align = 'center';
			columna7.rowSpan = 1;
			columna7.id = "delImp"+centroCostos+consecutivoOrden;
			fila.appendChild(columna7);
	//		fila.id = "del"+centroCostos+consecutivoOrden;
		}
		*/
		
		fila.appendChild(columna7);
		fila.appendChild(columna2);
		fila.appendChild(columna3);	
		fila.appendChild(columna4);
		fila.appendChild(columna5);
		//fila.appendChild(columna6);
		
		//ANEXAR LA FILA A LA TABLA
		cntDetalle.insertBefore( fila,cntDetalle.firstChild );
	//	cntDetalle.appendChild(fila);
		
		// if( !crearOrden ){
			// /*******************************************************************************************
			 // * Si no se va a crear la fila correspondiente a orden se debe buscar la fila que tenga la 
			 // * cabecera de la orden y aumentar el rowspan de la primera fila
			 // *******************************************************************************************/
			
			// //fila del encabezado de la orden
			// var encabezadoOrden = document.getElementById( "delImp"+centroCostos+consecutivoOrden );
			
			
			// if( encabezadoOrden.rowSpan == 0 ){
				// encabezadoOrden.rowSpan = 1;
			// }
			
			// fila.insertBefore( encabezadoOrden, fila.firstChild );
			
			// encabezadoOrden.rowSpan = encabezadoOrden.rowSpan+1;
		// }
		// else{
			// //fila del encabezado de la orden
			// var encabezadoOrden = document.getElementById( "delImp"+centroCostos+consecutivoOrden );
			// fila.insertBefore( encabezadoOrden, fila.firstChild );
		// }

		document.getElementById('wchkimpexamen'+cuentaExamenes).checked = true;
		document.getElementById('wchkimpexamen'+cuentaExamenes).style.display="none";
		

		marcarImpresionExamen(document.getElementById('wchkimpexamen'+cuentaExamenes),centroCostos,consecutivoOrden,codExamen,fechaCompuesta,'');

		
		cuentaExamenes++;

		examen.style.width = "100%";		
		
		justificacionUltimoExamenAgregado = columna5.firstChild;
		
		var hermanoActual = cntDetalle.parentNode.parentNode.parentNode;
		if(isset(hermanoActual) && hermanoActual)
			var hermanoAnterior = hermanoActual.previousSibling;
		if(isset(hermanoAnterior) && hermanoAnterior)
			var hermanoMayor = hermanoAnterior.previousSibling;
		
		/*
		var contenedorOrdenesPorCco = document.getElementById( "cntOtrosExamenes" );
		
		try{
			contenedorOrdenesPorCco.insertBefore( hermanoActual, contenedorOrdenesPorCco.firstChild );
			contenedorOrdenesPorCco.insertBefore( hermanoAnterior, contenedorOrdenesPorCco.firstChild );
			contenedorOrdenesPorCco.insertBefore( hermanoMayor, contenedorOrdenesPorCco.firstChild );
			
		//	columna6.firstChild.focus();
			
			//debugger;
			for( i = 0; i < contenedorOrdenesPorCco.childNodes.length; i++ ){
				
				if( contenedorOrdenesPorCco.childNodes[i].tagName == "DIV" && contenedorOrdenesPorCco.childNodes[i].id != '' && contenedorOrdenesPorCco.childNodes[i].id != centroCostos ){
					contenedorOrdenesPorCco.childNodes[i].style.display = 'none';
				}
				
			}
		}
		catch(e){
		}
		*/
		
		if(noPos=="on")
		{
			if(!adicionMultiple)
			{
				mostrarCtcProcedimientos( codExamen, cuentaExamenes-1 ); //Noviembre 08 de 2012
			}
			else
			{
				strPendientesCTC += 'procedimiento|'+codExamen+'|'+cuentaExamenes-1+'\r\n';
			}
		}
		
	} else {
		alert('Por favor ingrese la información del examen antes de agregar uno nuevo.');
	}
}


// Filtra en la tabla de ordenes pendientes segun los parametros de busqueda que se hayan establecido
function consultarOrdenesPendientes(wemp_pmla,wempresa,wbasedato,whis,wing)
{
	//debugger;
	//var parametros = "consultaAjaxKardex=33&wemp_pmla="+wemp_pmla+"&wempresa="+wempresa+"&wbasedato="+wbasedato					+"&whis="+whis+"&wing="+wing+"&wfecini="+document.forms.forma.wfecini.value+"&wfecfin="+document.forms.forma.wfecfin.value+"&wtiposerv="+document.forms.forma.wtiposerv.value+"&wprocedimiento="+document.forms.forma.wprocedimiento.value+"&westadodet="+westadodet;

		var tabla = document.getElementById( "examPendientes" );
		//var totalExamenes = tabla.rows[1].cells[0].rowSpan;
		var totalExamenes = tabla.rows.length - 3;	// se resta 3 porque se omiten las filas de busqueda rápida y encabezado
		var countRowSpan = 1;
		
		// Datos a buscar
		fechaInicioConsulta = document.forms.forma.wfecini2.value;
		fechaFinConsulta = document.forms.forma.wfecfin2.value;
		procedimientocioConsulta = document.forms.forma.wprocedimiento2.value;
		tipoServicioConsulta = document.forms.forma.wtiposerv2.value;
		justificacionConsulta = document.forms.forma.wjustificacion2.value;
		estadoConsulta = document.forms.forma.westadodet2.value;
		
		for( var i = 0; i < totalExamenes; i++ ){
			
			inicioFila = i + 3;
			// Establece si se encontro alguna de las cadeas buscadas en la fila actual
			findFecha = false;
			findServicio = false;
			findProcedimiento = false;
			findJustificacion = false;
			findEstado = false;

			var numFilas = tabla.rows[inicioFila].cells[0].rowSpan;
			
			if( countRowSpan > 1 )
			{
				var indiceCeldaFecha = 1;
				var indiceCeldaServicio = 2;
				var indiceCelda = 3;
				var indiceCeldaJustificacion = 4;
				var indiceCeldaEstado = 6;
			}
			else
			{
				var indiceCeldaFecha = 2;
				var indiceCeldaServicio = 3;
				var indiceCelda = 4;
				var indiceCeldaJustificacion = 5;
				var indiceCeldaEstado = 7;
			}

			if((countRowSpan > 1))
				countRowSpan--;
			
			if(numFilas>1)
				countRowSpan = numFilas;

			if(isset(tabla.rows[inicioFila].cells[indiceCeldaFecha]))
			{
				// Obtengo el campo fecha de la fila actual
				var campoFecha = tabla.rows[inicioFila].cells[indiceCeldaFecha].childNodes[0];
				if( compare_dates(campoFecha.value, fechaInicioConsulta) && compare_dates(fechaFinConsulta, campoFecha.value)) 
				{
					findFecha = true;
				}

				campoServicio = tabla.rows[inicioFila].cells[indiceCeldaServicio].innerHTML;
				if( tipoServicioConsulta=="" || tipoServicioConsulta == " " || campoServicio == tipoServicioConsulta ){
					findServicio = true;
				}

				campo = tabla.rows[inicioFila].cells[indiceCelda].childNodes[2];
				if( procedimientocioConsulta=="" || procedimientocioConsulta==" " || campo.value == procedimientocioConsulta){
					findProcedimiento = true;
				}

				campoJustificacion = tabla.rows[inicioFila].cells[indiceCeldaJustificacion].childNodes[0];
				if( justificacionConsulta=="" || justificacionConsulta==" " || campoJustificacion.value == justificacionConsulta){
					findJustificacion = true;
				}

				/*
				campoEstado = tabla.rows[inicioFila].cells[indiceCeldaEstado].innerHTML;
				alert(campoEstado+" - "+estadoConsulta);
				if( estadoConsulta=="" || estadoConsulta==" " || campoEstado.value == estadoConsulta ){
					findEstado = true;
				}*/

				if(!findFecha || !findServicio || !findProcedimiento || !findJustificacion) //|| !findEstado)
				{
					tabla.rows[inicioFila].style.display = "none";
				}
				else
				{
					tabla.rows[inicioFila].style.display = "";
				}
			}
			//alert('Encontrado');
		}
}

/*****************************************************************************************************************************
 * Valida antes de llamar la adicion de medico tratante
 ******************************************************************************************************************************/
function adicionarMedico(){	
	if(document.forms.forma.wselmed && document.forms.forma.wselmed.value != ''){
		var selMedico = document.getElementById("wselmed");
		var codigoMatrix = selMedico.value;
		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;		
		var fechaKardex = document.forms.forma.wfechagrabacion.value;
		var usuario = document.forms.forma.usuario.value;
		
		var vecDatosMedico = selMedico.value.split("-");
		var tipoDocumento = "";
		var nroDocumento = "";
		var nombreMedico = selMedico.options[selMedico.selectedIndex].text;
		var codigoEspecialidad = "";
		var idMedico = "";
		
		var tratante = document.getElementById("wchkmedtra").checked ? "on" : "off";
		
		insertarMedicoTratante(idMedico, tipoDocumento, nroDocumento, historia, ingreso, fechaKardex, tratante, usuario, codigoEspecialidad, nombreMedico, codigoMatrix);
	} else {
		alert("Debe seleccionar un médico para poder agregarlo.");
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
function quitarArticulo(idxElemento, tipoProtocolo, celdafila, contenedorArticulo, ctc ){
//	debugger;
	if( ctc ){
		ctc = "LQ";
	}
	
	var tipoTemporal = tipoProtocolo;
	tipoProtocolo = "N";
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
	
	tipoProtocolo = tipoTemporal; 

	var valor = false;
	if(codigoArticulo == ''){

		var cntDetalleKardex = document.getElementById(contenedorArticulo/*+tipoProtocolo*/);
		var filaEliminar = document.getElementById("trFil"+idxElemento);
		cntDetalleKardex.removeChild(filaEliminar);
		//elementosDetalle--;
		
		//el medicamento no se imprime
		try{
			$( "[id^=wchkimp]", filaEliminar )[0].checked = false;
		}
		catch(e){}
		
		//Si se elimina un medicamento entonces lo dejo cómo no modificado
		$( "#wmodificado"+ tipoProtocolo+idxElemento ).val( "N" );
		
		// return true; //Noviembre 19 de 2012
		valor = true;
	} 
	else {
		
		if( !ctc ){
			var msg = "Esta seguro de eliminar el articulo "+ trim(descripcionArticulo) + "?";
		}
		else
		{
			var msg = "Si sale del CTC el medicamento no será tenido en cuenta";
		}
	
		var confQuitar = false;
		if( ctc != "LQ" )
			confQuitar = confirm( msg );
			
		if( ctc == "LQ" || confQuitar )
		{
			//El medicamento no se imprime
			try{
				var filaEliminar = document.getElementById("trFil"+idxElemento);
				$( "[id^=wchkimp]", filaEliminar )[0].checked = false;
				
			}
			catch(e){}
			
			eliminarArticuloElemento(historia,ingreso,fecha,codigoArticulo,usuario,idxElemento,fini,hini,tipoProtocolo,contenedorArticulo);
			
			hayArticulosAImprimir();
			//elementosDetalle--;
			
			// Estas líneas permiten eliminar la fila cuando se elimina desde el CTC
			try{
				celdafila.parentNode.parentNode.style.display = 'none';
			}
			catch(e){}
			
			//Si se elimina un medicamento entonces lo dejo cómo no modificado
			$( "#wmodificado"+ tipoProtocolo+idxElemento ).val( "N" );
			
			// return true; //Noviembre 19 de 2012
			valor = true;
		}
		else{
			// return false;	//Noviembre 19 de 2012
			valor = false;
		}
	}
	
	hayArticulosAImprimir();
	return valor;
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
function quitarExamen( idxElemento, prefijoAlta, nuevoExamen, tipoMensaje ){
//	var codigoExamen = document.getElementById('wexamenlab'+idxElemento).value;
	
	var codigoExamen = document.getElementById('hexcco'+idxElemento).value;
	var fecha = document.forms.forma.wfecha.value;
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;
	
	//HCE
	var consecutivoOrden = document.getElementById("hexcons"+idxElemento).value;
	var consecutivoExamen = document.getElementById("hexcod"+idxElemento).value;
	var nroItem = document.getElementById("hexnroitem"+idxElemento).value;
	var cco = document.getElementById("hexcco"+idxElemento).value;
	var firmForm = document.getElementById("hiFormHce"+idxElemento).value;
	
	var msg = "";
	
	if( !tipoMensaje ){
		var msg = "Esta seguro de eliminar el examen?";
	}
	else{
		if(tipoMensaje!="hce")
			var msg = "Si no llena el CTC el examen no será tenido en cuenta para la orden?";
			
	}
	
	if(msg != "")
	{
		if( confirm( msg ) )
		{
			eliminarExamenElemento(historia,ingreso,codigoExamen,fecha,usuario,consecutivoOrden,consecutivoExamen,idxElemento, nroItem, prefijoAlta, nuevoExamen);

			//////////////////////////////////////////////////////////
			// Elimino los datos del formulario si es una orden asociada a un formulario de HCE
			parametros = "consultaAjaxKardex=51&basedatoshce="+document.forms.forma.wbasedatohce.value+"&wcco="+cco+"&historia="+historia+"&ingreso="+ingreso+"&firmHce="+firmForm+"&wemp_pmla="+document.forms.forma.wemp_pmla.value;

			try{
				ajaxhce=nuevoAjax();
			
				ajaxhce.open("POST", "ordenesidc.inc.php",true);
				ajaxhce.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajaxhce.send(parametros);
				
				ajaxhce.onreadystatechange=function()
				{ 

					if (ajaxhce.readyState==4 && ajaxhce.status==200){
						var formBorrado = ajaxhce.responseText;
						
						if(formBorrado=='ok')
						{
							return true;
						}
					} 
				}
				if ( !estaEnProceso(ajaxhce) ) {
					ajaxhce.send(null);
				}
			}catch(e){	}	
			//////////////////////////////////////////////////////////

			return true;
		}
	}
	else
	{
		eliminarExamenElemento(historia,ingreso,codigoExamen,fecha,usuario,consecutivoOrden,consecutivoExamen,idxElemento, nroItem, prefijoAlta, nuevoExamen);
		return true;
	}
	
	return false;
}

/*****************************************************************************************************************************
 * Define que valor toma el campo a grabar según el campo que se haya modificado
 *****************************************************************************************************************************/
function definirValorCampo(campo0,campo1,campo2)
{
	if( isset(document.getElementById(campo0)) && isset(document.getElementById(campo1)) && isset(document.getElementById(campo2)) )
	{
		var valorCampo0 = document.getElementById(campo0).value;
		var valorCampo1 = document.getElementById(campo1).value;
		var valorCampo2 = document.getElementById(campo2).value;
		
		if(valorCampo1!=valorCampo0)
		{
			document.getElementById(campo1).value = valorCampo1;
			document.getElementById(campo0).value = valorCampo1;
		}
		else if(valorCampo2!=valorCampo0)
		{
			document.getElementById(campo1).value = valorCampo2;
			document.getElementById(campo0).value = valorCampo2;
		}
	}
}

/*****************************************************************************************************************************
 * Debido que para la pestaña de alta se usaron campos que estan en otras pestañas
 * Se crea esta función que determina cual cambió y graba según este cambio
 * Si cambian los dos prevalece el valor del campo de la pestaña original, no la de alta
 *****************************************************************************************************************************/
function unificarCamposArticulos(idxElemento,tipoProtocolo){
	
	definirValorCampo('wdosisori'+tipoProtocolo+idxElemento,'wdosis'+tipoProtocolo+idxElemento,'wdosisimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wudosisori'+tipoProtocolo+idxElemento,'wudosis'+tipoProtocolo+idxElemento,'wudosisimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wffticaori'+tipoProtocolo+idxElemento,'wfftica'+tipoProtocolo+idxElemento,'wffticaimp'+tipoProtocolo+idxElemento);
	
	definirValorCampo('wperiodori'+tipoProtocolo+idxElemento,'wperiod'+tipoProtocolo+idxElemento,'wperiodimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wfinicioori'+tipoProtocolo+idxElemento,'wfinicio'+tipoProtocolo+idxElemento,'wfinicioimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wtxtobsori'+tipoProtocolo+idxElemento,'wtxtobs'+tipoProtocolo+idxElemento,'wtxtobsimp'+tipoProtocolo+idxElemento);
	
}

/*****************************************************************************************************************************
 * Debido que para la pestaña de alta se usaron campos que estan en otras pestañas
 * Se crea esta función que determina cual cambio y graba según este cambio
 * Si cambian los dos prevalece el valor del campo de la pestaña original, no la de alta
 *****************************************************************************************************************************/
function unificarCamposExamenes(idxElemento){
	
	definirValorCampo('wfsolori'+idxElemento,'wfsol'+idxElemento,'wfsolimp'+idxElemento);

	definirValorCampo('wnmexamen'+idxElemento,'wnmexamen'+idxElemento,'wnmexamenimp'+idxElemento);

	definirValorCampo('wtxtjustexamen'+idxElemento,'wtxtjustexamen'+idxElemento,'wtxtjustexamenimp'+idxElemento);
	
}

/*****************************************************************************************************************************
 * Validación de la grabación de un medicamento
 *****************************************************************************************************************************/
function grabarArticulo(idxElemento,tipoProtocolo){
	
	// Debido que para la pestaña de alta se usaron campos que estan en otras pestañas
	// Se crea esta función que determina cual cambio y graba según este cambio
	// Si cambian los dos prevalece el valor del campo de la pestaña original, no la de alta
	unificarCamposArticulos(idxElemento,tipoProtocolo);
	
	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fechaKardex = document.forms.forma.wfechagrabacion.value;
	var centroCostosGrabacion = document.forms.forma.centroCostosGrabacion.value;
	var prioridad = document.forms.forma.whusuariolactario.value;
	
	tipoProtocolo = "N";
	
	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);	
	var datosArticulos = "";
	var cantDosis = document.getElementById('wdosis'+tipoProtocolo+idxElemento);
	var unidadDosis = document.getElementById('wudosis'+tipoProtocolo+idxElemento);	
	var periodicidad = document.getElementById('wperiod'+tipoProtocolo+idxElemento);

	var posologia = document.getElementById('wposologia'+tipoProtocolo+idxElemento);
	var unidadPosologia = document.getElementById('wunidadposologia'+tipoProtocolo+idxElemento);	
	
	var equivHorasFrecuencia = periodicidad.value.substring(1,periodicidad.value.length);
	var condicion = document.getElementById('wcondicion'+tipoProtocolo+idxElemento);
	var formaFtica = document.getElementById('wfftica'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var horaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);

	var impresion = 'off';
	var deAlta = 'off';
	if(isset(document.getElementById('wimp'+tipoProtocolo+idxElemento)))
	{
		var impresion = document.getElementById('wimp'+tipoProtocolo+idxElemento).value;
		deAlta = document.getElementById('wimp'+tipoProtocolo+idxElemento).value;
	}
	
	if(isset(document.getElementById('wchkimp'+tipoProtocolo+idxElemento)))
	{
		if(document.getElementById('wchkimp'+tipoProtocolo+idxElemento).checked==true)
			var impresion = 'on';
		else
			var impresion = 'off';
	}
	
	var manejoInterno = 'on';
	if(isset(document.getElementById('wchkint'+tipoProtocolo+idxElemento)))
	{
		if(document.getElementById('wchkint'+tipoProtocolo+idxElemento).checked==true)
			manejoInterno = 'on';
		else
			manejoInterno = 'off';
	}
	
	//Via de administracion
	if(isset(document.getElementById('wviadmon'+tipoProtocolo+idxElemento)))
		var via = document.getElementById('wviadmon'+tipoProtocolo+idxElemento);
	else if(isset(document.getElementById('wviadmonimp'+tipoProtocolo+idxElemento)))
		var via = document.getElementById('wviadmonimp'+tipoProtocolo+idxElemento);
	else 
		var via = '';
		
	var confirmar = document.getElementById('wchkconf'+tipoProtocolo+idxElemento);
	var diasTto = document.getElementById('wdiastto'+tipoProtocolo+idxElemento);

	//if(deAlta!="on")
		var dosMax = document.getElementById('wdosmax'+tipoProtocolo+idxElemento).value;
	//else
		//var dosMax = "";
	
	var observacion = document.getElementById('wtxtobs'+tipoProtocolo+idxElemento);
	var usuario = document.forms.forma.usuario.value;	
	var primerKardex = (document.getElementById("wkardexnuevo") && document.getElementById("wkardexnuevo").value == 'S' ? "S" : "N");
	
	if( isset(document.getElementById('wcantaltaimp'+tipoProtocolo+idxElemento)) )
		var cantidadAlta = document.getElementById('wcantaltaimp'+tipoProtocolo+idxElemento).value;
	else if(deAlta=="on" && document.getElementById('wdosmax'+tipoProtocolo+idxElemento)!="")
		var cantidadAlta = document.getElementById('wdosmax'+tipoProtocolo+idxElemento).value;
	else
		var cantidadAlta = "";

	var unidadManejo = document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento) ? document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento).value : '';
	var vence = (document.getElementById('whvence'+tipoProtocolo+idxElemento) && document.getElementById('whvence'+tipoProtocolo+idxElemento).value == 'on' ? true : false);
	var cantFracciones = document.getElementById('whcmanejo'+tipoProtocolo+idxElemento) ? document.getElementById('whcmanejo'+tipoProtocolo+idxElemento).value : '';
	
	var artProtocolo = document.getElementById('whartpro'+tipoProtocolo+idxElemento).value;
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
	var origenArticulo = 'SF';
	var nombreArticulo = cd[1];
	
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
		alert('Debe ingresar el codigo del articulo: '+ datosArticulos);
		valido = false;
	}
	
	//Dosis
	if( manejoInterno != "on" ){
		if(valido && (!cantDosis || cantDosis.value == '')){	
			alert('Debe ingresar la cantidad de dosis a suministrar: '+ datosArticulos);
			valido = false;
		}
	}
	
	// if(valido && eval(cantDosis.value) <= 0)
	if( manejoInterno != "on" ){
		if(valido && $.trim( cantDosis.value ) == '' ){	
			alert('La cantidad de dosis debe ser mayor que cero: '+ datosArticulos);
			valido = false;
		}
	}
	
	
	//Unidad de Dosis
	if(valido && (!unidadDosis || unidadDosis.value == '')){
		alert('Debe seleccionar la unidad de dosis a suministrar: '+ datosArticulos);
		valido = false;
	}
	
	if( manejoInterno != "on" ){
		//Periodicidad o frecuencia
		if(valido && (!periodicidad || periodicidad.value == '')){	
			alert('Debe seleccionar la frecuencia con la que se debe suministrar: '+ datosArticulos);
			valido = false;
		}
	}
	
	
	//Forma farmaceutica
	if(valido && (!formaFtica || formaFtica.value == '')){
		formaFtica.value = '00';
//		alert('Debe ingresar la forma farmaceutica');
//		valido = false;
	}
	
	
	//Fecha de inicio del tratamiento
	if(valido && (!fechaInicio || fechaInicio == '')){	
		alert('Debe ingresar la fecha de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}
	
	//Hora de inicio del tratamiento
	if(valido && (!horaInicio || horaInicio == '')){	
		alert('Debe ingresar la hora de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}
	
	if( manejoInterno != "on" ){
		//Via de administracion
		if(deAlta!='on')
		{
			if(valido && (!via || via.value == '')){	
				alert('Debe seleccionar la vía de administración: '+ datosArticulos);
				valido = false;
			}
		}
	}
	
	//La fecha de inicio debe ser mayor o igual a la del kardex
	// 2013-02-06
	// Esta validación salía al grabar aún estando las fechas bien en el formulario
	// Se comenta porque mas abajo se hace la validación correcta de estos datos
	/*
	if(valido && (!esFechaMayorIgual(fechaInicio,fechaKardex))){	
		alert('La fecha de inicio de administración del artículo debe ser mayor o igual a la fecha actual: '+ datosArticulos);
		valido = false;
	}
	*/
	
	
	/*Si la hora actual de validación está entre las cero horas y las cuatro horas, todas las horas son permitidas
	 * Quiere decir esto que desde las seis de la mañana empiezan a haber restricciones de horario
	 */
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
	
	// if(valido && (parseInt(valDiaInicio)-parseInt(new Date().getDate())==0)){
		// if(parseInt(valHoraInicio) == 0 && parseInt(new Date().getHours()) != 0){
			// alert('La fecha de inicio de administración del artículo debe ser mayor o igual a la fecha actual: '+ datosArticulos);
			// valido = false;
		// }
	// }
	
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
			fechaHoraValidacionArticulo2 = document.getElementById('wfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:");	//Abril 26 de 2011
			
			/*
			if(codigoValidacionArticulo[0] == codigoArticulo && fechaHoraValidacionArticulo[0] == fechaInicioAnterior && fechaHoraValidacionArticulo[1].split(":")[0] == horaInicioAnterior.split(":")[0]){
				alert("El articulo "+trim(datosArticulo)+" ya se encuentra en la lista con la misma fecha y hora de inicio, NO SE PUEDE GRABAR");
				valido = false;
				break;
			}
			*/
			/************************************************************************
			 * Abril 26 de 2011
			 * fechaInicio = fh[0];
				horaInicio = fh[1];
			 ************************************************************************/
			/*
			else if(codigoValidacionArticulo[0] == codigoArticulo && fechaHoraValidacionArticulo2[0] == fechaInicio && fechaHoraValidacionArticulo2[1].split(":")[0] == horaInicio.split(":")[0]){
				alert("El articulo "+trim(datosArticulo)+" ya se encuentra en la lista con la misma fecha y hora de inicio, NO SE PUEDE GRABAR");
				valido = false;
				break;
			}
			*/
			/************************************************************************/
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
	
	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		grabarArticuloElemento(historia,ingreso,fechaKardex,codigoArticulo,cantDosis.value,unidadDosis.value,periodicidad.value,formaFtica.value,fechaInicio,horaInicio,via.value,artConfirmado,diasTto.value,observacion.value,origenArticulo,usuario,condicion.value,dosMax,cantGrabar,unidadManejo,cantidadManejo,primerKardex,equivHorasFrecuencia,fechaInicioAnterior,horaInicioAnterior,artNoDispensar,artProtocolo,centroCostosGrabacion,prioridad,idxElemento,nombreArticulo,cantidadAlta,impresion,deAlta,manejoInterno,unidadPosologia.value,posologia.value);
	}
	return valido;
}


 /*****************************************************************************************************************************
  * Validación de la grabación de un medicamento para evitar validacion del dia actual contra inicio del tratamiento
  ******************************************************************************************************************************/ 
function grabarArticuloSinValidacion(idxElemento,tipoProtocolo){

	// Debido que para la pestaña de alta se usaron campos que estan en otras pestañas
	// Se crea esta función que determina cual cambio y graba según este cambio
	// Si cambian los dos prevalece el valor del campo de la pestaña original, no la de alta
	unificarCamposArticulos(idxElemento,tipoProtocolo);
		
	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fechaKardex = document.forms.forma.wfechagrabacion.value;
	var centroCostosGrabacion = document.forms.forma.centroCostosGrabacion.value;
	var prioridad = document.forms.forma.whusuariolactario.value;
	
	tipoProtocolo = "N";
	
	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);	
	var datosArticulos = "";
	var cantDosis = document.getElementById('wdosis'+tipoProtocolo+idxElemento);
	var unidadDosis = document.getElementById('wudosis'+tipoProtocolo+idxElemento);	
	var periodicidad = document.getElementById('wperiod'+tipoProtocolo+idxElemento);
	
	var posologia = document.getElementById('wposologia'+tipoProtocolo+idxElemento);
	var unidadPosologia = document.getElementById('wunidadposologia'+tipoProtocolo+idxElemento);	
	
	var equivHorasFrecuencia = periodicidad.value.substring(1,periodicidad.value.length);
	var condicion = document.getElementById('wcondicion'+tipoProtocolo+idxElemento);
	var formaFtica = document.getElementById('wfftica'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var horaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	
	var impresion = 'off';
	var deAlta = 'off';
	if(isset(document.getElementById('wimp'+tipoProtocolo+idxElemento)))
	{
		var impresion = document.getElementById('wimp'+tipoProtocolo+idxElemento).value;
		var deAlta = document.getElementById('wimp'+tipoProtocolo+idxElemento).value;
	}
	
	if(isset(document.getElementById('wchkimp'+tipoProtocolo+idxElemento)))
	{
		if(document.getElementById('wchkimp'+tipoProtocolo+idxElemento).checked==true)
			var impresion = 'on';
		else
			var impresion = 'off';
	}

	var manejoInterno = 'on';
	if(isset(document.getElementById('wchkint'+tipoProtocolo+idxElemento)))
	{
		if(document.getElementById('wchkint'+tipoProtocolo+idxElemento).checked==true)
			manejoInterno = 'on';
		else
			manejoInterno = 'off';
	}
	
	
	//Via de administracion
	if(isset(document.getElementById('wviadmon'+tipoProtocolo+idxElemento)))
		var via = document.getElementById('wviadmon'+tipoProtocolo+idxElemento);
	else if(isset(document.getElementById('wviadmonimp'+tipoProtocolo+idxElemento)))
		var via = document.getElementById('wviadmonimp'+tipoProtocolo+idxElemento);
	else 
		var via = '';
		
	var confirmar = document.getElementById('wchkconf'+tipoProtocolo+idxElemento);
	var diasTto = document.getElementById('wdiastto'+tipoProtocolo+idxElemento);
	
	//if(deAlta!="on")
		var dosMax = document.getElementById('wdosmax'+tipoProtocolo+idxElemento).value;
	//else
		//var dosMax = "";
	
	var observacion = document.getElementById('wtxtobs'+tipoProtocolo+idxElemento);
	var usuario = document.forms.forma.usuario.value;
	var primerKardex = (document.getElementById("wkardexnuevo") && document.getElementById("wkardexnuevo").value == 'S' ? "S" : "N");
	
	if( isset(document.getElementById('wcantaltaimp'+tipoProtocolo+idxElemento)) )
		var cantidadAlta = document.getElementById('wcantaltaimp'+tipoProtocolo+idxElemento).value;
	else if(deAlta=="on" && document.getElementById('wdosmax'+tipoProtocolo+idxElemento)!="")
		var cantidadAlta = document.getElementById('wdosmax'+tipoProtocolo+idxElemento).value;
	else
		var cantidadAlta = "";

	var unidadManejo = document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento).value;
	var vence = (document.getElementById('whvence'+tipoProtocolo+idxElemento).value == 'on' ? true : false);
	var cantFracciones = document.getElementById('whcmanejo'+tipoProtocolo+idxElemento).value;
	
	var artProtocolo = "N";
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
	var origenArticulo = 'SF';
	var nombreArticulo = cd[1];
	
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
		alert('Debe ingresar el codigo del articulo: '+datosArticulos);
		valido = false;
	}
	
	//Dosis
	if( manejoInterno != "on" ){
		if(valido && (!cantDosis || cantDosis.value == '')){	
			alert('Debe ingresar la cantidad de dosis a suministrar: '+ datosArticulos);
			valido = false;
		}
	}

	if( manejoInterno != "on" ){
		if( valido && $.trim( cantDosis.value ) == '' ){	
			alert('La cantidad de dosis debe ser mayor que cero: '+ datosArticulos);
			valido = false;
		}
	}
	
	//Unidad de Dosis
	if(valido && (!unidadDosis || unidadDosis.value == '')){	
		alert('Debe seleccionar la unidad de dosis a suministrar: '+ datosArticulos);
		valido = false;
	}
	
	//Periodicidad o frecuencia
	if( manejoInterno != "on" ){
		if(valido && (!periodicidad || periodicidad.value == '')){	
			alert('Debe seleccionar la frecuencia con la que se debe suministrar: '+ datosArticulos);
			valido = false;
		}
	}
	
	//Forma farmaceutica
	if(valido && (!formaFtica || formaFtica.value == '')){
		formaFtica.value = '00';
//		alert('Debe ingresar la forma farmaceutica');
//		valido = false;
	}
	
	//Fecha de inicio del tratamiento
	if(valido && (!fechaInicio || fechaInicio == '')){	
		alert('Debe ingresar la fecha de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}
	
	//Hora de inicio del tratamiento
	if(valido && (!horaInicio || horaInicio == '')){	
		alert('Debe ingresar la hora de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}
	
	//Via de administracion
	if( manejoInterno != "on" ){
		if(deAlta!='on' )
		{
			if(valido && (!via || via.value == '')){	
				alert('Debe seleccionar la vía de administración: '+ datosArticulos);
				valido = false;
			}
		}
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
			fechaHoraValidacionArticulo2 = document.getElementById('wfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:");	//Abril 26 de 2011
			
			// if(codigoValidacionArticulo[0] == codigoArticulo && fechaHoraValidacionArticulo[0] == fechaInicioAnterior && fechaHoraValidacionArticulo[1].split(":")[0] == horaInicioAnterior.split(":")[0]){
				// alert("El articulo "+trim(datosArticulo)+" ya se encuentra en la lista con la misma fecha y hora de inicio, NO SE PUEDE GRABAR");
				// valido = false;
				// break;
			// }
			/************************************************************************
			 * Abril 26 de 2011
			 * fechaInicio = fh[0];
				horaInicio = fh[1];
			 ************************************************************************/
			// else if(codigoValidacionArticulo[0] == codigoArticulo && fechaHoraValidacionArticulo2[0] == fechaInicio && fechaHoraValidacionArticulo2[1].split(":")[0] == horaInicio.split(":")[0]){
				// alert("El articulo "+trim(datosArticulo)+" ya se encuentra en la lista con la misma fecha y hora de inicio, NO SE PUEDE GRABAR");
				// valido = false;
				// break;
			// }
			/************************************************************************/
		}
	}

	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		grabarArticuloElemento(historia,ingreso,fechaKardex,codigoArticulo,cantDosis.value,unidadDosis.value,periodicidad.value,formaFtica.value,fechaInicio,horaInicio,via.value,artConfirmado,diasTto.value,observacion.value,origenArticulo,usuario,condicion.value,dosMax,cantGrabar,unidadManejo,cantidadManejo,primerKardex,equivHorasFrecuencia,fechaInicioAnterior,horaInicioAnterior,artNoDispensar,artProtocolo,centroCostosGrabacion,prioridad,idxElemento,nombreArticulo,cantidadAlta,impresion,deAlta,manejoInterno,unidadPosologia.value,posologia.value);
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
 	var nombreArticulo = cd[2];
 	
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
function grabarExamen(idxElemento, grbAut ){ 
	
	if( grbAut == undefined )
		grbAut = true;
	
	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfechagrabacion.value;
	var impExamen = "off";

	// Permite unificar el valor de los campos de la pestaña Alta y la pestaña Otras Ordenes
	unificarCamposExamenes(idxElemento);

	
	 var altExamen = "";
	
	
	// OJO! Se quema porque el contador idxElemento no está encontrando nada pues el campo imprimir_examen cuando se agrega un examen 
	// no está creando este contador, hay que verificar para que lo haga
	//if(isset(document.getElementById('imprimir_examen'+idxElemento)) && document.getElementById('imprimir_examen'+idxElemento).checked)
	//Febrero 25 de 2014
	if( $( "[id^=imprimir_examen]", $( "#wnmexamen" + idxElemento ).parent().parent() )[0].checked ){
		impExamen = "on";
	}
	else{
		impExamen = "off";
	}
	
	var nomExamen = document.getElementById('wnmexamen'+idxElemento).value;
	var observaciones = ""; //document.getElementById('wtxtobsexamen'+idxElemento).value;
	var justificacion = document.getElementById('wtxtjustexamen'+idxElemento).value;
	var estadoExamen = "Pendiente";
	if(document.getElementById('westadoexamen'+idxElemento)){
		estadoExamen = document.getElementById('westadoexamen'+idxElemento).value;
	}
	
	
	var fechaDeSolicitado = document.getElementById('wfsol'+idxElemento).value;
	var usuario = document.forms.forma.usuario.value;
	var existe = false;
	
//	var vecIdExamen = document.getElementById('wexamenlab'+idxElemento).value.split("|");
	var codExamen = document.getElementById('hexcco'+idxElemento).value;
	var consExamen = document.getElementById('hexcod'+idxElemento).value;
	var consecutivoOrden = document.getElementById('hexcons'+idxElemento).value;
	var observacionesOrden = ""; //document.getElementById("wtxtobsexamen"+codExamen+""+consecutivoOrden).value;	
	var numeroItem = document.getElementById("hexnroitem"+idxElemento).value;
	
	// 2012-06-27
	// Se verifica primero si existe el campo para que la variable sea asignada correctamente
	if(isset(document.getElementById("hiReqJus"+idxElemento)))
		var reqJus = document.getElementById("hiReqJus"+idxElemento).value;
	else
		var reqJus = "";
	//	var observacionesOrden = "";
	var firma = document.getElementById("whfirma").value;

	/*****
	VALIDACION DE CAMPOS OBLIGATORIOS
	******/
	var valido = true;
	
	if(codExamen == ''){
		alert("Debe especificar un examen");
		valido = false;
	}
	
	/*
	if(document.getElementById('wnmexamen'+idxElemento).type == "text" && observacionesOrden == ""){
		alert("Para grabar la orden por primera vez, debe especificar observaciones");
		valido = false;
	}
	*/
	
//	if( justificacion == "" && codExamen != "2251" && codExamen != "3081" ){
	if( (justificacion == "" || justificacion == " " || justificacion == "." ) && reqJus == "on" ){
		alert( "Especificar una justificación para la ayuda o procedimiento: "+nomExamen );
		valido = false;
	}
	
	var firmHCE = $( "#hiFormHce" + idxElemento ).val();
	
	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		if(!existe){
			grabarExamenElemento(codExamen,nomExamen,historia,ingreso,fecha,observaciones,estadoExamen,fechaDeSolicitado,usuario,consecutivoOrden,firma,observacionesOrden,consExamen,justificacion,idxElemento,numeroItem,impExamen,firmHCE,altExamen,grbAut);
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
   	
   	//Abril 25 de 2011
   	if(  document.forms.forma.wservicio ){
   		var ccoPaciente = document.forms.forma.wservicio.value;
   	}
   	else{
   		var ccoPaciente = '';
   	}
   	
	/*Tipos de protocolos
	 * N:  Normal, el articulo se consulta normalmente.  (maestro de la central y maestro del servicio)
	 * A:  Analgesia, el articulo se agrega a los protocolos de analgesia (tipo especial de articulo A)
	 * U:  nUtricion, el articulo se agrega a los protocolos de nutricion (tipo especial de articulos U)
	 * Q:  Quimioterapia, el articulo se agrega a los protocolos de quimioterapia (tipo especial de articulos Q)
	 */
//	alert(tipoProtocolo);
	if(document.forms.forma.wnommed.value == ''){
		parametros = "consultaAjaxKardex=02&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo="+document.forms.forma.wcodmed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos+"&tipoProtocolo="+tipoProtocolo+"&ccoPaciente="+ccoPaciente; 
	} else {
		parametros = "consultaAjaxKardex=03&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre="+document.forms.forma.wnommed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos+"&tipoProtocolo="+tipoProtocolo+"&ccoPaciente="+ccoPaciente;
	}

//	alert(parametros);
	
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
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

//	alert(parametros);
	
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
	
		ajax.open("POST", "ordenesidc.inc.php",true);
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
	
		ajax.open("POST", "ordenesidc.inc.php",true);
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
	
//	alert(parametros);
	
	try{
		//$.blockUI({ message: $('#msjEspere') });		
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		//Esta funcion queda sin uso por que el ajax es sincrono
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
//				alert(mensaje);
//				$.growlUI(mensaje);
			}
			
			if(actualizaIntervalos){
				if(document.getElementById('cntEsquemaActual')){
					document.getElementById('cntEsquemaActual').style.display = "none";
				}
			}
			
			if(document.getElementById('btnQuitarEsquema')){
				document.getElementById('btnQuitarEsquema').disabled = false;
			}
			//$.unblockUI();
			//$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * 
 ******************************************************************************************************************************/
function cancelarOrdenElemento(historia,ingreso,fecha,usuario,centroCostos,consecutivoOrden){
	//debugger;
	var parametros = "consultaAjaxKardex=28&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
					+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&fecha="+fecha
					+"&centroCostos="+centroCostos+"&consecutivoOrden="+consecutivoOrden;
	
	var mensaje = "";
	
	try{
		$.blockUI({ message: $('#msjEspere') });		
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function()
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				//alert( "....."+ajax.responseText );
				switch (ajax.responseText) {
					case '1':
						//debugger;
						var contenedorCentroCostos = document.getElementById("detExamenes"+centroCostos);
						
						var tabla = contenedorCentroCostos.parentNode;
						var celdaExamen = document.getElementById("del"+centroCostos+""+consecutivoOrden);
						
						var totalExamenes = celdaExamen.rowSpan;
						var index = celdaExamen.parentNode.rowIndex;
						
						for( var i = 0; i < totalExamenes; i++ ){
							// grabarExamenADetalle( $( "[id^=wnmexamen]" , tabla.rows[ index ] ).attr( "id" ).substr( 9 ), '4' );
							contenedorCentroCostos.removeChild( tabla.rows[ index ] );
						}
						
						hayArticulosAImprimir();
						
						mensaje = "Orden cancelada con exito";
						
//						document.getElementById(centroCostos+""+consecutivoOrden).innerHTML = "<b>Orden cancelada...</b>";
//						var contenedorCentroCostos = document.getElementById(centroCostos);
//						contenedorCentroCostos.removeChild(document.getElementById("del"+centroCostos+""+consecutivoOrden));
//						mensaje = "Orden cancelada con exito";
						
						break;
					default:
						mensaje = "No se pudo cancelar la orden";
						break;
				}
//				alert(mensaje);
//				$.growlUI(mensaje);
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
 * 
 ******************************************************************************************************************************/
function consultarOrdenes(wemp_pmla,wempresa,wbasedato,whis,wing,wfecini,wfecfin,wtiposerv,wprocedimiento,westadodet){

	//debugger;
	var parametros = "consultaAjaxKardex=33&wemp_pmla="+wemp_pmla+"&wempresa="+wempresa+"&wbasedato="+wbasedato
					+"&whis="+whis+"&wing="+wing+"&wfecini="+document.forms.forma.wfecini.value+"&wfecfin="+document.forms.forma.wfecfin.value+"&wtiposerv="+document.forms.forma.wtiposerv.value+"&wprocedimiento="+document.forms.forma.wprocedimiento.value+"&westadodet="+westadodet;
	//alert(parametros);
	var mensaje = "";
	//alert(parametros);
	try{
		$.blockUI({ message: $('#msjEspere') });		
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function()
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				var contenedorDetOrdenes = document.getElementById("detOrdenesRealizadas");
				
				contenedorDetOrdenes.innerHTML=ajax.responseText;
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
 * 
 ******************************************************************************************************************************/
function grabarOrdenElemento(historia,ingreso,fecha,usuario,centroCostos,consecutivoOrden,observacionesOrden){
//	debugger;
	var parametros = "consultaAjaxKardex=29&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
				+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&fecha="+fecha
				+"&centroCostos="+centroCostos+"&consecutivoOrden="+consecutivoOrden+"&observacionesOrden="+observacionesOrden;

	var mensaje = "";

	//alert(parametros);

	try{
		$.blockUI({ message: $('#msjEspere') });		
		ajax=nuevoAjax();

		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){
				switch (ajax.responseText) {
					case '1':
						mensaje = "Observaciones de la orden actualizada con exito";
					break;
					default:
						mensaje = "No se actualizar la observacion de la orden";
					break;
				}
				//alert(mensaje);
				//$.growlUI(mensaje);
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if(!estaEnProceso(ajax)){
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamada ajax para inserción del medico tratante
 ******************************************************************************************************************************/
function insertarMedicoTratante(idRegistro, tipoDocumento, nroDocumento, historia, ingreso, fechaKardex, tratante, usuario, codigoEspecialidad, nombreMedico, codigoMatrix){
	var contenedor = document.getElementById('cntMedicos');
	var parametros = ""; 
		
	parametros = "consultaAjaxKardex=06&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoDocumento="+tipoDocumento
			+"&numeroDocumento="+nroDocumento+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&idRegistro="+idRegistro
			+"&fecha="+fechaKardex+"&tratante="+tratante+"&codigoEspecialidad="+codigoEspecialidad+"&codigoMatrix="+codigoMatrix;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{
				switch (ajax.responseText){
					case '1':	
						alert('El medico ya se encontraba asociado');
						break;
					case '2':
						var subIdMedico = codigoMatrix;
						
						if(tratante == "on"){
							
							var text = "<span id='Med"+subIdMedico+"' class='vinculo'>";
							
							//Vinculo de quitar medico
							var atr = new Array();
							atr['onClick'] = "javascript:quitarMedico('"+codigoMatrix+"');";
							text += crearCampo("8","","5.3",atr,nombreMedico+" (Tratante)");
							text += "<br/></span>";
							
							contenedor.innerHTML += text;
								
								
//							contenedor.innerHTML += "<span id='Med"+subIdMedico+"' class='vinculo'>";
////							contenedor.innerHTML += "<a href='#null' onClick='javascript:quitarMedico("+"\""+codigoMatrix+"\""+");'>" + nombreMedico + "</a> (Tratante)";
//							
//							//Vinculo de quitar medico
//							var atr = new Array();
//							atr['onClick'] = "javascript:quitarMedico('"+codigoMatrix+"');";
//							contenedor.innerHTML += crearCampo("8","","5.3",atr,nombreMedico+" (Tratante)");
//							contenedor.innerHTML += "<br/></span>";
						} else {
							
							var text = "<span id='Med"+subIdMedico+"' class='vinculo'>";
							
							//Vinculo de quitar medico
							var atr = new Array();
							atr['onClick'] = "javascript:quitarMedico('"+codigoMatrix+"');";
							text += crearCampo("8","","5.3",atr,nombreMedico);
							text += "<br/></span>";
							
							contenedor.innerHTML += text;
							
							
//							contenedor.innerHTML += "<span id='Med"+subIdMedico+"' class='vinculo'>";
////							contenedor.innerHTML += "<a href='#null' onClick='javascript:quitarMedico("+"\""+codigoMatrix+"\""+");'>" + nombreMedico + "</a>";
//							
//							//Vinculo de quitar medico
//							var atr = new Array();
//							atr['onClick'] = "javascript:quitarMedico('"+codigoMatrix+"');";
//							contenedor.innerHTML += crearCampo("8","","5.3",atr,nombreMedico);
//							contenedor.innerHTML += "<br/></span>";
							
//							alert( contenedor.innerHTML );
						}
						break;
					case '3':	
						alert('No pueden haber dos o mas medicos tratantes (responsables) al tiempo');
						break;
					default:
						alert('No se pudo asociar el medico');
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
		
		ajax.open("POST", "ordenesidc.inc.php",true);
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

//	alert(parametros);
	var mensaje = "";
					
	try{
//		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",false); 
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


function grabarIndicaciones(indicaiones,fechaKardex,historia,ingreso){
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=41&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&wfecha="+fechaKardex+"&windicaiones="+indicaiones+"&wemp_pmla="+document.forms.forma.wemp_pmla.value;
		
//	alert(parametros);
	
	try{
//		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",false); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		respuesta = ajax.responseText;
		if(respuesta=='0')
			alert("Las indicaciones no han sido guaradadas, verifique los datos.");
			
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}	
		
}


function cambiarDisplay(id) {
	if(isset(document.getElementById(id)))
	{
	  fila = document.getElementById(id);
	  if (fila.style.display != "none") {
		fila.style.display = "none"; //ocultar fila
	  }
	}
}

/*****************************************************************************************************************************
 * Llamada ajax para la inserción o modificación de un articulo
 ******************************************************************************************************************************/
function grabarArticuloElemento(historia,ingreso,fechaKardex,cdArt,cntDosis,unDosis,per,fftica,fini,hini,via,conf,dtto,obs,origenArticulo,usuario,condicion,dosMax,cantGrabar,unidadManejo,cantidadManejo,primerKardex,horasFrecuencia,fechaInicioAnt,horaInicioAnt,noDispensar,tipoProtocolo,centroCostosGrabacion,prioridad,idElemento,nombreArticulo,cantidadAlta,impresion,deAlta,manejoInterno,unidadPosologia,posologia){
	var parametros = "";
	var mensaje = "";
		
	parametros = "consultaAjaxKardex=01&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&codArticulo="+cdArt+"&cantDosis="+cntDosis+"&unDosis="+unDosis+"&per="+per+"&fmaFtica="+fftica+"&fini="+fini+"&hini="+hini+"&dosMax="+dosMax
		+"&via="+via+"&conf="+conf+"&dtto="+dtto+"&obs="+obs+"&codUsuario="+usuario+"&fechaKardex="+fechaKardex+"&origenArticulo="+origenArticulo+"&condicion="+condicion+"&cantGrabar="+cantGrabar
		+"&unidadManejo="+unidadManejo+"&cantidadManejo="+cantidadManejo+"&primerKardex="+primerKardex+"&horasFrecuencia="+horasFrecuencia+"&fIniAnt="+fechaInicioAnt.replace(" ","")+"&hIniAnt="+horaInicioAnt
		+"&noDispensar="+noDispensar+"&tipoProtocolo="+tipoProtocolo+"&centroCostosGrabacion="+centroCostosGrabacion+"&prioridad="+prioridad
		+"&nombreArticulo="+nombreArticulo+"&wcantidadAlta="+cantidadAlta+"&wimpresion="+impresion+"&walta="+deAlta+"&wmanejo="+manejoInterno+"&wposologia="+posologia+"&wunidadposologia="+unidadPosologia;
		

	try{
//		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",false); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		respuesta = ajax.responseText.split("*");
//		$.unblockUI();
		
		
		switch(respuesta[0]){
			case '0':
				mensaje = "No se pudo guardar la informacion del articulo";
				alert(mensaje);
				break;
			case '1':
				tipoProtocolo = "N";
				var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);
				
				if( !document.getElementById( "whfinicio"+tipoProtocolo+idElemento ) ){
					var fre2 = document.createElement( "input" );
					fre2.id = "whfinicio"+tipoProtocolo+idElemento;
					fre2.value = document.getElementById( "wfinicio"+tipoProtocolo+idElemento ).value;
					fre2.style.display = 'none';
					
					elemento.parentNode.parentNode.appendChild( fre2 );
				}
				
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
				
				if( arCTCArticulos[ cdArt ] && arCTCArticulos[ cdArt ] > 0 ){
				
					//Resto uno a la cantidad grabada
					//Esto para que graba justo con el ultimo articulo
					arCTCArticulos[ cdArt ]--;
					
					if( arCTCArticulos[ cdArt ] == 0 ){
						grabarAjaxArticulos( cdArt, tipoProtocolo, idElemento );
					}
				}
				
				//Resalto lo grabado
				var fila = document.getElementById('tr'+tipoProtocolo+idElemento);
				fila.className = 'fila1';
				
				mensaje = "El articulo se ha creado correctamente"; 
				//	$.growlUI('',mensaje);
				
				try{
					$( "#wmodificado"+tipoProtocolo+idElemento ).val( "N" );
				}
				catch(e){}

				break;
			case '2':
				tipoProtocolo = "N";
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
				
				try{
					$( "#wmodificado"+tipoProtocolo+idElemento ).val( "N" );
				}
				catch(e){}
				
//				$.growlUI('',mensaje);
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
//			alert(fini + " a las:" + hini);
			document.getElementById("whfinicio"+tipoProtocolo+idElemento).value = fini + " a las:" + hini;
		}
		
//		ajax.onreadystatechange=function() 
//		{
//			if (ajax.readyState==4 && ajax.status==200)
//			{ 
//				respuesta = ajax.responseText.split("*");
////				$.unblockUI();
//				switch(respuesta[0]){
//					case '0':
//						mensaje = "No se pudo guardar la informacion del articulo";
//						alert(mensaje);
//						break;
//					case '1':
//						var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);
//						
//						var texto = ""; 
//						if(elemento.tagName == "DIV"){
//							texto = elemento.innerHTML;	
//						} else {
//							texto = elemento.value;
//						}
//						var col = document.getElementById("wcolmed"+tipoProtocolo+idElemento);
//						
//						if(col){
//							col.innerHTML = "<div id='wnmmed"+tipoProtocolo+idElemento+"' class='campo2'>"+texto+"</div>";
//						}
//						
//						//Resalto lo grabado
//						var fila = document.getElementById('tr'+tipoProtocolo+idElemento);
//						fila.className = 'fila1';
//						
//						mensaje = "El articulo se ha creado correctamente"; 
//						$.growlUI('',mensaje);
//						break;
//					case '2':
//						var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);
//						var texto = ""; 
//							
//						if(elemento.tagName == "DIV"){
//							texto = elemento.innerHTML;	
//						} else {
//							texto = elemento.value;
//						}
//						
//						var col = document.getElementById("wcolmed"+tipoProtocolo+idElemento);
//
//						if(col && texto != "undefined"){
//							col.innerHTML = "<div id='wnmmed"+tipoProtocolo+idElemento+"' class='campo2'>"+texto+"</div>";
//						}
//						
//						mensaje = "El articulo se ha modificado correctamente";
//						$.growlUI('',mensaje);
//						break;
//					case '3':
//						mensaje = "El articulo no se puede modificar si se encuentra suspendido.";
//						alert(mensaje);
//						break;
//					case '4':
//						mensaje = "El articulo se modificó correctamente, tenga en cuenta que ya estaba dispensado completamente.";
//						var diferencia = (respuesta[1] ? respuesta[1] : 0) - (respuesta[2] ? respuesta[2] : 0);
//						mensaje += "\n-Se genero un ";
//						mensaje += (diferencia >= 0) ? "sobrante de " : "faltante de ";
//						mensaje += Math.abs(diferencia);
//						alert(mensaje);
//						break;
//					default:
//						mensaje = "No especificado: "+ajax.responseText;
//						alert(mensaje);
//						break;
//				}
//				
//				//Para las fechas anteriores cuando se crean los articulos
//				if(document.getElementById("whfinicio"+tipoProtocolo+idElemento)){
////					alert(fini + " a las:" + hini);
//					document.getElementById("whfinicio"+tipoProtocolo+idElemento).value = fini + " a las:" + hini;
//				}			
//			}
//		}
//		alert("Grabe");
		$.unblockUI();
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
 			+"&formaFarm="+formaFarm+"&origen="+origen;
 	} else {
 		parametros = "consultaAjaxKardex=17&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
			+"&codArticulo="+cdArt+"&dtto="+dtto+"&obs="+obs+"&codUsuario="+usuario+"&fechaKardex="+fechaKardex+"&via="+via+"&dosisMaximas="+dosmax+"&prioridad="+prioridad+"&fechaInicio="+fechaInicio
			+"&horaInicio="+horaInicio+"&autorizadoCtc="+autorizadoCtc;
 	}	
 	
 	try{
 		$.blockUI({ message: $('#msjEspere') });
 		ajax=nuevoAjax();
 		
 		ajax.open("POST", "ordenesidc.inc.php",true); 
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
									break;
								}
								cont1++;
							}
						} else {
							dosis.innerHTML = "Sin unidad de medida";							
						}						
						
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
	var fila = document.getElementById('trFil'+idxElemento);
	var estaSuspendido = 'on';
	
	tipoProtocolo = "N";
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
		
		ajax.open("POST", "ordenesidc.inc.php",true);
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
function grabarExamenElemento(codExamen,nomExamen,historia,ingreso,fecha,observaciones,estadoExamen,fechaDeSolicitado,usuario,consecutivoOrden,firma,observacionesOrden,consecutivoExamen,justificacion,idElemento, nroItem,impExamen,firmHCE,altExamen,grbAut){
	var parametros = "";
	var mensaje = "";
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
		
	parametros = "consultaAjaxKardex=7&wemp_pmla="+wemp_pmla+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&fecha="+fecha+"&codigoExamen="+codExamen+"&observaciones="+observaciones+"&estado="+estadoExamen+"&codUsuario="+usuario+"&nombreExamen="+nomExamen
		+"&fechaDeSolicitado="+fechaDeSolicitado+"&consecutivoOrden="+consecutivoOrden+"&firma="+firma+"&observacionesOrden="+observacionesOrden+"&consecutivoExamen="+consecutivoExamen
		+"&justificacion="+justificacion+"&numeroItem="+nroItem+"&impExamen="+impExamen+"&altExamen="+altExamen+"&firmHCE="+firmHCE;
	
	try{

		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",false); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
//		alert( "......."+ajax.responseText );
		
		var grabado = false;	//Noviembre 08 de 2012
		
		//Noviembre 08 de 2012
		var rpAjax = ajax.responseText.split( "|" );

		if( rpAjax[0] == '0' ){
			mensaje = "No se pudo grabar el examen";
		} 
		
		if( rpAjax[0] == '1'){
			//Resalto lo grabado
			var fila = document.getElementById('trEx'+idElemento);
			
			if( grbAut ){
				if( fila.className.toLowerCase() != 'fila1' && fila.className.toLowerCase() != 'fila2' )
					fila.className = 'fila1';
			}
			
			mensaje = "El examen se ha grabado correctamente";
			
			grabado = true;
		}
		
		if( rpAjax[0] == '2'){
			mensaje = "El examen se ha modificado correctamente";
			
			var fila = document.getElementById('trEx'+idElemento);
			
			if( grbAut ){
				if( fila.className.toLowerCase() != 'fila1' && fila.className.toLowerCase() != 'fila2' )
					fila.className = 'fila1';
			}
			
			grabado = true;
		}
		
		//Noviembre 08 de 2012
		if( grabado ){
			
			//Si queda grabado y el examen es nuevo (nro de item == 0 ) le asigno el nro de item
			if( document.getElementById("hexnroitem"+idElemento).value == 0 ){
				document.getElementById("hexnroitem"+idElemento).value = rpAjax[2];
				document.getElementById("hexcons"+idElemento).value = rpAjax[1];
				var auxTdNroOrden = $( "#del" + codExamen + consecutivoOrden );
				$( "b", auxTdNroOrden ).eq(0).html( "Orden Nro. " + rpAjax[1] );
				$( "#del" + codExamen + consecutivoOrden )[0].id = "del"+codExamen+rpAjax[1];
			}
		
			if( grbAut ){	//Es verdadero si se llama desde los botones de impresión o grabar
				grabarAjaxProcedimiento( codExamen, rpAjax[1], rpAjax[2], idElemento, wemp_pmla  );
			}
		}
		
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
	 		
 		ajax.open("POST", "ordenesidc.inc.php",true); 
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
		
		ajax.open("POST", "ordenesidc.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function(){ 
			if (ajax.readyState==4 && ajax.status==200){
				var prueba = ajax.responseText;
				if(ajax.responseText == 1){
					var cntMedicos = document.getElementById("cntMedicos");
					var filaEliminar = document.getElementById("Med"+idxMedico);
				 
				 	cntMedicos.removeChild(filaEliminar);
				 	
				 	mensaje = "El medico se ha retirado con exito";
				 }
			 
			 //No pudo eliminar
			 if(ajax.responseText == 0){
				 mensaje = 'No se pudo retirar el medico';
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
		
		ajax.open("POST", "ordenesidc.inc.php",true); 
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
		
		ajax.open("POST", "ordenesidc.inc.php",true); 
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
function eliminarArticuloElemento(historia,ingreso,fecha,cdArt,usuario,idx,fini,hini,tipoProtocolo,contenedorArticulo){
//	debugger; 

	//Consulto la fecha y hora de inicio del medicamento si no está definido
	if( !fini ){
		var aux = $( "#wfinicioN"+idx ).val().split( " a las:" );
		fini = aux[0];
		hini = aux[1]+":00";
	}

	var parametros = "consultaAjaxKardex=04&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
			+"&historia="+historia+"&ingreso="+ingreso+"&codArticulo="+cdArt+"&fecha="+fecha+"&codUsuario="+usuario+"&fechaInicio="+fini+"&horaInicio="+hini
			+"&tipoProtocolo="+tipoProtocolo;

					
	try{
		//$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{	
				var cntDetalleKardex = document.getElementById(contenedorArticulo);
				var filaEliminar = document.getElementById("trFil"+idx);
				if( cntDetalleKardex && filaEliminar ) 
					cntDetalleKardex.removeChild(filaEliminar);
				
				hayArticulosAImprimir();
			}
			//$.unblockUI();
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
		
		ajax.open("POST", "ordenesidc.inc.php",true); 
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
function eliminarExamenElemento(historia,ingreso,codigoExamen,fecha,usuario,consecutivoOrden,consecutivoExamen,idx, nroItem, prefijoAlta, nuevoExamen){
//	debugger;
	var parametros = "";
	var mensaje = "";
	
	parametros = "consultaAjaxKardex=8&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
			+"&codExamen="+codigoExamen+"&codUsuario="+usuario+"&consecutivoOrden="+consecutivoOrden+"&consecutivoExamen="+consecutivoExamen+"&numeroItem="+nroItem;
	
	prefijoAltaLower = prefijoAlta.toLowerCase();
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",true); 
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{  
//				alert( "......"+ajax.responseText );
				//Eliminado correctamente
				if( ajax.responseText == 1 ){
					var centroCostosExamen = document.getElementById("hexcco"+prefijoAltaLower+idx).value;
					var consecutivoOrden = document.getElementById("hexcons"+prefijoAltaLower+idx).value;

					//if(nuevoExamen!='on')
						var cntExamenes = document.getElementById("detExamenes"+centroCostosExamen+prefijoAlta);
					//else
						//var cntExamenes = document.getElementById("encabezadoExamenes");
					

					var filaEliminar = document.getElementById("trEx"+prefijoAlta+idx);
					var celdaExamen = document.getElementById("del"+prefijoAlta+centroCostosExamen+consecutivoOrden);
					
					var numRows = celdaExamen.rowSpan;

					if( celdaExamen.rowSpan > 1 &&	 filaEliminar.cells[0] == celdaExamen ){
						cntExamenes.parentNode.rows[ filaEliminar.rowIndex+1 ].insertBefore( celdaExamen, cntExamenes.parentNode.rows[ filaEliminar.rowIndex+1 ].cells[0] );
					}
					
					celdaExamen.rowSpan = numRows;
					
					if( numRows > 1 ){
						numRows--
						celdaExamen.rowSpan = numRows;
					}
					
				 	cntExamenes.removeChild(filaEliminar);
				 	
					hayArticulosAImprimir();
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
		
		ajax.open("POST", "ordenesidc.inc.php",true);
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
					/*
					if(document.getElementById('btnEsquema')){
						document.getElementById('btnEsquema').disabled = true;
					}
					*/
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



var response = null

function prompt2(prompttitle, message, sendto, strTratamientos) {

	var arrTratamientos = new Array();
	arrTratamientos = strTratamientos.split('|');
									
	promptbox = document.createElement('div');
	promptbox.setAttribute ('id' , 'prompt')
	document.getElementsByTagName('body')[0].appendChild(promptbox)
	promptbox = eval("document.getElementById('prompt').style")
	promptbox.position = 'absolute'
	promptbox.top = 40
	promptbox.left = 40
	promptbox.width = 470
	promptbox.border = '2 solid #6E6E6E'
	promptbox.display = 'none'

	document.getElementById('prompt').innerHTML = "<table cellspacing='0' cellpadding='0' border='0' width='100%'><tr valign='middle'><td height='27' class='encabezadoTabla'> &nbsp; " + prompttitle + "</td></tr>" 
	
	document.getElementById('prompt').innerHTML = document.getElementById('prompt').innerHTML + "<tr><td align='left' class='fila2'><br>" + message + "</td></tr><tr><td align='left' class='fila2'><br><br><select name='promptbox' id='promptbox'><option>*</option></select> &nbsp;&nbsp;&nbsp;&nbsp; <input type='button' class='prompt' value='OK' onMouseOver='this.style.border=\"1 outset #dddddd\"' onMouseOut='this.style.border=\"1 solid transparent\"' onClick='" + sendto + "(document.getElementById(\"promptbox\").value); document.getElementsByTagName(\"body\")[0].removeChild(document.getElementById(\"prompt\"))'> </td></tr></table>"

	creandoOptionsValue( document.getElementById( "promptbox" ), arrTratamientos );	
	
	//document.getElementById("promptbox").focus()
	document.getElementById("prompt").focus()

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
function seleccionarArticulo(codigo, nombreComercial, nombreGenerico, origen, grupo, forma, unidad, pos, unidadFraccion, cantFracciones, 
		vencimiento, diasVencimiento, dispensable, duplicable, diasMaximos, dosisMaximas, via, tipoProtocolo, tipoMedLiquido, esGenerico, 
		abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija, condicionSuministroFija,
		confirmadaPreparacionFija,diasMaximosFija,dosisMaximasFija,observacionesFija,componentesTipo,noEnviar,tipoManejo,unidadPosologia,posologia,deAlta){
	
	var parametros = ""; 
				
	parametros ="consultaAjaxKardex=52&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&articulo="+codigo; 

	var nombreArticuloGrabar =  nombreComercial.replace(/_/g," ");	
	
	if(!deAlta)
		deAlta = "off";
	
	var tipoProtocoloAux = "";
	var i = 0, idx = 0;
	
	tipoProtocoloAux = tipoProtocolo;
	tipoProtocoloAux = "N";
	
//	switch (tipoProtocolo) {
	switch (tipoProtocoloAux) {
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
			idx = elementosDetalle-1;
		
			posicionActual = elementosDetalle;
		break;
	}
	
	var mFechaInicio = "";

	var elementoAnteriorDetalle = document.getElementById("wnmmed"+tipoProtocoloAux+idx);
	var cntDetalleKardex = document.getElementById("detKardexAdd" + tipoProtocoloAux);
	
	var idTabla = "tbDetalle" + tipoProtocoloAux;
	
	stopEvent(window.onbeforeunload);
	
	if(posicionActual > 0){
		var elemento = elementoAnteriorDetalle;
		//forma = forma.toString();
		
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
			
			if(duplicable == 'off' && existe){
				adicionar = true;
			}
			
			if(horarioEspecial)
				contHorarioEspecial++;
			
			nombreArticuloAlert =  trim(nombreComercial.replace(/_/g," "));
			if(existe && !horarioEspecial){
				if(!confirm('El medicamento ' + nombreArticuloAlert + ' ya existe. Desea agregarlo?'))
				{
					var trEliminar = document.getElementById("trFil"+idx);
					cntDetalleKardex.removeChild(trEliminar);
					
					return false;
				}
			}	

			if(existe && horarioEspecial && contHorarioEspecial==1){
				if(!confirm('El medicamento ' + nombreArticuloAlert + ' ya existe. Desea agregarlo?'))
				{
					var trEliminar = document.getElementById("trFil"+idx);
					cntDetalleKardex.removeChild(trEliminar);
					
					return false;
				}
			}	
			
			tipoManejo = $.trim(tipoManejo);
			
			if(!adicionar){
				
				var mCantDosis = cantFracciones;
				var mUnidadDosis = unidadFraccion;
				var mDiasTto = diasMaximos;
				var mDosisTto = dosisMaximas;
				var mVia = via;
				
				var mNoEnviar = "";
				var mFrecuencia = "";
				var mCondicion = "";
				var mConfirmaPreparacion = "";
				var mObservaciones = "";
				
				mNoEnviar = noEnviarFija;
				mFrecuencia = frecuenciaFija;
				mCondicion = condicionSuministroFija;
				mConfirmaPreparacion = confirmadaPreparacionFija;
				mObservaciones = observacionesFija;

				//Marca de no enviar
				if(document.getElementById("wchkdisp"+tipoProtocoloAux+idx))
				{
					document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = false;

					//Enero 29 de 2013
					// Si es un insumo no dispensable marquese como no enviar
					if(mNoEnviar == "on")
						document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = true;
				
					//Abril 25 de 2011
					//Si es ustock marquese como no enviar
					if( noEnviar == 'on' )
						document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = true;
				}
				
				//Frecuencia
				if(document.getElementById("wperiod"+tipoProtocoloAux+idx) && mFrecuencia != ""){
					document.getElementById("wperiod"+tipoProtocoloAux+idx).value = mFrecuencia;
					
					//Busco si la frecuencia es oncologica o no para ocultar los select necesarios
					var selFre = document.getElementById("wperiod"+tipoProtocoloAux+idx);
					if( $( selFre.options[ selFre.selectedIndex ] ).hasClass( "cli" ) ){
						$( ".onc", selFre ).hide();
					}
					else{
						$( ".cli", selFre ).hide();
					}
				}
				
				//Condicion
				if(document.getElementById("wcondicion"+tipoProtocoloAux+idx) && mCondicion != ""){
					document.getElementById("wcondicion"+tipoProtocoloAux+idx).value = mCondicion;
				}
				
				//Confirma preparación
				if(origen == 'CM' && document.getElementById("wchkconf"+tipoProtocoloAux+idx) && mConfirmaPreparacion != ""){
					if(mConfirmaPreparacion == "on"){
						document.getElementById("wchkconf"+tipoProtocoloAux+idx).checked = true;
					} else {
						document.getElementById("wchkconf"+tipoProtocoloAux+idx).checked = false;
					}
				}
				
				//Observaciones
				if(document.getElementById("wtxtobs"+tipoProtocoloAux+idx)&& mObservaciones != ""){
					document.getElementById("wtxtobs"+tipoProtocoloAux+idx).value = mObservaciones;
				}
				
				//Tipo de Manejo (Interno/Externo)
				if(document.getElementById("wchkint"+tipoProtocoloAux+idx)){
					if(tipoManejo == "on" || tipoManejo == "On"){
						document.getElementById("wchkint"+tipoProtocoloAux+idx).checked = true;
						
						$( "#wdosis"+tipoProtocoloAux+idx ).attr( "onkeypress","" );
						
						
						
						
						
						
						var auxDosis = document.getElementById( "wdosis"+tipoProtocoloAux+idx );
						
						var cmpTD = auxDosis.parentNode;
						
						var newCmp = document.createElement( "input" );
						newCmp.type = 'text';
						newCmp.size = 3;
						newCmp.id = "wdosis"+tipoProtocoloAux+idx;
						newCmp.name = "wdosis"+tipoProtocoloAux+idx;
						newCmp.className = "textoNormal";
						$( newCmp ).val( auxDosis.value );
						
						//remuevo el campo que había
						cmpTD.removeChild( auxDosis );
						
						//agrego el campo nuevo
						cmpTD.insertBefore( newCmp, cmpTD.firstChild );
						
							
						
						
					} else {
						document.getElementById("wchkint"+tipoProtocoloAux+idx).checked = false;
												
						var auxDosis = document.getElementById( "wdosis"+tipoProtocoloAux+idx );
						
						var cmpTD = auxDosis.parentNode;
						
						var newCmp = document.createElement( "textarea" );	
						newCmp.id = "wdosis"+tipoProtocoloAux+idx;
						newCmp.name = "wdosis"+tipoProtocoloAux+idx;
						newCmp.className = "textoNormal";
						$( newCmp ).val( auxDosis.value );
						
						//remuevo el campo que había
						cmpTD.removeChild( auxDosis );
						
						// //agrego el campo nuevo
						// cmpTD.appendChild( newCmp );
						
						//agrego el campo nuevo
						cmpTD.insertBefore( newCmp, cmpTD.firstChild );
						
					}
				}
				
				//Si el articulo tiene parametros preestablecidos, se cargan en el detalle
				if(cantidadDosisFija != ""){
					mCantDosis = cantidadDosisFija;
				}
				
				if(unidadDosisFija != ""){
					mUnidadDosis = unidadDosisFija;
				}
				
				if(diasMaximosFija != ""){
					mDiasTto = diasMaximosFija;
				}
				
				if(dosisMaximasFija != ""){
					mDosisTto = dosisMaximasFija;
				}
				
				if(viasAdministracionFija != ""){
					mVia = viasAdministracionFija;
				}
				
				// if(fechaInicioFija && fechaInicioFija!=""){
					// mFechaInicio = fechaInicioFija;
				// }

				//Abre la ventana emergente
				if(abreVentanaFija == "S" && componentesTipo != "" || tipoMedLiquido == "L"){
					if(mFrecuencia!="H.E.")
						abrirModalArticulos(codigo,nombreComercial,nombreGenerico,tipoMedLiquido,esGenerico,idx,componentesTipo);
					else
						strPendientesModal += 'articulo|'+codigo+'|'+nombreComercial+'|'+nombreGenerico+'|'+tipoMedLiquido+'|'+esGenerico+'|'+idx+'|'+componentesTipo+'\r\n';
				}
				
				if(elemento.tagName != 'DIV'){
					
					//En esta seccion se dan los posibles avisos
					//Grupo de control
					//var nombreArticuloGrabar = nombreComercial;
					
					/*
					if(grupo == 'CTR'){
						alert("El medicamento " + nombreArticuloAlert + " se encuentra en el grupo de control.  Requiere formula de control");
						nombreArticuloGrabar = nombreComercial.replace(/_/g," ") + " (de control)";
					} else {
						nombreArticuloGrabar =  nombreComercial.replace(/_/g," ");
					}
					*/
					
					elemento.value = codigo + "-" + nombreArticuloGrabar;
					//elemento.value = nombreArticuloGrabar;
					
					// Se establece si la cadena msjNoPos ya tiene el codigo del artículo actual
					var avisoNoPos = msjNoPos.indexOf(codigo)
					msjNoPos += codigo+',';
					
					//No pos
					// if(avisoNoPos == -1 && pos == 'N'){
						// alert("El medicamento " + nombreArticuloAlert + " es NO POS");
					// }
					
					//Si no tiene fracciones en la unidad de manejo ni unidad de fraccion se avisa
					if( document.getElementById( "wckInterno" ).checked == false ){
						if( (mUnidadDosis == '' || mCantDosis == '') && !esLiquidoEndovenoso ){
							alert('El articulo con codigo ' + codigo + ' no tiene unidad de fraccion o fracciones por unidad de manejo.  Por favor notifique a servicio farmaceutico.');
						}
					}
					
					//Habilita o inhabilita la marca de confirmacion de preparacion
					if(origen != 'CM' && document.getElementById("wchkconf"+tipoProtocoloAux+idx)){
						document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = true;
					} else {
						if(document.getElementById("wchkconf"+tipoProtocoloAux+idx))
							document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = false;
					}
					
					//Unidad dosis
					if(document.getElementById("wudosis"+tipoProtocoloAux+idx)){
						document.getElementById("wudosis"+tipoProtocoloAux+idx).value = mUnidadDosis;
					}	
					
					//Unidad manejo
					if(document.getElementById("wcundmanejo"+tipoProtocoloAux+idx)){
						document.getElementById("wcundmanejo"+tipoProtocoloAux+idx).value = unidad;
					}

					//Cantidad maxima de fracciones en la unidad de manejo
					if(document.getElementById("wdosis"+tipoProtocoloAux+idx)){
						document.getElementById("wdosis"+tipoProtocoloAux+idx).value = mCantDosis;
						// 2012-06-27 
						// Se adiciona el atributo "readonly" para que este campo siempre sea de solo lectura, que no se pueda modificar
						if(!adicionMultiple)
							document.getElementById("wdosis"+tipoProtocoloAux+idx).readOnly = true;
					}			
					
					//Oculto para maximo de unidades del articulo
					if(document.getElementById("whcmanejo"+tipoProtocoloAux+idx)){
						document.getElementById("whcmanejo"+tipoProtocoloAux+idx).value = mCantDosis;
					}
					
					//Oculto vence
					if(document.getElementById("whvence"+tipoProtocoloAux+idx)){
						document.getElementById("whvence"+tipoProtocoloAux+idx).value = vencimiento;
					}
					
					//Oculto para maximo de unidades del articulo
					if(document.getElementById("whartpro"+tipoProtocoloAux+idx)){
						document.getElementById("whartpro"+tipoProtocoloAux+idx).value = tipoProtocolo;
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
					if(document.getElementById("wdiastto"+tipoProtocoloAux+idx) && parseInt(mDiasTto) > 0){
						document.getElementById("wdiastto"+tipoProtocoloAux+idx).value = mDiasTto;
					} else {
						document.getElementById("wdiastto"+tipoProtocoloAux+idx).value = '';
					}
					
					//Asigno dosis maximas
					if(document.getElementById("wdosmax"+tipoProtocoloAux+idx) && parseInt(mDosisTto) > 0){
						document.getElementById("wdosmax"+tipoProtocoloAux+idx).value = mDosisTto;
					} else {
						document.getElementById("wdosmax"+tipoProtocoloAux+idx).value = '';
					}

					//Asigno posologia
					if(document.getElementById("wposologia"+tipoProtocoloAux+idx)){
						document.getElementById("wposologia"+tipoProtocoloAux+idx).value = posologia;
					} else {
						document.getElementById("wposologia"+tipoProtocoloAux+idx).value = '';
					}
					
					//Asigno unidad de medida posologia
					if(document.getElementById("wunidadposologia"+tipoProtocoloAux+idx)){
						document.getElementById("wunidadposologia"+tipoProtocoloAux+idx).value = unidadPosologia;
					} else {
						document.getElementById("wunidadposologia"+tipoProtocoloAux+idx).value = '';
					}

					
					//Asigno via
					if(document.getElementById("wviadmon"+tipoProtocoloAux+idx)){
						document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = mVia;
					} else {
						document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = '';
					}
					
					if(document.getElementById("wfinicio"+tipoProtocoloAux+idx) && mFechaInicio){
						document.getElementById("wfinicio"+tipoProtocoloAux+idx).value = mFechaInicio;
					}
					//} else {
					//	document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = '';
					//}


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
						/*
						var opcionesMaestro = document.getElementById("wmviaadmon").options;			
					
						var cont1 = 0;
						var splVia = mVia.split(",");
						var opcionTmp = null;

						while(cont1 < opcionesMaestro.length){
							opcionTmp = document.createElement("option");		
							if(via.indexOf(opcionesMaestro[cont1].value) != -1){
								document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
							} 
							
							opcionTmp.text = opcionesMaestro[cont1].text;
							opcionTmp.value = opcionesMaestro[cont1].value;
							
							cont1++;
						}*/
					} else {
						var opcionesMaestro = document.getElementById("wmviaadmon").options;			
						
						var cont1 = 0;
						var splVia = mVia.split(",");
						var opcionTmp = null;

						while(cont1 < opcionesMaestro.length){
							opcionTmp = document.createElement("option");		
							document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
							
							opcionTmp.text = opcionesMaestro[cont1].text;
							opcionTmp.value = opcionesMaestro[cont1].value;
							
							cont1++;
						}
					}

					var historia = document.forms.forma.whistoria.value;
					var ingreso = document.forms.forma.wingreso.value;

					// Si el artículo es NO POS
					if(pos == 'N'){
						$( "#wespos"+tipoProtocoloAux+idx ).val( 'off' )
						$( "#wtienectc"+tipoProtocoloAux+idx ).val( 'on' )
						strPendientesCTC += 'articulo|'+codigo+'|'+tipoProtocoloAux+'|'+idx+'\r\n';
						//return;
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
	
	hayArticulosAImprimir();
}


function abrirCtcArticulos(value){
	
	if(value!="")
	{
		// if(false && !adicionMultiple)
		// {
			// if( !arCTCArticulos[ codigo ] ){
				// mostrarCtcArticulos2( codigo, tipoProtocoloAux, idx, tratamiento, deAlta );
			// }
		// }
		// else
		// {
			
			var codigoCtc = document.getElementById('codigoCtc').value;
			var tipoProtocoloAuxCtc = document.getElementById('tipoProtocoloAuxCtc').value;
			var idxCtc = document.getElementById('idxCtc').value;
			var tratamientoCtc = value;
			
			// strPendientesCTC += 'articulo|'+codigoCtc+'|'+tipoProtocoloAuxCtc+'|'+idxCtc+'|'+tratamientoCtc+'\r\n';
		//}
	
		$.unblockUI();
		
		filasNoPos = Array( 'articulo|'+codigoCtc+'|'+tipoProtocoloAuxCtc+'|'+idxCtc+'|'+tratamientoCtc );
		
		var arrNoPos = filasNoPos[0].split( "|" );
		var cadenaReemplazar = filasNoPos[0]+"\r\n";
		
		mostrarCtcArticulos2( arrNoPos[1], arrNoPos[2], arrNoPos[3], arrNoPos[4] );
		
		// if(strPendientesCTC!="")
			// abrirCTCMultiple();	
	
	}
}


/*****************************************************************************************************************************
 * Limpia el resultado del buscador de examenes
 ******************************************************************************************************************************/
function limpiarBuscadorExamenes(){
	var resultados = document.getElementById('cntExamenes').innerHTML = "";
}

/*****************************************************************************************************************************
 * Apertura de ventana modal para combinar los liquidos y las nutriciones
 * abrirModalArticulos(codigo,nombreComercial.replace(/_/g," "),nombreGenerico.replace(/_/g," "),tipoMedLiquido,esGenerico,idx,componentesTipo)
 ******************************************************************************************************************************/
function abrirModalArticulos(){
//	debugger;
	
	var codigo = arguments[0];
	var nombreComercial = arguments[1];
	var nombreGenerico = arguments[2];
	var tipo = arguments[3];
	var indiceArticulo = arguments[5];
	var componentesTipo = arguments[6];
	 
	grupoGenerico = arguments[4];

	// alert(codigo+" ------- "+nombreComercial+" ------- "+nombreGenerico+" ------- "+tipo+" ------- "+grupoGenerico+" ------- "+indiceArticulo+" ------- "+componentesTipo);
	
	//Inicializo las variables de los componentes genericos
	document.getElementById('articuloComponentes').innerHTML = "";
	
	//Para cada componente arrojado en la consulta, asigno la siguiente estructura:
	var vecComponentes = componentesTipo.split(";");
	var cont1 = 0;
	var referencia = "";
	var articulos = "<table border='0'>";
	var articulito = "";
	while(cont1 < vecComponentes.length){
		articulito = vecComponentes[cont1].split("**");
		if(articulito.length > 1 && articulito[2]){
			referencia = "javascript:adicionarComponenteArticulo('"+articulito[0]+"','"+reemplazarTodo(articulito[2]," ","_")+"','"+cont1+"','"+articulito[1]+"',"+articulito[4]+");";
			articulos += "<tr><td><input type='checkbox' name='check_insumo"+cont1+"' id='check_insumo"+cont1+"' onClick="+referencia+"></td><td>"+articulito[2]+"</td><td><input type'text' size='2' name='cantidad_insumo"+cont1+"' id='cantidad_insumo"+cont1+"' onChange="+referencia+" /> "+articulito[5]+"</td></tr>";
		}

		cont1++;
	}
	document.getElementById('listaComponentes').innerHTML = articulos;

	articulos += "</table>";
	
	//Indice del articulo en el que se ponen los componentes des
	document.getElementById('indiceArticuloComponentes').value = indiceArticulo;
	
	$.blockUI({ message: $('#modalArticulos'),
						css: {
							overflow: 'auto',
							cursor: 'auto'
						} });
}
/*****************************************************************************************************************************
 * Cerrado de ventana modal para combinar los liquidos y las nutriciones
 ******************************************************************************************************************************/
function cerrarModalArticulos(){
//	debugger;
	
	var indice = document.getElementById('indiceArticuloComponentes').value;

	if(grupoGenerico != "LQ")
	{
		document.getElementById("wtxtobsN"+indice).value += document.getElementById("wcomponentesarticulo").value;
		document.getElementById("wtxtobsN"+indice).style.width = "100%";
		document.getElementById("wcomponentesarticulo").value = "";
		document.getElementById("wcomponentesarticulocod").value = "";
		$.unblockUI();

		grupoGenerico = "";		
		
		setTimeout("abrirModalMultiple()", 1000);
	}
	else
	{
		var componentesLQ =  document.getElementById("wcomponentesarticulocod").value;

		adicionMultiple = true;
		
		$.unblockUI();
		
		if(isset(document.getElementById("wcomponentesarticulo")))
			document.getElementById("wcomponentesarticulo").value = "";
		if(isset(document.getElementById("wcomponentesarticulocod")))
			document.getElementById("wcomponentesarticulocod").value = "";

		eleccionMedicamentosInsumos(componentesLQ);

		quitarArticulo(indice,'%','','detKardexAddN','LQ');

		grupoGenerico = "";		
	}
	

}

function trim_ord (myString)
{
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
}

function str_replace_ord (search, replace, subject, count) {
        j = 0,
        temp = '',
        repl = '',
        sl = 0,        fl = 0,
        f = [].concat(search),
        r = [].concat(replace),
        s = subject,
        ra = Object.prototype.toString.call(r) === '[object Array]',        sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    } 
    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp) {                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
}
	
	
	
/******************************************************************************************************************************
 *Valida que la diferencia de fechas entre el servidor y el cliente no sea mas de media hora
 ******************************************************************************************************************************/
function adicionarComponenteArticulo(codigo,nombre,i,esDispensable,cantidadFracciones){
	
	var componentes = document.getElementById('wcomponentesarticulo').value;
	var componentescod = document.getElementById('wcomponentesarticulocod').value;

	if(document.getElementById('check_insumo'+i).checked) 
	{
		if(document.getElementById('cantidad_insumo'+i).value == "" || document.getElementById('cantidad_insumo'+i).value == " ")
			document.getElementById('cantidad_insumo'+i).value = cantidadFracciones;

		var strquitar = trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantidadAnterior[i] + "\r" + "\n";
		var strquitarcod = trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantidadAnterior[i] + " - " + esDispensable + "\r" + "\n";
		
		componentes = str_replace_ord(strquitar,"",componentes);
		componentescod = str_replace_ord(strquitarcod,"",componentescod);
		
		document.getElementById('wcomponentesarticulo').value = componentes;
		document.getElementById('wcomponentesarticulocod').value = componentescod;

		var cantComponente = document.getElementById('cantidad_insumo'+i).value;
		if(componentes.indexOf(nombre.replace(/_/g," "))==-1)
		{
			document.getElementById('wcomponentesarticulo').value += trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantComponente + "\r" + "\n";
			document.getElementById('wcomponentesarticulocod').value += trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantComponente + " - " + esDispensable + "\r" + "\n";
		}
		
		cantidadAnterior[i] = document.getElementById('cantidad_insumo'+i).value;
	}
	else
	{
		var cantComponente = document.getElementById('cantidad_insumo'+i).value;

		var strquitar = trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantComponente + "\r" + "\n";
		var strquitarcod = trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantComponente + "\r" + "\n";

		componentes = str_replace_ord(strquitar,"",componentes);
		componentescod = str_replace_ord(strquitarcod,"",componentescod);

		document.getElementById('wcomponentesarticulo').value = componentes;
		document.getElementById('wcomponentesarticulocod').value = componentescod;

		document.getElementById('cantidad_insumo'+i).value = "";
	}
}
/******************************************************************************************************************************
 *Valida que la diferencia de fechas entre el servidor y el cliente no sea mas de media hora
 ******************************************************************************************************************************/
function eliminarComponentesArticulos(){
	document.getElementById("wcomponentesarticulo").value = "";
	document.getElementById("wcomponentesarticulocod").value = "";
}
/*****************************************************************************************************************************
*
******************************************************************************************************************************/
function grabarEstadoAprobacionArticulos(historia,ingreso,fecha,codigosArticulos,estadoAprobacion,codUsuario){
	var parametros = ""; 
				
	parametros = "consultaAjaxKardex=26&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha+"&codigosArticulos="+codigosArticulos
				+"&codUsuario="+codUsuario+"&estadoAprobacion="+estadoAprobacion+"&wemp_pmla="+document.forms.forma.wemp_pmla.value;
		
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "ordenesidc.inc.php",true);
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


//Esta funcion permite que el usuario cierre la ventana de ordenes y no se grabe nada de lo que haya
//quedado en los formularios.
function salir_sin_grabar(){


	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	var historia = $('#whistoria').val();
	var ingreso = $('#wingreso').val();
	var wfechagrabacion = $('#wfechagrabacion').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var wcedula = $('#wcedula').val();
	var tipoDocumento = $('#wtipodoc').val();

	$.ajax({
			url: "ordenesidc.inc.php",
			type: "POST",
			data:{
				consultaAjaxKardex:       	'57',
				wemp_pmla:					wemp_pmla,
				whistoria:					historia,
				wingreso:					ingreso,
				wfechagrabacion:			wfechagrabacion,
				wbasedato:					wbasedato,
				wbasedatohce:				wbasedatohce,
				whce:						wbasedatohce,
				wcedula:					wcedula,
				tipoDocumento:				tipoDocumento,
				wusuario:					wusuario,
				weditable:					$( "#weditable" ).val(),
				// pestanasVistas:				$( "#pestanasVistas" ).val()

			},
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{

					cerrarModalHCE();
				}
			}
		}
	);

	if( $( "#programa" ).length > 0 ){
		window.close();
	}

	return "texto";
}