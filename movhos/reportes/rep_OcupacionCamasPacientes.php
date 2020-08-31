<head>	
<title>OCUPACION DE CAMAS</title>
</head>

<script type="text/javascript">
	function cerrarVentana()
	 {
      top.close();		  
     }
</script>

<body>

<?php
include_once("conex.php");

/*************************************************************************************************************************
 * 
 * Reporte Ocupacion de Camas por paciente
 * 
 * Objetivo: Generar un reporte donde muestre la ocupacion de camas
 *           entre dos fechas, por pacientes
 *           
 *  Actualizaciones:
 *
 * 2013-02-13. Se agregó la columna Edad en el resultado del reporte. También se quitó el rowspan de las filas con la misma historia,
 * de modo que si una historia estubo en varias habitaciones no se muestre una sola fila por historia y nombre sino que estos campos se 
 * muestren tantas filas como habitaciones haya tenido el paciente. Los dos puntos anteriores se hacen por solicitud de costos.
 * En la función tiempoOcupacion se modificó el cálculo de ocupación para las camas de modo que cumpla la siguiente regla:
 * Si en la tabla movhos_000067 se encuentra historia para la cama al final del día, se cuenta ese día como ocupado
 * Si en la tabla movhos_000067 NO se encuentra historia para la cama al final del día, se busca si el día anterior la cama tenia historia 
 * al final del día O si en la tabla movhos_000017 esa cama fue ocupada durante el día
 *
 * Julio 10 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos 
 * de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera funcion.
 *
 *  2012-05-30 - Se cambió  la función tiempoOcupacion para que haga el cálculo de ocupación ´con base en días de ocupación
 *				 y no en horas como estaba antes. Se adicionó una fila al final del reporte con los totales de cada columna
 *************************************************************************************************************************/

/*****************************************************************************
 *                                   FUNCIONES
 ****************************************************************************/

function detallePacientes( $pacientes, $diasTotales, $total_ocupacion, $camas ){
	
	global $conex;

	// Inicializo variables de totales
	$total_dias = 0;
	$total_porcentaje = 0;
	$contador = 0;
	
	if( count($pacientes) > 0 ){
		
		echo "<table align='center'>";
		
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td>Historia</td>";
		echo "<td>Nombre</td>";
		echo "<td>Edad</td>";
		echo "<td>Centro de<br>costos</td>";
		echo "<td>Habitaci&oacute;n</td>";
		echo "<td>D&iacute;as<br>ocupados</td>";
		echo "<td>% de<br>Ocupaci&oacute;n</td>";
		echo "</tr>";
		
		$i = 0;

		foreach( $pacientes as $keyPac => $valuePac ){
			
			$class = "class='fila".(($i%2)+1)."'";
			$style = "";
			
			list( $historia ) = explode( "-", $keyPac );
			//@$paciente = consultarInfoPacientePorHistoria( $conex, $historia );
			//@$nombre = $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;

			$contador_hab = 0;
			$total_porcentaje_hab = 0;
			
			foreach( $valuePac as $key => $value ){
			
				echo "<tr $class>";
				
				// Se comenta porque yano se va a hacer rowspan para agrupar historia y nombre paciente
				// echo "<td rowspan='".count($valuePac)."' style='$style'>$keyPac</td>";
				// echo "<td rowspan='".count($valuePac)."' style='$style'>$nombre</td>";

				echo "<td>".$keyPac."</td>";
				echo "<td>".$value['nombre']."</td>";
				echo "<td align='center' title='".$value['fnac']."'>".$value['edad']."</td>";
				echo "<td align='center'>{$value['cco']}</td>";
				echo "<td align='center'>{$value['habitacion']}</td>";
				echo "<td align='center'>".round( ($value['tiempo']), 2 )."</td>";
				echo "<td align='center'>".round( ($value['tiempo']/$diasTotales)*100, 2 )."</td>";
				
				$contador_hab++;
				$total_porcentaje_hab += ($value['tiempo']/$diasTotales)*100;
				
				echo "</tr>";
				$style = "display:none";
			}
			
			$total_porcentaje += $total_porcentaje_hab;
			$contador++;
			$i++;
		}
			
		echo "<tr class='encabezadoTabla'>";
		echo "<td align='left' colspan='5'><b> TOTAL </b></td>";
		echo "<td align='center'>".round( $total_ocupacion, 2 )."</td>";
		echo "<td align='center'>".round( ($total_ocupacion/$camas)*100, 2 )."</td>";
		echo "</tr>";

		echo "</table>";
	
	}
	else{
		echo "<center><b>No hay inforamci&oacute;n a mostrar</b></center>";
	}
}

