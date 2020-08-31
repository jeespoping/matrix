<html>

<head>
  <title>MATRIX - [MODIFICACI&Oacute;N TRASLADOS]</title>
  <meta http-equiv="Content-Type" content="text/html/; charset=UTF-8" />
</head>

<body>

<script type="text/javascript">

function irCco(){ document.forms.forma.wccoIngreso.focus(); }
function enter() { document.forms.forma.submit(); }
function cerrarVentana() {	window.close(); }

function inicio(){	
	document.forms.forma.reset();
	document.forms.forma.wccoIngreso.value = '';
	document.forms.forma.action = 'Modificacion_traslados.php?wemp_pmla=01';
	document.forms.forma.submit();
}

function enviar(){	
	document.forms.forma.action = 'Modificacion_traslados.php?wproceso=' + document.forms.forma.wproceso.value;
	document.forms.forma.submit();
}

function agregarArticulo(){ 
	var codArt = document.forms.forma.wcodart.value;
	var numart = document.forms.forma.wnumart.value;
	
	if(codArt != '' && numart != ''){
		if(!isNaN(numart) && numart > 0){
			document.forms.forma.submit();
		} else {
			alert("La cantidad del artículo debe ser un valor numérico y mayor que cero.");
		}	
	} else {
		alert("Debe ingresar el código del artículo y la cantidad");
	}
}

function quitarArticulos(){
	document.forms.forma.artquitar.value = '*';	
	document.forms.forma.submit();
}

function cargarArts(){	
	//Se necesita recargar la pagina 2 veces, una para grabar y otra para visualizar los cambios del detalle

	//Parametro para ingresar a la carga de articulos
	document.forms.forma.cargarArticulos.value = '*';
	
	//Carga de articulos	
	document.forms.forma.submit();
	
	//Elimina la lista temporal de nuevos articulos y no ingresa a cargar
	document.forms.forma.cargarArticulos.value = '';
	document.forms.forma.artquitar.value = '*';
	
	//Refresca la pantalla para visualizar los nuevos articulos	
//	enviar();
}

function consultarHistoria() {
	if(document.forms.forma.whistoria && document.forms.forma.whistoria.value != ''){
		document.forms.forma.action = 'Modificacion_traslados.php?wproceso=' + document.forms.forma.wproceso.value + '&whistoria=' + document.forms.forma.whistoria.value;
		document.forms.forma.submit();
	} else {
		alert('Debe ingresar el número de la historia');
	}
}

function procesoAnulacion() {
	if(confirm("Va a realizar la anulacion del traslado. Desea continuar?")){
		document.forms.forma.action = 'Modificacion_traslados.php?wproceso=' + document.forms.forma.wproceso.value + '&whistoria=' + document.forms.forma.whistoria.value + '&waccion=A';
		document.forms.forma.submit();
	}
}

function procesoAnulacionRecibo(){
	if(confirm("Va a realizar la anulación del RECIBO UNICAMENTE.  ¿Desea continuar?")){
		document.forms.forma.action = 'Modificacion_traslados.php?wproceso=' + document.forms.forma.wproceso.value + '&whistoria=' + document.forms.forma.whistoria.value + '&waccion=R';
		document.forms.forma.submit();
	}
}

function procesoDestino() {
	if(document.forms.forma.wccocod && document.forms.forma.wccocod.value != ''){
		if(document.forms.forma.whabcod && document.forms.forma.whabcod.value != ''){
			document.forms.forma.action = 'Modificacion_traslados.php?wproceso=' + document.forms.forma.wproceso.value + '&whistoria=' + document.forms.forma.whistoria.value + '&waccion=D';
			document.forms.forma.submit();
		} else {
			alert('Debe seleccionar la habitación destino');
		}
	} else {
		alert('Debe seleccionar el servicio destino');
	}
}

function procesoDestinoHabitaciones() {
	document.forms.forma.action = 'Modificacion_traslados.php?wproceso=' + document.forms.forma.wproceso.value + '&whistoria=' + document.forms.forma.whistoria.value + '&waccion=D&wccocod=' + document.forms.forma.wccocod.value;
	document.forms.forma.submit();
}

function consultarHabitaciones() {
	document.forms.forma.action = 'Modificacion_traslados.php?wproceso=' + document.forms.forma.wproceso.value + '&whistoria=' + document.forms.forma.whistoria.value;
	document.forms.forma.submit();
}

function modificarMaterial() {
	document.forms.forma.action = 'Modificacion_traslados.php?wproceso=' + document.forms.forma.wproceso.value + '&whistoria=' + document.forms.forma.whistoria.value + '&waccion=M';
	document.forms.forma.submit();
}

function alternarDestino(){
	if(document.forms.forma.chkDestino){
		var btnAnular = document.getElementById("btAnular");		
		var btnAnular2 = document.getElementById("btAnular2");
		var btnModificar = document.getElementById("btModificar");
		var slServicios = document.getElementById("slCco");
		var slHabitaciones = document.getElementById("slHab");
		
		if(document.forms.forma.chkDestino.checked == true){
			btnAnular.disabled = true;
			btnModificar.disabled = false;
			slServicios.disabled = false;
			slHabitaciones.disabled = false;
			if(btnAnular2)
				btnAnular2.disabled = true;
		} else {
			btnAnular.disabled = false;
			btnModificar.disabled = true;
			slServicios.disabled = true;
			slHabitaciones.disabled = true;
			if(btnAnular2)
				btnAnular2.disabled = false;
		}
	}
}

function validarCantidadMaxima(codigoMedicamento, cantMaxima, cantActual){
	var cantidadIngresada = (document.getElementById("tx"+codigoMedicamento)).value;
	
	var intCantidad = parseInt(cantidadIngresada);
	var intCantMaxima = parseInt(cantMaxima);
	var intCantActual = parseInt(cantActual);
	
	if(!isNaN(cantidadIngresada) && !isNaN(cantMaxima)){
		if(intCantidad == intCantActual){
			alert("La cantidad ingresada del medicamento no debe ser la misma actual.");
			document.getElementById("tx"+codigoMedicamento).value = '';
		} else {
			if(intCantidad > intCantMaxima){
				alert("La cantidad ingresada del medicamento supera la cantidad máxima permitida.");
				document.getElementById("tx"+codigoMedicamento).value = '';
			}
		}
	} else {
		alert("Debe ingresar un valor numérico");
		document.getElementById("tx"+codigoMedicamento).value = '';
	}
}

function alternarEliminar(codigoMedicamento){
	var cantidadIngresada = (document.getElementById("tx"+codigoMedicamento)).value;
	var chMedicamentoEliminar = document.getElementById("ch"+codigoMedicamento);
	
	if(chMedicamentoEliminar.checked == true){
		document.getElementById("tx"+codigoMedicamento).value = '';
		document.getElementById("tx"+codigoMedicamento).disabled = true;
	} else {
		document.getElementById("tx"+codigoMedicamento).disabled = false;
	}
}

function cerrarVentana()
{
	window.close();
}

function mostrarAyuda()
{
	window.open('../manuales/manual_modificacion_traslados.htm', 'window','width=650,height=550,scrollbars=yes,resizable=yes');
}

function modificarInnerHtml(contenido){

	var mensaje = document.getElementById("mensajes");
	if(mensaje){
		mensaje.innerHTML = contenido;
	}else{
		alert("no se pudo conseguir elemento");
	}
	
}

</script>
	
<?php
include_once("conex.php");

/*BS'D
 * MODIFICACION LISTA DE MATERIAL MEDICO
 * MODIFICACION DE DESTINO DE TRASLADO PARA ENTREGA Y RECIBO
 * ANULACION DE ENTREGA Y RECIBO
 * 
 * Autor: Mauricio Sánchez Castaño. 
 * 
 */
$wactualiz = "(Versión: 08 Enero de 2014)";                      // Aca se coloca la ultima fecha de actualizacion de este programa //

/*
 * TABLAS UTILIZADAS.
 * 
 * movhos_000017:  Entrega y Recibo de Pacientes
 * movhos_000004:  Detalle de saldos por paciente
 * movhos_000018:  Ubicacion actual de paciente, servicio habitacion anterior y actual  
 * movhos_000019:  Detalle de entrega y recibo de medicamentos  
 * movhos_000020:  Información de habitaciones 
 * movhos_000015:  Detalle aplicacion de material  
 * movhos_000030:  Saldos aplicados a paciente
 * movhos_000039:  Auditoria de modificacion de destino y anulacion de traslados (NUEVA)
 * movhos_000032:  Registro de Ingresos a servicios
 * movhos_000033:  Registro de Egresos a servicios
 * 
 * root_000017:  Información de pacientes
 * root_000037:  Numero de Ingreso de historias clinicas
 *
 * Modificaciones:
 * Septiembre 15 de 2016: Jonatan Lopez. Se cambia el numero de horas limite para cancelar el traslado a 24 horas por peticion de Beatriz Montoya, ademas se manejara como un parametro en la root_000051.
 * Septiembre 7 de 2016: Jonatan Lopez. Se asocia este programa a gestion de enfermeria para que la enfermera pueda cancelar el traslado del paciente, ademas si cambia el destino pueda recibirlo en ese nuevo destino.
	// --> 	Se inhabilita la opcion de de anular los recibos de pacientes.
	//		Jerson trujillo, 2015-05-28	
 * Enero 08 de 2014: Jonatan Lopes. Se agrega la funcion eliminar_estadistica_egreso en la funcion proceso_anulacion_recibo para que sea eliminado el registro de la tabla 	movhos_000033 con *					   				 respecto al egreso del paciente y asi no se afecten los indicadores.
 * Noviembre 30 de 2012: Jonatan Lopez. Cuando se anula una entrega, se activará de nuevo la solicitud en la central de camas.
 * Septiembre 18 de 2012.	Edwin MG.	Se quitan espacios al final de la historia para evitar que al cancelar la historia, se agregue un espacio al final.
 */

/*
 * Definición de Funciones
 */

/**
 * Consulta de la informacion del ultimo movimiento realizado para la historia y el ingreso dado, se usa para realizar validaciones para 
 * permitir cambios.
 * 
 * @param unknown_type $historia
 * @param unknown_type $ingreso
 * @return Horas transcurridas desde ultimo cambio, Tipo-Entrega o Recibo, Consecutivo del movimiento, Fecha data, Hora data, Servicio origen
 * Habitacion origen, Servicio destino, Habitacion destino, Nombre servicio origen, Nombre servicio destino
 */
function consultar_informacion_ultimo_estado($historia, $ingreso)
{
	global $wbasedato;
	global $conex;
	
	$q = "SELECT
			TIMESTAMPDIFF(HOUR, CONCAT(eyr.Fecha_data,' ', eyr.Hora_data), now()), eyr.Eyrtip, a.cdMvto, eyr.Fecha_data, eyr.Hora_data, eyr.Eyrsor, eyr.Eyrsde, eyr.Eyrhor, eyr.Eyrhde, ccoOr.Cconom, ccoDe.Cconom, eyr.Eyrids as id_solicitud  
		FROM
			".$wbasedato."_000017 eyr, ".$wbasedato."_000011 ccoOr, ".$wbasedato."_000011 ccoDe,
			(
			SELECT
				MAX(CAST(eyr.Eyrnum AS SIGNED)) cdMvto 
			FROM 
				".$wbasedato."_000017 eyr
			WHERE
				eyr.Eyrhis='".$historia."'
				AND eyr.Eyring='".$ingreso."'
				AND eyr.Eyrest='on'
			)a 
		WHERE
			eyr.Eyrnum = a.cdMvto
			AND ccoOr.Ccocod = eyr.Eyrsor
			AND ccoDe.Ccocod = eyr.Eyrsde;";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$resultado = array();
	if ($num > 0)
	{
		$resultado = mysql_fetch_array($res);
	}
	return $resultado;
}

/**
 * Consulta la ubicacion actual de un paciente dada una historia y un ingreso
 * 
 * @return 
 * Habitación actual
 * Habitacion anterior
 * Servicio actual 
 * Servicio anterio 
 */
function consultar_ubicacion_paciente($historia, $ingreso)
{
	global $wbasedato;
	global $conex;
	
	$q = "SELECT
			Ubihac, Ubihan, Ubisac, Ubisan	
		FROM
			".$wbasedato."_000018
		WHERE 
			Ubihis = '".$historia."'
			AND Ubiing = '".$ingreso."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$resultado = array();
	if ($num > 0)
	{
		$resultado = mysql_fetch_array($res);
	}
	return $resultado;
}

/**
 * Consulta la informacion de una habitacion
 *
 * @param unknown_type $habitacion
 * @return En alistamiento, disponible, estado, Pro, centro de costos, historia, ingreso
 */
function consultar_informacion_habitacion($habitacion)
{
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			Habali, Habdis, Habest, habpro, Habcco, Habhis, Habing
		FROM 
			".$wbasedato."_000020
		WHERE
			Habcod = '".$habitacion."'
		;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$resultado = array();
	if ($num > 0)
	{
		$resultado = mysql_fetch_array($res);
	}
	return $resultado;
}

/**
 * Consulta del ultimo ingreso de una historia
 *
 * @param unknown_type $historia
 * @return ingreso
 */
function consultar_ultimo_ingreso_historia($historia){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT" 
			." Oriing"
		." FROM"
			." root_000037"
		." WHERE"
			." Orihis='".$historia."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$ingreso = 0;
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$ingreso = $row[0];
	}
	return $ingreso;
}

/**
 * Consulta los datos basicos del paciente
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @return Nombre1, nombre2, apellido1, apellido2, id, cedula
 */
function consultar_info_paciente_x_historia($whistoria, $wingreso){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			pacno1, pacno2, pacap1, pacap2, pactid, pacced
		FROM 
			root_000036, root_000037
		WHERE
			oriced = pacced
			AND orihis = '".$whistoria."'
			AND oriing = '".$wingreso."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$info = array();
	if ($num > 0)
	{
		$info = mysql_fetch_array($res);		
	}
	return $info;
}

/**
 * Consulta el último codigo de traslado activo para un paciente
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @return codigo traslado
 */
function consultar_ultimo_codigo_activo_traslado($whistoria, $wingreso){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT
			MAX(CAST(eyr.Eyrnum AS SIGNED)) cdMvto 
		FROM 
			".$wbasedato."_000017 eyr
		WHERE
			eyr.Eyrhis='".$whistoria."'
			AND eyr.Eyring='".$wingreso."'
			AND eyr.Eyrest='on';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$consecutivo = 0;
	if ($num > 0)
	{
		$info = mysql_fetch_array($res);
		$consecutivo = $info[0];		
	}
	return $consecutivo;
}

/**
 * Consulta el penultimo codigo activo de traslado, se usa para anular la entrega cuando se realiza el proceso de anulación del recibo.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @return unknown
 */
function consultar_codigo_activo_anterior_traslado($whistoria, $wingreso, $consecutivoTraslado){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT
			MAX(CAST(eyr.Eyrnum AS SIGNED)) cdMvto 
		FROM 
			".$wbasedato."_000017 eyr
		WHERE
			eyr.Eyrest='on'
			AND eyr.Eyrhis='".$whistoria."'
			AND eyr.Eyring='".$wingreso."'
			AND eyr.Eyrnum!='".$consecutivoTraslado."'
			;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$consecutivo = 0;
	if ($num > 0)
	{
		$info = mysql_fetch_array($res);
		$consecutivo = $info[0];		
	}
	return $consecutivo;
}

/**
 * Consulta la información de un traslado dado un consecutivo
 * 
 * @return Fecha, hora, Servicio origen, servicio destino, habitación origen, tipo, Estado del traslado
 */
function consultar_traslado($consecutivoTraslado){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT
			Fecha_data,Hora_data,Eyrsor,Eyrsde,Eyrhor,Eyrhde,Eyrtip,Eyrest,Seguridad 	 
		FROM 
			".$wbasedato."_000017
		WHERE
			Eyrnum='".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$info = array();
	if ($num > 0)
	{
		$info = mysql_fetch_array($res);
	}
	return $info;
}

/**
 * Modifica los estados alistamiento y disponible de una habitacion 
 *
 * @param unknown_type $wcodigoHabitacion
 * @param unknown_type $walistamiento
 * @param unknown_type $wdisponible
 */
function modificar_estados_habitacion($wcodigoHabitacion, $walistamiento, $wdisponible)
{
	global $wbasedato;
	global $conex;
	
	$q = "UPDATE 
			".$wbasedato."_000020
		SET
			Habali = '".$walistamiento."', 
			Habdis = '".$wdisponible."'
		WHERE
			Habcod = '".$wcodigoHabitacion."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * Modifica la historia y el ingreso registrados para una habitación
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $wcodigoHabitacion
 */
function modificar_historia_habitacion($whistoria, $wingreso, $wcodigoHabitacion)
{
	global $wbasedato;
	global $conex;
	
	$whistoria = trim( $whistoria );	//Septiembre 18 de 2012
	
	$q = "UPDATE 
			".$wbasedato."_000020
		SET
			Habhis = '".$whistoria."', 
			Habing = '".$wingreso."'
		WHERE
			Habcod = '".$wcodigoHabitacion."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());		
}

/**
 * Modifica el estado de un traslado
 *
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $westado
 */
function modificar_estado_registro_traslado($consecutivoTraslado, $westado)
{
	global $wbasedato;
	global $conex;
    global $wcencam;
   
    
    //Se consulta el identificador de la solicitud cuando se hizo la entrega.
    $q = "SELECT Eyrids
			FROM ".$wbasedato."_000017
		   WHERE Eyrnum = '".$consecutivoTraslado."'";  	
  	$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
  	$row = mysql_fetch_array($res1);
    $widsolicitud = $row['Eyrids'];     
	
    //Se actualiza el consecutivo al estado que trae la funcion
	$q = "UPDATE ".$wbasedato."_000017 
             SET Eyrest = '".$westado."'			 
		   WHERE Eyrnum = '".$consecutivoTraslado."';";				
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());    
    
    if ($westado == 'off')
        {
        //=========================================================================================================================
        //Si el estdo que recibe es inactivar el traslado, entonces se debe activar la solicitud en la tabla 3 de cencam con el cumplimiento en ceros.
        $q = "UPDATE ".$wcencam."_000003
                 SET Fecha_Cumplimiento = '0000-00-00', Hora_cumplimiento = '00:00:00'			 
               WHERE id = '".$widsolicitud."'";				
        $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());      
        
        //============================================================================================================================
        
        //Se modifica el estado de realizado a off para la solicitud en la tabla 10 de cencam
        $q = "UPDATE ".$wcencam."_000010
                 SET Acarea = 'off'			 
               WHERE Acaids = '".$widsolicitud."'";				
        $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
        
        }
}

