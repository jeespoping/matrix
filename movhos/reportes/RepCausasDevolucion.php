<html>

<head>
  <title>MATRIX - [REPORTE CAUSAS DEVOLUCION]</title>
  
</head>

<body>

<script type="text/javascript">
function enter() { document.forms.forma.submit(); }
function inicio(){ 	document.location.href='RepCausasDevolucion.php?wemp_pmla='+document.forma.wemp_pmla.value; }
</script>
	
<?php
include_once("conex.php");

/*BS'D
 * Reporte recibos devoluciones
 * 
 * Autor: Mauricio Sánchez Castaño. 
 */

//Clases 
class devolucionDTO {
	var $consecutivo;	
	var $numeroDevolucion;
	var $justificacionDevolucion;
	var $servicio;
	var $fechaRecibo;
	var $horaRecibo;
	var $usuario;
}

class justificacionDTO {
	var $codigo;	
	var $descripcion;
}

class registroDTO {
	var $numeroDevolucion;
	var $codigoUsuarioRecibe;
	var $nombreUsuarioRecibe;
	var $fechaRecibo;
	var $horaRecibo;
	var $justificacion;
	var $servicio;
}

function consultarDevoluciones($devolucion,$wfechaini,$wfechafin){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT DISTINCT
			Devcon, Devfre, Devhre, Denori, Devjud, Cconom, Devlin 
		FROM
			".$wbasedato."_000028, ".$wbasedato."_000035, ".$wbasedato."_000011 
		WHERE 
			".$wbasedato."_000028.Fecha_data BETWEEN '".$wfechaini."' AND '".$wfechafin."'
			AND Dencon = Devcon
			AND Denori = Ccocod
			AND SUBSTRING_INDEX( Devjud, '-', 1 ) LIKE '".$devolucion->justificacionDevolucion."'
			AND Denori LIKE '".$devolucion->servicio."'
		;";
	
	if($devolucion->numeroDevolucion && $devolucion->numeroDevolucion != '%'){
		$q = "SELECT DISTINCT
			Devcon, Devfre, Devhre, Denori, Devjud, Cconom, Devlin 
		FROM
			".$wbasedato."_000028, ".$wbasedato."_000035, ".$wbasedato."_000011
		WHERE 
			Devcon = '".$devolucion->numeroDevolucion."'
			AND Denori = Ccocod
			AND Dencon = Devcon
		;";
	}
	
//	echo $q;
	
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	$coleccion = array();
	
	if ($num > 0)
	{
		$cont1 = 0;
		while($cont1 < $num){
			$resultSet = mysql_fetch_array($res);

			$registro = new registroDTO();

			$registro->numeroDevolucion 		= $resultSet['Devcon']." - ".$resultSet['Devlin'];
			$registro->fechaRecibo 				= $resultSet['Devfre'];
			$registro->horaRecibo 				= $resultSet['Devhre'];			
			$registro->justificacionDevolucion	= $resultSet['Devjud'];
			$registro->servicio 				= $resultSet['Denori']." - ".$resultSet['Cconom'];			

			$coleccion[] = $registro;
			$cont1++;
		}
	}
	return $coleccion;
}

function consultarCentrosCostos(){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			Ccocod, Cconom 
		FROM 
			".$wbasedato."_000011
		ORDER BY Ccocod 
		;";
	
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	$coleccion = array();	
	$cont1 = 0;
	
	while($cont1 < $num){
		$resultSet = mysql_fetch_array($res);

		$consulta = new centroCostosDTO();
		
		$consulta->codigo = $resultSet['Ccocod'];
		$consulta->nombre = strtoupper($resultSet['Cconom']);
		
		$coleccion[] = $consulta;
		$cont1++;
	}
	return $coleccion;
}

function consultarUsuariosQueRecibenDevoluciones(){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			Codigo, Descripcion 
		FROM 
			usuarios
		WHERE 
			Ccostos IN ('1050','1051')
			AND Activo = 'A'
		ORDER BY Descripcion";
	
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	$coleccion = array();	
	$cont1 = 0;
	
	while($cont1 < $num){
		$resultSet = mysql_fetch_array($res);

		$consulta = new Usuario();
		
		$consulta->codigo = $resultSet['Codigo'];
		$consulta->descripcion = strtoupper($resultSet['Descripcion']);
		
		$coleccion[] = $consulta;
		$cont1++;
	}
	return $coleccion;
}

function consultarJustificacionesDevolucion(){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT 
			Juscod, Jusdes  
		FROM 
			".$wbasedato."_000023 
		WHERE 
			Justip = 'D' 
			AND Jusest = 'on'";
	
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	$coleccion = array();	
	$cont1 = 0;
	
	while($cont1 < $num){
		$resultSet = mysql_fetch_array($res);

		$consulta = new justificacionDTO();
		
		$consulta->codigo = $resultSet['Juscod'];
		$consulta->descripcion = strtoupper($resultSet['Jusdes']);
		
		$coleccion[] = $consulta;
		$cont1++;
	}
	return $coleccion;
}

/*
 * Inicio aplicacion
 */
include_once("root/comun.php");

$wactualiz = " 25-06-2012";                      // Aca se coloca la ultima fecha de actualizacion de este programa //

