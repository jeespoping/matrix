<html>

<head>
<title>MATRIX - [REPORTE MODIFICIACIONES KARDEX]</title>

<script type="text/javascript">

function inicio(){ 
	document.location.href='RepKardexModificados.php?wemp_pmla='+document.forms.forma.wemp_pmla.value; 
}

function cerrarVentana(){ 
	window.close(); 
}

/*****************************************************************************************************************************
* Invocacion generica del calendario para la fecha de realización examen
******************************************************************************************************************************/
function verKardex(historia, ingreso, fecha){

	if(historia && ingreso && fecha){
		window.open('../procesos/generarKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value
					+'&waccion=b'+'&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha,
					'generarKardex','width=1200,toolbar=no,menubar=no,height=700,resizable=yes,location=no,scrollbars=yes'); 
	}
}

/*****************************************************************************************************************************
* 
******************************************************************************************************************************/
function consultar(){ 
	var formulario = document.forms.forma;
 	
	document.location.href='RepKardexModificados.php?wemp_pmla='+formulario.wemp_pmla.value+'&waccion=a&wservicio='+formulario.wservicio.value; 
}
 
</script>

</head>

<body>

<?php
include_once("conex.php");
/*BS'D
 * REPORTE KARDEX CAMBIADOS Y KARDEX NO CAMBIADOS
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
encabezado("Reporte kardex modificados",$wactualiz,"clinica");

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
	echo "<form name='forma' action='RepKardexModificados.php' method='post'>";
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
			if(isset($wservicio)){
				$wfecha = date("Y-m-d");
				$colServiciosHospitalarios = consultarCentrosCostosActivos();

				$wnomServicio = "";
				foreach ($colServiciosHospitalarios as $servicio){
					if($servicio->codigo == $wservicio){
						$wnomServicio = $servicio->descripcion;
						break; 
					}
				}
					
				//Pantalla de consulta, la informacion va agrupada por habitacion, paciente, seguida por un listado de medicamentos
				echo '<span class="subtituloPagina2" align="center">';
				echo "Kardex modificados y no modificados para el dia $wfecha en $wnomServicio";
				echo "</span><br><br>";
				
				$colUnidades = consultarUnidadesMedida();
				
				$coleccion = consultarKardexModificadosFecha($conex, $wbasedato, $wservicio, date("Y-m-d"));
				
				if(count($coleccion) > 0){
					
					//Cuerpo de la pagina
					echo "<table align='center'>";

					echo "<tr class='encabezadoTabla' align='center'>";

					//Encabezado
					echo "<td>Habitaci&oacute;n</td>";
					echo "<td>Historia</td>";
					echo "<td>Paciente</td>";
					echo "<td>Descripcion</td>";
					echo "<td>Ir al kardex</td>";
					
					echo "</tr>";
					
					$clase = "fila1";
					$cont2 = 1;
					
					foreach ($coleccion as $detalle) {
						
						if($clase == "fila2"){
							$clase = "fila1";
						} else {
							$clase = "fila2";
						}
						
						echo "<tr class=$clase>";

						echo "<td>$detalle->habitacion</td>";
						echo "<td>$detalle->historia-$detalle->ingreso</td>";
						echo "<td>$detalle->paciente</td>";
						echo "<td>$detalle->detalle</td>";
						echo "<td align=center><a href='javascript:verKardex($detalle->historia,$detalle->ingreso,\"$detalle->fecha\");'><img src='../../images/medical/root/grabar.png'></a>&nbsp;";

						echo "</tr>";
					}
					
					echo "</table>";
					echo "</div>";
					
					echo "<div align=center>";
					echo "<br>";
					echo "<input type='button' value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'>";
					echo "</div>";

				}else{
					mensajeEmergente("No se encontró información para los criterios especificados.");
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

			echo "</table>";
			
			echo "<div align='center'>";
			echo "<input type=button value='Consultar' onclick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'>";
			echo "</div>";
			break;
	}
}	
?>
</body>
</html>