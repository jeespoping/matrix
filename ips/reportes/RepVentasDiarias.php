<html>
<head>
<title>REPORTE DE VENTAS DIARIAS</title>

<!-- Funciones Javascript -->
<script type="text/javascript">

	function enviar(){
		document.forma.submit();
	}
	//Redirecciona a la pagina inicial
	function inicioReporte(wemp_pmla,wfecini,wfecfin,wsede,wtiporeporte)
	{
	 	document.location.href='RepVentasDiarias.php?wemp_pmla='+wemp_pmla+'&wfecini='+wfecini+'&wfecfin='+wfecfin+'&wsede='+wsede+'&wtiporeporte='+wtiporeporte+'&bandera=1';
	}

</script>

</head>

<?php
include_once("conex.php");
/*
 * REPORTE VENTAS DIARIAS
 */
//BS'D=================================================================================================================================
//PROGRAMA: RepVentasDiarias.php
//AUTOR: Mauricio Sánchez Castaño.
//TIPO DE SCRIPT: reporte
//RUTA DEL SCRIPT: matrix\IPS\Reportes\RepVentasDiarias.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+----------------------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 			|
//+-------------------+------------------------+----------------------------------------------------+
//|  2008-10-05       | Mauricio Sánchez       | creación del script.					 			|
//+-------------------+------------------------+----------------------------------------------------+
//|  2011-01-26       | Mario Cadavid   	   | Optimización de los query's para mejorar 			|
//| 				  |						   | el tiempo que se lleva en mostrar los resultados.	|
//| 				  |						   | También se adaptó el diseño con base en la hoja 	|
//|  			      | 				       | de estilos del sistema y se modificaron los campos |
//|  			      | 				       | de texto para que no pierdan los valores ingresados|
//+-------------------+------------------------+----------------------------------------------------+
//|  2011-10-18       | Mario Cadavid   	   | En la función consultarValorNotasGrupo	se agregó	|
//| 				  |						   | la consulta de fuentes de notas crédito para que 	|
//| 				  |						   | en el query donde se consulta el valor de las notas|
//|  			      | 				       | se tenga en cuenta estas fuentes.  				|
//+-------------------+------------------------+----------------------------------------------------+
//|  2011-12-29       | Mario Cadavid   	   | Optimización de los query's para mejorar 			|
//| 				  |						   | el tiempo que se lleva en mostrar los resultados.	|
//| 				  |						   | Se crearon las tablas temporales "facturas",		|
//|  			      | 				       | "recibos", "detalle_venta", "otros_conceptos",  	|
//|  			      | 				       | "monturas"  										|
//+-------------------+------------------------+----------------------------------------------------+

//FECHA ULTIMA ACTUALIZACION 	: 2011-12-28

//=================================================================================================================================*/
include_once("root/comun.php");

class otroConcepto{
	var $campo1;
	var $campo2;
	var $campo3;
	var $campo4;
}

//Funciones
function consultarOtrosConceptos($conex, $codigoFactura, $fuenteFactura)
{
	global $wbasedato;
	$wbasedato=strtolower($wbasedato);

	$q = "	SELECT
				Fenfac Factura,
				Fenffa Fuente,
				Grucod Codigo_grupo,
				Grudes Grupo,
				Venusu Facturo,
				(Vdeart) Articulo_montura,
				FLOOR(ABS(( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))))) Valor_montura
			FROM
				otros_conceptos
			WHERE Fenffa = '$fuenteFactura'
				AND Fenfac = '$codigoFactura'
			ORDER BY Fenfac
		 ";

	$coleccion = array();
	//echo $q;
	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$valorOtros = 0;
	$cont1 = 0;

	while ($cont1 < $num)
	{
		$otroConcepto = new otroConcepto();
		$resultSet = mysql_fetch_array($res);

		$otroConcepto->campo1 = $resultSet['Codigo_grupo'];
		$otroConcepto->campo2 = $resultSet['Facturo'];
		$otroConcepto->campo3 = $resultSet['Valor_montura'];
		$otroConcepto->campo4 = $resultSet['Articulo_montura'];

		$coleccion[] = $otroConcepto;
		$cont1++;
	}
	return $coleccion;
}

function consultarOtrosConceptos2($conex, $numventa)
{
	global $wbasedato;
	$wbasedato=strtolower($wbasedato);

	$q = "	SELECT

				Grucod Codigo_grupo,
				Grudes Grupo,
				Venusu Facturo,
				(Vdeart) Articulo_montura,
				FLOOR(ABS(( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))))) Valor_montura
			FROM
				otros_conceptos
			WHERE Vennum = '".$numventa."'
		 ";

	$coleccion = array();
	//echo $q;
	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$valorOtros = 0;
	$cont1 = 0;

	while ($cont1 < $num)
	{
		$otroConcepto = new otroConcepto();
		$resultSet = mysql_fetch_array($res);

		$otroConcepto->campo1 = $resultSet['Codigo_grupo'];
		$otroConcepto->campo2 = $resultSet['Facturo'];
		$otroConcepto->campo3 = $resultSet['Valor_montura'];
		$otroConcepto->campo4 = $resultSet['Articulo_montura'];

		$coleccion[] = $otroConcepto;
		$cont1++;
	}
	return $coleccion;
}

/**
 * Consulta de monturas asociadas, puede tratarse de una o varias monturas asociadas por factura, además en la columna monturas
 * deben detallarse los excedentes de montura
 *
 * @param unknown_type $conex
 * @param unknown_type $codigoFactura
 * @param unknown_type $fuenteFactura
 * @return unknown
 */


function consultarMonturasAsociadas($conex, $codigoFactura, $fuenteFactura)
{
	global $wbasedato;
	$wbasedato=strtolower($wbasedato);

	//Consulta monturas asociadas para la factura y la fuente y consulta los excedentes de montura.
	$q = "	SELECT
				ordvem, Vdeart, ROUND((( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))))) valor , Artnom
			FROM
				monturas
			WHERE Venffa = '$fuenteFactura'
				AND Vennfa = '$codigoFactura'
		 ";

	//echo $q;

	$coleccion = array();

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$valorOtros = 0;
	$cont1 = 0;

	while ($cont1 < $num)
	{
		$otroConcepto = new otroConcepto();
		$resultSet = mysql_fetch_array($res);

		$otroConcepto->campo1 = isset($resultSet['ordvem']) ? $resultSet['ordvem'] : '';
		$otroConcepto->campo2 = $resultSet['Vdeart']."-".$resultSet['Artnom'];
		$otroConcepto->campo3 = $resultSet['valor'];

		$coleccion[] = $otroConcepto;
		$cont1++;
	}
	return $coleccion;
}


function consultarMonturasAsociadas2($conex, $numeroventa)
{
	global $wbasedato;
	$wbasedato=strtolower($wbasedato);

	//Consulta monturas asociadas para la factura y la fuente y consulta los excedentes de montura.
	$q = "	SELECT
				ordvem, Vdeart, ROUND((( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))))) valor , Artnom
			FROM
				monturas
			WHERE
				Vennum = '".$numeroventa."'
		 ";

	//echo $q;

	$coleccion = array();

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$valorOtros = 0;
	$cont1 = 0;

	while ($cont1 < $num)
	{
		$otroConcepto = new otroConcepto();
		$resultSet = mysql_fetch_array($res);

		$otroConcepto->campo1 = isset($resultSet['ordvem']) ? $resultSet['ordvem'] : '';
		$otroConcepto->campo2 = $resultSet['Vdeart']."-".$resultSet['Artnom'];
		$otroConcepto->campo3 = $resultSet['valor'];

		$coleccion[] = $otroConcepto;
		$cont1++;
	}
	return $coleccion;
}

/**
 * Consulta de lentes asociados, puede tratarse de una o varios lentes asociados por factura, además en la columna lentes
 * deben detallarse los excedentes de lente
 *
 * @param unknown_type $conex
 * @param unknown_type $codigoFactura
 * @param unknown_type $fuenteFactura
 * @return unknown
 */
