<html>
<head>
<title>MATRIX - [MIGRACION FARMASTORE]</title>

<script type="text/javascript">
	function inicio(){ 
		document.location.href='FarmastoreMigracion1.php?wemp_pmla='+document.forms.forma.wemp_pmla.value; 
	}
	
	 function consultar(){ 
	 	var formulario = document.forms.forma;

		document.location.href='FarmastoreMigracion1.php?wemp_pmla='+formulario.wemp_pmla.value+'&waccion=a&wanio='+formulario.wanio.value+'&wmes='+formulario.wmes.value
		+'&wporcincremento='+formulario.wporcincremento.value+'&wbdorigen='+document.getElementById('wbdorigen').value+'&wbddestino='+document.getElementById('wbddestino').value;
	}
</script>

</head>

<body>
<?php
include_once("conex.php");
/*BS'D
 * Farmastore migracion de datos 
 * Autor: Mauricio Sánchez Castaño.
 */
include_once("root/comun.php");

$wactualiz = " 1.0 15-Oct-09";
$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

$usuarioValidado = true;

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
encabezado("Farmastore migracion",$wactualiz,"clinica");

//Variables globales
$nitFarmastore = "900016094-7";
$codigoConcepto = "900";
$usuarioFarmastore = "farpmla";

$fechaData = date("Y-m-d");
$horaData = date("H:i:s");

class Registro1{
	var $cco = "";
	var $ccoEquivalente = "";
	var $cantArts = "";	
}

/**
 * Funciones 
 */
function obtenerNuevoCentroCostos($ccoAnterior){
	$ccoNuevo = "";
	
	switch ($ccoAnterior) {
		case '3051':
			$ccoNuevo = '3062';
			break;
		case '3052':
			$ccoNuevo = '3063';
			break;	
		case '3054':
			$ccoNuevo = '3064';
			break;
		case '3053':
			$ccoNuevo = '3065';
			break;
		case '3055':
			$ccoNuevo = '3066';
			break;
		case '1061':
			$ccoNuevo = '3067';
			break;
		case '1062':
			$ccoNuevo = '3067';
			break;
		case '2005':
			$ccoNuevo = '1063';
			break;
		case '6060':
			$ccoNuevo = '1064';
			break;
		case '8080':
			$ccoNuevo = '1065';
			break;
		case '5050':
			$ccoNuevo = '1066';
			break;
		case '1060':
			$ccoNuevo = '1067';
			break;
		default :
		break;
	}	
	return $ccoNuevo;
}


