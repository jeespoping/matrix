<?php
include_once("conex.php");

/***************************************************************
 * Script para ejecutar los queries no realizados en unix
 ***************************************************************/

/****************************************************************************************
 * Inserta el log en la tabla unix
 ****************************************************************************************/
function insertarLogUnix( $conex, $wbasedato, $programa, $funcion, $msg, $parametros, $sql, $estado = 'on' ){
	
	$val = false;
	
	$fecha 	= date( "Y-m-d" );
	$hora 	= date( "H:i:s" );
	
	$sql = "INSERT INTO ".$wbasedato."_000247(       Medico    ,  Fecha_data ,  Hora_data ,     Logprg     ,     Logfnc     ,  Logmsg  ,      Logpar      ,  Logsql   ,   Logest    ,      Seguridad      )
									  VALUES( '".$wbasedato."', '".$fecha."', '".$hora."' , '".$programa."', '".$funcion."' ,'".$msg."', '".$parametros."', '".$sql."','".$estado."', 'C-".$wbasedato."' )";
									  
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 )
		$val = true;
	
	return $val;
}
 
/************************************************************************************
 * Actualiza el saldo para un articulo
 ************************************************************************************/
function actualizarSaldoInventarios( $conex_u, $conex, $cco, $can, $art, $mul ){

	$set = "";

	if( $mul < 0 ){
		$set = "salsal";
	}
	else{
		$set = "salent";
	}

	$sql = "UPDATE ivsal
			SET
				$set = $set + $can
			WHERE
				salano = '".date( "Y" )."'
				AND salmes = '".date( "m" )."'
				AND salser = '$cco'
				AND salart = '$art'
			";
			
	@$res = odbc_do( $conex_u, $sql );	// or die( "Error en el query $sql " );
	
	if( !$res ){
		$msg 		= "Error al actualizar ivsal";
		$parametros = mysql_escape_string( $sql );
		$esSql 		= 'on';
		$estado		= 'on';
		insertarLogUnix( $conex, "movhos", 'cargosPDA', 'actualizarSaldoInventarios', $msg, $parametros, $esSql, $estado );
	}
}
 
/************************************************************************
 * Consulta el multiplicador del concepto.
 * Esta función retorn 1 o -1
 ************************************************************************/
function consultarMultiplicador( $conex_u, $fue ){

	$val = "";

	$sql = "SELECT conmul
			  FROM ivcon
			 WHERE concod = '$fue'
			";
	
	$res = odbc_do( $conex_u, $sql ) or die( "Error en el query $sql " );
	
	if( odbc_fetch_row($res) ){
		$val = odbc_result( $res, 1 );
	}
	
	return $val;
}
 
/************************************************************************************
 * Registra un detalle de movimiento de de inventarios
 ************************************************************************************/
function registrarMovimientoInventarios( $conex, $conex_u, $fue, $doc, $item, $concepto, $art, $cantidad, $uni, $precio, $descuento, $iva, $total, $costo, $fueAnexa, $docAnexa ){
	
	$fecha = date( "Y-m-d" );
	list( $year, $moth ) = explode( "-", $fecha );

	$sql = "INSERT INTO ivmovdet( movdetfue, movdetdoc, movdetite, movdetano, movdetmes, movdetcon  , movdetart, movdetcan, movdetuni, movdetpre, movdetdes , movdetiva, movdettot, movdetcos, movdetfan   , movdetdan, movdetanu )
						  VALUES(   '$fue' ,   $doc   ,   $item  ,  '$year' ,  '$moth' , '$concepto',   '$art' , $cantidad,   '$uni' ,  $precio , $descuento,   $iva   ,  $total  ,   $costo , '$fueAnexa' , $docAnexa,    '0'    )
			";
	
	@$res = odbc_do( $conex_u, $sql );	// or die( odbc_error()." - Error en el query $sql - ".odbc_errormsg() );
	
	if( !$res ){
		$msg 		= "Error al insertar en ivmovdet";
		$parametros = mysql_escape_string( $sql );
		$esSql 		= 'on';
		$estado		= 'on';
		insertarLogUnix( $conex, "movhos", 'cargosPDA', 'registrarMovimientoInventarios', $msg, $parametros, $esSql, $estado );
	}
}
 
/**********************************************************************
 * Consultar precio promedio según la tabla ivsal para el mes y año
 * correspondiente
 **********************************************************************/
