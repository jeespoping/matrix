<?php
include_once("conex.php");
$hoy = date("Y-m-d");
/**************************************************************************************************
 * 											FUNCIONES
 **************************************************************************************************/

 /**************************************************************************************************
 * CALCULA LA EDAD DE UN PACIENTE TENIENDO LA FECHA DE NACIMIENTO.
 **************************************************************************************************/
function validarExistenciaArticulos( $his, $ing ){
	global $wbasedato;
	global $conex;
	$sql = "SELECT count(*) total
						FROM
						(
							SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, SUM(tcarcan) as can, 0 as dev, 0 as invent,  0 as aprov, Artuni, Tcarconcod, Tcarconnom, Tcarser, Tcarapr
							   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
							  WHERE tcarhis    = '$his'
								AND tcaring    = '$ing'
								AND tcardev   != 'on'
								AND Tcarprocod = artcod
						   GROUP BY 1
							  UNION
							 SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, 0 as can, SUM(tcarcan) as dev, 0 as invent, 0 as aprov, Artuni, Tcarconcod, Tcarconnom, Tcarser, Tcarapr
							   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
							  WHERE tcarhis    = '$his'
								AND tcaring    = '$ing'
								AND tcardev    = 'on'
								AND Tcarprocod = artcod
						   GROUP BY 1
						      UNION
							 SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, 0 as can, 0 as dev, SUM(tcarcan) as invent, 0 as aprov, Artuni, Tcarconcod, Tcarconnom, Tcarser, Tcarapr
							   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
							  WHERE tcarhis    = '$his'
								AND tcaring    = '$ing'
								AND Tcarapr = 'off'
								AND Tcarprocod = artcod
						   GROUP BY 1
						      UNION
							 SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, 0 as can, 0 as dev, 0 as invent, SUM(tcarcan) as aprov, Artuni, Tcarconcod, Tcarconnom, Tcarser, Tcarapr
							   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
							  WHERE tcarhis    = '$his'
								AND tcaring    = '$ing'
								AND Tcarapr = 'on'
								AND Tcarprocod = artcod
						   GROUP BY 1
						) as a";

				$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );
				$row = mysql_fetch_array( $res );
	return( $row[0] );
}

function calcular_edad($fecha){
    $dias = explode( "-", $fecha );
    $dias = mktime(0,0,0,$dias[1],$dias[2],$dias[0]);
    $edad = (int)((time()-$dias)/31556926 );
    return $edad;
}

 /**************************************************************************************************
 * FUNCION QUE CONSTRUYE EL HTML QUE PRESENTA EL DETALLE DE LAS APLICACIONES DE UN ARTICULO PARA LA HISTORIA
 * E INGRESO DE UN PACIENTE, DETALLA LA FECHA Y LA HORA DE CADA APLICACION ASÍ COMO LA RONDA.
 **************************************************************************************************/
