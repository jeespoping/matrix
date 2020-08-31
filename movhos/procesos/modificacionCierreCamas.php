<html>

<head>
  <title>MATRIX - [MODIFICACION CIERRE CAMAS]</title>

<style type="text/css">

#pantallaModif
{
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
</style>

</head>

<body>

<script type="text/javascript">
var cambio = false;

function activarCambios(){
	cambio = true;
}

function enter() { document.forms.forma.submit(); }
function cerrarVentana() {	window.close(); }

function mostrarEditar(){	
	//			   0	  1	    2		3	   4   5   6    7	    8	    9     10	 11		12	   13     14  	 15		16     17	   18		19
	//Argumentos: fecha,cdCco,cnOcu,pacDiaAnt,ing,egr,iye,cnDis,cnDisAnt,cnMMay,cnMMen,ingUrg,ingCir,ingAdm,ingTrs,egrTrs,dieTrs,dieAlM,egrAltas,diasEstan

	document.getElementById("pantallaModif").style.display = "block";
	
	document.getElementById("spServicio").innerHTML = arguments[1];
	document.getElementById("txServicio").value = arguments[1];
	document.getElementById("txOcupadas").value = arguments[2];
	document.getElementById("vrOcupadas").value = arguments[2];
	document.getElementById("txAnterior").value = arguments[3];
	document.getElementById("vrPacientesDiaAnt").value = arguments[3];
	document.getElementById("txIngresos").value = arguments[4];
	document.getElementById("txEgresos").value = arguments[5];
	document.getElementById("txIngyEgresos").value = arguments[6];
	document.getElementById("txDisponibles").value = arguments[7];	
	document.getElementById("vrDisponibles").value = arguments[7];
	document.getElementById("hDisponiblesAnt").value = arguments[8];
	document.getElementById("txDisponiblesAnt").value = arguments[8];
	document.getElementById("txMMayores").value = arguments[9];
	document.getElementById("txMMenores").value = arguments[10];
	
	document.getElementById("txIngresosU").value = arguments[11];
	document.getElementById("txIngresosC").value = arguments[12];
	document.getElementById("txIngresosA").value = arguments[13];
	document.getElementById("txIngresosT").value = arguments[14];
	document.getElementById("txEgresosT").value = arguments[15];
	document.getElementById("txDiasEstanciaT").value = arguments[16];
	document.getElementById("txDiasEstanciaAm").value = arguments[17];
	document.getElementById("txEgresosA").value = arguments[18];
	document.getElementById("txDiasEstancia").value = arguments[19];	
	
	cambio = false;
}

function actualizarIndicador(){
	
	if(cambio){
	var valido = true;
	
	//Antes de actualizar verifico las variables
	if(!validoNumeroPositivo("txDisponibles")){
		alert("Debe especificar un valor numerico mayor o igual a cero para las camas disponibles");		
		return false;
	}
	
	if(!validoNumeroPositivo("txOcupadas")){
		alert("Debe especificar un valor numerico mayor o igual a cero para las camas ocupadas");		
		return false;
	}
	
	if(!validoNumeroPositivo("txAnterior")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los pacientes del dia anterior");
		return false;
	}
	
	if(!validoNumeroPositivo("txIngresos")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los ingresos");
		return false;
	}
	
	if(!validoNumeroPositivo("txEgresos")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los egresos");
		return false;
	}
	
	if(!validoNumeroPositivo("txIngyEgresos")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los ingresos y egresos del mismo dia");
		return false;
	}

	if(!validoNumeroPositivo("txMMayores")){
		alert("Debe especificar un valor numerico mayor o igual a cero para las muertes mayores");
		return false;
	}
	
	if(!validoNumeroPositivo("txMMenores")){
		alert("Debe especificar un valor numerico mayor o igual a cero para las muertes menores");
		return false;
	}
	
	if(!validoNumeroPositivo("txIngresosU")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los ingresos por urgencias");
		return false;
	}
	
	if(!validoNumeroPositivo("txIngresosC")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los ingresos por cirugía");
		return false;
	}
	
	if(!validoNumeroPositivo("txIngresosA")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los ingresos por admisiones");
		return false;
	}
	
	if(!validoNumeroPositivo("txIngresosT")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los ingresos por traslado");
		return false;
	}
	
	if(!validoNumeroPositivo("txDiasEstanciaT")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los dias de estancia por traslado");
		return false;
	}
	
	if(!validoNumeroPositivo("txDiasEstanciaAm")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los dias de estancia por altas y muertes");
		return false;
	}
	
	if(!validoNumeroPositivo("txEgresosA")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los egresos por altas");
		return false;
	}
	
	if(!validoNumeroPositivo("txDiasEstancia")){
		alert("Debe especificar un valor numerico mayor o igual a cero para los dias de estancia");
		return false;
	}
	
	if(valido){
		document.forms.forma.wactualizar.value = '*';
		cambio = false;
		enter();
	} else {
		cambio = false;
	}
	}else{
		alert("No ha modificado ningún valor");
	}
}

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

function calcularDisponibles(){
	var disponibles = parseInt(document.getElementById('vrDisponibles').value);
	var ocupadasNueva = parseInt(document.getElementById('txOcupadas').value);
	var ocupadasAnterior = parseInt(document.getElementById('vrOcupadas').value);
	
	if(ocupadasNueva <= (ocupadasAnterior+disponibles)){
		if(!isNaN(ocupadasNueva)){		
			document.getElementById('txDisponibles').value = '0';
			document.getElementById('txDisponibles').value = eval(disponibles+(ocupadasAnterior-ocupadasNueva));
			activarCambios();
		}
	} else {
		document.getElementById('txOcupadas').value = '';
		alert("La cantidad no debe exceder " + (ocupadasAnterior+disponibles) + " camas ocupadas para este servicio");	
	}
}

function calcularDisponiblesDiaAnterior(){
	var disponibles = parseInt(document.getElementById('hDisponiblesAnt').value);
	var ocupadasNueva = parseInt(document.getElementById('txAnterior').value);
	var ocupadasAnterior = parseInt(document.getElementById('vrPacientesDiaAnt').value);
	
	if(ocupadasNueva > (ocupadasAnterior+disponibles)){
		document.getElementById('txAnterior').value = '';
		alert("La cantidad no debe exceder " + (ocupadasAnterior+disponibles) + " pacientes dia anterior para este servicio");	
	} else {
		if(!isNaN(ocupadasNueva)){		
			document.getElementById('txDisponiblesAnt').value = '0';			
			document.getElementById('txDisponiblesAnt').value = eval(disponibles+(ocupadasAnterior-ocupadasNueva));
			activarCambios();
		}
	}
}

function validarCantMuertes(){
	var egresos = parseInt(document.getElementById('txEgresos').value);
	var mmayor = parseInt(document.getElementById('txMMayores').value);
	var mmenor = parseInt(document.getElementById('txMMenores').value);
	
	if(egresos < (mmayor+mmenor)){
		document.getElementById('txMMayores').value = '0';
		document.getElementById('txMMenores').value = '0';
		alert("La cantidad de muertes mayores mas la cantidad de muertes menores no debe exceder la cantidad de egresos.");	
	} 
	activarCambios();
}

function sumaIngresos(){
	try{
		var ingU = parseInt(document.getElementById("txIngresosU").value);
		var ingC = parseInt(document.getElementById("txIngresosC").value);
		var ingA = parseInt(document.getElementById("txIngresosA").value);
		var ingT = parseInt(document.getElementById("txIngresosT").value);
	
		document.getElementById("txIngresos").value = (ingU+ingC+ingA+ingT);
	} catch (error){	}
	
	activarCambios();
}

function sumaEgresos(){
	try{
		var egrT = parseInt(document.getElementById("txEgresosT").value);
		var mmayor = parseInt(document.getElementById('txMMayores').value);
		var mmenor = parseInt(document.getElementById('txMMenores').value);
		var egrA = parseInt(document.getElementById('txEgresosA').value);
	
		document.getElementById("txEgresos").value = (egrT+mmayor+mmenor+egrA);
	} catch (error){	}
	
	activarCambios();
}

function sumaDias(){
	try{
		var diasT = parseInt(document.getElementById("txDiasEstanciaT").value);
		var diasAm = parseInt(document.getElementById('txDiasEstanciaAm').value);
	
		document.getElementById("txDiasEstancia").value = (diasT+diasAm);
	} catch (error){	}
	
	activarCambios();
}

function inicio()
{
	document.location.href='modificacionCierreCamas.php?wemp_pmla='+document.forma.wemp_pmla.value;	
}

function mostrarMensajePantalla(texto){
	document.getElementById('mensajePantalla').style.display = "block";
	document.getElementById('mensajePantalla').innerHTML = "  ::MENSAJE::  "+texto;
}

function ocultarMensajePantalla(){
	document.getElementById('mensajePantalla').style.display = "none";
}

</script>
	
<?php
include_once("conex.php");

/*BS'D
 * MODIFICACION INDICADORES DE INDICADORES HOSPITALARIOS
 * 
 * Autor: Mauricio Sánchez Castaño. 
 * Definicion de clases DTO
 *
 * Actualizacion: 
 
			2013-02-08: (Frederick) Se cambia el programa para que actualize la tabla 000038 cuando se hace una modificacion
						hasta ultimo dia del mes
						
			2012-10-23: (Frederick) Se estaban actualizando solamente 8 registros en adelante de la tabla 000038 cuando se hacia una modificacion,
						se obligo a actualizar todos los registros que existan en adelante
						
 
			2012-06-25 Se agregan las consultas consultarCentroCostos y dibujarSelect que listan los centros  
 *              de costos de un grupo dado en orden alfabetico y dibuja el select con esos centros   
 *              de costo respectivamente, tambien se agregaron explode a la variable wccocod para extraer el codigo
 *				del centro de costo para hacer las consultas Viviana Rodas
 */
 
 

//Representa un registro de indicador hospitalario
class indicadorDTO {
	var $codigoServicio;	
	var $fecha;
	var $hora;
	var $camasOcupadas;
	var $camasDisponibles;
	var $camasDisponiblesDiaAnterior;
	var $pacientesDiaAnterior;
	var $cantidadIngresos;
	var $cantidadEgresos;
	var $cantidadIngresosYEgresos;	
	var $cantidadIngresosUrgencias;
	var $cantidadIngresosCirugia;
	var $cantidadIngresosAdmisiones;
	var $cantidadIngresosTraslado;
	var $cantidadMuertesMayores;
	var $cantidadMuertesMenores;
	var $cantidadEgresosTraslado;	
	var $cantidadEgresosAlta;
	var $diasEstancia;	
	var $diasEstanciaTraslado;
	var $diasEstanciaAltasMuertes;	
}

function consultarIndicadores($indicador){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			Fecha_data, Hora_data, Cieser, Ciedis, Cieocu, Ciedes, Cieing, Cieegr, Ciediam, Cieiye, Ciemmay, Ciemmen, Cieinu, Cieinc, Cieina, Cieint, Ciegrt, Ciedit, Cieeal    
		FROM 
			".$wbasedato."_000038 
		WHERE
			Fecha_data = '".$indicador->fecha."'
			AND Cieser LIKE '".trim($indicador->codigoServicio)."'
		;";
	
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	$resultSet = array();
	$coleccion = array();
	
	if ($num > 0)
	{
		$cont1 = 0;
		while($cont1 < $num){
			$resultSet = mysql_fetch_array($res);

			$indRes = new indicadorDTO();

			$indRes->fecha 						= $resultSet['Fecha_data'];
			$indRes->hora 						= $resultSet['Hora_data'];
			$indRes->codigoServicio 			= $resultSet['Cieser'];			
			$indRes->camasDisponibles 			= $resultSet['Ciedis'];
			$indRes->camasOcupadas	 			= $resultSet['Cieocu'];
			
			$indRes->cantidadIngresos 			= $resultSet['Cieing'];
			$indRes->cantidadEgresos 			= $resultSet['Cieegr'];
			$indRes->cantidadIngresosYEgresos 	= $resultSet['Cieiye'];
			$indRes->cantidadMuertesMayores		= $resultSet['Ciemmay'];
			$indRes->cantidadMuertesMenores		= $resultSet['Ciemmen'];
			$indRes->cantidadIngresosUrgencias	= $resultSet['Cieinu'];
			$indRes->cantidadIngresosCirugia	= $resultSet['Cieinc'];
			$indRes->cantidadIngresosAdmisiones	= $resultSet['Cieina'];
			$indRes->cantidadIngresosTraslado	= $resultSet['Cieint'];
			$indRes->cantidadEgresosTraslado	= $resultSet['Ciegrt'];
			$indRes->diasEstancia				= $resultSet['Ciedes'];
			$indRes->diasEstanciaAltasMuertes	= $resultSet['Ciediam'];
			$indRes->diasEstanciaTraslado		= $resultSet['Ciedit'];	
			$indRes->cantidadEgresosAlta		= $resultSet['Cieeal'];		
			
			//Pacientes dia anterior						
			$indDiaAnt = consultarPacientesDiaAnterior($indRes);			
			
			$indRes->pacientesDiaAnterior		= !empty($indDiaAnt->camasOcupadas) ? $indDiaAnt->camasOcupadas : 0;
			$indRes->camasDisponiblesDiaAnterior= !empty($indDiaAnt->camasDisponibles) ? $indDiaAnt->camasDisponibles : 0;

//			$indRes->camasOcupadas 				= $indRes->pacientesDiaAnterior + $indRes->cantidadIngresos - $indRes->cantidadEgresos;
//			$indRes->camasDisponibles			= $resultSet['Ciedis'] + $resultSet['Cieocu'] - $indRes->camasOcupadas; 
//			if($indRes->camasDisponibles < 0){
//				$indRes->camasDisponibles		= 0;
//			}
			
			$coleccion[] = $indRes;
			
			$cont1++;
		}
	}
	return $coleccion;
}

function consultarPacientesDiaAnterior($ind){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			Cieocu, Ciedis 
		FROM 
			".$wbasedato."_000038 
		WHERE
			Fecha_data = DATE_ADD('".$ind->fecha."', INTERVAL -1 DAY)
			AND Cieser = '".trim($ind->codigoServicio)."'
		;";
	
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	$consulta = new indicadorDTO();
	if($num > 0){
		$resultSet = mysql_fetch_array($res);
		
		$consulta->fecha = $ind->fecha;
		$consulta->codigoServicio = $ind->codigoServicio;
		$consulta->camasOcupadas = $resultSet['Cieocu'];
		$consulta->camasDisponibles = $resultSet['Ciedis'];		
	}
	return $consulta;
}

function consultarCentrosCostosHospitalarios(){
	global $wbasedato;
	global $conex;
		
  	$q = "SELECT 
				ccocod, UPPER(Cconom)
			FROM 
				".$wbasedato."_000011
			WHERE  
  				Ccohos = 'on'
			ORDER by 1;";
  	
  	$res1 = mysql_query($q,$conex);
  	$num1 = mysql_num_rows($res1);
  	
  	$coleccion = array();
  	
  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$row1 = mysql_fetch_array($res1);
  			
  			$consulta = new centroCostosDTO();
  			
  			$consulta->codigo = $row1[0];
  			$consulta->nombre = $row1[1];
  			
			$coleccion[] = $consulta;
  		}
  	}
  	return $coleccion;
}

