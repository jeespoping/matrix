<html>
<head>
<title>REPORTE FACTURADO NETO POR ASESOR</title>
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
	$wbasedato=strtolower($wbasedato);
	
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

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

$wactualiz = "1.0 Octubre 05 de 2009";
encabezado("REPORTE FACTURADO NETO POR ASESOR", $wactualiz, "logo_".$wbasedato);

session_start();
if(!isset($_SESSION['user'])){
	echo "error";
}
else{

	if( (!isset($fecini) && !isset($fecfin) ) || ( $fecini > $fecfin ) ){
		
		echo "<form name='encabezado' action='RepFacturadoNetoXAsesor.php?wemp_pmla=06' method='post'>";
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
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); 
		
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
		//Luego se agrupan por asesor y factura  fenabo+fencmo+fencop+fendes
//		$sql = "SELECT
//					venusu as codigo,
//					descripcion as vennom, 
//					venffa, 
//					fenfac, 
//					(fenval+fenabo+fencmo+fencop) as valor,
//					fenvnc,
//					fenvnd
//				FROM
//					{$wbasedato}_000016 a,
//					{$wbasedato}_000018 b,
//					usuarios
//				WHERE
//					venusu like '$codasesor'
//					AND vennfa = fenfac
//					AND venffa = fenffa
//					AND venest = 'on'
//					AND codigo = venusu
//					AND fenfec BETWEEN '$fecini' AND '$fecfin'
//					AND fenest = 'on'
//				GROUP BY venusu, descripcion, fenfac
//				ORDER BY venusu";
					
		$sql = "SELECT
					venusu as codigo,
					descripcion as vennom, 
					venffa, 
					fenfac, 
					(fenval+fenabo+fencmo+fencop) as valor,
					fenvnc,
					fenvnd
				FROM
					{$wbasedato}_000016 a,
					{$wbasedato}_000018 b,
					{$wbasedato}_000024 c,
					{$wbasedato}_000003 d,
					usuarios
				WHERE
					venusu like '$codasesor'
					AND vennfa = fenfac
					AND venffa = fenffa
					AND venest = 'on'
					AND codigo = venusu
					AND fenfec BETWEEN '$fecini' AND '$fecfin'
					AND fenest = 'on'
					AND fencco = ccocod
					AND fencod = empcod
				GROUP BY venusu, descripcion, fenfac
				ORDER BY venusu";
					
		$res = mysql_query( $sql, $conex ) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
		
		$rows = mysql_fetch_array( $res );

		$i = 0;
		
		echo "<form action='RepFacturadoNetoXAsesor.php?wemp_pmla=06' method='post'>";
		
		echo "<br><p align='CENTER'><b>INFORME DETALLADO</b></p>";

		do
		{
			if( $rows ){
			
				$classfila = "class='fila".($i%2+1)."'";

				if( $asesor != trim($rows['codigo']) ){

					//Muestra el encabezado de la tabla por Asesor
					echo "<br><table align='CENTER'>";

					echo "<tr class='colorAzul5'><td colspan='8' style='font-size:16'><b>Asesor - {$rows['vennom']}</b></td></tr>
						<tr align='CENTER' class='encabezadotabla'>
						<td width='100'>FACTURA</td>
						<td width='150'>VALOR DE LA FACTURA</td>
						</tr>";	

					$asesor = trim($rows['codigo']);
				}
					
				echo "<tr $classfila>";
				echo "<td align='center'>{$rows['fenfac']}</td>";
				echo "<td align='right'>".number_format($rows['valor'] - $rows['fenvnc']+$rows['fenvnd'], 0, ".", ",")."</td>";
				echo "</tr>";
					
				//Calculando el total por Asesor
				$totVentasAsesor += $rows['valor'] - $rows['fenvnc']+$rows['fenvnd'];
				$totVentas += $rows['valor']-$rows['fenvnc']+$rows['fenvnd'];
					
				$filas = true;
					
				$rows = mysql_fetch_array( $res );

				$i++;
					
				if( $asesor != trim($rows['codigo']) ){

					//Muestra el total de ventas por Asesor
					echo "<tr class='encabezadotabla'>
						  <td>Total Asesor</td>
						  <td align='right'>".number_format( $totVentasAsesor, 0, ".", "," )."</td>
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
		
		if( $filas ){
			//reporte resumido
			
			$tot = 0;
			
//			$sql = "SELECT
//						venusu as codigo,
//						descripcion as vennom, 
//						SUM(fenval+fenabo+fencmo+fencop-fenvnc) as total
//					FROM
//						{$wbasedato}_000016 a,
//						{$wbasedato}_000018 b,
//						usuarios
//					WHERE
//						venusu like '$codasesor'
//						AND vennfa = fenfac
//						AND venffa = fenffa
//						AND venest = 'on'
//						AND codigo = venusu
//						AND fenfec BETWEEN '$fecini' AND '$fecfin'
//						AND fenest = 'on'
//					GROUP BY venusu, descripcion
//					ORDER BY venusu";
			
			$sql = "SELECT
						venusu as codigo,
						descripcion as vennom, 
						SUM(fenval+fenabo+fencmo+fencop-fenvnc) as total
					FROM
						{$wbasedato}_000016 a,
						{$wbasedato}_000018 b,
						{$wbasedato}_000024 c,
						{$wbasedato}_000003 d,
						usuarios
					WHERE
						venusu like '$codasesor'
						AND vennfa = fenfac
						AND venffa = fenffa
						AND venest = 'on'
						AND codigo = venusu
						AND fenfec BETWEEN '$fecini' AND '$fecfin'
						AND fenest = 'on'
						AND fencco = ccocod
						AND fencod = empcod
					GROUP BY venusu, descripcion
					ORDER BY venusu";
					
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta $sql -".mysql_error() );
			//Encabezado de la tabla resumen
			
			echo "<br><p align='CENTER'><b>INFORME RESUMIDO</b></p>";
			
			echo "<table align='CENTER'>";
			echo "<tr align='CENTER' class='encabezadotabla'>";
			echo "<td width='250'>Asesor</td>";
			echo "<td width='150'>Venta Total</td>";
			echo "</tr>";
			
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

				$classfila = "class='fila".($i%2+1)."'";
				
				echo "<tr $classfila>";
				echo "<td>{$rows['vennom']}</td>";
				echo "<td align='right'>".number_format((float)$rows['total'],0,".",",")."</td>";
				echo "</tr>";
				
				$tot += $rows['total'];
			}
			
			echo "<tr class='encabezadotabla'>";
			echo "<td>Total</td>";
			echo "<td align='right'>".number_format( (float)$tot ,0, "." , "," )."</td>";
			echo "</tr>";
			echo "</table>";
			
		}
		
		if( !$filas ){
			echo "<p align='CENTER'>No se genero Resultados</p>";
		}
		
		echo "<br><br><table align='center'>";
		echo "<tr><td><INPUT type='submit' value='Retornar' style='width:100'></td>
				<td><INPUT type='button' value='Cerrar' onClick='window.top.close();' style='width:100'></td>
				</tr>";
		echo "</table>";
		
		echo "</form>";

	}
}
?>


