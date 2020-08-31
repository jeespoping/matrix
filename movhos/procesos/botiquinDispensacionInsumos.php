<?php
include_once("conex.php"); 

header("Content-Type: text/html;charset=ISO-8859-1"); 

if( empty( $_SESSION['user'] ) ){
	
	if( isset( $consultaAjax ) ){
		echo json_encode( $array = array( 'error'=>'1000', 'message' => 'Sesion caducada' ) );
		die();
	}
	else{
		die( "<h1>Debe ingresar nuevamente al programa por matrix.</h1>" );
	}
}

include_once("root/barcod.php");
include_once("root/comun.php");
include_once("movhos/otros.php");
include_once("movhos/fxValidacionArticulo.php");
include_once("movhos/botiquin.inc.php");
include_once("movhos/cargosSF.inc.php");


$pos			= strpos($user,"-");
$wusuario		= substr($user,$pos+1,strlen($user));

$wuser1			= explode("-",$user);
$wusuario		= trim($wuser1[1]);

$wbasedato		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$whce			= consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
$wtalhuma		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "talhuma" );
$ccoHabilitados	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "ccoHabilitadosDispensacionInsumos" );
$curaciones		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "personalCuraciones" );
$mostarFoto		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "fotoAuxiliares" );
$cierreAutomatico=consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiempoCierreAutomaticoOrdenes" );
$wcliame		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );

if( empty( $cierreAutomatico ) )
	$cierreAutomatico = 10;

$mostarFoto = $mostarFoto == 'on' ? '' : 'none';

$ccoHabilitados	= explode( ",", $ccoHabilitados );
$curaciones		= explode( ",", $curaciones );

$validarSaldoAuxiliar = consultarAliasPorAplicacion( $conex, $wemp_pmla, "validarSaldoAuxiliar" );
$validarSaldoAuxiliar = $validarSaldoAuxiliar == 'on'? true: false;

$cargoUsuario = consultarCargoUsuario( $conex, $wtalhuma, $wemp_pmla, $wusuario );

$botiquines = consultarBotiquines( $conex, $wbasedato );

$botiquin = array(
				'nombre' 		=> '',
				'codigo' 		=> '',
				'descripcion' 	=> '',
				'esAyudaDx' 	=> false,
			);

			
$esAyudaDx = false;			
$ccoDispensables = "[]";
if( isset( $slBotiquin ) ){
	
	foreach( $botiquines as $key => $value ){
		if( $value['codigo'] == $slBotiquin ){
			$botiquin = $value;
			break;
		}
	}
	
	$ccoDispensables = consultarCcoDispensables( $conex, $wbasedato, $botiquin['codigo'] );
	
	$ccoDispensables= json_encode($ccoDispensables);
	$ccoHabilitados	= json_encode($ccoHabilitados);
	$curaciones 	= json_encode($curaciones);
	
	$esAyudaDx = $botiquin['esAyudaDx'];
}


// $botiquin = '1020';

/**************************************************************************************************************
 * BOTIQUIN - DISPENSACION DE INSUMOS
 *
 * Fecha de creación:	2017-05-05
 * Por:					Edwin Molina Grisales
 **************************************************************************************************************/
/**************************************************************************************************************
 * DESCRIPCION:
 *
 * La regente del botiquin le encarga los artículos a las auxiliares de enfermería, quines depues deben
 * devolver los insumos sobrantes u aplicar los insumos correspondientes por paciente.
 **************************************************************************************************************/
 /**************************************************************************************************************
 * MODIFICACIONES:
 *
 * Julio 13 de 2019			Edwin MG	- Se corrige al mostrar el pedido, los campos de cantidad a cargar por paciente (Los insumos se pintaban en el 
  *										  mismo orden que los pacientes pero esto no siempre ocurre )
 * Marzo 28 de 2019			Edwin MG	- Se quita el utf8_decode a los datos que se trae cuando se ingres un auxiliar
 * Marzo 12 de 2018			Edwin MG	- Se piden lotes para insumos implantables
 * Octubre 30 de 2017		Jessica		- Se agrega icono de paquetes con tooltip a los pacientes con paquetes agregados al realizar el pedido (el pedido no 
 *										  necesariamente incluye todos los insumos del paquete).
 *							Edwin MG.	- Permite dispensar sin pedidos para centros de costos de ayudas diagnósticas que no tienen cita
 * Octubre 11 de 2017.		Edwin MG.	- Al dispensar pedidos, sobre la habitación se muesta la historia-ingreso, nombre del paciente e identificación del paciente
 *										- Solo se permite dispensar insumos con pedidos
 *										- Se permite dispensar insumos a pacientes sin ubicación
 *										- En la función consultar pedidos se quita el filtro 'AND Habcco = Pedcco' en la consulta
 * Julio 24 de 2017.		Edwin MG.	- En la funcion consultarAuxiliarEnfermeria se agrega parametro codigoCompleto, para buscar por codigo completo en la consulta de dicha función
 * Julio 13 de 2017.		Edwin MG.	- Se impide dispensar pedidos mientras se edita un pedido.
 * Julio 04 de 2017.		Edwin MG.	- Se hacen cambios varios para la dipsensacion de pedidos de insumos
 * Junio 23 de 2017.		Edwin MG.	- Se permite cargar insumos con saldo negativo si está permitido según la configuración de articulos especiales(movhos_000008)
 * 										- Se modifica el scrip para que muestre la foto de la auxiliar(responsable de insumo).
 **************************************************************************************************************/
/************************************************************************************************************************
 * NOTAS:
 *
 * - La función barcod se encuentra en barcod.php. En este script hay una función similar a esta pero en javascript
 ************************************************************************************************************************/
 
  
function consultarPaquetesPedidos( $conex, $wemp_pmla, $wbasedato,$historia,$ingreso,$codigoPedido,$insumos,$cco)
{
	$queryPaquetesPaciente = "SELECT Dpepaq 
								FROM ".$wbasedato."_000231 
							   WHERE Dpecod='".$codigoPedido."' 
							     AND Dpehis='".$historia."' 
								 AND Dpeing='".$ingreso."' 
								 AND Dpeest='on' 
								 AND Dpeped!='0'
							GROUP BY Dpepaq;";
							 
	$resPaquetesPaciente = mysql_query($queryPaquetesPaciente, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPaquetesPaciente . " - " . mysql_error());		   
	$numPaquetesPaciente = mysql_num_rows($resPaquetesPaciente);
	
	$arrayPaquetePedido = array();
	if($numPaquetesPaciente>0)
	{
		while($rowPaquetesPaciente = mysql_fetch_array($resPaquetesPaciente))
		{
			$paquetes = explode(",",$rowPaquetesPaciente['Dpepaq']);
			for($i=0;$i<count($paquetes);$i++)
			{
				if(!in_array($paquetes[$i],$arrayPaquetePedido))
				{
					$arrayPaquetePedido[$paquetes[$i]] = $paquetes[$i];
				}
			}

		}
		
		$arrayPaquetesPaciente = consultarPaquetes($conex, $wemp_pmla, $wbasedato,$arrayPaquetePedido);
	}
	
	return $arrayPaquetesPaciente;
}

function consultarPaquetes($conex, $wemp_pmla, $wbasedato,$arrayPaquetePedido)
{
	
	$paquetes = implode("','", $arrayPaquetePedido);
	
	$queryPaquetes = "SELECT Paqcod,Paqdes 
						FROM ".$wbasedato."_000241 
					   WHERE Paqcod IN ('".$paquetes."') 
						 AND Paqest='on';";
							 
	$resPaquetes = mysql_query($queryPaquetes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPaquetes . " - " . mysql_error());		   
	$numPaquetes = mysql_num_rows($resPaquetes);
	
	$arrayPaquetes = array();
	if($numPaquetes>0)
	{
		while($rowPaquetes = mysql_fetch_array($resPaquetes))
		{
			$arrayPaquetes[$rowPaquetes['Paqcod']] = utf8_encode($rowPaquetes['Paqdes']);
		}
	}
	
	return $arrayPaquetes;
}
 
//Indica si el pedido está siendo editado o no ( on = editando, off= no esta editando )
function setPedidoEnEntrega( $conex, $wbasedato, $codigo, $estado ){
	
	if( $estado == 'on' ){
		$fecha = date( "Y-m-d" );
		$hora  = date( "H:i:s" );
	}
	else{
		$fecha = "0000-00-00";
		$hora  = "00:00:00";
	}
	
	$val = false;
	
	$sql =  " UPDATE ".$wbasedato."_000230
				 SET Pedeen = '".$estado."',
					 Pedfen = '".$fecha."',
					 Pedhen = '".$hora."'
			   WHERE Pedcod = '".$codigo."';";
	
	$res = mysql_query( $sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );		   
	
	if( mysql_affected_rows() > 0 )
	{
		$val = true;
	}
	
	return false;
}
 
function hayPedidosEnEdicion( $conex, $wemp_pmla, $wbasedato, $auxiliar, $botiquin ){
	
	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array(),
	);	
	
	// $tiempo = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiempoEntregaHacerPedidos" );
	
	// $fecha = date( "Y-m-d", time()-$tiempo*60 );
	// $hora  = date( "H:i:s", time()-$tiempo*60 );
	
	$sql = "SELECT Pedcod
			  FROM ".$wbasedato."_000230
			 WHERE Pedaux = '".$auxiliar."'
			   AND Pedbot = '".$botiquin."'
			   AND Pededi = 'on'
			   AND Pedest = 'on'
			 ";
			   // AND ( Pedfed > '".$fecha."' 
			    // OR ( Pedfed = '".$fecha."' AND Pedhed >= '".$hora."' ) )
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$val['error'] = 1;
		$val['message'] = "Actualmente se encuentra editando el pedido ";
		
		while( $rows = mysql_fetch_array( $res ) ){
			$val['data']['codigoPedidos'][] = $rows['Pedcod'];
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
 
function consultarPedidosBotiquin( $conex, $wemp_pmla, $wbasedato, $aux, $botiquin ){
	
	global $bd;
	
	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array(),
	);	
	
	$bd = $wbasedato;

	// $sql = "SELECT Dpecod, Dpehis, Dpeing, Habcod, Dpeins, Dpeped-Dpedis as Dpeped, Artcom, Artgen, Artuni, Ccocod, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pactid, Pacced
			  // FROM ".$wbasedato."_000230 a, ".$wbasedato."_000231 b, ".$wbasedato."_000020 c, ".$wbasedato."_000026 d, ".$wbasedato."_000011 e, root_000036 f, root_000037 g
			 // WHERE Pedaux = '".$aux."'
			   // AND Pedbot = '".$botiquin."'
			   // AND Pedest = 'on'
			   // AND Pedent = 'off'
			   // AND Dpecod = Pedcod
			   // AND Dpeest = 'on'
			   // AND Habhis = Dpehis
			   // AND Habing = Dpeing
			   // AND Habcco = Pedcco
			   // AND Artcod = Dpeins
			   // AND Ccocod = Habcco
			   // AND Orihis = Habhis
			   // AND Oriori = '".$wemp_pmla."'
			   // AND Oriced = pacced
			   // AND Oritid = pactid
			   // AND Dpeped-Dpedis > 0
		  // ORDER BY Habcod
			// ";
			
	$sql = "SELECT Dpecod, Dpehis, Dpeing, Ubihac as Habcod, Dpeins, Dpeped-Dpedis as Dpeped, Artcom, Artgen, Artuni, Ccocod, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pactid, Pacced, Arteim
			  FROM ".$wbasedato."_000230 a, ".$wbasedato."_000231 b, ".$wbasedato."_000018 c, ".$wbasedato."_000026 d, ".$wbasedato."_000011 e, root_000036 f, root_000037 g
			 WHERE Pedaux = '".$aux."'
			   AND Pedbot = '".$botiquin."'
			   AND Pedest = 'on'
			   AND Pedent = 'off'
			   AND Dpecod = Pedcod
			   AND Dpeest = 'on'
			   AND Ubihis = Dpehis
			   AND Ubiing = Dpeing
			   AND Artcod = Dpeins
			   AND Ccocod = Ubisac
			   AND Orihis = Ubihis
			   AND Oriori = '".$wemp_pmla."'
			   AND Oriced = pacced
			   AND Oritid = pactid
			   AND Dpeped-Dpedis > 0
		  ORDER BY Habcod
			";

	$res = mysql_query( $sql, $conex );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		
		$val['data']['codigosBarra'] = array();
		$val['data']['codigoPedidos'] = array();
		
		while( $rows = mysql_fetch_array( $res ) ){
			
			setPedidoEnEntrega( $conex, $wbasedato, $rows['Dpecod'], 'on' );
			
			if( empty( $rows['Habcod'] ) ){
				$rows['Habcod'] = $rows['Dpehis']."-".$rows['Dpeing'];
			}
			
			if( !in_array( $rows['Dpecod'], $val['data']['codigoPedidos'] ) )
				$val['data']['codigoPedidos'][] = $rows['Dpecod'];
			
			$art = array( 'cod' => $rows['Dpeins'] );
			ArticulosEspeciales( array( 'cod'=>$botiquin ), $art );
			$permiteNegativos = $art['neg'];
			
			if( isset( $val['data']['Pedidos'][$rows['Habcod']]['insumos'][$rows['Dpeins']] ) )
				continue;
			
			$codigosBarra = consultarCodigosBarra( $conex, $wbasedato, $rows['Dpeins'] );
			
			//Uno todos los codigo de barra
			// $val['data']['codigosBarra'] = array_merge( $val['data']['codigosBarra'], $codigosBarra );
			// $val['data']['codigosBarra'] = $codigosBarra;
			$val['data']['codigosBarra'] += $codigosBarra;
			
			$insumo = array(
				'codigo' 			=> $rows['Dpeins'],
				'nombreGenerico' 	=> utf8_encode( $rows['Artgen'] ),
				'nombreComercial' 	=> utf8_encode( $rows['Artcom'] ),
				'presentacion' 		=> utf8_encode( $rows['Artuni'] ),
				'saldo' 			=> consultarSaldoEnBotiquinMatrix( $conex, $wbasedato, $rows['Dpeins'], $botiquin ),
				'permiteNegativos'	=> $permiteNegativos,
				'implantable'		=> $rows['Arteim'] == 'on' ? true: false,
			);
			
			$paciente = array(
				'nombre'			=> utf8_encode( trim( $rows['Pacno1']." ".$rows['Pacno2'])." ".trim( $rows['Pacap1']." ".$rows['Pacap2'] ) ),
				'tipoDocumento'		=> $rows['Pactid'],
				'numeroDocumento'	=> $rows['Pacced'],
				'historia' 			=> $rows['Dpehis'],
				'ingreso' 			=> $rows['Dpeing'],
				'habitacion' 		=> $rows['Habcod'],
				'cco' => array(
					'codigo' 		=> $rows['Ccocod'],
					'nombre' 		=> utf8_encode( $rows['Cconom'] ),
					'descripcion' 	=> $rows['Ccocod']."-".utf8_encode( $rows['Cconom'] ),
				),
			);
			
			
			// //Lista de habitaciones
			// if( empty( $val['data']['habitaciones'] ) || !in_array( $rows['Habcod'], $val['data']['habitaciones'] ) ){
				// $val['data']['habitaciones'][] = $rows['Habcod'];
			// }
			
			
			//Lista de pacientes por insumos
			$val['data']['insumos'][$rows['Dpeins']]['insumo'] 	 = $insumo;
			$val['data']['insumos'][$rows['Dpeins']]['pedido'][] = array( 
				'paciente' => $paciente, 
				'cantidadSolicitada' => $rows['Dpeped'] 
			);
			
			//Lista de insumos Por paciente
			$insumo['cantidadSolicitada'] = $rows['Dpeped'];
			if( !isset($val['data']['Pedidos'][$rows['Habcod']]) ){
				$val['data']['Pedidos'][$rows['Habcod']] = array(
					'codigoPedido'			=> $rows['Dpecod'],
					'Paciente'				=> $paciente,
				);
			}
			
			//Lista de habitaciones
			if( empty( $val['data']['habitaciones'] ) || !in_array( $rows['Habcod'], $val['data']['habitaciones'] ) ){
				$val['data']['habitaciones'][] = $rows['Habcod'];
				
				$paquetesPedidos = consultarPaquetesPedidos( $conex, $wemp_pmla, $wbasedato,$rows['Dpehis'],$rows['Dpeing'],$rows['Dpecod'],$insumo,$rows['Ccocod']);
				
				if(count($paquetesPedidos)>0)
				{
					$val['data']['paquetes'][$rows['Habcod']] = $paquetesPedidos;
				}
			}
			
			
			$val['data']['Pedidos'][$rows['Habcod']]['insumos'][$rows['Dpeins']] = $insumo;
		}
	}
	
	return $val;
}
 
