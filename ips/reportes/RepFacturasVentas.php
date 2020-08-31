<html>

<head>
<title>MATRIX - [REPORTE DE FACTURAS POR FUENTE]</title>
</head>

<body>

<script type="text/javascript">
/******************************************************************************************************************************
 *Accion de consulta
 ******************************************************************************************************************************/
function consultar(){ 
	var wemp_pmla = document.forms.forma.wemp_pmla.value;

	var parametros = document.forms.forma.wfuente.value.split("-");
	
	var fuente = (parametros[0] && parametros[0] != '') ? parametros[0] : '';
	var notaCredito = (parametros[1] && parametros[1] != '') ? parametros[1] : '';
	var nombreFuente = (parametros[2] && parametros[2] != '') ? parametros[2] : '';
	var esRecibo = (parametros[3] && parametros[3] != '') ? parametros[3] : '';
	
	var fInicial = document.forms.forma.wfec_i.value;
	var fFinal = document.forms.forma.wfec_f.value;
 
 	if(esFechaMenorIgual(fInicial,fFinal)){
// 	 	alert('RepFacturasVentas.php?wemp_pmla='+wemp_pmla+'&waccion=a'+'&wfuente='+fuente+'&wfechaInicial='+fInicial+'&wfechaFinal='+fFinal+'&notaCredito='+notaCredito+'&nombreFuente='+nombreFuente+'&esRecibo='+esRecibo);
 		document.location.href = 'RepFacturasVentas.php?wemp_pmla='+wemp_pmla+'&waccion=a'+'&wfuente='+fuente+'&wfechaInicial='+fInicial+'&wfechaFinal='+fFinal+'&notaCredito='+notaCredito+'&nombreFuente='+nombreFuente+'&esRecibo='+esRecibo;	
 	} else {
 		alert("La fecha inicial debe ser menor a la fecha final de consulta.");
 	}	  
}

/******************************************************************************************************************************
 *Redirecciona a la pagina inicial
 ******************************************************************************************************************************/
function inicio(){
	document.location.href='RepFacturasVentas.php?wemp_pmla='+document.forms.forma.wemp_pmla.value;	
}
</script>

<?php
include_once("conex.php");
class fuenteDto{
	var $fuente = "";					//Fuente
	var $descripcion = "";				//Descripcion
	var $esNotaCredito = ""; 			//Nota credito
	var $esRecibo = ""; 				//Recibo
}

class dto{
	var $cdFactura = "";					//Factura
	var $cdVenta = "";						//Venta
	var $fechaFactura = 0; 					//Fecha factura
	var $fechaVenta = 0;					//Fecha venta
	var $fechaNotaCredito = 0;				//Fecha nota credito
	var $empresa = 0;						//empresa
	var $fuente = 0;						//Fuente
	var $tipoEmpresa = "";					//Tipo empresa
	var $esNotaCredito = "";				//La fuente corresponde a nota credito	
	var $cliente = "";						//Cliente
	var $concepto = "";						//Concepto
	var $valorTotalVenta = "";				//Valor total venta
	
	var $valorAntesDeIVA = 0;				//Valor antes de IVA
	var $valorIVA = 0;						//Valor de IVA
	var $valorDespuesDeIVA = 0;			 	//Valor despues de IVA
}
/******************************************************************************************************************************
 *Fuentes
 ******************************************************************************************************************************/
function consultarFuentes(){
	global $conex;
	global $wbasedato;
	
	$q= "SELECT 	
			carfue, cardes, Carncr, Carrec 
		FROM 
			".$wbasedato."_000040 
		WHERE 
			(Carest = 'on' AND Carncr = 'on' ) OR (Carest = 'on' AND Carfpa = 'on')";

//	echo $q;
	
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	$col = array();
	
	for ($i=1;$i<=$num1;$i++){
		$dto = new fuenteDto();
		
		$rs = mysql_fetch_array($res1);
		
		$dto->fuente		= $rs['carfue'];
		$dto->descripcion	= $rs['cardes'];
		$dto->esNotaCredito	= $rs['Carncr'];		//Si es nota credito, se consulta es por fecha de la nota y no por fecha de la fectura
		$dto->esRecibo		= $rs['Carrec'];		//Si es recibo, se consulta es por fecha de la nota y no por fecha de la fectura
		
		$col[] = $dto;
	}
	return $col;
}

/******************************************************************************************************************************
 *Consulta los indicadores dados los parametros
 ******************************************************************************************************************************/
