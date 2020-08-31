<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa     :   Estadisticas
 * Fecha        :   2014-11-05
 * Por          :   Frederick Aguirre Sanchez
 * Descripcion  :   Muestra las estadisticas necesarias relacionadas con el egreso
 * Condiciones  :
/ *********************************************************************************************************
MODIFICACIONES:
2016-03-31 camilo zz: se realizan las modificaciones para que el reporte guarde en el consolidado los datos por tipos de cco ejmplo(hospitalización), de tal manera que un mismo dato no cuente
                      2 veces por cada cco hospitalario en el que aplicó, sino una sola vez para el grupo.
2016-03-08 camilo zz: se corrige la forma en que se pintan los consolidados de las consultas al realizarlas para mas de un mes
2016-02-10 camilo zz: se modifica el software para que reporte en los detalles de cada categoría, aquellas historias que están egresadas con las conidiciones
                      pero que por alguna razón no fueron tenidas encuenta en la sección en cuestión, por ejemplo: consultar historias de hospitalización,
                      pero que no estén contando en los diagnósticos, teniendo en cuenta que todas las historias deben tener un diagnóstico principal, se reportan
                      como historias con comportamiento extraño para que sean verificadas.


 **********************************************************************************************************/
$wactualiz = "2016-03-31";

if(!isset($_SESSION['user'])){
    echo "error";
    return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=UTF-8');

//La siguiente condicion se hace porque si existe el parametro accion quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['accion'] )){
    echo "<html>";
    echo "<head>";
    echo "<title>Reportes Egreso</title>";
    echo '<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />';
    echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
    echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
    echo ' <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
    echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
    echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
    echo '<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" />';
    echo "<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>";
    echo "<script src='../../../include/root/toJson.js' type='text/javascript'></script>";
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



$conex               = obtenerConexionBD("matrix");
$wbasedato           = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$wbasedatomovhos     = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wbasedatocliame     = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliameCenimp");
$wbasedatohce        = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wcenimp             = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");

$wgruposccos          = array( "ccocir"=>"CIRUGIA - GRUPO","ccourg"=>"URGENCIAS","ccohos"=>"HOSPITALIZACION" );
$waddProcedencia      = array( "OTRB"=>"OTROS BARRIOS", "MNPAM"=>"MPIOS AREA METR.","PAIS"=>"OTRO PAIS" );
$wBarriosProcedencia  = array( 'BELEN','POBLADO','LAURELES','ESTADIO','LA AMERICA','GUAYABAL' );
$wccosCirIndividuales = consultarCcosCirIndividuales();

$codigoMunicipioMedellin        = "05001";
$diasHolguraParaGrabarHistorico = 7;
$topeGraficos                   = 8;



//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['accion'] )){
    $accion = $_REQUEST['accion'];
    if( $accion == "consultarestadisticas"){
        mostrarEstadisticas($_REQUEST['wfechai'], $_REQUEST['wfechaf'], @$_REQUEST['wgrupo']);
    }else if( $accion == 'consultarestadisticasfiltros' ){
        mostrarEstadisticas($_REQUEST['wfechai'], $_REQUEST['wfechaf'], @$_REQUEST['wgrupo'], @$_REQUEST['procedencia'], @$_REQUEST['entidad'], @$_REQUEST['especialidad'], @$_REQUEST['procedimientos'] );
    }else if( $accion == "consultarEntidad" ){
        $entidades = consultarEntidades($q);
        echo $entidades;
    }else if( $accion == "consultarEspecialidad" ){
        $entidades = consultarEspecialidades($q);
        echo $entidades;
    }else if( $accion == "consultarProcedimientos" ){
        $entidades = consultarProcedimientos($q);
        echo $entidades;
    }else if( $accion == "consultarFechaLimiteConsulta"){
        echo consultarFechaLimiteConsulta( $fechaInicio );
    }
    return;
}
//FIN*LLAMADOS*AJAX******************************************************************

    function consultarCcosCirIndividuales(){
        global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wbasedatomovhos;
        global $wemp_pmla;
        $ccosCirugia  = array();

        $query = " SELECT ccocod codigo, cconom nombre
                     FROM {$wbasedatomovhos}_000011
                    WHERE ccocir = 'on'
                      AND ccoest = 'on'";

        $rs    = mysql_query( $query, $conex );
        while( $row = mysql_fetch_assoc( $rs ) ){
            $ccosCirugia[$row['codigo']] = $row['nombre'];
        }
        return  $ccosCirugia;
    }

    function consultarProcedencia($wfecha_i='', $wfecha_f=''){
        global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wemp_pmla;
        global $codigoMunicipioMedellin, $waddProcedencia,$wBarriosProcedencia;

        $in = "";
        $i=0;
        foreach($wBarriosProcedencia as $barrio ){
            if( $i > 0 ) $in.=",";
            $in.="'".$barrio."'";
            $i++;
        }

        $sql = "SELECT Barcod as codigo,Bardes as nombre
                  FROM root_000034
                 WHERE barmun = '".$codigoMunicipioMedellin."'
                   AND bardes IN (".$in.")
                 ORDER BY Bardes
                 LIMIT 30
                ";

        $res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        $num = mysql_num_rows( $res );
        if( $num > 0 ){
            $result = array();
            while($row = mysql_fetch_assoc($res)){
                array_push( $result, $row);
            }
            foreach($waddProcedencia as $key=>$val){
                $row = array();
                $row['codigo'] = $key;
                $row['nombre'] = $val;
                array_push($result,$row);
            }
            return $result;
        }
    }

    function consultarEntidades($wentidad){
        global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wemp_pmla;

         $q="select Empcod as codigo, Empnom as nombre
                FROM ".$wbasedato."_000024
                where Empcod LIKE '%".utf8_decode($wentidad)."%' OR Empnom LIKE '%".utf8_decode($wentidad)."%'
                ORDER BY Empnom
                LIMIT 50
                ";

        $res = mysql_query($q, $conex);
        $num = mysql_num_rows($res);
        $val = "";
        if( $num > 0 ){
            while($rows = mysql_fetch_assoc($res)){
                $data[ 'valor' ] = Array( "cod"=> $rows[ 'codigo' ], "des"=> trim(utf8_encode($rows[ 'nombre' ])) );    //Este es el dato a procesar en javascript
                $data[ 'usu' ] = "{$rows[ 'codigo' ]}-".trim(utf8_encode($rows[ 'nombre' ]))."";    //Este es el que ve el usuario
                $dat = Array();
                $dat[] = $data;
                $val .= json_encode( $dat )."\n";
            }
        }
        return $val;
    }

    function consultarEspecialidades($espe){
        global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wbasedatomovhos;
        global $wemp_pmla;

        $q = "SELECT Espcod as codigo, Espnom as nombre
                FROM ".$wbasedatomovhos."_000044
                WHERE (Espcod LIKE '%".utf8_decode($espe)."%' or Espnom like '%".utf8_decode($espe)."%')
                ORDER BY Espnom
                LIMIT 50
                ";

        $res = mysql_query($q, $conex);
        $num = mysql_num_rows($res);
        $val = "";
        if( $num > 0 ){
            while($rows = mysql_fetch_assoc($res)){
                $data[ 'valor' ] = Array( "cod"=> $rows[ 'codigo' ], "des"=> trim(utf8_encode($rows[ 'nombre' ])) );    //Este es el dato a procesar en javascript
                $data[ 'usu' ] = "{$rows[ 'codigo' ]}-".trim(utf8_encode($rows[ 'nombre' ]))."";    //Este es el que ve el usuario
                $dat = Array();
                $dat[] = $data;
                $val .= json_encode( $dat )."\n";
            }
        }
        return $val;
    }

    function consultarProcedimientos($proc){
        global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wbasedatomovhos;
        global $wemp_pmla;

        $q = "SELECT Procod as codigo,Pronom as nombre
                FROM ".$wbasedato."_000103
                WHERE (Pronom LIKE '%".utf8_decode($proc)."%' or Procod like '%".utf8_decode($proc)."%')
                AND Proest = 'on'
                ORDER BY Pronom
                LIMIT 50
                ";

        $res = mysql_query($q, $conex);
        $num = mysql_num_rows($res);
        $val = "";
        if( $num > 0 ){
            while($rows = mysql_fetch_assoc($res)){
                $data[ 'valor' ] = Array( "cod"=> $rows[ 'codigo' ], "des"=> trim(utf8_encode($rows[ 'nombre' ])) );    //Este es el dato a procesar en javascript
                $data[ 'usu' ] = "{$rows[ 'codigo' ]}-".trim(utf8_encode($rows[ 'nombre' ]))."";    //Este es el que ve el usuario
                $dat = Array();
                $dat[] = $data;
                $val .= json_encode( $dat )."\n";
            }
        }
        return $val;
    }

    function mostrarEstadisticas($wfecha_i='', $wfecha_f='', $wgrupo='', $wprocedencia='', $wentidad='', $wespecialidad='', $wprocedimiento=''){
        global $conex;
        global $wbasedato;
        global $wbasedatohce;
        global $wbasedatomovhos;
        global $wbasedatocliame;
        global $wemp_pmla;
        global $wcenimp;
        global $diasHolguraParaGrabarHistorico;
        global $topeGraficos;
        global $wtipoProcedimiento;
        global $wtipoEspecialidad;
        global $wtipoGrupo;
        global $wcriteriosConsulta;
        global $wreemp;
        global $mostrarDetalle;

        $wcriteriosConsulta = str_replace("\\", "", $wcriteriosConsulta);
        $wcriteriosConsulta = json_decode( $wcriteriosConsulta, true );
        //echo "EDB-3-<PRE> ---> ".print_r($wcriteriosConsulta)."</PRE>";

        if( $wfecha_i == "" || $wfecha_f == "" )
            return;

        $wgrupoOri                  = $wgrupo;
        $arr_anio_mes               = array();

        $arr_entidad_anio_mes        = array();
        $arr_entidad_anio_mes_serv   = array();
        $arr_especialidad_anio_mes   = array();
        $arr_especialidadP_anio_mes  = array();
        $arr_especialidadS_anio_mes  = array();
        $arr_procedencia_anio_mes    = array();
        $arr_procedimiento_anio_mes  = array();
        $arr_procedimientoP_anio_mes = array();
        $arr_procedimientoS_anio_mes = array();
        $arr_diagnosticoP_anio_mes   = array();
        $arr_diagnostico_anio_mes    = array();
        $arr_diagnosticoS_anio_mes   = array();

        $arr_datos                  = array();
        $arr_usuarios               = array();

        $arr_his_sin_entidad        = array();
        $arr_his_sin_ccoing         = array();

        $arr_especialidades         = array();
        $arr_entidades              = array();
        $arr_entidades_tipo         = array();
        $arr_procedimientos         = array();
        $arr_barrios                = array();
        $auxiliarHistoriasRevisadas = array();

        $arr_fechai                 = explode("-",$wfecha_i);
        $arr_fechaf                 = explode("-",$wfecha_f);

        /*CONSULTAR LOS HISTORICOS*/
        $arr_periodos_guardados       = array();
        $arr_meses_consultados        = array();
        $peri_consulta_historica      = array();
        $peri_consulta_ordinaria      = array();
        $mesesConsultados             = 0;
        $detMesesConsultados          = array();
        $servicios_historia           = array();
        $arr_det_pacientes            = array();
        $arr_det_pacientes_total      = array();
        $arr_det_entidades            = array();
        $arr_det_entidades_total      = array();
        $arr_det_diagnosticos         = array();
        $arr_det_diagnosticos_total   = array();
        $arr_det_especialidades       = array();
        $arr_det_especialidades_total = array();
        $arr_det_procedimientos       = array();
        $arr_det_procedimientos_total = array();
        $arr_det_procedencia          = array();
        $arr_det_procedencia_total    = array();


        //--->  arreglos para mejorar los consolidados, con el propósito de no contar doble los diferentes criterios ejm: un diagnostico aplica en dos centros de costos hospitalarios 2016-03-30
        $arr_servicios_hospitalarios     = array();
        $arr_servicios_cirugia_grupo     = array();
        $arr_servicios_urgencias_grupo     = array();



        //----------------> se construye un arreglo que indica cuales meses se están consultando sean completos o incompletos<----------------------//
        $anhos_inicio_final = $arr_fechaf[0]*1 - $arr_fechai[0]*1;
        for ($i = 0; $i <= $anhos_inicio_final; $i++ ) {
            $indice_anho = "".($arr_fechai[0]*1 + $i)."";
            $arr_meses_consultados[$indice_anho] = array();
        }

        //---> se organizan las fechas consultadas en un arreglo indexado por años y meses, verificando si se consulta el mes completo
        foreach ($arr_meses_consultados as $anho => &$data) {
            if( $anho == $arr_fechai[0] ){
                $limiteInicial = $arr_fechai[1]*1;
            }else{
                $limiteInicial = 1;
            }

            if( $anho == $arr_fechaf[0] ){
                $limiteFinal  = $arr_fechaf[1];
            }else{
                $limiteFinal = 12;
            }

            for ( $j = $limiteInicial; $j <= $limiteFinal; $j++ ) {

                $prefijo = "";
                if($j < 10){
                    $prefijo = "0";
                }
                $mes =  $prefijo.$j;
                $analizandoMesInicial = false;
                $completo             = true;

                if( (String)$anho == $arr_fechai[0] and (String)$mes == $arr_fechai[1] ){
                    if( $arr_fechai[2] == "01" ){
                        $completo =  true;
                    }else{
                        $completo = false;
                    }
                    $analizandoMesInicial = true;
                }

                if( (String)$anho == $arr_fechaf[0] and (String)$mes == $arr_fechaf[1] ){
                    //---> si estoy parado en el mes final consultado
                    $diaSiguiente = strtotime( $wfecha_f." +1 day");
                    $diaSiguiente = date( "Y-m-d", $diaSiguiente );
                    $diaSiguiente = explode( "-", $diaSiguiente );
                    if( $diaSiguiente[1] != $arr_fechaf[1] ){//--> si cambia el número del mes es porque estaba consultadoCompletamente
                        if( !$analizandoMesInicial )//--> si se está analizando el mes inicial entonces no debe modificar el estado que traiga
                            $completo =  true;
                    }else{
                        $completo = false;
                    }
                }
                $arr_meses_consultados[$anho][$mes] = $completo;
                $mesesConsultados++;
            }
        }

        foreach ($arr_meses_consultados as $keyAnho => $meses) {
            foreach ($meses as $keyMes => $datos) {
                if( !$datos ){//sino se consultó completo, entonces no se guarda en el histórico
                   $detMesesConsultados[$keyAnho][$keyMes]['guardarHistorico'] = false;
                }else{
                    $fecAuxiliar = $keyAnho."-".$keyMes."-01";
                    $fecAuxiliar = (strtotime($fecAuxiliar." +1 month")) - 24*60*60;//ultimo dia del mes
                    $fecAuxiliar = date( 'Y-m-d', $fecAuxiliar );
                    $detMesesConsultados[$keyAnho][$keyMes]['guardarHistorico'] = false;
                    $detMesesConsultados[$keyAnho][$keyMes]['ultimoDia'] = $fecAuxiliar;
                }
            }
        }
        $arr_meses = array('01'=>'Enero', '02'=>'Febrero', '03'=>'Marzo', '04'=>'Abril', '05'=>'Mayo',
                            '06'=>'Junio','07'=>'Julio', '08'=>'Agosto', '09'=>'Septiembre',
                            '10'=>'Octubre', '11'=>'Noviembre', '12'=>'Diciembre'
                            );

        /*DATOS BASICOS*/

            // cargar servicios que pertenecen a los grupos

            $q2 = "SELECT ccocod
                        FROM {$wbasedatomovhos}_000011
                        WHERE ccohos = 'on'
                          AND ccoest = 'on'";
            $res2 = mysql_query($q2, $conex);
            $num2 = mysql_num_rows($res2);
            if( $num2 > 0 ){
                while($row2 = mysql_fetch_assoc($res2)){
                    array_push( $arr_servicios_hospitalarios, $row2['ccocod'] );
                }
            }
            //echo "<br>edb-><pre>".print_r($arr_servicios_hospitalarios)."</pre>";

            $q2 = "SELECT ccocod
                        FROM {$wbasedatomovhos}_000011
                        WHERE ccocir = 'on'
                          AND ccoest = 'on'";
            $res2 = mysql_query($q2, $conex);
            $num2 = mysql_num_rows($res2);
            if( $num2 > 0 ){
                while($row2 = mysql_fetch_assoc($res2)){
                    array_push( $arr_servicios_cirugia_grupo, $row2['ccocod'] );
                }
            }

            $q2 = "SELECT ccocod
                        FROM {$wbasedatomovhos}_000011
                        WHERE ccourg = 'on'
                          AND ccoest = 'on'";
            $res2 = mysql_query($q2, $conex);
            $num2 = mysql_num_rows($res2);
            if( $num2 > 0 ){
                while($row2 = mysql_fetch_assoc($res2)){
                    array_push( $arr_servicios_urgencias_grupo, $row2['ccocod'] );
                }
            }
            //Cargar todos los tipos de empresa
            $tipos_de_empresa = array();
            $q2  = "SELECT Temcod as codigo, Temdes as nombre
                      FROM ".$wbasedato."_000029 ";
            $res2 = mysql_query($q2, $conex);
            $num2 = mysql_num_rows($res2);
            if( $num2 > 0 ){
                while($row2 = mysql_fetch_assoc($res2)){
                    if( array_key_exists( $row2['codigo'], $tipos_de_empresa ) == false ){
                        $tipos_de_empresa[ $row2['codigo'] ] =$row2['nombre'];
                    }
                }
                $tipos_de_empresa[ 99 ] ="SIN TIPO DE ENTIDAD";
            }

            //Cargar todas las entidades
            $q2  = "SELECT Empcod as codigo, Empnom as nombre, Emptem as tipo_entidad
                      FROM ".$wbasedato."_000024 ";
            $res2 = mysql_query($q2, $conex);
            $num2 = mysql_num_rows($res2);
            if( $num2 > 0 ){
                while($row2 = mysql_fetch_assoc($res2)){
                    if( array_key_exists( $row2['codigo'], $arr_entidades ) == false ){
                        $arr_entidades[ $row2['codigo'] ] =$row2['nombre'];
                    }
                    if( array_key_exists( $row2['codigo'], $arr_entidades_tipo ) == false ){
                        $arr_entidades_tipo[ $row2['codigo'] ] = $row2['tipo_entidad'];
                    }
                }
                $arr_entidades_tipo[ "99" ] = "99";
                $arr_entidades[ "99" ] = "SIN ENTIDAD";
            }

            //Cargar todos los barrios
            $q2  = "SELECT Barcod as codigo, Bardes as nombre
                      FROM root_000034 ";
            $res2 = mysql_query($q2, $conex);
            $num2 = mysql_num_rows($res2);
            if( $num2 > 0 ){
                while($row2 = mysql_fetch_assoc($res2)){
                    if( array_key_exists( $row2['codigo'], $arr_barrios ) == false ){
                        $arr_barrios[ $row2['codigo'] ] =$row2['nombre'];
                    }
                }
                $arr_barrios[ "99" ] = "SIN BARRIO";
            }

            $arr_especialidades  = array();
            $qesps               = " SELECT Espcod, Espnom
                                       FROM {$wbasedatomovhos}_000044";
            $rsents              = mysql_query( $qesps,$conex );
            while( $rowesps = mysql_fetch_assoc( $rsents ) ){
                $arr_especialidades[$rowesps['Espcod']] = $rowesps['Espnom'];
            }

            $arr_diagnosticos  = array();
            $tipos             = array();
            $qentidad          = " SELECT Codigo, Descripcion
                                     FROM root_000011
                                    ORDER BY Descripcion";
            $rsents            = mysql_query( $qentidad,$conex );

            while( $rowents = mysql_fetch_assoc( $rsents ) ){
                $arr_diagnosticos[$rowents['Codigo']] = $rowents['Descripcion'];
            }

            $arr_procedimientos = array();
            $qprocedimi         = " SELECT Procod, Pronom
                                     FROM {$wbasedato}_000103";
            $rsproce            = mysql_query( $qprocedimi,$conex );
            while( $rowpros = mysql_fetch_assoc( $rsproce ) ){
                $arr_procedimientos[$rowpros['Procod']] = $rowpros['Pronom'];
            }

            //se organizan las condiciones para que solo se tengan en cuenta los servicios consultados
            if($wgrupo != ''){
                if( $wtipoGrupo == "grupo" ){

                    $query = " SELECT Ccocod
                                 FROM {$wbasedatomovhos}_000011
                                WHERE {$wgrupo}='on'
                                  AND Ccoest = 'on'";
                    $rsCco    = mysql_query( $query, $conex );
                    $cantCcos = mysql_num_rows( $rsCco );
                    if( $cantCcos > 1 ){
                        $auxCcos = array();
                    }
                    $grupoAux235 = substr($wgrupo, 3, 3);
                    //echo "<br> edb-> grupoAuxiliar $grupoAux235<br>";
                    while( $rowCcos = mysql_fetch_assoc($rsCco) ){
                        if( $cantCcos > 1 ){
                            array_push( $auxCcos, "'".$rowCcos['Ccocod']."'" );
                        } else if( $cantCcos == 1 ){
                            $condicionCco    = " AND Sercod = '{$rowCcos['Ccocod']}'";
                            $condicionCcoEsp = " AND Seeser = '{$rowCcos['Ccocod']}'";
                            $condicionCcoPro = " AND Proser = '{$rowCcos['Ccocod']}'";
                            $condicionCcoDia = " AND Sedser = '{$rowCcos['Ccocod']}'";
                            $condicionCco235 = " AND Repser = '{$rowCcos['Ccocod']}'";
                        }
                    }
                    if( $cantCcos > 1 ){
                        $auxCcos = implode(",", $auxCcos);
                        $condicionCco    = " AND Sercod in ({$auxCcos}) ";
                        $condicionCcoEsp = " AND Seeser in ({$auxCcos}) ";
                        $condicionCcoPro = " AND Proser in ({$auxCcos}) ";
                        $condicionCcoDia = " AND Sedser in ({$auxCcos}) ";
                        $condicionCco235 = " AND Repser in ({$auxCcos}) ";
                        $condicionCco235 = " AND Repser = '{$grupoAux235}' ";
                    }


                }else{
                    $condicionCco    = " AND Sercod = '$wgrupo' ";
                    $condicionCcoEsp = " AND Seeser = '$wgrupo' ";
                    $condicionCcoPro = " AND Proser = '$wgrupo' ";
                    $condicionCcoDia = " AND Sedser = '$wgrupo' ";
                    $condicionCco235 = " AND Repser = '$wgrupo' ";
                }
            }else{
                $condicionCco       = "";
                $condicionCcoEsp    = "";
                $condicionCcoPro    = "";
                $condicionCcoDia    = "";
                $condicionCco235    = " AND Repser = '' ";
            }

            //se organizan las condiciones para que solo se tengan en cuenta los servicios consultados
            ( $wtipoProcedimiento == "" ) ? $condicionPro235 = "  AND Reptip = 'PROCEDIMIENTOS' AND Reptim = ''" : $condicionPro235 = "  AND Reptip = 'PROCEDIMIENTOS' AND Reptim = '$wtipoProcedimiento'";
            ( $wtipoEspecialidad  == "" ) ? $condicionEsp235 = "  AND Reptip = 'ESPECIALIDAD'   AND Reptim = ''" : $condicionEsp235 = "  AND Reptip = 'ESPECIALIDAD'   AND Reptim = '$wtipoEspecialidad'";
            ( $wtipoDiagnostico   == "" ) ? $condicionDia235 = "  AND Reptip = 'DIAGNOSTICO'    AND Reptim = ''" : $condicionDi235  = "  AND Reptip = 'DIAGNOSTICO'    AND Reptim = '$wtipoDiagnostico'";
        /*FIN DATOS BASICOS*/


        $completo       = false;
        $wfecha_actual  = date("Y-m-d");
        $mesesGuardados = array();

        /** en este segmento se construyen el arreglo con los periodos que se deben consultar de manera ordinaria, dependiendo de si se están consultando
        meses completos y de si están o no en el histórico**/

        foreach ( $arr_meses_consultados as $keyAnho => $meses ) {

            foreach ($meses as $keyMes => $completo ){// si el mes anterior verificado estuvo completo y en el histórico, se debe guardar un periodo ordinario

                if( $enHistorico and $limiteInferiorOrdinario != "" and $limiteSuperiorOrdinario != "" ){
                    $aux = array( 'limiteInferior'=>$limiteInferiorOrdinario, 'limiteSuperior'=>$limiteSuperiorOrdinario );
                    array_push( $peri_consulta_ordinaria, $aux );
                    $limiteSuperiorOrdinario = "";
                    $limiteInferiorOrdinario = "";
                }

                $enHistorico = false;
                if( $completo and $wreemp != "on" ){//--> si el mes consultado está completo

                    if( $mostrarDetalle != "on" ){//--> si el mes está completo y se está consultando detalle
                        if( count( $wcriteriosConsulta )  >  0  ){
                            $numQuery = 0;
                            foreach ($wcriteriosConsulta as $keyCriterio => $datosCriterio) {
                                //--> construcción de consulta a la tabla de histórico.
                                $numQuery++;
                                $keyCriterio2 = strtoupper( $keyCriterio );
                                ( $keyCriterio2 == "ENTIDAD") ? $condicionTipoDetalle = "" : $condicionTipoDetalle = " AND Reptim = '{$datosCriterio['tipo']}' ";
                                if( $numQuery > 1 ){
                                    $q .= "UNION ALL ";
                                    $q .= "SELECT Repano as ano, Repmes as mes, Repser as servicio, Reptip as tipo, Repcod as codigo, Repval*1 as valor, Reptim tipoDetalle
                                             FROM {$wbasedato}_000235
                                            WHERE Repano = '{$keyAnho}'
                                              AND Repmes = '{$keyMes}'
                                              AND Reptip = '{$keyCriterio2}'
                                              {$condicionCco235}
                                              {$condicionTipoDetalle}";
                                }else{

                                    $q="SELECT Repano as ano, Repmes as mes, Repser as servicio, Reptip as tipo, Repcod as codigo, Repval*1 as valor, Reptim tipoDetalle
                                          FROM {$wbasedato}_000235
                                         WHERE Repano = '{$keyAnho}'
                                           AND Repmes = '{$keyMes}'
                                           AND Reptip = '{$keyCriterio2}'
                                           {$condicionCco235}
                                           {$condicionTipoDetalle}";
                                }
                                if( $numQuery > 0 and !isset($wcriteriosConsulta['Entidad'])){
                                    $q .= "UNION ALL ";
                                    $q.= "SELECT Repano as ano, Repmes as mes, Repser as servicio, Reptip as tipo, Repcod as codigo, Repval*1 as valor, Reptim tipoDetalle
                                    FROM {$wbasedato}_000235
                                   WHERE Repano = '{$keyAnho}'
                                     AND Repmes = '{$keyMes}'
                                     AND reptip = 'ENTIDAD'";
                                }
                            }
                        }else{//-> se está consultando absolutamete todo( no hay criterios específicos )
                            //echo " se está consultando todo ";
                            $q="SELECT Repano as ano, Repmes as mes, Repser as servicio, Reptip as tipo, Repcod as codigo, Repval*1 as valor, Reptim tipoDetalle
                                          FROM {$wbasedato}_000235
                                         WHERE Repano = '{$keyAnho}'
                                           AND Repmes = '{$keyMes}'
                                               {$condicionCco235}
                                           AND Reptim = ''";
                            $q.= " UNION ALL " ;

                            $q.= "SELECT Repano as ano, Repmes as mes, Repser as servicio, Reptip as tipo, Repcod as codigo, Repval*1 as valor, Reptim tipoDetalle
                                    FROM {$wbasedato}_000235
                                   WHERE Repano = '{$keyAnho}'
                                     AND Repmes = '{$keyMes}'
                                     AND reptip = 'ENTIDAD'
                                     AND Reptim = 'P'";
                        }
                        //echo " esta completo el mes consultado ".$q;

                        $q       .= " ORDER BY valor desc, tipo ";
                        $res     = mysql_query($q, $conex) or die( mysql_error(). "<br> - <br>" .$q);
                        $numPpal = mysql_num_rows($res);
                        $val     = "";
                        $conteoAux = 0;
                        while($rows = mysql_fetch_assoc($res)){
                            $enHistorico = true;
                            if( array_key_exists( $rows['ano'], $arr_periodos_guardados ) == false ){
                                $arr_periodos_guardados[ $rows['ano'] ] = array();
                            }
                            if( in_array( $rows['mes'], $arr_periodos_guardados[ $rows['ano'] ] ) == false ){
                                array_push($arr_periodos_guardados[ $rows['ano'] ],$rows['mes']);
                            }
                            if( !in_array( "$keyAnho-$keyMes", $mesesGuardados )){
                                if( array_key_exists( $keyAnho, $arr_anio_mes ) == false ){
                                    $arr_anio_mes[ $keyAnho ] = array();
                                }
                                if( in_array( $keyMes, $arr_anio_mes[ $keyAnho ] ) == false ){
                                    array_push($arr_anio_mes[ $keyAnho ],$keyMes);
                                }
                                //** si está en el histórico empiezo a cargar los datos en los arreglos contenedores**//
                                switch ( $rows['tipo'] ) {
                                    case 'ENTIDAD':
                                        $rows['codigo'] = trim( $rows['codigo'] );

                                        //El numero 99 indica que no se tiene informacion
                                        if( $rows['tipoDetalle'] == "P"){
                                            $rows['tipo_entidad'] = '02';
                                        }else{
                                            ( isset($arr_entidades_tipo[ $rows['codigo'] ]) )? $rows['tipo_entidad'] = $arr_entidades_tipo[ $rows['codigo'] ] : $rows['tipo_entidad'] = "99";
                                        }
                                        if( array_key_exists( $rows['tipo_entidad'], $arr_entidad_anio_mes ) == false ){
                                            $arr_entidad_anio_mes[ $rows['tipo_entidad'] ] = array('nombre'=>$tipos_de_empresa[$rows['tipo_entidad']],
                                                                                                  'entidades'=>array()
                                                                                                  );
                                        }
                                        $arr_entidad_anio_mes[ $rows['tipo_entidad'] ]['entidades'][ $rows['codigo'] ][$keyAnho][$keyMes]['valor'] += $rows['valor'];
                                        $total_egresos += $rows['valor'];
                                        break;
                                    case 'ESPECIALIDAD':
                                            $arr_especialidad_anio_mes[$rows['codigo']][$keyAnho][$keyMes]['valor'] += $rows['valor'];
                                        break;
                                    case 'DIAGNOSTICO':
                                            $arr_diagnostico_anio_mes[$rows['codigo']][$keyAnho][$keyMes]['valor'] += $rows['valor'];
                                            $conteoAux += $rows['valor']*1;
                                        break;
                                    case 'PROCEDIMIENTOS':
                                            $arr_procedimiento_anio_mes[$rows['codigo']][$keyAnho][$keyMes]['valor'] += $rows['valor'];
                                        break;
                                    case 'PROCEDENCIA':
                                        $arr_procedencia_anio_mes[$rows['codigo']][$keyAnho][$keyMes]['valor'] += $rows['valor'];
                                        break;
                                }
                            }
                            //array_push( $mesesGuardados, "$keyAnho-$keyMes");
                        }
                    }
                    if(!$enHistorico){// si se consultó completo pero no está en el histórico, entonces se debe verificar si es el primer mes consultado

                        /***
                            si el mes en análisis está completo y no ha sido guardado en el histórico, y adicionalmente la diferencia entre su último dia y la fecha actual es
                            mayor a los dias de holgura, los datos referentes a este mes pueden ser guardados en el histórico
                        ***/
                        $diasDiff = diasDiferencia( $wfecha_actual, $detMesesConsultados[$keyAnho][$keyMes]['ultimoDia'] );
                        if(  $diasDiff*1 > $diasHolguraParaGrabarHistorico*1 and $wgrupo == "" and count($wcriteriosConsulta) == 0 ){
                            $detMesesConsultados[$keyAnho][$keyMes]['guardarHistorico'] = true;
                        }
                        //****>

                        if( $keyAnho == $arr_fechai[0] and $keyMes== $arr_fechai[1] and $limiteInferiorOrdinario == ""){//--> el primer mes consultado
                            $limiteInferiorOrdinario = implode( "-", $arr_fechai );
                        }else{//--> si es un mes intermedio que es el primero que no está en el histórico, y es diferente del primer mes, ahí empieza el intervalo
                            if($limiteInferiorOrdinario == ""){//--> si se apiló la iteración anterior(periodo completo a consultar) hubo interrúpción y debe construirse otro intervalo
                                $limiteInferiorOrdinario = $keyAnho."-".$keyMes."-01";
                            }
                        }

                        if( $limiteSuperiorOrdinario == "" ){//--> si no había una fecha limite superior, se debe poner el último dia de este mes completo fuera del histórico
                            if( $keyAnho == $arr_fechaf[0] and $keyMes== $arr_fechaf[1] ){
                                $limiteSuperiorOrdinario = implode( "-", $arr_fechaf );
                                $aux = array( 'limiteInferior'=>$limiteInferiorOrdinario, 'limiteSuperior'=>$limiteSuperiorOrdinario );
                                array_push( $peri_consulta_ordinaria, $aux );
                            }else{
                                //--> buscar el último dia del mes actual
                                $fecAuxiliar = $keyAnho."-".$keyMes."-01";
                                $fecAuxiliar = (strtotime($fecAuxiliar." +1 month")) - 24*60*60;//dia primero del mes actual en la iteración + un mes, para llegar al primero del mes siguiente
                                                                                             // luego se le resta un dia, para saber cual es el último dia del mes en revisión.
                                $limiteSuperiorOrdinario = date('Y-m-d', $fecAuxiliar);
                            }
                        }else{//--> esta consultado completo pero no está en el histórico, entonces se actualiza el limite superior ordinario
                                //--> buscar el último dia del mes actual estudiado
                                $fecAuxiliar = $keyAnho."-".$keyMes."-01";
                                $fecAuxiliar = (strtotime($fecAuxiliar." +1 month")) - 24*60*60;//dia primero del mes actual en la iteración + un mes, para llegar al primero del mes siguiente
                                                                                                // luego se le resta un dia, para saber cual es el último dia del mes en revisión.
                                $limiteSuperiorOrdinario = date('Y-m-d', $fecAuxiliar);
                                $aux = array( 'limiteInferior'=>$limiteInferiorOrdinario, 'limiteSuperior'=>$limiteSuperiorOrdinario );
                                array_push( $peri_consulta_ordinaria, $aux );
                        }

                    }else{//-> si sí está en el histórico, entonces se guarda el periodo tal cual está

                            if( $mostrarDetalle == "on" and $limiteInferiorOrdinario == "" and $limiteSuperiorOrdinario == ""){//-> si está en el historico, pero está consultando detalle, y llegó hasta este punto, entonces se asignan las fechas del limite
                                $limiteInferiorOrdinarioAux = $wfecha_i;
                                $limiteSuperiorOrdinarioAux = $wfecha_f;
                                $aux = array( 'limiteInferior'=>$limiteInferiorOrdinarioAux, 'limiteSuperior'=>$limiteSuperiorOrdinarioAux );
                                array_push( $peri_consulta_ordinaria, $aux );
                                $limiteSuperiorOrdinarioAux = "";
                                $limiteInferiorOrdinarioAux = "";
                            }

                            if( $limiteInferiorOrdinario != "" and $limiteSuperiorOrdinario != "" ){
                                $aux = array( 'limiteInferior'=>$limiteInferiorOrdinario, 'limiteSuperior'=>$limiteSuperiorOrdinario );
                                array_push( $peri_consulta_ordinaria, $aux );
                                $limiteSuperiorOrdinario = "";
                                $limiteInferiorOrdinario = "";
                            }
                    }

                }else{//--> si el mes en verificación no se consultó completo, pregunto si es el primer mes consultado o el último, para establecer límites de consulta

                    if( $completo and $wreemp == "on" ){ //--> se consultó completo pero se quiere reemplazar si está en el histórico, así que se elimina y se guarda el periodo en el arreglo de consultas ordinarias
                        $queryReem = " DELETE FROM {$wbasedato}_000235 WHERE Repano='$keyAnho' AND Repmes = '{$keyMes}' ";
                        $rsReem    = mysql_query( $queryReem, $conex );

                        //--> despues de borrado, se organiza todo para que se guarde en el histórico
                        /***
                            si el mes en análisis está completo y no ha sido guardado en el histórico, y adicionalmente la diferencia entre su último dia y la fecha actual es
                            mayor a los dias de holgura, los datos referentes a este mes pueden ser guardados en el histórico
                        ***/
                        $diasDiff = diasDiferencia( $wfecha_actual, $detMesesConsultados[$keyAnho][$keyMes]['ultimoDia'] );
                        if(  $diasDiff*1 > $diasHolguraParaGrabarHistorico*1 and $wgrupo == "" and count($wcriteriosConsulta) == 0 ){
                            $detMesesConsultados[$keyAnho][$keyMes]['guardarHistorico'] = true;
                        }
                        //****>

                        if( $keyAnho == $arr_fechai[0] and $keyMes== $arr_fechai[1] ){//--> el primer mes consultado
                            $limiteInferiorOrdinario = implode( "-", $arr_fechai );
                        }

                        if( $limiteSuperiorOrdinario == "" ){//--> si no había una fecha limite superior, se debe poner el último dia de este mes completo fuera del histórico
                            if( $keyAnho == $arr_fechaf[0] and $keyMes== $arr_fechaf[1] ){
                                $limiteSuperiorOrdinario = implode( "-", $arr_fechaf );
                                $aux = array( 'limiteInferior'=>$limiteInferiorOrdinario, 'limiteSuperior'=>$limiteSuperiorOrdinario );
                                array_push( $peri_consulta_ordinaria, $aux );
                            }else{
                                //--> buscar el último dia del mes actual
                                $fecAuxiliar = $keyAnho."-".$keyMes."-01";
                                $fecAuxiliar = (strtotime($fecAuxiliar." +1 month")) - 24*60*60;//dia primero del mes actual en la iteración + un mes, para llegar al primero del mes siguiente
                                                                                             // luego se le resta un dia, para saber cual es el último dia del mes en revisión.
                                $limiteSuperiorOrdinario = date('Y-m-d', $fecAuxiliar);
                            }
                        }else{//--> esta consultado completo pero no está en el histórico, entonces se actualiza el limite superior ordinario
                                //--> buscar el último dia del mes actual estudiado
                                $fecAuxiliar = $keyAnho."-".$keyMes."-01";
                                $fecAuxiliar = (strtotime($fecAuxiliar." +1 month")) - 24*60*60;//dia primero del mes actual en la iteración + un mes, para llegar al primero del mes siguiente
                                                                                                // luego se le resta un dia, para saber cual es el último dia del mes en revisión.
                                $limiteSuperiorOrdinario = date('Y-m-d', $fecAuxiliar);
                        }
                    }

                    if( !$completo ){//-> si completo es falso no importa si se quiere reemplazar o no los datos

                        if( $limiteInferiorOrdinario == "" ){//--> si no hay limite inferior para consulta ordinaria
                            if( $keyAnho == $arr_fechai[0] and $keyMes== $arr_fechai[1] ){//--> el primer mes consultado
                                $limiteInferiorOrdinario = implode( "-", $arr_fechai );
                            }else{
                                $limiteInferiorOrdinario = $keyAnho."-".$keyMes."-01";
                            }
                        }

                        if( $limiteInferiorOrdinario != ""){

                                if( $keyAnho == $arr_fechaf[0] and $keyMes== $arr_fechaf[1] ){
                                    $limiteSuperiorOrdinario = implode( "-", $arr_fechaf );
                                    $aux = array( 'limiteInferior'=>$limiteInferiorOrdinario, 'limiteSuperior'=>$limiteSuperiorOrdinario );
                                    array_push( $peri_consulta_ordinaria, $aux );
                                }else{
                                    //--> buscar el último dia del mes actual
                                    $fecAuxiliar = $keyAnho."-".$keyMes."-01";
                                    $fecAuxiliar = (strtotime($fecAuxiliar." +1 month")) - 24*60*60;//dia primero del mes actual en la iteración + un mes, para llegar al primero del mes siguiente
                                                                                                 // luego se le resta un dia, para saber cual es el último dia del mes en revisión.
                                    $limiteSuperiorOrdinario = date('Y-m-d', $fecAuxiliar);
                                }
                        }
                    }

                }
            }
        }
        //echo "<br> EDB --> <pre>".print_r( $arr_diagnostico_anio_mes, true )."</pre>";
        //return;
        //---> punto de control acá se vuelven  a hacer las consultas.
        $tablaTmpEgreso   = "tmpRepEgr".date('His');
        $qaux             = "DROP TABLE IF EXISTS $tablaTmpEgreso";
        $resdr            = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
        $query            = "CREATE TEMPORARY TABLE IF NOT EXISTS $tablaTmpEgreso (INDEX idxtemporalhising(Egrhis, Egring) )";

        $realizarConsulta = false;
        $i                = 0;

        //echo "<pre>".print_r( $peri_consulta_ordinaria, true )."</pre>";
        foreach ( $peri_consulta_ordinaria as $i => $periodo ) {//--> construcción del query de egresos que no están guardados en la tabla de histórico
            $realizarConsulta = true;
            $i++;
            if( $i > 1 ){
                $query .= " UNION ALL ";
            }
            //Si eligieron un grupo, no es necesario traer todos los egresos
            if($wgrupo != ''){

                $query .= "SELECT Egrhis, Egring, Egrfee
                            FROM {$wbasedato}_000108 FORCE INDEX ( egrfeeidx ), {$wbasedato}_000112
                           WHERE Egrfee BETWEEN '{$periodo['limiteInferior']}' AND '{$periodo['limiteSuperior']}'
                             AND Egrhis=Serhis
                             AND Egring=Sering
                                 $condicionCco
                           GROUP BY Egrhis, Egring";
            }else{
                //---> creación tabla temporal
                $query .= "SELECT Egrhis, Egring, Egrfee
                            FROM {$wbasedato}_000108 FORCE INDEX ( egrfeeidx )
                           WHERE Egrfee BETWEEN '{$periodo['limiteInferior']}' AND '{$periodo['limiteSuperior']}'";
            }
        }
        //echo "<pre>".print_r( $query, true)."</pre>";
        if( $mostrarDetalle == "on")
            $realizarConsulta = true;


        if( $realizarConsulta ){//--> si hay egresos por consultar  en la tabla original( hay periodos consultados que no están en la tabla histórico ) se realiza la consulta
            $query .= " ORDER BY egrfee ";
            $res = mysql_query($query, $conex) or die( 'error '.mysql_error().' en el query: '.$query);

            //--> se construye un arrelgo donde se guardan los servicios visitados por cada historia-ingreso
            $q = "SELECT Serhis, Sering, Sercod
                    FROM {$tablaTmpEgreso} FORCE INDEX ( idxtemporalhising ), {$wbasedato}_000112
                   WHERE Serhis=Egrhis
                     AND Sering=Egring";
            $rsser = mysql_query( $q );
            while( $rowser = mysql_fetch_assoc( $rsser ) ){
                if( !isset($servicios_historia[$rowser['Serhis']."-".$rowser['Sering']] ) ){
                    $servicios_historia[$rowser['Serhis']."-".$rowser['Sering']] = array();
                }
                array_push( $servicios_historia[$rowser['Serhis']."-".$rowser['Sering']], $rowser['Sercod'] );
            }
        }

        if( $mostrarDetalle == "on" ){
            //--> hacer consulta con la tabla de usuarios para mostrar los datos de los pacientes a los que se le mostrará el detalle
            $querydet = "  SELECT pachis, Egring, pacno1, pacno2, pacap1, pacap2
                             FROM {$wbasedato}_000100 a
                            INNER JOIN
                                  {$tablaTmpEgreso} b ON ( a.pachis = b.Egrhis )";
            $rsdet    = mysql_query( $querydet, $conex )  or die (mysql_errno().":".mysql_error());
            while( $rowdet = mysql_fetch_assoc( $rsdet ) ){
                $nombre = $rowdet['pacno1']." ".$rowdet['pacno2']." ".$rowdet['pacap1']." ".$rowdet['pacap2'];
                $nombre = utf8_encode( $nombre );
                $arr_det_pacientes[$rowdet['pachis']."-".$rowdet['Egring']]['nombre'] = $nombre;
            }

        }
        //echo " <br> EDB-> <br><pre>".print_r( $arr_det_pacientes, true )."</pre>";

        //--> si hay periodos que deben ser consultados por fuera de la tabla histórica
        if( $realizarConsulta ){

            $q = "SELECT Egrhis as his, Egring as ing, Ingcem as entidad, Ingtpa as tipo_ingreso, Egrfee as fecha_egreso, Pacbar as barrio
                    FROM ".$tablaTmpEgreso." FORCE INDEX ( idxtemporalhising ), ".$wbasedato."_000101, ".$wbasedato."_000100
                   WHERE Egrhis=Inghis
                     AND Egring=Ingnin
                     AND Egrhis=Pachis
                     AND Inghis=Pachis
                     ORDER BY egrfee
                    ";
            $res = mysql_query($q, $conex) or die( 'error '.mysql_error().' en el query ');
            $numPpal = mysql_num_rows($res);


            if( count( $auxiliarHistoriasRevisadas ) > 0 ){
                unset( $auxiliarHistoriasRevisadas );
                $auxiliarHistoriasRevisadas = array();
            }
            if( $numPpal > 0 ){//--> construcción de arreglo con los datos

                //if( count( $wcriteriosConsulta ) == 0 or isset( $wcriteriosConsulta['entidad']) ){
                    //--> SEGMENTO DE ENTIDADES.
                        $historias="";
                        while($row2 = mysql_fetch_assoc($res)){
                            //Tiene entidad?
                            if( $row2['entidad'] == "" ){
                                array_push($arr_his_sin_entidad,$row2['his']);
                            }
                            $arr_datos[$row2['his']."-".$row2['ing']] = $row2 ; //Se guarda la fila en arr_datos
                            $historias .= ", '{$row2['his']}-{$row2['ing']}'";
                        }

                        //**********************BUSCAR EN OTRAS TABLAS LOS DATOS INCOMPLETOS( PACIENTES QUE NO TIENEN REGISTRADA ENTIDAD RESPONSABLE EN EL INGRESO PERO SI EN MOVHOS)****************//
                        if( count($arr_his_sin_entidad) > 0 ){
                            $q_usuarios = "SELECT  inghis as his,inging as ing,Ingres as entidad
                                             FROM ".$wbasedatomovhos."_000016
                                            WHERE Inghis IN (".implode(",",$arr_his_sin_entidad).")
                                              ";

                            $err1 = mysql_query($q_usuarios,$conex);
                            $num1 = mysql_num_rows($err1);

                            if( $num1 > 0 ){
                                while( $row1 = mysql_fetch_assoc($err1) ){
                                    if( array_key_exists(  $row1['his']."-".$row1['ing'], $arr_datos ) ){
                                        $dato                                     = $arr_datos[$row1['his']."-".$row1['ing']];
                                        $dato['entidad']                          = $row1['entidad'];
                                        $arr_datos[$row1['his']."-".$row1['ing']] = $dato;
                                    }
                                }
                            }
                        }
                        //**********************FIN BUSCAR EN OTRAS TABLAS LOS DATOS INCOMPLETOS****************//
                        $historiasSinServicios = 0;
                        foreach ($arr_datos as $keyn=>$row ){
                            $arr_fec  = explode("-",$row['fecha_egreso']);
                            $anio     = $arr_fec[0];
                            $mes      = $arr_fec[1];
                            $historia = $keyn;

                            if( array_key_exists( $anio, $arr_anio_mes ) == false ){
                                $arr_anio_mes[ $anio ] = array();
                            }
                            if( in_array( $mes, $arr_anio_mes[ $anio ] ) == false ){
                                array_push($arr_anio_mes[ $anio ],$mes);
                            }

                            //------------------------------PARA ENTIDADES---------------------------------

                            $row['entidad'] = trim( $row['entidad'] );
                            if( $row['entidad'] == "" ) $row['entidad'] = "99"; //Si no tiene entidad, se asigna 99

                            //El numero 99 indica que no se tiene informacion
                            ( isset($arr_entidades_tipo[ $row['entidad'] ]) )? $row['tipo_entidad'] = $arr_entidades_tipo[ $row['entidad'] ] : $row['tipo_entidad'] = "99";

                            if( $row['tipo_entidad'] == "99" && $row['tipo_ingreso'] == 'P'){//Si no tiene tipo de entidad y el tipo de ingreso es P(particular) se asume como PARTICULARES PERSONA NATURAL
                                $row['tipo_entidad'] = '02';
                            }


                            if( array_key_exists( $row['tipo_entidad'], $arr_entidad_anio_mes ) == false ){
                                $arr_entidad_anio_mes[ $row['tipo_entidad'] ] = array('nombre'=>$tipos_de_empresa[$row['tipo_entidad']],
                                                                                      'entidades'=>array()
                                                                                      );
                            }
                            /* fin de guardado de datos incluyendo servicio*/

                            if( array_key_exists( $row['entidad'], $arr_entidad_anio_mes[$row['tipo_entidad']]['entidades'] ) == false ){
                                $arr_entidad_anio_mes[$row['tipo_entidad']]['entidades'][ $row['entidad'] ] = array();
                                //para el trabajo con servicios
                                $arr_entidad_anio_mes_serv[ $row['entidad'] ] = array();
                                if( $mostrarDetalle == "on" ){
                                    $arr_det_entidades[$row['entidad']] = array();
                                }
                            }
                            if( array_key_exists( $anio, $arr_entidad_anio_mes[$row['tipo_entidad']]['entidades'][ $row['entidad'] ] ) == false ){
                                $arr_entidad_anio_mes[$row['tipo_entidad']]['entidades'][ $row['entidad'] ][ $anio ] = array();
                                $arr_entidad_anio_mes_serv[ $row['entidad'] ][$anio]                                 = array();
                                if( $mostrarDetalle == "on" ){
                                    $arr_det_entidades[$row['entidad']][$anio] = array();
                                }
                            }
                            if( array_key_exists( $mes, $arr_entidad_anio_mes[$row['tipo_entidad']]['entidades'][ $row['entidad'] ][$anio] ) == false ){
                                $arr_entidad_anio_mes_serv[ $row['entidad'] ][$anio][$mes]                                          = array();
                                $arr_entidad_anio_mes[$row['tipo_entidad']]['entidades'][ $row['entidad'] ][ $anio ][$mes]['valor'] = 1;
                                $arr_entidad_anio_mes_serv[ $row['entidad'] ][$anio][$mes] = array();
                                if( $mostrarDetalle == "on" ){
                                    $arr_det_entidades[$row['entidad']][$anio][$mes] = array();
                                }
                            }else{
                                $arr_entidad_anio_mes[$row['tipo_entidad']]['entidades'][ $row['entidad'] ][ $anio ][$mes]['valor']+= 1;
                            }
                            if( $mostrarDetalle == "on" ){
                                if( !( isset( $arr_det_entidades_total[$anio] ) ) ){
                                    $arr_det_entidades_total[$anio] = array();
                                }
                                if( !( isset( $arr_det_entidades_total[$anio][$mes] ) ) ){
                                    $arr_det_entidades_total[$anio][$mes] = array();
                                }
                                if( !in_array($historia, $arr_det_entidades_total[$anio][$mes]))
                                    array_push( $arr_det_entidades_total[$anio][$mes], $historia);
                                if( !isset($arr_det_entidades[$row['entidad']]) ){
                                    $arr_det_entidades[$row['entidad']] = array();
                                }
                                if( !isset($arr_det_entidades[$row['entidad']][$anio]) ){
                                    $arr_det_entidades[$row['entidad']][$anio] = array();
                                }
                                if( !isset($arr_det_entidades[$row['entidad']][$anio][$mes]) ){
                                    $arr_det_entidades[$row['entidad']][$anio][$mes] = array();
                                }
                                array_push( $arr_det_entidades[$row['entidad']][$anio][$mes], $historia );
                            }
                            //para trabajo con servicios
                            if( !array_key_exists( $keyn, $servicios_historia) ){
                                $servicios_historia[ $keyn ]= array('0'=> 'sin servicios');
                                $historiasSinServicios++;
                            }
                            foreach ($servicios_historia[$keyn] as $keyi => $value) {
                                //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                ( isset( $arr_entidad_anio_mes_serv[$row['entidad']][$anio][$mes][$value] ) ) ? $arr_entidad_anio_mes_serv[$row['entidad']][$anio][$mes][$value]++ : $arr_entidad_anio_mes_serv[$row['entidad']][$anio][$mes][$value] = 1;
                            }
                        }
                    //--> FIN SEGMENTO ENTIDADES
                        //echo "<br> EDB --> ENTIDADES --> <pre>".print_r( $arr_det_entidades, true )."</pre>";
                //}

                if( count( $wcriteriosConsulta ) == 0 or isset( $wcriteriosConsulta['diagnostico']) ){
                    //--> SEGMENTO DIAGNOSTICOS
                        while( $rowents = mysql_fetch_assoc( $rsents ) ){
                            $arr_diagnosticos[$rowents['Codigo']] = $rowents['Descripcion'];
                        }
                        $historiasXDiagnostico = array();
                        ( $wcriteriosConsulta['diagnostico']['tipo'] == "" or !isset($wcriteriosConsulta['diagnostico']['tipo'] ) ) ? $condicionTipoDiagnostico = "" : $condicionTipoDiagnostico = " AND a.Diatip = '{$wcriteriosConsulta['diagnostico']['tipo']}'";
                           $q = "SELECT Diahis as his, Diaing as ing, a.Diacod as cod, a.Diatip tipo
                                   FROM ".$tablaTmpEgreso." FORCE INDEX ( idxtemporalhising ), ".$wbasedato."_000109 a
                                  WHERE Egrhis=Diahis
                                    AND Egring=Diaing
                                    $condicionTipoDiagnostico
                                 GROUP BY Diahis,Diaing,a.Diacod, a.Diatip";
                          //echo "<br><br><br><br><pre>".print_r( $q, true)."</pre>";
                          $res = mysql_query($q, $conex) or die( 'error '.mysql_error().' en el query ');
                          $num = mysql_num_rows($res);
                          if( $num > 0 ){
                              while($row1 = mysql_fetch_assoc($res)){
                                  if( array_key_exists(  $row1['his']."-".$row1['ing'], $arr_datos ) ){
                                      $dato = $arr_datos[$row1['his']."-".$row1['ing']];
                                      $tieneServicios =  false;
                                      if( array_key_exists( "diagnosticos", $dato) == false ){
                                          $dato['diagnosticos'] = array();
                                          $dato['diagnosticosPrincipales'] = array();
                                          $dato['diagnosticosSecundarios'] = array();
                                      }
                                      $tipos[$row1['tipo']]++;
                                      $rowaxu                   = array();
                                      $rowaxu['cod']            = $row1['cod'];
                                      $rowaxu['nom']            = $arr_diagnosticos[$row1['cod']];
                                      $rowaxu['ser']            = array();//--> revisar query
                                      /*
                                       $arr_servicios_hospitalarios  = array();
                                        $arr_servicios_cirugia_grupo  = array();
                                       */
                                      unset( $sinContar );
                                      $sinContar     = array("hospitalarios"=>true, "cirugiaGrupo"=>true, "urgenciasGrupo"=>true );//--> variable que garantiza que si es un grupo solo cuente una vez para todos los servicios pertenecientes al grupo
                                      $qsers1 = " SELECT Sedhis, Seding, Sedser
                                                   FROM {$wbasedato}_000238
                                                  WHERE Sedhis = '{$row1['his']}'
                                                    AND Seding = '{$row1['ing']}'
                                                    AND Seddia = '{$row1['cod']}'
                                                    {$condicionCcoDia}";
                                      $rsser1 = mysql_query( $qsers1 ) or die( $qsers1);
                                      while ( $rowser1 = mysql_fetch_assoc( $rsser1 ) ){


                                         //-->  acá se hace la nueva validación para contar por grupo y no por servicios buscar "//-->  acá se haría la nueva validación para contar por grupo y no por servicios" de ahí para abajo
                                            if( in_array($rowser1['Sedser'], $arr_servicios_hospitalarios ) && $sinContar["hospitalarios"] ){
                                                $sinContar["hospitalarios"] = false;
                                                array_push($rowaxu['ser'], "hos" );
                                            }
                                            if( in_array($rowaxu['Sedser'], $arr_servicios_cirugia_grupo ) && $sinContar["cirugiaGrupo"] ){
                                                $sinContar["cirugiaGrupo"] = false;
                                                array_push($rowaxu['ser'], "cir" );
                                            }
                                            if( in_array($rowaxu['Sedser'], $arr_servicios_urgencias_grupo ) && $sinContar["urgenciasGrupo"] ){
                                                $sinContar["urgenciasGrupo"] = false;
                                                array_push($rowaxu['ser'], "urg" );
                                            }
                                        //--> termina la validacion por grupos para el consolidado

                                         array_push( $rowaxu['ser'], $rowser1['Sedser'] );
                                         $tieneServicios =  true;
                                      }
                                      if( !$tieneServicios ){
                                        array_push( $rowaxu['ser'], 'SS' );
                                      }


                                      if( !isset( $historiasXDiagnostico[$row1['cod']] ) ){
                                          $historiasXDiagnostico[$row1['cod']] = array();
                                      }

                                      if( !$tieneServicios and $condicionCcoDia != "" ){

                                      }else{

                                        if( !$tieneServicios and $condicionCcoDia == '' ){
                                            array_push( $dato['diagnosticos'], $rowaxu );
                                           }else{
                                                if ( $tieneServicios ) {
                                                    array_push( $dato['diagnosticos'], $rowaxu );
                                                }
                                          }

                                      }
                                      if( $row1['tipo']  == "S"){
                                          if( !$tieneServicios and $condicionCcoDia == '' ){
                                            array_push( $dato['diagnosticosSecundarios'], $rowaxu );
                                           }else{
                                                if ( $tieneServicios ) {
                                                    array_push( $dato['diagnosticosSecundarios'], $rowaxu );
                                                }
                                          }
                                      }else{
                                          if( !$tieneServicios and $condicionCcoDia == '' ){
                                            array_push( $dato['diagnosticosPrincipales'], $rowaxu );
                                           }else{
                                                if ( $tieneServicios ) {
                                                    array_push( $dato['diagnosticosPrincipales'], $rowaxu );
                                                }
                                          }
                                      }
                                      if( !in_array( $row1['his']."-".$row1['ing'], $historiasXDiagnostico[$row1['cod']] )){
                                          $arr_datos[$row1['his']."-".$row1['ing']] = $dato;
                                          array_push( $historiasXDiagnostico[$row1['cod']], $row1['his']."-".$row1['ing'] );
                                      }
                                  }
                              }
                          }

                          foreach ($arr_datos as $keyn=>$row ){
                            $arr_fec = explode("-",$row['fecha_egreso']);
                            $historia = $keyn;
                            $anio = $arr_fec[0];
                            $mes = $arr_fec[1];

                            if( array_key_exists( $anio, $arr_anio_mes ) == false ){
                                $arr_anio_mes[ $anio ] = array();
                            }
                            if( in_array( $mes, $arr_anio_mes[ $anio ] ) == false ){
                                array_push($arr_anio_mes[ $anio ],$mes);
                            }

                            //------------------------------PARA Diagnosticos---------------------------------
                            if( isset($row['diagnosticos']) ){
                                foreach( $row['diagnosticos'] as $diagnostico ){
                                    if( array_key_exists($diagnostico['cod'],$arr_diagnosticos) == false ){
                                        $arr_diagnosticos[$diagnostico['cod']] = $diagnostico['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $diagnostico['cod'], $arr_diagnostico_anio_mes ) == false ){
                                        $arr_diagnostico_anio_mes[ $diagnostico['cod'] ] = array();
                                        if( $mostrarDetalle ==  "on" ){
                                            $arr_det_diagnosticos[ $diagnostico['cod'] ] = array();
                                        }
                                    }
                                    if( array_key_exists( $anio, $arr_diagnostico_anio_mes[$diagnostico['cod'] ] ) == false ){
                                        $arr_diagnostico_anio_mes[ $diagnostico['cod'] ][ $anio ] = array();
                                        if( $mostrarDetalle ==  "on" ){
                                            $arr_det_diagnosticos[ $diagnostico['cod'] ][ $anio ] = array();
                                        }
                                    }
                                    if( array_key_exists( $mes, $arr_diagnostico_anio_mes[ $diagnostico['cod'] ][$anio] ) == false ){
                                        $arr_diagnostico_anio_mes[ $diagnostico['cod'] ][ $anio ][$mes]['valor'] = 1;
                                        if( $mostrarDetalle ==  "on" ){
                                            $arr_det_diagnosticos[ $diagnostico['cod'] ][ $anio ][$mes] = array();
                                        }
                                    }else{
                                        $arr_diagnostico_anio_mes[ $diagnostico['cod'] ][ $anio ][$mes]['valor']+= 1;
                                    }

                                    if( $mostrarDetalle ==  "on" ){
                                        if( !( isset( $arr_det_diagnosticos_total[$anio] ) ) ){
                                            $arr_det_diagnosticos_total[$anio] = array();
                                        }
                                        if( !( isset( $arr_det_diagnosticos_total[$anio][$mes] ) ) ){
                                            $arr_det_diagnosticos_total[$anio][$mes] = array();
                                        }
                                        if( !( isset( $arr_det_entidades_total[$anio] ) ) ){
                                            $arr_det_entidades_total[$anio] = array();
                                        }
                                        if( !( isset( $arr_det_entidades_total[$anio][$mes] ) ) ){
                                            $arr_det_entidades_total[$anio][$mes] = array();
                                        }
                                        if( !in_array($historia, $arr_det_entidades_total[$anio][$mes]))
                                            array_push( $arr_det_entidades_total[$anio][$mes], $historia);

                                        if( !isset($arr_det_diagnosticos[$diagnostico['cod']]) ){
                                            $arr_det_diagnosticos[$diagnostico['cod']] = array();
                                        }
                                        if( !isset($arr_det_diagnosticos[$diagnostico['cod']][$anio]) ){
                                            $arr_det_diagnosticos[$diagnostico['cod']][$anio] = array();
                                        }
                                        if( !isset($arr_det_diagnosticos[$diagnostico['cod']][$anio][$mes]) ){
                                            $arr_det_diagnosticos[$diagnostico['cod']][$anio][$mes] = array();
                                        }
                                        array_push( $arr_det_diagnosticos_total[ $anio ][$mes], $historia );
                                        array_push( $arr_det_diagnosticos[ $diagnostico['cod'] ][ $anio ][$mes], $historia );
                                    }
                                    //para trabajo con servicios
                                    foreach ($diagnostico['ser'] as $keyi => $value) {
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        ( isset( $arr_diagnostico_anio_mes[$diagnostico['cod']][$anio][$mes]['servicios'][$value] ) ) ? $arr_diagnostico_anio_mes[$diagnostico['cod']][$anio][$mes]['servicios'][$value]++ : $arr_diagnostico_anio_mes[$diagnostico['cod']][$anio][$mes]['servicios'][$value] = 1;
                                    }
                                }
                            }

                            //------------------------------PARA diagnosticos PRINCIPALES---------------------------------
                            if( isset($row['diagnosticosPrincipales']) ){


                                foreach( $row['diagnosticosPrincipales'] as $diagnosticoP ){
                                    /*if( $diagnosticoP['cod'] == "G459" ){
                                        echo "<br> edb--> arreglo por historia {$historia}--> fecha de egreso: {$row['fecha_egreso']} --> $anio --> $mes <br>".print_r( $diagnosticoP['ser'], true )."<br>";
                                        //echo "<br> edb--> ".print_r( $diagnosticoP['ser'], true )."<br>";
                                    }*/
                                    if( array_key_exists($diagnosticoP['cod'],$arr_diagnosticos) == false ){
                                        $arr_diagnosticos[$diagnosticoP['cod']] = $diagnosticoP['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $diagnosticoP['cod'], $arr_diagnosticoP_anio_mes ) == false ){
                                        $arr_diagnosticoP_anio_mes[ $diagnosticoP['cod'] ] = array();
                                    }
                                    if( array_key_exists( $anio, $arr_diagnosticoP_anio_mes[$diagnosticoP['cod'] ] ) == false ){
                                        $arr_diagnosticoP_anio_mes[ $diagnosticoP['cod'] ][ $anio ] = array();
                                    }
                                    if( array_key_exists( $mes, $arr_diagnosticoP_anio_mes[ $diagnosticoP['cod'] ][$anio] ) == false ){
                                        $arr_diagnosticoP_anio_mes[ $diagnosticoP['cod'] ][ $anio ][$mes]['valor'] = 1;
                                    }else{
                                        $arr_diagnosticoP_anio_mes[ $diagnosticoP['cod'] ][ $anio ][$mes]['valor']+= 1;
                                    }
                                    //para trabajo con servicios
                                    foreach ($diagnosticoP['ser'] as $keyi => $value) {
                                        //$value = $value*1;
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        if( $diagnosticoP['cod'] == "G459" ){
                                         //echo "<br>  edb--> entro $value ---> anio [$anio] ----> mes [$mes] ".print_r($arr_diagnosticoP_anio_mes[$diagnosticoP['cod']][$anio][$mes]['servicios'], true)." <br>";
                                        }
                                        ( isset( $arr_diagnosticoP_anio_mes[$diagnosticoP['cod']][$anio][$mes]['servicios'][$value] ) ) ? $arr_diagnosticoP_anio_mes[$diagnosticoP['cod']][$anio][$mes]['servicios'][$value]++ : $arr_diagnosticoP_anio_mes[$diagnosticoP['cod']][$anio][$mes]['servicios'][$value] = 1;
                                    }
                                }
                            }

                            //------------------------------PARA diagnosticos SECUNDARIAS---------------------------------
                            if( isset($row['diagnosticosSecundarios']) ){
                                foreach( $row['diagnosticosSecundarios'] as $diagnosticoS ){
                                    if( array_key_exists($diagnosticoS['cod'],$arr_diagnosticos) == false ){
                                        $arr_diagnosticoes[$diagnosticoS['cod']] = $diagnosticoS['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $diagnosticoS['cod'], $arr_diagnosticoS_anio_mes ) == false ){
                                        $arr_diagnosticoS_anio_mes[ $diagnosticoS['cod'] ] = array();
                                    }
                                    if( array_key_exists( $anio, $arr_diagnosticoS_anio_mes[$diagnosticoS['cod'] ] ) == false ){
                                        $arr_diagnosticoS_anio_mes[ $diagnosticoS['cod'] ][ $anio ] = array();
                                    }
                                    if( array_key_exists( $mes, $arr_diagnosticoS_anio_mes[ $diagnosticoS['cod'] ][$anio] ) == false ){
                                        $arr_diagnosticoS_anio_mes[ $diagnosticoS['cod'] ][ $anio ][$mes]['valor'] = 1;
                                    }else{
                                        $arr_diagnosticoS_anio_mes[ $diagnosticoS['cod'] ][ $anio ][$mes]['valor']+= 1;
                                    }
                                    //para trabajo con servicios
                                    foreach ($diagnosticoS['ser'] as $keyi => $value) {
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        ( isset( $arr_diagnosticoS_anio_mes[$diagnosticoS['cod']][$anio][$mes]['servicios'][$value] ) ) ? $arr_diagnosticoS_anio_mes[$diagnosticoS['cod']][$anio][$mes]['servicios'][$value]++ : $arr_diagnosticoS_anio_mes[$diagnosticoS['cod']][$anio][$mes]['servicios'][$value] = 1;
                                    }
                                }
                            }
                        }
                    //--> FIN SEGMENTO DIAGNOSTICOS
                    //echo "<br> EDB --> DIAGNOSTICOS --> <pre>".print_r( $arr_diagnosticoP_anio_mes["G459"], true )."</pre>";
                }

                if( count( $wcriteriosConsulta ) == 0 or isset( $wcriteriosConsulta['especialidad']) ){
                    //--> SEGMENTO ESPECIALIDADES
                        $historiasXespecialidad = array();
                        ( $wcriteriosConsulta['especialidad']['tipo'] == "" or !isset($wcriteriosConsulta['especialidad']['tipo'] ) ) ? $condicionTipoEspecialidad = "" : $condicionTipoEspecialidad = " AND a.Esptip = '{$wcriteriosConsulta['especialidad']['tipo']}'";
                           $q = "SELECT Egrhis as his, Egring as ing, a.Espcod as cod, a.Esptip tipo, Espmed medico
                                   FROM ".$tablaTmpEgreso." FORCE INDEX ( idxtemporalhising ), ".$wbasedato."_000111 a
                                  WHERE Egrhis=Esphis
                                    AND Egring=Esping
                                    $condicionTipoEspecialidad
                                 GROUP BY Egrhis,Egring,a.Espcod, a.Esptip, a.Espmed";
                          $j = 0;
                          $res = mysql_query($q, $conex) or die( 'error '.mysql_error().' en el query ');
                          $num = mysql_num_rows($res);
                          if( $num > 0 ){
                              while($row1 = mysql_fetch_assoc($res)){

                                  if( array_key_exists(  $row1['his']."-".$row1['ing'], $arr_datos ) ){
                                      $dato = $arr_datos[$row1['his']."-".$row1['ing']];
                                      $tieneServicios = false;
                                      if( array_key_exists( "especialidades", $dato) == false ){
                                          $dato['especialidades'] = array();
                                          $dato['especialidadesPrincipales'] = array();
                                          $dato['especialidadesSecundarios'] = array();
                                      }
                                      $tipos[$row1['tipo']]++;
                                      unset( $sinContar );
                                      $sinContar     = array("hospitalarios"=>true, "cirugiaGrupo"=>true, "urgenciasGrupo"=>true);//--> variable que garantiza que si es un grupo solo cuente una vez para todos los servicios pertenecientes al grupo
                                      unset( $rowaxu );
                                      $rowaxu        = array();
                                      $rowaxu['cod'] = $row1['cod'];
                                      $rowaxu['nom'] = $arrayEspecialidad[$row1['cod']];
                                      $rowaxu['ser'] = array();//--> revisar query
                                      $qsers1 = " SELECT Seehis, Seeing, Seemed, Seeser
                                                   FROM {$wbasedato}_000239
                                                  WHERE Seehis = '{$row1['his']}'
                                                    AND Seeing = '{$row1['ing']}'
                                                    AND Seeesp = '{$row1['cod']}'
                                                    AND Seemed = '{$row1['medico']}'
                                                        {$condicionCcoEsp}";
                                      $rsser1 = mysql_query( $qsers1 ) or die( $qsers1);
                                      while ( $rowser1 = mysql_fetch_assoc( $rsser1 ) ){
                                        $tieneServicios = true;
                                         //-->  acá se hace la nueva validación para contar por grupo y no por servicios buscar "//-->  acá se haría la nueva validación para contar por grupo y no por servicios" de ahí para abajo
                                            if( in_array($rowser1['Seeser'], $arr_servicios_hospitalarios ) && $sinContar["hospitalarios"] ){
                                                $sinContar["hospitalarios"] = false;
                                                array_push($rowaxu['ser'], "hos" );
                                            }
                                            if( in_array($rowser1['Seeser'], $arr_servicios_cirugia_grupo ) && $sinContar["cirugiaGrupo"] ){
                                                $sinContar["cirugiaGrupo"] = false;
                                                array_push($rowaxu['ser'], "cir" );
                                            }

                                            if( in_array($rowser1['Seeser'], $arr_servicios_urgencias_grupo ) && $sinContar["urgenciasGrupo"] ){
                                                $sinContar["urgenciasGrupo"] = false;
                                                array_push($rowaxu['ser'], "urg" );
                                            }
                                         //--> termina la validacion por grupos para el consolidado
                                         array_push( $rowaxu['ser'], $rowser1['Seeser'] );
                                      }

                                      if( !$tieneServicios ){
                                        array_push( $rowaxu['ser'], 'SS' );
                                      }

                                      if( !isset( $historiasXespecialidad[$row1['cod']] ) ){
                                          $historiasXespecialidad[$row1['cod']] = array();
                                      }

                                      $j++;
                                      if( !in_array( $row1['his']."-".$row1['ing'], $historiasXespecialidad[$row1['cod']] )){

                                        if( !$tieneServicios and $condicionCcoEsp == '' ){
                                            array_push( $dato['especialidades'], $rowaxu );
                                        }else{
                                            if( $tieneServicios ){
                                                array_push( $dato['especialidades'], $rowaxu );
                                            }
                                        }
                                        array_push( $historiasXespecialidad[$row1['cod']], $row1['his']."-".$row1['ing'] );
                                      }
                                      if( $row1['tipo']  == "S"){
                                           if( !$tieneServicios and $condicionCcoEsp == '' ){
                                              array_push( $dato['especialidadesSecundarios'], $rowaxu );
                                              $valorSecundariosGuardado ++;
                                            }else{
                                                if( $tieneServicios ){
                                                    array_push( $dato['especialidadesSecundarios'], $rowaxu );
                                                    $valorSecundariosGuardado ++;
                                                }
                                            }
                                      }else{
                                           if( !$tieneServicios and $condicionCcoEsp == '' ){
                                             array_push( $dato['especialidadesPrincipales'], $rowaxu );
                                           }else{
                                                if( $tieneServicios ){
                                                    array_push( $dato['especialidadesPrincipales'], $rowaxu );
                                                }
                                            }
                                      }
                                      $arr_datos[$row1['his']."-".$row1['ing']] = $dato;
                                  }else{
                                  }
                              }
                          }

                          //echo "<pre>".print_r($arr_datos, true)."</pre>";
                          //echo "<br> edb--> tamaño de historias: ".count( $arr_datos )."<br>";
                          $valorSecundariosGuardado = 0;
                          foreach ($arr_datos as $keyn=>$row ){
                            $historia = $keyn;
                            $arr_fec  = explode("-",$row['fecha_egreso']);
                            $anio     = $arr_fec[0];
                            $mes      = $arr_fec[1];

                            if( array_key_exists( $anio, $arr_anio_mes ) == false ){
                                $arr_anio_mes[ $anio ] = array();
                            }
                            if( in_array( $mes, $arr_anio_mes[ $anio ] ) == false ){
                                array_push($arr_anio_mes[ $anio ],$mes);
                            }

                            //------------------------------PARA ESPECIALIDADES---------------------------------
                            if( isset($row['especialidades']) ){
                                foreach( $row['especialidades'] as $especialidad ){
                                    if( array_key_exists($especialidad['cod'],$arr_especialidades) == false ){
                                        $arr_especialidades[$especialidad['cod']] = $especialidad['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $especialidad['cod'], $arr_especialidad_anio_mes ) == false ){
                                        $arr_especialidad_anio_mes[ $especialidad['cod'] ] = array();
                                        if( $mostrarDetalle == "on" ){
                                            $arr_det_especialidades[ $especialidad['cod'] ] = array();
                                        }
                                    }
                                    if( array_key_exists( $anio, $arr_especialidad_anio_mes[$especialidad['cod'] ] ) == false ){
                                        $arr_especialidad_anio_mes[ $especialidad['cod'] ][ $anio ] = array();
                                        if( $mostrarDetalle == "on" ){
                                            $arr_det_especialidades[ $especialidad['cod'] ][ $anio ] = array();
                                        }
                                    }
                                    if( array_key_exists( $mes, $arr_especialidad_anio_mes[ $especialidad['cod'] ][$anio] ) == false ){
                                        $arr_especialidad_anio_mes[ $especialidad['cod'] ][ $anio ][$mes] = array();
                                        if( $mostrarDetalle == "on" ){
                                            $arr_det_especialidades[ $especialidad['cod'] ][ $anio ][$mes] = array();
                                        }
                                        $arr_especialidad_anio_mes[ $especialidad['cod'] ][ $anio ][$mes]['valor'] = 1;
                                        $arr_especialidad_anio_mes[ $especialidad['cod'] ][ $anio ][$mes]['tipo'] = 'nn';
                                    }else{
                                        $arr_especialidad_anio_mes[ $especialidad['cod'] ][ $anio ][$mes]['valor']+= 1;
                                        $arr_especialidad_anio_mes[ $especialidad['cod'] ][ $anio ][$mes]['tipo'] = 'nn';
                                    }
                                    if( $mostrarDetalle == "on" ){
                                        if( !( isset( $arr_det_especialidades_total[$anio] ) ) ){
                                            $arr_det_especialidades_total[$anio] = array();
                                        }
                                        if( !( isset( $arr_det_especialidades_total[$anio][$mes] ) ) ){
                                            $arr_det_especialidades_total[$anio][$mes] = array();
                                        }
                                        if( !in_array($historia, $arr_det_especialidades_total[$anio][$mes]))
                                            array_push( $arr_det_especialidades_total[$anio][$mes], $historia);


                                        if( !isset($arr_det_especialidades[ $especialidad['cod'] ]) ){
                                            $arr_det_especialidades[ $especialidad['cod'] ] = array();
                                        }
                                        if( !isset($arr_det_especialidades[ $especialidad['cod'] ][$anio]) ){
                                            $arr_det_especialidades[ $especialidad['cod'] ][$anio] = array();
                                        }
                                        if( !isset($arr_det_especialidades[ $especialidad['cod'] ][$anio][$mes]) ){
                                            $arr_det_especialidades[ $especialidad['cod'] ][$anio][$mes] = array();
                                        }
                                        array_push( $arr_det_especialidades[ $especialidad['cod'] ][ $anio ][$mes], $historia );
                                    }
                                    //para trabajo con servicios
                                    foreach ($especialidad['ser'] as $keyi => $value) {
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        ( isset( $arr_especialidad_anio_mes[$especialidad['cod']][$anio][$mes]['servicios'][$value] ) ) ? $arr_especialidad_anio_mes[$especialidad['cod']][$anio][$mes]['servicios'][$value]++ : $arr_especialidad_anio_mes[$especialidad['cod']][$anio][$mes]['servicios'][$value] = 1;
                                    }
                                }
                            }
                            //------------------------------PARA ESPECIALIDADES PRINCIPALES---------------------------------
                            if( isset($row['especialidadesPrincipales']) ){
                                foreach( $row['especialidadesPrincipales'] as $especialidadP ){
                                    if( array_key_exists($especialidadP['cod'],$arr_especialidades) == false ){
                                        $arr_especialidades[$especialidadP['cod']] = $especialidadP['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $especialidadP['cod'], $arr_especialidadP_anio_mes ) == false ){
                                        $arr_especialidadP_anio_mes[ $especialidadP['cod'] ] = array();
                                    }
                                    if( array_key_exists( $anio, $arr_especialidadP_anio_mes[$especialidadP['cod'] ] ) == false ){
                                        $arr_especialidadP_anio_mes[ $especialidadP['cod'] ][ $anio ] = array();
                                    }
                                    if( array_key_exists( $mes, $arr_especialidadP_anio_mes[ $especialidadP['cod'] ][$anio] ) == false ){
                                        $arr_especialidadP_anio_mes[ $especialidadP['cod'] ][ $anio ][$mes] = array();
                                        $arr_especialidadP_anio_mes[ $especialidadP['cod'] ][ $anio ][$mes]['valor'] = 1;
                                        $arr_especialidadP_anio_mes[ $especialidadP['cod'] ][ $anio ][$mes]['tipo'] = 'P';
                                    }else{
                                        $arr_especialidadP_anio_mes[ $especialidadP['cod'] ][ $anio ][$mes]['valor'] += 1;
                                        $arr_especialidadP_anio_mes[ $especialidadP['cod'] ][ $anio ][$mes]['tipo'] = 'P';
                                    }
                                    //para trabajo con servicios
                                    foreach ($especialidadP['ser'] as $keyi => $value) {
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        ( isset( $arr_especialidadP_anio_mes[$especialidadP['cod']][$anio][$mes]['servicios'][$value] ) ) ? $arr_especialidadP_anio_mes[$especialidadP['cod']][$anio][$mes]['servicios'][$value]++ : $arr_especialidadP_anio_mes[$especialidadP['cod']][$anio][$mes]['servicios'][$value] = 1;
                                    }
                                }
                            }

                            //------------------------------PARA ESPECIALIDADES SECUNDARIAS---------------------------------
                            if( isset($row['especialidadesSecundarios']) ){
                                foreach( $row['especialidadesSecundarios'] as $especialidadS ){
                                    if( array_key_exists($especialidadS['cod'],$arr_especialidades) == false ){
                                        $arr_especialidades[$especialidadS['cod']] = $especialidadS['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $especialidadS['cod'], $arr_especialidadS_anio_mes ) == false ){
                                        $arr_especialidadS_anio_mes[ $especialidadS['cod'] ] = array();
                                    }
                                    if( array_key_exists( $anio, $arr_especialidadS_anio_mes[$especialidadS['cod'] ] ) == false ){
                                        $arr_especialidadS_anio_mes[ $especialidadS['cod'] ][ $anio ] = array();
                                    }
                                    if( array_key_exists( $mes, $arr_especialidadS_anio_mes[ $especialidadS['cod'] ][$anio] ) == false ){
                                        $arr_especialidadS_anio_mes[ $especialidadS['cod'] ][ $anio ][$mes] = array("valor"=>0, "tipo"=>"");
                                    }
                                    $arr_especialidadS_anio_mes[ $especialidadS['cod'] ][ $anio ][$mes]['valor']+= 1;
                                    $arr_especialidadS_anio_mes[ $especialidadS['cod'] ][ $anio ][$mes]['tipo'] = 'S';
                                    $valorSecundariosGuardado ++;
                                    foreach ($especialidadS['ser'] as $keyi => $value) {
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        ( isset( $arr_especialidadS_anio_mes[$especialidadS['cod']][$anio][$mes]['servicios'][$value] ) ) ? $arr_especialidadS_anio_mes[$especialidadS['cod']][$anio][$mes]['servicios'][$value]++ : $arr_especialidadS_anio_mes[$especialidadS['cod']][$anio][$mes]['servicios'][$value] = 1;
                                    }
                                }
                            }
                        }
                        if( isset( $wcriteriosConsulta['especialidad'] ) &&  $wcriteriosConsulta['especialidad']['tipo'] == "P" ){
                            unset( $arr_especialidad_anio_mes );
                            $arr_especialidad_anio_mes = $arr_especialidadP_anio_mes;
                        }
                        if( isset( $wcriteriosConsulta['especialidad'] ) && $wcriteriosConsulta['especialidad']['tipo'] == "S" ){
                            unset( $arr_especialidad_anio_mes );
                            $arr_especialidad_anio_mes = $arr_especialidadS_anio_mes;
                        }

                        $i = 0;
                    //--> FIN SEGMENTO ESPECIALIDADES
                        //echo "<br> EDB --> ESPECIALIDADES --> <pre>".print_r( $arr_det_especialidades, true )."</pre>";
                }

                if( count( $wcriteriosConsulta ) == 0 or isset( $wcriteriosConsulta['procedimientos']) ){
                    //--> SEGMENTO PROCEDIMIENTOS
                        //PROCEDIMIENTOS, TRAER PARA CADA REGISTRO
                        $historiasXprocedimiento = array();
                        ( $wcriteriosConsulta['procedimientos']['tipo'] == "" or !isset($wcriteriosConsulta['procedimientos']['tipo'] ) ) ? $condicionTipoProcedimiento = "" : $condicionTipoProcedimiento = " AND a.protip = '{$wcriteriosConsulta['procedimientos']['tipo']}'";
                        $q = "SELECT Egrhis as his, Egring as ing, b.Procod as cod, b.Pronom as nom, a.proser as ser, a.protip as tipo
                                FROM ".$tablaTmpEgreso." FORCE INDEX ( idxtemporalhising ), ".$wbasedato."_000110 a, ".$wbasedato."_000103 b
                               WHERE Egrhis=Prohis
                                 AND Egring=Proing
                                 AND a.Procod=b.Procod
                                 {$condicionTipoProcedimiento}
                                 {$condicionCcoPro}
                            GROUP BY Egrhis,Egring,a.Procod,4
                                ";
                        $res = mysql_query($q, $conex) or die( 'error '.mysql_error().' en el query ');
                        $num = mysql_num_rows($res);

                        if( $num > 0 ){

                            while($row1 = mysql_fetch_assoc($res)){

                                if( array_key_exists(  $row1['his']."-".$row1['ing'], $arr_datos ) ){
                                    $dato = $arr_datos[$row1['his']."-".$row1['ing']];
                                    if( array_key_exists( "procedimientos", $dato) == false ){
                                        $dato['procedimientos'] = array();
                                        $dato['procedimientosPrincipales'] = array();
                                        $dato['procedimientosSecundarios'] = array();
                                    }
                                    unset( $sinContar );
                                    $sinContar     = array("hospitalarios"=>true, "cirugiaGrupo"=>true, "urgenciasGrupo"=>true);//--> variable que garantiza que si es un grupo solo cuente una vez para todos los servicios pertenecientes al grupo
                                    $rowaxu=array();
                                    $rowaxu['cod'] = $row1['cod'];
                                    $rowaxu['nom'] = $row1['nom'];
                                    $rowaxu['ser'] = array();
                                    if( $row1['ser'] == '' ){
                                        array_push( $rowaxu['ser'], 'SS' );
                                    }else{
                                        array_push( $rowaxu['ser'], $row1['ser'] );
                                        //-->  acá se hace la nueva validación para contar por grupo y no por servicios buscar "//-->  acá se haría la nueva validación para contar por grupo y no por servicios" de ahí para abajo
                                            if( in_array($row1['ser'], $arr_servicios_hospitalarios ) && $sinContar["hospitalarios"] ){
                                                $sinContar["hospitalarios"] = false;
                                                array_push($rowaxu['ser'], "hos" );
                                            }
                                            if( in_array($row1['ser'], $arr_servicios_cirugia_grupo ) && $sinContar["cirugiaGrupo"] ){
                                                $sinContar["cirugiaGrupo"] = false;
                                                array_push($rowaxu['ser'], "cir" );
                                            }

                                            if( in_array($row1['ser'], $arr_servicios_urgencias_grupo ) && $sinContar["urgenciasGrupo"] ){
                                                $sinContar["urgenciasGrupo"] = false;
                                                array_push($rowaxu['ser'], "urg" );
                                            }
                                         //--> termina la validacion por grupos para el consolidado
                                    }
                                    if( !isset( $historiasXprocedimiento[$row1['cod']] ) ){
                                        $historiasXprocedimiento[$row1['cod']] = array();
                                    }
                                    array_push( $dato['procedimientos'], $rowaxu );
                                    if( $row1['tipo']  == "S"){
                                        array_push( $dato['procedimientosSecundarios'], $rowaxu );
                                    }else{
                                        array_push( $dato['procedimientosPrincipales'], $rowaxu );
                                        $procedimientosPnumero++;
                                    }
                                    if( !in_array( $row1['his']."-".$row1['ing'], $historiasXprocedimiento[$row1['cod']] )){
                                        $arr_datos[$row1['his']."-".$row1['ing']] = $dato;
                                        array_push( $historiasXprocedimiento[$row1['cod']], $row1['his']."-".$row1['ing'] );
                                    }
                                    $arr_datos[$row1['his']."-".$row1['ing']] = $dato;
                                }
                            }
                        }

                        $procedimientosPnumero = 0;
                        foreach ($arr_datos as $keyn=>$row ){

                            $arr_fec = explode("-",$row['fecha_egreso']);
                            $anio = $arr_fec[0];
                            $mes = $arr_fec[1];
                            $historia = $keyn;

                            if( array_key_exists( $anio, $arr_anio_mes ) == false ){
                                $arr_anio_mes[ $anio ] = array();
                            }
                            if( in_array( $mes, $arr_anio_mes[ $anio ] ) == false ){
                                array_push($arr_anio_mes[ $anio ],$mes);
                            }
                            //------------------------------PARA PROCEDIMIENTOS---------------------------------
                            if( isset($row['procedimientos']) ){
                                foreach( $row['procedimientos'] as $procedimiento ){
                                    if( array_key_exists($procedimiento['cod'],$arr_procedimientos) == false ){
                                        $arr_procedimientos[$procedimiento['cod']] = $procedimiento['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $procedimiento['cod'], $arr_procedimiento_anio_mes ) == false ){
                                        $arr_procedimiento_anio_mes[ $procedimiento['cod'] ] = array();
                                        if( $mostrarDetalle == "on" ){
                                            $arr_det_procedimientos[ $procedimiento['cod'] ] = array();
                                        }
                                    }
                                    if( array_key_exists( $anio, $arr_procedimiento_anio_mes[ $procedimiento['cod'] ] ) == false ){
                                        $arr_procedimiento_anio_mes[ $procedimiento['cod'] ][ $anio ] = array();
                                        if( $mostrarDetalle == "on" ){
                                            $arr_det_procedimientos[ $procedimiento['cod'] ][ $anio ] = array();
                                        }
                                    }
                                    if( array_key_exists( $mes, $arr_procedimiento_anio_mes[ $procedimiento['cod'] ][$anio] ) == false ){
                                        $arr_procedimiento_anio_mes[ $procedimiento['cod'] ][ $anio ][$mes]['valor'] = 1;
                                        if( $mostrarDetalle == "on" ){
                                            $arr_det_procedimientos[ $procedimiento['cod'] ][ $anio ][$mes] = array();
                                        }
                                    }else{
                                        $arr_procedimiento_anio_mes[ $procedimiento['cod'] ][ $anio ][$mes]['valor']+= 1;
                                    }
                                    if( $mostrarDetalle == "on" ){
                                        if( !( isset( $arr_det_procedimientos_total[$anio] ) ) ){
                                            $arr_det_procedimientos_total[$anio] = array();
                                        }
                                        if( !( isset( $arr_det_procedimientos_total[$anio][$mes] ) ) ){
                                            $arr_det_procedimientos_total[$anio][$mes] = array();
                                        }
                                        if( !in_array($historia, $arr_det_procedimientos_total[$anio][$mes]))
                                            array_push( $arr_det_procedimientos_total[$anio][$mes], $historia);

                                        if( !isset($arr_det_procedimientos[ $procedimiento['cod'] ]) ){
                                            $arr_det_procedimientos[ $procedimiento['cod'] ] = array();
                                        }
                                        if( !isset($arr_det_procedimientos[ $procedimiento['cod'] ][$anio]) ){
                                            $arr_det_procedimientos[ $procedimiento['cod'] ][$anio] = array();
                                        }
                                        if( !isset($arr_det_procedimientos[ $procedimiento['cod'] ][$anio][$mes]) ){
                                            $arr_det_procedimientos[ $procedimiento['cod'] ][$anio][$mes] = array();
                                        }

                                        array_push( $arr_det_procedimientos[ $procedimiento['cod'] ][ $anio ][$mes], $historia );
                                    }
                                    //para trabajo con servicios
                                    foreach ($procedimiento['ser'] as $keyi => $value) {
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        ( isset( $arr_procedimiento_anio_mes[$procedimiento['cod']][$anio][$mes]['servicios'][$value] ) ) ? $arr_procedimiento_anio_mes[$procedimiento['cod']][$anio][$mes]['servicios'][$value]++ : $arr_procedimiento_anio_mes[$procedimiento['cod']][$anio][$mes]['servicios'][$value] = 1;
                                    }
                                }
                            }

                            //------------------------------PARA PROCEDIMIENTOS PRINCIPALES---------------------------------
                            if( isset($row['procedimientosPrincipales']) ){
                                foreach( $row['procedimientosPrincipales'] as $procedimientoP ){
                                    if( array_key_exists($procedimientoP['cod'],$arr_procedimientos) == false ){
                                        $arr_procedimientos[$procedimientoP['cod']] = $procedimientoP['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $procedimientoP['cod'], $arr_procedimientoP_anio_mes ) == false ){
                                        $arr_procedimientoP_anio_mes[ $procedimientoP['cod'] ] = array();
                                    }
                                    if( array_key_exists( $anio, $arr_procedimientoP_anio_mes[$procedimientoP['cod'] ] ) == false ){
                                        $arr_procedimientoP_anio_mes[ $procedimientoP['cod'] ][ $anio ] = array();
                                    }
                                    if( array_key_exists( $mes, $arr_procedimientoP_anio_mes[ $procedimientoP['cod'] ][$anio] ) == false ){
                                        $arr_procedimientoP_anio_mes[ $procedimientoP['cod'] ][ $anio ][$mes]['valor'] = 1;
                                    }else{
                                        $arr_procedimientoP_anio_mes[ $procedimientoP['cod'] ][ $anio ][$mes]['valor']+= 1;
                                    }
                                    $procedimientosPnumero++;
                                    //para trabajo con servicios
                                    /*$querypr = "INSERT INTO `prueba235_2` (`anho`, `mes`, `Codigo`, `valor`) VALUES ('$keyn', '1', '', '')";
                                    $rspr    = mysql_query( $querypr, $conex );*/
                                    //para trabajo con servicios
                                    foreach ($procedimientoP['ser'] as $keyiP => $valueP) {
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        ( isset( $arr_procedimientoP_anio_mes[$procedimientoP['cod']][$anio][$mes]['servicios'][$valueP] ) ) ? $arr_procedimientoP_anio_mes[$procedimientoP['cod']][$anio][$mes]['servicios'][$valueP]++ : $arr_procedimientoP_anio_mes[$procedimientoP['cod']][$anio][$mes]['servicios'][$valueP] = 1;
                                    }
                                }
                            }
                            //------------------------------PARA PROCEDIMIENTOS SECUNDARIAS---------------------------------
                            if( isset($row['procedimientosSecundarios']) ){
                                foreach( $row['procedimientosSecundarios'] as $procedimientoS ){
                                    if( array_key_exists($procedimientoS['cod'],$arr_procedimientos) == false ){
                                        $arr_procedimientos[$procedimientoS['cod']] = $procedimientoS['nom'];
                                    }
                                    //------------------------------
                                    if( array_key_exists( $procedimientoS['cod'], $arr_procedimientoS_anio_mes ) == false ){
                                        $arr_procedimientoS_anio_mes[ $procedimientoS['cod'] ] = array();
                                    }
                                    if( array_key_exists( $anio, $arr_procedimientoS_anio_mes[$procedimientoS['cod'] ] ) == false ){
                                        $arr_procedimientoS_anio_mes[ $procedimientoS['cod'] ][ $anio ] = array();
                                    }
                                    if( array_key_exists( $mes, $arr_procedimientoS_anio_mes[ $procedimientoS['cod'] ][$anio] ) == false ){
                                        $arr_procedimientoS_anio_mes[ $procedimientoS['cod'] ][ $anio ][$mes]['valor'] = 1;
                                    }else{
                                        $arr_procedimientoS_anio_mes[ $procedimientoS['cod'] ][ $anio ][$mes]['valor']+= 1;
                                    }
                                    //para trabajo con servicios
                                    foreach ($procedimientoS['ser'] as $keyiP => $valueS) {
                                        //                                      entidad                   servi                                      entidad                   servi                                        entidad                   servi
                                        ( isset( $arr_procedimientoS_anio_mes[$procedimientoS['cod']][$anio][$mes]['servicios'][$valueS] ) ) ? $arr_procedimientoS_anio_mes[$procedimientoS['cod']][$anio][$mes]['servicios'][$valueS]++ : $arr_procedimientoS_anio_mes[$procedimientoS['cod']][$anio][$mes]['servicios'][$valueS] = 1;
                                    }
                                }
                            }
                        }
                        if( isset( $wcriteriosConsulta['procedimientos'] ) &&  $wcriteriosConsulta['procedimientos']['tipo'] == "P" ){
                            unset( $arr_procedimientos_anio_mes );
                            $arr_procedimientos_anio_mes = $arr_procedimientoP_anio_mes;
                        }
                        if( isset( $wcriteriosConsulta['procedimientos'] ) && $wcriteriosConsulta['procedimientos']['tipo'] == "S" ){
                            unset( $arr_procedimientos_anio_mes );
                            $arr_procedimientos_anio_mes = $arr_procedimientoS_anio_mes;
                        }
                    //--> FIN SEGMENTO PROCEDIMIENTOS
                        //echo "<br> EDB --> PROCEDIMIENTOS --> <pre>".print_r( $arr_det_procedimientos, true )."</pre>";
                }

                if( count( $wcriteriosConsulta ) == 0 or isset( $wcriteriosConsulta['procedencia']) ){
                    //--> SEGMENTO PROCEDENCIA
                        //si wgrupo esta definido es ccourg,ccocir o ccohos. Obtengo los 3 ultimos caracteres

                        if( $wtipoGrupo == "cco" )
                            $wgrupo = "ccocir";
                        if( isset($wgrupo) && $wgrupo != "" ){
                            $wgrupo = substr($wgrupo, -3);
                        }

                        foreach ($arr_datos as $keyn=>$row ){
                            if( isset($arr_barrios[ $row['barrio'] ]) == false ) $row['barrio'] = "99";
                            $arr_fec  = explode("-",$row['fecha_egreso']);
                            $anio     = $arr_fec[0];
                            $mes      = $arr_fec[1];
                            $historia = $keyn;

                            if( array_key_exists( $anio, $arr_anio_mes ) == false ){
                                $arr_anio_mes[ $anio ] = array();
                            }
                            if( in_array( $mes, $arr_anio_mes[ $anio ] ) == false ){
                                array_push($arr_anio_mes[ $anio ],$mes);
                            }

                            //------------------------------PARA PROCEDENCIA---------------------------------
                            if( array_key_exists( $row['barrio'], $arr_procedencia_anio_mes ) == false ){
                                $arr_procedencia_anio_mes[ $row['barrio'] ] = array();
                                if( $mostrarDetalle ==  "on" ){
                                    $arr_det_procedencia[ $row['barrio'] ] = array();
                                }
                            }
                            if( array_key_exists( $anio, $arr_procedencia_anio_mes[ $row['barrio'] ] ) == false ){
                                $arr_procedencia_anio_mes[ $row['barrio'] ][ $anio ] = array();
                                //$arr_procedencia_anio_mes[ $row['barrio'] ][ $anio ][$mes] = array();
                                if( $mostrarDetalle ==  "on" ){
                                    $arr_det_procedencia[ $row['barrio'] ][ $anio ] = array();
                                }
                            }
                            if( array_key_exists( $mes, $arr_procedencia_anio_mes[ $row['barrio'] ][$anio] ) == false ){
                                $arr_procedencia_anio_mes[ $row['barrio'] ][ $anio ][$mes] = array();
                                if( $mostrarDetalle ==  "on" ){
                                    $arr_det_procedencia[ $row['barrio'] ][ $anio ][$mes] = array();
                                }
                                $arr_procedencia_anio_mes[ $row['barrio'] ][ $anio ][$mes]['valor'] = 1;
                            }else{
                                $arr_procedencia_anio_mes[ $row['barrio'] ][ $anio ][$mes]['valor']+= 1;
                            }
                            if( $mostrarDetalle == "on"){
                                if( !( isset( $arr_det_procedencia_total[$anio] ) ) ){
                                    $arr_det_procedencia_total[$anio] = array();
                                }
                                if( !( isset( $arr_det_procedencia_total[$anio][$mes] ) ) ){
                                    $arr_det_procedencia_total[$anio][$mes] = array();
                                }
                                if( !in_array($historia, $arr_det_procedencia_total[$anio][$mes]))
                                    array_push( $arr_det_procedencia_total[$anio][$mes], $historia);

                                if( !isset($arr_det_procedencia[ $row['barrio'] ]) ){
                                    $arr_det_procedencia[ $row['barrio'] ] = array();
                                }
                                if( !isset($arr_det_procedencia[ $row['barrio'] ][$anio]) ){
                                    $arr_det_procedencia[ $row['barrio'] ][$anio] = array();
                                }
                                if( !isset($arr_det_procedencia[ $row['barrio'] ][$anio][$mes]) ){
                                    $arr_det_procedencia[ $row['barrio'] ][$anio][$mes] = array();
                                }

                                array_push( $arr_det_procedencia[ $row['barrio'] ][ $anio ][$mes], $historia );
                            }
                        }
                    //--> FIN SEGMENTO PROCEDENCIA
                        //echo "<br> EDB --> PROCEDENCIA --> <pre>".print_r( $arr_det_procedencia, true )."</pre>";
                }
                $total_egresos += count( $arr_datos );
            }
        }

        //--> fin punto de control de nuevas consultas

        ( $wtipoProcedimiento == "" ) ? $condicionTipoProcedimiento = "" : $condicionTipoProcedimiento = " AND Protip = '{$wtipoProcedimiento}'";
        ( $wtipoProcedimiento == "" ) ? $tabla110 = "" : $tabla110 = ", ".$wbasedato."_000110";
        ( $wtipoProcedimiento == "" ) ? $join110 = "" : $join110 = " AND Egrhis=Prohis AND Egring=Proing";

        if( $numPpal == 0 || $total_egresos == 0 ){
            echo "No hay datos con los parámetros ingresados";
            return;
        }

        /*************Creando un mensaje del rango de meses utilizado************/
        $mensajefuente = "";
        $prim_anio = ""; $prim_mes = ""; $ult_anio=""; $ult_mes = ""; $ianio=0; $imes=0;
        foreach( $arr_anio_mes as $anios => $meses ){
            if($ianio==0) $prim_anio=$anios;
            $ult_anio=$anios;
            foreach($meses as $mes){
                if($imes==0) $prim_mes=$arr_meses[$mes];
                $ult_mes=$arr_meses[$mes];
                $imes++;
            }
            $ianio++;
        }
        if( count( $arr_anio_mes ) > 1 ){
            $mensajefuente = $prim_mes." de ".$prim_anio." a ".$ult_mes." de ".$ult_anio;
        }else{
            $mensajefuente = $prim_mes." a ".$ult_mes." de ".$ult_anio;
            if( $prim_mes == $ult_mes )
                $mensajefuente = $prim_mes." de ".$ult_anio;
        }
        /*************FIN Creando un mensaje del rango de meses utilizado************/
        if( $mostrarDetalle == "on" and ( count($arr_entidad_anio_mes) > 0 ) and ( count( $wcriteriosConsulta ) == 0 or isset($wcriteriosConsulta['entidad']) ) ){
            echo "<input type='hidden' id='mostrarDetalleFaltantes' value='on'>";
        }else{
            echo "<input type='hidden' id='mostrarDetalleFaltantes' value='off'>";
        }
        echo "<div id='mensaje_fuente' style='display:none'>".$mensajefuente."</div>";
        $titulo = "Reporte de Pacientes";

        if( isset($wgrupo) && $wgrupo != "" ){
            if( $wgrupo == "cir" )
                $titulo.= " de Cirugia";
            if( $wgrupo == "hos" )
                $titulo.= " de Hospitalizacion";
            if( $wgrupo == "urg" )
                $titulo.= " de Urgencias";
        }

        echo "<center><span class='subtituloPagina2'>".$titulo." ".$mensajefuente."</span></center><br><br>";


        echo "<div style='width: 1000px;'>";

        $arr_suma_filas = array();
        $arr_suma_columnas = array();
        $i=0;

        foreach( $arr_anio_mes as $anios => $meses ){
            foreach($meses as $mes){
                $arr_suma_columnas[$i] = 0;
                $i++;
            }
        }

        if( ( count($arr_entidad_anio_mes) > 0 ) and ( count( $wcriteriosConsulta ) == 0 or isset($wcriteriosConsulta['entidad']) ) ){
            /*******************************************
            ***************ENTIDADES********************
            *******************************************/
            if( $wtipoProcedimiento == "" and $wtipoEspecialidad == "" ){
                //$tabla_oculta_entidades = "<table border=1 id='tabla_grafico_entidades' style='display:none;'>";
                //Esta tabla oculta es el codigo html que tiene una columna con el tipo de entidad y otra con el total de egresos
                $ordenados = array();
                $ordenadosTipo = array();
                foreach( $arr_entidad_anio_mes as $tipoentidadkey => $datostipoentidad ){
                    $total_tipo_entidad = 0;
                    $ordenadosTipo[$tipoentidadkey] = 0;
                    foreach( $datostipoentidad['entidades'] as $entidadkey => $datos ){

                        $ordenados[$entidadkey] = 0;
                        foreach( $arr_anio_mes as $anios => $meses ){
                            foreach($meses as $mes){
                                if( isset($datos[$anios][$mes] ) ){
                                    $ordenados[$entidadkey]+=$datos[$anios][$mes]['valor'];
                                    if( $detMesesConsultados[$anios][$mes]['guardarHistorico']  ){
                                        ( $tipoentidadkey == '02' ) ? $personaNatural = 'P' : $personaNatural = '';
                                        guardarRegistroHistorico($anios, $mes, $wgrupoOri, "ENTIDAD", $entidadkey, $datos[$anios][$mes]['valor'], $personaNatural);
                                        if( isset( $arr_entidad_anio_mes_serv[$entidadkey][$anios][$mes] ) ){
                                            foreach( $arr_entidad_anio_mes_serv[$entidadkey][$anios][$mes] as $keyServicio=>$valueXser ){
                                                guardarRegistroHistorico($anios, $mes, $keyServicio, "ENTIDAD", $entidadkey, $valueXser);
                                            }
                                        }else{
                                            //echo "<br> codigo entidad sin datos $entidadkey [$anios][$mes] <br>";
                                        }
                                    }
                                }
                            }
                        }
                        $total_tipo_entidad +=$ordenados[$entidadkey];
                        $ordenadosTipo[$tipoentidadkey] = $total_tipo_entidad;
                    }
                    //$tabla_oculta_entidades.=  "<td>".$total_tipo_entidad."</td>";
                    //$tabla_oculta_entidades.=  "</tr>";
                }
                //$tabla_oculta_entidades.= "</table>";
                arsort($ordenados);
                arsort($ordenadosTipo);


                $tabla_oculta_entidades = "<table border=1 id='tabla_grafico_entidades' style='display:none;'>";
                $cant_ents = 0;
                $suma_menores=0;
                foreach( $ordenadosTipo as $tipoEntikeyy => $cantidadd ){
                    if( $cant_ents > $topeGraficos  ){
                        $suma_menores+= $cantidadd;
                    }else{
                        $ent="";
                        ( isset( $arr_entidad_anio_mes[ $tipoEntikeyy ] ) )? $ent=$arr_entidad_anio_mes[ $tipoEntikeyy ]['nombre'] : $ent=$tipoEntikeyy;
                        $tabla_oculta_entidades.=  "<tr>";
                        $tabla_oculta_entidades.=  "<td>".$ent."</td>";
                        $tabla_oculta_entidades.=  "<td>".$cantidadd."</td>";
                        $tabla_oculta_entidades.=  "</tr>";
                        $cant_ents++;
                    }
                }
                if( $suma_menores > 0 ){
                    $tabla_oculta_entidades.=  "<tr>";
                    $tabla_oculta_entidades.=  "<td>OTROS</td>";
                    $tabla_oculta_entidades.=  "<td>".$suma_menores."</td>";
                    $tabla_oculta_entidades.=  "</tr>";
                }
                $tabla_oculta_entidades.=  "</table>";
                echo $tabla_oculta_entidades;





                //echo $tabla_oculta_entidades;

                echo "<div class='desplegables' style='width:100%;'>";
                echo "<h3><b>* ENTIDADES *</b></h3>";
                echo "<div>";
                echo '<img width="20" height="19" style="float:left; cursor:pointer;" onclick="dibujarGraficoEntidades()" src="../../images/medical/root/chart.png">';
                echo "<table class='tabla_r' id='tabla_entidades' align='center' width='85%'>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>&nbsp;</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    echo "<td align='center' colspan=".(count($meses) + 4 ).">".$anios."</td>";
                }
                echo "</tr>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>Entidad</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        echo "<td align='center'>".$arr_meses[$mes]."</td>";
                    }
                    echo "<td align='center'>Acum.</td>";
                    echo "<td align='center'>%</td>";
                    echo "<td align='center'>% Variac.</td>";
                    echo "<td align='center'>Promedio<br>Mes</td>";
                }
                echo "</tr>";


                $arr_suma_columnas_total = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                $i = 0;
                foreach( $arr_entidad_anio_mes as $tipoentidadkey => $datostipoentidad ){

                    if( $tipoentidadkey == '02' || $tipoentidadkey == '99' ) continue; //Las particulares y las que no tienen informacion no se muestran individual

                    $arr_suma_columnas_tipo_entidad = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                    //foreach( $datostipoentidad['entidades'] as $entidadkey => $datos ){
                    foreach( $ordenados as $entidadkey => $cantidad ){
                        if( isset(  $datostipoentidad['entidades'][$entidadkey] ) == false )
                            continue;
                        $datos = $datostipoentidad['entidades'][$entidadkey];
                        echo "<tr class='fila1'>";
                        $ent="";
                        ( isset( $arr_entidades[ $entidadkey ] ) )? $ent=$entidadkey." ".$arr_entidades[ $entidadkey ] : $ent=$entidadkey;
                        echo "<td>".$ent."</td>";
                        $j=0;
                        $divDetalles = "";
                        foreach( $arr_anio_mes as $anios => $meses ){
                            $total_ent_anio = 0;
                            $ult_mes=0;
                            $penu_mes=0;
                            foreach($meses as $mes){
                                $penu_mes=$ult_mes;
                                if( isset($datos[$anios][$mes] ) ){
                                    if( $mostrarDetalle == "on" ){
                                        $evento = " mostrando='off' onclick='mostrarDetalle(this, \"entidad\", \"$entidadkey\", \"$anios\", \"$mes\")' ";
                                    }else{
                                        $evento = "";
                                    }
                                    echo "<td align='center' style='cursor:pointer;' {$evento}>".$datos[$anios][$mes]['valor']."</td>";
                                    $total_ent_anio+=$datos[$anios][$mes]['valor'];
                                    $arr_suma_columnas_tipo_entidad[$j]+=$datos[$anios][$mes]['valor'];
                                    $ult_mes=$datos[$anios][$mes]['valor'];
                                }else{
                                    echo "<td align='center'>0</td>";
                                    $ult_mes=0;
                                }
                                $j++;
                            }
                            echo "<td align='center' class='fila2'>".$total_ent_anio."</td>";

                            $porc = ($total_ent_anio*100)/$total_egresos;
                            echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                            if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                            if( $penu_mes == 0 ) $penu_mes=1;
                            $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                            echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                            $pormes = $total_ent_anio / count($meses);
                            echo "<td align='center'>".number_format($pormes,2,',','.')."</td>";

                            if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "entidad", $entidadkey, $anio, $mes, $arr_det_entidades[$entidadkey][$anio][$mes], $arr_det_pacientes );
                        }
                        echo "</tr>";
                        if( $mostrarDetalle == "on" ){
                            echo "<tr id='tr_det_entidad_{$entidadkey}' style='background-color:white;display:none;'>";
                                echo "<td colspan='6'>{$divDetalles}</td>";
                            echo "</tr>";
                        }
                    }
                    //IMPRIMIR EL SUBTOTAL DEL TIPO DE ENTIDAD
                    echo "<tr class='encabezadotabla'>";
                    echo "<td align='center'>".$datostipoentidad['nombre']."</td>";
                    $j=0;
                    foreach( $arr_anio_mes as $anios => $meses ){
                        $total_ent_anio = 0;
                        $ult_mes=0;
                        $penu_mes=0;
                        foreach($meses as $mes){
                            $sumdat = $arr_suma_columnas_tipo_entidad[$j];
                            $penu_mes=$ult_mes;
                            $ult_mes=$sumdat;
                            echo "<td align='center'>".$sumdat."</td>";
                            $total_ent_anio+= $sumdat;
                            $arr_suma_columnas_total[$j]+=$sumdat; //El acumulado de todas los egresos del mes
                            $j++;
                        }
                        echo "<td align='center'>".$total_ent_anio."</td>";
                        $porc = ($total_ent_anio*100)/$total_egresos;
                        echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                        if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                        if( $penu_mes == 0 ) $penu_mes=1;
                        $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                        echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                        $pormes = $total_ent_anio / count($meses);
                        echo "<td align='center'>".number_format($pormes,2,',','.')."</td>";
                    }
                    echo "</tr>";
                }

                //IMPRIMIR LOS DATOS ACUMULADOS DE LOS PARTICULARES Y DE LAS ENTIDADES QUE NO SE TIENEN DATOS
                $arr_filas_extras = array("99","02");
                foreach( $arr_filas_extras as $valor_fe ){
                    $arr_suma_columnas_tipo_entidad = $arr_suma_columnas;
                    if( array_key_exists($valor_fe, $arr_entidad_anio_mes) ){
                            foreach( $arr_entidad_anio_mes[$valor_fe]['entidades'] as $entidadkey => $datos ){
                                $j=0;
                                foreach( $arr_anio_mes as $anios => $meses ){
                                    foreach($meses as $mes){
                                        if( isset($datos[$anios][$mes] ) ){
                                            $arr_suma_columnas_tipo_entidad[$j]+=$datos[$anios][$mes]['valor'];
                                        }
                                        $j++;
                                    }
                                }
                            }
                            echo "<tr class='encabezadotabla'>";
                            echo "<td align='center'>".$arr_entidad_anio_mes[$valor_fe]['nombre']."</td>";
                            $j=0;
                            foreach( $arr_anio_mes as $anios => $meses ){
                                $total_ent_anio = 0;
                                $ult_mes=0;
                                $penu_mes=0;
                                foreach($meses as $mes){
                                    $sumdat = $arr_suma_columnas_tipo_entidad[$j];
                                    $penu_mes=$ult_mes;
                                    $ult_mes=$sumdat;
                                    echo "<td align='center'>".$sumdat."</td>";
                                    $total_ent_anio+= $sumdat;
                                    $arr_suma_columnas_total[$j]+=$sumdat; //El acumulado de todas los egresos del mes
                                    $j++;
                                }
                                echo "<td align='center'>".$total_ent_anio."</td>";
                                $porc = ($total_ent_anio*100)/$total_egresos;
                                echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                                if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                                if( $penu_mes == 0 ) $penu_mes=1;
                                $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                                echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                                $pormes = $total_ent_anio / count($meses);
                                echo "<td align='center'>".number_format($pormes,2,',','.')."</td>";
                            }
                            echo "</tr>";
                    }
                }

                //IMPRIMIR LA FILA CON LOS TOTALES
                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>TOTAL</td>";
                $j=0;
                $divDetalles = "";
                foreach( $arr_anio_mes as $anios => $meses ){
                    $total_ent_anio = 0;
                    $ult_mes=0;
                    $penu_mes=0;
                    foreach($meses as $mes){
                        if( $mostrarDetalle == "on" ){
                            $evento = " mostrando='off' onclick='mostrarDetalle(this, \"entidad\", \"total\", \"$anios\", \"$mes\")' ";
                        }else{
                            $evento = "";
                        }
                        $sumdat = $arr_suma_columnas_total[$j];
                        $penu_mes=$ult_mes;
                        $ult_mes=$sumdat;
                        echo "<td align='center' style='cursor:pointer;' $evento>".$sumdat."</td>";
                        $total_ent_anio+= $sumdat;
                        $j++;
                        if( $mostrarDetalle == "on" ){
                                $divDetalles .= construirDivDetalle( "entidad", "total", $anios, $mes, $arr_det_entidades_total[$anios][$mes], $arr_det_pacientes );
                        }
                    }
                    echo "<td align='center'>".$total_ent_anio."</td>";
                    $porc = ($total_ent_anio*100)/$total_egresos;
                    echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                    if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                    if( $penu_mes == 0 ) $penu_mes=1;
                    $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                    echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                    $pormes = $total_ent_anio / count($meses);
                    echo "<td align='center'>".number_format((double)$pormes,2,',','.')."</td>";
                }
                echo "</tr>";
                if( $mostrarDetalle == "on" ){

                    echo "<tr id='tr_det_entidad_total' style='background-color:white;display:none;'>";
                        echo "<td colspan='6'>{$divDetalles}</td>";
                    echo "</tr>";
                }

                echo "</table>";
                echo "<center>
                <div id='contenedor_graf_entidad' style=' display:none;'>
                <div id='div_grafica_entidades' style='border: 1px solid #999999; width:600px; height:450px;'>
                </div>
                <span></span>
                <input type='button' value='Ocultar' onclick='ocultarDiv(\"contenedor_graf_entidad\")' />
                </div>
                </center>";
                echo "</div>";
                echo "</div>";
            }

            /*******************************************
            ***************FIN ENTIDADES********************
            *******************************************/
        }


        /*******************************************
        ***************DIAGNOSTICOS*****************
        *******************************************/
        if( true ){//ACA SE GUARDAN Y SE PINTA EL INFORME PARA LOS DIAGNÓSTICOS

            foreach( $arr_diagnosticoP_anio_mes as $diagnosticokeyP => $datosP ){

                $ent="";
                ( isset( $arr_diagnosticos[ $diagnosticokeyP ] ) )? $ent=$arr_diagnosticos[ $diagnosticokeyP ] : $ent=$diagnosticokeyP;

                foreach( $arr_anio_mes as $aniosP => $mesesP ){
                    foreach($mesesP as $mesP){
                        if( isset($datosP[$aniosP][$mesP] ) ){
                            if( $detMesesConsultados[$aniosP][$mesP]['guardarHistorico'] ){
                                guardarRegistroHistorico($aniosP, $mesP, $wgrupoOri, "DIAGNOSTICO", $diagnosticokeyP, $datosP[$aniosP][$mesP]['valor'], 'P');
                                if( !isset( $datosP[$aniosP][$mesP]['servicios'] ) ){
                                    $datosP[$aniosP][$mesP]['servicios'] = array();
                                }
                                foreach ($datosP[$aniosP][$mesP]['servicios'] as $keySerP => $valueserP ) {
                                    guardarRegistroHistorico($aniosP, $mesP, $keySerP, "DIAGNOSTICO", $diagnosticokeyP, $valueserP, 'P');
                                }
                            }
                        }
                    }
                }
            }

            foreach( $arr_diagnosticoS_anio_mes as $diagnosticokeyS => $datosS ){

                $ent="";
                ( isset( $arr_diagnosticos[ $diagnosticokeyS ] ) )? $ent=$arr_diagnosticos[ $diagnosticokeyS ] : $ent=$diagnosticokeyS;
                //$total_tipo_diagnostico = 0;

                //$ordenados[$diagnosticokeyS] = 0;
                foreach( $arr_anio_mes as $aniosS => $mesesS ){
                    foreach($mesesS as $mesS){

                        if( isset($datosS[$aniosS][$mesS] ) ){
                            $ordenados[$diagnosticokeyS]+=$datosS[$aniosS][$mesS]['valor'];
                            if(  $detMesesConsultados[$aniosS][$mesS]['guardarHistorico'] ){
                                guardarRegistroHistorico($aniosS, $mesS, $wgrupoOri, "DIAGNOSTICO", $diagnosticokeyS, $datosS[$aniosS][$mesS]['valor'], 'S');
                                if( !isset( $datosS[$aniosS][$mesS]['servicios'] ) ){
                                    $datosS[$aniosS][$mesS]['servicios'] = array();
                                }
                                foreach ($datosS[$aniosS][$mesS]['servicios'] as $keySerS => $valueserS ) {
                                    guardarRegistroHistorico($aniosS, $mesS, $keySerS, "DIAGNOSTICO", $diagnosticokeyS, $valueserS, 'S');
                                }
                            }
                        }
                    }
                }
            }

            //
            //---> final tipos de diagnosticos
            //
            //$tabla_oculta_diagnosticos = "<table border=1 id='tabla_grafico_diagnosticos' style='display:none;'>";
            //Esta tabla oculta es el codigo html que tiene una columna con el tipo de entidad y otra con el total de egresos
            /*PARA ORDENAR LOS REGISTROS DE MAYOR SUMA ANUAL A MENOR SUMA ANUAL*/
            $ordenados = array();
            foreach( $arr_diagnostico_anio_mes as $diagnosticokey => $datos ){
                $ent="";
                ( isset( $arr_diagnosticos[ $diagnosticokey ] ) )? $ent=$arr_diagnosticos[ $diagnosticokey ] : $ent=$diagnosticokey;
                //$tabla_oculta_diagnosticos.=  "<tr>";
                //$tabla_oculta_diagnosticos.=  "<td>".$ent."</td>";
                $total_tipo_diagnostico = 0;

                $ordenados[$diagnosticokey] = 0;
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        if( isset($datos[$anios][$mes] ) ){
                            $ordenados[$diagnosticokey]+=$datos[$anios][$mes]['valor'];
                            if( $detMesesConsultados[$anios][$mes]['guardarHistorico'] ){
                                guardarRegistroHistorico($anios, $mes, $wgrupoOri, "DIAGNOSTICO", $diagnosticokey, $datos[$anios][$mes]['valor']);
                                if( !isset( $datos[$anios][$mes]['servicios'] ) ){
                                    $datos[$anios][$mes]['servicios'] = array();
                                }
                                if( isset( $datos[$anios][$mes]['servicios']) and count( $datos[$anios][$mes]['servicios']) > 0 ){
                                    foreach ($datos[$anios][$mes]['servicios'] as $keySer => $valueser ) {
                                        guardarRegistroHistorico($anios, $mes, $keySer, "DIAGNOSTICO", $diagnosticokey, $valueser);
                                    }
                                }
                            }
                        }
                    }
                    $total_tipo_diagnostico+=$ordenados[$diagnosticokey];
                }
            }

            if( count( $arr_diagnostico_anio_mes) > 0 ){
                arsort($ordenados);
                $tabla_oculta_diagnosticos = "<table border=1 id='tabla_grafico_diagnosticos' style='display:none;'>";
                $cant_ents = 0;
                $suma_menores=0;
                foreach( $ordenados as $diagnosticokeyy => $cantidadd ){
                    if( $cant_ents >= $topeGraficos ){
                        $suma_menores+= $cantidadd;
                    }else{
                        $ent="";
                        ( isset( $arr_diagnosticos[ $diagnosticokeyy ] ) )? $ent=$arr_diagnosticos[ $diagnosticokeyy ] : $ent=$diagnosticokeyy;
                        $tabla_oculta_diagnosticos.=  "<tr>";
                        $tabla_oculta_diagnosticos.=  "<td>".$ent."</td>";
                        $tabla_oculta_diagnosticos.=  "<td>".$cantidadd."</td>";
                        $tabla_oculta_diagnosticos.=  "</tr>";
                        $cant_ents++;
                    }
                }
                if( $suma_menores > 0 ){
                    $tabla_oculta_diagnosticos.=  "<tr>";
                    $tabla_oculta_diagnosticos.=  "<td>OTROS</td>";
                    $tabla_oculta_diagnosticos.=  "<td>".$suma_menores."</td>";
                    $tabla_oculta_diagnosticos.=  "</tr>";
                }
                $tabla_oculta_diagnosticos.=  "</table>";
                echo $tabla_oculta_diagnosticos;

                echo "<div class='desplegables' style='width:100%;'>";
                echo "<h3><b>* DIAGNOSTICOS *</b></h3>";
                echo "<div>";
                echo '<img width="20" height="19" style="float:left; cursor:pointer;" onclick="dibujarGraficodiagnosticos()" src="../../images/medical/root/chart.png">';
                echo "<table class='tabla_r' id='tabla_diagnosticos' align='center' width='85%'>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>&nbsp;</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    echo "<td align='center' colspan=".(count($meses) + 4 ).">".$anios."</td>";
                }
                echo "</tr>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>Diagnostico</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        echo "<td align='center'>".$arr_meses[$mes]."</td>";
                    }
                    echo "<td align='center'>Acum.</td>";
                    echo "<td align='center'>%</td>";
                    echo "<td align='center'>% Variac.</td>";
                    echo "<td align='center'>Promedio<br>Mes</td>";
                }
                echo "</tr>";

                $arr_suma_columnas_total = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                $arr_suma_columnas_tipo_entidad = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                //foreach( $arr_diagnostico_anio_mes as $diagnosticokey => $datos ){
                foreach( $ordenados as $diagnosticokey => $cantidad ){
                    $datos = $arr_diagnostico_anio_mes[$diagnosticokey];
                    echo "<tr class='fila1'>";
                    $ent="";
                    ( isset( $arr_diagnosticos[ $diagnosticokey ] ) )? $ent=$diagnosticokey." ".$arr_diagnosticos[ $diagnosticokey ] : $ent=$diagnosticokey;
                    echo "<td>".$ent."</td>";
                    $j=0;
                    $divDetalles = "";
                    foreach( $arr_anio_mes as $anios => $meses ){
                        $total_ent_anio = 0;
                        $ult_mes=0;
                        $penu_mes=0;
                        foreach($meses as $mes){
                            $penu_mes=$ult_mes;
                            if( isset($datos[$anios][$mes] ) ){
                                if( $mostrarDetalle == "on" ){
                                    $evento = " mostrando='off' onclick='mostrarDetalle(this, \"diagnostico\", \"$diagnosticokey\", \"$anios\", \"$mes\")' ";
                                }else{
                                    $evento = "";
                                }
                                //echo "<td align='center' style='cursor:pointer;' {$evento}>$cantidad</td>";
                                echo "<td align='center' style='cursor:pointer;' {$evento}>{$datos[$anios][$mes]['valor']}</td>";
                                $total_ent_anio+=$datos[$anios][$mes]['valor'];
                                //$arr_suma_columnas_tipo_entidad[$j]+=$cantidad;
                                $arr_suma_columnas_tipo_entidad[$j]+=$datos[$anios][$mes]['valor'];
                                $ult_mes=$cantidad;
                            }else{
                                echo "<td align='center'>0</td>";
                                $ult_mes=0;
                            }
                            $j++;
                        }
                        echo "<td align='center' class='fila2'>".$total_ent_anio."</td>";

                        $porc = ($total_ent_anio*100)/$total_egresos;
                        echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                        if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                        if( $penu_mes == 0 ) $penu_mes=1;
                        $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                        echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                        $pormes = $total_ent_anio / count($meses);
                        echo "<td align='center'>".number_format((double)$pormes,2,',','.')."</td>";

                        if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "diagnostico", $diagnosticokey, $anio, $mes, $arr_det_diagnosticos[$diagnosticokey][$anio][$mes], $arr_det_pacientes );
                    }
                    echo "</tr>";
                    if( $mostrarDetalle == "on" ){
                        echo "<tr id='tr_det_diagnostico_{$diagnosticokey}' style='background-color:white;display:none;'>";
                            echo "<td colspan='6'>{$divDetalles}</td>";
                        echo "</tr>";
                    }
                }

                //IMPRIMIR LA FILA CON LOS TOTALES
                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>TOTAL</td>";
                $j=0;
                $divDetalles = "";
                foreach( $arr_anio_mes as $anios => $meses ){
                    $total_ent_anio = 0;
                    $ult_mes=1;
                    $penu_mes=0;
                    foreach($meses as $mes){
                        $sumdat = $arr_suma_columnas_tipo_entidad[$j];
                        $penu_mes=$ult_mes;
                        $ult_mes=$sumdat;
                        if( $mostrarDetalle == "on" ){
                            $evento = " mostrando='off' onclick='mostrarDetalle(this, \"diagnosticos\", \"total\", \"$anios\", \"$mes\")' ";
                        }else{
                            $evento = "";
                        }
                        echo "<td align='center' style='cursor:pointer;' $evento>".$sumdat."</td>";
                        $total_ent_anio+= $sumdat;
                        $j++;
                        if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "diagnosticos", "total", $anios, $mes, $arr_det_diagnosticos_total[$anios][$mes], $arr_det_pacientes );
                         if( $mostrarDetalle == "on" and ( count($arr_entidad_anio_mes) > 0 ) and ( count( $wcriteriosConsulta ) == 0 or isset($wcriteriosConsulta['entidad']) ) ){//-> si mostrar detalle y SI SE MUESTRA LA ENTIDAD TAMBIEN.
                                $divDetalleFaltante .= construirDivDetalleFaltante( "diagnosticos", "faltante", $anio, $mes, $arr_det_diagnosticos_total[$anios][$mes], $arr_det_pacientes, $arr_det_entidades_total[$anios][$mes] );
                        }
                    }
                    echo "<td align='center'>".$total_ent_anio."</td>";
                    $porc = ($total_ent_anio*100)/$total_egresos;
                    echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                    if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                    if( $penu_mes == 0 ) $penu_mes=1;
                    $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                    echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                    $pormes = $total_ent_anio / count($meses);
                    echo "<td align='center'>".number_format((double)$pormes,2,',','.')."</td>";
                }
                echo "</tr>";
                if( $mostrarDetalle == "on" ){
                    echo "<tr id='tr_det_diagnosticos_total' style='background-color:white;display:none;'>";
                        echo "<td colspan='6'>{$divDetalles}<br>{$divDetalleFaltante}</td>";
                    echo "</tr>";
                }




                echo "</table>";
                echo "<center>
                <div id='contenedor_graf_diagnostico' style='display:none;'>
                <div id='div_grafica_diagnosticos' style='border: 1px solid #999999; width:600px; height:450px;'>
                </div>
                <input type='button' value='Ocultar' onclick='ocultarDiv(\"contenedor_graf_diagnostico\")' />
                </div>
                </center>";
                echo "</div>";

                echo "</div>";
                /*******************************************
                ***************FIN DIAGNOSTICOS********************
                *******************************************/
                //-------------------------------------
            }
        }


        /*******************************************
        ***************ESPECIALIDADES***************
        *******************************************/
        if( $wtipoProcedimiento == ""){//--> ACÁ SE GUARDAN Y SE PINTA EL INFORME PARA LAS ESPECIALIADADES

            foreach( $arr_especialidadP_anio_mes as $especialidadkeyP => $datosP ){

                $ent="";
                ( isset( $arr_especialidades[ $especialidadkeyP ] ) )? $ent=$arr_especialidades[ $especialidadkeyP ] : $ent=$especialidadkeyP;

                foreach( $arr_anio_mes as $aniosP => $mesesP ){
                    foreach($mesesP as $mesP){
                        if( isset($datosP[$aniosP][$mesP] ) ){
                            if( $detMesesConsultados[$aniosP][$mesP]['guardarHistorico'] ){
                                guardarRegistroHistorico($aniosP, $mesP, $wgrupoOri, "ESPECIALIDAD", $especialidadkeyP, $datosP[$aniosP][$mesP]['valor'], 'P');
                                if( !isset( $datosP[$aniosP][$mesP]['servicios'] ) ){
                                    $datosP[$aniosP][$mesP]['servicios'] = array();
                                }
                                foreach ($datosP[$aniosP][$mesP]['servicios'] as $keySerP => $valueserP ) {
                                    guardarRegistroHistorico($aniosP, $mesP, $keySerP, "ESPECIALIDAD", $especialidadkeyP, $valueserP, 'P');
                                }
                            }
                        }
                    }
                }
            }

            foreach( $arr_especialidadS_anio_mes as $especialidadkeyS => $datosS ){

                $ent="";
                ( isset( $arr_especialidades[ $especialidadkeyS ] ) )? $ent=$arr_especialidades[ $especialidadkeyS ] : $ent=$especialidadkeyS;
                //$total_tipo_especialidad = 0;

                //$ordenados[$especialidadkeyS] = 0;
                foreach( $arr_anio_mes as $aniosS => $mesesS ){
                    foreach($mesesS as $mesS){
                        if( isset($datosS[$aniosS][$mesS] ) ){
                            if(  $detMesesConsultados[$aniosS][$mesS]['guardarHistorico'] ){
                                guardarRegistroHistorico($aniosS, $mesS, $wgrupoOri, "ESPECIALIDAD", $especialidadkeyS, $datosS[$aniosS][$mesS]['valor'], 'S');
                                if( !isset( $datosS[$aniosS][$mesS]['servicios'] ) ){
                                    $datosS[$aniosS][$mesS]['servicios'] = array();
                                }
                                foreach ($datosS[$aniosS][$mesS]['servicios'] as $keySerS => $valueserS ) {
                                    guardarRegistroHistorico($aniosS, $mesS, $keySerS, "ESPECIALIDAD", $especialidadkeyS, $valueserS, 'S');
                                }
                            }
                        }
                    }
                }
            }
            //$tabla_oculta_especialidades = "<table border=1 id='tabla_grafico_especialidades' style='display:none;'>";
            //Esta tabla oculta es el codigo html que tiene una columna con el tipo de entidad y otra con el total de egresos
            /*PARA ORDENAR LOS REGISTROS DE MAYOR SUMA ANUAL A MENOR SUMA ANUAL*/
            $ordenados = array();
            foreach( $arr_especialidad_anio_mes as $especialidadkey => $datos ){
                $ent="";
                ( isset( $arr_especialidades[ $especialidadkey ] ) )? $ent=$arr_especialidades[ $especialidadkey ] : $ent=$especialidadkey;
                //$tabla_oculta_especialidades.=  "<tr>";
                //$tabla_oculta_especialidades.=  "<td>".$ent."</td>";
                $total_tipo_especialidad = 0;

                $ordenados[$especialidadkey] = 0;
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        if( isset($datos[$anios][$mes] ) ){
                            $ordenados[$especialidadkey]+=$datos[$anios][$mes]['valor'];
                            if( $detMesesConsultados[$anios][$mes]['guardarHistorico'] ){
                                guardarRegistroHistorico($anios, $mes, $wgrupoOri, "ESPECIALIDAD", $especialidadkey, $datos[$anios][$mes]['valor']);
                                if( !isset( $datos[$anios][$mes]['servicios'] ) ){
                                    $datos[$anios][$mes]['servicios'] = array();
                                }
                                foreach ($datos[$anios][$mes]['servicios'] as $keySer => $valueser ) {
                                    guardarRegistroHistorico($anios, $mes, $keySer, "ESPECIALIDAD", $especialidadkey, $valueser);
                                }
                            }
                        }
                    }
                    $total_tipo_especialidad+=$ordenados[$especialidadkey];
                }
                //$tabla_oculta_especialidades.=  "<td>".$total_tipo_especialidad."</td>";
                //$tabla_oculta_especialidades.=  "</tr>";
            }
            //$tabla_oculta_especialidades.= "</table>";

            //echo "<br> EDB-> ARREGLO ORDENADOS EN ESPECIALIDADES: ".print_r( $ordenados )."<br>";
            if( count( $arr_especialidad_anio_mes) > 0 ){
                arsort($ordenados);
                $tabla_oculta_especialidades = "<table border=1 id='tabla_grafico_especialidades' style='display:none;'>";
                $cant_ents = 0;
                $suma_menores=0;
                foreach( $ordenados as $especialidadkeyy => $cantidadd ){
                    if( $cant_ents >= $topeGraficos ){
                        $suma_menores+= $cantidadd;
                    }else{
                        $ent="";
                        ( isset( $arr_especialidades[ $especialidadkeyy ] ) )? $ent=$arr_especialidades[ $especialidadkeyy ] : $ent=$especialidadkeyy;
                        $tabla_oculta_especialidades.=  "<tr>";
                        $tabla_oculta_especialidades.=  "<td>".$ent."</td>";
                        $tabla_oculta_especialidades.=  "<td>".$cantidadd."</td>";
                        $tabla_oculta_especialidades.=  "</tr>";
                        $cant_ents++;
                    }
                }
                if( $suma_menores > 0 ){
                    $tabla_oculta_especialidades.=  "<tr>";
                    $tabla_oculta_especialidades.=  "<td>OTROS</td>";
                    $tabla_oculta_especialidades.=  "<td>".$suma_menores."</td>";
                    $tabla_oculta_especialidades.=  "</tr>";
                }
                $tabla_oculta_especialidades.=  "</table>";
                echo $tabla_oculta_especialidades;

                echo "<div class='desplegables' style='width:100%;'>";
                echo "<h3><b>* ESPECIALIDADES *</b></h3>";
                echo "<div>";
                echo '<img width="20" height="19" style="float:left; cursor:pointer;" onclick="dibujarGraficoEspecialidades()" src="../../images/medical/root/chart.png">';
                echo "<table class='tabla_r' id='tabla_especialidades' align='center' width='85%'>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>&nbsp;</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    echo "<td align='center' colspan=".(count($meses) + 4 ).">".$anios."</td>";
                }
                echo "</tr>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>Especialidad</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        echo "<td align='center'>".$arr_meses[$mes]."</td>";
                    }
                    echo "<td align='center'>Acum.</td>";
                    echo "<td align='center'>%</td>";
                    echo "<td align='center'>% Variac.</td>";
                    echo "<td align='center'>Promedio<br>Mes</td>";
                }
                echo "</tr>";

                $arr_suma_columnas_total = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                $arr_suma_columnas_tipo_entidad = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                //foreach( $arr_especialidad_anio_mes as $especialidadkey => $datos ){
                foreach( $ordenados as $especialidadkey => $cantidad ){
                    $datos = $arr_especialidad_anio_mes[$especialidadkey];
                    echo "<tr class='fila1'>";
                    $ent="";
                    ( isset( $arr_especialidades[ $especialidadkey ] ) )? $ent=$especialidadkey." ".$arr_especialidades[ $especialidadkey ] : $ent=$especialidadkey;
                    echo "<td>".$ent."</td>";
                    $j=0;
                    $divDetalles = "";
                    foreach( $arr_anio_mes as $anios => $meses ){
                        $total_ent_anio = 0;
                        $ult_mes=0;
                        $penu_mes=0;
                        foreach($meses as $mes){
                            $penu_mes=$ult_mes;
                            if( $mostrarDetalle == "on" ){
                                $evento = " mostrando='off' onclick='mostrarDetalle(this, \"especialidad\", \"$especialidadkey\", \"$anios\", \"$mes\")' ";
                            }else{
                                $evento = "";
                            }
                            if( isset($datos[$anios][$mes]['valor'] ) ){
                                echo "<td align='center' style='cursor:pointer;' {$evento}>".$datos[$anios][$mes]['valor']."</td>";
                                $total_ent_anio+=$datos[$anios][$mes]['valor'];
                                $arr_suma_columnas_tipo_entidad[$j]+=$datos[$anios][$mes]['valor'];
                                $ult_mes=$datos[$anios][$mes]['valor'];
                            }else{
                                echo "<td align='center'>0</td>";
                                $ult_mes=0;
                            }
                            $j++;
                        }
                        echo "<td align='center' class='fila2'>".$total_ent_anio."</td>";

                        $porc = ($total_ent_anio*100)/$total_egresos;
                        echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                        if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                        if( $penu_mes == 0 ) $penu_mes=1;
                        $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                        echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                        $pormes = $total_ent_anio / count($meses);
                        echo "<td align='center'>".number_format((double)$pormes,2,',','.')."</td>";
                        if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "especialidad", $especialidadkey, $anio, $mes, $arr_det_especialidades[$especialidadkey][$anio][$mes], $arr_det_pacientes );
                    }
                    echo "</tr>";
                    if( $mostrarDetalle == "on" ){
                        echo "<tr id='tr_det_especialidad_{$especialidadkey}' style='background-color:white;display:none;'>";
                            echo "<td colspan='6'>{$divDetalles}</td>";
                        echo "</tr>";
                    }
                }

                //IMPRIMIR LA FILA CON LOS TOTALES
                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>TOTAL</td>";
                $j=0;
                $divDetalles = "";
                foreach( $arr_anio_mes as $anios => $meses ){
                    $total_ent_anio = 0;
                    $ult_mes=1;
                    $penu_mes=0;
                    foreach($meses as $mes){
                        $sumdat = $arr_suma_columnas_tipo_entidad[$j];
                        $penu_mes=$ult_mes;
                        $ult_mes=$sumdat;
                        if( $mostrarDetalle == "on" ){
                            $evento = " mostrando='off' onclick='mostrarDetalle(this, \"especialidades\", \"total\", \"$anios\", \"$mes\")' ";
                        }else{
                            $evento = "";
                        }
                        echo "<td align='center' style='cursor:pointer;' $evento>".$sumdat."</td>";
                        $total_ent_anio+= $sumdat;
                        if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "especialidades", "total", $anios, $mes, $arr_det_especialidades_total[$anios][$mes], $arr_det_pacientes );
                         if( $mostrarDetalle == "on" and ( count($arr_entidad_anio_mes) > 0 ) and ( count( $wcriteriosConsulta ) == 0 or isset($wcriteriosConsulta['entidad']) ) )//-> si mostrar detalle y SI SE MUESTRA LA ENTIDAD TAMBIEN.
                                $divDetalleFaltante .= construirDivDetalleFaltante( "especialidades", "faltante", $anios, $mes, $arr_det_especialidades_total[$anios][$mes], $arr_det_pacientes, $arr_det_entidades_total[$anios][$mes] );
                        $j++;
                    }
                    echo "<td align='center'>".$total_ent_anio."</td>";
                    $porc = ($total_ent_anio*100)/$total_egresos;
                    echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                    if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                    if( $penu_mes == 0 ) $penu_mes=1;
                    $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                    echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                    $pormes = $total_ent_anio / count($meses);
                    echo "<td align='center'>".number_format((double)$pormes,2,',','.')."</td>";
                }
                echo "</tr>";
                if( $mostrarDetalle == "on" ){
                    echo "<tr id='tr_det_especialidades_total' style='background-color:white;display:none;'>";
                        echo "<td colspan='6'>{$divDetalles}<br>{$divDetalleFaltante}</td>";
                    echo "</tr>";
                }




                echo "</table>";
                echo "<center>
                <div id='contenedor_graf_especialidad' style='display:none;'>
                <div id='div_grafica_especialidades' style='border: 1px solid #999999; width:600px; height:450px;'>
                </div>
                <input type='button' value='Ocultar' onclick='ocultarDiv(\"contenedor_graf_especialidad\")' />
                </div>
                </center>";
                echo "</div>";

                echo "</div>";
                /*******************************************
                ***************FIN ESPECIALIDADES********************
                *******************************************/
                //-------------------------------------
            }
        }


        /*******************************************
        ***************PROCEDIMIENTOS***************
        *******************************************/
        if( $wtipoEspecialidad == "" ){

            foreach( $arr_procedimientoP_anio_mes as $procedimientokeyP => $datosP ){
                foreach( $arr_anio_mes as $aniosP => $mesesP ){
                    foreach($mesesP as $mesP){
                        if( isset($datosP[$aniosP][$mesP]['valor'] ) ){
                            //$ordenados[$especialidadkeyP]+=$datosP[$aniosP][$mesP];
                            if(  $detMesesConsultados[$aniosP][$mesP]['guardarHistorico']  ){
                                guardarRegistroHistorico($aniosP, $mesP, $wgrupoOri, "PROCEDIMIENTOS", $procedimientokeyP, $datosP[$aniosP][$mesP]['valor'], 'P');
                                if( !isset( $datosP[$aniosP][$mesP]['servicios'] ) ){
                                    $datosP[$aniosP][$mesP]['servicios'] = array();
                                }
                                foreach ($datosP[$aniosP][$mesP]['servicios'] as $keySerP => $valueserP ) {
                                    guardarRegistroHistorico($aniosP, $mesP, $keySerP, "PROCEDIMIENTOS", $procedimientokeyP, $valueserP, 'P');
                                }
                            }
                        }
                    }
                }
            }

            foreach( $arr_procedimientoS_anio_mes as $procedimientokeyS => $datosS ){

                //$ordenados[$procedimientokeyS] = 0;
                foreach( $arr_anio_mes as $aniosS => $mesesS ){
                    foreach($mesesS as $mesS){
                        if( isset($datosS[$aniosS][$mesS]['valor'] ) ){
                            //$ordenados[$procedimientokeyS]+=$datosS[$aniosS][$mesS];
                            if( $detMesesConsultados[$aniosS][$mesS]['guardarHistorico'] ){
                                guardarRegistroHistorico($aniosS, $mesS, $wgrupoOri, "PROCEDIMIENTOS", $procedimientokeyS, $datosS[$aniosS][$mesS]['valor'], 'S');
                                if( !isset( $datosS[$aniosS][$mesS]['servicios'] ) ){
                                    $datosS[$aniosS][$mesS]['servicios'] = array();
                                }
                                foreach ($datosS[$aniosS][$mesS]['servicios'] as $keySerS => $valueserS ) {
                                    guardarRegistroHistorico($aniosS, $mesS, $keySerS, "PROCEDIMIENTOS", $procedimientokeyS, $valueserS, 'S');
                                }
                            }
                        }
                    }
                }
            }

            /*PARA ORDENAR LOS REGISTROS DE MAYOR SUMA ANUAL A MENOR SUMA ANUAL*/
            $ordenados = array();
            foreach( $arr_procedimiento_anio_mes as $procedimientokey => $datos ){
                $ordenados[$procedimientokey] = 0;
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        if( isset($datos[$anios][$mes]['valor'] ) ){
                            $ordenados[$procedimientokey]+=$datos[$anios][$mes]['valor'];
                            if( $detMesesConsultados[$anios][$mes]['guardarHistorico'] ){
                                guardarRegistroHistorico($anios, $mes, $wgrupoOri, "PROCEDIMIENTOS", $procedimientokey, $datos[$anios][$mes]['valor']);
                                if( !isset( $datos[$anios][$mes]['servicios'] ) ){
                                    $datos[$anios][$mes]['servicios'] = array();
                                }
                                foreach ($datos[$anios][$mes]['servicios'] as $keySer => $valueser ) {
                                    guardarRegistroHistorico($anios, $mes, $keySer, "PROCEDIMIENTOS", $procedimientokey, $valueser);
                                }
                            }

                        }
                    }
                }
            }

            if( count($arr_procedimiento_anio_mes) > 0 ){
                arsort($ordenados);
                $tabla_oculta_procedimientos2 = "<table border=1 id='tabla_grafico_procedimientos2' style='display:none;'>";
                $cant_ents = 0;
                $suma_menores=0;
                foreach( $ordenados as $procedimientokeyy => $cantidadd ){
                    if( $cant_ents >= $topeGraficos ){
                        $suma_menores+= $cantidadd;
                    }else{
                        $ent="";
                        ( isset( $arr_procedimientos[ $procedimientokeyy ] ) )? $ent=$arr_procedimientos[ $procedimientokeyy ] : $ent=$procedimientokeyy;
                        $tabla_oculta_procedimientos2.=  "<tr>";
                        $tabla_oculta_procedimientos2.=  "<td>".$ent."</td>";
                        $tabla_oculta_procedimientos2.=  "<td>".$cantidadd."</td>";
                        $tabla_oculta_procedimientos2.=  "</tr>";
                        $cant_ents++;
                    }
                }
                if( $suma_menores > 0 ){
                    $tabla_oculta_procedimientos2.=  "<tr>";
                    $tabla_oculta_procedimientos2.=  "<td>OTROS</td>";
                    $tabla_oculta_procedimientos2.=  "<td>".$suma_menores."</td>";
                    $tabla_oculta_procedimientos2.=  "</tr>";
                }
                $tabla_oculta_procedimientos2.=  "</table>";
                echo $tabla_oculta_procedimientos2;

                echo "<div class='desplegables' style='width:100%;'>";
                echo "<h3><b>* PROCEDIMIENTOS *</b></h3>";
                echo "<div>";
                echo '<img width="20" height="19" style="float:left; cursor:pointer;" onclick="dibujarGraficoProcedimientos()" src="../../images/medical/root/chart.png">';
                echo '<img width="20" height="19" style="float:left; cursor:pointer;" onclick="dibujarGraficoProcedimientos2()" src="../../images/medical/root/chart.png">';
                echo "<table class='tabla_r' id='tabla_procedimientos' align='center' width='85%'>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>&nbsp;</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    echo "<td align='center' colspan=".(count($meses) + 4 ).">".$anios."</td>";
                }
                echo "</tr>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>Procedimiento</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        echo "<td align='center'>".$arr_meses[$mes]."</td>";
                    }
                    echo "<td align='center'>Acum.</td>";
                    echo "<td align='center'>%</td>";
                    echo "<td align='center'>% Variac.</td>";
                    echo "<td align='center'>Promedio<br>Mes</td>";
                }
                echo "</tr>";


                $arr_suma_columnas_total = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                $arr_suma_columnas_tipo_entidad = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                //foreach( $arr_procedimiento_anio_mes as $procedimientokey => $datos ){
                foreach( $ordenados as $procedimientokey => $cantidad ){
                    $datos = $arr_procedimiento_anio_mes[$procedimientokey];
                    echo "<tr class='fila1'>";
                    $ent="";
                    ( isset( $arr_procedimientos[ $procedimientokey ] ) )? $ent=$procedimientokey." ".$arr_procedimientos[ $procedimientokey ] : $ent=$procedimientokey;
                    echo "<td>".$ent."</td>";
                    $j=0;
                    $divDetalles="";
                    foreach( $arr_anio_mes as $anios => $meses ){
                        $total_ent_anio = 0;
                        $ult_mes=0;
                        $penu_mes=0;
                        foreach($meses as $mes){
                            $penu_mes=$ult_mes;
                            if( isset($datos[$anios][$mes] ) ){
                                if( $mostrarDetalle == "on" ){
                                    $evento = " mostrando='off' onclick='mostrarDetalle(this, \"procedimiento\", \"$procedimientokey\", \"$anios\", \"$mes\")' ";
                                }else{
                                    $evento = "";
                                }
                                echo "<td align='center' style='cursor:pointer;' {$evento}>".$datos[$anios][$mes]['valor']."</td>";
                                $total_ent_anio+=$datos[$anios][$mes]['valor'];
                                $arr_suma_columnas_tipo_entidad[$j]+=$datos[$anios][$mes]['valor'];
                                $ult_mes=$datos[$anios][$mes]['valor'];
                            }else{
                                echo "<td align='center'>0</td>";
                                $ult_mes=0;
                            }
                            $j++;
                        }
                        echo "<td align='center' class='fila2'>".$total_ent_anio."</td>";

                        $porc = ($total_ent_anio*100)/$total_egresos;
                        echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                        if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                        if( $penu_mes == 0 ) $penu_mes=1;
                        $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                        echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                        $pormes = $total_ent_anio / count($meses);
                        echo "<td align='center'>".number_format((double)$pormes,2,',','.')."</td>";
                        if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "procedimiento", $procedimientokey, $anio, $mes, $arr_det_procedimientos[$procedimientokey][$anio][$mes], $arr_det_pacientes );

                    }
                    echo "</tr>";
                    if( $mostrarDetalle == "on" ){
                        echo "<tr id='tr_det_procedimiento_{$procedimientokey}' style='background-color:white;display:none;'>";
                            echo "<td colspan='6'>{$divDetalles}</td>";
                        echo "</tr>";
                    }
                }

                $tabla_oculta_procedimientos = "<table border=1 id='tabla_grafico_procedimientos' style='display:none;'>";

                //IMPRIMIR LA FILA CON LOS TOTALES
                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>TOTAL</td>";
                $j=0;
                $divDetalles = "";
                foreach( $arr_anio_mes as $anios => $meses ){
                    $total_ent_anio = 0;
                    $ult_mes=1;
                    $penu_mes=0;
                    foreach($meses as $mes){
                        $tabla_oculta_procedimientos.= "<tr>";
                        $tabla_oculta_procedimientos.="<td align='center'>".$arr_meses[$mes]."</td>";
                        $sumdat = $arr_suma_columnas_tipo_entidad[$j];
                        $penu_mes=$ult_mes;
                        $ult_mes=$sumdat;
                        if( $mostrarDetalle == "on" ){
                            $evento = " mostrando='off' onclick='mostrarDetalle(this, \"procedimientos\", \"total\", \"$anios\", \"$mes\")' ";
                        }else{
                            $evento = "";
                        }
                        echo "<td align='center' style='cursor:pointer;' $evento>".$sumdat."</td>";
                        $tabla_oculta_procedimientos.="<td align='center'>".$sumdat."</td>";
                        $tabla_oculta_procedimientos.= "</tr>";
                        $total_ent_anio+= $sumdat;
                        if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "procedimientos", "total", $anios, $mes, $arr_det_procedimientos_total[$anios][$mes], $arr_det_pacientes );
                         if( $mostrarDetalle == "on" and ( count($arr_entidad_anio_mes) > 0 ) and ( count( $wcriteriosConsulta ) == 0 or isset($wcriteriosConsulta['entidad']) ) )//-> si mostrar detalle y SI SE MUESTRA LA ENTIDAD TAMBIEN.
                                $divDetalleFaltante .= construirDivDetalleFaltante( "procedimientos", "faltante", $anios, $mes, $arr_det_procedimientos_total[$anios][$mes], $arr_det_pacientes, $arr_det_entidades_total[$anios][$mes] );

                        $j++;
                    }
                    echo "<td align='center'>".$total_ent_anio."</td>";
                    $porc = ($total_ent_anio*100)/$total_egresos;
                    echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                    if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                    if( $penu_mes == 0 ) $penu_mes=1;
                    $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                    echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                    $pormes = $total_ent_anio / count($meses);
                    echo "<td align='center'>".number_format((double)$pormes,2,',','.')."</td>";
                }
                echo "</tr>";
                if( $mostrarDetalle == "on" ){
                    echo "<tr id='tr_det_procedimientos_total' style='background-color:white;display:none;'>";
                        echo "<td colspan='6'>{$divDetalles}<br>{$divDetalleFaltante}</td>";
                    echo "</tr>";
                }


                $tabla_oculta_procedimientos.= "</table>";


                echo "</table>";
                echo $tabla_oculta_procedimientos;
                echo "<center>
                <div id='contenedor_graf_procedimientos' style='display:none;'>
                <div id='div_grafica_procedimientos' style='border: 1px solid #999999; width:600px; height:450px;'>
                </div>
                </div>
                <input type='button' value='Ocultar' onclick='ocultarDiv(\"contenedor_graf_procedimientos\")' />
                </center>";
                echo "</div>";

                echo "</div>";
                /*******************************************
                ***************FIN PROCEDIMIENTOS********************
                *******************************************/
            }
        }

        /*******************************************
        ***************PROCEDENCIA********************
        *******************************************/
        if( $wtipoProcedimiento == "" and $wtipoEspecialidad == "" ){

            /*PARA ORDENAR LOS REGISTROS DE MAYOR SUMA ANUAL A MENOR SUMA ANUAL*/
            $ordenados = array();
            foreach( $arr_procedencia_anio_mes as $barriokey => $datos ){
                $ordenados[$barriokey] = 0;
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        if( isset($datos[$anios][$mes] ) ){
                            $ordenados[$barriokey]+=$datos[$anios][$mes]['valor'];
                            if( $detMesesConsultados[$anios][$mes]['guardarHistorico'] ){
                                guardarRegistroHistorico($anios, $mes, $wgrupoOri, "PROCEDENCIA", $barriokey, $datos[$anios][$mes]['valor']);
                            }
                        }
                    }
                }
            }
            if( count($arr_procedencia_anio_mes) > 0 ){
                arsort($ordenados);
                $tabla_oculta_procedencia2 = "<table border=1 id='tabla_grafico_procedencia2' style='display:none;'>";
                $cant_ents = 0;
                $suma_menores=0;
                foreach( $ordenados as $procedenciakeyy => $cantidadd ){
                    if( $cant_ents >= $topeGraficos ){
                        $suma_menores+= $cantidadd;
                    }else{
                        $ent="";
                        ( isset( $arr_barrios[ $procedenciakeyy ] ) )? $ent=$arr_barrios[ $procedenciakeyy ] : $ent=$procedenciakeyy;
                        $tabla_oculta_procedencia2.=  "<tr>";
                        $tabla_oculta_procedencia2.=  "<td>".$ent."</td>";
                        $tabla_oculta_procedencia2.=  "<td>".$cantidadd."</td>";
                        $tabla_oculta_procedencia2.=  "</tr>";
                        $cant_ents++;
                    }
                }
                if( $suma_menores > 0 ){
                    $tabla_oculta_procedencia2.=  "<tr>";
                    $tabla_oculta_procedencia2.=  "<td>OTROS</td>";
                    $tabla_oculta_procedencia2.=  "<td>".$suma_menores."</td>";
                    $tabla_oculta_procedencia2.=  "</tr>";
                }
                $tabla_oculta_procedencia2.=  "</table>";
                echo $tabla_oculta_procedencia2;

                echo "<div class='desplegables' style='width:100%;'>";
                echo "<h3><b>* PROCEDENCIA *</b></h3>";
                echo "<div>";
                echo '<img width="20" height="19" style="float:left; cursor:pointer;" onclick="dibujarGraficoProcedencia()" src="../../images/medical/root/chart.png">';
                echo '<img width="20" height="19" style="float:left; cursor:pointer;" onclick="dibujarGraficoProcedencia2()" src="../../images/medical/root/chart.png">';
                echo "<table class='tabla_r' id='tabla_especialidades' align='center' width='85%'>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>&nbsp;</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    echo "<td align='center' colspan=".(count($meses) + 4 ).">".$anios."</td>";
                }
                echo "</tr>";

                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>Procedencia</td>";
                foreach( $arr_anio_mes as $anios => $meses ){
                    foreach($meses as $mes){
                        echo "<td align='center'>".$arr_meses[$mes]."</td>";
                    }
                    echo "<td align='center'>Acum.</td>";
                    echo "<td align='center'>%</td>";
                    echo "<td align='center'>% Variac.</td>";
                    echo "<td align='center'>Promedio<br>Mes</td>";
                }
                echo "</tr>";


                $arr_suma_columnas_total = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                $arr_suma_columnas_tipo_entidad = $arr_suma_columnas; //Se crea una copia de dicho arreglo

                //foreach( $arr_procedencia_anio_mes as $barriokey => $datos ){
                foreach( $ordenados as $barriokey => $cantidad ){
                    $datos = $arr_procedencia_anio_mes[$barriokey];
                    echo "<tr class='fila1'>";
                    $ent="";
                    ( isset( $arr_barrios[ $barriokey ] ) )? $ent=$barriokey." ".$arr_barrios[ $barriokey ] : $ent=$barriokey;
                    echo "<td>".$ent."</td>";
                    $j=0;
                    $divDetalles="";
                    foreach( $arr_anio_mes as $anios => $meses ){
                        $total_ent_anio = 0;
                        $ult_mes=0;
                        $penu_mes=0;
                        foreach($meses as $mes){
                            $penu_mes=$ult_mes;
                            if( isset($datos[$anios][$mes] ) ){
                                if( $mostrarDetalle == "on" ){
                                    $evento = " mostrando='off' onclick='mostrarDetalle(this, \"procedencia\", \"$barriokey\", \"$anios\", \"$mes\")' ";
                                }else{
                                    $evento = "";
                                }
                                echo "<td align='center' style='cursor:pointer;' {$evento}>".$datos[$anios][$mes]['valor']."</td>";
                                $total_ent_anio                     +=$datos[$anios][$mes]['valor'];
                                $arr_suma_columnas_tipo_entidad[$j] +=$datos[$anios][$mes]['valor'];
                                $ult_mes                             =$datos[$anios][$mes]['valor'];
                            }else{
                                echo "<td align='center'>0</td>";
                                $ult_mes=0;
                            }
                            $j++;
                        }
                        echo "<td align='center' class='fila2'>".$total_ent_anio."</td>";

                        $porc = ($total_ent_anio*100)/$total_egresos;
                        echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                        if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                        if( $penu_mes == 0 ) $penu_mes=1;
                        $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                        echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                        $pormes = $total_ent_anio / count($meses);
                        echo "<td align='center'>".number_format($pormes,2,',','.')."</td>";
                        if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "procedencia", $barriokey, $anios, $mes, $arr_det_procedencia[$barriokey][$anios][$mes], $arr_det_pacientes );
                    }
                    echo "</tr>";
                    if( $mostrarDetalle == "on" ){
                        echo "<tr id='tr_det_procedencia_{$barriokey}' style='background-color:white;display:none;'>";
                            echo "<td colspan='6'>{$divDetalles}</td>";
                        echo "</tr>";
                    }
                }

                $tabla_oculta_procedencia = "<table border=1 id='tabla_grafico_procedencia' style='display:none;'>";

                //IMPRIMIR LA FILA CON LOS TOTALES
                echo "<tr class='encabezadotabla'>";
                echo "<td align='center'>TOTAL</td>";
                $j=0;
                foreach( $arr_anio_mes as $anios => $meses ){
                    $total_ent_anio = 0;
                    $ult_mes=1;
                    $penu_mes=0;
                    foreach($meses as $mes){
                        $tabla_oculta_procedencia.= "<tr>";
                        $tabla_oculta_procedencia.="<td align='center'>".$arr_meses[$mes]."</td>";
                        $sumdat = $arr_suma_columnas_tipo_entidad[$j];
                        $penu_mes=$ult_mes;
                        $ult_mes=$sumdat;
                        if( $mostrarDetalle == "on" ){
                            $evento = " mostrando='off' onclick='mostrarDetalle(this, \"procedencias\", \"total\", \"$anios\", \"$mes\")' ";
                        }else{
                            $evento = "";
                        }
                        echo "<td align='center' style='cursor:pointer;' $evento>".$sumdat."</td>";
                        $tabla_oculta_procedencia.="<td align='center'>".$sumdat."</td>";
                        $tabla_oculta_procedencia.= "</tr>";
                        $total_ent_anio+= $sumdat;
                         if( $mostrarDetalle == "on" )
                                $divDetalles .= construirDivDetalle( "procedencias", "total", $anios, $mes, $arr_det_procedencia_total[$anios][$mes], $arr_det_pacientes );
                        $j++;
                    }
                    echo "<td align='center'>".$total_ent_anio."</td>";
                    $porc = ($total_ent_anio*100)/$total_egresos;
                    echo "<td align='center'>".number_format((double)$porc,2,',','.')."</td>";

                    if( $penu_mes == 0 ) $penu_mes = $ult_mes;
                    if( $penu_mes == 0 ) $penu_mes=1;
                    $porcvar = ( $ult_mes - $penu_mes ) * 100 / $penu_mes;
                    echo "<td align='center'>".number_format((double)$porcvar,2,',','.')."</td>";

                    $pormes = $total_ent_anio / count($meses);
                    echo "<td align='center'>".number_format($pormes,2,',','.')."</td>";
                }
                echo "</tr>";
                if( $mostrarDetalle == "on" ){
                    echo "<tr id='tr_det_procedencias_total' style='background-color:white;display:none;'>";
                        echo "<td colspan='6'>{$divDetalles}</td>";
                    echo "</tr>";
                }

                $tabla_oculta_procedencia.= "</table>";


                echo "</table>";
                echo $tabla_oculta_procedencia;
                echo "<center>
                <div id='contenedor_graf_procedencia' style='display:none;'>
                <div id='div_grafica_procedencia' style='border: 1px solid #999999; width:600px; height:450px;'>
                </div>
                </div>
                <input type='button' value='Ocultar' onclick='ocultarDiv(\"contenedor_graf_procedencia\")' />
                </center>";
                echo "</div>";

                echo "</div>";
                /*******************************************
                ***************FIN PROCEDENCIA********************
                *******************************************/
            }
        }

        echo "</div>";
    }

    function diasDiferencia( $wfecha1, $wfecha2 ){
        $arr_fec1 = explode("-",$wfecha1);
        $ano1 = $arr_fec1[0];
        $mes1 = $arr_fec1[1];
        $dia1 = $arr_fec1[2];

        $arr_fec2 = explode("-",$wfecha2);
        //defino fecha 2
        $ano2 = $arr_fec2[0];
        $mes2 = $arr_fec2[1];
        $dia2 = $arr_fec2[2];

        //calculo timestam de las dos fechas
        $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
        $timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

        //resto a una fecha la otra
        $segundos_diferencia = $timestamp1 - $timestamp2;
        //echo $segundos_diferencia;

        //convierto segundos en días
        $dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

        //obtengo el valor absoulto de los días (quito el posible signo negativo)
        $dias_diferencia = abs($dias_diferencia);

        //quito los decimales a los días de diferencia
        $dias_diferencia = round($dias_diferencia);

        return $dias_diferencia;
    }

    function mesesDiferencia($date1,$date2){
        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
        return $diff;
    }

    function guardarRegistroHistorico($anio, $mes, $servicio, $tipo, $codigo, $valor, $tipoMovimiento=''){
        global $conex,$wbasedato;
        $query = "INSERT ".$wbasedato."_000235 (    medico     ,        fecha_data  ,       hora_data  ,    Repano  , Repmes   , Repser         ,     Reptip ,       Repcod ,        Reptim,           Repval , Repest,     Seguridad )
                                        VALUES ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$anio."','".$mes."', '".$servicio."', '".$tipo."', '".$codigo."', '".$tipoMovimiento."','".$valor."', 'on'  ,  'C-".$wbasedato."')";
        $err1 = mysql_query($query,$conex) or die( "NO GUARDO HISTORICO" );
    }

    function vistaInicial(){

        global $wemp_pmla;
        global $wactualiz;
        global $conex;
        global $wgruposccos;
        global $wccosCirIndividuales;

        $width_sel = " width: 95%; ";
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/MSIE/i',$u_agent))
            $width_sel = "";

        echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

        $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
        $empresa = strtolower($institucion->baseDeDatos);
        encabezado("REPORTES DE EGRESO", $wactualiz, $empresa);

        echo "<center>";
        echo '<span class="subtituloPagina2">Parámetros de consulta</span>';

        echo '<br><br>';

        echo '<div style="width: 100%">';


        //---DIV PRINCIPAL
        echo '<table align="center">';
        echo "<tr>";
        echo '<td colspan=2 class="encabezadotabla">Servicio</td>';
        echo "</tr>";
        echo "<tr>";
        echo '<td colspan=2 class="fila1">';
        echo "<div align='center'>";
        echo "<select id='lista_grupos'  align='center' style='".$width_sel." margin:5px;'>";
        echo "<option value=''>TODOS</option>";
        foreach ($wgruposccos as $codigog=>$nombreg)
            echo "<option value='".$codigog."' tipoOpcion='grupo'>".$nombreg."</option>";
        foreach ($wccosCirIndividuales as $codigoCco=>$nombreCco)
            echo "<option value='".$codigoCco."' tipoOpcion='cco'>".$nombreCco."</option>";
        echo '</select>';
        echo '</div>';
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo '<td colspan=2 class="encabezadotabla">Tipo Especialidad</td>';
        echo "</tr>";
        echo "<tr>";
            echo '<td colspan=2 class="fila1">';
            echo "<input type='radio' name='radio_tipoEspecialidad' onClick='deshabilitarFiltro( this );' value='P'> PRIMARIA";
            echo "&nbsp;<input type='radio' name='radio_tipoEspecialidad' onClick='deshabilitarFiltro( this );' value='S'> SECUNDARIA";
            echo "&nbsp;<input type='radio' name='radio_tipoEspecialidad' value='' checked> No filtrar";
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo '<td colspan=2 class="encabezadotabla">Tipo Procedimiento</td>';
        echo "</tr>";
        echo "<tr>";
            echo '<td colspan=2 class="fila1">';
            echo "<input type='radio' name='radio_tipoProcedimiento'  onClick='deshabilitarFiltro( this )'; value='P'> PRIMARIO";
            echo "&nbsp;<input type='radio' name='radio_tipoProcedimiento'  onClick='deshabilitarFiltro( this )'; value='S'> SECUNDARIO";
            echo "&nbsp;<input type='radio' name='radio_tipoProcedimiento'  value='' checked> No filtrar";
            echo "</td>";
        echo "</tr>";

        echo "<tr class='encabezadotabla'>";
        echo "<td align='center' class='encabezadotabla'>Fecha Inicial</td>";
        echo "<td align='center' class='encabezadotabla'>Fecha Final</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='fila2'><input type='text' id='fecha_i' value='".date("Y-m")."-01' /></td>";
        echo "<td class='fila2'><input type='text' id='fecha_i' value='".date("Y-m")."-01' /><input type='text' id='fecha_i' value='".date("Y-m")."-01' /></td>";
        echo "</tr>";
        echo "</table>";

        echo "<input type=button id='btn_consultarfechas' value='Consultar' onClick='javascript:consultarFechas()' />";
        echo "<br><br>";

        echo '<div id="resultados_lista" align="center"></div>';
        echo "<br><br>";

        //------FIN FORMULARIO------
        echo "</div>";//Gran contenedor
        echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
        echo "<br><br>";

        echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
        echo "<br><br>";
        echo "<br><br>";
        //Mensaje de espera
        echo "<div id='msjEspere' style='display:none;'>";
        echo '<br>';
        echo "<img src='../../images/medical/ajax-loader5.gif'/>";
        echo "<br><br> Por favor espere un momento ... <br><br>";
        echo '</div>';
        //Mensaje de alertas
        echo "<div id='msjAlerta' style='display:none;'>";
        echo '<br>';
        echo "<img src='../../images/medical/root/Advertencia.png'/>";
        echo "<br><br><div id='textoAlerta'></div><br><br>";
        echo '</div>';
        echo '</center>';
    }

    function construirDivDetalle( $tipoDetalle, $keyElemento, $anio, $mes, $arr_det_elemento, $arr_det_pacientes ){
        /*echo "<pre>".print_r( $arr_det_elemento, true )."</pre>";
        echo "<pre>".print_r( $arr_det_pacientes, true )."</pre>";*/
        $div = "<div style='' align='center' class='fila2' id='div_det_{$tipoDetalle}_{$keyElemento}_{$anio}_{$mes}'>";
        $div .= "<span class='subtituloPagina2'>Detalle Historias reportadas </span><br>";
            if( count($arr_det_elemento) <= 0 ){
                $div .= "
                        <div id='div_det_{$tipoDetalle}_{$keyElemento}_{$anio}_{$mes}' style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' >
                            <br /><br />
                            [?] Sin pacientes que cumplan la caracteristica.
                        </div>";
            }else{
                $div .= "<br><br>";
                $div .= "<table>";
                    $div .= "<tr class='encabezadotabla'>";
                        $div .= "<td align='center' colspan='3'> Detalle - $tipoDetalle - $anio - $mes </td>";
                    $div .= "</tr>";
                    $div .= "<tr class='encabezadotabla'>";
                        $div .= "<td align='center'>Historia</td><td align='center'>Ingreso</td><td align='center'>Nombre</td>";
                    $div .= "</tr>";
                        foreach( $arr_det_elemento as $key => $datosHistoria ){
                            $datos    = explode( "-", $datosHistoria );
                            $nombre   = $arr_det_pacientes[$datosHistoria]['nombre'];
                            $div .= "<tr class='fila1'>";
                                $div .= "<td align='center'>{$datos[0]}</td><td align='center'>{$datos[1]}</td><td align='center'>{$nombre}</td>";
                            $div .= "</tr>";
                        }

                $div .= "</table>";
            }
        $div .= "<br><br></div>";

        return( $div );
    }

    function construirDivDetalleFaltante( $tipoDetalle, $keyElemento, $anio, $mes, $arr_det_elemento, $arr_det_pacientes, $arr_det_total_entidades){
        /*echo "<pre>".print_r( $arr_det_total_entidades, true )."</pre>";
        echo "<pre>".print_r( $arr_det_elemento, true )."</pre>";*/
        $arr_resultado = array_diff( $arr_det_total_entidades, $arr_det_elemento);
        //echo print_r($arr_resultado);
        $div = "<div style='' align='center' class='fila2' id='div_det_{$tipoDetalle}_{$keyElemento}_{$anio}_{$mes}'>";
        $div .= "<span class='subtituloPagina2'>Historias no reportadas respecto a entidades</span><br>";
            if( count($arr_resultado) <= 0 ){
                $div .= "
                        <div id='div_det_{$tipoDetalle}_{$keyElemento}_{$anio}_{$mes}' style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' >
                            <br /><br />
                            [?] Todos los pacientes reportados están presentes en esta sección.
                        </div>";
            }else{
                $div .= "<br><br>";
                $div .= "<table>";
                    $div .= "<tr class='encabezadotabla'>";
                        $div .= "<td align='center' colspan='3'> Detalle - $tipoDetalle - $anio - $mes </td>";
                    $div .= "</tr>";
                    $div .= "<tr class='encabezadotabla'>";
                        $div .= "<td align='center'>Historia</td><td align='center'>Ingreso</td><td align='center'>Nombre</td>";
                    $div .= "</tr>";
                        foreach( $arr_resultado as $key => $datosHistoria ){
                            $datos    = explode( "-", $datosHistoria );
                            $nombre   = $arr_det_pacientes[$datosHistoria]['nombre'];
                            $div .= "<tr class='fila1'>";
                                $div .= "<td align='center'>{$datos[0]}</td><td align='center'>{$datos[1]}</td><td align='center'>{$nombre}</td>";
                            $div .= "</tr>";
                        }

                $div .= "</table>";
            }
        $div .= "<br><br></div>";

        return( $div );
    }

    function consultarFechaLimiteConsulta( $fechaInicio ){
        $aux = strtotime( $fechaInicio."+1 month" ) - 24*60*60;
        $fechaMesAtras = date( "Y-m-d", $aux );
        return( $fechaMesAtras );
    }
