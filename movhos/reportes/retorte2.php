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
    	.texto4{color:#FFFFF;background:yellow;font-size:10pt;font-family:Tahoma;text-align:center;}
    	.texto5{color:#FFFFF;background:orange;font-size:10pt;font-family:Tahoma;text-align:center;}
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

 Noviembre 15 de 2013 (Jonatan Lopez)		
			Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 
 			para que traiga los datos de contingencia (tabla movhos_00143) con estado activo.
*/

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
	echo "<td class='titulo1'>Cantidad Integrador</td>";
	echo "<td class='titulo1'>Cantidad Unix</td>";
	echo "<td class='titulo1'>Responsable</td>";
	echo "<td class='titulo1'>Estado</td>";

	echo "</tr>";
}

session_start();
if (!isset($_SESSION['user']))
echo "error";
else
{

	include_once("movhos/otros.php");
	connectOdbc($conex_o, 'facturacion');

	echo "</br></br><table align='center' border='0'>";
	echo "<tr><td align=center class='tituloSup' colspan='2'><b>ESTADO DE GRABACION DE DOCUMENTOS</b></td></tr>";
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
		echo "<td class='texto2'><input type='checkbox' name='chkArt'></td>";
		echo "<td class='texto2' ><b>Artículo: </b><input type='text' size='10' name='art'></td></tr>";
		echo "<td class='texto2'></td>";
		echo "</td></tr>";
		echo "<td class='texto2'></td>";
		echo "<td class='texto2'><b>Fecha: </b><input type='text' size='10' name='fec1' value='" . date("Y-m-d") . "'></td></tr>";
		echo"<tr><td   class='texto2' colspan='2' align='center'><input type='submit' value='ACEPTAR'></td></tr></form>";
		echo "</form>";
	}
	else  if ($conex_o != 0)
	{
		$fen = "";
		$fde = "";
		$fit = "";
		$fdeit = "";
		$fiv = "";
		$fdeiv = "";
		$fec2=$fec1;
		if (isset($chkArt) or isset($chkHis))
		{
			$exp=explode ('-', $fec1);
			if(isset($exp[1]))
			{
				$tiempo=mktime(0,0,0,$exp[1],$exp[2],$exp[0])-(2*24*60*60);
				$fec1=date('Y-m-d', $tiempo);
			}
		}
		$fec3 = str_replace('-', '/', $fec1);
		$fec4 = str_replace('-', '/', $fec2);
		if (isset($chkHis))
		{
			$fen = " Fenhis = '" . $his . "' and Fening = '" . $ing . "' and";
			$fit = " A.Drohis = '" . $his . "' and";
		}

		if (isset($chkCco))
		{
			$fen = $fen . " Fencco = '" . $cco . "' and";
			$fit = $fit . " A.Drocco = '" . $cco . "' and";
		}

		$art=strtoupper($art);
		if (isset($chkArt))
		{
			//busco si el articulo existe en la 26 o si no es de la central
			$central=" SELECT Artcom "
			."       FROM " . $bd . "_000026 "
			."    WHERE Artcod = '" . $art . "' "
			."       AND Artest = 'on' ";

			$errcen = mysql_query($central, $conex);
			$central = mysql_num_rows($errcen);

			if($central>0)
			{
				$fen = $fen . "  Fdeart = '" . $art . "'  and ";
			}
			else
			{
				$fen = $fen . "  Fdeari = '" . $art . "' and ";
			}

			$fit = $fit . "        AND Drodetart = '" . $art . "' ";
		}

		if ($fec1==$fec2)
		{
			echo"<tr><td   class='texto2' colspan='2' align='center'>Fecha: ".$fec1." </td></tr>";
		}
		else
		{
			echo"<tr><td   class='texto2' colspan='2' align='center'>Fecha: ".$fec1." a ".$fec2."</td></tr></form>";
		}
		//VAMOS A BUSCAR LOS QUE ESTAN EN MATRIX PROCESADOS

		$fde = "  SELECT " . $bd . "_000003.Fecha_data as fec, " . $bd . "_000003.Hora_data as hor, Fenfue, Fencco, cast(Fenhis as char), Fening, Fennum, Fendoc, Fdelin, Fdeart, Fdeari, Fdecan, 0 as drocan, 0 as drodetcan, Fdeest, " . $bd . "_000003.Seguridad as res, Fdeubi "
		. "         FROM " . $bd . "_000003, " . $bd . "_000002 "
		. "        WHERE " . $fen. " "
		. "        " . $bd . "_000002.Fecha_data between '" . $fec1 . "' AND '" . $fec2 . "' "
		. "          AND Fdenum = Fennum "
		/*********************************************************************************************************************/
		/* Noviembre 13 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/	
		. " 	   UNION "
		. "		  SELECT " . $bd . "_000143.Fecha_data as fec, " . $bd . "_000143.Hora_data as hor, Fenfue, Fencco, cast(Fenhis as char), Fening, Fennum, Fendoc, Fdelin, Fdeart, Fdeari, Fdecan, 0 as drocan, 0 as drodetcan, Fdeest, " . $bd . "_000143.Seguridad as res, Fdeubi "
		. "         FROM " . $bd . "_000143, " . $bd . "_000002 "
		. "      WHERE " . $fen. " "
		. "         " . $bd . "_000002.Fecha_data between '" . $fec1 . "' AND '" . $fec2 . "' "
		. "        AND Fdenum = Fennum "
		. "		   AND Fdeest = 'on'";
		$fde ." ORDER BY  Fencco, Fenfue,  Fenfec, Fennum, Fdelin, Fdeart ";
		
		// se organiza query en matrix para itdro
		$err = mysql_query($fde, $conex) or die( mysql_error(). " - $fde - " );
		$num = mysql_num_rows($err);

		$colspan = 14;
		echo "<tr><td align='center'><br><table border=0 >";
		$fueant = '';
		$ccoAnt = '';

		for($i = 1;$i <= $num ;$i++)
		{
			$row = mysql_fetch_array($err);

			if ($i % 2 == 0)
			{
				$class = "texto";
			}
			else
			{
				$class = "texto1";
			}

			switch ($row[16])
			{
				case 'P':
				$integrador= $row[11];
				$unix= $row[11];
				break;

				default:
				//primero nos fijamos si ya esta procesado
				$q=" SELECT droest  "
				."     FROM itdro "
				."    WHERE dronum = ".$row[6]." "
				."    AND   drolin = ".$row[8]." ";

				$err_o= odbc_do($conex_o,$q);
				if (odbc_fetch_row($err_o))
				{
					if(odbc_result($err_o,1)=='P')
					{
						$integrador= $row[11];
						$unix= $row[11];
					}
					else if (odbc_result($err_o,1)=='S')
					{
						$integrador= $row[11];
						$unix= 0;
						$class='texto4';
					}
					else
					{
						$integrador= $row[11];
						$unix= 0;
						$class='texto3';
					}
				}
				else
				{
					$integrador= 0;
					$unix= 0;
					$class='texto5';
				}
				break;
			}

			if ($ccoAnt != $row[3])
			{
				echo "<tr><td colspan='" . $colspan . "'>&nbsp;</td></tr>";
				echo "<tr><td bgcolor='#f5f5dc' colspan='" . $colspan . "'><font color='#006699'><B>Centro de costos:</B> " . $row[3] . "</font></td></tr>";
				echo "<tr><td bgcolor='#FFDBA8' colspan='" . $colspan . "'><font color='#006699'><B>Fuente:</B> " . $row[2] . "</font></td></tr>";
				$ccoAnt = $row[3];
				$fueAnt = $row[2];
				pintarNombres(isset($chkUni));
			}
			else if ($fueAnt != $row[2])
			{
				echo "<tr><td bgcolor='#FFDBA8' colspan='" . $colspan . "'><font color='#006699'><B>Fuente:</B> " . $row[2] . "</font></td></tr>";
				$fueAnt = $row[2];
				pintarNombres(isset($chkUni));
			}

			echo "<tr>";
			echo "<td class='" . $class . "'>" . $row[0] . "</td>";
			echo "<td class='" . $class . "'>" . $row[1] . "</td>";
			echo "<td class='" . $class . "'>" . $row[4] . "</td>";
			echo "<td class='" . $class . "'>" . $row[5] . "</td>";
			echo "<td class='" . $class . "'>" . $row[7] . "</td>";
			echo "<td class='" . $class . "'>" . $row[6]. "</td>";
			echo "<td class='" . $class . "'>" . $row[8] . "</td>";
			echo "<td class='" . $class . "'>" . $row[9] . "</td>";
			echo "<td class='" . $class . "'>" . $row[10] . "</td>";
			echo "<td class='" . $class . "'>" . $row[11] . "</td>";
			echo "<td class='" . $class . "'>" . $integrador. "</td>";
			echo "<td class='" . $class . "'>" . $unix . "</td>";

			echo "<td class='" . $class . "'>" . substr($row[15], 2) . "</td>";
			if ($row[14] == "on" or $row[14] == "P")
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
		echo "<table align=center><tr><td><a href='retorte2.php?bd=".$bd."'</a>Retornar</td></tr></table>";
	}
	else
	{
		echo "<td class='errorTitulo'>EN ESTO MOMENTO NO HAY CONEXION CON UNIX</td></tr>";
	}
	echo "</table>";
}

?>
</body>
</html>