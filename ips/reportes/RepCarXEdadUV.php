<head>
  <title>REPORTE DE CARTERA POR EDADES</title>

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
    	.acumulado3{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.acumulado4{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.acumulado5{color:#003366;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.acumulado6{color:#003366;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.acumulado7{color:#003366;background:#f5f5dc;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.acumulado8{color:#003366;background:#f5f5dc;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
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

</SCRIPT>

</head>

<?php
include_once("conex.php");

/**
* NOMBRE:  REPORTE DE CARTERA POR EDADES
* 
* PROGRAMA: RepCarXEdadUV.php
* TIPO DE SCRIPT: PRINCIPAL
* //DESCRIPCION:Este reporte presenta la lista de notas debito o notas credito o recibos de caja con sus detalles por empresa o para todas las empresas
*    con sus saldos de cartera por edades o rangos de tiempo escogidos por el usuario
* 
* HISTORIAL DE ACTAULIZACIONES:
* 2006-06-20 carolina castano, creacion del script
* 2006-10-12 carolina castano, cambios de forma, presentación
* 2007-02-20 carolina castano, se adecua para que los rangos de las edades sean escogidos por el usuario de los configurados en base de datos
* 2007-08-15 carolina castano, se muestra el tipo de empresa en el reporte resumido
* 2008-03-28 se muestra comenta el query que retomaba la fecha de corte
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

    echo "<form action='RepCarXEdadUV.php' method=post name='forma'>";

    $wfecha = date("Y-m-d");

    echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

    if (!isset($wfecini) or !isset($wfecfin) or !isset($wfeccor) or !isset($wemp) or !isset($wtip) or !isset ($resultado))
    {
        echo "<center><table border=2 width='90%'>";
        echo "<tr><td align=center rowspan=2 width=33%><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH='80%' HEIGHT=100></td></tr>";
        echo "<tr><td class='titulo1' colspan=2 width='60%'>REPORTE DE CARTERA POR EDADES</td></tr>"; 
        // INGRESO DE VARIABLES PARA EL REPORTE//
        if (!isset ($bandera))
        {
            $wfecini = $wfecha;
            $wfecfin = $wfecha;
            $wfeccor = $wfecha;
        } 

        echo "<tr>";
        $cal = "calendario('wfecini','1')";
        echo "<td align=center class='texto3' width='33%'><b>FECHA INICIAL DE FACTURACION: </font></b>";
        campoFechaDefecto( "wfecini" ,$wfecini);

        echo "<td  align=center class='texto3' width='33%'><b>FECHA FINAL DE FACTURACION: </font></b>";
        campoFechaDefecto( "wfecfin" ,$wfecfin);

        echo "<td class='texto3' align=center>FECHA DE CORTE : ";
        campoFechaDefecto( "wfeccor" ,$wfeccor);

        // SELECCIONAR tipo de reporte
        echo "</tr>";

        echo "<tr>";
        echo "<td align=center class='texto3' >PARAMETROS DEL REPORTE: ";
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
        echo "<td align=center class='texto3' colspan=2 >SELECCIONE LOS RANGOS DE EDADES: <select name='wran'>";

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
        echo "</select></td>";
        echo "</tr>"; 
        
        
        //CENTRO DE COSTO
	    $q =  " SELECT ccocod, ccodes "
			 ."   FROM ".$wbasedato."_000003 "
			 ."  ORDER BY 1 ";
				 	 
		$res = mysql_query($q,$conex) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );;
		$num = mysql_num_rows($res);
		 
		  
		echo "<tr><td align=center class='texto3' colspan=1>SELECCIONE LA SUCURSAL: ";
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
            echo "<td align=center class='texto3' colspan=2 width='90%' >Responsable: <br><select name='wemp'>";

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
            echo "<td class='texto3'  colspan=2 width='90%' > Responsable: <br><select name='wemp'>";

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

        echo "<tr><td align=center class='texto3' COLSPAN='3' width='90%'>";
        echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> DESPLEGAR REPORTE DETALLADO&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()' > DESPLEGAR REPORTE RESUMIDO&nbsp;&nbsp;";
        echo "<input type='radio' name='vol' value='RE'  onclick='Seleccionar()' > DESPLEGAR REPORTE RESUMIDO CON PARTICULAR DETALLADO&nbsp;&nbsp;"; //submit
        echo "</b></td></tr></table></br>";
        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
    } 
    else 
        // MUESTRA DE DATOS DEL REPORTE
        {
	        $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
			$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
			$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
			$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro 
	        
			echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	        $wccoe = explode("-",$wcco);
			
			
            IF ($vol == 'RE')
            {
                $wemp = '% - Todas las empresas';
            } 
            
            if ($vol == 'SI')
                $wtit="REPORTE DE CARTERA POR EDADES DETALLADO";
            if ($vol == 'NO')
                $wtit="REPORTE DE CARTERA POR EDADES  RESUMIDO";
            if ($vol == 'RE')
                $wtit="REPORTE DE CARTERA POR EDADES  RESUMIDO CON PARTICULARES DETALLADO";

            echo "<table  align=center width='60%'>";
            echo "<tr><td>&nbsp;</td></tr>";
            echo"<td bgcolor=".$wclfa."><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=400 HEIGHT=100'></td>";
			echo"<td bgcolor=".$wcf2."><font size='4' color='FFFFFF'>".$wtit."</font></td>";
            echo "<tr><td>&nbsp;</td></tr>";
            //echo "<tr><td><B>Fecha: " . date('Y-m-d') . "</B></td></tr>";
            
            //echo "<tr><td align=right ><A href='RepCarXEdadUV.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wtip=" . $wtip . "&amp;wfeccor=" . $wfeccor . "&amp;wemp=" . $wemp . "&amp;wran=" . $wran . "&amp;bandera='1'>VOLVER</A></td></tr><tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
            echo "<tr>";  
		    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
		    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
		    echo "</tr>";
		    echo "<tr>";
		    echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">Sucursal: </font></b>".$wcco."</td>";
		    echo "</tr>";
		    echo "<tr>";
		    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Empresa: </font></b>".$wemp."</td>";
		    echo "<td bgcolor=".$wcf." align=center>Clasificado por: ".$wtip."</td>";
	        echo "</table></br>";
            
            echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
            echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
            echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
            echo "<input type='HIDDEN' NAME= 'wtip' value='".$wtip."'>";
            echo "<input type='HIDDEN' NAME= 'wfeccor' value='".$wfeccor."'>";
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
                 . "    FROM  " . $wbasedato . "_000018 "
                 . "   	WHERE  fenfec between '" . $wfecini . "'"
                 . "     AND '" . $wfecfin . "'"
                 . "     AND fencod = '01' "
                 . "     AND fenest = 'on' "
                 . "     AND fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                 . "     AND fencco<>'' "
                 . "     ORDER BY  fendpa ";

                $errh = mysql_query($q, $conex);
                $numh = mysql_num_rows($errh);

                $numh;
                if ($numh > 0)
                {
                    echo "<table  align =center width='100%'>";

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
                    $clase1 = "class='texto1'";
                    $clase2 = "class='texto4'"; 
                    // se busca en la tabla 20 y 21 registros, historia por historia en un for y entre las fechas escogidas
                    for ($i = 0;$i < $numh;$i++)
                    {
                        $rowh = mysql_fetch_array($errh);

                        $q = " SELECT fennpa "
                         . "    FROM  " . $wbasedato . "_000018 "
                         . "   	WHERE  fendpa= '" . $rowh[0] . "'";

                        $errp = mysql_query($q, $conex);
                        $rowp = mysql_fetch_array($errp);

                        $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo) "
                         . "    FROM  " . $wbasedato . "_000018 a"
                         . "   	WHERE  a.fenfec between '" . $wfecini . "'"
                         . "     AND '" . $wfecfin . "'"
                         . "     AND a.fendpa = '" . $rowh[0] . "' "
                         . "     AND a.fencco LIKE '".trim($wccoe[0])."'"
                         . "     AND a.fencod = '01' "
                         . "     AND a.fenest = 'on' "
                         . "     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                         . "     AND a.fencco<>'' "
                         . "     ORDER BY  a.fenffa, a.fenfac ";

                         //On
                         
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
                            // me meto al for de factura
                            $pinto = 0;
                            for ($j = 0;$j < $num1;$j++)
                            { 
                                // busco las facturas con saldos a la fecha de corte
                                $q = " SELECT  b.rdesfa, a.renfec, b.rdefue, b.rdenum, b.rdefac, b.rdeffa "
                                    ."   FROM  " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b   "
                                    ." 	WHERE rdefac= '" . $row[1] . "' "
                                    ."    AND rdeffa= '" . $row[0] . "' "
                                    ."    AND rdeest= 'on' "
                                    ."    AND rdesfa<>'' "
                                    ."    AND rdereg = 0  "
                                    ."    AND renfec <= '" . $wfeccor . "'  " 
                                // ."     AND rencco = '".$row[4]."'  "
                                    ."    AND renfue=rdefue  "
                                    ."    AND rennum=rdenum  "
                                    ."    AND rencco=rdecco  "
                                    ."  ORDER BY  b.id desc";

                                $err2 = mysql_query($q, $conex);
                                $y = mysql_num_rows($err2);
                                $row2 = mysql_fetch_array($err2);

                                if ($y > 0)
                                {
                                    if ($row2[0] > 0)
                                    {
                                        $q = " SELECT  MIN(cast(rdesfa as UNSIGNED)), Fecha_data "
                                         . "    FROM   " . $wbasedato . "_000021 "
                                         . "   	WHERE   rdefac= '" . $row2[4] . "' "
                                         . "     AND rdeffa= '" . $row2[5] . "' "
                                         . "     AND rdeest= 'on' "
                                         . "     AND rdesfa<>'' "
                                         . "     AND rdereg=0 "
                                         . "     AND rdefue='" . $row2[2] . "'  "
                                         . "     AND rdenum='" . $row2[3] . "'  "
                                         . "   GROUP BY rdenum, rdefue  ";

                                        $err2 = mysql_query($q, $conex);
                                        $row2 = mysql_fetch_array($err2);
                                    } 
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
                                if ($y > 0)
                                { 
                                    // parto la fecha de corte
                                    $dia2 = substr($row2[1], 8, 2); // pasar el dia a una variable
                                    $mes2 = substr($row2[1], 5, 2); // pasar el mes a una variable
                                    $anyo2 = substr($row2[1], 0, 4); // pasar el año a una variable
                                } 
                                else
                                { 
                                    // parto la fecha de corte
                                    $dia2 = substr($wfeccor, 8, 2); // pasar el dia a una variable
                                    $mes2 = substr($wfeccor, 5, 2); // pasar el mes a una variable
                                    $anyo2 = substr($wfeccor, 0, 4); // pasar el año a una variable
                                } 

                                $segundos1 = mktime(0, 0, 0, $mes, $dia, $anyo); // calcular cuantos segundos han pasado desde 1970
                                $segundos2 = mktime(0, 0, 0, $mes2, $dia2, $anyo2); // calcular cuantos segundos han pasado desde 1970
                                $segundos3 = $segundos2 - $segundos1;
                                $segundos3 = $segundos3 / 86400;

                                if ($y > 0)
                                {
                                    if ($row2[0] > 0)
                                    {
                                        for ($k = 0; $k < $numran; $k++)
                                        {
                                            switch ($k)
                                            {
                                                case 0:
                                                    if ($segundos3 >= $wmax['num'][$k] and $segundos3 < ($wmax['num'][$k + 1] + 1))
                                                    {
                                                        $wmax['salFac'][$k] = $row2[0];
                                                        $saldototal = $row2[0];
                                                    } 
                                                    break;

                                                case ($numran-1):
                                                    if ($segundos3 >= $wmax['num'][$k] + 1)
                                                    {
                                                        $wmax['salFac'][$k] = $row2[0];
                                                        $saldototal = $row2[0];
                                                    } 
                                                    break;

                                                default:
                                                    if ($segundos3 > $wmax['num'][$k] and $segundos3 < ($wmax['num'][$k + 1] + 1))
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
                                                if ($segundos3 >= $wmax['num'][$k] and $segundos3 < ($wmax['num'][$k + 1] + 1))
                                                {
                                                    $wmax['salFac'][$k] = $row[2];
                                                    $saldototal = $row[2];
                                                } 
                                                break;

                                            case ($numran-1):
                                                if ($segundos3 >= $wmax['num'][$k] + 1)
                                                {
                                                    $wmax['salFac'][$k] = $row[2];
                                                    $saldototal = $row[2];
                                                } 
                                                break;

                                            default:
                                                if ($segundos3 > $wmax['num'][$k] and $segundos3 < ($wmax['num'][$k + 1] + 1))
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
                                    echo "<tr><th align=CENTER class='titulo2' COLSPAN=6   width='60%' >&nbsp;</th>";
                                    echo "<th align=CENTER class='titulo2' >TOTAL VLR FACTURA</th>";
                                    for ($k = 0; $k < $numran; $k++)
                                    {
                                        if ($k + 1 != $numran)
                                        {
                                            echo "<td align=CENTER class='titulo2' >" . $wmax['num'][$k] . "-" . $wmax['num'][$k + 1] . " DIAS</td>";
                                        } 
                                        else
                                        {
                                            echo "<td align=CENTER class='titulo2'>+" . ($wmax['num'][$k] + 1) . " DIAS</td>";
                                        } 
                                    } 
                                    echo "<th align=CENTER class='titulo2'>TOTAL SALDO FACTURA</th></TR>";

                                    $titular = 1;
                                } 
                                if (!isset($class1) or $class1 == 'acumulado7')
                                {
                                    $class1 = 'acumulado5';
                                    $class2 = 'acumulado6';
                                } 
                                else
                                {
                                    $class1 = 'acumulado7';
                                    $class2 = 'acumulado8';
                                } 

                                echo "<tr><th align=right class='" . $class1 . "' colspan='6'>TOTAL ";
                                echo $rowh[0] . "-" . $rowp[0] . ":</th>";

                                echo "<th align=CENTER class='" . $class2 . "' >" . number_format($wtotfac, 0, '.', ',') . "</th>";

                                for ($k = 0; $k < $numran; $k++)
                                {
                                    echo "<td align=CENTER class='" . $class2 . "' width='10%'>" . number_format($wmax['salEmp'][$k], 0, '.', ',') . "</td>";
                                } 

                                echo "<th align=CENTER class='" . $class2 . "' >" . number_format($wtotsal, 0, '.', ',') . "</th></tr>";
                            } 
                        } 
                    } 
                    echo "<tr><th align=right class='titulo2' colspan='14'>&nbsp;</th></tr> ";
                } 
            } 
            // si la empresa es diferente a todas las empresas, la meto en el vector solo
            // si es todas las empresas meto todas en un vector para luego preguntarlas en un for
            if ($wemp != '% - Todas las empresas')
            {
                $print = explode('-', $wemp);
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
            } 
            else
            {
                if ($wtip == 'CODIGO')
                {
                    $q = " SELECT empcod, empnom, empnit, emptem "
                     . "   FROM " . $wbasedato . "_000024 "
                     . "  WHERE empcod=empres "
                     . "    AND empest='on' "
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
                    } 
                } 

                if ($wtip == 'NIT')
                {
                    $q = " SELECT  empnom, empnit, emptem "
                     . "   FROM " . $wbasedato . "_000024 "
//                     . "  WHERE empcod=empres "
                     . "  WHERE empest='on' "
                     . "  GROUP BY empnit ORDER BY 3, 2 desc ,1 ";

                    $res = mysql_query($q, $conex);
                    $num = mysql_num_rows($res);
                    for ($i = 0;$i < $num;$i++)
                    {
                        $row = mysql_fetch_array($res);
                        $empNom[$i] = $row[0];
                        $empNit[$i] = $row[1];
                        $empTip[$i] = $row[2];
                        $empresa[$i] = $row[1] . " - " . $row[0];
                    } 
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
            $clase1 = "class='texto1'";
            $clase2 = "class='texto4'"; 
            
            //se busca en la tabla 20 y 21 registros, empresa por empresa en un for y entre las fechas escogidas
            for ($i = 0;$i < $num;$i++)
            {
                if ($wtip == 'NIT')
                {
                    $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo), a.fennpa, a.fendpa, a.fencod "
                     . "     FROM  " . $wbasedato . "_000018 a"
                     . "   	WHERE  a.fenfec between '" . $wfecini . "'"
                     . "      AND '" . $wfecfin . "'"
                     . "      AND a.fennit = '" . $empNit[$i] . "' "
                     . "      AND a.fenest = 'on' "
                     . "      AND a.fencco LIKE '".trim($wccoe[0])."'"
                     . "      AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                     . "      AND a.fencco<>'' "
                     . "    ORDER BY  a.fenffa, a.fenfac ";
                } 
                if ($wtip == 'CODIGO')
                {
                    $q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fenfec, a.fencco, a.fensal, (fenval+fenviv+fencop+fencmo+fendes+fenabo), a.fennpa, a.fendpa, a.fencod "
                     . "     FROM  " . $wbasedato . "_000018 a "
                     . "   	WHERE  a.fenfec between '" . $wfecini . "'"
                     . "      AND '" . $wfecfin . "'"
                     . "      AND a.fenres = '" . $empCod[$i] . "' "
                     . "      AND a.fenest = 'on' "
                     . "      AND a.fencco LIKE '".trim($wccoe[0])."'"
                     . "      AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
                     . "      AND a.fencco<>'' "
                     . "    ORDER BY  a.fenffa, a.fenfac ";
                } 

                $err = mysql_query($q, $conex);
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
                    // me meto al for de factura
                    $pinto = 0;
                    for ($j = 0;$j < $num1;$j++)
                    { 
                        // busco las facturas con saldos a la fecha de corte
                        $q = " SELECT  b.rdesfa, a.renfec, b.rdefue, b.rdenum, b.rdefac, b.rdeffa "
                         . "    FROM  " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b   "
                         . "   	WHERE   rdefac= '" . $row[1] . "' "
                         . "     AND rdeffa= '" . $row[0] . "' "
                         . "     AND rdeest= 'on' "
                         . "     AND rdesfa<>'' "
                         . "     AND rdereg = 0  "
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
                                $q = " SELECT  MIN(cast(rdesfa as UNSIGNED)), Fecha_data"
                                 . "    FROM   " . $wbasedato . "_000021 "
                                 . "   	WHERE   rdefac= '" . $row2[4] . "' "
                                 . "     AND rdeffa= '" . $row2[5] . "' "
                                 . "     AND rdeest= 'on' "
                                 . "     AND rdesfa<>'' "
                                 . "     AND rdereg=0 "
                                 . "     AND rdefue='" . $row2[2] . "'  "
                                 . "     AND rdenum='" . $row2[3] . "'  "
                                  . "     group by rdenum, rdefue  ";


                                $err2 = mysql_query($q, $conex);
                                $row2 = mysql_fetch_array($err2);
                            } 
                        }
						 
                        for ($k = 0; $k < $numran; $k++)
                        {
                            $wlim['salFac'][$k] = 0;
                        } 
                        $saldototal = 0; 
                        // parto la fecha de generacion de la factura
                        $dia = substr($row[3], 8, 2); // pasar el dia a una variable
                        $mes = substr($row[3], 5, 2); // pasar el mes a una variable
                        $anyo = substr($row[3], 0, 4); // pasar el año a una variable
                       
                        //2008-03-28
                        
                        /* if ($y > 0)
                        { 
                            // parto la fecha de corte
                            $dia2 = substr($row2[1], 8, 2); // pasar el dia a una variable
                            $mes2 = substr($row2[1], 5, 2); // pasar el mes a una variable
                            $anyo2 = substr($row2[1], 0, 4); // pasar el año a una variable
                        } 
                        else*/
                        { 
                            // parto la fecha de corte
                            $dia2 = substr($wfeccor, 8, 2); // pasar el dia a una variable
                            $mes2 = substr($wfeccor, 5, 2); // pasar el mes a una variable
                            $anyo2 = substr($wfeccor, 0, 4); // pasar el año a una variable
                        } 

                        $segundos1 = mktime(0, 0, 0, $mes, $dia, $anyo); // calcular cuantos segundos han pasado desde 1970
                        $segundos2 = mktime(0, 0, 0, $mes2, $dia2, $anyo2); // calcular cuantos segundos han pasado desde 1970
                        $segundos3 = $segundos2 - $segundos1;
                        $segundos3 = $segundos3 / 86400;

                        if ($y > 0)
                        {
                            if ($row2[0] > 0)
                            {
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    switch ($k)
                                    {
                                        case 0:
                                            if ($segundos3 >= $wlim['num'][$k] and $segundos3 < ($wlim['num'][$k + 1] + 1))
                                            {
                                                $wlim['salFac'][$k] = $row2[0];
                                                $saldototal = $row2[0];
                                            } 
                                            break;

                                        case ($numran-1):
                                            if ($segundos3 >= $wlim['num'][$k] + 1)
                                            {
                                                $wlim['salFac'][$k] = $row2[0];
                                                $saldototal = $row2[0];
                                            } 
                                            break;

                                        default:
                                            if ($segundos3 > $wlim['num'][$k] and $segundos3 < ($wlim['num'][$k + 1] + 1))
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
                                        if ($segundos3 >= $wlim['num'][$k] and $segundos3 < ($wlim['num'][$k + 1] + 1))
                                        {
                                            $wlim['salFac'][$k] = $row[2];
                                            $saldototal = $row[2];
                                        } 
                                        break;

                                    case ($numran-1):
                                        if ($segundos3 >= $wlim['num'][$k] + 1)
                                        {
                                            $wlim['salFac'][$k] = $row[2];
                                            $saldototal = $row[2];
                                        } 
                                        break;

                                    default:
                                        if ($segundos3 > $wlim['num'][$k] and $segundos3 < ($wlim['num'][$k + 1] + 1))
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
                                        echo "<tr><td colspan='".($numran + 9)."' class='titulo3'>Empresa: ".$empCod[$i]."-".$empNit[$i]."-".$empNom[$i]."</td></tr>";
                                    if ($wtip == 'NIT')
                                        echo "<tr><td colspan='".($numran + 9)."' class='titulo3'>Empresa: ".$empNit[$i]."-".$empNom[$i]."</td></tr>";
                                    echo "<tr><td align=CENTER class='titulo2' width='10%'>FUENTE FACTURA</td>";
                                    echo "<td align=CENTER class='titulo2' width='20%'>NRO FACTURA</td>";
                                    echo "<td align=CENTER class='titulo2' width='10%'>FECHA FACTURA</td>";
                                    echo "<td aling=center class='titulo2' width='10%'>USUARIO</td>";
                                    echo "<td aling=center class='titulo2' width='10%'>TELEFONO</td>";
                                    echo "<td aling=center class='titulo2' width='10%'>CAJA FISICA</td>";
                                    echo "<td align=CENTER class='titulo2' width='10%'>VLR FACTURA</td>";

                                    for ($k = 0; $k < $numran; $k++)
                                    {
                                        if ($k + 1 != $numran)
                                        {
                                            echo "<td align=CENTER class='titulo2' width='10%'>".$wlim['num'][$k]."-".$wlim['num'][$k + 1]." DIAS</td>";
                                        } 
                                        else
                                        {
                                            echo "<td align=CENTER class='titulo2' width='10%'>+".($wlim['num'][$k] + 1)." DIAS</td>";
                                        } 
                                    } 
                                    echo "<td align=CENTER class='titulo2' width='10%'>TOTAL</td>";
                                    echo "<td align=CENTER class='titulo2' width='10%'>IPS o Empresa</td></tr>";
                                    $pinto = 1;
                                } 

                                echo '<tr>';
                                echo "<td align=CENTER ".$clase1." width='10%'>".$row[0]."</td>";
                                echo "<td align=CENTER ".$clase1." width='20%'>".$row[1]."</td>";
                                echo "<td align=CENTER ".$clase1." width='10%'>".$row[3]."</td>";
                                echo "<td align=CENTER ".$clase1." width='10%'>".$row[7]."</td>";
                                //===========================================================================
                                //Traigo el TELEFONO de la tabla 000041 (Clientes)
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
                                //Traigo el NUMERO DE LA CAJITA de la tabla 000133 (Ordenes de laboratorio)
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
                                echo "<td align=CENTER " . $clase2 . " width='10%'>" . number_format($row[6], 0, '.', ',') . "</td>";
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    echo "<td align=CENTER " . $clase2 . " width='10%'>" . number_format($wlim['salFac'][$k], 0, '.', ',') . "</td>";
                                } 
                                echo "<td align=CENTER " . $clase2 . " width='10%'>" . number_format($saldototal, 0, '.', ',') . "</td>";
                                
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
								   echo "<td align=CENTER ".$clase2." width='20%'>".$rowemp[0]."</td>";
							       }
								  else
								     echo "<td align=CENTER ".$clase1." width='20%'>&nbsp</td>"; 
								//===========================================================================
								echo '</tr>';

                                if ($clase1 == "class='texto2'")
                                {
                                    $clase1 = "class='texto1'";
                                    $clase2 = "class='texto4'";
                                } 
                                else
                                {
                                    $clase1 = "class='texto2'";
                                    $clase2 = "class='texto5'";
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
                                echo "<tr><th align=CENTER class='titulo2' COLSPAN=6   width='60%' >&nbsp;</th>";
                                echo "<td align=CENTER class='titulo2' width='10%'>TIPO</td>";
                                echo "<th align=CENTER class='titulo2' >TOTAL VLR FACTURA</th>";
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    if ($k + 1 != $numran)
                                    {
                                        echo "<td align=CENTER class='titulo2' >" . $wlim['num'][$k] . "-" . $wlim['num'][$k + 1] . " DIAS</td>";
                                    } 
                                    else
                                    {
                                        echo "<td align=CENTER class='titulo2'>+" . ($wlim['num'][$k] + 1) . " DIAS</td>";
                                    } 
                                } 
                                echo "<th align=CENTER class='titulo2'>TOTAL SALDO FACTURA</th></TR>";
                                $pinto2 = 1;

                                $class1 = 'acumulado7';
                                $class2 = 'acumulado8';
                            } 
                            else If (!isset($pinto2))
                            {
                                $pinto2 = 1;

                                $class1 = 'acumulado7';
                                $class2 = 'acumulado8';
                            } 
                            if ($class1 == 'acumulado7')
                            {
                                $class1 = 'acumulado5';
                                $class2 = 'acumulado6';
                            } 
                            else
                            {
                                $class1 = 'acumulado7';
                                $class2 = 'acumulado8';
                            } 
                        } 
                        else
                        {
                            $class1 = 'acumulado3';
                            $class2 = 'acumulado4';
                        } 

                        if (isset($ant) and $vol == 'NO')
                        {
                            if ($empTip[$i] != $ant)
                            {
                                echo "<tr><th align=CENTER class='acumulado3' colspan='7'>TOTAL TIPO: " . $ant . "</th>";
                                echo "<th align=CENTER class='acumulado4'>" . number_format($sum1, 0, '.', ',') . "</th>";
                                for ($k = 0; $k < $numran; $k++)
                                {
                                    echo "<th align=CENTER class='acumulado4'>" . number_format($sum[$k], 0, '.', ',') . "</th>";
                                } 
                                echo "<th align=CENTER class='acumulado4'>" . number_format($sum3, 0, '.', ',') . "</th>";
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

                        echo "<tr><th align=right class='".$class1."' colspan='6'>TOTAL ";

                        if ($wtip == 'CODIGO')
                            echo $empCod[$i]."-".$empNit[$i]."-".$empNom[$i].":</th>";
                        if ($wtip == 'NIT')
                            echo $empNit[$i]."-".$empNom[$i].":</th>";

                        if ($vol == 'NO')
                        {
                            echo "<th align=CENTER class='".$class2."' >".$empTip[$i]."</th>";
                        } 
                        echo "<th align=CENTER class='".$class2."' >".number_format($wtotfac, 0, '.', ',')."</th>";

                        for ($k = 0; $k < $numran; $k++)
                        {
                            echo "<td align=CENTER class='".$class2."' width='10%'>".number_format($wlim['salEmp'][$k], 0, '.', ',')."</td>";
                        } 

                        echo "<th align=CENTER class='".$class2."' >".number_format($wtotsal, 0, '.', ',')."</th>";
                        echo "<th align=CENTER class='".$class2."' >&nbsp</th></tr>";
                    } 
                } 
            } 

            /**
            * echo "<tr><th align=CENTER class='acumulado3' colspan='4'>TOTAL TIPO: ".$ant."</th>";
            * echo "<th align=CENTER class='acumulado4'>".number_format($sum1,0,'.',',')."</th>";
            * for ($k=0; $k<$numran; $k++)
            * {
            * echo "<th align=CENTER class='acumulado4'>".number_format($sum[$k],0,'.',',')."</th>";
            * }
            * echo "<th align=CENTER class='acumulado4'>".number_format($sum3,0,'.',',')."</th>";
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
                    echo "<tr><th align=CENTER class='acumulado2' colspan='7'>TOTAL</th>";
                } 
                else
                {
                    echo "<tr><th align=CENTER class='acumulado2' colspan='6'>TOTAL</th>";
                } 
                echo "<th align=CENTER class='acumulado1'>" . number_format($wtotal, 0, '.', ',') . "</th>";
                for ($k = 0; $k < $numran; $k++)
                {
                    echo "<th align=CENTER class='acumulado1'>" . number_format($wlim['saldo'][$k], 0, '.', ',') . "</th>";
                } 
                echo "<th align=CENTER class='acumulado1'>" . number_format($wsaldo, 0, '.', ',') . "</th>";
                
                if ($vol == 'SI')
                   echo "<th align=CENTER class='acumulado2'>&nbsp</th>";
            } 
            echo "</table>";
            echo "</br><center><A href='RepCarXEdadUV.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wtip=" . $wtip . "&amp;wfeccor=" . $wfeccor . "&amp;wemp=" . $wemp . "&amp;wran=" . $wran . "&amp;bandera='1'>VOLVER</A></center>";
            echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
        } 
    } 
	liberarConexionBD($conex);
    ?>
</body>
</html>
