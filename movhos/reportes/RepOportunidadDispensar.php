<?php
include_once("conex.php");

/************************************************************************************************************
 * Reporte		:	Oportunidad en la Dispensacion
 * Fecha		:	2010-10-22	
 * Por			:	Edwin Molina Grisales
 * Descripcion	:	Generar un reporte en el que se muestre la diferencia que hay entre la hora
 * 					de grabación de un Kardex electronico y la primera dispensación del día por
 * 					centro de costos.
 *********************************************************************************************************
 
 Actualizaciones:
 2018-09-06 (Edwin MG)	Se modfican los queries con UNIX_TIMESTAMP y las columnas entrega camillero-pedido y Promedio entrega camillero-dispensación se comentan
 2013-11-07 (Jonatan Lopez)
			Se agrega la mismo consulta con "UNION" de la tabla movhos_000143 donde se use la tabla 000003 de movhos, 
			para que tenga en cuenta los datos de la tabla de contingencia, los registros de la tabla 143 deben estar activo Fdeest='on'.
 
 2012-11-22 (Frederick Aguirre Sanchez).
			Se cambia la estructura del script para hacer peticiones con ajax y bloquear la pantalla mientras se carga la respuesta.
			Se cambio el orden de algunos indices de las tablas temporales.
			Se agrego una condicion para traer datos de una de las tablas temporales.
 
 Julio 11 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones 
  consultaCentrosCostos que hace la consulta de los centros de costos 
  de un grupo seleccionado y dibujarSelect que dibuja el select con los 
  centros de costos obtenidos de la primera funcion. 
 
 2010-10-22. Se actualiza reporte para mostrar los datos de hora de grabación del kardex, hora de aprobación del kardex
 		 	 y hora de cumplimiento del camillero. Igualmente se muestra la diferencia de tiempo el recibo 
 		 	 del carro y la dispensación del kardex, la diferencia de tiempo entre el recibo 
 		 	 del carro y la aprobación del kardex, y la diferencia de tiempo entre el cumplimiento del camillero y la
 		 	 dispensación del kardex.
 
 **********************************************************************************************************/
 
 $wactualiz = "Noviembre 7 de 2013";
 
 if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";

	echo "<title>Reporte de Oportunidad en Dispensacion</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//



include_once("root/comun.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");

//nombres de las tablas temporales
$tablaTemporal = "Temporal_".date( "Ymdhis" );
//		$tablaTemporal = "Temporal";
$tablaTemporal000003 = "Temporal000003_".date( "Ymdhis" );
$tablaTemporal000055 = "Temporal000055_".date( "Ymdhis" );
$tablaTemporal000093 = "Temporal000093_".date( "Ymdhis" );
$tablaTemporal000053 = "Temporal000053_".date( "Ymdhis" );
$tablaTemporalcencam03 = "TemporalCencam03_".date( "Ymdhis" );



//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if($action=="consultar"){
		ejecutarConsulta( $_REQUEST['cco_origen'], $_REQUEST['cco_destino'],$_REQUEST['fecha_inicio'], $_REQUEST['fecha_final'],  $_REQUEST['opciones_reporte'] );
		return;
	}else{
		return;
	}
}
//FIN*LLAMADOS*AJAX**************************************************************************************************************//


function horaPedido( $his, $ing, $articulo, $fechaInicio, $horaInicio, $fecha, $ori, $cco ){
	
	global $conex;
	global $wbasedato;
	global $tablaTemporal000055;
	global $tablaTemporal000053;
	
	
	$val = "";
	
	 // SUM( UNIX_TIMESTAMP( CONCAT( '1970-01-01 ', b.hora_data ) ) )  
 	$sql = "(SELECT	
	 			 b.hora_data
	 		FROM
	 			{$tablaTemporal000055} b
	 		WHERE
	 			kauhis = '$his'
	 			AND kauing = '$ing'
	 			AND kaufec = '$fecha'
	 			AND kaumen IN ( 'Articulo creado', 'Articulo actualizado' )
	 			AND INSTR( kaudes, '$ori' ) != 0
	 			AND INSTR( kaudes, '$articulo' ) != 0
	 			AND INSTR( kaudes, '$fechaInicio' ) != 0
	 			AND INSTR( kaudes, SUBSTRING_INDEX( ',$horaInicio', ':', 1 ) ) != 0
	 		GROUP BY
	 			kauhis, kauing, kaumen, kaudes) ";
	 			
	if( $fechaInicio != $fecha ){
	 	
		 // SUM( UNIX_TIMESTAMP( CONCAT( '1970-01-01 ', a.hora_data ) ) )
	 	$sql .= "UNION
	 			(SELECT
		 			a.hora_data
		 		FROM
		 			{$tablaTemporal000053} a
		 		WHERE
		 			karhis = '$his'
		 			AND karing = '$ing'
		 			AND a.fecha_data = '$fecha'
		 			AND karcco = '$cco')
		 		";
	}
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	$numrows = mysql_num_rows( $res );
	 
	if( $numrows > 0 ){
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
			$val += strtotime( "1970-01-01 ".$rows[0] );
		}
		return $val/$numrows;
	}

	return "00:00:00";
}

