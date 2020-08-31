<html>
<head>
<title>MATRIX - [MODIFICACIONES A LA BITACORA CTC]</title>
</head>

<body>

<script type="text/javascript">
function inicio(){ document.forms.forma.submit(); }
function cerrarVentana() { cerrarVentana(); }

function consultar(){
	document.location.href='cambiosBitacoraCTC.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a'
		+'&wconsecutivo=' + document.forms.forma.wconsecutivo.value;
}

function actualizar(){
	document.location.href='cambiosBitacoraCTC.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'
		+'&wconsecutivo=' + document.forms.forma.wconsecutivo.value+'&wvrfac=' + document.forms.forma.wvrfac.value
		+'&wnrofac=' + document.forms.forma.wnrofac.value+'&wfefac=' + document.forms.forma.wfefac.value;
}
</script>

<?php
include_once("conex.php");
include_once("root/comun.php");

//Definicion de clases
class BitacoraCTC{
	var $consecutivo;
	var $historia;
	var $ingreso;
	var $tipoDocumento;
	var $nroDocumento;
	var $nombrePaciente;
	
	var $numeroFactura;
	var $valorFactura;
	var $fechaFactura;
}

//Metodos de consulta
function consultarBitacoraPorConsecutivo($bitacora){
	global $conex;
	global $wbasedato;
	
	$registro = new BitacoraCTC();

	$info = array();
	
	//".$wbasedato."_000069
	
	$q = "SELECT 
			Consecutivo, Historia_Clinica, Ingreso_historia_clinica,Identificacion_paciente, Paciente, Numero_de_factura_ctc, Fecha_factura, Valor_factura   
		FROM 
			".$wbasedato."_000057
		WHERE 
			Consecutivo = $bitacora->consecutivo";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
//	echo $q;
	
	if ($num > 0)
	{
		$info = mysql_fetch_array($res);
		
		$vecIdentificacion = explode("-",$info['Identificacion_paciente']);
		
		/*
		//Consecutivo, 
		 * Historia_Clinica, 
		 * Ingreso_historia_clinica,
		 * Identificacion_paciente, 
		 * Paciente, 
		 * Numero_de_factura_ctc, 
		 * Fecha_factura, 
		 * Valor_factura
		*/
		
		$registro->consecutivo		= 	$info['Consecutivo'];
		$registro->historia 		= 	$info['Historia_Clinica'];
		$registro->ingreso 			= 	$info['Ingreso_historia_clinica'];
		$registro->tipoDocumento 	= 	$vecIdentificacion[0];
		$registro->nroDocumento 	= 	$vecIdentificacion[1];
		$registro->nombrePaciente	=	$info['Paciente'];
		
		$registro->valorFactura 	= 	$info['Valor_factura'];
		$registro->numeroFactura 	= 	$info['Numero_de_factura_ctc'];
		$registro->fechaFactura 	=	$info['Fecha_factura'];
	}
	
	return $registro;	
}

function actualizarBitacora($bitacora){
	global $conex;
	global $wbasedato;
	
	$exito = false;
	
	$info = array();
	
	//".$wbasedato."_000069
	
	$q = "UPDATE ".$wbasedato."_000057 SET  
			Numero_de_factura_ctc = '$bitacora->numeroFactura', 
			Fecha_factura = '$bitacora->fechaFactura', 
			Valor_factura = '$bitacora->valorFactura'  
		WHERE 
			Consecutivo = $bitacora->consecutivo";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
//	echo $q;
	
	if (mysql_affected_rows() > 0)
	{
		$exito = true;
	}
	
	return $exito;	
}


$wactualiz = " 1.0 26/Abril/2009";
/***********************************************************************************************************
 *          CAMBIOS BITACORA CTC               							   							       *
 * 																						   				   *
 * Creado: 26-Abr-2009 (Msanchez):  -									      							   *
 ***********************************************************************************************************/