function generarDetalleEncabezado($conexion, $anio, $mes, $incremento,$bdorigen, $bddestino){
	global $nitFarmastore;
	global $codigoConcepto;
	global $usuarioFarmastore;
	
	global $fechaData;
	global $horaData;

	$limiteOrdenesCompra = 2000;
	$contOrdenes = 1;

	$cco = "";
	$cantArts = "";
	$consecutivo = "";
	
	$coleccionCco = array();
	/***************************************************************************************************************
	 * CONSULTO LOS CENTROS DE COSTOS QUE SE ENCUENTRAN CON EQUIVALENCIA
	 ***************************************************************************************************************/
	$qCco = "SELECT 
			Karcco, COUNT(*) Cuenta
		FROM 
			{$bdorigen}_000007
		WHERE
			Karexi > 0
		GROUP BY 
			Karcco
		ORDER BY 
			2 DESC";
	
//	echo $qCco."<br>";
	
	$resCco = mysql_query($qCco, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qCco . " - " . mysql_error());
	$numCco = mysql_num_rows($resCco);
	
	if ($numCco > 0)
	{
		$contCco = 0;
		while ($contCco < $numCco){
			
			$infoCco = mysql_fetch_array($resCco);
			
			$registro = new Registro1();
			
			$registro->cco = $infoCco['Karcco'];
			if($bdorigen == $bddestino){
				$registro->ccoEquivalente = $infoCco['Karcco'];
			} else {
				$registro->ccoEquivalente = obtenerNuevoCentroCostos($infoCco['Karcco']);
			}
			$registro->cantArts = $infoCco['Cuenta'];
			
			if($registro->ccoEquivalente != ''){
				$coleccionCco[] = $registro;
			}
			
			$contCco++;
		}
		
	}
	/*************************************
	 * INCREMENTAR EL CONSECUTIVO
	 *************************************/
     $qInc = "UPDATE {$bddestino}_000008 SET
				Concon = Concon + 1
			WHERE 
				Concod = '900';";		

//	echo $qInc."<br>";
	$resInc = mysql_query($qInc, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qInc . " - " . mysql_error());
	/*************************************
	 * FIN INCREMENTAR EL CONSECUTIVO
	 *************************************/
		
	/***************************************************************************************************************
	 * FIN CONSULTA LOS CENTROS DE COSTOS QUE SE ENCUENTRAN CON EQUIVALENCIA
	 ***************************************************************************************************************/
	foreach ($coleccionCco as $centroCosto){
		$cont2 = 0;
		
		//Para el centro de costo consulto el detalle de los articulos
		$qArts = "SELECT 
					{$bdorigen}_000007.Medico,{$bdorigen}_000007.Fecha_data,{$bdorigen}_000007.Hora_data,Karcod,Karcco,Karexi,Karpro,Karvuc,Karmax,Karmin,Karpor,Karfuc,Artiva,{$bdorigen}_000007.Seguridad,{$bdorigen}_000001.Artfvn					  
				FROM
					{$bdorigen}_000007, {$bdorigen}_000001
				WHERE
					Karcod = Artcod
					AND Karexi > 0
					AND Karcco = '{$centroCosto->cco}'";
		
//		echo $qArts."<br>";
		
		$resArts = mysql_query($qArts, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qArts . " - " . mysql_error());
		$numArts = mysql_num_rows($resArts);
		
		/***
		 * Variables de control
		 */
		$cantArtsPorCco = 1;
		echo "CARGANDO ORDENES DEL CENTRO DE COSTOS $centroCosto->cco NUEVO CODIGO $centroCosto->ccoEquivalente, $centroCosto->cantArts ARTICULOS TOTALES<br>";
		
		if ($numArts > 0)
		{
			$contArts = 1;
			
			while ($contArts <= $numArts){
					
				$infoArts = mysql_fetch_array($resArts);

				if($cont2 == 2001 || $contArts == 1){
					/*************************************
					 * CONSULTA DEL CONSECUTIVO
					 *************************************/					
					$qCon = "SELECT
								Concon
							FROM 
								{$bddestino}_000008
							WHERE 
								Concod = '900';";		

//					echo $qCon."<br>";

					$resCon = mysql_query($qCon, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qCon . " - " . mysql_error());
					$numCon = mysql_num_rows($resCon);

					if ($numCon > 0)
					{
						$infoCon = mysql_fetch_array($resCon);
						$consecutivo = $infoCon['Concon'];
					}
					/*************************************
					 * FIN CONSULTA DEL CONSECUTIVO INICIAL
					 *************************************/
					
					/***************************************************************************************************************
					 * CREACION DEL ENCABEZADO Medico,Fecha_data,Hora_data,Karcod,Karcco,Karexi,Karpro,Karvuc,Karmax,Karmin,Karpor,Karfuc,Seguridad
					 ***************************************************************************************************************/
					echo "SE HA CREADO UNA NUEVA ORDEN DE COMPRA.  CONSECUTIVO:$consecutivo, CONCEPTO:$codigoConcepto  ::::$contOrdenes::::<br>";
					$qEnc = "INSERT INTO {$bddestino}_000010
								(Medico, Fecha_data, Hora_data, Menano, Menmes, Mendoc, Mencon,Menfec,Mencco,Menccd,Mendan,Menpre,Mennit,Menusu,Menfac,Menobs,Menest,Seguridad) 
							VALUES 
								('{$usuarioFarmastore}','{$fechaData}','{$horaData}','{$anio}','{$mes}','{$consecutivo}','{$codigoConcepto}','{$fechaData}','{$centroCosto->ccoEquivalente}','0','','0','$nitFarmastore','$usuarioFarmastore','','','off','C-$usuarioFarmastore')";
			
//					echo $qEnc."<br>";
					$resEnc = mysql_query($qEnc, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qEnc . " - " . mysql_error());					
					/***************************************************************************************************************
					 * FIN CREACION DEL ENCABEZADO
					 ***************************************************************************************************************/
					
					/*************************************
					 * INCREMENTAR EL CONSECUTIVO
					 *************************************/
					$qInc = "UPDATE {$bddestino}_000008 SET
								Concon = Concon + 1
							WHERE 
								Concod = '900';";		

//					echo $qInc."<br>";
					$resInc = mysql_query($qInc, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qInc . " - " . mysql_error());
					/*************************************
					 * FIN INCREMENTAR EL CONSECUTIVO
					 *************************************/
					$contOrdenes++;
					$cont2 = 1;
				}

				/***************************************************************************************************************
				 * CREACION DE CADA LINEA DE DETALLE
				 ***************************************************************************************************************/
				$valor = round(($infoArts['Karpro'] * $infoArts['Karexi']),2);
				if($infoArts['Karpro'] > 0){
					$valor = round(($infoArts['Karpro'] * $infoArts['Karexi'])*((1 + ($incremento/100))),2);
				}
				
				$fechaVencimiento = "0000-00-00";
				if($infoArts['Artfvn'] == 'on'){
					$fechaVencimiento = date("Y-m-d");
				}
				
				$qDet = "INSERT INTO {$bddestino}_000011
							(Medico,  Fecha_data,  Hora_data,  Mdecon,  Mdedoc,  Mdeart,  Mdecan,  Mdevto,  Mdepiv,  Mdefve,  Mdenlo,  Mdeest,  Seguridad) 
						VALUES 
							('{$usuarioFarmastore}','{$fechaData}','{$horaData}','{$codigoConcepto}','{$consecutivo}','{$infoArts['Karcod']}','{$infoArts['Karexi']}','{$valor}','{$infoArts['Artiva']}','{$fechaVencimiento}','1234','on','C-$usuarioFarmastore')";
			
//				echo $qDet."<br>";					
				$resDet = mysql_query($qDet, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qDet . " - " . mysql_error());
//				echo "<br><br>$cantArtsPorCco ARTICULOS CARGADOS PARA $centroCosto->cco EQUIVALENTE $centroCosto->ccoEquivalente<br><br>";
				/***************************************************************************************************************
			     * FIN CREACION DE CADA LINEA DE DETALLE
			     ***************************************************************************************************************/
				$contArts++;
				$cantArtsPorCco++;
				$cont2++;
			}
			echo ($cantArtsPorCco-1)." ARTICULOS TOTALES CARGADOS PARA $centroCosto->cco EQUIVALENTE $centroCosto->ccoEquivalente<br><br>";
		}
	}
}

