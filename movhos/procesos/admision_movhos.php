<?php
include_once("conex.php");
if(!isset($consultaAjax) || $consultaAjax=="")
{
	
$usuarioValidado = true;

if (!isset($_SESSION['user'])){
	$usuarioValidado = false; 	
} else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}

if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";	
	echo "Por favor cierre esta ventana e ingrese a matrix nuevamente.";
	return false;
}
?>
<html>

<head>
<title>MATRIX - [ADMISION DE PACIENTES]</title>
</head>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<body>

<script type="text/javascript">

function enter() { document.forms.forma.submit(); }

function inicio()
{
	document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value;
}

function seleccionServicioHabitacion(){
	document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=a'+'&wselcco='+document.forms.forma.wselcco.value;
}

function regresar(){
	document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value;
}

function consultarInfoAdmision(){
	document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=b&whistoria='+document.forms.forma.whistoria.value;
}

function mostrarMensajePantalla(texto){
	document.getElementById('mensajePantalla').style.display = "block";
	document.getElementById('mensajePantalla').innerHTML = "  ::MENSAJE::  "+texto;
}

function ocultarMensajePantalla(){
	document.getElementById('mensajePantalla').style.display = "none";
}

function disableEnterKey(e)
{
     var key;
     if(window.event)
          key = window.event.keyCode;     //IE
     else
          key = e.which;     //firefox
     if(key == 13)
          return false;
     else
          return true;
}


function cancelarEntrega(){
	document.forms.forma.submit();
}

function marcarEntrega(historia, ingreso){
	if(confirm("Desea confirmar la entrega del paciente desde urgencias?")){
		document.forms.forma.wactualizar.value = '*';
		document.forms.forma.whistoria.value = historia;
		document.forms.forma.wingreso.value = ingreso;

		document.forms.forma.submit();
	}
}

// Función Ajax para validar si el paciente tiene Alta en proceso o Está en Consulta
function validaIngreso()
{
	var parametros = "consultaAjax=valida&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&wbasedato="+document.forms.forma.wbasedato.value+"&wbasedatohce="+document.forms.forma.wbasedatohce.value+"&whistoria="+document.forms.forma.whistoria.value;
	try{
		var ajax = nuevoAjax();

		ajax.open("POST", "admision_movhos.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if (ajax.readyState==4 && ajax.status==200)
		{
			//alert(ajax.responseText);
			validaIng = ajax.responseText;

		}
	}catch(e){    }

	return validaIng;
}

// Función Ajax para validar si el paciente tiene Alta en proceso o Está en Consulta
function HabAsignada()
{
	var parametros = "consultaAjax=habasignada&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&wbasedato="+document.forms.forma.wbasedato.value+"&wbasedatohce="+document.forms.forma.wbasedatohce.value+"&whistoria="+document.forms.forma.whistoria.value;
	try{
		var ajax = nuevoAjax();

		ajax.open("POST", "admision_movhos.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if (ajax.readyState==4 && ajax.status==200)
		{
			//alert(ajax.responseText);
			habasignada = ajax.responseText;
		}
	}catch(e){    }

	return habasignada;
}

//Verificar si el cco del paciente es de ingreso
// Función Ajax para validar si el paciente tiene Alta en proceso o Está en Consulta
function verificar_cco_paciente()
{
	var parametros = "consultaAjax=verificar_cco_paciente&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&wbasedato="+document.forms.forma.wbasedato.value+"&wbasedatohce="+document.forms.forma.wbasedatohce.value+"&whistoria="+document.forms.forma.whistoria.value;
	try{
		var ajax = nuevoAjax();

		ajax.open("POST", "admision_movhos.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if (ajax.readyState==4 && ajax.status==200)
		{
			//alert(ajax.responseText);
			cco_ingreso = ajax.responseText;
		}
	}catch(e){    }

	return cco_ingreso;
}


function admitirPaciente(){
	var historia = document.forms.forma.whistoria.value;
	var servicioOrigen = document.forms.forma.wselcco.value;
	var servicioDestino = "";
	var habitacionDestino = "";

	var validarServicioDestino = true;
	
	var historia = $("#whistoria").val();
	
	if(historia == ""){
		alert("Debe ingresar la historia.");
		valido = false;
		return;		
	}
	
	msj_consulta = "";
	validaIng = validaIngreso();
    datosvalidacion = validaIng.split('-')
    estado_actual = datosvalidacion[0];
    ccoactual = datosvalidacion[1];
	
	if(estado_actual=='En consulta')
		msj_consulta = "El paciente se encuentra en consulta. ";
	
	if(estado_actual=='Alta en proceso')
	{
		alert("El paciente se encuentra en alta en proceso en el servicio "+ccoactual+", comuniquese con el funcionario correspondiente en esta area para que lo reactive y pueda realizar la admision.");
		return false;
	}

      // Valida si el paciente esta en alta definitiva
	if(estado_actual=='Alta definitiva')
	{
		alert("El paciente se encuentra en alta definitiva en el servicio "+ccoactual+", comuniquese con el funcionario correspondiente en esta area para que lo reactive y pueda realizar la admision.");
		return false;
	}
	//Si wselccodes (seleccion centro costos destino) es nulo, se trata de cirugia...
	if(document.forms.forma.wselccodes){
		servicioDestino = document.forms.forma.wselccodes.value;
		habitacionDestino = document.forms.forma.wselhab.value;
	} else {
		validarServicioDestino = false;
	}

	var valido = true;
	var mensaje = "";

	//validaciones
	if(historia == ''){
		mensaje += "Debe especificar una historia clínica \n\r";
		document.forms.forma.whistoria.value = '';
		valido = false;
	} else {
		//Prueba el patron regular... empezar por 1-9, seguido de digitos
		var reg = new RegExp("^[1-9]+[0-9]*");
    	if(!reg.test(historia)){
			mensaje += 'Debe especificar un numero de historia compuesta por digitos.  Sin cero adelante. \n\r';
			document.forms.forma.whistoria.value = '';
			valido = false;
    	}

		//Historia cero no se permite
		if(historia == '0'){
			mensaje += 'Debe especificar una historia clínica diferente de cero \n\r';
			document.forms.forma.whistoria.value = '';
			valido = false;
		}
		
		var admisiones = document.getElementById('admisiones').value;
		
		if(estado_actual=='total' && admisiones == 'on')
		{
			mensaje += 'Tiene medicamento(s) pendientes por aplicar o por devolver, favor avisar a la enfermera(o) encargada. \n\r';
			valido = false;
		}
		
		if(estado_actual=='descarte' && admisiones == 'on')
		{
			mensaje += 'Tiene medicamento(s) pendientes por aplicar o por devolver, favor avisar a la enfermera(o) encargada. \n\r';
			valido = false;
		}
	}

	if(validarServicioDestino){
		if(!servicioDestino || servicioDestino == ''){
			mensaje += "Debe especificar un servicio destino \n\r";
			valido = false;
		}

		if(!habitacionDestino || habitacionDestino == ''){
			mensaje += "Debe especificar una habitación destino \n\r";
			valido = false;
		}
	}

	
	$.ajax({
				url: "admision_movhos.php",
				type: "POST",
				data:{
					consultaAjax 	: 'verificar_solicitud_cama_activa',
					wemp_pmla		: document.forms.forma.wemp_pmla.value,
					wbasedato		: document.forms.forma.wbasedato.value,
					whistoria		: $("#whistoria").val()
					
				},
				dataType: "json",			
				async: false,
				success:function(data_json) {
				
					if (data_json.error == 1)
					{
						jAlert(data_json.mensaje);										
						return;
					}
					else{
							//Llamo a la funcion verificar_cco_paciente para saber si el centro de costos actual es de ingreso.
							var cco_ingreso_paciente = verificar_cco_paciente();	
							var ccoing_nombrecco = cco_ingreso_paciente.split('-');
							var ccoing = ccoing_nombrecco[0];
							
							//Si el cco del paciente no es de ingreso, muestra una alerta diciendo de donde es el paciente y que no puede ser admitido.
							if(ccoing == 'off' || ccoing == ""){
							
							mensaje += "El centro de costos "+ccoing_nombrecco[1]+", asociado al paciente no es de ingreso, modifique la admision en unix a un servicio de admisiones.";
								
							valido = false;
							
							}
							
							
							if(!valido){
								alert("Error: \n\r" + mensaje);
							} else {
								if(confirm(msj_consulta+"Desea realizar proceso de admisión del paciente?")){
									if(validarServicioDestino){
										document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=d&whistoria='+historia+'&servicioOrigen='+servicioOrigen+'&servicioDestino='+servicioDestino+'&habitacionDestino='+habitacionDestino;
									} else {
										document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=d&whistoria='+historia+'&servicioOrigen='+servicioOrigen;
									}
								}
							}
						
					}
				}			
				
			});

	
	

}

function admitirPacienteCirugia(){

	var historia = document.forms.forma.whistoria.value;
	var servicioOrigen = document.forms.forma.wselcco.value;
	var servicioDestino = "";
	var habitacionDestino = "";

	var validarServicioDestino = true;

	msj_consulta = "";
	validaIng = validaIngreso();
    validaIng = validaIngreso();
    datosvalidacion = validaIng.split('-')
    estado_actual = datosvalidacion[0]; //En cosulta, en proceso de traslado, en alta definitiva.
    ccoactual = datosvalidacion[1]; // SErvicio actual del paciente

	if(estado_actual=='En consulta')
        {
		msj_consulta = "El paciente se encuentra en consulta. ";
        }

	if(estado_actual=='Alta en proceso')
	{
		alert("El paciente se encuentra en alta en proceso en el servicio "+ccoactual+", comuniquese con el funcionario correspondiente en esta area para que lo reactive y pueda realizar la admision.");
		return false;
	}

      // Valida si el paciente esta en alta definitiva
	if(estado_actual=='Alta definitiva')
	{
		alert("El paciente se encuentra en alta definitiva en el servicio "+ccoactual+", comuniquese con el funcionario correspondiente en esta area para que lo reactive y pueda realizar la admision.");
		return false;
	}

	//Si wselccodes (seleccion centro costos destino) es nulo, se trata de cirugia...
	if(document.forms.forma.wselccodes){
		servicioDestino = document.forms.forma.wselccodes.value;
		habitacionDestino = document.forms.forma.wselhab.value;
	} else {
		validarServicioDestino = false;
	}

	var valido = true;
	var mensaje = "";

	//validaciones
	if(historia == '' || isNaN(historia)){
		mensaje += "Debe especificar una historia clínica compuesta por números \n\r";
		document.forms.forma.whistoria.value = '';
		valido = false;
	} else {
		//Historia cero no se permite
		if(historia == '0'){
			mensaje += 'Debe especificar una historia clínica diferente de cero \n\r';
			document.forms.forma.whistoria.value = '';
			valido = false;
		}
	}

	if(validarServicioDestino){
		if(!servicioDestino || servicioDestino == ''){
			mensaje += "Debe especificar un servicio y una habitación destino";
			valido = false;
		}
	}
			
	if(!valido){
		alert("Error: \n\r" + mensaje);
	} else {
	if(confirm(msj_consulta+"Desea realizar proceso de admisión del paciente?")){
		if(validarServicioDestino){
			document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=f&whistoria='+historia+'&servicioOrigen='+servicioOrigen+'&servicioDestino='+servicioDestino+'&habitacionDestino='+habitacionDestino;
		} else {
			document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=f&whistoria='+historia+'&servicioOrigen='+servicioOrigen;
		}
	}
	}
}

function mostrarAyuda()
{
	window.open('../manuales/PreentregaUrgencias.mht', 'window','width=650,height=550,scrollbars=yes,resizable=yes');
}


function consultarHabitaciones()
{

	var habasignada = HabAsignada(); // Esta funcion cosulta la habitacion asignada al paciente
	var urgencias = document.getElementById('wurgencias').value;
	var hab_cco = habasignada.split('-');	//Exploto el dato por habitacion y centro de costos

	if (urgencias == '1') //Evaluo si el servicio en el que esta el usuario es urgencias.
	{

		if (hab_cco[0] == '') // Si la habitacion esta vacia en la central de camas, se mostrara un mensaje diciendo que no hay cama asociada
							   // y retornara la interfaz a la pantalla de inicio.
		{
			alert("La historia no tiene habitación asignada en la Central de Asignación de Camas.");
			$('#wselccodes').attr('disabled','disabled'); //Si se muestra el mensaje deshabilitara el seleccionador de centro de costos.
			$('#wselhab').attr('disabled','disabled'); //Si se muestra el mensaje deshabilitara el seleccionador de habitacion.
			//inicio(); //Retorna a la pantalla principal.
			return;
		}

	}

	var contenedor = document.getElementById('cntHabitacion');

	var parametros = "";

	if (urgencias == '1') //Evaluo si el servicio en el que esta el usuario es urgencias.
	{
		$("#wselccodes").val(hab_cco[1]); //Le asigna el valor al seleccionador de centro de costos.
		$('#wselccodes').attr('disabled','disabled'); // Deshabilita el seleccionador para que no pueda seleccionar uno diferente al asignado en la central de camas.
		var ccodestino = hab_cco[1]; // Se enviara este dato por el ajax si el servicio es de urgencias.
	}
	else
	{
		var ccodestino = document.getElementById('wselccodes').value; // Se enviara este dato por el ajax si el servicio no es de urgencias.
	}

	parametros = "consultaAjax=04&basedatos="+document.forms.forma.wbasedato.value+"&servicio=" + ccodestino;

	if (urgencias != '1') //Evaluo si el servicio en el que esta el usuario es urgencias, para que no reemplace el seleccionador de habitacion.
	{
	contenedor.innerHTML = "<select><option value=''>Cargando...</option></select>";
	}

	try{
	ajax=nuevoAjax();

	ajax.open("POST", "../../../include/root/comun.php",true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(parametros);

	ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4 && ajax.status==200)
		{

			//si el servicio es de urgencias se pinta el seleccionador en el contenedor, se deshabilita el selecionador de habitacion y se le asigna la habitacion desde la central de camas.
			if (urgencias == '1')
				{

				contenedor.innerHTML=ajax.responseText;
				$('[name=wselhab]').attr('disabled','disabled');
				$("[name=wselhab]").val(hab_cco[0]);

				}
				else
				{
				contenedor.innerHTML=ajax.responseText;
				}
		}
	}
	if ( !estaEnProceso(ajax) ) {
		ajax.send(null);
	}
	}catch(e){	}



}

function cancelarAdmision(){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;

	document.location.href='admision_movhos.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=c'+'&whistoria='+historia+'&wingreso='+ingreso;
}

function irListaPreentrega(){
	document.location.href='admisionPreentrega.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=b';
}
</script>

<?php

/*BS'D
 * Nueva version de admisiones
 * Autor: Mauricio Sánchez Castaño.
 * Fecha creacion: 2008-09-24
 * Modificado:
 * @autor: Jonatan Lopez | 2016-08-31 | --> Se agrega validacion de solicitud de cama repetida, ademas no saldra el centro de costos de urgencias en el listado inicial.
 * @autor: Jonatan Lopez | 2016-08-19 | --> Se quita en la validacion de la cancelacion de entrega del paciente, el recibo,ya que esto generaba inconvenientes con los pacientes hospitalizados.
 * @autor: Jonatan Lopez | 2016-07-28 | --> Se inserta el usuario que pide la cama en el campo usu_central, ademas se agrega mensaje en la observacion que dice automatico admisiones.
 * @autor: Jonatan lopez | 2015-06-10 | --> Si el paciente no tiene servicio anterior, no esta en proceso de traslado y el servicio actual es diferente de urgencias y si tiene movimiento hospitalario ACTIVO,
											no deja cancelar la admision, ya que el programa estaba dejando cancelar historias con recibo activo en la tabla movhos_000017.
 * @autor: Jonatan lopez | 2015-03-12 | --> Se valida si el paciente tiene saldo pendiente por aplicar o descartar, si es asi no permite la admision por admisiones.
 * @autor: Jonatan lopez | 2014-11-24 | --> Se libera el cubiculo asignado al paciente en caso de tenerlo, esto se realiza para que no quede en dos ubicaciones al mismo tiempo.
 * @autor: Jonatan lopez | 2014-02-04 | --> Al seleccionar ingreso por admisiones, se valida si la historia esta actualmente en un cco de ingreso, si no es asi, no permitira la admision.
 * @autor: Jonatan lopez | 2013-11-26 | --> Se modifica la funcion consultarPacienteUnix por la funcion consultarPacienteMatrix para que no verifique en Matrix si el paciente existe
											y ya no tenga que ir a Unix para hacer esta validacion, en esta validacion del paciente en Matrix, este no debe estar en alta definitiva.
 * @autor: Jonatan lopez | 2013-05-17 | --> Se agrega ccoing=on en la funcion consultarCcoCirugia para que solo traiga los centros de costo de ingreso.
 * @autor: Jonatan lopez | 2013-03-08 | --> Se valida si el paciente se encuentra en alta en proceso y en alta definitiva, si es asi no permitira la admision, ademas si admiten un
 *                                          paciente por cirugia, este quedara en proceso de traslado.
 * @autor: Jonatan lopez | 2013-01-23 | --> Se modifica la asignacion de cama en el servicio urgencias para que traiga la cama desde la central de asignacion de cama,
 											si no tiene cama asociada mostrara un mensaje diciendo que no tiene cama asociada, esto permitira que solo se pueda seleccionar
 											la cama asignada en la central de asignacion de camas y se pueda recibir al paciente.
 * @autor: Jonatan lopez | 2012-12-19 | --> Se modifica la admision de pacientes por urgencias para que valida si la historia a admitir tiene solicitud de cama
 											en caso de no tenerla mostrara un mensaje diciendo que no tiene solicitud, para los demas centros de costos la solicutd
											de cama se hara de forma automatica, si se cancela una admision se eliminara la solicitud de cama para la historia seleccionada,
											ademas se elimina la validacion para el centro de costos cirugia, permitiendo seleccionar centro de costos y habitacion, de la
											misma forma que admisiones.
 * @autor: Mario Cadavid | 2012-05-08 | --> Se adicionaron los UNION a las consultas de unix de modo que se reemplacen los campos Nulos
 * @autor: Mario Cadavid | 2012-02-24 | --> Se creó la función "devolverAltaPaciente" que permite verificar si el paciente a
 *		   admitir con el ingreso actual ha sido dado de alta, si es asi deshace el alta en las tablas movhos_000018 y hce_000022
 * @autor: Mario Cadavid | 2012-02-16 | --> En la función actualizarUbicacionPaciente se adicionó la consulta de movhos_000018
 *		   para verificar si exitia ubisac y es diferente a urgencias, poner este y no urgencias
 * @autor: Mario Cadavid | 2012-02-15 | --> En la función eliminarIngresoPaciente se adicionó la consulta de movhos_000018
 *		   para establecer valor de ubisac y ubihac, igual para establecer valor de ubiptr si el servicio es cirugia
 * @autor: Mario Cadavid | 2012-02-14 | -->  Se adicionó la función "existeEnTablaMovimientos" para consultar si hay registros
 *		   en movhos_000017 y decidir si se hace el proceso de admisión a la institución. Antes si existia en movhos_000018 no
 *		   se dejaba admitir con este cambio, asi exista en movhos_000018 pero no en movhos_000017 deja hacer el proceso de
 *		   admisión, esto porque ya no se está borrando movhos_000018 al cancelar la admisión
 * @autor: Mario Cadavid | 2012-02-10 | -->  En la función eliminarIngresoPaciente que eliminaba el registro de la tabla
 * 		   movhos_000018, se cambio el query por una actualización para que ponga ubiptr en off de modo que se cancele el traslado
 * @autor: Mario Cadavid | 2012-01-30 | -->  En la función pacienteConMovimientoHospitalario se adicionó el parámetro
 * 		   $servicioOrigen de modo que se pueda consultar si es cirugía o urgencias, si es uno de estos, no se valida
 *		   si el paciente tiene movimiento hospitalario
 * @autor: Mario Cadavid | 2012-01-30 | -->  Se agregó la función validaIngreso que permite validar por
 * 		   medio de ajax si el paciente está en consulta o tiene alta en proceso.
 * @autor: Msanchez-->  Al cancelar admision no se encontraban todos los atributos de como quedo el paciente
 * 		   (2009-04-14)	debido a que este era eliminado por completo.  Linea 1470
 *
 */

class movimientoHospitalarioDTO {
	var $consecutivo;
	var $historia;
	var $ingreso;
	var $servicioOrigen;
	var $servicioDestino;
	var $habitacionOrigen;
	var $habitacionDestino;
	var $tipoMovimiento;
}


/**
 * Lista los centros de costos de ingreso
 *
 * @param unknown_type $seleccionCco
 * @param unknown_type $funcion
 */
function ccoIngreso(){
  	
	global $wbasedato;
	global $conex;
	
	$q = "SELECT ccocod, UPPER(Cconom)
			FROM ".$wbasedato."_000011
		   WHERE Ccoing = 'on'
  			 AND Ccoest = 'on'
			 AND ccocod not in (SELECT ccocod
								  FROM ".$wbasedato."_000011
								 WHERE Ccourg = 'on'
								   AND Ccoest = 'on')
			ORDER by 2;";
  	$res1 = mysql_query($q,$conex);
  	$num1 = mysql_num_rows($res1);

  	$coleccion = array();

  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$cco = new centroCostosDTO();
  			$row1 = mysql_fetch_array($res1);

  			$cco->codigo = $row1[0];
  			$cco->nombre = $row1[1];

  			$coleccion[] = $cco;
  		}
  	}
  	return $coleccion;
}

