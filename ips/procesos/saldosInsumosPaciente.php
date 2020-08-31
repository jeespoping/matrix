<?php
include_once("conex.php");
/****************************************************************************************************************************************
 * FUNCIONES
 ****************************************************************************************************************************************/
function consultarPermisoDevolucion( $wuser ){
	global $conex;
	global $wemp_pmla;

	$query = " SELECT Detval usuarios
				 FROM root_000051
				WHERE Detemp = '{$wemp_pmla}'
				  AND Detapl = 'permitir_devolver'";

	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_array( $rs );

	$usuarios 	   = explode( ",", $row['usuarios']);
	( in_array( $wuser, $usuarios ) ) ? $tienePermisos = true : $tienePermisos = false;
	return( $tienePermisos );
}

function pedirHora(){
	$hora = date( "H:i:s" );
	return($hora);
}

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

function detalleAplicacionesDevoluciones( $datosArticulo, $codigoArticulo, $padre, $class )
{
	global $usuarios;
	$hoy = date( "Y-m-d" );
	$ocultarFecha = false;

	if(!isset($datosArticulo))
		$datosArticulo = array();

	if( !isset( $datosArticulo["Devoluciones"][$hoy] ) ){
		$datosArticulo["Devoluciones"][$hoy] = array();
		$ocultarFecha = true;
	}

	$detalle = "";
	$detalle .= "<div align='center' class='{$class}' style='width:70%; height:100%' >";
	$j = 1;
	$i = 0;
	foreach( $datosArticulo as $keyMovimiento=>$fechas ){
		ksort( $datosArticulo[$keyMovimiento], true );
		($keyMovimiento == "Aplicaciones") ? $tituloRondaHora = "RONDA" : $tituloRondaHora = "HORA";
		$detalle .= "<div align='center' name='div_{$keyMovimiento}' style='width:100%;' class='{$class}'>";
		$detalle .= "<span class='subtituloPagina2'><font size='3'>  <b> {$keyMovimiento} </b> </font></span>";
		$detalle .= "<br><br>";
		$detalle .= "<table style='width:100%; height:100%;' cellpadding='0' cellspacing='0' >";
			$detalle .= "<tr class='encabezadotabla'>";
			$detalle .= "<td align='center' style='font-size:13px' width='20%'>FECHA</td>";
			$detalle .= "<td align='center' style='font-size:13px'>";
				 $detalle.= "<table style='width:100%; height:100%;' cellpadding='0' cellspacing='0'>";
				 $detalle.= "<tr class='encabezadotabla'><td align='center' width='25%' style='font-size:13px; border: 1px solid; border-color:#fffff;'>{$tituloRondaHora}</td><td align='center' width='15%' style='font-size:11px; border: 1px solid; border-color:#fffff;'>CANT.</td><td align='center' width='60%' style='font-size:11px; border: 1px solid; border-color:#fffff; '>USUARIO</td></tr>";
				 $detalle.= "</table>";
			$detalle .= "</td>";
			$detalle .= "</tr>";
		foreach( $fechas as $keyFecha=>$horas ){
			( $j == 1 ) ? $bordeArriba = "border-top: 3px solid;" : $bordeArriba = "";
			( trim( $keyFecha) == trim( $hoy ) ) ? $idTablaHoy = " id='tabla_{$padre}' " : $idTablaHoy = "";
			( trim( $keyFecha) == trim( $hoy ) and $ocultarFecha) ? $displayFecha = " style='display:none;' " : $displayFecha = "";
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
	$detalle .= "</div>";
	return( $detalle );
}


 /**************************************************************************************************
 * CALCULA LA EDAD DE UN PACIENTE TENIENDO LA FECHA DE NACIMIENTO.
 **************************************************************************************************/
function calcular_edad($fecha){
    $dias = explode( "-", $fecha );
    $dias = mktime(0,0,0,$dias[1],$dias[2],$dias[0]);
    $edad = (int)((time()-$dias)/31556926 );
    return $edad;
}

function tieneIngresoSiguiente( $his, $ing ){

	global $conex;
	global $wbasedato;
	global $wuser;

	$val = false;

	$sql = "SELECT *
			FROM {$wbasedato}_000100 a, {$wbasedato}_000101 b
			WHERE
				Pachis = '$his'
				AND Inghis = Pachis
				AND Ingnin = '$ing'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );
	$num = mysql_num_rows( $res );

	if( $rows = mysql_fetch_array($res) ){
		$val = true;
	}

	return $val;
}

/************************************************************************************
 * Consulto la cantidad aplicada para el medicamento para un paciente
 ************************************************************************************/
function consultarCantidadAplicada( $his, $ing, $art ){

	global $conex;
	global $wbasedato;
	global $wuser;
	global $arregloDetalle;
	global $wfecha;

	$val = 0;


	$sql = "SELECT Aplfec fechaAplicacion, Aplart articulo, Aplron ronda, Aplcan as can, Aplusu usuario, Fecha_data fechaCreacion, id
			FROM {$wbasedato}_000146 c
			WHERE
				aplhis = '$his'
				AND apling = '$ing'
				AND aplart = '$art'
				AND aplest = 'on'
			  ORDER BY FechaAplicacion, ronda
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );
	$num = mysql_num_rows( $res );
	while( $rows = mysql_fetch_array($res) ){
		$val += $rows[ 'can' ];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['cantidad']       = $rows['can'];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['usuario']        = $rows['usuario'];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['fechaCreacion']  = $rows['fechaCreacion'];
		$arregloDetalle[$art]['Aplicaciones'][$rows['fechaAplicacion']][$rows['ronda']]['id']  			  = $rows['id'];
	}

	/*if( $num == 0){
		$arregloDetalle[$art]['Aplicaciones'][$wfecha]['sin']['cantidad']				= 0;
		$arregloDetalle[$art]['Aplicaciones'][$wfecha]['sin']['usuario']       = $wuser;
		$arregloDetalle[$art]['Aplicaciones'][$wfecha]['sin']['fechaCreacion'] = $wfecha;
		$arregloDetalle[$art]['Aplicaciones'][$wfecha]['sin']['ronda']['id']  			= "";
	}*/

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
					FROM clisur_000106
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

function consultarAliasAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){
	 $q = "   SELECT Detval
				FROM root_000051
			   WHERE Detemp = '".$codigoInstitucion."'
				 AND Detapl = '".$nombreAplicacion."'";
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} else {
		return false;
	}
	return $alias;
}



  //FUNCION QUE PERMITE EL TRASLADO AUTOMATICO DE MEDICAMENTOS 8 Febrero 2012 /Jonatan López
  function trasladoautomatico(&$wexiste, $wdevol, $wbodega, $wconceptraslado, $wccogra, $wvaltar, $wno2, $waprovecha, $wconinv, $wpaquete, $wprocod, $wfecha, $hora, $wconmvto, $wcantidad, $wusuario, $whistoria, $wing )
    {

	  global $conex;
	  global $wbasedato;
	  global $wid;
	  global $whistoria;
      global $wing;
      global $wno1;
      global $wno2;
      global $wap1;
      global $wap2;
      global $wdoc;
      global $wcodemp;
      global $wnomemp;
      global $wfecing;
      global $wser;
      global $wcodcon;
      global $wnomcon;
      global $wprocod;
      global $wpronom;
      global $wcodter;
      global $wnomter;
      global $wporter;
      global $wcantidad;
      global $wvaltar;
      global $wrecexc;
      global $wfacturable;
      global $wcco;
      global $wccogra;
      global $wfeccar;
      global $wcontip;
      global $wconinv;
      global $wok;
      global $wcodpaq;
      global $wpaquete;
	  global $wtipfac;
	  global $respuestaAjax;

	//TRAIGO LAS EXISTENCIAS DEL CENTRO DE COSTOS DE LA VARIABLE $wbodega
	$q= "SELECT karexi, Karcod, Karcco "
	   ."  FROM ".$wbasedato."_000007 "
	   ." WHERE karcco = '".$wbodega."'"
	   ."   AND karcod = '".$wprocod."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row_exik = mysql_fetch_array($res);
	$wexisteKardex = $row_exik['karexi'];

	//TRAIGO EL NOMBRE DEL CENTRO DE COSTOS ASOCIADO A LA VARIABLE $wbodega
	$q= "SELECT Ccocod, Ccodes "
	   ."  FROM ".$wbasedato."_000003"
	   ." WHERE Ccocod = '".$wbodega."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$wnombrecco = strtoupper($row['Ccodes']);

	// VALIDO SI LA CANTIDAD DE PRODUCTOS ES MAYOR A LA QUE HAY EN EL KARDEX, SI ES MAYOR SE DETIENE LA FUNCION Y NO MUESTRA EL MENSAJE DEL
	// INVENTARIO DEL KARDEX ACTUAL PARA ESTE PRODUCTO
	if ($wcantidad > $wexisteKardex)
		{
		return false;
		}

	//SI LAS DEVOLUCIONES PARA LA HISTORIA E INGRESO ASOCIADAS A UN ARTICULOS SON IGUALES A CERO, DETIENE LA FUNCION trasladoautomatico
	if ($wdevol=='on')
		{
		return false;
		}
	elseif($wexiste == 0 and $wdevol=='on')
	{
	return false;
	}

	//VALIDO SI LA CANTIDAD DE PRODUCTOS EN EL KARDEX EN IGUAL A CERO O SI LA CANTIDAD EXISTENTE EN EL KARDEX EN MENOR A LA SOLICITADA
	//SI ES ASI SE DETIENE LA FUNCION
	if ($wexisteKardex == 0 or $wexisteKardex < $wcantidad)
		{

		$respuestaAjax[ 'mensaje' ] = "NO HAY INVENTARIO EN $wnombrecco PARA ESTE PRODUCTO";
		// echo "<script>
			    // alert ('NO HAY INVENTARIO EN $wnombrecco PARA ESTE PRODUCTO');
	          // </script>";
		return false;
		}

	 // BLOQUEA LAS TABLAS DEL KARDEX PARA ACTUALIZAR LOS CENTROS DE COSTOS DE TRASLADO
	  $q = "lock table ".$wbasedato."_000007 LOW_PRIORITY WRITE";
	  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			//ACTUALIZO EN LA TABLA DEL -- <SALDOS EN LINEA> -- DEL  **** KARDEX ****
	  if ($wexiste >= 0 and $wexisteKardex > 0 and $wdevol!='on')
		    {
			$q= " UPDATE ".$wbasedato."_000007 "
			   ."    SET karexi = karexi - ".$wcantidad
			   ."  WHERE karcco = '".$wbodega."'"
			   ."    AND karcod = '".$wprocod."'";
		    $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


             //Recalculo el costo promedio del articulo en el centro de costos destino
            $q= "SELECT karexi, karpro "
                ."  FROM ".$wbasedato."_000007 "
                ." WHERE karcco = '".$wbodega."'"
                ."   AND karcod = '".$wprocod."'";
            $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $row_cos = mysql_fetch_array($res_cos);

            $wexist_actuales_origen = $row_cos[0];
            $wcosto_pro_actual_origen = $row_cos[1];

            $q7= "SELECT karexi, karpro "
                ."  FROM ".$wbasedato."_000007 "
                ." WHERE karcco = '".$wccogra."'"
                ."   AND karcod = '".$wprocod."'";
            $res_cos = mysql_query($q7,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q7." - ".mysql_error());
            $row_cos = mysql_fetch_array($res_cos);

            $wexist_actuales_destino = $row_cos[0];
            $wcosto_pro_actual_destino = $row_cos[1];

            //Nuevo costo promedio del articulo en el centro de costos a grabar
            $wnuevocospro = (($wexist_actuales_destino * $wcosto_pro_actual_destino) + ($wcantidad * $wcosto_pro_actual_origen))/($wexist_actuales_destino + $wcantidad);
            $wnuevocospro = round($wnuevocospro,4);

            $q1= " UPDATE ".$wbasedato."_000007 "
                ."    SET Karpro = ".$wnuevocospro." "
                ."  WHERE Karcco = '".$wccogra."'"
                ."    AND Karcod = '".$wprocod."'";
            $err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


			//ACTUALIZA EL STOCK DEL CENTRO DE COSTOS
		    $q1= " UPDATE ".$wbasedato."_000007 "
			    ."    SET karexi = karexi + ".$wcantidad
			    ."  WHERE karcco = '".$wccogra."'"
			    ."    AND karcod = '".$wprocod."'";
		    $res3 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());



			//TRAIGO LAS NUEVAS EXISTENCIAS DEL KARDEX PARA REEMPLAZAR LA VARIABLE $wexiste
			$q= "SELECT karexi, Karcod, Karcco "
			   ."  FROM ".$wbasedato."_000007 "
			   ." WHERE karcco = '".$wccogra."'"
			   ."   AND karcod = '".$wprocod."'";
			$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row_exi = mysql_fetch_array($res2);
			$wexiste = $row_exi['karexi'];
			}
	    $q= " UNLOCK TABLES";
	    $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	  /////////////////////////////////////////////////////////////////////////////////
	  //ACTUALIZO Y TOMO EL CONSECUTIVO Y EL CODIGO DEL CONCEPTO DE VENTA EN INVENTARIO

	  $q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  $q1= " UPDATE ".$wbasedato."_000008 "
		  ."    SET concon = concon + 1 "
		  ."  WHERE concod = ".$wconceptraslado." ";
	  $err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  $q= " SELECT concon, concod "
		 ."   FROM ".$wbasedato."_000008 "
		 ."  WHERE concod = ".$wconceptraslado." ";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($err);
	  $wnromvto=$row[0];
	  $wconmvto=$row[1];                            //Concepto de Salida (Ventas)

	  $q = " UNLOCK TABLES";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  /////////////////////////////////////////////////////////////////////////////////
	  //ACA DESCARGO DEL INVENTARIO ===================================================
	  /////////////////////////////////////////////////////////////////////////////////

	  //===================================================================================================================================
	  //===================================================================================================================================
	  //GRABO EN LA TABLA DEL -- <ENCABEZADO> -- DEL **** MOVIMIENTO DE INVENTARIOS ****
	  $q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     , mendan, menpre, mennit, menusu, menfac, menest, Seguridad        ) "
		 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvto."','".$wconceptraslado."','".$wbodega."','".$wccogra."', '.'   , 0     , '0'   , '.'   , '.'   ,   'on', 'C-".$wusuario."')";
	  $res = mysql_query($q,$conex) or die ("Error en la 10 para la funcion translado: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  //===================================================================================================================================
	  //===================================================================================================================================
	  //GRABO EN LA TABLA DEL -- <DETALLE> -- DE  **** MOVIMIENTO DE INVENTARIOS ****
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //============================================================================================================================

	  //=========================================
	  //TRAIGO EL COSTO PROMEDIO DEL ARTICULO
	  $q= "SELECT karpro "
		 ."  FROM ".$wbasedato."_000007 "
		 ." WHERE karcco = '".$wbodega."'"
		 ."   AND karcod = '".$wprocod."'";
	  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row_cos = mysql_fetch_array($res2);

	  if ($row_cos[0] == "")
		 $row_cos[0]=0;

	  //=========================================
	  //GRABO EL DETALLE DEL ARTICULO
	  $q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan      ,  mdevto                    , mdepiv , mdeest, Seguridad        ) "
		 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconceptraslado."','".$wnromvto."','".$wprocod."',".$wcantidad.",".($wcantidad*$row_cos[0]).", '0'    , 'on'  , 'C-".$wusuario."')";
	  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());





	}


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FEBRERO 18 DE 2009: Toda esta funcion fue creada en esta fecha
  //*****************
  //                               Conc.Factu, Insumo  , Hist. , Ingreso, Cant. maxima para devolver
  function buscar_saldo_en_historia($wcodcon , $wprocod, $whis , $wing, $waprovecha )
    {
	  global $wbasedato;
	  global $conex;
	  global $wexidev;
	  //global $waprovecha;

	  if ($waprovecha=="on") //Si es devolucion de aprovechamiento
	    {
		  $q = " SELECT SUM(tcarcan) "
		      ."   FROM ".$wbasedato."_000106 "
		      ."  WHERE tcarhis    = '".$whis."'"
		      ."    AND tcaring    = '".$wing."'"
		      ."    AND tcarconcod = '".$wcodcon."'"       //Concepto de facturacion
		      ."    AND tcarprocod = '".$wprocod."'"
		      ."    AND tcardev   != 'on' "
		      ."    AND tcarapr    = 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);

		  if ($num > 0)
		    {
			  $row = mysql_fetch_array($res);
			  $wcangra=$row[0];

			  $q = " SELECT SUM(tcarcan) "
			      ."   FROM ".$wbasedato."_000106 "
			      ."  WHERE tcarhis    = '".$whis."'"
			      ."    AND tcaring    = '".$wing."'"
			      ."    AND tcarconcod = '".$wcodcon."'"    //Concepto de Facturacion
			      ."    AND tcarprocod = '".$wprocod."'"
			      ."    AND tcardev    = 'on' "
			      ."    AND tcarapr    = 'on'";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row = mysql_fetch_array($res);
			  $wcandev=$row[0];

			  $wexidev=($wcangra-$wcandev);  //Esta es la cantidad maxima que se puede devolver
			}
		    else
		      $wexidev=0;
	    }
	      else  //Si no es devolucion de aprovechamiento
	        {
			  $q = " SELECT SUM(tcarcan) "
			      ."   FROM ".$wbasedato."_000106 "
			      ."  WHERE tcarhis    = '".$whis."'"
			      ."    AND tcaring    = '".$wing."'"
			      ."    AND tcarconcod = '".$wcodcon."'"       //Concepto de facturacion
			      ."    AND tcarprocod = '".$wprocod."'"
			      ."    AND tcardev   != 'on' "
			      ."    AND tcarapr    = 'off' ";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if ($num > 0)
			    {
				  $row = mysql_fetch_array($res);
				  $wcangra=$row[0];

				  $q = " SELECT SUM(tcarcan) "
				      ."   FROM ".$wbasedato."_000106 "
				      ."  WHERE tcarhis    = '".$whis."'"
				      ."    AND tcaring    = '".$wing."'"
				      ."    AND tcarconcod = '".$wcodcon."'"    //Concepto de Facturacion
				      ."    AND tcarprocod = '".$wprocod."'"
				      ."    AND tcardev    = 'on' "
				      ."    AND tcarapr    = 'off'";
				  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row = mysql_fetch_array($res);
				  $wcandev=$row[0];

				  $wexidev=($wcangra-$wcandev);  //Esta es la cantidad maxima que se puede devolver
				}
			    else
			       $wexidev=0;
		    }
	}
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


  function validar_datos_a_grabar()
    {
	  global $conex;
      global $wbasedato;
      global $wusuario;
	  global $whistoria;
      global $wing;
      global $wno1;
      global $wno2;
      global $wap1;
      global $wap2;
      global $wdoc;
      global $wcodemp;
      global $wnomemp;
      global $wfecing;
      global $wser;
      global $wcodcon;
      global $wnomcon;
      global $wprocod;
      global $wpronom;
      global $wcodter;
      global $wnomter;
      global $wporter;
      global $wcantidad;
      global $wvaltar;
      global $wrecexc;
      global $wfacturable;
      global $wcco;
      global $wccogra;
      global $wfeccar;
      global $wcontip;
      global $wconinv;
      global $wok;
      global $wcodpaq;
      global $wpaquete;


	  global $respuestaAjax;

	  $wok="on";

      //Verifico que la historia y numero de ingreso exista
      $q = " SELECT COUNT(*) "
          ."   FROM ".$wbasedato."_000100, ".$wbasedato."_000101 "
          ."  WHERE pachis = '".$whistoria."'"
          ."    AND pachis = inghis "
          ."    AND ingnin = '".$wing."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  if ($num > 0)
	    {
	      $row = mysql_fetch_array($res);
	      if ($row[0]>0)
	         $wok="on";
	        else
	            {
		        $wok="off";

				$respuestaAjax[ 'mensaje' ] = "LA HISTORIA DIGITADA NO EXISTE";
		        }
        }

	if( $wok == "on" )
	{

      	if( isset($wpaquete) && $wpaquete == "on" )
		{
	      if( !isset($wcodpaq) || $wcodpaq == "" )
		    {
		  	$wok="off";
			$respuestaAjax[ 'mensaje' ] = "NO HA INGRESADO CODIGO DEL PAQUETE";
		    }
      	}
    }

      //Verifico que el concepto exista
      if ($wok=="on")
        {
	      $q = " SELECT COUNT(*) "
	          ."   FROM ".$wbasedato."_000004 "
	          ."  WHERE grucod = '".$wcodcon."'"
	          ."    AND gruest = 'on' ";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);
		  if ($num > 0)
		    {
		      $row = mysql_fetch_array($res);
		      if ($row[0]>0)
		         $wok="on";
		        else
		            {
			        $wok="off";
					$respuestaAjax[ 'mensaje' ] = "EL CONCEPTO NO EXISTE O ESTA INACTIVO";
			        }
	        }
        }

      //Verifico que el centro de costo exista
      if ($wok=="on")
        {
	      $q = " SELECT COUNT(*) "
	          ."   FROM ".$wbasedato."_000003 "
	          ."  WHERE ccocod = '".$wccogra."'"
	          ."    AND ccoest = 'on' ";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);
		  if($num > 0)
		    {
		      $row = mysql_fetch_array($res);

		      if ($row[0]>0)
		         $wok="on";
		        else
		            {
			        $wok="off";
					$respuestaAjax[ 'mensaje' ] = "EL CENTRO DE COSTO NO EXISTE O ESTA INACTIVO";
			        }
	        }
        }

      //Verifico que el CODIGO DEL PROCEDIMIENTO exista
      if ($wok=="on")
        {
	      if ($wconinv !="on")
	        {
		      $q = " SELECT COUNT(*) "
		          ."   FROM ".$wbasedato."_000103 "
		          ."  WHERE procod = '".$wprocod."'"
		          ."    AND proest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if($num > 0)
			    {
			      $row = mysql_fetch_array($res);
			      if ($row[0]>0)
			         $wok="on";
			        else
			            {
				        $wok="off";
						$respuestaAjax[ 'mensaje' ] = "EL PROCEDIMIENTO NO EXISTE O ESTA INACTIVO";
				        }
		        }
	        }
	        else
	            {
		        $q = " SELECT COUNT(*) "
			        ."   FROM ".$wbasedato."_000001 "
			        ."  WHERE artcod = '".$wprocod."'"
			        ."    AND artest = 'on' ";
			    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				if ($num > 0)
				    {
				    $row = mysql_fetch_array($res);
				    if ($row[0]>0)
				       $wok="on";
				      else
				        {
					        $wok="off";
							$respuestaAjax[ 'mensaje' ] = "EL ARTICULO NO EXISTE O ESTA INACTIVO";
					    }
			        }

	            }
        }

      //Verifico que el NOMBRE DEL PROCEDIMIENTO exista
      if($wok=="on")
        {
	      if($wconinv !="on")
	        {
		      $q = " SELECT COUNT(*) "
		          ."   FROM ".$wbasedato."_000103 "
		          ."  WHERE pronom = '".$wpronom."'"
		          ."    AND proest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if($num > 0)
			    {
			      $row = mysql_fetch_array($res);
			      if ($row[0]>0)
			         $wok="on";
			        else
			            {
				        $wok="off";
						$respuestaAjax[ 'mensaje' ] = "EL NOMBRE DEL PROCEDIMIENTO NO EXISTE O FUE CAMBIADO";
				        }
		        }
	        }
	        else
	            {
		        $q = " SELECT COUNT(*) "
			        ."   FROM ".$wbasedato."_000001 "
			        ."  WHERE artnom = '".$wpronom."'"
			        ."    AND artest = 'on' ";
			    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				if ($num > 0)
				    {
				    $row = mysql_fetch_array($res);
				    if ($row[0]>0)
				       $wok="on";
				      else
				        {
				          $wok="off";
						  $respuestaAjax[ 'mensaje' ] = "EL NOMBRE DEL ARTICULO NO EXISTE O FUE CAMBIADO";
					    }
			        }
		        }
	    }

      //Verifico que el NIT DEL TERCERO exista
      if($wok=="on")
        {
	      if($wcontip =="C")  //Si es compartido
	        {
		      $q = " SELECT COUNT(*) "
		          ."   FROM ".$wbasedato."_000051 "
		          ."  WHERE meddoc = '".$wcodter."'"
		          ."    AND medest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if($num > 0)
			    {
			      $row = mysql_fetch_array($res);
			      if ($row[0]>0)
			         $wok="on";
			        else
			            {
				        $wok="off";
						$respuestaAjax[ 'mensaje' ] = "EL TERCERO NO EXISTE O ESTA INACTIVO";
				        }
		        }
	        }
        }

      //Verifico que el NOMBRE DEL TERCERO exista
      if($wok=="on")
        {
	      if($wcontip =="C")  //Si es compartido
	        {
		      $q = " SELECT COUNT(*) "
		          ."   FROM ".$wbasedato."_000051 "
		          ."  WHERE mednom = '".$wnomter."'"
		          ."    AND medest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if ($num > 0)
			    {
			      $row = mysql_fetch_array($res);
			      if ($row[0]>0)
			         $wok="on";
			        else
			            {
				        $wok="off";
						$respuestaAjax[ 'mensaje' ] = "EL NOMBRE TERCERO NO EXISTE O FUE CAMBIADO";
				        }
		        }
	        }
        }

      //Verifico que el campo de RECONOCIDO O EXCEDENTE este digitado
      if($wok=="on")
        {
	      if($wrecexc!="R" and $wrecexc!="E")  //Si es compartido
	        {
		      $wok="off";
			  $respuestaAjax[ 'mensaje' ] = "DEBE COLOCAR SI ES RECONOCIDO O EXCEDENTE (R/E)";
			}
        }

      //Verifico que el campo de FACTURABLE este digitado
      if($wok=="on")
        {
	      if($wfacturable!="S" and $wfacturable!="N")  //Si es compartido
	        {
		      $wok="off";
			  $respuestaAjax[ 'mensaje' ] = "DEBE COLOCAR FACTURABLE (S/N)";
			}
        }

      //Verifico que el PORCENTAJE del tercero exista
      if($wok=="on")
        {
	      if($wcontip =="C")  //Si es compartido
	        {
		      if(!isset($wporter) or trim($wporter)=="")
		        {
			      $wok="off";
				  $respuestaAjax[ 'mensaje' ] = "EL TERCERO NO TIENE DEFINIDO UN PORCENTAJE";
				}
			}
        }

      //Verifico que la CANTIDAD exista y sea mayor a cero
      if($wok=="on")
        {
	      if(!isset($wcantidad) or $wcantidad<=0)
	        {
		      $wok="off";
			  $respuestaAjax[ 'mensaje' ] = "CANTIDAD DEBE SE MAYOR A CERO";
			}
        }

      //Verifico que la TARIFA exista y sea mayor a cero
      if($wok=="on")
        {
	      if (!isset($wvaltar))
	        {
		     $wok="off";
			 $respuestaAjax[ 'mensaje' ] = "NO EXISTE TARIFA PARA EL PROCEDIMIENTO";
			}
        }

      //Verifico que la FECHA exista
      if($wok=="on")
        {
	      if(!isset($wfeccar) or trim($wfeccar)=="")
	        {
		      $wok="off";
			  $respuestaAjax[ 'mensaje' ] = "LA FECHA DIGITADA NO EXISTE O NO SE DIGITO";
			}
        }

    }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Para buscar si existen cargos anteriores al ingreso actual sin facturar
  function cargos_anteriores()
    {
	    global $conex;
        global $wbasedato;
        global $wcajadm;
        global $whistoria;
        global $wing;
        global $wcargos_sin_facturar;

	    $q =  " SELECT tcaring "
	         ."   FROM ".$wbasedato."_000106 "
	         ."  WHERE tcarhis = '".$whistoria."'"
	         ."    AND tcaring <= '".(intval($wing)-1)."'+0"
	         ."    AND tcarest = 'on' "
	         ."    AND tcarvto <> (tcarfex+tcarfre) "    //Trae los cargos con valores negativos o positivos
	         ."    AND tcarfac = 'S' "
	         ."  GROUP BY 1 " ;
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num = mysql_num_rows($res);

	    if ($num > 0)
	        {
		    $wingresos = "";
		    for ($i=1;$i<=$num;$i++)
		       {
			    $row = mysql_fetch_array($res);
			    $wingresos= $wingresos.$row[0].", ";
		       }

		       $wmensaje="HAY CARGOS PENDIENTES DE FACTURAR DEL(OS) SIGUIENTE(S) INGRESO(S): ".$wingresos;

			   $respuestaAjax[ 'mensaje' ] = $wmensaje;
		       // echo '<script language="javascript">';
			   // echo 'alert ("'.$wmensaje.'")';
			   // echo '</script>';

			   $wcargos_sin_facturar="ok";
		    }
	}