/****************************************************************************************************************
 * Consulta el nombre del paciente por historia, habitación, nro de cedula o nombre según el parametro filtro
 ****************************************************************************************************************/
function consultarInsumosAuxiliarPorPaciente( $conex, $wbasedato, $wemp_pmla, $wauxiliar, $historia, $ingreso ){
	
	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> '',
	);	

	$sql = "SELECT Carbot, Cconom, Carhis, Caring, Artcod, Artcom, Artgen, Artuni, Unides, SUM(Carcca) as Carcca, SUM(Carcap) as Carcap, SUM(Carcde) as Carcde
			  FROM ".$wbasedato."_000227 a, ".$wbasedato."_000026 b, ".$wbasedato."_000027 c, ".$wbasedato."_000011 d
			 WHERE Caraux = '".$wauxiliar."'
			   AND Carins = Artcod
			   AND Carcca-Carcap-Carcde > 0
			   AND Artuni = Unicod
			   AND Carest = 'on'
			   AND Cartra = 'on'
			   AND Carbot = Ccocod
			   AND Carhis = '".$historia."'
			   AND Caring = '".$ingreso."'
		  GROUP BY 1,2,3,4,5,6,7,8,9
			";

	$res = mysql_query( $sql, $conex );
	$num = mysql_num_rows( $res );

	if( $res ){
		
		$val['message'] 		= "Total de articulos encontrados: ".$num;
		$val['totalArticulos'] 	= $num;
		
		$paciente = array();	//Esta es la información del paciente
		while( $rows = mysql_fetch_array( $res ) ){
			
			//Quito los espacios a todos los campos
			foreach( $rows as &$value ){
				$value = trim( $value );
			}
			
			$val['data'][] = array(
				'botiquin' 			=> array(
					'codigo' 		=> $rows['Carbot'],
					'nombre' 		=> utf8_encode( $rows['Cconom'] ),
				),
				'codigo' 			=> $rows['Artcod'],
				'nombreComercial'	=> utf8_encode( $rows['Artcom'] ),
				'nombreGenercio' 	=> utf8_encode( $rows['Artgen'] ),
				'unidad' 			=> array(
					'codigo' 		=> $rows['Artuni'],
					'descripcion' 	=> utf8_encode( $rows['Unides'] ),
				),
				'saldo' 			=> $rows['Carcca']-$rows['Carcap']-$rows['Carcde'],
			);
		}
	}
	else{
		$val[ 'error' ] 	= 1;
		$val[ 'message' ] 	= utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	}

	return $val;
}


/****************************************************************************************************************
 * 												FUNCIONES
 ****************************************************************************************************************/
 
function consultarCcoDispensables( $conex, $wbasedato, $cco ){
	
	$val = array();
	
	$sql = "SELECT b.Ccodes
			  FROM ".$wbasedato."_000058 b
			 WHERE b.ccoori = '".$cco."'
			   AND b.ccoest = 'on'
			";
	
	$res = mysql_query( $sql, $conex );
	
	$val[] = $cco;
	
	if( $res ){
		
		while( $rows = mysql_fetch_array($res) ){
			$val[] = $rows['Ccodes'];
		}
	}
	else{
		echo mysql_errno()." - Error en el query $sql - ".mysql_error();
	}
	
	return $val;
}


 
function consultarBotiquines( $conex, $wbasedato ){
	
	$val = array();
	
	$sql = "SELECT a.Ccocod, a.Cconom, a.Ccoayu
			  FROM ".$wbasedato."_000058 b, ".$wbasedato."_000011 a
			 WHERE b.ccoori = a.ccocod
			   AND b.ccoest = 'on'
			   AND a.ccoest = 'on'
		  GROUP BY 1,2
			";
	
	$res = mysql_query( $sql, $conex );
	
	if( $res ){
		
		while( $rows = mysql_fetch_array($res) ){
			$val[] = array(
				'nombre' 		=> $rows['Cconom'],
				'codigo' 		=> $rows['Ccocod'],
				'descripcion' 	=> $rows['Ccocod']."-".$rows['Cconom'],
				'esAyudaDx' 	=> $rows['Ccoayu'] == 'on' ? true: false,
			);
		}
	}
	else{
		echo mysql_errno()." - Error en el query $sql - ".mysql_error();
	}
	
	return $val;
}



function consultarSaldoEnBotiquinUnix( $conex_o, $art, $cco ){
	
	$saldo = 0;

	/*Primero buscar si esta matriculado en el centro de costoss (ivsal) y si tiene tarifa (ivtar)*/
	$sql = "SELECT salant, salent, salsal 
			  FROM ivsal,ivarttar 
			 WHERE arttarcod ='".strtoupper($art['cod'])."' 
			   AND salano = '".date('Y')."' 
			   AND salmes = '".date('m')."' 
			   AND salser = '".$cco."' 
			   AND salart = '".strtoupper($art)."'  ";
			   
	$err_o= odbc_do($conex_o,$sql);
	
	if(odbc_fetch_row($err_o))
	{
		$saldo = odbc_result($err_o,1)+odbc_result($err_o,2)-odbc_result($err_o,3);
	}
	
	return $saldo;
}

 
/*********************************************************************************************************
 *
 *********************************************************************************************************/
