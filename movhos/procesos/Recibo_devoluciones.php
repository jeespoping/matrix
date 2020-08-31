<head>
  <title>RECIBO DEVOLUCIONES</title>
</head>
<body>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()		  
     }
	 
	 function enter()
	{
	 document.recibo_devoluciones.submit();
	}
	
	//Funcion que calcula la cantidad faltante segun la cantidad que se esta recibiendo.
	function calcularfaltante(i)
	{
	
	var cant_rec_oculta = $('#valor_recibido_oculto'+i).val();
	var cant_rec = $('#recibido'+i).val();	
    var faltante_aux = cant_rec_oculta - cant_rec;
	
	//Evaluar si la cantidad digitada en mayor a la cantidad devuelta, si es asi muestra el mensaje y vuelve los datos al valor inicial.
	if(parseInt(cant_rec) > parseInt(cant_rec_oculta))
	{
	alert('La cantidad a recibir no puede ser mayor a la cantidad devuelta.');
	$('#recibido'+i).val(cant_rec_oculta);
	$('#faltante'+i).val('0');
	return;
	}
	
	if(parseInt(cant_rec) < 0)
	{
	alert('La cantidad a recibir no puede ser menor a cero.');
	$('#recibido'+i).val(cant_rec_oculta);
	$('#faltante'+i).val('0');
	return;
	}
	
	//Si la cantidad faltante es mayor a vero pondra ese valor en el campo de faltante.
	if(faltante_aux > 0)
	{
	 $('#faltante'+i).val(faltante_aux);		
	}
	else	
		{
		$('#faltante'+i).val('0');	
		}
	
	}
</script>	
<?php
include_once("conex.php");
/**
* RECIBO DEVOLUCIONES DE PACIENTES           *
* DE LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
*/
// ==========================================================================================================================================
// M O D I F I C A C I O N E S 	 
// ==========================================================================================================================================
// ==========================================================================================================================================
// Septiembre 26 de 2017 Jessica Madrid Mejía	- Se modifica el query para consultar la lista de centros de costos y mostrar solo los 
// 												  hospitalarios, de urgencias, 1050 y 1051
// ===========================================================================================================================================
//  Noviembre 19 de 2013 (Jonatan Lopez)		
//	Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003, para que traiga los datos de contingencia (tabla movhos_00143) 
//  con estado activo, ademas se controla el campo de cantidad devuelta a recibir para que el faltante siempre sea calculado automaticamente y se 
//  deshabilita ese campo para que no sea modificable.
//	Se agrega control en la consulta del detalle de la devolucion para saber en que tabla debe hacer la actualizacion de la devolucion, en la tabla
//	movhos_000003 o movhos_000143.
// ==========================================================================================================================================
// Abril 29 de 2013 	:   Ing. Edwin Molina G
// ==========================================================================================================================================
//
// - Se corrige el programa para que verifique si el usuario está registrado o no
// ==========================================================================================================================================
// Diciembre 28 de 2011 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// 
// - Se Adiciona la fecha y hora del medicamento el cual fue devuelto en el query de la lineas 147 y 165 se le
//   agrego la fecha y hora de la tabla movhos_000028 
// - se quito el boton ENTRAR con el que se realiza submit y se adiciona la función javascript enter la cual envia el submit del formulario
//   desde el campo select por opcion	
session_start();