/**
 * Modifica el servicio y habitación actual por el anterior servicio y habitación
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 */
function regresar_paciente_a_ubicacion_anterior($whistoria, $wingreso)
{
	global $wbasedato;
	global $conex;

	//Ubihac, Ubihan, Ubisac, Ubisan
	$ubicacionPaciente = consultar_ubicacion_paciente($whistoria, $wingreso);
	
	//Regresa el paciente a la ubicacion anterior, quitando el registro del servicio anterior y habitacion
	$q = "UPDATE  
			".$wbasedato."_000018
		SET
			Ubisac = '".$ubicacionPaciente[3]."',
			Ubihac = '".$ubicacionPaciente[1]."',
			Ubisan = '',
			Ubihan = ''
		WHERE
			Ubihis = '".$whistoria."'
			AND Ubiing = '".$wingreso."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * Elimina la estadística de egreso, la fecha data y hora data deben ser las mismas de la tabla 17
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $wfechaDataIngreso
 * @param unknown_type $whoraDataIngreso
 */
function eliminar_estadistica_egreso($whistoria, $wingreso, $wfechaDataIngreso, $whoraDataIngreso)
{
	global $wbasedato;
	global $conex;
	
	$q = "DELETE FROM 
			".$wbasedato."_000033
		WHERE
			Historia_clinica = '".$whistoria."'
			AND Fecha_data = '".$wfechaDataIngreso."'
			AND Hora_data = '".$whoraDataIngreso."'
			AND Num_ingreso = '".$wingreso."';";
				
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());		
}

/**
 * Elimina la estadística de ingreso, la fecha data y hora data deben ser las mismas de la tabla 17
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $wfechaDataIngreso
 * @param unknown_type $whoraDataIngreso
 */
function eliminar_estadistica_ingreso($whistoria, $wingreso, $wfechaDataIngreso, $whoraDataIngreso)
{
	global $wbasedato;
	global $conex;
	
	$q = "DELETE FROM 
			".$wbasedato."_000032
		WHERE
			Historia_clinica = '".$whistoria."'
			AND Fecha_data = '".$wfechaDataIngreso."'
			AND Hora_data = '".$whoraDataIngreso."'
			AND Num_ingreso = '".$wingreso."';";
				
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());		
}

/**
 * Realiza el registro de la auditoria de cambios de anulación y cambio destino
 *
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $fechaRegistro
 * @param unknown_type $horaRegistro
 * @param unknown_type $anulacion
 * @param unknown_type $fechaAnulacion
 * @param unknown_type $horaAnulacion
 * @param unknown_type $modificacion
 * @param unknown_type $fechaModificacion
 * @param unknown_type $horaModificacion
 * @param unknown_type $tipoCambio
 * @param unknown_type $usuario
 */
function registro_auditoria_cambios_traslados($consecutivoTraslado, $fechaRegistro, $horaRegistro, $anulacion, $fechaAnulacion, $horaAnulacion, $modificacion, $fechaModificacion, $horaModificacion, $tipoCambio, $usuario)
{
	global $wbasedato;
	global $conex;

	$q="INSERT INTO  
			".$wbasedato."_000039 (medico, Fecha_data, Hora_data, Aunnum, Aunanu, Aunfan, Aunhan, Aunmod, Aunfmo, Aunhmo, Aunacc, Seguridad)
		VALUES
			('movhos','".$fechaRegistro."','".$horaRegistro."','".$consecutivoTraslado."','".$anulacion."','".$fechaAnulacion."','".$horaAnulacion."','".$modificacion."','".$fechaModificacion."','".$horaModificacion."','".$tipoCambio."', 'A-".$usuario."');";	

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * Lista los centros de costos mediante un select que se coloca en la página, el parametro seleccion indica que centro de costos estará seleccionado
 * por defecto, el parametro funcion es el nombre de la función javascript a ejecutar en el evento que cambie el valor del select.
 *
 * @param unknown_type $seleccionCco
 * @param unknown_type $funcion
 */
function listado_centros_de_costos($seleccionCco, $funcion){
	global $wbasedato;
	global $conex;
	
	echo "<select name='wccocod' onchange ='".$funcion."'>";
	
  	$q = "SELECT 
				ccocod, UPPER(Cconom)
			FROM 
				".$wbasedato."_000011
			WHERE  
  				Ccohos = 'on'
			ORDER by 1;";
  	
  	$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
  	$num1 = mysql_num_rows($res1);
  		
  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$row1 = mysql_fetch_array($res1);
  			if($seleccionCco == $row1[0])
  			{
  				echo "<option value='".$row1[0]."' selected>".$row1[0]." - ".$row1[1]."</option>";
  			} else {
  				echo "<option value='".$row1[0]."'>".$row1[0]." - ".$row1[1]."</option>";
  			}
  		}
  	}
  	echo "</select>";
}

/**
 * Lista los centros de costos que hospitalizan, que se encuentren activos, con habitaciones disponibles en forma de select, el parametro seleccion indica que centro de costos estará seleccionado
 * por defecto, el parametro funcion es el nombre de la función javascript a ejecutar en el evento que cambie el valor del select.
 *
 * @param unknown_type $seleccionCco
 * @param unknown_type $funcion
 */
function listado_centros_de_costos_hosp_disponibles($seleccionCco, $funcion){
	global $wbasedato;
	global $conex;
	
	echo "<select name='wccocod' onchange ='".$funcion."' id='slCco'>";
	
  	$q = "SELECT 
			ccocod, UPPER(Cconom)
		FROM 
			".$wbasedato."_000011,
			(
				SELECT 
					Habcco 
				FROM
					".$wbasedato."_000020
				WHERE 
					Habdis = 'on'
				GROUP BY 
					Habcco 
			) a
		WHERE  
			Ccohos = 'on'
			AND Ccocod = '".$seleccionCco."'
			AND a.Habcco = ccocod 
		ORDER by 1;";
  	
  	$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
  	$num1 = mysql_num_rows($res1);
  		
  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$row1 = mysql_fetch_array($res1);
  			if($seleccionCco == $row1[0])
  			{
  				echo "<option value='".$row1[0]."' selected>".$row1[0]." - ".$row1[1]."</option>";
  			} else {
  				echo "<option value='".$row1[0]."'>".$row1[0]." - ".$row1[1]."</option>";
  			}
  		}
  	}
  	echo "</select>";
}

/**
 * Lista las habitaciones disponibles en forma de select dado un centro de costos
 *
 * @param unknown_type $servicioDestinoTraslado
 * @param unknown_type $seleccion
 * @param unknown_type $funcion
 */
function listado_habitaciones_cco($servicioDestinoTraslado, $seleccion, $funcion)
{
	global $wbasedato;
	global $conex;
	
	echo "<select name='whabcod' onchange ='".$funcion."' id='slHab'>";
	
  	$q = "SELECT 
			Habcod, Habcco
		FROM 
  			".$wbasedato."_000020
  		WHERE 
  			Habcco = '".$servicioDestinoTraslado."'
  			AND Habdis = 'on'  			
		ORDER by 1;";
  	
  	$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
  	$num1 = mysql_num_rows($res1);
  		
  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$row1 = mysql_fetch_array($res1);
  			if($row1[0] != $seleccion) {
  				echo "<option value='".$row1[0]."'>".$row1[0]."</option>";
  			}
  		}
  	}
  	echo "</select>";
}

/**
 * PROCESO:: Realiza el proceso de anulación de una entrega.  
 * 
 * 
 * 
 * NOTA 1: El movimiento de saldos de medicamentos se manipula aparte en caso de anular SOLO LA ENTREGA
 * NOTA 2: Este proceso se invoca en la anulación de entrega y DESDE el proceso de anulación de recibo ya que este ultimo realiza tanto la anulación del recibo como de la entrega.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $habitacionAnterior
 * @param unknown_type $habitacionDestino
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $fechaEstadoTraslado
 * @param unknown_type $horaEstadoTraslado
 * @param unknown_type $usuario
 * @param unknown_type $tipoCambio
 */
function proceso_anulacion_entrega($whistoria, $wingreso, $habitacionAnterior, $habitacionDestino, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $usuario, $tipoCambio){

	//Borra la historia y el ingreso de la habitación actual
	modificar_historia_habitacion("", "", $habitacionDestino);
	
	//Marca la habitación actual como no en alistamiento y disponible 
	modificar_estados_habitacion($habitacionDestino, 'off', 'on');
	
	//Marca la habitación anterior como no en alistamiento y no disponible
	modificar_estados_habitacion($habitacionAnterior, 'off', 'off');
	
	//Marca la habitacion anterior con la historia e ingreso
	modificar_historia_habitacion($whistoria, $wingreso, $habitacionAnterior);

	//Si proviene desde Recibo, se debe anular tambien la entrega previa y realizar sus correspondientes movimientos de saldos para el consecutivo de traslado.
	if($tipoCambio == 'Recibo'){
		//Consulta del consecutivo asociado a la entrega (previa al recibo que se está anulando)
		$penultimoConsecutivo = consultar_ultimo_codigo_activo_traslado($whistoria,$wingreso);
		
		//Anula la Entrega		
		modificar_estado_registro_traslado($penultimoConsecutivo, 'off');
		
		//Consulta la información del traslado, se usa
		$infoTraslado = consultar_traslado($penultimoConsecutivo);
		
		//Elimina el registro de estadistica de ingreso. La fecha y hora data de esta estadistica debe coincidir con la registrada en recibo a anular, esto indicará el registro correcto a eliminar.
		eliminar_estadistica_egreso($whistoria, $wingreso, $infoTraslado[0], $infoTraslado[1]);
		
		//Audita la anulacion de la entrega realizada, previo al recibo
		registro_auditoria_cambios_traslados($penultimoConsecutivo, date("Y-m-d"), (string)date("H:i:s"), "on", date("Y-m-d"), (string)date("H:i:s"), "off", "0-0-0", "0:0:0", "Anulacion", $usuario);
	} elseif ($tipoCambio == 'Entrega'){
		//Anula la entrega 
		modificar_estado_registro_traslado($consecutivoTraslado, 'off');

		//Elimina el registro de estadistica de egreso. La fecha y hora data de esta estadistica debe coincidir con la registrada en la entrega, esto indicará el registro correcto a eliminar.
		eliminar_estadistica_egreso($whistoria, $wingreso, $fechaEstadoTraslado, $horaEstadoTraslado);
	}

	//Devuelve el paciente a la ubicacion anterior, se elimina el registro de donde se encontraba antes por lo que la anulación solo puede ejecutarse una vez.
	regresar_paciente_a_ubicacion_anterior($whistoria, $wingreso);

	/* Modifica el estado de traslado:  Al anular la Entrega el paciente queda recibido en el servicio anterior y al anular el recibo
	 * como se anula tambien la entrega anterior el paciente quedará recibido en el servicio anterior por lo tanto cuando se realiza una
	 * anulación el paciente no quedará en proceso de traslado. 
	 */ 
	modificar_estado_traslado($whistoria, $wingreso, 'off');

	//Audita de anulación de entrega
	registro_auditoria_cambios_traslados($consecutivoTraslado, date("Y-m-d"), (string)date("H:i:s"), "on", date("Y-m-d"), (string)date("H:i:s"), "off", "0-0-0", "0:0:0", "Anulacion", $usuario);
}

/**
 * PROCESO:: Realiza el proceso de anulación de Recibo y adicionalmente de la Entrega previa.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $habitacionAnterior
 * @param unknown_type $habitacionDestino
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $fechaEstadoTraslado
 * @param unknown_type $horaEstadoTraslado
 * @param unknown_type $wuser
 */
function proceso_anulacion_recibo($whistoria, $wingreso, $habitacionAnterior, $habitacionDestino, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $wuser){
	//Anula el registro de Recibo
	modificar_estado_registro_traslado($consecutivoTraslado, 'off');

	//Realiza el proceso de anulación de la Entrega previa al movimiento de recibo
	proceso_anulacion_entrega($whistoria, $wingreso, $habitacionAnterior, $habitacionDestino, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $wuser, "Recibo");
	
	//Elimina la estadística de ingreso al servicio actual, la fecha y hora data deben coincidir con las del traslado.
	eliminar_estadistica_ingreso($whistoria, $wingreso, $fechaEstadoTraslado, $horaEstadoTraslado);
	eliminar_estadistica_egreso($whistoria, $wingreso, $fechaEstadoTraslado, $horaEstadoTraslado);
}

/**
 * Modifica la ubicación actual de un paciente
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $servicioOrigen
 * @param unknown_type $habitacionOrigen
 * @param unknown_type $servicioDestino
 * @param unknown_type $habitacionDestino
 */
