<html>
<head>
  <title>ENTREGA Y RECIBO DE PACIENTES EN CADA SERVICIO</title>

<script type="text/javascript">
function enter()
{
	document.forms.ent_rec_pac.submit();
}

function cerrarVentana()
{
	window.close();
}

 function entregarpaciente(wemp_pmla, historia, ingreso, cco, ccohos, id_solicitud, wccoapl)
    {

    document.location.href= 'Ent_y_Rec_Pac.php?wemp_pmla='+wemp_pmla+'&whis='+historia+'&wing='+ingreso+'&wcco='+cco+'&went_rec=Ent&wccohos='+ccohos+'&wid='+id_solicitud+'&wccoapl='+wccoapl;

    }

 function recibopaciente(wemp_pmla, historia, ingreso, cco, ccohos, id_solicitud, wccoapl)
    {

    document.location.href= 'Ent_y_Rec_Pac.php?wemp_pmla='+wemp_pmla+'&whis='+historia+'&wing='+ingreso+'&wcco='+cco+'&went_rec=Rec&wccohos='+ccohos+'&wid='+id_solicitud+'&wccoapl='+wccoapl;

    }


</script>
</head>

<body onload=ira()>

<?php
include_once("conex.php");
/**
* ENTREGA Y RECIBO DE PACIENTES    *
* CONEX, FREE => OK				   *
*/
// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
$wactualiz = " Septiembre 19 de 2014 ";      // Aca se coloca la ultima fecha de actualizacion de este programa //
// --> 	2015-05-26: Si el recibo es de un paciente que viene de urgencias, actualizar el ultimo detalle del kardex, en Kadare = 'off' (Aprobado por regente)
// 		Jerson Trujillo.
//  2014-09-19: Jonatan Lopez:  Se calculan los dias de estancia dependiendo del centro de costos de origen del paciente, si es de urgencias consultara la movhos_000016 y si es hospitalario
//								calculara sobre la movhos_000032
//	2014-04-08: Jerson trujillo:
//				Se agrega la funcion regrabarCargosAmbulatorios(), la cual permitira regrabarle los cargos grabados en urgencias,
//				grabarselos con tarifas hospitalarias cuando se haga un recibo de paciente de urgencias a un piso de hospitalizacion
//				esta funcionalidad solo estara activa cuando entre en funcionamiento la facturacion inteligente.
// Diciembre 02 de 2013 (Jonatan Lopez) Se agrega union con la tabla movhos_000143, donde se use la tabla movhos_000003, para traiga los datos de contingencia, en caso de que no haya conexion con unix.
// Mayo 16 de 2013 (Jerson trujillo): Se agregan nuevos campos para el insert del traslado de dietas
// Abril 24 de 2013 (Edwin MG)	Creo variable global $wfecha_a_buscar para que funcione correctamente la función query_articulo del include movhos.inc.php
// Abril 23 de 2013 (Jerson Trujillo): Modificacion para las dietas, se agregaron unos campos que hacian falta al hacer el traslado de la dieta.
// Enero 23 de 2012	(Edwin MG)	Al recibir el paciente, la justificación de los medicamentos de más de 2 rondas anteriores queda justificado automáticamente según el código de
//								justificación parametrizado en root_000051
// Diciembre 19 de 2012:(Jonatan Lopez)Se modifica el recibo de pacientes para que se relacione con la solicitud de cama y al recibirlo se marcara la solicitud como realizada y
//								saldra de la lista de solicitud de cama.
// Diciembre 12 de 2012:(Jonatan Lopez)Se mueve el registro de egreso (movhos_000033) cuando se hace el recibo de un paciente.
// Noviembre 30 de 2012:(Jonatan Lopez)Se cambia la consulta de paciente para la entrega de paciente, en la que solo se mostrara los que tengan solicitud desde la
//                              solicitud de camilleros y que tengan habitacion asignada, al entregarlo la enfermera no tendra que seleccionar el cco y la cama
//                              sino que ya la traera de la tabla cencam_000003, en la cual se registra la habitacion asignada. al entregarlo se marcara la solicitud
//                              como cumplido y saldra de la lista de solicitudes.
// Agosto 28 de 2012 (Edwin MG) Se impide realizar entrega de un paciente si la ronda en la cual se entrega el paciente no se ha terminado de aplicar los medicamentos correspondientes.
//								Al momento de hacer el recibo, los medicamentos que se encuentren entre las rondas comprendidas entre la ronda siguiente de la entrega del paciente
//								y cuatro horas antes del recibo quedan justificadas automaticamente.
//								Estas reglas no aplican si el paciente es traslado de urgencias a un piso
// 2012-07-10:(Jerson trujillo) Se agrega un procedidmiento para que cuando se relice un recivo de paciente, automaticamente se cancelen todos los pedidos de
//								alimentacion posteriores al actual.
// 2012-06-15  (Viviana Rodas)  Se agregan las funciones consultaCentroCostos que lista los centros de costos, de un grupo determinado y dibujarSelect que dibuja el select de los centros de costos.
// 2012-04-09: (Mario Cadavid) 	Se crea la variable $whabdes_aux que compara habcod = "-1" en la tabla movhos_000020 cuando $whabdes1[0] == "", esto porque estaba
//								dejando grabar registros asi no seleccionaran habitación destino, ya que existia un registro con habcod = "" en la tabla movhos_000020,
//								de este modo ya no deja pasar la grabación si no seleccionan habitación destino en los cco que no sean urgencias o cirugia
//								Se incluyó la visualización de la variable $aviso en la sección de recibo
// 2012-03-15: (Mario Cadavid) 	Se adicionó la función "recibir_medicamentos" que coloca como recibidos los medicamentos del carro que se agreguen al traslado
//								Siempre y cuando estos medicamentos hayan sido cargados antes de el registro de entrega del paciente
// 2011-11-18: (Edwin MG)		Se agrega en los inserts de la tabla 000015 de movhos, los campos aplufr y apldos
// 2011-11-10: (Mario Cadavid) 	Se incluyó la función es urgencias para usar en la grabación de entrega, si es urgencias poner Ubiptr='off'
// 2011-11-10: (Mario Cadavid) 	$whabdes1[0] = "-1" se cambio a $whabdes1[0] = "", debido que esta variable se usa para grabar además de consultar
// 2011-11-08: (Mario Cadavid) 	Se incluye cirugia en la selección de centros de costos de entrega y se modifica la validación y grabación cuando el centro de costos es cirugia o urgencias. También se ocultó el botón "Grabar entrega" si hay alguna validación pendiente y se muestran los avisos de validaciones pendientes
// 2008-03-13: 					Se cambia el query que trae el maximo numero del recibo de los pacientes para que muestre el detalle de los articulos para recibir


// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
/**
* **************************************FUNCIONES********************************************************************************
*/

function consultarAplicadoJustificado($conex, $wbasedato, $whis, $wing, $wart, $whora_par_actual, $wfecha_actual, &$wjustificacion, $wido){

	//===============================================================
	//Paso la hora a formato de 12 horas
	//===============================================================
	//Dejo el formato a 24 horas con meridiano (AM - PM)
	$whora_a_buscar = gmdate( "H:00 - A", $whora_par_actual*3600 );
	//===============================================================

	//===============================================================
	 $q = " SELECT COUNT(*) "
		     ."   FROM ".$wbasedato."_000015 "
		     ."  WHERE aplhis = '".$whis."'"
		     ."    AND apling = '".$wing."'"
		     ."    AND aplfec = '".$wfecha_actual."'"
	         ."    AND aplron like '".trim($whora_a_buscar)."'"
		     ."    AND aplart = '".$wart."'"
		     ."    AND aplest = 'on' "
			 ."    AND aplido = ".$wido;

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	if ($row[0] > 0){
	    return true;
	}
	else   //Si NO tiene aplicacion busco si tiene Justificacion de porque NO se aplico
	{
		//===============================================================
		//Busco si tiene Justificacion
		//===============================================================
		$q = " SELECT jusjus "
		    ."   FROM ".$wbasedato."_000113 "
		    ."  WHERE jushis = '".$whis."'"
		    ."    AND jusing = '".$wing."'"
		    ."    AND jusfec = '".$wfecha_actual."'"
		    ."    AND jusron LIKE '".trim($whora_a_buscar)."'"
		    ."    AND jusart = '".$wart."'"
		    ."    AND jusido = ".$wido;

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

	    if( $num > 0 ){
			$row = mysql_fetch_array($res);
			$wjustificacion = $row[0];
		}
		else{
			$wjustificacion="";
		}

		return false;     //Indica que no esta aplicado
	}
}

//Consulta el tipo de habitacion a facturar.
function consultartipohab($conex, $whab, $wbasedato){

	$q = " SELECT Habtfa "
		."   FROM ".$wbasedato."_000020 "
		."  WHERE habcod = '".$whab."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	return $row['Habtfa'];


}

/******************************************************************************************************************************
 * La funcion valida que si se puede entregar o recibir un paciente
 *
 * - Cuando se entrega un paciente, la ronda actual tiene que estar completamente aplicada
 * - Al momento de recibir un paciente, la rondas que no esten aplicadas antes a mas de 4 horas, desde el momento de entrega
 *   se les debe colocar automaticamente una justificación.
 *
 * $cco					Centro de costos al cual que pertenece el paciente
 * $hab					habitacion en que se encuentra el paciente
 * $fecha				fecha de aplicación que se desea validar
 * $rondaInicial		Ronda en que se desea validar las aplicaciones
 * $totalRondas			Ronda en que se desea validar las aplicaciones
 * $proceso				valido si es entrega
 ******************************************************************************************************************************/
