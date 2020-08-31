<head>
</head>

<style>
input{ 
	width:100px; 
	}
</style>

<body>
<?php
include_once("conex.php");
/****************************************************************************************************************************
 * Este reporte muestra cuantos productos han sido creados en Central de Mezclas y cuantos han sido cargado a los pacientes
 *
 * Fecha de creación: Septiembre 1 de 2011
 ****************************************************************************************************************************/
 
/****************************************************************************************************************************
 *														FUNCIONES
 ****************************************************************************************************************************/
 
 /************************************************************************************************************
  * Consulta la informacion para una ronda, es decir la hora, la cantidad a dispensar y la cantidad dispensada
  ************************************************************************************************************/
 function consultarInformacionHora( $horasAplicacion, $ronda, &$hora, &$cdi, &$dis ){
 
	$val = "";
	
	if( empty( $horasAplicacion ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicacion );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		if( $ronda == $valores[0] ){
			$hora = $valores[0];
			$cdi = $valores[1];
			$dis = $valores[2];
			$val = $exp[$i];
			break;
		}
	}
	
	return $val; 
 }
 
 /************************************************************************
  * Consulto la regleta del dia anterior
  ************************************************************************/
 function consultarMediaNocheDiaAnterior( $conex, $wbasedato, $reg ){
 
	$val = "";
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000054
			WHERE
				id = '$reg'
			"; //echo "......<pre>$sql</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$val = $rows[ 'Kadcpx' ];
	}
	
	return $val;
 }
 
 /************************************************************************************
 * Consulta el saldo de dispensacion grabado
 ************************************************************************************/ 
function consultarSaldoDispensacionGrabado( $horasAplicar ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < 1; $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		// $val = $valores[1];
		
		if( $valores[0] == 'Ant' ){
			$val = $valores[2];
		}
	}
	
	return $val;
}

/************************************************************************************
 * Consulta el saldo de dispensacion
 ************************************************************************************/ 
function consultarSaldoDispensacion( $horasAplicar ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < 1; $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		// $val = $valores[1];
		
		if( $valores[0] == 'Ant' ){
			$val = $valores[1];
		}
	}
	
	return $val;
}
 
/**
 * Consulto el total de saldo en piso, teniendo en cuenta la historia
 */
 function consultarSaldosEnPiso( $conex, $wbasedato, $historia, $ingreso, $articulo ){
 
	$val = 0;
 
	$sql = "SELECT
				SUM(spauen - spausa)
			FROM
				{$wbasedato}_000004
			WHERE
				spahis = '$historia'
				AND spaing = '$ingreso'
				AND spaart = '$articulo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$rows = mysql_fetch_array( $res );
		
		$val = $rows[0];
	}
	
	return $val;
 }
 
 /****************************************************************************************
 * Calcula la cantidad a dispensar hasta una ronda dada
 * 
 * @return unknown_type
 ****************************************************************************************/
function cantidadTotalADispensarRonda( $horasAplicar, $ronda ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += $valores[1];
		
		if( $ronda == $valores[0] ){
			break;
		}
	}
	
	return $val;
}
 
 /****************************************************************************************
 * Calcula la cantidad dispensada hasta una ronda dada
 * 
 * @return unknown_type
 ****************************************************************************************/
 function cantidadTotalDispensadaRonda( $horasAplicar, $ronda ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += $valores[2];
		
		if( $ronda == $valores[0] ){
			break;
		}
	}
	
	return $val;
}
 
 
/******************************************************************************************************************************
 * Dada una regleta, que tiene la siguiente forma
 * [Hora1]-Can1-Dis1,[Hora2]-Can2-Dis2,[Hora3]-Can3-Dis3,...,[HoraN]-CanN-DisN
 *
 * Siendo HoraN, un formato de hora de 24 con minutos y segundos (ejem: 10:00:00), devuelve las horas en que el medicamento 
 * fue cargado al paciente
 *
 * @param	$regleta	String
 ******************************************************************************************************************************/