/**
 * Consulta el centro de costos de Urgencias
 *
 * @return unknown
 */
function consultarCcoUrgencias(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod, Cconom
		FROM
			".$wbasedato."_000011
		WHERE
			Ccourg = 'on'; ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = new centroCostosDTO();

	if($filas > 0){
		$fila = mysql_fetch_row($res);

		$cco->codigo = $fila[0];
		$cco->nombre = $fila[1];
	}
	return $cco;
}

/**
 * Consulta el centro de costos de Cirugia
 *
 * @return unknown
 */
function consultarCcoCirugia(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod, Cconom
		FROM
			".$wbasedato."_000011
		WHERE
			Ccocir = 'on'
            AND ccoing = 'on'; ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = new centroCostosDTO();

	if($filas > 0){
		$fila = mysql_fetch_row($res);

		$cco->codigo = $fila[0];
		$cco->nombre = $fila[1];
	}

	return $cco;
}

//Verifica si el paciente tiene solicitud de cama en la central de camilleros.
function consultar_solicitud_cama( $whistoria, $wemp_pmla)
{

	global $conex;
	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

	$q = " SELECT historia "
        ."   FROM ".$wcencam."_000003 "
        ."  WHERE historia = '".$whistoria."'"
		."    AND Fecha_llegada = '0000-00-00'"
		."	  AND Hora_llegada = '00:00:00'"
		."    AND Fecha_cumplimiento = '0000-00-00'"
		."    AND Hora_cumplimiento = '00:00:00'"
		."    AND Anulada = 'No'"
		."    AND Central = '".$wcentral_camas."'";
    $res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
    $row = mysql_fetch_array($res);
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
    	return false;
    }
    else
    {
    	return true;
    }

}


//Funcion que trae el nombre del centro de costos origen.
function nombreccoorigen($worigen)
{

	global $conex;
	global $wcencam;

	//Traigo el Centro de Costo
    $q = " SELECT Nombre "
        ."   FROM ".$wcencam."_000004 "
        ."  WHERE Estado = 'on'"
		."    AND Cco LIKE '%".$worigen."%'";
    $rescco = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
    $rowcco = mysql_fetch_array($rescco);
    $wccoorigen = $rowcco[0];

    return $wccoorigen;
}

 //Funcion para traer el responsable segun la historia.
 function traerresponsable($whis, $wemp_pmla)
    {

         global $conex;
         global $wbasedato;
         global $wemp_pmla;

        $q = " SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
            ."   FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016"
            ."  WHERE Inghis = '".$whis."'"
            ."    AND ubihis  = Inghis"
            ."    AND ubiing  = Inging"
            ."    AND ubihis  = orihis "
            ."    AND ubiing  = oriing "
            ."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
            ."    AND oriced  = pacced "
            ."    AND oritid  = pactid "
            ."  GROUP BY 1, 2, 3, 4, 5, 6, 7 "
            ."  ORDER BY Inghis, Inging ";
        $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
        $row = mysql_fetch_array($res);

        $wresponsable = $row['ingnre'];

        return $wresponsable;
    }

 // Funcion que permite extraer la edad del paciente en años, meses y dias.
  function calcularAnioMesesDiasTranscurridos($fecha_inicio, $fecha_fin = '')
    {
        $datos = array('anios'=>0,'meses'=>0,'dias'=>0);

        if($fecha_inicio != '' && $fecha_inicio != '0000-00-00')
        {
            $fecha_de_nacimiento = $fecha_inicio;

            $fecha_actual = date ("Y-m-d");
            if($fecha_fin != '' && $fecha_fin != '0000-00-00')
            {
                $fecha_actual = $fecha_fin;
            }
            // echo "<br>Fecha final: $fecha_actual";
            // echo "<br>Fecha inicio: $fecha_de_nacimiento";

            // separamos en partes las fechas
            $array_nacimiento = explode ( "-", $fecha_de_nacimiento );
            $array_actual = explode ( "-", $fecha_actual );

            $anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años
            $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
            $dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días

            //ajuste de posible negativo en $días
            if ($dias < 0)
            {
                --$meses;

                //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
                switch ($array_actual[1]) {
                    case 1:     $dias_mes_anterior=31; break;
                    case 2:     $dias_mes_anterior=31; break;
                    case 3:
                            if (checkdate(2,29,$array_actual[0]))
                            {
                                $dias_mes_anterior=29; break;
                            } else {
                                $dias_mes_anterior=28; break;
                            }
                    case 4:     $dias_mes_anterior=31; break;
                    case 5:     $dias_mes_anterior=30; break;
                    case 6:     $dias_mes_anterior=31; break;
                    case 7:     $dias_mes_anterior=30; break;
                    case 8:     $dias_mes_anterior=31; break;
                    case 9:     $dias_mes_anterior=31; break;
                    case 10:     $dias_mes_anterior=30; break;
                    case 11:     $dias_mes_anterior=31; break;
                    case 12:     $dias_mes_anterior=30; break;
                }
                $dias=$dias + $dias_mes_anterior;
            }

            //ajuste de posible negativo en $meses
            if ($meses < 0)
            {
                --$anos;
                $meses=$meses + 12;
            }
            //echo "<br>Tu edad es: $anos años con $meses meses y $dias días";
            $datos['anios'] = $anos;

        }

        return $datos;
    }

//Funcion que cancela la solicitud de cama si cancelan la admision de un paciente
function cancelar_solicitud_cama($worigen, $wmotivo, $wdatos_pac, $wdestino, $wsolicito, $wccoorigen, $whistoria, $whab_asignada, $wemp_pmla, $wusuario)
{

	global $conex;
	global $wbasedato;

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

    $q2 =        "  SELECT max(id) as id "
                ."    FROM ".$wcencam."_000003 A"
                ."   WHERE Hora_llegada      = '00:00:00' "
                ."     AND Hora_Cumplimiento = '00:00:00' "
                ."     AND Anulada           = 'No' "
                ."     AND Historia          != ''"
                ."     AND Historia          = '".$whistoria."'"
                ."     AND Central           = '".$wcentral_camas."'";
    $res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q2 . "-" . mysql_error());
    $row = mysql_fetch_array($res2);
    $wid = $row['id'];

    $q3 =        "  SELECT Hab_asignada "
                ."    FROM ".$wcencam."_000003"
                ."   WHERE id      = '".$wid."' ";
    $res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q3 . "-" . mysql_error());
    $row_hab = mysql_fetch_array($res3);
    $whab_pro = $row_hab['Hab_asignada'];

	//La habitacion se pone en proceso de ocupacion
    $q_hab =  " UPDATE ".$wbasedato."_000020 "
        . "    SET Habpro = 'off'"
        . "  WHERE Habcod = '".$whab_pro."'";
    $err = mysql_query($q_hab, $conex) or die (mysql_errno() . $q_hab . " - " . mysql_error());


    $q = "UPDATE ".$wcencam."_000003
		     SET Anulada = 'Si'
		   WHERE id     = '".$wid."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());

}

//Funcion que consulta el tipo de cama (compartida, individual, uce, etc)
function consultartipocama($wemp_pmla,$whab_asignada)
	{

		global $conex;
		global $wbasedato;

		$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
		$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

		$q2 =        "  SELECT habtip "
	                ."    FROM ".$wbasedato."_000020"
	                ."   WHERE Habcod      = '".$whab_asignada."'";
	    $res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q2 . "-" . mysql_error());
	    $row = mysql_fetch_array($res2);
	    $wcodigotipo = $row['habtip'];

		//Busca en la tabla de tipos de cama
 		$q = 		 "  SELECT Tipcod, Tipdes "
                    ."    FROM ".$wcencam."_000007 "
                    ."   WHERE Tipcod  = '".$wcodigotipo."'";
        $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
        $row = mysql_fetch_array($rescam);


    	if ($row['Tipcod'] != '')
    	{
    		$wtipo_cama = $row['Tipcod']." - ".$row['Tipdes'];
    	}
    	else
    	{
    		$wtipo_cama = '';
    	}

    	return $wtipo_cama;

	}