function respuestaCamilleroHoraCumplimiento( $ccodes, $habitacion, $fecha, $horaDispensacion, $ccoori ){
	
	global $conex;
	global $wbasedato;
	global $wcencam;
	global $tablaTemporalcencam03;
	
//	$horaDispensacion = "00:00:00";
	
	$val = "";

	 $sql = "SELECT	
				hora_cumplimiento
	 		FROM
	 			{$tablaTemporalcencam03} a,
	 			{$wcencam}_000004 c
	 		WHERE
				a.fecha_data = '$fecha'
				AND habitacion LIKE '%$habitacion%'
				AND hora_respuesta >= '$horaDispensacion'
				AND motivo = 'DESPACHO DE MEDICAMENTOS'
				AND origen = c.nombre
				AND cco LIKE '$ccoori%'
			ORDER BY
				hora_respuesta ASC
	 		";

	
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = strtotime( "$fecha ".$rows[0] );
	}

	return $val;
}

function horaReciboCarro( $his, $ing, $fecha, $horaDispensacion, $cco, $art, $ori, $rec ){
	
	global $conex;
	global $wbasedato;
	global $tablaTemporal000003;
	global $tablaTemporal000093;
	
	$val = strtotime( "1970-01-01 00:00:00" );
	
	if( $rec != 'on' ){
		return $val;
	}
	
	$num = 0;
	
	$sql = "SELECT	
	 			recfre, rechre, recnum  
	 		FROM
	 			{$tablaTemporal000093} b
	 		WHERE
	 			rechis= '$his'
	 			AND recing = '$ing'
	 			AND recart = '$art'
	 			AND b.fecha_data >= '$fecha'
	 		"; //echo "<br>......<pre>$sql</pre>";
	 			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	for( $i = 0; $rows = mysql_fetch_array($res); ){

		// AND (UNIX_TIMESTAMP( CONCAT( fecha_data,' ', c.hora_data ) ) = UNIX_TIMESTAMP( '$fecha $horaDispensacion' )-1
			 // OR
			 // UNIX_TIMESTAMP( CONCAT( fecha_data, c.hora_data ) ) = UNIX_TIMESTAMP( '$fecha $horaDispensacion' )
		// )			 			
		if( $ori == 'SF' ){
	 		
			// $sql = "SELECT	
			 			// ".strtotime( "{$rows['recfre']} {$rows['rechre']}" ) ."
			 		// FROM
			 			// {$tablaTemporal000003} c
			 		// WHERE
			 			// fdeart = '{$art}'
			 			// AND fdenum = '{$rows['recnum']}'
			 			// AND c.fecha_data = '$fecha'
			 			// AND ( CONCAT( fecha_data,' ', c.hora_data ) = '".date( "Y-m-d H:i:s", strtotime( $fecha." ".$horaDispensacion )-1 )."'
			 				 // OR
			 				  // CONCAT( fecha_data,' ', c.hora_data ) = '".$fecha." ".$horaDispensacion."'
			 			// )
			 		// "; //echo "<br>......<pre>$sql</pre>";
					
			$sql = "SELECT	
			 			".strtotime( "{$rows['recfre']} {$rows['rechre']}" ) ."
			 		FROM
			 			{$tablaTemporal000003} c
			 		WHERE
			 			fdeart = '{$art}'
			 			AND fdenum = '{$rows['recnum']}'
			 			AND c.fecha_data = '$fecha'
			 			AND ( c.hora_data = '".date( "H:i:s", strtotime( $fecha." ".$horaDispensacion )-1 )."'
			 				 OR
			 				  c.hora_data = '".$horaDispensacion."'
			 			)
			 		"; //echo "<br>......<pre>$sql</pre>";
		}
		else{
			
			// $sql = "SELECT	
			 			// ".strtotime( "{$rows['recfre']} {$rows['rechre']}" ) ."   
			 		// FROM
			 			// {$tablaTemporal000003} c
			 		// WHERE
			 			// fdeari = '{$art}'
			 			// AND fdenum = '{$rows['recnum']}'
			 			// AND (UNIX_TIMESTAMP( CONCAT( fecha_data,' ', c.hora_data ) ) = UNIX_TIMESTAMP( '$fecha $horaDispensacion' )-1
			 				 // OR
			 				 // UNIX_TIMESTAMP( CONCAT( fecha_data, c.hora_data ) ) = UNIX_TIMESTAMP( '$fecha $horaDispensacion' )
			 			// )
			 			// AND c.fecha_data = '$fecha'
			 		// "; //echo "<br>......<pre>$sql</pre>";
					
			$sql = "SELECT	
			 			".strtotime( "{$rows['recfre']} {$rows['rechre']}" ) ."   
			 		FROM
			 			{$tablaTemporal000003} c
			 		WHERE
			 			fdeari = '{$art}'
			 			AND fdenum = '{$rows['recnum']}'
			 			AND c.fecha_data = '$fecha'
			 			AND ( c.hora_data = '".date( "H:i:s", strtotime( $fecha." ".$horaDispensacion )-1 )."'
			 				 OR
			 				  c.hora_data = '".$horaDispensacion."'
			 			)
			 		"; //echo "<br>......<pre>$sql</pre>";
		}
 		
		 $res2 = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		 
		 if( $rows2 = mysql_fetch_array($res2) ){
		 	$val += $rows2[0];
		 	return $val;
		 }
 	}
 	
 	return $val;
}
	
