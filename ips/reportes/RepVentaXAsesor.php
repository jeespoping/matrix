<html>
<head>
<title>REPORTE DE VENTAS POR ASESOR</title>
<?php
include_once("conex.php");
/*
 * Nombre del programa:  	RepVentaXAssesor.php
 * Opcion:				 	Reporte de Ventas por Asesor
 * Fecha de creacion:		2009-09-02
 * Descripción:				Generea un reporte que muestra las ventas para lentes, monturas y otros de 
 * 							cada Asesor en un rango de fechas
 * 
 * Características
 * 
 * Antes de generar el reporte se puede elegir un rango de fechas e igualmente escoger el asesor que se desee.
 * Si el usuario es un administrador podra visualizar cualquier Asesor, de lo contrario, solo se podrá visualizar
 * el usuario registrado  
 * 
 * Nota: Los lentes a que se refiere el reporte son oftalmicos y de EPS, con otros se hace referencia a 
 * 		 accesorios, estuches, liquidos y lentes de contactos
 */

/*********************************************************************************************************
 * Funciones
 ********************************************************************************************************/

/**
 * Busca los conceptos por los que puede haber rebaja en una factura, estos son SERVICIOS, COPAGOS, SUBSIDOS
 * 
 * @param $ffa				Fuente de la factura
 * @param $fac				Numero de la factura
 * @return unknown_type
 */
function buscarOtrosConceptos( $fac, $ffa ){
	
	global $conex;
	global $wbasedato;
	
	$descuentos = array();
	$descuentos['servicios'] = 0;
	$descuentos['copagos'] = 0;
	$descuentos['subsidios'] = 0;
	
	//Buscando los conceptos de subsidios, copagos y servicios 
	$sql = "SELECT 
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM ".$wbasedato."_000065 WHERE Fdefue = '$ffa' AND Fdedoc = '$fac' AND Fdecon = 'SE' ) valor_servicios,
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM ".$wbasedato."_000065 WHERE Fdefue = '$ffa' AND Fdedoc = '$fac' AND Fdecon = 'COP' ) valor_copagos,
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM ".$wbasedato."_000065 WHERE Fdefue = '$ffa' AND Fdedoc = '$fac' AND Fdecon = 'SUB' ) valor_subsidios				 
		";
	
	$res = mysql_query( $sql, $conex );
	
	while( $ser = mysql_fetch_array( $res )){
		$descuentos['servicios'] = $ser[0];
		$descuentos['copagos'] = $ser[1];
		$descuentos['subsidios'] = $ser[2];
	}
	
	return $descuentos;
	
}

/**
 * Busca si un valor existe en la lista
 * @param $lista
 * @param $valor
 * @return unknown_type
 */

function enLista( $lista, $valor ){
	
	for( $i = 0; $i < count($lista); $i++ ){
		if( $lista[$i][0] == $valor ){
			return $i;
		}
	}
	
	return -1;
}

/********************************************************************************************************/

/*********************************************************************************************************
 * Comienzo del programa
 ********************************************************************************************************/
include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = $institucion->baseDeDatos;
$wentidad = $institucion->nombre;

$wactualiz = "1.0 Septiembre 22 de 2009";
encabezado("REPORTE DE VENTAS POR ASESOR", $wactualiz, "logo_".$wbasedato);