// Funcion que registra automaticamente la solicitud de cama. // 14 dic Jonatan Lopez
function solicitud_cama($worigen, $wmotivo, $wdatos_pac, $wdestino, $wsolicito, $wccoorigen, $whistoria, $whab_asignada, $wemp_pmla, $wusuario)
{

	global $conex;
    global $wemp_pmla;
    global $wbasedato;

	$wfecha=date("Y-m-d");
	$whora = (string)date("H:i:s");
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
	$wunidestsolcamas1 = consultarAliasPorAplicacion($conex, $wemp_pmla, 'UnidadDestinoSolCamas');
    $wunidestsolcamas_dato = explode("-", $wunidestsolcamas1);
    $wmotivodb = $wunidestsolcamas_dato[0];
    $wdestinodb = $wunidestsolcamas_dato[1];

    //TRAIGO LOS MOTIVOS DE LLAMADO
     $q = "  SELECT Descripcion "
        ."    FROM ".$wcencam."_000001"
        ."   WHERE Estado = 'on' "
        ."     AND id = ".$wmotivodb."";
    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
    $row = mysql_fetch_array($res);
    $wmotivo_solicitud = $row['Descripcion'];

    //Traigo el nombre del destino que se marcará automaticamente.
    $q =     " SELECT nombre "
            ."   FROM ".$wcencam."_000004"
            ."  WHERE Estado = 'on' "
            ."    AND id = ".$wdestinodb."";
    $res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
    $row = mysql_fetch_array($res);
    $wunidestsolcamas = $row['nombre'];

    //Traigo los datos del paciente
	$q =     " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex "
	        ."   FROM root_000036, root_000037 "
	        ."  WHERE orihis = '".$whistoria."'"       //Como Historia
	        ."    AND oriori = '".$wemp_pmla."'"
	        ."    AND oriced = pacced "
	        ."    AND oritid = pactid ";
    $reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
    $rowhab = mysql_fetch_array($reshab);

    $wedad = calcularAnioMesesDiasTranscurridos($rowhab['Pacnac'], $fecha_fin = '');
    $wresponsable = traerresponsable($whistoria, $wemp_pmla);

	$wdatos_pac = "<b>Historia: ".$whistoria."</b><br>Pac: ".$rowhab['Pacno1']." ".$rowhab['Pacno2']." ".$rowhab['Pacap1']." ".$rowhab['Pacap2']."<br>Edad:".$wedad['anios']."<br>Genero:".$rowhab['Pacsex']."<br>Responsable:".$wresponsable;

	$wcamillero = consultartipocama($wemp_pmla,$whab_asignada);

	//Consulta si ya hay un solicitud activa para la historia, si es asi no hace el registro.
	$q2 =        "  SELECT Motivo, Observacion, Destino, Solicito, Camillero, A.Hora_data, Hora_respuesta, Habitacion, A.Id, observ_central, A.Fecha_data "
                ."    FROM ".$wcencam."_000003 A"
                ."   WHERE Origen = '".$worigen."'"
                ."     AND Hora_llegada      = '00:00:00' "
                ."     AND Hora_Cumplimiento = '00:00:00' "
                ."     AND Anulada           = 'No' "
                ."     AND Historia          != ''"
                ."     AND Historia          = '".$whistoria."'"
                ."     AND Central           = '".$wcentral_camas."'";
    $res2 = mysql_query($q2,$conex);
    $numsolicitudes = mysql_num_rows($res2);

    if ($numsolicitudes == 0)
	    {

	    //La habitacion se pone en proceso de ocupacion
        $q =  " UPDATE ".$wbasedato."_000020 "
            . "    SET Habpro = 'on'"
            . "  WHERE Habcod = '".$whab_asignada."'";
        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

		//Se registra la solicitud en la tabla 3 de cencam
		$q = "  INSERT INTO ".$wcencam."_000003 (   Medico  ,   Fecha_data,    Hora_data,         Origen  ,              Motivo     ,     Habitacion,              Destino        ,   Solicito    ,      Ccosto,      Camillero,    Anulada,   Central             ,    Historia      ,   Hab_asignada       , Fec_asigcama, Hora_asigcama, Usu_central, Observacion, Seguridad) "
						        ."       VALUES ('".$wcencam."','".$wfecha."','".$whora."'  ,'".$worigen."','".$wmotivo_solicitud."', '".$wdatos_pac.  "', '".$wunidestsolcamas."','".$wusuario."','".$worigen."','".$wcamillero."',   'No' , '".$wcentral_camas."' , '".$whistoria."' , '".$whab_asignada."' , '".$wfecha."' , '".$whora."' , '".$wusuario."', 'Automatica admisiones','C-".$wusuario."')";
	    $res2 = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());
	    $wid=mysql_insert_id(); // Ultimo id insertado.

	    registrarAsignacion($wid, $wusuario, $wemp_pmla); //Registrar en la tabla cencam_000010 la solicitud.

		}

}

// Funcion que registra la solicitud en la tabla 10 de cencam
function registrarAsignacion($wid, $wusuario, $wemp_pmla)
    {

    global $conex;
    global $wcencam;

    $wfecha=date("Y-m-d");
	$whora = (string)date("H:i:s");

    $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

    $q =     "  SELECT Acaids "
            ."    FROM ".$wcencam."_000010"
            ."   WHERE Acaids   = '".$wid."'"
            ."     AND Acaest   = 'on'";
    $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $num = mysql_num_rows($res);

    //Si el id ya tiene registro en on en la tabla 10 de cencam, no hara registro de datos.
    if ($num == 0)
        {
        $q =  " INSERT INTO ".$wcencam."_000010(   Medico       ,   Fecha_data,   Hora_data,   Acaids,   Acaest,   Acarea, Seguridad     ) "
                                . "    VALUES('".$wcencam."','".$wfecha."','".$whora."','".$wid."'        ,'on'   ,'off' , 'C-" . $wusuario . "')";
        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
        }

    }


/**
 * Consulta si un paciente tiene al menos un movimiento hospitalario (registro en la tabla 17)
 *
 * @param unknown_type $historiaClinica
 * @param unknown_type $ingresoHistoriaClinica
 * @return unknown
 */

function pacienteConMovimientoHospitalario($historiaClinica, $ingresoHistoriaClinica, $servicioOrigen){

	global $conex;
	global $wbasedato;

	$es = false;

	$q = "SELECT
				Eyrtip
		 	FROM
		 		".$wbasedato."_000017,
		 		(
				SELECT
					MAX(CAST(eyr.Eyrnum AS SIGNED)) cdMvto
				FROM
					".$wbasedato."_000017 eyr
				WHERE
					eyr.Eyrhis='".$historiaClinica."'
					AND eyr.Eyring='".$ingresoHistoriaClinica."'
					AND eyr.Eyrest='on'
				)a
			WHERE
				Eyrnum = a.cdMvto
			";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$fila = mysql_fetch_array($err);
		if(($fila['Eyrtip'] == 'Entrega') && !esCirugia($conex,$servicioOrigen) && !esUrgencias($conex,$servicioOrigen)){
			$es = true;
		}
	}
	return $es;
}

function consultarPacientesPreentregados($centroCostos){

	global $wbasedato;
	global $conex;

	$q = "SELECT DISTINCT
			Ubihis, Ubiing, Pacno1, Pacno2, Pacap1, Pacap2, Ubisac, Eyrsde, Cconom, Eyrhde
		FROM
			".$wbasedato."_000018, ".$wbasedato."_000017, root_000037, root_000036, ".$wbasedato."_000011
		WHERE
			Ubisac = '".$centroCostos."'
			AND Ccocod = Eyrsde
			AND Ubihis = Eyrhis
			AND Ubiing = Eyring
			AND Eyrhis = Orihis
			AND Eyring = Oriing
			AND Pacced = Oriced
			AND Pactid = Oritid
			AND Ubisan = ''
			AND Ubihan = ''
			AND Ubihac = ''
			AND Ubiptr = 'on'
			AND Eyrest = 'on'
		";

//		echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$coleccion = array();

	if($filas > 0){
		$cont1 = 0;

		while($cont1 < $filas){
			$fila = mysql_fetch_array($res);
			$info = new pacientesPreentregadosDTO();

			$info->historiaPaciente = $fila['Ubihis'];
			$info->ingresoHistoriaPaciente = $fila['Ubiing'];
			$info->nombrePaciente = $fila['Pacno1']." ".$fila['Pacno2']." ".$fila['Pacap1']." ".$fila['Pacap2'];
			$info->ccoActual = $fila['Ubisac'];
			$info->ccoDestino = $fila['Eyrsde']." - ".$fila['Cconom'];
			$info->habitacionDestino = $fila['Eyrhde'];

			$coleccion[] = $info;
			$cont1++;
		}
	}
	return $coleccion;
}

function consultarUltimoMovimientoPaciente($whistoria,$wingreso){

	global $wbasedato;
	global $conex;

	$q = "SELECT
			Eyrhis, Eyring, Eyrsor, Eyrsde, Eyrhor, Eyrhde, Eyrtip, Eyrest
		FROM
			".$wbasedato."_000017
		WHERE
			Eyrhis = '".$whistoria."'
			AND Eyring = '".$wingreso."'
			AND Eyrtip = 'ENTREGA'
			AND Eyrest = 'on'
		";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$fila = mysql_fetch_array($res);

		$info = new movimientoHospitalarioDTO();

		$info->historia = $fila['Eyrhis'];
		$info->ingreso = $fila['Eyring'];
		$info->servicioOrigen = $fila['Eyrsde'];
		$info->servicioDestino = $fila['Eyrsor'];
		$info->habitacionOrigen = $fila['Eyrhor'];
		$info->habitacionDestino = $fila['Eyrhde'];
		$info->tipoMovimiento = $fila['Eyrtip'];
	}
	return $info;
}

function modificarUbicacionActualPaciente($whistoria, $wingreso, $servicioOrigen, $habitacionOrigen, $servicioDestino, $habitacionDestino) {

	global $wbasedato;
	global $conex;

	$q = "UPDATE
			".$wbasedato."_000018
		SET
			Ubisac = '".$servicioDestino."',
			Ubihac = '".$habitacionDestino."',
			Ubisan = '".$servicioOrigen."',
			Ubihan = '".$habitacionOrigen."'
		WHERE
			Ubihis = '".$whistoria."'
			AND Ubiing = '".$wingreso."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$num = mysql_affected_rows();
}

function modificarHistoriaHabitacion($conex, $wbasedato, $whistoria, $wingreso, $wcodigoHabitacion){
	
	
	//Solo hay liberacion si la historia e ingreso tienen datos.
	if($whistoria != "" and $wingreso != ""){
	
		//Se libera el cubiculo si lo tiene.
		$q_cub = "UPDATE ".$wbasedato."_000020
				 SET Habhis = '', Habing = '', Habdis = 'on'
			   WHERE Habhis = '".$whistoria."'
				 AND Habing = '".$wingreso."'";
		$res_cub = mysql_query($q_cub, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cub . " - " . mysql_error());
	
	}
	
	$q = "UPDATE ".$wbasedato."_000020
		     SET Habhis = '".$whistoria."',
			     Habing = '".$wingreso."',
				 Habdis = 'off'
		   WHERE Habcod = '".$wcodigoHabitacion."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}

function deshabilitarUltimoMovimientoHospitalario($conex, $wbasedato, $whistoria, $wingreso){
	$q = "UPDATE
			".$wbasedato."_000017
		SET
			Eyrest = 'off'
		WHERE
			Eyrhis = '".$whistoria."'
			AND Eyrtip = 'Entrega'
			AND Eyring = '".$wingreso."';";

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}

function eliminarIngresoPaciente($conex, $wbasedato, $historia, $ingreso, $servicio)
{
	$q = "	SELECT Ubisac, Ubihac, Ubisan, Ubihan
			  FROM ".$wbasedato."_000018
			 WHERE Ubihis = '".$historia."'
			   AND Ubiing = '".$ingreso."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	$ubisac = $row['Ubisac'];
	$ubihac = $row['Ubihac'];
	$ubiptr = 'off';

	if($row['Ubisan']!='' && $row['Ubisan']!='NO APLICA' && $row['Ubisan']!='.')
	{
		$ubisac = $row['Ubisan'];
		$ubihac = $row['Ubihan'];
	}

	if(esCirugia($conex,$ubisac))
		$ubiptr = 'on';

	$q = "UPDATE
				".$wbasedato."_000018
			SET
				Ubiptr = '".$ubiptr."', Ubisac = '".$ubisac."', Ubihac = '".$ubihac."', Ubisan = '', Ubihan = ''
			WHERE
				Ubihis = '".$historia."'
				AND Ubiing = '".$ingreso."';";

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}

function actualizarOrigenHistoriaPacientePorHistoria($conex, $historia){

	$q = "UPDATE
			root_000037
		SET
			Oriing='".$pac['ing']."'
		WHERE
			Orihis = '0' ";

	$rs = mysql_query($q,$conex);
}

//Esta funcion reemplaza la funcion consultarPacienteUnix, ya que este programa no debe depender de la conexion con este sistema. (25 Noviembre 2013 Jonatan)
//Funcion que consulta todos los datos de un paciente, verificando que el alta definitiva sea off, osea que este activo en la clinica.
function consultarPacienteMatrix($pacienteConsulta){
	
	global $wbasedato;
	global $wemp_pmla;
	global $conex;
	
	$paciente = new pacienteDTO();
	
	$ingreso = consultarUltimoIngresoHistoria($conex, $pacienteConsulta->historiaClinica, $wemp_pmla);
	
	$q = "SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, pacnac, pacsex, Ubisac, Ubihac, Ubisan, Ubihan, 
				 d.fecha_data as fechaIngreso, d.Hora_data as horaIngreso, Ingres, Ingnre, Ingtip
		    FROM root_000036 as a, root_000037 as b, ".$wbasedato."_000018 as c, ".$wbasedato."_000016 as d
		   WHERE oriced = pacced 
			 AND oritid = pactid
			 AND Ubihis = Orihis
			 AND Ubiing = Oriing
			 AND Ubihis = Inghis
			 AND Ubiing = Inging
			 AND Ubiald = 'off'
			 AND orihis = '".$pacienteConsulta->historiaClinica."'
			 AND oriing = '".$ingreso."'
			 AND oriori = '".$wemp_pmla."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if ($num > 0)
	{
		$info = mysql_fetch_array($res);

		$paciente->historiaClinica = $pacienteConsulta->historiaClinica;
		$paciente->ingresoHistoriaClinica = $ingreso;
		$paciente->nombre1 = $info['pacno1'];
		$paciente->nombre2 = $info['pacno2'];
		$paciente->apellido1 = $info['pacap1'];
		$paciente->apellido2 = $info['pacap2'];
		$paciente->documentoIdentidad = $info['pacced'];
		$paciente->tipoDocumentoIdentidad = $info['pactid'];
		$paciente->fechaNacimiento = $info['pacnac'];
		$paciente->genero = $info['pacsex'];
		$paciente->fechaIngreso = $info['fechaIngreso'];
		$paciente->horaIngreso = $info['horaIngreso'];
		$paciente->habitacionActual = $info['Ubihac'];
		$paciente->numeroIdentificacionResponsable = $info['Ingres'];
		$paciente->nombreResponsable = $info['Ingnre'];		
		$paciente->servicioActual = $info['Ubisac'];
		$paciente->tipoResponsable = $info['Ingtip'];
		
	}
			
	return $paciente;
}

/**
 * Existe un registro del paciente en la tabla 36
 *
 * @param unknown_type $conex
 * @param unknown_type $paciente
 * @return unknown
 */
function existeEnTablaUnicaPacientes($conex,$paciente){

	$esta = false;

	$q = "SELECT
				*
		  	FROM
		  		root_000036
			WHERE
				Pacced = '".$paciente->documentoIdentidad."'
				AND Pactid = '".$paciente->tipoDocumentoIdentidad."'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$esta = true;
	}
	return $esta;
}