function modificar_ubicacion_actual_paciente($whistoria, $wingreso, $servicioOrigen, $habitacionOrigen, $servicioDestino, $habitacionDestino, $id_solicutud_cama) {
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	
	$infoInstitucion = consultar_informacion_institucion($wemp_pmla);
    $wcencam = $infoInstitucion[5];
	
	$q = "UPDATE ".$wcencam."_000003  
		     SET hab_asignada = '".$habitacionDestino."'
		   WHERE id = '".$id_solicutud_cama."'";	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	
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
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * Modifica el estado de traslado en la ubicacion del paciente
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $estado
 */
function modificar_estado_traslado($whistoria, $wingreso, $estado){
	global $wbasedato;
	global $conex;
	
	$q = "UPDATE 
			".$wbasedato."_000018  
		SET
			Ubiptr = '".$estado."'
		WHERE
			Ubihis = '".$whistoria."'
			AND Ubiing = '".$wingreso."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * Modifica el destino de traslado de un paciente.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $servicioDestino
 * @param unknown_type $habitacionDestino
 */
function modificar_destino_traslado_paciente($whistoria, $wingreso, $consecutivoTraslado, $servicioDestino, $habitacionDestino) {
	global $wbasedato;
	global $conex;
	
	$q = "UPDATE
			".$wbasedato."_000017  
		SET
			Eyrsde = '".$servicioDestino."',
			Eyrhde = '".$habitacionDestino."'						
		WHERE
			Eyrnum = '".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * Modifica el destino de traslado de un paciente para el caso del recibo
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $servicioDestino
 * @param unknown_type $habitacionDestino
 */
function modificar_destino_traslado_paciente_recibo($whistoria, $wingreso, $consecutivoTraslado, $servicioDestino, $habitacionDestino) {
	global $wbasedato;
	global $conex;
	
	$q = "UPDATE
			".$wbasedato."_000017  
		SET
			Eyrsde = '".$servicioDestino."',
			Eyrhde = '".$habitacionDestino."',	
			Eyrhor = '".$habitacionDestino."'
		WHERE
			Eyrnum = '".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * PROCESO: Proceso de cambio de destino para entrega
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $servicioAnterior
 * @param unknown_type $habitacionAnterior
 * @param unknown_type $wccocod
 * @param unknown_type $whabdes
 * @param unknown_type $habitacionDestinoTemp
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $fechaEstadoTraslado
 * @param unknown_type $horaEstadoTraslado
 * @param unknown_type $usuario
 * @param unknown_type $tipoCambio
 */
function proceso_cambio_destino_entrega($whistoria, $wingreso, $servicioAnterior, $habitacionAnterior, $wccocod, $whabdes, $habitacionDestinoTemp, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $usuario, $tipoCambio, $id_solicutud_cama){
	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	
	$infoInstitucion = consultar_informacion_institucion($wemp_pmla);
    $wcencam = $infoInstitucion[5];
	
	$q = "SELECT Hab_asignada 
		    FROM ".$wcencam."_000003
		   WHERE id = '".$id_solicutud_cama."'";	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$hab_asignada = $row['Hab_asignada'];
	
	//Modifica la ubicacion actual del paciente (servicio origen, habitacion origen, servicio destino, habitación destino)
	modificar_ubicacion_actual_paciente($whistoria, $wingreso, $servicioAnterior, $habitacionAnterior, $wccocod, $whabdes, $id_solicutud_cama);		
	
	//Modifica el codigo del servicio registrado en la estadistica de egresos
	$q="UPDATE 
			".$wbasedato."_000033  
		SET
			Tipo_egre_serv = '".$wccocod."'			
		WHERE
			Historia_clinica = '".$whistoria."'
			AND Num_ingreso = '".$wingreso."'
			AND Fecha_data = '".$fechaEstadoTraslado."'
			AND Hora_data = '".$horaEstadoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	if($tipoCambio == 'Recibo'){
		//Cambia el destino en el registro de Recibo por los nuevos
		modificar_destino_traslado_paciente_recibo($whistoria,$wingreso,$consecutivoTraslado, $wccocod, $whabdes);
		
		//Consulta el consecutivo de la entrega, es decir, el anterior al del recibo
		$consecutivoAnterior = consultar_codigo_activo_anterior_traslado($whistoria, $wingreso, $consecutivoTraslado);
		
		//Cambia el destino de la Entrega previa al Recibo
		modificar_destino_traslado_paciente($whistoria,$wingreso,$consecutivoAnterior, $wccocod, $whabdes);
		
		//Audita el cambio de destino de entrega PREVIO AL RECIBO
		registro_auditoria_cambios_traslados($consecutivoAnterior, date("Y-m-d"), (string)date("H:i:s"), "off", "0-0-0", "0:0:0", "on", date("Y-m-d"), (string)date("H:i:s"), "Destino", $usuario);
	} elseif($tipoCambio == 'Entrega'){
		//Modifica el destino en el registro de entrega
		modificar_destino_traslado_paciente($whistoria,$wingreso,$consecutivoTraslado, $wccocod, $whabdes);
	}
	
	//Marca la habitacion anterior como no en alistamiento y disponible 
	modificar_estados_habitacion($habitacionDestinoTemp, "off", "on");
	
	//Quita la historia de la habitacion anterior liberada
	modificar_historia_habitacion("","",$habitacionDestinoTemp);
	
	//Marca habitacion destino nueva como ocupada
	modificar_estados_habitacion($whabdes, "off", "off");
	
	//Marca la habitacion destino nueva con la historia y el ingreso
	modificar_historia_habitacion($whistoria,$wingreso,$whabdes);
	
	//Audita el cambio de destino de entrega
	registro_auditoria_cambios_traslados($consecutivoTraslado, date("Y-m-d"), (string)date("H:i:s"), "off", "0-0-0", "0:0:0", "on", date("Y-m-d"), (string)date("H:i:s"), "Destino, hab anterior en cencam3: $hab_asignada", $usuario);
}

/**
 * PROCESO: Proceso de cambio de destino para recibo.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $servicioAnterior
 * @param unknown_type $habitacionAnterior
 * @param unknown_type $servicioDestino
 * @param unknown_type $habitacionDestino
 * @param unknown_type $habitacionDestinoTemp
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $fechaEstadoTraslado
 * @param unknown_type $horaEstadoTraslado
 * @param unknown_type $usuario
 */
function proceso_cambio_destino_recibo($whistoria, $wingreso, $servicioAnterior, $habitacionAnterior, $servicioDestino, $habitacionDestino, $habitacionDestinoTemp, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $usuario){
	global $wbasedato;
	global $conex;
	
	//Proceso de cambio de destino para la entrega.
	proceso_cambio_destino_entrega($whistoria, $wingreso, $servicioAnterior, $habitacionAnterior, $servicioDestino, $habitacionDestino, $habitacionDestinoTemp, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $usuario, "Recibo");
	
	//Modifica el servicio por el actual en la estadistica de ingreso
	$q = "UPDATE 
			".$wbasedato."_000032  
		SET
			Servicio = '".$servicioDestino."'			
		WHERE
			Historia_clinica = '".$whistoria."'
			AND Num_ingreso = '".$wingreso."'	
			AND Fecha_data = '".$fechaEstadoTraslado."'
			AND Hora_data = '".$horaEstadoTraslado."'	
		;";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	//Consulta el penultimo consecutivo de traslado, para modificar la estadistica de egreso 
	$penultimoConsecutivo = consultar_codigo_activo_anterior_traslado($whistoria,$wingreso,$consecutivoTraslado);
	
	//Consulta la información del traslado
	$infoTraslado = consultar_traslado($penultimoConsecutivo);
	
	//Modifica el codigo del servicio registrado en la estadistica de egresos
	$q = "UPDATE 
			".$wbasedato."_000033  
		SET
			Tipo_egre_serv = '".$servicioDestino."'			
		WHERE
			Historia_clinica = '".$whistoria."'
			AND Num_ingreso = '".$wingreso."'
			AND Fecha_data = '".$infoTraslado[0]."'
			AND Hora_data = '".$infoTraslado[1]."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	//Audita el cambio de destino de recibo
	registro_auditoria_cambios_traslados($consecutivoTraslado, date("Y-m-d"), (string)date("H:i:s"), "off", "0-0-0", "0:0:0", "on", date("Y-m-d"), (string)date("H:i:s"), "Destino", $usuario);
}

/**
 * PROCESO: Proceso de anulación del recibo UNICAMENTE dejando al paciente en estado entregado.  Esto se usa para permitirle al usuario 
 * ingresar mas articulos a la entrega del paciente en el servicio destino.
 * 
 * Se permite unicamente cuando el servicio origen es de aplicación automática
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $servicioOrigenTraslado
 * @param unknown_type $servicioDestinoTraslado
 */
function proceso_anulacion_solo_recibo($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigen, $servicioDestino, $fechaTraslado, $horaTraslado, $usuario){
	
	if(consultar_aplicacion_automatica_cco($servicioOrigen) == 'on'){
		//Anula el registro de Recibo
		modificar_estado_registro_traslado($consecutivoTraslado, 'off');

		//Activar el proceso de traslado en la ubicacion del paciente
		modificar_estado_traslado($whistoria, $wingreso, 'on');
		
		//Elimina la estadística de ingreso al servicio actual, la fecha y hora data deben coincidir con las del traslado.
		eliminar_estadistica_ingreso($whistoria, $wingreso, $fechaTraslado, $horaTraslado);
		
		//Audita de anulación de recibo
		registro_auditoria_cambios_traslados($consecutivoTraslado, date("Y-m-d"), (string)date("H:i:s"), "on", date("Y-m-d"), (string)date("H:i:s"), "off", "0-0-0", "0:0:0", "Anulacion", $usuario);
	}
}

/**
 * Consulta la cantidad de articulos que se encuentran como pendientes de aplicación para un traslado
 *
 * @param unknown_type $consecutivoTraslado
 * @return cantidad de articulos
 */
function cuenta_saldo_pendiente_paciente($consecutivoTraslado) {
	global $wbasedato;
	global $conex;
	
	$q="SELECT 
			COUNT(Detart)
		FROM
			".$wbasedato."_000019 
		WHERE	
			Detnum = ".$consecutivoTraslado."
			AND Detest = 'on';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$cuenta = 0;
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$cuenta = $row[0];
	}
	return $cuenta;
}

/**
 * Consulta si un centro de costos aplica automaticamente o no:  On: Si - Off: No
 *
 * @param unknown_type $codCentroCostos
 * @return aplica
 */
function consultar_aplicacion_automatica_cco($codCentroCostos) {	
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			Ccoapl
		FROM
			".$wbasedato."_000011 
		WHERE	
			Ccocod = '".$codCentroCostos."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$aa = "";
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$aa = $row[0];
	}
	return $aa;
}

/**
 * Verifica si un articulo se encuentra previamente aplicado
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $fechaIngreso
 * @param unknown_type $horaIngreso
 */
function existe_articulo_aplicado($whistoria, $wingreso, $codigoArticulo, $fechaIngreso, $horaIngreso){
	global $wbasedato;
	global $conex;

	$existeAplicado = false;
	
	$q = "SELECT
			COUNT(*)
		FROM 
			".$wbasedato."_000015
		WHERE
			Aplhis = '".$whistoria."'
			AND Apling = '".$wingreso."'
			AND Aplart = '".$codigoArticulo."'
			AND Fecha_data = '".$fechaIngreso."'
			AND Hora_data = '".$horaIngreso."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$resultado1 = array();
	if ($num > 0)
	{
		$resultado1 = mysql_fetch_array($res);
		$existeAplicado = true;
	}
	return $existeAplicado;
}

/**
 * Consulta si un medicamento tiene entradas de aprovechamiento en los saldos del paciente, se utiliza en el ingreso de aplicacion de articulos
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $codigoArticulo
 * @return unknown
 */
function consultar_aprovechamiento_saldo_pac($whistoria, $wingreso, $codigoArticulo){
	global $wbasedato;
	global $conex;

	$existeAplicado = false;
	
	$q = "SELECT
			COUNT(*)
		FROM 
			".$wbasedato."_000004
		WHERE
			Spahis = '".$whistoria."'
			AND Spaing = '".$wingreso."'
			AND Spaart = '".$codigoArticulo."'
			AND Spauen > Spausa
			AND Spaaen > 0;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$resultado1 = array();
	if ($num > 0)
	{
		$resultado1 = mysql_fetch_array($res);
		if($resultado1 > 0){
			$existeAplicado = true;
		}
	}
	return $existeAplicado;
}

/**
 * Consulta si un articulo en la tabla de saldos aplicados (servicios de aplicacion automatica) tienen aprovechamiento.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $codigoArticulo
 * @return unknown
 */
function consultar_aprovechamiento_saldo_apl($whistoria, $wingreso, $codigoArticulo){
	global $wbasedato;
	global $conex;

	$existeAplicado = false;
	
	$q = "SELECT
			COUNT(*)
		FROM 
			".$wbasedato."_000030
		WHERE
			Splhis = '".$whistoria."'
			AND Spling = '".$wingreso."'
			AND Splart = '".$codigoArticulo."'
			AND Spluen > Splusa
			AND Splaen > 0;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$resultado1 = array();
	if ($num > 0)
	{
		$resultado1 = mysql_fetch_array($res);
		if($resultado1 > 0){
			$existeAplicado = true;	
		}
	}
	return $existeAplicado;
}


/**
 * Inserta una nueva aplicación de medicamento.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $codigoArticulo
 * @param unknown_type $cantidadArticulo
 * @param unknown_type $fechaIngresoArticulo
 * @param unknown_type $horaIngresoArticulo
 */
function crear_aplicacion_medicamento($whistoria, $wingreso, $consecutivoTraslado, $codigoArticulo, $cantidadArticulo, $servicio, $fechaIngresoArticulo, $horaIngresoArticulo, $aprovechamiento, $usuario){
	global $wbasedato;
	global $conex;	
	
	$q1 = "INSERT INTO
				".$wbasedato."_000015 (Medico, Fecha_data, Hora_data, Aplhis, Apling, Aplron, Aplart, Apldes, Aplcan, Aplcco, Aplusu, Aplapr, Aplest, Aplaap, Aplnde, Aplfde, Apldde, Aplnum, Apllin, Aplapv, Aplfec, Aplapl, Seguridad)
			VALUES	
				('".$wbasedato."','".$fechaIngresoArticulo."','".$horaIngresoArticulo."','".$whistoria."','".$wingreso."','".date('H:i - A')."','".$codigoArticulo."','".consultar_nombre_articulo($codigoArticulo)."','".$cantidadArticulo."',".$servicio."','".$usuario."','off','on','0','','','','','','".$aprovechamiento."','".$fechaIngresoArticulo."','','A-".$usuario."');";
		
	$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
}

/**
 * Realiza la aplicación de un medicamento de acuerdo a la cantidad dada por el detalle de entrega y recibo, esto se usa para la anulación
 * de Cco origen AA a Cco destino NAA
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdMedicamento
 */
function activar_aplicacion_medicamento_desde_detalle_entrega($whistoria, $wingreso, $consecutivoTraslado, $codigoArticulo, $cantidadArticulo, $fechaAplicacion, $horaAplicacion){
	global $wbasedato;
	global $conex;
	
	$q3 = "UPDATE
				".$wbasedato."_000015
			SET
				Aplest = 'on'
			WHERE 
				Fecha_data = '".$fechaAplicacion."'   
				AND Hora_data = '".$horaAplicacion."'  
				AND Aplhis = '".$whistoria."'  
				AND Apling = '".$wingreso."' 
				AND Aplart = '".$codigoArticulo."';";

	$res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
}

/**
 * Inactiva las aplicaciones asociadas en el detalle de recibo para la tabla 15
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $codigoArticulo
 * @param unknown_type $cantidadArticulo
 * @param unknown_type $servicio
 */
function inactivar_aplicacion_medicamento_desde_detalle_recibo($whistoria, $wingreso, $consecutivoTraslado, $codigoArticulo, $cantidadArticulo, $fechaAplicacion, $horaAplicacion){
	global $wbasedato;
	global $conex;
	
	$q3 = "UPDATE
				".$wbasedato."_000015
			SET
				Aplest = 'off'
			WHERE 
				Fecha_data = '".$fechaAplicacion."'   
				AND Hora_data = '".$horaAplicacion."'  
				AND Aplhis = '".$whistoria."'  
				AND Apling = '".$wingreso."' 
				AND Aplart = '".$codigoArticulo."';";
	
	$res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
}

/**
 * Consulta el centro de costos que realizo cargas de material medico sobre la tabla 30 para saber a que registro efectuar la modificación
 * en las entradas.
 *  
 * Sujeto a las siguientes condiciones: 
 * 
 * -Ultimo que cargo
 * -No sea hospitalario (los hospitalarios cargan y quedan aplicados): Por lo general los saldos que se mueven son los que tengan cco no hosp.
 * 
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $cdMedicamento
 * @return ultimo centro de costos que modifico el listado
 */
function consultar_ultimo_cco_modifica_saldos_app($whistoria, $wingreso, $cdMedicamento){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT DISTINCT 
			Splcco
		FROM 
			".$wbasedato."_000030, ".$wbasedato."_000011			
		WHERE
			Ccocod = Splcco 
			AND Ccohos = 'off'		
			AND Splhis = '".$whistoria."'
			AND Spling = '".$wingreso."'
			AND Splart = '".$cdMedicamento."'
		ORDER BY
			".$wbasedato."_000030.Fecha_data DESC, ".$wbasedato."_000030.Hora_data DESC;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$info = "";
	$data = array();
	if ($num > 0)
	{
		$data = mysql_fetch_array($res);
		$info = $data[0];
	}
	return $info;
}

/**
 * Consulta el centro de costos que modifica los saldos de la tabla 4
 *
 * Sujeto a las siguientes condiciones: 
 * 
 * -Ultimo que cargo
 * -No sea hospitalario (los hospitalarios cargan y quedan aplicados): Por lo general los saldos que se mueven son los que tengan cco no hosp.
 * 
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $cdMedicamento
 * @return ultimo centro de costos que modifico el listado
 */
function consultar_ultimo_cco_modifica_saldos_pac($whistoria, $wingreso, $cdMedicamento){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT DISTINCT
			Spacco
		FROM 
			".$wbasedato."_000004, ".$wbasedato."_000011
		WHERE
			Ccocod = Spacco 
			AND Ccohos = 'off'		
			AND Spahis = '".$whistoria."'
			AND Spaing = '".$wingreso."'
			AND Spaart = '".$cdMedicamento."'
		ORDER BY
			".$wbasedato."_000004.Fecha_data DESC, ".$wbasedato."_000004.Hora_data DESC;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$info = "";
	$data = array();
	if ($num > 0)
	{
		$data = mysql_fetch_array($res);
		$info = $data[0];
	}
	return $info;
}

/**
 * PROCESO:: Proceso de reversa del listado de medicamentos para entregas
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 */
function proceso_devolucion_med_entrega($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigen, $servicioActual){
	global $wbasedato;
	global $conex;
	global $wuser;
	
	//Consulta del material en el detalle a mover 
	$q = "SELECT 
			Detart, Detcan, Fecha_data, Hora_data 
		 FROM 
			".$wbasedato."_000019
		 WHERE
		 	Detest = 'on'
			AND Detnum = '".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if ($num > 0)
	{
		$cont1 = 0;
		
		while($cont1 <= $num){
			$resultado = mysql_fetch_array($res);

			$codigoArticulo = $resultado[0];
			$cantidadArticulo = $resultado[1];
			$fechaIngresoArticulo = $resultado[2];
			$horaIngresoArticulo = $resultado[3];

			if($codigoArticulo != ''){
				//TODO::::Debug
//				mensajeEmergente("Moviendo ".$codigoArticulo);
				
				//Actualiza el saldo de medicamento modificado
				actualizar_saldo_medicamento_modificado($whistoria, $wingreso, $codigoArticulo, $cantidadArticulo, 0, "Entrega", $servicioOrigen, $servicioActual);

				//Si la aplicacion del articulo existe, actualiza su estado de aplicacion, caso contrario inserta una nueva aplicacion
				if(existe_articulo_aplicado($whistoria, $wingreso, $codigoArticulo, $fechaIngresoArticulo, $horaIngresoArticulo)){
					//Activa la aplicacion del medicamento de acuerdo al consecutivo
					activar_aplicacion_medicamento_desde_detalle_entrega($whistoria, $wingreso, $consecutivoTraslado, $codigoArticulo, $cantidadArticulo, $fechaIngresoArticulo, $horaIngresoArticulo);
				}else{
					//Si en la tabla de saldos del paciente NAA: tabla 04 existe aprovechamiento, se ingresa aprovechamiento en on para la aplicacion nueva.
					$aprovechamiento = consultar_aprovechamiento_saldo_pac($whistoria, $wingreso, $codigoArticulo);					
					
					//Crea la aplicacion del medicamento
					crear_aplicacion_medicamento($whistoria, $wingreso, $consecutivoTraslado, $codigoArticulo, $cantidadArticulo, $servicioActual, $fechaIngresoArticulo, $aprovechamiento, $horaIngresoArticulo, $wuser);
				}
									
				//Anula el detalle
				anular_medicamento_detalle($consecutivoTraslado, $codigoArticulo);
			}
			$cont1++;
		}
	}
}

/**
 * PROCESO: Realiza el proceso de devolución de saldos para la entrega en el cambio de destino.
 * 
 * A diferencia del proceso normal de anulación en este no se anula el detalle aún así se haya movido en los saldos, esto debido a que 
 * los articulos definidos en el detalle deben quedar disponibles para la proxima entrega 
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $servicioOrigenTraslado
 * @param unknown_type $servicioDestinoTraslado
 */
function proceso_movimiento_saldos_destino_med_entrega($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTraslado, $servicioNuevo){
	global $wbasedato;
	global $conex;
	
	//Consulta del material en el detalle a mover 
	$q = "SELECT 
			Detart, Detcan, Fecha_data, Hora_data 
		 FROM 
			".$wbasedato."_000019
		 WHERE
		 	Detest = 'on'
			AND Detnum = '".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	//Consulta del tipo de aplicacion del servicio origen
	//AA -Aplicacion Automatica, NAA No Aplicacion Automatica
	$servicioOrigenAA = consultar_aplicacion_automatica_cco($servicioOrigenTraslado);
	$servicioActualAA = consultar_aplicacion_automatica_cco($servicioDestinoTraslado);
	$servicioDestinoAA = consultar_aplicacion_automatica_cco($servicioNuevo);
	
	if ($num > 0)
	{
		$cont1 = 0;
		
		while($cont1 <= $num){
			$resultado = mysql_fetch_array($res);

			$codigoArticulo = $resultado[0];
			$cantidadArticulo = $resultado[1];
			$fechaIngresoArticulo = $resultado[2];
			$horaIngresoArticulo = $resultado[3];			

			if($codigoArticulo != ''){
				//TODO::::Debug
//				mensajeEmergente("Moviendo ".$codigoArticulo);
				
				//Actualiza el saldo de medicamento modificados
				if($servicioOrigenAA == 'off' && $servicioActualAA == 'on' && $servicioDestinoAA=='off'){
					//-30 +04
					actualizar_saldo_medicamento_lista($whistoria, $wingreso, $consecutivoTraslado, $codigoArticulo, 0, $cantidadArticulo, "Entrega", $servicioOrigen, $servicioDestinoTraslado);

					if(existe_articulo_aplicado($whistoria, $wingreso, $codigoArticulo, $fechaIngresoArticulo, $horaIngresoArticulo)){
						inactivar_aplicacion_medicamento_desde_detalle_recibo($whistoria,$wingreso,$consecutivoTraslado, $codigoArticulo, $cantidadArticulo, $fechaIngresoArticulo, $horaIngresoArticulo);
					}
				} else {
					//+30 -04
					actualizar_saldo_medicamento_lista($whistoria, $wingreso, $consecutivoTraslado, $codigoArticulo, $cantidadArticulo, 0, "Entrega", $servicioOrigen, $servicioDestinoTraslado);
				}
			}
			$cont1++;
		}
	}
}

/**
 * PROCESO:: Proceso de reversa del listado de medicamentos para recibos
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 */
function proceso_devolucion_med_recibido($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigen, $servicioActual){
	global $wbasedato;
	global $conex;
	
	//Consulta del tipo de aplicación de los centros de costos
	$aplicacionCcoOrigen = consultar_aplicacion_automatica_cco($servicioOrigen);
	$aplicacionCcoActual = consultar_aplicacion_automatica_cco($servicioActual);
	
	//Consulta del material en el detalle de recibo
	$q = "SELECT 
			Detart, Detcan, Fecha_data, Hora_data
		 FROM 
			".$wbasedato."_000019
		 WHERE
		 	Detest = 'on'
			AND Detnum = '".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if ($num > 0)
	{
		$cont1 = 0;
		while($cont1 <= $num){
			$resultado = mysql_fetch_array($res);

			$codigoArticulo = $resultado[0];
			$cantidadArticulo = $resultado[1];
			$fechaIngresoArticulo = $resultado[2];
			$horaIngresoArticulo = $resultado[3];
			
			if($codigoArticulo != ''){
				//TODO::::Debug
//				mensajeEmergente("Moviendo ".$codigoArticulo);
				
				//Actualiza el saldo modificado
				actualizar_saldo_medicamento_modificado($whistoria, $wingreso, $codigoArticulo, $cantidadArticulo, 0, "Recibo", $servicioOrigen, $servicioActual);

				if($aplicacionCcoOrigen == 'on' && $aplicacionCcoActual == 'on'){				
					//Si ambos servicios son de aplicación automática NO SE DESACTIVAN las aplicaciones
				} else {
					inactivar_aplicacion_medicamento_desde_detalle_recibo($whistoria, $wingreso, $consecutivoTraslado, $codigoArticulo, $cantidadArticulo, $fechaIngresoArticulo, $horaIngresoArticulo);
				}
				
				//Anula el detalle
				anular_medicamento_detalle($consecutivoTraslado, $codigoArticulo);
			}
			$cont1++;
		}
	}
}

/**
 * Modifica el centro de costos registrado en una aplicación de un articulo, tiene en cuenta la fecha y hora de la aplicación para realizar 
 * la modificación.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $codigoArticulo
 * @param unknown_type $servicioActual
 * @param unknown_type $servicioNuevo
 * @param unknown_type $fechaIngresoArticulo
 * @param unknown_type $horaIngresoArticulo
 */
function modificar_centro_costos_aplicacion($whistoria, $wingreso, $codigoArticulo, $servicioActual, $servicioNuevo, $fechaIngresoArticulo, $horaIngresoArticulo){
	global $wbasedato;
	global $conex;
	
	$q = "UPDATE
			".$wbasedato."_000015
		SET
			Aplcco = '".$servicioNuevo."'
		WHERE 
			Aplhis = '".$whistoria."'
			AND Apling = '".$wingreso."'
			AND Aplart = '".$codigoArticulo."'
			AND Aplcco = '".$servicioActual."'
			AND Fecha_data = '".$fechaIngresoArticulo."'
			AND Hora_data = '".$horaIngresoArticulo."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * PROCESO:: Proceso de movimiento de saldos para recibo en el cambio destino, es una variación del proceso normal de devolucion de movimientos
 * de saldos.  Este es el caso 8 para el cambio de destino (Ver documentacion).
 * 
 * Cuando se realizó el proceso de recibo del servicio NAA a AA ya se movieron los saldos de articulos de la tabla 04 - 30 y fueron aplicados,
 * con la misma fecha data y hora data del detalle de recibo, con estos dos campos se relacionarán efectivamente las aplicaciones a las que
 * se les deben modificar el codigo del centro de costos.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $servicioOrigen
 * @param unknown_type $servicioActual
 */
function proceso_movimiento_saldos_med_recibido_caso8($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigen, $servicioActual, $servicioNuevo){
	global $wbasedato;
	global $conex;
	
	//Consulta del material en el detalle de recibo
	$q = "SELECT 
			Detart, Detcan, Fecha_data, Hora_data 
		 FROM 
			".$wbasedato."_000019
		 WHERE
		 	Detest = 'on'
			AND Detnum = '".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if ($num > 0)
	{
		$cont1 = 0;
		while($cont1 <= $num){
			$resultado = mysql_fetch_array($res);

			$codigoArticulo = $resultado[0];
			$cantidadArticulo = $resultado[1];
			$fechaIngresoArticulo = $resultado[2];
			$horaIngresoArticulo = $resultado[3];
			
			if($codigoArticulo != ''){
				//TODO::::Debug
//				mensajeEmergente("Cambiando centro costos: ".$codigoArticulo." este: ".$servicioActual." por este: ".$servicioNuevo);
				
				//Modifica el centro de costos de las aplicaciones registradas en el centro de costos anterior por el nuevo destino
				modificar_centro_costos_aplicacion($whistoria, $wingreso, $codigoArticulo, $servicioActual, $servicioNuevo, $fechaIngresoArticulo, $horaIngresoArticulo);
			}
			$cont1++;
		}
	}
}

/**
 * Valida la existencia de un centro de costos retornando el nombre del centro de costos.
 *
 * @param unknown_type $wccoIngreso
 * @return nombre centro de costos
 */
function nombre_centro_costos($wccoIngreso){
	global $wbasedato;
	global $conex;	

	$q="SELECT 
			Cconom 
		FROM
			".$wbasedato."_000011  
		WHERE
			Ccocod = '".$wccoIngreso."';";
	
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$existe = "";
	if($num > 0){
		$fila = mysql_fetch_array($res);
		$existe = $fila[0];
	}
	return $existe;
}

/**
 * Consulta si un centro de costos es hospitalario
 *
 * @param unknown_type $codigoCco
 * @return es hospitalario
 */
function es_hospitalario_centro_costos($codigoCco){
	global $wbasedato;
	global $conex;	

	$q = "SELECT 
			Ccohos 
		FROM
			".$wbasedato."_000011  
		WHERE
			Ccocod = '".$wccoIngreso."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$es = false;
	if($num > 0){
		$fila = mysql_fetch_array($res);
		
		if($fila[0] == 'on'){
			$es = true;
		}
	}
	return $es;
}

/**
 * Retorna el codigo del centro de costos ingresado llevandolo de la forma UN.XXXX a XXXX
 *
 * @param unknown_type $cco
 * @return codigo centro de costos
 */
function retornar_codigo_centro_costos($cco){
	$ccoTemp = "";
	
	if (strpos($cco,"-") > 0){
		$wccosto=explode("-",$cco);
		$ccoTemp=$wccosto[0];
	}
	
	if (strpos($cco,".") > 0){
		$wccosto=explode(".",$cco);
		$ccoTemp=$wccosto[1];
	}
	return $ccoTemp;
}

/**
 * Valida que el centro de costos ingresado, sea el centro de costos anterior, usado para permitir los cambios en la anulación, destino y 
 * lista de medicamentos.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $cco
 * @return unknown
 */
function validar_cambios_traslado_centro_costos($whistoria, $wingreso, $cco){
	global $wbasedato;
	global $conex;	

	$existe = false; 
	
	$q = "SELECT 
			Ccocod 
		FROM
			".$wbasedato."_000011, ".$wbasedato."_000018  
		WHERE
			Ubisan = Ccocod 
			AND Ccocod = ".$cco."
			AND Ubihis = ".$whistoria."
			AND Ubiing = ".$wingreso."			
		;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){
		$fila = mysql_fetch_array($res);
		
		if($fila[0] == $cco){
			$existe = true;
		} 
	}
	return $existe;
}

/**
 * Consulta el valor máximo posible a modificar para un medicamento en particular en la lista de medicamentos
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $codArticulo
 * @param unknown_type $ccoOrigen
 * @param unknown_type $ccoActual
 * @return cantidad maxima medicamento
 */
function maxima_cantidad_material_modificar($whistoria, $wingreso, $codArticulo, $ccoOrigen){
	global $wbasedato;
	global $conex;	

	//Consulta el ultimo centro de costos que modificó la lista de la 30 y la 4. Ya que se puede presentar que un mismo articulo sea cargado/aplicado por centros de costos diferentes
	$ccoSaldosAplicado = consultar_ultimo_cco_modifica_saldos_app($whistoria, $wingreso, $codArticulo);
	$ccoSaldosPaciente = consultar_ultimo_cco_modifica_saldos_pac($whistoria, $wingreso, $codArticulo);
	
	$cant = 0;
	
	//Saldo de la tabla 30, origen AA
	$q="SELECT 
			(Spluen - Splusa) saldoApl
		FROM
			".$wbasedato."_000030
		WHERE	
			Splhis='".$whistoria."' 
			AND Spling='".$wingreso."' 
			AND Splcco='".$ccoOrigen."'
			AND Splart='".$codArticulo."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$fila = array();
	if($num > 0){
		$fila = mysql_fetch_array($res);
		$cant = $fila[0];
	} else {
		//Saldo de la tabla 30, origen AA
		$q="SELECT
			(Spluen - Splusa) saldoApl
		FROM
			".$wbasedato."_000030
		WHERE	
			Splhis='".$whistoria."' 
			AND Spling='".$wingreso."' 
			AND Splcco='".$ccoSaldosAplicado."'
			AND Splart='".$codArticulo."';";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		if($num > 0){
			$fila = mysql_fetch_array($res);
			$cant = $fila[0];
		}
	}
	return $cant;
}

/**
 * Consulta el nombre de un articulo dado su codigo, la busqueda se realiza en las tablas de central de mezclas y en las de movimiento
 * hospitalario.
 *
 * @param unknown_type $codArticulo
 * @return unknown
 */
function consultar_nombre_articulo($codArticulo){
	global $wbasedato;
	global $wcenmez;
	global $conex;
	$row = array();
	
	//Nombre del articulo en el maestro de articulos de movhos
	$q = "SELECT 
				artcom, artuni, unides
			FROM 
				".$wbasedato."_000026, ".$wbasedato."_000027
			WHERE
				artuni = unicod 
				AND artcod = '".$codArticulo."' ;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
	}
	else
	{
		//Nombre del articulo en la base de datos de central de mezclas
		$q = "SELECT 
					artcom, artuni, unides
				FROM 
					".$wcenmez."_000002, ".$wbasedato."_000027
				WHERE 
					artuni = unicod 
					AND artcod = '".$codArticulo."';";
		
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		if ($num > 0)
		{
			$row = mysql_fetch_array($res);
		}
		else
		{
			//Nombre del articulo en la base de datos de central de 'movhos', pero buscando con el codigo del proveedor en la tabla movhos_000009
			$codArticulo = BARCOD($codArticulo);
			
			$q = "SELECT 
						artcom, artuni, unides, ".$wbasedato."_000009.artcod
					FROM 
						".$wbasedato."_000009, ".$wbasedato."_000026, ".$wbasedato."_000027
					WHERE 
						artcba='".$codArticulo."'
						AND ".$wbasedato."_000009.artcod = ".$wbasedato."_000026.artcod
						AND artuni=unicod;";

			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
			}
		}
	}
	return $row;
}

