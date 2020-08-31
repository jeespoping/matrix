<?php
include_once("conex.php");
/** REPORTE DE FACTURAS RADICADAS ANTE LAS EMPRESAS RESPONSABLES DE PAGO, ERP, EPS, EPS-S, ETC **/
/**
*** FECHA DE CREACIÓN: 2014-04-03
*** AUTOR: ING. Camilo Zapata Z.
*** DESCRIPCION: Este reporte presenta los datos de la sabana (Monitoreo ventilatorio y Monitoreo Hemodinámico ).
*** ACTUALIZACIONES:
***2016-07-13  camilo zapata: se modifica el programa implementando el parámetro sabthd de la tabla 202, que indica si debe tener en cuenta la fecha y la hora data a la hora de mostrar el reporte
*                             de un formulario específico, esto porque hay formularios que se guardan electrónicamente desde un dispositivo, que no puede seleccionar del campo hora indicado por el estandar.
*                             así pues, por ejemplo, el formulario hce_305 tomará la hora data como hora del monitoreo, a menos de que el campo seleccion que indica la hora esté debidamente diligenciado.
***2016-05-12  Camilo Zapata : Los formularios a mostrar en la sabana y los respectivos campos a  partir de ahora se consultaran en un maestro (movhos_000202) en lugar de la tabla root_51
*
*** 2014-08-06 (camilo Zapata): Se modificó el programa para que no trabaje con la fecha data, como fecha del monitoreo, sino que utiliza
***                             el campo establecido como parámetro. Adicionalmente se dió una holgura de un dia para grabar monitoreos del dia
***                             anterior.
***2014-09-09  Camilo Zapata : Se agregó la condicion de graficar datos que sean tipo "Formula".
**/
?>
<?php
if(!isset($_SESSION['user'])){
    echo "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
        [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
        </div>";
    return;
}
include_once( "conex.php" );



$pos   = strpos($user,"-");
$wuser = substr($user,$pos+1,strlen($user));

/** INICIO DE FUNCIONES **/

//--> funcion que construye un arreglo con los datos generales del reporte( simulando un objeto ), el nombre, el código, etc.
function arregloReportes(){
    global $conex;
    global $whce;
    global $wcodFormularios;
    $aux           = explode( ",", $wcodFormularios);
    $arrayReportes = array();
    foreach ($aux as $i => $value) {
        $wcodForm = $aux[$i];
        $query  = "SELECT Encdes, Enccol
                     FROM {$whce}_000001
                    WHERE Encpro = '{$wcodForm}'
                      AND Encest = 'on'";
        $rs     = mysql_query( $query, $conex );
        $row    = mysql_fetch_array( $rs );

        $arrayReportes[$wcodForm]['nombre']   = strtoupper($row['Encdes']);
        $arrayReportes[$wcodForm]['columnas'] = $row['Enccol'];
    }
    return( $arrayReportes );
}

//--> funcion que construye un formulario inicial para presentar en pantalla en caso de que no se acceda directamente desde hce.
function mostrarFormularioPrincipal(){
}

//--> se construyen los arreglos con la información de los campos de cada formulario, código, nombre, tipo de dato, etc
function construirArreglosContenedores( $wcodForm ){

    global $whce;
    global $wemp_pmla;
    global $whis;
    global $wing;
    global $conex;
    global $horaActual;
    global $horaDeCorte;
    global $hoy;
    global $ayer;
    global $paraHorMonitor;
    global $paraFecMonitor;
    global $paraThdMonitor;
    global $arregloTitulos;
    global $titulosAimprimir;
    global $columnasFormulario;
    global $consecutivosMemo;
    global $wmovhos;
    $arregloAuxiliar               = array();
    $consecutivosMemo[$wcodForm]          = array();

    $arreglosContenedores[$wcodForm]['horas'] = array();
    $arreglosContenedores[$wcodForm]['campos']= array();
    $arreglosContenedores[$wcodForm]['tipos']= array();

   /* echo $horaDeCorte."  -  ".$horaActual;
    echo "<pre>";
        print_r( $arreglosContenedores[$wcodForm]['horas'] );
    echo "</pre>"; */

    if( $horaActual*1 < $horaDeCorte*1 ){ // el arreglo de horas va con la fecha anterior y con la fecha actual;

        for( $i = $horaDeCorte*1; $i < 24; $i=$i+1){//se construye desde la hora de corte de ayer hasta la media noche
            $j = $i;

            if($j > 23){
                $j = $j - 24;
                $sufijo = '';
            }

            if($j >= 12)
                $sufijo = ''; //$sufijo = '- PM';
            if($j < 10){
                $prefijo = '0';
            }else{
                $prefijo = '';
            }
            $arreglosContenedores[$wcodForm]['horas'][$ayer]["{$prefijo}{$j}"]="{$prefijo}{$j}:00 {$sufijo}";
        }

        for( $i = 0 ; $i <= $horaActual*1 ; $i=$i+1){//se completa de la media noche hasta la hora actual del dia de hoy
            $j = $i;

            if($j > 23){
                $j = $j - 24;
                $sufijo = '';
            }

            if($j >= 12)
                $sufijo = ''; //$sufijo = '- PM';
            if($j < 10){
                $prefijo = '0';
            }else{
                $prefijo = '';
            }
            $arreglosContenedores[$wcodForm]['horas'][$hoy]["{$prefijo}{$j}"]="{$prefijo}{$j}:00 {$sufijo}";
        }

    }else{ // el arreglo de horas va solo con la fecha actual;
            for($i = $horaDeCorte*1; $i <= $horaActual*1; $i=$i+1){//se construye desde la hora de corte hasta la hora actual
                $j = $i;

                if($j > 23){
                    $j = $j - 24;
                    $sufijo = '';
                }

                if($j >= 12)
                    $sufijo = ''; //$sufijo = '- PM';
                if($j < 10){
                    $prefijo = '0';
                }else{
                    $prefijo = '';
                }
                $arreglosContenedores[$wcodForm]['horas'][$hoy]["{$prefijo}{$j}"]="{$prefijo}{$j}:00 {$sufijo}";
            }
    }

    $query = "SELECT Sabcam formulariosSabana, sabfes fechas_sabana, sabfer fechaRegistro, sabhor hora_registro_sabana, sabthd tomar_hora_data
                FROM {$wmovhos}_000202
               WHERE Sabfor = '{$wcodForm}'
                 AND Sabest = 'on'";

    $rs   = mysql_query( $query, $conex );
    $rowFor = mysql_fetch_assoc( $rs ) or die( mysql_error());

    $aux = $rowFor['fechas_sabana'];
    $aux = explode( ",", $aux );
    foreach ($aux as $i => $dato ) {
        array_push( $consecutivosMemo[$wcodForm], $dato );
    }

    $aux = $rowFor['formulariosSabana'];
    $aux = explode( ",", $aux );
    foreach ($aux as $i => $dato ) {
        $arreglosContenedores[$wcodForm]['campos'][$dato] = "";
    }

    $paraHorMonitor[$wcodForm] = $rowFor['hora_registro_sabana'];
    $paraFecMonitor[$wcodForm] = $rowFor['fechaRegistro'];
    $paraThdMonitor[$wcodForm] = $rowFor['tomar_hora_data'];

    foreach ( $arreglosContenedores[$wcodForm]['campos'] as $keyCampo => $nombre ){
        array_push( $arregloAuxiliar, "'".$keyCampo."'" );
    }
    unset( $arreglosContenedores[$wcodForm]['campos']);

    $camposBuscados = implode( ",", $arregloAuxiliar );
    $query = "SELECT Detcon, Detorp orden, Dettip, Detdes, Detnpa
                FROM {$whce}_000002
               WHERE Detpro = '{$wcodForm}'
                 AND Detcon in ( $camposBuscados )
                 AND Dettip != 'Texto'
                 AND Detest = 'on'"
            ."  UNION ALL"
            ." SELECT Detcon, Detorp orden, Dettip, Detdes, Detnpa
                 FROM {$whce}_000002
                WHERE Detpro = '{$wcodForm}'
                  AND Dettip in ('Titulo', 'Subtitulo')
                  AND Detest  = 'on'
             ORDER BY orden asc";
    $rs    = mysql_query( $query, $conex )or die(mysql_error());
    while( $row = mysql_fetch_array( $rs ) ){
        if( ( $row['Dettip'] == "Titulo" or $row['Dettip'] == "Subtitulo" ) and trim( $row['Detnpa'] ) == "" ){
            $row['Detnpa'] = "&nbsp;";
            $titulosAimprimir[$wcodForm][$row['Detcon']] = false;
        }
        $arreglosContenedores[$wcodForm]['campos'][$row['Detcon']] = $row['Detnpa'];
        $arreglosContenedores[$wcodForm]['tipos'][$row['Detcon']] = $row['Dettip'];
        $arreglosContenedores[$wcodForm]['descripcion'][trim($row['Detcon'])] = $row['Detdes'];
        $arreglosContenedores[$wcodForm]['nombres'][trim($row['Detcon'])] = ( $row['Detnpa'] );
    }


    /** CONSTRUCCIÓN DEL ARREGLO CON LOS TITULOS, SUBTITULOS, Y CAMPOS AGRUPADOS **/
    $query = " SELECT Detorp orden, Dettip, Detdes, Detnpa, Detcon, Detcoa
                 FROM {$whce}_000002
                WHERE Detpro = '{$wcodForm}'
                 AND  Detest = 'on'
                ORDER BY orden";
    $rs   = mysql_query( $query, $conex ) or die( mysql_error() );

    $arrayGrupos   = array();
    $clsAcumuladas = 8; //variable de control de consecutivos dentro de un grupo
    $numGrupo      = -1; //indice para ir creando grupos

    //---> se crean grupos de consecutivos basados en el número de columnas que ocupan dentro del formulario de historia clínica, para despues crear los arboles
    while( $row = mysql_fetch_array( $rs ) ){

        $tipo     = $row['Dettip'];
        $columnas = $row['Detcoa'];
        if('Dettip' != "Memo"){
            //echo " $columnas - $clsAcumuladas - $columnasFormulario <br>";

            if( $clsAcumuladas >= $columnasFormulario ){//se llenó el cupo dentro del grupo
                $numGrupo      ++;
                $clsAcumuladas = 0;
                $arrayGrupos[$numGrupo] = array();
             }
            $arrayGrupos[$numGrupo][$row['Detcon']]['tipo']           = $tipo;
            $arrayGrupos[$numGrupo][$row['Detcon']]['nombre']         = $row['Detnpa'];
            $arrayGrupos[$numGrupo][$row['Detcon']]['columnas']       = $row['Detcoa'];
            $arrayGrupos[$numGrupo][$row['Detcon']]['tienePadre']     = false;
            $arrayGrupos[$numGrupo][$row['Detcon']]['padre']          = "";
            $arrayGrupos[$numGrupo][$row['Detcon']]['consecutivo']    = $row['Detcon'];
            $arrayGrupos[$numGrupo][$row['Detcon']]['hijosCompletos'] = false;
            $clsAcumuladas = $clsAcumuladas + $columnas;
        }else{
            //--> aca se arma el arreglo de los memo
        }
    }
    armarArbolConsecutivo( $arrayGrupos, $wcodForm, 0, $columnasFormulario );

    return($arreglosContenedores);
}

function armarArbolConsecutivo( &$grupos ,$wcodForm, $grupoInicio, $columnasFormulario, &$elementoPadre = array() ){

    global $arregloTitulos;
    $parar          = false;
    $pararXtitulo = false;

    foreach( $grupos[$grupoInicio] as $consecutivo => &$elemento ){

        if($pararXtitulo)
            break;
        if( $elemento['tipo'] != "Titulo" and $elemento['tipo'] != "Subtitulo" ){
            $elemento['tipo'] = "dato";
        }
        $tipo          = $elemento['tipo'];
        $columnas      = $elemento['columnas'];
        $columnasHijas = 0;
        $columnasHijas2= 0;
        $parar  = false;
        if( $tipo == "Titulo" or $tipo == "Subtitulo" ){//-> si el campo es titulo busco en los grupos siguientes todos los hijos


            $sonTitulos     = false;
            $estaSegmentado = false;
            $elementosEnSig = 0;

            if(!isset( $grupos[$grupoInicio+1] ) )
                $grupos[$grupoInicio+1] = array();

            foreach(  $grupos[$grupoInicio+1] as $consecutivoHijo => $elementoHijo ) {//este for tiene como proposito ver si lo que hay son titulos y/o subtitulos
                if( $elementoHijo['tipo'] == "Subtitulo" or $elementoHijo['tipo'] == "Titulo" )
                    $sonTitulos == true;
                $elementosEnSig++;
            }

            if( $elementosEnSig and $sonTitulos ){
                $estaSegmentado = true;
            }

            for ( $grupo = $grupoInicio+1;  $grupo < count( $grupos ) ;  $grupo++ ) {

                if( !$elemento['hijosCompletos'] ){

                    $parar = false;

                    foreach ( $grupos[$grupo] as $consecutivoHijo => &$elementoHijo ) {

                        if( $elementoHijo['tipo'] != "Titulo" and $elementoHijo['tipo'] != "Subtitulo" ){
                            $elementoHijo['tipo'] = "dato";
                        }
                        if( $parar )
                            break;

                        if( !$elementoHijo['tienePadre'] and $elementoHijo['tipo'] != "Titulo" ){

                            if( $elementoHijo['tipo'] == "Subtitulo" and $elementoHijo['columnas'] == $elementoPadre['columnas'] ){

                                if( $elementoPadre['tipo'] == "Titulo"){
                                    $arregloTitulos[$wcodForm][$elementoPadre['consecutivo']]['hijos'][$consecutivoHijo]['tipo'] = $elementoHijo['tipo'];
                                    $elementoHijo['tienePadre']                                                          = true;
                                    $elementoHijo['padre']                                                               = $elementoPadre['consecutivo'];
                                }else{
                                    $arregloTitulos[$wcodForm][$elementoPadre['padre']]['hijos'][$consecutivoHijo]['tipo'] = $elementoHijo['tipo'];
                                    $elementoHijo['tienePadre']                                                    = true;
                                    $elementoHijo['padre']                                                         = $elementoPadre['padre'];
                                    $parar = true;
                                }


                            }else{

                                if( !$elemento['hijosCompletos'] ){
                                    $arregloTitulos[$wcodForm][$consecutivo]['hijos'][$consecutivoHijo]['tipo'] = $elementoHijo['tipo'];
                                    $elementoHijo['tienePadre']                                         = true;
                                    $elementoHijo['padre']                                              = $consecutivo;
                                    $columnasHijas2 = $columnasHijas2 + $elementoHijo['columnas'];
                                }

                                if( ( $sonTitulos and $estaSegmentado and $elemento['columnas']*1 == $columnasHijas2*1 ) and $grupo*1 == ($grupoInicio+1)*1 and !$elemento['hijosCompletos'] ){
                                    $elemento['hijosCompletos'] = true;
                                    $grupo++;

                                }

                                if( !$sonTitulos and $elemento['tipo'] == "Subtitulo" and $elemento['columnas']*1 < $columnasFormulario*1 and $elemento['columnas']*1 == $columnasHijas2*1 and $grupo == $grupoInicio+1 and !$elemento['hijosCompletos']){
                                    $elemento['hijosCompletos'] = true;
                                    $grupo++;
                                }
                            }

                        }

                        if( $elementoHijo['tipo'] == "Titulo" or  $elementoHijo['tipo'] == "Subtitulo" ){

                            if($elementoHijo['tipo'] == "Titulo"){
                                $elemento['hijosCompletos'] = true;
                                $parar = true;
                            }

                            $columnasHijas += $elementoHijo['columnas'];
                            armarArbolConsecutivo( $grupos, $wcodForm, $grupo , $columnasFormulario, $elementoHijo );
                            if( $columnasHijas == $columnas )
                                $pararXtitulo = true;

                        }

                    }
                }
            }

        }else{
                  if(  !$elemento['tienePadre'] ){
                        $arregloTitulos[$wcodForm][$elementoPadre['consecutivo']]['hijos'][$consecutivo]['tipo'] = $elemento['tipo'];
                        $elemento['tienePadre']                                          = true;
                        $elemento['padre']                                               = $consecutivo;
                  }
        }
    }

    return;
}

//--> funcion de hce que muestra los datos del paciente
function mostrarEncabezadoPaciente( $whis, $wing, $wempresa ){

    global $conex;
    global $wmovhos;
    global $whce;
    global $wemp_pmla;
    global $wcliame;
    global $hoy;

    $datosPaciente = array();

    $query = " SELECT CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente ,Pacnac as fecha_nacimiento,Pacsex as genero,$whis as historia,$wing as ingreso,Pactid as tipo_documento,Pacced as documento"
            ."   FROM root_000036, root_000037 "
            . " WHERE orihis = '".$whis."'"
            . "   AND pacced = oriced "
            . "   AND pactid = oritid "
            . "   AND oriori = '".$wemp_pmla."' ";

    $query2 = "SELECT Ingnre,Ubisac,Ubihac,Cconom ,ING.Fecha_data fechaIngreso, UBI.ubifad, Empmai "
            . "  FROM ".$wmovhos."_000018 UBI, ".$wmovhos."_000011, ".$wmovhos."_000016 ING LEFT JOIN ".$wcliame."_000024 ON (empcod = Ingres)"
            . " WHERE Ubihis = '".$whis."'"
            . "   AND Ubiing = '".$wing."'"
            . "   AND ubihis = inghis "
            . "   AND ubiing = inging "
            . "   AND ccocod = ubisac ";

    $err  = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
    $num  = mysql_num_rows($err);
    $err2 = mysql_query($query2,$conex) or die(mysql_errno().":".mysql_error());
    $num2 = mysql_num_rows($err2);

    if ($num>0 && $num2>0){
        $row = mysql_fetch_assoc($err);
        $row2 = mysql_fetch_array($err2);

        $sexo="MASCULINO";
        if($row['genero'] == "F")
            $sexo="FEMENINO";

        $ann   =(integer)substr($row['fecha_nacimiento'],0,4)*360 +(integer)substr($row['fecha_nacimiento'],5,2)*30 + (integer)substr($row['fecha_nacimiento'],8,2);
        $aa    =(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
        $ann1  =($aa - $ann)/360;
        $meses =(($aa - $ann) % 360)/30;

        if ($ann1<1){
            $dias1=(($aa - $ann) % 360) % 30;
            $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";
        }else{
            $dias1=(($aa - $ann) % 360) % 30;
            $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
        }

        $wpac = $row['tipo_documento']." ".$row['documento']."<br>".$row['paciente'];

        $datosPaciente['nombre'] = $row['paciente'];

        $datosPaciente['email']  = $row2['Empmai'];

        $datosPaciente['historia'] = $whis;

        $datosPaciente['ingreso'] = $wing;

        $datosPaciente['Habitacion'] = $row2['Ubihac'];

        $datosPaciente['FechaIngreso'] = $row2['fechaIngreso'];
        if(!isset($wing))
            $wing=$row['ingreso'];
        if( $row2[5] == '0000-00-00' ) $row2[5] = $hoy;
            $color="#dddddd";

        $encabezadoPaciente = "<center><table border='1' width='712' class='tipoTABLE1'>";
        $encabezadoPaciente .= "<tr><td rowspan='3' align='center'><IMG SRC='/MATRIX/images/medical/root/".$wempresa.".jpg' id='logo'></td>";
        $encabezadoPaciente .= "<td id='tipoL01C' align='center' class='fila2'><b>Paciente</b></td><td colspan='5' id='tipoL04' align='center' class='fila1' align='center'><b>".$wpac."</b></td></tr>";
        $encabezadoPaciente .= "<tr><td id='tipoL01C' align='center' class='fila2'><b>Historia Clinica</b></td><td id='tipoL02C' align='center' class='fila1'><b>".$row['historia']."-".$wing."</b></td><td id='tipoL01' align='center' class='fila2'><b>Edad</b></td><td id='tipoL02C' align='center' class='fila1'><b>".$wedad."</b></td><td id='tipoL01C' align='center' class='fila2'><b>Sexo</b></td><td id='tipoL02C' align='center' class='fila1'><b>".$sexo."</b></td></tr>";
        $encabezadoPaciente .= "<tr><td id='tipoL01C' align='center' class='fila2'><b>Servicio</b></td><td id='tipoL02C' align='center' class='fila1'><b>".$row2[3]."</b></td><td id='tipoL01C' align='center' class='fila2'><b>Habitacion</b></td><td id='tipoL02C' align='center' class='fila1'><b>".$row2[2]."</b></td><td id='tipoL01C' align='center' class='fila2'><b>Entidad</b></td><td id='tipoL02C' align='center' class='fila1'><b>".$row2[0]."</b></td></tr>";
        $encabezadoPaciente .= "</table><br></center>";
    }

    $datosPaciente['encabezadoPaciente'] = $encabezadoPaciente;
    return( $datosPaciente );
}

// --> funcion que encuentra el nombre de una fecha ( lunes, martes, miercoles.... )
function nombreFecha( $fecha ){

    $fechats = strtotime($fecha); //a timestamp
    $nombre  = "";
    //el parametro w en la funcion date indica que queremos el dia de la semana
    //lo devuelve en numero 0 domingo, 1 lunes,....
    $numeroDia =  date('w', $fechats);
    switch( $numeroDia ){
        case 0:
            $nombre = "Domingo";
            break;
        case 1:
            $nombre = "Lunes";
            break;
        case 2:
            $nombre = "Martes";
            break;
        case 3:
            $nombre = "Miercoles";
            break;
        case 4:
            $nombre = "Jueves";
            break;
        case 5:
            $nombre = "Viernes";
            break;
        case 6:
            $nombre = "Sabado";
            break;
    }
    return( $nombre );
}

// --> esta función modifica la hora de corte del reporte para que coincida con los requermientos de los parámetros.
//     en caso de que se seleccione que el reporte muestre las últimas 24 horas, entonces la hora de corte se aumenta en uno para que
//     muestre desde la hora de corte de ayer, hasta la acutal.
function establecerHoraDeCorte( &$whoraDeCorte, &$horaActual, $wultimas24 = "", $fechaEspecifica, $wdiaCompleto = "" ){

    if($fechaEspecifica){
        if( $wdiaCompleto ==  "on" ){
            $horaActual = 23;
            $whoraDeCorte = 0;
            $wultimas24 = "si";
        }else{
            $horaCorteAux = $whoraDeCorte;
            $horaActual   = $horaCorteAux*1 - 1;
        }
    }else{
        if( $wultimas24 == "si"){

           $horaCorteAux = $horaActual;
            if( $horaCorteAux == 23 ){
                $whoraDeCorte = -1;
            }else{
                $whoraDeCorte = $horaCorteAux + 1;
            }
        }
    }
}

// --> esta funcion es recursiva y verifica si tiene algun dato dentro de los hijos de un nodo( titulo o subtitulo ) para así decidir si se va a imprimir en pantalla
//     dicho título.
function imprimirTitulo( $wcodForm, $keyCampo, $arregloSubtitulos ){

    global $titulosAimprimir;
    global $camposDiligenciados;

    $imprimir      = false;
    $arrayAuxiliar = $arregloSubtitulos[$wcodForm][$keyCampo];

    if(  !isset($arrayAuxiliar['hijos']) or count($arrayAuxiliar['hijos']) == 0  ){
        return( false );
    }

    $i = 0;

    foreach ( $arrayAuxiliar['hijos'] as $codigoHijo => $dato ) {

          if( $i < count( $arrayAuxiliar['hijos'] ) ){

                if( $arrayAuxiliar['hijos'][$codigoHijo]['tipo'] != "dato" ){
                    $imprimir = imprimirTitulo( $wcodForm, $codigoHijo, $arregloSubtitulos );

                    if( $imprimir ){
                        $titulosAimprimir[$wcodForm][$codigoHijo] = true;
                        $titulosAimprimir[$wcodForm][$keyCampo]   = true;
                        return( $imprimir );
                    }
                }else{

                    if(in_array( $codigoHijo, $camposDiligenciados[$wcodForm] ) ){
                        $imprimir = true;
                        return( $imprimir );
                    }
                }
            }else{
                return($imprimir);
            }
            $i++;
    }

    return( $imprimir );
}

function  moverEnCeldas( &$filaMemo, $valor, $keyCelda ){
    $cantidad = 1;
    $keySiguiente = $keyCelda + 1;
    while( isset( $filaMemo[$keySiguiente] ) and $filaMemo[$keySiguiente]['valor'] == $valor ){
    //while( isset( $filaMemo[$keySiguiente] ) and $filaMemo[$keySiguiente]['valor'] == $valor or $filaMemo[$keySiguiente]['valor'] == "&nbsp;" ){
        $filaMemo[$keySiguiente]['impreso'] = true;
        $cantidad ++;
        $keySiguiente ++;
    }
    return( $cantidad );
}
/** FIN DE FUNCIONES **/

/** INICIO LLAMADOS AJAX **/
if( $peticionAjax == "generarInforme" ){

    //variables en la petición ajax
    $wemp_pmla            = $_REQUEST['wemp_pmla'];
    $wempresa             = $_REQUEST['wempresa'];
    $whce                 = $_REQUEST['whce'];
    $wmovhos              = $_REQUEST['wmovhos'];
    $wcliame              = $_REQUEST['wcliame'];
    $wcodFormularios      = $_REQUEST['wcodFormularios'];
    $whis                 = $_REQUEST['whis'];
    $wing                 = $_REQUEST['wing'];
    $wfechahoy            = $_REQUEST['wfechahoy'];
    $wultimas24           = $_REQUEST['wultimas24'];
    $horaDeCorte          = $_REQUEST['whoraDeCorte']; //el reporte va de 8 am a 8 am, en caso de que se este generando en una hora menor se deben incluir los datos del dia anterior.
    $wdiaCompleto         = $_REQUEST['wdiaCompleto'];
    $fechaEspecifica      = false;
    $datosFormularios     = arregloReportes(); //primero construye un arreglo con los nombres de los reportes
    $wcodFormularios      = explode( ",",$wcodFormularios ); // se modifica la variable con un arreglo compuesto por los codigos de los formularios a consultar
    $horaActual           = date("H");
    $arregloTitulos       = array();
    $titulosAimprimir     = array();
    $camposDiligenciados  = array();
    $consecutivosMemo     = array(); // compuesto básicamente por datos que viajan entre la grabación de los formularios( fechas )
    $datosMemo            = array(); //datos tipo memo que están diligenciados, se arma al recorrer el result set de la consulta principal

    //variables internas de la petición
    if( (!isset($wfechahoy) or trim( $wfechahoy ) == "") ){//si entro directo por la historia, la primera vez
        $hoy = date('Y-m-d');
        $sol = ( strtotime( $hoy ) - 3600);
        $ayer  = date('Y-m-d', $sol);
    }else{//si entro por seleccion de fecha
        if( strtotime( $wfechahoy ) < strtotime(date('Y-m-d')) ){
            if( $wdiaCompleto != "on"){
                $hoy             = ( strtotime( $wfechahoy ) ) + (3600*24); // SELECCIONA DESDE ESE DIA HASTA EL SIGUIENTE
                $hoy             = date('Y-m-d', $hoy );
                $fechaEspecifica = true;
            }else{
                $hoy = $wfechahoy;
                $fechaEspecifica = true;
            }
        }else{
            $hoy = $wfechahoy;
            if( $wdiaCompleto == "on" ){
                $fechaEspecifica = true;
            }
        }
        $sol = ( strtotime( $hoy ) - 3600);
        $ayer= date('Y-m-d', $sol);
    }

    $encabezadoPaciente   = mostrarEncabezadoPaciente( $whis, $wing, $wempresa );
    $nombreHoy            = nombreFecha( $hoy );
    $nombreAyer           = nombreFecha( $ayer );
    establecerHoraDeCorte( $horaDeCorte, $horaActual, $wultimas24, $fechaEspecifica, $wdiaCompleto );

    echo "<br>".$encabezadoPaciente['encabezadoPaciente']."<br>";
    //--> Se construye todo el informe para cada uno de los formularios
    foreach ($wcodFormularios as $m => $value){

        $wcodForm                       = $wcodFormularios[$m];
        $columnasFormulario             = $datosFormularios[$wcodForm]['columnas'];
        $paraFecMonitor                 = array();
        $paraThdMonitor                 = array();
        $arreglosContenedores           = construirArreglosContenedores( $wcodForm ); //este es un arreglo con la siguiente indexación ['horas'] = arreglo de horas y ['datos'] = arreglo con datos del formulario
        $arregloAuxiliar                = array();
        $resultados                     = array();
        $camposDiligenciados[$wcodForm] = array();
        $horasMonitoreo                 = array();
        $horasMonitoreoHd               = array();
        $fechaMonitoreo                 = array();
        $fechaMonitoreoHd               = array();
        $fechasPendientesDeImprimir     = array();
        $queryAux                       = "";
        $queryAux2                      = "";

        /** se construye un string de la forma: ('10', '21','13') para consultar solo los campos de interes en el reporte**/
        foreach ( $arreglosContenedores[$wcodForm]['campos'] as $keyCampo => $nombre ){
            array_push( $arregloAuxiliar, "'".$keyCampo."'" );
        }
        $camposBuscados = implode( ",", $arregloAuxiliar );

        //en caso de que la hora del momento de consulta sea inferior a la hora de corte, se deben incluir los datos del dia anterior,
        //desde la hora de corte
        if( $horaActual*1 < $horaDeCorte*1 ){
            $queryAux = "  UNION ALL
                          SELECT a.Fecha_data, a.Hora_data, Movusu, Movcon, Movtip, Movdat
                            FROM {$whce}_{$wcodForm} a
                           INNER JOIN
                                 {$whce}_000002 ON ( Detpro='{$wcodForm}' AND Detcon = Movcon )
                           WHERE Movhis = '{$whis}'
                             AND Moving = '{$wing}'
                             AND Movcon IN ({$camposBuscados})
                             AND Dettip NOT IN ('Titulo', 'Subtitulo', 'Texto')
                             AND a.Fecha_data = '{$ayer}'";
                             //AND a.Hora_data  > '{$horaDeCorte}:00:00'";
        }else{
            $query2     = "";
            //$query2   = " AND a.Hora_data >= '{$horaDeCorte}:00:00'";
        }

        $manana = strtotime( $hoy ) + (3600*24);
        $manana = date('Y-m-d', $manana );

        $query = "SELECT a.Fecha_data, a.Hora_data, Movusu, Movcon, Movtip, Movdat
                    FROM {$whce}_{$wcodForm} a
                   INNER JOIN
                         {$whce}_000002 ON ( Detpro='{$wcodForm}' AND Detcon = Movcon )
                   WHERE Movhis = '{$whis}'
                     AND Moving = '{$wing}'
                     AND Movcon IN ({$camposBuscados})
                     AND Dettip NOT IN ('Titulo', 'Subtitulo', 'Texto')
                     AND a.Fecha_data between '".$hoy."' and '".$manana."'
                     $query2";

        $query .= $queryAux;
        $query .= " ORDER  BY Fecha_data, Hora_data, Movusu, Movcon asc ";
        $rs   = mysql_query( $query, $conex ) or die( mysql_error()." <br> ".$query );
        $num  = mysql_num_rows( $rs );

        /** Acá vamos a construir un arreglo con los datos **/

        $arrayAuxiliar            = array(); //-> guardará los datos de un formularios( hasta que termine de recorrer los resultados o disminuya el consecutivo )
        $horaAnt                  = ""; //-> variable de control para verificar el cambio de consecutivo;
        $horaMonitoreo            = ""; //-> hora real del monitoreo registrado.
        $campoFechaEncontrado     = false;
        $fechasAuxiliares         = array();

        //-> el siguiente segmento de código almacena los datos con la misma hora de guardado( mismo formulario ) en un arreglo auxiliar,  adicionalmente, cuando encuentra el campo
        //   correspondiente a la hora del monitoreo, guarda este en una variable para posteriormente organizar los resultados asignandoles la hora de monitoreo correspondiente a los datos,
        //  esto debido a que en ocasiones no corresponden la hora de grabación y la hora del monitoreo grabado.
        while( $row = mysql_fetch_array( $rs ) ){


            //--> aca verifico si se debe tener en cuenta la hora y la fecha data.
            //    por ejemplo el formulario 335 debe reportar datos aunque no tengan el campo de la hora diligenciado.


            if( trim($row["Movcon"]) == trim($paraHorMonitor[$wcodForm]) && ( trim($row["Movcon"] ) != "" ) ){

                $row['Movdat'] = explode("-", $row['Movdat']);
                $row['Movdat'] = $row['Movdat'][0];
                ( strlen( trim($row['Movdat']) ) == 1 ) ? $datoCampo = "0".trim($row['Movdat']) : $datoCampo = trim($row['Movdat']);
                $horasMonitoreo[$row['Fecha_data']][$row['Hora_data']]= trim($datoCampo);

            }else{

                //--> 2016-07-13 --> si no es igual al consecutivo que indica la hora a tomar en cuenta por parámetro
                if( $paraThdMonitor[$wcodForm] == "on" ){
                    if( !isset($horasMonitoreo[$row['Fecha_data']][$row['Hora_data']]) or ($horasMonitoreo[$row['Fecha_data']][$row['Hora_data']] == "") ){
                        $horaAux = explode(":", $row['Hora_data'] );
                        $horaAux = $horaAux[0];
                        ( strlen( trim($horaAux) ) == 1 ) ? $datoCampo = "0".trim($horaAux) : $datoCampo = trim($horaAux);
                        $horasMonitoreo[$row['Fecha_data']][$row['Hora_data']]= trim($datoCampo);
                    }
                }

                if( !isset( $fechaMonitoreo[$row['Fecha_data']][$row['Hora_data']] ) and $fechasAuxiliares[$row['Fecha_data']][$row['Hora_data']] != true ){
                    $fechaMonitoreo[$row['Fecha_data']][$row['Hora_data']] = $row['Fecha_data'];
                }

                if( $row["Movcon"] == $paraFecMonitor[$wcodForm] ){
                    $fechaMonitoreo[$row['Fecha_data']][$row['Hora_data']]= $row['Movdat'];
                    $fechasAuxiliares[$row['Fecha_data']][$row['Hora_data']] = true;
                }else{
                    //-->2016-07-13 para que tenga en cuenta formularios que no tienen campo para la fecha, definido.
                    if( $paraThdMonitor[$wcodForm] == "on" ){
                        if( !isset( $fechaMonitoreo[$row['Fecha_data']][$row['Hora_data']] ) or ( $fechaMonitoreo[$row['Fecha_data']][$row['Hora_data']] == "" ) ){
                            $fechaMonitoreo[$row['Fecha_data']][$row['Hora_data']]= $row['Fecha_data'];
                            $fechasAuxiliares[$row['Fecha_data']][$row['Hora_data']] = true;
                        }
                    }
                    if( trim( $row['Movdat'] ) != "" ){
                        $arrayAuxiliar[$row['Fecha_data']][$row['Hora_data']][$row['Movcon']] = $row['Movdat'];
                        array_push( $camposDiligenciados[$wcodForm], $row['Movcon'] );//registro que este consecutivo fue diligenciado( para el control de titulos );
                    }
                }
            }
        }

        foreach ( $arrayAuxiliar as $keyFecha => $horas ) {
            foreach ($horas as $keyHora => $conceptos ) {
                foreach ($conceptos as $keyCampo => $dato) {
                    $resultados[$keyCampo][trim($fechaMonitoreo[$keyFecha][$keyHora])][$horasMonitoreo[$keyFecha][$keyHora]] = $dato;

                    if( in_array( $keyCampo, $consecutivosMemo[$wcodForm] ) ){

                        $colspan                                                                = $datosMemo[$wcodForm][$keyCampo][$dato]['colspan'];
                        ( $colspan*1 > 0 ) ? $datosMemo[$wcodForm][$keyCampo][$dato]['colspan'] = $colspan + 1 : $datosMemo[$wcodForm][$keyCampo][$dato]['colspan'] = 1;
                        $datosMemo[$wcodForm][$keyCampo][$dato]['impresa']                      = false;
                        $datosMemo[$wcodForm][$keyCampo][$dato]['fechasPendientesDeImprimir']   = 0;
                    }
                }
            }
        }

        // si no se encontraron datos, se notifica.
        $num = 1;
        if( $num <= 0 ){

            echo "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sin_datos'>
                  No se encontraron datos correspondientes a la consulta realizada para ".( $datosFormularios[$wcodForm]['nombre'] ).".
                </div>";
            return false;
        }

        /** EMPIEZA LA CONSTRUCCIÓN DEL HTML, CON LOS DATOS RESULTANTES **/
        $fila = 0;
        $tabla  = "<input type='hidden' id='wfechaIng'  value='".$encabezadoPaciente['fechaIngreso']."'>";
        $tabla .= "<input type='hidden' id='wfechahoyReal' value='".date('Y-m-d')."'>";
        $tabla .= "<div style='width:100%;' id='div_contenedor_tabla' class='contenedorSabana' formulario='{$wcodForm}' align='center' >";
        $tabla .= "<div class='desplegables' style='width:100%;'>";
        $tabla .= "<h3><b>* ".( $datosFormularios[$wcodForm]['nombre'] )." *</b></h3>";
        $tabla .= "<div>";
        $tabla .= "<div class='caja_flotante_query' formulario='{$wcodForm}' width='100%'></div>";
        $tabla .= "<table id='tabla_contenedora_{$wcodForm}' formulario='{$wcodForm}' width='100%'>";

        //--> ENCABEZADO DATOS DEL PACIENTE
        ( $wdiaCompleto == "on" ) ? $checked = "checked" : $checked = "";
        $tabla .= "<tr class='caja_flotante_query_ori1' formulario='{$wcodForm}'><td colspan='40' align='left'><div class='div_nombreFormulario' formulario='{$wcodForm}' align='center' style='display:none;'>".( $datosFormularios[$wcodForm]['nombre'] )."</div></td></tr>";
        $tabla .= "<tr class='caja_flotante_query_ori2'><td colspan='40' align='left'>";
            $tabla .= "<table width='100%' class='tipoTABLE1' border='1'>";
            $tabla .= "<tr formulario='{$wcodForm}'>";
            $tabla .= "<td align='left' width='8%px' class='botona'> DATOS </br> PACIENTE: </td>";
            $tabla .= "<td align='left' width='30%' nowrap class='fila2'> ".$encabezadoPaciente['nombre']." </td>";
            $tabla .= "<td align='left' width='8%' class='botona'> HISTORIA:</td><td class='fila2' width='8%'> ".$whis." - ".$wing." </td>";
            $tabla .= "<td align='left' width='15%'class='botona'> HABITACI&Oacute;N:</td><td class='fila2' width='5%'>".$encabezadoPaciente['Habitacion']."</td>";
            $tabla .= "<td align='left' width='12%'class='botona'> Ver dia Completo: <input type='checkbox' id='chk_diacompleto' {$checked} onclick='diaCompleto( this )'></td>";
            ( (!isset($wfechahoy) or trim($wfechahoy) == "") or $wfechahoy == date( 'Y-m-d' ) ) ? $dia = $hoy : $dia = $ayer;
            ( $wdiaCompleto == "on" ) ? $dia = $wfechahoy : $dia = $dia;
            $tabla .= "<td align='right'width='14%'class='botona'> FECHA: <input type='text' class='inputFecha' name='wfechahoy' onchange='cambiarFecha(this)'  size='11' value='{$dia}'>";
            $tabla .= "</td>";
            $tabla .= "<td class='fila2' style='cursor:pointer;' title='ClicK para generar' onclick='generarInforme();'><img width= heigth='10' src='../../images/medical/hce/lupa.PNG'></td>";
            $tabla .= "</tr>";
            $tabla .= "</table>";
        $tabla .= "</td></tr>";

        //--> ENCABEZADO DE FECHAS
        $tabla .= "<tr class='caja_flotante_query_ori3' formulario='{$wcodForm}'>";
        $tabla .= "<td align='center' class='encabezadotabla' colspan='2'> FECHA: </td>";
        $i = 0;
            foreach ($arreglosContenedores[$wcodForm]['horas'] as $keyFecha => $horas ){
                $i++;
                ( $keyFecha == $hoy ) ? $nombreDia = $nombreHoy : $nombreDia = $nombreAyer;
                $tabla .= "<td align='center' class='encabezadotabla' colspan='".count($arreglosContenedores[$wcodForm]['horas'][$keyFecha])."'>{$keyFecha} ({$nombreDia})</td>";
            }
        $tabla .= "</tr>";

        //--> ENCABEZADO CON LAS HORAS.
        $tabla .= "<tr class='caja_flotante_query_ori4' formulario='{$wcodForm}'>";
        $tabla .= "<td align='center' class='encabezadotabla' colspan='2'> CAMPO\HORAS: </td>";
                    $z= 0;
                    foreach ($arreglosContenedores[$wcodForm]['horas'] as $keyFecha => $horas ){
                        foreach ($arreglosContenedores[$wcodForm]['horas'][$keyFecha] as $keyHora => $dato) {
                            $z++;
                            $tabla .= "<td align='center' class='encabezadotabla td_horas' seleccionado='no' numeroTd='{$z}' formulario='{$wcodForm}' hora='{$keyHora}' encabezado='si'>{$dato}</td>";
                        }
                    }
        $tabla .= "</tr>";

        //--> ENCABEZADO DONDE SE GRAFÍCA
        $tabla .= "<tr class='caja_flotante_query_ori5 fila2' formulario='{$wcodForm}'>";
        //$tabla .= "<td align='center' class='encabezadotabla' colspan='1'><img height='19' src='../../images/medical/root/chart.png' tooltip='si' style='background-color: #FFFFFF;cursor:pointer;' titlesegmento='&lt;span style=&quot;color: #000000;font-size: 10pt;&quot;&gt;Graficar&lt;/span&gt;' alt='' onclick='GraficarComparativamente( \"{$wcodForm}\" );'> </td><td align='center'><input type='checkbox' name='chk_todos' formulario='{$wcodForm}' onclick='ModificarTodos(this);'></td>";
        $tabla .= "<td align='center' class='encabezadotabla' colspan='2'><img height='19' src='../../images/medical/root/chart.png' tooltip='si' style='background-color: #FFFFFF;cursor:pointer;' titlesegmento='&lt;span style=&quot;color: #000000;font-size: 10pt;&quot;&gt;Graficar&lt;/span&gt;' alt='' onclick='GraficarComparativamente( \"{$wcodForm}\" );'> </td>";
                    $z = 0; //variable para enumerar los td y facilitar el seleccionado de horas a graficar
                    $tooltipTootal =  'titlesegmento="<span style=\'color: #000000;font-size: 10pt; background-color: #FFFFFF; width: 100%; height: 100%;\'>Click para iniciar y finalizar<br> rango de horas </span>" alt=""';
                    foreach ($arreglosContenedores[$wcodForm]['horas'] as $keyFecha => $horas ){
                        foreach ($arreglosContenedores[$wcodForm]['horas'][$keyFecha] as $keyHora => $dato) {
                            $z++;
                            $tabla .= "<td align='center' alt='click' style='cursor:pointer;' class='filtroRangoHoras' tooltip='si' {$tooltipTootal} seleccionado='no' numeroTd='{$z}' formulario='{$wcodForm}' onmouseover='seleccionarParaGraficar( this );' onclick='modificarRangoHoras(this);'>&nbsp</td>";
                        }
                    }
        $tabla .= "<input type='hidden' tipo='controlRangoHoras' formulario='{$wcodForm}' clicks='0' tdInicial='' tdFinal=''>";
        $tabla .= "</tr>";

        $totalFilas   = 0;

        foreach( $arreglosContenedores[$wcodForm]['campos'] as $keyCampo=>$nombre ){


            $descripcionTitulo = ( $arreglosContenedores[$wcodForm]['descripcion'][trim($keyCampo)] );
            if( $arreglosContenedores[$wcodForm]['tipos'][$keyCampo] == "Titulo" or $arreglosContenedores[$wcodForm]['tipos'][$keyCampo] == "Subtitulo" ){

                $colspanTitulo = 3 + $z;
                $tipo = $arreglosContenedores[$wcodForm]['tipos'][$keyCampo];
                ( $tipo == "Titulo" ) ? $wclass = " class='tituloFormulario' tipo='titulo' " : $wclass = "class='subTituloFormulario' tipo='titulo'";

                if( !$titulosAimprimir[$wcodForm][$keyCampo] ){

                    $titulosAimprimir[$wcodForm][$keyCampo] = imprimirTitulo( $wcodForm, $keyCampo, $arregloTitulos );
                }

                if( $titulosAimprimir[$wcodForm][$keyCampo] ){// si hay que imprimir el titulo

                    //-->este Arreglo Guarda las columas que se imprimiran si es un campo tipo memo
                    $tabla .= "<tr $wclass><td colspan='$colspanTitulo' align='center'> $descripcionTitulo </td></tr>";
                    foreach ($arregloTitulos[$wcodForm][$keyCampo]['hijos'] as $keyCampoHijo => $dato) {
                        unset( $filaMemo );
                        $filaMemo     = array();
                        $auxiliarMemo = array();
                        if( $dato['tipo'] != "Titulo" and $dato['tipo'] != "Subtitulo" ){

                            if( count( $resultados[$keyCampoHijo] ) > 0){
                                $descripcionCampo = ( $arreglosContenedores[$wcodForm]['descripcion'][trim($keyCampoHijo)] );
                                $nombreCampo      = ( $arreglosContenedores[$wcodForm]['nombres'][trim($keyCampoHijo)] );
                                ( in_array( $keyCampoHijo, $consecutivosMemo[$wcodForm] ) ) ? $tipo2 = "Memo" : $tipo2 = "noMemo";
                                $tabla .= "<tr tipo='{$dato['tipo']}' tipo2='{$tipo2}' >";
                                $totalFilas++;
                                $i = 0;
                                //** td con icono para graficar**//
                                if( $arreglosContenedores[$wcodForm]['tipos'][$keyCampoHijo] == "Numero" or $arreglosContenedores[$wcodForm]['tipos'][$keyCampoHijo] == "Formula"){
                                    $checkbox = "<input type='checkbox' tipo='campo' campo='{$keyCampoHijo}' nombreCampo='".( $nombreCampo )."' formulario='{$wcodForm}' onclick='seleccionarCampo(this)'>";
                                }else{

                                    $checkbox  = "&nbsp;";
                                }
                                //hasta acá
                                $numero_td = 0;
                                (is_int( $totalFilas/2 )) ? $wclass = "fila1" : $wclass = "fila2";
                                $tooltipTootal =  "titlesegmento='<span style=\"color: #000000;font-size: 10pt; background-color: #FFFFFF; width: 100%; height: 100%;\">".$descripcionCampo."</span>' alt=''";
                                $tabla .= "<td align='left' class='{$wclass}' {$tooltipTootal} width='70px' tooltip='si' style='cursor:pointer;'>".( $nombreCampo )." </td><td align='left' class='{$wclass}' width='20px'>{$checkbox}</td>";
                                foreach( $arreglosContenedores[$wcodForm]['horas'] as $keyFecha => $datosFecha ) {
                                    foreach( $arreglosContenedores[$wcodForm]['horas'][$keyFecha] as $keyHora => $datos ) {
                                        $numero_td++;

                                        //SI ES UN TIPO BOOLEANO QUE MUESTRE EL CHULO EN CASO DE QUE ESTÉ CHECKEADO
                                        if( $arreglosContenedores[$wcodForm]['tipos'][$keyCampoHijo] == "Booleano" and $resultados[$keyCampoHijo][$keyFecha][$keyHora] == "CHECKED")
                                                $resultados[$keyCampoHijo][$keyFecha][$keyHora] = "<img src='/matrix/images/medical/movhos/checkmrk.ico'>";

                                        //SI ES UN TIPO SELECCION QUE MUESTRE SOLO EL VALOR, SIN EL CÓDIGO ASOCIADO.
                                        if( $arreglosContenedores[$wcodForm]['tipos'][$keyCampoHijo] == "Seleccion"){
                                            $aux = $resultados[$keyCampoHijo][$keyFecha][$keyHora];
                                            $aux2 = $aux;
                                            $aux = explode( "-", $aux );
                                            $aux = substr( $aux2, strpos($aux2, "-") + 1 );
                                            $resultados[$keyCampoHijo][$keyFecha][$keyHora] = $aux;
                                        }

                                        if( !isset( $resultados[$keyCampoHijo][$keyFecha][$keyHora]) ){
                                            $resultados[$keyCampoHijo][$keyFecha][$keyHora] = "&nbsp;";
                                        }

                                        $txtImprimir = $resultados[$keyCampoHijo][$keyFecha][$keyHora];

                                        if( in_array( $keyCampoHijo, $consecutivosMemo[$wcodForm] ) ){
                                            $datoNormal = false;
                                        }else{
                                            $datoNormal = true;
                                        }

                                        if( $datoNormal ){
                                            if( $arreglosContenedores[$wcodForm]['tipos'][$keyCampoHijo] == "Formula"){
                                                //$txtImprimir = number_format( $txtImprimir, 0, '.', '.' );
                                                $auxiliarN   = explode('.', $txtImprimir );
                                                $txtImprimir = $auxiliarN[0];
                                            }
                                            $tabla      .= "<td align='center' numerotd='{$numero_td}' class='{$wclass}' colspan='colspan' width='60px' formulario='{$wcodForm}' campo='{$keyCampoHijo}' hora='{$keyHora}' encabezado='no'>".$txtImprimir."</td>";
                                        }else{

                                            unset( $nuevoArregloAuxiliar );
                                            $nuevoArregloAuxiliar['valor']   = $txtImprimir;
                                            $nuevoArregloAuxiliar['impreso'] = false;
                                            $nuevoArregloAuxiliar['texto']   = "<td align='center' *colspan* numerotd='{$numero_td}' class='{$wclass}' colspan='colspan' width='60px' formulario='{$wcodForm}' campo='{$keyCampoHijo}' hora='{$keyHora}' encabezado='no'>".$txtImprimir."</td>";
                                            array_push( $filaMemo, $nuevoArregloAuxiliar );
                                        }
                                        $i++;
                                    }
                                }
                                if( count( $filaMemo ) > 0 ){
                                    $celda   = 0;
                                    $colspan = 1;
                                    for( $keyCelda = 0; $keyCelda < count($filaMemo); $keyCelda++ ){
                                        $valor = $filaMemo[$keyCelda];
                                        if( $arreglosContenedores[$wcodForm]['tipos'][$keyCampoHijo] == "Numero" or $arreglosContenedores[$wcodForm]['tipos'][$keyCampoHijo] == "Formula"){
                                            $checkbox = "<input type='checkbox' tipo='campo' campo='{$keyCampoHijo}' nombreCampo='".( $nombreCampo )."' formulario='{$wcodForm}' onclick='seleccionarCampo(this)'>";
                                        }
                                        if( $valor['valor'] != "&nbsp;" and !$valor['impreso']){
                                            $colspanNuevo = moverEnCeldas( $filaMemo, $valor['valor'], $keyCelda );
                                            $keyCelda     =  $keyCelda + $colspanNuevo - 1;
                                            $tabla        .= str_replace("*colspan*", "colspan='{$colspanNuevo}'", $valor['texto'] );
                                       }else{
                                            $tabla .= $valor['texto'];
                                       }
                                    }
                                }
                                $tabla .= "</tr>";
                            }

                        }
                    }
                }
            }
        }
        $tabla .= "</table> ";
        $tabla .= "</div></div>";
        $tabla .= "</div>";
        echo $tabla."<br>";
    }//fin del for para el arreglo de formularios consultados
    //echo "<center><input type='button' id='btn_retornar1' onclick='retornar()' value='Retornar' /></center><br>";
    return;
}
/** FINAL LLAMADOS AJAX**/

?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title> REPORTE SABANA </title>
    <style type="text/css">

        .subTituloFormulario {
            background: none repeat scroll 0 0 #CCCCCC;
            color: #000066;
            font-family: Arial;
            font-size: 10pt;
            font-weight: bold;
            height: 1em;
            text-align: left;
            width: 90em;
        }

        .tituloFormulario {
            background: none repeat scroll 0 0 #999999;
            color: #000066;
            cursor: pointer;
            font-family: Arial;
            font-size: 12pt;
            font-weight: bold;
            height: 1em;
            text-align: left;
        }

        .botona{
            font-size:13px;
            font-family:Verdana,Helvetica;
            font-weight:bold;
            color:white;
            background:#638cb5;
            border:0px;
            height:30px;
            margin-left: 1%;
            cursor: pointer;
         }

        .tipoTABLE1 {
            border-collapse: collapse;
            border-style: solid;
            font-family: Arial;
        }

        .caja_flotante_query{
            position: absolute;
            top:0;
        }

        #tooltip{
            color: #2A5DB0;
            font-family: Arial,Helvetica,sans-serif;
            position:absolute;
            z-index:3000;
            border:1px solid #2A5DB0;
            background-color:#FFFFFF;
            padding:2px;
            opacity:1;
        }

        #tooltip h7, #tooltip div{
            margin:0;
            width:auto
        }

        .borderDiv2 {
            border: 2px solid #2A5DB0;
            padding: 15px;
        }

        .modal{
            display:none;
            cursor:default;
            background:none;
            repeat scroll 0 0;
            position:relative;
            width:98%;
            height:98%;
            overflow:auto;
        }
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
            width: 100px; /*must have*/
            height: 100px; /*must have*/
        }

        .horaSeleccionada{
            background-color: #20F031;
        }

        .div_nombreFormulario{
            background: url("../../../include/root/jqueryui_1_9_2/cupertino/images/ui-bg_glass_50_3baae3_1x400.png") repeat-x scroll 50% 50% #3BAAE3;
            border: 1px solid #2694E8;
            color: #FFFFFF;
            font-weight: bold;
            border-top-right-radius: 6px;
            border-top-left-radius: 6px;
        }
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
    <script src="../../../include/root/LeerTablaAmericas.js"  type="text/javascript"></script>
    <script src="../../../include/root/amcharts/amcharts.js"  type="text/javascript"></script>
    <script type="text/javascript">//codigo javascript propio
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

        function crearEncabezadoFlotante(){
            $(window).scroll(function() {
                var formularios = $("#wcodFormularios").val();
                formularios =  formularios.split(",");
                for( var i=0; i<formularios.length; i++){

                    codigoFormulario = formularios[i];
                    $(".caja_flotante_query[formulario='"+codigoFormulario+"']").html("");
                    $(".caja_flotante_query[formulario='"+codigoFormulario+"']").hide();
                    var posicion_query = $(".caja_flotante_query[formulario='"+codigoFormulario+"']").offset();
                    var posicion_query_ori1 = $(".caja_flotante_query_ori1[formulario='"+codigoFormulario+"']").offset();
                    var tamTabla = $("#tabla_contenedora_"+codigoFormulario).width();

                    if( posicion_query != undefined ){

                        if ( $(window).scrollTop() >= posicion_query_ori1.top && $(window).scrollTop() < (posicion_query_ori1.top + $("#tabla_contenedora_"+codigoFormulario).height() ) ) {
                            $(".div_nombreFormulario[formulario='"+codigoFormulario+"']").show();
                            var tabla = $("#tabla_contenedora_"+codigoFormulario).clone("true");
                            $(tabla).attr( "id", "tb1" );
                            var j = 1;
                            $(tabla).find("tr").each(function(){
                                if( j < 6){
                                    $(this).find("td").attr( "encabezado", "no" );
                                }
                                if( j > 6 ){
                                    $(this).remove();
                                }
                                j++;
                            });
                            $(tabla).find(".caja_flotante_query_ori4").attr("clone","si");

                            $(".caja_flotante_query[formulario='"+codigoFormulario+"']").width( tamTabla+"px" );
                            $(".caja_flotante_query[formulario='"+codigoFormulario+"']").addClass( 'fila2' );
                            $(".caja_flotante_query[formulario='"+codigoFormulario+"']").append( tabla );

                            filatam = 2;
                            var m = 0;
                            $(".caja_flotante_query_ori4[formulario='"+codigoFormulario+"'][clone!='si']").find("td").each(function () {
                                m++;
                                var tamMayor  = $( this ).width();
                                var numero_td = $( this ).attr("numeroTd");
                                $(".caja_flotante_query_ori4[formulario='"+codigoFormulario+"'][clone='si']").find("td:nth-child("+m+")").css("width", tamMayor );
                            });

                            if( $(window).scrollTop() < (posicion_query_ori1.top + $("#tabla_contenedora_"+codigoFormulario).height() ) - $(".caja_flotante_query[formulario='"+codigoFormulario+"']").height() ){
                                $('#tb1').find("td[tooltip=si]").each(function(){
                                    $(this).attr('title', $(this).attr('titlesegmento'));
                                    $(this).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
                                });
                                $(".caja_flotante_query[formulario='"+codigoFormulario+"']").show();
                                $(".caja_flotante_query[formulario='"+codigoFormulario+"']").css('marginTop', $(window).scrollTop() );
                                $(".caja_flotante_query[formulario='"+codigoFormulario+"']").css('marginLeft',  posicion_query_ori1.left - 45 );
                            }

                        } else {

                            $(".div_nombreFormulario[formulario='"+codigoFormulario+"']").hide();
                            $(".caja_flotante_query[formulario='"+codigoFormulario+"']").hide();
                            $(".caja_flotante_query[formulario='"+codigoFormulario+"']").html("");
                        }

                    }

                }
            });
        }

        function generarInforme(){

            var wemp_pmla       =$("#wemp_pmla").val() ;
            var wempresa        =$("#wempresa").val() ;
            var whce            =$("#whce").val() ;
            var wmovhos         =$("#wmovhos").val() ;
            var wcliame         =$("#wcliame").val() ;
            var wcodFormularios =$("#wcodFormularios").val() ;
            var whis            =$("#whis").val() ;
            var wing            =$("#wing").val() ;
            var wfechahoy       =$("input[name='wfechahoy']").val() ;
            var whoraDeCorte    =$("#whoraDeCorte").val() ;
            var wultimas24      =$("#wultimas24").val();
            var wdiaCompleto    =$("#wdiaCompleto").val();

            //validaciones previas al llamado
            if( $.trim(whis) == "" || $.trim(wing) == "" ){
                alerta( "El reporte no puede generarse, los datos están incompletos" );
            }
            $("#div_formularioppal").hide();
            $("#div_respuesta").hide();
            $("#div_respuesta").html("");
            $("#msjEspere").show();
            $.ajax({

                   url: "rep_sabana.php",
                  type: "POST",
                  data: {
                          peticionAjax: "generarInforme",
                                  whce: whce,
                               wmovhos: wmovhos,
                               wcliame: wcliame,
                             wemp_pmla: wemp_pmla,
                              wempresa: wempresa,
                       wcodFormularios: wcodFormularios,
                                  whis: whis,
                                  wing: wing,
                             wfechahoy: wfechahoy,
                          entroDirecto: $("#entroDirecto").val(),
                          whoraDeCorte: whoraDeCorte,
                            wultimas24: wultimas24,
                          wdiaCompleto: wdiaCompleto
                        },
                  success: function(data)
                  {
                    $("#msjEspere").hide();
                    $("#div_respuesta").html( data );
                    $("#div_respuesta").show();
                    $("#entroDirecto").val("off");

                    $( ".inputFecha" ).datepicker( "destroy" );
                    $( ".inputFecha" ).removeClass("hasDatepicker").removeAttr('id');
                    $(".inputFecha").datepicker({
                         showOn: "button",
                         buttonImage: "../../images/medical/root/calendar.gif",
                         buttonImageOnly: true,
                         changeYear:true,
                         reverseYearRange: true,
                         changeMonth: true,
                         minDate: $("#wfechaIng").val(),
                         maxDate: $("#wfechahoyReal").val()
                    });

                    $(".contenedorSabana").each(function(){
                        var contenedor =  $(this);
                        contenedor.find("table:nth-child(1)").each(function(){
                            var tam = $(this).width();
                            contenedor.css("width", tam+"px");
                        });
                    });

                    $( ".desplegables" ).accordion({
                            collapsible: true,
                            active:0,
                            heightStyle: "content",
                            icons: null
                    });

                    crearEncabezadoFlotante();

                    $('td[tooltip=si]').each(function(){
                        $(this).attr('title', $(this).attr('titlesegmento'));
                        $(this).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
                    });
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
            $(".caja_flotante_query").html( "" );
            $(".caja_flotante_query").hide();
            $("#div_respuesta").html( "" );
            $("#div_respuesta").hide();
            $("#div_formularioppal").show();
        }

        function seleccionarCampo( obj ){
            var formulario = $( obj ).attr("formulario");
            if( $( obj ).is(":checked") ){
                $("input[type='checkbox'][tipo='campo'][formulario!='"+formulario+"']:checked").attr("checked", false);
            }
            $(".caja_flotante_query[formulario='"+formulario+"']").show();
        }

        function GraficarComparativamente( codForm ){

            var campos = $("#tabla_contenedora_"+codForm).find("input[type='checkbox'][tipo='campo']:checked").length;

            if( campos == 0 ){
                alerta( "Seleccione los campos que desea comparar" );
                return;
            }

            //validación de limitación de rango de fechas.
            if( $(".td_horas[formulario='"+codForm+"'][encabezado='si'][seleccionado='si']").length == 0 ){
                limitacionHoras = "";
            }else{
                limitacionHoras = "[seleccionado='si']";
            }
            var arrayCampos = new Array();
            var encabezado  = "<tr><td rowspan='2'>campo</td>";
                encabezado  += "<td colspan='"+( campos-1) +"'> Tabla auxiliar comparativa </td></tr>";
            campos          = campos +1;
            encabezado += "</tr>";
            $("#tabla_contenedora_"+codForm).find("input[type='checkbox'][tipo='campo']:checked").each(function(){
                encabezado += "<td>"+$(this).attr("nombreCampo")+"</td>";
                arrayCampos.push( $(this).attr("campo") );
            });
            encabezado += "</tr>";

            tabla  = "<table id='comparativa_"+codForm+"' border='1'>";
            tabla += encabezado;
            // buscar filas marcadas, invertir, construir tabla y graficar como en el ejemplo del manual
            $(".td_horas[formulario='"+codForm+"'][encabezado='si']"+limitacionHoras+"").each(function(){//las horas reportadas

                var agregar = false;
                fila = "<tr><td>"+ $(this).text() +"</td>";
                for (var i in arrayCampos ) {
                  valor = $("td[formulario='"+codForm+"'][campo='"+arrayCampos[i]+"'][hora='"+$(this).attr("hora")+"']").html();
                  if( $.trim(valor) != "&nbsp;" ){
                    agregar = true;

                  }
                  fila += "<td>"+ valor +" </td>";
                }
                fila += "</tr>";

                if( agregar == true ){
                    tabla += fila;
                }

            });
            tabla += "</table>";
            $("#contenedor_auxiliar").html( tabla );

            $('#contenedor_graficador').html("<center><div id='amchart1' style='border: 1px solid #999999; width:600px; height:350px;'></div></center><br>");
            $("#botonmas").val("Opciones");

            $("#comparativa_"+codForm).LeerTablaAmericas({
                        empezardesdefila : 2,
                        titulo           : 'Grafica Comparativa' ,
                        tituloy          : 'cantidad',
                        filaencabezado   : [1,0],
                        datosadicionales : "todos",
                        rotulos          : "si",
                        tipografico      : 'line'
            });

            $("#contenedor_graficador").dialog({
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                 },
                 show: {
                     effect: "blind",
                     duration: 500
                  },
                 hide: {
                     effect: "blind",
                     duration: 500
                  },
                 height: 600,
                 width: 800,
                 rezisable: true
            });
            $("input[type='radio'][value='line']").trigger("click");
            //$("#rotulo1comparativa_"+codForm).trigger("click");
        }

        function alerta( txt ){
            $("#textoAlerta").text( txt );
            $.blockUI({ message: $('#msjAlerta') });
                setTimeout( function(){
                                $.unblockUI();
                            }, 1600 );
        }

        function modificarRangoHoras( obj ){

            var numeroTd   = $(obj).attr("numeroTd");
            var formulario = $(obj).attr("formulario");
            var clicks = $("input[type='hidden'][formulario='"+formulario+"'][tipo='controlRangoHoras']").attr("clicks");

            clicks  = clicks*1 + 1;
            if( clicks == 2 )
                clicks = 0;

            if( $(obj).attr("seleccionado") == "si" ){
                if( clicks == 1)
                clicks = 1;
            }

            clicks = $("input[type='hidden'][formulario='"+formulario+"'][tipo='controlRangoHoras']").attr("clicks", clicks);
            seleccionarParaGraficar( obj );
        }

        function seleccionarParaGraficar( obj ){

            var elegido    = "no";
            var numeroTd   = $(obj).attr("numeroTd");
            var formulario = $(obj).attr("formulario");

            if( $("input[type='hidden'][formulario='"+formulario+"'][tipo='controlRangoHoras']").attr("clicks") == "0"){
                return;
            }

            if( $("td[numeroTd='"+numeroTd+"'][formulario='"+formulario+"']").attr("seleccionado") == "no" ){//si se está seleccionando una hora

                var menorSeleccionado = 0;
                for( var i = 1; i < numeroTd ; i++ ){//busco si hay horas anteriores seleccionadas para graficar
                    if(  $("td[numeroTd='"+i+"'][formulario='"+formulario+"']").attr("seleccionado") == "si"){
                        menorSeleccionado = i;
                        i = numeroTd;
                    }
                }

                if( menorSeleccionado > 0 ){// si hay horas anteriores seleccionadas, selecciono todas desde la primer elegida hasta la seleccionada Actual
                    for( var i = menorSeleccionado; i <= numeroTd ; i++ ){//selecciono desde el menor hasta el actual
                         $("td[numeroTd='"+i+"'][formulario='"+formulario+"']").attr("seleccionado", "si");
                         $("td[numeroTd='"+i+"'][formulario='"+formulario+"']").addClass("horaSeleccionada");
                    }
                }else{// si no hay horas anteriores elegidas, solo selecciono la actual
                     $("td[numeroTd='"+numeroTd+"'][formulario='"+formulario+"']").attr("seleccionado", "si");
                     $("td[numeroTd='"+numeroTd+"'][formulario='"+formulario+"']").addClass("horaSeleccionada");
                }

                var tdSiguiente = numeroTd*1 + 1; //verifico que a la izquierda haya espacios vacios, si los hay quito las horas superiores elegidas
                if( $("td[numeroTd='"+tdSiguiente+"'][formulario='"+formulario+"']").attr("seleccionado" ) == "no"){
                    for( var i = tdSiguiente; i <= 24 ; i++ ){//selecciono desde el menor hasta el actual
                         $("td[numeroTd='"+i+"'][formulario='"+formulario+"']").attr("seleccionado", "no");
                         $("td[numeroTd='"+i+"'][formulario='"+formulario+"']").removeClass("horaSeleccionada");
                    }
                }
            }else{
                    //buscar todos las horas que estén a la izquierda, y quitarlos tambien
                    for( var i = numeroTd; i <= 24 ; i++ ){
                        $("td[numeroTd='"+i+"'][formulario='"+formulario+"']").attr("seleccionado", "no");
                        $("td[numeroTd='"+i+"'][formulario='"+formulario+"']").removeClass("horaSeleccionada");
                    }
            }

            if( $("td[formulario='"+formulario+"'][seleccionado='si']" ).length == 0 ){
                $("input[type='hidden'][formulario='"+formulario+"'][tipo='controlRangoHoras']").attr("clicks", "0");
            }
        }

        function diaCompleto( chk ){
            if( $(chk).is(":checked") ){
                $("#wdiaCompleto").val("on");
            }else{
                $("#wdiaCompleto").val("off")
            }
        }

        function ModificarTodos( obj ){
            var chkTodos   = jQuery( obj );
            var formulario = chkTodos.attr( "formulario" );
            if( chkTodos.is( ":checked" ) ){
                $("input[type='checkbox'][formulario='"+formulario+"'][tipo='campo']:not(:checked)").trigger("click");
                $("input[type='checkbox'][formulario='"+formulario+"'][name='chk_todos']:not(:checked)").attr( "checked", true );
            }else{
                $("input[type='checkbox'][formulario='"+formulario+"'][tipo='campo']:checked").trigger("click");
                $("input[type='checkbox'][formulario='"+formulario+"'][name='chk_todos']:checked").attr( "checked", false );
            }
        }

        function cambiarFecha( obj ){
            var valor = $(obj).val();
            $(".inputFecha").val( valor );
        }

        $(document).ready(function(){
            var historia = $("#whis").val();
            var ingreso  = $("#wing").val();
            if( $.trim(historia) != "" && $.trim(ingreso) != "" ){
                generarInforme();
            }
        });
    </script>
</head>
<body>
<?php
include_once( "root/comun.php" );

$wactualiz       = "2016-07-13";
//$wcodFormularios = "000116,000117";
$institucion     = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wempresa        = strtolower($institucion->baseDeDatos);
$whce            = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wmovhos         = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcliame         = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$reportes        = arregloReportes();

encabezado( "REPORTE SABANA UCI", $wactualiz, $wempresa );

?>
<input type='hidden' id='wemp_pmla' value='<?php echo $wemp_pmla ?>'>
<input type='hidden' id='wcodFormularios'  value='<?php echo $wcodFormularios ?>'>
<input type='hidden' id='whce'      value='<?php echo $whce ?>'>
<input type='hidden' id='wmovhos'   value='<?php echo $wmovhos ?>'>
<input type='hidden' id='wcliame'   value='<?php echo $wcliame ?>'>
<input type='hidden' id='wempresa'  value='<?php echo $wempresa ?>'>
<input type='hidden' id='whoraDeCorte'  value='<?php echo $whoraDeCorte ?>'>
<input type='hidden' id='wultimas24'  value='<?php echo $wultimas24 ?>'>
<input type='hidden' id='wdiaCompleto'  value='<?php echo $wdiaCompleto ?>'>
<!--<input type='hidden' id='wfechahoy'  value='<?php //( !isset($wfechahoy) ) ? $wfechahoy = date('Y-m-d') : $wfechahoy = $wfechahoy; echo $wfechahoy; ?>'>-->

<div id='div_formularioppal' align='center' style='width:100%;<?php ( !isset($whis) and !isset($wing) ) ? $mostrar = "" : $mostrar = "display:none;" ; echo $mostrar; ?>'>
    <div style='width:40%;' align='center'>
        <table>
            <tr class='encabezadotabla'><td colspan='4'> Por favor ingrese los parámetros de busqueda</td></tr>
            <tr class='fila1'>
                <td align='left' width='35%'> Historia: </td><td width='20%'><input type='text' id='whis' size='15' value='<?php echo $whis ?>'></td>
                <td align='left' width='35%'> Ingreso: </td><td width='10%'><input type='text' id='wing' size='3' value='<?php echo $wing ?>'></td>
            </tr>
        </table>
    </div><br>
    <input type='button' id='btn_generarReporte' value='Generar' onclick='generarInforme();'>
</div>
<div id='div_respuesta' style='display:none;' align='center'>
</div>
<div id='msjAlerta' style='display:none;'>
    <br>
    <img src='../../images/medical/root/Advertencia.png'/>
    <br><br><div id='textoAlerta'></div><br><br>
</div>

<center>
    <div id='msjEspere' style='display:none;'>
        <br /><br />
        <img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Espere por Favor</font>
        <br /><br /><br />
    </div>
</center>
<div id='msjRespuesta' name='msjRespuesta' style='display:none;'></div><br>
<div id='contenedor_graficador' style='display:none'></div>
<div id='msjAlerta' style='display:none;'>
    <br>
    <img src='../../images/medical/root/Advertencia.png'/>
    <br><br><div id='textoAlerta'></div><br><br>
</div>
<div id='contenedor_auxiliar' style='display:none'></div>
<center>
    <table>
        <tr><td align='center' colspan='9'>
                <div align='center' id='div_cerrar_ventana'>
                  <!--  <input type=button value='Cerrar Ventana' onclick='cerrarVentana()'> -->
                </div><br>
            </td>
        </tr>
    </table>
</center>
</body>
</html>
