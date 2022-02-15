<html>

<head>
  <title>MATRIX - [REPORTE RECIBO DE DEVOLUCIONES]</title>
</head>

<body>

<script type="text/javascript">
function enter() { document.forms.forma.submit(); }
function inicio(){ 	document.location.href='RepRecibosDevoluciones.php?wemp_pmla='+document.forma.wemp_pmla.value; }
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
	var $fechaRecibo;
	var $horaRecibo;
	var $usuario;
}

class registroDTO{
	var $numeroDevolucion;
	var $codigoUsuarioRecibe;
	var $nombreUsuarioRecibe;
	var $fechaRecibo;
	var $horaRecibo;
}

function consultarDevoluciones($devolucion,$wfechaini,$wfechafin){
	global $wbasedato;
	global $conex;
	
	$q = "SELECT DISTINCT
		  		Devcon, Devfre, Devhre, Devusu, Descripcion, Devlin
		FROM
				".$wbasedato."_000028, usuarios
		WHERE 
				Codigo = Devusu 
				AND Devusu IS NOT NULL
				AND Devusu <> ''
				AND Devfre BETWEEN '".$wfechaini."' AND '".$wfechafin."'
				AND Devusu LIKE '".$devolucion->codigoUsuarioRecibe."'
				AND Devcon LIKE '".$devolucion->numeroDevolucion."'
		;";
	
	if($devolucion->numeroDevolucion && $devolucion->numeroDevolucion != '%'){
		$q = "SELECT DISTINCT
		  		Devcon, Devfre, Devhre, Devusu, Descripcion, Devlin
		FROM
				".$wbasedato."_000028, usuarios
		WHERE 
				Codigo = Devusu 
				AND Devusu IS NOT NULL
				AND Devusu <> ''
				AND Devcon = '".$devolucion->numeroDevolucion."'
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

			$registro->codigoUsuarioRecibe 		= $resultSet['Devusu'];
			$registro->fechaRecibo 				= $resultSet['Devfre'];
			$registro->horaRecibo 				= $resultSet['Devhre'];			
			$registro->nombreUsuarioRecibe 		= $resultSet['Descripcion'];
			$registro->numeroDevolucion 		= $resultSet['Devcon']." - ".$resultSet['Devlin'];			

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
	global $wemp_pmla;

	$ccoSF=ccoUnificadoSF();
	$ccoCM=ccoUnificadoCM();
	
	$q = "SELECT 
			Codigo, Descripcion 
		FROM 
			usuarios
		WHERE 
			Ccostos IN ('{$ccoSF}','{$ccoCM}')
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

/*
 * Inicio aplicacion
 */
include_once("root/comun.php");

$wactualiz = "Febrero 10 de 2022";

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
	echo "<form name='forma' action='RepRecibosDevoluciones.php' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	//Al principio se mostrará al usuario las dos posibles opciones de proceso que tiene: Proceso de cambio destino / anulación o de cambios
	//en la lista de materiales
	
	encabezado("REPORTE RECIBOS DEVOLUCIONES POR USUARIO", $wactualiz, "clinica");
	
	if(!isset($wfechaini) && !isset($wfechafin) && !isset($numdevolucion) && !isset($wccocod)){

		//Cuerpo de la pagina
		echo "<table align='center' border=0>";
			
		echo '<span class="subtituloPagina2">';
		echo "Par&aacute;metros de consulta";
		echo "</span>";
		echo "<br>";
		echo "<br>";

		//Numero de devolucion
		echo "<tr><td class='fila1' width=200>N&uacute;mero devoluci&oacute;n</td>";
		echo "<td class='fila2' align='center' width=250>";
		echo "<input type='text' size='8' name='numdevolucion' id='numdevolucion' size=16 onkeypress='return validarEntradaEntera(event);' class='textoNormal'>";
		echo "</td>";
		echo "</tr>";
			
		//Fecha inicial consulta
		echo "<tr><td class='fila1' width=200>Fecha inicial</td>";
		echo "<td class='fila2' align='center' width=250>";
		campoFecha("wfechaini");
		echo "</td>";
		echo "</tr>";
		
		//Fecha final consulta
		echo "<tr><td class='fila1' width=200>Fecha final</td>";
		echo "<td class='fila2' align='center' width=250>";
		campoFecha("wfechafin");
		echo "</td>";
		echo "</tr>";
		
		//Usuario que recibe
		echo "<tr><td class='fila1' width=200>Usuario que recibe</td>";
		
		$usuarios = consultarUsuariosQueRecibenDevoluciones();
	
		echo "<td class='fila2' align='center' width=250>";
		echo "<select name='usuarioRecibe'>";
		echo "<option value='%'>Todos los usuarios</option>";
		
		foreach ($usuarios as $usuario){			
			echo "<option value='".$usuario->codigo."'>".$usuario->codigo." - ".$usuario->descripcion."</option>";
		}
		echo "</select>";
		
		echo "</td>";
		echo "</tr>";
		
		echo "<div align='center'>";		
		echo "<tr><td align=center colspan=19><br><input type=button value='Consultar' onclick='javascript:enter();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
		echo "</div>";
		echo "</table>";

		echo "<div align='center'>";
		echo "<br>";
		echo "<tr><td align=center colspan=19></td></tr>";
		echo "</div>";
	} else {
		echo "<INPUT TYPE='hidden' NAME='wfechaini' value='".$wfechaini."'>";
		echo "<INPUT TYPE='hidden' NAME='wfechafin' value='".$wfechafin."'>";
		echo "<INPUT TYPE='hidden' NAME='numdevolucion' value='".$numdevolucion."'>";
		echo "<INPUT TYPE='hidden' NAME='usuarioRecibe' value='".$usuarioRecibe."'>";

		//Parametros y consulta de los indicadores
		$registroConsulta = new registroDTO();

		//Criterios de consulta
		$registroConsulta->codigoUsuarioRecibe = $usuarioRecibe;
		$registroConsulta->numeroDevolucion = empty($numdevolucion) ? '%' : $numdevolucion;

		$consulta = consultarDevoluciones($registroConsulta,$wfechaini,$wfechafin);

		echo "<span class=subtituloPagina2>::NOTA::Para la columna devolución se muestra el número de línea de la devolución ya que el usuario que recibe puede ser diferente para cada una de ellas.</span>";
		
		//Si hay datos muestra la informacion
		if(sizeof($consulta) > 0){
			echo "<table align='center'>";

			//Encabezados de la tabla
			echo "<tr align='center' class=encabezadoTabla>";
			echo "<td>Devolucion número - línea</td>";
			echo "<td>Fecha recibo</td>";
			echo "<td>Hora recibo</td>";
			echo "<td>Usuario que recibe</td>";
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
					echo "<td>$reg->codigoUsuarioRecibe - $reg->nombreUsuarioRecibe</td>";
						
					echo "</tr>";
				}
			}
			echo "<tr><td align=center colspan=18><br><input type=button value='Regresar' onclick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
			echo "</table>";

		} else {
			echo "<span class=subtituloPagina>No se encontraron devoluciones</span>";
			echo "<div align=center colspan=9><br><input type=button value='Regresar' onclick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
		}
		echo "</div>";
	}
	liberarConexionBD($conex);
}
/**
 * Actualizacion: -Se parametrizo los centros de costos de Central de Mezclas y Dispensacion Servicio Farmaceutico
 * @by: Marlon Osorio
 * @date: 2022/01/11
 * 
*/
?>
</body>
</html>