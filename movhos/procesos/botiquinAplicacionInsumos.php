<?php
include_once("conex.php");

header("Content-Type: text/html;charset=ISO-8859-1");

include_once("root/comun.php");
include_once("movhos/botiquin.inc.php");
include_once("citas/funcionesAgendaCitas.php");
// 


$pos		= strpos($user,"-");
$wusuario	= substr($user,$pos+1,strlen($user));

$wuser1		= explode("-",$user);
$wusuario	= trim($wuser1[1]);

//Comentar..................
// $wusuario	= "0103727";

$wbasedato	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$whce		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
$wtalhuma	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "talhuma" );

/**************************************************************************************************************
 * BOTIQUIN - DISPENSACION DE INSUMOS
 *
 * Fecha de creación:	2017-05-10
 * Por:					Edwin Molina Grisales
 **************************************************************************************************************/
/**************************************************************************************************************
 * DESCRIPCION:
 *
 * Permite a la auxiliar de enfermería aplicar insumos de acuerdo a lo que tiene cargado a su nombre desde el
 * botiquin.
 **************************************************************************************************************/

 /**************************************************************************************************************
 * ACTUALIZACIONES:
 *
 * Julio 11 de 2019 	Edwin MG	Se inicialia variable como array en la función consultarPacientePorFiltro (val[data] = [])
 * Diciembre 7 de 2017 	Edwin MG	Se corrige validación al aplicar la lista de insumos. El campo cantidad puede ser vacio o 0 y no se tiene en cuenta al aplicar el articulo.
 *									Antes de este cambio si el campo cantidad era 0 sacaba un mensaje que decía que la cantidad a aplicar debe ser mayor a 0 para aplicar pero si el campo
 *									cantidad era vacio se ignoraba el articulo para aplicar.
 * Noviembre 15 de 2017 Edwin MG	Se hacen cambios varios para que se puedan aplicar los insumos de cirugía
 * Octubre 30 de 2017.	EDWIN MG. 	A la información del paciente se le agrega si este tiene cita o no para un cco
 * Octubre 23 de 2017.	EDWIN MG. 	Al dar clic sobre aplicar articulo se deshabilita para que no se puede volver a dar clic
 * Junio 27 de 2017.	EDWIN MG. 	En la funcion consultarPacientePorFiltro se le quita en el query la validacion de que el cco sea de hospitalización (ccohos = 'on')
 * Junio 23 de 2017.	EDWIN MG. 	Se permite aplicar insumos con saldo negativos si está permitido en la configuración de articulos especiales (movhos_000008)
 * Junio 21 de 2017.	EDWIN MG. 	Se muestra los pacientes que tengan saldo así esten de alta.
 * Junio 13 de 2017.	EDWIN MG. 	Se agrega fecha a la nota aclaratoria por anulación de aplicación para HCE.
 * Junio 6 de 2017.		EDWIN MG. 	Se valida que insumo requiera justificación por el cco por el cuál se dispensa,
 *									en caso contrario se se busca por el cco *
 **************************************************************************************************************/



/****************************************************************************************************************
 * 												FUNCIONES
 ****************************************************************************************************************/

function consultarTablaCitas( $conex, $wbasedato, $cco ){

	$tabla = "";

	$q = "SELECT Ccopci, Ccontc
			FROM ".$wbasedato."_000011
		   WHERE ccocod = '".$cco."'; ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	if( $fila = mysql_fetch_array($res) ){
		$tabla = $fila['Ccopci']."_".$fila['Ccontc'];
	}

	return $tabla;
}


// Función que consulta en la root_000117 los datos del centro de costo
// return $row array con los datos del centro de costo
function getInfoModeloAtencionPorCCo( $conex, $cco )
{
	$sql ="SELECT *
	         FROM root_000117
			WHERE centroCosto = '".$cco."'";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );

	$row = $res ? mysql_fetch_assoc($res) : array();

	return $row;
}

function esPacienteDeAyudaDxParaPedidos( $conex, $wemp_pmla, $wbasedato, $his, $ing, $cco ){

	$val = false;

	$wbasedatoCliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );

	$row = getInfoModeloAtencionPorCCo( $conex, $cco );

	if( count( $row ) > 0 ){

		$prefijoCampos = getPrefixTables($row['nombreCc'] );

		$tablaAyudasDiagnosticas = consultarTablaCitas( $conex, $wbasedato, $cco );
		$prefijoTablaAyudasDiagnosticas = $row['nombreCc'];

		if( !empty( $prefijoCampos ) && !empty($tablaAyudasDiagnosticas) ){

			$queryHabitaciones =  "SELECT ".$prefijoCampos."tip,".$prefijoCampos."doc,".$prefijoCampos."his,".$prefijoCampos."ing,CONCAT_WS(' ',Pacno1,Pacno2,Pacap1,Pacap2) AS nombre,Ubihis,Ubiing
									FROM ".$tablaAyudasDiagnosticas." a,".$prefijoTablaAyudasDiagnosticas."_000023 b,".$wbasedatoCliame."_000100,".$wbasedatoCliame."_000101,".$wbasedato."_000018
									WHERE Fecha='".date("Y-m-d")."'
									 AND Activo='A'
									 AND Causa=''
									 AND ".$prefijoCampos."doc=Cedula
									 AND b.Fecha_data=Fecha
									 AND ".$prefijoCampos."est='on'
									 AND ".$prefijoCampos."fpr!='on'
									 AND ".$prefijoCampos."acp!=''
									 AND Pactdo=".$prefijoCampos."tip
									 AND Pacdoc=".$prefijoCampos."doc
									 AND inghis=Pachis
									 AND Ubihis=Inghis
									 AND Ubiing=Ingnin
									 AND Ubiald!='on'
									 AND Ubihis='".$his."'
									 AND Ubiing='".$ing."'
									 ;";

			$res = mysql_query( $queryHabitaciones, $conex ) or die( mysql_error()." - Error en el query $queryHabitaciones -".mysql_error() );
			$num = mysql_num_rows( $res );

			if( $num > 0 ){
				$val = true;
			}
		}
	}

	return $val;

}

function consultarLoteArticulo( $conex, $wcliame, $art, $turno ){
	
	$val = false;
	
	if( $turno > 0 ){
		
		$sql = "SELECT Lotlot
				  FROM ".$wcliame."_000240
				 WHERE lottur = '".$turno."'
				   AND lotins = '".$art."'
				   AND lotest = 'on'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$val = $rows['Lotlot'];
		}
	}
	
	return $val;
}
 
/****************************************************************************************************************
 * Consulta el nombre del paciente por historia, habitación, nro de cedula o nombre según el parametro filtro
 ****************************************************************************************************************/
function consultarCodigosBarra( $conex, $wbasedato, $art ){
	
	$val = array();	

	$sql = "SELECT Artcba
			  FROM ".$wbasedato."_000009
			 WHERE Artcod = '".$art."'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	
	$val = array(
		"$art" => $art,
	);
	
	if( $res ){
		
		while( $rows = mysql_fetch_array( $res ) ){
			$rows['Artcba'] = trim( $rows['Artcba'] );
			$val[ utf8_encode( $rows['Artcba'] ) ] = $art;
		}
	}
	
	return $val;
}
 
/********************************************************************************************************
 * Carga a facturacion y aplica un articulo al paciente
 ********************************************************************************************************/
function desaplicarArticuloTurnoCirugia( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $insumo, $cantidad, $unidadArticulo, $ccoori, $ccodes, $habitacion, $historia, $ingreso, $justificacion, $turnocx = 0 ){

	$val = array(
		'error' 		=> 0,
		'message' 		=> '',
		'data' 			=> '',
		'conexionUnix' 	=> false,
	);
	
	$encabezadoAct = actualizarEncabezadoCargoInsumoDesaplicacion( $conex, $wbasedato, $ccoori, $wauxiliar, $insumo, $cantidad, $historia, $ingreso, $turnocx );

	if( !empty($encabezadoAct['data']['insumos']) ){

		foreach( $encabezadoAct['data']['insumos'] as $key => $value ){

			if( $value != 'totalDesaplicada' ){

				$wmovimiento = 'AA';	//Es una aplicacion anulada
				$fechaEncabezado = $value['fecha'];
				$can = $value['cantidad'];
				$val['data']['auditoria'] = registrarAuditoriaCargoInsumo( $conex, $wbasedato, $ccoori, $wauxiliar, $insumo, $fechaEncabezado, $wmovimiento, $historia, $ingreso, $ccodes, $habitacion, -1*$can, $wauxiliar, $justificacion, 'off', '', '', '', $turnocx );

				$val['data']['numeroDocumento'] = $dronum;
				$val['data']['numeroLinea'] 	= $drolin;
				$val['data']['fechaAplicacion'] = $fecApl;
				$val['data']['rondaAplicacion'] = $ronApl;
				$val['data']['horaAplicacion'] 	= date( "H:i:s" );
			}
		}
	}

	return $val;
}

/****************************************************************************************************************
 * Consulta el nombre del paciente por historia, habitación, nro de cedula o nombre según el parametro filtro
 ****************************************************************************************************************/
function consultarPacientePorUbicacion( $conex, $wbasedato, $wemp_pmla, $his, $ing, $turnocx=0){

	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array(),
	);

	$wbasedato_tcx    = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tcx");
	$wbasedato_cliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion");

	if($turnocx>0)
	{
		$sql = "SELECT  tcx11.Turhis AS Ubihis, tcx11.Turnin AS Ubiing, m18.Ubisac, mx11.Cconom, CONCAT( 'Qx ', Turqui ) as Ubihac, m18.Ubihan, c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2, c100.Pactdo AS Oritid, c100.Pacdoc AS Oriced, m18.Ubimue, m18.Ubiald, m18.Ubialp, Turcir, Turnom
				FROM    {$wbasedato_tcx}_000011 AS tcx11
				        left join
				        {$wbasedato_cliame}_000100 AS c100 ON (tcx11.Turhis=c100.Pachis)
				        left join
				        {$wbasedato_cliame}_000101 AS c101 ON (c100.Pachis=c101.Inghis and tcx11.Turnin=c101.Ingnin)
				        left join
				        {$wbasedato}_000018 as m18 ON (Ubihis=c101.Inghis and m18.Ubiing=c101.Ingnin and m18.Ubihis != '')
				        left join
				        {$wbasedato_tcx}_000012 AS tc12 ON (tcx11.Turqui=tc12.Quicod)
				        left join
				        {$wbasedato}_000011 AS mx11 ON (tc12.Quicco=mx11.Ccocod)
				WHERE tcx11.Turtur='{$turnocx}'";
	}
	else
	{
		$sql = "SELECT  Ubihis, Ubiing, Ubisac, Cconom, Ubihac, Ubihan, Pacno1, Pacno2, Pacap1, Pacap2, Oritid, Oriced, Ubimue, Ubiald, Ubialp, '' as Turcir, '' as Turnom
				  FROM ".$wbasedato."_000018 a , ".$wbasedato."_000011 b, root_000037 c, root_000036 d
				 WHERE a.Ubisac =  b.ccocod
				   AND c.orihis =  a.Ubihis
				   AND c.oriing =  a.Ubiing
				   AND d.pacced =  c.oriced
				   AND d.pactid =  c.oritid
				   AND a.Ubihis != ''
				   AND c.oriori =  '".$wemp_pmla."'
				   AND a.Ubihis =  '".$his."'
				   AND a.Ubiing =  '".$ing."'
				";
	}

	$res = mysql_query( $sql, $conex );

	if( $res ){

		while( $rows = mysql_fetch_array( $res ) ){

			$msg = '';
			if( $rows['Ubialp'] == 'on' )
				$msg = "Alta en proceso";

			if( $rows['Ubiald'] == 'on' )
				$msg = "Alta definitiva";

			if( $rows['Ubimue'] == 'on' )
				$msg = ( $msg != '' ? $msg."<br>": '' )."Fallecido";
			
			$nombrePaciente = utf8_encode( $rows['Pacno1']." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'] );
			$nombrePaciente = trim( $nombrePaciente ) == '' ? $rows['Turnom']: $nombrePaciente;

			$val['data'][] = array(
				'historia' 			=> empty( $rows['Ubihis'] ) ? '': $rows['Ubihis'],
				'ingreso' 			=> empty( $rows['Ubiing'] ) ? '': $rows['Ubiing'],
				'primerNombre' 		=> utf8_encode( $rows['Pacno1'] ),
				'segundoNombre' 	=> utf8_encode( $rows['Pacno2'] ),
				'primerApellido' 	=> utf8_encode( $rows['Pacap1'] ),
				'segundoApellido' 	=> utf8_encode( $rows['Pacap2'] ),
				'nombre' 			=> $nombrePaciente,
				'tipoDocumento'		=> utf8_encode( $rows['Oritid'] ),
				'numeroDocumento'	=> utf8_encode( $rows['Oriced'] ),
				'ubicacion' 		=> array(
					'cco' => array(
						'codigo' 		=> empty( $rows['Ubisac'] ) ? '': $rows['Ubisac'],
						'nombre' 		=> utf8_encode( $rows['Cconom'] ),
						'descripcion' 	=> utf8_encode( $rows['Ubisac']."-".$rows['Cconom'] ),
					),
					'habitacion' 	=> empty($rows['Ubihac']) ? $rows['Ubihan']: $rows['Ubihac'],
					'muerte' 		=> $rows['Ubimue'],
					'mensaje' 		=> utf8_encode( $msg ),
				),
				'turnocx' 			=> $turnocx,
				'cirugia' 			=> $rows['Turcir'] != '' ? explode( "-", substr( $rows['Turcir'], 0, -1 ) ) : '',
			);
		}
	}
	else{
		$val[ 'error' ] 	= 1;
		$val[ 'message' ] 	= mysql_errno()." - Error en el query $sql - ".mysql_error();
	}

	return $val;
}