if (!isset($user) || !isset($_SESSION['user'])){
	terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
}else{
	//Se verifica que se envie codigo de empresa
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	} else {
		$conex = obtenerConexionBD("matrix");

		//Calculo de fecha y hora actual, es opcional
		$wfecha=date("Y-m-d");
		$whora = (string)date("H:i:s");

		$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
		$winstitucion = $institucion->nombre;

		$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

		encabezado("MODIFICACIONES A LA BITACORA CTC", $wactualiz, "clinica");

		echo "<form name='forma' action='cambiosBitacoraCTC.php' method=post>";

		echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

		//Se extrae el codigo del usuario que ha ingresado
		if (strpos($user,"-") > 0){
			$wusuario = substr($user,(strpos($user,"-")+1),strlen($user));
		}

		if(!isset($waccion)){
			$waccion = "";
		}
		
		//FC para hacer las acciones
		switch ($waccion){
			case 'a':	//Consulta la bitacora de acuerdo a los dos parametros enviados
				//Composicion del objeto bitacora 
				$bitacoraConsulta = new BitacoraCTC();
				$bitacoraRespuesta = new BitacoraCTC();
				
				$bitacoraConsulta->consecutivo = "".$wconsecutivo;
				
				if(!empty($bitacoraConsulta->consecutivo)){
					$bitacoraRespuesta = consultarBitacoraPorConsecutivo($bitacoraConsulta);
				}
				
//				var_dump($bitacoraRespuesta);
				
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";

				if(!empty($bitacoraRespuesta->nombrePaciente)){
					//Oculto consecutivo
					echo "<input type='hidden' name='wconsecutivo' class='textoNormal' value='$bitacoraRespuesta->consecutivo'>";
					
					//Subtitulo
					echo '<span class="subtituloPagina2">';
					echo "Datos factura CTC paciente $bitacoraRespuesta->nombrePaciente.";
					echo "</span>";
					echo "<br>";
					echo "<br>";

					//Consecutivo
					echo "<tr><td class='fila1' width='120'>Consecutivo</td>";
					echo "<td class='fila2' align='center'>";
					echo "$bitacoraRespuesta->consecutivo";
					echo "</td>";
					echo "</tr>";
					
					//Historia 
					echo "<tr><td class='fila1' width='120'>Historia - ingreso</td>";
					echo "<td class='fila2' align='center'>";
					echo "$bitacoraRespuesta->historia - $bitacoraRespuesta->ingreso";
					echo "</td>";
					echo "</tr>";
					
					//Documento paciente
					echo "<tr><td class='fila1' width='180'>Tipo y numero documento </td>";
					echo "<td class='fila2' align='center'>";
					echo "$bitacoraRespuesta->tipoDocumento - $bitacoraRespuesta->nroDocumento";
					echo "</td>";
					echo "</tr>";
					
					//Numero de factura
					echo "<tr><td class='fila1' width='120'>Numero</td>";
					echo "<td class='fila2' align='center'>";
					echo "<INPUT TYPE='text' NAME='wnrofac' class='textoNormal' value='$bitacoraRespuesta->numeroFactura'>";
					echo "</td>";
					echo "</tr>";
						
					//Fecha factura
					echo "<tr>";
					echo "<td class='fila1'>Fecha</td>";
					echo "<td class='fila2' align='center'>";
					campoFechaDefecto("wfefac",$bitacoraRespuesta->fechaFactura);
					echo "</td>";
					
					//Valor factura
					echo "<tr>";
					echo "<td class='fila1'>Valor</td>";
					echo "<td class='fila2' align='center'>";
					echo "<INPUT TYPE='text' NAME='wvrfac' class='textoNormal' value='$bitacoraRespuesta->valorFactura' onkeypress='return validarEntradaEntera(event);'>";
					echo "</td>";

					echo "</tr>";

					echo "<tr><td align=center colspan=4><br><input type=button value='Actualizar' onclick='javascript:actualizar();'> | <input type=button value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";
				} else {
					//Subtitulo
					echo '<span class="subtituloPagina2">';
					echo 'No se encontr&oacute; informaci&oacute;n de bit&aacute;cora CTC.';
					echo "</span>";
					echo "<br>";
					echo "<br>";
					
					echo "<tr><td align=center colspan=4><br><input type=button value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";
				}
				
				echo "</table>";
				
				break;
			case 'b':	//Actualizar bitacora ctc
				$bitacoraActualizacion = new BitacoraCTC();
				
				$bitacoraActualizacion->consecutivo = $wconsecutivo;
				$bitacoraActualizacion->valorFactura = $wvrfac;
				$bitacoraActualizacion->numeroFactura = $wnrofac;
				$bitacoraActualizacion->fechaFactura = $wfefac;
				
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";
				
				if(isset($wconsecutivo) && $wconsecutivo != ""){
					if(actualizarBitacora($bitacoraActualizacion)){
						//Subtitulo
						echo '<span class="subtituloPagina2">';
						echo 'Los datos se han actualizado con exito';
						echo "</span>";
						echo "<br>";
						echo "<br>";
					} else {
						//Subtitulo
						echo '<span class="subtituloPagina2">';
						echo 'Los datos se no se actualizaron.  Intente nuevamente';
						echo "</span>";
						echo "<br>";
						echo "<br>";
					}
				}
				echo "<tr><td align=center colspan=4><br><input type=button value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";
				echo "</table>";
				
				break;
			default:	//Filtro de pantalla o pantalla inicial
				
				$colTipos = consultarTiposDocumento();
				
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";

				//Ingreso de fecha de consulta
				echo '<span class="subtituloPagina2">';
				echo 'Ingrese los par&aacute;metros de consulta';
				echo "</span>";
				echo "<br>";
				echo "<br>";

				//Consecutivo
				echo "<tr><td class='fila1'>Consecutivo</td>";
				echo "<td class='fila2' align='center'>";
				echo "<INPUT TYPE='text' NAME='wconsecutivo' SIZE=10 onkeypress='return validarEntradaEntera(event);' class='textoNormal'>";
				echo "</td>";
				echo "</tr>";
					
				echo "<tr><td align=center colspan=4><br><input type=button value='Consultar' onclick='javascript:consultar();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";
				echo "</table>";

				break;
		}
	}
}
liberarConexionBD($conex);
?>
</body>
</html>
