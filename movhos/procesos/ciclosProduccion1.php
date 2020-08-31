<?php
include_once("conex.php");
/************************************************************************************************************************************************************************
 * ESPECIFICACIONES DEL PROGRAMA
 * 
 * CICLOS DE PRODUCCION
 * 
 * Se pretende un reporte en que se muestre todos los medicamentos a producir para un TIPO DE MEDICAMENTO segun el kardex de enfermería.
 * 
 * 
 * ESPECIFICACIONES
 * 
 * - Mostrar una lista con todos los tipos de articulos que tienen cantidades por producir
 * 
 * - Para cada tipo de ariculo, dado una ronda, y tiempo para el que se produce, mostrar la lista de articulos que se van a producir con su correspondiente cantidad.
 * 	 Si para un tipo de articulo no se va a producir nada, no mostrar la lista.
 * 
 * - Se graba el encabezado del ciclo de produccion (movhos_000106) cuando se confirma toda la producción o si no hay nada para producir para el articulo.
 * 
 * DETALLE
 * 
 * - Al marcar un articulo, este se registra en la base de datos(tabla movhos_000107) con los datos correspondientes, siempre y cuando no se encuentre ya para la ronda y fecha.
 * - Si un articulo ya fue producido y se vuelve a marcar, el registro correspondiente al articulo seleccionado queda desactivado.
 * - En caso de que halla alguna modificacion al articulo y tipo de articulo por ronda se le actualizan los datos
 * - Por cada articulo, se muestra un detalle al dar click sobre el enlace correspondiente, con la siguiente información:
 * 		Historia
 * 		Ingreso
 * 		Cco en el que se encuentra
 * 		Habitación actual
 * 		Dosis
 * 		Total de unidades que se gastaría
 * 
 * DETERMINAR RONDA A MOSTRAR
 * 
 * Si la proxima ronda (PR) menos la hora actual (HA) esta entre la hora de corte de producción (HCP) y la hora corte de dispensación (HCD) se muestra la producción de la siguiente ronda.
 * En caso contrario se muestra la ronda anterior siempre y cuando no se haya dispensado totalmente la ronda anterior.
 * 
 *   	(PR-HA) <= HCP and (PR-HA) >= (HCD)
 * 
 * DICCIONARIO
 * 
 * Hora corte de dispensacion:	A partir de cuanto tiempo antes comienza la dispensacion
 * Hora corte de produccion:	A partir de cuanto tiempo antes comienza la produccion
 * Ronda: 						Hora para la que se aplica un medicamento, va cada 2 horas comenzando desde las 00:00:00 del día en curso
 * 
 ************************************************************************************************************************************************************************/

/************************************************************************************************************************************************************************
 * 																					FUNCIONES
 ************************************************************************************************************************************************************************/

/************************************************************************************************************************
 * activa o desactiva un registro segun estado
 * 
 * @param $conex
 * @param $wbasedato
 * @param $fecha
 * @param $ronda
 * @param $tipoArticulo
 * @param $estado
 * @return unknown_type
 ************************************************************************************************************************/
function activarDesactivarEncabezado( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $estado ){
	
	$val = false;
	
	$sql = "UPDATE
				{$wbasedato}_000106
			SET
				ecpest = '$estado'
			WHERE
				Ecpfec = '$fecha'
				AND Ecpron = '$ronda'
				AND Ecptar = '$tipoArticulo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	
	if(  mysql_affected_rows() > 0  ){
		$val = true;
	}
	
	return true;
}

/************************************************************************************************
 * Consulta el nombre de un paciente segun la historia
 * 
 * @param $conex
 * @param $his
 * @return unknown_type
 ************************************************************************************************/
function consultarNombrePaciente( $conex, $his ){
	
	$val = "";
	
	$sql = "SELECT
				*
			FROM
				root_000036 a, root_000037 b 
			WHERE
				orihis = '$his'
				AND oriori = '01'
				AND oriced = pacced
				AND pactid = oritid 
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$rows = mysql_fetch_array( $res );
		   	 
		$val = $rows['Pacno1']." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'];
	}
	
	return $val;
}

/************************************************************************************************
 * Muestra el detalle de un articulo
 * 
 * @param $tipo
 * @param $articulo
 * @param $detalleArticulo
 * @return unknown_type
 ************************************************************************************************/
function mostrarDetalleArticulo( $tipo, $articulo, $detalleArticulo ){

	$val = false;
	
	$i = 0;
	
	echo "<table align='center' id='{$tipo}_{$articulo}'>";
	
	echo "<tr class='encabezadotabla' align='center'>";
	echo "<td style='width:100'>Historia</td>";
	echo "<td>Nombre</td>";
	echo "<td>Dosis</td>";
	echo "<td>Cantidad Total</td>";
	echo "</tr>";
	
	foreach( $detalleArticulo[$tipo][$articulo] as $keyDetalle => $valueDetalle ){

		$fila = "class='fila".(($i%2)+1)."'";
		
		echo "<tr $fila>";
		
			
			echo "<td align='center'>$keyDetalle</td>";
			
			echo "<td>";
			echo $valueDetalle['nombre'];
			echo "</td>";
			
			echo "<td style='width:80' align='center'>";
			echo $valueDetalle['dosis']." ".$valueDetalle['unidadDosis'];
			echo "</td>";
			
			echo "<td align='center'>";
			echo $valueDetalle['unidadesTotales'];
			echo "</td>";
		echo "</tr>";
		
		$i++;
	}
	
	echo "</table>";
	
	return $val;
}

/****************************************************************************************************
 * Actualiza el registro segun el id
 * 
 * @param $conex
 * @param $wbasedato
 * @param $cantidad
 * @param $unidadFraccion
 * @param $id
 * @return unknown_type
 ****************************************************************************************************/
function actualizarRegistro( $conex, $wbasedato, $cantidad, $unidadFraccion, $id ){
	
	$val = false;
	
	$sql = "UPDATE
				{$wbasedato}_000107 
			SET
				Cpxcan = '$cantidad',
				Cpxuni = '$unidadFraccion',
				Cpxest = 'on'
			WHERE
				id = '$id'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}

/************************************************************************************************************************
 * Registra, actualiza o desactiva el registro segun el caso
 * 
 * @param $conex
 * @param $wbasedato
 * @param $fecha
 * @param $ronda
 * @param $tipoArticulo
 * @param $articulo
 * @param $cantidad
 * @param $unidadFraccion
 * @return unknown_type
 ************************************************************************************************************************/
function movimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $cantidad, $unidadFraccion ){
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000107
			WHERE
				Cpxfec = '$fecha' 
				AND Cpxron = '$ronda'
				AND Cpxtar = '$tipoArticulo'
				AND Cpxart = '$articulo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		if( $rows[ 'Cpxcan' ] == $cantidad && $rows[ 'Cpxuni' ] == $unidadFraccion ){
			
			$estado = 'on';
			if( $rows[ 'Cpxest' ] == 'on' ){
				$estado = 'off';
			}
			
			$val = activarDesactivarRegistro( $conex, $wbasedato, $rows[ 'id' ], $estado );
			
			if( $estado == 'off' ){
				activarDesactivarEncabezado( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $estado );
			}	
		}
		else{
			$val = actualizarRegistro( $conex, $wbasedato, $cantidad, $unidadFraccion, $rows[ 'id' ] );
		}
	}
	else{
		if( registrarMovimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $cantidad, $unidadFraccion ) ){
			$val = true;
		}
		else{
			$val = false;
		}
	}
	
	return $val;
}

/******************************************************************************
 * Desactiva el registro segun el id
 * @param $conex
 * @param $wbasedato
 * @param $id
 * @return unknown_type
 ******************************************************************************/
function activarDesactivarRegistro( $conex, $wbasedato, $id, $estado ){
	
	$val = false;
	
	$sql = "UPDATE 
				{$wbasedato}_000107
			SET
				Cpxest = '$estado'
			WHERE
				id = '$id'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() ){
		$val = true;
	}
	
	return $val;
}

/****************************************************************************************
 * Busca si una ronda ya fue producida para un tipo de articulo
 * 
 * @param $conex
 * @param $wbasedato
 * @param $fecha
 * @param $ronda
 * @param $tipoArticulo
 * @return unknown_type
 ****************************************************************************************/
function rondaProducida( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo ){
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000106
			WHERE
				Ecpfec = '$fecha'
				AND Ecpron = '$ronda'
				AND Ecptar = '$tipoArticulo'
				AND Ecpest = 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}

/****************************************************************************************************************
 * Detemirna si para un aritculo y tipo de articulo ya le crearon la produccion necesaria para la ronda
 * 
 * @param $conex
 * @param $wbasedato
 * @param $ronda
 * @param $articulo
 * @param $tipoArticulo
 * @return unknown_type
 ****************************************************************************************************************/
function tieneProduccion( $conex, $wbasedato, $wcenmez, $fecha, $ronda, $articulo, $tipoArticulo, &$datosTipo ){
	
	$val = false;

//	$sql = "SELECT
//				Cpxcan, Cpxuni
//			FROM
//				{$wbasedato}_000107
//			WHERE
//				cpxfec = '$fecha'
//				AND cpxron = '$ronda'
//				AND cpxtar = '$tipoArticulo'
//				AND cpxart = '$articulo'
//			"; echo "<br>..........<pre>$sql</pre>";
				
	$sql = "SELECT
				Cpxcan, Cpxuni, Deffra
			FROM
				{$wbasedato}_000107 a, {$wbasedato}_000059 b
			WHERE
				cpxfec = '$fecha'
				AND cpxron = '$ronda'
				AND cpxtar = '$tipoArticulo'
				AND cpxart = '$articulo'
				AND defart = cpxart
				AND defest = 'on'
				AND cpxest = 'on'
			"; //echo "<br>..........<pre>$sql</pre>";
	
	$res = mysql_query( $sql, $conex ) or die();
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$datosTipo[ $tipoArticulo ][ $articulo ]['codArticulo'] = $articulo;
		$datosTipo[ $tipoArticulo ][ $articulo ]['nomArticulo'] = nombreArticulo( $conex, $wbasedato, $wcenmez, $articulo );
		@$datosTipo[ $tipoArticulo ][ $articulo ]['canArticulo'] = $rows['Cpxcan'];
		@$datosTipo[ $tipoArticulo ][ $articulo ]['canDosis'] = 1*$rows['Cpxcan'];
		@$datosTipo[ $tipoArticulo ][ $articulo ]['uniDosis'] = $rows['Cpxuni'];
		@$datosTipo[ $tipoArticulo ][ $articulo ]['fraUnidad'] = $rows['Deffra'];
		@$datosTipo[ $tipoArticulo ][ $articulo ]['producido'] = true;
		
		$val = true;
	}
	
	return $val;
}

/************************************************************************************************ 
 * Si la proxima ronda (PR) menos la hora actual (HA) esta entre la hora de corte de producción (HCP) y la hora corte de dispensación (HCD) se muestra la producción de la siguiente ronda.
 * En caso contrario se muestra la ronda anterior siempre y cuando no se haya dispensado totalmente la ronda anterior.
 * 
 *   	(PR-HA) <= HCP and (PR-HA) >= (HCD)
 ************************************************************************************************/
function mostrarRonda( $fechaProximaRonda, $proximaRonda, $horaCortePx, $horaCorteDispensacion ){
//	echo "<br>.......$fechaProximaRonda";
	$ini1970 = strtotime( "1970-01-01 00:00:00" );
	
	$fechorActual = time();
	$fechorRonda = strtotime( "$fechaProximaRonda $proximaRonda" );
	$produccion = strtotime( "1970-01-01 $horaCortePx" )-$ini1970;
	$dispensacion = strtotime( "1970-01-01 $horaCorteDispensacion" ) - $ini1970;
	
//	echo "<br>*........PR: $produccion DIF:".($fechorRonda - $fechorActual);
//	echo "<br>|........DP: $dispensacion DIF:".($fechorRonda -$fechorActual);
	
	if( $fechorRonda - $fechorActual <= $produccion && $fechorRonda -$fechorActual >= $dispensacion ){
		return true;
	}
	else{
		return false;
	}
}

/************************************************************************************************************************************
 * Calcula el total de aplicaciones de un medicamento, segun la ronda de produccion
 * 
 * @param $inicioRonda
 * @param $tiempoProduccion
 * @param $inicioArticulo		Incio del articulo igual o superior a hora inicio de ronda
 * @return unknown_type
 ************************************************************************************************************************************/