function consultarFacturas($wfuente,$wfechaInicial,$wfechaFinal,$notaCredito,$esRecibo){
	global $wbasedato;
	global $conex;
	
	$coleccion = array();
	$empresa = "";
	
	if($notaCredito == "on"){
		$q = "SELECT
				Vennit, Rdefue, Rdefac, Fenfec, Vennum, Venfec, Rdevco, Rdefec, Rdevca, Fentip, Fenres, Rdecon, Venvto
			FROM
				{$wbasedato}_000016, {$wbasedato}_000018, (
					SELECT
						Fecha_data Rdefec, Rdefue, Rdefac, SUM(Rdevco) Rdevco, SUM(Rdevca) Rdevca, Rdecon
					FROM
						{$wbasedato}_000021
					WHERE
						Rdefue = '$wfuente'
						AND Rdeest = 'on'
					GROUP BY 
						Rdenum, Rdefue, Rdefac
				) a
			WHERE
				Venest = 'on'
				AND Fenest = 'on'
				AND a.Rdefac = Vennfa
				AND a.Rdefac = Fenfac
				AND Venano = Fenano
				AND Venmes = Fenmes
				AND Rdefec BETWEEN '$wfechaInicial' AND '$wfechaFinal'
			ORDER BY 
				Fenres, Fentip, Vennit, Fenfec, Rdefac";
	} else {
		if($esRecibo == "on"){
			$q = "SELECT
				Vennit, Fenffa, Fenfac, Fenfec, Vennum, Venfec, (Venvto-Venviv) Venvto, Venviv, Venvto total, Fentip, Fenres
			FROM
				{$wbasedato}_000016, {$wbasedato}_000018, (
					SELECT DISTINCT 
						Fecha_data Rdefec, Rdefue, Rdefac, SUM(Rdevco) Rdevco
					FROM
						{$wbasedato}_000021
					WHERE
						Rdefue = '$wfuente'
						AND Rdeest = 'on'
					GROUP BY 
						Rdefue, Rdefac
				) a
			WHERE
				Venest = 'on'
				AND Fenest = 'on'
				AND a.Rdefac = Vennfa
				AND a.Rdefac = Fenfac
				AND Venano = Fenano
				AND Venmes = Fenmes
				AND Rdefec BETWEEN '$wfechaInicial' AND '$wfechaFinal'
			ORDER BY 
				Fenres, Fentip, Fenfec, Vennit, Rdefac";
		} else {
			$q = "SELECT 
					 Vennit, Fenffa, Fenfac, Fenfec, Vennum, Venfec, (Venvto-Venviv) Venvto, Venviv, Venvto total, Fentip, Fenres
				FROM 
					{$wbasedato}_000016, {$wbasedato}_000018
				WHERE 
					Venest = 'on'
					AND Fenest = 'on'
					AND Fenffa = '$wfuente'
					AND Fenfec = Venfec
					AND Vennfa = Fenfac
					AND Venano = Fenano
					AND Venmes = Fenmes
					AND Fenfec BETWEEN '$wfechaInicial' AND '$wfechaFinal'
				ORDER BY 
					Fenres, Fentip, Vennit, Fenfec, Fenfac";
		}
	}
	
//	echo $q;
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$rs = mysql_fetch_array($res);
	
	while($rs){
		$dto = new dto();
	
		//Consultados		 
		$dto->cdVenta			= $rs['Vennum'];
		$dto->fechaFactura		= $rs['Fenfec'];
		$dto->fechaVenta		= $rs['Venfec'];
		$dto->tipoEmpresa 		= $rs['Fentip'];
		$dto->fuente			= $wfuente;
		
		//Cliente
		if($rs['Vennit'] == '9999' || $rs['Vennit'] == '99999'){
			$cliente = "PARTICULAR";		
		} else {
			$q2 = "SELECT Clinom empresa FROM {$wbasedato}_000041 WHERE Clidoc = '".$rs['Vennit']."' GROUP BY Clidoc";
			
			$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$rs2 = mysql_fetch_array($res2);
			
			if($rs2){
				$cliente = isset($rs2['empresa']) ? $rs2['empresa'] : '';
			} else {
				$cliente = '';
			}
		}
		
		//Empresa
		$q2 = "SELECT Empnom empresa FROM {$wbasedato}_000024 WHERE Empcod = '".$rs['Fenres']."' GROUP BY Empcod";
			
		$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
		$rs2 = mysql_fetch_array($res2);
			
		if($rs2){
			$empresa = isset($rs2['empresa']) ? $rs2['empresa'] : '';
		} else {
			$empresa = '';
		}
		
		$dto->cliente				= $rs['Vennit']."-".$cliente;
		$dto->empresa				= $empresa;
		
		if($notaCredito == "on"){
			$dto->cdFactura 		= $rs['Rdefac'];
			$dto->esNotaCredito 	= true;
			$dto->fechaNotaCredito 	= $rs['Rdefec'];
			$dto->valorAntesDeIVA 	= $rs['Rdevco'];
			$dto->valorIVA			= 0;
			$dto->valorDespuesDeIVA = $rs['Rdevco'];
			$dto->concepto			= $rs['Rdecon'];
			$dto->valorTotalVenta 	= $rs['Venvto'];
		} else {
			$dto->cdFactura 		= $rs['Fenfac'];
			$dto->esNotaCredito = false;
			$dto->valorAntesDeIVA 	= $rs['Venvto'];
			$dto->valorIVA			= $rs['Venviv'];
			$dto->valorDespuesDeIVA = $rs['total'];
		}
		
		$rs = mysql_fetch_array($res);
		
		$coleccion[] = $dto;
	}
	return $coleccion;
}