?>
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

    #tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
    #tooltip h3, #tooltip div{margin:0; width:auto}
    .amarilloSuave{
        background-color: #F7D358;
    }
</style>

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
    //Variable de estado que indica si se esta moviendo un producto
    var moviendo_global = false;
    var datos_paciente = new Object();
    var mostrandoMenu = false;

    //************cuando la pagina este lista...**********//
    $(document).ready(function() {
        //agregar eventos a campos de la pagina
        $("#enlace_retornar").hide();
        $("#enlace_retornar").click(function() {
            restablecer_pagina();
        });

        $("#fecha_i, #fecha_f").datepicker({
          showOn: "button",
          buttonImage: "../../images/medical/root/calendar.gif",
          buttonImageOnly: true,
          maxDate:"+0D"
        });

        /*$("input[type='radio']").on(
            'click', function( event ){
            if( $(this).val() != "" ){
                $("#chk_mostrarDetalle_datos").attr( "checked", false );
            }
        );*/

        $("input[type='radio']").on( "click", function(){
            if( $(this).val() != "" && $("#chk_mostrarDetalle_datos").is(":checked") ){
                $("#chk_mostrarDetalle_datos").attr( "checked", false );
                validarMostrarDetalle( $("#chk_mostrarDetalle_datos") );
            }
        });

        buscarEntidades();
        buscarEspecialidad();
        buscarProcedimiento();
    });


    function buscarEntidades(){
        var wbasedato = $("#wbasedato").val();
        var wemp_pmla = $("#wemp_pmla").val();
        $("#lista_entidades").autocomplete("rep_egreso.php?consultaAjax=&accion=consultarEntidad&wbasedato="+wbasedato+"&wemp_pmla="+wemp_pmla,
        {
            cacheLength:1,
            delay:300,
            max: 100,
            scroll: false,
            scrollHeight: 500,
            matchSubset: false,
            matchContains: true,
            width:250,
            autoFill:false,
            minChars: 3,
            json:"json",
            formatItem: function(data, i, n, value) {
                //convierto el string en json
                eval( "var datos = "+data );
                return datos[0].usu;    //Eso es lo que se muestra al usuario
            },
            formatResult: function(data, value){
                //convierto el string en json
                eval( "var datos = "+data );
                return datos[0].valor.des;
            }
        }).result(
            function(event, item ){
                eval( "var datos = "+item );
                //Guardo el ultimo valor que selecciona el usuario
                //this.parentNode.parentNode El tr que contiene el input
                $( "input[type=text]", this.parentNode ).eq(0).val( datos[0].valor.des ).removeClass("inputblank");;
                this._lastValue = this.value;
                $( "input[type=hidden]", this.parentNode ).eq(0).val( datos[0].valor.cod );
                $( "input[type=text]", this.parentNode ).removeClass( "campoRequerido" );
                //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar

            }
        ).on({
            change: function(){
                var cmp = this;
                setTimeout( function(){
                    //Pregunto si la pareja es diferente
                    if( ( ( cmp._lastValue && cmp._lastValue != cmp.value )  )
                        || ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
                    )
                    {
                        alert( "Digite una entidad válida" );
                        $( "input[type=hidden]", cmp.parentNode ).val( '' );
                        cmp.value = '';
                        cmp.focus();
                    }
                }, 200 );
            }
        });
    }

    function buscarEspecialidad(){
        var wbasedato = $("#wbasedato").val();
        var wemp_pmla = $("#wemp_pmla").val();
        $("#lista_especialidad").autocomplete("rep_egreso.php?consultaAjax=&accion=consultarEspecialidad&wbasedato="+wbasedato+"&wemp_pmla="+wemp_pmla,
        {
            cacheLength:1,
            delay:300,
            max: 100,
            scroll: false,
            scrollHeight: 500,
            matchSubset: false,
            matchContains: true,
            width:250,
            autoFill:false,
            minChars: 3,
            json:"json",
            formatItem: function(data, i, n, value) {
                //convierto el string en json
                eval( "var datos = "+data );
                return datos[0].usu;    //Eso es lo que se muestra al usuario
            },
            formatResult: function(data, value){
                //convierto el string en json
                eval( "var datos = "+data );
                return datos[0].valor.des;
            }
        }).result(
            function(event, item ){
                eval( "var datos = "+item );
                //Guardo el ultimo valor que selecciona el usuario
                //this.parentNode.parentNode El tr que contiene el input
                $( "input[type=text]", this.parentNode ).eq(0).val( datos[0].valor.des ).removeClass("inputblank");;
                this._lastValue = this.value;
                $( "input[type=hidden]", this.parentNode ).eq(0).val( datos[0].valor.cod );
                $( "input[type=text]", this.parentNode ).removeClass( "campoRequerido" );
                //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar

            }
        ).on({
            change: function(){
                var cmp = this;
                setTimeout( function(){
                    //Pregunto si la pareja es diferente
                    if( ( ( cmp._lastValue && cmp._lastValue != cmp.value )  )
                        || ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
                    )
                    {
                        alert( "Digite una entidad válida" );
                        $( "input[type=hidden]", cmp.parentNode ).val( '' );
                        cmp.value = '';
                        cmp.focus();
                    }
                }, 200 );
            }
        });
    }

    function buscarProcedimiento(){
        var wbasedato = $("#wbasedato").val();
        var wemp_pmla = $("#wemp_pmla").val();
        $("#lista_procedimientos").autocomplete("rep_egreso.php?consultaAjax=&accion=consultarProcedimientos&wbasedato="+wbasedato+"&wemp_pmla="+wemp_pmla,
        {
            cacheLength:1,
            delay:300,
            max: 100,
            scroll: false,
            scrollHeight: 500,
            matchSubset: false,
            matchContains: true,
            width:250,
            autoFill:false,
            minChars: 3,
            json:"json",
            formatItem: function(data, i, n, value) {
                //convierto el string en json
                eval( "var datos = "+data );
                return datos[0].usu;    //Eso es lo que se muestra al usuario
            },
            formatResult: function(data, value){
                //convierto el string en json
                eval( "var datos = "+data );
                return datos[0].valor.des;
            }
        }).result(
            function(event, item ){
                eval( "var datos = "+item );
                //Guardo el ultimo valor que selecciona el usuario
                //this.parentNode.parentNode El tr que contiene el input
                $( "input[type=text]", this.parentNode ).eq(0).val( datos[0].valor.des ).removeClass("inputblank");;
                this._lastValue = this.value;
                $( "input[type=hidden]", this.parentNode ).eq(0).val( datos[0].valor.cod );
                $( "input[type=text]", this.parentNode ).removeClass( "campoRequerido" );
                //se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar

            }
        ).on({
            change: function(){
                var cmp = this;
                setTimeout( function(){
                    //Pregunto si la pareja es diferente
                    if( ( ( cmp._lastValue && cmp._lastValue != cmp.value )  )
                        || ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
                    )
                    {
                        alert( "Digite una entidad válida" );
                        $( "input[type=hidden]", cmp.parentNode ).val( '' );
                        cmp.value = '';
                        cmp.focus();
                    }
                }, 200 );
            }
        });
    }

    function mostrar_ocultar( ele, clase_tabla ){
        ele = jQuery(ele); //ele es el td
        ele = ele.parent(); //ele es el tr que contiene el td
        ele = ele.next(); //ele es el tr que le sigue
        //ele.toggle();
        if( ele.is(":visible") ){ //El tr con las tablas de los detalles esta visible
            if( ele.find("."+clase_tabla).is(":visible") ){ //Di click para cerrar la tabla del mismo detalle
                ele.hide(); //Oculto el tr que contiene las tablas
            }else{  //Di click para ver otro detalle
                ele.find("table").hide(); //oculto todas las tablas
                ele.find("."+clase_tabla).show(); //muestro la tabla del detalle que solicite
            }
        }else{
            ele.find("table").hide(); //oculto todas las tablas
            ele.show(); //Muestro el tr que contiene las tablas
            ele.find("."+clase_tabla).show();
        }

        if( ele.find("."+clase_tabla).length == 0 ){        //La tabla no existe, hay que traerla con AJAX
            var datos_ocultos = ele.find(".json_oculto").val();
            var wemp_pmla = $("#wemp_pmla").val();
            $.blockUI({ message: $('#msjEspere') });

            $.post('rep_egreso.php', { wemp_pmla: wemp_pmla, accion: "consultartabla", datos: datos_ocultos, consultaAjax: "0"} ,
                function(data) {
                    $.unblockUI();
                    ele.find("table").eq(0).after( data );  //Pegar el data (que debe contener la tabla que traje) luego de la primer tabla que haya en el tr oculto
                    ele.find("."+clase_tabla).show();
                });
            return;
        }
        ele.find("."+clase_tabla).show();
    }

    function consultarFechas(){
       var filtroParticular = true;//--> si se hace algún tipo de filtro niego inmediatamente el guardado en la tabla de histórico(000235)
       var wfechai          = $("#fecha_i").val();
       var wfechaf          = $("#fecha_f").val();
       var wemp_pmla        = $("#wemp_pmla").val();
       var grupo            = $("#lista_grupos").val();
       var tipoGrupo        = $("#lista_grupos").find("option:selected").attr("tipoOpcion");
       var numFiltros       = $(".chk_criterioPpal:checked").length;
       var parametrosObj    = {};
       var reemplazar       = $("#chk_reemplazar_datos");

       if( $(reemplazar).is(":checked") ){
        var wreemp = "on";
       }else{
        var wreemp = "off";
       }

       if( $("#chk_mostrarDetalle_datos").is(":checked") ){
        var mostrarDetalle = "on";
       }else{
        var mostrarDetalle = "off";
       }

       if( numFiltros > 0 ){
            var filtroParticular = false;
            $(".chk_criterioPpal:checked").each(function(){
                var temaConsultar = $(this).attr("name").split("_");
                    temaConsultar = temaConsultar[1];
                    parametrosObj[temaConsultar] = {};
                    if( $(this).attr("aplicaSubtipos") == "on" ){
                        parametrosObj[temaConsultar]['tipo'] = $(this).parent().next("td").find("input[type='radio']:checked").val();
                    }else{
                        parametrosObj[temaConsultar]['tipo'] = "";
                    }
            });
       }
       var parametros = $.toJSON( parametrosObj );

       var tipoProcedimiento = $("input[name='radio_tipoProcedimiento']:checked").val();
       var tipoEspecialidad  = $("input[name='radio_tipoEspecialidad']:checked").val();



        $.blockUI({ message: $('#msjEspere') });

        $.blockUI({ message: $('#msjEspere') });
        $.post('rep_egreso.php', { wemp_pmla: wemp_pmla,
                                            accion: "consultarestadisticas",
                                           wfechai: wfechai,
                                           wfechaf: wfechaf,
                                            wgrupo: grupo,
                                      consultaAjax: "0",
                                        wtipoGrupo: tipoGrupo,
                                wcriteriosConsulta: parametros,
                                            wreemp: wreemp,
                                    mostrarDetalle: mostrarDetalle
            } ,
            function(data) {
                $.unblockUI();
                $('#resultados_lista').html(data);
                $("#resultados_lista").show();
                //$("#btn_consultarfechas").hide();
                $(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
                $( ".desplegables" ).accordion({
                    collapsible: true,
                    active:0,
                    heightStyle: "content",
                    icons: null
                });
            });
    }

    function usarFiltros(){
        var wfechai = $("#fecha_i").val();
        var wfechaf = $("#fecha_f").val();
        var wemp_pmla = $("#wemp_pmla").val();

        var grupo = $("#lista_grupos").val();
        var procedencia = $("#lista_procedencia").val();
        var entidad = $("#buscar_entidad").val();
        var especialidad = $("#buscar_especialidad").val();
        var procedimiento = $("#buscar_procedimiento").val();

        $.blockUI({ message: $('#msjEspere') });

        $.post('rep_egreso.php', { wemp_pmla: wemp_pmla, accion: "consultarestadisticasfiltros", wfechai: wfechai, wfechaf: wfechaf, grupo:grupo,
        procedencia:procedencia, entidad:entidad, especialidad:especialidad, procedimiento:procedimiento, consultaAjax: "0"} ,
            function(data) {
                $.unblockUI();
                $('#resultados_lista').html(data);
                $("#resultados_lista").show();
                //$("#btn_consultarfechas").hide();
                $(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
                $( ".desplegables" ).accordion({
                    collapsible: true,
                    active:0,
                    heightStyle: "content",
                    icons: null
                });
            });
    }

    //Funcion que se activa cuando se presiona el enlace "retornar"
    function restablecer_pagina(){
        //Si esta visible la tabla de menu...
        $("#lista_pisos option").eq(0).attr('selected',true); //llevar la opcion 0 a la lista de pisos
        $("#resultados_lista").hide( 'drop', {}, 500 ); //esconder lista de pacientes
        $("#enlace_retornar").hide(); //esconder enlace retornar
    }


    function alerta( txt ){
        $("#textoAlerta").text( txt );
        $.blockUI({ message: $('#msjAlerta') });
            setTimeout( function(){
                            $.unblockUI();
                        }, 1600 );
    }

    function dibujarGraficoEntidades(){
        $("#contenedor_graf_entidad").show();
        $('html, body').animate({ scrollTop: $('#contenedor_graf_entidad').offset().top }, 'slow');
        $("#tabla_grafico_entidades").LeerTablaAmericas({
            empezardesdefila : 0,
            titulo           : 'Usuarios por entidad' ,
            tituloy          : 'cantidad',
            datosadicionales : 'todo',
            rotulos          : 'si',
            tipografico      : 'torta',
            gradoderotulos   : 90,
            dimension        : '3d',
            divgrafica       : 'div_grafica_entidades'
        });
        if( $("#fuente_entidades").length == 0 ){
            $("#div_grafica_entidades").after("<span id='fuente_entidades'>Fuente: Clínica Las Américas. Cuadro de usuarios por entidad "+$("#mensaje_fuente").text()+"</span><br>");
        }
    }

    function dibujarGraficodiagnosticos(){
        $("#contenedor_graf_diagnostico").show();
        $('html, body').animate({ scrollTop: $('#contenedor_graf_diagnostico').offset().top }, 'slow');
        $("#tabla_grafico_diagnosticos").LeerTablaAmericas({
            empezardesdefila : 0,
            titulo           : 'Diagnosticos' ,
            tituloy          : 'cantidad',
            datosadicionales : 'todo',
            rotulos          : 'si',
            tipografico      : 'torta',
            gradoderotulos   : 90,
            dimension        : '3d',
            divgrafica       : 'div_grafica_diagnosticos'
        });
        if( $("#fuente_diagnostico").length == 0 ){
            $("#div_grafica_diagnosticos").after("<span id='fuente_diagnostico'>Fuente: Clínica Las Américas. Cuadro de Diagnosticos "+$("#mensaje_fuente").text()+"</span><br>");
        }
    }


    function dibujarGraficoEspecialidades(){
        $("#contenedor_graf_especialidad").show();
        $('html, body').animate({ scrollTop: $('#contenedor_graf_especialidad').offset().top }, 'slow');
        $("#tabla_grafico_especialidades").LeerTablaAmericas({
            empezardesdefila : 0,
            titulo           : 'Egresos por especialidad' ,
            tituloy          : 'cantidad',
            datosadicionales : 'todo',
            rotulos          : 'si',
            tipografico      : 'column',
            gradoderotulos   : 90,
            dimension        : '3d',
            divgrafica       : 'div_grafica_especialidades'
        });
        if( $("#fuente_especialidades").length == 0 ){
            $("#div_grafica_especialidades").after("<span id='fuente_especialidades'>Fuente: Clínica Las Américas. Cuadro de egresos por especialidad "+$("#mensaje_fuente").text()+"</span><br>");
        }
    }

    function dibujarGraficoProcedimientos(){
        $("#contenedor_graf_procedimientos").show();
        $('html, body').animate({ scrollTop: $('#contenedor_graf_procedimientos').offset().top }, 'slow');
        $('#contenedor_graf_procedimientos').html("<div id='div_grafica_procedimientos' style='border: 1px solid #999999; width:600px; height:450px;'></div>");
        $("#tabla_grafico_procedimientos").LeerTablaAmericas({
            empezardesdefila : 0,
            titulo           : 'Procedimientos realizados' ,
            tituloy          : 'cantidad',
            datosadicionales : 'todo',
            rotulos          : 'si',
            tipografico      : 'linea',
            gradoderotulos   : 90,
            dimension        : '3d',
            divgrafica       : 'div_grafica_procedimientos'
        });
        if( $("#fuente_procedimientos").length == 0 ){
            $("#div_grafica_procedimientos").after("<span id='fuente_procedimientos'>Fuente: Clínica Las Américas. Cuadro de procedimientos realizados "+$("#mensaje_fuente").text()+"</span><br>");
        }
    }

    function dibujarGraficoProcedimientos2(){
        $("#contenedor_graf_procedimientos").show();
        $('html, body').animate({ scrollTop: $('#contenedor_graf_procedimientos').offset().top }, 'slow');
        $('#contenedor_graf_procedimientos').html("<div id='div_grafica_procedimientos' style='border: 1px solid #999999; width:600px; height:450px;'></div>");
        $("#tabla_grafico_procedimientos2").LeerTablaAmericas({
            empezardesdefila : 0,
            titulo           : 'Egresos por procedimientos' ,
            tituloy          : 'cantidad',
            datosadicionales : 'todo',
            rotulos          : 'si',
            tipografico      : 'column',
            gradoderotulos   : 90,
            dimension        : '3d',
            divgrafica       : 'div_grafica_procedimientos'
        });
        if( $("#fuente_procedimientos").length == 0 ){
            $("#div_grafica_procedimientos").after("<span id='fuente_procedimientos'>Fuente: Clínica Las Américas. Cuadro de procedimientos realizados "+$("#mensaje_fuente").text()+"</span><br>");
        }
    }

    function dibujarGraficoProcedencia(){
        $("#contenedor_graf_procedencia").show();
        $('html, body').animate({ scrollTop: $('#contenedor_graf_procedencia').offset().top }, 'slow');
        $('#contenedor_graf_procedencia').html("<div id='div_grafica_procedencia' style='border: 1px solid #999999; width:600px; height:450px;'></div>");
        $("#tabla_grafico_procedencia").LeerTablaAmericas({
            empezardesdefila : 0,
            titulo           : 'Procedencia de pacientes atendidos' ,
            tituloy          : 'cantidad',
            datosadicionales : 'todo',
            rotulos          : 'si',
            tipografico      : 'linea',
            gradoderotulos   : 90,
            dimension        : '3d',
            divgrafica       : 'div_grafica_procedencia'
        });
        if( $("#fuente_precedencia").length == 0 ){
            $("#div_grafica_procedencia").after("<span id='fuente_precedencia'>Fuente: Clínica Las Américas. Cuadro de procedencia de pacientes atendidos "+$("#mensaje_fuente").text()+"</span><br>");
        }
    }

    function dibujarGraficoProcedencia2(){
        $("#contenedor_graf_procedencia").show();
        $('html, body').animate({ scrollTop: $('#contenedor_graf_procedencia').offset().top }, 'slow');
        $('#contenedor_graf_procedencia').html("<div id='div_grafica_procedencia' style='border: 1px solid #999999; width:600px; height:450px;'></div>");
        $("#tabla_grafico_procedencia2").LeerTablaAmericas({
            empezardesdefila : 0,
            titulo           : 'Egresos por procedencia' ,
            tituloy          : 'cantidad',
            datosadicionales : 'todo',
            rotulos          : 'si',
            tipografico      : 'column',
            gradoderotulos   : 90,
            dimension        : '3d',
            divgrafica       : 'div_grafica_procedencia'
        });
        if( $("#fuente_precedencia").length == 0 ){
            $("#div_grafica_procedencia").after("<span id='fuente_procedimientos'>Fuente: Clínica Las Américas. Cuadro de procedimientos realizados "+$("#mensaje_fuente").text()+"</span><br>");
        }
    }

    function ocultarDiv( idElemento ){
        $("#"+idElemento).hide();
    }

    function deshabilitarFiltro( obj ){
        if( $(obj).attr("name") == "radio_tipoProcedimiento" && $(obj).val() != "" ){
            $("input[type='radio'][name='radio_tipoEspecialidad'][value='']").click();
        }
        if( $(obj).attr("name") == "radio_tipoEspecialidad" && $(obj).val() != "" ){
            $("input[type='radio'][name='radio_tipoProcedimiento'][value='']").click();
        }
    }

    function validarMostrarDetalle ( obj ){

        if( $( obj ).is(":checked") ){
            $("#chk_reemplazar_datos").attr( "checked", false );
            $("#fecha_i").val( $("#hoy").val() );
            $("#fecha_f").val( $("#hoy").val() );
            $("#fecha_i").datepicker( "destroy" );
            $("#fecha_f").datepicker( "destroy" );
            $("#fecha_i").datepicker({
              showOn: "button",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
              maxDate:"+0D",
              onSelect: function(dateText, inst ) {
                   consultarFinalPermitida( dateText );
              }
            });
            $("input[type='radio']").attr("checked", false);
            $("input[type='radio'][value='']").attr("checked", true);

        }else{
            $("#fecha_i").val( $("#iniMes").val() );
            $("#fecha_f").val( $("#hoy").val() );
            $("#fecha_i").datepicker( "destroy" );
            $("#fecha_f").datepicker( "destroy" );
            $("#fecha_i, #fecha_f").datepicker({
              showOn: "button",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
              maxDate:"+0D"
            });
        }
    }

    function mostrarDetalle( obj, tipo, keyElemento, anio, mes ){

        if( $(obj).attr("mostrando") == "off" ){
            //--> ocultar todo en esta tabla
            $( obj ).parent().parent().find("tr[id^='tr_det_']").find("div").hide();
            $( obj ).parent().parent().find("tr[id^='tr_det_']").hide();
            $( obj ).parent().parent().find("td[mostrando]").removeClass("amarilloSuave");
            $( obj ).parent().parent().find("td[mostrando]").attr("mostrando","off");

            //---> mostrar solo lo que se quiere ver
            $( obj ).parent().next("tr").show();
            $( obj ).parent().next("tr").find("div[id='div_det_"+tipo+"_"+keyElemento+"_"+anio+"_"+mes+"']").show();
            //div_det_diagnosticos_total_2015_09
            if( $("#mostrarDetalleFaltantes").val() == "on" && keyElemento == "total" ){
                $( obj ).parent().next("tr").find("div[id='div_det_"+tipo+"_faltante_"+anio+"_"+mes+"']").show();
            }
            $( obj ).attr("mostrando", "on");
            $( obj ).addClass("amarilloSuave");

        }else{
            $( obj ).parent().next("tr").hide();
            $( obj ).parent().next("tr").find("div").hide();
            $( obj ).parent().find("td[mostrando]").attr("mostrando","off");
            $( obj ).parent().find("td[mostrando]").removeClass("amarilloSuave");
        }
    }

    function consultarFinalPermitida( fechaInicio ){

        var rango_superior      = 245;
        var rango_inferior      = 11;
        var aleatorio           = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

        $.ajax({
            url     : "rep_egreso.php",
            type    : "POST",
            async   : true,
            data    : {
                        accion            : "consultarFechaLimiteConsulta",
                        consultaAjax      : "si",
                        fechaInicio       : fechaInicio,
                        wemp_pmla         : $("#wemp_pmla").val()
                      },
            success : function(data){

                    $("#fecha_f").val(data);
                    $("#fecha_f").datepicker( "destroy" );
                    $("#fecha_f").datepicker({
                          showOn: "button",
                          buttonImage: "../../images/medical/root/calendar.gif",
                          buttonImageOnly: true,
                          maxDate: data,
                          minDate: fechaInicio+" -1D"
                    });
            }
        });
    }
</script>
</head>
    <body>
        <!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
        <?php
            $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
            $empresa = strtolower($institucion->baseDeDatos);
            encabezado("REPORTES DE EGRESO", $wactualiz, $empresa);
            $mesAtras = date( "Y-m-d", strtotime( date("Y-m-d")."-1 month" ) );
            $iniMes   = date('Y-m')."-01";
            $hoy      = date('Y-m-d');
        ?>
        <input type='hidden' id ='wemp_pmla' value='<?=$wemp_pmla?>'/>
        <input type='hidden' id ='mesAtras'  value='<?=$mesAtras?>'/>
        <input type='hidden' id ='iniMes'    value='<?=$iniMes?>'/>
        <input type='hidden' id ='hoy'       value='<?=$hoy?>'/>
        <center>
            <span class='subtituloPagina2'>Parámetros de consulta</span>
        </center><br><br>
        <div style='width:100%' align='center'>
            <table style='width:40%;'>
                <tr>
                    <th align='right' colspan='2'><span class='subtituloPagina2'><font size='1'>No seleccionar ningún criterio principal<br> consultará toda la información disponible</font></span></th>
                </tr>
                <tr class='encabezadotabla'>
                    <th align='center'> CRITERIO PRINCIPAL </th>
                    <th align='center'> SUBCATEGORIA </th>
                </tr>
                <tr class='fila2'>
                    <td align='left' style='width:40%;'> <input type='checkbox' name='chk_diagnostico' aplicaSubtipos='on' class='chk_criterioPpal' id='wdiag'> Diagnósticos  </td>
                    <td align='center' nowrap='nowrap'> <input type='radio' name='rad_diag' value='P'> Primarios <input type='radio' name='rad_diag' value='S'> Secundarios <input type='radio' name='rad_diag' checked value=''> Todo </td>
                </tr>
                <tr class='fila2'>
                    <td align='left' style='width:40%;'> <input type='checkbox' name='chk_procedimientos' aplicaSubtipos='on' class='chk_criterioPpal' id='wproc'> Procedimientos</td>
                    <td align='center' nowrap='nowrap'> <input type='radio' name='rad_proc' value='P'> Primarios <input type='radio' name='rad_proc' value='S'> Secundarios <input type='radio' name='rad_proc' checked value=''> Todo </td>
                </tr>
                <tr class='fila2'>
                    <td align='left' style='width:40%;'> <input type='checkbox' name='chk_especialidad' aplicaSubtipos='on' class='chk_criterioPpal' id='wesp'> Especialidades</td>
                    <td align='center' nowrap='nowrap'> <input type='radio' name='rad_espe' value='P'> Primarios <input type='radio' name='rad_espe' value='S'> Secundarios <input type='radio' name='rad_espe' checked value=''> Todo </td>
                </tr>
                <tr class='fila2'>
                    <td align='left' style='width:40%;'> <input type='checkbox' name='chk_entidad' aplicaSubtipos='off' class='chk_criterioPpal' id='went'> Entidades      </td>
                    <!--<td align='center' nowrap='nowrap'> <input type='radio' name='rad_enti' value='P'> Primarios <input type='radio' name='rad_enti' value='S'> Secundarios <input type='radio' name='rad_enti' value=''> TODOS </td>-->
                    <td align='center' nowrap='nowrap'> NO APLICA </td>
                </tr>
                <tr class='fila2'>
                    <td align='left' style='width:40%;'> <input type='checkbox' name='chk_procedencia' aplicaSubtipos='off' class='chk_criterioPpal' id='wbar'> Procedencia( Barrios )      </td>
                    <!--<td align='center' nowrap='nowrap'> <input type='radio' name='rad_bar' value='P'> Primarios <input type='radio' name='rad_bar' value='S'> Secundarios <input type='radio' name=='rad_bar' value=''> TODOS </td>-->
                    <td align='center' nowrap='nowrap'> NO APLICA </td>
                </tr>
                <tr class='encabezadotabla'>
                    <th align='center' colspan='2'> SERVICIOS </th>
                </tr>
                <tr>
                    <td colspan='2' align='center' class='fila2'>
                        <select id='lista_grupos'  align='center' style='margin:5px; width:100%;'>
                            <option value=''>TODOS</option>";
                            <?php
                        foreach ($wgruposccos as $codigog=>$nombreg){ ?>
                            <option value='<?=$codigog?>' tipoOpcion='grupo'><?=$nombreg?></option>
                        <?php } ?>
                            <?php
                        foreach ($wccosCirIndividuales as $codigoCco=>$nombreCco){ ?>
                            <option value='<?=$codigoCco?>' tipoOpcion='cco'><?=$nombreCco?></option>
                        <?php }?>
                            <option value='SS'> Sin Servicio Registrado </option>>
                        </select>
                    </td>
                </tr>
                <tr class='encabezadotabla' colspan='2'>
                    <th colspan='2' align='center'>RANGO DE FECHAS</th>
                </tr>
                <tr class='fila2'>
                    <td align='center' colspan='2' style='width:40%;'> <input type='text' disabled id='fecha_i' value='<?php echo date('Y-m')."-01"; ?>'>&nbsp;&nbsp;  HASTA &nbsp;&nbsp;<input type='text' disabled id='fecha_f' value='<?php echo date('Y-m-d'); ?>' /> </td>
                </tr>
                <tr class='fila2'>
                    <td align='center' colspan='2' style='width:40%;'> <input type='checkbox' id='chk_mostrarDetalle_datos' value='' onclick='validarMostrarDetalle( this )'> MOSTRAR DETALLE ( Solo permite un mes del dia de hoy a atrás) </td>
                </tr>
                <tr class='fila2'>
                    <td align='center' colspan='2' style='width:40%;'> <input type='checkbox' id='chk_reemplazar_datos' value='on'> REEMPLAZAR DATOS EN HISTÓRICO </td>
                </tr>
            </table>
        </div><br>
       <center>
        <input type=button id='btn_consultarfechas' value='Consultar' onClick='javascript:consultarFechas()' />
        <br><br>
        <div id="resultados_lista" align="center"></div>
        <br><br>
        <a id='enlace_retornar' href='#' >RETORNAR</a>
        <br><br>
        <input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />
        <br><br>
        <br><br>
        <div id='msjEspere' style='display:none;'>
            <br>
            <img src='../../images/medical/ajax-loader5.gif'/>
            <br><br> Por favor espere un momento ... <br><br>
        </div>
       <div id='msjAlerta' style='display:none;'>
            <br>
            <img src='../../images/medical/root/Advertencia.png'/>
            <br><br><div id='textoAlerta'></div><br><br>
       </div>
    </body>
</html>