function ejecutarConsulta($wcco_origen, $wcco_destino, $wfecha_inicio, $wfecha_final, $wopciones_reporte){
	
		global $wbasedato;
		global $conex;
		global $wcencam;
		global $tablaTemporal;
		global $tablaTemporal000003;
		global $tablaTemporal000055;
		global $tablaTemporal000093;
		global $tablaTemporal000053;
		global $tablaTemporalcencam03;
		
		//$SALIDA="";
		
		//$SALIDA.="<BR> inicio".date('h:i:s');
		
		$calculoFechaIni = strtotime( $wfecha_inicio );
		$calculoFechaFin = strtotime( $wfecha_final );
		
		$calculoFechaTotal = strtotime( $wfecha_final )-strtotime( $wfecha_inicio )+strtotime( "1970-01-01 00:00:00" );
		
		if( date( "z", $calculoFechaTotal ) > 7 ){
			echo "<center><b>Solo se calcularan los 7 primeros d&iacute;as</b></center><br><br>";
			$wfecha_final = date( "Y-m-d", strtotime( $wfecha_inicio )+24*3600*6 );
		}		
		
		echo "<table align='center'>";
		 
		echo "<tr>";
		echo "<td class='encabezadotabla'>CCO Origen</td>";
		echo "<td class='fila1'>";
		echo $wcco_origen;
		echo "</td>"; 
		echo "</tr>";
		 
		echo "<tr>";
		echo "<td class='encabezadotabla'>CCO Destino</td>";
		echo "<td class='fila1'>";
		 
		if( $wcco_destino == '%' ){
		 	echo "% - Todos";
		}
		else{
			echo $wcco_destino;
		}
		
		$exp = explode( "-", $wcco_destino );
		 $wcco_destino =  $exp[0];
		 
		 $exp = explode( "-", $wcco_origen );
		 $wcco_origen =  $exp[0];
		 
		if( $wcco_origen == '1050' ){
			$origen = 'SF';
		}
		else{
			$origen = 'CM';
		}
		
		echo "</td>"; 
		echo "</tr>";
		 
		echo "<tr>";
		echo "<td class='encabezadotabla'>Fecha Inicial</td>";
		echo "<td class='fila1'>"; 
		echo $wfecha_inicio;
		echo"</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Fecha Final</td>";
		echo "<td class='fila1'>"; 
		echo $wfecha_final;
		echo"</td>";
		echo "</tr>";
		echo "</table>";
		
		
		//$SALIDA.="<BR>antes de crear temporales".date('h:i:s');
		//creando tabla temporal basasado en la 000003 de movhos
		//Se agrega la mismo consulta con "UNION" con la tabla 143 de movhos, para que tenga en cuenta los datos de la tabla de contingencia. 07 Noviembre 2013 Jonatan
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $tablaTemporal000003  
				(INDEX idx ( fdeart, fdenum, fecha_data ))
				SELECT 
					fecha_data, hora_data, fdenum, fdeart, fdeari 
				FROM 
					{$wbasedato}_000003 a
				WHERE 
					a.fecha_data BETWEEN '$wfecha_inicio' AND '$wfecha_final'
				UNION
				SELECT 
					fecha_data, hora_data, fdenum, fdeart, fdeari 
				FROM 
					{$wbasedato}_000143 a
				WHERE 
					a.fecha_data BETWEEN '$wfecha_inicio' AND '$wfecha_final'
					AND Fdeest = 'on'
				"; //echo "........<pre>$sql</pre>";
		 					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
		
		//creando tabla temporal basasado en la 000055 de movhos
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $tablaTemporal000055
				(INDEX idx ( kauhis, kauing, kaufec )) 
				SELECT 
					fecha_data, hora_data, kaufec, kaudes, kauhis, kauing, kaumen 
				FROM 
					{$wbasedato}_000055 a
				WHERE 
					a.fecha_data BETWEEN '$wfecha_inicio' AND '$wfecha_final'
					AND kaumen IN ( 'Articulo creado', 'Articulo actualizado' )
				"; //echo "........<pre>$sql</pre>";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
		
		
		//creando tabla temporal basasado en la 000053 de movhos
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $tablaTemporal000053
				(INDEX idx ( karhis, karing, fecha_data, karcco )) 
				SELECT 
					 fecha_data, hora_data, karhis, karing, karcco 
				FROM 
					{$wbasedato}_000053 a
				WHERE 
					a.fecha_data BETWEEN '$wfecha_inicio' AND '$wfecha_final'
				"; //echo "........<pre>$sql</pre>";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
					
					
		//creando tabla temporal basasado en la 000093 de movhos
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $tablaTemporal000093
				(INDEX idx ( rechis, recing, recart, fecha_data )) 
				SELECT 
					fecha_data, rechis, recing, recart, recnum, recfre, rechre 
				FROM 
					{$wbasedato}_000093 a
				WHERE 
					a.fecha_data BETWEEN '$wfecha_inicio' AND '$wfecha_final'
				"; //echo "........<pre>$sql</pre>";
		 					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
		
		//creando tabla temporal basasado en la 000003 de central de camilleros
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $tablaTemporalcencam03
				(INDEX idx ( fecha_data, habitacion(80), hora_respuesta )) 
				SELECT 
					* 
				FROM 
					{$wcencam}_000003 a
				WHERE 
					a.fecha_data BETWEEN '$wfecha_inicio' AND '$wfecha_final'
					AND central = 'SERFAR'
					AND motivo = 'DESPACHO DE MEDICAMENTOS'
				"; 
				//2012-11-22 se agrego la condicion "motivo = 'DESPACHO DE MEDICAMENTOS'", ya que las consultas sobre la tabla temporal siempre tienen esa condicion
				
		 					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
		 
		//creando tabla temporal
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $tablaTemporal as 
				SELECT 
					habhis, habing, habcco, b.fecha_data as habfec, cconom, habcod, ccorec
				FROM 
					{$wbasedato}_000067 b, {$wbasedato}_000011 c
				WHERE 
					b.fecha_data BETWEEN '$wfecha_inicio' AND '$wfecha_final'
					AND habcco = ccocod
					AND ccohos = 'on'
					AND habcco LIKE '$wcco_destino'
					AND ccorec = 'on'
					AND habhis != ''
					AND c.fecha_data <= '$wfecha_inicio'
				GROUP BY 
					1,2,3,4,5
				"; //echo "........<pre>$sql</pre>";
		 					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
		//$SALIDA.="<BR>al final de crear temporales".date('h:i:s');			
		//query principal
		//Busco todos los articulos en un rango de fechas del kardex
		// UNIX_TIMESTAMP( CONCAT( kadfec,' ', kadhdi ) ) as kadhdi, 
		$sql = "SELECT
					kadart, 
					kadhdi, 
					kading, 
					habcco, 
					habcod,
					a.kadfec as fecha,
					cconom,
					kadfin,
					kadhin,
					kadhis,
					kadcco,
					kadori,
					ccorec
				FROM
					{$wbasedato}_000054 a,
					{$tablaTemporal} b
				WHERE
					kadhis = habhis
					AND kading = habing
					AND kadfec = habfec
					AND kadori = '$origen'
					AND kadsus != 'on'
					AND kadhdi != '00:00:00'
					AND kadare = 'on'
				GROUP BY 
					habcco, a.kadfec, kadhis, kading, kadart, kadfin, kadhin
				";
						
//		echo "........<PRE>$sql</pre>";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		//$SALIDA.="<BR>primer select".date('h:i:s');			
		if( $numrows > 0 ){
			
			
			$i = 0;
			
			$ccoActual = '';
			$fecActual = '';
			$hisActual = '';
			$ingActual = '';
			$pedido = '';
			$dispensacion = '';
			$entregaCamillero = '';
			$reciboCarro = '';
			$hisingTotalArticulos = 0;
			$historiasReciboCarro = 0;
			
			$auxentregaCamillero = 0;
			$articulosEntregaCamillero = 0;
			$articulosReciboCarro = 0;
			//$SALIDA.="<BR>antes del for".date('h:i:s');	
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
				
				$rows[ 'kadhdi' ] = strtotime( $rows['fecha']." ".$rows['kadhdi'] );
				
				$ccos[ $rows['habcco'] ] = $rows['cconom'] ;
				
				if( $i != 0 && ( $rows[ 'kadhis' ] != $hisActual && $rows[ 'kading' ] != $ingActual ) ){
					
					if( $articulosEntregaCamillero == 0 ){
						$articulosEntregaCamillero = 1;
						$entregaCamillero = $dispensacion/$hisingTotalArticulos;
					}
					
					if( $articulosReciboCarro == 0 ){
						$articulosReciboCarro = 1;
						$reciboCarro = $dispensacion/$hisingTotalArticulos;
					}
					
					//Datos para el detalle
					$difReciboDispensacion = $reciboCarro/$articulosReciboCarro - $dispensacion/$hisingTotalArticulos + strtotime( "1970-01-01 00:0:00" );
					$difEntregaDispensacion = $entregaCamillero/$articulosEntregaCamillero - $dispensacion/$hisingTotalArticulos + strtotime( "1970-01-01 00:0:00" );
					$difDispensacionPedido = $dispensacion/$hisingTotalArticulos - $pedido/$hisingTotalArticulos + strtotime( "1970-01-01 00:0:00" );
					
					if( $entregaCamillero == $dispensacion/$hisingTotalArticulos ){
						$difEntregaPedido = strtotime( "1970-01-01 00:0:00" );
					}
					else{
						$difEntregaPedido = $entregaCamillero/$articulosEntregaCamillero - $pedido/$hisingTotalArticulos + strtotime( "1970-01-01 00:0:00" );
					}
					
					
					//Datos para el resumen
					@$resumen[ $ccoActual ]['entregaDispensacion'] += $difEntregaDispensacion;
					@$resumen[ $ccoActual ]['reciboDispensacion'] += $difReciboDispensacion;
					@$resumen[ $ccoActual ]['dispensacionPedido'] += $difDispensacionPedido;
					@$resumen[ $ccoActual ]['hisDispensacionPedido']++;
					
					@$resumen[ $ccoActual ]['entregaPedido'] += $difEntregaPedido;
					
					
					if( $entregaCamillero > $dispensacion/$hisingTotalArticulos ){
						@$resumen[ $ccoActual ]['hisEntregaDispensacion']++;
						@$totales['hisEntregaDispensacion']++;
						@$totales['totalEntregaDispensacion'] += $difEntregaDispensacion;
						
						@$resumen[ $ccoActual ]['hisEntregaPedido']++;
						@$totales['hisEntregaPedido']++;
						@$totales['totalEntregaPedido'] += $difEntregaPedido;
					}
					
					if( $reciboCarro > $dispensacion/$hisingTotalArticulos ){
						@$resumen[ $ccoActual ]['hisReciboDispensacion']++;
						@$totales['hisReciboDispensacion']++;
						@$totales['totalReciboDispensacion'] += $difReciboDispensacion;
					}
					
					//Totales
					@$totales['historias']++;
					@$totales['totalDispensacionPedido'] += $difDispensacionPedido;
					@$totales['hisDispensacionPedido']++;
					
					$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'horaPedido' ] = date( "H:i:s", $pedido/$hisingTotalArticulos );
					$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'horaDispensacion' ] = date( "H:i:s", $dispensacion/$hisingTotalArticulos );
					$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'horaEntregaCamillero' ] = ($entregaCamillero/$articulosEntregaCamillero == $reciboCarro/$articulosReciboCarro )? "SIN CAMILLERO" :date( "H:i:s", $entregaCamillero/$articulosEntregaCamillero );
					$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'horaReciboCarro' ] = ($dispensacion/$hisingTotalArticulos == $reciboCarro/$articulosReciboCarro )? "SIN RECIBO" :date( "Y-m-d H:i:s", $reciboCarro/$articulosReciboCarro );
					$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoReciboDispensacion' ] = @date( "H:i:s", $difReciboDispensacion );
					$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoEntregaDispensacion' ] = @date( "H:i:s", $difEntregaDispensacion );
					$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoDispensacioPedido' ] = date( "H:i:s", $difDispensacionPedido );
					$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoEntregaPedido' ] = date( "H:i:s", $difEntregaPedido );
					
					$pedido = '';
					$dispensacion = 0;
					$reciboCarro = 0;
					$hisingTotalArticulos = 0;
					$entregaCamillero = 0;
					$articulosReciboCarro = 0;
					$articulosEntregaCamillero = 0;
				}
				
				
				if( $rows[ 'habcco' ] != $ccoActual ){
					$ccoActual = $rows[ 'habcco' ];
				}
				
				if( $rows[ 'fecha' ] != $fecActual ){
					$fecActual = $rows[ 'fecha' ];
				}
				
				if( $rows[ 'kadhis' ] != $hisActual ){
					$hisActual = $rows[ 'kadhis' ];
				}
			
			
				if( $rows[ 'kading' ] != $ingActual ){
					$ingActual = $rows[ 'kading' ];
				}