function detalleAplicacionesArticulo( $datosTotales, $articulo, $padre, $ronda, $clasePadre ){
	global $usuarios, $hoy, $wuser, $wfecApl;
	if( !isset( $datosTotales["Devoluciones"] ) ){
				$datosTotales["Devoluciones"] = array();
	}
	if( !isset( $datosTotales["Aplicaciones"] ) ){
				$datosTotales["Aplicaciones"] = array();
				$ocultarFecha = true;
	}

	$detalle  = "<div align='center' style='width:80%; height:100%' class='fila2'>";

	foreach ($datosTotales as $keyMovimiento => $datos) {
		$detalle .= "<div align='center' name='div_{$keyMovimiento}' style='width:100%;' class='{$class}'>";
		$detalle .= "<span class='subtituloPagina2'><font size='3'>  <b> {$keyMovimiento} </b> </font></span>";
		$detalle .= "<br><br>";
		if( !isset( $datos[$wfecApl] ) ){
			$sinDatosParaFecha = true;
		}

		//SI HAY DATOS PARA LA FECHA PERO NO EN LA RONDA( NO SE DEBE MOSTRAR LA RONDA PERO SI LA FECHA)
		if( isset( $datos[$wfecApl] ) and (!isset( $datos[$wfecApl][$ronda] ) ) ){
			$sinDatosEnRonda = true;
		}

		if( !isset( $datos[$wfecApl][$ronda] ) ){
			$datos[$wfecApl][$ronda]['cantidad'] = 0;
			$datos[$wfecApl][$ronda]['usuario']  = $wuser;
			$datos[$wfecApl][$ronda]['fechaCreacion']  = $hoy;
			$datos[$wfecApl][$ronda]['id'] = "";
		}

		if( $keyMovimiento == "Aplicaciones" ){
			ksort( $datos, true );
			//SE VERIFICA EL ESTADO ACTUAL DE APLICACIONES PARA LA FECHA Y LA RONDA ACTUALES QUE SE ESTÁN REVISANDO
			//EN CASO DE QUE NO HAYA DATOS O EN LA FECHA O EN LA RONDA ESPECIFICAMENTE SE CONSTRUYEN CON VALORES POR DEFECTO QUE FACILITAN
			//EL MANEJO VISUAL POSTERIOR( APLICACION, MOSTRAR EN PANTALLA EL NUEVO DETALLE, ETC... ).
			$j = 1; //EL UNICO PROPOSITO DE ESTA VARIABLE ES CONTROLAR LA ADICION DE MARGENES A LAS FILAS DE LAS FECHAS.
			$detalle .= "<table width='100%' cellpadding='0' cellspacing='0'>";
			 $detalle .= "<tr class='encabezadotabla'>";
				$detalle .= "<td align='center' style='font-size:13px' width='15%'>FECHA APL.</td>";
				$detalle .= "<td align='center' style='font-size:13px'>";
					 $detalle.= "<table style='width:100%; height:100%;' cellpadding='0' cellspacing='0'>";
					 $detalle.= "<tr class='encabezadotabla'><td align='center' width='10%' style='font-size:11px; border: 1px solid; border-color:#fffff;'>RONDA</td><td align='center' width='5%' style='font-size:11px; border: 1px solid; border-color:#fffff;'>CANT.</td><td align='center' width='5%' style='font-size:11px; border: 1px solid; border-color:#fffff;'>APR.</td><td align='center' width='10%' style='font-size:11px; border: 1px solid; border-color:#fffff;'>ANULAR.</td><td align='center' width='50%' align='center' style='font-size:11px; border: 1px solid; border-color:#fffff; '>USUARIO</td><td align='center' width='30%' style='font-size:11px; border: 1px solid; border-color:#fffff; '>Fecha y hora registro</td></tr>";
					 $detalle.= "</table>";
				$detalle .= "</td>";
			 $detalle .= "</tr>";
			 $i = 0;
			 foreach( $datos as $keyFecha=>$rondas ){

				ksort( $rondas, true );

				( trim($keyFecha) == trim($wfecApl) ) ? $idfecha = " id='fecha_hoy{$padre}' " : $idfecha  = "";
				( $sinDatosParaFecha and ( trim($keyFecha) == trim($wfecApl) ) ) ? $displayFecha = "style='display:none;'" : $displayFecha = "";
				( $j == 1 ) ? $bordeArriba = "border-top: 3px solid;" : $bordeArriba = "";
				 $j++;


				$detalle .= "<tr {$displayFecha} articulo='{$articulo}' hoy='{$keyFecha}' {$idfecha}>";
				$detalle .= "<td align='center' class='botona' width='15%' style='border-bottom: 3px solid; {$bordeArriba} border-color:#FFFFFF;'>{$keyFecha}</td>";
				$detalle .= "<td style='border-bottom: 3px solid; {$bordeArriba} border-color:#FFFFFF;'>";
					$detalle .= "<table style='width:100%; height:100%;' cellpadding='0' cellspacing='0'>";
				foreach( $rondas as $keyRonda=>$datosRonda )
				{
					( isset($usuarios[$datosRonda['usuario']]) ) ? $nombreUsuario = $usuarios[$datosRonda['usuario']] : $nombreUsuario = $usuario[$datosRonda['usuario']] = consultarNombreUsuario( $datosRonda['usuario'] );
					( is_int($i/2) ) ? $wclass='fila1' : $wclass='fila2';

					//EN ESTE FRAGMENTO SE LE PONE ID A LOS CONTENEDORES DE LOS DATOS QUE CORRESPONDEN A LA FECHA DE APLICACION,
					//CON EL FIN DE IDENTIFICARLOS DE MANERA SENCILLA A LA HORA DE ACTUALIZAR EN CASO DE ANULACION.
					( ( trim($keyRonda) == trim($ronda) ) and ( $keyFecha == $wfecApl ) ) ? $id = " id='canApl{$padre}' " : $id= "";
					( ( trim($keyRonda) == trim($ronda) ) and ( $keyFecha == $wfecApl ) ) ? $idapr = " id='aprApl{$padre}' " : $idapr= "";
					( ( trim($keyRonda) == trim($ronda) ) and ( $keyFecha == $wfecApl ) ) ? $idtr = " id='tr_canApl{$padre}' " : $idtr= "";
					( ( trim($keyRonda) == trim($ronda) ) and ( $keyFecha == $wfecApl ) ) ? $wclass1 = "botona" : $wclass1= $wclass;

					//ESTO DEBE CONTROLAR QUE SOLO SE PUEDAN ANULAR LAS APLICACIONES REALIZADAS EL DIA DE HOY( SIN IMPORTAR PARA QUE FECHA SE REALIZÓ ).
					( ( $datosRonda['fechaCreacion'] != $hoy ) or ( trim( $datosRonda['usuario'] ) != trim( $wuser ) ) ) ? $habilitarAnular = " disabled fechaCreacion='{$datosRonda['fechaCreacion']}' " : $habilitarAnular = "";

					if(  $sinDatosEnRonda and ( trim($keyRonda) == trim($ronda) ) and ( $keyFecha == $wfecApl ) ){
						$displayRonda = " style='display:none;' ";
					}else{
							$displayRonda = "";
							$i++;
						}
						$idRegistro = $datosRonda['id'];

					( $datosRonda['aprov'] == "on" ) ? $aprovechamiento = "Si" : $aprovechamiento = "No";
						//SE CONSTRUYE EL EVENTO SI ESTÁ HABILITADO PARA ANULAR
						if( $habilitarAnular == "" ){
							//DEFINO QUE DEBE OCULTARSE EN CASO DE ANULACIÓN.
							( ( trim($keyFecha) == trim($wfecApl) ) and ( trim( $keyRonda ) == trim( $ronda ) ) ) ? $habilitarInput = "si" : $habilitarInput = "no";
							$evento = "onclick='anularAplicacion( \"{$habilitarInput}\", \"{$padre}\", \"{$articulo}\", \"{$keyFecha}\", \"{$keyRonda}\", this, \"{$aprovechamiento}\" )' ";
						}else{
								$evento = "";
							}
						$detalle .= "<tr {$displayRonda} {$idtr}>";
						$detalle .= "<td align='center' class='{$wclass}'  width='10%' height='100%' style='font-size:12px; padding:0;'>{$keyRonda}</td>";
						$detalle .= "<td align='center' class='{$wclass1}' width='5%' height='100%' style='font-size:12px; padding:0;' {$id}>{$datosRonda['cantidad']}</td>";
						$detalle .= "<td align='center' class='{$wclass}' width='5%' height='100%' style='font-size:12px; padding:0;' {$idapr}>{$aprovechamiento}</td>";
						$detalle .= "<td align='center' class='{$wclass}'  width='10%' height='100%' style='font-size:12px; padding:0;'><input {$habilitarAnular} type='checkbox' id='chk_anular_{$articulo}_{$keyFecha}_{$keyRonda}' registro='{$idRegistro}' cantidad='{$datosRonda['cantidad']}' {$evento}></td>";
						$detalle .= "<td align='center' class='{$wclass}'  width='50%' height='100%' style='font-size:12px; padding:0;'>({$datosRonda['usuario']}) {$nombreUsuario}</td>";
						$detalle .= "<td align='center' class='{$wclass}'  width='25%' height='100%' style='font-size:12px; padding:0;'>({$datosRonda['fechaCreacion']}) {$datosRonda['horaCreacion']}</td>";
						$detalle .= "</tr>";
				}
					$detalle .= "</table>";
				$detalle .= "</td>";
				$detalle .= "</tr>";
			 }
			 $detalle .= "</table>";
			 $detalle .= "</div>";
		}else{
				$sinDatosParaFecha = false;
				if( !isset( $datosTotales["Devoluciones"][$hoy] ) ){
					$datosTotales["Devoluciones"][$hoy] = array();
					$sinDatosParaFecha = true;
				}
				ksort( $datosTotales[$keyMovimiento], true );

				$detalle .= "<table style='width:100%; height:100%;' cellpadding='0' cellspacing='0' >";
				$detalle .= "<tr class='encabezadotabla'>";
				$detalle .= "<td align='center' style='font-size:13px' width='20%'>FECHA</td>";
				$detalle .= "<td align='center' style='font-size:13px'>";
					 $detalle.= "<table style='width:100%; height:100%;' cellpadding='0' cellspacing='0'>";
					 $detalle.= "<tr class='encabezadotabla'><td align='center' width='25%' style='font-size:13px; border: 1px solid; border-color:#fffff;'>{$tituloRondaHora}</td><td align='center' width='15%' style='font-size:11px; border: 1px solid; border-color:#fffff;'>CANT.</td><td align='center' width='60%' style='font-size:11px; border: 1px solid; border-color:#fffff; '>USUARIO</td></tr>";
					 $detalle.= "</table>";
				$detalle .= "</td>";
				$detalle .= "</tr>";
				foreach( $datosTotales[$keyMovimiento] as $keyFecha=>$horas ){
					( $j == 1 ) ? $bordeArriba = "border-top: 3px solid;" : $bordeArriba = "";
					( $keyFecha == $hoy ) ? $idTablaHoy = " id='tabla_{$padre}' " : $idTablaHoy = "";
					( ( $keyFecha == $hoy ) and $sinDatosParaFecha ) ? $displayFecha = " style='display:none;' " : $displayFecha = "";
					$j++;
					$detalle .= "<tr {$displayFecha}>";
					$detalle .= "<td align='center' class='botona' style='border-bottom: 3px solid; {$bordeArriba} border-color:#FFFFFF;'>{$keyFecha}</td>";
					$detalle .= "<td style='border-bottom: 3px solid; {$bordeArriba} border-color:#FFFFFF;'>";

					$detalle .= "<table {$idTablaHoy} style='width:100%; height:100%;' cellpadding='0' cellspacing='0'>";

					foreach( $horas as $keyHora=>$datos){
						( isset($usuarios[$datos['usuario']]) ) ? $nombreUsuario = $usuarios[$datos['usuario']] : $nombreUsuario = $usuario[$datos['usuario']] = consultarNombreUsuario( $datos['usuario'] );
						( is_int($i/2) ) ? $wclass='fila1' : $wclass='fila2';
						$detalle .= "<tr>";
						$detalle .= "<td align='center' class='{$wclass}'  width='25%' height='100%' style='font-size:12px; padding:0;'>{$keyHora}</td>";
						$detalle .= "<td align='center' class='{$wclass}'  width='15%' height='100%' style='font-size:12px; padding:0;'>{$datos['cantidad']}</td>";
						$detalle .= "<td align='center' class='{$wclass}'  width='60%' height='100%' style='font-size:12px; padding:0;'>({$datos['usuario']}) {$nombreUsuario}</td>";
						$detalle .= "</tr>";
						$i++;
					}
					$detalle .= "</table>";
					$detalle .= "</td>";
					$detalle .= "</tr>";
				}
				$detalle .= "</table>";
				$detalle .= "</div>";
		}
	}
	$detalle .= "</div>";
	return( $detalle );
 }

 /*CONSULTA EL NOMBRE DEL USUARIO QUE HIZO LAS APLICACIONES*/

function consultarNombreUsuario( $codigo ){
	global $conex;
	$nombre = "";
	$query  = "SELECT descripcion
				 FROM usuarios
			    WHERE codigo = '{$codigo}'";
	$rs     = mysql_query( $query, $conex );
	$row    = mysql_fetch_array( $rs );
	return( $row['descripcion'] );
 }