function consultarIndicador($indicadorConsulta){
	global $wbasedato;
	global $conex;
	
	$resultado = new indicadorDTO();
	
	$q = "SELECT
			Fecha_data, Hora_data, Cieser, Ciedis, Cieocu, Cieing, Cieegr, Cieiye, Ciedes, Ciediam, Ciemmay, Ciemmen, Cieinu, Cieinc, Cieina, Cieint, Ciegrt, Ciedit, Cieeal 
		FROM 
			".$wbasedato."_000038 
		WHERE
			Fecha_data = '".$indicadorConsulta->fecha."'
			AND Cieser = '".trim($indicadorConsulta->codigoServicio)."'
		;";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$resultSet = mysql_fetch_array($res);
	
	$resultado->fecha 						= $resultSet['Fecha_data'];
	$resultado->codigoServicio 				= $resultSet['Cieser'];
	$resultado->hora 						= $resultSet['Hora_data'];
	$resultado->camasDisponibles 			= $resultSet['Ciedis'];
	$resultado->camasOcupadas 				= $resultSet['Cieocu'];
	$resultado->cantidadIngresos 			= $resultSet['Cieing'];
	$resultado->cantidadEgresos 			= $resultSet['Cieegr'];
	$resultado->cantidadIngresosYEgresos 	= $resultSet['Cieiye'];
	$resultado->cantidadMuertesMayores		= $resultSet['Ciemmay'];
	$resultado->cantidadMuertesMenores		= $resultSet['Ciemmen'];
	$resultado->cantidadIngresosUrgencias	= $resultSet['Cieinu'];
	$resultado->cantidadIngresosCirugia		= $resultSet['Cieinc'];
	$resultado->cantidadIngresosAdmisiones	= $resultSet['Cieina'];
	$resultado->cantidadIngresosTraslado	= $resultSet['Cieint'];
	$resultado->cantidadEgresosTraslado		= $resultSet['Ciegrt'];
	$resultado->diasEstancia				= $resultSet['Ciedes'];
	$resultado->diasEstanciaAltasMuertes	= $resultSet['Ciediam'];
	$resultado->diasEstanciaTraslado		= $resultSet['Ciedit'];
	$resultado->cantidadEgresosAlta			= $resultSet['Cieeal'];

	//Pacientes dia anterior
	$indDiaAnt = consultarPacientesDiaAnterior($indicadorConsulta);			
			
	$resultado->pacientesDiaAnterior		= $indDiaAnt->camasOcupadas;
	$resultado->camasDisponiblesDiaAnterior = $indDiaAnt->camasDisponibles;
			
	return $resultado;
}