/**
 * Inicio de la aplicacion
 */
include_once("root/comun.php");

$wactualiz = " 1.0 10-Feb-10";

if (!isset($user))
	if(!isset($_SESSION['user']))
		session_register("user");

if(!isset($_SESSION['user']))
	terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
else{
	$conex = obtenerConexionBD("matrix");
	
	$wfecha=date("Y-m-d");

	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "farpmla");

	//Encabezado 
	encabezado("REPORTE DE FACTURAS POR RANGO DE FECHAS Y FUENTE",$wactualiz,"clinica");
	
	echo "<form name='forma' action='RepFacturasVentas.php' method=post>";

	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	
	if(!isset($waccion)){
		$waccion = "";
	}
	
	switch ($waccion){
		case 'a': //Consulta de los indicadores
			$totalValorAntesDeIVA	= 0;
			$totalValorIVA			= 0;
			$totalValorDespuesDeIVA	= 0;
			$totalValorVentaTotal	= 0;
				
			$class="fila2";
			
			echo '<span class="subtituloPagina2">';
			echo "Reporte de facturas por fuente.  Desde $wfechaInicial hasta $wfechaFinal";
			echo "<br>";
			echo "<b>Fuente:</b>  $wfuente - $nombreFuente";
			echo "</span>";
			
			echo "<br>";
			echo "<br>";
			
			echo "<center><br><input type='button' value='Regresar' onClick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></center><br>";
			
			echo "<table align='center' border=0>";
				
			$fechaTemp = "";
			$empresa = "";
			$mostrarSub = false;
			$spanTotal = "4";
			
			if(isset($wfuente) && isset($wfechaInicial) && isset($wfechaFinal) && isset($notaCredito) && isset($esRecibo)){
				$col = consultarFacturas($wfuente,$wfechaInicial,$wfechaFinal,$notaCredito,$esRecibo);
				
				if(count($col) > 0){
				
					foreach ($col as $elemento){
						if($empresa != $elemento->empresa){
							
							echo "<tr>";
								
							echo "<td colspan=3>";
							echo "<br>";
							echo "<span class='subtituloPagina2'>";
							echo "<b>Empresa:</b> $elemento->empresa - <b>Tipo:</b> $elemento->tipoEmpresa";							
							echo "</span>";
							echo "<br>";
							echo "<br>";
							echo "</td>";
							
							echo "</tr>";
							
							echo "<tr class='encabezadoTabla' align=center>";
								
							echo "<td>Cliente</td>";
							echo "<td>Factura</td>";
							echo "<td>Fecha</td>";
							echo "<td>Venta</td>";
							if($notaCredito == "on"){
								echo "<td>Concepto</td>";
								echo "<td>Valor concepto</td>";
								echo "<td>Valor cancelado</td>";
								echo "<td>Valor venta</td>";
							} else {
								echo "<td>Valor antes de IVA</td>";
								echo "<td>Valor IVA</td>";
								echo "<td>Valor despues de IVA</td>";
							}
								
							echo "</tr>";
							$mostrarSub = true;
						} else {
							$mostrarSub = false;
						}
						
						$empresa = $elemento->empresa;
						
						if($class=="fila1"){
							$class="fila2";
						} else {
							$class="fila1";
						}
						
						echo "<tr class='$class'>";
						
						echo "<td>$elemento->cliente</td>";
						echo "<td align='center'>$elemento->cdFactura</td>";
						
						if($elemento->esNotaCredito){
							echo "<td align='center'>$elemento->fechaNotaCredito</td>";
							echo "<td align='center'>$elemento->cdVenta</td>";
							echo "<td align='center'>$elemento->concepto</td>";
							echo "<td align='right'>".number_format($elemento->valorAntesDeIVA,0,'.',',')."</td>";
							echo "<td align='right'>".number_format($elemento->valorAntesDeIVA,0,'.',',')."</td>";
							echo "<td align='right'>".number_format($elemento->valorTotalVenta,0,'.',',')."</td>";
							$spanTotal = "5";
						} else {
							echo "<td align='center'>$elemento->fechaFactura</td>";
							echo "<td align='center'>$elemento->cdVenta</td>";
							echo "<td align='right'>".number_format($elemento->valorAntesDeIVA,0,'.',',')."</td>";
							echo "<td align='right'>".number_format($elemento->valorIVA,0,'.',',')."</td>";
							echo "<td align='right'>".number_format($elemento->valorDespuesDeIVA,0,'.',',')."</td>";
						}
						
						echo "</tr>";

						$totalValorAntesDeIVA	+= $elemento->valorAntesDeIVA;
						$totalValorIVA			+= $elemento->valorIVA;
						$totalValorDespuesDeIVA	+= $elemento->valorDespuesDeIVA;
						$totalValorVentaTotal	+= $elemento->valorTotalVenta;
					}
					
					//Totalizado
					echo "<tr>";

					echo "<td colspan=2>";
					echo "<br>";
					echo '<span class="subtituloPagina2">';
					echo "<b>Totales</b>";
					echo "</span>";
					echo "<br>";
					echo "<br>";
					echo "</td>";
						
					echo "</tr>";
					
					echo "<tr class=encabezadoTabla>";

					echo "<td colspan=$spanTotal>".count($col)." registro(s)</td>";
					echo "<td align='right'>".number_format($totalValorAntesDeIVA,0,'.',',')."</td>";
					
					if(!$elemento->esNotaCredito){
						echo "<td align='right'>".number_format($totalValorIVA,0,'.',',')."</td>";
						echo "<td align='right'>".number_format($totalValorDespuesDeIVA,0,'.',',')."</td>";
					} else{
						echo "<td align='right'>".number_format($totalValorDespuesDeIVA,0,'.',',')."</td>";
						echo "<td align='right'>".number_format($totalValorVentaTotal,0,'.',',')."</td>";
					}

					echo "</tr>";
					
					echo "</table>";
					echo "<center><br><input type='button' value='Regresar' onClick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></center>";
				} else {
					echo "</table>";
					mensajeEmergente("No se encontraron recibos en el rango especificado");
					funcionJavascript("inicio();");
				}
			} else { //si no llegan los parametros del reporte
				echo "</table>";
				mensajeEmergente("Verifique los parámetros de consulta: fuente, fecha inicial y fecha final.");				
			}
			
			break; //Fin consulta reporte
		default:  //Filtro
			echo "<table align='center' border=0>";
				
			echo '<span class="subtituloPagina2">';
			echo "Par&aacute;metros de consulta";
			echo "</span>";
			echo "<br>";
			echo "<br>";
				
			$col = consultarFuentes();
			
			//Centros de costos hospitalarios
			echo "<tr>";
			
			echo "<td class='fila1'>Fuente</td>";
			echo "<td class='fila2'>";
			
			echo "<select name='wfuente' class='textoNormal'>";
			
			foreach ($col as $elemento){
//				$elemento = new fuenteDto();
				echo "<option value='$elemento->fuente-$elemento->esNotaCredito-$elemento->descripcion-$elemento->esRecibo'>$elemento->fuente - $elemento->descripcion</option>";	
			}
			
			echo "</select>";
			echo "</td>";
			
			echo"</tr>";
			
			//Fecha inicial de consulta
			echo "<tr>";
			echo "<td class='fila1'>Fecha inicial</td>";
			echo "<td class='fila2'>";			
			campoFecha("wfec_i");
			echo "</td>";
			echo "</tr>";
			
			//Fecha final de consulta
			echo "<tr>";
			echo "<td class='fila1'>Fecha final</td>";
			echo "<td class='fila2'>";
			campoFecha("wfec_f");
			echo "</td>";
			echo "</tr>";

			echo "<tr><td align=center colspan=2><br><input type='button' value='Consultar' onClick='javascript:consultar();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr></center>";
			
			echo "</table>";
			
			break;
	} //Fin switch
}
liberarConexionBD($conex);
?>