function insertarPacienteTablaUnica($conex, $paciente){
	$date=date("Y-m-d");
	$hora=date("H:i:s");

	$q = "INSERT INTO
			root_000036
				(medico,fecha_data,hora_data,Pacced,Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Pactid,Seguridad)
			VALUES
				('root','".$date."','".$hora."', '$paciente->documentoIdentidad', '$paciente->nombre1', '".$paciente->nombre2."', '".$paciente->apellido1."', '".$paciente->apellido2."', '".$paciente->fechaNacimiento."', '".$paciente->genero."', '$paciente->tipoDocumentoIdentidad',  'A-root' )";

	$err=mysql_query($q,$conex);
}


function existeEnTablaIngresos($conex,$paciente,$origen){
	$esta = false;

	$q = "SELECT
				*
		  	FROM
		  		root_000037
			WHERE
				Oriced = '".$paciente->documentoIdentidad."'
				AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
				AND Orihis = '".$paciente->historiaClinica."'
				AND Oriori = '".$origen."'";

//	echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$esta = true;
	}
	return $esta;
}

function existeEnTablaResponsables($conex,$pacienteMatrix, $wemp_pmla){
	
	global $wbasedato;
	$esta = false;
	
	$q = "SELECT
				*
		  	FROM
		  		".$wbasedato."_000016
			WHERE
				Inghis = '".$pacienteMatrix->historiaClinica."'
				AND Inging = '".$pacienteMatrix->ingresoHistoriaClinica."';";

//	echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$esta = true;
	}
	return $esta;
}

function existeEnTablaMovimientos($conex,$pacienteMatrix){
	
	global $wbasedato;
	$esta = false;

	$q = "SELECT
				*
		  	FROM
		  		".$wbasedato."_000017
			WHERE
				Eyrhis = '".$pacienteMatrix->historiaClinica."'
				AND Eyring = '".$pacienteMatrix->ingresoHistoriaClinica."'
				AND Eyrest = 'on';";

//	echo $q;

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$esta = true;
	}
	return $esta;
}

function insertarIngresoPaciente($conex, $paciente, $wemp_pmla){

	$date=date("Y-m-d");
	$hora=date("H:i:s");

	$q = "INSERT INTO root_000037
			( medico,fecha_data,hora_data,Oriced,Orihis,Oriing,Oriori,Oritid,Seguridad)
		VALUES
			('root','".$date."','".$hora."','".$paciente->documentoIdentidad."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$wemp_pmla."', '".$paciente->tipoDocumentoIdentidad."',  'A-root' )";
//	echo $q;
	$err=mysql_query($q,$conex);
}

function actualizarIngresoPaciente($conex, $pacienteAnterior, $pacienteNuevo, $origen){

	$q = "UPDATE
			root_000037
		SET
			Orihis = '".$pacienteNuevo->historiaClinica."',
			Oriing = '".$pacienteNuevo->ingresoHistoriaClinica."'
		WHERE
			Orihis = '".$pacienteNuevo->historiaClinica."'
			AND Oriced = '$pacienteNuevo->documentoIdentidad'
			AND Oritid = '$pacienteNuevo->tipoDocumentoIdentidad'
			AND Oriori = '$origen';";

	$err1=mysql_query($q,$conex);
}

function insertarResponsablePaciente($conex, $paciente, $wemp_pmla){
	
	global $wbasedato;	
	$date=date("Y-m-d");
	$hora=date("H:i:s");

	$q = "INSERT INTO ".$wbasedato."_000016
			(medico,Fecha_data,Hora_data,Inghis,Inging,Ingres,Ingnre,Ingtip,Seguridad)
		VALUES
			('movhos','".$date."','".$hora."','".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$paciente->numeroIdentificacionResponsable."', '".$paciente->nombreResponsable."', '".$paciente->tipoResponsable."', 'A-movhos' )";

//	echo $q;
	$err=mysql_query($q,$conex);
}

function actualizarResponsablePaciente($conex, $pacienteAnterior, $pacienteNuevo){
	
	global $wbasedato;
	
	$q = "UPDATE
			".$wbasedato."_000016
		SET
			Ingres = '".$pacienteNuevo->numeroIdentificacionResponsable."',
			Ingnre = '".$pacienteNuevo->nombreResponsable."',
			Ingtip = '".$pacienteNuevo->tipoResponsable."'
		WHERE
			Inghis = '".$pacienteAnterior->historiaClinica."'
			AND Inging = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
//	echo $q;
	$err1=mysql_query($q,$conex);
}

function actualizarDocumentoPacienteTablaIngresos($conex, $pacienteAnterior, $pacienteNuevo){

	$q = "UPDATE
			root_000037
		SET
			Oriced = '".$pacienteNuevo->documentoIdentidad."',
			Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE
			Orihis = '".$pacienteAnterior->historiaClinica."'
			AND Oriing = '".$pacienteAnterior->ingresoHistoriaClinica."' ";

	$err1=mysql_query($q,$conex);
}

function actualizarDocumentoPacienteTablaUnica($conex, $pacienteAnterior, $pacienteNuevo){

	$q = "UPDATE
			root_000036
		SET
			Pacced = '".$pacienteNuevo->documentoIdentidad."',
			Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE
			Pacced = '".$pacienteAnterior->documentoIdentidad."'
			AND Pactid = '".$pacienteAnterior->tipoDocumentoIdentidad."' ";

	$err1=mysql_query($q,$conex);
}

function actualizarTablaUnicaPacientes($conex, $pacienteAnterior, $pacienteNuevo){

	$q = "UPDATE
			root_000036
		SET
			Pacced = '".$pacienteNuevo->documentoIdentidad."',
			Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE
			Pacced = '".$pacienteAnterior->documentoIdentidad."'
			AND Pactid = '".$pacienteAnterior->tipoDocumentoIdentidad."' ";

	$err1=mysql_query($q,$conex);
}

function eliminarPacienteTablaUnica($conex,$paciente){

	$q = "DELETE FROM
			root_000036
		WHERE
			Pacced = '".$paciente->documentoIdentidad."'
			AND Pactid = '".$paciente->tipoDocumentoIdentidad."' ";

	$err1=mysql_query($q,$conex);
}

function grabarIngresoPaciente($conex,$ingreso){
	
	global $wbasedato;
	$date=date("Y-m-d");
	$hora=date("H:i:s");

	$q = "INSERT INTO
			".$wbasedato."_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,Ubiptr,Seguridad)
		VALUES
			('movhos','".$date."','".$hora."','".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', '".$ingreso->servicioActual."', '".$ingreso->servicioAnterior."', '".$ingreso->habitacionActual."',  '".$ingreso->habitacionAnterior."','".$ingreso->altaEnProceso."', '".$ingreso->altaDefinitiva."', '".$ingreso->fechaAltaProceso."','".$ingreso->horaAltaProceso."', '".$ingreso->fechaAltaDefinitiva."', '".$ingreso->horaAltaDefinitiva."', '".$ingreso->enProcesoTraslado."', '".$ingreso->usuario."' )";

	$err=mysql_query($q,$conex);
}

function modificarEstadoTrasladoPaciente($conex,$ingresoPaciente){
	
	global $wbasedato;
	
	$q = "UPDATE
				".$wbasedato."_000018
			SET
				Ubiptr = 'on'
			WHERE
				Ubihis = '".$ingresoPaciente->historiaClinica."'
				AND Ubiing = '".$ingresoPaciente->ingresoHistoriaClinica."'
			";
			
			//return;
	$err=mysql_query($q,$conex);
}

function actualizarUbicacionPaciente($conex,$ingresoAnterior, $ingresoNuevo){

    global $wbasedato;
    $date=date("Y-m-d");
	$hora=date("H:i:s");
	
	
	$q = "	SELECT Ubisac, Ubihac, Ubisan, Ubihan
			  FROM ".$wbasedato."_000018
			 WHERE Ubihis = '".$ingresoAnterior->historiaClinica."'
			   AND Ubiing = '".$ingresoAnterior->ingresoHistoriaClinica."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	if(esUrgencias($conex,$ingresoNuevo->servicioAnterior) && $row['Ubisac']!=$ingresoNuevo->servicioAnterior)
		$ingresoNuevo->servicioAnterior = $row['Ubisac'];

	 $q = "UPDATE ".$wbasedato."_000018 SET
			Ubisac = '".$ingresoNuevo->servicioActual."',
			Ubisan = '".$ingresoNuevo->servicioAnterior."',
			Ubihac = '".$ingresoNuevo->habitacionActual."',
			Ubihan = '".$ingresoNuevo->habitacionAnterior."',
            Ubiptr = '".$ingresoNuevo->enProcesoTraslado."'
		WHERE
			Ubihis = '".$ingresoAnterior->historiaClinica."'
			AND Ubiing = '".$ingresoAnterior->ingresoHistoriaClinica."' ";

	$err=mysql_query($q,$conex);
	
}

function devolverAltaPaciente($conex,$ingresoPaciente){
	
	global $wbasedato;
	$date=date("Y-m-d");
	$hora=date("H:i:s");
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	$q = "	SELECT Ubiald
			  FROM ".$wbasedato."_000018
			 WHERE Ubihis = '".$ingresoPaciente->historiaClinica."'
			   AND Ubiing = '".$ingresoPaciente->ingresoHistoriaClinica."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	if($row['Ubiald']=='on')
	{
		$q = " UPDATE ".$wbasedato."_000018
				  SET Ubiald = 'off', Ubifad = '0000-00-00', Ubihad = '00:00:00', Ubiuad = ''
				WHERE Ubihis = '".$ingresoPaciente->historiaClinica."'
				  AND Ubiing = '".$ingresoPaciente->ingresoHistoriaClinica."' ";
		$err=mysql_query($q,$conex);
	}

	$qmtr = " SELECT Mtrcon
				FROM ".$whce."_000022
			   WHERE Mtrhis = '".$ingresoPaciente->historiaClinica."'
				 AND Mtring = '".$ingresoPaciente->ingresoHistoriaClinica."' ";
	$resmtr = mysql_query($qmtr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmtr . " - " . mysql_error());
	$rowmtr = mysql_fetch_array($resmtr);

	$qcon = " SELECT Concod
				FROM ".$whce."_000035
			   WHERE Conalt = 'on'
				 OR Conmue = 'on' ";
	$rescon = mysql_query($qcon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcon . " - " . mysql_error());

	$upd_con = 0;
	while($rowcon = mysql_fetch_array($rescon))
	{
		if($rowcon[0]==$rowmtr[0])
			$upd_con = 1;
	}

	if($upd_con==1)
	{
		$q = "UPDATE ".$whce."_000022
				 SET Mtrcon = ''
			   WHERE Mtrhis = '".$ingresoPaciente->historiaClinica."'
				 AND Mtring = '".$ingresoPaciente->ingresoHistoriaClinica."' ";
		$err=mysql_query($q,$conex);
	}
}

function grabarMovimientoHospitalario($conex,$ingresoPaciente,$usuario){
	
	global $wbasedato;
	
	$date=date("Y-m-d");
	$hora=date("H:i:s");
	
	//Bloqueo de tablas
	$q = "LOCK TABLE ".$wbasedato."_000001 LOW_PRIORITY WRITE";
	$err = mysql_query($q, $conex);

//	mensajeEmergente("bloqueo tablas");
	//Actualizacion del consecutivo de entrega y recibo
	$q = " UPDATE
				".$wbasedato."_000001
			SET
				connum=connum + 1
			WHERE
				contip='entyrec' ";

	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	//Captura del consecutivo de entrega y recibo
	$q = "SELECT
			connum
		FROM
			".$wbasedato."_000001
		WHERE
			contip='entyrec' ";

	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	$row = mysql_fetch_array($err);
	$wconsec = $row[0];
//	mensajeEmergente("aumentado consetutivo".$wconsec);

//	mensajeEmergente("desbloqueo tablas");
	$q = "UNLOCK TABLES";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

//	mensajeEmergente("insert");
	$q = "INSERT INTO
			".$wbasedato."_000017 (Medico,Fecha_data,Hora_data,Eyrnum,Eyrhis,Eyring,Eyrsor,Eyrsde,Eyrhor,Eyrhde,Eyrtip,Eyrest,Seguridad)
		VALUES
			('movhos','".$date."','".$hora."','".$wconsec."','".$ingresoPaciente->historiaClinica."','".$ingresoPaciente->ingresoHistoriaClinica."','".$ingresoPaciente->servicioAnterior."','".$ingresoPaciente->servicioActual."','".$ingresoPaciente->habitacionAnterior."','".$ingresoPaciente->habitacionActual."','Entrega','on','C-".$usuario->codigo."')";

//	echo $q;
	$err=mysql_query($q,$conex);
	echo mysql_error();
}

