<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Reporte de mercados
 * Fecha		:	2015-05-29
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Reporte para consultar los insumos programados para una cirugia que entre las fechas elegidas
					aun no han sido liquidados.
 * Condiciones  :
 *********************************************************************************************************

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  CAMBIOS PARA MIGRACION
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	CODIGO	|	FECHA		|	AUTOR 	|	DESCRIPCION	
----------------------------------------------------------------------------------------------------------------------------------------------
	MIGRA_1	|	2019-02-26	|	Jerson	|	Se corrigen tildes

	
----------------------------------------------------------------------------------------------------------------------------------------------



 Actualizaciones:
 * 2018-03-07 Edwar Jaramillo:
 	- Modificación a la estructura del reporte, actualización de query y librerias para el uso de ventanas modales.
	- Se agrega Nota para explicar el uso y resultados del reporte.
	- Modificación de la codificación del archivo .php y codificación del html a utf8 para evitar problemas con tíldes.
	- Mejor manejo del error cuando se hace la consulta ajax, en caso tal de ocurrir un error en el servidor se informe al usuario
		y evitar que el programa se quede en una aparente ejecución sin terminar.
	- Al resultado del reporte se le agregan nuevas columnas para indicar el stock para cada insumo, la cantidad cargada en mercados y el stock final (=stockInicial-cantidadEnMercado)
	- Se agregan colores de fondo a los número de turno y fechas que tengan insumos cargados y no estén liquidados de años o meses anteriores.
	- Modificación a la respuesta ajax del reporte para manejar mejor la respuesta mediante objeto json.
 * 2018-02-26 Edwar Jaramillo:
 	Nuevos filtros para consultar mercados anulados completamente o parcialmente, cuando un mercado es anulado
	completamente es porque el turno de cirugía no tiene ni un solo insumo agregado en la tabla de mercados cliame_207,
	parcialmente es porque el turno de cirugía tiene insumos asociados en la tabla de mercados pero desde el almacen
	de cirugía (ó facturadoras de cirugía) le han eliminado insumos al mercado (quedan en cliame_245).
 * 2016-04-12  Felipe Alvarez: Se le añade la posibilidad de generar el reporte sin tener en cuenta fechas , con esto se logra que se
								pueda ver en su totalidad todos los insumos sin entregarse.


 **********************************************************************************************************/

$wactualiza = "2018-03-07";
$consultaAjax='';