/**
 * Consulta a partir del detalle de entrega y recibo en forma de tabla el listado y la cantidad de medicamentos que es posible modificar 
 * en la lista de medicamentos.
 *
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consulta
 * @param unknown_type $ccoOrigen
 * @param unknown_type $ccoActual
 */
function generar_tabla_lista_materiales($consecutivoTraslado, $whistoria, $wingreso, $consulta, $ccoOrigen, $ccoActual){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			det.Detart, art.Artcom, art.Artgen, det.Detcan, det.Detest  
		FROM
			".$wbasedato."_000019 det
		LEFT JOIN
			".$wbasedato."_000026 art
		ON 
			det.Detart = art.Artcod
		WHERE	
			det.Detest = 'on'
			AND det.Detnum = '".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		
		echo "<table>";
		
		//Encabezado
		echo "<tr class=encabezadoTabla>";
		echo "<td width='100' align='center'>";
		echo "Código";
		echo "</td>";
		echo "<td width='400' align='center'>";
		echo "Descripción";
		echo "</td>";
		echo "<td width='100' align='center'>";
		echo "Cantidad";
		echo "</td>";
		if($consulta == false){
			echo "<td width='100' align='center'>";
			echo "Modificación";
			echo "</td>";
			echo "<td width='80' align='center' >";
			echo "Máximo";
			echo "</td>";
			echo "<td width='80' align='center'>";
			echo "Eliminar";
			echo "</td>";
		}
		echo "</tr>";
		
		//Cuerpo de la tabla
		$cont1 = 1;
		while($cont1 <= $num){
			if($row[1] != ''){
				$nombreArticulo = $row[1];	
			} else {
				$infoArt = consultar_nombre_articulo($row[0]);
				$nombreArticulo = $infoArt[0];
			}
			 			
			$clase = "fila1";
			
			if(($cont1 % 2) == 0){
				$clase = "fila2";
			}
			
			echo "<tr class=$clase>";
			echo "<td align='center'>";			 
			echo $row[0];
			echo "</td>";
			echo "<td align='center'>";
			echo $nombreArticulo;
			echo "</td>";
			echo "<td align='center'>";
			echo $row[3];
			echo "</td>";
			if($consulta == false){
				echo "<input type='hidden' name='medicamentos[".$cont1."]' value='".$row[0]."'>";
				echo "<input type='hidden' name='actual[".$row[0]."]' value='".$row[3]."'>";
				$cantMaxima = maxima_cantidad_material_modificar($whistoria, $wingreso, $row[0], $ccoOrigen);
				$parametros = "\"".$row[0]."\",\"".$cantMaxima."\",\"".$row[3]."\"";
				echo "<td align='center'>";
				echo "<input type='text' id='tx".$row[0]."' name='cantidades[".$row[0]."]' size='2' maxlength='4' onblur='javascript:validarCantidadMaxima(".$parametros.");'>";
				echo "</td>";
				echo "<td align='center'>";
				echo $cantMaxima;
				echo "</td>";
				echo "<td align='center'>";
				echo "<input type='checkbox' id='ch".$row[0]."' name='eliminar[".$row[0]."]' onclick='javascript:alternarEliminar("."\"".$row[0]."\"".")'>";
				echo "</td>";
			}
			echo "</tr>";
			
			$row = mysql_fetch_array($res);
			$cont1++;
		}
		echo "</table>";
	}
}

/**
 * Modifica el valor de la cantidad de medicamento en el detalle de entrega y recibo.
 *
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdMedicamento
 * @param unknown_type $saldoNuevo
 */
