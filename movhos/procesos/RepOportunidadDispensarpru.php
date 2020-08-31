<?php
include_once("conex.php");

/************************************************************************************************************
 * Reporte		:	Oportunidad en la Dispensacion
 * Fecha		:	2009-10-26	
 * Por			:	Edwin Molina Grisales
 * Descripcion	:	Generar un reporte en el que se muestre la diferencia que hay entre la hora
 * 					de grabación de un Kardex electronico y la primera dispensación del día por
 * 					centro de costos.
 **********************************************************************************************************/

/**
 * Se halla el código y el nombre del centro de costos
 * 
 * @param $cco
 * @return unknown_type
 */

function getCCO( $cco ){

	global $conex;
	global $wbasedato;
	 
	$ccodes = "";
	
	$sql = "SELECT	
	 			ccocod, cconom  
	 		FROM
	 			{$wbasedato}_000011
	 		WHERE
	 			ccocod = '$cco'";
	 			
	 $res = mysql_query( $sql, $conex );
	 
	for( $i = 0;$rows = mysql_fetch_array($res); $i++ ){
		$ccodes = $rows[0]." - ".$rows[1];
	}
	
	return $ccodes;
}

/************************************************************************************************
 * 
 ***********************************************************************************************/

include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = $institucion->baseDeDatos;
$wentidad = $institucion->nombre;

$wactualiz = "Octubre 26 de 2009";
encabezado("REPORTE DE OPORTUNIDAD EN DISPENSACION", $wactualiz, "clinica");

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );

session_start();
if(false && !isset($_SESSION['user'])){
	echo "error";
}
else{
	
	if( !isset($encabezado) ){
		$encabezado = true;
	}
	
	//Inicio del Reporte, pide al usuario los parametros necesarios para generar el reporte
	if( $encabezado || ( $fecfin < $fecini ) ){
		
		if( !isset($fecini) ){
		 	$fecini = date("Y-m-d");
		 }

		if( !isset($fecfin) ){
		 	$fecfin = date("Y-m-d");
		 }
		 
		 echo "<form method='post'>";
		 
		 echo "<table align='center'>";
		 
		 echo "<tr>";
		 echo "<td class='encabezadotabla'>Centro de costos</td>";
		 echo "<td class='fila1'>";

		 echo "<select name='ccoorigen'>";
		 
	 	$sql = "SELECT	
		 			ccocod, cconom  
		 		FROM
		 			{$wbasedato}_000011
		 		WHERE
		 			ccotra='on'
		 			and ccofac='on'";
		 			
		 $res = mysql_query( $sql, $conex );
		 
		 for( ;$rows = mysql_fetch_array($res); ){
		 	echo "<option value='{$rows['ccocod']}'>{$rows['ccocod']}-{$rows['cconom']}</option>";
		 }
		 
		 echo "</select>";
		 
		 echo "</td>"; 
		 echo "</tr>";
		 
		 echo "<tr>";
		 echo "<td class='encabezadotabla'>Centro final</td>";
		 echo "<td class='fila1'>";

		 echo "<select name='ccodestino'>";
		 echo "<option value='%'>% - Todos</option>";
		 
		$sql = "SELECT	
		 			ccocod, cconom  
		 		FROM
		 			{$wbasedato}_000011
		 		WHERE
		 			ccohos='on'
		 			and ccourg<>'on'
		 			and ccoest='on'
		 			and ccoing<>'on'";
		 			
		 $res = mysql_query( $sql, $conex );
		 
		 for( ;$rows = mysql_fetch_array($res); ){
		 	echo "<option value='{$rows['ccocod']}'>{$rows['ccocod']}-{$rows['cconom']}</option>";
		 }
		 
		 echo "</select>";
		 
		 echo "</td>"; 
		 echo "</tr>";
		 
		 echo "<tr>";
		 echo "<td class='encabezadotabla'>Fecha Inicial</td>";
		 echo "<td class='fila1'>"; 
		 campoFechaDefecto( "fecini", date("Y-m-d") );
		 echo"</td>";
		 echo "</tr>";
		 echo "<tr>";
		 echo "<td class='encabezadotabla'>Fecha Inicial</td>";
		 echo "<td class='fila1'>"; 
		 campoFechaDefecto( "fecfin", date("Y-m-d") );
		 echo"</td>";
		 echo "</tr>";
		 echo "</table>";
		 
		 echo "<INPUT type='hidden' name='encabezado' value='0'>";
		 
		 echo "<br>";
		 echo "<br>";
		 echo "<table align='center'>";
		 echo "<tr>";
		 echo "<td><INPUT type='submit' value='GENERAR' style='width:100'></td>";
		 echo "<td><INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'></td>";
		 echo "</tr>";
		 echo "</table>";
		 
		 echo "</form>";
		
	}
	else{
		
		$historias = 0;
		$ccos = array();
		
		if( $ccoorigen == '1050' ){
			$origen = 'SF';
		}
		else{
			$origen = 'CM';
		}
		
		//Buscando centros de costos
		$sql = "SELECT	
		 			ccocod, cconom  
		 		FROM
		 			{$wbasedato}_000011
		 		WHERE
		 			ccocod like '%'
		 			and ccohos='on'
		 			and ccourg<>'on'";
		 			
		 $res = mysql_query( $sql, $conex );
		 
		for( $i = 0;$rows = mysql_fetch_array($res); $i++ ){
			$ccos[ $rows[0] ]['cod'] = $rows[0];
			$ccos[ $rows[0] ]['nom'] = $rows[1];
			
			$ccos[ $rows[0] ]['hisxdia'] = 0;			//historia clinicas por dia
			$ccos[ $rows[0] ]['histotales'] = 0; 		//historias clinicas totales, incluye repetidas del dia anterior
			$ccos[ $rows[0] ]['horas'] = 0;				//Tiempo total acumulado
			
			$ccos[ $rows[0] ]['hinik'] = 0;
			$ccos[ $rows[0] ]['hinid'] = 0;
			
		}

		echo "<form method='post'>";
		
		echo "<table align='center'>";
		 
		echo "<tr>";
		echo "<td class='encabezadotabla'>CCO Origen</td>";
		echo "<td class='fila1'>";
		echo getCCO( $ccoorigen );
		echo "</td>"; 
		echo "</tr>";
		 
		echo "<tr>";
		echo "<td class='encabezadotabla'>CCO Destino</td>";
		echo "<td class='fila1'>";
		 
		if( $ccodestino == '%' ){
		 	echo "% - Todos";
		}
		else{
			echo getCCO( $ccodestino );
		}
		 
		 echo "</td>"; 
		 echo "</tr>";
		 
		 echo "<tr>";
		 echo "<td class='encabezadotabla'>Fecha Inicial</td>";
		 echo "<td class='fila1'>"; 
		 echo $fecini;
		 echo"</td>";
		 echo "</tr>";
		 echo "<tr>";
		 echo "<td class='encabezadotabla'>Fecha Inicial</td>";
		 echo "<td class='fila1'>"; 
		 echo $fecfin;
		 echo"</td>";
		 echo "</tr>";
		 echo "</table>";
		
		 if( $origen == "SF" ){
		 	
			 $sql = "SELECT 
						d.hora_data, 
						MIN( kadhdi ) , 
						kadhis, 
						kading, 
						habcco, 
						habcod,
						a.kadfec as fecha 
					FROM 
						{$wbasedato}_000054 a,
						{$wbasedato}_000053 d, 
						{$wbasedato}_000067 b, 
						{$wbasedato}_000011 c
					WHERE 
						kadhis = habhis
						AND kading = habing
						AND kadhis = karhis
						AND kading = karing
						AND kargra = 'on'
						AND kadhdi > d.hora_data
						AND kadori = '$origen'
						AND kadfec BETWEEN '$fecini' AND '$fecfin'
						AND b.fecha_data = a.kadfec
						AND d.fecha_data = a.kadfec
						AND habcco = ccocod
						AND ccocod LIKE '$ccodestino'
						AND ccohos LIKE 'on'
						AND ccourg <> 'on'
						AND kadhdi <> '00:00:00'
					GROUP BY habcco, a.kadfec, kadhis";
		}
		else{
					
			$sql = "SELECT 
						e.hora_data, 
						MIN( kadhdi ) , 
						kadhis, 
						kading, 
						habcco, 
						habcod,
						a.kadfec as fecha 
					FROM 
						{$wbasedato}_000054 a,
						{$wbasedato}_000053 d, 
						{$wbasedato}_000067 b, 
						{$wbasedato}_000011 c,
						{$wbasedato}_000055 e,
						root_000025 f
					WHERE 
						kadhis = habhis
						AND kading = habing
						AND kadhis = karhis
						AND kading = karing
						AND kargra = 'on'
						AND kadhdi > e.hora_data
						AND kadori = '$origen'
						AND kadfec BETWEEN '$fecini' AND '$fecfin'
						AND b.fecha_data = a.kadfec
						AND d.fecha_data = a.kadfec
						AND habcco = ccocod
						AND ccocod LIKE '$ccodestino'
						AND ccohos LIKE 'on'
						AND ccourg <> 'on'
						AND kadhdi <> '00:00:00'
						AND kauhis = kadhis
						AND kauing = kading
						AND kaumen IN ( 'Articulo creado', 'Articulo actualizado' )
						AND SUBSTRING( e.seguridad FROM INSTR( e.seguridad, '-' ) +1 ) = f.empleado
						AND f.cc = '1051'
						AND e.fecha_data = a.kadfec
					GROUP BY habcco, a.kadfec, kadhis";
		}

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );

		if( $numrows > 0 ){
			
			echo "<br>";
			echo "<p align='center'><B>REPORTE DETALLADO</B></p>";
			
			$i = 0;
			
			$ccoactual = '';
			
			for( ;$rows = mysql_fetch_array( $res ); ){
				
				$class = "class='fila".(($i%2)+1)."'";
				
				if( $ccoactual != $rows['habcco'] ){
					
					if($i > 0){
						echo "</table>";
					}
				
					$ccoactual = $rows['habcco'];
					
					echo "<br><br>";
					
					echo "<table align='center'>";
					echo "<tr class='colorAzul5'>"; 
					echo "<td colspan='6'><b>";
					echo getCCO( $ccoactual ); 
					echo "</b></td>";
					echo "</tr>";
					
					echo "<tr align='center' class='encabezadotabla'>";
					echo "<td width='70'>Fecha</td>";
					echo "<td width='70'>Historia</td>";
					echo "<td width='70'>Ingreso</td>";
					echo "<td width='100'>Hora<br>grabacion<br>del Kardex</td>";
					echo "<td width='100'>Hora<br>dispensacion</td>";
					echo "<td width='100'>Tiempo</td>";
					echo "</tr>";
					
				}
				
				//buscando la diferencia de horas
				$tiempoDispensacion = strtotime( $rows[1] )-strtotime( $rows[0])+strtotime('1970-01-01 00:00:00') ; 
				
				@$ccos[ $rows['habcco'] ]['hisxdia']++;
				@$ccos[ $rows['habcco'] ]['histotales']++;
				@$ccos[ $rows['habcco'] ]['horas'] += $tiempoDispensacion-strtotime('1970-01-01 00:00:00');
				@$ccos[ $rows['habcco'] ]['hinik'] += strtotime( $rows[0] ) - strtotime( date("Y-m-d").' 00:00:00');
				@$ccos[ $rows['habcco'] ]['hinid'] += strtotime( $rows[1] ) - strtotime( date("Y-m-d").' 00:00:00');
				
	//			echo date("H:i:s",$tiempoDispensacion)."<br>";
				echo "<tr $class>";
				echo "<td>{$rows['fecha']}</td>";
				echo "<td align='right'>{$rows['kadhis']}</td>";
				echo "<td align='right'>{$rows['kading']}</td>";
				echo "<td align='center'>{$rows[0]}</td>";
				echo "<td align='center'>{$rows[1]}</td>";
				echo "<td align='center'>".@date("H:i:s",$tiempoDispensacion)."</td>";
				echo "</tr>";
				
				$historias++;
				
				$i++;
							
			}
			
			echo "</table>";
			
			echo "<br>";
			echo "<p align='center'><B>REPORTE RESUMIDO</B></p>";
			
			echo "<br>";
			echo "<table align='center'>";
			echo "<tr align='center' class='encabezadotabla'>";
			echo "<td>Servicio</td>";
			echo "<td>Promedio</td>";
			echo "<td>Total Historias</td>";
			echo "</tr>";
			
			$i = 0;
			$total = 0;
			$promedio = 0;
			
			foreach ( $ccos as $dato ){
				
				$class = "class='fila".(($i%2)+1)."'";
				
				if( $dato['histotales'] > 0 ){
					
					$prom = (($dato['horas'])/$dato['histotales'])+strtotime('1970-01-01 00:00:00');
					
					echo "<tr $class>";
					echo "<td align='left' width='350'>{$dato['cod']}-{$dato['nom']}</td>";
					echo "<td align='center' width='250'>".date( "H", $prom )." Horas, ".date( "i", $prom )." Minutos</td>";
					echo "<td align='center'>{$dato['histotales']}</td>";
					echo "</tr>";
					
					$total += $dato['histotales'];
					$promedio += $dato['horas'];
					
					$i++;
				}
				
			}
			
			echo "<tr align='center' class='encabezadotabla'>";
			echo "<td colspan='1'>Totales</td>";
			
			if( $total != 0 ){
				$prom = $promedio/$total+strtotime('1970-01-01 00:00:00');
				echo "<td align='center'>".date("H",$prom)." Horas, ".date("i",$prom)." Minutos</td>";
			}
			else{
				echo "<td align='center'>0</td>";
			}
			
			echo "<td>$total</td>";
			echo "</tr>";
			
			echo "</table>";
		}
		else{
			echo "<br>";
			echo "<p align='center'>NO SE GENERO NINGUN RESULTADO</p>";
		}
		
		
		//Mostrando los botones de retornar y Cerrar
		echo "<br>";
		echo "<br>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td><INPUT type='submit' value='RETORNAR' style='width:100'></td>";
		echo "<td><INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'></td>";
		echo "</tr>";
		echo "</table>";
		
		echo "</form>";
		
	}
}
?>