function calcularCantidadAplicacionesRondaProduccion( $fechaActual, $inicioRonda, $tiempoProduccion, $inicioArticulo, $frecuencia, $dosisMaximas, $diasTto ){

	$porDosisDias = true;
	
	$iniRonda = strtotime( "$fechaActual $inicioRonda" );
	
	$iniArt = $inicioArticulo;
	
	$finRonda = $iniRonda+$tiempoProduccion*3600;
	
	
	//Dosis maximas
	if(!empty($dosisMaximas) ){
		$fechorDosisMaximas = $inicioArticulo+$frecuencia*($dosisMaximas-1)*3600;
	}
//	echo "<br><br>.......".date( "Y-m-d H:i:s", $inicioArticulo );

	//Dias de tratamiento
	if( !empty($diasTto) ){
		$fechorDiasTto = strtotime( date( "Y-m-d", $inicioArticulo )." 23:59:59" )*$diasTto*24*3600;	//Calculo cuando termina el medicamento por dias de tto
	}

	if( !empty($diasTto) ){
		if( $inicioArticulo <= $fechorDiasTto ){
			$porDosisDias = true;
		}
		else{
			$porDosisDias = false;
		}
	}
//	echo "<br>.......".$dosisMaximas;
	if( !empty($dosisMaximas) && $porDosisDias ){
		if( $inicioArticulo <= $fechorDosisMaximas ){
			$porDosisDias = true;
		}
		else{
			$porDosisDias = false;
		}
	}
//	echo "<br>.......".$porDosisDias;
	
	$val = 0;
	
	for( $i = 0; $iniArt < $finRonda && $porDosisDias; $i++ ){
		$iniArt += $frecuencia*3600;
		$val++;
		
		if( !empty($diasTto) ){
			if( $iniArt <= $fechorDiasTto ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}
		
		if( !empty($dosisMaximas) && $porDosisDias ){
			if( $iniArt <= $fechorDosisMaximas ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}
	}

	return $val;
}

/*********************************************************************************************************************************************
 * Registra el encabezado del ciclo de producción
 * 
 * @param $conex
 * @param $wbasedato
 * @param $fecha
 * @param $ronda
 * @param $tipoArticulo
 * @return unknown_type
 *********************************************************************************************************************************************/
function registrarEncabezadoProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo ){
	
	global $wuser;
	
	$fechaActual = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	//Busco si existe encabezdo
	$sql = "SELECT
					*
				FROM
					{$wbasedato}_000106
				WHERE
					Ecpfec = '$fecha'
					AND Ecpron = '$ronda'
					AND Ecptar = '$tipoArticulo'
				";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	//Si existe encabezado, se activa el registro, caso contrario se inserta
	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		if( $rows[ 'Ecpest' ] == 'off' ){
			activarDesactivarEncabezado( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, 'on' );
		}
	}
	else{
	
		$sql = "INSERT INTO {$wbasedato}_000106
						(   Medico    ,   fecha_data  , hora_data,  Ecpfec ,  Ecpron ,     Ecptar     ,  Ecpusu , Ecpest,   seguridad    )
				  VALUES( '$wbasedato', '$fechaActual',  '$hora' , '$fecha', '$ronda', '$tipoArticulo', '$wuser',  'on' , 'C-$wbasedato' )";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			return true;
		}
		else{
			return false;	
		}
	}
}

/*********************************************************************************************************************************************
 * 
 * 
 * @param $conex
 * @param $wbasedato
 * @param $campos
 * @return unknown_type
 *********************************************************************************************************************************************/
function registrarMovimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $cantidad, $unidadFraccion ){
	
	global $wuser;
	
	$fechaActual = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO {$wbasedato}_000107
					(   Medico    ,   fecha_data  , hora_data,  Cpxfec , Cpxron  ,     Cpxtar     ,   Cpxart   ,    Cpxcan  ,       Cpxuni     , Cpxusu  , Cpxest,  seguridad    )
			  VALUES( '$wbasedato', '$fechaActual',  '$hora' , '$fecha', '$ronda', '$tipoArticulo', '$articulo', '$cantidad', '$unidadFraccion', '$wuser',   'on'  , 'C-$wbasedato' )";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;	
	}
}

/************************************************************************************************************************************************
 * Crea un array de rondas, cada x horas comenzando desde las 00:00:00 usando como clave tatno numero como la hora de la ronda
 * 
 * @return unknown_type
 ************************************************************************************************************************************************/
function crearRondas( $rango ){
	
	$rondas = Array();
	
	$hora = strtotime( "1970-01-01 00:00:00" );
	
	for( $i = 0, $j = 0; $j < 24; $i++, $j += $rango ){
		
		$rondas[ $i ] = date( "H:i:s", $hora + $j*3600 );
		$rondas[ $rondas[ $i ] ] = $i; 
	}
	
	return $rondas;
}

/************************************************************************************************************************
 * Devuelve un array con todas las frecuncias, con codigo y valor
 * 
 * @return unknown_type
 ************************************************************************************************************************/
function consultarFrecuencias(){
	
	global $conex;
	global $wbasedato;
	
	$val = false;
	
	$sql = "SELECT
				*					
			FROM {$wbasedato}_000043
			WHERE 1
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - $sql ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		@$val[ $rows[ 'Percod' ] ] = $rows[ 'Perequ' ];
	}
	
	return $val;
}

/************************************************************************************************************************
 * Indica si el articulo empieza a la misma hora que la hora actual dada
 * 
 * @param $fechaActual
 * @param $fechaIncio
 * @param $horaInicio
 * @param $frecuencia
 * @return unknown_type
 * 
 * Nota: La frecuencia esta dada en horas
 * 
 * Marzo 1 de 2011
 ************************************************************************************************************************/
//function perteneceRonda( $fechaActual, $horaActual, $tiempoPreparacion, $fechaIncio, $horaInicio, $frecuencia, &$horaSiguiente, &$despuesHoraCortePx ){
//	
//	$horaIncioActual = false;
//	$despuesHoraCortePx = false;
//	
//	//Fecha actual debe ser menor o igual que la fecha de inicio
//	if( $fechaIncio <= $fechaActual ){
//		
//		//convierto las fechas
//		$fechorActual = strtotime( "$fechaActual $horaActual" );
//		$fechorInicio = strtotime( "$fechaIncio $horaInicio" );
//		$fechorfinal = $fechorActual+$tiempoPreparacion*3600;
//		
//		$horaIncioActual = $fechorInicio;
//		$horaSiguiente = $horaIncioActual; 
//		//Sumo la frecuencia hasta el día en que comience el medicamento
//		for( $i = 0 ; $fechorInicio < $fechorActual; $i++ ){
//
//			$fechorInicio += $frecuencia*3600;
//			$horaIncioActual = $fechorInicio;
//		}
//		//echo "<br>..........Ronda:$horaActual-".date( "H:i:s", $fechorfinal ).".......HIM:$fechaIncio $horaInicio - $frecuencia: HFM".date( "H:i:s",$horaIncioActual );
//		if( $horaIncioActual >= $fechorActual && $horaIncioActual < $fechorfinal ){
//			
//			if( $horaIncioActual >= $fechorActual ){
//				$despuesHoraCortePx = true;
//			}
//			
//			$horaSiguiente = $horaIncioActual;
//			return true;
//		}
//		else{
//			return false;
//		}
//	}
//	
//	return false;
//}

