<html>

<head>
<title>MATRIX - [REPORTE MEDICAMENTOS POR APLICAR]</title>

<script type="text/javascript">

function inicio(){ 
	document.location.href='RepMedicamentosAplicar.php?wemp_pmla='+document.forms.forma.wemp_pmla.value; 
}

function cerrarVentana(){ 
	window.close(); 
}

/*****************************************************************************************************************************
* 
******************************************************************************************************************************/
function consultar(){ 
	var formulario = document.forms.forma;
 	
	document.location.href='RepMedicamentosAplicar.php?wemp_pmla='+formulario.wemp_pmla.value+'&waccion=a&wservicio='+formulario.wservicio.value
	+'&whabitacion='+formulario.whabitacion.value+'&whora='+formulario.whora.value; 
}
 
</script>

</head>

<body>
<?php
include_once("conex.php");
/*BS'D
 * REPORTE MEDICAMENTOS A APLICAR
 * Autor: Mauricio Sánchez Castaño.
 */
include_once("movhos/kardex.inc.php");

$wactualiz = " 1.0 02-Jun-09";
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
	echo "<form name='forma' action='RepMedicamentosAplicar.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}
	
	//FC para hacer las acciones
	switch ($waccion){
		case 'a':		//Consulta de los datos del reporte
			if(isset($wservicio) && isset($whabitacion) && isset($whora)){
				//Pantalla de consulta, la informacion va agrupada por habitacion, paciente, seguida por un listado de medicamentos
				echo '<span class="subtituloPagina2" align="center">';
				if($whabitacion == "%"){
					echo "Entrega de articulos para todas las habitaciones para la hora $whora:00";	
				} else {
					echo "Entrega de articulos para la habitacion $whabitacion para la hora $whora:00";
				}
				
				echo "</span><br><br>";
				
				$colUnidades = consultarUnidadesMedida();
				
				$colMedicamentosPendientes = consultarMedicamentosPendientesHorario($conex, $wbasedato, $wservicio, date("Y-m-d"), $whabitacion, $whora);
				$cuentaMedicamentosPendientes = count($colMedicamentosPendientes);

				if($cuentaMedicamentosPendientes > 0){
					//Cuerpo de la pagina
					echo "<table align='center'>";

					echo "<tr class='encabezadoTabla' align='center'>";

					//Encabezado
					echo "<td>Habitacion</td>";
					echo "<td>Historia</td>";
					echo "<td>Paciente</td>";
					echo "<td>Articulo</td>";
					echo "<td>Cantidad y unidades</td>";
						
					for($cont1 = 1; $cont1 <= 24; $cont1++){
						if($cont1 == $whora){
							echo "<td><font size=5>$cont1</font></td>";
						}else{
							echo "<td>$cont1</td>";
						}
					}
						
					$clase = "fila1";
					$cont2 = 1;
					
					foreach ($colMedicamentosPendientes as $articulo) {
						
						if($clase == "fila2"){
							$clase = "fila1";
						} else {
							$clase = "fila2";
						}
						
						echo "<tr class=$clase>";

						echo "<td>$articulo->habitacion</td>";
						echo "<td>$articulo->historia-$articulo->ingreso</td>";
						echo "<td>$articulo->paciente</td>";
						echo "<td>$articulo->codigoArticulo</td>";
						echo "<td>$articulo->dosis</td>";

						$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),$articulo->fechaInicioAdministracion,$articulo->horaInicioAdministracion,$articulo->horasFrecuencia);

						$cont1 = 1;	  //Conteo de

						while($cont1 <= 24){
							if(isset($arrAplicacion[$cont1]) && $arrAplicacion[$cont1] == '*'){
								if($cont1 == $whora){
									echo "<td class='celdaResaltadaRojo' align='center'><font size=5>*</font></td>";
								}else{
									echo "<td class='celdaResaltadaRojo' align='center'>*</td>";
								}
							} else {
								echo "<td>&nbsp;</td>";
							}
							$cont1++;
						}
						$cont2++;
						
						echo "</tr>";
					}
					echo "<tr>";
					echo "<td>";

					echo "</td>";
					echo "</tr>";

					echo "</table>";
					echo "</div>";
					
					echo "<div align=center>";
					echo "<br>";
					echo "<input type='button' value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'>";
					echo "</div>";

				}else{
					mensajeEmergente("No se encontraron medicamentos pendientes para la hora, el servicio y la habitacion especificada.");
					funcionJavascript("inicio();");
				}
			} else {
				mensajeEmergente("Por favor revise los parametros de entrada");
				funcionJavascript("inicio();");
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
			$colServiciosHospitalarios = consultarCentrosCostosHospitalarios();
			echo "<tr><td class='fila1'>Servicio</td>";
			echo "<td class='fila2' align='center'>";
			echo "<select id=wservicio name=wservicio>";
			foreach ($colServiciosHospitalarios as $servicio){
				echo "<option value='$servicio->codigo'>$servicio->codigo - $servicio->nombre</option>"; 
			}
			echo "</select>";
			echo "</td>";
			echo "</tr>";
				
			//Habitacion
			echo "<tr><td class='fila1'>Habitacion</td>";
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
				
			//Hora
			echo "<tr><td class='fila1'>Hora</td>";
			echo "<td class='fila2' align='center'>";
			echo "<select name='whora'>";
				
			$contHraInicio = 1;
			while ( $contHraInicio <= 24 ){
				echo "<option value='$contHraInicio'>$contHraInicio</option>";
				$contHraInicio++;
			}
			echo "</select>";
				
			echo "</td>";

			echo "</tr>";
				
			echo "</td>";
			echo "</tr>";
			echo "<div align='center'>";
			echo "<tr><td align=center colspan=2><input type=button value='Consultar' onclick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</div>";
			echo "</table>";
			break;
	}
}	
?>