function indicadorAString($indicador){
	$string = "";
	
	$string = $string.$indicador->camasDisponibles.",";
	$string = $string.$indicador->camasOcupadas.",";
	$string = $string.$indicador->cantidadIngresos.",";
	$string = $string.$indicador->cantidadEgresos.",";
	$string = $string.$indicador->cantidadIngresosYEgresos.",";
	$string = $string.$indicador->camasDisponiblesDiaAnterior.",";
	$string = $string.$indicador->pacientesDiaAnterior.",";
	$string = $string.$indicador->cantidadMuertesMayores.",";
	$string = $string.$indicador->cantidadMuertesMenores.",";
	$string = $string.$indicador->cantidadIngresosUrgencias.",";
	$string = $string.$indicador->cantidadIngresosCirugia.",";
	$string = $string.$indicador->cantidadIngresosAdmisiones.",";
	$string = $string.$indicador->cantidadIngresosTraslado.",";
	$string = $string.$indicador->cantidadEgresosTraslado.",";
	$string = $string.$indicador->cantidadEgresosAlta.",";
	$string = $string.$indicador->diasEstancia.",";
	$string = $string.$indicador->diasEstanciaAltasMuertes.",";
	$string = $string.$indicador->diasEstanciaTraslado;
	
	return $string;
}

