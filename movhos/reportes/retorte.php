<html>
<head>
<title>Reporte</title>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#006699;background:#FFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup2{color:#4DBECB;background:#FFFFF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;text-align:center;}
    	<!-- -->
    	<!--.titulo2{color:#003366;background:#57C8D5;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}-->
    	.titulo2{color:#003366;background:#4DBECB;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#0A3D6F;background:#61D2DF;font-size:11pt;font-family:Arial;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:10pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#006699;background:#CCFFFF;font-size:11pt;font-family:Tahoma;font-weight:bold;}
    	.texto3{color:#FFFFF;background:red;font-size:10pt;font-family:Tahoma;text-align:center;}
    	<!-- .acumulado1{color:#003366;background:#FFCC66;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	-->
    	.errorTitulo{color:#FF0000;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}

    	.alert{background:#FFFFAA;color:#FF9900;font-size:10pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:10pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:10pt;font-family:Arial;text-align:center;}

    	.tituloA1{color:#FFFFFF;background:#660099;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#660066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;text-align:center;}

    </style>
</head>
<body>
<?php
include_once("conex.php");
/**
* 
* @wvar $pac
* 				[nom]: Nom
* 				[act]: El paciente esta ctivo en UNIX, es decir fue encontrado en la tabla inpac.
* 				[alt]:Boolean. El usuario quiere hacerle el alta definitiva al paciente.<br>
* 				[permisoAlta]:Boolean. Todos los registros activos del apciente estan en cargados a la cuanta, se puede efectuar el alta.<br>
* 				[]:<br>
* @wvar $ok	Determina si se deben o no buscar registros procesados del paciente. Se da si el paciente esta activo en UNIX o se encontro su información en MATRIX.
* @wvar $alta  false:debe preguntar al usuario si desea dar un alta.<br>
* 				cco:el usuario eligio hacer un alta del paciente a un centro de costos.<br>
* 				def: el usuario eligio hacer el alta definitiva de la institucicón.<br>
*/
/**
* Aqui se van a hacer las altas parciales, es decir a los centros de costos y las altas totales, de salida de la institución.
* Solo las altas totales hacen que los registros del día queden en estado procesado. Así mismo cuando se quita un alta total todos los registros del día deben
* pasar a estado transición, por eso se debe correr la función actualizacionDetalleRegistros con estado sin alta .
* 
* PARA LA PRÓXIMA VERSIÓN ESTO NO DEBE SER ASÍ.

ACTUALIZACION
 Agosto 05 de 2020	(Edwin MG)
			Se muestra el o los números de orden de control para aquellos medicamentos de control cargados a pacientes
 Noviembre 15 de 2013 (Jonatan Lopez)		
			Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 
 			para que traiga los datos de contingencia (tabla movhos_00143) con estado activo.


*/

function consultarNumeroOrdenControl( $conex, $wmovhos, $his, $ing, $fecha, $art ){

	$val = [];
	
	$sql = "SELECT Ctrcon
			  FROM ".$wmovhos."_000133 a
			 WHERE Ctrhis = '".$his."'
			   AND Ctring = '".$ing."' 
			   AND Ctrfge = '".$fecha."' 
			   AND Ctrart = '".$art."' 
			   AND Ctrest = 'on' 
			 ";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $rows = mysql_fetch_array( $res ) ){
		$val[] = $rows['Ctrcon'];
	}
	
	return $val;
}

function pintarNombres($unix)
{
	echo "<tr>";
	echo "<td class='titulo1'>Fecha</td>";
	echo "<td class='titulo1'>Hora</td>";
	echo "<td class='titulo1'>Historia</td>";
	echo "<td class='titulo1'>Ingreso</td>";
	echo "<td class='titulo1'>Doc. UNIX</td>";
	echo "<td class='titulo1'>Doc. Matrix</td>";
	echo "<td class='titulo1'>Linea</td>";
	echo "<td class='titulo1'>Articulo</td>";
	echo "<td class='titulo1'>Articulo</td>";
	echo "<td class='titulo1'>Cantidad Matrix</td>";
	echo "<td class='titulo1'>Nro de Orden<br>de control</td>";
	if ($unix)
	{
		echo "<td class='titulo1'>Cantidad Integrador</td>";
		echo "<td class='titulo1'>Cantidad Unix</td>";
	}
	echo "<td class='titulo1'>Responsable</td>";
	echo "<td class='titulo1'>Estado</td>";

	echo "</tr>";
}

function Comparar($vector1, $err)
{
	if ($vector1[4] > odbc_result($err, 4))
	{
		return -1;
	}
	else if ($vector1[4] < odbc_result($err, 4))
	{
		return 1;
	}
	else
	{
		if ($vector1[3] > odbc_result($err, 3))
		{
			return -1;
		}
		else if ($vector1[3] < odbc_result($err, 3))
		{
			return 1;
		}
		else
		{
			if ($vector1[7] > odbc_result($err, 7))
			{
				return -1;
			}
			else if ($vector1[7] < odbc_result($err, 7))
			{
				return 1;
			}
			else
			{
				if ($vector1[10] > odbc_result($err, 10))
				{
					return -1;
				}
				else if ($vector1[10] < odbc_result($err, 10))
				{
					return 1;
				}
				else
				{
					if ($vector1[9] > odbc_result($err, 9))
					{
						return -1;
					}
					else if ($vector1[9] < odbc_result($err, 9))
					{
						return 1;
					}
					else
					{
						return 0;
					}
				}
			}
		}
	}
}

function bi($matriz, $tamano, $err)
{
	if ($tamano > 0)
	{
		$li = 1;
		$ls = $tamano;
		while ($ls - $li > 1)
		{
			$lm = (integer)(($li + $ls) / 2);
			$res = Comparar($matriz[$lm], $err);
			// echo 'comparar'.$res.'fin';
			if ($res == 0)
			{
				$matriz[$lm][13] = $matriz[$lm][13] + odbc_result($err, 13);
				$matriz[$lm][14] = $matriz[$lm][14] + odbc_result($err, 14);
				// echo count($matriz).'a';
				return $matriz;
			} elseif ($res < 0)
			{
				$ls = $lm;
			}
			else
			{
				$li = $lm;
			}
		}

		if (Comparar($matriz[$li], $err) == 0)
		{
			$matriz[$li][13] = $matriz[$li][13] + odbc_result($err, 13);
			$matriz[$li][14] = $matriz[$li][14] + odbc_result($err, 14);
			return $matriz;
		} elseif (Comparar($matriz[$ls], $err) == 0)
		{
			$matriz[$ls][13] = $matriz[$ls][13] + odbc_result($err, 13);
			$matriz[$ls][14] = $matriz[$ls][14] + odbc_result($err, 14);
			return $matriz;
		}
		else
		{
			// echo '*'.$li.'*';
			if (Comparar($matriz[$li], $err) < 0)
			{
				// echo 'menor';
				$matriz = incluir($li, $matriz, $err);
				return $matriz;
			}
			else
			{
				// echo 'mayor';
				$res = Comparar($matriz[$ls], $err);
				if ($res < 0)
				{
					$matriz = incluir($li + 1, $matriz, $err);
					return $matriz;
				}
				else
				{
					$matriz = incluir($ls + 1, $matriz, $err);
					return $matriz;
				}
			}
		}
	}
	else
	{
		$res = Comparar($matriz[1], $err);
		if ($res == 0)
		{
			$matriz[1][13] = $matriz[1][13] + odbc_result($err, 13);
			$matriz[1][14] = $matriz[1][14] + odbc_result($err, 14);
			return $matriz;
		}
		else if ($res < 0)
		{
			$matriz = incluir(1, $matriz, $err);
			return $matriz;
		}
		else
		{
			$matriz = incluir(2, $matriz, $err);
			return $matriz;
		}
	}
}

function incluir($posicion, $matriz, $err)
{
	for ($i = count($matriz); $i >= $posicion; $i--)
	{
		$matriz[$i + 1] = $matriz[$i];
	}

	$matriz[$posicion][1] = odbc_result($err, 1);
	$matriz[$posicion][2] = odbc_result($err, 2);
	$matriz[$posicion][3] = odbc_result($err, 3);
	$matriz[$posicion][4] = odbc_result($err, 4);
	$matriz[$posicion][5] = odbc_result($err, 5);
	$matriz[$posicion][6] = odbc_result($err, 6);
	$matriz[$posicion][7] = odbc_result($err, 7);
	$matriz[$posicion][8] = odbc_result($err, 8);
	$matriz[$posicion][9] = odbc_result($err, 9);
	$matriz[$posicion][10] = odbc_result($err, 10);
	$matriz[$posicion][11] = odbc_result($err, 11);
	$matriz[$posicion][12] = odbc_result($err, 12);
	$matriz[$posicion][13] = odbc_result($err, 13);
	$matriz[$posicion][14] = odbc_result($err, 14);
	$matriz[$posicion][15] = odbc_result($err, 15);
	$matriz[$posicion][16] = odbc_result($err, 16);

	return $matriz;
}

session_start();
if (!isset($_SESSION['user']))
echo "error";
else
{

	

	


	echo "</br></br><table align='center' border='0'>";
	echo "<tr><td align=center class='tituloSup' colspan='2'><b>REPORTE</b></td></tr>";
	echo "<tr><td align=center class='tituloSup1' colspan='2'>retorte.php Versión Noviembre 15 de 2013<br><br></td></tr>";
	echo "<tr>";

	if (!isset($fec1))
	{
		/**
        * Pide la historia y el ingreso
        */
		echo "<form action='' method='POST'>";
		echo "<td align='center'><br><table border=0 width=400>";
		echo "<tr>";
		echo "<td class='texto2'><input type='checkbox' name='chkHis'></td>";
		echo "<td class='texto2' ><b>Historia: </b><input type='text' size='10' name='his'> &nbsp; ";
		echo "Ingreso: </b><input type='text' size='4' name='ing'></td></tr>";
		echo "<td class='texto2'><input type='checkbox' name='chkCco'></td>";
		echo "<td class='texto2' ><b>Cco: </b><input type='text' size='4' name='cco'></td></tr>";
		echo "<td class='texto2'><input type='checkbox' name='chkCod'></td>";
		echo "<td class='texto2' ><b>Codigo de nomina: </b><input type='text' size='4' name='cod'></td></tr>";
		echo "<td class='texto2'><input type='checkbox' name='chkArt'></td>";
		echo "<td class='texto2' ><b>Artículo: </b><input type='text' size='10' name='art'></td></tr>";
		echo "<td class='texto2'></td>";
		echo "<td class='texto2' ><b>Estado: </b>";
		echo "<select name='inc'>";
		echo "<option selected value='t'>Todos</option>";
		echo "<option value='on'>Activos</option>";
		echo "<option value='off'>Inactivos</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "<td class='texto2'></td>";
		echo "<td class='texto2'><b>Fechas: </b><input type='text' size='10' name='fec1' value='" . date("Y-m") . "-01'>&nbsp;<input type='text' size='10' name='fec2' value='" . date("Y-m-d") . "'></td></tr>";

		include_once("movhos/otros.php");
		connectOdbc($conex_o, 'facturacion');

		if ($conex_o != 0)
		{
			//echo "<td class='texto2' colspan='2' align='center'><input type='checkbox' name='chkUni'>Consultar cargos en Unix</td>";
		}
		echo"<tr><td   class='texto2' colspan='2' align='center'><input type='submit' value='ACEPTAR'></td></tr></form>";
		echo "</form>";
	}
	else
	{
		include_once("movhos/otros.php");
		connectOdbc($conex_o, 'facturacion');

		$enc = false;
		$det = false;
		$fen = "";
		$fde = "";
		$fit = "";
		$fdeit = "";
		$fiv = "";
		$fdeiv = "";
		$fec3 = str_replace('-', '/', $fec1);
		$fec4 = str_replace('-', '/', $fec2);
		if (isset($chkHis))
		{
			$fen = " Fenhis = '" . $his . "' and Fening = '" . $ing . "' and";
			$fit = " Drohis = '" . $his . "' and";
			$fiv = " Drohis = '" . $his . "' and";
			$enc = true;
		}

		if (isset($chkCco))
		{
			$fen = $fen . " Fencco = '" . $cco . "' and";
			$fit = $fit . " Droser = '" . $cco . "' and";
			$fiv = $fiv . " Drocco = '" . $cco . "' and";
			$enc = true;
		}
		
		if (isset($chkCod))
		{		
			$fde_seguridad = "        AND " . $bd . "_000003.Seguridad = 'A-". $cod . "' ";	
			$fde_seguridad_contingencia = "        AND " . $bd . "_000143.Seguridad = 'A-". $cod . "' "; //Se agrega esta variable para controlar el campo de seguridad en la tabla de contingencia. 13 nov jonatan
		}
		
		//Esta validacion se pasa para este lugar para permitir que la variable $fde_art se utilice en el union de la consulta que continua.
		$art=strtoupper($art);
		if (isset($chkArt))
		{
			//busco si el articulo existe en la 26 o si no es de la central
			$central=" SELECT Artcom "
				."       FROM " . $bd . "_000026 "
				."      WHERE Artcod = '" . $art . "' "
				."        AND Artest = 'on' ";
			$errcen = mysql_query($central, $conex);
			$central = mysql_num_rows($errcen);

			if($central>0)
			{
				$fde_art = 		"        AND Fdeart = '" . $art . "' ";				
			}
			else
			{
				$fde_art = 		"        AND Fdeari = '" . $art . "' ";				
			}
			$fdeit = $fdeit . "        AND Droart = '" . $art . "' ";
			$fdeiv = $fdeiv . "        AND Drodetart = '" . $art . "' ";
		}
		
		if ($enc)
		{
		
			// Se organiza query en Matrix para los cargos
			$fde = "SELECT " . $bd . "_000003.Fecha_data as fec, " . $bd . "_000003.Hora_data as hor, Fenfue, Fencco, cast(Fenhis as char), Fening, Fennum, Fendoc, Fdelin, Fdeart, Fdeari, Fdecan, 0 as drocan, 0 as drodetcan, Fdeest, " . $bd . "_000003.Seguridad as res, Fenfec "
			. "         FROM " . $bd . "_000003, " . $bd . "_000002 "
			. "      WHERE " . SUBSTR($fen, 0, (strlen($fen)-3)) . " "
			. "        AND " . $bd . "_000002.Fecha_data between '" . $fec1 . "' AND '" . $fec2 . "' "
			. "        AND Fdenum = Fennum "
			. "	$fde_seguridad " //Esta variable controla la consulta por codigo de matrix para esta parte de la consulta.
			. " $fde_art "	//Esta variable controla el filtro por codigo del articulo	    
			. " UNION " //Se agrega este "UNION" para traiga los datos de contingencia (tabla movhos_00143). Nov 13 de 2013 Jonatan Lopez
			. " SELECT " . $bd . "_000143.Fecha_data as fec, " . $bd . "_000143.Hora_data as hor, Fenfue, Fencco, cast(Fenhis as char), Fening, Fennum, Fendoc, Fdelin, Fdeart, Fdeari, Fdecan, 0 as drocan, 0 as drodetcan, Fdeest, " . $bd . "_000143.Seguridad as res, Fenfec "
			. "         FROM " . $bd . "_000143, " . $bd . "_000002 "
			. "      WHERE " . SUBSTR($fen, 0, (strlen($fen)-3)) . " "
			. "        AND " . $bd . "_000002.Fecha_data between '" . $fec1 . "' AND '" . $fec2 . "' "
			. "        AND Fdenum = Fennum "
			. "        AND Fdeest = 'on'"
			. "		$fde_seguridad_contingencia" //Esta variable controla la consulta por codigo de matrix para esta otra parte de la consulta.
			. "     $fde_art "; //Esta variable controla el filtro por codigo del articulo
			
			// se organiza query en matrix para itdro
			$fdeit = "SELECT drofec, '' as drohor, drofue, droser, drohis, '' as droing, dronum, '' as drodoc, drolin,   droart, '' as droari, 0 as Fdecan, drocan, 0 as drodetcan, droest, '' as drores "
			. "        	FROM itdro "
			. "  	WHERE " . SUBSTR($fit, 0, (strlen($fit)-3)) . " "
			. "			AND drofec between '" . $fec3 . "' AND '" . $fec4 . "' ";
			// se organiza query en matrix para ivdro
			$fdeiv = "SELECT drofec, '' as drohor, drofue, drocco, drohis, '' as droing, drodocnum, drodoc, drodetite,   drodetart, '' as droari, 0 as Fdcan, 0 as drocan, drodetcan, 'on' as droest, '' as drores "
			. "        	FROM ivdro, ivdrodet, OUTER Itdrodoc "
			. "  	WHERE " . SUBSTR($fiv, 0, (strlen($fiv)-3)) . " "
			. "			AND drofec between '" . $fec3 . "' AND '" . $fec4 . "' "
			. "			AND drodoc=drodetdoc "
			. "			AND drofue=drodetfue "
			. "			AND drodocdoc=drodoc "
			. "			AND drodocfue=drofue ";
		}
		else
		{
			// Se organiza query en Matrix para los cargos
			$fde = "SELECT " . $bd . "_000003.Fecha_data as fec, " . $bd . "_000003.Hora_data as hor, Fenfue, Fencco, Fenhis, Fening, Fennum, Fendoc, Fdelin, Fdeart, Fdeari, Fdecan, 0 as drocan, 0 as drodetcan, Fdeest, " . $bd . "_000003.Seguridad as res, Fenfec "
			. "        FROM " . $bd . "_000002, " . $bd . "_000003 "
			. "       WHERE " . $bd . "_000002.Fecha_data between '" . $fec1 . "' AND '" . $fec2 . "' "
			. "         AND Fdenum = Fennum "
			. "	            $fde_seguridad " //Esta variable controla la consulta por codigo de matrix para esta parte de la consulta.
			. "     $fde_art" //Esta variable controla el filtro por codigo del articulo
			. "       UNION " //Se agrega este "UNION" para traiga los datos de contingencia (tabla movhos_00143). Nov 13 de 2013 Jonatan Lopez
			. "      SELECT " . $bd . "_000143.Fecha_data as fec, " . $bd . "_000143.Hora_data as hor, Fenfue, Fencco, Fenhis, Fening, Fennum, Fendoc, Fdelin, Fdeart, Fdeari, Fdecan, 0 as drocan, 0 as drodetcan, Fdeest, " . $bd . "_000143.Seguridad as res, Fenfec "
			. "        FROM " . $bd . "_000002, " . $bd . "_000143 "
			. "       WHERE " . $bd . "_000002.Fecha_data between '" . $fec1 . "' AND '" . $fec2 . "' "
			. "         AND Fdenum = Fennum "
			. "         AND Fdeest = 'on'"
			. " $fde_seguridad_contingencia" //Esta variable controla la consulta por codigo de matrix para esta otra parte de la consulta.
			. " $fde_art "; //Esta variable controla el filtro por codigo del articulo
			// se organiza query en matrix para itdro
			$fdeit = "SELECT drofec, '' as drohor, drofue, droser, drohis, '' as droing, dronum, '' as drodoc, drolin,   droart, '' as droari, 0 as Fdecan, drocan, 0 as drodetcan, droest, '' as drores "
			. "        	FROM itdro "
			. "  	WHERE drofec between '" . $fec3 . "' AND '" . $fec4 . "' ";
			// se organiza query en matrix para ivdro
			$fdeiv = "SELECT drofec, '' as drohor, drofue, drocco, drohis, '' as droing, drodocnum, drodoc, drodetite,   drodetart, '' as droari, 0 as Fdcan, 0 as drocan, drodetcan, 'on' as droest, '' as drores "
			. "        	FROM ivdro, ivdrodet, OUTER Itdrodoc "
			. "  	WHERE drofec between '" . $fec3 . "' AND '" . $fec4 . "' "
			. "			AND drodoc=drodetdoc "
			. "			AND drofue=drodetfue "
			. "			AND drodocdoc=drodoc "
			. "			AND drodocfue=drofue ";
		}
				

		if ($inc != "t")
		{
			$fde = $fde . "        AND Fdeest = '" . $inc . "' ";
		}

		if (isset($chkUni))
		{
			$colspan = 15;
		}
		else
		{
			$colspan = 13;
		}

		if (isset($chkUni))
		{
			$fde = $fde . "    ORDER BY  Fencco, Fenfue,  Fenfec, Fennum, Fdeart, Fdelin ";
		}
		else
		{
			$fde = $fde . "    ORDER BY  Fencco, Fenfue,  Fenfec, Fennum, Fdelin, Fdeart ";
		}

		$err = mysql_query($fde, $conex);
		$num = mysql_num_rows($err);

		if (isset($chkUni))
		{
			$fdeit = $fdeit . "    	ORDER BY droser, drofue, drofec, dronum, droart, drolin ";

			$err_o1 = odbc_do($conex_o, $fdeit);

			$fdeiv = $fdeiv . "    	ORDER BY drocco, drofue, drofec, drodoc, drodetart, drodetite";

			$err_o2 = odbc_do($conex_o, $fdeiv);
		}

		if ($num > 0)
		{
			echo "<td class='tituloSup2'>Se encontraron " . $num . " registros en Matrix. </td></tr>";
		}
		// los tres vectores deben integrarse en uno sola organizado
		// primero organizo el vector es decir recorro el resultado metiendolo en un vector
		for($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($err);

			$vector[$i][1] = $row[0];
			$vector[$i][2] = $row[1];
			$vector[$i][3] = $row[2];
			$vector[$i][4] = $row[3];
			$vector[$i][5] = $row[4];
			$vector[$i][6] = $row[5];
			$vector[$i][7] = $row[6];
			$vector[$i][8] = $row[7];
			$vector[$i][9] = $row[8];
			$vector[$i][10] = $row[9];
			$vector[$i][11] = $row[10];
			$vector[$i][12] = $row[11];
			$vector[$i][13] = $row[12];
			$vector[$i][14] = $row[13];
			$vector[$i][15] = $row[14];
			$vector[$i][16] = $row[15];
		}

		if (isset($chkUni))
		{
			$x = 0;
			$y = 0;
			// luego recorro los resultados de itdro para meterlos en el vector
			while (odbc_fetch_row($err_o1))
			{
				$vector = bi($vector, count($vector), $err_o1);
				// echo count($vector).'b';
				$x++;
			}
			// luego recorro los resultados de facturacion para meterlos en el vector
			while (odbc_fetch_row($err_o2))
			{
				$vector = bi($vector, count($vector), $err_o2);
				$y++;
			}

			echo "<td class='tituloSup2'>Se encontraron " . $x . " registros en Unix-integrador. </td></tr>";

			echo "<td class='tituloSup2'>Se encontraron " . $y . " registros en Unix-facturación. </td></tr>";
		}

		if (isset ($vector) and count($vector) > 0)
		{
			echo "<tr><td align='center'><br><table border=0 >";
			$fueant = '';
			$ccoAnt = '';

			for($i = 1;$i <= count($vector);$i++)
			{
				$consControl = consultarNumeroOrdenControl( $conex, $bd, $vector[$i][5], $vector[$i][6], $vector[$i][1], $vector[$i][11] );

				if (isset($chkUni) and ($vector[$i][12] != $vector[$i][13] or $vector[$i][13] != $vector[$i][14]))
				{
					$class = "texto3";
				}
				else
				{
					if ($i % 2 == 0)
					{
						$class = "texto";
					}
					else
					{
						$class = "texto1";
					}
				}

				if ($ccoAnt != $vector[$i][4])
				{
					echo "<tr><td colspan='" . $colspan . "'>&nbsp;</td></tr>";
					echo "<tr><td bgcolor='#f5f5dc' colspan='" . $colspan . "'><font color='#006699'><B>Centro de costos:</B> " . $vector[$i][4] . "</font></td></tr>";
					echo "<tr><td bgcolor='#FFDBA8' colspan='" . $colspan . "'><font color='#006699'><B>Fuente:</B> " . $vector[$i][3] . "</font></td></tr>";
					$ccoAnt = $vector[$i][4];
					$fueAnt = $vector[$i][3];
					pintarNombres(isset($chkUni));
				}
				else if ($fueAnt != $vector[$i][3])
				{
					echo "<tr><td bgcolor='#FFDBA8' colspan='" . $colspan . "'><font color='#006699'><B>Fuente:</B> " . $vector[$i][3] . "</font></td></tr>";
					$fueAnt = $vector[$i][3];
					pintarNombres(isset($chkUni));
				}

				echo "<tr>";
				echo "<td class='" . $class . "'>" . $vector[$i][1] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][2] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][5] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][6] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][8] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][7] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][9] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][10] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][11] . "</td>";
				echo "<td class='" . $class . "'>" . $vector[$i][12] . "</td>";
				echo "<td class='" . $class . "'>" . implode( ",", $consControl ) . "</td>";
				

				if (isset($chkUni))
				{
					echo "<td class='" . $class . "'>" . $vector[$i][13] . "</td>";
					echo "<td class='" . $class . "'>" . $vector[$i][14] . "</td>";
				}

				echo "<td class='" . $class . "'>" . substr($vector[$i][16], 2) . "</td>";
				if ($vector[$i][15] == "on" or $vector[$i][15] == "P")
				{
					echo "<td class='" . $class . "'>Activo</td>";
				}
				else
				{
					echo "<td class='" . $class . "'><i>Inactivo</i></td>";
				}
				echo "</tr>";
			}

			echo "</table></td></tr>";
			echo "<table align=center><tr><td><a href='retorte.php?bd=".$bd."'</a>Retornar</td></tr></table>";
		}
		else
		{
			echo "<td class='errorTitulo'>NO SE ENCONTRARON REGISTROS CON ESAS CARACTERISTICAS</td></tr>";
		}
	}
	echo "</table>";
}

?>
</body>
</html>