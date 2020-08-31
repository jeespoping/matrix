<html>
<head>
<title>MATRIX - [REPORTE ENTREGA MEDICAMENTOS POR HORARIO]</title>

<script type="text/javascript">
	function inicio(){ 
		document.location.href='RepEntregaMedicamentosHorario.php?wemp_pmla='+document.forms.forma.wemp_pmla.value; 
	}
	
	function cerrarVentana()
	 {
      window.close()
     }

	
	function consultarHabitacionesOcupadas(){
		var servicio = document.forms.forma.wservicio.value;		
		document.forms.forma.submit();				
	}
	/*****************************************************************************************************************************
	* 
	******************************************************************************************************************************/
	 function consultar(){ 
	 	var formulario = document.forms.forma;

	 	//Valida que la hora inicial y final sean de menor a mayor respectivamente
	 	var horaInicial = parseInt(formulario.whoraini.value);
	 	var horaFinal = parseInt(formulario.whorafin.value);
	 	var diasDiferencia = parseInt(formulario.wtxtdias.value);

	 	if(diasDiferencia < 0 || diasDiferencia > 3){
			alert("Los dias de diferencia deben ser entre 1 y 3");
			return;
		}
	 	
		document.location.href='RepEntregaMedicamentosHorario.php?wemp_pmla='+formulario.wemp_pmla.value+'&waccion=a&wservicio='+formulario.wservicio.value
		+'&whabitacion='+formulario.whabitacion.value+'&whoraini='+formulario.whoraini.value+'&whorafin='+formulario.whorafin.value+'&wdiasdif='+diasDiferencia
		+'&whorario='+'';
	 }
	 
	 window.onload = function(){
		document.forms.forma.elements[ "wservicio" ].onchange = consultarHabitacionesOcupadas;
	 }
</script>

</head>

<body>
<?php
include_once("conex.php");
/*BS'D
 * REPORTE ENTREGA DE MEDICAMENTOS POR HORARIO 
 * Autor: Mauricio Sánchez Castaño.
 */
 
 //Actualizacion:
 //Octubre 29 de 2014 Jonatan Lopez
 //Se agrega validacion para la variable de fecha, si ya esta declarada en la url entonces tomara esa fecha, sino tomara la del script, ademas se cerrara la ventana
 //si el programa es llamado desde otro programa (Ej: gestionEnfermeria.php).
 //==========================================================
 //Julio 6 de 2012 Viviana Rodas
 // Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos de un grupo seleccionado y dibujarSelect //que dibuja el select con los centros de costos obtenidos de la primera funcion.

include_once("movhos/kardex.inc.php");

$wactualiz = "2012-07-06";
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
encabezado("Reporte entrega medicamentos por horario",$wactualiz,"clinica");

