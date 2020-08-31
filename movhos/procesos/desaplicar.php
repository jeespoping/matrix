<head>
  <title>MEDICAMENTOS ANTERIORES</title>
  <link href="/matrix/root/caro.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
 	
	function validar()
   {
   if(window.document.desaplicar.wcan)
   {
   	textoCampo = window.document.desaplicar.wcan.value;
   	textoCampo = validarNumero(textoCampo);
   	window.document.desaplicar.wcan.value = textoCampo;
   	window.document.desaplicar.wcan.focus();
   	}
   }

   function validarNumero(valor)
   {
   	valor = parseFloat(valor);
   	if (isNaN(valor))
   	{
   		alert('Debe ingresar un numero');
   		return '';
   	}else if (valor>10000)
   	{
   		alert('Debe ingresar un numero menor o igual a 1');
   		return '';
   	}
   	else
   	{
   		return valor;
   	}

   }	
</script>					
</head>
<body onload=ira()>
<?php
include_once("conex.php");
/**
* RECIBO DEVOLUCIONES DE PACIENTES           *
* DE LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
*/
session_start();

if (!isset($user))
    if (!isset($_SESSION['user']))
        session_register("user");

    session_register("wemp_pmla");

    if (!isset($_SESSION['user']))
        echo "error";
    else
    {
        

        
 
        // $conexunix = odbc_pconnect('facturacion','facadm','1201')
        // or die("No se ralizo Conexion con el Unix");
        include_once("root/magenta.php"); 
        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
        $wactualiz = "(Version OCTUBRE de 2007)"; // Aca se coloca la ultima fecha de actualizacion de este programa //                                                                           
        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
        echo "<form name='desaplicar' action='desaplicar.php' method=post onsubmit=validar()>";
        $wfecha = date("Y-m-d");
        $whora = (string)date("H:i:s");
        $pos = strpos($user, "-");
        $wusuario = substr($user, $pos + 1, strlen($user));

        echo '<div id="header">';
        echo '<div id="logo">';
        if (!isset($reporte))
        {
            echo '<h1><a href="desaplicar.php">PROGRAMA PARA MEDICAMENTOS ANTERIORES</a></h1>';
        } 
        else
        {
            echo '<h1><a href="desaplicar.php">REPORTE DE DEVOLUCIONES</a></h1>';
        } 
        echo '<h2>CLINICA LAS AMERICAS<b>' . $wactualiz . '</h2>';
        echo '</div>';
        echo '</div></br></br></br></br></br>';

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

        if (!isset($wcco) or trim($wcco) == "")
        {
            $hini = date("H:i:s"); 
            // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
            $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
               . "   FROM ".$wtabcco.", ".$wbasedato."_000011"
               . "  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
            $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
            $num = mysql_num_rows($res);

            echo "<br>";
            echo "<br>";
            echo "<br>";

            echo '<div id="page" align="center">';
            echo '<div id="feature" class="box-pink" align="center">';
            echo '<h2 class="section"><b>SELECCIONE EL SERVICIO EN EL QUE SE ENCUENTRA:</b></h2>';
            echo '<div class="content">';
            echo '<table align=center cellspacing="10" >';
            echo "<tr><td align=center><select name='wcco' id='searchinput'>";
            echo "<option>&nbsp</option>";
            for ($i = 1;$i <= $num;$i++)
            {
                $row = mysql_fetch_array($res);
                echo "<option>" . $row[0] . " - " . $row[1] . "</option>";
            } 
            echo "</select></td></tr>";

            echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='ENTRAR'></td></tr>";
            echo "</table>";
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } 
        else
        {
            if (!isset($whis))
            {
                $wccosto = explode("-", $wcco);
                $wcco = $wccosto[0];

                $q = " SELECT cconom "
                 . "   FROM " . $wtabcco
                 . "  WHERE ccocod = '" . $wcco . "'";
                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                $row = mysql_fetch_array($res);
                $wnomcco = $row[0];

                $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced "
                 . "   FROM " . $wbasedato . "_000020, " . $wbasedato . "_000018, root_000036, root_000037 "
                 . "  WHERE habcco  = '" . $wcco . "'"
                 . "    AND habali != 'on' " // Que no este para alistar
                . "    AND habdis != 'on' " // Que no este disponible, osea que este ocupada
                . "    AND habcod  = ubihac "
                 . "    AND ubihis  = orihis "
                 . "    AND ubiing  = oriing "
                 . "    AND ubiald != 'on' "
                 . "    AND ubiptr != 'on' "
                 . "    AND ubisac  = '" . $wcco . "'"
                 . "    AND oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
                . "    AND oriced  = pacced "
                 . "    AND habhis  = ubihis "
                 . "    AND habing  = ubiing "
                 . "  GROUP BY 1,2,3,4,5,6,7,8 "
                 . "  ORDER BY 1 ";
                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                $num = mysql_num_rows($res);

                echo '<div id="page" align="center">';
                echo '<div id="feature" class="box-orange" align="center">';
                echo '<h2 class="section"><b>Servicio o Unidad: ' . $wnomcco . '</b></h2>';

                echo '<div class="content">';
                echo '<table align=center>';
                echo "<tr>";
                echo "<th>HABITACION</th>";
                echo "<th>HISTORIA</th>";
                echo "<th>INGRESO</th>";
                echo "<th>PACIENTE</th>";
                echo "</tr>";

                $whabant = "";
                if ($num > 0)
                {
                    for($i = 1;$i <= $num;$i++)
                    {
                        $row = mysql_fetch_array($res);

                        if (is_integer($i / 2))
                            $wcolor = "#ffffcc";
                        else
                            $wcolor = "#ffffff";

                        $whab = $row[0];
                        $whis = $row[1];
                        $wing = $row[2];
                        $wpac = $row[3] . " " . $row[4] . " " . $row[5] . " " . $row[6];
                        $wtid = $row[7]; //Tipo documento paciente
                        $wdpa = $row[8]; //Documento del paciente
                        if ($whabant != $whab)
                        {
                            echo "<tr>";
                            echo "<td bgcolor=" . $wcolor . ">" . $whab . "</td>";
                            echo "<td  bgcolor=" . $wcolor . ">" . $whis . "</td>";
                            echo "<td bgcolor=" . $wcolor . ">" . $wing . "</td>";
                            echo "<td  bgcolor=" . $wcolor . ">" . $wpac . "</td>";
                            echo "</tr>";

                            $whabant = $whab;
                        } 
                    } 
                    // CENTRO DE COSTO
                    echo "</table>";
                    echo "<input type='HIDDEN' name= 'wcco' value='" . $wcco . "'>";

                    echo "</br>";
                    echo "</br>";

                    ?>
						<script>
	     				function ira(){document.desaplicar.whis.focus();}
	  					</script>
	  				<?php

                    echo "<table ALIGN='CENTER'>";
                    echo "<th bgcolor='#99cc99'>INGRESE LA HISTORIA: <INPUT TYPE='text' NAME='whis' id='searchinput' size=10></th>"; //Cantidad Recibida Neta (menos el faltante)
                    echo "</table>";
                } 
                else
                {
                    echo "NO HAY HABITACIONES OCUPADAS";
                } 

                echo "</table>";

                echo '</div>';
                echo '</div>';
                echo '</div>';

                echo "<table align='center'>";
            } 
            else
            {
                $whis = trim($whis);
                $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2 "
                 . "   FROM " . $wbasedato . "_000020, root_000036, root_000037 "
                 . "  WHERE habcco  = '" . $wcco . "'"
                 . "    AND habali != 'on' " // Que no este para alistar
                . "    AND habdis != 'on' " // Que no este disponible, osea que este ocupada
                . "    AND habhis  = orihis "
                 . "    AND habing  = oriing "
                 . "    AND oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
                . "    AND oriced  = pacced "
                 . "    AND habhis  = '" . $whis . "'"
                 . "  GROUP BY 1,2,3,4,5,6 "
                 . "  ORDER BY 1 ";

                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                $num = mysql_num_rows($res);

                if ($num > 0)
                {
                    $row = mysql_fetch_array($res);

                    $whab = $row[0];
                    $whis = $row[1];
                    $wing = $row[2];
                    $wpac = $row[3] . " " . $row[4] . " " . $row[5] . " " . $row[6]; 
                    // HISTORIA
                    echo "<input type='HIDDEN' name= 'whis' value='" . $whis . "'>"; 
                    // INGRESO
                    echo "<input type='HIDDEN' name= 'wing' value='" . $wing . "'>"; 
                    // CENTRO DE COSTO
                    echo "<input type='HIDDEN' name= 'wcco' value='" . $wcco . "'>"; 
                    // PACIENTE
                    echo "<input type='HIDDEN' name= 'wpac' value='" . $wpac . "'>"; 
                    // HABITACION
                    echo "<input type='HIDDEN' name= 'whab' value='" . $whab . "'>";

                    echo '<div id="page" align="center">';
                    echo '<div id="feature" class="box-blue" align="center">';
                    echo '<h2 class="section"><b>HISTORIA: ' . $whis . '     Paciente: ' . $wpac . '     HABITACION: ' . $whab . '</b></h2>';

                    echo '<div class="content">';
                    echo '<table align=center cellspacing="20" cellpadding="10">';

                    if (!isset($warticulo))
                    {

                        ?>	    
	      					<script>
	       					function ira(){document.desaplicar.warticulo.focus();}
	      					</script>
	     			<?php

                        echo "<th bgcolor='#99cc99'>MEDICAMENTO O INSUMO: <INPUT TYPE='text' NAME='warticulo' id='searchinput' size=10></th>";
                        echo "</tr>";
                    } 
                    else
                    {
                        if (!isset($wcan) or $wcan == '')
                        { 
                            // Aca busco si el codigo es de un proveedor y traigo el codigo propio
                            $q = " SELECT artcod "
                             . "     FROM " . $wbasedato . "_000009 "
                             . "   WHERE artcba = '" . $warticulo . "'";
                            $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                            $num = mysql_num_rows($res);
                            if ($num > 0)
                            {
                                $row = mysql_fetch_array($res);
                                $warticulo = $row[0];
                            } 
                            // Aca traigo el saldo del articulo para el paciente
                            $q = " SELECT sum(aplcan), apldes, aplcco, aplapv " // Tomo la prioridad MAXIMA del centro costo que tenga saldo del articulo
                            . "   FROM " . $wbasedato . "_000015 "
                             . "  WHERE aplhis          = '" . $whis . "' "
                             . "    AND apling          = '" . $wing . "' "
                             . "    AND aplart          = '" . strtoupper($warticulo) . "'"
                             . "  GROUP BY 3, 4 "
                             . "  ORDER BY 1 desc ";

                            $res2 = mysql_query($q, $conex);
                            $row2 = mysql_fetch_array($res2);

                            if ($row2[0] > 0)
                            {
                                $wsal = $row2[0];
                                $wccoapl = $row2[2]; //Aca defino de que centro de costos del que se va  a desaplicar el insumo
                                $waprovecha = $row2[3];
                            } 
                            else
                            {
                                $wsal = 0;
                                $wccoapl = $wcco; //Aca defino de que centro de costos se va aplicar el insumo
                                $waprovecha = 'off';
                            } 

                            if ($wsal > 0) // Si hay saldo se puede hacer la desaplicacion
                                {
                                    // Por aca indica que si se puede aplicar el medicamento
                                    $q = " SELECT artcom, unides "
                                     . "   FROM " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
                                     . "  WHERE artcod = '" . strtoupper($warticulo) . "'"
                                     . "    AND artuni = unicod ";
                                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                $num = mysql_num_rows($res);

                                if ($num > 0)
                                {
                                    $row = mysql_fetch_array($res);
                                    $wdesc = $row[0];
                                    $wunid = $row[1];
                                } 
                                else
                                { 
                                    // Por aca busco si el articulo es de la central de produccion
                                    $q = " SELECT artcom, unides "
                                     . "   FROM " . $wcenmez . "_000002, " . $wbasedato . "_000027 "
                                     . "  WHERE artcod = '" . strtoupper($warticulo) . "'"
                                     . "    AND artuni = unicod ";
                                    $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                                    $num = mysql_num_rows($res);

                                    if ($num > 0)
                                    {
                                        $row = mysql_fetch_array($res);
                                        $wdesc = $row[0];
                                        $wunid = $row[1];
                                    } 
                                } 

                                ?>	    
	      							<script>
	       								function ira(){document.desaplicar.wcan.focus();}
	      							</script>
	     						<?php

                                echo "<INPUT TYPE='hidden' NAME='warticulo' value='" . $warticulo . "'>";
                                echo "<INPUT TYPE='hidden' NAME='wdesc' value='" . $wdesc . "'>";
                                echo "<INPUT TYPE='hidden' NAME='wunid' value='" . $wunid . "'>";
                                echo "<INPUT TYPE='hidden' NAME='waprovecha' value='" . $waprovecha . "'>";
                                echo "<INPUT TYPE='hidden' NAME='wccoapl' value='" . $wccoapl . "'>";
                                echo "<td>" . $warticulo . "-" . $wdesc . " ( " . $wunid . ")</th>";
                                echo "<th>CANTIDAD: <INPUT TYPE='text' NAME='wcan' VALUE='1' id='searchinput' size=10 ></th>";
                                echo "</tr>";
                                echo "<tr>";
                                echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='ACEPTAR'></td></tr>";
                                echo "</tr>";
                            } 
                            else
                            {
                                echo 'EL PACIENTE NO TIENE SALDO APLICADO DEL MEDICAMENTO O INSUMO: ' . strtoupper($warticulo);

                                ?>	    
	      						<script>
	       						function ira(){document.desaplicar.warticulo.focus();}
	      						</script>
	     							<?php

                                echo "<th bgcolor='#99cc99'>MEDICAMENTO O INSUMO: <INPUT TYPE='text' NAME='warticulo' id='searchinput' size=10></th>";
                                echo "</tr>";
                                
                                echo "</table></br><table align='center'>"; 
                                // ahora muestro los articulos que han sido desplicados en la ronda
                                $q = " SELECT aplart, apldes, SUM(aplcan), Hora_data "
                                 . "   FROM " . $wbasedato . "_000015 "
                                 . "  WHERE aplhis     = " . $whis
                                 . "    AND apling     = " . $wing
                                 . "    AND Fecha_data = '" . $wfecha . "'"
                                 . "    AND Hora_data     > '" . $hini . "'"
                                 . "    AND aplest     = 'on' "
                                 . "     AND Seguridad  = 'C-" . $wusuario . "' "
                                 . "    GROUP by Hora_data, aplart "
                                 . "    order by Hora_data desc ";

                                $res = mysql_query($q, $conex);
                                $wnr = mysql_num_rows($res);

                                echo "<tr><td align=center colspan=5><b>ARTICULOS QUE PUEDEN SER DEVUELTOS:</b></td></tr>";

                                echo "<tr>";
                                echo "<th bgcolor=3399FF>Articulo</th>";
                                echo "<th bgcolor=3399FF>Descripción</th>";
                                echo "<th bgcolor=3399FF>Presentación</th>";
                                echo "<th bgcolor=3399FF>Cantidad</th>";
                                echo "</tr>";

                                $i = 1;
                                while ($i <= $wnr)
                                {
                                    $row = mysql_fetch_row($res);

                                    $q = " SELECT unides "
                                     . "   FROM " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
                                     . "  WHERE artcod = '" . $row[0] . "'"
                                     . "    AND artuni = unicod ";
                                    $res1 = mysql_query($q, $conex);
                                    $wpres = mysql_fetch_row($res1);

                                    if ($wpres[0] == "")
                                    {
                                        $q = " SELECT unides "
                                         . "   FROM " . $wcenmez . "_000002, " . $wbasedato . "_000027 "
                                         . "  WHERE artcod = '" . $row[0] . "'"
                                         . "    AND artuni = unicod ";
                                        $res1 = mysql_query($q, $conex);
                                        $wpres = mysql_fetch_row($res1);
                                    } 

                                    if (is_integer($i / 2))
                                        $wcolor = "33FFFF";
                                    else
                                        $wcolor = "FFFFFF";

                                    echo "<tr>";
                                    echo "<td bgcolor=" . $wcolor . ">" . $row[0] . "</td>";
                                    echo "<td bgcolor=" . $wcolor . ">" . $row[1] . "</td>";
                                    echo "<td bgcolor=" . $wcolor . ">" . $wpres[0] . "</td>";
                                    echo "<td bgcolor=" . $wcolor . " align=center>" . ($row[2] * -1) . "</td>";
                                    echo "</tr>";

                                    $i = $i + 1;
                                } 
                            } 
                        } 
                        else
                        { 
                            // INSERTO APLICACIONES NEGATIVAS A LA TABLA DE APLICACION
                            $wfecha = date("Y-m-d");
                            $whora = (string)date("H:i:s");
                            $wron = date("G:i - A"); 
                            // ACA VEO SI HAY CANTIDAD SUFICIENTE PARA DEVOLVER
                            $q = " SELECT sum(aplcan) " // Tomo la prioridad MAXIMA del centro costo que tenga saldo del articulo
                               . "   FROM " . $wbasedato . "_000015 "
                               . "  WHERE aplhis = '" . $whis . "' "
                               . "    AND apling = '" . $wing . "' "
                               . "    AND aplart = '" . strtoupper($warticulo) . "'";

                            $res2 = mysql_query($q, $conex);
                            $row2 = mysql_fetch_array($res2);

                            if ($row2[0] >= $wcan)
                            {
                                $q = " SELECT sum(aplcan), apldes, aplcco, aplapv " // Tomo la prioridad MAXIMA del centro costo que tenga saldo del articulo
                                   . "   FROM " . $wbasedato . "_000015 "
                                   . "  WHERE aplhis          = '" . $whis . "' "
                                   . "    AND apling          = '" . $wing . "' "
                                   . "    AND aplart          = '" . strtoupper($warticulo) . "'"
                                   . "  GROUP BY 3, 4 "
                                   . "  ORDER BY 1 asc ";
                                $res2 = mysql_query($q, $conex);
                                $num2 = mysql_num_rows($res2);

                                $cuenta = $wcan;
                                for($y = 0;$y < $num2;$y++)
                                {
                                    $row2 = mysql_fetch_array($res2);
                                    if ($row2[0] > 0)
                                    {
                                        if ($row2[0] < $cuenta)
                                        {
                                            $wval = $row2[0];
                                            $cuenta = $cuenta - $row2[0];
                                        } 
                                        else
                                        {
                                            $wval = $cuenta;
                                            $cuenta = 0;
                                            $y = $num2;
                                        } 

                                        $q1 = " INSERT INTO " . $wbasedato . "_000015 (   Medico       ,   Fecha_data ,   Hora_data ,   aplron  ,   aplhis  ,   apling  ,   aplcco     ,   aplart                   ,   apldes   ,  aplcan        ,   aplusu      , aplapr , aplest,   aplapv     ,   aplfec    , Seguridad        ) "
                                            . "                                VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$wron."','".$whis."','".$wing."','".$row2[2]."','".strtoupper($warticulo)."','".$wdesc."',".($wval * -1).",'".$wusuario."', 'off'  , 'on'  ,'".$row2[3]."','".$wfecha."', 'C-".$wusuario."') ";
                                        $res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " No se ha eliminado la aplicación " . mysql_error());

                                        //Traigo de que centro de costo desaplico
                                        $q = " SELECT spacco "
                                           . "   FROM ".$wbasedato . "_000004 "
                                           . "  WHERE spahis  = '".$whis."'"
                                           . "    AND spaing  = '".$wing."'"
                                           . "    AND spaart  = '".$warticulo."'"
                                           . "    AND spausa >= '".$wcan."'";
                                        $res3 = mysql_query($q, $conex);
                                        $num3 = mysql_num_rows($res3);
                                        
                                        if ($num3 > 0)
                                           {
	                                        $row3 = mysql_fetch_array($res3);   
                                            $q = " UPDATE " . $wbasedato . "_000004 "
	                                           . "    SET spausa = spausa - " . $wval // Salidas Unix
	                                           . "  WHERE spahis = '" . $whis . "'"
	                                           . "    AND spaing = '" . $wing . "'"
	                                           . "    AND spaart = '" . $warticulo . "'"
	                                           . "    AND spacco = '" . $row3[0] . "'";
                                            $res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " No se ha incrementado el saldo " . mysql_error());
                                           }	
                                    } 
                                } 
                                // se confirma que todo salio bien
                                echo "<div class='alertaOk' align='center'>SE HA REALIZADO EL PROCESO CORRECTAMENTE</div>"; 
                                // vuelvo a preguntar otro articulo
                                ?>	    
	      							<script>
	       							function ira(){document.desaplicar.warticulo.focus();}
	      							</script>
	     						<?php

                                echo "<tr>";
                                echo "<th bgcolor='#99cc99' ALIGN='center'>MEDICAMENTO O INSUMO: <INPUT TYPE='text' NAME='warticulo' id='searchinput' size=10></th>";
                                echo "</tr>";
                                echo "</table></br><table align='center'>"; 
                                // ahora muestro los articulos que han sido desplicados en la ronda
                                $q = " SELECT aplart, apldes, SUM(aplcan), Hora_data "
                                 . "   FROM " . $wbasedato . "_000015 "
                                 . "  WHERE aplhis     = " . $whis
                                 . "    AND apling     = " . $wing
                                 . "    AND Fecha_data = '" . $wfecha . "'"
                                 . "    AND Hora_data     > '" . $hini . "'"
                                 . "    AND aplest     = 'on' "
                                 . "     AND Seguridad  = 'C-" . $wusuario . "' "
                                 . "    GROUP by Hora_data, aplart "
                                 . "    order by Hora_data desc ";

                                $res = mysql_query($q, $conex);
                                $wnr = mysql_num_rows($res);

                                echo "<tr><td align=center colspan=5><b>ARTICULOS QUE PUEDEN SER DEVUELTOS:</b></td></tr>";

                                echo "<tr>";
                                echo "<th bgcolor=3399FF>Articulo</th>";
                                echo "<th bgcolor=3399FF>Descripción</th>";
                                echo "<th bgcolor=3399FF>Presentación</th>";
                                echo "<th bgcolor=3399FF>Cantidad</th>";
                                echo "</tr>";

                                $i = 1;
                                while ($i <= $wnr)
                                {
                                    $row = mysql_fetch_row($res);

                                    $q = " SELECT unides "
                                     . "   FROM " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
                                     . "  WHERE artcod = '" . $row[0] . "'"
                                     . "    AND artuni = unicod ";
                                    $res1 = mysql_query($q, $conex);
                                    $wpres = mysql_fetch_row($res1);

                                    if ($wpres[0] == "")
                                    {
                                        $q = " SELECT unides "
                                         . "   FROM " . $wcenmez . "_000002, " . $wbasedato . "_000027 "
                                         . "  WHERE artcod = '" . $row[0] . "'"
                                         . "    AND artuni = unicod ";
                                        $res1 = mysql_query($q, $conex);
                                        $wpres = mysql_fetch_row($res1);
                                    } 

                                    if (is_integer($i / 2))
                                        $wcolor = "33FFFF";
                                    else
                                        $wcolor = "FFFFFF";

                                    echo "<tr>";
                                    echo "<td bgcolor=" . $wcolor . ">" . $row[0] . "</td>";
                                    echo "<td bgcolor=" . $wcolor . ">" . $row[1] . "</td>";
                                    echo "<td bgcolor=" . $wcolor . ">" . $wpres[0] . "</td>";
                                    echo "<td bgcolor=" . $wcolor . " align=center>" . ($row[2] * -1) . "</td>";
                                    echo "</tr>";

                                    $i = $i + 1;
                                } 
                            } 
                            else
                            {
                                echo 'EL USUARIO SOLO TIENE UN SALDO DE MEDICAMENTO O INSUMO APLICADO DE ' . $row2[0] . ' ' . $wunid ;

                                ?>	    
	      							<script>
	       								function ira(){document.desaplicar.wcan.focus();}
	      							</script>
	     						<?php

                                echo "<INPUT TYPE='hidden' NAME='warticulo' value='" . $warticulo . "'>";
                                echo "<INPUT TYPE='hidden' NAME='wdesc' value='" . $wdesc . "'>";
                                echo "<INPUT TYPE='hidden' NAME='wunid' value='" . $wunid . "'>";
                                echo "<INPUT TYPE='hidden' NAME='waprovecha' value='" . $waprovecha . "'>";
                                echo "<INPUT TYPE='hidden' NAME='wccoapl' value='" . $wccoapl . "'>";
                                echo "<td>" . $warticulo . "-" . $wdesc . " ( " . $wunid . ")</th>";
                                echo "<th>CANTIDAD: <INPUT TYPE='text' NAME='wcan' VALUE='" . $row2[0] . "' id='searchinput' size=10></th>";
                                echo "</tr>";
                                echo "<tr>";
                                echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='ACEPTAR'></td></tr>";
                                echo "</tr>";
                            } 
                        } 
                    } 
                } 
                else
                {
                    echo 'LA HISTORIA DIGITADA NO EXISTE EN EL SERVICIO';
                } 
                echo "</table></BR><table align='center' cellspacing='20'>";
                echo "<tr>";
                echo "<td align=center colspan=9 bgcolor='#ffffcc'><A href='desaplicar.php?wbasedato=" . $wbasedato . "&wemp_pmla=" . $wemp_pmla . "&wcco=" . $wcco . "&hini=" . $hini . "'><b>CAMBIAR DE HISTORIA</b></A></td>";
                echo "</tr>";
            } 
            echo "<tr>";
            echo "<td align=center colspan=9 bgcolor='#ffffcc'><A href='desaplicar.php?wbasedato=" . $wbasedato . "&wemp_pmla=" . $wemp_pmla . "&hini=" . $hini . "'><b>INICIO</b></A></td>";
            echo "</tr>";
            echo "</table>";
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } 
    } 

    echo "<input type='HIDDEN' name='hini' value='" . $hini . "'>";
    echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";
    echo "<input type='HIDDEN' name='wbasedato' value='" . $wbasedato . "'>";
    echo '</form>';
    include_once("free.php");

    ?>