function marcarHabitacion($conex,$habitacion){

	global $wbasedato;
	
	//Solo hay liberacion si la historia e ingreso tienen datos.
	if($habitacion->historiaClinica != "" and $habitacion->ingresoHistoriaClinica != ""){
	
		//Libero el cubiculo actual.
		$q_cub = "UPDATE ".$wbasedato."_000020
					 SET Habhis = '', Habing = '', Habdis = 'on'
				   WHERE Habhis = '".$habitacion->historiaClinica."'
					 AND Habing = '".$habitacion->ingresoHistoriaClinica."'";
		$res_cub = mysql_query($q_cub, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cub . " - " . mysql_error());
	
	}
		
    $q = "UPDATE ".$wbasedato."_000020
		     SET Habhis = '".$habitacion->historiaClinica."', 
				 Habing = '".$habitacion->ingresoHistoriaClinica."', 
				 Habdis = '".$habitacion->disponible."'
		   WHERE Habcod = '".$habitacion->codigo."' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function consultarCentroCostoAdmisiones($conex, $wselcco, $wbasedato){
	
	$es = "";

	$q = "SELECT Ccoadm
		 	FROM ".$wbasedato."_000011
		   WHERE Ccocod = '".$wselcco."'
		     AND Ccoadm = 'on'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccoadm'] == 'on') ? $es = 'on' : $es = 'off';
	}

	return $es;
	
	
}


function esUrgencias($conex, $servicio){
	
	global $wbasedato;
	
	$es = false;

	$q = "SELECT
				Ccourg
		 	FROM
		 		".$wbasedato."_000011
			WHERE
				Ccocod = '".$servicio."' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccourg'] == 'on') ? $es = true : $es = false;
	}

	return $es;
}

function esCirugia($conex, $servicio){
	
	global $wbasedato;
	
	$es = false;

	$q = "SELECT
				Ccocir
		 	FROM
		 		".$wbasedato."_000011
			WHERE
				Ccocod = '".$servicio."' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccocir'] == 'on') ? $es = true : $es = false;
	}

	return $es;
}

function pacienteIngresado($conex, $paciente){
	
	global $wbasedato;
	
	$es = false;

	$q = "SELECT
				*
		 	FROM
		 		".$wbasedato."_000018
			WHERE
				Ubihis = '".$paciente->historiaClinica."'
				AND Ubiing   = '".$paciente->ingresoHistoriaClinica."'
			";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}

/*
 * Inicio aplicacion
 */
include_once("root/comun.php");

$wactualiz = "Agosto 31 de 2016";

if (!isset($user)){
	if (!isset($_SESSION['user'])) {
		session_register("user");
	}
}

//Codigo de usuario que ingreso al sistema
if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));

$usuario = new Usuario();

$usuario->codigo = $wuser;

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Valida codigo de usuario en sesion si no esta registrado el sistema termina la ejecucion
if (!isset($_SESSION['user'])){
	terminarEjecucion("usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.");
} else {
	//Conexion base de datos Matrix
	$conex = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "HCE");
	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');

	//Forma
	echo "<form name='forma' action='admision_movhos.php' method=post>";
	echo "<input type='HIDDEN' NAME='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' NAME='wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' NAME='wbasedatohce' value='".$wbasedatohce."'>";
	echo "<input type='HIDDEN' NAME='wcencam' value='".$wcencam."'>";

	/* La variable waccion contendrá la raiz de la accion que se va a realizar.
	 * 1.  waccion no definida o vacia:  Muestra la pantalla inicial
	 * 2.  waccion (a): Realiza la admisión de un paciente
	 * 3.  waccion (b): Realiza la validacion de la informacion de un paciente
	 * 4.  waccion (c): Realiza la cancelación de la admisión de un paciente
	 */
	if(!isset($waccion)){
		$waccion = "";
	}else{
		if($waccion == ''){
			if(isset($whistoria)){
				$waccion = "b";
			}
		}
	}
	echo "<input type='HIDDEN' NAME='waccion'>";

	encabezado("ADMISIÓN DE PACIENTES", $wactualiz, "clinica");

	//if(conexionOdbc($conex, $wbasedato, &$conexUnix, 'facturacion')){ //Se vuelve comentario ya que no tiene que establecer conexion con unix  21 Noviembre 2013 Jonatan
	    //Ya no hay que verificar la conexion con unix 2 Noviembre de 2013 Jonatan
		//En esta seccion ya hay conexión con unix verificada.

		$ccoUrgencias = consultarCcoUrgencias();
		$ccoCirugia = consultarCcoCirugia();

		//************INICIO LOG::: Log de todas las admisiones
		$debug = true;
		if($debug){
			$fechaLog = date("Y-m-d");
			$horaLog = date("H:i:s");

	    	//Creacion de un archivo plano para tomar una imagen de la informacion de las camas en ese momento
	    	$nombreArchivo = "admisiones.txt";

	    	//Apuntador en modo de adicion si no existe el archivo se intenta crear...
	    	$archivo = fopen($nombreArchivo, "a");
	    	if(!$archivo){
	    		$archivo = fopen($nombreArchivo, "w");
	    	}

	    	$contenidoLog = "****INICIO Admision..$fechaLog - $horaLog.  Usuario:$usuario->codigo \r\n";
	    }
		//************FIN LOG::: de admisiones

		switch($waccion){

			case 'a': //Pantalla para la seleccion de servicio y habitacion destino (si aplica)
				echo "<input type=hidden name=wselcco value=".$wselcco.">";
				$onchange = ''; // Esta variable controla que se haga onchange si el servicio es de urgencias.
				//Centro de costos que admite, cco urgencias y cco cirugia
				$ccoDeAdmision = consultarCentroCosto($conex, $wselcco, $wbasedato);				
				$admisiones = consultarCentroCostoAdmisiones($conex, $wselcco, $wbasedato);
				echo "<input type=hidden id='admisiones' value='".$admisiones."'>";				
				
				$wurgencias = esUrgencias($conex,$wselcco);
				($ccoDeAdmision == $ccoUrgencias) ? $admiteUrgencias = true : $admiteUrgencias = false;
				($ccoDeAdmision == $ccoCirugia) ? $admiteCirugia = true : $admiteCirugia = false;

				/* Los centros de costos que admiten tienen diferentes comportamientos:
				 *
				 * 1. URGENCIAS: Permite seleccion del servicio y habitacion destino PERO no ocupa habitación.
				 * 2. CIRUGIA:	NO PERMITE seleccion del servicio y habitacion destino, se deja EN RECUPERACION, no ocupa habitacion
				 * 3. RESTO: Se hace entrega del paciente normalmente al servicio y habitacion destino
				 */
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";
			    if ($wurgencias == '1') // Evalua si el servicio es urgencias
			    {
			    	$onchange = "onchange='consultarHabitaciones();'";
			    }
				echo '<span class="subtituloPagina2">';
				echo "Admisi&oacute;n de paciente desde ".$ccoDeAdmision->codigo." - ".$ccoDeAdmision->nombre;
				echo "</span>";
				echo "<br>";
				echo "<br>";

					if(!$admiteCirugia){
					//Historia clínica
					echo "<tr><td class='fila1' width=200>Historia clínica</td>";
					echo "<td class='fila2' align='center' width=250>";
					echo "<input type='text' size='8' name='whistoria' id='whistoria' onKeyPress='return disableEnterKey(event)' $onchange  class='textoNormal'>";
					echo "</td>";
					echo "</tr>";

					//Servicio destino
					$centrosCostosHospitalarios = centrosCostosHospitalarios($conex, $wbasedato);
					echo "<tr><td class='fila1'>Servicio destino</td>";
					echo "<td class='fila2' align='center'>";


					echo "<select name=wselccodes id=wselccodes onchange='javascript:consultarHabitaciones();' class='textoNormal'>";
					echo "<option value=''>Seleccione</option>";
					foreach ($centrosCostosHospitalarios as $centroCostosHospitalario) {
						echo "<option value=".$centroCostosHospitalario->codigo.">".$centroCostosHospitalario->nombre."</option>";
					}
					echo "</select>";

					echo "</td></tr>";


					//Habitaciones disponibles
					echo "<tr><td class='fila1'>Habitaciones disponibles</td>";
					echo "<td class='fila2' align='center'>";
					echo "<span id='cntHabitacion'>";
					echo "<select name='wselhab' id='wselhab' class='textoNormal'>";
					echo "<option value=''>Seleccione</option>";
					echo "</select>";
					echo "</span>";					
					echo "</td>";
					echo "</tr>";
				} else {
					//Historia clínica
					echo "<tr><td class='fila1' width=200>Historia clínica</td>";
					echo "<td class='fila2' align='center' width=250>";
					echo "<input type='text' size='8' name='whistoria' id='whistoria' onKeyPress='return disableEnterKey(event)' class='textoNormal'>";
					echo "</td>";
					echo "</tr>";

				}

				echo "<tr><td colspan=2 align=center>";
				echo "<br><input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;|&nbsp;<input type=button value='Admitir paciente' onclick='javascript:admitirPaciente();'>&nbsp;|&nbsp;<input type=button value='Cancelar entrega' onclick='javascript:cancelarEntrega();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				echo "</td></tr>";
				echo "<input type=hidden name=wurgencias id=wurgencias value=".$wurgencias.">";
				echo "</table>";

				break; //Fin seleccion servicio que admite
			case 'b': //Realiza la validacion de la informacion de un paciente, mostrando un mensaje de la ubicación donde se encuentra
				$contenidoLog = $contenidoLog."---->Accion: Consulta estado de admision \r\n";
				$contenidoLog = $contenidoLog."Parametros::whistoria: '$whistoria' \r\n";

				$paciente = new pacienteDTO();
				$paciente = consultarInfoPacientePorHistoria($conex,$whistoria);

				$paciente->historiaClinica = $whistoria;

				$datospacienteMatrix = consultarPacienteMatrix($paciente);

				if(isset($paciente->ingresoHistoriaClinica) && isset($datospacienteMatrix->ingresoHistoriaClinica)){
					if($paciente->ingresoHistoriaClinica != $datospacienteMatrix->ingresoHistoriaClinica){
						$paciente->ingresoHistoriaClinica = $datospacienteMatrix->ingresoHistoriaClinica;
						$paciente->nombre1 = "";
					}
					$contenidoLog = $contenidoLog."Paciente Matrix: $datospacienteMatrix->historiaClinica-$datospacienteMatrix->ingresoHistoriaClinica \r\n";
				}

				//Datos del paciente
				if(isset($paciente->nombre1)){
					$pacienteIngresado = pacienteIngresado($conex, $paciente);
					$pacienteConMovimientoHospitalario = pacienteConMovimientoHospitalario($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,"-1");

					//Facturacion hace un registro en la 18 con proceso de traslado en off
					if($pacienteIngresado){
						$ubicacionPaciente = new ingresoPacientesDTO();
						$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);

						if(esUrgencias($conex,$ubicacionPaciente->servicioActual) && $ubicacionPaciente->enProcesoTraslado == 'off'){
							$pacienteIngresado = false;
						}
					}

					echo "<center><span class='subtituloPagina2'>El paciente ".$paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2.". Con historia clínica ".$paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica."</span></center>";
					$contenidoLog = $contenidoLog."El paciente ".$paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2.". Con historia clínica ".$paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica." \r\n";

					if($pacienteIngresado){
						$ubicacionPaciente = new ingresoPacientesDTO();
						$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
						$contenidoLog = $contenidoLog."Ubicacion actual del paciente: Ubisan: $ubicacionPaciente->servicioAnterior. Ubisac: $ubicacionPaciente->servicioActual. Ubihan:$ubicacionPaciente->habitacionAnterior. Ubihac:$ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado \r\n";

						$ccoActual = consultarCentroCosto($conex,$ubicacionPaciente->servicioActual,$wbasedato);
						$ccoAnterior = consultarCentroCosto($conex,$ubicacionPaciente->servicioAnterior,$wbasedato);

						//Se encuentra ya admitido
						if(isset($ccoActual->nombre)){
							echo "<center><span class='subtituloPagina2'>Se encuentra admitido en la institución en el servicio ".$ccoActual->codigo."-".$ccoActual->nombre."</center></span>";
							$contenidoLog = $contenidoLog."Se encuentra admitido en la institución en el servicio ".$ccoActual->codigo."-".$ccoActual->nombre." \r\n";
						}

						//Con servicio anterior
						if(isset($ccoAnterior->nombre)){
							echo "<center><span class='subtituloPagina2'>Procedente del servicio ".$ccoAnterior->codigo."-".$ccoAnterior->nombre."</center></span><br/>";
							$contenidoLog = $contenidoLog."Procedente del servicio ".$ccoAnterior->codigo."-".$ccoAnterior->nombre." \r\n";
						} else {
							$ccoAnterior->codigo = "";
						}

						/* La admision se puede cancelar si:
						 * 1. Si el servicio actual es uno de los de ingreso
						 * 2. Si el servicio anterior es uno de ingreso y adicionalmente el ultimo movimiento es de entrega.
						 * 3. Si no tiene servicio anterior y no esta en proceso de traslado.
						 */
						$centrosCostosIngreso = ccoIngreso();
						$puedeCancelarAdmision = false;

						if(esCirugia($conex,$ubicacionPaciente->servicioActual) && $ubicacionPaciente->enProcesoTraslado == 'off')
							modificarEstadoTrasladoPaciente($conex,$ubicacionPaciente); //Marcar proceso de traslado ubiptr='on'

						foreach ($centrosCostosIngreso as $centroCostosIngreso) {
							if($centroCostosIngreso->codigo == $ccoActual->codigo){
								$puedeCancelarAdmision = true;
								break;
							} else {
								if($centroCostosIngreso->codigo == $ccoAnterior->codigo && $pacienteConMovimientoHospitalario){
									$puedeCancelarAdmision = true;
									break;
								}
							}
						}
						
						//Si no tiene servicio anterior, no esta en proceso de traslado y el servicio actual es diferente de urgencias. Jonatan Lopez 10 Junio 2015
						if($ubicacionPaciente->servicioActual != $ccoUrgencias && $ubicacionPaciente->servicioAnterior == '' && $ubicacionPaciente->enProcesoTraslado == 'off'){							
							//Si tiene movimiento hospitalario, osea, una entrega o recibo activas no permite cancelar.
							if($pacienteConMovimientoHospitalario){
								$puedeCancelarAdmision = false;
							}else{							
								$puedeCancelarAdmision = true;
							}
						}

						//Acceso a cancelación de la admisión
						if($puedeCancelarAdmision){
							$contenidoLog = $contenidoLog."Puede cancelar admision \r\n";
							echo "<br/><center><input type=button value='Cancelar admision' onclick='javascript:cancelarAdmision();'></center>";
							echo "<input type=hidden name=whistoria value=".$paciente->historiaClinica.">";
							echo "<input type=hidden name=wingreso value=".$paciente->ingresoHistoriaClinica.">";
						}else{
							echo "<br><center><span class='subtituloPagina2'>No puede cancelar la admisión</span></center>";
							$contenidoLog = $contenidoLog."No puede cancelar la admision \r\n";
						}

					} else {
						echo "<center><span class='subtituloPagina2'>aun no ha sido admitido a la institucion en Matrix</center></span>";
						$contenidoLog = $contenidoLog."Aun no ha sido admitido a la institucion en Matrix \r\n";
					}
				} else {
					echo "<center><span class='subtituloPagina2'>El paciente con historia clínica ".$whistoria." no ha sido admitido a la institución en Matrix</center></span>";
					$contenidoLog = $contenidoLog."El paciente con historia clínica ".$whistoria." <br/>Aun no ha sido admitido a la institución en Matrix \r\n";
				}

				//Finalmente los botones de regresar y cerrar ventana
				echo "<br/>";
				echo "<br/>";
				echo "<div align=center>";
				echo "<input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				echo "</div>";

				break;
			case 'c': //Realiza la cancelación de la admisión de un paciente

				$contenidoLog = $contenidoLog."---->Accion: Cancelando admision... \r\n";
				$contenidoLog = $contenidoLog."PARAMETROS:::whistoria: '$whistoria'  \r\n";
				//Funcion que cancela la solicitud de la cama.
				cancelar_solicitud_cama($wnombre_origen, $wmotivo, $wdatos_pac, $wdestino, $wsolicito, $servicioOrigen, $whistoria, $whab_asignada, $wemp_pmla, $usuario->codigo);
				$paciente = new pacienteDTO();
				$paciente = consultarInfoPacientePorHistoria($conex,$whistoria);
				$datospacienteMatrix = consultarPacienteMatrix($paciente);

				if(isset($datospacienteMatrix->ingresoHistoriaClinica)){
					if($paciente->ingresoHistoriaClinica != $datospacienteMatrix->ingresoHistoriaClinica){
						$paciente->ingresoHistoriaClinica = $datospacienteMatrix->ingresoHistoriaClinica;
					}
				}

				//Datos del paciente
				if(isset($paciente->nombre1)){
					$ubicacionPaciente = new ingresoPacientesDTO();
					$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);

					$contenidoLog = $contenidoLog."Paciente: $ubicacionPaciente->historiaClinica-$ubicacionPaciente->ingresoHistoriaClinica:: Ubisan: $ubicacionPaciente->servicioAnterior Ubisac: $ubicacionPaciente->servicioActual Ubihan: $ubicacionPaciente->habitacionAnterior Ubihac: $ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado  \r\n";

					/* La cancelación consiste en llevar a cabo lo siguiente:
					 * 1. Desmarcar la historia de la habitacion tabla 20
					 * 2. Eliminar el registro del movimiento hospitalario tabla 17
					 * 3. Eliminar el ingreso en la tabla 18
					 */
					$exito = true;