function actualizar_cantidad_detalle($consecutivoTraslado, $cdMedicamento, $saldoNuevo){
	global $wbasedato;
	global $conex;
	
	$q = "UPDATE  
			".$wbasedato."_000019
		SET
			Detcan = '".$saldoNuevo."'
		WHERE	
			Detart = '".$cdMedicamento."'  
			AND Detnum = '".$consecutivoTraslado."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * Verifica si un medicamento ya se encuentra insertado en la tabla de saldos por paciente (4) esto con el fin de saber si se crea un nuevo
 * registro (insert) o se realiza un update.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $cdMedicamento
 * @return existe material
 */
function existe_material_en_saldo_paciente($whistoria, $wingreso, $cdMedicamento, $servicioOrigenTraslado){
	global $wbasedato;
	global $conex;	

	$existe = false; 
	
	$q = "SELECT 
			Spaart 
		FROM
			".$wbasedato."_000004  
		WHERE
			Spahis = '".$whistoria."'
			AND Spaing = '".$wingreso."'
			AND Spaart = '".$cdMedicamento."'
			AND Spacco = '".$servicioOrigenTraslado."'
		;";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	//TODO: verificacion existencia saldos en la tabla 4 dado un servicio
//	mensajeEmergente("consulta existe saldo paciente: ".$q);
	
	if($num > 0){
		$fila = mysql_fetch_array($res);
		
		if($fila[0] == $cdMedicamento){
			$existe = true;
		}
	}
	return $existe;
}

/**
 * Actualiza el saldo del medicamento que se actualiza de la lista, realizando los movimientos de la 30 a la 4.
 * 
 * Se deben tener en cuenta todos los posibles casos tanto cuando el saldo es mayor, igual o menor como para el tipo de aplicación de los
 * centros de costos de origen y actual.
 * 
 * NAA: El servicio (centro de costos) no aplica automáticamente
 * AA:  El servicio (centro de costos) aplica automáticamente
 * 
 * -->Caso 1: NAA - AA (Entrega)
 * 
 * -->Caso 2: NAA - AA (Recibo)
 * La cantidad de material (19) se resta en las entradas de la (30) y se suma en las entradas de la (04)
 *
 * -->Caso 3: NAA - NAA (Entrega)
 * 
 * -->Caso 4: NAA - NAA (Recibo)
 * 
 * -->Caso 5: AA - NAA (Entrega)
 * La cantidad de material (19) se suma en las entradas de la (30) y se resta en las entradas de la (04)
 * 
 * -->Caso 6: AA - NAA (Recibo)
 * 
 * -->Caso 7: AA - AA (Entrega)
 * 
 * -->Caso 8: AA - AA (Recibo)
 * 
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $cdMedicamento
 * @param unknown_type $saldoActual
 * @param unknown_type $saldoNuevo
 * @param unknown_type $tipoUltimoCambio
 */
function actualizar_saldo_medicamento_modificado($whistoria, $wingreso, $cdMedicamento, $saldoActual, $saldoNuevo, $tipoUltimoCambio, $servicioOrigen, $servicioActual){
	global $wbasedato;
	global $conex;
	
	//Consulta el ultimo centro de costos que modificó la lista de la 30 y la 4. 
	//Ya que se puede presentar que un mismo articulo sea cargado/aplicado por centros de costos diferentes
	$ccoSaldosAplicado = consultar_ultimo_cco_modifica_saldos_app($whistoria, $wingreso, $cdMedicamento);
	$ccoSaldosPaciente = consultar_ultimo_cco_modifica_saldos_pac($whistoria, $wingreso, $cdMedicamento);

	//Flags para controlar la modificación de entradas de aprovechamiento
	$modificarAprovSaldoApp = permite_modificar_aprovechamiento_saldo_app($whistoria, $wingreso, $ccoSaldosAplicado, $cdMedicamento);
	$modificarAprovSaldoPac = permite_modificar_aprovechamiento_saldo_pac($whistoria, $wingreso, $ccoSaldosPaciente, $cdMedicamento);
	
	//Consulta el tipo de aplicación de los centros de costos origen y actual
	$aplicacionCcoOrigen = consultar_aplicacion_automatica_cco($servicioOrigen);
	$aplicacionCcoActual = consultar_aplicacion_automatica_cco($servicioActual);
	
	/*
	 * Este flag indicará si se realiza movimiento de las siguientes formas: 
	 * TRUE: Restar de la 30 sumando a la 4 el detalle de la 19
	 * FALSE: Sumar a la 30 restando de la 4 el detalle de la 19 ->Por defecto para modificacion lista de medicamentos
	 */
	$moverDeAplicadoaPaciente=false;
	
	//El tipo de cambio solo es de Entrega y Recibo
	if($tipoUltimoCambio == 'Entrega'){
		/*
		 * Por el momento los siguientes casos no realizan movimientos de saldos en caso de requerir mas agregar la condición:
		 * Caso 1: NAA - AA (Entrega)
		 * Caso 3: NAA - NAA (Entrega)
		 * Caso 7: AA - AA (Entrega)
		 */
		
		//Caso 5: AA - NAA (Entrega)
		if($aplicacionCcoOrigen == 'on' && $aplicacionCcoActual == 'off'){
			$moverDeAplicadoaPaciente = false;
		}
	} else {
		/*
		 * Por el momento los siguientes casos no realizan movimientos de saldos en caso de requerir mas agregar la condición:
		 * Caso 4: NAA - NAA (Recibo) 
		 * Caso 8: AA - AA (Recibo)
		 */
		//Caso 6: AA - NAA (Recibo): Tener en cuenta que en el proceso de recibo como tal no hay movimiento de saldos PERO como previo a esto 
		//existió una entrega como la del Caso 5, se deben devolver los saldos.
		if($aplicacionCcoOrigen == 'on' && $aplicacionCcoActual == 'off'){
			$moverDeAplicadoaPaciente = false;
		}		
		
		//Caso 2: NAA - AA (Recibo): La cantidad de material (19) se resta en las entradas de la (30) y se suma en las entradas de la (04)
		if($aplicacionCcoOrigen == 'off' && $aplicacionCcoActual == 'on'){
			$moverDeAplicadoaPaciente = true; 	
		}
	}
	
	if($moverDeAplicadoaPaciente == true){
		$saldo = $saldoActual-$saldoNuevo;			
		
		//Baja las 'entradas' de la tabla 30
		$q = "UPDATE
				".$wbasedato."_000030
			SET
				Spluen = Spluen-".$saldo."
			WHERE	
				Splhis = '".$whistoria."'
				AND Spling = '".$wingreso."' 
				AND Splart = '".$cdMedicamento."'
				AND Splcco = '".$ccoSaldosAplicado."';";
		
		/* Si el valor de la entrada es menor igual a la entrada aprovechamiento se modifica tambien el valor del aprovechamiento,
		 * el query se cambia por el siguiente
		 */
		if($modificarAprovSaldoApp == true){
			//Baja las 'entradas' de la tabla 30 teniendo en cuenta la disminucion en 'entradas' aprovechamientos
			$q = "UPDATE
					".$wbasedato."_000030
				SET
					Spluen = Spluen-".$saldo.",
					Splaen = Splaen-".$saldo."
				WHERE	
					Splhis = '".$whistoria."'
					AND Spling = '".$wingreso."' 
					AND Splart = '".$cdMedicamento."'
					AND Splcco = '".$ccoSaldosAplicado."';";
		}
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		$cantidad = $saldo;
		
		//TODO: DEBUG:
//		mensajeEmergente("mueve de 30 a 4, la cantidad: ".$saldo."Aplicaciones: ".$aplicacionCcoOrigen."-".$aplicacionCcoActual."Cco30".$ccoSaldosAplicado."Cco4".$ccoSaldosPaciente);
		
		//Verifica que en la tabla de saldos paciente (4) existe el registro para el material especificado, caso contrario se crea/inserta un nuevo registro
		$servicioActualizar = $ccoSaldosAplicado;
		if($servicioActualizar == ''){
			$servicioActualizar = $ccoSaldosPaciente;
		}
		if(existe_material_en_saldo_paciente($whistoria, $wingreso, $cdMedicamento, $servicioActualizar)){
			//Sube las 'entradas' de la tabla 4
			$q1 = "UPDATE
				".$wbasedato."_000004
			SET
				Spauen = Spauen+".$cantidad."
			WHERE	
				Spahis = '".$whistoria."'
				AND Spaing = '".$wingreso."' 
				AND Spaart = '".$cdMedicamento."'
				AND Spacco = '".$servicioActualizar."';";
			
			/* Si el valor de unidades en la entrada es <= al saldo de aprovechamiento, se modifica tambien el aprovechamiento
			 * se modifica el query
			 */
			if($modificarAprovSaldoPac == true){
				$q1 = "UPDATE
						".$wbasedato."_000004
					SET
						Spauen = Spauen+".$cantidad.",
						Spaaen = Spaaen+".$cantidad."
					WHERE	
						Spahis = '".$whistoria."'
						AND Spaing = '".$wingreso."' 
						AND Spaart = '".$cdMedicamento."'
						AND Spacco = '".$servicioActualizar."';";
			}
			
			$res = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
		} else {
			//Crea el registro en los saldos de la tabla 4, con la cantidad necesaria
			$q1 = "INSERT INTO
						".$wbasedato."_000004 (medico, Fecha_data, Hora_data, Spahis, Spaing, Spacco, Spaart, Spauen, Spausa, Spaaen, Spaasa, Seguridad) 
				(
					SELECT 
						'movhos', '".date("Y-m-d")."', '".(string)date("H:i:s")."', Splhis, Spling, Splcco, Splart, ".$cantidad.", 0, Splaen, Splasa, Seguridad 
					FROM 
						".$wbasedato."_000030
					WHERE
						Splhis = '".$whistoria."'
						AND Spling = '".$wingreso."'
						AND Splart = '".$cdMedicamento."'
						AND Splcco = '".$servicioActualizar."'		
				);";
			$res = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());						
		}
	} else {
		/*
		 * Para casos en los que el origen aplica automaticamente se debe tener en cuenta que ahora se pueden agregar articulos del mismo
		 * servicio origen, de tal forma que se debe verificar a cual centro de costos corresponde el movimiento de saldos de los siguientes:
		 * -Cco de la tabla 30
		 * -Cco de la tabla 04
		 * -Cco origen
		 *  
		 * (Junio 3/2008)
		 */		
		$saldo = $saldoActual-$saldoNuevo;
		$servicioActualizar = $ccoSaldosPaciente;
		
		if(!existe_material_en_saldo_paciente($whistoria, $wingreso, $cdMedicamento, $servicioActualizar)){
			if(existe_material_en_saldo_paciente($whistoria, $wingreso, $cdMedicamento, $ccoSaldosAplicado)){
				$servicioActualizar = $ccoSaldosAplicado;		
			} else {
				$servicioActualizar = $servicioOrigen;
			}
		}
		
		
		//Sube las 'salidas' de la tabla 30
		$q = "UPDATE  
				".$wbasedato."_000030
			SET
				Spluen = Spluen+".$saldo."
			WHERE	
				Splhis = '".$whistoria."'
				AND Spling = '".$wingreso."' 
				AND Splart = '".$cdMedicamento."'
				AND Splcco = '".$servicioActualizar."';";
		
		/* Si el valor de la entrada es menor igual a la entrada aprovechamiento se modifica tambien el valor del aprovechamiento,
		 * el query se cambia
		 */
		if($modificarAprovSaldoApp == true){
			$q = "UPDATE  
					".$wbasedato."_000030
				SET
					Spluen = Spluen+".$saldo.",
					Splaen = Splaen+".$saldo."
				WHERE	
					Splhis = '".$whistoria."'
					AND Spling = '".$wingreso."' 
					AND Splart = '".$cdMedicamento."'
					AND Splcco = '".$servicioActualizar."';";
		}
		
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$registrosActualizados = mysql_affected_rows();
		
		$cantidad = $saldo;
		
		//TODO DEBUG:
//		mensajeEmergente("mueve de 4 a 30, la cantidad: ".$saldo."Aplicaciones: ".$aplicacionCcoOrigen."-".$aplicacionCcoActual."Cco30".$ccoSaldosAplicado."Cco4".$ccoSaldosPaciente);
		
		if(existe_material_en_saldo_paciente($whistoria, $wingreso, $cdMedicamento, $servicioActualizar)){
			//Baja 'entradas' de la tabla 4
			$q1 = "UPDATE
				".$wbasedato."_000004
			SET
				Spauen = Spauen-".$saldo."
			WHERE	
				Spahis = '".$whistoria."'
				AND Spaing = '".$wingreso."' 
				AND Spaart = '".$cdMedicamento."'
				AND Spacco = '".$servicioActualizar."';";
			
			/* Si el valor de unidades en la entrada es <= al saldo de aprovechamiento, se modifica tambien el aprovechamiento
			 * se modifica el query
			 */
			if($modificarAprovSaldoPac == true){
				//Baja 'entradas' de la tabla 4
				$q1 = "UPDATE
						".$wbasedato."_000004
					SET
						Spauen = Spauen-".$saldo.",
						Spaaen = Spaaen-".$cantidad."
					WHERE	
						Spahis = '".$whistoria."'
						AND Spaing = '".$wingreso."' 
						AND Spaart = '".$cdMedicamento."'
						AND Spacco = '".$servicioActualizar."';";
			}
			
			$res = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
		} else {
			//Crea el registro en los saldos de la tabla 4, con la cantidad necesaria
			$q1 = "INSERT INTO
						".$wbasedato."_000004 (medico, Fecha_data, Hora_data, Spahis, Spaing, Spacco, Spaart, Spauen, Spausa, Spaaen, Spaasa, Seguridad) 
				(
					SELECT 
						'movhos', '".date("Y-m-d")."', '".(string)date("H:i:s")."', Splhis, Spling, Splcco, Splart, ".$cantidad.", 0, Splaen, Splasa, Seguridad 
					FROM 
						".$wbasedato."_000030
					WHERE
						Splhis = '".$whistoria."'
						AND Spling = '".$wingreso."'
						AND Splart = '".$cdMedicamento."'
						AND Splcco = '".$servicioActualizar."'							
				);";

			$res = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
		}
	}
}

/**
 * Modifica la cantidad de aplicaciones de un medicamento para un traslado particular
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $cdMedicamento
 * @param unknown_type $fechaTraslado
 * @param unknown_type $horaTraslado
 * @param unknown_type $servicio
 * @param unknown_type $saldo
 */
function modificar_cantidad_aplicaciones($whistoria, $wingreso, $cdMedicamento, $fechaTraslado, $horaTraslado, $servicio, $saldo){
	global $wbasedato;
	global $conex;
	
	$q = "UPDATE  
			".$wbasedato."_000015
		SET
			Aplcan = '".$saldo."'
		WHERE	
			Fecha_data = '".$fechaTraslado."' 
			AND Hora_data = '".$horaTraslado."'
			AND Aplhis = '".$whistoria."'  
			AND Apling = '".$wingreso."'  
			AND Aplart = '".$cdMedicamento."' 
			AND Aplcco = '".$servicio."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * PROCESO: Actualiza el saldo de material de la lista de medicamentos.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdMedicamento
 * @param unknown_type $saldoActual
 * @param unknown_type $saldoNuevo
 * @param unknown_type $tipoUltimoCambio
 * @param unknown_type $servicioOrigen
 * @param unknown_type $servicioActual
 */
function actualizar_saldo_medicamento_lista($whistoria, $wingreso, $consecutivoTraslado, $cdMedicamento, $saldoActual, $saldoNuevo, $tipoUltimoCambio, $servicioOrigen, $servicioActual){
	global $wbasedato;
	global $conex;
	
	$saldo = $saldoActual-$saldoNuevo;	
	$servicio = $servicioOrigen;
	
	//Cantidad de aprovechamiento antes del update para la tablas 30 y 4
	$cantidadAprovechamientoApl = consultar_cantidad_aprovechamiento_saldo_app($whistoria, $wingreso, $servicio, $cdMedicamento);
	$cantidadAprovechamientoPac = consultar_cantidad_aprovechamiento_saldo_pac($whistoria, $wingreso, $servicio, $cdMedicamento);
	
	$cantidadAprovechamiento = 0;
	
	if($saldo > 0){
		$cantidadAprovechamiento = $cantidadAprovechamientoPac; 
	} else {
		$cantidadAprovechamiento = $cantidadAprovechamientoApl;
	}
	
	//Sube las 'salidas' de la tabla 30
	$q = "UPDATE
			".$wbasedato."_000030
			SET
				Spluen = Spluen+".$saldo."
			WHERE	
				Splhis = '".$whistoria."'
				AND Spling = '".$wingreso."' 
				AND Splart = '".$cdMedicamento."'
				AND Splcco = '".$servicio."';";

	/* Si el valor de la entrada es menor igual a la entrada aprovechamiento se modifica tambien el valor del aprovechamiento,
	 * el query se cambia
	 */
	if($cantidadAprovechamiento > 0){
		$q = "UPDATE
				".$wbasedato."_000030
			SET
					Spluen = Spluen+".$saldo.",
					Splaen = Splaen+".$cantidadAprovechamiento."
				WHERE	
					Splhis = '".$whistoria."'
					AND Spling = '".$wingreso."' 
					AND Splart = '".$cdMedicamento."'
					AND Splcco = '".$servicio."';";
	}
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//El servicio que modifica tambien puede ser el piso que efectuó la entrega
	$numModif = mysql_affected_rows();
	if($numModif == 0){
		
		//Consulta el ultimo centro de costos que modificó la lista de la 30 y la 4. 
		//Ya que se puede presentar que un mismo articulo sea cargado/aplicado por centros de costos diferentes
		$ccoSaldosAplicado = consultar_ultimo_cco_modifica_saldos_app($whistoria, $wingreso, $cdMedicamento);
		$ccoSaldosPaciente = consultar_ultimo_cco_modifica_saldos_pac($whistoria, $wingreso, $cdMedicamento);
		
		$servicio = $ccoSaldosPaciente;
		if($saldoNuevo > $saldoActual){
			$servicio = $ccoSaldosAplicado;
		}
		
		//Cantidad de aprovechamiento antes del update para la tablas 30 y 4
		$cantidadAprovechamientoApl = consultar_cantidad_aprovechamiento_saldo_app($whistoria, $wingreso, $servicio, $cdMedicamento);
		$cantidadAprovechamientoPac = consultar_cantidad_aprovechamiento_saldo_pac($whistoria, $wingreso, $servicio, $cdMedicamento);
	
		if($saldo > 0){
			$cantidadAprovechamiento = $cantidadAprovechamientoPac;
		} else {
			$cantidadAprovechamiento = $cantidadAprovechamientoApl;
		}
		
		$q = "UPDATE
			".$wbasedato."_000030
			SET
				Spluen = Spluen+".$saldo."
			WHERE	
				Splhis = '".$whistoria."'
				AND Spling = '".$wingreso."' 
				AND Splart = '".$cdMedicamento."'
				AND Splcco = '".$servicio."';";

		/* Si el valor de la entrada es menor igual a la entrada aprovechamiento se modifica tambien el valor del aprovechamiento,
		 * el query se cambia
		 */
		if($cantidadAprovechamiento > 0){
			$q = "UPDATE
				".$wbasedato."_000030
			SET
					Spluen = Spluen+".$saldo.",
					Splaen = Splaen+".$cantidadAprovechamiento."
				WHERE	
					Splhis = '".$whistoria."'
					AND Spling = '".$wingreso."' 
					AND Splart = '".$cdMedicamento."'
					AND Splcco = '".$servicio."';";
		}
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	//Baja 'entradas' de la tabla 4
	$q1 = "UPDATE
				".$wbasedato."_000004
			SET
				Spauen = Spauen-".$saldo."
			WHERE	
				Spahis = '".$whistoria."'
				AND Spaing = '".$wingreso."' 
				AND Spaart = '".$cdMedicamento."'
				AND Spacco = '".$servicio."';";
		
	/* Si el valor de unidades en la entrada es <= al saldo de aprovechamiento, se modifica tambien el aprovechamiento
	 * se modifica el query
	 */
	if($cantidadAprovechamiento > 0){
		//Baja 'entradas' de la tabla 4
		$q1 = "UPDATE
						".$wbasedato."_000004
					SET
						Spauen = Spauen-".$saldo.",
						Spaaen = Spaaen-".$cantidadAprovechamiento."
					WHERE	
						Spahis = '".$whistoria."'
						AND Spaing = '".$wingreso."' 
						AND Spaart = '".$cdMedicamento."'
						AND Spacco = '".$servicio."';";
	}

	$res = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
}

/**
 * Valida si se mueven los saldos de aprovechamiento de la tabla 30, se modifican las entradas de aprovechamiento si:
 * entrada <= entrada aprovechamiento
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $ccoSaldosAplicado
 * @param unknown_type $cdMedicamento
 * @return modifica aprovechamiento
 */
function permite_modificar_aprovechamiento_saldo_app($whistoria, $wingreso, $servicio, $cdMedicamento){
	global $wbasedato;
	global $conex;	

	$q = "SELECT 
			Spluen, Splaen 
		FROM
			".$wbasedato."_000030  
		WHERE
			Splhis = '".$whistoria."'
			AND Spling = '".$wingreso."'
			AND Splart = '".$cdMedicamento."'
			AND Splcco = '".$servicio."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$permite = false;
	if($num > 0){
		$fila = mysql_fetch_array($res);
		
		if($fila[0] <= $fila[1] && $fila[1] != 0){
			$permite = true;
		}
	}
	return $permite;
}

/**
 * Valida si se debe realizar movimiento de las entradas de aprovechamiento en la tabla 04
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $servicio
 * @param unknown_type $cdMedicamento
 * @return permite modificar aprovechamiento
 */
function permite_modificar_aprovechamiento_saldo_pac($whistoria, $wingreso, $servicio, $cdMedicamento){
	global $wbasedato;
	global $conex;	

	$q = "SELECT 
			Spauen, Spaaen 
		FROM
			".$wbasedato."_000004  
		WHERE
			Spahis = '".$whistoria."'
			AND Spaing = '".$wingreso."'
			AND Spaart = '".$cdMedicamento."'
			AND Spacco = '".$servicio."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$permite = false;
	if($num > 0){
		$fila = mysql_fetch_array($res);
		
		if($fila[0] <= $fila[1] && $fila[1] != 0){
			$permite = true;
		}
	}
	return $permite;
}

/**
 * PROCESO:: Proceso que modifica la cantidad de saldo de un medicamento, aplica unicamente cuando el servicio origen tiene aplicación
 * automática
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdMedicamento
 * @param unknown_type $saldoActual
 * @param unknown_type $saldoNuevo
 * @param unknown_type $aplOrigen
 * @param unknown_type $aplActual
 * @param unknown_type $tipoUltimoCambio
 */
function proceso_modificar_cantidad_saldo_medicamento($whistoria, $wingreso, $consecutivoTraslado, $cdMedicamento, $saldoActual, $saldoNuevo, $aplOrigen, $aplActual, $tipoUltimoCambio, $servicioOrigen, $servicioActual){
	if($aplOrigen == "on"){
		//Modifica la cantidad de saldo del detalle.
		actualizar_cantidad_detalle($consecutivoTraslado, $cdMedicamento, $saldoNuevo);

		if($aplActual == "off"){
			//Si el actual no aplica automaticamente, se modifican los saldos de detalle de entrega y mueve saldos entre la 4 y 30
			actualizar_saldo_medicamento_lista($whistoria, $wingreso, $consecutivoTraslado, $cdMedicamento, $saldoActual, $saldoNuevo, $tipoUltimoCambio, $servicioOrigen, $servicioActual);		
		}
	}
}

/**
 * Anula un medicamento en particular del detalle de entrega y recibo.
 *
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdMedicamento
 */
function anular_medicamento_detalle($consecutivoTraslado, $cdMedicamento){
	global $wbasedato;
	global $conex;
	
	$q = "UPDATE  
				".$wbasedato."_000019
			SET
				Detest = 'off'
			WHERE	
				Detnum = '".$consecutivoTraslado."'
				AND Detart = '".$cdMedicamento."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * PROCESO:: Proceso de eliminación del saldo de un medicamento desde la lista de medicamentos.
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdMedicamento
 * @param unknown_type $saldoActual
 * @param unknown_type $aplOrigen
 * @param unknown_type $aplActual
 * @param unknown_type $tipoUltimoCambio
 */
function proceso_eliminar_saldo_medicamento($whistoria, $wingreso, $consecutivoTraslado, $cdMedicamento, $saldoActual, $aplOrigen, $aplActual, $tipoUltimoCambio, $servicioOrigen, $servicioActual){
	if($aplOrigen == "on"){
		//Si ambos origen y actual aplican automáticamente, se modifica el saldo en el detalle de entrega (19)
		anular_medicamento_detalle($consecutivoTraslado, $cdMedicamento);
			
		//Si el actual no aplica automaticamente, se modifican los saldos de detalle de entrega y mueve saldos entre la 4 y 30, con nuevo saldo cero
		actualizar_saldo_medicamento_lista($whistoria, $wingreso, $consecutivoTraslado, $cdMedicamento, $saldoActual, 0, $tipoUltimoCambio, $servicioOrigen, $servicioActual);
	}
}

/**
 * Consulta la información de la institución dado el codigo de la empresa en la Promotora
 *
 * @param unknown_type $wemp_pmla
 * @return unknown
 */
function consultar_informacion_institucion($wemp_pmla){
	global $wbasedato;
	global $conex;
	
	$q = " SELECT 
				detapl, detval, empdes
			FROM 
				root_000050, root_000051
			WHERE 
				empcod = '".$wemp_pmla."'
				AND empest = 'on'
				AND empcod = detemp;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);

			if ($row[0] == "cenmez")
			$wcenmez = $row[1];

			if ($row[0] == "afinidad")
			$wafinidad = $row[1];

			if ($row[0] == "movhos")
			$wbasedato = $row[1];

			if ($row[0] == "tabcco")
			$wtabcco = $row[1];
            
            if ($row[0] == "camilleros")
			$wcencam = $row[1];

			$winstitucion=$row[2];
		}
	}
	
	$info[0] = $wcenmez;
	$info[1] = $wafinidad;
	$info[2] = $wbasedato;
	$info[3] = $wtabcco;
	$info[4] = $winstitucion;
    $info[5] = $wcencam;
	
	return $info;
}