if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}else{
	//Conexion base de datos
	$conex = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	//Forma
	echo "<form name='forma' action='FarmastoreMigracion1.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";

	//Fecha de consulta
	$fecha 	= date("Y-m-d");
	$hora 	= date("H:i:s");
	
	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}

	//FC para hacer las acciones
	switch ($waccion){
		case 'a':		//Consulta de los datos del reporte
			//Pantalla de consulta, la informacion va agrupada por habitacion, paciente, seguida por un listado de medicamentos
			echo '<span class="subtituloPagina2" align="center">';
			echo "Iniciando el proceso";
			echo "</span><br><br>";

			if(isset($wanio) && $wanio != '' && isset($wmes) && $wmes != '' && isset($wporcincremento) && $wporcincremento != '' && isset($wbdorigen) && $wbdorigen != '' && isset($wbddestino) && $wbddestino != ''){
//				echo "Parametros: '$wanio','$wmes','$wporcincremento','$wbdorigen','$wbddestino'";
				generarDetalleEncabezado($conex, $wanio, $wmes, $wporcincremento, $wbdorigen, $wbddestino);
			} else {
				mensajeEmergente("Revise que haya digitado los parámetros correctamente: año, mes, porcentaje incremento, base de datos origen y base de datos destino");
				funcionJavascript("inicio();");
			}
			
			echo '<span class="subtituloPagina2" align="center">';
			echo "Proceso finalizado.";
			echo "</span><br><br>";
			
			echo "<div align=center>";
			echo "<br>";
			echo "<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'>";
			echo "</div>";

			break;
		default:		//Muestra la pantalla inicial			
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Ingrese los parámetros de consulta';
			echo "</span>";
			echo "<br>";
			echo "<br>";
			
			//Año de generacion 
			echo "<tr><td class='fila1' width=120>Año</td>";
			echo "<td class='fila2' align='center' width=120>";
			echo "<input type='text' name='wanio' id='wanio' value='2009' size='3' maxlength='4' onkeypress='return validarEntradaEntera(event);'>";
			echo "</td>";
			echo "</tr>";
			
			//Mes de generacion wporcincremento
			echo "<tr><td class='fila1'>Mes</td>";
			echo "<td class='fila2' align='center'>";
			echo "<input type='text' name='wmes' id='wmes' value='10' size='1' maxlength='2' onkeypress='return validarEntradaEntera(event);'>";
			echo "</td>";
			echo "</tr>";
			
			//Incremento
			echo "<tr><td class='fila1'>Incremento</td>";
			echo "<td class='fila2' align='center'>";
			echo "<input type='text' name='wporcincremento' id='wporcincremento' value='1.5' size='2' onkeypress='return validarEntradaEntera(event);'> % ";
			echo "</td>";
			echo "</tr>";
			
			//Base de datos origen
			echo "<tr><td class='fila1'>Base de datos origen</td>";
			echo "<td class='fila2' align='center'>";
			echo "<select id='wbdorigen'>";
			echo "<option value='farstore' selected>farstore</option>";
			echo "<option value='farpmla'>farpmla</option>";
			echo "</select>";
			echo "</td>";
			echo "</tr>";
			
			//Base de datos destino
			echo "<tr><td class='fila1'>Base de datos destino</td>";
			echo "<td class='fila2' align='center'>";
			echo "<select id='wbddestino'>";
			echo "<option value='farstore'>farstore</option>";
			echo "<option value='farpmla' selected>farpmla</option>";
			echo "</select>";
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "<br/>";
			echo "<div align='center'>";
			echo "<tr><td align=center colspan=2><input type=button value='Generar' onclick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</div>";
		break;
	}
}
?>