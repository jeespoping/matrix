<html>
<head>
  <title>REPORTE DE CARTERA POR EDADES</title>

<script LANGUAGE="JavaScript1.2">
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

<?php
include_once("conex.php");

/**
* NOMBRE:  REPORTE DE CARTERA POR EDADES
*
* PROGRAMA: RepCarXEdad.php
* TIPO DE SCRIPT: PRINCIPAL
* //DESCRIPCION:Este reporte presenta la lista de notas debito o notas credito o recibos de caja con sus detalles por empresa o para todas las empresas
*    con sus saldos de cartera por edades o rangos de tiempo escogidos por el usuario
*
* HISTORIAL DE ACTAULIZACIONES:
* 2014-02-10 Camilo Zapata  - Se hizo una corrección a los cambios realizados por edwin, los cuales estaban usando las fechas de la última radicación de cada movimiento para sacar la edad,
*                             cuando se debe usar es la fecha de radicación y el corte.
* 2012-11-30 Edwin Molina G	- Se modifica el reporte para que las edades las cuenta a partir de la ultima fecha de radicado
* 2012-09-26 Camilo Zapata.	- se modificó el script para que permita seleccionar el estado actual de las facturas(radicada-glosada-generada-devuelta)
* 2012-09-24 Camilo Zapata.	- se mejoró el query para que tenga en cuenta que los movimientos correspondan al mismo responsable de la factura consultando los posibles rencod en la 24.
* 2012-09-20 Camilo Zapata. - se comentó el cambio anterior.
*							- se cambiaron los querys que consultan la 21 para que tenga en cuenta el último movimiento realizado, no el de menor saldo lineas(553 y 1056)
* 2012-09-14 Camilo Zapata. se modificó el script para que compare por saldo de la factura de la 18 en caso de no encontrar valor de saldo en la 21(buscar donde dice ojo)
* 2006-06-20 carolina castano, creacion del script
* 2006-10-12 carolina castano, cambios de forma, presentación
* 2007-02-20 carolina castano, se adecua para que los rangos de las edades sean escogidos por el usuario de los configurados en base de datos
* 2007-08-15 carolina castano, se muestra el tipo de empresa en el reporte resumido
* 2008-03-28 se muestra comenta el query que retomaba la fecha de corte
* 2011-07-22 Creación de tablas temporales para mejorar la velocidad en consulta del script - Mario Cadavid
* 2011-07-25 Modificación de diseño adaptando el reporte a la hoja de estilos actual de Matrix - Mario Cadavid
* 2011-07-26 Modificación de los case's que definen los rangos de edades y se deshace el cambio hecho en 2008-03-28
*			 ya que se debe tener la fecha de corte por cada ciclo - Mario Cadavid
* 2012-08-24 Se agregó la consulta de los registros de empresas con el mismo NIT de la empresa responsable, esto para
* 			 que en las consultas por NIT muestre los registros asociados al NIT de la empresa responsable, es decir, no solo la
*			 cartera directa de la empresa sino tambien la de sus empleados. - Mario Cadavid
*
* Tablas que utiliza:
* $wbasedato."_000024: Maestro de Fuentes, select
* $wbasedato."_000018: select de facturas entre dos fechas
* $wbasedato."_000020: select en encabezado de cartera
* $wbasedato."_000021: select en detalle de cartera
* $wbasedato."_000080: select de rangos para las edades
*
* @author ccastano
* @package defaultPackage
*/

/************************************************************************************************
 * Consulto la ultima fecha de la radicación
 ************************************************************************************************/
function consultarFechaRadicado( $conex, $wbasedato, $factura, $fuenteFactura, $fechaCorte ){

	$val = "";

	$sql = "SELECT
				a.Fecha_data
			FROM
				{$wbasedato}_000021 a, {$wbasedato}_000040 b
			WHERE
				Rdefac = '$factura'
				AND rdeffa = '$fuenteFactura'
				AND rdefue = carfue
				AND carest = 'on'
				AND carrad = 'on'
				AND a.Fecha_data <= '$fechaCorte'
			ORDER BY
				a.fecha_data desc
                Limit 1;
			";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 'Fecha_data' ];
	}

	return $val;
}

$wautor = "Carolina Castano P.";
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