function perteneceRonda( $fechaActual, $horaActual, $tiempoPreparacion, $fechaIncio, $horaInicio, $frecuencia, &$horaSiguiente, &$despuesHoraCortePx, $dosisMaximas, $diasTto ){
	
	$horaIncioActual = false;
	$despuesHoraCortePx = false;
	
	$porDosisDias = true;	//Indica si por Dosis Maximas o dias de tratamiento el medicamento pertenece a la ronda
	
	//Fecha actual debe ser menor o igual que la fecha de inicio
	if( $fechaIncio <= $fechaActual ){
		
		//convierto las fechas
		$fechorActual = strtotime( "$fechaActual $horaActual" );
		$fechorInicio = strtotime( "$fechaIncio $horaInicio" );
		$fechorfinal = $fechorActual+$tiempoPreparacion*3600;
		
		//Dosis maximas
		if(!empty($dosisMaximas) ){
			$fechorDosisMaximas = $fechorInicio+$frecuencia*($dosisMaximas-1)*3600;
		}
		
		//Dias de tratamiento
		if( !empty($diasTto) ){
			$fechorDiasTto = strtotime( "$fechaIncio 23:59:59" )*$diasTto*24*3600;	//Calculo cuando termina el medicamento por dias de tto
		}

		if( !empty($diasTto) ){
			if( $fechorInicio <= $fechorDiasTto ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}
//		echo "<br>.......".$dosisMaximas;
		if( !empty($dosisMaximas) && $porDosisDias ){
			if( $fechorInicio <= $fechorDosisMaximas ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}
//		echo "<br>.......HDM: ".date( "Y-m-d H:i:s", $fechorDosisMaximas );
//		echo "<br>........".$porDosisDias;
		
		$horaIncioActual = $fechorInicio;
		$horaSiguiente = $horaIncioActual; 
		//Sumo la frecuencia hasta el día en que comience el medicamento
		for( $i = 0 ; $fechorInicio < $fechorActual && $porDosisDias; $i++ ){

			$fechorInicio += $frecuencia*3600;
			$horaIncioActual = $fechorInicio;
			
			$porDosisDias = true;
			
			if( !empty($diasTto) ){
				if( $horaIncioActual <= $fechorDiasTto ){
					$porDosisDias = true;
				}
				else{
					$porDosisDias = false;
				}
			}
			
			if(!empty($dosisMaximas) && $porDosisDias ){
				if( $horaIncioActual <= $fechorDosisMaximas ){
					$porDosisDias = true;
				}
				else{
					$porDosisDias = false;
				}
			}
		}
//		echo "<br>........HF: ".date("Y-m-d H:i:s", $fechorfinal );
//		echo "<br>........Inicio despues de ronda: ".date("Y-m-d H:i:s", $horaIncioActual );
		//echo "<br>..........Ronda:$horaActual-".date( "H:i:s", $fechorfinal ).".......HIM:$fechaIncio $horaInicio - $frecuencia: HFM".date( "H:i:s",$horaIncioActual );
		if( $horaIncioActual >= $fechorActual && $horaIncioActual < $fechorfinal ){
			
			if( !empty($diasTto) ){
				if( $horaIncioActual > $fechorDiasTto ){
					return false;
				}
			}
			
			if( !empty($dosisMaximas) ){
				if( $horaIncioActual > $fechorDosisMaximas ){
//					echo "<br>......no pertenece";
					return false;
				}
			}
			
			if( $horaIncioActual >= $fechorActual ){
				$despuesHoraCortePx = true;
			}

			$horaSiguiente = $horaIncioActual;
			return true;
		}
		else{
			return false;
		}
	}
	
	return false;
}

/************************************************************************************************************************
 * Devuelve el nombre del articulo
 * @param $conex
 * @param $wbasedato
 * @param $codigo		Codigo del articulo
 * @return unknown_type
 ************************************************************************************************************************/
function nombreArticulo( $conex, $wbasedato, $wcenmez, $codigo ){
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000026
			WHERE
				artcod = '$codigo';
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		if( $rows = mysql_fetch_array( $res ) ){
			return $rows['Artcom'];
		}
	}
	else{
		$sql = "SELECT
					*
				FROM
					{$wcenmez}_000002
				WHERE
					artcod = '$codigo';
				";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
		$numrows = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			return $rows['Artcom'];
		}
	}
	
	return false;
}

/************************************************************************************************************************
 * Determina si un articulo es generico y devuelve el tipo de articulo al que pertenece
 * 
 * @param $conexion
 * @param $wbasedatoMH
 * @param $wbasedatoCM
 * @param $codArticulo
 * @return unknown_type
 ************************************************************************************************************************/
function esArticuloGenerico( $conexion, $wbasedatoMH, $wbasedatoCM, $codArticulo ){
	
	$sql = "SELECT
				*
			FROM
				{$wbasedatoMH}_000068,
				{$wbasedatoCM}_000002,
				{$wbasedatoCM}_000001
			WHERE
				artcod = '$codArticulo'
				AND arttip = tipcod
				AND tiptpr = arktip
				AND artest = 'on'
				AND arkest = 'on'
				AND tipest = 'on' 
			";
	
	$res = mysql_query( $sql, $conexion ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		return $rows['Arktip'];
	}
	else{
		return false;
	}
}


/********************************************************************************************************************************
 * Pinta la tabla con todos los articulo para un tipo de articulo
 * 
 * @param $tipoArticulo
 * @return unknown_type
 ********************************************************************************************************************************/