/****************************************************************************************************************************************
 * FIN FUNCIONES
 ****************************************************************************************************************************************/

//===========================================================================================================================================
//INICIO DEL PROGRAMA
//===========================================================================================================================================

include_once("root/comun.php");

session_start();

// Validación de usuario
// if (!isset($user))
// {
	// if (!isset($_SESSION['user']))
	// {
		// session_register("user");
	// }
// }

//Codigo de usuario que ingreso al sistema
if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));

$usuario = new Usuario();

$usuario->codigo = $wuser;
//Valida codigo de usuario en sesion si no esta registrado el sistema termina la ejecucion
if (!isset($_SESSION['user']))
{
	if( !empty( $consultaAjax ) ){
		echo json_encode( Array( "mensaje" => "usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix." ) );
	}
	else{
		terminarEjecucion("usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.");
	}
}
else
{
	$seguridad = $usuario->codigo;
	$conex = obtenerConexionBD("matrix");

	session_register("wpagook");
	session_register("wprestamo");




	$query =   " SELECT Empcod, Empbda
				   FROM root_000050
				  WHERE Empcod = '$wemp_pmla'";
	$res = mysql_query($query, $conex) or die(mysql_error() . " - Error en el query: $query - " . mysql_error());
	$row = mysql_fetch_array($res);
	$wbasedato = $row['Empbda'];

	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));
	$puedeDevolver   = consultarPermisoDevolucion( $wusuario );
	$wfecha=date("Y-m-d");
	$hora = (string)date("H:i:s");

	//ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
	$wbasedato = strtolower($wbasedato);
	$q =" SELECT Cjecco, Cjecaj, Cjetin, cjetem, cjeadm, cjebod  "
	   ."   FROM ".$wbasedato."_000030 "
	   ."  WHERE Cjeusu = '".$wusuario."'"
	   ."    AND Cjeest = 'on' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);

		$pos = strpos($row[0],"-");
		$wcco = substr($row[0],0,$pos);
		$wnomcco = substr($row[0],$pos+1,strlen($row[0]));
		$wbod = $row['cjebod'];
		$pos = strpos($row[1],"-");
		$wcaja = substr($row[1],0,$pos);
		$wnomcaj = substr($row[1],$pos+1,strlen($row[1]));

		$wcajadm=$row[4];

		$wtiping = $row[2];
		global $wbod;
		if (!isset($wtipcli)) $wtipcli = $row[3];
	}
    else{
		if( !empty( $consultaAjax ) ){
			echo json_encode( Array( "mensaje" => "usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix." ) );
		}
		else{
			echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR";
		}
	   exit;
	}

	$wcol=6;  //Numero de columnas que se tienen o se muestran en pantalla

	/*********************************************************************************
	* AQUI COMIENZA EL PROGRAMA
	********************************************************************************/

	global $wexidev; //FEBRERO 18 DE 2009:

	$wcf="fila2";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="fila1";  //COLOR DEL FONDO 2  -- Azul claro

	//===========================================================================================================================================
	//ACA COMIENZA EL ENCABEZADO DE LA VENTA

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//ACA SE GRABAN LOS CARGOS O DEVOLUCION
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if( !empty( $consultaAjax ) ){

		switch( $consultaAjax ){

			case 10:
			{
				$respuestaAjax = Array( "mensaje" => "", "hora"=>"" );

				$wfeccar = date( "Y-m-d" );
				$whorcar = date( "H:i:s" );

				////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//HISTORIA CLINICA
				//*************************************************************************************************************************************************************************************************************************************************************************************
				if(isset($whistoria)) //Si ya fue digitado el documento del cliente
				{
					if($whistoria != "" and strpos($whistoria,"-")==0)  //Por si digitan la historia junto con el número de ingreso, no deje grabar
					{
						$q= " SELECT MAX(ingnin+0) "
							 ."  FROM ".$wbasedato."_000100, ".$wbasedato."_000101 "
							 ." WHERE pachis = '".$whistoria."'"
							 ."   AND pachis = inghis ";
						$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num1 = mysql_num_rows($res1);
						$row1 = mysql_fetch_array($res1);

						// if (!isset($wing) or ($wing==""))
							// $wwing=$row1[0];
						// else
							// $wwing=$wing;

						if(isset($wing) and $wing != "")
						{
							$q= "SELECT pachis,  pacno1, pacno2, pacap1, pacap2, pacdoc, ingcem, ingent, ingfei, ingsei, ingnin, ingtar "
								 ."  FROM ".$wbasedato."_000100, ".$wbasedato."_000101 "
								 ." WHERE pachis = '".$whistoria."'"
								 ."   AND pachis = inghis "
								 ."   AND ingnin = ".$wing;
							$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num1 = mysql_num_rows($res1);

							if($num1 > 0)
							{
								$row1 = mysql_fetch_array($res1);
								$whis=$row1[0];
								if (!isset($wing) or ($wing == ""))
									$wing=$row1[10];
								$wno1=$row1[1];
								$wno2=$row1[2];
								$wap1=$row1[3];
								$wap2=$row1[4];
								$wdoc=$row1[5];
								$wcodemp=$row1[6]; //Este es el Codigo de la empresa no el NIT
								$wnomemp=$row1[7];
								$wfecing=$row1[8];
								$wser=$row1[9];
								$wtar=$row1[11];

								if (!isset($wfeccar))
									$wfeccar=$wfecha;
							}
						}
					}
				}

				//*************************************************************************************************************************************************************************************************************************************************************************************

				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//INGRESO NRO:
				if(isset($wing))
				{
					$q ="SELECT count(*) "
					  ."   FROM ".$wbasedato."_000101 "
					  ."  WHERE inghis = '".$whistoria."'"
					  ."    AND ingnin = '".$wing."'";
					$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row = mysql_fetch_array($res);
					if($row[0] == 0)
					{
						$respuestaAjax[ 'mensaje' ] = "LA HISTORIA CON ESTE NUMERO DE INGRESO NO EXISTE";
					}
				}

				if (isset($wcodcon) and ($wcodcon != ""))
				{
					//21 de Marzo de 2012
					//Esta validacion permite identificar si el usuario puede grabar conceptos

					if ($wbod == 'on')
					{
						$q =" SELECT Grucod, Gruinv, Gruest"
						   ."   FROM ".$wbasedato."_000004"
						   ."  WHERE gruest = 'on' "
						   ."    AND grucod = '".$wcodcon."'"
						   ."    AND gruinv = 'on'";
						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
						$wccogra = $wcco;
					}
					else
					{
						$num=1;
					}

					if($num > 0)
					{
						if (!isset($wpaquete) or $wpaquete != "on")
						{
						  $q =  " SELECT grucod, grudes, gruarc, gruser, grutip, grumva, gruinv, gruabo, grutab, grumca "
							   ."   FROM ".$wbasedato."_000004 "
							   ."  WHERE gruest = 'on' "
							   ."    AND grucod = '".$wcodcon."'"
							   ."    AND gruser in ('A','H') "  //Solo trae los servicios de ambos (Pos y Hospitalario) u Hospitalario
							   ."  ORDER BY grudes ";
						}
						else
						{
							$q =  " SELECT  grucod, grudes, gruarc, gruser, grutip, grumva, gruinv, gruabo, grutab, grumca, "
								 ."         paqdetpro, pronom, paqdetvac, paqdetfec, paqdetvan "
								 ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000114, ".$wbasedato."_000103 "
								 ."  WHERE  gruest = 'on' "
								 ."    AND  grucod = '".$wcodcon."'"
								 ."    AND  gruser in ('A','H') "  //Solo trae los servicios de ambos (Pos y Hospitario) u Hospitario
								 ."    AND  grucod = paqdetcon "
								 ."    AND  paqdetcod = '".$wcodpaq."'"
								 ."    AND  paqdetest = 'on' "
								 //."    AND  (TRIM(mid(paqdettar,1,instr(paqdettar,'-')-1)) = '".$wtar."'"
								 //."     OR   TRIM(mid(paqdettar,1,instr(paqdettar,'-')-1)) = '*') "
								 ."    AND  (TRIM(paqdettar) = '".$wtar."'"
								 ."     OR   TRIM(paqdettar) = '*') "
								 ."    AND  paqdetpro = procod "
								 ."  ORDER  BY grudes ";
						}

						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

						if ($num > 0)
						{
							$row = mysql_fetch_array($res);
							$wcodcon = $row[0];   //Codigo del concepto
							$wnomcon = $row[1];   //Nombre del concepto
							$warctar = $row[2];   //Archivo para validar las tarifas
							$wconser = $row[3];   //Tipo de servicio (P)OS, (H)OSPITALARIO o (A)MBOS
							$wcontip = $row[4];   //Tipo de concepto (P)ropio o (C)ompartido
							$wconmva = $row[5];   //Indica si el valor se puede colocar al momento de grabar el cargo
							$wconinv = $row[6];   //Indica si mueve inventarios
							$wconabo = $row[7];   //indica si es un concepto de abono
							$wcontab = $row[8];   //Tipo de Abono
							$wconmca = $row[9];   //Indica si el concepto mueve caja

							if (isset($wpaquete) and $wpaquete == "on")
							{
								$wprocod = $row[10];
								$wpronom = $row[11];
								$wpvac   = $row[12];
								$wpfec   = $row[13];
								$wpvan   = $row[14];

								//if ($wfecha < $wpfec)   //Aca evaluo si tomo el valor anterior o el actual
								if ($wfeccar < $wpfec)    //Aca evaluo si tomo el valor anterior o el actual
								 $wvaltar = $wpvan;
								else
								   $wvaltar = $wpvac;
							}
						}
					}
				}

				////////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//PROCEDIMIENTO O INSUMO
				////////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if (isset($wprocod) and ($wprocod != "") and isset($warctar) and ($warctar != "" and ($wconabo == "off" or $wconabo == "")))
				{
					//ACA SEGUN EL ARCHIVO DE VALIDACION DE TARIFAS SE TRAEN LOS PROCEDIMIENTOS
					$q="";
					switch ($warctar)
					{
						case "000026":  //Tarifas de medicamentos y material
						{
							//Modificacion hecha el 5 de Junio de 2008
							//Con esto hago que si se ingreso el codigo del proveedor se busque en la homologacion y traiga el codigo de
							//la clinica, si no es el codigo del proveedor sigue buscando con el codigo ingreso la tarifa y el saldo.
							$q = " SELECT mid(axpart,1,instr(axpart,'-')-1) "
								."   FROM ".$wbasedato."_000009 "
								."  WHERE axpcpr = '".$wprocod."'"
								."    AND axpest = 'on' ";
							$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num = mysql_num_rows($res);
							if ($num > 0)
							{
								$row = mysql_fetch_array($res);
								$wprocod = $row[0];
							}

							//AVERIGUO SI EL ARTICULO MANEJA APROVECHAMIENTO
							$q = " SELECT artapv "
								."   FROM ".$wbasedato."_000001 "
								."  WHERE artcod = '".$wprocod."'"
								."    AND artest = 'on' ";
							$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num = mysql_num_rows($res);
							$rowapv = mysql_fetch_array($res);

							if ($rowapv[0] <> "on" or !isset($waprovecha))   //Si no maneja aprovechamiento
							{
								$q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec, karexi, 'C', '0' "
								   ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000007, ".$wbasedato."_000004 "
								   ."  WHERE artcod                            = '".$wprocod."'"
								   ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
								   ."    AND artest                            = 'on' "
								   //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
								   ."    AND arttip                            = grutia "                   //CAMBIO DE SEPTIEMBRE 5  DE 2007:
								   ."    AND grucod                            = '".$wcodcon."'"            //  "    "      "      "  "   "
								   ."    AND mtaest                            = 'on' "
								   //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
								   ."    AND karcco                            = '".$wccogra."'"
								   ."    AND karcod                            = artcod "
								   ."    AND karexi                            >= 0 "
								   ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = '".$wtar."'"
								   ."  ORDER BY 2 ";
							}
							else                //Si el articulo maneja aprovechamiento no valido todavia la existencia de cantidades
							{
								$q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec, 0, 'C', '0' "
									 ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000004 "
									 ."  WHERE artcod                            = '".$wprocod."'"
									 ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
									 ."    AND artest                            = 'on' "
									 //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
									 ."    AND arttip                            = grutia "                   //CAMBIO DE SEPTIEMBRE 5  DE 2007:
									 ."    AND grucod                            = '".$wcodcon."'"            //  "    "      "      "  "   "
									 ."    AND mtaest                            = 'on' "
									 //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
									 ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = '".$wtar."'"
									 ."  ORDER BY 2 ";
							}
							$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num = mysql_num_rows($res);

							if ($num==0)   //Si no existe el articulo con la tarifa del paciente lo busco con la tarifa asterisco (*)
							{
							   //On if ($rowapv[0] <> "on")   //Si no maneja aprovechamiento
							   if (!isset($waprovecha) or $waprovecha=="off")   //Si no maneja aprovechamiento
								{
								   $q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec, karexi, 'C', '0' "
										."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000007, ".$wbasedato."_000004 "
										."  WHERE artcod                            = '".$wprocod."'"
										."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
										."    AND artest                            = 'on' "
										//."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
										."    AND arttip                            = grutia "           //CAMBIO DE SEPTIEMBRE 5  DE 2007:
										."    AND grucod                            = '".$wcodcon."'"    //  "    "      "      "  "   "
										."    AND mtaest                            = 'on' "
										//."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
										."    AND karcco                            = '".$wccogra."'"
										."    AND karcod                            = artcod "
										."    AND karexi                            >= 0 "
										."    AND mid(mtatar,1,instr(mtatar,'-')-1) = '*' "
										."  ORDER BY 2 ";
								}
								else   //Si maneja aprovechamiento todavia no valido la existencia
								{
									 $q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec, 0, 'C', '0' "
										  ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000004 "
										  ."  WHERE artcod                            = '".$wprocod."'"
										  ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
										  ."    AND artest                            = 'on' "
										  //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
										  ."    AND arttip                            = grutia "           //CAMBIO DE SEPTIEMBRE 5  DE 2007:
										  ."    AND grucod                            = '".$wcodcon."'"    //  "    "      "      "  "   "
										  ."    AND mtaest                            = 'on' "
										  //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
										  ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = '*' "
										  ."  ORDER BY 2 ";
								}
							}
						}
						break;

						case "000104":  //Tarifas de procedimientos y examenes
						{
							if (!isset($wpaquete) or $wpaquete != "on")
							{
								$q = " SELECT proemptfa "
									."   FROM ".$wbasedato."_000070"
								    ."  WHERE proempcod = '".$wprocod."'"
								    ."    AND proempemp = '".$wcodemp."'"
								    ."    AND proempest = 'on' ";
								$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								$num = mysql_num_rows($res);

								if ($num > 0)
								{
									//Por aca ingresa si el codigo esta relacionado con la empresa por la que viene el paciente
									//y tiene prelación para tomar el valor a cobrar segun el tipo de liquidacion que diga en la
									//tabla _000070 - Relacion procedimientos - empresas
									$q =  " SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, 'CODIGO', propun "
									   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104, ".$wbasedato."_000070 "
									   ."  WHERE procod                                  = '".$wprocod."'"
									   ."    AND procod                                  = mid(tarcod,1,instr(tarcod,'-')-1) "
									   ."    AND proest                                  = 'on' "
									   ."    AND tarest                                  = 'on' "
									   ."    AND (mid(tarcon,1,instr(tarcon,'-')-1)      = '".$wcodcon."'  "
									   ."     OR  tarcon                                 = '".$wcodcon."') "
									   ."    AND (mid(tartar,1,instr(tartar,'-')-1)      = '".$wtar."'"
									   ."     OR  tartar                                 = '".$wtar."') "
									   ."    AND procod                                  = proempcod "
									   ."    AND proempemp                               = '".$wcodemp."'"
									   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = 'CODIGO'"

									   ." UNION "

									   ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, 'UVR', propun "
									   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104, ".$wbasedato."_000070 "
									   ."  WHERE procod                                  like '%".$wprocod."%'"
									   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = mid(tarcod,1,instr(tarcod,'-')-1) "
									   ."    AND proest                                  = 'on' "
									   ."    AND (mid(tarcon,1,instr(tarcon,'-')-1)      = '".$wcodcon."'  "
									   ."     OR  tarcon                                 = '".$wcodcon."') "
									   ."    AND (mid(tartar,1,instr(tartar,'-')-1)      = '".$wtar."'"
									   ."     OR  tartar                                 = '".$wtar."') "
									   ."    AND tarest                                  = 'on' "
									   ."    AND procod                                  = proempcod "
									   ."    AND proempemp                               = '".$wcodemp."'"
									   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = 'UVR'"
									   ."    AND propun                                 >= taruvi "
									   ."    AND propun                                 <= taruvf "

									   ." UNION "

									   //Modificacion JULIO 18 DE 2007
									   ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, 'UVR', propun "
									   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104, ".$wbasedato."_000070 "
									   ."  WHERE procod                                  LIKE '%".$wprocod."%'"
									   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = mid(tarcod,1,instr(tarcod,'-')-1) "
									   ."    AND proest                                  = 'on' "
									   ."    AND (mid(tarcon,1,instr(tarcon,'-')-1)      = '".$wcodcon."'  "
									   ."     OR  tarcon                                 = '".$wcodcon."') "
									   ."    AND (mid(tartar,1,instr(tartar,'-')-1)      = '".$wtar."'"
									   ."     OR  tartar                                 = '".$wtar."') "
									   ."    AND tarest                                  = 'on' "
									   ."    AND procod                                  = proempcod "
									   ."    AND proempemp                               = '".$wcodemp."'"
									   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = 'UVR'"
									   ."    AND taruvi                                  = 0 "
									   ."    AND taruvf                                  = 0 "
									   //=============================

									   ." UNION "

									   ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, 'GQX', propun "
									   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104, ".$wbasedato."_000070 "
									   ."  WHERE procod                                  LIKE '%".$wprocod."%'"
									   ."    AND mid(progqx,1,instr(progqx,'-')-1)       = mid(tarcod,1,instr(tarcod,'-')-1) "
									   ."    AND proest                                  = 'on' "
									   ."    AND (mid(tarcon,1,instr(tarcon,'-')-1)      = '".$wcodcon."'  "
									   ."     OR  tarcon                                 = '".$wcodcon."') "
									   ."    AND (mid(tartar,1,instr(tartar,'-')-1)      = '".$wtar."'"
									   ."     OR  tartar                                 = '".$wtar."') "
									   ."    AND tarest                                  = 'on' "
									   ."    AND procod                                  = proempcod "
									   ."    AND proempemp                               = '".$wcodemp."'"
									   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = 'GQX'"
									   ."  ORDER BY 2 ";
								}
								else
								{
									$q =  " SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, protfa, propun "
										 ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
										 ."  WHERE procod                             = '".$wprocod."'"
										 ."    AND protfa                             = 'CODIGO' "
										 ."    AND procod                             = mid(tarcod,1,instr(tarcod,'-')-1) "
										 ."    AND proest                             = 'on' "
										 ."    AND tarest                             = 'on' "
										 ."    AND (mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'  "
										 ."     OR  tarcon                            = '".$wcodcon."') "
										 ."    AND (mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
										 ."     OR  tartar                            = '".$wtar."') "

										 ." UNION "

										 ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, protfa, 'rango' "
										 ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
										 ."  WHERE procod                             LIKE '%".$wprocod."%'"
										 ."    AND protfa                             = 'UVR' "
										 ."    AND protfa                             = mid(tarcod,1,instr(tarcod,'-')-1) "
										 ."    AND proest                             = 'on' "
										 ."    AND (mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'  "
										 ."     OR  tarcon                            = '".$wcodcon."') "
										 ."    AND (mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
										 ."     OR  tartar                            = '".$wtar."') "
										 ."    AND tarest                             = 'on' "
										 ."    AND propun                            >= taruvi "
										 ."    AND propun                            <= taruvf "

										 ." UNION "

										 ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, protfa, propun "
										 ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
										 ."  WHERE procod                             LIKE '%".$wprocod."%'"
										 ."    AND protfa                             = 'UVR' "
										 ."    AND protfa                             = mid(tarcod,1,instr(tarcod,'-')-1) "
										 ."    AND proest                             = 'on' "
										 ."    AND (mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'  "
										 ."     OR  tarcon                            = '".$wcodcon."') "
										 ."    AND (mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
										 ."     OR  tartar                            = '".$wtar."') "
										 ."    AND tarest                             = 'on' "
										 ."    AND taruvi                             = taruvf "

										 ." UNION "

										 ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, protfa, propun "
										 ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
										 ."  WHERE procod                             LIKE '%".$wprocod."%'"
										 ."    AND protfa                             = 'GQX' "
										 ."    AND mid(progqx,1,instr(progqx,'-')-1)  = mid(tarcod,1,instr(tarcod,'-')-1) "
										 ."    AND proest                             = 'on' "
										 ."    AND (mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'  "
										 ."     OR  tarcon                            = '".$wcodcon."') "
										 ."    AND (mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
										 ."     OR  tartar                            = '".$wtar."') "
										 ."    AND tarest                             = 'on' "
										 ."  ORDER BY 2 ";
								}
							}  //Fin del then de if wpaquete != "on"
							else
							{  //Si entra aca es porque se liquida por PAQUETE
								$q =  " SELECT procod, procod, pronom, paqdetvac, 0, paqdetvan, paqdetfec, 0, 'PAQUETE', '0' "
									 ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000114 "
									 ."  WHERE procod    = '".$wprocod."'"
									 ."    AND procod    = paqdetpro "
									 ."    AND proest    = 'on' "
									 ."    AND paqdetest = 'on' "
									 ."    AND paqdetcon = '".$wcodcon."'"
									 //."    AND  (TRIM(mid(paqdettar,1,instr(paqdettar,'-')-1)) = '".$wtar."'"
									 //."     OR   TRIM(mid(paqdettar,1,instr(paqdettar,'-')-1)) = '*') "
									 ."    AND  (TRIM(paqdettar) = '".$wtar."'"
									 ."     OR   TRIM(paqdettar) = '*') "
									 ."    AND paqdetcod = '".$wcodpaq."'"
									 ."  ORDER BY 2 ";
							}
						}      //Fin del case de tarifas de la tabla 000104
						break;
					}

					if ($q=="")
					{
						$respuestaAjax[ 'mensaje' ] = "EL CONCEPTO O GRUPO NO TIENE ARCHIVO DE TARIFAS DEFINIDO";
					}
					else
					{
						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

						if ($num == 0)
						{
							$respuestaAjax[ 'mensaje' ] = "EL PROCEDIMIENTO O INSUMO NO EXISTE O NO TIENE ESTA TARIFA DEFINIDA o NO ESTA DEFINIDO PARA EL CONCEPTO SELECCIONADO o NO TIENE EXISTENCIA PARA EL CENTRO DE COSTOS SELECCIONADO o LA HOMOLAGACION ESTA MAL HECHA (Código definido como del proveedor)";
						}
					}

					if ($num > 0)
					{
						$row = mysql_fetch_array($res);

						$wprocod = $row[1];  //Codigo del insumo
						$wpronom = $row[2];  //Nombre del insumo
						$wprovac = $row[3];  //Valor Actual
						$wproiva = $row[4];  //% IVA
						$wprovan = $row[5];  //Valor Anterior
						$wprofec = $row[6];  //Fecha cambio de tarifa
						$wexiste = $row[7];  //Indica la cantidad existente cuando el concepto mueve inventarios o es del POS
						$wtipfac = $row[8];  //Indica como se factura el procedimiento o articulo (C)odigo, (G)rupo Qx, (U)VR
						$wpunuvr = $row[9];  //Numero de puntos que se facturan para el procedimiento seleccionado


						//****************
						//FEBRERO 18 DE 2009:====================================================
						if (isset($wdevol) and $wdevol=="on")
						{
							if (!isset($waprovecha))
							 $waprovecha="off";

							buscar_saldo_en_historia($wcodcon, $wprocod, $whistoria, $wing, $waprovecha);
							$wexiste=$wexidev;
						}
						//=====================================================================

						//==============================================================================================================
						//Como antes no validé la existencia, si el articulo es de aprovechamiento, aca traigo la existencia
						//==============================================================================================================
						/* //On  //FEBRERO 18 DE 2009:
						if (isset($rowapv[0]) and $rowapv[0]=="on")
						 {
						  $q = " SELECT karexi "
							  ."   FROM ".$wbasedato."_000007 "
							  ."  WHERE karcco = '".$wccogra."'"
							  ."    AND karcod = '".$wprocod."'"
							  ."    AND karexi > 0 ";
						  $resexi = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						  $num = mysql_num_rows($resexi);   // or die (mysql_errno()." - ".mysql_error());
						  if ($num > 0)
							 {
							  $rowexi=mysql_fetch_array($resexi);
							  $wexiste=$rowexi[0];
							 }
						 } */
						//==============================================================================================================
						// echo "<input type='HIDDEN' name='wexiste' value='".$wexiste."'>";
						// echo "<input type='HIDDEN' name='wtipfac' value='".$wtipfac."'>";

						if ($wconmva!="S")
						{
							switch ($wtipfac)
							{
								case "C":                                      //Para Medicamentos y Material MQX
							    {
									//if ($wfecha < $wprofec)                 //Aca evaluo si tomo el valor anterior o el actual
									if ($wfeccar < $wprofec)                  //Aca evaluo si tomo el valor anterior o el actual
									   $wvaltar = $wprovan;
									  else
										 $wvaltar = $wprovac;

								if (isset($wdevol) and $wdevol=="on")
								   $wvaltar=($wvaltar*(-1));
								}
								break;
								case "CODIGO":
								{
								//if ($wfecha < $wprofec)                 //Aca evaluo si tomo el valor anterior o el actual
								if ($wfeccar < $wprofec)                  //Aca evaluo si tomo el valor anterior o el actual
								   $wvaltar = $wprovan;
								  else
									 $wvaltar = $wprovac;
								}
								break;
								case "GQX":
								{
								//if ($wfecha < $wprofec)                 //Aca evaluo si tomo el valor anterior o el actual
								if ($wfeccar < $wprofec)                  //Aca evaluo si tomo el valor anterior o el actual
								   $wvaltar = $wprovan;
								  else
									 $wvaltar = $wprovac;
								}
								break;
								case "UVR":
								{
									//Las UVR se liquidan d e dos formas:
									//(1): Si esta la tarifa definida en un rango de UVR se toma el valor total grabado en el maestro de tarifas
									//     es decir NO se multiplica por UVR por un valor individual de la UVR.
									//(2): Si no se tiene un rango de UVR's se liquida la cantidad de UVR del procedimiento por un valor unitario
									//     de la UVR.
									//if ($wfecha < $wprofec)                 //Aca evaluo si tomo el valor anterior o el actual
									if ($wfeccar < $wprofec)                  //Aca evaluo si tomo el valor anterior o el actual
										if ($wpunuvr == 'rango')
											$wvaltar = $wprovan;                //Valor anterior del rango de UVR's
										else
											$wvaltar = $wprovan*$wpunuvr;     //Valor anterior por las UVR
									else
										if ($wpunuvr == 'rango')
											$wvaltar = $wprovac;              //Valor actual del rango de UVR's
										else
											$wvaltar = $wprovac*$wpunuvr;   //Valor actual por las UVR
								}
								break;
							}
						}

						// echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' VALUE='".$wprocod."' size = 6 ></td>";                                  //wcodcon
						// echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' VALUE='".$wpronom."' size = 20 ondblclick='enter()'></td>";   //wnomcon

						if ($wcontip == "C")
						{
						}
						else
						{
						}
					}
					else
					{
						// echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' size = 6  onchange='enter()'></td>";                                                     //wcodcon
						// echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' size = 20 onchange='enter()' ondblclick='enter()' ></td>";                      //wnomcon
					}
				}
				else
			    {
					if (isset($wpronom) and ($wpronom != "") and isset($warctar) and ($warctar != "") and ($wconabo == "off" or !isset($wconabo)))
				    {
						$q="";
						switch ($warctar)
						{
						 case "000026":  //Tarifas de medicamento y material
							 {
							  $q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec "             //CAMBIO DE SEPTIEMBRE 5  DE 2007:
								   ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000007, ".$wbasedato."_000004 "
								   ."  WHERE artnom                            like '%".$wpronom."%'"
								   ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
								   ."    AND artest                            = 'on' "
								   //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
								   ."    AND arttip                            = grutia "                         //CAMBIO DE SEPTIEMBRE 5  DE 2007:
								   ."    AND grucod                            = '".$wcodcon."'"                  //  "    "       "     "   "  "
								   ."    AND mtaest                            = 'on' "
								   //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
								   ."    AND karcco                            = '".$wccogra."'"
								   ."    AND karcod                            = artcod "
								   ."    AND karexi                            > 0 "
								   ."    AND (mid(mtatar,1,instr(mtatar,'-')-1) = '".$wtar."'"
								   ."     OR  mid(mtatar,1,instr(mtatar,'-')-1) = '*') "
								   ."  GROUP BY 1,2,3 "
								   ."  ORDER BY 2 ";
							 }
							 break;
						 case "000104":  //Tarifas de procedimiento y examenes
							 {
							  if (!isset($wpaquete) or $wpaquete != "on")
								  $q =  " SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec "
									   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
									   ."  WHERE pronom                            like '%".$wpronom."%'"
									   ."    AND protfa                            = 'CODIGO' "
									   ."    AND procod                            = mid(tarcod,1,instr(tarcod,'-')-1) "
									   ."    AND proest                            = 'on' "
									   ."    AND mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'"
									   ."    AND mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
									   ."    AND tarest                            = 'on' "

									   ." UNION "

									   ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec "
									   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
									   ."  WHERE pronom                            like '%".$wpronom."%'"
									   ."    AND protfa                            = 'UVR' "
									   ."    AND protfa                            = mid(tarcod,1,instr(tarcod,'-')-1) "
									   ."    AND proest                            = 'on' "
									   ."    AND mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'"
									   ."    AND mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
									   ."    AND tarest                            = 'on' "

									   ." UNION "

									   ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec "
									   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
									   ."  WHERE pronom                            like '%".$wpronom."%'"
									   ."    AND protfa                            = 'GQX' "
									   ."    AND mid(progqx,1,instr(progqx,'-')-1) = mid(tarcod,1,instr(tarcod,'-')-1) "
									   ."    AND proest                            = 'on' "
									   ."    AND mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'"
									   ."    AND mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
									   ."    AND tarest                            = 'on' "
									   ."  ORDER BY 2 ";
								 else
									$q =  " SELECT procod, procod, pronom, paqdetvac, 0, paqdetvan, paqdetfec "
										 ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000114 "
										 ."  WHERE pronom    like '%".$wpronom."%'"
										 ."    AND procod    = paqdetpro "
										 ."    AND proest    = 'on' "
										 ."    AND paqdetest = 'on' "
										 ."    AND paqdetcon = '".$wcodcon."'"
										 ."    AND paqdettar = '".$wtar."'"
										 ."    AND paqdetcod = '".$wcodpaq."'"
										 ."  ORDER BY 2 ";
							 }
							 break;
						}

					if ($q=="") //Si entre por aca es porque no tiene archivo definido el concepto
					   {
							$respuestaAjax[ 'mensaje' ] = "EL CONCEPTO O GRUPO NO TIENE ARCHIVO DE TARIFAS DEFINIDO";
					   }

					  else
						 {
						  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
						 }

					$sw=0;
					if ($num > 0)
					   {
						for ($i=1;$i<=$num;$i++)
						   {
							$row = mysql_fetch_array($res);
							if ($num == 1)
							   {
								$sw="1";
								$wprocod=$row[1];
								$wpronom=$row[2];
								// echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' value='".$wprocod."' size=6 ></td>";
								// echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' value='".$wpronom."' size=20 onchange='enter()'></td>";
							   }
							  else
								 {
								  if ($num > 1)                 //Si entra por aca es porque el concepto tiene varios registros con el nombre muy similar
									 $wprocod1[$i]=$row[1];     //$wprocod=$row[1];
								 }
							$wpronom1[$i]=str_replace(" ","%",$row[2]);
						   }
						if ($sw==0)
						   {
							// echo "<td align=left class=".$wcf." colspan=1><b></b><SELECT name='wpronom' onchange='enter()' ondblclick='enter()' >";

							// for ($i=1;$i<=$num;$i++)
							   // {
								// echo "<option>".$wpronom1[$i]."</option>";
								// $wpronom=str_replace(" ","%",$wpronom1[$i]);
							   // }
							// echo "</select></td>";
						   }
					   }
					  else
						 {
						  // echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' size=6  onchange='enter()'></td>";                        //wprocod
						  // echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' size=20 onchange='enter()'></td>";    //wpronom
						 }
				   }
				  else
					 {
					  if (!isset($wcodcon) or !isset($wconabo) or $wconabo == "off" or $wconabo == "")
						 {
						  // echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' size=6  value='' onchange='enter()'></td>";                          //wprocod
						  // echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' size=20 value='' onchange='enter()' ondblclick='enter()' ></td>";      //wpronom
						 }
						else
						   { //No tiene procedimiento porque es un abono
							// echo "<td align=left class=".$wcf." colspan=1>&nbsp</td>";
							// echo "<td align=left class=".$wcf." colspan=1>&nbsp</td>";
						   }

					  $wprocod = "";  //Codigo del insumo
					  $wpronom = "";  //Nombre del insumo
					  $wprovac = 0;   //Valor Actual
					  $wproiva = 0;   //% IVA
					  $wprovan = 0;   //Valor Anterior
					  $wprofec = "";  //Fecha cambio de tarifa
					  $wexiste = "";  //Indica la cantidad existente cuando el concepto mueve inventarios o es del POS
					  $wtipfac = "";  //Indica como se factura el procedimiento o articulo (C)odigo, (G)rupo Qx, (U)VR
					  $wpunuvr = 0;   //Numero de puntos que se fcaturan para el procedimiento seleccionado
					  $wvaltar = 0;
					 }
			    }

				/**************************************************************************************************
				 * GRABANDO LOS DATOS
				 **************************************************************************************************/
				if(isset($wgrabar))
				{
					if(isset($wconabo) && $wconabo=="on")
					{
						$wccogra==$wcco;
					}

					global $wdevol;
					global $waprovecha;
					global $wconmvto;
					global $wtrasladoauto;
					global $wauto;

					//SEPTIEMBRE  4  DE 2007:
					validar_datos_a_grabar();

					if ($wok=="on")   //Si entra es porque paso la validación de todos los campos
					//===========================================================================
					{

						//SEPTIEMBRE 5  DE 2007: Valido que si es por aprovechamiento pueda garbarse
						if (isset($waprovecha) and ($waprovecha=="on") and (!isset($wdevol) or $wdevol=="off"))
						{
							$wexiste=$wcantidad;
						}

						// FEBRERO 8 DE 2012: Declaracion de variables que permiten el proceso de traslado automatico Jonatan Lopez -------
						$wccotrasladar = consultarAliasAplicacion($conex, $wemp_pmla, 'TrasladoAutomaticoenCargo');
						$wbodega = consultarAliasAplicacion($conex, $wemp_pmla, 'BodegaPrincipal');
						$wconceptraslado = consultarAliasAplicacion($conex, $wemp_pmla, 'ConceptodeTraslado');

						if($wccotrasladar == true and $wbodega == true and $wconceptraslado == true and $wccogra == $wccotrasladar and (!isset($waprovecha) or $waprovecha=="off") and (isset($wconinv) and $wconinv=="on") and $wbod != 'on')
						{
							//Funcion que permite traslado automatico desde un centro de costos declarado en $wbodega hacia $wccotrasladar.
							trasladoautomatico($wexiste, $wdevol, $wbodega, $wconceptraslado, $wccogra, $wvaltar, $wno2, $waprovecha, $wconinv, $wpaquete, $wprocod, $wfecha, $hora, $wconmvto, $wcantidad, $wusuario, $whistoria, $wing );
							$wauto = 'on';
						}

						if ($wbod == 'on' and (!isset($waprovecha) or $waprovecha=="off") and (isset($wconinv) and $wconinv=="on"))
						{
							//$wccogra = $wcco;
							$wccotrasladar = $wcco;

							trasladoautomatico($wexiste, $wdevol, $wbodega, $wconceptraslado, $wccogra, $wvaltar, $wno2, $waprovecha, $wconinv, $wpaquete, $wprocod, $wfecha, $hora, $wconmvto, $wcantidad, $wusuario, $whistoria, $wing );
							$wauto = 'on';
						}


						//---------------------------------------
						//On if ((isset($wexiste) and ($wexiste >= $wcantidad or isset($waprovecha))and ($wconser == "A"or $wconser == "H")) or ($wconinv!="on" and $wconser=="H")) //tipo de servicio es (P)os o (A)mbos y valido que la cantidad a grabar sea menor a la que existe en el kardex
						if((isset($wexiste) and ($wexiste >= $wcantidad) and ($wconser == "A" or $wconser == "H")) or ($wconinv!="on" and $wconser=="H")) //tipo de servicio es (P)os o (A)mbos y valido que la cantidad a grabar sea menor a la que existe en el kardex
						{
							//echo "<script>alert ('despues de existe $wexiste');</script>";
							if(isset($wconinv) and $wconinv=="on" and (!isset($waprovecha) or $waprovecha=="off"))  //Si mueve inventarios y no es un aprovechamiento
							{
								//Si es un concepto de devolucion se multiplica la cantidad por menos 1
								$q = "lock table ".$wbasedato."_000007 LOW_PRIORITY WRITE";
								$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

								//DESCARGO DEL KARDEX
								//===================================================================================================================================
								//ACTUALIZO EN LA TABLA DEL -- <SALDOS EN LINEA> -- DEL  **** KARDEX ****
								if ($wauto == 'on' and !isset($wdevol) and $wdevol!="on")
								{
									$q= " UPDATE ".$wbasedato."_000007 "
									   ."    SET karexi = karexi - ".$wcantidad
									   ."  WHERE karcco = '".$wccogra."'"
									   ."    AND karcod = '".$wprocod."'"
									   ."    AND karexi > 0 ";
									$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$q= " UNLOCK TABLES";
									$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								}
								elseif ($wauto == 'on' and isset($wdevol) and $wdevol=="on")
								{

								}
								else{
									if(isset($wdevol) and $wdevol=="on")
									{
									  $q= " UPDATE ".$wbasedato."_000007 "
										 ."    SET karexi  = karexi - ".($wcantidad*(-1))
										 ."  WHERE karcco  = '".$wccogra."'"
										 ."    AND karcod  = '".$wprocod."'"
										 ."    AND karexi >= 0 ";

									}
									//NO PERMITE REALIZAR NINGUN PROCESO SI EL CENTRO DE COSTOS DE LA VARIABLE $wccotrasladar ES IGUAL A $wccogra 14 Feb 2012
									else
									{
										$q= " UPDATE ".$wbasedato."_000007 "
										   ."    SET karexi = karexi - ".$wcantidad
										   ."  WHERE karcco = '".$wccogra."'"
										   ."    AND karcod = '".$wprocod."'"
										   ."    AND karexi > 0 ";
									}

									$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$q= " UNLOCK TABLES";
									$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								}


								/////////////////////////////////////////////////////////////////////////////////
								//ACTUALIZO Y TOMO EL CONSECUTIVO Y EL CODIGO DEL CONCEPTO DE VENTA EN INVENTARIO
								$q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
								$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								if(isset($wdevol) and $wdevol=='on')
								{
									//Traigo el Concepto de Salida para las ventas
									$q= " SELECT concod "
									   ."   FROM ".$wbasedato."_000008 "
									   ."  WHERE conmve = 'on' ";
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$row = mysql_fetch_array($err);
									$wconven=$row[0];

									$q= " UPDATE ".$wbasedato."_000008 "
									   ."    SET concon = concon + 1 "
									   ."  WHERE concan = '".$wconven."'";          //Concepto de Salida
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									//Busco el concepto de entrada (devolucion) basado en el concepto de salida para las ventas
									$q= " SELECT concon, concod "
									   ."   FROM ".$wbasedato."_000008 "
									   ."  WHERE concan = '".$wconven."'";          //Concepto de Salida
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$row = mysql_fetch_array($err);
									$wnromvto=$row[0];
									$wconmvto=$row[1];                              //Concepto de entrada (Devolucion)
								}
								else
								{
									$q= " UPDATE ".$wbasedato."_000008 "
									   ."    SET concon = concon + 1 "
									   ."  WHERE conmve = 'on' ";
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									$q= " SELECT concon, concod "
									   ."   FROM ".$wbasedato."_000008 "
									   ."  WHERE conmve = 'on' ";
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$row = mysql_fetch_array($err);
									$wnromvto=$row[0];
									$wconmvto=$row[1];                            //Concepto de Salida (Ventas)
								}
								$q = " UNLOCK TABLES";
								$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							}
							else
							{
								$wnromvto="";
								$wconmvto="";
							}

							if(($wcantidad*$wvaltar) > 0 or $wdevol=="on")
							{
								if (!isset($wno2) or $wno2 == "")
									$wno2=" ";

								if (trim($wccogra) == "")
									$wccogra=$wcco;

								//SEPTIEMBRE 5  DE 2007:
								if (!isset($waprovecha))
									$waprovecha="off";

								//FEBRERO 16 DE 2009:
								if (!isset($wdevol) or $wconinv=="off")
									$wdevol="off";

								$q= " INSERT INTO ".$wbasedato."_000106 (   Medico       ,   Fecha_data ,   Hora_data,   tcarusu     ,   tcarhis       ,   tcaring  ,   tcarfec     ,   tcarsin ,   tcarres                 ,   tcarno1 ,   tcarno2  ,   tcarap1 ,   tcarap2 ,   tcardoc ,   tcarser    ,   tcarconcod ,   tcarconnom ,   tcarprocod ,   tcarpronom ,   tcartercod ,   tcarternom ,   tcarterpor ,   tcarcan      ,   tcarvun    ,   tcarvto                      ,   tcarrec                ,   tcarfac                    ,   tcartfa    ,tcarest,   tcarnmo     ,   tcarcmo     ,    tcarapr       ,   tcardev   , Seguridad) "
								   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$whistoria."' ,'".$wing."' ,'".$wfeccar."' ,'".$wser."','".$wcodemp."-".$wnomemp."','".$wno1."','".$wno2."' ,'".$wap1."','".$wap2."','".$wdoc."','".$wccogra."','".$wcodcon."','".$wnomcon."','".$wprocod."','".$wpronom."','".$wcodter."','".$wnomter."','".$wporter."','".$wcantidad."','".$wvaltar."','".round($wcantidad*$wvaltar)."','".strtoupper($wrecexc)."','".strtoupper($wfacturable)."','".$wtipfac."','on'   ,'".$wnromvto."','".$wconmvto."', '".$waprovecha."','".$wdevol."', 'C-".$wusuario."')";
								$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							}

							$wid=mysql_insert_id();   //Esta función devuelve el id despues de un insert, siempre y cuando el campo sea de autoincremento

							//////////////////////////////////////////////////////////////////////////////////////
							//SI ES UN CARGO CORRESPONDIENTE A UN ** PAQUETE ** ACA GRABO EL MOVIMIENTO DE PAQUETE
							//////////////////////////////////////////////////////////////////////////////////////
							if(isset($wpaquete) and $wpaquete=="on")
							{
								$q= " INSERT INTO ".$wbasedato."_000115 (   Medico       ,   Fecha_data,   Hora_data,   movpaqhis  ,   movpaqing,    movpaqcod ,  movpaqreg,   movpaqcon  , movpaqest, Seguridad        ) "
								   ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,".$whistoria.",".$wing."   ,'".$wcodpaq."',".$wid."   ,'".$wcodcon."', 'on'     , 'C-".$wusuario."')";
								$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							}


							/////////////////////////////////////////////////////////////////////////////////
							//ACA DESCARGO DEL INVENTARIO ===================================================
							/////////////////////////////////////////////////////////////////////////////////
																	 //SEPTIEMBRE 5  DE 2007:
							if(isset($wconinv) and $wconinv=="on" and (!isset($waprovecha) or $waprovecha=="off"))
							{
								//===================================================================================================================================
								//===================================================================================================================================
								//GRABO EN LA TABLA DEL -- <ENCABEZADO> -- DEL **** MOVIMIENTO DE INVENTARIOS ****
								$q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     , mendan, menpre, mennit, menusu, menfac, menest, Seguridad        ) "
								   ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvto."','".$wconmvto."','".$wccogra."','".$wccogra."', '.'   , 0     , '0'   , '.'   , '.'   ,   'on', 'C-".$wusuario."')";
								$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

								//===================================================================================================================================
								//===================================================================================================================================
								//GRABO EN LA TABLA DEL -- <DETALLE> -- DE  **** MOVIMIENTO DE INVENTARIOS ****
								//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
								//============================================================================================================================

								//=========================================
								//TRAIGO EL COSTO PROMEDIO DEL ARTICULO
								$q= "SELECT karpro "
									 ."  FROM ".$wbasedato."_000007 "
									 ." WHERE karcco = '".$wccogra."'"
									 ."   AND karcod = '".$wprocod."'";
									 //."   AND karexi > 0 ";
								$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								$row_cos = mysql_fetch_array($res2);

								if ($row_cos[0] == "")
									$row_cos[0]=0;

								//=========================================
								//GRABO EL DETALLE DEL ARTICULO
								$q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan      ,  mdevto                    , mdepiv , mdeest, Seguridad        ) "
								   ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconmvto."','".$wnromvto."','".$wprocod."',".$wcantidad.",".($wcantidad*$row_cos[0]).", '0'    , 'on'  , 'C-".$wusuario."')";
								$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

								//DEVOLUCION EN CASO DE ESTAR ACTIVA LA FUNCION trasladoautomatico
								if(isset($wdevol) and $wdevol=="on" and $wauto =='on' or $wexidev > 0)
								{
									$q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									//Traigo el Concepto de translado
									$q= " SELECT concod "
									   ."   FROM ".$wbasedato."_000008 "
									   ."  WHERE concod = ".$wconceptraslado." ";
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$row = mysql_fetch_array($err);
									$wcont=$row[0];

									$q= " UPDATE ".$wbasedato."_000008 "
									   ."    SET concon = concon + 1 "
									   ."  WHERE concod = '".$wcont."'";          //Concepto de Salida
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									//Busco el concepto de entrada (devolucion) basado en el concepto de salida para las ventas
									$q= " SELECT concon, concod "
									   ."   FROM ".$wbasedato."_000008 "
									   ."  WHERE concod = '".$wcont."'";          //Concepto de Salida
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$row = mysql_fetch_array($err);
									$wnromvto=$row[0];
									$wconmvto=$row[1];                              //Concepto de entrada (Devolucion)

									$q = " UNLOCK TABLES";
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									//=============================================================================================================
									//Recalculo el costo promedio del articulo en el centro de costos destino (Bodega)

									// Consulto que hay en el centro de costos origen (1270)
									$q= " SELECT karexi, karpro "
										."  FROM ".$wbasedato."_000007 "
										." WHERE karcco = '".$wccogra."'"
										."   AND karcod = '".$wprocod."'";
									$res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$row_cos = mysql_fetch_array($res_cos);
									$wexist_actuales_origen = $row_cos[0];
									$wcosto_pro_actual_origen = $row_cos[1];

									//Consulto que hay en el centro de costos destino (Bodega)
									$q7= "SELECT karexi, karpro "
										."  FROM ".$wbasedato."_000007 "
										." WHERE karcco = '".$wbodega."'"
										."   AND karcod = '".$wprocod."'";
									$res_cos = mysql_query($q7,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q7." - ".mysql_error());
									$row_cos = mysql_fetch_array($res_cos);
									$wexist_actuales_destino = $row_cos[0];
									$wcosto_pro_actual_destino = $row_cos[1];

									//Nuevo costo promedio del articulo en el centro de costos destino (Bodega)
									$wnuevocospro = (($wexist_actuales_destino * $wcosto_pro_actual_destino) + ($wcantidad * $wcosto_pro_actual_origen))/($wexist_actuales_destino + $wcantidad);
									$wnuevocospro = round($wnuevocospro,4);

									$q1= " UPDATE ".$wbasedato."_000007 "
										."    SET Karpro = ".$wnuevocospro." "
										."  WHERE Karcco = '".$wbodega."'"
										."    AND Karcod = '".$wprocod."'";
									$err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									//Se actualizan las existencias del centro de costos destino, en este caso la bodega.
									$q1= " UPDATE ".$wbasedato."_000007 "
									   ."    SET karexi = karexi - ".($wcantidad*(-1))
									   ."  WHERE karcco = '".$wbodega."'"
									   ."    AND karcod = '".$wprocod."'";
									$res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());


									//=============================================================================================================


									$q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     , mendan, menpre, mennit, menusu, menfac, menest, Seguridad        ) "
																." VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvto."','".$wconmvto."','".$wccogra."','".$wbodega."', '.'   , 0     , '0'   , '.'   , '.'   ,   'on', 'C-".$wusuario."')";
									$res = mysql_query($q,$conex) or die ("Error en la 10 para la funcion translado devolucion: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									//GRABO EL DETALLE DEL ARTICULO
									$q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan      ,  mdevto                    , mdepiv , mdeest, Seguridad        ) "
															 ."    VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconmvto."','".$wnromvto."','".$wprocod."',".$wcantidad.",".($wcantidad*$wcosto_pro_actual_origen).", '0'    , 'on'  , 'C-".$wusuario."')";
									$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

							    }
							}

							//**************************
							//Aca grabo la auditoria
							//**************************
							$q= " INSERT INTO ".$wbasedato."_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis       ,   auding  ,   audreg  , audacc ,   audusu     , Seguridad) "
							 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$whistoria."','".$wing."','".$wid."', 'Grabo','".$wusuario."', 'C-".$wusuario."')";
							$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

							$wcodcon    ="";
							$wnomcon    ="";
							$wccogra    ="";
							$wprocod    ="";
							$wpronom    ="";
							$wcodter    ="";
							$wporter    ="";
							$wcantidad  ="";
							$wrecexc    ="";
							$wfacturable="";

							unset($wcodcon);
							unset($wnomcon);
							unset($wccogra);
							unset($wprocod);
							unset($wpronom);
							unset($wcodter);
							unset($wnomter);
							unset($wporter);
							unset($wcantidad);
							unset($wvaltar);
							unset($wrecexc);
							unset($wfacturable);
							unset($wgrabar);
							unset($wdevol);
							unset($waprovecha);   //SEPTIEMBRE 5  DE 2007:
						}
						else
						{
							//FEBRERO 18 DE 2009: la parte THEN del if
							if(isset($wdevol) and $wdevol=="on" and $wexidev==0)    //Esto lo hago para sacar el mensaje adecuado segun el tipo de concepto y si tiene saldo en la historia
							{
								$respuestaAjax[ 'mensaje' ] = "LA HISTORIA NO TIENE CARGADA LA CANTIDAD DIGITADA";
							}
							else
							{
								$respuestaAjax[ 'mensaje' ] = "LA CANTIDAD DIGITADA ES MAYOR A LA EXISTENTE EN EL INVENTARIO";
							}
						}
					}
				}

				echo json_encode( $respuestaAjax );

			}
			break;
			case 13:
				$data = array("hora"=>"");
				$data['hora'] = pedirHora();
				echo json_encode( $data );
			break;
		}
	}
	else{

?>
<head>
  <title>SALDOS DE INSUMOS POR PACIENTE</title>
</head>
<body>
<style>
	.botona{
		font-size:13px;
		font-family:Verdana,Helvetica;
		font-weight:bold;
		color:white;
		background:#638cb5;
		margin-left: 1%;
	}

	.inputHabilitado{
		background-color: FAEF90;
	}

	.inputDesHabilitado{
		background-color:ffffff;
	}
</style>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript">
	function enter()
	{
		document.forms.cargos.submit();
	}

	function MostrarPaquetes(wcodpaq,wbasedato)
   	{
	   	opciones = " width=700, ";
		opciones = opciones+" height=300,";
		//var objBoton= document.getElementById('botBuscarPac');
	    opciones = opciones+" top=150,";
		opciones = opciones+" left=200,";
		opciones = opciones+" status=Yes,";
		opciones = opciones+" menubar=No,";
		opciones = opciones+" resizable=yes,";
		opciones = opciones+" scrollbars=yes,";
		opciones = opciones+" alwaysRaised";

		document.cargos.method='POST';
		document.cargos.action="GetPaquetes.php?wcodpaq="+wcodpaq+"&wbasedato="+wbasedato;

		var winPaquetes=window.open('', 'Paquetes', opciones);
		document.cargos.target="Paquetes";
		document.cargos.submit();
   	}

   	function cerrarVentana()
	{
      top.close()
     }


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
		alert( "Error: " + e );
		return false;
	}
}