/**
 * Calcula el tiempo transcurrido entre dos horas del mismo día.
 * 
 * @param $horaini
 * @param $horafin
 * @return unknown_type
 */
function calcularTiempo( $horaini, $horafin ){
	
	$time = 0;
	
	$exp = explode( ":", $horaini );
	
	$horini = $exp[0];
	$minini = $exp[1];
	$segini = $exp[2];
	
	$exp = explode( ":", $horafin );
	
	$horfin = $exp[0];
	$minfin = $exp[1];
	$segfin = $exp[2];
	
	$inicial = mktime( $horini, $minini, $segini );
	$final = mktime( $horfin, $minfin, $segfin );
	
	$time = $final - $inicial;

	return abs( $time );
	
}

/**
 * Busca los pacientes que ocuparon la cama durante el día y devuelve el array de estos
 * 
 * @param $cco
 * @param $cama
 * @param $fecha
 * @return unknown_type
 */
function pacientesDuranteDia( $cco, $cama, $fecha ){
	
	global $conex;
	global $wbasedato;
	global $tipoEmpresaReporte;
	
	$pacientes = array();
	
	//Se busca si hubo entrega de un paciente a otro cento de costos
	$sql = "SELECT
				*, a.Hora_data as Hora_data
			FROM
				{$wbasedato}_000017 a, {$wbasedato}_000016 b
			WHERE
				eyrtip = 'Recibo'
				AND eyrsde = '$cco'
				AND eyrhde = '$cama'
				AND a.fecha_data = '$fecha'
				AND inghis = eyrhis
				AND inging = eyring
				AND eyrest = 'on'
			ORDER BY a.id DESC
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	
		
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
		if( $tipoEmpresaReporte != 1 ){
			if( $rows['Ingtip'] == '02' || $rows['Ingtip'] == '' ){
				$rows['Ingres'] = '02';
			}
		}
		//echo $rows['Eyrhis']." - ";
		$pacientes[ $i ] = $rows;
	}
	
	return $pacientes;
	
}

/**
 * Devuelve la hora en que fue dada una cama al paciente
 * 
 * @param $cco			Centro de Costps
 * @param $cama			Cama
 * @param $fecha		Fecha en que fue dada la cama
 * @param $his			Historia
 * @param $ing			Ingreso
 * @return unknown_type
 */
function horaEntrada( $cco, $cama, $fecha, $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$hora = '23:59:59';
	
	//Se busca si hubo entrega de un paciente a otro cento de costos
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000017
			WHERE
				eyrtip = 'Recibo'
				AND fecha_data = '$fecha'
				AND eyrsde = '$cco'
				AND eyrhde = '$cama'
				AND eyrhis = '$his'
				AND eyring = '$ing'
				AND eyrest = 'on'
			";// echo "<pre>......$sql</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$hora = $rows['Hora_data'];
	}
	
	return $hora;
}