/**
 * Consulta la cantidad de articulos del detalle de entrega y recibo que han sido aplicados posterior a la fecha y la hora del traslado señalado
 * por el consecutivo en el servicio especificado.
 *
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $servicio
 * @return cantidad aplicaciones
 */
function cantidad_aplicaciones_del_detalle($consecutivoTraslado, $servicio){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			COUNT( * )
		FROM 
			".$wbasedato."_000015 apl, ".$wbasedato."_000017 trs, ".$wbasedato."_000019 det
		WHERE 
			apl.Fecha_data >= trs.Fecha_data 
			AND apl.Hora_data > trs.Hora_data 
			AND apl.Aplhis = trs.Eyrhis 
			AND apl.Apling = trs.Eyring 
			AND det.Detnum = trs.Eyrnum
			AND det.Detart = apl.Aplart
			AND det.Detest = 'on'
			AND apl.Aplest = 'on' 
			AND trs.Eyrnum = '".$consecutivoTraslado."' 
			AND apl.Aplcco = '".$servicio."';";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$cuenta = 0;
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$cuenta = $row[0];
	}
	return $cuenta;
}

/**
 * Verifica para la adicion de nuevos articulos si se cuenta con el saldo suficiente para realizar el registro del artículo como nuevo.
 * LOS SALDOS SE VERIFICAN CONTRA EL ULTIMO CENTRO DE COSTOS QUE CARGO ARTICULOS AL PACIENTE O BIEN EL DE INGRESO (PROPIO)
 * 
 *
 * @param unknown_type $wcodart
 * @param unknown_type $wapl
 * @param unknown_type $vector
 * @return unknown
 */
function verificar_saldo($whistoria, $wingreso, $cdArticulo, $cantidad, $appCcoOrigen, $ccoOrigen){
	global $wbasedato;
	global $wcenmez;
	global $conex;
	
	$valido = true;
	
	$servicio = consultar_ultimo_cco_modifica_saldos_app($whistoria, $wingreso, $cdArticulo);
	
	if ($appCcoOrigen == "on"){
		$q =  " SELECT sum(spluen-splusa) "
		. "   FROM ".$wbasedato."_000030 "
		. "  WHERE splhis = '" . $whistoria . "'"
		. "    AND spling = '" . $wingreso . "'"
		. "    AND splart = '" . $cdArticulo . "'"
		. "    AND splcco IN ('".$servicio."','".$ccoOrigen."')";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		if ($row[0] < $cantidad){
			$valido = false;
		}
	}else{
		$q = " SELECT sum(spauen-spausa) "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis = '" . $whistoria . "'"
		. "    AND spaing = '" . $wingreso . "'"
		. "    AND spaart = '" . $cdArticulo . "'";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		if ($row[0] < $can){
			$valido = false;
		}
	}
	return $valido;
}

/**
 * Verifica si un articulo que va a ser agregado al detalle ya existe en lo trasladado. 
 *
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdArticulo
 */
function existe_articulo_detalle($consecutivoTraslado, $cdArticulo){
	global $wbasedato;
	global $conex;
	
	$existe = false;
	
	$q = "SELECT 
			Detart
		FROM 
			".$wbasedato."_000019 
		WHERE 
			Detest = 'on'
			AND Detnum = '".$consecutivoTraslado."'
			AND Detart = '".$cdArticulo."';";
		
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$num = mysql_num_rows($res);
	
	if($num > 0){
		if($row[0] == $cdArticulo){
			$existe = true;
		}	
	}
	
	return $existe;
}

function consultar_cantidad_aprovechamiento_saldo_app($whistoria, $wingreso, $servicioCarga, $cdArticulo){
	global $wbasedato;
	global $conex;
	
	$cant = 0;
	
	$q = "SELECT 
			Splaen 
		FROM
			".$wbasedato."_000030  
		WHERE
			Splhis = '".$whistoria."'
			AND Spling = '".$wingreso."'
			AND Splart = '".$cdArticulo."'
			AND Splcco = '".$servicioCarga."';";
		
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$num = mysql_num_rows($res);
	
	if($num > 0){
		$cant = $row[0];
	}
	return $cant;
}

function consultar_cantidad_aprovechamiento_saldo_pac($whistoria, $wingreso, $servicio, $cdMedicamento){
	global $wbasedato;
	global $conex;
	
	$cant = 0;
	
	$q = "SELECT 
			Spaaen 
		FROM
			".$wbasedato."_000004  
		WHERE
			Spahis = '".$whistoria."'
			AND Spaing = '".$wingreso."'
			AND Spaart = '".$cdMedicamento."'
			AND Spacco = '".$servicio."';";
		
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$num = mysql_num_rows($res);
	
	if($num > 0){
		$cant = $row[0];
	}
	return $cant;
}

/**
 * Inserta un articulo nuevo al detalle de entrega y recibo
 *
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdArticulo
 * @param unknown_type $cantidad
 * @param unknown_type $usuario
 */
function insertar_articulo_detalle_ent_y_rec($consecutivoTraslado, $cdArticulo, $cantidad){
	global $wbasedato;
	global $conex;
	
	//Consulta de la informacion del ultimo traslado, esto con el fin de que la fecha la hora y el usuario coincidan con el que realizo el traslado
	$infoTraslado = consultar_traslado($consecutivoTraslado);
	
	$q = "INSERT INTO 
				".$wbasedato."_000019(Medico, Fecha_data, Hora_data, Detnum, Detart, Detcan, Detest, Seguridad)
			VALUES
				('".$wbasedato."', '".$infoTraslado[0]."', '".$infoTraslado[1]."', '".$consecutivoTraslado."', '".$cdArticulo."', '".$cantidad."', 'on', '".$infoTraslado[8]."');";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/**
 * Inserta un articulo en el registro de los saldos de paciente (saldos de articulos de servicios de aplicacion no automatica)
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $servicioCarga
 * @param unknown_type $cantidad
 */  
function insertar_articulo_saldo_paciente($whistoria, $wingreso, $consecutivoTraslado, $cdArticulo, $servicioCarga, $cantidad, $cantidadAprovechamiento){
	global $wbasedato;
	global $conex;
	
	$infoTraslado = consultar_traslado($consecutivoTraslado);
	
	if($cantidadAprovechamiento != $cantidad && $cantidadAprovechamiento > 0){
			$cantidadAprovechamiento -= $cantidad;
		}
	
	if(!existe_material_en_saldo_paciente($whistoria, $wingreso, $cdArticulo, $servicioCarga)){
		$q = "INSERT INTO
				".$wbasedato."_000004(Medico, Fecha_data, Hora_data, Spahis, Spaing, Spacco, Spaart, Spamen, Spamsa, Spauen, Spausa, Spaaen, Spaasa, Seguridad)
			VALUES
				('".$wbasedato."', '".$infoTraslado[0]."', '".$infoTraslado[1]."', '".$whistoria."', '".$wingreso."', '".$servicioCarga."', '".$cdArticulo."', '0', '0', '".$cantidad."' ,'0' ,'".$cantidadAprovechamiento."', '0','".$infoTraslado[8]."');";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	} else {
		$q1 = "UPDATE
					".$wbasedato."_000004
				SET
					Spauen = Spauen+".$cantidad.",
					Spaaen = Spaaen+".$cantidadAprovechamiento."
				WHERE	
					Spahis = '".$whistoria."'
					AND Spaing = '".$wingreso."' 
					AND Spaart = '".$cdArticulo."'
					AND Spacco = '".$servicioCarga."';";
		
		$res = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
	}
}
		
/**
 * Graba un nuevo articulo al detalle de entrega y recibo, realizando el movimiento de saldos necesario.
 * 
 * Restricciones y consideraciones:
 * -El servicio origen debe ser de aplicación automática. 
 * -El paciente debe estar entregado MAS NO RECIBIDO.
 * -Como siempre se aumenta el detalle, siempre se disminuye en la tabla 30
 * -El centro de costos a modificar puede ser, el ultimo que carga (1050 o 1051 o el mismo piso ej. 1020)
 *
 * @param unknown_type $whistoria
 * @param unknown_type $wingreso
 * @param unknown_type $consecutivoTraslado
 * @param unknown_type $cdArticulo
 * @param unknown_type $cantidad
 */
function grabar_articulo_detalle($whistoria, $wingreso, $consecutivoTraslado, $cdArticulo, $cantidad, $servicioOrigen, $servicioActual){
	
	//Aplicacion destino
	$aplCcoActual = consultar_aplicacion_automatica_cco($servicioActual);	
	
	//Almacena el servicio sobre el cual se cargan los articulos.
	$servicioCarga = $servicioOrigen;
	
	//Disminuye la cantidad de saldo en la tabla 30.  
	global $wbasedato;
	global $conex;
	
	//Aprovechamientos
	$cantidadAprovechamiento = consultar_cantidad_aprovechamiento_saldo_app($whistoria, $wingreso, $servicioCarga, $cdArticulo);
	if($cantidadAprovechamiento == 0){
		$q = "UPDATE  
				".$wbasedato."_000030
			SET
				Spluen = Spluen-".$cantidad."
			WHERE	
				Spluen > Splusa
				AND Splhis = '".$whistoria."'  
				AND Spling = '".$wingreso."' 
				AND Splcco = '".$servicioCarga."' 
				AND Splart = '".$cdArticulo."';";	
	} else {	
		$q = "UPDATE  
				".$wbasedato."_000030
			SET
				Spluen = Spluen-".$cantidad.",
				Splaen = Splaen-".$cantidad."
			WHERE	
				Spluen > Splusa
				AND Splhis = '".$whistoria."'  
				AND Spling = '".$wingreso."' 
				AND Splcco = '".$servicioCarga."' 
				AND Splart = '".$cdArticulo."';";
	}
			
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$numModif = mysql_affected_rows();
	
	if($numModif == 0){
		$cco = consultar_ultimo_cco_modifica_saldos_app($whistoria,$wingreso,$cdArticulo);
		$cantidadAprovechamiento = consultar_cantidad_aprovechamiento_saldo_app($whistoria, $wingreso, $cco, $cdArticulo);
		
		$q = "UPDATE  
				".$wbasedato."_000030
			SET
				Spluen = Spluen-".$cantidad."		
			WHERE	
				Spluen > Splusa
				AND Splhis = '".$whistoria."'  
				AND Spling = '".$wingreso."' 
				AND Splcco = '".$cco."' 
				AND Splart = '".$cdArticulo."';";
		
		if($cantidadAprovechamiento > 0){
			$q = "UPDATE  
				".$wbasedato."_000030
			SET
				Spluen = Spluen-".$cantidad.",
				Splaen = Splaen-".$cantidad."		
			WHERE	
				Spluen > Splusa
				AND Splhis = '".$whistoria."'  
				AND Spling = '".$wingreso."' 
				AND Splcco = '".$cco."' 
				AND Splart = '".$cdArticulo."';";
		}
	
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$servicioCarga = $cco;
	}
	
	//Si el servicio en el que se encuentra el paciente actualmente es de aplicacion no automatica, debe crearse el registro en la tabla 4
	if($aplCcoActual == 'off'){
		
		/*Crea un articulo en los saldos de pacientes, se debe tener en cuenta que es posible que el articulo ya se encuentre en el detalle
		 * mas con estado off, en tal caso se actualiza el estado y la cantidad
		*/		
		insertar_articulo_saldo_paciente($whistoria, $wingreso, $consecutivoTraslado, $cdArticulo, $servicioCarga, $cantidad, $cantidadAprovechamiento);
	}
	
	//Crea el registro en la tabla de detalle de entrega y recibo (tabla 19)
	insertar_articulo_detalle_ent_y_rec($consecutivoTraslado, $cdArticulo, $cantidad);
}

function mensajePantalla($mensaje){
	funcionJavascript("modificarInnerHtml('".$mensaje."');");
}

/*
 * FIN DEFINICION FUNCIONES
 */

/*
 * Variables
 */
$colorFondo1 = "660099";
$colorFondo2 = "66FFFF";
$colorFondo3 = "#82caff";
$colorFondo4 = "660099";

$colorLetra1 = "#000000";

$tamanoTitulo1="4";

$colorLetraFondo1 = "ffffff";
$colorLetraFondo2 = "660099";

//$horasMaximoModificacion = 12;


/*
 * Inicio aplicacion
 */
include_once("root/comun.php");
include_once("root/barcod.php");

$wactualiz = "Septiembre 15 de 2016";

if (!isset($user)){
	if (!isset($_SESSION['user'])) {
		session_register("user");
	}
}

if (strpos($user, "-") > 0)
$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));