function consultarHorasGrabadas( $regleta ){

	$val = "";
	
	if( !empty($regleta) ){
		
		//Busco todas las horas posibles segun la regleta
		$horasRegleta = explode( ",", $regleta );
		
		//Consulto las horas de aplicacion, estas son todas aquellas que CanN sean mayores a 0
		$paso = false;
		for( $i = 0; $i < count( $horasRegleta ); $i++ ){
			
			//indice	Significado
			//	0		Hora
			//	1		Cantidad a cargar
			//	2		Cantidad dispensada
			$valores = explode( "-", $horasRegleta[$i] );
			
			if( $valores[0] == "00:00:00" & $paso ){
				break;
			}
			
			if( $valores[0] != "Ant" ){
				if( $valores[2] > 0 ){
					$val .= "-".substr( $valores[0], 0, 2 );
				}
				$paso = true;
			}
		}
	}
	
	return substr( $val, 1 );
}
  

/******************************************************************************************************************************
 * Dada una regleta, que tiene la siguiente forma
 * [Hora1]-Can1-Dis1,[Hora2]-Can2-Dis2,[Hora3]-Can3-Dis3,...,[HoraN]-CanN-DisN
 *
 * Siendo HoraN, un formato de hora de 24 con minutos y segundos (ejem: 10:00:00), devuelve las hora en que el medicamento 
 * debe ser aplicado
 *
 * @param	$regleta	String
 ******************************************************************************************************************************/
function consultarHorasAdministracion( $regleta ){

	$val = "";
	
	if( !empty($regleta) ){
		
		//Busco todas las horas posibles segun la regleta
		$horasRegleta = explode( ",", $regleta );
		
		//Consulto las horas de aplicacion, estas son todas aquellas que CanN sean mayores a 0
		$paso = false;
		for( $i = 0; $i < count( $horasRegleta ); $i++ ){
			
			//indice	Significado
			//	0		Hora
			//	1		Cantidad a cargar
			//	2		Cantidad dispensada
			$valores = explode( "-", $horasRegleta[$i] );
			
			if( $valores[0] == "00:00:00" && $paso ){
				break;
			}
			
			if( $valores[0] != "Ant" ){
				if( $valores[1] > 0 ){
					$val .= "-".substr( $valores[0], 0, 2 );
				}
				$paso = true;
			}
		}
	}
	
	return substr( $val, 1 );
}

/****************************************************************************************************
 * Consulto cuantos medicamentos hay en nevera (creados)
 ****************************************************************************************************/
function consultarSaldoLotesPorArticulo( $conex, $wcenmez, $producto, $fecha ){

	$val = 0;
	
	//Consulto el saldo de los articulos siempre y cuando no se halla vencido el medicamento
	$sql = "SELECT
				SUM(plosal) as Saldo
			FROM
				{$wcenmez}_000004
			WHERE
				plopro = '$producto'
				AND plosal > 0 
				AND plofve >= '$fecha'
			"; //echo ".....<pre>$sql</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		@$val = $rows['Saldo'];
	}
	
	return $val;
}
  
/************************************************************************************************
 * Devuelve un array con la informacion del paciente ( nombre, historia, ingreso, cedula, 
 * habitación, centro de costo )
 * 
 * @param $conex
 * @param $his
 * @return unknown_type
 ************************************************************************************************/
function consultarInformacionDelPaciente( $conex, $wbasedato, $his, $ori, &$paciente ){
	
	$val = "";
	
	//Consulta la inforamcion basica del paciente
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000020 c, root_000036 a, root_000037 b 
			WHERE
				habhis = '$his'
				AND habhis = orihis				
				AND oriced = pacced
				AND pactid = oritid 
				AND oriori = '$ori'
			"; //echo ".........<pre>$sql</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$rows = mysql_fetch_array( $res );
		
		$paciente[ $his ]['historia'] = $rows[ 'Habhis' ];
		$paciente[ $his ]['ingreso'] =  $rows[ 'Habing' ];
		$paciente[ $his ]['nombre'] = $rows['Pacno1']." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'];
		$paciente[ $his ]['cedula'] = $rows['Pacced'];
		$paciente[ $his ]['cco'] = $rows['Habcco'];
		$paciente[ $his ]['habitacion'] = $rows['Habcod'];
	}
	
	return $val;
}

