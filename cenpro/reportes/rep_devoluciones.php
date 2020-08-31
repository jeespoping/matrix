<?php
include_once("conex.php");
/**
 PROGRAMA                   : rep_devoluciones.php
 AUTOR                      : Frederick Aguirre.
 FECHA CREACION             : 06 de noviembre de 2012

 DESCRIPCION:
 Muestra las devoluciones de los articulos para central de mezclas o servicio farmaceutico.

 CAMBIOS:
	

**/

$wactualiz = "2012-11-26";
	
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo '<html xmlns="http://www.w3.org/1999/xhtml">';
	echo "<head>";

	echo "<title>Reporte de Devoluciones</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';

	echo '<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet"  />';

	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
}


	//********************FUNCIONES COMUNES****************************//
	

	include_once("root/comun.php");

	

	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	
	$conexunix; //conexion a unix de inventarios
	$conexUnixPac; // conexion a unix para informacion de pacientes
	conexionOdbc($conex, 'movhos', $conexUnixPac, 'facturacion');
	conexionOdbc($conex, 'movhos', $conexUnix, 'inventarios');
		
	$conex = obtenerConexionBD("matrix");

	
	$costos_global = array();
	$precios_global = array();

	//FIN***************************************************************//


	//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
	if( isset($_REQUEST['action'] )){
		$action = $_REQUEST['action'];
		if($action=="consultar"){
			ejecutarBusqueda( $_REQUEST['origen'], $_REQUEST['tipo'], $_REQUEST['fecha_inicial'],  $_REQUEST['fecha_final'] );
			return;
		}else if($action=="consultar_costo"){
	//http://mx.lasamericas.com.co/matrix/cenpro/reportes/rep_devoluciones.php?wemp_pmla=01&action=consultar_costo&anox=2012&mesx=05&origenx=SF&articulox=A2BA27
			$valor = consultarCosto($_REQUEST['origenx'], $_REQUEST['anox'], $_REQUEST['mesx'], $_REQUEST['articulox']);
			if( $valor == 0)
				echo "<br> consulto con mes menos";
				$valor = consultarCosto($_REQUEST['origenx'], $_REQUEST['anox'], (int)($_REQUEST['mesx'])-1, $_REQUEST['articulox']);
			echo "--------->".$valor;
		}
		else if($action=="consultar_pv"){
	//http://mx.lasamericas.com.co/matrix/cenpro/reportes/rep_devoluciones.php?wemp_pmla=01&action=consultar_pv&historia=422288&ingreso=1&origen=SF&articulo=A2BA27
	//PARA VER LA HISTORIA-INGRESO DE LA DEVOLUCION select * from movhos_000035 where dencon = 176405
			$valor = consultarPrecioVenta($_REQUEST['origen'], $_REQUEST['historia'], $_REQUEST['ingreso'], $_REQUEST['articulo']);
			echo "--------->".$valor;
		}
		return;
	}
	//FIN****************************************************************************************************************//
	class devolucion{
		var $fecha;
		var $ano;
		var $mes;
		var $consecutivo;
		var $historia;
		var $ingreso;
		var $articulo;
		var $nombre_articulo;
		var $cantidad;
		var $justificacion;
		var $precio_venta;
		var $costo;
		var $insumo;
		var $nombre_insumo;
		var $cantidad_insumo;
		var $numero;
		var $linea;
	}

	//**************************FUNCIONES DE PHP********************************************//
	function ejecutarBusqueda($worigen, $wtipo, $wf_inicial, $wf_final){
		global $conex;
		global $wcenmez;
		global $wemp_pmla;
		global $wmovhos;
		
		global $costos_global;
				
		$filas_tabla1 = array();
		$filas_tabla2 = array();
		$total_tabla1 = array(); //Para calcular ultima fila con los totales
	
		//Consulto todas las devoluciones entre las fechas elegidas (Encabezado Devolucion);
		$query_ED = "	SELECT 		Fecha_data as fecha_devolucion, Dencon as consecutivo, Denhis as historia, Dening as ingreso "
					 ."	  FROM 		".$wmovhos."_000035 "
					 ."	 WHERE 		Fecha_data BETWEEN '".$wf_inicial."' AND '".$wf_final."'"
				 ."	  ORDER BY 		fecha_devolucion, consecutivo, historia, ingreso ";
		$num_enc_dev = 0;
		$res_enc_dev = mysql_query($query_ED, $conex);
		$num_enc_dev = mysql_num_rows($res_enc_dev);
		if ($num_enc_dev > 0 ){
				while($row_enc_dev = mysql_fetch_assoc($res_enc_dev)) {
						//Consulto el detalle de las devoluciones 
						$query_DD = "	SELECT 		Devnum as numero, Devlin as linea, Devces as cantidad,  REPLACE(Devjud, '  ',' ') as justificacion "
									 ."	  FROM 		".$wmovhos."_000028 "
									 ."	 WHERE 		Devcon = ".$row_enc_dev['consecutivo']
								 ."	  ORDER BY 		numero, linea, cantidad";
						
						$exp_fecha = explode( "-", $row_enc_dev['fecha_devolucion']);
						$ano_devolucion = $exp_fecha[0];
						$mes_devolucion = $exp_fecha[1];
						
						$num_det_dev = 0;
						$res_det_dev = mysql_query($query_DD, $conex);
						$num_det_dev = mysql_num_rows($res_det_dev);
						if ($num_det_dev > 0 ){
								
								while($row_det_dev = mysql_fetch_assoc($res_det_dev)) { 
								
									//Si es central de mezclas la linea debe ser cero
									if($worigen == "CM" && $row_det_dev['linea'] != 0){
											continue;
									}
									
									$query_DC = "";
									if($worigen == "CM"){

										//Consulto el detalle cargos para central de mezclas
										$query_DC = "	SELECT 		DC.Fdeart as insumo, DC.Fdeari as articulo, DC.Fdecan as cantidad_insumo, "
													 ."				ART.Artcom as nombre_comercial, LOT.Fecha_data as fecha_lote, DC.Fdelin as linea "
													 ."	  FROM 		".$wmovhos."_000003 DC , ".$wcenmez."_000002 ART ,".$wcenmez."_000004 LOT "
													 ."	 WHERE 		DC.Fdenum = ".$row_det_dev['numero']
													 ."    AND      ART.Artcod = DC.Fdeari "
													 ."    AND      DC.Fdelot != '' "
													 ."    AND      LOT.Plocod = DC.Fdelot "
													 ."    AND      LOT.Plopro = ART.Artcod "
												 ."	  ORDER BY 		articulo, DC.Fecha_data";

									}else if( $worigen == "SF" ){
										//Consulto el detalle cargos para servicio farmaceutico
										$query_DC = "	SELECT 		DC.Fdeart as articulo, '*' as insumo, '*' as cantidad_insumo, "
													 ."				ART.Artcom as nombre_comercial, '*' as fecha_lote, DC.Fdelin as linea "
													 ."	  FROM 		".$wmovhos."_000003 DC , ".$wmovhos."_000026 ART "
													 ."	 WHERE 		DC.Fdenum = ".$row_det_dev['numero']
													 ."	   AND		DC.Fdelin = ".$row_det_dev['linea']
													 ."    AND      DC.Fdelot = '' "
													 ."    AND      ART.Artcod = DC.Fdeart "
												 ."	  ORDER BY 		articulo, DC.Fecha_data";
									}
									
									$num_art_dev = 0;
									$res_art_dev = mysql_query($query_DC, $conex);
									$num_art_dev = mysql_num_rows($res_art_dev);
									if ($num_art_dev > 0 ){
										while($row_art_dev = mysql_fetch_assoc($res_art_dev)) {
										
												//Si es material quimico quirurgico...
												if($wtipo == "MMQ" && esMMQ($row_art_dev['articulo']) == false){
													continue;
												}else if($wtipo != "MMQ" && esMMQ($row_art_dev['articulo']) == true){ //si no es material quimico quirurgico...
													continue;
												}
												$ano_lote = "";
												$mes_lote = "";
												$exp_fecha = explode( "-", $row_art_dev['fecha_lote']);
												if( sizeof ( $exp_fecha ) > 1 ){
													$ano_lote = $exp_fecha[0];
													$mes_lote = $exp_fecha[1];
												}
												//Creo una nueva devolucion	
												$devolucion = new devolucion();
												$devolucion->fecha = $row_enc_dev['fecha_devolucion'];
												$devolucion->ano = $ano_devolucion;
												$devolucion->mes = $mes_devolucion;
												$devolucion->consecutivo = $row_enc_dev['consecutivo'];	
												$devolucion->historia = $row_enc_dev['historia'];
												$devolucion->ingreso = $row_enc_dev['ingreso'];
												$devolucion->articulo = $row_art_dev['articulo'];
												$devolucion->nombre_articulo = $row_art_dev['nombre_comercial'];
												$devolucion->cantidad = $row_det_dev['cantidad'];
												$devolucion->justificacion = $row_det_dev['justificacion'];
												
												$devolucion->numero = $row_det_dev['numero'];
												$devolucion->linea = $row_art_dev['linea'];
												$insumo = null;
												
												if($worigen == "CM"){
													//En central de mezclas la cantidad es 1, porque en detalle cargo
													//aparecen los N insumos para un mismo articulo
													//$devolucion->cantidad = 1;
													$devolucion->insumo = $row_art_dev['insumo'];
													$devolucion->nombre_insumo = consultarNombreInsumo($devolucion->insumo );
													//$devolucion->costo = consultarCosto($worigen, $insumo->ano_lote, $insumo->mes_lote, $insumo->codigo); 
													$devolucion->costo = consultarCosto($worigen, $ano_lote, $mes_lote, $row_art_dev['insumo']); 
													if( $devolucion->costo == 0){//No hay costos para el ano-mes buscado?  buscar en el mes anterior
														$ano_consulta = $ano_lote;
														$mes_consulta = ($mes_lote)-1;
														if( (($devolucion->mes)-1) == 0){//EL mes que se consulto es enero y no hay datos? busquemos en el mes anterior, no mes 0 si no mes 12 del ano anterior
															$ano_consulta = $ano_consulta-1;
															$mes_consulta = 12;
														}
														$devolucion->costo = consultarCosto($worigen, $ano_consulta, $mes_consulta, $row_art_dev['insumo']);
													}
													$devolucion->cantidad_insumo = 	$row_art_dev['cantidad_insumo'];
													$devolucion->costo = ($devolucion->costo) * ($row_art_dev['cantidad_insumo']);
													
													$devolucion->precio_venta = consultarPrecioVenta($worigen, $devolucion->historia, $devolucion->ingreso, $row_art_dev['insumo']); 
													$devolucion->precio_venta = ($devolucion->precio_venta) * ($row_art_dev['cantidad_insumo']);
													
												}else if( $worigen = "SF"){
													$devolucion->costo = consultarCosto($worigen, $devolucion->ano, $devolucion->mes, $devolucion->articulo);
													if( $devolucion->costo == 0){//No hay costos para el ano-mes buscado?  buscar en el mes anterior
														$ano_consulta = $devolucion->ano;
														$mes_consulta = ($devolucion->mes)-1;
														if( (($devolucion->mes)-1) == 0){//EL mes que se consulto es enero y no hay datos? busquemos en el mes anterior, no mes 0 si no mes 12 del ano anterior
															$ano_consulta = $ano_consulta-1;
															$mes_consulta = 12;
														}
														$devolucion->costo = consultarCosto($worigen, $ano_consulta, $mes_consulta, $devolucion->articulo);
													}
													$devolucion->costo = ($devolucion->costo)*($devolucion->cantidad);
													
													$devolucion->precio_venta = consultarPrecioVenta($worigen, $devolucion->historia, $devolucion->ingreso, $devolucion->articulo);
													$devolucion->precio_venta = ($devolucion->precio_venta)*($devolucion->cantidad);
												}											
												agregarDatoFilaTabla1( $filas_tabla1, $devolucion );
												agregarDatoFilaTabla2( $filas_tabla2, $devolucion, $worigen );
										}
									}
								}
						}
				}
		}
		
		$exp_fecha_ini = explode( "-", $wf_inicial );
		$exp_fecha_fin = explode( "-", $wf_final);
		$ano_ini = $exp_fecha_ini[0];
		$ano_fin = $exp_fecha_fin[0];
		$mes_ini = (int)$exp_fecha_ini[1];
		$mes_fin = $exp_fecha_fin[1];
		
		/*  Construyo un arreglo con los meses y otro con los años*/
		$seguir = true;
		$meses_dig = array();
		$anos_dig = array();
		$anok = $ano_ini;
		$mesk = $mes_ini;
		while( $seguir ){
			if($ano_fin == $anok && $mesk >= $mes_fin){
				$seguir = false;
			}
			array_push( $meses_dig, $mesk );
			array_push ( $anos_dig, $anok );
			$mesk++;
			if( $mesk > 12 ){
				$anok = $anok + 1 ;
				$mesk = $mesk -12;
			}
		}
		/*  FIN Construyo un arreglo con los meses y otro con los años*/
		
		//Arreglo con el nombre de los meses
		$meses = getNombresMeses( $meses_dig  );
		
		//Ordernar los datos por articulo, ano, mes
		if(sizeof($filas_tabla1) > 0){
			foreach ($filas_tabla1 as $key => $row) {
					$volume[$key]  = $row['ano'];
					$volume2[$key]  = $row['mes'];
					$volume3[$key]  = $row['articulo'];
			}
			array_multisort($volume3, SORT_ASC, $volume, SORT_ASC, $volume2, SORT_ASC, $filas_tabla1);
			$volume = null; $volume2=null; $volume3=null;
		}
		//Ordernar los datos por justificacion, ano, mes
		if(sizeof($filas_tabla2) > 0){
			foreach ($filas_tabla2 as $key => $row) {
					$volume[$key]  = $row['ano'];
					$volume2[$key]  = $row['mes'];
					$volume3[$key]  = $row['justificacion'];
			}
			array_multisort( $volume3, SORT_ASC, $volume, SORT_ASC, $volume2, SORT_ASC, $filas_tabla2);
			$volume = null; $volume2=null; $volume3=null;
		}
		//********CONSTRUIR TABLA 1 ******************//
		
		//variables con los primeros datos que debe contener la tabla
		$articulo_mostrado = $filas_tabla1[0]['articulo'];
		$mes_mostrado = ((int)$meses_dig[0]);
		$ano_mostrado = ((int)$anos_dig[0]);
		
		$ultimo_mes = end( $meses_dig );
		$ultimo_ano = end ($anos_dig );
		
		$cant_meses = sizeof( $meses_dig );
		//CONSTRUCCION DE LA PRIMERA TABLA
		$colspan_titulo = 2+($cant_meses*5);
		
		$titulo_tabla="Devoluciones de ";
		if( $wtipo == "med" ){
			$titulo_tabla.=" medicamentos para ";
		}
		if( $worigen == "SF" ){//para servicio farmaceutico
			$titulo_tabla.=" Servicio Farmaceutico";
		}else if($worigen == "CM" ){//para central de mezclas
			$titulo_tabla.=" Central de Mezclas";
		}
		
		$titulo_tabla.="<br> entre el dia ".$wf_inicial." y el ".$wf_final;
		
		echo "<div style='width: 100%;'>";
		
		$title = "Tener en cuenta que debido al aprovechamiento, <br>"
			."pueden no incluirse en un cargo aquellos insumos que <br>tienen fracciones disponibles para el mismo paciente.";
		
		echo "<center>";
		echo "<table align='center' id='tabla_resultados'>";
		echo "<thead>";
		if( $worigen == "CM" ){
			echo "<tr><td colspan='".$colspan_titulo."' class='msg_tooltip' title='{$title}' align='right'><b>Observacion</b></td></tr>";
		}
		echo "<tr class='encabezadoTabla'>";
		echo "<th colspan='".$colspan_titulo."' align='center'>".$titulo_tabla."</th>";
		echo "</tr>";
		echo "<tr class='encabezadoTabla caja_flotante_query_ori1'>";
		echo "<th colspan=2>&nbsp;</th>";
		foreach ($meses as $mes){
			echo "<th class='centrar' colspan=5>".$mes."</th>";
		}
		echo "</tr>";
		echo"<tr class='encabezadoTabla caja_flotante_query_ori2'>";
		echo"<th>Codigo</th>";
		echo"<th>Nombre</th>";
		foreach ($meses as $mes){
			echo "<th class='centrar type-int'>Cantidad <br>devuelta </th>";
			echo "<th class='centrar'>Costo Unitario</th>";
			echo "<th class='centrar'>Costo Total</th>";
			echo "<th class='centrar'>Precio de <br>venta Unitaria</th>";
			echo "<th class='centrar'>Precio de <br>venta Total</th>";
		}
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		
		$nombres_encabezado = array();
		$nombres_encabezado[0]="Codigo";
		$nombres_encabezado[1]="Nombre";
		$nombres_encabezado[2]="Cantidad<br>Devuelta";
		$nombres_encabezado[3]="Costo Unitario";
		$nombres_encabezado[4]="Costo Total";
		$nombres_encabezado[5]="Precio de<br>Venta Unitario";
		$nombres_encabezado[6]="Precio de<br>Venta Total";
		//msg_tooltip
		
		$fila_tabla_oculta="";
		//contador
		$ii = 0;
		//control
		$wclass="";
		$imprimio_primero = false;

		foreach ( $filas_tabla1 as $key => $row ){
			
				//SI el articulo es distinto significa que imprimiremos un nuevo tr
				if( $articulo_mostrado != $row['articulo'] ){
					//Para los meses del final que no se han encontrado datos se imprimen ceros
					while( $ano_mostrado<=$ultimo_ano && $mes_mostrado < $ultimo_mes ){
						echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[2]."'>0</td>";
						echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[3]."'>0</td>";
						echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[4]."'>0</td>";
						echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[5]."'>0</td>";
						echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[6]."'>0</td>";
						
						//Tabla oculta
						//XXX$fila_tabla_oculta.="<td class='$wclass' colspan=5> </td>";
						
						$mes_mostrado++;
						if( $mes_mostrado > 12 ){
							$mes_mostrado = 1;
							$ano_mostrado++;
						}
					}
					echo "</tr>";
					
					//tabla oculta
					echo "<tr id='oculto_$ii' style='display:none;'>";
					echo "<td></td>";
					echo "<td colspan=".( (5 * $cant_meses)+1)." nowrap='nowrap'>";
					echo $fila_tabla_oculta;
					echo "</td>";
					echo "</tr>";
					
					$fila_tabla_oculta="";
					$imprimio_primero = false;
				}
				
				if($imprimio_primero == false){
					//Comienza el nuevo tr
					$mes_mostrado = ((int)$meses_dig[0]);
					$ano_mostrado = ((int)$anos_dig[0]);	
					
					if($ii % 2 == 0)
						$wclass = "fila1";
					else
						$wclass = "fila2";
					$ii++;
					
					$row['nombre_articulo'] = str_replace( 'Ñ', '&Ntilde;', $row['nombre_articulo'] );
					$row['nombre_articulo'] = str_replace( '?æ', '&Ntilde;', $row['nombre_articulo'] );
					
					$id = "oculto_".($ii);
					echo "<tr onclick="."\""."javascript:mostrarOculto('".$id."',this)"."\"  class=".$wclass.">";
					echo "<td class='msg_tooltip' title='".$nombres_encabezado[0]."'>".$row['articulo']."</td>";
					echo "<td class='msg_tooltip' title='".$nombres_encabezado[1]."'>".$row['nombre_articulo']."</td>";
					
					//Tabla oculta
					//XXX$fila_tabla_oculta.="<td class='$wclass' colspan=2> </td>";
					
					$imprimio_primero = true;
				}else{
					$mes_mostrado++;
					if( $mes_mostrado > 12 ){
						$mes_mostrado = 1;
						$ano_mostrado++;
					}
				}
				
				//Para los meses del principio que no se han encontrado datos se imprimen ceros
				while( (int)$row['ano'] >= $ano_mostrado && (int)$row['mes'] > $mes_mostrado ){
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[2]."'>0</td>";
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[3]."'>0</td>";
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[4]."'>0</td>";
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[5]."'>0</td>";
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[6]."'>0</td>";
					
					//Tabla oculta
					//XXX$fila_tabla_oculta.="<td class='$wclass' colspan=7> </td>";
					
					$mes_mostrado++;
					if( $mes_mostrado > 12 ){
						$mes_mostrado = 1;
						$ano_mostrado++;
					}
				}
				
				//Se imprimen datos para el ano y mes con datos encontrados
				//if( $row['ano'] == $ano_mostrado && $row['mes'] == $mes_mostrado){
				$articulo_mostrado = $row['articulo'];
				echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[2]."'>".number_format($row['cantidad'], 0, '.', ',')."</td>";
				echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[3]."'>".number_format(($row['costo']/$row['cantidad']), 0, '.', ',')."</td>";
				echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[4]."'>".number_format($row['costo'], 0, '.', ',')."</td>";
				echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[5]."'>".number_format(($row['precio_venta']/$row['cantidad']), 0, '.', ',')."</td>";
				echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[6]."'>".number_format($row['precio_venta'], 0, '.', ',')."</td>";
				
				//XXX$fila_tabla_oculta.="<td colspan=7>";
				$fila_tabla_oculta.="<div class='divlinea'><table>";
				$colspan_fto = 5;
				$fila_tabla_oculta.="<tr class='encabezadoTabla'><td>Nro. devolucion</td><td>Fecha</td>";
				if($worigen=="CM"){
					$colspan_fto = 9;
					$fila_tabla_oculta.="<td>Nro. cargo</td><td>Linea</td><td>Insumo</td><td>Nombre</td>";
				}
				$fila_tabla_oculta.="<td>Cantidad<br>Devuelta</td><td>Costo<br>Total</td><td>Precio<br>Venta Total</td></tr>";
				$fila_tabla_oculta.="<tr class='encabezadoTabla centrar'><td colspan=".$colspan_fto.">".getNombresMeses( $mes_mostrado )."</td></tr>";
				$ind=0;
				
				$consecutivo_anterior =$row['detalles'][0]->numero;
				foreach ($row['detalles'] as $keyx => $devx){
					if($consecutivo_anterior != $devx->numero)
						$ind++; 
					$consecutivo_anterior = $devx->numero;
					if($ind % 2 == 0)
						$wclassi = "fila1";
					else
						$wclassi = "fila2";
					
					$fila_tabla_oculta.="<tr class='".$wclassi."'>";
					$fila_tabla_oculta.= "<td>".$devx->consecutivo."</td>";
					$fila_tabla_oculta.= "<td>".$devx->fecha."</td>";
					if($worigen=="CM"){
						$fila_tabla_oculta.= "<td>".$devx->numero."</td>";
						$fila_tabla_oculta.= "<td>".$devx->linea."</td>";
						$fila_tabla_oculta.= "<td>".$devx->insumo."</td>";
						$fila_tabla_oculta.= "<td>".$devx->nombre_insumo."</td>";
						$fila_tabla_oculta.= "<td class='derecha'>".number_format($devx->cantidad_insumo, 0, '.', ',')."</td>";
					}else{
						$fila_tabla_oculta.= "<td class='derecha'>".number_format($devx->cantidad, 0, '.', ',')."</td>";
					}
					$fila_tabla_oculta.= "<td class='derecha'>".number_format($devx->costo, 0, '.', ',')."</td>";
					$fila_tabla_oculta.= "<td class='derecha'>".number_format($devx->precio_venta, 0, '.', ',')."</td>";
					$fila_tabla_oculta.="</tr>";
				}
				$fila_tabla_oculta.="</table></div>";
				//XXX$fila_tabla_oculta.="</td>";
				//Para sumarle datos a la ultima fila de resultados
				sumarTotales($total_tabla1, $ano_mostrado, $mes_mostrado, $row['cantidad'], $row['costo'], $row['precio_venta']);									
		}

		//Para el ultimo dato
		while( $ano_mostrado<=$ultimo_ano && $mes_mostrado < $ultimo_mes ){
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[2]."'>0</td>";
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[3]."'>0</td>";
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[4]."'>0</td>";
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[5]."'>0</td>";
					echo"<td class='derecha msg_tooltip' title='".$nombres_encabezado[6]."'>0</td>";
					$mes_mostrado++;
					if( $mes_mostrado > 12 ){
						$mes_mostrado = 1;
						$ano_mostrado++;
					}
				}

		//tabla oculta
		echo "<tr id='oculto_$ii' style='display:none;'>";
		echo "<td></td>";
		echo "<td colspan=".( (5 * $cant_meses)+1)." nowrap='nowrap'>";
		echo $fila_tabla_oculta;
		echo "</td>";
		echo "</tr>";
				
		//ORGANIZAR LA ULTIMA FILA CON LOS TOTALES, DADO QUE HAY AÑO-MES QUE NO TIENE DATOS Y NO GUARDAN EN ESTE ARREGLO HAY QUE CREARLOS EN CEROS
		$ano_v = $anos_dig[0];
		for( $i = 0; $i < sizeof($meses_dig); $i++ ){
			$mes_v = $meses_dig[$i];
			$siHay = false;
			foreach ($total_tabla1 as $key => $row) {
					if( $row['ano'] == $ano_v && $row['mes'] == $mes_v ){
						$siHay = true;
					}
			}
			if( $siHay == false ){
				$va['ano'] = $ano_v;
				$va['mes'] = $mes_v;
				$va['cantidad'] = 0;
				$va['costo'] = 0;
				$va['venta'] = 0;
				array_push( $total_tabla1, $va );
			}
			if($mes_v == 12 ){
				$ano_v = $ano_v+1;
			}
		}
		
		if(sizeof($total_tabla1) > 0){
			foreach ($total_tabla1 as $key => $row) {
					$volume[$key]  = $row['ano'];
					$volume2[$key]  = $row['mes'];
			}
			array_multisort($volume, SORT_ASC, $volume2, SORT_ASC, $total_tabla1);
			$volume = null; $volume2=null;
		}
		echo "</tbody>";
		echo "<tfoot>";
		echo '<tr class="encabezadoTabla"><td colspan=2></td>';
		foreach ( $total_tabla1 as $key => $val ){
			echo "<td class='derecha'>".number_format($val['cantidad'],  0, '.', ',')."</td>";
			echo"<td class='derecha'> </td>";
			echo "<td class='derecha'>".number_format($val['costo'],  0, '.', ',')."</td>";
			echo"<td class='derecha'> </td>";
			echo "<td class='derecha'>".number_format($val['venta'],  0, '.', ',')."</td>";
		}
		echo "</tr>";
		echo "</tfoot>";
		echo "</table>";
		echo "</center>";
		
		echo "<br><br><br><br><br>";

		//********FIN CONSTRUIR TABLA 1 ******************//
		
		//********CONSTRUIR TABLA 2 ******************//
		
		//variables con los primeros datos que debe contener la tabla
		$articulo_mostrado = $filas_tabla2[0]['justificacion'];
		$mes_mostrado = ((int)$meses_dig[0]);
		$ano_mostrado = ((int)$anos_dig[0]);
		
		$ultimo_mes = end( $meses_dig );
		$ultimo_ano = end ($anos_dig );
		
		//Variable que almacena los tr td
		$titulo_tabla="CAUSAS DE DEVOLUCION";
		$colspan_titulo = 1+($cant_meses*3);
		echo  "<center>";
		echo  "<table align='center' id='tabla_resultados2'>";
		echo  "<thead>";
		echo  "<tr class='encabezadoTabla'>";
		echo  "<th colspan='".$colspan_titulo."' align='center'>".$titulo_tabla."</th>";
		echo  "</tr>";
		echo  "<tr class='encabezadoTabla'>";
		echo  "<th colspan=1>&nbsp;</th>";
		foreach ($meses as $mes){
			echo  "<th class='centrar' colspan=3>".$mes."</th>";
		}
		echo  "</tr>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th>Motivo</th>";
		foreach ($meses as $mes){
			echo  "<th class='centrar type-int'>Cantidad <br>Devuelta</th>";
			echo  "<th class='centrar'>Costo Total</th>";
			echo  "<th class='centrar'>Precio de<br> Venta Total</th>";
		}
		echo  "</tr>";
		echo  "</thead>";
		echo  "<tbody>";
		$wclass="";
		//control
		$imprimio_primero = false;
		$fila_tabla_oculta="";
		foreach ( $filas_tabla2 as $key => $row ){
			
			//SI el articulo es distinto significa que imprimiremos un nuevo tr
			if( $articulo_mostrado != $row['justificacion'] ){
				//Para los meses del final que no se han encontrado datos se imprimen ceros
					while( $ano_mostrado<=$ultimo_ano && $mes_mostrado < $ultimo_mes ){
							echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[2]."'>0</td>";
							echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[4]."'>0</td>";
							echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[6]."'>0</td>";
							
							//Tabla oculta
							//xxx$fila_tabla_oculta.="<td class='$wclass' colspan=3> </td>";
								
							$mes_mostrado++;
							if( $mes_mostrado > 12 ){
								$mes_mostrado = 1;
								$ano_mostrado++;
							}
					}
					echo "</tr>";
					
					//tabla oculta
					/*echo  "<tr id='oculto_$ii' style='display:none;'>";
					echo  $fila_tabla_oculta;
					echo  "</tr>";*/
					//tabla oculta
					echo "<tr id='oculto_$ii' style='display:none;'>";
					echo "<td colspan=".( (3 * $cant_meses)+1)." nowrap='nowrap'>";
					echo $fila_tabla_oculta;
					echo "</td>";
					echo "</tr>";
					
					$fila_tabla_oculta="";
						
					$imprimio_primero = false;
			}
			
			if($imprimio_primero == false){
					//Comienza el nuevo tr
					$mes_mostrado = ((int)$meses_dig[0]);
					$ano_mostrado = ((int)$anos_dig[0]);

					if($ii % 2 == 0)
						$wclass = "fila1";
					else
						$wclass = "fila2";
					$ii++;				
					$row['justificacion'] = str_replace( 'Ñ', '&Ntilde;', $row['justificacion'] );
					$row['justificacion'] = str_replace( '?æ', '&Ntilde;', $row['justificacion'] );
					
					$id = "oculto_".($ii);
					echo "<tr onclick="."\""."javascript:mostrarOculto('".$id."',this)"."\"  class=".$wclass.">";
					echo "<td class='msg_tooltip' title='Justificacion'>".$row['justificacion']."</td>";
					
					//Tabla oculta
					//xxx$fila_tabla_oculta.="<td class='$wclass' > </td>";
						
					$imprimio_primero = true;
			}else{
					$mes_mostrado++;
					if( $mes_mostrado > 12 ){
						$mes_mostrado = 1;
						$ano_mostrado++;
					}
			}
			
			//Para los meses del principio que no se han encontrado datos se imprimen ceros
			while( (int)$row['ano'] >= $ano_mostrado && (int)$row['mes'] > $mes_mostrado ){
					echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[2]."'>0</td>";
					echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[4]."'>0</td>";
					echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[6]."'>0</td>";
					
					//Tabla oculta
						//xxx$fila_tabla_oculta.="<td class='$wclass' colspan=3> </td>";
						
					$mes_mostrado++;
					if( $mes_mostrado > 12 ){
						$mes_mostrado = 1;
						$ano_mostrado++;
					}
			}
			
			//Se imprimen datos para el ano y mes con datos encontrados
			$articulo_mostrado = $row['justificacion'];
			echo "<td class='derecha msg_tooltip'  title='".$nombres_encabezado[2]."'>".number_format($row['cantidad'], 0, '.', ',')."</td>";
			echo "<td class='derecha msg_tooltip'  title='".$nombres_encabezado[4]."'>".number_format($row['costo'], 0, '.', ',')."</td>";
			echo "<td class='derecha msg_tooltip'  title='".$nombres_encabezado[6]."'>".number_format($row['precio_venta'], 0, '.', ',')."</td>";
			//$fila_tabla_oculta.="<td colspan=3>";
			$fila_tabla_oculta.="<div class='divlinea'><table>";
				$colspan_fto = 5;
				$fila_tabla_oculta.="<tr class='encabezadoTabla'><td>Nro. devolucion</td><td>Fecha</td>";
				if($worigen=="CM"){
					$colspan_fto = 9;
					$fila_tabla_oculta.="<td>Nro. cargo</td><td>Linea</td><td>Insumo</td><td>Nombre</td>";
				}
				$fila_tabla_oculta.="<td>Cantidad<br>Devuelta</td><td>Costo<br>Total</td><td>Precio<br>Venta Total</td></tr>";
				$fila_tabla_oculta.="<tr class='encabezadoTabla centrar'><td colspan=".$colspan_fto.">".getNombresMeses( $mes_mostrado )."</td></tr>";
			$ind=0;
			
			$consecutivo_anterior =$row['detalles'][0]->numero;
			foreach ($row['detalles'] as $keyx => $devx){
					if($consecutivo_anterior != $devx->numero)
						$ind++; 
					$consecutivo_anterior = $devx->numero;
					if($ind % 2 == 0)
						$wclassi = "fila1";
					else
						$wclassi = "fila2";
			
					$fila_tabla_oculta.="<tr class='".$wclassi."'>";
					$fila_tabla_oculta.= "<td>".$devx->consecutivo."</td>";
					$fila_tabla_oculta.= "<td>".$devx->fecha."</td>";
					if($worigen=="CM"){
						$fila_tabla_oculta.= "<td>".$devx->numero."</td>";
						$fila_tabla_oculta.= "<td>".$devx->linea."</td>";
						$fila_tabla_oculta.= "<td>".$devx->insumo."</td>";
						$fila_tabla_oculta.= "<td>".$devx->nombre_insumo."</td>";
						$fila_tabla_oculta.= "<td class='derecha'>".number_format($devx->cantidad_insumo, 0, '.', ',')."</td>";
					}else{
						$fila_tabla_oculta.= "<td class='derecha'>".number_format($devx->cantidad, 0, '.', ',')."</td>";
					}
					$fila_tabla_oculta.= "<td class='derecha'>".number_format($devx->costo, 0, '.', ',')."</td>";
					$fila_tabla_oculta.= "<td class='derecha'>".number_format($devx->precio_venta, 0, '.', ',')."</td>";
					$fila_tabla_oculta.="</tr>";
			}
			$fila_tabla_oculta.="</table></div>";
			//$fila_tabla_oculta.="</td>";
										
		}
		//Para el ultimo dato
		while( $ano_mostrado<=$ultimo_ano && $mes_mostrado < $ultimo_mes ){
				echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[2]."'>0</td>";
				echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[4]."'>0</td>";
				echo "<td class='derecha msg_tooltip' title='".$nombres_encabezado[6]."'>0</td>";
				$mes_mostrado++;
				if( $mes_mostrado > 12 ){
					$mes_mostrado = 1;
					$ano_mostrado++;
				}
		}
		/*echo  "<tr id='oculto_".($ii)."' style='display:none;'>";
		echo  $fila_tabla_oculta;
		echo  "</tr>";*/
		echo "<tr id='oculto_$ii' style='display:none;'>";
		echo "<td colspan=".( (3 * $cant_meses)+1)." nowrap='nowrap'>";
		echo $fila_tabla_oculta;
		echo "</td>";
		echo "</tr>";
		
		echo "</tr>";
		echo  "</tbody>";
			
		echo  "<tfoot>";
		echo  '<tr class="encabezadoTabla"><td></td>';
		foreach ( $total_tabla1 as $key => $val ){
			echo  "<td class='derecha'>".number_format($val['cantidad'],  0, '.', ',')."</td>";
			echo  "<td class='derecha'>".number_format($val['costo'],  0, '.', ',')."</td>";
			echo  "<td class='derecha'>".number_format($val['venta'],  0, '.', ',')."</td>";
			}
		echo  "</tr>";
		echo  "</tfoot>";
		
		//********FIN CONSTRUIR TABLA 2******************//
		
		echo  "</table>";
		echo  "</center>";
		echo "</div>";
		
	}
	
	function agregarDatoFilaTabla1(&$filas, $devolucion){
		//Verifica si ya existen datos para el insumo, la historia y el ingreso, si existe le suma a la cantidad
		$existe = false;
		foreach ( $filas as &$dev ){
			if( $dev['ano'] == $devolucion->ano && $dev['mes'] == $devolucion->mes && $dev['articulo'] == $devolucion->articulo && $dev['consecutivo'] != $devolucion->consecutivo){
				//$dev['cantidad'] = $dev['cantidad'] + $devolucion->cantidad;
				if($dev['numero'] != $devolucion->numero){
					$dev['cantidad'] = $dev['cantidad'] + $devolucion->cantidad;
					$dev['numero'] = $devolucion->numero;
				}
				$dev['costo'] += $devolucion->costo;
				$dev['precio_venta'] += $devolucion->precio_venta;
				$dev['consecutivo'] = $devolucion->consecutivo;
				array_push( $dev['detalles'], $devolucion );
				$existe = true;
			}else if($dev['ano'] == $devolucion->ano && $dev['mes'] == $devolucion->mes && $dev['articulo'] == $devolucion->articulo && $dev['consecutivo'] == $devolucion->consecutivo){
				//Traer costo y sumar al global				//Traer precio y sumar al global
				if($dev['numero'] != $devolucion->numero){
					$dev['cantidad'] = $dev['cantidad'] + $devolucion->cantidad;
					$dev['numero'] = $devolucion->numero;
				}
				$dev['costo'] += $devolucion->costo;
				$dev['precio_venta'] += $devolucion->precio_venta;
				array_push( $dev['detalles'], $devolucion );
				$existe = true;
			}
		}
		
		//Si no existe crea una nueva posicion
		if($existe == false){
			$va['ano'] = $devolucion->ano;
			$va['mes'] = $devolucion->mes;
			$va['articulo'] = $devolucion->articulo;
			$va['consecutivo'] = $devolucion->consecutivo;
			$va['cantidad'] = $devolucion->cantidad;
			$va['nombre_articulo'] = $devolucion->nombre_articulo;
			$va['costo'] = $devolucion->costo;
			$va['precio_venta'] = $devolucion->precio_venta;
			$va['numero'] = $devolucion->numero;
			$va['detalles'] = array();
			array_push( $va['detalles'], $devolucion );
			array_push( $filas, $va );
		}
	}
	
	function agregarDatoFilaTabla2(&$filas, $devolucion , $worigen){
		//Verifica si ya existen datos para el insumo, la historia y el ingreso, si existe le suma a la cantidad
		$existe = false;
		foreach ( $filas as &$dev ){
			if( $dev['ano'] == $devolucion->ano && $dev['mes'] == $devolucion->mes && $dev['justificacion'] == $devolucion->justificacion && $dev['consecutivo'] != $devolucion->consecutivo){
				if( $worigen == "CM" ){
					if($dev['numero'] != $devolucion->numero){
						$dev['cantidad'] = $dev['cantidad'] + $devolucion->cantidad;
						$dev['numero'] = $devolucion->numero;
					}
				}else{
					$dev['cantidad'] = $dev['cantidad'] + $devolucion->cantidad;
				}
				$dev['costo'] += $devolucion->costo;
				$dev['precio_venta'] += $devolucion->precio_venta;
				$dev['consecutivo'] = $devolucion->consecutivo;
				$dev['articulo'] = $devolucion->articulo;
				array_push( $dev['detalles'], $devolucion );
				$existe = true;
			}else if( $dev['ano'] == $devolucion->ano && $dev['mes'] == $devolucion->mes && $dev['justificacion'] == $devolucion->justificacion && $dev['consecutivo'] == $devolucion->consecutivo && $dev['articulo'] != $devolucion->articulo){
				
				$dev['costo'] += $devolucion->costo;
				$dev['precio_venta'] += $devolucion->precio_venta;
				$dev['articulo'] = $devolucion->articulo;
				if( $worigen == "CM" ){
					if($dev['numero'] != $devolucion->numero){
						$dev['cantidad'] = $dev['cantidad'] + $devolucion->cantidad;
						$dev['numero'] = $devolucion->numero;
					}
				}else{
					$dev['cantidad'] = $dev['cantidad'] + $devolucion->cantidad;
				}
				array_push( $dev['detalles'], $devolucion );
				$existe = true;
			}else if($dev['ano'] == $devolucion->ano && $dev['mes'] == $devolucion->mes && $dev['justificacion'] == $devolucion->justificacion && $dev['consecutivo'] == $devolucion->consecutivo){
				$dev['costo'] += $devolucion->costo;
				$dev['precio_venta'] += $devolucion->precio_venta;
				if( $worigen == "CM" ){
					if($dev['numero'] != $devolucion->numero){
						$dev['cantidad'] = $dev['cantidad'] + $devolucion->cantidad;
						$dev['numero'] = $devolucion->numero;
					}
				}
				array_push( $dev['detalles'], $devolucion );
				$existe = true;
			}
		}
		//Si no existe crea una nueva posicion
		if($existe == false){
			$va['ano'] = $devolucion->ano;
			$va['mes'] = $devolucion->mes;
			$va['justificacion'] = $devolucion->justificacion;
			$va['consecutivo'] = $devolucion->consecutivo;
			$va['articulo'] = $devolucion->articulo;
			$va['cantidad'] = $devolucion->cantidad;
			$va['costo'] = $devolucion->costo;
			$va['precio_venta'] = $devolucion->precio_venta;
			$va['numero'] = $devolucion->numero;
			$va['detalles'] = array();
			array_push( $va['detalles'], $devolucion );
			array_push( $filas, $va );
		}
	}
	
	function sumarTotales(&$total_tabla1, $ano, $mes, $cantidad, $costo, $venta){
		//Verifica si ya existen datos para ese año y mes, si existe le suma a la cantidad, al costo y la venta
		$existe = false;
		foreach ( $total_tabla1 as $key => &$val ){
			if( $val['ano'] == trim($ano) && $val['mes'] == trim($mes) ){
				$val['cantidad'] = $val['cantidad'] + $cantidad;
				$val['costo'] = $val['costo'] + $costo;
				$val['venta'] = $val['venta'] + $venta;
				$existe = true;
			}
		}
		//Si no existe crea una nueva posicion
		if($existe == false){
			$va['ano'] = trim($ano);
			$va['mes'] = trim($mes);
			$va['cantidad'] = $cantidad;
			$va['costo'] = $costo;
			$va['venta'] = $venta;
			array_push( $total_tabla1, $va );
		}
	}
	

	function consultarPrecioVenta($worigen, $whis, $wing, $wart){
		global $conexUnix;
		global $conexUnixPac;
		global $precios_global;

		// Consulta de datos Unix
		//return 0;
		foreach ( $precios_global as $key => $val ){
			if( $val['articulo'] == $wart && $val['historia'] == $whis && $val['ingreso'] == $wing ){
				return $val['precio'];
			}
		}

		$tarifa = 0;
		
		$responsable = consultarResponsable( $whis, $wing );
		
		//Buscar la tarifa para el paciente en la tabla inpac
		$query="SELECT 	emptar 
				  FROM 	inemp
				 WHERE 	empcod='".trim($responsable)."'
				   AND 	emptar is not null
				 UNION
				SELECT 	' ' as emptar
				  FROM 	inemp
				 WHERE 	empcod='".trim($whis)."'
				   AND 	emptar is null ";
		$err_o = odbc_do($conexUnixPac,$query) or die( odbc_error()." - $q - ".odbc_errormsg() );

		if (odbc_fetch_row($err_o))
		{	
			$tarifa_od = odbc_fetch_row($err_o);
			$tarifa = odbc_result($err_o,'emptar');
		}
		else
		{
		//echo "<br>ELSE ".$responsable." -";
			////Buscar la tarifa para el paciente en la tabla inpaci
			$query="SELECT 	egrtar 
					  FROM 	inmegr
					 WHERE 	egrhis='".trim($whis)."'
					   AND 	egrnum='".trim($wing)."'
					   AND 	egrtar is not null
					 UNION
					SELECT 	' ' as egrtar 
					  FROM 	inmegr
					 WHERE  egrhis='".trim($whis)."'
					   AND   egrnum='".trim($wing)."'
				       AND  	egrtar is null ";
			$err_1 = odbc_do($conexUnixPac,$query) or die( odbc_error()." Query buscame.php - $query - ".odbc_errormsg() );
			if (odbc_fetch_row($err_1))
			{
				$tarifa_od = odbc_fetch_row($err_1);
				$tarifa = odbc_result($err_1,'egrtar');
			}
		}
		//Busca el valor del articulo con esa tarifa
		$query = "	SELECT 	arttarval
					  FROM	ivarttar
					 WHERE	arttarcod='".trim($wart)."' 
					   AND 	arttartar='".trim($tarifa)."'
					   AND  arttartse='*'";

		$row;

		$err_o_geo = odbc_do($conexUnix,$query) or die( odbc_error()." - $query - ".odbc_errormsg() );

		if (! odbc_fetch_row($err_o_geo))
		{

			//Busca el valor del articulo con la tarifa normal
			$query = "	SELECT 	arttarval
						  FROM	ivarttar
						 WHERE	arttarcod='".trim($wart)."' 
						   AND 	arttartar='*'
						   AND  arttartse='*'";
						   			
			$err_o_geo = odbc_do($conexUnix,$query);
			if (! odbc_fetch_row($err_o_geo))
			{
				return 0;
			}else{	
				$row=odbc_fetch_row($err_o_geo);
			}
		}else{
			$row=odbc_fetch_row($err_o_geo);
		}

		$valor = odbc_result($err_o_geo,'arttarval');
		$val['articulo'] = $wart;
		$val['historia'] = $whis;
		$val['ingreso'] = $wing;
		$val['precio'] = $valor;
		array_push( $precios_global, $val );
				
		return $valor;
				 
	}

	function consultarCosto($worigen, $ano, $mes, $producto){
		global $conex;
		global $wcenmez;
		global $conexUnixPac;
		global $costos_global;
		//return 0;
		if( $worigen == "SF" ){
			foreach ( $costos_global as $key => $val ){
				if($val['articulo'] == $producto ){
					return $val['costo'];
				}
			}
		}
		if( $worigen == "CM" ){
			foreach ( $costos_global as $key => $val ){
				if( $val['articulo'] == $producto && $val['ano'] == $ano && $val['mes'] == $mes ){
					return $val['costo'];
				}
			}
		}
			//COSTO PROMEDIO DE CENPRO
			if( $worigen == "CM" ){
				//año y mes de la tabla cenpro4
				$query3 = "SELECT 	Salvuc as valor_costo_promedio, Salpro as factor_conversion "
						."   FROM 	".$wcenmez."_000014  "
						."  WHERE   Salano = ".$ano." " 
						."    AND   Salmes = ".$mes." " 
						."    AND   Salcod = '".$producto."' " ;
				
				$res3 = mysql_query($query3, $conex);	
				$row2 = mysql_fetch_assoc($res3);		
				if( mysql_num_rows($res3) > 0 ){						
					//$prod2 = @($row2['valor_costo_promedio']/$row2['factor_conversion']);
					$prod2 = @($row2['valor_costo_promedio']);
					$val['articulo'] = $producto;
					$val['costo'] = $prod2;
					$val['ano'] = $ano;
					$val['mes'] = $mes;
					array_push( $costos_global, $val );
					return $prod2;
				}else{
					return 0;
				}
			}else if( $worigen == "SF" ){
				$query="SELECT 	artcoa, artcos 
						  FROM 	ivart
						 WHERE 	artcod='".trim($producto)."'
						   AND 	artcoa is not null
						 UNION
						SELECT 	0 as artcoa, artcos 
						  FROM 	ivart
						 WHERE  artcod='".trim($producto)."'
						   AND 	artcoa is null ";
					$err_1 = odbc_do($conexUnixPac,$query) or die( odbc_error()." - $query - ".odbc_errormsg() );
					if (odbc_fetch_row($err_1))
					{
						$tarifa_od = odbc_fetch_row($err_1);
						$costo = odbc_result($err_1,'artcos');
						$val['articulo'] = $producto;
						$val['costo'] = $costo;
						$val['ano'] = "";
						$val['mes'] = "";
						array_push( $costos_global, $val );
						return $costo;
					}
			}
	}
	
		
	function consultarResponsable($whis, $wing){
		global $conex;
		global $wmovhos;

		$query = "SELECT Ingres as responsable
				  FROM  {$wmovhos}_000016
				 WHERE 	Inghis = '$whis'
				   AND	Inging = '$wing'";
		$res = mysql_query( $query, $conex );
		$rows = mysql_fetch_array( $res );
		return $rows[0];
	}
	//Es material quimico quirurgico?
	function esMMQ( $art ){

		global $conex;
		global $wmovhos;

		$esmmq = false;

		$sql = "SELECT 	artcom, artgen, artgru, melgru, meltip
				  FROM  {$wmovhos}_000026 LEFT OUTER JOIN {$wmovhos}_000066 
					ON  melgru = SUBSTRING_INDEX( artgru, '-', 1 )
				 WHERE 	artcod = '$art'	";

		$res = mysql_query( $sql, $conex );

		if( $rows = mysql_fetch_array( $res ) ){
			if( (empty( $rows['melgru'] ) || $rows['melgru'] == 'E00' ) && !empty($rows['artcom']) ){
				$esmmq = true;
			}
			else{
				$esmmq = false;
			}
		}

		return $esmmq;
	}
	
	function consultarNombreInsumo( $cod_insumo ){
		global $conex;
		global $wmovhos;

		$query = "SELECT Artcom as nombre_insumo
				  FROM  {$wmovhos}_000026
				 WHERE 	Artcod = '$cod_insumo' ";
		$res = mysql_query( $query, $conex );
		$rows = mysql_fetch_array( $res );
		return $rows[0];
	}
	
	function getNombresMeses( $meses_numeros ){
		$es_arreglo = true;
		if( is_array( $meses_numeros) == false){
			$es_arreglo = false;
			$aux = $meses_numeros;
			$meses_numeros = array();
			array_push ( $meses_numeros, $aux );
		}
		$meses = array();
		foreach ($meses_numeros as $mes){
			switch ($mes) {
				case 1:
					array_push( $meses, 'Enero' );
					break;
				case 2:
					array_push( $meses, 'Febrero' );
					break;
				case 3:
					array_push( $meses, 'Marzo' );
					break;
				case 4:
					array_push( $meses, 'Abril' );
					break;
				case 5:
					array_push( $meses, 'Mayo' );
					break;
				case 6:
					array_push( $meses, 'Junio' );
					break;
				case 7:
					array_push( $meses, 'Julio' );
					break;
				case 8:
					array_push( $meses, 'Agosto' );
					break;
				case 9:
					array_push( $meses, 'Septiembre' );
					break;
				case 10:
					array_push( $meses, 'Octubre' );
					break;
				case 11:
					array_push( $meses, 'Noviembre' );
					break;
				case 12:
					array_push( $meses, 'Diciembre' );
					break;
				case 13:
					array_push( $meses, 'Enero' );
					break;
				case 14:
					array_push( $meses, 'Marzo' );
					break;
				case 15:
					array_push( $meses, 'Abril' );
					break;
				case 16:
					array_push( $meses, 'Mayo' );
					break;
			}
		}
		if( $es_arreglo == false )
			return $meses[0];
		return $meses;
	}
		
	function vistaInicial(){
		global $wemp_pmla;
		global $wccos;
		global $wccosSU;
		global $wactualiz;
		//Se imprimen variables ocultas
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		echo '<div class="caja_flotante_query"></div>';
		echo "<center>";

		encabezado("REPORTE DE DEVOLUCIONES",$wactualiz,"clinica");


		$fecha_hoy = date("Y-m-d");
		$fecha_pddm = date("Y-m");
		$fecha_pddm.="-01";
		
		
		echo '<span class="subtituloPagina2">Parámetros de consulta</span>';
		echo '<br><br>';

		echo "<div id='parametros_consulta'>";

		echo "<table align='center'>";
		echo "<tr>";
		echo "<td class='fila1'>Fecha Inicial</td>";
		echo "<td class='fila2'  colspan=2 align='center'>";
		campoFechaDefecto( "f_inicial", $fecha_pddm );
		echo "</td>";
		echo "<tr>";
		echo "</tr>";
		echo "<td class='fila1'>Fecha Final</td>";
		echo "<td class='fila2' colspan=2  align='center'>";
		campoFechaDefecto( "f_final", $fecha_hoy );
		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td class='fila1'>Origen</td>";
		echo "<td class='fila2' align='left'>";

		echo "<input type='radio' name='origen' value='CM' />";
		echo "<label>Central de Mezclas</label>";
		echo "</td>";
		echo "<td class='fila2'  align='left'>";
		echo "<input type='radio' name='origen' value='SF' />";
		echo "<label>Servicio Farmaceutico</label>";

		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td class='fila1'>Tipo</td>";
		echo "<td class='fila2' align='left'>";
		echo "<input type='radio' name='tipo' value='med' />";
		echo "<label>Medicamentos</label>";
		echo "</td>";
		echo "<td class='fila2'  align='left'>";
		echo "<input type='radio' name='tipo' value='MMQ' />";
		echo "<label>Material medico quirurgico</label>";
		echo "</td>";
		echo "</tr>";

		echo "</table>";

		echo "<br>";
		echo '<input type="button" id="consultar" value="Consultar"></input>';
		echo '</div>';
		echo '<br><br>';
		echo '<div id="resultados"></div>';
		echo '</center>';

		echo '<center>';
		echo '<br><br>';
		echo '<table>';
		echo '<tr>';
		echo "<td align='center'>";
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>';
		echo "<input type=button value='Cerrar Ventana' onclick='cerrarVentana()'>";
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</center>';
		echo '<br><br><br><br>';
		
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		
		//Mensaje de alertas
		echo "<div id='msjAlerta' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/root/Advertencia.png'/>";
		echo "<br><br><div id='textoAlerta'></div><br><br>";
		echo '</div>';
		
	}
	