if (!isset($_SESSION['user'])){
	echo "error";
}else{
	$conex = obtenerConexionBD("matrix");
	
	//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
	if(!isset($wemp_pmla)){
		$wemp_pmla = '01';
	} 
	
	$horasMaximoModificacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HorasMaximoModificacionTraslado'); //Se cambia el numero de horas con esta funcion para que pueda ser manejado desde la root_000051

	$infoInstitucion = consultar_informacion_institucion($wemp_pmla);

	$wcenmez = $infoInstitucion[0];
	$wafinidad = $infoInstitucion[1];
	$wbasedato = $infoInstitucion[2];
	$wtabcco = $infoInstitucion[3];
	$winstitucion = $infoInstitucion[4];
    $wcencam = $infoInstitucion[5];

	//Forma
	echo "<form name='forma' action='Modificacion_traslados.php' method=post>";

	encabezado("MODIFICACION TRASLADOS Y LISTA DE MEDICAMENTOS", $wactualiz, "clinica");
	
	//Al principio se mostrará al usuario las dos posibles opciones de proceso que tiene: Proceso de cambio destino / anulación o de cambios
	//en la lista de materiales
	if(!isset($wccoIngreso) || $wccoIngreso == ''){	
		//Cuerpo de la pagina
		echo "<table align='center' border=0>";

		echo '<span class="subtituloPagina2">';
		echo "Par&aacute;metros de consulta";
		echo "</span>";
		echo "<br>";
		echo "<br>";

		//Centro de costos
		echo "<tr><td class='fila1' width=200>Centro de costos</td>";
		echo "<td class='fila2' align='center' width=250>";
		echo "<INPUT TYPE='password' NAME='wccoIngreso' SIZE=7>";
		echo "</td>";
		echo "</tr>";
				
		echo "</table>";

		echo "<div align='center'>";
		echo "<br>";
		echo "<br>";
		echo "IMPORTANTE: Las anulaciones y los cambios de destino se permiten &uacute;nicamente antes de <b>".$horasMaximoModificacion."</b> horas despu&eacute;s de efectuadas.";
		echo "<br>";
		echo "<br>";
		echo "<tr><td align=center colspan=9><a href='javascript:mostrarAyuda();'>Ver manual de usuario...</a></td></tr>";		
		echo "<br>";
		echo "<br>";
		echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
		echo "</div>";
		funcionJavascript("irCco();");
	} else {
		//El centro de costos que ingresa a la aplicación es el único que puede devolver sus propios cambios
		//Llega con el formato UN.####
		echo "<input type='hidden' name='wccoIngreso' value='".$wccoIngreso."'>";
		$ccoIngreso = retornar_codigo_centro_costos($wccoIngreso);
		$nombreCcoIngreso = nombre_centro_costos($ccoIngreso); 
		
		if($nombreCcoIngreso != "") {

			if(!isset($wproceso)){
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";
			
				echo '<span class="subtituloPagina2">';
				echo "Modificaci&oacute;n traslados y lista de medicamentos";
				echo "</span>";
				echo "<br>";
				echo "<br>"; 	
				
				echo "<table align='center'>";
				echo "<tr align='center'><td width='600' height='30' colspan='2' class=encabezadoTabla><font text size=".$tamanoTitulo1." color='".$colorLetraFondo1."'>Servicio o unidad: ".$nombreCcoIngreso."</td></tr>";
				echo "<tr align='center'><td width='600' height='30' colspan='2' class=fila2><font text size=".$tamanoTitulo1." color='".$colorLetraFondo2."'>Seleccione el proceso de modificaci&oacute;n a realizar</td></tr>";
				echo "<tr align='center'>";
				echo "<td rowspan='2' height='60' class=encabezadoTabla><font text color='".$colorLetraFondo1."'><b>Destino, anulaci&oacute;n traslado </b><br><input type='radio' name='wproceso' value='D' onclick='javascript:enviar();'>";
				echo "<td rowspan='2' height='60' class=encabezadoTabla><font text color='".$colorLetraFondo1."'><b>Lista de medicamentos </b><br><input type='radio' name='wproceso' value='M' onclick='javascript:enviar();'>";
				echo "</tr>";
				echo "<tr align='center'>";
				echo "</tr>";
				echo "<td height='30' colspan='4' align='center' class=fila2><a href='javascript:inicio()'><font text color='".$colorLetraFondo2."'>Regresar al inicio</font></a></td>";
				echo "</tr>";
				echo "<tr><td align=center colspan=9><br><input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
				echo "</table>";
				echo "</div>";
			} else {
				echo "<input type='hidden' name='wproceso' value='".$wproceso."'>";
				echo "<div id='header'>";
				echo "<div id='logo'>";
					
				//Este flag asocia el tipo de proceso a ejecutar
				switch ($wproceso){
					case 'D':
						echo '<span class="subtituloPagina2">';
						echo "Cambio de destino o anulaci&oacute;n de traslado";
						echo "</span>";
						echo "<br>";
						echo "<br>";

						//Cuerpo de la pagina
						echo "<table align='center'>";

						if(!isset($whistoria)){
							//Historia e ingreso
							echo "<tr align='center'><td width='600' height='30' colspan='2' class=encabezadoTabla><font text size=".$tamanoTitulo1." color='".$colorLetraFondo1."'>Servicio o unidad: ".$nombreCcoIngreso."</td></tr>";
							echo "<br><br><tr><td width='100'>&nbsp;</td>";
							echo "<td>";
							echo "<tr><td align=center class=encabezadoTabla><b><font color='".$colorLetraFondo1."'>Ingrese la historia :</font></b><input type='text' name='whistoria' size=10></td></tr>";
							echo "</td>";
							echo "</tr></td>";
							echo "<tr><td colspan='2' align='center'>";
							echo "</tr></td>";
							echo "<tr><td>&nbsp;</tr></td>";							
							echo "<tr>";							
							echo "<td colspan='4' align='center'><a href='javascript:inicio();'>Regresar al inicio</a></td>";
							echo "</tr>";
							echo "<tr><td align=center colspan=9><br><input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
						} else {
						
							$whistoria = trim( $whistoria );	//Septiembre 18 de 2012
							
							//Se consulta y muestra el origen y destino para la historia, en esta pantalla
							//se muestran las dos posibles opciones $waccion, Anular o Cambiar Destino
							$wingreso = consultar_ultimo_ingreso_historia($whistoria);

							if($wingreso > 0){

								echo "<input type='hidden' name='whistoria' value='".$whistoria."'>";
								echo "<input type='hidden' name='wingreso' value='".$wingreso."'>";
									
								//Para la historia y el ingreso se consulta la informacion del ultimo estado del traslado
								//Muestra el servicio, habitacion origen y destino actual para la historia
								$infoUltimoEstado = consultar_informacion_ultimo_estado($whistoria,$wingreso);

								//La habitacion anterior y el servicio anterior se sacan de esta consulta
								$infoUbicacionPaciente = consultar_ubicacion_paciente($whistoria, $wingreso);

								$horasUltimoCambio = $infoUltimoEstado[0];
								$tipoUltimoCambio = $infoUltimoEstado[1];
								$consecutivoTraslado = $infoUltimoEstado[2];
								$fechaEstadoTraslado = $infoUltimoEstado[3];
								$horaEstadoTraslado = $infoUltimoEstado[4];
								$servicioOrigenTraslado = $infoUltimoEstado[5];
								$servicioDestinoTemp = $infoUltimoEstado[6];
								$habitacionAnterior = $infoUbicacionPaciente[1];
								$habitacionDestinoTemp = $infoUltimoEstado[8];
								$id_solicutud_cama = $infoUltimoEstado['id_solicitud'];
								$servicioDestinoTraslado = $servicioDestinoTemp;
								$habitacionDestino = $habitacionDestinoTemp;

								if(isset($wccocod)) {
									$servicioDestinoTraslado = $wccocod;
								}
								if(isset($whabdes)){
									$habitacionDestino = $whabdes;
								}

								$nombreSerOrigen = $infoUltimoEstado[9];
								$nombreSerDestino = $infoUltimoEstado[10];

								//Valida que ningun artículo que se encuentre en el detalle de medicamentos se haya aplicado despues de la entrega, si algun articulo
								//se aplicó, no se permitirá ningun movimiento de anulacion o cambio destino.
								$cantValidacionApps = cantidad_aplicaciones_del_detalle($consecutivoTraslado, $servicioDestinoTraslado);
								if($cantValidacionApps > 0){
									$realizarProceso = false;
									mensajeEmergente("No se puede realizar el proceso, porque se han aplicado medicamentos del detalle de art\u00edculos despues de realizado el recibo.");
									funcionJavascript("inicio();");
								}

								//Validación de horas maximas transcurridas para permitir cambios
								if($horasUltimoCambio > $horasMaximoModificacion){
									$realizarProceso = false;
									mensajeEmergente("El \u00faltimo movimiento de la historia ".$whistoria."-".$wingreso." ocurri\u00f3 hace ".$horasUltimoCambio." horas. Las modificaciones solo pueden ser realizadas antes de ".$horasMaximoModificacion." horas.");
									funcionJavascript("inicio();");
								}
								
								$appCcoIngreso = consultar_aplicacion_automatica_cco($servicioOrigenTraslado);
								
								//Si el centro de costos que ingresa no es el que efectuo el ultimo cambio, no se permite la ejecucion de los procesos
								if(validar_cambios_traslado_centro_costos($whistoria, $wingreso, $ccoIngreso)) {

									echo "<input type='hidden' name='horasMaximoModificacion' value='".$horasMaximoModificacion."'>";
									echo "<input type='hidden' name='horasUltimoCambio' value='".$horasUltimoCambio."'>";

									//Flag que relaciona la accion de anulacion o cambio de destino
									if(!isset($waccion))
									{
										//Vista de la informacion del estado actual
										//Consulta de la información del paciente (nombre)
										$infoPaciente = consultar_info_paciente_x_historia($whistoria, $wingreso);
										$nombrePaciente = $infoPaciente[0]." ".$infoPaciente[1]." ".$infoPaciente[2]." ".$infoPaciente[3];
											
										//Historia e ingreso
										echo "<br><br><tr><td width='200' class=fila1>Historia</td>";
										echo "<td class=fila2>".$whistoria." - ".$wingreso."</td>";
										echo "</tr>";
											
										//Nombre del paciente
										echo "<tr>";
										echo "<td class=fila1>Paciente</td>";
										echo "<td class=fila2>".$nombrePaciente."</td>";
										echo "</tr>";

										//Servicio-Habitacion, Origen
										echo "<tr>";
										echo "<td class=fila1>Ultimo cambio</td>";
										echo "<td class=fila2>".$tipoUltimoCambio."</td>";
										echo "</tr>";
										echo "</table>";
										echo "<br>&nbsp;";
										echo "&nbsp;";

										//Servicio-Habitacion, destino: Encabezado
										//Al realizar el cambio del servicio se deben consultar las habitaciones disponibles
										echo "<table align='center'>";
										echo "<tr align='center' class=encabezadoTabla>";
										echo "<td bgcolor=white>&nbsp;</td>";
										echo "<td>Origen</td>";
										echo "<td>Actual</td>";
										echo "<td>Otro destino ";
										if(isset($chkDestino)){
											echo "<input type='checkbox' name='chkDestino' onclick='javascript:alternarDestino();' checked>";											
										} else { 
											echo "<input type='checkbox' name='chkDestino' onclick='javascript:alternarDestino();'>";
										}
										echo "</td>";
										echo "</tr>";
											
										echo "<tr align='center'>";
										echo "<td class=encabezadoTabla width='100'><font text color=".$colorLetraFondo1.">Servicio</font></td>";
										echo "<td class=fila2>".$servicioOrigenTraslado." - ".$nombreSerOrigen."</td>";
										echo "<td class=fila2>$servicioDestinoTemp - $nombreSerDestino</td>";
										echo "<td align='center' class=fila2>";

										//Consulta del maestro de centros de costos en forma de select - Cambio: Solo se permitira moverlo dentro del mismo servicio actual
										listado_centros_de_costos_hosp_disponibles($servicioDestinoTemp, "javascript:consultarHabitaciones();");

										echo "</td>";
										echo "</tr>";
										echo "<tr align='center'>";
										echo "<td class=encabezadoTabla>Habitacion</td>";
										echo "<td class=fila2>".$habitacionAnterior."</td>";
										echo "<td class=fila2>$habitacionDestinoTemp</td>";
										echo "<td align='center' class=fila2>";

										//Consulta del maestro de habitaciones disponibles para el centro de costos especificado en forma de select
										listado_habitaciones_cco($servicioDestinoTraslado, $habitacionDestino, "");

										echo "</td>";
										echo "</tr>";
										echo "<tr><td colspan='4'>&nbsp;</td></tr>";
											
										echo "<tr align='center'>";
										echo "<td colspan='4' height='30' class=encabezadoTabla>Detalle material</td>";
										echo "</tr>";
										echo "<tr align='center'>";										
										echo "<td colspan='4'>";
										echo "<br>";
										echo "<br>";
										generar_tabla_lista_materiales($infoUltimoEstado[2], $whistoria, $wingreso, true, $servicioDestinoTraslado, $servicioDestinoTraslado);
										echo "<br>";
										echo "<br>";
										echo "</td></tr>";

										echo "<tr class=encabezadoTabla>";
										echo "<td align='center' colspan='4' height='30'>Han transcurrido <b>".$horasUltimoCambio."</b> horas desde ultimo movimiento de traslado</td>";
										echo "</tr>";

										echo "<tr>";
										if($tipoUltimoCambio == "Entrega"){
											echo "<td colspan='4' align='center' height='50'><input type='button' value='Anular entrega' onclick='javascript:procesoAnulacion();' id='btAnular'>&nbsp;|&nbsp;";
										} else {
											echo "<td colspan='4' align='center' height='50'>";
											
											// --> 	Se inhabilita la opcion de de anular los recibos de pacientes.
											//		Jerson trujillo, 2015-05-28		
											
											// if($appCcoIngreso == 'on'){
												// echo "<input type='button' value='Anular SOLO recibo' onclick='javascript:procesoAnulacionRecibo();' id='btAnular2'>&nbsp;|&nbsp;";	
											// }
											// echo "<input type='button' value='Anular recibo y entrega' onclick='javascript:procesoAnulacion();' id='btAnular'>&nbsp;|&nbsp;";
										}
										echo "<input type='button' value='Modificar destino' onclick='javascript:procesoDestino();' disabled id='btModificar'></td>";										
										echo "</tr>";
										echo "<tr>";
										echo "<td colspan='4' align='center'><a href='javascript:inicio()'>Regresar al inicio</a></td>";
										echo "</tr>";
										echo "<tr><td align=center colspan=9><br><input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
										funcionJavascript("alternarDestino();");
									} else {
										/* Validaciones iniciales
										 * Estas modificaciones se permiten para movimientos realizados en un tiempo no superior a 12 horas
										 * el valor dado por la variable horasMaximoModificacion
										 */
										$realizarProceso = true;

										$infoUbicacionActual = consultar_ubicacion_paciente($whistoria, $wingreso);

										$infoHabitacion = consultar_informacion_habitacion($habitacionAnterior);

										$habAnteriorAli = $infoHabitacion[0];
										$habAnteriorDis = $infoHabitacion[1];
										$habAnteriorEst = $infoHabitacion[2];
										$habAnteriorPro = $infoHabitacion[3];
										$habAnteriorSer = $infoHabitacion[4];
										$habAnteriorHis = $infoHabitacion[5];
										$habAnteriorIng = $infoHabitacion[6];

										if(isset($wccocod)) {
											$servicioDestinoTraslado = $wccocod;
										}

										if(isset($whabdes)){
											$habitacionDestino = $whabdes;
										}
											
										//**************TODO: DEBUG AQUI HAGO UN ALERT PARA SABER LOS DESTINOS DE TRASLADOS***********
//										mensajeEmergente("Valores::: Hab: ".$habitacionAnterior." HDis:".$habAnteriorDis." HEst:".$habAnteriorEst." HHis:".$habAnteriorHis." HIng:".$habAnteriorIng);

										//Validacion para anulacion: La habitación anterior debe estar disponible, caso contrario con el mismo paciente
										if($waccion == 'A' || $waccion == 'R'){
											if($habAnteriorDis == 'off')
											{
												if($habAnteriorHis != '' && $habAnteriorIng != ''){
													if($habAnteriorHis != $whistoria || $habAnteriorIng != $wingreso)
													{
														$realizarProceso = false;
														mensajeEmergente("La habitaci\u00f3n ".$habitacionAnterior." ya se encuentra ocupada con otro paciente (".$habAnteriorHis."-".$habAnteriorIng."). No se puede continuar el proceso");
														funcionJavascript("inicio();");
													}
												}
											}
										}
										
										if($realizarProceso){
											switch($waccion){
												//Procesos de anulacion de recibo y entrega.
												case 'A':
													$habitacionAnterior = $infoUbicacionActual[1];

													//Esto se realiza debido a que el usuario puede alterar los select de destino para Anular entrega o recibo
													$servicioDestinoTraslado = $servicioDestinoTemp;
													$habitacionDestino = $habitacionDestinoTemp;

													//TODO: DEBUG AQUI HAGO UN ALERT PARA SABER LOS DESTINOS DE TRASLADOS
//													mensajeEmergente("Valores::: SOrigen:".$servicioOrigenTraslado." HOrigen:".$habitacionAnterior." SDestino:".$servicioDestinoTraslado." HDestino:".$habitacionDestino);

													if ($tipoUltimoCambio == 'Entrega')
													{
														proceso_anulacion_entrega($whistoria, $wingreso, $habitacionAnterior, $habitacionDestino, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $wuser, "Entrega");
													} else {
														proceso_anulacion_recibo($whistoria, $wingreso, $habitacionAnterior, $habitacionDestino, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $wuser);
													}

													//Proceso de verificacion de saldos de medicamentos
													$unidadesEnSaldo = cuenta_saldo_pendiente_paciente($consecutivoTraslado);
													if($unidadesEnSaldo > 0)
													{
														//AA-Aplica Automaticamente, NAA-No Aplica Automaticamente
														$servicioOrigenAA = consultar_aplicacion_automatica_cco($servicioOrigenTraslado);
														$servicioDestinoAA = consultar_aplicacion_automatica_cco($servicioDestinoTraslado);
															
														//TODO:  DEBUG
//														mensajeEmergente("Tipo: ".$tipoUltimoCambio." Origen: ".$servicioOrigenAA." Destino: ".$servicioDestinoAA);									
														
														if ($tipoUltimoCambio == 'Entrega')
														{
															if($servicioOrigenAA == 'on' && $servicioDestinoAA == 'off') {
																//TODO: DEBUG
//																mensajeEmergente("Devuelve Entrega: OrigenAA-DestinoAA: ".$servicioOrigenAA."-".$servicioDestinoAA);
																proceso_devolucion_med_entrega($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTraslado);
															}
														} else {
															if($servicioOrigenAA == 'off' && $servicioDestinoAA=='on'){
																//TODO: DEBUG																
//																mensajeEmergente("Devuelve Recibo: OrigenAA-DestinoAA: ".$servicioOrigenAA."-".$servicioDestinoAA);
																proceso_devolucion_med_recibido($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTraslado);
															}
															
															if($servicioOrigenAA == 'on' && $servicioDestinoAA=='off'){
																//TODO: DEBUG																
//																mensajeEmergente("Devuelve Recibo: OrigenAA-DestinoAA: ".$servicioOrigenAA."-".$servicioDestinoAA);
																proceso_devolucion_med_recibido($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTraslado);
															}
															
														}
													} 
													mensajeEmergente("El traslado ha sido anulado con \u00e9xito.");
													funcionJavascript("inicio();");
													break;
												case 'D':
													//Proceso de cambio de destino - Habitacion desocupada.
													//En $servicioDestinoTemp se guarda el servicio destino original mientras que en $habitacionDestinoTemp se guarda la habitacion destino original
													$realizoProceso = false;
													
													//TODO: DEBUG AQUI HAGO UN ALERT PARA SABER LOS DESTINOS DE TRASLADOS
//													mensajeEmergente("Valores::: SOrigen:".$servicioOrigenTraslado." HOrigen:".$habitacionAnterior." SActual:".$servicioDestinoTemp." HDestino:".$habitacionDestinoTemp." SDestino:".$wccocod." HDestino:".$whabcod);

													//Consulta del tipo de aplicacion del servicio origen
													//AA -Aplicacion Automatica, NAA No Aplicacion Automatica
													$servicioOrigenAA = consultar_aplicacion_automatica_cco($servicioOrigenTraslado);
													$servicioActualAA = consultar_aplicacion_automatica_cco($servicioDestinoTemp);
													$servicioDestinoAA = consultar_aplicacion_automatica_cco($wccocod);

													//TODO: DEBUG
//													mensajeEmergente($servicioOrigenAA."-".$servicioActualAA."-".$servicioDestinoAA);
													
													//Proceso de verificacion de saldos de medicamentos
													$unidadesEnSaldo = cuenta_saldo_pendiente_paciente($consecutivoTraslado);
													
													if ($tipoUltimoCambio == 'Entrega')
													{
														/*UNICAMENTE Si se realiza lo siguiente para la entrega:
														 * Origen: AA
														 * Actual: AA
														 * Nuevo Destino: NAA
														 *
														 * Se notifica al usuario que realice la anulación de la entrega y luego efectue el proceso normal de entrega
														 */
														if($servicioOrigenAA == 'on' && $servicioActualAA == 'on' && $servicioDestinoAA == 'off'){
															mensajeEmergente("PARA CAMBIAR EL DESTINO DE ESTA ENTREGA POR FAVOR REALICE LA ANULACI\u00d3N Y LUEGO VUELVA A REALIZAR EL PROCESO DE ENTREGA DEL PACIENTE.");
															funcionJavascript("inicio();");
														}else {															
															proceso_cambio_destino_entrega($whistoria, $wingreso, $servicioOrigenTraslado, $habitacionAnterior, $wccocod, $whabcod, $habitacionDestinoTemp, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $wuser, "Entrega", $id_solicutud_cama);
															
															if($unidadesEnSaldo > 0)
															{
																//Caso 11: AA-NAA-AA
																if($servicioOrigenAA == 'on' && $servicioActualAA == 'off' && $servicioDestinoAA == 'on') {
																	proceso_movimiento_saldos_destino_med_entrega($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTemp, $wccocod);
																}
															}
															$realizoProceso = true;
														}
													} else {
														/*UNICAMENTE Si se realiza lo siguiente para el recibo:
														 * Origen: AA
														 * Actual: AA
														 * Nuevo Destino: NAA
														 *
														 * Se notifica al usuario que realice el proceso de anulación de la entrega y luego efectue el proceso normal de entrega nuevamente
														 */
														if(($servicioOrigenAA == 'off' && $servicioActualAA == 'off' && $servicioDestinoAA == 'on') || 
																($servicioOrigenAA == 'on' && $servicioActualAA == 'on' && $servicioDestinoAA == 'off')){
															mensajeEmergente("PARA CAMBIAR EL DESTINO DE ESTE RECIBO POR FAVOR REALICE EL PROCESO DE ANULACI\u00d3N Y LUEGO VUELVA A REALIZAR EL PROCESO DE ENTREGA Y RECIBO DEL PACIENTE.");
															funcionJavascript("inicio();");
														}else {
															proceso_cambio_destino_recibo($whistoria, $wingreso, $servicioOrigenTraslado, $habitacionAnterior, $wccocod, $whabcod, $habitacionDestinoTemp, $consecutivoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $wuser);
															
															if($unidadesEnSaldo > 0)
															{
																//Caso 6: NAA-AA-NAA
																if($servicioOrigenAA == 'off' && $servicioActualAA == 'on' && $servicioDestinoAA=='off'){
																	proceso_movimiento_saldos_destino_med_entrega($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTemp, $wccocod);
																}

																//Caso 8: NAA-AA-AA
																if($servicioOrigenAA == 'off' && $servicioActualAA == 'on' && $servicioDestinoAA=='on'){
																	proceso_movimiento_saldos_med_recibido_caso8($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTemp, $wccocod);
																}
																
																//Caso 12: AA-NAA-AA
																if($servicioOrigenAA == 'on' && $servicioActualAA == 'off' && $servicioDestinoAA == 'on') {
																	proceso_movimiento_saldos_destino_med_entrega($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTemp, $wccocod);
																}
															}
															$realizoProceso = true;
														}
													}
													
													if($realizoProceso){
														mensajeEmergente("El destino del traslado ha sido modificado con \u00e9xito.");
														funcionJavascript("inicio();");
													}
													break;
												case 'R':
													/* Proceso de anulación del Recibo UNICAMENTE.  
													 * 
													 * El usuario podrá acceder a esta opción con las siguientes condiciones:
													 * -El servicio origen es de aplicación automática
													 * -El traslado está en estado Recibo
													 * 
													 * Las anteriores condiciones NO OBLIGAN a realizar movimientos de saldos ya que:
													 * -Recibo de AA-NAA: Se mueven saldos es en la entrega
													 * -Recibo de AA-AA: No mueve saldos  
													 */
													proceso_anulacion_solo_recibo($whistoria, $wingreso, $consecutivoTraslado, $servicioOrigenTraslado, $servicioDestinoTraslado, $fechaEstadoTraslado, $horaEstadoTraslado, $wuser);
													
													mensajeEmergente("El recibo ha sido anulado con \u00e9xito.");
													funcionJavascript("inicio();");
													break;
												//Otros PROCESOS pueden ir aqui. Declarando un case y enviandolo en la variable $waccion	
												default:
													//Otras acciones pueden ir aqui.
													break;
											}
										}
									}
								} else {
									mensajeEmergente("El centro de costos seleccionado no realiz\u00f3 el \u00faltimo traslado.");
									funcionJavascript("inicio();");
								}
							} else {
								$realizarProceso = false;
								mensajeEmergente("El n\u00famero del ingreso no pudo consultarse, por favor verifique el n\u00famero de la historia.");
								funcionJavascript("enviar();");
							}
						}
						echo "</table>";
						echo "</div>";
						break;
						case 'M':
							//Si el centro de costos origen aplica se permite el uso de este modulo
							$ccoIngreso = retornar_codigo_centro_costos($wccoIngreso);
							
							if(consultar_aplicacion_automatica_cco($ccoIngreso) == 'off'){
								mensajeEmergente("\u00danicamente se permiten cambios de la lista de art\u00edculos si el centro de costos origen aplica autom\u00e1ticamente.");
								funcionJavascript("inicio();");
							} else {
								//Encabezado
								echo '<span class="subtituloPagina2">';
								echo "Cambios lista de medicamentos";
								echo "</span>";
								echo "<br>";
								echo "<br>";
									
								//Cuerpo de la pagina
								echo "<table align='center' border=0>";

								if(!isset($whistoria)){
									//Historia e ingreso
									echo "<tr align='center'><td width='600' height='30' colspan='2' class=encabezadoTabla>Servicio o unidad: ".$nombreCcoIngreso."</td></tr>";
									echo "<br><br><tr><td width='100'>&nbsp;</td>";
									echo "<td>";
									echo "<tr><td align=center class=encabezadoTabla>Ingrese la historia :<input type='text' name='whistoria' size=10></td></tr>";
									echo "</td>";
									echo "</tr></td>";
									echo "<tr><td colspan='2' align='center'>";
									echo "</tr></td>";
									echo "<tr><td>&nbsp;</tr></td>";
									echo "<tr>";
									echo "<td colspan='4' align='center'><a href='javascript:inicio();'>Regresar al inicio</a></td>";
									echo "</tr>";
									echo "<tr><td align=center colspan=9><br><input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
								} else {
									//Se consulta y muestra el origen y destino para la historia, en esta pantalla
									//se muestran las dos posibles opciones $waccion, Anular o Cambiar Destino

									$wingreso = consultar_ultimo_ingreso_historia($whistoria);

									if($wingreso > 0){
										echo "<input type='hidden' name='whistoria' value='".$whistoria."'>";
										echo "<input type='hidden' name='wingreso' value='".$wingreso."'>";

										$infoUbicacionActual = consultar_ubicacion_paciente($whistoria, $wingreso);
										$infoUltimoEstado = consultar_informacion_ultimo_estado($whistoria,$wingreso);

										//$horasUltimoCambio = $infoUltimoEstado[0];
										$tipoUltimoCambio = $infoUltimoEstado[1];
										$consecutivoTraslado = $infoUltimoEstado[2];
										$servicioOrigenTraslado = $infoUltimoEstado[5];
										$servicioDestinoTraslado = $infoUltimoEstado[6];
										$habitacionAnterior = $infoUbicacionActual[1];
										$habitacionDestino = $infoUltimoEstado[8];
										$nombreSerOrigen = $infoUltimoEstado[9];
										$nombreSerDestino = $infoUltimoEstado[10];

										if($tipoUltimoCambio == 'Recibo'){
											mensajeEmergente("No se permiten cambios en los saldos de art\u00edculos si el paciente ya se encuentra recibido.");
											funcionJavascript("inicio();");
										} else {
											//Si el centro de costos que ingresa no es el que efectuo el ultimo cambio, no se permiten la ejecucion
											//de los procesos
											if(validar_cambios_traslado_centro_costos($whistoria, $wingreso, $ccoIngreso)) {
												//Validaciones para permitir la modificación de la lista de materiales
												$aplOrigen = consultar_aplicacion_automatica_cco($servicioOrigenTraslado);
												$aplActual = consultar_aplicacion_automatica_cco($servicioDestinoTraslado);

												/* Validaciones para cambios:
												 * -Se permiten cambios en el listado de materiales si y solo si el servicio origen es de AA
												 */
												if($aplOrigen == 'on'){
													//Consulta de la información del paciente
													$infoPaciente = consultar_info_paciente_x_historia($whistoria, $wingreso);
													$nombrePaciente = $infoPaciente[0]." ".$infoPaciente[1]." ".$infoPaciente[2]." ".$infoPaciente[3];

													if(!isset($waccion))
													{
														echo "<br><br><tr><td width='200' class=encabezadoTabla>Historia</td>";
														echo "<td class=fila2>".$whistoria." - ".$wingreso."</td>";
														echo "</tr>";

														//Nombre del paciente
														echo "<tr>";
														echo "<td class=encabezadoTabla>Paciente</td>";
														echo "<td class=fila2>".$nombrePaciente."</td>";
														echo "</tr>";

														//Servicio-Habitacion, Origen
														echo "<tr>";
														echo "<td class=encabezadoTabla>Servicio origen</td>";
														echo "<td class=fila2>".$servicioOrigenTraslado." - ".$nombreSerOrigen."</td>";
														echo "</tr>";
														echo "<tr>";
														echo "<td class=encabezadoTabla>Habitacion origen</td>";
														echo "<td class=fila2>".$habitacionAnterior."</td>";
														echo "</tr>";
														echo "<tr>";
														echo "<td class=encabezadoTabla>Servicio actual</td>";
														echo "<td class=fila2>".$servicioDestinoTraslado." - ".$nombreSerDestino."</td>";
														echo "</tr>";
														echo "<tr>";
														echo "<td class=encabezadoTabla>Habitacion actual</td>";
														echo "<td class=fila2>".$habitacionDestino."</td>";
														echo "</tr>";
														echo "</table>";

														//Se consulta la lista de materiales asociados al ultimo consecutivo de traslado.
														//Esta lista incluye la cantidad a modificar y si se anula el material señalado
														echo "<div align='center'>";
														echo "<br>";
														echo "<table>";
														echo "<tr align='center'>";
														echo "<td colspan='4' height='30' class=encabezadoTabla>Listado actual de artículos en el detalle de entrega</td>";
														echo "</tr>";
														echo "</table>";
														echo "<br>";
														generar_tabla_lista_materiales($consecutivoTraslado, $whistoria, $wingreso, false, $servicioOrigenTraslado, $servicioDestinoTraslado);
														echo "<br>";
														echo "</div>";

														echo "<table align='center'>";
														echo "<tr><td colspan='2' align='center'>";
														echo "<input type='reset' value='Limpiar'>&nbsp;|&nbsp;<input type='button' value='Modificar' onclick='javascript:modificarMaterial();'>";
														echo "</tr></td>";
														echo "<tr><td><br><br>";
														echo "</tr></td>";
														
														//Si el paciente está ENTREGADO (no RECIBIDO) y además el centro de costos seleccionado es de aplicación automática se permite adicionar articulos
														$appCcoIngreso = consultar_aplicacion_automatica_cco($ccoIngreso);
														if($appCcoIngreso == 'on' && $tipoUltimoCambio == 'Entrega'){
															echo "<input type='hidden' name='cargarArticulos' value=''>";
															echo "<tr align='center'>";
															echo "<td colspan='4' height='30' class=encabezadoTabla>Listado de artículos nuevos por agregar al detalle</td>";
															echo "</tr>";
															echo "<tr align='center'>";
															echo "<td colspan = '4'>";

															//Lista los articulos que se encuentren la lista a adicionar
															//Encabezado
															echo "<table>";
															echo "<tr>";
															echo "<td width='100' align='center' class=encabezadoTabla>";
															echo "Código";
															echo "</td>";
															echo "<td width='400' align='center' class=encabezadoTabla>";
															echo "Descripción";
															echo "</td>";
															echo "<td width='100' align='center' class=encabezadoTabla>";
															echo "Cantidad";
															echo "</td>";
															echo "</tr>";

															if(!isset($articulos)){
																$articulos = array();
																$artscant = array();
															}

															//Remover articulos
															echo "<input type='hidden' name='artquitar' value=''>";
															if(isset($artquitar) && $artquitar == '*'){
																unset($articulos);
																unset($artscant);
															} else {
																//Adicion del nuevo articulo en el arreglo
																if(isset($wcodart) && isset($wnumart) && $cargarArticulos != '*'){
																	if(isset($artscant[$wcodart])){
																		mensajeEmergente("El art\u00edculo ".$wcodart." ya se encuentra en la lista.");
																	}else {
																		$cont1 = count($articulos);
																		$info_articulo = consultar_nombre_articulo($wcodart);

																		//Si código de articulo existe
																		if(isset($info_articulo[0])){
																			//Si no esta en el detalle del traslado
																			if(!existe_articulo_detalle($consecutivoTraslado, $wcodart)){
																				//Si tiene saldo disponible del artículo
																				if(verificar_saldo($whistoria, $wingreso, $wcodart, $wnumart, $appCcoIngreso, $ccoIngreso)){
																					$cont1++;
																					$articulos[$cont1] = strtoupper($wcodart);
																					$artscant[strtoupper($wcodart)] = $wnumart;
																				}else{
																					mensajeEmergente("El art\u00edculo no fue cargado al paciente o no cuenta con saldo suficiente");
																				}
																			}else {
																				mensajeEmergente("El art\u00edculo ya se encuentra cargado en el detalle de entrega");
																			}
																		}else{
																			mensajeEmergente("El art\u00edculo no existe.");
																		}
																	}
																}
															}
															//Listado de articulos nuevos
															$cont2 = 0;
															$cont3 = 0;
															$clase = "fila1";

															if(isset($articulos)){
																if(isset($cargarArticulos) && $cargarArticulos == '*'){
																	foreach ($articulos as $cdArticulo){
																		if($cdArticulo != ''){
																			grabar_articulo_detalle($whistoria, $wingreso, $consecutivoTraslado, $cdArticulo, $artscant[$cdArticulo], $ccoIngreso, $servicioDestinoTraslado);
//																			mensajeEmergente("Se han grabado los articulos con exito.");
																			funcionJavascript("enviar();");
																		}
																	}
																	unset($articulos);
																	unset($artscant);
																	unset($cargarArticulos);
																}else{
																	foreach ($articulos as $cdArticulo){
																		if($cdArticulo != ''){
																			//Cambio de colores del listado
																			if($clase == "fila1"){
																				$clase = "fila2";
																			} else {
																				$clase = "fila1";
																			}

																			echo "<input type='hidden' name='articulos[".$cont2."]' value='".strtoupper($cdArticulo)."'>";
																			echo "<input type='hidden' name='artscant[".$cdArticulo."]' value='".$artscant[$cdArticulo]."'>";
																			echo "<tr class='$clase'>";
																			echo "<td align='center' bgcolor=".$color.">";
																			echo $cdArticulo;
																			echo "</td>";
																			echo "<td align='center' bgcolor=".$color.">";

																			$info_articulo = consultar_nombre_articulo(strtoupper($cdArticulo));

																			if(count($info_articulo) == 0){
																				echo "no encontrado";
																			} else {
																				echo $info_articulo[0];
																			}

																			echo "</td>";
																			echo "<td align='center' bgcolor=".$color.">";
																			echo $artscant[$cdArticulo];
																			echo "</td>";
																			echo "</tr>";
																			$cont3++;
																		}
																		$cont2++;
																	}
																}
															}

															echo "</table>";
															echo "</td>";
															echo "</tr>";
															echo "<tr align='center'>";
															echo "<td colspan='4'>";
															echo "<br>";
															echo "<br>";
															echo "Codigo articulo: <input type='text' name='wcodart'>&nbsp;&nbsp;&nbsp;";
															echo "Cantidad: <input type='text' name='wnumart' size='5' maxlength='6'>&nbsp;&nbsp;&nbsp;";
															echo "<a href='javascript:agregarArticulo();'>Agregar</a>&nbsp;&nbsp;&nbsp;";
															//											echo "<a href='javascript:quitarArticulos();'>Borrar lista nuevos articulos</a>&nbsp;&nbsp;&nbsp;";
															echo "<br>";
															echo "<br>";
															echo "</td></tr>";
															echo "<tr><td colspan='4' align='center'>";
															echo "<input type='button' value='Borrar lista articulos nuevos' onclick='javascript:quitarArticulos();'>&nbsp;|&nbsp;";
															echo "<input type='button' value='Grabar articulos nuevos' onclick='javascript:cargarArts();'></td>";
															echo "</tr>";
															echo "<tr><td>&nbsp;</td></tr>";
															echo "<tr align='center'>";
															echo "<td colspan='4' height='30' class=encabezadoTablas>&nbsp;</td>";
															echo "</tr>";
														}
														//Fin adicionar articulos
														
														echo "<tr>";
														echo "<td colspan='4' align='center'><a href='javascript:inicio()'>Regresar al inicio</a></td>";
														echo "</tr>";
														echo "<tr><td align=center colspan=9><br><input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
													} else {
														switch($waccion){
															case 'M':
																//Proceso de cambio de destino - Habitacion desocupada.

																//Captura de los valores ingresados de cantidad nueva y checks de eliminación
																foreach($medicamentos as $cdMedicamento){
																	if(isset($cantidades[$cdMedicamento]) && $cantidades[$cdMedicamento] != ''){
																		proceso_modificar_cantidad_saldo_medicamento($whistoria, $wingreso, $consecutivoTraslado, $cdMedicamento, $actual[$cdMedicamento], $cantidades[$cdMedicamento], $aplOrigen, $aplActual, $tipoUltimoCambio, $servicioOrigenTraslado, $servicioDestinoTraslado);
																	} else {
																		if(isset($eliminar[$cdMedicamento]))
																		{
																			proceso_eliminar_saldo_medicamento($whistoria, $wingreso, $consecutivoTraslado, $cdMedicamento, $actual[$cdMedicamento], $aplOrigen, $aplActual, $tipoUltimoCambio, $servicioOrigenTraslado, $servicioDestinoTraslado);
																		}
																	}
																}
																funcionJavascript("document.forma.submit();");
																break;
															default:
																//otras acciones pueden agregarse aqui
																break;
														}//Fin switch accion
													}
												} else {
													mensajeEmergente("\u00danicamente se permiten cambios en la lista de medicamentos cuyo centro de costos origen sea de aplicaci\u00f3n autom\u00e1tica.");
													funcionJavascript("inicio();");
												}
											} else {
												mensajeEmergente("El centro de costos seleccionado no realiz\u00f3 el \u00faltimo traslado.");
												funcionJavascript("inicio();");
											}
										}
									} else {
										mensajeEmergente("El n\u00famero del ingreso no pudo consultarse, por favor verifique el n\u00famero de la historia.");
										funcionJavascript("enviar();");
									}
								}
									
								echo "</tr></td>";
								echo "</table>";
								echo "</div>";
							}
							break;
							default:
							echo "<h1>Opcion no contemplada</h1>";
							break;
				}
			}
		} else {
			mensajeEmergente("El centro de costos seleccionado no existe.");
			funcionJavascript("inicio();");
		}
	}//Fin flag del radio button del proceso
	//include_once("free.php");
}
?>
</body>
</html>