$query = "TRUNCATE edadesSinRes";
$rs = mysql_query( $query, $conex );
    echo "<form action='RepCarXEdad.php' method=post name='forma'>";

    $wfecha = date("Y-m-d");

    echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

    if (!isset($wfecini) or !isset($wfecfin) or !isset($wfeccor) or !isset($wemp) or !isset($wtip) or !isset ($resultado))
    {
		$wactualiz = "2014-02-10";
		encabezado("REPORTE DE CARTERA POR EDADES", $wactualiz, "logo_".$wbasedato);
        echo "<center><table border=0 width='90%'>";
        // INGRESO DE VARIABLES PARA EL REPORTE//
        if (!isset ($bandera))
        {
            $wfecini = $wfecha;
            $wfecfin = $wfecha;
            $wfeccor = $wfecha;
        }

        echo "<tr height='34'>";
        echo "<td align=center class='fila2'>FECHA INICIAL DE FACTURACION: ";
        campoFechaDefecto("wfecini", $wfecini);
        echo "</td>";

        echo "<td  align=center class='fila2'>FECHA FINAL DE FACTURACION: ";
        campoFechaDefecto("wfecfin", $wfecfin);
        echo "</td>";

        echo "<td class='fila2' align=center>FECHA DE CORTE : ";
        campoFechaDefecto("wfeccor", $wfeccor);
        echo "</td>";

        // SELECCIONAR tipo de reporte
        echo "</tr>";

        echo "<tr height='34'>";
        echo "<td align=center class='fila2' >PARAMETROS DEL REPORTE: ";
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
        echo "</select></td>";
        // CONSULTA Y DESPLIEGUE DE RANGOS DE EDAD
        echo "<td align=right class='fila2' colspan=2 >SELECCIONE LOS RANGOS DE EDADES: <select name='wran'>";

        if (isset($wran))
        {
            echo "<option selected>" . $wran . "</option>";

            $q = "   SELECT rcacod, rcarai, rcaraf, rcaord "
             . "     FROM " . $wbasedato . "_000080 "
             . "    WHERE rcacod != (mid('" . $wran . "',1,instr('" . $wran . "','-')-1)) "
             . "      AND rcaest = 'on' order by rcacod, rcaord";
        }
        else
        {
            $q = "   SELECT rcacod, rcarai, rcaraf, rcaord "
             . "     FROM " . $wbasedato . "_000080 "
             . "    WHERE rcaest = 'on' order by rcacod, rcaord";
        }

        $res1 = mysql_query($q, $conex);
        $num1 = mysql_num_rows($res1);
        $ant = 0;

        for ($i = 1;$i <= $num1;$i++)
        {
            $row1 = mysql_fetch_array($res1);
            if ($row1[0] != $ant)
            {
                if ($i != 1)
                {
                    echo ")</option>";
                }
                echo "<option>" . $row1[0] . " - RANGO " . $row1[0] . " - (" . $row1[1];
            }
            else
            {
                echo ", " . $row1[1];
                if ($i == $num1)
                {
                    echo ")</option>";
                }
            }

            $ant = $row1[0];
        }
        echo "</select> &nbsp; &nbsp;&nbsp;</td>";
        echo "</tr><tr height='34'>";
        // SELECCIONAR EMPRESA
        if( isset($wemp) && substr($wemp,0,3) != 'EMP' )
        {
            echo "<td align=center class='fila2' colspan=2 width='60%' >RESPONSABLE: <select name='wemp'>";

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

            $q = " SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom "
            . "   FROM " . $wbasedato . "_000024 "
            . "  WHERE empcod != empres "
            . "  GROUP BY emptem "
            . "  ORDER BY empnom ";

            $res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
            $num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());

            for ($i = 0;$i < $num;$i++)
            {
                $row = mysql_fetch_array($res);
                if( "EMP - " . $row[1] . " - " . $row[2] == $wemp ){
                	echo "<option selected>EMP - " . $row[1] . " - " . $row[2] . "</option>";
                }
                else{
                	echo "<option>EMP - " . $row[1] . " - " . $row[2] . "</option>";
                }
            }

            echo "</select></td>";
        }
        else
        {
            echo "<td align='center' class='fila2' colspan=2 width='60%'> RESPONSABLE: <select name='wemp'>";

            $q = " SELECT empcod, empnit, empnom "
             . "   FROM " . $wbasedato . "_000024 "
             . "  WHERE empcod = empres "
             . "  ORDER BY empnom ";

            $res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
            $num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());
            echo "<option>% - Todas las empresas</option>";
            for ($i = 0;$i <= $num;$i++)
            {
                $row = mysql_fetch_array($res);
                echo "<option>" . $row[0] . " - " . $row[1] . " - " . $row[2] . "</option>";
            }


            $q = " SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom "
            . "   FROM " . $wbasedato . "_000024 "
            . "  WHERE empcod != empres "
            . "  GROUP BY emptem "
            . "  ORDER BY empnom ";

            $res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
            $num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());

            for ($i = 0;$i < $num;$i++)
            {
                $row = mysql_fetch_array($res);
                if( "EMP - " . $row[1] . " - " . $row[2] == $wemp ){
                	echo "<option selected>EMP - " . $row[1] . " - " . $row[2] . "</option>";
                }
                else{
                	echo "<option>EMP - " . $row[1] . " - " . $row[2] . "</option>";
                }
            }

            echo "</select></td>";
        }
		//SELECCIONAR ESTÁDO DE LA FACTURA
		$q =  " SELECT estcod, estdes "
		."   FROM ".$wbasedato."_000144 "
		."	 WHERE estest='on'"
		."  ORDER BY 1 ";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		echo "<td align='center' colspan=2 class='fila2'>SELECCIONE ESTADO: ";
		echo "<select name='wesf'>";
		echo "<option value='Todos'>Todos</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			echo "<option value=".$row[0]."-".$row[1].">".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td>";
        echo "</tr>";

        echo "<input type='HIDDEN' NAME= 'wbasedato' value='" . $wbasedato . "'>";

        echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
        echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

        echo "<tr><td align=center class='fila2' COLSPAN='3' width='90%'>";
        echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> DESPLEGAR REPORTE DETALLADO&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()' > DESPLEGAR REPORTE RESUMIDO&nbsp;&nbsp;";
        echo "<input type='radio' name='vol' value='RE'  onclick='Seleccionar()' > DESPLEGAR REPORTE RESUMIDO CON PARTICULAR DETALLADO&nbsp;&nbsp;"; //submit
        echo "</b></td></tr></table></br>";
        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
    }
    else
        // MUESTRA DE DATOS DEL REPORTE
        {


            /****************************************************************************************************
             * Julio 22 de 2011
             * Creando tabla temporal para la tabla 00018, esto para mejorar la velocidad del reporte
             ****************************************************************************************************/
			 $filtroEstadoFactura='';
			 $estados = array();
			 if($wesf!='Todos')
				{
					$wesf=explode("-",$wesf);
					$wesfConsultar=$wesf[0];
					$wesf=$wesf[1];
					$estados[$wesfConsultar]=$wesf;
					$filtroEstadoFactura=" AND Fenesf = '{$wesfConsultar}'";
				}else
					{
						$q =  " SELECT estcod, estdes "
							."    FROM ".$wbasedato."_000144 "
							."	 WHERE estest='on'"
							."   ORDER BY 1 ";
							$res = mysql_query($q,$conex);
							$num = mysql_num_rows($res);
						while($row=mysql_fetch_array($res))
						{
							$estados[$row[0]]=$row[1];
						}

					}

            $temp = "Temp18".date("His");

	           $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $temp
	           		   (INDEX idx( fencod, fentip ), INDEX idx2( fencod, fennit ), INDEX idx3( fencod ), INDEX idx4( fencod, fencco ), INDEX idx5( fendpa, fencod, fencco ), INDEX idx6( fendpa ), INDEX idx7( fenfec ) )

	            	   SELECT Fensal, Fecha_data, Hora_data, Fenano, Fenmes, Fenfec, Fenffa, Fenfac, Fentip, Fennit, Fencod, Fenres, Fenval, Fenviv, Fencop, Fencmo, Fendes, Fenabo, Fenvnd, Fenvnc, Fenest, fencco, Fendpa, Fennpa, id, fenesf
	            		 FROM {$wbasedato}_000018
	            		WHERE fenfec between '$wfecini' AND '$wfecfin'
						  AND fenest = 'on'
						  AND fencco <> ''
						  AND fensal <> 0
						  {$filtroEstadoFactura}

					UNION

					   SELECT Fensal, a.Fecha_data, a.Hora_data, Fenano, Fenmes, Fenfec, Fenffa, Fenfac, Fentip, Fennit, Fencod, Fenres, Fenval, Fenviv, Fencop, Fencmo, Fendes, Fenabo, Fenvnd, Fenvnc, Fenest, fencco, Fendpa, Fennpa, a.id, fenesf
	            		 FROM {$wbasedato}_000018 a, {$wbasedato}_000021 b
	            		WHERE fenfec between '$wfecini' AND '$wfecfin'
						  AND fenest = 'on'
						  AND fencco<>''
						  AND fenffa = rdeffa
						  AND fenfac = rdefac
						  AND b.Fecha_data > '$wfeccor'
						  {$filtroEstadoFactura}
						GROUP BY rdeffa, rdefac
					";
           // echo "<br>".$sql;
            $res = mysql_query( $sql ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

            /****************************************************************************************************
             * Junio 29 de 2011
             * Creando tabla temporal para la tabla 00024, esto para mejorar la velocidad del reporte
             ****************************************************************************************************/
            $temp24 = "Temp24".date("His");

            $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $temp24
            		( INDEX idx( Empcod, emptem(10) ) )
            		SELECT
            			*
            		FROM
            			{$wbasedato}_000024
            		WHERE
            			empcod!=empres
            		";

			$res = mysql_query( $sql ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

            /****************************************************************************************************
             * Julio 22 de 2011
             * Creando tabla temporal para la tabla 00021, esto para mejorar la velocidad del reporte
             ****************************************************************************************************/
            $temp21 = "Temp21".date("His");

            $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $temp21
            		(INDEX idx5( rdeffa(10), rdefac ), INDEX idx( rdefue(2), rdenum, rdecco(4), rdefac, rdevta ), INDEX idx2( rdefac ), INDEX idx3( rdereg ), INDEX idx4( rdevta(15), rdefue(2), rdecco(4), rdeest) )
					  SELECT  rdesfa, Fecha_data, rdeest, rdereg, rdefac, rdeffa, rdefue, rdenum, rdecco, rdevta, Hora_data, id "
				 . "    FROM  ".$wbasedato."_000021 "
				 . "   WHERE rdeest= 'on' "
				 . "     AND rdesfa<>'' "
				 . "     AND rdereg=0 ";

			$res = mysql_query( $sql ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

            /****************************************************************************************************
             * Julio 22 de 2011
             * Creando tabla temporal para las tablas 000020 y 000021, esto para mejorar la velocidad del reporte
             ****************************************************************************************************/

            $temp2021 = "Temp2021".date("His");

            $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $temp2021
							  (INDEX idx5( rdeffa(10), rdefac ), INDEX idx( rdefue(2), rdenum, rdecco(4), rdefac, rdevta ), INDEX idx2( rdefac ), INDEX idx3( rdereg ), INDEX idx4( rdevta(15), rdefue(2), rdecco(4), rdeest), INDEX idx6( renfue(2), rennum, rencco ), INDEX idx7( renfec ) )
							  SELECT b.rdesfa, a.renfec, b.rdefue, b.rdenum, b.rdefac, b.rdeffa, b.rdecco, b.rdevta, b.rdereg, b.rdeest, a.renfue, a.rennum,  a.rencco, b.id, rencod "
                         . "    FROM " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b   "
                         . "   WHERE rdeest= 'on' "
                         . "     AND rdesfa<>'' "
                         . "     AND rdereg = 0  "
                         . "     AND renfec <= '" . $wfeccor . "'  "
                         . "     AND renfue=rdefue  "
                         . "     AND rennum=rdenum  "
                         . "     AND rencco=rdecco  "
                         . "   ORDER BY  b.id desc";

			//echo "<br><br>segundo query:<br>".$sql;
			$res = mysql_query( $sql ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );


			IF ($vol == 'RE')
            {
                $wemp = '% - Todas las empresas';
            }

            echo "<table  align=center width='60%'>";
            echo "<tr><td>&nbsp;</td></tr>";
            echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=340 HEIGHT=100></td></tr>";
            echo "<tr><td>&nbsp;</td></tr>";
            echo "<tr><td class='textoNormal'><B>Fecha: " . date('Y-m-d') . "</B></td></tr>";
            if ($vol == 'SI')
                echo "<tr><td class='textoNormal'><B>REPORTE DE CARTERA POR EDADES DETALLADO</B></td></tr>";
            if ($vol == 'NO')
                echo "<tr><td><B>REPORTE DE CARTERA POR EDADES  RESUMIDO</B></td></tr>";
            if ($vol == 'RE')
                echo "<tr><td><B>REPORTE DE CARTERA POR EDADES  RESUMIDO CON PARTICULARES DETALLADO</B></td></tr>";
            echo "<tr><td align=center><br /><A href='RepCarXEdad.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wtip=" . $wtip . "&amp;wfeccor=" . $wfeccor . "&amp;wemp=" . $wemp . "&amp;wran=" . $wran . "&amp;bandera='1'>Volver</A></td></tr><tr><td align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'><br /><br /></td></tr>";
            echo "<tr><td class='fila2' height='27'><b>Fecha inicial:</b> " . $wfecini . " &nbsp; &nbsp;&nbsp; &nbsp; <b>Fecha final:</b> " . $wfecfin . " &nbsp; &nbsp;&nbsp; &nbsp; <b>Fecha de corte:</b> " . $wfeccor . "</td></tr>";
            echo "<tr><td class='fila2' height='27'><b>Empresa:</b> " . $wemp . "</td></tr>";
            echo "<tr><td class='fila2' height='27'><b>Clasificado por:</b> " . $wtip . "</td></tr>";
			echo "<tr><td class='fila2' height='27'><b>Facturas con estado:</b> " . $wesf . "</td></tr>";
            echo "</table><br />";

            echo "<input type='HIDDEN' NAME= 'wfecini' value='" . $wfecini . "'>";
            echo "<input type='HIDDEN' NAME= 'wfecfin' value='" . $wfecfin . "'>";
            echo "<input type='HIDDEN' NAME= 'wemp' value='" . $wemp . "'>";
			echo "<input type='HIDDEN' NAME= 'wesf' value='" . $wesf . "'>";
            echo "<input type='HIDDEN' NAME= 'wtip' value='" . $wtip . "'>";
            echo "<input type='HIDDEN' NAME= 'wfeccor' value='" . $wfeccor . "'>";
            echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
            /**
            * **********************************Consulto lo pedido *******************
            */
            // se realiza un procesamiento del rango escogido para determinar los periodos
            $exp = explode('-', $wran);
            $exp2 = explode(',', trim($exp[2]));
            $numran = count($exp2);
            // Si se quiere detallado por particular
            if ($vol == 'RE')
            {
                for ($i = 0; $i < $numran; $i++)
                {
                    switch ($i)
                    {
                        case 0:
                            $wmax['num'][$i] = substr(trim($exp2[$i]), 1);
                            break;

                        case ($numran-1):
                            $wmax['num'][$i] = substr(trim($exp2[$i]), 0, -1);
                            break;

                        default:
                            $wmax['num'][$i] = trim($exp2[$i]);
                            break;
                    }
                    $wmax['saldo'][$i] = 0;
                }

                $q = " SELECT distinct fendpa "
                 . "    FROM  " . $temp
                 . "   	WHERE  fencod = '01' "
                 . "     AND fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                 . "     ORDER BY  fendpa ";

                $errh = mysql_query($q, $conex);
                $numh = mysql_num_rows($errh);

                $numh;
                echo "<table  align =center width='100%'>";
                if ($numh > 0)
                {

                    $wtotfac = 0;
                    $wtotsal = 0;
                    $senal = 0;

                    for ($j = 0; $j < $numran; $j++)
                    {
                        $wmax['salEmp'][$j] = 0;
                    }
                    // me meto al for de factura
                    $pinto = 0;
                    $cuenta = 0;
                    $wtotal = 0;
                    $wsaldo = 0;
                    $clase1 = "class='fila1'";
                    $clase2 = "class='fila1'";
                    // se busca en la tabla 20 y 21 registros, historia por historia en un for y entre las fechas escogidas
                    for ($i = 0;$i < $numh;$i++)
                    {
                        $rowh = mysql_fetch_array($errh);

                        $q = " SELECT fennpa "
                         . "     FROM  " . $wbasedato . "_000018 "
                         . "   	WHERE  fendpa= '" . $rowh[0] . "'";

                        $errp = mysql_query($q, $conex);
                        $rowp = mysql_fetch_array($errp);

                        $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo), fennit, fenres, fencod "
                         . "    FROM  " . $temp . " a"
                         . "   	WHERE a.fendpa = '" . $rowh[0] . "' "
                         . "     AND a.fencod = '01' "
                         . "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                         . "     ORDER BY  a.fenffa, a.fenfac ";

                        $err = mysql_query($q, $conex);
                        $num1 = mysql_num_rows($err);

                        if ($num1 > 0)
                        {
                            $wtotfac = 0;
                            $wtotsal = 0;
                            $senal = 0;

                            for ($j = 0; $j < $numran; $j++)
                            {
                                $wmax['salEmp'][$j] = 0;
                            }

                            $row = mysql_fetch_array($err);
							$nit = $row[7];
							if($row['fenres']!=$row['fencod'])
							{
								$qnit ="SELECT empnit
										  FROM {$wbasedato}_000024
										 WHERE empcod='{$row['fenres']}'";
								$rsnit = mysql_query($qnit, $conex);
								$rowNit = mysql_fetch_array($rsnit);
								$nit = $rowNit[0];
							}
                            // me meto al for de factura
                            $pinto = 0;
                            for ($j = 0;$j < $num1;$j++)
                            {
								$qres = "SELECT Empres, Empcod
										 FROM {$wbasedato}_000024
										WHERE Empnit='{$nit}'";
								$rsRes = mysql_query($qres, $conex);
								$numResponsables = mysql_num_rows($rsRes);
								$filtroResponsables='';
								for($m=0; $m < $numResponsables; $m++ )
								{
									$rowRes = mysql_fetch_array($rsRes);
									if($m==0)
										$filtroResponsables=" AND Rencod IN ( '{$row['fencod']}', '{$rowRes[0]}', '{$rowRes[1]}'";
										else
										 $filtroResponsables.=", '{$rowRes[0]}', '{$rowRes[1]}' ";

									if($m==$numResponsables-1)
										$filtroResponsables.=")";
								}


                                // busco las facturas con saldos a la fecha de corte
                                $q = " SELECT  rdesfa, renfec, rdefue, rdenum, rdefac, rdeffa "
                                 . "    FROM  " . $temp2021
                                 . "   	WHERE   rdefac= '" . $row[1] . "' "
                                 . "     AND rdeffa= '" . $row[0] . "' "
								 .$filtroResponsables;

                                $err2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                $y = mysql_num_rows($err2);
                                $row2 = mysql_fetch_array($err2);

								/*****************************************************************************
								 * Noviembre 30 de 2012
								 *****************************************************************************/
								$fecUltRadicado = consultarFechaRadicado( $conex, $wbasedato, $row[1], $row[0], $wfeccor );

								$fechaConsultar = "";
								$auxRow3 = $row[3];

								if( $fecUltRadicado != '' ){
									$fechaConsultar = "AND fecha_data >= '$fecUltRadicado' ";

									$row[3] = $fecUltRadicado;
								}
								/*****************************************************************************/
                                if ($y > 0)
                                {


                                    if ($row2[0] > 0)
                                    {
                                        $q = " SELECT  rdesfa, Fecha_data, Hora_data, id "
                                         . "    FROM   " . $temp21
                                         . "   	WHERE   rdefac= '" . $row2[4] . "' "
                                         . "     AND rdeffa= '" . $row2[5] . "' "
                                         . "     AND rdefue='" . $row2[2] . "'  "
                                         . "     AND rdenum='" . $row2[3] . "'  "
										  ."   ORDER BY id desc"
										  ."   LIMIT 1";

                                        $err2 = mysql_query($q, $conex);
                                        $row2 = mysql_fetch_array($err2);
                                    }/*else
									$row2[0]=$row[5]; //OJO, COMENTAR EN CASO DE SER NECESARIO. y descomentar lo anterior*/
                                }

                                for ($k = 0; $k < $numran; $k++)
                                {
                                    $wmax['salFac'][$k] = 0;
                                }
                                $saldototal = 0;
                                // parto la fecha de generacion de la factura
                                $dia = substr($row[3], 8, 2); // pasar el dia a una variable
                                $mes = substr($row[3], 5, 2); // pasar el mes a una variable
                                $anyo = substr($row[3], 0, 4); // pasar el año a una variable
                               /* if ($y > 0)
                                {
                                    $dia2 = substr($row2[1], 8, 2); // pasar el dia a una variable
                                    $mes2 = substr($row2[1], 5, 2); // pasar el mes a una variable
                                    $anyo2 = substr($row2[1], 0, 4); // pasar el año a una variable
                                }
                                else
                                {*/
                                    // parto la fecha de corte
                                    $dia2 = substr($wfeccor, 8, 2); // pasar el dia a una variable
                                    $mes2 = substr($wfeccor, 5, 2); // pasar el mes a una variable
                                    $anyo2 = substr($wfeccor, 0, 4); // pasar el año a una variable
                                //}

                                $segundos1 = mktime(0, 0, 0, $mes, $dia, $anyo); // calcular cuantos segundos han pasado desde 1970
                                $segundos2 = mktime(0, 0, 0, $mes2, $dia2, $anyo2); // calcular cuantos segundos han pasado desde 1970
                                $segundos3 = $segundos2 - $segundos1;
                                $segundos3 = $segundos3 / 86400;

								/******************************************************************************************
								 * Noviembre 30 de 2012
								 ******************************************************************************************/
								// Si es negativo significa que el movimiento es el ultimo saldo antes del radicado
								if( $segundos3 < 0 ){

									$segundos3 = ( strtotime( $wfeccor ) - $segundos1  )/86400;
									//$segundos3 = 0;
								}
								/******************************************************************************************/

								//Recupero nuevamente la fecha de la factura
								$row[3] = $auxRow3; 	//Noviembre 30 de 2012

                                if ($y > 0)
                                {
                                    if ($row2[0] > 0)
                                    {
                                        for ($k = 0; $k < $numran; $k++)
                                        {
                                            switch ($k)
                                            {
                                                case 0:
                                                    if ($segundos3 >= $wmax['num'][$k] and $segundos3 <= ($wmax['num'][$k+1]))
                                                    {
                                                        $wmax['salFac'][$k] = $row2[0];
                                                        $saldototal = $row2[0];
                                                    }
                                                    break;

                                                case ($numran-1):
                                                    if ($segundos3 > $wmax['num'][$k])
                                                    {
                                                        $wmax['salFac'][$k] = $row2[0];
                                                        $saldototal = $row2[0];
                                                    }
                                                    break;

                                                default:
                                                    if ($segundos3 > $wmax['num'][$k] and $segundos3 <= ($wmax['num'][$k+1]))
                                                    {
                                                        $wmax['salFac'][$k] = $row2[0];
                                                        $saldototal = $row2[0];
                                                    }
                                                    break;
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    for ($k = 0; $k < $numran; $k++)
                                    {
                                        switch ($k)
                                        {
                                            case 0:
                                                if ($segundos3 >= $wmax['num'][$k] and $segundos3 <= ($wmax['num'][$k+1]))
                                                {
                                                    $wmax['salFac'][$k] = $row[2];
                                                    $saldototal = $row[2];
                                                }
                                                break;

                                            case ($numran-1):
                                                if ($segundos3 > $wmax['num'][$k])
                                                {
                                                    $wmax['salFac'][$k] = $row[2];
                                                    $saldototal = $row[2];
                                                }
                                                break;

                                            default:
                                                if ($segundos3 > $wmax['num'][$k] and $segundos3 <= ($wmax['num'][$k +1]))
                                                {
                                                    $wmax['salFac'][$k] = $row[2];
                                                    $saldototal = $row[2];
                                                }
                                                break;
                                        }
                                    }
                                }

                                if ($saldototal != 0)
                                {
                                    $wtotfac = $wtotfac + $row[6];
                                    $wtotsal = $wtotsal + $saldototal;
                                    $wtotal = $wtotal + $row[6];
                                    $wsaldo = $wsaldo + $saldototal;

                                    for ($k = 0; $k < $numran; $k++)
                                    {
                                        $wmax['salEmp'][$k] = $wmax['salEmp'][$k] + $wmax['salFac'][$k];
                                        $wmax['saldo'][$k] = $wmax['saldo'][$k] + $wmax['salFac'][$k];
                                    }

                                    $cuenta = $cuenta + 1;
                                }

                                $row = mysql_fetch_array($err);
                            }

                            if ($wtotsal != 0)
                            {
                                if (!isset ($titular))
                                {
                                    echo "<table  align =center width='100%'>";
                                    echo "<tr><th align=CENTER class='encabezadoTabla' COLSPAN=4   width='60%' >&nbsp;</th>";
                                    //La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                                    //echo "<th align=CENTER class='encabezadoTabla' >TOTAL VLR FACTURA</th>";
                                    for ($k = 0; $k < $numran; $k++)
                                    {
                                        if ($k + 1 != $numran)
                                        {
                                            echo "<td align=CENTER class='encabezadoTabla' >" . $wmax['num'][$k] . "-" . $wmax['num'][$k + 1] . " DIAS</td>";
                                        }
                                        else
                                        {
                                            echo "<td align=CENTER class='encabezadoTabla'>+" . ($wmax['num'][$k] + 1) . " DIAS</td>";
                                        }
                                    }
                                    echo "<th align=CENTER class='encabezadoTabla'>TOTAL SALDO FACTURA</th></TR>";

                                    $titular = 1;
                                }
                                if (!isset($class1) or $class1 == 'fila2')
                                {
                                    $class1 = 'fila1';
                                    $class2 = 'fila1';
                                }
                                else
                                {
                                    $class1 = 'fila2';
                                    $class2 = 'fila2';
                                }

                                echo "<tr><th align='left' class='" . $class1 . "' colspan='4'>TOTAL ";
                                echo $rowh[0] . "-" . $rowp[0] . ":</th>";

                                //La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                                //echo "<th align=right class='" . $class2 . "' >" . number_format($wtotfac, 0, '.', ',') . "</th>";

                                for ($k = 0; $k < $numran; $k++)
                                {
                                    echo "<td align=right class='" . $class2 . "' width='10%'>" . number_format($wmax['salEmp'][$k], 0, '.', ',') . "</td>";
                                }

                                echo "<th align=right class='" . $class2 . "' >" . number_format($wtotsal, 0, '.', ',') . "</th></tr>";
                            }
                        }
                    }

                }
                    echo "<tr><th align=CENTER class='encabezadoTabla' COLSPAN=4   width='60%' >&nbsp;</th>";
                    //La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                    //echo "<th align=CENTER class='encabezadoTabla' >TOTAL VLR FACTURA</th>";
                    for ($k = 0; $k < $numran; $k++)
                      {
                    	if ($k + 1 != $numran)
                    	  {
                    		echo "<td align=CENTER class='encabezadoTabla' >" . $wmax['num'][$k] . "-" . $wmax['num'][$k + 1] . " DIAS</td>";
                          }
                          else
                          {
                            echo "<td align=CENTER class='encabezadoTabla'>+" . ($wmax['num'][$k] + 1) . " DIAS</td>";
                          }
                      }
                    echo "<th align=CENTER class='encabezadoTabla'>TOTAL SALDO FACTURA</th></TR>";
            }
            // si la empresa es diferente a todas las empresas, la meto en el vector solo
            // si es todas las empresas meto todas en un vector para luego preguntarlas en un for
            if ($wemp != '% - Todas las empresas')
            {
                $print = explode('-', $wemp);

                if( trim($print[0]) != 'EMP' ){
	                $empCod[0] = trim ($print[0]);
	                $empNom[0] = trim ($print[2]);
	                $empNit[0] = trim ($print[1]);

	                $q = " SELECT emptem "
	                 . "   FROM " . $wbasedato . "_000024 "
	                 . "  WHERE empcod='" . $empCod[0] . "' "
	                 . "  AND empnit='" . $empNit[0] . "' ";

	                $res = mysql_query($q, $conex);
	                $row = mysql_fetch_array($res);
	                $empTip[0] = $row[0];
	                $num = 1;

					$empCod2[0] = $empCod[0];

	                $empleado[0] = 'off';
                }
                else{
                	$empCod[0] = trim ($print[0]);
	                $empNom[0] = trim ($print[2]);
	                $empNit[0] = trim ($print[1])."-".trim ($print[2]);

	                $q = " SELECT emptem "
	                 . "   FROM " . $wbasedato . "_000024 "
	                 . "  WHERE empcod!=empres "
	                 . "    AND SUBSTRING_INDEX( emptem,'-', 1 ) = '{$empNit[0]}' ";
	                 "  GROUP 1 ";
//	                 . "  AND empnit='" . $empNit[0] . "' "; echo "......".$q;

	                $res = mysql_query($q, $conex);
	                $row = mysql_fetch_array($res);
	                $empTip[0] = $row[0];
	                $num = 1;

					$empCod2[0] = $empCod[0];

                	$empleado[0] = 'on';
                }
            }
            else
            {
                if ($wtip == 'CODIGO')
                {
                    $q = " SELECT empcod, empnom, empnit, emptem, 'off' as empleado"
                     . "   FROM " . $wbasedato . "_000024 "
                     . "  WHERE empcod=empres "
                     . "  ORDER BY 4, 3 desc ,1 ";

                    $res = mysql_query($q, $conex);
                    $num = mysql_num_rows($res);
                    for ($i = 0;$i < $num;$i++)
                    {
                        $row = mysql_fetch_array($res);
                        $empCod[$i] = $row[0];
                        $empNom[$i] = $row[1];
                        $empNit[$i] = $row[2];
                        $empTip[$i] = $row[3];
                        $empresa[$i] = $row[0] . " - " . $row[2] . " - " . $row[1];
                        $empleado[$i] = $row[4];
                    }

					/* comentado 2012-08-24
					// Se comenta porque las facturados de los empleados se incluyen dentro de las facturas de la empresa

                    $auxNum = $num;

					$q = " SELECT 'on' as empcod, 'on' as empnom, emptem as empnit, 'on' as emptem, 'on' as empleado"
                     . "   FROM " . $wbasedato . "_000024 "
                     . "  WHERE empcod!=empres "
                     . "  GROUP BY 3 "
                     . "  ORDER BY 4, 3 desc ,1 ";

                    $res = mysql_query($q, $conex);
                    $num = mysql_num_rows($res);
                    for ($i = 0;$i < $num;$i++)
                    {
                        $row = mysql_fetch_array($res);
                        $empCod[$auxNum+$i] = $row[0];
                        $empNom[$auxNum+$i] = $row[1];
                        $empNit[$auxNum+$i] = $row[2];
                        $empTip[$auxNum+$i] = $row[2];
                        $empresa[$auxNum+$i] = $row[0] . " - " . $row[2] . " - " . $row[1];
                        $empleado[$auxNum+$i] = $row[4];
                    }

                    $num = $num+$auxNum;
					*/
                }

                if ($wtip == 'NIT')
                {
                    $q = " SELECT  empnom, empnit, emptem, 'off' as empleado "
                     . "   FROM " . $wbasedato . "_000024 "
                     . "  WHERE empcod=empres "
                     . "  GROUP BY empnit ORDER BY 3, 2 desc ,1 ";

                    $q = " SELECT  empnom, empnit, emptem, 'off' as empleado, empcod "
                     . "   FROM " . $wbasedato . "_000024 "
                     . "  WHERE empcod=empres "
                     . "  GROUP BY empnit, empcod ORDER BY 3, 2 desc ,1 ";

                    $res = mysql_query($q, $conex);
                    $num = mysql_num_rows($res);
                    for ($i = 0;$i < $num;$i++)
                    {
                        $row = mysql_fetch_array($res);
                        $empNom[$i] = $row[0];
                        $empNit[$i] = $row[1];
                        $empTip[$i] = $row[2];
                        $empresa[$i] = $row[1] . " - " . $row[0];
                        $empleado[$i] = $row[3];
                        $empCod2[$i] = $row[4];	//Junio 20 de 2011
                        $empNitAnt[$i] = $row[1]; 	//Junio 20 de 2011
                    }

					/* comentado 2012-08-24
					// Se comenta porque las facturados de los empleados se incluyen dentro de las facturas de la empresa

                    $auxNum = $num;

                    $q = " SELECT  'on' as empnom, emptem as empnit, 'on' as emptem, 'on' as empleado "
                     . "   FROM " . $wbasedato . "_000024 "
                     . "  WHERE empcod!=empres "
                     . "  GROUP BY 2 "
                     . "  ORDER BY 4, 3 desc ,1 ";

                    $q = " SELECT  'on' as empnom, emptem as empnit, emptem, 'on' as empleado, empcod "
                     . "   FROM " . $wbasedato . "_000024 "
                     . "  WHERE empcod!=empres "
                     . "  GROUP BY 2 "
                     . "  ORDER BY 3, 2,5 desc ,1 ";

                    $res = mysql_query($q, $conex);
                    $num = mysql_num_rows($res);
                    for ($i = 0;$i < $num;$i++)
                    {
                        $row = mysql_fetch_array($res);
                        $empNom[$auxNum+$i] = $row[0];
                        $empNit[$auxNum+$i] = $row[1];
                        $empTip[$auxNum+$i] = $row[1];
                        $empresa[$auxNum+$i] = $row[1] . " - " . $row[0];
                        $empleado[$auxNum+$i] = $row[3];
                        $empCod2[$auxNum+$i] = $row[4];	//Junio 20 de 2011
                        $empNitAnt[$auxNum+$i] = $row[1]; 	//Junio 20 de 2011
                    }

                    $num = $num+$auxNum;
					*/
                }
            }
            // se realiza un procesamiento del rango escogido para determinar los periodos
            $exp = explode('-', $wran);
            $exp2 = explode(',', trim($exp[2]));
            $numran = count($exp2);

            for ($i = 0; $i < $numran; $i++)
            {
                switch ($i)
                {
                    case 0:
                        $wlim['num'][$i] = substr(trim($exp2[$i]), 1);
                        break;

                    case ($numran-1):
                        $wlim['num'][$i] = substr(trim($exp2[$i]), 0, -1);
                        break;

                    default:
                        $wlim['num'][$i] = trim($exp2[$i]);
                        break;
                }
                $wlim['saldo'][$i] = 0;
            }

            $cuenta = 0;
            $wtotal = 0;
            $wsaldo = 0;
            $clase1 = "class='fila1'";
            $clase2 = "class='fila1'";

            // se busca en la tabla 20 y 21 registros, empresa por empresa en un for y entre las fechas escogidas
//            $dejarPinto = false;
            for ($i = 0;$i < $num;$i++)
            {
            	if( $empleado[$i] != 'on' ){
	                if ($wtip == 'NIT')
	                {

						// 2012-08-23
						// Consulto los registros de empresas con el mismo NIT de la empresa responsable
						$qemp = "	SELECT b.empcod
									FROM ".$wbasedato."_000024 a, ".$wbasedato."_000024 b
									WHERE a.empcod = '" . $empCod2[$i] . "'
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


					   $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo), fennit, fenesf, fenres, fencod "
	                     . "    FROM  " . $temp . " a"
	                     . "   	WHERE a.fenres IN " . $regemp . " "	//Junio 20 de 2011
//	                     . "     AND a.fennit = '" . $empNit[$i] . "' "
//	                     . "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
	                     . "     ORDER BY  a.fenffa, a.fenfac ";
	                }
	                if ($wtip == 'CODIGO')
	                {
	                    $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo), fennit, fenesf, fenres, fencod "
	                     . "    FROM  " . $temp . " a "
	                     . "   	WHERE  a.fenres = '" . $empCod[$i] . "' "
//	                     . "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
	                     . "     ORDER BY  a.fenffa, a.fenfac ";
	                }
            	}
                else
				{

                     $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo), fennit, fenesf, fenres, fencod "
                     . "    FROM  " . $temp . " a, ". $temp24 . " b"
                     . "   	WHERE  a.fencod != a.fenres "
//                     . "     AND a.fentip = '" . $empNit[$i] . "' "
					 . "     AND a.fentip = '" . $empNit[$i] . "' "
                     . "     AND a.fentip = emptem "
                     . "     AND a.fencod = empcod "
//                     . "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                     . "     ORDER BY  a.fenffa, a.fenfac ";

                     $exp = explode( "-", $empNit[$i] );

                     $empNit[$i] = $exp[0];
                     $empCod[$i] = $exp[0];
                     $empNom[$i] = @$exp[1];
                }

                $err = mysql_query($q, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
                $num1 = mysql_num_rows($err);

                if ($num1 > 0)
                {
                    $wtotfac = 0;
                    $wtotsal = 0;
                    $senal = 0;

                    for ($j = 0; $j < $numran; $j++)
                    {
                        $wlim['salEmp'][$j] = 0;
                    }

                    $row = mysql_fetch_array($err);
					$nit = $row[7];
						if($row['fenres']!=$row['fencod'])
						{
							$qnit ="SELECT empnit
									  FROM {$wbasedato}_000024
									 WHERE empcod='{$row['fenres']}'";
							$rsnit = mysql_query($qnit, $conex);
							$rowNit = mysql_fetch_array($rsnit);
							$nit = $rowNit[0];
						}

                    // me meto al for de factura
                    $pinto = 0;
                    for ($j = 0;$j < $num1;$j++)
                    {
                        //2012-09-24 Query agregado para buscar los posibles rencod asociados a los movimientos de las facturas
						$qres = "SELECT Empres, Empcod
								   FROM {$wbasedato}_000024
								  WHERE Empnit='{$nit}'";
						$rsRes = mysql_query($qres, $conex);
						$numResponsables = mysql_num_rows($rsRes);
						$filtroResponsables='';
						for($m=0; $m < $numResponsables; $m++ )
						{
							$rowRes = mysql_fetch_array($rsRes);
							if($m==0)
								$filtroResponsables=" AND Rencod IN ( '{$row['fencod']}', '{$rowRes[0]}', '{$rowRes[1]}'";
								else
								 $filtroResponsables.=", '{$rowRes[0]}', '{$rowRes[1]}' ";

							if($m==$numResponsables-1)
								$filtroResponsables.=")";
						}
						// busco las facturas con saldos a la fecha de corte
                        $q = " SELECT  rdesfa, renfec, rdefue, rdenum, rdefac, rdeffa "
                         . "    FROM  " . $temp2021
                         . "   	WHERE   rdefac= '" . $row[1] . "' "
                         . "     AND rdeffa= '" . $row[0] . "' "
						 .$filtroResponsables;
                        $err2 = mysql_query($q, $conex);
                        $y = mysql_num_rows($err2);
                        $row2 = mysql_fetch_array($err2);

                        /*****************************************************************************
						 * Noviembre 30 de 2012
						 *
						 * Consulto la ultima fecha de radicado para hacer el calculo
						 *****************************************************************************/
						$fecUltRadicado = consultarFechaRadicado( $conex, $wbasedato, $row[1], $row[0], $wfeccor );

						$fechaConsultar = "";
						$auxRow3 = $row[3];

						if( $fecUltRadicado != '' ){
							$fechaConsultar = "AND fecha_data >= '$fecUltRadicado' ";

							$row[3] = $fecUltRadicado;
						}
						/*****************************************************************************/
						if ($y > 0)
                        {
                            if ($row2[0] > 0)
                            {
                                $q = " SELECT  rdesfa, Fecha_data, Hora_data, id"
                                 . "    FROM   " . $temp21
                                 . "   	WHERE   rdefac= '" . $row2[4] . "' "
                                 . "     AND rdeffa= '" . $row2[5] . "' "
                                 . "     AND rdefue='" . $row2[2] . "'  "
                                 . "     AND rdenum='" . $row2[3] . "'  "
								 . "   ORDER BY id desc"
								 . "   LIMIT 1";


                                $err2 = mysql_query($q, $conex) or die ("wepa".mysql_error());
                                $row2 = mysql_fetch_array($err2);
                            }
                        }

                        for ($k = 0; $k < $numran; $k++)
                        {
                            $wlim['salFac'][$k] = 0;
                        }
                        $saldototal = 0;

                        // parto la fecha de generacion de la factura o la fecha de la radicación
                        $dia = substr($row[3], 8, 2); // pasar el dia a una variable
                        $mes = substr($row[3], 5, 2); // pasar el mes a una variable
                        $anyo = substr($row[3], 0, 4); // pasar el año a una variable

                        //2008-03-28

                       /* if ($y > 0)
                        {
                            $dia2 = substr($row2[1], 8, 2); // pasar el dia a una variable
                            $mes2 = substr($row2[1], 5, 2); // pasar el mes a una variable
                            $anyo2 = substr($row2[1], 0, 4); // pasar el año a una variable
                        }
                        else
                        {*/
                        // parto la fecha de corte
                        $dia2 = substr($wfeccor, 8, 2); // pasar el dia a una variable
                        $mes2 = substr($wfeccor, 5, 2); // pasar el mes a una variable
                        $anyo2 = substr($wfeccor, 0, 4); // pasar el año a una variable
                        //}

                        $segundos1 = mktime(0, 0, 0, $mes, $dia, $anyo); // calcular cuantos segundos han pasado desde 1970
                        $segundos2 = mktime(0, 0, 0, $mes2, $dia2, $anyo2); // calcular cuantos segundos han pasado desde 1970
                        $segundos3 = $segundos2 - $segundos1;
                        $segundos3 = $segundos3 / 86400;

						/******************************************************************************************
						 * Noviembre 30 de 2012
						 ******************************************************************************************/
						// Si es negativo significa que el movimiento del ultimo saldo es antes del radicado
						if( $segundos3 < 0 ){
							$segundos3 = ( strtotime( $wfeccor ) - $segundos1 )/86400;
							// $segundos3 = 0;
						}
						/******************************************************************************************/

						//Recupero nuevamente la fecha de la factura
						$row[3] = $auxRow3; 	//Noviembre 30 de 2012

                        if ($y > 0)
                        {
                            if ($row2[0] > 0)
                            {
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    switch ($k)
                                    {
                                        case 0:
                                            if ($segundos3 >= $wlim['num'][$k] and $segundos3 <= ($wlim['num'][$k+1]))
                                            {
                                                $wlim['salFac'][$k] = $row2[0];
                                                $saldototal = $row2[0];
                                            }
                                            break;

                                        case ($numran-1):
                                            if ($segundos3 > $wlim['num'][$k])
                                            {
                                                $wlim['salFac'][$k] = $row2[0];
                                                $saldototal = $row2[0];
                                            }
                                            break;

                                        default:
                                            if ($segundos3 > $wlim['num'][$k] and $segundos3 <= ($wlim['num'][$k+1]))
                                            {
                                                $wlim['salFac'][$k] = $row2[0];
                                                $saldototal = $row2[0];
                                            }
                                            break;
                                    }
                                }
                            }
                        }
                        else
                        {
                            for ($k = 0; $k < $numran; $k++)
                            {
                                switch ($k)
                                {
                                    case 0:
                                        if ($segundos3 >= $wlim['num'][$k] and $segundos3 <= ($wlim['num'][$k+1]))
                                        {
                                            $wlim['salFac'][$k] = $row[2];
                                            $saldototal = $row[2];
                                        }
                                        break;

                                    case ($numran-1):
                                        if ($segundos3 > $wlim['num'][$k])
                                        {
                                            $wlim['salFac'][$k] = $row[2];
                                            $saldototal = $row[2];
                                        }
                                        break;

                                    default:
                                        if ($segundos3 > $wlim['num'][$k] and $segundos3 <= ($wlim['num'][$k+1]))
                                        {
                                            $wlim['salFac'][$k] = $row[2];
                                            $saldototal = $row[2];
                                        }
                                        break;
                                }
                            }
                        }

                        if ($saldototal != 0)
                        {
                            if ($vol == 'SI')
                            {
                                if ($pinto == 0)
                                {
                                    echo "<table align =center width='100%'>";
                                    if ($wtip == 'CODIGO')
                                        echo "<tr><td colspan='" . ($numran + 6) . "' class='titulo'>Empresa: " . $empCod[$i] . "-" . $empNit[$i] . "-" . $empNom[$i] . "</td></tr>";
                                    if ($wtip == 'NIT')
                                        echo "<tr><td colspan='" . ($numran + 6) . "' class='titulo'>Empresa: " . $empNit[$i] . "-" . $empNom[$i] . "</td></tr>";
                                    echo "<tr><td align=CENTER class='encabezadoTabla' width='10%'>FUENTE FACTURA</td>";
                                    echo "<td align=CENTER class='encabezadoTabla' width='20%'>NRO FACTURA</td>";
                                    echo "<td align=CENTER class='encabezadoTabla' width='10%'>FECHA FACTURA</td>";
									echo "<td align=CENTER class='encabezadoTabla' width='10%'>ESTADO FACTURA</td>";
                        			//La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                                    //echo "<td align=CENTER class='encabezadoTabla' width='10%'>VLR FACTURA</td>";

                                    for ($k = 0; $k < $numran; $k++)
                                    {
                                        if ($k + 1 != $numran)
                                        {
                                            echo "<td align=CENTER class='encabezadoTabla' width='10%'>" . $wlim['num'][$k] . "-" . $wlim['num'][$k + 1] . " DIAS</td>";
                                        }
                                        else
                                        {
                                            echo "<td align=CENTER class='encabezadoTabla' width='10%'>+" . ($wlim['num'][$k] + 1) . " DIAS</td>";
                                        }
                                    }
                                    echo "<td align=CENTER class='encabezadoTabla' width='10%'>TOTAL</td></tr>";
                                    $pinto = 1;
                                }

                                echo '<tr>';
                                echo "<td align=CENTER " . $clase1 . " width='10%'>" . $row[0] . "</td>";
                                echo "<td align=CENTER " . $clase1 . " width='20%'>" . $row[1] . "</td>";
                                echo "<td align=CENTER " . $clase1 . " width='10%'>" . $row[3] . "</td>";
								echo "<td align=CENTER " . $clase1 . " width='10%'>" . $estados[$row[8]] . "</td>";
		                        //La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                                //echo "<td align=right " . $clase2 . " width='10%'>" . number_format($row[6], 0, '.', ',') . "</td>";
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    echo "<td align=right " . $clase2 . " width='10%'>" . number_format($wlim['salFac'][$k], 0, '.', ',') . "</td>";
                                }
                                echo "<td align=right " . $clase2 . " width='10%'>" . number_format($saldototal, 0, '.', ',') . "</td>";
                                echo '</tr>';

                                if ($clase1 == "class='fila2'")
                                {
                                    $clase1 = "class='fila1'";
                                    $clase2 = "class='fila1'";
                                }
                                else
                                {
                                    $clase1 = "class='fila2'";
                                    $clase2 = "class='fila2'";
                                }
                            }

                            $wtotfac = $wtotfac + $row[6];
                            $wtotsal = $wtotsal + $saldototal;
                            $wtotal = $wtotal + $row[6];
                            $wsaldo = $wsaldo + $saldototal;

                            for ($k = 0; $k < $numran; $k++)
                            {
                                $wlim['salEmp'][$k] = $wlim['salEmp'][$k] + $wlim['salFac'][$k];
                                $wlim['saldo'][$k] = $wlim['saldo'][$k] + $wlim['salFac'][$k];
                            }

                            $cuenta = $cuenta + 1;
                        }

                        $row = mysql_fetch_array($err);
                    }

                    if ($wtotsal != 0)
                    {
                        if ($vol != 'SI')
                        {
                            If (!isset($pinto2) and ($vol != 'RE'))
                            {
                                echo "<table  align =center width='100%'>";
                                echo "<tr><th align=CENTER class='encabezadoTabla' COLSPAN=4   width='60%' >&nbsp;</th>";
                                echo "<td align=CENTER class='encabezadoTabla' width='10%'>TIPO</td>";
                                //La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                                //echo "<th align=CENTER class='encabezadoTabla' >TOTAL VLR FACTURA</th>";
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    if ($k + 1 != $numran)
                                    {
                                        echo "<td align=CENTER class='encabezadoTabla' >" . $wlim['num'][$k] . "-" . $wlim['num'][$k + 1] . " DIAS</td>";
                                    }
                                    else
                                    {
                                        echo "<td align=CENTER class='encabezadoTabla'>+" . ($wlim['num'][$k] + 1) . " DIAS</td>";
                                    }
                                }
                                echo "<th align=CENTER class='encabezadoTabla'>TOTAL SALDO FACTURA</th></TR>";
                                $pinto2 = 1;

                                $class1 = 'fila2';
                                $class2 = 'fila2';
                            }
                            else If (!isset($pinto2))
                            {
                                $pinto2 = 1;

                                $class1 = 'fila2';
                                $class2 = 'fila2';
                            }
                            if ($class1 == 'fila2')
                            {
                                $class1 = 'fila1';
                                $class2 = 'fila1';
                            }
                            else
                            {
                                $class1 = 'fila2';
                                $class2 = 'fila2';
                            }
                        }
                        else
                        {
                            $class1 = 'encabezadoTabla';
                            $class2 = 'encabezadoTabla';
                        }

                        if (isset($ant) and $vol == 'NO')
                        {
                            if ($empTip[$i] != $ant)
                            {
                                echo "<tr><th align=CENTER class='encabezadoTabla' colspan='5'>TOTAL TIPO: " . $ant . "</th>";
                                //La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                                //echo "<th align=right class='encabezadoTabla'>" . number_format($sum1, 0, '.', ',') . "</th>";
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    echo "<th align=right class='encabezadoTabla'>" . number_format($sum[$k], 0, '.', ',') . "</th>";
                                }
                                echo "<th align=right class='encabezadoTabla'>" . number_format($sum3, 0, '.', ',') . "</th>";
                                $sum1 = $wtotfac;
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    $sum[$k] = $wlim['salEmp'][$k];
                                }
                                $sum3 = $wtotsal;
                                $ant = $empTip[$i];
                            }
                            else
                            {
                                $sum1 = $sum1 + $wtotfac;
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    $sum[$k] = $sum[$k] + $wlim['salEmp'][$k];
                                }
                                $sum3 = $sum3 + $wtotsal;
                            }
                        }
                        else if ($vol == 'NO')
                        {
                            $sum1 = $wtotfac;
                            for ($k = 0; $k < $numran; $k++)
                            {
                                $sum[$k] = $wlim['salEmp'][$k];
                            }
                            $sum3 = $wtotsal;
                            $ant = $empTip[$i];
                            $ant = $empTip[$i];
                        }

                        echo "<tr><th align=left class='" . $class1 . "' colspan='4'>TOTAL ";

                        if ($wtip == 'CODIGO')
                            echo $empCod[$i] . "-" . $empNit[$i] . "-" . $empNom[$i] . ":</th>";
                        if ($wtip == 'NIT')
                            echo $empNit[$i] . "-" . $empNom[$i] . ":</th>";

                        if ($vol == 'NO')
                        {
                            echo "<th align=CENTER class='" . $class2 . "' >" . $empTip[$i] . "</th>";
                        }
                        //La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                        //echo "<th align=right class='" . $class2 . "' >" . number_format($wtotfac, 0, '.', ',') . "</th>";

                        for ($k = 0; $k < $numran; $k++)
                        {
                            echo "<td align=right class='" . $class2 . "' width='10%'>" . number_format($wlim['salEmp'][$k], 0, '.', ',') . "</td>";
                        }

                        echo "<th align=right class='" . $class2 . "' >" . number_format($wtotsal, 0, '.', ',') . "</th></tr>";
                    }
                }
            }

            /**
            * echo "<tr><th align=CENTER class='encabezadoTabla' colspan='4'>TOTAL TIPO: ".$ant."</th>";
            * echo "<th align=right class='encabezadoTabla'>".number_format($sum1,0,'.',',')."</th>";
            * for ($k=0; $k<$numran; $k++)
            * {
            * echo "<th align=right class='encabezadoTabla'>".number_format($sum[$k],0,'.',',')."</th>";
            * }
            * echo "<th align=right class='encabezadoTabla'>".number_format($sum3,0,'.',',')."</th>";
            */

            if ($cuenta == 0)
            {
                echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
                echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento en el rango de fechas seleccionado</td><tr>";
            }

            else if ($cuenta != 0)
            {
                if ($vol == 'NO')
                {
                    echo "<tr><th align=CENTER class='encabezadoTabla' colspan='5'>TOTAL</th>";
                }
                else
                {
                    echo "<tr><th align=CENTER class='encabezadoTabla' colspan='4'>TOTAL</th>";
                }
                //La siguiente línea se comenta ya que se quitó la columna "TOTAL VLR FACTURA" - 2010-12-28
                //echo "<th align=right class='encabezadoTabla'>" . number_format($wtotal, 0, '.', ',') . "</th>";
                for ($k = 0; $k < $numran; $k++)
                {
                    echo "<th align=right class='encabezadoTabla'>" . number_format($wlim['saldo'][$k], 0, '.', ',') . "</th>";
                }
                echo "<th align=right class='encabezadoTabla'>" . number_format($wsaldo, 0, '.', ',') . "</th>";
            }
            echo "</table>";
            echo "<br /><center><A href='RepCarXEdad.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wtip=" . $wtip . "&amp;wfeccor=" . $wfeccor . "&amp;wemp=" . $wemp . "&amp;wran=" . $wran . "&amp;bandera='1'>Volver</A></center>";
            echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
        }
    }
	liberarConexionBD($conex);
    ?>
</body>
</html>