//					mensajeEmergente($ubicacionPaciente->habitacionActual);
					//Paso 1.
					if(isset($ubicacionPaciente->habitacionActual)){
						//Marca habitación
						$habitacion = new habitacionDTO();

						$habitacion->codigo = $ubicacionPaciente->habitacionActual;
						$habitacion->historiaClinica = "";
						$habitacion->ingresoHistoriaClinica = "";
						$habitacion->disponible = "on";

						marcarHabitacion($conex,$habitacion);
						$contenidoLog = $contenidoLog."Desmarcando habitacion $habitacion->codigo \r\n";
					}

					//Paso 2.
					deshabilitarUltimoMovimientoHospitalario($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
					$contenidoLog = $contenidoLog."Inactivando movimiento hospitalario \r\n";

					//Paso 3.
					eliminarIngresoPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $ubicacionPaciente->servicioActual);
					$contenidoLog = $contenidoLog."Eliminando registro de ubicacion del paciente \r\n";

					echo "<center><span class='subtituloPagina2'>La cancelación de la admisión del paciente $paciente->nombre1 $paciente->nombre2 $paciente->apellido1 $paciente->apellido2 con historia clínica $paciente->historiaClinica-$paciente->ingresoHistoriaClinica</center></span>";
					$contenidoLog = $contenidoLog."La cancelación de la admisión del paciente $paciente->nombre1 $paciente->nombre2 $paciente->apellido1 $paciente->apellido2 con historia clínica $paciente->historiaClinica-$paciente->ingresoHistoriaClinica<br/>se realizó con éxito. \r\n";

					echo "<center><span class='subtituloPagina2'>se realizó con éxito.</center></span>";
				} else {
					echo "<center><span class='subtituloPagina2'>No se encontró paciente con la historia: ".$whistoria."</center></span>";
				}
				echo "<br/><center><input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'><center>";

				$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $datospacienteMatrix->historiaClinica, $datospacienteMatrix->ingresoHistoriaClinica);

				$contenidoLog = $contenidoLog."Ubicacion final del paciente cancelación: ";
				if(isset($ubicacionPaciente->servicioAnterior)){
					$contenidoLog = $contenidoLog."Ubisan: $ubicacionPaciente->servicioAnterior";
				}

				if(isset($ubicacionPaciente->servicioActual)){
					$contenidoLog = $contenidoLog.". Ubisac: $ubicacionPaciente->servicioActual";
				}

				if(isset($ubicacionPaciente->habitacionAnterior)){
					$contenidoLog = $contenidoLog.". Ubihan:$ubicacionPaciente->habitacionAnterior";
				}

				if(isset($ubicacionPaciente->habitacionActual)){
					$contenidoLog = $contenidoLog.". Ubihac:$ubicacionPaciente->habitacionActual";
				}

				if(isset($ubicacionPaciente->enProcesoTraslado)){
					$contenidoLog = $contenidoLog.". Ubiptr: $ubicacionPaciente->enProcesoTraslado";
				}
				$contenidoLog = $contenidoLog."\r\n";

				break;
			case 'd': //Admisión a la institución
				//Consulta de paciente en UNIX
				$contenidoLog = $contenidoLog."---->Accion: Realizando proceso de admision \r\n";
				//$contenidoLog = $contenidoLog."PARAMETROS:::whistoria: '$whistoria' servicioOrigen: '$servicioOrigen' servicioDestino:'$servicioDestino' habitacionDestino:'$habitacionDestino' \r\n";

				$ccoUrgencias = consultarCcoUrgencias();
				$ccoCirugia = consultarCcoCirugia();

				//Se valida que el centro de costos desde el que piden sea diferente de urgencias.
				if ($ccoUrgencias->codigo != $servicioOrigen)
				{
					if($ccoCirugia->codigo != $servicioOrigen)
					{
						$wnombre_origen = nombreccoorigen($servicioOrigen);
						//Funcion que hace la solicitud de cama.
						solicitud_cama($wnombre_origen, $wmotivo, $wdatos_pac, $wdestino, $wsolicito, $servicioOrigen, $whistoria, $habitacionDestino, $wemp_pmla, $usuario->codigo);
					}

				}
				else
				{
					$consultar_solic_cama = consultar_solicitud_cama( $whistoria, $wemp_pmla);
					if($consultar_solic_cama)
					{
						mensajeEmergente("La historia no tiene solicitud de cama.");
						funcionJavascript("regresar();");
						return false;
					}

				}

				$contenidoLog = $contenidoLog."PARAMETROS:::whistoria: '$whistoria' servicioOrigen: '$servicioOrigen'";
				if(isset($servicioDestino)){
					$contenidoLog = $contenidoLog."servicioDestino:'$servicioDestino'";
				}
				if(isset($servicioDestino)){
					$contenidoLog = $contenidoLog."habitacionDestino:'$habitacionDestino' \r\n";
				}

				$paciente = new pacienteDTO();

				$paciente->historiaClinica = $whistoria;

				$datospacienteMatrix = consultarPacienteMatrix($paciente);  //Paciente Unix

				if(isset($datospacienteMatrix->nombre1) && isset($datospacienteMatrix->ingresoHistoriaClinica)){

					/* El proceso de Admision en MATRIX es el siguiente:
					 *
					 * NO SE PERMITE historia cero:
					 * 1.Si no es historia cero:
					 * 1.1 Validar existencia de historia en UNIX //Esta validacion ya no se realiza 25 Nov 2013 Jonatan
					 * 1.2 Si el paciente no se encuentra registrado en UNIX //Esta validacion ya no se realiza 25 Nov 2013 Jonatan
					 * 1.2.1 Finaliza la aplicacion, no existe en UNIX //Esta validacion ya no se realiza 25 Nov 2013 Jonatan
					 * 1.3 Si esta registrado en UNIX // Verifica si esta registrado en Matrix, ya no verifica en UNIX. 25 Nov 2013 Jonatan
					 * 1.4 Se busca en la tabla unica de pacientes 36
					 * 1.4.1 Si NO esta registrado se ingresa la información en la tabla 36 // El paciente ya tuvo que estar en la esta tabla por medio del programa pacientes_unix_matrix.php 25 Nov 2013 Jonatan
					 
					 * ---------------------------------------------------------------------------------------------------------------
					 //Estos pasos ya no se realizan, ya que el programa pacientes_unix_matrix.php los realiza.
					 * 1.5 Se busca en la tabla de ingresos paciente 37 
					 * 1.5.1 Si esta registrado se actualiza el numero de ingreso del paciente
					 * 1.5.2 Si NO esta registrado se crea registro de ingreso
					 * 1.5.2.1 Se verifica si el numero y tipo de documento del paciente para los ingresos es el mismo
					 * 1.5.2.1.1 Si NO es el mismo documento y tipo, se actualiza en la tabla de ingresos 37
					 * 1.5.2.1.2 Si NO es el mismo documento y tipo, se eliminan los registros de paciente 36 del documento anterior
					 * 1.6 Si NO existe, se crea registro en la tabla 16
					 * 1.7.1 Si existe el registro se actualiza el nombre y nit del responsable					 
					 * ---------------------------------------------------------------------------------------------------------------
					 
					 * 2.Si el servicio que admite es URGENCIAS
					 * 2.1.El paciente se registra en la tabla 18 sin habitacion destino, servicio y habitacion anterior.
					 * 2.2.Se registra en la tabla 17 el movimiento hospitalario
					 *
					 * 3.Si el servicio que admite es CIRUGIA
					 * 3.1.El paciente se registra en la tabla 18 sin habitacion destino, servicio y habitación anterior.
					 *
					 * 4.Si el servicio que admite es ADMISIONES U OTROS.
					 * 4.1.El paciente se registra en la tabla 18 con habitacion destino, servicio y habitación anterior.
					 * 4.2.Se registra en la tabla 17 el movimiento hospitalario
					 * 4.3.Se marca la habitación en la tabla 20
					 *
					 * NOTA 1.  Para urgencias hay una lista de pacientes preentregados, cuando se confirma la lista, el sistema:
					 * 			a. Marca en la tabla 18 los servicios origen y destino, habitacion origen y destino conforme a la tabla 17.
					 * 			b. Marca la habitación con la historia.
					 *
					 * NOTA 2.  Para cirugia hay un listado de pacientes en recuperación, cuando se confirma la recuperación, el sistema:
					 * 			a. Realiza el proceso de admisión como si fuese de admisiones u otro.
					 */
					$pacienteMatrix = consultarInfoPacientePorHistoria($conex, $paciente->historiaClinica);
					
					$ingresoAnterior = "";

					if(!$pacienteMatrix){
						$pacienteMatrix = $datospacienteMatrix;
					} else {
						if(isset($pacienteMatrix->ingresoHistoriaClinica)){
							$ingresoAnterior = $pacienteMatrix->ingresoHistoriaClinica;
						} else {
							$pacienteMatrix->ingresoHistoriaClinica = $ingresoAnterior;
						}

						if($datospacienteMatrix){
							if($pacienteMatrix->ingresoHistoriaClinica != $datospacienteMatrix->ingresoHistoriaClinica){
								$pacienteMatrix->ingresoHistoriaClinica = $datospacienteMatrix->ingresoHistoriaClinica;
								$pacienteMatrix->nombre1 = "";
							}
						}
					}

					//Si ya se encuentra admitido va a consultar la información, la guia es la tabla 18, si esta ahi, YA SE CONSIDERA INGRESADO
//					mensajeEmergente("3");
					if(!isset($pacienteMatrix->historiaClinica) || empty($pacienteMatrix->historiaClinica)){
						$pacienteMatrix->historiaClinica = $datospacienteMatrix->historiaClinica;
					}

					$pacienteIngresado = pacienteIngresado($conex,$pacienteMatrix);

					//Facturacion hace un registro en la 18 con proceso de traslado en off
					if($pacienteIngresado){
						$ubicacionPaciente = new ingresoPacientesDTO();
						$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $datospacienteMatrix->historiaClinica, $datospacienteMatrix->ingresoHistoriaClinica);
						$contenidoLog = $contenidoLog."Ubicacion actual del paciente: Ubisan: $ubicacionPaciente->servicioAnterior. Ubisac: $ubicacionPaciente->servicioActual. Ubihan:$ubicacionPaciente->habitacionAnterior. Ubihac:$ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado \r\n";

						if(esUrgencias($conex,$ubicacionPaciente->servicioActual) && $ubicacionPaciente->enProcesoTraslado == 'off'){
							$pacienteIngresado = false;
						}
					}



					if(!isset($pacienteMatrix->ingresoHistoriaClinica) or !$pacienteIngresado or !existeEnTablaMovimientos($conex,$pacienteMatrix)) {
//						echo "No hay ingreso historia o no esta ingresado";
						$pacienteEnTablaUnica = false;
						$pacienteEnTablaIngresos = false;
						$pacienteConResponsablePaciente = false;
						$mismoDocumentoIdentidad = false;

						$caso = "";
						if(esCirugia($conex, $servicioOrigen)){
							$caso = "1";
						} elseif (esUrgencias($conex, $servicioOrigen)){
							$caso = "2";
						}


						//Proceso de ingreso en tabla unica y de ingresos pacientes root_000037, root_000036
						$pacienteEnTablaIngresos = existeEnTablaIngresos($conex,$datospacienteMatrix, $wemp_pmla);
						if(isset($pacienteMatrix->documentoIdentidad)){
							$pacienteEnTablaUnica = existeEnTablaUnicaPacientes($conex,$pacienteMatrix);
							$pacienteConResponsablePaciente = existeEnTablaResponsables($conex,$datospacienteMatrix, $wemp_pmla);

							//DEBUG
							$estadoIngreso = $pacienteEnTablaIngresos ? "tiene registro en 37" : "no tiene registro en 37";
							$estadoIngreso = $pacienteEnTablaUnica ? "tiene registro en 36" : "no tiene registro en 36";
							$estadoIngreso = $pacienteConResponsablePaciente ? "tiene responsable" : "no tiene responsable";
							$contenidoLog = $contenidoLog."$estadoIngreso,$estadoIngreso,$estadoIngreso \r\n";

							if($pacienteMatrix->documentoIdentidad == $datospacienteMatrix->documentoIdentidad && $pacienteMatrix->tipoDocumentoIdentidad == $datospacienteMatrix->tipoDocumentoIdentidad){
								if(!$pacienteEnTablaUnica && !$pacienteEnTablaIngresos){
									$mismoDocumentoIdentidad = false;
								} else {
//									mensajeEmergente("Meesmo documento");
									$mismoDocumentoIdentidad = true;
								}
							}

						} else {
							$pacienteMatrix->historiaClinica = $whistoria;
							$pacienteMatrix->ingresoHistoriaClinica = $datospacienteMatrix->ingresoHistoriaClinica;
							$pacienteMatrix->documentoIdentidad = "";
							$pacienteMatrix->tipoDocumentoIdentidad = "";
						}

						if(!$pacienteEnTablaUnica){
							$contenidoLog = $contenidoLog."Insertando en tabla unica \r\n";
							insertarPacienteTablaUnica($conex, $datospacienteMatrix);
						}

						//Tabla 37
						if(!$pacienteEnTablaIngresos){
							$contenidoLog = $contenidoLog."Insertando ingresos \r\n";
							insertarIngresoPaciente($conex, $datospacienteMatrix, $wemp_pmla);
						} else {
							$contenidoLog = $contenidoLog."Actualizando ingresos \r\n";
							$pacienteMatrix->ingresoHistoriaClinica = $ingresoAnterior;
							actualizarIngresoPaciente($conex, $pacienteMatrix, $datospacienteMatrix, $wemp_pmla);
							$pacienteMatrix->ingresoHistoriaClinica = $datospacienteMatrix->ingresoHistoriaClinica;
						}

						//Tabla 16
						if(!$pacienteConResponsablePaciente){
							$contenidoLog = $contenidoLog."Insertado responsable \r\n";
							insertarResponsablePaciente($conex, $datospacienteMatrix, $wemp_pmla);
						} else {
							$contenidoLog = $contenidoLog."Actualizado responsable \r\n";
							actualizarResponsablePaciente($conex, $pacienteMatrix, $datospacienteMatrix);
						}

						if(!$mismoDocumentoIdentidad){
							$contenidoLog = $contenidoLog."Actualiza documento identidad \r\n";
//							mensajeEmergente("Actualizando doc ingresos");
							actualizarDocumentoPacienteTablaIngresos($conex,$pacienteMatrix,$datospacienteMatrix);

//							mensajeEmergente("Actualizando doc unica");
							actualizarDocumentoPacienteTablaUnica($conex,$pacienteMatrix,$datospacienteMatrix);
						}

						//Proceso de movimiento hospitalario
						$ingresoPaciente = new ingresoPacientesDTO();

						$ingresoPaciente->historiaClinica = $datospacienteMatrix->historiaClinica;
						$ingresoPaciente->ingresoHistoriaClinica = $datospacienteMatrix->ingresoHistoriaClinica;
						$ingresoPaciente->servicioActual = $servicioOrigen;
						$ingresoPaciente->habitacionActual = "";

						$ingresoPaciente->fechaAltaProceso = "0000-00-00";
						$ingresoPaciente->horaAltaProceso = "00:00:00";
						$ingresoPaciente->fechaAltaDefinitiva = "0000-00-00";
						$ingresoPaciente->horaAltaDefinitiva = "00:00:00";

						$ingresoPaciente->enProcesoTraslado = "on";
						$ingresoPaciente->altaDefinitiva = "off";
						$ingresoPaciente->altaEnProceso = "off";

						$ingresoPaciente->usuario = "A-".$wbasedato;
							
						/* Casos:
						 * 1. 		Cirugia
						 * 2. 		Urgencias
						 * default. Admisiones y otros
						 */
						switch ($caso){
							case '1':
								//Si hay un registro en urgencias
								$pacienteIngresado = pacienteIngresado($conex,$pacienteMatrix);

								//Grabar ingreso paciente
								$ingresoPaciente->servicioAnterior = "";
								$ingresoPaciente->habitacionAnterior = "";

								if(!$pacienteIngresado){
									$contenidoLog = $contenidoLog."Grabando ubicacion cirugia paciente: Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado \r\n";
									grabarIngresoPaciente($conex,$ingresoPaciente);
								} else {
									$contenidoLog = $contenidoLog."Actualizando ubicacion cirugia paciente Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado \r\n";
									//Actualizo estado de la ubicacion del paciente
									actualizarUbicacionPaciente($conex, $ingresoPaciente, $ingresoPaciente);

									//Marcar proceso de traslado
									modificarEstadoTrasladoPaciente($conex,$ingresoPaciente);
								}

								break;
							case '2':
								//Si hay un registro en urgencias
								$pacienteIngresado = pacienteIngresado($conex,$pacienteMatrix);

								//Grabar ingreso paciente
								$ingresoPaciente->servicioAnterior = "";
								$ingresoPaciente->habitacionAnterior = "";
								
								if(!$pacienteIngresado){
									$contenidoLog = $contenidoLog."Grabando ubicacion urgencias paciente Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado\r\n";
									grabarIngresoPaciente($conex,$ingresoPaciente);
								} else {
								
							
								
									$contenidoLog = $contenidoLog."Actualizando ubicacion urgencias paciente Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado \r\n";
									actualizarUbicacionPaciente($conex, $ingresoPaciente, $ingresoPaciente);

									modificarEstadoTrasladoPaciente($conex,$ingresoPaciente);
								}


								//Movimiento hospitalario
								$ingresoPaciente->servicioAnterior = $servicioOrigen;
								$ingresoPaciente->habitacionAnterior = "";
								$ingresoPaciente->servicioActual = $servicioDestino;
								$ingresoPaciente->habitacionActual = $habitacionDestino;
								
								grabarMovimientoHospitalario($conex,$ingresoPaciente,$usuario);
								$contenidoLog = $contenidoLog."Grabando movimiento hospitalario Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado\r\n";
								break;
							default :
								//Si hay un registro en urgencias
								$pacienteIngresado = pacienteIngresado($conex,$pacienteMatrix);

								if(!$pacienteIngresado){
									//Grabar ingreso paciente
									$ingresoPaciente->servicioAnterior = $servicioOrigen;
									$ingresoPaciente->habitacionAnterior = "";
									$ingresoPaciente->servicioActual = $servicioDestino;
									$ingresoPaciente->habitacionActual = $habitacionDestino;

									$contenidoLog = $contenidoLog."Grabando ubicacion admisiones paciente Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado\r\n";
									grabarIngresoPaciente($conex,$ingresoPaciente);
								} else {
									//Grabar ingreso paciente
									$ingresoPaciente->servicioAnterior = $ccoUrgencias->codigo;
									$ingresoPaciente->habitacionAnterior = "";
									$ingresoPaciente->servicioActual = $servicioDestino;
									$ingresoPaciente->habitacionActual = $habitacionDestino;

									actualizarUbicacionPaciente($conex, $ingresoPaciente, $ingresoPaciente);

									modificarEstadoTrasladoPaciente($conex,$ingresoPaciente);
									$contenidoLog = $contenidoLog."Actualizando ubicacion admisiones paciente Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado\r\n";
								}

								//Movimiento hospitalario
								$ingresoPaciente->servicioAnterior = $servicioOrigen;
								$ingresoPaciente->habitacionAnterior = "";
								$ingresoPaciente->servicioActual = $servicioDestino;
								$ingresoPaciente->habitacionActual = $habitacionDestino;

								$contenidoLog = $contenidoLog."Grabando movimiento hospitalario admisiones paciente Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado\r\n";
								grabarMovimientoHospitalario($conex,$ingresoPaciente,$usuario);

								//Marca habitación
								$habitacion = new habitacionDTO();

								$habitacion->codigo = $ingresoPaciente->habitacionActual;
								$habitacion->servicio = $ingresoPaciente->servicioActual;
								$habitacion->historiaClinica = $datospacienteMatrix->historiaClinica;
								$habitacion->ingresoHistoriaClinica = $datospacienteMatrix->ingresoHistoriaClinica;
								$habitacion->disponible = "off";

								$contenidoLog = $contenidoLog."Marcando habitacion admisiones paciente Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado\r\n";
								marcarHabitacion($conex,$habitacion);
								break;
						}

						devolverAltaPaciente($conex, $ingresoPaciente);


						echo "<br/><center><span class='subtituloPagina2'>El paciente $datospacienteMatrix->nombre1 $datospacienteMatrix->nombre2 $datospacienteMatrix->apellido1 $datospacienteMatrix->apellido2 con historia clínica $datospacienteMatrix->historiaClinica-$datospacienteMatrix->ingresoHistoriaClinica<br/>ha sido admitido a la institución con éxito.</span></center><br/>";
						$contenidoLog = $contenidoLog."El paciente $datospacienteMatrix->nombre1 $datospacienteMatrix->nombre2 $datospacienteMatrix->apellido1 $datospacienteMatrix->apellido2 con historia clínica $datospacienteMatrix->historiaClinica-$datospacienteMatrix->ingresoHistoriaClinica<br/>ha sido admitido a la institución con éxito.<br/><br/> \r\n";
					} else {
						//Si ya hay registro en la tabla 18 mostrar la pantalla de consulta Accion b
						echo "<input type=hidden name=whistoria value=".$pacienteMatrix->historiaClinica.">";
						funcionJavascript("consultarInfoAdmision();");
					}

					$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $datospacienteMatrix->historiaClinica, $datospacienteMatrix->ingresoHistoriaClinica);
					$contenidoLog = $contenidoLog."Ubicacion final del paciente admision: Ubisan: $ubicacionPaciente->servicioAnterior. Ubisac: $ubicacionPaciente->servicioActual. Ubihan:$ubicacionPaciente->habitacionAnterior. Ubihac:$ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado \r\n";
				} else {
					$contenidoLog = $contenidoLog."La historia $whistoria no está activa en Matrix. No se puede realizar la admisión.<br/> \r\n";
					echo "<br/><center><span class='subtituloPagina2'>La historia $whistoria no está activa en Matrix. No se puede realizar la admisión.</span></center><br>";
				}
				echo "<br/><center><input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></center>";

				break;
			case 'e':  //Exclusivo para seleccion de destino para cirugia
				echo "<input type=hidden name=wselcco value=".$wselcco.">";

				//Centro de costos que admite, cco urgencias y cco cirugia
				$ccoDeAdmision = consultarCentroCosto($conex, $wselcco, $wbasedato);
                $wurgencias = esUrgencias($conex,$wselcco);
				//Se presentan los servicios y las habitaciones disponibles
				echo "<table align='center' border=0>";

				echo '<span class="subtituloPagina2">';
				echo "Admisi&oacute;n de paciente desde ".$ccoDeAdmision->codigo." - ".$ccoDeAdmision->nombre;
				echo "</span>";
				echo "<br>";
				echo "<br>";

				//Centro de costos que admite, cco urgencias y cco cirugia
				$ccoDeAdmision = consultarCentroCosto($conex, $wselcco, $wbasedato);

				//Historia clínica
				echo "<tr><td class='fila1' width=200>Historia clínica</td>";
				echo "<td class='fila2' align='center' width=250>";
				echo "<input type='text' size='8' name='whistoria' id='whistoria' value='".$historia."' readonly>";
				echo "</td>";
				echo "</tr>";

				//Servicio destino
				$centrosCostosHospitalarios = centrosCostosHospitalarios($conex, $wbasedato);
				echo "<tr><td class='fila1'>Servicio destino</td>";
				echo "<td class='fila2' align='center'>";

				echo "<select name=wselccodes id=wselccodes onchange='javascript:consultarHabitaciones();' class='textoNormal'>";
				echo "<option value=''>Seleccione</option>";
				foreach ($centrosCostosHospitalarios as $centroCostosHospitalario) {
					echo "<option value=".$centroCostosHospitalario->codigo.">".$centroCostosHospitalario->nombre."</option>";
				}
				echo "</select>";

				echo "</td></tr>";

				//Habitaciones disponibles
				echo "<tr><td class='fila1'>Habitaciones disponibles</td>";
				echo "<td class='fila2' align='center'>";
				echo "<span id='cntHabitacion'>";
				echo "<select name='wselhab' id='wselhab' class='textoNormal'>";
				echo "<option value=''>Seleccione</option>";
				echo "</select>";
				echo "</span>";

				echo "</td>";
				echo "</tr>";

				echo "</table>";

				echo "<center>";
				echo "<input type=button value='Admitir paciente' onclick='javascript:admitirPacienteCirugia();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				echo "</center>";
                echo "<input type=hidden name=wurgencias id=wurgencias value=".$wurgencias.">";

				break; //Fin seleccion servicio que admite
				break;
			case 'f': //Seleccion servicio y habitacion destino cirugia

				$contenidoLog = $contenidoLog."---->Accion: Seleccionando servicio destino cirugia \r\n";
				$contenidoLog = $contenidoLog."PARAMETROS:::whistoria:'$whistoria' servicioOrigen:'$servicioOrigen' servicioDestino:'$servicioDestino' habitacionDestino:'$habitacionDestino' \r\n";

				//Consulta de paciente en UNIX
				$paciente = new pacienteDTO();

				$paciente->historiaClinica = $whistoria;
				$datospacienteMatrix = consultarPacienteMatrix($paciente);  //Paciente Unix

				if(isset($datospacienteMatrix->nombre1)){

					$consultar_solic_cama = consultar_solicitud_cama( $whistoria, $wemp_pmla);
					if($consultar_solic_cama)
					{
						mensajeEmergente("La historia no tiene solicitud de cama.");
						funcionJavascript("irListaPreentrega();");
						return false;
					}

					if(!pacienteConMovimientoHospitalario($datospacienteMatrix->historiaClinica, $datospacienteMatrix->ingresoHistoriaClinica, $servicioOrigen)){

						//whistoria='+historia+'&servicioOrigen='+servicioOrigen+'&servicioDestino='+servicioDestino+'&habitacionDestino

						//Proceso de movimiento hospitalario
						$ingresoPaciente = new ingresoPacientesDTO();

						$ingresoPaciente->historiaClinica = $datospacienteMatrix->historiaClinica;
						$ingresoPaciente->ingresoHistoriaClinica = $datospacienteMatrix->ingresoHistoriaClinica;
						$ingresoPaciente->servicioActual = $servicioOrigen;
						$ingresoPaciente->habitacionActual = "";

						$ingresoPaciente->fechaAltaProceso = "0000-00-00";
						$ingresoPaciente->horaAltaProceso = "00:00:00";
						$ingresoPaciente->fechaAltaDefinitiva = "0000-00-00";
						$ingresoPaciente->horaAltaDefinitiva = "00:00:00";

						$ingresoPaciente->enProcesoTraslado = "on";
						$ingresoPaciente->altaDefinitiva = "off";
						$ingresoPaciente->altaEnProceso = "off";

						$ingresoPaciente->usuario = "A-".$wbasedato;

						$ingresoNuevo = $ingresoPaciente;

						//Grabar ingreso paciente
						$ingresoNuevo->servicioAnterior = $servicioOrigen;
						$ingresoNuevo->habitacionAnterior = "";
						$ingresoNuevo->servicioActual = $servicioDestino;
						$ingresoNuevo->habitacionActual = $habitacionDestino;

						$contenidoLog = $contenidoLog."Actualizando ingreso paciente Ubisan: $ingresoNuevo->servicioAnterior. Ubisac: $ingresoNuevo->servicioActual. Ubihan:$ingresoNuevo->habitacionAnterior. Ubihac:$ingresoNuevo->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado\r\n";
						actualizarUbicacionPaciente($conex, $ingresoPaciente,$ingresoNuevo);

						//Movimiento hospitalario
						$ingresoPaciente->servicioAnterior = $servicioOrigen;
						$ingresoPaciente->habitacionAnterior = "";
						$ingresoPaciente->servicioActual = $servicioDestino;
						$ingresoPaciente->habitacionActual = $habitacionDestino;

						$contenidoLog = $contenidoLog."Generando movimiento hospitalario Ubisan: $ingresoPaciente->servicioAnterior. Ubisac: $ingresoPaciente->servicioActual. Ubihan:$ingresoPaciente->habitacionAnterior. Ubihac:$ingresoPaciente->habitacionActual Ubiptr: $ingresoPaciente->enProcesoTraslado\r\n";
						grabarMovimientoHospitalario($conex,$ingresoPaciente,$usuario);

						//Marca habitación
						$habitacion = new habitacionDTO();

						$habitacion->codigo = $habitacionDestino;
						$habitacion->servicio = $servicioDestino;
						$habitacion->historiaClinica = $ingresoPaciente->historiaClinica;
						$habitacion->ingresoHistoriaClinica = $ingresoPaciente->ingresoHistoriaClinica;
						$habitacion->disponible = "off";

						$contenidoLog = $contenidoLog."Marcando habitacion \r\n";
						marcarHabitacion($conex,$habitacion);

						//Debe ir a la pantalla de pacientes en cirugia!
						mensajeEmergente("El paciente $datospacienteMatrix->nombre1 $datospacienteMatrix->nombre2 $datospacienteMatrix->apellido1 $datospacienteMatrix->apellido2 con historia clínica $datospacienteMatrix->historiaClinica-$datospacienteMatrix->ingresoHistoriaClinica. Se ha entregado desde cirugía con éxito.");
						$contenidoLog = $contenidoLog."El paciente $datospacienteMatrix->nombre1 $datospacienteMatrix->nombre2 $datospacienteMatrix->apellido1 $datospacienteMatrix->apellido2 con historia clínica $datospacienteMatrix->historiaClinica-$datospacienteMatrix->ingresoHistoriaClinica. Se ha entregado desde cirugía con éxito. \r\n";
						funcionJavascript("irListaPreentrega();");
					}else {
						echo "<br/>El paciente $datospacienteMatrix->nombre1 $datospacienteMatrix->nombre2 $datospacienteMatrix->apellido1 $datospacienteMatrix->apellido2 con historia clínica $datospacienteMatrix->historiaClinica-$datospacienteMatrix->ingresoHistoriaClinica<br/> ya había sido entregado desde cirugía previamente.<br/>";
					}
				} else {
					echo "<br/>La historia $whistoria no está activa en Unix. No se puede realizar la admisión.<br/>";
				}
				echo "<br/><input type=button  value='Regresar' onclick='javascript:history.go(-2);'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";

				if(isset($datospacienteMatrix->historiaClinica) && isset($datospacienteMatrix->ingresoHistoriaClinica))
				{
					$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $datospacienteMatrix->historiaClinica, $datospacienteMatrix->ingresoHistoriaClinica);
					$contenidoLog = $contenidoLog."Ubicacion final del paciente cirugia: Ubisan: $ubicacionPaciente->servicioAnterior. Ubisac: $ubicacionPaciente->servicioActual. Ubihan:$ubicacionPaciente->habitacionAnterior. Ubihac:$ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado \r\n";
				}

				break;
			default:  //Muestra la pantalla inicial
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";

				//Por servicio de ingreso
				$centrosCostosIngreso = ccoIngreso();
				echo "<tr><td class='fila1' width='150'>Servicio de ingreso</td>";
				echo "<td class='fila2' align='center' width='200'>";

				echo "<select name=wselcco id=wselcco onchange='javascript:seleccionServicioHabitacion();' class='textoNormal'>";
				echo "<option value='-'>Seleccione</option>";
				foreach ($centrosCostosIngreso as $centroCostosIngreso) {
					echo "<option value=".$centroCostosIngreso->codigo.">".$centroCostosIngreso->nombre."</option>";
				}
				echo "</select>";
				echo "</td>";
				echo "</tr>";

				echo "<tr><td align=center colspan=4><br><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
				echo "</table>";