/**
 * Busca la hora en que a un paciente se entrega a otra habitación o se le dio de alta
 * 
 * @param $cco
 * @param $cama
 * @param $fecha
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function horaSalida( $cco, $cama, $fecha, $his, $ing ){

	global $conex;
	global $wbasedato;
	
	$hora = '00:00:00';
	
	//Se busca si hubo entrega de un paciente a otro cento de costos
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000017
			WHERE
				eyrtip = 'Entrega'
				AND eyrsor = '$cco'
				AND eyrhor = '$cama'
				AND eyrhis = '$his'
				AND eyring = '$ing'
				AND fecha_data = '$fecha'
				AND eyrest = 'on'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		if( $rows = mysql_fetch_array( $res ) ){
			$hora = $rows['Hora_data'];
		}
	}
	else{
		
		//Se busca si hubo alta definitiva para la cama durante el día
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000018
				WHERE
					ubihis = '$his'
					AND ubiing = '$ing'
					AND ubihac = '$cama'
					AND ubisac = '$cco'
					AND ubiald = 'on'
					AND ubifad = '$fecha'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$hora = $rows['Ubihad'];
		}
		
	}
	
	return $hora;

	global $conex;
	global $wbasedato;
	
	$hora = '00:00:00';
	
	//Se busca si hubo alta definitiva para la cama durante el día
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000018
			WHERE
				ubihis = '$his'
				AND ubiing = '$ing'
				AND ubihac = '$cama'
				AND ubisac = '$cco'
				AND ubiald = 'on'
				AND ubifad = '$fecha'
			";
	
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		if( $rows = mysql_fetch_array( $res ) ){
			$hora = $rows['Ubihad'];
		}
	}
	else{
		
		//Se busca si hubo entrega de un paciente a otro cento de costos
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000017
				WHERE
					eyrtip = 'Entrega'
					AND eyrsor = '$cco'
					AND eyrhor = '$cama'
					AND eyrhis = '$his'
					AND eyring = '$ing'
					AND fecha_data = '$fecha'
					AND eyrest = 'on'
				"; 
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$hora = $rows['Hora_data'];
		}
		
	}
	
	return $hora;
	
}

// Función que retorna la edad con base en la fecha de nacimiento
function calcularEdad($fechaNacimiento) {

	$ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($aa - $ann)/360;
	$meses=(($aa - $ann) % 360)/30;
	if ($ann1<1){
		$dias1=(($aa - $ann) % 360) % 30;
		// $wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		$wedad=(string)(integer)$meses." mes(es) ";
	} else {
		$dias1=(($aa - $ann) % 360) % 30;
		//$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		$wedad=(string)(integer)$ann1." a&ntilde;o(s) ";
	}

	return $wedad;
}		

// Función que con base en la historia y la emmpresa del paciente, retorna el nombre, la edad y fecha de nacimiento de éste
function datosPaciente($historia, &$nombre, &$edad, &$fecha_nacimiento) {
	
	global $conex;
	global $wbasedato;
	global $wemp_pmla;

	// Busco nombres y fecha de nacimiento del paciente
	$query = " SELECT Pacnac, Pacno1, Pacno2, Pacap1, Pacap2
				 FROM root_000037, root_000036
				WHERE Orihis = '".$historia."'
				  AND Oriced = Pacced
				  AND Oriori = '".$wemp_pmla."'
				  AND Oritid = Pactid
			";
	$respac = mysql_query( $query, $conex ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
	$numrowspac = mysql_num_rows( $respac );
	if($numrowspac>0)
	{
		$rowpac = mysql_fetch_array($respac);
		$edad = calcularEdad($rowpac['Pacnac']);
		$fecha_nacimiento = $rowpac['Pacnac'];
		$nombre = $rowpac['Pacno1']." ".$rowpac['Pacno2']." ".$rowpac['Pacap1']." ".$rowpac['Pacap2'];				
	}
}
		
/**
 * Calcula el tiempo de ocupación por cama durante el rango de fechas dado
 * 
 * @param $cco				Centro de costos
 * @param $cama				Cama
 * @param $fechaini			Fecha inicial
 * @param $fechafin			Fecha final
 * @return unknown_type
 */