function actualizarCargoInsumo( $conex, $wbasedato, $wcco, $wauxiliar, $warticulo, $wcantidad, $whis, $wing ){
	
	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array(),
	);

	$fecha 	= date( "Y-m-d" );
	
	$sql = "UPDATE ".$wbasedato."_000227
			   SET Carcca = Carcca+$wcantidad,
			       Cartra = 'on'
			 WHERE Carbot = '".$wcco."'
			   AND Caraux = '".$wauxiliar."'
			   AND Carhis = '".$whis."'
			   AND Caring = '".$wing."'
			   AND Carins = '".$warticulo."'
			   AND Carfec = '".$fecha."'
			";

	$res = mysql_query( $sql, $conex );

	if( $res ){
		if( mysql_affected_rows() > 0 ){
			$val[ 'message' ] 	= "Articulo actualizado correctamente";
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

	return $val;
}


/*********************************************************************************************************
 *
 *********************************************************************************************************/
function registrarCargoInsumo( $conex, $wbasedato, $wcco, $wauxiliar, $warticulo, $wcantidad, $whis, $wing ){
	
	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array(),
	);

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	
	$sql = "INSERT INTO 
			".$wbasedato."_000227(     Medico      , Fecha_data , Hora_data ,   Carbot  ,    Caraux      ,   Carhis   ,  Caring    ,     Carins     ,   Carfec   ,     Carcca     , Carcap, Carcde, Cartra, Carest,       Seguridad    )
						   VALUES( '".$wbasedato."','".$fecha."','".$hora."','".$wcco."','".$wauxiliar."', '".$whis."', '".$wing."','".$warticulo."','".$fecha."','".$wcantidad."',  '0'  ,   '0' ,  'on' , 'on'  , 'C-".$wbasedato."' )
			";

	$res = mysql_query( $sql, $conex );

	if( $res ){
		if( mysql_affected_rows() > 0 ){
			$val[ 'message' ] 	= "Articulo cargado correctamente";
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

	return $val;
}

/*********************************************************************************************************
 *
 *********************************************************************************************************/
function cargarInsumo( $conex, $wbasedato, $wcliame, $wcco, $wauxiliar, $warticulo, $wcantidad, $usuario, $whis, $wing, $wccopac, $whabpac, $wlotes = array() ){
	
	global $conex_o;
	global $bd;
	
	$bd = $wbasedato;
	
	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array(
							'accion'	=> '',
							'fecha'		=> '',
							'hora'		=> '',
							'auditoria'	=> '',
						),
	);
	
	$arIns = array( 'cod'=>$warticulo, 'can' => $wcantidad );
	
	ArticulosEspeciales( array( 'cod'=>$wcco ), $arIns );
	$arIns['can'] = $wcantidad;
	
	//connectOdbc(&$conex_o, 'inventarios');

	if( false && $conex_o ){
		$saldo = TarifaSaldo( $arIns, array( 'cod'=>$wcco ), 'C', false, $error );
	}
	else{
		$saldo = TarifaSaldoMatrix( $arIns, array( 'cod'=>$wcco ), 'C', false, $error );
	}

	if( $saldo ){
		
		$fecha 	= date( "Y-m-d" );
		$hora 	= date( "H:i:s" );
		
		$sql = "SELECT *
				  FROM ".$wbasedato."_000227
				 WHERE Carbot = '".$wcco."'
				   AND Caraux = '".$wauxiliar."'
				   AND Carhis = '".$whis."'
				   AND Caring = '".$wing."'
				   AND Carins = '".$warticulo."'
				   AND Carfec = '".$fecha."'
				";

		$res = mysql_query( $sql, $conex );
		$num = mysql_num_rows( $res );

		if( $res ){
			
			if( $num > 0 ){
				$val = actualizarCargoInsumo( $conex, $wbasedato, $wcco, $wauxiliar, $warticulo, $wcantidad, $whis, $wing );
				$val['data']['accion'] = 'actualizado';
			}
			else{
				$val = registrarCargoInsumo( $conex, $wbasedato, $wcco, $wauxiliar, $warticulo, $wcantidad, $whis, $wing );
				$val['data']['accion'] = 'registrado';
			}
			
			$val['data']['fecha'] 	= $fecha;
			$val['data']['hora'] 	= $hora;
			
			if( $val['error'] == 0 ){
				
				$wmovimiento = 'CR';
				$justificacion = '';
				
				//Si la habitación es igual a la historia e ingreso, significa que el paciente está sin ubicación
				$whabpac = $whabpac == $whis."-".$wing ? '' : $whabpac;
				
				$val[ 'data' ]['auditoria'] = registrarAuditoriaCargoInsumo( $conex, $wbasedato, $wcco, $wauxiliar, $warticulo, $fecha, $wmovimiento, $whis, $wing, $wccopac, $whabpac, $wcantidad, $usuario, $justificacion );
				
				if( is_array( $wlotes ) && count($wlotes) > 0 ){
					
					foreach( @$wlotes as $lote => $canLote ){
						
						if( $canLote > 0 ){
							
							$turno 		= '';
							$devolucion = 0;
							$estado 	= 'on';
							$pda 		= 'on';
							
							//Consulto el médico tratante
							$med 		= consultar_MedicoTratante($conex, $wbasedato, $whis, $wing, date("Y-m-d") );
							
							//Si no hay médico trante el día actual busco el del día anterior
							if( empty( $med ) ){
								$med 	= consultar_MedicoTratante($conex, $wbasedato, $whis, $wing, date("Y-m-d", time()-24*3600 ) );
							}
							
							registrarLote( $conex, $wcliame, $turno, $warticulo, $lote, $canLote, $devolucion, $fecha, $hora, $usuario, $estado, $whis, $wing, $pda, $wcco, $med, $wauxiliar );
						}
					}
				}
			}
		}
		else{
			$val[ 'error' ] 	= 1;
			$val[ 'message' ] 	= mysql_errno()." - Error en el query $sql - ".mysql_error();
		}
	}
	else{
		$val[ 'error' ] 	= 1;
		$val[ 'message' ] 	= "No hay saldo suficiente para cargar la cantidad solicitada";
	}

	return $val;
	
}



/********************************************************************************************************
 * Consulta la información de la auxiliar de enfermería
 *
 * Julio 24 de 2017			Edwin MG	Se agregar parametro codigoCompleto
 ********************************************************************************************************/
function consultarAuxiliarEnfermeria( $conex, $wemp_pmla, $wbasedato, $whce, $aux, $botiquin, $codigoCompleto = false ){
	
	$val = array(
		'error' 	=> 0,
		'message' 	=> '',
		'data' 		=> array(),
	);
	
	$rolesAuxiliar = consultarAliasPorAplicacion( $conex, $wemp_pmla, "rolesAuxiliarEnfermeria" );
	$rolesAuxiliar = "'".str_replace( ",", "','", trim( $rolesAuxiliar  ) )."'";
	
	$wtalhuma		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "talhuma" );
	
	$filtro = "OR codigo LIKE '%".$aux."%'";
	if( $codigoCompleto ){
		$filtro = "OR codigo LIKE '".$aux."'";
	}
			
	$sql = "SELECT Codigo, Descripcion, Rolcod, Roldes
			  FROM ".$whce."_000020 a, ".$whce."_000019 b, usuarios
			 WHERE codigo = usucod
			   AND usurol = rolcod
			   AND rolest = 'on'
			   AND usuest = 'on'
			   AND activo = 'A'
			   AND rolcod IN ( ".$rolesAuxiliar.")
			   AND (descripcion LIKE '%".$aux."%'
			    $filtro
			   )
			";
	
	$res = mysql_query( $sql, $conex );
	
	if( $res ){
		
		while( $rows = mysql_fetch_array($res) ){
			
			$wempresa = $wemp_pmla;
			//últimos 5 digitos
			$wcodigo = substr( $rows['Codigo'], -5 );
			
			if( strlen( $rows['Codigo'] ) > 5 ){
				$wempresa = substr( $rows['Codigo'], 0, 2 );
			}
			
			//Consulto el saldo pendiente que debe la enfermera los últimos con más de dos días
			$saldoPendiente = consultarSaldoAuxiliar( $conex, $wbasedato, time()-24*3600, $rows['Codigo'], $botiquin );
			
			$val[ 'data' ][] = array(
				'codigo' 			=> $rows['Codigo'],
				'nombre'			=> utf8_encode( $rows['Descripcion'] ),
				'rol'				=> array( 
					'codigo' 		=> utf8_encode( $rows['Rolcod'] ),
					'nombre' 		=> utf8_encode( $rows['Roldes'] ),
					'descripcion' 	=> utf8_encode( $rows['Rolcod']."-".$rows['Roldes'] ),
				),
				'value'  			=> utf8_encode($rows['Codigo']."-".$rows['Descripcion'] ),
				'label'  			=> utf8_encode($rows['Codigo']."-".$rows['Descripcion'] ),
				'saldoPendiente'	=> $saldoPendiente*1,
				'foto'				=> consultarFoto($conex,$wemp_pmla,$wtalhuma,$wcodigo,$wempresa), //'../../images/medical/tal_huma/1020422615.jpg',
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


if( $consultaAjax ){	//si hay ajax

	switch( $consultaAjax ){
		
		case 'consultarAuxiliar':
			$result = consultarAuxiliarEnfermeria( $conex, $wemp_pmla, $wbasedato, $whce, $term, $bot );
			echo json_encode( $result['data'] );
		break;

		case 'consultarInsumo':
			
			if( !isset( $validarSaldo ) )
				$validarSaldo = true;
			
			if( $validarSaldo == 'false' ){
				$validarSaldo = false;
			}
			
			$result = consultarInsumo( $conex, $wbasedato, $term, $bot, $validarSaldo );
			
			echo json_encode( $result );
		break;
		
		case 'cargarInsumo':
			$result = cargarInsumo( $conex, $wbasedato, $wcliame, $wcco, $wauxiliar, $warticulo, $wcantidad, $wusuario, $whis, $wing, $wccopac, $whabpac, $wlotes );
			echo json_encode( $result );
		break;
		
		case 'consultarInsumosAuxiliarPorPaciente':
			$result = consultarInsumosAuxiliarPorPaciente( $conex, $wbasedato, $wemp_pmla, $wauxiliar, $whistoria, $wingreso );
			echo json_encode( $result );
		break;
		
		case 'consultarPedidos':
			
			$result = hayPedidosEnEdicion( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $wbotiquin );
			if( $result['error'] == 0 ){
				$result = consultarPedidosBotiquin( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $wbotiquin );
			}
			echo json_encode( $result );
		break;
		
		case 'cargarPedido':
		
			function marcarPedidoComoEntregado( $conex, $wbasedato, $wcod ){
				
				$val = false;
				
				$sql = "UPDATE ".$wbasedato."_000230
						   SET Pedent = 'on'
						 WHERE Pedcod = '".$wcod."'
						";
				
				$res = mysql_query( $sql, $conex );
				
				if( $res ){
					if( mysql_affected_rows() > 0 ){
						$val = true;
					}
				}
				else{
					
				}
				
				return $val;
			}
			
			function actualizarCantidadCargada( $conex, $wbasedato, $wcod, $whis, $wing, $wins, $wcan ){
				
				$val = false;
				
				$sql = "UPDATE ".$wbasedato."_000231
						   SET Dpedis = '".$wcan."'
						 WHERE Dpecod = '".$wcod."'
						   AND Dpehis = '".$whis."'
						   AND Dpeing = '".$wing."'
						   AND Dpeins = '".$wins."'
						";
				
				$res = mysql_query( $sql, $conex );
				
				if( $res ){
					if( mysql_affected_rows() > 0 ){
						$val = true;
					}
				}
				else{
					
				}
				
				return $val;
			}
		
		
			function cargarPedido( $conex, $wbasedato, $wcliame, $wusuario ){
				
				$result = array();
				
				$codsPedidos = array();
				
				$wcco 		= $_POST['wcco'];
				$wauxiliar	= $_POST['wauxiliar'];
				$wdatos		= $_POST['wdatos'];
				
				foreach( $wdatos as $key => $value ){
					
					$res = array();
					
					$warticulo 		= $value['warticulo']; 
					$wcantidad		= $value['wcantidad']; 
					$whis 			= $value['whis'];  
					$wing 			= $value['wing'];  
					$wccopac 		= $value['wccopac']; 
					$whabpac 		= $value['whabpac']; 
					$wcodigoPedido	= $value['wcodigoPedido']; 
					$wlotes			= $value['wlotes']; 
					
					if( !in_array( $wcodigoPedido, $codsPedidos ) ){
						$codsPedidos[] = $wcodigoPedido;
					}
					
					$res = cargarInsumo( $conex, $wbasedato, $wcliame, $wcco, $wauxiliar, $warticulo, $wcantidad, $wusuario, $whis, $wing, $wccopac, $whabpac, $wlotes );
					$res['insumo'] = $warticulo;
					
					$result[$whabpac][] = $res;
					
					//Actualizo la cantidad cargada en el pedido
					actualizarCantidadCargada( $conex, $wbasedato, $wcodigoPedido, $whis, $wing, $warticulo, $wcantidad );
					
					setPedidoEnEntrega( $conex, $wbasedato, $wcodigoPedido, 'off' );
					// $result[$whabpac]['cantidadActualizada'] = actualizarCantidadCargada( $conex, $wbasedato, $wcodigoPedido, $whis, $wing, $warticulo, $wcantidad );
				}
				
				//Marco el pedido como entregado
				if( count($codsPedidos) > 0 ){
					foreach( $codsPedidos as $key => $value ){
						marcarPedidoComoEntregado( $conex, $wbasedato, $value );
					}
				}
				
				return $result;
			}
		
			
			$result = hayPedidosEnEdicion( $conex, $wemp_pmla, $wbasedato, $wauxiliar, $wcco );
			if( $result['error'] == 0 ){
				$result = cargarPedido( $conex, $wbasedato, $wcliame, $wusuario );
			}

			echo json_encode( $result );
		break;
		
		case 'cambiarEstadoEnEntrega':
			setPedidoEnEntrega( $conex, $wbasedato, $codPedido, $estado );
		break;
	}
}
else{	//si no hay ajax
?>
<html>
<head>
	<title>BOTIQUIN - DISPENSACION DE INSUMOS</title>
	
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

	var ccoDispensables = <?=$ccoDispensables?>;
	var ccoHabilitados 	= <?=$ccoHabilitados?>;
	var curaciones 		= <?=$curaciones?>;
	var esAyudaDx 		= <?php echo $esAyudaDx ? 'true': 'false'; ?>;

	var Paciente = {};

	var auxiliar = {
		codigo		: "",
		nombre		: "",
		descripcion	: "",
		foto	: "",
		articulos	: [],
		set			: function( codigo, nombre, foto ){
			this.codigo 	= codigo;
			this.nombre 	= nombre;
			this.descripcion= codigo+"-"+nombre;
			this.foto		= foto;
		},
		unset		: function(){
			this.codigo 	= "";
			this.nombre 	= "";
			this.descripcion= "";
		}
	}
	
	Pedidos = null;
	
	var listaArticulos = {};
	
	function agregarLotePorInsumo( articulo ){
		
		$( "#ltLotes" ).remove();
		$( "#canLote" ).remove();
		$( "#lote" ).remove();
		
		var options = "";
		if( listaArticulos.arts[ articulo.codigo ] ){
			if( listaArticulos.arts[ articulo.codigo ].lotes ){
				for( var x in listaArticulos.arts[ articulo.codigo ].lotes ){
					options += "<option value='"+x+"'>"
				}
			}
		}
		
		$( "<div style='text-align:center;'>"
			  +"<div style='padding: 10px;'>"
				+"<b>"+articulo.nombreGenerico+"</b>"
			  +"</div>"
			  +"<div style='padding: 10px;'>"
				+"<label style='width:100px;font-weight:bold;'>Cantidad: </label><input type='number' name='canLote' id='canLote' style='width:50px;' value='1' min="+(articulo.saldo*-1)+" max="+articulo.saldo+"></input>"
			  +"</div>"
			  +"<div style='padding: 10px;'>"
				+"<label style='width:100px;font-weight:bold;'>Lote: </label><input type='text' list=ltLotes  name='lote' id='lote' style='width:100px;' value='' autocomplete=off></input>"
				+"<datalist id=ltLotes>"
					+options
				+"</datalist>"
			  +"</div>"
		  +"</div>" ).dialog({
			title	: "Diligenciar el lote",
			modal	: true,
			buttons	: {
				"Aceptar" : function(){
					var lote 	= $( "[name=lote]", this ).val();
					var can 	= $( "[name=canLote]", this ).val()*1;
					var valido  = true;
					var msg 	= '';
					
					if( lote!= '' ){
						
						if( listaArticulos.arts[ articulo.codigo ] ){
							if( listaArticulos.arts[ articulo.codigo ].lotes[ lote ] ){
								
								// if( listaArticulos.arts[ articulo.codigo ].lotes[ lote ] < can ){
									// msg = "La cantidad no puede ser mayor a "+listaArticulos.arts[ articulo.codigo ].lotes[ lote ];
									// valido = false;
								// }
								
								if( listaArticulos.arts[ articulo.codigo ].lotes[ lote ]+can < 0 ){
									msg = "La cantidad no puede ser menor a "+( listaArticulos.arts[ articulo.codigo ].lotes[ lote ]*-1 );
									valido = false;
								}
							}
							else if( can < 0 ){
								msg = "La cantidad debe ser mayor a 0 ";
								valido = false;
							}
						}
						else if( can < 0 ){
							msg = "La cantidad debe ser mayor a 0 ";
							valido = false;
						}
						
						if( articulo.saldo < can ){
							msg = "La cantidad no puede ser mayor a "+articulo.saldo;
							valido = false;
						}
						
						if( articulo.saldo + can < 0 ){
							msg = "La cantidad no puede ser menor a "+( articulo.saldo*-1 );
							valido = false;
						}
							
						if( valido ){
							
							articulo.lote = {};
							articulo.lote.codigo = lote;
							articulo.lote.cantidad = can*1;
							
							agregarArticuloALista( articulo, can );
							
							$( this ).dialog( "close" );
							
							setTimeout( function(){ $(this).remove() },500 );
						}
						else{
							jAlert( msg, "ALERTA" );
						}
					}
					else{
						jAlert( "Debe ingresar un lote", "ALERTA" )
					}
					
				},
				"Cancelar" : function(){
					$( this ).dialog( "close" );
				},
			},
		});
		
		setTimeout( function(){
			$( "#inBuscadorInsumo" ).val('');
			$( "#inBuscadorInsumo" ).change();
		}, 200 );
		
	}
	
	function diligenciarLote( insumo, cantidadACargar, pedido ){
		
		$( "#ltLotes" ).remove();
		$( "#canLote" ).remove();
		$( "#lote" ).remove();
		
		console.log( "insumo" )
		console.log( insumo )
		console.log( "pedido" )
		console.log( pedido )
		var habitacion 	 = pedido.paciente.habitacion;
		var codigoInsumo = insumo.codigo;
		var valido = true;
		
		var option = "";
		try{
			for( var x in Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes ){
				option += "<option value='"+x+"'></option>";
			}
		}
		catch(e){}
		
		$( "<div style='text-align:center;'>"
			  +"<div style='padding: 10px;'>"
				+"<b>"+Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].nombreGenerico+"</b>"
			  +"</div>"
			  +"<div style='padding: 10px;'>"
				+"<label style='width:100px;font-weight:bold;'>Cantidad: </label><input type='number' name='canLote' id='canLote' style='width:50px;' value='1' min="+(cantidadACargar.val()*-1)+" max="+pedido.cantidadSolicitada+"></input>"
			  +"</div>"
			  +"<div style='padding: 10px;'>"
				+"<label style='width:100px;font-weight:bold;'>Lote: </label><input type='text' list=ltLotes  name='lote' style='width:100px;' value='' autocomplete=off></input>"
				+"<datalist id=ltLotes>"
					+option
				+"</datalist>"
			  +"</div>"
		  +"</div>" ).dialog({
			title	: "Diligenciar el lote",
			modal	: true,
			buttons	: {
				"Aceptar" : function(){
					
					
					var lote 	= $( "[name=lote]", this ).val();
					var can 	= $( "[name=canLote]", this ).val()*1;
					var valido  = true;
					var msg 	= '';
					
					if( lote != '' ){
						
						Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes
						
						if( Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo] ){
							
							if( Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes ){
								if( Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes[ lote ] ){
									
									if( Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes[ lote ]+can < 0 ){
										msg = "La cantidad no puede ser menor a "+( Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes[ lote ]*-1 );
										valido = false;
									}
								}
								else if( can < 0 ){
									msg = "La cantidad debe ser mayor a 0";
									valido = false;
								}
							}
							else if( can < 0 ){
								msg = "La cantidad debe ser mayor a 0";
								valido = false;
							}
						}
						else if( can < 0 ){
							msg = "La cantidad debe ser mayor a 0";
							valido = false;
						}
						
						if( insumo.saldo < cantidadACargar.val()*1+can*1 ){
							msg = "La cantidad no puede ser mayor a "+( insumo.saldo - cantidadACargar.val()*1 );
							valido = false;
						}
						
						if( insumo.saldo + cantidadACargar.val()*1+can*1 < 0 ){
							msg = "La cantidad no puede ser menor a "+( insumo.saldo*-1 );
							valido = false;
						}
						
						if( pedido.cantidadSolicitada < cantidadACargar.val()*1+can*1 ){
							msg = "La cantidad no puede ser mayor a "+( pedido.cantidadSolicitada - cantidadACargar.val()*1 )+". Supera la cantidad solicitada";
							valido = false;
						}
						
						
						
						
						// if( lote == '' ){
							// msg = "Debe ingresar un lote"
							// valido = false;
						// }
						
						// if( can > insumo.saldo ){
							// msg = "La cantidad no puede ser mayor a "+insumo.saldo*1;
							// valido = false;
						// }
						
						if( valido ){
							
							cantidadACargar.val( cantidadACargar.val()*1 + can*1 );
							cantidadACargar.change();
							
							if( !pedido.lotes ){
								pedido.lotes = {};
								Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes = {};
							}
							
							if( !pedido.lotes[lote] ){
								pedido.lotes[lote] = 0;
								Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes[lote] = 0;
							}
							
							pedido.lotes[lote] += can;
							Pedidos.data.Pedidos[habitacion].insumos[codigoInsumo].lotes[lote] += can*1;
							
							$( this ).dialog( "close" );
							
							var _self = this;
							setTimeout( function(){ $(_self).remove() },500 );
						}
						else{
							jAlert( msg, "ALERTA" );
						}
					}
					else{
						jAlert( "Debe ingresar un lote.", "ALERTA" );
					}
					
				},
				"Cancelar" : function(){
					$( this ).dialog( "close" );
				},
			},
		});
		
	}
	
	
	function cambiarEstadoEdicionPedido( codigo, estado ){
		
		if( codigo != '' ){
		
			$.ajax({
				url		: "botiquinDispensacionInsumos.php",
				type	: "POST",
				async	: false,
				data	: {
					consultaAjax 	: 'cambiarEstadoEnEntrega',
					wemp_pmla		: "<?=$wemp_pmla?>",
					codPedido		: codigo,
					estado			: estado,
				},
			});
		}
	}
	
	function cambiarEstadoEnEntrega(){
		
		if( Pedidos != undefined ){
			if( Pedidos && Pedidos.data && Pedidos.data.codigoPedidos ){
				
				if( Pedidos.data.codigoPedidos.length > 0 ){
					$( Pedidos.data.codigoPedidos ).each(function(){
						//this es el codigo del pedido
						cambiarEstadoEdicionPedido( this, 'off' );
					});
				}
			}
		}
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
	
	function validarSessionAjax(respuesta){
		if( respuesta && respuesta.error == '1000' ){
			jAlert( "<b style='font-size:20pt;'>DEBE INGRESAR NUEVAMENTE AL PROGRAMA POR MATRIX</b>", "ALERTA", function(){
				cerrarVentana();
			});
		}
	}
	
	
	function validarPedido(){
		
		var esValido = true;
		
		//Lo cargado debe corresponder con la cantidad solicitada
		for( var x in Pedidos.data.insumos ){
			
			var pedido = Pedidos.data.insumos[x].pedido;
			
			//Al menos debe haber ingresado una cantidad a cargar
			esValido = 3;
			for( var i in pedido ){
				
				if( !pedido[i].cantidadCargada.attr("disabled") && pedido[i].cantidadCargada.val()*1 > 0 ){
					esValido = true;
					break;
				}
			}
			
			if( esValido === true )
				break
		}
		
		//Lo cargado debe corresponder con la cantidad solicitada
		if( esValido === true ){
			for( var x in Pedidos.data.insumos ){
				
				var pedido = Pedidos.data.insumos[x].pedido;
				
				//Si no tiene cantidades
				for( var i in pedido ){
					console.log("---------")
					console.log(i)
					console.log(pedido[i])
					if( !pedido[i].cantidadCargada.attr("disabled") && pedido[i].cantidadSolicitada*1 > pedido[i].cantidadCargada.val()*1 ){
						esValido = 2;
					}
				}
			}
		}
		
		return esValido;
	}
	
	function cargarPedidoAuxiliar(){
				
		if( Pedidos ){
			
			var pedidoValido = validarPedido();
			
			if( pedidoValido !== true ){
				
				switch( pedidoValido ){
					case 2: 
						msg = "Hay insumos que no se le van a cargar la cantidad solicitada. Desea continuar?";
						jConfirm( msg, "ALERTA", function( resp ){
							if( resp ){
								cargarPedido();
							}
						});
					break;
					
					case 3: 
						msg = "Debe ingresar almenos un insumo para poder dispensar";
						jAlert( msg, "ALERTA" );
						return;
					break;
				}
				
			}
			else{
				cargarPedido();
			}
		}
		
		function cargarPedido(){
			
			var insumos = [];
			
			var habitaciones = Pedidos.data.Pedidos
			
			for( var x in habitaciones ){
				
				var paciente = habitaciones[x].Paciente;
				var ins		 = habitaciones[x].insumos;
				console.log(ins);
				for( var i in ins ){
					
					if( ins[i].cantidadCargada.val()*1 > 0 ){
					
						insumos.push({
							warticulo	 : i,	//Codigo del insumo
							whis		 : paciente.historia,
							wing		 : paciente.ingreso,
							wccopac		 : paciente.cco.codigo,
							whabpac		 : x,	//Código de la habitación
							wcantidad	 : ins[i].cantidadCargada.val(),
							wcodigoPedido: habitaciones[x].codigoPedido,
							wlotes		 : ins[i].lotes ? ins[i].lotes: '',
						});
					}
				}
			}
			
			
			$.ajax({
				url		: "./botiquinDispensacionInsumos.php",
				type	: "POST",
				dataType: "json",
				data	: {
					consultaAjax: 'cargarPedido',
					wemp_pmla	: "<?=$wemp_pmla?>",
					wcco		: "<?=$botiquin['codigo']?>",
					wauxiliar	: auxiliar.codigo,
					wdatos		: insumos,
				},
				async	: false,
				success	: function(respuesta) {
					
					validarSessionAjax(respuesta);
					
					if( respuesta && respuesta.error && respuesta.error == 1 ){
						jAlert( respuesta.message, "ALERTA" );
						$( "#btCargarPedido" ).attr({disabled: true });
					}
					else{

						var allValido = true;
						
						//Para validación vertical
						for( var x in respuesta ){
							//x es la habitacion
							var valido = true;
							var msg = "";
							for( var i in respuesta[x] ){
								if( respuesta[x][i].error != 0 ){
									valido 	 = false;
									if( msg == "" )
										msg = respuesta[x][i].message;
									else
										msg+= respuesta[x][i].message;
								}
								else{
									//Quito los datos necesario de las listas parano volverlas a procesar en caso de que halla grabado
									//Deshabilito el campo que grabo correctamente
									Pedidos.data.Pedidos[x].insumos[ respuesta[x][i].insumo ].cantidadCargada.attr({
										disabled: true,
									});
									delete( Pedidos.data.Pedidos[x].insumos[ respuesta[x][i].insumo ] );
								}
							}
							
							if( valido ){
								
								var chk = $( "."+x+" a" ).eq(0);
								
								chk.css({
									display: "",
								});
								
								chk.tooltip({ 
									showURL: false,
									showBody: ' - ',
								});
							}
							else{
								
								allValido = false;
								
								var err = $( "."+x +" a" ).eq(1)
								
								err.css({
									display: "",
								});
								
								
								err.attr({
									title: msg,
								});
								
								err.tooltip({ 
									showURL: false,
									showBody: ' - ',
								});
							}
						}
					
					
						//Para validación horizontal
						// Pedidos.data.insumos[ respuesta[x][i].insumo ].pedido
						//x es el código del insumo
						for( var x in Pedidos.data.insumos ){
							
							//insumo es un array que contiene toda la información del insumo
							var insumo = Pedidos.data.insumos[x].pedido;
							
							var validoHorizontal = null;
							
							for( var i in insumo ){
								
								//cantidadCargada es el input correspondiente, si está deshabilitado es que cargo correctamente 
								if( insumo[i].cantidadCargada.attr("disabled") != 'disabled' && insumo[i].cantidadCargada.val() != '' ){
									validoHorizontal = false;
									$( "."+x+" a" ).eq(1).css({display:''});
									break;
								}
								else if(insumo[i].cantidadCargada.attr("disabled") == 'disabled' && insumo[i].cantidadCargada.val() != ''){
									validoHorizontal = true;
								}
							}
							
							if( validoHorizontal == true ){
								$( "."+x+" a" ).eq(0).css({display:''});
							}
							else{
								// $( "."+x+" a" ).eq(1).css({display:''});
							}
						}
						
						if( allValido ){
							$( "#btCargarPedido" ).attr({disabled:true});
						}
					}
				}
			});
			
			return true;
		}
		
	}
	
	
	
	
	
	function calcularCantidadCargada( tr ){
		
		var cantidadTotalCargada = 0;
		$( "input", tr ).each(function(){
			cantidadTotalCargada += $( this ).val()*1;
		});
		
		$( "span", tr  ).html(cantidadTotalCargada);
	}
	
	function soloNumerosParaPedidos(){
		
		//Permito escribir solo números, presionar enter o una direccional(flecha arriba, abajo, izquierda derecha)
		$( "input", $( "#tbPedidos > tbody" ) ).keypress(function(e){
			
			var key =  e.which || e.keyCode;
			
			//Eso se hace para capturar la velocidad al presionar una tecla
			//Humanamente no es posible presionar dos teclas en menos de 10 milisigundos
			//console.log( Date.now() - this._now );
			if( Date.now() - this._now < 50 ){
				this._now = Date.now();
				
				if( key == 13 ){
					$( this ).val( this.__valAnterior );
				}
				
				e.preventDefault();
			}
			else{
				
				this._now = Date.now();
				
				$( "#dvInsumosPedidos")[0]._valIngresado = '';
				document._valIngresado = '';
				
				      // //números					//letras minusculas			////letras mayusculas
				// if( key >= 65 && key <= 90 ||  key >= 97 && key <= 122 ||  key >= 48 && key <= 57 ){
						
					// $( "#dvInsumosPedidos")[0]._valIngresado = key;
					// document._valIngresado = key;
				// }
				
					//Numeros					//direccionales			//Enter		  //backspace	//tab	  //Suprimir
				if( key >= 48 &&  key <= 57 || key >= 37 &&  key <= 40 || key == 13 || key == 8 || key == 9 || key == 46 ){
					
					var inputs = $( "input", $( "#tbPedidos > tbody" ) );
					var trs = $( "tr", $( "#tbPedidos > tbody" ) );
					
					//indice que ocupa el input en todos los inputs
					var idx = inputs.index( this );	
					
					//indice que ocupa la fila a la que pertenece el input
					var idxTr = trs.index( $( this ).parent().parent() );	
					
					//indice que ocupa la fila a la que pertenece el input
					var idxTd = $( "td", trs.eq(idxTr) ).index( $( this ).parent() );	
					
					//Si es una direccional o enter
					switch( key ){
						
						//Izquierda
						case 37: 
							//Debe moverse al anterior input
							if( idx > 0 ){
								inputs.eq(idx-1).focus();
							}
						break;
						
						//Arriba
						case 38: 
							//Busco todos los td del tr anterior
							//Y busco el último input de acuerdo a la posicion en que ocupa en la fila actual
							//De lo contrario busco el primero despues de la posicion actual
							if( idxTr > 0 ){
								var o = $( "td:lt("+( (idxTd+1)*1 )+") > input[type=text]:last", trs.eq( idxTr-1) );
								if( o.length > 0 ){
									o.focus();
								}
								else{
									$( "td:gt("+idxTd+") > input[type=text]:first", trs.eq( idxTr-1) ).focus()
								}
							}
						break;
						
						//Abajo
						case 40: 
							//Busco todos los td del tr que lo sigue
							//Y busco el último input de acuerdo a la posicion en que ocupa en la fila actual
							//De lo contrario busco el primero despues de la posicion actual
							if( idxTr < trs.length-1 ){
								var o = $( "td:lt("+( (idxTd+1)*1 )+") > input[type=text]:last", trs.eq( idxTr+1) );
								if( o.length > 0 ){
									o.focus();
								}
								else{
									$( "td:gt("+idxTd+") > input[type=text]:first", trs.eq( idxTr+1) ).focus()
								}
							}
						break;
						
						//Derecha o enter
						// case 9:		//Tab
						case 13:	//Enter
						case 39: 	//Tecla derecha
							//Debe moverse al anterior input
							if( idx < inputs.length ){
								inputs.eq(idx+1).focus();
							}
						break;
					}
				}
				else{
					
					e.preventDefault();
					// return false;
				}
			}
			
		}).focus(function(e){
			this._now = Date.now();
			
			$( "#lbInsumoLeido" ).css({display:'none'});
		});
		
	}
	
	function crearCampoFilaPedidos( tr, pedido, pacientes, insumo ){
		
		var td = $( "<td class='fondoverde'>"+pedido.cantidadSolicitada+"</td><td><input type='text' value=''></td>" )
					.appendTo( tr );
		
		var inp = $( "input", td.eq(1) );
		
		inp[0].__valAnterior;
		inp.change(function(e){
			
			$( "#tbPedidos td" ).removeClass("fondorojo");
			
			var cantidadTotalCargada = 0;
			$( "input", tr ).each(function(){
				if( inp[0] !=  this )
					cantidadTotalCargada += $( this ).val()*1;
			});
			
			if( $( this ).val()*1 > pedido.cantidadSolicitada*1 || ( !insumo.permiteNegativos && cantidadTotalCargada+$( this ).val()*1 > insumo.saldo ) ){
				generarSonidoAlerta();
				
				if( $( this ).val()*1 > pedido.cantidadSolicitada*1 ){
					td.eq(0).addClass("fondorojo");
				}
				
				if( !insumo.permiteNegativos && cantidadTotalCargada+$( this ).val()*1 > insumo.saldo ){
					$( "td", tr ).eq(2).addClass("fondorojo");
				}
				
				$( this ).val( this.__valAnterior );
				
				$( inp ).focus();
			}
			else{
				this.__valAnterior = $( this ).val();
				calcularCantidadCargada( tr )
			}
		});
		
		if( insumo.implantable ){
			
			inp.focus(function(){				
				diligenciarLote( insumo, $( this ), pedido );
			});
		}
		
		return inp;
	}
	
	function setTablaPedidos( habitaciones, insumos, pedidosHabitaciones, paquetes ){
	
		//habitaciones es un array que contiene todas las habitaciones qué se deben mostrar
		//Debo crear nuevamente el encabezado para los datos a mostrar
		
		
		var encabezadoPaquetes = "<tr style='text-align:center;background-color:#FAFAFA;'><td colspan='3'>&nbsp</td>";
		var encabezado = "<tr style='text-align:center;'><td>Insumo</td><td title=' - Presentaci&oacute;n'>Pre.</td><td>Saldo</td>";
		for( var x in habitaciones ){
			
			var htmlPaquete = "";
			var mostrarIcono = "style='display:none;'";
			for(paciente in paquetes)
			{
				if(paciente==habitaciones[x])
				{
					// htmlPaquete = " - <b>Paquetes: </b>";
					// htmlPaquete = " - ";
					htmlPaquete = "";
					for(codPaquete in paquetes[paciente])
					{
						htmlPaquete += "- "+paquetes[paciente][codPaquete]+"<br>";
						mostrarIcono = "";
					}
				}
				
				
			}
			
			encabezadoPaquetes += "<td colspan=2 title='"+htmlPaquete+"'><img src='/matrix/images/medical/movhos/paquete.svg' height=17 width=17 "+mostrarIcono+" class='iconoPaquete' style='fill:yellow'></td>";
			
			var pac 	= pedidosHabitaciones[ habitaciones[x] ].Paciente
			var txTitle = " - <b>Historia: </b>"+pac.historia+"-"+pac.ingreso+"<br>"
						  +"<b>Nombre: </b>"+pac.nombre+"<br>"
						  +"<b>Identifiaci&oacute;n: </b>"+pac.tipoDocumento+" "+pac.numeroDocumento;			  
			
			encabezado += "<td colspan=2 title='"+txTitle+"'>"+habitaciones[x]+"</td>";
		}
		
		encabezadoPaquetes += "<td colspan=3>&nbsp;</td></tr>";
		encabezado += "<td colspan=3>Total</td></tr>";
		
		$( "#tbPedidos > thead" ).html( encabezadoPaquetes+encabezado );
		
		//Creo los datos por insumo
		for( var x in insumos ){
			
			msgTitle = "";
			if( insumos[x].insumo.permiteNegativos )
				msgTitle = " title=' - Permite negativos'";
			
			var tr = $( 
						"<tr>"
							+"<td"+msgTitle+">"+insumos[x].insumo.codigo+"-"+insumos[x].insumo.nombreGenerico+"</td>"
							+"<td>"+insumos[x].insumo.presentacion+"</td>"
							+"<td style='text-align:center'>"+insumos[x].insumo.saldo+"</td>"
						+"</tr>" 
					).appendTo( "#tbPedidos > tbody" );
			
			//Pedidos Por Insumo
			var pedido = insumos[x].pedido;
			// var ped = 0;
			var totalSolicitado = 0;
			//hab indice por habitacion, ped -> indice por pedido
			for( var hab = 0; hab < habitaciones.length; hab++ ){
				
				//Julio 13 de 2019
				//Se busca la posición del pedido con respecto al paciente
				var ped = -1;
				for( var z = 0; z < pedido.length; z++ ){
					if( habitaciones[hab] == pedido[z].paciente.habitacion ){
						ped = z;
						break;
					}
						
				}
				
				
				
				//Si el paciente está en la mimsa posicion le asigno los datos correspondientes
				if( pedido[ped] && habitaciones[hab] == pedido[ped].paciente.habitacion ){
					
					var inp = crearCampoFilaPedidos( tr,  pedido[ped], pedidosHabitaciones, insumos[x].insumo );
					totalSolicitado += pedido[ped].cantidadSolicitada*1;
					
					//Agrego el inputs al paciente para que al momento de grabar sea más fácil encontrar los datos
					pedidosHabitaciones[ habitaciones[hab] ].insumos[ insumos[x].insumo.codigo ].cantidadCargada = inp;
					insumos[x].pedido[ped].cantidadCargada = inp;
					
					// ped++;
				}
				else{
					$( tr ).append( "<td class='fondoverde'></td><td></td>" );
				}
			}
			
			var td = $(  "<td class='fondoamarillo' style='width:30px;text-align:center;'>"+totalSolicitado+"</td>"
						+"<td style='width:30px;text-align:center;'><span></span></td>"
						+"<td class='"+x+"' style='width:30px;text-align:center;'>"
							+"<a href=#null style='display:none;'><img src='/matrix/images/medical/movhos/checkmrk.ico' height=17 width=17></a>"
							+"<a href=#null style='display:none;'><img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17></a>"
						+"</td>" 
					)
					.appendTo( tr );
		}
		
		$( "#tbPedidos tfoot" ).remove();
		
		//Se adiciona fila final para mostrar mensajes de errores
		var txFila = "<tfoot><tr class='encabezadotabla'><td colspan=3>&nbsp</td>";
		for( var hab = 0; hab < habitaciones.length; hab++ ){
			txFila += "<td class='"+habitaciones[hab]+"' colspan=2 style='text-align:center;'>"
						 +"<a href=#null style='display:none;'><img src='/matrix/images/medical/movhos/checkmrk.ico' height=17 width=17></a>"
						 +"<a href=#null style='display:none;'><img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17></a>"
					 +"</td>";
		}
		txFila += "<td colspan=3></td></tr></tfoot>";
		
		$( txFila ).appendTo( "#tbPedidos" );
		
		soloNumerosParaPedidos();
	}
	
	function consultarPedidos(){
		
		if( '<?=$botiquin['codigo']?>' != '' ){
			
			$.ajax({
				url		: "./botiquinDispensacionInsumos.php",
				type	: "POST",
				dataType: "json",
				data	: {
					consultaAjax: 'consultarPedidos',
					wemp_pmla	: "<?=$wemp_pmla?>",
					wbotiquin	: "<?=$botiquin['codigo']?>",
					wauxiliar	: auxiliar.codigo,
				},
				async	: false,
				success	: function(respuesta) {
					
					validarSessionAjax(respuesta);
					
					//console.log(respuesta)
					Pedidos = respuesta;
					
					$( "#btCargarPedido" ).attr({
						disabled: false,
					});
					
					if( respuesta.error == 0 ){
						
						if( respuesta.data != '' ){
							
							//Si tiene pedidos
							
							//Borro todos los campos de la tabla tbPedidos qué son los Pedidos anteriores
							$( "#tbPedidos > tbody" ).html("");
							
							if( 1 ){
								setTablaPedidos( respuesta.data.habitaciones, respuesta.data.insumos, respuesta.data.Pedidos, respuesta.data.paquetes );
								
								$( "ul" ).css({
									display: '',
								});
								
								setTimeout(function(){
									$("#dvTabs").tabs('select', 1);
									$("#dvTabs > ul").css({ display: esAyudaDx ? '' : 'none' });
								}, 100 );
								
								$( "[title]" ).tooltip({
									showURL: false,
									showBody: " - ",
								});
								
								// //Oculto la dispensacion normal
								// $( "#dvContenidoInsumos" ).css({
									// display: 'none',
								// });
								
								// //Muestro el pedido
								// $( "#dvInsumosPedidos" ).css({
									// display: '',
								// });
							}
						}
						else{
							//Si no tiene pedidos
							$( "ul" ).css({
								display: 'none',
							});
							
							$( "#tbPedidos > tbody > tr" ).html("");
							$( "#tbPedidos > thead > tr" ).html("");
							$( "#tbPedidos > tfoot > tr" ).html("");
							
							setTimeout(function(){
								$("#dvTabs").tabs('select', 0);
								// $( "#dvContenidoInsumos" ).css({ display: 'none' });
								if( !esAyudaDx ){
									$( "#dvContenidoInsumos" ).html("<div style='margin: 0 25%;width: 50%;display: inline;text-align:center;font-size:20pt; font-weight: bold;'>SE DEBE REALIZAR UN PEDIDO</div>");
								}
								
							}, 100 );
						}
						
					}
					else{
						jAlert( respuesta.message, "ALERTA" );
						
						if( respuesta.error == 1 ){
							
							//Si no tiene pedidos
							$( "ul" ).css({
								display: 'none',
							});
							
							$( "#tbPedidos > tbody > tr" ).html("");
							$( "#tbPedidos > thead > tr" ).html("");
							$( "#tbPedidos > tfoot > tr" ).html("");
							
							setTimeout(function(){
								$("#dvTabs").tabs('select', 0);
								if( !esAyudaDx ){
									$( "#dvContenidoInsumos" ).html("<div style='margin: 0 25%;width: 50%;display: inline;text-align:center;font-size:20pt; font-weight: bold;'>SE DEBE REALIZAR UN PEDIDO</div>");
								}
							}, 100 );
						}
					}
				}
			});
		}
	}
	
	
	function actualizarSaldosBotiquin(){
		
		if( '<?=$botiquin['codigo']?>' != '' ){
			
			//Actualizando saldos
			$.ajax({
				url		: "./proceso_saldosUnix.php",
				type	: "POST",
				async	: true,
				data: {
					cco: '<?=$botiquin['codigo']?>'
				},
			});
		}
	}
	
	
	function consultarInsumosAuxiliarPorPaciente(){
		
		$.ajax({
			url		: "./botiquinDispensacionInsumos.php",
			type	: "POST",
			dataType: "json",
			data	: {
				consultaAjax: 'consultarInsumosAuxiliarPorPaciente',
				wemp_pmla	: "<?=$wemp_pmla?>",
				wauxiliar	: auxiliar.codigo,
				whistoria	: Paciente.historia,
				wingreso	: Paciente.ingreso,
			},
			async	: true,
			success	: function(respuesta) {
				
				validarSessionAjax(respuesta);
				
				if( respuesta.error == 0 ){
					
					var insumos = respuesta.data;
					
					if( insumos.length > 0 ){
						
						var tbInsumo = $(
							"<table id='tbInsumosPendientes'>"
								+"<thead class='encabezadotabla'>"
									+"<th>Insumo</th>"
									+"<th colspan=2>Saldo</th>"
								+"</thead>"
							+"</table>"
						);
						
						for( var i in insumos ){
							tbInsumo.append(
								"<tr class='fila1'>" 
									+"<td>"+insumos[i].codigo+"-"+insumos[i].nombreGenercio+"</td>"
									+"<td style='text-align:center;'>"+insumos[i].saldo+"</td>"
									+"<td style='text-align:center;'>"+"<span title=' - "+insumos[i].unidad.descripcion+"'>"+insumos[i].unidad.codigo+"</span></td>"
								+"</tr>" 
							);
						}
						
						$( "#dvInsumosPorPaciente" )
							.html("")
							.css({display:""});
						
						$( "#dvInsumosPorPaciente" ).append( "<CENTER style='padding:20px;' title=' - Insumos pendientes por aplicar o devolver<br>para el paciente <b>"+Paciente.nombre+"</b><br>por parte de <b>"+auxiliar.nombre+"</b>'><b style='font-size:14pt;'>INSUMOS PENDIENTES</b><img src='../../images/medical/root/help.png' style='width:17px;height:17px;'></CENTER>" );
						$( "#dvInsumosPorPaciente" ).append( tbInsumo );
						
						$( "[title]",$( "#dvInsumosPorPaciente" ) ).tooltip({
							showBody: " - ",
							showURL : false,
						})
						
					}
					else{
						$( "#dvInsumosPorPaciente" )
						 .html("<div id='dvMsgInsumosPendientesAuxiliar'>"
								+"<div>"
									+"<div>"
										+"<span>NO TIENE INSUMOS PENDIENTES PARA APLICAR O DEVOLVER<BR>PARA EL PACIENTE<BR><b>"+Paciente.nombre+"</b></span>"
									+"</div>"
								+"</div>"
							+"</div>")
						.css({display:""});
					}
				}
				else{
					jAlert( respuesta.message, "ALERTA" );
				}
			}
		});
	}
	
	
	function generarSonidoAlerta()
	{
		if($("#audio_fb").length == 0)
		{
			var elemento_audio = '<audio id="audio_fb"><source src="../../images/medical/root/alerta_error.mp3" type="audio/mp3"></audio>';
			$( "body" ).append(elemento_audio);
		}
		$("#audio_fb")[0].play();
	}

	
	function agregarArticuloALista( articulo, cantidaPorDefecto ){
		
		if( !cantidaPorDefecto )
			cantidaPorDefecto = 1; //Por defecto siempre aparece cantidad uno
		
		var enLista = listaArticulos.arts[ articulo.codigo ] ? true: false;
		
		$( "#tbListaInsumosACargar input" ).removeClass('fondoamarillo');
		
		//Si el articulo no está en la lista agrego el articulo al formulario
		if( !enLista ){
			
			listaArticulos.arts[articulo.codigo] 		= articulo;
			listaArticulos.arts[articulo.codigo].msg 	= [];
			listaArticulos.arts[articulo.codigo].error 	= false;
			
			if( articulo.implantable && articulo.lote ){
				listaArticulos.arts[articulo.codigo].lotes = {};
				listaArticulos.arts[articulo.codigo].lotes[ articulo.lote.codigo ] = articulo.lote.cantidad;
			}
			
			listaArticulos.length++;
			
			var fila = $(
				 "<tr>"
				+"<td>"+articulo.codigo+"-"+articulo.nombreGenerico+"</td>"
				+"<td style='text-align:center;'>"+articulo.saldo+"</td>"
				+"<td style='width:70px'><input type='text' onKeyPress='return validarEntradaEntera(event);'></td>"
				+"<td style='width:50px'><span title='"+articulo.unidad.descripcion+"'>"+articulo.unidad.codigo+"</span></td>"
				+"<td><a href=#null><img src='../../images/medical/root/borrar.png' height=17 width=17></a></td>"
				+"<td><a href=#null><img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17></a></td>"
				+"<td><a href=#null><img src='/matrix/images/medical/movhos/checkmrk.ico' height=17 width=17></a></td>"
				+"</tr>" 
			);
			
			//Este es eliminar articulo
			var eliminar = $( "a", fila ).eq(0)
			eliminar.click(function(){
				fila.remove();
				
				delete( listaArticulos.arts[articulo.codigo] );
				listaArticulos.length--;
				
				if( listaArticulos.length <= 0 )
					$( "#btCargarInsumos" ).attr({disabled: true });
			});
			
			var alertas = $( "a", fila ).eq(1);
			alertas.hide();
			var guardar = $( "a", fila ).eq(2);
			guardar.hide();
			
			$( "[title]", fila ).tooltip();
			
			var cantidad = $( "input", fila );
			cantidad.val( cantidaPorDefecto );	//Por defecto siempre aparece cantidad uno
			cantidad[0]._valAnterior = 1;
			
			//Valida que los datos sean correctos
			listaArticulos.arts[articulo.codigo].validate = function(){
				
				var val = true;
				listaArticulos.arts[articulo.codigo].error 	= false;
				listaArticulos.arts[articulo.codigo].msg = [];
				if( cantidad.val()*1 == 0 || isNaN( cantidad.val() ) ){
					listaArticulos.arts[articulo.codigo].msg.push( "La cantidad a cargar debe ser <b>mayor</b> a 0" );
					listaArticulos.arts[articulo.codigo].error 	= true;
				}
				if( cantidad.val()*1 > articulo.saldo && !articulo.permiteNegativos ){
					cantidad.val( cantidad[0]._valAnterior );
					listaArticulos.arts[articulo.codigo].msg.push( "La cantidad a cargar debe ser <b>menor o igual a</b> "+articulo.saldo );
					listaArticulos.arts[articulo.codigo].error 	= true;
				}
				
				return !listaArticulos.arts[articulo.codigo].error;
			}
			
			listaArticulos.arts[articulo.codigo].getMsgErrors = function(){
				
				var msg = "";
				
				if( listaArticulos.arts[articulo.codigo].error ){
					for( var x in listaArticulos.arts[articulo.codigo].msg ){
						if( msg != '' )
							msg += "<br>";
						msg += listaArticulos.arts[articulo.codigo].msg[x];
					}
				}
				
				return msg;
			}
			
			listaArticulos.arts[articulo.codigo].showErrors = function(){
				if( listaArticulos.arts[articulo.codigo].error ){
					generarSonidoAlerta();
					var msg = "";
					for( var x in listaArticulos.arts[articulo.codigo].msg ){
						if( msg != '' )
							msg += "<br>";
						msg += listaArticulos.arts[articulo.codigo].msg[x];
					}
					
					alertas.attr({ title: msg });
					alertas.tooltip({ showURL: false });
					alertas.show();
				}
				else{
					alertas.hide();
				}
			}
			
			listaArticulos.arts[articulo.codigo].showErrorUser = function(){
				listaArticulos.arts[articulo.codigo].showErrors();
				alertas.mouseover()
				setTimeout(function(){
					alertas.mouseout()
					$( this ).focus();
				}, 3000 );
			}
			
			listaArticulos.arts[articulo.codigo].save = function(){
				
				if( listaArticulos.arts[articulo.codigo].validate() ){
					
					$.ajax({
						url		: "./botiquinDispensacionInsumos.php",
						type	: "POST",
						dataType: "json",
						data	: {
							consultaAjax: 'cargarInsumo',
							wemp_pmla	: "<?=$wemp_pmla?>",
							wcco		: "<?=$botiquin['codigo']?>",
							wauxiliar	: auxiliar.codigo,
							warticulo	: articulo.codigo,
							whis		: Paciente.historia,
							wing		: Paciente.ingreso,
							wccopac		: Paciente.ubicacion.cco.codigo,
							whabpac		: Paciente.ubicacion.habitacion,
							wcantidad	: cantidad.val(),
							wlotes		: articulo.lotes,
						},
						async	: false,
						success	: function(respuesta) {
							
							validarSessionAjax(respuesta);
							
							// console.log(respuesta)
							if( respuesta.error == 0 ){
								
								eliminar.hide();
								alertas.hide();
								guardar.show();
								
								guardar.attr({
									title: respuesta.data.accion+" - "+respuesta.data.fecha+" "+respuesta.data.hora+"<br>"+cantidad.val()+" "+articulo.unidad.codigo,
								})
								
								guardar.tooltip({ 
									showURL: false,
									showBody: ' - ',
								});
								
								
								cantidad.removeClass('fondoamarillo');
								cantidad.attr({
									disabled: true,
								});
								
								//Elimino de la lista
								//Esto para que se pueda volver a agregar a la lista
								delete( listaArticulos.arts[articulo.codigo] );
								listaArticulos.length--;
							}
							else{
								listaArticulos.arts[articulo.codigo].msg = [];
								listaArticulos.arts[articulo.codigo].error 	= true;
								listaArticulos.arts[articulo.codigo].msg.push( respuesta.message );
								listaArticulos.arts[articulo.codigo].showErrors();
							}
						}
					});
					
					return true;
				}
				else{
					listaArticulos.arts[articulo.codigo].showErrors();
					return false;
				}
			}
			
			listaArticulos.arts[articulo.codigo].add = function( can ){
				cantidad.addClass( 'fondoamarillo' );
				cantidad.val( cantidad.val()*1+can*1 );
				if( listaArticulos.arts[articulo.codigo].validate() )
					cantidad[0]._valAnterior = cantidad.val();
			}
			
			$( "#tbListaInsumosACargar" ).append( fila );
			
			cantidad.keypress(function( event ){
				if ( event.which == 13 ){
					$( "#inBuscadorInsumo" ).focus();
					// event.preventDefault();
				}
			}).change(function(){
				
				if( !listaArticulos.arts[articulo.codigo].validate() ){
					generarSonidoAlerta();
					$( "#btCargarInsumos" ).attr({disabled: true });
					
					listaArticulos.arts[articulo.codigo].showErrors();
					alertas.mouseover();
					
					setTimeout(function(){
						cantidad.focus();
						cantidad.select();
					}, 20 );
					
					setTimeout(function(){
						$( "#btCargarInsumos" ).attr({disabled: false });
						alertas.mouseout()
					}, 3000 );
				}
				cantidad[0]._valAnterior = cantidad.val();
			});
			
			if( !articulo.preEnter ){
				cantidad.focus();
				cantidad.select();
			}
			else{
				$( "#inBuscadorInsumo" ).focus();
			}
			
			cantidad.addClass( 'fondoamarillo' );
			
			$( "#btCargarInsumos" ).attr({disabled: false });
			
			if( articulo.implantable){
				cantidad.focus(function(){
					agregarLotePorInsumo( articulo );
				});
			}
			
			return listaArticulos.arts[articulo.codigo];
		}
		else{
			
			if( articulo.implantable && articulo.lote ){
				
				if( listaArticulos.arts[articulo.codigo].lotes[ articulo.lote.codigo ] ){
					listaArticulos.arts[ articulo.codigo ].lotes[ articulo.lote.codigo ] += articulo.lote.cantidad;
				}
				else{
					// listaArticulos.arts[articulo.codigo].lotes = {};
					listaArticulos.arts[articulo.codigo].lotes[ articulo.lote.codigo ] = articulo.lote.cantidad;
				}
			}
			
			listaArticulos.arts[articulo.codigo].add(cantidaPorDefecto);
			
			listaArticulos.arts[articulo.codigo].showErrors();
			listaArticulos.arts[articulo.codigo].showErrorUser();
			
			$( "#btCargarInsumos" ).attr({disabled: false });
			
			return listaArticulos.arts[articulo.codigo];
		}
		
	}
	
	function setInsumosACargar(){
		
		//Muestro el div que contiene la información de la axuliar con los datos a cargar
		$( "#dvDatosAuxiliar" ).show();
		$( "#dvAuxiliarEnfermeria" ).html( 
			"<span>"
				+"<span style='position: absolute;left: 0;top: 0px;display:<?=$mostarFoto?>'><img src='"+auxiliar.foto+"' height='75px' style='border: 1px solid black;background-color:white;'></span>"
				+"<span>"+auxiliar.descripcion+ "</span>"
			+"<span>"
		);
		
		//Borro todos los datos por que son de otra auxiliar
		$( "#inBuscadorInsumo" ).val( "" );
		$( "#dvListaInsumosACargar" ).html( 
			"<table id='tbListaInsumosACargar'>"+
				"<thead class=encabezadotabla>"+
					"<th style='width:400px'>Insumo</th>"+
					"<th>Saldo</th>"+
					"<th colspan=2 style='width:120px'>Cantidad</th>"+
					"<th colspan=3 style='width:120px'>Acci&oacute;n</th>"+
				"</thead>"+
			"</table>"
		);
		
		
		//Permite que la foto se vea grande para el suplente.
		$( "#dvAuxiliarEnfermeria img" ).click(function() {

			// var imagen = $(this).attr('id');
			// var id = $('.'+imagen).attr('id');

			$.blockUI({
				message: "<img src='"+auxiliar.foto+"' style='border: 1px solid black;background-color:white;' height='700px' onClick='$.unblockUI();'>", 
				css: { 
					top:  ($(window).height() - 700) /2 + 'px', 
					left: ($(window).width() - 700) /2 + 'px', 
					width: 'auto'
				} 
			}); 

			$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
		});
		
		listaArticulos = {
			arts	: {},
			length	: 0,
		}
	}
	
	function setPaciente( paciente ){
			
		Paciente = paciente;
		
		$( "#inBuscadorInsumo" ).attr({
			disabled: false,
		});
				
		$( "#dvInfoPaciente" ).html('');
		
		$( "#btCargarInsumos" ).attr({disabled: true });
		
		/*$( "#dvInfoPaciente" ).html( "<b>"+Paciente.nombre
									+"<br>"+Paciente.tipoDocumento+" "+Paciente.numeroDocumento
									+"</b><br>Paciente con historia <b>"+Paciente.historia+"-"+Paciente.ingreso
									+"</b> ubicado en <b>"+Paciente.ubicacion.habitacion+"</b>" );*/
									
		$( "#dvInfoPaciente" ).html( "<table style='width:70%;margin:0 auto;'>"
										+"<tr class='encabezadotabla' style='text-align:center'>"
											+"<td colspan=4>INFORMACI&Oacute;N DEL PACIENTE</td>"
										+"</tr>"
										+"<tr>"
											+"<td class='fila1'><b>Paciente</b></td>"
											+"<td class='fila2' colspan=3>"+Paciente.nombre+"</td>"
										+"</tr>"
										+"<tr>"
											+"<td class='fila1'><b>Historia</b></td>"
											+"<td class='fila2'>"+Paciente.historia+"-"+Paciente.ingreso+"</td>"
											+"<td class='fila1'><b>Identificaci&oacute;n</b></td>"
											+"<td class='fila2'>"+Paciente.tipoDocumento+" "+Paciente.numeroDocumento+"</td>"
										+"</tr>"
										+"<tr>"
											+"<td class='fila1'><b>Ubicaci&oacute;n</b></td>"
											+"<td class='fila2' colspan=3><b>"+Paciente.ubicacion.cco.nombre+"</b> en la habitaci&oacute;n <b>"+Paciente.ubicacion.habitacion+"</b></td>"
										+"</tr>"
									+"</table>" );
									
		setInsumosACargar();
		setTimeout( function(){
			$( "#inBuscadorPaciente" ).val('');
			$( "#inBuscadorInsumo" ).focus();
		}, 200 );
		
		consultarInsumosAuxiliarPorPaciente();
	}
	
	
	
	function seleccionarAuxiliar( event, ui ){
		
		cambiarEstadoEnEntrega();
		
		Pedidos = null;
		
		auxiliar.set( ui.item.codigo, ui.item.nombre, ui.item.foto );
		if( $("#validarSaldoAuxiliar").val() == 0 || $("#validarSaldoAuxiliar").val() != 0 && ui.item.saldoPendiente == 0 ){
			
			$( "#dvInfoPaciente" ).html('');
			Paciente = {};
			
			setInsumosACargar();
			setTimeout(function(){
				
				$( "#dvInsumosPorPaciente" ).html(
					"<div id='dvMsgInsumosPendientesAuxiliar'>"
						+"<div>"
							+"<div>"
								+"<span>DEBE SELECCIONAR UN PACIENTE</span>"
							+"</div>"
						+"</div>" 
					+"</div>" 
				);
				
				$( "#inBuscadorAuxiliar" ).val('');
				$( "#inBuscadorPaciente" ).focus();
				$( "#inBuscadorInsumo" ).attr({
					disabled: true,
				});
			}, 200 );
			
			consultarPedidos();
			
			document._valIngresado = '';
		}
		else{
			jAlert( "La auxiliar <b>"+auxiliar.descripcion+"</b> tiene saldo pendiente", "ALERTA" );
			setTimeout(function(){ 
				$( "#inBuscadorAuxiliar" ).val('');
			}, 200 );
		}
	}
	

	$( document ).ready(function(){
		
		//consulto la axuliar a la que se le cargará los insumos
		$( "#inBuscadorAuxiliar" ).autocomplete({
			source		: "./botiquinDispensacionInsumos.php?wemp_pmla=<?=$wemp_pmla?>&consultaAjax=consultarAuxiliar&bot=<?=$botiquin['codigo']?>",
			minLength	: 3,
			delay		: 300,
			change		: function( event, ui ){
				
				if( !ui.item )
					$( "#inBuscadorAuxiliar" ).val("");
			},
			select		: seleccionarAuxiliar,			
		}).click(function(){
			var _self = this;
			_self.permitir = _self.permitir ? _self.permitir : false;
			
			if( !_self.permitir ){
				
				if( listaArticulos.length > 0  ){
					jConfirm( "Tiene insumos pendientes por cargar a <b>"+auxiliar.nombre+"</b>.<br>Desea continuar?", "ALERTA",function(resp){
						if( resp ){
							_self.permitir = true;
							$( _self ).focus();
						}
					});
				}
			}
			else{
				_self.permitir = false;
			}
		});
		
		//consulto la axuliar a la que se le cargará los insumos
		$( "#inBuscadorInsumo" ).autocomplete({
			// source		: "./botiquinDispensacionInsumos.php?wemp_pmla=<?=$wemp_pmla?>&bot=<?=$botiquin['codigo']?>&consultaAjax=consultarInsumo",
			source			: function( request, response ){
				$.ajax({
					url		: "./botiquinDispensacionInsumos.php?wemp_pmla=<?=$wemp_pmla?>&bot=<?=$botiquin['codigo']?>",
					type	: "POST",
					dataType: "json",
					data	: {
						consultaAjax: 'consultarInsumo',
						term		: request.term,
						validarSaldo: false,
					},
					async	: true,
					success	: function( respuesta ){
						
						validarSessionAjax(respuesta);
						
						if( respuesta.error == 0 ){
							
							
							var data = [];
							for( var idx  in respuesta.data ){
								if( respuesta.data[idx].saldo > 0 || respuesta.data[idx].permiteNegativos )
									data.push( respuesta.data[idx] );
							}
							
							if( data.length > 0 ){
								response( data );
								$( "#msgInsumos" ).html( "" );
								$( "#dvMsgAlertas" ).css({
									display: "none",
								});
							}
							else if( $( "#inBuscadorInsumo" )[0].enterPress ){
								$( "#inBuscadorInsumo" ).val('');
								generarSonidoAlerta();
								
								var msg = "No se encontr&oacute; el insumo";
								if( respuesta.data.length == 1 ){
									if( respuesta.data[0].saldo == 0 )
										msg = "El insumo <br><b>"+respuesta.data[0].codigo+"-"+respuesta.data[0].nombreGenerico+"</b><br>se encuentra sin saldo";
								}
									
								$( "#msgInsumos" ).html( msg );
								
								$( "#dvMsgAlertas" ).css({
									display: "table",
								});
									
								$( "#inBuscadorInsumo" ).mouseover();
								
								response( data );
							}
							else{
								
								var msg = "No se encontr&oacute; el insumo";
								$( "#msgInsumos" ).html( msg );
								
								$( "#dvMsgAlertas" ).css({
									display: "table",
								});
								
								response( [] );
							}
							
						}
						else{
							//console.log( respuesta.message )
							response( [] );
						}
					}
				});
			},
			change	: function( event, ui ){
				if( !ui.item ){
					$( "#inBuscadorInsumo" ).val('');
				}
			},
			minLength	: 3,
			delay		: 300,
			autoFocus	: true,
			open		: function(event, ui){
				
				var enterPresionado = this.enterPress;				//this en este caso es $( "#inBuscadorInsumo" )
				var options 		= $(this).data("autocomplete");	//options es el objeto autocomplete asociado a $( "#inBuscadorInsumo" )
				var numOptions		= $( "li", options.menu.element[0] ).length;
				ttt = options;
				//Si el total de resultados es uno se deja la opción mostrada como seleccionada
				if( numOptions == 1 ){
					
					var opt = $( "li", options.menu.element[0] ).eq(0);
					var data = opt.data( "item.autocomplete" );			//Esto es un item de source
					data.preEnter = false;	//Para usar en posteriores opciones
					
					setTimeout(function(){
							//Event crea un evento artificial
							options.menu.activate( $.Event( 'click' ), opt );
							
							if( enterPresionado ){
								data.preEnter = true;
								options.menu.select( $.Event('click'), { item: opt });
							}
						},
						10
					);
				}
			},
			select		: function( event, ui ){
				
				if( !ui.item.implantable ){
					
					var ins = agregarArticuloALista( ui.item );
					setTimeout(function(){ 
						$( "#inBuscadorInsumo" ).val('');
						$( "#inBuscadorInsumo" ).change();
						
						var msg = ins.getMsgErrors();
						
						if( msg != '' ){
							$( "#msgInsumos" ).html( "<b>"+ins.codigo+"-"+ins.nombreGenerico+"</b><br>"+msg );
									
							$( "#dvMsgAlertas" ).css({
								display: "table",
							});
						}
							
					}, 200 );
					
					this.enterPress = false;
				}
				else{
					agregarLotePorInsumo( ui.item );
				}
			},
		}).keypress(function( event ){
			if( event.which == 13 )
				this.enterPress = true;
			else
				this.enterPress = false;
		});
		
		
		$( "#btCargarInsumos" ).click(function(){
			
			if( listaArticulos.arts ){
				
				var error = false;
				
				for( var idx in listaArticulos.arts ){
					if( !listaArticulos.arts[idx].save() ){
						error = true;
					}
				}
				
				consultarInsumosAuxiliarPorPaciente();
				
				if( error )
					jAlert( "Uno o m&aacute;s articulos no se guardaron correctamente.", "ALERTA" );
				else{
					$( "#inBuscadorPaciente" ).focus();
					$( "#btCargarInsumos" ).attr({ disabled: true });
					$( "#inBuscadorInsumo" ).attr({ disabled: true });
				}
			}
		});
		
		
		//consulto la axuliar a la que se le cargará los insumos
		$( "#inBuscadorPaciente" ).autocomplete({
			// source		: "./botiquinAplicacionInsumos.php?wemp_pmla=<?=$wemp_pmla?>&consultaAjax=consultarPaciente",
			source		: function( request, response ){
					
				$.ajax({
					url		: "./botiquinAplicacionInsumos.php?wemp_pmla=<?=$wemp_pmla?>",
					type	: "POST",
					dataType: "json",
					data	: {
						consultaAjax: 'consultarPaciente',
						term		: request.term,
						cco			: '<?=$botiquin['codigo']?>',
					},
					async	: true,
					success	: function( respuesta ){
						
						validarSessionAjax(respuesta);
						
						var datosAutocomplete = [];
						
						for( var idx in respuesta ){
							if( $.inArray( respuesta[idx].ubicacion.cco.codigo, ccoDispensables ) >= 0 
								&& ( $.inArray( auxiliar.codigo, curaciones ) >= 0 
								|| $.inArray( '*', ccoHabilitados ) >= 0
								|| $.inArray( respuesta[idx].ubicacion.cco.codigo, ccoHabilitados ) >= 0 )
								|| esAyudaDx && !respuesta[idx].esPacienteConCita
							){
								datosAutocomplete.push(respuesta[idx]);
							}
						}
						
						response( datosAutocomplete );
						
						// response( respuesta.data );
					}
				});
			},
			open		: function(event, ui){
				
				var enterPresionado = this.enterPress;				//this en este caso es $( "#inBuscadorInsumo" )
				var options 		= $(this).data("autocomplete");	//options es el objeto autocomplete asociado a $( "#inBuscadorInsumo" )
				var numOptions		= $( "li", options.menu.element[0] ).length;
				ttt = options;
				//Si el total de resultados es uno se deja la opción mostrada como seleccionada
				if( numOptions == 1 ){
					
					var opt = $( "li", options.menu.element[0] ).eq(0);
					var data = opt.data( "item.autocomplete" );			//Esto es un item de source
					data.preEnter = false;	//Para usar en posteriores opciones
					
					setTimeout(function(){
							//Event crea un evento artificial
							options.menu.activate( $.Event( 'click' ), opt );
							
							if( enterPresionado ){
								data.preEnter = true;
								options.menu.select( $.Event('click'), { item: opt });
							}
						},
						10
					);
				}
			},
			minLength	: 3,
			delay		: 300,
			change		: function( event, ui ){
				if( !ui.item )
					$( "#inBuscadorPaciente" ).val("");
			},
			select		: function( event, ui ){
				
				if( listaArticulos.length > 0 ){
					jConfirm( "Tiene insumos pendientes por cargar para el paciente "+Paciente.nombre+".", "ALERTA", function(resp){
						if( resp ){
							setPaciente( ui.item );
						}
					});
				}
				else{
					setPaciente( ui.item );
				}
			},
		}).keypress(function( event ){
			if( event.which == 13 )
				this.enterPress = true;
			else
				this.enterPress = false;
		});
		
		
		$( "#btCerrarVentana" ).click(function(){
			if( listaArticulos.length > 0 || auxiliar.codigo != "" ){
				jConfirm( "Al cerrar perder&aacute; los datos ingresados. Desea salir de la aplicaci&oacute;n?", "ALERTA", function(resp){
					if( resp ){
						cambiarEstadoEnEntrega();
						window.onbeforeunload = null;
						cerrarVentana();
					}
				});
			}
			else{
				cerrarVentana();
			}
		});
		
		
		$( "#btSeleccionarBotiquin, #btRegresar, #slBotiquin" ).change(function(){
			if( listaArticulos.length > 0 || auxiliar.codigo != "" ){
				var _self = this;
				jConfirm( "Al cambiar de botiquin perder&aacute; los datos ingresados", "ALERTA", function(resp){
					if( resp ){
						cambiarEstadoEnEntrega();
						window.onbeforeunload = null;
						document.forms[0].submit();
					}
					else{
						$( _self ).val('');
					}
				});
			}
			else{
				document.forms[0].submit();
			}
		});
		
		
		$( "#btRegresar" ).click(function(){
			if( listaArticulos.length > 0 || auxiliar.codigo != "" ){
				jConfirm( "Perder&aacute; los cambios realizados. Desea continuar?", "ALERTA", function(resp){
					if( resp ){
						cambiarEstadoEnEntrega();
						window.onbeforeunload = null;
						document.forms[0].submit();
					}
				});
			}
			else{
				document.forms[0].submit();
			}
		});
		
		
		if( $( "#dvAuxiliarEnfermeria" ).length > 0 ){
			
			actualizarSaldosBotiquin();
		}
		
		
		$( "[title]" ).tooltip({
			showURL: false,
			showBody: " - ",
		});
		
		
		
		// $( "#dvInsumosPedidos").keypress(function(e){
		$( document ).keypress(function(e){
			
			if( Pedidos && Pedidos.data != '' ){
				
				if( !this._valIngresado )
					this._valIngresado = '';

				var key =  e.which || e.keyCode;

				if( key >= 65 && key <= 90 ||  key >= 97 && key <= 122 ||  key >= 48 && key <= 57 ){
					this._valIngresado += e.key;
				}
				else{
					// console.log( "valor ingresado..." )
					// console.log( this._valIngresado )
					
					this._valIngresado = BARCOD( this._valIngresado );
					
					var codigoOriginal = Pedidos.data.codigosBarra[ $.trim( this._valIngresado.toUpperCase() ) ];
					
					var insumo = Pedidos.data.insumos[ codigoOriginal ];
					
					//Si existe el articulo voy campo a campo para sumarle uno al valor
					if( insumo ){
						var cargo = false;
						var pedidos = insumo.pedido;

						for( var x in pedidos ){
							// console.log( "Pedido en el document" );
							// console.log( pedidos[x] );
							if( pedidos[x].cantidadSolicitada*1 > pedidos[x].cantidadCargada.val()*1 ){
								pedidos[x].cantidadCargada.val( pedidos[x].cantidadCargada.val()*1+1 );
								pedidos[x].cantidadCargada.change();
								$( "#tbPedidos input:text" ).removeClass("fondoamarillo");
								pedidos[x].cantidadCargada.addClass("fondoamarillo");
								cargo = true;
								break;
							}
						}
						
						if(!cargo){
							$( "#tbPedidos input:text" ).removeClass("fondoamarillo");
							$( "#tbPedidos td" ).removeClass("fondorojo");
							$( "td", pedidos[x].cantidadCargada.parent().parent() ).eq(-2).addClass('fondorojo');
							generarSonidoAlerta();
						}
						
						$( "#lbInsumoLeido" )
							.html( "<b>Insumo le&iacute;do: </b>"+insumo.insumo.codigo+"-"+insumo.insumo.nombreGenerico )
							.css({display:''})
						$( "#inInsumoPedido" ).focus();
					}
					
					this._valIngresado = '';
					e.stopPropagation();
				}
			}
		}).click(function(){
			this._valIngresado = '';
		});
		
		// $( "#dvInsumosPedidos").keypress(function(e){
		$( "#inInsumoPedido" ).keypress(function(e){
			
			$( "#lbInsumoLeido" )
				.html('')
				.css({display:'none'});
	
			_valIngresado = $( this ).val();
	
			if( !_valIngresado )
				_valIngresado = '';

			var key =  e.which || e.keyCode;

			// if( key >= 65 && key <= 90 ||  key >= 97 && key <= 122 ||  key >= 48 && key <= 57 ){
				// _valIngresado += e.key;
			// }
			// else{
			//Si enter
			if( key == 13 ){
				
				// var insumo = Pedidos.data.insumos[ $.trim( _valIngresado.toUpperCase() ) ];
				
				_valIngresado = BARCOD( _valIngresado );
				
				var codigoOriginal = Pedidos.data.codigosBarra[ $.trim( _valIngresado.toUpperCase() ) ];
					
				var insumo = Pedidos.data.insumos[ codigoOriginal ];
				
				//Si existe el articulo voy campo a campo para sumarle uno al valor
				if( insumo ){
					
					$( "#lbInsumoLeido" )
						.html( "<b>Insumo le&iacute;do: </b>"+insumo.insumo.codigo+"-"+insumo.insumo.nombreGenerico )
						.css({display:''});
					
					var cargo = false;
					var pedidos = insumo.pedido;

					for( var x in pedidos ){
						
						if( pedidos[x].cantidadSolicitada*1 > pedidos[x].cantidadCargada.val()*1 ){
							console.log( "pppp...." );
							console.log( insumo );
							if( !insumo.insumo.implantable ){
								pedidos[x].cantidadCargada.val( pedidos[x].cantidadCargada.val()*1+1 );
								pedidos[x].cantidadCargada.change();
								$( "#tbPedidos input:text" ).removeClass("fondoamarillo");
								pedidos[x].cantidadCargada.addClass("fondoamarillo");
							}
							else{
								diligenciarLote( insumo.insumo, pedidos[x].cantidadCargada, pedidos[x] );
							}
							
							cargo = true;
							
							break;
						}
					}
					
					if(!cargo){
						
						$( "#tbPedidos input:text" ).removeClass("fondoamarillo");
						$( "#tbPedidos td" ).removeClass("fondorojo");
						// if( !insumo.insumo.permiteNegativos ){
							$( "td", pedidos[x].cantidadCargada.parent().parent() ).eq(-2).addClass('fondorojo');
						// }
						generarSonidoAlerta();
					}
				}
				else{
					$( "#lbInsumoLeido" )
						.html( "El insumo con c&oacute;digo <b>"+$( this ).val()+"</b> no se encuentra en la lista de pedidos." )
						.css({display:''});
					generarSonidoAlerta();
				}
				
				_valIngresado = '';
				
				$( this ).val( '' );
				
				e.stopPropagation();
				
			}
		}).change(function(){ $( this ).val(''); });
		
		
		$( "#dvTabs" ).tabs();
		
		var tiempo_cierre = <?=$cierreAutomatico;?>;
		if( tiempo_cierre ){
		   //Variable que contiene la funcion salir_sin_grabar que despues de cinco minutos se activa.
			var timerHandle = setTimeout(function() {
				window.onbeforeunload = null;
				cambiarEstadoEnEntrega();
				cerrarVentana();
			}, tiempo_cierre * 60 * 1000 );

			//Esta funcion reunicia el cerrado automatico.
			function resetTimer() {
				window.clearTimeout(timerHandle);
				timerHandle = setTimeout(function(){ 
					window.onbeforeunload = null;
					cambiarEstadoEnEntrega(); 
					cerrarVentana(); 
				}, tiempo_cierre * 60 * 1000);
			}
			
			//Si hay movimiento del mouse la funcion de cerrado automatico se reinicia.
			$(document).on('mousemove', function(e){
				resetTimer();
			});

			//Si hay movimiento del teclado la funcion de cerrado automatico se reinicia.
			$(document).on('keypress', function(e){
				resetTimer();
			});
	   }
		
	});
	
	window.onbeforeunload = function(){

		try{
			cambiarEstadoEnEntrega();
			// if( Pedidos || listaArticulos.length > 0 || auxiliar.codigo != "" )
				// return "Perderá los datos ingresados.";
		}
		catch(e){}
	}
	
	window.onunload = function(){
		cambiarEstadoEnEntrega();
	}
</script>

</head>

<style type="text/css">

	body{
		width: auto;
		height: auto;
	}

	fieldset{
		border: 2px solid #e0e0e0;
		height: auto;
		margin: 10px auto;
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
	
	#dvBotiquin{
		margin: 0 auto;
		width: 50%;
		text-align: center;
		font-size: 20px;
		background-color: #f7f5a3;
		padding: 20px;
		top: 25px;
		# height: 50px;
		position: relative;
		border-radius: 10px 10px 10px 10px;
		-moz-border-radius: 10px 10px 10px 10px;
		-webkit-border-radius: 10px 10px 10px 10px;
		border: 0px solid #000000;
	}

	#dvDatosRegentes, #dvDatosAuxiliar{
		width: 100%;
		clear: both;
	}
	
	#dvDatosRegentes > div{
		margin: 0 auto;
		width: 80%;
		height: 175px;
	}
	
	#dvDatosRegentes > div > div{
		float: left;
		width: 40%;
		margin: 0 20px;
		height: 100%;
	}
	
	#dvDatosRegentes > div > div:nth-child(1){
		margin: 0 2% 20px 8%;
	}
	
	#dvDatosRegentes > div > div:nth-child(2){
		margin: 0 8% 20px 2%;
	}
	
	#inBuscadorAuxiliar{
		width: 100%;
	}
	
	fieldset > div{    
		margin: 0 auto;
		width: 90%;
		font-weight: bold;
	}
	
	#dvDatosAuxiliar{
		width: 90%;
		margin: 20px auto;
	}
	
	#dvAuxiliarEnfermeria{
		font-size: 20px;
		font-weight: bold;
		text-align: center;
		background: #2a5db0;
		color: white;
		border-radius: 0px 10px 10px 0px;
		-moz-border-radius: 0px 10px 10px 0px;
		-webkit-border-radius: 0px 10px 10px 0px;
		margin: 10px auto 20px;
		width: 70%;
		padding: 26px;
		position:relative;
	}
	
	#dvInsumosACargar button{
		width: 250px;
		height: 30px;
	}
	
	#inBuscadorInsumo{
		width: 270px;
	}
	
	#tbListaInsumosACargar{
		margin: 20px auto;
		font-size: 10pt;
		padding: 0 0 20px 0;
	}
	
	#tbListaInsumosACargar tbody > tr > td > input{
		width: 70px;
	}
	
	#tbListaInsumosACargar tbody > tr > td:nth-child(n+4){
		width: 40px;
		text-align: center;
	}
	
	#tbListaInsumosACargar tbody > tr:nth-child(odd), #tbInsumosPendientes > tbody > tr:nth-child(odd), #tbPedidos > tbody > tr:nth-child(odd){
		background: #E8EEF7;
	}
	 
	#tbListaInsumosACargar tbody > tr:nth-child(even), #tbInsumosPendientes > tbody > tr:nth-child(even), #tbPedidos > tbody > tr:nth-child(even){
		background-color: #C3D9FF;
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
	
	#dvContenidoInsumos > div{
		float: left;
		display: inline-block;
		background-color: #fafafa;
		height: 100%;
	}
	
	#dvInsumosACargar > div{
		width:80%;
		margin: 0 auto;
	}
	
	#tbInsumosPendientes{
		margin: 0 auto;
		width: 90%;
	}
	
	#dvListaInsumosACargar{
		width: 100%;
		min-height: 230px;
		/*overflow: auto;*/
	}
	
	
	
	
	#dvMsgInsumosPendientesAuxiliar{
		/*height: 500px;*/
		vertical-align: middle;
		font-size: 18px;
		text-align: center;
		display: table-cell;
		width: 100%;
		position: relative;
		top: 100px;
	}
	
	
	#dvMsgInsumosPendientesAuxiliar span{
		background-color: #FFFFCC;
		display: block;
		width: 80%;
		margin: 0 auto;
		padding: 20px;
		border-radius: 10px 10px 10px 10px;
		-moz-border-radius: 10px 10px 10px 10px;
		-webkit-border-radius: 10px 10px 10px 10px;
	}
	
	#tbPedidos input{
		width:30px;
	}
	
	#tbPedidos{
		font-size:10pt;
	}
	
	#lbInsumoLeido{
		background-color: #FFFFCC;
		padding: 5px 15px;
		border-radius: 2px 2px 2px 2px;
		-moz-border-radius: 2px 2px 2px 2px;
		-webkit-border-radius: 2px 2px 2px 2px;
	}
	
	.ui-tabs .ui-tabs-panel{
		padding: 0px;
	}
	.ui-widget-content{
		background: #fafafa;
	}