?>
	
	<style type="text/css">
		A:link { color: #2A5DB0; }
		
		.centrar{
					text-align:center;
					nowrap: nowrap;
		}
		
		.divlinea{
			display: -moz-inline-stack; /* FF2*/
			display: inline-block;
			vertical-align: top; /* BASELINE CORRECCIÓN*/
			zoom: 1; /* IE7 (hasLayout)*/
			*display: inline; /* IE */
			width: auto;
	
		}
		
		th[class|="type"]{
			cursor:pointer;
		}
		.derecha{
				text-align:right;
				nowrap: nowrap;
		}
		.fila1{
			font-size: 9pt;
		}
		.fila2{
			font-size: 9pt;
		}
		a img{
			border:0;
		}
		.caja_flotante_query{
			position: absolute;
			top:0;
		}
		
		 #tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
		 #tooltip h3, #tooltip div{margin:0; width:auto}
	</style>
	
	<script language="javascript">
			//Cuando carga la pagina inicializa eventos
			$(document).ready(function() {
			
				$("#consultar").click(function() {
					realizarConsulta();
				});
				
				
				$("#enlace_retornar").click( function(){
					$("#resultados").html("");
				} );
			});
			
			function realizarConsulta(){
			
				var f_inicial = $("#f_inicial").val();
				var f_final = $("#f_final").val();
				
				//Controlando que no elija mas de un año
				array_fi = f_inicial.split("-");
				array_ff = f_final.split("-");
				
				var start = new Date(array_fi[0], array_fi[1], array_fi[2]);
				var end = new Date(array_ff[0], array_ff[1], array_ff[2]);
		
				var diff = new Date(end - start);
				var days = diff/1000/60/60/24;
				
				if( days > 60 ){
					alerta("El rango de fechas es mayor es 60 dias");
					return;
				}
				if( days < 0 ){
					alerta("La fecha final debe ser mayor que la fecha inicial");
					return;
				}

				var origen = $('input[name=origen]:checked').val();
				var tipo = $('input[name=tipo]:checked').val();
				var wemp_pmla = $("#wemp_pmla").val();

				if(origen == undefined){
					alerta("Debe seleccionar un origen");
					return;
				}
				if(tipo == undefined){
					alerta("Debe seleccionar el tipo");
					return;
				}
				
				$.blockUI({ message: $('#msjEspere') });
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
				//Realiza el llamado ajax con los parametros de busqueda
				$.get('rep_devoluciones.php', { wemp_pmla: wemp_pmla, action: "consultar", origen: origen, tipo: tipo, fecha_inicial: f_inicial, consultaAjax:aleatorio, fecha_final: f_final } ,
					function(data) {
						//oculta el mensaje de cargando
						$.unblockUI();
						$("#resultados").html(data);
						$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
					});	
			}
			
			function mostrarOculto(idElemento, elemento){

				//ESCONDE O MUESTRA LA FILA OCULTA
				if ( document.getElementById(idElemento).style.display==''){
					document.getElementById(idElemento).style.display='none';
			    }
				else{
					document.getElementById(idElemento).style.display='';

				}
			}
			
			function alerta( txt ){
				$("#textoAlerta").text( txt );
				$.blockUI({ message: $('#msjAlerta') });
					setTimeout( function(){
									$.unblockUI();
								}, 2000 );
			}
			
	</script>

    </head>
    <body>

		<!-- EN ADELANTE ES LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>