function consultarConsecutivoNotaHCE( $conex, $whce, $tabla, $fecha, $hora, $his, $ing ){

	$val = false;

	//Consulto el último consecutivo para la nota
	$sql = "SELECT Movcon*1 as Movcons
	          FROM ".$whce."_".$tabla."
			 WHERE Fecha_data= '".$fecha."'
			   AND Hora_data = '".$hora."'
			   AND movpro	 = '".$tabla."'
			   AND movhis	 = '".$his."'
			   AND moving	 = '".$ing."'
			   AND movcon	 > 1000
		  ORDER BY 1 DESC
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['Movcons']+1;
	}

	return $val;
}

/*********************************************************************************************************
 *
 *********************************************************************************************************/
function consultarMovimiento( $conex, $wbasedato, $id ){

	$val = array();

	 $sql = "SELECT a.Medico, a.Fecha_data, a.Hora_data, a.Movbot, a.Movaux, a.Movins, a.Movfec, a.Movmov, a.Movhis, a.Moving, a.Movcco, a.Movhab, a.Movcmo, a.Movfmo, a.Movhmo, a.Movumo, a.Movjmo, a.Movtra, a.Movant, a.Movest, a.Seguridad, b.Artcom, b.Artgen, b.Artuni
	          FROM ".$wbasedato."_000228 a, ".$wbasedato."_000026 b
			 WHERE a.id = '".$id."'
			   AND artcod = movins
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){

		$val = array(
			"fechaRegistro" 			=> $rows['Fecha_data'],
			"horaRegistro" 				=> $rows['Hora_data'],
			"botiquin" 					=> $rows['Movbot'],
			"auxliar" 					=> $rows['Movaux'],
			"insumo" 					=> $rows['Movins'],
			"nombreGenerico" 			=> trim( $rows['Artgen'] ),
			"nombreComercial" 			=> trim( $rows['Artcom'] ),
			"presentacion" 				=> $rows['Artuni'],
			"fecha" 					=> $rows['Movfec'],
			"movimiento" 				=> $rows['Movmov'],
			"historia" 					=> $rows['Movhis'],
			"ingreso" 					=> $rows['Moving'],
			"ccoPaciente" 				=> $rows['Movcco'],
			"habitacion" 				=> $rows['Movhab'],
			"cantidadMovimiento" 		=> $rows['Movcmo'],
			"fechaMovimiento" 			=> $rows['Movfmo'],
			"horaMovimiento" 			=> $rows['Movhmo'],
			"usuarioMovmiento"			=> $rows['Movumo'],
			"justificacionMovimiento" 	=> $rows['Movjmo'],
			"esTrasaldo"				=> $rows['Movtra'],
			"anterior" 					=> $rows['Movant'],
			"estado" 					=> $rows['Movest'],
		);
	}

	return $val;
}

/*********************************************************************************************************
 * Indica si un insumo requiere justificacion
 * Junio 6 de 2017
 *********************************************************************************************************/
function insumoRequiereJustificacion( $conex, $wbasedato, $wbot, $winsumo ){

	$val = false;

	//Consultando todo lo que tenga saldo
	$sql = "SELECT Arsjus, Arsjpd, Arscan
			  FROM ".$wbasedato."_000232
			 WHERE Arscco = '".$wbot."'
			   AND Arscod = '".$winsumo."'
			   AND Arsest = 'on'
			";

	$resIns = mysql_query( $sql, $conex );
	if( $rows = mysql_fetch_array( $resIns ) ){
		$val['requiereJustificacion'] = $rows['Arsjus'] == 'on' ? 'on': 'off';
		if( $rows['Arsjus'] != 'on' )
				$rows['Arsjpd'] = '';

		$val['justificacionPorDefecto'] = $rows['Arsjpd'];
		$val['cantidadJustificacion'] = $rows['Arscan'];
	}

	return $val;
}

/*********************************************************************************************************
 *
 *********************************************************************************************************/
function desaplicarMovimiento( $conex, $wbasedato, $id ){

	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> '',
	);

	$sql = "UPDATE ".$wbasedato."_000228
			   SET Movest = 'off'
			 WHERE id='".$id."'
			";

	$res = mysql_query( $sql, $conex );

	if( $res ){

		if( mysql_affected_rows() > 0 ){

			$val[ 'message' ] 	= "Aplicacion cancelada";
		}
		else{
			$val[ 'error' ] 	= 1;
			$val[ 'message' ] 	= "El articulo no se pudo desaplicar";
		}
	}

	return $val;
}

function consultarAplicaciones( $conex, $wbasedato, $wemp_pmla, $wauxiliar, $historia, $ingreso, $wturnocx ){

	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> '',
	);

	$wbasedato_tcx    = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tcx");
	$ultimasHorasAplicadas = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horasAplicadasInsumos" );

	//Solo se busca lo que este aplicado las últimas 12 horas
	$time = time() - $ultimasHorasAplicadas*3600;

	//Consultando todo lo que tenga saldo
	$sql = "SELECT a.Medico, a.Fecha_data, a.Hora_data, Movbot, Movaux, Movins, Movfec, Movmov, Movhis, Moving, Movcco, Movhab, Movcmo, Movfmo, Movhmo, Movumo, Movjmo, Movtra, Movant, Movest, a.Seguridad, Unides, Unicod, Artcod, Artgen, Artcom, Cconom, Habcco, Habcod, Pacno1, Pacno2, Pacap1, Pacap2, a.id, Movtur
			  FROM ".$wbasedato."_000228 a, ".$wbasedato."_000026 b, ".$wbasedato."_000027 c, ".$wbasedato."_000020 d, ".$wbasedato."_000011 e, root_000036 f, root_000037 g
			 WHERE Movaux = '".$wauxiliar."'
			   AND Movhis = '".$historia."'
			   AND Moving = '".$ingreso."'
			   AND Movins = Artcod
			   AND Movmov = 'AP'
			   AND Movest = 'on'
			   AND Artuni = Unicod
			   AND Movhab = habcod
			   AND habcco = ccocod
			   AND orihis = habhis
			   AND oriori = '".$wemp_pmla."'
			   AND oriced = pacced
			   AND oritid = pactid
			   AND Movtur = 0
			   AND ( a.Fecha_data > '".date( "Y-m-d", $time )."'
			    OR ( a.Fecha_data = '".date( "Y-m-d", $time )."' AND a.Hora_data >= '".date( "H:i:s", $time )."' ) )
			 UNION
			SELECT a.Medico, a.Fecha_data, a.Hora_data, Movbot, Movaux, Movins, Movfec, Movmov, Turhis as Movhis, Turnin as Moving, Movcco, Movhab, Movcmo, Movfmo, Movhmo, Movumo, Movjmo, Movtra, Movant, Movest, a.Seguridad, Unides, Unicod, Artcod, Artgen, Artcom, Cconom, Movcco as Habcco, Movhab as Habcod, Pacno1, Pacno2, Pacap1, Pacap2, a.id, Movtur
			  FROM ".$wbasedato."_000228 a LEFT JOIN ".$wbasedato."_000011 e ON Ccocod = Movcco, ".$wbasedato."_000026 b, ".$wbasedato."_000027 c, root_000036 f, root_000037 g, ".$wbasedato_tcx."_000011 AS tcx11
			 WHERE Movaux = '".$wauxiliar."'
			   AND Movins = Artcod
			   AND Movmov = 'AP'
			   AND Movest = 'on'
			   AND Artuni = Unicod
			   AND orihis = Turhis
			   AND oriori = '".$wemp_pmla."'
			   AND oriced = pacced
			   AND oritid = pactid
			   AND Movtur > 0
			   AND Turtur = Movtur
			   AND Turtur = '".$wturnocx."'
			   AND ( a.Fecha_data > '".date( "Y-m-d", $time )."'
			    OR ( a.Fecha_data = '".date( "Y-m-d", $time )."' AND a.Hora_data >= '".date( "H:i:s", $time )."' ) )
		  ORDER BY 1, 2 ASC
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){

		while( $rows = mysql_fetch_array( $res ) ){

			$val['data'][] = array(
				'botiquin' 	=> $rows['Movbot'],
				'nombre'	=> utf8_encode( trim( $rows['Pacno1']." ".$rows['Pacno2'])." ".trim( $rows['Pacap1']." ".$rows['Pacap2'] ) ),
				'auxiliar' 	=> $rows['Movaux'],
				'historia' 	=> $rows['Movhis'],
				'ingreso' 	=> $rows['Moving'],
				'fecha' 	=> $rows['Fecha_data'],
				'hora' 		=> $rows['Hora_data'],
				'cantidad'	=> $rows['Movcmo'],
				'movimiento'=> $rows['id'],
				'turnocx'	=> $rows['Movtur'],
				'ubicacion'	=> array(
					'cco'		 => array(
						'codigo' 	  => $rows['Movcco'],
						'nombre' 	  => $rows['Cconom'],
						'descripcion' => $rows['Movcco']."-".$rows['Cconom'],
					),
					'habitacion' => $rows['Movhab'],
				),
				'insumo' 	=> array(
					'codigo' 			=> $rows['Artcod'],
					'nombreComercial' 	=> utf8_encode( $rows['Artcom'] ),
					'nombreGenercio'	=> utf8_encode( $rows['Artgen'] ),
					'presentacion' 		=> array(
						'codigo' 		=> $rows['Unicod'],
						'descripcion' 	=> $rows['Unides'],
					),
				),
			);
		}
	}

	return $val;
}

/*********************************************************************************************************
 *
 *********************************************************************************************************/
function actualizarEncabezadoCargoInsumoDesaplicacion( $conex, $wbasedato, $wcco, $wauxiliar, $warticulo, $wcantidad, $whis, $wing, $turnocx = 0 ){

	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> '',
	);
	
	$filtro_paciente = "AND Carhis = '".$whis."'
			   			AND Caring = '".$wing."'";
	if($turnocx > 0)
	{
		$filtro_paciente = "AND Cartur = '{$turnocx}'";
	}

	//Consultando todo lo que tenga saldo
	$sql = "SELECT Fecha_data, Hora_data, Carbot, Caraux, Carhis, Caring, Carins, Carfec, Carcca, Carcap, Carcde, Cartra, Carest, Seguridad, id
			  FROM ".$wbasedato."_000227
			 WHERE Carbot = '".$wcco."'
			   AND Caraux = '".$wauxiliar."'
			   {$filtro_paciente}
			   AND Carins = '".$warticulo."'
			   AND Carcap > 0
		  ORDER BY Carfec DESC
			";

	$resIns = mysql_query( $sql, $conex );
	$rows = mysql_fetch_array( $resIns );

	while( $wcantidad > 0 && $rows ){

		$fecha 	= $rows['Carfec'];

		$saldo = $rows['Carcap'];

		$can = 0;
		if( $wcantidad <= $saldo ){
			$can = $wcantidad;
			$wcantidad = 0;
		}
		else{
			$can = $saldo;
			$wcantidad -= $saldo;
		}

		$sql = "UPDATE ".$wbasedato."_000227
				   SET Carcap = Carcap-$can
				 WHERE Carbot = '".$wcco."'
				   AND Caraux = '".$wauxiliar."'
				  {$filtro_paciente}
				   AND Carins = '".$warticulo."'
				   AND Carfec = '".$fecha."'
				   AND Carcap-$can >= 0
				";

		$res = mysql_query( $sql, $conex );

		if( $res ){

			if( mysql_affected_rows() > 0 ){

				$val[ 'message' ] 	= "Articulo actualizado correctamente";
				$val[ 'data' ]['totalDesaplicada'] += $can;
				$val[ 'data' ]['insumos'][] = array(
					'cantidad' 	=> $can,
					'fecha' 	=> $fecha,
				);

				actualizarEncabezadoCargoInsumoTransito( $conex, $wbasedato, $wcco, $wauxiliar, $whis, $wing, $warticulo, $fecha );
			}
			else{
				$val[ 'error' ] 	= 1;
				$val[ 'message' ] 	= "El articulo no se pudo desaplicar";
			}
		}
		else{
			$val[ 'error' ] 	= 1;
			$val[ 'message' ] 	= mysql_errno()." - Error en el query $sql - ".mysql_error();
		}

		$rows = mysql_fetch_array( $resIns );
	}

	return $val;
}


/********************************************************************************************************
 * Carga a facturacion y aplica un articulo al paciente
 ********************************************************************************************************/