//				$pacienteConsulta = new pacienteDTO();
//				$pacienteConsulta->historiaClinica = "387808";
//
//				$prueba = consultarPacienteMatrix($conexUnix, $pacienteConsulta);
//
//				var_dump($prueba);

				break; //Fin pantalla inicial
		}//Fin case

		//Msanchez:**************GRABA LOG**************
		if($debug){
			if($archivo){
				// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
				if (is_writable($nombreArchivo)) {
					// Escribir $contenido a nuestro arcivo abierto.
					fwrite($archivo, $contenidoLog);
					fclose($archivo);
				}
			}
		}
	    //Msanchez::***************FIN GRABA LOG*************
	//Se vuelve comentario ya que no tiene que establecer conexion con unix  21 Noviembre de 2013 Jonatan
	//} else {
		//No hay conexion con unix
	//}

	echo "</div>"; //Cierre div id=page
}

//Liberacion de conexion Matrix
liberarConexionBD($conex);

//Liberacion de conexion Unix
//Se vuelve comentario ya que no tiene que establecer conexion con unix  21 Noviembre Jonatan
//liberarConexionOdbc($conexUnix);
?>
</body>
</html>
<?php
}
else
{
	

	include_once("root/comun.php");
	
function verificar_solicitud_cama_activa($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria){
	
	global $conex;	
	

	
	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'id'=>'');
	
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
		
	$q2 = " SELECT Motivo, Observacion, Destino, Solicito, Camillero, A.Hora_data, Hora_respuesta, Habitacion, A.Id, observ_central, A.Fecha_data ,Origen"
		."    FROM ".$wcencam."_000003 A"
		."   WHERE Fecha_cumplimiento = '0000-00-00' "		
		."     AND Hora_Cumplimiento = '00:00:00' "
		."     AND Anulada           = 'No' "
		."     AND Historia          = '".$whistoria."'"
		."     AND Central           = '".$wcentral_camas."'";
	$res2 = mysql_query($q2,$conex);
	$numsolicitudes = mysql_num_rows($res2);
	$row3 = mysql_fetch_array($res2);
	
	if ($numsolicitudes > 0)
	{
		
		$datamensaje['mensaje'] = "Hay una solicitud de cama activa para este paciente hecha en ".$row3['Origen'].".";
		$datamensaje['error'] = 1;
				
	}
	
	echo json_encode($datamensaje);
	
	return;
	
	
}
	
	
	//Valida si el centro de costos del paciente es de ingreso, en caso de no serlo, no permitirá la admision.
	function verificar_cco_paciente($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria)
	{

		global $conex;
		

		
	   //Consulta el cco del paciente en el ultimo ingreso.
		$q = " SELECT ubisac "
	        ."   FROM ".$wbasedato."_000018 "
	        ."  WHERE ubihis = '".$whistoria."'"
			."    AND ubiald = 'off'
			ORDER BY CONCAT(Fecha_data,' ', Hora_data) DESC";
	    $res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
	    $row = mysql_fetch_array($res);
	    $wcco_paciente = $row['ubisac'];
		
		//Consulta si los datos del cco, si es de ingreso y su nombre.
	    $q2 =        "  SELECT cconom, ccoing "
	                ."    FROM ".$wbasedato."_000011 "
	                ."   WHERE ccocod      = '".$wcco_paciente."'";
	    $res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q2 . "-" . mysql_error());
	    $row_hab = mysql_fetch_array($res2);
	
		$wcconom = $row_hab['cconom'];
	    $wccoing = $row_hab['ccoing'];
		

	    return trim($wccoing."-".$wcconom);


	}
	
	//Verifica si el paciente tiene habitacion asignada.	
	function habasignada($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria)
	{

		global $conex;
		

		$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');
		$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

		$q = " SELECT hab_asignada, historia "
	        ."   FROM ".$wcencam."_000003 "
	        ."  WHERE historia = '".$whistoria."'"
			."    AND Fecha_llegada = '0000-00-00'"
			."	  AND Hora_llegada = '00:00:00'"
			."    AND Fecha_cumplimiento = '0000-00-00'"
			."    AND Hora_cumplimiento = '00:00:00'"
			."    AND Anulada = 'No'"
			."    AND Central = '".$wcentral_camas."'";
	    $res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
	    $row = mysql_fetch_array($res);
	    $num = mysql_num_rows($res);

	    $whab_asignada = $row['hab_asignada'];

	    $q2 =        "  SELECT habcod, habcco "
	                ."    FROM ".$wbasedato."_000020"
	                ."   WHERE Habcod      = '".$whab_asignada."'";
	    $res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q2 . "-" . mysql_error());
	    $row_hab = mysql_fetch_array($res2);

	    $whabitacion = $row_hab['habcod'];
	    $whabcco = $row_hab['habcco'];

	    return trim($whabitacion."-".$whabcco);



	}