</style>

<body>
<?php

	$wactualiz = "2017-10-30";
	encabezado( "BOTIQUIN - DISPENSACION DE INSUMOS", $wactualiz, "clinica" );
	
	$regente = consultarUsuario( $conex, $wusuario );
	
?>

<form>
	<input type='hidden' name='wemp_pmla' value='<?=$wemp_pmla?>'>
	<input type='hidden' id='validarSaldoAuxiliar' value='<?php echo $validarSaldoAuxiliar ? 1: "0";?>'>
	<a id=aaa></a>
	<?php
	if( !isset( $slBotiquin ) ){
	?>
	<div id='dvDatosRegentes'>
		<div>
			<div>
			
				<div>
					<fieldset>
						<legend>Usuario</legend>
						<div>
							<table style='width:100%;'>
								<tr>
									<td class='fila1' style='width:150px;'><b>Cargo</b></td>
									<td class='fila2'><?=$cargoUsuario['descripcion']?></td>
								</tr>
								<tr>
									<td class='fila1' style='width:150px;'><b>C&oacute;digo y nombre</b></td>
									<td class='fila2'><?=$regente->codigo?>-<?=$regente->descripcion?></td>
								</tr>
							</table>
							
								
						</div>
					</fieldset>
					
					<fieldset>
						<legend>Seleccione un botiquin</legend>
						<div>
							<table style='width:100%;'>
								<tr>
									<td class=fila1 style='width:150px;'><b>Botiquines</b></td>
									<td class=fila2>
										<select id='slBotiquin' name='slBotiquin' onChange='form.submit()'>
											<option value=''>Seleccione...</option>
											<?php
												foreach( $botiquines as $key => $value ){
													echo "<option value='".$value['codigo']."'>".$value['descripcion']."</option>";
												}
											?>
										</select>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
				
			</div>
			
			<div>
				<div id='dvBotiquin'>
					SELECCIONE UN BOTIQUIN
				</div>
			</div>
			
		</div>
	</div>
	
	<div style='text-align:center;padding:20px;'>
		<input type='button' value='Cerrar ventana' onClick='cerrarVentana();'>
	</div>
	<?php
	}
	else{
	?>
	<div id='dvDatosRegentes'>
		<div>
			<div>
			
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
									<td class='fila2'><?=$regente->codigo?>-<?=$regente->descripcion?></td>
								</tr>
							</table>
						</div>
					</fieldset>
					
					<fieldset style='display:<?=!empty( $botiquin['codigo'] ) ? '': 'none'?>;'>
						<legend>Responsable de insumos</legend>
						<div>
							<input type='text' id='inBuscadorAuxiliar' placeholder='Buscar auxiliar'>
						</div>
					</fieldset>
				</div>
				
			</div>
			
			<div>
				<div id='dvBotiquin'>
					<?=!empty( $botiquin['codigo'] ) ? '': 'SELECCIONE UN<BR>'?>
					BOTIQUIN<BR>
					<?=$botiquin['nombre']?>
				</div>
				<div style='text-align:center;height:auto;position:relative;top:30px;display:;'>
					<!-- <input type='button' id='btSeleccionarBotiquin' value='Regresar a seleccion de botiquin'> -->
					
					<table style='margin:0 auto;'>
						<tr>
							<td class='fila1'><b>Cambiar de botiquin:</b></td>
							<td class='fila2'>
								<select id='slBotiquin' name='slBotiquin'>
									<option value=''>Seleccione...</option>
									<?php
										foreach( $botiquines as $key => $value ){
											if( $botiquin['codigo'] != $value['codigo'] )
												echo "<option value='".$value['codigo']."'>".$value['descripcion']."</option>";
										}
									?>
								</select>
							</td>
						</tr>
					</table>
					
					
					<a href='./monitorInsumosBotiquin.php?wemp_pmla=<?=$wemp_pmla?>&wcco=<?=$botiquin['codigo']?>' target=new>Ir a Monitor insumos</a>	
				</div>
				
				
				
			</div>
			
		</div>
		
	</div>
	
	<div style='display:none;'>
		<div style='width:80%;margin:0 auto;text-align:right'>
			<a href='./monitorInsumosBotiquin.php?wemp_pmla=<?=$wemp_pmla?>&wcco=<?=$botiquin['codigo']?>'>Ir a Monitor insumos</a>
		</div>	
	</div>
	
	<div id='dvDatosAuxiliar' style='display:none;'>
		<div>
			<div id='dvAuxiliarEnfermeria'>
				AQUI VA EL NOMBRE DE LA AUXILIAR
			</div>
			
			<div id='dvTabs' style='display: table;width: 100%;;height:40%;'>
				<ul>
					<li><a href='#dvContenidoInsumos'><span>Adicionar insumo no pedido</span></a></li>
					<li><a href='#dvInsumosPedidos'><span>Pedido</span></a></li>
				</ul>
			<!--</div> -->
			
				<div id='dvContenidoInsumos' style='display: table;width: 100%;height: 90%;padding:10px;'>
					<div id='dvInsumosPorPaciente' style='width: 29%;border-right: solid white 5px;'>
						<div id='dvMsgInsumosPendientesAuxiliar'>
							<div>
								<div>
									<span>DEBE SELECCIONAR UN PACIENTE</span>
								</div>
							</div>
						</div>
					</div>
					
					<div id='dvInsumosACargar' style='width:70%;position:relative;'>
					
						<div class='fila1'>
							<label style='font-weight:bold;'>Consultar paciente</label>
							<input type='text' id='inBuscadorPaciente' placeholder='Buscar paciente' style='width:250px;'>
							<img src='../../images/medical/root/help.png' style='width:17px;height:17px;' title='Buscador de pacientes - Puede buscar el paciente por habitaci&oacute;n, historia,<br>n&uacute;mero de documento o nombre'>
						</div>
						
						<div id='dvInfoPaciente' style='margin: 20px auto;'>
						</div>
						
						<div>
							<table style='width:100%;'>
								<tr class='fila1'>
									<td style='width:70px;'><label style='font-weight:bold;'>Insumo</label></td>
									<td class=fila2 style='width:300px;'><input type='text' id='inBuscadorInsumo' placeholder='Buscar insumo a cargar'><img src='../../images/medical/root/help.png' style='width:17px;height:17px;' title='Buscador de insumos - Puede buscar un insumo por codigo,<br>nombre generico o nombre comercial'></td>
									<td>
										<div class="fondoamarillo" style='display:none;width:100%;' id='dvMsgAlertas'>
											<div style='display:table-row;'>
												<div href="#null" style='display:table-cell;padding:5px;vertical-align:middle;'>
													<img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17>
												</div>
												<div id='msgInsumos' style='display:table-cell;padding:5px;'></div>
											</div>
										</div>
									</td>
								</tr>
							</table>
						</div>
						
						<div id='dvListaInsumosACargar'>
						</div>
						
						<div style='text-align:center;position: absolute;bottom: 0;margin: 0 auto;width: 100%;'>
							<input type='button' id='btCargarInsumos' value='Cargar al responsable'>
						</div>
						
					</div>
				</div>
			
				<div id='dvInsumosPedidos' style='padding:10px;'>
					
					<div style='font-size:10pt;padding:5px;'>
						<label><b>Insumo a cargar</b></label>
						<input id='inInsumoPedido' type='text' value=''>
						<label id='lbInsumoLeido' style='display:none'></label>
					</div>
				
					<table id='tbPedidos' style='margin: 0 auto;'>
						<thead class='encabezadotabla'></thead>
						<tbody></tbody>
					</table>
				
					<div style='text-align:center;padding:10px;'>
						<input type='button' id ='btCargarPedido' value='Cargar pedido al responsable' onClick='cargarPedidoAuxiliar();' style='font-size:10pt;font-family:verdana;'>
					</div>
				</div>
		
			</div>
		</div>
	</div>
	
	<div style='text-align:center;padding:20px;clear:both;'>
		<input type='button' id ='btCerrarVentana' value='Cerrar ventana'>
		<input type='button' id='btRegresar' value='Regresar a seleccion de botiquin' style='display:none;'>
	</div>
	
	<?php
	}
	?>
