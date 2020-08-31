<head>
<title>INSUMOS FACTURADOS Y DEVUELTOS POR CENTRO DE COSTOS</title>
</head>

<script type="text/javascript">

	function addCCO( cmOrigen, cmDestino ){

		if( cmDestino.value.indexOf( "% - Todos" ) > -1  ){
			return;
		}
		
		valor = cmOrigen.options[ cmOrigen.options.selectedIndex ].text;

		if( valor != "% - Todos" ){
		
			pos = cmDestino.value.indexOf( valor );
		
			if( pos == -1 ){

				if( cmDestino.value.length > 0 ){
					cmDestino.value = cmDestino.value+"\r";
				}
			
				cmDestino.value = cmDestino.value+valor;

				if( cmDestino.name == "txGrupos" ){
					gruposChange(0);
				}
			}
		}
		else{
			cmDestino.value = valor;
			if( cmDestino.name == "txGrupos" ){
				gruposChange(0);
			}
		}
	}

	function removeCCO( cmOrigen, cmDestino ){
		
		pos = cmDestino.value.indexOf( cmOrigen.options[ cmOrigen.options.selectedIndex ].text );

		if( pos > -1 )
		{			
			valor = cmDestino.value.substring( 0, pos-1 );
			valor = valor+cmDestino.value.substring( pos+cmOrigen.options[ cmOrigen.options.selectedIndex ].text.length, cmDestino.value.length );

			cmDestino.value=valor;

			if( cmDestino.value.indexOf("\n") == 0 ){
				cmDestino.value = cmDestino.value.substring( 1, cmDestino.value.length );
			}else if( cmDestino.value.indexOf("\n") == 1 ){
				cmDestino.value = cmDestino.value.substring( 2, cmDestino.value.length );
			}
		}

		if( cmDestino.name == "txGrupos" ){
			gruposChange(0);
		}
	}

	function gruposChange( valor ){
		document.mainmenu.Menu.value = valor;
		document.mainmenu.submit();
	}
	
	function cerrarVentana()
	{
    	top.close();		  
    }
</script>

<body>

<?php
include_once("conex.php");
/***************************************************************************
 *
 * Creado por: Edwin Molina Grisales
 * Programa: rep_seraplixccos.php
 * Fecha: 11-05-2009
 * Codigo de requerimientos: 1505
 * Objetivo: Generar un reporte de los registros de aplicacion 
 * 			 facturados del sistema de alta por centro de costos, 
 * 			 articulos, cantidad cargada y devuelta de acuerdo a un rango 
 * 			 de fechas, articulo a mostrar y el centro de costos, tanto
 * 			 origen como destino.
 *
 **************************************************************************/

/***************************************************************************
 *
 * Tablas usadas para el reporte (todos de tipo consulta)
 * 
 * 000002	Encabezado cargos
 * 000003	Detalle cargos
 * 000011	Serivicios o centros de costos
 * 000026	Para hallar las unidades de medida de los productos
 * 000002	De cenpro, para hallar las unidades de medida de los productos
 * 
 * 
 * Variables principales del programa
 *
 * $ccoori:					Centro de costos origen
 * $ccodes:					Centro de costos destino
 * $fechaini				Fecha Inicial
 * $fechafin				Fecha Final
 *
 ***************************************************************************/

/***************************************************************************
 * Actualizacion.
 * Noviembre 7 de 2013: Jonatan Lopez
 * Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 
 * para que traiga los datos de contingencia (tabla movhos_00143) con estado activo.
 ****************************************************************************
 * Julio 11 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones 
 * consultaCentrosCostos que hace la consulta de los centros de costos 
 * de un grupo seleccionado y dibujarSelect que dibuja el select con los 
 * centros de costos obtenidos de la primera funcion. 
  ****************************************************************************
 * 2009-09-08	Edwin Molina Grisales. 
 * 				Se puede consultar adicionalmente por 
 * 				grupos de articulos y varios centros de costos, tanto de 
 * 				origen como de destino, igualmente sucede lo mismo con los
 * 				grupos de articulo y articulos
 ***************************************************************************/

?>

<?php

/****************************************************************************
 * FUNCIONES
 ****************************************************************************/