/******************************************************************************************
 * Ejecuta un cargo o una devolución según el caso
 ******************************************************************************************/
function realizarCargoDevolucion( filaAct, dev ){

	//Creo la url para el ajax
	var params = cearUrlPorCampos( filaAct );

	//adiciono los datos extras
	params = params+"&wemp_pmla="+$( "#wemp_pmla" ).val()+"&consultaAjax=10";

	consultasAjax( "POST",
				   "./saldosInsumosPaciente.php",
				   params,
				   true,
				   function( ajax ){
						if( ajax.readyState==4 && ajax.status==200 ){

							if( ajax.responseText != '' ){

								contador--;

								eval( "var datos = "+ajax.responseText );
								if( datos.mensaje != '' ){
									$( "input[type='checkbox']", filaAct ).attr( "checked", false );
									mensajesAjax += datos.mensaje+".\n";
								}
								else{
									var saldoAnterior = $( "[name='canSal']", filaAct ).html()*1
									var devAnterior = $( "[name='canDev']", filaAct ).html()*1
									hora = pedirHora();

									$( "[name='canSal']", filaAct ).html( saldoAnterior - $( "[name='wcantidad']", filaAct ).val()*1 );
									$( "[name='canDev']", filaAct ).html( devAnterior + $( "[name='wcantidad']", filaAct ).val()*1 );

									$( "input[type='checkbox']", filaAct ).attr( "checked", false );
									$( "input[type='checkbox']", filaAct ).attr( "disabled", "disabled" );

									//ACTUALIZACION EN TABLA DETALLE
									filaHija      = $(filaAct).attr("fila");
									codigoUsuario = $( "#wcodigo_usuario" ).val();
									nombreUsuario = $( "#wnombre_usuario" ).val();
									htmlNuevoTr   =  "<tr><td align='center' class='fila1'  width='25%' height='100%' style='font-size:12px; padding:0;'>"+hora+"</td>";
									htmlNuevoTr  += "<td align='center' class='fila1'  width='15%' height='100%' style='font-size:12px; padding:0;'>"+saldoAnterior+"</td>";
									htmlNuevoTr  += "<td align='center' class='fila1'  width='60%' height='100%' style='font-size:12px; padding:0;'>( "+codigoUsuario+" ) "+nombreUsuario+"</td></tr>";
									$("#tabla_"+filaHija).append( htmlNuevoTr );
									trFecha = $("#tabla_"+filaHija).parent().parent().show();

									if( dev ){
										// alert( "La devolcuión de articulo"+$( "[name='wprocod']" ).val()+" - "+$( "[name='wpronom']" ).val() + " se ha realizado con exito" );
										mensajesAjax += "La devolcuión de articulo "+$( "[name='wprocod']", filaAct ).val()+" - "+$( "[name='wpronom']", filaAct ).val() + " se ha realizado con exito.\n";
									}
									else{
										// alert( "El saldo del articulo "+$( "[name='wprocod']" ).val()+" - "+$( "[name='wpronom']" ).val() +" se ha traslado al ingreso siguiente" );
										mensajesAjax += "El saldo del articulo "+$( "[name='wprocod']", filaAct ).val()+" - "+$( "[name='wpronom']", filaAct ).val() +" se ha traslado al ingreso siguiente.\n";
									}
								}

								if( contador == 0 ){
									alert( mensajesAjax );
								}
								$.unblockUI();
							}
						}
				   }
	);
}