/****************************************************************************************************************************
 * Consulta la informacion basica de un producto ( codigo, nombre comercial, nombre generico, saldo )
 ****************************************************************************************************************************/
function consultarInformacionProducto( $conex, $wcenmez, $wbasedato, $producto, &$articulos ){


	$sql = "SELECT
					*
				FROM
					{$wbasedato}_000026
				WHERE
					artcod = '$producto'
				";
	
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$rows = mysql_fetch_array( $res );
		
		$articulos[ $rows['Artcod'] ][ 'codigo' ] = $rows['Artcod'];
		$articulos[ $rows['Artcod'] ][ 'nombreComercial' ] = $rows['Artcom'];
		$articulos[ $rows['Artcod'] ][ 'nombreGenerico' ] = $rows['Artgen'];
		$articulos[ $rows['Artcod'] ][ 'Saldo' ] = consultarSaldoLotesPorArticulo( $conex, $wcenmez, $producto, date( "Y-m-d" ) );
		$articulos[ $rows['Artcod'] ][ 'unidadMinima' ] = $rows['Artuni'];
	}
	else{

		$sql = "SELECT
					*
				FROM
					{$wcenmez}_000002
				WHERE
					artcod = '$producto'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
		
			$rows = mysql_fetch_array( $res );
			
			$articulos[ $rows['Artcod'] ][ 'codigo' ] = $rows['Artcod'];
			$articulos[ $rows['Artcod'] ][ 'nombreComercial' ] = $rows['Artcom'];
			$articulos[ $rows['Artcod'] ][ 'nombreGenerico' ] = $rows['Artgen'];
			$articulos[ $rows['Artcod'] ][ 'Saldo' ] = consultarSaldoLotesPorArticulo( $conex, $wcenmez, $producto, date( "Y-m-d" ) );
			$articulos[ $rows['Artcod'] ][ 'unidadMinima' ] = $rows['Artuni'];
		}
	}
}

/**
 * Dibuja la tabla con los resultados
 */