function desaplicarArticulo( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $insumo, $cantidad, $unidadArticulo, $ccoori, $ccodes, $habitacion, $historia, $ingreso, $justificacion ){

	//Hago el include de los archivos necesarios
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("movhos/cargosSF.inc.php");
	include_once("ips/funciones_facturacionERP.php");

	global $bd;
	global $conex_o;

	global $pac;
	global $emp;
	// global $wbasedato;
	global $usuario;
	global $wuse;
	global $cco;
	global $desde_CargosPDA;

	$val = array(
		'error' 		=> 0,
		'message' 		=> '',
		'data' 			=> '',
		'conexionUnix' 	=> false,
	);

	$bd = $wbasedato;

	//Consulta la información del articulo
	$infoArt = consultarInsumo( $conex, $wbasedato, $insumo, $ccoori, false );

	//Se debe crear arrays para que las funciones usadas funcionen
	//Array de articulo
	$art = array(
		'cod' => $insumo,	//Codigo del insumo
		'nom' => $infoArt['data'][0]['nombreComercial'],	//nombre comercial
		'can' => $cantidad,	//Cantidad a cargar
		'uni' => $unidadArticulo,
		'lot' => '',		//El lote solo es para productos de CM, por tanto es vacio
		'ini' => $insumo,	//Codigo leido inicial, puede ser codigo de barras. Siempre se envia el codigo matrix
		'ubi' => 'M',		//M de Matrix
		'ser' => $ccodes,	//Servicio de destiono, es decir, el cco del paciente
		'dis' => 'off',		//Indica si está en el carro, como es aplicacion en piso no hay carro. Siempre es off
	);


	//Array de pacientes
	$pac = array(
		'his' => $historia,
		'ing' => $ingreso,
		'sac' => $ccodes,
	);

	$cco = array(
		'cod' => $ccoori,
	);

	//Tiptrans es la accion a realizar, C es para cargo
	//D para devolucion
	$tipTrans = "D";

	//Esto es aprovechamiento, siempre false por que no se usa
	//Además es carga de insumos, no son aprovechables
	$aprov = false;

	//Variable que arroja errores en las funciones usadas
	$error = "";

	//Esta tabla es la tabla en la que se registra el cargo
	//Se deja por defecto la que se maneja cuando hay unix
	$tabla = "000003";

	//conexion unix
	connectOdbc($conex_o, 'inventarios');

	//Consulto la informacion del cco
	//la variable $cco se modifica dentro de la función
	getCco($cco,$tipTrans, $wemp_pmla);

	//Fuente de cargo sin aprovechamiento
	$fuente = $cco['fue'];

	//Si no hay conexion unix se maneja la tabla auxiliar para estos casos
	//Esta es la tabla 000143
	if( !$conex_o ){
		$tabla = "000143";
	}

	$saldoDisponible = consultarSaldoInsumoAuxiliarPorBotiquin( $conex, $wbasedato, $ccoori, $wauxiliar, $historia, $ingreso, $insumo );

	if( true || $saldoDisponible >= $cantidad ){

		$tarSal = false;
		if( $conex_o ){
			$tarSal				 = TarifaSaldo($art, $cco,$tipTrans,$aprov, $error);
			$val['conexionUnix'] = true;
		}
		else{
			$tarSal		= TarifaSaldoMatrix($art, $cco,$tipTrans,$aprov, $error);
		}

		if( $tarSal ){

			//Variables que se modifican dentro de la funcion: &$date, &$cns, &$dronum, &$drolin, &$error y no requieren ser inicializados
			//$date queda con la fecha actual si no se inicializa
			$artValido	= Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, $date, $cns, $dronum, $drolin, false, $wauxiliar, $error );

			if( $artValido ){

				$artValido 	= registrarDetalleCargo($date, $dronum, $drolin, $art, $wauxiliar,$error, $tabla);

				if( $artValido ){

					if( $conex_o ){
						$artValido = registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, $error);
					}

					if( $artValido ){

						//Moviendo saldo del paciente (movhos_000004)
						//Se hace una entrada por que es un cargo
						// $artValido	= registrarSaldosNoApl( $pac, $art, $cco, $aprov, $wauxiliar, $tipTrans, false, &$error );

						if( $artValido ){

							//Moviendo saldo del paciente (movhos_000004)
							//Se hace una salida por que es una aplicacion
							// $artValido	= registrarSaldosNoApl( $pac, $art, $cco, $aprov, $wauxiliar, "D", false, &$error );

							if($artValido){

								$fecApl=$date;
								$ronApl=gmdate("H:00 - A", floor( date( "H" )/2 )*2*3600 );

								//Registrar la aplicación del artículo
								$artValido = registrarAplicacion( $pac, $art, $cco,$aprov, $fecApl, $ronApl, $wauxiliar, $tipTrans, $dronum, $drolin, $error );

								$emp 			= $wemp_pmla;
								$usuario 		= $wauxiliar;
								$wuse 			= $wauxiliar;
								$desde_CargosPDA= true;

								CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );

								$encabezadoAct = actualizarEncabezadoCargoInsumoDesaplicacion( $conex, $wbasedato, $ccoori, $wauxiliar, $insumo, $cantidad, $historia, $ingreso );

								if( !empty($encabezadoAct['data']['insumos']) ){

									foreach( $encabezadoAct['data']['insumos'] as $key => $value ){

										if( $value != 'totalDesaplicada' ){

											$wmovimiento = 'AA';	//Es una aplicacion anulada
											$fechaEncabezado = $value['fecha'];
											$can = $value['cantidad'];
											$val['data']['auditoria'] = registrarAuditoriaCargoInsumo( $conex, $wbasedato, $ccoori, $wauxiliar, $insumo, $fechaEncabezado, $wmovimiento, $historia, $ingreso, $ccodes, $habitacion, -1*$can, $wauxiliar, $justificacion );

											$val['data']['numeroDocumento'] = $dronum;
											$val['data']['numeroLinea'] 	= $drolin;
											$val['data']['fechaAplicacion'] = $fecApl;
											$val['data']['rondaAplicacion'] = $ronApl;
											$val['data']['horaAplicacion'] 	= date( "H:i:s" );
										}
									}
								}
							}
							else{
								$val['error'] 		= 1;
								$val['message'] 	= "No logra registrar saldo al paciente por devolucion de articulo";
								$val['extraError'] = $error;
							}
						}
						else{
							$val['error'] 		= 1;
							$val['message'] 	= "No logra registrar saldo al paciente por cargo de articulo";
							$val['extraError'] = $error;
						}
					}
					else{
						$val['error'] 		= 1;
						$val['message'] 	= "No se logra registrar movimiento en itdro";
						$val['extraError'] = $error;
					}
				}
				else{
					$val['error'] 		= 1;
					$val['message'] 	= "No se logra registrar movimiento de cargo";
					$val['extraError'] = $error;
				}
			}
			else{
				$val['error'] 		= 1;
				$val['message'] 	= "No se logra crear o encontrar un numero de documento y/o linea valido";
				$val['extraError'] = $error;
			}
		}
		else{
			$val['error'] 		= 1;
			$val['message'] 	= "No se encuentra tarifa o saldo del articulo";
			$val['extraError'] = $error;
		}
	}
	else{
		$val['error'] 		= 1;
		$val['message'] 	= "La auxiliar no tiene saldo suficiente para cargar la cantidad";
		$val['extraError'] = $error;
	}

	return $val;
}


function actualizarSaldoBotiquin( $conex, $wbasedato, $wcco, $wart, $wcan ){

	$val = false;

	$sql = "UPDATE ".$wbasedato."_000141
			   SET Salsal = Salsal+$wcan
			 WHERE Salser = '".$wcco."'
			   AND Salart = '".$wart."'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		$val = true;
	}

	return $val;
}


function validarFirmaElectronica( $conex, $wbasedato, $usuario, $firma ){

	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> '',
	);

	$estado = "";

	$q = "SELECT
			Usucod,Usucla
		FROM
			".$wbasedato."_000020
		WHERE
			Usucod = '".$usuario."'
			AND Usucla = '".$firma."'
			AND Usuest = 'on';";

	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);

	if($num > 0){
		$val['data'] = "1";
	} else {
		if(!empty($firma)){
			$val['data'] = "2";
		}
	}

	return $val;
}


/*********************************************************************************************************
 *
 *********************************************************************************************************/
function consultarSaldoInsumoAuxiliarPorBotiquin( $conex, $wbasedato, $wbot, $wauxiliar, $whis, $wing, $warticulo ){

	$val = 0;

	//Consultando todo lo que tenga saldo
	$sql = "SELECT SUM(Carcca-Carcap-Carcde) as Saldo
			  FROM ".$wbasedato."_000227
			 WHERE Carbot = '".$wbot."'
			   AND Caraux = '".$wauxiliar."'
			   AND Carhis = '".$whis."'
			   AND Caring = '".$wing."'
			   AND Carins = '".$warticulo."'
			";

	$resIns = mysql_query( $sql, $conex );
	if( $rows = mysql_fetch_array( $resIns ) ){
		$val = $rows['Saldo'];
	}

	return $val;
}


/*********************************************************************************************************
 *
 *********************************************************************************************************/
function actualizarEncabezadoCargoInsumoTransito( $conex, $wbasedato, $wbot, $wauxiliar, $whis, $wing, $warticulo, $fecha ){

	$val = false;

	$sql = "SELECT (Carcca-Carcap-Carcde) as Saldo, id
			  FROM ".$wbasedato."_000227
			 WHERE Carbot = '".$wbot."'
			   AND Caraux = '".$wauxiliar."'
			   AND Carhis = '".$whis."'
			   AND Caring = '".$wing."'
			   AND Carins = '".$warticulo."'
			   AND Carfec = '".$fecha."'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){

		$transito = 'on';
		if( $rows['Saldo'] == 0 ){
			$transito = 'off';
		}

		$sql = "UPDATE ".$wbasedato."_000227
				   SET Cartra = '".$transito."'
				 WHERE id='".$rows['id']."'
				";

		$resAct = mysql_query( $sql, $conex );
		if( mysql_affected_rows() > 0 ){
			$val = true;
		}

	}

	return $val;
}

/*********************************************************************************************************
 *
 *********************************************************************************************************/
function actualizarEncabezadoCargoInsumoAplicacion( $conex, $wbasedato, $wcco, $wauxiliar, $warticulo, $wcantidad, $whis, $wing, $turnocx=0 ){

	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array("totalAplicada"=>0),
	);

	$filtro_paciente = "AND Carhis = '".$whis."'
			   			AND Caring = '".$wing."'";
	if($turnocx > 0)
	{
		$filtro_paciente = "AND Cartur = '{$turnocx}'";
	}

	//Consultando todo lo que tenga saldo
	$sql = "SELECT Fecha_data, Hora_data, Carbot, Caraux, Carhis, Caring, Carins, Carfec, Carcca, Carcap, Carcde, Cartra, Carest, Seguridad, id
			  FROM ".$wbasedato."_000227
			 WHERE Carbot = '".$wcco."'
			   AND Caraux = '".$wauxiliar."'
			   {$filtro_paciente}
			   AND Carins = '".$warticulo."'
			   AND Carcca-Carcap-Carcde > 0
		  ORDER BY Carfec ASC
			";

	$resIns = mysql_query( $sql, $conex );
	$rows = mysql_fetch_array( $resIns );

	while( $wcantidad > 0 && $rows ){

		$fecha 	= $rows['Carfec'];

		$saldo = $rows['Carcca']-$rows['Carcap']-$rows['Carcde'];

		$can = 0;
		if( $wcantidad <= $saldo ){
			$can = $wcantidad;
			$wcantidad = 0;
		}
		else{
			$can = $saldo;
			$wcantidad -= $saldo;
		}

		$sql = "UPDATE ".$wbasedato."_000227
				   SET Carcap = Carcap+$can
				 WHERE Carbot = '".$wcco."'
				   AND Caraux = '".$wauxiliar."'
				   {$filtro_paciente}
				   AND Carins = '".$warticulo."'
				   AND Carfec = '".$fecha."'
				   AND Carcca-(Carcap+$can)-Carcde >= 0
				";

		$res = mysql_query( $sql, $conex );

		if( $res ){

			if( mysql_affected_rows() > 0 ){

				$val[ 'message' ] 	= "Articulo actualizado correctamente";
				$val[ 'data' ]['totalAplicada'] += $can;
				$val[ 'data' ]['insumos'][] = array(
					'cantidad' 	=> $can,
					'fecha' 	=> $fecha,
				);

				actualizarEncabezadoCargoInsumoTransito( $conex, $wbasedato, $wcco, $wauxiliar, $whis, $wing, $warticulo, $fecha );
			}
			else{
				$val[ 'error' ] 	= 1;
				$val[ 'message' ] 	= "El articulo no se pudo cargar a la auxiliar";
			}
		}
		else{
			$val[ 'error' ] 	= 1;
			$val[ 'message' ] 	= mysql_errno()." - Error en el query $sql - ".mysql_error();
		}

		$rows = mysql_fetch_array( $resIns );
	}

	return $val;
}

function aplicarArticuloTurnoCirugia($conex, $wemp_pmla, $wbasedato, $wauxiliar, $insumo, $cantidad, $unidadArticulo, $ccoori, $ccodes, $habitacion, $historia, $ingreso, $justificacion, $fecha, $hora, $turnocx)
{
	$val = array(
		'error' 		=> 0,
		'message' 		=> '',
		'data' 			=> array("auditoria"),
		'conexionUnix' 	=> false,
	);
	$encabezadoAct = actualizarEncabezadoCargoInsumoAplicacion( $conex, $wbasedato, $ccoori, $wauxiliar, $insumo, $cantidad, $historia, $ingreso, $turnocx );

	if( !empty($encabezadoAct['data']['insumos']) ){

		foreach( $encabezadoAct['data']['insumos'] as $key => $value ){

			if( $value != 'totalAplicada' ){

				$wmovimiento = 'AP';	//Es una aplicación
				$fechaEncabezado = $value['fecha'];
				$can = $value['cantidad'];
				$val['data']['auditoria'] = registrarAuditoriaCargoInsumo( $conex, $wbasedato, $ccoori, $wauxiliar, $insumo, $fechaEncabezado, $wmovimiento, $historia, $ingreso, $ccodes, $habitacion, $can, $wauxiliar, $justificacion, 'off', '', $fecha, $hora, $turnocx );
				$val['data']['fechaAplicacion'] = $fecha;
				$val['data']['rondaAplicacion'] = gmdate( "H:i:s", floor( date("H")/2 )*2*3600 );
				$val['data']['horaAplicacion'] 	= $hora;
			}
		}
	}
	return $val;
}

