<?php
include_once("conex.php");
/**********************************************************************************************************************************************************
 * Fecha de creacion:	Enero 24 de 2012
 * Por:					Edwin Molina Grisales
 * Descripción general:	Modulo que con las funciones necesarias para indicar si para un centro de costos, con fecha y ronda dados, todos los medicamentos
 *						obligatorios a aplicar fueron aplicados
 **********************************************************************************************************************************************************/
 

/********************************************************************************************************************************************************************************
 * Verifica si un medicamento obligtario tiene aplicacion o no.  Si el medicamento es obligatorio y no se ha aplicado el medicamento devuelve false, caso contraio devuelve verdadero
 *
 * Un medicamento es obligatorio es suministrar si:
 * - La condicion no es a necesidad
 * - No esta suspendido
 * - No tiene justificacion
 *
 * $historia				Historia del paciente
 * $ingreso					Ingreso del paciente
 * $aplicaciones			Vector de aplicaciones del paciente, este vector es creado en la funcion estaAplicadoCcoPorRonda
 * $fecha					Fecha de consulta
 * $ronda					Ronda a la cual se quiere buscar, numero entero entre 0 - 23
 * $codigoArticulo			Codigo del medicamento a buscar
 * $cantidadCargada			Cantidad cargada del medicamento y la cual se va a aplicar
 * $suspendido				Indica si el medicamento esta suspendido o no, 'on' si esta suspendido, caso contrario no esta suspendido
 * $aNecesidad				Indica si el medicamento es a necesidad
 ********************************************************************************************************************************************************************************/
function verificarAplicacion( $historia, $ingreso, $aplicaciones, $fecha, $ronda, $codigoArticulo, $cantidadCargada, $suspendido, $aNecesidad ){

	$esCondicionANecesidad = esANecesidad( $aNecesidad );
	
	$cantidadAplicada = @$aplicaciones[ $historia."-".$ingreso ][$codigoArticulo][$ronda];
	
	if( $suspendido == "on" ){
	
		$estaSuspendido = buscarSiEstaSuspendido($historia, $ingreso, $codigoArticulo, $ronda, $fecha );
		
		if( $estaSuspendido != 'on' ){
			$suspendido = "off";
		}
		else{
			return true;
		}
	}
	
	$justificacion = consultarJustificacion( $historia, $ingreso, $fecha, $ronda, $codigoArticulo );
	
	//Son medicamentos Obligatorios aquellos que no tienen justificacion, no son a necesidad y no estan suspendidos
	if( !$esCondicionANecesidad && empty( $justificacion ) && $suspendido != "on" ){
		
		if( $cantidadCargada == $cantidadAplicada ){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return true;
	}
}

// /****************************************************************************************
 // * Indica si el articulo fue reemplazado en una fecha dada
 // ****************************************************************************************/
// function fueReemplazado( $historia, $ingreso, $fecha, $articulo ){

	// global $conex;
	// global $wbasedato;

	// $sql = "SELECT 
				// * 
			// FROM
				// {$wbasedato}_000055
			// WHERE
				// kaumen = 'Articulo ha sido reemplazado desde el perfil farmacologico'
				// AND Kauhis = '$historia'
				// AND Kauing = '$ingreso'
				// AND Kaufec = '$fecha'
				// AND Kaumen = 'A:,$articulo%'
			// ";
			
	// $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql ".mysql_error() );
	// $num = mysql_num_rows( $res );
	
	// if( $num > 0 ){
		// return true;
	// }
	// else{
		// return false;
	// }
// }


include_once( "root/comun.php" );
include_once( "movhos/movhos.inc.php" );
   
if(!isset($_SESSION['user'])){
	exit("<b>Usuario no registrado</b>");
}
else{

	$conex = obtenerConexionBD("matrix");
	
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

}

// $fecha = "2012-01-09";
// $cco = "1185";

if( !empty($fecha) && !empty($cco) ){

	echo "$fecha - $cco<br><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 0 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 0, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 2 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 2, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 4 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 4, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 6 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 6, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 8 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 8, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 10 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 10, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 12 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 12, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 14 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 14, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 16 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 16, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 18 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 18, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 20 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 20, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";

	$habs = array();
	echo "<br>Respuesta: $fecha - 22 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 22, $habs ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	echo "<pre>"; var_dump($habs); echo "</pre><br>";
}

echo "<form>";
echo "Fecha <input type='text' name='fecha'>";
echo "<br>cco <input type='text' name='cco'>";
echo "<INPUT type='submit' value='probar'>";

echo "<INPUT type='hidden' name='wemp_pmla' value='01'>";
echo "</form>"

// echo "<br>Respuesta: 2012-01-09 - 18 ".( ( estaAplicadoCcoPorRonda( $cco, $fecha, 18 ) == true ) ? "<b>Aplicado</b>": "<b>Sin Aplicar</b>" ) ;
	
?>