/**
 *
 */
function actualizarIndicador($indicadorModificar) {
	global $wbasedato;
	global $conex;
	global $wuser;
	
	$exito = true;
	$cantDiasModificar = 8;
	
	if(!empty($indicadorModificar->codigoServicio)){
		
		$indicadorAnterior = consultarIndicador($indicadorModificar);	
		
		$a_date = $indicadorModificar->fecha;
		$ultimoDiaMes = date("Y-m-t", strtotime($a_date)); //2013-02-08

		//2012-10-23
		$query_cant =    " 	SELECT 		count(*) as cantidad "
						."    FROM ".$wbasedato."_000038 "
						."	 WHERE		Fecha_data BETWEEN '".$indicadorModificar->fecha."' AND '".$ultimoDiaMes."'"   
						."	   AND 		Cieser = '".trim($indicadorModificar->codigoServicio)."' ";
		$res_cant = mysql_query($query_cant, $conex) or die ("Error: " . mysql_errno() . " - en el query " . mysql_error());
		$row_cant = mysql_fetch_assoc($res_cant);
		
		if( isset( $row_cant['cantidad'] )){
			$cantDiasModificar = $row_cant['cantidad'];
		}
		//2012-10-23
		
		$q="UPDATE ".$wbasedato."_000038 SET
			Ciedis = '".$indicadorModificar->camasDisponibles."',
			Cieocu = '".$indicadorModificar->camasOcupadas."',
			Cieing = '".$indicadorModificar->cantidadIngresos."',
			Cieegr = '".$indicadorModificar->cantidadEgresos."',
			Cieiye = '".$indicadorModificar->cantidadIngresosYEgresos."',
			Ciemmay = '".$indicadorModificar->cantidadMuertesMayores."',
			Ciemmen = '".$indicadorModificar->cantidadMuertesMenores."',
			Cieinu = '".$indicadorModificar->cantidadIngresosUrgencias."', 
			Cieinc = '".$indicadorModificar->cantidadIngresosCirugia."', 
			Cieina = '".$indicadorModificar->cantidadIngresosAdmisiones."', 
			Cieint = '".$indicadorModificar->cantidadIngresosTraslado."', 
			Ciegrt = '".$indicadorModificar->cantidadEgresosTraslado."',
			Ciedes = '".$indicadorModificar->diasEstancia."',
			Ciediam = '".$indicadorModificar->diasEstanciaAltasMuertes."',
			Ciedit = '".$indicadorModificar->diasEstanciaTraslado."',
			Cieeal = '".$indicadorModificar->cantidadEgresosAlta."' 
		WHERE
			Cieser = '".$indicadorModificar->codigoServicio."'
			AND Fecha_data = '".$indicadorModificar->fecha."';";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		//Actualiza pacientes dia anterior
		$q="UPDATE ".$wbasedato."_000038 SET
			Ciedis = '".$indicadorModificar->camasDisponiblesDiaAnterior."',
			Cieocu = '".$indicadorModificar->pacientesDiaAnterior."'
		WHERE
			Cieser = '".$indicadorModificar->codigoServicio."'
			AND Fecha_data = DATE_ADD('".$indicadorModificar->fecha."', INTERVAL -1 DAY);";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		//Realiza la actualización en cascada de los pacientes dia anterior
		$vecFechaConsulta = explode("-",$indicadorModificar->fecha);
		$diaEnSegundos = (60 * 60 * 24);
		
		for($cont1 = 0 ; $cont1 < $cantDiasModificar ; $cont1++){
			
			//Fecha actual en formato unixtimestamp
			$fechaConsulta = mktime(0,0,0,$vecFechaConsulta[1],$vecFechaConsulta[2],$vecFechaConsulta[0]);
			$fechaFinal = $fechaConsulta + $diaEnSegundos*($cont1);
			$fechaFinalConsulta = date('Y-m-d', $fechaFinal);
			
//			echo "Procesando fecha $fechaFinalConsulta";

			//Consulto los ingresos y los egresos para el dia presente
			$q="SELECT
					Cieing, Cieegr, Ciedis, Cieocu, (Ciedis+Cieocu) Camas_totales, (SELECT 
																						Cieocu 
																					FROM 
																						{$wbasedato}_000038 
																					WHERE 
																						Cieser = '{$indicadorModificar->codigoServicio}' 
																						AND Fecha_data = DATE_SUB('".$fechaFinalConsulta."', INTERVAL +1 DAY)) pacDiaAnterior
				FROM
					".$wbasedato."_000038
				WHERE
					Cieser = '".$indicadorModificar->codigoServicio."'
					AND Fecha_data = '$fechaFinalConsulta';";
		
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			
			if($num > 0){
				$resultSet = mysql_fetch_array($res);

//				echo "Valores anteriores Ocupadas ".$resultSet['Cieocu'].". Disponibles ".$resultSet['Ciedis'];
					
				$ingresosTotales	 	= $resultSet['Cieing'];
				$egresosTotales			= $resultSet['Cieegr'];
				$pacientesDiaAnterior 	= $resultSet['pacDiaAnterior'];
				
				if($cont1 == 0){
					$pacientesDiaAnterior 	= $indicadorModificar->pacientesDiaAnterior;
				}
				
				$camasTotales 			= $resultSet['Camas_totales'];

				$camasOcupadas 			= $pacientesDiaAnterior + $ingresosTotales - $egresosTotales;
				$camasDisponibles 		= $camasTotales - $camasOcupadas;
					
//				echo "Valores nuevos Ocupadas $camasOcupadas. Disponibles $camasDisponibles<br>";
					
				//Actualizo el valor de pacientes dia anterior o camas ocupadas del dia anterior
				$q="UPDATE ".$wbasedato."_000038 SET
					Ciedis = '".$camasDisponibles."',
					Cieocu = '".$camasOcupadas."'
				WHERE
					Cieser = '".$indicadorModificar->codigoServicio."'
					AND Fecha_data = '".$fechaFinalConsulta."';";

				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			} else {
				break;
			}
		}
		
		//Registro de auditoria en la tabla 39	
		$q="INSERT INTO  
			".$wbasedato."_000039 (medico, Fecha_data, Hora_data, Aunnum, Aunanu, Aunfan, Aunhan, Aunmod, Aunfmo, Aunhmo, Aunacc, Seguridad)
		VALUES
			('movhos','".date("Y-m-d")."','".(string)date("H:i:s")."','".str_replace("-","",$indicadorModificar->fecha)."-".$indicadorModificar->codigoServicio."','off','0-0-0','0:0:0','on','".date("Y-m-d")."','".(string)date("H:i:s")."','Anterior::".indicadorAString($indicadorAnterior)." Nuevo::".indicadorAString($indicadorModificar)."', 'A-".$wuser."');";	

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	} else {
		$exito = false;
	}
	return $exito;
}