/********************************************************************************************************
 * Carga a facturacion y aplica un articulo al paciente
 ********************************************************************************************************/
function aplicarArticulo( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $insumo, $cantidad, $unidadArticulo, $ccoori, $ccodes, $habitacion, $historia, $ingreso, $justificacion, $fecha, $hora ){

	//Hago el include de los archivos necesarios
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("movhos/cargosSF.inc.php");
	include_once("ips/funciones_facturacionERP.php");

	global $bd;
	global $conex_o;


	global $pac;
	global $emp;
	// global $wbasedato;
	global $usuario;
	global $wuse;
	global $cco;
	global $desde_CargosPDA;


	$val = array(
		'error' 		=> 0,
		'message' 		=> '',
		'data' 			=> array(),
		'conexionUnix' 	=> false,
	);

	$bd = $wbasedato;

	$infoArt = consultarInsumo( $conex, $wbasedato, $insumo, $ccoori, false );

	//Se debe crear arrays para que las funciones usadas funcionen
	//Array de articulo
	$art = array(
		'cod' => $insumo,	//Codigo del insumo
		'nom' => $infoArt['data'][0]['nombreComercial'],	//nombre comercial
		'can' => $cantidad,	//Cantidad a cargar
		'uni' => $unidadArticulo,
		'lot' => '',		//El lote solo es para productos de CM, por tanto es vacio
		'ini' => $insumo,	//Codigo leido inicial, puede ser codigo de barras. Siempre se envia el codigo matrix
		'ubi' => 'M',		//M de Matrix
		'ser' => $ccodes,	//Servicio de destiono, es decir, el cco del paciente
		'dis' => 'off',		//Indica si está en el carro, como es aplicacion en piso no hay carro. Siempre es off
	);


	//Array de pacientes
	$pac = array(
		'his' => $historia,
		'ing' => $ingreso,
		'sac' => $ccodes,
	);

	$cco = array(
		'cod' => $ccoori,
	);

	//Tiptrans es la accion a realizar, C es para cargo
	//D para devolucion
	$tipTrans = "C";

	//Esto es aprovechamiento, siempre false por que no se usa
	//Además es carga de insumos, no son aprovechables
	$aprov = false;

	//Variable que arroja errores en las funciones usadas
	$error = "";

	//Esta tabla es la tabla en la que se registra el cargo
	//Se deja por defecto la que se maneja cuando hay unix
	$tabla = "000003";

	//conexion unix
	connectOdbc($conex_o, 'inventarios');

	//Consulto la informacion del cco
	//la variable $cco se modifica dentro de la función
	getCco($cco,$tipTrans, $wemp_pmla);

	//Fuente de cargo sin aprovechamiento
	$fuente = $cco['fue'];

	//Si no hay conexion unix se maneja la tabla auxiliar para estos casos
	//Esta es la tabla 000143
	if( !$conex_o ){
		$tabla = "000143";
	}

	$saldoDisponible = consultarSaldoInsumoAuxiliarPorBotiquin( $conex, $wbasedato, $ccoori, $wauxiliar, $historia, $ingreso, $insumo );

	if( $saldoDisponible >= $cantidad ){

		ArticulosEspeciales( $cco, $art );
		$art['can'] = $cantidad;

		$tarSal = false;
		if( $conex_o ){
			$tarSal				 = TarifaSaldo($art, $cco,$tipTrans,$aprov, $error);
			$val['conexionUnix'] = true;
		}
		else{
			$tarSal		= TarifaSaldoMatrix($art, $cco,$tipTrans,$aprov, $error);
		}

		if( $tarSal ){

			//Variables que se modifican dentro de la funcion: &$date, &$cns, &$dronum, &$drolin, &$error y no requieren ser inicializados
			//$date queda con la fecha actual si no se inicializa
			$artValido	= Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, $date, $cns, $dronum, $drolin, false, $wauxiliar, $error );

			if( $artValido ){

				$artValido 	= registrarDetalleCargo($date, $dronum, $drolin, $art, $wauxiliar,$error, $tabla);

				if( $artValido ){

					if( $conex_o ){
						$artValido =registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, $error);

						$emp 			= $wemp_pmla;
						$usuario 		= $wauxiliar;
						$wuse 			= $wauxiliar;
						$desde_CargosPDA= true;

						CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );
					}

					if( $artValido ){

						//Moviendo saldo del paciente (movhos_000004)
						//Se hace una entrada por que es un cargo
						$artValido	= registrarSaldosNoApl( $pac, $art, $cco, $aprov, $wauxiliar, $tipTrans, false, $error );

						if( $artValido ){

							//Moviendo saldo del paciente (movhos_000004)
							//Se hace una salida por que es una aplicacion
							$artValido	= registrarSaldosNoApl( $pac, $art, $cco, $aprov, $wauxiliar, "D", false, $error );

							if($artValido){

								$fecApl=$date;
								$ronApl=gmdate("H:00 - A", floor( date( "H" )/2 )*2*3600 );
								$horaAplicacion = $hora;

								//Registrar la aplicación del artículo
								$artValido = registrarAplicacion( $pac, $art, $cco,$aprov, $fecApl, $ronApl, $wauxiliar, $tipTrans, $dronum, $drolin, $error );

								$encabezadoAct = actualizarEncabezadoCargoInsumoAplicacion( $conex, $wbasedato, $ccoori, $wauxiliar, $insumo, $cantidad, $historia, $ingreso );

								if( !empty($encabezadoAct['data']['insumos']) ){

									foreach( $encabezadoAct['data']['insumos'] as $key => $value ){

										if( $value != 'totalAplicada' ){

											$wmovimiento = 'AP';	//Es una aplicación
											$fechaEncabezado = $value['fecha'];
											$can = $value['cantidad'];
											$val['data']['auditoria'] = registrarAuditoriaCargoInsumo( $conex, $wbasedato, $ccoori, $wauxiliar, $insumo, $fechaEncabezado, $wmovimiento, $historia, $ingreso, $ccodes, $habitacion, $can, $wauxiliar, $justificacion, 'off', '', $fecha, $horaAplicacion );

											$val['data']['numeroDocumento'] = $dronum;
											$val['data']['numeroLinea'] 	= $drolin;
											$val['data']['fechaAplicacion'] = $fecApl;
											$val['data']['rondaAplicacion'] = $ronApl;
											$val['data']['horaAplicacion'] 	= $horaAplicacion;
										}
									}
								}
							}
							else{
								$val['error'] 		= 1;
								$val['message'] 	= "No logra registrar saldo al paciente por devolucion de articulo";
								$val['extraError'] = $error;
							}
						}
						else{
							$val['error'] 		= 1;
							$val['message'] 	= "No logra registrar saldo al paciente por cargo de articulo";
							$val['extraError'] = $error;
						}
					}
					else{
						$val['error'] 		= 1;
						$val['message'] 	= "No se logra registrar movimiento en itdro";
						$val['extraError'] = $error;
					}
				}
				else{
					$val['error'] 		= 1;
					$val['message'] 	= "No se logra registrar movimiento de cargo";
					$val['extraError'] = $error;
				}
			}
			else{
				$val['error'] 		= 1;
				$val['message'] 	= "No se logra crear o encontrar un numero de documento y/o linea valido";
				$val['extraError'] = $error;
			}
		}
		else{
			$val['error'] 		= 1;
			$val['message'] 	= "No se encuentra tarifa o saldo del articulo";
			$val['extraError'] = $error;
		}
	}
	else{
		$val['error'] 		= 1;
		$val['message'] 	= "La auxiliar no tiene saldo suficiente para cargar la cantidad";
		$val['extraError'] = $error;
	}

	return $val;
}

/****************************************************************************************************************
 * Consulta el nombre del paciente por historia, habitación, nro de cedula o nombre según el parametro filtro
 ****************************************************************************************************************/
function consultarArticulosAuxiliar( $conex, $wbasedato, $wemp_pmla, $wauxiliar ){

	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array(),
	);

	$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
	
	// "SELECT Carbot, Cconom, Carhis, Caring, Artcod, Artcom, Artgen, Artuni, Habcco, Movhab, SUM( Movcmo )
		  // FROM movhos_000026 a, movhos_000011 b, movhos_000020, (
			// SELECT Carbot, Carhis, Caring,Carins, Movhab, sum(Movcmo) AS Movcmo
			  // FROM movhos_000227 a, movhos_000228 e
			 // WHERE Caraux = '0102602'
			   // AND Carcca-Carcap-Carcde > 0
			   // AND Carest = 'on'
			   // AND Cartra = 'on'
			   // AND Movbot = Carbot
			   // AND Movaux = Caraux
			   // AND Movhis = Carhis
			   // AND Moving = Caring
			   // AND Movins = Carins
			   // AND Movfec = Carfec
			   // AND Movmov IN ('CR')
			   // AND Movest = 'on'
		  // GROUP BY 1,2,3,4,5
		     // UNION
			// SELECT Carbot, Carhis, Caring,Carins, Movhab, sum(Movcmo)*-1 AS Movcmo
			  // FROM movhos_000227 a, movhos_000228 e
			 // WHERE Caraux = '0102602'
			   // AND Carcca-Carcap-Carcde > 0
			   // AND Carest = 'on'
			   // AND Cartra = 'on'
			   // AND Movbot = Carbot
			   // AND Movaux = Caraux
			   // AND Movhis = Carhis
			   // AND Moving = Caring
			   // AND Movins = Carins
			   // AND Movfec = Carfec
			   // AND Movmov IN ('AP','DV')
			   // AND Movest = 'on'
		  // GROUP BY 1,2,3,4,5
		// ) AS tb
		     // WHERE Artcod = Carins
			   // AND Ccocod = Carbot
			   // AND Habcod = Movhab
		// GROUP BY 1,2,3,4,5,6,7"

	$sql = "SELECT Carbot, Cconom, Carhis, Caring, Artcod, Artcom, Artgen, Artuni, Unides, SUM(Carcca) as Carcca, SUM(Carcap) as Carcap, SUM(Carcde) as Carcde, Arsjus, Arscco, Arsjpd, Cartur, Arscan
			  FROM ".$wbasedato."_000227 a
   LEFT OUTER JOIN ".$wbasedato."_000232 e
				ON Arscco = Carbot
			   AND Arscod = Carins
			   AND Arsest = 'on',
				   ".$wbasedato."_000026 b,
				   ".$wbasedato."_000027 c,
				   ".$wbasedato."_000011 d
			 WHERE Caraux = '".$wauxiliar."'
			   AND Carins = Artcod
			   AND Carcca-Carcap-Carcde > 0
			   AND Artuni = Unicod
			   AND Carest = 'on'
			   AND Cartra = 'on'
			   AND Carbot = Ccocod
		  GROUP BY 16,1,2,3,4,5,6,7,8,9
		  ORDER BY 16,1,2,3,4,7,6
			";

	$res = mysql_query( $sql, $conex );
	$num = mysql_num_rows( $res );

	if( $res ){

		$val['message'] 		= "Total de articulos encontrados: ".$num;
		$val['totalArticulos'] 	= $num;

		$rows = mysql_fetch_array( $res );

		$paciente = consultarPacientePorUbicacion( $conex, $wbasedato, $wemp_pmla, $rows['Carhis'], $rows['Caring'], $rows['Cartur'] );
		
		// $paciente = consultarPacientePorFiltro( $conex, $wbasedato, $wemp_pmla, $rows['Carhis'] );
		$paciente = $paciente['data'][0];	//Esta es la información del paciente
		// print_r($paciente);
		$hisAnt = $rows['Carhis'];
		$turAnt = $rows['Cartur'];
		while( $rows ){

			//Quito los espacios a todos los campos
			foreach( $rows as &$value ){
				$value = trim( $value );
			}

			//Si no hay cco en la 91 significa que no se encuentra para ese cco justificación
			//Hay que buscar si tiene justificación para el cco *
			if( empty( $rows['Arscco'] ) ){
				$reqJus = insumoRequiereJustificacion( $conex, $wbasedato, '*', $rows['Artcod'] );
				if( $reqJus !== false ){
					$rows['Arsjus'] = $reqJus['requiereJustificacion'];
					$rows['Arsjpd'] = $reqJus['justificacionPorDefecto'];
					$rows['Arscan'] = $reqJus['cantidadJustificacion'];
				}
			}

			if( $rows['Arsjus'] != 'on' ){
				$rows['Arsjpd'] = '';
			}
			
			$codigosBarras = consultarCodigosBarra( $conex, $wbasedato, $rows['Artcod'] );
			
			$lote = consultarLoteArticulo( $conex, $wcliame, $art, $rows['Cartur'] );
			$lote = $lote == false ? '' : $lote;

			$paciente['articulos'][] = array(
				'botiquin' 				=> array(
					'codigo' 			=> $rows['Carbot'],
					'nombre' 			=> '',//utf8_encode($rows['Carnbo'])
				),
				'codigo' 				=> $rows['Artcod'],
				'nombreComercial'		=> utf8_encode( $rows['Artcom'] ),
				'nombreGenercio' 		=> utf8_encode( $rows['Artgen'] ),
				'unidad' 				=> array(
					'codigo' 			=> $rows['Artuni'],
					'descripcion' 		=> utf8_encode( $rows['Unides'] ),
				),
				'saldo' 				=> $rows['Carcca']-$rows['Carcap']-$rows['Carcde'],
				'requiereJustificacion' => $rows['Arsjus'] == 'on' ? true: false,
				'cantidadJustificacion' => (int)$rows['Arscan'],
				'justificacionDefecto'  => utf8_encode( $rows['Arsjpd'] ),
				'codigosBarras'			=> $codigosBarras,
				'lote'					=> $lote,
			);

			$rows = mysql_fetch_array( $res );

			if(($turAnt!='' && $turAnt != $rows['Cartur']) || $hisAnt != $rows['Carhis'] ){
				$val['data'][] = $paciente;
				if( $rows ){
					$paciente = consultarPacientePorUbicacion( $conex, $wbasedato, $wemp_pmla, $rows['Carhis'], $rows['Caring'], $rows['Cartur'] );
					// $paciente = consultarPacientePorFiltro( $conex, $wbasedato, $wemp_pmla, $rows['Carhis'] );
					$paciente = $paciente['data'][0];	//Esta es la información del paciente
					$hisAnt = $rows['Carhis'];
					$turAnt = $rows['Cartur'];
				}
			}
		}
	}
	else{
		$val[ 'error' ] 	= 1;
		$val[ 'message' ] 	= utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	}

	return $val;
}