//				if($rows[ 'kadhis' ] != $hisActual && $rows[ 'kading' ] != $ingActual){
						
				$pedido += horaPedido( $rows['kadhis'], $rows['kading'], $rows['kadart'], $rows['kadfin'], $rows['kadhin'], $rows['fecha'], $origen, $rows['kadcco'] );
				$dispensacion += $rows['kadhdi'];
				$auxreciboCarro = horaReciboCarro( $rows['kadhis'], $rows['kading'], $rows['fecha'], date( "H:i:s", $rows['kadhdi'] ), $wcco_origen, $rows['kadart'], $rows['kadori'], $rows['ccorec'] );
				
//				$reciboCarro += $auxreciboCarro;
				
				if( $auxreciboCarro > strtotime('1970-01-01 00:00:00') ){
					$reciboCarro += $auxreciboCarro;
					$articulosReciboCarro++; 
				}
				
				$auxentregaCamillero = respuestaCamilleroHoraCumplimiento( $rows['habcco'], $rows['habcod'], $rows['fecha'],  date( "H:i:s", $rows['kadhdi'] ), $wcco_origen );
				
				if( $auxentregaCamillero > strtotime( date( "Y-m-d H:i:s", $rows['kadhdi'] ) ) ){
					
					$entregaCamillero += $auxentregaCamillero;
					$articulosEntregaCamillero++;
				}