</form>
</body>
</html>
<?php
}

if( isset($auxiliar) && !empty( $auxiliar ) ){
	
	//Se busca la auxiliar por codigo exacto
	$dataAux = array();
	$dataAux = consultarAuxiliarEnfermeria( $conex, $wemp_pmla, $wbasedato, $whce, $auxiliar, $botiquin['codigo'], true );
	$datosAux['ui']['item'] = $dataAux['data'][0];
	
	$datosAux['ui']['item']['nombre']				= ( $datosAux['ui']['item']['nombre'] );
	$datosAux['ui']['item']['rol']['codigo']		= ( $datosAux['ui']['item']['rol']['codigo'] );
	$datosAux['ui']['item']['rol']['nombre']		= ( $datosAux['ui']['item']['rol']['nombre'] );
	$datosAux['ui']['item']['rol']['descripcion'] 	= ( $datosAux['ui']['item']['rol']['descripcion'] );
	$datosAux['ui']['item']['value']  				= ( $datosAux['ui']['item']['value'] );
	$datosAux['ui']['item']['label']  				= ( $datosAux['ui']['item']['label'] );
	
	// echo "<pre>"; var_dump( $datosAux ); echo "</pre>";
	?>
	<script>
		seleccionarAuxiliar( '', <?=json_encode($datosAux['ui']) ?> );
	</script>
	<?php
}