function tooltipAplicaciones( ){
	$title  = "<div align='center'>";
		$title .= "<span style='font-size:10px;' class='subtituloPagina2' > CLICK PARA VER DETALLE </span>";
	$title .= "</div>";
	return( $title );
}

 /************************************************************************************************
 * Crea un array de datos que hace los siguiente.
 *
 * Toma todas las variables enviadas por Post, y las convierte en un array
 *
 * Explicacion:
 * En el formulario HTML, toda variable que se quiera mandar por POST y que sirva para un proceso ajax
 * su nombre comienza con dat_. Ahora si, una variable comienza con dat_Enc, quiere decir que se va a crear
 * un encabezado de protocolo y si por el contrario comienza con dat_Det, quiere decir que se va a grabar un
 * detalle del protocolo. Estas variables son sensitivas a mayusculas y minisculas.
 * El array que se crea es el requerido para que pueda ser procesado por la funcion crearStringInsert.
 ************************************************************************************************/

function crearArrayDatos( $wbasedato ){

	$val = Array();

	$crearDatosExtras = false;

	foreach( $_POST as $keyPost => $valuePost ){

		switch( substr( $keyPost, 0,7 ) ){

			case 'dat_Det':
				if( substr( $keyPost, 7, 3 ) != 'id' ){
					$val[ 'Apl'.substr( $keyPost, 7,3 ) ] = utf8_decode( $valuePost );
					$crearDatosExtras = true;
				}
				else{
					$val[ substr( $keyPost, 7, 3 ) ] = utf8_decode( $valuePost );
					$crearDatosExtras = true;
				}
			break;

			default: break;
		}
	}

	if( $crearDatosExtras ){
		$val[ 'Medico' ] = $wbasedato;
		$val[ 'Fecha_data' ] = date( "Y-m-d" );
		$val[ 'Hora_data' ] = date( "H:i:s" );
		$val[ 'Seguridad' ] = "C-$wbasedato";
	}

	return $val;
}


/***************************************************************************************
 * inserta los datos a la tabla
 *
 * $datos	Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla 	Nombre de la tabla a la que se va a insertar los datos
 ***************************************************************************************/
function crearStringInsert( $tabla, $datos ){

	$stPartInsert = "";
	$stPartValues = "";

	foreach( $datos as $keyDatos => $valueDatos ){

		$stPartInsert .= ",$keyDatos";
		$stPartValues .= ",'$valueDatos'";
	}

	$stPartInsert = "INSERT INTO $tabla(".substr( $stPartInsert, 1 ).")";	//quito la coma inicial
	$stPartValues = " VALUES (".substr( $stPartValues, 1 ).")";

	return $stPartInsert.$stPartValues;
}

/***************************************************************************************
 * Crea un string que corresponde a un UPDATE valido
 *
 * $datos	Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla 	Nombre de la tabla a la que se va a insertar los datos
 ***************************************************************************************/
function crearStringUpdate( $tabla, $datos ){

	$stPartInsert = "";
	$stPartValues = "";

	//campos que no se actualizan
	$prohibidos[ "Medico" ] = true;
	$prohibidos[ "Fecha_data" ] = true;
	$prohibidos[ "Hora_data" ] = true;
	$prohibidos[ "Seguridad" ] = true;
	$prohibidos[ "id" ] = true;

	foreach( $datos as $keyDatos => $valueDatos ){

		if( !isset( $prohibidos[ $keyDatos ] ) ){
			$stPartInsert .= ",$keyDatos = '$valueDatos' ";
		}
	}

	$stPartInsert = "UPDATE $tabla SET ".substr( $stPartInsert, 1 );	//quito la coma inicial
	$stPartValues = " WHERE id = '{$datos[ 'id' ]}'";

	return $stPartInsert.$stPartValues;

	//UPDATE  `matrix`.`movhos_000138` SET  `Dprest` =  'off' WHERE  `movhos_000138`.`id` =82;
}

function consultarCantidadAplicada( $his, $ing, $art, $ronda = '%' ){

	global $conex;
	global $wbasedato;
	global $wuser;
	global $arregloDetalle;
	global $hoy;
	global $wfecApl;
	global $aplsXtipo;

	$val = 0;

	$sql = "SELECT Aplfec fechaAplicacion, Aplart articulo, Aplron ronda, Aplcan as can, Aplusu usuario, Fecha_data fechaCreacion, id, Aplapr aprov, Hora_data horaCreacion
			FROM {$wbasedato}_000146 c
			WHERE aplhis = '$his'
			  AND apling = '$ing'
			  AND aplart = '$art'
			  AND aplest = 'on'
			  AND aplron LIKE '$ronda'
			ORDER BY FechaAplicacion, ronda";

	/*if( $art == "H010145"){
		echo $sql;
	}*/
	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );
	$num = mysql_num_rows( $res );

	while( $rows = mysql_fetch_array($res) ){
		$val += $rows[ 'can' ];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['cantidad']      = $rows['can'];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['usuario']       = $rows['usuario'];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['fechaCreacion'] = $rows['fechaCreacion'];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['horaCreacion']  = $rows['horaCreacion'];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['id']            = $rows['id'];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['aprov']         = $rows['aprov'];

		if( $rows['aprov'] == "on" ){
			$aplsXtipo['aprov'][$art] += $rows['can'];
		}else{
			$aplsXtipo['invent'][$art] += $rows['can'];
		}
	}
	return $val;
}

function consultarCantidadDevuelta( $his, $ing, $art, $cantidad )
{
	global $conex;
	global $wbasedato;
	global $wuser;
	global $arregloDetalle;
	global $wfecha;
	if( $cantidad*1 > 0 )
	{
		$query = "SELECT Fecha_data fechaDevolucion, Hora_data horaDevolucion, Tcarcan can, Tcarusu usuario, id
					FROM {$wbasedato}_000106
				   WHERE tcarhis   ='{$his}'
					AND tcaring    ='{$ing}'
					AND tcarprocod ='{$art}'
					AND tcardev    ='on'
				  ORDER BY id";
		$rs = mysql_query( $query, $conex );

		while( $rows = mysql_fetch_array($rs) ){
			$arregloDetalle[$art]['Devoluciones'][$rows['fechaDevolucion']][$rows['horaDevolucion']]['cantidad'] = $rows['can'];
			$arregloDetalle[$art]['Devoluciones'][$rows['fechaDevolucion']][$rows['horaDevolucion']]['usuario']  = $rows['usuario'];
			$arregloDetalle[$art]['Devoluciones'][$rows['fechaDevolucion']][$rows['horaDevolucion']]['fechaCreacion']  = $rows['fechaDevolucion'];
			$arregloDetalle[$art]['Devoluciones'][$rows['fechaDevolucion']][$rows['horaDevolucion']]['id']  = $rows['id'];
		}

	}else{
			$arregloDetalle['Devoluciones'][$art][$rows['fechaDevolucion']]['cantidad'] = 0;
			$arregloDetalle['Devoluciones'][$art][$rows['fechaDevolucion']]['usuario']  = $wuser;
			$arregloDetalle['Devoluciones'][$art][$rows['fechaDevolucion']]['fechaCreacion']  = $wfecha;
			$arregloDetalle['Devoluciones'][$art][$rows['fechaDevolucion']]['id']  = "";
		 }
}