function consultarPrecioPromedio( $conex_u, $cco, $art ){

	$val = 0;

	//Diciembre 15 de 2017. Se consulta el precio del articulo en ivartpro y no de ivsal
	//Consulto el precio del articulo
	//En caso de no encontrar registro el valor es 0
	$sql = " SELECT artpropro
			   FROM ivartpro
			  WHERE artproano = '".date( "Y" )."'
				AND artpromes = '".date( "m" )."'
				AND artproart = '".$art."'
			";
			
	$res = odbc_do( $conex_u, $sql ) or die( "Error en el query $sql " );

	if( odbc_fetch_row($res) ){
		$val = trim( odbc_result( $res, 1 ) );
		$val = empty( $val ) ? 0: $val;
	}
	
	return $val;
}
 
/************************************************************************************
 * Registra un encabezado de movimiento de de inventarios
 ************************************************************************************/
function registrarEncabezadoMovimientoInventarios( $conex, $conex_u, $fue, $doc, $concepto, $ccoOrigen, $ccoDestino, $nit, $docNit, $fuenteFactura, $docFactura ){

	$fecha = date( "Y-m-d" );
	list( $year, $moth ) = explode( "-", $fecha );

	$sql = "INSERT INTO ivmov( movfue, movdoc, movano , movmes , movfec  ,  movcon    ,    movser   , movnit, movdni  ,     movffa      ,    movdfa  , movanu )
						VALUES( '$fue',  $doc , '$year', '$moth', '$fecha', '$concepto', '$ccoOrigen', '$nit', $docNit , '$fuenteFactura', $docFactura,   '0'   )
			";
	
	@$res = odbc_do( $conex_u, $sql ); // or die( "Error en el query $sql " );
	
	if( !$res ){
		$msg 		= "Error al insertar en ivmov";
		$parametros = mysql_escape_string( $sql );
		$esSql 		= 'on';
		$estado		= 'on';
		insertarLogUnix( $conex, "movhos", 'cargosPDA', 'registrarEncabezadoMovimientoInventarios', $msg, $parametros, $esSql, $estado );
	}
}	
 
/****************************************************************
 * Consulta el nro de documento para una fuente y la actualiza
 ****************************************************************/
function consultarConsecutivoFuente( $conex_u, $fue ){

	$doc = 0;

	$sql = "SELECT fuesec
			  FROM sifue
			 WHERE fuecod = '$fue'
			";
	
	$res = odbc_do( $conex_u, $sql );
	
	if( $res ){
		
		if( odbc_fetch_row($res) ){
		
			$doc = odbc_result($res,1);
			
			$sql = "UPDATE sifue
					   SET fuesec = fuesec+1
					 WHERE fuecod = '$fue'
					";
		
			@$res = odbc_do( $conex_u, $sql );
			
			if( !$res ){
				$doc = 0;
			}
		}
	}
	
	return $doc;
}

/****************************************************************
 * Ajusta el inventario
 * $articulos	Array con las siguientes posiciones
 *				cod		Código del artículo
 *				uni 	Unidad del artículo
 *				fra 	Fracción del artículo
 *				can		Cantidad
 ****************************************************************/
function ajustarInventario( $conex, $conex_u, $fue, $concepto, $cco, $articulos ){

	/*********************************************************************
	 * Esto se hace siempre y cuando $articulo no este vacio
	 *
	 * 1. Se crea el encabezado de movimiento de acuerdo a fuente y cco
	 * 2. Se crea el detalle de movimiento
	 * 3. Se mueve el inventario 
	 *********************************************************************/
	 
	$proveedor = '6601';	//Siempre es este, es un proveedor generico
	$docProveedor = 0;	//Siempr vacio
	$fuenteFactura = '';
	$docFactura = 0;
	$ccoDestino = '';
	 
	if( !empty( $articulos ) && count( $articulos ) > 0 ){
	 
		 /*********************************************************************
		  * 1. Se crea el encabezado de movimiento de acuerdo a fuente y cco
		  *********************************************************************/
		 
		 //Consulto el nro de documento para la fuente dada
		 $doc = consultarConsecutivoFuente( $conex_u, $fue );
		 
		 if( !empty( $doc ) ){
		 
			 registrarEncabezadoMovimientoInventarios( $conex, $conex_u, $fue, $doc, $concepto, $cco, $ccoDestino, $proveedor, $docProveedor, $fuenteFactura, $docFactura );
			 
			 /*********************************************************************
			  * Grabo el detalle de movimiento
			  *********************************************************************/
			 $item = 0;
			 foreach( $articulos as $artKey => $art ){
				
				$item++;
				$descuento = 0.0; 
				$iva = 0.0;
				$fueAnexa = '';
				$docAnexa = 0;
			 
				$precioArticulo = consultarPrecioPromedio( $conex_u, $cco, $art[ 'cod' ] );
			 
				$precio = $precioArticulo*$art[ 'can' ];
				$total = $precio;
				$costo = $precio;
			 
				registrarMovimientoInventarios( $conex, $conex_u, $fue, $doc, $item, $concepto, $art[ 'cod' ], $art[ 'can' ], $art[ 'uni' ], $precio, $descuento, $iva, $total, $costo, $fueAnexa, $docAnexa );
				
				$mul = consultarMultiplicador( $conex_u, $concepto );
				actualizarSaldoInventarios( $conex_u, $conex, $cco, $art[ 'can' ], $art[ 'cod' ], $mul );
			 }
		}
		else{
			//Hubo error al consultar los datos de sifue
			$msg 		= "Error al consultar o actualizar sifue";
			$parametros = array(
				'fue' 		=> $fue,
				'concepto'	=> $concepto,
				'cco'		=> $cco,
				'articulos'	=> $articulos,
			);
			$esSql 	= 'off';
			$estado = 'on';
			insertarLogUnix( $conex, "movhos", 'cargosPDA', 'ajustarInventario', $msg, json_encode( $parametros ), $esSql, $estado );
		}
	}
}
 