if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}else{
	//Conexion base de datos
	$conex = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	//Forma
	echo "<form name='forma' action='RepEntregaMedicamentosHorario.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

	//Fecha de consulta
	if(!isset($fecha)){
	$fecha = date("Y-m-d");  
	}else{
	$otro_origen = "ok";	
	}
	//$fecha = "2012-06-28";
	
	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}

	
	//FC para hacer las acciones
	switch ($waccion){
		case 'a':		//Consulta de los datos del reporte
			/**
			 * document.location.href='RepEntregaMedicamentosHorario.php?wemp_pmla='+formulario.wemp_pmla.value+'&waccion=a&wservicio='+formulario.wservicio.value
			+'&whabitacion='+formulario.whabitacion.value+'&whoraini='+formulario.whoraini.value+'&whorafin='+formulario.whorafin.value+'&wdiasdif='+diasDiferencia
			+'&whorario='+'';
			 */
			echo "<meta http-equiv='refresh' content='120;url=RepEntregaMedicamentosHorario.php?wemp_pmla=".$wemp_pmla."&wuser=".$wuser."&waccion=a&wservicio=".$wservicio."&whabitacion=".$whabitacion."&whoraini=".$whoraini."&whorafin=".$whorafin."&wdiasdif=".$wdiasdif."'>";
			if(isset($wservicio) && isset($whabitacion) && isset($whoraini) && isset($whorafin)){
				
				$fechaFinal = sumarDiasFecha($fecha,$wdiasdif);
				
//				echo "La fecha final es: '$fechaFinal'";
				
				//Pantalla de consulta, la informacion va agrupada por habitacion, paciente, seguida por un listado de medicamentos
				echo '<span class="subtituloPagina2" align="center">';
				if($whabitacion == "%"){
					echo "Entrega de articulos para todas las habitaciones desde el $fecha a las $whoraini:00 horas, hasta el $fechaFinal a las $whorafin:00 horas.";	
				} else {
					echo "Entrega de articulos para la habitacion $whabitacion desde el s a las $whoraini:00 horas, hasta el $fechaFinal a las $whorafin:00 horas.";
				}
				
				echo "</span><br><br>";
				
				$colUnidades = consultarUnidadesMedida();
				$mostrarArticulo = false;
				$historiaTemp = "";
				$fechaKardexTemp = "";
				$clase = "fila1";
				
				if ($wservicio=="")
				{
					$wservicio="%";
					//echo "----".$wservicio;
				}
				else
				{
				$wservicio1=explode("-",$wservicio);  //agregado
				$wservicio=$wservicio1[0];
				//echo "<br>****".$wservicio;
				}
				
				$colMedicamentosPendientes = consultarMedicamentosPendientesHorario($wservicio, $fecha, $whabitacion);
				
				$cuentaMedicamentosPendientes = count($colMedicamentosPendientes);

				if($wdiasdif > 0){
					if($whoraini == $whorafin || $whoraini > $whorafin){
						$horaInicia = $whoraini;
						if($whoraini == 2){
							$horaTermina = 24;
						} else {
							$horaTermina = $whorafin-2;
						}
					}
				} else {
					if($whoraini > $whorafin){
						$horaInicia = $whoraini;
						if($whoraini == 2){
							$horaTermina = 24;
						} else {
							$horaTermina = $whorafin;
						}
					} else {
						$horaInicia = $whoraini;
						$horaTermina = $whorafin;
					}
				}
					
				//Para cualquier valor de dias de diferencia si la hora inicial es menor que la hora final la iteracion es desde ini hasta fin
				if($whoraini < $whorafin){
					$horaInicia = $whoraini;
					$horaTermina = $whorafin;
				} else {
					$diferencia = ((24 - $whoraini) + 2 + $whorafin)/2;
				}
				
				//Se itera los n dias de diferencia desde la fecha actual hacia adelante
				$cont2 = 0; 
				while ($cont2 <= $wdiasdif){
					$fechaKardex = sumarDiasFecha($fecha,$cont2);
						
					if($cuentaMedicamentosPendientes > 0){
							
						echo "<table align='center'>";
							
						foreach ($colMedicamentosPendientes as $articulo){

							if($fechaKardexTemp != $fechaKardex){
								//Titulo con la fecha del kardex
								echo "<tr>";
								echo "<td colspan=4 width=900><br/>";
								echo "<span class='subtituloPagina2' align='center'>";
								echo "Fecha del kardex $fechaKardex";
								echo "</span><br><br>";
								echo "</td>";
								echo "</tr>";
							}

							$arrAplicacion = obtenerVectorAplicacionMedicamentos($fechaKardex,$articulo->fechaInicioAdministracion,$articulo->horaInicioAdministracion,$articulo->horasFrecuencia);

							$cont3 = 0;
							
							for($cont1 = $horaInicia; ($horaInicia > $horaTermina) ? $cont3 <= $diferencia : $cont1 <= $horaTermina;){
								if(isset($arrAplicacion[$cont1]) && $arrAplicacion[$cont1] == "*"){

									if($historiaTemp != $articulo->historia){
											
										//Paciente historia y habitación
										echo "<tr>";
										echo "<td colspan=4 width=900><br/>";
										echo "<span class='subtituloPagina2' align='center'>";
										echo "Habitación <b>$articulo->habitacion</b>. Paciente $articulo->historia-$articulo->ingreso $articulo->paciente.";
										echo "</span><br><br>";
										echo "</td>";
										echo "</tr>";

										echo "<tr class='encabezadoTabla' align='center'>";
											
										//Encabezado
										echo "<td>Articulo</td>";
										echo "<td>Cantidad y unidades</td>";
										echo "<td>Frecuencia</td>";
										echo "<td>Via</td>";
										echo "<td>Condición</td>";
										echo "<td>Hora</td>";
											
										echo "</tr>";
									}

									//Datos del articulo
									if($clase == "fila2"){
										$clase = "fila1";
									} else {
										$clase = "fila2";
									}

									echo "<tr class=$clase>";

									echo "<td>$articulo->codigoArticulo</td>";
									echo "<td>$articulo->dosis</td>";
									echo "<td>$articulo->frecuencia</td>";
									echo "<td>$articulo->via</td>";
									echo "<td>$articulo->condicion</td>";
									echo "<td>$cont1</td>";

									if($historiaTemp != $articulo->historia){
										echo "</tr>";
									}
									$historiaTemp  = $articulo->historia;
								}
								
								if($cont1 == 26){
									$cont1 = 2;
								} else {
									$cont1 += 2;
								}
								$cont3++;
							}
							$fechaKardexTemp = $fechaKardex;
						}
						echo "</table>";
							
						echo "</div>";
							

					}else{
						mensajeEmergente("No se encontraron medicamentos pendientes para el servicio, la habitacion y el rango de horas especificados.");
						if($otro_origen != 'ok'){
						funcionJavascript("inicio();");
						}else{
						funcionJavascript("cerrarVentana();");
						}
					}
					$cont2++;
				}
				echo "<div align=center>";
				echo "<br>";
				echo "<input type='button' value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'>";
				echo "</div>";
			} else {
				mensajeEmergente("Por favor revise los parametros de entrada");
				if($otro_origen != 'ok'){
				funcionJavascript("inicio();");
				}else{
				funcionJavascript("cerrarVentana();");
				}
			}//Cierre del cuerpo de la pagina

			break;
		default:		//Muestra la pantalla inicial			
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Ingrese los parámetros de consulta';
			echo "</span>";
			echo "<br>";
			echo "<br>";
			
			//Por Servicio
						
			//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		    $cco="Ccohos";
    		$sub="off";
	    	$tod="Todos";
		    $ipod="off";
		      //$cco=" ";
		    $centrosCostos = consultaCentrosCostos($cco);
					
			echo "<table align='center' border=0 width=410>";		
			$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wservicio");
					
			echo $dib;
			echo "</table>";	
				
			//echo"--".$wservicio;
			$wservicio1=explode("-",$wservicio);
			$wservicio=$wservicio1[0];
			//echo"<br>----ddd".$wservicio;
				
			//Habitacion
			echo "<table align=center width=406>";
			echo "<tr><td class='fila1' width=70>Habitacion</td>";
			echo "<td class='fila2' align='center'>";
				
			if(isset($wservicio)){
				$colHabitacionesPorServicio = consultarHabitacionesOcupadasServicio($wservicio);
			} else {
				$colHabitacionesPorServicio = array();
			}
				
			echo "<select id=whabitacion name=whabitacion>";
			echo "<option value='%'>Todas las habitaciones</option>";

			foreach ($colHabitacionesPorServicio as $habitaciones){
				echo "<option value='$habitaciones->codigo'>$habitaciones->codigo</option>";
			}

			echo "</select>";
				
			echo "</td>";
			echo "</tr>";

			//Desde
			echo "<tr><td class='fila1'>Desde</td>";
			echo "<td class='fila2' align='center'>";
			echo $fecha." a las ";
			
			echo "<select name='whoraini'>";
			$contHraInicio = 1;
			while ( $contHraInicio <= 24 ){
				if($contHraInicio % 2 == 0){
					echo "<option value='$contHraInicio'>$contHraInicio</option>";	
				}
				$contHraInicio++;
			}
			echo "</select>";
			
			echo "</td>";
			echo "</tr>";
			
			//Hasta
			echo "<tr><td class='fila1'>Hasta</td>";
			echo "<td class='fila2' align='center'>";
			echo "<input type='text' name='wtxtdias' id='wtxtdias' value='0' size='2' onkeypress='return validarEntradaEntera(event);'> dia(s) despues, ";
			
			echo "a las&nbsp;&nbsp;";
			echo "<select name='whorafin'>";
			$contHraInicio = 1;
			while ( $contHraInicio <= 24 ){
				if($contHraInicio % 2 == 0){
					echo "<option value='$contHraInicio'>$contHraInicio</option>";
				}
				$contHraInicio++;
			}
			echo "</select>";
				
			echo "</td>";

			echo "</tr>";
				
			echo "</table>";
			
			echo "<br/>";
			echo "<div align='center'>";
			echo "<tr><td align=center colspan=2><input type=button value='Consultar' onclick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</div>";
		break;
	}
}
?>