/****************************************************************************************************************
 * Consulta el nombre del paciente por historia, habitación, nro de cedula o nombre según el parametro filtro
 ****************************************************************************************************************/
function consultarPacientePorFiltro( $conex, $wbasedato, $wemp_pmla, $filtro, $cco = '' ){

	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> [],
	);

	$sql = "SELECT  Habhis, Habing, Habcco, Cconom, Habcod, Pacno1, Pacno2, Pacap1, Pacap2, Oritid, Oriced
			  FROM ".$wbasedato."_000020 a , ".$wbasedato."_000011 b, root_000037 c, root_000036 d
			 WHERE a.habcco  = b.ccocod
			   AND c.orihis  = a.habhis
			   AND c.oriing  = a.habing
			   AND d.pacced  = c.oriced
			   AND d.pactid  = c.oritid
			   AND a.habhis  != ''
			   AND c.oriori  =  '".$wemp_pmla."'
			   AND ( c.orihis     =  '".$filtro."'
			    OR d.pacced       =  '".$filtro."'
			    OR a.habcod LIKE '%".$filtro."%'
			    OR CONCAT( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) LIKE '%".$filtro."%' )
			";

	$sql = "SELECT  Ubihis, Ubiing, Ubisac, Cconom, Ubihac, Ubihan, Pacno1, Pacno2, Pacap1, Pacap2, Oritid, Oriced, Ubimue, Ubiald, Ubialp
			  FROM ".$wbasedato."_000018 a , ".$wbasedato."_000011 b, root_000037 c, root_000036 d
			 WHERE a.Ubisac =  b.ccocod
			   AND c.orihis =  a.Ubihis
			   AND c.oriing =  a.Ubiing
			   AND d.pacced =  c.oriced
			   AND d.pactid =  c.oritid
			   AND a.Ubiald = 'off'
			   AND c.oriori  =  '".$wemp_pmla."'
			   AND ( c.orihis     =  '".$filtro."'
			    OR d.pacced       =  '".$filtro."'
			    OR a.Ubihac LIKE '%".$filtro."%'
			    OR CONCAT( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) LIKE '%".$filtro."%' )
			";

	$res = mysql_query( $sql, $conex );

	if( $res ){

		while( $rows = mysql_fetch_array( $res ) ){

			$value = utf8_encode( $rows['Ubihac']."-".$rows['Ubihis']."-".$rows['Pacno1']." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'] );

			$val['data'][] = array(
				'value' 			=> $value,
				'label' 			=> $value,
				'historia' 			=> $rows['Ubihis'],
				'ingreso' 			=> $rows['Ubiing'],
				'primerNombre' 		=> utf8_encode( $rows['Pacno1'] ),
				'segundoNombre' 	=> utf8_encode( $rows['Pacno2'] ),
				'primerApellido' 	=> utf8_encode( $rows['Pacap1'] ),
				'segundoApellido' 	=> utf8_encode( $rows['Pacap2'] ),
				'nombre' 			=> utf8_encode( $rows['Pacno1']." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'] ),
				'tipoDocumento'		=> utf8_encode( $rows['Oritid'] ),
				'numeroDocumento'	=> utf8_encode( $rows['Oriced'] ),
				'ubicacion' 		=> array(
					'cco' => array(
						'codigo' 		=> $rows['Ubisac'],
						'nombre' 		=> utf8_encode( $rows['Cconom'] ),
						'descripcion' 	=> utf8_encode( $rows['Ubisac']."-".$rows['Cconom'] ),
					),
					'habitacion' => $rows['Ubihac'],
				),
				'esPacienteConCita' => esPacienteDeAyudaDxParaPedidos( $conex, $wemp_pmla, $wbasedato, $rows['Ubihis'], $rows['Ubiing'], $cco ),
			);
		}
	}
	else{
		$val[ 'error' ] 	= 1;
		$val[ 'message' ] 	= mysql_errno()." - Error en el query $sql - ".mysql_error();
	}

	return $val;
}

/****************************************************************************************************************
 * 												FIN DE FUNCIONES
 ****************************************************************************************************************/


if( isset($consultaAjax) ){	//si hay ajax

	switch( $consultaAjax ){

		case 'consultarPaciente':
			$result = consultarPacientePorFiltro( $conex, $wbasedato, $wemp_pmla, $term, $cco );
			echo json_encode( $result['data'] );
		break;

		case 'consultarArticulosAuxiliar':
			$result = consultarArticulosAuxiliar( $conex, $wbasedato, $wemp_pmla, $wauxiliar );
			echo json_encode( $result['data'] );
		break;

		case 'aplicarListaArticulo':

			$fecha  = date( "Y-m-d" );
			$hora   = date( "H:i:s" );
			$result = array();
			if( count( $insumos ) > 0 ){

				$grid 	= "";
				$i		= 0;
				foreach( $insumos as $key => $value ){

					//Como siempre es para la misma historia e ingreso y habitacion, tomo el valor cada vez que pase
					$historia 	= $value['historia'];
					$ingreso 	= $value['ingreso'];
					$ubicacion 	= $value['ccoDescripcion']." Hab. ".$value['habitacion'];
					$wccoori	= $value['ccoori'];
					$turnocx	= $value['turnocx'];
					$resapli    = array(); 

					if(isset($turnocx) && $turnocx > 0 )
					{
						$resapli = aplicarArticuloTurnoCirugia($conex, $wemp_pmla, $wbasedato, $wauxiliar, $value['insumo'], $value['cantidad'], $value['unidadArticulo'], $value['ccoori'], $value['ccodes'], $value['habitacion'], $value['historia'], $value['ingreso'], utf8_decode( $value['justificacion'] ), $fecha, $hora, $turnocx);
					}
					else
					{
						$resapli = aplicarArticulo( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $value['insumo'], $value['cantidad'], $value['unidadArticulo'], $value['ccoori'], $value['ccodes'], $value['habitacion'], $value['historia'], $value['ingreso'], utf8_decode( $value['justificacion'] ), $fecha, $hora );

						//Si fue aplicado actualizo el saldo en matrix por que este también debio cambiar en unix
						actualizarSaldoBotiquin( $conex, $wbasedato, $wccoori, $value['insumo'], $value['cantidad'] );
					}

					if( $resapli['data']['error'] == 0 ){

						if( trim( $value['justificacion'] ) != ''  ){
							$grid .= "*".$value['insumo']."-".str_replace( "*", "x", $value['nombreInsumo'] )."|".$value['cantidad']."|".utf8_decode( str_replace( "*", "x", $value['justificacion'] ) );
							$i++;
						}

						$result[] = $resapli;
					}
				}

				if( $i > 0 ){

					$grid = $i.$grid;

					//Hago esto por si hay que configurar en root_000051
					$datosHCEJustificaciones = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tablaHceJustificacionInsumos" );
					list( $tabla, $conFecha, $conHora, $conUbicacion, $conJustificaciones ) = explode( "-", $datosHCEJustificaciones );

					$conFirma = 1000;	//La firma siempre es en el campo con consecutivo 1000



					//Consulto el rol de la auxiliar
					$rol = consultarRolHCE( $conex, $whce, $wauxiliar );

					//Registrando los datos correspondientes al formulario de justificación de insumos de HCE
					registrarCampoEnHCE( $conex, $whce, $fecha, $hora, $tabla, $conFecha, $historia, $ingreso, 'Fecha', $fecha, $wauxiliar );
					registrarCampoEnHCE( $conex, $whce, $fecha, $hora, $tabla, $conHora, $historia, $ingreso, 'Hora', $hora, $wauxiliar );
					registrarCampoEnHCE( $conex, $whce, $fecha, $hora, $tabla, $conUbicacion, $historia, $ingreso, 'Memo', $ubicacion, $wauxiliar );
					registrarCampoEnHCE( $conex, $whce, $fecha, $hora, $tabla, $conJustificaciones, $historia, $ingreso, 'Grid', $grid, $wauxiliar );
					registrarCampoEnHCE( $conex, $whce, $fecha, $hora, $tabla, $conFirma, $historia, $ingreso, 'Firma', sha1($wfirma), $wauxiliar );

					//Firma Electronica
					registrarFirmaFormularioHCE( $conex, $whce, $fecha, $hora, $tabla, $historia, $ingreso, $wauxiliar, 'on', $rol['codigo'], $wccoori );
				}
			}

			echo @json_encode( $result );
		break;

		case 'aplicarArticulo':
			$result = aplicarArticulo( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $insumo, $cantidad, $unidadArticulo, $ccoori, $ccodes, $habitacion, $historia, $ingreso, utf8_decode( $justificacion ) );
			echo json_encode( $result );
		break;


		case 'desaplicarListaArticulo':

			if( count( $insumos ) > 0 ){

				$grid 	= "";
				$i		= 0;
				foreach( $insumos as $key => $value ){

					//Como siempre es para la misma historia e ingreso y habitacion, tomo el valor cada vez que pase
					$historia 	= $value['historia'];
					$ingreso 	= $value['ingreso'];
					$ubicacion 	= $value['ccoDescripcion']." Hab. ".$value['habitacion'];
					$cco		= $value['ccoori'];
					$id			= $value['movimiento'];
					$cantidad	= $value['cantidad'];
					$turnocx	= $value['turnocx'];

					$datosMovimiento = consultarMovimiento( $conex, $wbasedato, $id );
					$cancelado 		 = desaplicarMovimiento( $conex, $wbasedato, $id );

					if( $cancelado['error'] == 0 ){

						if(isset($turnocx) && $turnocx > 0 ){
							$res = desaplicarArticuloTurnoCirugia( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $value['insumo'], $value['cantidad'], $value['unidadArticulo'], $value['ccoori'], $value['ccodes'], $value['habitacion'], $value['historia'], $value['ingreso'], $justificacion, $turnocx );
						}
						else{
							$res = desaplicarArticulo( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $value['insumo'], $value['cantidad'], $value['unidadArticulo'], $value['ccoori'], $value['ccodes'], $value['habitacion'], $value['historia'], $value['ingreso'], utf8_decode( $value['justificacion'] ) );
						}
						
						//Si fue aplicado actualizo el saldo en matrix por que este también debio cambiar en unix
						actualizarSaldoBotiquin( $conex, $wbasedato, $cco, $value['insumo'], -1*$value['cantidad'] );

						if( $res['data']['error'] == 0 ){

							if( trim( $value['justificacion'] ) != ''  ){
								$grid .= "*".$value['insumo']."-".str_replace( "*", "x", $value['nombreInsumo'] )."|".(-1*$value['cantidad'])."|".utf8_decode( str_replace( "*", "x", $value['justificacion'] ) );
								$i++;
							}

							$result[] = $res;
						}
					}
				}

				//Hago esto por si hay que configurar en root_000051
				$datosHCEJustificaciones = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tablaHceJustificacionInsumos" );
				list( $tabla, $conFecha, $conHora, $conUbicacion, $conJustificaciones ) = explode( "-", $datosHCEJustificaciones );

				$fecha			 = $datosMovimiento['fechaMovimiento'];
				$hora			 = $datosMovimiento['horaMovimiento'];
				$estaJustificado = $datosMovimiento['justificacionMovimiento'] != '' ? true: false;

				if( $estaJustificado ){
					$conNota = 1001;	//La firma siempre es en el campo con consecutivo 1000
					$ultimoCons = consultarConsecutivoNotaHCE( $conex, $whce, $tabla, $fecha, $hora, $historia, $ingreso );
					if( $ultimoCons !== false )
						$conNota = $ultimoCons;

					//Registrando los datos correspondientes al formulario de justificación de insumos de HCE
					$nota = date( "Y-m-d H:i:s" )."<b><u>No se utiliza el insumo ".$datosMovimiento['insumo']."-".$datosMovimiento['nombreGenerico']." (".$cantidad." ".$datosMovimiento['presentacion']." )";
					registrarCampoEnHCE( $conex, $whce, $fecha, $hora, $tabla, $conNota, $historia, $ingreso, 'Nota', $nota, $wauxiliar );
				}

			}

			echo json_encode( $result );
		break;


		case 'validarFirmaElectronica':
			$result = validarFirmaElectronica( $conex, $whce, $usuario, sha1($firma) );
			echo json_encode( $result );
		break;

		case 'consultarAplicaciones':
			$result = consultarAplicaciones( $conex, $wbasedato, $wemp_pmla, $wauxiliar, $whistoria, $wingreso, $wturnocx );
			echo json_encode( $result );
		break;

		default: break;
	}
}
else{	//si no hay ajax

	$cargoUsuario = consultarCargoUsuario( $conex, $wtalhuma, $wemp_pmla, $wusuario );
	$auxiliarEnfermeria = consultarUsuario( $conex, $wusuario );


	$ma = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'mostrarAplicaciones' );
	$ocultarMostrarAplicaciones = $ma == 'on' ? '': 'none';
	
	$ma = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'mostrarAplicacionesCirugia' );
	$ocultarMostrarAplicacionesCirugia = $ma == 'on' ? '': 'none';