/**
 * Crea una clausal IN (x) para la consulta principal, en caso de que $valores
 * sea vacio crea un LIKE '%'
 * 
 * @param String $valores	Lista para crear un IN
 * @return String $in		Clausula IN o LIKE
 */
function crearIN( $valores ){
	
	if( empty( $valores ) ){
		$in = "LIKE '%'";
		return $in;
	}
	else{
	
		$in = "IN (";
		$i = 0;

		$ccocodnam = explode( "\r", $valores );
		
		foreach( $ccocodnam as $val ){

			if( !empty($val) ){
				
				if( $val == "% - Todos" ){
					$in = "LIKE '%'";
					return $in;
				}

				$exp = explode(" - ", $val );

				if( $i > 0 ){
					$in .= ",";
				}
				$in .= "'".trim($exp[0])."'";

				$i++;
			}

		}
		$in .= ")";

		return $in;
	}
	
}


/**
 * Determina si un articulo esta en la lista dada. Devuelve -1
 * en caso de que el articulo no se encuentre en la Lista.
 * Devuelve -1 en caso de no encontrar el articulo en la lista,
 * en caso contrario, devuelve la posicion donde se encontro
 * 
 * @param String $artcod			Codigo del Articulo
 * @param Array[][] $Lista			Lista de articulo
 * @return Integer
 */
function enLista( $artcod, $Lista ){

	$pos = -1;
	
	for( $i = 0; $i < count($Lista); $i++ ){
		if ($Lista[ $i ][0] == $artcod ){
			return $i;
		}
	}
	
	return $pos;
}

/**
 * Agrega a la Lista el articulo no encontrado, en caso de encontrarse,
 * suma los valores correspondientes al articulo de la tabla
 *  
 * @param Array[] 	$art		Array con la informacion del articulo			
 * @param Array[] 	$uni		Array con la informacion de las unidades
 * @param Array[][] $Lista		Lista que se va llenando con los articulos
 */
function agregarLista( $art, $uni, &$Lista ){
	
	$cont = count($Lista);
	
	$pos = enLista( $art[0], $Lista );
	
	if( $pos > -1  ){
		$Lista[ $pos ][3] += $art[1];
		$Lista[ $pos ][4] += $art[2];
		$Lista[ $pos ][5] += $art[1]-$art[2];
	}
	else{
		$Lista[ $cont ][0] = $art[0];
		$Lista[ $cont ][1] = $uni[1];
		$Lista[ $cont ][2] = $uni[0];
		$Lista[ $cont ][3] = $art[1];
		$Lista[ $cont ][4] = $art[2];
		$Lista[ $cont ][5] = $art[1]-$art[2];
		$Lista[ $cont ][6] = $uni[2];
	}
}

function pintarResumido( $lista ){
	
	if( empty($lista) ){
		return;
	}
	
	$i = 0;
	
	//Ordena el array $lista por el nombre
	// Obtener una lista de columnas
	foreach ($lista as $llave => $fila) {
	    $nombre[$llave] = $fila[1];
	}
	// Ordenar los datos con volumen descendiente, edicion ascendiente
	// Agregar $datos como el último parámetro, para ordenar por la llave común
	
	//Ordenando el array
	array_multisort ($nombre, SORT_ASC, $lista);
	
	echo "<br><br><p align='CENTER' style='font-size:16'><b>INFORME RESUMIDO</b></p>";
	
	echo "<br><table align=center>
			<tr class='encabezadotabla'>
				<th width=70>Código</th>
				<th width=400>Nombre Comercial</th>
				<th width=400>Nombre Generico</th>
				<th>Unidad de manejo</th>
				<th width=100>Cantidad</th>
				<th width=100>Devueltos</th>
				<th width=100>Total</th>";
	
	
	foreach( $lista as $info ){
		
//		echo "<br>";
		$fila = "fila".(($i%2)+1);
		
		echo "	</tr><tr class='$fila'>
			<td align=center>{$info[0]}</td>
			<td>{$info[1]}</td>
			<td>{$info[6]}</td>
			<td align=center>{$info[2]}</td>
			<td align=right>".number_format($info[3],2,".",",")."</td>
			<td align=right>".number_format($info[4],2,".",",")."</td>
			<td align=right>".number_format($info[5],2,".",",")."</td></tr>
			";
		
		$i++;
	}
	
	echo "</table>";
	
}
/****************************************************************************
 FIN DE FUNCIONES
/***************************************************************************/