/************************************************************************************************************************
 * Consulta los insumos de un producto
 ************************************************************************************************************************/
function ejecutarLogUnix( $conex_u, $conex, $wbasedato ){
	
	$wemp_pmla = "01";
	
	if( $conex_u ){
		
		$enviarCorreo = false;
		$ids = "";
		
		$sql = "SELECT Logmsg, Logpar, Logsql, Logest, id
				  FROM ".$wbasedato."_000247
				 WHERE logest = 'on'
				   AND logprg = 'cargosPDA'
				 ";
										  
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
			
			while( $rows = mysql_fetch_array( $res ) ){
				
				$accion = strtolower( $rows[ 'Logsql' ] );
				
				//Es un sql de unix
				if( $accion == 'on' ){
					
					//Si es un sql se ejecuta
					$sql_u = $rows['Logpar'];
					
					@$res_u = odbc_do( $conex_u, $sql_u );
					
					//Si se ejecuta correctamente pongo en off el registro para que no se vuelva a ejecutar
					// if( $res_u ){
						
						$sqlupt = "UPDATE ".$wbasedato."_000247
									  SET logest = 'off'
									WHERE id = '".$rows[ 'id' ]."'";
														  
						$resupt = mysql_query( $sqlupt, $conex ) or die( mysql_errno()." - Error en el query $sqlupt - ".mysql_error() );
					// }
					
					if( !$res_u ){
						$enviarCorreo = true;
						$ids .= $rows[ 'id' ].",";
					}
				}
				else{
					//Son parametros de función
					$params 	= json_decode( $rows['Logpar'], true );
					
					$fue		= $params['fue'];
					$concepto	= $params['concepto'];
					$cco		= $params['cco'];
					$articulos	= $params['articulos'];
					
					ajustarInventario( $conex, $conex_u, $fue, $concepto, $cco, $articulos );
					
					$sqlupt = "UPDATE ".$wbasedato."_000247
								  SET logest = 'off'
								WHERE id = '".$rows[ 'id' ]."'";
														  
					$resupt = mysql_query( $sqlupt, $conex ) or die( mysql_errno()." - Error en el query $sqlupt - ".mysql_error() );
				}
			}
		}
	
		if( $enviarCorreo ){
			
			$email        		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailDsi");
			$email        		= explode("--", $email );
			$wdestinatarios[]	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailSoporte");
			
			/*************************************************************************
			 * Si es un tercer intento se debe hacer un correo
			 *************************************************************************/
				
			$wasunto 			= "Error al ejecutar query unix";
			$mensaje 			= "No se lográ ejecutar query en unix correspondiente a los ids ".$ids." de la tabla ".$wbasedato."_000247";
			$altbody 			= "";
			
			$wremitente			= array( 'email'	=> $email[0],
										 'password' => $email[1],
										 'from' 	=> $email[0],
										 'fromName' => 'Desarrollo de sofware, PMLA',
								 );
			
			sendToEmail( $wasunto, $mensaje, $altbody, $wremitente, $wdestinatarios );
			
			/*************************************************************************/
		}
	}
}


include_once("root/comun.php");





echo date("Y-m-d H:i:s")." : Realizando conexi&oacute;n unix.<br>";
$conex_o = odbc_connect('inventarios','','');

if($conex_o)
	echo date("Y-m-d H:i:s")." : Conexi&oacute;n a unix realizada.<br>";

$wbasedato = "movhos";

echo date("Y-m-d H:i:s")." : Ejecutando log Unix.<br>";
ejecutarLogUnix( $conex_o, $conex, $wbasedato );

echo date("Y-m-d H:i:s")." : Fin ejecuci&oacute;n Log Unix<br>";
odbc_close( $conex_o );