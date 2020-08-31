<html>

<head>
<title>MATRIX - [ADMISION DE PACIENTES]</title>
<link href="/matrix/root/caro.css" rel="stylesheet" type="text/css" />

<!-- Inclusion del calendario -->
<link rel="stylesheet" href="../../zpcal/themes/winter.css" />
<script type="text/javascript" src="../../zpcal/src/utils.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
<script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
<!-- Fin inclusión calendario -->

<style type="text/css">
.boton {
	background-color: #848484;
	font-family: verdana;
	color: #FFFFFF;
	border-bottom-style: solid;
	border-bottom-color: #ACACAC;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-width: 1px;
	border-top-width: 1px;
	border-left-color: #90815C;
	border-right-color: #9F8F69;
	border-top-color: #626262;
}

.boton2 {
	background-color: #88A1F0;
	font-family: verdana;
	color: #FFFFFF;
	border-top-color: #626262;
	border-top-width: 1px;
	border-left-color: #90815C;
	border-left-width: 1px;
	border-right-color: #9F8F69;
	border-right-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #ACACAC;
	border-bottom-width: 1px;
}

#pantallaModif {
	background-color: #E5EBFD;
	border-bottom-color: #000000;
	border-bottom-style: solid;
	border-bottom-width: 1px;
	border-left-color: #000000;
	border-left-style: solid;
	border-left-width: 1px;
	border-right-color: #000000;
	border-right-style: solid;
	border-right-width: 1px;
	border-top-color: #000000;
	border-top-style: solid;
	border-top-width: 1px;
	font-family: verdana;
	padding-bottom: 5px;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 30px;
	width: 670px;
}

#mensajePantalla {
	background-color: #F8E0E6;
	font-family: verdana;
	width: 670px;
	height: 40px;
	border-top-style: solid;
	border-top-color: #D41506;
	border-top-width: 1px;
	border-left-style: solid;
	border-left-color: #FFFFFF;
	border-left-width: 1px;
	border-right-style: solid;
	border-right-color: #FFFFFF;
	border-right-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #D41506;
	border-bottom-width: 1px;
	padding-top: 0px;
	padding-left: 10px;
	padding-right: 0px;
	padding-bottom: 0px;
	font-size: 11px;
	font-style: italic;
	font-weight: bolder;
}
</style>

</head>

<body>

<script type="text/javascript">
function enter() { document.forms.forma.submit(); }
function cerrarVentana() {	window.close(); }

function inicio()
{
	document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value;	
}

function seleccionServicioHabitacion(){
	document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=a'+'&wselcco='+document.forms.forma.wselcco.value;
}

function regresar(){
	document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value;
}

function consultarInfoAdmision(){
	document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=b&whistoria='+document.forms.forma.whistoria.value;
}

function mostrarMensajePantalla(texto){
	document.getElementById('mensajePantalla').style.display = "block";
	document.getElementById('mensajePantalla').innerHTML = "  ::MENSAJE::  "+texto;
}

function ocultarMensajePantalla(){
	document.getElementById('mensajePantalla').style.display = "none";
}

function calendario(){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfinmed',button:'btnFecha',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
}	

function marcarEntrega(historia, ingreso){
	if(confirm("Desea confirmar la entrega del paciente desde urgencias?")){
		document.forms.forma.wactualizar.value = '*';
		document.forms.forma.whistoria.value = historia;
		document.forms.forma.wingreso.value = ingreso;
		
		document.forms.forma.submit();
	}
}

function admitirPaciente(){
	var historia = document.forms.forma.whistoria.value;
	var servicioOrigen = document.forms.forma.wselcco.value;
	var servicioDestino = ""; 
	var habitacionDestino = ""; 
	
	var validarServicioDestino = true;
	
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
			mensaje += "Debe especificar un servicio destino \n\r";
			valido = false;
		}
	
		if(!habitacionDestino || habitacionDestino == ''){
			mensaje += "Debe especificar una habitación destino";
			valido = false;
		}	
	}
	
	if(!valido){
		alert("Error: \n\r" + mensaje);
	} else {
	if(confirm("Desea realizar proceso de admisión del paciente?")){
		if(validarServicioDestino){
			document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=d&whistoria='+historia+'&servicioOrigen='+servicioOrigen+'&servicioDestino='+servicioDestino+'&habitacionDestino='+habitacionDestino;
		} else {
			document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=d&whistoria='+historia+'&servicioOrigen='+servicioOrigen;
		}
	}
	}
}

function admitirPacienteCirugia(){
	
	var historia = document.forms.forma.whistoria.value;
	var servicioOrigen = document.forms.forma.wselcco.value;
	var servicioDestino = ""; 
	var habitacionDestino = ""; 
	
	var validarServicioDestino = true;
	
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
	if(confirm("Desea realizar proceso de admisión del paciente?")){
		if(validarServicioDestino){
			document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=f&whistoria='+historia+'&servicioOrigen='+servicioOrigen+'&servicioDestino='+servicioDestino+'&habitacionDestino='+habitacionDestino;
		} else {
			document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=f&whistoria='+historia+'&servicioOrigen='+servicioOrigen;
		}
	}
	}
}