if (!isset($user)){
	if (!isset($_SESSION['user'])){
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
	echo "<form name='forma' action='RepCausasDevolucion.php' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	//Al principio se mostrará al usuario las dos posibles opciones de proceso que tiene: Proceso de cambio destino / anulación o de cambios
	//en la lista de materiales
	encabezado("REPORTE CAUSAS DE DEVOLUCION POR SERVICIO", $wactualiz, "clinica");
	
	if(!isset($wfechaini) && !isset($wfechafin) && !isset($numdevolucion) && !isset($codjustificacion) && !isset($wccocod)){

		//Cuerpo de la pagina
		echo "<table align='center' border=0 width=600>";
			
		echo '<span class="subtituloPagina2">';
		echo "Ingrese los parámetros de consulta";
		echo "</span>";

		//Número devolución
		echo "<tr><td class='fila1' width=110>Número devolución</td>";
		echo "<td class='fila2' align='center' >";
		echo "<input type='text' size='8' name='numdevolucion' id='numdevolucion' onkeypress='return validarEntradaEntera(event);' class='textoNormal'>";
		echo "</td>";
		echo "</tr>";
			
		//Fecha inicial
		echo "<tr><td class='fila1' width=110>Fecha inicial</td>";
		echo "<td class='fila2' align='center' >";
		campoFecha("wfechaini");
		echo "</td>";
		echo "</tr>";
		
		//Fecha final
		echo "<tr><td class='fila1' width=110>Fecha final</td>";
		echo "<td class='fila2' align='center' >";
		campoFecha("wfechafin");
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		//Servicio
				
		$cco="Ccohos";
		$sub="off";
		$tod="Todos";
		//$cco=" ";
		$ipod="off";
		$centrosCostos = consultaCentrosCostos($cco);
					
		echo "<table align='center' border=0 width=600>";		
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wccocod");
					
		echo $dib;
		echo "</table>";		
					 			
		
		//Justificación
		$justificaciones = consultarJustificacionesDevolucion();
		
		echo "<table align='center' border=0 width=600>";
		echo "<tr><td class='fila1' width=110>Justificación</td>";
		echo "<td class='fila2' align='center'>";
		echo "<select name='codjustificacion'>";
		echo "<option value='%'>Todas las justificaciones</option>";
		
		foreach ($justificaciones as $justificacion){			
			echo "<option value='".$justificacion->codigo."'>".$justificacion->codigo." - ".$justificacion->descripcion."</option>";
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";
		
		//echo "<br><br>";		
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		
		echo "</br>";
		echo "<div align='center'>";		
		echo "<tr><td align=center colspan=19><input type=button value='Consultar' onclick='javascript:enter();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
		echo "</div>";
		
	} else {
	
			$wser1=explode("-",$wccocod);
			$wccocod=$wser1[0]; 
			
		
		echo "<INPUT TYPE='hidden' NAME='wfechaini' value='".$wfechaini."'>";
		echo "<INPUT TYPE='hidden' NAME='wfechafin' value='".$wfechafin."'>";
		echo "<INPUT TYPE='hidden' NAME='numdevolucion' value='".$numdevolucion."'>";

		//Parametros y consulta de los indicadores
		$registroConsulta = new registroDTO();

		//Criterios de consulta
		$registroConsulta->numeroDevolucion = empty($numdevolucion) ? '%' : $numdevolucion;
		$registroConsulta->justificacionDevolucion = empty($codjustificacion) ? '%' : $codjustificacion;
		$registroConsulta->servicio = empty($wccocod) ? '%' : $wccocod;

		$consulta = consultarDevoluciones($registroConsulta,$wfechaini,$wfechafin);
			
		echo "<span class=subtituloPagina2>::NOTA::Para la columna devolución se muestra el número de línea de la devolución ya que la justificación puede ser diferente para cada una de ellas.</span>";
		
		//Si hay datos muestra la informacion
		if(sizeof($consulta) > 0){
			echo "<table align='center'>";

			//Encabezados de la tabla
			echo "<tr align='center' class=encabezadoTabla>";
			echo "<td>Devolucion número - línea</td>";
			echo "<td>Fecha recibo</td>";
			echo "<td>Hora recibo</td>";
			echo "<td>Justificacion</td>";
			echo "<td>Servicio</td>";
			echo "</tr>";

			$cont1=0;
			foreach ($consulta as $reg){
					
				if(!empty($reg->numeroDevolucion)){
					$cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
					$cont1++;
						
					echo "<tr align='center' class=$clase>";

					echo "<td>$reg->numeroDevolucion</td>";
					echo "<td>$reg->fechaRecibo</td>";
					echo "<td>$reg->horaRecibo</td>";
					echo "<td>$reg->justificacionDevolucion</td>";
					echo "<td>$reg->servicio</td>";
						
					echo "</tr>";
				}
			}
			echo "<tr><td align=center colspan=18><br><input type=button value='Regresar' onclick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
			echo "</table>";

		} else {
			echo "<span class=subtituloPagina2>No se encontraron devoluciones</span>";
			echo "<div align=center colspan=9><br><input type=button value='Regresar' onclick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
		}
		echo "</div>";
	}
	liberarConexionBD($conex);
}
?>
</body>
</html>