function devolver( campo ){

	var fila = campo.parentNode.parentNode;

	if( $( "[name='wcantidad']", fila ).val() > 0 ){

		//activo la variable para devolver el articulo
		$( "[name='wdevol']", fila ).val( 'on' );

		realizarCargoDevolucion( fila, true );

		//dejo la variable nuevamente como estaba
		$( "[name='wdevol']", fila ).val( '' );
	}
	else{
		alert( "No hay saldo del articulo a devolver" );
	}
}

function devolverCargar( campo ){

	var fila = campo.parentNode.parentNode;

	if( $( "[name='wcantidad']", fila ).val() > 0 ){

		//Devuelvo el articulo para el saldo actual
		devolver( campo );

		//activo la variable para devolver el articulo
		$( "[name='wdevol']", fila ).val( 'off' );

		//Dejo la variable devolver en off ya que no se desea hacer una devolución si no un cargo
		var ant = $( "[name='wing']", fila ).val();	//ingreso actual

		$( "[name='wing']", fila ).val( ant*1 + 1 );

		realizarCargoDevolucion( fila, false );

		$( "[name='wdevol']", fila ).val( '' );
		$( "[name='wing']", fila ).val( ant );
		$.unblockUI();
	}
	else{
		alert( "No hay saldo del articulo a devolver" );
	}
}

