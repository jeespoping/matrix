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
 * - Para cada tipo de articulo, dado una ronda, y tiempo para el que se produce, mostrar la lista de articulos que se van a producir con su correspondiente cantidad.
 * 	 Si para un tipo de articulo no se va a producir nada, no mostrar la lista.
 * 
 * - Se graba el encabezado del ciclo de produccion (movhos_000106) cuando se confirma toda la producción o si no hay nada para producir para el articulo.
 * 
 * - El usuario puede elegir que ronda producir si lo desea, esto se le llama producción no porgramada, de lo contrario el programa seguirá mostrando que ronda
 *   es la siguiente en producir.
 *   
 * - EL programa debe tener un filtro por cco.
 * 
 * - Crear los productos automaticamente en CM. Se debe verificar que las presentaciones existan, y que el producto no este creado.
 * 
 * - Hacer traslado automatico de articulos.
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
 * Modalidad:					Hay dos tipos de modalidad, la programada y no programada.  La programada es la que por si solo se va cambiando de hora para producir, mientras que la 
 * 								no programada es en la que puede elegir que ronda producir.
 * 
 ************************************************************************************************************************************************************************/
 
 /************************************************************************************************************************************************************************
  * Modificaciones:
  * 
  * Febrero 21 de 2022 Marlon Osorio. - Se parametriza el centro de costos 1051.
  * Julio 11 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones 
  * consultaCentrosCostos que hace la consulta de los centros de costos 
  * de un grupo seleccionado y dibujarSelect que dibuja el select con los 
  * centros de costos obtenidos de la primera funcion.
  *
  * Agosto 8 de 2011.	(Edwin MG).	Se agrega tipo ficticio SADT (Sin asignación de tipo), que indica que medicamentos estan mal configurados en central de mezclas
  ************************************************************************************************************************************************************************/

/************************************************************************************************************************************************************************
 * 																					FUNCIONES
 ************************************************************************************************************************************************************************/


/*********************************************************************************************************************************************************
 * 												FUNCIONES PARA CREAR INSUMO Y PRODUCTOS AUTOMATICAMENTE
 *********************************************************************************************************************************************************/

/**
 * Crea el nombre de un aritculo
 */
function crearNombreInsumo( $conex, $wcenmez, $detalleTipo ){
	
	$consecutivo = $detalleTipo['Tipcon']+1;
	
	$num = strlen( $consecutivo );
	
	if( $num < 4 ){
		$codigo = $detalleTipo['Tipdis'].str_repeat( "0", 4 - $num ).$consecutivo;
	}
	else{
		$codigo = $detalleTipo['Tipdis'].$consecutivo;
	}
	
	$sql = "UPDATE
				{$wcenmez}_000001
			SET
				Tipcon = Tipcon+1
			WHERE
				id = '{$detalleTipo[ 'id' ]}'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return $codigo;
}

/**
 * 
 * @param $conex
 * @param $wcenmez
 * @param $insumo
 * @param $presentacion
 * @param $cco
 * @param $conversion
 * @param $existencias
 * @param $fechaVencimiento
 * @param $unidad
 * @param $costo
 * @return unknown_type
 */