include_once("root/comun.php");

if(!isset($_SESSION['user']))
	exit("error");
//else{
	
$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$wactualizacion = "Noviembre 7 de 2013";
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

encabezado("INSUMOS FACTURADOS Y DEVUELTOS POR CENTRO DE COSTOS", $wactualizacion ,"clinica");

//AQUI COMIENZA EL PROGRAMA
//if( !isset($ccoori) || !isset($ccodes) || !isset($fechaini) || !isset($fechafin) )
if( !isset($Menu) || $Menu == 0 )
{
	//Definicion de variables
	if( !isset($txOrigen) ){
		$txOrigen='';
	}
	
	if( !isset($txDestino) ){
		$txDestino = '';
	}
	
	if( !isset($txGrupos) ){
		$txGrupos = '';
	}
	
	if( !isset($txArticulo) ){
		$txArticulo = '';
	}
	
	$q = " SELECT detapl, detval "
		. "   FROM root_000050, root_000051 "
		. "  WHERE empcod = '" . $wemp_pmla . "'"
		. "    AND empest = 'on' "
		. "    AND empcod = detemp ";
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

			if ($row[0] == "tabcco")
				$wtabcco = $row[1];
		}
	}
	else
	{
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	}
	
	if( !isset($fechaini) )
		$fechaini = date("Y-m-01");
	
	if( !isset($fechafin) )
		$fechafin = date("Y-m-t");

	echo "
	<br><br><form action='rep_insfacdevxcco.php?wemp_pmla=01' method='post' name='mainmenu' id='mainmenu'>
	<table align=center>
	<tr class='encabezadotabla' align=center>
			<td width='550'>Centro de costos de origen</td>
	</tr>";
	
	echo "<tr class='fila1'>
			<td><SELECT name='ccoori' style='width:75%'><option>% - Todos</option>";

	
		
	$cco="ccoing<>'on' AND ccourg<>'on' AND ccofac='on'";
	$filtro="--";
		
	$centrosCostos = consultaCentrosCostos($cco, $filtro);
	foreach ($centrosCostos as $centroCostos)
	{
         echo "<option>".$centroCostos->codigo." - ".$centroCostos->nombre."</option>";	
    }		
		//echo "<option>{$rows[0]} - {$rows[1]}</option>";
//		echo "<option value='{$rows[0]}'>{$rows[0]} - {$rows[1]}</option>";*/
   

	echo "</SELECT>";
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( ccoori, txOrigen );'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( ccoori, txOrigen );'>";
	
	echo "<tr><td>
			  <TEXTAREA id='txOrigen' name='txOrigen' style='width:100%;' value='$txOrigen' Rows='3' readonly>$txOrigen</TEXTAREA></td>";
	
	echo "<tr class='encabezadotabla' align=center>
			<td width='550'>Centro de costos de destino</td>
	</tr>";
	
	echo "<tr class='fila1'><td><SELECT name='ccodes' style='width:75%'>";
	echo "			<option value='%'>% - Todos</option>";

	
	$cco="ccofac='on'";
	$filtro="--";
		
	$centrosCostos = consultaCentrosCostos($cco, $filtro);
	foreach ($centrosCostos as $centroCostos)
	{
         echo "<option>".$centroCostos->codigo." - ".$centroCostos->nombre."</option>";	
    }	
		//echo "<option>{$rows[0]} - {$rows[1]}</option>";
//	}

	echo "</SELECT>";
	
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( ccodes, txDestino );'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( ccodes, txDestino );'>";
	
	echo "<tr><td><TEXTAREA id='txDestino' name='txDestino' style='width:100%;' value='$txDestino' Rows='3' readonly>$txDestino</TEXTAREA></td></tr>";
	
	
	
	echo "<tr class='encabezadotabla' align=center>
			<td width='550'>Grupos de Articulos a Buscar</td>
	</tr>";
	
	echo "<tr class='fila1'>
				<td>
					<SELECT name='grupos' style='width:75%'>";
	echo "					<option value='%'>% - Todos</option>";
	
	//Hallando los grupos de articulo			
	$sql = "SELECT 
				artgru
			FROM 
				{$wbasedato}_000026
			GROUP BY 1
			ORDER BY 1 ASC";
	