function marcarTodos( campo, nameCampo, nameCampo2 ){

	if( campo.checked )
	{
		filtro1 = ":not(:checked)";
		filtro2 = ":checked";
	}else
		{
			filtro1 = ":checked";
			filtro2 = ":not(:checked)";
		}

	var tipo = $(campo).attr( "tipo" );
	$( "#cargosPaciente" ).find( "[name="+nameCampo+"][tipo!="+tipo+"]:enabled"+filtro1 ).attr( "checked", campo.checked );
	$( "#cargosPaciente" ).find( "[name="+nameCampo+"][tipo!="+tipo+"]:enabled"+filtro2 ).click();
	$( "#cargosPaciente" ).find( "[name="+nameCampo+"][tipo!="+tipo+"]:enabled"+filtro1 ).attr( "checked", campo.checked );

	if( campo.checked)
		$( "[name="+nameCampo2+"][tipo='todos']", $( "#cargosPaciente" ) ).attr( "checked", !campo.checked );
}

function soloUnCheckbox( campo ){

	var chk   = jQuery(campo);
	var val   = campo.checked;
	var tipo  = chk.attr("name");
	var valor;
	var input;

	if(chk.is(":checked"))
	{
		if( tipo == "cbDev")
		{
			valor = chk.parent().prev( "td" ).html()*1;
			input = jQuery(chk.parent().next("td").children("input"));
			input.removeAttr("disabled");
		}else{
				valor = chk.parent().prev( "td" ).prev( "td" ).prev( "td" ).html()*1;
				input = jQuery(chk.parent().prev("td").children("input"));
				input.attr("disabled", "disabled");
			 }

		input.val(valor);
		input.removeClass("inputDesHabilitado");
		input.addClass("inputHabilitado");

	}else{
			if( tipo == "cbDev")
			{
				input = jQuery(chk.parent().next("td").children("input"));
			}else
				{
					input = jQuery(chk.parent().prev("td").children("input"));
				}
			input.val("");
			input.attr("disabled", "disabled");
			input.removeClass("inputHabilitado");
			input.addClass("inputDesHabilitado");
		 }
	  $( "input[type='checkbox']", campo.parentNode.parentNode ).attr( "checked", false );
	  campo.checked = val;
}