function validarEntregaRecibo( $conex, $wbasedato, $cco, $hab, $his, $ing, $fecha, $rondaInicial, $totalRondas, $proceso ){

	global $whora_par_actual;
	global $wemp_pmla;

	$whora_par_actual = $rondaInicial;

	$val = true;

	if( $proceso == "Ent" ){	//entrega de paciente

		if( !es_cirugia_o_urgencias( $cco ) ){

			$rondaInicialUnix = strtotime( $fecha." ".$rondaInicial.":00:00" );

			//recorro el rango de rondas por aplicar
			for( $ronda = $rondaInicialUnix; $ronda < $rondaInicialUnix + $totalRondas*2*3600; $ronda += 2*3600 ){

				if( $val ){

					$habitacionesFaltantes = '';		//indica que habitaciones hay sin aplicar

					$whora_par_actual = date( "H", $ronda );

					//Valido que no falten aplicaciones para la ronda de entrega de pacientes
					$haySinAplicar = estaAplicadoCcoPorRonda( $cco, date( "Y-m-d", $ronda ), date( "H", $ronda )*1, &$habitacionesFaltantes );

					if( !$haySinAplicar && !empty($habitacionesFaltantes) ){

						//Si hay habitaciones sin aplicar busco si se encuentra la habitacion del paciente
						//$habitacionesFaltantes es un array cuyo valor tiene las habitaciones sin aplicar
						for( $i = 0; $i < count($habitacionesFaltantes); $i++ ){

							//Si se encuentra la habitacion del paciente significa que no debe hacerse la entrega
							if( strtoupper( $hab ) == strtoupper( $habitacionesFaltantes[$i] ) ){
								$val = false;
								break;
							}
						}
					}
				}
			}
		}

		unset( $whora_par_actual );
	}
	else{	//recibo de paciente

		/******************************************************************************************************************
		 * El proceso para el recibo es el siguiente
		 *
		 * - Busco la fecha y hora de la ultima entrega del paciente
		 * - Verifico que hallan pasado mas de 4 horas desde la entrega
		 * - Todo medicamento que se encuentre entre la ultima ronda y la antepenultima ronda, debe quedar justificado
		 *   automaticamente si no esta aplicado o justificado
		 ******************************************************************************************************************/

		 //Busco la fecha y hora de la ultima entrega
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000017
				WHERE
					eyrhis = '$his'
					AND eyring = '$ing'
					AND eyrest = 'on'
					AND eyrsde = '$cco'
					AND eyrtip = 'Entrega'
				ORDER BY
					fecha_data desc, hora_data desc
				";

		$resEnt = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		if( $rowEntrega = mysql_fetch_array( $resEnt ) ){

			if( !es_cirugia_o_urgencias( $rowEntrega['Eyrsor'] ) ){

				//verifico que halla pasado mas de 4 horas desde la entrega
				//Para ello convierto la fecha y hora de entrega en formato unix
				$fechorEntrega = strtotime( $rowEntrega['Fecha_data']." ".$rowEntrega['Hora_data'] ) - strtotime( "1970-01-01 00:00:00" );

				//Busco la ronda siguiente de la entrega ya que la ronda en la que se hizo la entrega tuvo que
				//ya ser aplicada
				$fechorEntrega = ceil( $fechorEntrega/(2*3600) )*2*3600 + strtotime( "1970-01-01 00:00:00" );	//Esto halla la hora par siguiente

				$fechorActual = time();

				if( $fechorActual - $fechorEntrega >= 4*3600 ){

					//Busco los medicamento que pertenecen al rango de rondas
					for( $i = $fechorEntrega; $i < $fechorActual-4*3600; $i += 2*3600 ){

						$whora_par_actual = date( "H", $i );

						$wfecha_a_buscar = date( "Y-m-d", $i );	//Abril 24 de 2013

						//Consulto los medicamentos que pertenecen a una ronda para la fecha actual
						query_articulos( $his, $ing, date( "Y-m-d", $i ), &$resArt );
						$num = mysql_num_rows( $resArt );

						if($num == 0)  //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
						{
							query_articulos( $his, $ing, date( "Y-m-d", $i - 24*3600 ), &$resArt );
							$num = mysql_num_rows( $resArt );
						}

						if( $num > 0 ){

							while( $rowsArticulos = mysql_fetch_array( $resArt ) ){

								//fecha y hora de inicio del articulo
								$fechorInicio = strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5].":00:00" );

								//verifico que si pertenezca el articulo por fecha y hora de inicio
								if( $fechorInicio <= $i ){

									//Si pertenezca a la ronda por dosis maximas o días de tratamiento
									$porDmaDtto = true;

									//Quito espacios de la base a los campos por si trae de la base de datos
									$rowsArticulos['Kaddma'] = trim( $rowsArticulos['Kaddma'] );
									$rowsArticulos['Kaddia'] = trim( $rowsArticulos['Kaddia'] );

									if( $rowsArticulos['Kaddma'] != '' || $rowsArticulos['Kaddia'] != '' ){

										if( $rowsArticulos['Kaddma'] != '' ){	//dosis maxima

											//busco cuando termina el articulo por dosis maxima
													  //incio articulo    dosis maxima				    frecuencia en horas
											$finArt = $fechorInicio + ( $rowsArticulos['Kaddma'] - 1 )*$rowsArticulos['perequ']*3600;
										}
										elseif( $rowsArticulos['Kaddia'] != '' ){	//dias de tratamiento

													 //fecha y hora de inicio del medicamento a las 00			//dias de tratamiento
											$finArt = strtotime( date( "Y-m-d 00:00:00", $fechorInicio ) ) + $rowsArticulos['Kaddia']*24*3600-1;
										}

										//Si la fecha y hora de terminación del articulo es menor a la ronda actual
										//no pertenece a la ronda
										if( $i > $finArt ){
											$porDmaDtto = false;
										}
									}

									if( $porDmaDtto ){

										//Si no esta aplicado o justificado el articulo, entonces hay que justificarlo
										$artJusApl = consultarAplicadoJustificado($conex, $wbasedato, $his, $ing, $rowsArticulos['kadart'], $whora_par_actual, date( "Y-m-d", $i ), &$wjustificacion, $rowsArticulos['Kadido'] );

										//Si no fue aplicado o justificado debo justificar el medicamento
										if( !$artJusApl ){

											/******************************************************************************************
											 * Consulto la justificación por la no aplicación
											 ******************************************************************************************/
											$wjust = '';

											$codJus = consultarAliasPorAplicacion( $conex, $wemp_pmla, "justificacionAutomaticaReciboPacientes" );	//Enero 23 de 2012

											//Enero 23 de 2012
											//Consulto la justificación
											$sqlJus = "SELECT
															*
														FROM
															{$wbasedato}_000023
														WHERE
															juscod = '$codJus'
														";

											$resJust = mysql_query( $sqlJus, $conex ) or die( mysql_errno()." - Error en el query $sqlJus - ".mysql_error() );

											if( $rowsJust = mysql_fetch_array( $resJust ) ){
												$wjust = $rowsJust[ 'Juscod' ]." - ".$rowsJust[ 'Jusdes' ];	//coloco justificación
											}
											/******************************************************************************************/

											$q = " INSERT INTO ".$wbasedato."_000113(   Medico       ,   Fecha_data       ,   Hora_data        ,   jushis  ,   jusing  ,           jusart             ,       jusfec            ,           jusron          ,   jusjus   ,          jusido            , Seguridad        ) "
												."                            VALUES('".$wbasedato."','".date("Y-m-d")."' ,'".date("H:i:s")."' ,'".$his."' ,'".$ing."' ,'".$rowsArticulos['kadart']."','".date( "Y-m-d", $i )."', '".date( "H:i - A", $i )."','".$wjust."',".$rowsArticulos['Kadido'].", 'C-".$wbasedato."') ";

											$res1 = mysql_query($q,$conex) or die ( "Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() );
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	unset( $whora_par_actual );

	return $val;
}

/****************************************************************************************
 * Noviembre 18 de 2011
 ****************************************************************************************/
function consultarFraccion( $conex, $wbasedato, $wcenmez, $articulo ){

	$val = Array();

	//Consulto el medicamento en SF
	$sql = "SELECT *
			FROM {$wbasedato}_000026
			WHERE
				artcod = '$articulo'
			";

	$resArt = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $resArt );

	if( $num > 0 ){
		//Consulto fraccion para SF
		$val = consultarFraccionPorArticulo( $conex, $wbasedato, $articulo, 1050 );

		if( empty($val) ){
			$rows = mysql_fetch_array( $resArt );
			$val['unidad'] = $rows['Artuni'];
			$val['fraccion'] = 1;
		}
	}
	else{
		//Consulto en CM
		$sql = "SELECT *
				FROM {$wcenmez}_000002
				WHERE
					artcod = '$articulo'
				";

		$resArt = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $resArt );

		if( $num > 0 ){
			$val = consultarFraccionPorArticulo( $conex, $wbasedato, $articulo, 1051 );

			//Si no encuentro debe estar en CM
			if( empty($val) ){
				$rows = mysql_fetch_array( $resArt );
				$val['unidad'] = $rows['Artuni'];
				$val['fraccion'] = 1;
			}
		}
	}

	return $val;
}
/****************************************************************************************/

/************************************************************************************************************
 * Consulta la unidad y fraccion de un articulo segun la tabla de fracciones movhos_000059
 ************************************************************************************************************/
function consultarFraccionPorArticulo( $conex, $wbasedato, $articulo, $cco ){

	$val = Array();

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000059
			WHERE
				defart = '$articulo'
				AND defcco = '$cco'
				AND defest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		$rows = mysql_fetch_array( $res );
		$val['unidad'] = $rows['Deffru'];
		$val['fraccion'] = $rows['Deffra'];
	}

	return $val;
}


//Funcion que actualiza la solicitud a realizada en la tabla 10 de cencam, y actualiza el identificador con la fecha y hora de llegada
//si no la tiene, ademas de la fecha y hora de cumplimiento si no la tiene.
function actualizarregistros($wid)
{
    global $conex;
    global $wcencam;
    global $wfecha;
    global $whora;


    //La solicitud se cambia a realizado en la tabla 10 de cencam.
    $q=  " UPDATE ".$wcencam."_000010 "
        ."    SET Acarea = 'on' "
        ."  WHERE Acaids ='".$wid."'"
        ."    AND Acaest = 'on'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    //Se consultan los datos para la solicitud
    $sql = "SELECT Fecha_llegada, Hora_llegada, Fecha_Cumplimiento, Hora_cumplimiento
			  FROM ".$wcencam."_000003
			 WHERE id = '".$wid."'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_array($res);

    $wfecha_llegada = $row['Fecha_llegada'];
    $wfecha_cumplimiento = $row['Fecha_Cumplimiento'];

    //Aqui actualizamos la fecha de llegada si no tiene.
    if ($wfecha_llegada == '0000-00-00')
    {
    $q=  " UPDATE ".$wcencam."_000003 "
        ."    SET Fecha_llegada = '".$wfecha."', Hora_llegada = '".$whora."' "
        ."  WHERE id ='".$wid."'"
        ."    AND Anulada = 'No'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    }

    //Aqui actualizamos la fecha de cumplimiento si no tiene.
    if ($wfecha_cumplimiento == '0000-00-00')
    {
    $q=  " UPDATE ".$wcencam."_000003 "
        ."    SET Fecha_cumplimiento = '".$wfecha."', Hora_cumplimiento = '".$whora."' "
        ."  WHERE id ='".$wid."'"
        ."    AND Anulada = 'No'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    }


}
function verificar_saldo($wcodart, $wapl, $vector)
{
	global $wbasedato;
	global $wcenmez;
	global $conex;
	global $whis;
	global $wing;
	global $wcco;
	global $wcantidad;
	global $wok;

	$can=$wcantidad;
	for($i=1; $i<count($vector); $i++)
	{
		if($vector[$i][0]==$wcodart)
		{
			$can=$can+$vector[$i][1];
		}
	}
	if ($wapl == "on")
	  {
		$q =  " SELECT sum(spluen-splusa) "
			. "   FROM ".$wbasedato."_000030, ".$wbasedato."_000011 "
			. "  WHERE splhis = '" . $whis . "'"
			. "    AND spling = '" . $wing . "'"
			. "    AND splart = '" . $wcodart . "'"
		    . "    AND (splcco = '".$wcco."'"
		    ."      OR (splcco = ccocod "
		    ."     AND  ccotra = 'on')) ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);
		if ($row[0] >= $can)
		$wok = "on";
		else
		$wok = "off";
	  }
	 else
	   {
		$q = " SELECT sum(spauen-spausa) "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis = '" . $whis . "'"
		. "    AND spaing = '" . $wing . "'"
		. "    AND spaart = '" . $wcodart . "'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		if ($row[0] >= $can)
		$wok = "on";
		else
		$wok = "off";
	   }

	return $wok;
}
// ===========================================================================================================================================
// *******************************************************************************************************************************************
// *******************************************************************************************************************************************
// ===========================================================================================================================================
function buscar_articulo(&$wcodart)
{
	global $wbasedato;
	global $wcenmez;
	global $conex;
	global $wok;
	global $wartnom;
	global $wartuni;
	global $wunides;
	// Busco el nombre del articulo en el maestro de  articulos de movhos
	$q = " SELECT artcom, artuni, unides "
	. "   FROM " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
	. "  WHERE artcod = '" . $wcodart . "'"
	. "    AND artuni = unicod ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$wartnom = $row[0];
		$wartuni = $row[1];
		$wunides = $row[2];
		$wok = "on";
	}
	else
	{
		// Busco el nombre del articulo en la base de datos de central de mezclas
		$q = " SELECT artcom, artuni, unides "
		. "   FROM " . $wcenmez . "_000002, " . $wbasedato . "_000027 "
		. "  WHERE artcod = '" . $wcodart . "'"
		. "    AND artuni = unicod ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$row = mysql_fetch_array($res);
			$wartnom = $row[0];
			$wartuni = $row[1];
			$wunides = $row[2];
			$wok = "on";
		}
		else
		{
			// Busco el nombre del articulo en la base de datos de central de 'movhos', pero buscando con el
			// codigo del proveedor en la tabla movhos_000009
			$wcodart=BARCOD($wcodart);
			$q = " SELECT artcom, artuni, unides, " . $wbasedato . "_000009.artcod "
			. "   FROM " . $wbasedato . "_000009, " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
			. "  WHERE artcba                       = '" . $wcodart . "'"
			. "    AND " . $wbasedato . "_000009.artcod = " . $wbasedato . "_000026.artcod "
			. "    AND artuni                       = unicod ";

			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				$wartnom = $row[0];
				$wartuni = $row[1];
				$wunides = $row[2];
				$wcodart = $row[3];
				$wok = "on";
			}
			else
			{
				$wartnom = "Codigo no existe";
				$wartuni = "";
				$wunides = "";
				$wok = "off";
			}
		}
	}
}

// *******************************************************************************************************************************************
// ===========================================================================================================================================
// *******************************************************************************************************************************************
// ===========================================================================================================================================
function Detalle_ent_rec($wtip)
{
	global $whis;
	global $wing;
	global $wbasedato;
	global $conex;

	global $wartnom;
	global $wartuni;
	global $wunides;

	global $wcan_ent;
	global $wjustif;

	global $wnum_art;
	global $warr_art;
	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos

	if($wtip=='NoApl')
	{
		$q = " SELECT spaart, ROUND(sum(spauen-spausa),2) "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis                            = '" . $whis . "'"
		. "    AND spaing                            = '" . $wing . "'"
		. "    AND ROUND((spauen-spausa),4) > 0 "
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	else
	{
		/*$q = " SELECT Max(Eyrnum) "
		. "   FROM " . $wbasedato . "_000017 "
		. "  WHERE eyrhis                            = '" . $whis . "'"
		. "    AND eyring                            = '" . $wing . "'"
		. "    AND eyrtip= 'Entrega' ";*/

		// 2008-03-13
		$q = " SELECT Eyrnum, Fecha_data, Hora_data "
		. "   FROM " . $wbasedato . "_000017 "
		. "  WHERE eyrhis                            = '" . $whis . "'"
		. "    AND eyring                            = '" . $wing . "'"
		. "    AND eyrtip= 'Entrega' "
		. "  ORDER BY 2 desc, 3 desc";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$q = " SELECT Detart, sum(Detcan) "
		. "   FROM " . $wbasedato . "_000019 "
		. "  WHERE detnum                        = '" . $row[0]  . "'"
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num >= 1)
	{
		$wnum_art = $num;

		echo "<input type='HIDDEN' name='wnum_art' value='" . $wnum_art . "'>";

		echo "<center><table border=0>";
		echo "<tr class=encabezadoTabla>";
		echo "<td>Articulo</font></td>";
		echo "<td>Descripción</font></td>";
		echo "<td>Presentación</font></td>";
		echo "<td>Cantidad</font></td>";
		echo "</tr>";

		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);

			if ($i % 2 == 0)
			   $wclass = "fila1";
			  else
			    $wclass = "fila2";

			echo "<tr class=".$wclass.">";
			echo "<td>".$row[0]."</td>";
			buscar_articulo($row[0]);

			echo "<td>".$wartnom."</td>";
			echo "<td>".$wunides."</td>";
			echo "<td align=center>".$row[1]."</td>";
			echo "</tr>";

			$warr_art[$i][0] = $row[0];
			$warr_art[$i][1] = $row[1];

			echo "<input type='HIDDEN' name='warr_art[" . $i . "][0]' value='" . $warr_art[$i][0] . "'>";
			echo "<input type='HIDDEN' name='warr_art[" . $i . "][1]' value='" . $warr_art[$i][1] . "'>";
		}
	}
}


function AnularAplicacion($whis, $wing, $wcco, $wart, $wartnom, $wcta, $wuser, $apv)
{
	//AnularAplicacion($whis, $wing, $wcco, strtoupper($warr_art[$i][0]), $wartnom, $wcta, $wuser, 'off')
	global $wbasedato;
	global $conex;

	//lo que se hace aca es una anulacion de la aplicacion hasta que sea necesario
	//selecciono las aplicaciones de la tabla 15
	$q = " SELECT Aplcan, A.id, Aplron,  Aplusu,  Aplapr, Aplnum, Apllin, Aplfec, Aplufr, Apldos "
	. "   FROM " . $wbasedato . "_000015 A "
	. "  WHERE Aplhis = '" . $whis . "'"
	. "    AND Apling = '" . $wing . "'"
	. "    AND Aplart = '" . $wart . "'"
	. "    AND Aplest = 'on' ";

/*
	IF($apv=='on')
	{
		$q = $q."    AND Aplapv = 'on' ";
	}
	else
	{
		$q = $q."    AND Aplapv != 'on' ";
	}
*/
	$q = $q."     Order by 2 desc";

	$rest = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	$num = mysql_num_rows($rest);

	$can=$wcta;
	for ($j = 1;$j <= $num;$j++)
	{
		$row = mysql_fetch_array($rest);

		if( $j == 1 ){
			$dosis = $row['Apldos']/$row['Aplcan'];
		}

		//se anula la aplicacion
		$q = " UPDATE " . $wbasedato . "_000015 "
		. "    SET aplest = 'off' "
		. "  WHERE id='" .$row[1]. "' ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		if($row[0]<=$can)
		{
			$can=$can-$row[0];
		}
		else
		{
			$saldo=$row[0]-$can;
			//se graba la aplicacion con el saldo de lo que habia menos lo devuelto
			$q= " INSERT INTO ".$wbasedato."_000015 (    medico       ,          fecha_data,           hora_data,      Aplhis,       Apling,         Aplron,      Aplart,                                                        Apldes,     Aplcan,      Aplcco,        Aplusu,        Aplapr, Aplest,        Aplnum,        Apllin,        Aplfec,               Aplufr,                Apldos,     Seguridad ) "
			."                        		 VALUES ( '".$wbasedato."', '".date('Y:m:d')."', '".date('H:i:s')."', '".$whis."',  '".$wing."',  '".$row[2]."', '".$wart."', '".str_replace("'","\'",str_replace("\\","\\\\",$wartnom))."', ".$saldo.", '".$wcco."', '".$row[3]."', '".$row[4]."',   'on', '".$row[5]."', '".$row[6]."', '".$row[7]."', '".$row['Aplufr']."', '".($saldo*$dosis)."', 'A-".$wuser."')";
			$err = mysql_query($q,$conex);
			echo mysql_error();
			$num=mysql_affected_rows();
			if($num<1)
			{
				$error['color']="#ff0000";
				$error['codInt']="1019";
				$error['codSis']=mysql_errno();
				$error['descSis']=mysql_error();
				return (false);
			}

			$can=0;
		}

		if($can==0)
		{
			$j=$num+1;
		}
	}
	return(true);
}

function es_cirugia_o_urgencias($wcco)
{
	global $wbasedato;
	global $conex;

	$q = " SELECT count(*) "
		."   FROM ".$wbasedato."_000011 "
		."  WHERE ccocod = '".$wcco."' "
		."    AND (ccocir = 'on' "
		."     OR ccourg = 'on') ";
	$rescir = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$rowcir = mysql_fetch_array($rescir);

	if($rowcir[0]>0)
		return true;
	else
		return false;
}

function es_urgencias($wcco)
{
	global $wbasedato;
	global $conex;

	$q = " SELECT count(*) "
		."   FROM ".$wbasedato."_000011 "
		."  WHERE ccocod = '".$wcco."' "
		."    AND ccourg = 'on' ";
	$resurg = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$rowurg = mysql_fetch_array($resurg);

	if($rowurg[0]>0)
		return true;
	else
		return false;
}


// =============================================================================
// 2012-03-15
// Se colocan como recibidos los medicamentos del carro que se agreguen al traslado
// Siempre y cuando estos medicamentos hayan sido cargados antes de el registro de entrega del paciente
// =============================================================================
function recibir_medicamentos($historia,$ingreso,$origen,$destino,$articulo_cod,$articulo_ent,$origen_apl)
{
	global $wbasedato;
	global $conex;

	//if ($origen_apl=='on')		// Se comenta ya que no es solo para centros de costo de aplicación automática
	//{

		// Obtengo datos de entrega
		$q = " SELECT b.Fecha_data fecha, b.Hora_data hora, Detart, Detcan	 "
			."   FROM ".$wbasedato."_000017 a, ".$wbasedato."_000019 b "
			."  WHERE Eyrhis = '".$historia."'"
			."    AND Eyring = '".$ingreso."'"
			."	  AND Eyrsor = '".$origen."'"
			."	  AND Eyrsde = '".$destino."'"
			."	  AND Detart = '".$articulo_cod."'"
			."	  AND Eyrtip = 'Entrega'"
			."	  AND Eyrest = 'on'"
			."	  AND Eyrnum = Detnum"
			."	  AND Detest = 'on'";
		$resent = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$nument = mysql_num_rows($resent);
		$rowent = mysql_fetch_array($resent);
		//echo $q." - Datos de entrega<br>";

		// Consulto datos de lo cargado
		$q = " SELECT b.Fecha_data fecha, b.Hora_data hora, Fdenum, Fdelin, Fdecan, '000003' AS tabla_origen"
			."   FROM ".$wbasedato."_000002 a, ".$wbasedato."_000003 b "
			."  WHERE fenhis = '".$historia."'"
			."    AND fening = '".$ingreso."'"
			."	  AND fennum = fdenum"
			."	  AND fdeart = '".$articulo_cod."'"
			."	  AND Fenest = 'on'"
			."	  AND Fdeest = 'on'"
			."	  AND Fdedis = 'on'"
			/*********************************************************************************************************************/
			/* Diciembre 02 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
			/*********************************************************************************************************************/
			."	UNION "
			." SELECT b.Fecha_data fecha, b.Hora_data hora, Fdenum, Fdelin, Fdecan, '000143' AS tabla_origen  "
			."   FROM ".$wbasedato."_000002 a, ".$wbasedato."_000143 b "
			."  WHERE fenhis = '".$historia."'"
			."    AND fening = '".$ingreso."'"
			."	  AND fennum = fdenum"
			."	  AND fdeart = '".$articulo_cod."'"
			."	  AND Fenest = 'on'"
			."	  AND Fdeest = 'on'"
			."	  AND Fdedis = 'on'"
			."	ORDER BY fdenum ASC, fdelin ASC";
		$rescar = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numcar = mysql_num_rows($rescar);
		//echo $q." - Datos de cargos<br>";

		// Si encuentra datos de lo cargado
		if ($numcar > 0)
		{
			// Variable que llevará la cuenta de la cantidad de articulos trasladados y restará según vaya recibiendo
			$res_ent = $articulo_ent;

			// Ciclo para recorrer los articulos cargados
			for($l=0;$l<$numcar;$l++)
			{
				$rowcar = mysql_fetch_array($rescar);

				// Fechas de entrega y cargo para definir que no se reciba lo cargado despues de la entrega
				$fecha_entrega = strtotime ( $rowent['fecha']." ".$rowent['hora'], $now = time()  );
				$fecha_cargo = strtotime ( $rowcar['fecha']." ".$rowcar['hora'], $now = time()  );

				//echo "$fecha_entrega - $fecha_cargo - $res_ent<br>";

				// Si aun no se ha recibido lo trasladado y la fecha y hora de cargo es menor o igual a la entrega
				if($res_ent>0 && $fecha_cargo<=$fecha_entrega)
				{
				    $tabla_origen = $rowcar['tabla_origen'];
					// Se actualiza la tabla de detalle de cargos estableciendo el articulo como recibido
					$q =  " UPDATE " . $wbasedato . "_".$tabla_origen." "
						. "    SET Fdedis = 'off' " // Articulo recibido
						. "  WHERE Fdenum = '".$rowcar['Fdenum']. "'"
						. "    AND Fdelin = '".$rowcar['Fdelin']. "'";
					$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
					$res_ent = $res_ent - $rowcar['Fdecan'];
					if($res_ent == 0)
						$recibir = 0;
					//echo $q." - Actualizacion movhos_000003 / 143 <br>";
				}
			}
		}
	//}
}

// *******************************************************************************************************************************************
// ===========================================================================================================================================
/**
* **************************************PROGRAMA********************************************************************************
*/
session_start();

if (!isset($user))
	if (!isset($_SESSION['user']))
	    session_register("user");
if(!isset($_SESSION['user']) and !isset($user)) //Se activa para presentaciones con Diapositivas
  echo "error usuario no registrado";
else
{
	include_once("root/comun.php");
	include_once("root/magenta.php");
	include_once("root/barcod.php");
	include_once("movhos/movhos.inc.php");

	$conex = obtenerConexionBD("matrix");

	// --> Jerson trujillo, 2014-04-08
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	if($nuevaFacturacion == 'on')
	{
		include_once("ips/funciones_facturacionERP.php");
	}

	// Se incializan variables de fecha hora y usuario
	if (strpos($user, "-") > 0)
	   $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	  else
	     $wuser=$user;

	$wfecha = date("Y-m-d");
	$whora = (string)date("H:i:s");
	// Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	$q = " SELECT empdes "
		."   FROM root_000050 "
		."  WHERE empcod = '".$wemp_pmla."'"
		."    AND empest = 'on' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	$wnominst = $row[0];

	$q = " SELECT detapl, detval, empdes "
		."   FROM root_000050, root_000051 "
		."  WHERE empcod = '".$wemp_pmla."'"
		."    AND empest = 'on' "
		."    AND empcod = detemp ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);

			if ($row[0] == "cenmez")
			$wcenmez = $row[1];

			if ($row[0] == "afinidad")
			$wafinidad = $row[1];

			if ($row[0] == "movhos")
			$wbasedato = $row[1];

			if ($row[0] == "camilleros")
			$wcencam = $row[1];

			if ($row[0] == "tabcco")
			$wtabcco = $row[1];

			$winstitucion=$row[2];
		}
	}
	else
	{
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	}

	encabezado("Entrega y Recibo de Pacientes",$wactualiz, "clinica");


	// FORMA ================================================================
	echo "<form name='ent_rec_pac' action='Ent_y_Rec_Pac.php' method=post>";
	echo "<center><table>";
	echo "<tr>";
	echo "</tr>";
	echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";
	echo "<input type='HIDDEN' name='wbasedato' value='" . $wbasedato . "'>";
	echo "<input type='HIDDEN' name='user' value='" . $user . "'>";
	// Entro aca si no se ha seleccionado el servicio origen
	if (!isset($wcco) or trim($wcco) == "")
	{


		if ($wuser=="03150")
		{
			//llamado a las funciones que listan los centros de costos y la que dibuja el select
			$cco="Ccohos";
			$sub="off";
			$tod="";
			//$cco=" ";
			$ipod="off";

			$centrosCostos = consultaCentrosCostos($cco);
			echo "<table align='center' border=0 >";
			$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);

			echo $dib;
			echo "</table>";

			echo "<center><tr><td align=center colspan=4 bgcolor=#cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
			echo "</table>";

			$j = 0;
		}
		else
		{
		       ?>
				 <script>
				 function ira(){document.ent_rec_pac.wcco.focus();}
				 </script>
			   <?php
			   echo "<td bgcolor=00CCFF align=center><INPUT TYPE='password' NAME='wcco' SIZE=7 id=section></td>";
		}
	}
	else
	{
		if (strpos($wcco,"-") > 0)
		{
			$wccosto=explode("-",$wcco);
			$wcco=$wccosto[0];
		}
		else
		  {
			if (strpos($wcco,".") > 0)
			   {
				$wccosto=explode(".",$wcco);
				$wcco=$wccosto[1];
			   }
		  }

		$q = " SELECT cconom "
			."   FROM ".$wtabcco
			."  WHERE ccocod = '".$wcco."'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);
		$wnomcco = $row[0];

		if (!isset($went_rec))
		{
			echo "<br>";
			echo "<center><table>";
			echo "<tr class=titulo>";
			//echo "<td bgcolor=660099 colspan=4 align=center><b><font size=4 color=ffffff>Servicio o Unidad: " . $wnomcco . "</font></b></td>";
			echo "<td colspan=4 align=center><b>Servicio o Unidad: " . $wnomcco . "</b></td>";
			echo "</tr>";
			echo "<tr class=fila1>";
			echo "<td align=center colspan=2><b>&nbsp</b></td>";
			echo "</tr>";
			echo "<tr class=tituloMedio>";
			echo "<td align=center colspan=2><b>SELECCIONE EL PROCESO A REALIZAR</font></b></td>";
			echo "</tr>";
			echo "<tr class=fila1>";
			echo "<td align=center colspan=2><b>&nbsp</b></td>";
			echo "</tr>";
			echo "<tr class=titulo>";
			echo "<td align=center>Entrega de Paciente :<br><INPUT TYPE='radio' NAME='went_rec' value='Ent' onclick='enter()'></td>";
			echo "<td align=center>Recibo de Paciente  :<br><INPUT TYPE='radio' NAME='went_rec' value='Rec' onclick='enter()'></td>";
			echo "</tr>";

			echo "<tr class=link>";
			echo "<td align=center colspan=7><b><A href='Ent_y_Rec_Pac.php?wtabcco=".$wtabcco."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."'>Retornar</b></A></td>";
			echo "</tr>";
			echo "</table>";
		}
		else // Aca entra cuando ya se selecciono el proceso a realizar.
		{
			if (!isset($wccohos)) // seleccionar la historia
			{
				echo "<center><table>";
				// Traigo el INDICADOR de si el centro de costo es hospitalario o No
				$q = " SELECT ccohos, ccoapl "
					."   FROM " . $wbasedato."_000011 "
					."  WHERE ccocod = '".$wcco."'";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$num = mysql_num_rows($res);
				if ($num > 0)
				{
					$row = mysql_fetch_array($res);
					if ($row[0] == "on")
					$wccohos = "on";
					else
					$wccohos = "off";

					if ($row[1] == "on")
					$wccoapl = "on";
					else
					$wccoapl = "off";

				}
				else
				{
					$wccohos = "off";
					$wccoapl = "off";
				}

				if ($wccohos == "on") // SI ES HOSPITALIZADO
				{

                    $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

					if ($went_rec == "Ent") // SI ES ENTREGA HAGO ESTE QUERY
					{

						// Aca trae los pacientes que estan hospitalizados en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado
					    $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ".$wcencam."_000003.id as idsolicitud "
							."   FROM " . $wbasedato . "_000020, root_000036, root_000037, " . $wbasedato . "_000018, ".$wcencam."_000003, ".$wcencam."_000010"
							."  WHERE habcco  = '" . $wcco . "'"
							."    AND habali != 'on' " // Que no este para alistar
							."    AND habdis != 'on' " // Que no este disponible, osea que este ocupada
							."    AND habhis  = orihis "
							."    AND habing  = oriing "
							."    AND oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
							."    AND oriced  = pacced "
							."    AND oritid  = pactid "
							."    AND habhis  = ubihis "
							."    AND habing  = ubiing "
							."    AND ubiptr  != 'on' " // Solo los pacientes que no esten siendo trasladados
							."    AND ubisac  = '" . $wcco . "'" // Servicio Actual
                            ."    AND ".$wcencam."_000003.id = ".$wcencam."_000010.Acaids "
                            ."    AND ".$wcencam."_000003.historia = ubihis  "
                            ."    AND Acaest = 'on'"
                            ."    AND Acarea = 'off'"
                            ."    AND Anulada = 'No'"
                            ."    AND central = '".$wcentral_camas."'"
							."  GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
							."  ORDER BY Habord, Habcod ";  //se agrega el campo orden
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						$num = mysql_num_rows($res);
					}
					else // SI ES RECIBO HAGO EL SIGUIENTE QUERY, que este en proceso de traslado en el servicio actual
					{
						// Aca trae los pacientes que estan hospitalizados en el Servicio (ubisac) del usuario matrix, haciendo la relacion con la solicitud de cama (cencam_000003)
					   $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ".$wcencam."_000003.id as idsolicitud "
							."   FROM " . $wbasedato . "_000020, root_000036, root_000037, " . $wbasedato . "_000018 ,  ".$wcencam."_000003, ".$wcencam."_000010"
							."  WHERE habcco  = '" . $wcco . "'"
							."    AND habali  = 'off' " // Que no este para alistar
							."    AND habhis  = orihis "
							."    AND habing  = oriing "
							."    AND oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
							."    AND oriced  = pacced "
							."    AND oritid  = pactid "
							."    AND habhis  = ubihis "
							."    AND habing  = ubiing "
							."    AND ubiptr  = 'on' " // Solo los pacientes que esten siendo trasladados
							."    AND ubisac  = '" . $wcco . "'" // Servicio Anterior
                            ."    AND ".$wcencam."_000003.id = ".$wcencam."_000010.Acaids "
                            ."    AND ".$wcencam."_000003.historia = ubihis  "
                            ."    AND Acaest = 'on'"
                            ."    AND hab_asignada = ubihac"
                            ."    AND Acarea in ('off','on')"
                            ."    AND Anulada = 'No'"
                            ."    AND central = '".$wcentral_camas."'"
							."  GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
							."  ORDER BY Habord, Habcod ";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						$num = mysql_num_rows($res);
					}
				}
				else // SI ES AMBULATORIO (URGENCIAS, ADMISIONES, ETC.)
				{
					// Aca trae los pacientes que no estaban hospitalizados (Urgencias)
					$q = " SELECT '', ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pactid, pacced "
						."   FROM " . $wbasedato . "_000018, root_000036, root_000037 "
						."  WHERE ubisac = '" . $wcco . "'"
						."    AND ubihis = orihis "
						."    AND ubiing = oriing "
						."    AND oriori = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
						."    AND oriced = pacced "
						."    AND oritid = pactid "
						."  GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
						."  ORDER BY 1 ";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);
				}

				echo "<tr class=titulo>";
				echo "<td colspan=5  align=center><b>Servicio o Unidad: " . $wnomcco . "</b></td>";
				echo "</tr>";

				echo "<tr class=fila1>";

				if ($went_rec == "Ent")
					echo "<td colspan=5  align=center><b>** ENTREGA ** DE PACIENTES CON SOLICITUD DE CAMAS</b></td>";
				else
					echo "<td colspan=5 align=center><b>** RECIBO ** DE PACIENTES</b></td>";
				echo "</tr>";

				echo "<tr class=encabezadoTabla>";
				echo "<th>Habitacion</th>";
				echo "<th>Historia</th>";
				echo "<th>Paciente</th>";
				echo "<th>Afinidad</th>";
				if ($went_rec == "Ent")
                {
                echo "<th>Ir a entrega</th>";
                }
                else
                {
                     echo "<th>Ir a recibo</th>";
                }

				echo "</tr>";

				if ($num > 0)
				{
				$i = 1;
					while($row = mysql_fetch_array($res))
					{


						if (is_integer($i / 2))
						   $wclass = "fila1";
						  else
						    $wclass = "fila2";

						$whab = $row[0]; //Habitacion
						$whis = $row[1]; //Historia
						$wing = $row[2]; //Ingreso
						$wpac = $row[3] . " " . $row[4] . " " . $row[5] . " " . $row[6]; //Nombre paciente
						$wtid = $row[7]; //Tipo documento paciente
						$wdpa = $row[8]; //Documento del paciente
						$widsolicitud = $row['idsolicitud'];  //id de la solicitud para el paciente en la tabla 3 de cencam.

                        //Consulto el centro de costos donde debe ser entregado el paciente
                        $q =     "  SELECT id,Hab_asignada  "
                                ."    FROM ".$wcencam."_000003"
                                ."   WHERE id = '".$widsolicitud."'"
                                ."     AND central = 'CAMAS'"
                                ."ORDER BY id desc";
                        $res_q = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
                        $row = mysql_fetch_array($res_q);
                        $whab_asignada = $row['Hab_asignada'];

                        if($whab_asignada != '')
                        {
                            if ($went_rec == "Ent")
                            {
                            $wboton_mensaje =  "<input type='button' onclick='entregarpaciente(\"".$wemp_pmla."\",\"".$whis."\",\"".$wing."\",\"".$wcco."\",\"".$wccohos."\",\"".$widsolicitud."\", \"".$wccoapl."\")' value='Ok'></font>";
                            }
                            else
                            {
                             $wboton_mensaje =  "<input type='button' onclick='recibopaciente(\"".$wemp_pmla."\",\"".$whis."\",\"".$wing."\",\"".$wcco."\",\"".$wccohos."\",\"".$widsolicitud."\", \"".$wccoapl."\")' value='Ok'></font>";
                            }
                        }
                        else
                        {
                            if($went_rec == "Ent")
                            {
                            $wboton_mensaje = 'Sin habitación <br> asignada';
                            }
                            else
                            {
                            $wboton_mensaje =  "<input type='button' onclick='recibopaciente(\"".$wemp_pmla."\",\"".$whis."\",\"".$wing."\",\"".$wcco."\",\"".$wccohos."\",\"".$widsolicitud."\", \"".$wccoapl."\")' value='Ok'></font>";
                            }

                        }
						echo "<tr class=".$wclass.">";
						echo "<td align=center><font size=3><b>" . $whab . "</b></font></td>";
						echo "<td align=center><font size=3><b>" . $whis . " - " . $wing . "</b></font></td>";
						echo "<td align=left><font size=3><b>" . $wpac . "</b></font></td>";
						// ======================================================================================================
						// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
						$wafin = clienteMagenta($wdpa, $wtid, &$wtpa, &$wcolorpac);
						if ($wafin)
						{
							echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
						}
						else
						   echo "<td>&nbsp</td>";

                           echo "<td align=center>$wboton_mensaje</td>"; //Aqui se muestra el boton Ok o el mensaje sin habitacion asignada


						// ======================================================================================================
						echo "</tr>";
					$i++;
					}
				}
				else
				echo "NO HAY HABITACIONES OCUPADAS";
				echo "</table>";

                ?>
   				 <script>
   				 function ira(){document.ent_rec_pac.whis.focus();}
    			</script>
  				<?php

  				echo "<br>";
  				echo "</SELECT></td></tr></table><br>";

  				// CENTRO DE COSTO
  				echo "<input type='HIDDEN' name='wcco'     value='" . $wcco . "'>";
  				if (isset($j)) echo "<input type='HIDDEN' name='j'        value='" . $j . "'>";
  				// NOMBRE DEL CENTRO DE COSTO
  				echo "<input type='HIDDEN' name='wnomcco'  value='" . $wnomcco . "'>";
  				// TABLA DE CENTRO DE COSTO
  				echo "<input type='HIDDEN' name='wtabcco'  value='" . $wtabcco . "'>";
  				// INDICADOR DE C. COSTO DE HOSPITALIZACION
  				echo "<input type='HIDDEN' name='wccohos'  value='" . $wccohos . "'>";
  				echo "<input type='HIDDEN' name='wccoapl'  value='" . $wccoapl . "'>";
  				echo "<input type='HIDDEN' name='went_rec' value='" . $went_rec . "'>";


  				echo "<br><br>";

  				echo "<table>";

  				//=================================================================================================================================================================
  				//***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***==
  				//=================================================================================================================================================================
  				//DESDE ACA HAGO UNA BUSQUEDA DE LAS CONDICIONES EN QUE SE ENCUENTRA EL SERVICIO

  				//Busco si hay pacientes por recibir en el servicio
  				$q = " SELECT count(*) "
	  				."   FROM ".$wbasedato."_000018 "
	  				."  WHERE ubisac = '".$wcco."'"
	  				."    AND ubiptr = 'on' ";
  				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  				$row = mysql_fetch_array($res);
  				if ($row[0] > 0)
  				{
  					echo "<tr class=encabezadoTabla>";
  					echo "<td>Tiene (".$row[0].") Paciente(s) para Recibir.</td>";
  					echo "</tr>";
  				}

  				//Busco a cuantos pacientes se les puede dar alta definitiva
  				$q = " SELECT count(*) "
	  				."   FROM ".$wbasedato."_000018,".$wbasedato."_000022 "
	  				."  WHERE ubialp  = 'on' "
	  				."    AND ubisac  = '".$wcco."'"
	  				."    AND ubihis  = cuehis "
	  				."    AND ubiing  = cueing "
	  				."    AND ubiald != 'on' "
	  				."    AND cuegen  = 'on' "
	  				."    AND cuepag  = 'on' "
	  				."    AND cuecok  = 'on' ";
  				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  				$row = mysql_fetch_array($res);
  				if ($row[0] > 0)
  				{
  					echo "<tr class=encabezadoTabla>";
  					echo "<td>Tiene (".$row[0].") Paciente(s) en alta esperando el Alta Definitiva.</td>";
  					echo "</tr>";
  				}

  				//Busco cuantos pacientes estan con factura pero no se han enviado a caja a pagar
  				$q = " SELECT count(*) "
	  				."   FROM ".$wbasedato."_000018,".$wbasedato."_000022 "
	  				."  WHERE ubialp  = 'on' "
	  				."    AND ubisac  = '".$wcco."'"
	  				."    AND ubihis  = cuehis "
	  				."    AND ubiing  = cueing "
	  				."    AND ubiald != 'on' "
	  				."    AND cuegen != 'on' "
	  				."    AND cuepag != 'on' "
	  				."    AND cuecok  = 'on' ";
  				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  				$row = mysql_fetch_array($res);
  				if ($row[0] > 0)
  				{
  					echo "<tr class=encabezadoTabla>";
  					echo "<td>Tiene (".$row[0].") Paciente(s) en alta y esta(n) siendo facturado(s) en este momento.</td>";
  					echo "</tr>";
  				}

  				//Busco a cuantos pacientes estan pendientes de la devolucion o que el facturador verifique si puede facturar
  				$q = " SELECT count(*) "
	  				."   FROM ".$wbasedato."_000018 "
	  				."  WHERE ubialp  = 'on' "
	  				."    AND ubisac  = '".$wcco."'"
	  				."    AND ubihis not in ( SELECT cuehis "
	  				."                          FROM ".$wbasedato."_000022 "
	  				."                         WHERE cuehis = ubihis  "
	  				."                           AND cueing = ubiing )"
	  				."    AND ubiald != 'on' ";
  				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  				$row = mysql_fetch_array($res);
  				if ($row[0] > 0)
  				{
  					echo "<tr class=encabezadoTabla>";
  					echo "<td>Tiene (".$row[0].") Paciente(s) en alta, pendiente(s) de la Devolución o que el facturador verifique si puede facturar.</td>";
  					echo "</tr>";
  				}

  				//Busco cuantos pacientes estan facturados pero pendientes del pago
  				$q = " SELECT count(*) "
	  				."   FROM ".$wbasedato."_000018,".$wbasedato."_000022 "
	  				."  WHERE ubialp  = 'on' "
	  				."    AND ubisac  = '".$wcco."'"
	  				."    AND ubihis  = cuehis "
	  				."    AND ubiing  = cueing "
	  				."    AND ubiald != 'on' "
	  				."    AND cuegen  = 'on' "
	  				."    AND cuepag != 'on' "
	  				."    AND cuecok  = 'on' ";
  				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  				$row = mysql_fetch_array($res);
  				if ($row[0] > 0)
  				{
  					echo "<tr class=encabezadoTabla>";
  					echo "<td>Tiene (".$row[0].") Paciente(s) en alta, pendiente(s) de pago en Caja.</td>";
  					echo "</tr>";
  				}

  				//Busco si hay pacientes con Muerte pero no tiene Proceso de Alta
  				$q = " SELECT count(*) "
	  				."   FROM ".$wbasedato."_000018 "
	  				."  WHERE ubisac  = '".$wcco."'"
	  				."    AND ubimue  = 'on'   "
	  				."    AND ubialp != 'on' ";
  				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  				$row = mysql_fetch_array($res);
  				if ($row[0] > 0)
  				{
  					echo "<tr class=encabezadoTabla>";
  					echo "<td>Tiene (".$row[0].") Paciente(s) con Muerte y Sin proceso de Alta.</td>";
  					echo "</tr>";
  				}
  				//=================================================================================================================================================================
  				//***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***===***==
  				//=================================================================================================================================================================
  				echo "</table>";
  				echo "<br><br>";
			}
			else
			{
				// */*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*///
				// ACA SE INGRESA CUANDO YA SE A SELECIONADO LA HISTORIA A TRASLADAR Y COMIENZA EL ENCABEZADO DEL TRASLADO //
				// Y SE SABE SI EL CENTRO DE COSTO ES HOSPITALARIO O NO Y SI GRABA APLICANDO DE UNA VEZ O NO               //
				// */*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*///
				if (isset($whis) and trim($whis) != "")
				{
					echo "</table>";
					echo "<br>";

					if ($went_rec=="Ent")
					{
						$q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, ubihac "
							."   FROM " . $wbasedato . "_000018, root_000036, root_000037 "
							."  WHERE ubisac = '" . $wcco . "'"
							."    AND ubihis = orihis "
							."    AND ubiing = oriing "
							."    AND oriori = '".$wemp_pmla."'"     // Empresa Origen de la historia,
							."    AND oriced = pacced "
							."    AND oritid = pactid "
							."    AND orihis = '" . $whis."'"
							."  GROUP BY 1,2,3,4,5,6,7 "
							."  ORDER BY 1 ";
					}
					else
					{
						// Aca traigo la historia con su numero de ingreso, nombre paciente y habitacion
						$q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, ubihac "
							."   FROM " . $wbasedato . "_000018, root_000036, root_000037 "
							."  WHERE ubisac = '" . $wcco . "'"
							."    AND ubihis = orihis "
							."    AND ubiing = oriing "
							."    AND oriori = '".$wemp_pmla."'"     // Empresa Origen de la historia,
							."    AND oriced = pacced "
							."    AND oritid = pactid "
							."    AND orihis = '" . $whis."'"
							."    AND ubiptr = 'on' "
							."  GROUP BY 1,2,3,4,5,6,7 "
							."  ORDER BY 1 ";
					}
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);
					if ($num > 0)
					{
						$row = mysql_fetch_array($res);
						$whis = $row[0];
						$wing = $row[1];
						$wpac = $row[2] . " " . $row[3] . " " . $row[4] . " " . $row[5];
						$whab = $row[6];

						// ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						// ============= P R O C E S O   D E   G R A B A C I O N =========================================================================================
						// ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						if (isset($wgrabar) and isset($wccodes))
						{
							$wccodes1 = explode("-", $wccodes);
							if(isset($whabdes))
							{
								$whabdes1 = explode("-", $whabdes);
							}
							else
							{
								$whabdes1[0] = "";
								$whabdes1[1] = "";
							}
							// Verifico que se alla seleccionado el centro de costo
							$q = " SELECT count(*) "
							    ."   FROM " . $wtabcco
							    ."  WHERE ccocod = '" . $wccodes1[0] . "'";
							$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
							$num = mysql_num_rows($res);
							$row = mysql_fetch_array($res);

							// 2012-04-09
							if($whabdes1[0] != "" && $whabdes1[0] != " ")
								$whabdes_aux = $whabdes1[0];
							else
								$whabdes_aux = "-1";

							// Verifico que se alla seleccionado la habitacion
							$q = " SELECT count(*) "
							    ."   FROM ".$wbasedato."_000020 "
							    ."  WHERE habcod = '".$whabdes_aux."'";
							$reshab = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
							$rowhab = mysql_fetch_array($reshab);

							// Si el centro de costos es cirugia o urgencias no se valida selección de habitación		// 2011-11-08
							$cirugia_o_urgencia = es_cirugia_o_urgencias($wccodes1[0]);

							if ($row[0] > 0 and ($rowhab[0] > 0 or $cirugia_o_urgencia))		// 2011-11-08
							{
								/****************************************************************************************
								 * Agosto 24 de 2012
								 * Valido que no falten aplicaciones de las ultimas rondas por aplicar pra poder realizar
								 * al entrega del paciente
								 ****************************************************************************************/
								//total de rondas que se quieren comprobar
								$totalRondas = 2;

								//A la fecha actual le resto total de rondas horas
								$fechaComprobar = time()-($totalRondas-1)*2*3600;			//fecha y hora a comprobar la aplicacion en formato Unix

								//busco la ronda actual
								$rondaInicial = floor( date( "H", $fechaComprobar )/2 )*2;

								//if ($whis!="288582")
								//   {
									//Valido que no falten aplicaciones para la ultimas rondas
									$procesar = validarEntregaRecibo( $conex, $wbasedato, $wcco, $whab, $whis, $wing, date( "Y-m-d", $fechaComprobar ), $rondaInicial, $totalRondas, $went_rec );
								//   }
								//  else
								//     $procesar=true;
								/****************************************************************************************/

								if( $procesar ){

									// Cuando se hace la entrega del paciente se graba en el campo 'ubisan' el codigo del centro de costos actual, osea el del usuario matrix que,
									// esta realizando la entrega y en el campo 'ubisac' el centro de costo nuevo que eligio para enviarlo.
									// Cuando se reciba el paciente se lee el campo 'ubisac' pero que este siendo trasladado unicamente.
									// Aca tambien se graba en las tablas del 'censo diario'.
									// ============================================================================================
									// ACA TRAIGO EL CONSECUTIVO DEL DOCUMENTO QUE SE GRABA EN EL MOVIMIENTO TABLAS:000017 y 000019
									// ============================================================================================
									$q = "lock table " . $wbasedato . "_000001 LOW_PRIORITY WRITE";
									$err = mysql_query($q, $conex);

									$wconsec = "";

									$q = " UPDATE " . $wbasedato . "_000001 "
									   . "    SET connum=connum + 1 "
									   . "  WHERE contip='entyrec' ";
									$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

									$q = "SELECT connum "
									. "  FROM " . $wbasedato . "_000001 "
									. " WHERE contip='entyrec' ";
									$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
									$row = mysql_fetch_array($err);
									$wconsec = $row[0];

									$q = " UNLOCK TABLES";
									$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
									// ============================================================================================
									if ($went_rec == "Ent") // ******* ENTREGA *******
									{
										$wtipo = "Entrega";

										$ubiptr = 'on';
										if(es_urgencias($wccodes1[0]))
											$ubiptr = 'off';

										// Aca actualizo la ubicación del paciente
										if($cirugia_o_urgencia)
										{
											$q = " UPDATE " . $wbasedato . "_000018 "
												."    SET ubisan = '".$wcco."',"        // Servicio Anterior
												."        ubihan = '".$whab."',"        // Habitacion Anterior
												."        ubisac = '".$wccodes1[0]."'," // Servicio Actual o Destino
												."        ubihac = ''," 				// Habitacion Actual o Destino
												."        ubiptr = '".$ubiptr."'  "     // En Proceso de Traslado
												."  WHERE ubihis = '".$whis."'"
												."    AND ubiing = '".$wing."'";
										}
										else
										{
											$q = " UPDATE " . $wbasedato . "_000018 "
												."    SET ubisan = '".$wcco."',"        // Servicio Anterior
												."        ubihan = '".$whab."',"        // Habitacion Anterior
												."        ubisac = '".$wccodes1[0]."'," // Servicio Actual o Destino
												."        ubihac = '".$whabdes1[0]."'," // Habitacion Actual o Destino
												."        ubiptr = 'on'  "                  // En Proceso de Traslado
												."  WHERE ubihis = '".$whis."'"
												."    AND ubiing = '".$wing."'";
										}

										$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

										if ((isset($whabdes) and trim($whabdes[0]) != "" and trim($whabdes[0]) != " ") or $cirugia_o_urgencia)
										{
											$wfecha = date("Y-m-d");
											$whora = (string)date("H:i:s");

											// Aca actualizo la habitacion anterior, la libero
											$q =  " UPDATE " . $wbasedato . "_000020 "
												. "    SET habhis = '', "
												. "        habing = '', "
												. "        habali = 'on', "          // habitacion para alistar
												. "        habdis = 'off', "         // habitacion disponible
												. "        habfal = '".$wfecha."', " // Fecha en que se coloca para alistar
												. "        habhal = '".$whora."' "   // Hora en que se coloca para alistar
												. "  WHERE habcod = '" . $whab . "'";
											$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

											if(!$cirugia_o_urgencia)
											{
												// Aca actualizo la habitacion actual o para la que va el paciente
												$q =  " UPDATE " . $wbasedato . "_000020 "
													. "    SET habhis = '" . $whis . "'," // Historia
													. "        habing = '" . $wing . "'," // Ingreso
													. "        habdis = 'off', "          // Habitacion ya lista
													. "        habali = 'off' "
													. "  WHERE habcod = '" .$whabdes1[0] . "'";
												$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
											}
										}
										else
										{
											// Aca actualizo las habitaciones
											$q =  " UPDATE " . $wbasedato . "_000018 "
												. "    SET ubisan = '" . $wcco . "',"        // Servicio Anterior
												. "        ubihan = '" . $whab . "',"        // Habitacion Anterior
												. "        ubisac = '" . $wccodes1[0] . "'," // Servicio Actual o Destino
												. "        ubihac = '" . $whabdes1[0] . "'," // Servicio Actual o Destino
												. "        ubiptr = 'on'  "                  // En Proceso de Traslado
												. "  WHERE ubihis = '" . $whis . "'"
												. "    AND ubiing = '" . $wing . "'";
											$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
										}


										$wserori1 = explode("-", $wserori);
										actualizarregistros($wid); //Aqui se actualiza el registro de la tabla cencam_000003 para la llegada, el cumplimiento y el realizado
                                        //de la tabla cencam_000010.
									}
									else // ******* RECIBO *******
									{
										$wtipo = "Recibo";
										// =============================================================================
										// Si esta en proceso de traslado, verifico si la cama que le habian asignado es
										// la misma que en la que quedo, si no, para liberar la habitacion o cama.
										// =============================================================================
										$q =  " SELECT ubihac "
											. "   FROM " . $wbasedato . "_000018 "
											. "  WHERE ubihis = '" . $whis . "'"
											. "    AND ubiing = '" . $wing . "'"
											. "    AND ubiptr = 'on' ";
										$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
										$row = mysql_fetch_array($err);

										if ($row[0] != $whabdes1[0])
										{
											// Libero la habitacion que se habia colocado cuando se entrego el paciente
											$q =  " UPDATE ".$wbasedato."_000020 "
												. "    SET habhis = '', "
												. "        habing = '', "
												. "        habdis = 'on' "
												. "  WHERE habcod = '".$row[0]."'";
											$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
										}
										// =============================================================================
										// =============================================================================
										// Aca actualizo la ubicación del paciente
										// =============================================================================
										$q =  " UPDATE ".$wbasedato."_000018 "
											. "    SET ubiptr = 'off', " // Termina el Proceso de Traslado
											. "        ubihac = '".$whabdes1[0] . "'"
											. "  WHERE ubihis = '".$whis."'"
											. "    AND ubiing = '".$wing."'";
										$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
										// =============================================================================
										// =============================================================================
										// Aca actualizo la habitación en la que quedo el paciente
										// =============================================================================
										$q =  " UPDATE " . $wbasedato . "_000020 "
											. "    SET habdis = 'off', " // Habitacion ya ocupada
											. "        habhis = '".$whis."',"
											. "        habing = '".$wing."'"
											. "  WHERE habcod = '".$whabdes1[0]. "'";
										$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
										// =============================================================================

										$wserori1 = explode("-", $wserori);

										if (trim($wserori1[0]) != trim($wccodes1[0]))
										{
											// ======================================================================================================================================================================================================================
											// Aca grabo el movimiento -- INGRESO -- del *** CENSO DIARIO ***
											// ======================================================================================================================================================================================================================
											// Si el paciente a estado antes en el servicio para el mismo ingreso, traigo cuantas veces para sumarle una
											$q = " SELECT COUNT(*) "
												."   FROM ".$wbasedato."_000032 "
												."  WHERE Historia_clinica = '".$whis."'"
												."    AND Num_ingreso      = '".$wing."'"
												."    AND Servicio         = '".$wccodes1[0]."'";
											$err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());
											$row = mysql_fetch_array($err);

											$wingser = $row[0] + 1; //Sumo un ingreso a lo que traigo el query

											$q = " INSERT INTO ".$wbasedato."_000032 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica ,   Num_ingreso,   Servicio       ,   Num_ing_Serv,   Fecha_ing ,   Hora_ing ,   Procedencia    , Seguridad     ) "
												."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."'         ,'".$wing."'   ,'".$wccodes1[0] . "','" . $wingser . "' ,'" . $wfecha . "','" . $whora . "','" . $wserori1[0] . "', 'C-" . $wuser . "')";
											$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
											// ======================================================================================================================================================================================================================

                                            // ======================================================================================================================================================================================================================
											// Aca grabo el movimiento de -- EGRESO -- del *** CENSO DIARIO ***
											// ======================================================================================================================================================================================================================
											
											$wcco_origen = es_urgencias($wserori1[0]);
											//Si el centro de costos de origen del paciente es de urgencias hara el calculo con los datos de la tabla movhos_000016, sino buscara en la tabla movhos_000032.
											if($wcco_origen){
											
											// Aca calculo los días de estancia en el servicio  ************************
											$q =  " SELECT ROUND(TIMESTAMPDIFF(HOUR,Fecha_data,now())/24,2) "
												. "   FROM ".$wbasedato."_000016 "
												. "  WHERE inghis = '".$whis."'"
												. "    AND inging = '".$wing."'";
											$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
											$row = mysql_fetch_array($err);
											$wdiastan = $row[0];
											
											}else{
											
												// Aca calculo los días de estancia en el servicio  ************************
											$q =  " SELECT ROUND(TIMESTAMPDIFF(HOUR,Fecha_ing,now())/24,2) "
												. "   FROM ".$wbasedato."_000032 "
												. "  WHERE Historia_clinica = '".$whis."'"
												. "    AND Num_ingreso      = '".$wing."'"
												. "    AND Servicio         = '".$wserori1[0]."'"
											   ." GROUP BY Num_ing_Serv DESC";
											$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
											$row = mysql_fetch_array($err);
											$wdiastan = $row[0];
											
											}
     

											if ($wdiastan == "" or $wdiastan == 0)
											   $wdiastan = 0;

											$q =  " INSERT INTO ".$wbasedato."_000033(   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,   Tipo_Egre_Serv ,  Dias_estan_Serv, Seguridad     ) "
												. "                            VALUES('".$wbasedato."','".$wfecha."','".$whora."','".$whis."'        ,'".$wing."'   ,'".$wserori1[0]."' ,".$wingser."  ,'".$wfecha."'      ,'".$whora."'     ,'".$wccodes1[0]."',".$wdiastan."    , 'C-" . $wuser . "')";
											$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
											// ======================================================================================================================================================================================================================
										}

										$wserori1 = explode("-", $wserori);

										//Consultar el centro de costos anterior
										$q_cco=" SELECT Ubisan
												   FROM ".$wbasedato."_000018
												 WHERE  Ubihis = '".$whis."'
												   AND  Ubiing = '".$wing."'
												";
										$res_cco = mysql_query($q_cco, $conex) or die (mysql_errno().$q_cco." - ".mysql_error());
										$row_cco = mysql_fetch_array($res_cco);

										// --> Consultar el patron de dieta segun nutricion
										$dsn = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
										$dsn = explode('-', $dsn);
										$dsn = $dsn[1];

										// ==================================================================================
										// Consulto si el paciente tiene programados pedidos de alimentacion para cancelarlos.
										// Se cancelan todos los servicios de pedido, posteriores al actual y se re-programan
										// Nuevamente pero con el centro de costos y habitacion actual .
										// 2012-07-11
										// ==================================================================================

										// --> Consulto todos los pedidos que tenga programados el paciente, mayores o iguales a hoy.
										$q_pedidos=" Select a.id, movfec, a.Fecha_data as fecha, a.Hora_data as hora, movhis, moving, movser, movdie, movhab, movind, movobs,"
												  ." 		movest, movobc, movval, movint, movpco, movmpo, movcan, movaut, movcco, Movpqu, Movods, Movdsn, Serhdi, Movnut "
												  ."  From ".$wbasedato."_000077 as a, ".$wbasedato."_000076 "
												  ." Where Movfec >= '".$wfecha."' "
												  ."   And Movhis = '".$whis."' "
												  ."   And Moving = '".$wing."' "
												  ."   And Movcco = '".trim($row_cco['Ubisan'])."' "
												  ."   And Movest = 'on' "
												  ."   And Movser = Sercod ";

										$res_pedidos = mysql_query($q_pedidos, $conex) or die (mysql_errno()."Query - ".$q_pedidos."(Consultar pedidos de alimenatcion para cancelar) ".mysql_error());
										$num_pedidos = mysql_num_rows($res_pedidos);
										if ($num_pedidos>0)
										{
											while($row_pedidos=mysql_fetch_array($res_pedidos))
											{
												$cancelar_pedido_cco_origen       = 'no';
												$fecha_pedido 					  = '0000-00-00';
												$Pedido							  = $row_pedidos['movdie'];
												$validar_si_existe_pedido		  = 'no';

												// --> Si es un pedido de hoy
												if($row_pedidos['fecha'] == $wfecha)
												{
													// --> No se han distribuido, entonces se programa para HOY nuevamente y para el nuevo centro de costos
													if($whora < $row_pedidos['Serhdi'])
													{
														$cancelar_pedido_cco_origen	= 'si';
														$fecha_pedido 				= $wfecha;
													}
													// --> Ya se distribuyeron, entonces se programa para MAÑANA y para el nuevo centro de costos, pero no se cancela el de hoy
													else
													{
														// --> solo si es para un patron DSN
														if(trim($row_pedidos['movdie']) == trim($dsn))
														{
															$fecha_pedido = date("Y-m-d", strtotime("$wfecha+ 1 day"));
															$validar_si_existe_pedido = 'si';
														}
													}

												}
												// --> Si es un pedido de mañana, entonces se programa para MAÑANA y para el nuevo centro de costos
												else
												{
													$cancelar_pedido_cco_origen   	 = 'si';
													$fecha_pedido 					 = $row_pedidos['fecha'];
												}

												if($Pedido!='')
												{

													if ($wcco!=$row_pedidos['movcco'])//Si es un traslado para otro cc (Otro servicio)
													{
														// 1- Cancelo el pedido de alimentacion
														if($cancelar_pedido_cco_origen == 'si')
														{
															$q_cancelar= " UPDATE ".$wbasedato."_000077 "
																		." SET Movest='off' "
																		." WHERE id='".$row_pedidos['id']."'";
															mysql_query($q_cancelar, $conex) or die (mysql_errno() . $q_cancelar . " - " . mysql_error());
														}

														if ($fecha_pedido != '0000-00-00')
														{
															if($validar_si_existe_pedido == 'si')
															{
																// --> Si ya existe algun pedido no debo insertar el nuevo pedido
																$q_existe_pe =" SELECT count(*) AS cantidad
																			      FROM ".$wbasedato."_000077
																		         WHERE Movfec = '".$fecha_pedido."'
																			       AND Movhis = '".$whis."'
																				   AND Moving = '".$wing."'
																				   AND Movser = '".$row_pedidos['movser']."'
																			";
																$res_existe_pe = mysql_query($q_existe_pe, $conex) or die (mysql_errno()."Query - ".$q_existe_pe."(Consultar si existe pedido de alimentacion) ".mysql_error());
																$row_existe_pe = mysql_fetch_array($res_existe_pe);
																if($row_existe_pe['cantidad'] > 0)
																	$grabar_pedido = 'no';
																else
																	$grabar_pedido = 'si';
															}
															else
															{
																$grabar_pedido = 'si';
															}

															if($grabar_pedido == 'si')
															{
																// 2- Inserto nuevamente el pedido pero con el CC y habitacion actual
																$q_nuevo = " INSERT INTO " . $wbasedato . "_000077 (   Medico       ,   	Fecha_data,    	Hora_data,     	Movfec,         		Movhis,                     Moving,                   Movhab,                Movser,    				  Movdie,		 			     Movind,				   Movobs,				      Movest,      		Movobc,                		Movval, 					  Movint,				      Movpco,						Movmpo,                      Movcan,                      Movaut,             Movcco,			Movpqu, 						Movods, 					Movdsn, 			Movnut,					Seguridad     ) "
																	."                                		  VALUES ('".$wbasedato ."','".$fecha_pedido."','".$whora."','".$fecha_pedido."','".$row_pedidos['movhis']."','".$row_pedidos['moving']."','".$whabdes1[0]."','".$row_pedidos['movser']."','".$row_pedidos['movdie']."','".$row_pedidos['movind']."','".$row_pedidos['movobs']."', 'on','".$row_pedidos['movobc']."','".$row_pedidos['movval']."','".$row_pedidos['movint']."','".$row_pedidos['movpco']."','".$row_pedidos['movmpo']."','".$row_pedidos['movcan']."','".$row_pedidos['movaut']."','".$wcco."','".$row_pedidos['Movpqu']."','".$row_pedidos['Movods']."','".$row_pedidos['Movdsn']."','".$row_pedidos['Movnut']."',	'C-".$wuser."')";
																mysql_query($q_nuevo, $conex) or die (mysql_errno() . $q_nuevo . " - " . mysql_error());
															}
														}

														// 3- Si el paciente tiene servicios individuales debo cancelar los productos de la 84

														//Consultar si tiene programados servicios individuales dentro del pedido de dieta
														$patron_con_productos='';
														if (strpos($Pedido,","))
														{
															$wpatron=explode(",",$Pedido);
															foreach($wpatron as $valor_patr)	//recorro todos los patrones
															{
																if (servicio_individual($valor_patr)) // Funcion que esta en movhos.inc.php
																	$patron_con_productos=$valor_patr;
															}
														}
														else
														{
															if (servicio_individual($Pedido))
																$patron_con_productos=$Pedido;
														}

														//Si tiene servicios individuales los debo cancelar
														if ($patron_con_productos!='')
														{
															//Consulto toda la informacion relacionada con el producto
															$q_productos="	SELECT id, Detfec, Detpro, Detcos, Detcan, Detcal, Detcla
																			  FROM ".$wbasedato."_000084
																			 WHERE Detfec = '".$row_pedidos['fecha']."'
																			   AND Dethis = '".$row_pedidos['movhis']."'
																			   AND Deting = '".$row_pedidos['moving']."'
																			   AND Detser = '".$row_pedidos['movser']."'
																			   AND Detcco = '".$row_pedidos['movcco']."'
																			   AND Detpat = '".$patron_con_productos."'
																			   AND Detest = 'on'
																			";
															$res_productos = mysql_query($q_productos, $conex) or die (mysql_errno()."Query - ".$q_productos."(Consultar servicios individuales) ".mysql_error());
															$num_productos = mysql_num_rows($res_productos);
															if ($num_productos>0)
															{
																for ($xx=0;$xx<$num_productos;$xx++)
																{
																	$row_productos=mysql_fetch_array($res_productos);

																	// 1- Cancelo el producto
																	if($cancelar_pedido_cco_origen == 'si')
																	{
																		$q_cancelar_pro = "  UPDATE ".$wbasedato."_000084 "
																						 ."    SET Detest = 'off' "
																						 ."  WHERE id = '".$row_productos['id']."'";
																		mysql_query($q_cancelar_pro,$conex) or die (mysql_errno()."En el query (Cancelar Servicios individuales)".$q_cancelar_pro." - ".mysql_error());
																	}

																	if ($fecha_pedido != '0000-00-00' && $grabar_pedido == 'si')
																	{
																		//Inserto nuevamente el registro pero con el nuevo cco
																		$q_insertar_pro = " INSERT INTO " . $wbasedato . "_000084 (		Medico, 		Fecha_data, 	 Hora_data, 		Detfec, 			Dethis, 						Deting, 				Detser, 						Detpat, 					Detpro, 						Detcos, 			 Detest, 		Detcan, 			   Detcco, 				Detcal, 					Detcla,     				Seguridad)
																															VALUES('".$wbasedato ."','".$fecha_pedido."','".$whora."','".$fecha_pedido."','".$row_pedidos['movhis']."','".$row_pedidos['moving']."','".$row_pedidos['movser']."','".$patron_con_productos."','".$row_productos['Detpro']."','".$row_productos['Detcos']."','on','".$row_productos['Detcan']."','".$wcco."','".$row_productos['Detcal']."','".$row_productos['Detcla']."','C-".$wuser."')
																						";
																		mysql_query($q_insertar_pro, $conex) or die (mysql_errno() ."(Insertar producto para el nuevo cco )". $q_insertar_pro . " - " . mysql_error());
																	}
																}
															}
														}

														// 4- Registro los movimientos en la tabla de auditoria

														// Registro el movimiento 'Cancelado por traslado' en la tabla de auditoria, esto es solo paa que quede la evidencia del traslado
														if($cancelar_pedido_cco_origen == 'si')
														{
															$q_registro = " INSERT INTO " . $wbasedato . "_000078 (   Medico       ,   Fecha_data,    			Hora_data,      				 Audhis,   		         Auding,                     Audser,                      Audacc,      Auddie,    			Audcco,    				Audusu,    Seguridad     ) "
																."                                		  VALUES ('".$wbasedato ."','".$row_pedidos['fecha']."','".$whora."','".$row_pedidos['movhis']."','".$row_pedidos['moving']."','".$row_pedidos['movser']."','CANCELADO POR TRASLADO','".$Pedido."',	'".$row_pedidos['movcco']."','".$wuser."','C-".$wuser."')";
															mysql_query($q_registro, $conex) or die (mysql_errno().$q_registro." - ".mysql_error());
														}

														if ($fecha_pedido != '0000-00-00' && $grabar_pedido == 'si')
														{
															// Registro el movimiento 'PEDIDO POR TRASLADO' en la tabla de auditoria, para que le aparezca al nuevo cc que recive el paciente
															$q_registro2 = " INSERT INTO " . $wbasedato . "_000078 (   Medico       ,   Fecha_data,    	Hora_data, 			      Audhis,   		         Auding,                     Audser,                      Audacc,     Auddie,	Audcco,		  Audusu,    Seguridad     ) "
																."                                		  VALUES ('".$wbasedato ."','".$fecha_pedido."','".$whora."','".$row_pedidos['movhis']."','".$row_pedidos['moving']."','".$row_pedidos['movser']."','PEDIDO POR TRASLADO','".$Pedido."','".$wcco."','".$wuser."','C-".$wuser."')";
															mysql_query($q_registro2, $conex) or die (mysql_errno().$q_registro2." - ".mysql_error());
														}
													}
													else 	//Si el traslado es para el mismo piso (el mismo cc), Solo actualizo la habitacion
													{
														//Actualizar habitacion
														$q_actualizar= " UPDATE ".$wbasedato."_000077 "
																	." 	    SET Movhab='".$whabdes1[0]."' "
																	."    WHERE id='".$row_pedidos['id']."'";
														mysql_query($q_actualizar, $conex) or die (mysql_errno() . $q_actualizar . " - " . mysql_error());
													}
												}
											}
										}

										// ==================================================================================

										//------------------------------------------------------------------
										//	--> REGRABAR CARGOS AMBULATORIOS, FACTURACION INTELIGENTE
										//		Jerson trujillo, 2014-04-08
										//------------------------------------------------------------------
										$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
										if($nuevaFacturacion == 'on')
										{
											$wuse = $wuser;
											regrabarCargosAmbulatorios($whis, $wing, trim($row_cco['Ubisan']), $wcco);
										}

										actualizarregistros($wid); //Aqui se actualiza el registro de la tabla cencam_000003 para la llegada, el cumplimiento y el realizado
									
										// --> 	Si el recibo es de un paciente que viene de urgencias: 
										//		Actualizar el ultimo detalle del kardex, en Kadare = 'off' (Aprobado por regente)
										// 		2015-05-26, Jerson Trujillo.
										
										if(es_urgencias($row_cco['Ubisan']))
										{
											$fechaAyer = strtotime('-1 day', strtotime(date("Y-m-d")));
											$fechaAyer = date('Y-m-d', $fechaAyer);

											// --> Consultar si el paciente tiene ordenes para la fecha actual o un dia anterior
											$sqlTieneKar = "
											SELECT Fecha_data, Karcco
											  FROM ".$wbasedato."_000053
											 WHERE Karhis 		= '".$whis."'
											   AND Karing 		= '".$wing."'
											   AND (Fecha_data	= '".date("Y-m-d")."' OR Fecha_data = '".$fechaAyer."')
										     ORDER BY Fecha_data DESC
											";
											$resTieneKar = mysql_query($sqlTieneKar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTieneKar):</b><br>".mysql_error());
											if($rowTieneKar = mysql_fetch_array($resTieneKar))
											{
												$fechaKardex 	= $rowTieneKar['Fecha_data'];
												$ccoKardex 		= trim($rowTieneKar['Karcco']);
												
												// --> Actualizar el parametro Kadare = 'off' del detalle del kardex.
												$sqlActPar = "
												UPDATE ".$wbasedato."_000054
												   SET Kadare = 'off'
												 WHERE Kadhis = '".$whis."'
												   AND Kading = '".$wing."'
												   AND Kadfec = '".$fechaKardex."'
												";
												mysql_query($sqlActPar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActPar):</b><br>".mysql_error());
											}
										}	
										
									}

									$wtipo_hab_fac_e = consultartipohab($conex, $whab, $wbasedato);
									$wtipo_hab_fac_r = consultartipohab($conex, $whabdes1[0],$wbasedato);

									// Aca grabo el encabezado de la entrega o recibo
									$q = " INSERT INTO " . $wbasedato . "_000017 (   Medico       ,   Fecha_data,   Hora_data,   Eyrnum     ,   Eyrhis  ,   Eyring  ,   Eyrsor  ,   Eyrsde         ,   Eyrhor  ,   Eyrhde         ,   Eyrtip   , Eyrest, Eyrids,  Eyrthe , Eyrthr, Seguridad     ) "
										."                                VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $whora . "','" . $wconsec . "','" . $whis . "','" . $wing . "','" . $wserori1[0] . "','" . $wccodes1[0] . "','" . $whab . "','" . $whabdes1[0] . "','" . $wtipo . "', 'on', '" . $wid . "'  , '".$wtipo_hab_fac_e."', '".$wtipo_hab_fac_r."', 'C-" . $wuser . "')";
									$err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());

									// Aca se graba el detalle del movimiento si lo hay
									if (isset($wnum_art) or (isset($j) and $j > 0))
									{
										if (!isset($wnum_art))
										$wcan_art = $j;
										else
										$wcan_art = $wnum_art;

										//averiguo que clase de centro de costos es el destino
										// Traigo el INDICADOR de si el centro de costo es hospitalario o No
										$q = " SELECT ccoapl "
											."   FROM " . $wbasedato . "_000011 "
											."  WHERE ccocod = '" . $wccodes1[0] . "'";
										$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
										$num = mysql_num_rows($res);
										if ($num > 0)
										{
											$row = mysql_fetch_array($res);
											if ($row[0] == "on")
											$wdesapl= "on";
											else
											$wdesapl = "off";
										}
										else
										{
											$wdesapl = "off";
										}


										//Averiguo que clase de centro de costos es el destino
										//Traigo el INDICADOR de si el centro de costo es hospitalario o No
										$q = " SELECT ccoapl "
											."   FROM ".$wbasedato."_000011 "
											."  WHERE ccocod = '".$wserori1[0]."'";
										$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
										$num = mysql_num_rows($res);
										if ($num > 0)
										   {
											$row = mysql_fetch_array($res);
											if ($row[0] == "on")
											   $woriapl= "on";
											  else
												$woriapl = "off";
										   }
										  else
											 {
											  $woriapl = "off";
											 }


										for ($i = 1;$i <= $wcan_art;$i++)
										{
											$q = " INSERT INTO ".$wbasedato."_000019 (   Medico       ,   Fecha_data,   Hora_data,   Detnum     ,   Detart             ,  Detcan            , Detest, Seguridad     ) "
												."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wconsec."','".$warr_art[$i][0]."',".$warr_art[$i][1].", 'on'  , 'C-".$wuser."')";
											$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

											$wctr = $warr_art[$i][1];     //Cantidad a trasladar
											$wronda = (string)date("H");
											buscar_articulo($warr_art[$i][0]);

											if ($went_rec == "Ent") // ******* ENTREGA *******
											{
												if($wdesapl=='off' and $woriapl=='on')
												{
													// =========================================================================================================================================
													// Aca hago el traslado de los saldos de la tabla 000030 a la 000004, si el centro de costo aplica automaticamente cuando se factura. Ej:UCI
													// =========================================================================================================================================
													$q = " SELECT spluen, splusa, splaen, splasa, splcco, Ccopap "
														."   FROM ".$wbasedato."_000030, ".$wbasedato."_000011 "
														."  WHERE splhis = '".$whis."'"
														."    AND spling = '".$wing."'"
														."    AND splart = '".$warr_art[$i][0]."'"
														."    AND (splcco = '".$wserori1[0]."'"      //2 de Mayo de 2008
														."     OR  splcco = ccocod "                 //2 de Mayo de 2008
														."    AND  ccotra = 'on') "                  //2 de Mayo de 2008
														."    Order by 6";
													$rest = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
													$num = mysql_num_rows($rest);

													for ($j = 1;$j <= $num;$j++)
													{
														$row = mysql_fetch_array($rest);

														$wuen = $row[0]; //Unix entradas
														$wusa = $row[1]; //Unix salidas
														$waen = $row[2]; //Aprovechamientos entradas
														$wasa = $row[3]; //Aprovechamientos salidas
														$wscc = $row[4]; //centro de costos que grabo

														if(($wuen-$wusa)>0)
														{
															if(($wuen-$wusa)<$wctr)
															  {
																$wcta=$wuen-$wusa;
																$wctr=$wctr-$wcta;
															  }
															 else
															   {
																$wcta=$wctr;
																$wctr=0;
															   }

															if($wctr<=0)
															  {
																$j=$num+1;
															  }

															if (($wuen-$wusa-$waen+$wasa) >= $wcta) // La cantidad en la 000030 es mayor a lo que se va a trasladar
															  {
																$q=  " SELECT id "
																	."   FROM ".$wbasedato."_000004 "
																	."  WHERE Spahis = '".$whis."' "
																	."    AND Spaing = '".$wing."' "
																	."    AND Spacco = '".$wscc."' "
																	."    AND Spaart = '".$warr_art[$i][0]."' ";
																$errs = mysql_query($q,$conex);
																$nums = mysql_num_rows($errs);

																if ($nums > 0)
																   {
																	$q = " UPDATE ".$wbasedato."_000004 "
																		."    SET spauen = spauen+ ".$wcta
																		."  WHERE spahis = '".$whis."'"
																		."    AND spaing = '".$wing."'"
																		."    AND spacco = '".$wscc."'"
																		."    AND spaart = '".$warr_art[$i][0]."'";
																	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
																   }
																  else
																	{
																	 $q=  " INSERT INTO ".$wbasedato."_000004 (   medico       ,    Fecha_data      ,    Hora_data       ,    Spahis  ,    Spaing ,    Spacco  ,    Spaart              ,   Spauen , Spausa, Spaaen, Spaasa, Seguridad     ) "
																		 ."                            VALUES ('".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$warr_art[$i][0] ."', ".$wcta.", 0     , 0     , 0     , 'A-".$wuser."') ";

																	 $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
																	}

																$q = " UPDATE ".$wbasedato."_000030 "
																	."    SET spluen = spluen-".$wcta
																	."  WHERE splhis = '".$whis."'"
																	."    AND spling = '".$wing."'"
																	."    AND splcco = '".$wscc."'"
																	."    AND splart = '".$warr_art[$i][0] . "'";
																$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

																AnularAplicacion($whis, $wing, $wcco, strtoupper($warr_art[$i][0]), $wartnom, $wcta, $wuser, 'off');

															  }

															if (($wuen - $wusa - $waen) < $wcta) // La cantidad en la 000030 es menor a lo que se va a trasladar
															{
																$q= " SELECT id "
																."      FROM ".$wbasedato."_000004 "
																."     WHERE Spahis	= '".$whis."' "
																."       AND Spaing	= '".$wing."' "
																."       AND Spacco	= '".$wscc."' "
																."       AND Spaart	= '".$warr_art[$i][0]."' ";
																$errs = mysql_query($q,$conex);
																$nums = mysql_num_rows($errs);

																if ($nums > 0)
																  {
																	$q = " UPDATE ".$wbasedato."_000004 "
																		."    SET spauen = spauen+".$wcta.", "
																		."        spaaen = spaaen+".($wcta - ($wuen - $wusa - $waen+ $wasa))
																		."  WHERE spahis = '".$whis."'"
																		."    AND spaing = '".$wing."'"
																		."    AND spacco = '".$wscc."'"
																		."    AND spaart = '".$warr_art[$i][0]."'";
																	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
																  }
																else
																   {
																	$q=  " INSERT INTO ".$wbasedato."_000004 (    medico,          Fecha_data,           Hora_data,            Spahis,          Spaing,             Spacco,            Spaart,   Spauen,   Spausa,   Spaaen,  Spaasa,         Seguridad) "
																		."                            VALUES ( '".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$warr_art[$i][0] ."', ".$wcta.", 0, ".($wcta - ($wuen - $wusa - $waen + $wasa)).", 0, 'A-".$wuser."')";

																	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
																   }


																$q = " UPDATE ".$wbasedato."_000030 "
																	."    SET spluen = spluen-".$wcta.", "
																	."        splaen = splaen-".($wcta - ($wuen - $wusa - $waen + $wasa))
																	."  WHERE splhis = '".$whis."'"
																	."    AND spling = '".$wing."'"
																	."    AND splcco = '".$wscc."'"
																	."    AND splart = '".$warr_art[$i][0] . "'";
																$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

																if (($wuen - $wusa - $waen + $wasa)>0)
																{
																	AnularAplicacion($whis, $wing, $wcco, strtoupper($warr_art[$i][0]), $wartnom, ($wuen - $wusa - $waen + $wasa), $wuser, 'off');
																}

																if (($wcta - ($wuen - $wusa - $waen + $wasa))>0)
																{
																	AnularAplicacion($whis, $wing, $wcco, strtoupper($warr_art[$i][0]), $wartnom, ($wcta - ($wuen - $wusa - $waen + $wasa)), $wuser, 'on');
																}
															}
														}
													}
												}
											}
											if ($went_rec == "Rec") // ******* RECIBO *******
											{
												if($wdesapl=='on' and $woriapl=='off')
												{
													// =========================================================================================================================================
													// Aca hago el traslado de los saldos de la tabla 000030 a la 000004, si el centro de costo aplica automaticamente cuando se factura. Ej:UCI
													// =========================================================================================================================================
													$q = " SELECT spauen, spausa, spaaen, spaasa, spacco, ccopap "
														."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
														."  WHERE spahis = '".$whis."'"
														."    AND spaing = '".$wing."'"
														."    AND spaart = '".$warr_art[$i][0]."'"
														."    AND Ccocod = Spacco "
														."    Order by 6";

													$rest = mysql_query($q, $conex) or die (mysql_errno().$q." - " . mysql_error());
													$num = mysql_num_rows($rest);
													for ($j = 1;$j <= $num;$j++)
													{
														$row = mysql_fetch_array($rest);

														$wuen = $row[0]; //Unix entradas
														$wusa = $row[1]; //Unix salidas
														$waen = $row[2]; //Aprovechamientos entradas
														$wasa = $row[3]; //Aprovechamientos salidas
														$wscc=  $row[4]; //centro de costos que grabo

														if(($wuen - $wusa)>0)
														{
															if(($wuen - $wusa)<$wctr)
															{
																$wcta=$wuen - $wusa;
																$wctr=$wctr-$wcta;
															}
															else
															{
																$wcta=$wctr;
																$wctr=0;
															}

															if($wctr<=0)
															{
																$j=$num+1;
															}

															//Si sigue vacio

															/************************************************************************
															 * Noviembre 15 de 2011
															 ************************************************************************/
															//Consulto la fraccion de articulo para poder hacer el insert en la tabla de aplicaciones
															$infoFraccion = consultarFraccion( $conex, $wbasedato, $wcenmez, $warr_art[$i][0] );
															/************************************************************************/

															if (($wuen - $wusa - $waen + $wasa) >= $wcta) // La cantidad en la 000030 es mayor a lo que se va a trasladar
															{

																$q=  " SELECT id "
																	."   FROM ".$wbasedato."_000030 "
																	."  WHERE Splhis = '".$whis."' "
																	."    AND Spling = '".$wing."' "
																	."    AND Splcco = '".$wscc."' "
																	."    AND Splart = '".$warr_art[$i][0]."' ";
																$errs = mysql_query($q,$conex);
																$nums = mysql_num_rows($errs);

																if ($nums > 0)
																{
																	$q = " UPDATE ".$wbasedato."_000030 "
																		."    SET spluen = spluen+". $wcta
																		."  WHERE splhis = '".$whis."'"
																		."    AND spling = '".$wing."'"
																		."    AND splcco = '".$wscc."'"
																		."    AND splart = '".$warr_art[$i][0] . "'";
																	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
																}
																else
																{
																	$q=  " INSERT INTO ".$wbasedato."_000030 (    medico,          Fecha_data,           Hora_data,            Splhis,          Spling,             Splcco,            Splart,   Spluen,   Splusa,   Splaen,  Splasa,         Seguridad) "
																		."                            VALUES ( '".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$warr_art[$i][0] ."', ".$wcta.", 0, 0, 0, 'A-".$wuser."')";

																	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
																}

																$q = " UPDATE ".$wbasedato."_000004 "
																	."    SET spauen = spauen-".$wcta
																	."  WHERE spahis = '".$whis."'"
																	."    AND spaing = '".$wing."'"
																	."    AND spacco = '".$wscc."'"
																	."    AND spaart = '".$warr_art[$i][0] . "'";
																$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

																$q = " INSERT INTO " . $wbasedato . "_000015 (       Medico       ,       Fecha_data ,       Hora_data ,       aplron    ,       aplhis  ,      apling   ,       aplcco  ,       aplart                         ,         apldes   ,       aplcan  ,    aplusu      , aplapr , aplest , aplapv ,       aplfec    ,                        aplufr,                                  apldos,  Seguridad        ) "
																. "                            		  VALUES ('" . $wbasedato . "','" . $wfecha . "' ,'" . $whora . "' ,'" . $wronda . "','" . $whis . "','" . $wing . "','" . $wcco . "','" . strtoupper($warr_art[$i][0]) . "','" . $wartnom . "'," . ($wcta) . ",'" . $wuser. "', 'off'  , 'on'   ,   'off','" . $wfecha . "', '".$infoFraccion['unidad']."', '".($infoFraccion['fraccion']*$wcta)."', 'C-" . $wuser . "') ";
																$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

															}
															if (($wuen - $wusa - $waen + $wasa) < $wcta) // La cantidad en la 000030 es mayor a lo que se va a trasladar
															{
																$q= " SELECT id "
																   ."   FROM ".$wbasedato."_000030 "
																   ."  WHERE Splhis	= '".$whis."' "
																   ."    AND Spling	= '".$wing."' "
																   ."    AND Splcco	= '".$wscc."' "
																   ."    AND Splart	= '".$warr_art[$i][0]."' ";
																$errs = mysql_query($q,$conex);
																$nums = mysql_num_rows($errs);

																if ($nums > 0)
																{
																	$q = " UPDATE ".$wbasedato."_000030 "
																		."    SET spluen = spluen+".$wcta.", "
																		."        splaen = splaen+".($wcta - ($wuen - $wusa - $waen + $wasa))
																		."  WHERE splhis = '".$whis."'"
																		."    AND spling = '".$wing."'"
																		."    AND splcco = '".$wscc."'"
																		."    AND splart = '".$warr_art[$i][0] . "'";
																	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
																}
																else
																{
																	$q= " INSERT INTO ".$wbasedato."_000030 (           medico,          Fecha_data,    Hora_data       ,    Splhis  ,    Spling ,    Splcco  ,    Splart             ,   Spluen , Splusa,   Splaen                                     ,  Splasa, Seguridad     ) "
																	   ."                            VALUES ( '".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$warr_art[$i][0]."', ".$wcta.", 0     , ".($wcta - ($wuen - $wusa - $waen + $wasa)).", 0      , 'A-".$wuser."')";

																	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
																}


																$q = " UPDATE ".$wbasedato."_000004 "
																	."    SET spauen = spauen-".$wcta. ", "
																	."        spaaen = spaaen-".($wcta - ($wuen - $wusa - $waen + $wasa))
																	."  WHERE spahis = '".$whis."'"
																	."    AND spaing = '".$wing."'"
																	."    AND spacco = '".$wscc."'"
																	."    AND spaart = '".$warr_art[$i][0] . "'";
																$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

																if (($wuen - $wusa - $waen+ $wasa)>0)
																{
																	$q = " INSERT INTO ".$wbasedato."_000015 (   Medico       ,   Fecha_data ,   Hora_data ,   aplron    ,   aplhis  ,  apling   ,   aplcco  ,   aplart                         ,   apldes     ,  aplcan                            ,   aplusu   , aplapr , aplest ,   aplapv,   aplfec    ,                        aplufr,                                                           apldos, Seguridad         ) "
																		."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$wronda."','".$whis."','".$wing."','".$wcco."','".strtoupper($warr_art[$i][0])."','".$wartnom."',".(($wuen - $wusa - $waen+ $wasa)).",'".$wuser."', 'off'  , 'on'   ,'off'    ,'".$wfecha."', '".$infoFraccion['unidad']."', '".($infoFraccion['fraccion']*($wuen - $wusa - $waen+ $wasa))."', 'C-" . $wuser . "') ";
																	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
																}

																if (($wcta - ($wuen - $wusa - $waen+ $wasa))>0)
																{
																	$q = " INSERT INTO " . $wbasedato . "_000015 (   Medico       ,   Fecha_data ,   Hora_data ,   aplron    ,   aplhis  ,  apling   ,   aplcco  ,   aplart                         ,   apldes     ,  aplcan                                      ,   aplusu   , aplapr , aplest , aplapv,   aplfec    ,                        aplufr,                                                                     apldos, Seguridad       ) "
																		."                                VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$wronda."','".$whis."','".$wing."','".$wcco."','".strtoupper($warr_art[$i][0])."','".$wartnom."',".(($wcta - ($wuen - $wusa - $waen+ $wasa))).",'".$wuser."', 'off'  , 'on'   , 'on'  ,'".$wfecha."', '".$infoFraccion['unidad']."', '".($infoFraccion['fraccion']*($wcta - ($wuen - $wusa - $waen+ $wasa)))."', 'C-".$wuser . "') ";
																	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
																}
															}
														}
													}
												}

												// =============================================================================
												// 2012-03-15
												// =============================================================================
												recibir_medicamentos($whis,$wing,$wserori1[0],$wccodes1[0],$warr_art[$i][0],$warr_art[$i][1],$woriapl);

											}
										}
									}	// Fin grabación de detalle
								}
								else{
									if( $went_rec == "Ent" ){
										$aviso = "FALTAN ARTICULOS POR APLICAR EN LAS ULTIMAS DOS RONDAS";
									}
									else{
										$aviso = "NO SE PUDO REALIZAR EL RECIBO, FALTAN ARTICULO POR APLICAR";
									}
									// $aviso = "DEBE SELECCIONAR SERVICIO Y HABITACION DESTINO ";
									$mostrar_grabar = false;
								}
							}
							else
							{
			       			  $aviso = "DEBE SELECCIONAR SERVICIO Y HABITACION DESTINO ";
							  $mostrar_grabar = false;
							}

							$wgrabo = "on";
							echo "<input type='HIDDEN' name='wgrabo' value='" . $wgrabo . "'>";
						}
						// ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						// ACA TERMINA EL PROCESO DE GRABACION
						// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						echo "<center><table border=0>";
						if ($went_rec == "Ent")
						{

							echo "<tr class=titulo>";
							echo "<td colspan=4 align=center>** ENTREGA ** DE PACIENTES CON SOLICITUD DE CAMA</td>";
							echo "</tr>";

							// Busco el Servicio Origen
							$q = " SELECT ubisac, cconom, ubihac "
								."   FROM ".$wbasedato."_000018, ".$wtabcco
								."  WHERE ubihis = '".$whis."'"
								."    AND ubiing = '".$wing."'"
								."    AND ubisac = ccocod ";
							$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num = mysql_num_rows($res);
							$row = mysql_fetch_array($res);

							$wserori = $row[0]."-".$row[1];
							$whabori = $row[2];
						}

						if ($went_rec == "Rec")
						{
							echo "<tr class=encabezadoTabla>";
							echo "<td colspan=4 align=center>** RECIBO ** DE PACIENTES</td>";
							echo "</tr>";
							// Busco el Servicio Origen
							$q = " SELECT ubisan, cconom, ubihan "
								."   FROM ".$wbasedato."_000018, ".$wtabcco
								."  WHERE ubihis = '".$whis."'"
								."    AND ubiing = '".$wing."'"
								."    AND ubisan = ccocod ";
							$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num = mysql_num_rows($res);
							$row = mysql_fetch_array($res);

							$wserori = $row[0]."-".$row[1];
							$whabori = $row[2];
							// Busco el Servicio Destino
							$q = " SELECT ubisac, cconom, ubihac "
								."   FROM ".$wbasedato."_000018, ".$wtabcco
								."  WHERE ubihis = '".$whis . "'"
								."    AND ubiing = '".$wing . "'"
								."    AND ubisac = ccocod ";

							$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num = mysql_num_rows($res);
							$row = mysql_fetch_array($res);

							$wserdes = $row[0]."-".$row[1];
							if (!isset($whabdes)) $whabdes = $row[2];
						}

						echo "<tr class=fila1>";
						echo "<td><b>Historia: </b>".$whis." - ".$wing."</td>";
						echo "<td><b>Paciente: </b>".$wpac."</td>";
						echo "<td><b>Habitación: </b>".$whab."</td>";
						echo "</tr>";

						echo "<tr class=fila1>";
						echo "<td>&nbsp</td>";
						echo "<td align=center><b>Origen</b></td>";
						echo "<td align=center><b>Destino Asignado</b></td>";
						echo "</tr>";

						echo "<tr class=fila1>";
						echo "<td align=left  ><b>Servicio: </b></td>";
						echo "<td align=center>".$wserori."</td>"; //Servicio Origen
						$wcco_ori = $row[0];

						switch ($went_rec)
						{
							// ********************
							case "Ent": // Entrega de pacientes
								// ================================================================================================
								// Aca traigo el centro de costo DESTINO si lo tiene. Si no pongo la lista de servicios disponibles
								// ================================================================================================
								//Consulto el centro de costos donde debe ser entregado el paciente
                                $q =     "  SELECT id, Hab_asignada  "
                                        ."    FROM ".$wcencam."_000003"
                                        ."   WHERE id = ".$wid."";
                                $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
                                $num = mysql_num_rows($res);
                                $row = mysql_fetch_array($res);
                                $whab_asignada = $row['Hab_asignada'];
                                $whabdes = $row['Hab_asignada'];
                                $wid = $row['id'];

                                //Consulto el centro de costos donde debe ser entregado el paciente
                                $q =     "  SELECT habcco "
                                        ."    FROM ".$wbasedato."_000020"
                                        ."   WHERE Habcod = '".$whab_asignada."'"
                                        ."     AND Habest = 'on'";
                                $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
                                $num = mysql_num_rows($res);
                                $row = mysql_fetch_array($res);
                                $wcoo_asignado = $row['habcco'];
                                $wccodes = $wcoo_asignado;


								$q = " SELECT  ".$wbasedato."_000011.ccocod, ".$wtabcco.".cconom "
									."   FROM  ".$wbasedato."_000011, ".$wtabcco
									."  WHERE (".$wbasedato."_000011.ccohos = 'on' "
									."	   OR  ".$wbasedato."_000011.ccocir = 'on') "
									."    AND  ".$wbasedato."_000011.ccoest = 'on' "
									."    AND  ".$wbasedato."_000011.ccocod = ".$wtabcco.".ccocod "
									."    AND  ".$wbasedato."_000011.ccocod = '".$wcoo_asignado."'"
									."    ORDER BY Ccoord, Ccocod ";   //AGREGADO
								$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								$num = mysql_num_rows($res);

								// Creo la variable que define si se muestra el boton de Grabar entrega
								if(!isset($mostrar_grabar)) $mostrar_grabar = true;
								if(!isset($aviso)) $aviso = "";

								echo "<input type='HIDDEN' id='wid' name='wid' value='" . $wid . "'>";
                                echo "<input type='HIDDEN' id='widsolicitud' name='wid' value='".$wid."'>";
								echo "<td align=center colspan=2 bgcolor=FFFFCC><select name='wccodes'>";

								if ($num > 1) // Si hay mas de un centro de costo, muestro el campo en blanco por defecto
								  if (isset($wccodes))
									 echo "<option>".$wccodes."</option>";
									else
									   echo "<option> </option>";

								for ($i = 1;$i <= $num;$i++)
								{
									$row = mysql_fetch_array($res);
									echo "<option>" . $row[0] . "-" . $row[1] . "</option>";
								}
								echo "</select></td>";
								// ================================================================================================
								echo "</tr>";

								echo "<tr class=fila1>";
								echo "<td align=left  ><b>Habitación: </b></td>";
								echo "<td align=center>".$whabori."</td>";
								// ================================================================================================
								// Aca traigo la habitación DESTINO si la tiene. Si no pongo la lista de habitaciones disponibles
								// ================================================================================================
								if (isset($wccodes) && $wccodes!="" && $wccodes!=" ")
								{
									$wccod = explode("-", $wccodes);

									$q = " SELECT habcod "
										."   FROM ".$wbasedato."_000020 "
										."  WHERE habdis  = 'on' "
										."    AND habali != 'on' "
										."    AND habcco  = '".$wccod[0]."'"
										."    AND habcod  = '".$whab_asignada."'"
										."    AND habest  = 'on'"
										." ORDER BY Habord, Habcod	";
									$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$num = mysql_num_rows($res);

									echo "<td align=center colspan=2> &nbsp;&nbsp; ";
									if($num>0 && !es_cirugia_o_urgencias($wccod[0]))
									{
										echo "<select name='whabdes'>";

										for ($i = 1;$i <= $num;$i++)
										   {
											$row = mysql_fetch_array($res);
											if ($whabdes == $row[0])
											   echo "<option selected>" . $row[0] . "</option>";
											  else
                                               echo "<option>" . $row[0] . "</option>";
										   }
										echo "</select>";
									}
									elseif($num==0 && !es_cirugia_o_urgencias($wccod[0]))
									{
										if(isset($wgrabo) && $wgrabo=="on")
											$aviso = "";
										else
											$aviso = "NO HAY HABITACIONES DISPONIBLES";
										$mostrar_grabar = false;
									}
									else
									{
										echo "&nbsp;";
									}
									echo " &nbsp;&nbsp; </td>";

									// Si es cirugia o urgencias y hay articulos pendientes se muestra aviso y se oculta el boton de grabar
									if(es_cirugia_o_urgencias($wccod[0]) && isset($wnum_art) && $wnum_art > 0)
									{
										$aviso = "DEBE HACER DEVOLUCION DE MEDICAMENTOS ANTES DE HACER EL TRASLADO";
										$mostrar_grabar = false;
									}

								}
								else
								{
									echo "<td align=center colspan=2>&nbsp;</td>";
									$mostrar_grabar = false;
								}
								// ================================================================================================
								echo "</tr>";

								// Muestro aviso si lo hay
								if(isset($aviso) && $aviso!="")
									echo "<tr><td colspan='3'><br /><div align='center' class='fondoRojo' style='width:100%'><b><blink> ".$aviso." </blink></b></div></td></tr>";

							break;

							case "Rec": // Recibo de pacientes
								// ================================================================================================
								// Aca traigo el centro de costo DESTINO si lo tiene. Si no pongo la lista de servicios disponibles
								// ================================================================================================
                                //Consulto el centro de costos donde debe ser entregado el paciente
                                $q =     "  SELECT id, Hab_asignada  "
                                        ."    FROM ".$wcencam."_000003"
                                        ."   WHERE id = ".$wid."";
                                $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
                                $num = mysql_num_rows($res);
                                $row = mysql_fetch_array($res);
                                $whab_asignada = $row['Hab_asignada'];
                                $whabdes = $row['Hab_asignada'];
                                $wid = $row['id'];

                                //Consulto el centro de costos donde debe ser entregado el paciente
                                $q =     "  SELECT habcco "
                                        ."    FROM ".$wbasedato."_000020"
                                        ."   WHERE Habcod = '".$whab_asignada."'"
                                        ."     AND Habest = 'on'";
                                $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
                                $num = mysql_num_rows($res);
                                $row = mysql_fetch_array($res);
                                $wcoo_asignado = $row['habcco'];
                                $wccodes = $wcoo_asignado;


								$q = " SELECT ".$wbasedato."_000011.ccocod, ".$wtabcco.".cconom "
									."   FROM ".$wbasedato."_000011, " . $wtabcco
									."  WHERE ".$wbasedato."_000011.ccohos = 'on' "
									."    AND ".$wbasedato."_000011.ccoest = 'on' "
									."    AND ".$wbasedato."_000011.ccocod = ".$wtabcco.".ccocod "
                                    ."    AND  ".$wbasedato."_000011.ccocod = '".$wcoo_asignado."'"
									."    ORDER BY Ccoord, Ccocod ";  //AGREGADO
								$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								$num = mysql_num_rows($res);

                                echo "<input type='HIDDEN' id='wid' name='wid' value='" . $wid . "'>";
                                echo "<input type='HIDDEN' id='widsolicitud' name='wid' value='".$wid."'>";
								echo "<td align=center colspan=2 bgcolor=FFFFCC><select name='wccodes' value='".$wserdes."'>";

								if ($num > 1) // Si hay mas de un centro de costo, muestro el campo en blanco por defecto
								{
									if ($wserdes != "")
									$wccodes = $wserdes;

									if (isset($wccodes))
										echo "<option>".$wccodes."</option>";
									else
										echo "<option> </option>";
								}

								for ($i = 1;$i <= $num;$i++)
								   {
									$row = mysql_fetch_array($res);
									echo "<option>".$row[0]."-".$row[1]."</option>";
								   }
								echo "</select></td>";
								// ================================================================================================
								echo "</tr>";

								echo "<tr class=fila1>";
								echo "<td align=left  ><b>Habitación: </b></td>";
								echo "<input type='HIDDEN' id='wid' name='wid' value='" . $wid. "'>";
								echo "<td align=center>".$whabori."</td>"; //Habitacion Origen
								// ================================================================================================
								// Aca traigo la habitación DESTINO si la tiene. Si no pongo la lista de habitaciones disponibles
								// ================================================================================================
								if (isset($wccodes))
								{
									$wccod = explode("-", $wccodes);

									$q = " SELECT habcod "
										."   FROM ".$wbasedato."_000020 "
										."  WHERE habdis  = 'on' "
										."    AND habcco  = '".$wccod[0]."'"
										."    AND habcod  = '".$whab_asignada."'"
										."    AND habest  = 'on'"
										." ORDER BY Habord, Habcod	";
									$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$num = mysql_num_rows($res);

									echo "<td align=center colspan=2> &nbsp;&nbsp; <select name='whabdes'>";

									if (isset($whabdes))
									echo "<option>".$whabdes."</option>";

									for ($i = 1;$i <= $num;$i++)
									  {
										$row = mysql_fetch_array($res);

										if ($whabdes == $row[0])
										   echo "<option selected>".$row[0]."</option>";
										  else
											 echo "<option>".$row[0]."</option>";
									  }
									echo "</select> &nbsp;&nbsp; </td>";
								}
								else
								{
									echo "<td align=center colspan=2> &nbsp;&nbsp; <select name='whabdes' onchange='enter()'>";
									echo "<option></option>";
									echo "</select> &nbsp;&nbsp; </td>";
								}
								// ================================================================================================
								echo "</tr>";

								// 2012-04-09
								// Muestro aviso si lo hay
								if(isset($aviso) && $aviso!="")
									echo "<tr><td colspan='3'><br /><div align='center' class='fondoRojo' style='width:100%'><b><blink> ".$aviso." </blink></b></div></td></tr>";

							break;
						}

						echo "</table>";
						echo "<br>";
						echo "<HR align=center></hr>";

						$wserori1 = explode("-", $wserori);
						//averiguo que clase de centro de costos origen aplica automaticamente
						$q = " SELECT ccoapl "
							."   FROM ".$wbasedato."_000011 "
							."  WHERE ccocod = '".$wserori1[0]."'";
						$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);
						if ($num > 0)
						  {
							$row = mysql_fetch_array($res);
							if ($row[0] == "on")
							   $woriapl= "on";
							  else
							     $woriapl = "off";
						  }
						else
						   {
							$woriapl = "off";
						   }

						if ($wccoapl == "off" or $went_rec == "Rec")
						{
							echo "<center><table>";
							if ($wccoapl == "off" or $woriapl=='off')
							{
								echo "<th align=center class=titulo>DETALLE DE ARTICULOS</th>";
								Detalle_ent_rec('NoApl'); //En esta funcion se muestran todos los articulos que tiene saldo
							}
							else
							{
								echo "<th align=center bgcolor=000066><font text=4 color=ffffff><b>DETALLE DE ARTICULOS</b></font></th>";
								Detalle_ent_rec('Apl'); //En esta funcion se muestran todos los articulos que se entregaron a otro servicio
							}
							echo "</table>";
							echo "<HR align=center></hr>";

						}
						else
						{
							if ($wccoapl == "on" and $went_rec == "Ent")
							{
								echo "<center><table>";
								echo "<tr class=titulo align=center><td><INPUT TYPE='submit' NAME=wdetallar VALUE='Detallar Articulos a Entregar'></td></tr>";
								echo "</table>";
								echo "<HR align=center></hr>";
							}
						}

						if (!isset($wgrabo)) // Esto lo hago para que cuando grabe se desaparezca la opcion de volver a grabar
						{
							switch ($went_rec)
							{
							  case "Ent":
								if ($wccoapl == "on" and isset($wdetallar))
								{
									echo "<center><table>";
									if ($wdetallar == "Detallar Articulos a Entregar")
									{
                                    ?>
     								<script>
     								function ira(){document.ent_rec_pac.warticulo.focus();}
     								</script>
   									<?php

   									echo "<tr class=fila1>";
   									if (!isset($warticulo))
   									  {
   										echo "<td colspan=4 align=center><b>Medicamento e Insumo :</b><INPUT TYPE='text' NAME='warticulo' SIZE=10></td>";
   									  }
   									 else
   									   {
   										echo "<td colspan=4 align=center><b>Medicamento e Insumo :</b><INPUT TYPE='text' NAME='warticulo' SIZE=10 VALUE='" . strtoupper($warticulo) . "'></td>";
   									   }
   									echo "</tr>";

   									if (isset($warticulo) and $warticulo != "")
   									  {
   										buscar_articulo($warticulo);
   										if ($wok == "off")
   										  {
                                           ?>
		     								<script>
		     								alert ("EL ARTICULO NO EXISTE");
            								 </script>
  		   								   <?php
   										  }
   									  }

   									if (!isset($wok)) $wok = "off";

   									if (!isset($wcantidad) and isset($warticulo) and $wok == "on" and ($warticulo != ""))
   									  {
   										$j = $j + 1;
   										$warr_art[$j][0] = strtoupper($warticulo);

                                        ?>
             							 <script>
             							  function ira(){document.ent_rec_pac.wcantidad.focus();}
             							  function ira(){document.ent_rec_pac.wcantidad.select();}
             							 </script>
           								<?php

           								echo "<td bgcolor=660099 colspan=4 align=center><font color=ffffff><b>Cantidad :</b></font><INPUT TYPE='text' NAME='wcantidad' SIZE=10 VALUE=1></td>";
           								$wart = strtoupper($warticulo);

           								$warticulo = "";
           								echo "<input type='HIDDEN' name='warticulo' value='" . strtoupper($warticulo) . "'>";
           								echo "<input type='HIDDEN' name='wart' value='" . $wart . "'>";
           								echo "</tr>";
       								  }

   									if (isset($wcantidad))
   									  {
   										if ($wcantidad<=0)
   										   {
   											$j--;

                                    		?>
		       								<script>
		       								alert ("LA CANTIDAD DEBE SER UN NUMERO POSITIVO");
               								</script>
  		     								<?php
   										   }
   										  else
   										   {
   											if(is_numeric($wcantidad))
   											  {
   												verificar_saldo($wart, $wccoapl, $warr_art);

   												if ($wok == "on")
   												   $warr_art[$j][1] = $wcantidad;
   												  else
   												     {
   													  $j--;

                                    				  ?>
		       										   <script>
		       										     alert ("EL ARTICULO NO TIENE PENDIENTE DE APLICAR O NO SE LE FACTURO ESA CANTIDAD");
               										   </script>
  		     										  <?php
   												     }
   											  }
   											 else
   											   {
   												  $j--;

                                    			  ?>
		       										<script>
		       										alert ("LA CANTIDAD DEBE SER UN NUMERO POSITIVO");
               										</script>
  		     									  <?php
   											   }
   										   }
   									   }
									}
									echo "</table>";

									echo "<input type='HIDDEN' name='wdetallar' value='" . $wdetallar . "'>";
									if (isset($j))
									{
										echo "<input type='HIDDEN' name='j' value='" . $j . "'>";
										for ($i = 1;$i <= $j;$i++)
										{
											if (isset($warr_art[$i][0])) echo "<input type='HIDDEN' name='warr_art[" . $i . "][0]' value='" . $warr_art[$i][0] . "'>";
											if (isset($warr_art[$i][1])) echo "<input type='HIDDEN' name='warr_art[" . $i . "][1]' value='" . $warr_art[$i][1] . "'>";
										}
									}

									echo "<br><br>";

									if (isset($j) and $j >= 1 and $wdetallar == "Detallar Articulos a Entregar")
									{
										echo "<center><table border=0>";
										echo "<tr class=encabezadoTabla>";
										echo "<td>Articulo</td>";
										echo "<td>Descripción</ftd>";
										echo "<td>Presentación</td>";
										echo "<td>Cantidad</td>";
										for ($i = 1;$i <= $j;$i++)
										{
											if ($i % 2 == 0)
												$wcolor = "66FFFF";
											else
												$wcolor = "CCCCFF";

											buscar_articulo($warr_art[$i][0]);

											echo "<tr>";
											echo "<td bgcolor=" . $wcolor . ">" . $warr_art[$i][0] . "</td>";
											echo "<td bgcolor=" . $wcolor . ">" . $wartnom . "</td>";
											echo "<td bgcolor=" . $wcolor . ">" . $wartuni . " - " . $wunides . "</td>";
											if (isset($warr_art[$i][1])) echo "<td bgcolor=" . $wcolor . " align=center>" . $warr_art[$i][1] . "</td>";
											echo "</tr>";

											unset($wcantidad);
										}
										echo "</table>";
									}
								}

								if($mostrar_grabar)
								{
									echo "<tr>";
									echo "<td align=center bgcolor=#cccccc colspan=7><input type='checkbox' name='wgrabar'><input type='submit' value='Grabar Entrega'></td>";
									echo "</tr>";
								}
							  break;

							  case "Rec":

								echo "<tr>";
								echo "<td align=center bgcolor=#cccccc colspan=7><input type='checkbox' name='wgrabar'><input type='submit' value='Grabar Recibo'></td>";
								echo "</tr>";
								?>
								<script>
								setTimeout("document.forms.ent_rec_pac.submit()", 30000);
								</script>
								<?php

							  break;
							}
						}

						echo "<input type='HIDDEN' name='wcco'     value='" . $wcco . "'>";
						if (isset($j)) echo "<input type='HIDDEN' name='j'        value='" . $j . "'>";
						echo "<input type='HIDDEN' name='wnomcco'  value='" . $wnomcco . "'>";
						echo "<input type='HIDDEN' name='wtabcco'  value='" . $wtabcco . "'>";
						echo "<input type='HIDDEN' name='wccohos'  value='" . $wccohos . "'>";
						echo "<input type='HIDDEN' name='wccoapl'  value='" . $wccoapl . "'>";
						echo "<input type='HIDDEN' name='whis'     value='" . $whis . "'>";
						echo "<input type='HIDDEN' name='wing'     value='" . $wing . "'>";
						echo "<input type='HIDDEN' name='wpac'     value='" . $wpac . "'>";
						echo "<input type='HIDDEN' name='whab'     value='" . $whab . "'>";
						echo "<input type='HIDDEN' name='went_rec' value='" . $went_rec . "'>";
						echo "<input type='HIDDEN' name='wserori'  value='" . $wserori . "'>";
						echo "<input type='HIDDEN' id='wid' name='wid' value='" . $wid. "'>";
					} //Fin del if que verifica que la historia exista
					else
					{

                    ?>
					  <script>
					  alert ("LA HISTORIA DIGITADA NO EXISTE EN ESTE SERVICIO");
					  </script>
					<?php
					}
				} //fin del then del if (isset($whis) and trim($whis!=""))
			}
			echo "<center><table>";
			if (!isset($j))
			$j = 0;

			echo "<input type='HIDDEN' name='j' value='" . $j . "'>";

			echo "<tr>";
			echo "<td align=center colspan=7><A href='Ent_y_Rec_Pac.php?wcco=" . $wcco . "&amp;wtabcco=" . $wtabcco . "&amp;wbasedato=" . $wbasedato . "&amp;wemp_pmla=" . $wemp_pmla . "'><b>Retornar</b></A></td>";
			echo "</tr>";
			echo "</table>";
		}
	} //Fin del Else de Centro de Costo o Servicio
} // if de register
echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";
echo "<input type='HIDDEN' name='wbasedato' value='" . $wbasedato . "'>";
echo "<input type='HIDDEN' name='cencam' value='" . $wcencam . "'>";
if (isset($wcco)) echo "<input type='HIDDEN' name='wcco' value='" . $wcco . "'>";
echo "</form>";

echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
unset($wano);
unset($wmes);
unset($wdia);
unset($wcco);

include_once("free.php");

?>
</body>
</html>