?>
<html>
<head>
	<title>APLICACION DE INSUMOS</title>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>

	<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>


	<script  type="text/javascript">

		var Auxiliar = {
			codigo				: "<?=$auxiliarEnfermeria->codigo?>",
			nombre				: "<?=$auxiliarEnfermeria->descripcion?>",
			descripcion			: "<?=$auxiliarEnfermeria->codigo?>-<?=$auxiliarEnfermeria->descripcion?>",
			articulosAsociados 	: {},
			consultarArticulos	: function(){
				consultarArticulosAsociados( this );	//this es el objeto Auxiliar
			},
		}

		//El paciente se setea al seleccionar un paciente en el buscador de pacientes
		var Paciente = {};
		
		function generarSonidoAlerta()
		{
			if($("#audio_fb").length == 0)
			{
				var elemento_audio = '<audio id="audio_fb"><source src="../../images/medical/root/alerta_error.mp3" type="audio/mp3"></audio>';
				$( "body" ).append(elemento_audio);
			}
			$("#audio_fb")[0].play();
		}
		
		//Función similar a BARCOD
		function BARCOD( codigo ){
			
			codigo=$.trim(codigo);
			
			if( codigo.length == 15 ){
				return codigo.substr(2,13);
			}
			else if( codigo.length >= 16 ){
				return codigo.substr(2,14);
			}
			else{
				return codigo;
			}
		}
		
		function buscarInsumoADevolver( cmp ){
			
			$( cmp ).keypress(function(e){
			
				// $( "#lbInsumoLeido" )
					// .html('')
					// .css({display:'none'});
		
				var _valIngresado = $( this ).val();
		
				if( !_valIngresado )
					_valIngresado = '';

				var key =  e.which || e.keyCode;
				
				//Si enter
				if( key == 13 ){
					
					$( "#tbListaInsumosAplicar input[type=text]" ).css({ background: "" });
					
					_valIngresado = BARCOD( _valIngresado ).toUpperCase();
						
					// var insumo = Pedidos.data.insumos[ codigoOriginal ];
					var insumo = false;
					
					var articulos = Paciente.articulos;
					
					//Busco si el codigo existe en la lista de articulos
					for( var x in articulos ){
						if( articulos[x].codigosBarras && articulos[x].codigosBarras[ _valIngresado ] == articulos[x].codigo ){
							insumo = articulos[x];
							break;
						}
					}
					console.log( insumo )
					
					//Si existe el articulo voy campo a campo para sumarle uno al valor de cantidad a devolver
					if( insumo ){
						insumo.cantidadDevolver.val( insumo.cantidadDevolver.val()*1+1 );
						insumo.cantidadDevolver.change();
						insumo.cantidadDevolver.css({ background: "yellow" });
						
						$( "#spMsgInsumo" ).html( "El articulo le&iacute;do es <b>"+insumo.codigo+"-"+insumo.nombreGenercio+"</b>" );
					}
					else{
						// $( "#lbInsumoLeido" )
							// .html( "El insumo con c&oacute;digo <b>"+$( this ).val()+"</b> no se encuentra en la lista de pedidos." )
							// .css({display:''});
						$( "#spMsgInsumo" ).html( "<b>El insumo no se encuentra en la lista</b>." );
						generarSonidoAlerta();
					}
					
					_valIngresado = '';
					
					$( this ).val( '' );
					
					e.stopPropagation();
					
				}
			}).change(function(){ $( this ).val(''); });
		}

		function crearFilaDesaplicar( datos ){
			
			var fila = $(
				"<tr>"
					+"<td style='text-align:center;'>"+datos.fecha+" "+datos.hora+"</td>"
					+"<td style='text-align:center;'>"+datos.historia+"-"+datos.ingreso+"</td>"
					+"<td>"+datos.nombre+"</td>"
					+"<td style='text-align:center;'>"+datos.ubicacion.habitacion+"</td>"
					+"<td>"+datos.insumo.codigo+"-"+datos.insumo.nombreGenercio+"</td>"
					+"<td style='text-align:center;'>"+datos.cantidad+"</td>"
					+"<td style='text-align:center;'><span title='"+datos.insumo.presentacion.descripcion+"'>"+datos.insumo.presentacion.codigo+"</span></td>"
					+"<td style='text-align:center;width:50px;'><a href=# title=' - Desaplicar'><img src='../../images/medical/root/borrar.png'></a></td>"
					+"<td style='text-align:center;width:50px;'><a href='#null' style='display: none;'><img src='/matrix/images/medical/movhos/checkmrk.ico' width='17' height='17'></a></td>"
				+"</tr>"
			);

			$( "[title]", fila ).tooltip({
				showURL: false,
				showBody: ' - ',
			});

			var aDesaplicar 	= $( "a", fila ).eq(0);
			var aVerificacion 	= $( "a", fila ).eq(1);

			aDesaplicar.click(function(){

				jConfirm( "Est&oacute; seguro(a) que desea desaplicar el articulo?", "ALERTA",function( resp ){

					if( resp ){

						$.ajax({
							url		: "./botiquinAplicacionInsumos.php",
							type	: "POST",
							dataType: "json",
							data	: {
								consultaAjax	: 'desaplicarListaArticulo',
								wemp_pmla		: "<?=$wemp_pmla?>",
								wauxiliar		: Auxiliar.codigo,
								insumos			: [{
									insumo			: datos.insumo.codigo,
									cantidad		: datos.cantidad,
									unidadArticulo	: datos.insumo.presentacion.codigo,
									ccoori			: datos.botiquin,
									ccodes			: datos.ubicacion.cco.codigo,
									ccoDescripcion	: datos.ubicacion.cco.descripcion,
									habitacion		: datos.ubicacion.habitacion,
									historia		: datos.historia,
									ingreso			: datos.ingreso,
									movimiento		: datos.movimiento,
									justificacion	: '',
									turnocx			: datos.turnocx,
								}],
							},
							async	: true,
							success	: function(respuesta) {

								if( respuesta[0].error == 0 ){

									aVerificacion.show();
									aDesaplicar.hide();
								}
								else{
									console.log( "Hubo un error al consultar los Articulos asociados" );
									console.log( respuesta );
									jAlert( respuesta.message, "ALERTA" );
								}
							}
						});
					}
				});

			});

			return fila;
		}

		function consultarArticulosDesaplicar( dvArticulo ){

			$.ajax({
				url		: "./botiquinAplicacionInsumos.php",
				type	: "POST",
				dataType: "json",
				data	: {
					consultaAjax	: 'consultarAplicaciones',
					wemp_pmla		: "<?=$wemp_pmla?>",
					wauxiliar		: Auxiliar.codigo,
					whistoria		: Paciente.historia,
					wingreso		: Paciente.ingreso,
					wturnocx		: Paciente.turnocx,
				},
				async	: true,
				success	: function(respuesta) {

					var dvInsumosDesaplicar = $(
							"<div style='width:80%;margin:0 auto;display:none'>"
								+"<h2>ARTICULOS APLICADOS POR PACIENTE<h2>"
								+"<table id='tbDesaplicar' style='width:90%;margin:0 auto;'>"
									+"<thead class='encabezadotabla' style='text-align:center'>"
										+"<td>Fecha y hora<br>de aplicaci&oacute;n</td>"
										+"<td>Historia</td>"
										+"<td>Paciente</td>"
										+"<td>Ubicaci&oacute;n</td>"
										+"<td>Insumo</td>"
										+"<td colspan=2>Cantidad</td>"
										+"<td colspan=2>Acci&oacute;n</td>"
									+"</thead>"
								+"</table>"
								+"<br></center><input type='button' value='cerrar' style='width:100px;'></center>"
							+"</div>" );

					var tb 		= $( "table", dvInsumosDesaplicar );
					var btCerrar = $( "input[type=button]", dvInsumosDesaplicar );

					for( var idx in respuesta.data ){
						var fila = crearFilaDesaplicar( respuesta.data[idx] );
						tb.append( fila );
					}

					$.blockUI({
						message	: dvInsumosDesaplicar,
						css		: {
							width	: '80%',
							margin	: '0 auto',
							left	: '10%',
							top		: '10%',
							height	: '80%',
							overflow: 'auto',
						}
					});

					btCerrar.click(function(){

						if( $( "#tbDesaplicar img[src*=ico]:visible" ).length > 0 ){

							//$( "#dvArticulosAsociados > div:gt(0)" ).remove();
							// Auxiliar.consultarArticulos();

							$.ajax({
								url		: "./botiquinAplicacionInsumos.php",
								type	: "POST",
								dataType: "json",
								data	: {
									consultaAjax: 'consultarArticulosAuxiliar',
									wemp_pmla	: "<?=$wemp_pmla?>",
									wauxiliar	: Auxiliar.codigo,
								},
								async	: true,
								success	: function(respuesta) {

									if( true || respuesta.error == 0 ){
										var data = respuesta;
										for( var idx in data ){
											// agregarArticuloAuxiliar( data[idx] );

											if( data[idx].historia == Paciente.historia && data[idx].ingreso == Paciente.ingreso && data[idx].ubicacion.habitacion == Paciente.ubicacion.habitacion ){

												$( "#tbListaInsumosAplicar > tbody > tr" ).remove();

												Paciente.articulos = data[idx].articulos;

												for( var x in Paciente.articulos )
													agregarArticuloAPaciente( dvArticulo, Paciente.articulos[x] );

												break;
											}
										}
									}
									else{
										console.log( "Hubo un error al consultar los Articulos asociados" );
										console.log( respuesta );
										jAlert( respuesta.message, "ALERTA" );
									}
								}
							});

						}

						$.unblockUI();
					});
				}
			});

		}

		function aplicarListaInsumos( data ){

			$( "#btAplicarInsumos" ).attr({disabled:true});

			$.ajax({
				url		: "./botiquinAplicacionInsumos.php",
				type	: "POST",
				dataType: "json",
				data	: {
					consultaAjax	: 'aplicarListaArticulo',
					wemp_pmla		: "<?=$wemp_pmla?>",
					wauxiliar		: Auxiliar.codigo,
					wfirma			: $( "#pswFirma" ).val(),
					// insumos			: [ { a: "insumo1", b: "insumo2", c: "insumo3", d: "insumo4" } ],
					insumos			: data.insumos,
				},
				async	: true,
				success	: function(respuesta) {
					
					var ok = true;
					var verificacion = true;

					for( var idx in respuesta ){
						verificacion = data.success[idx]( respuesta[idx] );
						if( !verificacion )
							ok = false;
					}
					
					if( ok ){
						jAlert( "LOS INSUMOS FUERON APLICADOS CORRECTAMENTE", "ALERTA" )
					}
					else{
						jAlert( "Uno o más insumos no se aplicaron", "ALERTA" );
					}

					$( "#btAplicarInsumos" ).attr({disabled:false});
				}
			});
		}

		function validarFirmaDigital(){

			//"consultaAjaxKardex=26&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&usuarioHce=" + usuario + "&firma=" + firma+ "&wemp_pmla=" + wemp_pmla;

			$.ajax({
				url		: "./botiquinAplicacionInsumos.php",
				type	: "POST",
				dataType: "json",
				data	: {
					consultaAjax	: 'validarFirmaElectronica',
					wemp_pmla		: "<?=$wemp_pmla?>",
					usuario			: Auxiliar.codigo,
					firma			: $( "#pswFirma" ).val(),
				},
				async	: true,
				success	: function(respuesta){

					var clase = "";
					var mensaje = "";
					var campoFirma = "";

					if( respuesta.data != '' ){

						$( "#pswFirma" ).parent().removeClass( "fondoVerde" );
						$( "#pswFirma" ).parent().removeClass( "fondoRojo" );

						switch(respuesta.data){
							case '1':
								clase = "fondoVerde";
								$( "#btAplicarInsumos" ).attr({disabled: false });
								break;
							case '2':
								clase = "fondoRojo";
								$( "#btAplicarInsumos" ).attr({disabled: true });
								break;
							default:
								clase = "fondoRojo";
								$( "#btAplicarInsumos" ).attr({disabled: true });
								mensaje = "<div class='blink'> &nbsp; Firma Err&oacute;nea </div>";
								break;
						}

						$( "#pswFirma" ).parent().addClass( clase );
					}
				}
			});
		}


		/**
		 *
		 */
		function agregarArticuloAPaciente( filaAux, articulo ){

			var trArticulo = $( "<tr>"
									+"<td>"+articulo.codigo+"-"+articulo.nombreGenercio+"</td>"
									+"<td>"+articulo.saldo+"</td>"
									+"<td><input type='text' value='' onKeyPress='return validarEntradaEntera(event);'></td>"
									+"<td><span title='"+articulo.unidad.descripcion+"'>"+articulo.unidad.codigo+"</span></td>"
									+"<td><input type='text' value='' onKeyPress='return validarEntradaEntera(event);'></td>"
									+"<td><textarea></textarea></td>"
									+"<td><a href=#null><img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17></a></td>"
									+"<td><a href=#null><img src='/matrix/images/medical/movhos/checkmrk.ico' height=17 width=17></a></td>"
									+"<td style='display:none;'><a href=#null><img src='../../images/medical/root/borrar.png' height=17 width=17></a></td>"
								+"</tr>" );

			var msgErrors = [];

			var cantidadAplicar = $( "input", trArticulo ).eq(0);
			var cantidadDevolver= $( "input", trArticulo ).eq(1);
			var justificacion 	= $( "textarea", trArticulo );
			var acciones 		= $( "a", trArticulo );


			//Oculto todas las acciones
			acciones.hide();

			var alertas 	= acciones.eq(0);
			var verificacion= acciones.eq(1);
			var desaplicar 	= acciones.eq(2);
			
			articulo.cantidadAplicar = cantidadAplicar;
			articulo.cantidadDevolver= cantidadDevolver;
			
			if( Paciente.turnocx*1 > 0 ){
				cantidadAplicar.val( articulo.saldo );
				cantidadDevolver.val( 0 );
			}

			cantidadAplicar[0].valor_anterior = 1;
			cantidadAplicar.change(function(){
				
				$( "#tbListaInsumosAplicar input[type=text]" ).css({ background: "" });

				if( articulo.saldo - cantidadAplicar.val()*1 >= 0 ){
					this.valor_anterior = cantidadAplicar.val();
					cantidadDevolver.val( articulo.saldo - cantidadAplicar.val() );
					if( articulo.requiereJustificacion && $( this ).val()*1 >= articulo.cantidadJustificacion ){
						justificacion.attr({disabled:false});
					}
					else{
						justificacion.attr({disabled:true});
					}
				}
				else{
					var msg = "La cantidad a Aplicar excede el saldo disponible."
								+"<br>El saldo disponible es "+articulo.saldo;
					jAlert( msg, "ALERTA",function(){
						cantidadAplicar[0].valor_anterior = articulo.saldo;
						cantidadAplicar.val( articulo.saldo );
						cantidadDevolver.val( articulo.saldo - cantidadAplicar.val() );
						cantidadAplicar.select();
					});
				}

			}).blur(function(){
				if( justificacion.val() == '' )
					justificacion.val( articulo.justificacionDefecto );

				justificacion.focus();
			}).keyup(function(event){
				if ( event.which == 13 ){
					$( "#inBuscadorInsumo" ).focus();
					$( this ).blur();
				}
			});
			
			cantidadDevolver[0].valor_anterior = 0;
			cantidadDevolver.change(function(){

				if( articulo.saldo - cantidadDevolver.val()*1 >= 0 ){
					this.valor_anterior = cantidadDevolver.val();
					cantidadAplicar.val( articulo.saldo - cantidadDevolver.val() );
					if( articulo.requiereJustificacion && $( this ).val()*1 >= articulo.cantidadJustificacion ){
						justificacion.attr({disabled:false});
					}
					else{
						justificacion.attr({disabled:true});
					}
				}
				else{
					var msg = "La cantidad a Devolver excede el saldo disponible."
								+"<br>El saldo disponible es "+articulo.saldo;
					jAlert( msg, "ALERTA",function(){
						cantidadDevolver[0].valor_anterior = articulo.saldo;
						cantidadDevolver.val( articulo.saldo );
						cantidadAplicar.val( articulo.saldo - cantidadDevolver.val() );
						cantidadDevolver.select();
					});
				}

			}).blur(function(){
				if( justificacion.val() == '' )
					justificacion.val( articulo.justificacionDefecto );

				justificacion.focus();
			}).keyup(function(event){
				if ( event.which == 13 ){
					$( "#inBuscadorInsumo" ).focus();
					$( this ).blur();
				}
			});

			//Cómo apenas se está mostrando el articulo siempre off
			//es true una vez se aplique el articulo
			articulo.aplicado = false;	//Indica si el articulo fue aplicado o no

			//Seteo la función aplicar, Esta es la que aplicará el articulo al paciente
			//Se puede llamar por ejemplo desde Paciente.articulo[0].aplicar()
			//Lo mismo ocurre para cualquier funcion creada en esta sección

			articulo.esValido = function(){

				var val = true;

				if( cantidadAplicar.val() != '' ){

					var msg = [];

					if( Paciente && Paciente.historia && Paciente.historia == '' || Paciente.nombre != ''  ){
						msg.push( "Debe seleccionar un Paciente" );
					}

					if( articulo.requiereJustificacion && justificacion.val() == '' && cantidadAplicar.val()*1 >= articulo.cantidadJustificacion ){
						msg.push( "Debe ingresar una justificaci&oacute;n para el insumo" );
						val = false;
					}

					if( isNaN(cantidadAplicar.val()) || cantidadAplicar.val()*1 <= 0 ){
						msg.push( "La cantidad a aplicar debe ser mayor a 0" );
						val = false;
					}

					if( cantidadAplicar.val()*1 > articulo.saldo ){
						msg.push( "La cantidad a aplicar debe ser menor a "+articulo.saldo );
						val = false;
					}

					msgErrors = msg;
				}

				return val;
			}

			articulo.showAlertas = function(){

				 var msg = "";

				 for( var idx in msgErrors ){
					 if( msg != '' )
						 msg += "<br>";
					 msg += msgErrors[idx];
				 }

				 alertas.attr( "title", " - "+msg );
				 alertas.tooltip({
					showURL:false,
					showBody: ' - ',
				});

				alertas.show();
			}

			articulo.aplicar = function(){

				if( articulo.esValido() ){

					if( !articulo.aplicado && cantidadAplicar.val() != '' ){

						$.ajax({
							url		: "./botiquinAplicacionInsumos.php",
							type	: "POST",
							dataType: "json",
							data	: {
								consultaAjax	: 'aplicarArticulo',
								wemp_pmla		: "<?=$wemp_pmla?>",
								wauxiliar		: Auxiliar.codigo,
								insumo			: articulo.codigo,
								cantidad		: cantidadAplicar.val(),
								unidadArticulo	: articulo.unidad.codigo,
								ccoori			: articulo.botiquin.codigo,
								ccodes			: Paciente.ubicacion.cco.codigo,
								ccoDescripcion	: Paciente.ubicacion.cco.descripcion,
								habitacion		: Paciente.ubicacion.habitacion,
								historia		: Paciente.historia,
								ingreso			: Paciente.ingreso,
								turnocx			: Paciente.turnocx,
								justificacion	: justificacion.val(),
							},
							async	: true,
							success	: function(respuesta) {

								if( respuesta.error == 0 ){

									articulo.saldo -= cantidadAplicar.val()*1;

									verificacion.attr({
										title: "Aplicado - "+respuesta.data.fechaAplicacion
											  +" "+respuesta.data.horaAplicacion
											  +"<br>ronda de aplicacion: "+respuesta.data.rondaAplicacion,
									});

									verificacion.tooltip({
										showURL : false,
										showBody: ' - ',
									});
									verificacion.show();


									cantidadAplicar.attr({disabled:true});
									justificacion.attr({disabled:true});

									alertas.hide();
									desaplicar.hide();

									articulo.aplicado = true;
								}
								else{
									console.log( "Hubo un error al consultar los Articulos asociados" );
									console.log( respuesta );
									jAlert( respuesta.message, "ALERTA" );
								}
							}
						});
					}
				}
				else{
					articulo.showAlertas();
				}
			}

			articulo.datosParaAplicarInsumo = function(){

				return {
					data:{
						insumo			: articulo.codigo,
						nombreInsumo	: articulo.nombreGenercio,
						cantidad		: cantidadAplicar.val(),
						unidadArticulo	: articulo.unidad.codigo,
						ccoori			: articulo.botiquin.codigo,
						ccodes			: Paciente.ubicacion.cco.codigo,
						ccoDescripcion	: Paciente.ubicacion.cco.descripcion,
						habitacion		: Paciente.ubicacion.habitacion,
						historia		: Paciente.historia,
						ingreso			: Paciente.ingreso,
						turnocx			: Paciente.turnocx,
						justificacion	: justificacion.val(),
					},
					success: function(respuesta) {

						if( respuesta.error == 0 ){

							articulo.saldo -= cantidadAplicar.val()*1;

							var articulosSinSaldo = 0;
							for( var idx in Paciente.articulos ){
								if( Paciente.articulos[idx].saldo == 0 )
									articulosSinSaldo++;
							}

							if( Paciente.articulos.length == articulosSinSaldo )
								$( filaAux ).addClass( "desactive" );

							verificacion.attr({
								title: "Aplicado - "+respuesta.data.fechaAplicacion
									  +" "+respuesta.data.horaAplicacion
									  +"<br>ronda de aplicacion: "+respuesta.data.rondaAplicacion,
							});

							verificacion.tooltip({
								showURL : false,
								showBody: ' - ',
							});
							verificacion.show();


							cantidadAplicar.attr({disabled:true});
							justificacion.attr({disabled:true});

							alertas.hide();
							desaplicar.hide();

							articulo.aplicado = true;
							
							return true;
						}
						else{
							console.log( "Hubo un error al consultar los Articulos asociados" );
							console.log( respuesta );
							jAlert( respuesta.message, "ALERTA" );
							
							return false;
						}
					}
				}
			}

			//Si requiere cantidad y la cantidad para justificar es 0 o 1 siempre debe mostrar el campo
			//Esto es por qué siempre se tiene que justificar al menos un articulo
			if( !articulo.requiereJustificacion || ( articulo.requiereJustificacion && articulo.cantidadJustificacion > 1 ) ){
				justificacion.attr({disabled: true });
			}

			$( "#tbListaInsumosAplicar" ).append( trArticulo );
		}



		/**
		 *
		 */
		function agregarArticuloAuxiliar( paciente ){

			if(paciente.ubicacion){

				var cirugias = "";
				if( paciente.cirugia.length > 0 ){
					for( var x in paciente.cirugia ){
						if( cirugias == "" )
							cirugias = paciente.cirugia[x];
						else
							cirugias += "<br>"+paciente.cirugia[x];
					}
					
					var txtCirugias = cirugias;
					cirugias = "<b>Turno:</b> "+paciente.turnocx+"<br><br><b>Ciug&iacute;a(s):</b><br>"+cirugias+"";
				}
			
				var dvArticulo = $( "<div>"
										+"<div style='"+( paciente.ubicacion.mensaje != '' ? 'background-color:#FEAAA4' : '' )+"'>"
											+"<span>"+paciente.ubicacion.habitacion+"</span>"
										+"</div>"
										+"<div>"
											+"<span>"+paciente.historia+"-"+paciente.ingreso+"</span>"
										+"</div>"
										+"<div>"
											+"<span>"+paciente.nombre+"</span>"
										+"</div>"
									+"</div>" );

				dvArticulo.click(function(){

					var _self = this;

					if( Paciente != paciente ){

						if( $( "input[value!=],textarea[value!=]", $( "#tbListaInsumosAplicar" ) ).filter(":enabled").length > 0 ){

							jConfirm( "Tiene insumos pendientes por aplicar.<br>Si cambia de paciente perder&aacute; la informaci&oacute;n ingresada.<br>Desea continuar?", "ALERTA", function(resp){
								if(resp){
									cambiarPaciente();
								}
							});
						}
						else{
							cambiarPaciente();
						}
					}

					function cambiarPaciente(){

						if( !$( _self ).hasClass( "desactive" ) ){

							//Debo dejar siempre que se cambie de paciente que la auxiliar
							//escriba la firma
							$( ".fondoVerde" ).removeClass( "fondoVerde" );
							$( "#btAplicarInsumos" ).attr({ disabled: true });
							$( "#pswFirma" ).val( "" );

							$( ".active" ).removeClass( "active" );
							$( dvArticulo ).addClass( "active" );

							$( "#tbListaInsumosAplicar > tbody > tr" ).remove();

							Paciente = paciente;

							$( "#dvInfoPaciente > div" ).html( "" );
							var aAplicaciones = $( "<a style='display:<?=$ocultarMostrarAplicaciones?>' href=#>Mostrar aplicaciones del paciente</a>" );
							if( cirugias != '' )
								aAplicaciones = $( "<a style='display:<?=$ocultarMostrarAplicacionesCirugia?>' href=#>Mostrar aplicaciones del paciente</a>" );

							$( "#dvInfoPaciente > div" ).append( aAplicaciones );
							
							if( cirugias != '' ){
								$( "#dvInfoPaciente > div" ).append( "<br><br><div style='text-align:left;' id='dvInsumosDevolver'>"
																	+"<label><b>Insumo sobrante:</b></label>"
																	+"<INPUT TYPE='text' value='' id='inInsumosDevolver'>"
																	+"<span id='spMsgInsumo'></span>"
																	+"</div>" );
								
								buscarInsumoADevolver( $( "#inInsumosDevolver" ) );
							}

							aAplicaciones.click(function(){
								consultarArticulosDesaplicar( dvArticulo );
							});

							$( ".nombre" ).html( Paciente.nombre );
							$( ".identificacion" ).html( Paciente.tipoDocumento+" "+Paciente.numeroDocumento );
							$( ".ubicacion" ).html( 
								"<b>"+Paciente.ubicacion.cco.nombre+"</b> habitaci&oacute;n <b>"+Paciente.ubicacion.habitacion+"</b>" 
								+ ( cirugias != '' ? " y <b>turno</b> "+paciente.turnocx: "" )
							);
							$( ".historia" ).html( Paciente.historia+"-"+Paciente.ingreso );
							
							if( cirugias != '' ){
								$( ".cirugia" ).parent().parent().css({display:''});
								$( ".cirugia" ).html( txtCirugias );
							}
							else{
								$( ".cirugia" ).parent().parent().css({display:'none'});
							}

							var articulos = paciente.articulos;
							for( var idx in articulos ){
								//Solo agrego articulos que tengan saldo superior a 0
								if( articulos[idx].saldo > 0 )
									agregarArticuloAPaciente( dvArticulo, articulos[idx] );
							}
						}
					}
				});

				if( paciente.ubicacion.mensaje != '' || cirugias != '' ){
					
					var msgTitle = " - <b>"+paciente.ubicacion.mensaje+"</b>";
					if( paciente.ubicacion.mensaje != '' ){
						msgTitle += "<br><br>"+cirugias;
					}
					else{
						msgTitle = " - "+cirugias;
					}
					
					dvArticulo.attr({ title: msgTitle });
					dvArticulo.tooltip({
						showBody: " - ",
						showURL: false,
					})
				}
				

				$( "#dvArticulosAsociados" ).append( dvArticulo );

				return dvArticulo;
			}
		}




		//Metodo del objeto Auxiliar
		/**
		 *
		 */
		function consultarArticulosAsociados( auxiliar ){

			$.ajax({
				url		: "./botiquinAplicacionInsumos.php",
				type	: "POST",
				dataType: "json",
				data	: {
					consultaAjax: 'consultarArticulosAuxiliar',
					wemp_pmla	: "<?=$wemp_pmla?>",
					wauxiliar	: auxiliar.codigo,
				},
				async	: true,
				success	: function(respuesta) {

					if( true || respuesta.error == 0 ){
						var data = respuesta;
						for( var idx in data ){
							agregarArticuloAuxiliar( data[idx] );
						}
					}
					else{
						console.log( "Hubo un error al consultar los Articulos asociados" );
						console.log( respuesta );
						jAlert( respuesta.message, "ALERTA" );
					}
				}
			});
		}






		/************************************************************************************************************************
		 * Función Ready
		 *
		 * Se inicializa funciones princepales
		 ********************************************************************************************************************************/
		$( document ).ready(function(){

			//consulto la axuliar a la que se le cargará los insumos
			$( "#inBuscarPaciente" ).autocomplete({
				source		: "./botiquinAplicacionInsumos.php?wemp_pmla=<?=$wemp_pmla?>&consultaAjax=consultarPaciente",
				minLength	: 3,
				select		: function( event, ui ){
					console.log( ui )
					Paciente = ui.item;

					$( "#dvInfoPaciente" ).html( Paciente.nombre );

					setTimeout( function(){
						$( "#inBuscarPaciente" ).val('');
					}, 300 );
				},
			});

			Auxiliar.consultarArticulos();	//Está funcion realmente llama a la función consultarArticulosAsociados



			//Nueva forma de aplicar
			// ================================================================================
			// ================================================================================
			// ================================================================================
			$( "#btAplicarInsumos" ).click(function(){
				//Si se ha seleccionado paciente
				//Se procede a aplicar los articulos
				if( Paciente && Paciente.historia && Paciente.historia != '' || Paciente.nombre != '' ){

					if( $( "#tbListaInsumosAplicar input[value!=]" ).length > 0 ){

						var val = false;

						var data = {
							insumos: [],
							success: [],
						};

						//En los articulos está la función aplicar
						var insumosAplicar = Paciente.articulos;
						for( var i in insumosAplicar ){

							//Solo tiene en cuenta lo que no se ha aplicado
							if( !insumosAplicar[i].aplicado ){

								if( insumosAplicar[i].esValido() ){
									var a = insumosAplicar[i].datosParaAplicarInsumo();

									if( a.data.cantidad*1 > 0 ){
										data.insumos.push( a.data );
										data.success.push( a.success );
										var val = true;
									}
								}
								else{
									insumosAplicar[i].showAlertas();
									// val = false;
								}
							}
						}

						if( val ){
							aplicarListaInsumos( data );
						}
						else{
							jAlert( "Al menos un insumo debe ser mayor a cero en cantidad para poder aplicar.", "ALERTA" );
						}
					}
					else{
						jAlert( "Al menos un insumo debe ser mayor a cero en cantidad para poder aplicar.", "ALERTA" );
					}
				}
				else{
					jAlert( "No se ha seleccionado un paciente", "ALERTA" );
				}
			}).attr({
				disabled:true,
			});





		});

	</script>

