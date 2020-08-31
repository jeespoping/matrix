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
 * Realizado por: Edwin Molina Grisales
 *
 * Reporte Porcentaje Ocupacion de Camas
 *
 * Objetivo: Generar un reporte donde muestre el porcentaje de ocupacion de camas
 *           entre dos fechas, centro de costos hospitalario con sus respectivas camas
 *
 *  Actualizaciones:
 *
 * 2013-02-13. En la función tiempoOcupacion se modificó el cálculo de ocupación para las camas de modo que cumpla la siguiente regla:
 * Si en la tabla movhos_000067 se encuentra historia para la cama al final del día, se cuenta ese día como ocupado
 * Si en la tabla movhos_000067 NO se encuentra historia para la cama al final del día, se busca si el día anterior la cama tenia historia
 * al final del día O si en la tabla movhos_000017 esa cama fue ocupada durante el día
 *
 *  2012-07-23 - Se le hace trim al cco a la hora de consultar ya que a veces trae un espacio en el codigo del cco
 *
 *  2012-07-06 - (Viviana Rodas) Se agregaron las funciones consultaCentrosCostos y dibujarSelect que listan los centros de costos en
 *                orden alfabetico, de un grupo seleccionado y dibujarSelect que construye el select de dichos centros de costos.
 *
 *  2012-05-30 - Se cambió  la función tiempoOcupacion para que haga el cálculo de ocupación ´con base en días de ocupación
 *				 y no en horas como estaba antes. Se adicionó una fila al final del reporte con los totales de cada columna
 *
 *  2011-05-16	Se agrega opcion nueva para el reporte.  Se agrega opcion para generar el reporte por empresa, hasta
 *  			ahora solo se podía por tipo de empresa.
 *
 *  2010-05-01	Se corrige query, se cambia la tabla 000020 por 000067, linea 814
 *
 *  2010-04-12	Por: Edwin Molina Grisales.
 *  			Se agrega la opción de generar el reporte detallado de camas por empresa y el porcentaje de ocupación
 *  			se calcula día a día.
 *
 *************************************************************************************************************************/

/*****************************************************************************
 *                                   FUNCIONES
 ****************************************************************************/

/**
 * Crea un arrray con key igual al codigo de la empresa y y valor igual a la descripcion del campo
 *
 * @param $emp
 * @return unknown_type
 */
function maestroEmpresas( &$emp ){

	global $wbasedato;
	global $conex;
	global $tipoEmpresaReporte;

	conexionOdbc($conex, $wbasedato, $conex_o, 'facturacion' );

	$emp = Array();
	$emp[''] = 'PARTICULARES';

	if( $tipoEmpresaReporte == 1 ){

		$sql = "SELECT
					temcod, temdes
				FROM
					intem
				";

		$res = odbc_do( $conex_o, $sql ) or die( odbc_error()." - Error en el query $sql - ".odbc_errormsg() );

		for( $i = 0; $rows = odbc_fetch_row( $res ); $i++ ){

			$emp[ odbc_result( $res, 'temcod' ) ] = odbc_result( $res, 'temdes' );
		}
	}
	else{

		$sql = "SELECT
					Ingres as temcod, Ingnre as temdes, Ingtip
				FROM
					{$wbasedato}_000016
				WHERE
					Ingtip != '02'
					AND Ingtip != ''
				GROUP BY
					Ingres
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		$emp['02'] = 'PARTICULARES';

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			if( $rows[ 'Ingtip' ] != "02" && $rows[ 'Ingtip' ] != "" ){
				@$emp[ $rows[ 'temcod' ] ] = $rows[  'temdes' ];
			}
		}
	}
}

/**
 * Genera un reporte detallado por camas y muestra el porcentaje de ocupación de esta cama por empresa
 *
 * @param $dias
 * @return unknown_type
 */