// if(!isset($_SESSION['user'])){
if(!isset($_SESSION["user"])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
// header('Content-type: text/html;charset=ISO-8859-1');





$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

//**************************FUNCIONES PHP********************************************//
	function cssAntiguedadFecha($fecha){
		$cssBg = '';
		$expFec = explode("-", $fecha);
		$yearCurr = date("Y");
		$yearMonthCurr = date("Ym");

		if(($yearCurr*1) > ($expFec[0]*1)){
			$cssBg = 'resaltar-muy-antiguo';
		} elseif (($yearCurr*1) == ($expFec[0]*1) && ($yearMonthCurr*1) > (($expFec[0].$expFec[1])*1)){
			$cssBg = 'resaltar-antiguo';
		}

		return $cssBg;
	}

	function generarReporte($conex, $wbasedato, $wtcx, $wmovhos, $wemp_pmla, $wusuario, $wcco, $fecha_inicio, $fecha_fin, $siFechas, $wanulados ){
		$tablaTmp = "tmpMerc".$wusuario;
		$style_insumos_borrados = "";

		$arr_ccos = centro_costos_reporte($conex, $wmovhos);
		$implcco = array_keys($arr_ccos);
		$implcco = implode("','", $implcco);

		// 2018-02-26 Edwar Jaramillo, consultar mercados o insumos eliminados.
		if($wanulados != ''){
			$qaux = "DROP TABLE IF EXISTS {$tablaTmp}";
			$res = mysql_query($qaux, $conex);

			$filtros_anulados = "";
			$and = "";
			if($siFechas == 'si')
			{
				// tcx11.Turfec BETWEEN '2017-01-01' AND '2017-02-28'
				$filtros_anulados = "{$and} tcx11.Turfec BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."'";
				$and = " AND";
			}

			$filtro_cco = "";
			if($wcco != '')
			{
				$filtro_cco = " AND tcx12.Quicco = '".trim($wcco)."'";
			}

			$in_not_in = 'NOT IN'; // Mercado borrado totalmente, el turno no existe en tabla de mercados cliame_207
			$style_insumos_borrados = '<div style="background-color: #FFFFCC;font-size: 8pt;">
											SE ESTÁ CONSULTANDO MERCADOS O INSUMOS QUE FUERON BORRADOS (Mercado anulado parcialmente o totalmente).
											<br>Las cantidades usadas en estos artículos <strong>no influyen</strong> en los saldos reales del stock.
										</div>';
			if($wanulados == 'parcial')
			{
				$in_not_in = 'IN'; // Insumos borrados parcialmente, el turno existe con mercado en la tabla cliame_207 pero también existe en cliame_245 con insumos borrados
			}

			$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tablaTmp}
					(INDEX idxtur(Turtur))
					SELECT 	tcx11.Turtur, tcx11.Turfec, tcx11.Turhis AS his, tcx11.Turnin AS ing, tcx11.Turdoc AS doc, tcx11.Turnom AS nompac, mch11.Ccocod AS cco, mch11.Cconom AS cconom
					FROM  	{$wtcx}_000011 AS tcx11
					      	INNER JOIN
					      	{$wtcx}_000012 AS tcx12 ON (tcx11.Turqui = tcx12.Quicod {$filtro_cco})
					      	INNER JOIN
					      	{$wmovhos}_000011 AS mch11 ON (mch11.Ccocod = tcx12.Quicco)
					        INNER JOIN
					        {$wbasedato}_000245 AS c245 ON (c245.Mpatur = tcx11.Turtur)
					WHERE {$filtros_anulados}
					      {$and} tcx11.Turest = 'on'
					GROUP BY tcx11.Turtur";

			$rs = mysql_query($sql, $conex) or die(mysql_error());

			$q= " 	SELECT 	tmp.Turtur AS tur, tmp.Turfec AS fec, c245.Mpatur AS tur245, tmp.his, tmp.ing, tmp.doc, tmp.nompac, tmp.cco, tmp.cconom,
					      	mv26.Artcod as art, mv26.Artcom as artnom, mv26.Artgen as artnomg, SUM( c245.Mpacan - c245.Mpadev ) as can, '0' AS stock141
					FROM 	{$tablaTmp} AS tmp FORCE INDEX ( idxtur )
					        INNER JOIN
					        {$wbasedato}_000245 AS c245 ON (c245.Mpatur = tmp.Turtur)
					        INNER JOIN
					        {$wmovhos}_000026 AS mv26 ON (mv26.Artcod = c245.Mpacom)
			        WHERE 	c245.Mpatur {$in_not_in} (
                            	SELECT c207.Mpatur from cliame_000207 AS c207 WHERE c207.Mpatur = tmp.Turtur group by c207.Mpatur
                           	)
				  	GROUP BY tmp.Turtur, c245.Mpacom
					ORDER BY tmp.cco, mv26.Artcod";

			// echo "<div style='text-align:left;'><pre>".$q."</pre></div>";
		}
		// 2016-04-12  Felipe Alvarez Posibilidad de generar el reporte sin fechas , con el fin que salgan todos los insumos, si el parametro sifechas = no , trae todos los
		// insumos sin tener en cuenta las fechas
		elseif($siFechas=='no')
		{
			$and = "";
			$filtro_cco = "mv141.Salser IN ('$implcco')";
			if( $wcco != "" ){
				$and = " AND Mpacco = '".trim($wcco)."' ";
				$filtro_cco = "mv141.Salser = '".trim($wcco)."'";
			}

			// V2
			$q = "	SELECT  tcx11.Turhis as his, tcx11.Turnin as ing, tcx11.Turtur as tur, tcx11.Turfec as fec, tcx11.Turdoc as doc, tcx11.Turnom as nompac,
							( cli207.Mpacan - cli207.Mpadev ) as can , cli207.Mpacom , mv11.Ccocod as cco, mv11.Cconom as cconom,
							mv26.Artcod as art, mv26.Artcom as artnom, mv26.Artgen as artnomg,
						 	(mv141.Salant+mv141.Salent-(mv141.Salsal)) AS stock141
					FROM  	{$wtcx}_000011 AS tcx11
							INNER JOIN
							{$wbasedato}_000207 AS cli207 ON (tcx11.Turtur = cli207.Mpatur AND cli207.Mpalux != 'on' AND cli207.Mpaest = 'on' {$and})
							INNER JOIN
							{$wmovhos}_000026 AS mv26 ON (cli207.Mpacom = mv26.Artcod)
							INNER JOIN
							{$wmovhos}_000011 AS mv11 ON (cli207.Mpacco = mv11.Ccocod)
							LEFT JOIN
							{$wmovhos}_000141 AS mv141 ON (mv141.Salser = mv11.Ccocod AND cli207.Mpacom = mv141.Salart)
					WHERE 	tcx11.Turest = 'on'";

			// echo $q;
			// return;
		}
		else
		{
			$qaux = "DROP TABLE IF EXISTS {$tablaTmp}";
			$res = mysql_query($qaux, $conex);

			$andCco = "";
			if( $wcco != "" ){
				$andCco = " AND mv11.Ccocod = '".trim($wcco)."' ";
			}

			$q = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tablaTmp}
						(INDEX idxtmp(Turtur))
						SELECT	tcx11.Turtur, tcx11.Turfec, tcx11.Turhis, tcx11.Turnin, tcx11.Turdoc, tcx11.Turnom, mv11.Ccocod, mv11.Cconom
						FROM	{$wtcx}_000011 AS tcx11
								INNER JOIN
								{$wtcx}_000012 AS tcx12 ON (tcx11.Turqui = tcx12.Quicod)
								INNER JOIN
								{$wmovhos}_000011 AS mv11 ON (mv11.Ccocod = tcx12.Quicco {$andCco})
						WHERE	tcx11.Turfec BETWEEN '{$fecha_inicio}' AND '{$fecha_fin}'
								AND tcx11.Turest = 'on'";

			$rs = mysql_query($q, $conex) or die(mysql_error());

			$q= " 	SELECT 	tmp.Turhis as his, tmp.Turnin as ing, tmp.Turtur as tur, tmp.Turfec as fec, tmp.Turdoc as doc, tmp.Turnom as nompac, tmp.Ccocod as cco, tmp.Cconom as cconom,
						 	mv26.Artcod as art, mv26.Artcom as artnom, mv26.Artgen as artnomg, ( cli207.Mpacan - cli207.Mpadev ) as can,
						 	(mv141.Salant+mv141.Salent-(mv141.Salsal)) AS stock141
					FROM 	{$tablaTmp} AS tmp FORCE INDEX ( idxtmp )
							INNER JOIN
							{$wbasedato}_000207 AS cli207 ON (tmp.Turtur = cli207.Mpatur AND cli207.Mpalux != 'on' AND cli207.Mpaest = 'on')
							INNER JOIN
							{$wmovhos}_000026 AS mv26 ON (cli207.Mpacom = mv26.Artcod)
							LEFT JOIN
							{$wmovhos}_000141 AS mv141 ON (mv141.Salser = tmp.Ccocod AND cli207.Mpacom = mv141.Salart)
					ORDER BY tmp.Ccocod, mv26.Artcod";
			//echo $q;
		}

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 	// while( $row_explain = mysql_fetch_assoc($res)){
	 	// 	echo "<div style='text-align:left;'><pre>".print_r($row_explain,true)."</pre></div>";
	 	// }
		$num = mysql_num_rows($res);

		$arr_datos = array();

		if ($num > 0){
			while( $row = mysql_fetch_assoc($res) ){

				if( $row['can'] == 0 )
					continue;

				if( array_key_exists( $row['cco'], $arr_datos ) == false ){
					$arr_datos[ $row['cco'] ] = array();
					$arr_datos[ $row['cco'] ]['nombrecco'] = $row['cconom'];
					$arr_datos[ $row['cco'] ]['articulos'] = array();
				}
				if( array_key_exists( $row['art'], $arr_datos[ $row['cco'] ]['articulos'] ) == false ){
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ] = array();
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['artnom'] = $row['artnom'];
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['artnomg'] = $row['artnomg'];
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['cantidad'] = 0;
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['stock141'] = $row['stock141'];
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['pacientes'] = array();
				}
				$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['cantidad']+= $row['can'];

				array_push( $arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['pacientes'], $row );
			}
		}

		$html_abrirTablaDetalle = "	<tr class='detalle fondoAmarillo' style='display:none;'>
										<td colspan=6 align='center'>
											<div class='div_detalle' align='center' style='margin: 10 0 10 0;'>
												<table class='tabla_detalle' align='center' border='1' bordercolor='white'>
													<tr class='encabezadotabla'>
														<td colspan=6 align='center'><font size=3><b>Pacientes</b></font>
														</td>
													</tr>
													<tr class='encabezadoTabla'>
														<td align='center'>Historia</td>
														<td align='center'>Ingreso</td>
														<td align='center'>Nombre</td>
														<td align='center'>Turno</td>
														<td align='center'>Fecha</td>
														<td align='center'>Cantidad</td>
													</tr>";

		$html = "";

		$html .= "<div style='width: 1000px;'>";

		$msj_cargado_sin_liquidar = 'Cargado sin liquidar';
		if($wanulados != ''){
			$msj_cargado_sin_liquidar = 'Cantidad anulada';
		}

		$html .= $style_insumos_borrados;
		if($wanulados == ''){
			$html .= "<div style='font-size:7pt;text-align:left;'>[<span class='resaltar-muy-antiguo'>Turno años anteriores</span>, <span class='resaltar-antiguo'>Turno meses anteriores</span>]</div>";
		}
		foreach($arr_datos as $cod_cco => $dato ){
			$dato['nombrecco'] = strtoupper( $dato['nombrecco'] );

			$html .= "
					<div class='desplegables' style='width:100%;'>
					<h3><b>* ".$cod_cco.'-'.$dato['nombrecco']." *</b></h3>
					<div>
						<table class='entidades' align='center' width='800px' border='1' bordercolor='white'>
						<tr class='encabezadotabla'>
							<td colspan=6 align='center'><font size=4><b>ARTICULOS SIN LIQUIDAR DEL PISO ".utf8_encode($dato['nombrecco'])."</b></font></td>
				 		</tr>
						<tr class='encabezadoTabla'>
							<td align='center'>Codigo</td>
							<td align='center'>Nombre Comercial</td>
							<td align='center'>Nombre Genérico</td>
							<td align='center'>Stock inicial</td>
							<td align='center'>".$msj_cargado_sin_liquidar."</td>
							<td align='center'>Saldo físico<br>(stock-cargado)</td>
						</tr>";
			$class='fila1';
			$classdet='fila1';

			$historia = "";
			$ingreso = "";
			$fecha_data = "";

			//Ordenar por cantidad
			uasort($dato['articulos'], function ($a, $b) { return $b['cantidad'] - $a['cantidad']; });

			foreach( $dato['articulos'] as $codArt=>$fila ){

				($class == "fila2" )? $class = "fila1" : $class = "fila2";
				$stock141 = 0;
				$saldo_fisico = 0;
				if($wanulados == ''){
					$stock141 = number_format((double)$fila['stock141'],0,'.',',');

					// if($fila['stock141'] < 0){
					// 	$fila['stock141'] = 0;
					// }

					$saldo_fisico = ($fila['stock141'] - $fila['cantidad']);
					$saldo_fisico = number_format((double)$saldo_fisico,0,'.',',');
				} else {
					$stock141 = '--';
					$saldo_fisico = '--';
				}

				$html .= "<tr class='".$class."' onclick='mostrarDetalle(this)' style='cursor:pointer;'>
						<td class='cssBorder' align='center'>".$codArt."</td>
						<td class='cssBorder' align='left'>".utf8_encode($fila['artnom'])."</td>
						<td class='cssBorder' align='left'>".utf8_encode($fila['artnomg'])."</td>
						<td class='cssBorder' align='right'>".$stock141."</td>
						<td class='cssBorder' align='right'>".number_format((double)$fila['cantidad'],0,'.',',')."</td>
						<td class='cssBorder' align='right'>".$saldo_fisico."</td>
					 </tr>";

				//Detalle de los pacientes
				$html .= $html_abrirTablaDetalle;

				//Ordenar por cantidad
				// uasort($fila['pacientes'], function ($a, $b) { return $b['can'] - $a['can']; });
				uasort($fila['pacientes'], function ($a, $b) { return str_replace("-", "", $a['fec']) - str_replace("-", "", $b['fec']); });

				foreach($fila['pacientes'] as $filap){
					$cssAntiguo = '';
					if($wanulados == ''){
						$cssAntiguo = cssAntiguedadFecha($filap['fec']);
					}

					($classdet == "fila2" )? $classdet = "fila1" : $classdet = "fila2";
					$html .= "<tr class='".$classdet."'>
								<td align='center'>".$filap['his']."</td>
								<td align='center'>".$filap['ing']."</td>
								<td align='left'>".utf8_encode($filap['nompac'])."</td>
								<td class='padding_td ".$cssAntiguo."' align='center'>".$filap['tur']."</td>
								<td class='padding_td ".$cssAntiguo."' align='center'>".$filap['fec']."</td>
								<td align='right'>".number_format((double)$filap['can'],0,'.',',')."</td>
							</tr>";
				}
				$html .= "</table>"; // cerrar la tabla_detalle
				$html .= "</div>"; // cerrar el div_detalle
				$html .= "</td>";
				$html .= "</tr>"; //cerrar el tr class='detalle'
			}
			$html .= "</table>";
			$html .= "</div>";
			$html .= "</div>";
		}

		if( count( $arr_datos ) == 0 ){
			$html .= "<font size=4><b>SIN RESULTADOS</b></font>";
		}
		$html .= "<br><br>";

		$html .= "</div>";
		return $html;
	}

	function centro_costos_reporte($conex, $wmovhos){
		$arr_ccos = array();
		$q = " SELECT Ccocod as cod, Cconom as nom "
			."   FROM ".$wmovhos."_000011 "
			."  WHERE Ccocir = 'on'
			ORDER BY Ccoord";
		$res = mysql_query($q,$conex);
		while( $row = mysql_fetch_assoc($res) ){
			$arr_ccos[$row['cod']] = utf8_encode($row['nom']);
		}
		return $arr_ccos;
	}