</head>

<style type="text/css">

	body{
		width: auto;
		height: auto;
	}

	* {
	  -webkit-box-sizing: border-box;
		 -moz-box-sizing: border-box;
			  box-sizing: border-box;
	}

	fieldset{
		border: 2px solid #e0e0e0;
		height: auto;
	}

	fieldset > div{
		margin: 0 auto;
		width: 90%;
		font-weight: bold;
	}

	legend{
		background-color: #e6e6e6;
		border-color: -moz-use-text-color #e0e0e0 #e0e0e0;
		border-image: none;
		border-style: none solid solid;
		border-width: 0 2px 2px;
		font-family: Verdana;
		font-size: 11pt;
	}



	#content{
		width: 80%;
		margin: 0 auto;
		display: table;
		height: 80%;
	}

	#content > div {
		display: inline-block;
		width: 45%;
		height: 100%;
		padding: 10px;
		background-color: #fafafa;
	}


	#content > div > div{
		width: 100%;
		float: left;
	}

	#dvArticulosAsociados{
		display: table;
		border-collapse: separate;
		border-spacing: 2px 5px;
		margin: 0 auto;
	}

	#dvArticulosAsociados > div{
		display: table-row;
		margin: 5px 1px;
		border-radius: 10px 10px 10px 10px;
		-moz-border-radius: 10px 10px 10px 10px;
		-webkit-border-radius: 10px 10px 10px 10px;
	}

	#dvArticulosAsociados > div > div{
		display: table-cell;
		padding: 10px 5px;
		background-color: #E8EEF7;
	}

	#dvArticulosAsociados > div > div:first-child{
		border-radius: 10px 0px 0px 10px;
		-moz-border-radius: 10px 0px 0px 10px;
		-webkit-border-radius: 10px 0px 0px 10px;
	}

	#dvArticulosAsociados > div > div:last-child{
		border-radius: 0px 10px 10px 0px;
		-moz-border-radius: 0px 10px 10px 0px;
		-webkit-border-radius: 0px 10px 10px 0px;
	}

	.active{
		-webkit-box-shadow: 2px 2px 5px #999;
		  -moz-box-shadow: 2px 2px 5px #999;
		  filter: shadow(color=#999999, direction=135, strength=2);
	}

	.active > div{
		background-color: #C3D9FF;
		font-weight:  bold;
	}

	.desactive{
		color: gray;
	}

	.title{
		text-align: center;
		font-weight: bold;
		margin: 20px auto;
		font-size: 14pt;
	}



	#dvArticulosAsociados > div {
		margin: 0 auto;
		width: 80%;
	}



	#tbListaInsumosAplicar{
		margin: 20px auto;
		# width: 80%;
		font-size: 10pt;
	}

	#tbListaInsumosAplicar tbody > tr:nth-child(odd){
		background: #E8EEF7;
	}

	#tbListaInsumosAplicar tbody > tr:nth-child(even){
		background-color: #C3D9FF;
	}

	#tbListaInsumosAplicar tbody > tr > td:nth-child(2){
		text-align:center;
	}

	#tbListaInsumosAplicar tbody > tr > td:nth-child(n+6){
		width: 40px;
		text-align: center;
	}

	#tbListaInsumosAplicar input{
		width: 50px;
	}



	#tooltip{
		border-radius: 5px 5px 5px 5px;
		-moz-border-radius: 5px 5px 5px 5px;
		-webkit-border-radius: 5px 5px 5px 5px;
	}

	#tooltip h3{
		font-size: 10pt;
	}

	#popup_title{
		background: #FEAAA4;
		color: black;
	}



	#dvArticulosAsociados > div:nth-child(n+2):hover{
		-webkit-box-shadow: 2px 2px 5px #999;
		  -moz-box-shadow: 2px 2px 5px #999;
		  filter: shadow(color=#999999, direction=135, strength=2);
		  cursor: pointer;
	}



	#tbDesaplicar{
		font-family: verdana;
		font-size: 10pt;
	}

	#tbDesaplicar tbody > tr:nth-child(odd){
		background: #E8EEF7;
	}

	#tbDesaplicar tbody > tr:nth-child(even){
		background-color: #C3D9FF;
	}
	
	#spMsgInsumo{
		background-color: #FFFFCC;
		padding: 5px 15px;
		border-radius: 2px 2px 2px 2px;
		-moz-border-radius: 2px 2px 2px 2px;
		-webkit-border-radius: 2px 2px 2px 2px;
	}