//				}
				
				$hisingTotalArticulos++;
			
			}
			//$SALIDA.="<BR>despues del for".date('h:i:s');	
			
			//$SALIDA.="<BR>Sigue el primer if".date('h:i:s');	
			if( $rows[ 'kadhis' ] != $hisActual && $rows[ 'kading' ] != $ingActual ){
					
				if( $articulosEntregaCamillero == 0 ){
					$articulosEntregaCamillero = 1;
					$entregaCamillero = $dispensacion/$hisingTotalArticulos;
				}
				
				if( $articulosReciboCarro == 0 ){
					$articulosReciboCarro = 1;
					$reciboCarro = $dispensacion/$hisingTotalArticulos;
				}
				
				//Datos para el detalle
				$difReciboDispensacion = $reciboCarro/$articulosReciboCarro - $dispensacion/$hisingTotalArticulos + strtotime( "1970-01-01 00:0:00" );
				$difEntregaDispensacion = $entregaCamillero/$articulosEntregaCamillero - $dispensacion/$hisingTotalArticulos + strtotime( "1970-01-01 00:0:00" );
				$difDispensacionPedido = $dispensacion/$hisingTotalArticulos - $pedido/$hisingTotalArticulos + strtotime( "1970-01-01 00:0:00" );
				
				if( $entregaCamillero == $dispensacion/$hisingTotalArticulos ){
					$difEntregaPedido = strtotime( "1970-01-01 00:0:00" );
				}
				else{
					$difEntregaPedido = $entregaCamillero/$articulosEntregaCamillero - $pedido/$hisingTotalArticulos + strtotime( "1970-01-01 00:0:00" );
				}
				
				
				//Datos para el resumen
				@$resumen[ $ccoActual ]['entregaDispensacion'] += $difEntregaDispensacion;
				@$resumen[ $ccoActual ]['reciboDispensacion'] += $difReciboDispensacion;
				@$resumen[ $ccoActual ]['dispensacionPedido'] += $difDispensacionPedido;
				@$resumen[ $ccoActual ]['hisDispensacionPedido']++;
				
				@$resumen[ $ccoActual ]['entregaPedido'] += $difEntregaPedido;
				
				
				if( $entregaCamillero > $dispensacion/$hisingTotalArticulos ){
					@$resumen[ $ccoActual ]['hisEntregaDispensacion']++;
					@$totales['hisEntregaDispensacion']++;
					@$totales['totalEntregaDispensacion'] += $difEntregaDispensacion;
					
					@$resumen[ $ccoActual ]['hisEntregaPedido']++;
					@$totales['hisEntregaPedido']++;
					@$totales['totalEntregaPedido'] += $difEntregaPedido;
				}
				
				if( $reciboCarro > $dispensacion/$hisingTotalArticulos ){
					@$resumen[ $ccoActual ]['hisReciboDispensacion']++;
					@$totales['hisReciboDispensacion']++;
					@$totales['totalReciboDispensacion'] += $difReciboDispensacion;
				}
				
				//Totales
				@$totales['historias']++;
				@$totales['totalDispensacionPedido'] += $difDispensacionPedido;
				@$totales['hisDispensacionPedido']++;
				
				$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'horaPedido' ] = date( "H:i:s", $pedido/$hisingTotalArticulos );
				$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'horaDispensacion' ] = date( "H:i:s", $dispensacion/$hisingTotalArticulos );
				$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'horaEntregaCamillero' ] = ($entregaCamillero/$articulosEntregaCamillero == $reciboCarro/$articulosReciboCarro )? "SIN CAMILLERO" :date( "H:i:s", $entregaCamillero/$articulosEntregaCamillero );
				$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'horaReciboCarro' ] = ($dispensacion/$hisingTotalArticulos == $reciboCarro/$articulosReciboCarro )? "SIN RECIBO" :date( "Y-m-d H:i:s", $reciboCarro/$articulosReciboCarro );
				$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoReciboDispensacion' ] = date( "H:i:s", $difReciboDispensacion );
				$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoEntregaDispensacion' ] = date( "H:i:s", $difEntregaDispensacion );
				$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoDispensacioPedido' ] = date( "H:i:s", $difDispensacionPedido );
				$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoEntregaPedido' ] = date( "H:i:s", $difEntregaPedido );
				
				$pedido = '';
				$dispensacion = 0;
				$reciboCarro = 0;
				$hisingTotalArticulos = 0;
				$entregaCamillero = 0;
				$articulosReciboCarro = 0;
				$articulosEntregaCamillero = 0;
			}
			
			//$SALIDA.="<BR>Sigue el segundo if DETALLADO".date('h:i:s');	
			if( $wopciones_reporte != '3' ){
				
				echo "<br><p align='center'><B>REPORTE DETALLADO</B></p>";
				
				//Impresion del detalle del reporte
				foreach( $detalle as $keyCco => $valueCco ){
					
					echo "<br><br>";
					echo "<table align='center'>";
					echo "<tr class='colorAzul5'>";
					echo "<td colspan='10'><b>";
					echo $keyCco."-".$ccos[ $keyCco ];
					echo "</b></td>";
					echo "</tr>";
					
					echo "<tr align='center' class='encabezadotabla'>";
					echo "<td width='70'>Fecha</td>";
					echo "<td width='70'>Historia</td>";
					echo "<td width='100'>Hora<br>de pedido</td>";
					echo "<td width='100'>Hora<br>dispensaci&oacute;n</td>";
					echo "<td width='100'>Hora<br>entrega camillero</td>";
					echo "<td width='100'>Hora<br>Recibo de carro</td>";
					echo "<td width='100'>Tiempo<br>Recibo-dispensaci&oacute;n</td>";
					// echo "<td width='100'>Tiempo<br>entrega camillero-Dispensaci&oacute;n</td>";
					// echo "<td width='100'>Tiempo<br>entrega camillero-Pedido</td>";
					echo "<td width='100'>Tiempo<br>Dispensacion-Pedido</td>";
					echo "</tr>";
					
					$i = 0;
									
					foreach( $valueCco as $keyFecha => $valueFecha ){
						
						foreach( $valueFecha as $keyHisIng => $valueHisIng ){
						
							$class = "class='fila".(($i%2)+1)."'";
							
	//						$detalle[ $ccoActual ][ $fecActual ][ $hisActual."-".$ingActual ][ 'tiempoDispensacioPedido' ] = "";
								
							echo "<tr $class  align='center'>";
							echo "<td>{$keyFecha}</td>";
							echo "<td>{$keyHisIng}</td>";
							echo "<td>".$valueHisIng[ 'horaPedido' ];
							echo "<td>".$valueHisIng[ 'horaDispensacion' ];
							echo "<td>".$valueHisIng[ 'horaEntregaCamillero' ];
							echo "<td>".$valueHisIng[ 'horaReciboCarro' ];
							echo "<td>".$valueHisIng[ 'tiempoReciboDispensacion' ];
							// echo "<td>".$valueHisIng[ 'tiempoEntregaDispensacion' ];
							// echo "<td>".$valueHisIng[ 'tiempoEntregaPedido' ];
							echo "<td>".$valueHisIng[ 'tiempoDispensacioPedido' ];
							echo "</tr>";
							
							$i++;
						}
						
					}
					
					echo "</table>";
				}
			}
			
			//$SALIDA.="<BR>Sigue el tercer if RESUMEN".date('h:i:s');	
			//impresion Resumen
			if( $wopciones_reporte != '2' ){

				echo "<br>";
				echo "<p align='center'><B>REPORTE RESUMIDO</B></p>";

				echo "<br>";
				echo "<table align='center'>";
				echo "<tr align='center' class='encabezadotabla'>";
				echo "<td>Servicio</td>";
				// echo "<td width='200'>Promedio<br>entrega camillero-pedido</td>";
				// echo "<td width='200'>Promedio<br>entrega camillero-dispensaci&oacute;n</td>";
				echo "<td>Promedio<br>Recibo-dispensaci&oacute;n</td>";
//				echo "<td>Promedio<br>Recibo-aprobaci&oacute;n</td>";
				echo "<td>Promedio<br>Dispensaci&oacute;n-pedido</td>";
				echo "<td>Total Historias</td>";
				echo "</tr>";
					
				$i = 0;
				foreach( $resumen as $key => $value ){

					$class = "class='fila".(($i%2)+1)."'";
						
					echo "<tr $class align='center'>";
					echo "<td align='left'>$key-{$ccos[ $key ]}</td>";
					// echo "<td>".@date( "H:i:s", $value['entregaPedido']/$value['hisEntregaPedido'] )."</td>";
					// echo "<td>".@date( "H:i:s", $value['entregaDispensacion']/$value['hisEntregaDispensacion'] )."</td>";
					echo "<td>".@date( "H:i:s", $value['reciboDispensacion']/$value['hisReciboDispensacion'] )."</td>";
					echo "<td>".date( "H:i:s", $value['dispensacionPedido']/$value['hisDispensacionPedido'] )."</td>";
					echo "<td>{$value['hisDispensacionPedido']}</td>";

					echo "</tr>";
				}

				echo "<tr class='encabezadoTabla' align='center'>";
				echo "<td align='center'>Total</td>";
				// echo "<td>".@date( "H:i:s", $totales['totalEntregaPedido']/$totales['hisEntregaPedido'] )."</td>";
				// echo "<td>".@date( "H:i:s", $totales['totalEntregaDispensacion']/$totales['hisEntregaDispensacion'] )."</td>";
				echo "<td>".@date( "H:i:s", $totales['totalReciboDispensacion']/$totales['hisReciboDispensacion'] )."</td>";
				echo "<td>".@date( "H:i:s", $totales['totalDispensacionPedido']/$totales['hisDispensacionPedido'] )."</td>";
				echo "<td>{$totales['historias']}</td>";
				echo "</tr>";
					
				echo "</table>";
			}
			
		}
		else{
			echo "<br>";
			echo "<p align='center'>NO SE GENERO NINGUN RESULTADO</p>";
		}
		
		//echo "SALIDA: ".$SALIDA;

}