function mostrarAyuda()
{
	window.open('../manuales/PreentregaUrgencias.mht', 'window','width=650,height=550,scrollbars=yes,resizable=yes');
}

function nuevoAjax()
	{ 
		var xmlhttp=false; 
		try 
		{ 
			xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
		}
		catch(e)
		{ 
			try
			{ 
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
			} 
			catch(E) { xmlhttp=false; }
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); } 

		return xmlhttp; 
	}
	
	function estaEnProceso(xmlhttp) {
		switch ( xmlhttp.readyState ) {
			case 1, 2, 3:
				return true;
			break;
			// Case 4 and 0
			default:
				return false;
			break;
		}
	}
	
	function consultarHabitaciones()
	{
		var contenedor = document.getElementById('cntHabitacion');
		var parametros = ""; 
				
		parametros = "consultaAjax=04&basedatos="+document.forms.forma.wbasedato.value+"&servicio=" + document.getElementById('wselccodes').value; 
		contenedor.innerHTML = "<select><option value=''>Cargando...</option></select>";		
		
		try{
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/root/comun.php",true);
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

function cancelarAdmision(){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	
	document.location.href='admision2.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=c'+'&whistoria='+historia+'&wingreso='+ingreso;	
}

function irListaPreentrega(){
	document.location.href='admisionPreentrega.php?wemp_pmla='+document.forma.wemp_pmla.value+'&waccion=b';
}
</script>

<?php
include_once("conex.php");

/*BS'D - 356074
 * Nueva version de admisiones
 * Autor: Mauricio Sánchez Castaño.
 * Fecha creacion: 2008-09-24
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
			Ccocir = 'on'; ";

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
 * Consulta si un paciente tiene al menos un movimiento hospitalario (registro en la tabla 17)
 *
 * @param unknown_type $historiaClinica
 * @param unknown_type $ingresoHistoriaClinica
 * @return unknown
 */
function pacienteConMovimientoHospitalario($historiaClinica, $ingresoHistoriaClinica){

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
//	echo $q;
	if($num>0)
	{
		$fila = mysql_fetch_array($err);
		if($fila['Eyrtip'] == 'Entrega'){
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
	$q = "UPDATE
			".$wbasedato."_000020
		SET
			Habhis = '".$whistoria."', 
			Habing = '".$wingreso."',
			Habdis = 'off'
		WHERE
			Habcod = '".$wcodigoHabitacion."';";

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

function eliminarIngresoPaciente($conex, $wbasedato, $historia, $ingreso){
	$q = "DELETE FROM
			".$wbasedato."_000018
		WHERE
			Ubihis = '".$historia."'
			AND Ubiing = '".$ingreso."';";

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}

function consultarUltimoIngresoUnix($conex,$historia){
		
	$q = "SELECT 
				pacnum 
		  	FROM	
		  		inpaci 
			WHERE	
				pachis = '".$pac['his']."' ";
		
	$rs = odbc_do($conex,$q);
	$campos= odbc_num_fields($rs);
	
	$ultimoIngreso = "";
	
	if(odbc_fetch_row($rs)) {
		$ultimoIngreso = odbc_result($rs,1);
	}
	return $ultimoIngreso;
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

function consultarPacienteUnix($conex, $pacienteConsulta){
	$paciente = new pacienteDTO();
	
	$q = "SELECT
			pacnom, pacap1, pacap2, pacnum, pacfec, pachor, pachab, paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco
		FROM 
			inpac, insercco
		WHERE	
			pachis = '".$pacienteConsulta->historiaClinica."' 
			AND serccoser = pacser ";

	$rs = odbc_do($conex,$q);
	$campos = odbc_num_fields($rs);
	
	if (odbc_fetch_row($rs))
	{
		$nombre = explode(" ",trim(odbc_result($rs,1)));
		$paciente->nombre1 = $nombre[0];
		
		if(isset($nombre[1])){
			$paciente->nombre2 = $nombre[1];	
		}
		
		$paciente->apellido1 = trim(odbc_result($rs,2));
		$paciente->apellido2 = trim(odbc_result($rs,3));
		$paciente->historiaClinica = trim($pacienteConsulta->historiaClinica);
		$paciente->ingresoHistoriaClinica = trim(odbc_result($rs,4));
		$paciente->fechaIngreso = str_replace("/","-",trim(odbc_result($rs,5)));
		$paciente->horaIngreso = str_replace(".",":",trim(odbc_result($rs,6))).":00";
		$paciente->habitacionActual = trim(odbc_result($rs,7));
		$paciente->numeroIdentificacionResponsable = trim(odbc_result($rs,8));
		$paciente->nombreResponsable = trim(odbc_result($rs,9));
		$paciente->tipoDocumentoIdentidad = trim(odbc_result($rs,10)); 
		$paciente->documentoIdentidad = trim(odbc_result($rs,11));
		$paciente->fechaNacimiento = trim(odbc_result($rs,12));
		$paciente->genero = trim(odbc_result($rs,13));
		$paciente->deHospitalizacion = trim(odbc_result($rs,14));
		$paciente->servicioActual = trim(odbc_result($rs,15));
	}

	//Si es hospitalizacion, se trae el servicio y habitación actual:
	if(isset($paciente->deHospitalizacion) && $paciente->deHospitalizacion == "H")
	{
		$q = " SELECT 
					trahab, traser, serccocco	
				FROM 
					inmtra, insercco
				WHERE 
					trahis = ".$paciente->historiaClinica."
					AND tranum = ".$paciente->ingresoHistoriaClinica."
					AND traegr IS NULL
					AND serccoser = traser ";
		
		$err_f1 = odbc_do($conex,$q);
		$campos1= odbc_num_fields($err_f1);
		
		if (odbc_fetch_row($err_f1))
		{
			$paciente->servicioActual = odbc_result($err_f1,3);
			$paciente->habitacionActual = odbc_result($err_f1,1);
		}
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
				AND Oriori = '".$origen."'";
		
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);
	
	if($filas > 0){
		$esta = true;	
	}
	return $esta;
}

function existeEnTablaResponsables($conex,$pacienteMatrix, $wemp_pmla){
	$esta = false;
	
	$q = "SELECT 
				* 
		  	FROM	
		  		movhos_000016 
			WHERE	
				Inghis = '".$paciente->historiaClinica."' 
				AND Inging = '".$paciente->ingresoHistoriaClinica."';";
		
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
	
	$err=mysql_query($q,$conex);
}

function actualizarIngresoPaciente($conex, $pacienteAnterior, $pacienteNuevo){
	
	$q = "UPDATE 
			root_000037 
		SET 
			Orihis = '".$pacienteNuevo->historiaClinica."', 
			Oriing = '".$pacienteNuevo->ingresoHistoriaClinica."'
		WHERE 
			Orihis = '".$pacienteAnterior->historiaClinica."' 
			AND Oriing = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
		
	$err1=mysql_query($q,$conex);
}

function insertarResponsablePaciente($conex, $paciente, $wemp_pmla){	
	$date=date("Y-m-d");
	$hora=date("H:i:s");
			
	$q = "INSERT INTO movhos_000016 
			(medico,Fecha_data,Hora_data,Inghis,Inging,Ingres,Ingnre,Seguridad)
		VALUES 
			('movhos','".$date."','".$hora."','".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$paciente->numeroIdentificacionResponsable."', '".$paciente->nombreResponsable."', 'A-movhos' )";
	
//	echo $q;
	$err=mysql_query($q,$conex);
}

function actualizarResponsablePaciente($conex, $pacienteAnterior, $pacienteNuevo){
	
	$q = "UPDATE 
			movhos_000016 
		SET 
			Ingres = '".$pacienteNuevo->numeroIdentificacionResponsable."', 
			Ingnre = '".$pacienteNuevo->nombreResponsable."'
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
	$date=date("Y-m-d");
	$hora=date("H:i:s");
			
	$q = "INSERT INTO 
			movhos_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,Ubiptr,Seguridad)
		VALUES 
			('movhos','".$date."','".$hora."','".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', '".$ingreso->servicioActual."', '".$ingreso->servicioAnterior."', '".$ingreso->habitacionActual."',  '".$ingreso->habitacionAnterior."','".$ingreso->altaEnProceso."', '".$ingreso->altaDefinitiva."', '".$ingreso->fechaAltaProceso."','".$ingreso->horaAltaProceso."', '".$ingreso->fechaAltaDefinitiva."', '".$ingreso->horaAltaDefinitiva."', '".$ingreso->enProcesoTraslado."', '".$ingreso->usuario."' )";
	
	$err=mysql_query($q,$conex);
}

function actualizarUbicacionPaciente($conex,$ingresoAnterior, $ingresoNuevo){
	$date=date("Y-m-d");
	$hora=date("H:i:s");
			
	$q = "UPDATE movhos_000018 SET 
			Ubisac = '".$ingresoNuevo->servicioActual."',
			Ubisan = '".$ingresoNuevo->servicioAnterior."',
			Ubihac = '".$ingresoNuevo->habitacionActual."',
			Ubihan = '".$ingresoNuevo->habitacionAnterior."'
		WHERE 
			Ubihis = '".$ingresoAnterior->historiaClinica."'
			AND Ubiing = '".$ingresoAnterior->ingresoHistoriaClinica."' ";
		
	
	$err=mysql_query($q,$conex);
}

function grabarMovimientoHospitalario($conex,$ingresoPaciente,$usuario){
	$date=date("Y-m-d");
	$hora=date("H:i:s");

	//Bloqueo de tablas
	$q = "LOCK TABLE MOVHOS_000001 LOW_PRIORITY WRITE";
	$err = mysql_query($q, $conex);

//	mensajeEmergente("bloqueo tablas");
	//Actualizacion del consecutivo de entrega y recibo
	$q = " UPDATE 
				movhos_000001 
			SET 
				connum=connum + 1 
			WHERE 
				contip='entyrec' ";

	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	//Captura del consecutivo de entrega y recibo
	$q = "SELECT 
			connum 
		FROM 
			movhos_000001 
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
			movhos_000017 (Medico,Fecha_data,Hora_data,Eyrnum,Eyrhis,Eyring,Eyrsor,Eyrsde,Eyrhor,Eyrhde,Eyrtip,Eyrest,Seguridad) 
		VALUES 
			('movhos','".$date."','".$hora."','".$wconsec."','".$ingresoPaciente->historiaClinica."','".$ingresoPaciente->ingresoHistoriaClinica."','".$ingresoPaciente->servicioAnterior."','".$ingresoPaciente->servicioActual."','".$ingresoPaciente->habitacionAnterior."','".$ingresoPaciente->habitacionActual."','Entrega','on','C-".$usuario->codigo."')";
	
//	echo $q;
	$err=mysql_query($q,$conex);
	echo mysql_error();	
}

function marcarHabitacion($conex,$habitacion){
	
	$q = "UPDATE 
			movhos_000020
		SET
			Habhis = '".$habitacion->historiaClinica."', 
			Habing = '".$habitacion->ingresoHistoriaClinica."',
			Habdis = '".$habitacion->disponible."'
		WHERE
			Habcod = '".$habitacion->codigo."' ";
	
//	echo $q;
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function esUrgencias($conex, $servicio){
	$es = false;

	$q = "SELECT 
				Ccourg 
		 	FROM 
		 		movhos_000011
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
	$es = false;

	$q = "SELECT 
				Ccocir 
		 	FROM 
		 		movhos_000011
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
	$es = false;

	$q = "SELECT 
				*
		 	FROM 
		 		movhos_000018
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
 * FIN DEFINICION FUNCIONES
 */

/*
 * Variables
 */
$colorEncabezadoTabla = "#88A1F0";
$colorFila1 = "#CDD5F0";
$colorFila2 = "#F6F5FF";

$colorLetraEncabezadoTabla = "#000000";
$colorLetraTabla = "";

$tamanoTitulo1="4";

$colorLetraFondo1 = "ffffff";
$colorLetraFondo2 = "660099";

$horasMaximoModificacion = 12;
$wactualiz = "(Versión 26/Septiembre/2008)";

/*
 * Inicio aplicacion
 */
include_once("root/comun.php");

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
	$wactualiz = ".:Version 2.0:. 2008-09-30";
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	//Forma
	echo "<form name='forma' action='admision2.php' method=post>";
	echo "<input type='HIDDEN' NAME='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' NAME='wbasedato' value='".$wbasedato."'>";	

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
	
	//Encabezado de la pantalla de la aplicacion
	echo "<div id='header'>";
	echo "<div id='logo'>";
	echo "<h1>ADMISIÓN DE PACIENTES</h1>";
	echo "<h2><b>".$winstitucion."</b>&nbsp;".$wactualiz."</h2>";
	echo "</div>";
	echo "</div></br></br>";
	echo "<div id='page' align='center'>";
	echo "</br>";
	echo "</br>";
	
	if(connectOdbc($conex, $wbasedato, &$conexUnix, 'facturacion')){
		//En esta seccion ya hay conexión con unix verificada.
		switch($waccion){
			case 'a': //Pantalla para la seleccion de servicio y habitacion destino (si aplica)
				echo "<input type=hidden name=wselcco value=".$wselcco.">";
					
				//Centro de costos que admite, cco urgencias y cco cirugia
				$ccoDeAdmision = consultarCentroCosto($conex, $wselcco, $wbasedato);
				$ccoUrgencias = consultarCcoUrgencias();
				$ccoCirugia = consultarCcoCirugia();
					
				($ccoDeAdmision == $ccoUrgencias) ? $admiteUrgencias = true : $admiteUrgencias = false;
				($ccoDeAdmision == $ccoCirugia) ? $admiteCirugia = true : $admiteCirugia = false;
					
				/*
				 * Los centros de costos que admiten tienen diferentes comportamientos:
				 *
				 * 1. URGENCIAS: Permite seleccion del servicio y habitacion destino PERO no ocupa habitación.
				 * 2. CIRUGIA:	NO PERMITE seleccion del servicio y habitacion destino, se deja EN RECUPERACION, no ocupa habitacion
				 * 3. RESTO: Se hace entrega del paciente normalmente al servicio y habitacion destino
				 */
					
				echo "Admision de paciente desde ".$ccoDeAdmision->codigo." - ".$ccoDeAdmision->nombre."<br/><br/>";

				if(!$admiteCirugia){
					//Se presentan los servicios y las habitaciones disponibles
					echo "<table border=1 cellspacing=0>";
					echo "<tr><td>";
						
					echo "Historia clínica:&nbsp;&nbsp;&nbsp;";
						
					echo "</td>";
					echo "<td>";

					echo "<input type='text' size='8' name='whistoria' id='whistoria'>";

					echo "</td>";
					echo "</tr>";

					echo "<tr><td>";
						
					echo "Servicio:&nbsp;&nbsp;&nbsp;";
						
					echo "</td>";
					echo "<td>";
						
					$centrosCostosHospitalarios = centrosCostosHospitalarios($conex, $wbasedato);
						
					echo "<select name=wselccodes id=wselccodes onchange='javascript:consultarHabitaciones();'>";
					echo "<option value=''>--Seleccione un servicio--</option>";
					foreach ($centrosCostosHospitalarios as $centroCostosHospitalario) {
						echo "<option value=".$centroCostosHospitalario->codigo.">".$centroCostosHospitalario->nombre."</option>";
					}
					echo "</select>";
						
					echo "</td></tr>";
					echo "<tr><td>";
						
					echo "Habitaciones disponibles:&nbsp;&nbsp;&nbsp;";
						
					echo "</td>";
					echo "<td align='center'>";
						
					echo "<span id='cntHabitacion'>";
					echo "<select name='wselhab' id='wselhab'>";
					echo "<option value=''>--Seleccione una habitacion--</option>";
					echo "</select>";
					echo "</span>";
						
					echo "</td></tr>";
				} else {
					//Unicamente se muestra la historia
					echo "<table border=1 cellspacing=0>";
					echo "<tr><td>";
						
					echo "Historia clínica:&nbsp;&nbsp;&nbsp;";
						
					echo "</td>";
					echo "<td>";

					echo "<input type='text' size='8' name='whistoria' id='whistoria'>";

					echo "</td>";
					echo "</tr>";
				}
					
				echo "<tr><td colspan=2 align=center>";
				echo "<input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;&nbsp;<input type=button value='Admitir paciente' onclick='javascript:admitirPaciente();'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				echo "</td></tr>";
					
				echo "</table>";
					
				break; //Fin seleccion servicio que admite
			case 'b': //Realiza la validacion de la informacion de un paciente, mostrando un mensaje de la ubicación donde se encuentra
				$paciente = new pacienteDTO();
				$paciente = consultarInfoPacientePorHistoria($conex,$whistoria);
				
				//Datos del paciente
				if(isset($paciente->nombre1)){
//					mensajeEmergente("1");
					$pacienteIngresado = pacienteIngresado($conex, $paciente);
//					mensajeEmergente("2");
					$pacienteConMovimientoHospitalario = pacienteConMovimientoHospitalario($paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
					
					echo "El paciente ".$paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2.". Con historia clínica ".$paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica."<br/>";

					if($pacienteIngresado){
						$ubicacionPaciente = new ingresoPacientesDTO();
						$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
						
						$ccoActual = consultarCentroCosto($conex,$ubicacionPaciente->servicioActual,$wbasedato);
						$ccoAnterior = consultarCentroCosto($conex,$ubicacionPaciente->servicioAnterior,$wbasedato);	
								
						//Se encuentra ya admitido
						if(isset($ccoActual->nombre)){
							echo "Se encuentra admitido en la institución en el servicio ".$ccoActual->codigo."-".$ccoActual->nombre."<br/>";
						}
						
						//Con servicio anterior
						if(isset($ccoAnterior->nombre)){
							echo "Procedente del servicio ".$ccoAnterior->codigo."-".$ccoAnterior->nombre."<br/>";	
						} else {
							$ccoAnterior->codigo = "";
						}
						
						/* La admision se puede cancelar si:
						 * 1. Si el servicio actual es uno de los de ingreso
						 * 2. Si el servicio anterior es uno de ingreso y adicionalmente el ultimo movimiento es de entrega.
						 */
						$centrosCostosIngreso = centrosCostosIngreso($conex, $wbasedato);
						$puedeCancelarAdmision = false;
						
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
						
						//Acceso a cancelación de la admisión
						if($puedeCancelarAdmision){							
							echo "<br/><input type=button value='Cancelar admision' onclick='javascript:cancelarAdmision();'>";
							echo "<input type=hidden name=whistoria value=".$paciente->historiaClinica.">";
							echo "<input type=hidden name=wingreso value=".$paciente->ingresoHistoriaClinica.">";
						}
						
					} else {
						echo "Aun no ha sido admitido a la institucion en Matrix"; 
					}
				} else {
					echo "El paciente con historia clínica ".$whistoria." <br/>Aun no ha sido admitido a la institución en Matrix";
				}
				
				//Finalmente los botones de regresar y cerrar ventana
				echo "<br/>";
				echo "<br/>";
				echo "<div align=center>";
				echo "<input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				echo "</div>";
				
				break;
			case 'c': //Realiza la cancelación de la admisión de un paciente
				$paciente = new pacienteDTO();
				$paciente = consultarInfoPacientePorHistoria($conex,$whistoria);
				
				//Datos del paciente
				if(isset($paciente->nombre1)){
					$ubicacionPaciente = new ingresoPacientesDTO();
					$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
						
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
					}
					
					//Paso 2.
//					mensajeEmergente("inhabilitando ultimo movimiento hospitalario");
					deshabilitarUltimoMovimientoHospitalario($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
					
					//Paso 3.
//					mensajeEmergente("Eliminando ingreso paciente");
					eliminarIngresoPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
										
					echo "La cancelación de la admisión del paciente $paciente->nombre1 $paciente->nombre2 $paciente->apellido1 $paciente->apellido2 con historia clínica $paciente->historiaClinica-$paciente->ingresoHistoriaClinica<br/>";
					echo "se realizó con éxito.<br/>";
				} else {
					echo "No se encontró paciente con la historia: ".$whistoria."<br/>";				
				}
				echo "<br/><input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				break;
			case 'd': //Admisión a la institución
				//Consulta de paciente en UNIX 
				$paciente = new pacienteDTO();

				$paciente->historiaClinica = $whistoria;
				
				$pacienteUnix = consultarPacienteUnix($conexUnix, $paciente);  //Paciente Unix				

				//DEBUG**
//				echo "Realizando la admisión PACIENTE EXISTE".$whistoria."-".$servicioOrigen."-".$servicioDestino."-".$habitacionDestino;

				if(isset($pacienteUnix->nombre1)){

					/* El proceso de Admision en MATRIX es el siguiente:
					 *
					 * NO SE PERMITE historia cero:
					 * 1.Si no es historia cero:
					 * 1.1 Validar existencia de historia en UNIX
					 * 1.2 Si el paciente no se encuentra registrado en UNIX
					 * 1.2.1 Finaliza la aplicacion, no existe en UNIX
					 * 1.3 Si esta registrado en UNIX
					 * 1.4 Se busca en la tabla unica de pacientes 36
					 * 1.4.1 Si NO esta registrado se ingresa la información en la tabla 36
					 * 1.5 Se busca en la tabla de ingresos paciente 37
					 * 1.5.1 Si esta registrado se actualiza el numero de ingreso del paciente
					 * 1.5.2 Si NO esta registrado se crea registro de ingreso
					 * 1.5.2.1 Se verifica si el numero y tipo de documento del paciente para los ingresos es el mismo
					 * 1.5.2.1.1 Si NO es el mismo documento y tipo, se actualiza en la tabla de ingresos 37
					 * 1.5.2.1.2 Si NO es el mismo documento y tipo, se eliminan los registros de paciente 36 del documento anterior
					 * 1.6 Si NO existe, se crea registro en la tabla 16
					 * 1.7.1 Si existe el registro se actualiza el nombre y nit del responsable
					 * 
					 * 2.Si el servicio que admite es URGENCIAS
					 * 2.1.El paciente se registra en la tabla 18 sin habitacion destino, servicio y habitacion anterior.
					 * 2.2.Se registra en la tabla 17 el movimiento hospitalario
					 * 
					 * 3.Si el servicio que admite es CIRUGIA
					 * 3.1.El paciente se reigstra en la tabla 18 sin habitacion destino, servicio y habitación anterior.
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
						
//					echo "pacienteMATRIX::";
//					var_dump($pacienteMatrix);
//					
//					if(isset($pacienteMatrix)){
//						mensajeEmergente("Seteao");	
//					} else {
//						mensajeEmergente("NO Seteao");
//					}
					
					//Si ya se encuentra admitido va a consultar la información, la guia es la tabla 18, si esta ahi, YA SE CONSIDERA INGRESADO
//					mensajeEmergente("3");
					if(!isset($pacienteMatrix->ingresoHistoriaClinica) or !pacienteIngresado($conex,$pacienteMatrix)){
//						mensajeEmergente("4");
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

//						echo "pacienteMATRIX::";
//						var_dump($pacienteMatrix);
//						echo "pacienteUNIX::";
//						var_dump($pacienteUnix);
							
						//Proceso de ingreso en tabla unica y de ingresos pacientes root_000037, root_000036
						if(isset($pacienteMatrix->documentoIdentidad)){
//							mensajeEmergente("verificando si existe");
							$pacienteEnTablaUnica = existeEnTablaUnicaPacientes($conex,$pacienteMatrix);
							$pacienteEnTablaIngresos = existeEnTablaIngresos($conex,$pacienteMatrix, $wemp_pmla);
							$pacienteMatrix->historiaClinica = $pacienteUnix->historiaClinica;
							$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
							$pacienteConResponsablePaciente = existeEnTablaResponsables($conex,$pacienteMatrix, $wemp_pmla);							

							if($pacienteMatrix->documentoIdentidad == $pacienteUnix->documentoIdentidad && $pacienteMatrix->tipoDocumentoIdentidad == $pacienteUnix->tipoDocumentoIdentidad){
								if(!$pacienteEnTablaUnica && !$pacienteEnTablaIngresos){
									$mismoDocumentoIdentidad = false;
								} else {
//									mensajeEmergente("Meesmo documento");
									$mismoDocumentoIdentidad = true;
								}
							}
						} else {
							$pacienteMatrix->historiaClinica = $whistoria;
							$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
							$pacienteMatrix->documentoIdentidad = "";
							$pacienteMatrix->tipoDocumentoIdentidad = "";  
						}
							
						if(!$pacienteEnTablaUnica){
//							mensajeEmergente("Insertando en tabla unica");
							insertarPacienteTablaUnica($conex, $pacienteUnix);
						}
							
						//Tabla 37
						if(!$pacienteEnTablaIngresos){
//							mensajeEmergente("Insertando ingresos");
							insertarIngresoPaciente($conex, $pacienteUnix, $wemp_pmla);
						} else {
//							mensajeEmergente("Actualizando ingresos");
							actualizarIngresoPaciente($conex, $pacienteMatrix, $pacienteUnix);
						}
						
						//Tabla 16
						if(!$pacienteConResponsablePaciente){
							insertarResponsablePaciente($conex, $pacienteUnix, $wemp_pmla);
						} else {
							actualizarResponsablePaciente($conex, $pacienteMatrix, $pacienteUnix);
						}
							
						if(!$mismoDocumentoIdentidad){
//							mensajeEmergente("Actualizando doc ingresos");
							actualizarDocumentoPacienteTablaIngresos($conex,$pacienteMatrix,$pacienteUnix);

//							mensajeEmergente("Actualizando doc unica");
							actualizarDocumentoPacienteTablaUnica($conex,$pacienteMatrix,$pacienteUnix);
						}
							
						//Proceso de movimiento hospitalario
						$ingresoPaciente = new ingresoPacientesDTO();
							
						$ingresoPaciente->historiaClinica = $pacienteUnix->historiaClinica;
						$ingresoPaciente->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
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
								//Grabar ingreso paciente
								$ingresoPaciente->servicioAnterior = "";
								$ingresoPaciente->habitacionAnterior = "";
								
								grabarIngresoPaciente($conex,$ingresoPaciente);
									
								break;
							case '2':
								//Grabar ingreso paciente
								$ingresoPaciente->servicioAnterior = "";
								$ingresoPaciente->habitacionAnterior = "";
								
								grabarIngresoPaciente($conex,$ingresoPaciente);
//								mensajeEmergente("Ingresado");	
								
								//Movimiento hospitalario
								$ingresoPaciente->servicioAnterior = $servicioOrigen;
								$ingresoPaciente->habitacionAnterior = "";
								$ingresoPaciente->servicioActual = $servicioDestino;
								$ingresoPaciente->habitacionActual = $habitacionDestino;

								grabarMovimientoHospitalario($conex,$ingresoPaciente,$usuario);
//								mensajeEmergente("Movimiento");
								break;
							default :
								//Grabar ingreso paciente
								$ingresoPaciente->servicioAnterior = $servicioOrigen;
								$ingresoPaciente->habitacionAnterior = "";
								$ingresoPaciente->servicioActual = $servicioDestino;
								$ingresoPaciente->habitacionActual = $habitacionDestino;

								grabarIngresoPaciente($conex,$ingresoPaciente);
									
								//Movimiento hospitalario
								$ingresoPaciente->servicioAnterior = $servicioOrigen;
								$ingresoPaciente->habitacionAnterior = "";
								$ingresoPaciente->servicioActual = $servicioDestino;
								$ingresoPaciente->habitacionActual = $habitacionDestino;

								grabarMovimientoHospitalario($conex,$ingresoPaciente,$usuario);
									
								//Marca habitación
								$habitacion = new habitacionDTO();
									
								$habitacion->codigo = $ingresoPaciente->habitacionActual;
								$habitacion->servicio = $ingresoPaciente->servicioActual;
								$habitacion->historiaClinica = $pacienteMatrix->historiaClinica;
								$habitacion->ingresoHistoriaClinica = $pacienteMatrix->ingresoHistoriaClinica;
								$habitacion->disponible = "off";
									
								marcarHabitacion($conex,$habitacion);
								break;
						}
						echo "<br/>El paciente $pacienteUnix->nombre1 $pacienteUnix->nombre2 $pacienteUnix->apellido1 $pacienteUnix->apellido2 con historia clínica $pacienteUnix->historiaClinica-$pacienteUnix->ingresoHistoriaClinica<br/>ha sido admitido a la institución con éxito.<br/><br/>";
					} else {
						//Si ya hay registro en la tabla 18 mostrar la pantalla de consulta Accion b
						echo "<input type=hidden name=whistoria value=".$pacienteMatrix->historiaClinica.">";
						funcionJavascript("consultarInfoAdmision();");
					}
				} else {
					echo "<br/>La historia $whistoria no está activa en Unix. No se puede realizar la admisión.<br/>";
				}
				echo "<br/><input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				break;
			case 'e':  //Exclusivo para seleccion de destino para cirugia
				echo "<input type=hidden name=wselcco value=".$wselcco.">";
					
				//Centro de costos que admite, cco urgencias y cco cirugia
				$ccoDeAdmision = consultarCentroCosto($conex, $wselcco, $wbasedato);
					
				echo "Admision de paciente desde ".$ccoDeAdmision->codigo." - ".$ccoDeAdmision->nombre."<br/><br/>";

				//Se presentan los servicios y las habitaciones disponibles
				echo "<table border=1 cellspacing=0>";
				echo "<tr><td>";

				echo "Historia clínica:&nbsp;&nbsp;&nbsp;";

				echo "</td>";
				echo "<td>";

				echo "<input type='text' size='8' name='whistoria' id='whistoria' value='".$historia."'>";

				echo "</td>";
				echo "</tr>";

				echo "<tr><td>";

				echo "Servicio:&nbsp;&nbsp;&nbsp;";

				echo "</td>";
				echo "<td>";

				$centrosCostosHospitalarios = centrosCostosHospitalarios($conex, $wbasedato);

				echo "<select name=wselccodes id=wselccodes onchange='javascript:consultarHabitaciones();'>";
				echo "<option>--Seleccione un servicio--</option>";
				foreach ($centrosCostosHospitalarios as $centroCostosHospitalario) {
					echo "<option value=".$centroCostosHospitalario->codigo.">".$centroCostosHospitalario->nombre."</option>";
				}
				echo "</select>";

				echo "</td></tr>";
				echo "<tr><td>";

				echo "Habitaciones disponibles:&nbsp;&nbsp;&nbsp;";

				echo "</td>";
				echo "<td align='center'>";

				echo "<span id='cntHabitacion'>";
				echo "<select name='wselhab' id='wselhab'>";
				echo "<option>--Seleccione una habitacion--</option>";
				echo "</select>";
				echo "</span>";

				echo "</td></tr>";
					
				echo "<tr><td colspan=2 align=center>";
				echo "<input type=button value='Admitir paciente' onclick='javascript:admitirPacienteCirugia();'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				echo "</td></tr>";
					
				echo "</table>";
					
				break; //Fin seleccion servicio que admite
				break;
			case 'f':
				//Consulta de paciente en UNIX 
				$paciente = new pacienteDTO();

				$paciente->historiaClinica = $whistoria;
				$pacienteUnix = consultarPacienteUnix($conexUnix, $paciente);  //Paciente Unix

				if(isset($pacienteUnix->nombre1)){

					if(!pacienteConMovimientoHospitalario($pacienteUnix->historiaClinica, $pacienteUnix->ingresoHistoriaClinica)){

						//whistoria='+historia+'&servicioOrigen='+servicioOrigen+'&servicioDestino='+servicioDestino+'&habitacionDestino
							
						//Proceso de movimiento hospitalario
						$ingresoPaciente = new ingresoPacientesDTO();

						$ingresoPaciente->historiaClinica = $pacienteUnix->historiaClinica;
						$ingresoPaciente->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
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
							
						actualizarUbicacionPaciente($conex, $ingresoPaciente,$ingresoNuevo);

						//Movimiento hospitalario
						$ingresoPaciente->servicioAnterior = $servicioOrigen;
						$ingresoPaciente->habitacionAnterior = "";
						$ingresoPaciente->servicioActual = $servicioDestino;
						$ingresoPaciente->habitacionActual = $habitacionDestino;

						grabarMovimientoHospitalario($conex,$ingresoPaciente,$usuario);

						//Marca habitación
						$habitacion = new habitacionDTO();

						$habitacion->codigo = $habitacionDestino;
						$habitacion->servicio = $servicioDestino;
						$habitacion->historiaClinica = $ingresoPaciente->historiaClinica;
						$habitacion->ingresoHistoriaClinica = $ingresoPaciente->ingresoHistoriaClinica;
						$habitacion->disponible = "off";

						marcarHabitacion($conex,$habitacion);

						//Debe ir a la pantalla de pacientes en cirugia!
						mensajeEmergente("El paciente $pacienteUnix->nombre1 $pacienteUnix->nombre2 $pacienteUnix->apellido1 $pacienteUnix->apellido2 con historia clínica $pacienteUnix->historiaClinica-$pacienteUnix->ingresoHistoriaClinica. Se ha entregado desde cirugía con éxito.");
						funcionJavascript("irListaPreentrega();");						
					}else {
						echo "<br/>El paciente $pacienteUnix->nombre1 $pacienteUnix->nombre2 $pacienteUnix->apellido1 $pacienteUnix->apellido2 con historia clínica $pacienteUnix->historiaClinica-$pacienteUnix->ingresoHistoriaClinica<br/> ya había sido entregado desde cirugía previamente.<br/>";
					}
				} else {
					echo "<br/>La historia $whistoria no está activa en Unix. No se puede realizar la admisión.<br/>";
				}
				echo "<br/><input type=button  value='Regresar' onclick='javascript:regresar();'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				break;
			default:  //Muestra la pantalla inicial
				echo "Selección del servicio de ingreso<br/><br/>";
				echo "<table border=1 cellspacing=0>";
				echo "<tr><td>";
					
				echo "Servicio:&nbsp;&nbsp;&nbsp;";
					
				echo "</td>";
				echo "<td>";
					
				$centrosCostosIngreso = centrosCostosIngreso($conex, $wbasedato);
					
				echo "<select name=wselcco id=wselcco onchange='javascript:seleccionServicioHabitacion();'>";
				echo "<option value='-'>--Seleccione un servicio--</option>";
				foreach ($centrosCostosIngreso as $centroCostosIngreso) {
					echo "<option value=".$centroCostosIngreso->codigo.">".$centroCostosIngreso->nombre."</option>";
				}
				echo "</select>";
					
				echo "</td></tr>";

				echo "</td></tr>";
					
				echo "<tr><td colspan=2 align=center>";
				echo "<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
				echo "</td></tr>";
					
				echo "</table>";
				break; //Fin pantalla inicial
		}//Fin case
	} else {
		//No hay conexion con unix
	}
	
	echo "</div>"; //Cierre div id=page
}

//Liberacion de conexion Matrix
liberarConexionBD($conex);

//Liberacion de conexion Unix
liberarConexionOdbc($conexUnix);
?>
</body>
</html>
