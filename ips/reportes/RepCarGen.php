<html>
<head>
<title>REPORTE DE CARTERA GENERAL</title>

<style type="text/css">
//
body {
	background: white url(portal.gif) transparent center no-repeat scroll;
}

.titulo1 {
	color: #FFFFFF;
	background: #006699;
	font-size: 20pt;
	font-family: Arial;
	font-weight: bold;
	text-align: center;
}

.titulo2 {
	color: #003366;
	background: #A4E1E8;
	font-size: 9pt;
	font-family: Arial;
	font-weight: bold;
	text-align: center;
}

.titulo3 {
	color: #003366;
	background: #57C8D5;
	font-size: 12pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: left;
}

.titulo4 {
	color: #003366;
	font-size: 12pt;
	font-family: Arial;
	font-weight: bold;
	text-align: center;
}

.texto1 {
	color: #006699;
	background: #FFFFFF;
	font-size: 9pt;
	font-family: Tahoma;
	text-align: center;
}

.texto2 {
	color: #006699;
	background: #f5f5dc;
	font-size: 9pt;
	font-family: Tahoma;
	text-align: center;
}

.texto3 {
	color: #006699;
	background: #A4E1E8;
	font-size: 9pt;
	font-weight: bold;
	font-family: Tahoma;
	text-align: center;
}

.texto4 {
	color: #006699;
	background: #FFFFFF;
	font-size: 9pt;
	font-family: Tahoma;
	text-align: right;
}

.texto5 {
	color: #006699;
	background: #f5f5dc;
	font-size: 9pt;
	font-family: Tahoma;
	text-align: right;
}

.acumulado1 {
	color: #003366;
	background: #FFCC66;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: right;
}

.acumulado2 {
	color: #003366;
	background: #FFCC66;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.acumulado3 {
	color: #003366;
	background: #FFDBA8;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.acumulado4 {
	color: #003366;
	background: #FFDBA8;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: right;
}

.error1 {
	color: #FF0000;
	font-size: 10pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}
</style>

<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->

function Seleccionar()
{
	document.forma.submit();
}

</script>

</head>
<body>
<?php
include_once("conex.php");
/**
 * NOMBRE:  REPORTE DE CARTERA GENERAL
 *
 * PROGRAMA: RepCarGen.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION:Este reporte presenta la lista de notas debito o notas credito o recibos de cajacon sus detalles por empresa o para todas las empresas
 *
 * HISTORIAL DE ACTUALIZACIONES:
 * 2013-01-16 Mario Cadavid, Jerson Trujillo y Camilo Zapata. Se crearon las tablas temporales para mejorar el tiempo de generación del reporte
 * 			  Se bajó considerablemente el tiempo, ya que antes se recorría registro por registro de la tabla 000018 en cada ciclo cuando eran consultas de varios años, ya solo se recorre las facturas necesarias
 * 2012-09-24 Camilo Zapata. se mejoró el query para que tenga en cuenta que los movimientos correspondan al mismo responsable de la factura consultando los posibles rencod en la 24.
 *							 considerando que si es un particular debe buscar el nit del fencod y no utilizar su nit.
 * 2012-09-20 Camilo Zapata. se comentó el cambio anterior, y se modificó el query de la linea 777 para que tome el saldo mas reciente en lugar del menor saldo .
 * 2012-09-11 Camilo Zapata. se corrigieron los valores de la suma de saldos, estaban sumando los que no eran.
 * 2012-09-11 Camilo Zapata. se modificó el script para que la variable de contról de pintado en pantalla inicie habilitada. ya que estaba omitiendo la primer tanda de
 *			  				 facturas. y se modificó el script para que compare por saldo de la factura de la 18 no de los recibos de la 21(buscar donde dice ojo)
 * 2012-08-24 Mario Cadavid. Se agregó la función cambiarCadena para que los caracteres especiales se muestren correctamente en el reporte
 * 2006-06-20 carolina castano, creacion del script
 * 2006-10-12 carolina castano, cambios de forma, presentación
 * 2009-05-15 Edwin Molina Grisales, se añade encabezado actual
 *            y se quita la fila de presenaciòn
 * 2012-02-23 Mario Cadavid, Se agregó la consulta de los registros de empresas con el mismo NIT de la empresa responsable, esto para
 * 			  que en las consultas por NIT muestre los registros asociados al NIT de la empresa responsable, es decir, no solo la
 *			  cartera directa de la empresa sino tambien la de sus empleados. Por esto se comentaron las consultas que mostraban
 *			  los empleados como empresa separada con sus facturas, ya que estas facturas aparecen dentro de la empresa, por ejemplo:
 *			  las facturas de empleados clinica de sur aparecian al final como empresa "Empleado clinica del sur", ya no aparecen al final
 * 			  sino que aparecen dentro de las facturas de "Clinica del sur"
 *
 * Tablas que utiliza:
 * $wbasedato."_000024: Maestro de Fuentes, select
 * $wbasedato."_000018: select de facturas entre dos fechas
 * $wbasedato."_000020: select en encabezado de cartera
 * $wbasedato."_000021: select en detalle de cartera
 *
 * @author ccastano
 * @package defaultPackage
 */

/**
 * Consulta el multiplicaador del concepto, es decir si resta o multiplica a una factura
 *
 * @param unknown_type $codigo el codigo del concepto de cartera, la fuente del documento al que pertenece
 * @param unknown_type $fuente
 * @return unknown
 */
function consultarMulti ($codigo, $fuente)
{
	global $wbasedato;
	global $wemp_pmla;
	global $conex;

	$q="select conmul from ".$wbasedato."_000044 where concod='".$codigo."' and confue='".$fuente."'  ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$row = mysql_fetch_row($res);
	return (-1*$row[0]);

}

function cambiarCadena ($message) {
$search = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ã¡,Ã©,Ã­,Ã³,Ãº,Ã±,ÃÃ¡,ÃÃ©,ÃÃ­,ÃÃ³,ÃÃº,ÃÃ±,Ã?Â?,ÃƒÂƒÃ,Ã,ÂƒƒÂ,Â±");
 $replace = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ñ,,,,Ñ");
 $message= str_replace($search, $replace, $message);  
 $message= str_replace("‚", "", $message);  
 return $message;
}
 