function mostrarArticulosRonda( $tipoArticulo, $codTipo, $detalleArticulos ){
	
	if( count( $tipoArticulo ) ){
		
		$i = 0;
		
		foreach( $tipoArticulo as $keyTipo => $valueTipo ){
			
			if( $i == 0 ){
				
				echo "<table align='center' id='tbDetalle{$codTipo}' style='display:none'>";

				echo "<tr class='encabezadotabla' align='center'>";
					
				echo "<td>Codigo</td>";
				echo "<td>Nombre</td>";
				echo "<td>Cantidad dosis</td>";
				echo "<td>Cantidad de unidades<br>con aprovechamiento</td>";
				echo "<td>Gasto total</td>";
				echo "<td>Cantidad de unidades<br>sin aprovechamiento</td>";
				echo "<td>Producido</td>";
				echo "<td>Mostrar/Ocultar<br>Detalle</td>";
					
				echo "</tr>";
			}
			
			$class = "class='fila".(($i % 2)+1)."'";

			echo "<tr $class>";
				
			echo "<td>";
			echo $valueTipo['codArticulo'];
			echo "</td>";
			echo "<td>";
			echo $valueTipo['nomArticulo'];
			echo "</td>";
			echo "<td align='center'>";
			echo $valueTipo['canDosis']." ".$valueTipo['uniDosis'];
			echo "</td>";
			echo "<td align='center' class='fondoamarillo'>";
			echo ceil( $valueTipo['canDosis']/$valueTipo['fraUnidad'] );
			echo "</td>";
			echo "<td align='center'>";
			echo (ceil( $valueTipo['canDosis']/$valueTipo['fraUnidad'] )*$valueTipo['fraUnidad'])." ".$valueTipo['uniDosis'];
			echo "</td>";
			echo "<td align='center'>";
			echo $valueTipo['canArticulo'];
			echo "</td>";

			if( !$valueTipo['producido'] ){
				echo "<td align='center'><INPUT type='checkbox' name='' id='' onClick='grabarMovimientoArticulo( this )'></td>";
			}
			else{
				echo "<td align='center'><INPUT type='checkbox' name='' id='' onClick='grabarMovimientoArticulo( this )' checked></td>";
			}
			
			echo "<td align='center'>";
			echo "<INPUT type='checkbox' onClick='mostrarOcultarDetalle( this )'>";
			echo "</td>";
				
			echo "</tr>";
			
			echo "<tr style='display:none'>";
			echo "<td colspan='8'><br>";
			mostrarDetalleArticulo( $codTipo, $valueTipo['codArticulo'], $detalleArticulos );
			echo "<br></td>";
			echo "</tr>";
				
			$i++;
		}
		
		echo "<tr>";
		echo "<td align='center' colspan='8'><br><INPUT type='button' value='Retornar' style='width:100' onClick='retornar( this )'></td>";
		echo "</tr>";
		
		echo "</table>";
	}
}

/********************************************************************************************************************************************
 * Consulta la ultima ronda creada para un tipo de articulo
 * 
 * @param $tipo
 * @return unknown_type
 ********************************************************************************************************************************************/
function consultarUltimaRonda( $conex, $wbasedato, $tipo ){
	
	$val = date("Y-m-d 00:00:00");
	
	$fecha = date( "Y-m-d", strtotime( date("Y-m-d H:i:s") )-24*3600 );
	
	//Consulto la ultima ronda que se hizo para un tipo de articulo
	//desde el día anterior, esto por que la siguiente ronda puede ser a la medianoche
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000106
			WHERE
				ecptar = '$tipo'
				AND ecpfec >= '$fecha'
				AND ecpest = 'on'
			ORDER BY
				fecha_data desc, hora_data desc 
			"; //echo "<BR>.......A: ".$sql;
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res) ){
		$val = $rows[ 'Ecpfec' ]." ".$rows[ 'Ecpron' ];
	}
	
	return $val;
}


/************************************************************************************************************************************
 * De acuerdo a un array de ronda, determina cual es la siguiente ronda
 * 
 * @param $arRonda			Arrays de rondas
 * @param $numRonda			
 * @return unknown_type
 ************************************************************************************************************************************/
function proximaRonda( $arRonda, $ronda ){
	
	$proxmaRonda = $ronda;

	if(true){
		
		if( $arRonda[ $ronda ]+1 <= count($arRonda/2)){
			$arRonda[ $arRonda[ $ronda ]+1 ] = 0;
		}
		else{
			$proxmaRonda = $arRonda[ 1 ];
		}
	}
	else{
	
	}
	
	return $proxmaRonda;
}

/************************************************************************************************************************
 * Proxima ronda de produccion
 * 
 * @return unknown_type
 ************************************************************************************************************************/
function proximaRondaProduccion( $fecha, $hora, $tiempo ){
	
//	$val = date( "H:i:s", strtotime( "$fecha $hora" )+3600*$tiempo );
	$val = strtotime( "$fecha $hora" )+3600*$tiempo;
	
	return $val;
}

/************************************************************************************************************************************************************************
 * 																				FIN DE FUNCIONES
 ************************************************************************************************************************************************************************/
$usuarioValidado = true;
	
if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
	echo "Usuario no valido";
	exit;
}else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
	echo "Usuario no valido";
}
	
