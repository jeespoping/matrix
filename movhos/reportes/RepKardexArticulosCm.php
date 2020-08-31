<html>
<head>
<title>MATRIX - [REPORTE ARTICULOS CENTRAL DE MEZCLAS EN KARDEX]</title>

<script type="text/javascript">
function inicio(){ 
	document.location.href='RepKardexArticulosCm.php?wemp_pmla='+document.forms.forma.wemp_pmla.value; 
}
	
function consultar(){ 
	var formulario = document.forms.forma;

	document.location.href='RepKardexArticulosCm.php?wemp_pmla='+formulario.wemp_pmla.value+'&waccion=a&wservicio='+formulario.wservicio.value;
}

function go_saveas() {
    if (!!window.ActiveXObject) {
        var nombre = "respaldoKardexCentral";
        document.execCommand("SaveAs",false,nombre);
    } else if (!!window.netscape) {
        var r=document.createRange();
        r.setStartBefore(document.getElementsByTagName("head")[0]);
        var oscript=r.createContextualFragment('<script id="scriptid" type="application/x-javascript" src="chrome://global/content/contentAreaUtils.js"><\/script>');
        document.body.appendChild(oscript);
        r=null;
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            saveDocument(document);
        } catch (e) {
            //no further notice as user explicitly denied the privilege
        } finally {
            var oscript=document.getElementById("scriptid");    //re-defined
            oscript.parentNode.removeChild(oscript);
        }
    }
}

</script>

</head>

<body>
<?php
include_once("conex.php");
/*BS'D
 * REPORTE CONTINGENCIA KARDEX 
 * Autor: Mauricio S�nchez Casta�o.
 *
 *  Actualizacion: Julio 6 de 2012 (Viviana Rodas) Se agregaron las funciones consultaCentrosCostos y dibujarSelect que listan los centros de
 * costos en orden alfabetico, de un grupo seleccionado y dibujarSelect que construye el select de dichos centros de costos
 */
include_once("movhos/kardex.inc.php");

$wactualiz = " 2012-07-06";

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
encabezado("Reporte articulos central de mezclas en kardex",$wactualiz,"clinica");

if(!$usuarioValidado){
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
	echo "<form name='forma' action='RepKardexArticulosCm.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

	//Estrategia de FC con par�metro waccion
	if(!isset($waccion)){
		$waccion = "";
	}
    
	if ($wservicio=="")
	{
		$wservicio="%";
	}
	else
	{
		$wservicio1=explode("-",$wservicio);
		$wservicio=trim($wservicio1[0]);
	}
	
			
	//FC para hacer las acciones
	switch ($waccion){
		case 'a':		//Consulta de los datos del reporte
		
			if(isset($wservicio)){
				//Pantalla de consulta, la informacion va agrupada por habitacion, paciente, seguida por un listado de medicamentos
				echo '<span class="subtituloPagina2" align="center">';
				echo "Listado de articulos por paciente. Fecha: ".date("Y-m-d")." Hora: ".date("H:i:s");				
				echo "</span><br><br>";
				
				
				echo "<div id=lnkGrabar><a href='#1' onclick='go_saveas();return false'>Grabar copia local</a></div><br/>";
				
				$colUnidades = consultarUnidadesMedida();
				$mostrarArticulo = false;
				$historiaTemp = "";
				$clase = "fila1";
				
				
				
			//	$coleccion = consultarListadoArticulosPacientesCentral($wservicio, date("Y-m-d"));   original
		$coleccion = consultarListadoArticulosPacientesCentral($wservicio, "2012-06-28");
				
				$cuentaColeccion = count($coleccion);

				if($cuentaColeccion > 0){
					
					echo "<table align='center'>";
					
					foreach ($coleccion as $articulo) {
						if($historiaTemp != $articulo->historia){
							//Paciente historia y habitaci�n
							echo "<tr>";
							echo "<td colspan=4 width=900><br/>";
							echo "<span class='subtituloPagina2' align='center'>";
							echo "Habitaci�n <b>$articulo->habitacion</b>. Paciente $articulo->historia-$articulo->ingreso $articulo->paciente.";
							echo "</span><br><br>";
							echo "</td>";
							echo "</tr>";

							echo "<tr class='encabezadoTabla' align='center'>";

							//Encabezado
							echo "<td>Articulo</td>";
							echo "<td>Dosis</td>";
							echo "<td>Frecuencia</td>";
							echo "<td>Fecha y hora de inicio</td>";
							echo "<td>Cantidad a <br>dispensar</td>";
							echo "<td>Cantidad <br>dispensada</td>";
							echo "<td>Dosis <br>m&aacute;ximas</td>";
							echo "<td>Dias de <br>tratamiento</td>";
							echo "<td>Observaciones</td>";
								
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
						echo "<td>$articulo->horasFrecuencia Horas</td>";
						echo "<td>$articulo->fechaInicioAdministracion a las $articulo->horaInicioAdministracion</td>";
						echo "<td align='center'>$articulo->cantidadADispensar</td>";
						echo "<td align='center'>$articulo->cantidadDispensada</td>";
						echo "<td align='center'>$articulo->dosisMaximas</td>";
						echo "<td align='center'>$articulo->diasTratamiento</td>";
						echo "<td>$articulo->observaciones</td>";

						if($historiaTemp != $articulo->historia){
							echo "</tr>";
						}
						$historiaTemp  = $articulo->historia;
					}
					echo "</table>";
					
					echo "</div>";
					
					echo "<div align=center>";
					echo "<br>";
					echo "<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'>";
					echo "</div>";

				}else{
					mensajeEmergente("No se encontraron registros.");
//					funcionJavascript("inicio();");
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
			echo 'Ingrese los par�metros de consulta';
			echo "</span>";
			echo "<br>";
			echo "<br>";
			
			//Por Servicio
			
			//*********************llamado a las funciones que listan los centros de costos y la que dibuja el select************************
			$cco="Ccohos";
			$sub="off";
			$tod="Todos";
			//$cco=" ";
			$ipod="off";
			$centrosCostos = consultaCentrosCostos($cco);
			echo "<center><table align='center' border=0 >";
			$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod,"wservicio");
					
			echo $dib;
			echo "</table></center>";
			
		
			echo "<br/>";
			echo "<div align='center'>";
			echo "<tr><td align=center colspan=2><input type=button value='Consultar' onclick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</div>";
		break;
	}
}
?>