// =================================================================================================================================
include_once("root/comun.php");
session_start();
if (!isset($_SESSION['user']))
	echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	$key = substr($user, 2, strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;

	echo "<form action='RepCarGen.php' method=post name='forma'>";


	$wfecha = date("Y-m-d");

	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset($wfeccor) or !isset($wemp) or !isset($wtip) or !isset ($resultado))
	{
		$wactualiz = "Enero 16 de 2013";
		encabezado("REPORTE DE CARTERA GENERAL", $wactualiz, "logo_".$wbasedato);
		echo "<br><br>";
			
		echo "<center><table border=0>";
		//        echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=500 HEIGHT=100></td></tr>";
		//        echo "<tr><td class='titulo1'>REPORTE DE CARTERA GENERAL</td></tr>";
		// INGRESO DE VARIABLES PARA EL REPORTE//
		if (!isset ($bandera))
		{
			$wfecini = $wfecha;
			$wfecfin = $wfecha;
			$wfeccor = $wfecha;
		}

		//        echo "<tr>";
		//        echo "<td align=center class='texto3'><b>FECHA INICIAL DE FACTURACION: </font></b>";
		echo "<tr class='fila1'>";
		echo "<td align=center><b>FECHA INICIAL DE FACTURACION: </font></b>";
		campoFechaDefecto("wfecini",$wfecini);
		echo "</td>";

		//        echo "<td align=center class='texto3'><b>FECHA FINAL DE FACTURACION: </font></b>";
		echo "<td align=center><b>FECHA FINAL DE FACTURACION: </font></b>";
		campoFechaDefecto("wfecfin",$wfecfin);
		echo "</td>";

		echo "</tr>";

		//        echo "<tr>";
		echo "<tr class='fila1'>";
		//        echo "<td class='texto3' align=center>FECHA DE CORTE : ";
		echo "<td align=center>FECHA DE CORTE : ";
		campoFechaDefecto("wfeccor",$wfeccor);
		echo "</td>";

		// SELECCIONAR tipo de reporte
		//        echo "<td align=center class='texto3' >PARAMETROS DEL REPORTE: ";
		echo "<td align=center>PARAMETROS DEL REPORTE: ";
		echo "<select name='wtip'>";
		if (isset ($wtip))
		{
			if ($wtip == 'CODIGO')
			{
				echo "<option>CODIGO</option>";
				echo "<option>NIT</option>";
			}
			if ($wtip == 'NIT')
			{
				echo "<option>NIT</option>";
				echo "<option>CODIGO</option>";
			}
		}
		else
		{
			echo "<option>CODIGO</option>";
			echo "<option>NIT</option>";
		}
		echo "</select></td></tr>";
		// SELECCIONAR EMPRESA
		if (isset($wemp) && substr($wemp,0,3) != 'EMP' )
		{
			echo "<td align=center class='fila1' colspan=2 >Responsable: <br><select name='wemp'>";

			if ($wemp != '% - Todas las empresas')
			{
				$q = "   SELECT count(*) "
				. "     FROM " . $wbasedato . "_000024 "
				. "    WHERE empcod = (mid('" . $wemp . "',1,instr('" . $wemp . "','-')-1)) "
				. "      AND empcod = empres ";
				$res1 = mysql_query($q, $conex);
				$num1 = mysql_num_rows($res1);
				$row1 = mysql_fetch_array($res1);
			}
			else
			{
				$row1[0] = 1;
			}

			if ($row1[0] > 0)
			{
				echo "<option selected>" . $wemp . "</option>";
				if ($wemp != '% - Todas las empresas')
				{
					echo "<option>% - Todas las empresas</option>";
				}

				$q = "   SELECT count(*) "
				. "     FROM " . $wbasedato . "_000024 "
				. "    WHERE empcod != (mid('" . $wemp . "',1,instr('" . $wemp . "','-')-1)) "
				. "      AND empcod = empres ";
				$res = mysql_query($q, $conex);
				$num = mysql_num_rows($res);
				$row = mysql_fetch_array($res);
				if ($row[0] > 0)
				{
					$q = "   SELECT empcod, empnit, empnom "
					. "     FROM " . $wbasedato . "_000024 "
					. "    WHERE empcod != (mid('" . $wemp . "',1,instr('" . $wemp . "','-')-1)) "
					. "      AND empcod = empres order by 3";
					$res1 = mysql_query($q, $conex);
					$num1 = mysql_num_rows($res1);
					for ($i = 1;$i <= $num1;$i++)
					{
						$row1 = mysql_fetch_array($res1);
						echo "<option>" . $row1[0] . " - " . $row1[1] . " - " . $row1[2] . "</option>";
					}
				}
			}
			
			$q = "SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom "
			. "   FROM " . $wbasedato . "_000024 "
			. "  WHERE empcod != empres "
			. "  GROUP BY emptem "
			. "  ORDER BY empnom ";

			$res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());

			for ($i = 1;$i <= $num;$i++)
			{
				$row = mysql_fetch_array($res);
				if( "EMP - " . $row[1] . " - " . $row[2] != $wemp ){
					echo "<option>EMP - " . $row[1] . " - " . $row[2] . "</option>";
				}
				else{
					echo "<option selected>EMP - " . $row[1] . " - " . $row[2] . "</option>";
				}
			}
			echo "</select></td>";
		}
		else
		{
			echo "<td class='fila1' colspan=2 align=center > Responsable: <br><select name='wemp'>";

			$q = "SELECT empcod, empnit, empnom "
			. "   FROM " . $wbasedato . "_000024 "
			. "  WHERE empcod = empres "
			. "  ORDER BY empnom ";

			$res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());

			echo "<option>% - Todas las empresas</option>";
			for ($i = 1;$i <= $num;$i++)
			{
				$row = mysql_fetch_array($res);
				echo "<option>" . $row[0] . " - " . $row[1] . " - " . $row[2] . "</option>";
			}
			
			
			
			$q = "SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom "
			. "   FROM " . $wbasedato . "_000024 "
			. "  WHERE empcod != empres "
			. "  GROUP BY emptem "
			. "  ORDER BY empnom ";

			$res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());

			for ($i = 1;$i <= $num;$i++)
			{
				$row = mysql_fetch_array($res);
			
				if( "EMP - " . $row[1] . " - " . $row[2] != @$wemp ){
					echo "<option>EMP - " . $row[1] . " - " . $row[2] . "</option>";
				}
				else{
					echo "<option selected>EMP - " . $row[1] . " - " . $row[2] . "</option>";
				}
//				echo "<option>" . $row[0] . " - " . $row[1] . " - " . $row[2] . "</option>";
			}
			
			echo "</select></td>";
		}
		echo "</tr>";

		echo "<input type='HIDDEN' NAME= 'wbasedato' value='" . $wbasedato . "'>";

		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

		//        echo "<tr><td align=center class='texto3' COLSPAN='2'>";
		echo "<tr class='fila1'><td align=center COLSPAN='2'>";
		echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> DESPLEGAR REPORTE DETALLADO&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()' > DESPLEGAR REPORTE RESUMIDO&nbsp;&nbsp;"; //submit
		echo "</b></td></tr></table></br>";
		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
	}
	// MUESTRA DE DATOS DEL REPORTE
	else
	{

		/**************************************************************************************/
		/////////////////////////	INICIA GENERACIÓN DEL REPORTE	////////////////////////////
		/**************************************************************************************/
	
	
		$wactualiz = "Enero 16 de 2013";
		encabezado("REPORTE DE CARTERA GENERAL", $wactualiz, "logo_".$wbasedato);
		echo "<br><br>";
		
		echo "<table  align=center width='60%'>";
//		echo "<tr><td>&nbsp;</td></tr>";
//		echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=340 HEIGHT=100></td></tr>";
//		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td><B>Fecha: " . date('Y-m-d') . "</B></td></tr>";
		if ($vol == 'SI')
			echo "<tr><td><B>REPORTE DE CARTERA GENERAL DETALLADO</B></td></tr>";
		else
			echo "<tr><td><B>REPORTE DE CARTERA GENERAL RESUMIDO</B></td></tr>";
		echo "</table>";
//		echo "</tr><td align=right><A href='RepCarGen.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wtip=" . $wtip . "&amp;wfeccor=" . $wfeccor . "&amp;wemp=" . $wemp . "&amp;bandera='1'>VOLVER</A></td></tr><tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
//		echo "<tr><td><tr><td>Fecha inicial: " . $wfecini . "</td></tr>";
//		echo "<tr><td>Fecha final: " . $wfecfin . "</td></tr>";
		
		echo 	"<br><br><table align=center>
			  		<tr class='encabezadotabla' align=center>
			  			<td width=150><b>Fecha Inicial</b></td>
			  			<td width=150><b>Fecha Final</b></td>
			  		<tr class='fila1' align=center>
			  			<td>$wfecini</td>
			  			<td>$wfecfin</td>
			  		</tr>
			  	</tr></table>";
		
		echo "<table  align=center width='60%'>";
		echo "<tr><td><br><br><b>Fecha de corte: </b>" . $wfeccor . "</td></tr>";
		echo "<tr><td><b>Empresa: </b>" . $wemp . "</td></tr>";
		echo "<tr><td><b>clasificado por </b>: " . $wtip . "</td></tr>";
		echo "</table></br><br>";

		echo "<input type='HIDDEN' NAME= 'wfecini' value='" . $wfecini . "'>";
		echo "<input type='HIDDEN' NAME= 'wfecfin' value='" . $wfecfin . "'>";
		echo "<input type='HIDDEN' NAME= 'wemp' value='" . $wemp . "'>";
		echo "<input type='HIDDEN' NAME= 'wtip' value='" . $wtip . "'>";
		echo "<input type='HIDDEN' NAME= 'wfeccor' value='" . $wfeccor . "'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		/**
		 * **********************************Consulto lo pedido *******************
		 */
		// si la empresa es diferente a "% - Todas las empresas", la meto en el vector solo
		// si es "% - Todas las empresas" meto todas en un vector para luego preguntarlas en un for
		if ($wemp != '% - Todas las empresas')
		{
			
			$print = explode('-', $wemp);
			
			if( trim ($print[0]) != 'EMP' ){
				$empCod[0] = trim ($print[0]);
				$empNom[0] = trim ($print[2]);
				$empNit[0] = trim ($print[1]);
				$empresa[0] = $empCod[0] . " - " . $empNit[0] . " - " . $empNom[0];
				$empleado[0] = 'off';
				$num = 1;
			}
			else{
				$empCod[0] = trim ($print[0]);
				$empNom[0] = trim ($print[2]);
				$empNit[0] = trim ($print[1])."-".trim( $print[2] );
				$empresa[0] = $empCod[0] . " - " . $empNit[0] . " - " . $empNom[0];
				$empleado[0] = 'on';
				$num = 1;
			}
		}
		else
		{
			
			// Si es consulta por codigo
			if ($wtip == 'CODIGO')
			{
				// Consulto las empresas donde el codigo es igual al codigo del responsable
				$q = " (SELECT empcod, empnom, empnit, 'off' as empleado "
				. "   FROM " . $wbasedato . "_000024 "
				. "  WHERE empcod=empres "
				. "  ORDER BY 3 desc ,1 ) ";

				$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				for ($i = 0;$i < $num;$i++)
				{
					$row = mysql_fetch_array($res);
					$empCod[$i] = $row[0];
					$empNom[$i] = $row[1];
					$empNit[$i] = $row[2];
					$empresa[$i] = $row[0] . " - " . $row[2] . " - " . $row[1];
					$empleado[$i] = $row[3];
				}
				
				/* comentado 2012-02-23
				// Se comenta porque las facturados de los empleados se incluyen dentro de las facturas de la empresa
				$auxNum = $num;

				$q = " (SELECT 'on' as empcod, 'on' as empnom, emptem as empnit, 'on' as empleado "
				. "   FROM " . $wbasedato . "_000024 "
				. "  WHERE empcod!=empres "
				. "  GROUP BY emptem "
				. "  ORDER BY 3 desc ,1 ) ";

				$res = mysql_query($q, $conex);
				$num = mysql_num_rows($res);
				
				for ($i = 0;$i < $num;$i++)
				{
					$row = mysql_fetch_array($res);
					$empCod[$auxNum+$i] = $row[0];
					$empNom[$auxNum+$i] = $row[1];
					$empNit[$auxNum+$i] = $row[2];
					$empresa[$auxNum+$i] = $row[0] . " - " . $row[2] . " - " . $row[1];
					$empleado[$auxNum+$i] = $row[3];
				}
				
				$num += $auxNum; 
				*/
			}

			if ($wtip == 'NIT')
			{
				$q = "( SELECT  empnom, empnit, 'off' as empleado, empcod "
				. "   FROM " . $wbasedato . "_000024 "
				. "  WHERE empcod=empres "
				. "  GROUP BY empnit  "
				. "  ORDER BY 2 desc ,1 )";

				$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				for ($i = 0;$i < $num;$i++)
				{
					$row = mysql_fetch_array($res);
					$empCod[$i] = $row[3];
					$empNom[$i] = $row[0];
					$empNit[$i] = $row[1];
					$empresa[$i] = $row[1] . " - " . $row[0];
					$empleado[$i] = $row[2];
				}
				
				/* comentado 2012-02-23
				// Se comenta porque las facturados de los empleados se incluyen dentro de las facturas de la empresa
				$auxNum = $num;
				
				$q = " (SELECT 'on' as empnom, emptem as empnit, 'on' as empleado, empcod "
				. "   FROM " . $wbasedato . "_000024 "
				. "  WHERE empcod!=empres "
				. "  GROUP BY emptem "
				. "  ORDER BY 3 desc ,1 ) ";

				$res = mysql_query($q, $conex);
				$num = mysql_num_rows($res);
				
				for ($i = 0;$i < $num;$i++)
				{
					$row = mysql_fetch_array($res);
					$empCod[$auxNum+$i] = $row[3];
					$empNom[$auxNum+$i] = $row[0];
					$empNit[$auxNum+$i] = $row[1];
					$empresa[$auxNum+$i] = $row[1] . " - " . $row[0];
					$empleado[$auxNum+$i] = $row[2];
				}
				
				$num += $auxNum; 
				*/
			}
		}
		// se busca en la tabla 20 y 21 registros, empresa por empresa en un for y entre las fechas escogidas.  En la tabla 21
		// se encuentra el saldo que dejo la nota en la factura.
		$cuenta = 0;
		$wtotal = 0;
		$wsaldo = 0;
		$clase1 = "style='font-size:10pt;font-weight:normal'";
		$clase2 = "style='text-align:right;font-size:10pt;font-weight:normal'";

		$globalglosa = 0;			//Calcula la glosa total de todas las empresas
		$globalcredito = 0;			//Caclula el credito total de todas las empresas
		$globaldebito = 0;			//Caclula el debito total de todas las empresas
		$globalrecibos = 0;			//Caclula los recibos totales de todas las empresas
		$globalaceptados = 0;		//Caclula el valor total aceptado de las glosas de todas las empresas

		// 2012-01-16
		// Creación de tablas temporales para mejorar el tiempo de generación del reporte
		
		$qdel = "	DROP TABLE IF EXISTS tmp_facturas ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		$qdel = "	DROP TABLE IF EXISTS tmp_movfeccor ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());
		
		$qdel = "	DROP TABLE IF EXISTS tmp_movfecfin ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());
		
		
		$qtmp = "	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_movfeccor 
	           		   (INDEX idx( fenres ), INDEX idx2( fenfec ), INDEX idx3( fenffa, fenfac ) ) "
					. " SELECT a.Medico, a.Fecha_data, a.Hora_data, Fenano, Fenmes, Fenfec, Fenffa, Fenfac, Fentip, Fennit, Fencod, Fenres, Fenval, Fenviv, Fencop, Fencmo, Fendes, Fenabo, Fenvnd, Fenvnc, Fensal, Fenest, fencre, fenpde, fenrec, fentop, fenhis, fening, fenesf, fenrln, fencco, Fenrbo, Fenimp, Fendpa, Fennpa, Fendev, Fennac, a.Seguridad, a.id id, b.id idb "
					. "     FROM  " . $wbasedato . "_000018 a, " . $wbasedato . "_000021 b "
					. "   	WHERE  a.fenfec between '" . $wfecini . "'"
					. "     AND '" . $wfecfin . "'"
					. "     AND a.fenest = 'on' "
					. "     AND a.fencco<>'' "
					. "     AND a.fenval<>0 "
					. "     AND a.fensal>=0 "
					. "     AND a.fenffa = b.rdeffa "
					. "     AND a.fenfac = b.rdefac "
					. "     AND b.fecha_data <=  '" . $wfeccor . "' "
					. "     AND b.rdeest= 'on' "
					. "     AND ( b.rdesfa>0 OR b.rdesfa='' ) "
					. "     AND b.rdereg=0 "
					. "		GROUP BY a.fenffa, a.fenfac "
					. "     ORDER BY idb ";
		$restmp = mysql_query($qtmp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qtmp . " - " . mysql_error());

		$qtmp = "	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_movfecfin 
	           		   (INDEX idx( fenres ), INDEX idx2( fenfec ), INDEX idx3( fenffa, fenfac ) ) "
					. " SELECT a.Medico, a.Fecha_data, a.Hora_data, Fenano, Fenmes, Fenfec, Fenffa, Fenfac, Fentip, Fennit, Fencod, Fenres, Fenval, Fenviv, Fencop, Fencmo, Fendes, Fenabo, Fenvnd, Fenvnc, Fensal, Fenest, fencre, fenpde, fenrec, fentop, fenhis, fening, fenesf, fenrln, fencco, Fenrbo, Fenimp, Fendpa, Fennpa, Fendev, Fennac, a.Seguridad, a.id id, b.id idb "
					. "     FROM  " . $wbasedato . "_000018 a, " . $wbasedato . "_000021 b "
					. "   	WHERE  a.fenfec between '" . $wfecini . "'"
					. "     AND '" . $wfecfin . "'"
					. "     AND a.fenest = 'on' "
					. "     AND a.fencco<>'' "
					. "     AND a.fenval<>0 "
					. "     AND a.fensal>=0 "
					. "     AND a.fenffa = b.rdeffa "
					. "     AND a.fenfac = b.rdefac "
					. "     AND b.fecha_data >  '" . $wfeccor . "' "
					. "     AND b.rdeest= 'on' "
					. "     AND b.rdereg=0 "
					//. "		AND CONCAT(a.fenffa, a.fenfac) NOT IN ( SELECT CONCAT(a.fenffa, a.fenfac) FROM tmp_movfeccor ) "
					. "		GROUP BY a.fenffa, a.fenfac "
					. "     ORDER BY idb ";
		$restmp = mysql_query($qtmp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qtmp . " - " . mysql_error());

		$qtmp = "	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_facturas 
	           		   (INDEX idx( fenres ), INDEX idx2( fenfec ), INDEX idx3( fenffa, fenfac ) ) "
					. " SELECT a.Medico, a.Fecha_data, a.Hora_data, Fenano, Fenmes, Fenfec, Fenffa, Fenfac, Fentip, Fennit, Fencod, Fenres, Fenval, Fenviv, Fencop, Fencmo, Fendes, Fenabo, Fenvnd, Fenvnc, Fensal, Fenest, fencre, fenpde, fenrec, fentop, fenhis, fening, fenesf, fenrln, fencco, Fenrbo, Fenimp, Fendpa, Fennpa, Fendev, Fennac, a.Seguridad, a.id "
					. "     FROM  tmp_movfeccor a "
					."	UNION "
					. " SELECT a.Medico, a.Fecha_data, a.Hora_data, Fenano, Fenmes, Fenfec, Fenffa, Fenfac, Fentip, Fennit, Fencod, Fenres, Fenval, Fenviv, Fencop, Fencmo, Fendes, Fenabo, Fenvnd, Fenvnc, Fensal, Fenest, fencre, fenpde, fenrec, fentop, fenhis, fening, fenesf, fenrln, fencco, Fenrbo, Fenimp, Fendpa, Fennpa, Fendev, Fennac, a.Seguridad, a.id "
					. "     FROM  tmp_movfecfin a "
					."	UNION "
					. " SELECT a.Medico, a.Fecha_data, a.Hora_data, Fenano, Fenmes, Fenfec, Fenffa, Fenfac, Fentip, Fennit, Fencod, Fenres, Fenval, Fenviv, Fencop, Fencmo, Fendes, Fenabo, Fenvnd, Fenvnc, Fensal, Fenest, fencre, fenpde, fenrec, fentop, fenhis, fening, fenesf, fenrln, fencco, Fenrbo, Fenimp, Fendpa, Fennpa, Fendev, Fennac, a.Seguridad, a.id "
					. "     FROM  " . $wbasedato . "_000018 a "
					. "   	WHERE  a.fenfec between '" . $wfecini . "'"
					. "     AND '" . $wfecfin . "'"
					. "     AND a.fenest = 'on' "
					. "     AND a.fencco<>'' "
					. "     AND a.fenval<>0 "
					. "     AND a.fensal>0 ";
		$restmp = mysql_query($qtmp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qtmp . " - " . mysql_error());
		
		
		
		for ($i = 0;$i < $num;$i++)
		{
			if( $empleado[$i] != 'on' )
			{
				
				if ($wtip == 'NIT')
				{
					// 2012-02-23
					// Consulto los registros de empresas con el mismo NIT de la empresa responsable 
					$qemp = "	SELECT b.empcod
								FROM ".$wbasedato."_000024 a, ".$wbasedato."_000024 b
								WHERE a.empcod = '" . $empCod[$i] . "' 
								AND	a.empnit = b.empnit";
					$resemp = mysql_query($qemp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qemp . " - " . mysql_error());
					$numemp = mysql_num_rows($resemp);
					
					if($numemp>0)
						$regemp = "(";
					else
						$regemp = "('')";
						
					while($rowemp = mysql_fetch_array($resemp))
					{
						if($rowemp[0]!="NO APLICA" && $rowemp[0]!="" && $rowemp[0]!=NULL)
							$regemp .= "'".$rowemp[0]."',";
					}
					
					if($numemp>0)
					{
						if($regemp!="(")
						{
							$regemp .= ")";
							$regemp = str_replace(",)",")",$regemp);
						}
						else
							$regemp = "('')";
					}
					
					$q = "  SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv), fennpa, fentip, fenres, fennit, fencod "
					. "     FROM  tmp_facturas a "
					. "   	WHERE  a.fenfec between '" . $wfecini . "'"
					. "     AND '" . $wfecfin . "'"
					. "     AND a.fenres IN " . $regemp . " "
					. "     AND a.fenest = 'on' "
					. "     AND a.fencco<>'' "
					. "     AND a.fenval<>0 "
					. "     AND a.fensal>=0 "
//					. "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
					. "     ORDER BY  a.fenffa, a.fenfac ";
					//echo $q."<br>";

					/* QUERY COMENTADO 2012-02-23
					$q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv), fennpa, fentip"
					. "    FROM  " . $wbasedato . "_000018 a"
					. "   	WHERE  a.fenfec between '" . $wfecini . "'"
					. "     AND '" . $wfecfin . "'"
					. "     AND a.fennit = '" . $empNit[$i] . "' "
					. "     AND a.fenest = 'on' "
					. "     AND a.fencco<>'' "
					. "     AND a.fenval<>0 "
//					. "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
					. "     ORDER BY  a.fenffa, a.fenfac ";
					*/
				}
				if ($wtip == 'CODIGO')
				{
					//Original
					//echo "por acá";
					//				$q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo)"
					$q = "  SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv), fennpa, fentip, fenres, fennit, fencod "
					. "     FROM  tmp_facturas a "
					. "   	WHERE  a.fenfec between '" . $wfecini . "'"
					. "     AND '" . $wfecfin . "'"
					. "     AND a.fenres = '" . $empCod[$i] . "' "
					. "     AND a.fenest = 'on' "
					. "     AND a.fencco<>'' "
					. "     AND a.fenval<>0 "
					. "     AND a.fensal>=0 "
//					. "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
					. "     ORDER BY  a.fenffa, a.fenfac ";
					//echo "<br> query inicial: ".$q;
				}
			}
			else
			{
				$q = "  SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv), fennpa, fentip, fenres, fennit, fencod "
				. "     FROM  tmp_facturas a"
				. "   	WHERE  a.fenfec between '" . $wfecini . "'"
				. "     AND '" . $wfecfin . "'"
				. "     AND a.fenres != fencod "
				. "     AND a.fentip = '" . $empNit[$i] . "' "
				. "     AND a.fenest = 'on' "
				. "     AND a.fencco<>'' "
				. "     AND a.fenval<>0 "
				. "     AND a.fensal>=0 "
//				. "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
				. "     ORDER BY  a.fenffa, a.fenfac ";
				
				$exp = explode( "-", $empNit[$i] );
				$empCod[$i] = $exp[0];
				$empNit[$i] = $exp[0];
				$empNom[$i] = @$exp[1];
			}
			
			$err = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num1 = mysql_num_rows($err);
			
			if ($num1 > 0)
			{
				$wtotfac = 0;
				$wtotsal = 0;
				$senal = 0;
				$row = mysql_fetch_array($err);
				$fensal = $row[5];
				$pinto = 0;
				$pintar = 0;

				$totalglosa = 0;		//Calcula el total de la glosa por empresa
				$totalaceptados = 0;	//Calcula el total aceptado de la glosa por empresa
				$totalcredito = 0;		//Calcula el total credito de la factura por empresa
				$totaldebito = 0;		//Calcula el total debito de la factura por empresa
				$totalrecibos = 0;		//Calcula el total de recibos de cartera por empresa
				$nit=$row[10];
				if($row['fenres']!=$row['fencod'])
				{
					$qnit ="SELECT empnit
						      FROM {$wbasedato}_000024
							 WHERE empcod='{$row['fenres']}'";
					$rsnit = mysql_query($qnit, $conex);
					$rowNit = mysql_fetch_array($rsnit);
					$nit = $rowNit[0];
				}
				for ( $j = 0;$j < $num1;$j++)
				{
					//2012-09-24 Query agregado para buscar los posibles rencod asociados a los movimientos de las facturas
					$qres = "SELECT Empres, Empcod 
							   FROM {$wbasedato}_000024 
							  WHERE Empnit='{$nit}'";
					$rsRes = mysql_query($qres, $conex) or die(mysql_error().'ajam'.$qres);
					$numResponsables = mysql_num_rows($rsRes);
					$filtroResponsables='';
					for($m=0; $m < $numResponsables; $m++ )
					{
						$rowRes = mysql_fetch_array($rsRes);
						if($m==0)
							$filtroResponsables=" AND Rencod IN ('{$row['fencod']}', '{$rowRes[0]}', '{$rowRes[1]}' ";
							else
							 $filtroResponsables.=", '{$rowRes[0]}', '{$rowRes[1]}' ";
							
						if($m==$numResponsables-1)
							$filtroResponsables.=")";
					}
					$q = "  SELECT  b.rdesfa, b.rdefue, b.rdenum, b.rdefac, b.rdeffa "
					. "     FROM  " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b   "
					. "   	WHERE rdefac= '" . $row[1] . "' "
					. "     AND rdeffa= '" . $row[0] . "' "
					. "     AND rdeest= 'on' "
					. "     AND rdesfa<>'' "
					. "     AND rdereg=0 "
					. "     AND renfec <= '" . $wfeccor . "'  "
					//. "	    AND (rencod = '".$row['fenres']."' or rencod='".$row['fencod']."')"//ojooooooooo
					.$filtroResponsables
					// ."     AND rencco = '".$row[4]."'  "
					. "     AND renfue=rdefue  "
					. "     AND rennum=rdenum  "
					. "     AND rencco=rdecco  "
					. "     ORDER BY  b.id desc"
					. "		LIMIT 1";
					/*if($row[1]=='T-125582')
						echo $q;*/
					$err2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$y = mysql_num_rows($err2);
					$row2 = mysql_fetch_array($err2);
					
					if ($y > 0)
					{
						if ($row2[0] > 0)
						{
							/*$q = "  SELECT  MIN(cast(rdesfa as UNSIGNED)) "
							. "     FROM   " . $wbasedato . "_000021 "
							. "   	WHERE   rdefac= '" . $row2[3] . "' "
							. "     AND rdeffa= '" . $row2[4] . "' "
							. "     AND rdeest= 'on' "
							. "     AND rdesfa<>'' "
							. "     AND rdereg=0 "
							. "     AND rdefue='" . $row2[1] . "'  "
							. "     AND rdenum='" . $row2[2] . "'  "
							. "     group by rdenum, rdefue  ";*/
							$q = "  SELECT rdesfa "// va a tocar hacer join con la 20 y verificar el responsable
							. "       FROM " . $wbasedato . "_000021 "
							. "   	 WHERE rdefac= '" . $row2[3] . "' "
							. "        AND rdeffa= '" . $row2[4] . "' "
							. "        AND rdeest= 'on' "
							. "        AND rdesfa<>'' "
							. "        AND rdereg=0 "
							. "        AND rdefue='" . $row2[1] . "'  "
							. "        AND rdenum='" . $row2[2] . "'  "
							//. "     group by rdenum, rdefue  "
							. "	     ORDER BY id desc"
							. "		LIMIT 1";

							$err2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$row2 = mysql_fetch_array($err2);
						}
					}else{
								$pintar = 1;
						}
					
					//Hallando el total de la glosa
					//Consulta 0015
					$sql = "SELECT  rdevca, carfue, carglo, carncr, carndb, rdenum, rdefac, carrec, rdereg, carabo, carcfa, carcca, rdevco, rdeglo, rdecon, rdeffa "
					. "     FROM   {$wbasedato}_000021 a, {$wbasedato}_000040 , {$wbasedato}_000020 "
					. "   	WHERE rdefac= '" . $row[1] . "' "
					. "   	AND rdeffa= '" . $row[0] . "' "
					. "     AND rdeest = 'on' "
					. "   	AND rdefue = renfue"
					. "   	AND rdenum = rennum"
					. "   	AND rencod = '" . $empCod[$i] . "' "
					. "     AND carfue = rdefue  "
					. "     AND a.fecha_data <= '$wfeccor'  "; //echo "<br><br><pre>......$sql</pre>";
//					. "     group by rdenum, rdefue  "; 

					$glosa = 0;
					$aceptado = 0;
					$ndebito = 0;
					$ncredito = 0;
					$recibos = 0;

					$result = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0015 - ".mysql_error() );

					$rows = "";
					for(; $rows = mysql_fetch_array($result);){
						//echo "entro<br>".$rows[0];
						if( $rows['carglo'] == 'on' ){
							$glosa += $rows[0];

							//Hallando el total aceptado de la glosa
							//Consulta No. 0016

							$sql = "SELECT
										Rdevco
									FROM
										{$wbasedato}_000021 a, {$wbasedato}_000040 b
									WHERE
										rdeglo = '{$rows['carfue']}-{$rows['rdenum']}'
										AND rdefac = '{$rows['rdefac']}'
										AND rdeest = 'on'
										AND a.fecha_data <= '$wfeccor'
										AND rdefue = carfue
										AND carncr = 'on'
										AND carcca = 'on'
									"; //echo "<br>........<pre>".$sql."</pre>";
								
							$result2 = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0016 - ".mysql_error() );

							$rows2 = "";
							for(; $rows2 = mysql_fetch_array($result2);){
								$aceptado += $rows2[0];
							}
							
							
							$sql = 	"SELECT fdevco, rdefac, rdefue, rdenum "
									." FROM {$wbasedato}_000065 b, {$wbasedato}_000021 a, {$wbasedato}_000040 c "
									."WHERE rdefac = '{$rows['rdefac']}' "
									."	AND rdeglo = '{$rows['carfue']}-{$rows['rdenum']}' "
									."	AND a.fecha_data <= '$wfeccor' "
									."  AND fdefue = rdefue "
									."  AND fdedoc = rdenum "
									."  AND rdeest = 'on' "
									."  AND rdefue = carfue "
									."  AND carcfa = 'on' "
									."  AND carncr = 'on' "
									."  AND rdeglo <> '' ";
								
							$result2 = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0016 - ".mysql_error() );

							$rows2 = "";
							for(; $rows2 = mysql_fetch_array($result2);){
								$aceptado += $rows2[0];
							}
						}

						if( $rows['carndb'] == 'on' ){
							$ndebito += $rows[0];
						}

						if( $rows['carncr'] == 'on' && $rows['rdeglo'] == '' ){
								
							//Hallando el total de notas credito
							//Consulta No. 0017

							/*if ( $rows['carcca'] == 'on' ){
								$ncredito += $rows['rdevco'];
							}*/
						//	else if( $rows['rdeglo'] == '' ){
									
								$sql = "SELECT fdevco "
								."FROM {$wbasedato}_000065 "
								."WHERE fdefue = '{$rows['carfue']}' "
								."AND fdedoc = '{$rows['rdenum']}' "
								."AND fdefac =  '{$rows['rdefac']}' "
								."AND fdeffa =  '{$rows['rdeffa']}' "
								."AND fecha_data <= '$wfeccor' ";
								
								$result2 = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0017 - ".mysql_error() );

								$rows2 = "";
									
								for(; $rows2 = mysql_fetch_array($result2);){
									$ncredito += $rows2[0];
								}
						//	}
						}

						if( $rows['carrec'] == 'on' && $rows['carabo'] != 'on' ){
							
							if( $rows['rdecon'] != '' ){
								$exp = explode( "-", $rows['rdecon'] );
								
								$signo = consultarMulti( $exp[0], $rows['carfue'] );
							}
							else{
								$signo = 1;
							}
							
							$recibos += $rows['rdevco']+$rows['rdevca']*$signo;
						}
					}

					if ($vol == 'SI')
					{	
						//echo "<br>pinto: $pinto y pintar: $pintar";
						if ($pinto == 0 && $pintar == 1 )
						{
							echo "<br><table  align =center>";
							if ($wtip == 'CODIGO')
								echo "<tr class='colorAzul4'><td colspan=12><b>Empresa: " . $empCod[$i] . "-" . $empNit[$i] . "-" . $empNom[$i] . "</b></td></tr>";
							if ($wtip == 'NIT')
								echo "<tr><td colspan=12 class='colorAzul4'><b>Empresa: " . $empNit[$i] . "-" . $empNom[$i] . "<b></td></tr>";

							echo "<tr class='encabezadotabla'><th align=CENTER width='70'>FUENTE FACTURA</th>";
							echo "<th align=CENTER width='85'>NRO FACTURA</th>";
							echo "<th align=CENTER width='90'>FECHA FACTURA</th>";
							echo "<th align=CENTER width='80'>CODIGO</th>";
							echo "<th align=CENTER width='250'>NOMBRE</th>";
							echo "<th align=CENTER width='110'>VLR FACTURA</th>";
							echo "<th align=CENTER width='85'>N. DEBITO</th>";		//fila agregada
							echo "<th align=CENTER width='85'>N. CREDITO</th>";	//fila agregada
							echo "<th align=CENTER width='85'>RECIBOS</th>";	//fila agregada
							echo "<th align=CENTER width='85'>GLOSA</th>";			//fila agregada
							echo "<th align=CENTER width='85'>ACEPTADO</th>";		//fila agregada
							echo "<th align=CENTER width='110' >SALDO FACTURA</th>";

							$pinto = 1;
						}

						if ($y > 0)
						{
							if ($row2[0] > 0)
							{
								echo '<tr>';
								echo "<th align=CENTER " . $clase1 . " >" . $row[0] . "</th>";
								echo "<th align=CENTER " . $clase1 . " >" . $row[1] . "</th>";
								echo "<th align=CENTER " . $clase1 . " >" . $row[3] . "</th>";
								echo "<th align=CENTER   " . $clase1 . " >" . $row['fenres'] . "</th>";
								echo "<th align=LEFT   " . $clase1 . " >" . cambiarCadena($row['fennpa']) . "</th>";
								echo "<th align=CENTER " . $clase2 . " >" . number_format($row[6], 0, '.', ',') . "</th>";
								echo "<th align=CENTER " . $clase2 . " >" . number_format($ndebito, 0, '.', ',') . "</th>";	//fila agregada
								echo "<th align=CENTER " . $clase2 . " >" . number_format($ncredito, 0, '.', ',') . "</th>";	//fila agregada
								echo "<th align=CENTER " . $clase2 . " >" . number_format($recibos, 0, '.', ',') . "</th>";	//fila agregada
								echo "<th align=CENTER " . $clase2 . " >" . number_format($glosa, 0, '.', ',') . "</th>";	//fila agregada
								echo "<th align=CENTER " . $clase2 . " >" . number_format($aceptado, 0, '.', ',') . "</th>";	//fila agregada
								echo "<th align=CENTER " . $clase2 . " >" . number_format($row2[0], 0, '.', ',') . "</th>";
								echo '</tr>';

								$globalaceptados += $aceptado;
								$totalaceptados += $aceptado;
								$totaldebito += $ndebito;
								$globaldebito += $ndebito;
								$totalcredito += $ncredito;
								$globalcredito += $ncredito;
								$totalrecibos += $recibos;
								$globalrecibos += $recibos;
								$totalglosa += $glosa;
								$globalglosa += $glosa;

								if ($clase1 == "class='fila2' style='font-size:10pt;font-weight:normal'")
								{
									//$clase1 = "class='texto1'";
									//$clase2 = "class='texto4'";
									$clase1 = "class='fila1' style='font-size:10pt;font-weight:normal'";
									$clase2 = "class='fila1' style='text-align:right;font-size:10pt;font-weight:normal'";
								}
								else
								{
									//$clase1 = "class='texto2'";
									//$clase2 = "class='texto5'";
									$clase1 = "class='fila2' style='font-size:10pt;font-weight:normal'";
									$clase2 = "class='fila2' style='text-align:right;font-size:10pt;font-weight:normal'";
								}
							}
						}
						else
						{
							//echo '<tr>';
							//echo "<th align=CENTER " . $clase1 . " width='20%'>" . $row[0] . "</th>";
							//echo "<th align=CENTER " . $clase1 . " width='20%'>" . $row[1] . "</th>";
							//echo "<th align=CENTER " . $clase1 . " width='20%'>" . $row[3] . "</th>";
							//echo "<th align=CENTER " . $clase2 . " width='20%' >" . number_format($row[6], 0, '.', ',') . "</th>";
							//echo "<th align=CENTER " . $clase2 . "width='20%' >" . number_format($row[2], 0, '.', ',') . "</th>";
							//echo '</tr>';

							echo "<tr>";
							echo "<th align=CENTER " . $clase1 . " >" . $row[0] . "</th>";
							echo "<th align=CENTER " . $clase1 . " >" . $row[1] . "</th>";
							echo "<th align=CENTER " . $clase1 . " >" . $row[3] . "</th>";
							echo "<th align=CENTER   " . $clase1 . " >" . $row['fenres'] . "</th>";
							echo "<th align=LEFT   " . $clase1 . " >" . cambiarCadena($row['fennpa']) . "</th>";
							echo "<th align=CENTER " . $clase2 . " >" . number_format($row[6], 0, '.', ',') . "</th>";
							echo "<th align=CENTER " . $clase2 . " >" . number_format($ndebito, 0, '.', ',') . "</th>";	//fila agregada
							echo "<th align=CENTER " . $clase2 . " >" . number_format($ncredito, 0, '.', ',') . "</th>";	//fila agregada
							echo "<th align=CENTER " . $clase2 . " >" . number_format($recibos, 0, '.', ',') . "</th>";	//fila agregada
							echo "<th align=CENTER " . $clase2 . " >" . number_format($glosa, 0, '.', ',') . "</th>"; //fila agregada
							echo "<th align=CENTER " . $clase2 . " >" . number_format($aceptado, 0, '.', ',') . "</th>"; //fila agregada
							echo "<th align=CENTER " . $clase2 . " >" . number_format($row[2], 0, '.', ',') . "</th>";
							echo '</tr>';

							$globalaceptados += $aceptado;
							$totalaceptados += $aceptado;
							$totaldebito += $ndebito;
							$globaldebito += $ndebito;
							$totalcredito += $ncredito;
							$globalcredito += $ncredito;
							$totalrecibos += $recibos;
							$globalrecibos += $recibos;
							$totalglosa += $glosa;
							$globalglosa += $glosa;
								
							if ($clase1 == "class='fila2' style='font-size:10pt;font-weight:normal'")
							{
								//$clase1 = "class='texto1'";
								//$clase2 = "class='texto4'";
								$clase1 = "class='fila1' style='font-size:10pt;font-weight:normal'";
								$clase2 = "class='fila1' style='text-align:right;font-size:10pt;font-weight:normal'";
							}
							else
							{
								//$clase1 = "class='texto2'";
								//$clase2 = "class='texto5'";
								$clase1 = "class='fila2' style='font-size:10pt;font-weight:normal'";
								$clase2 = "class='fila2' style='text-align:right;font-size:10pt;font-weight:normal'";
							}
						}
					}

					if ($y > 0)
					{
						if ($row2[0] > 0)
						{
							$wtotsal = $wtotsal + $row2[0];
							$wsaldo = $wsaldo + $row2[0];
							$wtotfac = $wtotfac + $row[6];
							$wtotal = $wtotal + $row[6];
							$cuenta = $cuenta + 1;

							if ($vol != 'SI'){
								$globalaceptados += $aceptado;
								$totalaceptados += $aceptado;
								$totaldebito += $ndebito;
								$globaldebito += $ndebito;
								$totalcredito += $ncredito;
								$globalcredito += $ncredito;
								$totalrecibos += $recibos;
								$globalrecibos += $recibos;
								$totalglosa += $glosa;
								$globalglosa += $glosa;
							}
						}
					}
					else
					{
						$wtotsal = $wtotsal + $row[2];
						$wsaldo = $wsaldo + $row[2];
						$wtotfac = $wtotfac + $row[6];
						$wtotal = $wtotal + $row[6];
						$cuenta = $cuenta + 1;

						if ($vol != 'SI'){
							$globalaceptados += $aceptado;
							$totalaceptados += $aceptado;
							$totaldebito += $ndebito;
							$globaldebito += $ndebito;
							$totalcredito += $ncredito;
							$globalcredito += $ncredito;
							$totalrecibos += $recibos;
							$globalrecibos += $recibos;
							$totalglosa += $glosa;
							$globalglosa += $glosa;
						}
					}

					$row = mysql_fetch_array($err);
				}

				if ($wtotsal != 0)
				{
					if ($vol != 'SI')
					{
						echo "<br><table align=center>";
						if ($wtip == 'CODIGO')
							echo "<tr><td colspan=12 class='encabezadotabla'><b>Empresa: " . $empCod[$i] . "-" . $empNit[$i] . "-" . $empNom[$i] . "</b></td></tr>";
						if ($wtip == 'NIT')
							echo "<tr class='encabezadotabla'><td colspan=12 class='colorAzul4'><b>Empresa: " . $empNit[$i] . "-" . $empNom[$i] . "</b></td></tr>";
						echo "<tr class='fila2' align=center style='font-size:8pt'>";
						echo "<td COLSPAN=5>&nbsp</td>";
						echo "<td width='110'><b>TOTAL VALOR FACTURA</b></td>";
						echo "<td width='85'><b>TOTAL VALOR N. DEBITO</b></td>";
						echo "<td width='85'><b>TOTAL VALOR N.CREDITO</b></td>";
						echo "<td width='85'><b>TOTAL VALOR RECIBOS</b></td>";
						echo "<td width='85'><b>TOTAL GLOSA</b></td>"; //Fila agregada
						echo "<td width='85'><b>TOTAL ACEPTADO</b></td>"; //Fila agregada
						echo "<td width='110'><b>TOTAL SALDO FACTURA</b></td></tr>";
					}
					//                    echo "<td class='acumulado3' colspan='3' width='40%'>TOTAL EMPRESA</td>";
					//                    echo "<td  class='acumulado4' width='30%'>" . number_format($wtotfac, 0, '.', ',') . "</td>";
					//                    echo "<td  class='acumulado4' width='30%'>" . number_format($wtotsal, 0, '.', ',') . "</td></tr>";
					echo "<tr class='encabezadotabla' style='font-size_10pt'><td colspan='5'><b>TOTAL EMPRESA</b></td>";
					echo "<td align=RIGHT><b>" . number_format($wtotfac, 0, '.', ',') . "</b></td>";
					echo "<td align=RIGHT><b>" . number_format($totaldebito, 0, '.', ',') . "</b></td>";	//nueva
					echo "<td align=RIGHT><b>" . number_format($totalcredito, 0, '.', ',') . "</b></td>";	//Fila agregada
					echo "<td align=RIGHT><b>" . number_format($totalrecibos, 0, '.', ',') . "</b></td>";	//Fila agregada
					echo "<td align=RIGHT><b>" . number_format($totalglosa, 0, '.', ',') . "</b></td>";	//Fila agregada
					echo "<td align=RIGHT><b>" . number_format($totalaceptados, 0, '.', ',') . "</b></td>";	//Fila agregada
					echo "<td align=RIGHT><b>" . number_format($wtotsal, 0, '.', ',') . "</b></td></tr>";
				}
			}
		}

		if ($cuenta == 0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento en el rango de fechas seleccionado</td><tr>";
		}

		else if ($cuenta != 0)
		{
			//            echo "<tr><th align=CENTER class='acumulado2' colspan='3'>TOTAL </th>";
			//            echo "<th align=CENTER class='acumulado1'>" . number_format($wtotal, 0, '.', ',') . "</th>";
			//            echo "<th align=CENTER class='acumulado1'>" . number_format($wsaldo, 0, '.', ',') . "</th>";
			echo "<tr class='colorAzul5' style='font-size:10pt;font-style:bold'><td align=CENTER colspan='5'><b>TOTAL</b></td>";
			echo "<td align=RIGHT style='font-size:10pt;font-style:bold'><b>" . number_format($wtotal, 0, '.', ',') . "</b></td>";
			echo "<td align=RIGHT style='font-size:10pt;font-style:bold'><b>" . number_format($globaldebito, 0, '.', ',') . "</b></td>";	//fila nueva
			echo "<td align=RIGHT style='font-size:10pt;font-style:bold'><b>" . number_format($globalcredito, 0, '.', ',') . "</b></td>";	//fila nueva
			echo "<td align=RIGHT style='font-size:10pt;font-style:bold'><b>" . number_format($globalrecibos, 0, '.', ',') . "</b></td>";	//fila nueva
			echo "<td align=RIGHT style='font-size:10pt;font-style:bold'><b>" . number_format($globalglosa, 0, '.', ',') . "</b></td>";	//fila nueva
			echo "<td align=RIGHT style='font-size:10pt;font-style:bold'><b>" . number_format($globalaceptados, 0, '.', ',') . "</b></td>";	//fila nueva
			echo "<td align=RIGHT style='font-size:10pt;font-style:bold'><b>" . number_format($wsaldo, 0, '.', ',') . "</b></td><tr>";
		}
		echo "</table>";
		echo "</br><center><A href='RepCarGen.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wtip=" . $wtip . "&amp;wfeccor=" . $wfeccor . "&amp;wemp=" . $wemp . "&amp;bandera='1'>VOLVER</A></center>";
		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
	}
}
//$registro[37]="<A HREF='javascript: abrirVentana(\"http://200.24.5.118/matrix/det_registro.php?id=0&pos1=nomina&pos2=0&pos3=0&pos4=000005&pos5=0&pos6=nomina&tipo=P&Valor=&Form=000005-nomina-C-Nivel de Escolaridad&call=0&change=0&key=nomina&r[0]=".$registro[1]."&r[1]=".$registro[2]."&r[2]=".$registro[5]."&Pagina=1\")'></A>";
liberarConexionBD($conex);
?>
</body>
</html>