function consultarLentesAsociados($conex, $codigoFactura, $fuenteFactura)
{
	global $wbasedato;
	$wbasedato=strtolower($wbasedato);

	$q = "	SELECT
				Artnom,ordven.ordvel,
				Vdeart,
				ROUND((( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))))) valor,
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'SE' ) valor_servicios,
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'COP' ) valor_copagos,
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'SUB' ) valor_subsidios
			FROM
				ordven, detalle_venta, ".$wbasedato."_000001
			WHERE
				Vdeart = Artcod
				AND Vennum = Vdenum
				AND Venffa = '$fuenteFactura'
				AND Vennfa = '$codigoFactura'
				AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN ('LO','LE')
			UNION
			SELECT
				Artnom,ordven2.ordvel,
				Vdeart,
				ROUND((( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))))) valor,
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'SE' ) valor_servicios,
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'COP' ) valor_copagos,
				(SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'SUB' ) valor_subsidios
			FROM
				ordven2, detalle_venta, ".$wbasedato."_000001
			WHERE
				Vdeart = Artcod
				AND Vennum = Vdenum
				AND Venffa = '$fuenteFactura'
				AND Vennfa = '$codigoFactura'
				AND Artcod IN ('99EXD01','99EXD02')
		 ";

	//echo $q;

	$coleccion = array();

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$valorOtros = 0;
	$cont1 = 0;

	while ($cont1 < $num)
	{
		$otroConcepto = new otroConcepto();
		$resultSet = mysql_fetch_array($res);

		$otroConcepto->campo1 = isset($resultSet['ordvel']) ? $resultSet['ordvel'] : '';
		$otroConcepto->campo2 = $resultSet['Vdeart']."-".$resultSet['Artnom'];

		if($cont1 == 0){
			$otroConcepto->campo3 = $resultSet['valor'] + $resultSet['valor_servicios'] + $resultSet['valor_copagos'] + $resultSet['valor_subsidios'];
		} else {
			$otroConcepto->campo3 = $resultSet['valor'];
		}

		$coleccion[] = $otroConcepto;
		$cont1++;
	}
	return $coleccion;
}

function consultarLentesAsociados2($conex, $numeroventa)
{
	global $wbasedato;
	$wbasedato=strtolower($wbasedato);

	$q = "	SELECT
				ordven.ordvel,
				Vdeart,
				ROUND((( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))))) valor,Artnom	FROM
				ordven, detalle_venta, ".$wbasedato."_000001
			WHERE
				Vdeart = Artcod
				AND Vennum = Vdenum
				AND Vennum = '".$numeroventa."'
				AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN ('LO','LE')
			UNION
			SELECT
				ordven2.ordvel,
				Vdeart,
				ROUND((( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))))) valor , Artnom
			FROM
				ordven2, detalle_venta, ".$wbasedato."_000001
			WHERE
				Vdeart = Artcod
				AND Vennum = Vdenum
				AND Vennum = '".$numeroventa."'
				AND Artcod IN ('99EXD01','99EXD02')
		 ";





	//echo $q;

	$coleccion = array();

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$valorOtros = 0;
	$cont1 = 0;

	while ($cont1 < $num)
	{
		$otroConcepto = new otroConcepto();
		$resultSet = mysql_fetch_array($res);

		$otroConcepto->campo1 = isset($resultSet['ordvel']) ? $resultSet['ordvel'] : '';
		$otroConcepto->campo2 = $resultSet['Vdeart']."-".$resultSet['Artnom'];

		if($cont1 == 0){
			$otroConcepto->campo3 = $resultSet['valor'] + $resultSet['valor_servicios'] + $resultSet['valor_copagos'] + $resultSet['valor_subsidios'];
		} else {
			$otroConcepto->campo3 = $resultSet['valor'];
		}

		$coleccion[] = $otroConcepto;
		$cont1++;
	}
	return $coleccion;
}

function consultarValorNotasGrupo($conex, $factura, $fuente, $grupos){
	global $wbasedato;
	$wbasedato=strtolower($wbasedato);

	// Consulto las notas crédito de facturación
	$qnc =   "   SELECT Carfue "
		  ."     FROM ".$wbasedato."_000040 "
		  ."  	WHERE Carncr = 'on' "
		  ."	  AND Carest = 'on' ";
	$resnc = mysql_query($qnc,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qnc." - ".mysql_error());
	$numnc = mysql_num_rows($resnc);
	if($numnc>0)
		$fuentes_notas = "(";

	while($rownc = mysql_fetch_array($resnc))
	{
		$fuentes_notas .= "'".$rownc['Carfue']."',";
	}

	if($numnc>0)
		$fuentes_notas .= ")";

	$fuentes_notas = str_replace(",)",")",$fuentes_notas);

	$q = "	SELECT
				IFNULL(SUM(Fdevco),0) valor
			FROM
				vnotas
			WHERE Fdecon IN ($grupos)
			AND Rdefac = '$factura'
			AND Rdeffa = '$fuente'
			AND Fdefue IN $fuentes_notas
		 ";

	//echo "<div align='center'>".$q."</div>";

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$valor = 0;

	if($num>0)
	{
		$resultSet = mysql_fetch_array($res);
		$valor = $resultSet['valor'];
	}
	return $valor;
}

/**
 * Este valor consolidado es exclusivo para lentes y debe contemplar SUBSIDIOS, COPAGO Y SERVICIOS.  Ya que esto mismo fue cargado al valor
 * del lente
 *
 * @param unknown_type $conex
 * @param unknown_type $factura
 * @param unknown_type $fuente
 * @param unknown_type $grupos
 * @return unknown
 */
function consultarValorNotasLente($conex, $factura, $fuente, $grupos){
	global $wbasedato;
	$wbasedato=strtolower($wbasedato);

	$q = "	SELECT
				IFNULL(SUM(Fdevco),0) valor
			FROM
				recibos, detalle_concepto
			WHERE
				Fdefue = Rdefue
				AND Fdedoc = Rdenum
				AND Fdecon IN ($grupos)
				AND Rdefac = '$factura'
				AND Rdeffa = '$fuente'
		 ";

	//echo "<div align='center'>".$q."</div>";

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$valor = 0;

	if($num>0)
	{
		$resultSet = mysql_fetch_array($res);
		$valor = $resultSet['valor'];
	}
	return $valor;
}

//Inicio
$conex = obtenerConexionBD("matrix");

//Validación de usuario
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

$wactualiz="Octubre 18 de 2011";

session_start();