function ejecutarFunciones(){

	contador = 0;
	mensajesAjax = '';

	var tbTabla = document.getElementById( "cargosPaciente" );

	//Busco todos los elementos que se encuentran input tipo checkbox que esten checkeados
	var jqElements = $(tbTabla).find( "input[type=checkbox]:checked" );

	//Solo proceso si hay elementos seleccionados
	if( jqElements.length > 0 ){

		if( confirm( "Desea ejecutar las acciones correspondiente" ) ){

			jqElements.each(function(x){

				//Contador que indica el total de peticiones ajax
				contador++;

				$.blockUI({ message: $( "#msg" )});

				//Si solo se va a devolver el articulo
				if( this.name == 'cbDev' ){
					devolver( this );
				}
				else{
					//Si va a devolver y cargar los articulos al ingreso siguiente
					devolverCargar( this );
				}
			});
		}
	}
	else{
		alert( "Debe seleccionar por lo menos un elemento." );
	}
}

function mostrarDetalle( tipoDetalle, tipoDetalle2, padre, tdPadre ){
	resumen  = jQuery(tdPadre);
	cantidad = resumen.html()*1;
	if( cantidad == 0 ){
		alert( "Sin Datos por mostrar" );
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

function pedirHora(){
	var horaRetornada = "";
	$.ajax({
				url: "saldosInsumosPaciente.php",
			   type: "POST",
			  async: false,
			   data: {

					 consultaAjax: "13",
						wemp_pmla: $( "#wemp_pmla" ).val()
					 },
			success: function ( data ){
						horaRetornada =  data.hora;
					 },
		   dataType: "json"
		});
	return(horaRetornada);
}

window.onload = function(){
	$(".solofloat").keyup(function(){
			if ($(this).val() !="")
				{
					$(this).val($(this).val().replace(/^(0)|[^0-9]/g, ""));
				}
			});

	$(".solofloat").focusout(function(){
		var saldo;
			if ( $(this).val() !="" ){
				saldo = $(this).parent().prev( "td" ).prev( "td" ).html()*1;
				if ( $(this).val()*1 > saldo ){
					alert('La cantidad a aplicar debe ser menor o igual al saldo disponible');
					$(this).val( saldo );
				}
			}
	});
}
</script>
<?php

		$wactualiz=" 2013-05-24 ";

		// Definición del encabezado del aplicativo
		encabezado( "SALDOS DE INSUMOS POR PACIENTE", $wactualiz, "clisur" );
		$wcodigo_usuario = $wuser;
		$wnombre_usuario = consultarNombreUsuario( $wcodigo_usuario );
		echo "<form name='cargos' action='' method=post>";

		echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='HIDDEN' name='wcodigo_usuario' id='wcodigo_usuario' value='".$wcodigo_usuario."'>";
		echo "<input type='HIDDEN' name='wnombre_usuario' id='wnombre_usuario' value='".$wnombre_usuario."'>";

		//Si no se ha ingresao historia e ingreso se piden los datos
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
			echo "<tr><td align='center' colspan='2'><INPUT type='submit' value='Consultar'></td></tr>";
			echo "</table>";

			echo "<br>";

			echo "<table align='center'>";
			echo "<tr align='center'><td><INPUT type='button' value='Cerrar' onClick='cerrarVentana()'></td></tr>";
			echo "</table>";
		}
		else{

			//Muestro los articulos que necesita el paciente
			//Consulto todo lo que tiene cargado el paciente
			$sql = "SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, SUM( can - dev ) as can, Artuni, Tcarconcod, Tcarconnom, Tcarser, SUM( can ) AS grabados, SUM( dev )as devueltos
					FROM
					(
						SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, SUM(tcarcan) as can, 0 as dev, Artuni, Tcarconcod, Tcarconnom, Tcarser
						   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
						  WHERE tcarhis    = '$his'
							AND tcaring    = '$ing'
							AND tcardev   != 'on'
							AND tcarapr   != 'on'
							AND Tcarprocod = artcod
					   GROUP BY 1
						  UNION
						 SELECT Tcarprocod, Tcarpronom, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, 0 as can, SUM(tcarcan) as dev, Artuni, Tcarconcod, Tcarconnom, Tcarser
						   FROM {$wbasedato}_000106 a, {$wbasedato}_000001 b
						  WHERE tcarhis    = '$his'
							AND tcaring    = '$ing'
							AND tcardev    = 'on'
							AND tcarapr   != 'on'
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
				$nom = $rows[' Tcarno1']." ".$rows[' Tcarno2']." ".$rows['Tcarap1']." ".$rows['Tcarap2']." ";

				$query = "SELECT Pacsex sexo, Pacfna fechaNacimiento, Pacap1, Pacap2, Pacno1, Pacno2, Pactam
							FROM {$wbasedato}_000100
						   WHERE Pachis = '{$his}'";
				$rs  = mysql_query( $query, $conex );
				$row = mysql_fetch_array( $rs );
				$arregloDetalle = array();
				$nom  = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2']." ";
				$edad = calcular_edad( $row['fechaNacimiento'] );
				( $puedeDevolver ) ? $moverSaldo = "" : $moverSaldo = "disabled";

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

				echo "<br>";
				echo "<br>";

				mysql_data_seek( $res,0 );
				/**************************************************************************************************************/

				$cargarIngresoSiguiente = tieneIngresoSiguiente( $his, $ing+1 );
				//$cargarIngresoSiguiente = true; //........hay que activar la línea

				echo "<table align='center' id='cargosPaciente' style='border: 1px solid; border-color:#2A5DB0;'>";

				echo "<tr class='encabezadotabla' align='center'>";
				echo "<td style='width:100'>C&oacute;digo</td>";
				echo "<td>Descripci&oacute;n</td>";
				echo "<td>Can. Grabada</td>";
				echo "<td>Can. Aplicada</td>";
				echo "<td>Can. Devuelta</td>";
				echo "<td>Saldo</td>";
				echo "<td>Devolver<br><INPUT type='checkbox' name='cbDev' {$moverSaldo} tipo='todos' onClick='marcarTodos( this, \"cbDev\", \"cbIngSig\");'></td>";
				echo "<td>Can. a Devolver</td>";

				if( $cargarIngresoSiguiente ){
					echo "<td>Cargar al<br>siguiente ingreso<br><INPUT type='checkbox' {$moverSaldo} tipo='todos' name='cbIngSig' onClick='marcarTodos( this, \"cbIngSig\", \"cbDev\" );'></td>";
				}

				echo "</tr>";

				for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$canAplicada = consultarCantidadAplicada( $his, $ing, $rows[ 'Tcarprocod' ] );
					consultarCantidadDevuelta( $his, $ing, $rows[ 'Tcarprocod' ], $rows[ 'devueltos' ] );

					if( $rows[ 'can' ] - $canAplicada >= 0 ){

						$class = "class='fila".($i%2+1)."'";

						echo "<tr $class fila='{$i}' detalle='false' >";

						//Campos ocultos
						echo "<td style='display:none'>";
						echo "<INPUT type='hidden' name='whistoria' value='$his'>";
						echo "<INPUT type='hidden' name='wing' value='$ing'>";
						echo "<INPUT type='hidden' name='wprocod' value='{$rows[ 'Tcarprocod' ]}'>";
						echo "<INPUT type='hidden' name='wpronom' value='{$rows[ 'Tcarpronom' ]}'>";
						echo "<INPUT type='hidden' name='wpronom' value='{$canAplicada}'>";
						echo "<INPUT type='hidden' name='wgrabada' value='{$rows[ 'grabados' ]}'>";
						echo "<INPUT type='hidden' name='wcodcon' value='{$rows[ 'Tcarconcod' ]}'>";
						echo "<INPUT type='hidden' name='wnomcon' value='{$rows[ 'Tcarconnom' ]}'>";
						//echo "<INPUT type='hidden' name='wcantidad' value='".($rows[ 'can' ] - $canAplicada)."'>";
						echo "<INPUT type='hidden' name='wgrabar' value='on'>";
						echo "<INPUT type='hidden' name='wccogra' value='{$rows[ 'Tcarser' ]}'>";
						echo "<INPUT type='hidden' name='wdevol'>";
						echo "<INPUT type='hidden' name='wrecexc' value='R'>";
						echo "<INPUT type='hidden' name='wfacturable' value='S'>";

						echo "</td>";

						//Código del articulo
						echo "<td align='center'>{$rows[ 'Tcarprocod' ]}</td>";

						//Descripción del articulo
						echo "<td>{$rows[ 'Tcarpronom' ]}</td>";

						//Cantidad grabada
						echo "<td align='center' name='canGra'>".($rows[ 'grabados' ])."</td>";

						//Cantidad Aplicada
						//echo "<td align='center' name='canGra'>".($rows[ 'grabados' ])."</td>";
						echo "<td align='center' name='canApl' id='Aplicaciones{$i}' style='cursor:pointer;' mostrandoDetalle='off' onclick='mostrarDetalle( \"Devoluciones\", \"Aplicaciones\", \"{$i}\", this )'>{$canAplicada}</td>";

						//Cantidad Devuelta
						// echo "<td align='center' name='canApl'>".( $canAplicada ? $canAplicada : "0" )."</td>";
						echo "<td align='center' name='canDev' id='Devoluciones{$i}' style='cursor:pointer;' mostrandoDetalle='off' onclick='mostrarDetalle( \"Aplicaciones\", \"Devoluciones\", \"{$i}\", this )'>".($rows[ 'devueltos' ])."</td>";

						//Saldo
						echo "<td align='center' name='canSal'>".($rows[ 'can' ] - $canAplicada)."</td>";

						( (($rows[ 'can' ] - $canAplicada)*1  == 0) or ( !$puedeDevolver ) ) ? $deshabilitarMovimientos = "disabled" : $deshabilitarMovimientos = "";
						//Boton de devolución del articulo
						echo "<td align='center'>";
						echo "<INPUT type='checkbox' name='cbDev' tipo='particular' padre='{$i}' {$deshabilitarMovimientos}  onClick='soloUnCheckbox( this );'>";
						echo "</td>";

						echo "<td align='center'><input type='text' size='2' disabled class='solofloat inputDesHabilitado' name='wcantidad' movimiento='devolver' value=''></td>";
						/****************************************************************************************
						 * Boton para cargar al ingreso siguiente
						 ****************************************************************************************/
						if( $cargarIngresoSiguiente ){
							$colspan = 10;
							echo "<td align='center'>";

							//El botón solo se habilita si hay un ingreso superior para el paciente y que sea el último
							// echo "<INPUT type='checkbox' name='cbIngSig' onClick='devolverCargar( this );'>";
							echo "<INPUT type='checkbox' name='cbIngSig' {$deshabilitarMovimientos} onClick='soloUnCheckbox( this );'>";
							echo "</td>";
						}else
							{
								$colspan = 9;
							}
						/****************************************************************************************/

						echo "</tr>";

						//******************************************** FILAS DETALLE DE APLICACIONES ****************************************************************//

							echo "<tr detalle='true' style='display:none;' id='detalle_{$i}'>";
							//echo "<tr detalle='true' id='detalle_{$i}'>";
								echo "<td colspan='{$colspan}' align='center'>";  /*contenedor*/
								echo "<br>";
									echo detalleAplicacionesDevoluciones( $arregloDetalle[$rows[ 'Tcarprocod' ]], $rows[ 'Tcarprocod' ], $i, $class );
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
			echo "<td colspan='2' align='center'><INPUT type='button' value='Guardar Cambios' onClick='ejecutarFunciones()'></td>";
			echo "</tr>";

			echo "<tr><td><br></td></tr>";

			echo "<tr><td align='center'><INPUT type='button' value='Retornar' style='width:100' onClick='location.href=\"saldosInsumosPaciente.php?wemp_pmla=$wemp_pmla\"'></td></tr>";
			echo "<tr><td align='center'>&nbsp;</td></tr>";
			echo "<tr><td align='center'><INPUT type='button' value='Cerrar' onClick='cerrarVentana()'></td></tr>";

			echo "</table>";

			echo "<div id='msg' style='display:none'>Procesando....</div>";
		}

		echo "</form>";
	}
}
?>
