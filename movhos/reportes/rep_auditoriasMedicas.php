<?php
include_once("conex.php");
/************************************************************************************************************
 * Reporte      :   Auditorias Medicas por entidad, centro de costos y rango de fechas
 * Fecha        :   2014-07-15
 * Por          :   Camilo Zapata
 * Descripcion  :   El objetivo de este programa es presentar la cantidad de auditorias realizadas dentro
                    del conjunto de parámetros establecidos( fechas, entidades responsables de pacientes y/o centros de costos)
 *********************************************************************************************************
 Actualizaciones.
 2014-09-22 --  Camilo Zapata: se realizan modificaciones para mostrar la información organizada por auditor, de tal manera que se sepa las auditorias
                                realiazadas por auditor a los pacientes cuyas entidades responsables están asociadas al usuairo específico.
 **********************************************************************************************************/
    if( !isset($_SESSION['user']) ){//session muerta en una petición ajax

        echo "<br /><br /><br /><br />
                <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                    [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
                </div>";
        return;
    }

    //--> SI EXISTE UNA PETICION AJAX, RECIBO Y ASIGNO LAS VARIABLES POST.
    if( isset($peticionAjax) ){

        $wemp_pmla = $_REQUEST['wemp_pmla'];
        $wbasedato = $_REQUEST['wbasedato'];
        $wcliame   = $_REQUEST['wcliame'];
        //echo $wcliame;
        $wservicio = $_REQUEST['wservicio'];
        $wentidad  = $_REQUEST['wentidad'];
        $wempresas = $_REQUEST['wempresas'];
        $wfec_i    = $_REQUEST['wfec_i'];
        $wfec_f    = $_REQUEST['wfec_f'];

        $wentidad  = explode( "|", $wentidad );
        $wempresas = str_replace( '\'', '', $wempresas );
        $wempresas = str_replace( '\"', '', $wempresas );
    }

    //-->FUNCIONES.
    function consultarEntidades( $auditor="" ){
        global $conex;
        global $wbasedato;
        global $wcliame;
        global $wemp_pmla;
        global $peticionAjax;

        $caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
        $caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

        ( trim( $auditor != "" ) and $auditor != "%" ) ? $condicion1 = "JOIN ".$wbasedato."_000167 b on Eauemp = Empcod" : $condicion1 = "LEFT JOIN ".$wbasedato."_000167 b on Eauemp = Empcod";
        ( trim( $auditor != "" ) and $auditor != "%" ) ? $condicion = " WHERE Eaucau ='{$auditor}' " : $condicion = "";

        //traer lista de auditorias para el paciente
        $query = "  SELECT      Empnit as nit, Empcod as codigo, Empnom as nombre, Eaucau auditor "
                 ."   FROM      ".$wcliame."_000024 a "
                 .$condicion1
                 .$condicion
                 ."    AND Empest = 'on'"
                ."   ORDER BY   Empnom";

       /* if( isset($peticionAjax) )
            echo $query;*/
        $num = 0;
        $res = mysql_query($query, $conex) or die( mysql_error() );
        $num = mysql_num_rows($res);

        $arreglo = array();
        if ($num > 0 ){
                while( $row = mysql_fetch_assoc($res) ){
                    $row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
                    $row['nombre'] = utf8_decode( $row['nombre'] );
                    array_push($arreglo,  trim($row['codigo']).", ".trim($row['nit']).", ".trim($row['nombre']) );
                }
            array_push( $arreglo, "%, Todos" );
        }
        return $arreglo;
    }

    function consultaCentrosCostosPropia( $ccos, $wcco, $tipoCco='' ){

        global $wemp_pmla, $conex, $wbasedato;

        $cadena    = explode(",", $ccos);
        $respuesta = array();
        ( $wcco != "%" ) ? $condicionCco = " AND Ccocod = '{$wcco}' " : $condicionCco = "";

        if( count($cadena) == 1 and $cadena[0] == "ccohos" ){
            $condicionHospitalarios = " AND Ccocir = 'off' And Ccourg = 'off'";
        }else{
            if($tipoCco != "")
                $condicionHospitalarios = " AND $tipoCco = 'on' ";
            else
                $condicionHospitalarios = "";
        }

        $q = " SELECT Ccocod, UPPER( Cconom ) Cconom, ccohos, ccourg, ccocir
                FROM ".$wbasedato."_000011
               WHERE Ccoest = 'on' and Ccocod != '*' and ";

        $where="";
        for ($j=0; $j < count( $cadena ); $j++ )
        {
            if ( $j > 0 ){
                $where .= " or ".$cadena[$j]." = 'on' ";
            }else{
                $where .= " ( ".$cadena[$j]." = 'on' ";
            }
        }
        $q = $q." ".$where." ) ";
        $q = $q." ".$condicionCco."";
        $q = $q." ".$condicionHospitalarios."";
        $q = $q." ORDER BY Ccoord, Ccocod ";  //ordenar por el campo Ccoord

        $res1 = mysql_query($q,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
        $num1 = mysql_num_rows($res1);

        while( $row = mysql_fetch_array( $res1 ) ){
            $respuesta[$row['Ccocod']]['Codigo'] =  $row['Ccocod'];
            $respuesta[$row['Ccocod']]['Nombre'] =  $row['Cconom'];
            $respuesta[$row['Ccocod']]['ccohos'] =  $row['ccohos'];
            $respuesta[$row['Ccocod']]['ccocir'] =  $row['ccocir'];
            $respuesta[$row['Ccocod']]['ccourg'] =  $row['ccourg'];
        }
        return( $respuesta );
    }

    function traer_medico_tte($whis, $wing, &$i){
        global $conex;
        global $wbasedato;

        $q = " SELECT Distinct Medno1, Medno2, Medap1, Medap2  "
            ."   FROM ".$wbasedato."_000047, ".$wbasedato."_000048 "
            ."  WHERE methis = '".$whis."'"
            ."    AND meting = '".$wing."'"
            ."    AND metest = 'on' "
            //."    AND metfek = '".$wfecha."'"
            ."    AND mettdo = medtdo "
            ."    AND metdoc = meddoc "
            ."    AND Medno1 != ''";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0){
            $wmed="";
            for ($i=1; $i <= $wnum;$i++){
                $row = mysql_fetch_array($res);
                if ($i < $wnum)
                { $wmed = $wmed.$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."<br>"; }
                else
                { $wmed = $wmed.$row[0]." ".$row[1]." ".$row[2]." ".$row[3]; }
            }
            return $wmed;
        }
        else
        { return "Sin Medico"; }
    }

    function datosAuditores( $rolAuditor ){
        global $wemp_pmla, $conex, $wbaseHce;
        $rolAux = explode(",",$rolAuditor);
        $arrAux = array();
        foreach ( $rolAux as $i => $value ) {
            array_push( $arrAux, "'".$rolAux[$i]."'" );
        }
        $query = " SELECT descripcion, codigo, usurol, roldes
                     FROM {$wbaseHce}_000020, {$wbaseHce}_000019, usuarios
                    WHERE usurol IN ({$rolAuditor})
                      AND usuest = 'on'
                      AND rolcod = usurol
                      AND codigo = usucod
                      AND empresa = '{$wemp_pmla}'
                      AND Activo = 'A'";
        $rs    = mysql_query( $query, $conex );
        while( $row  = mysql_fetch_array( $rs ) ){
            $datos['arreglo'][$row['codigo']]['nombre']      = $row['descripcion'];
            $datos['arreglo'][$row['codigo']]['rol']         = $row['usurol'];
            $datos['arreglo'][$row['codigo']]['descripcion'] = $row['roldes'];
            ( empty($datos['string'] ) ) ? $datos['string']  = $row['codigo']."-".$row['descripcion'] : $datos['string'] .= ",".$row['codigo']."-".$row['descripcion'];
        }
        return( $datos );
    }

    function empresasPorAuditor( $auditor ){
        global $wemp_pmla, $conex, $wbaseHce, $wbasedato;

        ( trim($auditor) ==  "" ) ? $condicion = " WHERE Eauest = 'on' " : $condicion = " WHERE Eaucau = '{$auditor}' And Eauest = 'on' ";
        $query = " SELECT Eaucau, Eauemp
                     FROM {$wbasedato}_000167
                     {$condicion}";
        $rs    = mysql_query( $query, $conex );

        while( $row = mysql_fetch_array( $rs ) ){
            $resultado['consolidado'][$row['Eaucau']][$row['Eauemp']] = "";
            if( empty( $resultado['asociadas'][$row['Eaucau']] ) )
                $resultado['asociadas'][$row['Eaucau']] = array();
            array_push( $resultado['asociadas'][$row['Eaucau']], $row['Eauemp'] );
        }
        return( $resultado );
    }

    function empresasResponsables( $empresas='' ){

        global $wcliame, $conex;
        $empresasArray = array();
        if( trim( $empresas ) == "" ){
            $query = " SELECT Empcod codigo, Empnom nombre
                         FROM {$wcliame}_000024
                        WHERE Empest = 'on'";
            $rs    = mysql_query( $query, $conex ) or die( mysql_error() );
            while( $row = mysql_fetch_array( $rs ) ){
                $empresasArray[$row['codigo']] = $row['nombre'];
            }
        }
        return( $empresasArray );
    }

    function diasEstanciaUrgencias( $fechaInicial, $fechaFinal ){

        if( $fechaFinal == "0000-00-00" )
            $fechaFinal = date( "Y-m-d" );
        $fechaInicial  = strtotime( $fechaInicial );
        $fechaFinal    = strtotime( $fechaFinal );

        $dias          = ( $fechaFinal - $fechaInicial )/(3600*24);
        $dias          = ceil( $dias );
        if($dias == 0)
            $dias = 1;
        return ($dias);

    }
    //--> FIN DE FUNCIONES.

    if( $peticionAjax == "consultarAuditorias" ){

        //( $wtipoServicio == "%" ) ? $tiposConsultados = "ccohos, ccourg, ccocir" : $tiposConsultados = $wtipoServicio;
        $tiposConsultados      = "ccohos, ccourg, ccocir";
        $resultados            = array();
        $pacientesEnClinica    = array();
        $total                 = array();
        $arregloAuditorias     = array();
        $tiposPertinenciaXhis  = array();
        $arregloResponsables   = empresasResponsables();
        $condicionResponsables = "";
        $wccos                 = consultaCentrosCostosPropia( $tiposConsultados, $wservicio, $wtipoServicio);
        $empresasXauditor      = empresasPorAuditor( $wauditor );
        $totalHisAuditadas     = 0;
        $tiposPertinencia      = array( 'Audest'=> array( 'nombre'=> 'Estancia',            'cantidadTotal' => 0),
                                        'Audmed'=> array( 'nombre'=> 'Medicamentos',        'cantidadTotal' => 0),
                                        'Audayd'=> array( 'nombre'=> 'Ayudas diagnosticas', 'cantidadTotal' => 0),
                                        'Audpob'=> array( 'nombre'=> 'Posibles Objeciones', 'cantidadTotal' => 0),
                                        'Audvob'=> array( 'nombre'=> 'Valor objeciones',    'cantidadTotal' => 0),
                                        'Audead'=> array( 'nombre'=> 'Eventos Adversos',    'cantidadTotal' => 0),
                                        'Audrei'=> array( 'nombre'=> 'Reingreso',           'cantidadTotal' => 0),
                                        'Audalt'=> array( 'nombre'=> 'Alta Temprana',       'cantidadTotal' => 0),
                                        'Audobs'=> array( 'nombre'=> 'Observaciones',       'cantidadTotal' => 0),
                                 );
        $wauditores2           = explode( ",", $wauditores );
        //--> Construcción de arreglo de auditores con la estructura $arrayAuditores['codigo del auditor'] = "nombre del auditor ";
        foreach ($wauditores2 as $key => $auditor) {

            $datosAuditor = explode( "-", $auditor );
            $arrayAuditores[$datosAuditor[0]] = $datosAuditor[1];
        }
        $arrayAuditores['sinAuditor'] = "Auditorias de pacientes cuyas entidades responsables no tienen Auditor Encargado";

        if( $wauditor != "" ){//** si se está seleccionando un solo auditor para reportar
            unset( $arrayAuditores['sinAuditor'] );
        }
        //<--

        ( $wfec_f      == date('Y-m-d')  ) ? $agregarTabla18         = true : $agregarTabla18 = false ; //--> si la consulta
        ( $wservicio   == "%"            ) ? $centroCostosConsultado = "Todos" : $centroCostosConsultado = $wccos[$wservicio]['Nombre'];
        ( $wentidad[0] == "%"            ) ? $entidadConsultada      = "Todos" : $entidadConsultada      = $wentidad[1];
        ( $wentidad[0] != "%"            ) ? $condicionResponsables  = " AND ingres = '{$wentidad[0]}' " : $condicionResponsables = "";

        if( $wtipoServicio != "%" ){

            if( $wservicio == "%"){
                if( count( $wccos ) > 1 ){
                    $condicionCco = " AND Ubisac IN ( ";
                    $i = 0;
                    foreach ( $wccos as $keyCco => $wcco ) {
                        $i++;
                        ( $i == 1 ) ? $condicionCco .= " '{$keyCco}' " : $condicionCco .= ", '{$keyCco}' " ;
                    }
                    $condicionCco .= " ) ";
                }else{
                    $condicionCco = " AND Ubisac = ";
                    $i = 0;
                    foreach ( $wccos as $keyCco => $wcco ) {
                        $i++;
                        ( $i == 1 ) ? $condicionCco .= " '{$keyCco}' " : $condicionCco .= ", '{$keyCco}' " ;
                    }
                }
            }else{

                $condicionCco = " AND Ubisac = '{$wservicio}' ";

            }
        }else{
            ( $wservicio   != "%" ) ? $condicionCco = " AND Ubisac = '{$wservicio}' " : $condicionCco = "";
        }

        $condicionCco67 = str_replace("Ubisac", "Habcco", $condicionCco );
        if( $agregarTabla18 ){
            $wfecFinal67 = strtotime($wfec_f) - 3600; //hoy a las 00:00 - una hora =  ayer;
            $wfecFinal67 = date( 'Y-m-d', $wfecFinal67 );
        }else{
            $wfecFinal67 = $wfec_f;
        }

        //** si la consulta consiste en centros de costos hospitalarios.
        //--> se consultan todos los pacientes que amanecieron en las camas durante del periodo de tiempo consultado
        $query = " SELECT Habcco centroCostos, Habhis historia, Habing ingreso, c.ingres responsable, Concat( Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2 ) nombre, a.fecha_data FechaEstadia
                     FROM {$wbasedato}_000067 a
                    INNER JOIN
                          {$wbasedato}_000016 c on ( inghis = Habhis AND inging = Habing {$condicionResponsables})
                    INNER JOIN
                          root_000037 on (Habhis = orihis AND oriori = '{$wemp_pmla}')
                    INNER JOIN
                           root_000036 on ( Pactid = Oritid AND Pacced = Oriced )
                    WHERE a.fecha_data between '{$wfec_i}' and '{$wfec_f}'
                      AND habest = 'on'
                      AND habhis != ''
                      AND habing != ''
                      {$condicionCco67}";

        //** si la consulta es de centros de costos de urgencias o cirugia.
        if( $wtipoServicio == "ccourg" or $wtipoServicio == "ccocir" ){
            $query = " SELECT Ubisac centroCostos, Ubihis historia, Ubiing ingreso, c.ingres responsable, Concat( Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2 ) nombre, a.fecha_data FechaIngreso, a.Ubifad FechaEgreso, a.fecha_data FechaEstadia
                         FROM {$wbasedato}_000018 a
                        INNER JOIN
                              {$wbasedato}_000016 c on ( inghis = Ubihis AND inging = Ubiing {$condicionResponsables})
                        INNER JOIN
                              root_000037 on (Ubihis = orihis AND oriori = '{$wemp_pmla}')
                        INNER JOIN
                               root_000036 on ( Pactid = Oritid AND Pacced = Oriced )
                        WHERE  a.fecha_data between  '{$wfec_i}' and '{$wfec_f}'
                          {$condicionCco}";
        }
        // WHERE  a.Ubifad <= '{$wfec_f}'

        $rs    = mysql_query( $query, $conex ) or die( mysql_error().$query );
        $num   = mysql_num_rows( $rs );

        if( $num == 0 ){//* no hay datos en la tabla de 67
             echo "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] No existen registros en la tabla de habitaciones.
                  </div>";
             return;
        }
        //-->consulto los pacientes que estuvieron en la clínica a la media noche durante el tiemp que estuvo en l arena
        while( $row = mysql_fetch_array( $rs ) ){

            $auditorResponsable = "sinAuditor";

            if( !isset( $empresasXauditor['asociadas'] ) )
                $empresasXauditor['asociadas'] = array();
            foreach( $empresasXauditor['asociadas'] as $keyAuditor => $empresas ){
                if( in_array( $row['responsable'], $empresas ) )
                    $auditorResponsable = $keyAuditor;
            }
            $historias ++;
            $diasEstancia = 1;

            if( $wtipoServicio == "ccourg" or $wtipoServicio == "ccocir" ){ //--> si están consultando
                //$diasEstancia  = diasEstanciaUrgencias( $row['FechaIngreso'], $row['FechaEgreso'] );
                $diasEstancia  = 1;
            }

            if( empty( $wccos[$row['centroCostos']]['diasEstancia'] ) ){
                $wccos[$row['centroCostos']]['diasEstancia'] = $diasEstancia;
            }else{
                if( $wtipoServicio != "ccourg" or $wtipoServicio != "ccocir" )
                    $wccos[$row['centroCostos']]['diasEstancia']++;
            }
            if( !isset( $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']] ) ){
                $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['diasEstancia'] = $diasEstancia;
                $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['fechas'] = array();
            }else{
                if( $wtipoServicio != "ccourg" or $wtipoServicio != "ccocir" )
                    $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['diasEstancia'] ++;
            }
            array_push( $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['fechas'], $row['FechaEstadia']);

            $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['nombre']           = $row['nombre'];
            $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['responsable']      = $row['responsable'];
            $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['diasAuditados']    = 0;
            $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['pacienteAuditado'] = false;
            $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['historia']         = $row['historia'];
            $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['ingreso']          = $row['ingreso'];
            $pacientesEnClinica[$auditorResponsable][$row['centroCostos']][$row['historia']."-".$row['ingreso']]['responsable']      = utf8_encode( $arregloResponsables[$row['responsable']] );
        }
        //-->consulto todas las auditorias realizadas en el periodo de tiempo consultado
        $query2 = " SELECT *
                      FROM {$wbasedato}_000136
                     WHERE Fecha_data between '{$wfec_i}' and '{$wfec_f}'";
        $rs2    = mysql_query( $query2, $conex );

        while( $row2 = mysql_fetch_array( $rs2 ) ){

            if( !isset( $arregloAuditorias[$row2['Audhis']."-".$row2['Auding']] ) ){
                $arregloAuditorias[$row2['Audhis']."-".$row2['Auding']]['fechas'] = array();
            }
            array_push( $arregloAuditorias[$row2['Audhis']."-".$row2['Auding']]['fechas'], $row2['Fecha_data'] );
            foreach ( $tiposPertinencia as $keyPertinencia => $tipoPertinencia2 ) {
                if( trim( $row2[$keyPertinencia] ) != "" ){

                    if( $keyPertinencia == "Audvob" ){
                        $tiposPertinenciaXhis[$row2['Audhis']."-".$row2['Auding']][$row2['Fecha_data']][$keyPertinencia]['cantidad']  = $tiposPertinenciaXhis[$row2['Audhis']."-".$row2['Auding']][$row2['Fecha_data']][$keyPertinencia]['cantidad'] + $row2[$keyPertinencia];
                        $tiposPertinenciaXhis[$row2['Audhis']."-".$row2['Auding']][$row2['Fecha_data']][$keyPertinencia]['contado']   = false;
                    }else{
                        $tiposPertinenciaXhis[$row2['Audhis']."-".$row2['Auding']][$row2['Fecha_data']][$keyPertinencia]['cantidad'] = $tiposPertinenciaXhis[$row2['Audhis']."-".$row2['Auding']][$row2['Fecha_data']][$keyPertinencia]['cantidad'] + 1;
                        $tiposPertinenciaXhis[$row2['Audhis']."-".$row2['Auding']][$row2['Fecha_data']][$keyPertinencia]['contado']  = false;
                    }
                }
            }
        }
        $totalAuditoriasConjuntas = 0;
        $divResponsables          = "";
        foreach ( $arrayAuditores as $keyAuditor => $value ) {

            $totalPacientesAuditor        = 0;
            $totalDiasEstancia            = 0;
            $totalPacienAuditados         = 0;
            $totalDiasAuditados           = 0;
            $porCentajeTotalAuditados     = 0;
            $porCentajeTotalDiasAuditados = 0;
            if( count( $pacientesEnClinica[$keyAuditor] ) > 0 ){

                //---> DIV CON LA LISTA DE ENTIDADES QUE TIENE ASOCIADOS UN AUDITOR <---
                /*echo "<pre>";
                    print_r( $empresasXauditor );
                echo "</pre>";*/

                $evento = "";
                if( $keyAuditor != "sinAuditor" ){
                    $evento = "<span class='subtituloPagina2' style='cursor:pointer;' onclick='mostrarEmpresas( \"$keyAuditor\" );'><font size='2' >Empresas Asociadas </font></span>";
                    $divResponsables  .= "<div id='empresas_$keyAuditor' align='center' style='display:none;'>";
                    $divResponsables  .= "<table>";
                        $divResponsables .= "<tr class='noborrar'>";
                        $divResponsables .= "<tr class='fila2 noborrar'>";
                            $divResponsables .= "<td align='center'>Codigo</td>";
                            $divResponsables .= "<td align='center'>Entidad</td>";
                        $divResponsables .= "</tr>";
                        foreach ($empresasXauditor['consolidado'][$keyAuditor] as $codigoEmpresa => $datosEmpresa ){
                            $divResponsables .= "<tr class='fila1'>";
                            $divResponsables .= "<td>".$codigoEmpresa."</td>";
                            $divResponsables .= "<td>".utf8_encode( $arregloResponsables[$codigoEmpresa] )."</td>";
                            $divResponsables .= "</tr>";
                        }
                    $divResponsables .= "</table>";
                    $divResponsables .= "</div>";
                }
                $respuesta .= "<div align='center' class='desplegables'>";
                $respuesta .= "<h3 id='encabezado_$keyAuditor'><b>* ".utf8_encode( $value )." *</b>$evento</h3>";
                //$respuesta .= $evento;

                $respuesta .=  "<table>";
                $respuesta .=  "<tr class='encabezadotabla'><td width='300px;'> Centro Costos </td><td> Num. pacientes </td><td> Dias Estancia </td><td> Num. Pacientes Auditados </td><td> Num. dias Auditados </td><td> % Pacientes Auditados </td><td> % Dias Auditados </td></tr>";

                $i = 0;
                foreach ($wccos as $keyCco => $wcco ) {

                    $pacientesEnCco        = count( $pacientesEnClinica[$keyAuditor][$keyCco] );
                    $totalPacientesAuditor += $pacientesEnCco;
                    $diasEstancia          = 0;
                    $pacientesAuditados    = 0;
                    $diasAuditadosCco         = 0;

                    if( $pacientesEnCco > 0 ){

                        foreach ( $pacientesEnClinica[$keyAuditor][$keyCco] as $historiaIngreso => &$datosHistoriav ){

                            $diasEstancia      += $pacientesEnClinica[$keyAuditor][$keyCco][$historiaIngreso]['diasEstancia'];
                            $totalDiasEstancia += $pacientesEnClinica[$keyAuditor][$keyCco][$historiaIngreso]['diasEstancia'];

                            if( array_key_exists( $historiaIngreso, $arregloAuditorias ) ){
                                $pacientesAuditados  ++;
                                $totalPacienAuditados++;
                                $pacientesEnClinica[$keyAuditor][$keyCco][$historiaIngreso]['pacienteAuditado'] = true;
                                foreach ( $pacientesEnClinica[$keyAuditor][$keyCco][$historiaIngreso]['fechas'] as $indice => $fecha ) {
                                    if( in_array( $fecha, $arregloAuditorias[$historiaIngreso]['fechas'] ) ){

                                        $diasAuditadosCco ++;
                                        $datosHistoriav['diasAuditados'] ++;
                                        $totalDiasAuditados++;

                                        foreach ( $tiposPertinencia as $keyCampo => &$tipoPertinencia ){
                                            if( isset($tiposPertinenciaXhis[$historiaIngreso][$fecha][$keyCampo]) and ($tiposPertinenciaXhis[$historiaIngreso][$fecha][$keyCampo]['contado'] != true) ){
                                                $tipoPertinencia['cantidadTotal'] += $tiposPertinenciaXhis[$historiaIngreso][$fecha][$keyCampo]['cantidad']*1;
                                                $tiposPertinenciaXhis[$historiaIngreso][$fecha][$keyCampo]['contado']  = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //echo " edb --> $keyCco: ".$pacientesEnCco."  --- $pacientesAuditados  --- dias auditados: $diasAuditadosCco<br>";
                        ( is_int($i/2) ) ? $wclass  = " class='fila1' " : $wclass  = " class='fila2' ";
                        ( is_int($i/2) ) ? $wclass2 = " class='fila2' " : $wclass2 = " class='fila1' ";
                        $i++;
                        //**detalle de pacientes que estuvieron acostados en las distintas fechas en los centros de costos consultados
                            $respuestaDet  = "<div style='width:100%;padding-top:20px;padding-bottom:39px;'>";
                            $respuestaDet .= "<table name='historiasEnCco' style='font-size:10px; border: 1px solid #000;'>";
                                $respuestaDet .= "<tr class='encabezadotabla'><td>Historia-ingreso</td><td>Nombre</td><td>Responsable</td><td>Dias Estancia</td><td>Auditado</td><td>Dias Auditados</td><td>&nbsp;</td></tr>";
                                foreach ($pacientesEnClinica[$keyAuditor][$keyCco] as $historiaIngreso => $datosHistoria ) {

                                    if( $datosHistoria['pacienteAuditado'] and $datosHistoria['diasAuditados'] == 0 ){
                                        $pacientesAuditados--;
                                        $totalPacienAuditados--;
                                    }

                                    if( $datosHistoria['pacienteAuditado'] ){
                                        //echo "<br> edb--> $historiaIngreso -- {$tiposPertinenciaXhis[$historiaIngreso]['Audobs']['cantidad']}<br>";
                                        $observaciones += $tiposPertinenciaXhis[$historiaIngreso]['Audobs']['cantidad'];
                                    }

                                    $respuestaDet .= "<tr class='$wclass2'>";
                                        $respuestaDet .= "<td align='left'>{$historiaIngreso}</td>";
                                        $respuestaDet .= "<td align='left'>".utf8_encode( $datosHistoria['nombre'] )."</td>";
                                        $respuestaDet .= "<td align='left'>".$datosHistoria['responsable']."</td>";
                                        $respuestaDet .= "<td align='center'>{$datosHistoria['diasEstancia']}</td>";
                                        ( $datosHistoria['pacienteAuditado'] and $datosHistoria['diasAuditados'] > 0 ) ? $pacienteAuditado =  "<img width:'15px' height='15px' src='/matrix/images/medical/movhos/checkmrk.ico'>" : $pacienteAuditado = "";
                                        $respuestaDet .= "<td align='center'>$pacienteAuditado</td>";
                                        $respuestaDet .= "<td align='center'>{$datosHistoria['diasAuditados']}</td>";
                                        if( $datosHistoria['pacienteAuditado'] and $datosHistoria['diasAuditados'] > 0 )
                                            $respuestaDet .= "<td nowrap='nowrap' style='cursor:pointer;' onclick='abrirAuditorias( \"{$datosHistoria['historia']}\", \"{$datosHistoria['ingreso']}\",  \"$wemp_pmla\", $keyCco, \"\", \"{$datosHistoria['nombre']}\", \"{$datosHistoria['documento_pac']}\",  \"{$datosHistoria['habitacion']}\", \"{$datosHistoria['f_nacimiento']}\", \"{$datosHistoria['medico']}\" )'><font color='blue'>ver</font></td>";
                                        else
                                            $respuestaDet .= "<td>&nbsp;</td>";
                                    $respuestaDet .= "</tr>";
                                }
                            $respuestaDet .= "</table>";
                            $respuestaDet .= "</div>";

                        $respuestaDet .= "</td></tr>";

                        $porcenPacientesAuds = ( $pacientesAuditados/$pacientesEnCco ) * 100;
                        $porcenDiasAuds      = ( $diasAuditadosCco/$diasEstancia ) * 100;

                        $respuesta .= "<tr $wclass >";
                               $respuesta .= "<td align='left'>{$keyCco} - {$wccos[$keyCco]['Nombre']}</td>";
                               $respuesta .= "<td align='center' style='cursor:pointer;' onclick='verOcultarDetalle( this )'>".$pacientesEnCco."</td>"; //-> pacientes en el centro de costos durante el periodo
                               $respuesta .= "<td align='center' style='cursor:pointer;' onclick='verOcultarDetalle( this )'>".$diasEstancia."</td>";
                               $respuesta .= "<td align='center' style='cursor:pointer;' onclick='verOcultarDetalle( this )'>".$pacientesAuditados."</td>";
                               $respuesta .= "<td align='center' style='cursor:pointer;' onclick='verOcultarDetalle( this )'>".$diasAuditadosCco."</td>";
                               $respuesta .= "<td align='center'>".number_format( $porcenPacientesAuds, 2, '.', '.' )."</td>";
                               $respuesta .= "<td align='center'>".number_format( $porcenDiasAuds, 2, '.', '.' )."</td>";
                        $respuesta .= "</tr>";
                        //--> detalles de los datos presentados
                        $respuesta  .= "<tr $wclass2 align='center' style='display:none;'><td colspan='7'>";
                        $respuesta  .= $respuestaDet;
                    }
                }

                $porCentajeTotalAuditados     = ($totalPacienAuditados/$totalPacientesAuditor)*100;
                $porCentajeTotalDiasAuditados = ($totalDiasAuditados/$totalDiasEstancia)*100;
                $totalAuditoriasConjuntas     += $totalDiasAuditados;
                $respuesta .=  "<tr class='encabezadotabla'><td width='300px;' align='left'> Total: </td><td align='center'>".$totalPacientesAuditor."</td><td align='center'>".$totalDiasEstancia."</td><td align='center'>$totalPacienAuditados</td><td align='center'>". $totalDiasAuditados."</td><td align='center'>".number_format($porCentajeTotalAuditados, 2, '.', '.')."</td><td align='center'>".number_format($porCentajeTotalDiasAuditados, 2, '.', '.')."</td></tr>";
                $respuesta .= "</table>";
                $respuesta .= "</div><br>";
            }

        }
        $existeResumen = false;
        $respuesta2 = "";
        $respuesta2  .=  "<table>";
        $respuesta2  .=  "<tr class='encabezadotabla'><td width='300px;'> Tipo Pertinencia </td><td> N&uacute;mero Auditorias </td></tr>";
        $i = 0;
        foreach ($tiposPertinencia as $keyCampo => &$tipoPertinencia ){
            if( $tipoPertinencia['cantidadTotal'] > 0 ){
                $i++;
                $existeResumen = true;
                ( is_int($i/2) ) ? $wclass= " class='fila1' " : $wclass= " class='fila2' ";
                ( $keyCampo == "Audvob") ? $texto = " $ ".number_format( $tipoPertinencia['cantidadTotal'], 0, '.', '.' ) : $texto = $tipoPertinencia['cantidadTotal'];
                $respuesta2 .= "<tr $wclass ><td align='left'>{$tipoPertinencia['nombre']}</td><td align='center' >".$texto."</td></tr>";
            }
        }
        $respuesta2 .=  "<tr class='encabezadotabla'><td>TOTAL AUDITORIAS</td><td align='center'>{$totalAuditoriasConjuntas}</td></tr>";
        $respuesta2 .=  "</table>";

        echo $respuesta;
        if( $existeResumen )
                echo $respuesta2;
        echo $divResponsables;
        return;
    }

    if( $peticionAjax == "consultarEntidadesAuditor" ){
        $wentid = consultarEntidades( $wauditor );
        $ent    = json_encode( $wentid );
        $data   = array( 'entidadesEncontradas'=>$ent );
        echo json_encode( $data );
        return;
    }
?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title> Reporte Auditorías Concurrentes </title>
    <style>
    </style>
    <style>
        /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
        .ui-datepicker {font-size:12px;}
        /* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
        .ui-datepicker-cover {
            display: none; /*sorry for IE5*/
            display/**/: block; /*sorry for IE5*/
            position: absolute; /*must have*/
            z-index: -1; /*must have*/
            filter: mask(); /*must have*/
            top: -4px; /*must have*/
            left: -4px; /*must have*/
            width: 200px; /*must have*/
            height: 200px; /*must have*/
        }

        .datosConsulta{
            border-top-right-radius    : 10px;
            border-top-left-radius     : 10px;
            border-bottom-right-radius : 10px;
            border-bottom-left-radius  : 10px;
        }
    </style>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <script>
        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
            'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);
    </script>
    <script>

        //Funcion jquery para quitar o poner el color de fondo de los elementos disabled o readonly en internet explorer
        jQuery.fn.cssie = function() {
            $(this).each(function() {
                $(this)
                    if ( $.browser.msie ) {
                        if( $(this).css('background-color') == '#ffffff'){
                            if(! $(this).attr("readonly") ){ $(this).css('background-color','#DAFFE6')};
                        }else{
                            if( $(this).attr("readonly") ){ $(this).css('background-color','#ffffff')};
                        }
                        if( $(this).is(':text') ){
                            if( $(this).css('background-color') == '#ffffff'){
                                if(! $(this).attr("disabled") )  $(this).css('background-color','#DAFFE6');
                            }else{
                                if( $(this).attr("disabled") ) $(this).css('background-color','#ffffff');
                            }
                        }
                    }
                });
            return $(this);
        }

        $(document).ready(function(){

            var entidades_array = new Array();
            //Selecciona la variable con las entidades y llena el arreglo para mostrarse en el input de "Entidad"
            var entidadesx = $("#entidades_json").val();
            //console.log(entidadesx + "  inicial ");
            var datos = eval ( entidadesx );
            for( i in datos ){
                entidades_array.push( datos[i] );
            }
            //Autocompletar para las entidades responsables, cuando seleccione uno llama a buscarcodigoentidad
            $( "#responsable" ).autocomplete({
                source: entidades_array,
                minLength : 2
            });

            $("#boton_consultar").click(function(){
                realizarConsulta();
            });

            $("#wfec_i, #wfec_f").datepicker({
              showOn: "button",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
              maxDate:"+1D"
            });
            setTimeout(function(){
                $("input[type='radio'][value='ccohos']").click();
            },500);
        });

        function realizarConsulta(){
            var wservicio   = $("#servicio").val();
            var wentidad    = $("#responsable").val();
            var wfecInicial = $("#wfec_i").val();
            var wfecFinal   = $("#wfec_f").val();
            var wauditores  = $("#datos_auditores").val();

            if( $.trim(wservicio) == "" || $.trim(wentidad) == "" ){
                alerta( "Por favor, seleccione un valor válido para todos los parámetros" );
                return;
            }

            aux      = wentidad.split(",");
            wentidad = $.trim(aux[0])+"|"+$.trim(aux[2]) ;

            $("#msjEspere").toggle();
            //$("#divMenuPpal").toggle();
            $.ajax({
                url: "rep_auditoriasMedicas.php",
               type: "POST",
              async: false,
               data: {
                        peticionAjax: "consultarAuditorias",
                        consultaAjax: "si",
                           wemp_pmla: $("#wemp_pmla").val(),
                           wbasedato: $("#wbasedato").val(),
                             wcliame: $("#wcliame").val(),
                           wservicio: wservicio,
                            wentidad: wentidad,
                           wempresas: $("#entidades_json").val(),
                              wfec_i: wfecInicial,
                              wfec_f: wfecFinal,
                       wtipoServicio: $("input[type='radio'][name='radio_tipoServicio']:checked").val(),
                            wauditor: $("#wauditor").val(),
                          wauditores: $("#datos_auditores").val()

                      },
                success: function(data)
                {
                    if( data != "" ){
                        $("#msjEspere").toggle();
                        $("#resultados_lista").html( data );
                        $("#resultados_lista").show();
                        $("#enlace_retornar").show();
                        $( ".desplegables" ).accordion({
                                collapsible: true,
                                active:0,
                                heightStyle: "content",
                                icons: null
                        });
                    }else{
                         $("#msjEspere").toggle();
                         var data2  = "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>[?] No existen registros en asociados al Auditor.</div>";
                         $("#resultados_lista").html( data2 );
                         $("#resultados_lista").show();
                         $("#enlace_retornar").show();
                    }

                }
            });
        }

        function alerta( txt ){
            $("#textoAlerta").text( txt );
            $.blockUI({ message: $('#msjAlerta') });
                setTimeout( function(){
                    $.unblockUI();
                }, 1600 );
        }

        function retornar(){

            $("#resultados_lista").hide();
            $("#enlace_retornar").hide();
            $("#resultados_lista").html("");
            //$("#divMenuPpal").show();
        }

        function verOcultarDetalle( obj ){
            detalle =  $(obj).parent().next("tr");
            $(detalle).toggle();
        }

        function consultarAuditorias(historia, ingreso, fecha){
            var rango_superior = 245;
            var rango_inferior = 11;
            var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
            var wemp_pmla = $("#wemp_pmla").val();
            $.blockUI({ message: $('#msjEspere') });
            $.get('../procesos/auditoria_medica.php', { wemp_pmla: wemp_pmla, action: "consultandoAuditoria", historia: historia, ingreso: ingreso, fecha: fecha, consultaAjax: aleatorio} ,
                function(data) {
                    $.unblockUI();
                    llenarFormulario( data, fecha );
                }, 'json');
        }

        //Luego de traer los datos de la auditoria esta funcion se encarga de mostralos en el formulario
        function llenarFormulario( auditorias, fecha ){
            restablecer_formulario();
            var auditoria = auditorias['auditorias_todas'];
            var auditoria_editable = auditorias['auditoria_editable'];

            if ( auditoria_editable != undefined ){
                auditoria_editable_global = auditoria_editable;
                editando_global = true;
            }

            $("#formulario_auditoria textarea").removeAttr("disabled");
            $("#valor_objeciones").removeAttr("disabled");
            //PERTINENCIA ESTANCIA
            if( auditoria.estancia == ""){
                $('input[name=au_estancia][value=Si]').prop('checked', true);
                $("#val_estancia").attr("disabled", "disabled");
            }else{
                $('input[name=au_estancia][value=No]').prop('checked', true);
                $("#val_estancia").val( auditoria.estancia );
            }
            //PERTINENCIA MEDICAMENTOS
            if( auditoria.medicamentos == ""){
                $('input[name=au_medicamentos][value=Si]').prop('checked', true);
                $("#val_medicamentos").attr("disabled", "disabled");
            }else{
                $('input[name=au_medicamentos][value=No]').prop('checked', true);
                $("#val_medicamentos").val( auditoria.medicamentos );
            }
            //PERTINENCIA AYUDAS DIAGNOSTICAS
            if( auditoria.ayudas_diagnosticas == ""){
                $('input[name=au_ay_diag][value=Si]').prop('checked', true);
                $("#val_ayu_diag").attr("disabled", "disabled");
            }else{
                $('input[name=au_ay_diag][value=No]').prop('checked', true);
                $("#val_ayu_diag").val( auditoria.ayudas_diagnosticas );
            }
            //POSIBLES OBJECIONES
            if( auditoria.valor_objeciones == ""){
                $('input[name=au_pos_obj][value=No]').prop('checked', true);
                $("#val_pos_obj").attr("disabled", "disabled");
                $("#valor_objeciones").attr("disabled", "disabled");
            }else{
                $('input[name=au_pos_obj][value=Si]').prop('checked', true);
                $("#val_pos_obj").val( auditoria.posibles_objeciones );
                $("#valor_objeciones").val(auditoria.valor_objeciones);
            }
            //EVENTOS ADVERSOS
            if( auditoria.eventos_adversos == ""){
                $('input[name=au_even_adv][value=No]').prop('checked', true);
                $("#val_even_adv").attr("disabled", "disabled");
            }else{
                $('input[name=au_even_adv][value=Si]').prop('checked', true);
                $("#val_even_adv").val( auditoria.eventos_adversos );
            }
            //REINGRESO
            if( auditoria.reingreso == ""){
                $('input[name=au_reingreso][value=No]').prop('checked', true);
                $("#val_reingreso").attr("disabled", "disabled");
            }else{
                $('input[name=au_reingreso][value=Si]').prop('checked', true);
                $("#val_reingreso").val( auditoria.reingreso );
            }
            //ALTA TEMPRANA
            if( auditoria.alta_temprana == ""){
                $('input[name=au_alt_temp][value=Si]').prop('checked', true);
                $("#val_alt_temp").attr("disabled", "disabled");
            }else{
                $('input[name=au_alt_temp][value=No]').prop('checked', true);
                $("#val_alt_temp").val( auditoria.alta_temprana );
            }
            //OBSERVACIONES
            var observaciones = $("#au_obs").val( auditoria.observaciones );

            $("#titulo_formulario_auditoria").html("<b>Auditoria(s) realizada(s) el dia "+fecha+"</b>");

            if( $.browser.msie ) $("#formulario_auditoria textarea").attr("disabled", false);
            $("#formulario_auditoria input").attr("disabled", "disabled");
            (! $.browser.msie ) ? $("#formulario_auditoria textarea").attr("disabled", "disabled") : $("#formulario_auditoria textarea").attr("readonly", true)

            if( $.browser.msie ) $("#formulario_auditoria textarea").css('background-color','#ffffff');
            if( $.browser.msie ) $("#formulario_auditoria input[type='text']").css('background-color','#ffffff');

            if ( auditoria_editable != undefined ){
                $("#formulario_auditoria input[type='radio']").attr('onclick','').unbind('click');
                validaciones_nuevos_textarea();

                $('input[name=au_estancia][value=Si]').prop('checked', true);
                $('input[name=au_medicamentos][value=Si]').prop('checked', true);
                $('input[name=au_ay_diag][value=Si]').prop('checked', true);
                $('input[name=au_pos_obj][value=No]').prop('checked', true);
                $('input[name=au_even_adv][value=No]').prop('checked', true);
                $('input[name=au_reingreso][value=No]').prop('checked', true);
                //ACTIVA LA FUNCION QUE MUESTRA EL OTRO TEXTAREA PARA CADA CAMPO DEL FORMULARIO
                if( $.browser.msie ) $("#formulario_auditoria textarea").attr("disabled", false);
                $("#formulario_auditoria input").attr("disabled", "disabled");
                (! $.browser.msie ) ? $("#formulario_auditoria textarea").attr("disabled", "disabled") : $("#formulario_auditoria textarea").attr("readonly", true);
                //habilita todos los input tipo radio
                $("#formulario_auditoria input[type='radio']").attr("disabled", false);
                $("#formulario_auditoria input[type='checkbox']").attr("disabled", false);
                $("#boton_guardar_auditoria").attr("disabled", false);
                $("#boton_guardar_auditoria").show();
                $("#add_auditoria").hide();
                $('#au_obsx').attr('disabled', false );
                $('#au_obsx').attr('readonly', false );

                if( $.browser.msie ) $("#formulario_auditoria textarea").css('background-color','#ffffff');
                if( $.browser.msie ) $("#valor_objeciones").css('background-color','#ffffff');
                if ( $.browser.msie )  $("#au_obsx").cssie();
            }
        }

        function abrirAuditorias( historia, ingreso, wemp_pmla, servicio, fecha, paciente, documento_pac, habitacion, f_nacimiento, medico  ){

            $.ajax({
                url: "../procesos/auditoria_medica.php",
               type: "GET",
              async: false,
               data: {
                        action       : "mostrarPaciente",
                        consultaAjax : "si",
                        wemp_pmla    : wemp_pmla,
                        historia     : historia,
                        ingreso      : ingreso,
                        servicio     : servicio,
                        fecha        : fecha,
                        paciente     : paciente,
                        doc_paciente : documento_pac,
                        habitacion   : habitacion,
                        nacimiento   : f_nacimiento,
                        medico       : medico

                      },
                success: function(data)
                {
                    if( data ){
                       $("#resultados_paciente").dialog({
                             title: " Detalle Auditorias - Paciente: "+paciente,
                             modal: true,
                             buttons: {
                                Ok: function() {
                                    $( this ).dialog( "close" );
                                }
                             },
                             show: {
                                effect   : "blind",
                                duration : 500
                             },
                             hide: {
                                effect   : "blind",
                                duration : 500
                            },
                            height    : 1300,
                            width     : 1400,
                            rezisable : true
                        });
                        $("#resultados_paciente").html( data );
                        $("#add_auditoria").hide();
                        $("#boton_guardar_auditoria").attr("disabled",false);
                        $("#boton_guardar_auditoria").hide();
                        $(".boton_editar").hide();
                        $("#guardar_en_bitacora").parent().parent().hide();
                        $(".tipo3V").eq(0).click();
                    }

                }
            });
        }

        function restablecer_formulario(){
            validaciones_formulario_auditoria();

            $('input[name=au_estancia][value=Si]').prop('checked', true);
            $('input[name=au_medicamentos][value=Si]').prop('checked', true);
            $('input[name=au_ay_diag][value=Si]').prop('checked', true);
            $('input[name=au_pos_obj][value=No]').prop('checked', true);
            $('input[name=au_even_adv][value=No]').prop('checked', true);
            $('input[name=au_reingreso][value=No]').prop('checked', true);
            $("#val_estancia").val("");
            $("#val_medicamentos").val("");
            $("#val_ayu_diag").val("");
            $("#val_pos_obj").val("");
            $("#valor_objeciones").val("");
            $("#val_even_adv").val("");
            $("#val_reingreso").val("");
            $("#val_alt_temp").val("");
            $("#au_obs").val("");

            $("#titulo_formulario_auditoria").html("");
            $("#boton_guardar_auditoria").show('slow');
            $("#formulario_auditoria input").attr("disabled", false);
            $("#valor_objeciones").attr("disabled", "disabled");

            auditoria_editable_global = "";
            editando_global = false;
            $(".boton_editar").hide();
            $(".campos_editables").remove();
            $("#formulario_auditoria textarea").attr('cols',120);
            $("#guardar_en_bitacora").attr('checked', false );
            if( $.browser.msie ) $("#formulario_auditoria textarea").attr("disabled", false);
            (! $.browser.msie ) ? $("#formulario_auditoria textarea").attr("disabled", "disabled") : $("#formulario_auditoria textarea").attr("readonly", true);
            if( $.browser.msie ) $("#valor_objeciones").css('background-color','#ffffff');
            $("#au_obs").attr("disabled", false);
            $('#au_obs').attr('readonly', false );
        }

        //Funcion que asigna las acciones cuando se de click en los radios
        function validaciones_formulario_auditoria(){
            //Quitar las acciones que tengan asignadas al darle click a todos los input tipo radio
            $("#formulario_auditoria input[type='radio']").attr('onclick','').unbind('click');

            //PERTINENCIA ESTANCIA  //Si: inhabilitar input text  --- No: habilitar input text
            $('input[name=au_estancia]').click(function(){
                if( $(this).val() == "Si" ){
                    (! $.browser.msie ) ? $("#val_estancia").attr("disabled", "disabled") : $("#val_estancia").attr("readonly", true).cssie();
                    $("#val_estancia").val("");
                }else
                    (! $.browser.msie ) ? $("#val_estancia").attr("disabled", false).focus() : $("#val_estancia").attr("readonly", false).cssie().focus()
            });

            //PERTINENCIA MEDICAMENTOS  //Si: inhabilitar input text  --- No: habilitar input text
            $('input[name=au_medicamentos]').click(function(){
                if( $(this).val() == "Si" ){
                    (! $.browser.msie ) ? $("#val_medicamentos").attr("disabled", "disabled") : $("#val_medicamentos").attr("readonly", true).cssie();
                    $("#val_medicamentos").val("");
                }else
                    (! $.browser.msie ) ? $("#val_medicamentos").attr("disabled", false).focus() : $("#val_medicamentos").attr("readonly", false).cssie().focus();
            });

            //PERTINENCIA AYUDAS DIAGNOSTICAS   //Si: inhabilitar input text  --- No: habilitar input text
            $('input[name=au_ay_diag]').click(function(){
                if( $(this).val() == "Si" ){
                    (! $.browser.msie ) ? $("#val_ayu_diag").attr("disabled", "disabled") : $("#val_ayu_diag").attr("readonly", true).cssie();
                    $("#val_ayu_diag").val("");
                }else
                    (! $.browser.msie ) ? $("#val_ayu_diag").attr("disabled", false).focus() : $("#val_ayu_diag").attr("readonly", false).cssie().focus();
            });

            //POSIBLES OBJECIONES   //Si: habilitar los 2 input text  --- No: inhabilitar los 2 input text
            $('input[name=au_pos_obj]').click(function(){
                if( $(this).val() == "No" ){
                    (! $.browser.msie ) ? $("#val_pos_obj").attr("disabled", "disabled") : $("#val_pos_obj").attr("readonly", true).cssie();
                    $("#valor_objeciones").attr("disabled", "disabled").cssie();
                    $("#val_pos_obj").val("");
                    $("#valor_objeciones").val("");
                }else{
                    (! $.browser.msie ) ? $("#val_pos_obj").attr("disabled", false).focus() : $("#val_pos_obj").attr("readonly", false).cssie().focus();
                    $("#valor_objeciones").attr("disabled", false).cssie();
                    $("#valor_objeciones").attr("readonly", false);
                }
            });

            //EVENTOS ADVERSOS  //Si: habilitar input text  --- No: inhabilitar input text
            $('input[name=au_even_adv]').click(function(){
                if( $(this).val() == "No" ){
                    (! $.browser.msie ) ? $("#val_even_adv").attr("disabled", "disabled") :  $("#val_even_adv").attr("readonly", true).cssie();
                    $("#val_even_adv").val("");
                }else
                    (! $.browser.msie ) ? $("#val_even_adv").attr("disabled", false).focus() : $("#val_even_adv").attr("readonly", false).cssie().focus();
            });

            //REINGRESO //Si: habilitar input text  --- No: inhabilitar input text
            $('input[name=au_reingreso]').click(function(){
                if( $(this).val() == "No" ){
                    (! $.browser.msie ) ? $("#val_reingreso").attr("disabled", "disabled") : $("#val_reingreso").attr("readonly", true).cssie();
                    $("#val_reingreso").val("");
                }else
                    (! $.browser.msie ) ? $("#val_reingreso").attr("disabled", false).focus() :  $("#val_reingreso").attr("readonly", false).cssie().focus();
            });

            //ALTA TEMPRANA //Habilitar si le da click a cualquiera
            $('input[name=au_alt_temp]').click(function(){
                (! $.browser.msie ) ? $("#val_alt_temp").attr("disabled", false).focus() : $("#val_alt_temp").attr("readonly", false).cssie().focus();
            });

            //Solo valores numericos
            $("#valor_objeciones").keyup(function(){
                if ($(this).val() !="")
                    $(this).val($(this).val().replace(/[^0-9\.]/g, ""));
            });

            //Si es internet explorer: readonly; Si es otro explorador: disabled
            (! $.browser.msie ) ? $("#formulario_auditoria textarea").attr("disabled", "disabled") : $("#formulario_auditoria textarea").attr("readonly", true);
            (! $.browser.msie ) ? $("#valor_objeciones").attr("disabled", "disabled") : $("#valor_objeciones").attr("readonly", true);
            (! $.browser.msie ) ? $("#au_obs").attr("disabled", false).cssie() :  $("#au_obs").attr("readonly", false).cssie();
        }

        function filtrarCcos( obj ){

            var tipoServicio = $(obj).val();
            if( tipoServicio == "ccohos" ){
                $("#servicio > option["+tipoServicio+"='on']").show();
                $("#servicio > option[ccourg='on']").hide();
                $("#servicio > option[ccocir='on']").hide();
            }else{
                $("#servicio > option["+tipoServicio+"='on']").show();
                $("#servicio > option["+tipoServicio+"!='on']").hide();
            }

        }

        function consultarEntidades(){

            try{
                $("#responsable").autocomplete("destroy");
            }catch(e){}
             $.ajax({
                    url     : "rep_auditoriasMedicas.php",
                    type    : "POST",
                    async   : false,
                    data: {
                        peticionAjax: "consultarEntidadesAuditor",
                        consultaAjax: "si",
                           wemp_pmla: $("#wemp_pmla").val(),
                           wbasedato: $("#wbasedato").val(),
                             wcliame: $("#wcliame").val(),
                            wauditor: $("#wauditor").val()
                           //aleatorio: aleatorio
                    },
                    success: function(data) {
                        $("#entidades_json").val("");
                        $("#responsable").val("%");
                        $("#entidades_json").val( data.entidadesEncontradas );
                        var entidades_array = new Array();
                        var entidadesx      = $("#entidades_json").val();
                        var datos           = eval ( entidadesx );
                        for( i in datos ){
                            entidades_array.push( datos[i] );
                        }
                        $("#responsable").autocomplete({
                            source: entidades_array,
                            minLength : 2
                        });
                    },
                    dataType: "json"
            });
        }

        function mostrarEmpresas(keyAuditor){

            var nombreAuditor = $("#wauditor > option[value='"+keyAuditor+"']").html();
             $("#empresas_"+keyAuditor).dialog({
                 title: " ENTIDADES ASOCIADAS AL AUDITOR: "+nombreAuditor,
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $("#encabezado_"+keyAuditor).click();
                        $( this ).dialog( "close" );
                    }
                 },
                 show: {
                    effect   : "blind",
                    duration : 500
                 },
                 hide: {
                    effect   : "blind",
                    duration : 500
                },
                height    : 400,
                width     : 500,
                rezisable : true
            });
            $(".ui-dialog-titlebar-close").hide();//-->oculto la x de cerrar, por defecto
        }

/*        function consultarEntidades(){
            try{
                $("#responsable").autocomplete("destroy");
            }catch(e){}

             $("#responsable").autocomplete({
                            source: function(request, response){
                                $.ajax({
                                        url     : "rep_auditoriasMedicas.php",
                                        type    : "POST",
                                        async   : false,
                                        data: {
                                            peticionAjax: "consultarEntidadesAuditor",
                                            consultaAjax: "si",
                                               wemp_pmla: $("#wemp_pmla").val(),
                                               wbasedato: $("#wbasedato").val(),
                                                wauditor: $("#wauditor").val()
                                               //aleatorio: aleatorio
                                        },
                                        success: function(data) {
                                            $("#entidades_json").val("");
                                            var entidades_array = new Array();
                                            var entidadesx      = data.entidadesEncontradas;
                                            var datos           = eval ( entidadesx );
                                            for( i in datos ){
                                                entidades_array.push( datos[i] );
                                            }
                                            response(entidades_array);
                                        },
                                        dataType: "json"
                                });
                            },
                            minLength : 2
             });
        }*/
    </script>
</head>
<?php
    include_once("root/comun.php");
    $conex      = obtenerConexionBD("matrix");
    $wuser      = substr($user,$pos+1,strlen($user));
    $wbasedato  = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
    $wbaseHce   = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
    $wcenmez    = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
    $wcliame    = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
    $rolAuditor = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoRolAuditores" );
    $wccos      = consultaCentrosCostosPropia("ccohos, ccourg, ccocir", "%");
    $wauditores = datosAuditores( $rolAuditor );
    $wactualiz  = "2014-10-02";

    encabezado("AUDITORIA MEDICA CONCURRENTE", $wactualiz, "clinica");
?>
<body>
    <input type='hidden' id ='wemp_pmla'        value='<?php echo $wemp_pmla; ?>'/>
    <input type='hidden' id ='wbasedato'        value='<?php echo $wbasedato; ?>'/>
    <input type='hidden' id ='wcliame'          value='<?php echo $wcliame; ?>'/>
    <input type='hidden' id ='wbaseHce'         value='<?php echo $wbaseHce; ?>'/>
    <input type='hidden' id ='datos_auditores'  value='<?php echo $wauditores['string']; ?>'/>
    <input type='hidden' id ='servicio_elegido' value='' />
    <div align='center' id='divMenuPpal'>
        <span class="subtituloPagina2">Par&#225;metros de consulta</span>
        <table align='center'>
            <tr>
                <td colspan='2' class='fila1' width='150px'>Auditor</td>
                <td colspan='2' class='fila2' align='left'>
                    <select id='wauditor' required='required' onchange='consultarEntidades();'> <option value='' selected>Todos</option>
                    <?php
                        foreach( $wauditores['arreglo'] as $codigoAuditor => $datos){
                            if( $codigoAuditor != $wuser ){
                                echo "<option value='{$codigoAuditor}'>{$datos['nombre']}</option>";
                            }
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan='2' class='fila1' width='150px'>Servicio</td>
                <td colspan='2' class='fila2' align='left'>
                    <select id='servicio' required='required'>
                    <?php
                        echo "<option value='%'  selected>Todos</option>";
                        foreach ( $wccos as $keyCco => $wcco ) {
                           echo "<option value='{$wcco['Codigo']}' Ccohos='{$wcco['ccohos']}' Ccourg='{$wcco['ccourg']}' Ccocir='{$wcco['ccocir']}'>{$wcco['Codigo']}-{$wcco['Nombre']}</option>";
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan='2' class='fila1' width='150px'>Entidad</td>
                <td colspan='2' class='fila2'  align='center'>
                    <input type='text' placeholder='Digite el nombre de la entidad (% pata todos )' id='responsable' style='width:100%' required='required' value='%' />
                    <?php
                        //Imprime un json en una variable oculta con las entidades
                        $wentid = consultarEntidades();
                        $ent    = json_encode( $wentid );
                    ?>
                    <input type='hidden' id='entidades_json' value='<?php echo $ent; ?>' />
                </td>
            </tr>
            <tr class='filaoculta'>
                <td colspan='2' class='fila1'>Fecha Inicio</td>
                <td colspan='2' class='fila2'>
                <input type='text' class='input_fechas' id='wfec_i' value='<?php echo date("Y-m")."-01"; ?>' />
                </td>
            </tr>
            <tr class='filaoculta'>
                <td colspan='2' class='fila1'>Fecha Fin</td>
                <td colspan='2' class='fila2'>
                <input type='text' class='input_fechas' id='wfec_f' value='<?php echo date("Y-m-d"); ?>' />
                </td>
            </tr>
            <tr class='filaoculta'>
                <td colspan='2' class='fila1'>Tipo de Servicio</td>
                <td colspan='2' class='fila2'>
                    <input type='radio' name='radio_tipoServicio' value='ccohos' onclick='filtrarCcos(this)' /> Hospitalarios&nbsp;
                    <input type='radio' name='radio_tipoServicio' value='ccourg' onclick='filtrarCcos(this)' /> Urgencias&nbsp;
                    <input type='radio' name='radio_tipoServicio' value='ccocir' onclick='filtrarCcos(this)' /> Cirugia&nbsp;
                <!--    <input type='radio' name='radio_tipoServicio' value='%' checked onclick='filtrarCcos(this)'/> Todos&nbsp; -->
                </td>
            </tr>
        </table>
        <br>
        <input type="button" class="botona" id="boton_consultar" value="Consultar"></input>
        <br><br>
    </div>

    <center>
        <div id='msjEspere' style='display:none;'>
            <br>
            <img src='../../images/medical/ajax-loader5.gif'/>
            <br><br> Por favor espere un momento ... <br><br>
        </div>
        <div id='resultados_lista'></div>
        <div id='resultados_paciente' style='display:none;'></div><br>
        <div id='resultadosPrueba' style='display:none;'></div><br>
        <input type='button' id='enlace_retornar' value='Retornar' onclick='retornar();' style='cursor:pointer; display:none; font-color:blue;' >
        <br><br>
        <input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()'>
        <br><br>
        <div id='msjAlerta' style='display:none;'>
            <br>
            <img src='../../images/medical/root/Advertencia.png'/>
            <br><br><div id='textoAlerta'></div><br><br>
        </div>
    </center>
</body>
</html>