// Consulto los datos de la empresa actual y los asigno a la variable $empresa
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("REPORTE DE VENTAS DIARIAS",$wactualiz,"logo_".$wbasedato);

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es válido
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte

  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");

  	$wcf1="#41627e";  //Fondo encabezado del Centro de costos
  	$wcf="#c2dfff";   //Fondo procedimientos
  	$wcf2="003366";  //Fondo titulo pantalla de ingreso de parametros
  	$wcf3="#659ec6";  //Fondo encabezado del detalle
  	$wclfg="003366"; //Color letra parametros

  	echo "<form action='RepVentasDiarias.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset($wsede) or !isset($resultado))
  	{
		echo "<center><table border=0>";

		//Petición de ingreso de parametros
		echo "<tr>";
		echo "<td height='37' colspan='2' align='center' class='encabezadoTabla'>
				Seleccione los datos a consultar
			  </td></tr>";

		if(!isset($wtiporeporte) or $wtiporeporte=='')
		{
			echo "<tr>
					<td class='fila2'>
							<input type='radio' name='wtiporeporte' value='vsinfacturas' checked>
								<b>Ventas sin Facturas</b>
					</td>
					<td class='fila2'>
							<input type='radio' name='wtiporeporte' value='vconfacturas'>
								<b>Ventas Facturadas</b>
					</td>
				  </tr>";
		}
		else if($wtiporeporte=='vsinfacturas')
		{
			echo "<tr>
					<td class='fila2'>
							<input type='radio' name='wtiporeporte' value='vsinfacturas' checked>
								<b>Ventas sin Facturas</b>
					</td>
					<td class='fila2'>
							<input type='radio' name='wtiporeporte' value='vconfacturas'>
								<b>Ventas Facturadas</b>
					</td>
				  </tr>";
		}
		else
		{
				echo "<tr>
					<td class='fila2'>
							<input type='radio' name='wtiporeporte' value='vsinfacturas' >
								<b>Ventas sin Facturas</b>
					</td>
					<td class='fila2'>
							<input type='radio' name='wtiporeporte' value='vconfacturas' checked>
								<b>Ventas Facturadas</b>
					</td>
				  </tr>";


		}

		//Parámetros de consulta del reporte
  		if (!isset ($bandera))
  		{
 			$wfecini=$wfecha;
  			$wfecfin=$wfecha;
  			$wsede="";
		}

		//Fecha inicial de consulta
  		echo "<tr>";
  		echo "<td class=fila2 align=center> &nbsp; <b>Fecha inicial : &nbsp; </b>";
  		campoFechaDefecto("wfecini", $wfecini);
  		echo " &nbsp; </td>";

  		//Fecha final de consulta
  		echo "<td class=fila2 align=center> &nbsp; <b>Fecha final : </b> &nbsp; ";
  		campoFechaDefecto("wfecfin", $wfecfin );
  		echo " &nbsp; </td>";
  		echo "</tr>";

		//Sede
		//Si son administrativos pueden ver todas las sedes, caso contrario solamente la sede a la que está vinculado el usuario que ingresa
		$esAdministrativo = false;
		$centroCostos = "";
		$vecUsuario = explode("-",$user);

  	  	$q=  "	SELECT cjeadm, SUBSTRING_INDEX( Cjecco, '-', 1 ) Cjecco
				FROM ".$wbasedato."_000030
				WHERE Cjeusu = '$vecUsuario[1]'
				ORDER by 1";

  		$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
  		$num1 = mysql_num_rows($res1);

  		if ($num1 > 0 )
  		{
  			$row1 = mysql_fetch_array($res1);

  			if($row1['cjeadm'] == 'on'){
  				$esAdministrativo = true;
  			}
  			$centroCostos = $row1['Cjecco'];
  		}



  		if($esAdministrativo)
		{
  			$q=  "SELECT ccocod, ccodes "
  			."    FROM ".$wbasedato."_000003 "
  			."    ORDER by 1";
  		}
		else
		{
  			$q=  "SELECT ccocod, ccodes "
  			."    FROM ".$wbasedato."_000003
  				  WHERE Ccocod = '$centroCostos'
  				  ORDER by 1";
  		}

		echo "<tr><td colspan='2' align=center class=fila2> &nbsp; <b>Sede: &nbsp;</b>";
  		echo "<select name='wsede'>";

  		$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
  		$num1 = mysql_num_rows($res1);
  		if ($num1 > 0 )
  		{

  			for ($i=1;$i<=$num1;$i++)
  			{
  				$row1 = mysql_fetch_array($res1);
				$selec = "";
				if($wsede==$row1[0]) $selec = " selected";
  				echo "<option value=".$row1[0].$selec.">".$row1[0]."-".$row1[1]."</option>";
  			}
  		}
  		echo "</select> &nbsp; </td></tr>";

  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
  		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

		//Botones "Consultar" y "Cerrar ventana"
		echo "<tr><td align=center colspan=2><br /><input type='submit' id='searchsubmit' value='Consultar'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";

  		echo "</table>";
  	}
	else
	{
		//Se registra fecha y hora de la ultima ejecucion
  		//************INICIO LOG:::
		$debug = true;
		if($debug)
		{
			$fechaLog = date("Y-m-d");
			$horaLog = date("H:i:s");

	    	//Creacion de un archivo plano para tomar una imagen de la informacion de las camas en ese momento
	    	$nombreArchivo = "uvglobal_vtas.dat";

	    	//Apuntador en modo de adicion si no existe el archivo se intenta crear...
	    	$archivo = fopen($nombreArchivo, "w");
	    	if(!$archivo){
	    		$archivo = fopen($nombreArchivo, "w");
	    	}

	    	$contenidoLog = "*CONSULTA DE VENTAS NUEVA ($fechaLog - $horaLog)*->  Usuario: \r\n";
	    }
		//************FIN LOG:::

  		//Consulto la sede
		$q = "	SELECT ccocod, ccodes "
			."  FROM ".$wbasedato."_000003 "
			."  WHERE ccocod = '".$wsede."'";
  		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);
		if($row1[1] && $row1[1]!='')
			$wsedenom = ' - '.$row1[1];
		else
			$wsedenom = '';

		//Muestro los parámetros que se ingresaron en la consulta
		echo "<table border=0 cellspacing=2 cellpadding=0 align=center size='300'>";

		if($wtiporeporte =='vsinfacturas')
		{
			echo "<tr>
					<td class='fila2' align='center' colspan='2'>
						<b style='font-size: 15pt' >Ventas sin Facturas </b>
					</td>
				  </tr>";
		}
		else
		{
				echo "<tr>
					<td class='fila2' align='center' colspan='2'>
						<b style='font-size: 15pt'>Ventas Facturadas </b>
					</td>
				  </tr>";

		}
		echo "<tr class='fila2'>";
		echo "<td align=left><strong>&nbsp;Fecha inicial : </strong>".$wfecini."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "<td align=left><strong>&nbsp;Fecha final : </strong>".$wfecfin."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "</tr>";
		echo "<tr class='fila2'>";
		echo "<td align=center colspan='2'><strong>&nbsp;Sede : </strong>".$wsede.$wsedenom."</td>";
		echo "</tr>";
		echo "</table>";

  		echo "</br>";

  		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		echo "<input type='HIDDEN' NAME= 'wsede' value='".$wsede."'>";
  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		if($wtiporeporte =='vsinfacturas')
		{

			//-----------------------------------

			// Borra la tabla temporal de detalle_venta
			$qdel = "	DROP TABLE IF EXISTS encabezado_venta";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());


			// Creación de la tabla temporal de encabezado_venta
			$qven =  " CREATE TABLE IF NOT EXISTS encabezado_venta "
					." ( INDEX idxvnum ( Vennum(10) ), INDEX idxffavnfa ( Venffa(10), Vennfa(20) ) ) "
					." SELECT Venano, Venmes, Vennum, Venfec, Vencon, Vencco, Vencaj, Vencod, Vennit, Ventcl, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Venffa, Vennfa, Vennmo, Venusu, Ventve, Venmsj, Venest , Venbon				"
					." FROM  ".$wbasedato."_000016 "
					." WHERE  Venffa ='' "
					."   AND  Vennfa = '' "
					."   AND  Venfec  BETWEEN '".$wfecini."' AND '".$wfecfin."'"
					."   AND  Venest = 'on' ";
			// echo $qven."<br />";
			$resven = mysql_query($qven, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qven . " - " . mysql_error());

			//----------------------------------

			//------------------------------------
			// Borra la tabla temporal de detalle_venta
			$qdel = "	DROP TABLE IF EXISTS detalle_venta ";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			// Creación de la tabla temporal de detalle_venta
			$qven =  " CREATE TABLE IF NOT EXISTS detalle_venta "
					." ( INDEX idxnum ( Vdenum(10) ), INDEX idxart ( Vdeart(20) ) ) "
					." SELECT Vdenum, Vdeart, Vdevun, Vdecan, Vdepiv, Vdedes, Vdeest "
					." FROM encabezado_venta, ".$wbasedato."_000017 "
					." WHERE Vennum = Vdenum ";
			//echo $qven."<br />";
			$resven = mysql_query($qven, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qven . " - " . mysql_error());

			//----------------------------------------

			//--------------------------------------------------
			// Borra la tabla temporal de detalle_venta
			$qdel = "	DROP TABLE IF EXISTS recibos ";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());


			// Creación de la tabla temporal de recibos
			$qfac =  " CREATE TABLE IF NOT EXISTS recibos "
					." ( INDEX idxfac ( Rdeffa(4),Rdefac(10) ), INDEX idxdoc ( Rdefue(4),Rdenum ), INDEX idxcco ( Rdecco(10) )   ) "
					." SELECT Rdefac, Rdeffa, Rdefue, Rdenum, Rdecco "
					." FROM encabezado_venta, ".$wbasedato."_000021 "
					." WHERE Rdevta = Vennum ";
			//echo $qfac."<br />";
			$resfac = mysql_query($qfac, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qfac . " - " . mysql_error());
			//-----------------------------------------------------



			//--------------------------------------------------
			// Borra la tabla temporal de detalle_venta
			$qdel = "	DROP TABLE IF EXISTS ord ";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());


			// Creación de la tabla temporal de ordenes voy
			$qord =  " CREATE TABLE IF NOT EXISTS ord "
					." ( INDEX idxfac ( ordffa(4),ordfac(10) ) ) "
					." SELECT MIN( ordfen ) ordfen, ordfac, ordffa, ordvel, ordvem, ordlei, ordled, ordcaj,ordven "
					." FROM encabezado_venta, ".$wbasedato."_000133 "
					." WHERE ordven = Vennum"
					." GROUP BY ordven ";

			//echo $qord."<br />";
			$resord = mysql_query($qord, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qord . " - " . mysql_error());

			//----------------------------------------

			//-----------------------------------------
			// Borra la tabla temporal de ordenes vendidas si existe
			$qdel = "	DROP TABLE IF EXISTS ordven ";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			// Creación de la tabla temporal de ordenes vendidas
			$qordven =   " CREATE TABLE IF NOT EXISTS ordven "
						." (INDEX idxvfac ( Venffa(4),Vennfa(10) ), INDEX idxvnum (Vennum) ) "
						." SELECT Ordvem, Ordvel, Ordcaj, Vennum, Venffa, Vennfa, Venusu, Vencaj, Vencco "
						." FROM encabezado_venta "
						." LEFT JOIN ord "
						." 		ON ordven = Vennum ";
			//echo $qordven."<br />";
			$resordven = mysql_query($qordven, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qordven . " - " . mysql_error());
			//------------------------------------------

			//--------------------------------------------------
			//Necesito crear otra temporal para ordenes vendidas debido que en
			// uno de los query un UNION necesita usar 2 veces esta tabla temporal
			// Borra la tabla temporal de ordenes vendidas 2 si existe
			$qdel = "	DROP TABLE IF EXISTS ordven2 ";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			// Creación de la tabla temporal de ordenes vendidas
			$qordven2 =  " CREATE TABLE IF NOT EXISTS ordven2 "
						." (INDEX idxvvfac ( Venffa(4),Vennfa(10) ), INDEX idxvvnum (Vennum) ) "
						." SELECT Ordvem, Ordvel, Ordcaj, Vennum, Venffa, Vennfa, Venusu, Vencaj, Vencco "
						." FROM ordven ";
			//echo $qordven2."<br />";
			$resordven2 = mysql_query($qordven2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qordven2 . " - " . mysql_error());

			//-----------------------------------------------


			//-----------------------------------------
			// Borra la tabla temporal de ordenes vendidas si existe
			$qdel = "	DROP TABLE IF EXISTS monturas ";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());


			// Creación de la tabla temporal de detalle de facturas por concepto
			$qmon =  "  CREATE TABLE IF NOT EXISTS monturas
						(INDEX idxvfac ( Venffa(4),Vennfa(10) ) )
						SELECT
							ordven.ordvem ordvem, Vdeart, Vdevun, Vdecan, Vdedes, Vdepiv, Venffa, Vennfa,Vennum,Artnom
						FROM
							ordven, detalle_venta, ".$wbasedato."_000001
						WHERE
							Vdeart = Artcod
							AND Vennum = Vdenum
							AND SUBSTRING_INDEX( Artgru, '-', 1 ) = 'MT'
						UNION
						SELECT
							ordven2.ordvem, Vdeart, Vdevun, Vdecan, Vdedes , Vdepiv, Venffa, Vennfa,Vennum,Artnom
						FROM
							ordven2, detalle_venta, ".$wbasedato."_000001
						WHERE
							Vdeart = Artcod
							AND Vennum = Vdenum
							AND Artcod = '99EXD03'
						";
			//echo $qmon."<br />";
			$resmon = mysql_query($qmon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmon . " - " . mysql_error());
			//---------------------------------------------------


			//-------------------------------
			// Borra la tabla temporal de detalle de facturas por conceptos
			$qdel = "	DROP TABLE IF EXISTS detalle_concepto ";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			// Creación de la tabla temporal de detalle de facturas por concepto
			$qdet =  " CREATE TABLE IF NOT EXISTS detalle_concepto "
					." ( INDEX idxfac ( fdefue(4),fdedoc(10) ), INDEX idxcco ( fdecco(10) ), INDEX idxcon ( fdecon(10) ) ) "
					." SELECT Fdefue, Fdedoc, Fdecco, Fdecon, Fdevco, Fdevde, Fdeest "
					." FROM facturas, ".$wbasedato."_000065 "
					." WHERE Fenffa = Fdeffa "
					." AND Fenfac = Fdefac ";
			//echo $qdet."<br />";
			//---------------------------------------


			 // Borra la tabla temporal de detalle de facturas por conceptos
			$qdel = "	DROP TABLE IF EXISTS otros_conceptos ";
			$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			// Creación de la tabla temporal de detalle de facturas por concepto
			$qotr =  "  CREATE TABLE IF NOT EXISTS otros_conceptos

					SELECT
						 Grucod, Grudes, encabezado_venta.Venusu, Vdeart, Vdevun, Vdecan, Vdedes, Vdepiv  , encabezado_venta.Venffa, encabezado_venta.Vennfa,encabezado_venta.Vennum
					FROM
						detalle_venta, encabezado_venta, ".$wbasedato."_000001 , ".$wbasedato."_000004
					WHERE
						Vdeest = 'on'
						AND Vdenum = encabezado_venta.Vennum
						AND Vdeart = Artcod
						AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN ('LC','ES','LQ','AC')
						AND Grucod = SUBSTRING_INDEX( Artgru, '-', 1 )
					";

			$resotr = mysql_query($qotr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qotr . " - " . mysql_error());


			$consulta = "SELECT  Vennum, Venfec ,Vennit, Clinom ,IFNULL( ( SELECT Vmpmed FROM ".$wbasedato."_000050 WHERE Vmpvta = Vennum ), '' ) Medico , Venvto,IFNULL(ordcaj,'') Caja , Empnom,Empcod, encabezado_venta.Vencco
						   FROM  encabezado_venta LEFT JOIN ord ON ordven  = Vennum , ".$wbasedato."_000041 , ".$wbasedato."_000024
						  WHERE  Clidoc = Vennit
						    AND  Vencod = Empcod
							AND  Vencco = '".$wsede."'";

			$resconsulta = mysql_query($consulta, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $consulta . " - " . mysql_error());
			$cont1 = 0;
			while($facturaActual = mysql_fetch_array($resconsulta))
			{

				if($cont1==0)
				{
					echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecini\",\"$wfecfin\",\"$wsede\",\"$wtiporeporte\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";
					echo "<table border=0 align=center>";
					echo "<tr class='encabezadotabla'>";
					echo "<td align=center rowspan=2><b>NUMERO VENTA</b></td>";
					echo "<td align=center rowspan=2><b>FECHA VENTA</b></td>";
					echo "<td align=center rowspan=2><b>CAJA</b></td>";
					echo "<td align=center colspan=3><b>CLIENTE</b></td>";
					echo "<td align=center rowspan=2><b>MEDICO</b></td>";
					echo "<td align=center colspan=3><b>MONTURA</b></td>";
					echo "<td align=center colspan=3><b>LENTE 1</b></td>";
					echo "<td align=center colspan=3><b>OTROS (LC,ES,LQ,AC)</b></td>";
					echo "<td align=center rowspan=2><b>VALOR VENTA</b></td>";
					echo "<td align=center rowspan=2><b>ABONOS</b></td>";
					echo "<td align=center rowspan=2><b>SALDO</b></td>";
					echo "</tr>";
					echo "<tr class='encabezadotabla'>";
					echo "<td align=center><b>DOCUMENTO</b></td>";
					echo "<td align=center><b>NOMBRE</b></td>";
					echo "<td align=center><b>IPS</b></td>";
					echo "<td align=center><b>VENDEDOR MONTURA</b></td>";
					echo "<td align=center><b>CODIGO MONTURA</b></td>";
					echo "<td align=center><b>VALOR</b></td>";
					echo "<td align=center><b>VENDEDOR LENTE</b></td>";
					echo "<td align=center><b>CODIGO LENTE</b></td>";
					echo "<td align=center><b>VALOR</b></td>";
					echo "<td align=center><b>CODIGO</b></td>";
					echo "<td align=center><b>VENDEDOR</b></td>";
					echo "<td align=center><b>VALOR</b></td>";
					echo "</tr>";


				}

				$cont1 % 2 == 0 ? $class = 'fila1' : $class = 'fila2';
				$cont1 ++ ;
				echo "<tr class='".$class."'>";
				echo "<td>".$facturaActual['Vennum']."</td>";
				echo "<td>".$facturaActual['Venfec']."</td>";
				echo "<td>".$facturaActual['Caja']."</td>";
				echo "<td>".$facturaActual['Vennit']."</td>";
				echo "<td>".$facturaActual['Clinom']."</td>";
				echo "<td>".$facturaActual['Empnom']."</td>";
				echo "<td>".$facturaActual['Medico']."</td>";

				$monturas = consultarMonturasAsociadas2($conex, $facturaActual['Vennum']);
				$cantidadMonturas = count($monturas);
				if($cantidadMonturas == 0){
					$cantidadMonturas++;
				}

				$campo7 = "";
				$campo8 = "";
				$campo9 = "";

				foreach ($monturas as $montura){
					$campo7 .= $montura->campo1."<br>";
					$campo8 .= $montura->campo2."<br>";
					//$campo9 .= number_format(($montura->campo3),2,'.',',')."<br>";
					$campo9 .= $montura->campo3."<br>";

					$acumMonturas += $montura->campo3;
				}
				echo "<td  align=left>".$campo7."</td>";
				echo "<td  align=left>".$campo8."</td>";
				echo "<td  align=right>".$campo9."</td>";

				$lentes = consultarLentesAsociados2($conex, $facturaActual['Vennum']);
				$campo10 = "";
				$campo11 = "";
				$campo12 = "";

				foreach ($lentes as $lente){
					$campo10 .= $lente->campo1."<br>";
					$campo11 .= $lente->campo2."<br>";
					//$campo12 .= number_format(($lente->campo3),2,'.',',')."<br>";
					$campo12 .= $lente->campo3."<br>";

					$acumLentes += $lente->campo3;
				}
				// $selecttotallentes
				// $campo12 =
				// $acumLentes = ;

				$qvalorlentes = "SELECT Vdevun  ,Vdecan
								   FROM ".$wbasedato."_000017  , ".$wbasedato."_000001
								  WHERE Vdenum ='".$facturaActual['Vennum']."'
									AND Vdeart = Artcod
									AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN ('SE','COP','SUB') ";

				$qvalorlentes;
				$resqvalorlentes = mysql_query($qvalorlentes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qvalorlentes . " - " . mysql_error());

				$wvalorlentes = 0;
				while($rowqvalorlentes = mysql_fetch_array($resqvalorlentes))
				{
					$wvalorlentes = $wvalorlentes + ($rowqvalorlentes['Vdevun'] * $rowqvalorlentes['Vdecan']);
				}

				if( trim( $campo12 ) == "" ){
					$campo12 = 0;
				}
				if( trim( $wvalorlentes ) == "" ){
					$wvalorlentes = 0;
				}
				$campo12 =  $campo12  + $wvalorlentes;
				$acumLentes = $acumLentes + $wvalorlentes;
				echo "<td  align=left>".$campo10."</td>";
				echo "<td  align=left>".$campo11."</td>";
				echo "<td  align=right>".$campo12."</td>";

				$otrosConceptos = consultarOtrosConceptos2($conex, $facturaActual['Vennum']);

				//Otros
				$contCptos = 0;
				$campo23 = "";
				$campo24 = "";
				$campo25 = "";
				$campo26 = "";
				$campo27 = "";
				$campo28 = "";

				foreach ($otrosConceptos as $concepto){
					if($contCptos > 0){
						//echo "<tr class='".$class."'><td colspan='13'></td>";
					}
					$campo23 .= $concepto->campo1." - ".$concepto->campo4."<br>";
					$campo24 .= $concepto->campo2."</br>";
					$campo25 .= $concepto->campo3."</br>";

					/*echo "<td align=left >".$concepto->campo1." - ".$concepto->campo4."</td>";
					echo "<td align=left>".$concepto->campo2."</td>";
					// echo "<td align=right>".number_format(($concepto->campo3),2,'.',',')."</td>";
					echo "<td align=right>".$concepto->campo3."</td>";*/

					$acumOtrosLentes += $concepto->campo3;

					if($contCptos > 0){
					//	echo "</tr>";
					} else {

						$query_saldo = "SELECT  Rdevta,Rdevca
										  FROM  ".$wbasedato."_000021
										 WHERE  Rdevta='".$facturaActual['Vennum']."' AND Rdeest='on'";

						$res2 = mysql_query($query_saldo,$conex);
						$vrecibido = 0;
						while($row3 = mysql_fetch_array($res2))
						{

							$vrecibido =$vrecibido + $row3['Rdevca'] ;

						}
						$acumAbonos  += $vrecibido;

						$acumValorFactura += $facturaActual['Venvto'];
						// echo "<td align=right>".number_format(($facturaActual['Venvto']),2,'.',',')."</td>";
						// echo "<td  align=right>".number_format(($vrecibido),2,'.',',')."</td>";
						$campo26.= $facturaActual['Venvto'];
						$campo27.= $vrecibido;

						//echo "<td align=right>".$facturaActual['Venvto']."</td>";
						//echo "<td  align=right>".$vrecibido."</td>";
						$saldo = (($facturaActual['Venvto']*1  )- ($vrecibido *1 ));
						$acumSaldos += $saldo;
						$campo28.= $saldo;
						//echo "<td  align=right>".$saldo."</td>";
					 }
					$contCptos++;
				}
				if($contCptos != 0)
				{
					echo "<td  align=left>".$campo23."</td>";
					echo "<td  align=left>".$campo24."</td>";
					echo "<td  align=right>".$campo25."</td>";
					echo "<td  align=right>".$campo26."</td>";
					echo "<td  align=right>".$campo27."</td>";
					echo "<td  align=right>".$campo28."</td>";
				}
				if($contCptos == 0){

					echo "<td  align=left>&nbsp;</td>";
					echo "<td  align=left>&nbsp;</td>";
					echo "<td  align=left></td>";

					$query_saldo = "SELECT  Rdevta,Rdevca
									  FROM  ".$wbasedato."_000021
									 WHERE  Rdevta='".$facturaActual['Vennum']."' AND Rdeest='on'";

					$res2 = mysql_query($query_saldo,$conex);
					$vrecibido = 0;
					while($row3 = mysql_fetch_array($res2))
					{

						$vrecibido =$vrecibido + $row3['Rdevca'] ;

					}

					$acumAbonos  += $vrecibido;

					$acumValorFactura += $facturaActual['Venvto'];
					echo "<td  align=left>".$facturaActual['Venvto']."</td>";
					//echo "<td  align=right>".number_format(($vrecibido),2,'.',',')."</td>";
					echo "<td  align=right>".$vrecibido."</td>";
					$saldo = (($facturaActual['Venvto']*1  )- ($vrecibido *1 ));
					$acumSaldos += $saldo;
					//echo "<td  align=right>".number_format(($saldo),2,'.',',')."</td>";
					echo "<td  align=right>".$saldo."</td>";
				}

				echo "</tr>";

			}
			if($cont1 == 0)
			{
				echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecini\",\"$wfecfin\",\"$wsede\" , \"$wtiporeporte\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>No se encontraron documentos con los criterios especificados</td><tr>";

				return;
			}

			echo "<tr class='encabezadotabla'>";
			echo "<td align=center colspan=9><b>Facturas encontradas: ".$cont1."</b></td>";
			//echo "<td align=right><b>".number_format(($acumMonturas),2,'.',',')."</b></td>";
			echo "<td align=right><b>".$acumMonturas."</b></td>";
			echo "<td align=center colspan=2><b>&nbsp;</b></td>";
			//echo "<td align=right><b>".number_format(($acumLentes),2,'.',',')."</b></td>";
			echo "<td align=right><b>".$acumLentes."</b></td>";
			echo "<td align=center colspan=2><b>&nbsp;</b></td>";
			// echo "<td align=right><b>".number_format(($acumOtrosLentes),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumValorFactura),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumAbonos),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumSaldos),2,'.',',')."</b></td>";
			echo "<td align=right><b>".$acumOtrosLentes."</b></td>";
			echo "<td align=right><b>".$acumValorFactura."</b></td>";
			echo "<td align=right><b>".$acumAbonos."</b></td>";
			echo "<td align=right><b>".$acumSaldos."</b></td>";

			echo "</tr>";
			echo "</table>";
			return ;
		}
		else
		{


		}

		///////////////////////////////////////////////////////////////////////
		///////////////// CREACIÓN DE LAS TABLAS TEMPORALES ///////////////////
		///////////////////////////////////////////////////////////////////////

		// Borra la tabla temporal de facturas
		$qdel = "	DROP TABLE IF EXISTS facturas ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de facturas
		$qfac =  " CREATE TABLE IF NOT EXISTS facturas "
				." ( INDEX idxfac ( fenffa(4),fenfac(10) ), INDEX idxfec ( fenfec ), INDEX idxcod ( fencod(10) ), INDEX idxcco ( fencco(10) )   ) "
				." SELECT Fenfec, Fenffa, Fenfac, Fencod, Fenval, Fencop, Fenabo, Fenvnc, Fensal, Fenest, fencco, Fenrbo, Fendpa, Fennpa "
				." FROM ".$wbasedato."_000018 "
				." WHERE Fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
				."	 AND Fenest = 'on'";
		//echo $qfac."<br />";
		$resfac = mysql_query($qfac, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qfac . " - " . mysql_error());

		// Borra la tabla temporal de recibos
		$qdel = "	DROP TABLE IF EXISTS recibos ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de recibos
		$qfac =  " CREATE TABLE IF NOT EXISTS recibos "
				." ( INDEX idxfac ( Rdeffa(4),Rdefac(10) ), INDEX idxdoc ( Rdefue(4),Rdenum ), INDEX idxcco ( Rdecco(10) )   ) "
				." SELECT Rdefac, Rdeffa, Rdefue, Rdenum, Rdecco "
				." FROM facturas, ".$wbasedato."_000021 "
				." WHERE Rdefac = Fenfac "
				." AND Rdeffa = Fenffa";
		//echo $qfac."<br />";
		$resfac = mysql_query($qfac, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qfac . " - " . mysql_error());

		// Borra la tabla temporal de detalle de facturas por conceptos
		$qdel = "	DROP TABLE IF EXISTS detalle_concepto ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de detalle de facturas por concepto
		$qdet =  " CREATE TABLE IF NOT EXISTS detalle_concepto "
				." ( INDEX idxfac ( fdefue(4),fdedoc(10) ), INDEX idxcco ( fdecco(10) ), INDEX idxcon ( fdecon(10) ) ) "
				." SELECT Fdefue, Fdedoc, Fdecco, Fdecon, Fdevco, Fdevde, Fdeest "
				." FROM facturas, ".$wbasedato."_000065 "
				." WHERE Fenffa = Fdeffa "
				." AND Fenfac = Fdefac ";
		//echo $qdet."<br />";
		$resdet = mysql_query($qdet, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdet . " - " . mysql_error());

		// Borra la tabla temporal de detalle de facturas por conceptos
		$qdel = "	DROP TABLE IF EXISTS encabezado_venta ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de detalle de facturas por concepto
		$qven =  " CREATE TABLE IF NOT EXISTS encabezado_venta "
				." ( INDEX idxvnum ( Vennum(10) ), INDEX idxffavnfa ( Venffa(10), Vennfa(20) ) ) "
				." SELECT Venano, Venmes, Vennum, Venfec, Vencon, Vencco, Vencaj, Vencod, Vennit, Ventcl, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Venffa, Vennfa, Vennmo, Venusu, Ventve, Venmsj, Venest , Venbon				"
				." FROM facturas, ".$wbasedato."_000016 "
				." WHERE Fenffa = Venffa "
				." AND Fenfac = Vennfa "
				." AND Venest = 'on'";
		//echo $qven."<br />";
		$resven = mysql_query($qven, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qven . " - " . mysql_error());

		// Borra la tabla temporal de detalle de facturas por conceptos
		$qdel = "	DROP TABLE IF EXISTS detalle_venta ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de detalle de facturas por concepto
		$qven =  " CREATE TABLE IF NOT EXISTS detalle_venta "
				." ( INDEX idxnum ( Vdenum(10) ), INDEX idxart ( Vdeart(20) ) ) "
				." SELECT Vdenum, Vdeart, Vdevun, Vdecan, Vdepiv, Vdedes, Vdeest "
				." FROM encabezado_venta, ".$wbasedato."_000017 "
				." WHERE Vennum = Vdenum ";
		//echo $qven."<br />";
		$resven = mysql_query($qven, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qven . " - " . mysql_error());


		// Borra la tabla temporal de valor de notas de grupo
		$qdel = "	DROP TABLE IF EXISTS vnotas ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de valor de notas de grupo
		$qnot =  " 	CREATE TABLE IF NOT EXISTS vnotas "
				." 	SELECT fdecon, rdefac, rdeffa, fdevco, fdefue "
				." 	FROM recibos, ".$wbasedato."_000065 "
				." 	WHERE Rdefue = Fdefue "
				."	AND Rdenum = Fdedoc "
				."	AND Rdecco = Fdecco ";
		//echo $qnot."<br />"; Mirar
		/*
		$q = "	SELECT
				IFNULL(SUM(Fdevco),0) valor
				FROM
					".$wbasedato."_000021, ".$wbasedato."_000065
				WHERE
					Rdefue = Fdefue
					AND Rdenum = Fdedoc
					AND Rdefac = '$factura'
					AND Rdeffa = '$fuente'
					AND Rdecco = Fdecco
					AND Fdecon IN ($grupos)
				";
		*/
		$resnot = mysql_query($qnot, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qnot . " - " . mysql_error());


		// Borra la tabla temporal de ordenes si existe
		$qdel = "	DROP TABLE IF EXISTS ord ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de ordenes voy
		$qord =  " CREATE TABLE IF NOT EXISTS ord "
				." ( INDEX idxfac ( ordffa(4),ordfac(10) ) ) "
				." SELECT MIN( ordfen ) ordfen, ordfac, ordffa, ordvel, ordvem, ordlei, ordled, ordcaj "
				." FROM facturas, ".$wbasedato."_000133 "
				." WHERE Fenffa = Ordffa "
				." AND Fenfac = Ordfac "
				." GROUP BY ordfac ";
		//echo $qord."<br />";
		$resord = mysql_query($qord, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qord . " - " . mysql_error());


		// Borra la tabla temporal de ordenes facturadas si existe
		$qdel = "	DROP TABLE IF EXISTS ordfac ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de ordenes facturadas
		$qordfac =   " CREATE TABLE IF NOT EXISTS ordfac "
					." (INDEX idxffac ( Fenffa(4),Fenfac(10) ), INDEX idxffec (Fenfec) ) "
					." SELECT Ordvem, Ordvel, Ordcaj, Fenfac, Fenffa, Fencod, Fenest, Fenfec, Fendpa, Fennpa, Fenval, Fenabo, Fencop, Fenrbo, Fenvnc, Fensal, Fencco "
					." FROM facturas "
					." LEFT JOIN ord "
					." 		ON ord.ordffa = Fenffa "
					." 		AND ord.ordfac = Fenfac ";
		//echo $qordfac."<br />";
		$resordfac = mysql_query($qordfac, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qordfac . " - " . mysql_error());


		// Borra la tabla temporal de ordenes vendidas si existe
		$qdel = "	DROP TABLE IF EXISTS ordven ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de ordenes vendidas
		$qordven =   " CREATE TABLE IF NOT EXISTS ordven "
					." (INDEX idxvfac ( Venffa(4),Vennfa(10) ), INDEX idxvnum (Vennum) ) "
					." SELECT Ordvem, Ordvel, Ordcaj, Vennum, Venffa, Vennfa, Venusu, Vencaj, Vencco "
					." FROM encabezado_venta "
					." LEFT JOIN ord "
					." 		ON ord.ordffa = Venffa "
					." 		AND ord.ordfac = Vennfa ";
		//echo $qordven."<br />";
		$resordven = mysql_query($qordven, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qordven . " - " . mysql_error());

		//Necesito crear otra temporal para ordenes vendidas debido que en
		// uno de los query un UNION necesita usar 2 veces esta tabla temporal
		// Borra la tabla temporal de ordenes vendidas 2 si existe
		$qdel = "	DROP TABLE IF EXISTS ordven2 ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de ordenes vendidas
		$qordven2 =  " CREATE TABLE IF NOT EXISTS ordven2 "
					." (INDEX idxvvfac ( Venffa(4),Vennfa(10) ), INDEX idxvvnum (Vennum) ) "
					." SELECT Ordvem, Ordvel, Ordcaj, Vennum, Venffa, Vennfa, Venusu, Vencaj, Vencco "
					." FROM ordven ";
		//echo $qordven2."<br />";
		$resordven2 = mysql_query($qordven2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qordven2 . " - " . mysql_error());



		 // Borra la tabla temporal de detalle de facturas por conceptos
		$qdel = "	DROP TABLE IF EXISTS otros_conceptos ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de detalle de facturas por concepto
		$qotr =  "  CREATE TABLE IF NOT EXISTS otros_conceptos
					(INDEX idxvfac ( Fenffa(4),Fenfac(10) ) )
					SELECT
						Fenfac, Fenffa, Grucod, Grudes, Venusu, Vdeart, Vdevun, Vdecan, Vdedes, Vdepiv
					FROM
						ordfac, detalle_concepto, detalle_venta, encabezado_venta, ".$wbasedato."_000001, ".$wbasedato."_000004, ".$wbasedato."_000024
				 	WHERE Fdeest = 'on'
						AND Fdecon IN ('LC','ES','LQ','AC')
						AND Fdefue = Fenffa
						AND Fdedoc = Fenfac
						AND Fdecon = SUBSTRING_INDEX( Artgru, '-', 1 )
						AND Vdeest = 'on'
						AND Vdenum = Vennum
						AND Vdeart = Artcod
						AND Venffa = Fenffa
						AND Vennfa = Fenfac
						AND Empcod = Fencod
						AND Grucod = Fdecon
						AND Grutab = 'NO APLICA'
					ORDER BY Fenfac
				 ";
		//echo $qotr."<br />";
		$resotr = mysql_query($qotr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qotr . " - " . mysql_error());


		 // Borra la tabla temporal de detalle de facturas por conceptos
		$qdel = "	DROP TABLE IF EXISTS monturas ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de detalle de facturas por concepto
		$qmon =  "  CREATE TABLE IF NOT EXISTS monturas
					(INDEX idxvfac ( Venffa(4),Vennfa(10) ) )
					SELECT
						ordven.ordvem ordvem, Vdeart, Vdevun, Vdecan, Vdedes, Vdepiv, Venffa, Vennfa,Artnom
					FROM
						ordven, detalle_venta, ".$wbasedato."_000001
					WHERE
						Vdeart = Artcod
						AND Vennum = Vdenum
						AND SUBSTRING_INDEX( Artgru, '-', 1 ) = 'MT'
					UNION
					SELECT
						ordven2.ordvem, Vdeart, Vdevun, Vdecan, Vdedes , Vdepiv, Venffa, Vennfa,Artnom
					FROM
						ordven2, detalle_venta, ".$wbasedato."_000001
					WHERE
						Vdeart = Artcod
						AND Vennum = Vdenum
						AND Artcod = '99EXD03'
					";
		//echo $qmon."<br />";
		$resmon = mysql_query($qmon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmon . " - " . mysql_error());



		///////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////

		/* Primera parte de la consulta informacion de monturas y lentes.
		 * -->2008-11-06: Por factura es posible tener una o varias monturas, de igual forma los lentes.  Se redefine la consulta
		 * -->2008-11-11: Se puede optimizar quitando el join con la tabla de ordenes.
		 */
		$q = "SELECT
					*
				FROM
					(
					SELECT
						Fenfac Factura,
						Fenffa Fuente,
						Fenfec fecha_factura,
						IFNULL(ordfac.ordcaj,'') Caja,
						Fendpa Doc_paciente,
						Fennpa Nro_doc_paciente,
						IFNULL( ( SELECT Vmpmed FROM ".$wbasedato."_000050 WHERE Vmpvta = Vennum ), '' ) Medico,
						Grucod Codigo_grupo,
						Grudes Grupo,
				        ( CASE WHEN Grucod IN ('LO','SE','LE') THEN (SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'SE' )  ELSE '' END) valor_servicios,
				        ( CASE WHEN Grucod IN ('LO','SE','LE') THEN (SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'COP' ) ELSE '' END) valor_copagos,
				        ( CASE WHEN Grucod IN ('LO','SE','LE') THEN (SELECT IFNULL(ROUND(SUM( Fdevco-Fdevde )),0) FROM detalle_concepto WHERE Fdefue = Venffa AND Fdedoc = Vennfa AND Fdecon = 'SUB' ) ELSE '' END) valor_subsidios,
						Fenval Valor_Total_Factura,
						( Fenabo + Fencop + Fenrbo )Abonos,
						Fenvnc Nota_Credito,
						Fensal Saldo,
						Empnom Ips,
						Venfec,
						Vennum
					FROM
						ordfac, detalle_concepto, detalle_venta, encabezado_venta, ".$wbasedato."_000001, ".$wbasedato."_000004, ".$wbasedato."_000028, ".$wbasedato."_000024
					WHERE
						Fdeest = 'on'
						AND Fdecon IN (SELECT Grucod FROM uvglobal_000004 WHERE Grutab = 'NO APLICA')
						AND Fdefue = Fenffa
						AND Fdedoc = Fenfac
						AND Fdecon = SUBSTRING_INDEX( Artgru, '-', 1 )
						AND Vdeest = 'on'
						AND Vdenum = Vennum
						AND Venffa = Fenffa
						AND Vennfa = Fenfac
						AND Fencco LIKE '".$wsede."'
						AND Vdeart = Artcod
						AND Empcod = Fencod
						AND Grucod = Fdecon
						AND Grutab = 'NO APLICA'
						AND Gruest = 'on'
						AND Cajcco = Vencco
					GROUP BY 1,2
					ORDER BY Fenfac,Grucod
					) facturas
		";
		//echo '<div align=center>'.$q.'</div>';

		//LOG
  		$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Ejecutando query...*-> $q \r\n";

		$err = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($err);

		//Variables acumuladoras
		$acumMonturas = 0;
		$acumLentes = 0;
		$acumOtrosLentes = 0;
		$acumValorFactura = 0;
		$acumAbonos = 0;
		$acumSaldos = 0;
		$acumNotasLentes = 0;
		$acumNotasMonturas = 0;
		$acumNotasOtros = 0;

		if($num > 0)
		{

			//Variables de control
			$cdFactura = "";
			$cdGru = "";
			$auxNombreCco = "";
			$auxNombreGru = "";

			$cont1 = 0;

			//Botones "Retornar" y "Cerrar ventana"
			echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecini\",\"$wfecfin\",\"$wsede\" , \"$wtiporeporte\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p><br><br>";

			echo "<table border=0 align=center>";

			//Encabezados de columna
			echo "<tr class='encabezadotabla'>";
			echo "<td align=center rowspan=2><b>FACTURA</b></td>";
			echo "<td align=center rowspan=2><b>FECHA FACTURA</b></td>";
			echo "<td align=center rowspan=2><b>NUMERO VENTA</b></td>";
			echo "<td align=center rowspan=2><b>FECHA VENTA</b></td>";
			echo "<td align=center rowspan=2><b>CAJA</b></td>";
			echo "<td align=center colspan=3><b>CLIENTE</b></td>";
			echo "<td align=center rowspan=2><b>MEDICO</b></td>";
			echo "<td align=center colspan=3><b>MONTURA</b></td>";
			echo "<td align=center colspan=3><b>LENTE</b></td>";
			echo "<td align=center colspan=3><b>OTROS (LC,ES,LQ,AC)</b></td>";
			echo "<td align=center rowspan=2><b>VALOR FACTURA</b></td>";
			echo "<td align=center rowspan=2><b>ABONOS</b></td>";
			echo "<td align=center colspan=3><b>NOTAS CREDITO</b></td>";
			echo "<td align=center rowspan=2><b>SALDO</b></td>";
			echo "</tr>";
			echo "<tr class='encabezadotabla'>";
			echo "<td align=center><b>DOCUMENTO</b></td>";
			echo "<td align=center><b>NOMBRE</b></td>";
			echo "<td align=center><b>IPS</b></td>";
			echo "<td align=center><b>VENDEDOR MONTURA</b></td>";
			echo "<td align=center><b>CODIGO MONTURA</b></td>";
			echo "<td align=center><b>VALOR</b></td>";
			echo "<td align=center><b>VENDEDOR LENTE</b></td>";
			echo "<td align=center><b>CODIGO LENTE</b></td>";
			echo "<td align=center><b>VALOR</b></td>";
			echo "<td align=center><b>CODIGO</b></td>";
			echo "<td align=center><b>VENDEDOR</b></td>";
			echo "<td align=center><b>VALOR</b></td>";
			echo "<td align=center><b>LENTE</b></td>";
			echo "<td align=center><b>MONTURA</b></td>";
			echo "<td align=center><b>OTROS</b></td>";
			echo "</tr>";

			$salto = false;
			while($cont1 < $num){

				$cont1 % 2 == 0 ? $class = 'fila1' : $class = 'fila2';

				$facturaActual = mysql_fetch_array($err);

				$campo1 = $facturaActual['Factura'];
				$campo2 = $facturaActual['Caja'];
				$campo3 = $facturaActual['Doc_paciente'];
				$campo4 = $facturaActual['Nro_doc_paciente'];
				$campo5 = $facturaActual['Ips'];
				$campo6 = $facturaActual['Medico'];

				$campo7 = "";
				$campo8 = "";
				$campo9 = "";

				$campo10 = "";
				$campo11 = "";
				$campo12 = "";

				//Valor factura, abonos, saldo
				$campo13 = $facturaActual['Valor_Total_Factura'];
				$campo14 = $facturaActual['Abonos'];
				$campo15 = $facturaActual['Saldo'];
				$campo16 = $facturaActual['fecha_factura'];

				/*Campos nuevos num venta y fecha venta*/
				$campo17 = $facturaActual['Venfec'];
				$campo18 = $facturaActual['Vennum'];

  				$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Factura $cont1 Consultando otros conceptos...*-> \r\n";
				$otrosConceptos = consultarOtrosConceptos($conex, $facturaActual['Factura'], $facturaActual['Fuente']);

				$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consultando monturas asociadas...*->\r\n";
				$monturas = consultarMonturasAsociadas($conex, $facturaActual['Factura'], $facturaActual['Fuente']);

				$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consultando lentes asociados...*->\r\n";
				$lentes = consultarLentesAsociados($conex, $facturaActual['Factura'], $facturaActual['Fuente']);

				$cantidadOtros = count($otrosConceptos);
				$cantidadMonturas = count($monturas);

				if($cantidadOtros == 0){
					$cantidadOtros++;
				}

				if($cantidadMonturas == 0){
					$cantidadMonturas++;
				}

				$cantidadFilas = $cantidadOtros;
				//$cantidadOtros > $cantidadMonturas ? $cantidadFilas = $cantidadOtros : $cantidadFilas = $cantidadMonturas;

				//echo $cantidadOtros;
				//echo $cantidadMonturas;

				//Acumuladores
				$campo12 = ( trim($campo12)  == "" ) ? 0 : $campo12*1;
				$acumLentes += $campo12;
				$acumValorFactura += $campo13;
				$acumAbonos += $campo14;
				$acumSaldos += $campo15;

				//Poblar celdas
				echo "<tr class=".$class.">";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo1."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo16."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo18."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo17."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo2."</td>";

				$campo4 = '';
				$qnombre = "SELECT Clinom FROM ".$wbasedato."_000041 WHERE Clidoc = '".$campo3."' ";
				$err3 = mysql_query($qnombre,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$nombreactual ='';
				$nombreactual = mysql_fetch_array($err3);
				$campo4 = $nombreactual['Clinom'] ;


				echo "<td rowspan='$cantidadFilas' align=left>".$campo3."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo4."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo5."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo6."</td>";

				foreach ($monturas as $montura){
					$campo7 .= $montura->campo1."<br>";
					$campo8 .= $montura->campo2."<br>";
					// $campo9 .= number_format(($montura->campo3),2,'.',',')."<br>";
					$campo9 .= $montura->campo3."<br>";

					$acumMonturas += $montura->campo3;
				}

				echo "<td rowspan='$cantidadFilas' align=left>".$campo7."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo8."</td>";
				echo "<td rowspan='$cantidadFilas' align=right>".$campo9."</td>";

				foreach ($lentes as $lente){
					$campo10 .= $lente->campo1."<br>";
					$campo11 .= $lente->campo2."<br>";
					// $campo12 .= number_format(($lente->campo3),2,'.',',')."<br>";
					$campo12 .= $lente->campo3."<br>";

					$acumLentes += $lente->campo3;
				}

				echo "<td rowspan='$cantidadFilas' align=left>".$campo10."</td>";
				echo "<td rowspan='$cantidadFilas' align=left>".$campo11."</td>";
				echo "<td rowspan='$cantidadFilas' align=right>".$campo12."</td>";

				//Otros
				$contCptos = 0;

				foreach ($otrosConceptos as $concepto){
					if($contCptos > 0){
						echo "<tr>";
					}
					echo "<td align=left>".$concepto->campo1." - ".$concepto->campo4."</td>";
					echo "<td align=left>".$concepto->campo2."</td>";
					//echo "<td align=right>".number_format(($concepto->campo3),2,'.',',')."</td>";
					echo "<td align=right>".$concepto->campo3."</td>";

					$acumOtrosLentes += $concepto->campo3;

					if($contCptos > 0){
						echo "</tr>";
					} else {
						// echo "<td rowspan='$cantidadFilas' align=right>".number_format(($campo13),2,'.',',')."</td>";
						// echo "<td rowspan='$cantidadFilas' align=right>".number_format(($campo14),2,'.',',')."</td>";
						echo "<td rowspan='$cantidadFilas' align=right>".$campo13."</td>";
						echo "<td rowspan='$cantidadFilas' align=right>".$campo14."</td>";

						//Notas
						$valorNotasLentes = consultarValorNotasGrupo($conex, $facturaActual['Factura'], $facturaActual['Fuente'], "'LO','LE'");
						if($valorNotasLentes > 0){
							$valorNotasLentes = $valorNotasLentes + $facturaActual['valor_servicios'] + $facturaActual['valor_copagos'] + $facturaActual['valor_subsidios'];
						}
						$acumNotasLentes += $valorNotasLentes;
						$valorNotasMonturas = consultarValorNotasGrupo($conex, $facturaActual['Factura'], $facturaActual['Fuente'], "'MT'");
						$acumNotasMonturas += $valorNotasMonturas;
						$valorNotasOtros = consultarValorNotasGrupo($conex, $facturaActual['Factura'], $facturaActual['Fuente'], "'LC','ES','LQ','AC' \r\n  \r\n");
						$acumNotasOtros += $valorNotasOtros;

						// echo "<td rowspan='$cantidadFilas' align=right>".number_format(($valorNotasLentes),2,'.',',')."</td>";
						// echo "<td rowspan='$cantidadFilas' align=right>".number_format(($valorNotasMonturas),2,'.',',')."</td>";
						// echo "<td rowspan='$cantidadFilas' align=right>".number_format(($valorNotasOtros),2,'.',',')."</td>";
						echo "<td rowspan='$cantidadFilas' align=right>".$valorNotasLentes."</td>";
						echo "<td rowspan='$cantidadFilas' align=right>".$valorNotasMonturas."</td>";
						echo "<td rowspan='$cantidadFilas' align=right>".$valorNotasOtros."</td>";

						echo "<td rowspan='$cantidadFilas' align=left>".$campo15."</td>";
					}
					$contCptos++;
				}

				if($contCptos == 0){
					//Notas
					$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consultando valor notas grupo 'LO','LE'...*->\r\n";
					$valorNotasLentes = consultarValorNotasGrupo($conex, $facturaActual['Factura'], $facturaActual['Fuente'], "'LO','LE'");
					if($valorNotasLentes > 0){
						$valorNotasLentes = $valorNotasLentes + $facturaActual['valor_servicios'] + $facturaActual['valor_copagos'] + $facturaActual['valor_subsidios'];
					}
					$acumNotasLentes += $valorNotasLentes;
					$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consultando valor notas grupo 'MT'...*->\r\n";
					$valorNotasMonturas = consultarValorNotasGrupo($conex, $facturaActual['Factura'], $facturaActual['Fuente'], "'MT'");
					$acumNotasMonturas += $valorNotasMonturas;
					$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Consultando valor notas grupo 'LC','ES','LQ','AC'...*->\r\n";
					$valorNotasOtros = consultarValorNotasGrupo($conex, $facturaActual['Factura'], $facturaActual['Fuente'], "'LC','ES','LQ','AC'");
					$acumNotasOtros += $valorNotasOtros;

//					$acumOtrosLentes = 0;

					echo "<td rowspan='$cantidadFilas' align=left>&nbsp;</td>";
					echo "<td rowspan='$cantidadFilas' align=left>&nbsp;</td>";
					echo "<td rowspan='$cantidadFilas' align=left>0</td>";

					echo "<td rowspan='$cantidadFilas' align=left>".$campo13."</td>";
					echo "<td rowspan='$cantidadFilas' align=left>".$campo14."</td>";

					//Notas
					// echo "<td rowspan='$cantidadFilas' align=right>".number_format(($valorNotasLentes),2,'.',',')."</td>";
					// echo "<td rowspan='$cantidadFilas' align=right>".number_format(($valorNotasMonturas),2,'.',',')."</td>";
					// echo "<td rowspan='$cantidadFilas' align=right>".number_format(($valorNotasOtros),2,'.',',')."</td>";
					echo "<td rowspan='$cantidadFilas' align=right>".$valorNotasLentes."</td>";
					echo "<td rowspan='$cantidadFilas' align=right>".$valorNotasMonturas."</td>";
					echo "<td rowspan='$cantidadFilas' align=right>".$valorNotasOtros."</td>";

					echo "<td rowspan='$cantidadFilas' align=right>".$campo15."</td>";
					echo "</tr>";
				}

				$cont1++;
			}

			echo "<tr class='encabezadotabla'>";
			echo "<td align=center colspan=11><b>Facturas encontradas: ".$cont1."</b></td>";
			//echo "<td align=right><b>".number_format(($acumMonturas),2,'.',',')."</b></td>";
			echo "<td align=right><b>".$acumMonturas."</b></td>";
			echo "<td align=center colspan=2><b>&nbsp;</b></td>";
			// echo "<td align=right><b>".number_format(($acumLentes),2,'.',',')."</b></td>";
			echo "<td align=right><b>".$acumLentes."</b></td>";
			echo "<td align=center colspan=2><b>&nbsp;</b></td>";
			// echo "<td align=right><b>".number_format(($acumOtrosLentes),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumValorFactura),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumAbonos),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumNotasLentes),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumNotasMonturas),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumNotasOtros),2,'.',',')."</b></td>";
			// echo "<td align=right><b>".number_format(($acumSaldos),2,'.',',')."</b></td>";
			echo "<td align=right><b>".$acumOtrosLentes."</b></td>";
			echo "<td align=right><b>".$acumValorFactura."</b></td>";
			echo "<td align=right><b>".$acumAbonos."</b></td>";
			echo "<td align=right><b>".$acumNotasLentes."</b></td>";
			echo "<td align=right><b>".$acumNotasMonturas."</b></td>";
			echo "<td align=right><b>".$acumNotasOtros."</b></td>";
			echo "<td align=right><b>".$acumSaldos."</b></td>";

			echo "</tr>";
			echo "</table>";

			//Botones "Retornar" y "Cerrar ventana"
			echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecini\",\"$wfecfin\",\"$wsede\" , \"$wtiporeporte\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";
		} else {
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>No se encontraron documentos con los criterios especificados</td><tr>";
		}

		$contenidoLog = $contenidoLog.date(" (Y-m-d")."-".date("H:i:s) ").": Fin de ejecución...*->\r\n";

		//Msanchez:**************GRABA LOG**************
		if($debug)
		{
			if($archivo)
			{
				// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
				if (is_writable($nombreArchivo))
				{
					// Escribir $contenido a nuestro arcivo abierto.
					fwrite($archivo, $contenidoLog);
					fclose($archivo);
				}
			}
		}
	    //Msanchez::***************FIN GRABA LOG*************
  	}
}

// LIBERAR TABLAS TEMPORALES

// Borra la tabla temporal de valor de notas de grupo
$qdel = "DROP TABLE IF EXISTS vnotas ";
$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

// Borra la tabla temporal de ordenes si existe
$qdel = "DROP TABLE IF EXISTS ord ";
$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

// Borra la tabla temporal de ordenes facturadas si existe
$qdel = "DROP TABLE IF EXISTS ordfac ";
$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

// Borra la tabla temporal de ordenes vendidas si existe
$qdel = "DROP TABLE IF EXISTS ordven ";
$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

// Borra la tabla temporal de ordenes vendidas 2 si existe
$qdel = "DROP TABLE IF EXISTS ordven2 ";
$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

liberarConexionBD($conex);
?>
</html>