//**************************FIN FUNCIONES PHP********************************************//

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
	$action = $_REQUEST['action'];
	if( $action == "generarReporte"){
		$data['html'] = generarReporte($conex, $wbasedato, $wtcx, $wmovhos, $wemp_pmla, $wusuario, $wcco, $fecha_inicio, $fecha_fin , $siFechas, $wanulados );
	}
	echo json_encode($data);
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");


// vistaInicial();
$anio = date("Y");
$anio--;
$width_sel = " width: 80%; ";
$u_agent = $_SERVER['HTTP_USER_AGENT'];
if(preg_match('/MSIE/i',$u_agent))
	$width_sel = "";

$arr_ccos = centro_costos_reporte($conex, $wmovhos);

$ccos = "";
if(count($arr_ccos) > 0 ){
	foreach($arr_ccos as $codcco => $nom){
		$ccos .= "<option value=".$codcco.">".$codcco.'-'.$nom."</option>";
	}
}

?>
	<html>
	<head>
	<title>Reporte de Mercados</title>
	<meta charset="utf-8">

    <script src="../../../include/root/jquery.min.js"></script>

    <script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>

    <script src="../../../include/root/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

	<style>
		/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
		.ui-datepicker {font-size:12px;}
		/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
		.ui-datepicker-cover {
			display: none; /*sorry for IE5*/
			display/**/: block; /*sorry for IE5*/
			position: absolute; /*must have*/
			z-index: -1; /*must have*/
			filter: mask(); /*must have*/
			top: -4px; /*must have*/
			left: -4px; /*must have*/
			width: 200px; /*must have*/
			height: 200px; /*must have*/
		}

		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
		#tooltip h3, #tooltip div{margin:0; width:auto}
		.loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            border-bottom: 5px solid #3498db;
            width: 30px;
            height: 30px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }
        .titulopagina2
        {
            border-bottom-width: 1px;
            /*border-color: <?=$bordemenu?>;*/
            border-left-width: 1px;
            border-top-width: 1px;
            font-family: verdana;
            font-size: 18pt;
            font-weight: bold;
            height: 30px;
            margin: 2pt;
            overflow: hidden;
            text-transform: uppercase;
        }
        .fila1 {
            background-color:   #C3D9FF;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .fila2 {
            background-color:   #E8EEF7;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .encabezadoTabla {
            background-color : #2a5db0;
            color            : #ffffff;
            font-size        : 9pt;
            font-weight      : bold;
            padding          : 1px;
            font-family      : verdana;
        }
        .resaltar-muy-antiguo{
        	background-color: #FFA500;
        }
        .resaltar-antiguo{
        	background-color: #FFF24D;
        }
        .cssBorder{
        	border: 1px solid #ffffff;
        }
        .padding_td{
        	padding-left: 5px;
        	padding-right: 5px;
        }
	</style>
	<script>
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		changeYear: true,
        changeMonth: true,
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