function tiempoOcupacion( $cco, $cama, $fechaini, $fechafin ){

	global $conex;
	global $wbasedato;
	global $tipoEmpresa;
	global $detalleCamas;
	global $tipoEmpresaReporte;
	global $wemp_pmla;
	
	global $ocupantes;
	
	//Definición de variables
	
	$hisAnterior = '';				//Indica la historia del paciente del día anterior
	$ingAnterior = '';				//Indica el ingreso del paciente del día anterior
	$ocupacionDia = 0;				//Tiempo de ocupación de una cama durante el día (en horas)
	$tiempoOcupacion = 0;			//Tiempo de ocupación de una cama durante el rango de fechas (en horas)
	
	//Calculo la fecha de día anterior de la inicial
	$exp = explode( "-", $fechaini );
	$fechaini = date( "Y-m-d", mktime( 0, 0, 0, $exp[1], $exp[2]-1, $exp[0] ) );
	
	if( $tipoEmpresaReporte == 1 )
	{
		$tipEmpRep = "Ingtip";
	}

	//Buscando los pacientes que se encuentran en cama al final del día
	$sql = "SELECT  
				*, b.Fecha_data as Fecha_data
			FROM 
				{$wbasedato}_000067 b LEFT OUTER JOIN {$wbasedato}_000016 a
				ON  habhis = inghis AND habing = inging  
			WHERE 
				b.habcco = '$cco'
				AND b.habcod = '$cama'
				AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
				AND b.habest = 'on'
			ORDER BY b.Fecha_data
			";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );

	// Si hay registros de ocupación
	if( $numrows > 0 )
	{
		$rows = mysql_fetch_array( $res );
		
		$hisAnterior = $rows[ 'Habhis' ];
		$ingAnterior = $rows[ 'Habing' ];

		// Asignación de variables $edad y $fecha_nacimiento
		$edad = "";
		$fecha_nacimiento = "";
		$nombre = "";

		// Obtengo los datos del paciente
		datosPaciente($rows['Habhis'], $nombre, $edad, $fecha_nacimiento);
		
		//Creando array por cama y tipo de empresa
		foreach( $tipoEmpresa as $emp => $tip ){
			$detalleCamas[ $rows['Habcod'] ][ $emp ] = 0;
		}
		
		//Creando array por cama y tipo de empresa
		for( $i = 0; $rows = mysql_fetch_array( $res ) ; $i++ )
		{
			// Se inicializan variables
			$ocupacionDia = 0;
			$edad = "";
			$fecha_nacimiento = "";
			$nombre = "";
			
			// Si hay paciente en la habitación al final del día
			if( trim($rows['Habhis']) != '' && trim($rows['Habhis']) != 'NO APLICA' )
			{
				// Obtengo los datos del paciente
				datosPaciente($rows['Habhis'], $nombre, $edad, $fecha_nacimiento);

				// Resgistro ocupación para el día actual
				$ocupacionDia += 1;
				
				@$tipoEmpresa[ $rows[ $tipEmpRep ] ] += 1;
				@$detalleCamas[ $rows['Habcod'] ][ $rows[ $tipEmpRep ] ] += 1;

				// Asigno valores al array
				@$ocupantes[ $rows[ 'Habhis' ]."-".$rows[ 'Habing' ] ][ $rows['Habcod'] ][ 'habitacion' ] = $rows['Habcod'];
				@$ocupantes[ $rows[ 'Habhis' ]."-".$rows[ 'Habing' ] ][ $rows['Habcod'] ][ 'tiempo' ] += 1;
				@$ocupantes[ $rows[ 'Habhis' ]."-".$rows[ 'Habing' ] ][ $rows['Habcod'] ][ 'cco' ] = $rows['Habcco'];
				@$ocupantes[ $rows[ 'Habhis' ]."-".$rows[ 'Habing' ] ][ $rows['Habcod'] ][ 'edad' ] = $edad;
				@$ocupantes[ $rows[ 'Habhis' ]."-".$rows[ 'Habing' ] ][ $rows['Habcod'] ][ 'fnac' ] = $fecha_nacimiento;
				@$ocupantes[ $rows[ 'Habhis' ]."-".$rows[ 'Habing' ] ][ $rows['Habcod'] ][ 'nombre' ] = $nombre;
					
			}
			else
			{
				//Busco los pacientes en la cama durante el día y que no sean los mismo del día anterior
				$pacientes = pacientesDuranteDia( $cco, $cama, $rows[ 'Fecha_data' ] );

				if( ( count( $pacientes ) > 0 ) || ($hisAnterior != '' && $ingAnterior != '') )
				{
					// Resgistro ocupación para el día actual
					$ocupacionDia += 1;
					
					if( count( $pacientes ) > 0 )
					{
						foreach( $pacientes as $pac )
						{
							if( trim($pac['Eyrhis']) != '' && trim($pac['Eyrhis']) != 'NO APLICA' )
							{
								// Obtengo los datos del paciente
								datosPaciente($pac['Eyrhis'], $nombre, $edad, $fecha_nacimiento);
				
								@$tipoEmpresa[ $pac[ $tipEmpRep ] ] += 1;
								@$detalleCamas[ $rows['Habcod'] ][ $pac[ $tipEmpRep ] ] += 1;

								// Asigno valores al array
								@$ocupantes[ $pac['Eyrhis']."-".$pac['Eyring'] ][ $rows['Habcod'] ][ 'tiempo' ] += 1;
								@$ocupantes[ $pac['Eyrhis']."-".$pac['Eyring'] ][ $rows['Habcod'] ][ 'habitacion' ] = $rows['Habcod'];
								@$ocupantes[ $pac['Eyrhis']."-".$pac['Eyring'] ][ $rows['Habcod'] ][ 'cco' ] = $rows['Habcco'];
								@$ocupantes[ $pac['Eyrhis']."-".$pac['Eyring'] ][ $rows['Habcod'] ][ 'edad' ] = $edad;
								@$ocupantes[ $pac['Eyrhis']."-".$pac['Eyring'] ][ $rows['Habcod'] ][ 'fnac' ] = $fecha_nacimiento;
								@$ocupantes[ $pac['Eyrhis']."-".$pac['Eyring'] ][ $rows['Habcod'] ][ 'nombre' ] = $nombre;
								break;
							}
						}
					}
					elseif($hisAnterior != '' && $ingAnterior != '')
					{
						if( trim($hisAnterior) != '' && trim($hisAnterior) != 'NO APLICA' )
						{
							// Obtengo los datos del paciente
							datosPaciente($hisAnterior, $nombre, $edad, $fecha_nacimiento);
							
							@$tipoEmpresa[ $rows[ $tipEmpRep ] ] += 1;
							@$detalleCamas[ $rows['Habcod'] ][ $rows[ $tipEmpRep ] ] += 1;

							// Asigno valores al array
							@$ocupantes[ $hisAnterior."-".$ingAnterior ][ $rows['Habcod'] ][ 'tiempo' ] += 1;
							@$ocupantes[ $hisAnterior."-".$ingAnterior ][ $rows['Habcod'] ][ 'habitacion' ] = $rows['Habcod'];
							@$ocupantes[ $hisAnterior."-".$ingAnterior ][ $rows['Habcod'] ][ 'cco' ] = $rows['Habcco'];
							@$ocupantes[ $hisAnterior."-".$ingAnterior ][ $rows['Habcod'] ][ 'edad' ] = $edad;
							@$ocupantes[ $hisAnterior."-".$ingAnterior ][ $rows['Habcod'] ][ 'fnac' ] = $fecha_nacimiento;
							@$ocupantes[ $hisAnterior."-".$ingAnterior ][ $rows['Habcod'] ][ 'nombre' ] = $nombre;
						}
					}
				}
			}
				
			$tiempoOcupacion += $ocupacionDia;
			
			$hisAnterior = $rows[ 'Habhis' ];
			$ingAnterior = $rows[ 'Habing' ];
		}
		
	}
	
	return $tiempoOcupacion;
			
}