function detallePorEmpresa( $dias, $camas, $cco, $fechaini, $fechafin ){

	global $wbasedato;
	global $conex;
	global $detalleCamas;
	global $tipoEmpresaReporte;

	$i = 0;
	$class = '';
	$hasDatos = false;

	maestroEmpresas( $empresas );

	if( $tipoEmpresaReporte != 1 ){
		echo "<center><b>REPORTE DETALLADO POR HABITACION Y EMPRESAS</b></center>";
	}
	else{
		echo "<center><b>REPORTE DETALLADO POR HABITACION Y TIPO DE EMPRESAS</b></center>";
	}
	echo "<br><br>";

	$total_ocupacion = 0;
	$suma_dias = 0;

	$arr_hab_dia = array();

		$q = "   SELECT ing.Historia_clinica, ing.Num_ingreso, ing.Fecha_data as fecha_ingreso
			   FROM ".$wbasedato."_000032 ing, ".$wbasedato."_000033 egr
			  WHERE	ing.Fecha_data = egr.Fecha_data
				AND egr.Historia_clinica = ing.Historia_clinica
				AND ing.Servicio = egr.Servicio
				AND ing.Fecha_data BETWEEN '".$fechaini."' AND '".$fechafin."'
				AND ing.Servicio LIKE '%".$cco."%' ";
		$res1 = mysql_query($q,$conex);
		// echo $q;

		while($row_his_ing = mysql_fetch_array($res1))
		{

			$whab_ocupada = consul_hab_ocupada($row_his_ing['Historia_clinica'],$row_his_ing['Num_ingreso'], $cco, $row_his_ing['fecha_ingreso'] );

			 //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($whab_ocupada, $arr_hab_dia))
			{
				$arr_hab_dia[$whab_ocupada] = array();
			}

			//Aqui se forma el arreglo
			$arr_hab_dia[$whab_ocupada][] = $row_his_ing['Historia_clinica'];

		}
		// echo "<pre>" . print_r($arr_hab_dia, true) . "</pre>";

	foreach( $detalleCamas as $cama => $tipemp ){

		$total = 0;

		$j = 0;

		foreach( $tipemp as $emp => $tiempo ){

			if( $tiempo != 0 ){


				$codcama = trim($cama);
				//Si la habitacion tuvo ingreso y egresos el mismo dia sumara uno o mas a la ocupacion
			   //(El arreglo $arr_hab_dia viene de la consulta a las tablas 17, 32, 33, mas arriba de este comentario).
			   if(array_key_exists($codcama, $arr_hab_dia) and $cco != '1179')
				{
					//echo "<pre>" . print_r($empresas, true) . "</pre>";
					$tiempo += count($arr_hab_dia[$codcama]);
					unset($arr_hab_dia[$codcama]);
				}

				if( $j == 0 ){

					echo "<table align='center'>";

					echo "<tr class='encabezadotabla' align='center'>";
					echo "<td>Cama</td>";
					echo "<td>Total de días</td>";
					echo "<td width='250'>Empresa</td>";
					echo "<td>Días Ocupados</td>";
					echo "<td>% de Ocupación</td>";
					echo "</tr>";

				}

				$total += $tiempo;

				$class = "class='fila".($i%2+1)."'";

				echo "<tr $class>";

				if( $j == 0 ){
					echo "<td bgcolor='#FFFFFF' align='center'><b>$cama</b></td>";
				}
				else{
					echo "<td  bgcolor='#FFFFFF'></td>";
				}

				echo "<td>$dias</td>";
				echo "<td width=100>{$empresas[ $emp ]}</td>";
//				echo "<td width=100>$emp</td>";
				echo "<td width=100 align='center'>".number_format( ($tiempo), 2, '.',',')."</td>";
				echo "<td width=100 align='center'>".number_format( (($tiempo)/$dias)*100, 2, '.',',')."</td>";
				echo "</tr>";

				$i++;
				$j++;
			}

		}

		// echo "<pre>" . print_r($tipemp, true) . "</pre>";

		if( $total > 0 ){

			$hasDatos = true;

			echo "<tr class='encabezadotabla'>";
			echo "<td>Subtotal</td>";
			echo "<td>$dias</td>";
			echo "<td></td>";
			echo "<td align='center'>".number_format($total,2,'.',',')."</td>";
			echo "<td align='center'>".number_format($total/$dias*100,2,'.',',')."</td>";
			echo "</tr>";

			$total_ocupacion += $total;
			$suma_dias += $dias;

		}

	}

	// echo "<pre>" . print_r($detalleCamas, true) . "</pre>";

	echo "<tr><td colspan='5'>&nbsp;</td></tr>";

	if($suma_dias==0)
		$suma_dias = 1;

	if(isset($total))
	{
		echo "<tr class='encabezadotabla'>";
		echo "<td><b>TOTAL</b></td>";
		echo "<td><b>$dias</b></td>";
		echo "<td></td>";
		echo "<td align='center'><b>".number_format($total_ocupacion,2,'.',',')."</b></td>";
		echo "<td align='center'><b>".number_format($total_ocupacion/$camas*100,2,'.',',')."</b></td>";
		echo "</tr>";
	}

	echo "</table>";

	echo "<br><br>";

	if( !$hasDatos ){
		echo "<center><b>NO HUBO RESULTADOS PARA LA CONSULTA DADA</b></center>";
		echo "<br><br>";
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

	//Definición de variables

	$hisAnterior = '';				//Indica la historia del paciente del día anterior
	$ingAnterior = '';				//Indica el ingreso del paciente del día anterior
	$ocupacionDia = 0;				//Tiempo de ocupación de una cama durante el día (en horas)
	$tiempoOcupacion = 0;			//Tiempo de ocupación de una cama durante el rango de fechas (en horas)

	//Calculo la fecha de día anterior de la inicial
	$exp = explode( "-", $fechaini );
	$fechaini = date( "Y-m-d", mktime( 0, 0, 0, $exp[1], $exp[2]-1, $exp[0] ) );


	//Buscando los pacientes que se encuentran en cama al final del día
	if( $tipoEmpresaReporte == 1 )
	{
		$tipEmpRep = "Ingtip";
	}
	else
	{
		$tipEmpRep = "Ingres";
	}
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
				AND b.habhis != 'NO APLICA'
			ORDER BY b.Fecha_data";
	// echo $sql."<br>";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );

	// Si hay registros de ocupación
	if( $numrows > 0 )
	{
		$rows = mysql_fetch_array( $res );

		if( $tipoEmpresaReporte != 1 ){

			if( @$rows['Ingtip'] == '02' || @$rows['Ingtip'] == '' ){
				@$rows['Ingres'] = '02';
			}
		}

		$hisAnterior = $rows[ 'Habhis' ];
		$ingAnterior = $rows[ 'Habing' ];

		//Creando array por cama y tipo de empresa
		foreach( $tipoEmpresa as $emp => $tip ){
			$detalleCamas[ $rows['Habcod'] ][ $emp ] = 0;
		}

		//Creando array por cama y tipo de empresa
		for( $i = 0; $rows = mysql_fetch_array( $res ) ; $i++ )
		{
			if( $tipoEmpresaReporte != 1 ){

				if( @$rows['Ingtip'] == '02' || @$rows['Ingtip'] == '' ){
					@$rows['Ingres'] = '02';
				}
			}

			$ocupacionDia = 0;

			// Si hay paciente en la habitación al final del día
			if( trim($rows['Habhis']) != '' && trim($rows['Habhis']) != 'NO APLICA' )
			{
				// Resgistro ocupación para el día actual
				//$ocupacionDia += 1;

				@$tipoEmpresa[ $rows[ $tipEmpRep ] ] += 1;
				@$detalleCamas[ $rows['Habcod'] ][ $rows[ $tipEmpRep ] ] += 1;
				$ocupacionDiaYo += 1;
				if($rows['Fecha_data'] != $fechaini)
					{
					// Registro ocupación para el día actual
					$ocupacionDia += 1;
					}
			}
			else
			{
				//Busco los pacientes en la cama durante el día y que no sean los mismo del día anterior
				$pacientes = pacientesDuranteDia( $cco, $cama, $rows[ 'Fecha_data' ] );

				/*if( ( count( $pacientes ) > 0 ) || ($hisAnterior != '' && $ingAnterior != '') )
				{
					// Resgistro ocupación para el día actual
					$ocupacionDia += 1;

					if( count( $pacientes ) > 0 ){
						foreach( $pacientes as $pac ){
							if( trim($pac['Eyrhis']) != '' && trim($pac['Eyrhis']) != 'NO APLICA' )
							{
								//$ocupacionDia += 1;
								$tipoEmpresa[ $pac[ $tipEmpRep ] ] += 1;
								$detalleCamas[ $rows['Habcod'] ][ $pac[ $tipEmpRep ] ] += 1;
								break;
							}
						}
					}
					elseif($hisAnterior != '' && $ingAnterior != '')
					{
						if( trim($hisAnterior) != '' && trim($hisAnterior) != 'NO APLICA' )
						{
							@$tipoEmpresa[ $rows[ $tipEmpRep ] ] += 1;
							@$detalleCamas[ $rows['Habcod'] ][ $rows[ $tipEmpRep ] ] += 1;
						}
					}
				}*/
				if($rows['Fecha_data'] == $fechaini)
					{

					$wcamaocupada = cama_ocup_ult_dia($rows['Habcod'], $rows['Habcco'], $fechaini);

					if($wcamaocupada > 0)
						{

						$ocupacionDia += 1;

						}
					}
			}

			$tiempoOcupacion += $ocupacionDia;

			$hisAnterior = $rows[ 'Habhis' ];
			$ingAnterior = $rows[ 'Habing' ];
		}

	}
	if( $ocupacionDiaYo != $tiempoOcupacion ) echo "HAY DIFERENCIA $ocupacionDiaYo : $tiempoOcupacion";;
	return $tiempoOcupacion;

}

//Consulta la habitacion que ocupo y desocupo en un mismo dia.
function consul_hab_ocupada($whis,$wing, $wcco, $wfecha_ingreso)
	{

	global $conex;
	global $wbasedato;

	$sql = "SELECT Eyrhor
			  FROM ".$wbasedato."_000017
		     WHERE Eyrhis = '".$whis."'
			   AND Eyring = '".$wing."'
			   AND Fecha_data = '".$wfecha_ingreso."'
			   AND Eyrsde LIKE '%".$wcco."%'
			   AND Eyrtip = 'Recibo'
			   AND Eyrest = 'on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$row = mysql_fetch_array($res);

	//echo $sql."<br>";
	return $row['Eyrhor'];

	}

function cama_ocup_ult_dia($whabcod, $wcco, $fechaini)
	{

	global $conex;
	global $wbasedato;

    $sql = "SELECT id
			  FROM ".$wbasedato."_000067
		     WHERE Habcod = '".$whabcod."'
			   AND Fecha_data = '".$fechaini."'
			   AND Habhis != ''
			   AND Habcco = '".$wcco."'
			   AND Habest = 'on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$num = mysql_num_rows($res);

	echo $sql."<br>";

	return $num;

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

	if( $ini == -1 || $fin ==  -1 )
	{
		return 0;
	}
	else
	{
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

$wactualiz = " Febrero 13 de 2013 ";

encabezado("OCUPACION DE CAMAS", $wactualiz ,"clinica");

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
	echo "<form action='repOcupacionCamas.php?wemp_pmla=01' method='post'>";


	//Seleccionar CENTRO DE COSTOS
	  //*********************llamado a las funciones que listan los centros de costos y la que dibuja el select************************
		$cco="Ccohos";
		$sub="off";
		$tod="Todos";
		//$cco=" ";
		$ipod="off";
		$centrosCostos = consultaCentrosCostos($cco);
		echo "<center><table align='center' border=0 >";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "cco");

		echo $dib;
		echo "</table></center>";


	echo "<br><br><table align='center'>
		<tr class='encabezadotabla'>
			<td align='center' style='width:200'>Fecha inicial</td>
			<td align='center' style='width:200'>Fecha final</td>
		</tr><tr class='fila1'>
			<td align='center'>";
	campoFechaDefecto( "fechaini", $fechaini );
	echo "</td>
			<td align='center'>";
	campoFechaDefecto( "fechafin", $fechafin );
	echo "</td>
		</tr>
	</table>";

	echo "<br><br>";
	echo "<table align='center'>";

	echo "<tr>";
	echo "<td colspan='2'>";
	echo "<INPUT type='radio' name='tipoEmpresaReporte' value='1' checked>Por Tipo de empresa ";
	echo "</td>";
	echo "<td colspan='1'>";
	echo "<INPUT type='radio' name='tipoEmpresaReporte' value='2'>Por empresa ";
	echo "</td>";
	echo "</tr>";

	echo "<tr><td colpsn='3'><br></td><tr>";

	echo "<tr><td width='120'>";
	echo "<INPUT type='radio' name='tipoReporte' value='1' checked>Resumido ";
	echo "</td>";
	echo "<td width='120'>";
	echo "<INPUT type='radio' name='tipoReporte' value ='2'>Detallado";
	echo "</td>";
	echo "<td width='200'>";
	echo "<INPUT type='radio' name='tipoReporte' value='3'>Resumido y detallado ";
	echo "</tr></td>";
	echo "</table>";
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
else{



	$tipoEmpresa = array();
	$detalleCamas = array();

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
	else{

		$sql = "SELECT
					ingres, ingtip
				FROM
					{$wbasedato}_000016
				WHERE
					ingtip != ''
					AND ingtip != '02'
				GROUP BY
					ingres";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		$tipoEmpresa[ '02' ] = 0;
		$tipoEmpresa[ '' ] = 0;

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

			if( $tipoEmpresaReporte != 1 ){

				if( $rows[ 'ingtip' ] == '02' || $rows[ 'ingtip' ] == '' ){
					$rows['ingres'] = '02';
				}
			}

			$tipoEmpresa[ $rows['ingres'] ] = 0;
		}
	}



	echo "<form action='repOcupacionCamas.php?wemp_pmla=01' method='post'>";

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
	$cco = trim( $explcco[0] );	//Julio 23 de 2012. Para el cco se hace trim ya que a veces trae un espacio

	//Buscando las camas a mostrar, con su respectivo centro de costos
	$sql = "SELECT
				habcod as cama, habcco as cco, cconom as nom
			FROM
				{$wbasedato}_000011 c, {$wbasedato}_000067  b LEFT OUTER JOIN {$wbasedato}_000016 a ON habhis = inghis AND habing = inging
			WHERE
				habcco like '$cco'
				AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
				AND habest  = 'on'
				AND habcco  = ccocod
				AND ccohos  = 'on'
				AND ccourg != 'on'
				AND ccoest  = 'on'
				AND ccocir != 'on'
			  GROUP BY 2, 1 ";

	// echo $sql."<br>";
	$res = mysql_query( $sql, $conex ) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());;

	$dias = calcularDias( $fechaini, $fechafin );

	$hasRow = false;

	$rows = mysql_fetch_array($res);

	$arr_hab_dia = array();

	if( $tipoReporte == 1 || $tipoReporte == 3 ){
		if( $tipoEmpresaReporte != 1 ){
			echo "<center><b>REPORTE RESUMIDO POR HABITACION</b></center>";
			echo "<br><br>";
		}
		else{
			echo "<center><b>REPORTE RESUMIDO POR HABITACION</b></center>";
			echo "<br><br>";
		}
	}

	for(; $rows;){

		$q = "   SELECT ing.Historia_clinica, ing.Num_ingreso, ing.Fecha_data as fecha_ingreso
			   FROM ".$wbasedato."_000032 ing, ".$wbasedato."_000033 egr
			  WHERE	ing.Fecha_data = egr.Fecha_data
				AND egr.Historia_clinica = ing.Historia_clinica
				AND ing.Servicio = egr.Servicio
				AND ing.Fecha_data BETWEEN '".$fechaini."' AND '".$fechafin."'
				AND ing.Servicio LIKE '%".$rows['cco']."%' ";
		$res1 = mysql_query($q,$conex);

		while($row_his_ing = mysql_fetch_array($res1))
		{

			$whab_ocupada = consul_hab_ocupada($row_his_ing['Historia_clinica'],$row_his_ing['Num_ingreso'], $rows['cco'], $row_his_ing['fecha_ingreso'] );

			 //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($whab_ocupada, $arr_hab_dia))
			{
				$arr_hab_dia[$whab_ocupada] = array();
			}

			//Aqui se forma el arreglo
			$arr_hab_dia[$whab_ocupada][] = $row_his_ing['Historia_clinica'];

		}
		// echo "<pre>" . print_r($arr_hab_dia, true) . "</pre>";

		//encabezado de la tabla por centro de costos
		//Solo se muestra si el reporte es Resumido o Resumido y Detallado
		if( $tipoReporte == 1 || $tipoReporte == 3 ){

			echo "<table align='center'>
				<tr class='encabezadotabla'>
					<td colspan='5'>Centro de Costos: {$rows['cco']}-{$rows['nom']}</td>
				</tr><tr class='encabezadotabla' align='center'>
					<td width='100'>Cama</td>
					<td width='100'>Total de días</td>
					<td width='100'>Días Sin uso</td>
					<td width='100'>Días Ocupados</td>
					<td width='100'>% de Ocupacion</td>
				</tr>";
		}

		// Calcula el número de camas disponibles en el periodo de fechas y servicio indicado
		$qcam= "SELECT
					habcco, COUNT(habcod) camas
				FROM
					{$wbasedato}_000011 c, {$wbasedato}_000067  b LEFT OUTER JOIN {$wbasedato}_000016 a ON habhis = inghis AND habing = inging
				WHERE
					habcco like '".$rows['cco']."'
					AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
					AND habest = 'on'
					AND habcco = ccocod
					AND ccohos = 'on'
					AND ccourg != 'on'
				    AND ccoest  = 'on'
				    AND ccocir != 'on'
				  GROUP BY 1 ";

		$res_cam = mysql_query( $qcam, $conex );
		$row_cam = mysql_fetch_array($res_cam);
		$camas = $row_cam['camas'];

		// Inicializo totales
		$total_sin_uso = 0;
		$total_ocupacion = 0;
		$suma_dias = 0;

		$auxcco = $rows['cco'];
		for( $i = 0; $auxcco == $rows['cco']; $i++ )
		{

			$ocupacion = tiempoOcupacion( $rows['cco'], $rows['cama'], $fechaini, $fechafin );

			if( $ocupacion>0 )
			{
				$codcama = trim($rows['cama']);
				//Si la habitacion tuvo ingreso y egresos el mismo dia sumara uno o mas a la ocupacion
			   //(El arreglo $arr_hab_dia viene de la consulta a las tablas 17, 32, 33, mas arriba de este comentario).
			   if(array_key_exists($codcama, $arr_hab_dia) and $rows['cco'] != '1179')
				{
					$ocupacion += count($arr_hab_dia[$codcama]);
				}

				if( $tipoReporte == 1 || $tipoReporte == 3  )
				{
					$rowscount['dso'] = $dias - $ocupacion;

					$fila =  "class='fila".($i%2+1)."'";

					echo "<tr $fila align='center'>
							<td>{$rows['cama']}</td>
							<td>$dias</td>
							<td>".number_format( $rowscount['dso'],2,'.',',')."</td>
							<td>".number_format( ($dias-$rowscount['dso']),2,".","," )."</td>
							<td>".number_format( (($dias-$rowscount['dso'])/$dias)*100,2,".",",")."</td>
						</tr>";

					$total_sin_uso += $rowscount['dso'];
					$total_ocupacion += $ocupacion;
					$suma_dias += $dias;
				}
			}

			$rows = mysql_fetch_array( $res);
		}

		if( $tipoReporte == 1 || $tipoReporte == 3 )
		{
			echo "<tr class='encabezadoTabla' align='center'>
					<td><b>TOTAL</b></td>
					<td>$dias</td>
					<td><b>".number_format( $total_sin_uso,2,'.',',')."</b></td>
					<td><b>".number_format( ($total_ocupacion),2,".","," )."</b></td>
					<td><b>".number_format( ($total_ocupacion/$camas)*100,2,".",",")."</b></td>
				</tr>";

			echo "</table><br><br>";
		}

		$hasRow = true;
	}

	if( !$hasRow ){
		echo "<p align='center'>NO HAY INFORMACION PARA LA CONSULTA SUMINISTRADA</p>";
	}

	if( $tipoReporte > 1 ){
		detallePorEmpresa( $dias, $camas, $cco, $fechaini, $fechafin );
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

//	mysql_close( $conex );
	//echo "<pre>".print_r($detalleCamas, true)."</true>";
}
?>
</body>