function registrarPresentacion( $conex, $wcenmez, $insumo, $presentacion, $cco, $conversion, $existencias, $fechaVencimiento, $unidad, $costo ){
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO 
			{$wcenmez}_000009(   Medico , Fecha_data, Hora_data,  Appcod  ,     Apppre     , Appcco,    Appcnv    ,      Appexi   ,       Appfve       ,   Appuni ,  Appcos , Appest,   Seguridad  ) 
				       VALUES( '$wcenmez', '$fecha'  , '$hora'  , '$insumo', '$presentacion', '$cco', '$conversion', '$existencias', '$fechaVencimiento', '$unidad', '$costo',  'on' , 'C-$wcenmez' )";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Registra el insumo correspondiente para un prodcuto
 * @param $conex
 * @param $wcenmez
 * @param $producto
 * @param $insumo
 * @param $cantidad
 * @param $factor
 * @param $aplicaPurga
 * @return unknown_type
 */
function registrarInsumo( $conex, $wcenmez, $producto, $insumo, $cantidad, $factor, $aplicaPurga ){
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO 
			{$wcenmez}_000003(   Medico , Fecha_data, Hora_data,    Pdepro  ,  Pdeins  ,   Pdecan   ,  Pdefac  ,    Pdeest     ,    Pdeapp     , Seguridad  ) 
				       VALUES( '$wcenmez', '$fecha'  , '$hora'  , '$producto', '$insumo', '$cantidad', '$factor',     'on'     , '$aplicaPurga', 'C-$wcenmez' )";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/**
 * 
 * @param $conex
 * @param $wcenmez
 * @param $wbasedato
 * @param $codigo
 * @param $nombreComercial
 * @param $nombreGenerico
 * @param $unidad
 * @param $via
 * @param $tiempoInfusion
 * @param $tiempoVecimiento
 * @param $fechaCreacion
 * @param $tipo
 * @return unknown_type
 */
function registrarArticuloNuevo( $conex, $wcenmez, $wbasedato, $codigo, $nombreComercial, $nombreGenerico, $unidad, $via, $tiempoInfusion, $tiempoVecimiento, $fechaCreacion, $tipo ){
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO 
			{$wcenmez}_000002(   Medico , Fecha_data, Hora_data,   Artcod ,        Artcom     ,      Artgen      ,   Artuni , Artvia,      Arttin      ,     Arttve         ,     Artfec      ,  Arttip, Artcon, Artfot, Artnev, Artest, Artdes, Artfac, Artpes, Artpur, Arthis, Artins, Artgra, Artord, Artapp, Artnap, Artmin, Artmax,   Seguridad  ) 
				       VALUES( '$wcenmez', '$fecha'  , '$hora'  , '$codigo', '$nombreComercial', '$nombreGenerico', '$unidad', '$via', '$tiempoInfusion', '$tiempoVecimiento', '$fechaCreacion', '$tipo',    '' ,   ''  ,    '' , 'on' ,   ''  ,   ''  ,   ''  ,   '0' ,    '' ,   ''  ,   ''  ,   ''  ,   ''  ,   ''  ,   ''  ,   ''  , 'C-$wcenmez' )";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/**
 * 
 * @return unknown_type
 */
function registrarTipoProducto( $conex, $wcenmez, $codigo, $descripcion, $codificado, $distintivo, $esNombreCompuesto,$esProduto, $unidad, $esMaterialPreparacion, $esInsumoNutiricon, $esVehiculoDilcuion, $esMaterialQuirugio, $afectaPeso, $esInsumoNoAprovechable, $esNka, $tipoProtocolo ){
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO 
			{$wcenmez}_000001(   Medico  , Fecha_data, Hora_data,   Tipcod ,      Tipdes   ,    Tipcdo    , Tipcon,     Tipdis   ,       Tipnco        ,  Tippro     ,  Tipuni  ,          Tipmmq         ,         Tipinu      ,       Tipvdi         ,        Tipmat        ,    Tipppe    , Tipest,         Tipina          ,  Tipnka ,     Tiptpr      , Tipimp,  Seguridad   ) 
				       VALUES( '$wcenmez', '$fecha'  , '$hora'  , '$codigo', '$descripcion', '$codificado',   '0' , '$distintivo', '$esNombreCompuesto', '$esProduto', '$unidad', '$esMaterialPreparacion', '$esInsumoNutiricon', '$esVehiculoDilcuion', '$esMaterialQuirugio', '$afectaPeso',  'on' ,'$esInsumoNoAprovechable', '$esNka', '$tipoProtocolo',   ''  , 'C-$wcenmez' )";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Busca si un articulo de SF tiene su insumo correspondiente en CM, si no lo tiene lo crea
 * @param $conex
 * @param $wcenmez
 * @param $wbasedato
 * @param $articulo
 * @return unknown_type
 */
function crearInsumo( $conex, $wcenmez, $wbasedato, $articulo, $nombreComercial, $nombreGenerico, $unidad, $conversion ){
	global $wemp_pmla;
	$val = true;
	
	//Consulto si presentacion del aritculo
	$sql = "SELECT
				*
			FROM
				{$wcenmez}_000009 a
			WHERE
				apppre = '$articulo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Errro en el query $sql -".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	//Si no existe la presentacion creo la presentacion
	if( $numrows == 0 ){
		
		//Consulto insumo a crear
		$sql = "SELECT
					*
				FROM
					{$wcenmez}_000001 a
				WHERE
					tipcdo = 'on'
					AND tippro != 'on'
				";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$rows = mysql_fetch_array( $res );
		
		$insumo = crearNombreInsumo( $conex, $wcenmez, $rows );
		
		$existencias = 0;
		$tiempoVencimiento = 24*60;
		$costo = 0;

		$ccoCM=ccoUnificadoCM(); //Se obtiene el Codigo de Central de Mezclas

		registrarPresentacion( $conex, $wcenmez, $insumo, $articulo, $ccoCM, $conversion, $existencias, $tiempoVencimiento, $unidad, $costo );
		
		$via = '';
		$fechaCreacion = date( "Y-m-d" );
		$tiempoInfusion = 0;
		
		registrarArticuloNuevo( $conex, $wcenmez, $wbasedato, $insumo, $nombreComercial, $nombreGenerico, $unidad, $via, $tiempoInfusion, $tiempoVencimiento, $fechaCreacion, $rows['Tipcod'] );
	}
	
	return $val;
}

function crearProducto( $conex, $wcenmez, $wbasedato, $articulo, $nombreComercial, $nombreGenerico, $dosis, $unidad, $conversion, $tipo ){
	
	$val = false;
	
	crearInsumo( $conex, $wcenmez, $wbasedato, $articulo, $nombreComercial, $nombreGenerico, $unidad, $conversion );
	
	//verifico que no exista el producto con los mismos insumos
	$sql = "SELECT
				*
			FROM
				{$wcenmez}_000002
			WHERE
				artcod IN ( 
					SELECT
						pdepro
					FROM
						{$wcenmez}_000003 a, 
						{$wcenmez}_000009 b
					WHERE
						pdecan = '$dosis'
						AND pdeins = appcod
						AND apppre = '$articulo'
					)
				AND 1 IN (SELECT 
							count(*) 
					   FROM 
							{$wcenmez}_000003 
					   WHERE 
					   		pdepro = artcod
					   	)
			"; //echo "......$sql";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el equery $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows == 0 ){
		
		//Verifico si existe un producto para el tipo de articulo
		$sql = "SELECT
					*
				FROM
					{$wcenmez}_000001 a
				WHERE
					tipdis = '$tipo'
					AND tippro = 'on'
					AND tipcdo = 'on'
				"; //echo "......$sql";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows == 0 ){	//Si no existe lo creo
			
			//Hallando el codigo para el nuevo producto
			$sql = "SELECT max( tipcod ) as codigo FROM {$wcenmez}_000001";
			
			$resCod = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rowsCod = mysql_fetch_array( $resCod );
			
			$codigo = $rowsCod[ 'codigo' ]+1;
			////////////////////////////////////////////////////////////////////////////////////////////////////
			
			//Hallando el nombre del nuevo producto
			$sql = "SELECT 
						Tardes 
					FROM 
						{$wbasedato}_000099
					WHERE
						Tarcod = '$tipo'
					";
			
			$resDes = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rowsDes = mysql_fetch_array( $resDes );
			
			$descripcion = $rowsDes[ 'Tardes' ];
			////////////////////////////////////////////////////////////////////////////////////////////////////
			
//			registrarTipoProducto( $conex, $wcenmez, $codigo, $descripcion, 'on', $tipo, 'off', 'on', $unidad, 'off', 'off', 'off', 'off', 'off', 'off', '', '' );
			registrarTipoProducto( $conex, $wcenmez, $codigo, $descripcion, 'on', $tipo, 'off', 'on', 'BO', 'off', 'off', 'off', 'off', 'off', 'off', '', '' );
			
			$sql = "SELECT
						*
					FROM
						{$wcenmez}_000001 a
					WHERE
						tipcod = '$codigo'
					";
						
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$rows = mysql_fetch_array( $res );
		}
		else{
			$rows = mysql_fetch_array( $res );
		}
		
		
		
		
		
		
		
		//Consulto insumo a crear
//		$sql = "SELECT
//					*
//				FROM
//					{$wcenmez}_000001 a
//				WHERE
//					tipcod = '18'
//				";
//					
//		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
//		$rows = mysql_fetch_array( $res );

		$producto = crearNombreInsumo( $conex, $wcenmez, $rows );

		$fechaCreacion = date( "Y-m-d" );

		$via = '';
		$tiempoInfusion = 24*60;
		$tiempoVencimiento = 365;

		registrarArticuloNuevo( $conex, $wcenmez, $wbasedato, $producto, $nombreComercial, $nombreGenerico, 'BO', $via, $tiempoInfusion, $tiempoVencimiento, $fechaCreacion, $rows['Tipcod'] );
		
		
		$sql = "SELECT
					*
				FROM
					{$wcenmez}_000009 b
				WHERE
					apppre = '$articulo'
				";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$rows = mysql_fetch_array( $res );
		
		$aplicaPurga = 'off';
		$factor = '';
		registrarInsumo( $conex, $wcenmez, $producto, $rows['Appcod'], $dosis, $factor, $aplicaPurga );
	}

	return $val;
}

/*********************************************************************************************************************************************************
 * 												FIN DE FUNCIONES PARA CREAR INSUMO Y PRODUCTOS AUTOMATICAMENTE
 *********************************************************************************************************************************************************/

/**
 * Cuenta cuantas filas en total se debe mostrar por articulo y tipo
 * @return unknown_type
 */
function contarFilasPorArticulo( $detalleCco, $tipoArticulo, $articulo, $filtroCco ){
	
	$val = 0;
	
	if( $filtroCco != "%" ){
//		echo "<br>.........$tipoArticulo $articulo";
		foreach( $detalleCco[ $tipoArticulo ][ $articulo ] as $keyCanFraccion  => $valueCanFraccion  ){
			
			if( $keyCanFraccion != 'totalFilas' ){
				
				foreach( $valueCanFraccion as $keyUnifraccion => $valueUniFraccion  ){
					
					if( isset( $valueUniFraccion[ $filtroCco ] ) ){
						$val += count( $detalleCco[ $tipoArticulo ][ $articulo ][ $keyCanFraccion ][ $keyUnifraccion ] );
					}
				}
			}
		}
		
		return $val;
	}
	
	
	return $detalleCco[ $tipoArticulo ][ $articulo ][ 'totalFilas' ];
}

/**
 * Imprime el resporte de insumos por articulos para los articulos qeu aplican
 * 
 * @param $insumos
 * @return unknown_type
 */
function reporteInsumos( $insumos ){
	
	if( count($insumos) > 0 ){
		
		echo "<table align='center'>";
		
		echo "<tr class='encabezadotabla' align='center'>";
		
		echo "<td>Codigo insumo</td>";
		echo "<td>Nombre comercial</td>";
		echo "<td>Cantidad total<br>por insumo</td>";
		echo "<td>Unidad<br>insumo</td>";
		echo "<td>Producto</td>";
		echo "<td>Cantidad<br>Insumo por<br>articulo</td>";
		
		echo "</tr>";
		
		$i = 0;
		
		foreach( $insumos as $keyInsumo => $valueInsumo ){
			
			$i++;
			
			$style = '';
			
			foreach( $valueInsumo as $key => $value ){
				
				if( $key != 'cantidadInsumo' ){
				
					$class = "class='fila".(($i%2)+1)."'";
						
					echo "<tr $class>";
						
					echo "<td style='$style' rowspan='".( count($valueInsumo)-1 )."'>";
					echo $value[ 'codigoInsumo' ];
					echo "</td>";
					
					echo "<td style='$style' rowspan='".( count($valueInsumo)-1 )."'>";
					echo $value[ 'nombreInsumo' ];
					echo "</td>";
					
					echo "<td align='right' style='$style' rowspan='".( count($valueInsumo)-1 )."'>{$valueInsumo['cantidadInsumo']}</td>";
					
					echo "<td align='center' style='$style' rowspan='".( count($valueInsumo)-1 )."'>";
					echo $value[ 'unidadInsumo' ];
					echo "</td>";
					
					echo "<td>";
					echo $value[ 'codigoArticulo' ];
					echo "</td>";
					
					echo "<td align='right'>";
					echo $value[ 'cantidadInsumo' ];
					echo "</td>";
						
					$style = 'display:none';
				}
				
			}
			
			echo "</tr>";
		}

		echo "</table>";
	}
}

/**
 * Crea el insumo correspondiente para un articulo para mostrar el reporte para articulos genericos
 * 
 * @param $conex
 * @param $wbasedato
 * @param $wcenmez
 * @param $tipoArticulo
 * @param $codigoArticulo
 * @param $aplicaciones
 * @param $insumos
 * @return unknown_type
 */
function creandoInsumos( $conex, $wbasedato, $wcenmez, $tipoArticulo, $codigoArticulo, $aplicaciones, &$insumos ){
	
	$sql = "SELECT
				*
			FROM
				{$wcenmez}_000003 a, 
				{$wcenmez}_000002 b
			WHERE
				pdepro = '$codigoArticulo'
				AND artcod = pdeins
				AND pdeest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows($res);
	
	if( $numrows > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i ++ ){
			
			@$insumos[ $tipoArticulo ][ $rows['Pdeins'] ][ $codigoArticulo ][ 'codigoArticulo' ] = $codigoArticulo;
			@$insumos[ $tipoArticulo ][ $rows['Pdeins'] ][ $codigoArticulo ][ 'codigoInsumo' ] = $rows['Pdeins'];
			@$insumos[ $tipoArticulo ][ $rows['Pdeins'] ][ $codigoArticulo ][ 'nombreInsumo' ] = $rows['Artcom'];
			@$insumos[ $tipoArticulo ][ $rows['Pdeins'] ][ $codigoArticulo ][ 'unidadInsumo' ] = $rows['Artuni'];
			@$insumos[ $tipoArticulo ][ $rows['Pdeins'] ][ $codigoArticulo ][ 'cantidadInsumo' ] += $rows['Pdecan']*$aplicaciones;
			@$insumos[ $tipoArticulo ][ $rows['Pdeins'] ][ 'cantidadInsumo' ] += $rows['Pdecan']*$aplicaciones;
		}		
	}	
}

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
function activarDesactivarEncabezado( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $estado, $modaliad ){
	
	$val = false;
	
	$sql = "UPDATE
				{$wbasedato}_000106
			SET
				ecpest = '$estado',
				ecpmod = '$modaliad'
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

	global $slFiltroCco;
	
	$val = false;
	
	$i = 0;
	
	if( !isset($detalleArticulo[$tipo]) || count( $detalleArticulo[$tipo][$articulo] ) == 0 ){
		echo "<center><b>No se encontraron registros</b></center>";
		return $val;
	}
	
	echo "<table align='center' id='{$tipo}_{$articulo}'>";
	
	echo "<tr class='encabezadotabla' align='center'>";
	echo "<td style='width:100'>Historia</td>";
	echo "<td>Nombre</td>";
	echo "<td>Fecha y hora<br>de inicio</td>";
	echo "<td>Frecuencia<br>(en horas)</td>";
	echo "<td>Total aplicaciones por<br>ronda a producir</td>";
	echo "<td>Dosis</td>";
	echo "<td>Cantidad<br>Total</td>";
	echo "<td>Observaciones</td>";
	echo "</tr>";
	
	foreach( $detalleArticulo[$tipo][$articulo] as $keyDetalle => $valueDetalle ){

		$fila = "class='fila".(($i%2)+1)."'";
		$style = '';
		
		foreach( $valueDetalle as $key => $value ){
			
			if( $slFiltroCco == "%" || $value[ 'Cco' ] == $slFiltroCco ){
			
				echo "<tr $fila>";
				
					echo "<td align='center' rowspan='".count($valueDetalle)."' style='$style'>$keyDetalle</td>";
					
					echo "<td rowspan='".count($valueDetalle)."' style='$style'>";
					echo $value['nombre'];
					echo "</td>";
					
					echo "<td align='center'>";
					echo $value['fechaHoraInicio'];
					echo "</td>";
					
					echo "<td align='center'>";
					echo $value['frecuencia'];
					echo "</td>";
					
					echo "<td style='width:80' align='center'>";
					echo $value['totalAplicaciones'];
					echo "</td>";
					
					echo "<td style='width:80' align='center'>";
					echo $value['dosis']." ".$value['unidadDosis'];
					echo "</td>";
					
					echo "<td align='center'>";
					echo $value['unidadesTotales'];
					echo "</td>";
					
					echo "<td align='center'>";
					echo $value['Observaciones'];
					echo "</td>";
					
					$style = "display:none";
					
				echo "</tr>";
			}
			else{
				$i--;
			}
		}
		
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
function actualizarRegistro( $conex, $wbasedato, $candosis, $unidadFraccion, $cco, $dosisTotales, $canSinAprovechamiento, $cantidad, $id ){
	
	$val = false;
	
	$sql = "UPDATE
				{$wbasedato}_000107 
			SET
				Cpxdos = '$candosis',
				Cpxuni = '$unidadFraccion',
				Cpxcco = '$cco',
				Cpxdto = '$dosisTotales',
				Cpxcsa = '$canSinAprovechamiento',
				Cpxcan = '$cantidad',
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
function movimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $candosis, $unidadFraccion, $cco, $dosisTotales, $canSinAprovechamiento, $cantidad ){
	
	$val = false;
	
	global $wcenmez;
	$wcenmez = "cenpro";
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000107
			WHERE
				Cpxfec = '$fecha' 
				AND Cpxron = '$ronda'
				AND Cpxtar = '$tipoArticulo'
				AND Cpxart = '$articulo'
				AND Cpxdos = '$candosis'
				AND Cpxuni = '$unidadFraccion'
				AND Cpxcco = '$cco'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		if( $rows[ 'Cpxdos' ] == $candosis && $rows[ 'Cpxuni' ] == $unidadFraccion && $rows[ 'Cpxcan' ] == $cantidad ){
				
			$estado = 'on';
			if( $rows[ 'Cpxest' ] == 'on' ){
				
				$estado = 'off';
			}
			
			$val = activarDesactivarRegistro( $conex, $wbasedato, $rows[ 'id' ], $estado );
			
			if( $estado == 'off' ){
				activarDesactivarEncabezado( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $estado, '' );
			}	
		}
		else{
			$val = actualizarRegistro( $conex, $wbasedato, $candosis, $unidadFraccion, $cco, $dosisTotales, $canSinAprovechamiento, $cantidad, $rows[ 'id' ] );
		}
	}
	else{
		
		$comercial = $generico = 'PRUEBA 1';
//		crearProducto( $conex, $wcenmez, $wbasedato, $articulo, $comercial, $generico, $candosis, $unidadFraccion, 0, $tipoArticulo );
		
		if( registrarMovimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $candosis, $unidadFraccion, $cco, $dosisTotales, $canSinAprovechamiento, $cantidad ) ){
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
				AND ( Ecpmod = 'P'
				OR ( 
					ecpmod = 'N'
					AND  UNIX_TIMESTAMP( NOW() )  > UNIX_TIMESTAMP(CONCAT( ecpfec,' ',ecpron ) ) 
				) )
			"; 
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
//		if( $rows[ 'Ecpmod' ] == 'P' || ( $rows[ 'Ecpmod' ] == 'N' && time() > strtotime( $rows[ 'Ecpfec' ]." ".$rows[ 'Ecpron' ] ) ) ){
			$val = true;
//		}
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
function tieneProduccion( $conex, $wbasedato, $wcenmez, $fecha, $ronda, $articulo, $tipoArticulo, &$datosTipo, $dosis, $unidadDosis ){
	
	global $slFiltroCco;
	global $detalleCco;
	
	$val = false;
//Cpxdos, Cpxuni, Cpxcsa, Cpxcan, Cpxdto
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000107 a
			WHERE
				cpxfec = '$fecha'
				AND cpxron = '$ronda'
				AND cpxtar = '$tipoArticulo'
				AND cpxest = 'on'
				AND cpxcco LIKE '$slFiltroCco' 				
			"; //echo ".......<pre>$sql</pre>";
				
//				AND cpxart = '$articulo'
//				AND cpxdos = '$dosis'
//				AND cpxuni = '$unidadDosis'
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	for( ;$rows = mysql_fetch_array( $res ); ){
		
		@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['codArticulo'] = $rows['Cpxart'];
		
		if( !isset($datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['nomArticulo']) ){
			@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['nomArticulo'] = nombreArticulo( $conex, $wbasedato, $wcenmez, $rows['Cpxart'] );
		}
		
		@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['canArticulo'] += ceil( $rows['Cpxcsa'] );
		@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['canDosis'] += $rows['Cpxcan']*$rows['Cpxdos'];
		@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['uniDosis'] = $rows['Cpxuni'];
		@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['fraUnidad'] = $rows['Cpxdto']/$rows['Cpxcsa'];
		@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['producido'] = ( $rows['Cpxest'] == 'on' )? true: false;
		
		@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['dosisTotales'][ "".$rows['Cpxdos']."" ]['total'] += $rows['Cpxcan'];
		@$datosTipo[ $rows['Cpxtar'] ][ $rows['Cpxart'] ]['dosisTotales'][ "".$rows['Cpxdos']."" ]['producido'] = ( $rows['Cpxest'] == 'on' )? true: false;
		
		//Calculo cuanto se ha grabado del articulo por cco
		if( !isset($detalleCco[ $rows['Cpxtar'] ][ $rows['Cpxart'] ][ $rows['Cpxdos'] ][ $rows['Cpxuni'] ][ $rows[ 'Cpxcco' ] ]) ){
			@$detalleCco[ $rows['Cpxtar'] ][ $rows['Cpxart'] ][ 'totalFilas' ]++;
		}
		@$detalleCco[ $rows['Cpxtar'] ][ $rows['Cpxart'] ][ $rows['Cpxdos'] ][ $rows['Cpxuni'] ][ $rows[ 'Cpxcco' ] ]['grabado'] = $rows['Cpxcan'];
		@$detalleCco[ $rows['Cpxtar'] ][ $rows['Cpxart'] ][ $rows['Cpxdos'] ][ $rows['Cpxuni'] ][ $rows[ 'Cpxcco' ] ]['marcaGrabado'] = true;
		
		@$detalleCco[ $rows['Cpxtar'] ][ $rows['Cpxart'] ][ $rows['Cpxdos'] ][ $rows['Cpxuni'] ][ $rows[ 'Cpxcco' ] ]['unidadesTotales'] = 0;
		@$detalleCco[ $rows['Cpxtar'] ][ $rows['Cpxart'] ][ $rows['Cpxdos'] ][ $rows['Cpxuni'] ][ $rows[ 'Cpxcco' ] ]['unidadDosis'] = $rows['Cpxuni'];
		@$detalleCco[ $rows['Cpxtar'] ][ $rows['Cpxart'] ][ $rows['Cpxdos'] ][ $rows['Cpxuni'] ][ $rows[ 'Cpxcco' ] ]['dosis'] = $rows['Cpxdos'];
		
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

	$ini1970 = strtotime( "1970-01-01 00:00:00" );
	
	$fechorActual = time();
	$fechorRonda = strtotime( "$fechaProximaRonda $proximaRonda" );
	$produccion = strtotime( "1970-01-01 $horaCortePx" )-$ini1970;
	$dispensacion = strtotime( "1970-01-01 $horaCorteDispensacion" ) - $ini1970;
	
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

	if( !empty($dosisMaximas) && $porDosisDias ){
		if( $inicioArticulo <= $fechorDosisMaximas ){
			$porDosisDias = true;
		}
		else{
			$porDosisDias = false;
		}
	}
	
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
function registrarEncabezadoProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $modalidad ){
	
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
			activarDesactivarEncabezado( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, 'on', $modalidad );
		}
	}
	else{
	
		$sql = "INSERT INTO {$wbasedato}_000106
						(   Medico    ,   fecha_data  , hora_data,  Ecpfec ,  Ecpron ,     Ecptar     ,  Ecpusu , Ecpest,   Ecpmod    ,   seguridad    )
				  VALUES( '$wbasedato', '$fechaActual',  '$hora' , '$fecha', '$ronda', '$tipoArticulo', '$wuser',  'on' , '$modalidad', 'C-$wbasedato' )";
		
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
 * Registra el movimiento para un articulo del ciclo de produccción
 * 
 * @param $conex
 * @param $wbasedato
 * @param $campos
 * @return unknown_type
 *********************************************************************************************************************************************/
function registrarMovimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $candosis, $unidadFraccion, $cco, $dosisTotales, $cantidadSinAprovechamiento, $cantidad ){
	
	global $wuser;
	
	$fechaActual = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "INSERT INTO {$wbasedato}_000107
					(   Medico    ,   fecha_data  , hora_data,  Cpxfec , Cpxron  ,     Cpxtar     ,   Cpxart   ,    Cpxdos  ,       Cpxuni     ,   Cpxcan	, Cpxcco,     Cpxdto     ,            Cpxcsa            , Cpxusu  , Cpxest,  seguridad    )
			  VALUES( '$wbasedato', '$fechaActual',  '$hora' , '$fecha', '$ronda', '$tipoArticulo', '$articulo', '$candosis', '$unidadFraccion', '$cantidad', '$cco', '$dosisTotales', '$cantidadSinAprovechamiento', '$wuser',  'on' , 'C-$wbasedato' )";
	
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

/**
 * Indica si un artciulo peretenece a una ronda o no
 * 
 * @param $fechaActual
 * @param $horaActual
 * @param $tiempoPreparacion
 * @param $fechaIncio
 * @param $horaInicio
 * @param $frecuencia
 * @param $horaSiguiente
 * @param $despuesHoraCortePx
 * @param $dosisMaximas
 * @param $diasTto
 * @return unknown_type
 */
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

		if( !empty($dosisMaximas) && $porDosisDias ){
			if( $fechorInicio <= $fechorDosisMaximas ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}
		
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
//				echo "<br>.....Hello en English...".date( "Y-m-d H:i:s", $fechorActual );
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
 *
 * Modificacon: 
 * Agosto 8 de 2011.	(Edwin MG).	Si un articulo es de central de mezclas pero no tiene tipo, se hace aparte y se coloca como asignacion
 *			    					de tipo
 ************************************************************************************************************************/
 function esArticuloGenerico( $conexion, $wbasedatoMH, $wbasedatoCM, $codArticulo ){
	
	//Consulto de que tipo es el articulo
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
	
		/********************************************************************************
		 * Verifico que sea de la central y tenga tipo
		 * Si es de la central y no tiene tipo se agrega en un campo nuevo
		 ********************************************************************************/
		
		$sql = "SELECT
					*
				FROM
					{$wbasedatoCM}_000002,
					{$wbasedatoCM}_000001
				WHERE
					artcod = '$codArticulo'
					AND arttip = tipcod
					AND artest = 'on'
					AND tipest = 'on' 
				";
		
		$res = mysql_query( $sql, $conexion ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){	//si es de central
			
			$rows = mysql_fetch_array( $res);
			
			
			if( empty( $rows['Tiptpr'] ) || trim( $rows['Tiptpr'] ) == '' || strtoupper( trim( $rows['Tiptpr'] ) ) == 'NO' ){ //Si no tiene tipo de articulo, se asigna a uno
				return "SADT";	//Sin Asignacion de Tipo
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
}
 
/* 
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
}*/


/********************************************************************************************************************************
 * Pinta la tabla con todos los articulo para un tipo de articulo
 * 
 * @param $tipoArticulo
 * @return unknown_type
 ********************************************************************************************************************************/
function mostrarArticulosRonda( $tipoArticulo, $codTipo, $detalleArticulos, $detalleNuevo, $fechaRonda, $rondaActual ){
	
	global $insumos;
	global $slFiltroCco;
	global $detalleCco;
	
	global $wbasedato;
	global $conex;
	
	$styleProducido = "";
	if( $slFiltroCco != "%" ){
		$styleProducido = "";
	}
	
	if( count( $tipoArticulo ) ){
		
		$i = 0;
		
		foreach( $tipoArticulo as $keyTipo => $valueTipo ){
			
			$hayDiferencias = false;
			
			if( $i == 0 ){
				
				echo "<table align='center' id='tbDetalle{$codTipo}' style='display:;'>";

				echo "<tr class='encabezadotabla' align='center'>";
					
				echo "<td rowspan='2'>Codigo</td>";
				echo "<td rowspan='2'>Nombre</td>";
				echo "<td rowspan='2'>Cantidad dosis</td>";
				echo "<td rowspan='2'>Cantidad de unidades<br>con aprovechamiento</td>";
				echo "<td rowspan='2'>Gasto total</td>";
				echo "<td rowspan='2'>Cantidad de unidades<br>sin aprovechamiento</td>";
				echo "<td colspan='3'>Dosis a empacar</td>";
				echo "<td rowspan='2' style='$styleProducido'>Producido<br><INPUT type='checkbox' onClick='marcarTodo(this)' style='display:'></td>";
				echo "<td rowspan='2' style='display:none'>Mostrar/Ocultar<br>Detalle</td>";
				echo "<td rowspan='2'>Modificado</td>";
				echo "<td colspan='5' style='display:none'>Cco</td>";
				
				echo "<tr class='encabezadotabla' align='center'>";
				echo "<td>Fraccion</td>";
				echo "<td>Unidad</td>";
				echo "<td>Cantidad</td>";

				echo "<td style='display:none'>Cco</td>";
				echo "<td style='display:none'>Can</td>";
				echo "<td style='display:none'>Uni</td>";
				echo "<td style='display:none'>Tot</td>";
				echo "<td style='display:none'>gra</td>";
				
				echo "</tr>";
					
				echo "</tr>";
			}
			
			$class = "class='fila".(($i % 2)+1)."'";
			
			
			
			$rowspan = contarFilasPorArticulo( $detalleCco, $codTipo, $valueTipo['codArticulo' ], $slFiltroCco );
			
			//if rowspan es 0, significa que no hay datos a mostrar para dicho articulo, por tal motivo se ocultan todas la filas posibles
			//para dicho articulo
			$styleMostrarFila = '';
			if( $rowspan == 0 ){
				$styleMostrarFila = 'display:none';
				if( $i >0 )
					$i--;
			}
			
			$rowspan = $detalleCco[ $codTipo ][ $valueTipo['codArticulo' ] ]['totalFilas'];
			
			echo "<tr $class onClick='mostrarOcultarDetalle( this );' style='cursor:pointer;$styleMostrarFila'>";

			echo "<td rowspan='$rowspan'>";
			echo $valueTipo['codArticulo'];
			echo "</td>";
			echo "<td rowspan='$rowspan'>";
			echo $valueTipo['nomArticulo'];
			echo "</td>";
			echo "<td align='center' rowspan='$rowspan'>";
			echo $valueTipo['canDosis']." ".$valueTipo['uniDosis'];
			echo "</td>";
			echo "<td align='center' class='fondoamarillo' rowspan='$rowspan'>";
			echo @ceil( $valueTipo['canDosis']/$valueTipo['fraUnidad'] );
			echo "</td>";
			echo "<td align='center' rowspan='$rowspan'>";
			echo @(ceil( $valueTipo['canDosis']/$valueTipo['fraUnidad'] )*$valueTipo['fraUnidad'])." ".$valueTipo['uniDosis'];
			echo "</td>";
			echo "<td align='center' rowspan='$rowspan'>";
			echo $valueTipo['canArticulo'];
			echo "</td>";
			
			if( count( @$valueTipo['dosisTotales'] ) > 0 ){
				
				$j = 0;
				
				foreach( $valueTipo['dosisTotales'] as $key => $value ){
					
					$rowspanCco = count( $detalleCco[ $codTipo ][ $valueTipo['codArticulo' ] ][ $key ][ $valueTipo['uniDosis'] ] );
					$styleCco = '';
					$styleFiltro = '';
					
					foreach( $detalleCco[ $codTipo ][ $valueTipo['codArticulo' ] ][ $key ][ $valueTipo['uniDosis'] ] as $keyCco => $valueCco ){
						
						//si para una cantidad y unidad de fraccion, no hay cco a mostrar, se oculta las fila correspondiente
						if( $slFiltroCco != "%" ){
							if( !isset( $detalleCco[ $codTipo ][ $valueTipo['codArticulo' ] ][ $key ][ $valueTipo['uniDosis'] ][ $slFiltroCco ] ) ){
								$styleCco = 'display:none';	//Oculta las columnas correspondientes a las dosis a empacar
								$styleFiltro = 'display:none';
							}
						}
						
						if( $j > 0 ){
							echo "<tr $class onClick='mostrarOcultarDetalle( this );' style='cursor:pointer;$styleMostrarFila'>";
						}
					
						echo "<td rowspan='$rowspanCco' align='center' style='$styleCco'>$key</td>";
						echo "<td rowspan='$rowspanCco' align='center' style='$styleCco'>{$valueTipo['uniDosis']}</td>";
						echo "<td rowspan='$rowspanCco' align='center' style='$styleCco'>{$value['total']}</td>";
						
						if( $styleCco == '' ){	
							if( $value['modificado'] == 0 ){
								if( !$value['marcar'] ){									
									/************************************************************************************************
									 * Agosto 18 de 2011
									 * Si no aparece marcado se desconfirma el encabezado del producto
									 ************************************************************************************************/
									 // activarDesactivarEncabezado( $conex, $wbasedato, $fechaRonda, $rondaActual, $codTipo, 'off', '' );
									/************************************************************************************************/
									echo "<td rowspan='$rowspanCco' style='$styleProducido;' align='center' onClick='stopEvent(event);'><INPUT type='checkbox' name='' id='' onClick='grabarMovimientoArticulo( this ); stopEvent(event);'></td>";
								}
								else{
									echo "<td rowspan='$rowspanCco' style='$styleProducido;' align='center' onClick='stopEvent(event);'><INPUT type='checkbox' name='' id='' onClick='grabarMovimientoArticulo( this ); stopEvent(event);' checked></td>";
								}
							}
							else{
								echo "<td rowspan='$rowspanCco' style='$styleProducido;' align='center' onClick='stopEvent(event);'><INPUT type='checkbox' name='' id='' onClick='grabarMovimientoArticulo( this ); stopEvent(event);' checked></td>";
							}
						}
						else{
							echo "<td style='$styleCco'></td>";
						}
						
						if( $value['modificado'] == 0 ){
							echo "<td rowspan='$rowspanCco' align='center' style='$styleCco'>0</td>";
						}
						else{
							echo "<td rowspan='$rowspanCco' align='center' class='fondorojo' style='$styleCco'>{$value['modificado']}</td>";
						}
						
						//Imprimo el detalle de los cco por articulo
						echo "<td style='display:none'>$keyCco</td>";
						echo "<td style='display:none'>{$valueCco['dosis']}</td>";
						echo "<td style='display:none'>{$valueCco['unidadDosis']}</td>";
						echo "<td style='display:none'>{$valueCco['unidadesTotales']}</td>";
						
						if( $valueCco['marcaGrabado'] && $valueCco['grabado'] - $valueCco['unidadesTotales'] != 0 ){
							echo "<td class='fondorojo' style='display:none'>{$valueCco['grabado']}</td>";
						}
						else{
							echo "<td style='display:none'>{$valueCco['grabado']}</td>";
						}
						
						if( $j > 0 ){
							echo "</tr>";
						}
						
						$styleCco = 'display:none';
						
						$j++;
					}
				}
			}
			
			echo "<tr style='display:none'>";
			echo "<td colspan='11'><br>";
			mostrarDetalleArticulo( $codTipo, $valueTipo['codArticulo'], $detalleArticulos );
			echo "<br></td>";
			echo "</tr>";
			
			$i++;
		}
		
		if( isset($insumos[$codTipo]) ){
			echo "<tr>";
			echo "<td colspan='11'><br>";
			reporteInsumos( $insumos[$codTipo] );
			echo "</td>";
			echo "</tr>";
		}
		
		echo "<tr style='display:none'>";
		echo "<td align='center' colspan='11'><br><INPUT type='button' value='Retornar' style='width:100' onClick='retornar( this )'></td>";
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
function consultarUltimaRonda( $conex, $wbasedato, $tipo, $lapso ){
	
	$val = date("Y-m-d 00:00:00");
	
	$fecha = date( "Y-m-d", strtotime( date("Y-m-d H:i:s") )-24*3600 );
	
//	$lapso = 2;	//unidad en horas
	
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
				AND ( ecpmod = 'P'
				OR ( 
					ecpmod = 'N'
					AND UNIX_TIMESTAMP( NOW() ) > UNIX_TIMESTAMP(CONCAT( ecpfec,' ',ecpron ) ) 
				) )	
			ORDER BY
				ecpfec desc, ecpron desc 
			"; //echo "<BR>.......A: ".$sql;
				
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000106
			WHERE
				ecptar = '$tipo'
				AND ecpfec >= '$fecha'
				AND ecpest = 'on'
				AND ( ecpmod = 'P'
				OR ( 
					ecpmod = 'N'
					AND UNIX_TIMESTAMP( NOW() ) > UNIX_TIMESTAMP(CONCAT( ecpfec,' ',ecpron ) ) 
				) )	
			ORDER BY
				Ecpfec asc, ecpron asc
			"; //echo "<BR>.......A: <pre>".$sql."</pre>";
			
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000106 a, {$wbasedato}_000099 b
			WHERE
				ecptar = '$tipo'
				AND ecpfec >= '$fecha'
				AND ecpest = 'on'
				AND ( ecpmod = 'P'
				OR ( 
					ecpmod = 'N'
					AND UNIX_TIMESTAMP( NOW() ) > UNIX_TIMESTAMP(CONCAT( ecpfec,' ',ecpron ) ) 
				) )	
				AND tarcod = ecptar
			ORDER BY
				Ecpfec asc, ecpron asc
			"; //echo "<BR>.......A: <pre>".$sql."</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
		$compHoras = strtotime( $rows['Ecpfec']." ".$rows['Ecpron'] );
		//echo "<br>.......".date( "Y-m-d H:i:s", $compHoras );
		if( $i > 0 ){
			if( time()+$rows['Tarpre']*3600 >= $compHoras ){
				if( $compHoras == strtotime( $val )+$lapso*3600 ){
					$val = date( "Y-m-d H:i:s", $compHoras );
				}
				else{
					break;
				}
			}
			else{
				break;
			}
		}
		else{
			$val = date( "Y-m-d H:i:s", $compHoras );
		}
		
		//echo "<br>......222:".$val;
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

/********************************************************************************************************
 * Consulta cuanto es la cantidad modificada para cada articulo a mostrar
 * 
 * @param $detalleCco
 * @return unknown_type
 ********************************************************************************************************/
function consultarCantidadModificada( $detalleCco ){
	
	global $datosTipo;
	global $tiposAriculos;
	global $slFiltroCco;
	
	global $wbasedato;
	global $conex;
	
	$val = 0;
	
	foreach( $detalleCco as $keyTipoArticulo => $valueTipoArticulo ){
		
		foreach( $valueTipoArticulo as $keyArticulo => $valueArticulo ){
			
			foreach( $valueArticulo as $keyCanFraccion => $valueCanFraccion ){
				
				if( $keyCanFraccion != 'totalFilas' ){
				
					foreach( $valueCanFraccion as $keyUniFraccion => $valueUniFraccion ){
						
						$val = 0;
						
						$marcar = true;	//indica si todo esta grabado
							
						foreach( $valueUniFraccion as $keyCco => $valueCco ){

							if( $valueCco['marcaGrabado'] ){
								$val += $valueCco['unidadesTotales']-$valueCco['grabado'];
							}
							elseif( $slFiltroCco == "%" || $slFiltroCco == $keyCco ){
//								$val += $valueCco['unidadesTotales'];
								$marcar = false;
							}

							$datosTipo[ $keyTipoArticulo ][ $keyArticulo ]['dosisTotales'][ "".$keyCanFraccion."" ]['modificado'] = $val;
						}
						
						$datosTipo[ $keyTipoArticulo ][ $keyArticulo ]['dosisTotales'][ "".$keyCanFraccion."" ]['marcar'] = $marcar;	//Indica el articulo para cada centro de costos fue grabado con anterioridad
						
						/************************************************************************************************
						 * Agosto 18 de 2011
						 * Si no aparece marcado se desconfirma el encabezado del producto
						 ************************************************************************************************/
						$rondaActual = date( "H:i:s", $tiposAriculos[$keyTipoArticulo]['proximaRonda'] );
						$rondaActualFecha = date( "Y-m-d", $tiposAriculos[$keyTipoArticulo]['proximaRonda'] );
						if( !$marcar ){
							activarDesactivarEncabezado( $conex, $wbasedato, $rondaActualFecha, $rondaActual, $keyTipoArticulo, 'off', '' );
						}
						/************************************************************************************************/
					}
				}
			}
		}
	}
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
			$cco = $ccos;
			//grabo uno por uno los cco para la dosis
			/**
			 * Los ccos vienen de la forma [Cco1]-[cancco1],[Cco2]-[cancco2],...[Ccon]-[canccon]
			 */
			$exp = explode( ",", $ccos );
			
			for( $i = 0; $i < count($exp); $i++ ){
				@list( $cco, $can ) = explode( "-", $exp[$i] );
				
				if( !empty( $can ) ){
					@movimientoCicloProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, $articulo, $candosis, $unidadFraccion, $cco, $dosisTotales*$can/$cantidad, $canSinAprovechamiento*$can/$cantidad, $can );
				}
			}
			break;
			
		case 2:
			registrarEncabezadoProduccion( $conex, $wbasedato, $fecha, $ronda, $tipoArticulo, @$modalidad );
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

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

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
var celdaRonda = 3;		//Celda de la tabla de tipo de articulo que contiene la ronda que se muestra en el detalle
var celdaTipo = 0;		//Celda de la tabla de tipo de articulo que contiene el codigo del tipo de articulo
var cancelarOnclickMostrarDetalle = false;
var celdaProducido = 6;	//Indica cual es la celda que tiene el campo producido
var noPreguntar = false;	//Indica si debe prguntar "Desea grabar el Articulo?"

var interval;		//Indica el ide del intervalo para detener el reloj

var ultimoFilaDetalleSeleccionado = -1;
var ultimoTablaDetalleSeleccionado = "";


function stopEvent(e) {
	
	if (!e) e = window.event;
    if (e.stopPropagation) {
        e.stopPropagation();
    } else {
        e.cancelBubble = true;
    }
}

function mostrarReloj( tiempo ){
//	return; //..........Quitar linea
	tiempo = tiempo - 1000;

	var dtTiempo = new Date();
	dtTiempo.setTime( tiempo );

	if( dtTiempo.getSeconds() < 10 ){		
		var tiempoFaltante = dtTiempo.getMinutes()+":0"+dtTiempo.getSeconds();
	}
	else{
		var tiempoFaltante = dtTiempo.getMinutes()+":"+dtTiempo.getSeconds();
	}

	if(dtTiempo.getMinutes() < 10 ){
		tiempoFaltante = "0"+tiempoFaltante;
	}
	
	document.getElementById( 'tiempoRestante' ).innerHTML = tiempoFaltante;

	if( tiempo == 0 ){
		document.forms[0].submit();
	}
	
	interval = setTimeout( "mostrarReloj("+tiempo+")", 1000 );
}

function ocultarSelHoras( campo ){

	var slFiltro = document.getElementById( "tbFiltro" );

	if( campo.selectedIndex == 0 ){
		slFiltro.rows[0].cells[2].style.display = "none";		
		slFiltro.rows[1].cells[2].style.display = "none";
	}
	else{
		slFiltro.rows[0].cells[2].style.display = "";		
		slFiltro.rows[1].cells[2].style.display = "";
	}
}

function blink(){
	
	var tb = document.getElementById( "tbTiposArticulos" );

	for( var i = 1; i < tb.rows.length; i = i + 3 ){
		var tb2 = tb.rows[i+2].cells[0].firstChild

		for( var j = 2; j < tb2.rows.length; j++ ){

//			alert( "........."+tb2.rows[j].cells.length );
			if( tb2.rows[j].cells.length >= 6 && tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].style.display == '' ){

				if( !tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].hasBlink && tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].className.toLowerCase() == "fondorojo" ){
					tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].hasBlink = 1;
				}
				
				if( tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].hasBlink ){
					if( tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].hasBlink == 1 ){
						tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].className = tb2.rows[j].cells[ tb2.rows[j].cells.length-7 ].className;
						tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].hasBlink = 2;
					}
					else{
	
						tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].className = "fondorojo";
						tb2.rows[j].cells[ tb2.rows[j].cells.length-6 ].hasBlink = 1;
					}
				}
			}
		}
	}
}


function marcarTodo( campo ){

	var tabla = campo.parentNode.parentNode.parentNode;

	if( confirm( "Desea grabar todos los articulos?" ) ){

		//$.blockUI({ message: $( '#mensaje' ) });
		
		noPreguntar = true;
		for( var i = 1; i < tabla.rows.length; i++ ){
	
			if( tabla.rows[i].cells[ 9 ] && tabla.rows[i].cells[ 9 ].firstChild && tabla.rows[i].cells[ 9 ] && tabla.rows[i].cells[ 9 ].firstChild.tagName  && tabla.rows[i].cells[ 9 ].firstChild.tagName.toLowerCase() == 'input' ){
	//			tabla.rows[i].cells[ celdaProducido ].firstChild.checked = true;
				if( tabla.rows[i].cells[ 9 ].firstChild.checked == !campo.checked ){
					tabla.rows[i].cells[ 9 ].firstChild.click();
				}
			}

			else if( tabla.rows[i].cells[ 3 ] && tabla.rows[i].cells[ 3 ].firstChild && tabla.rows[i].cells[ 3 ] && tabla.rows[i].cells[ 3 ].firstChild.tagName  && tabla.rows[i].cells[ 3 ].firstChild.tagName.toLowerCase() == 'input' ){
				//			tabla.rows[i].cells[ celdaProducido ].firstChild.checked = true;
				if( tabla.rows[i].cells[ 3 ].firstChild.checked == !campo.checked ){
					tabla.rows[i].cells[ 3 ].firstChild.click();
				}
			}
		}

		validacionGrabarEncabezado( campo );

		//$.unblockUI(); 
	}
	else{
		campo.checked = !campo.checked; 
	}

	noPreguntar = false;	
}

/******************************************************************************************
 * Muestra u oculta el detalle de los articulos
 * El detalle del articulo siempre es la fila que le siga al articulo seleccionado
 ******************************************************************************************/
function mostrarOcultarDetalle( campo ){

	if( !cancelarOnclickMostrarDetalle ){
		fila = campo;
		tabla = fila.parentNode;
		var tablaDetalle = tabla;
		var totalColumnas = tablaDetalle.rows[2].cells.length;
	//	fila = campo.parentNode.parentNode;
	//	tabla = fila.parentNode;
	
		//Busco la fila que contiene el codigo del articulo
		for( var i = fila.rowIndex; i > 1 ; i-- ){

			if( totalColumnas == tablaDetalle.rows[i].cells.length ){
				var fila = tablaDetalle.rows[i];
				break;
			}
		}
	
		if( tabla.rows[ fila.rowIndex+fila.cells[0].rowSpan ].style.display == 'none' ){
			 tabla.rows[ fila.rowIndex+fila.cells[0].rowSpan ].style.display = '';

			 var numFila = fila.rowIndex+fila.cells[0].rowSpan;
			 if( tabla.rows[ fila.rowIndex+fila.cells[0].rowSpan ].className.toLowerCase() == 'fondorojo' ){
				 tabla.rows[ fila.rowIndex+fila.cells[0].rowSpan+1 ].style.display = '';
				 numFila = fila.rowIndex+fila.cells[0].rowSpan+1;
			 }
			 
			 if( ultimoFilaDetalleSeleccionado > -1 &&  ultimoTablaDetalleSeleccionado.rows[ ultimoFilaDetalleSeleccionado ] != tabla.rows[ numFila ] ){
				 ultimoTablaDetalleSeleccionado.rows[ ultimoFilaDetalleSeleccionado ].style.display = 'none';
			 }

			 ultimoFilaDetalleSeleccionado = numFila;
			 ultimoTablaDetalleSeleccionado = tablaDetalle; 
		}
		else{
			tabla.rows[ fila.rowIndex+fila.cells[0].rowSpan ].style.display = 'none';

			
			if( tabla.rows[ fila.rowIndex+fila.cells[0].rowSpan ].className.toLowerCase() == 'fondorojo' ){
				 tabla.rows[ fila.rowIndex+fila.cells[0].rowSpan+1 ].style.display = 'none';
			 }
		}
	}
	else{
		cancelarOnclickMostrarDetalle = false;
	}
}

function retornar( campo ){

	if( campo.tagName.toLowerCase() ==  "input" ){
		campo.parentNode.parentNode.parentNode.parentNode.style.display = 'none';
	}

	if( eliminar ){
		var tablaPrincipal = document.getElementById( "tbTiposArticulos" );
		tablaPrincipal.rows[ tipoArticuloSelecconado ].parentNode.removeChild( tablaPrincipal.rows[ tipoArticuloSelecconado ] );
	}

	tipoArticuloSelecconado = 0;
	eliminar = false;
}

/**
 * 
 */
function seleccionarTipoArticulo( campo ){

	//clearInterval( interval );
	tipoArticuloSelecconado = campo.rowIndex;

	var tipoArt = campo.cells[0].innerHTML;
	var tb = campo.parentNode;

	var tbMostrar = document.getElementById( "tbDetalle"+tipoArt );

	if( tb ){
		if( tb.rows[ tipoArticuloSelecconado+1 ].style.display != "" ){

			tb.rows[ tipoArticuloSelecconado+1 ].style.display = "";
			tb.rows[ tipoArticuloSelecconado+2 ].style.display = ""; 

			tbMostrar.style.display = "";

			document.getElementById( tipoArt+"Desplegar" ).value = 'off';
		}
		else{

			tb.rows[ tipoArticuloSelecconado+1 ].style.display = "none";
			tb.rows[ tipoArticuloSelecconado+2 ].style.display = "none";
			
			tbMostrar.style.display = "none";
			document.getElementById( tipoArt+"Desplegar" ).value = 'on';
			retornar( campo );
			return;
		}
	}
}

/************************************************************************************************************
 * Crea un array coorespondiente a los cco que se van a grabar de la forma
 * [Cco1]-[CanCco1],[Cco2]-[CanCco2],[Cco3]-[CanCco3]...
 ************************************************************************************************************/
function crearCcos( campo ){

	var Ccos = "";

	var celdaCampo = campo.parentNode;
	var fila = campo.parentNode.parentNode;
	var tabla = fila.parentNode;
	var slFiltro = document.getElementById( "slFiltroCcoInicial" );
//	var filtro = slFiltro.options[ slFiltro.selectedIndex ].value;
	var filtro = slFiltro.value;

	var totalCcos = celdaCampo.rowSpan;

	//busco los cco que se van a grabar para un articulo
	var j = 0;
	
	for( var i = fila.rowIndex; i < fila.rowIndex+totalCcos; i++ ){

		var filaActual = tabla.rows[ i ];

		if( !fila.cells[ fila.cells.length-6 ].hasBlink || fila.cells[ fila.cells.length-6 ].hasBlink == 0 ){	//Si no tiene blink es por que se va a grabar

			if( campo.checked ){	//Si el campo producido esta marcado (campo.checked) significa que voy a grabar lo producido
				if( j == 0){
					if( parseInt( filaActual.cells[ filaActual.cells.length-2 ].innerHTML ) > 0 
						&& filaActual.cells[ filaActual.cells.length-2 ].innerHTML != filaActual.cells[ filaActual.cells.length-1 ].innerHTML 
					){
						Ccos = Ccos+filaActual.cells[ filaActual.cells.length-5 ].innerHTML+"-"+filaActual.cells[ filaActual.cells.length-2 ].innerHTML;

						var divAux = document.createElement( "div" );

						divAux.innerHTML = filaActual.cells[ filaActual.cells.length-2 ].innerHTML;

						filaActual.cells[ filaActual.cells.length -1 ].removeChild( filaActual.cells[ filaActual.cells.length -1 ].firstChild )
						filaActual.cells[ filaActual.cells.length -1 ].appendChild( divAux.firstChild );
						
						j++;
					}
				}
				else{
					if( parseInt( filaActual.cells[ filaActual.cells.length-2 ].innerHTML ) > 0 
						&& filaActual.cells[ filaActual.cells.length-2 ].innerHTML != filaActual.cells[ filaActual.cells.length-1 ].innerHTML 
					){
						Ccos = Ccos+","+filaActual.cells[ filaActual.cells.length-5 ].innerHTML+"-"+parseInt( filaActual.cells[ filaActual.cells.length-2 ].innerHTML );

						var divAux = document.createElement( "div" );

						divAux.innerHTML = filaActual.cells[ filaActual.cells.length-2 ].innerHTML;

						filaActual.cells[ filaActual.cells.length -1 ].removeChild( filaActual.cells[ filaActual.cells.length -1 ].firstChild )
						filaActual.cells[ filaActual.cells.length -1 ].appendChild( divAux.firstChild );
						
						j++;
					}
				}
			}
			else{ 	//Si el campo producido esta desmarcado (campo.checked) significa que voy a cancelar lo grabado
				if( j == 0){
					if( parseInt( filaActual.cells[ filaActual.cells.length-2 ].innerHTML ) > 0 ){
						Ccos = Ccos+filaActual.cells[ filaActual.cells.length-5 ].innerHTML+"-"+filaActual.cells[ filaActual.cells.length-2 ].innerHTML;

						var divAux = document.createElement( "div" );

						divAux.innerHTML = "0";

						filaActual.cells[ filaActual.cells.length -1 ].removeChild( filaActual.cells[ filaActual.cells.length -1 ].firstChild )
						filaActual.cells[ filaActual.cells.length -1 ].appendChild( divAux.firstChild );
						
						j++;
					}
				}
				else{
					if( parseInt( filaActual.cells[ filaActual.cells.length-2 ].innerHTML ) > 0 ){
						Ccos = Ccos+","+filaActual.cells[ filaActual.cells.length-5 ].innerHTML+"-"+parseInt( filaActual.cells[ filaActual.cells.length-2 ].innerHTML );

						var divAux = document.createElement( "div" );

						divAux.innerHTML = "0";

						filaActual.cells[ filaActual.cells.length -1 ].removeChild( filaActual.cells[ filaActual.cells.length -1 ].firstChild )
						filaActual.cells[ filaActual.cells.length -1 ].appendChild( divAux.firstChild );
						
						j++;
					}
				}
			}
		}
		else{	//Si tiene blink es por que es una modificacion
			if( j == 0){
				if( filaActual.cells[ filaActual.cells.length-1 ].className.toLowerCase() == "fondorojo" ){
					Ccos = Ccos+filaActual.cells[ filaActual.cells.length-5 ].innerHTML+"-"+filaActual.cells[ filaActual.cells.length-1 ].innerHTML;
					j++;
				}
			}
			else{
				if( filaActual.cells[ filaActual.cells.length-1 ].className.toLowerCase() == "fondorojo" ){
					Ccos = Ccos+","+filaActual.cells[ filaActual.cells.length-5 ].innerHTML+"-"+parseInt( filaActual.cells[ filaActual.cells.length-1 ].innerHTML );
					j++;
				}
			}
		}
	}

	return Ccos;
}

/****************************************************************************************************************
 * Realiza llamada ajax para guardar el movimiento de un articulo
 ****************************************************************************************************************/
function grabarMovimientoArticulo( campo ){

	if( true || campo.checked == true ){

		cancelarOnclickMostrarDetalle = false;
		
		if( true || noPreguntar || confirm( "Desea grabar el articulo?" ) ){

			var tablaPrincipal = document.getElementById( "tbTiposArticulos" );
		
			if( tablaPrincipal ){

				var ronda = tablaPrincipal.rows[ tipoArticuloSelecconado ].cells[ celdaRonda ].innerHTML;
				var tipoArticulo = tablaPrincipal.rows[ tipoArticuloSelecconado ].cells[ celdaTipo ].innerHTML;


				var cellIndex = campo.parentNode.cellIndex;
				var fila = campo.parentNode.parentNode;
				var tablaDetalle = fila.parentNode;
				var totalColumnas = tablaDetalle.rows[2].cells.length;



				//Busco la fila que contiene el codigo del articulo
				for( var i = fila.rowIndex; i > 1 ; i-- ){

					if( totalColumnas == tablaDetalle.rows[i].cells.length ){
						var filaArt = tablaDetalle.rows[i];
						break;
					}
				}

				var dosisTotales = parseInt( filaArt.cells[ celdaCantidad+2 ].innerHTML );
				var cantidadAprovechamiento = filaArt.cells[ celdaCantidad+1 ].innerHTML;
				var canSinAprovechamiento = filaArt.cells[ celdaCantidad+3 ].innerHTML;
				var fraccion = dosisTotales/cantidadAprovechamiento;

				var cantidad = fila.cells[cellIndex-1].innerHTML;

				//Unidad de dosis y cantidad de dosis
				var cantidadDosis = fila.cells[cellIndex-3].innerHTML;
				var unidadFraccion = fila.cells[cellIndex-2].innerHTML;
				

				var canSinAprovechamiento = Math.ceil( cantidad*cantidadDosis/fraccion );
				var dosisTotales = Math.ceil( cantidad*cantidadDosis/fraccion )*fraccion;

				var canAprovechamiento = 0;



				var articulo = filaArt.cells[ celdaArticulo ].innerHTML;
				

				var wbasedato = document.getElementById( "wbasedato" ).value;
				var fecha = tablaPrincipal.rows[ tipoArticuloSelecconado ].cells[ tablaPrincipal.rows[ tipoArticuloSelecconado ].cells.length-1 ].innerHTML;	//La fecha por tipo de articulos
				var wemp_pmla = $("#wemp_pmla").val();

				//creando arrya de cco
				/******************************************************************************
				 * Creo un arrya con los cco que se van a grabar con formato
				 * [Cco1]-[Cantidad Cco1],[Cco2]-[Cantidad Cco2],...] 
				 ******************************************************************************/
				 ccos = crearCcos(campo);
				/******************************************************************************/
			
				parametros = "opcionAjax=1&fecha="+fecha
							 +"&ronda="+ronda
							 +"&tipoArticulo="+tipoArticulo
							 +"&articulo="+articulo
							 +"&candosis="+cantidadDosis
							 +"&wbasedato="+wbasedato
							 +"&unidadFraccion="+unidadFraccion
							 +"&canAprovechamiento="+canAprovechamiento
							 +"&dosisTotales="+dosisTotales
							 +"&canSinAprovechamiento="+canSinAprovechamiento
							 +"&cantidad="+cantidad
							 +"&ccos="+ccos
							 +"&wemp_pmla="+wemp_pmla
							;

				var resultado = consultasAjax( "POST", "ciclosProduccion.php", parametros, false, '' );
		

				if( resultado.length < 3 ){

					if( false && campo.checked == false ){
						return;
					}
					else{
						
						if( !fila.cells[ fila.cells.length-6 ].hasBlink  || fila.cells[ fila.cells.length-6 ].hasBlink == 0 ){

							if( !noPreguntar ){

								validacionGrabarEncabezado( campo );
							}
						}
						else{
							//En caso de haber cambio en el articulo
							//Debo sumar el campo modificado a la cantidad tanto como diga el campo modificado
							var modificado = fila.cells[ fila.cells.length-6 ].innerHTML;

							cantidad = parseInt( cantidad*1 ) + parseInt( modificado*1 ); 

							var divAux = document.createElement( "div" );

							divAux.innerHTML = cantidad;

							fila.cells[ fila.cells.length -8 ].removeChild( fila.cells[ fila.cells.length -8 ].firstChild )
							fila.cells[ fila.cells.length -8 ].appendChild( divAux.firstChild );

							fila.cells[ fila.cells.length-6 ].className = fila.cells[ 0 ].className;

							divAux.innerHTML = "0";
							fila.cells[ fila.cells.length-6 ].removeChild( fila.cells[ fila.cells.length-6 ].firstChild );
							fila.cells[ fila.cells.length-6 ].appendChild( divAux.firstChild ); 

							fila.cells[ fila.cells.length-6 ].hasBlink = 0;

							//acutalizo los valores de la fila principal
							filaArt.cells[ celdaCantidad ].innerHTML = parseFloat( filaArt.cells[ celdaCantidad ].innerHTML ) + modificado*fila.cells[ fila.cells.length -10 ].innerHTML;
							filaArt.cells[ celdaCantidad+1 ].innerHTML = Math.ceil( parseFloat( filaArt.cells[ celdaCantidad ].innerHTML )/fraccion );
							filaArt.cells[ celdaCantidad+2 ].innerHTML = parseFloat( filaArt.cells[ celdaCantidad+1 ].innerHTML )*fraccion;
							filaArt.cells[ celdaCantidad+3 ].innerHTML = parseFloat( filaArt.cells[ celdaCantidad+3 ].innerHTML ) + (modificado/Math.abs(modificado))*Math.ceil( Math.abs(modificado)*fila.cells[ fila.cells.length -10 ].innerHTML/fraccion );
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

/****************************************************************************************
 * Valida que todos los articulos para un tipo de articulo esten marcados en on
 * Si es así se graba el encabezado
 ****************************************************************************************/
function validacionGrabarEncabezado( campo ){

	//reviso que todos los campos esten marcado en on
	//si es así se registra el encabezado
	var tablaDetalle = campo.parentNode.parentNode.parentNode;
	var celda = campo.parentNode.cellIndex;
	var produccionCompleta = true;

	for( var i = 1; i < tablaDetalle.rows.length-1; i++ ){

		if( tablaDetalle.rows[i].cells[ 3 ] && tablaDetalle.rows[i].cells[ 3 ].firstChild && tablaDetalle.rows[i].cells[ 3 ].firstChild.checked == false 
			&& tablaDetalle.rows[i].cells[ 2 ] && tablaDetalle.rows[i].cells[ 2 ].innerHTML > 0
		){
			produccionCompleta = false;
			break;
		}

		if( tablaDetalle.rows[i].cells[ 9 ] && tablaDetalle.rows[i].cells[ 9 ].firstChild && tablaDetalle.rows[i].cells[ 9 ].firstChild.checked == false 
			&& tablaDetalle.rows[i].cells[ 8 ] && tablaDetalle.rows[i].cells[ 8 ].innerHTML > 0
		){
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
		var fecha = tablaPrincipal.rows[ tipoArticuloSelecconado ].cells[ tablaPrincipal.rows[ tipoArticuloSelecconado ].cells.length-1 ].innerHTML;
//		var modalidad = document.getElementById( "slModalidad" ).options[ document.getElementById( "slModalidad" ).selectedIndex ].text.substr( 0, 1 )
		var modalidad = document.getElementById( "slModalidadInicial" ).value;
		var wemp_pmla = $("#wemp_pmla").val();
	
		//$fecha, $ronda, $tipoArticulo
		
		parametros = "opcionAjax=2&fecha="+fecha+"&ronda="+ronda+"&tipoArticulo="+tipoArticulo+"&wbasedato="+wbasedato+"&modalidad="+modalidad+"&wemp_pmla="+wemp_pmla;

		var resultado = consultasAjax( "POST", "ciclosProduccion.php", parametros, false, '' );

		if( resultado.length > 2 ){
			alert( "Error inesperado al grabar encabezado: "+resultado );
		}
	}
}

function seleccionarTipo( campo, i ){

	tipoArticuloSelecconado = campo.rowIndex-i;
}

window.onload = function(){

	mostrarReloj( 600000 );

	ocultarSelHoras( document.getElementById( "slModalidad" ) );

	/******************************************************************************************
	 * Consultando que tablas se deben ocultar por que al filtrar no tiene articulos 
	 ******************************************************************************************/
	 var tabTipos = document.getElementById( "tbTiposArticulos" );

	 for( var i = 1; i < tabTipos.rows.length; i = i+3 ){

		 var ocultar = true;

		 var tabDetalle = tabTipos.rows[i+2].cells[0].firstChild;

		 for( var j = 2; j < tabDetalle.rows.length; j++ ){

			 if( tabDetalle.rows[j].style.display != 'none' && tabDetalle.rows[j].cells.length > 1 ){
				 ocultar = false;
				 break;
			 }
		 }

		 if( ocultar ){
			 tabTipos.rows[i].style.display = 'none';
			 tabTipos.rows[i+1].style.display = 'none';
			 tabTipos.rows[i+2].style.display = 'none';
		 }		 
	 }
	/******************************************************************************************/

	

	
	
	if(document.getElementById("fixedFiltro")){
    	$("#fixedFiltro").draggable();    	
    }

	setInterval( "blink()", 1000 );	
}

</script>
<body>
<?php 
//echo "RECORDAR ARREGLAR EL QUERY DEL KARDEX.....<BR>";
//echo date("Y-m-d H:i:s");
echo "<form method=post>";
	
	$wactualiz = " Febrero 21 de 2022 ";

	include_once("root/comun.php");

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	
	$fechaActual = date("Y-m-d");
	
	//Indica si la ronda mostrada es de ayer, hoy o mañana
	$infoDia[ 0 ] = "Ayer";
	$infoDia[ 1 ] = "Hoy";
	$infoDia[ 2 ] = "Ma&ntilde;ana";
	

	
	echo "<INPUT TYPE='hidden' id='wbasedato' name='wbasedato' value='$wbasedato'>";
	echo "<INPUT TYPE='hidden' id='wcenmez' name='wcenmez' value='$wcenmez'>";
	echo "<INPUT type='hidden' id='hiFecha' name='hiFecha' value='$fechaActual'>";
	echo "<INPUT type='hidden' id='wemp_pmla' name='wemp_pmla' value='$wemp_pmla'>";
	
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
	
	//Tiempo restante de recarga
//	echo "<table align='right' style='width:80%'>";
//	echo "<tr><td align='right'><b>Tiempo sin actualizar: </b>";
////	echo "<div id='tiempoRestante' style='font-size:10pt;font-weight:bold;'></div>";
//	echo "</td><td><a onclick='document.forms[0].submit();'>Actualizar</a></td></tr>";
//	echo "</table>";
	
	$arRondas = crearRondas( 2 );
	
	//Creando filtro por cco, horas y modalidad
//	echo "<br><br>";
//	echo "<div id='fixedFiltro' style='position:absolute;z-index:99;width:790px;height:45px;right:10px;top:10px;padding:5px;background:#FFFFCC;border:2px solid #FFD700'>";
	echo "<div>";
	echo "<table align='center' id='tbFiltro'>";
	echo "<tr class='encabezadotabla' align='center'>";
	echo "<td>Centro de costos</td>";
	echo "<td>Modalidad</td>";
	echo "<td>Hora</td>";
	echo "<td>Tiempo restante para acutalizar(seg)</td>";
	echo "<td rowspan='2' class='fila2'><INPUT TYPE='button' onClick='javascript:document.forms[0].submit();' value='Actualizar'></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td>";
	
//	echo "<select name='slFiltroCco' id='slFiltroCco' onChange='document.forms[0].submit();'>";
	echo "<select name='slFiltroCco' id='slFiltroCco'>";
	echo "<option value='%'>% - Todos</option>";
	
	/*$sql = "SELECT
				ccocod, cconom
			FROM
				{$wbasedato}_000011
			WHERE
				ccoest = 'on'
				AND ccohos = 'on'
				AND ccocpx = 'on'
			";
				
	$resCco = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $resCco );*/
	
	/*for( $i = 0; $rows = mysql_fetch_array( $resCco ); $i++ ){
		
		if( $slFiltroCco == $rows['ccocod'] ){
			echo "<option selected value='{$rows['ccocod']}'>{$rows['ccocod']}-{$rows['cconom']}</option>";
		}
		else{
			echo "<option value='{$rows['ccocod']}'>{$rows['ccocod']}-{$rows['cconom']}</option>";
		}
	}*/
	$cco="ccoest = 'on' AND ccohos = 'on' AND ccocpx = 'on'";
	$filtro="--";
		
	$centrosCostos = consultaCentrosCostos($cco, $filtro);
	foreach ($centrosCostos as $centroCostos)
	{
		 if( $slFiltroCco == $centroCostos->codigo ){
         echo "<option selected value='".$centroCostos->codigo."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
		}
		else{
		echo "<option value='".$centroCostos->codigo."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
		}
    }
	
	echo "</select>";
	
	echo "</td>";
	
	
	//Modalidad
	echo "<td>";
	
	$modalidades[ 1 ] = "Programada";
	$modalidades[ 2 ] = "No programada";
	
	if( !isset($slModalidad) ){
		$slModalidad = 1;
	}

	echo "<select name='slModalidad' id='slModalidad' onChange='ocultarSelHoras(this);'>";
//	echo "<select name='slModalidad' id='slModalidad'>";
	
	foreach( $modalidades as $key => $value ){
		
		if( $key == $slModalidad ){
			echo "<option value='$key' selected>$value</option>";
		}
		else{
			echo "<option value='$key'>$value</option>";
		}
	}
	
//	echo "<option value='1'>Programada</option>";
//	echo "<option value='2'>No programada</option>";
	
	echo "</select>";
	
	echo "</td>";
	
	
	//Horas
	echo "<td>";
	
	echo "<select  name='slRonda'>";
	
	for( $i = 0; $i < 12; $i++ ){
		
		if( $i*2 == $slRonda ){
			if( $i*2 > 9 ){
				echo "<option value='".($i*2)."' selected>".($i*2)."</option>";
			}
			else{
				echo "<option value='0".($i*2)."' selected>0".($i*2)."</option>";
			}
		}
		else{
			if( $i*2 > 9 ){
				echo "<option value='".($i*2)."'>".($i*2)."</option>";
			}
			else{
				echo "<option value='0".($i*2)."'>0".($i*2)."</option>";
			}
		}
	}
	
	echo "</select>";
	
	echo "</td>";
	
	
	
	////Tiempo transcurrido sin actualizar
	echo "<td class='fila2' align='center'>";
	echo "<div id='tiempoRestante' style='font-size:10pt;font-weight:bold;'></div>";
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table>";
	
	echo "</div>";
	
	unset( $rows );
	unset( $res );
	
	if( !isset($slFiltroCco) ){
		$slFiltroCco = "%";
	}		
	//fin creacion de filtro por cco
	
	//creo campos ocultos para que son los usados en javascript para guardar datos en la BD
	echo "<INPUT type='hidden' id='slFiltroCcoInicial' name='slFiltroCcoInicial' value='$slFiltroCco'>";
	echo "<INPUT type='hidden' id='slModalidadInicial' name='slModalidadInicial' value='".substr( $modalidades[ $slModalidad ], 0, 1 )."'>";

	$tiposAriculos = Array();	//tipos de articulo
	$datosTipo = Array();		//datos del kardex por tipo
	$detalleArticulo = Array();		//Muesra el detalle de articulos por paciente
	$detalleNuevo = Array();	//Si un articulo ya esta producido, la informacion se guarda en este arreglo
								//Eso se hace para poder comparar si hubo algun cambio
	
	$frecuenciaPreparacion = 6;
	
//	$ronda = "20:00:00";
	
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
				AND tarpdx = 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
				
	if( $numrows > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['codigo'] = $rows[ 'Tarcod' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['nombre'] = $rows[ 'Tardes' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['tiempoPreparacion'] = $rows[ 'Tarpre' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCorteProduccion'] = $rows[ 'Tarhcp' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCaroteDispensacion'] = $rows[ 'Tarhcd' ];
			
			$aux = consultarUltimaRonda( $conex, $wbasedato, $rows[ 'Tarcod' ], $rows[ 'Tarpre' ] );
			// echo "<br>........$aux ---- ".$rows[ 'Tarcod' ];
			$auxfec = ""; 
			@list( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'] ) = explode( " ", $aux );
			
			if( !isset($slModalidad) || $slModalidad == 1 ){
				//Agosto 18 de 2011
				//rondaProducida( $conex, $wbasedato, date( "Y-m-d", strtotime( $aux ) ), "$slRonda:00:00", $rows[ 'Tarcod' ] );
				if( rondaProducida( $conex, $wbasedato, $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'], $rows[ 'Tarcod' ] ) && time() > proximaRondaProduccion( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'], $rows[ 'Tarpre' ] ) - ( strtotime( "1970-01-01 ".$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCorteProduccion'] ) - strtotime( "1970-01-01 00:00:00" ) )  ){
					// echo "Hola22222.......".$rows[ 'Tarcod' ];
					$tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] = proximaRondaProduccion( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'], $rows[ 'Tarpre' ] );
				}
				else{ 	//Agosto 18 de 2011
					// echo "Hola111.......".$rows[ 'Tarcod' ];
					$tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] = strtotime( $auxfec." ". $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'] );
				}
			}
			else{
//				$aux = proximaRondaProduccion( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'], $rows[ 'Tarpre' ] );
//				$aux = date( "Y-m-d" )." $slRonda:00:00";

				$aux2 = strtotime( date( "Y-m-d" )." $slRonda:00:00" );	//ronda seleccionada
				
				$dif = $aux2 - strtotime( $aux );	//diferencia entre ultima ronda y la ronda seleccionada
				
				//Si la difrencia sobre
				if( $dif > 0 ){
					$aux3 = ceil( $dif / ($rows[ 'Tarpre' ]*3600) )*($rows[ 'Tarpre' ]*3600);
				}
				else{
					
					$rndProducida = rondaProducida( $conex, $wbasedato, date( "Y-m-d", strtotime( $aux ) ), "$slRonda:00:00", $rows[ 'Tarcod' ] );
					if( time() > strtotime( $aux ) ){
						if( $rndProducida ){
							$aux = date( "Y-m-d" )." $slRonda:00:00";
//							echo date( "Y-m-d H:i:s" );
							$aux3 = 24*3600;
						}
						else{
							$aux3 = $rows[ 'Tarpre' ]*3600;
						}
					}
					else{
						$aux3 = 0;
					}
				}
				
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] = strtotime( $aux ) + $aux3;
//				echo "<br>......{$rows[ 'Tarcod' ]} - ".date( "Y-m-d H:i:s", $tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] );
				$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCorteProduccion'] = 24*3600; 
//				echo "<br>Hola........".$rows[ 'Tarcod' ].".......".date( "Y-m-d H:i:s", $tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] );
				
//				echo "<br>".date( "H:i:s", strtotime( $aux ) + $aux3 );
			} 
			
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['tieneArticulos'] = 0;
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['totalArticulosSinFiltro'] = 0;
		}
	}
	
	/************************************************************************************************************************
	 * Creando tipo de articulo ficticio
	 ************************************************************************************************************************/
	$tiposAriculos[ "SADT" ]['codigo'] = "SADT";
	$tiposAriculos[ "SADT" ]['nombre'] = "SIN ASIGNACION DE TIPO";
	$tiposAriculos[ "SADT" ]['tiempoPreparacion'] = 24;
	$tiposAriculos[ "SADT" ]['horaCorteProduccion'] = 2;
	$tiposAriculos[ "SADT" ]['horaCaroteDispensacion'] = 2;
	
	$auxfec = ""; 
	@list( $auxfec, $tiposAriculos[ "SADT" ]['ronda'] ) = explode( " ", date( "Y-m-d 00:00:00" ) );	
	
	$tiposAriculos[ "SADT" ]['proximaRonda'] = strtotime( "Y-m-d 00:00:00" )+2*3600;	
	/************************************************************************************************************************/

	
	if( empty( $detalle ) || $detalle == 'off' ){
		
		//consultando los códigos de productos de la centra
		$sql = "SELECT
					*
				FROM
					{$wcenmez}_000001
				WHERE
					tippro = 'on'
					AND tipest = 'on'
				";
			
		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
			
			for($i = 0; $rows = mysql_fetch_array( $res ); $i++){
				$productosCM[ strtolower( $rows['Tipdis'] ) ] = 1;
			}
		}
		
		echo "<div>";
		
		//Consultando el kardex
		//	Tener en cuenta que el paciente no este de alta o con alta en proceso

		//creando tabla temporal de la 54
		$temp = "Temp54".date("His");
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $temp 				
			    ( INDEX idx( kadhis, kading, kadfin, kadhin, kadfec ) )
				SELECT 
					* 
				FROM
					{$wbasedato}_000054 a
				WHERE
					kadfec = '".date( "Y-m-d" )."'
					AND kadare = 'on'
					AND kadsus != 'on'
					AND kadest = 'on'
					AND kadcdi > 0 
					AND kadess != 'on'
					AND kadcdi - kaddis > 0				
				UNION
				SELECT 
					* 
				FROM
					{$wbasedato}_000054 a
				WHERE
					kadfec = '".date( "Y-m-d", time()-24*3600 )."'
					AND kadare = 'on'
					AND kadsus != 'on'
					AND kadest = 'on'
					AND kadcdi > 0 
					AND kadess != 'on'
					AND kadcdi - kaddis > 0
					AND kadhis NOT IN( SELECT
											karhis
									   FROM
									   		{$wbasedato}_000053 b
									   WHERE
									   		b.fecha_data = '".date("Y-m-d")."'
									 )
				ORDER BY
					kadpro, kadart
				";
					
		//Consulta para prueba local
		// $temp = "Temp54".date("His");
		// $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $temp 				
			    // ( INDEX idx( kadhis, kading, kadfin, kadhin, kadfec ) )
				// SELECT 
					// * 
				// FROM
					// {$wbasedato}_000054 a
				// WHERE
					// kadfec = '".date( "Y-m-d" )."'
					// AND kadest = 'on'
					// AND kadcdi > 0 
				// UNION
				// SELECT 
					// * 
				// FROM
					// {$wbasedato}_000054 b
				// WHERE
					// kadfec = '".date( "Y-m-d", time()-24*3600 )."'
					// AND kadest = 'on'
					// AND kadcdi > 0
					// AND kadhis NOT IN (SELECT
											// karhis
										// FROM
											// {$wbasedato}_000053 b
										// WHERE
											// b.fecha_data = '".date( "Y-m-d" )."'
									  // )
				// ORDER BY
					// kadpro, kadart
				// ";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		//Consulta para pruebas locales
		// $sql = "SELECT 
					// * 
				// FROM
					// $temp, {$wbasedato}_000018
				// WHERE
					// ubihis = kadhis
					// AND ubiing = kading
					// AND ubisac like '%'
				// "; //echo "<br>........<pre>$sql</pre>";
					
		//Consulta todos los pacientes que se encuentren en el piso
		$sql = "SELECT 
					a.*, Habcco as Ubisac 
				FROM
					$temp a, 
					{$wbasedato}_000020 b, 
					{$wbasedato}_000011 c
				WHERE
					habhis = kadhis
					AND habing = kading
					AND habcco = ccocod
					AND ccocpx = 'on'
				"; //echo "<br>........<pre>$sql</pre>";
			
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
				
				if( isset( $tiposAriculos[ $tipo ] ) && $tipo != "SADT" ){
				
					//$fecpxr: fecha proxima ronda
					list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s" ,$tiposAriculos[ $tipo ]['proximaRonda'] ) );
					
//					echo "<br>......----$tipo ".date( "Y-m-d H:i:s" ,$tiposAriculos[ $tipo ]['proximaRonda'] );
					
					$tiempoPreparacion = $tiposAriculos[ $tipo ]['tiempoPreparacion'];
					$corteProduccion = $tiposAriculos[ $tipo ]['horaCorteProduccion'];
					$corteDispensacion = $tiposAriculos[ $tipo ]['horaCaroteDispensacion'];
					
					$horaSiguiente = "";
					$perteneceRonda = perteneceRonda( $fecpxr, $ronda, $tiempoPreparacion, $rows['Kadfin'], $rows['Kadhin'], $frecuencias[ $rows['Kadper'] ], $horaSiguiente, $despuesHoraCortePx, $rows['Kaddma'], $rows['Kaddia'] );
					
					if( $perteneceRonda ){
						$tiposAriculos[ $tipo ]['totalArticulosSinFiltro']++;
					}
					
					if( true || !isset($slFiltroCco) || $slFiltroCco == '%' || $slFiltroCco == $rows['Ubisac'] ){
						
						if( $perteneceRonda ){
							$tiposAriculos[ $tipo ]['tieneArticulos']++;
						}
						
						$mostrarRonda = mostrarRonda( $fecpxr, $ronda, $corteProduccion, $corteDispensacion );
						
						$rondaProducida = false && rondaProducida( $conex, $wbasedato, $fecpxr, $ronda, $tipo );	//Agosto 18 de 2011, siempre en false
						
						$tiposAriculos[ $tipo ]['rondaProducida'] = $rondaProducida;
						

						if( !$rondaProducida && time() > $tiposAriculos[ $tipo ]['proximaRonda'] ){
							$mostrarRonda = true;
						}
						elseif( !$rondaProducida && time() > $tiposAriculos[ $tipo ]['proximaRonda'] - strtotime( "1970-01-01 {$tiposAriculos[ $tipo ]['horaCorteProduccion']}" ) + strtotime( "1970-01-01 00:00:00" )  ){
							$mostrarRonda = true;
						} 
						
						//Si tiene modalidad no programada y no se ha producido la ronda seleccionada, entonces se debe mostrar la ronda
						//ademas la ronda debe ser igual a la hora seleccionada
						if( /*!$rondaProducida && */( isset($slModalidad) && $slModalidad == 2 ) ){
							$mostrarRonda = false;

							if( $slRonda.":00:00" == $ronda ){
								$mostrarRonda = true;
								$rondaProducida = false;
							}
						}

						if( $perteneceRonda && $mostrarRonda && !$rondaProducida ){
							
							if( !isset( $datosTipo[ $tipo ] ) || $i == 0 ){
								if( true || $slFiltroCco == "%" && $perteneceRonda && $mostrarRonda && !$rondaProducida ){
									tieneProduccion( $conex, $wbasedato, $wcenmez, $fecpxr, $ronda, $rows[ 'Kadart' ], $tipo, $datosTipo, $rows[ 'Kadcfr' ], $rows[ 'Kadufr' ] );
								}
							}
							
							//Miro si ya marcaron el articulo como producido
							//Si es así muestra la información de lo producido, de lo contrario de lo que se va a producir
							if( !isset($datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['producido']) || !isset($datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ] ) ){
								$tieneProduccion = false;
							}
							else{
								$tieneProduccion = $datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['producido'];
							}
							
							if( true || !$tieneProduccion ){
								
								$totalAplicaciones = calcularCantidadAplicacionesRondaProduccion( $fecpxr, $ronda, $tiempoPreparacion, $horaSiguiente, $frecuencias[ $rows['Kadper'] ], $rows[ 'Kaddma' ], $rows[ 'Kaddia' ] );
								
								$can = ceil( $rows['Kadcfr']/$rows['Kadcma']*$totalAplicaciones );
									
								if( $can < 0 ){
									$can = 0;
								}
								
								if( !( !isset($slFiltroCco) || $slFiltroCco == '%' || $slFiltroCco == $rows['Ubisac'] ) ){
									$totalAplicaciones = 0;
									$can = 0;
								}
								
//								if( /*!$tieneProduccion ||*/ !isset($detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]) ){
//								if( !isset( $detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']] ) ){
								if( !isset($detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ] )
									|| ( isset($detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ] )
									&& $detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['marcaGrabado'] == false )  
								){
									$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['codArticulo'] = $rows[ 'Kadart' ];
									if( !isset($datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo']) ){
										$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo'] = nombreArticulo( $conex, $wbasedato, $wcenmez, $rows[ 'Kadart' ] );
									}
									@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['canArticulo'] += $can;
									@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['canDosis'] += $rows['Kadcfr']*$totalAplicaciones;
									@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['uniDosis'] = $rows['Kadufr'];
									@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['fraUnidad'] = $rows['Kadcma'];

									@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['producido'] = false;
									
									@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['total'] += $totalAplicaciones;
									@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['producido'] = false;
								}
								else{
									
									$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['codArticulo'] = $rows[ 'Kadart' ];
									
									if( !isset($detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo']) ){
										$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo'] = nombreArticulo( $conex, $wbasedato, $wcenmez, $rows[ 'Kadart' ] );
									}
									@$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['canArticulo'] += $can;
									@$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['canDosis'] += $rows['Kadcfr']*$totalAplicaciones;
									@$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['uniDosis'] = $rows['Kadufr'];
									@$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['fraUnidad'] = $rows['Kadcma'];

									@$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['producido'] = true;
									
									@$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['total'] += $totalAplicaciones;
									@$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['producido'] = true;
								}
								
								//informacion detallada
								/****************************************************************************************************************
								 * Para pintar el detalle por articulo 
								 ****************************************************************************************************************/
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['codArticulo'] = $rows[ 'Kadart' ];
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['nomArticulo'] = $datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo'];
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['dosis'] += $rows[ 'Kadcfr' ];
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['unidadesTotales'] += $can;
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['nombre'] = consultarNombrePaciente( $conex, $rows[ 'Kadhis' ] );
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['unidadDosis'] = $rows['Kadufr'];
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['frecuencia'] = $frecuencias[ $rows['Kadper'] ];
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['fechaHoraInicio'] = $rows['Kadfin']." a las ".$rows['Kadhin'];
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['totalAplicaciones'] = $totalAplicaciones;
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['Cco'] = $rows[ 'Ubisac' ];
								@$detalleArticulo[ $tipo ][ $rows[ 'Kadart' ] ][ $rows[ 'Kadhis' ].'-'.$rows[ 'Kading' ] ][$rows['Kadfin']." a las ".$rows['Kadhin']]['Observaciones'] = $rows[ 'Kadobs' ];
								/****************************************************************************************************************/
								
								/****************************************************************************************************************
								 * Esto es para controlar la grabacion por cco
								 ****************************************************************************************************************/
								if( !isset($detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]) ){
										@$detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ 'totalFilas' ]++;
								}
								
								if( !isset( $datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['modificado'] ) ){
									$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['modificado'] = 0;
								}
								
								@$detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['unidadesTotales'] += $totalAplicaciones;;
								@$detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['unidadDosis'] = $rows['Kadufr'];
								@$detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['dosis'] = $rows['Kadcfr'];
								
								//coloco la cantidad grabada
								if( !isset($detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['grabado']) ){
									$detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['grabado'] = 0;
									$detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['marcaGrabado'] = false;
									
									@$detalleNuevo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['producido'] = false;
									
									@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['producido'] = false;
								}
								
//								@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['modificado'] = $detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['unidadesTotales']-$detalleCco[ $tipo ][ $rows[ 'Kadart' ] ][ $rows['Kadcfr'] ][ $rows['Kadufr'] ][ $rows[ 'Ubisac' ] ]['grabado'];
								
								if( isset( $productosCM[ strtolower($tipo) ] ) ){
									creandoInsumos( $conex, $wbasedato, $wcenmez, $tipo, $rows[ 'Kadart' ], $totalAplicaciones, $insumos );
								}
								/****************************************************************************************************************/
							}
							else{
							}
						}
					}
				}
				elseif( $tipo == "SADT" ){
					/************************************************************************************************************
					 * Agosto 8 de 2011
					 ************************************************************************************************************/
					
					@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['canArticulo'] += $can;
					@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['canDosis'] += $rows['Kadcfr']*$totalAplicaciones;
					@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['uniDosis'] = $rows['Kadufr'];
					@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['fraUnidad'] = $rows['Kadcma'];

					@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['producido'] = false;
					
					@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['total'] += $totalAplicaciones;
					@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['dosisTotales'][ "".$rows['Kadcfr']."" ]['producido'] = false;
					
					@$datosTipo[ $tipo ][ $rows[ 'Kadart' ] ]['nomArticulo'] = nombreArticulo( $conex, $wbasedato, $wcenmez, $rows[ 'Kadart' ] );
					/************************************************************************************************************/
				}
			}
		}
		
//		echo "<br>.........".contarFilasPorArticulo( $detalleCco, "N", "110922", "1183" );

		if( isset($detalleCco) && count($datosTipo) > 0  ){
			consultarCantidadModificada( $detalleCco );
		}
		
		if( count($datosTipo) > 0 ){
			
			echo "<table align='center' id='tbTiposArticulos' style='width:100%'>";
	
			echo "<tr class='encabezadotabla' align='center' style='display:none'>";
			echo "<td>Codigo</td>";
			echo "<td style='width:100%'>Tipo de art&iacute;culos</td>";
			echo "<td>Corte de<br>produccion</td>";
			echo "<td>Pr&oacute;xima<br>ronda</td>";
			echo "<td>Para</td>";
			echo "<td style='display:none'>Detalle</td>";
			echo "</tr>";
	
			$i = 0;
			foreach( $tiposAriculos as $keyTipos => $valueTipos ){
			
				if( count( @$datosTipo[ $keyTipos ] ) > 0 && $keyTipos != "SADT" ){
					
						$totalRondas = $valueTipos['tiempoPreparacion']/2;
					
						$rondaActual = date( "H:i:s", $valueTipos['proximaRonda'] );
						$rondaActualFecha = date( "Y-m-d", $valueTipos['proximaRonda'] );
						
						//Control para mostrar ayer, hoy o mañana
						$indexInfoDia = 0;
						
						if( $rondaActualFecha == $fechaActual ){
							$indexInfoDia = 1;
						}
						elseif( $rondaActualFecha > $fechaActual ){
							$indexInfoDia = 2;
						}
					
						$class = "class='fila".(($i % 2)+1)."'";
						
						
						
						$var = "{$keyTipos}Desplegar";
						
						if( !isset( $$var ) || isset($$var) && $$var == 'off' ){
							echo "<INPUT type='hidden' value='off' name='{$keyTipos}Desplegar' id='{$keyTipos}Desplegar'>";
							$$var = 'off';
						}
						else{
							echo "<INPUT type='hidden' value='on' name='{$keyTipos}Desplegar' id='{$keyTipos}Desplegar'>";
							$$var = 'on';
						}
						
						if( $$var == 'on' ){
							$style = 'display:none';
						}
						else{
							$style = '';
						}
					
					if( !rondaProducida( $conex, $wbasedato, $rondaActualFecha, $rondaActual, $keyTipos ) ){	//Agosto 18 de 2011, siempre en false 
					
						echo "<tr $class onClick='seleccionarTipoArticulo(this)' style='cursor:pointer;font-size:20pt;'>";
						echo "<td align='center' style='display:none'>$keyTipos</td>";
						echo "<td style='width:100%;height:40' align='center'><b>{$valueTipos['nombre']}</b></td>";
						echo "<td align='center' style='display:none'>{$valueTipos['horaCorteProduccion']}</td>";
						echo "<td style='display:none'>$rondaActual</td>";
						echo "<td style='display:none'>$totalRondas ronda(s)</td>";
						echo "<td align='center' style='display:none'><a onClick='seleccionarTipoArticulo(this)'>ver</a></td>";
						echo "<td align='center' style='display:none'>$rondaActualFecha</td>";
						echo "</tr>";
						
						
						
						echo "<tr style='$style' onMouseOver='seleccionarTipo(this, 1);'>";
						echo "<td>";
						
						echo "<table align='center'>";
						echo "<tr>";
						echo "<td align='center' style='display:none'>{$valueTipos['horaCorteProduccion']}</td>";
						echo "<td align='center'><b>Producci&oacute;n pendiente para la(s)</b></td>";
						echo "<td>$rondaActual ".date( "a", strtotime( $rondaActual ) )." de <b>{$infoDia[ $indexInfoDia ]}</b></td>";
						echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
						echo "<td align='center'><b>Producción para</b></td>";
						echo "<td>$totalRondas ronda(s)</td>";
						echo "</tr>";
						echo "</table>";
						
						echo "</td>";
						
						echo "</tr>";
						
						//Mostrando la tabla de datos
						echo "<tr style='$style' onMouseOver='seleccionarTipo(this, 2);'>";
						echo "<td>";
						mostrarArticulosRonda( $datosTipo[$keyTipos], $keyTipos, $detalleArticulo, $detalleNuevo, $rondaActualFecha, $rondaActual );
						echo "<br><br>";
	//					echo "Total Articulos: {$valueTipos['tieneArticulos']}/{$valueTipos['totalArticulosSinFiltro']}";
						echo "</td>";
						echo "</tr>";
						
						$i++;
					}
				}
				elseif( $keyTipos == "SADT" ){
				
					/************************************************************************************************
					 * Agosto 8 de 2011
					 * Presentacion de articulos sin asignacion de tipo
					 ************************************************************************************************/				
					if( isset($datosTipo[ $keyTipos ]) && count( $datosTipo[ $keyTipos ] ) > 0 ){
						$class = "class='fila".(($i % 2)+1)."'";
						echo "<tr $class onClick='seleccionarTipoArticulo(this)' style='cursor:pointer;font-size:20pt;'>";
						echo "<td align='center' style='display:none'>$keyTipos</td>";
						echo "<td style='width:100%;height:40' align='center'><b>{$valueTipos['nombre']}</b></td>";
						echo "<td align='center' style='display:none'></td>";
						echo "<td style='display:none'></td>";
						echo "<td style='display:none'></td>";
						echo "<td align='center' style='display:none'><a onClick='seleccionarTipoArticulo(this)'>ver</a></td>";
						echo "<td align='center' style='display:none'></td>";
						echo "</tr>";
						
						echo "<tr style='display:none' onMouseOver='seleccionarTipo(this, 1);'>";
						echo "<td>";
						
						echo "<table align='center'>";
						echo "<tr>";
						echo "<td align='center' style='display:none'>{$valueTipos['horaCorteProduccion']}</td>";
						echo "<td align='center' style='display:'><b>Articulos que no se encuentran bien cofigurados</b></td>";
						echo "</tr>";
						echo "</table>";
						
						echo "</td>";
						
						echo "<tr style='display:none'>";
						echo "<td>";
						
						echo "<table align='center'>";

						echo "<tr class='encabezadotabla'>";
						echo "<td>Codigo</td>";
						echo "<td>Nombre</td>";
						echo "</tr>";
						
						$j = 0;
						foreach( $datosTipo[ $keyTipos ] as $keyArticulos => $valueArticulos ){
						
							$class2 = "class='fila".(($j % 2)+1)."'";
							
							echo "<tr>";
							
							echo "<td $class2>";
							echo $keyArticulos;
							echo "</td>";
							
							echo "<td $class2>";
							echo $valueArticulos['nomArticulo'];
							echo "</td>";
							
							echo "</tr>";
							
							$j++;
						}			
						
						echo "</table>";
						
						echo "</td>";
						echo "</tr>";
						
						$i++;
						/************************************************************************************************/
					}					
				}
			}
			
			echo "</table>";
			
			if( $i == 0 ){
				echo "<p><center><b>Sin productos para producir</b></center></p>";
			}
		}
		else{
			echo "<p><center><b>Sin productos para producir</b></center></p>";
		}
		
		echo "</div>";
		
		//Registro encabezados de los articulos si no se encuentra nada para producir, esto para que
		//muestre la ronda siguiente
		foreach( $tiposAriculos as $keyTipos => $valueTipos ){
		
			if( $keyTipos != "SADT" ){
			
				if( !isset($valueTipos['rondaProducida']) ){
					list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s" ,$valueTipos['proximaRonda'] ) );
					$valueTipos['rondaProducida'] = rondaProducida( $conex, $wbasedato, $fecpxr, $ronda, $keyTipos );
				}
				
				if( $valueTipos['totalArticulosSinFiltro'] == 0 && time() > $valueTipos['proximaRonda'] && !$valueTipos['rondaProducida'] ){
					registrarEncabezadoProduccion( $conex, $wbasedato, date( "Y-m-d", $valueTipos['proximaRonda'] ), date( "H:i:s", $valueTipos['proximaRonda'] ), $keyTipos, $modalidades[ $slModalidad ][ 0 ] );
				}
			}
		}
	}
	
	echo "<br></br>";
	echo "<table align='center'>";
	echo "<tr><td><INPUT type='button' value='Cerrar ventana' style='width:100' onClick='cerrarVentana()'></td></tr>";
	echo "</table>";
	
	echo "</form>";
	
	echo "<div id='mensaje' style='display:none'>";
	echo "<h1>Procesando....</h1>";
	echo "</div>";
	
	echo "</body>";
 
//<script>
//if(document.getElementById("fixedFiltro")) { 
//	fixedMenuId = "fixedFiltro"; 
//	var fixedMenu = {
//		hasInner:typeof window.innerWidth == "number", 
//		hasElement:document.documentElement != null && document.documentElement.clientWidth, 
//		menu:document.getElementById ? document.getElementById(fixedMenuId) : document.all ? document.all[fixedMenuId] : document.layers[fixedMenuId]
//	}; 
//	fixedMenu.computeShifts = function() { 
//		fixedMenu.shiftX = fixedMenu.hasInner ? pageXOffset : fixedMenu.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu.shiftX += fixedMenu.targetLeft > 0 ? fixedMenu.targetLeft : (fixedMenu.hasElement ? document.documentElement.clientWidth : fixedMenu.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu.targetRight - fixedMenu.menu.offsetWidth; 
//		fixedMenu.shiftY = fixedMenu.hasInner ? pageYOffset : fixedMenu.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu.shiftY += fixedMenu.targetTop > 0 ? fixedMenu.targetTop : (fixedMenu.hasElement ? document.documentElement.clientHeight : fixedMenu.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu.targetBottom - fixedMenu.menu.offsetHeight }; 
//		fixedMenu.moveMenu = function() { 
//			fixedMenu.computeShifts(); 
//			if(fixedMenu.currentX != fixedMenu.shiftX || fixedMenu.currentY != fixedMenu.shiftY) { 
//				fixedMenu.currentX = fixedMenu.shiftX; 
//				fixedMenu.currentY = fixedMenu.shiftY; 
//				if(document.layers) { 
//					fixedMenu.menu.left = fixedMenu.currentX; fixedMenu.menu.top = fixedMenu.currentY 
//				}
//				else { 
//					fixedMenu.menu.style.left = fixedMenu.currentX + "px"; fixedMenu.menu.style.top = fixedMenu.currentY + "px" 
//				} 
//			}
//			fixedMenu.menu.style.right = ""; 
//			fixedMenu.menu.style.bottom = "" 
//		}; 
//		fixedMenu.floatMenu = function() { 
//			fixedMenu.moveMenu(); 
//			setTimeout("fixedMenu.floatMenu()", 20) 
//		};
//		fixedMenu.addEvent = function(a, b, f) { 
//			if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { 
//				a[b + "_num"] = 0; 
//				if(typeof a[b] == "function") {
//					a[b + 0] = a[b]; a[b + "_num"]++ 
//				}
//				a[b] = function(c) { 
//				var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g 
//				} 
//			}
//			for(var e = 0;e < a[b + "_num"];e++)
//				if(a[b + e] == f)
//					return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu.init = function() { if(fixedMenu.supportsFixed())fixedMenu.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu.menu : fixedMenu.menu.style; fixedMenu.targetLeft = parseInt(a.left); fixedMenu.targetTop = parseInt(a.top); fixedMenu.targetRight = parseInt(a.right); fixedMenu.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu.addEvent(window, "onscroll", fixedMenu.moveMenu); fixedMenu.floatMenu() } }; fixedMenu.addEvent(window, "onload", fixedMenu.init); fixedMenu.hide = function() { fixedMenu.menu.style.display = "none"; return false }; fixedMenu.show = function() { fixedMenu.menu.style.display = "block"; return false } }
//</script>

	
}
?>