/**
 * Calcula los dias transcurridos entre dos fechas, en caso de que alguna de las fechas
 * ingresadas como parametro sea falsa, la funcion retornará 0
 * 
 * @param date $fecini
 * @param date $fecfin
 * @return integer
 * 
 */

function calcularDias( $fecini, $fecfin){

	$ini = strtotime( $fecini );
	$fin = strtotime( $fecfin );
	
	if( $ini == -1 || $fin ==  -1 ){
		return 0;
	}
	else{
		$dif = $fin - $ini;
		return ( $dif/(24*3600) + 1 );
	}
}


/*********************************************************************************
 * AQUI COMIENZA EL PROGRAMA
 ********************************************************************************/

include_once("root/comun.php");

if(!isset($_SESSION['user']))
	exit("error");
//else{
	
$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

encabezado("OCUPACION DE CAMAS POR PACIENTE", "Febrero 13 de 2013" ,"clinica");

if( !isset($mostrar) ){
	$mostrar = 'off';
}

//if( !isset( $fechafin ) || !isset( $fechaini ) )
if( $mostrar == 'off' )
{
	
	if( !isset( $fechafin ) ){
		$fechafin = date("Y-m-d");
	}
	
	if( !isset( $fechaini ) ){
		$fechaini = date("Y-m-01");
	}
	
	//Eleccion de fecha
	echo "<form action='rep_OcupacionCamasPacientes.php?wemp_pmla=01' method='post'>";
	
	//Buscando los centros de costos
	
	 //**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
	 	$cco="ccohos = 'on' AND ccoest = 'on' AND ccoing <> 'on'";  // filtros para la consulta
		$sub="off";
		$tod="Todos";
		$ipod="off";  
		//$cco="Todos";
		$filtro="--";
		$centrosCostos = consultaCentrosCostos($cco, $filtro);  
		 
		
		echo "<table align='center' border=0>";		
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "cco");
					
		echo $dib;
		echo "</table>";
	
	
	echo "<br><br><table align='center'>
		<tr>
			<td align='center' style='width:200' class='fila1'>Fecha inicial</td>
			<td align='center' style='width:200' class='fila1'>Fecha final</td>
		</tr><tr>
			<td align='center' class='fila2'>";
	campoFechaDefecto( "fechaini", $fechaini );
	echo "</td>
			<td align='center' class='fila2'>";
	campoFechaDefecto( "fechafin", $fechafin );
	echo "</td>
		</tr>
	</table>";
	
	echo "<br><br>";
	echo "<table align='center'>";
	
	echo "<br><br>";
	
	
	//Botones ver y cerrar
	echo "<br><table align='center'>
		<tr>
			<td align='center' width='150'><INPUT type='submit' value='Ver' style='width:100' name='btVer'></INPUT></td>
			<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></INPUT></td>
		</tr>
	</table>";
	
	echo "<INPUT type='hidden' name='mostrar' value='on'>";
	
	echo "</form>";
	
	echo "<br><p align='center'><b>Nota:</b> La fecha inicial no debe ser menor al 7 de julio de 2009,
	<br>si la fecha es menor, afectará los calculos.</p>";
}
elseif( true ){
	
	$tipoEmpresaReporte = 1;
	
	//creando tabla temporal
	
	$tipoEmpresa = Array();
	$detalleCamas = Array();
	
	//Creando Array con los tipos de datos
	if( $tipoEmpresaReporte == '1' ){
		
		$sql = "SELECT
					ingtip
				FROM
					{$wbasedato}_000016
				GROUP BY
					ingtip";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

			$tipoEmpresa[ $rows['ingtip'] ] = 0;
		}
	}
	
	echo "<form action='rep_OcupacionCamasPacientes.php?wemp_pmla=01' method='post'>";
	
	//encabezado del informe
	echo "<br><table align='center'>
		<tr align='left'>
			<td width='150' class='fila1'>Fecha inicial</td>
			<td width='150' class='fila2'>$fechaini</td>
		</tr>
		<tr class='fila1' align='left'>
			<td class='fila1'>Fecha final</td>
			<td class='fila2'>$fechafin</td>
		</tr>
		<tr>
			<td class='fila1'>Centro de costos:</td>
			<td class='fila2'>$cco</td>
		</tr>
	</table><br><br>";
	
	$explcco = explode("-", $cco);
	$cco = $explcco[0];
	
	//Buscando las camas a mostrar, con su respectivo centro de costos
	$sql = "SELECT 
				habcod as cama, habcco as cco, cconom as nom
			FROM
				{$wbasedato}_000067 a, {$wbasedato}_000011 b
			WHERE 
				habcco like '$cco'
				AND habcco = ccocod
				AND ccohos = 'on'
				AND ccoing <> 'on'
				AND ccoest = 'on'
			GROUP BY 2, 1";

	$res = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	
	$dias = calcularDias( $fechaini, $fechafin );
	
	// Calcula el número de camas disponibles en el periodo de fechas y servicio indicado
		if($cco!="%")
		{
			$qcam= "
				SELECT 
					habcco, COUNT(habcod) camas
				FROM
					{$wbasedato}_000011 c, {$wbasedato}_000067  b LEFT OUTER JOIN {$wbasedato}_000016 a ON habhis = inghis AND habing = inging
				WHERE 
					habcco like '$cco'
					AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
					AND habest = 'on'
					AND habcco = ccocod
					AND ccohos = 'on'
					AND ccoing <> 'on'
					AND ccoest = 'on'
				  GROUP BY 1 ";
		}
		else
		{
			$qcam= "
				SELECT 
					COUNT(habcod) camas
				FROM
					{$wbasedato}_000011 c, {$wbasedato}_000067  b LEFT OUTER JOIN {$wbasedato}_000016 a ON habhis = inghis AND habing = inging
				WHERE 
					b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
					AND habest = 'on'
					AND habcco = ccocod
					AND ccohos = 'on'
					AND ccoing <> 'on'
					AND ccoest = 'on' ";
		}

	$res_cam = mysql_query( $qcam, $conex );
	$row_cam = mysql_fetch_array($res_cam);
	$camas = $row_cam['camas'];

	$hasRow = false; 
	
	$rows = mysql_fetch_array($res);
	
	if( true ){
		$total_ocupacion = 0;
		for(; $rows;){
			$ocupacion = tiempoOcupacion( $rows['cco'], $rows['cama'], $fechaini, $fechafin );
			$total_ocupacion += $ocupacion;
			$rows = mysql_fetch_array($res);
		}
		
		
		detallePacientes( $ocupantes, $dias, $total_ocupacion, $camas );
		echo "<br>";
	}
	
	echo "<INPUT type='hidden' name='fechaini' value='$fechaini'>";
	echo "<INPUT type='hidden' name='fechafin' value='$fechafin'>";
	
	echo "<table align='center'>
		<tr>
			<td width='150'>
				<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
			</td>
			<td width='150'>
				<INPUT type='submit' value='Retornar' style='width:100'>
			</td>
			<td></td>
		</tr>";
	
	echo "<INPUT type='hidden' name='mostrar' value='off'>";
	
	echo "</form>";
}
?>
</body>