function pintarResultados( $detalleTabla, $pacientes, $articulos ){

	if( !empty($detalleTabla) ){
	
		echo "<table align='center'>";
		
		//Creo encabezado de la tabla
		echo "<tr class='encabezadotabla'>";
		echo "<th>Historia</td>";
		echo "<th>Nombre del<br>paciente</td>";
		echo "<th>Habitaci&oacute;n</td>";
		echo "<th>C&oacute;digo del<br>articulo</td>";
		echo "<th>Nombre del<br>articulo</td>";
		echo "<th>Horarios de<br>Administraci&oacute;n</td>";
		echo "<th>Dosis Grabadas</th>";
		echo "<th>Dosis en nevera</th>";
		echo "<th>Dosis pendientes<br>por producir</th>";		
		echo "<th>Saldo de dispensaci&oacute;n</th>";
		echo "<th>Saldo grabado</th>";
		echo "<th>Dosis pendientes<br>por grabar</th>";
		echo "<th>Enviar</th>";
		echo "<th>Suspendido</th>";
		echo "<th>Confirmado</th>";
		echo "<th>Aprobado en el perfil</th>";
		echo "<th>Saldo en servicio</th>";
		echo "<th>Kardex automático</th>";
		
		echo "</tr>";
		
		//Mostrando la informacion encontrada
		$j = 0;
		$l = 0;
		foreach( $pacientes as $keyPaciente => $valuePaciente ){
			
			//Consulto cuantos medicamentos tiene un paciente
			$rowsSpan = $valuePaciente[ 'totalArticulos' ];
			
			for( $k = 0; $k < $rowsSpan; $k++ ){
			
				$classFilaPaciente = "class='fila".( ($j%2)+1 )."'";
				$classFilaArticulo = "class='fila".( ($l%2)+1 )."'";
			
				//Creo la fila para los pacientes
				if( $k == 0 ){	//Esto es para que muestre una sola vez la información del paciente por piso
				
					echo "<tr $classFilaPaciente>";
					
					//Mostrando la inforamcion del paciente
					echo "<td align='center' rowspan='$rowsSpan'>";
					echo $valuePaciente['historia']."-".$valuePaciente['ingreso'];
					echo "</td>";
					echo "<td rowspan='$rowsSpan'>";
					echo $valuePaciente['nombre'];
					echo "</td>";
					echo "<td align='center' rowspan='$rowsSpan'>";
					echo $valuePaciente['habitacion'];
					echo "</td>";
					
					$l = $j;
				}
				else{
					echo "<tr $classFilaArticulo>";
				}
				
				$i = $valuePaciente['filasAsociadas'][$k];
				
				//Codigo del articulo
				echo "<td align='center'>";
				echo $articulos[ $detalleTabla[$i]['articulo'] ]['codigo'];
				echo "</td>";
				
				//Nombre del articulo
				echo "<td>";
				echo $articulos[ $detalleTabla[$i]['articulo'] ]['nombreComercial'];
				echo "</td>";
				
				//Horarios de administración
				echo "<td align='center'>";
				echo $detalleTabla[$i]['horasAplicacion'];
				echo "</td>";
				
				//Dosis grabadas
				echo "<td align='center'>";
				echo $detalleTabla[$i]['cantidadGrabada']."";
				echo "</td>";
				
				//Dosis en nevera
				echo "<td align='center'>";
				echo $articulos[ $detalleTabla[$i]['articulo'] ][ 'Saldo' ];
				echo "</td>";
				
				//Dosis pendientes por producir
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'dosisPendientes' ]."";
				echo "</td>";
				
				
				
				//Cantidad sin dispensar dia anterior
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'saldoDispensacion' ];
				echo "</td>";
				
				//Cantidad grabada del saldo de dispensacion del día anterior
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'saldoGrabado' ];
				echo "</td>";
				
				//Dosis pendientes por grabar             
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'dosisPorGrabar' ]."";
				echo "</td>";
				
				//Enviar
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'enviar' ];
				echo "</td>";
				
				//Suspendido
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'suspendido' ];
				echo "</td>";
				
				//Confirmado
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'confirmado' ];
				echo "</td>";
				
				//Aprobado en el perfil
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'aprobado' ];
				echo "</td>";
				
				//Saldo en servicio
				echo "<td align='center'>";
				if( empty($detalleTabla[ $i ][ 'saldoPiso' ]) ){
					echo "0 ".$articulos[ $detalleTabla[$i]['articulo'] ][ 'unidadMinima' ];
				}
				else{
					echo $detalleTabla[ $i ][ 'saldoPiso' ]." ".$articulos[ $detalleTabla[$i]['articulo'] ][ 'unidadMinima' ];
				}
				echo "</td>";
				
				//Kardex automático
				echo "<td align='center'>";
				echo $detalleTabla[ $i ][ 'kardexAutomatico' ];
				echo "</td>";
				
				
				
				echo "</tr>";
				
				$l++;
			}
			$j++;
			
		}
		
		echo "</table>";
	}
}

/****************************************************************************************************************************
 * 												FIN DE FUNCIONES
 ****************************************************************************************************************************/
  
/****************************************************************************************************************************
 *												INICIO DEL PROGRAMA
 ****************************************************************************************************************************/