</style>

<body>
<?php

	$wactualiz = "2017-06-21";
	encabezado( "APLICACION DE INSUMOS", $wactualiz, "clinica" );

?>

<form>
	<input type='hidden' name='wemp_pmla' value='<?=$wemp_pmla?>'>

	<div id='content'>

		<!-- Aquí se muestra la información completa correspondiente a la auxiliar -->
		<div style="width: 40%;margin: 0 0 5px;">

			<!-- Información de usuario -->
			<div style='height:100px'>

				<div>
					<fieldset>
						<legend>Usuario</legend>
						<div>
							<table style='width:100%;'>
								<tr>
									<td class='fila1'><b>Cargo</b></td>
									<td class='fila2'><?=$cargoUsuario['descripcion']?></td>
								</tr>
								<tr>
									<td class='fila1'><b>C&oacute;digo y nombre</b></td>
									<td class='fila2'><?=$auxiliarEnfermeria->codigo?>-<?=$auxiliarEnfermeria->descripcion?></td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>

			</div>


			<!-- Lista de pacientes con insumos a aplicar -->
			<div style='clear:both;'>
				<div>
					<div class='title'>PACIENTES CON SALDOS DE INSUMOS</div>
					<div id='dvArticulosAsociados'>

						<div style='text-align:center;font-weight:bold;'>
							<div style='background-color:#2A5DB0;color:white;'>
								<span title='Habitaci&oacute;n'>Hab.</span>
							</div>
							<div style='background-color:#2A5DB0;color:white;'>
								<span>Historia</span>
							</div>
							<div style='background-color:#2A5DB0;color:white;'>
								<span>Nombre</span>
							</div>
						</div>

					</div>
				</div>
			</div>

		</div>


		<!-- Aquí se muestra la información completa correspondiente al paciente -->
		<div style="width: 59%;float: right;margin: 0 0 5px;position:relative;">
			<div>
				<div id='dvInfoPaciente'>

					<fieldset>
						<legend>Paciente seleccionado</legend>
						<div>
							<table style='width:100%;'>
								<tr>
									<td class='fila1'><b>Nombre</b></td>
									<td class='fila2' colspan=3><span class=nombre></span></td>
								</tr>
								<tr>
									<td class='fila1'><b>Identificaci&oacute;n</b></td>
									<td class='fila2'><span class=identificacion></span></td>
									<td class='fila1'><b>Historia</b></td>
									<td class='fila2'><span class=historia></span></td>
								</tr>
								<tr>
									<td class='fila1'><b>Ubicaci&oacute;n</b></td>
									<td class='fila2' colspan=3><span class='ubicacion'></span></td>
								</tr>
								<tr>
									<td class='fila1'><b>Cirug&iacute;a</b></td>
									<td class='fila2' colspan=3><span class='cirugia'></span></td>
								</tr>
							</table>
						</div>
					</fieldset>

					<div style='text-align:right;'></div>

				</div>

				<div id='dvArticulosAplicar' style='overflow: auto;max-height: 400px;'>

					<table id='tbListaInsumosAplicar'>
						<thead class=encabezadotabla>
							<th colspan=8>INSUMOS A APLICAR</th>
						</thead>
						<thead class=encabezadotabla>
							<th style='width:400px'>Insumo</th>
							<th style='width:120px'>Saldo</th>
							<th colspan=2 style='width:120px'>Cantidad</th>
							<th colspan=1 style='width:120px'>Sobrante</th>
							<th>Justificaci&oacute;n</th>
							<th colspan=2 style='width:80px'>Acci&oacute;n</th>
						</thead>
					</table>

				</div>
				<div style="position: absolute;bottom: 0;margin: 0 auto;width: 100%;">
					<table style='margin:0 auto;'>
						<tbody>
							<tr style="height: 70px;">
								<td class="fila1" height="30"> &nbsp; &nbsp; Firma digital &nbsp; </td>
								<td class="fila2" height="30"><input name="pswFirma" placeHolder='Ingrese su firma digital' size="40" maxlength="80" id="pswFirma" value="" class="tipo3" onkeyup="validarFirmaDigital();" type="password"></td>
								<td class='fila1'>
									<!-- El onclick se encuentra como $( "#btAplicarInsumos" ).onClick en el ready -->
									<input type='button' id='btAplicarInsumos' value='Aplicar Insumos al Paciente'>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div style="height:auto;width:100%;padding:10px 0px;text-align:center;">
			<input type='button' value='Cerrar ventana' onClick='cerrarVentana();'>
		</div>

	</div>


</form>

</body>
</html>
<?php
}