/*
 * FIN DEFINICION FUNCIONES
 */

/*
 * Inicio aplicacion
 */
include_once("root/comun.php");
include_once("root/barcod.php");

$colorEncabezadoTabla = "#88A1F0";
$colorFila1 = "#CDD5F0";
$colorFila2 = "#F6F5FF";

$colorLetraEncabezadoTabla = "#000000";
$colorLetraTabla = "";

$tamanoTitulo1="4";

$colorLetraFondo1 = "ffffff";
$colorLetraFondo2 = "660099";

$wactualiz = " 2013-02-08";                      // Aca se coloca la ultima fecha de actualizacion de este programa //

if (!isset($user)){
	if (!isset($_SESSION['user'])) {
		session_register("user");
	}
}

if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

if (!isset($_SESSION['user'])){
	terminarEjecucion("usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar.");
}else{
	//Conexion base de datos
	$conex = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	
	//Forma
	echo "<form name='forma' action='modificacionCierreCamas.php' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	//Actualizacion de un indicador
	if(isset($wactualizar) && $wactualizar == '*'){
		$indicadorModificar = new indicadorDTO();
		
		$indicadorModificar->codigoServicio = $txServicio;
		$indicadorModificar->fecha = $wfecha;
		$indicadorModificar->camasOcupadas = $txOcupadas;
		$indicadorModificar->camasDisponibles = $txDisponibles;
		$indicadorModificar->pacientesDiaAnterior = $txAnterior;
		$indicadorModificar->cantidadIngresos = $txIngresos;
		$indicadorModificar->cantidadEgresos = $txEgresos;
		$indicadorModificar->cantidadIngresosYEgresos = $txIngyEgresos;
		$indicadorModificar->camasDisponiblesDiaAnterior = $txDisponiblesAnt;
		$indicadorModificar->cantidadMuertesMayores = $txMMayores;
		$indicadorModificar->cantidadMuertesMenores = $txMMenores;
		
		$indicadorModificar->cantidadIngresosUrgencias = $txIngresosU;
		$indicadorModificar->cantidadIngresosCirugia = $txIngresosC;
		$indicadorModificar->cantidadIngresosAdmisiones = $txIngresosA;
		$indicadorModificar->cantidadIngresosTraslado = $txIngresosT;
		$indicadorModificar->cantidadEgresosTraslado = $txEgresosT;
		$indicadorModificar->diasEstanciaAltasMuertes = $txDiasEstanciaAm;		
		$indicadorModificar->diasEstanciaTraslado = $txDiasEstanciaT;
		$indicadorModificar->cantidadEgresosAlta = $txEgresosA;
		$indicadorModificar->diasEstancia = $txDiasEstancia;
		
		if(actualizarIndicador($indicadorModificar)){
			mensajeEmergente("El indicador ha sido modificado con éxito.");	
		}
	}

	//Encabezado
	encabezado("MODIFICACION CIERRE CAMAS", $wactualiz, "clinica");
		
	//Al principio se mostrará al usuario las dos posibles opciones de proceso que tiene: Proceso de cambio destino / anulación o de cambios
	//en la lista de materiales
	if(!isset($wfecha) || !isset($wccocod)){
		//Cuerpo de la pagina
		echo "<table align='center' border=0>";
			
		echo '<span class="subtituloPagina2">';
		echo "Ingrese los parámetros de consulta";
		echo "</span>";
		echo "<br>";
		echo "<br>";

		//Fecha
		echo "<tr><td class='fila1' width=192 align='center'>Fecha</td>";
		echo "<td class='fila2' align='center' width=200>";
		campoFecha("wfecha");
		echo "</td>";
		echo "</tr>";

		//Consultar centros de costos hospitalarios
			
		$cco="Ccohos";
		$sub="off";
		$tod="Todos";
		//$cco=" ";
		$ipod="off";
		$centrosCostos = consultaCentrosCostos($cco);
		//echo "<table align='center' border=0 >";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wccocod");
					
		echo $dib;
		//echo "</table>";
					  
		echo "<tr><td align=center colspan=19><br><input type=button value='Consultar' onclick='javascript:enter();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
		
		echo "</table>";
	} else {
	
				
		$wser1=explode("-",$wccocod);   //se extrae el codigo del centro de costo cuando se consultan todos
		$wccocod=$wser1[0];
		
		
							
		//Nombre centro de costos	
		$ccoConsulta = new centroCostosDTO();
		$ccoConsulta->nombre = "% - Todos";
		
		if($wccocod != '%'){
//			$ccoConsulta->codigo = $wccocod;
//			$ccoConsulta = consultarCentroCosto($conex,$ccoConsulta, $wbasedato);

			$wccocod=explode("-",$wccocod);	//se extrae el codigo centro de costo
			$wccocod=$wccocod[0];
			
			$ccoConsulta = consultarCentroCosto($conex,$wccocod, $wbasedato);
			
		}	
		echo "<INPUT TYPE='hidden' NAME='wfecha' value='".$wfecha."'>";		
						
		if($ccoConsulta->nombre != "") {
			echo '<span class="subtituloPagina2">';
			echo "Cierre camas para ".$ccoConsulta->nombre." del dia ".$wfecha;
			echo "</span>";
			echo "<br>";
			echo "<br>";
				
			//Especificacion del error en caso de existir
			echo "<div id='mensajePantalla' style='display:none'>";
			echo "&nbsp;";
			echo "</div>";

			//Parametros y consulta de los indicadores
			$indicadorConsulta = new indicadorDTO();

			//Criterios de consulta
			$indicadorConsulta->codigoServicio = $wccocod;
			$indicadorConsulta->fecha = $wfecha;

			$consulta = consultarIndicadores($indicadorConsulta);
			
			//Si hay datos muestra la informacion
			if(sizeof($consulta) > 0){
				echo "<table align='center'>";

				//Encabezados de la tabla				
				echo "<tr align='center' class=encabezadoTabla>";
				echo "<td width='80' rowspan='2'>Servicio</td>";
				echo "<td width='60' rowspan='2'>Camas disponibles</td>";
				echo "<td width='60' rowspan='2'>Camas ocupadas</td>";
				echo "<td width='70' rowspan='2'>Pacientes dia anterior</td>";
				echo "<td colspan='5' align='center'>Ingresos</td>";
				echo "<td colspan='5'>Egresos</td>";
				echo "<td width='100' rowspan='2'>Ingresos y egresos del mismo dia</td>";
				echo "<td colspan='3'>Días de estancia egresos</td>";
				echo "<td width='80' rowspan='2'>Modificar</td>";
				echo "</tr>";
				echo "<tr align='center' class=encabezadoTabla>";				
				echo "<td>Urgencias</td>";
				echo "<td>Cirugia</td>";
				echo "<td>Admisiones</td>";
				echo "<td>Traslado</td>";
				echo "<td>Totales</td>";				
				echo "<td>Traslado</td>";
				echo "<td>M > 48 horas</td>";
				echo "<td>M < 48 horas</td>";
				echo "<td>Altas</td>";
				echo "<td>Totales</td>";				
				echo "<td>Traslados</td>";
				echo "<td>Altas y muertes</td>";
				echo "<td>Totales</td>";
				echo "</tr>";
				
				$cont1=0;
				foreach ($consulta as $ind){
					if(!empty($ind->codigoServicio)){
						$cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
						$cont1++;
							
						$ingresosTotales = $ind->cantidadIngresosUrgencias+$ind->cantidadIngresosCirugia+$ind->cantidadIngresosAdmisiones+$ind->cantidadIngresosTraslado;
						
						echo "<tr align='center' class='$clase'>";
						echo "<td id='$ind->codigoServicio'>$ind->codigoServicio</td>";
						echo "<td>$ind->camasDisponibles</td>";
						echo "<td>$ind->camasOcupadas</td>";
						echo "<td>$ind->pacientesDiaAnterior</td>";						
						echo "<td>$ind->cantidadIngresosUrgencias</td>";
						echo "<td>$ind->cantidadIngresosCirugia</td>";
						echo "<td>$ind->cantidadIngresosAdmisiones</td>";
						echo "<td>$ind->cantidadIngresosTraslado</td>";
						echo "<td>$ingresosTotales</td>";
						echo "<td>$ind->cantidadEgresosTraslado</td>";
						echo "<td>$ind->cantidadMuertesMayores</td>";
						echo "<td>$ind->cantidadMuertesMenores</td>";
						echo "<td>$ind->cantidadEgresosAlta</td>";
						echo "<td>$ind->cantidadEgresos</td>";
						echo "<td>$ind->cantidadIngresosYEgresos</td>";						
						echo "<td>$ind->diasEstanciaTraslado</td>";
						echo "<td>$ind->diasEstanciaAltasMuertes</td>";
						echo "<td>$ind->diasEstancia</td>";
						echo "<td><input type='radio' name='servicio' value='' onclick='javascript:mostrarEditar(".$indicadorConsulta->fecha.",$ind->codigoServicio,$ind->camasOcupadas,$ind->pacientesDiaAnterior,$ind->cantidadIngresos,$ind->cantidadEgresos,$ind->cantidadIngresosYEgresos,$ind->camasDisponibles,$ind->camasDisponiblesDiaAnterior,$ind->cantidadMuertesMayores,$ind->cantidadMuertesMenores,$ind->cantidadIngresosUrgencias,$ind->cantidadIngresosCirugia,$ind->cantidadIngresosAdmisiones,$ind->cantidadIngresosTraslado,$ind->cantidadEgresosTraslado,$ind->diasEstanciaTraslado,$ind->diasEstanciaAltasMuertes,$ind->cantidadEgresosAlta,$ind->diasEstancia);'></td>";
							
						echo "</tr>";
					}
				}
				
				echo "<tr><td align=center colspan=18><br><input type=button value='Regresar' onclick='javascript:inicio();'></td></tr>";				
				echo "</table>";
				
				//Se muestra cuando los usuarios van a modificar
				echo "<div id='pantallaModif' style='display:none' align='center'>";
				
				echo "<input type='hidden' name='wactualizar' value=''/>";
				echo "<input type='hidden' id='txFecha' name='txFecha'/>";
				echo "<input type='hidden' id='vrOcupadas'/>";
				echo "<input type='hidden' id='vrDisponibles'/>";
				echo "<input type='hidden' id='vrPacientesDiaAnt'/>";
				echo "<input type='hidden' id='hDisponiblesAnt'/>";
				echo "<input type='hidden' id='txDisponiblesAnt' name='txDisponiblesAnt'/>";
				echo "<input type='hidden' id='txServicio' name='txServicio'/>";				
				echo "<INPUT TYPE='hidden' NAME='wccocod' value='".$wccocod."'>";
								
				echo "<span class=subtituloPagina2>";
				echo "Modificar valores para el servicio <strong><span id='spServicio'></span></strong><br>";
				echo "</span></br>";
				
				echo "<table>";
				echo "<tr>";
				echo "<td width='200'>Camas disponibles</td>";
				echo "<td><input type='text' id='txDisponibles' name='txDisponibles' value='' size='2' maxlength='4' readonly></td>";
				echo "<td>Camas ocupadas</td>";
				echo "<td><input type='text' id='txOcupadas' name='txOcupadas' value='' size='2' maxlength='4' onchange='javascript:calcularDisponibles();'></td>";				
				echo "</tr>";
				echo "<tr>";
				echo "<td>Pacientes dia anterior</td>";
				echo "<td><input type='text' id='txAnterior' name='txAnterior' value='$ind->pacientesDiaAnterior' size='2' maxlength='4' onchange='javascript:calcularDisponiblesDiaAnterior();'></td>";
				echo "<td>Ingresos y egresos del mismo dia</td>";
				echo "<td><input type='text' id='txIngyEgresos' name='txIngyEgresos' value='$ind->cantidadIngresosYEgresos' size='2' maxlength='4' onchange='javascript:activarCambios();'></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=4 class=subtituloPagina2>";
				echo "Ingresos";	
				echo "</td>";			
				echo "</tr>";
				echo "<tr>";
				echo "<td>Urgencias</td>";
				echo "<td><input type='text' id='txIngresosU' name='txIngresosU' value='$ind->cantidadIngresosUrgencias' size='2' maxlength='4' onchange='javascript:sumaIngresos();'></td>";
				echo "<td>Cirugía</td>";
				echo "<td><input type='text' id='txIngresosC' name='txIngresosC' value='$ind->cantidadIngresosCirugia' size='2' maxlength='4' onchange='javascript:sumaIngresos();'></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>Admisiones</td>";
				echo "<td><input type='text' id='txIngresosA' name='txIngresosA' value='$ind->cantidadIngresosAdmisiones' size='2' maxlength='4' onchange='javascript:sumaIngresos();'></td>";
				echo "<td>Traslado</td>";
				echo "<td><input type='text' id='txIngresosT' name='txIngresosT' value='$ind->cantidadIngresosTraslado' size='2' maxlength='4' onchange='javascript:sumaIngresos();'></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
				echo "<td><b>Totales<b></td>";
				echo "<td><input type='text' id='txIngresos' name='txIngresos' value='$ind->cantidadIngresos' size='2' maxlength='4' readonly></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=4 class=subtituloPagina2>";
				echo "Egresos";	
				echo "</td>";			
				echo "</tr>";
				echo "<tr>";
				echo "<td>Traslado</td>";
				echo "<td><input type='text' id='txEgresosT' name='txEgresosT' value='$ind->cantidadEgresosTraslado' size='2' maxlength='4' onchange='javascript:sumaEgresos();'></td>";
				echo "<td>Muertes mayores a 48 horas</td>";
				echo "<td><input type='text' id='txMMayores' name='txMMayores' value='$ind->cantidadMuertesMayores' size='2' maxlength='4' onchange='javascript:sumaEgresos();'></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>Muertes menores a 48 horas</td>";
				echo "<td><input type='text' id='txMMenores' name='txMMenores' value='$ind->cantidadMuertesMenores' size='2' maxlength='4' onchange='javascript:sumaEgresos();'></td>";
				echo "<td>Altas</td>";
				echo "<td><input type='text' id='txEgresosA' name='txEgresosA' value='$ind->cantidadEgresosAlta' size='2' maxlength='4' onchange='javascript:sumaEgresos();'></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
				echo "<td><b>Totales</b></td>";
				echo "<td><input type='text' id='txEgresos' name='txEgresos' value='$ind->cantidadEgresos' size='2' maxlength='4' readonly></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=4 class=subtituloPagina2>";
				echo "Dias de estancia egresos";	
				echo "</td>";			
				echo "</tr>";
				echo "<tr>";
				echo "<td>Traslados</td>";
				echo "<td><input type='text' id='txDiasEstanciaT' name='txDiasEstanciaT' value='$ind->diasEstanciaTraslado' size='2' maxlength='4' onchange='javascript:sumaDias();'></td>";
				echo "<td>Altas y muertes</td>";
				echo "<td><input type='text' id='txDiasEstanciaAm' name='txDiasEstanciaAm' value='$ind->diasEstanciaAltasMuertes' size='2' maxlength='4' onchange='javascript:sumaDias();'></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
				echo "<td><b>Totales</b></td>";
				echo "<td><input type='text' id='txDiasEstancia' name='txDiasEstancia' value='$ind->diasEstancia' size='2' maxlength='4' readonly></td>";
				echo "</tr>";
				echo "<br>";
				echo "<tr><td align=center colspan=9><br><input type=button value='Modificar' onclick='javascript:actualizarIndicador();'/>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
				echo "</table>";
				
				echo "</div>";				
			} else {
				echo '<span class="subtituloPagina2">';
				echo "No se encontraron estadísticas";
				echo "</span>";
				echo "<br>";
				echo "<br>";
				
				echo "<div align=center colspan=9><input type=button value='Regresar' onclick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
			}
			echo "</div>";
		}
	}
	liberarConexionBD($conex);
}
?>
</body>
</html>