</script>
<script>

	//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		console.clear();
		$(".enlace_retornar").hide();
		$(".enlace_retornar").click(function() {
			restablecer_pagina();
		});

		$("#fecha_inicio, #fecha_fin").datepicker({
		  showOn: "button",
		  buttonImage: "../../images/medical/root/calendar.gif",
		  buttonImageOnly: true,
		  maxDate:"+0D"
		});
	});

    /**
     * [jAlert Simula el JAlert usado en las anteriores versiones de JQuery]
     * @param  {[type]} html   [description]
     * @param  {[type]} titulo [description]
     * @return {[type]}        [description]
     */
    function jAlert(html,titulo){
        if($("#jAlert").length == 0)
        {
          var div_jAlert = '<!-- Modal jAlert -->'
                            +'<div class="modal fade" id="jAlert" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">'
                            +'  <div class="modal-dialog" role="document">'
                            +'    <div class="modal-content">'
                            +'      <div class="modal-header">'
                            +'        <h4 class="modal-title" id="alertModalLabel">Modal title</h4>'
                            +'        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">'
                            +'          <span aria-hidden="true">&times;</span>'
                            +'        </button> -->'
                            +'      </div>'
                            +'      <div class="modal-body" >'
                            +'        ...'
                            +'      </div>'
                            +'      <div class="modal-footer">'
                            +'        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>'
                            +'        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->'
                            +'      </div>'
                            +'    </div>'
                            +'  </div>'
                            +'</div>';
          $("body").append(div_jAlert);
        }

        $("#jAlert").find(".modal-header").removeClass("bg-danger");

        $("#jAlert").find("#alertModalLabel").html(titulo);
        $("#jAlert").find(".modal-body").html(html);
        var bg = (titulo.toLowerCase() == 'alerta') ? 'bg-danger': 'bg-primary';
        $("#jAlert").find(".modal-header").addClass(bg);
        $("#jAlert").modal({ backdrop: 'static',
                             keyboard: false}).css("z-index", 2030);
        if((titulo.toLowerCase() == 'alerta')) { $("#jAlert").css("z-index", 2030); }

        $("#jAlert").on('hidden.bs.modal', function (event) {
          if ($('.modal:visible').length) //check if any modal is open
          {
                $('body').addClass('modal-open');//add class to body
          }
        });
    }


    /**
     * [mensajeFailAlert: Muestra un mensaje en pantalla cuando se generó un error en la respuesta ajax]
     * @param  {[type]} mensaje     [description]
     * @param  {[type]} xhr         [description]
     * @param  {[type]} textStatus  [description]
     * @param  {[type]} errorThrown [description]
     * @return {[type]}             [description]
     */
    function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
    {
        var msj_extra = '';
        msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
        // jAlert($("#failJquery").val()+msj_extra, "Mensaje");
        jAlert($("#failJquery").val()+msj_extra, "Alerta");
        // $(".alert").alert("Mensaje");
        $("#div_error_interno").html(xhr.responseText);
        // console.log(xhr);
        // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
        fnModalLoading_Cerrar();
        // $(".bloquear_todo").removeAttr("disabled");
    }

	function generarReporte(){
		$("#div_error_interno").html();
		$('input:radio[name=siFechas]:checked')

		// 2016-04-12  Felipe Alvarez Posibilidad de generar el reporte sin fechas , con el fin que salgan todos los insumos, si el parametro sifechas = no , trae todos los
		// insumos sin tener en cuenta las fechas
		if($('input:radio[name=siFechas]:checked').val() =='ok') {
			var siFechas ='si';
		}
		else
		{
			var siFechas ='no';
		}
		var wemp_pmla = $("#wemp_pmla").val();
		var wcco = $("#wcco").val();
		var fecha_inicio = $("#fecha_inicio").val();
		var fecha_fin = $("#fecha_fin").val();
		var wanulados = $("#wanulados").val();

		// $.blockUI({ message: $('#msjEspere') });
		fnModalLoading($('#msjEspere').html());

		//Realiza el llamado ajax con los parametros de busqueda
		$.get('rep_mercados.php', {
				wemp_pmla : wemp_pmla,
				wbasedato : $("#wbasedato").val(),
				wmovhos   : $("#wmovhos").val(),
				wtcx      : $("#wtcx").val(),
				action    : "generarReporte", wcco: wcco, fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, consultaAjax: '',
				siFechas  : siFechas,
				wanulados : wanulados
			} ,
			function(data) {
				// $.unblockUI();
				fnModalLoading_Cerrar();
				$("#contenido").html(data.html);
				$(".enlace_retornar").show();
				// $(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$( ".desplegables" ).accordion({
					collapsible: true,
					active:0,
					heightStyle: "content",
					icons: null
				});
			},'json').done(function(){
				//
			}).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
	}

	function mostrarDetalle(obj){
		obj = jQuery(obj);
		obj.next(".detalle").toggle();
	}

	//Funcion que se activa cuando se presiona el enlace "retornar"
	function restablecer_pagina(){
		$("#contenido").html("");
		$("#wcco").val("");
		$("#wanulados").val("");
		$(".enlace_retornar").hide();
	}

    /**
     * [fnModalLoading: Es función se encarga de mostrar una ventana modal cada vez que se hace un llamado ajax con el fin de bloquear la página web hasta que se
     *                    se genere una respuesta y evitar que el usuario genere más eventos (click) sin terminar la petición anterior y evitar problemas
     *                    en la veracidad de datos]
     * @return {[type]} [description]
     */
    function fnModalLoading(msje_anexo)
    {
        var msj = (msje_anexo == undefined) ? '': msje_anexo;
        $("#div_loading").find("#msj_anexo_loading").html(msj);
        $("#div_loading").modal({backdrop: 'static',
                                keyboard: false});
        $("#div_loading").css("z-index", "9500");
    }

    /**
     * [fnModalLoading_Cerrar: complemento a la función fnModalLoading, esta se encarga de cerrar la ventana modal]
     * @return {[type]} [description]
     */
    function fnModalLoading_Cerrar()
    {
        $("#div_loading").modal('hide');
        $("#div_loading").find("#msj_anexo_loading").html("");
    }