function aplicarMedicamentos(){

	global $conex;
	global $wbasedato;
	global $wuser;
	global $wfecApl;
	global $waplapr;

	//Array de respuesta
	$resp = Array( "mensaje" => '', "data" => '' );

	$datos = crearArrayDatos( $wbasedato );
	$datos[ 'Aplusu' ] = $wuser;
	$datos[ 'Aplest' ] = "on";
	$resp["data"]      = array();


	/****************************************************************
	 * Debo validar
	 * 1 - Que halla saldo suficiente
	 * 2 - Que no halla la misma fecha y hora de aplicación
	 *	   de ser así es acutalización
	 * 3 - Que la cantidad aplicar sea mayor a 0
	 * 4 - Que se halla seleccionado ronda
	 ****************************************************************/

	if( !empty( $datos[ 'Aplcan' ] ) && !empty( $datos[ 'Aplron' ] ) ){

		//Consulto todo lo que tiene cargado el paciente
		//Se quita el filtro de aprovechamiento para que muestre los articulos que lo son. (Jonatan 08 Agosto 2013)
		$sql = " SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, SUM(tcarcan) as can, Artuni
				   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
				  WHERE tcarhis    = '{$datos[ 'Aplhis' ]}'
					AND tcaring    = '{$datos[ 'Apling' ]}'
					AND tcardev   != 'on'
					AND Tcarprocod = artcod
					AND artcod     = '{$datos[ 'Aplart' ]}'
			   GROUP BY 1 ";

		//Se quita el filtro de aprovechamiento para que muestre los articulos que lo son. (Jonatan 08 Agosto 2013)
		$sql = "SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, SUM( can - dev ) as can, Artuni, Tcarconcod, Tcarconnom, Tcarser
					FROM
					(
						SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, SUM(tcarcan) as can, 0 as dev, Artuni, Tcarconcod, Tcarconnom, Tcarser
						   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
						  WHERE tcarhis    = '{$datos[ 'Aplhis' ]}'
							AND tcaring    = '{$datos[ 'Apling' ]}'
							AND tcardev   != 'on'
							AND Tcarprocod = artcod
							AND artcod     = '{$datos[ 'Aplart' ]}'
					   GROUP BY 1
						  UNION
						 SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, 0 as can, SUM(tcarcan) as dev, Artuni, Tcarconcod, Tcarconnom, Tcarser
						   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
						  WHERE tcarhis    = '{$datos[ 'Aplhis' ]}'
							AND tcaring    = '{$datos[ 'Apling' ]}'
							AND tcardev    = 'on'
							AND Tcarprocod = artcod
							AND artcod     = '{$datos[ 'Aplart' ]}'
					   GROUP BY 1
				    ) as a
					GROUP BY 1
					";

		$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			$rows = mysql_fetch_array( $res );

			$canAplicada = consultarCantidadAplicada( $datos[ 'Aplhis' ], $datos[ 'Apling' ], $datos[ 'Aplart' ] );

			if( $rows[ 'can' ] > 0 && $rows[ 'can' ] - $canAplicada > 0 ){

				if( $rows[ 'can' ] - $canAplicada >= $datos[ 'Aplcan' ] ){

					//Busco que no halla aplicación para la fecha y ronda seleccionada
					/*$sql = "SELECT
								id
							FROM
								{$wbasedato}_000146 a
							WHERE
								Aplhis = '{$datos[ 'Aplhis' ]}'
								AND Apling = '{$datos[ 'Apling' ]}'
								AND Aplron = '{$datos[ 'Aplron' ]}'
								AND Fecha_data = '".date( "Y-m-d" )."'
							";*/
					$sql = "SELECT
								id
							FROM
								{$wbasedato}_000146 a
							WHERE
								Aplhis = '{$datos[ 'Aplhis' ]}'
								AND Apling = '{$datos[ 'Apling' ]}'
								AND Aplron = '{$datos[ 'Aplron' ]}'
								AND Aplfec = '{$wfecApl}'
								AND Aplest = 'on'
							";
					$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );
					$num = mysql_num_rows( $res );
					if( true || $num == 0 ){ //Lo dejo siempre insertando datos
						//Si no se encuentra nada es que se va a insertar el registro
						$sql = crearStringInsert( $wbasedato."_000146", $datos );
					}
					else{
						//Si encuentra una aplicación para la ronda seleccionada entonces es una actualización
						$rows = mysql_fetch_array( $res );

						//Creo el campo id que se requiere para completar el update
						$datos[ 'id' ] = $rows[ 'id' ];

						$sql = crearStringUpdate( $wbasedato."_000146", $datos );
					}
					mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

					if( mysql_affected_rows() > 0 ){
						$resp[ "data" ][ "canApl" ] = consultarCantidadAplicada( $datos[ 'Aplhis' ], $datos[ 'Apling' ], $datos[ 'Aplart' ] );
					}
					if( $idInsertado = mysql_insert_id() )
					{
						$resp[ "data" ][ "idRegistro" ] = $idInsertado;
						$resp[ "data" ][ "articulo" ]   = $datos[ 'Aplart' ];
						$resp[ "data" ][ "ronda" ] 		= $datos[ 'Aplron' ];
						$resp[ "data" ][ "fechaApl" ]	= $wfecApl;
						$resp[ "data" ][ "cantidad" ]	= $datos[ 'Aplcan' ];
					}
				}
				else{
					$resp[ "mensaje" ] = "La cantidad a aplicar es mayor al saldo";
				}
			}
			else{
				$resp[ "mensaje" ] = "No hay saldo suficiente";
			}
		}
		else{
			$resp[ "mensaje" ] = "No hay saldo suficiente";
		}
	}
	else{
		$resp[ "mensaje" ] = "Falta seleccionar cantidad o ronda de aplicacion para el articulo {$rows[ 'Aplart' ]}";
	}

	return $resp;
}


function anularAplicacion( $wemp_pmla, $codArt, $fecApl, $ronApl, $wid ){
	global $conex;
	global $wbasedato;
	global $wuser;

	/*$query = "UPDATE {$wbasedato}_000146
				 SET Aplest = 'off'
			   WHERE Aplhis = '{$whis}'
			     AND Apling = '{$wing}'
				 AND Aplart = '{$codArt}'
				 AND Aplfec = '{$fecApl}'
				 AND Aplart = '{$ronApl}'";*/
	$query = "UPDATE {$wbasedato}_000146
				 SET Aplest = 'off'
			   WHERE     id = '{$wid}'";
	$rs	   = mysql_query( $query, $conex );
	$afect = mysql_affected_rows();
	return( $afect );
}
/**************************************************************************************************
 * FIN DE FUNCIONES
 **************************************************************************************************/


include_once( "root/comun.php" );



session_start();

//Codigo de usuario que ingreso al sistema
if( strpos($user, "-") > 0 )
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion" );

$wactualiz = "2013-08-30";	//Fecha de ultima modificación