include_once( "root/comun.php" );
   
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

	encabezado( "MONITOR DISPENSACION", "1.0 Septiembre 6 de 2011" ,"clinica" );

	echo "<form>";

	if( empty($fecha) ){
		$fecha = date( "Y-m-d" );
	}
	
	if( empty($mostrar) ){
		$mostrar = "off";
	}
	
	echo "<INPUT type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	//$fecha = date( "Y-m-d" );
	
	if( $mostrar == "off" ){	//Si no se han elegido los parametros

		echo "<table align='center'>";

		// echo "<tr class='encabezadotabla'>";
		// echo "<td class='fila1'>Servicio de origen</td>";
		
		// //Buscando los centro de costos de traslado (SF y CM)
		// //Estos son los de origen
		// $sql = "SELECT
					// *
				// FROM
					// {$wbasedato}_000011
				// WHERE
					// ccotra = 'on'
					// AND ccofac = 'on'
				// ";
				
		// $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		// echo "<td class='fila2'>";
		// echo "<select name='slCcoDestino'>";
		
		// for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			// echo "<option value='{$rows['Ccocod']}'>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
		// }
		
		// echo "</select>";
		// echo "</td>";
		
		// echo "</tr>";
		
		
		
		echo "<tr class='encabezadotabla'>";
		echo "<td class='fila1'>Centro de costos</td>";
		
		//Buscando los centro de costos de traslado (SF y CM)
		//Estos son los de origen
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000011
				WHERE
					ccocpx = 'on'
					AND ccohos = 'on'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		echo "<td class='fila2'>";
		echo "<select name='slCcoDestino'>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			echo "<option value='{$rows['Ccocod']}'>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
		}
		
		echo "</select>";
		echo "</td>";
		
		echo "</tr>";
		echo "<tr>";
		
		echo "<td class='fila1'><b>Fecha</b></td>";
		
		echo "<td class='fila2'>";
		campoFechaDefecto( "fecha", $fecha );
		echo "</td>";
		echo "</tr>";

		
		echo "</tr>";

		echo "</table>";
		
		echo "<br><table align='center'>";
		
		echo "<tr><td>";
		echo "<center><INPUT type='button' value='Aceptar' onclick='document.forms[0].submit();'></center>";
		echo "</td>";
		
		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></center>";
		echo "</td></tr>";
		
		echo "</table>";
		
		echo "<INPUT type='hidden' name='mostrar' value='on'>";
		
		
	}
	else{	//Ya se escogieron los parametos
	
		//Mostrando informacion de fecha
		echo "<center style='font-size:20pt'><b>$fecha</b></center>";
		echo "<center style='font-size:14pt'>Desde las <b>00:00:00</b> - <b>23:59:59</b></center>";
		echo "<br>";
	
		//Consulto los medicamentos del dia para central de mezclas
		//Todo medicamento de la central de mezclas debe estar confirmado por enfermeria (kadcnf = on ) y son 
		//los unicos que estan confirmados
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000053 a, 
					{$wbasedato}_000054 b,
					{$wbasedato}_000020 c
				WHERE
					kadfec = '$fecha'
					AND karhis = kadhis
					AND karing = kading
					AND karcco = kadcco
					AND a.fecha_data = kadfec
					AND kadest = 'on'
					AND kadhis = habhis
					AND kading = habing
					AND habcco = '$slCcoDestino'
				"; //echo "......<pre>$sql</pre>";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
		
			$detalleTabla = Array();	//Contiene la informacion de la tabla a dibujar
			$pacientes = Array();		//Contiene la informacion de los pacientes
			$articulos = Array();		//Contiene la informacion de la tabla
		
			for( $i = 0, $fila = 0; $rows = mysql_fetch_array( $res ); $i++, $fila++ ){
			
				//Consulto nombre la informacion del paciente si no se ha consultado
				if( empty( $pacientes[ $rows['Kadhis'] ] ) ){
					consultarInformacionDelPaciente( $conex, $wbasedato, $rows['Kadhis'], $wemp_pmla, $pacientes );
				}
				
				//Agrego informacion adicional a los pacientes
				@$pacientes[ $rows['Kadhis'] ][ 'totalArticulos' ]++;
				@$pacientes[ $rows['Kadhis'] ][ 'filasAsociadas' ][] = $fila;
				
				//Consulto la informacion del medicamento si no se ha consultado con anterioridad
				if( empty( $articulos[ $rows['Kadart'] ] ) ){
					consultarInformacionProducto( $conex, $wcenmez, $wbasedato, $rows['Kadart'], $articulos );
				}
				
				//Agrego informacion adicional al articulo
				@$articulos[ $rows['Kadart'] ][ 'totalHistorias' ]++;
				@$articulos[ $rows['Kadart'] ][ 'hisAsociadas' ][] = $rows[ 'Kadhis' ];
				
				//Agrego informacion para la tabla
				
				//Consulto la regleta del dia anterior y la informacion necesaria de la media noche
				$regletaAnterior = consultarMediaNocheDiaAnterior( $conex, $wbasedato, $rows['Kadreg'] );
				$infoHora = consultarInformacionHora( $regletaAnterior, "00:00:00", $hora, $cdi, $dis );
				
				if( !empty( $infoHora ) ){
					$infoHora .= ',';
				}
				
				
				$detalleTabla[ $fila ][ 'historia' ] = $rows[ 'Kadhis' ];
				$detalleTabla[ $fila ][ 'articulo' ] = $rows[ 'Kadart' ];
				$detalleTabla[ $fila ][ 'horasAplicacion' ] = consultarHorasAdministracion( $infoHora.$rows['Kadcpx'] );
				$detalleTabla[ $fila ][ 'cantidadGrabada' ] = consultarHorasGrabadas( $infoHora.$rows['Kadcpx'] );
				
				
				$detalleTabla[ $fila ][ 'enviar' ] = ( $rows[ 'Kadess' ] == 'on' )? "Si" : "No";				
				$detalleTabla[ $fila ][ 'suspendido' ] = ( $rows[ 'Kadsus' ] == 'on' )? "Si" : "No";
				
				if( $rows[ 'Kadori' ] == 'SF' ){
					$detalleTabla[ $fila ][ 'confirmado' ] = "No requiere";
				}
				else{
					$detalleTabla[ $fila ][ 'confirmado' ] = ( $rows[ 'Kadcon' ] == 'on' )? "Si" : "No";
				}
				
				$detalleTabla[ $fila ][ 'aprobado' ] = ( $rows[ 'Kadare' ] == 'on' )? "Si" : "No";
				$detalleTabla[ $fila ][ 'saldoPiso' ] = consultarSaldosEnPiso( $conex, $wbasedato, $rows['Kadhis'], $rows['Kading'], $rows['Kadart'] )."";
				$detalleTabla[ $fila ][ 'kardexAutomatico' ] = ( $rows[ 'Karaut' ] != '' )? "Si" : "No";
				$detalleTabla[ $fila ][ 'saldoDispensacion' ] = consultarSaldoDispensacion( $rows['Kadcpx'] );
				$detalleTabla[ $fila ][ 'saldoGrabado' ] = consultarSaldoDispensacionGrabado( $rows['Kadcpx'] );
				
				
				
				//Calculando dosis pendientes por grabar
				$totalAplicaciones = cantidadTotalADispensarRonda( $infoHora.$rows['Kadcpx'], "22:00:00" );
				$dosisGrabadas = cantidadTotalDispensadaRonda( $infoHora.$rows['Kadcpx'], "22:00:00" );
				
				$detalleTabla[ $fila ][ 'dosisPorGrabar' ] = $totalAplicaciones-$dosisGrabadas;
				$dosisPendientesPorProducir = $detalleTabla[ $fila ][ 'dosisPorGrabar' ] - $articulos[ $rows['Kadart'] ][ 'Saldo' ];
				
				//Solo aplica para central de mezclas
				if( $rows[ 'Kadori' ] == "CM" ){
					$detalleTabla[ $fila ][ 'dosisPendientes' ] = ( $dosisPendientesPorProducir > 0 )? $dosisPendientesPorProducir: 0;	//Dosis pendientes por producir
				}
				else{
					$detalleTabla[ $fila ][ 'dosisPendientes' ] = "NA";	//Dosis pendientes por producir
				}
			}
			
			//Pinto los resultados encontrados
			pintarResultados( $detalleTabla, $pacientes, $articulos );
		}
		else{
			echo "<center style='font-size:20pt'><b>NO SE ENCONTRARON RESULTADOS</b></center>";
		}
		
		echo "<br><table align='center'>";
		
		echo "<tr><td>";
		echo "<center><INPUT type='button' value='Retornar' onclick='document.forms[0].submit();'></center>";
		echo "</td>";
		
		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></center>";
		echo "</td></tr>";
		
		echo "</table>";
	}

	echo "</form>";
}
   
  /****************************************************************************************************************************
   *												FIN DEL PROGRAMA
   ****************************************************************************************************************************/
?>
</body>