//				echo $sql;
	$res = mysql_query( $sql, $conex );
	
	
	for( $i = 0; $rows = mysql_fetch_array($res); $i++){
//		echo "<option>{$rows['artcod']}-{$rows['artcom']}</option>";
		echo "<option>{$rows['artgru']}</option>";
	}
	
	echo "</SELECT>";
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( grupos, txGrupos );'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( grupos, txGrupos );'>";
	
	echo "<tr><td><TEXTAREA id='txGrupos' name='txGrupos' style='width:100%;' value='$txGrupos' onChange='javascript: gruposChange( 0 );' Rows='3' readonly>$txGrupos</TEXTAREA></td>";

	echo "<tr class='encabezadotabla' align=center>
			<td width='550'>Articulos a Buscar</td>
	</tr>
	";
		
	echo "<tr class='fila1'><td><SELECT name='articulo' style='width:75%'>";
	echo "					<option value='%'>% - Todos</option>";
	
	//Creando clausula IN para la consulta
	$grupoArticulo=crearIN($txGrupos);
	
	//Hallando los articulo de acuerdo a los grupos
	$sql = "SELECT 
				artcod, artcom
			FROM 
				{$wbasedato}_000026
			WHERE
				artgru $grupoArticulo
			ORDER BY 1 ASC";
	
	$res = mysql_query( $sql, $conex );
	
	for( $i = 0; $rows = mysql_fetch_array($res); $i++){
//		echo "<option>{$rows['artcod']}-{$rows['artcom']}</option>";
		echo "<option>{$rows['artcod']} - {$rows['artcom']}</option>";
	}
	
	echo "</SELECT>";
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( articulo, txArticulo );'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( articulo, txArticulo );'>";
	
	echo "<tr><td><TEXTAREA id='txArticulo' name='txArticulo' style='width:100%;' value='$txArticulo' Rows='3' readonly>$txArticulo</TEXTAREA></td></tr>";
	
	echo "</table>";
	
	//tabla para la eleccion de fechas
	echo "
	<br>
	<table align=center>
		<tr class='encabezadotabla' align=center>
			<td>Fecha inicial
			<td>Fecha final
		<tr class='fila1'>
			<td>";
	campoFechaDefecto( "fechaini", $fechaini );
	echo "		<td>";
	campoFechaDefecto( "fechafin", $fechafin );
	echo "
	</table><br>

	<table align='CENTER'>
		<tr>
			<td><INPUT type='radio' name='opcion' value='1' checked>Detallado</td>
		</tr><tr>
			<td><INPUT type='radio' name='opcion' value='2'>Detallado y Resumido</td>
		</tr><tr>
			<td><INPUT type='radio' name='opcion' value='3'>Resumido</td>
		</tr>
	</table>
	
	<br><table align=center>
		<tr>
			<td><INPUT type='button' value='Generar' style='width:120' onClick='javascript: gruposChange( 1 );'></td>
			<td><INPUT type='button' value='Cerrar Ventana' onClick='cerrarVentana();' style='width:120'></td>
	</table>
	
	<INPUT type='hidden' name='wcenmez' value='$wcenmez'>
	<INPUT type='hidden' name='Menu' value='0'>
	
	</form>";
}
else{
	
	if( !isset($opcion) ){
		$opcion = '1';	
	}
	
	$Lista = array();
	
	//Creando los centros de costos que se van a consultar de origen
	if( !empty($txOrigen) ){
		$ccoOrigenes = crearIN($txOrigen);
	}
	
	
	//Creando los centros de costos que se van a consultar de destino
	if( !empty($txDestino) ){
		$ccoDestinos = crearIn($txDestino);
	}
	
//	if( $articulo == "%" ){
//		$artmostrar = "Todos";
//	}
//	else
//	{
//		$artmostrar = $articulo;
//		$aux = explode("-", $articulo);
//		$articulo = $aux[0]; 
//	}
//	
//	if( $ccodes == "%" ){
//		$destino = "Todos";
//	}
//	else
//	{
//		$destino = $ccodes; 
//	}
	
	//encabezado del informe, fecha inicial y fecha final con que fue generado el reporte
	echo "<form name='main' action='rep_insfacdevxcco.php?wemp_pmla=$wemp_pmla' method='post'>";
	
	echo 
	"<br><br><table align=center>
		<tr>
			<td align='left' class='fila1'>Centro de costos origen</td>
			<td class='fila2'>";
	
	$expccoori = explode("\r", $txOrigen);
	$impresion = "";
	$i = 0;
	foreach( $expccoori as $oriori ){
		if( $i > 0 )
			$impresion .= "<br>";
		
		if( strpos($oriori, "% - Todos" ) === false ){
			if( !empty($oriori) ){
				$impresion .= "$oriori";
				$i++;
			}
		}
		else{
			$impresion = "Todos";
			break;
		}
	}
	
	if( $i == 0){
		echo "Todos";
	}
	else{
		echo $impresion;
	}
	
	echo "	</tr><tr>
			<td align='left' class='fila1'>Centro de costos destino</td>
			<td class='fila2'>";
	
	$expccoori = explode("\r", $txDestino);
	$impresion = "";
	$i = 0;
	foreach( $expccoori as $oriori ){
		if( $i > 0 )
			$impresion .= "<br>";
		
		if( strpos($oriori, "% - Todos" ) === false ){
			if( !empty($oriori) ){
				$impresion .= "$oriori";
				$i++;
			}
		}
		else{
			$impresion = "Todos";
			break;
		}
	}
	
	if( $i == 0){
		echo "Todos";
	}
	else{
		echo $impresion;
	}
	
	echo "</tr><tr>
			<td align='left' class='fila1'>Grupos</td>
			<td class='fila2'>";
	
	$expccoori = explode("\r", $txGrupos);
	$impresion = "";
	$i = 0;
	foreach( $expccoori as $oriori ){
		if( $i > 0 )
			$impresion .= "<br>";
		
		if( strpos($oriori, "% - Todos" ) === false ){
			if( !empty($oriori) ){
				$impresion .= "$oriori";
				$i++;
			}
		}
		else{
			$impresion = "Todos";
			break;
		}
	}
	
	if( $i == 0){
		echo "Todos";
	}
	else{
		echo $impresion;
	}
	
	echo "</tr><tr>
			<td align='left' class='fila1'>Articulo</td>
			<td class='fila2'>";
	
	$expccoori = explode("\r", $txArticulo);
	$impresion = "";
	$i = 0;
	foreach( $expccoori as $oriori ){
		if( $i > 0 )
			$impresion .= "<br>";
		
		if( strpos($oriori, "% - Todos" ) === false ){
			if( !empty($oriori) ){
				$impresion .= "$oriori";
				$i++;
			}
		}
		else{
			$impresion = "Todos";
		}
	}
	
	if( $i == 0){
		echo "Todos";
	}
	else{
		echo $impresion;
	}
	
	echo "</tr><tr>
			<td align='left' class='fila1'>Fecha inicial</td>
			<td class='fila2'>$fechaini</td>
		</tr><tr>
			<td align='left' class='fila1'>Fecha final</td>
			<td class='fila2'>$fechafin</td>
		</tr>
	</table><br>";
	
//	$aux = explode( " - ", $ccoori );
//	$ccoori = $aux[0]; 
//	$aux = explode( " - ", $ccodes );
//	$ccodes = $aux[0];
	
	//Buscando las fuentes de cargos
	$sql = "SELECT
				ccofca
			FROM
				{$wbasedato}_000011
			WHERE
				ccofca <> '' AND
				ccofca <> 'NO APLICA'				
			GROUP BY ccofca";
				
	$res = mysql_query( $sql, $conex );
	
	//generando string con las opciones de cargos que deben estar en la consulta principal
	$cargos ="(";
	for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
		if( $i > 0 )
			$cargos .= ",";
		
		$cargos = $cargos."'$rows[0]'";
	}
	$cargos = $cargos.")";
	
	//Buscando las fuentes de devolucion
	$sql = "SELECT
				ccofde
			FROM
				{$wbasedato}_000011
			WHERE
				ccofde <> '' AND
				ccofde <> 'NO APLICA'				
			GROUP BY ccofde";
				
	$res = mysql_query( $sql, $conex );
	
	//generando string con las opciones de devolucion que deben estar en la consulta principal
	$devolucion ="(";
	for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
		if( $i > 0 )
			$devolucion .= ",";
		
		$devolucion = $devolucion."'$rows[0]'";
	}
	$devolucion = $devolucion.")";
	
	$todosgrupos = false;
	
	$ccoori = crearIN( $txOrigen );
	$ccodes = crearIN( $txDestino );
	$grupos = crearIN( $txGrupos );
	$articulos = crearIN( $txArticulo );

	//Consulta principal
	//Busco la cantidad cargada y devueta facturada por centro de costos y articulo
	/*Detalle del query
	 * Se crea dos tabalas en el FROM. 
	 * - La primera busca la cantidad cargada
	 * - La segunda busca la cantidad devuelta
	 * Se unen para formar una tabla y por ultimo se suma para hallar la cantidad
	 * carga y devuelta
	 * Se agrega "UNION" a la consulta para traiga los datos de contingencia (tabla movhos_00143). Noviembre 07 de 2013 Jonatan Lopez
	 */
	$query = "
	 		SELECT 
	 			fdeart, sum( car ) AS car, sum( dev ) AS dev, fdeser, fencco, artcom
			FROM (
				SELECT * FROM(
					SELECT 
						fdeart, sum( fdecan ) AS car, 0 AS dev, fdeser, fencco, artcom
					FROM 
						{$wbasedato}_000003 a, {$wbasedato}_000002 b, {$wbasedato}_000026 c
					WHERE 
						fencco $ccoori
						AND fdeser $ccodes
						AND fennum = fdenum
						AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
						AND fenfue IN $cargos
						AND fdeser <> ''
						AND fdeart $articulos
						AND c.artcod = fdeart
						AND artgru $grupos
					GROUP BY fdeser, fdeart
					UNION
					SELECT 
						fdeart, sum( fdecan ) AS car, 0 AS dev, fdeser, fencco, artcom
					FROM 
						{$wbasedato}_000143 a, {$wbasedato}_000002 b, {$wbasedato}_000026 c
					WHERE 
						fencco $ccoori
						AND fdeser $ccodes
						AND fennum = fdenum
						AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
						AND fenfue IN $cargos
						AND fdeser <> ''
						AND fdeart $articulos
						AND c.artcod = fdeart
						AND artgru $grupos
						AND Fdeest = 'on'
					GROUP BY fdeser, fdeart
				) as td1
			UNION 
				SELECT* FROM(
					SELECT 
						fdeart, 0 AS car, sum( fdecan ) AS dev, fdeser, fencco, artcom
					FROM 
						{$wbasedato}_000003 a, {$wbasedato}_000002 b, {$wbasedato}_000026 c
					WHERE 
						fencco $ccoori 
						AND fdeser $ccodes
						AND fennum = fdenum
						AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
						AND fenfue IN $devolucion
						AND fdeser <> ''
						AND fdeart $articulos
						AND c.artcod = fdeart
						AND artgru $grupos
					GROUP BY fdeser, fdeart
					UNION
					SELECT 
						fdeart, 0 AS car, sum( fdecan ) AS dev, fdeser, fencco, artcom
					FROM 
						{$wbasedato}_000143 a, {$wbasedato}_000002 b, {$wbasedato}_000026 c
					WHERE 
						fencco $ccoori 
						AND fdeser $ccodes
						AND fennum = fdenum
						AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
						AND fenfue IN $devolucion
						AND fdeser <> ''
						AND fdeart $articulos
						AND c.artcod = fdeart
						AND artgru $grupos
						AND Fdeest = 'on'
					GROUP BY fdeser, fdeart
				) AS td2
			) AS datos
			GROUP BY fdeser, fdeart
			ORDER BY fdeser, artcom, fdeart";
						
	$res = mysql_query($query, $conex) or die("Error en la consulta - $query");
	$numrows = mysql_num_rows($res);
	
	//Tabla con la informaciòn del reporte
	
	$rows = "";

	//Recorrro las filas halladas
	$rows = mysql_fetch_array($res);
	
	$error = true;	//indica si se generan filas
	
	if( $opcion == '1' || $opcion == '2' )
		echo "<br><br><p align='CENTER' style='font-size:16'><b>INFORME DETALLADO</b></p>";
	
	for(  ; $rows;  ){
		$error = false;
		$auxcostos = $rows[3];
		
		
		if( !empty($auxcostos) ){
			$sql = "SELECT
					ccocod, cconom 
				FROM 
				{$wbasedato}_000011
				WHERE 
					ccocod = $auxcostos AND
					ccohos = 'on'";

			$r = mysql_query($sql,$conex);
			$rs = mysql_fetch_array($r);
		}
		else
		{
			$rs[0] = "";
			$rs[1] = "SIN CENTRO DE COSTO";			
		}
		
		if( $opcion == '1' || $opcion == '2' ){
			echo "<br><table align=center>
			<tr>
				<td colspan=4 class=''><b>Centro de Costos: {$rs[0]} - {$rs[1]}</b> 
			<tr class='encabezadotabla'>
				<th width=70>Código</th>
				<th width=400>Nombre Comercial</th>
				<th width=400>Nombre Generico</th>
				<th>Unidad de manejo</th>
				<th width=100>Cargados</th>
				<th width=100>Devueltos</th>
				<th width=100>Cantidad Neta</th>";
		}
		
		for($i = 0; $rows[3] == $auxcostos; $i++){
			$fila = "fila".(($i%2)+1);
			
			//Hallo la unidad de medida
			//Se encuentra el tabla 26, si no esta en esta 
			//se encuentra en la tabla 2 de cenpro
			$query = "SELECT artuni, artcom, artgen 
					 FROM {$wbasedato}_000026
					 WHERE artcod = '{$rows[0]}'";

			$result = mysql_query($query);
			if( mysql_num_rows($result) > 0 ){
				$rowuni = mysql_fetch_array($result);
			}
			else{
				$query = "SELECT artuni, artcom, artgen 
					 	 FROM {$wcenmez}_000002
					 	 WHERE artcod = '{$rows[0]}'";
				
				$result = mysql_query($query);
				$rowuni = mysql_fetch_array($result);
			} 
			
			//Se imprime los resultados de la consulta
			if( $opcion == '1' || $opcion == '2' ){
				echo "	</tr><tr class='$fila'>
					<td align=center>{$rows[0]}</td>
					<td>{$rowuni[1]}</td>
					<td>{$rowuni[2]}</td>
					<td align=center>{$rowuni[0]}</td>
					<td align=right>".number_format($rows[1],2,".",",")."</td>
					<td align=right>".number_format($rows[2],2,".",",")."</td>
					<td align=right>".number_format($rows[1]-$rows[2],2,".",",")."</td></tr>
					";
			}
			
			agregarLista( $rows, $rowuni, $Lista );
			
			$rows = mysql_fetch_array($res);			
		}
		echo "</table>";		
	}
	
	if( $opcion == '2' || $opcion == '3' )
		pintarResumido( $Lista );
	
	if( $error )
		echo "<p align='center'>No hubo resultados para su consulta</p>";
	
	echo "<INPUT type=HIDDEN name=fechaini value='$fechaini'>";
	echo "<INPUT type=HIDDEN name=fechafin value='$fechafin'>";
	echo "<INPUT type=HIDDEN name='txOrigen' value='$txOrigen'>";
	echo "<INPUT type=HIDDEN name='txDestino' value='$txDestino'>";
	echo "<INPUT type=HIDDEN name='txGrupos' value='$txGrupos'>";
	echo "<INPUT type=HIDDEN name='txArticulo' value='$txArticulo'>";
	
	echo "<br><table align=center>
			<tr align=center>
			<td colspan=5>
				<INPUT type='submit' value='Retornar' style='width:100'> | 
				<INPUT type='button' value='Cerrar' onClick='cerrarVentana()' style='width:100'></td>
		</tr>
	</table>
	</form>";
}

?>

</body>