//Valida codigo de usuario en sesion si no esta registrado el sistema termina la ejecucion
if( !isset($_SESSION['user']) )
{
	terminarEjecucion("usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.");
}
else{

	if( $consultaAjax ){	//si hay ajax

		switch( $consultaAjax ){

			case 10:
				$resp = aplicarMedicamentos();

				echo json_encode( $resp );
			break;

			case 11:
			break;

			case "anularAplicacion":
				$afectados = anularAplicacion( $wemp_pmla, $codArt, $fecApl, $ronApl, $wid );
				$data  = array( "afectados"=>$afectados );
				echo json_encode( $data );
			break;

		}
	}
	else{

?>
<html>
<head>
	<title>APLICACION DE MEDICAMENTOS</title>
<style>
	.inputHabilitado{
		background-color: FAEF90;
	}

	.inputDesHabilitado{
		background-color:ffffff;
	}

	.botona{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			margin-left: 1%;
		 }

	#tooltip{
			color: #2A5DB0;
			font-family: Arial,Helvetica,sans-serif;
			position:absolute;
			z-index:3000;
			border:1px solid #2A5DB0;
			background-color:#C3D9FF;
			padding:0px;
			opacity:1;}
	#tooltip div{margin:0; width:120px; height:15px; class:fila1}
</style>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
	<script>
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);

	/******************************************************************
	 * AJAX
	 ******************************************************************/

	/******************************************************************
	 * Realiza una llamada ajax a una pagina
	 *
	 * met:		Medtodo Post o Get
	 * pag:		Página a la que se realizará la llamada
	 * param:	Parametros de la consulta
	 * as:		Asincronro? true para asincrono, false para sincrono
	 * fn:		Función de retorno del Ajax, no requerido si el ajax es sincrono
	 *
	 * Nota:
	 * - Si la llamada es GET las opciones deben ir con la pagina.
	 * - Si el ajax es sincrono la funcion retorna la respuesta ajax (responseText)
	 * - La funcion fn recibe un parametro, el cual es el objeto ajax
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
			this.ajax.send(this.parametros);

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
	/************************************************************************/

	/************************************************************************************************
	 * Esta funcion crea una url de la siguiente forma:
	 *
	 * campoName1=campoValue1&campoName1=campoValue1&campoName1=campoValue1...
	 *
	 * donde campoNameN es el nombre del campo y campoValueN es valor de un campo
	 *
	 * Cada campo se encuentra en contenedor, este puede ser un div, form, td, tr, table, etc...
	 * En general un campo que puede contener entre sus etiquetas otros elementos html
	 ************************************************************************************************/
	function cearUrlPorCampos( contenedor ){

		var url;

		try{
			var url = "";

			var tagBuscar = new Array( "INPUT", "TEXTAREA", "SELECT" );	//Array con los tags que se quieren buscar

			for( var j = 0; j < tagBuscar.length; j++ ){

				var elementos = contenedor.getElementsByTagName( tagBuscar[j] );

				if( elementos ){

					for( var i = 0; i < elementos.length; i++ ){

						if( elementos[i].name != '' ){

							switch( elementos[i].type.toLowerCase() ){

								case 'checkbox':
									if( elementos[i].checked == true ){
										url += "&"+elementos[i].name + "=on";
									}
									else{
										url += "&"+elementos[i].name + "=off";
									}
									break;

								case 'radio':
									if( elementos[i].checked == true ){
										url += "&"+elementos[i].name + "=" + elementos[i].value;
									}
									break;

								default: url += "&"+elementos[i].name + "=" + elementos[i].value;
									break;
							}
						}
					}
				}
			}

			return url.substr( 1 );	//le quito el & inicial
		}
		catch(e){
			alerta( "Error: " + e );
			return false;
		}
	}

	//Ejecuta el ajax para guardar las aplicaciones
	function aplicarMeds(){

		var tbDatos = document.getElementById( "cargosPaciente" );

		var faltanDatos   = true;
		var errorDeSaldos = false;

		if( tbDatos ){

			contador = 0;

			$.blockUI({ message: $( "#msg" )});

			for( var i = 1 ; i < tbDatos.rows.length ; i++ ){

				var doAjax = true;
				if( $(tbDatos.rows[ i ]).attr("detalle") == "false" )
				{
					$( "input,select", tbDatos.rows[ i ] ).each(
						function( x ){
							if( $( this ).val() == '' ){
								doAjax = false;
							}
						}
					);

					var filaAct = tbDatos.rows[ i ];

					if( doAjax ){
						faltanDatos   = false;
						valido 		  = validarValores(filaAct );
						errorDeSaldos = !valido;
						if( valido ){
							aplicarArticulo( filaAct );
						}else{
							intermitenciaFilaErronea( filaAct );
							return false;

						}
					}
				}
			}

			if(faltanDatos && !errorDeSaldos ){
				alerta( "Debe seleccionar por lo menos una ronda y la cantidad a aplicar para un articulo" );
				$.unblockUI();
			}
		}
	}

	//recibe la respuesta ajax de la aplicacion, y modifica en pantalla los valores para que reflejen dicho movimiento
	function aplicarArticulo( filaAct ){

		//Creo la url para el ajax
		var params = cearUrlPorCampos( filaAct );

		//adiciono los datos extras
		params = params+"&wemp_pmla="+vwemp_pmla.value+"&consultaAjax=10"+"&wfecApl="+vwfecApl.value;
		contador++;	//Contador global para que indique cuantas peticiones ajax se realizan

		consultasAjax( "POST",
					   "./aplicacionesCargos.php",
					   params,
					   true,
					   function( ajax ){
							if( ajax.readyState==4 && ajax.status==200 ){

								if( ajax.responseText != '' ){

									eval( "var datos = "+ajax.responseText );

									if( datos.mensaje != '' ){
										alerta( datos.mensaje );
										$.unblockUI();
									}
									else{
											aux = jQuery(filaAct);
											fila = aux.attr("fila");
											$( "#canApl"+fila+"").html( $( "#canApl"+fila+"").html()*1 + $( "input[type='text']", filaAct ).val()*1 );
											checkAprov = aux.find( "input[type='checkbox']" );
											if( $( checkAprov ).is(":checked") ){
											    aplicoAprovechamiento = "Si";
											}else{
												aplicoAprovechamiento = "No";
											}
											$( "#aprApl"+fila+"").html( aplicoAprovechamiento );

											$( "#fecha_hoy"+fila+"").show();
											$( "#tr_canApl"+fila+"").show();

											$( "[name='canAplTotal']", filaAct ).html( datos.data.canApl );

											$( "[name='canSaldo']", filaAct ).html($( "[name='canSaldo']", filaAct ).html()*1 - $( "input[type='text']", filaAct ).val()*1 );

											if( $( "[name='canSaldo']", filaAct ).html()*1 - $( "[name='canAplTotal']", filaAct ).html()*1 < 0 ){

												$( "input[type='text']", filaAct ).attr( "disabled", true );
												$( "input[type='text']", filaAct).removeClass( "inputHabilitado");
												$( "input[type='text']", filaAct).addClass( "inputDesHabilitado");
											}

											//actualización de saldos
											checkAprov = aux.find( "input[type='checkbox']" );
											if( $( checkAprov ).is(":checked") ){
												$( "[name='canSaldoApr']", filaAct ).html($( "[name='canSaldoApr']", filaAct ).html()*1 - $( "input[type='text']", filaAct ).val()*1 );
											}else{
												$( "[name='canSaldoInv']", filaAct ).html($( "[name='canSaldoInv']", filaAct ).html()*1 - $( "input[type='text']", filaAct ).val()*1 );
											}


											$( "input[type='text']", filaAct ).val( '' );
											$( "input[type='text']", filaAct ).attr( "disabled", true );
											$( "input[type='text']", filaAct).removeClass( "inputHabilitado");
											$( "input[type='text']", filaAct).addClass( "inputDesHabilitado");
											$( checkAprov ).attr( "disabled", true );
											$( "#chk_anular_"+datos.data.articulo+"_"+datos.data.fechaApl+"_"+datos.data.ronda ).attr( "registro", datos.data.idRegistro );
											$( "#chk_anular_"+datos.data.articulo+"_"+datos.data.fechaApl+"_"+datos.data.ronda ).attr( "cantidad", datos.data.cantidad );

											contador--;

											if( contador == 0 ){
												$.unblockUI();
											}
									}
								}
							}
					   }
		);

	}

	//no se está usando
	function cambiarRonda( campo ){

		$( "[name='"+campo.name+"']" ).val( campo.value );
	}

	// mostrar/ocultar el detalle de las aplicaciones de un articulo.
	function mostrarDetalle( td ){

		control   = jQuery( td );
		mostrando = control.attr( "mostrandoDetalle" );
		valor     = $.trim( control.html() );
		if( valor*1 == 0 ){
			alerta( "Sin aplicacionesActuales" );
			return;
		}
		if( mostrando == "false" )
		{
			padre   = control.parent();
			detalle = $( padre ).next( "tr" );
			$( detalle ).show();
			control.attr( "mostrandoDetalle", "true" );
		}else
			{
				padre   = control.parent();
				detalle = $( padre ).next( "tr" );
				$( detalle ).hide();
				control.attr( "mostrandoDetalle", "false" );
			}
	}

	//funcion que mediante ajax realiza la anulación de una aplicacion, ademas hace los cambios en pantalla que reflejan dicho movimiento
	function anularAplicacion( habilitarInput, padre, articulo, fechaAplicacion, ronda, checkbox, aplicoAprov ){

		wid      = $(checkbox).attr( "registro" );
		cantidad = $(checkbox).attr( "cantidad" );
		if( !confirm(" ¿Está seguro de querer anular está aplicación? ") ){
			$(checkbox).removeAttr( "checked" );
			return;
		}
		var tr_fecha;
		var ronda;
		var padreRonda;
		var tabla;
		var tablaPadre;
		$.ajax({
				url: "aplicacionesCargos.php",
				type: "POST",
				data:{

					 consultaAjax: "anularAplicacion",
						wemp_pmla: vwemp_pmla.value,
						   codArt: articulo,
						   fecApl: fechaAplicacion,
						   ronApl: ronda,
							  wid: wid
					 },
				success: function ( data ){
						if(data.afectados*1 > 0)
						{
							saldoAnterior = $( "#saldo_"+padre ).html()*1;
							//ACTUALIZACION DE DATOS EN PANTALLA
							$( "#saldo_"+padre ).html( $( "#saldo_"+padre ).html()*1 + cantidad*1 );
							$( "#Aplicaciones"+padre ).html( $( "#Aplicaciones"+padre ).html()*1 - cantidad*1 );
							aprovechamientoAuxiliar = $(checkbox).parent().prev( "td" ).html();
							if( $.trim(aplicoAprov)=="Si" || $.trim(aprovechamientoAuxiliar)=="Si" ){
									$( "#saldo_Aprov"+padre ).html($( "#saldo_Aprov"+padre ).html()*1 + cantidad*1 );
							}else{
									$( "#saldo_Inven"+padre ).html($( "#saldo_Inven"+padre ).html()*1 + cantidad*1 );
							}

							//SE REESTABLECEN LOS ESTADOS OCULTOS DEL DETALLE
							$(checkbox).removeAttr( "checked" );
							$(checkbox).parent().prev( "td" ).prev( "td" ).html("0");

							ronda      = $(checkbox).parent().parent(); //oculta el tr de la ronda en la fecha
							$(ronda).hide();
							padreRonda = $(ronda).parent();
							filasVisiblesPadreRonda = $(padreRonda).children(":visible");
							//SI DESPUES DE OCULTA LA RONDA NO QUEDA NADA VISIBLE DEBE OCULTARSE TAMBIEN LA FECHA.
							if( filasVisiblesPadreRonda.length == 0 ){
								tr_fecha = $( "tr [articulo='"+articulo+"'][hoy='"+fechaAplicacion+"']" ); //oculta el tr de la ronda en la fecha
								tablaPadre = ( $(tr_fecha).parent() );
								filasVisibles = $(tablaPadre).children(":visible");
								$(tr_fecha).hide();
							}
							filasVisibles = $(tablaPadre).children(":visible");
							//SI SE OCULTA LA FECHA Y NO QUEDAN FECHAS VISIBLES DEBE CONTRAERSE EL TR QUE CONTIENE EL DETALLE CORRESPONDIENTE
							if( filasVisibles.length == 1 ){
								tabla = $( "#detalle_"+padre );
								$( "#Aplicaciones"+padre ).attr( "mostrandoDetalle", "false" );
								$(tabla).hide();
							}

							//EN CASO DE QUE EL SALDO SEA MAYOR A CERO Y O SE ESTÉ DEVOLVIENDO UNA APLICACION PARA LA FECHA Y RONDA
							//QUE SE ESTÁ REVISANDO ACTUALMENTE SE DEBE REHABILITAR EL INPUT QUE RECIBE EL VALOR A APLICAR
							if( habilitarInput == "si" || saldoAnterior == 0 )
							{
								$( "#dat_Detcan_"+padre ).removeAttr( "disabled" );
								$( "#dat_Detcan_"+padre ).removeClass( "inputDesHabilitado" );
								$( "#dat_Detcan_"+padre ).addClass( "inputHabilitado" );
								$( "#dat_Detapr"+padre ).attr( "disabled", false );
								$( "#dat_Detapr"+padre ).attr( "checked", false );

							}
						}
					 },
				dataType: "json"
		});
	}

	window.onload = function(){
			var fechaActual = $("#wfecApl").val();
			vwemp_pmla = document.getElementById( "wemp_pmla" );
			vwfecApl   = document.getElementById( "wfecApl" );

			$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50});

			$(".solofloat").keyup(function(){
			if ($(this).val() !="")
				{
					$(this).val($(this).val().replace(/^(0)|[^0-9]/g, ""));
				}
			});

			$(".solofloat").focusout(function(){
				var input      = jQuery(this);
				var filaActual     = input.parent().parent();

				setTimeout(function(){
					validarValores( filaActual );
				}, 500);
			});

			//Manejo del calendario.

			$( "#wfec" ).keyup(function(){//evita que se escriba la fecha manualmente para evitar errores
				fechaActual = $("#wfecApl").val()
				$( "#wfec" ).val( fechaActual );
				return;
			});

			try{ //inicializa el dataPicker asegurando que solo se pueda seleccionar una fecha mayor o igual a la fecha de ingreso del paciente
					var fechaIngreso  = $("#fechaIngreso");
					formatoFecha = fechaIngreso.val().split("-");
					$( "#wfec" ).datepicker( {
									 showOn: "button",
								buttonImage: "../../images/medical/root/calendar.gif",
							buttonImageOnly: true,
									maxDate: "+0D",
									minDate: new Date( formatoFecha[0], formatoFecha[1] -1 , formatoFecha[2]  ),
								   altField: "#wfecApl"
					} );
				}catch(e){
						$( "#wfec" ).datepicker( {
									 showOn: "button",
								buttonImage: "../../images/medical/root/calendar.gif",
							buttonImageOnly: true,
									maxDate: "+0D",
								   altField: "#wfecApl"
						} );
					}
	}

	function mostrarDetalle2( tipoDetalle, tipoDetalle2, padre, tdPadre ){
		resumen  = jQuery(tdPadre);
		cantidad = resumen.html()*1;
		if( cantidad == 0 ){
			alerta( "Sin Datos por mostrar" );
			return;
		}
		if( resumen.attr( "mostrandoDetalle") == "off" ){
			resumen.attr( "mostrandoDetalle", "on" );
			$( "#detalle_"+padre ).find("div[name=div_"+tipoDetalle+"]").hide();
			$( "#detalle_"+padre ).find("div[name=div_"+tipoDetalle2+"]").show();
			$( "#detalle_"+padre ).show();
			$( "#"+tipoDetalle+""+padre ).attr( "mostrandoDetalle", "off" );
		}else
			{
				resumen.attr( "mostrandoDetalle", "off" );
				$( "#detalle_"+padre ).find("div[name=div_"+tipoDetalle2+"]").hide();
				$( "#detalle_"+padre ).hide();
			}
	}

	function validarMovimiento( chk ){

		check = jQuery( chk );
		filaActual = check.parent().parent();
		validarValores( filaActual );
	}

	function validarValores( filaActual ){

		fila       = jQuery( filaActual );
		check       = fila.find("input[type='checkbox']");
		checkAprov = jQuery( check );
		input      = fila.find("input[type='text']");
		valor      = $( input ).val()*1;
		validado   = true;

		if( valor > 0 ){
			saldoTot =  fila.find("td[name='canSaldo']").html()*1;
			saldoApr =  fila.find("td[name='canSaldoApr']").html()*1;
			saldoInv =  fila.find("td[name='canSaldoInv']").html()*1;

			if ( valor > saldoTot ){
				alerta(' Saldo TOTAL insuficiente ');
				input.val( "" );
				checkAprov.attr("checked", false);
				validado = false;
				return(validado);
			}

			if( ( checkAprov.is(":checked") == true ) && ( valor > saldoApr ) ){
				alerta(' Saldo de APROVECHAMIENTO insuficiente ');
				validado = false;
				input.val( "" );
				checkAprov.attr("checked", false);
				return(validado);
			}

			if( ( checkAprov.is(":checked") == false ) && ( valor > saldoInv ) ){
				alerta(' Saldo de INVENTARIO insuficiente ');
				validado = false;
				input.val( "" );
				checkAprov.attr("checked", false);
				return(validado);
			}
		}
		return( validado )
	}

	function alerta( txt ){
		if( !$('#msjAlerta').is(":visible") ){
			$("#textoAlerta").text( txt );
			$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
					$.unblockUI();
			}, 1600 );
		}
	}

	function intermitenciaFilaErronea( filaAct ){
		claseOrginal = $(filaAct).attr( "claseOrginal" );
		 var i = 0;
		intervalo = setInterval(function(){
			if( i < 10 ){
			  if( filaAct.className == claseOrginal ){
			      filaAct.className = 'fondorojo';
			  }else{
			      filaAct.className = claseOrginal;
			  }
			  i++;
			}else{
				clearInterval( intervalo );
			}
		},600);
	}
	</script>