function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;

		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		encabezado("REPORTE DE OPORTUNIDAD EN DISPENSACION", $wactualiz, "clinica");

		echo "<table align='center' class='rep_parametros'>";

		echo "<tr>";
		echo "<td class='encabezadotabla'>Centro de costos origen</td>";
		echo "<td class='fila1'>";

		echo "<select id='ccoorigen' style='width:100%'>";


		$cco="ccotra='on' and ccofac='on'";
		$filtro="--";

		$centrosCostos = consultaCentrosCostos($cco, $filtro);
		foreach ($centrosCostos as $centroCostos)
		{
		echo "<option value='".$centroCostos->codigo."-".$centroCostos->nombre."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";	
		}	

		echo "</select>";

		echo "</td>"; 
		echo "</tr>";

		echo "<tr>";
		echo "<td class='encabezadotabla'>Centro de costos destino</td>";
		echo "<td class='fila1'>";

		echo "<select id='ccodestino' style='width:100%'>";
		echo "<option value='%'>% - Todos</option>";

			 
		$cco="ccohos='on' and ccourg<>'on' and ccoest='on' and ccoing<>'on'";
		$filtro="--";

		$centrosCostos = consultaCentrosCostos($cco, $filtro);
		foreach ($centrosCostos as $centroCostos)
		{
		echo "<option value='".$centroCostos->codigo."-".$centroCostos->nombre."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";	
		}
		 
		echo "</select>";

		echo "</td>"; 
		echo "</tr>";
		
		$fecha_hoy = date("Y-m-d");
		$fecha_pddm = date("Y-m");
		$fecha_pddm.="-01";

		echo "<tr>";
		echo "<td class='encabezadotabla'>Fecha Inicial</td>";
		echo "<td class='fila1'>"; 
		campoFechaDefecto( "fecini", $fecha_pddm );
		echo"</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Fecha Final</td>";
		echo "<td class='fila1'>"; 
		campoFechaDefecto( "fecfin", $fecha_hoy );
		echo"</td>";
		echo "</tr>";
		echo "</table>";


		echo "<br><br>"; 
		echo "<table align='center' class='rep_parametros'>";
		echo "<tr>";
		echo "<td>";
		echo "<INPUT type='radio' name='opcionesReporte' value='1' checked> Detallado y Resumido";
		echo "</td>";
		echo "<tr>";
		echo "<td>";
		echo "<INPUT type='radio' name='opcionesReporte' value='2'> Detallado";
		echo "</td>";
		echo "<tr>";
		echo "<td>";
		echo "<INPUT type='radio' name='opcionesReporte' value='3'> Resumido";
		echo "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<br><br>";
		echo '<center>';
		echo "<input type='button' value='Consultar' id='consultar' class='rep_parametros' style='width:100'>";
		echo "<br><br>"; 
		echo "<div id='resultados'></div>";
		echo "<br><br>"; 
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()'>";
		echo "<br><br>"; 
		echo "<br><br>";
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		echo '</center>';
}
?>
<script>

