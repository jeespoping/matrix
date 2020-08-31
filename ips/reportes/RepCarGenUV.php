<head>
  <title>REPORTE DE CARTERA GENERAL</title>



  <!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
<!--    <link rel="stylesheet" href="../../zpcal/themes/fancyblue.css" />-->

<!-- Loading Calendar JavaScript files -->
<!--    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>-->
<!--    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>-->
<!--    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>-->

<!-- Loading language definition file -->
<!--    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>-->

   <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}

    	.titulo1{color:#FFFFFF;background:#006699;font-size:20pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.texto3{color:#006699;background:#A4E1E8;font-size:9pt;font-weight:bold;font-family:Tahoma;text-align:center;}
    	.texto4{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:right;}
    	.texto5{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Tahoma;text-align:right;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.acumulado2{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado3{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado4{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
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

<?php
include_once("conex.php");
/**
* NOMBRE:  REPORTE DE CARTERA GENERAL para Unidad Visual
*
* PROGRAMA: RepCarGen.php
* TIPO DE SCRIPT: PRINCIPAL
* //DESCRIPCION:Este reporte presenta la lista de notas debito o notas credito o recibos de cajacon sus detalles por empresa o para todas las empresas
*
* HISTORIAL DE ACTAULIZACIONES:
* 2006-06-20 carolina castano, creacion del script
* 2006-10-12 carolina castano, cambios de forma, presentación
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

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;

	$wactualiz = '2009-11-03';

    echo "<form action='RepCarGenUV.php' method=post name='forma'>";


    $wfecha = date("Y-m-d");

    echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

    if (!isset($wfecini) or !isset($wfecfin) or !isset($wfeccor) or !isset($wemp) or !isset($wtip) or !isset ($resultado))
    {
    	encabezado( "REPORTE DE CARTERA GENERAL", $wactualiz, "logo_".$wbasedato );

    	echo "<br>";
    	echo "<center><table border=2>";
//        echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=500 HEIGHT=100></td></tr>";
//        echo "<tr><td class='titulo1'>REPORTE DE CARTERA GENERAL</td></tr>";
        // INGRESO DE VARIABLES PARA EL REPORTE//
        if (!isset ($bandera))
        {
            $wfecini = $wfecha;
            $wfecfin = $wfecha;
            $wfeccor = $wfecha;
        }

        echo "<tr class='fila1'>";
        $cal = "calendario('wfecini','1');";
        echo "<td align=center><b>FECHA INICIAL DE FACTURACION: </font></b>";
        campoFechaDefecto("wfecini", $wfecini);

        echo "<td  align=center><b>FECHA FINAL DE FACTURACION: </font></b>";
        campoFechaDefecto("wfecfin", $wfecfin);
        echo "</tr>";

        echo "<tr class='fila1'>";

        echo "<td align=center><b>FECHA DE CORTE : </b>";
        campoFechaDefecto( "wfeccor", $wfeccor);

        // SELECCIONAR tipo de reporte
        echo "<td align=center><b>PARAMETROS DEL REPORTE: </b>";
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

        //CENTRO DE COSTO
	    $q =  " SELECT ccocod, ccodes "
			 ."   FROM ".$wbasedato."_000003 "
			 ."  ORDER BY 1 ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);


		echo "<tr class='fila1'><td align=center colspan=1><b>SELECCIONE LA SUCURSAL: </b>";
		echo "<select name='wcco'>";
		echo "<option>% - Todos</option>";
		for ($i=1;$i<=$num;$i++)
		   {
		    $row = mysql_fetch_array($res);
		    echo "<option>".$row[0]."-".$row[1]."</option>";
	       }
		echo "</select></td>";


        // SELECCIONAR EMPRESA
        if (isset($wemp))
        {
            echo "<td align=center colspan=2 ><b>Responsable:</b> <br><select name='wemp'>";

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
            echo "</select></td>";
        }
        else
        {
            echo "<td colspan=2 > Responsable: <br><select name='wemp'>";

            $q = " SELECT empcod, empnit, empnom "
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
            echo "</select></td>";
        }
        echo "</tr>";

        echo "<input type='HIDDEN' NAME= 'wbasedato' value='" . $wbasedato . "'>";

        echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
        echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

        echo "<tr class='fila1'><td align=center COLSPAN='2'>";
        echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked><b> DESPLEGAR REPORTE DETALLADO</b>&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()' ><b> DESPLEGAR REPORTE RESUMIDO&nbsp;&nbsp;</b>"; //submit
        echo "</b></td></tr></table></br>";
        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
    }
    // MUESTRA DE DATOS DEL REPORTE
    else
    {
	    $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
		$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
		$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
		$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

		if ($vol == 'SI')
           $wTit="REPORTE DE CARTERA GENERAL DETALLADO";
          else
             $wTit="<B>REPORTE DE CARTERA GENERAL RESUMIDO";


        encabezado( $wTit, $wactualiz, "logo_".$wbasedato );

        echo "<table  align=center width='60%'>";
        echo "<tr><td>&nbsp;</td></tr>";

//        echo "<tr>";
//        echo"<td bgcolor=".$wclfa."><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=400 HEIGHT=100'></td>";
//		echo"<td bgcolor=".$wcf2."><font size='4' color='FFFFFF'>".$wTit."</font></td>";
//		echo "</tr>";
		echo "<tr class='fila1'>";
	    echo "<td align='center'><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
	    echo "<td align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
	    echo "</tr>";
	    echo "<tr class='fila1'>";
	    echo "<td align=center colspan=2><b><font text color=".$wclfg.">Sucursal: </font></b>".$wcco."</td>";
	    echo "</tr>";
	    echo "<tr  class='fila1'>";
	    echo "<td align=center><b><font text color=".$wclfg.">Empresa: </font></b>".$wemp."</td>";
	    echo "<td align=center><b><font text color=".$wclfg.">Clasificado por:</font></b> " . $wtip . "</td>";
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
        // si la empresa es diferente a todas las empresas, la meto en el vector solo
        // si es todas las empresas meto todas en un vector para luego preguntarlas en un for
        if ($wemp != '% - Todas las empresas')
        {
            $print = explode('-', $wemp);
            $empCod[0] = trim ($print[0]);
            $empNom[0] = trim ($print[2]);
            $empNit[0] = trim ($print[1]);
            $empresa[0] = $empCod[0] . " - " . $empNit[0] . " - " . $empNom[0];
            $num = 1;
        }
        else
        {
            if ($wtip == 'CODIGO')
            {
                $q = " SELECT empcod, empnom, empnit "
                   . "   FROM " . $wbasedato . "_000024 "
                   . "  WHERE empcod=empres "
                   . "    AND empest='on' "
                   . "  ORDER BY 3 desc ,1 ";

                $res = mysql_query($q, $conex);
                $num = mysql_num_rows($res);
                for ($i = 0;$i < $num;$i++)
                {
                    $row = mysql_fetch_array($res);
                    $empCod[$i] = $row[0];
                    $empNom[$i] = $row[1];
                    $empNit[$i] = $row[2];
                    $empresa[$i] = $row[0] . " - " . $row[2] . " - " . $row[1];
                }
            }

            if ($wtip == 'NIT')
            {
                $q = " SELECT  empnom, empnit "
                 . "   FROM " . $wbasedato . "_000024 "
//                 . "  WHERE empcod=empres "
                 . "  WHERE empest='on' "
                 . "  GROUP BY empnit ORDER BY 2 desc ,1 ";

                $res = mysql_query($q, $conex);
                $num = mysql_num_rows($res);
                for ($i = 0;$i < $num;$i++)
                {
                    $row = mysql_fetch_array($res);
                    $empNom[$i] = $row[0];
                    $empNit[$i] = $row[1];
                    $empresa[$i] = $row[1] . " - " . $row[0];
                }
            }
        }
        // se busca en la tabla 20 y 21 registros, empresa por empresa en un for y entre las fechas escogidas.  En la tabla 21
        // se encuentra el saldo que dejo la nota en la factura.
        $cuenta = 0;
        $wtotal = 0;
        $wsaldo = 0;
        $clase1 = "class='texto1' class='fila1' align='center'";
        $clase2 = "class='texto4' class='fila1' align='right'";
        $clase1 = "class='fila1' align='center'";
        $clase2 = "class='fila1' align='right'";

        echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	    $wccoe = explode("-",$wcco);

        for ($i = 0;$i < $num;$i++)
        {
            if ($wtip == 'NIT')
            {
                $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo), a.fennpa, a.fendpa, a.fencod "
                 . "    FROM  " . $wbasedato . "_000018 a"
                 . "   	WHERE  a.fenfec between '" . $wfecini . "'"
                 . "     AND '" . $wfecfin . "'"
                 . "     AND a.fennit = '" . $empNit[$i] . "' "
                 . "     AND a.fenest = 'on' "
                 . "     AND a.fencco LIKE '".trim($wccoe[0])."'"
                 . "     AND a.fencco<>'' "
                 . "     AND a.fenval<>0 "
                 . "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                 . "     ORDER BY  a.fenffa, a.fenfac ";
            }
            if ($wtip == 'CODIGO')
            {
                $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo), a.fennpa, a.fendpa, a.fencod "
                 . "    FROM  " . $wbasedato . "_000018 a "
                 . "   	WHERE  a.fenfec between '" . $wfecini . "'"
                 . "     AND '" . $wfecfin . "'"
                 . "     AND a.fenres = '" . $empCod[$i] . "' "
                 . "     AND a.fenest = 'on' "
                 . "     AND a.fencco LIKE '".trim($wccoe[0])."'"
                 . "     AND a.fencco<>'' "
                 . "     AND a.fenval<>0 "
                 . "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                 . "     ORDER BY  a.fenffa, a.fenfac ";
            }

            $err = mysql_query($q, $conex);
            $num1 = mysql_num_rows($err);

            if ($num1 > 0)
            {
                $wtotfac = 0;
                $wtotsal = 0;
                $senal = 0;
                $row = mysql_fetch_array($err);

                $pinto = 0;

                for ($j = 0;$j < $num1;$j++)
                {
                    $q = " SELECT  b.rdesfa, b.rdefue, b.rdenum, b.rdefac, b.rdeffa "
                     . "    FROM  " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b   "
                     . "   	WHERE   rdefac= '" . $row[1] . "' "
                     . "     AND rdeffa= '" . $row[0] . "' "
                     . "     AND rdeest= 'on' "
                     . "     AND rdesfa<>'' "
                     . "     AND rdereg=0 "
                     . "     AND renfec <= '" . $wfeccor . "'  "
                    // ."     AND rencco = '".$row[4]."'  "
                    . "     AND renfue=rdefue  "
                     . "     AND rennum=rdenum  "
                     . "     AND rencco=rdecco  "
                     . "     ORDER BY  b.id desc";

                    $err2 = mysql_query($q, $conex);
                    $y = mysql_num_rows($err2);
                    $row2 = mysql_fetch_array($err2);

                    if ($y > 0)
                    {
                        if ($row2[0] > 0)
                        {
                            $q = " SELECT  MIN(cast(rdesfa as UNSIGNED)) "
                             . "    FROM   " . $wbasedato . "_000021 "
                             . "   	WHERE   rdefac= '" . $row2[3] . "' "
                             . "     AND rdeffa= '" . $row2[4] . "' "
                             . "     AND rdeest= 'on' "
                             . "     AND rdesfa<>'' "
                             . "     AND rdereg=0 "
                             . "     AND rdefue='" . $row2[1] . "'  "
                             . "     AND rdenum='" . $row2[2] . "'  "
                             . "     group by rdenum, rdefue  ";

                            $err2 = mysql_query($q, $conex);
                            $row2 = mysql_fetch_array($err2);
                        }
                    }

                    if ($vol == 'SI')
                    {
                        if ( $pinto == 0 && ( ($y > 0 && $row2[0] > 0 ) || ($y <= 0) ) )
                        {
                            echo "<table  align =center width='750'>";
                            if ($wtip == 'CODIGO')
                                echo "<tr><td colspan=10 class='colorAzul5'><b>Empresa: " . $empCod[$i] . "-" . $empNit[$i] . "-" . $empNom[$i] . " </b></td></tr>";
                            if ($wtip == 'NIT')
                                echo "<tr><td colspan=10 class='colorAzul5'><b>Empresa: " . $empNit[$i] . "-" . $empNom[$i] . " </b></td></tr>";

                            echo "<th align=CENTER class='encabezadotabla' width='20%'>FUENTE FACTURA</th>";
                            echo "<th align=CENTER class=encabezadotabla width='20%'>NRO FACTURA</th>";
                            echo "<th align=CENTER class=encabezadotabla width='20%'>FECHA FACTURA</th>";
                            echo "<th align=CENTER class=encabezadotabla width='20%'>VLR FACTURA</th>";
                            echo "<th align=CENTER class=encabezadotabla width='20%' >SALDO FACTURA</th>";
                            echo "<th align=CENTER class=encabezadotabla width='20%' >USUARIO</th>";
                            echo "<th align=CENTER class=encabezadotabla width='20%' >TELEFONO</th>";
                            echo "<th align=CENTER class=encabezadotabla width='20%' >CAJA FISICA</th>";
                            echo "<th align=CENTER class=encabezadotabla width='20%' >IPS o EMPRESA</th>";

                            $pinto = 1;
                        }

                        if ($y > 0)
                        {
                            if ($row2[0] > 0)
                            {
                                echo '<tr>';
                                echo "<th align=CENTER ".$clase1." width='20%'>".$row[0]."</th>";
                                echo "<th align=CENTER ".$clase1." width='20%'>".$row[1]."</th>";
                                echo "<th align=CENTER ".$clase1." width='20%'>".$row[3]."</th>";
                                echo "<th  			   ".$clase2." width='20%' >".number_format($row[6], 0, '.', ',') . "</th>";
                                echo "<th  			   ".$clase2." width='20%' >".number_format($row2[0], 0, '.', ',') . "</th>";
                                echo "<th align=CENTER ".$clase1." width='20%'>".$row[7]."</th>";
                                //===========================================================================
                                //Traigo el telefono de la tabla 000041 (Clientes)
                                //===========================================================================
                                $q = " SELECT clite1 "
                                    ."   FROM ".$wbasedato."_000041 "
                                    ."  WHERE clidoc = '".$row[8]."'";
                                $rescli = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$numcli=mysql_affected_rows();

								if ($numcli > 0 ) {
								   $rowcli = mysql_fetch_array($rescli);
								   echo "<th align=CENTER ".$clase1." width='20%'>".$rowcli[0]."</th>";
							       }
								  else
								     echo "<th align=CENTER ".$clase1." width='20%'>&nbsp</th>";
								//===========================================================================

								//===========================================================================
                                //Traigo el numero de la cajita de la tabla 000133 (Ordenes de laboratorio)
                                //===========================================================================
                                $q = " SELECT ordcaj "
                                    ."   FROM ".$wbasedato."_000133 "
                                    ."  WHERE ordffa = '".$row[0]."'"
                                    ."    AND ordfac = '".$row[1]."'";
                                $rescaj = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$numcaj=mysql_affected_rows();
								if ($numcaj > 0 ) {
								   $rowcaj = mysql_fetch_array($rescaj);
								   echo "<th align=CENTER ".$clase1." width='20%'>".$rowcaj[0]."</th>";
							       }
								  else
								     echo "<th align=CENTER ".$clase1." width='20%'>&nbsp</th>";
								//===========================================================================

								//===========================================================================
                                //Traigo el NOMBRE DE LA EMPRESA (IPS) de la tabla 000024 (Empresas)
                                //===========================================================================
                                $wempcod=explode("-",$row[9]);
                                $q = " SELECT empnom "
                                    ."   FROM ".$wbasedato."_000024 "
                                    ."  WHERE empcod = '".$wempcod[0]."'";
                                $resemp = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$numemp=mysql_affected_rows();

								if ($numemp > 0 ) {
								   $rowemp = mysql_fetch_array($resemp);
								   echo "<td align=CENTER ".$clase2." width='20%'>".$rowemp[0]." </td>";
							       }
								  else
								     echo "<td align=CENTER ".$clase1." width='20%'>&nbsp;</td>";
								//===========================================================================
                                echo '</tr>';

                                if ($clase1 == "class='fila2' align='center'")
                                {
                                    $clase1 = "class='texto1'";
                                    $clase2 = "class='texto4'";
                                    $clase1 = "class='fila1' align='center'";
        							$clase2 = "class='fila1' align='right'";
                                }
                                else
                                {
                                    $clase1 = "class='texto2'";
                                    $clase2 = "class='texto5'";
                                    $clase1 = "class='fila2' align='center'";
                                    $clase2 = "class='fila2' align='right'";
                                }
                            }
                        }
                        else
                        {
                            echo '<tr>';
                            echo "<th align=CENTER ".$clase1." width='20%'>".$row[0]."</th>";
                            echo "<th align=CENTER ".$clase1." width='20%'>".$row[1]."</th>";
                            echo "<th align=CENTER ".$clase1." width='20%'>".$row[3]."</th>";
                            echo "<th 			   ".$clase2." width='20%' >".number_format($row[6], 0, '.', ',') . "</th>";
                            echo "<th 			   ".$clase2." width='20%' >".number_format($row[2], 0, '.', ',') . "</th>";
                            echo "<th align=CENTER ".$clase1." width='20%'>".$row[7]."</th>";

                            //===========================================================================
                            //Traigo el telefono de la tabla 000041
                            //===========================================================================
                            $q = " SELECT clite1 "
                                ."   FROM ".$wbasedato."_000041 "
                                ."  WHERE clidoc = '".$row[8]."'";
                            $rescli = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							$numcli=mysql_affected_rows();

							if ($numcli > 0 ) {
							   $rowcli = mysql_fetch_array($rescli);
							   echo "<th align=CENTER ".$clase1." width='20%'>".$rowcli[0]."</th>";
						       }
							  else
							     echo "<th align=CENTER ".$clase1." width='20%'>&nbsp</th>";
							//===========================================================================

							//===========================================================================
                            //Traigo el numero de la cajita de la tabla 000133 (Ordenes de laboratorio)
                            //===========================================================================
                            $q = " SELECT ordcaj "
                                ."   FROM ".$wbasedato."_000133 "
                                ."  WHERE ordffa = '".$row[0]."'"
                                ."    AND ordfac = '".$row[1]."'";
                            $rescaj = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							$numcaj=mysql_affected_rows();
							if ($numcaj > 0 ) {
							   $rowcaj = mysql_fetch_array($rescaj);
							   echo "<th align=CENTER ".$clase1." width='20%'>".$rowcaj[0]."</th>";
						       }
							  else
							     echo "<th align=CENTER ".$clase1." width='20%'>&nbsp</th>";
							//===========================================================================

							//===========================================================================
                            //Traigo el NOMBRE DE LA EMPRESA (IPS) de la tabla 000024 (Empresas)
                            //===========================================================================
                            $wempcod=explode("-",$row[9]);
                            $q = " SELECT empnom "
                                ."   FROM ".$wbasedato."_000024 "
                                ."  WHERE empcod = '".$wempcod[0]."'";
                            $resemp = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							$numemp=mysql_affected_rows();

							if ($numemp > 0 ) {
							   $rowemp = mysql_fetch_array($resemp);
							   echo "<td align=CENTER ".$clase2." width='20%'>".$rowemp[0]." </td>";
						       }
							  else
							     echo "<td align=CENTER ".$clase1." width='20%'>&nbsp;</td>";
							//===========================================================================
							echo '</tr>';

                            if ($clase1 == "class='fila2' align='center'")
                            {
                                $clase1 = "class='texto1'";
                                $clase2 = "class='texto4'";
                                $clase1 = "class='fila1' align='center'";
        						$clase2 = "class='fila1' align='right'";
                            }
                            else
                            {
                                $clase1 = "class='texto2'";
                                $clase2 = "class='texto5'";
                                $clase1 = "class='fila2' align='center'";
                                $clase2 = "class='fila2' align='right'";
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
                        }
                    }
                    else
                    {
                        $wtotsal = $wtotsal + $row[2];
                        $wsaldo = $wsaldo + $row[2];
                        $wtotfac = $wtotfac + $row[6];
                        $wtotal = $wtotal + $row[6];
                        $cuenta = $cuenta + 1;
                    }

                    $row = mysql_fetch_array($err);
                }

                if ($wtotsal != 0 )
                {
                    if ($vol != 'SI')
                    {
                    	echo "<table  align =center width='750'>";
                        if ($wtip == 'CODIGO')
                            echo "<tr><td colspan=9 class='colorAzul5'><b>Empresa: " . $empCod[$i] . "-" . $empNit[$i] . "-" . $empNom[$i] . " </b></td></tr>";
                        if ($wtip == 'NIT')
                            echo "<tr><td colspan=9 class='colorAzul5'><b>Empresa: " . $empNit[$i] . "-" . $empNom[$i] . " </b></td></tr>";
                        echo "<td  class=encabezadotabla COLSPAN=3 width='40%'>&nbsp;</td>";
                        echo "<td  class=encabezadotabla width='30%'>TOTAL VALOR FACTURA</td>";
                        echo "<td class=encabezadotabla width='30%'> TOTAL SALDO FACTURA</td></TR>";
                    }
                    echo "<td class='colorazul5' align='center' colspan='3' width='40%'><b>TOTAL EMPRESA</b></td>";
                    echo "<td  class='colorazul5' align='right' width='30%'><b>" . number_format($wtotfac, 0, '.', ',') . "</b></td>";
                    echo "<td  class='colorazul5' align='right' width='30%'><b>" . number_format($wtotsal, 0, '.', ',') . "</b></td>";

                    if ($vol == 'SI')
                    {
                    	echo "<td class='colorazul5' align='center' colspan='4' width='40%'><b>&nbsp</b></td></tr>";
                    }
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
            echo "<tr><th align=CENTER class='colorAzul4' align='center' colspan='3'>TOTAL </th>";
            echo "<th class='colorAzul4' align='right'>" . number_format($wtotal, 0, '.', ',') . "</th>";
            echo "<th class='colorAzul4' align='right'>" . number_format($wsaldo, 0, '.', ',') . "</th>";

            if ($vol == 'SI')
            {
            	echo "<th align=CENTER class='colorAzul4' align='center' colspan='4'&nbsp</th>";
            }
        }
        echo "</table>";
        echo "</br><center><A href='RepCarGenUV.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wtip=" . $wtip . "&amp;wfeccor=" . $wfeccor . "&amp;wemp=" . $wemp . "&amp;bandera='1'>VOLVER</A></center>";
        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
    }
}
liberarConexionBD($conex);
?>
</body>
</html>