elseif( isset( $opcionAjax ) && !empty($opcionAjax) ){
	
	include_once( "conex.php" );
	mysql_select_db( "matrix" );
	
	//Llamadas Ajax
	switch( $opcionAjax ){
		
		case 1:
			movimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $cantidad, $unidadFraccion );
			//registrarMovimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $cantidad, $unidadFraccion );
			break;
			
		case 2:
			registrarEncabezadoProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo );
			break;
			
		case 3:
			break;
			
		case 4:
			break;
			
		case 5:
			break;
			
		default: break;
	}
}
else{
?>
<head>
</head>

<script>

/************************************************************
 * AJAX
 ***********************************************************/

 	/******************************************************************
 	 * Realiza una llamada ajax a una pagina
 	 * 
 	 * met:		Medtodo Post o Get
 	 * pag:		Página a la que se realizará la llamada
 	 * param:	Parametros de la consulta
 	 * as:		Asincronro? true para asincrono, false para sincrono
 	 * fn:		Función de retorno del Ajax
 	 *
 	 * Nota: Si la llamada es GET las opciones deben ir con la pagina.
 	 ******************************************************************/
	function consultasAjax( met, pag, param, as, fn ){
	
		this.metodo = met;
		this.parametros = param; 
		this.pagina = pag;
		this.asc = as;
		this.fnchange = fn; 
	
		try{
			this.ajax=nuevoAjax();
	
			this.ajax.open( this.metodo, this.pagina, this.asc );
			this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			this.ajax.send( this.parametros );
	
			if( this.asc ){
				var xajax = this.ajax;
	//			this.ajax.onreadystatechange = this.fnchange;
				this.ajax.onreadystatechange = function(){ fn( xajax ) };
				
				if ( !estaEnProceso(this.ajax) ) {
					this.ajax.send(null);
				}
			}
			else{
				return this.ajax.responseText;
			}
		}catch(e){	}
	}

/************************************************************
 * AJAX
 ***********************************************************/

var tipoArticuloSelecconado = 0;
var eliminar = false;

//Datos constantes
var celdaArticulo = 0;	//Celda del detalle del tipo de articulo que contiene el indice de la celda codigo del articulo
var celdaCantidad = 2;	//Celda del detalle del tipo de articulo que contiene el indice de la celda cantidad
var celdaRonda = 2;		//Celda de la tabla de tipo de articulo que contiene la ronda que se muestra en el detalle
var celdaTipo = 0;		//Celda de la tabla de tipo de articulo que contiene el codigo del tipo de articulo

/******************************************************************************************
 * Muestra u oculta el detalle de los articulos
 * El detalle del articulo siempre es la fila que le siga al articulo seleccionado
 ******************************************************************************************/
function mostrarOcultarDetalle( campo ){

	fila = campo.parentNode.parentNode;
	tabla = fila.parentNode;

	if( tabla.rows[ fila.rowIndex+1 ].style.display == 'none' ){
		 tabla.rows[ fila.rowIndex+1 ].style.display = ''; 
	}
	else{
		tabla.rows[ fila.rowIndex+1 ].style.display = 'none';
	}
}

function retornar( campo ){

	campo.parentNode.parentNode.parentNode.parentNode.style.display = 'none';

	if( eliminar ){
		var tablaPrincipal = document.getElementById( "tbTiposArticulos" );
		tablaPrincipal.rows[ tipoArticuloSelecconado ].parentNode.removeChild( tablaPrincipal.rows[ tipoArticuloSelecconado ] );
	}

	var tbPrincipal = document.getElementById( "tbTiposArticulos" );

	for( var i = 1 ; i < tbPrincipal.rows.length; i++ ){
		tbPrincipal.rows[i].style.display = '';
	}

	tipoArticuloSelecconado = 0;
	eliminar = false;
}

/**
 * 
 */
function seleccionarTipoArticulo( campo ){

	tipoArticuloSelecconado = campo.parentNode.parentNode.rowIndex;

	var tipoArt = campo.parentNode.parentNode.cells[0].innerHTML;

	var tbMostrar = document.getElementById( "tbDetalle"+tipoArt );

	if( tbMostrar ){
		tbMostrar.style.display = "";
	}

	var tbPrincipal = campo.parentNode.parentNode.parentNode;

	for( var i = 1 ; i < tbPrincipal.rows.length; i++ ){
		if( tipoArticuloSelecconado != i ){
			tbPrincipal.rows[i].style.display = 'none';
		}
	}
}

/****************************************************************************************************************
 * Realiza llamada ajax para guardar el movimiento de un articulo
 ****************************************************************************************************************/
function grabarMovimientoArticulo( campo ){

	if( true || campo.checked == true ){
		
		if( confirm( "Desea grabar el articulo" ) ){

			var tablaPrincipal = document.getElementById( "tbTiposArticulos" );
		
			if( tablaPrincipal ){
		
				var ronda = tablaPrincipal.rows[ tipoArticuloSelecconado ].cells[ celdaRonda ].innerHTML;
				var tipoArticulo = tablaPrincipal.rows[ tipoArticuloSelecconado ].cells[ celdaTipo ].innerHTML;
		
				var fila = campo.parentNode.parentNode;
			
				var articulo = fila.cells[ celdaArticulo ].innerHTML;
				var cantidad = fila.cells[ celdaCantidad ].innerHTML.split( " " );
		
				var wbasedato = document.getElementById( "wbasedato" ).value;
				var fecha = document.getElementById( "hiFecha" ).value;
			
				parametros = "opcionAjax=1&fecha="+fecha+"&ronda="+ronda+"&tipoArticulo="+tipoArticulo+"&articulo="+articulo+"&cantidad="+cantidad[0]+"&wbasedato="+wbasedato+"&unidadFraccion="+cantidad[1];
		
				var resultado = consultasAjax( "POST", "ciclosProduccion1.php", parametros, false, '' );
		
//				alert( ".....|-"+resultado+"-|" );

				if( resultado.length < 3 ){

					if( false && campo.checked == false ){
						return;
					}
					else{
						//reviso que todos los campos esten marcado en on
						//si es así se registra el encabezado
						var tablaDetalle = campo.parentNode.parentNode.parentNode;
						var celda = campo.parentNode.cellIndex;
						var produccionCompleta = true;
	
						for( var i = 1; i < tablaDetalle.rows.length-1; i++ ){

							if( tablaDetalle.rows[i].cells[ celda ] && tablaDetalle.rows[i].cells[ celda ].firstChild.checked == false ){
								produccionCompleta = false;
								break;
							}
						}
	
						if( produccionCompleta ){
							
							grabarEncabezadoTipo( campo );
							eliminar = true;
						}
						else{
							eliminar = false;
						}
					}
				}
				else{
					alert( "Error inesperado al grabar Movimiento: "+resultado );
				}
			}
		}
		else{
			campo.checked = false;
		}
	}
	else{
		campo.checked = true;
	} 
}

/****************************************************************************************************************
 * Graba el encabezado para el tipo de articulo
 ****************************************************************************************************************/
function grabarEncabezadoTipo( campo ){

	var tablaPrincipal = document.getElementById( "tbTiposArticulos" );

	if( tablaPrincipal ){

		var ronda = tablaPrincipal.rows[ tipoArticuloSelecconado ].cells[ celdaRonda ].innerHTML;
		var tipoArticulo = tablaPrincipal.rows[ tipoArticuloSelecconado ].cells[ celdaTipo ].innerHTML;

		var fila = campo.parentNode.parentNode;
	
		var wbasedato = document.getElementById( "wbasedato" ).value;
		var fecha = document.getElementById( "hiFecha" ).value;
	
		//$fecha, $ronda, $tipoArticulo
		
		parametros = "opcionAjax=2&fecha="+fecha+"&ronda="+ronda+"&tipoArticulo="+tipoArticulo+"&wbasedato="+wbasedato;

		var resultado = consultasAjax( "POST", "ciclosProduccion1.php", parametros, false, '' );

		if( resultado.length > 2 ){
			alert( "Error inesperado al grabar encabezado: "+resultado );
		}
	} 
}

</script>
<body>
<?php 
//echo "RECORDAR ARREGLAR EL QUERY DEL KARDEX.....<BR>";
//echo date("Y-m-d H:i:s");
	
	$wactualiz = " 1.0 Marzo 31 de 2011";

	include_once("root/comun.php");

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	
	$fechaActual = date("Y-m-d");
//	$fechaActual = "2011-03-26";
	
	echo "<INPUT TYPE='hidden' id='wbasedato' name='wbasedato' value='$wbasedato'>";
	echo "<INPUT TYPE='hidden' id='wcenmez' name='wcenmez' value='$wcenmez'>";
	echo "<INPUT type='hidden' id='hiFecha' name='hiFecha' value='$fechaActual'>";
	
	encabezado("Ciclos de producción",$wactualiz,"clinica");
	
	if (!$usuarioValidado){
		echo '<span class="subtituloPagina2" align="center">';
		echo 'Error: Usuario no autenticado';
		echo "</span><br><br>";

		terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
	}
	
	//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	

	$tiposAriculos = Array();	//tipos de articulo
	$datosTipo = Array();		//datos del kardex por tipo
	$detalleArticulo = Array();		//Muesra el detalle de articulos por paciente
	
	$frecuenciaPreparacion = 6;
	
//	$ronda = "20:00:00";
	
	$arRondas = crearRondas( 2 );
	
	$frecuencias = consultarFrecuencias();
	
	//Consultando los tipos de protocolo
	$sql = "SELECT 
				* 
			FROM
				{$wbasedato}_000099 a
			WHERE
				Tarest = 'on'
				AND tarhcp != '00:00:00'
				AND tarhcd != '00:00:00'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
				
	if( $numrows > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['codigo'] = $rows[ 'Tarcod' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['nombre'] = $rows[ 'Tardes' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['tiempoPreparacion'] = $rows[ 'Tarpre' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCorteProduccion'] = $rows[ 'Tarhcp' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCaroteDispensacion'] = $rows[ 'Tarhcd' ];
			
			$aux = consultarUltimaRonda( $conex, $wbasedato, $rows[ 'Tarcod' ] );
			$auxfec = ""; 
			@list( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'] ) = explode( " ", $aux );
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] = proximaRondaProduccion( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'], $rows[ 'Tarpre' ] );; 
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['tieneArticulos'] = 0;
		}
	}
	
	if( empty( $detalle ) || $detalle == 'off' ){
		
		echo "<div>";
		
		//Consultando el kardex
		//	Tener en cuenta que el paciente no este de alta o con alta en proceso
		$sql = "SELECT 
					* 
				FROM
					{$wbasedato}_000054 a
				WHERE
					kadfec = '".date( "Y-m-d" )."'
					AND kadare != 'on'
					AND kadsus != 'on'
					AND kadest = 'on'
					AND kadcdi > 0 
					AND kadcdi - kaddis > 0
				ORDER BY
					kadpro, kadart
				";
					
		$sql = "SELECT 
					* 
				FROM
					{$wbasedato}_000054 a
				WHERE
					kadfec = '".date( "Y-m-d" )."'
					AND kadsus != 'on'
					AND kadest = 'on'
				ORDER BY
					kadpro, kadart
				"; //echo "<br>........<pre>$sql</pre>";
					
//		$sql = "SELECT 
//					* 
//				FROM
//					{$wbasedato}_000054 a, {$wcenmez}_000002 b, {$wcenmez}_000001 c
//				WHERE
//					kadfec = '2011-03-26'
//					AND kadare = 'on'
//					AND kadsus != 'on'
//					AND kadest = 'on'
//					AND kadess != 'on'
//					AND artcod = kadart
//					AND arttip = tipcod
//					AND tippro = 'on'
//				ORDER BY
//					kadpro, kadart
//				"; echo ".......<pre>$sql</pre>";
//					
//		$sql = "SELECT 
//					* 
//				FROM
//					{$wbasedato}_000054 a, {$wcenmez}_000002 b, {$wcenmez}_000001 c
//				WHERE
//					kadfec = '".date("Y-m-d")."'
//					AND kadest = 'on'
//					AND artcod = kadart
//					AND arttip = tipcod
//					AND tippro = 'on'
//				ORDER BY
//					kadpro, kadart
//				"; echo ".......<pre>$sql</pre>";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows ){
		
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
				
				$can = 0;
				
				$esGenerico = esArticuloGenerico( $conex, $wbasedato, $wcenmez, $rows[ 'Kadart' ] );
				
				//Busco proxima ronda
				if( $esGenerico ){
					$tipo = $esGenerico;
				}
				else{
					$tipo = $rows[ 'Kadpro' ];
				}
				
				if( isset( $tiposAriculos[ $tipo ] ) ){
				
					//$fecpxr: fecha proxima ronda
					list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s" ,$tiposAriculos[ $tipo ]['proximaRonda'] ) );
					$tiempoPreparacion = $tiposAriculos[ $tipo ]['tiempoPreparacion'];
					$corteProduccion = $tiposAriculos[ $tipo ]['horaCorteProduccion'];
					$corteDispensacion = $tiposAriculos[ $tipo ]['horaCaroteDispensacion'];
					
					$horaSiguiente = "";
//					echo "<br><br>......".$rows['Kadart'];
//					$perteneceRonda = perteneceRonda( $fechaActual, $ronda, $tiempoPreparacion, $rows['Kadfin'], $rows['Kadhin'], $frecuencias[ $rows['Kadper'] ], $horaSiguiente );
//					$perteneceRonda = perteneceRonda( $fecpxr, $ronda, $tiempoPreparacion, $rows['Kadfin'], $rows['Kadhin'], $frecuencias[ $rows['Kadper'] ], $horaSiguiente, $despuesHoraCortePx );
					$perteneceRonda = perteneceRonda( $fecpxr, $ronda, $tiempoPreparacion, $rows['Kadfin'], $rows['Kadhin'], $frecuencias[ $rows['Kadper'] ], $horaSiguiente, $despuesHoraCortePx, $rows['Kaddma'], $rows['Kaddia'] );
					
					$mostrarRonda = mostrarRonda( $fecpxr, $ronda, $corteProduccion, $corteDispensacion );
					
//					$rondaProducida = rondaProducida( $conex, $wbasedato, $fechaActual, $ronda, $tipo );
					$rondaProducida = rondaProducida( $conex, $wbasedato, $fecpxr, $ronda, $tipo );
					
					$tiposAriculos[ $tipo ]['rondaProducida'] = $rondaProducida;
					
					if( $perteneceRonda ){
						$tiposAriculos[ $tipo ]['tieneArticulos']++;
					}
					
					
					if( !$rondaProducida && time() > $tiposAriculos[ $tipo ]['proximaRonda'] ){
						$mostrarRonda = true;
					}
					elseif( !$rondaProducida && $despuesHoraCortePx ){
						$mostrarRonda = true;
					}
					
//					echo "<br>.....tipo: $tipo";
//					echo "<br>.....Ronda: $ronda";
//					echo "<br>.....his: ".$rows[ 'Kadhis' ];
//					echo "<br>.....his: ".$rows[ 'Kadart' ];
//					echo "<br>.....Pertenece: ".$perteneceRonda;
					
					if( $perteneceRonda && $mostrarRonda && !$rondaProducida ){
						
						//Miro si ya marcaron el articulo como producido
						//Si es así muestra la información de lo producido, de lo contrario de lo que se va a producir
						if( !isset($datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['producido']) ){
							$tieneProduccion = tieneProduccion( $conex, $wbasedato, $wcenmez, $fechaActual, $ronda, $rows[ 'Kadart' ], $tipo, $datosTipo );
						}
						else{
							$tieneProduccion = $datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['producido'];
						}
						
						if( true || !$tieneProduccion ){
							
//							$totalAplicaciones = calcularCantidadAplicacionesRondaProduccion( $fecpxr, $ronda, $tiempoPreparacion, $horaSiguiente, $frecuencias[ $rows['Kadper'] ] );
							$totalAplicaciones = calcularCantidadAplicacionesRondaProduccion( $fecpxr, $ronda, $tiempoPreparacion, $horaSiguiente, $frecuencias[ $rows['Kadper'] ], $rows[ 'Kaddma' ], $rows[ 'Kaddia' ] );
//							echo "<br>.......Total aplica: $totalAplicaciones";
							$can = ceil( $rows['Kadcfr']/$rows['Kadcma']*$totalAplicaciones );
								
							if( $can < 0 ){
								$can = 0;
							}
								
							if( !$tieneProduccion ){
								$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['codArticulo'] = $rows[ 'Kadart' ];
								$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo'] = nombreArticulo( $conex, $wbasedato, $wcenmez, $rows[ 'Kadart' ] );
								@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['canArticulo'] += $can;
								@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['canDosis'] += $rows['Kadcfr']*$totalAplicaciones;
								@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['uniDosis'] = $rows['Kadufr'];
								@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['fraUnidad'] = $rows['Kadcma'];
								//							@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['producido'] = false;
								@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['producido'] = false;
							}
							
							//informacion detallada

							/*	Historia
							 * 		Ingreso
							 * 		Cco en el que se encuentra
							 * 		Habitación actual
							 * 		Dosis
							 * 		Total de unidades que se gastaría
							 * */
							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['codArticulo'] = $rows[ 'Kadart' ];
							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['nomArticulo'] = $datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo'];
							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['dosis'] += $rows[ 'Kadcfr' ];
							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['unidadesTotales'] += $can;
							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['nombre'] = consultarNombrePaciente( $conex, $rows[ 'Kadhis' ] );
							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['unidadDosis'] = $rows['Kadufr'];
						}
						else{
//							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['codArticulo'] = $rows[ 'Kadart' ];
//							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['nomArticulo'] = $datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo'];
//							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['dosis'] += $rows[ 'Kadcfr' ];
//							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['unidadesTotales'] += $can;
//							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['nombre'] = consultarNombrePaciente( $conex, $rows[ 'Kadhis' ] );
//							@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ]['unidadDosis'] = $rows['Kadufr'];
						}
					}
				}
			}
		}
		
		if( count($datosTipo) > 0 ){
			
			echo "<table align='center' id='tbTiposArticulos'>";
	
			echo "<tr class='encabezadotabla' align='center'>";
			echo "<td>Codigo</td>";
			echo "<td>Tipo de art&iacute;culos</td>";
			echo "<td>Ronda sin<br>producir</td>";
			echo "<td>Para</td>";
			echo "<td>Detalle</td>";
			echo "</tr>";
	
			$i = 0;
			foreach( $tiposAriculos as $keyTipos => $valueTipos ){
				
				if( count( @$datosTipo[ $keyTipos ] ) > 0 ){
				
					$totalRondas = $valueTipos['tiempoPreparacion']/2;
				
					$rondaActual = date( "H:i:s" ,$valueTipos['proximaRonda'] );
				
					$class = "class='fila".(($i % 2)+1)."'";
				
					echo "<tr $class>";
					echo "<td align='center'>$keyTipos</td>";
					echo "<td>{$valueTipos['nombre']}</td>";
					echo "<td>$rondaActual</td>";
					echo "<td>$totalRondas ronda(s)</td>";
					echo "<td align='center'><a onClick='seleccionarTipoArticulo(this)'>ver</a></td>";
					echo "</tr>";
				
					$i++;
				}
			}
			
			echo "</table>";
		}
		else{
			echo "<p><center><b>Sin productos para producir</b></center></p>";
		}
		
		echo "</div>";
		
		//dibujanto las respectivas tablas por tipo
		foreach( $datosTipo as $keyDatos => $valueDatos ){
			
			mostrarArticulosRonda( $valueDatos, $keyDatos, $detalleArticulo );
		}
		
		//Registro encabezados de los articulos si no se encuentra nada para producir, esto para que
		//muestre la ronda siguiente
		foreach( $tiposAriculos as $keyTipos => $valueTipos ){
			
			if( !isset($valueTipos['rondaProducida']) ){
				list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s" ,$valueTipos['proximaRonda'] ) );
				$valueTipos['rondaProducida'] = rondaProducida( $conex, $wbasedato, $fecpxr, $ronda, $keyTipos );
			}
			
			if( $valueTipos['tieneArticulos'] == 0 && time() > $valueTipos['proximaRonda'] && !$valueTipos['rondaProducida'] ){
				registrarEncabezadoProduccion( $conex, $wbasedato, date( "Y-m-d", $valueTipos['proximaRonda'] ), date( "H:i:s", $valueTipos['proximaRonda'] ), $keyTipos );
			}
		}
	}
	
	echo "<br></br>";
	echo "<table align='center'>";
	echo "<tr><td><INPUT type='button' value='Cerrar ventana' style='width:100' onClick='cerrarVentana()'></td></tr>";
	echo "</table>";
	
	echo "</body>";
}
?>