session_start();
if(!isset($_SESSION['user'])){
	echo "error";
}
else{

	if( (!isset($fecini) && !isset($fecfin) ) || ( $fecini > $fecfin ) ){
		
		echo "<form name='encabezado' action='RepVentaXAsesor.php?wemp_pmla=06' method='post'>";
		echo "<br><br>";
		echo "<table align='center'>";
		echo "<tr class='encabezadotabla' align='center'>
			  	<td width='180'>Fecha Inicial</td>
			  	<td width='180'>Fecha Final</td>
			  </tr>
			  <tr align='center' class='fila1'>
			  	<td align='center'>";
		campoFechaDefecto( 'fecini', date( "Y-m-d") );
		echo "</td><td align='center'>";
		campoFechaDefecto( 'fecfin', date( "Y-m-d") );
		echo "</td></tr>";
		echo "</table>";
		
		echo "<br><br><table align='center'>";
		echo "<tr class='encabezadotabla'><td align='CENTER'>Asesores";
		echo "</td></tr>";
		
		echo "<tr><td>";
		
		//Buscando si el usuario es Administrador
		$adm = false;
		
		$sql =  "SELECT
					cjeadm 
				 FROM
				 	{$wbasedato}_000030
				 WHERE
				 	cjeusu='$key'"; 
		
		$res = mysql_query( $sql, $conex ); 
		
		if( $rows = mysql_fetch_array( $res ) ){
			if( $rows[0] == 'on' ){
				$adm = true;
			}
			else{
				$adm = false;
			}
		}
		
		//Si es administrador puede ver todos los Asesores
		//de lo contrario solo puede ver el de si mismo
		if( $adm ){
			
			echo "<select name='codasesor'><option value='%'>Todos</option>";
			
			$sql = "SELECT
						cjeusu, codigo as cod, descripcion as nom 
					FROM
						{$wbasedato}_000030, usuarios
					WHERE
						cjeusu = codigo
						AND activo='A'
						AND cjeest='on'
						AND cjefac='on'
						AND cjeadm!='on'
						AND empresa='$wemp_pmla'";
		
			$res = mysql_query( $sql, $conex );
			
			for( ; $rows = mysql_fetch_array( $res ); ){
				echo "<option value='{$rows['cod']}-{$rows['nom']}'>{$rows['cod']}-{$rows['nom']}</option>";
			}
		}
		else{
			$usuario = new Usuario();
			$usuario = consultarUsuario( $conex, $key );
			
			echo "<select name='codasesor'>";
			echo "<option value='{$usuario->codigo}-{$usuario->descripcion}'>{$usuario->codigo}-{$usuario->descripcion}</option>";
		}
		
		echo "</select>";
		
		echo "</td></tr>";
		echo "</table>";

		echo "<br><br><table align='center'>";
		echo "<tr><td><INPUT type='submit' value='Generar' style='width:100'></td>";
		echo "<td><INPUT type='button' value='Cerrar' onClick='window.top.close();' style='width:100'></td></tr>";
		echo "</table>";
		
		echo "</form>";

	}
	else{
		
		//************INICIO LOG::: 
		$debug = true;	
		if($debug){
			$fechaLog = date("Y-m-d");
			$horaLog = date("H:i:s");
				
		    	//Creacion de un archivo plano para tomar una imagen de la informacion de las camas en ese momento
		    	$nombreArchivo = "LogRepVentaXAsesor.log";		
			    	
		    	//Apuntador en modo de adicion si no existe el archivo se intenta crear...
		    	$archivo = fopen($nombreArchivo, "w"); 
		    	if(!$archivo){
		    		$archivo = fopen($nombreArchivo, "w");
		    	}
		    	
		    	$contenidoLog = "*CONSULTA DE VENTAS POR ASESOR ($fechaLog - $horaLog)*->  Usuario: $key\r\n";
		}
		//************FIN LOG:::
		
		if( $codasesor == "%" ){
			$exp[1] = 'Todos';
		}
		else{
			$exp = explode("-", $codasesor );
			$codasesor = "%$exp[0]%";
		}
		
		//Encabezado del informe
		echo "<table align='center'><tr>
			  	<td width='100' class='encabezadotabla'>Asesor:</td>
			  	<td width='200' class='fila1'>$exp[1]</td>
			  </tr><tr>
			  	<td width='100' class='encabezadotabla'>Desde:</td>
			  	<td width='100' class='fila1'>$fecini</td>
			  </tr>
			  <tr>
			  	<td width='100' class='encabezadotabla'>Hasta:</td>
			  	<td width='100' class='fila1'>$fecfin</td>
			  </tr>
			  <tr></table>";
		
		$asesor = '';
		$filas = false;
		$totLentesAsesor = 0;
		$totMonturasAsesor = 0;
		$totOtrosAsesor = 0;
		$totVentasAsesor = 0;
		$totSubsidiosAsesor = 0;
		$totCopagosAsesor = 0;
		$totServiciosAsesor = 0;
		
		$totLentes = 0;
		$totMonturas = 0;
		$totVentas = 0;
		$totSubsidios = 0;
		$totCopagos = 0;
		$totServicios = 0;
		$totOtros = 0;
		
		$listFac = array();
		$marca = '';

		//Buscando la suma total de los ventas por lentes, por monturas
		//y por otros por cada asesor.
		//La primera parte de la consulta busca la suma total de los lentes
		//La segunda parte de la consulta busca la suma total de las monturas
		//La tercera busca los otros conceptos de acuerdo a la factura
		//Luego se agrupan por asesor y factura
		$sql= "SELECT 
				codigo, 
				vennom, 
				SUM( lentes ) as Lentes, 
				SUM( montura ) as Monturas,
				SUM(Otros) as Otros, 
				fenfac,
				fenffa
			FROM(
				SELECT codigo, vennom, lentes, montura, 0 as otros, fenfac, fenffa
				FROM (
					SELECT 
						IFNULL(SUBSTRING_INDEX( ordvel, '-', 1 ), SUBSTRING_INDEX( ordvem, '-', 1 ) ) AS codigo, 
						IFNULL(SUBSTRING( ordvel FROM INSTR( ordvel, '-' ) + 1 ), SUBSTRING( ordvem FROM INSTR( ordvem, '-' ) + 1 ) ) AS vennom, 
						SUM( fdevco - fdevde ) AS lentes, 
						0 AS montura, 
						fenfac, fenffa
					FROM 
						{$wbasedato}_000016 a, 
						{$wbasedato}_000018 b, 
						{$wbasedato}_000065 c, 
						{$wbasedato}_000133 d
					WHERE 
						a.fecha_data BETWEEN '$fecini' AND '$fecfin' 
						AND venusu = SUBSTRING_INDEX( ordvem, '-', 1 ) 
						AND venusu LIKE '$codasesor'
						AND vennfa = ordfac
						AND vennfa = fenfac 
						AND vennfa = fdedoc
						AND fenffa = fdefue 
						AND fdecon IN ('LO', 'LE')
					GROUP BY 1 , fenfac
					) AS t1
				UNION 
				SELECT codigo, vennom, lentes, montura, 0 as otros, fenfac, fenffa
				FROM (		
					SELECT 
						IFNULL(SUBSTRING_INDEX( ordvem, '-', 1 ),SUBSTRING_INDEX( ordvel, '-', 1 )) AS codigo, 
						IFNULL(SUBSTRING( ordvem FROM INSTR( ordvem, '-' ) + 1 ),SUBSTRING( ordvel FROM INSTR( ordvel, '-' ) + 1 ) ) AS vennom, 
						0 AS lentes, 
						SUM( fdevco - fdevde ) AS montura, 
						fenfac,
						fenffa
					FROM 
						{$wbasedato}_000016 a, 
						{$wbasedato}_000018 b, 
						{$wbasedato}_000065 c, 
						{$wbasedato}_000133 d
					WHERE 
						a.fecha_data BETWEEN '$fecini' AND '$fecfin' 
						AND venusu = SUBSTRING_INDEX( ordvem, '-', 1 )
						AND venusu LIKE '$codasesor'
						AND vennfa = ordfac 
						AND vennfa = fenfac 
						AND vennfa = fdedoc 
						AND fenffa = fdefue
						AND fdecon IN ('MT')
					GROUP BY 1 , fenfac
					) AS t2
				UNION
				SELECT venusu as codigo, vennom, lentes, montura, otros, fenfac, fenffa
				FROM (
					SELECT
						venusu, 
						descripcion as vennom,
						fenfac, 
						0 as lentes, 
						0 as montura, 
						SUM( fdevco - fdevde ) as otros,
						fenffa
					FROM
						{$wbasedato}_000016 a, 
						{$wbasedato}_000018 b, 
						{$wbasedato}_000065 c,
						usuarios
					WHERE
						codigo=venusu
						AND venffa=fenffa
						AND venusu LIKE '$codasesor'
						AND vennfa=fenfac
						AND fenfac=fdedoc
						AND fdefue=fenffa
						AND fdecon IN ('LC','ES','LQ','AC')
						and a.fecha_data BETWEEN '$fecini' AND '$fecfin'
					GROUP BY
						venusu, fenfac
					) as t3
				) AS datos
			WHERE 
				codigo LIKE '$codasesor'
				AND codigo <> ''
			GROUP BY 1 ,2, fenfac";

		$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consultando ventas por asesor...*-> \r\n";
		$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consulta - $sql...*-> \r\n";
		
		$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
		
		$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consulta realizada con exito...*-> \r\n";
		
		$rows = mysql_fetch_array( $res );

		$i = 0;
		
		echo "<form action='RepVentaXAsesor.php?wemp_pmla=06' method='post'>";

		$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Mostrando los datos en pantalla...*-> \r\n";
		
		do
		{
			if( $rows ){
			
				$classfila = "class='fila".($i%2+1)."'";

				if( $asesor != trim($rows['codigo']) ){
					
					$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Inicio de asesor...*-> \r\n";

					//Muestra el encabezado de la tabla por Asesor
					echo "<br><br><table align='CENTER'>";

					echo "<tr class='colorAzul5'><td colspan='8' style='font-size:16'><b>Asesor - {$rows['vennom']}</b></td></tr>
						<tr align='CENTER' class='encabezadotabla'>
						<td width='100'>FACTURA</td>
						<td width='150'>LENTE</td>
						<td width='150'>MONTURA</td>
						<td width='150'>OTROS</td>
						<td width='150'>SERVICIOS</td>
						<td width='150'>COPAGOS</td>
						<td width='150'>SUBSIDIOS</td>
						<td width='150'>TOTAL</td>
						</tr>";	

					$asesor = trim($rows['codigo']);
				}
					
				if( enLista( $listFac, $rows['fenfac'] ) == -1 ){
					
					$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Buscando otros conceptos de la factura: {$rows['fenfac']}...*-> \r\n";
					
					$des = buscarOtrosConceptos( $rows['fenfac'], $rows['fenffa'] );
					$listFac[count($listFac)][0] = trim($rows['fenfac']);
					$marca = '';;
					
					$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consulta de otros conceptos de la factura: {$rows['fenfac']} finalizada...*-> \r\n";
				}
				else{
					$marca = '* ';
					$des['subsidios'] = 0;
					$des['copagos'] = 0;	
					$des['servicios'] = 0;
				}	
				//Cuerpo de la tabal por Asesor
				echo "<tr $classfila>";
				echo "<td align='center'>$marca{$rows['fenfac']}</td>";
				echo "<td align='right'>".number_format($rows['Lentes'], 0, ".", ",")."</td>";
				echo "<td align='right'>".number_format($rows['Monturas'], 0, ".", ",")."</td>";
				echo "<td align='right'>".number_format($rows['Otros'], 0, ".", ",")."</td>";
				echo "<td align='right'>".number_format($des['servicios'], 0, ".", ",")."</td>";
				echo "<td align='right'>".number_format($des['copagos'], 0, ".", ",")."</td>";
				echo "<td align='right'>".number_format($des['subsidios'], 0, ".", ",")."</td>";
				echo "<td align='right'>".number_format($rows['Lentes']+$rows['Monturas']+$rows['Otros']+$des['servicios']+$des['copagos']+$des['subsidios'], 0, ".", ",")."</td>";
				echo "</tr>";
					
				//Calculando el total por Asesor
				$totLentesAsesor += $rows['Lentes'];
				$totMonturasAsesor += $rows['Monturas'];
				$totOtrosAsesor += $rows['Otros'];
				$totSubsidiosAsesor += $des['subsidios'];
				$totCopagosAsesor += $des['copagos'];	
				$totServiciosAsesor += $des['servicios'];
				$totVentasAsesor += $rows['Lentes']+$rows['Monturas']+$rows['Otros']+$des['servicios']+$des['copagos']+$des['subsidios'];
					
				$filas = true;
					
				$rows = mysql_fetch_array( $res );

				$i++;
				
				$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Fila terminada...*-> \r\n";
					
				if( $asesor != trim($rows['codigo']) ){

					$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Fin de Asesor...*-> \r\n";
					//Muestra el total de ventas por Asesor
					echo "<tr class='encabezadotabla'>
						  <td>Total Asesor</td>
						  <td align='right'>".number_format( $totLentesAsesor, 0, ".", "," )."</td>
						  <td align='right'>".number_format($totMonturasAsesor, 0, ".", "," )."</td>
						  <td align='right'>".number_format($totOtrosAsesor, 0, ".", "," )."</td>
						  <td align='right'>".number_format($totServiciosAsesor, 0, ".", "," )."</td>
						  <td align='right'>".number_format($totCopagosAsesor, 0, ".", "," )."</td>
						  <td align='right'>".number_format($totSubsidiosAsesor, 0, ".", "," )."</td>
						  <td align='right'>".number_format($totVentasAsesor, 0, ".", "," )."</td>
						  </tr></table>";

					$totLentesAsesor = 0;
					$totMonturasAsesor = 0;
					$totVentasAsesor = 0;
					$totOtrosAsesor = 0;
					$totSubsidiosAsesor = 0;
					$totCopagosAsesor = 0;
					$totServiciosAsesor = 0;
				}
			}
			
		} while( $rows );
		
		if( !$filas ){
			echo "<p align='CENTER'>No se genero Resultados</p>";
		}
		
		echo "<p align='center'><b>Nota</b>: Los calculos de servicios, copagos y subsidos aparecen calculados solo para la primera aparición de la factura.</p>";
		echo "<p align='center'>Las facturas con \"*\" son repeticiones de la misma factura.</p>";
		echo "<br><br><table align='center'>";
		echo "<tr><td><INPUT type='submit' value='Retornar' style='width:100'></td>
				<td><INPUT type='button' value='Cerrar' onClick='window.top.close();' style='width:100'></td>
				</tr>";
		echo "</table>";
		
		echo "</form>";
		
		$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Fin de ejecución...*->\r\n";
		
		//Msanchez:**************GRABA LOG**************
		if($debug){
			if($archivo){
				// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
				if (is_writable($nombreArchivo)) {
					// Escribir $contenido a nuestro arcivo abierto.
					fwrite($archivo, $contenidoLog);
					fclose($archivo);
				}
			}
		}
		//Msanchez::***************FIN GRABA LOG*************
		

	}
}
?>