// if (!isset($user))
    // if (!isset($_SESSION['user']))
        // session_register("user");

    //session_register("wemp_pmla", "wcen_mezc");

    //if(!isset($_SESSION['user']))
    if(!isset($_SESSION['user']) and !isset($user)) //Se activa para presentaciones con Diapositivas
      die( "<b>La sesión a caducado, entre nuevamente a MATRIX o recargue la página principal</b>" );
    else
    {
        

        

        include_once("root/comun.php");
        include_once("root/magenta.php"); 
        
        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
        $wactualiz = "Septiembre 26 de 2017"; // Aca se coloca la ultima fecha de actualizacion de este programa //                          
        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
        echo "<form name='recibo_devoluciones' action='Recibo_devoluciones.php' method=post>";
        echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>"; 
        // echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
        $wfecha = date("Y-m-d");
        $whora = (string)date("H:i:s");

        encabezado("RECIBO DE DEVOLUCIONES",$wactualiz, "clinica");
                
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
            echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
        } 

        if (!isset($wcco) or trim($wcco) == "")
        { 
            // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
            // $q = " SELECT " . $wtabcco . ".ccocod, " . $wtabcco . ".cconom "
             // . "   FROM " . $wtabcco . ", " . $wbasedato . "_000011"
             // . "  WHERE " . $wtabcco . ".ccocod = " . $wbasedato . "_000011.ccocod ";
			 
			 $q = "SELECT ccocod,cconom 
					 FROM ".$wbasedato."_000011 
					WHERE (Ccohos='on' OR Ccourg='on' OR (ccofac='on' AND Ccotra='on') ) 
					  AND Ccoest='on'
					  ORDER BY ccocod;";
					  
            $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
            $num = mysql_num_rows($res);

            echo '<center><span class="subtituloPagina">SELECCIONE EL SERVICIO EN EL QUE SE ENCUENTRA:</span>';
            
            echo '<table align=center>';
            echo "<tr><td align=center><select name='wcco' id='searchinput' onchange='enter()'>";
            echo "<option>&nbsp</option>";
            for ($i = 1;$i <= $num;$i++)
            {
                $row = mysql_fetch_array($res);
                echo "<option>" . $row[0] . " - " . $row[1] . "</option>";
            } 
            echo "</select></td></tr>";

            echo "<tr></tr>";
            //echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='ENTRAR'></td></tr>";
            echo "</table>";
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

            // $q = " SELECT cconom "
             // . "   FROM " . $wtabcco
             // . "  WHERE ccocod = '" . $wcco . "'";
			 
			  $q = "SELECT cconom 
					 FROM ".$wbasedato."_000011 
					WHERE Ccocod = '".$wcco."' 
					  AND Ccoest='on';";
			
            $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
            $row = mysql_fetch_array($res);
            $wnomcco = $row[0];

            if (!isset($wnde))
            {
                $q = " CREATE TEMPORARY TABLE if not exists tempo2 as "
                 . " SELECT devcon, fenhis, fening, " . $wbasedato . "_000028.fecha_data as fecha, " . $wbasedato . "_000028.hora_data as hora, denori "
                 . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000002, " . $wbasedato . "_000035 "
                 . "  WHERE devfre = '0000-00-00' "
                 . "    AND devhre = '00:00:00' "
                 . "    AND devusu = '' "
                 . "    AND devnum = fennum "
                 . "    AND fenest = 'on' "
                 . "    AND fencco = '" . $wcco . "'"
                 . "    AND devcon = dencon "
                 . "    AND denori <> '" . $wcco . "' "
                 . "  GROUP BY 1, 2, 3, 4 ";

                $res = mysql_query($q, $conex);

                $q = "CREATE UNIQUE INDEX index_his ON tempo2 (Fenhis, Fening, Devcon)";

                $res = mysql_query($q, $conex);

                $q = " SELECT devcon, fenhis, fening, pacno1, pacno2, pacap1, pacap2, fecha, ubihac, hora, pactid, pacced, denori "
                 . "   FROM tempo2, " . $wbasedato . "_000018,  root_000036, root_000037 "
                 . "  WHERE cast(fenhis as char) = ubihis "
                 . "    AND cast(fening as char)= ubiing "
                 . "    AND ubihis = orihis "
                 . "    AND oriced = pacced "
				 . "    AND oritid = pactid "
                 . "    AND oriori = '" . $wemp_pmla . "'"
                 . "  ORDER BY 1 DESC ";

                $res = mysql_query($q, $conex);
                $num = mysql_num_rows($res);

                echo '<div id="page" align="center">';
                echo '<div id="feature" class="box-orange" align="center">';
                echo '<h2 class="section"><b>Servicio o Unidad: ' . $wnomcco . '</b></h2>';

                echo '<table align=center>';
                echo "<tr class=encabezadoTabla>";
                echo "<th>Devolucion Nro</th>";
                echo "<th>Servicio</th>";
                echo "<th>Fecha</th>";
                echo "<th>Hora</th>";
                echo "<th>Habitacion Actual</th>";
                echo "<th>Historia</th>";
                echo "<th>Ingreso</th>";
                echo "<th>Paciente</th>";
                echo "<th>Afinidad</th>";
                echo "<th>&nbsp</th>";
                echo "</tr>";

                if ($num > 0)
                {
                    for($i = 1;$i <= $num;$i++)
                    {
                        $row = mysql_fetch_array($res);

                        $wnde = $row[0];
                        $wser = $row[12]; 
                        $whis = $row[1];
                        $wing = $row[2];
                        $wpac = $row[3] . " " . $row[4] . " " . $row[5] . " " . $row[6];
                        $wfec = $row[7];
                        $whab = $row[8];
                        $whor = $row[9];
                        $wtid = $row[10]; //Tipo documento paciente
                        $wdpa = $row[11]; //Documento del paciente
                        if (is_integer($i / 2))
                           $wclass="fila1";
                          else
                            $wclass="fila2";

                        echo "<tr class=".$wclass.">";
                        echo "<td align=center>".$wnde."</td>";
                        echo "<td align=center>".$wser."</td>";
                        echo "<td align=center>".$wfec."</td>";
                        echo "<td align=center>".$whor."</td>";
                        echo "<td align=center>".$whab."</td>";
                        echo "<td align=center>".$whis."</td>";
                        echo "<td align=center>".$wing."</td>";
                        echo "<td align=left  >".$wpac."</td>"; 
                        // ======================================================================================================
                        // En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
                        $wafin = clienteMagenta($wdpa, $wtid, $wtpa, $wcolorpac);
                        if ($wafin)
                        {
                            echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
                        } 
                        else
                            echo "<td>&nbsp</td>"; 
                        // ======================================================================================================
                        echo "<td align=center class=link><A href='Recibo_devoluciones.php?wnde=".$wnde."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&whis=".$whis."&wing=".$wing."&wpac=".$wpac."&wcco=".trim($wcco)."&whab=".$whab."&wser=".$wser."'>Recibir</A></td>";
                        echo "</tr>";
                    } 
                    echo "<meta http-equiv='refresh' content='40;url=Recibo_devoluciones.php?wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'>";
                } 
                else
                {
                    echo "NO HAY DEVOLUCIONES PENDIENTES DE RECIBO";
                } 
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } 
            else
            {
                $q = " CREATE TEMPORARY TABLE if not exists tempo1 as "
                 . " SELECT fdeart, artcom, sum(fdecan) as fdecan, 0 AS aplaap, devjus, devcfs, devjud, fencco, '' as desdes,  '000003' AS tabla_origen "
                 . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000003, " . $wbasedato . "_000026, " . $wbasedato . "_000002, " . $wbasedato . "_000011 "
                 . "  WHERE devcon = " . $wnde
                 . "    AND devnum = fdenum "
                 . "    AND devnum = fennum "
                 . "    AND devlin = fdelin "
                 . "    AND fdeart = artcod "
                 . "    AND fdeest = 'on' "
                 . "    AND fdecan > 0 "
                 . "    AND fencco like '" . $wcco . "'"
                 . "    AND fencco = Ccocod "
                 . "    AND Ccoima='off'  "
                 . "  GROUP BY 1, 2, 4,5,6,7,8  "
				/*********************************************************************************************************************/
				/* Noviembre 19 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
				/*********************************************************************************************************************/
				 . "  UNION"
				 . " SELECT fdeart, artcom, sum(fdecan) as fdecan, 0 AS aplaap, devjus, devcfs, devjud, fencco, '' as desdes, '000143' AS tabla_origen  "
                 . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000143, " . $wbasedato . "_000026, " . $wbasedato . "_000002, " . $wbasedato . "_000011 "
                 . "  WHERE devcon = " . $wnde
                 . "    AND devnum = fdenum "
                 . "    AND devnum = fennum "
                 . "    AND devlin = fdelin "
                 . "    AND fdeart = artcod "                
                 . "    AND fdecan > 0 "
                 . "    AND fencco like '" . $wcco . "'"
                 . "    AND fencco = Ccocod "
                 . "    AND Ccoima='off'  "
				 . "	AND Fdeest = 'on'" //Solo los registros activos de la tabla movhos_000143
                 . "  GROUP BY 1, 2, 4,5,6,7,8  "
                 . "  UNION " // ==== Articulos de Dispensacion Descartados ==== //
                 . " SELECT desart, artcom, 0 AS fdecan, sum(descan) as aplaap, '' as devjus, 0 as devcfs, '' as devjud, descco as fencco, desdes, '000003' AS tabla_origen "
                 . "   FROM  " . $wbasedato . "_000026," . $wbasedato . "_000031 "
                 . "  WHERE descon = " . $wnde
                 . "    AND desart = artcod "
                 . "    AND descan > 0 "
                 . "    AND descco like '" . $wcco . "'"
                 . "  GROUP BY 1, 2, 8, 9 "
                 . "  UNION " // ==== Articulos Central de Mezclas ==== //
                 . " SELECT fdeari, artcom, COUNT(distinct(fdenum)) as fdecan, 0 AS aplaap, devjus, devcfs, devjud, fencco, '' as desdes, '000003' AS tabla_origen"
                 . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000003, " . $wcenmez . "_000002, " . $wbasedato . "_000002, " . $wbasedato . "_000011 "
                 . "  WHERE devcon = " . $wnde
                 . "    AND devnum = fdenum "
                 . "    AND devnum = fennum "
                 . "    AND fdeari = artcod "
                 . "    AND fdeest = 'on' "
                 . "    AND fdecan > 0 "
                 . "    AND fdelot <> '' "
                 . "    AND fencco like '" . $wcco . "'"
                 . "    AND fencco = Ccocod "
                 . "    AND Ccoima='on'  "
                 . "  GROUP BY 1, 2, 4,5,6,7,8  "
				/*********************************************************************************************************************/
				/* Noviembre 19 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
				/*********************************************************************************************************************/
				 . "  UNION "
				 . " SELECT fdeari, artcom, COUNT(distinct(fdenum)) as fdecan, 0 AS aplaap, devjus, devcfs, devjud, fencco, '' as desdes, '000143' AS tabla_origen"
                 . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000143, " . $wcenmez . "_000002, " . $wbasedato . "_000002, " . $wbasedato . "_000011 "
                 . "  WHERE devcon = " . $wnde
                 . "    AND devnum = fdenum "
                 . "    AND devnum = fennum "
                 . "    AND fdeari = artcod "                 
                 . "    AND fdecan > 0 "
                 . "    AND fdelot <> '' "
                 . "    AND fencco like '" . $wcco . "'"
                 . "    AND fencco = Ccocod "
                 . "    AND Ccoima='on'  "
				 . "	AND Fdeest = 'on'" //Solo los registros activos de la tabla movhos_000143
                 . "  GROUP BY 1, 2, 4,5,6,7,8  "
                 . "  UNION " // ==== Articulos Central de Mezclas Descartados ==== //
                 . " SELECT desart, artcom, 0 as fdecan, sum(descan) as aplaap, '' as devjus, 0 as devcfs, '' as devjud, descco as fencco, desdes, '000003' AS tabla_origen "
                 . "   FROM " . $wcenmez . "_000002, " . $wbasedato . "_000031 "
                 . "  WHERE descon = " . $wnde
                 . "    AND desart = artcod "
                 . "    AND descan > 0 "
                 . "    AND descco like '" . $wcco . "'"
                 . "  GROUP BY 1, 2, 8, 9 ";

                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                if (!isset($reporte))
                {
                    $q = " SELECT fdeart, artcom, sum(fdecan), sum(aplaap), devjus, sum(devcfs), devjud, fencco, desdes, tabla_origen "
                     . "   FROM tempo1 "
                     . "    WHERE desdes<>'02' "
                     . "   GROUP BY 1, 2,8 "
                     . "   ORDER BY 2 ";
                } 
                else
                {
                    $q = " SELECT fdeart, artcom, sum(fdecan), sum(aplaap), devjus, sum(devcfs), devjud, fencco, desdes, tabla_origen "
                     . "   FROM tempo1 "
                     . "  GROUP BY 1, 2, 8 "
                     . "  ORDER BY 8,2 ";
                } 
	
                $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                $num = mysql_num_rows($res);

                if ($num > 0 and !isset($reporte))
                {
                    echo "<center><span class=subtituloPagina2>Servicio o Unidad: ".$wnomcco."  Devolucion Numero: ".$wnde."    Servicio: ".$wser."    Historia: ".$whis."  Habitacion: ".$whab."</span></center><br>";
                    echo "<br>";
                    
                    echo '<table align=center>';

                    echo "<tr class=encabezadoTabla>";
                    echo "<th colspan=2>&nbsp</th>";
                    echo "<th colspan=6>Cantidad</th>";
                    echo "<th colspan=3>Justificacion Faltante</th>";
                    echo "</tr>";

                    echo "<tr class=encabezadoTabla>";
                    echo "<th colspan=2>&nbsp</th>";
                    echo "<th colspan=4>&nbsp</th>";
                    echo "<th colspan=2>Faltante</th>";
                    echo "<th colspan=5>&nbsp</th>";
                    echo "</tr>";

                    echo "<tr class=encabezadoTabla>";
                    echo "<th>Articulo</th>";
                    echo "<th>Descripcion</th>";
                    echo "<th>Devuelta</th>";
                    echo "<th>Razón</th>";
                    echo "<th >Descartada</th>";
                    echo "<th>Recibido</th>";
                    echo "<th>" . $wnomcco . "</th>";
                    echo "<th>Servicio Origen</th>";
                    echo "<th>Servicio Origen</th>";
                    echo "<th>" . $wnomcco . "</th>";
                    echo "<th>Observacion</th>";
                    echo "</tr>";

                    $qc = " SELECT Ccoima "
                        . "   FROM " . $wbasedato . "_000011 "
                        . "  WHERE Ccocod = '" . $wcco . "'";
                    $resc = mysql_query($qc, $conex) ;
                    $rowc = mysql_fetch_array($resc);

                    for($i = 1;$i <= $num;$i++)
                    {
                        $row = mysql_fetch_array($res);

                        if (is_integer($i / 2))
                            $wclass = "fila1";
                        else
                            $wclass = "fila2";

                        echo "<tr class=".$wclass.">";
                        echo "<td align=center>".$row[0]."</font></td>";   //Articulo
                        echo "<td align=left>".$row[1]."</td>";            //Descripcion
                        echo "<td align=center><b>".($row[2])."</b></td>"; //Cantidad Devuelta
                        echo "<td align=center><b>".($row[6])."</b></td>"; //Razón de la Devolución
                        echo "<td align=center><b>".$row[3]."</b></td>";   //Cantidad Descarte  
                        if (!isset($wcre[$i]))
							{
                            echo "<td><INPUT TYPE='text' id='recibido".$i."' NAME='wcre[".$i."]' value=".($row[2] - $row[5])." onchange='calcularfaltante(\"".$i."\");' size=7></td>"; //Cantidad Recibida Neta (menos el faltante)
							echo "<INPUT TYPE='hidden' id='valor_recibido_oculto".$i."' name='val_oculto".$i."' value=".($row[2] - $row[5]).">"; //Cantidad devuelta Neta inicial (menos el faltante)							
							}
                          else
							{
                            echo "<td><INPUT TYPE='text' id='recibido".$i."' NAME='wcre[".$i."]' value=".$wcre[$i]." onchange='calcularfaltante(\"".$i."\");' size=7></td>"; //Cantidad Recibida
							echo "<INPUT TYPE='hidden'  id='valor_recibido_oculto".$i."' value=".($row[2] - $row[5]).">"; //Cantidad devuelta inicial oculta.							
							}
                        if (!isset($wfal[$i]))
                            echo "<td><INPUT TYPE='text' NAME='wfal[".$i."]' id='faltante".$i."' value='0' readonly size=7></td>"; //Cantidad Faltante en Recibo
                          else
                            echo "<td><INPUT TYPE='text' NAME='wfal[".$i."]' id='faltante".$i."' value='".$wfal[$i]."' readonly size=7></td>"; //Cantidad Faltante en Recibo
                        echo "<td align=center><b>".$row[5]."</font></td>"; //Cantidad Faltante Servicio Origen 
                        if ($rowc[0] == 'on')
                        {
                            $q = " SELECT devcfs, devjus  "
                             . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000003, " . $wbasedato . "_000002 "
                             . "  WHERE devcon = " . $wnde
                             . "    AND devnum = fdenum "
                             . "    AND devnum = fennum "
                             . "    AND devjus <> '' "
                             . "    AND fdeari = '" . $row[0] . "' "
                             . "    AND fdeest = 'on' "
                             . "    AND fdecan > 0 "
                             . "    AND fencco like '" . $wcco . "'"	
							/*********************************************************************************************************************/
							/* Noviembre 19 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
							/*********************************************************************************************************************/
							 . "  UNION "
							 . " SELECT devcfs, devjus  "
                             . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000143, " . $wbasedato . "_000002 "
                             . "  WHERE devcon = " . $wnde
                             . "    AND devnum = fdenum "
                             . "    AND devnum = fennum "
                             . "    AND devjus <> '' "
                             . "    AND fdeari = '" . $row[0] . "' "                             
                             . "    AND fdecan > 0 "
                             . "    AND fencco like '" . $wcco . "'"
							 . "    AND fdeest = 'on'"; //Solo los registros activos de la tabla movhos_000143
                        } 
                        else
                        {
                            $q = " SELECT devcfs, devjus  "
                             . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000003, " . $wbasedato . "_000002 "
                             . "  WHERE devcon = " . $wnde
                             . "    AND devnum = fdenum "
                             . "    AND devnum = fennum "
                             . "    AND devjus <> '' "
                             . "    AND devlin = fdelin "
                             . "    AND fdeart = '" . $row[0] . "' "
                             . "    AND fdeest = 'on' "
                             . "    AND fdecan > 0 "
                             . "    AND fencco like '" . $wcco . "'"
							/*********************************************************************************************************************/
							/* Noviembre 19 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
							/*********************************************************************************************************************/
							 . "  UNION "
							 . " SELECT devcfs, devjus  "
                             . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000143, " . $wbasedato . "_000002 "
                             . "  WHERE devcon = " . $wnde
                             . "    AND devnum = fdenum "
                             . "    AND devnum = fennum "
                             . "    AND devjus <> '' "
                             . "    AND devlin = fdelin "
                             . "    AND fdeart = '" . $row[0] . "' "                             
                             . "    AND fdecan > 0 "
                             . "    AND fencco like '" . $wcco . "'"
							 . "    AND fdeest = 'on'"; //Solo los registros activos de la tabla movhos_000143
                        } 
                        $resjus = mysql_query($q, $conex);
                        $numjus = mysql_num_rows($resjus);

                        $jus = '';
                        for($y = 1;$y <= $numjus;$y++)
                        {
                            $rowjus = mysql_fetch_array($resjus);
                            $jus = $jus . ' (' . $rowjus[0] . ')' . $rowjus[1] . ' ';
                        } 

                        echo "<td align=center>".$jus."</font></td>"; //Justif. Faltante Servicio Origen
                        $q = " SELECT juscod, jusdes "
                         . "   FROM " . $wbasedato . "_000023 "
                         . "  WHERE jusest = 'on'  and justip='F' ";
                        $resjus = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
                        $numjus = mysql_num_rows($resjus);

                        if ($numjus > 0)
                        {
                            echo "<td><SELECT name='wjfa[".$i."]' id='searchinput'>"; //Justificacion Faltante Serv. Farmaceutico
                            if (!isset($wjfa[$i]))
                                echo "<OPTION>Seleccionar...</option>";
                            else
                                echo "<OPTION SELECTED>" . $wjfa[$i] . "</option>";

                            for($j = 0;$j < $numjus;$j++)
                            {
                                $rowjus = mysql_fetch_array($resjus);
                                echo "<OPTION>".$rowjus[0]."-".$rowjus[1]."</option>";
                            } 
                            echo "</SELECT></td>";
                            if (!isset($wobs[$i]))
                                echo "<td><textarea name='wobs[".$i."]' cols='20' rows='1' id='searchinput'>&nbsp;</textarea></td>";
                            else
                                echo "<td><textarea name='wobs[".$i."]' cols='20' rows='1' id='searchinput'>".$wobs[$i]."</textarea></td>";
                        } 
                        // echo "<td align=center bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wjfa'></td>";
                        echo "</tr>";

                        if (isset($wgrabar))
                        {
							$wtabla_origen = $row['tabla_origen']; //Dependiendo del dato 'tabla_origen' en la tabla temporal tempo1 se hara la actualizacion.
							
                            if ($rowc[0] == 'on')
                            { 								
                                // Aca actualizo cada uno de los articulos de la devolucion colocando la cantidad recibida y faltante con su justificacion si la hay
                                $q = " UPDATE " . $wbasedato . "_000028," . $wbasedato . "_".$wtabla_origen.", " . $wbasedato . "_000002  "
                                 . "    SET devcrf ='" . $wcre[$i] . "',"
                                 . "        devcff ='" . $wfal[$i] . "',"
                                 . "        devjuf ='" . $wjfa[$i] . "',"
                                 . "        devobs ='" . $wobs[$i] . "',"
                                 . "    	devfre ='" . $wfecha . "',"
                                 . "        devhre ='" . $whora . "',"
                                 . "        devusu ='" . $wuser . "'"
                                 . "  WHERE devcon =" . $wnde
                                 . "    AND devnum = fdenum "
                                 . "    AND fdeari = '" . $row[0] . "'"
                                 . "    AND fdenum = fennum "
                                 . "    AND fencco = '" . $wcco . "'";
                            } 
                            else
                            { 
                                // Aca actualizo cada uno de los articulos de la devolucion colocando la cantidad recibida y faltante con su justificacion si la hay
                                $q = " UPDATE " . $wbasedato . "_000028," . $wbasedato . "_".$wtabla_origen.", " . $wbasedato . "_000002  "
                                 . "    SET devcrf ='" . $wcre[$i] . "',"
                                 . "        devcff ='" . $wfal[$i] . "',"
                                 . "        devjuf ='" . $wjfa[$i] . "',"
                                 . "        devobs ='" . $wobs[$i] . "',"
                                 . "    	devfre ='" . $wfecha . "',"
                                 . "        devhre ='" . $whora . "',"
                                 . "        devusu ='" . $wuser . "'"
                                 . "  WHERE devcon =" . $wnde
                                 . "    AND devlin = fdelin "
                                 . "    AND devnum = fdenum "
                                 . "    AND fdeart = '" . $row[0] . "'"
                                 . "    AND fdenum = fennum "
                                 . "    AND fencco = '" . $wcco . "'";
                            } 
                            $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());							
                        } 
						
						
                    } 

                    if (!isset($wgrabar)) // Esto lo hago para que cuando grabe se desaparezca la opcion de volver a grabar
                        {
                         echo "<tr class=boton>";
                         echo "<td align=center colspan=11><input type='checkbox' name='wgrabar' id='searchinput'>&nbsp;&nbsp;<input type='submit' value='Grabar Recibo' id='searchsubmit'></td>";
                         echo "</tr>";
                    } 
                    else
                    {
                        echo "SE HA GRABADO CORRECTAMENTE EL RECIBO DE DEVOLUCION";
                    } 
                } 
                else if ($num > 0 and isset($reporte))
                {
                    echo "<center><span class=subtituloPagina2>DEVOLUCION NUMERO: ".$wnde."    HISTORIA: ".$historia."     INGRESO: ".$ingreso."</span></center><br>";
                    echo "<br>";
                    
                    echo '<table align=center>';

                    echo "<tr class=encabezadoTabla>";
                    echo "<th>Centro de costos</th>";
                    echo "<th>Articulo</th>";
                    echo "<th>Cantidad Devuelta</th>";
                    echo "<th>Justificante de devolucion</th>";
                    echo "<th>Cantidad Faltante</th>";
                    echo "<th>Justificacion Faltante</th>";
                    echo "<th>Cantidad Descartada</th>";
                    echo "<th>Destino de descarte</th>";
                    echo "</tr>";

                    for($i = 1;$i <= $num;$i++)
                    {
                        $row = mysql_fetch_array($res);

                        if (is_integer($i / 2))
                            $wclass = "fila1";
                        else
                            $wclass = "fila2";

                        if ($row[3] > 0)
                        {
                            $q = " SELECT desdes, jusdes  "
                             . "   FROM " . $wbasedato . "_000031, " . $wbasedato . "_000023"
                             . "  WHERE descon = " . $wnde
                             . "    AND desart = '" . $row[0] . "' "
                             . "    AND descco = '" . $row[7] . "'"
                             . "    AND juscod = desdes "
                             . "    AND justip = 'S' "
                             . "    AND jusest = 'on' " ;

                            $res5 = mysql_query($q, $conex);
                            $row5 = mysql_fetch_array($res5);
                            $jus = $row5[0] . "-" . $row5[1]; //descarte 
                        } 
                        else
                        {
                            $jus = ''; //descarte 
                        } 

                        echo "<tr>";
                        echo "<td align=center>".$row[7]."</td>"; //Articulo
                        echo "<td align=center>".$row[0]."-".$row[1]."</td>"; //Articulo
                        echo "<td align=center><b>".$row[2]."</b></td>"; //Cantidad Devuelta
                        $q = " SELECT devces, devjud  "
                         . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000003, " . $wbasedato . "_000002 "
                         . "  WHERE devcon = " . $wnde
                         . "    AND devnum = fdenum "
                         . "    AND devnum = fennum "
                         . "    AND devjud <> '' "
                         . "    AND devlin = fdelin "
                         . "    AND fdeart = '" . $row[0] . "' "
                         . "    AND fdeest = 'on' "
                         . "    AND fdecan > 0 "
                         . "    AND fencco like '" . $wcco . "'"
						 /*********************************************************************************************************************/
						 /* Noviembre 19 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
						 /*********************************************************************************************************************/
						 . "  UNION "
						 . " SELECT devces, devjud  "
                         . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000143, " . $wbasedato . "_000002 "
                         . "  WHERE devcon = " . $wnde
                         . "    AND devnum = fdenum "
                         . "    AND devnum = fennum "
                         . "    AND devjud <> '' "
                         . "    AND devlin = fdelin "
                         . "    AND fdeart = '" . $row[0] . "' "
                         . "    AND fdeest = 'on' "
                         . "    AND fdecan > 0 "
                         . "    AND fencco like '" . $wcco . "'"
						 . "    AND fdeest = 'on'"; //Solo los registros activos de la tabla movhos_000143
                        $resjus = mysql_query($q, $conex);
                        $numjus = mysql_num_rows($resjus);

                        $jus = '';
                        for($y = 1;$y <= $numjus;$y++)
                        {
                            $rowjus = mysql_fetch_array($resjus);
                            $jus = $jus . ' (' . $rowjus[0] . ')' . $rowjus[1] . ' </br>';
                        } 

                        echo "<td align=center bgcolor=" . $wcolor . ">" . $jus . "</td>"; //Justificante de devolucion
                        echo "<td align=center bgcolor=" . $wcolor . "><b>" . $row[5] . "</b></td>"; //Cantidad faltante
                        $q = " SELECT devcfs, devjus  "
                         . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000003, " . $wbasedato . "_000002 "
                         . "  WHERE devcon = " . $wnde
                         . "    AND devnum = fdenum "
                         . "    AND devnum = fennum "
                         . "    AND devjus <> '' "
                         . "    AND devlin = fdelin "
                         . "    AND fdeart = '" . $row[0] . "' "
                         . "    AND fdeest = 'on' "
                         . "    AND fdecan > 0 "
                         . "    AND fencco like '" . $wcco . "'"
						/*********************************************************************************************************************/
						/* Noviembre 19 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
						/*********************************************************************************************************************/
						 . "  UNION "
						 . " SELECT devcfs, devjus  "
                         . "   FROM " . $wbasedato . "_000028, " . $wbasedato . "_000143, " . $wbasedato . "_000002 "
                         . "  WHERE devcon = " . $wnde
                         . "    AND devnum = fdenum "
                         . "    AND devnum = fennum "
                         . "    AND devjus <> '' "
                         . "    AND devlin = fdelin "
                         . "    AND fdeart = '" . $row[0] . "' "
                         . "    AND fdeest = 'on' "
                         . "    AND fdecan > 0 "
                         . "    AND fencco like '" . $wcco . "'"
						 . "    AND fdeest = 'on'"; //Solo los registros activos de la tabla movhos_000143
                        $resjus = mysql_query($q, $conex);
                        $numjus = mysql_num_rows($resjus);

                        $jus = '';
                        for($y = 1;$y <= $numjus;$y++)
                        {
                            $rowjus = mysql_fetch_array($resjus);
                            $jus = $jus.' ('.$rowjus[0].')'.$rowjus[1].' </br>';
                        } 
                        echo "<td align=center bgcolor=".$wcolor.">".$jus."</td>"; //Justificacion faltante  
                        echo "<td align=center bgcolor=".$wcolor."><b>".$row[3]."</b></td>"; //Cantidad descarte 
                        $q = " SELECT descan, desdes, jusdes  "
                         . "   FROM  ".$wbasedato."_000031, ".$wbasedato."_000023  "
                         . "  WHERE descon = ".$wnde
                         . "    AND desart = '".$row[0]."' "
                         . "    AND descan > 0 "
                         . "    AND descco like '".$wcco."'"
                         . "    AND juscod = desdes "
                         . "    AND justip = 'S'"
                         . "    AND jusest = 'on'";

                        $resjus = mysql_query($q, $conex);
                        $numjus = mysql_num_rows($resjus);

                        $jus = '';
                        for($y = 1;$y <= $numjus;$y++)
                        {
                            $rowjus = mysql_fetch_array($resjus);
                            $jus = $jus.' ('.$rowjus[0].')'.$rowjus[2].' </br>';
                        } 
                        echo "<td align=center>".$jus."</td>"; //Cantidad descarte   
                        echo "</tr>";
                    } 
                } 

                if (!isset($reporte))
                { 
                    // CENTRO DE COSTO
                    echo "<input type='HIDDEN' name= 'wcco' value='" . $wcco . "'>";
                    echo "<input type='HIDDEN' name= 'wnde' value='" . $wnde . "'>";
                    echo "<input type='HIDDEN' name= 'whis' value='" . $whis . "'>";
                    echo "<input type='HIDDEN' name= 'whab' value='" . $whab . "'>";
					echo "<input type='HIDDEN' name= 'wser' value='" . $wser . "'>";
                    echo "<tr>";
                    echo "<td align=center colspan=11><A href='Recibo_devoluciones.php?wbasedato=" . $wbasedato . "&wemp_pmla=" . $wemp_pmla . "&wcco=" . $wcco . "'><b>Retornar</b></A></td>";
                    echo "</tr>";
                } 
            } 
            if (!isset($reporte))
            {
                echo "<tr>";
                echo "<th align=center colspan=11><A href='Recibo_devoluciones.php?wbasedato=" . $wbasedato . "&wemp_pmla=" . $wemp_pmla . "'><b>Inicio</b></A></td>";
                echo "</tr>";
            } 
        } 
        echo "</table>";
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } // if de register
    echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";
    echo "<input type='HIDDEN' name='wbasedato' value='" . $wbasedato . "'>";

    echo "<br>";
    echo "<center><table>"; 
    echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
    include_once("free.php");

    ?>