</script>
</head>

<body width="100%">
	<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
	<input type='hidden' id ='wemp_pmla' value="<?=$wemp_pmla?>"/>
	<input type='hidden' id ='wbasedato' value="<?=$wbasedato?>"/>
	<input type='hidden' id ='wmovhos' value="<?=$wmovhos?>"/>
	<input type='hidden' id ='wtcx' value="<?=$wtcx?>"/>

	<?=encabezado("<div class='titulopagina2'>Reporte de saldos con mercados sin liquidar</div>", $wactualiza, "clinica");?>

	<div style="" class="container">

		<div class="alert alert-warning" role="alert">
				<!-- <strong>NOTA: </strong> Este reporte se encarga de consultar el stock de insumos que están asociados a turnos de cirugía y por cada uno se verá la cantidad cargada en los mercados que están sin liquidar.
				El reporte se puede especificar por centro de costos y por fechas, permitiendo filtrar los turnos de cirugía que pertenezcan al centro de costo especificado y entre las fechas
				establecidas. La opción de consultar mercados o insumos anulados, no influyen en el stock real de cada insumo, esto es solo para consultar los insumos o mercados que fueron eliminados
				y que ántes estuvieron asociados a un turno de cirugía. -->
			<!--MIGRA_1-->
			<p style="text-align: justify; font-size: 9pt;">
				<strong>NOTA: </strong><br>
				<strong>Si se coloca la opci&oacute;n SI tener en cuenta fechas: </strong>
				Este reporte se encarga de consultar el stock de insumos del centro de costo seleccionado y por cada insumo se ver&aacute; la cantidad cargada en los mercados de Cirug&iacute;as sin liquidar en el rango de fechas especificado.
				<br><br>
				<strong>Si se coloca la opci&oacute;n NO tener en cuenta fechas: </strong>
				El reporte mostrar&aacute; el stock actual de insumos del centro de costo seleccionado y por cada insumo se ver&aacute; la cantidad cargada en los mercados de Cirug&iacute;as sin liquidar sin limite de fecha.
				<br><br>
				La opci&oacute;n de consultar mercados o insumos anulados, no influyen en el stock real de cada insumo, esto es solo para consultar los insumos o mercados que fueron eliminados y que &aacute;ntes estuvieron asociados a un turno de cirug&iacute;a.
			</p>
		</div>
		<!-- //------------TABLA DE PARAMETROS------------- -->
		<table align="center">
			<tr>
				<td colspan=2 align='center' class='encabezadotabla'>Centro de Costos</td>
			</tr>
			<tr>
				<td colspan=2 class='fila2' align='center'>
					<select id='wcco' style='<?=$width_sel?> margin:5px;'>
						<option value=''>TODOS</option>
						<?=$ccos?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" class="encabezadotabla">Consultar mercado/insumos anulados</td>
			</tr>
			<tr>
				<td colspan="2" class="fila2" align="center">
					<select id="wanulados" style="<?=$width_sel?> margin:5px;">
						<option value="">Mercados activos</option>
						<option value="total">Mercados anulados totalmente</option>
						<option value="parcial">Mercados anulados parcialmente</option>
					</select>
					<p style="font-size:8pt;text-align:left;margin-left:5px;">
						* Activos: son los turnos que tienen mercado sin liquidar.<br>
						* Totalmente: cuando se anularon todos los insumos del turno.<br>
						* Parcialmente: cuando se han anulado algunos insumos del turno.</p>
				</td>
			</tr>
			<tr>
				<td colspan=2 align='center' class='encabezadotabla'>Tener en cuenta Fechas</td>
			</tr>
			<!-- // 2016-04-12  Felipe Alvarez Posibilidad de generar el reporte sin fechas , con el fin que salgan todos los insumos -->
			<tr>
				<td class='fila2' align='center'>
					Si<input type='radio' name='siFechas' value='ok' checked >
				</td>
				<td class='fila2' align='center'>
					No<input type='radio' name='siFechas'  value='nook'>
				</td>
			</tr>
			<tr>
				<td colspan=2 align='center' class='encabezadotabla'>Fecha</td>
			</tr>
			<tr>
				<td class='fila2' align='center'>
					<input type='text' id='fecha_inicio' value='<?=date("Y-m-d")?>' disabled placeholder=' '>
				</td>
				<td class='fila2' align='center'>
					<input type='text' id='fecha_fin' value='<?=date("Y-m-d")?>' disabled placeholder=' '>
				</td>
			</tr>

			<tr class='fila2'>
				<td colspan=2 align='center'>
					<input type='button' value='Generar' onclick='generarReporte()' />
				</td>
			</tr>
		</table>
		<!-- //------------FIN TABLA DE PARAMETROS------------- -->

	</div><!-- //Gran contenedor -->
	<center>
		<br><br>
		<a class='enlace_retornar' href='#' >RETORNAR</a>
		<br><br>
		<br><br>
		<div id='contenido' style='display:;'></div>
		<br><br>
		<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />
	</center>

	<!-- Mensaje de espera -->
	<div id='msjEspere' style='display:none;'>
		<!-- <img src='../../images/medical/ajax-loader5.gif'/> -->
		<!-- <br>Por favor espere un momento ... -->
	</div>

	<!-- Modal loading -->
	<div class="modal fade" id="div_loading" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-primary">
	        <h4 class="modal-title" id="loadingModalLabel">Procesando ...</h4>
	        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button> -->
	      </div>
	      <div class="modal-body">
	        <div class="container-fluid">
	            <div class="row">
	                <div class="col-md-4 col-sm-4">
	                    <div class="loader pull-right"></div>
	                </div>
	                <div class="col-md-6 col-sm-6">Espere un momento por favor... <span class="text-info" id="msj_anexo_loading"></span></div>
	            </div>
	        </div>
	      </div>
	      <!-- <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
	      </div> -->
	    </div>
	  </div>
	</div>
	<input type='hidden' name='failJquery' id='failJquery' value='El programa termin&oacute; de ejecutarse pero con algunos inconvenientes <br>(El proceso no se complet&oacute; correctamente)' >
	<div id="div_error_interno" style="display:none;"></div>

</body>
</html>