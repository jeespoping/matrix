<html>
<head>
<title>HCE - [IMPRESION]</title>

<!-- JQUERY para los tabs 
<link type="text/css" href="../../ui.all.css" rel="stylesheet" />
<link type="text/css" href="../../ui.datepicker.css" rel="stylesheet" />
<script type="text/javascript" src="../../jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../ui.core.js"></script>
<script type="text/javascript" src="../../ui.tabs.js"></script>
<script type="text/javascript" src="../../ui.draggable.js"></script>
<script type="text/javascript" src="../../ui.datepicker.js"></script>
 Fin JQUERY para los tabs -->

<script type="text/javascript">
/*****************************************************************************************************************************
 * Primera acción de la impresion de HCE, 
 ******************************************************************************************************************************/
function consultarHCE(){
	var tipoDocumento = document.getElementById("wseltdoc");
	var nroDocumento = document.getElementById("wnrodoc");

	var historia = document.getElementById("whistoria");
	var ingreso = document.getElementById("wingreso");
	
	if((tipoDocumento && tipoDocumento.value && nroDocumento && nroDocumento.value) || (historia && historia.value)){
		document.location.href = 'impresionHCE.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia.value+'&wingreso='+ingreso.value+
								'&wtipdoc='+tipoDocumento.value+'&wnrodoc='+nroDocumento.value;
	} else {
		alert("Por favor revise los criterios de consulta.");
	}
}
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function confirmarGeneracion(){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	
	if(historia && ingreso){
		document.location.href = 'generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha;
	} else {
		alert("No se encontró historia, ingreso y fecha en los parametros de entrada.");		
	}
}
/******************************************************************************************************************************
 *Redirecciona a la pagina inicial del kardex
 ******************************************************************************************************************************/
function inicio(){
	document.location.href='impresionHCE.php?wemp_pmla='+document.forms.forma.wemp_pmla.value;	
}
/******************************************************************************************************************************
 *Redirecciona a la pagina inicial del kardex
 ******************************************************************************************************************************/
function imprimir(){
	alert("En construcción");	
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
</script>

</head>

<body>

<?php
include_once("conex.php");
/*BS'D
 * IMPRESION DE HCE
 * Autor: Mauricio Sánchez Castaño.
 */
include_once("hce/impresionHce.inc.php");

$wactualiz = " 1.0 02-Ago-09";
$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false; 	
}else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;	
}

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
encabezado("Impresi&oacute;n HCE",$wactualiz,"clinica");
	