</head>
<?php
		$usuarios = array();
		$usuarios[$wuser] = consultarNombreUsuario( $wuser );
		$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
		$empresa = strtolower($institucion->baseDeDatos);
		encabezado("APLICACION DE MEDICAMENTOS",$wactualiz, $empresa );

		echo "<form>";

		echo "<INPUT type='hidden' name='wemp_pmla' id='wemp_pmla' value='$wemp_pmla'>";
		( !isset($wfecApl) ) ? $wfecApl = $hoy : $wfecApl = $wfecApl;
		echo "<INPUT type='hidden' id='wfecApl' name='wfecApl' value='{$wfecApl}'>";

		if( empty($his) && empty( $ing ) ){

			echo "<table align='center'>";

			echo "<tr align='center' class='encabezadotabla'>";
			echo "<td colspan='2'>INGRESE LOS DATOS</td>";
			echo "</tr>";

			echo "<tr>";
			echo "<td class='fila1'>Historia:</td>";
			echo "<td class='fila2'><INPUT type='text' name='his'></td>";
			echo "</tr>";

			echo "<tr>";
			echo "<td class='fila1'>Ingreso:</td>";
			echo "<td class='fila2'><INPUT type='text' name='ing'></td>";
			echo "</tr>";

			echo "<tr><td align='center' colspan='2'><INPUT type='submit' value='Ir a aplicaciones'></td></tr>";
			echo "</table>";

			echo "<br>";

			echo "<table align='center'>";
			echo "<tr><td align='center'><INPUT type='button' value='Cerrar' onClick='cerrarVentana()'></td>";
			echo "</tr>";
			echo "</table>";
		}
		else{
				$articulos = validarExistenciaArticulos( $his, $ing );
				if( ( $articulos*1 ) > 0 ){

					if( empty( $ronda ) ){

						echo "<table align='center'>";
						echo "<tr align='center' class='encabezadotabla'><td colspan='2'>INGRESE LOS DATOS</td></tr>";

						echo "<tr>";
						echo "<td class='fila1'>Historia:</td>";
						echo "<td class='fila2'><INPUT type='text' name='his' value='$his'></td>";
						echo "</tr>";

						echo "<tr>";
						echo "<td class='fila1'>Ingreso:</td>";
						echo "<td class='fila2'><INPUT type='text' name='ing' value='$ing'></td>";
						echo "</tr>";

						$query  = "SELECT Ingfei
									 FROM {$wbasedato}_000101
									WHERE inghis = '{$his}'
									  AND ingnin = '{$ing}'";
						$rs     = mysql_query( $query, $conex );
						$row    = mysql_fetch_array( $rs );

						echo "<input type='hidden' name='fechaIngreso' id='fechaIngreso' value='{$row['Ingfei']}'>";

						echo "<tr>";
						echo "<td class='fila1'>Fecha a aplicar:</td>";
						echo "<td class='fila2'><INPUT type='text' name='wfec' id='wfec' enabled='enabled' value='{$hoy}'></td>";
						echo "</tr>";

						echo "<tr>";
						echo "<td class='fila1'>Ronda a aplicar:</td>";
						echo "<td class='fila2'>";
						echo "<SELECT name='ronda' onChange='cambiarRonda( this )'>";
						echo "<option></option>";
						for( $j = 0; $j < 24; $j += 2 ){
							echo "<option value='".($j < 10 ? "0".$j : $j )."'>".($j < 10 ? "0".$j : $j )."</option>";
						}
						echo "</SELECT>";
						echo "</td>";
						echo "</tr>";
						echo "<tr><td align='center' colspan='2'><INPUT type='submit' value='Ir a aplicaciones'></td></tr>";
						echo "</table>";

						echo "<br>";

						echo "<table align='center'>";
						echo "<tr><td colspan='2' align='center'><INPUT type='button' value='Limpiar Formulario' style='width:150' onClick='location.href=\"aplicacionesCargos.php?wemp_pmla=$wemp_pmla\"'></td></tr>";
						echo "<tr><td align='center'><INPUT type='button' value='Cerrar' onClick='cerrarVentana()'></td></tr>";

						echo "</table>";
					}else{
							//Se quita el filtro de aprovechamiento para que muestre los articulos que lo son. (Jonatan 08 Agosto 2013)

							$sql = "SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, SUM( can - dev ) as can, SUM( dev ) as devueltos, Artuni, Tcarconcod, Tcarconnom, Tcarser, SUM( can ) AS grabados, SUM(invent) as invent, SUM(aprov) as aprov
									FROM
									(
										SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, SUM(tcarcan) as can, 0 as dev, 0 as invent,  0 as aprov, Artuni, Tcarconcod, Tcarconnom, Tcarser, Tcarapr
										   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
										  WHERE tcarhis    = '$his'
											AND tcaring    = '$ing'
											AND tcardev   != 'on'
											AND Tcarprocod = artcod
									   GROUP BY 1
										  UNION
										 SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, 0 as can, SUM(tcarcan) as dev, 0 as invent, 0 as aprov, Artuni, Tcarconcod, Tcarconnom, Tcarser, Tcarapr
										   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
										  WHERE tcarhis    = '$his'
											AND tcaring    = '$ing'
											AND tcardev    = 'on'
											AND Tcarprocod = artcod
									   GROUP BY 1
										  UNION
										 SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, 0 as can, 0 as dev, SUM(tcarcan) as invent, 0 as aprov, Artuni, Tcarconcod, Tcarconnom, Tcarser, Tcarapr
										   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
										  WHERE tcarhis    = '$his'
											AND tcaring    = '$ing'
											AND Tcarapr = 'off'
											AND Tcarprocod = artcod
									   GROUP BY 1
										  UNION
										 SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, 0 as can, 0 as dev, 0 as invent, SUM(tcarcan) as aprov, Artuni, Tcarconcod, Tcarconnom, Tcarser, Tcarapr
										   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
										  WHERE tcarhis    = '$his'
											AND tcaring    = '$ing'
											AND Tcarapr = 'on'
											AND Tcarprocod = artcod
									   GROUP BY 1
									) as a
									GROUP BY 1
								   ";

							$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );
							$num = mysql_num_rows( $res );
							if( $num > 0 ){

								/**************************************************************************************************************
								 * Datos demograficos
								 **************************************************************************************************************/
								$rows = mysql_fetch_array( $res );

								$query = "SELECT Pacsex sexo, Pacfna fechaNacimiento, Pacap1, Pacap2, Pacno1, Pacno2, Pactam
											FROM {$wbasedato}_000100
										   WHERE Pachis = '{$his}'";
								$rs  = mysql_query( $query, $conex );
								$row = mysql_fetch_array( $rs );

								$arregloDetalle = array();
								$aplsXtipo      = array();
								$nom            = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2']." ";
								$edad           = calcular_edad( $row['fechaNacimiento'] );

								$query  = "SELECT Ingtpa
											 FROM {$wbasedato}_000101
											WHERE inghis = '{$his}'
											  AND ingnin = '{$ing}'";
								$rs2    = mysql_query( $query, $conex );
								$row2   = mysql_fetch_array( $rs2 );

								$tipoAtencion = $row2['Ingtpa'];
								$tipoServicio = $row['pactam'];
								echo "<table align='center' width='80%'>";

								echo "<tr><td class='encabezadotabla'>Nombre: </td><td class='fila1' style='width:250' colspan='3' align='left'>&nbsp;$nom</td></tr>";
								echo "<tr><td class='encabezadotabla' width='20%'>Historia: </td><td align='center' class='fila1' width='30%'>{$his}-$ing</td><td class='encabezadotabla' width='30%'>Identificación: </td><td class='fila1' align='center'>&nbsp;{$rows['Tcardoc']}&nbsp;</td></tr>";
								echo "<tr><td class='encabezadotabla'>Sexo: </td><td class='fila1' align='center'>{$row['sexo']}</td><td class='encabezadotabla'>Edad: </td><td class='fila1' align='center'>{$edad} Años</td></tr>";
								echo "<tr ".(( $tipoAtencion != "M" ) ? "style='display:none;'" : "")."><td colspan='2'>&nbsp;</td><td class='encabezadotabla'>Tipo de servicio: </td><td class='fila1' align='center'>{$tipoServicio}</td></tr>";
								echo "</table>";

								echo "<br><br><div align='center'><span class='subtituloPagina2'><font size='3'> FECHA A APLICAR: <b> {$wfecApl} </b>  </font></span><br></div>";
								echo "<div align='center'><span class='subtituloPagina2'><font size='3'> RONDA A APLICAR: <b> {$ronda} </b> </font></span><br></div>";
								echo "<br>";

								mysql_data_seek( $res,0 );
								/**************************************************************************************************************/

								echo "<table align='center' id='cargosPaciente' style='border: 1px solid; border-color:#2A5DB0;'>";

								echo "<tr class='encabezadotabla' align='center'>";
								echo "<td style='width:100' rowspan='2'>C&oacute;digo</td>";
								echo "<td rowspan='2'>Descripci&oacute;n</td>";
								echo "<td  rowspan='2'>Unidad</td>";
								echo "<td align='center' colspan='3' nowrap='nowrap'> CANTIDADES <br>GRABADAS </td>";
								echo "<td align='center' colspan='3'> SALDOS ACTUALES </td>";
								echo "<td  rowspan='2'>Cantidad</td>";
								echo "<td  rowspan='2'>Aprov.</td>";
								echo "<td rowspan='2'>Can. Total<br>Aplicada</td>";
								echo "<td rowspan='2'>Can. Total<br>Devuelta</td>";
								echo "<tr class='encabezadotabla' align='center'>";
								echo "<td width='40'>Invent</td>";
								echo "<td width='40'>Aprov</td>";
								echo "<td width='40'>Total</td>";
								echo "<td width='40'>Invent</td>";
								echo "<td width='40'>Aprov</td>";
								echo "<td width='40'>Total</td>";
								echo "</tr>";

								echo "</tr>";

								for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

									$canAplicada = consultarCantidadAplicada( $his, $ing, $rows[ 'Tcarprocod' ] );
									consultarCantidadDevuelta( $his, $ing, $rows[ 'Tcarprocod' ], $rows['devueltos'] );

									$canAplicadaRonda = $arregloDetalle[$rows[ 'Tcarprocod' ]]['Aplicaciones'][$wfecApl][$ronda]['cantidad'];

									if( $rows[ 'can' ] > 0 && ( $rows[ 'can' ] - $canAplicada >= 0 || $canAplicadaRonda ) ){

										$disabled   = "";
										$claseInput = "inputHabilitado";
										$haySaldo   = true;

										if( $rows[ 'can' ] - $canAplicada <= 0 ){
											$disabled   = "disabled";
											$claseInput = "inputDesHabilitado";
											$haySaldo   = false;
										}

										( !isset( $arregloDetalle[$rows[ 'Tcarprocod' ]]['Aplicaciones'][$wfecApl][$ronda]['cantidad'] ) ) ? $canAplicadaRonda = "0" : $canAplicadaRonda;
										if( $arregloDetalle[$rows[ 'Tcarprocod' ]]['Aplicaciones'][$wfecApl][$ronda]['cantidad']*1 > 0 )
										{
											$disabled   = "disabled";
											$claseInput = "inputDesHabilitado";
										}

										$class = "'fila".($i%2+1)."'";

										/************************************************************* FILAS DE RESUMEN ************************************************************/
										echo "<tr class=$class fila='{$i}' detalle='false' validada='true' claseOrginal=$class >";

										//Campos ocultos
										echo "<td style='display:none'>";
										echo "<INPUT type='hidden' name='dat_Dethis' value='$his'>";
										echo "<INPUT type='hidden' name='dat_Deting' value='$ing'>";
										echo "<INPUT type='hidden' name='dat_Detart' value='{$rows[ 'Tcarprocod' ]}'>";
										echo "<INPUT type='hidden' name='dat_Detron' value='{$ronda}'>";
										echo "<INPUT type='hidden' name='dat_Detfec' value='{$wfecApl}'>";
										echo "</td>";

										//Código del articulo
										echo "<td align='center'>{$rows[ 'Tcarprocod' ]}</td>";

										//Descripción del articulo
										echo "<td>{$rows[ 'Tcarpronom' ]}</td>";

										//unidad de medida del articulo
										echo "<td>";
										echo $rows[ 'Artuni' ];
										echo "</td>";

										//Cantidad por inventario
										echo "<td align='center' name='canInvent'>{$rows[ 'invent' ]}</td>";

										//Cantidad por aprovechamiento
										echo "<td align='center' name='canAprov'>{$rows[ 'aprov' ]}</td>";

										//Cantidad grabada
										echo "<td align='center' name='canGrabados'>{$rows[ 'grabados' ]}</td>";

										//Cantidad saldo inventario
										$saldo = $rows[ 'invent' ] - $aplsXtipo['invent'][$rows[ 'Tcarprocod' ]];
										echo "<td align='center' name='canSaldoInv' id='saldo_Inven{$i}'>{$saldo}</td>";

										//Cantidad saldo aprovechamiento
										$saldo = $rows[ 'aprov' ] - $aplsXtipo['aprov'][$rows[ 'Tcarprocod' ]];
										echo "<td align='center' name='canSaldoApr' id='saldo_Aprov{$i}'>{$saldo}</td>";

										//Cantidad saldo total
										$saldo = $rows[ 'can' ] - $canAplicada;
										echo "<td align='center' name='canSaldo' id='saldo_{$i}'>{$saldo}</td>";

										//Cantidad aplicada
										echo "<td>";
										echo "<INPUT type='text' class='solofloat {$claseInput}' name='dat_Detcan' id='dat_Detcan_{$i}' style='width:100' $disabled>";
										echo "</td>";
										//checkBox para aplicaciones de aprovechamiento
										echo "<td align='center'>";
										echo "<INPUT type='checkbox' name='dat_Detapr' id='dat_Detapr{$i}' $disabled onclick='validarMovimiento( this );'>";
										echo "</td>";

										//Cantidad aplicada total
										echo "<td align='center' name='canAplTotal' id='Aplicaciones{$i}' class='msg_tooltip' style='cursor:pointer;' title=\"".tooltipAplicaciones()."\" mostrandoDetalle='off' onclick='mostrarDetalle2( \"Devoluciones\", \"Aplicaciones\", \"{$i}\", this )'>".( $canAplicada ? $canAplicada : "0" )."</td>";

										//Cantidad devuelta
										echo "<td align='center' name='canDevueltos' id='Devoluciones{$i}' style='cursor:pointer;' mostrandoDetalle='off' onclick='mostrarDetalle2( \"Aplicaciones\", \"Devoluciones\", \"{$i}\", this )'>{$rows[ 'devueltos' ]}</td>";

										echo "</tr>";

										//***********************************************  FIN FILAS DE RESUMEN ******************************************************************** //
										/*if( $rows[ 'Tcarprocod' ] == "H021801"){
											echo "<pre>";
											print_r(  $arregloDetalle[$rows[ 'Tcarprocod' ]]['Devoluciones']);
											echo "</pre>";
											//echo "<pre>";
											//print_r(  $arregloDetalle[$rows[ 'Tcarprocod' ]]);
											//echo "</pre>";
										}*/
										//******************************************** FILAS DETALLE DE APLICACIONES ****************************************************************//
										echo "<tr detalle='true' style='display:none;' id='detalle_{$i}'>";
											echo "<td colspan='13' align='center'>";  /*contenedor*/
											echo "<br>";
												echo detalleAplicacionesArticulo( $arregloDetalle[$rows[ 'Tcarprocod' ]], $rows[ 'Tcarprocod' ], $i, $ronda, $class );
											echo "<br>";
											echo "</td>";
										echo "</tr>";


									}
									else{
										$i--;
									}
								}

								echo "</table>";
							}
							else{
								echo "<center><b>NO SE ENCONTRARON ARTICULOS CARGADOS AL PACIENTE</b></center>";
							}


						//Borton de retornar
						echo "<br>";
						echo "<table align='center'>";

						echo "<tr>";
						echo "<td>";
						echo "<INPUT type='button' onClick='javascript:aplicarMeds();' value='Aplicar' style='width:100'>";
						echo "</td>";
						echo "</tr>";

						echo "<tr>";
						echo "<td><br><INPUT type='button' value='Retornar' style='width:100' onClick='location.href=\"aplicacionesCargos.php?wemp_pmla=$wemp_pmla&his=$his&ing=$ing\"'></td>";
						echo "</tr>";

						echo "</table>";

						echo "<div id='msg' style='display:none'>Procesando....</div>";
					}
				}else{
					//Borton de retornar
					    echo "<center><b>NO SE ENCONTRARON ARTICULOS CARGADOS AL PACIENTE</b></center>";
						echo "<br>";
						echo "<table align='center'>";

						echo "<tr>";
						echo "<td><br><INPUT type='button' value='Retornar' style='width:100' onClick='location.href=\"aplicacionesCargos.php?wemp_pmla=$wemp_pmla\"'></td>";
						echo "</tr>";

						echo "</table>";
				}
		}
		echo "<div id='msjAlerta' style='display:none;'>";
				echo '<br>';
				echo "<img src='../../images/medical/root/Advertencia.png'/>";
				echo "<br><br><div id='textoAlerta'></div><br><br>";
				echo '</div>';
		echo "</form>";
		echo "</html>";
	}
}
?>