//************cuando la pagina este lista...**********//
			$(document).ready(function() {
				//Cuando cargue completamente la pagina
				
				//agregar eventos a campos de la pagina
				$("#consultar").click(function() {
					realizarConsulta();
				});
				
				$("#enlace_retornar").hide();
				$("#enlace_retornar").click(function() {
					restablecer_pagina();
				});
			});
			
			function restablecer_pagina(){
				$(".rep_parametros").fadeIn('slow');
				$('#resultados').hide('slow');
				$("#enlace_retornar").hide('slow');
			}
			
			function realizarConsulta(){
			
				var wemp_pmla = $("#wemp_pmla").val();
				var cco_origen = $("#ccoorigen").val();
				var cco_destino = $("#ccodestino").val();
				var f_inicio = $("#fecini").val();
				var f_final = $("#fecfin").val();
				var opcs_reporte = $('input[name=opcionesReporte]:checked').val();

				//muestra el mensaje de cargando
				$.blockUI({ message: $('#msjEspere') });
				
				$("#enlace_retornar").fadeIn('slow');
				
				//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
						
				//Realiza el llamado ajax con los parametros de busqueda
				$.get('RepOportunidadDispensar.php', { wemp_pmla: wemp_pmla, action: "consultar", cco_origen: cco_origen, cco_destino: cco_destino, fecha_inicio: f_inicio, fecha_final: f_final, opciones_reporte: opcs_reporte,  consultaAjax: aleatorio} ,
					function(data) {
						//oculta el mensaje de cargando
						$(".rep_parametros").hide();
						$.unblockUI();
						$('#resultados').html(data);	
						$('#resultados').show('slow');						
					});			
			}
</script>

</head>
    <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>