if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";
	
	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}else{
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	//Fecha grabacion
	$fechaActual = date("Y-m-d");
	
	//Base de datos, se generaliza de generar kardex
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	//Forma
	echo "<form name='forma' action='impresionHCE.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
	echo "<input type='hidden' name='wfechaActual' value='$fechaActual'>";

	$usuario = consultarUsuario($conex, $wuser);
	
	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}
	
	//FC para hacer las acciones
	switch ($waccion){
		case 'a':  //Con base en los criterios especificados, consulto los ingresos del paciente
			
			/**************************
			 * Inicio de la consulta
			 **************************/
			$tipoConsulta = '';
			$paciente = new pacienteImpresionDTO();
			
			if(!empty($wtipdoc) && !empty($wnrodoc)){
				$tipoConsulta = "c";
			}
			
			if(!empty($whistoria) && empty($wingreso)){
				$tipoConsulta = "a";
			}
			
			if(!empty($whistoria) && !empty($wingreso)){
				$tipoConsulta = "b";
			}
			
			switch($tipoConsulta){
				case 'a':	//Consulta por historia clínica con el ultimo ingreso
					$paciente = consultarPacienteHistoria($whistoria);
					break;
				case 'b':	//Consulta por historia clínica con el ingreso especificado por el usuario
					$paciente = consultarPacienteHistoriaIngreso($whistoria,$wingreso);
					break;
				case 'c':	//Consulta por tipo y numero de documento
					$paciente = consultarPacienteDocumento();
					break;
				default:
					break;
			}
			
			echo "<input type='hidden' name='whistoria' value='$paciente->historiaClinica'>";
			echo "<input type='hidden' name='wingreso' value='$paciente->ingresoHistoriaClinica'>";
			
//			var_dump($paciente);
			
			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo "Ingresos del paciente $paciente->nombre1 $paciente->nombre2 $paciente->apellido1 $paciente->apellido2.  Historia cl&iacute;nica $paciente->historiaClinica";
			echo "</span>";
			echo "<br>";
			echo "<br>";
			
			echo "<table align='center'>";
			
			echo "<tr class=encabezadoTabla>";
			echo "<td>N&uacute;mero de Ingreso</td>";
			echo "<td>Fecha y hora de ingreso</td>";
			echo "<td>Fecha y hora de egreso</td>";
			echo "<td>Impresi&oacute;n</td>";
			echo "</tr>";
			
			$clase = "fila1";
			
			foreach ($paciente->ingresos as $ingreso){
				if($clase == "fila2"){
					$clase = "fila1";
				} else {
					$clase = "fila2";
				}
				
				echo "<tr class='$clase'>";
				
				echo "<td>";
				echo $ingreso->ingresoHistoriaClinica;				
				echo "</td>";
				
				echo "<td>";
				echo "$ingreso->fechaData $ingreso->horaData";				
				echo "</td>";
				
				echo "<td>";
				echo "$ingreso->fechaAltaDefinitiva $ingreso->horaAltaDefinitiva";				
				echo "</td>";	
				
				echo "<td>";
				echo "<a href='pruebaImpresion3.php' target='_blank'>Imprimir</a>";				
				echo "</td>";
				
				echo "</tr>";				
			}
			
			echo "<tr><td colspan=4 align=center><input type='button' value='Regresar' id='regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</table>";
			
			break;
		case 'b': //Cuando ya hay un kardex creado se muestra la pantalla de modificación
			break;
		case 'c': //Actualización del kardex
			break;
		default:  //Muestra la pantalla inicial			
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Ingrese los parámetros de impresi&oacute;n';
			echo "</span>";
			echo "<br>";
			echo "<br>";

			//Historia clinica
			echo "<tr><td class='fila1' width=150>Historia cl&iacute;nica</td>";
			echo "<td class='fila2' align='center'>";
			echo "<INPUT TYPE='text' NAME='whistoria' SIZE=10 onKeyPress='return teclaEnterEntero(event,"."\"consultarHCE();\");' class='textoNormal'>";
			echo "</td>";
			echo "</tr>";
	
			//Ingreso
			echo "<tr><td class='fila1'>Ingreso</td>";
			echo "<td class='fila2' align='center'>";
			echo "<INPUT TYPE='text' NAME='wingreso' SIZE=5 onKeyPress='return teclaEnterEntero(event,"."\"consultarHCE();\");' class='textoNormal'>";
			echo "</td>";
			echo "</tr>";
			
			//Tipo y numero de documento
			$tiposDocumento = consultarTiposDocumento();
			echo "<tr><td class='fila1' width=150>Tipo de documento</td>";
			echo "<td class='fila2' align='center'>";
			echo "<select class=seleccionNormal id=wseltdoc align='center'>";
			echo "<option value=''>Seleccione un tipo de documento</option>";
			foreach ($tiposDocumento as $tipo){
				echo "<option value='$tipo->codigo'>$tipo->descripcion</option>";
			}
			echo "</select>"; 
			echo "</td>";
			echo "</tr>";
			
			//Numero de documento
			echo "<tr><td class='fila1' width=150>N&uacute;mero de documento</td>";
			echo "<td class='fila2' align='center'>";
			echo "<INPUT TYPE='text' id='wnrodoc' SIZE=15 onKeyPress='return teclaEnterEntero(event,"."\"consultarHCE();\");' class='textoNormal'>";
			echo "</td>";
			echo "</tr>";
			
			
			echo "<tr><td align=center colspan=4><br><input type=button value='Generar impresion' onclick='javascript:consultarHCE();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</table>";
			break;
	}
	liberarConexionBD($conex);
}
?>
</body>
</html>