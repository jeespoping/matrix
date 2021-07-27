		<head>
  <title>RECIBO DEVOLUCIONES</title>
  
</head>
<body>
<?php
include_once("conex.php");
/**
* RECIBO DEVOLUCIONES DE PACIENTES           *
* DE LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
*/
// ==========================================================================================================================================
// M O D I F I C A C I O N E S 
// ==========================================================================================================================================
// Noviembre 07 de 2013: Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 para que traiga los     
//						datos de contingencia (tabla movhos_00143) con estado activo. Jonatan Lopez	 	 
// ==========================================================================================================================================
// Julio 12 de 2012 Viviana Rodas Se cambian los parametros que se envian a la funcion consultaCentroCostos(), para refinar mas la consulta 
// de los centros de costos.
// ==========================================================================================================================================
// Junio 27 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos 
// 	de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera funcion.
// ==========================================================================================================================================
// Diciembre 28 de 2011 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// - Se cambian los estilos a los actuales
// - Se cambia la hora del encabezado de la devolucion (tabla movhos 2) por la detalle (movhos3), lineas 183 y 200
//===========================================================================================================================================
 

session_start();

if (!isset($user))
if (!isset($_SESSION['user']))
session_register("user");

session_register("wemp_pmla", "wcen_mezc");

if (!isset($_SESSION['user']))
echo "error";
else
{
	

	include_once("root/comun.php");
	

	// $conexunix = odbc_pconnect('facturacion','facadm','1201')
	// or die("No se ralizo Conexion con el Unix");
	include_once("root/magenta.php");
	include_once("movhos/otros.php");
	$bd = $wbasedato;
	$wactualiz = "Noviemnbre 7 de 2013"; // Aca se coloca la ultima fecha de actualizacion de este programa //
	$titulo = "REPORTE DE DEVOLUCIONES";
	encabezado($titulo,$wactualiz, "clinica");  
	connectOdbc($conex_o, 'facturacion');
	connectOdbc($conex_i, 'inventarios');
	// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	//$test = centroCostosCM();echo $test;
	// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	echo "<form name='Reporte_devoluciones' action='Reporte_devoluciones.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";
	// echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	$wfecha = date("Y-m-d");
	$whora = (string)date("H:i:s");

	
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	// Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	$q = " SELECT detapl, detval "
	. "   FROM root_000050, root_000051 "
	. "  WHERE empcod = '" . $wemp_pmla . "'"
	. "    AND empest = 'on' "
	. "    AND empcod = detemp ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);

			if ($row[0] == "cenmez")
			$wcenmez = $row[1];

			if ($row[0] == "afinidad")
			$wafinidad = $row[1];

			if ($row[0] == "movhos")
			$wbasedato = $row[1];

			if ($row[0] == "tabcco")
			$wtabcco = $row[1];
		}
	}
	else
	{
		echo '<div id="page">';
		echo '<div id="content">';
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
		echo '</div>';
		echo '</div>';
	}

	if (!isset($wfeci) or !isset($wfecf) or !isset($wcco) or trim($wcco) == "")
	{
		if (!isset($wfeci) && !isset($wfecf))
		{
			$wfeci = date('Y-m-d');
			$wfecf = date('Y-m-d');
		}
		// /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		echo "<br>";
		echo "<br>";
		echo "<br>";

				
		//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		$cco="Ccoest = 'on'
				AND (
				(
				Ccohos = 'on'
				OR Ccoing = 'on'
				)
				AND Ccoayu <> 'on'
				AND ccofac = 'on'
				AND ccotra = 'off'
				)
				OR (
				Ccofac = 'on'
				AND Ccotra = 'on'
				)
				OR (
				Ccocir = 'on'
				OR Ccourg = 'on'
				OR Ccoadm = 'on'
				)
				";
		$sub="off";
		$tod="";
		$ipod="off";
		//$cco=" ";
		$filtro="--";
		$centrosCostos = consultaCentrosCostos($cco,$filtro);
					
		echo "<table align='center' border=0>";		
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
		echo $dib;
		echo "</table>"; 
		
		echo '<table align=center cellspacing="5" >';
		echo "<tr><td class=fila1 align=center><b>Fecha Inicial : </b></td><td class=fila2>";
		campoFechaDefecto("wfeci", $wfeci);
		echo"</td></tr>";
		echo "<tr><td class=fila1 align=center><b>Fecha Final : </b></td><td class=fila2>";
		campoFechaDefecto("wfecf", $wfecf);
		echo "</td></tr>";
		//echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='ENTRAR'></td></tr>";
		echo "</table>";
		echo "<br/>";
		echo "<center><input type='submit' id='searchsubmit' value='ENTRAR'> </center>";
		
	}
	else
	{
			
		if ($wcco == 'x')
		{
			$wcco = '%';
		}
		else
		{
			$wccosto = explode("-", $wcco);
			$wcco = $wccosto[0];
			
		}

		$wcco = trim($wcco);
		$q = " SELECT cconom "
		. "   FROM " . $wtabcco
		. "  WHERE ccocod = '" . $wcco . "'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);
		$wnomcco = $row[0];
	

		//$wcco = trim($wcco);
		$ccoCM = centroCostosCM();

		if ($wcco == $ccoCM)
		{
			$q = " SELECT devcon, fenhis, fening, fenfec, " . $wbasedato . "_000003.hora_data as hora, Count(distinct(Fdenum)), Devcfs, devjus, devcrf, devcff, devjuf , devobs, devfre, devhre, denori, denusu, fdeari, artcom, fdenum "
			. "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000002, " . $wbasedato . "_000035, " . $wbasedato . "_000003, ".$wcenmez."_000002 "
			. "  WHERE devfre <> '0000-00-00' "
			. "    AND devhre <> '00:00:00' "
			. "    AND devusu <> '' "
			. "    AND devnum = fennum "
			. "    AND fenest = 'on' "
			. "    AND fencco = '" . $wcco . "'"
			. "    AND  dencon = devcon "
			. "    AND  devnum = fdenum "
			. "    AND  artcod = fdeari "
			. "    AND  fenfec between '".$wfeci."' and '".$wfecf."' "
			. "  group BY 1, 18 "	
		/*********************************************************************************************************************/
		/* Noviembre 07 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/			
			. "  UNION "
			. " SELECT devcon, fenhis, fening, fenfec, " . $wbasedato . "_000143.hora_data as hora, Count(distinct(Fdenum)), Devcfs, devjus, devcrf, devcff, devjuf , devobs, devfre, devhre, denori, denusu, fdeari, artcom, fdenum "
			. "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000002, " . $wbasedato . "_000035, " . $wbasedato . "_000143, ".$wcenmez."_000002 "
			. "  WHERE devfre <> '0000-00-00' "
			. "    AND devhre <> '00:00:00' "
			. "    AND devusu <> '' "
			. "    AND devnum = fennum "
			. "    AND fenest = 'on' "
			. "    AND fencco = '" . $wcco . "'"
			. "    AND  dencon = devcon "
			. "    AND  devnum = fdenum "
			. "    AND  artcod = fdeari "
			. "    AND  fenfec between '".$wfeci."' and '".$wfecf."' "
			. "	   AND  fdeest = 'on'" //El registro debe estar activo en la tabla de contingencia para ser mostrado.
			. "    group BY 1, 18 "
			. "  ORDER BY 1, 2, 3, 4 ";		
		}
		else
		{
		 $q = " SELECT devcon, fenhis, fening, fenfec, " . $wbasedato . "_000003.hora_data as hora, Devces, Devcfs, devjus, devcrf, devcff, devjuf , devobs, devfre, devhre, denori, denusu, fdeart, artcom "
			. "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000002, " . $wbasedato . "_000035, " . $wbasedato . "_000003, " . $wbasedato . "_000026 "
			. "  WHERE devfre <> '0000-00-00' "
			. "    AND devhre <> '00:00:00' "
			. "    AND devusu <> '' "
			. "    AND devnum = fennum "
			. "    AND fenest = 'on' "
			. "    AND fencco = '" . $wcco . "'"
			. "    AND  dencon = devcon "
			. "    AND  devnum = fdenum "
			. "    AND  devlin = fdelin "
			. "    AND  artcod = fdeart "
			. "    AND  fenfec between '".$wfeci."' and '".$wfecf."' "
			/*********************************************************************************************************************/
			/* Noviembre 07 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
			/*********************************************************************************************************************/	
			. "  UNION "
			. " SELECT devcon, fenhis, fening, fenfec, " . $wbasedato . "_000143.hora_data as hora, Devces, Devcfs, devjus, devcrf, devcff, devjuf , devobs, devfre, devhre, denori, denusu, fdeart, artcom "
			. "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000002, " . $wbasedato . "_000035, " . $wbasedato . "_000143, " . $wbasedato . "_000026 "
			. "  WHERE devfre <> '0000-00-00' "
			. "    AND devhre <> '00:00:00' "
			. "    AND devusu <> '' "
			. "    AND devnum = fennum "
			. "    AND fenest = 'on' "
			. "    AND fencco = '" . $wcco . "'"
			. "    AND  dencon = devcon "
			. "    AND  devnum = fdenum "
			. "    AND  devlin = fdelin "
			. "    AND  artcod = fdeart "
			. "    AND  fenfec between '".$wfeci."' and '".$wfecf."' "
			. "	   AND  fdeest = 'on'" //El registro debe estar activo en la tabla de contingencia para ser mostrado.
			. "  ORDER BY 1, 2, 3, 4 ";
		}
		$res = mysql_query($q, $conex)or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($res);

		echo '<h2 class="section" align=center><b>Servicio o Unidad: ' . $wnomcco . '</b></h2>';

		echo '<div class="content">';
		echo '<table align=center>';
		echo "<tr class=encabezadoTabla>";
		echo "<th>Dev Nro</th>";
		echo "<th>Servicio</th>";
		echo "<th>Historia</th>";
		echo "<th>Activo</th>";
		echo "<th>Ingreso</th>";
		echo "<th>Articulo</th>";
		echo "<th>Fecha</th>";
		echo "<th>Hora</th>";
		echo "<th>C. dev</th>";
		echo "<th>Faltante Origen</th>";
		echo "<th>jus faltante origen</th>";
		echo "<th>C. recibida</th>";
		echo "<th>Faltante Rec.</th>";
		echo "<th>DIFERENCIA</th>";
		echo "<th>VALOR</th>";
		echo "<th>Obs</th>";
		echo "<th>Fecha rec.</th>";
		echo "<th>Hora rec.</th>";
		echo "</tr>";

		if ($num > 0)
		{
			for($i = 1;$i <= $num;$i++)
			{
				$row = mysql_fetch_array($res);
				if (is_integer($i / 2))
				$wclass = "fila1";
				else
				$wclass = "fila2";

				echo "<tr>";
				echo "<td align=center class=" . $wclass . ">" . $row[0] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[14] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[1] . "</td>";

				$query = "SELECT * "
				."FROM	inpac "
				."WHERE	pachis = '".$row[1]."' ";
				$err_f = odbc_do($conex_o,$query);
				if (odbc_fetch_row($err_f))
				{
					echo "<td align=center class=" . $wclass . ">A</td>";
				}
				else
				{
					echo "<td align=center class=" . $wclass . ">I</td>";
				}

				echo "<td align=center class=" . $wclass . ">" . $row[2] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[16] . "-" . $row[17] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[3] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[4] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[5] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[6] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[7] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[8] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[9] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . ($row[5] - $row[8]) . "</td>";

				//consultamos el valor del costo promedio para el mes
				$q= "SELECT artpropro "
				."     FROM ivartpro "
				."    WHERE artproano = '".date('Y')."' "
				."      and artpromes = '".date('m')."' "
				."      and artproart =  '".$row[16]."'  ";

				$err_i= odbc_do($conex_i,$q);
				if(odbc_fetch_row($err_i))
				{
					$val=($row[5] - $row[8])*odbc_result($err_i,1);
					$val= number_format($val,2,'.',',');
				}
				else
				{
					$val='-';
				}
				echo "<td align=center class=" . $wclass . ">" . $val . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[11] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[12] . "</td>";
				echo "<td align=center class=" . $wclass . ">" . $row[13] . "</td>";
			}
		}
		else
		{
			echo "NO HAY DEVOLUCIONES RECIBIDAS";
		}

		echo "<tr>";
		echo "<br/>";
		echo "<th align=center colspan=18><A href='Reporte_devoluciones.php?wbasedato=" . $wbasedato . "&wemp_pmla=" . $wemp_pmla . "&wfeci=" . $wfeci . "&wfecf=" . $wfecf . "'><b>Retornar</b></A></td>";
		echo "</tr>";
		echo "</table>";
	} // if de register
}
function centroCostosCM()
	{
		global $conex;
		global $wbasedato;
		
		$sql = "SELECT
					Ccocod
				FROM
					".$wbasedato."_000011
				WHERE
					ccotra = 'on'
					AND ccoima = 'on'	
					AND ccoest = 'on'
				";
		
		$res= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			return $rows[ 'Ccocod' ];
		}
	}
echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";
echo "<input type='HIDDEN' name='wbasedato' value='" . $wbasedato . "'>";

include_once("free.php");

    ?>
