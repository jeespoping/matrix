/*****************************************************************************************************************************
 * CONJUNTO DE FUNCIONES COMUNES JAVASCRIPT
 * CREADO	:  Mayo de 2009
 * AUTOR	:  Mauricio Sanchez C.
 * 
 * Historial
 * ---------
 * 
 * 01-May-09	:	Creado
 * 02-Sep-09	:	Se anexa funcion para desplegar el reloj
 ******************************************************************************************************************************/



/*****************************************************************************************************************************
 * VARIABLES GLOBALES
 ******************************************************************************************************************************/
var browser=navigator.appName;
var esIE = false;

/*****************************************************************************************************************************
 * FUNCIONES
 ******************************************************************************************************************************/
function cerrarVentana(){
	top.close(); 
}

/*****************************************************************************************************************************
 * Verifica si una fecha dada en formato AAAA-MM-DD es MAYOR sin incluir a la fecha actual
 ******************************************************************************************************************************/
function esFechaMenorIgualAActual(fecha){
	var esMayor = false;

	var fechaConsulta = new Date();
	var fechaActual = new Date();

	var anioActual = eval(fechaActual.getFullYear());
	var mesActual = eval(fechaActual.getMonth()+1);
	var diaActual = eval(fechaActual.getDate());
	
	var anioConsulta = fecha.substring(0,4);
	var mesConsulta = fecha.substring(5,7);
	var diaConsulta = fecha.substring(8,10);
	
	fechaConsulta.setFullYear(anioConsulta);
	fechaConsulta.setMonth(eval(mesConsulta-1)); 
	fechaConsulta.setDate(diaConsulta);
	
	if(fechaConsulta <= fechaActual){
		esMayor = true;
	}
	
	return esMayor;
}

/*****************************************************************************************************************************
 * Verifica si la fecha 1 dada en el primer parametro es menor igual a la fecha 2... Formatos de fechas AAAA-MM-DD
 ******************************************************************************************************************************/
function esFechaMenorIgual(fecha1,fecha2){
	var esMenorIgual = false;

	//Fecha objeto 1
	var fechaObj1 = new Date();
	
	var anioObj1 = fecha1.substring(0,4);
	var mesObj1 = fecha1.substring(5,7);
	var diaObj1 = fecha1.substring(8,10);
	
	fechaObj1.setFullYear(anioObj1);
	fechaObj1.setMonth(eval(mesObj1-1)); 
	fechaObj1.setDate(diaObj1);
	
	//Fecha objeto 2
	var fechaObj2 = new Date();

	var anioObj2 = fecha2.substring(0,4);
	var mesObj2 = fecha2.substring(5,7);
	var diaObj2 = fecha2.substring(8,10);
	
	fechaObj2.setFullYear(anioObj2);
	fechaObj2.setMonth(eval(mesObj2-1)); 
	fechaObj2.setDate(diaObj2);
	
	if(fechaObj1 <= fechaObj2){
		esMenorIgual = true;
	}
	
	return esMenorIgual;
}

/*****************************************************************************************************************************
 * Verifica si la fecha 1 dada en el primer parametro es mayor igual a la fecha 2... Formatos de fechas AAAA-MM-DD
 ******************************************************************************************************************************/
function esFechaMayorIgual(fecha1,fecha2){
	var esMayorIgual = false;

	//Fecha objeto 1
	var fechaObj1 = new Date();
	
	var anioObj1 = fecha1.substring(0,4);
	var mesObj1 = fecha1.substring(5,7);
	var diaObj1 = fecha1.substring(8,10);
	
	fechaObj1.setFullYear(anioObj1);
	fechaObj1.setMonth(eval(mesObj1-1)); 
	fechaObj1.setDate(diaObj1);
	
	//Fecha objeto 2
	var fechaObj2 = new Date();

	var anioObj2 = fecha2.substring(0,4);
	var mesObj2 = fecha2.substring(5,7);
	var diaObj2 = fecha2.substring(8,10);
	
	fechaObj2.setFullYear(anioObj2);
	fechaObj2.setMonth(eval(mesObj2-1)); 
	fechaObj2.setDate(diaObj2);
	
	if(fechaObj1 >= fechaObj2){
		esMayorIgual = true;
	}
	
	return esMayorIgual;
}

/*****************************************************************************************************************************
 * Valida un numero entero positivo de entrada.  Recibe el id del elemento
 ******************************************************************************************************************************/
function validoNumeroPositivo(idElemento){
	var valido = true;
	var elemento = document.getElementById(idElemento);
	if(elemento && elemento.value != ''){
		if(isNaN(elemento.value)){
			valido = false;
		}else{
			if(elemento.value < 0){
				valido = false;
			}
		}		
	} else {
		valido = false;
	}
	return valido;
}

/*****************************************************************************************************************************
 *  Valida entrada entera
 ******************************************************************************************************************************/
function validarEntradaEntera(e) { 
    tecla = (document.all) ? e.keyCode : e.which; 
    if (tecla==8 || tecla==13) 
    	return true; 
    
	//Solo digitos
    patron = /\d/;
    
    te = String.fromCharCode(tecla); 
    
    return patron.test(te);
}

/*****************************************************************************************************************************
 * Validar entrada decimal
 ******************************************************************************************************************************/
function validarEntradaDecimal(e) { 
    tecla = (document.all) ? e.keyCode : e.which; 
    if (tecla==8 || tecla==13 || tecla==46) {
    	return true; 
    }
    
	//Solo digitos
    patron = /\d/;
    
    te = String.fromCharCode(tecla); 
    
    return patron.test(te);
}  
 /*****************************************************************************************************************************
  * Redirecciona a una pagina de error
  ******************************************************************************************************************************/
function redirigir(mensaje){
	document.location.href = "root/redireccion.php?mensaje=" + mensaje;
}
 
/*****************************************************************************************************************************
 * Crea un objeto de tipo XMLHttpRequest para AJAX
 ******************************************************************************************************************************/
function nuevoAjax(){ 
	var xmlhttp=false;
	try{ xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}
	catch(e){ 
		try{ xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); } 
		catch(E) { xmlhttp=false; }
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); }
	
	return xmlhttp; 
}

/*****************************************************************************************************************************
 * Verifica si la llamada Ajax se encuentra en proceso de ejecución 
 ******************************************************************************************************************************/
function estaEnProceso(xmlhttp) {
	switch ( xmlhttp.readyState ) {
		case 1, 2, 3:
			return true;
		break;
		// Case 4 y 0
		default:
			return false;
		break;
	}
}
/*****************************************************************************************************************************
 * Deja en un campo 'reloj' en el formulario
 ******************************************************************************************************************************/
function mueveReloj()
{ 
   	momentoActual = new Date(); 
   	hora = momentoActual.getHours(); 
   	minuto = momentoActual.getMinutes(); 
   	segundo = momentoActual.getSeconds(); 

   	horaImprimible = hora + " : " + minuto + " : " + segundo; 

   	document.forms[0].reloj.value = horaImprimible;

   	setTimeout("mueveReloj()",1000); 
}

/*****************************************************************************************************************************
 * Detección de internet explorer... Hasta el momento esto funciona OK
 ******************************************************************************************************************************/
if (browser=="Microsoft Internet Explorer"){
	esIE = true;
}