function validar_unidad($art){

	global $wbasedato;
	global $conex;
	
	$dividir = 1;
	
	//Si la unidad de la tabla 26 es igual a la unidad de la tabla 115 entonces tomara la concentracion de la tabla 115.	
	$q = "  SELECT Relcon
			  FROM ".$wbasedato."_000026, ".$wbasedato."_000115
			 WHERE Relart = Artcod
			   AND Reluni = Artuni
			   AND Relart = '".$art."'" ;
	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	
	if($num > 0){
	
		$dividir = $row['Relcon'];
		
	}else{
		
		//Revisar si la unidad de presentacion es diferente de la unidad de fracccion, ademas se revisa si la fraccion es igual a 1,
		//en este caso se tomara la concentracion (Ej: un PUFF es igual a una DO)
		$q = "  SELECT Relcon
			      FROM ".$wbasedato."_000059, ".$wbasedato."_000115
				 WHERE Relart = Defart
			       AND Relpre != Deffru
			       AND Relart = '".$art."'
			       AND Deffra = '1'" ;
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$row = mysql_fetch_array($res);
		
		if($num > 0){	
		
			$dividir = $row['Relcon'];
		
		}	
	}
	
	return $dividir;

}
	
function buscar_saldo_pendiente($wtip, $whis, $wing)
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	global $wartnom;
	global $wartuni;
	global $wunides;
	
	
	$control_saldo = "";

	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos

	 $q = " SELECT spaart, spauen-spausa, id, spacco "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis                            = '" . $whis . "'"
		. "    AND spaing                            = '" . $wing . "'"
		. "    AND ROUND((spauen-spausa),3) > 0 "
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num >= 1)
	{
				
		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);	
			
			$validar_unidad = validar_unidad($row[0]);
			
			//Validar si el saldo actual tiene unidades completas, en ese caso no permite la entrega del paciente.
			if(floor($row[1]/$validar_unidad) >= 1){				
								
				$control_saldo = "total";
			
			}
			elseif( ($row[1]/$validar_unidad - floor($row[1]/$validar_unidad)) > 0){
								
				$control_saldo = "descarte";
			}
		}		
	}
	
	return $control_saldo;
}
	
	
	function validaEstado($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria)
	{

        global $conex;

		


        //Verifica el estado actual del paciente.
		$qalp = "	SELECT ubialp, ubiald, ubisac, ubiing
					  FROM ".$wbasedato."_000018
					 WHERE Ubihis = '".$whistoria."'
					 ORDER BY Ubiing+0 DESC";
		$resalp = mysql_query($qalp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalp . " - " . mysql_error());
		$rowalp = mysql_fetch_array($resalp);
		$ingreso = $rowalp['ubiing'];
		
		$validar_saldo = buscar_saldo_pendiente('NoApl', $whistoria, $ingreso);
		
        //Verifica si esta en consulta
		$qcur = "	SELECT Mtrcur
					FROM ".$wbasedatohce."_000022
					WHERE Mtrhis = '".$whistoria."'
					ORDER BY Mtring DESC";
		$rescur = mysql_query($qcur, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcur . " - " . mysql_error());
		$rowcur = mysql_fetch_array($rescur);

        //Nombre del centro de costos
        $qser = "	SELECT Cconom
					FROM ".$wbasedato."_000011
					WHERE ccocod = '".$rowalp['ubisac']."'";
		$resser = mysql_query($qser, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qser . " - " . mysql_error());
		$rowser = mysql_fetch_array($resser);

		if($rowalp['ubialp']=="on")
			return "Alta en proceso-".$rowser['Cconom']."";
		elseif($rowcur['Mtrcur']=="on")
			return "En consulta";
        elseif($rowalp['ubiald']=="on")
			return "Alta definitiva-".$rowser['Cconom']."";
		elseif($validar_saldo != ''){
			
			if($validar_saldo == 'total'){
				
				return "total-Tiene saldo pendiente por aplicar para este paciente.";
			}else{
				
				return "descarte-Tiene saldo pendiente por descartar para este paciente.";
			}
			
		}
		else
			return "admision";

	}


	if(isset($consultaAjax) && $consultaAjax=="valida")
	{
		echo validaEstado($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria);
	}

	if(isset($consultaAjax) && $consultaAjax=="habasignada")
	{
		echo habasignada($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria);
	}
	
	if(isset($consultaAjax) && $consultaAjax=="verificar_cco_paciente")
	{
		echo verificar_cco_paciente($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria);
	}
	
	
	if(isset($consultaAjax) && $consultaAjax=="verificar_solicitud_cama_activa")
	{
		echo verificar_solicitud_cama_activa($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria);
	}
}
?>
